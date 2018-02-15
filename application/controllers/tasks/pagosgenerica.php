<?php

//MGAP
//0=pendiente, 1=paga, 8=rechazada la por pasarela, 9=cancelada

//MTOP
//
//10 Pago Iniciado en componente
//15 Pago Iniciado en Gateway
//20 Pago Pendiente en Gateway
//25 Pago OK en GW
//40 Pago No Iniciado
//42 Error en Gateway
//45 Rechazado Gateway
//99 Transacción Anulada


//DEBE SER DE LA FORMA 'OP1,OP2'
define('ESTADOS_AVANZAR', '25');
//debe ser de la forma '"OP1","OP2"'
define('ESTADOS_BUSCAR', '"10","15","20","25"');

class PagosGenerica extends CI_Controller {

  public function __construct() {
    parent::__construct();

    if(!$this->input->is_cli_request()) {
      redirect(site_url());
    }
  }

  public function conciliacion() {

    //12 horas de ejecucion
    ini_set('max_execution_time', 43200);
    //sin limite de memoria, la libera al terminar
    ini_set('memory_limit', '-1');

    echo 'Iniciando proceso de conciliacion de pagos genericos, aguarde por favor. (versión 1.0).' . PHP_EOL;

    $estados_buscar = explode(',',ESTADOS_BUSCAR);
    $estados_avanzar = explode(',',ESTADOS_AVANZAR);

    //lock por base
    echo 'Obteniendo  lock por base de datos.....' . PHP_EOL;
    $lockdb = $this->load->database('default',TRUE);
    $lockdb->trans_start();
    $lockdb->query('LOCK TABLES lock_task WRITE');
    echo 'obtuvo lock continua con el proceso '. PHP_EOL;


    //echo 'Obteniendo  lock por file ubicado en /var/tmp/pagosblockfile .....' . PHP_EOL;
    //$file_handle = fopen("/var/tmp/pagosblockfile","w");
    //flock($file_handle, LOCK_EX);
    //echo 'Obtuvo lock continua ' . PHP_EOL;

   $conn = Doctrine_Manager::connection();
   $stmt= $conn->prepare('select id from etapa where pendiente = 1 order by id');
   $stmt->execute();
   $datos =  $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

   $total = count($datos);

   echo 'Total de etapas pendientes a procesar: ' . $total . ' ' . PHP_EOL;

    $limit = 1000;
    $offset = 0;



    while ($offset < ($total +$limit)){

      echo 'Inicia bucle limit ' . $limit . ' offset ' . $offset  .PHP_EOL;

      $spl = array_slice($datos,$offset, $limit);
      if (!$spl){
        break;
      }

      $que = Doctrine_Query::create()
              ->from('Etapa e')
              ->whereIn('e.id',$spl)
              ->orderby('e.id');

      $etapas_pendientes =  $que->execute();
      if($etapas_pendientes && count($etapas_pendientes) > 0) {
      echo 'count ' . count($etapas_pendientes)  .PHP_EOL;
      foreach($etapas_pendientes as $etapa) {

        foreach($etapa->Tarea->Pasos as $paso) {
          foreach($paso->Formulario->Campos as $campo) {
            if($campo->tipo == 'pagos') {
              //verificamos que exista la fila en la tabla pago

              //en pagos siempre primero iniciado y despues las siguientes opciones
              $whereIn = 'p.estado IN ("iniciado",' . ESTADOS_BUSCAR . ')';
              $query = Doctrine_Query::create()
                  ->from('Pago p')
                  ->where($whereIn)
                  ->andWhere('p.id_etapa = ?', $etapa->id)
                  ->orderby('p.id DESC');

              //echo $query->getSQLQuery() .  PHP_EOL;
              $pago_fila =  $query->fetchOne();

              $pasarela = Doctrine_Query::create()
                  ->from('PasarelaPagoGenerica pa')
                  ->where('pa.id = ?', $pago_fila->pasarela)
                  ->execute();
              $pasarela = $pasarela[0];


              if ($pago_fila && $pasarela){

                if (in_array($pago_fila->estado , $estados_avanzar)){
                  echo 'Etapa id ' . $etapa->id . ' con pago id ' .  $pago_fila->id . ' en estado ' . $pago_fila->estado .  ' SE CIERRA '.  PHP_EOL;
                  $this->cerrarEtapa($etapa);
                } else {
                    //invoca WS
                    $variable_idestado = $pasarela->variable_idestado;
                    $codigo_operacion_soap = $pasarela->codigo_operacion_soap_consulta;

                    $operacion = Doctrine_Query::create()
                                ->from('WsOperacion o')
                                ->where('o.codigo = ?', $codigo_operacion_soap)
                                ->fetchOne();
                    $servicio = Doctrine::getTable('WsCatalogo')->find($operacion->catalogo_id);

                    echo 'Invoca WS Etapa id ' . $etapa->id . ' con pago id ' .  $pago_fila->id . ' en estado ' . $pago_fila->estado .  ' id sol '. $pago_fila->id_solicitud  . PHP_EOL;

                    $ci = get_instance();
                    $ci->load->helper('soap_execute');
                    soap_execute($etapa, $servicio, $operacion, $operacion->soap);

                    $error_servicio_pagos = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("ws_error", $etapa->id);

                    if($error_servicio_pagos) {
                      echo '*** WS Response error code:' . $error_servicio_pagos->valor.  PHP_EOL;
                    }else{
                      $id_estado = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $variable_idestado), $etapa->id);
                      echo '*** WS Response OK: ' .$id_estado->valor .  PHP_EOL;
                      if (in_array($id_estado->valor , $estados_avanzar)){
                        echo 'Etapa id ' . $etapa->id . ' con pago id ' .  $pago_fila->id . ' en estado ' . $id_estado->valor .  ' SE CIERRA '.  PHP_EOL;
                        $this->cerrarEtapa($etapa);
                      }
                    }
              }
            }else{
                 //echo 'Etapa id ' . $etapa->id . '  SIN fila de pago NO SE CIERRA ' . PHP_EOL;
             }
            }
          }
        }
      }
      echo PHP_EOL;
      echo 'Bucle completado offset ' . $offset . PHP_EOL;
    }
    else {
      echo PHP_EOL;
      echo 'No se han encontrado etapas pendientes.' . PHP_EOL;
    }
      //aumenta el offset
      $offset =  $offset + $limit;
    }

    echo 'Libera lock fin del proceso ' . PHP_EOL;

    $lockdb->query('UNLOCK TABLES;');
    $lockdb->trans_complete();
    $lockdb->close();
  }

function cerrarEtapa($etapa){
  $etapa->avanzar();
}




}

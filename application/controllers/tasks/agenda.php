<?php

define('SERVICE_URL', 'https://192.168.1.13:13002/sae-admin/rest/consultas/reservas-por-agenda-y-documento-full');
define('SERVICE_TOKEN', 'GDeUenbzsQDUmuBoKjexGHlXX');

class Agenda extends CI_Controller {

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

    echo 'Iniciando proceso de conciliacion de agenda, aguarde por favor. (versión 1.0).' . PHP_EOL;

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
            if($campo->tipo == 'agenda') {
              //se debe buscar el campo donde almacena el documento o si ejecuto logueado
              //obtener el usuario para obtener la cedula
              $doc_variable = 'documento_tramite_inicial__e'. $etapa->tramite_id;
              $dato=Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($doc_variable,$etapa->id);
              if ($dato){
                $datoDocumento=Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($dato->valor,$etapa->id);
                if ($datoDocumento){
                  //existe un componente agenda en el tramite y tiene el tramite un campo marcado como documento
                  //se invoca al servicio web de sae para ver se si registró o no
                  //si se registro avanza la etapa, en otro caso no la avanza.
                  $service_url = SERVICE_URL;

                  $temp = new DateTime($this->created_at);
                  $fecha_desde = date_format(  $temp, 'Ymd');


                  $url = $campo->extra->url;
                  $parts = parse_url($url);
                  parse_str($parts['query'], $query);
                  $idAgenda =  $query['a'];
                  $idTramite =  $query['q'];



                  if ($idTramite){
                    $curl_post_data = array(
                          "token" => SERVICE_TOKEN,
                          "idAgenda" => $idAgenda,
                          "tipoDocumento"=> 'CI',
                          "numeroDocumento"=> $datoDocumento->valor,
                          "codigoTramite"=>  $idTramite,
                          "fechaDesde" => $fecha_desde
                    );
                  }else{
                    $curl_post_data = array(
                          "token" => SERVICE_TOKEN,
                          "idAgenda" => $idAgenda,
                          "tipoDocumento"=> 'CI',
                          "numeroDocumento"=> $datoDocumento->valor,
                          "fechaDesde"=> $fecha_desde
                    );
                  }



                  $payload = json_encode( $curl_post_data);

                  echo 'Invoca WS Etapa id ' . $etapa->id . ' proceso ' .  $etapa->Tramite->Proceso->nombre . ' payload ' .  $payload .PHP_EOL;

                  $curl = curl_init();
                  curl_setopt($curl, CURLOPT_URL, $service_url);
                  curl_setopt($curl, CURLOPT_TIMEOUT, 30);
                  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                  curl_setopt($curl, CURLOPT_POST, true);
                  curl_setopt($curl, CURLOPT_POSTFIELDS, $payload );
                  curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                  $curl_response = curl_exec($curl);

                  $curl_errno = curl_errno($curl); // -- Codigo de error
                  $curl_error = curl_error($curl); // -- Descripcion del error
                  $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE); // -- Codigo respuesta HTTP

                  curl_close($curl);
                  if ($curl_errno == 0 && $http_code == '200'){
                      $curl_response = json_decode($curl_response);
                      echo '*** WS Response OK: cantidad reservas '  . $curl_response->cantidad .  PHP_EOL;
                       foreach($curl_response->reservas as $reserva) {
                         if ($reserva->estado == 'reservada' || $reserva->estado == 'usada'){
                           //existe una reserva con fecha posterior a la etapa y está resevada o usada
                           echo '*** Reserva en estado reservada o usada se cierra etapa '  . json_encode($reserva).  PHP_EOL;
                           $this->cerrarEtapa($etapa);
                         }
                       }
                  }else{
                    echo '*** WS Response error code:' . $http_code . '-errno:' . $curl_errno . ' -response:' . $ws_response.  PHP_EOL;
                  }


                }

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

    //lock por file
    //fclose($file_handle);
    //lock por base

    $lockdb->query('UNLOCK TABLES;');
    $lockdb->trans_complete();
    $lockdb->close();


  }

function cerrarEtapa($etapa){
  $etapa->avanzar();
}




}

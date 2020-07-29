<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cron extends CI_Controller {

    public function __construct() {
        parent::__construct();

        if (!$this->input->is_cli_request()) {
            echo 'Accion no permitida';
            exit;
        }
    }

    public function hourly() {
        //Indexamos las busquedas en Sphinx
        system('cd sphinx; searchd; indexer --rotate --all');
    }

    public function daily() {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
        $this->session->set_userdata('cron_exc', 'true');
        echo PHP_EOL . '====== INICIO CRON DAILY ======' . PHP_EOL;

        $this->procesar_etapas_sin_fecha_variable();
        $this->procesar_etapas_con_fecha_variable();

        $this->limpiar_tramites_en_blanco();
        $this->limpiar_usuarios_no_registrados();

        echo PHP_EOL . '======  FIN CRON DAILY ======' . PHP_EOL;
    }

    private function procesar_etapas_sin_fecha_variable() {
        echo PHP_EOL . '**************************************************';
        echo PHP_EOL . '    -- Procesando etapas SIN fecha variable --' . PHP_EOL;
        echo '**************************************************' . PHP_EOL;
        echo '(fecha de vencimiento definida despues de finalizar la etapa anterior)' . PHP_EOL . PHP_EOL;

        $cantidad = -1;
        while ($cantidad != 0) {
            $etapas_vencidas = Doctrine_Query::create()
                    ->from('Etapa e, e.Tarea t')
                    ->where('e.pendiente = 1')
                    ->andWhere('t.vencimiento = 1')
                    ->andWhere('t.escalado_automatico = 1')
                    ->andWhere('t.vencimiento_a_partir_de_variable IS NULL OR t.vencimiento_a_partir_de_variable = ""')
                    ->andWhere('e.vencimiento_at < date(now())')
                    ->limit(1000)
                    ->execute();
            $cantidad = count($etapas_vencidas);
            echo 'Cantidad total de etapas a procesar: ' . count($etapas_vencidas) . PHP_EOL;
            echo '  |_ Cantidad de etapas a escalar (vencidas): ' . count($etapas_vencidas) . PHP_EOL . PHP_EOL;
            //se escalan las vencidas
            foreach ($etapas_vencidas as $etapa) {
                if ($etapa->Tarea->notificar_vencida) {
                    $dias_por_vencer = ceil((strtotime($etapa->vencimiento_at) - time()) / 60 / 60 / 24);
                    //si vencio hace 1 dia (cron se ejecuta todos los dias)
                    if ($dias_por_vencer == -1) {
                        $this->notificar_email($etapa, true);
                    }
                }

                $this->escalar($etapa);
            }
        }
        $cantidad = -1;
        $etapas_por_vencer = Doctrine_Query::create()
                ->from('Etapa e, e.Tarea t')
                ->where('e.pendiente = 1')
                ->andWhere('t.vencimiento = 1')
                ->andWhere('t.vencimiento_notificar = 1')
                ->andWhere('t.vencimiento_a_partir_de_variable IS NULL OR t.vencimiento_a_partir_de_variable = ""')
                ->andWhere('DATEDIFF(e.vencimiento_at,NOW()) = t.vencimiento_notificar_dias')
                ->limit(1000)
                ->execute();

        $cantidad = count($etapas_por_vencer);
        while ($cantidad != 0) {
            echo 'Cantidad total de etapas a procesar: ' . (count($etapas_por_vencer)) . PHP_EOL;
            echo '  |_ Cantidad de etapas a notificar (por vencer): ' . count($etapas_por_vencer) . PHP_EOL;
            //se notifcan las que estan por vencer y para ser notificadas
            foreach ($etapas_por_vencer as $etapa) {
                $this->notificar_email($etapa);
                $cantidad--;
            }
        }
    }

    private function procesar_etapas_con_fecha_variable() {

        echo PHP_EOL . '**************************************************';
        echo PHP_EOL . '    -- Procesando etapas CON fecha variable --' . PHP_EOL;
        echo '**************************************************' . PHP_EOL;
        echo '(fecha de vencimiento fue definida despues de una fecha @@ o fija d-m-a)' . PHP_EOL . PHP_EOL;

        $etapas_con_fecha_variable = Doctrine_Query::create()
                ->from('Etapa e, e.Tarea t')
                ->where('e.pendiente = 1')
                ->andWhere('t.vencimiento = 1')
                ->andWhere('(t.escalado_automatico = 1 OR t.vencimiento_notificar = 1)')
                ->andWhere('t.vencimiento_a_partir_de_variable IS NOT NULL AND t.vencimiento_a_partir_de_variable != ""')
                ->execute();

        echo 'Cantidad total de etapas a procesar: ' . count($etapas_con_fecha_variable) . PHP_EOL . PHP_EOL;

        foreach ($etapas_con_fecha_variable as $etapa) {
            $procesada = false;

            //se debe recalcular la fecha para las etapas con fecha variable tanto para escalar o como para ser notificadas
            $etapa->vencimiento_at = $etapa->calcularVencimiento();
            $etapa->save();

            if ($etapa->vencimiento_at) { //si se pudo calcular una fecha de vencimiento
                $dias_por_vencer = ceil((strtotime($etapa->vencimiento_at) - time()) / 60 / 60 / 24);

                if ($etapa->Tarea->vencimiento_notificar && $dias_por_vencer == $etapa->Tarea->vencimiento_notificar_dias) {
                    $this->notificar_email($etapa);
                    $procesada = true;
                } else if ($etapa->Tarea->notificar_vencida && $dias_por_vencer == -1) {
                    $this->notificar_email($etapa, true);
                    $procesada = true;
                }

                //se escalan las vencidas
                if ($etapa->Tarea->escalado_automatico && $dias_por_vencer < 0) {
                    $this->escalar($etapa);
                    $procesada = true;
                }

                if (!$procesada) {
                    //echo '    Etapa id: '.$etapa->id.' -> No es necesario notificar/escalar'. PHP_EOL;
                }
            } else {
                //echo '    Etapa id: '.$etapa->id.' -> Aun no se encuentra el dato '.$etapa->Tarea->vencimiento_a_partir_de_variable. PHP_EOL;
            }
        }
    }

    private function notificar_email($etapa, $etapa_vencida = false) {

        $CI = & get_instance();
        $regla = new Regla($etapa->Tarea->vencimiento_notificar_email);
        $email = $regla->getExpresionParaOutput($etapa->id);

        if ($email) {
            $cuenta = $etapa->Tramite->Proceso->Cuenta;
            $dias_por_vencer = ceil((strtotime($etapa->vencimiento_at) - time()) / 60 / 60 / 24);

            if (!$cuenta->correo_remitente) {
                ($CI->config->item('main_domain') == '') ? $from = $cuenta->nombre . '@simple' : $from = $cuenta->nombre . '@' . $CI->config->item('main_domain');
            } else {
                $from = $cuenta->correo_remitente;
            }

            $this->email->from($from, 'Simple');
            $this->email->to($email);

            if ($etapa_vencida) {
                $this->email->subject('Simple - Etapa se encuentra vencida');
                $this->email->message('La etapa "' . $etapa->Tarea->nombre . '" se encuentra vencida <br> Usuario asignado: ' . $etapa->Usuario->usuario);
                //echo '    Etapa id '.$etapa->id.' -> Notificando que se eccuentra vencida('.$etapa->vencimiento_at.') al correo: '.$email . PHP_EOL;
            } else {
                $this->email->subject('Simple - Etapa se encuentra por vencer');
                $this->email->message('La etapa "' . $etapa->Tarea->nombre . '" se encuentra a ' . $dias_por_vencer . ' d&iacutea/s por vencer <br> Usuario asignado: ' . $etapa->Usuario->usuario);
                //echo '    Etapa id '.$etapa->id.' -> Notificando que va a vencer('.$etapa->vencimiento_at.') en '.$etapa->Tarea->vencimiento_notificar_dias.' dia/s al correo: '.$email . PHP_EOL;
            }

            $this->email->send();
        } else {
            //echo '    Etapa id '.$etapa->id.' -> No se pudo notificar: no se encuentra el dato '.$etapa->Tarea->vencimiento_notificar_email . PHP_EOL;
        }
    }

    private function escalar($etapa) {
        $CI = & get_instance();
        $CI->load->helper('texto_helper');

        //se marca la etapa actual que fue escalada
        $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('escalado_automatico', $etapa->id);
        if ($dato_seguimiento) {
            $dato_seguimiento->delete();
        }

        $dato_seguimiento = new DatoSeguimiento();
        $dato_seguimiento->etapa_id = $etapa->id;
        $dato_seguimiento->nombre = 'escalado_automatico';
        $dato_seguimiento->valor = '1';
        $dato_seguimiento->save();

        $etapa->avanzar();

        //se marca la siguiente etapa para saber desde que tarea fue escalada
        $etapa_siguiente = $etapa->Tramite->getUltimaEtapaPendiente();

        if ($etapa_siguiente) {
            //echo '    Etapa id '.$etapa->id .' -> Vencio en la fecha '.$etapa->vencimiento_at.' y fue escalada (avanzando) hacia la etapa id '.$etapa_siguiente->id . PHP_EOL;

            $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('escalado_automatico_desde_' . strtolower(eliminar_tildes(solo_letras_y_numeros($etapa->Tarea->nombre))), $etapa_siguiente->id);
            if ($dato_seguimiento) {
                $dato_seguimiento->delete();
            }
            $dato_seguimiento = new DatoSeguimiento();
            $dato_seguimiento->etapa_id = $etapa_siguiente->id;
            $dato_seguimiento->nombre = 'escalado_automatico_desde_' . strtolower(eliminar_tildes(solo_letras_y_numeros($etapa->Tarea->nombre)));
            $dato_seguimiento->valor = '1';
            $dato_seguimiento->save();
        } else {
            //echo '    Etapa id '.$etapa->id .' -> Vencio en la fecha '.$etapa->vencimiento_at.' y fue escalada (avanzando) hacia el fin del proceso' . PHP_EOL;
        }
    }

    private function limpiar_tramites_en_blanco() {
        echo PHP_EOL . '**************************************************';
        echo PHP_EOL . '       -- Limpiando tramites en blanco -- ' . PHP_EOL;
        echo '**************************************************' . PHP_EOL;

        //Limpia los tramites que que llevan mas de 1 dia sin modificarse, sin avanzar de etapa y sin datos ingresados (En blanco).
        $tramites_en_blanco = Doctrine_Query::create()
                ->from('Tramite t, t.Etapas e, e.Usuario u, e.DatosSeguimiento d')
                ->where('t.updated_at < DATE_SUB(NOW(),INTERVAL 1 DAY) AND t.pendiente = 1')
                ->groupBy('t.id')
                ->having('COUNT(e.id) = 1 AND COUNT(d.id) = 0')
                ->execute();

        $tramites_en_blanco->delete();
    }

    private function limpiar_usuarios_no_registrados() {
        echo PHP_EOL . '**************************************************';
        echo PHP_EOL . '    -- Limpiando usuarios no registrados --' . PHP_EOL;
        echo '**************************************************' . PHP_EOL;

        //Elimino los registros no registrados con mas de 1 dia de antiguedad y que no hayan iniciado etapas
        $noregistrados = Doctrine_Query::create()
                ->from('Usuario u')
                ->where('u.registrado = 0 AND DATEDIFF(NOW(),u.updated_at) >= 1 AND NOT exists (SELECT 1 FROM etapa e1 WHERE e1.usuario_id = u.id)')
                ->execute(array(1), Doctrine_Core::HYDRATE_ON_DEMAND);
        $id_usuario = array();
        $cont = 0;
        foreach ($noregistrados as $value) {
            $id_usuario[] = $value->id;
            $cont++;
            if ($cont == 4000) {
                $noregistrados_id = Doctrine_Query::create()
                        ->from('Usuario u')
                        ->whereIn('u.id', $id_usuario)
                        ->execute();
                $noregistrados_id->delete();
                $id_usuario = array();
                $cont = 0;
            }
        }
        if (isset($id_usuario[0])) {
            $noregistrados_id = Doctrine_Query::create()
                    ->from('Usuario u')
                    ->whereIn('u.id', $id_usuario)
                    ->execute();
            $noregistrados_id->delete();
        }
    }

    public function limpiar_monitoreo() {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
        echo PHP_EOL . '====== INICIO CRON MONITOREO ======' . PHP_EOL;
        echo PHP_EOL . '*********** 15 dias fecha actual ';
        echo '****************************' . PHP_EOL;

        $cantidad = -1;
        $cantidad_total = 0;
        while ($cantidad != 0) {

            $monitoreo_antiguo = Doctrine_Query::create()
                    ->from('Monitoreo m')
                    ->where('m.fecha < date_add(now(),INTERVAL -15 Day)')
                    ->limit(3000)
                    ->execute();
            $cantidad = count($monitoreo_antiguo);
            $cantidad_total+=$cantidad;
            $monitoreo_antiguo->delete();
            $q = Doctrine_Manager::getInstance()->getCurrentConnection();
            $q->execute("commit;");
        }
        echo 'Se procede a limpiar ' . $cantidad_total . ' registros en la tabla Monitoreo' . PHP_EOL;

        echo PHP_EOL . '====== FIN CRON MONITOREO ======' . PHP_EOL;
    }

}

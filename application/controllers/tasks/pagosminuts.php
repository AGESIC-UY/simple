<?php

define('DESDE', '600');
define('HASTA', '0');
define('TIPO_EVENTOS_EJECUTAR', 'enviar_correo|webservice_extended');
define('PROCESOS', '9066|9082');
define('FECHA_COMPARAR', 'created_at'); //created_at || updated_at

class PagosMinuts extends CI_Controller {

    public function __construct() {
        parent::__construct();

        if (!$this->input->is_cli_request()) {
            redirect(site_url());
        }

        $this->load->helper('trazabilidad_helper');
    }

    public function conciliacion() {
        //12 horas de ejecucion
        ini_set('max_execution_time', 43200);
        //sin limite de memoria, la libera al terminar
        ini_set('memory_limit', '-1');
        $this->session->set_userdata('cron_exc', 'true');
        echo 'Iniciando proceso de conciliacion de pagos, aguarde por favor. (versión 1.7).' . PHP_EOL;
        $this->guardar_log(' ', true);
        $this->guardar_log('**************************************************** ' . strftime(date("d F Y H:i:s")) . ' ***************************************************************', true);
        $this->guardar_log('************************  Iniciando proceso de conciliacion de pagos, aguarde por favor. (versión 1.7).*************************', true);
        $this->guardar_log('*****************************************************************************************************************************************************', true);
        $this->guardar_log(' ', true);

        //lock por base
        echo 'Obteniendo lock por base de datos.....' . PHP_EOL;
        $this->guardar_log('Obteniendo lock por base de datos.....');

        $lockdb = $this->load->database('default', TRUE);
        $lockdb->trans_start();
        $lockdb->query('LOCK TABLES lock_task WRITE');
        echo 'obtuvo lock continua con el proceso ' . PHP_EOL;
        $this->guardar_log('obtuvo lock continua con el proceso');


        //echo 'Obteniendo  lock por file ubicado en /var/tmp/pagosblockfile .....' . PHP_EOL;
        //$file_handle = fopen("/var/tmp/pagosblockfile","w");
        //flock($file_handle, LOCK_EX);

        if (is_numeric(DESDE)) {
            $desde = date("Y-m-d H:i:s");
            $desde = strtotime($desde);
            $desde = $desde - (DESDE * 60);
            $desde = date("Y-m-d H:i:s", $desde);
        }

        //$desde = DESDE . ' 00:00:00';
        if (is_numeric(HASTA)) {
            $hasta = date("Y-m-d H:i:s");
            $hasta = strtotime($hasta);
            $hasta = $hasta - (HASTA * 60);
            $hasta = date("Y-m-d H:i:s", $hasta);
        }


        //$hasta = HASTA . ' 23:59:59';
        //$desde = date("Y-m-d H:i:s", strtotime($desde));
        //$hasta = date("Y-m-d H:i:s", strtotime($hasta));

        echo 'Contando cantidad total de etapas pendientes entre las fechas ' . $desde . ' y ' . $hasta . PHP_EOL;
        $this->guardar_log('Contando cantidad total de etapas pendientes entre las fechas ' . $desde . ' y ' . $hasta);
        $conn = Doctrine_Manager::connection();

        if (PROCESOS != '') {
            $procesos_constant = explode('|', PROCESOS);
        }

        $procesos_root = array();
        foreach ($procesos_constant as $proceso_id) {
            $proceso = Doctrine::getTable('Proceso')->find($proceso_id);
            if ($proceso) {
                if (!in_array((string) $proceso->root, $procesos_root, true)) {
                    $procesos_root[] = (string) $proceso->root;
                }
            }
        }

        $procesos_permitidos = array();
        foreach ($procesos_root as $root) {
            $procesos = Doctrine::getTable('Proceso')->findByRoot($root);
            if ($procesos) {
                foreach ($procesos as $proceso) {
                    if (!in_array((string) $proceso->id, $procesos_permitidos, true)) {
                        $procesos_permitidos[] = (string) $proceso->id;
                    }
                }
            }
        }

        if (is_array($procesos_permitidos) && count($procesos_permitidos) > 0) {
            $queryStr = "select e.id from etapa e, tramite t, proceso p  where e.pendiente = 1 and e.tramite_id = t.id and t.proceso_id = p.id and (p.id IN (" . implode(',', $procesos_permitidos) . ") or p.root IN (" . implode(',', $procesos_permitidos) . "))";
        } else {
            $queryStr = "select e.id from etapa e where e.pendiente = 1 ";
        }

        if ($desde) {
            $queryStr = $queryStr . " and e." . ((defined('FECHA_COMPARAR') && FECHA_COMPARAR) ? FECHA_COMPARAR : "created_at") . " >= '" . $desde . "' ";
        }
        if ($hasta) {
            $queryStr = $queryStr . " and e." . ((defined('FECHA_COMPARAR') && FECHA_COMPARAR) ? FECHA_COMPARAR : "created_at") . " <= '" . $hasta . "' ";
        }
        $queryStr = $queryStr . " order by id";

        echo 'consulta a base de datos ' . $queryStr . PHP_EOL;
        $stmt = $conn->prepare($queryStr);
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        $total = count($datos);

        echo 'Total de etapas procesar: ' . $total . ' ' . PHP_EOL;
        $this->guardar_log('Total de etapas a procesar: ' . $total);

        $limit = 1000;
        $offset = 0;

        while ($offset < ($total + $limit)) {

            echo 'Inicia bucle limit ' . $limit . ' offset ' . $offset . PHP_EOL;
            $this->guardar_log('Inicia bucle limit ' . $limit . ' offset ' . $offset);

            $spl = array_slice($datos, $offset, $limit);
            if (!$spl) {
                break;
            }

            $que = Doctrine_Query::create()
                    ->from('Etapa e')
                    ->whereIn('e.id', $spl)
                    ->orderby('e.id');

            $etapas_pendientes = $que->execute();
            if ($etapas_pendientes && count($etapas_pendientes) > 0) {
                echo 'count ' . count($etapas_pendientes) . PHP_EOL;
                $this->guardar_log('count ' . count($etapas_pendientes));

                foreach ($etapas_pendientes as $etapa) {
                    foreach ($etapa->Tarea->Pasos as $paso) {
                        foreach ($paso->Formulario->Campos as $campo) {
                            if ($campo->tipo == 'pagos') {
                                //verificamos que exista la fila en la tabla pago
                                $pago_fila = Doctrine_Query::create()
                                        ->from('Pago p')
                                        ->where('p.id_etapa = ?', $etapa->id)
                                        ->orderby('p.id DESC')
                                        ->fetchOne();
                                $pago_fila_primero = Doctrine_Query::create()
                                        ->from('Pago p')
                                        ->where('p.id_etapa = ? and p.estado = "token_solicita"', $etapa->id)
                                        ->orderby('p.id ASC')
                                        ->fetchOne();
                                $pasarela = new stdClass();

                                echo 'Etapa id ' . $etapa->id . ' con pago ' . $pago_fila . ' SE ANALIZA ' . PHP_EOL;

                                if ($pago_fila) {
                                    if ($pago_fila->estado == 'realizado') {
                                        echo 'Etapa id ' . $etapa->id . ' con pago id ' . $pago_fila->id . ' en estado ' . $pago_fila->estado . ' SE CIERRA ' . PHP_EOL;
                                        $this->guardar_log('Etapa id ' . $etapa->id . ' con pago id ' . $pago_fila->id . ' en estado ' . $pago_fila->estado . ' SE CIERRA ');
                                        $this->cerrarEtapa($etapa, $paso->orden - 1);
                                    } else if ($pago_fila->estado == 'rc') {
                                        echo 'Etapa id ' . $etapa->id . ' con pago id ' . $pago_fila->id . ' en estado ' . $pago_fila->estado . ' SE CIERRA ' . PHP_EOL;
                                        $this->guardar_log('Etapa id ' . $etapa->id . ' con pago id ' . $pago_fila->id . ' en estado ' . $pago_fila->estado . ' SE CIERRA ');
                                        $this->cerrarEtapa($etapa, $paso->orden - 1);
                                    } else if ($pago_fila->id_solicitud > 0 && ($pago_fila->estado == 'pendiente' || $pago_fila->estado == 'iniciado' || $pago_fila->estado == 'token_solicita' )) {
                                        //invoca WS
                                        echo $pago_fila_primero->id . "--" . md5($pago_fila_primero->id . '_clave_tramite_pasarela_pagos');
                                        $clave_tramite = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(md5($pago_fila_primero->id . '_clave_tramite_pasarela_pagos'), $pago_fila_primero->id_etapa);
                                        $ws_body = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:con="http://tempuri.org/wsorg/Consultas">';
                                        if (!empty(SOAP_PASARELA_PAGO_CONSULTA)) {
                                            if (SOAP_PASARELA_PAGO_CONSULTA == '1.1') {
                                                $ws_body = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:con="http://tempuri.org/wsorg/Consultas">';
                                            }
                                        }
                                        $ws_body = $ws_body . '<soap:Header/>
                                        <soap:Body>
                                           <con:ObtenerDatosTransaccion>
                                              <con:pIdSolicitud>' . (integer) $pago_fila->id_solicitud . '</con:pIdSolicitud>
                                              <con:pIdTramite>' . (integer) $pago_fila->id_tramite . '</con:pIdTramite>
                                              <con:pClave>' . (string) $clave_tramite->valor . '</con:pClave>
                                           </con:ObtenerDatosTransaccion>
                                        </soap:Body>
                                     </soap:Envelope>';


                                        $ws_header = array(
                                            "Content-type: text/xml;charset=\"utf-8\"",
                                            "Accept: text/xml",
                                            "Cache-Control: no-cache",
                                            "Pragma: no-cache",
                                            "Content-length: " . strlen($ws_body),
                                            "SOAPAction: \"http://tempuri.org/wsorg/Consultas/ObtenerDatosTransaccion\""
                                        );

                                        $pasarela = Doctrine_Query::create()
                                                ->from('PasarelaPagoAntel pa')
                                                ->where('pa.id = ?', $pago_fila->pasarela)
                                                ->execute();
                                        $pasarela = $pasarela[0];

                                        if (!$pasarela || empty($pasarela->certificado)) {
                                            continue;
                                        }

                                        echo 'Invoca WS Etapa id ' . $etapa->id . ' con pago id ' . $pago_fila->id . ' en estado ' . $pago_fila->estado . ' id sol ' . $pago_fila->id_solicitud . PHP_EOL;
                                        $this->guardar_log('Invoca WS Etapa id ' . $etapa->id . ' con pago id ' . $pago_fila->id . ' en estado ' . $pago_fila->estado . ' id sol ' . $pago_fila->id_solicitud);


                                        $ws_do = curl_init();
                                        curl_setopt($ws_do, CURLOPT_URL, WS_PASARELA_PAGO_CONSULTA);
                                        curl_setopt($ws_do, CURLOPT_CONNECTTIMEOUT, PASARELA_PAGO_TIMEOUT_CONEXION);
                                        curl_setopt($ws_do, CURLOPT_TIMEOUT, PASARELA_PAGO_TIMEOUT_RESPUESTA);
                                        curl_setopt($ws_do, CURLOPT_RETURNTRANSFER, true);
                                        curl_setopt($ws_do, CURLOPT_SSL_VERIFYPEER, false);
                                        curl_setopt($ws_do, CURLOPT_SSL_VERIFYHOST, false);
                                        curl_setopt($ws_do, CURLOPT_POST, true);
                                        curl_setopt($ws_do, CURLOPT_SSLVERSION, 1);
                                        curl_setopt($ws_do, CURLOPT_SSLCERT, UBICACION_CERTIFICADOS_PASARELA . $pasarela->certificado);
                                        curl_setopt($ws_do, CURLOPT_SSLCERTPASSWD, $pasarela->pass_clave_certificado);
                                        curl_setopt($ws_do, CURLOPT_SSLKEY, UBICACION_CERTIFICADOS_PASARELA . $pasarela->clave_certificado);

                                        curl_setopt($ws_do, CURLOPT_ENCODING, 'gzip');
                                        curl_setopt($ws_do, CURLOPT_POSTFIELDS, $ws_body);
                                        curl_setopt($ws_do, CURLOPT_HTTPHEADER, $ws_header);

                                        if (!empty(PROXY_PASARELA_PAGO)) {
                                            curl_setopt($ws_do, CURLOPT_PROXY, PROXY_PASARELA_PAGO);
                                        }


                                        $ws_response = curl_exec($ws_do);

                                        $curl_errno = curl_errno($ws_do); // -- Codigo de error
                                        $curl_error = curl_error($ws_do); // -- Descripcion del error
                                        $http_code = curl_getinfo($ws_do, CURLINFO_HTTP_CODE); // -- Codigo respuesta HTTP
                                        curl_close($ws_do);

                                        if ($curl_errno > 0 || $http_code != 200) {
                                            echo '*** WS Response error code:' . $http_code . '-errno:' . $curl_errno . ' - certificado:' . $pasarela->certificado . ' -response:' . $ws_response . PHP_EOL;
                                            $this->guardar_log('*** WS Response error code:' . $http_code . '-errno:' . $curl_errno . ' - certificado:' . $pasarela->certificado . ' -response:' . $ws_response);
                                        } else {

                                            $xml = new SimpleXMLElement($ws_response);
                                            $nuevo_estado = $xml->xpath("//*[local-name() = 'IdEstado']/text()");
                                            echo '*** WS Response OK: ' . $nuevo_estado[0] . PHP_EOL;
                                            $this->guardar_log('*** WS Response OK: ' . $nuevo_estado[0]);

                                            $usuario = "Sistema - CRON";
                                            switch ($nuevo_estado[0]) {
                                                case '0':
                                                    //transaccion no existente se puede marcar la fila con id solicitud 0 para que no se
                                                    //ejecute nuevamente en el futuro.
                                                    //$pago_fila->id_solicitud  = 0;
                                                    //$pago_fila->save();
                                                    break;
                                                case '1':
                                                    //Pendiente de Pago no se cierra la etapa
                                                    break;
                                                case '3':
                                                    //Rechazada por Forma de Pago no se cierra la etapa
                                                    break;
                                                case '6':
                                                    //RC-Se obtuvo identificador de cobro, ticket para pago offline generado
                                                    //se debe cerrar la etapa
                                                    //se generan variables
                                                    //if (!empty($pago_fila)) {
                                                    $registro_pago = new Pago();
                                                    $registro_pago->id_solicitud = $pago_fila->id_solicitud;
                                                    $registro_pago->id_tramite_interno = $pago_fila->id_tramite_interno;
                                                    $registro_pago->id_tramite = $pago_fila->id_solicitud;
                                                    $registro_pago->id_etapa = $pago_fila->id_etapa;
                                                    $registro_pago->pasarela = $pago_fila->pasarela;
                                                    $registro_pago->estado = 'rc';
                                                    $registro_pago->fecha_actualizacion = date('d/m/Y H:i:s');
                                                    $registro_pago->usuario = $usuario;
                                                    $registro_pago->save();
                                                    // }
                                                    $this->generar_variables_pago($registro_pago, $xml, $registro_pago->id_solicitud, $etapa->id);

                                                    $this->cerrarEtapa($etapa, $paso->orden - 1);
                                                    echo '********* SE CIERRA ' . PHP_EOL;
                                                    $this->guardar_log('********* SE CIERRA ');
                                                    break;
                                                case '9':
                                                    //Paga se debe cerrar la etapa
                                                    //  if (!empty($pago_fila)) {
                                                    $registro_pago = new Pago();
                                                    $registro_pago->id_solicitud = $pago_fila->id_solicitud;
                                                    $registro_pago->id_tramite_interno = $pago_fila->id_tramite_interno;
                                                    $registro_pago->id_tramite = $pago_fila->id_solicitud;
                                                    $registro_pago->id_etapa = $pago_fila->id_etapa;
                                                    $registro_pago->pasarela = $pago_fila->pasarela;
                                                    $registro_pago->estado = 'realizado';
                                                    $registro_pago->fecha_actualizacion = date('d/m/Y H:i:s');
                                                    $registro_pago->usuario = $usuario;
                                                    $registro_pago->save();
                                                    // }
                                                    $this->generar_variables_pago($registro_pago, $xml, $registro_pago->id_solicitud, $etapa->id);
                                                    $this->cerrarEtapa($etapa, $paso->orden - 1);
                                                    echo '********* SE CIERRA ' . PHP_EOL;
                                                    $this->guardar_log('********* SE CIERRA ');
                                                    break;
                                                case '12':
                                                    //Error del sistema, no se cierra la etapa
                                                    break;
                                                case '16':
                                                    //RC - Reversada, no se cierra la etapa
                                                    break;
                                                case '99':
                                                    //Rechazo no se cierra la etapa.
                                                    break;
                                                default:
                                                    //No se cierra la etapa
                                                    break;
                                            }
                                        }
                                    } else {
                                        //echo 'Etapa id ' . $etapa->id . '  CON fila de pago NO SE CIERRA por estado o id de solicitud 0 ' . PHP_EOL;
                                    }

                                    //-- comienza TRAZABILIDAD CONSULTA ESTADO DEL PAGO
                                    $secuencia = $paso->orden - 1;
                                    $pago_realizado = false;

                                    $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa->id);
                                    $descripcion_traza="";
                                    if ($dato) {
                                        if (isset($pasarela->id)) {
                                            if ($dato->valor == 'pendiente') {
                                                $regla = new Regla($pasarela->descripcion_pendiente_traza);
                                                $descripcion_traza = $regla->getExpresionParaOutput($etapa->id);
                                            } else if ($dato->valor == 'iniciado') {
                                                $regla = new Regla($pasarela->descripcion_iniciado_traza);
                                                $descripcion_traza = $regla->getExpresionParaOutput($etapa->id);
                                            } else if ($dato->valor == 'realizado') {
                                                $regla = new Regla($pasarela->descripcion_realizado_traza);
                                                $descripcion_traza = $regla->getExpresionParaOutput($etapa->id);
                                                $pago_realizado = true;
                                            } else if ($dato->valor == 'error') {
                                                $regla = new Regla($pasarela->descripcion_error_traza);
                                                $descripcion_traza = $regla->getExpresionParaOutput($etapa->id);
                                            } else if ($dato->valor == 'token_solicita') {
                                                $regla = new Regla($pasarela->descripcion_token_solicita_traza);
                                                $descripcion_traza = $regla->getExpresionParaOutput($etapa->id);
                                            } else if ($dato->valor == 'rechazado') {
                                                $regla = new Regla($pasarela->descripcion_reachazado_traza);
                                                $descripcion_traza = $regla->getExpresionParaOutput($etapa->id);
                                            } else {
                                                $descripcion_traza = 'consulta estado pasarela de pagos antel';
                                            }
                                        }
                                    } else {
                                        $descripcion_traza = 'consulta estado pasarela de pagos antel';
                                    }

                                    enviar_traza_linea_pago($etapa, $secuencia, $descripcion_traza, $pago_realizado, $paso);
                                    //-- termina TRAZABILIDAD CONSULTA ESTADO DEL PAGO
                                } else {
                                    //echo 'Etapa id ' . $etapa->id . '  SIN fila de pago NO SE CIERRA ' . PHP_EOL;
                                }
                            }
                        }
                    }
                }
                echo PHP_EOL;
                echo 'Bucle completado offset ' . $offset . PHP_EOL;
                $this->guardar_log('Bucle completado offset ' . $offset);
            } else {
                echo PHP_EOL;
                echo 'No se han encontrado etapas pendientes.' . PHP_EOL;
                $this->guardar_log('No se han encontrado etapas pendientes.');
            }
            //aumenta el offset
            $offset = $offset + $limit;
        }

        echo 'Libera lock fin del proceso ' . PHP_EOL;
        $this->guardar_log('Libera lock fin del proceso');

        //lock por file
        //fclose($file_handle);
        //lock por base

        $lockdb->query('UNLOCK TABLES;');
        $lockdb->trans_complete();
        $lockdb->close();
    }

    function cerrarEtapa($etapa, $secuencia_inicio) {
        $this->ejecutar_eventos_pasos_etapa($etapa, $secuencia_inicio);
        $this->marcar_etapa_avanzada_por_cron_pagos($etapa);
        enviar_traza_final_tarea($etapa);
        $this->avanzar_sin_ejecutar_eventos($etapa);
        enviar_traza_final_proceso($etapa); //verifica si es fin de proceso para enviar traza
    }

    function generar_variables_pago($registro_de_pago, $xml, $id_solicitud, $etapa_id) {
        $pago_estado = $xml->xpath("//*[local-name() = 'IdEstado']/text()");
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago', $etapa_id);
        if ($dato)
            $dato->delete();
        $dato = new DatoSeguimiento();
        $dato->nombre = 'codigo_estado_solicitud_pago';
        $dato->valor = '0';
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdEstado', $etapa_id);
        if ($dato)
            $dato->delete();
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_IdEstado';
        $dato->valor = (string) $pago_estado[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Fecha', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_fecha = $xml->xpath("//*[local-name() = 'Fecha']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_Fecha';
        $dato->valor = (string) $pago_fecha[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Transaccion', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_transaccion = $xml->xpath("//*[local-name() = 'IdTransaccion']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_Transaccion';
        $dato->valor = (string) $pago_transaccion[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Autorizacion', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_autorizacion = $xml->xpath("//*[local-name() = 'Autorizacion']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_Autorizacion';
        $dato->valor = (string) $pago_autorizacion[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdFormaPago', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_forma = $xml->xpath("//*[local-name() = 'IdFormaPago']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_IdFormaPago';
        $dato->valor = (string) $pago_forma[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_FechaConciliacion', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_concilia = $xml->xpath("//*[local-name() = 'FechaConciliacion']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_FechaConciliacion';
        $dato->valor = (string) $pago_concilia[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdTasa', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_id_tasa = $xml->xpath("//*[local-name() = 'IdTasa']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_IdTasa';
        $dato->valor = (string) $pago_id_tasa[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_ValorTasa', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_valor_tasa = $xml->xpath("//*[local-name() = 'ValorTasa']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_ValorTasa';
        $dato->valor = (string) $pago_valor_tasa[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_MontoTotal', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_monto = $xml->xpath("//*[local-name() = 'MontoTotal']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_MontoTotal';
        $dato->valor = (string) $pago_monto[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdTramite', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_id_tramite = $xml->xpath("//*[local-name() = 'IdTramite']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_IdTramite';
        $dato->valor = (string) $pago_id_tramite[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_ImporteTasa1', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_tasa1 = $xml->xpath("//*[local-name() = 'ImporteTasa1']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_ImporteTasa1';
        $dato->valor = (string) $pago_tasa1[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_ImporteTasa2', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_tasa2 = $xml->xpath("//*[local-name() = 'ImporteTasa2']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_ImporteTasa2';
        $dato->valor = (string) $pago_tasa2[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_ImporteTasa3', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_tasa3 = $xml->xpath("//*[local-name() = 'ImporteTasa3']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_ImporteTasa3';
        $dato->valor = (string) $pago_tasa3[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Cantidades', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_cantidades = $xml->xpath("//*[local-name() = 'Cantidades']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_Cantidades';
        $dato->valor = (string) $pago_cantidades[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_FechaVto', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_fecha_vto = $xml->xpath("//*[local-name() = 'FechaVto']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_FechaVto';
        $dato->valor = (string) $pago_fecha_vto[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_CodDesglose', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_desglose = $xml->xpath("//*[local-name() = 'CodDesglose']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_CodDesglose';
        $dato->valor = (string) $pago_desglose[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_MontoDesglose', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_monto_desglose = $xml->xpath("//*[local-name() = 'MontoDesglose']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_MontoDesglose';
        $dato->valor = (string) $pago_monto_desglose[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_DesRechazo', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_rechazo = $xml->xpath("//*[local-name() = 'DesRechazo']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_DesRechazo';
        $dato->valor = (string) $pago_rechazo[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Ventanilla', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_ventanilla = $xml->xpath("//*[local-name() = 'Ventanilla']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_Ventanilla';
        $dato->valor = (string) $pago_ventanilla[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_CodError', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_cod_error = $xml->xpath("//*[local-name() = 'CodError']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_CodError';
        $dato->valor = (string) $pago_cod_error[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_DesError', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_des_error = $xml->xpath("//*[local-name() = 'DesError']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_DesError';
        $dato->valor = (string) $pago_des_error[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Mensaje', $etapa_id);
        if ($dato)
            $dato->delete();
        $pago_mensaje = $xml->xpath("//*[local-name() = 'Mensaje']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_Mensaje';
        $dato->valor = (string) $pago_mensaje[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();
    }

    function ejecutar_eventos_pasos_etapa($etapa, $secuencia_inicio) {
        //echo 'ejecutar_eventos_pasos_etapa ' . $secuencia_inicio . ' ' . count($etapa->getPasosEjecutables()) .  PHP_EOL;
        $secuencia_actual = 0;
        $secuencia_contador = 0;
        foreach ($etapa->getPasosEjecutables() as $paso) {

            $secuencia_actual = $paso->orden - 1;
            //echo 'ejecutar_eventos_pasos_etapa_For ' . $secuencia_inicio . ' -' . $secuencia_actual .  PHP_EOL;
            //solo cuando se llega al paso del pago
            if ($secuencia_actual >= $secuencia_inicio) {
                $paso_final = sizeof($etapa->getPasosEjecutables()) - 1 == $secuencia_contador;

                //echo 'ejecutar_eventos_pasos_etapa FIN ' . $secuencia_inicio . ' '. $secuencia_actual . ' ' . $paso_final. PHP_EOL;
                if ($paso_final) {
                    if ($secuencia_actual != $secuencia_inicio) {
                        $this->ejecutar_eventos_paso_antes($paso, $etapa);
                    }
                    $this->ejecutar_eventos_paso_despues($paso, $etapa);
                    $this->ejecutar_eventos_tarea_despues($etapa);
                    break;
                } else {
                    //echo 'ejecutar_eventos_pasos_etapa else ' . $secuencia_inicio . ' '. $secuencia_actual . ' '. PHP_EOL;
                    if ($secuencia_actual != $secuencia_inicio) {
                        $this->ejecutar_eventos_paso_antes($paso, $etapa);
                    }
                    $this->ejecutar_eventos_paso_despues($paso, $etapa);
                }
            }
            $secuencia_contador++;
        }
    }

    function ejecutar_eventos_paso_antes($paso, $etapa) {
        //Ejecutamos los eventos iniciales del paso
        $eventos_inicio_paso = Doctrine_Query::create()->from('Evento e')
                ->where('e.paso_id = ? AND e.instante = ?', array($paso->id, 'antes'))
                ->execute();
        foreach ($eventos_inicio_paso as $e) {

            $ejecutar = false;
            $eventos_permitidos = explode('|', TIPO_EVENTOS_EJECUTAR);

            foreach ($eventos_permitidos as $tipo_evento) {
                if ($e->Accion->tipo == $tipo_evento) {
                    $ejecutar = true;
                    break;
                }
            }

            echo 'evaluando ' . $e->Accion->nombre . ' tipo ' . $e->Accion->tipo . ' ejectuar: ' . $ejecutar . PHP_EOL;
            if (!$ejecutar) {
                continue;
            }

            $r = new Regla($e->regla);
            if ($r->evaluar($etapa->id)) {
                echo 'Se ejecuta el evento ' . $e->Accion->nombre . ' tipo ' . $e->Accion->tipo . PHP_EOL;
                $this->guardar_log('Se ejecuta el evento ' . $e->Accion->nombre . ' tipo ' . $e->Accion->tipo);
                $e->Accion->ejecutar($etapa, $e); //al pasarle el parametro $e (evento) los eventos trazan segun lo definido en el modelado
            } else {
                echo 'no evalua regla ' . $e->Accion->nombre . ' tipo ' . $e->Accion->tipo . ' ejectuar: ' . $ejecutar . PHP_EOL;
            }
        }
    }

    function ejecutar_eventos_paso_despues($paso, $etapa) {

        //Ejecutamos los eventos finales del paso
        $eventos_fin_paso = Doctrine_Query::create()->from('Evento e')
                ->where('e.paso_id = ? AND e.instante = ?', array($paso->id, 'despues'))
                ->execute();
        foreach ($eventos_fin_paso as $e) {
            $ejecutar = false;
            $eventos_permitidos = explode('|', TIPO_EVENTOS_EJECUTAR);
            foreach ($eventos_permitidos as $tipo_evento) {
                if ($e->Accion->tipo == $tipo_evento) {
                    $ejecutar = true;
                    break;
                }
            }

            echo 'evaluando ' . $e->Accion->nombre . ' tipo ' . $e->Accion->tipo . ' ejectuar: ' . $ejecutar . PHP_EOL;
            if (!$ejecutar) {
                continue;
            }

            $r = new Regla($e->regla);
            if ($r->evaluar($etapa->id)) {
                echo 'Se ejecuta el evento ' . $e->Accion->nombre . ' tipo ' . $e->Accion->tipo . PHP_EOL;
                $this->guardar_log('Se ejecuta el evento ' . $e->Accion->nombre . ' tipo ' . $e->Accion->tipo);
                $e->Accion->ejecutar($etapa, $e); //al pasarle el parametro $e (evento) los eventos trazan segun lo definido en el modelado
            } else {
                echo 'no evalua regla ' . $e->Accion->nombre . ' tipo ' . $e->Accion->tipo . ' ejectuar: ' . $ejecutar . PHP_EOL;
            }
        }
    }

    function ejecutar_eventos_tarea_despues($etapa) {
        //ejecuta los eventos despues de ejecutar la tarea
        $eventos_despues_tarea = Doctrine_Query::create()->from('Evento e')
                ->where('e.tarea_id = ? AND e.instante = ? AND e.paso_id IS NULL', array($etapa->Tarea->id, 'despues'))
                ->execute();
        foreach ($eventos_despues_tarea as $e) {
            $ejecutar = false;
            $eventos_permitidos = explode('|', TIPO_EVENTOS_EJECUTAR);
            foreach ($eventos_permitidos as $tipo_evento) {
                if ($e->Accion->tipo == $tipo_evento) {
                    $ejecutar = true;
                    break;
                }
            }

            echo 'evaluando ' . $e->Accion->nombre . ' tipo ' . $e->Accion->tipo . ' ejectuar: ' . $ejecutar . PHP_EOL;
            if (!$ejecutar) {
                continue;
            }

            $r = new Regla($e->regla);
            if ($r->evaluar($etapa->id)) {
                echo 'Se ejecuta el evento ' . $e->Accion->nombre . ' tipo ' . $e->Accion->tipo . PHP_EOL;
                $this->guardar_log('Se ejecuta el evento ' . $e->Accion->nombre . ' tipo ' . $e->Accion->tipo);
                $e->Accion->ejecutar($etapa, $e); //al pasarle el parametro $e (evento) los eventos trazan segun lo definido en el modelado
            } else {
                echo 'no evalua regla ' . $e->Accion->nombre . ' tipo ' . $e->Accion->tipo . ' ejectuar: ' . $ejecutar . PHP_EOL;
            }
        }
    }

    function marcar_etapa_avanzada_por_cron_pagos($etapa) {
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('tarea_avanzada_cron_pagos', $etapa->id);
        if ($dato) {
            $dato->delete();
        }
        $dato = new DatoSeguimiento();
        $dato->etapa_id = $etapa->id;
        $dato->nombre = 'tarea_avanzada_cron_pagos';
        $dato->valor = '1';
        $dato->save();
    }

    function guardar_log($mensaje, $sin_fecha = null) {
        /* $log = fopen(__DIR__.'/../../../uploads/datos/cron_pago_antel.log', "a");

          if($sin_fecha){
          fwrite($log, $mensaje ."\n");
          }
          else{
          fwrite($log, date("Y-m-d H:i:s").' ---> '. $mensaje ."\n");
          }
          fclose($log); */
    }

    public function avanzar_sin_ejecutar_eventos($this_etapa, $usuarios_a_asignar = null) {
        Doctrine_Manager::connection()->beginTransaction();
        // Cerramos esta etapa
        $this->cerrar_sin_ejecutar_eventos($this_etapa);

        $tp = $this_etapa->getTareasProximas();
        if ($tp->estado != 'sincontinuacion') {
            if ($tp->estado == 'completado') {
                if ($this_etapa->Tramite->getEtapasActuales()->count() == 0)
                    $this->cerrar_tramite_sin_ejecutar_eventos($this_etapa->Tramite);
            }
            else {
                if ($tp->estado == 'pendiente') {
                    $tareas_proximas = $tp->tareas;
                    foreach ($tareas_proximas as $tarea_proxima) {
                        $etapa = new Etapa();
                        $etapa->tramite_id = $this_etapa->Tramite->id;
                        $etapa->tarea_id = $tarea_proxima->id;
                        $etapa->pendiente = 1;
                        $etapa->save();

                        $usuario_asignado_id = NULL;
                        if ($tarea_proxima->asignacion == 'ciclica') {
                            $usuarios_asignables = $etapa->getUsuarios();
                            $usuario_asignado_id = $usuarios_asignables[0]->id;
                            $ultimo_usuario = $tarea_proxima->getUltimoUsuarioAsignado($this_etapa->Tramite->Proceso->id);
                            if ($ultimo_usuario) {
                                foreach ($usuarios_asignables as $key => $u) {
                                    if ($u->id == $ultimo_usuario->id) {
                                        $usuario_asignado_id = $usuarios_asignables[($key + 1) % $usuarios_asignables->count()]->id;
                                        break;
                                    }
                                }
                            }
                        } else if ($tarea_proxima->asignacion == 'manual') {
                            $usuario_asignado_id = $usuarios_a_asignar[$tarea_proxima->id];
                        } else if ($tarea_proxima->asignacion == 'usuario') {
                            $regla = new Regla($tarea_proxima->asignacion_usuario);
                            $u = $regla->evaluar($this_etapa->id);
                            $usuario_asignado_id = $u;
                        }

                        //Para mas adelante poder calcular como hacer las uniones
                        if ($tp->conexion == 'union')
                            $etapa->etapa_ancestro_split_id = null;
                        else if ($tp->conexion == 'paralelo' || $tp->conexion == 'paralelo_evaluacion')
                            $etapa->etapa_ancestro_split_id = $this_etapa->id;
                        else
                            $etapa->etapa_ancestro_split_id = $this_etapa->etapa_ancestro_split_id;

                        $etapa->save();
                        $etapa->vencimiento_at = $etapa->calcularVencimiento();
                        $etapa->save();

                        if ($usuario_asignado_id)
                            $this->asignar_sin_ejecutar_eventos($usuario_asignado_id, $etapa);

                        $etapa->notificarTareaPendiente();
                    }
                    $this_etapa->Tramite->updated_at = date("Y-m-d H:i:s");
                    $this_etapa->Tramite->save();
                }
            }
        }
        Doctrine_Manager::connection()->commit();
    }

    public function cerrar_sin_ejecutar_eventos($this_etapa) {
        // Si ya fue cerrada, retornamos inmediatamente.
        if (!$this_etapa->pendiente)
            return;

        //si se ejecuta desde la conciliacion no se tiene session
        //nunca se debe generar esta variable
        if ($this_etapa->Tarea->almacenar_usuario) {
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this_etapa->Tarea->almacenar_usuario_variable, $this_etapa->id);
            if (!$dato)
                $dato = new DatoSeguimiento();
            $dato->nombre = $this_etapa->Tarea->almacenar_usuario_variable;
            $dato->valor = $this_etapa->usuario_id;
            $dato->etapa_id = $this_etapa->id;
            $dato->save();
        }

        //Cerramos la etapa
        $this_etapa->pendiente = 0;
        $this_etapa->ended_at = date('Y-m-d H:i:s');
        $this_etapa->save();
    }

    public function asignar_sin_ejecutar_eventos($usuario_id, $this_etapa) {
        if (!$this_etapa->canUsuarioAsignarsela($usuario_id))
            return;

        $this_etapa->usuario_id = $usuario_id;
        $this_etapa->save();
    }

    public function cerrar_tramite_sin_ejecutar_eventos($this_tramite) {
        Doctrine_Manager::connection()->beginTransaction();

        foreach ($this_tramite->Etapas as $e) {
            $this->cerrar_sin_ejecutar_eventos($e);
        }
        $this_tramite->pendiente = 0;
        $this_tramite->ended_at = date('Y-m-d H:i:s');
        $this_tramite->save();

        Doctrine_Manager::connection()->commit();
    }

}

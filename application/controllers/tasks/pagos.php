<?php

class Pagos extends CI_Controller {

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
        echo 'Iniciando proceso de conciliacion de pagos, aguarde por favor. (versión 1.5).' . PHP_EOL;

        //lock por base
        echo 'Obteniendo  lock por base de datos.....' . PHP_EOL;
        $lockdb = $this->load->database('default', TRUE);
        $lockdb->trans_start();
        $lockdb->query('LOCK TABLES lock_task WRITE');
        echo 'obtuvo lock continua con el proceso ' . PHP_EOL;


        //echo 'Obteniendo  lock por file ubicado en /var/tmp/pagosblockfile .....' . PHP_EOL;
        //$file_handle = fopen("/var/tmp/pagosblockfile","w");
        //flock($file_handle, LOCK_EX);
        //echo 'Obtuvo lock continua ' . PHP_EOL;

        $conn = Doctrine_Manager::connection();
        $stmt = $conn->prepare('select id from etapa where pendiente = 1 order by id');
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        $total = count($datos);

        echo 'Total de etapas pendientes a procesar: ' . $total . ' ' . PHP_EOL;

        $limit = 1000;
        $offset = 0;



        while ($offset < ($total + $limit)) {

            echo 'Inicia bucle limit ' . $limit . ' offset ' . $offset . PHP_EOL;

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

                                if ($pago_fila) {

                                    if ($pago_fila->estado == 'realizado') {
                                        echo 'Etapa id ' . $etapa->id . ' con pago id ' . $pago_fila->id . ' en estado ' . $pago_fila->estado . ' SE CIERRA ' . PHP_EOL;
                                        $this->cerrarEtapa($etapa);
                                    } else if ($pago_fila->estado == 'rc') {
                                        echo 'Etapa id ' . $etapa->id . ' con pago id ' . $pago_fila->id . ' en estado ' . $pago_fila->estado . ' SE CIERRA ' . PHP_EOL;
                                        $this->cerrarEtapa($etapa);
                                    } else if ($pago_fila->id_solicitud > 0 && ($pago_fila->estado == 'pendiente' || $pago_fila->estado == 'iniciado' || $pago_fila->estado == 'token_solicita' )) {
                                        //invoca WS

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
                                        } else {

                                            $xml = new SimpleXMLElement($ws_response);
                                            $nuevo_estado = $xml->xpath("//*[local-name() = 'IdEstado']/text()");
                                            echo '*** WS Response OK: ' . $nuevo_estado[0] . PHP_EOL;

                                            //if ($pago_fila->id_solicitud == 5114) {
                                            //echo('Mensaje -  Consulta pago' . " -IdPago: " . $pago_fila_primero->id . " MD5-" . md5($pago_fila_primero->id . '_clave_tramite_pasarela_pagos') . " -Tramite: " . $pago_fila->id_tramite . " -Clave: " . $clave_tramite->valor . " -Etapa: " . $pago_fila_primero->id_etapa . "\n" . " -Respuesta" . $ws_response . "");
                                            // }
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

                                                    $this->generar_variables_pago($registro_pago, $xml, $registro_pago->id_solicitud, $etapa->id);
                                                    $this->cerrarEtapa($etapa);
                                                    echo '********* SE CIERRA ' . PHP_EOL;
                                                    break;
                                                case '9':
                                                    //Paga se debe cerrar la etapa
                                                    //if (!empty($pago_fila)) {
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
                                                    //}
                                                    $this->generar_variables_pago($registro_pago, $xml, $registro_pago->id_solicitud, $etapa->id);
                                                    $this->cerrarEtapa($etapa);
                                                    echo '********* SE CIERRA ' . PHP_EOL;
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

                                    if ($dato) {
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
                                        } else if ($dato->valor == 'reachazado') {
                                            $regla = new Regla($pasarela->descripcion_reachazado_traza);
                                            $descripcion_traza = $regla->getExpresionParaOutput($etapa->id);
                                        } else {
                                            $descripcion_traza = 'consulta estado pasarela de pagos antel';
                                        }
                                    } else {
                                        $descripcion_traza = 'consulta estado pasarela de pagos antel';
                                    }
                                    echo ("Datos:  - ". $secuencia." - ".$descripcion_traza." - ".$pago_realizado);

                                    enviar_traza_linea_pago($etapa, $secuencia, $descripcion_traza, $pago_realizado,$paso);
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
            } else {
                echo PHP_EOL;
                echo 'No se han encontrado etapas pendientes.' . PHP_EOL;
            }
            //aumenta el offset
            $offset = $offset + $limit;
        }

        echo 'Libera lock fin del proceso ' . PHP_EOL;

        //lock por file
        //fclose($file_handle);
        //lock por base

        $lockdb->query('UNLOCK TABLES;');
        $lockdb->trans_complete();
        $lockdb->close();
    }

    function cerrarEtapa($etapa) {
        enviar_traza_final_tarea($etapa);
        $etapa->avanzar();
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

}

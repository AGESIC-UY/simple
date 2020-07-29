<?php

//MGAP
//
//0 Pendiente
//1 Paga
//8 Rechazada la por pasarela
//9 Cancelada
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
//Estados definidos para los que SIMPLE cancela las reservas de SAE sin consultar el estado a la pasarela.
define('ESTADOS_CANCELAR', '"E","P","X","A","R"');

//Estados definidos para los que SIMPLE si consulta la pasarela antes de cancelar la reserva en SAE.
//Únicamente si la resuesta de la pasarela es un estado definido en ESTADOS_BUSCAR se cancela la reserva.
define('ESTADOS_BUSCAR', '"E","P","X","A","R"');


//los tipos de eventos permitidos a ejecutar cuando avanza la tarea y 'camina'
//los pasos
define('TIPO_EVENTOS_EJECUTAR', 'enviar_correo|webservice_extended');

//solo considera las etapas de estos proceso definidos
define('PROCESOS', '9086');
define('DIAS_EVALUAR', '1');

class PagosGenericaAgendas extends CI_Controller {

    public function __construct() {
        parent::__construct();

        if (!$this->input->is_cli_request()) {
            redirect(site_url());
        }

        $this->load->helper('trazabilidad_helper');
    }

    public function conciliacion() {
        if (!is_numeric(DIAS_EVALUAR)) {
            define('DIAS', '0');
        } else {
            define('DIAS', DIAS_EVALUAR);
        }
        //12 horas de ejecucion
        ini_set('max_execution_time', 43200);
        //sin limite de memoria, la libera al terminar
        ini_set('memory_limit', '-1');
        $this->session->set_userdata('cron_exc', 'true');
        echo(' ' . PHP_EOL);
        echo('***************************************** ' . strftime(date("d F Y H:i:s")) . ' ****************************************************' . PHP_EOL);
        echo('************************** Iniciando proceso de conciliación Agenda - Pago Genérico. (versión 1.0).**********************' . PHP_EOL);
        echo('************************ Tiempo definido para cancelar la reserva ' . DIAS . ' días después de estar confimada *********************' . PHP_EOL);
        echo('*************************************************************************************************************************' . PHP_EOL);
        echo(' ' . PHP_EOL);

        $this->guardar_log(' ', false);
        $this->guardar_log(' ', true);
        $this->guardar_log(' ', false);
        $this->guardar_log('***************************************** ' . strftime(date("d F Y H:i:s")) . ' ***********************************************', false);
        $this->guardar_log('********************* Iniciando proceso de conciliación Agenda - Pago Genérico. (versión 1.0).**********************', false);
        $this->guardar_log('******************* Tiempo definido para cancelar la reserva ' . DIAS . ' días después de estar confimada *********************', false);
        $this->guardar_log('********************************************************************************************************************', false);
        $this->guardar_log(' ', false);
        $estados_buscar = explode(',', ESTADOS_BUSCAR);
        $estados_avanzar = explode(',', ESTADOS_CANCELAR);

        //lock por base
        echo 'Obteniendo lock por base de datos.....' . PHP_EOL;
        $this->guardar_log('Obteniendo lock por base de datos.....');
        $lockdb = $this->load->database('default', TRUE);
        $lockdb->trans_start();
        $lockdb->query('LOCK TABLES lock_task WRITE');

        echo 'obtuvo lock continua con el proceso ' . PHP_EOL;
        $this->guardar_log('obtuvo lock continua con el proceso');

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

            if (is_array($datos)) {
                $spl = array_slice($datos, $offset, $limit);
            } else {
                $spl = false;
            }
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
                    $campo_pago = null;
                    $campo_agenda = null;
                    $campo_agenda_multiple = null;
                    $paso_pago = null;
                    $paso_agenda = null;
                    foreach ($etapa->Tarea->Pasos as $paso) {
                        foreach ($paso->Formulario->Campos as $campo) {
                            if ($campo->tipo == 'pagos') {
                                $campo_pago = $campo;
                                $paso_pago = $paso;
                            }
                            if ($campo->tipo == 'agenda_sae') {
                                $campo_agenda = $campo;
                                $paso_agenda = $paso;
                            }
                            if ($campo->tipo == 'agenda_multiple_sae') {
                                $campo_agenda_multiple = $campo;
                                $paso_agenda = $paso;
                            }
                        }
                    }
                    if ($campo_pago && $campo_agenda) {
                        echo 'Analizando -> Campo Pago ' . $campo_pago->id . ' Campo Agenda Común ' . $campo_agenda->id . ' Etapa ' . $etapa->id . PHP_EOL;
                        $this->guardar_log('Analizando -> Campo Pago ' . $campo_pago->id . ' Campo Agenda Común ' . $campo_agenda->id . ' Etapa ' . $etapa->id, false);
                        $agenda_confirmada = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda->nombre, $etapa->id);
                        if ($agenda_confirmada) {
                            $datos = $agenda_confirmada->valor;
                            $confirmada_fecha = $datos->fecha_confirmacion_reserva;
                            if ($confirmada_fecha) {
                                $fecha = strtotime('+' . DIAS . ' day', $confirmada_fecha);
                                if (strtotime(date("Y-m-d")) >= $fecha) {
                                    $se_cancela = $this->consultarPago($etapa, $paso_pago);
                                    if ($se_cancela) {
                                        echo 'Se procede a cancelar la reserva de la Agenda Común ' . $campo_agenda->id . ' para la Etapa ' . $etapa->id . PHP_EOL;
                                        $this->guardar_log('Se procede a cancelar la reserva de la Agenda Común ' . $campo_agenda->id . ' para la Etapa ' . $etapa->id, false);
                                        $codigoCancelacion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda->nombre . '_codigoCancelacion', $etapa->id);
                                        $idReserva = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda->nombre . '_id', $etapa->id);
                                        $url = $campo_agenda->extra->url_base . '/cancelar_reserva';
                                        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda->nombre . '_recurso', $etapa->id);
                                        $recurso = $datos_seguimiento->valor;
                                        $data_array = array(
                                            "token" => $campo_agenda->extra->token,
                                            "idEmpresa" => $campo_agenda->extra->id_empresa,
                                            "idAgenda" => $campo_agenda->extra->id_agenda,
                                            "idReserva" => $idReserva->valor,
                                            "codigoCancelacion" => $codigoCancelacion->valor,
                                            "idioma" => 'es'
                                        );
                                        $this->cancelarAgenda($data_array, $url);
                                        $this->limpiarDatosAgendaComun($etapa, $campo_agenda);
                                        //$this->cerrarEtapa($etapa, $paso_pago->orden - 1);
                                    }
                                }
                            }
                        }
                    } else if ($campo_pago && $campo_agenda_multiple) {
                        echo 'Analizando -> Campo Pago ' . $campo_pago->id . ' Campo Agenda Múltiple ' . $campo_agenda_multiple->id . ' Etapa ' . $etapa->id . PHP_EOL;
                        $this->guardar_log('Analizando -> Campo Pago ' . $campo_pago->id . ' Campo Agenda Múltiple ' . $campo_agenda_multiple->id . ' Etapa ' . $etapa->id, false);
                        $agenda_confirmada = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_multiple->nombre . '_confirmada_reservas', $etapa->id);
                        if ($agenda_confirmada) {
                            $datos_fecha = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_multiple->nombre . '_fecha_confirmacion', $etapa->id);
                            $confirmada_fecha = $datos_fecha->valor;

                            if ($confirmada_fecha) {
                                $fecha = strtotime('+' . DIAS . ' day', $confirmada_fecha);
                                //echo date("Y-m-d", $fecha);
                                if (strtotime(date("Y-m-d")) >= $fecha) {
                                    $se_cancela = $this->consultarPago($etapa, $paso_pago);
                                    if ($se_cancela) {
                                        echo 'Se procede a cancelar las reservas de la Agenda Múltiple ' . $campo_agenda_multiple->id . ' para la Etapa ' . $etapa->id . PHP_EOL;
                                        $this->guardar_log('Se procede a cancelar las reservas de la Agenda Múltiple ' . $campo_agenda_multiple->id . ' para la Etapa ' . $etapa->id, false);
                                        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_multiple->nombre . '_recurso', $etapa->id);
                                        $id_recurso = $datos_seguimiento->valor;
                                        $datos_seguimiento_json = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_multiple->nombre . '__json_reserva', $etapa->id);
                                        if ($datos_seguimiento_json) {
                                            $json_reservar = $datos_seguimiento_json->valor;
                                            $url = $campo_agenda_multiple->extra->url_base . '/cancelar_reserva';
                                            foreach ($json_reservar as $key => $value) {
                                                $id_reserva = $value->id_reserva;
                                                $codigoCancelacion = $value->codigoCancelacion;
                                                $data_array = array(
                                                    "token" => $campo_agenda_multiple->extra->token,
                                                    "idEmpresa" => $campo_agenda_multiple->extra->id_empresa,
                                                    "idAgenda" => $campo_agenda_multiple->extra->id_agenda,
                                                    "idRecurso" => $id_recurso,
                                                    "idReserva" => $id_reserva,
                                                    "codigoCancelacion" => $codigoCancelacion,
                                                    "idioma" => 'es'
                                                );
                                                $this->cancelarAgenda($data_array, $url);
                                            }
                                            $datos_seguimiento_json->valor = json_encode("");
                                            $datos_seguimiento_json->save();
                                            //$this->cerrarEtapa($etapa, $paso_pago->orden - 1);
                                            $this->limpiarDatosAgendaMultiple($etapa, $campo_agenda_multiple);
                                        }
                                    }
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
                $this->guardar_log('No se han encontrado etapas pendientes.' . PHP_EOL);
            }
            //aumenta el offset
            $offset = $offset + $limit;
        }

        echo 'Libera lock fin del proceso ' . PHP_EOL;
        $this->guardar_log('Libera lock fin del proceso');

        $lockdb->query('UNLOCK TABLES;');
        $lockdb->trans_complete();
        $lockdb->close();
    }

    function consultarPago($etapa, $paso) {
        $se_cancela = false;

        $estados_buscar = explode(',', ESTADOS_BUSCAR);
        $estados_cancelar = explode(',', ESTADOS_CANCELAR);
        $conn = Doctrine_Manager::connection();
        $queryPago = 'select p.id FROM pago p  where p.id_etapa = ' . $etapa->id . '
                          and p.estado IN ("iniciado",' . ESTADOS_BUSCAR . ') and
                          not exists (select 1 from pago p1 where p1.id_etapa =p.id_etapa and p1.id > p.id)';



        $stmtPago = $conn->prepare($queryPago);
        $stmtPago->execute();
        $datosPago = $stmtPago->fetchAll(PDO::FETCH_COLUMN, 0);
        $id_pago_fila = isset($datosPago[0]) ? $datosPago[0] : null;

        if ($id_pago_fila && !empty($id_pago_fila) && $id_pago_fila != '') {
            $pago_fila = Doctrine::getTable('Pago')->find($id_pago_fila);
            $pasarela = Doctrine::getTable('PasarelaPagoGenerica')->find($pago_fila->pasarela);
        } else {
            $pago_fila = null;
            $pasarela = null;
            $se_cancela = false;
        }

        if ($pago_fila && $pasarela) {
            echo 'Procesa pago fila ' . $id_pago_fila . PHP_EOL;
            $consulta_pago_realizada = false;
            $estado_base = '"' . $pago_fila->estado . '"';
            if (in_array($estado_base, $estados_cancelar)) {
                // echo 'mal';
                $se_cancela = true;
                echo '*** Estado ACTUAL PARA CANCELAR: ' . $pago_fila->estado . PHP_EOL;
            } else if (in_array($pago_fila->estado, $estados_buscar) || $pago_fila->estado == "iniciado") {
                // echo 'Invoca';
                //invoca WS
                $variable_idestado = $pasarela->variable_idestado;
                $codigo_operacion_soap = $pasarela->codigo_operacion_soap_consulta;

                $operacion = Doctrine_Query::create()
                        ->from('WsOperacion o')
                        ->where('o.codigo = ?', $codigo_operacion_soap)
                        ->fetchOne();
                $servicio = Doctrine::getTable('WsCatalogo')->find($operacion->catalogo_id);

                echo 'Invoca WS Etapa id ' . $etapa->id . ' con pago id ' . $pago_fila->id . ' en estado ' . $pago_fila->estado . ' id sol ' . $pago_fila->id_solicitud . PHP_EOL;
                $this->guardar_log('Invoca WS Etapa id ' . $etapa->id . ' con pago id ' . $pago_fila->id . ' en estado ' . $pago_fila->estado . ' id sol ' . $pago_fila->id_solicitud);

                $ci = get_instance();
                $ci->load->helper('soap_execute');
                soap_execute($etapa, $servicio, $operacion, $operacion->soap);


                $error_servicio_pagos = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("ws_error", $etapa->id);

                if ($error_servicio_pagos) {
                    echo '*** WS Response error code:' . $error_servicio_pagos->valor . PHP_EOL;
                    $this->guardar_log('*** WS Response error code:' . $error_servicio_pagos->valor);
                } else {
                    $id_estado = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $variable_idestado), $etapa->id);
                    echo '*** WS Response OK: ' . $id_estado->valor . PHP_EOL;
                    $this->guardar_log('*** WS Response OK: ' . $id_estado->valor);

                    $estado_pasarela = '"' . $id_estado->valor . '"';
                    echo '*** CONDICION: Estado pasarela' . $estado_pasarela . " Comparando: " . in_array($estado_pasarela, $estados_buscar) . PHP_EOL;
                    if (in_array($estado_pasarela, $estados_buscar)) {
                        echo '*** Estado ACTUAL PARA CANCELAR WS: ' . $id_estado->valor . PHP_EOL;
                        $se_cancela = true;
                    } else {
                        $se_cancela = false;
                    }
                    $consulta_pago_realizada = true;
                }
            } else {
                $se_cancela = false;
            }

            //-- comienza TRAZABILIDAD CONSULTA ESTADO DEL PAGO
            $secuencia = $paso->orden - 1;

            $descripciones_estados_traza = json_decode($pasarela->descripciones_estados_traza);
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago_generico', $etapa->id);

            if (count($descripciones_estados_traza) > 0 && $dato) {
                foreach ($descripciones_estados_traza as $descripcion) {
                    if (trim($descripcion->codigo) != '' && trim($descripcion->valor) != '') {
                        if ((int) $descripcion->codigo == (int) $dato->valor) {
                            $descripcion_traza = $descripcion->valor;
                            break;
                        }
                    }
                }

                if (!$descripcion_traza) {
                    $descripcion_traza = 'Consulta de estado pasarela generica';
                }
            } else {
                $descripcion_traza = 'Consulta de estado pasarela generica';
            }

            enviar_traza_linea_pago($etapa, $secuencia, $descripcion_traza, $consulta_pago_realizada, $paso);
            //-- termina TRAZABILIDAD CONSULTA ESTADO DEL PAGO
        } else {
            $queryPago = 'select p.id FROM pago p  where p.id_etapa = ' . $etapa->id;
            $stmtPago = $conn->prepare($queryPago);
            $stmtPago->execute();
            $datosPago = $stmtPago->fetchAll(PDO::FETCH_COLUMN, 0);
            if ($datosPago) {
                echo '*** NO SE CANCELA PORQUE EXISTE EL PAGO Y NO ES UN ESTADO A BUSCAR: ' . PHP_EOL;
                $se_cancela = false;
            } else {
                echo '*** SE CANCELA PORQUE NO EXISTE EL PAGO O LA PASARELA: ' . PHP_EOL;
                $se_cancela = true;
            }
        }
        return $se_cancela;
    }

    function cancelarAgenda($data_array, $url) {
        $data = json_encode($data_array);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);

        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data))
        );

        $result = curl_exec($curl);
        $curl_errno = curl_errno($curl);
        $curl_error = curl_error($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
        if ($curl_errno > 0 || $http_code != 200) {
            $log = fopen(__DIR__ . '/../../logs/agenda_sae.log', "a");
            fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR CURL: ' . $curl_error . ' (http code: ' . $http_code . ')' . "\n");
            fwrite($log, ' --> URL: ' . $url . "\n");
            fwrite($log, ' --> DATA: ' . $data . "\n");
            fwrite($log, ' --> RESULT: ' . $result . "\n");
            fclose($log);
        }
        return $result;
    }

    function cerrarEtapa($etapa, $secuencia_inicio) {
        $this->ejecutar_eventos_pasos_etapa($etapa, $secuencia_inicio);
        $this->marcar_etapa_avanzada_por_cron_pagos($etapa);
        enviar_traza_final_tarea($etapa);
        $this->avanzar_sin_ejecutar_eventos($etapa);
        enviar_traza_final_proceso($etapa); //verifica si es fin de proceso para enviar traza
    }

    function limpiarDatosAgendaMultiple($etapa, $campo_agenda_multiple) {
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_multiple->nombre . '_token_reservas', $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_multiple->nombre . '_empresa', $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_multiple->nombre . '_agenda', $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_multiple->nombre . '_codTramite', $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_multiple->nombre . '__json_reserva', $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_multiple->nombre . '__clonada', $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_multiple->nombre . '__fecha_modificacion', $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_multiple->nombre . '_confirmada_reservas', $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_multiple->nombre . '_fecha_confirmacion', $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_multiple->nombre . '_reservas', $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_multiple->nombre . '_recurso', $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_multiple->nombre, $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        echo 'Limpiando datos de la agenda ' . $campo_agenda_multiple->nombre . PHP_EOL;
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
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('tarea_avanzada_cron_pagos_agendas', $etapa->id);
        if ($dato) {
            $dato->delete();
        }
        $dato = new DatoSeguimiento();
        $dato->etapa_id = $etapa->id;
        $dato->nombre = 'tarea_avanzada_cron_pagos_agendas';
        $dato->valor = '1';
        $dato->save();
    }

    function guardar_log($mensaje, $sin_fecha = null) {
        if (defined(CRON_LOGS)) {
            if (CRON_LOGS == 1) {
                $log = fopen(__DIR__ . '/../../../uploads/datos/cron_agenda_pago_generica.log', "a+");
                if (!$sin_fecha) {
                    fwrite($log, $mensaje . "\n");
                } else {
                    fwrite($log, date("Y-m-d H:i:s") . ' ---> ' . $mensaje . "\n");
                }
                fclose($log);
            }
        }
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

    function limpiarDatosAgendaComun($etapa, $campo_agenda_comun) {
        echo 'Limpiando datos de la agenda ' . $campo_agenda_comun->nombre . PHP_EOL;
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_comun->nombre . '_id', $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_comun->nombre . '_serieNumero', $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_comun->nombre . '_codigoCancelacion', $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_comun->nombre . '_codigoTrazabilidad', $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_comun->nombre . '_textoTicket', $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_agenda_comun->nombre, $etapa->id);
        if ($datos_seguimiento)
            $datos_seguimiento->delete();
    }

}

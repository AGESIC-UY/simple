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


//DESDE y HASTA en minutos, Considera las etapas que su fecha
//de creacion es mayor igual a now()-desde y menor igual a now()-hasta
define('DESDE', '920');
define('HASTA', '0');

//los tipos de eventos permitidos a ejecutar cuando avanza la tarea y 'camina'
//los pasos
define('TIPO_EVENTOS_EJECUTAR', 'enviar_correo|webservice_extended');

//solo considera las etapas de estos proceso definidos
define('PROCESOS', '9068');
define('FECHA_COMPARAR', 'created_at'); //created_at || updated_at

class PagosGenericaMinuts extends CI_Controller {

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
        echo 'Iniciando proceso de conciliacion de pagos genericos, aguarde por favor. (versión 1.7).' . PHP_EOL;
        $this->guardar_log(' ', true);
        $this->guardar_log('**************************************************** ' . strftime(date("d F Y H:i:s")) . ' ***************************************************************', true);
        $this->guardar_log('************************  Iniciando proceso de conciliacion de pagos genericos, aguarde por favor. (versión 1.7).*************************', true);
        $this->guardar_log('*****************************************************************************************************************************************************', true);
        $this->guardar_log(' ', true);

        $estados_buscar = explode(',', ESTADOS_BUSCAR);
        $estados_avanzar = explode(',', ESTADOS_AVANZAR);

        //lock por base
        echo 'Obteniendo lock por base de datos.....' . PHP_EOL;
        $this->guardar_log('Obteniendo lock por base de datos.....');
        $lockdb = $this->load->database('default', TRUE);
        $lockdb->trans_start();
        $lockdb->query('LOCK TABLES lock_task WRITE');
        echo 'obtuvo lock continua con el proceso ' . PHP_EOL;
        $this->guardar_log('obtuvo lock continua con el proceso');


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
                                //en pagos siempre primero iniciado y despues las siguientes opciones, Poruqe si cierra el navegador
                                //el usuario el estado queda siempre en iniciado
                                $conn = Doctrine_Manager::connection();
                                $queryPago = 'select p.id FROM pago p  where p.id_etapa = ' . $etapa->id . '
                          and p.estado IN ("iniciado",' . ESTADOS_BUSCAR . ') and
                          not exists (select 1 from pago p1 where p1.id_etapa =p.id_etapa and p1.id > p.id)';



                                $stmtPago = $conn->prepare($queryPago);
                                $stmtPago->execute();
                                $datosPago = $stmtPago->fetchAll(PDO::FETCH_COLUMN, 0);
                                $id_pago_fila = isset($datosPago[0]) ? $datosPago[0] : null;
                                //echo $queryPago . ' return : ' . $id_pago_fila .  PHP_EOL;

                                if ($id_pago_fila && !empty($id_pago_fila) && $id_pago_fila != '') {
                                    $pago_fila = Doctrine::getTable('Pago')->find($id_pago_fila);
                                    $pasarela = Doctrine::getTable('PasarelaPagoGenerica')->find($pago_fila->pasarela);
                                } else {
                                    $pago_fila = null;
                                    $pasarela = null;
                                }

                                if ($pago_fila && $pasarela) {
                                    echo 'Procesa pago fila ' . $id_pago_fila . PHP_EOL;
                                    $consulta_pago_realizada = false;
                                    if (in_array($pago_fila->estado, $estados_avanzar)) {
                                        echo 'Etapa id ' . $etapa->id . ' con pago id ' . $pago_fila->id . ' en estado ' . $pago_fila->estado . ' SE CIERRA ' . PHP_EOL;
                                        $this->guardar_log('Etapa id ' . $etapa->id . ' con pago id ' . $pago_fila->id . ' en estado ' . $pago_fila->estado . ' SE CIERRA ');
                                        $this->cerrarEtapa($etapa, $paso->orden - 1);
                                    } else {
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
                                            if (in_array($id_estado->valor, $estados_avanzar)) {
                                                echo 'Etapa id ' . $etapa->id . ' con pago id ' . $pago_fila->id . ' en estado ' . $id_estado->valor . ' SE CIERRA ' . PHP_EOL;
                                                $this->guardar_log('Etapa id ' . $etapa->id . ' con pago id ' . $pago_fila->id . ' en estado ' . $id_estado->valor . ' SE CIERRA ');
                                                $this->cerrarEtapa($etapa, $paso->orden - 1);
                                            }
                                            $consulta_pago_realizada = true;
                                        }
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

    function cerrarEtapa($etapa, $secuencia_inicio) {
        $this->ejecutar_eventos_pasos_etapa($etapa, $secuencia_inicio);
        $this->marcar_etapa_avanzada_por_cron_pagos($etapa);
        enviar_traza_final_tarea($etapa);
        $this->avanzar_sin_ejecutar_eventos($etapa);
        enviar_traza_final_proceso($etapa); //verifica si es fin de proceso para enviar traza
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

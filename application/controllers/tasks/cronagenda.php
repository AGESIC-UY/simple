<?php

define('DIAS_EVALUAR', '10');
define('HORAS_EVALUAR', '0');
define('TIPO_EVENTOS_EJECUTAR', 'enviar_correo');
define('PROCESOS', '88938');

//FALSE: No valida si hay componente de pago en la etapa. El cron se ejecuta con normalidad.
//TRUE: Valida si hay componente de pago en la etapa. Si no hay componente, el cron procesa las etapas con normalidad.
//TRUE: Valida si hay componente de pago en la etapa. Si hay componente, el cron queda sin efecto no procesando ninguna etapa.
define('EVALUAR_COMPONENTE_PAGO', TRUE); // TRUE o FALSE

class CronAgenda extends CI_Controller {

    public function __construct() {
        parent::__construct();

        if (!$this->input->is_cli_request()) {
            redirect(site_url());
        }

        $this->load->helper('trazabilidad_helper');
    }

    public function revision() {
        if (!is_numeric(DIAS_EVALUAR)) {
            define('DIAS', '0');
        } else {
            define('DIAS', DIAS_EVALUAR);
        }
        if (!is_numeric(HORAS_EVALUAR)) {
            define('HORAS', '0');
        } else {
            define('HORAS', HORAS_EVALUAR);
        }

        ini_set('max_execution_time', 43200);
        //sin limite de memoria, la libera al terminar
        ini_set('memory_limit', '-1');
        $this->session->set_userdata('cron_exc', 'true');
        echo(' ' . PHP_EOL);
        echo('***************************************** ' . strftime(date("d F Y H:i:s")) . ' ****************************************************' . PHP_EOL);
        echo('************************ Iniciando proceso de verificación de Agenda Múltiple. (versión 1.0).*************************' . PHP_EOL);
        echo('***************** Rango de tiempo definido ' . DIAS . ' días y ' . HORAS . ' horas antes de la primera reserva del lote ********************' . PHP_EOL);
        echo('**********************************************************************************************************************' . PHP_EOL);
        echo(' ' . PHP_EOL);

        $this->guardar_log(' ', false);
        $this->guardar_log(' ', true);
        $this->guardar_log(' ', false);
        $this->guardar_log('***************************************** ' . strftime(date("d F Y H:i:s")) . ' ****************************************************', false);
        $this->guardar_log('************************ Iniciando proceso de verificación de Agenda Múltiple. (versión 1.0).*************************', false);
        $this->guardar_log('***************** Rango de tiempo definido ' . DIAS . ' días y ' . HORAS . ' horas antes de la primera reserva del lote ********************', false);
        $this->guardar_log('**********************************************************************************************************************', false);
        $this->guardar_log(' ', false);

        //lock por base
        echo 'Obteniendo lock por base de datos.....' . PHP_EOL;
        $this->guardar_log('Obteniendo lock por base de datos.....', false);

        $lockdb = $this->load->database('default', TRUE);
        $lockdb->trans_start();
        $lockdb->query('LOCK TABLES lock_task WRITE');
        echo 'obtuvo lock continua con el proceso ' . PHP_EOL;

        echo 'Contando cantidad total de etapas pendientes' . PHP_EOL;
        $this->guardar_log('Contando cantidad total de etapas pendientes', false);
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
        $this->guardar_log('Consulta a base de datos ' . $queryStr, false);

        $stmt = $conn->prepare($queryStr);
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        $total = count($datos);

        echo 'Total de etapas procesar: ' . $total . ' ' . PHP_EOL;
        $this->guardar_log('Total de etapas procesar: ' . $total, false);

        $limit = 1000;
        $offset = 0;

        while ($offset < ($total + $limit)) {

            echo 'Inicia bucle limit ' . $limit . ' offset ' . $offset . PHP_EOL;
            $this->guardar_log('Inicia bucle limit ' . $limit . ' offset ' . $offset, false);

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
                $this->guardar_log('Cantidad ' . count($etapas_pendientes), false);

                foreach ($etapas_pendientes as $etapa) {
                    foreach ($etapa->Tarea->Pasos as $paso) {
                        if ($etapa->pendiente == 1) {
                            if ($this->noTienePago($etapa)) {
                                foreach ($paso->Formulario->Campos as $campo) {
                                    if ($campo->tipo == 'agenda_multiple_sae') {
                                        $agenda_confirmada = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo->nombre . '_confirmada_reservas', $etapa->id);
                                        if ($agenda_confirmada) {
                                            $fecha_permitida = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo->nombre . '__fecha_modificacion', $etapa->id);
                                            if ($fecha_permitida) {
                                                echo 'Analizando -> Campo ' . $campo->id . ' Etapa ' . $etapa->id . PHP_EOL;
                                                $this->guardar_log('Analizando -> Campo ' . $campo->id . ' Etapa ' . $etapa->id, false);

                                                echo 'Creando la fecha a comparar' . PHP_EOL;
                                                $this->guardar_log('Creando la fecha a comparar', false);

                                                $fecha = date("Y-m-d H:i:s", $fecha_permitida->valor);
                                                $fecha_permitida_comparar = date("Y-m-d H:i:s", strtotime('-' . HORAS . ' hour', strtotime($fecha)));
                                                $fecha_permitida_comparar = strtotime($fecha_permitida_comparar . ' -' . DIAS . ' day');
                                                echo 'Fecha a comparar ' . date("Y-m-d H:i:s", $fecha_permitida_comparar) . PHP_EOL;
                                                $this->guardar_log('Fecha a comparar ' . date("Y-m-d H:i:s", $fecha_permitida_comparar), false);

                                                if (strtotime(date("Y-m-d H:i:s")) > $fecha_permitida_comparar) {
                                                    echo 'Se avanza la etapa ' . $etapa->id . PHP_EOL;
                                                    $this->guardar_log('Se avanza la etapa ' . $etapa->id, false);
                                                    $this->cerrarEtapa($etapa, $paso->orden - 1);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                echo PHP_EOL;
                echo 'Bucle completado offset ' . $offset . PHP_EOL;
                $this->guardar_log('Bucle completado offset ' . $offset, false);
            } else {
                echo PHP_EOL;
                echo 'No se han encontrado etapas pendientes.' . PHP_EOL;
                $this->guardar_log('No se han encontrado etapas pendientes.', false);
            }
            //aumenta el offset
            $offset = $offset + $limit;
        }

        echo 'Libera lock fin del proceso ' . PHP_EOL;
        $this->guardar_log('Libera lock fin del proceso ', false);
        $this->guardar_log(' ', false);
        $this->guardar_log('**********************************************************************************************************************', false);
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

    function noTienePago($etapa) {
        if (EVALUAR_COMPONENTE_PAGO) {
            foreach ($etapa->Tarea->Pasos as $paso) {
                foreach ($paso->Formulario->Campos as $campo) {
                    if ($campo->tipo == 'pagos') {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    function ejecutar_eventos_pasos_etapa($etapa, $secuencia_inicio) {
        echo 'ejecutar_eventos_pasos_etapa ' . $secuencia_inicio . ' ' . count($etapa->getPasosEjecutables()) . PHP_EOL;
        $secuencia_actual = 0;
        $secuencia_contador = 0;
        foreach ($etapa->getPasosEjecutables() as $paso) {
            echo 'ejecutar_eventos_pasos_etapa ' . $paso->orden . ' ' . count($etapa->getPasosEjecutables()) . PHP_EOL;
            $secuencia_actual = $paso->orden - 1;
            if ($secuencia_actual >= $secuencia_inicio) {
                $paso_final = sizeof($etapa->getPasosEjecutables()) - 1 == $secuencia_contador;
                if ($paso_final) {
                    if ($secuencia_actual != $secuencia_inicio) {
                        $this->ejecutar_eventos_paso_antes($paso, $etapa);
                    }
                    $this->ejecutar_eventos_paso_despues($paso, $etapa);
                    $this->ejecutar_eventos_tarea_despues($etapa);
                    break;
                } else {
                    echo 'ejecutar_eventos_pasos_etapa else ' . $secuencia_inicio . ' ' . $secuencia_actual . ' ' . PHP_EOL;
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
                $e->Accion->ejecutar($etapa, $e); //al pasarle el parametro $e (evento) los eventos trazan segun lo definido en el modelado
            } else {
                echo 'no evalua regla ' . $e->Accion->nombre . ' tipo ' . $e->Accion->tipo . ' ejectuar: ' . $ejecutar . PHP_EOL;
            }
        }
    }

    function marcar_etapa_avanzada_por_cron_pagos($etapa) {
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('tarea_avanzada_cron_agenda', $etapa->id);
        if ($dato) {
            $dato->delete();
        }
        $dato = new DatoSeguimiento();
        $dato->etapa_id = $etapa->id;
        $dato->nombre = 'tarea_avanzada_cron_agenda';
        $dato->valor = '1';
        $dato->save();
    }

    function guardar_log($mensaje, $sin_fecha = null) {
        if (defined(CRON_LOGS)) {
            if (CRON_LOGS == 1) {
                $log = fopen(__DIR__ . '/../../../uploads/datos/cron_agenda_multiple.log', "a+");
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

}

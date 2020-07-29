<?php

/*
  Envia el cabezal (online, sino lo encola), tomando en cuanta la configuracion de envio de guid automatico por email.
  Ademas envia la primera linea que marca el inicio de: nombre tarea. Se utiliza en el ejecutar() del controlador de etapas.
 */

function sendInvolucrado($etapa) {
    $CI = &get_instance();
    $enviar = $etapa->Tramite->Proceso->Cuenta->traza_involucrado;
    $listaTarea=$etapa->Tramite->Proceso->Tareas;
    $primeraTarea=$etapa->Tarea;
    foreach ($listaTarea as $tarea) {
        if($tarea->inicial){
            $primeraTarea=$tarea;
            break;
        }
    }
    if ($primeraTarea->inicial && $primeraTarea->acceso_modo != "registrados") {
        $CI->session->set_userdata('send_oid', false);
    } elseif ($enviar == 1) {
        $CI->session->set_userdata('send_oid', false);
    } elseif ($enviar == 2) {
        $CI->session->set_userdata('send_oid', true);
    } elseif ($enviar == 3 && $etapa->Tarea->Proceso->ProcesoTrazabilidad->traza_involucrado == 1) {
        $CI->session->set_userdata('send_oid', false);
    } elseif ($enviar == 3 && $etapa->Tarea->Proceso->ProcesoTrazabilidad->traza_involucrado == 2) {
        $CI->session->set_userdata('send_oid', true);
    } else {
        $CI->session->set_userdata('send_oid', false);
    }
}

function enviar_traza_cabezal($etapa) {

    $CI = &get_instance();

    if ($etapa->Tarea->trazabilidad) {
        sendInvolucrado($etapa);
        $CI->load->helper('trazabilidad_id_helper');
        $datos = trazabilidad_id();
        $canal_inicio = $datos->canal_inicio;
        $inicioAsistido = $datos->inicioAsistido;
        $oid = $datos->oid;

        (empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = '' : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);
        $num_paso_linea = 1;
        $sec_linea = 1;
        $cantidad_total_pasos = 0;
        $paso = $etapa->getPasoEjecutable(0);
        $formulario = $paso->Formulario;
        //print_r("Tareas ".count($formulario->Proceso->Tareas));
        foreach ($formulario->Proceso->Tareas as $tarea) {
            //print_r("Pasos ".count($tarea->Pasos));
            $cantidad_total_pasos = $cantidad_total_pasos + count($tarea->Pasos);
        }

        $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("id_transaccion_traza", $etapa->id);
        $id_transaccion = $id_transaccion->valor;

        $args = array(
            'tramite_id' => (string) $etapa->tramite_id,
            'secuencia' => (string) $sec_linea,
            'paso' => (string) $num_paso_linea,
            'organismo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->organismo_id,
            'oficina_id' => (string) $oficina_id,
            'proceso_externo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
            'pasos_ejecutables' => (string) $cantidad_total_pasos,
            'estado' => 'EN_EJECUCION', //EN EJECUCION
            'etapa' => (string) $etapa->id,
            'tarea' => (string) $etapa->Tarea->id,
            'canal_inicio' => (string) $canal_inicio,
            'role' => (string) "SOLICITANTE",
            'oid' => (string) $oid,
            'nombre_proceso' => (string) $etapa->Tarea->Proceso->nombre,
            'inicio_asistido' => (string) $inicioAsistido,
            'etiqueta' => (string) $etapa->Tarea->etiqueta_traza,
            'visibilidad' => (string) $etapa->Tarea->visible_traza,
            'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
            'tipoRegistroTrazabilidad' => "COMUN",
            'id_transaccion' => (string) $id_transaccion,
            'fechaOrganismo' => (string) str_replace(" ", "T", trim($etapa->created_at)));

        if ($etapa->Tarea->inicial) {
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(WS_VARIABLE_COD_TRAZABILIDAD, $etapa->id);

            if (!$dato || $dato->valor == '') {

                //genero guid online y guardo variable @@
                $genero_guid = trazabilidad_online_cabezal($args);

                //en caso de que no se haya podido generar online se hace store and fordware
                if (isset($genero_guid) && $genero_guid) {
                    $args['cabezal'] = 1;
                } else {
                    $args['cabezal'] = 0;
                }

                $traza_cabezal = Doctrine_Query::create()
                        ->from('Trazabilidad ts')
                        ->where('ts.id_tramite = ? AND ts.estado = ?', array($etapa->Tramite->id, 'C'))
                        ->orderBy('secuencia ASC')
                        ->fetchOne();

                if (empty($traza_cabezal)) {
                    $traza = new Trazabilidad();
                    $traza->id_etapa = $etapa->id;
                    $traza->id_tramite = $etapa->tramite_id;
                    $traza->id_tarea = $etapa->Tarea->id;
                    $traza->num_paso = 0;
                    $traza->secuencia = 0;
                    $traza->estado = 'C';
                    if ($args['cabezal'] == 0) {
                        $traza->enviar_correo = 0;
                    } else {
                        $proceso_trazabilidad = $formulario->Proceso->ProcesoTrazabilidad;
                        $cuenta = $etapa->Tramite->Proceso->Cuenta;

                        $envio_email = enviar_guid_email_automatico($proceso_trazabilidad, $cuenta, $traza, $etapa);

                        if (!$envio_email) {
                            $traza->enviar_correo = 0;
                        } else {
                            $traza->enviar_correo = 1;
                        }
                    }
                    $traza->save();
                }
                // -- Encola la operacion para enviar la linea (y en caso de que no se haya generado el guid el cabezal tambien ($args['cabezal_enviado'] = 0))
                if ($args['cabezal'] == 0) {
                    $CI->load->library('resque/resque');
                    Resque::enqueue('default', 'TrazaCabezal', $args);
                }
            }
        }
    }
}

/*
  Envia inicio de tarea.
 */

function enviar_traza_inicio_tarea($etapa, $secuencia) {

    $CI = &get_instance();

    if ($etapa->Tarea->trazabilidad) {
        sendInvolucrado($etapa);
        $CI->load->helper('trazabilidad_id_helper');
        $datos = trazabilidad_id();
        $canal_inicio = $datos->canal_inicio;
        $inicioAsistido = $datos->inicioAsistido;
        $oid = $datos->oid;

        (empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = '' : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);
        $num_paso_linea = 1;
        $sec_linea = 1;
        $cantidad_total_pasos = 0;
        $paso = $etapa->getPasoEjecutable($secuencia);
        $formulario = $paso->Formulario;
        foreach ($formulario->Proceso->Tareas as $tarea) {
            $cantidad_total_pasos = $cantidad_total_pasos + count($tarea->Pasos);
        }

        $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("id_transaccion_traza", $etapa->id);
        $id_transaccion = $id_transaccion->valor;

        $args = array(
            'tramite_id' => (string) $etapa->tramite_id,
            'secuencia' => (string) $sec_linea,
            'paso' => (string) $num_paso_linea,
            'organismo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->organismo_id,
            'oficina_id' => (string) $oficina_id,
            'proceso_externo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
            'pasos_ejecutables' => (string) $cantidad_total_pasos,
            'nombre_tarea' => (string) "Inicio de " . $etapa->Tarea->nombre,
            'estado' => 'EN_EJECUCION', //EN EJECUCION
            'etapa' => (string) $etapa->id,            
            'tarea' => (string) $etapa->Tarea->id,
            'canal_inicio' => (string) $canal_inicio,
            'role' => (string) "SOLICITANTE",
            'oid' => (string) $oid,
            'nombre_proceso' => (string) $etapa->Tarea->Proceso->nombre,
            'inicio_asistido' => (string) $inicioAsistido,
            'etiqueta' => (string) $etapa->Tarea->etiqueta_traza,
            'visibilidad' => (string) $etapa->Tarea->visible_traza,
            'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
            'tipoRegistroTrazabilidad' => "COMUN",
            'id_transaccion' => (string) $id_transaccion);
        if ($etapa->Tarea->inicial && $secuencia == 0) {

            $traza_linea = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.estado = ?', array($etapa->Tramite->id, 'I'))
                    ->orderBy('secuencia ASC')
                    ->fetchOne();

            if (empty($traza_linea)) {
                $traza = new Trazabilidad();
                $traza->id_etapa = $etapa->id;
                $traza->id_tramite = $etapa->tramite_id;
                $traza->id_tarea = $etapa->Tarea->id;
                $traza->num_paso = 1;
                $traza->secuencia = 1;
                $traza->estado = 'I';
                $traza->save();
            }

            // -- Encola la operacion para enviar la linea (y en caso de que no se haya generado el guid el cabezal tambien ($args['cabezal_enviado'] = 0))
            $CI->load->library('resque/resque');
            Resque::enqueue('default', 'TrazaLinea', $args);
        }else if (!$etapa->Tarea->inicial && $secuencia == 0) {
            $args['cabezal'] = 1;

            $tarea_inicial = $formulario->Proceso->getTareaInicial();

            //$cabezal = '0';
            $num_paso = $secuencia;
            $num_paso_linea = $secuencia + 1;

            if ((sizeof($etapa->getPasosEjecutables()) - 1) == $secuencia && !$tarea_inicial) {
                $estado = 'FINALIZADO';
                $estado_linea = 'F';
            } else {
                if (($tarea_inicial->id == $etapa->Tarea->id) && ($secuencia == 0)) {
                    //$cabezal = '1';
                    $estado = 'INICIO';
                } else {
                    $estado = 'EN_EJECUCION';
                }
                $estado_linea = 'I';
            }

            $traza_existente = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_etapa = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->id, 0))
                    ->limit(1)
                    ->fetchOne();

            $paso_existe = null;
            if (!empty($traza_existente)) {
                $paso_existe = $traza_existente->num_paso_real;
            }

            $traza_tramite = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ?', array($etapa->Tramite->id))
                    ->orderBy('secuencia DESC')
                    ->limit(1)
                    ->fetchOne();

            if (empty($traza_tramite)) {

                $sec = 0;
                $sec_linea = $sec + 1;

                $traza = new Trazabilidad();
                $traza->id_etapa = $etapa->id;
                $traza->id_tramite = $etapa->tramite_id;
                $traza->id_tarea = $etapa->Tarea->id;
                $traza->num_paso = $num_paso + 1;
                $traza->secuencia = $sec + 1;
                $traza->estado = $estado_linea;
                $traza->num_paso_real = 0;
                $traza->save();
            } else {
                $traza_tramite_actual = Doctrine_Query::create()
                        ->from('Trazabilidad ts')
                        ->where('ts.id_tramite = ? AND ts.id_etapa = ?', array($etapa->Tramite->id, $etapa->id))
                        ->orderBy('secuencia DESC')
                        ->fetchOne();

                $sec = $traza_tramite->secuencia + 1;
                $sec_linea = $sec;

                $traza = new Trazabilidad();
                $traza->id_etapa = $etapa->id;
                $traza->id_tramite = $etapa->tramite_id;
                $traza->id_tarea = $etapa->Tarea->id;

                if (empty($traza_tramite_actual)) {
                    $traza_misma_tarea = Doctrine_Query::create()
                            ->from('Trazabilidad ts')
                            ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, 0))
                            ->orderBy('secuencia DESC')
                            ->fetchOne();

                    $traza->num_paso = (!$traza_misma_tarea ? $traza_tramite->num_paso + 1 : $traza_misma_tarea->num_paso);
                    $num_paso = $traza_tramite->num_paso + 1;
                    $num_paso_linea = (!$traza_misma_tarea ? $num_paso : $traza_misma_tarea->num_paso);
                    $traza->secuencia = $sec;
                } else {
                    $traza->num_paso = (!$paso_existe ? $traza_tramite->num_paso + 1 : $traza_tramite->num_paso);
                    $num_paso = (!$paso_existe ? $traza_tramite->num_paso + 1 : $paso_existe);
                    $num_paso_linea = (!$paso_existe ? $num_paso : $paso_existe);
                    $traza->secuencia = $traza_tramite->secuencia + 1;
                }

                $traza->estado = $estado_linea;
                $traza->num_paso_real = 0;
                $traza->save();
            }

            foreach ($formulario->Proceso->Tareas as $tarea) {
                $cantidad_total_pasos = $cantidad_total_pasos + count($tarea->Pasos);
            }

            $args = array(
                'tramite_id' => (string) $etapa->tramite_id,
                'secuencia' => (string) $sec_linea,
                'paso' => (string) $num_paso_linea,
                'organismo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->organismo_id,
                'oficina_id' => (string) $oficina_id,
                'proceso_externo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
                'pasos_ejecutables' => (string) $cantidad_total_pasos,
                'nombre_tarea' => (string) "Inicio de " . $etapa->Tarea->nombre,
                'estado' => 'EN_EJECUCION', //EN EJECUCION
                'etapa' => (string) $etapa->id,
                'tarea' => (string) $etapa->Tarea->id,
                'canal_inicio' => (string) $canal_inicio,
                'role' => (string) "SOLICITANTE",
                'oid' => (string) $oid,
                'nombre_proceso' => (string) $etapa->Tarea->Proceso->nombre,
                'inicio_asistido' => (string) $inicioAsistido,
                'etiqueta' => (string) $etapa->Tarea->etiqueta_traza,
                'visibilidad' => (string) $etapa->Tarea->visible_traza,
                'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
                'tipoRegistroTrazabilidad' => "COMUN",
                'id_transaccion' => (string) $id_transaccion);


            // -- Encola la operacion para enviar la linea (y en caso de que no se haya generado el guid el cabezal tambien)
            $CI->load->library('resque/resque');
            Resque::enqueue('default', 'TrazaLinea', $args);
        }
    }
}

/*
  Envia una linea en caso de que ocurra un cierre automatico, utilizado por ejemplo en el controlador etapas ejecutar() cuando se da
  un cierre automatico en el inicio de una tarea final o no tiene paso de confirmacion.
 */

function enviar_traza_cierre_automatico($etapa, $secuencia) {

    $CI = &get_instance();
    sendInvolucrado($etapa);
    $paso = $etapa->getPasoEjecutable($secuencia);
    $CI->load->helper('trazabilidad_id_helper');
    $datos = trazabilidad_id();
    $canal_inicio = $datos->canal_inicio;
    $inicioAsistido = $datos->inicioAsistido;
    $oid = $datos->oid;
    if (($etapa->Tarea->trazabilidad && $paso->enviar_traza == 1)) {
        $formulario = $paso->Formulario;
        $tarea_inicial = $formulario->Proceso->getTareaInicial();

        $num_paso = $secuencia;
        $num_paso_linea = $secuencia + 1;

        if ((sizeof($etapa->getPasosEjecutables()) - 1) == $secuencia && !$tarea_inicial) {
            $estado = 'FINALIZADO';
            $estado_linea = 'F';
        } else {
            if (($tarea_inicial->id == $etapa->Tarea->id) && ($secuencia == 0)) {
                $estado = 'INICIO';
            } else {
                $estado = 'EN_EJECUCION';
            }

            $estado_linea = 'I';
        }

        try {
            $traza_existente = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_etapa = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->id, $paso->orden))
                    ->limit(1)
                    ->fetchOne();

            $paso_existe = null;
            if (!empty($traza_existente)) {
                $paso_existe = $traza_existente->num_paso_real;
            }

            $traza_tramite = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ?', array($etapa->Tramite->id))
                    ->orderBy('secuencia DESC')
                    ->limit(1)
                    ->fetchOne();

            if (empty($traza_tramite)) {
                $traza_cabezal = Doctrine_Query::create()
                        ->from('Trazabilidad ts')
                        ->where('ts.id_tramite = ? AND ts.estado = ?', array($etapa->Tramite->id, 'C'))
                        ->orderBy('secuencia ASC')
                        ->fetchOne();

                $sec = 0;
                $sec_linea = $sec + 1;

                if (empty($traza_cabezal)) {
                    $traza = new Trazabilidad();
                    $traza->id_etapa = $etapa->id;
                    $traza->id_tramite = $etapa->tramite_id;
                    $traza->id_tarea = $etapa->Tarea->id;
                    $traza->num_paso = 0;
                    $traza->secuencia = $sec;
                    $traza->estado = 'C';
                    $traza->save();
                }

                $traza = new Trazabilidad();
                $traza->id_etapa = $etapa->id;
                $traza->id_tramite = $etapa->tramite_id;
                $traza->id_tarea = $etapa->Tarea->id;
                $traza->num_paso = $num_paso + 1;
                $traza->secuencia = $sec + 1;
                $traza->estado = $estado_linea;
                $traza->num_paso_real = $paso->orden;
                $traza->save();
            } else {
                $traza_cabezal = Doctrine_Query::create()
                        ->from('Trazabilidad ts')
                        ->where('ts.id_tramite = ? AND ts.estado = ?', array($etapa->Tramite->id, 'C'))
                        ->orderBy('secuencia ASC')
                        ->fetchOne();

                $traza_tramite_actual = Doctrine_Query::create()
                        ->from('Trazabilidad ts')
                        ->where('ts.id_tramite = ? AND ts.id_etapa = ?', array($etapa->Tramite->id, $etapa->id))
                        ->orderBy('secuencia DESC')
                        ->fetchOne();

                $sec = $traza_tramite->secuencia + 1;
                $sec_linea = $sec;

                $traza = new Trazabilidad();
                $traza->id_etapa = $etapa->id;
                $traza->id_tramite = $etapa->tramite_id;
                $traza->id_tarea = $etapa->Tarea->id;

                if (empty($traza_tramite_actual)) {
                    $traza_misma_tarea = Doctrine_Query::create()
                            ->from('Trazabilidad ts')
                            ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso->orden))
                            ->orderBy('secuencia DESC')
                            ->fetchOne();

                    $traza->num_paso = (!$traza_misma_tarea ? $traza_tramite->num_paso + 1 : $traza_misma_tarea->num_paso);
                    $num_paso = $traza_tramite->num_paso + 1;
                    $num_paso_linea = (!$traza_misma_tarea ? $num_paso : $traza_misma_tarea->num_paso);
                    $traza->secuencia = $sec;
                } else {
                    $traza->num_paso = (!$paso_existe ? $traza_tramite->num_paso + 1 : $traza_tramite->num_paso);
                    $num_paso = (!$paso_existe ? $traza_tramite->num_paso + 1 : $paso_existe);
                    $num_paso_linea = (!$paso_existe ? $num_paso : $paso_existe);
                    $traza->secuencia = $traza_tramite->secuencia + 1;
                }

                $traza->estado = $estado_linea;
                $traza->num_paso_real = $paso->orden;
                $traza->save();
            }

            $cantidad_total_pasos = 0;
            foreach ($formulario->Proceso->Tareas as $tarea) {
                $cantidad_total_pasos = $cantidad_total_pasos + count($tarea->Pasos);
            }

            (empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = '' : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);


            $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("id_transaccion_traza", $etapa->id);
            $id_transaccion = $id_transaccion->valor;


            $estado_fin_tarea = "EN_EJECUCION";
            $descripcion_fin_tarea = 'Fin de: ' . $etapa->Tarea->nombre;
            $secuencia_fin_tarea = $sec_linea + 1;
            $num_paso_linea_fin_tarea = $num_paso_linea + 1;

            //envio traza de linea
            $args = array('tramite_id' => (string) $etapa->tramite_id,                
                'secuencia' => (string) ($secuencia_fin_tarea),
                'paso' => (string) ($num_paso_linea_fin_tarea),
                'organismo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->organismo_id,
                'oficina_id' => (string) $oficina_id,
                'proceso_externo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
                'pasos_ejecutables' => (string) $cantidad_total_pasos,
                'estado' => (string) $estado_fin_tarea,
                'etapa' => (string) $etapa->id,
                'tarea' => (string) $etapa->Tarea->id,
                'canal_inicio' => (string) $canal_inicio,
                'nombre_tarea' => (string) $descripcion_fin_tarea,
                'role' => (string) "SOLICITANTE",
                'oid' => (string) $oid,
                'nombre_proceso' => (string) $etapa->Tarea->Proceso->nombre,
                'inicio_asistido' => (string) $inicioAsistido,
                'etiqueta' => (string) $etapa->Tarea->etiqueta_traza,
                'visibilidad' => (string) $etapa->Tarea->visible_traza,
                'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
                'tipoRegistroTrazabilidad' => "COMUN",
                'id_transaccion' => (string) $id_transaccion);

            // -- Encola la operacion
            $CI->load->library('resque/resque');
            Resque::enqueue('default', 'TrazaLinea', $args);

            $traza = new Trazabilidad();
            $traza->id_etapa = $etapa->id;
            $traza->id_tramite = $etapa->tramite_id;
            $traza->id_tarea = $etapa->Tarea->id;
            $traza->num_paso = $num_paso_linea_fin_tarea;
            $traza->secuencia = $secuencia_fin_tarea;
            $traza->estado = 'I';
            $traza->save();

            //si es una tarea final ademas mando la linea de fin de proceso
            if ($etapa->Tarea->final) {
                $estado_fin_proceso = $etapa->Tarea->trazabilidad_estado;
                $descripcion_fin_proceso = 'Fin de: ' . $etapa->Tarea->Proceso->nombre;
                $secuencia_fin_proceso = $sec_linea + 2;
                $num_paso_linea_finproceso = $num_paso_linea + 2;
                $args['estado'] = (string) $estado_fin_proceso;
                $args['nombre_tarea'] = (string) $descripcion_fin_proceso;
                $args['secuencia'] = (string) $secuencia_fin_proceso;
                $args['paso'] = (string) $num_paso_linea_finproceso;

                // -- Encola la operacion
                $CI->load->library('resque/resque');
                Resque::enqueue('default', 'TrazaLinea', $args);

                $traza = new Trazabilidad();
                $traza->id_etapa = $etapa->id;
                $traza->id_tramite = $etapa->tramite_id;
                $traza->id_tarea = $etapa->Tarea->id;
                $traza->num_paso = $num_paso_linea_finproceso;
                $traza->secuencia = $secuencia_fin_proceso;
                $traza->estado = 'I';
                $traza->save();
            }

            $count_envio_traza_paso = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso->orden))
                    ->orderBy('secuencia DESC')
                    ->count();
            // si el paso esta setado para enviar traza y si no se se registro la traza aun mas de una vez
            if ($count_envio_traza_paso == 1 && $paso->enviar_traza == 1) {

                $estado = "EN_EJECUCION"; //por defecto en ejecucion
                $descripcion = $paso->nombre; //por nombre del paso
                //envio traza de linea
                $args = array('tramite_id' => (string) $etapa->tramite_id,
                    'secuencia' => (string) $sec_linea,                    
                    'paso' => (string) $num_paso_linea,
                    'organismo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->organismo_id,
                    'oficina_id' => (string) $oficina_id,
                    'proceso_externo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
                    'pasos_ejecutables' => (string) $cantidad_total_pasos,
                    'nombre_tarea' => (string) $descripcion,
                    'estado' => (string) $estado,
                    'etapa' => (string) $etapa->id,
                    'tarea' => (string) $etapa->Tarea->id,
                    'canal_inicio' => (string) $canal_inicio,
                    'role' => (string) "SOLICITANTE",
                    'oid' => (string) $oid,
                    'nombre_proceso' => (string) $etapa->Tarea->Proceso->nombre,
                    'inicio_asistido' => (string) $inicioAsistido,
                    'etiqueta' => (string) $paso->etiqueta_traza,
                    'visibilidad' => (string) $paso->visible_traza,
                    'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
                    'tipoRegistroTrazabilidad' => "COMUN",
                    'id_transaccion' => (string) $id_transaccion);

                // -- Encola la operacion
                $CI->load->library('resque/resque');
                Resque::enqueue('default', 'TrazaLinea', $args);
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
}

/*
  Envia una traza de una linea (paso), esta funcion NO verifica cierre automatico (se utiliza en la api) para simular el ejcutar_form()
  de la etapa a medida que avanza los pasos.
 */

function enviar_traza_linea_paso($etapa, $secuencia) {

    $CI = &get_instance();
    sendInvolucrado($etapa);
    $paso = $etapa->getPasoEjecutable($secuencia);
    $formulario = $paso->Formulario;
    $CI->load->helper('trazabilidad_id_helper');
    $datos = trazabilidad_id();
    $canal_inicio = $datos->canal_inicio;
    $inicioAsistido = $datos->inicioAsistido;
    $oid = $datos->oid;
    //$prox_paso_traza = $etapa->getPasoEjecutable($secuencia + 1);
    //$proximo_paso_cierre_automatico = ($etapa->Tarea->final ||  !$etapa->Tarea->paso_confirmacion)  && $prox_paso_traza && $prox_paso_traza->getReadonly() && end($etapa->getPasosEjecutables()) == $prox_paso_traza;
    //if(($etapa->Tarea->trazabilidad && $paso->enviar_traza) || $proximo_paso_cierre_automatico) {
    if ($etapa->Tarea->trazabilidad && $paso->enviar_traza == 1) {
        $tarea_inicial = $formulario->Proceso->getTareaInicial();

        $num_paso = $secuencia;
        $num_paso_linea = $secuencia + 1;

        if ((sizeof($etapa->getPasosEjecutables()) - 1) == $secuencia && !$tarea_inicial) {
            $estado = 'FINALIZADO';
            $estado_linea = 'F';
        } else {
            if (($tarea_inicial->id == $etapa->Tarea->id) && ($secuencia == 0)) {
                $estado = 'INICIO';
            } else {
                $estado = 'EN_EJECUCION';
            }

            $estado_linea = 'I';
        }

        try {
            $traza_existente = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_etapa = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->id, $paso->orden))
                    ->limit(1)
                    ->fetchOne();

            $paso_existe = null;
            if (!empty($traza_existente)) {
                $paso_existe = $traza_existente->num_paso_real;
            }

            $traza_tramite = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ?', array($etapa->Tramite->id))
                    ->orderBy('secuencia DESC')
                    ->limit(1)
                    ->fetchOne();

            if (empty($traza_tramite)) {
                $traza_cabezal = Doctrine_Query::create()
                        ->from('Trazabilidad ts')
                        ->where('ts.id_tramite = ? AND ts.estado = ?', array($etapa->Tramite->id, 'C'))
                        ->orderBy('secuencia ASC')
                        ->fetchOne();

                $sec = 0;
                $sec_linea = $sec + 1;

                if (empty($traza_cabezal)) {
                    $traza = new Trazabilidad();
                    $traza->id_etapa = $etapa->id;
                    $traza->id_tramite = $etapa->tramite_id;
                    $traza->id_tarea = $etapa->Tarea->id;
                    $traza->num_paso = 0;
                    $traza->secuencia = $sec;
                    $traza->estado = 'C';
                    $traza->save();
                }

                $traza = new Trazabilidad();
                $traza->id_etapa = $etapa->id;
                $traza->id_tramite = $etapa->tramite_id;
                $traza->id_tarea = $etapa->Tarea->id;
                $traza->num_paso = $num_paso + 1;
                $traza->secuencia = $sec + 1;
                $traza->estado = $estado_linea;
                $traza->num_paso_real = $paso->orden;
                $traza->save();
            } else {
                $traza_cabezal = Doctrine_Query::create()
                        ->from('Trazabilidad ts')
                        ->where('ts.id_tramite = ? AND ts.estado = ?', array($etapa->Tramite->id, 'C'))
                        ->orderBy('secuencia ASC')
                        ->fetchOne();

                $traza_tramite_actual = Doctrine_Query::create()
                        ->from('Trazabilidad ts')
                        ->where('ts.id_tramite = ? AND ts.id_etapa = ?', array($etapa->Tramite->id, $etapa->id))
                        ->orderBy('secuencia DESC')
                        ->fetchOne();

                $sec = $traza_tramite->secuencia + 1;
                $sec_linea = $sec;

                $traza = new Trazabilidad();
                $traza->id_etapa = $etapa->id;
                $traza->id_tramite = $etapa->tramite_id;
                $traza->id_tarea = $etapa->Tarea->id;

                if (empty($traza_tramite_actual)) {
                    $traza_misma_tarea = Doctrine_Query::create()
                            ->from('Trazabilidad ts')
                            ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso->orden))
                            ->orderBy('secuencia DESC')
                            ->fetchOne();

                    $traza->num_paso = (!$traza_misma_tarea ? $traza_tramite->num_paso + 1 : $traza_misma_tarea->num_paso);
                    $num_paso = $traza_tramite->num_paso + 1;
                    $num_paso_linea = (!$traza_misma_tarea ? $num_paso : $traza_misma_tarea->num_paso);
                    $traza->secuencia = $sec;
                } else {
                    $traza->num_paso = (!$paso_existe ? $traza_tramite->num_paso + 1 : $traza_tramite->num_paso);
                    $num_paso = (!$paso_existe ? $traza_tramite->num_paso + 1 : $paso_existe);
                    $num_paso_linea = (!$paso_existe ? $num_paso : $paso_existe);
                    $traza->secuencia = $traza_tramite->secuencia + 1;
                }

                $traza->estado = $estado_linea;
                $traza->num_paso_real = $paso->orden;
                $traza->save();
            }

            $cantidad_total_pasos = 0;
            foreach ($formulario->Proceso->Tareas as $tarea) {
                $cantidad_total_pasos = $cantidad_total_pasos + count($tarea->Pasos);
            }

            (empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = '' : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);


            $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("id_transaccion_traza", $etapa->id);
            $id_transaccion = $id_transaccion->valor;

            $count_envio_traza_paso = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso->orden))
                    ->orderBy('secuencia DESC')
                    ->count();
            // si el paso esta setado para enviar traza y si no se se registro la traza aun mas de una vez
            if ($count_envio_traza_paso == 1 && $paso->enviar_traza == 1) {

                $estado = "EN_EJECUCION"; //por defecto en ejeucion
                $descripcion = $paso->nombre; //por nombre del paso
                //envio traza de linea
                $args = array('tramite_id' => (string) $etapa->tramite_id,
                    'secuencia' => (string) $sec_linea,
                    'paso' => (string) $num_paso_linea,
                    'organismo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->organismo_id,
                    'oficina_id' => (string) $oficina_id,
                    'proceso_externo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
                    'pasos_ejecutables' => (string) $cantidad_total_pasos,
                    'nombre_tarea' => (string) $descripcion,
                    'estado' => (string) $estado,
                    'etapa' => (string) $etapa->id,
                    'tarea' => (string) $etapa->Tarea->id,
                    'canal_inicio' => (string) $canal_inicio,
                    'role' => (string) "SOLICITANTE",
                    'oid' => (string) $oid,
                    'tipoRegistroTrazabilidad' => "COMUN",
                    'nombre_proceso' => (string) $etapa->Tarea->Proceso->nombre,
                    'inicio_asistido' => (string) $inicioAsistido,
                    'etiqueta' => (string) $paso->etiqueta_traza,
                    'visibilidad' => (string) $paso->visible_traza,
                    'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,                    
                    'id_transaccion' => (string) $id_transaccion);

                // -- Encola la operacion
                $CI = & get_instance();
                $CI->load->library('resque/resque');
                Resque::enqueue('default', 'TrazaLinea', $args);
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
}

/*
  Envia una traza de una linea (paso), esta funcion SI verifica cierre automatico se utiliza por ejemplo ejcutar_form()
  del controlador de etapas a medida que avanza los pasos.
 */

function enviar_traza_linea_paso_con_cierre_automatico($etapa, $secuencia) {

    $CI = &get_instance();
    sendInvolucrado($etapa);
    $paso = $etapa->getPasoEjecutable($secuencia);
    $formulario = $paso->Formulario;
    $prox_paso_traza = $etapa->getPasoEjecutable($secuencia + 1);
    $proximo_paso_cierre_automatico = ($etapa->Tarea->final || !$etapa->Tarea->paso_confirmacion) && $prox_paso_traza && $prox_paso_traza->getReadonly() && end($etapa->getPasosEjecutables()) == $prox_paso_traza;

    $CI->load->helper('trazabilidad_id_helper');
    $datos = trazabilidad_id();
    $canal_inicio = $datos->canal_inicio;
    $inicioAsistido = $datos->inicioAsistido;
    $oid = $datos->oid;
    if (($etapa->Tarea->trazabilidad && $paso->enviar_traza) || $proximo_paso_cierre_automatico) {
        $tarea_inicial = $formulario->Proceso->getTareaInicial();

        $num_paso = $secuencia;
        $num_paso_linea = $secuencia + 1;

        if ((sizeof($etapa->getPasosEjecutables()) - 1) == $secuencia && !$tarea_inicial) {
            $estado = 'FINALIZADO';
            $estado_linea = 'F';
        } else {
            if (($tarea_inicial->id == $etapa->Tarea->id) && ($secuencia == 0)) {
                $estado = 'INICIO';
            } else {
                $estado = 'EN_EJECUCION';
            }

            $estado_linea = 'I';
        }

        try {
            $traza_existente = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_etapa = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->id, $paso->orden))
                    ->limit(1)
                    ->fetchOne();

            $paso_existe = null;
            if (!empty($traza_existente)) {
                $paso_existe = $traza_existente->num_paso_real;
            }

            $traza_tramite = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ?', array($etapa->Tramite->id))
                    ->orderBy('secuencia DESC')
                    ->limit(1)
                    ->fetchOne();

            if (empty($traza_tramite)) {
                $traza_cabezal = Doctrine_Query::create()
                        ->from('Trazabilidad ts')
                        ->where('ts.id_tramite = ? AND ts.estado = ?', array($etapa->Tramite->id, 'C'))
                        ->orderBy('secuencia ASC')
                        ->fetchOne();

                $sec = 0;
                $sec_linea = $sec + 1;

                if (empty($traza_cabezal)) {
                    $traza = new Trazabilidad();
                    $traza->id_etapa = $etapa->id;
                    $traza->id_tramite = $etapa->tramite_id;
                    $traza->id_tarea = $etapa->Tarea->id;
                    $traza->num_paso = 0;
                    $traza->secuencia = $sec;
                    $traza->estado = 'C';
                    $traza->save();
                }

                $traza = new Trazabilidad();
                $traza->id_etapa = $etapa->id;
                $traza->id_tramite = $etapa->tramite_id;
                $traza->id_tarea = $etapa->Tarea->id;
                $traza->num_paso = $num_paso + 1;
                $traza->secuencia = $sec + 1;
                $traza->estado = $estado_linea;
                $traza->num_paso_real = $paso->orden;
                $traza->save();
            } else {
                $traza_cabezal = Doctrine_Query::create()
                        ->from('Trazabilidad ts')
                        ->where('ts.id_tramite = ? AND ts.estado = ?', array($etapa->Tramite->id, 'C'))
                        ->orderBy('secuencia ASC')
                        ->fetchOne();

                $traza_tramite_actual = Doctrine_Query::create()
                        ->from('Trazabilidad ts')
                        ->where('ts.id_tramite = ? AND ts.id_etapa = ?', array($etapa->Tramite->id, $etapa->id))
                        ->orderBy('secuencia DESC')
                        ->fetchOne();

                $sec = $traza_tramite->secuencia + 1;
                $sec_linea = $sec;

                $traza = new Trazabilidad();
                $traza->id_etapa = $etapa->id;
                $traza->id_tramite = $etapa->tramite_id;
                $traza->id_tarea = $etapa->Tarea->id;

                if (empty($traza_tramite_actual)) {
                    $traza_misma_tarea = Doctrine_Query::create()
                            ->from('Trazabilidad ts')
                            ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso->orden))
                            ->orderBy('secuencia DESC')
                            ->fetchOne();

                    $traza->num_paso = (!$traza_misma_tarea ? $traza_tramite->num_paso + 1 : $traza_misma_tarea->num_paso);
                    $num_paso = $traza_tramite->num_paso + 1;
                    $num_paso_linea = (!$traza_misma_tarea ? $num_paso : $traza_misma_tarea->num_paso);
                    $traza->secuencia = $sec;
                } else {
                    $traza->num_paso = (!$paso_existe ? $traza_tramite->num_paso + 1 : $traza_tramite->num_paso);
                    $num_paso = (!$paso_existe ? $traza_tramite->num_paso + 1 : $paso_existe);
                    $num_paso_linea = (!$paso_existe ? $num_paso : $paso_existe);
                    $traza->secuencia = $traza_tramite->secuencia + 1;
                }

                $traza->estado = $estado_linea;
                $traza->num_paso_real = $paso->orden;
                $traza->save();
            }


            $cantidad_total_pasos = 0;
            foreach ($formulario->Proceso->Tareas as $tarea) {
                $cantidad_total_pasos = $cantidad_total_pasos + count($tarea->Pasos);
            }

            (empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = '' : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);


            $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("id_transaccion_traza", $etapa->id);
            $id_transaccion = $id_transaccion->valor;

            $prox_paso_traza = $etapa->getPasoEjecutable($secuencia + 1);

            //verifico si el proximo paso no es un cirre automatico, en el caso que sea envio la traza
            if ($proximo_paso_cierre_automatico) {
                $estado_fin_tarea = "EN_EJECUCION";
                $descripcion_fin_tarea = 'Fin de: ' . $etapa->Tarea->nombre;
                $secuencia_fin_tarea = $sec_linea + 1;
                $num_paso_linea_fin_tarea = $num_paso_linea + 1;
                try {
                    //envio traza de linea
                    $args = array('tramite_id' => (string) $etapa->tramite_id,                        
                        'secuencia' => (string) ($secuencia_fin_tarea),
                        'paso' => (string) ($num_paso_linea_fin_tarea),
                        'organismo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->organismo_id,
                        'oficina_id' => (string) $oficina_id,
                        'proceso_externo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
                        'pasos_ejecutables' => (string) $cantidad_total_pasos,
                        'nombre_tarea' => (string) $descripcion_fin_tarea,
                        'estado' => (string) $estado_fin_tarea,
                        'etapa' => (string) $etapa->id,
                        'tarea' => (string) $etapa->Tarea->id,
                        'canal_inicio' => (string) $canal_inicio,
                        'role' => (string) "SOLICITANTE",
                        'oid' => (string) $oid,
                        'nombre_proceso' => (string) $etapa->Tarea->Proceso->nombre,
                        'inicio_asistido' => (string) $inicioAsistido,
                        'etiqueta' => (string) $etapa->Tarea->etiqueta_traza,
                        'visibilidad' => (string) $etapa->Tarea->visible_traza,
                        'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
                        'tipoRegistroTrazabilidad' => "COMUN",
                        'id_transaccion' => (string) $id_transaccion);

                    // -- Encola la operacion
                    $CI->load->library('resque/resque');
                    Resque::enqueue('default', 'TrazaLinea', $args);

                    $traza = new Trazabilidad();
                    $traza->id_etapa = $etapa->id;
                    $traza->id_tramite = $etapa->tramite_id;
                    $traza->id_tarea = $etapa->Tarea->id;
                    $traza->num_paso = $num_paso_linea_fin_tarea;
                    $traza->secuencia = $secuencia_fin_tarea;
                    $traza->estado = 'I';
                    $traza->save();

                    //si es una tarea final ademas mando la linea de fin de proceso
                    if ($etapa->Tarea->final) {
                        $estado_fin_proceso = $etapa->Tarea->trazabilidad_estado;
                        $descripcion_fin_proceso = 'Fin de: ' . $etapa->Tarea->Proceso->nombre;
                        $secuencia_fin_proceso = $sec_linea + 2;
                        $num_paso_linea_finproceso = $num_paso_linea + 2;
                        $args['estado'] = (string) $estado_fin_proceso;
                        $args['nombre_tarea'] = (string) $descripcion_fin_proceso;
                        $args['secuencia'] = (string) $secuencia_fin_proceso;
                        $args['paso'] = (string) $num_paso_linea_finproceso;

                        // -- Encola la operacion
                        $CI = & get_instance();
                        $CI->load->library('resque/resque');
                        Resque::enqueue('default', 'TrazaLinea', $args);

                        $traza = new Trazabilidad();
                        $traza->id_etapa = $etapa->id;
                        $traza->id_tramite = $etapa->tramite_id;
                        $traza->id_tarea = $etapa->Tarea->id;
                        $traza->num_paso = $num_paso_linea_finproceso;
                        $traza->secuencia = $secuencia_fin_proceso;
                        $traza->estado = 'I';
                        $traza->save();
                    }
                } catch (Exception $e) {
                    log_message('error', $e->getMessage());
                }
            }

            $count_envio_traza_paso = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso->orden))
                    ->orderBy('secuencia DESC')
                    ->count();
            // si el paso esta setado para enviar traza y si no se se registro la traza aun mas de una vez
            if ($count_envio_traza_paso == 1 && $paso->enviar_traza == 1) {

                $estado = "EN_EJECUCION"; //por defecto en ejeucion
                $descripcion = $paso->nombre; //por nombre del paso
                //envio traza de linea
                $args = array('tramite_id' => (string) $etapa->tramite_id,
                    'secuencia' => (string) $sec_linea,
                    'paso' => (string) $num_paso_linea,
                    'organismo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->organismo_id,
                    'oficina_id' => (string) $oficina_id,
                    'proceso_externo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
                    'pasos_ejecutables' => (string) $cantidad_total_pasos,
                    'nombre_tarea' => (string) $descripcion,
                    'estado' => (string) $estado,
                    'etapa' => (string) $etapa->id,
                    'tarea' => (string) $etapa->Tarea->id,
                    'canal_inicio' => (string) $canal_inicio,
                    'role' => (string) "SOLICITANTE",
                    'oid' => (string) $oid,
                    'nombre_proceso' => (string) $etapa->Tarea->Proceso->nombre,
                    'inicio_asistido' => (string) $inicioAsistido,
                    'etiqueta' => (string) $paso->etiqueta_traza,
                    'visibilidad' => (string) $paso->visible_traza,
                    'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
                    'tipoRegistroTrazabilidad' => "COMUN",                    
                    'id_transaccion' => (string) $id_transaccion);

                // -- Encola la operacion
                $CI = & get_instance();
                $CI->load->library('resque/resque');
                Resque::enqueue('default', 'TrazaLinea', $args);
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
}

/*
  Envia una traza final de tarea
 */

function enviar_traza_final_tarea($etapa) {

    $CI = &get_instance();
    sendInvolucrado($etapa);
    $tarea_inicial = $etapa->Tarea->Proceso->getTareaInicial();
    $secuencia = sizeof($etapa->getPasosEjecutables()) - 1;
    $num_paso = $secuencia;
    $num_paso_linea = $secuencia + 1;
    $paso = $etapa->getPasoEjecutable($secuencia);
    $CI->load->helper('trazabilidad_id_helper');
    $datos = trazabilidad_id();
    $canal_inicio = $datos->canal_inicio;
    $inicioAsistido = $datos->inicioAsistido;
    $oid = $datos->oid;
    if ((sizeof($etapa->getPasosEjecutables()) - 1) == $secuencia && !$tarea_inicial) {
        $estado = 'FINALIZADO';
        $estado_linea = 'F';
    } else {
        if (($tarea_inicial->id == $etapa->Tarea->id) && ($secuencia == 0)) {
            $estado = 'INICIO';
        } else {
            $estado = 'EN_EJECUCION';
        }

        $estado_linea = 'I';
    }

    $traza_existente = Doctrine_Query::create()
            ->from('Trazabilidad ts')
            ->where('ts.id_tramite = ? AND ts.id_etapa = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->id, $paso->orden))
            ->limit(1)
            ->fetchOne();

    $paso_existe = null;
    if (!empty($traza_existente)) {
        $paso_existe = $traza_existente->num_paso_real;
    }

    $traza_tramite = Doctrine_Query::create()
            ->from('Trazabilidad ts')
            ->where('ts.id_tramite = ?', array($etapa->Tramite->id))
            ->orderBy('secuencia DESC')
            ->limit(1)
            ->fetchOne();

    if (empty($traza_tramite)) {
        $traza_cabezal = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.estado = ?', array($etapa->Tramite->id, 'C'))
                ->orderBy('secuencia ASC')
                ->fetchOne();

        $sec = 0;
        $sec_linea = $sec + 1;

        if (empty($traza_cabezal)) {
            $traza = new Trazabilidad();
            $traza->id_etapa = $etapa->id;
            $traza->id_tramite = $etapa->tramite_id;
            $traza->id_tarea = $etapa->Tarea->id;
            $traza->num_paso = 0;
            $traza->secuencia = $sec;
            $traza->estado = 'C';
            $traza->save();
        }
        $traza = new Trazabilidad();
        $traza->id_etapa = $etapa->id;
        $traza->id_tramite = $etapa->tramite_id;
        $traza->id_tarea = $etapa->Tarea->id;
        $traza->num_paso = $num_paso + 1;
        $traza->secuencia = $sec + 1;
        $traza->estado = $estado_linea;
        $traza->num_paso_real = $paso->orden;
        $traza->save();
    } else {
        $traza_cabezal = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.estado = ?', array($etapa->Tramite->id, 'C'))
                ->orderBy('secuencia ASC')
                ->fetchOne();

        $traza_tramite_actual = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_etapa = ?', array($etapa->Tramite->id, $etapa->id))
                ->orderBy('secuencia DESC')
                ->fetchOne();

        $sec = $traza_tramite->secuencia + 1;
        $sec_linea = $sec;

        $traza = new Trazabilidad();
        $traza->id_etapa = $etapa->id;
        $traza->id_tramite = $etapa->tramite_id;
        $traza->id_tarea = $etapa->Tarea->id;

        if (empty($traza_tramite_actual)) {
            $traza_misma_tarea = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso->orden))
                    ->orderBy('secuencia DESC')
                    ->fetchOne();

            $traza->num_paso = (!$traza_misma_tarea ? $traza_tramite->num_paso + 1 : $traza_misma_tarea->num_paso);
            $num_paso = $traza_tramite->num_paso + 1;
            $num_paso_linea = (!$traza_misma_tarea ? $num_paso : $traza_misma_tarea->num_paso);
            $traza->secuencia = $sec;
        } else {
            $traza->num_paso = (!$paso_existe ? $traza_tramite->num_paso + 1 : $traza_tramite->num_paso);
            $num_paso = (!$paso_existe ? $traza_tramite->num_paso + 1 : $paso_existe);
            $num_paso_linea = (!$paso_existe ? $num_paso : $paso_existe);
            $traza->secuencia = $traza_tramite->secuencia + 1;
        }

        $traza->estado = $estado_linea;
        $traza->num_paso_real = $paso->orden;
        $traza->save();
    }

    $cantidad_total_pasos = 0;
    foreach ($etapa->Tarea->Proceso->Tareas as $tarea) {
        $cantidad_total_pasos = $cantidad_total_pasos + count($tarea->Pasos);
    }

    (empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = '' : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);

    $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("id_transaccion_traza", $etapa->id);
    $id_transaccion = $id_transaccion->valor;

    try {
        $tareas_proximas = $etapa->getTareasProximas();

        $descripcion = 'Fin de: ' . $etapa->Tarea->nombre;
        $estado = "EN_EJECUCION";
        //envio traza de linea
        $args = array('tramite_id' => (string) $etapa->tramite_id,
            'secuencia' => (string) $sec_linea,
            'paso' => (string) $num_paso_linea,
            'organismo_id' => (string) $etapa->Tarea->Proceso->ProcesoTrazabilidad->organismo_id,
            'oficina_id' => (string) $etapa->Tarea->trazabilidad_id_oficina,
            'proceso_externo_id' => (string) $etapa->Tarea->Proceso->ProcesoTrazabilidad->proceso_externo_id,
            'pasos_ejecutables' => (string) count($etapa->Tarea->Pasos),
            'nombre_tarea' => (string) $descripcion,
            'estado' => (string) $estado,
            'etapa' => (string) $etapa->id,
            'tarea' => (string) $etapa->Tarea->id,
            'canal_inicio' => (string) $canal_inicio,
            'role' => (string) "SOLICITANTE",
            'oid' => (string) $oid,
            'nombre_proceso' => (string) $etapa->Tarea->Proceso->nombre,
            'inicio_asistido' => (string) $inicioAsistido,
            'etiqueta' => (string) $etapa->Tarea->etiqueta_traza,
            'visibilidad' => (string) $etapa->Tarea->visible_traza,
            'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
            'tipoRegistroTrazabilidad' => "COMUN",
            'pasos_ejecutables' => (string) $cantidad_total_pasos,
            'canal_inicio' => (string) $canal_inicio,            
            'id_transaccion' => (string) $id_transaccion);

        // -- Encola la operacion
        $CI->load->library('resque/resque');
        Resque::enqueue('default', 'TrazaLinea', $args);
        enviar_traza_cabezal($etapa);
    } catch (Exception $e) {
        log_message('error', $e->getMessage());
    }
}

/*
  Envia una traza final del proceso, se verifica si el fin del proceso se dio por un una conexion
  por evaluacion o paralela por evaluacion para obtener el estado de trazabilidad. Se utiliza por ejemplo en el ejecutar_fin_form() del controlador de etapas
 */

function enviar_traza_final_proceso($etapa) {

    $tareas_proximas = $etapa->getTareasProximas();

    if (!$etapa->Tarea->final && !(!$etapa->Tarea->final && ($tareas_proximas->estado == 'completado' || $tareas_proximas->estado == 'standby' || $tareas_proximas->estado == 'sincontinuacion'))) {
        return;
    }

    $CI = &get_instance();
    sendInvolucrado($etapa);
    $tarea_inicial = $etapa->Tarea->Proceso->getTareaInicial();
    $secuencia = sizeof($etapa->getPasosEjecutables()) - 1;
    $paso = $etapa->getPasoEjecutable($secuencia);
    $num_paso = $secuencia;
    $num_paso_linea = $secuencia + 1;
    $CI->load->helper('trazabilidad_id_helper');
    $datos = trazabilidad_id();
    $canal_inicio = $datos->canal_inicio;
    $inicioAsistido = $datos->inicioAsistido;
    $oid = $datos->oid;
    if ((sizeof($etapa->getPasosEjecutables()) - 1) == $secuencia && !$tarea_inicial) {
        $estado = 'FINALIZADO';
        $estado_linea = 'F';
    } else {
        if (($tarea_inicial->id == $etapa->Tarea->id) && ($secuencia == 0)) {
            $estado = 'INICIO';
        } else {
            $estado = 'EN_EJECUCION';
        }

        $estado_linea = 'I';
    }

    $traza_existente = Doctrine_Query::create()
            ->from('Trazabilidad ts')
            ->where('ts.id_tramite = ? AND ts.id_etapa = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->id, $paso->orden))
            ->limit(1)
            ->fetchOne();

    $paso_existe = null;
    if (!empty($traza_existente)) {
        $paso_existe = $traza_existente->num_paso_real;
    }

    $traza_tramite = Doctrine_Query::create()
            ->from('Trazabilidad ts')
            ->where('ts.id_tramite = ?', array($etapa->Tramite->id))
            ->orderBy('secuencia DESC')
            ->limit(1)
            ->fetchOne();

    if (empty($traza_tramite)) {
        $traza_cabezal = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.estado = ?', array($etapa->Tramite->id, 'C'))
                ->orderBy('secuencia ASC')
                ->fetchOne();

        $sec = 0;
        $sec_linea = $sec + 1;

        if (empty($traza_cabezal)) {
            $traza = new Trazabilidad();
            $traza->id_etapa = $etapa->id;
            $traza->id_tramite = $etapa->tramite_id;
            $traza->id_tarea = $etapa->Tarea->id;
            $traza->num_paso = 0;
            $traza->secuencia = $sec;
            $traza->estado = 'C';
            $traza->save();
        }
        $traza = new Trazabilidad();
        $traza->id_etapa = $etapa->id;
        $traza->id_tramite = $etapa->tramite_id;
        $traza->id_tarea = $etapa->Tarea->id;
        $traza->num_paso = $num_paso + 1;
        $traza->secuencia = $sec + 1;
        $traza->estado = $estado_linea;
        $traza->num_paso_real = $paso->orden;
        $traza->save();
    } else {
        $traza_cabezal = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.estado = ?', array($etapa->Tramite->id, 'C'))
                ->orderBy('secuencia ASC')
                ->fetchOne();

        $traza_tramite_actual = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_etapa = ?', array($etapa->Tramite->id, $etapa->id))
                ->orderBy('secuencia DESC')
                ->fetchOne();

        $sec = $traza_tramite->secuencia + 1;
        $sec_linea = $sec;

        $traza = new Trazabilidad();
        $traza->id_etapa = $etapa->id;
        $traza->id_tramite = $etapa->tramite_id;
        $traza->id_tarea = $etapa->Tarea->id;

        if (empty($traza_tramite_actual)) {
            $traza_misma_tarea = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso->orden))
                    ->orderBy('secuencia DESC')
                    ->fetchOne();

            $traza->num_paso = (!$traza_misma_tarea ? $traza_tramite->num_paso + 1 : $traza_misma_tarea->num_paso);
            $num_paso = $traza_tramite->num_paso + 1;
            $num_paso_linea = (!$traza_misma_tarea ? $num_paso : $traza_misma_tarea->num_paso);
            $traza->secuencia = $sec;
        } else {
            $traza->num_paso = (!$paso_existe ? $traza_tramite->num_paso + 1 : $traza_tramite->num_paso);
            $num_paso = (!$paso_existe ? $traza_tramite->num_paso + 1 : $paso_existe);
            $num_paso_linea = (!$paso_existe ? $num_paso : $paso_existe);
            $traza->secuencia = $traza_tramite->secuencia + 1;
        }

        $traza->estado = $estado_linea;
        $traza->num_paso_real = $paso->orden;
        $traza->save();
    }


    $cantidad_total_pasos = 0;
    foreach ($etapa->Tarea->Proceso->Tareas as $tarea) {
        $cantidad_total_pasos = $cantidad_total_pasos + count($tarea->Pasos);
    }

    (empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = '' : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);

    $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("id_transaccion_traza", $etapa->id);
    $id_transaccion = $id_transaccion->valor;

    try {

        if ($etapa->Tarea->final) { //fin tramite por ser tarea final
            $descripcion = 'Fin de: ' . $etapa->Tarea->Proceso->nombre;

            //obtengo estado del combo  de la tarea
            $estado = $etapa->Tarea->trazabilidad_estado;
        } elseif (!$etapa->Tarea->final && ($tareas_proximas->estado == 'completado' || $tareas_proximas->estado == 'standby' || $tareas_proximas->estado == 'sincontinuacion')) { //fin tramite por conexion evaluacion
            $descripcion = 'Fin de: ' . $etapa->Tarea->Proceso->nombre;

            $conexiones = $etapa->Tarea->ConexionesOrigen;

            //obtengo estado del combo de la conexion por evaluacion
            foreach ($conexiones as $c) {
                if (($c->tipo == 'evaluacion' || $c->tipo == 'paralelo_evaluacion') && $c->evaluarRegla($etapa->id)) {
                    $estado_traza_conexion = $c->estado_fin_trazabilidad;
                    break;
                }
            }
            $estado = $estado_traza_conexion;
        }

        //envio traza de linea fin del proceso
        $args = array('tramite_id' => (string) $etapa->tramite_id,
            'secuencia' => (string) $sec_linea,
            'paso' => (string) $num_paso_linea,
            'organismo_id' => (string) $etapa->Tarea->Proceso->ProcesoTrazabilidad->organismo_id,
            'oficina_id' => (string) $etapa->Tarea->trazabilidad_id_oficina,
            'proceso_externo_id' => (string) $etapa->Tarea->Proceso->ProcesoTrazabilidad->proceso_externo_id,
            'pasos_ejecutables' => (string) count($etapa->Tarea->Pasos),
            'nombre_tarea' => (string) $descripcion,
            'estado' => (string) $estado,
            'etapa' => (string) $etapa->id,
            'tarea' => (string) $etapa->Tarea->id,
            'canal_inicio' => (string) $canal_inicio,
            'role' => (string) "SOLICITANTE",
            'oid' => (string) $oid,
            'nombre_proceso' => (string) $etapa->Tarea->Proceso->nombre,
            'inicio_asistido' => (string) $inicioAsistido,
            'etiqueta' => (string) "",
            'visibilidad' => (string) "VISIBLE",
            'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
            'tipoRegistroTrazabilidad' => "COMUN",
            'pasos_ejecutables' => (string) $cantidad_total_pasos,
            'canal_inicio' => (string) $canal_inicio,
            'id_transaccion' => (string) $id_transaccion);

        // -- Encola la operacion
        $CI->load->library('resque/resque');
        Resque::enqueue('default', 'TrazaLinea', $args);
    } catch (Exception $e) {
        log_message('error', $e->getMessage());
    }
}

/*
  Envia una traza para los eventos
 */

function enviar_traza_linea_evento($etapa, $secuencia, $evento) {

    if ($etapa->Tarea->trazabilidad && $evento->traza) {
        $CI = &get_instance();
        sendInvolucrado($etapa);
        $paso = $etapa->getPasoEjecutable($secuencia);
        $formulario = $paso->Formulario;
        $tarea_inicial = $formulario->Proceso->getTareaInicial();
        $CI->load->helper('trazabilidad_id_helper');
        $datos = trazabilidad_id();
        $canal_inicio = $datos->canal_inicio;
        $inicioAsistido = $datos->inicioAsistido;
        $oid = $datos->oid;
        $paso_orden = $paso->orden - 1;
        $estado_linea = 'I';
        $paso_existe = null;

        $traza_existente = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_etapa = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->id, $paso_orden))
                ->limit(1)
                ->fetchOne();

        if (!empty($traza_existente)) {
            $paso_existe = $traza_existente->num_paso_real + 2;
        }

        $traza_tramite = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ?', array($etapa->Tramite->id))
                ->orderBy('secuencia DESC')
                ->limit(1)
                ->fetchOne();

        $traza_tramite_actual = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_etapa = ?', array($etapa->Tramite->id, $etapa->id))
                ->orderBy('secuencia DESC')
                ->fetchOne();

        $sec = $traza_tramite->secuencia + 1;
        $sec_linea = $sec;

        $traza = new Trazabilidad();
        $traza->id_etapa = $etapa->id;
        $traza->id_tramite = $etapa->tramite_id;
        $traza->id_tarea = $etapa->Tarea->id;

        if (empty($traza_tramite_actual)) {
            $traza_misma_tarea = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso_orden))
                    ->orderBy('secuencia DESC')
                    ->fetchOne();

            $traza->num_paso = (!$traza_misma_tarea ? $traza_tramite->num_paso + 1 : $traza_misma_tarea->num_paso);
            $num_paso = $traza_tramite->num_paso + 1;
            $num_paso_linea = (!$traza_misma_tarea ? $num_paso : $traza_misma_tarea->num_paso);
            $traza->secuencia = $sec;
        } else {
            $traza->num_paso = $traza_tramite->num_paso + 1;
            $num_paso_linea = $traza_tramite->num_paso + 1;
            $traza->secuencia = $sec;
        }

        $traza->estado = $estado_linea;
        $traza->num_paso_real = $paso_orden;
        $traza->save();


        $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("id_transaccion_traza", $etapa->id);
        $id_transaccion = $id_transaccion->valor;


        $count_envio_traza_paso = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso_orden))
                ->orderBy('secuencia DESC')
                ->count();

        //if($count_envio_traza_paso == 2){
        //datos WS
        $id_transaccion = str_replace(" ", "_", strtoupper($formulario->Proceso->ProcesoTrazabilidad->organismo_id)) . ':' . str_replace(" ", "_", strtoupper($formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id)) . ':' . $etapa->tramite_id;
        (empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = '' : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);
        $nombre_tarea = $etapa->Tarea->nombre;

        $consulta_servicio = $evento->Accion->tipo == 'webservice_extended' || $evento->Accion->tipo == 'pasarela_pago';

        if ($consulta_servicio) {
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('ws_error', $etapa->id);
            $regla = new Regla($evento->variable_error_soap);
            $variable_error = $regla->getExpresionParaOutput($etapa->id);

            if ($dato || $variable_error && $evento->descripcion_error_soap) {
                $regla = new Regla($evento->descripcion_error_soap);
                $descripcion_paso = $regla->getExpresionParaOutput($etapa->id);
            } else if ($evento->descripcion_traza) {
                $regla = new Regla($evento->descripcion_traza);
                $descripcion_paso = $regla->getExpresionParaOutput($etapa->id);
            } else {
                $descripcion_paso = $evento->Accion->nombre;
            }
        } else if ($evento->descripcion_traza) {
            $regla = new Regla($evento->descripcion_traza);
            $descripcion_paso = $regla->getExpresionParaOutput($etapa->id);
        } else {
            $descripcion_paso = $evento->Accion->nombre;
        }

        $args = array(
            'id_transaccion' => $id_transaccion,
            
            'oficina_id' => $oficina_id,
            'secuencia' => $sec_linea,
            'paso' => $num_paso_linea,
            'nombre_tarea' => $descripcion_paso,
            'estado' => 'EN_EJECUCION', //EN EJECUCION,
            'descripcion_paso' => $descripcion_paso,
            'role' => (string) "SOLICITANTE",
            'oid' => (string) $oid,
            'nombre_proceso' => (string) $etapa->Tarea->Proceso->nombre,
            'inicio_asistido' => (string) $inicioAsistido,
            'etiqueta' => (string) $evento->etiqueta_traza,
            'visibilidad' => (string) $evento->visible_traza,
            'etapa' => (string) $etapa->id,
            'tarea' => (string) $etapa->Tarea->id,
            'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
            'organismo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->organismo_id,
            'tramite_id' => (string) $etapa->tramite_id,
            'proceso_externo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
            'tipoRegistroTrazabilidad' => $evento->tipo_registro_traza,
            'pasos_ejecutables' => (string) 0,
            'canal_inicio' => (string) $canal_inicio
        );

        $CI->load->library('resque/resque');
        Resque::enqueue('default', 'TrazaLinea', $args);
        //}
    }
}

/*
  Envia una traza para los eventos para los eventos definidos en el instante despues de ejecutar la tarea
 */

function enviar_traza_linea_evento_despues_tarea($etapa, $secuencia, $evento) {

    if ($etapa->Tarea->trazabilidad && $evento->traza) {
        $CI = &get_instance();
        sendInvolucrado($etapa);
        $paso_orden = -1;
        $estado_linea = 'I';
        $paso_existe = null;
        $CI->load->helper('trazabilidad_id_helper');
        $datos = trazabilidad_id();
        $canal_inicio = $datos->canal_inicio;
        $inicioAsistido = $datos->inicioAsistido;
        $oid = $datos->oid;
        $traza_existente = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_etapa = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->id, $paso_orden))
                ->limit(1)
                ->fetchOne();

        if (!empty($traza_existente)) {
            $paso_existe = $traza_existente->num_paso_real + 2;
        }

        $traza_tramite = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ?', array($etapa->Tramite->id))
                ->orderBy('secuencia DESC')
                ->limit(1)
                ->fetchOne();

        $traza_tramite_actual = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_etapa = ?', array($etapa->Tramite->id, $etapa->id))
                ->orderBy('secuencia DESC')
                ->fetchOne();

        $sec = $traza_tramite->secuencia + 1;
        $sec_linea = $sec;

        $traza = new Trazabilidad();
        $traza->id_etapa = $etapa->id;
        $traza->id_tramite = $etapa->tramite_id;
        $traza->id_tarea = $etapa->Tarea->id;

        if (empty($traza_tramite_actual)) {
            $traza_misma_tarea = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso_orden))
                    ->orderBy('secuencia DESC')
                    ->fetchOne();

            $traza->num_paso = (!$traza_misma_tarea ? $traza_tramite->num_paso + 1 : $traza_misma_tarea->num_paso);
            $num_paso = $traza_tramite->num_paso + 1;
            $num_paso_linea = (!$traza_misma_tarea ? $num_paso : $traza_misma_tarea->num_paso);
            $traza->secuencia = $sec;
        } else {
            $traza->num_paso = $traza_tramite->num_paso + 1;
            $num_paso_linea = $traza_tramite->num_paso + 1;
            $traza->secuencia = $sec;
        }

        $traza->estado = $estado_linea;
        $traza->num_paso_real = $paso_orden;
        $traza->save();


        $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("id_transaccion_traza", $etapa->id);
        $id_transaccion = $id_transaccion->valor;


        $count_envio_traza_paso = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso_orden))
                ->orderBy('secuencia DESC')
                ->count();

        //if($count_envio_traza_paso == 2){
        //datos WS
        $id_transaccion = str_replace(" ", "_", strtoupper($etapa->Tarea->Proceso->ProcesoTrazabilidad->organismo_id)) . ':' . str_replace(" ", "_", strtoupper($etapa->Tarea->Proceso->ProcesoTrazabilidad->proceso_externo_id)) . ':' . $etapa->tramite_id;
        (empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = '' : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);
        $nombre_tarea = $etapa->Tarea->nombre;

        $consulta_servicio = $evento->Accion->tipo == 'webservice_extended' || $evento->Accion->tipo == 'pasarela_pago';

        if ($consulta_servicio) {
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('ws_error', $etapa->id);
            $regla = new Regla($evento->variable_error_soap);
            $variable_error = $regla->getExpresionParaOutput($etapa->id);

            if ($dato || $variable_error && $evento->descripcion_error_soap) {
                $regla = new Regla($evento->descripcion_error_soap);
                $descripcion_paso = $regla->getExpresionParaOutput($etapa->id);
            } else if ($evento->descripcion_traza) {
                $regla = new Regla($evento->descripcion_traza);
                $descripcion_paso = $regla->getExpresionParaOutput($etapa->id);
            } else {
                $descripcion_paso = $evento->Accion->nombre;
            }
        } else if ($evento->descripcion_traza) {
            $regla = new Regla($evento->descripcion_traza);
            $descripcion_paso = $regla->getExpresionParaOutput($etapa->id);
        } else {
            $descripcion_paso = $evento->Accion->nombre;
        }

        $args = array(
            'id_transaccion' => $id_transaccion,
            'oficina_id' => $oficina_id,
            'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
            'secuencia' => $sec_linea,
            'paso' => $num_paso_linea,
            'nombre_tarea' => $descripcion_paso,
            'estado' => 'EN_EJECUCION', //EN EJECUCION,
            'descripcion_paso' => $descripcion_paso,
            'role' => (string) "SOLICITANTE",
            'oid' => (string) $oid,
            'nombre_proceso' => (string) $etapa->Tarea->Proceso->nombre,
            'inicio_asistido' => (string) $inicioAsistido,
            'etiqueta' => (string) $evento->etiqueta_traza,
            'visibilidad' => (string) $evento->visible_traza,
            'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
            'organismo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->organismo_id,
            'tramite_id' => (string) $etapa->tramite_id,
            'proceso_externo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
            'tipoRegistroTrazabilidad' => $evento->tipo_registro_traza,
            'pasos_ejecutables' => (string) 0,
            'canal_inicio' => (string) $canal_inicio,
            'etapa' => (string) $etapa->id,
            'tarea' => (string) $etapa->Tarea->id
        );

        $CI->load->library('resque/resque');
        Resque::enqueue('default', 'TrazaLinea', $args);
        //}
    }
}

/*
  Envia una traza para las acciones de tipo traza
 */

function enviar_traza_linea_accion($etapa, $secuencia, $descripcion, $tipo_registro, $etiqueta_traza, $visible_traza) {

    if ($etapa->Tarea->trazabilidad) {
        $CI = &get_instance();
        sendInvolucrado($etapa);
        $paso = $etapa->getPasoEjecutable($secuencia);
        $formulario = $paso->Formulario;
        $tarea_inicial = $formulario->Proceso->getTareaInicial();
        $CI->load->helper('trazabilidad_id_helper');
        $datos = trazabilidad_id();
        $canal_inicio = $datos->canal_inicio;
        $inicioAsistido = $datos->inicioAsistido;
        $oid = $datos->oid;
        $paso_orden = $paso->orden - 1;
        $estado_linea = 'I';
        $paso_existe = null;

        $traza_existente = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_etapa = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->id, $paso_orden))
                ->limit(1)
                ->fetchOne();

        if (!empty($traza_existente)) {
            $paso_existe = $traza_existente->num_paso_real + 2;
        }

        $traza_tramite = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ?', array($etapa->Tramite->id))
                ->orderBy('secuencia DESC')
                ->limit(1)
                ->fetchOne();

        $traza_tramite_actual = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_etapa = ?', array($etapa->Tramite->id, $etapa->id))
                ->orderBy('secuencia DESC')
                ->fetchOne();

        $sec = $traza_tramite->secuencia + 1;
        $sec_linea = $sec;

        $traza = new Trazabilidad();
        $traza->id_etapa = $etapa->id;
        $traza->id_tramite = $etapa->tramite_id;
        $traza->id_tarea = $etapa->Tarea->id;

        if (empty($traza_tramite_actual)) {
            $traza_misma_tarea = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso_orden))
                    ->orderBy('secuencia DESC')
                    ->fetchOne();

            $traza->num_paso = (!$traza_misma_tarea ? $traza_tramite->num_paso + 1 : $traza_misma_tarea->num_paso);
            $num_paso = $traza_tramite->num_paso + 1;
            $num_paso_linea = (!$traza_misma_tarea ? $num_paso : $traza_misma_tarea->num_paso);
            $traza->secuencia = $sec;
        } else {
            $traza->num_paso = $traza_tramite->num_paso + 1;
            $num_paso_linea = $traza_tramite->num_paso + 1;
            $traza->secuencia = $sec;
        }

        $traza->estado = $estado_linea;
        $traza->num_paso_real = $paso_orden;
        $traza->save();


        $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("id_transaccion_traza", $etapa->id);
        $id_transaccion = $id_transaccion->valor;


        $count_envio_traza_paso = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso_orden))
                ->orderBy('secuencia DESC')
                ->count();

        //if($count_envio_traza_paso == 2){
        //datos WS
        $id_transaccion = str_replace(" ", "_", strtoupper($formulario->Proceso->ProcesoTrazabilidad->organismo_id)) . ':' . str_replace(" ", "_", strtoupper($formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id)) . ':' . $etapa->tramite_id;
        (empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = '' : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);
        $nombre_tarea = $etapa->Tarea->nombre;

        $args = array(
            'id_transaccion' => $id_transaccion,
            'oficina_id' => $oficina_id,
            'secuencia' => $sec_linea,
            'paso' => $num_paso_linea,
            'nombre_tarea' => $descripcion,
            'estado' => 'EN_EJECUCION', //EN EJECUCION,
            'descripcion_paso' => $descripcion,
            'role' => (string) "SOLICITANTE",
            'oid' => (string) $oid,
            'nombre_proceso' => (string) $etapa->Tarea->Proceso->nombre,
            'inicio_asistido' => (string) $inicioAsistido,
            'etiqueta' => (string) $etiqueta_traza,
            'visibilidad' => (string) $visible_traza,
            'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
            'organismo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->organismo_id,
            'tramite_id' => (string) $etapa->tramite_id,
            'proceso_externo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
            'tipoRegistroTrazabilidad' => $tipo_registro,
            'etapa' => (string) $etapa->id,
            'tarea' => (string) $etapa->Tarea->id,
            'pasos_ejecutables' => (string) 0,
            'canal_inicio' => (string) $canal_inicio
        );

        $CI->load->library('resque/resque');
        Resque::enqueue('default', 'TrazaLinea', $args);
        //}
    }
}

/*
  Envia una traza para las acciones de tipo traza para los eventos definidos en el instante despues de ejecutar tarea
 */

function enviar_traza_linea_accion_despues_tarea($etapa, $secuencia, $descripcion, $tipo_registro, $etiqueta_traza, $visible_traza) {

    if ($etapa->Tarea->trazabilidad) {
        $CI = &get_instance();
        sendInvolucrado($etapa);
        $paso_orden = -1;
        $estado_linea = 'I';
        $paso_existe = null;
        $CI->load->helper('trazabilidad_id_helper');
        $datos = trazabilidad_id();
        $canal_inicio = $datos->canal_inicio;
        $inicioAsistido = $datos->inicioAsistido;
        $oid = $datos->oid;
        $traza_existente = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_etapa = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->id, $paso_orden))
                ->limit(1)
                ->fetchOne();

        if (!empty($traza_existente)) {
            $paso_existe = $traza_existente->num_paso_real + 2;
        }

        $traza_tramite = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ?', array($etapa->Tramite->id))
                ->orderBy('secuencia DESC')
                ->limit(1)
                ->fetchOne();

        $traza_tramite_actual = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_etapa = ?', array($etapa->Tramite->id, $etapa->id))
                ->orderBy('secuencia DESC')
                ->fetchOne();

        $sec = $traza_tramite->secuencia + 1;
        $sec_linea = $sec;

        $traza = new Trazabilidad();
        $traza->id_etapa = $etapa->id;
        $traza->id_tramite = $etapa->tramite_id;
        $traza->id_tarea = $etapa->Tarea->id;

        if (empty($traza_tramite_actual)) {
            $traza_misma_tarea = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso_orden))
                    ->orderBy('secuencia DESC')
                    ->fetchOne();

            $traza->num_paso = (!$traza_misma_tarea ? $traza_tramite->num_paso + 1 : $traza_misma_tarea->num_paso);
            $num_paso = $traza_tramite->num_paso + 1;
            $num_paso_linea = (!$traza_misma_tarea ? $num_paso : $traza_misma_tarea->num_paso);
            $traza->secuencia = $sec;
        } else {
            $traza->num_paso = $traza_tramite->num_paso + 1;
            $num_paso_linea = $traza_tramite->num_paso + 1;
            $traza->secuencia = $sec;
        }

        $traza->estado = $estado_linea;
        $traza->num_paso_real = $paso_orden;
        $traza->save();


        $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("id_transaccion_traza", $etapa->id);
        $id_transaccion = $id_transaccion->valor;


        $count_envio_traza_paso = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso_orden))
                ->orderBy('secuencia DESC')
                ->count();

        //if($count_envio_traza_paso == 2){
        //datos WS
        $id_transaccion = str_replace(" ", "_", strtoupper($etapa->Tarea->Proceso->ProcesoTrazabilidad->organismo_id)) . ':' . str_replace(" ", "_", strtoupper($etapa->Tarea->Proceso->ProcesoTrazabilidad->proceso_externo_id)) . ':' . $etapa->tramite_id;
        (empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = '' : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);
        $nombre_tarea = $etapa->Tarea->nombre;

        $cantidad_total_pasos = 0;
        foreach ($formulario->Proceso->Tareas as $tarea) {
            $cantidad_total_pasos = $cantidad_total_pasos + count($tarea->Pasos);
        }

        $args = array(
            'id_transaccion' => $id_transaccion,
            'oficina_id' => $oficina_id,
            'secuencia' => $sec_linea,
            'paso' => $num_paso_linea,
            'nombre_tarea' => $nombre_tarea,
            'estado' => 'EN_EJECUCION', //EN EJECUCION,
            'descripcion_paso' => $descripcion,
            'role' => (string) "SOLICITANTE",
            'oid' => (string) $oid,
            'nombre_proceso' => (string) $etapa->Tarea->Proceso->nombre,
            'inicio_asistido' => (string) $inicioAsistido,
            'etiqueta' => (string) $visible_traza,
            'visibilidad' => (string) $visible_traza,
            'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
            'organismo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->organismo_id,
            'tramite_id' => (string) $etapa->tramite_id,
            'proceso_externo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
            'tipoRegistroTrazabilidad' => $tipo_registro,
            'pasos_ejecutables' => (string) $cantidad_total_pasos,
            'canal_inicio' => (string) $canal_inicio,
            'etapa' => (string) $etapa->id,
            'tarea' => (string) $etapa->Tarea->id
        );

        $CI->load->library('resque/resque');
        Resque::enqueue('default', 'TrazaLinea', $args);
        //}
    }
}

/*
  Envia una traza pago
 */

function enviar_traza_linea_pago($etapa, $secuencia, $descripcion_traza, $pago_realizado, $paso_pago = null) {

    $dato_traza = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('trazaPagoRealizado', $etapa->id);

    //si el pago esta realizado no se envia traza
    if ($dato_traza && $pago_realizado) {
        return;
    } else {
        if (!$dato_traza) {
            $dato_traza = new DatoSeguimiento();
            $dato_traza->etapa_id = $etapa->id;
            $dato_traza->nombre = 'trazaPagoRealizado';
            $dato_traza->valor = '1';
            $dato_traza->save();
        }
    }

    if ($etapa->Tarea->trazabilidad) {
        $CI = &get_instance();
        sendInvolucrado($etapa);
        $CI->load->helper('trazabilidad_id_helper');
        $datos = trazabilidad_id();
        $canal_inicio = $datos->canal_inicio;
        $inicioAsistido = $datos->inicioAsistido;
        $oid = $datos->oid;
        if ($paso_pago) {
            $paso = $paso_pago;
        } else {
            $paso = $etapa->getPasoEjecutable($secuencia);
        }

        $formulario = $paso->Formulario;
        $paso_orden = $paso->orden - 1;
        $estado_linea = 'I';
        $paso_existe = null;

        $tarea_inicial = $formulario->Proceso->getTareaInicial();



        $traza_existente = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_etapa = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->id, $paso_orden))
                ->limit(1)
                ->fetchOne();

        if (!empty($traza_existente)) {
            $paso_existe = $traza_existente->num_paso_real + 2;
        }

        $traza_tramite = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ?', array($etapa->Tramite->id))
                ->orderBy('secuencia DESC')
                ->limit(1)
                ->fetchOne();

        $traza_tramite_actual = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_etapa = ?', array($etapa->Tramite->id, $etapa->id))
                ->orderBy('secuencia DESC')
                ->fetchOne();

        $sec = $traza_tramite->secuencia + 1;
        $sec_linea = $sec;

        $traza = new Trazabilidad();
        $traza->id_etapa = $etapa->id;
        $traza->id_tramite = $etapa->tramite_id;
        $traza->id_tarea = $etapa->Tarea->id;

        if (empty($traza_tramite_actual)) {
            $traza_misma_tarea = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso_orden))
                    ->orderBy('secuencia DESC')
                    ->fetchOne();

            $traza->num_paso = (!$traza_misma_tarea ? $traza_tramite->num_paso + 1 : $traza_misma_tarea->num_paso);
            $num_paso = $traza_tramite->num_paso + 1;
            $num_paso_linea = (!$traza_misma_tarea ? $num_paso : $traza_misma_tarea->num_paso);
            $traza->secuencia = $sec;
        } else {
            $traza->num_paso = $traza_tramite->num_paso + 1;
            $num_paso_linea = $traza_tramite->num_paso + 1;
            $traza->secuencia = $sec;
        }

        $traza->estado = $estado_linea;
        $traza->num_paso_real = $paso_orden;
        $traza->save();


        $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("id_transaccion_traza", $etapa->id);
        $id_transaccion = $id_transaccion->valor;


        $count_envio_traza_paso = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso_orden))
                ->orderBy('secuencia DESC')
                ->count();

        //if($count_envio_traza_paso == 2){
        //datos WS
        $id_transaccion = str_replace(" ", "_", strtoupper($formulario->Proceso->ProcesoTrazabilidad->organismo_id)) . ':' . str_replace(" ", "_", strtoupper($formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id)) . ':' . $etapa->tramite_id;
        (empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = '' : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);
        $nombre_tarea = $etapa->Tarea->nombre;

        $cantidad_total_pasos = 0;
        foreach ($formulario->Proceso->Tareas as $tarea) {
            $cantidad_total_pasos = $cantidad_total_pasos + count($tarea->Pasos);
        }

        $args = array(
            'id_transaccion' => $id_transaccion,
            'oficina_id' => $oficina_id,
            'secuencia' => $sec_linea,
            'paso' => $num_paso_linea,
            'nombre_tarea' => $descripcion_traza,
            'estado' => 'EN_EJECUCION', //EN EJECUCION,
            'descripcion_paso' => $descripcion_traza,
            'role' => (string) "SOLICITANTE",
            'oid' => (string) $oid,
            'nombre_proceso' => (string) $etapa->Tarea->Proceso->nombre,
            'inicio_asistido' => (string) $inicioAsistido,
            'etiqueta' => (string) "PAGO",
            'visibilidad' => (string) "VISIBLE",
            'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
            'organismo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->organismo_id,
            'tramite_id' => (string) $etapa->tramite_id,
            'proceso_externo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
            'tipoRegistroTrazabilidad' => 'COMUN',
            'etapa' => (string) $etapa->id,
            'tarea' => (string) $etapa->Tarea->id,
            'pasos_ejecutables' => (string) $cantidad_total_pasos,
            'canal_inicio' => (string) $canal_inicio,
            'cabezal' => 1
        );

        $CI->load->library('resque/resque');
        Resque::enqueue('default', 'TrazaLinea', $args);
        //}
    }
}

function enviar_traza_agenda_externa($etapa, $paso) {

    if (($etapa->Tarea->trazabilidad && $paso->enviar_traza)) {
        $CI = &get_instance();
        sendInvolucrado($etapa);
        $formulario = $paso->Formulario;
        $tarea_inicial = $formulario->Proceso->getTareaInicial();

        $estado_linea = 'I';

        try {
            $traza_existente = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_etapa = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->id, $paso->orden))
                    ->limit(1)
                    ->fetchOne();

            $paso_existe = null;
            if (!empty($traza_existente)) {
                $paso_existe = $traza_existente->num_paso_real;
            }

            $traza_tramite = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ?', array($etapa->Tramite->id))
                    ->orderBy('secuencia DESC')
                    ->limit(1)
                    ->fetchOne();

            $traza_tramite_actual = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_etapa = ?', array($etapa->Tramite->id, $etapa->id))
                    ->orderBy('secuencia DESC')
                    ->fetchOne();

            $sec = $traza_tramite->secuencia + 1;
            $sec_linea = $sec;

            $traza = new Trazabilidad();
            $traza->id_etapa = $etapa->id;
            $traza->id_tramite = $etapa->tramite_id;
            $traza->id_tarea = $etapa->Tarea->id;

            if (empty($traza_tramite_actual)) {
                $traza_misma_tarea = Doctrine_Query::create()
                        ->from('Trazabilidad ts')
                        ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso->orden))
                        ->orderBy('secuencia DESC')
                        ->fetchOne();

                $traza->num_paso = $traza_misma_tarea->num_paso + 1;
                $num_paso_linea = $traza_misma_tarea->num_paso + 1;
                $traza->secuencia = $sec;
            } else {
                $traza->num_paso = $traza_tramite->num_paso + 1;
                $num_paso_linea = $traza_tramite->num_paso + 1;
                $traza->secuencia = $sec;
            }

            $traza->estado = $estado_linea;
            $traza->num_paso_real = $paso->orden;
            $traza->save();

            $canal_inicio = detect_current_device();

            $cantidad_total_pasos = 0;
            foreach ($formulario->Proceso->Tareas as $tarea) {
                $cantidad_total_pasos = $cantidad_total_pasos + count($tarea->Pasos);
            }

            (empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = '' : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);


            $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("id_transaccion_traza", $etapa->id);
            $id_transaccion = $id_transaccion->valor;


            $count_envio_traza_paso = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso->orden))
                    ->orderBy('secuencia DESC')
                    ->count();

            //if ($count_envio_traza_paso == 1){

            $estado = "EN_EJECUCION"; //por defecto en ejeucion
            $descripcion = $paso->nombre; //por nombre del paso
            $CI->load->helper('trazabilidad_id_helper');
            $datos = trazabilidad_id();
            $canal_inicio = $datos->canal_inicio;
            $inicioAsistido = $datos->inicioAsistido;
            $oid = $datos->oid;
            //envio traza de linea
            $args = array('tramite_id' => (string) $etapa->tramite_id,
                'secuencia' => (string) $sec_linea,
                'paso' => (string) $num_paso_linea,
                'organismo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->organismo_id,
                'oficina_id' => (string) $oficina_id,
                'proceso_externo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
                'pasos_ejecutables' => (string) $cantidad_total_pasos,
                'nombre_tarea' => (string) "Agenda externa: " . $descripcion,
                'estado' => (string) $estado,
                'canal_inicio' => (string) $canal_inicio,                
                'etapa' => (string) $etapa->id,
                'tarea' => (string) $etapa->Tarea->id,
                'tipoRegistroTrazabilidad' => "COMUN",
                'inicio_asistido' => (string) $inicioAsistido,
                'nombre_proceso' => (string) "Traza agenda externa",
                'etiqueta' => (string) $etapa->Tarea->etiqueta_traza,
                'visibilidad' => (string) $etapa->Tarea->visible_traza,
                'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
                'role' => (string) "SOLICITANTE",
                'oid' => (string) $oid,
                'id_transaccion' => (string) $id_transaccion);

            // -- Encola la operacion
            $CI = & get_instance();
            $CI->load->library('resque/resque');
            Resque::enqueue('default', 'TrazaLinea', $args);
            //}
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
}

function enviar_traza_sub_proceso_agenda($etapa, $paso) {
    if ($etapa->Tarea->trazabilidad && $paso->enviar_traza) {
        $CI = &get_instance();
        sendInvolucrado($etapa);
        $formulario = $paso->Formulario;
        $tarea_inicial = $formulario->Proceso->getTareaInicial();

        $paso_orden = $paso->orden - 1;
        $estado_linea = 'I';
        $paso_existe = null;

        $traza_existente = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_etapa = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->id, $paso_orden))
                ->limit(1)
                ->fetchOne();

        if (!empty($traza_existente)) {
            $paso_existe = $traza_existente->num_paso_real + 2;
        }

        $traza_tramite = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ?', array($etapa->Tramite->id))
                ->orderBy('secuencia DESC')
                ->limit(1)
                ->fetchOne();

        $traza_tramite_actual = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_etapa = ?', array($etapa->Tramite->id, $etapa->id))
                ->orderBy('secuencia DESC')
                ->fetchOne();

        $sec = $traza_tramite->secuencia + 1;
        $sec_linea = $sec;

        $traza = new Trazabilidad();
        $traza->id_etapa = $etapa->id;
        $traza->id_tramite = $etapa->tramite_id;
        $traza->id_tarea = $etapa->Tarea->id;

        if (empty($traza_tramite_actual)) {
            $traza_misma_tarea = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso_orden))
                    ->orderBy('secuencia DESC')
                    ->fetchOne();

            $traza->num_paso = (!$traza_misma_tarea ? $traza_tramite->num_paso + 1 : $traza_misma_tarea->num_paso);
            $num_paso = $traza_tramite->num_paso + 1;
            $num_paso_linea = (!$traza_misma_tarea ? $num_paso : $traza_misma_tarea->num_paso);
            $traza->secuencia = $sec;
        } else {
            $traza->num_paso = $traza_tramite->num_paso + 1;
            $num_paso_linea = $traza_tramite->num_paso + 1;
            $traza->secuencia = $sec;
        }

        $traza->estado = $estado_linea;
        $traza->num_paso_real = $paso_orden;
        $traza->save();


        $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("id_transaccion_traza", $etapa->id);
        $id_transaccion = $id_transaccion->valor;


        $count_envio_traza_paso = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso_orden))
                ->orderBy('secuencia DESC')
                ->count();

        //if($count_envio_traza_paso == 2){
        //datos WS
        $id_transaccion = str_replace(" ", "_", strtoupper($formulario->Proceso->ProcesoTrazabilidad->organismo_id)) . ':' . str_replace(" ", "_", strtoupper($formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id)) . ':' . $etapa->tramite_id;
        (empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = '' : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);
        $nombre_tarea = $etapa->Tarea->nombre;
        $estado = "EN_EJECUCION"; //En ejecución
        $tipoRegistroTrazabilidad = "SUBPROCESO"; //sub-proceso

        $CI->load->helper('trazabilidad_id_helper');
        $datos = trazabilidad_id();
        $canal_inicio = $datos->canal_inicio;
        $inicioAsistido = $datos->inicioAsistido;
        $oid = $datos->oid;

        $args = array('tramite_id' => (string) $etapa->tramite_id,
            'secuencia' => (string) $sec_linea,
            'paso' => (string) $num_paso_linea,
            'organismo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->organismo_id,
            'oficina_id' => (string) $oficina_id,
            'proceso_externo_id' => (string) $formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
            'pasos_ejecutables' => (string) $cantidad_total_pasos,
            'nombre_tarea' => (string) "Sub-Proceso Agenda externa: " . $nombre_tarea,
            'estado' => (string) $estado,
            'canal_inicio' => (string) $canal_inicio,            
            'etapa' => (string) $etapa->id,
            'tarea' => (string) $etapa->Tarea->id,
            'tipoRegistroTrazabilidad' => $tipoRegistroTrazabilidad,
            'inicio_asistido' => (string) $inicioAsistido,
            'nombre_proceso' => (string) "Traza agenda externa",
            'etiqueta' => (string) $etapa->Tarea->etiqueta_traza,
            'visibilidad' => (string) $etapa->Tarea->visible_traza,
            'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
            'role' => (string) "SOLICITANTE",
            'oid' => (string) $oid,
            'id_transaccion' => (string) $id_transaccion);

        $CI->load->library('resque/resque');
        Resque::enqueue('default', 'TrazaLinea', $args);
        //}
    }
}

function enviar_traza_eliminar_tramite($tramite) {
    $etapa = $tramite->getUltimaEtapa();

    if ($etapa->Tarea->trazabilidad) {
        $CI = &get_instance();
        sendInvolucrado($etapa);
        $traza_tramite = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ?', array($etapa->Tramite->id))
                ->orderBy('secuencia DESC')
                ->limit(1)
                ->fetchOne();

        $traza_tramite_actual = Doctrine_Query::create()
                ->from('Trazabilidad ts')
                ->where('ts.id_tramite = ? AND ts.id_etapa = ?', array($etapa->Tramite->id, $etapa->id))
                ->orderBy('secuencia DESC')
                ->fetchOne();

        $sec = $traza_tramite->secuencia + 1;
        $sec_linea = $sec;

        $traza = new Trazabilidad();
        $traza->id_etapa = $etapa->id;
        $traza->id_tramite = $etapa->tramite_id;
        $traza->id_tarea = $etapa->Tarea->id;
        $traza->estado = 'E';
        $traza->num_paso_real = -1;
        $traza->save();


        $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("id_transaccion_traza", $etapa->id);
        if ($id_transaccion) {
            $id_transaccion = $id_transaccion->valor;
        } else {
            $id_transaccion = str_replace(" ", "_", strtoupper($etapa->Tarea->Proceso->ProcesoTrazabilidad->organismo_id)) . ':' . str_replace(" ", "_", strtoupper($etapa->Tarea->Proceso->ProcesoTrazabilidad->proceso_externo_id)) . ':' . $etapa->tramite_id;
        }
        (empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = '' : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);
        //if($count_envio_traza_paso == 2){
        //datos WS

        $nombre_tarea = $etapa->Tarea->nombre;
        $estado = "CANCELADO";
        $tipoRegistroTrazabilidad = "COMUN"; //sub-proceso

        $CI->load->helper('trazabilidad_id_helper');
        $datos = trazabilidad_id();
        $canal_inicio = $datos->canal_inicio;
        $inicioAsistido = $datos->inicioAsistido;
        $oid = $datos->oid;

        $args = array('tramite_id' => (string) $etapa->tramite_id,
            'secuencia' => (string) $sec_linea,
            'paso' => (string) -1,
            'organismo_id' => (string) $etapa->Tarea->Proceso->ProcesoTrazabilidad->organismo_id,
            'oficina_id' => (string) $oficina_id,
            'proceso_externo_id' => (string) $etapa->Tarea->Proceso->ProcesoTrazabilidad->proceso_externo_id,
            'pasos_ejecutables' => (string) -1,
            'nombre_tarea' => (string) "Eliminar trámite: " . $etapa->Tarea->Proceso->nombre,
            'estado' => (string) $estado,
            'canal_inicio' => (string) $canal_inicio,
            'etapa' => (string) $etapa->id,
            'tarea' => (string) $etapa->Tarea->id,
            'tipoRegistroTrazabilidad' => $tipoRegistroTrazabilidad,
            'inicio_asistido' => (string) $inicioAsistido,
            'nombre_proceso' => (string) "Eliminar trámite",
            'etiqueta' => (string) "ELIMINADO",
            'visibilidad' => (string) "VISIBLE",
            'oficina_nombre' => (string) $etapa->Tarea->trazabilidad_nombre_oficina,
            'role' => (string) "SOLICITANTE",
            'oid' => (string) $oid,
            'id_transaccion' => (string) $id_transaccion);

        $CI->load->library('resque/resque');

        Resque::enqueue('default', 'TrazaLinea', $args);
    }
}

/* * ************************************* Internas del helper ******************************************* */

function trazabilidad_online_cabezal($args) {
    $path = __DIR__;
    $path_array = explode('/', $path);
    $path_array = array_slice($path_array, 0, count($path_array) - 1);
    $path_array = array_slice($path_array, 0, count($path_array) - 1);
    $path = implode('/', $path_array);

    define('TRAZA_PATH', $path);
    
    $etapa_id = $args['etapa'];
    $tarea_id = $args['tarea'];
    $tramite_id = $args['tramite_id'];
    $pasos_ejecutables = $args['pasos_ejecutables'];
    $secuencia = $args['secuencia'];
    $paso = $args['paso'];
    $id_organismo = $args['organismo_id'];
    $id_oficina = $args['oficina_id'];
    $proceso_externo_id = $args['proceso_externo_id'];
    $estado = $args['estado'];
    $canal_inicio = $args['canal_inicio'];
    $inicio_asistido = $args['inicio_asistido'];
    $nombre_oficina = $args['oficina_nombre'];
    $nombre_proceso = $args['nombre_proceso'];
    $id_transaccion = $args['id_transaccion'];
    $oid = $args['oid'];
    $role = $args['role'];
    $etiqueta = $args['etiqueta'];
    $visibilidad = $args['visibilidad'];
    $fechaOrganismo = $args['fechaOrganismo'];

    $soap_body_cabezal = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
        xmlns:ws="http://ws.web.captura.trazabilidad.agesic.gub.uy/api/v2/cabezalService">
   <soapenv:Header/>
   <soapenv:Body>
           <ws:persist>
              <traza>
                <idTraza>
                    <oidOrganismo>' . $id_organismo . '</oidOrganismo>
                    <idProceso>' . $proceso_externo_id . '</idProceso>
                    <idInstancia>' . $tramite_id . '</idInstancia>
                    <tipoProceso>' . WS_AGESIC_TIPO_PROCESO_TRAZABILIDAD . '</tipoProceso>
                </idTraza>';
    if ($id_oficina != '') {
        $soap_body_cabezal .='
                <oidOficina>' . $id_oficina . '</oidOficina>';
    } $soap_body_cabezal .='
                <oficina>' . $nombre_oficina . '</oficina>
                <fechaHoraOrganismo>' . $fechaOrganismo . '</fechaHoraOrganismo>
                <appOrigen>Simple ' . SIMPLE_VERSION . '</appOrigen>';
    if (!$oid) {
        $soap_body_cabezal .='
                <involucrados></involucrados>';
    } else {
        $soap_body_cabezal .='
                <involucrados>
                    <involucrado>
                        <oid>' . $oid . '</oid>
                        <role>' . $role . '</role>
                    </involucrado>
                </involucrados>';
    }
    $soap_body_cabezal .= '
                <datosProceso xsi:type="ws:datosProcesoTramiteDTO">
                    <datosExtra></datosExtra>
                    <canalDeInicio>' . $canal_inicio . '</canalDeInicio>
                    <inicioAsistidoProceso>' . $inicio_asistido . '</inicioAsistidoProceso>
                </datosProceso>
                <cantidadPasosProceso>' . $pasos_ejecutables . '</cantidadPasosProceso>
              </traza>
           </ws:persist>
         </soapenv:Body>
        </soapenv:Envelope>';



    $soap_header_cabezal = array(
        "Content-type: text/xml;charset=\"utf-8\"",
        "Accept: text/xml",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
        "Content-length: " . strlen($soap_body_cabezal)
    );

    $soap_do = curl_init();
    curl_setopt($soap_do, CURLOPT_URL, WS_AGESIC_TRAZABLIDAD_CABEZAL);
    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($soap_do, CURLOPT_TIMEOUT, 10);
    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($soap_do, CURLOPT_POST, true);
    curl_setopt($soap_do, CURLOPT_POSTFIELDS, $soap_body_cabezal);
    curl_setopt($soap_do, CURLOPT_HTTPHEADER, $soap_header_cabezal);
    $soap_response_cabezal = curl_exec($soap_do);
    $curl_errno = curl_errno($soap_do);
    $curl_error = curl_error($soap_do);
    curl_close($soap_do);

    if ($curl_errno > 0) {
        $log = fopen(TRAZA_PATH . '/application/logs/trazabilidad.log', "a+");
        fwrite($log, 'CABEZAL GENERADO: ' . $soap_body_cabezal);
        fwrite($log, PHP_EOL);
        fwrite($log, 'ERROR: ' . $curl_error);
        fwrite($log, PHP_EOL);
        fwrite($log, 'fecha: ' . date('Y-m-d', time()) . 'T' . date('H:i:s', time()));
        fclose($log);

        //throw new Exception('No es posible enviar el cabezal.');
        //en el modo online retorna falso en caso de que no se pueda realizar
        return false;
    }

    // -- Crea variable con cod de traza obtenido
    $xml = new SimpleXMLElement($soap_response_cabezal);
    $cod_traza = $xml->xpath(WS_XPATH_COD_TRAZABILIDAD);
    $cod_estado = $xml->xpath(WS_XPATH_COD_ESTADO);


    /* echo '<pre>'. htmlentities($soap_response_cabezal).'</pre>';
      return; */

    $str_database = file_get_contents(TRAZA_PATH . '/application/config/database.php');

    preg_match("/'hostname'] = '([a-zA-Z0-9._,;-@$+?%&#!=()*]*)'/", $str_database, $hostname);
    preg_match("/'username'] = '([a-zA-Z0-9._,;-@$+?%&#!=()*]*)'/", $str_database, $username);
    preg_match("/'password'] = '([a-zA-Z0-9._,;-@$+?%&#!=()*]*)'/", $str_database, $password);
    preg_match("/'database'] = '([a-zA-Z0-9._,;-@$+?%&#!=()*]*)'/", $str_database, $database);

    $conn = new mysqli($hostname[1], $username[1], $password[1], $database[1]);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (count($cod_estado) > 0) {
        if ($cod_estado[0] == "OK") {
            $guid = '"' . $cod_traza[0] . '"';
            $sql = "insert into dato_seguimiento (etapa_id, nombre, valor) values ('" . $etapa_id . "', '" . WS_VARIABLE_COD_TRAZABILIDAD . "', '" . $guid . "')";

            if (!$conn->query($sql)) {                
                $log = fopen(TRAZA_PATH . '/application/logs/trazabilidad.log', "a+");
                fwrite($log, 'No es posible crear variable de GUID de traza.');
                fwrite($log, 'BODY' . $soap_body_cabezal);
                fwrite($log, PHP_EOL);
                fwrite($log, $sql);
                fwrite($log, PHP_EOL);
                fwrite($log, 'RESPUESTA CABEZAL: ' . $soap_response_cabezal);
                fwrite($log, PHP_EOL);
                fclose($log);
                throw new Exception('No es posible crear variable de GUID de traza.');
                return false;
            }
        } else {
            $log = fopen(TRAZA_PATH . '/application/logs/trazabilidad.log', "a+");
            fwrite($log, 'CABEZAL GENERADO: ' . $soap_body_cabezal);
            fwrite($log, PHP_EOL);
            fwrite($log, 'RESPUESTA CABEZAL: ' . $soap_response_cabezal);
            fwrite($log, PHP_EOL);
            fclose($log);
            return false;
        }
    } else {
        $log = fopen(TRAZA_PATH . '/application/logs/trazabilidad.log', "a+");
        fwrite($log, 'CABEZAL GENERADO: ' . $soap_body_cabezal);
        fwrite($log, PHP_EOL);
        fwrite($log, 'RESPUESTA CABEZAL: ' . $soap_response_cabezal);
        fwrite($log, PHP_EOL);
        fclose($log);
        return false;
    }

    $conn->close();

    return true;
}

function enviar_guid_email_automatico($proceso_trazabilidad, $cuenta, $traza, $etapa) {
    $CI = &get_instance();
    if ($cuenta->envio_guid_automatico && $proceso_trazabilidad->envio_guid_automatico) {
        $regla = new Regla($proceso_trazabilidad->email_envio_guid);
        $destinatario_email = $regla->getExpresionParaOutput($etapa->id);

        $remitente_email = $cuenta->correo_remitente;

        $regla = new Regla($cuenta->asunto_email_guid);
        $asunto_email = $regla->getExpresionParaOutput($etapa->id);

        $regla = new Regla($cuenta->cuerpo_email_guid);
        $contenido_email = $regla->getExpresionParaOutput($etapa->id);

        //Enviar emails
        $data = new stdClass();
        $data->from = $remitente_email;
        $data->from_name = $cuenta->nombre_largo;
        $data->to = $destinatario_email;
        $data->subject = $asunto_email;
        $data->message = $contenido_email;
        $data->cc = null;
        $data->bcc = null;
        $data->attach = null;
        $data_json = json_encode($data);
        $b64 = base64_encode($data_json);
        $comando = 'php index.php tasks/enviarmails enviar "' . $b64 . '" > /dev/null &';
        exec($comando);
        return true;
        /* $CI->load->helper('enviar_email');
          return enviar_emails($remitente_email, $cuenta->nombre_largo, $destinatario_email, $asunto_email, $contenido_email); */
    }
}

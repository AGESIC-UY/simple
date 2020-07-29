<?php

class API extends MY_BackendController {

    public function _auth() {
        UsuarioBackendSesion::force_login();

        if (!UsuarioBackendSesion::has_rol('super') && !UsuarioBackendSesion::has_rol('desarrollo')) {
            redirect('backend');
        }
    }

    /*
     * Documentacion de la API
     */

    public function index() {
        $this->_auth();

        $data['title'] = 'API';
        $data['content'] = 'backend/api/index';
        $this->load->view('backend/template', $data);
    }

    public function token() {
        $this->_auth();

        $data['cuenta'] = UsuarioBackendSesion::usuario()->Cuenta;

        $data['title'] = 'Configurar Código de Acceso';
        $data['content'] = 'backend/api/token';
        $this->load->view('backend/template', $data);
    }

    public function token_form() {
        $this->_auth();

        $cuenta = UsuarioBackendSesion::usuario()->Cuenta;

        $this->form_validation->set_rules('api_token', 'Token', 'max_length[32]');

        $respuesta = new stdClass();
        if ($this->form_validation->run() == true) {
            $cuenta->api_token = $this->input->post('api_token');
            $cuenta->save();

            $respuesta->validacion = true;
            $respuesta->redirect = site_url('backend/api');
        } else {
            $respuesta->validacion = false;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function tramites_recurso() {
        $this->_auth();

        $data['title'] = 'Tramites';
        $data['content'] = 'backend/api/tramites_recurso';
        $this->load->view('backend/template', $data);
    }

    public function tramites_obtener() {
        $this->_auth();

        $data['title'] = 'Tramites: obtener';
        $data['content'] = 'backend/api/tramites_obtener';
        $this->load->view('backend/template', $data);
    }

    public function tramites_listar() {
        $this->_auth();

        $data['title'] = 'Tramites: listar';
        $data['content'] = 'backend/api/tramites_listar';
        $this->load->view('backend/template', $data);
    }

    public function tramites_listarporproceso() {
        $this->_auth();

        $data['title'] = 'Tramites: listar por proceso';
        $data['content'] = 'backend/api/tramites_listarporproceso';
        $this->load->view('backend/template', $data);
    }

    public function tramites_ejecutar() {
        $this->_auth();

        $data['title'] = 'Tramites: ejecutar';
        $data['content'] = 'backend/api/tramites_ejecutar';
        $this->load->view('backend/template', $data);
    }

    public function procesos_instanciar() {
        $this->_auth();

        $data['title'] = 'Procesos: instanciar';
        $data['content'] = 'backend/api/procesos_instanciar';
        $this->load->view('backend/template', $data);
    }

    public function procesos_recurso() {
        $this->_auth();

        $data['title'] = 'Procesos';
        $data['content'] = 'backend/api/procesos_recurso';
        $this->load->view('backend/template', $data);
    }

    public function procesos_obtener() {
        $this->_auth();

        $data['title'] = 'Procesos: obtener';
        $data['content'] = 'backend/api/procesos_obtener';
        $this->load->view('backend/template', $data);
    }

    public function procesos_listar() {
        $this->_auth();

        $data['title'] = 'Procesos: listar';
        $data['content'] = 'backend/api/procesos_listar';
        $this->load->view('backend/template', $data);
    }

    /*
     * Llamadas de la API
     */

    public function tramites($tramite_id = null) {
        $api_token = $this->input->get('token');

        $cuenta = Cuenta::cuentaSegunDominio();

        if (!$cuenta->api_token)
            show_404();

        if ($cuenta->api_token != $api_token)
            show_error('No tiene permisos para acceder a este recurso.', 401);

        $respuesta = new stdClass();
        if ($tramite_id) {
            $tramite = Doctrine::getTable('Tramite')->find($tramite_id);

            if (!$tramite)
                show_404();

            if ($tramite->Proceso->Cuenta != $cuenta)
                show_error('No tiene permisos para acceder a este recurso.', 401);


            $respuesta->tramite = $tramite->toPublicArray();
        } else {
            $offset = $this->input->get('pageToken') ? 1 * base64_decode(urldecode($this->input->get('pageToken'))) : null;
            $limit = ($this->input->get('maxResults') && $this->input->get('maxResults') <= 20) ? 1 * $this->input->get('maxResults') : 10;

            $query = Doctrine_Query::create()
                    ->from('Tramite t, t.Proceso.Cuenta c')
                    ->where('c.id = ?', array($cuenta->id))
                    ->orderBy('id desc');
            if ($offset)
                $query->andWhere('id < ?', $offset);

            $ntramites_restantes = $query->count() - $limit;

            $query->limit($limit);
            $tramites = $query->execute();

            $nextPageToken = null;
            if ($ntramites_restantes > 0)
                $nextPageToken = urlencode(base64_encode($tramites[count($tramites) - 1]->id));

            $respuesta->tramites = new stdClass();
            $respuesta->tramites->titulo = 'Listado de Trámites';
            $respuesta->tramites->tipo = '#tramitesFeed';
            $respuesta->tramites->nextPageToken = $nextPageToken;
            $respuesta->tramites->items = null;
            foreach ($tramites as $t)
                $respuesta->tramites->items[] = $t->toPublicArray();
        }

        header('Content-type: application/json');
        echo json_indent(json_encode($respuesta));
    }

    public function procesos($proceso_id = null, $recurso = null) {
        $api_token = $this->input->get('token');

        $cuenta = Cuenta::cuentaSegunDominio();

        if (!$cuenta->api_token)
            show_404();

        if ($cuenta->api_token != $api_token)
            show_error('No tiene permisos para acceder a este recurso.', 401);

        if ($proceso_id) {
            $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

            if (!$proceso)
                show_404();

            if ($proceso->Cuenta != $cuenta)
                show_error('No tiene permisos para acceder a este recurso.', 401);

            if ($recurso == 'tramites') {
                $offset = $this->input->get('pageToken') ? 1 * base64_decode(urldecode($this->input->get('pageToken'))) : null;
                $limit = ($this->input->get('maxResults') && $this->input->get('maxResults') <= 20) ? 1 * $this->input->get('maxResults') : 10;

                $query = Doctrine_Query::create()
                        ->from('Tramite t, t.Proceso p')
                        ->where('p.id = ?', array($proceso->id))
                        ->orderBy('id desc');
                if ($offset)
                    $query->andWhere('id < ?', $offset);

                $ntramites_restantes = $query->count() - $limit;

                $query->limit($limit);
                $tramites = $query->execute();

                $nextPageToken = null;
                if ($ntramites_restantes > 0)
                    $nextPageToken = urlencode(base64_encode($tramites[count($tramites) - 1]->id));

                $respuesta = new stdClass();
                $respuesta->tramites = new stdClass();
                $respuesta->tramites->titulo = 'Listado de Trámites';
                $respuesta->tramites->tipo = '#tramitesFeed';
                $respuesta->tramites->nextPageToken = $nextPageToken;
                $respuesta->tramites->items = null;
                foreach ($tramites as $t)
                    $respuesta->tramites->items[] = $t->toPublicArray();
            } else {
                $respuesta = new stdClass();
                $respuesta->proceso = $proceso->toPublicArray();
            }
        } else {

            $procesos = Doctrine::getTable('Proceso')->findByCuentaId($cuenta->id);

            $respuesta = new stdClass();
            $respuesta->procesos = new stdClass();
            $respuesta->procesos->titulo = 'Listado de Procesos';
            $respuesta->procesos->tipo = '#procesosFeed';
            $respuesta->procesos->items = null;
            foreach ($procesos as $t)
                $respuesta->procesos->items[] = $t->toPublicArray();
        }

        header('Content-type: application/json');
        echo json_indent(json_encode($respuesta));
    }

    //avanzar Tarea - 2017 v 1.1
    public function ejecutar_tarea($tramite_id = null, $secuencia = 0) {
        $this->session->set_userdata('api_exc', 'true');
        $this->load->helper('trazabilidad_helper');
        //show_error($tramite_id);
        $api_token = $this->input->get('token');
        $datos = $this->input->get('data');
        if (!$datos) {
            $datos = $this->input->post('data');
        }

        $datos_class = json_decode($datos, false);

        $cuenta = Cuenta::cuentaSegunDominio();

        if (!$cuenta->api_token)
            show_404();

        if ($cuenta->api_token != $api_token)
            show_error('No tiene permisos para acceder a este recurso.', 401);

        $respuesta = new stdClass();
        if ($tramite_id) {
            $tramite = Doctrine::getTable('Tramite')->find($tramite_id);

            if (!$tramite)
                show_404();

            if ($tramite->Proceso->Cuenta != $cuenta)
                show_error('No tiene permisos para acceder a este recurso.', 401);


            $respuesta = array(
                "resultado" => "ERROR",
                "mensaje" => "Sin etapa pendiente o automatica"
            );

            $usuario = $this->getUsuario($datos_class->usuario, $tramite->Proceso->cuenta_id);
            if ($usuario === 1) {
                $respuesta = array(
                    'resultado' => 'ERROR',
                    'tema' => 'Tarea no ejecutada',
                    'mensaje' => 'El usuario "' . $datos_class->usuario . '" no extá registrado.'
                );

                header('Content-type: application/json');
                echo json_indent(json_encode($respuesta));
                exit();
            } else if ($usuario === 2) {
                $respuesta = array(
                    'resultado' => 'ERROR',
                    'tema' => 'Tarea no ejecutada',
                    'mensaje' => 'El usuario definido en PARAMETROS no existe.'
                );

                header('Content-type: application/json');
                echo json_indent(json_encode($respuesta));
                exit();
            } else if ($usuario === 3) {
                $respuesta = array(
                    'resultado' => 'ERROR',
                    'tema' => 'Tarea no ejecutada',
                    'mensaje' => 'No existe la clave "usuario_api" registrada como Parámetro.'
                );

                header('Content-type: application/json');
                echo json_indent(json_encode($respuesta));
                exit();
            }

            foreach ($tramite->getEtapasActuales() as $etapa) {
                $etapa_automatica = $etapa->Tarea->automatica;
                //$resultado = $resultado.' ' .$etapa->id  .' ' . $etapa_automatica . ' ' .$etapa->pendiente;
                if ($etapa_automatica) {
                    //$resultado = $resultado.' ' .' es automatica ';
                    if ($etapa->pendiente) {
                        $etapa->asignar_sin_validacion($usuario->id);
                        //se guardan datos de seguimiento
                        foreach ($datos_class->datos->variables as $dato_seguimento_guardar) {
                            $this->guardar_dato_seguimiento($dato_seguimento_guardar, $etapa);
                        }
                        $etapa->avanzar();
                        //$this->generarUsuarioFinEtapa($etapa);
                        $respuesta = array(
                            "resultado" => "OK",
                            "mensaje" => "Se avanzó la etapa con id " . $etapa->id
                        );

                        //TRAZABILIDAD
                        $paso = $etapa->getPasoEjecutable($secuencia);
                        $formulario = $paso->Formulario;
                        $id = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('idEstadoTrazabilidad', $etapa->id);
                        if (!$id) {
                            $dato = new DatoSeguimiento();
                            $dato->etapa_id = $etapa->id;
                            $dato->nombre = 'idEstadoTrazabilidad';
                            $dato->valor = (string) $etapa->Tarea->trazabilidad_estado;
                            $dato->save();
                        }
                        $id_transaccion = str_replace(" ", "_", strtoupper($formulario->Proceso->ProcesoTrazabilidad->organismo_id)) . ':' . str_replace(" ", "_", strtoupper($formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id)) . ':' . $etapa->tramite_id;

                        // -- Genera variable con el ID de transaccion
                        $dato_s = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_transaccion_traza', $etapa->id);
                        if (!$dato_s)
                            $dato_s = new DatoSeguimiento();

                        $dato_s->etapa_id = $etapa->id;
                        $dato_s->nombre = 'id_transaccion_traza';
                        $dato_s->valor = (string) $id_transaccion;
                        $dato_s->save();

                        enviar_traza_inicio_tarea($etapa, $secuencia);
                        enviar_traza_linea_paso_con_cierre_automatico($etapa, $secuencia);
                        $etapa->finalizarPaso($paso, $secuencia);
                        enviar_traza_final_tarea($etapa);

                        if (($etapa->Tarea->final || !$etapa->Tarea->paso_confirmacion)) {
                            enviar_traza_final_proceso($etapa);
                        }
                    }
                }
            }
        }

        header('Content-type: application/json');
        echo json_indent(json_encode($respuesta));
    }

    private function getUsuario($usuario, $cuenta_id) {
        if ($usuario) {
            $usuario_base = Doctrine::getTable('Usuario')->findOneByCuentaIdAndUsuario($cuenta_id, $usuario);
            return !$usuario_base ? 1 : $usuario_base;
        } else {
            $usuario = Doctrine::getTable('Parametro')->findOneByCuentaIdAndClave($cuenta_id, "usuario_api");
            if ($usuario) {
                $usuario_base = Doctrine::getTable('Usuario')->findOneByCuentaIdAndUsuario($cuenta_id, $usuario->valor);
                return !$usuario_base ? 2 : $usuario_base;
            } else {
                return 3;
            }
        }
    }

    public function instanciar($proceso_id = null) {
        $this->session->set_userdata('api_exc', 'true');
        $api_token = $this->input->get('token');
        $datos = $this->input->get('data');

        if (!$datos) {
            $datos = $this->input->post('data');
        }

        $datos_class = json_decode($datos, false);

        $cuenta = Cuenta::cuentaSegunDominio();

        if (!$proceso_id || !$cuenta->api_token) {
            show_404();
        }

        if ($cuenta->api_token != $api_token) {
            show_error('No tiene permisos para acceder a este recurso.', 401);
        }

        $proceso_param = Doctrine::getTable('Proceso')->find($proceso_id);
        $proces_activo = Doctrine::getTable('Proceso')->findIdProcesoActivoRoot($proceso_param->root, $cuenta->id);
        //print_r($proces_activo);
        if (!$proces_activo->id) {
            $respuesta = array(
                'resultado' => 'ERROR',
                'tema' => 'Trámite no iniciado',
                'mensaje' => 'No existe una versión activa para el proceso con id "' . $proceso_id . '".'
            );
            header('Content-type: application/json');
            echo json_indent(json_encode($respuesta));
            exit();
        }
        $proceso = Doctrine::getTable('Proceso')->find($proces_activo->id);

        if (!$proceso) {
            $respuesta = array(
                'resultado' => 'ERROR',
                'tema' => 'Trámite no iniciado',
                'mensaje' => 'El proceso con id "' . $proceso_id . '" no existe.'
            );

            header('Content-type: application/json');
            echo json_indent(json_encode($respuesta));
            exit();
        }

        if (!$proceso->instanciar_api) {
            $respuesta = array(
                'resultado' => 'ERROR',
                'tema' => 'Trámite no iniciado',
                'mensaje' => 'El proceso "' . $proceso->nombre . '" no está configurado para ser instanciado.'
            );

            header('Content-type: application/json');
            echo json_indent(json_encode($respuesta));
            exit();
        }

        if (!isset($datos_class->usuario)) {
            $datos_class->usuario = "";
        }

        if (!isset($datos_class->tarea_hasta)) {
            $datos_class->tarea_hasta = "";
        }

        if (!isset($datos_class->datos)) {
            $datos_class->datos = new stdClass();
        }

        //busco usuario, si no existe lo creo
        $usuario = $this->getUsuario($datos_class->usuario, $proceso->cuenta_id);
        if ($usuario === 1) {
            $respuesta = array(
                'resultado' => 'ERROR',
                'tema' => 'Tarea no ejecutada',
                'mensaje' => 'El usuario "' . $datos_class->usuario . '" no está registrado.'
            );

            header('Content-type: application/json');
            echo json_indent(json_encode($respuesta));
            exit();
        } else if ($usuario === 2) {
            $respuesta = array(
                'resultado' => 'ERROR',
                'tema' => 'Tarea no ejecutada',
                'mensaje' => 'El usuario definido en PARAMETROS no existe.'
            );

            header('Content-type: application/json');
            echo json_indent(json_encode($respuesta));
            exit();
        } else if ($usuario === 3) {
            $respuesta = array(
                'resultado' => 'ERROR',
                'tema' => 'Tarea no ejecutada',
                'mensaje' => 'No existe la clave "usuario_api" registrada como Parámetro.'
            );

            header('Content-type: application/json');
            echo json_indent(json_encode($respuesta));
            exit();
        }

        $this->load->helper('pasos_pdf_helper');
        $this->load->helper('trazabilidad_helper');
        $this->load->helper('archivo_base_64_helper');


        //se crea una nueva instancia del proceso, creando un tramite, no se utilizar el metodo iniciar de tramite porque utiliza el usuario logueado
        $tramite = new Tramite();
        $tramite->proceso_id = $proceso->id;
        $tramite->pendiente = 1;

        //se crea una nueva instancia de la tarea inicial, creando una etapa
        $etapa = new Etapa();
        $etapa->tarea_id = $proceso->getTareaInicial()->id;
        $etapa->pendiente = 1;

        $tramite->Etapas[] = $etapa;
        $tramite->save();

        //se asigna usuario a la primera etapa
        $etapa->asignar_sin_validacion($usuario->id);

        //obtenemos utlima etapa que en este caso es la primera (esta pendiente por ser la primera)
        $ultima_etapa = $etapa; // ultima etapa representa la etapa actual

        $id_primera_etapa = $ultima_etapa->id;

        $tarea_hasta_es_la_tarea_inicial = $ultima_etapa->id == $id_primera_etapa && strtolower(trim($ultima_etapa->Tarea->nombre)) == strtolower(trim($datos_class->tarea_hasta));
        $tarea_hasta_no_es_igual_ultima_etapa = !empty($ultima_etapa) && strtolower(trim($ultima_etapa->Tarea->nombre)) != strtolower(trim($datos_class->tarea_hasta));

        //se avanza etapas automaticamente hasta la indicada
        while ($tarea_hasta_es_la_tarea_inicial || $tarea_hasta_no_es_igual_ultima_etapa) {

            //se guardan datos de seguimiento
            foreach ($datos_class->datos as $tarea_datos) {
                //guardo solo datos de la tarea que se esta ejecutando
                if (strtolower(trim($tarea_datos->tarea->nombre)) == strtolower(trim($ultima_etapa->Tarea->nombre))) {
                    foreach ($tarea_datos->tarea->variables as $dato_seguimento_guardar) {
                        $this->guardar_dato_seguimiento($dato_seguimento_guardar, $ultima_etapa);
                    }
                    break;
                }
            }

            //se marca la etapa como ejecutada automaticamente para luego mostrarla de otro color en seguimiento
            $dato_etapa_automatica = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('tarea_ejecutada_automaticamente', $ultima_etapa->id);
            if ($dato_etapa_automatica) {
                $dato_etapa_automatica->delete();
            }
            $dato_etapa_automatica = new DatoSeguimiento();
            $dato_etapa_automatica->etapa_id = $ultima_etapa->id;
            $dato_etapa_automatica->nombre = 'tarea_ejecutada_automaticamente';
            $dato_etapa_automatica->valor = '1';
            $dato_etapa_automatica->save();

            //se genera usaurio de fin de etapa en datos de seguimiento
            $dato_usuario_fin_etapa_generado = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("usuario_fin_etapa_generado", $ultima_etapa->id);
            if ($dato_usuario_fin_etapa_generado) {
                $dato_usuario_fin_etapa_generado->delete();
            }
            $dato_usuario_fin_etapa_generado = new DatoSeguimiento();
            $dato_usuario_fin_etapa_generado->nombre = 'usuario_fin_etapa_generado';
            $dato_usuario_fin_etapa_generado->valor = $usuario->id;
            $dato_usuario_fin_etapa_generado->etapa_id = $ultima_etapa->id;
            $dato_usuario_fin_etapa_generado->save();

            // -- Se genera el ID de transaccion correspondiente para trazabilidad.
            $id_transaccion = str_replace(" ", "_", strtoupper($ultima_etapa->Tarea->Proceso->ProcesoTrazabilidad->organismo_id)) . ':' . str_replace(" ", "_", strtoupper($ultima_etapa->Tarea->Proceso->ProcesoTrazabilidad->proceso_externo_id)) . ':' . $ultima_etapa->tramite_id;

            // -- Genera variable con el ID de transaccion
            $dato_id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_transaccion_traza', $ultima_etapa->id);
            if ($dato_id_transaccion) {
                $dato_id_transaccion->delete();
            }
            $dato_id_transaccion = new DatoSeguimiento();
            $dato_id_transaccion->etapa_id = $ultima_etapa->id;
            $dato_id_transaccion->nombre = 'id_transaccion_traza';
            $dato_id_transaccion->valor = (string) $id_transaccion;
            $dato_id_transaccion->save();

            //ejecuta los eventos antes de la ejecutar la tarea
            $eventos_antes_tarea = Doctrine_Query::create()->from('Evento e')
                    ->where('e.tarea_id = ? AND e.instante = ? AND e.paso_id IS NULL AND e.instanciar_api = 1', array($ultima_etapa->Tarea->id, 'antes'))
                    ->execute();
            foreach ($eventos_antes_tarea as $e) {
                $r = new Regla($e->regla);
                if ($r->evaluar($ultima_etapa->id)) {
                    $e->Accion->ejecutar($ultima_etapa);
                }
            }

            $secuencia = 0;
            $ejecutar_eventos_tarea_despues = true;
            //ejecuta pasos de la etapa, eventos y trazabilidad
            foreach ($ultima_etapa->getPasosEjecutables() as $paso) {
                $paso_final = sizeof($ultima_etapa->getPasosEjecutables()) - 1 == $secuencia;

                if ($secuencia == 0 && !$paso_final) {
                    enviar_traza_inicio_tarea($ultima_etapa, $secuencia);
                    $this->ejecutar_eventos_paso_antes($paso, $ultima_etapa);
                    enviar_traza_linea_paso($ultima_etapa, $secuencia);
                    $this->ejecutar_eventos_paso_despues($paso, $ultima_etapa);
                }
                //caso en que tiene 1 solo paso
                else if ($secuencia == 0 && $paso_final) {
                    enviar_traza_inicio_tarea($ultima_etapa, $secuencia);
                    $this->ejecutar_eventos_paso_antes($paso, $ultima_etapa);
                    enviar_traza_linea_paso($ultima_etapa, $secuencia);
                    $this->ejecutar_eventos_paso_despues($paso, $ultima_etapa);
                    enviar_traza_final_tarea($ultima_etapa);
                    $this->ejecutar_eventos_tarea_despues($ultima_etapa);
                    enviar_traza_final_proceso($ultima_etapa);
                    break;
                }
                //paso final de la etapa
                else if ($secuencia != 0 && $paso_final) {
                    $this->ejecutar_eventos_paso_antes($paso, $ultima_etapa);
                    enviar_traza_linea_paso($ultima_etapa, $secuencia);
                    $this->ejecutar_eventos_paso_despues($paso, $ultima_etapa);
                    enviar_traza_final_tarea($ultima_etapa);
                    $this->ejecutar_eventos_tarea_despues($ultima_etapa);
                    enviar_traza_final_proceso($ultima_etapa);
                    break;
                } else {
                    $this->ejecutar_eventos_paso_antes($paso, $ultima_etapa);
                    enviar_traza_linea_paso($ultima_etapa, $secuencia);
                    $this->ejecutar_eventos_paso_despues($paso, $ultima_etapa);
                }

                $secuencia++;
            }

            //genera el pdf con los pasos antes de avanzar
            imprimir_pasos_pdf($ultima_etapa->id);

            //se avanza la etapa a la siguiente (siguiente tarea)
            $ultima_etapa->avanzar_sin_ejecutar_eventos();

            //luego de avanzar obtenemos nueva ultima etapa para seguir con la condicion del while
            $ultima_etapa = $tramite->getUltimaEtapaPendiente();
            //print_r($ultima_etapa . " ->" . $ultima_etapa->Tarea->asignacion . " -- " . $ultima_etapa->Tarea->nombre);
            //asignamos el usuario solo en caso de que sea auto servicio a la proxima etapa
            if ($ultima_etapa) {
                if (($ultima_etapa->Tarea->asignacion == 'autoservicio') && strtolower(trim($ultima_etapa->Tarea->nombre)) != strtolower(trim($datos_class->tarea_hasta))) {
                    $ultima_etapa->asignar_sin_ejecutar_eventos($usuario->id);
                }

                $tarea_hasta_no_es_igual_ultima_etapa = !empty($ultima_etapa) && strtolower(trim($ultima_etapa->Tarea->nombre)) != strtolower(trim($datos_class->tarea_hasta));
            } else {
                break;
            }
            if ($tarea_hasta_es_la_tarea_inicial) {
                break;
            }
        }
        if (!$ultima_etapa) {
            $respuesta = array(
                'resultado' => 'OK',
                'tramiteId' => $tramite->id,
                'mensaje' => 'El proceso "' . $proceso->nombre . '" fue instanciado y cerrado correctamente.'
            );
        } else {
            $respuesta = array(
                'resultado' => 'OK',
                'tramiteId' => $tramite->id,
                'mensaje' => 'El proceso "' . $proceso->nombre . '" fue instanciado correctamente, avanzando hasta la tarea "' . $datos_class->tarea_hasta . '".'
            );
        }
        header('Content-type: application/json');
        echo json_indent(json_encode($respuesta));
    }

    private function guardar_dato_seguimiento($dato_seguimento_guardar, $etapa) {
        if (isset($dato_seguimento_guardar->extension)) {
            //archivo base 64
            $tramite_id = $etapa->Tramite->id;
            $nombre_original_archivo = $dato_seguimento_guardar->nombre;
            $extension = $dato_seguimento_guardar->extension;
            $datos_base_64 = $dato_seguimento_guardar->valor;
            $nombre_archivo_subido = procesar_archivo_base_64($tramite_id, $nombre_original_archivo, $extension, $datos_base_64);

            if ($nombre_archivo_subido) {
                $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($dato_seguimento_guardar->nombre, $etapa->id);
                if ($dato_seguimiento) {
                    $dato_seguimiento->delete();
                }
                $dato_seguimiento = new DatoSeguimiento();
                $dato_seguimiento->etapa_id = $etapa->id;
                $dato_seguimiento->nombre = $dato_seguimento_guardar->nombre;
                $dato_seguimiento->valor = (string) $nombre_archivo_subido . '.' . $extension;
                $dato_seguimiento->save();

                $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($dato_seguimento_guardar->nombre . '__origen', $etapa->id);
                if ($dato_seguimiento) {
                    $dato_seguimiento->delete();
                }
                $dato_seguimiento = new DatoSeguimiento();
                $dato_seguimiento->etapa_id = $etapa->id;
                $dato_seguimiento->nombre = $dato_seguimento_guardar->nombre . '__origen';
                $dato_seguimiento->valor = (string) $nombre_original_archivo . '.' . $extension;
                $dato_seguimiento->save();
            }
        }
        //es un campo check box, radio o select
        else if (isset($dato_seguimento_guardar->etiqueta)) {
            $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($dato_seguimento_guardar->nombre . '__etiqueta', $etapa->id);

            if ($dato_seguimiento) {
                $dato_seguimiento->delete();
            }
            $valor = $dato_seguimento_guardar->etiqueta;
            if (is_array($valor)) {
                $valor = json_encode($valor, 0);
            }
            $dato_seguimiento = new DatoSeguimiento();
            $dato_seguimiento->etapa_id = $etapa->id;
            $dato_seguimiento->nombre = $dato_seguimento_guardar->nombre . '__etiqueta';
            $dato_seguimiento->valor = (string) $valor;
            $dato_seguimiento->save();

            $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($dato_seguimento_guardar->nombre, $etapa->id);
            if ($dato_seguimiento) {
                $dato_seguimiento->delete();
            }
            $valor = $dato_seguimento_guardar->valor;
            if (is_array($valor)) {
                $valor = json_encode($valor, 0);
            }
            $dato_seguimiento = new DatoSeguimiento();
            $dato_seguimiento->etapa_id = $etapa->id;
            $dato_seguimiento->nombre = $dato_seguimento_guardar->nombre;
            $dato_seguimiento->valor = (string) $valor;
            $dato_seguimiento->save();
        } else {
            $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($dato_seguimento_guardar->nombre, $etapa->id);
            if ($dato_seguimiento) {
                $dato_seguimiento->delete();
            }
            $dato_seguimiento = new DatoSeguimiento();
            $dato_seguimiento->etapa_id = $etapa->id;
            $dato_seguimiento->nombre = $dato_seguimento_guardar->nombre;
            $dato_seguimiento->valor = (string) $dato_seguimento_guardar->valor;
            $dato_seguimiento->save();
        }
    }

    private function validar_usuario($usuario) {
        $usuario = explode("-", $usuario);

        $pais = $usuario[0];
        $tipo_doc = $usuario[1];
        $num_doc = $usuario[2];

        if (is_numeric($num_doc)) {
            if ($pais == 'uy' && $tipo_doc == 'ci') {
                return true;
            } else if ($pais == 'ar' && $tipo_doc == 'dni' || $tipo_doc == 'psp') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function ejecutar_eventos_paso_antes($paso, $etapa) {
        //Ejecutamos los eventos iniciales del paso
        $eventos_inicio_paso = Doctrine_Query::create()->from('Evento e')
                ->where('e.paso_id = ? AND e.instante = ? AND e.instanciar_api = 1', array($paso->id, 'antes'))
                ->execute();
        foreach ($eventos_inicio_paso as $e) {
            $r = new Regla($e->regla);
            if ($r->evaluar($etapa->id))
                $e->Accion->ejecutar($etapa, $e);
        }
    }

    private function ejecutar_eventos_paso_despues($paso, $etapa) {

        //Ejecutamos los eventos finales del paso
        $eventos_fin_paso = Doctrine_Query::create()->from('Evento e')
                ->where('e.paso_id = ? AND e.instante = ? AND e.instanciar_api = 1', array($paso->id, 'despues'))
                ->execute();
        foreach ($eventos_fin_paso as $e) {
            $r = new Regla($e->regla);
            if ($r->evaluar($etapa->id))
                $e->Accion->ejecutar($etapa, $e);
        }
    }

    private function ejecutar_eventos_tarea_despues($etapa) {
        //ejecuta los eventos despues de ejecutar la tarea
        $eventos_despues_tarea = Doctrine_Query::create()->from('Evento e')
                ->where('e.tarea_id = ? AND e.instante = ? AND e.paso_id IS NULL AND e.instanciar_api = 1', array($etapa->Tarea->id, 'despues'))
                ->execute();
        foreach ($eventos_despues_tarea as $e) {
            $r = new Regla($e->regla);
            if ($r->evaluar($etapa->id)) {
                $e->Accion->ejecutar($etapa, $e);
            }
        }
    }

}

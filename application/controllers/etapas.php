<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Etapas extends MY_Controller {

    public function __construct() {
        parent::__construct();

        UsuarioSesion::limpiar_sesion();
    }

    public function inbox() {
      if(!UsuarioSesion::usuario()->registrado){
          redirect(site_url());
      }

      $orderby = 'updated_at';
      $direction = $this->input->get('direction') == 'desc' ? 'desc' : 'asc';

      $data['etapas'] = Doctrine::getTable('Etapa')->findPendientes(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio(),$orderby,$direction);

      $data['orderby']=$orderby;
      $data['direction']=$direction;
      $data['sidebar'] = 'inbox';
      $data['content'] = 'etapas/inbox';
      $data['title'] = 'Bandeja de Entrada';
      $this->load->view('template', $data);
    }

    public function sinasignar() {
        if (!UsuarioSesion::usuario()->registrado) {
            $this->session->set_flashdata('redirect', current_url());
            redirect('autenticacion/login');
        }

        $data['etapas'] = Doctrine::getTable('Etapa')->findSinAsignar(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio());

        $data['sidebar'] = 'sinasignar';
        $data['content'] = 'etapas/sinasignar';
        $data['title'] = 'Sin Asignar';
        $this->load->view('template', $data);
    }

    public function ejecutar($etapa_id, $secuencia=0) {
        $iframe = $this->input->get('iframe');

        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
        if(!$etapa) {
            show_404();
        }
        if ($etapa->usuario_id != UsuarioSesion::usuario()->id) {
            if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('autenticacion/login_saml');
            }

            $data['error'] = 'Usuario no tiene permisos para ejecutar esta etapa.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }
        if (!$etapa->pendiente) {
            $data['error'] = 'Esta etapa ya fue completada';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }
        if (!$etapa->Tarea->activa()) {
            $data['error'] = 'Esta etapa no se encuentra activa';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }
        if ($etapa->vencida()) {
            $data['error'] = 'Esta etapa se encuentra vencida';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }

        $qs = $this->input->server('QUERY_STRING');
        $paso = $etapa->getPasoEjecutable($secuencia);
        if (!$paso) {
            redirect('etapas/ejecutar_fin/' . $etapa->id . ($qs ? '?' . $qs : ''));
        }
        else if (($etapa->Tarea->final || !$etapa->Tarea->paso_confirmacion) && $paso->getReadonly() && end($etapa->getPasosEjecutables()) == $paso) { //No se requiere mas input
            $etapa->iniciarPaso($paso, $secuencia);
            $etapa->finalizarPaso($paso, $secuencia);
            $etapa->avanzar();
            redirect('etapas/ver/' . $etapa->id . '/' . (count($etapa->getPasosEjecutables())-1));
        }
        else {
            $etapa->iniciarPaso($paso, $secuencia);

            $data['secuencia'] = $secuencia;
            $data['etapa'] = $etapa;
            $data['paso'] = $paso;
            $data['qs'] = $this->input->server('QUERY_STRING');

            $data['sidebar'] = UsuarioSesion::usuario()->registrado ? 'inbox' : 'disponibles';
            $data['content'] = 'etapas/ejecutar';
            $data['title'] = $etapa->Tarea->nombre;
            $template = $this->input->get('iframe') ? 'template_iframe' : 'template';

            // Paso actual (se utiliza diferente de 'secuencia' ya que se incrementará en la vista).
            $data['step_position'] = $secuencia;

            $this->load->view($template, $data);
        }
    }

    public function ejecutar_form($etapa_id, $secuencia) {
        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

        if ($etapa->usuario_id != UsuarioSesion::usuario()->id) {
            $data['error'] = 'Usuario no tiene permisos para ejecutar esta etapa.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }
        if (!$etapa->pendiente) {
            $data['error'] = 'Esta etapa ya fue completada';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }
        if (!$etapa->Tarea->activa()) {
            $data['error'] = 'Esta etapa no se encuentra activa';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }
        if ($etapa->vencida()) {
            $data['error'] = 'Esta etapa se encuentra vencida';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }

        $paso = $etapa->getPasoEjecutable($secuencia);
        $formulario = $paso->Formulario;
        $modo = $paso->modo;

        $respuesta = new stdClass();

        if ($modo == 'edicion') {
            $organismo_id = null;

            $validar_formulario = FALSE;
            foreach ($formulario->Campos as $c) {
                //Validamos los campos que no sean readonly y que esten disponibles (que su campo dependiente se cumpla)
                if ($c->isEditableWithCurrentPOST()) {
                    $c->formValidate($etapa->id);
                    $validar_formulario = TRUE;
                }
            }

            // Si se requiere guardado parcial.
            if($this->input->post('no_advance') == 1) {
              $validado = true;
            }
            else {
              if($this->form_validation->run() == TRUE) {
                $validado = true;
              }
              else {
                $validado = false;
              }
            }

            if (!$validar_formulario || $validado) {
                //Almacenamos los campos
                foreach ($formulario->Campos as $c) {
                    //Almacenamos los campos que no sean readonly y que esten disponibles (que su campo dependiente se cumpla)
                    if ($c->isEditableWithCurrentPOST()) {
                        if(($c->tipo != 'error') && ($c->tipo != 'dialogo')) {
                          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($c->nombre, $etapa->id);
                          if (!$dato)
                            $dato = new DatoSeguimiento();

                          $dato->nombre = $c->nombre;
                          $dato->valor = $this->input->post($c->nombre);
                          $dato->etapa_id = $etapa->id;
                          $dato->save();
                        }
                    }
                }
                $etapa->save();

                // Si se requiere guardado parcial.
                if($this->input->post('no_advance') == 1) {
                  $respuesta->validacion = TRUE;
                  $respuesta->redirect = site_url('/etapas/inbox');
                }
                else {
                  $etapa->finalizarPaso($paso, $secuencia);

                  // -- Si encuentra variables de errores avisa que se ha registrado un error de parte de una acción.
                  $errors = false;
                  foreach ($formulario->Campos as $c) {
                    if(($c->tipo == 'error') || ($c->tipo == 'dialogo')) {
                      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $c->valor_default), $etapa->id);

                      if($dato) {
                        if($dato->valor != "") {
                          $errors = true;
                        }
                      }
                    }
                  }

                  $respuesta->validacion = TRUE;

                  // ----- traza inicio
                  if($etapa->Tarea->trazabilidad) {
                    $tarea_inicial = $formulario->Proceso->getTareaInicial();

										$cabezal = 0;
										$num_paso = $secuencia;
										$num_paso_linea = $secuencia + 1;

										if((sizeof($etapa->getPasosEjecutables())-1) == $secuencia) {
											$estado = 2;
											$estado_linea = 'F';
										}
										else {
											if(($tarea_inicial->id == $etapa->Tarea->id) && ($secuencia == 0)) {
												$cabezal = 1;
												$estado = 1;
											}
											else {
												$estado = 2;
											}

											$estado_linea = 'I';
										}

                    try {
      								$traza_existente = Doctrine_Query::create()
													->from('Trazabilidad ts')
													->where('ts.id_tramite = ? AND ts.id_etapa = ? AND ts.num_paso_real = ?',
														array($etapa->Tramite->id, $etapa->id, $paso->orden))
													->limit(1)
													->fetchOne();

											$paso_existe = null;
											if(!empty($traza_existente)) {
												$paso_existe = $traza_existente->num_paso_real;
											}

											$traza_tramite = Doctrine_Query::create()
															->from('Trazabilidad ts')
															->where('ts.id_tramite = ?', array($etapa->Tramite->id))
															->orderBy('secuencia DESC')
															->limit(1)
															->fetchOne();

											if(empty($traza_tramite)) {
												$traza_cabezal = Doctrine_Query::create()
															->from('Trazabilidad ts')
															->where('ts.id_tramite = ? AND ts.estado = ?', array($etapa->Tramite->id, 'C'))
															->orderBy('secuencia ASC')
															->fetchOne();

												$sec = 0;
												$sec_linea = $sec + 1;

												if(empty($traza_cabezal)) {
													$traza = new Trazabilidad();
													$traza->id_etapa = $etapa->id;
													$traza->id_tramite = $etapa->tramite_id;
													$traza->id_tarea = $etapa->Tarea->id;
													$traza->num_paso = 0;
													$traza->secuencia = $sec;
													$traza->estado = 'C';
													$traza->save();
												}
												else {
													$cabezal = 0;
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
											}
											else {
												$traza_cabezal = Doctrine_Query::create()
															->from('Trazabilidad ts')
															->where('ts.id_tramite = ? AND ts.estado = ?', array($etapa->Tramite->id, 'C'))
															->orderBy('secuencia ASC')
															->fetchOne();

												if(!empty($traza_cabezal)) {
													$cabezal = 0;
												}

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

												if(empty($traza_tramite_actual)) {
													$traza_misma_tarea = Doctrine_Query::create()
																->from('Trazabilidad ts')
																->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso->orden))
																->orderBy('secuencia DESC')
																->fetchOne();

													$traza->num_paso = (!$traza_misma_tarea ? $traza_tramite->num_paso + 1 : $traza_misma_tarea->num_paso);
													$num_paso = $traza_tramite->num_paso + 1;
													$num_paso_linea = (!$traza_misma_tarea ? $num_paso : $traza_misma_tarea->num_paso);
													$traza->secuencia = $sec;
												}
												else {
													$traza->num_paso = (!$paso_existe ? $traza_tramite->num_paso + 1 : $traza_tramite->num_paso);
													$num_paso = (!$paso_existe ? $traza_tramite->num_paso + 1 : $paso_existe);
													$num_paso_linea = (!$paso_existe ? $num_paso : $paso_existe);
													$traza->secuencia = $traza_tramite->secuencia + 1;
												}

												$traza->estado = $estado_linea;
												$traza->num_paso_real = $paso->orden;
												$traza->save();
											}

											$this->load->helper('device_helper');
											$canal_inicio = detect_current_device();

											$cantidad_total_pasos = 0;
											foreach($formulario->Proceso->Tareas as $tarea) {
												$cantidad_total_pasos = $cantidad_total_pasos + count($tarea->Pasos);
											}

											(empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = $formulario->Proceso->ProcesoTrazabilidad->organismo_id : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);

											if (($secuencia == 0) || ($secuencia == sizeof($etapa->getPasosEjecutables()) - 1)) {
												$args = array('tramite_id' => $etapa->tramite_id, 'secuencia' => $sec_linea, 'paso' => $num_paso_linea,
																		  'organismo_id' => $formulario->Proceso->ProcesoTrazabilidad->organismo_id, 'oficina_id' => $oficina_id,
																		  'proceso_externo_id' => $formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
																		  'usuario_id' => UsuarioSesion::usuario()->id, 'pasos_ejecutables' => $cantidad_total_pasos,
																		  'cabezal' => $cabezal, 'nombre_tarea' => $etapa->Tarea->nombre, 'estado' => $estado,
																		  'canal_inicio' => $canal_inicio, 'nombre_paso' => $paso->nombre);

												// -- Encola la operacion
												$CI =& get_instance();
												$CI->load->library('resque/resque');
												Resque::enqueue('default', 'Trazabilidad', $args);
											}
                    }
                    catch(Exception $e) {
                      log_message('error', $e->getMessage());
                    }
                  }
                  // ----- traza fin

                  $qs = $this->input->server('QUERY_STRING');
                  $prox_paso = $etapa->getPasoEjecutable($secuencia + 1);

                  // Si hay registro de error de parte de una acción invocada vuelve a mostrar la secuencia actual, de lo contrario avanza.
                  if($errors) {
                      $respuesta->redirect = site_url('etapas/ejecutar/' . $etapa_id . '/' . ($secuencia)) . ($qs ? '?' . $qs : '');
                  }
                  else {
                    if (!$prox_paso) {
                        $respuesta->redirect = site_url('etapas/ejecutar_fin/' . $etapa_id) . ($qs ? '?' . $qs : '');
                    } else if ($etapa->Tarea->final && $prox_paso->getReadonly() && end($etapa->getPasosEjecutables()) == $prox_paso) { //Cerrado automatico
                        $etapa->iniciarPaso($prox_paso, $secuencia);
                        $etapa->finalizarPaso($prox_paso, $secuencia);
                        $etapa->avanzar();
                        $respuesta->redirect = site_url('etapas/ver/' . $etapa->id . '/' . (count($etapa->getPasosEjecutables())-1));
                    } else {
                        $respuesta->redirect = site_url('etapas/ejecutar/' . $etapa_id . '/' . ($secuencia + 1)) . ($qs ? '?' . $qs : '');
                    }
                  }
              }
            } else {
                $respuesta->validacion = FALSE;
                $respuesta->errores = validation_errors();
            }
        } else if ($modo == 'visualizacion') {
            $respuesta->validacion = TRUE;

            $qs = $this->input->server('QUERY_STRING');
            $prox_paso = $etapa->getPasoEjecutable($secuencia + 1);

            if (!$prox_paso) {
                $respuesta->redirect = site_url('etapas/ejecutar_fin/' . $etapa_id) . ($qs ? '?' . $qs : '');
            } else if ($etapa->Tarea->final && $prox_paso->getReadonly() && end($etapa->getPasosEjecutables()) == $prox_paso) { //Cerrado automatico
                $etapa->iniciarPaso($prox_paso, $secuencia);
                $etapa->finalizarPaso($prox_paso, $secuencia);
                $etapa->avanzar();
                $respuesta->redirect = site_url('etapas/ver/' . $etapa->id . '/' . (count($etapa->getPasosEjecutables())-1));
            } else {
                $respuesta->redirect = site_url('etapas/ejecutar/' . $etapa_id . '/' . ($secuencia + 1)) . ($qs ? '?' . $qs : '');
            }
        }

        echo json_encode($respuesta);
    }

    public function asignar($etapa_id) {
        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

        if ($etapa->usuario_id) {
            $data['error'] = 'Etapa ya fue asignada.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }

        if (!$etapa->canUsuarioAsignarsela(UsuarioSesion::usuario()->id)) {
            $data['error'] = 'Usuario no puede asignarse esta etapa.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }

        $etapa->asignar(UsuarioSesion::usuario()->id);

        redirect('etapas/inbox');
    }

    public function ejecutar_fin($etapa_id) {
        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

        if ($etapa->usuario_id != UsuarioSesion::usuario()->id) {
            $data['error'] = 'Usuario no tiene permisos para ejecutar esta etapa.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }
        if (!$etapa->pendiente) {
            $data['error'] = 'Esta etapa ya fue completada';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }
        if (!$etapa->Tarea->activa()) {
            $data['error'] = 'Esta etapa no se encuentra activa';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }

        //if($etapa->Tarea->asignacion!='manual'){
        //    $etapa->Tramite->avanzarEtapa();
        //    redirect();
        //    exit;
        //}

        $data['etapa'] = $etapa;
        $data['tareas_proximas'] = $etapa->getTareasProximas();
        $data['qs'] = $this->input->server('QUERY_STRING');

        $data['sidebar'] = UsuarioSesion::usuario()->registrado ? 'inbox' : 'disponibles';
        $data['content'] = 'etapas/ejecutar_fin';
        $data['title'] = $etapa->Tarea->nombre;
        $template = $this->input->get('iframe') ? 'template_iframe' : 'template';

        $this->load->view($template, $data);
    }

    public function ejecutar_fin_form($etapa_id) {
        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

        if ($etapa->usuario_id != UsuarioSesion::usuario()->id) {
            $data['error'] = 'Usuario no tiene permisos para ejecutar esta etapa.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }
        if (!$etapa->pendiente) {
            $data['error'] = 'Esta etapa ya fue completada';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }
        if (!$etapa->Tarea->activa()) {
            $data['error'] = 'Esta etapa no se encuentra activa';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }

        $etapa->avanzar($this->input->post('usuarios_a_asignar'));

        // -- Si encuentra variables de errores avisa que se ha registrado un error de parte de una acción.
        $errors = false;
        $errors_msg = '';
        $error_servicio_com = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("ws_error", $etapa->id);
        $error_servicio = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("error", $etapa->id);

        if($error_servicio_com) {
          if($error_servicio_com->valor != "") {
            $errors = true;
            $errors_msg = $error_servicio_com->valor;
          }
        }
        if($error_servicio) {
          if($error_servicio->valor != "") {
            $errors = true;
            $errors_msg = $error_servicio->valor;
          }
        }

        // Si hay registro de error de parte de una acción invocada vuelve a mostrar la secuencia actual, de lo contrario avanza.
        if($errors) {
          $respuesta->redirect = site_url('etapas/ejecutar_fin/' . $etapa_id);
          $respuesta->error_paso_final = $errors_msg;
        }
        else {
          $respuesta = new stdClass();
          $respuesta->validacion = TRUE;

          if ($this->input->get('iframe'))
              $respuesta->redirect = site_url('etapas/ejecutar_exito');
          else
              $respuesta->redirect = site_url();
        }

        echo json_encode($respuesta);
    }

    //Pagina que indica que la etapa se completo con exito. Solamente la ven los que acceden mediante iframe.
    public function ejecutar_exito() {
        $data['content'] = 'etapas/ejecutar_exito';
        $data['title'] = 'Etapa completada con éxito';

        $this->load->view('template_iframe', $data);
    }

    public function ver($etapa_id, $secuencia = 0) {
        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

        if (UsuarioSesion::usuario()->id != $etapa->usuario_id) {
            $data['error'] = 'No tiene permisos para hacer seguimiento a este tramite.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
        }

        $paso = $etapa->getPasoEjecutable($secuencia);

        $data['etapa'] = $etapa;
        $data['paso'] = $paso;
        $data['secuencia'] = $secuencia;

        $data['sidebar'] = 'participados';
        $data['title'] = 'Historial - ' . $etapa->Tarea->nombre;
        $data['content'] = 'etapas/ver';
        $this->load->view('template', $data);
    }
}

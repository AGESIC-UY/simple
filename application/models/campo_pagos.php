<?php

require_once('campo.php');

class CampoPagos extends Campo {

    public $requiere_nombre=true;
    public $requiere_datos=false;
    public $estatico=true;
    public $etiqueta_tamano='xxlarge';
    public $requiere_validacion=true;

    function setTableDefinition() {
        parent::setTableDefinition();
    }

    function setUp() {
        parent::setUp();
        $this->setTableName("campo");
    }

    public function isEditableWithCurrentPOST(){
      return true;
    }

    public function formValidate($etapa_id = null){
      $CI=& get_instance();
      $validacion=$this->validacion;
      if($etapa_id){
          $regla = new Regla($this->validacion);
          $validacion = $regla->getExpresionParaOutput($etapa_id);
      }

      $validacioStr = implode('|', $validacion);
      $CI->form_validation->set_rules($this->nombre, ucfirst($this->etiqueta),$validacioStr.'['.$etapa_id.']');
    }

    protected function display($modo, $dato, $etapa_id) {
        $CI = &get_instance();

        $pago = Doctrine_Query::create()
                    ->from('Pago p')
                    ->where('p.id_etapa = ?', $etapa_id)
                  ->andWhere('p.estado = ?', 'pago_func')
                  ->fetchOne();

        if($this->pago_online == 0 && UsuarioSesion::usuarioMesaDeEntrada() && $CI->session->userdata('id_usuario_ciudadano') && $CI->uri->segment(1) != 'backend') {
          if(!$pago){
            return '<input type="checkbox" name="check_pago_online" id="check_pago_online" value="1">'.MENSAJE_PAGO_FUNCIONARIO;
          }
          else {
            return '<strong style="color:green">'.MENSAJE_PAGO_CONFIRMADO_FUNCIONARIO.'</strong><br>
            <input type="checkbox" name="check_cancelar_pago" id="check_cancelar_pago" value="1"><b style="color:red"> Cancelar pago</b> ';
          }
        }
        else if($pago && !UsuarioSesion::usuarioMesaDeEntrada() && !$CI->session->userdata('id_usuario_ciudadano') && $CI->uri->segment(1) != 'backend'){
            return '<strong style="color:green">'.MENSAJE_PAGO_CONFIRMADO_FUNCIONARIO.'</strong>';
        }
        else if ($CI->uri->segment(3) == 'ver_etapa'){
          if(!$pago){
            return '<strong style="color:black">'.MENSAJE_PAGO_NO_CONFIRMADO_FUNCIONARIO.'</strong>';
          }
          else {
            return '<strong style="color:green">'.MENSAJE_PAGO_CONFIRMADO_FUNCIONARIO.'</strong>';
          }
        }
        else {
        if($etapa_id) {
            $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
            $regla=new Regla($this->valor_default);
            $valor_default=$regla->getExpresionParaOutput($etapa->id);
        }
        else {
          $valor_default=$this->valor_default;
        }

        foreach($this->Formulario->Proceso->Acciones as $accion) {
          if($accion->id == $valor_default) {
            $pasarela = $accion->extra;
          }
        }

        if(!isset($pasarela->metodo)) {
          $metodo_pasarela = 'antel';
        }
        else {
          $metodo_pasarela = $pasarela->metodo;
        }

        switch($metodo_pasarela) {
        case 'generico':
        //*************************************************************************
        //******************************PASARELA GENERICA**************************
        //*************************************************************************


          $pasarela_generica = Doctrine_Query::create()
            ->from('PasarelaPagoGenerica pg')
            ->where('pg.id = ?', $pasarela->pasarela_pago_generica_id)
            ->fetchOne();

          $id_sol = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $pasarela_generica->variable_idsol), $etapa_id);


          if(strtolower($pasarela_generica->metodo_http) == 'post') {
            //*************************************************************************
            //******************************PASARELA GENERICA POST*********************
            //*************************************************************************

            if(!filter_var($pasarela_generica->url_redireccion, FILTER_VALIDATE_URL)) {
              $url_redireccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $pasarela_generica->variable_redireccion), $etapa_id);
              $url_redireccion = $url_redireccion->valor;
            }
            else {
              $url_redireccion = $pasarela_generica->url_redireccion;
            }

            $variables_get_ticket = [];
            preg_match_all ('/@@(\w+)((->\w+|\[\w+\])*)/', $pasarela_generica->url_ticket, $variables_get_ticket);

            $url_ticket_completa = $pasarela_generica->url_ticket;

            foreach($variables_get_ticket[0] as $variable_get_ticket) {
              $valor_variable = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $variable_get_ticket), $etapa_id);
              if($valor_variable) {
                $url_ticket_completa = str_replace($variable_get_ticket, $valor_variable->valor, $url_ticket_completa);
              }
            }

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago_generico', $etapa_id);
            if ($dato) {
              if ($dato->valor != '0') {
                if($dato->valor == '-1') {
                  // -- En caso de TIMEOUT  en el ws de consulta de estado
                  $display = '<div class="no-margin-box">';
                  $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
                  $display .= '<div>';
                  $display .= '<div>';

                  $display .= '<div class="mensaje_estado_pago_generico"><div class="dialogo validacion-warning"><h3 class="dialogos_titulo small-loader">'. MENSAJE_PAGO_CONSULTA_TITULO .'</h3><div id="floatingCirclesG" class="small-loader"><div class="f_circleG" id="frotateG_01"></div><div class="f_circleG" id="frotateG_02"></div><div class="f_circleG" id="frotateG_03"></div><div class="f_circleG" id="frotateG_04"></div><div class="f_circleG" id="frotateG_05"></div><div class="f_circleG" id="frotateG_06"></div><div class="f_circleG" id="frotateG_07"></div><div class="f_circleG" id="frotateG_08"></div></div><div>'. MENSAJE_PAGO_CONSULTA .'</div></div></div>';
                  $display .= '<input type="hidden" id="verificar_estado_pago_generico" />';
                  $display .= '<input type="hidden" id="id_solicitud_pago_generico" value="'.$id_sol->valor.'" />';
                  $display .= '<input type="hidden" id="etapa_id" value="'.$etapa_id.'" />';
                  $display .= '<input type="hidden" id="pasarela_generica_id" value="'.$pasarela_generica->id.'" />';
                  $display .= '<input name="MsgPago" value="'. $this->etiqueta .'" type="hidden" />';
                  $display .= '<input id="var_idsol" value="'. $pasarela_generica->variable_idsol .'" type="hidden" />';

                  $display .= '</div>';
                  $display .= '</div>';
                  $display .= '</div>';
                  $display .= '</div>';
                }
                else {
                  //la variable codigo_estado_solicitud_pago_generico está en 1 no se debe verificar_estado_pago_generico
                  //por lo tanto se comenta el input hidden
                  $error_servicio_pagos = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("ws_error", $etapa_id);

                  $display = '<div class="no-margin-box">';
                  $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';

                  if(!$error_servicio_pagos) {
                    $display .= '<div class="text-center">';
                  }
                  else {
                    $display .= '<div>';
                  }

                  $display .= '<div data-action="'. $url_redireccion .'" id="form_pasarela_pago_generica" method="post">';
                  $display .= '<div class="mensaje_estado_pago_generico hidden"><div class="dialogo validacion-warning"><h3 class="dialogos_titulo small-loader">'. MENSAJE_PAGO_CONSULTA_TITULO .'</h3><div id="floatingCirclesG" class="small-loader"><div class="f_circleG" id="frotateG_01"></div><div class="f_circleG" id="frotateG_02"></div><div class="f_circleG" id="frotateG_03"></div><div class="f_circleG" id="frotateG_04"></div><div class="f_circleG" id="frotateG_05"></div><div class="f_circleG" id="frotateG_06"></div><div class="f_circleG" id="frotateG_07"></div><div class="f_circleG" id="frotateG_08"></div></div><div>'. MENSAJE_PAGO_CONSULTA .'</div></div></div>';
                  //$display .= '<div class="no-margin-box">';
                  //$display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
                  //$display .= '<div class="text-center">';

                  if(!$error_servicio_pagos) {
                    $display .= '<p id="etiqueta_pago">'. $this->etiqueta .'</p>';
                  }

                  if ($modo == 'visualizacion') {
                    $display .= '<div class="btn btn-link disabled">Continuar</div>';
                  }
                  elseif($error_servicio_pagos) {
                    $display .= '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">';
                    $display .= MENSAJE_PAGO_ERROR_TITULO;
                    $display .= '</h3><div>';
                    $display .= MENSAJE_PAGO_ERROR;
                    $display .= '</div></div>';
                  }
                  else {
                    $display .= '<input type="button" class="btn btn-primary" value="Realizar pago" id="form_pago_submit_generico" />';
                    $display .= '<input type="submit" value="Submit" class="btn-link hidden" id="form_pago_submit_generico_real" />';
                  }

                  $display .= '<br /><br />';
                  $display .= '<input id="'.$this->id.'" type="text" name="' . $this->nombre . '" value="'.$pasarela_generica->id.'" class="hidden" />';
                  // $display .= '<input type="hidden" id="verificar_estado_pago_generico" />';
                  $display .= '<input type="hidden" id="id_solicitud_pago_generico" value="'.$id_sol->valor.'" />';
                  $display .= '<input type="hidden" id="etapa_id" value="'.$etapa_id.'" />';
                  $display .= '<input type="hidden" id="pasarela_generica_id" value="'.$pasarela_generica->id.'" />';
                  $display .= '<input name="MsgPagoGenerico" value="'. $this->etiqueta .'" type="hidden" />';
                  $display .= '<input id="var_idsol" value="'. $pasarela_generica->variable_idsol .'" type="hidden" />';

                  $variables_post = json_decode($pasarela_generica->variables_post);
                  foreach($variables_post as $variable) {
                    $valor_variable = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $variable->valor), $etapa_id);
                    if($valor_variable) {
                      $display .= '<input type="hidden" name="'.$variable->nombre.'" value="'.$valor_variable->valor.'" />';
                    }
                    else {
                      $display .= '<input type="hidden" name="'.$variable->nombre.'" value="'.$variable->valor.'" />';
                    }
                  }

                  if($pasarela_generica->url_ticket) {
                    if($pasarela_generica->ticket_metodo == 'get' || !$pasarela_generica->ticket_metodo) {
                      $display .= '<div class="dialogo validacion-info text-left imprimir_ticket_generico hidden"><h3 class="dialogos_titulo">Impimir ticket</h3><div class="alert alert-error">';
                      $display .= '' . $pasarela_generica->mensaje_reimpresion_ticket . ' <a href="'.$url_ticket_completa.'" target="_blank" class="">Imprimir ticket de pago</a>';
                      $display .= '</div></div>';
                    }
                    else {
                      $display .= '<div data-action="'.$url_ticket_completa.'" class="dialogo validacion-info text-left imprimir_ticket_generico imprimir_ticket_generico_post hidden"><h3 class="dialogos_titulo">Impimir ticket</h3><div class="alert alert-error">';
                      $display .= '' . $pasarela_generica->mensaje_reimpresion_ticket . ' <a href="#" class="imprimir_ticket_generico_post_button" >Imprimir ticket de pago</a>';

                      $variables_ticket = json_decode($pasarela_generica->ticket_variables);
                      foreach($variables_ticket as $variable) {
                        $valor_variable = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $variable->valor), $etapa_id);
                        if($valor_variable) {
                          $display .= '<input type="hidden" name="'.$variable->nombre.'__ticket" value="'.$valor_variable->valor.'" />';
                        }
                        else {
                          $display .= '<input type="hidden" name="'.$variable->nombre.'__ticket" value="'.$variable->valor.'" />';
                        }
                      }

                      $display .= '</div></div>';
                    }
                  }

                  $display .= '</div>';
                  $display .= '</div>';
                  $display .= '</div>';
                  $display .= '</div>';

                }
              }
              else {
                //la variable codigo_estado_solicitud_pago_generico está en 0 SE debe  verificar_estado_pago_generico
                $display = '<div data-action="'. $url_redireccion .'" id="form_pasarela_pago_generica" method="post">';
                $display .= '<div class="mensaje_estado_pago_generico"><div class="dialogo validacion-warning"><h3 class="dialogos_titulo small-loader">'. MENSAJE_PAGO_CONSULTA_TITULO .'</h3><div id="floatingCirclesG" class="small-loader"><div class="f_circleG" id="frotateG_01"></div><div class="f_circleG" id="frotateG_02"></div><div class="f_circleG" id="frotateG_03"></div><div class="f_circleG" id="frotateG_04"></div><div class="f_circleG" id="frotateG_05"></div><div class="f_circleG" id="frotateG_06"></div><div class="f_circleG" id="frotateG_07"></div><div class="f_circleG" id="frotateG_08"></div></div><div>'. MENSAJE_PAGO_CONSULTA .'</div></div></div>';
                $display .= '<div class="no-margin-box">';
                $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
                $display .= '<div class="text-center">';
                $display .= '<p id="form_pago_etiqueta">'. $this->etiqueta .'</p>';
                $display .= '<input type="button" class="btn btn-primary" value="Realizar pago" id="form_pago_submit_generico" />';
                $display .= '<input type="submit" value="Submit" class="btn-link hidden" id="form_pago_submit_generico_real" />';
                $display .= '<br /><br />';
                $display .= '<input id="'.$this->id.'" type="text" name="' . $this->nombre . '" value="'.$pasarela_generica->id.'" class="hidden" />';
                $display .= '<input type="hidden" id="verificar_estado_pago_generico" />';
                $display .= '<input type="hidden" id="id_solicitud_pago_generico" value="'.$id_sol->valor.'" />';
                $display .= '<input type="hidden" id="etapa_id" value="'.$etapa_id.'" />';
                $display .= '<input type="hidden" id="pasarela_generica_id" value="'.$pasarela_generica->id.'" />';
                $display .= '<input name="MsgPagoGenerico" value="'. $this->etiqueta .'" type="hidden" />';
                $display .= '<input id="var_idsol" value="'. $pasarela_generica->variable_idsol .'" type="hidden" />';

                $variables_post = json_decode($pasarela_generica->variables_post);
                foreach($variables_post as $variable) {
                  $valor_variable = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $variable->valor), $etapa_id);
                  if($valor_variable) {
                    $display .= '<input type="hidden" name="'.$variable->nombre.'" value="'.$valor_variable->valor.'" />';
                  }
                  else {
                    $display .= '<input type="hidden" name="'.$variable->nombre.'" value="'.$variable->valor.'" />';
                  }
                }

                if($pasarela_generica->url_ticket) {
                  if($pasarela_generica->ticket_metodo == 'get' || !$pasarela_generica->ticket_metodo) {
                    $display .= '<div class="dialogo validacion-info text-left imprimir_ticket_generico hidden"><h3 class="dialogos_titulo">Impimir ticket</h3><div class="alert alert-error">';
                    $display .= '' . $pasarela_generica->mensaje_reimpresion_ticket . ' <a href="'.$url_ticket_completa.'" target="_blank" class="">Imprimir ticket de pago</a>';
                    $display .= '</div></div>';
                  }
                  else {
                    $display .= '<div data-action="'.$url_ticket_completa.'" class="dialogo validacion-info text-left imprimir_ticket_generico imprimir_ticket_generico_post hidden"><h3 class="dialogos_titulo">Impimir ticket</h3><div class="alert alert-error">';
                    $display .= '' . $pasarela_generica->mensaje_reimpresion_ticket . ' <a href="#" class="imprimir_ticket_generico_post_button" >Imprimir ticket de pago</a>';

                    $variables_ticket = json_decode($pasarela_generica->ticket_variables);
                    foreach($variables_ticket as $variable) {
                      $valor_variable = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $variable->valor), $etapa_id);
                      if($valor_variable) {
                        $display .= '<input type="hidden" name="'.$variable->nombre.'__ticket" value="'.$valor_variable->valor.'" />';
                      }
                      else {
                        $display .= '<input type="hidden" name="'.$variable->nombre.'__ticket" value="'.$variable->valor.'" />';
                      }
                    }

                    $display .= '</div></div>';
                  }
                }

                $display .= '</div>';
                $display .= '</div>';
                $display .= '</div>';
                $display .= '</div>';
              }
            }
            else {
              //no existe la variable codigo_estado_solicitud_pago_generico se debe verificar_estado_pago_generico
              $display = '<div class="mensaje_estado_pago_generico hidden"><div class="dialogo validacion-warning"><h3 class="dialogos_titulo small-loader">'. MENSAJE_PAGO_CONSULTA_TITULO .'</h3><div id="floatingCirclesG" class="small-loader"><div class="f_circleG" id="frotateG_01"></div><div class="f_circleG" id="frotateG_02"></div><div class="f_circleG" id="frotateG_03"></div><div class="f_circleG" id="frotateG_04"></div><div class="f_circleG" id="frotateG_05"></div><div class="f_circleG" id="frotateG_06"></div><div class="f_circleG" id="frotateG_07"></div><div class="f_circleG" id="frotateG_08"></div></div><div>'. MENSAJE_PAGO_CONSULTA .'</div></div></div>';
              $display .= '<div data-action="'. $url_redireccion .'" id="form_pasarela_pago_generica" method="post">';
              $display .= '<div class="no-margin-box">';
              $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
              $display .= '<div class="text-center">';
              $display .= '<p id="form_pago_etiqueta">'. $this->etiqueta .'</p>';
              $display .= '<input type="button" class="btn btn-primary" value="Realizar pago" id="form_pago_submit_generico" />';
              $display .= '<input type="submit" value="Submit" class="btn-link hidden" id="form_pago_submit_generico_real" />';
              $display .= '<br /><br />';
              $display .= '<input id="'.$this->id.'" type="text" name="' . $this->nombre . '" value="'.$pasarela_generica->id.'" class="hidden" />';
              $display .= '<input type="hidden" id="verificar_estado_pago_generico" />';
              $display .= '<input type="hidden" id="id_solicitud_pago_generico" value="'.$id_sol->valor.'" />';
              $display .= '<input type="hidden" id="etapa_id" value="'.$etapa_id.'" />';
              $display .= '<input type="hidden" id="pasarela_generica_id" value="'.$pasarela_generica->id.'" />';
              $display .= '<input name="MsgPagoGenerico" value="'. $this->etiqueta .'" type="hidden" />';
              $display .= '<input id="var_idsol" value="'. $pasarela_generica->variable_idsol .'" type="hidden" />';

              $variables_post = json_decode($pasarela_generica->variables_post);
              foreach($variables_post as $variable) {
                $valor_variable = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $variable->valor), $etapa_id);
                if($valor_variable) {
                  $regla=new Regla($valor_variable->valor);
                  $valor_variable_limpia = $regla->getExpresionParaOutput($etapa_id);

                  $display .= '<input type="hidden" name="'.$variable->nombre.'" value="'.$valor_variable_limpia.'" />';
                }
                else {
                  $regla=new Regla($variable->valor);
                  $valor_variable_limpia = $regla->getExpresionParaOutput($etapa_id);

                  $display .= '<input type="hidden" name="'.$variable->nombre.'" value="'.$valor_variable_limpia.'" />';
                }
              }

              if($pasarela_generica->url_ticket) {
                if($pasarela_generica->ticket_metodo == 'get' || !$pasarela_generica->ticket_metodo) {
                  $display .= '<div class="dialogo validacion-info text-left imprimir_ticket_generico hidden"><h3 class="dialogos_titulo">Impimir ticket</h3><div class="alert alert-error">';
                  $display .= '' . $pasarela_generica->mensaje_reimpresion_ticket . ' <a href="'.$url_ticket_completa.'" target="_blank" class="">Imprimir ticket de pago</a>';
                  $display .= '</div></div>';
                }
                else {
                  $display .= '<div data-action="'.$url_ticket_completa.'" class="dialogo validacion-info text-left imprimir_ticket_generico imprimir_ticket_generico_post hidden"><h3 class="dialogos_titulo">Impimir ticket</h3><div class="alert alert-error">';
                  $display .= '' . $pasarela_generica->mensaje_reimpresion_ticket . ' <a href="#" class="imprimir_ticket_generico_post_button" >Imprimir ticket de pago</a>';

                  $variables_ticket = json_decode($pasarela_generica->ticket_variables);
                  foreach($variables_ticket as $variable) {
                    $valor_variable = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $variable->valor), $etapa_id);
                    if($valor_variable) {
                      $display .= '<input type="hidden" name="'.$variable->nombre.'__ticket" value="'.$valor_variable->valor.'" />';
                    }
                    else {
                      $display .= '<input type="hidden" name="'.$variable->nombre.'__ticket" value="'.$variable->valor.'" />';
                    }
                  }

                  $display .= '</div></div>';
                }
              }

              $display .= '</div>';
              $display .= '</div>';
              $display .= '</div>';
              $display .= '</div>';
            }
          }
          else {
            //************************************************************************
            //******************************PASARELA GENERICA GET*********************
            //************************************************************************

            $variables_get_redireccion = [];
            preg_match_all ('/@@(\w+)((->\w+|\[\w+\])*)/', $pasarela_generica->url_redireccion, $variables_get_redireccion);

            $url_redireccion_completa = $pasarela_generica->url_redireccion;

            foreach($variables_get_redireccion[0] as $variable_get_redireccion) {
              $valor_variable = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $variable_get_redireccion), $etapa_id);
              if($valor_variable) {
                $url_redireccion_completa = str_replace($variable_get_redireccion, $valor_variable->valor, $url_redireccion_completa);
              }
            }

            $variables_get_ticket = [];
            preg_match_all ('/@@(\w+)((->\w+|\[\w+\])*)/', $pasarela_generica->url_ticket, $variables_get_ticket);

            $url_ticket_completa = $pasarela_generica->url_ticket;

            foreach($variables_get_ticket[0] as $variable_get_ticket) {
              $valor_variable = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $variable_get_ticket), $etapa_id);
              if($valor_variable) {
                $url_ticket_completa = str_replace($variable_get_ticket, $valor_variable->valor, $url_ticket_completa);
              }
            }

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago_generico', $etapa_id);
            if ($dato) {
              if ($dato->valor != '0') {
                if($dato->valor == '-1') {
                  // -- En caso de TIMEOUT o ERROR al obtener el ID de solicitud o token
                  $display = '<div class="mensaje_estado_pago_generico"><div class="dialogo validacion-warning"><h3 class="dialogos_titulo small-loader">'. MENSAJE_PAGO_CONSULTA_TITULO .'</h3><div id="floatingCirclesG" class="small-loader"><div class="f_circleG" id="frotateG_01"></div><div class="f_circleG" id="frotateG_02"></div><div class="f_circleG" id="frotateG_03"></div><div class="f_circleG" id="frotateG_04"></div><div class="f_circleG" id="frotateG_05"></div><div class="f_circleG" id="frotateG_06"></div><div class="f_circleG" id="frotateG_07"></div><div class="f_circleG" id="frotateG_08"></div></div><div>'. MENSAJE_PAGO_CONSULTA .'</div></div></div>';
                  $display .= '<div class="no-margin-box">';
                  $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
                  $display .= '<div>';
                  $display .= '<div>';

                  //$display .= '<div class="mensaje_estado_pago_generico"><div class="dialogo validacion-warning"><h3 class="dialogos_titulo small-loader">'. MENSAJE_PAGO_CONSULTA_TITULO .'</h3><div id="floatingCirclesG" class="small-loader"><div class="f_circleG" id="frotateG_01"></div><div class="f_circleG" id="frotateG_02"></div><div class="f_circleG" id="frotateG_03"></div><div class="f_circleG" id="frotateG_04"></div><div class="f_circleG" id="frotateG_05"></div><div class="f_circleG" id="frotateG_06"></div><div class="f_circleG" id="frotateG_07"></div><div class="f_circleG" id="frotateG_08"></div></div><div>'. MENSAJE_PAGO_CONSULTA .'</div></div></div>';
                  $display .= '<input type="hidden" id="verificar_estado_pago_generico" />';
                  $display .= '<input name="IdEtapa" value="'. $etapa_id .'" type="hidden" />';
                  $display .= '<input name="IdSol" value="'. $id_sol->valor .'" type="hidden" />';
                  $display .= '<input name="MsgPagoGenerico" value="'. $this->etiqueta .'" type="hidden" />';
                  $display .= '<input type="hidden" id="id_solicitud_pago_generico" value="'.$id_sol->valor.'" />';
                  $display .= '<input type="hidden" id="etapa_id" value="'.$etapa_id.'" />';
                  $display .= '<input type="hidden" id="pasarela_generica_id" value="'.$pasarela_generica->id.'" />';

                  $display .= '</div>';
                  $display .= '</div>';
                  $display .= '</div>';
                  $display .= '</div>';
                }
                else {
                  //la variable codigo_estado_solicitud_pago_generico esta en 1 NO se verifica el estado del pago
                  $error_servicio_pagos = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("ws_error", $etapa_id);

                  $display = '<div class="no-margin-box">';
                  $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';

                  if(!$error_servicio_pagos) {
                    $display .= '<div class="text-center">';
                  }
                  else {
                    $display .= '<div id="form_pasarela_pago_generica">';
                  }

                  $display .= '<div class="mensaje_estado_pago_generico hidden"><div class="dialogo validacion-warning"><h3 class="dialogos_titulo small-loader">'. MENSAJE_PAGO_CONSULTA_TITULO .'</h3><div id="floatingCirclesG" class="small-loader"><div class="f_circleG" id="frotateG_01"></div><div class="f_circleG" id="frotateG_02"></div><div class="f_circleG" id="frotateG_03"></div><div class="f_circleG" id="frotateG_04"></div><div class="f_circleG" id="frotateG_05"></div><div class="f_circleG" id="frotateG_06"></div><div class="f_circleG" id="frotateG_07"></div><div class="f_circleG" id="frotateG_08"></div></div><div>'. MENSAJE_PAGO_CONSULTA .'</div></div></div>';
                  //$display .= '<div class="no-margin-box">';
                  //$display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
                  //$display .= '<div class="text-center">';

                  if(!$error_servicio_pagos) {
                    $display .= '<p id="form_pago_etiqueta">'. $this->etiqueta .'</p>';
                  }

                  if ($modo == 'visualizacion') {
                    $display .= '<div class="btn btn-link disabled">Continuar</div>';
                  }
                  elseif($error_servicio_pagos) {
                    $display .= '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">';
                    $display .= MENSAJE_PAGO_ERROR_TITULO;
                    $display .= '</h3><div>';
                    $display .= MENSAJE_PAGO_ERROR;
                    $display .= '</div></div>';
                  }
                  else {
                    $display .= '<a href="'.$url_redireccion_completa.'" class="btn btn-primary" id="form_pago_submit_generico">Realizar pago</a>';
                    $display .= '<a href="'.$url_redireccion_completa.'" class="hidden btn btn-primary" id="form_pago_submit_real_generico">Realizar pago</a>';
                  }

                  $display .= '<br /><br />';
                  $display .= '<input id="'.$this->id.'" type="text" name="' . $this->nombre . '" value="'.$pasarela_generica->id.'" class="hidden" />';

                  if($pasarela_generica->url_ticket) {
                    if($pasarela_generica->ticket_metodo == 'get' || !$pasarela_generica->ticket_metodo) {
                      $display .= '<div class="dialogo validacion-info text-left imprimir_ticket_generico hidden"><h3 class="dialogos_titulo">Impimir ticket</h3><div class="alert alert-error">';
                      $display .= '' . $pasarela_generica->mensaje_reimpresion_ticket . ' <a href="'.$url_ticket_completa.'" target="_blank" class="">Imprimir ticket de pago</a>';
                      $display .= '</div></div>';
                    }
                    else {
                      $display .= '<div data-action="'.$url_ticket_completa.'" class="dialogo validacion-info text-left imprimir_ticket_generico imprimir_ticket_generico_post hidden"><h3 class="dialogos_titulo">Impimir ticket</h3><div class="alert alert-error">';
                      $display .= '' . $pasarela_generica->mensaje_reimpresion_ticket . ' <a href="#" class="imprimir_ticket_generico_post_button" >Imprimir ticket de pago</a>';

                      $variables_ticket = json_decode($pasarela_generica->ticket_variables);
                      foreach($variables_ticket as $variable) {
                        $valor_variable = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $variable->valor), $etapa_id);
                        if($valor_variable) {
                          $display .= '<input type="hidden" name="'.$variable->nombre.'__ticket" value="'.$valor_variable->valor.'" />';
                        }
                        else {
                          $display .= '<input type="hidden" name="'.$variable->nombre.'__ticket" value="'.$variable->valor.'" />';
                        }
                      }

                      $display .= '</div></div>';
                    }
                  }

                  $display .= '</div>';
                  $display .= '</div>';
                  $display .= '</div>';
                  // $display .= '<input type="hidden" id="verificar_estado_pago_generico" />';
                  $display .= '<input type="hidden" id="id_solicitud_pago_generico" value="'.$id_sol->valor.'" />';
                  $display .= '<input type="hidden" id="etapa_id" value="'.$etapa_id.'" />';
                  $display .= '<input type="hidden" id="pasarela_generica_id" value="'.$pasarela_generica->id.'" />';
                  $display .= '<input name="MsgPagoGenerico" value="'. $this->etiqueta .'" type="hidden" />';
                  $display .= '<input id="var_idsol" value="'. $pasarela_generica->variable_idsol .'" type="hidden" />';
                }
              }
              else {
                //la variable codigo_estado_solicitud_pago_generico esta en 0 se verifica el estado del pago.
                $display = '<div class="mensaje_estado_pago_generico"><div class="dialogo validacion-warning"><h3 class="dialogos_titulo small-loader">'. MENSAJE_PAGO_CONSULTA_TITULO .'</h3><div id="floatingCirclesG" class="small-loader"><div class="f_circleG" id="frotateG_01"></div><div class="f_circleG" id="frotateG_02"></div><div class="f_circleG" id="frotateG_03"></div><div class="f_circleG" id="frotateG_04"></div><div class="f_circleG" id="frotateG_05"></div><div class="f_circleG" id="frotateG_06"></div><div class="f_circleG" id="frotateG_07"></div><div class="f_circleG" id="frotateG_08"></div></div><div>'. MENSAJE_PAGO_CONSULTA .'</div></div></div>';
                $display .= '<div class="no-margin-box">';
                $display .= '<div class="controls hidden cuerpo_componente_pago_generico" data-fieldset="'.$this->fieldset.'">';
                $display .= '<div class="text-center">';
                $display .= '<p id="form_pago_etiqueta">'. $this->etiqueta .'</p>';
                $display .= '<a href="'.$url_redireccion_completa.'" class="btn btn-primary" id="form_pago_submit_generico">Realizar pago</a>';
                $display .= '<a href="'.$url_redireccion_completa.'" class="hidden btn btn-primary" id="form_pago_submit_real_generico">Realizar pago</a>';
                $display .= '<br /><br />';
                $display .= '<input id="'.$this->id.'" type="text" name="' . $this->nombre . '" value="'.$pasarela_generica->id.'" class="hidden" />';

                if($pasarela_generica->url_ticket) {
                  if($pasarela_generica->ticket_metodo == 'get' || !$pasarela_generica->ticket_metodo) {
                    $display .= '<div class="dialogo validacion-info text-left imprimir_ticket_generico hidden"><h3 class="dialogos_titulo">Impimir ticket</h3><div class="alert alert-error">';
                    $display .= '' . $pasarela_generica->mensaje_reimpresion_ticket . ' <a href="'.$url_ticket_completa.'" target="_blank" class="">Imprimir ticket de pago</a>';
                    $display .= '</div></div>';
                  }
                  else {
                    $display .= '<div data-action="'.$url_ticket_completa.'" class="dialogo validacion-info text-left imprimir_ticket_generico imprimir_ticket_generico_post hidden"><h3 class="dialogos_titulo">Impimir ticket</h3><div class="alert alert-error">';
                    $display .= '' . $pasarela_generica->mensaje_reimpresion_ticket . ' <a href="#" class="imprimir_ticket_generico_post_button" >Imprimir ticket de pago</a>';

                    $variables_ticket = json_decode($pasarela_generica->ticket_variables);
                    foreach($variables_ticket as $variable) {
                      $valor_variable = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $variable->valor), $etapa_id);
                      if($valor_variable) {
                        $display .= '<input type="hidden" name="'.$variable->nombre.'__ticket" value="'.$valor_variable->valor.'" />';
                      }
                      else {
                        $display .= '<input type="hidden" name="'.$variable->nombre.'__ticket" value="'.$variable->valor.'" />';
                      }
                    }

                    $display .= '</div></div>';
                  }
                }

                $display .= '</div>';
                $display .= '</div>';
                $display .= '</div>';
                $display .= '<input type="hidden" id="verificar_estado_pago_generico" />';
                $display .= '<input type="hidden" id="id_solicitud_pago_generico" value="'.$id_sol->valor.'" />';
                $display .= '<input type="hidden" id="etapa_id" value="'.$etapa_id.'" />';
                $display .= '<input type="hidden" id="pasarela_generica_id" value="'.$pasarela_generica->id.'" />';
                $display .= '<input name="MsgPagoGenerico" value="'. $this->etiqueta .'" type="hidden" />';
                $display .= '<input id="var_idsol" value="'. $pasarela_generica->variable_idsol .'" type="hidden" />';
              }
            }
          }

          //para los dos casos se genera el boton de pago que luego JS en el front_end extendido le da logica
          $display .= '<span class="vista_componente_pago_generico hidden">';
          $display .= '<div class="no-margin-box">';
          $display .= '<div class="text-center">';
          $display .= '<p id="form_pago_etiqueta_">'. $this->etiqueta .'</p>';
          $display .= '<a href="#" class="btn btn-primary" id="form_pago_submit_generico_">Realizar pago</a>';
          $display .= '</div>';
          $display .= '</div>';
          $display .= '</span>';

          if(isset($etapa)) {
            preg_match('/('. $etapa->id .')\/([0-9]*)/', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $match);

            if($match) {
              $secuencia = (int)$match[2];
            }
            else {
              $secuencia = 0;
            }

            $url_vuelta = site_url('etapas/ejecutar/' . $etapa->id . '/' . $secuencia);

            $CI = &get_instance();
            $CI->session->set_userdata('simple_bpm_gwp_redirect',  $url_vuelta);
          }

          return $display;

          break;
        default:
          //*************************************************************************
          //******************************PASARELA ITC*******************************
          //*************************************************************************

          // ID tramite
          preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $pasarela->id_tramite, $variable_encontrada);
          if($variable_encontrada) {
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa_id);
            $id_tramite = $dato->valor;
          }
          else {
            $id_tramite = $pasarela->id_tramite;
          }

          //  tasa 1
          preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $pasarela->tasa_1, $variable_encontrada);
          if($variable_encontrada) {
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa_id);
            $tasa_1 = $dato->valor;
          }
          else {
            $tasa_1 = $pasarela->tasa_1;
          }

          //  tasa 2
          preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $pasarela->tasa_2, $variable_encontrada);
          if($variable_encontrada) {
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa_id);
            $tasa_2 = $dato->valor;
          }
          else {
            $tasa_2 = $pasarela->tasa_2;
          }

          //  tasa 3
          preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $pasarela->tasa_3, $variable_encontrada);
          if($variable_encontrada) {
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa_id);
            $tasa_3 = $dato->valor;
          }
          else {
            $tasa_3 = $pasarela->tasa_3;
          }

          // vencimiento
          preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $pasarela->vencimiento, $variable_encontrada);
          if($variable_encontrada) {
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa_id);
            $vencimiento = $dato->valor;
          }
          else {
            $vencimiento = $pasarela->vencimiento;
          }

          $fecha = str_replace('/', '', $vencimiento.'0000');
          $fecha = strtotime($fecha);
          $fecha_vencimiento = date("YmdHi", $fecha);

          //  codigos desglose
          preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $pasarela->codigos_desglose, $variable_encontrada);
          if($variable_encontrada) {
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa_id);
            $codigos_desglose = $dato->valor;
          }
          else {
            $codigos_desglose = $pasarela->codigos_desglose;
          }

          //  montos desglose
          preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $pasarela->montos_desglose, $variable_encontrada);
          if($variable_encontrada) {
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa_id);
            $montos_desglose = $dato->valor;
          }
          else {
            $montos_desglose = $pasarela->montos_desglose;
          }

          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago', $etapa_id);
          if ($dato) {
            if ($dato->valor != '0') {
              // -- En caso de TIMEOUT el ws de consulta de estado
              if($dato->valor == '-1') {
                $id_sol =  Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_sol_pasarela_pagos', $etapa_id);

                $display = '<div class="no-margin-box">';
                $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
                $display .= '<div>';
                $display .= '<div>';

                $display .= '<div class="mensaje_estado_pago"><div class="dialogo validacion-warning"><h3 class="dialogos_titulo small-loader">'. MENSAJE_PAGO_CONSULTA_TITULO .'</h3><div id="floatingCirclesG" class="small-loader"><div class="f_circleG" id="frotateG_01"></div><div class="f_circleG" id="frotateG_02"></div><div class="f_circleG" id="frotateG_03"></div><div class="f_circleG" id="frotateG_04"></div><div class="f_circleG" id="frotateG_05"></div><div class="f_circleG" id="frotateG_06"></div><div class="f_circleG" id="frotateG_07"></div><div class="f_circleG" id="frotateG_08"></div></div><div>'. MENSAJE_PAGO_CONSULTA .'</div></div></div>';
                $display .= '<input type="hidden" id="verificar_estado_pago" />';
                $display .= '<input name="IdEtapa" value="'. $etapa_id .'" type="hidden" />';
                $display .= '<input name="IdSol" value="'. $id_sol->valor .'" type="hidden" />';
                $display .= '<input name="MsgPago" value="'. $this->etiqueta .'" type="hidden" />';

                $display .= '</div>';
                $display .= '</div>';
                $display .= '</div>';
                $display .= '</div>';
              }
              else {
                //la variabel codigo_estado_solicitud_pago esta en 1 NO SE verifica el esstado del pago
                $error_servicio_pagos = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("ws_error", $etapa_id);

                $id_sol =  Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_sol_pasarela_pagos', $etapa_id);
                $token =  Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('token_pasarela_pagos', $etapa_id);

                $display = '<div class="no-margin-box">';
                $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';

                if(!$error_servicio_pagos) {
                  $display .= '<div class="text-center">';
                }
                else {
                  $display .= '<div>';
                }

                $display .= '<div data-action="'. POST_PASARELA_PAGO .'" id="form_pasarela_pago">';

                $display .= '<input name="Token" value="'. (isset($token->valor) ? $token->valor : '') .'" type="hidden" />';

                if(!$error_servicio_pagos) {
                  $display .= '<p>'. $this->etiqueta .'</p>';
                }

                if ($modo == 'visualizacion') {
                  $display .= '<div class="btn btn-link disabled">Continuar</div>';
                }
                elseif($error_servicio_pagos) {
                  $display .= '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">';
                  $display .= MENSAJE_PAGO_ERROR_TITULO;
                  $display .= '</h3><div>';
                  $display .= MENSAJE_PAGO_ERROR;
                  $display .= '</div></div>';
                }
                else {
                  $display .= '<input type="button" value="Realizar pago" class="btn btn-primary" id="form_pago_submit" />';
                  $display .= '<input type="submit" value="Submit" class="btn-link hidden" id="form_pago_submit_real" />';
                }

                $display .= '<input name="IdSol" value="'. $id_sol->valor .'" type="hidden" />';

                $display .= '</div>';
                $display .= '</div>';
                $display .= '</div>';
                $display .= '</div>';

                if(isset($etapa)) {
                  preg_match('/('. $etapa->id .')\/([0-9]*)/', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $match);

                  if($match) {
                    // $secuencia = (int)$match[2] + 1;
                    $secuencia = (int)$match[2];
                  }
                  else {
                    // $secuencia = 1;
                    $secuencia = 0;
                  }

                  $url_vuelta = site_url('etapas/ejecutar/' . $etapa->id . '/' . $secuencia);

                  $CI = &get_instance();
                  $CI->session->set_userdata('simple_bpm_gwp_redirect',  $url_vuelta);
                }
              }
            }
            else {
              //la variable codigo_estado_solicitud_pago esta en 0 SE verifica el estado del pago

              $id_sol =  Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_sol_pasarela_pagos', $etapa_id);

              $display = '<div class="no-margin-box" id="mensaje_estado_pasarela_pago_box">';
              $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
              $display .= '<div>';
              $display .= '<div class="mensaje_estado_pago"><div class="dialogo validacion-warning"><h3 class="dialogos_titulo small-loader">'. MENSAJE_PAGO_CONSULTA_TITULO .'</h3><div id="floatingCirclesG" class="small-loader"><div class="f_circleG" id="frotateG_01"></div><div class="f_circleG" id="frotateG_02"></div><div class="f_circleG" id="frotateG_03"></div><div class="f_circleG" id="frotateG_04"></div><div class="f_circleG" id="frotateG_05"></div><div class="f_circleG" id="frotateG_06"></div><div class="f_circleG" id="frotateG_07"></div><div class="f_circleG" id="frotateG_08"></div></div><div>'. MENSAJE_PAGO_CONSULTA .'</div></div></div>';

              $display .= '<input type="hidden" id="verificar_estado_pago" />';
              $display .= '<input name="IdSol" value="'. $id_sol->valor .'" type="hidden" />';
              $display .= '<input name="IdTramite" value="'. $etapa->tramite_id .'" type="hidden" />';
              $display .= '<input name="IdEtapa" value="'. $etapa_id .'" type="hidden" />';
              $display .= '<input name="MsgPago" value="'. $this->etiqueta .'" type="hidden" />';

              $display .= '</div>';
              $display .= '</div>';
              $display .= '</div>';
            }
          }
          else {
            //la variable codigo_estado_solicitud_pago no existe SE verifica el estado del pago

            $id_sol =  Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_sol_pasarela_pagos', $etapa_id);

            $display = '<div class="no-margin-box">';
            $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
            $display .= '<div>';
            $display .= '<div class="mensaje_estado_pago"><div class="dialogo validacion-warning"><h3 class="dialogos_titulo small-loader">'. MENSAJE_PAGO_CONSULTA_TITULO .'</h3><div id="floatingCirclesG" class="small-loader"><div class="f_circleG" id="frotateG_01"></div><div class="f_circleG" id="frotateG_02"></div><div class="f_circleG" id="frotateG_03"></div><div class="f_circleG" id="frotateG_04"></div><div class="f_circleG" id="frotateG_05"></div><div class="f_circleG" id="frotateG_06"></div><div class="f_circleG" id="frotateG_07"></div><div class="f_circleG" id="frotateG_08"></div></div><div>'. MENSAJE_PAGO_CONSULTA .'</div></div></div>';

            $display .= '<input type="hidden" id="verificar_estado_pago" />';
            $display .= '<input name="IdSol" value="'. $id_sol->valor .'" type="hidden" />';
            $display .= '<input name="IdTramite" value="'. $etapa->tramite_id .'" type="hidden" />';
            $display .= '<input name="IdEtapa" value="'. $etapa_id .'" type="hidden" />';
            $display .= '<input name="MsgPago" value="'. $this->etiqueta .'" type="hidden" />';

            $display .= '</div>';
            $display .= '</div>';
            $display .= '</div>';
          }

          return $display;
        }
      }

    }

    public function backendExtraValidate(){
      $CI=&get_instance();
      $CI->form_validation->set_rules('valor_default', 'Valor_default', 'required');
    }
}

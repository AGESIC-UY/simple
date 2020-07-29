<?php

require_once('campo.php');

class CampoAgendaMultipleSae extends Campo {

    public $requiere_nombre = true;
    public $requiere_datos = false;
    public $estatico = false;
    public $requiere_datos_agenda = true;

    function setTableDefinition() {
        parent::setTableDefinition();

        $this->hasColumn('readonly', 'bool', 0, array('default' => 0));
    }

    function setUp() {
        parent::setUp();
        $this->setTableName("campo");
    }

    protected function display($modo, $dato, $etapa_id) {
        if (!$etapa_id) {
            return '<p data-fieldset="' . $this->fieldset . '" agenda-multiple-campo="agenda_multiple_sae">' . $this->etiqueta . '</p>';
        }

        $regla = new Regla('@@' . $this->nombre);
        $datos_agenda_clonar = $regla->getExpresionParaOutput($etapa_id);
        if ($datos_agenda_clonar != '') {
            $this->clonarAgenda($etapa_id);
        }

        $display = "";
        preg_match('/(' . $etapa_id . ')\/([0-9]*)/', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $match);

        if (!$match) {
            $secuencia = 0;
            $paso_numero = 1;
        } else {
            $secuencia = (int) $match[2];
            $paso_numero = $secuencia + 2;
        }

        $dato_agenda = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->nombre . "_confirmada_reservas", $etapa_id);
        if ($dato_agenda) {
            $dato_agenda = $dato_agenda->valor;
        }

        if ($dato_agenda == 1) {
            $datos_comfirmacion = $dato_agenda;

            $dato_agenda_reservas = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->nombre . "_reservas", $etapa_id);

            $datos_recurso = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->nombre . "_recurso", $etapa_id);
            if ($datos_recurso) {
                $datos_recurso_acivo = $datos_recurso->valor;
            }
        } else if ($modo == 'visualizacion') {
            $display = '<div class="control-group">';
            $display .= '<div data-fieldset="' . $this->fieldset . '" style="margin: auto;">';
            $display .= '<p class="dialogo validacion-warning" id="titulo_reserva_no_confirmada">
                          Reserva no realizada </p>';
            $display .= '</div>';
            $display .= '</div>';
            return $display;
        }

        $regla = new Regla($this->extra->url_base);
        $url_base_con_variables = $regla->getExpresionParaOutput($etapa_id);

        $regla = new Regla($this->extra->token);
        $token_con_variables = $regla->getExpresionParaOutput($etapa_id);

        $regla = new Regla($this->extra->id_empresa);
        $id_empresa_con_variables = $regla->getExpresionParaOutput($etapa_id);

        $regla = new Regla($this->extra->id_agenda);
        $id_agenda_con_variables = $regla->getExpresionParaOutput($etapa_id);
        if ($modo != 'visualizacion') {
            $datos_token_reserva = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->nombre . "_token_reservas", $etapa_id);

            if ($datos_token_reserva) {

                $datos_recurso = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->nombre . "_recurso", $etapa_id);
                if ($datos_recurso) {
                    $datos_recurso_valor = $datos_recurso->valor;
                }
                try {

                    $datos_api = array(
                        "token" => $token_con_variables,
                        "idEmpresa" => $id_empresa_con_variables,
                        "idAgenda" => $id_agenda_con_variables,
                        "tokenReserva" => $datos_token_reserva->valor,
                        "idRecurso" => $datos_recurso_valor,
                        "idioma" => "es"
                    );
                    $resultado = $this->call_api_agenda('POST', $url_base_con_variables . '/reserva_multiple_validar_token', json_encode($datos_api));


                    if (($resultado->resultado == 0 || $resultado->estado == "expirado" || $resultado->estado == "confirmado" || $resultado->estado == "cancelado") && !isset($datos_comfirmacion)) {
                        $datos_token_reserva->delete();

                        $datos_personas_json = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->nombre . "__json_reserva", $etapa_id);
                        if ($datos_personas_json) {
                            $datos_personas_json->delete();
                        }

                        $datos_recurso_ = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->nombre . "_recurso", $etapa_id);
                        if ($datos_recurso_) {
                            $datos_recurso_->delete();
                        }

                        $datos_reservas_ = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->nombre . "_reservas", $etapa_id);
                        if ($datos_reservas_) {
                            $datos_reservas_->delete();
                        }
                    }
                } catch (Exception $e) {
                    $display = '<div class="control-group">';
                    $display .= '<div class="controls" data-fieldset="' . $this->fieldset . '">';
                    $display .= '<p style="background: #e2e9ef;padding: 19px;text-align: center;margin-left: -238px;">' . $this->etiqueta . ' no se encuentra disponible en este momento. Intente nuevamente más tarde, gracias.</p>';
                    $display .= '<input id="' . $this->id . '" type="hidden" name="' . $this->nombre . '" value="' . $this->nombre . '|' . $etapa_id . '">';
                    $display .= '</div>';
                    $display .= '</div>';

                    $display .= '<script>
                      $(".validacion-error").html("' . $this->etiqueta . ' no se encuentra disponible en este momento. Intente nuevamente más tarde, gracias.").fadeOut().fadeIn();
                      $("html, body").animate({ scrollTop: 0 }, "fast");
                    </script>';

                    return $display;
                }
            }
        }
        //se permiten configurar agenda con datos de variables @@

        $regla = new Regla($this->extra->id_recurso);
        $id_recurso_con_variables = $regla->getExpresionParaOutput($etapa_id);

        $regla = new Regla($this->extra->datos_ws_cod_tramite);
        $datos_ws_cod_tramite_con_variables = $regla->getExpresionParaOutput($etapa_id);

        $regla = new Regla($this->extra->datos_ws_nom_tramite);
        $datos_ws_nom_tramite_con_variables = $regla->getExpresionParaOutput($etapa_id);

        $regla = new Regla($this->extra->nombre_tramite);
        $reserva_nombre_tramite_con_variables = $regla->getExpresionParaOutput($etapa_id);

        $regla = new Regla($this->extra->email_tramite);
        $reserva_email_tramite_con_variables = $regla->getExpresionParaOutput($etapa_id);

        $regla = new Regla($this->extra->documento_tramite);
        $reserva_documento_tramite_con_variables = $regla->getExpresionParaOutput($etapa_id);

        $datos_con_variables = $this->datos;


        //dejamos realizar la reserva no estamos en modo visualizacion ni tenemos el dato de la agenda.
        $datos_api = array(
            "token" => $token_con_variables,
            "idEmpresa" => $id_empresa_con_variables,
            "idAgenda" => $id_agenda_con_variables,
            "idioma" => "es"
        );

        try {
            $resultado = $this->call_api_agenda('POST', $url_base_con_variables . '/recursos_por_agenda', json_encode($datos_api));
        } catch (Exception $e) {
            $display = '<div class="control-group">';
            $display .= '<div class="controls" data-fieldset="' . $this->fieldset . '">';
            $display .= '<p style="background: #e2e9ef;padding: 19px;text-align: center;margin-left: -238px;">' . $this->etiqueta . ' no se encuentra disponible en este momento. Intente nuevamente más tarde, gracias.</p>';
            $display .= '<input id="' . $this->id . '" type="hidden" name="' . $this->nombre . '" value="' . $this->nombre . '|' . $etapa_id . '">';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<script>
                      $(".validacion-error").html("' . $this->etiqueta . ' no se encuentra disponible en este momento. Intente nuevamente más tarde, gracias.").fadeOut().fadeIn();
                      $("html, body").animate({ scrollTop: 0 }, "fast");
                    </script>';

            return $display;
        }

        $regla = new Regla($this->extra->datos_ws_json);
        $datos_ws_con_variables = $regla->getExpresionParaOutput($etapa_id);
        $display .= '<div id="contenido_agenda_multiple' . $this->id . '" class="agenda-container">';

        $display .= '<div id="ubicacion_div" data-fieldset="' . $this->fieldset . '">';
        $display .= '<div class="control-group">';
        $display .= '<span class="control-label" data-fieldset="' . $this->fieldset . '" >Seleccionar ubicación:</span>';

        $display .= '<div class="controls">';
        $display .= '<div class="row-fluid radio">';
        $display .= '<div class="span6">';
        $total_recursos_multiple = 0;
        if (isset($resultado->recursos)) {
            foreach ($resultado->recursos as $recurso) {
                $se_encontro_recurso_id = $recurso->multiple == 1 && ($id_recurso_con_variables && ((int) $recurso->id) == ((int) $id_recurso_con_variables));

                if (($se_encontro_recurso_id || !$id_recurso_con_variables) && $recurso->multiple == 1) {
                    $total_recursos_multiple++;
                    if ($recurso->latitud) {
                        $latitud = $recurso->latitud;
                    } else {
                        $latitud = 0;
                    }
                    if ($recurso->longitud) {
                        $longitud = $recurso->longitud;
                    } else {
                        $longitud = 0;
                    }
                    if ($recurso->telefono) {
                        $telefono = '\'' . $recurso->telefono . '\'';
                    } else {
                        $telefono = 0;
                    }
                    if ($recurso->direccion) {
                        $direccion = '\'' . $recurso->direccion . '\'';
                    } else {
                        $direccion = 0;
                    }

                    //remplazo comillas para que no de errores js
                    $telefono = str_replace('"', '', $telefono);
                    $telefono = str_replace("'", '', $telefono);
                    $direccion = str_replace('"', '', $direccion);
                    $direccion = str_replace("'", '', $direccion);

                    $parametros_js = $this->id . ',' . $latitud . ',' . $longitud . ',\'' . $telefono . '\',\'' . $direccion . '\',true,' . $recurso->id;

                    $display .= '<label for="' . $recurso->id . '_' . $this->id . '"><input type="radio" name="recurso_' . $this->id . '" id="' . $recurso->id . '_' . $this->id . '"  value="' . $recurso->id . '"' . (isset($datos_recurso_acivo) ? ($datos_recurso_acivo == $recurso->id ? "checked" : "") : "") . ' onchange="recurso_seleccionado(' . $parametros_js . ');">' . $recurso->nombre . '</label>';

                    if ($se_encontro_recurso_id) {
                        break;
                    }
                }
            }
        }

        if (isset($resultado->recursos) && count($resultado->recursos) == 0 || $total_recursos_multiple == 0) {
            $display .= '<p>No hay recursos disponibles</p>';
        } else if ($id_recurso_con_variables && !$se_encontro_recurso_id) {
            $display .= '<p>No hay recursos disponibles</p>';
        }

        $display .= '</div><!--fin span6-->';

        $display .= '<div class="span6">';
        $display .= '<div id="mapa_' . $this->id . '" class="map"></div>';
        $display .= '<p id="direccion_recurso_' . $this->id . '"></p>';
        $display .= '<p id="telefono_recurso_' . $this->id . '"></p>';
        $display .= '<div class="recuadro">' . (isset($resultado->textoPaso1) ? $resultado->textoPaso1 : "" ) . '</div>';
        $display .= '</div><!--fin span6-->';
        $display .= '</div><!--fin row-fluid-->';
        $display .= '</div><!--fin controls-->';
        $display .= '</div><!--fin control-group-->';
        $display .= '</div><!--fin ubicacion_div-->';

        $display .= '<div id="agenda_div_ancla"></div>';
        $display .= '<div id="persona_div" style="display:none;" data-fieldset="' . $this->fieldset . '">';
        $display .= '<hr>';

        $display .= '<div class="control-group">';
        $display .= ' <input type="text" class="hidden" for="recurso" id="id_recurso" value=""/>';
        $display .= ' <input type="text" class="hidden" for="token_reserva" id="token_reserva" value=""/>';
        $display .= '<div class="alto-tabla"><table class="table" name="datos_sae" for="datos_sae">';
        $display .= '<caption class="hidden-accessible">Personas</caption>';
        $display .= '<thead>';
        $display .= '<tr>';
        $display .= '<th>Seleccionar</th>';
        $display .= '<th>Persona</th>';
        $display .= '<th>Fecha</th>';
        $display .= '<th>Hora</th>';
        if ($modo != 'visualizacion') {
            $display .= '<th>Acción</th>';
        }
        $display .= '</tr>';
        $display .= '</thead>';
        $display .= '<tbody>';

        $datos_personas_json = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->nombre . "__json_reserva", $etapa_id);
        if ($datos_personas_json) {
            $datos_personas_array = $datos_personas_json->valor;
        }
        $personas = json_decode($datos_ws_con_variables);
        $cantidad_personas = count($personas);
        foreach ($personas as $key => $value) {
            $display .= ' <tr>';
            $display .= ' <td><label class="radio"><input type="radio" class="input-medium" name="mostrar" onchange="reservar_persona(' . $key . ');"/></label></td>';
            $dato_mostrar = "";
            foreach ($datos_con_variables as $idx => $valor) {
                if (isset($valor->mostrar)) {
                    $dato_mostrar .= $value[$idx] . " ";
                }
            }
            $display .= ' <td><label class="accessible" for="persona">' . $dato_mostrar . '</label></td>';
            $display .= ' <td><label class="fecha" for="fecha" id="fecha' . $key . '">' . (isset($datos_personas_array->$key) ? $datos_personas_array->$key->fecha : "") . '</label></td>';
            $display .= ' <td><label class="hora" for="hora" id="hora' . $key . '">' . (isset($datos_personas_array->$key) ? $datos_personas_array->$key->hora : "") . '</label></td>';
            if ($modo != 'visualizacion') {
                $display .= ' <td>';
                $datos_ws = "";
                foreach ($datos_con_variables as $idx => $valor) {
                    $datos_ws .= $valor->agrupacion . "." . $valor->variable . "." . $value[$valor->posision] . ";";
                }
                $display .= ' <input type="text" class="hidden" for="datos" id="datos_ws' . $key . '" value="' . $datos_ws . '"/>';
                $display .= ' <input type="text" class="hidden" for="cancelacion" id="id_cancelacion' . $key . '" value="' . (isset($datos_personas_array->$key) ? $datos_personas_array->$key->codigoCancelacion : "") . '"/>';
                $display .= ' <input type="text" class="hidden" for="traza" id="id_trazabilidad_reserva' . $key . '" value="' . (isset($datos_personas_array->$key) ? $datos_personas_array->$key->codigoTrazabilidad : "") . '"/>';
                $display .= ' <input type="text" class="hidden" for="serie" id="serie' . $key . '" value="' . (isset($datos_personas_array->$key) ? $datos_personas_array->$key->serieNumero : "") . '"/>';
                $display .= ' <input type="text" class="hidden" for="id" id="id_reserva' . $key . '" value="' . (isset($datos_personas_array->$key) ? $datos_personas_array->$key->id_reserva : "") . '"/>';
                $display .= ' <input type="text" class="hidden estado_reserva" for="estado_reserva" id="estado_reserva' . $key . '" value="' . (isset($datos_comfirmacion) ? ($datos_comfirmacion == 1 && isset($datos_personas_array->$key) ? "1" : "0") : (isset($datos_personas_array->$key) && $dato_agenda != 0 ? "0" : "-1")) . '"/>';
                if ($modo != 'visualizacion') {
                    $display .= ' <input id="eliminar' . $key . '" value="" name="eliminar" onclick="eliminar_reserva_persona_click(' . $key . ');" class="button-no-style icn icn-error-sm  eliminar_reserva_persona" type="button" style="' . (isset($datos_comfirmacion) ? "display:none;" : (isset($datos_personas_array) ? "" : "display:none;")) . '")" >';
                    $display .= ' <input id="cancelar' . $key . '" value="" name="editar" onclick="confirm(\'¿Está seguro que desea eliminar la reserva?. No podrá volver a agendar a esta persona\')?cancelar_reserva_persona_click(' . $key . '):\'\';" class="button-no-style icn icn-error-sm editar_reserva_persona" type="button" style="display:none;">';
                    $display .= ' <input id="editar' . $key . '" value="" name="editar" onclick="reservar_persona(' . $key . ');" class="button-no-style icn icn-edit-sm  editar_reserva_persona" type="button" style="display:none;">';
                }
                $display .= '</td>';
            }
            $display .= ' </tr>';
        }
        $display .= '</tbody>';
        $display .= '</table></div>';
        if ($modo != 'visualizacion') {
            $display .= '<div id="btn_accion_reservas">';
            $display .= '<ul class="form-action-buttons">';
            $display .= '<li><input type="button" class="btn btn-secundary btn-danger btn-lg eliminar-reserva" style="' . (isset($datos_comfirmacion) ? "display:none;" : (isset($datos_personas_array) ? "" : "display:none;")) . '" id="eliminar_reservas"  onClick="eliminar_reservas_click();" value="Eliminar las reservas"/></li>';
            $display .= '<li><input type="button" class="btn btn-secundary btn-info btn-lg confirmar-reservas" style="' . (isset($datos_comfirmacion) ? "display:none;" : (isset($datos_personas_array) ? "" : "display:none;")) . '"  id="confirmar_reservas"  onClick="confirmar_reservas_click();" value="Confirmar las reservas"/></li>';
            $display .= '<li><input type="button" class="btn btn-secundary btn-danger btn-lg cancelar-reserva " style="' . (isset($datos_comfirmacion) ? "" : "display:none;") . '" id="cancelar_reservas"  onClick="cancelar_reservas_click();" value="Cancelar las reservas"/></li>';
            $display .= '</ul>';
            $display .= '</div>';
        }
        $display .= '</div><!--fin control-group-->';
        $display .= '<hr>';
        $display .= '</div>';

        $display .= '<div id="agenda_div" style="display:none;" data-fieldset="' . $this->fieldset . '">';

        $display .= '<div class="control-group">';
        $display .= ' <input type="text" class="hidden" for="persona" id="id_persona" value=""/>';
        $display .= '<span class="control-label">Preferencia de horario:</span>';
        $display .= '<div class="controls" id="preferencia_horarios">';
        $display .= '<label for="filto_cualquiera" class="radio">
                          <input type="radio" id="filto_cualquiera" class="filtro_fecha" name="pref_horarios" value="cualquier" onchange="preferencia_hora_seleccionada();" checked/>
                          Cualquier horario</label>

                        <label for="filto_maniana" class="radio">
                          <input type="radio" id="filto_maniana" class="filtro_fecha" name="pref_horarios" value="maniana" onchange="preferencia_hora_seleccionada();"/>
                          Solo por la mañana</label>

                        <label for="filto_tarde" class="radio">
                          <input type="radio" id="filto_tarde" class="filtro_fecha"  name="pref_horarios" value="tarde" onchange="preferencia_hora_seleccionada();"/>
                          Solo por la tarde</label>';
        $display .= '</div><!--fin controls-->';
        $display .= '</div><!--fin control-group-->';

        $display .= '<hr>';
        $display .= '<div class="control-group">';
        $display .= '<span class="control-label" data-fieldset="' . $this->fieldset . '">Seleccionar día:</span>';

        $display .= '<div class="controls">';
        $display .= '<div class="row-fluid">';
        $display .= '<div class="span6">';
        $display .= '<div id="calendario"></div>';
        $display .= '<input id="fechas_disponibles" title="fechas disponibles"  type="hidden" name="fechas_disponibles" value="" disabled="disabled">';
        $display .= '<input id="dia_seleccionado" title="dia seleccionado" type="text" name="dia_seleccionado" value="" disabled>';
        $display .= '<input id="hora_seleccionada" title="hora seleccionado"  type="hidden" name="hora_seleccionada" value="">';
        $display .= '<input id="sin_fechas_disponibles" title="sin fechas disponibles" type="text" name="sin_fechas_disponibles" value="" disabled>';
        $display .= '</div><!--fin span6-->';

        $display .= '<div class="span6">';
        $display .= '<div id="calendario_texto_fijo" class="recuadro">
                              <ul class="tips pasoTexto">
                                <li>Los días marcados en color verde tienen turnos disponibles</li>
                                <li>Seleccione el día de su preferencia haciendo click con el mouse</li>
                                <li>Luego de seleccionar el día, debajo del calendario se mostrarán los horarios disponibles para ese día</li>
                                <li>Seleccione un horario para continuar con la reserva</li>
                              </ul>
                            </div>';
        $display .= '<div id="calendario_texto_dinamico" class="recuadro"></div>';
        $display .= '</div><!--fin span6-->';
        $display .= '</div><!--fin row-fluid-->';
        $display .= '</div><!--fin controls-->';
        $display .= '</div><!--fin control-group-->';


        $display .= '<div id="horarios_lbl">';
        $display .= '<hr>';
        $display .= '<div class="control-group">';
        $display .= '<span class="control-label">Horarios disponibles:<span class="comentario">Zona horaria America/Montevideo</span></span>';

        $display .= '<div class="controls">';
        $display .= '<div class="row-fluid">';
        $display .= '<div class="span6">';
        $display .= '<div id="horas_cupos_disponibles_maniana"></div>';
        $display .= '</div><!--fin span6-->';

        $display .= '<div class="span6">';
        $display .= '<div id="horas_cupos_disponibles_tarde"></div>';
        $display .= '</div><!--fin span6-->';
        $display .= '</div><!--fin row-fluid-->';
        $display .= '</div><!--fin controls-->';
        $display .= '</div><!--fin control-group-->';
        $display .= '</div><!--fin horarios_lbl-->';

        $display .= '<div id="confrmar_hora_div_ancla"></div>';

        $display .= '<div style="display:none;" id="confrmar_hora_div">';
        $display .= '<hr>';
        $display .= '<div class="control-group">';
        $display .= '<span class="control-label">Verificar fecha y hora:</span>';

        $display .= '<div class="controls">';
        $display .= '<div id="confirmar_fecha_hora_seleccionada" class="msg-reserva"></div>';
        $display .= '</div><!--fin controls-->';
        $display .= '</div><!--fin control-group-->';
        $display .= '</div><!--fin confrmar_hora_div-->';

        $display .= '<div style="display:none;" id="confirmar_reserva_div">';
        $display .= '<hr>';
        $display .= '<input type="button" class="btn btn-secundary btn-lg confirmar-reserva" id="anadir_reserva"  onClick="anadir_reserva_click();" value="Reservar Persona"/>';

        if ($this->validacion) {
            $CI = &get_instance();

            //si el funcionario SI esta actuando como ciudadano y requiere agendar
            if ($this->requiere_agendar && UsuarioSesion::usuarioMesaDeEntrada() && $CI->session->userdata('id_usuario_ciudadano')) {
                $display .= '<span class="comentario-boton" >*Requerido para continuar el trámite</span>';
            }

            //si el funcionario NO esta actuando como ciudadano
            if (!$CI->session->userdata('id_usuario_ciudadano')) {
                $display .= '<span class="comentario-boton" >*Requerido para continuar el trámite</span>';
            }
        }

        $display .= '</div><!--fin confirmar_reserva_div-->';

        $display .= '</div><!--fin agenda_div-->';

        $display .= '<div style="display:none;" id="reserva_confirmada_ok" data-fieldset="' . $this->fieldset . '">';
        $display .= '<p class="dialogo validacion-success" id="titulo_reserva_confirmada"></p>';
        $display .= '<p id="num_serie_reserva"></p>';
        $display .= '<p id="cod_cancelacion_reserva"></p>';
        $display .= '<p id="cod_trazabilidad_reserva"></p>';
        $display .= '<p id="texto_ticket_reserva"></p>';
        $display .= '</div><!--fin reserva_confirmada_ok-->';

        $display .= '<input id="' . $this->id . '" type="hidden" name="' . $this->nombre . '" value="' . $this->nombre . '|' . $etapa_id . '">';


        $display .= '</div>';

        $display .= '<script type="text/javascript">
                var objeto_reserva = {};';
        if (isset($datos_comfirmacion)) {
            $recurso_reserva = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->nombre . "_recurso", $etapa_id);
            $token_reserva = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->nombre . "_token_reservas", $etapa_id);
            $display .= '$( "input[name=\'mostrar\']:radio" )
                        .wrap( "<span class=\'icn-success-sm icon-confirm\'></span>" )
                        .parent();
                        $( "input[name=\'recurso_' . $this->id . '\']:radio" )
                        .wrap( "<span class=\'icon-noconfirm\'></span>" )
                        .parent();
                        $( "input[id=\'' . $recurso_reserva->valor . '_' . $this->id . '\']:radio" )
                            .parent()
                            .removeClass( "icon-noconfirm" )
                        .addClass( "icon-confirm" )
                        .addClass( "icn-success-sm" );
                        
                        $( "input:radio" )
                        .attr("disabled", true)
                        .parent();
                        $( ".filtro_fecha" )
                        .attr("disabled", false)
                        .parent().removeClass( "icon-confirm icn-success-sm" );
                  $("#id_recurso").val(' . $recurso_reserva->valor . ');     
                 $("#persona_div").show();
                 $("#token_reserva").val("' . $token_reserva->valor . '");
                  $( ".editar_reserva_persona" ).show();
                  $( ".eliminar_reserva_persona" ).hide();
';
        }

        $display .= 'recurso_seleccionado(' . $this->id . ',0, 0, 0, 0, false,0);
                    function preferencia_hora_seleccionada() {
                        var pref = $("input:radio[name=pref_horarios]:checked").val();
                        if (pref === "maniana") {
                            $("#horas_cupos_disponibles_tarde").css("display", "none");
                            $("#horas_cupos_disponibles_maniana").css("display", "block");
                        }

                        if (pref === "tarde") {
                            $("#horas_cupos_disponibles_maniana").css("display", "none");
                            $("#horas_cupos_disponibles_tarde").css("display", "block");
                        }

                        if (pref === "cualquier") {
                            $("#horas_cupos_disponibles_tarde").css("display", "block");
                            $("#horas_cupos_disponibles_maniana").css("display", "block");
                        }

                    }

                    function check_hora_seleccionada(id) {
                        $("#confrmar_hora_div").hide().fadeIn();
                        $("#hora_seleccionada").val($("#span_hora_seleccionada_" + id).html().trim());
                        $("#confirmar_fecha_hora_seleccionada").html($("#dia_seleccionado").val() + " | " + $("#hora_seleccionada").val() + " hs.");
                        $("#confirmar_reserva_div").hide().fadeIn();
                        $("html, body").animate({
                            scrollTop: $("#confirmar_reserva_div").offset().top
                        }, "fast");
                    }

                    function anadir_reserva() {
                        var id_persona = $("#id_persona").val();
                        var datos_ws_sae = $("#datos_ws" + id_persona).val();
                        var estado = $("#estado_reserva" + id_persona).val();
                        var token_reserva = $("#token_reserva").val();
                        var id_reserva = $("#id_reserva" + id_persona).val();
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "' . site_url("etapas/agenda_multiple_sae_api_anadir_reserva") . '",
                            data: {
                                "method": "POST",
                                "url": "' . $url_base_con_variables . '/reserva_multiple_anadir_reserva",
                                "token": "' . @openssl_encrypt($token_con_variables, 'bf-ofb', 'simple_1058') . '",
                                "id_empresa": ' . $id_empresa_con_variables . ',
                                "id_agenda": ' . $id_agenda_con_variables . ',
                                "id_recurso": $("#id_recurso").val(),
                                "id_disponibilidad": $("input[name=horas_disponibles_chk]:checked").val(),
                                "datos_reserva": datos_ws_sae,
                                "idioma": "es",
                                "etapa_id": "' . $etapa_id . '",
                                "paso_numero": ' . $paso_numero . ',
                                "nombre_campo": "' . $this->nombre . '",                                
                                "cantidad_personas": "' . $cantidad_personas . '",                                
                                "fecha": $("#dia_seleccionado").val(),
                                "hora": $("#hora_seleccionada").val(),
                                "id_persona": id_persona,
                                "token_reserva": token_reserva
                            }
                        })
                                .done(function (data) {
                                    if (parseInt(data.resultado) === 1) {
                                        $("#fecha" + id_persona).html($("#dia_seleccionado").val());
                                        $("#hora" + id_persona).html($("#hora_seleccionada").val());
                                        $("#id_cancelacion" + id_persona).val(data.codigoCancelacion);
                                        $("#id_trazabilidad_reserva" + id_persona).val(data.codigoTrazabilidad);
                                        $("#serie" + id_persona).val(data.serieNumero);
                                        $("#id_reserva" + id_persona).val(data.id);
                                        $("#estado_reserva" + id_persona).val(0);
                                        $("#ubicacion_div").fadeOut();
                                        $("#agenda_div").fadeOut();
                                        $("#editar" + id_persona).hide();
                                        $("#cancelar" + id_persona).hide();
                                        $("#eliminar" + id_persona).show();
                                        $("#anadir_reserva").prop("disabled", false);
                                        $("#anadir_reserva").val("Reservar Persona");
                                        $(".validacion-error").html("").hide();
                                        $("#confirmar_reservas").show();
                                        $("#eliminar_reservas").show();
                                    }
                                    else if (parseInt(data.resultado) === 2) {
                                        $("html, body").animate({scrollTop: 0}, "fast");
                                        $(".validacion-error").html("La reserva no se pudo realizar porque la disponibilidad indicada quedó sin cupos.").fadeOut().fadeIn();
                                        $("#anadir_reserva").prop("disabled", false);
                                        $("#anadir_reserva").val("Reservar Persona");
                                    }
                                    else {
                                        $("html, body").animate({scrollTop: 0}, "fast");
                                        if (data.error === "ya_existe_una_reserva_para_el_dia_especificado_con_los_datos_proporcionados") {
                                            $(".validacion-error").html("La reserva no se pudo realizar: ya existe una reserva para el día especificado y los datos proporcionados.").fadeOut().fadeIn();
                                        }
                                        else {
                                            if (data.error) {
                                                $(".validacion-error").html("La reserva no se pudo realizar: los datos proporcionados no cumplen con las validaciones necesarias en la agenda (" + data.error + ").").fadeOut().fadeIn();
                                            } else {
                                                $(".validacion-error").html("La reserva no se pudo realizar: los datos proporcionados no cumplen con las validaciones necesarias en la agenda (" + data.errores + ").").fadeOut().fadeIn();
                                            }
                                        }
                                        $("#anadir_reserva").prop("disabled", false);
                                        $("#anadir_reserva").val("Reservar Persona");
                                    }
                                })
                                .fail(function (jqXHR, textStatus, errorThrown) {
                                    $("html, body").animate({scrollTop: 0}, "fast");
                                    $(".validacion-error").html("No se pudo realizar la reserva, intente nuevamente. Gracias.").fadeOut().fadeIn();
                                    $("#anadir_reserva").prop("disabled", false);
                                    $("#anadir_reserva").val("Reservar Persona");
                                });
                    }
                    
                        function editar_reserva() {
                        var id_persona = $("#id_persona").val();
                        var datos_ws_sae = $("#datos_ws" + id_persona).val();
                        var estado = $("#estado_reserva" + id_persona).val();
                        var token_reserva = $("#token_reserva").val();
                        var id_reserva = $("#id_reserva" + id_persona).val();
                        var id_cancelacion =$("#id_cancelacion" + id_persona).val();
                         var estado = $("#estado_reserva" + id_persona).val();
                    if(estado==1){
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "' . site_url("etapas/agenda_multiple_sae_api_editar_reserva") . '",
                            data: {
                                "method": "POST",
                                "url": "' . $url_base_con_variables . '/modificar_reserva",
                                "token": "' . @openssl_encrypt($token_con_variables, 'bf-ofb', 'simple_1058') . '",
                                "id_empresa": ' . $id_empresa_con_variables . ',
                                "id_agenda": ' . $id_agenda_con_variables . ',
                                "id_recurso": $("#id_recurso").val(),
                                "id_disponibilidad": $("input[name=horas_disponibles_chk]:checked").val(),
                                "id_reserva": id_reserva,
                                "idioma": "es",
                                "etapa_id": "' . $etapa_id . '",
                                "paso_numero": ' . $paso_numero . ',
                                "nombre_campo": "' . $this->nombre . '",
                                "secuencia": ' . $secuencia . ',
                                "fecha": $("#dia_seleccionado").val(),
                                "hora": $("#hora_seleccionada").val(),
                                "id_persona": id_persona,
                                "id_cancelacion": id_cancelacion,
                            }
                        })
                                .done(function (data) {
                                    if (parseInt(data.resultado) === 1) {
                                        $("#fecha" + id_persona).html($("#dia_seleccionado").val());
                                        $("#hora" + id_persona).html($("#hora_seleccionada").val());
                                        $("#id_cancelacion" + id_persona).val(data.codigoCancelacion);
                                        $("#id_trazabilidad_reserva" + id_persona).val(data.codigoTrazabilidad);
                                        $("#serie" + id_persona).val(data.serieNumero);
                                        $("#id_reserva" + id_persona).val(data.id);
                                        $("#estado_reserva" + id_persona).val(1);
                                        $("#ubicacion_div").fadeOut();
                                        $("#agenda_div").fadeOut();
                                        $("#editar" + id_persona).show();
                                        $("#cancelar" + id_persona).show();
                                        $("#eliminar" + id_persona).hide();
                                        $("#anadir_reserva").prop("disabled", false);
                                        $("#anadir_reserva").val("Reservar Persona");
                                        $(".validacion-error").html("").hide();
                                    }
                                    else if (parseInt(data.resultado) === 2) {
                                        $("html, body").animate({scrollTop: 0}, "fast");
                                        $(".validacion-error").html("La reserva no se pudo realizar porque la disponibilidad indicada quedó sin cupos.").fadeOut().fadeIn();
                                        $("#anadir_reserva").prop("disabled", false);
                                        $("#anadir_reserva").val("Reservar Persona");
                                    }
                                    else {
                                        $("html, body").animate({scrollTop: 0}, "fast");
                                        if (data.error === "ya_existe_una_reserva_para_el_dia_especificado_con_los_datos_proporcionados") {
                                            $(".validacion-error").html("La reserva no se pudo realizar: ya existe una reserva para el día especificado y los datos proporcionados.").fadeOut().fadeIn();
                                        }
                                        else {
                                            if (data.error) {
                                                $(".validacion-error").html("La reserva no se pudo realizar: los datos proporcionados no cumplen con las validaciones necesarias en la agenda (" + data.error + ").").fadeOut().fadeIn();
                                            } else {
                                                $(".validacion-error").html("La reserva no se pudo realizar: los datos proporcionados no cumplen con las validaciones necesarias en la agenda (" + data.errores + ").").fadeOut().fadeIn();
                                            }
                                        }
                                        $("#anadir_reserva").prop("disabled", false);
                                        $("#anadir_reserva").val("Reservar Persona");
                                    }
                                })
                                .fail(function (jqXHR, textStatus, errorThrown) {
                                    $("html, body").animate({scrollTop: 0}, "fast");
                                    $(".validacion-error").html("No se pudo realizar la reserva, intente nuevamente. Gracias.").fadeOut().fadeIn();
                                    $("#anadir_reserva").prop("disabled", false);
                                    $("#anadir_reserva").val("Reservar Persona");
                                });
                                
                        }else{
                      $("html, body").animate({scrollTop: 0}, "fast");
                      $(".validacion-error").html("No se pude editar. La reserva no existe o no está confirmada.").fadeOut().fadeIn();
                      }
                    }

                    function eliminar_reserva() {
                        var id_persona = $("#id_persona").val();
                        var estado = $("#estado_reserva" + id_persona).val();
                        var token_reserva = $("#token_reserva").val();
                        var id_reserva = $("#id_reserva" + id_persona).val();
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "' . site_url("etapas/agenda_multiple_sae_api_eliminar_reserva") . '",
                            data: {
                                "method": "POST",
                                "url": "' . $url_base_con_variables . '/reserva_multiple_eliminar_reserva",
                                "token": "' . @openssl_encrypt($token_con_variables, 'bf-ofb', 'simple_1058') . '",
                                "id_empresa": ' . $id_empresa_con_variables . ',
                                "id_agenda": ' . $id_agenda_con_variables . ',
                                "id_reserva": id_reserva,
                                "token_reserva": token_reserva,
                                "id_recurso": $("#id_recurso").val(),
                                "nombre_campo": "' . $this->nombre . '",
                                "etapa_id": "' . $etapa_id . '",
                                "idioma": "es"
                            }
                        })
                                .done(function (data) {                
                                    $("#fecha" + id_persona).html("");
                                    $("#hora" + id_persona).html("");
                                    $("#id_cancelacion" + id_persona).val("");
                                    $("#id_trazabilidad_reserva" + id_persona).val("");
                                    $("#serie" + id_persona).val("");
                                    $("#id_reserva" + id_persona).val("");
                                    $("#estado_reserva" + id_persona).val(-1);
                                    anadir_reserva();
                                })
                                .fail(function (jqXHR, textStatus, errorThrown) {
                                    $("html, body").animate({scrollTop: 0}, "fast");
                                    $(".validacion-error").html("No se pudo cancelar la reserva, intente nuevamente. Gracias.").fadeOut().fadeIn();
                                    $("#anadir_reserva").prop("disabled", false);
                                    $("#anadir_reserva").val("Reservar Persona");
                                });
                    }

                    function cancelar_reserva_persona_click(id_persona) {
                    var estado = $("#estado_reserva" + id_persona).val();
                    if(estado==1){
                        var id_reserva = $("#id_reserva" + id_persona).val();
                        var cod_cancelacion = $("#id_cancelacion" + id_persona).val();
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "' . site_url("etapas/agenda_multiple_sae_api_cancelar_reserva") . '",
                            data: {
                                "method": "POST",
                                "url": "' . $url_base_con_variables . '/cancelar_reserva",
                                "token": "' . @openssl_encrypt($token_con_variables, 'bf-ofb', 'simple_1058') . '",
                                "id_empresa": ' . $id_empresa_con_variables . ',
                                "id_agenda": ' . $id_agenda_con_variables . ',
                                "id_reserva": id_reserva,
                                "id_recurso": $("#id_recurso").val(),
                                "id_persona": id_persona,
                                "codigo_cancelacion": cod_cancelacion,
                                "nombre_campo": "' . $this->nombre . '",
                                "etapa_id": "' . $etapa_id . '",
                                "idioma": "es"
                            }
                        })
                                .done(function (data) {
                                    if (parseInt(data.resultado) === 1) {
                                        $("#fecha" + id_persona).html("");
                                        $("#hora" + id_persona).html("");
                                        $("#id_cancelacion" + id_persona).val("");
                                        $("#id_trazabilidad_reserva" + id_persona).val("");
                                        $("#serie" + id_persona).val("");
                                        $("#id_reserva" + id_persona).val("");
                                        $("#estado_reserva" + id_persona).val(0);
                                        $("#anadir_reserva").prop("disabled", false);
                                        $("#anadir_reserva").val("Reservar Persona");
                                        $(".validacion-error").html("").hide();
                                        $("#editar" + id_persona).hide();
                                        $("#cancelar" + id_persona).hide();
                                        $("#eliminar" + id_persona).hide();
                                    } else {
                                        $("html, body").animate({scrollTop: 0}, "fast");
                                        $(".validacion-error").html("No se pudo cancelar la reserva, intente nuevamente. Gracias.").fadeOut().fadeIn();
                                    }
                                })
                                .fail(function (jqXHR, textStatus, errorThrown) {
                                    $("html, body").animate({scrollTop: 0}, "fast");
                                    $(".validacion-error").html("No se pudo cancelar la reserva, intente nuevamente. Gracias.").fadeOut().fadeIn();
                                    $("#anadir_reserva").prop("disabled", false);
                                    $("#anadir_reserva").val("Reservar Persona");
                                });
                      }else{
                      $("html, body").animate({scrollTop: 0}, "fast");
                      $(".validacion-error").html("No se pudo cancelar. La reserva no existe o no está confirmada.").fadeOut().fadeIn();
                      }
                    }
                    function confirmar_reservas_click() {
                        var token_reserva = $("#token_reserva").val();
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "' . site_url("etapas/reserva_multiple_confirmar_reservas") . '",
                            data: {
                                "method": "POST",
                                "url": "' . $url_base_con_variables . '/reserva_multiple_confirmar_reservas",
                                "token": "' . @openssl_encrypt($token_con_variables, 'bf-ofb', 'simple_1058') . '",
                                "id_empresa": ' . $id_empresa_con_variables . ',
                                "id_agenda": ' . $id_agenda_con_variables . ',
                                "token_reserva": token_reserva,
                                "id_recurso": $("#id_recurso").val(),
                                "nombre_campo": "' . $this->nombre . '",
                                "id_campo": "' . $this->id . '",
                                "secuencia": ' . $secuencia . ',
                                "etapa_id": "' . $etapa_id . '",
                                "idioma": "es"
                            }
                        })
                                .done(function (data) {
                                if(data.error==-2){
                                    $("html, body").animate({scrollTop: 0}, "fast");
                                    $(".validacion-error").html("Existen reservas no realizadas. Por favor completa la agenda.").fadeOut().fadeIn();
                                    $("#anadir_reserva").prop("disabled", false);
                                    $("#anadir_reserva").val("Reservar Persona");
                                    }else if(data.resultado==1){
                                    $("#eliminar_reservas").hide();
                                    $("#confirmar_reservas").hide();
                                    $("#cancelar_reservas").show();
                                    $("#agenda_div").hide();
                                    $( "input:radio" )
                                        .wrap( "<span class=\'icn-success-sm icon-confirm\'></span>" )
                                        .attr("disabled", true)
                                        .removeAttr("checked")
                                        .parent();
                                    $( ".estado_reserva" ).attr("value", "1");
                                    $(".editar_reserva_persona").show();
                                    $(".eliminar_reserva_persona").hide();
                                    //Traza sub-proceso
                                    $.ajax({
                                      type: "POST",
                                      dataType: "text",
                                      url: "' . site_url("etapas/trazabilidad_sub_proceso_agenda") . '",
                                      data: {
                                              "etapa_id":' . $etapa_id . ',
                                              "secuencia": ' . $secuencia . ',
                                            }
                                        }).fail(function(jqXHR, textStatus, errorThrown) {
                                            console.log("La solicitud a fallado al enviar traza");
                                        });
                                    //Termina traza sub-proceso
                                    }else{
                                    $("html, body").animate({scrollTop: 0}, "fast");
                                    $(".validacion-error").html(data.error).fadeOut().fadeIn();
                                    $("#anadir_reserva").prop("disabled", false);
                                    $("#anadir_reserva").val("Reservar Persona");
                                    }
                                })
                                .fail(function (jqXHR, textStatus, errorThrown) {
                                    $("html, body").animate({scrollTop: 0}, "fast");
                                    $(".validacion-error").html("No se pudo confirmar las reservas, intente nuevamente. Gracias.").fadeOut().fadeIn();
                                    $("#anadir_reserva").prop("disabled", false);
                                    $("#anadir_reserva").val("Reservar Persona");
                                });
                    }

                    function eliminar_reservas_click() {
                        var token_reserva = $("#token_reserva").val();
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "' . site_url("etapas/reserva_multiple_eliminar_reservas") . '",
                            data: {
                                "method": "POST",
                                "url": "' . $url_base_con_variables . '/reserva_multiple_cancelar_reservas",
                                "token": "' . @openssl_encrypt($token_con_variables, 'bf-ofb', 'simple_1058') . '",
                                "id_empresa": ' . $id_empresa_con_variables . ',
                                "id_agenda": ' . $id_agenda_con_variables . ',
                                "token_reserva": token_reserva,
                                "id_recurso": $("#id_recurso").val(),
                                "nombre_campo": "' . $this->nombre . '",
                                "etapa_id": "' . $etapa_id . '",
                                "idioma": "es"
                            }
                        })
                                .done(function (data) {
                                    $("#contenido_agenda_multiple' . $this->id . '").load(" #contenido_agenda_multiple' . $this->id . '");
                                })
                                .fail(function (jqXHR, textStatus, errorThrown) {
                                    $("html, body").animate({scrollTop: 0}, "fast");
                                    $(".validacion-error").html("No se pudo cancelar la reserva, intente nuevamente. Gracias.").fadeOut().fadeIn();
                                    $("#anadir_reserva").prop("disabled", false);
                                    $("#anadir_reserva").val("Reservar Persona");
                                });
                    }

                    function eliminar_reserva_persona_click(id_persona) {
                        var estado = $("#estado_reserva" + id_persona).val();
                        if (estado == 0) {
                            var token_reserva = $("#token_reserva").val();
                            var id_reserva = $("#id_reserva" + id_persona).val();
                            $.ajax({
                                type: "POST",
                                dataType: "json",
                                url: "' . site_url("etapas/agenda_multiple_sae_api_eliminar_reserva") . '",
                                data: {
                                    "method": "POST",
                                    "url": "' . $url_base_con_variables . '/reserva_multiple_eliminar_reserva",
                                    "token": "' . @openssl_encrypt($token_con_variables, 'bf-ofb', 'simple_1058') . '",
                                    "id_empresa": ' . $id_empresa_con_variables . ',
                                    "id_agenda": ' . $id_agenda_con_variables . ',
                                    "id_reserva": id_reserva,
                                    "id_persona": id_persona,
                                    "token_reserva": token_reserva,
                                    "id_recurso": $("#id_recurso").val(),
                                    "nombre_campo": "' . $this->nombre . '",
                                    "etapa_id": "' . $etapa_id . '",
                                    "idioma": "es"
                                }
                            })
                                    .done(function (data) {
                                    if (parseInt(data.resultado) === 1) {
                                        $("#fecha" + id_persona).html("");
                                        $("#hora" + id_persona).html("");
                                        $("#id_cancelacion" + id_persona).val("");
                                        $("#id_trazabilidad_reserva" + id_persona).val("");
                                        $("#serie" + id_persona).val("");
                                        $("#id_reserva" + id_persona).val("");
                                        $("#estado_reserva" + id_persona).val(-1);
                                        $("#editar" + id_persona).hide();
                                        $("#cancelar" + id_persona).hide();
                                        $("#eliminar" + id_persona).hide();
                                     } else {
                                        $("html, body").animate({scrollTop: 0}, "fast");
                                        $(".validacion-error").html("No se pudo eliminar la reserva, intente nuevamente. Gracias.").fadeOut().fadeIn();
                                    }
                                    })
                                    .fail(function (jqXHR, textStatus, errorThrown) {
                                        $("html, body").animate({scrollTop: 0}, "fast");
                                        $(".validacion-error").html("No se pudo cancelar la reserva, intente nuevamente. Gracias.").fadeOut().fadeIn();
                                        $("#anadir_reserva").prop("disabled", false);
                                        $("#anadir_reserva").val("Reservar Persona");
                                    });
                        }
                    }



                    function anadir_reserva_click() {
                        var id_persona = $("#id_persona").val();
                        var estado = $("#estado_reserva" + id_persona).val();
                        $("#anadir_reserva").prop("disabled", true);
                        $("#anadir_reserva").val("Enviando...");
                        //console.log(estado);
                        if (estado == 1) {
                           editar_reserva();
                        }
                        if (estado == -1) {
                            anadir_reserva();
                        }

                    }

                    function cancelar_reservas_click() {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "' . site_url("etapas/reserva_multiple_cancelar_todas_reservas") . '",
                            data: {
                                "method": "POST",
                                "url": "' . $url_base_con_variables . '/cancelar_reserva",
                                "token": "' . @openssl_encrypt($token_con_variables, 'bf-ofb', 'simple_1058') . '",
                                "nombre_campo": "' . $this->nombre . '",
                                "etapa_id": "' . $etapa_id . '",
                                "idioma": "es"
                            }
                        })
                                .done(function (data) {
                                    if (data.error.length == 0) {
                                        location.reload(true);
                                    } else {
                                        $("html, body").animate({ scrollTop: 0 }, "fast");
                                        $(".validacion-error").html("No se pudo cancelar todas las reservas.").fadeOut().fadeIn();  
                                        //location.reload(true);
                                    }
                                })
                                .fail(function (jqXHR, textStatus, errorThrown) {
                                    $("html, body").animate({scrollTop: 0}, "fast");
                                    $(".validacion-error").html("No se pudo cancelar la reserva, intente nuevamente. Gracias.").fadeOut().fadeIn();
                                    $("#anadir_reserva").prop("disabled", false);
                                    $("#anadir_reserva").val("Reservar Persona");
                                });
                    }

                    function recurso_seleccionado(id_campo, latitud, longitud, telefono, direccion, cambio_radio, id_recurso) {

                        if (cambio_radio) {

                    //MOSTRAR MAPA
                            $("#mapa_" + id_campo).html("");
                            if (telefono != 0)
                                $("#telefono_recurso_" + id_campo).html("Teléfono: " + telefono);
                            else
                                $("#telefono_recurso_" + id_campo).html("");
                            if (direccion != 0)
                                $("#direccion_recurso_" + id_campo).html("Dirección: " + direccion);
                            else
                                $("#direccion_recurso_" + id_campo).html("");
                            lat2 = latitud;
                            lon2 = longitud;
                            zoom2 = 15;
                            if ((lat2 == "" || lat2 == "0") && (lon2 == "" || lon2 == "0")) {
                                lat2 = "-32.5476626";
                                lon2 = "-55.4411862";
                                zoom2 = 2;
                            }
                            var coord2 = ol.proj.fromLonLat([parseFloat(lon2), parseFloat(lat2)]);
                            //Capa del mapa
                            var mapLayer = new ol.layer.Tile({
                                source: new ol.source.OSM()
                            });
                            //Capa del punto
                            var iconStyle = new ol.style.Style({
                                image: new ol.style.Icon(({
                                    anchor: [0.5, 0.5],
                                    anchorXUnits: "fraction",
                                    anchorYUnits: "fraction",
                                    opacity: 0.75,
                                    src: "' . base_url() . 'assets/img/pin.png"
                                }))
                            });
                            var iconFeature = new ol.Feature({
                                geometry: new ol.geom.Point(coord2),
                                name: "X"
                            });
                            iconFeature.setStyle(iconStyle);
                            var vectorSource = new ol.source.Vector({
                                features: [iconFeature]
                            });
                            var vectorLayer = new ol.layer.Vector({
                                source: vectorSource
                            });
                            //Dibujar el mapa con las dos layers
                            var map = new ol.Map({
                                target: "mapa_" + id_campo,
                                layers: [mapLayer, vectorLayer],
                                view: new ol.View({
                                    center: coord2,
                                    zoom: zoom2
                                })
                            });
                            $("html, body").animate({
                                scrollTop: $("#mapa_" + id_campo).offset().top
                            }, "fast");
                            // TERMINA MOSTRAR MAPA

                            $("#id_recurso").val(id_recurso);
                            $.ajax({
                                type: "POST",
                                dataType: "json",
                                url: "' . site_url("etapas/agenda_multiple_sae_api_obtener_token") . '",
                                data: {
                                    "method": "POST",
                                    "url": "' . $url_base_con_variables . '/reserva_multiple_obtener_token",
                                    "token": "' . @openssl_encrypt($token_con_variables, 'bf-ofb', 'simple_1058') . '",
                                    "id_empresa": ' . $id_empresa_con_variables . ',
                                    "id_agenda": ' . $id_agenda_con_variables . ',
                                    "codigoTramite": "' . $datos_ws_cod_tramite_con_variables . '",
                                    "nombre": "' . $reserva_nombre_tramite_con_variables . '",
                                    "email": "' . $reserva_email_tramite_con_variables . '",
                                    "documento": "' . $reserva_documento_tramite_con_variables . '",
                                    "nombre_campo": "' . $this->nombre . '",
                                    "etapa_id": "' . $etapa_id . '",
                                    "id_recurso": $("#id_recurso").val(),
                                    "idioma": "es"
                                }
                            })
                                    .done(function (data) {   
                                        //$("#persona_div").load("#persona_div");
                                        $("#token_reserva").val(data.token_reservas);                                        
                                        $("#persona_div").hide();
                                        if(data.token_base==0){
                                            $( ".fecha" )
                                                .html(""); 
                                            $( ".hora" )
                                                .html(""); 
                                            $(".editar_reserva_persona").hide();
                                            $(".eliminar_reserva_persona").hide();
                                            $("#confirmar_reservas").hide();
                                            $("#eliminar_reservas").hide();
                                            $("#agenda_div").hide();
                                            $("input:radio[name=mostrar]").removeAttr("checked");
                                        }
                                        $("#persona_div").show();                                        
                                    })
                                    .fail(function (jqXHR, textStatus, errorThrown) {
                                        $("html, body").animate({scrollTop: 0}, "fast");
                                        $(".validacion-error").html("No se pudo obtener el token de la reserva, intente nuevamente. Gracias.").fadeOut().fadeIn();
                                        $("#anadir_reserva").prop("disabled", false);
                                        $("#anadir_reserva").val("Reservar Persona");
                                    });
                        }
                    }


                      
                      function reservar_persona(id_persona){
                      var id_recurso= $("#id_recurso").val();
                      $("#id_persona").val(id_persona);
                      var estado = $("#estado_reserva" + id_persona).val();
                      console.log(estado);
                      if(estado!=0){
                        $.blockUI({
                        message: \'<img src="' . site_url() . 'assets/img/ajax-loader.gif"></img>\',
                         css: {
                           width: \'70px\',
                           height: \'60px\',
                            border: \'none\',
                            padding: \'15px\',
                            backgroundColor: \'#000\',
                            textAlign: \'center\',
                            color: \'#fff\',
                            top: \'40%\',
                            left: \'50%\',
                       }});
                      
                      $("#agenda_div").show();
                      
                      $("#dia_seleccionado").hide().val("");
                      $("#horas_cupos_disponibles_tarde").html("");
                      $("#horas_cupos_disponibles_maniana").html("");
                      $("#horarios_lbl").hide();

                      $("#confrmar_hora_div").hide();
                      $("#confirmar_fecha_hora_seleccionada").html("");
                      $("#hora_seleccionada").val("");

                      $("#confirmar_reserva_div").hide();

                      var datos;                      
                      $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "' . site_url("etapas/agenda_sae_api_disponibilidades") . '",
                        data: {
                                "method": "POST",
                                "url": "' . $url_base_con_variables . '/disponibilidades_por_recurso",
                                "token": "' . @openssl_encrypt($token_con_variables, 'bf-ofb', 'simple_1058') . '",
                                "id_empresa": ' . $id_empresa_con_variables . ',
                                "id_agenda": ' . $id_agenda_con_variables . ',
                                "id_recurso": id_recurso,
                                "idioma": "es",
                              }
                          })
                          .done(function(data) {
                            $.unblockUI();

                            datos = data;
                            var result = "";
                            if(data.disponibilidades.length > 0){
                              var json_data = data.disponibilidades;
                              var cont = 1;

                              jQuery.each(json_data, function(i, val) {

                                  jQuery.each(val, function(idate, valdate) {

                                      var anio = JSON.stringify(idate).substring(1, 5);
                                      var mes = JSON.stringify(idate).substring(5, 7);
                                      var dia = JSON.stringify(idate).substring(7, 9);
                                      var fecha = dia+"/"+mes+"/"+anio;

                                      if(cont == 1)
                                        result += \'["\' + fecha + \'"\' + \',\';

                                      else if(cont == (Object.keys(json_data).length))
                                        result += \'"\' + fecha + \'"\' + \']\';

                                      else
                                        result += \'"\' + fecha + \'"\' + \',\';

                                      cont  = cont +1;
                                  });
                              });

                              $("#sin_fechas_disponibles").val("").hide();
                            }
                            else{
                              $("#sin_fechas_disponibles").val("No hay cupos disponibles").hide().fadeIn();
                                $("#horas_cupos_disponibles").html("");
                            }

                            $("#fechas_disponibles").val(result);

                            if((data.textoAgendaPaso2 === null || data.textoAgendaPaso2 === "") && (data.textoRecursoPaso2 === null || data.textoRecursoPaso2 === "")){
                              $("#calendario_texto_dinamico").css("display","none");
                              $("#calendario_texto_dinamico").html("");
                            }
                            else {
                              var textoAgendaPaso2 = "";
                              var textoRecursoPaso2 = "";
                              if(data.textoAgendaPaso2 !== null && data.textoAgendaPaso2 !== ""){
                                textoAgendaPaso2 = data.textoAgendaPaso2;
                              }

                              if(data.textoRecursoPaso2 !== null && data.textoRecursoPaso2 !== ""){
                                textoRecursoPaso2 = data.textoRecursoPaso2;
                              }

                              $("#calendario_texto_dinamico").html("<p>" + textoAgendaPaso2 + "</p>" + "<p>" + textoRecursoPaso2 + "</p>");
                              $("#calendario_texto_dinamico").css("display","block");
                            }


                            jQuery.getScript("' . base_url() . 'assets/js/calendario-sae.js")
                              .done(function() {
                                $("#calendario").html("<div id=\'datepickerinline\' class=\'calendar-disponibilidad\'></div>");

                                $("#datepickerinline").datepicker({
                                	dateFormat: "yymmdd",
                                	onSelect: function(date) {
                                      $("#horas_cupos_disponibles_tarde").html("");
                                      $("#horas_cupos_disponibles_maniana").html("");
                                      $("#horarios_lbl").hide();

                                      $("#confrmar_hora_div").hide();
                                      $("#confirmar_fecha_hora_seleccionada").html("");
                                      $("#hora_seleccionada").val("");

                                      $("#confirmar_reserva_div").hide();

                                      var anio_select = date.substring(0, 4);
                                      var mes_select = date.substring(4, 6);
                                      var dia_Select = date.substring(6, 8);
                                      var fecha_select = dia_Select+"/"+mes_select+"/"+anio_select;

                                     $("#dia_seleccionado").val(fecha_select).hide().fadeIn();

                                     if(datos.disponibilidades.length > 0){
                                       var disponibilidades_json = datos.disponibilidades;
                                       var horas_disponibles = "";

                                       jQuery.each(disponibilidades_json, function(indice, fechas) {

                                           jQuery.each(fechas, function(fecha, horas) {

                                             if(String(fecha) === String(date)){

                                                $("#horas_cupos_disponibles_maniana").append("<p>Por la mañana</p>");
                                                $("#horas_cupos_disponibles_tarde").append("<p>Por la tarde</p>");

                                               jQuery.each(horas, function(hora, hora_datos) {
                                                 if(parseInt(hora_datos.cupo) > 0){
                                                    if(hora < "13:00"){
                                                      $("#horas_cupos_disponibles_maniana").append(\'<label for="\' + hora_datos.id + \'" class="radio"><input type="radio" id="\' + hora_datos.id + \'" name="horas_disponibles_chk" style="display: inline;" value=\' + hora_datos.id + \' onchange="check_hora_seleccionada(\' + hora_datos.id + \');" /><span id="span_hora_seleccionada_\' + hora_datos.id + \'"> \'+ hora +\'</span> <span class="cupos-horas"> - \' + hora_datos.cupo + \' lugares</span></label>\');
                                                    }
                                                    else{
                                                      $("#horas_cupos_disponibles_tarde").append(\'<label for="\' + hora_datos.id + \'" class="radio"><input type="radio" id="\' + hora_datos.id + \'" name="horas_disponibles_chk" style="display: inline;" value=\' + hora_datos.id + \' onchange="check_hora_seleccionada(\' + hora_datos.id + \');" /><span id="span_hora_seleccionada_\' + hora_datos.id + \'"> \'+ hora +\'</span> <span class="cupos-horas"> - \' + hora_datos.cupo + \' lugares</span></label>\');
                                                    }
                                                  }
                                                });

                                                if($("#horas_cupos_disponibles_maniana").html() === "<p>Por la mañana</p>"){
                                                    $("#horas_cupos_disponibles_maniana").append(\'<p class="no-horas-disponibles">No hay horarios disponibles</p>\');
                                                }

                                                if($("#horas_cupos_disponibles_tarde").html() === "<p>Por la tarde</p>"){
                                                    $("#horas_cupos_disponibles_tarde").append(\'<p class="no-horas-disponibles">No hay horarios disponibles</p>\');
                                                }

                                                $("#horarios_lbl").show();

                                                $("html, body").animate({
                                                    scrollTop: $("#horarios_lbl").offset().top
                                                }, "fast");

                                                return;
                                             }
                                           });
                                       });
                                     }
                                  },
                                  beforeShowDay: disponibilidadFecha
                                });

                                //$("#agenda_div").hide().fadeIn();

                              })
                              .fail(function(jqXHR, textStatus, errorThrown) {
                                $("html, body").animate({ scrollTop: 0 }, "fast");
                                $(".validacion-error").html("Problemas con la agenda, intnte nuevamente. Gracias.").fadeOut().fadeIn();
                              });

                          })
                          .fail(function(jqXHR, textStatus, errorThrown ) {
                              $("html, body").animate({ scrollTop: 0 }, "fast");
                              $(".validacion-error").html("Problemas con la agenda, intnte nuevamente. Gracias.").fadeOut().fadeIn();
                          });
                          }else{
                          $("html, body").animate({ scrollTop: 0 }, "fast");
                              $(".validacion-error").html("Esta reserva no ha sido confirmada. para editarla primero debe eliminar dicha reserva").fadeOut().fadeIn();
                          }
}
                  </script>';


        return $display;
    }

    public function backendExtraFields() {

        if (isset($this->extra->url_base)) {
            $url_base = $this->extra->url_base;
        } else {
            $url_base = '';
        }

        if (isset($this->extra->id_empresa)) {
            $id_empresa = $this->extra->id_empresa;
        } else {
            $id_empresa = '';
        }

        if (isset($this->extra->id_agenda)) {
            $id_agenda = $this->extra->id_agenda;
        } else {
            $id_agenda = '';
        }

        if (isset($this->extra->token)) {
            $token = $this->extra->token;
        } else {
            $token = '';
        }

        if (isset($this->extra->datos_ws_json)) {
            $datos_ws_json = $this->extra->datos_ws_json;
        } else {
            $datos_ws_json = '';
        }

        if (isset($this->extra->id_recurso)) {
            $id_recurso = $this->extra->id_recurso;
        } else {
            $id_recurso = '';
        }
        if (isset($this->extra->datos_ws_cod_tramite)) {
            $datos_ws_cod_tramite = $this->extra->datos_ws_cod_tramite;
        } else {
            $datos_ws_cod_tramite = '';
        }
        if (isset($this->extra->datos_ws_nom_tramite)) {
            $datos_ws_nom_tramite = $this->extra->datos_ws_nom_tramite;
        } else {
            $datos_ws_nom_tramite = '';
        }

        if (isset($this->extra->documento_tramite)) {
            $documento_tramite = $this->extra->documento_tramite;
        } else {
            $documento_tramite = '';
        }

        if (isset($this->extra->nombre_tramite)) {
            $nombre_tramite = $this->extra->nombre_tramite;
        } else {
            $nombre_tramite = '';
        }

        if (isset($this->extra->email_tramite)) {
            $email_tramite = $this->extra->email_tramite;
        } else {
            $email_tramite = '';
        }

        $display = '<div class="control-group"><div><label for="url_base">URL Base</label><input id="url_base" type="text" name="extra[url_base]" value="' . $url_base . '"></div></div>';
        $display .= '<div class="control-group"><div><label for="id_empresa">Empresa ID</label><input id="id_empresa" type="text"  name="extra[id_empresa]" value="' . $id_empresa . '"></div></div>';
        $display .= '<div class="control-group"><div><label for="id_agenda">Agenda ID</label><input id="id_agenda" type="text" name="extra[id_agenda]" value="' . $id_agenda . '"></div></div>';
        $display .= '<div class="control-group"><div><label for="id_recurso">Recurso ID</label><input id="id_recurso" type="text" name="extra[id_recurso]" value="' . $id_recurso . '"></div></div>';
        $display .= '<div class="control-group"><div><label for="token">Token</label><input id="token" type="text" name="extra[token]" value="' . $token . '"></div></div>';
        $display .= '<div class="control-group"><div><label for="cod_tramite">Código del trámite</label><input id="cod_tramite" type="text" name="extra[datos_ws_cod_tramite]" value="' . $datos_ws_cod_tramite . '"></div></div>';
        $display .= '<div class="control-group"><div><label for="nom_tramite">Nombre del trámite</label><input id="nom_tramite" type="text" name="extra[datos_ws_nom_tramite]" value="' . $datos_ws_nom_tramite . '"></div></div>';
        $display .= '<div class="control-group"><div><label for="documento_tramite">Documento</label><input id="documento_tramite" type="text" name="extra[documento_tramite]" value="' . $documento_tramite . '"></div></div>';
        $display .= '<div class="control-group"><div><label for="nombre_tramite">Nombre</label><input id="nombre_tramite" type="text" name="extra[nombre_tramite]" value="' . $nombre_tramite . '"></div></div>';
        $display .= '<div class="control-group"><div><label for="email_tramite">Email</label><input id="email_tramite" type="text" name="extra[email_tramite]" value="' . $email_tramite . '"></div></div>';
        $display .= '<div class="control-group"><div><label for="datos_ws_json">JSON de reserva</label><textarea id="datos_ws_json" style="min-height: 0px;"class="input-xxlarge" name="extra[datos_ws_json]"
      placeholder="@@variable || json">' . $datos_ws_json . '</textarea></div></div>';

        return $display;
    }

    public function backendExtraValidate() {
        $CI = &get_instance();
        $CI->form_validation->set_rules('extra[url_base]', 'URL Base', 'trim|required');
        $CI->form_validation->set_rules('extra[id_empresa]', 'Empresa ID', 'required|is_natural');
        $CI->form_validation->set_rules('extra[id_agenda]', 'Agenda ID', 'required|validar_id_recurso');

        if ($CI->input->post('extra')['id_recurso']) {
            $CI->form_validation->set_rules('extra[id_recurso]', 'Recurso ID', 'validar_id_recurso');
        }

        $CI->form_validation->set_rules('extra[token]', 'Token', 'trim|required');
        $CI->form_validation->set_rules('extra[datos_ws_cod_tramite]', 'Código del trámite', 'trim|required');
        $CI->form_validation->set_rules('extra[email_tramite]', 'Email', 'trim|required');
        $CI->form_validation->set_rules('extra[nombre_tramite]', 'Nombre', 'trim|required');
        $CI->form_validation->set_rules('extra[documento_tramite]', 'Documento', 'trim|required');
        $CI->form_validation->set_rules('extra[datos_ws_json]', 'JSON de reserva', 'trim|required');
        if (!$CI->input->post('datos')['0']) {
            $CI->form_validation->set_rules('datos_sae', 'Datos de SAE', 'required');
        }
    }

    // Method: POST, GET
    private function call_api_agenda($method, $url, $data = false) {
        try {
            $curl = curl_init();

            switch ($method) {
                case "POST":
                    curl_setopt($curl, CURLOPT_POST, 1);

                    if ($data) {
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                    }
                    break;
                case "GET":
                    if ($data) {
                        $url = sprintf("%s?%s", $url, http_build_query($data));
                    }
                    break;
                default:
                    throw new Exception('Metodos soportados: GET o POST');
                    break;
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
                $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
                fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR CURL: ' . $curl_error . ' (http code: ' . $http_code . ')' . "\n");
                fclose($log);
                throw new Exception($curl_error);
            }

            return json_decode($result);
        } catch (Exception $e) {
            $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
            fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR EXCEPTION: ' . $e->getMessage() . "\n");
            fclose($log);
            throw new Exception($e->getMessage());
        }
    }

    public function clonarDatos($etapa, $valor, $nombre) {
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre, $etapa);
        if ($datos_seguimiento) {
            $datos_seguimiento->delete();
        }
        $datos_seguimiento = new DatoSeguimiento();
        $datos_seguimiento->nombre = $nombre;
        $datos_seguimiento->valor = $valor;
        $datos_seguimiento->etapa_id = $etapa;
        $datos_seguimiento->save();
    }

    public function clonarAgenda($etapa_id) {
        $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->nombre . '__clonada', $etapa_id);
        if (!$datos_seguimiento) {
            $this->clonarDatos($etapa_id, "1", $this->nombre . "__clonada");

            $regla = new Regla('@@' . $this->nombre . "_token_reservas");
            $datos_agenda = $regla->getExpresionParaOutput($etapa_id);
            if ($datos_agenda != '') {
                $this->clonarDatos($etapa_id, $datos_agenda, $this->nombre . "_token_reservas");
            }

            $regla = new Regla('@@' . $this->nombre . "_empresa");
            $datos_agenda = $regla->getExpresionParaOutput($etapa_id);
            if ($datos_agenda != '') {
                $this->clonarDatos($etapa_id, $datos_agenda, $this->nombre . "_empresa");
            }

            $regla = new Regla('@@' . $this->nombre . "_agenda");
            $datos_agenda = $regla->getExpresionParaOutput($etapa_id);
            if ($datos_agenda != '') {
                $this->clonarDatos($etapa_id, $datos_agenda, $this->nombre . "_agenda");
            }

            $regla = new Regla('@@' . $this->nombre . "_codTramite");
            $datos_agenda = $regla->getExpresionParaOutput($etapa_id);
            if ($datos_agenda != '') {
                $this->clonarDatos($etapa_id, $datos_agenda, $this->nombre . "_codTramite");
            }

            $regla = new Regla('@@' . $this->nombre . "__fecha_modificacion");
            $datos_agenda = $regla->getExpresionParaOutput($etapa_id);
            if ($datos_agenda != '') {
                $this->clonarDatos($etapa_id, $datos_agenda, $this->nombre . "__fecha_modificacion");
            }

            $regla = new Regla('@@' . $this->nombre . "__json_reserva");
            $datos_agenda = $regla->getExpresionParaOutput($etapa_id);
            if ($datos_agenda != '') {
                $this->clonarDatos($etapa_id, $datos_agenda, $this->nombre . "__json_reserva");
            }

            $regla = new Regla('@@' . $this->nombre . "_confirmada_reservas");
            $datos_agenda = $regla->getExpresionParaOutput($etapa_id);
            if ($datos_agenda != '') {
                $this->clonarDatos($etapa_id, $datos_agenda, $this->nombre . "_confirmada_reservas");
            }

            $regla = new Regla('@@' . $this->nombre . "_fecha_confirmacion");
            $datos_agenda = $regla->getExpresionParaOutput($etapa_id);
            if ($datos_agenda != '') {
                $this->clonarDatos($etapa_id, $datos_agenda, $this->nombre . "_fecha_confirmacion");
            }

            $regla = new Regla('@@' . $this->nombre . "_reservas");
            $datos_agenda = $regla->getExpresionParaOutput($etapa_id);
            if ($datos_agenda != '') {
                $this->clonarDatos($etapa_id, $datos_agenda, $this->nombre . "_reservas");
            }

            $regla = new Regla('@@' . $this->nombre . "_recurso");
            $datos_agenda = $regla->getExpresionParaOutput($etapa_id);
            if ($datos_agenda != '') {
                $this->clonarDatos($etapa_id, $datos_agenda, $this->nombre . "_recurso");
            }
        }
    }

}

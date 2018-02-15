<?php
require_once('campo.php');

class CampoAgendaSae extends Campo{

    public $requiere_nombre=true;
    public $requiere_datos=false;
    public $estatico=false;

    function setTableDefinition() {
        parent::setTableDefinition();

        $this->hasColumn('readonly','bool',0,array('default'=>0));
    }

    function setUp() {
        parent::setUp();
        $this->setTableName("campo");
    }

    protected function display($modo, $dato, $etapa_id) {
      if (!$etapa_id) {
        return '<p data-fieldset="'.$this->fieldset.'" agenda-campo="agenda_sae">'.$this->etiqueta.'</p>';
      }

      preg_match('/('. $etapa_id .')\/([0-9]*)/', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $match);

      if(!$match) {
        $secuencia = 0;
        $paso_numero = 1;
      }
      else {
        $secuencia = (int)$match[2];
        $paso_numero = $secuencia+2;
      }

      $dato_agenda = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->nombre, $etapa_id);

      if($dato_agenda){
        $datos_comfirmacion = $dato_agenda->valor;
        $display  = '<div class="control-group">';
        $display .=   '<div data-fieldset="'.$this->fieldset.'" style="margin: auto;">';
        $display .=     '<p class="dialogo validacion-success " id="titulo_reserva_confirmada">
                          Reserva confirmada '.$datos_comfirmacion->fecha_confirmacion.' hs.</p>';
        $display .=     '<p>Serie y número: '.$datos_comfirmacion->serieNumero.'</p>';
        $display .=     '<p>Código de cancelación: '.$datos_comfirmacion->codigoCancelacion.'</p>';
        $display .=     '<p>Código de trazabilidad: '.$datos_comfirmacion->codigoTrazabilidad.'</p>';
        $display .=     '<p>Mensaje: '.$datos_comfirmacion->textoTicket.'</p>';
        $display .=     '<input id="'.$this->id.'" type="hidden" name="'.$this->nombre.'" value="'.$this->nombre.'|'.$etapa_id.'">';
        $display .=   '</div>';
        $display .= '</div>';
        return $display;
      }else if ($modo == 'visualizacion'){
        $display  = '<div class="control-group">';
        $display .=   '<div data-fieldset="'.$this->fieldset.'" style="margin: auto;">';
        $display .=     '<p class="dialogo validacion-warning" id="titulo_reserva_no_confirmada">
                          Reserva no realizada </p>';
        $display .=   '</div>';
        $display .= '</div>';
        return $display;
      }

      //se permiten configurar agenda con datos de variables @@
      $regla = new Regla($this->extra->token);
      $token_con_variables = $regla->getExpresionParaOutput($etapa_id);

      $regla = new Regla($this->extra->id_empresa);
      $id_empresa_con_variables = $regla->getExpresionParaOutput($etapa_id);

      $regla = new Regla($this->extra->id_agenda);
      $id_agenda_con_variables = $regla->getExpresionParaOutput($etapa_id);

      $regla = new Regla($this->extra->url_base);
      $url_base_con_variables = $regla->getExpresionParaOutput($etapa_id);


      //dejamos realizar la reserva no estamos en modo visualizacion ni tenemos el dato de la agenda.
      $datos_api = array(
        "token" => $token_con_variables,
        "idEmpresa"=> $id_empresa_con_variables,
        "idAgenda"=> $id_agenda_con_variables,
        "idioma"=> "es"
      );

      try{
        $resultado = $this->call_api_agenda('POST',$url_base_con_variables.'/recursos_por_agenda', json_encode($datos_api));
      }
      catch(Exception $e){
        $display  = '<div class="control-group">';
        $display .=   '<div class="controls" data-fieldset="'.$this->fieldset.'">';
        $display .=     '<p style="background: #e2e9ef;padding: 19px;text-align: center;margin-left: -238px;">'.$this->etiqueta.' no se encuentra disponible en este momento. Intente nuevamente más tarde, gracias.</p>';
        $display .=     '<input id="'.$this->id.'" type="hidden" name="'.$this->nombre.'" value="'.$this->nombre.'|'.$etapa_id.'">';
        $display .=   '</div>';
        $display .= '</div>';

        $display .= '<script>
                      $(".validacion-error").html("'.$this->etiqueta.' no se encuentra disponible en este momento. Intente nuevamente más tarde, gracias.").fadeOut().fadeIn();
                      $("html, body").animate({ scrollTop: 0 }, "fast");
                    </script>';

        return $display;
      }

      $regla = new Regla($this->extra->datos_ws);
      $datos_ws_con_variables = $regla->getExpresionParaOutput($etapa_id);
      $display .=   '<div>';

      $display .= '<div id="ubicacion_div" data-fieldset="'.$this->fieldset.'">';
      $display .=   '<div class="control-group">';
      $display .=     '<span class="control-label" data-fieldset="'.$this->fieldset.'" >Seleccionar ubicación:</span>';

      $display .=     '<div class="controls">';
      $display .=       '<div class="row-fluid radio">';
      $display .=         '<div class="span6">';
                            foreach ($resultado->recursos as $recurso) {
                              if($recurso->latitud){
                                $latitud = $recurso->latitud;
                              } else{
                                $latitud = 0;
                              }
                              if($recurso->longitud){
                                $longitud = $recurso->longitud;
                              } else{
                                $longitud = 0;
                              }
                              if($recurso->telefono){
                                $telefono = '\''.$recurso->telefono.'\'';
                              } else{
                                $telefono = 0;
                              }
                              if($recurso->direccion){
                                $direccion = '\''.$recurso->direccion.'\'';
                              }else{
                                $direccion = 0;
                              }

                              //remplazo comillas para que no de errores js
                              $telefono = str_replace('"','',$telefono);
                              $telefono =  str_replace("'",'',$telefono);
                              $direccion = str_replace('"','',$direccion);
                              $direccion =  str_replace("'",'',$direccion);

                              $parametros_js = $this->id.','.$latitud.','.$longitud.',\''.$telefono.'\',\''.$direccion.'\',true,'.$recurso->id;

                              $display .= '<label for="'.$recurso->id.'_'.$this->id.'"><input type="radio" name="recurso_'.$this->id.'" id="'.$recurso->id.'_'.$this->id.'"  value="'.$recurso->id.'" onchange="recurso_seleccionado('.$parametros_js.');">'.$recurso->nombre.'</label>';
                            }
      $display .=         '</div><!--fin span6-->';

      $display .=         '<div class="span6">';
      $display .=           '<div id="mapa_'.$this->id.'" class="map"></div>';
      $display .=           '<p id="direccion_recurso_'.$this->id.'"></p>';
      $display .=           '<p id="telefono_recurso_'.$this->id.'"></p>';
      $display .=           '<div class="recuadro">'.$resultado->textoPaso1.'</div>';
      $display .=         '</div><!--fin span6-->';
      $display .=       '</div><!--fin row-fluid-->';
      $display .=     '</div><!--fin controls-->';
      $display .=   '</div><!--fin control-group-->';
      $display .= '</div><!--fin ubicacion_div-->';

      $display .= '<div id="agenda_div_ancla"></div>';
      $display .= '<div id="agenda_div" style="display:none;" data-fieldset="'.$this->fieldset.'">';
      $display .=   '<hr>';
      $display .=   '<div class="control-group">';
      $display .=     '<span class="control-label">Preferencia de horario:</span>';
      $display .=     '<div class="controls" id="preferencia_horarios">';
      $display .=       '<label for="filto_cualquiera" class="radio">
                          <input type="radio" id="filto_cualquiera" name="pref_horarios" value="cualquier" onchange="preferencia_hora_seleccionada();" checked/>
                          Cualquier horario</label>

                        <label for="filto_maniana" class="radio">
                          <input type="radio" id="filto_maniana" name="pref_horarios" value="maniana" onchange="preferencia_hora_seleccionada();"/>
                          Solo por la mañana</label>

                        <label for="filto_tarde" class="radio">
                          <input type="radio" id="filto_tarde" name="pref_horarios" value="tarde" onchange="preferencia_hora_seleccionada();"/>
                          Solo por la tarde</label>';
      $display .=     '</div><!--fin controls-->';
      $display .=   '</div><!--fin control-group-->';

      $display .=   '<hr>';
      $display .=   '<div class="control-group">';
      $display .=     '<span class="control-label" data-fieldset="'.$this->fieldset.'">Seleccionar día:</span>';

      $display .=     '<div class="controls">';
      $display .=       '<div class="row-fluid">';
      $display .=         '<div class="span6">';
      $display .=           '<div id="calendario"></div>';
      $display .=           '<input id="fechas_disponibles" title="fechas disponibles"  type="hidden" name="fechas_disponibles" value="" disabled="disabled">';
      $display .=           '<input id="dia_seleccionado" title="dia seleccionado" type="text" name="dia_seleccionado" value="" disabled>';
      $display .=           '<input id="hora_seleccionada" title="hora seleccionado"  type="hidden" name="hora_seleccionada" value="">';
      $display .=           '<input id="sin_fechas_disponibles" title="sin fechas disponibles" type="text" name="sin_fechas_disponibles" value="" disabled>';
      $display .=         '</div><!--fin span6-->';

      $display .=         '<div class="span6">';
      $display .=           '<div id="calendario_texto_fijo" class="recuadro">
                              <ul class="tips pasoTexto">
                                <li>Los días marcados en color verde tienen turnos disponibles</li>
                                <li>Seleccione el día de su preferencia haciendo click con el mouse</li>
                                <li>Luego de seleccionar el día, debajo del calendario se mostrarán los horarios disponibles para ese día</li>
                                <li>Seleccione un horario para continuar con la reserva</li>
                              </ul>
                            </div>';
      $display .=           '<div id="calendario_texto_dinamico" class="recuadro"></div>';
      $display .=         '</div><!--fin span6-->';
      $display .=       '</div><!--fin row-fluid-->';
      $display .=     '</div><!--fin controls-->';
      $display .=   '</div><!--fin control-group-->';


      $display .=   '<div id="horarios_lbl">';
      $display .=     '<hr>';
      $display .=     '<div class="control-group">';
      $display .=       '<span class="control-label">Horarios disponibles:<span class="comentario">Zona horaria America/Montevideo</span></span>';

      $display .=       '<div class="controls">';
      $display .=         '<div class="row-fluid">';
      $display .=           '<div class="span6">';
      $display .=             '<div id="horas_cupos_disponibles_maniana"></div>';
      $display .=           '</div><!--fin span6-->';

      $display .=           '<div class="span6">';
      $display .=             '<div id="horas_cupos_disponibles_tarde"></div>';
      $display .=           '</div><!--fin span6-->';
      $display .=         '</div><!--fin row-fluid-->';
      $display .=       '</div><!--fin controls-->';
      $display .=     '</div><!--fin control-group-->';
      $display .=   '</div><!--fin horarios_lbl-->';

      $display .=   '<div id="confrmar_hora_div_ancla"></div>';

      $display .=   '<div style="display:none;" id="confrmar_hora_div">';
      $display .=     '<hr>';
      $display .=     '<div class="control-group">';
      $display .=       '<span class="control-label">Verificar fecha y hora:</span>';

      $display .=       '<div class="controls">';
      $display .=         '<div id="confirmar_fecha_hora_seleccionada" class="msg-reserva"></div>';
      $display .=       '</div><!--fin controls-->';
      $display .=     '</div><!--fin control-group-->';
      $display .=   '</div><!--fin confrmar_hora_div-->';

      $display .=   '<div style="display:none;" id="confirmar_reserva_div">';
      $display .=     '<hr>';
      $display .=     '<input type="button" class="btn btn-secundary btn-lg confirmar-reserva" id="confirmar_reserva"  onClick="confirmar_reserva_click();" value="Confirmar Reserva"/>';

                      if($this->validacion){
                        $CI = &get_instance();

                        //si el funcionario SI esta actuando como ciudadano y requiere agendar
                        if($this->requiere_agendar && UsuarioSesion::usuarioMesaDeEntrada() && $CI->session->userdata('id_usuario_ciudadano')){
                          $display .= '<span class="comentario-boton" >*Requerido para continuar el trámite</span>';
                        }

                        //si el funcionario NO esta actuando como ciudadano
                        if(!$CI->session->userdata('id_usuario_ciudadano')){
                          $display .= '<span class="comentario-boton" >*Requerido para continuar el trámite</span>';
                        }
                      }

      $display .=   '</div><!--fin confirmar_reserva_div-->';

      $display .= '</div><!--fin agenda_div-->';

      $display .= '<div style="display:none;" id="reserva_confirmada_ok" data-fieldset="'.$this->fieldset.'">';
      $display .=   '<p class="dialogo validacion-success" id="titulo_reserva_confirmada"></p>';
      $display .=   '<p id="num_serie_reserva"></p>';
      $display .=   '<p id="cod_cancelacion_reserva"></p>';
      $display .=   '<p id="cod_trazabilidad_reserva"></p>';
      $display .=   '<p id="texto_ticket_reserva"></p>';
      $display .= '</div><!--fin reserva_confirmada_ok-->';

      $display .= '<input id="'.$this->id.'" type="hidden" name="'.$this->nombre.'" value="'.$this->nombre.'|'.$etapa_id.'">';


      $display .='</div>';

      $display .='<script type="text/javascript">

                  recurso_seleccionado('.$this->id.',0, 0, 0, 0, false,0);

                  function preferencia_hora_seleccionada(){
                    var pref  = $("input:radio[name=pref_horarios]:checked").val();

                    if(pref === "maniana"){
                      $("#horas_cupos_disponibles_tarde").css("display","none");
                      $("#horas_cupos_disponibles_maniana").css("display","block");
                    }

                    if(pref === "tarde"){
                      $("#horas_cupos_disponibles_maniana").css("display","none");
                      $("#horas_cupos_disponibles_tarde").css("display","block");
                    }

                    if(pref === ""){
                      $("#horas_cupos_disponibles_tarde").css("display","block");
                      $("#horas_cupos_disponibles_maniana").css("display","block");
                    }

                  }

                  function check_hora_seleccionada(id){
                      $("#confrmar_hora_div").hide().fadeIn();
                      $("#hora_seleccionada").val($("#span_hora_seleccionada_"+id).html().trim());
                      $("#confirmar_fecha_hora_seleccionada").html($("#dia_seleccionado").val() + " | " + $("#hora_seleccionada").val() +" hs.");

                      $("#confirmar_reserva_div").hide().fadeIn();

                      $("html, body").animate({
                          scrollTop: $("#confirmar_reserva_div").offset().top
                      }, "fast");
                  }

                  function confirmar_reserva_click(){

                    $("#confirmar_reserva").prop("disabled", true);
                    $("#confirmar_reserva").val("Enviando...");

                    $.ajax({
                      type: "POST",
                      dataType: "json",
                      url: document.Constants.host + "/etapas/agenda_sae_api_confirmar_reserva",
                      data: {
                              "method": "POST",
                              "url": "'.$url_base_con_variables.'/confirmar_reserva",
                              "token": "'.openssl_encrypt($token_con_variables, 'bf-ofb', 'simple_1058') .'",
                              "id_empresa": '.$id_empresa_con_variables.',
                              "id_agenda": '.$id_agenda_con_variables.',
                              "id_recurso": $("input[name=recurso_'.$this->id.']:checked").val(),
                              "id_disponibilidad": $("input[name=horas_disponibles_chk]:checked").val(),
                              "datos_reserva": "'.$datos_ws_con_variables.'",
                              "idioma": "es",
                              "etapa_id":"'.$etapa_id.'",
                              "paso_numero": '.$paso_numero.',
                              "nombre_campo": "'.$this->nombre.'",
                              "secuencia": '.$secuencia.'
                            }
                        })
                        .done(function(data) {

                          //Traza sub-proceso
                          $.ajax({
                            type: "POST",
                            dataType: "text",
                            url: document.Constants.host + "/etapas/trazabilidad_sub_proceso_agenda",
                            data: {
                                    "etapa_id":'.$etapa_id.',
                                    "secuencia": '.$secuencia.',
                                  }
                              }).fail(function(jqXHR, textStatus, errorThrown) {
                                  console.log("La solicitud a fallado al enviar traza");
                              });
                            //Termina traza sub-proceso

                          if(parseInt(data.resultado) === 1){

                            var fecha_hora_confirmada = $("#dia_seleccionado").val() + " | " + $("#hora_seleccionada").val();

                            $("#ubicacion_div").html("").fadeOut();
                            $("#agenda_div").html("").fadeOut();

                            $("#num_serie_reserva").html("Serie y número: " + data.serieNumero);
                            $("#cod_cancelacion_reserva").html("Código de cancelación: " + data.codigoCancelacion);
                            $("#cod_trazabilidad_reserva").html("Código de trazabilidad: " + data.codigoTrazabilidad);
                            $("#texto_ticket_reserva").html("Mensaje: " + data.textoTicket);
                            $("#titulo_reserva_confirmada").html("Reserva confirmada " + fecha_hora_confirmada +" hs.");
                            $("#'.$this->id.'").val("'.$this->nombre.'|'.$etapa_id.'");

                            $(".validacion-error").css("display","none");
                            $(".mensaje_error_campo").css("display","none");

                            $("#reserva_confirmada_ok").fadeIn();

                            $.ajax({
                              type: "POST",
                              dataType: "json",
                              url: document.Constants.host + "/etapas/confirmar_reserva_agenda_sae_interno_simple",
                              data: {
                                "campo_nombre": "'.$this->nombre.'",
                                "datos":  JSON.stringify(data),
                                "etapa_id":"'.$etapa_id.'",
                                "fecha":  fecha_hora_confirmada
                              }

                              }).fail(function(jqXHR, textStatus, errorThrown) {
                                console.log("La solicitud a fallado al guardar dato de seguimiento");
                              });

                          }
                          else if(parseInt(data.resultado) === 2){
                            $("html, body").animate({ scrollTop: 0 }, "fast");
                            $(".validacion-error").html("La reserva no se pudo realizar porque la disponibilidad indicada quedó sin cupos.").fadeOut().fadeIn();
                            $("#confirmar_reserva").prop("disabled", false);
                            $("#confirmar_reserva").val("Confirmar Reserva");
                          }
                          else{
                            $("html, body").animate({ scrollTop: 0 }, "fast");
                            if(data.error === "ya_existe_una_reserva_para_el_dia_especificado_con_los_datos_proporcionados"){
                              $(".validacion-error").html("La reserva no se pudo realizar: ya existe una reserva para el día especificado y los datos proporcionados.").fadeOut().fadeIn();
                            }
                            else{
                              if (data.error){
                                $(".validacion-error").html("La reserva no se pudo realizar: los datos proporcionados no cumplen con las validaciones necesarias en la agenda (" + data.error+").").fadeOut().fadeIn();
                              }else{
                                $(".validacion-error").html("La reserva no se pudo realizar: los datos proporcionados no cumplen con las validaciones necesarias en la agenda (" + data.errores+").").fadeOut().fadeIn();
                              }
                            }
                            $("#confirmar_reserva").prop("disabled", false);
                            $("#confirmar_reserva").val("Confirmar Reserva");
                          }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                          $("html, body").animate({ scrollTop: 0 }, "fast");
                          $(".validacion-error").html("No se pudo realizar la reserva, intente nuevamente. Gracias.").fadeOut().fadeIn();
                          $("#confirmar_reserva").prop("disabled", false);
                          $("#confirmar_reserva").val("Confirmar Reserva");
                        });
                  }

                  function recurso_seleccionado(id_campo,latitud, longitud, telefono, direccion, cambio_radio,id_recurso){

                    if(cambio_radio){

                      //MOSTRAR MAPA
                      $("#mapa_" + id_campo).html("");

                      if(telefono != 0)
                        $("#telefono_recurso_" + id_campo).html("Teléfono: " + telefono);
                      else
                        $("#telefono_recurso_" + id_campo).html("");

                      if(direccion != 0)
                        $("#direccion_recurso_" + id_campo).html("Dirección: " + direccion);
                      else
                        $("#direccion_recurso_" + id_campo).html("");

                	    lat2 = latitud;
                	    lon2 = longitud;
                	    zoom2 = 15;
                	    if((lat2=="" || lat2=="0") && (lon2=="" || lon2=="0")) {
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
                          anchor: [0.5, 46],
                          anchorXUnits: "fraction",
                          anchorYUnits: "pixels",
                          opacity: 0.75,
                          src: "'.base_url().'assets/img/pin.png"
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

                      $.blockUI({
                        message: \'<img src="'.site_url().'assets/img/ajax-loader.gif"></img>\',
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

                      $("#agenda_div").fadeOut();
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
                        url: document.Constants.host + "/etapas/agenda_sae_api_disponibilidades",
                        data: {
                                "method": "POST",
                                "url": "'.$url_base_con_variables.'/disponibilidades_por_recurso",
                                "token": "'.openssl_encrypt($token_con_variables, 'bf-ofb', 'simple_1058').'",
                                "id_empresa": '.$id_empresa_con_variables.',
                                "id_agenda": '.$id_agenda_con_variables.',
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


                            jQuery.getScript("'.base_url().'assets/js/calendario-sae.js")
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

                                $("#agenda_div").hide().fadeIn();

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
                        }
                      }
                  </script>';


      return $display;
    }

    public function backendExtraFields() {

      if(isset($this->extra->url_base)) {
        $url_base = $this->extra->url_base;
      }
      else {
        $url_base = '';
      }

      if(isset($this->extra->id_empresa)) {
        $id_empresa = $this->extra->id_empresa;
      }
      else {
        $id_empresa = '';
      }

      if(isset($this->extra->id_agenda)) {
        $id_agenda = $this->extra->id_agenda;
      }
      else {
        $id_agenda = '';
      }

      if(isset($this->extra->token)) {
        $token = $this->extra->token;
      }
      else {
        $token = '';
      }

      if(isset($this->extra->datos_ws)) {
        $datos_ws = $this->extra->datos_ws;
      }
      else {
        $datos_ws = '';
      }

      $display = '<label for="url_base">URL Base</label><input id="url_base" type="text" name="extra[url_base]" value="'.$url_base.'">';
      $display .= '<label for="id_empresa">Empresa ID</label><input id="id_empresa" type="text"  name="extra[id_empresa]" value="'.$id_empresa.'">';
      $display .= '<label for="id_agenda">Agenda ID</label><input id="id_agenda" type="text" name="extra[id_agenda]" value="'.$id_agenda.'">';
      $display .= '<label for="token">Token</label><input id="token" type="text" name="extra[token]" value="'.$token.'">';
      $display .= '<label for="datos_ws">Datos</label><textarea id="datos_ws" class="input-xxlarge" name="extra[datos_ws]"
      placeholder="agrupacion_campo,nombre_campo,valor_campo;agrupacion_campo,nombre_campo,valor_campo">'.$datos_ws.'</textarea>';

      return $display;
    }

    public function backendExtraValidate() {
        $CI=&get_instance();
        $CI->form_validation->set_rules('extra[url_base]', 'URL Base', 'required');
        $CI->form_validation->set_rules('extra[id_empresa]', 'Empresa ID', 'required');
        $CI->form_validation->set_rules('extra[id_agenda]', 'Agenda ID', 'required');
        $CI->form_validation->set_rules('extra[token]', 'Token', 'required');
        $CI->form_validation->set_rules('extra[datos_ws]', 'Datos', 'required');
    }

    // Method: POST, GET
    private function call_api_agenda($method, $url, $data = false) {
      try{
        $curl = curl_init();

        switch ($method){
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data){
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            case "GET":
              if ($data){
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

        if ($curl_errno > 0 || $http_code != 200){
            $log = fopen(__DIR__.'/../logs/agenda_sae.log', "a");
            fwrite($log, date("Y-m-d H:i:s").' --> ERROR CURL: '.$curl_error.' (http code: '.$http_code.')'."\n");
            fclose($log);
            throw new Exception($curl_error);
        }

        return json_decode($result);
      }
      catch(Exception $e){
          $log = fopen(__DIR__.'/../logs/agenda_sae.log', "a");
          fwrite($log, date("Y-m-d H:i:s").' --> ERROR EXCEPTION: '.$e->getMessage()."\n");
          fclose($log);
          throw new Exception($e->getMessage());
      }
    }
}

<?php

require_once('accion.php');

class AccionVariableObn extends Accion {

    public function displayForm() {
        $display = '<div class="form-horizontal">';
        $display.= '<div class="control-group">';
        $display.= '<label for="variable" class="control-label">Variable</label>';
        $display.='<div class="controls">';
        $display.='<input id="variable_obn" type="text" name="extra[variable_obn]" value="' . (isset($this->extra->variable_obn) ? $this->extra->variable_obn : '') . '" />';
        $display.='<input type="hidden" name="extra[obn]" value="' . ($this->extra ? $this->extra->obn : '') . '" />';
        $display.='<input type="hidden" name="extra[tipo]" value="' . ($this->extra ? $this->extra->tipo : '') . '" />';

        $display.='</div>';
        $display.='</div>';
        if (isset($this->extra->tipo)) {
            if ($this->extra->tipo == "query") {
                $display.= '<div class="control-group">';
                $display.='<div class="controls">';
                $display.='<div class="radio"><label><input type="radio" ' . (isset($this->extra->radio_tipo) ? ($this->extra->radio_tipo == 'query' ? "checked" : "") : '') . ' data-toggle="radio_tipo" name="extra[radio_tipo]" value="query">Obtener datos</label></div>';
                $display.='<div class="radio"><label><input type="radio" ' . (isset($this->extra->radio_tipo) ? ($this->extra->radio_tipo == 'count' ? "checked" : "") : '') . ' data-toggle="radio_tipo" name="extra[radio_tipo]" value="count">Obtener cantidad</label></div>';
                $display.='</div>';
                $display.='</div>';
                $display.= '<div style="display:none" id="div_query">';
                $display.= '<div class="control-group">';
                $display.= '<label class="control-label" for="consulta">Consulta</label>';
                $display.='<div class="controls">';
                $display.='<select name="extra[consulta]" id="consulta_query">';
                $display.='<option value="" disabled ' . (isset($this->extra->consulta) ? "" : 'selected') . ' >-- Seleccione la Consulta --</option>';
                $obn = Doctrine::getTable('ObnStructure')->findOneByIdentificador($this->extra->obn);
                if ($obn) {
                    $json = json_decode($obn->json);
                    $consultas = $json->OBN_CA;
                    foreach ($consultas as $value) {
                        if ($value->tipo == "query") {
                            $display.='<option ' . (isset($this->extra->consulta) ? ($this->extra->consulta == $value->nombre ? "selected" : "") : '') . ' value="' . $value->nombre . '" data-id-sql="' . $value->consulta . '" data-id-query="' . $value->consulta_sql . '" >' . $value->nombre . '</option>';
                        }
                    }
                }
                $display.='</select>';
                $display.='</div>';
                $display.='</div>';
                $display.= '<div class="control-group">';
                $display.= '<label class="control-label" for="coleccion">¿Colección?</label>';
                $display.='<div class="controls">';
                $display.='<input type="checkbox" id="coleccion" name="extra[coleccion]" ' . (isset($this->extra->coleccion) ? 'checked' : '') . ' />';
                $display.='</div>';
                $display.='</div>';
                $display.='</div>';

                $display.= '<div style="display:none" id="div_count">';
                $display.= '<div class="control-group">';
                $display.= '<label class="control-label" for="consulta">Consulta</label>';
                $display.='<div class="controls">';
                $display.='<select name="extra[consulta]" id="consulta_count">';
                $display.='<option value="" disabled selected>-- Seleccione la Consulta --</option>';
                $obn = Doctrine::getTable('ObnStructure')->findOneByIdentificador($this->extra->obn);
                if ($obn) {
                    $json = json_decode($obn->json);
                    $consultas = $json->OBN_CA;
                    foreach ($consultas as $value) {
                        if ($value->tipo == "count") {
                            $display.='<option ' . (isset($this->extra->consulta) ? ($this->extra->consulta == $value->nombre ? "selected" : "") : '') . ' value="' . $value->nombre . '" data-id-sql="' . $value->consulta . '" data-id-query="' . $value->consulta_sql . '" >' . $value->nombre . '</option>';
                        }
                    }
                }
                $display.='</select>';
                $display.='</div>';
                $display.='</div>';
                $display.='</div>';
                $display.= '<div class="control-group">';
                $display.='<div class="controls">';
                $display.='<input type="text" readonly id="query_sql" name="extra[query_sql]"  value="' . (isset($this->extra->query_sql) ? $this->extra->query_sql : '') . '"/>';
                $display.='</div>';
                $display.='</div>';
                $display.= '<div class="control-group">';
                $display.='<div class="controls" id="param_sql">';
                if (isset($this->extra->sql_param)) {
                    foreach ($this->extra->sql_param as $key => $value) {
                        $display.='<input placeholder="@@parametro_' . ($key + 1) . '" name="extra[sql_param][' . $key . ']" type="text" value="' . $value . '"><br><br>';
                    }
                }
                $display.='</div>';
                $display.='</div>';
            }
        }

        $display.='</div>';
        $display.='<script type="text/javascript">';
        $display.='$(document).ready(function () {';
        $display.='
            
            if($("input[type=radio][data-toggle=radio_tipo][checked]").val()=="query"){
            $("#div_query").show();
            $("#div_count").hide();
            }
            if($("input[type=radio][data-toggle=radio_tipo][checked]").val()=="count"){
            $("#div_count").show();
            $("#div_query").hide();
            }
            
            $("input[type=radio][data-toggle=radio_tipo]").change(function() {
            if($(this).val()=="count"){
            $("#div_count").show();
            $("#div_query").hide();            
            }else{
            $("#div_query").show();
            $("#div_count").hide();
            }
            $("#param_sql").html("");
            $( "#consulta_query" ).val("");
            $( "#consulta_count" ).val("");
            $("#query_sql").val("");
            $("#coleccion").removeAttr("checked");
        });';

        $display.='$( "#consulta_query" ).change(function() {
          $("#param_sql").html("");
          var id_query = $(this).val();
          var query = $("#consulta_query option:selected").attr("data-id-sql");
          var query_sql = $("#consulta_query option:selected").attr("data-id-query");
          var param = cuantasVecesAparece(query);
          $("#query_sql").val(query);
          for(var i=0; i<param; i++){
          var x = document.createElement("INPUT");
          x.setAttribute("type", "text");
          x.setAttribute("placeholder", "@@parametro_"+(i+1)+"");
          x.setAttribute("name", "extra[sql_param]["+i+"]");
          $("#param_sql").append(x);
          $("#param_sql").append("<br><br>");
          }
        });';
        $display.='$( "#consulta_count" ).change(function() {
            $("#param_sql").html("");
          var id_query = $(this).val();
          var query = $("#consulta_count option:selected").attr("data-id-sql");
          var query_sql = $("#consulta_count option:selected").attr("data-id-query");
          var param = cuantasVecesAparece(query);
          $("#query_sql").val(query_sql);
          for(var i=0; i<param; i++){
          var x = document.createElement("INPUT");
          x.setAttribute("type", "text");
          x.setAttribute("placeholder", "@@parametro_"+(i+1)+"");
          x.setAttribute("name", "extra[sql_param]["+i+"]");
          $("#param_sql").append(x);
          $("#param_sql").append("<br><br>");
          }
        });';
        $display.=' });';
        $display.='function cuantasVecesAparece(cadena){
            var indices = [];
            for(var i = 0; i < cadena.length; i++) {
              if (cadena[i].toLowerCase() === "?") indices.push(i);
            }
                  return indices.length;
          }';
        $display.='</script>';



        return $display;
    }

    public function validateForm($tipo = null) {
        $CI = & get_instance();
        $CI->form_validation->set_rules('extra[variable_obn]', 'Variable', 'required');
        if ($tipo == "query") {
            $CI->form_validation->set_rules('extra[consulta]', 'Consulta', 'required');
            $CI->form_validation->set_rules('extra[sql_param]', 'Parámetros', 'required');
        }
    }

    public function ejecutar(Etapa $etapa, $evento = null) {
        $CI = & get_instance();
        $CI->load->helper('buscar_obn_helper');
        $accion_obn = $this->extra;
        $obn = Doctrine::getTable('ObnStructure')->findOneByIdentificador($accion_obn->obn);
        $clase = crearNombreClaseObjeto($obn->identificador);
        if ($accion_obn->tipo == "ini") {
            $obn_nuevo = obtenerOBNVacio($obn->identificador);
            $dato = new ObnDatosSeguimiento();
            $dato->nombre = $accion_obn->variable_obn;
            $dato->valor = $obn_nuevo;
            $dato->etapa_id = $etapa->id;
            $dato->obn_id = $obn->id;
            $dato->save();
        } else if ($accion_obn->tipo == "set") {
            $error = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('obn_error_guardar', $etapa->id);
            if ($error)
                $error->delete();
            $error = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('obn_error', $etapa->id);
            if ($error)
                $error->delete();
            //guardar buscar el obn y buscar la instancia y actualizar los datos
            $dato = Doctrine::getTable('ObnDatosSeguimiento')->findByNombreObnIdHastaEtapa($accion_obn->variable_obn, $etapa->id, $obn->id);
            if ($dato) {
                $atributos_obn = json_decode($dato->valor);
                if ($atributos_obn->id == null) {
                    $obj_new = new $clase();
                } else {
                    $obj_new = Doctrine::getTable($clase)->find($atributos_obn->id);
                }
                $atributos = json_decode($obn->json);
                $atributos = $atributos->OBN_ATR;
                foreach ($atributos as $value) {
                    $atributo = $value->nombre;
                    $tipo_atributo = $value->tipo;
                    $valor_atr = isset($atributos_obn->$atributo) ? $atributos_obn->$atributo : false;
                    if ($tipo_atributo != "obn" && $tipo_atributo != "date" && $valor_atr && $value->multiple != 1 && isset($obj_new->$atributo)) {
                        $obj_new->$atributo = $valor_atr;
                    } else if ($tipo_atributo == "obn" && $valor_atr && $value->multiple != 1 && isset($obj_new->$atributo)) {
                        if ($valor_atr) {
                            $valor_atr_n = $valor_atr;
                            $obj_new->$atributo = $valor_atr_n->id;
                        }
                    } else if ($tipo_atributo == "obn" && $valor_atr && $value->multiple == 1 && isset($obj_new->$atributo)) {
                        if ($valor_atr) {
                            $valor_atr_arr = $valor_atr->parametros;
                            $atributo_valor = "";
                            foreach ($valor_atr_arr as $value2) {
                                $atributo_valor .= $value2 . ",";
                            }
                            $obj_new->$atributo = substr($atributo_valor, 0, -1);
                        }
                    } else if ($tipo_atributo == "date" && $valor_atr && isset($obj_new->$atributo)) {
                        $fecha = $valor_atr;
                        $fecha_ok = date("Y/m/d", strtotime($fecha));
                        $obj_new->$atributo = $fecha_ok;
                    } else if ($value->tipo != "obn" && $valor_atr && $value->multiple == 1 && isset($obj_new->$atributo)) {
                        $obj_new->$atributo = $valor_atr;
                    }
                }
                try {
                    $obj_new->save();
                    $atributos_obn->id = $obj_new->id;
                    $dato->valor = json_encode($atributos_obn);
                    $dato->save();
                } catch (Exception $exc) {
                    $error = new DatoSeguimiento();
                    $error->etapa_id = $etapa->id;
                    $error->nombre = 'obn_error_guardar';
                    $error->valor = "Se produjo un error inesperado. Contacte al administrador.";
                    $error->save();
                    $error = new DatoSeguimiento();
                    $error->etapa_id = $etapa->id;
                    $error->nombre = 'obn_error';
                    $error->valor = (string) $exc->getMessage();
                    $error->save();
                }
            } else {
                $error = new DatoSeguimiento();
                $error->etapa_id = $etapa->id;
                $error->nombre = 'obn_error_guardar';
                $error->valor = "Se produjo un error inesperado. Contacte al administrador.";
                $error->save();
                $error = new DatoSeguimiento();
                $error->etapa_id = $etapa->id;
                $error->nombre = 'obn_error';
                $error->valor = "La variable" . $accion_obn->variable_obn . "no se puede guardar porque no existe.";
                $error->save();
            }
        } else if ($accion_obn->tipo == "query" && !isset($accion_obn->coleccion)) {
            $obn_query = Doctrine::getTable('ObnQueries')->findOneByNombreAndIdObn($accion_obn->consulta, $obn->id);
            $consulta = $obn_query->nombre;
            $parametros = "";
            foreach ($accion_obn->sql_param as $value) {
                $regla = new Regla($value);
                $valor = $regla->getExpresionParaOutput($etapa->id);
                $parametros.=$valor . ",";
            }
            $parametros = substr($parametros, 0, -1);
            $parametros = explode(",", $parametros);

            $objeto = $clase::$consulta($parametros);
            if ($obn_query->tipo == "count") {
                $result = $objeto;
                $dato = Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($accion_obn->variable_obn, $etapa->id);
                if (!$dato)
                    $dato = new DatoSeguimiento();
                $dato->nombre = $accion_obn->variable_obn;
                $dato->valor = json_encode($result);
                $dato->etapa_id = $etapa->id;
                $dato->save();
            } else {
                $objeto_negocio = $objeto->limit(1)->execute();
                $result = json_decode(obtenerOBN($objeto_negocio[0]->id, $obn->identificador));
                $dato = Doctrine::getTable('ObnDatosSeguimiento')->findByNombreObnIdHastaEtapa($accion_obn->variable_obn, $etapa->id, $obn->id);
                if (!$dato)
                    $dato = new ObnDatosSeguimiento();
                $dato->nombre = $accion_obn->variable_obn;
                $dato->valor = json_encode($result);
                $dato->etapa_id = $etapa->id;
                $dato->obn_id = $obn->id;
                $dato->save();
            }
        }else if ($accion_obn->tipo == "query" && isset($accion_obn->coleccion)) {
            $obn_query = Doctrine::getTable('ObnQueries')->findOneByNombreAndIdObn($accion_obn->consulta, $obn->id);
            $clase = crearNombreClaseObjeto($obn->identificador);
            $consulta = $obn_query->nombre;
            $parametros = "";
            foreach ($accion_obn->sql_param as $value) {
                $regla = new Regla($value);
                $valor = $regla->getExpresionParaOutput($etapa->id, $obn->id);
                $parametros.=$valor . ",";
            }
            $parametros = substr($parametros, 0, -1);
            $parametros = explode(",", $parametros);
            $objeto = $clase::$consulta($parametros);
            $objeto_negocio["sql"] = $clase;
            $objeto_negocio["identificador"] = $obn->identificador;
            $objeto_negocio["consulta"] = $consulta;
            $parametro = $objeto->getParams();
            $objeto_negocio['parametros'] = $parametro['where'];
            $dato = Doctrine::getTable('ObnDatosSeguimiento')->findByNombreObnIdHastaEtapa($accion_obn->variable_obn, $etapa->id, $obn->id);
            if (!$dato)
                $dato = new ObnDatosSeguimiento();
            $dato->nombre = $accion_obn->variable_obn;
            $dato->valor = json_encode($objeto_negocio);
            $dato->etapa_id = $etapa->id;
            $dato->obn_id = $obn->id;
            $dato->save();
        }else if ($accion_obn->tipo == "get") {
            $clase = crearNombreClaseObjeto($obn->identificador);
            $objeto_negocio["sql"] = $clase;
            $objeto_negocio["prof"] = 0;
            $objeto_negocio["identificador"] = $obn->identificador;
            $objeto_negocio["consulta"] = "obtenerOBN";
            $dato = Doctrine::getTable('ObnDatosSeguimiento')->findByNombreObnIdHastaEtapa($accion_obn->variable_obn, $etapa->id, $obn->id);
            if (!$dato)
                $dato = new ObnDatosSeguimiento();
            $dato->nombre = $accion_obn->variable_obn;
            $dato->valor = json_encode($objeto_negocio);
            $dato->etapa_id = $etapa->id;
            $dato->obn_id = $obn->id;
            $dato->save();
        }

        //trazabilidad evento
        $this->trazar($etapa, $evento);
    }

    private function trazar($etapa, $evento) {
        if ($evento) {
            $CI = & get_instance();
            $CI->load->helper('trazabilidad_helper');

            $ejecutar_fin = false;

            preg_match('/(' . $etapa->id . ')\/([0-9]*)/', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $match);
            if (!$match) {
                $secuencia = 0;

                $ejecutar_fin = strpos($_SERVER['REQUEST_URI'], '/ejecutar_fin_form/' . $etapa->id);
                if ($ejecutar_fin) {
                    $secuencia = sizeof($etapa->getPasosEjecutables());
                }
            } else {
                $secuencia = (int) $match[2];
            }

            if ($ejecutar_fin) {
                enviar_traza_linea_evento_despues_tarea($etapa, $secuencia, $evento);
            } else {
                enviar_traza_linea_evento($etapa, $secuencia, $evento);
            }
        }
    }

}

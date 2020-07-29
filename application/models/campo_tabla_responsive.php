<?php

require_once('campo.php');

class CampoTablaResponsive extends Campo {

    public $requiere_datos = false;
    public $requiere_validacion = true;
    public $reporte = true;
    public $asociar_obn = true;

    public function formValidate($etapa_id = null) {
        //la validacion del componente tabla
        $CI = & get_instance();
        $validacion = $this->validacion;
        if ($etapa_id) {
            $regla = new Regla($this->validacion);
            $validacion = $regla->getExpresionParaOutput($etapa_id);
        }
        $tablametada = serialize($this->extra);
        $validacioStr = implode('|', $validacion) . '|' . 'validar_campos_tabla[' . $tablametada . ']';
        $CI->form_validation->set_rules($this->nombre, ucfirst($this->etiqueta), $validacioStr);
    }

    protected function display($modo, $dato, $etapa_id) {
        if ($etapa_id) {
            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            $regla = new Regla($this->valor_default);
            $valor_default = $regla->getExpresionParaOutput($etapa->id);
        } else {
            $valor_default = $this->valor_default;
        }

        if ($modo == 'visualizacion') {
            $this->readonly = 1;
        }

        $columns = $this->extra->columns;


        if (isset($this->extra->generar_fila_automatica)) {
            if ($this->extra->generar_fila_automatica) {
                $agregar_fila_scirpt = '$(document).ready(function() {
                                    if($("table[id=' . $this->id . ']").find("tr[role=row]").length - 1 === 0)
                                      $("#addR-' . $this->id . '").click();
            });';
            } else {
                $agregar_fila_scirpt = '';
            }
        } else {
            $agregar_fila_scirpt = '';
        }

        $display = '<div class="control-group">';
        $display.='<span class="h4" data-fieldset="' . $this->fieldset . '">' . $this->etiqueta . (!in_array('required', $this->validacion) ? ':' : '*:') . '</span>';
        $display.='<div class="" data-fieldset="' . $this->fieldset . '">';

        if ($this->ayuda_ampliada) {
            $display .= '<span><button type="button" class="tooltip_help_click" onclick="return false;"><span class="icn icn-circle-help"></span><span class="hide-read">Ayuda</span></button>';
            $display .= '<span class="hidden tooltip_help_line">' . strip_tags($this->ayuda_ampliada) . '</span></span>';
        }

        if ($this->ayuda)
            $display.='<span class="help-block">' . $this->ayuda . '</span>';

        $display.='<table id="' . $this->id . '" class="display dataTable" role="grid"><caption class="hide-read">' . $this->etiqueta . '</caption><thead></thead><tbody></tbody></table>';
        if (!$this->readonly) {
            $display .= '<span class="link-agregar"><input type="button" id="addR-' . $this->id . '" value="+ Agregar fila" class="btn-link"/></span>';
        }
        if (empty($valor_default)) {
            $valor_default = '[[]]';
        }
        $display.='<input class="input-xxlarge" type="hidden"  name="' . $this->nombre . '" value=\'' . ($dato ? json_encode($dato->valor) : $valor_default) . '\' />';
        $display.='</div>';
        $display.='</div>';

        $display .= '<script type="text/javascript">

            //dado el input con el valor de la tabla lo convierte al data set que se usa en el objeto DataTable de jquery.
            //se le debe pasar el objeto con las columnas de la tabla
            function getDataArray' . $this->id . '(headers){
              var data;

              try {

                //version 1.02 se hace esta validacion
                if ($("[name=\"' . $this->nombre . '\"]").val() == "\"\""){
                  data = [
                    new Array(headers.length)
                  ];
                  $("[name=\"' . $this->nombre . '\"]").val("");

                }else{
                  data = JSON.parse($("[name=\"' . $this->nombre . '\"]").val());
                }
              }
              catch(err) {
                data = [
                  new Array(headers.length)
                ];
              }

              var table_readonly = "' . ($this->readonly ? 1 : 0) . '";
              dataSet = [];
              var arr = [];
              if(table_readonly == 0) {
                $(data).each(function() {
                 this.unshift("");
                });
                arr = Object.keys(data).map(function(k) {
                  return data[k];
                });
              }
              else {
                arr = Object.keys(data).map(function(k) {
                  return data[k];
                });
              }


              try{
              if (table_readonly == 1){
                //no se tiene acciones, el if table_readonly se puso por el incidente
                //de que el link no se desplegaba en solo lectura
                dataSet = arr;
              }else if (typeof arr[0][1] != "undefined"){
                //las acciones generan que el al menos se tenga 2 elemeentos en la pos 0
                //entonces se deja vacio si no tiene nada en la pos 1
                dataSet = arr;
              }
            }
            catch(e) {
              dataSet = arr;
            }

            return dataSet

            }

            function accionFs' . $this->id . '(b, row_number) {
              updateTableDataInput' . $this->id . '();
              var data_t = JSON.parse($("input[name=\"' . $this->nombre . '\"]").val());

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

              $.ajax({
                type: "post",
                url: document.Constants.host + "/tabla/accion/' . (isset($etapa->id) ? $etapa->id : "") . '",
                data: {tabla_id:' . $this->id . ', tabla_data: data_t, row_number: row_number},
                complete: function(resultado) {
                  //setea el resultado en el input
                  var a = resultado.responseText.indexOf("ws_error");
                  if (!(resultado.responseText.indexOf("ws_error")>= 0)){
                    $("input[name=\"' . $this->nombre . '\"]").val(resultado.responseText);
                    var table = $("#' . $this->id . '").DataTable();
                    document.table = table;
                    //crea el array
                    var columnas = table.columns().toArray()[0];
                    var array = getDataArray' . $this->id . '(columnas);
                    //dibuja nuevamente la tabla
                    table.clear();
                    table.rows.add(array);
                    table.draw();
                  }else{
                    var error = JSON.parse(resultado.responseText);
                    $("<div title=\'Ha ocurrido un error\' class=\'validacion-popup validacion-error\'><div class=\'dialog-icon\'><span class=\'icn icn-circle-error-lg\'></span></div>" + error.ws_error+"</div>").dialog(
                        {modal: true,draggable: false,
                          resizable: false,
                          dialogClass: \'ui-dialog-tabla\',
                          buttons: {
                            \'Cerrar\': function() {
                              $(this).dialog("close");
                          }
                        }
                    });
                  }
                  $.unblockUI();

                }
              });
            }

            function updateTableDataInput' . $this->id . '() {
              var data_array = [];
              var table = $("#' . $this->id . '").DataTable();
              var table_data = table.$("input, select");

              $("#' . $this->id . ' tr").each(function() {
                var nueva_fila = [];
                $(this).find("input, select").each(function() {
                  if($(this).attr("name") != "eliminar" && $(this).attr("name") != "accion") {
                    nueva_fila.push($(this).val());
                  }
                });

                data_array.push(nueva_fila);
              });

              var data_array_final;
              if($("table.dataTable tr").size() > 2) {
                data_array_final = [data_array.slice(1)];
              }
              else {
                // data_array_final = [data_array[1]];
                data_array_final = [[data_array[1]]];
              }
              var new_val = JSON.stringify(data_array_final[0]);
              $("input[name=\"' . $this->nombre . '\"]").val(new_val);

            }


            function updateTableData' . $this->id . '() {
              $(".action-buttons-primary .btn-primary, #save_step").click(function() {
                updateTableDataInput' . $this->id . '();
              });
              if ($(".requiere_accion_disparador").bindFirst){
                $(".requiere_accion_disparador").bindFirst("click", function() {
                  updateTableDataInput' . $this->id . '();
                });
              }

            }

            document.combos' . $this->id . ' = [];

            function comboRendererResponsive' . $this->id . '(col) {
              var api_url = col.data.toString(); // Handsontable.helper.stringify(col.data);
              var dt = "";
              if (navigator.appVersion.toString().indexOf(".NET") > 0){
                var start_with = (api_url.indexOf("http") === 0);
              }
              else {
                var start_with = api_url.startsWith("http");
              }
              if((api_url != "") && (start_with)) {
                try {
                  $.ajax({
                    dataType: "json",
                    url: api_url,
                    success: function(json) {
                      try{
                        var options = "";
                        $(json).each(function(k, v) {
                          options += "<option>"+ this[Object.keys(this)[0]] +"</option>";
                        });
                        var id = "combo-dinamico-' . $this->id . '-" + col.id;
                        dt =  "<select title=\"" + col.header + "\" class=\""+ id +"\" name=\"grid_combo\">"+ options +"</select>";
                        document.combos' . $this->id . '[col.id] = dt;
                        $(".combo-dinamico-' . $this->id . '-" + col.id).html(dt);

                        //actualiza los valores del select en la tabla luego de cargado los options del servicio rest
                        if($("input[name=\"' . $this->nombre . '\"]").val() == "") {
                          var valores = [];
                        }
                        else {
                          var valores = JSON.parse($("input[name=\"' . $this->nombre . '\"]").val());
                        }

                        if(valores != "") {
                          var i = 0;
                          $("#' . $this->id . ' tbody tr").each(function() {
                            v = valores[i];

                            i++;
                            var j = 0;
                            if(typeof v != "undefined") {
                              $(this).find("td select, td input").each(function() {
                                if((typeof v[j] != "undefined") && (v[j] != "") && ($(this).attr("name") != "eliminar")) {
                                  $(this).val(v[j]);
                                  j++
                                }
                              });
                            }
                          });
                        }

                        updateTableData' . $this->id . '();
                      }
                      catch(error) {
                        console.log(error);
                      }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                     dt = "Error al generar combo.";
                      return dt;
                    }
                  });
                } catch(error) {
                  dt = "Error al generar combo.";
                  return dt;
                }
              }
              else if(api_url != "") {
                api_url = api_url.split(",");
                var options = "";
                $(api_url).each(function() {
                  var option = this.split(":");
                  options += "<option value=\""+ option[0].trim() +"\">" + option[1] + "</option>";
                });
                var id = "combo-dinamico-' . $this->id . '-" + col.id;

                dt =  "<select title=\"" + col.header + "\" class=\""+ id +"\" name=\"grid_combo\">"+ options +"</select>";
                document.combos' . $this->id . '[col.id] = dt;
                $(".combo-dinamico-' . $this->id . '-" + col.id).html(dt);
              }
              else {
                dt = "";
                return dt;
              }
            }
            $(document).ready(function() {
            $("#' . $this->id . '").on("change", ":input", function (event) {
        updateTableDataInput' . $this->id . '();
    });

            
              document.combos_cargados = [];
              document.columns = [];
              var mode = "' . $modo . '";
              var columns = ' . json_encode($columns) . ';

              columns = columns.map(function(c) {
                if(' . $this->readonly . ') {
                  switch(c.type) {
                    case "link":
                       return {title: c.header, "render":  function (data, type, row , meta) {
                          var row_value = (row[meta.col] == "undefined" ? "" : row[meta.col])
                          return "<a target=\"_blank\" href=\""+ row_value +"\">"+ row_value +"</a>";
                        }
                      }
                      break;
                    default:
                      return {title: c.header}
                  }
                }
                else {
                  switch(c.type) {
                    case "link":
                        return {title: c.header, "render": function (data, type, row , meta) {
                          if(type == "display") {
                            var row_value = (row[meta.col] == "undefined" ? "" : row[meta.col])
                            if (typeof row_value == "undefined"){
                              row_value = "";
                            }

                            var dt = "<input type=\"text\" value=\""+ row_value  +"\" name=\"link\" class=\"item_code\" title=\"" + c.header + "-" + meta.row + "\" />";
                             var api = new $.fn.dataTable.Api(meta.settings);
                             var $el = $("input, select, textarea", api.cell({row: meta.row, column: meta.col}).node());
                             // var $html = $(data).wrap("<div/>").parent();
                             if ($el.prop("tagName") === "INPUT") {
                                var valT = ($el.val() == "undefined" ? "" : $el.val());
                                dt = "<input type=\"text\" value=\""+ valT  +"\" name=\"link\" class=\"item_code\" title=\"" + c.header + "-" + meta.row + "\" />";
                             }
                           }
                           updateTableData' . $this->id . '();
                           return dt;
                        }
                      }
                      break;
                    case "combo":
                        // Obtiene los datos del servicio  para la columna
                        comboRendererResponsive' . $this->id . '(c);
                        return {title: c.header, "render":  function (data, type, row , meta) {
                          if(document.combos' . $this->id . '[c.id]) {
                            var dt = document.combos' . $this->id . '[c.id];
                            var row_value = (row[meta.col] == "undefined" ? "" : row[meta.col])
                            dt = dt.replace("value=\""+ row_value +"\"", "value=\""+ row_value +"\" selected");
                          }
                          else {
                            // todavia no termina de cargar los valores del servicio ajax
                            // se retorna con el texto Cargando....
                            var id = "combo-dinamico-' . $this->id . '-" + c.id;
                            var dt =  "<select title=\"" + c.header + "-" + meta.row + "\" class=\""+ id +"\" name=\"grid_combo\"><option>Cargando..</option></select>";
                          }
                          return dt;
                        }}
                      break;
                    default:
                      return {title: c.header, "render": function (data, type, row , meta) {
                        if(type == "display") {
                          var row_value = (row[meta.col] == "undefined" ? "" : row[meta.col])
                          if (typeof row_value == "undefined"){
                            row_value = "";
                          }
                          document.ro = row_value;
                          var dt = "<input type=\'text\' value=\'"+ row_value  +"\' name=\'position\' class=\'item_code\' title=\"" + c.header + "-" + meta.row + "\" >";
                           var api = new $.fn.dataTable.Api(meta.settings);
                           var $el = $("input, select, textarea", api.cell({row: meta.row, column: meta.col}).node());
                           // var $html = $(data).wrap("<div/>").parent();
                           if ($el.prop("tagName") === "INPUT") {
                              var valT = ($el.val() == "undefined" ? "" : $el.val());
                              dt = "<input type=\'text\' value=\'"+ valT  +"\' name=\'position\' class=\'item_code\' title=\"" + c.header + "-" + meta.row + "\" >";
                           }
                         }
                         updateTableData' . $this->id . '();
                         return dt;
                      }
                    }
                  }
                }
              });

              var headers = columns.map(function(c){
                return c.header;
              });

              dataSet = getDataArray' . $this->id . '(headers);
              var table_readonly = "' . ($this->readonly ? 1 : 0) . '";

              if(table_readonly == 0) {
                columns.unshift({title: "Acciones", class: "acciones_t", width: "3%", "render": function ( data, type, row , meta) {
                     var dt = "<input type=\'button\' id=\'' . $this->id . 'eliminar" +meta.row +  " \' value=\'eliminar\' name=\'eliminar\' onClick=\'removeFs' . $this->id . '(this);\' class=\'button-no-style icn icn-error-sm hide-text-read\' />";
                     if (Number.isInteger(' . $this->extra->accion_id . ')){
                       dt = dt + "<input type=\'button\' id=\'' . $this->id . 'accion" +meta.row +  "\' value=\'accion\' name=\'accion\' onClick=\'accionFs' . $this->id . '(this,"+ meta.row +");\' class=\'button-no-style icn icn-search-sm hide-text-read\' />";
                     }
                     return dt;
                  }
                });
              }

              try {
                $("#' . $this->id . '").DataTable({
                     data: dataSet,
                     columns: columns,
                     responsive: false,
                     paging: false,
                     ordering: false,
                     searching: false,
                     info: false,
                     select: false,
                     bDestroy: true,
                     language: {emptyTable: "Sin datos disponibles"},
                     autoWidth: false,
                     "columnDefs": [ {
                      targets: "_all",
                     "createdCell": function (td, cellData, rowData, row, col) {
                      if (true ) {
                        $(td).attr({"data-title": columns[col].title})
                      }
                    }
                  } ]
                  });
                }
                catch(error) {
                  console.log(error);
                }
              });

              $("#' . $this->id . '").on("keyup change", ".child input, .child select, .child textarea", function(e) {
                var table = $("#' . $this->id . '").DataTable();
                var $el = $(this);
                var rowIdx = $el.closest("ul").data("dtr-index");
                var colIdx = $el.closest("li").data("dtr-index");
                var cell = table.cell({row: rowIdx, column: colIdx}).node();
                $("input, select, textarea", cell).val($el.val());
                if($el.is(":checked")) {
                  $("input", cell).prop("checked", true);
                }
              });

              function appendRow' . $this->id . '() {
                 var t = $("#' . $this->id . '").DataTable();
                 var columnas = [];
                 var columns = ' . json_encode($columns) . ';
                 $(columns).each(function() {
                   columnas.push("");
                 });
                 var table_readonly = "' . ($this->readonly ? 1 : 0) . '";
                 if(table_readonly == 0) {
                   columnas.unshift({ title: "Acciones" ,"render": function ( data, type, row , meta) {
                        var dt = "<input type=\'button\' id=\'' . $this->id . 'eliminar" +meta.row +  "\' value=\'eliminar\' name=\'eliminar\' onClick=\'removeFs' . $this->id . '(this);\' class=\'button-no-style icn icn-error-sm hide-text-read\' />";
                        if (Number.isInteger(' . $this->extra->accion_id . ')){
                          dt = dt + "<input type=\'button\' id=\'' . $this->id . 'accion" +meta.row +  "\' value=\'accion\' name=\'accion\' onClick=\'accionFs' . $this->id . '(this,"+meta.row+");\' class=\'button-no-style icn icn-search-sm hide-text-read\' />";
                        }
                        return dt;
                     }
                   });
                 }
                 var node = t.row.add(columnas).draw().node();
                 var detail_row = "";
                 $(node).addClass("result-row");
                 node = node.outerHTML;
                 $(node).hide().fadeIn("normal");
                 updateTableData' . $this->id . '();
                     updateTableDataInput' . $this->id . '();
               }

               function removeFs' . $this->id . '(btnElement) {
                  var table =  $("#' . $this->id . '").DataTable();
                  var rowElement = $(btnElement).closest("tr");
                  var row =  table.row(rowElement);
                  row.remove();
                  table.draw(false);
                  updateTableData' . $this->id . '();
                  updateTableDataInput' . $this->id . '();
                }

                $("#addR-' . $this->id . '").off("click");
                $("#addR-' . $this->id . '").click(function(event) {
                  appendRow' . $this->id . '();
                });' . $agregar_fila_scirpt . '
            </script>';



        return $display;
    }

    public function backendExtraFields() {

        if (isset($this->extra->accion_id)) {
            $accion_id = $this->extra->accion_id;
        } else {
            $accion_id = '';
        }

        if (isset($this->extra->generar_fila_automatica) && $this->extra->generar_fila_automatica) {
            $generar_fila_automatica_ckecked = 'checked';
            $generar_fila_automatica_val = '1';
        } else {
            $generar_fila_automatica_ckecked = '';
            $generar_fila_automatica_val = '0';
        }
        $output = '<div class="control-group"><div>';
        $output = '<label for="accion_ws_id">Acción</label>';
        $output.='<select name="extra[accion_id]" id="accion_ws_id">';
        $output.='<option value="">Seleccionar</option>';
        foreach ($this->Formulario->Proceso->Acciones as $d)
            $output.='<option value="' . $d->id . '" ' . ($accion_id == $d->id ? 'selected' : '') . '>' . $d->nombre . '</option>';
        $output.='</select></div></div>';

        $accion_error = isset($this->extra->accion_error) ? $this->extra->accion_error : null;
        $output.= '<div class="control-group"><div>';
        $output.='<label for="accion_error">Variable con el  error</label>';
        $output.='<input type="text" id="accion_error" name="extra[accion_error]" placeholder="Varianle con el error de la accioón" value="' . ($accion_error ? $accion_error : '') . '" /></div></div>';
        $output.= '<div class="control-group"><div>';
        $output .= '<label for="generar_fila_automatica_chk">
                    <input id="generar_fila_automatica_chk" type="checkbox" name="generar_fila_automatica_name" value="" ' . $generar_fila_automatica_ckecked . '> Genera la primera fila automáticamente
                    <input id="generar_fila_automatica" type="hidden" name="extra[generar_fila_automatica]" value="' . $generar_fila_automatica_val . '">
                    </label></div></div>';

        $output .= '<script>
                    $(document).ready(function() {
                        $("#generar_fila_automatica_chk").change(function() {
                            if(this.checked) {
                              $("#generar_fila_automatica").val("1");
                            }
                            else{
                              $("#generar_fila_automatica").val("0");
                            }
                        });
                      });
                  </script>';

        $columns = array();
        if (isset($this->extra->columns))
            $columns = $this->extra->columns;
        $output .= '<div class="control-group">
            <div class="columnas">
                <script type="text/javascript">
                    $(document).ready(function() {
                      $(".tipo").on("change", function() {
                        switch($(this).val()) {
                          case "link":
                            $(this).parent().parent().find(".url").first().removeAttr("readonly");
                            $(this).parent().parent().find(".url").first().attr({"placeholder": "URL del enlace"});
                            $(this).parent().find(".url").first().addClass("hidden");
                            break;
                          case "combo":
                            //$(this).parent().parent().find(".url").first().removeAttr("readonly");
                            document.element = $(".url").first();
                            $(this).parent().find(".url").first().removeClass("hidden");
                            $(this).parent().parent().find(".url").first().attr({"placeholder": "URL del servicio"});
                            break;
                          default:
                            $(this).parent().parent().find(".url").first().attr({"readonly": ""});
                            $(this).parent().parent().find(".url").first().attr({"placeholder": ""});
                            $(this).parent().find(".url").first().addClass("hidden");
                        }
                      });

                        $("#formEditarCampo .columnas .nuevo").click(function(){
                            var pos=$("#formEditarCampo .columnas table tbody tr").size();
                            var html="<tr>";
                            html+="<td><label class=\'hidden-accessible\' for=\'etiqueta"+pos+"\'>etiqueta"+pos+"</label><input class=\'input-large\' id=\'etiqueta"+pos+"\' type=\'text\' name=\'extra[columns]["+pos+"][header]\' /></td>";
                            html+="<td><label class=\'hidden-accessible\' for=\'validacion"+pos+"\'>validacion"+pos+"</label><input class=\'input-small\' id=\'validacion"+pos+"\' type=\'text\' name=\'extra[columns]["+pos+"][validacion]\' /></td>";
                            html+="<td><label class=\'hidden-accessible\' for=\'variable_accion"+pos+"\'>variable_accion"+pos+"</label><input class=\'input-small\' id=\'variable_accion"+pos+"\' type=\'text\' name=\'extra[columns]["+pos+"][variable_accion]\' /></td>";
                            html+="<td><label class=\'hidden-accessible\' for=\'tipo"+pos+"\'>tipo"+pos+"</label><select class=\'input-small tipo\' id=\'tipo"+pos+"\' name=\'extra[columns]["+pos+"][type]\' ><option>text</option><option>numeric</option><option>link</option><option>combo</option></select><input type=\'text\' class=\'input-medium url hidden\' name=\'extra[columns]["+pos+"][data]\' /><input type=\"hidden\" value=\""+pos+"\" name=\"extra[columns]["+pos+"][id]\" /></td>";
                            html+="<td class=\'actions\'><button type=\'button\' class=\'btn btn-danger eliminar\'><span class=\'icon-trash icon-white\'></span></button></td>";
                            html+="</tr>";

                            $("#formEditarCampo .columnas table tbody").append(html);

                            $(".tipo").on("change", function() {
                              switch($(this).val()) {
                                case "link":
                                  $(this).parent().parent().find(".url").first().removeAttr("readonly");
                                  $(this).parent().parent().find(".url").first().attr({"placeholder": "URL del enlace"});
                                  $(this).parent().find(".url").first().addClass("hidden");
                                  break;
                                case "combo":
                                  //$(this).parent().parent().find(".url").first().removeAttr("readonly");
                                  document.element = $(".url").first();
                                  $(this).parent().find(".url").first().removeClass("hidden");
                                  $(this).parent().parent().find(".url").first().attr({"placeholder": "URL del servicio"});
                                  break;
                                default:
                                  $(this).parent().parent().find(".url").first().attr({"readonly": ""});
                                  $(this).parent().parent().find(".url").first().attr({"placeholder": ""});
                                  $(this).parent().find(".url").first().addClass("hidden");
                              }
                            });
                        });
                        $("#formEditarCampo .columnas").on("click",".eliminar",function(){
                            $(this).closest("tr").remove();
                        });
                    });
                </script>
                <h4>Columnas</h4>
                <button class="btn nuevo" type="button"><span class="icon-plus"></span> Nuevo</button>
                <table class="table" name="extra[columns]">
                  <caption class="hidden-accessible">Columnas</caption>
                    <thead>
                        <tr>
                            <th>Etiqueta</th>
                            <th>Validación</th>
                            <th>Variable</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </div>';

        if ($columns) {
            $i = 0;
            foreach ($columns as $key => $c) {
                $output.='
                <tr>
                    <td><label class="hidden-accessible" for="etiqueta' . $i . '">etiqueta' . $i . '</label><input class="input-large" id="etiqueta' . $i . '" type="text" name="extra[columns][' . $i . '][header]" value="' . $c->header . '" /></td>
                    <td><label class="hidden-accessible" for="validacion' . $i . '">validacion' . $i . '</label><input class="input-small" id="validacion' . $i . '" type="text" name="extra[columns][' . $i . '][validacion]" value="' . $c->validacion . '" /></td>
                    <td><label class="hidden-accessible" for="variable_accion' . $i . '">variable_accion' . $i . '</label><input class="input-small" id="variable_accion' . $i . '" type="text" name="extra[columns][' . $i . '][variable_accion]" value="' . $c->variable_accion . '" /></td>
                    <td><label class="hidden-accessible" for="tipo' . $i . '">tipo' . $i . '</label><select class="input-small tipo" id="tipo' . $i . '" name="extra[columns][' . $i . '][type]"><option ' . ($c->type == 'text' ? 'selected' : '') . '>text</option><option ' . ($c->type == 'numeric' ? 'selected' : '') . '>numeric</option><option ' . ($c->type == 'link' ? 'selected' : '') . '>link</option><option ' . ($c->type == 'combo' ? 'selected' : '') . '>combo</option></select><input type="text" class="input-medium url ' . ($c->type == 'combo' ? '' : 'hidden') . '" name="extra[columns][' . $i . '][data]" value="' . ($c->type == 'combo' ? $c->data : '') . '" /><input type="hidden" value="' . $i . '" name="extra[columns][' . $i . '][id]" /></td>
                    <td class="actions"><button type="button" class="btn btn-danger eliminar"><span class="icon-trash icon-white"></span></button></td>
                </tr>
                ';
                $i++;
            }
        }

        $output.='
        </tbody>
        </table>
        ';

        return $output;
    }

    public function backendExtraValidate() {
        $CI = &get_instance();
        $CI->form_validation->set_rules('extra[columns]', 'Columnas', 'required');
    }

}

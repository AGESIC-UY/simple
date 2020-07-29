<?php

require_once('campo.php');

class CampoTablaDatos extends Campo {

    public $requiere_datos = false;
    public $requiere_validacion = true;
    public $reporte = true;
    public $asociar_obn = true;

//    public function formValidate($etapa_id = null) {
//        //la validacion del componente tabla
//        $CI = & get_instance();
//        $validacion = $this->validacion;
//        if ($etapa_id) {
//            $regla = new Regla($this->validacion);
//            $validacion = $regla->getExpresionParaOutput($etapa_id);
//        }
//        $tablametada = serialize($this->extra);
//        $validacioStr = implode('|', $validacion) . '|' . 'validar_campos_tabla[' . $tablametada . ']';
//        $CI->form_validation->set_rules($this->nombre, ucfirst($this->etiqueta), $validacioStr);
//    }

    protected function display($modo, $dato, $etapa_id) {
        if ($etapa_id) {
            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            $regla = new Regla($this->valor_default);
            $valor_default = $regla->getExpresionParaOutput($etapa->id);
            $buscando_atributo2 = explode(".", $this->variable_obn);
            $atributo_obn = explode(".", $this->variable_obn);
            $variable = $buscando_atributo2[0];
            if (isset($buscando_atributo2[1])) {
                if ($buscando_atributo2[1][0] != "[") {
                    $variable = $buscando_atributo2[0] . "." . $buscando_atributo2[1];
                    if (isset($buscando_atributo2[2]) && isset($buscando_atributo2[3])) {
                        $indice = $buscando_atributo2[2];
                        $atributo2 = $buscando_atributo2[3];
                    } else if (isset($buscando_atributo2[2])) {
                        $indice = false;
                        $atributo2 = $buscando_atributo2[2];
                    }
                } else {
                    if (isset($buscando_atributo2[1]) && isset($buscando_atributo2[2])) {
                        $indice = $buscando_atributo2[1];
                        $atributo2 = $buscando_atributo2[2];
                    } else if (isset($buscando_atributo2[1])) {
                        $indice = false;
                        $atributo2 = $buscando_atributo2[1];
                    }
                }
            }
            $regla = new Regla($variable);
            $variable_obn = $regla->getExpresionParaOutput($etapa->id);
            $variable_obn = json_decode(htmlspecialchars_decode($variable_obn));
            if (isset($atributo2)) {
                if (isset($variable_obn->OBN)) {
                    $iden_atributo2 = $variable_obn->OBN;
                } else if (isset($variable_obn->sql)) {
                    $iden_atributo2 = $variable_obn->identificador;
                }
                $variable_obn = json_decode(htmlspecialchars_decode(obnAtributo2($iden_atributo2, $indice)));
                if (isset($variable_obn->$atributo2)) {
                    $variable_obn = $variable_obn->$atributo2;
                }
            }
        } else {
            $valor_default = $this->valor_default;
        }

        if ($modo == 'visualizacion') {
            $this->extra->tipo_tabla_datos = "visualizar";
        }

        $this->readonly = 1;

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

        if ($this->extra->tipo_tabla_datos != "visualizar" && $modo != 'visualizacion' && isset($etapa)) {
            $display.='<div class="btn-group">';
            if ($this->extra->tipo_tabla_datos == "abm") {
                $display.='<a class="btn btn-inverse salvarParcial" href="#" onclick="darAlta' . $this->id . '();"><span class="icon-file"></span> Alta</a>';

                if (isset($atributo_obn[1])) {
                    $display.='<a class="btn btn-inverse" href="#" onclick="return seleccionarAccion' . $this->id . '();"><span class="icon-upload"></span> Asociar</a>';
                }
            }
            if ($this->extra->tipo_tabla_datos == "asociar") {
                $display.='<a class="btn btn-inverse" href="#" onclick="return seleccionarAccion' . $this->id . '();"><span class="icon-upload"></span> Asociar</a>';
            }
            $display.='</div>';
        }
        $display.='<table id="' . $this->id . '" class="display dataTable" role="grid"><caption class="hide-read">' . $this->etiqueta . '</caption><thead></thead><tbody></tbody></table>';

        if (empty($valor_default)) {
            $valor_default = '';
        }
        $coleccion = isset($variable_obn->parametros) ? 1 : (isset($variable_obn->OBN) ? 0 : 1);
        $display.='<input class="input-xxlarge" type="hidden"  name="' . $this->nombre . '" value=\'\' />';
        $display.='<input class="input-xxlarge" type="hidden" id="' . $this->nombre . '_idobn_' . $this->id . '" name="' . $this->nombre . '_idobn" value=\'' . (isset($variable_obn->parametros) ? json_encode($variable_obn->parametros) : (isset($variable_obn->id) ? json_encode([$variable_obn->id]) : json_encode([]))) . '\' />';
        $display.='<input class="input-xxlarge" type="hidden" id="' . $this->nombre . '_iddelete_' . $this->id . '" name="' . $this->nombre . '_iddelete" value=\'' . json_encode([]) . '\' />';
        $display.='<input class="input-xxlarge" type="hidden" id="' . $this->nombre . '_idadd_' . $this->id . '" name="' . $this->nombre . '_idadd" value=\'' . json_encode([]) . '\' />';
        $display.='</div>';
        $display.='</div>';

        $display .= '<script type="text/javascript">
        var parametros=$("#' . ($this->nombre . '_idobn_' . $this->id) . '").val();
            //dado el input con el valor de la tabla lo convierte al data set que se usa en el objeto DataTable de jquery.
            //se le debe pasar el objeto con las columnas de la tabla
            function getDataArray' . $this->id . '(headers){
              var data;

              try {

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

              var table_readonly = "0";
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
                    console.log("Status: " + textStatus);
                    console.log(errorThrown);
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
            var form= "<div class=\"hidden\">";
            var url=window.location;
            form+="<form id=\"darAlt_' . $this->id . '\" action=\"' . site_url('obn_consultas/alta_obn') . '\" method=\"POST\">";
              form+= " <input name=\"variable_obn\" value=\"' . $this->variable_obn . '\">";
               form+= "<input name=\"etapa\" value=\"' . $etapa_id . '\">";
              form+= " <input name=\"secuencia\" value=\""+url+"\">";
              form+= " <input name=\"id\" value=\"\">";
              form+="  <input name=\"campo\" value=\"' . $this->id . '\">";
            form+="</form></div>";
            $(".ajaxForm").parent().append(form);
              document.combos_cargados = [];
              document.columns = [];
              var mode = "' . $modo . '";
              var columns = ' . json_encode($columns) . ';

              columns = columns.map(function(c) {
                if(' . $this->readonly . ') {
                  switch(c.type) {                    
                    default:
                      return {name:c.atributo,title: c.header}
                  }
                }
                else {
                  switch(c.type) {                    
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
              var table_readonly = "' . $this->extra->tipo_tabla_datos . '";
              var modo_tabla_editar = "' . ($modo == "visualizacion" ? 0 : 1) . '";

              if(table_readonly == "abm" && modo_tabla_editar==1) {
                columns.unshift({title: "Acciones", class: "acciones_t", width: "10%", "render": function ( data, type, row , meta) {
                        var dt="";
                        dt += "<input type=\'button\' id=\'' . $this->id . 'editar" +meta.row +  "\' value=\'editar\' name=\'editar\' onClick=\'editarFs' . $this->id . '(this);\' class=\'salvarParcial button-no-style icn icn-edit-sm hide-text-read\' title=\'Editar\' />";
                        ';
        if (isset($atributo_obn[1])) {
            $display .='dt += "<input type=\'button\' id=\'' . $this->id . 'eliminar" +meta.row +  " \' value=\'eliminar\' name=\'eliminar\' onClick=\'removeFs' . $this->id . '(this);\' class=\'button-no-style icn icn-error-sm hide-text-read\' title=\'Desasociar\'/>"; ';
        }

        $display .='dt += "<input type=\'button\' id=\'' . $this->id . 'eliminarInstancia" +meta.row +  "\' value=\'eliminarInstancia\' name=\'eliminarInstancia\' onClick=\'confirmation' . $this->id . '(this);\' class=\'button-no-style icn icn-delete-sm hide-text-read\'title=\'Eliminar\' />";
                        dt += "<input type=\'hidden\' id=\'' . $this->id . 'IdentificarInstancia" +meta.row +  "\' value=\'\' name=\'IdentificarInstancia\' class=\'button-no-style icn icn-edit-sm hide-text-read\' />";
                     
                return dt;
               } });
              }
               if(table_readonly == "asociar" && modo_tabla_editar==1) {
                columns.unshift({title: "Acciones", class: "acciones_t", width: "3%", "render": function ( data, type, row , meta) {
                     var dt = "<input type=\'button\' id=\'' . $this->id . 'eliminar" +meta.row +  " \' value=\'eliminar\' name=\'eliminar\' onClick=\'removeFs' . $this->id . '(this);\' class=\'button-no-style icn icn-error-sm hide-text-read\' title=\'Desasociar\'/>";
                     dt += "<input type=\'hidden\' id=\'' . $this->id . 'IdentificarInstancia" +meta.row +  "\' value=\'\' name=\'IdentificarInstancia\' class=\'button-no-style icn icn-edit-sm hide-text-read\' />";  
                return dt;
               }
                });
              }

              try {
                $("#' . $this->id . '").DataTable({                    
                     data: dataSet,
                     columns: columns,
                     responsive: false,
                     iDisplayLength: ' . $this->extra->filar_paginas . ',
                     bLengthChange : false,
                     ordering: false,
                     searching: false,
                     info: false,
                     select: false,
                     bDestroy: true,
                     language: {
                            emptyTable: "Sin datos disponibles",
                            "paginate": {
                                        "first":      "Primero",
                                        "last":       "Último",
                                        "next":       "»",
                                        "previous":   "«"
                                         },
                             "processing": "Procesando solicitud",
                             "infoEmpty": "Sin datos disponibles",
                             "zeroRecords": "Sin datos disponibles",
                            },
                     autoWidth: false,
                     bProcessing: true,
                     bServerSide: true,
                     ajax:{
                            url :"' . site_url('obn_consultas/obener_atributo_obn') . '", // json datasource
                            type: "post",  // type of method  , by default would be get
                            data: {
                                "variable_obn": "' . ($this->variable_obn != "" ? $this->variable_obn : $this->nombre) . '",
                                "etapa": "' . $etapa_id . '",
                                "atributos": "' . htmlentities(json_encode($columns)) . '"                                
                                },
                            error: function(){  // error handling code
                                $("#' . $this->id . '_processing").css("display","none");
                            }
                       },
                     "columnDefs": [{
                     defaultContent: "-",
                      targets: "_all",
                     "createdCell": function (td, cellData, rowData, row, col) {
                      if (true) {
                        var atributo=columns[col].name;
                        $(td).attr({"data-title": columns[col].name});
                        if(columns[col].name!=""){
                        $(td).html(rowData[atributo]);
                        }
                      if(!atributo){
                        $(td).find("input[name=\'IdentificarInstancia\']").val(rowData["id"]);
                        }
                    }}
                  } ]
                  });
                }
                catch(error) {
                  
                }   
              });
              
              function appendRow' . $this->id . '(id) {
                addIdObn' . $this->id . '(id);
                var id_obn= {"del":$("#' . ($this->nombre . '_iddelete_' . $this->id) . '").val(),"add":$("#' . ($this->nombre . '_idadd_' . $this->id) . '").val()};               
                var table =  $("#' . $this->id . '").DataTable();  
                table.ajax.url("' . site_url('obn_consultas/obener_atributo_obn_op') . '?id="+window.btoa(JSON.stringify(id_obn))).load();                
               }

               function removeFs' . $this->id . '(btnElement) {
                  var table =  $("#' . $this->id . '").DataTable();    
                  var rowElement = $(btnElement).closest("tr");
                  var row =  table.row(rowElement);
                  var id =  $(btnElement).parent();
                  id=$(id).find("input:hidden").val();
                  removeIdObn' . $this->id . '(id);
                  var id_obn= {"del":$("#' . ($this->nombre . '_iddelete_' . $this->id) . '").val(),"add":$("#' . ($this->nombre . '_idadd_' . $this->id) . '").val()};
                  table.ajax.url("' . site_url('obn_consultas/obener_atributo_obn_op') . '?id="+window.btoa(JSON.stringify(id_obn))).load();                  
                }
                
               function eliminarInstFs' . $this->id . '(btnElement) {
                  var table =  $("#' . $this->id . '").DataTable();    
                  var rowElement = $(btnElement).closest("tr");
                  var row =  table.row(rowElement);
                  var id =  $(btnElement).parent();
                  id=$(id).find("input:hidden").val();
                  removeIdObn' . $this->id . '(id);
                  var id_obn= {"del":$("#' . ($this->nombre . '_iddelete_' . $this->id) . '").val(),"add":$("#' . ($this->nombre . '_idadd_' . $this->id) . '").val(),"eliminar":id};
                  table.ajax.url("' . site_url('obn_consultas/obener_atributo_obn_delete') . '?id="+window.btoa(JSON.stringify(id_obn))).load();                              
                }

               ' . $agregar_fila_scirpt . '
                    
                function addIdObn' . $this->id . '(element){
                    var array = JSON.parse($("#' . ($this->nombre . '_idobn_' . $this->id) . '").val());
                    var idx = array.indexOf(""+element+"");
                    var array_add = JSON.parse($("#' . ($this->nombre . '_idadd_' . $this->id) . '").val());
                    var idx_add = array_add.indexOf(""+element+"");
                    var array_del = JSON.parse($("#' . ($this->nombre . '_iddelete_' . $this->id) . '").val());
                    var idx_del = array_del.indexOf(""+element+"");
                    ';
        if ($coleccion) {
            $display .= 'if(idx==-1){
                        array.push(""+element+"");
                        }
                        if(idx_add==-1){
                        array_add.push(""+element+"");
                        }
                        if(idx_del!=-1){
                        array_del.splice(idx_del,1);
                        }
                        ';
        } else {
            $display .= 'if(idx==-1){
                            array.splice(0, array.length);
                            array.push(""+element+"");
                         }
                        if(idx_add==-1){
                            array_add.splice(0, array.length);
                            array_add.push(""+element+"");
                        }
                        if(idx_del!=-1){
                        array_del.splice(idx_del,1);
                        }
                        ';
        }

        $display .= '
                    $("#' . ($this->nombre . '_idobn_' . $this->id) . '").val(JSON.stringify(array));  
                    $("#' . ($this->nombre . '_idadd_' . $this->id) . '").val(JSON.stringify(array_add));
                    $("#' . ($this->nombre . '_iddelete_' . $this->id) . '").val(JSON.stringify(array_del));
                }
                
                function removeIdObn' . $this->id . '(element){
                    var array = JSON.parse($("#' . ($this->nombre . '_idobn_' . $this->id) . '").val());
                    var idx = array.indexOf(""+element+"");
                    if(idx!=-1){
                    array.splice(idx, 1);
                    }
                   $("#' . ($this->nombre . '_idobn_' . $this->id) . '").val(JSON.stringify(array));
                       
                    var array_add = JSON.parse($("#' . ($this->nombre . '_idadd_' . $this->id) . '").val());
                    var idx_add = array_add.indexOf(""+element+"");
                    if(idx_add!=-1){
                    array_add.splice(idx, 1);
                    }
                   $("#' . ($this->nombre . '_idadd_' . $this->id) . '").val(JSON.stringify(array_add));
                       
                    var array_del = JSON.parse($("#' . ($this->nombre . '_iddelete_' . $this->id) . '").val());
                    var idx_del = array_del.indexOf(""+element+"");
                    if(idx_del==-1){
                    array_del.push(""+element+"");
                    }
                    $("#' . ($this->nombre . '_iddelete_' . $this->id) . '").val(JSON.stringify(array_del));
                }
                
                function confirmation' . $this->id . '(btnElement) {
                    if(confirm("Se eliminará la instancia del objeto definitivamente. ¿Realmente desea eliminar?."))
                    {
                        return eliminarInstFs' . $this->id . '(btnElement);
                    }
                    return false;
                }
                
                function darAlta' . $this->id . '() {
                salvarParcial' . $this->id . '();
                }
                
                function editarFs' . $this->id . '(btnElement) {
                  var table =  $("#' . $this->id . '").DataTable();    
                  var rowElement = $(btnElement).closest("tr");
                  var row =  table.row(rowElement);
                  var id =  $(btnElement).parent();
                  id=$(id).find("input:hidden").val();
                  
                  var inp= $("#darAlt_' . $this->id . '").find("input[name=\'id\']").val(id);
                  salvarParcial' . $this->id . '();    
                  
                }
                function salvarParcial' . $this->id . '(){
                    var form = $(".ajaxForm")[0];                    
                    $.blockUI();
                    $(\'#no_advance\').val(1);
                    if (!form.submitting) {
                        form.submitting = true;
                        $.ajax({
                            url: form.action,
                            data: $(form).serialize(),
                            type: form.method,
                            dataType: "json",
                            success: function (response) {
                                $(\'#no_advance\').val(0);
                                form.submitting = false;
                                $("#darAlt_' . $this->id . '").submit();
                                $.unblockUI();
                            },
                            error: function () {
                                $.unblockUI();
                            }
                        });                           
                    }
                }
                
                
                function seleccionarAccion' . $this->id . '(){
                    $.blockUI();
                    $("#modal_tabla_obn").load(site_url+"obn_consultas/tabla_obn?obn="+window.btoa("' . ($this->variable_obn != "" ? $this->variable_obn : $this->nombre) . '")+"&&etapa=' . $etapa_id . '"+"&&campo=' . $this->id . '");
                    $("#modal_tabla_obn").modal({backdrop: "static", keyboard: false});
                    $.unblockUI();
                    return false;
                }
            </script>';

        $display.='<div class="modal hide fade" id="modal_tabla_obn">
           </div>
           ';

        return $display;
    }

    public function backendExtraFields() {
        $output = '';
        $output = '<div class="control-group"><div>';
        $output.='<label for="filar_paginas">Filas por páginas</label>';
        $filas_paginas = isset($this->extra->filar_paginas) ? $this->extra->filar_paginas : "10";
        $output.='<input type="text" id="filar_paginas" name="extra[filar_paginas]" placeholder="Filas por páginas" value="' . $filas_paginas . '" /></div></div>';

        $columns = array();
        if (isset($this->extra->columns))
            $columns = $this->extra->columns;
        $output .= '<div class="control-group">
            <div class="columnas">
                <script type="text/javascript">
                    $(document).ready(function() {
                      $("#tipo_tabla_datos").on("change", function() {
                        switch($(this).val()) {
                          case "abm":
                            $("#div_formulario_tabla_datos_asociar").removeClass("hidden");
                            break;
                          default:
                            $("#div_formulario_tabla_datos_asociar").addClass("hidden");
                            break;
                        }
                      });
                      
                        if($("#tipo_tabla_datos").val()=="abm"){
                            $("#div_formulario_tabla_datos_asociar").removeClass("hidden");
                        }else{
                            $("#div_formulario_tabla_datos_asociar").addClass("hidden");
                        }
                        $("#formEditarCampo .columnas .nuevo").click(function(){
                            var pos=$("#formEditarCampo .columnas table tbody tr").size();
                            var html="<tr>";
                            html+="<td><label class=\'hidden-accessible\' for=\'etiqueta"+pos+"\'>etiqueta"+pos+"</label><input class=\'input\' id=\'etiqueta"+pos+"\' type=\'text\' name=\'extra[columns]["+pos+"][header]\' /></td>";
                            html+="<td><label class=\'hidden-accessible\' for=\'atributo"+pos+"\'>atributo"+pos+"</label><input class=\'input\' id=\'atributo"+pos+"\' type=\'text\' name=\'extra[columns]["+pos+"][atributo]\' /></td>";
                            html+="<td class=\'actions\'><button type=\'button\' class=\'btn btn-danger eliminar\'><span class=\'icon-trash icon-white\'></span></button></td>";
                            html+="</tr>";

                            $("#formEditarCampo .columnas table tbody").append(html);
                            
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
                            <th>Atributo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    ';

        if ($columns) {
            $i = 0;
            foreach ($columns as $key => $c) {
                $output.='
                <tr>
                    <td><label class="hidden-accessible" for="etiqueta' . $i . '">etiqueta' . $i . '</label><input class="input" id="etiqueta' . $i . '" type="text" name="extra[columns][' . $i . '][header]" value="' . $c->header . '" /></td>
                    <td><label class="hidden-accessible" for="atributo' . $i . '">atributo' . $i . '</label><input class="input" id="atributo' . $i . '" type="text" name="extra[columns][' . $i . '][atributo]" value="' . $c->atributo . '" /></td>
                   <td class="actions"><button type="button" class="btn btn-danger eliminar"><span class="icon-trash icon-white"></span></button></td>
                </tr>
                ';
                $i++;
            }
        }

        $output.='
        </tbody>
        </table></div></div>
        ';

        return $output;
    }

    public function backendExtraValidate() {
        $CI = &get_instance();
        $CI->form_validation->set_rules('extra[columns]', 'Columnas', 'required');
        $CI->form_validation->set_rules('extra[tipo_tabla_datos]', 'Tipo de Tabla', 'required');
        $CI->form_validation->set_rules('extra[filar_paginas]', 'Filas por Páginas', 'required|numeric');
    }

}

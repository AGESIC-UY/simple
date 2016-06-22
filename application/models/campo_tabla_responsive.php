<?php
require_once('campo.php');
class CampoTablaResponsive extends Campo{

    public $requiere_datos=false;
    public $requiere_validacion=false;

    protected function display($modo, $dato,$etapa_id) {
        if($etapa_id){
            $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
            $regla=new Regla($this->valor_default);
            $valor_default=$regla->getExpresionParaOutput($etapa->id);
        }
        else {
            $valor_default=$this->valor_default;
        }

        if($modo == 'visualizacion') {
          $this->readonly = 1;
        }

        $columns = $this->extra->columns;

        $display  = '<div class="control-group">';
        $display.='<span class="h4" data-fieldset="'.$this->fieldset.'">' . $this->etiqueta . (!in_array('required', $this->validacion) ? ':' : '*:') . '</span>';
        $display.='<div class="" data-fieldset="'.$this->fieldset.'">';
        if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';
        $display.='<table id="'.$this->id.'" class="display dataTable" role="grid"><caption class="hide-read">'.$this->etiqueta.'</caption><thead></thead><tbody></tbody></table>';
        if(!$this->readonly) {
          $display .= '<span class="link-agregar"><input type="button" id="addR-'.$this->id.'" value="+ Agregar fila" class="btn-link"/></span>';
        }
        $display.='<input class="input-xxlarge" type="hidden" autocomplete="off" name="' . $this->nombre . '" value=\'' . ($dato?json_encode($dato->valor):$valor_default) . '\' />';
        $display.='</div>';
        $display.='</div>';

        $display .= '<script type="text/javascript">
            function updateTableData'.$this->id.'() {
              $(".action-buttons-primary .btn-primary, #save_step").click(function() {
                var data_array = [];
                var table = $("#'.$this->id.'").DataTable();
                var table_data = table.$("input, select");

                $("#'.$this->id.' tr").each(function() {
                  var nueva_fila = [];
                  $(this).find("input, select").each(function() {
                    if($(this).attr("name") != "eliminar") {
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
                $("input[name=\"'. $this->nombre .'\"]").val(new_val);
              });
            }

            document.combos'.$this->id.' = [];

            function comboRendererResponsive'.$this->id.'(col) {
              var api_url = col.data.toString(); // Handsontable.helper.stringify(col.data);
              var dt = "";
              if((api_url != "") && (api_url.startsWith("http"))) {
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
                        var id = "combo-dinamico-'. $this->id .'-" + col.id;
                        dt =  "<select title=\"" + col.header + "\" class=\""+ id +"\" name=\"grid_combo\">"+ options +"</select>";
                        document.combos'.$this->id.'[col.id] = dt;
                        $(".combo-dinamico-'. $this->id .'-" + col.id).html(dt);

                        //actualiza los valores del select en la tabla luego de cargado los options del servicio rest
                        if($("input[name=\"'.$this->nombre.'\"]").val() == "") {
                          var valores = [];
                        }
                        else {
                          var valores = JSON.parse($("input[name=\"'.$this->nombre.'\"]").val());
                        }

                        if(valores != "") {
                          var i = 0;
                          $("#'.$this->id.' tbody tr").each(function() {
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

                        updateTableData'.$this->id.'();
                      }
                      catch(error) {
                        console.log(error);
                      }
                    },
                    error: function() {
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
                var id = "combo-dinamico-'. $this->id .'-" + col.id;

                dt =  "<select class=\""+ id +"\" name=\"grid_combo\">"+ options +"</select>";
                document.combos'.$this->id.'[col.id] = dt;
                $(".combo-dinamico-'. $this->id .'-" + col.id).html(dt);
              }
              else {
                dt = "";
                return dt;
              }
            }

            $(document).ready(function() {
              document.combos_cargados = [];
              document.columns = [];
              var mode = "'.$modo.'";
              var columns = '.json_encode($columns).';

              columns = columns.map(function(c) {
                if('.$this->readonly.') {
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
                           updateTableData'.$this->id.'();
                           return dt;
                        }
                      }
                      break;
                    case "combo":
                        // Obtiene los datos del servicio  para la columna
                        comboRendererResponsive'.$this->id.'(c);
                        return {title: c.header, "render":  function (data, type, row , meta) {
                          if(document.combos'.$this->id.'[c.id]) {
                            var dt = document.combos'.$this->id.'[c.id];
                            var row_value = (row[meta.col] == "undefined" ? "" : row[meta.col])
                            dt = dt.replace("value=\""+ row_value +"\"", "value=\""+ row_value +"\" selected");
                          }
                          else {
                            // todavia no termina de cargar los valores del servicio ajax
                            // se retorna con el texto Cargando....
                            var id = "combo-dinamico-'. $this->id .'-" + c.id;
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
                         updateTableData'.$this->id.'();
                         return dt;
                      }
                    }
                  }
                }
              });

              var headers = columns.map(function(c){
                return c.header;
              });

              var data;

              try {
                data = JSON.parse($("[name=\"'.$this->nombre.'\"]").val());
                document.dd = data;
                // data = data.slice(1);
              }
              catch(err) {
                data = [
                  new Array(headers.length)
                ];
              }

              var table_readonly = "'.($this->readonly ? 1 : 0).'";
              dataSet = [];

              if(table_readonly == 0) {
                $(data).each(function() {
                  this.unshift("");
                });
                var arr = Object.keys(data).map(function(k) {
                  return data[k];
                });
              }
              else {
                var arr = Object.keys(data).map(function(k) {
                  return data[k];
                });
              }


              try{
              if (typeof arr[0][1] != "undefined"){
                dataSet = arr;
              }
            }
            catch(e) {
            }
              if(table_readonly == 0) {
                columns.unshift({title: "Acciones", class: "acciones_t", width: "3%", "render": function ( data, type, row , meta) {
                     var dt = "<input type=\'button\' id=\'eliminar" +meta.row +  "\' value=\'eliminar\' name=\'eliminar\' onClick=\'removeFs'.$this->id.'(this);\' class=\'button-no-style icn icn-error-sm hide-text-read\' />";
                     return dt;
                  }
                });
              }

              try {
                $("#'.$this->id.'").DataTable({
                     data: dataSet,
                     columns: columns,
                     responsive: false,
                     paging: false,
                     ordering: false,
                     searching: false,
                     info: false,
                     select: false,
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

              $("#'.$this->id.'").on("keyup change", ".child input, .child select, .child textarea", function(e) {
                var table = $("#'.$this->id.'").DataTable();
                var $el = $(this);
                var rowIdx = $el.closest("ul").data("dtr-index");
                var colIdx = $el.closest("li").data("dtr-index");
                var cell = table.cell({row: rowIdx, column: colIdx}).node();
                $("input, select, textarea", cell).val($el.val());
                if($el.is(":checked")) {
                  $("input", cell).prop("checked", true);
                }
              });

              function appendRow'.$this->id.'() {
                 var t = $("#'.$this->id.'").DataTable();
                 var columnas = [];
                 var columns = '.json_encode($columns).';
                 $(columns).each(function() {
                   columnas.push("");
                 });
                 var table_readonly = "'.($this->readonly ? 1 : 0).'";
                 if(table_readonly == 0) {
                   columnas.unshift({ title: "Acciones" ,"render": function ( data, type, row , meta) {
                        var dt = "<input type=\'button\' id=\'eliminar" +meta.row +  "\' value=\'eliminar\' name=\'eliminar\' onClick=\'removeFs'.$this->id.'(this);\' class=\'button-no-style icn icn-error-sm hide-text-read\' />";
                        return dt;
                     }
                   });
                 }
                 var node = t.row.add(columnas).draw().node();
                 var detail_row = "";
                 $(node).addClass("result-row");
                 node = node.outerHTML;
                 $(node).hide().fadeIn("normal");
                 updateTableData'.$this->id.'();
               }

               function removeFs'.$this->id.'(btnElement) {
                  var table =  $("#'.$this->id.'").DataTable();
                  var rowElement = $(btnElement).closest("tr");
                  var row =  table.row(rowElement);
                  row.remove();
                  table.draw(false);
                  updateTableData'.$this->id.'();
                }

                $("#addR-'.$this->id.'").off("click");
                $("#addR-'.$this->id.'").click(function(event) {
                  appendRow'.$this->id.'();
                });
            </script>';

        return $display;
    }

    public function backendExtraFields() {
        $columns=array();
        if(isset($this->extra->columns))
            $columns=$this->extra->columns;

        $output = '
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
                            html+="<td><label class=\'hidden-accessible\' for=\'etiqueta"+pos+"\'>etiqueta"+pos+"</label><input class=\'input-medium\' id=\'etiqueta"+pos+"\' type=\'text\' name=\'extra[columns]["+pos+"][header]\' /></td>";
                            html+="<td><label class=\'hidden-accessible\' for=\'tipo"+pos+"\'>tipo"+pos+"</label><select class=\'input-medium tipo\' id=\'tipo"+pos+"\' name=\'extra[columns]["+pos+"][type]\' ><option>text</option><option>numeric</option><option>link</option><option>combo</option></select><input type=\'text\' class=\'input-medium url hidden\' name=\'extra[columns]["+pos+"][data]\' /><input type=\"hidden\" value=\""+pos+"\" name=\"extra[columns]["+pos+"][id]\" /></td>";
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
                <table class="table">
                  <caption class="hidden-accessible">Columnas</caption>
                    <thead>
                        <tr>
                            <th>Etiqueta</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    ';

        if($columns){
            $i=0;
            foreach($columns as $key => $c){
                $output.='
                <tr>
                    <td><label class="hidden-accessible" for="etiqueta'.$i.'">etiqueta'.$i.'</label><input class="input-large" id="etiqueta'.$i.'" type="text" name="extra[columns]['.$i.'][header]" value="'.$c->header.'" /></td>
                    <td><label class="hidden-accessible" for="tipo'.$i.'">tipo'.$i.'</label><select class="input-small tipo" id="tipo'.$i.'" name="extra[columns]['.$i.'][type]"><option '.($c->type=='text'?'selected':'').'>text</option><option '.($c->type=='numeric'?'selected':'').'>numeric</option><option '.($c->type=='link'?'selected':'').'>link</option><option '.($c->type=='combo'?'selected':'').'>combo</option></select><input type="text" class="input-medium url '.($c->type=='combo'?'':'hidden').'" name="extra[columns]['.$i.'][data]" value="'. ($c->type=='combo'?$c->data:'') .'" /><input type="hidden" value="'. $i .'" name="extra[columns]['.$i.'][id]" /></td>
                    <td class="actions"><button type="button" class="btn btn-danger eliminar"><span class="icon-trash icon-white"></span></button></td>
                </tr>
                ';
                $i++;
            }
        }

        $output.='
        </tbody>
        </table>
        </div>
        ';

        return $output;
    }

    public function backendExtraValidate(){
        $CI=&get_instance();
        $CI->form_validation->set_rules('extra[columns]','Columnas','required');
    }
}

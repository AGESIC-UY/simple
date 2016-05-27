<?php
require_once('campo.php');
class CampoGrid extends Campo{

    public $requiere_datos=false;

    protected function display($modo, $dato,$etapa_id) {
        if($etapa_id){
            $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
            $regla=new Regla($this->valor_default);
            $valor_default=$regla->getExpresionParaOutput($etapa->id);
        }else{
            $valor_default=$this->valor_default;
        }

        $columns = $this->extra->columns;

        $display  = '<div class="control-group">';
        $display.='<span class="control-label" data-fieldset="'.$this->fieldset.'">' . $this->etiqueta . (!in_array('required', $this->validacion) ? ' (Opcional):' : '*:') . '</span>';
        $display.='<div class="controls" data-fieldset="'.$this->fieldset.'">';
        $display.='<div class="grid" data-id="'.$this->id.'" style="width: 100%;"></div>';
        $display.='<input class="input-xxlarge" type="hidden" name="' . $this->nombre . '" value=\'' . ($dato?json_encode($dato->valor):$valor_default) . '\' />';
        if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';
        $display.='</div>';
        $display.='</div>';

        $display .= '
            <script>
            function linkRenderer(instance, td, row, col, prop, value, cellProperties) {
              var url = Handsontable.helper.stringify(value);
              td.innerHTML = "<a href=\""+ url +"\" target=\"_blank\">" + url + "</a>";
              return td;
            }

            function comboRenderer(instance, td, row, col, prop, value, cellProperties) {
              var api_url = Handsontable.helper.stringify(value);

              if(api_url != "") {
                try {
                  $.ajax({
                    dataType: "json",
                    url: api_url, // for test: https://restcountries.eu/rest/v1/all
                    success: function(json) {
                      var options = "";
                      $(json).each(function(k, v) {
                        options += "<option>"+ this[Object.keys(this)[0]] +"</option>";
                      });

                      td.innerHTML = "<select name=\"grid_combo\">"+ options +"</select>";
                      return td;
                    },
                    error: function() {
                      td.innerHTML = "Error al generar combo.";
                      return td;
                    }
                  });
                } catch(error) {
                  td.innerHTML = "Error al generar combo.";
                  return td;
                }
              }
              else {
                td.innerHTML = "";
                return td;
              }
            }

            $(document).ready(function(){
                var mode = "'.$modo.'";
                var columns = '.json_encode($columns).';
                columns = columns.map(function(c) {
                  switch(c.type) {
                    case "link":
                      return {header: c.header, renderer: linkRenderer}
                      break;
                    case "combo":
                      return {header: c.header, renderer: comboRenderer}
                      break;
                    default:
                      return {header: c.header, type: c.type}
                  }
                });
                document.col = columns;
                var headers = columns.map(function(c){return c.header;});
                var data;
                try {
                    data = JSON.parse($("[name=\''.$this->nombre.'\']").val());
                    data = data.slice(1);
                }
                catch(err) {
                    data = [
                      new Array(headers.length)
                    ];
                }

                $(".grid[data-id='.$this->id.']").handsontable({
                  data: data,
                  readOnly: mode=="visualizacion",
                  minSpareRows: 0,
                  rowHeaders: false,
                  colHeaders: headers,
                  columns: columns,
                  contextMenu: true,
                  stretchH: false,
                  autoWrapRow: true,
                  afterChange: function (change, source) {
                    var rows = this.getData().slice();
                    rows.unshift(headers);
                    var json = JSON.stringify(rows);
                    $("[name=\''.$this->nombre.'\']").val(json);
                  }
                });
              });
            </script>
        ';

        return $display;
    }

    public function backendExtraFields() {

        $columns=array();
        if(isset($this->extra->columns))
            $columns=$this->extra->columns;

        $output = '
            <div class="columnas">
                <script type="text/javascript">
                    $(document).ready(function(){
                        $("#formEditarCampo .columnas .nuevo").click(function(){
                            var pos=$("#formEditarCampo .columnas table tbody tr").size();
                            var html="<tr>";
                            html+="<td><label class=\'hidden-accessible\' for=\'etiqueta"+pos+"\'>etiqueta"+pos+"</label><input class=\'input-large\' id=\'etiqueta"+pos+"\' type=\'text\' name=\'extra[columns]["+pos+"][header]\' /></td>";
                            html+="<td><label class=\'hidden-accessible\' for=\'tipo"+pos+"\'>tipo"+pos+"</label><select class=\'input-medium tipo\' id=\'tipo"+pos+"\' name=\'extra[columns]["+pos+"][type]\' ><option>text</option><option>numeric</option><option>link</option><option>combo</option></select></td>";
                            html+="<td class=\'actions\'><button type=\'button\' class=\'btn btn-danger eliminar\'><span class=\'icon-trash icon-white\'></span></button></td>";
                            html+="</tr>";

                            $("#formEditarCampo .columnas table tbody").append(html);

                            /*
                            $(".tipo").on("change", function() {
                              switch($(this).val()) {
                                case "link":
                                  $(this).parent().parent().find(".url").first().removeAttr("readonly");
                                  $(this).parent().parent().find(".url").first().attr({"placeholder": "URL del enlace"});
                                  break;
                                case "combo":
                                  $(this).parent().parent().find(".url").first().removeAttr("readonly");
                                  $(this).parent().parent().find(".url").first().attr({"placeholder": "URL del servicio"});
                                  break;
                                default:
                                  $(this).parent().parent().find(".url").first().attr({"readonly": ""});
                                  $(this).parent().parent().find(".url").first().attr({"placeholder": ""});
                              }
                            });
                            */
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
                    <td><label class="hidden-accessible" for="tipo'.$i.'">tipo'.$i.'</label><select class="input-small tipo" id="tipo'.$i.'" name="extra[columns]['.$i.'][type]"><option '.($c->type=='text'?'selected':'').'>text</option><option '.($c->type=='numeric'?'selected':'').'>numeric</option><option '.($c->type=='link'?'selected':'').'>link</option><option '.($c->type=='combo'?'selected':'').'>combo</option></select></td>
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

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

        $display.='
            <script>
                $(document).ready(function(){
                    var mode = "'.$modo.'";
                    var columns = '.json_encode($columns).';
                    document.col = columns;
                    var headers = columns.map(function(c){return c.header;});
                    var data;
                    try{
                        data = JSON.parse($("[name=\''.$this->nombre.'\']").val());
                        data = data.slice(1);
                    }catch(err){
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
                      contextMenu: false,
                      stretchH: "all",
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
                            html+="<td><label class=\'hidden-accessible\' for=\'etiqueta"+pos+"\'>etiqueta"+pos+"</label><input id=\'etiqueta"+pos+"\' type=\'text\' name=\'extra[columns]["+pos+"][header]\' /></td>";
                            html+="<td><label class=\'hidden-accessible\' for=\'tipo"+pos+"\'>tipo"+pos+"</label><select id=\'tipo"+pos+"\' name=\'extra[columns]["+pos+"][type]\' ><option>text</option><option>numeric</option></select></td>";
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
                    <td><label class="hidden-accessible" for="etiqueta'.$i.'">etiqueta'.$i.'</label><input id="etiqueta'.$i.'" type="text" name="extra[columns]['.$i.'][header]" value="'.$c->header.'" /></td>
                    <td><label class="hidden-accessible" for="tipo'.$i.'">tipo'.$i.'</label><select id="tipo'.$i.'" name="extra[columns]['.$i.'][type]"><option '.($c->type=='text'?'selected':'').'>text</option><option '.($c->type=='numeric'?'selected':'').'>numeric</option></select></td>
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

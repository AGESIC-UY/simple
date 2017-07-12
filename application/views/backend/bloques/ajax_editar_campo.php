<script type="text/javascript">
    $(document).ready(function(){
        $('.validacion').typeahead({
            mode: "multiple",
            delimiter: "|",
            source: ["required","rut","ci","alpha_numeric_ext","not_empty_table","consulta_pago_completo_generico","min_length_table[num]","max_length_table[num]","min_length[num]","max_length[num]","exact_length[num]","greater_than[num]","less_than[num]","alpha","alpha_numeric","alpha_dash","alpha_space","numeric","integer","decimal","is_natural","is_natural_no_zero","valid_email","valid_emails","valid_ip","valid_base64","trim","is_unique[exp]"]
        });

        //Funcionalidad del llenado de nombre usando el boton de asistencia
        $("#formEditarCampo .asistencia .dropdown-menu a").click(function(){
            var nombre=$(this).text();
            $("#formEditarCampo input[name=nombre]").val(nombre);
        });

        //Llenamos el select box de dependientes
        var selected=$("#formEditarCampo select[name=dependiente_campo]").val();
        var html='<option value="">Seleccionar</option>';
        var names=new Array();
        $("#formEditarFormulario :input[name]").each(function(i,el){
            var name=$(el).attr("name");
            if($.inArray(name, names)==-1){
                names.push(name);
                html+='<option>'+name+'</option>';
            }
        });
        $("#formEditarCampo select[name=dependiente_campo]").html(html);
        $("#formEditarCampo select[name=dependiente_campo]").val(selected);

        //Funcionalidad en campo dependientes para seleccionar entre tipo regex y string
        $buttonRegex=$("#formEditarCampo .campoDependientes .buttonRegex");
        $buttonString=$("#formEditarCampo .campoDependientes .buttonString");
        $inputDependienteTipo=$("#formEditarCampo input[name=dependiente_tipo]");
        $buttonString.attr("disabled",$inputDependienteTipo.val()=="string");
        $buttonRegex.attr("disabled",$inputDependienteTipo.val()=="regex");
        $buttonRegex.click(function(){
            $buttonString.prop("disabled",false);
            $buttonRegex.prop("disabled",true);
            $inputDependienteTipo.val("regex");
        });
        $buttonString.click(function(){
            $buttonString.prop("disabled",true);
            $buttonRegex.prop("disabled",false);
            $inputDependienteTipo.val("string");
        });

        //Funcionalidad en campo dependientes para seleccionar entre tipo igualdad y desigualdad
        $buttonDesigualdad=$("#formEditarCampo .campoDependientes .buttonDesigualdad");
        $buttonIgualdad=$("#formEditarCampo .campoDependientes .buttonIgualdad");
        $inputDependienteRelacion=$("#formEditarCampo input[name=dependiente_relacion]");
        $buttonIgualdad.attr("disabled",$inputDependienteRelacion.val()=="==");
        $buttonDesigualdad.attr("disabled",$inputDependienteRelacion.val()=="!=");
        $buttonDesigualdad.click(function(){
            $buttonIgualdad.prop("disabled",false);
            $buttonDesigualdad.prop("disabled",true);
            $inputDependienteRelacion.val("!=");
        });
        $buttonIgualdad.click(function(){
            $buttonIgualdad.prop("disabled",true);
            $buttonDesigualdad.prop("disabled",false);
            $inputDependienteRelacion.val("==");
        });

        //Llenado automatico del campo nombre
        $("#formEditarCampo input[name=etiqueta]").blur(function(){
            ellipsize($("#formEditarCampo input[name=etiqueta]"),$("#formEditarCampo input[name=nombre]"));
        });
        //Llenado automatico del campo valor
        $("#formEditarCampo").on("blur","input[name$='[etiqueta]']",function(){
            var campoOrigen=$(this);
            var campoDestino=$(this).closest("tr").find("input[name$='[valor]']")
            ellipsize(campoOrigen,campoDestino);
        });

        function ellipsize(campoOrigen,campoDestino){
            if($(campoDestino).val()==""){
                var string=$(campoOrigen).val().trim();
                string=string.toLowerCase();
                string=string.replace(/\s/g,"_");
                string=string.replace(/á/g,"a");
                string=string.replace(/é/g,"e");
                string=string.replace(/í/g,"i");
                string=string.replace(/ó/g,"o");
                string=string.replace(/ú/g,"u");
                string=string.replace(/\W/g,"");
                $(campoDestino).val(string);
            }
        }

    });

</script>

<div class="modal-header">
    <button class="close" data-dismiss="modal">×</button>
    <h3>Edición de Campo</h3>
</div>
<form id="formEditarCampo" class="ajaxForm" method="POST" action="<?= site_url('backend/bloques/editar_campo_form/' . ($edit ? $campo->id : '')) ?>">
  <div class="modal-body">
        <input type="hidden" name="bloque_id" value="<?= $bloque->id ?>" />
        <div class="validacion validacion-error"></div>
        <?php if (!$edit): ?>
            <input type="hidden" name="formulario_id" value="<?= $formulario->id ?>" />
            <input type="hidden" name="tipo" value="<?= $campo->tipo ?>" />
        <?php endif; ?>
        <label for="etiqueta">Etiqueta</label>
        <?php if($campo->etiqueta_tamano=='xxlarge'):?>
          <?php if($campo->tipo == 'javascript'):?>
            <textarea id="etiqueta" class="input-xxlarge campo_javascript_codigo" rows="15" name="etiqueta"><?= htmlspecialchars($campo->etiqueta) ?></textarea>
          <?php else: ?>
            <textarea id="etiqueta" class="input-xxlarge" rows="5" name="etiqueta"><?= htmlspecialchars($campo->etiqueta) ?></textarea>
          <?php endif; ?>
        <?php else: ?>
        <input id="etiqueta" type="text" name="etiqueta" value="<?= htmlspecialchars($campo->etiqueta) ?>" />
        <?php endif ?>
        <?php if($campo->requiere_nombre):?>
        <label for="nombre_e">Nombre</label>
        <input id="nombre_e" type="text" name="nombre" value="<?= $campo->nombre ?>" />
        <?php $campos_asistencia=$formulario->Proceso->getNombresDeCampos($campo->tipo,false) ?>
        <?php if(count($campos_asistencia)):?>
        <div class="btn-group asistencia" style="display: inline-block; vertical-align: top;">
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="icon-th-list"></span><span class="caret"></span></a>
            <ul class="dropdown-menu">
                <?php foreach ($campos_asistencia as $c): ?>
                    <li><a href="#"><?= $c ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif ?>
        <?php else: ?>
        <input type="hidden" name="nombre" value="<?=$campo->nombre?$campo->nombre:uniqid();?>" />
        <?php endif; ?>

        <?php if(!$campo->estatico):?>
        <label for="ayuda">Ayuda contextual (Opcional)</label>
        <input id="ayuda" type="text" class="input-xxlarge" name="ayuda" value="<?=$campo->ayuda?>" />
        <?php endif ?>

        <?php if (!$campo->estatico): ?>
            <label class="checkbox" for="soloLectura"><input id="soloLectura" type="checkbox" name="readonly" value="1" <?=$campo->readonly?'checked':''?> /> Solo lectura</label>
        <?php endif; ?>
        <?php if (!$campo->estatico): ?>
            <label for="validacion">Reglas de validación</label>
            <input id="validacion" class='validacion' type="text" name="validacion" value="<?= $edit ? implode('|', $campo->validacion) : 'required' ?>"/>
               <?php endif; ?>
            <?php if(!$campo->estatico):?>
            <label for="valor_default">Valor por defecto</label>
            <input id="valor_default" type="text" name="valor_default" value="<?=htmlspecialchars($campo->valor_default)?>" />
            <?php endif ?>
            <?php if(($campo->tipo != 'fieldset') && ($campo->tipo != 'bloque')):?>
            <label for="lista_de_fieldsets">Fieldset al que pertenece</label>
            <input id="lista_de_fieldsets" type="text" name="fieldset" value="<?=htmlspecialchars($campo->fieldset)?>" />
            <?php endif ?>
            <?php if($campo->tipo == 'bloque'):?>
            <label for="selector_bloques">Tipo de bloque</label>
            <select name="valor_default" id="selector_bloques">
                <option value="" <?= $campo->valor_default == '' ? 'selected' : '' ?>>-- Seleccionar tipo de bloque --</option>
                <option value="datos_personales" <?= $campo->valor_default == 'datos_personales' ? 'selected' : '' ?>>Datos personales</option>
                <option value="encuesta_satisfaccion" <?= $campo->valor_default == 'encuesta_satisfaccion' ? 'selected' : '' ?>>Encuesta de satisfacción</option>
            </select>
            <?php endif ?>
            <div class="campoDependientes">
                <label for="dependiente_campo">Visible solo si</label>
                <select id="dependiente_campo" class="input-medium" name="dependiente_campo">
                    <option value="<?=$campo->dependiente_campo?>"><?=$campo->dependiente_campo?></option>
                </select>
                <div class="btn-group" style="margin-bottom: 9px;">
                    <button type="button" class="buttonIgualdad btn">=</button><button type="button" class="buttonDesigualdad btn">!=</button>
                </div>
                <input type="hidden" name="dependiente_relacion" value="<?=isset($campo) && $campo->dependiente_relacion? $campo->dependiente_relacion:'==' ?>" />
                <span class="input-append">
                  <label class="hidden-accessible" for="dependiente_valor">Valor</label>
                    <input id="dependiente_valor" type="text" name="dependiente_valor" value="<?= isset($campo) ? $campo->dependiente_valor : '' ?>" /><button type="button" class="buttonString btn">String</button><button type="button" class="buttonRegex btn">Regex</button>
                </span>
                <input type="hidden" name="dependiente_tipo" value="<?=isset($campo) && $campo->dependiente_tipo? $campo->dependiente_tipo:'string' ?>" />
            </div>

            <?=$campo->extraForm()?$campo->extraForm():''?>

        <?php if ($campo->requiere_datos): ?>
            <div class="datos">
                <script type="text/javascript">
                    $(document).ready(function(){
                        $('#formEditarCampo .datos .nuevo').click(function(){
                            var pos=$('#formEditarCampo .datos table tbody tr').size();
                            var html='<tr>';
                            html+='<td><label class="hidden-accessible" for="etiqueta'+pos+'">etiqueta'+pos+'</label><input id="etiqueta'+pos+'" type="text" name="datos['+pos+'][etiqueta]" /></td>';
                            html+='<td><label class="hidden-accessible" for="valor'+pos+'">valor'+pos+'</label><input id="valor'+pos+'" type="text" name="datos['+pos+'][valor]" /></td>';
                            html+='<td class="actions"><button type="button" class="btn btn-danger"><span class="icon-trash icon-white"></span></button></td>';
                            html+='</tr>';

                            $('#formEditarCampo .datos table tbody').append(html);
                        });
                        $('#formEditarCampo .datos').on('click','.eliminar',function(){
                            $(this).closest('tr').remove();
                        });
                    });
                </script>
                <h4>Datos</h4>
                <button class="btn nuevo" type="button"><span class="icon-plus"></span> Nuevo</button>
                <table class="table">
                  <caption class="hidden-accessible">Opciones</caption>
                  <thead>
                      <tr>
                          <th>Etiqueta</th>
                          <th>Valor</th>
                          <th>Acciones</th>
                      </tr>
                  </thead>
                  <tbody>
                    <?php if($campo->datos):?>
                      <?php $i=0 ?>
                      <?php foreach ($campo->datos as $key => $d): ?>
                        <tr>
                          <td><label class="hidden-accessible" for="etiqueta<?= $i ?>">etiqueta<?= $i ?></label><input id="etiqueta<?= $i ?>" type="text" name="datos[<?= $i ?>][etiqueta]" value="<?= $d->etiqueta ?>" /></td>
                          <td><label class="hidden-accessible" for="valor<?= $i ?>">valor<?= $i ?></label><input id="valor<?= $i ?>" type="text" name="datos[<?= $i ?>][valor]" value="<?= $d->valor ?>" /></td>
                          <td class="actions"><button type="button" class="btn btn-danger"><span class="icon-trash icon-trash"></span></button></td>
                        </tr>
                        <?php $i++ ?>
                      <?php endforeach; ?>
                    <?php endif ?>
                  </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?=$campo->backendExtraFields()?>


</div>
<div class="modal-footer">
  <button type="submit" class="btn btn-primary">Guardar</button>
    <!-- a href="#" onclick="javascript:$('#formEditarCampo').submit();return false;" class="btn btn-primary">Guardar</a -->
    <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
</div>
</form>

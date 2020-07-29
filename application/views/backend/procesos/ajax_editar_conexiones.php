<script type="text/javascript">
    $(document).ready(function(){

        <?php for($i = 0; $i <= count($conexiones); $i++):?>

          $('select[name="conexiones[<?php echo $i;?>][tarea_id_destino]"]').change(function() {
            if($(this).val() == ''){
                $('select[name="conexiones[<?php echo $i;?>][estado_fin_trazabilidad]"]').show();
                $('#no_traza_'+<?= $i ?>).hide();
            }
            else {
              $('select[name="conexiones[<?php echo $i;?>][estado_fin_trazabilidad]"]').hide();
              $('#no_traza_'+<?= $i ?>).show();
            }

          });
        <?php endfor;?>

        $("[title]").tooltip();

        //Funcionalidad del llenado de nombre usando el boton de asistencia
        $("#formEditarConexion").on("click",".asistencia .dropdown-menu a",function(){
            var nombre=$(this).text();
            $(this).closest("td").find(":input").val(nombre);
        });


        $("#formEditarConexion .botonNuevaConexion").click(function(){
            var html=$("#formEditarConexion table tbody tr:last").clone();
            $("#formEditarConexion table tbody").append(html);
            $("#formEditarConexion table tbody tr").each(function(i,row){
                $(row).find("[name]").each(function(j,el){
                    el.name=el.name.replace(/\[\w+\]/,"["+i+"]");
                });

            });

            var nueva_pos = ($("#formEditarConexion table tr").length-2);

            $("#formEditarConexion table tbody tr:last").find('p').attr('id','no_traza_'+nueva_pos);

            $('select[name="conexiones['+nueva_pos+'][tarea_id_destino]"]').change(function() {
              if($(this).val() == ''){
                  $('select[name="conexiones['+nueva_pos+'][estado_fin_trazabilidad]"]').show();
                  $('#no_traza_'+nueva_pos).hide();
              }
              else {
                $('select[name="conexiones['+nueva_pos+'][estado_fin_trazabilidad]"]').hide();
                $('#no_traza_'+nueva_pos).show();
              }

            });

            $("#formEditarConexion .botonEliminarConexion").click(function(){
                $(this).closest("tr").remove();
            });
        });

        $("#formEditarConexion .botonEliminarConexion").click(function(){
            $(this).closest("tr").remove();
        });
    });
</script>

<div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3>Editar Conexiones</h3>
</div>
<form id="formEditarConexion" class="ajaxForm" method="POST" action="<?= site_url('backend/procesos/editar_conexiones_form/' . $conexiones[0]->TareaOrigen->id) ?>">
<div class="modal-body">
        <div class="validacion validacion-error"></div>

        <label>Tipo</label>
        <input type="text" value="<?= $conexiones[0]->tipo ?>" disabled />

        <br /><br />

        <?php if($conexiones[0]->tipo!='secuencial'):?>
        <div><button class="btn botonNuevaConexion" type="button"><span class="icon-plus"></span> Nueva</button></div>
        <?php endif ?>

        <table class="table">
            <thead>
                <tr>
                    <th>Origen</th>
                    <th>Destino</th>
                    <?php if ($conexiones[0]->tipo == 'evaluacion'|| $conexiones[0]->tipo == 'paralelo_evaluacion'): ?>
                    <th style="min-width: 100px;">Estado Traza</th>
                    <?php endif; ?>
                    <th>Regla</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($conexiones as $key=>$conexion):?>
                <tr>
                    <td><?= $conexion->TareaOrigen->nombre ?></td>
                    <td>
                        <select name="conexiones[<?=$key?>][tarea_id_destino]">
                            <option value="">Fin del proceso</option>
                            <?php foreach($conexion->TareaOrigen->Proceso->Tareas as $t):?>
                            <option value="<?=$t->id?>" <?=$t->id==$conexion->tarea_id_destino?'selected':''?>><?=$t->nombre?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>

                    <?php if ($conexion->tipo == 'evaluacion' || $conexion->tipo == 'paralelo_evaluacion'): ?>
                    <td>
                      <?php $estados_posibles = unserialize(ID_ESTADOS_POSIBLES_CONEXION_EVALUACION_TRAZABILIDAD); ?>
                      <select name="conexiones[<?=$key?>][estado_fin_trazabilidad]"  style="<?= (!$conexion->tarea_id_destino ? '' : 'display:none') ?>" >
                      <?php foreach($estados_posibles as $estado_k => $estado_v): ?>
                        <option value="<?=$estado_k?>" <?= ($conexion->estado_fin_trazabilidad ==  $estado_k ? 'selected' : '') ?>> <?=$estado_v?> </option>
                      <?php endforeach; ?>
                      </select>
                      <p id="no_traza_<?=$key?>" style="<?= ($conexion->tarea_id_destino ? 'display:block' : 'display:none') ?>">No envía</p>
                    </td>
                    <?php endif; ?>

                    <td>
                        <?php if ($conexion->tipo == 'evaluacion' || $conexion->tipo == 'paralelo_evaluacion'): ?>
                        <input type="text" name="conexiones[<?=$key?>][regla]" value="<?= htmlspecialchars($conexion->regla) ?>" title="Los nombres de campos escribalos anteponiendo @@. Ej: @@edad >= 18" />
                        <div class="btn-group asistencia" style="display: inline-block; vertical-align: top;">
                            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="icon-th-list"></span><span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <?php foreach ($conexion->TareaOrigen->Proceso->getCampos() as $c): ?>
                                    <li><a href="#">@@<?= $c->nombre ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php else: ?>
                        <p>N/A</p>
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <input type="hidden" name="conexiones[<?=$key?>][tipo]" value="<?=$conexion->tipo?>" />
                        <button class="btn botonEliminarConexion" type="button"><span class="icon-trash"></span></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>

        </table>

</div>
<div class="modal-footer">
    <a href="<?= site_url('backend/procesos/eliminar_conexiones/' . $conexiones[0]->TareaOrigen->id) ?>" class="btn btn-danger pull-left" onclick="return confirm('¿Esta seguro que desea eliminar esta conexión?')">Eliminar</a>
    <button type="submit" class="btn btn-primary">Guardar</button>
    <!--a href="#" onclick="javascript:$('#formEditarConexion').submit();return false;" class="btn btn-primary">Guardar</a-->
    <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
</div>
</form>

<script type="text/javascript">
    $(document).ready(function(){
        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        $("#selectGruposUsuarios").select2();

        $("[rel=tooltip]").tooltip();

        $(".datepicker")
        .datepicker({
            format: "dd-mm-yyyy",
            weekStart: 1,
            autoclose: true,
            language: "es"
        });

        $('#formEditarTarea .nav-tabs a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        //Permite borrar pasos
        $(".tab-pasos").on("click",".delete",function(){
            $(this).closest("tr").remove();
            return false;
        });
        //Permite agregar nuevos pasos
        $(".tab-pasos .form-agregar-paso button").click(function(){
            var $form=$(".tab-pasos .form-agregar-paso");

            var pos=1+$(".tab-pasos table tbody tr").size();
            var formularioId=$form.find(".pasoFormulario option:selected").val();
            var formularioNombre=$form.find(".pasoFormulario option:selected").text();
            var modo=$form.find(".pasoModo option:selected").val();
            var regla=$form.find(".pasoRegla").val();
            if($form.find(".pasoNombre").val().length) {
              var nombre = $form.find(".pasoNombre").val();
            }
            else {
                var nombre = formularioNombre;
            }

            var html="<tr>";
            html+="<td>"+pos+"</td>";
            html+='<td><a title="Editar" target="_blank" href="'+site_url+'backend/formularios/editar/'+formularioId+'">'+formularioNombre+'</td>';
            html+="<td>"+nombre+"</td>";
            html+="<td>"+regla+"</td>";
            html+="<td>"+modo+"</td>";
            html+='<td>';
            html+='<input type="hidden" name="pasos['+pos+'][id]" value="" />';
            html+='<input type="hidden" name="pasos['+pos+'][formulario_id]" value="'+formularioId+'" />';
            html+='<input type="hidden" name="pasos['+pos+'][regla]" value="'+escapeHtml(regla)+'" />';
            html+='<input type="hidden" name="pasos['+pos+'][nombre]" value="'+escapeHtml(nombre)+'" />';
            html+='<input type="hidden" name="pasos['+pos+'][modo]" value="'+modo+'" />';
            html+='<a class="delete" title="Eliminar" href="#"><span class="icon-trash"></span></a>';
            html+='</td>';
            html+="</tr>";

            $(".tab-pasos table tbody").append(html);

            return false;
        });
        //Permite que los pasos sean reordenables
        $(".tab-pasos table tbody").sortable({
            revert: true,
            stop: function(){
                //Reordenamos las posiciones
                $(this).find("tr").each(function(i,e){
                    $(e).find("td:nth-child(1)").text(i+1);
                    $(e).find("input[name*=formulario_id]").attr("name","pasos["+(i+1)+"][formulario_id]");
                    $(e).find("input[name*=regla]").attr("name","pasos["+(i+1)+"][regla]");
                    $(e).find("input[name*=modo]").attr("name","pasos["+(i+1)+"][modo]");
                });
            }
        });

        //Permite borrar eventos
        $(".tab-eventos").on("click",".delete",function(){
            $(this).closest("tr").remove();
            return false;
        });
        //Permite agregar nuevos eventos
        $(".tab-eventos .form-agregar-evento button").click(function(){
            var $form=$(".tab-eventos .form-agregar-evento");

            var pos=1+$(".tab-eventos table tbody tr").size();
            var accionId=$form.find(".eventoAccion option:selected").val();
            var accionNombre=$form.find(".eventoAccion option:selected").text();
            var regla=$form.find(".eventoRegla").val();
            var instante=$form.find(".eventoInstante option:selected").val();
            var pasoId=$form.find(".eventoPasoId option:selected").val();
            var pasoNombre=$form.find(".eventoPasoId option:selected").text();
            var pasoTitle=$form.find(".eventoPasoId option:selected").attr("title");

            var html="<tr>";
            html+="<td>"+pos+"</td>";
            html+='<td><a title="Editar" target="_blank" href="'+site_url+'backend/acciones/editar/'+accionId+'">'+accionNombre+'</td>';
            html+="<td>"+regla+"</td>";
            html+="<td>"+instante+"</td>";
            html+="<td><abbr title='"+pasoTitle+"'>"+pasoNombre+"</abbr></td>";
            html+='<td>';
            html+='<input type="hidden" name="eventos['+pos+'][accion_id]" value="'+accionId+'" />';
            html+='<input type="hidden" name="eventos['+pos+'][regla]" value="'+escapeHtml(regla)+'" />';
            html+='<input type="hidden" name="eventos['+pos+'][instante]" value="'+instante+'" />';
            html+='<input type="hidden" name="eventos['+pos+'][paso_id]" value="'+pasoId+'" />';
            html+='<a class="delete" title="Eliminar" href="#"><span class="icon-trash"></span></a>';
            html+='</td>';
            html+="</tr>";

            $(".tab-eventos table tbody").append(html);

            return false;
        });

        //$("#modalEditarTarea form input[name=socket_id_emisor]").val(socketId);
        //$("#modalEditarTarea .botonEliminar").attr("href",function(i,href){return href+"?socket_id_emisor="+socketId;})
    });
</script>


<div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3>Editar Tarea</h3>
</div>
<form id="formEditarTarea" class="ajaxForm" method="POST" action="<?= site_url('backend/procesos/editar_tarea_form/' . $tarea->id) ?>">
<div class="modal-body">
        <div class="validacion validacion-error"></div>

        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab1">Definición</a></li>
                <li><a href="#tab2">Asignación</a></li>
                <li><a href="#tab3">Usuarios</a></li>
                <li><a href="#tab4">Pasos</a></li>
                <li><a href="#tab5">Eventos</a></li>
                <li><a href="#tab6">Vencimiento</a></li>
                <li><a href="#tab7">Otros</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab1">
                    <label for="nombre"><strong>Nombre</strong></label>
                    <input class="span12" id="nombre" name="nombre" type="text" value="<?= $tarea->nombre ?>" />
                    <br/>
                    <span class="control-label"><strong>Activación</strong></span>
                    <div class="row-fluid">
                        <div class="span6">
                            <label class="checkbox" for="Tinicial"><input id="Tinicial" name="inicial" value="1" type="checkbox" <?= $tarea->inicial ? 'checked' : '' ?>> Tarea Inicial</label>
                            <label class="checkbox" for="Tfinal"><input id="Tfinal" name="final" value="1" type="checkbox" <?= $tarea->final ? 'checked' : '' ?>> Tarea Final</label>
                        </div>
                        <div class="span6">
                            <script>
                                $(document).ready(function(){
                                    $("input[name=activacion]").change(function(){
                                        if($("input[name=activacion]:checked").val()=='entre_fechas')
                                            $("#activacionEntreFechas").show();
                                        else
                                            $("#activacionEntreFechas").hide();
                                    }).change();

                                    $('#fechaInicial').datepicker({
                                        format: 'dd-mm-yyyy',
                                        startDate: '0d',
                                        autoclose: true
                                    }).on('changeDate', function (selected) {
                                        var minDate = new Date(selected.date.valueOf());
                                        $('#fechaFinal').datepicker({setEndDate: minDate, format: 'dd-mm-yyyy'});
                                    });

                                    $("#fechaFinal").datepicker({
                                        format: 'dd-mm-yyyy',
                                        autoclose: true
                                    }).on('changeDate', function (selected) {
                                        var minDate = new Date(selected.date.valueOf());
                                        $('#fechaInicial').datepicker({setEndDate: minDate, format: 'dd-mm-yyyy'});
                                    });
                                });
                            </script>
                            <label class="radio" for="Tactiva"><input id="Tactiva" name="activacion" value="si" type="radio" <?= $tarea->activacion == 'si' ? 'checked' : '' ?>>Tarea activada</label>
                            <label class="radio" for="TentreFechas"><input id="TentreFechas" name="activacion" value="entre_fechas" type="radio" <?= $tarea->activacion == 'entre_fechas' ? 'checked' : '' ?>>Tarea activa entre fechas</label>
                            <div id="activacionEntreFechas" class="hide form-horizontal form-horizontal-fino" style="margin-left: 20px;">
                              <div class="control-group">
                                <label class="control-label" for="fechaInicial">Fecha inicial</label>
                                <div class="controls">
                                  <input class="datepicker_" id="fechaInicial" rel="tooltip" title="Deje el campo en blanco para no considerar una fecha inicial" type="text" name="activacion_inicio" value="<?= $tarea->activacion_inicio ? date('d-m-Y', $tarea->activacion_inicio) : '' ?>" placeholder="DD-MM-AAAA" />
                                </div>
                              </div>
                              <div class="control-group">
                                <label class="control-label" for="fechaFinal">Fecha final</label>
                                <div class="controls">
                                  <input class="datepicker_" id="fechaFinal" rel="tooltip" title="Deje el campo en blanco para no considerar una fecha final" type="text" name="activacion_fin" value="<?= $tarea->activacion_fin ? date('d-m-Y', $tarea->activacion_fin) : '' ?>" placeholder="DD-MM-AAAA" />
                                </div>
                              </div>
                            </div>
                            <label class="radio" for="Tdesactivada"><input id="Tdesactivada" name="activacion" value="no" type="radio" <?= $tarea->activacion == 'no' ? 'checked' : '' ?>>Tarea desactivada</label>
                        </div>
                    </div>
                    <label class="checkbox" for="trazabilidad"><strong><input type="checkbox" id="trazabilidad" name="trazabilidad" value="1" <?= ($tarea->trazabilidad ? 'checked' : '') ?> /> Trazabilidad</strong></label>
					          <br/>
                    <label for="previsualizacion"><strong>Información para previsualización</strong></label>
                    <textarea class="span12" rows="5" id="previsualizacion" name="previsualizacion"><?=$tarea->previsualizacion?></textarea>
                    <div class="help-block">Información que aparecera en la bandeja de entrada al pasar el cursor por encima.</div>
                </div>
                <div class="tab-pane" id="tab2">
                    <script type="text/javascript">
                        $(document).ready(function(){
                            $("input[name=asignacion]").click(function(){
                                if(this.value=="usuario")
                                    $("#optionalAsignacionUsuario").removeClass("hide");
                                else
                                    $("#optionalAsignacionUsuario").addClass("hide");
                            });
                        });
                    </script>
                    <span class="control-label"><strong>Regla de asignación</strong></span>
                    <label class="radio" rel="tooltip" title="Los usuarios se asignan en forma ciclica. Se van turnando dentro del grupo de usuarios en forma circular." for="ciclica"><input id="ciclica" type="radio" name="asignacion" value="ciclica" <?= $tarea->asignacion == 'ciclica' ? 'checked' : '' ?> /> Cíclica</label>
                    <label class="radio" rel="tooltip" title="Al finalizar la tarea anterior, se le pregunta al usuario a quien se le va a asignar esta tarea." for="manual"><input id="manual" type="radio" name="asignacion" value="manual" <?= $tarea->asignacion == 'manual' ? 'checked' : '' ?> /> Manual</label>
                    <label class="radio" rel="tooltip" title="La tarea queda sin asignar, y los usuarios mismos deciden asignarsela segun corresponda." for="autoservicio"><input id="autoservicio" type="radio" name="asignacion" value="autoservicio" <?= $tarea->asignacion == 'autoservicio' ? 'checked' : '' ?> /> Auto Servicio</label>

                    <div class="form-inline">
                      <label class="radio" rel="tooltip" title="Ingresar el id de usuario a quien se le va asignar. Se puede ingresar una variable que haya almacenado esta información. Ej: @@usuario_inical" for="usuario"><input type="radio" name="asignacion" id="usuario" value="usuario" <?= $tarea->asignacion == 'usuario' ? 'checked' : '' ?> /> Usuario</label>
                      <div id="optionalAsignacionUsuario" class="<?= $tarea->asignacion == 'usuario' ? '' : 'hide' ?>" style="margin-left: 20px;">
                        <label class="hidden-accessible" for="asignacion_usuario">Usuario</label>
                          <input type="text" name="asignacion_usuario" id="asignacion_usuario" value="<?= $tarea->asignacion_usuario ?>" placeholder='Ej: @@id' />
                      </div>
                    </div>
                    <br />
                    <label class="checkbox" for="notificarCorreo"><input type="checkbox" id="notificarCorreo" name="asignacion_notificar" value="1" <?= $tarea->asignacion_notificar ? 'checked' : '' ?> /> Notificar vía correo electrónico al usuario asignado.</label>
                </div>
                <div class="tab-pane" id="tab3">
                    <script type="text/javascript">
                        $(document).ready(function(){
                            $("input[name=acceso_modo]").change(function(){
                                if(this.value=="grupos_usuarios")
                                    $("#optionalGruposUsuarios").removeClass("hide");
                                else
                                    $("#optionalGruposUsuarios").addClass("hide");
                            });
                        });
                    </script>
                    <label class='radio' for="cualquierPersona"><input id="cualquierPersona" type="radio" name="acceso_modo" value="publico" <?= $tarea->acceso_modo == 'publico' ? 'checked' : '' ?> /> Cualquier persona puede acceder.</label>
                    <label class='radio' for="soloRegistrados"><input id="soloRegistrados" type="radio" name="acceso_modo" value="registrados" <?= $tarea->acceso_modo == 'registrados' ? 'checked' : '' ?> /> Sólo los usuarios registrados.</label>
                    <!--<label class='radio' for="soloClaveunica"><input id="soloClaveunica" type="radio" name="acceso_modo" value="claveunica" <?= $tarea->acceso_modo == 'claveunica' ? 'checked' : '' ?> /> Sólo los usuarios registrados con ClaveUnica.</label>-->
                    <label class='radio' for="soloGrupo"><input id="soloGrupo" type="radio" name="acceso_modo" value="grupos_usuarios" <?= $tarea->acceso_modo == 'grupos_usuarios' ? 'checked' : '' ?> /> Sólo los siguientes grupos de usuarios pueden acceder.</label>
                    <div id="optionalGruposUsuarios" style="height: 300px;" class="<?= $tarea->acceso_modo == 'grupos_usuarios' ? '' : 'hide' ?>">
                      <label class="hidden-accessible" for="selectGruposUsuarios">Grupos de usuarios</label>
                      <select id="selectGruposUsuarios" class="input-xlarge" name="grupos_usuarios[]" multiple>
                          <?php foreach($tarea->Proceso->Cuenta->GruposUsuarios as $g):?>
                              <option value="<?=$g->id?>" <?=in_array($g->id,explode(',',$tarea->grupos_usuarios))?'selected':''?>><?=$g->nombre?></option>
                          <?php endforeach ?>
                          <?php foreach(explode(',',$tarea->grupos_usuarios) as $g): ?>
                              <?php if(!is_numeric($g)): ?>
                              <option selected><?=$g?></option>
                              <?php endif ?>
                          <?php endforeach ?>
                      </select>
                      <div class='help-block'>Puede incluir variables usando @@. Las variables deben contener el numero id del grupo de usuarios.</div>
                    </div>
                </div>
                <div class="tab-pasos tab-pane" id="tab4">

                    <table class="table">
                      <caption class="hide-text">Pasos</caption>
                        <thead>
                            <tr class="form-agregar-paso">
                                <td></td>
                                <td>
                                  <label class="hidden-accessible" for="formulario">formulario</label>
                                  <select class="pasoFormulario input-small" id="formulario">
                                      <?php foreach ($formularios as $f): ?>
                                          <option value="<?= $f->id ?>"><?= $f->nombre ?></option>
                                      <?php endforeach; ?>
                                  </select>
                                </td>
                                <td>
                                  <label class="hidden-accessible" for="nombre_paso">Título del paso</label>
                                  <input type="text" class="pasoNombre input-medium" value="" name="nombre_paso" id="nombre_paso" placeholder="Título del paso" />
                                </td>
                                <td>
                                  <label class="hidden-accessible" for="regla">Condición</label>
                                  <input class="pasoRegla" type="text" id="regla" placeholder="Escribir regla condición aquí" />
                                </td>
                                <td>
                                  <label class="hidden-accessible" for="modo">Modo</label>
                                  <select class="pasoModo input-small" id="modo">
                                      <option value="edicion">Edición</option>
                                      <option value="visualizacion">Visualización</option>
                                  </select>
                                </td>
                                <td>
                                  <button type="button" class="btn" title="Agregar"><span class="icon-plus"></span></button>
                                </td>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>Formulario</th>
                                <th>Título del paso</th>
                                <th>Condición</th>
                                <th>Modo</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tarea->Pasos as $key => $p): ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td><a title="Editar" target="_blank" href="<?= site_url('backend/formularios/editar/' . $p->Formulario->id) ?>"><?= $p->Formulario->nombre ?></a></td>
                                    <td><?= $p->nombre ?></td>
                                    <td><?= $p->regla ?></td>
                                    <td><?= $p->modo ?></td>
                                    <td>
                                        <input type="hidden" name="pasos[<?= $key + 1 ?>][id]" value="<?= $p->id ?>" />
                                        <input type="hidden" name="pasos[<?= $key + 1 ?>][formulario_id]" value="<?= $p->formulario_id ?>" />
                                        <input type="hidden" name="pasos[<?= $key + 1 ?>][nombre]" value="<?= $p->nombre ?>" />
                                        <input type="hidden" name="pasos[<?= $key + 1 ?>][regla]" value="<?= $p->regla ?>" />
                                        <input type="hidden" name="pasos[<?= $key + 1 ?>][modo]" value="<?= $p->modo ?>" />
                                        <a class="delete" title="Eliminar paso" href="#"><span class="icon-trash"></span></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <label class="checkbox" for="incluirUltimoPaso"><input type="checkbox" id="incluirUltimoPaso" name="paso_confirmacion" value="1" <?=$tarea->paso_confirmacion?'checked':''?> > Incluir último paso de confirmación antes de avanzar la tarea.</label>
                </div>
                <div class="tab-eventos tab-pane" id="tab5">
                    <table class="table">
                      <caption class="hide-text">Eventos</caption>
                        <thead>
                            <tr class="form-agregar-evento">
                                <td></td>
                                <td>
                                  <label class="hidden-accessible" for="accion">Acción</label>
                                  <select class="eventoAccion input-medium" id="accion">
                                      <?php foreach ($acciones as $f): ?>
                                          <option value="<?= $f->id ?>"><?= $f->nombre ?></option>
                                      <?php endforeach; ?>
                                  </select>
                                </td>
                                <td>
                                  <label class="hidden-accessible" for="condicion">Condición</label>
                                  <input class="eventoRegla input-medium" id="condicion" type="text" placeholder="Escribir regla condición" />
                                </td>
                                <td>
                                  <label class="hidden-accessible" for="instante">Instante</label>
                                  <select class="eventoInstante input-small" id="instante">
                                      <option value="antes">Antes</option>
                                      <option value="despues">Después</option>
                                  </select>
                                </td>
                                <td>
                                  <label class="hidden-accessible" for="momento">Momento</label>
                                  <select class="eventoPasoId input-medium" id="momento">
                                      <option value="">Ejecutar Tarea</option>
                                      <?php foreach ($tarea->Pasos as $p): ?>
                                      <option value="<?=$p->id?>" title="<?=$p->Formulario->nombre?>">Ejecutar Paso <?=$p->orden?></option>
                                      <?php endforeach ?>
                                  </select>
                                </td>
                                <td>
                                  <button type="button" class="btn" title="Agregar"><span class="icon-plus"></span></button>
                                </td>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>Accion</th>
                                <th>Condición</th>
                                <th>Instante</th>
                                <th>Momento</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tarea->Eventos as $key => $p): ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td><a title="Editar" target="_blank" href="<?= site_url('backend/acciones/editar/' . $p->Accion->id) ?>"><?= $p->Accion->nombre ?></a></td>
                                    <td><?= $p->regla ?></td>
                                    <td><?= $p->instante ?></td>
                                    <td><?=$p->paso_id?'<abbr title="'.$p->Paso->Formulario->nombre.'">Ejecutar Paso '.$p->Paso->orden.'</abbr>':'Ejecutar Tarea'?></td>
                                    <td>
                                        <input type="hidden" name="eventos[<?= $key + 1 ?>][accion_id]" value="<?= $p->accion_id ?>" />
                                        <input type="hidden" name="eventos[<?= $key + 1 ?>][regla]" value="<?= $p->regla ?>" />
                                        <input type="hidden" name="eventos[<?= $key + 1 ?>][instante]" value="<?= $p->instante ?>" />
                                        <input type="hidden" name="eventos[<?= $key + 1 ?>][paso_id]" value="<?= $p->paso_id ?>" />
                                        <a class="delete" title="Eliminar evento" href="#"><span class="icon-trash"></span></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="tab6">
                    <script>
                        $(document).ready(function(){
                            $("input[name=vencimiento]").change(function(){
                                if(this.checked)
                                    $("#vencimientoConfig").show();
                                else
                                    $("#vencimientoConfig").hide();
                            }).change();

                            $("select[name=vencimiento_unidad]").change(function(){
                                if(this.value=="D")
                                    $("#habilesConfig").show();
                                else
                                    $("#habilesConfig").hide();
                            }).change();
                        });
                    </script>
                    <label class="checkbox" for="vencimiento"><input type="checkbox" id="vencimiento" name="vencimiento" value="1" <?=$tarea->vencimiento?'checked':''?> /> ¿La etapa tiene vencimiento?</label>
                    <div id="vencimientoConfig" class="hide" style="margin-left: 20px;">
                      <div class="form-inline">
                        La etapa se vencera
                        <label class="hidden-accessible" for="vencimiento_valor">Cantidad</label>
                        <input type="text" name="vencimiento_valor" id="vencimiento_valor" class="input-mini" value="<?= $tarea->vencimiento_valor?$tarea->vencimiento_valor:5 ?>" />
                        <label class="hidden-accessible" for="vencimiento_unidad">Unidad de medida</label>
                        <select name="vencimiento_unidad" class="input-small" id="vencimiento_unidad">
                            <option value="D" <?= $tarea->vencimiento_unidad == 'D' ? 'selected' : '' ?>>días</option>
                            <option value="W" <?= $tarea->vencimiento_unidad == 'W' ? 'selected' : '' ?>>meses</option>
                            <option value="M" <?= $tarea->vencimiento_unidad == 'M' ? 'selected' : '' ?>>años</option>
                        </select>
                        despues de completada la etapa anterior.
                      </div>
                        <br />
                        <label id='habilesConfig' class='checkbox' for="habilesConsid"><input id="habilesConsid" type='checkbox' name='vencimiento_habiles' value='1' <?=$tarea->vencimiento_habiles?'checked':''?> /> Considerar solo días habiles.</label>
                        <div class="form-inline">
                          <label class="checkbox" for="notificar"><input type="checkbox" id="notificar" name="vencimiento_notificar" value="1" <?=$tarea->vencimiento_notificar?'checked':''?> /> Notificar</label>
                          <label for="vencimiento_notificar_dias">cuando quede</label>
                          <input class="input-mini" type="text" name="vencimiento_notificar_dias" id="vencimiento_notificar_dias" value="<?=$tarea->vencimiento_notificar_dias?>" /> día al siguiente
                          <label for="vencimiento_notificar_email">correo:</label>
                        </div>
                        <div style="margin-top:5px;">
                         <input style="margin-left: 20px;" type="text" name="vencimiento_notificar_email" id="vencimiento_notificar_email" placeholder="ejemplo@mail.com" value="<?=$tarea->vencimiento_notificar_email?>" />
                         <div style="margin-left: 20px;" class="help-block">Tambien se pueden usar variables. Ej: @@email</div>
                       </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab7">
                    <script type="text/javascript">
                        $(document).ready(function(){
                            $("input[name=almacenar_usuario]").click(function(){
                                if(this.checked)
                                    $("#optionalAlmacenarUsuario").removeClass("hide");
                                else
                                    $("#optionalAlmacenarUsuario").addClass("hide");
                            });
                        });
                    </script>
                    <label class="checkbox" for="almacenar_usuario"><input id="almacenar_usuario" type="checkbox" name="almacenar_usuario" value="1" <?= $tarea->almacenar_usuario ? 'checked' : '' ?> /> ¿Almacenar el identificador del usuario que lleva a cabo esta tarea?</label>
                    <div id="optionalAlmacenarUsuario" class="<?= $tarea->almacenar_usuario ? '' : 'hide' ?> form-inline">
                        <label for="almacenar_usuario_variable">Variable</label>
                        <div class="input-prepend">
                            <span class="add-on">@@</span><input type="text" name="almacenar_usuario_variable" id="almacenar_usuario_variable" value="<?= $tarea->almacenar_usuario_variable ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
<div class="modal-footer">
    <a href="<?= site_url('backend/procesos/eliminar_tarea/' . $tarea->id) ?>" class="btn btn-danger pull-left" onclick="return confirm('¿Esta seguro que desea eliminar esta tarea?')">Eliminar</a>
    <button type="submit" class="btn btn-primary">Guardar</button>
    <!--a href="#" onclick="javascript:$('#formEditarTarea').submit();return false;" class="btn btn-primary">Guardar</a-->
    <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
</div>
</form>

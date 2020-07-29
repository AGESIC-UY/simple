<script type="text/javascript">
    $(document).ready(function () {

        $("#Tinicial").click(function () {
            if ($(this).is(':checked')) {
                $('#div_trazabilidad_cabezal').removeClass('hidden').show();
            }
            else {
                $('#div_trazabilidad_cabezal').hide();
            }
        });

        $("#Tfinal").change(function () {
            if ($(this).is(':checked')) {
                $('#div_trazabilidad_estado').removeClass('hidden').show();
            }
            else {
                $('#div_trazabilidad_estado').hide();
            }
        });

        function escapeHtml(text) {
            return text
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
        }

        $("#selectGruposUsuarios").select2({tags: true});

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
        $(".tab-pasos").on("click", ".delete", function () {
            $(this).closest("tr").remove();
            return false;
        });
        //Permite agregar nuevos pasos
        $(".tab-pasos .form-agregar-paso button").click(function () {
            var $form = $(".tab-pasos .form-agregar-paso");

            var pos = 1 + $(".tab-pasos table tbody tr").size();
            var formularioId = $form.find(".pasoFormulario option:selected").val();
            var formularioNombre = $form.find(".pasoFormulario option:selected").text();
            var modo = $form.find(".pasoModo option:selected").val();
            var generar_pdf = $form.find(".pasoGenerar_pdf option:selected").val();
            var enviar_traza = $form.find(".pasoEnviar_traza option:selected").val();
            var etiqueta_traza = $form.find(".pasoEtiqueta_traza option:selected").val();
            var visible_traza = $form.find(".pasoVisible_traza option:selected").val();

            var regla = $form.find(".pasoRegla").val();
            if ($form.find(".pasoNombre").val().length) {
                var nombre = $form.find(".pasoNombre").val();
            }
            else {
                var nombre = formularioNombre;
            }

            var html = "<tr>";

            html += "<td>" + pos + "</td>";

            html += '<td>';
            html += '<select class="input-medium" name="pasos[' + pos + '][formulario_id]" disabled>';
<?php foreach ($formularios as $f): ?>
                html += '<option value="<?= $f->id ?>"><?= $f->nombre ?></option>';
<?php endforeach; ?>
            html += '</select>';
            html += '</td>';
            html += '<td>';
            html += '<a title="Editar formulario ' + formularioNombre + '" target="_blank" href="' + site_url + 'backend/formularios/editar/' + formularioId + '" id="pasos_' + pos + '__formulario_id_"><span class="icon-file-text-alt"></span></a>';
            html += '</td>';

            html += '<td><input type="text" name="pasos[' + pos + '][nombre]" value="' + escapeHtml(nombre) + '" class="pasoNombre input-medium" disabled/></td>';

            html += '<td><input type="text" name="pasos[' + pos + '][regla]" value="' + escapeHtml(regla) + '" class="pasoRegla" disabled/></td>';

            html += '<td>';
            html += '<select class="input-small" name="pasos[' + pos + '][modo]" disabled>';
            html += '<option value="edicion">Edición</option>';
            html += '<option value="visualizacion" selected>Visualización</option>';
            html += '</select>';
            html += '</td>';

            html += '<td>';
            html += '<select class="input-mini" name="pasos[' + pos + '][generar_pdf]" title="Generar PDF" disabled>';
            html += '<option value="1">Si</option>';
            html += '<option value="0" selected>No</option>';
            html += '</select>';
            html += '</td>';

            html += '<td>';
            html += '<select class="input-mini" name="pasos[' + pos + '][enviar_traza]" title="Enviar traza" disabled>';
            html += '<option value="1">Si</option>';
            html += '<option value="0" selected>No</option>';
            html += '</select>';
            html += '</td>';
            
            html += '<td>';
            html += '<select class="pasoEtiqueta_traza input-medium" name="pasos[' + pos + '][etiqueta_traza]" title="Etiqueta" disabled>';
            html += '<option value=""></option>';
            html += '<?php foreach ($etiquetas as $etiqueta) { ?>';
            html += '<option value="<?php echo $etiqueta->etiqueta ?>" > <?php echo $etiqueta->etiqueta ?></option>';
            html += '<?php } ?>';
            html += '</select>';
            html += '</td>';
                                            
                                        
            html += '<td>';
            html += '<select class="pasoVisible_traza input-medium" name="pasos[' + pos + '][visible_traza]" title="Visibilidad" disabled>';
            html += '<option value="VISIBLE">Visible</option>';
            html += '<option value="USO_INTERNO">Uso interno</option>';
            html += '</select>';
            html += '</td>';                                        
                                        
            html += '<td>';
            html += '<input type="hidden" name="pasos[' + pos + '][id]" value="" />';
            html += '<a class="delete" title="Eliminar" href="#"><span class="icon-trash"></span></a>';
            html += '</td>';

            //habilitar deshabilitar paso
            html += '<td>';
            html += '<input type="checkbox" id="check-[' + pos + '][id]" class="habilitar" hidden/>';
            html += '<label for="check-[' + pos + '][id]"><span class="icon-edit" title="Habilitar/Deshabilitar paso" style="color:#174f82;"></span></label>';
            html += '</td>';

            html += "</tr>";

            $(".tab-pasos table tbody").append(html);

            $("select[name^='pasos[" + pos + "][formulario_id]'] option[value='" + formularioId + "']").attr('selected', 'selected');
            $("select[name^='pasos[" + pos + "][modo]'] option[value='" + modo + "']").attr('selected', 'selected');
            $("select[name^='pasos[" + pos + "][generar_pdf]'] option[value='" + generar_pdf + "']").attr('selected', 'selected');
            $("select[name^='pasos[" + pos + "][enviar_traza]'] option[value='" + enviar_traza + "']").attr('selected', 'selected');
            $("select[name^='pasos[" + pos + "][etiqueta_traza]'] option[value='" + etiqueta_traza + "']").attr('selected', 'selected');
            $("select[name^='pasos[" + pos + "][visible_traza]'] option[value='" + visible_traza + "']").attr('selected', 'selected');

            $("select[name*='[formulario_id]']").click(function () {
                var id_link_form = '#' + $(this).attr('name').replace("[", "_").replace("]", "_").replace("[", "_").replace("]", "_");
                var nueva_etiqueta_link = '<span class="icon-file-text-alt"></span>';
                var nuevo_link = site_url + 'backend/formularios/editar/' + $(this).val();

                $(id_link_form).html(nueva_etiqueta_link);
                $(id_link_form).attr('href', nuevo_link);
            });

            return false;
        });
        //Permite que los pasos sean reordenables
        $(".tab-pasos table tbody").sortable({
            revert: true,
            stop: function () {
                //Reordenamos las posiciones
                $(this).find("tr").each(function (i, e) {
                    $(e).find("td:nth-child(1)").text(i + 1);
                    $(e).find("select[name*=formulario_id]").attr("name", "pasos[" + (i + 1) + "][formulario_id]");
                    $(e).find("input[name*=nombre]").attr("name", "pasos[" + (i + 1) + "][nombre]");
                    $(e).find("input[name*=regla]").attr("name", "pasos[" + (i + 1) + "][regla]");
                    $(e).find("select[name*=modo]").attr("name", "pasos[" + (i + 1) + "][modo]");
                    $(e).find("select[name*=generar_pdf]").attr("name", "pasos[" + (i + 1) + "][generar_pdf]");
                    $(e).find("select[name*=etiqueta_traza]").attr("name", "pasos[" + (i + 1) + "][etiqueta_traza]");
                    $(e).find("select[name*=visible_traza]").attr("name", "pasos[" + (i + 1) + "][visible_traza]");
                    $(e).find("select[name*=enviar_traza]").attr("name", "pasos[" + (i + 1) + "][enviar_traza]");

                    var nuevo_nombre_form = $(e).find("select[name*=formulario_id] option:selected").text();
                    var nuevo_id_form = $(e).find("select[name*=formulario_id] option:selected").val();
                    var nuevo_paso_form = (i + 1);
                    var nuevo_link_form = site_url + 'backend/formularios/editar/' + nuevo_id_form;
                    $(e).find("select[name*=formulario_id]").next().attr('id', 'pasos_' + nuevo_paso_form + '__formulario_id_');
                    $(e).find("select[name*=formulario_id]").next().attr('href', nuevo_link_form);
                    $(e).find("select[name*=formulario_id]").next().text(nuevo_nombre_form);
                });
            }
        });

        //Permite borrar eventos
        $(".tab-eventos").on("click", ".delete", function () {
            $(this).closest("tr").remove();
            return false;
        });
        //Permite agregar nuevos eventos
        $(".tab-eventos .form-agregar-evento button").click(function () {
            var $form = $(".tab-eventos .form-agregar-evento");

            var pos = 1 + $(".tab-eventos table tbody tr").size();
            var accionId = $form.find(".eventoAccion option:selected").val();
            var accionNombre = $form.find(".eventoAccion option:selected").text();
            var regla = $form.find(".eventoRegla").val();
            var instante = $form.find(".eventoInstante option:selected").val();
            var pasoId = $form.find(".eventoPasoId option:selected").val();
            var pasoNombre = $form.find(".eventoPasoId option:selected").text();
            var pasoTitle = $form.find(".eventoPasoId option:selected").attr("title");
            var instanciarApi = $form.find(".eventoInstanciarApi option:selected").val();
            var instanciarApiText = $form.find(".eventoInstanciarApi option:selected").text();
            var variableErrorSoap = $form.find(".eventoVariableErrorSoap").val();
            var variableDescripcionErrorSoap = $form.find(".eventoDescripcionErrorSoap").val();
            var variableDescripcionTraza = $form.find(".eventoDescripcionTraza").val();
            var variableEtiquetaTraza = $form.find(".eventoEtiquetaTraza").val();
            var variableVisibleTraza = $form.find(".eventoVisibleTraza").val();
            var variableTipoRegistroTraza = $form.find(".eventoTipoRegistroTraza option:selected").val();
            var variableTipoRegistroTrazaText = $form.find(".eventoTipoRegistroTraza option:selected").text();
            var variableTraza = $form.find(".eventoTraza option:selected").val();
            var variableTrazaText = $form.find(".eventoTraza option:selected").text();


            var html = "<tr>";
            html += "<td>" + pos + "</td>";
            html += '<td><a title="Editar" target="_blank" href="' + site_url + 'backend/acciones/editar/' + accionId + '">' + accionNombre + '</td>';
            html += "<td>" + regla + "</td>";
            html += "<td>" + instante + "</td>";
            html += "<td><abbr title='" + pasoTitle + "'>" + pasoNombre + "</abbr></td>";
            html += "<td>" + instanciarApiText + "</td>";
            html += "<td>" + variableTrazaText + "</td>";
            html += "<td>" + variableTipoRegistroTrazaText + "</td>";
            html += "<td>" + variableDescripcionTraza + "</td>";
            html += "<td>" + variableEtiquetaTraza + "</td>";
            html += "<td>" + variableVisibleTraza + "</td>";
            html += "<td>" + variableDescripcionErrorSoap + "</td>";
            html += "<td>" + variableErrorSoap + "</td>";
            html += '<td>';
            html += '<input type="hidden" name="eventos[' + pos + '][accion_id]" value="' + accionId + '" />';
            html += '<input type="hidden" name="eventos[' + pos + '][regla]" value="' + escapeHtml(regla) + '" />';
            html += '<input type="hidden" name="eventos[' + pos + '][instante]" value="' + instante + '" />';
            html += '<input type="hidden" name="eventos[' + pos + '][paso_id]" value="' + pasoId + '" />';
            html += '<input type="hidden" name="eventos[' + pos + '][instanciar_api]" value="' + instanciarApi + '" />';
            html += '<input type="hidden" name="eventos[' + pos + '][traza]" value="' + variableTraza + '" />';
            html += '<input type="hidden" name="eventos[' + pos + '][tipo_registro_traza]" value="' + variableTipoRegistroTraza + '" />';
            html += '<input type="hidden" name="eventos[' + pos + '][descripcion_traza]" value="' + variableDescripcionTraza + '" />';
            html += '<input type="hidden" name="eventos[' + pos + '][etiqueta_traza]" value="' + variableEtiquetaTraza + '" />';
            html += '<input type="hidden" name="eventos[' + pos + '][visible_traza]" value="' + variableVisibleTraza + '" />';
            html += '<input type="hidden" name="eventos[' + pos + '][descripcion_error_soap]" value="' + variableDescripcionErrorSoap + '" />';
            html += '<input type="hidden" name="eventos[' + pos + '][variable_error_soap]" value="' + variableErrorSoap + '" />';
            html += '<a class="delete" title="Eliminar" href="#"><span class="icon-trash"></span></a>';
            html += '</td>';
            html += "</tr>";

            $(".tab-eventos table tbody").append(html);

            return false;
        });
        //Permite borrar Validaciones
        $(".tab-validaciones").on("click", ".delete", function () {
            $(this).closest("tr").remove();
            return false;
        });
        //Permite agregar nuevos Validaciones
        $(".tab-validaciones .form-agregar-validaciones button").click(function () {
            var $form = $(".tab-validaciones .form-agregar-validaciones");

            var pos = 1 + $(".tab-validaciones table tbody tr").size();
            var validacionId = $form.find(".validacionAccion option:selected").val();
            var validacionNombre = $form.find(".validacionAccion option:selected").text();
            var regla = $form.find(".validacionRegla").val();
            var instante = $form.find(".validacionInstante option:selected").val();
            var pasoId = $form.find(".validacionPasoId option:selected").val();
            var pasoNombre = $form.find(".validacionPasoId option:selected").text();
            var pasoTitle = $form.find(".validacionPasoId option:selected").attr("title");


            var html = "<tr>";
            html += "<td>" + pos + "</td>";
            html += '<td><a title="Editar" target="_blank" href="' + site_url + 'backend/validaciones/editar/' + validacionId + '">' + validacionNombre + '</td>';
            html += "<td>" + regla + "</td>";
            html += "<td>" + instante + "</td>";
            html += "<td><abbr title='" + pasoTitle + "'>" + pasoNombre + "</abbr></td>";
            html += '<td>';
            html += '<input type="hidden" name="validaciones[' + pos + '][validacion_id]" value="' + validacionId + '" />';
            html += '<input type="hidden" name="validaciones[' + pos + '][regla]" value="' + escapeHtml(regla) + '" />';
            html += '<input type="hidden" name="validaciones[' + pos + '][instante]" value="' + instante + '" />';
            html += '<input type="hidden" name="validaciones[' + pos + '][paso_id]" value="' + pasoId + '" />';
            html += '<a class="delete" title="Eliminar" href="#"><span class="icon-trash"></span></a>';
            html += '</td>';
            html += "</tr>";

            $(".tab-validaciones table tbody").append(html);
            return false;
        });

        //Permite habilitar y deshabilitar filas
        $(".tab-pasos").on("click", ".habilitar", function () {
            var row = $(this).closest('tr');
            if ($(this).is(':checked'))
            {
                $(row).find('.pasoRegla, .input-medium, .input-mini, .input-small').prop("disabled", false);
            }
            else
            {
                $(row).find('.pasoRegla, .input-medium, .input-mini, .input-small').prop("disabled", true);
            }
        });
        //$("#modalEditarTarea form input[name=socket_id_emisor]").val(socketId);
        //$("#modalEditarTarea .botonEliminar").attr("href",function(i,href){return href+"?socket_id_emisor="+socketId;});

        $('#trazabilidad').click(function () {
            if ($(this).is(':checked')) {

                if ($('#Tinicial').is(':checked') && !$('#Tfinal').is(':checked')) {
                    $('#div_trazabilidad_cabezal').removeClass('hidden').show();
                }

                if (!$('#Tinicial').is(':checked') && $('#Tfinal').is(':checked')) {
                    $('#div_trazabilidad_estado').removeClass('hidden').show();
                }

                if ($('#Tinicial').is(':checked') && $('#Tfinal').is(':checked')) {
                    $('#div_trazabilidad_cabezal').removeClass('hidden').show();
                    $('#div_trazabilidad_estado').removeClass('hidden').show();
                }

                $('#trazabilidad_id_oficina_box').removeClass('hidden').show();
                $('#trazabilidad_nombre_oficina_box').removeClass('hidden').show();
                $('#div_trazabilidad_etiqueta').removeClass('hidden').show();
                $('#div_trazabilidad_visible').removeClass('hidden').show();

            }
            else {
                $('#trazabilidad_id_oficina_box').hide();
                $('#trazabilidad_nombre_oficina_box').hide();
                $('#div_trazabilidad_cabezal').hide();
                $('#div_trazabilidad_estado').hide();
                $('#div_trazabilidad_etiqueta').hide();
                $('#div_trazabilidad_visible').hide();
            }
        });

        $("select[name*='[formulario_id]']").click(function () {
            var id_link_form = '#' + $(this).attr('name').replace("[", "_").replace("]", "_").replace("[", "_").replace("]", "_");
            var nueva_etiqueta_link = '<span class="icon-file-text-alt"></span>';
            var nuevo_link = site_url + 'backend/formularios/editar/' + $(this).val();

            $(id_link_form).html(nueva_etiqueta_link);
            $(id_link_form).attr('href', nuevo_link);
        });

        $("input[type=radio][name=chk_despues]").change(function () {
            if (this.value == 'despues_etapa' && $(this).is(':checked')) {
                $("#vencimiento_a_partir_de_variable").val("");
            }
        });

    });
    function LimpiaCheckbox() {
        if ($(this).not(':checked'))
        {
            $('.pasoRegla, .input-medium, .input-mini, .input-small').prop("disabled", false);
        }
        return true;
    }
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
                <li><a href="#tab9">Validaciones</a></li>
                <li><a href="#tab6">Vencimiento</a></li>
                <li><a href="#tab8">Trazabilidad</a></li>
                <li><a href="#tab7">Otros</a></li>
                <li><a href="#tab10">Datos</a></li>
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
                            <label class="checkbox" for="TAutomatica"><input id="TAutomatica" name="automatica" value="1" type="checkbox" <?= $tarea->automatica ? 'checked' : '' ?>> Tarea en espera de señal</label>
                        </div>
                        <div class="span6">
                            <script>
                                $(document).ready(function () {
                                    $("input[name=activacion]").change(function () {
                                        if ($("input[name=activacion]:checked").val() == 'entre_fechas')
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

                                    $('#fechaFinal').change(function () {
                                        var fechaInicial = $("#fechaInicial").datepicker('getDate');
                                        var fechaFinal = $("#fechaFinal").datepicker('getDate');

                                        if (fechaInicial > fechaFinal) {
                                            $("#fecha_inicio").addClass("error");
                                            $("#fecha_final").addClass("error");
                                            document.getElementById("save").disabled = true;
                                            var contenedor = document.getElementById("mensaje");
                                            contenedor.style.display = "block";
                                            return true;
                                        } else {
                                            $("#fecha_inicio").removeClass("error");
                                            $("#fecha_final").removeClass("error");
                                            document.getElementById("save").disabled = false;
                                            var contenedor = document.getElementById("mensaje");
                                            contenedor.style.display = "none";
                                            return true;
                                        }
                                    });

                                });
                            </script>
                            <label class="radio" for="Tactiva"><input id="Tactiva" name="activacion" value="si" type="radio" <?= $tarea->activacion == 'si' ? 'checked' : '' ?>>Tarea activada</label>
                            <label class="radio" for="TentreFechas"><input id="TentreFechas" name="activacion" value="entre_fechas" type="radio" <?= $tarea->activacion == 'entre_fechas' ? 'checked' : '' ?>>Tarea activa entre fechas</label>
                            <div id="activacionEntreFechas" class="hide form-horizontal form-horizontal-fino" style="margin-left: 20px;">
                                <div class="control-group" id="fecha_inicio">
                                    <label class="control-label" for="fechaInicial">Fecha inicial</label>
                                    <div class="controls">
                                        <input class="datepicker_" id="fechaInicial" rel="tooltip" title="Deje el campo en blanco para no considerar una fecha inicial" type="text" name="activacion_inicio" value="<?= $tarea->activacion_inicio ? date('d-m-Y', $tarea->activacion_inicio) : '' ?>" placeholder="DD-MM-AAAA" />
                                        <label class="mensaje_error_campo" id="mensaje" style="display:none;">La fecha inicial no puede ser mayor que la final</label>
                                    </div>
                                </div>
                                <div class="control-group" id="fecha_final">
                                    <label class="control-label" for="fechaFinal">Fecha final</label>
                                    <div class="controls">
                                        <input class="datepicker_" id="fechaFinal" rel="tooltip" title="Deje el campo en blanco para no considerar una fecha final" type="text" name="activacion_fin" value="<?= $tarea->activacion_fin ? date('d-m-Y', $tarea->activacion_fin) : '' ?>" placeholder="DD-MM-AAAA" />
                                    </div>
                                </div>
                            </div>
                            <label class="radio" for="Tdesactivada"><input id="Tdesactivada" name="activacion" value="no" type="radio" <?= $tarea->activacion == 'no' ? 'checked' : '' ?>>Tarea desactivada</label>
                        </div>
                    </div>
                    <label for="previsualizacion"><strong>Información para previsualización</strong></label>
                    <textarea class="span12" rows="5" id="previsualizacion" name="previsualizacion"><?= $tarea->previsualizacion ?></textarea>
                    <div class="help-block">Información que aparecerá en la bandeja de entrada al pasar el cursor por encima.</div>
                </div>
                <div class="tab-pane" id="tab2">
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $("input[name=asignacion]").click(function () {
                                if (this.value == "usuario")
                                    $("#optionalAsignacionUsuario").removeClass("hide");
                                else
                                    $("#optionalAsignacionUsuario").addClass("hide");
                            });

                            $('#notificarCorreo').click(function () {
                                if ($(this).is(":not(:checked)")) {
                                    $('#asignacion_notificar_mensaje').hide().addClass('hidden');
                                }
                                else {
                                    $('#asignacion_notificar_mensaje').show().removeClass('hidden');
                                }
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
                    <textarea id="asignacion_notificar_mensaje" name="asignacion_notificar_mensaje" class="input-xxlarge <?= $tarea->asignacion_notificar ? '' : 'hidden' ?>" placeholder="Mensaje a enviar (opcional)."><?= $tarea->asignacion_notificar_mensaje ?></textarea>
                </div>
                <div class="tab-pane" id="tab3">
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $("input[name=acceso_modo]").change(function () {
                                if (this.value == "grupos_usuarios")
                                    $("#optionalGruposUsuarios").removeClass("hide");
                                else
                                    $("#optionalGruposUsuarios").addClass("hide");
                            });
                        });
                    </script>
                    <label class='radio' for="cualquierPersona"><input id="cualquierPersona" type="radio" name="acceso_modo" value="publico" <?= $tarea->acceso_modo == 'publico' ? 'checked' : '' ?> /> Cualquier persona puede acceder.</label>
                    <label class='radio' for="soloRegistrados"><input id="soloRegistrados" type="radio" name="acceso_modo" value="registrados" <?= $tarea->acceso_modo == 'registrados' ? 'checked' : '' ?> /> Sólo los usuarios registrados.</label>
                    <label class="radio" for="confianza_id">Nivel de Confianza
                        <select class="tipo" id="confianza_id" name="nivel_confianza">
                            <option <?= $tarea->nivel_confianza == NIVEL_CONFIANZA_AG ? 'selected' : '' ?> value='<?= NIVEL_CONFIANZA_AG ?>'>Autogestionado</option>
                            <option <?= $tarea->nivel_confianza == NIVEL_CONFIANZA_VP ? 'selected' : '' ?> value='<?= NIVEL_CONFIANZA_VP ?>'>Verificado Presencial</option>
                            <option <?= $tarea->nivel_confianza == NIVEL_CONFIANZA_VCI ? 'selected' : '' ?> value='<?= NIVEL_CONFIANZA_VCI ?>'>Verificado Firma Electrónica</option>
                            <option <?= $tarea->nivel_confianza == NIVEL_CONFIANZA_CI ? 'selected' : '' ?> value='<?= NIVEL_CONFIANZA_CI ?>'>Cédula Identidad</option>
                        </select>
                    </label>
                    <!--<label class='radio' for="soloClaveunica"><input id="soloClaveunica" type="radio" name="acceso_modo" value="claveunica" <?= $tarea->acceso_modo == 'claveunica' ? 'checked' : '' ?> /> Sólo los usuarios registrados con ClaveUnica.</label>-->
                    <label class='radio' for="soloGrupo"><input id="soloGrupo" type="radio" name="acceso_modo" value="grupos_usuarios" <?= $tarea->acceso_modo == 'grupos_usuarios' ? 'checked' : '' ?> /> Sólo los siguientes grupos de usuarios pueden acceder.</label>
                    <div id="optionalGruposUsuarios" style="height: 300px;" class="<?= $tarea->acceso_modo == 'grupos_usuarios' ? '' : 'hide' ?>">
                        <label class="hidden-accessible" for="selectGruposUsuarios">Grupos de usuarios</label>
                        <select id="selectGruposUsuarios" class="input-xlarge" name="grupos_usuarios[]" multiple>
                            <?php foreach ($tarea->Proceso->Cuenta->GruposUsuarios as $g): ?>
                                <option value="<?= $g->id ?>" <?= in_array($g->id, explode(',', $tarea->grupos_usuarios)) ? 'selected' : '' ?>><?= $g->nombre ?></option>
                            <?php endforeach ?>
                            <?php foreach (explode(',', $tarea->grupos_usuarios) as $g): ?>
                                <?php if (!is_numeric($g)): ?>
                                    <option selected><?= $g ?></option>
                                <?php endif ?>
                            <?php endforeach ?>
                        </select>
                        <div class='help-block'>Puede incluir variables usando @@. Las variables deben contener el numero id del grupo de usuarios.</div>
                    </div>
                </div>
                <div class="tab-pasos tab-pane" id="tab4">

                    <table class="table table_condensed">
                        <caption class="hide-text">Pasos</caption>
                        <thead>
                            <tr class="form-agregar-paso">
                                <td></td>
                                <td>
                                    <label class="hidden-accessible" for="formulario">formulario</label>
                                    <select class="pasoFormulario input-medium" id="formulario">
                                        <?php foreach ($formularios as $f): ?>
                                            <option value="<?= $f->id ?>"><?= $f->nombre ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td></td>
                                <td>
                                    <label class="hidden-accessible" for="nombre_paso">Título del paso</label>
                                    <input type="text" class="pasoNombre input-medium" value="" name="nombre_paso" id="nombre_paso" placeholder="Título del paso" />
                                </td>
                                <td>
                                    <label class="hidden-accessible" for="regla">Condición</label>
                                    <input class="pasoRegla input-medium" type="text" id="regla" placeholder="Escribir regla condición aquí" />
                                </td>
                                <td>
                                    <label class="hidden-accessible" for="modo">Modo</label>
                                    <select class="pasoModo input-small" id="modo">
                                        <option value="edicion">Edición</option>
                                        <option value="visualizacion">Visualización</option>
                                    </select>
                                </td>

                                <td>
                                    <label class="hidden-accessible" for="generar_pdf">Generar PDF</label>
                                    <select class="pasoGenerar_pdf input-mini" id="generar_pdf" title="Generar PDF">
                                        <option value="1">Si</option>
                                        <option value="0" selected>No</option>
                                    </select>
                                </td>

                                <td>
                                    <label class="hidden-accessible" for="enviar_traza">Traza</label>
                                    <select class="pasoEnviar_traza input-mini" id="enviar_traza" title="Enviar traza">
                                        <option value="1">Si</option>
                                        <option value="0" selected>No</option>
                                    </select>
                                </td>
                                
                                <td>
                                    <label class="hidden-accessible" for="enviar_traza">Traza</label>
                                    <select class="pasoEtiqueta_traza input-medium" id="paso_etiqueta_traza" title="Etiqueta">
                                        <option value=""></option>
                                         <?php foreach ($etiquetas as $etiqueta) { ?>
                                            <option value="<?php echo $etiqueta->etiqueta ?>"> <?php echo $etiqueta->etiqueta ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                
                                <td>
                                    <label class="hidden-accessible" for="enviar_traza">Traza</label>
                                    <select class="pasoVisible_traza input-medium" id="paso_visible_traza" title="Visibilidad">
                                        <option value="VISIBLE">Visible</option>
                                        <option value="USO_INTERNO">Uso interno</option>
                                    </select>
                                </td>

                                <td>
                                    <button type="button" class="btn" title="Agregar"><span class="icon-plus"></span></button>
                                </td>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>Formulario</th>
                                <th></th>
                                <th>Título del paso</th>
                                <th>Condición</th>
                                <th>Modo</th>
                                <th>PDF</th>
                                <th>Traza</th>
                                <th>Etiqueta</th>
                                <th>Visibilidad</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tarea->Pasos as $key => $p): ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>

                                    <td>
                                        <select class="input-medium" name="pasos[<?= $key + 1 ?>][formulario_id]" id="select" disabled>
                                            <?php foreach ($formularios as $f): ?>
                                                <?php if ($p->formulario_id == $f->id): ?>
                                                    <option value="<?= $f->id ?>" selected><?= $f->nombre ?></option>
                                                <?php else: ?>
                                                    <option value="<?= $f->id ?>"><?= $f->nombre ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <a title="Editar formulario <?= $p->Formulario->nombre ?>" target="_blank" href="<?= site_url('backend/formularios/editar/' . $p->Formulario->id) ?>" id="pasos_<?= $key + 1 ?>__formulario_id_"><span class="icon-file-text-alt"></span></a>
                                    </td>

                                    <td><input type="text" name="pasos[<?= $key + 1 ?>][nombre]" value="<?= $p->nombre ?>" class="pasoNombre input-medium" disabled/></td>

                                    <td><input type="text" name="pasos[<?= $key + 1 ?>][regla]" value="<?= $p->regla ?>" class="pasoRegla" disabled/></td>

                                    <td>
                                        <select class="pasoModo input-small" name="pasos[<?= $key + 1 ?>][modo]" disabled>

                                            <?php if ($p->modo === 'visualizacion'): ?>
                                                <option value="visualizacion" selected>Visualización</option>
                                                <option value="edicion">Edición</option>
                                            <?php else: ?>
                                                <option value="edicion" selected>Edición</option>
                                                <option value="visualizacion">Visualización</option>
                                            <?php endif; ?>
                                        </select>
                                    </td>

                                    <td>
                                        <select class="pasoModo input-mini" name="pasos[<?= $key + 1 ?>][generar_pdf]" title="Generar PDF" disabled>
                                            <?php if ($p->generar_pdf): ?>
                                                <option value="1" selected>Si</option>
                                                <option value="0">No</option>
                                            <?php else: ?>
                                                <option value="0" selected>No</option>
                                                <option value="1">Si</option>
                                            <?php endif; ?>
                                        </select>
                                    </td>

                                    <td>
                                        <select class="pasoModo input-mini" name="pasos[<?= $key + 1 ?>][enviar_traza]" title="Enviar traza" disabled>
                                            <?php if ($p->enviar_traza): ?>
                                                <option value="1" selected>Si</option>
                                                <option value="0">No</option>
                                            <?php else: ?>
                                                <option value="0" selected>No</option>
                                                <option value="1">Si</option>
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                    
                                    <td>
                                        <select class="pasoEtiqueta_traza input-medium" name="pasos[<?= $key + 1 ?>][etiqueta_traza]" title="Etiqueta" disabled>
                                            <option value=""></option>
                                            <?php foreach ($etiquetas as $etiqueta) { ?>
                                            <option value="<?php echo $etiqueta->etiqueta ?>" <?php echo $etiqueta->etiqueta==$p->etiqueta_traza ? "selected":"" ?>> <?php echo $etiqueta->etiqueta ?></option>
                                        <?php } ?>
                                        </select>
                                    </td>
                                    
                                    <td>
                                        <select class="pasoVisible_traza input-medium" name="pasos[<?= $key + 1 ?>][visible_traza]" title="Visibilidad" disabled>
                                        <option value="VISIBLE" <?php echo "VISIBLE"==$p->visible_traza ? "selected":"" ?>>Visible</option>
                                        <option value="USO_INTERNO" <?php echo "USO_INTERNO"==$p->visible_traza ? "selected":"" ?>>Uso interno</option>
                                        </select>
                                    </td>

                                    <td>
                                        <input type="hidden" name="pasos[<?= $key + 1 ?>][id]" value="<?= $p->id ?>" />
                                        <a class="delete" title="Eliminar paso" href="#"><span class="icon-trash"></span></a>
                                    </td>
                                    <td>
                                        <input type="checkbox" id="check-<?= $key + 1 ?>" class="habilitar" hidden/>
                                        <label for="check-<?= $key + 1 ?>"><span class="icon-edit" title="Habilitar/Deshabilitar paso" style="color:#174f82;"></span></label>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <label class="checkbox" for="incluirUltimoPaso"><input type="checkbox" id="incluirUltimoPaso" name="paso_confirmacion" value="1" <?= $tarea->paso_confirmacion ? 'checked' : '' ?> > Incluir último paso de confirmación antes de avanzar la tarea.</label>
                </div>
                <div class="tab-eventos tab-pane" id="tab5">
                    <table class="table table_condensed">
                        <caption class="hide-text">Eventos</caption>
                        <thead>
                            <tr class="form-agregar-evento">
                                <td></td>
                                <td>
                                    <label class="hidden-accessible" for="accion">Acción</label>
                                    <select class="eventoAccion input-small" id="accion">
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
                                            <option value="<?= $p->id ?>" title="<?= $p->Formulario->nombre ?>">Ejecutar Paso <?= $p->orden ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>

                                <td>
                                    <label class="hidden-accessible" for="instanciar_api">Instanciar API</label>
                                    <select class="eventoInstanciarApi input-mini" id="instanciar_api">
                                        <option value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                </td>

                                <td>
                                    <label class="hidden-accessible" for="traza">Traza</label>
                                    <select class="eventoTraza input-mini" id="traza">
                                        <option value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                </td>

                                <td>
                                    <label class="hidden-accessible" for="tipo_registro_traza">Tipo de registro traza</label>
                                    <select class="eventoTipoRegistroTraza input-small" id="tipo_registro_traza">
                                        <?php $id_tipo_registro_posibles_traza = unserialize(ID_TIPO_REGISTRO_POSIBLES_TRAZABILIDAD); ?>

                                        <?php foreach ($id_tipo_registro_posibles_traza as $estado_k => $estado_v) { ?>
                                            <option value="<?php echo $estado_k ?>" <?= $estado_k == 3 ? 'selected' : '' ?> > <?php echo $estado_v ?></option>
                                        <?php } ?>
                                    </select>
                                </td>

                                <td>
                                    <label class="hidden-accessible" for="descripcion_traza">Descripción de traza</label>
                                    <input class="eventoDescripcionTraza input-medium" id="descripcion_traza" type="text" placeholder="Descripción de traza" />
                                </td>
                                
                                <td>
                                    <label class="hidden-accessible" for="evento_etiqueta_traza">Etiqueta</label>
                                <select class ="eventoEtiquetaTraza input-medium" id="evento_etiqueta_traza" name="evento_etiqueta_traza">
                                    <option value=""></option>
                                        <?php foreach ($etiquetas as $etiqueta) { ?>
                                            <option value="<?php echo $etiqueta->etiqueta ?>"> <?php echo $etiqueta->etiqueta ?></option>
                                        <?php } ?>
                                </select>    
                                </td>
                                
                                <td>
                                    <label class="hidden-accessible" for="evento_visible_traza">Visibilidad</label>
                                   <select class ="eventoVisibleTraza input-medium" id="evento_visible_traza" name="evento_visible_traza">
                                        <option value="VISIBLE" >Visible</option>
                                        <option value="USO_INTERNO">Uso interno</option>
                                    </select>
                                </td>

                                <td>
                                    <label class="hidden-accessible" for="descripcion_error_soap">Descripción error (Consultar Servicio)</label>
                                    <input class="eventoDescripcionErrorSoap input-medium" id="descripcion_error_soap" type="text" placeholder="Descripción error" />
                                </td>

                                <td>
                                    <label class="hidden-accessible" for="variable_error_soap">Variable error (Consultar Servicio)</label>
                                    <input class="eventoVariableErrorSoap input-medium" id="variable_error_soap" type="text" placeholder="Variable error" />
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
                                <th>Instanciar API</th>
                                <th>Traza</th>
                                <th>Tipo de registro traza</th>
                                <th>Descripción de traza</th>
                                <th>Etiqueta</th>
                                <th>Visibilidad</th>
                                <th>Descripción error (Consultar Servicio)</th>
                                <th>Variable error (Consultar Servicio)</th>
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
                                    <td><?= $p->paso_id ? '<abbr title="' . $p->Paso->Formulario->nombre . '">Ejecutar Paso ' . $p->Paso->orden . '</abbr>' : 'Ejecutar Tarea' ?></td>
                                    <td><?= $p->instanciar_api ? 'Si' : 'No' ?></td>
                                    <td><?= $p->traza ? 'Si' : 'No' ?></td>
                                    <td>
                                        <?php $id_tipo_registro_posibles_traza = unserialize(ID_TIPO_REGISTRO_POSIBLES_TRAZABILIDAD); ?>
                                        <?php foreach ($id_tipo_registro_posibles_traza as $estado_k => $estado_v) { ?>
                                            <?php
                                            if ($estado_k == $p->tipo_registro_traza) {
                                                echo $estado_v;
                                                break;
                                                ?>
                                            <?php } ?>
    <?php } ?>
                                    </td>
                                    <td><?= $p->descripcion_traza ?></td>
                                    <td><?= $p->etiqueta_traza ?></td>
                                    <td><?= $p->visible_traza ?></td>
                                    <td><?= $p->descripcion_error_soap ?></td>
                                    <td><?= $p->variable_error_soap ?></td>
                                    <td>
                                        <input type="hidden" name="eventos[<?= $key + 1 ?>][accion_id]" value="<?= $p->accion_id ?>" />
                                        <input type="hidden" name="eventos[<?= $key + 1 ?>][regla]" value="<?= $p->regla ?>" />
                                        <input type="hidden" name="eventos[<?= $key + 1 ?>][instante]" value="<?= $p->instante ?>" />
                                        <input type="hidden" name="eventos[<?= $key + 1 ?>][paso_id]" value="<?= $p->paso_id ?>" />
                                        <input type="hidden" name="eventos[<?= $key + 1 ?>][instanciar_api]" value="<?= $p->instanciar_api ?>" />
                                        <input type="hidden" name="eventos[<?= $key + 1 ?>][traza]" value="<?= $p->traza ?>" />
                                        <input type="hidden" name="eventos[<?= $key + 1 ?>][tipo_registro_traza]" value="<?= $p->tipo_registro_traza ?>" />
                                        <input type="hidden" name="eventos[<?= $key + 1 ?>][descripcion_traza]" value="<?= $p->descripcion_traza ?>" />
                                        <input type="hidden" name="eventos[<?= $key + 1 ?>][etiqueta_traza]" value="<?= $p->etiqueta_traza ?>" />
                                        <input type="hidden" name="eventos[<?= $key + 1 ?>][visible_traza]" value="<?= $p->visible_traza ?>" />
                                        <input type="hidden" name="eventos[<?= $key + 1 ?>][descripcion_error_soap]" value="<?= $p->descripcion_error_soap ?>" />
                                        <input type="hidden" name="eventos[<?= $key + 1 ?>][variable_error_soap]" value="<?= $p->variable_error_soap ?>" />
                                        <a class="delete" title="Eliminar evento" href="#"><span class="icon-trash"></span></a>
                                    </td>
                                </tr>
<?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="tab-validaciones tab-pane" id="tab9">
                    <table class="table table_condensed">
                        <caption class="hide-text">Validaciones</caption>
                        <thead>
                            <tr class="form-agregar-validaciones">
                                <td></td>
                                <td>
                                    <label class="hidden-accessible" for="validacion">Validación</label>
                                    <select class="validacionAccion input-small" id="accion">
                                        <?php foreach ($validaciones as $f): ?>
                                            <option value="<?= $f->id ?>"><?= $f->nombre ?></option>
<?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <label class="hidden-accessible" for="condicion">Condición</label>
                                    <input class="validacionRegla input-medium" id="condicion" type="text" placeholder="Escribir regla condición" />
                                </td>
                                <td>
                                    <label class="hidden-accessible" for="instante">Instante</label>
                                    <select class="validacionInstante input-small" id="instante">
                                        <option value="antes">Antes</option>
                                        <option value="despues">Después</option>
                                    </select>
                                </td>
                                <td>
                                    <label class="hidden-accessible" for="momento">Momento</label>
                                    <select class="validacionPasoId input-medium" id="momento">
                                        <?php foreach ($tarea->Pasos as $p): ?>
                                            <option value="<?= $p->id ?>" title="<?= $p->Formulario->nombre ?>">Ejecutar Paso <?= $p->orden ?></option>
<?php endforeach ?>
                                    </select>
                                </td>

                                <td>
                                    <button type="button" class="btn" title="Agregar"><span class="icon-plus"></span></button>
                                </td>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>Validación</th>
                                <th>Condición</th>
                                <th>Instante</th>
                                <th>Momento</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
<?php foreach ($tarea->Validaciones as $key => $p): ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td><a title="Editar" target="_blank" href="<?= site_url('backend/validaciones/editar/' . $p->Validacion->id) ?>"><?= $p->Validacion->nombre ?></a></td>
                                    <td><?= $p->regla ?></td>
                                    <td><?= $p->instante ?></td>
                                    <td><?= $p->paso_id ? '<abbr title="' . $p->Paso->Formulario->nombre . '">Ejecutar Paso ' . $p->Paso->orden . '</abbr>' : 'Ejecutar Tarea' ?></td>
                                    <td>
                                        <input type="hidden" name="validaciones[<?= $key + 1 ?>][validacion_id]" value="<?= $p->validacion_id ?>" />
                                        <input type="hidden" name="validaciones[<?= $key + 1 ?>][regla]" value="<?= $p->regla ?>" />
                                        <input type="hidden" name="validaciones[<?= $key + 1 ?>][instante]" value="<?= $p->instante ?>" />
                                        <input type="hidden" name="validaciones[<?= $key + 1 ?>][paso_id]" value="<?= $p->paso_id ?>" />
                                        <a class="delete" title="Eliminar validación" href="#"><span class="icon-trash"></span></a>
                                    </td>
                                </tr>
<?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="tab6">
                    <script>
                        $(document).ready(function () {
                            $("input[name=vencimiento]").change(function () {
                                if (this.checked)
                                    $("#vencimientoConfig").show();
                                else
                                    $("#vencimientoConfig").hide();
                            }).change();

                            $("select[name=vencimiento_unidad]").change(function () {
                                if (this.value == "D")
                                    $("#habilesConfig").show();
                                else
                                    $("#habilesConfig").hide();
                            }).change();
                        });
                    </script>
                    <label class="checkbox" for="vencimiento"><input type="checkbox" id="vencimiento" name="vencimiento" value="1" <?= $tarea->vencimiento ? 'checked' : '' ?> /> Activar vencimiento</label>
                    <div id="vencimientoConfig" class="hide">

                        <div class="form-inline">
                            La etapa se vencerá
                            <label class="hidden-accessible" for="vencimiento_valor">Cantidad</label>
                            <input type="text" name="vencimiento_valor" id="vencimiento_valor" class="input-mini" value="<?= $tarea->vencimiento_valor ? $tarea->vencimiento_valor : 5 ?>" />
                            <label class="hidden-accessible" for="vencimiento_unidad">Unidad de medida</label>
                            <select name="vencimiento_unidad" class="input-small" id="vencimiento_unidad">
                                <option value="D" <?= $tarea->vencimiento_unidad == 'D' ? 'selected' : '' ?>>días</option>
                                <option value="W" <?= $tarea->vencimiento_unidad == 'W' ? 'selected' : '' ?>>meses</option>
                                <option value="M" <?= $tarea->vencimiento_unidad == 'M' ? 'selected' : '' ?>>años</option>
                            </select>
                            <div class="radio-etapas">
                                <label class="checkbox" for="chk_despues_etapa"><input type="radio" id="chk_despues_etapa" name="chk_despues" value="despues_etapa" <?= $tarea->vencimiento_a_partir_de_variable ? '' : 'checked' ?> /> después de completada la etapa anterior<span style="color: gray;"> * Si es una etapa inicial se toma en cuenta la fecha de creación del trámite</span></label>
                                <label class="checkbox" for="chk_despues_fecha"><input type="radio" id="chk_despues_fecha" name="chk_despues" value="despues_fecha" <?= $tarea->vencimiento_a_partir_de_variable ? 'checked' : '' ?> />
                                    después de la fecha <input class="input-large" type="text" name="vencimiento_a_partir_de_variable" id="vencimiento_a_partir_de_variable" class="input-medium"  placeholder="día-mes-año / @@fecha" value="<?= $tarea->vencimiento_a_partir_de_variable ? $tarea->vencimiento_a_partir_de_variable : '' ?>" style="display:inline"/>
                                </label>
                            </div>
                        </div>
                        <label id='habilesConfig' class='checkbox' for="habilesConsid" style="margin-top: -9px;"><input id="habilesConsid" type='checkbox' name='vencimiento_habiles' value='1' <?= $tarea->vencimiento_habiles ? 'checked' : '' ?> /> Considerar solo días hábiles.</label>
                        <hr>
                        <label id='escaladoConfig' class='checkbox' for="escaladoConsid"><input id="escaladoConsid" type='checkbox' name='escalado_automatico' value='1' <?= $tarea->escalado_automatico ? 'checked' : '' ?> /> Activar escalalado automático al vencer la etapa </label>
                        <p style="color: gray;"> * Al vencer la etapa se avanza a la siguiente. Si es una etapa final o se cumplen ciertas condiciones el escalado puede finalizar el trámite </p>
                        <hr>
                        <div class="form-inline">
                            <label class="checkbox" for="notificar"><input type="checkbox" id="notificar" name="vencimiento_notificar" value="1" <?= $tarea->vencimiento_notificar ? 'checked' : '' ?> /> Notificar</label>
                            <label for="vencimiento_notificar_dias">cuando quede</label>
                            <input class="input-mini" type="text" name="vencimiento_notificar_dias" id="vencimiento_notificar_dias" value="<?= $tarea->vencimiento_notificar_dias ?>" /> día/s para vencer
                        </div>
                        <br>
                        <label class="checkbox" for="notificar_vencida"><input type="checkbox" id="notificar_vencida" name="notificar_vencida" value="1" <?= $tarea->notificar_vencida ? 'checked' : '' ?> /> Notificar cuando la etapa haya vencido</label>
                        <br>
                        <label for="vencimiento_notificar_email">Correo de notificaciones <input class="input-large" style="margin-bottom: 3px" type="text" name="vencimiento_notificar_email" id="vencimiento_notificar_email" placeholder="ejemplo@dominio.com / @@email" value="<?= $tarea->vencimiento_notificar_email ?>" /></label>

                    </div>
                </div>
                <div class="tab-pane" id="tab7">
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $("input[name=almacenar_usuario]").click(function () {
                                if (this.checked)
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

                    <label for="nombre"><strong>Texto Paso final Pendiente</strong></label>
                    <input class="span12" id="paso_final_pendiente" name="paso_final_pendiente" type="text" value="<?= $tarea->paso_final_pendiente ?>" />
                    <br/>
                    <label for="nombre"><strong>Texto Paso final StandBy</strong></label>
                    <input class="span12" id="paso_final_standby" name="paso_final_standby" type="text" value="<?= $tarea->paso_final_standby ?>" />
                    <br/>
                    <label for="nombre"><strong>Texto Paso final Compĺetado</strong></label>
                    <input class="span12" id="paso_final_completado" name="paso_final_completado" type="text" value="<?= $tarea->paso_final_completado ?>" />
                    <br/>
                    <label for="nombre"><strong>Texto Paso final Sin Continuación</strong></label>
                    <input class="span12" id="paso_final_sincontinuacion" name="paso_final_sincontinuacion" type="text" value="<?= $tarea->paso_final_sincontinuacion ?>" />
                    <br/>
                    <label for="texto_boton_paso_final"><strong>Texto Botón Paso Final</strong></label>
                    <input class="span12" id="texto_boton_paso_final" name="texto_boton_paso_final" type="text" value="<?= $tarea->texto_boton_paso_final ?>" />
                    <br/>
                    <label for="texto_boton_generar_pdf"><strong>Texto Botón Generar PDF</strong></label>
                    <input class="span12" id="texto_boton_generar_pdf" name="texto_boton_generar_pdf" type="text" value="<?= $tarea->texto_boton_generar_pdf ?>" />

                </div>
                <div class="tab-pane" id="tab8">

                    <div class="row-fluid">
                        <div class="span4">
                            <label class="checkbox" for="trazabilidad"><input type="checkbox" id="trazabilidad" name="trazabilidad" value="1" <?= ($tarea->trazabilidad ? 'checked' : '') ?> /> Activar trazabilidad</label>
                         </div>
                        <div class="span4">
                            <div id="trazabilidad_nombre_oficina_box" class="<?= ($tarea->trazabilidad ? '' : 'hidden') ?>">
                                <label for="trazabilidad_nombre_oficina">Nombre de oficina</label>
                                <input type="text" id="trazabilidad_nombre_oficina" name="trazabilidad_nombre_oficina" autocomplete="on" value="<?= $tarea->trazabilidad_nombre_oficina ?>" />
                            </div>
                            </div>
                        <div class="span4">
                            <div id="trazabilidad_id_oficina_box" class="<?= ($tarea->trazabilidad ? '' : 'hidden') ?>">
                                <label for="trazabilidad_id_oficina">ID de oficina</label>
                                <input type="text" id="trazabilidad_id_oficina" name="trazabilidad_id_oficina" autocomplete="on" value="<?= $tarea->trazabilidad_id_oficina ?>" />
                            </div>
                            </div>                           
                        
                    </div>

                    <div class="row-fluid">
                        <div class="span4">                             
                        </div>

                        <div class="span4">
                            <div id="div_trazabilidad_cabezal" class="<?= ($tarea->trazabilidad && $tarea->inicial ? '' : 'hidden') ?>">
                                <label for="trazabilidad_cabezal">Traza cabezal</label>
                                <select id="trazabilidad_cabezal" name="trazabilidad_cabezal">
                                    <?php $estados_posibles_cabezal = unserialize(ID_ESTADOS_POSIBLES_CABEZAL_TRAZABILIDAD); ?>

                                    <?php foreach ($estados_posibles_cabezal as $estado_k => $estado_v) { ?>
                                        <option value="<?php echo $estado_k ?>"> <?php echo $estado_v ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="span4">
                                <div id="div_trazabilidad_visible" class="<?= ($tarea->trazabilidad ? '' : 'hidden') ?>">
                                    <label for="trazabilidad_visible">Visibilidad de traza</label>
                                    <select id="trazabilidad_visible" name="trazabilidad_visible">
                                        <option value="VISIBLE" <?php echo "VISIBLE"==$tarea->visible_traza ? "selected":"" ?>>Visible</option>
                                        <option value="USO_INTERNO" <?php echo "USO_INTERNO"==$tarea->visible_traza ? "selected":"" ?>>Uso interno</option>
                                    </select>
                                </div>
                       </div>
                    </div>

                    <div class="row-fluid">
                        <div class="span4">                           
                        </div>

                        <div class="span4">
                            <div id="div_trazabilidad_estado" class="<?= ($tarea->trazabilidad && $tarea->final ? '' : 'hidden') ?>">
                                <label for="trazabilidad_estado">Traza línea final</label>
                                    <?php $estados_posibles = unserialize(ID_ESTADOS_POSIBLES_TRAZABILIDAD); ?>
                                <select id="trazabilidad_estado" name="trazabilidad_estado">
                                    <?php foreach ($estados_posibles as $estado_k => $estado_v) { ?>
                                        <option value="<?= $estado_k ?>" <?= ($tarea->trazabilidad_estado == $estado_k ? 'selected' : '') ?>><?= $estado_v ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="span4">
                                <div id="div_trazabilidad_etiqueta" class="<?= ($tarea->trazabilidad? '' : 'hidden') ?>">
                                    <label for="trazabilidad_etiqueta">Etiqueta de traza</label>
                                    <select id="trazabilidad_etiqueta" name="trazabilidad_etiqueta">
                                        <option value=""></option>
                                        <?php foreach ($etiquetas as $etiqueta) { ?>
                                            <option value="<?php echo $etiqueta->etiqueta ?>" <?php echo $etiqueta->etiqueta==$tarea->etiqueta_traza ? "selected":"" ?>> <?php echo $etiqueta->etiqueta ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="tab-datos-expuestos tab-pane" id="tab10">
                    <script>
                        function selectAll() {
                            $("#seleccionados").find("*").prop("selected", true);
                        }

                        function SelectAllFunction() {
                            if ($("input[name=SelectAll]:checked").val()) {
                                $(".SelectAll").prop('checked', true);
                            } else {
                                $(".SelectAll").prop('checked', false);
                            }
                        }

                        function seleccionarForm(id) {
                            if ($("input[name=" + id + "]:checked").val()) {
                                $("." + id).prop('checked', true);
                            } else {
                                $("." + id).prop('checked', false);
                            }
                        }
                    </script>

                    <div class="row-fluid">
                        <div class="span6"><h5>Variables de formulario
                                <a href="/assets/ayuda/simple/backend/modelamiento-del-proceso/disenador.html#pestana_datos" target="_blank">
                                    <span class="glyphicon glyphicon-info-sign"></span>
                                </a></h5></div>
                        <div class="span6"><h5>Variables de proceso</h5></div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6" style="overflow-y: auto; height:280px;width:48%;border:.5px solid;border-radius: 5px;border-color:#DDDDDD;">
                            <?php
                            $formularios = array();
                            $nameform = array();
                            foreach ($variablesFormularios as $key => $valuesAry) {
                                $var = $valuesAry['nombre_formulario'];
                                if (!in_array($var, $nameform)) {
                                    $nameform[] = $var;
                                }
                                $formIndex = array_search($var, $nameform);
                                $formularios[$formIndex][] = $valuesAry;
                            }

                            foreach ($formularios as $key => $res) {
                                $id = $key;
                                ?>
                                &nbsp;<input type="checkbox" onclick="seleccionarForm(<?php echo $id; ?>)" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $id; ?>"/>&nbsp;<b><?php echo $res[0]['nombre_formulario']; ?></b><br><?php
                                foreach ($res as $d) {
                                    ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="checkbox" class="<?php echo $id ?>" name="varForm[]" id="<?php echo $d['variable_id'] ?>" value="<?php echo $d['variable_id'] ?>" />&nbsp;<?php echo $d['nom_variables']; ?><br>
                                    <?php
                                    if ($d['exponer_campo'] == 1) {
                                        echo ' <script type="text/javascript">';

                                        echo '$("#"+' . $d["variable_id"] . ').prop("checked",true)';
                                        echo '</script>';
                                    }
                                }
                            }
                            ?>
                        </div>
                        <div class="span6" style="overflow-y: auto; height:280px;width:47%;border: 0.5px solid;border-radius: 5px;border-color:#DDDDDD;">
                            <?php
                            $count = count($variablesProcesos);
                            if ($count > 0) {
                                ?>&nbsp;<input type="checkbox" onclick="SelectAllFunction();" name="SelectAll" value="0">&nbsp;<b>All</b><br><?php
                                foreach ($variablesProcesos as $res) {
                                    $variables = json_decode($res['extra']);
                                    $variables = get_object_vars($variables);
                                    ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" class="SelectAll" name="varPro[]" id="var<?php echo $res['variable_id'] ?>" value="<?php echo $res['variable_id'] ?>">&nbsp;<?php echo $variables['variable'];
                            echo " ";
                            //print_r($res);
                            //echo $res[3]; ?>
                                    <br>
                                        <?php if ($res['exponer_variable'] == 1) { ?>
                                        <script type="text/javascript">
                                            $("#var" +<?php echo $res['variable_id'] ?>).prop('checked', true);
                                        </script>
                                    <?php
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="<?= site_url('backend/procesos/eliminar_tarea/' . $tarea->id) ?>" class="btn btn-danger pull-left" onclick="return confirm('¿Esta seguro que desea eliminar esta tarea?')">Eliminar</a>
        <button id="save" name="save" type="submit" class="btn btn-primary" onclick="LimpiaCheckbox()">Guardar</button>
        <!--a href="#" onclick="javascript:$('#formEditarTarea').submit();return false;" class="btn btn-primary">Guardar</a-->
        <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
    </div>
</form>

<script src="<?= base_url() ?>assets/js/modelador-acciones.js" type="text/javascript"></script>
<ul class="breadcrumb">
    <li class="active">
        Reportes de Usuario
    </li>
</ul>
<h2>Reportes de Usuario</h2>

<fieldset>
    <legend>Filtros</legend>
    <form id="formAgregarFiltrosReporte" class="ajaxForm form-horizontal" method="GET" action="#">
        <div class='row-fluid'>
            <div class='span6'>
                <div class="control-group">
                    <label for="filtro_grupo" class="control-label">Grupos de usuarios</label>
                    <div class="controls">
                        <select class="filter" id="filtro_grupo" name="filtro_grupo">
                            <option value="" selected></option>
                            <?php
                            if ($grupos) {
                                if (count($grupos) > 1) {
                                    ?>
                                    <option value="">Todos</option>
                                    <?php
                                }

                                foreach ($grupos as $grupo) {
                                    ?>
                                    <option value="<?= $grupo->id ?>"><?= $grupo->nombre ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class='span6'>
                <div class="control-group">
                    <label for="filtro_usuario" class="control-label">Usuarios</label>
                    <div class="controls">
                        <select class="filter" id="filtro_usuario" name="filtro_usuario">
                            <option value="" selected></option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?= $usuario->id ?>"><?= $usuario->usuario ?> - <?= $usuario->nombres ?> <?= $usuario->apellido_paterno ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class='row-fluid'>
            <div class='span6'>                
                <div class='control-group' id="fechas">
                    <label class='control-label' for="created_at_desde">Fecha de creación</label>
                    <div class='controls'>
                        <input type='text' name='created_at_desde' id="desde" placeholder='Desde' class='datepicker input-small' value='' />
                        <div class="hidden-accessible"><label for="hasta">Fecha de creación hasta</label></div>
                        <input type='text' name='created_at_hasta' id="hasta" title="fecha de creación hasta" placeholder='Hasta' class='datepicker input-small' value='' />
                        <span class="mensaje_error_campo" id="mensaje" style="display:none;">El rango de fechas es incorrecto</span>
                    </div>
                </div>
            </div>
            <div class='span6'>
                <div class='control-group' id="fecha_update" >
                    <label class='control-label' for="fechaInicial">Fecha de último cambio</label>
                    <div class='controls'>
                        <input type='text' name='updated_at_desde' id="fechaInicial" placeholder='Desde' class='datepicker input-small' value='' />
                        <div class="hidden-accessible"><label for="fechaFinal">Fecha de último cambio hasta</label></div>
                        <input type='text' name='updated_at_hasta' id="fechaFinal" placeholder='Hasta' class='datepicker input-small' value='' />
                        <span class="mensaje_error_campo" id="mesage" style="display:none;">El rango de fechas es incorrecto</span>
                    </div>
                </div>
            </div>
        </div>
        <div class='row-fluid'>
            <div class='span6'>
                <div class='control-group'>
                    <span class='control-label'>Estado del trámite</span>
                    <div class='controls'>
                        <label class='radio' for="cualquiera"><input id="cualquiera" type='radio' checked="" name='pendiente' value='-1'> Cualquiera</label>
                        <label class='radio' for="curso"><input id="curso" type='radio' name='pendiente' value='1'> En curso</label>
                        <label class='radio' for="completado"><input id="completado" type='radio' name='pendiente' value='0'> Completado</label>
                    </div>
                </div>
            </div>
            <div class='span6'>
                <div class="busqueda_avanzada">
                    <button type="button" class="btn btn-primary" id="generar_reporte_usuario">Generar</button>
                </div>
            </div>
        </div>

    </form>
</fieldset>
<script>
    $(document).ready(function () {
        $('#hasta').change(function () {

            var desde = $("#desde").datepicker('getDate');
            var hasta = $("#hasta").datepicker('getDate');

            if (desde > hasta) {
                $("#fechas").addClass("error");
                document.getElementById("generar_reporte_usuario").disabled = true;
                document.getElementById("fechaInicial").disabled = true;
                document.getElementById("fechaFinal").disabled = true;
                document.getElementById("fechaInicial").value = "";
                document.getElementById("fechaFinal").value = "";
                $("#fecha_update").removeClass("error");
                var contenedor = document.getElementById("mensaje");
                var error = document.getElementById("mesage");
                contenedor.style.display = "block";
                error.style.display = "none";
                return true;
            } else {
                $("#fechas").removeClass("error");
                document.getElementById("generar_reporte_usuario").disabled = false;
                document.getElementById("fechaInicial").disabled = false;
                document.getElementById("fechaFinal").disabled = false;
                var contenedor = document.getElementById("mensaje");
                contenedor.style.display = "none";
                return true;
            }
        });
        $('#fechaFinal').change(function () {

            var fechaInicial = $("#fechaInicial").datepicker('getDate');
            var fechaFinal = $("#fechaFinal").datepicker('getDate');
            if (fechaInicial > fechaFinal) {

                $("#fecha_update").addClass("error");
                document.getElementById("generar_reporte_usuario").disabled = true;
                var contenedor = document.getElementById("mesage");
                contenedor.style.display = "block";
                return true;
            } else {
                $("#fecha_update").removeClass("error");
                document.getElementById("generar_reporte_usuario").disabled = false;
                var contenedor = document.getElementById("mesage");
                contenedor.style.display = "none";
                return true;
            }
        });
    });
</script>
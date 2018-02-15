<script>
    $(document).ready(function(){
        $('#fechaFinal').change(function() {
            var fechaInicial = $("#fechaInicial").datepicker('getDate');
            var fechaFinal = $("#fechaFinal").datepicker('getDate');
            if (fechaInicial > fechaFinal){
                $("#fecha_update").addClass("error");
                    document.getElementById("save").disabled = true;
                    document.getElementById("estado").disabled = true;
                    var contenedor = document.getElementById("mesage");
                        contenedor.style.display = "block";
                    return true;
            }else {
                $("#fecha_update").removeClass("error");
                    document.getElementById("save").disabled = false;
                    document.getElementById("estado").disabled = false;
                    var contenedor = document.getElementById("mesage");
                        contenedor.style.display = "none";
                    return true;
            }
        });
    });

    function toggleBusquedaAvanzada() {
        $("#busquedaAvanzada").slideToggle();
            document.getElementById("save").disabled = true;
        return false;
    }
    function habilitar() {
        document.getElementById("save").disabled = false;
    }

</script>
<ul class="breadcrumb">
    <li>
        Seguimiento de Pagos
    </li>
</ul>

<h2>Seguimiento de Pagos</h2>
<div class="row-fluid acciones-generales">
    <div class='pull-right'>
        <div class="busqueda_avanzada"><a href='#' onclick='toggleBusquedaAvanzada()'>Busqueda avanzada</a></div>
    </div>
</div>
<div id='busquedaAvanzada' style='display: <?=$busqueda_avanzada?'block':'none'?>;'>
    <form class='form-horizontal' method="get" action="<?= current_url() ?>">
        <fieldset>
            <legend>Filtros de búsqueda</legend>
            <input type='hidden' name='busqueda_avanzada' value='1' />
            <div class='row-fluid'>
                <div class='span6'>
                    <div class='control-group'>
                        <label for="estado" class="control-label">Estado</label>
                        <div class="controls">
                            <select class="form-control" id="estado" name="estado" onclick="habilitar()">
                                <?php if(isset($estado) && $estado != ''):?>
                                    <option value=""></option>
                                        <option value="">Todos</option>
                                    <?php foreach($estado as $row):?>
                                        <?php if($row->estado == $_GET['estado']):?>
                                            <option value="<?php echo $row->estado; ?>" selected><?php echo $row->estado; ?></option>
                                        <?php else: ?>
                                            <option value="<?php echo $row->estado; ?>"><?php echo $row->estado; ?></option>
                                        <?php endif;?>
                                    <?php endforeach;?>
                                <?php else: ?>
                                    <option value="" selected></option>
                                        <option value="">Todos</option>
                                    <?php foreach($estado as $row):?>
                                        <option value="<?php echo $_GET['estado']; ?>" selected><?php echo $_GET['estado']; ?></option>
                                    <?php endforeach;?>
                                <?php endif;?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class='span6'>
                    <div class='control-group' id="fecha_update" >
                        <label class='control-label' for="fechaInicial">Fecha de último cambio</label>
                        <div class='controls'>
                            <input type='text' name='updated_at_desde' id="fechaInicial" placeholder='Desde' class='datepicker input-small' value='<?= $updated_at_desde ?>'/>
                            <div class="hidden-accessible"><label for="fechaFinal">Fecha de último cambio hasta</label></div>
                            <input type='text' name='updated_at_hasta' id="fechaFinal" placeholder='Hasta' class='datepicker input-small' value='<?= $updated_at_hasta ?>' onclick="habilitar()"/>
                            <span class="mensaje_error_campo" id="mesage" style="display:none;">El rango de fechas es incorrecto</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class='busqueda_avanzada'><button type="submit" class="btn btn-primary" id="save">Buscar</button>
                <a id="limpiar_filtro" href="?filtro=1">Limpiar</a></div>
        </fieldset>
        <!-- Buscar de Busqueda Avanzada -->
    </form>
</div>
<table class="table">
  <caption class="hide-text">Seguimiento de Pagos</caption>
    <thead>
        <tr>
            <th>ID trámite interno</th>
            <th>ID trámite externo</th>
            <th>ID solicitud</th>
            <th>Pasarela</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registros as $p): ?>
        <tr>
            <td><a href="<?=site_url('backend/seguimiento/ver/'.$p->id_tramite_interno)?>"><?=$p->id_tramite_interno?></a></td>
            <td><?=$p->id_tramite?></td>
            <td><?=$p->id_solicitud?></td>
            <td><?=$p->pasarela?></td>
            <td><span class="estado badge <?= ($p->estado == 'realizado' ? 'badge-success' : ($p->estado == 'error' ? 'badge-important' : ($p->estado == 'pendiente' ? 'badge-warning' : ($p->estado == 'rechazado' ? 'badge-important' : 'badge-secondary')))) ?>"><?=$p->estado?></span></td>
            <td class="actions">
                <a class="btn btn-primary" href="<?=site_url('backend/seguimiento/ver_pago/'.$p->id)?>"><span class="icon-eye-open icon-white"></span> Ver detalle</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div id="paginado_div"><?php if(isset($this->pagination)) echo $this->pagination->create_links(); ?></div>

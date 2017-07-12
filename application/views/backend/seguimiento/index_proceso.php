<script>
    function editarVencimiento(etapaId) {
        $("#modal").load(site_url + "backend/seguimiento/ajax_editar_vencimiento/" + etapaId);
        $("#modal").modal();
        return false;
    }

    function toggleBusquedaAvanzada() {
        $("#busquedaAvanzada").slideToggle();
        return false;
    }
</script>

<ul class="breadcrumb">
    <li><a href="<?= site_url('backend/seguimiento/index') ?>">Seguimiento de Procesos</a><span class="divider">/</span></li>
    <li class="active"><?= $proceso->nombre ?></li>
</ul>
<h2><?= $proceso->nombre ?></h2>
<div class="row-fluid acciones-generales">
    <div class='pull-right'>
        <form class="form-search" method="GET" action="<?= current_url() ?>">
            <div class="input-append">
                <div class="hidden-accessible"><label for="buscar">Buscar</label></div>
                <input name="query" id="buscar" value="<?= $query ?>" type="text" class="search-query" />
                <button type="submit" class="btn">Buscar</button>
            </div>
        </form>
        <div class="busqueda_avanzada"><a href='#' onclick='toggleBusquedaAvanzada()'>Busqueda avanzada</a></div>
    </div>

    <?php if(UsuarioBackendSesion::usuario()->rol!='seguimiento'): ?>
    <div class="btn-group pull-left">
        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
            Operaciones
            <span class="caret"></span>
        </a>

        <ul class="dropdown-menu">
            <li><a href="<?= site_url('backend/seguimiento/borrar_proceso/' . $proceso->id) ?>" onclick="if (confirm('¿Esta seguro que desea eliminar todos los tramites de este proceso?'))
            return confirm('Atención. Esta operación no se podra deshacer y borrara todos los tramites en curso de este proceso. ¿Esta seguro que desea continuar?');
        else
            return false;">Borrar todo</a></li>
        </ul>
    </div>
    <?php endif ?>
</div>

<div id='busquedaAvanzada' style='display: <?=$busqueda_avanzada?'block':'none'?>;'>
  <form class='form-horizontal'>
    <fieldset>
      <legend>Filtros de búsqueda</legend>
            <input type='hidden' name='busqueda_avanzada' value='1' />
            <div class='row-fluid'>
                <div class='span6'>
                    <div class='control-group'>
                        <label class='control-label' for="termino">Término a buscar</label>
                        <div class='controls'>
                            <input name="query" value="<?= $query ?>" id="termino" type="text" />
                        </div>
                    </div>
                </div>
                <div class='span6'>
                    <div class='control-group'>
                        <span class='control-label'>Estado del trámite</span>
                        <div class='controls'>
                            <label class='radio' for="cualquiera"><input id="cualquiera" type='radio' name='pendiente' value='-1' <?= $pendiente == -1 ? 'checked' : '' ?>> Cualquiera</label>
                            <label class='radio' for="curso"><input id="curso" type='radio' name='pendiente' value='1' <?= $pendiente == 1 ? 'checked' : '' ?>> En curso</label>
                            <label class='radio' for="completado"><input id="completado" type='radio' name='pendiente' value='0' <?= $pendiente == 0 ? 'checked' : '' ?>> Completado</label>
                        </div>
                    </div>
                </div>
              </div>
              <div class='row-fluid'>
                <div class='span6'>
                    <div class='control-group'>
                        <label class='control-label' for="desde">Fecha de creación</label>
                        <div class='controls'>
                            <input type='text' name='created_at_desde' id="desde" placeholder='Desde' class='datepicker input-small' value='<?= $created_at_desde ?>' />
                            <div class="hidden-accessible"><label for="hasta">Fecha de creación hasta</label></div>
                            <input type='text' name='created_at_hasta' id="hasta" placeholder='Hasta' class='datepicker input-small' value='<?= $created_at_hasta ?>' />
                        </div>
                    </div>
                  </div>
                  <div class='span6'>
                    <div class='control-group'>
                        <label class='control-label' for="cambio-desde">Fecha de último cambio</label>
                        <div class='controls'>
                            <input type='text' name='updated_at_desde' id="cambio-desde" placeholder='Desde' class='datepicker input-small' value='<?= $updated_at_desde ?>' />
                            <div class="hidden-accessible"><label for="cambio-hasta">Fecha de último cambio hasta</label></div>
                            <input type='text' name='updated_at_hasta' id="cambio-hasta" placeholder='Hasta' class='datepicker input-small' value='<?= $updated_at_hasta ?>' />
                        </div>
                    </div>
                </div>
            </div>
      </fieldset>
      <div class='busqueda_avanzada'><button type="submit" class="btn btn-primary">Buscar</button></div>
    </form>
</div>

<?= $this->pagination->create_links() ?>

<table class="table">
  <caption class="hide-text">Procesos</caption>
    <thead>
        <tr>
            <th><a href="<?= current_url() . '?query=' . $query . '&pendiente=' . $pendiente . '&created_at_desde=' . $created_at_desde . '&created_at_hasta=' . $created_at_hasta . '&updated_at_desde=' . $updated_at_desde . '&updated_at_hasta=' . $updated_at_hasta . '&order=id&direction=' . ($direction == 'asc' ? 'desc' : 'asc') ?>">Id <?= $order == 'id' ? $direction == 'asc' ? '<span class="icon-chevron-down"></span>' : '<span class="icon-chevron-up"></span>'  : '' ?></a></th>
            <th><a href="<?= current_url() . '?query=' . $query . '&pendiente=' . $pendiente . '&created_at_desde=' . $created_at_desde . '&created_at_hasta=' . $created_at_hasta . '&updated_at_desde=' . $updated_at_desde . '&updated_at_hasta=' . $updated_at_hasta . '&order=pendiente&direction=' . ($direction == 'asc' ? 'desc' : 'asc') ?>">Estado <?= $order == 'pendiente' ? $direction == 'asc' ? '<span class="icon-chevron-down"></span>' : '<span class="icon-chevron-up"></span>'  : '' ?></a></th>
            <th>Etapa actual</th>
            <th>Documento</th>
            <th><a href="<?= current_url() . '?query=' . $query . '&pendiente=' . $pendiente . '&created_at_desde=' . $created_at_desde . '&created_at_hasta=' . $created_at_hasta . '&updated_at_desde=' . $updated_at_desde . '&updated_at_hasta=' . $updated_at_hasta . '&order=created_at&direction=' . ($direction == 'asc' ? 'desc' : 'asc') ?>">Fecha de creación <?= $order == 'created_at' ? $direction == 'asc' ? '<span class="icon-chevron-down"></span>' : '<span class="icon-chevron-up"></span>'  : '' ?></th>
            <th><a href="<?= current_url() . '?query=' . $query . '&pendiente=' . $pendiente . '&created_at_desde=' . $created_at_desde . '&created_at_hasta=' . $created_at_hasta . '&updated_at_desde=' . $updated_at_desde . '&updated_at_hasta=' . $updated_at_hasta . '&order=updated_at&direction=' . ($direction == 'asc' ? 'desc' : 'asc') ?>">Fecha de Último cambio <?= $order == 'updated_at' ? $direction == 'asc' ? '<span class="icon-chevron-down"></span>' : '<span class="icon-chevron-up"></span>'  : '' ?></a></th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tramites as $t): ?>
            <tr>
                <td><?= $t->id ?></td>
                <td><?= $t->pendiente ? 'En curso' : 'Completado' ?></td>
                <td>
                  <?php
                    $etapas_array = array();
                    $c=1;
                    $documento = null;
                    foreach ($t->getEtapasActuales() as $e) {
                      if($c == 1) {
                        $documento = $e->getUsuarioInicial();
                      }

                      $c++;

                      $etapas_array[] = $e->Tarea->nombre . ($e->vencimiento_at ? ' <a href="#" onclick="return editarVencimiento(' . $e->id . ')" title="Cambiar fecha de vencimiento">(' . $e->getFechaVencimientoAsString() . ')</a>' : '');
                    }

                    echo implode(', ', $etapas_array);
                  ?>
                </td>
                <td><?= $documento; ?></td>
                <td><?= strftime('%c', mysql_to_unix($t->created_at)) ?></td>
                <td><?= strftime('%c', mysql_to_unix($t->updated_at)) ?></td>
                <td class="actions">
                    <a class="btn btn-primary" href="<?= site_url('backend/seguimiento/ver/' . $t->id) ?>"><span class="icon-white icon-eye-open"></span> Seguimiento<span class="hide-text"> de <?= $t->id ?></span></a>
                    <?php if(UsuarioBackendSesion::usuario()->rol!='seguimiento'): ?><a class="btn btn-danger" href="<?= site_url('backend/seguimiento/borrar_tramite/' . $t->id) ?>" onclick="return confirm('¿Esta seguro que desea borrar estre trámite?')"><span class="icon-white icon-trash"></span> Eliminar<span class="hide-text"> <?= $t->id ?></span></a><?php endif ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->pagination->create_links() ?>

<div id="modal" class="modal hide fade" >

</div>

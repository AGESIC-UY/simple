<h1>Etapas sin asignar</h1>

<div class="busqueda_avanzada"><a href='#' id="busqueda_filtro_toggle">Búsqueda avanzada</a></div>
<br />
<fieldset id="busqueda_filtro" style="display:none;">
  <legend>Filtros de búsqueda</legend>

  <table width="100%">
    <tr>
      <td>
        <label for="busqueda_id_tramite" class="control-label">Id</label>
        <input class="filter" data-col="id" type="text" id="busqueda_id_tramite" name="busqueda_id_tramite" value="<?php if(isset($busqueda_id_tramite)) echo $busqueda_id_tramite; ?>"/>

        <label for="busqueda_etapa" class="control-label">Etapa</label>
        <input class="filter" data-col="etapa" type="text" id="busqueda_etapa" name="busqueda_etapa" value="<?php if(isset($busqueda_etapa)) echo $busqueda_etapa; ?>"/>

        <?php $has_grupos = count(UsuarioSesion::usuario()->GruposUsuarios);?>
        <?php if($has_grupos):?>
            <label for="busqueda_grupo" class="control-label">Grupo</label>
            <select class="filter" data-col="grupo" id="busqueda_grupo" name="busqueda_grupo">

              <?php if(isset($busqueda_grupo) && $busqueda_grupo != ''):?>
                <option value=""></option>
                  <?php if($has_grupos > 1):?>
                    <option value="">Todos</option>
                  <?php endif;?>

                  <?php foreach(UsuarioSesion::usuario()->GruposUsuarios as $grupo):?>
                      <?php if($grupo->id == $busqueda_grupo):?>
                        <option value="<?php echo $grupo->id; ?>" selected><?php echo $grupo->nombre; ?></option>
                        <?php else: ?>
                        <option value="<?php echo $grupo->id; ?>"><?php echo $grupo->nombre; ?></option>
                      <?php endif;?>
                  <?php endforeach;?>

                <?php else: ?>
                    <option value="" selected></option>
                    <?php if($has_grupos > 1):?>
                      <option value="">Todos</option>
                    <?php endif;?>

                    <?php foreach(UsuarioSesion::usuario()->GruposUsuarios as $grupo):?>
                      <option value="<?php echo $grupo->id; ?>"><?php echo $grupo->nombre; ?></option>
                    <?php endforeach;?>
                <?php endif;?>

            </select>
        <?php endif;?>

        <label for="busqueda_termino" class="control-label">Término a buscar</label>
        <input class="filter" type="text" id="busqueda_termino" name="busqueda_termino" value="<?php if(isset($busqueda_termino)) echo $busqueda_termino; ?>"/>
      </td>
      <td>
        <label for="busqueda_nombre" class="control-label">Nombre del trámite</label>
        <input class="filter" data-col="nombre" type="text" id="busqueda_nombre" name="busqueda_nombre"  value="<?php if(isset($busqueda_nombre)) echo $busqueda_nombre; ?>"/>

        <label for="busqueda_documento" class="control-label">Documento del trámite o del usuario</label>
        <input class="filter" data-col="documento" type="text" id="busqueda_documento" name="busqueda_documento" value="<?php if(isset($busqueda_documento)) echo $busqueda_documento; ?>"/>

        <label for="busqueda_modificacion" class="control-label">Fecha de último cambio</label>
        <input class="datepicker_" type="text" id="busqueda_modificacion_desde" name="busqueda_modificacion_desde" placeholder="Desde" value="<?php if(isset($busqueda_modificacion_desde)) echo $busqueda_modificacion_desde; ?>"/>
        <input class="datepicker_" type="text" id="busqueda_modificacion_hasta" name="busqueda_modificacion_hasta" placeholder="Hasta"  value="<?php if(isset($busqueda_modificacion_hasta)) echo $busqueda_modificacion_hasta; ?>"/>
        <input class="filter hidden" data-col="modificación" type="text" id="busqueda_modificacion" name="busqueda_modificacion" />

        <br /><br />
        <input type="button" id="btn_buscar_filtro" class="btn btn-primary" value="Buscar" />
        <a id="limpiar_filtro" href="?filtro=1">Limpiar</a>
      </td>
    </tr>
  </table>
  <div id="lbl_error_filtro"></div>
</fieldset>


<?php if (count($etapas) > 0): ?>
<table id="mainTable" class="table">
  <caption class="hide-text">Etapas sin asignar</caption>
    <thead>
        <tr>
            <th>Id</th>
            <th>Nombre</th>
            <th>Etapa</th>
            <?php if(UsuarioSesion::usuario()->registrado && UsuarioSesion::usuario()->cuenta_id): ?>
              <th>Documento</th>
            <?php endif; ?>
            <th>Modificación</th>
            <th class="hidden">Grupo</th>
            <th>Vencimiento</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($etapas as $e): ?>
            <tr <?=$e->getPrevisualizacion()?'data-toggle="popover" data-html="true" data-title="<h4>Previsualización</h4>" data-content="'.htmlspecialchars($e->getPrevisualizacion()).'" data-trigger="hover" data-placement="bottom"':''?>>
                <td class="list_id_tramite"><?=$e->Tramite->id?></td>
                <td class="name" class="list_nombre"><?= $e->Tramite->Proceso->nombre ?></td>
                <td class="list_etapa"><?=$e->Tarea->nombre ?></td>
                <?php if(UsuarioSesion::usuario()->registrado && UsuarioSesion::usuario()->cuenta_id): ?>
                  <td class="list_documento"><?= $e->getUsuarioInicial(); ?></td>
                <?php endif; ?>
                <td class="time list_modificacion"><?= strftime('%d.%b.%Y',mysql_to_unix($e->updated_at))?> <br /><?= strftime('%H:%M:%S',mysql_to_unix($e->updated_at))?></td>
                <td data-title="Grupo" class="hidden list_grupo">
                  <?php
                    echo $e->Tarea->grupos_usuarios;
                  ?>
                </td>
                <td><?=$e->vencimiento_at?strftime('%c',strtotime($e->vencimiento_at)):'N/A'?></td>
                <td class="actions"><a href="<?=site_url('etapas/asignar/'.$e->id)?>" class="btn btn-primary"><span class="icon-check icon-white"></span> Asignármelo <span class="hide-text">Etapa <?=$e->Tramite->id?></span></a></td>
                <td class="actions"> <a href="<?=site_url('etapas/imprimir_pasos_tarea_sin_asignar/'.$e->id)?>" class="btn btn-primary" target="_blank"> <span class="icon-file-text icon-white"></span> Visualizar </a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div id="paginado_div"><?php if(isset($this->pagination)) echo $this->pagination->create_links(); ?></div>
<?php else: ?>
  <table id="mainTable" class="table"></table>
  <p>No hay trámites sin asginar.</p>
<?php endif; ?>

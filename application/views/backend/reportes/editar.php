<ul class="breadcrumb">
    <li><a href="<?= site_url('backend/reportes') ?>">Gestión</a><span class="divider">/</span></li>
    <li><a href="<?=site_url('backend/reportes/listar/'.$proceso->id)?>"><?= $proceso->nombre ?></a><span class="divider">/</span></li>
    <li class="active"><?=$edit?$reporte->nombre:'Reporte'?></li>
</ul>
<h2>Reporte</h2>

<form class="ajaxForm form-horizontal" method="POST" action="<?=site_url('backend/reportes/editar_form/'.($edit?$reporte->id:''))?>">
    <fieldset>
        <legend>Datos generales</legend>
        <div class="validacion validacion-error"></div>
        <?php if(!$edit):?>
        <input type="hidden" name="proceso_id" value="<?=$proceso->id?>" />
        <?php endif; ?>
        <div class="control-group">
          <label class="control-label" for="nombre">Nombre</label>
          <div class="controls">
            <input id="nombre" type="text" name="nombre" value="<?=$edit?$reporte->nombre:''?>" />
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="tipo">Tipo</label>
          <div class="controls">
            <select id="tipo" name="tipo">
                <option value="resumen" <?php if(isset($reporte)){ echo $reporte->tipo == 'resumen' ? 'selected' : ''; } ?>>B&aacute;sico</option>
              <option value="completo" <?php if(isset($reporte)){ echo $reporte->tipo == 'completo' ? 'selected' : ''; } ?>>Completo</option>
            </select>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="campos">Campos</label>
          <div class="controls">
            <select id="campos" name="campos[]" style="height: 240px;" multiple>
                <?php foreach($proceso->getNombresDeVariables() as $c):?>
                  <?php if(!(strpos($c, 'email_tramite_inicial__e') !== FALSE) && !preg_match('/^[a-f0-9]{32}$/', $c) && !(strpos($c, 'documento_tramite_inicial__e') !== FALSE) && !(strpos($c, 'documento_tramite__') !== FALSE) && !(strpos($c, 'ws_error') !== FALSE) && !(strpos($c, 'BLOQUE_') !== FALSE)): ?>
                      <option value="<?=$c?>" <?=$edit && in_array($c,$reporte->campos)?'selected':''?>><?=$c?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="control-group">
          <label for="grupos_usuarios_permiso" class="control-label">Grupos de Usuarios</label>
          <div class="controls">
            <select class="chosen" id="grupos_usuarios_permiso" name="grupos_usuarios_permiso[]" data-placeholder="Seleccione los grupos de usuarios que tienen  permiso para ejecutar el reporte" multiple>
                <?php foreach($grupos_usuarios as $g): ?>
                  <option value="<?=$g->id?>" <?=in_array($g->id,explode(',',$reporte->grupos_usuarios_permiso))?'selected':''?> > <?=$g->nombre?> </option>
                <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="control-group">
          <label for="usuarios_permiso" class="control-label">Usuarios</label>
          <div class="controls">
            <select class="chosen" id="usuarios_permiso" name="usuarios_permiso[]" data-placeholder="Seleccione los usuarios que tienen permiso para ejecutar el reporte" multiple>
                <?php foreach($usuarios_frontend_y_backend as $uf): ?>
                  <option value="<?=$uf->usuario?>" <?=in_array($uf->usuario,explode(',',$reporte->usuarios_permiso))?'selected':''?> > <?=$uf->displayUsername(true).' (backend y forntend)'?> </option>
                <?php endforeach; ?>
                <?php foreach($usuarios_solo_backend as $ub): ?>
                  <option value="<?=$ub->usuario?>" <?=in_array($ub->usuario,explode(',',$reporte->usuarios_permiso))?'selected':''?> > <?=$ub->displayUsername(true).' (solo backend)'?> </option>
                <?php endforeach; ?>
            </select>
          </div>
        </div>
    </fieldset>

    <ul class="form-action-buttons">
        <li class="action-buttons-primary">
            <ul>
                <li>
                  <input class="btn btn-primary btn-lg" type="submit" value="Guardar" />
                </li>
            </ul>
        </li>
        <li class="action-buttons-second">
            <ul>
                <li class="float-left">
                  <a class="btn btn-link btn-lg" href="<?=site_url('backend/reportes/listar/'.$proceso->id)?>">Cancelar</a>
                </li>
            </ul>
        </li>
    </ul>
</form>

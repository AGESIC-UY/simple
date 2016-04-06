<h2>Editar cuenta</h2>
<form method="POST" class="ajaxForm" action="<?=site_url('cuentas/editar_form')?>">
    <fieldset>
        <legend>Completa la información de tu cuenta</legend>
        <div class="validacion cuenta-editar"></div>
        <div class="form-horizontal">
          <div class="control-group">
            <label for="nombre" class="control-label">Nombres</label>
            <div class="controls">
              <input type="text" id="nombre" name="nombres" value="<?=$usuario->nombres?>" />
              <div class="mensaje_error_campo"><?php if(isset($mensaje)) { if($tipo_error == 'primer_nombre') { echo $mensaje; }} ?></div>
            </div>
          </div>
          <div class="control-group">
            <label for="apellido_paterno" class="control-label">Apellido Paterno</label>
            <div class="controls">
              <input type="text" id="apellido_paterno" name="apellido_paterno" value="<?=$usuario->apellido_paterno?>" />
              <div class="mensaje_error_campo"><?php if(isset($mensaje)) { if($tipo_error == 'primer_apellido') { echo $mensaje; }} ?></div>
            </div>
          </div>
          <div class="control-group">
            <label for="apellido_materno" class="control-label">Apellido Materno</label>
            <div class="controls">
              <input type="text" id="apellido_materno" name="apellido_materno" value="<?=$usuario->apellido_materno?>" />
              <div class="mensaje_error_campo"><?php if(isset($mensaje)) { if($tipo_error == 'segundo_apellido') { echo $mensaje; }} ?></div>
            </div>
          </div>
          <div class="control-group">
            <label for="email" class="control-label">Correo electrónico</label>
            <div class="controls">
              <input type="text" id="email" name="email" value="<?=$usuario->email?>" />
              <div class="mensaje_error_campo"><?php if(isset($mensaje)) { if($tipo_error == 'email') { echo $mensaje; }} ?></div>
            </div>
          </div>
          <?php if($usuario->cuenta_id): ?>
            <div class="control-group">
              <span class="control-label"></span>
              <div class="controls">
                <label class="checkbox" for="vacaciones"><input type="checkbox" id="vacaciones" name="vacaciones" value="1" <?=$usuario->vacaciones?'checked':''?> /> ¿Fuera de oficina?</label>
              </div>
            </div>
          <?php endif ?>
          <input type="hidden" name="redirect" value="<?=$redirect?>" />
    </fieldset>
    <ul class="form-action-buttons">
        <li class="action-buttons-primary">
            <ul>
                <li>
                  <button class="btn btn-primary" type="submit">Guardar</button>
                </li>
            </ul>
        </li>
        <li class="action-buttons-second">
            <ul>
                <li class="float-left">
                  <button class="btn btn-link" type="button" onclick="javascript:history.back()">Cancelar</button>
                </li>
            </ul>
        </li>
    </ul>
</form>

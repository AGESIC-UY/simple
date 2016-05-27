<ul class="breadcrumb">
  <li>Administración <span class="divider">/</span></li>
  <li>
    <a href="<?= site_url('manager/cuentas') ?>">Usuarios Backend</a> <span class="divider">/</span>
  </li>
  <li class="active"><?= $title ?> <?=$usuario->email?></li>
</ul>

<h2>Usuario: <?=$usuario->email?></h2>
<form class="ajaxForm" method="post" action="<?= site_url('manager/usuarios/editar_form/' . $usuario->id) ?>">
  <fieldset>
    <legend><?= $title ?></legend>
    <div class="form-horizontal">
      <div class="validacion validacion-error"></div>
      <div class="control-group">
        <label for="usuario" class="control-label">Usuario</label>
        <div class="controls">
          <input id="usuario" type="text" name="usuario" value="<?=isset($usuario)?$usuario->usuario:''?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="email">Correo Electrónico</label>
        <div class="controls">
          <input id="email" type="text" name="email" value="<?=$usuario->email?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="password">Contraseña</label>
        <div class="controls">
          <input id="password" type="password" name="password" value=""/>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="password_confirm">Confirmar contraseña</label>
        <div class="controls">
          <input id="password_confirm" type="password" name="password_confirm" value=""/>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="nombre">Nombre</label>
        <div class="controls">
          <input id="nombre" type="text" name="nombre" value="<?= $usuario->nombre?>"/>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="apellidos">Apellidos</label>
        <div class="controls">
          <input id="apellidos" type="text" name="apellidos" value="<?= $usuario->apellidos?>"/>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="cuenta_id">Cuenta</label>
        <div class="controls">
          <select id="cuenta_id" name="cuenta_id">
              <?php foreach($cuentas as $c):?>
              <option value="<?=$c->id?>" <?=$c->id==$usuario->cuenta_id?'selected':''?>><?=$c->nombre?></option>
              <?php endforeach ?>
          </select>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="rol">Rol</label>
        <div class="controls">
          <select id="rol" name="rol">
              <option value="super" <?= $usuario->rol == 'super' ? 'selected' : '' ?>>super</option>
              <option value="modelamiento" <?= $usuario->rol == 'modelamiento' ? 'selected' : '' ?>>modelamiento</option>
              <option value="operacion" <?= $usuario->rol == 'operacion' ? 'selected' : '' ?>>operacion</option>
              <option value="gestion" <?= $usuario->rol == 'gestion' ? 'selected' : '' ?>>gestion</option>
          </select>
        </div>
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
                <a class="btn btn-link btn-lg" href="<?= site_url('manager/usuarios') ?>">Cancelar</a>
              </li>
          </ul>
      </li>
  </ul>
</form>

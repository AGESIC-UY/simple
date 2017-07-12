<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?= site_url('backend/configuracion') ?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/configuracion/usuarios') ?>">Usuarios</a> <span class="divider">/</span>
            </li>
            <li class="active"><?= isset($usuario) ?$usuario->usuario:'Crear' ?></li>
        </ul>
        <h2><?= isset($usuario) ?$usuario->usuario:'Crear' ?></h2>
        <form class="ajaxForm" method="post" action="<?= site_url('backend/configuracion/usuario_editar_form/' . (isset($usuario)?$usuario->id:'')) ?>">
            <fieldset>
                <legend>Editar usuario</legend>
                <div class="validacion validacion-error"></div>
                <div class="form-horizontal">
                  <div class="control-group">
                    <label for="nombre" class="control-label">Usuario</label>
                    <div class="controls">
                      <input type="text" id="nombre" name="usuario" value="<?=isset($usuario)?$usuario->usuario:''?>" />
                      <?php if(!isset($usuario)) { ?>
                        <a href="#" class="btn btn-primary" id="verificar_existe_usuario">Verificar</a>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="password" class="control-label">Contraseña</label>
                    <div class="controls">
                      <input type="password" id="password" name="password" value=""/>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="password_confirm" class="control-label">Confirmar contraseña</label>
                    <div class="controls">
                      <input type="password" id="password_confirm" name="password_confirm" value=""/>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="nombres" class="control-label">Nombres</label>
                    <div class="controls">
                      <input type="text" id="nombres" name="nombres" value="<?=isset($usuario)?$usuario->nombres:''?>"/>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="apellido_paterno" class="control-label">Apellido Paterno</label>
                    <div class="controls">
                      <input type="text" id="apellido_paterno" name="apellido_paterno" value="<?=isset($usuario)?$usuario->apellido_paterno:''?>"/>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="apellido_materno" class="control-label">Apellido Materno</label>
                    <div class="controls">
                      <input type="text" id="apellido_materno" name="apellido_materno" value="<?=isset($usuario)?$usuario->apellido_materno:''?>"/>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="email" class="control-label">Correo electrónico</label>
                    <div class="controls">
                      <input type="text" id="email" name="email" value="<?=isset($usuario)?$usuario->email:''?>"/>
                    </div>
                  </div>
                  <div class="control-group">
                    <span class="control-label"></span>
                    <div class="controls">
                      <label class="checkbox" for="oficina"><input type="checkbox" id="oficina" name="vacaciones" value="1" <?=isset($usuario) && $usuario->vacaciones?'checked':''?> /> ¿Fuera de oficina?</label>
                    </div>
                  </div>
                  <div class="control-group">
                    <span class="control-label"></span>
                    <div class="controls">
                      <label class="checkbox" for="acceso_reportes"><input type="checkbox" id="acceso_reportes" name="acceso_reportes" value="1" <?=isset($usuario) && $usuario->acceso_reportes?'checked':''?> /> ¿Acceso a reportes?</label>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="grupos_usuarios" class="control-label">Grupos de Usuarios</label>
                    <div class="controls">
                      <select class="chosen" id="grupos_usuarios" name="grupos_usuarios[]" data-placeholder="Seleccione los grupos de usuarios" multiple>
                          <?php foreach($grupos_usuarios as $g): ?>
                          <option value="<?=$g->id?>" <?=isset($usuario) && $usuario->hasGrupoUsuarios($g->id)?'selected':''?>><?=$g->nombre?></option>
                          <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                </div>
            </fieldset>
            <ul class="form-action-buttons">
                <li class="action-buttons-primary">
                    <ul>
                        <li>
                          <button class="btn btn-primary btn-lg" type="submit">Guardar</button>
                        </li>
                    </ul>
                </li>
                <li class="action-buttons-second">
                    <ul>
                        <li class="float-left">
                          <a class="btn btn-link btn-lg" href="<?=site_url('backend/configuracion/usuarios')?>">Cancelar</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </form>

    </div>
</div>

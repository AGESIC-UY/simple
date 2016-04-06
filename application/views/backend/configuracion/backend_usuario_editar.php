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
            <li class="active"><?= isset($usuario) ?$usuario->email:'Crear' ?></li>
        </ul>
        <h2 class="active"><?= isset($usuario) ?$usuario->email:'Crear' ?></h2>
        <form class="ajaxForm" method="post" action="<?= site_url('backend/configuracion/backend_usuario_editar_form/' . (isset($usuario)?$usuario->id:'')) ?>">
            <fieldset>
              <legend>Editar usuario</legend>
              <div class="validacion"></div>
              <div class="form-horizontal">
                  <div class="control-group">
                    <label for="id" class="control-label">E-Mail</label>
                    <div class="controls">
                      <input type="text" name="email" value="<?=isset($usuario)?$usuario->email:''?>" <?=  isset($usuario)?'disabled':''?>/>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="id" class="control-label">Contraseña</label>
                    <div class="controls">
                      <input type="password" name="password" value=""/>
                      <?php if(isset($usuario)):?><span class="help-inline">Solo si desea modificarla</span><?php endif ?>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="id" class="control-label">Confirmar contraseña</label>
                    <div class="controls">
                      <input type="password" name="password_confirm" value=""/>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="id" class="control-label">Nombre</label>
                    <div class="controls">
                      <input type="text" name="nombre" value="<?=isset($usuario)?$usuario->nombre:''?>"/>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="id" class="control-label">Apellidos</label>
                    <div class="controls">
                      <input type="text" name="apellidos" value="<?=isset($usuario)?$usuario->apellidos:''?>"/>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="id" class="control-label">Rol</label>
                    <div class="controls">
                      <select name="rol">
                          <option value="super" <?=  isset($usuario) && $usuario->rol=='super'?'selected':''?>>super</option>
                          <option value="modelamiento" <?=  isset($usuario) && $usuario->rol=='modelamiento'?'selected':''?>>modelamiento</option>
                          <option value="seguimiento" <?=  isset($usuario) && $usuario->rol=='seguimiento'?'selected':''?>>seguimiento</option>
                          <option value="operacion" <?=  isset($usuario) && $usuario->rol=='operacion'?'selected':''?>>operación</option>
                          <option value="gestion" <?=  isset($usuario) && $usuario->rol=='gestion'?'selected':''?>>gestión</option>
                          <option value="desarrollo" <?=  isset($usuario) && $usuario->rol=='desarrollo'?'selected':''?>>desarrollo</option>
                          <option value="configuracion" <?=  isset($usuario) && $usuario->rol=='configuracion'?'selected':''?>>configuración</option>
                      </select>
                      <div class="help-block">
                        <ul>
                          <li>super: Tiene todos los privilegios del sistema.</li>
                          <li>modelamiento: Permite modelar y diseñar el funcionamiento del trámite.</li>
                          <li>seguimiento: Permite hacer seguimiento de los trámites.</li>
                          <li>operación: Permite hacer seguimiento y operaciones sobre los trámites como eliminación y edición.</li>
                          <li>gestión: Permite acceder a reportes de gestión y uso de la plataforma.</li>
                          <li>desarrollo: Permite acceder a la API de desarrollo, para la integración con plataformas externas.</li>
                          <li>configuración: Permite configurar los usuarios y grupos de usuarios que tienen acceso al sistema.</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
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
                          <a class="btn btn-link" href="<?=site_url('backend/configuracion/backend_usuarios')?>">Cancelar</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </form>
    </div>
</div>

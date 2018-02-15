<script>
  function updateRolesObserver() {
    if($('.check_rol[data-rol=seguimiento]').is(':checked')) {
      $('#seguimiento').show();
    }
    else {
      $('#seguimiento').hide();
    }
    
    $('#seg_alc_control_total').change(function() {
      if ($(this).is(':checked')) {
        $('#grupos').hide();
      }
      else {
        $('#grupos').show();
      }
    });

    $(".rol").change();
    $("#seg_alc_control_total").change();

    if(document.exists_seguimiento_role) {
      if(!$('#seg_alc_control_total').is(':checked')) {
        $('#grupos').show();
      }
      else {
        $('#grupos').hide();
      }
    }

    var checked_roles = [];
    $('.check_rol:checked').each(function() {
      checked_roles.push($(this).attr('data-rol'));
    });

    $('input[name=rol]').val(checked_roles.toString());
  }

  function loadRoles() {
    var roles = $('input[name=rol]').val();
        roles = roles.split(',');

    $(roles).each(function() {
      $('.check_rol[data-rol='+ this.toString() +']').prop('checked', true);

      if(this.toString() == 'seguimiento') {
        document.exists_seguimiento_role = 1;
      }
    });

    updateRolesObserver();
  }

  $(document).ready(function() {
    loadRoles();

    if($('.check_rol[data-rol=super]').is(':checked')) {
      $('.check_rol').not('.check_rol[data-rol=super]').attr({'disabled': true});
      $('.check_rol[data-rol=super]').attr({'disabled': false});
    }
    else if($('.check_rol').not('.check_rol[data-rol=super]').is(':checked')) {
      $('.check_rol[data-rol=super]').attr({'disabled': true});
      $('.check_rol').not('.check_rol[data-rol=super]').attr({'disabled': false});
    }

    $('.check_rol').change(function() {
      if($('.check_rol[data-rol=super]').is(':checked')) {
        $('.check_rol').not('.check_rol[data-rol=super]').attr({'disabled': true});
        $('.check_rol[data-rol=super]').attr({'disabled': false});
      }
      else if($('.check_rol').not('.check_rol[data-rol=super]').is(':checked')) {
        $('.check_rol[data-rol=super]').attr({'disabled': true});
        $('.check_rol').not('.check_rol[data-rol=super]').attr({'disabled': false});
      }
      else {
        $('.check_rol').attr({'disabled': false});
      }

      updateRolesObserver();
    });
  });
</script>
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
              <div class="validacion validacion-error"></div>
              <div class="form-horizontal">
                  <div class="control-group">
                    <label for="usuario" class="control-label">Usuario</label>
                    <div class="controls">
                      <input id="usuario" type="text" name="usuario" value="<?=isset($usuario)?$usuario->usuario:''?>" />
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="email" class="control-label">Correo electrónico</label>
                    <div class="controls">
                      <input id="email" type="text" name="email" value="<?=isset($usuario)?$usuario->email:''?>" <?=  isset($usuario)?'disabled':''?>/>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="password" class="control-label">Contraseña</label>
                    <div class="controls">
                      <input id="password" type="password" name="password" value=""/>
                      <?php if(isset($usuario)):?><span class="help-inline">Solo si desea modificarla</span><?php endif ?>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="password_confirm" class="control-label">Confirmar contraseña</label>
                    <div class="controls">
                      <input id="password_confirm" type="password" name="password_confirm" value=""/>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="nombre" class="control-label">Nombre</label>
                    <div class="controls">
                      <input id="nombre" type="text" name="nombre" value="<?=isset($usuario)?$usuario->nombre:''?>"/>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="apellidos" class="control-label">Apellidos</label>
                    <div class="controls">
                      <input id="apellidos" type="text" name="apellidos" value="<?=isset($usuario)?$usuario->apellidos:''?>"/>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="rol" class="control-label">Rol</label>
                    <div class="controls">
                      <input type="hidden" id="rol" name="rol" value="<?php if(isset($usuario)) { echo $usuario->rol; } ?>" />
                      <span id="rol_modelo">
                        <div class="help-block lista_de_roles" style="font-size:8pt;">
                          <dl>
                            <dt><input type="checkbox" class="check_rol" data-rol="super" /> Super</dt>
                            <dd>Tiene todos los privilegios del sistema.</dd>
                            <dt><input type="checkbox" class="check_rol" data-rol="modelamiento" /> Modelamiento</dt>
                            <dd>Permite modelar y diseñar el funcionamiento del trámite.</dd>
                            <dt><input type="checkbox" class="check_rol" data-rol="seguimiento" /> Seguimiento</dt>
                            <dd>Permite hacer seguimiento de los trámites.</dd>

                            <div id="seguimiento">
                              <div>
                                <label class="checkbox" for="seg_alc_control_total"><input type="checkbox" id="seg_alc_control_total" name="seg_alc_control_total" value="1" <?=isset($usuario) && $usuario->seg_alc_control_total?'checked':''?> /> Control Total</label>
                              </div>
                              <div id='grupos'>
                                <select  id="seg_alc_grupos_usuarios" name="seg_alc_grupos_usuarios[]" multiple>
                                  <?php if(count($grupos_usuarios) >= 1): ?>
                                   <option value="todos" <?=isset($usuario) && in_array('todos',$usuario->seg_alc_grupos_usuarios)?'selected':''?>>Todos</option>
                                 <?php endif; ?>
                                    <?php foreach($grupos_usuarios as $g): ?>
                                      <option value="<?=$g->id?>" <?=isset($usuario) && in_array($g->id,$usuario->seg_alc_grupos_usuarios)?'selected':''?>><?=$g->nombre?></option>
                                    <?php endforeach; ?>
                                </select>
                              </div>
                              <div>
                                <label class="checkbox" for="seg_reasginar"><input type="checkbox" id="seg_reasginar" name="seg_reasginar" value="1" <?=isset($usuario) && $usuario->seg_reasginar?'checked':''?> /> Reasignar tareas de funcionario</label>
                              </div>
                              <div>
                                <label class="checkbox" for="seg_reasginar_usu"><input type="checkbox" id="seg_reasginar_usu" name="seg_reasginar_usu" value="1" <?=isset($usuario) && $usuario->seg_reasginar_usu?'checked':''?> /> Reasignar tareas de ciudadano</label>
                              </div>
                            </div>

                            <dt><input type="checkbox" class="check_rol" data-rol="gestion" /> Gestión</dt>
                            <dd>Permite acceder a reportes de gestión y uso de la plataforma.</dd>
                            <dt><input type="checkbox" class="check_rol" data-rol="desarrollo" /> Desarrollo</dt>
                            <dd>Permite acceder a la API de desarrollo, para la integración con plataformas externas.</dd>
                            <dt><input type="checkbox" class="check_rol" data-rol="configuracion" /> Configuración</dt>
                            <dd>Permite configurar los usuarios y grupos de usuarios que tienen acceso al sistema.</dd>
                          </dl>
                        </div>
                      </span>
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
                          <a class="btn btn-link btn-lg" href="<?=site_url('backend/configuracion/backend_usuarios')?>">Cancelar</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </form>
    </div>
</div>

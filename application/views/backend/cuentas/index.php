<ul class="breadcrumb">
  <li class="active"><?= $title ?></li>
</ul>
<h2><?= $title ?></h2>



<?php if($this->session->flashdata('message')):?>
<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <?=$this->session->flashdata('message')?></div>
<?php endif?>

<form class="ajaxForm" method="post" action="<?= site_url('backend/cuentas/cuenta_form/' . (isset($usuario)?$usuario->id:'')) ?>">
    <fieldset>
        <legend>Cambiar contraseña</legend>
        <div class="validacion validacion-error"></div>
        <div class="form-horizontal">
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
                  <a class="btn btn-link btn-lg" href="#" onclick="javascript:history.back()">Cancelar</a>
                </li>
            </ul>
        </li>
    </ul>
</form>

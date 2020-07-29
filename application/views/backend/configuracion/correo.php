<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?= site_url('backend/configuracion') ?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li class="active">Correo electrónico</li>
        </ul>
        <h2>General: Correo electrónico</h2>
        <form class="ajaxForm" method="post" action="<?= site_url('backend/configuracion/correo_form/') ?>">
            <fieldset id="accion-correo">
                <legend>Editar información de correo para la cuenta</legend>
                <div class="validacion validacion-error"></div>
                <div class="form-horizontal">
                  <div class="control-group">
                    <label for="correo_remitente" class="control-label">Dirección de remitente</label>
                    <div class="controls">
                      <input class="input-large" id="correo_remitente" type="text" name="correo_remitente" value="<?=$cuenta->correo_remitente?>" />
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
                          <a class="btn btn-link btn-lg" href="<?=site_url('backend/configuracion/')?>">Cancelar</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </form>

    </div>
</div>

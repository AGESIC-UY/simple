<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?= site_url('backend/configuracion') ?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li class="active">Notificaciones</li>
        </ul>
        <h2>Monitoreo: Notificaciones</h2>
        <form class="ajaxForm" method="post" action="<?= site_url('backend/configuracion/monitoreo_notificaciones_form/') ?>">
            <fieldset>

                <legend>Editar correo para envio de notificaciones</legend>
                <div class="validacion validacion-error"></div>
                <div class="form-horizontal">
                  <div class="control-group">
                    <label for="email" class="control-label">Email </label>
                    <div class="controls">
                      <input id="email" type="email" name="email" value="<?= $email; ?>"/>
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
                          <a class="btn btn-link btn-lg" href="<?=site_url('backend/configuracion/misitio')?>">Cancelar</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </form>

    </div>
</div>

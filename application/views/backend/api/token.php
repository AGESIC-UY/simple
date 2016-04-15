<div class="row-fluid">
    <div class="span3">
        <?php $this->load->view('backend/api/sidebar') ?>
    </div>
    <div class="span9">
      <ul class="breadcrumb">
          <li>
              <a href="<?= site_url('backend/api') ?>">Api</a> <span class="divider">/</span>
          </li>
          <li class="active"><?=$title?></li>
      </ul>
        <h2><?=$title?></h2>

        <form class="ajaxForm" method="post" action="<?=site_url('backend/api/token_form')?>">
            <fieldset>
                <legend>Configurar Código de Acceso</legend>
                <div class="validacion validacion-error"></div>
                <p>Para poder acceder a la API deberas configrar un código de acceso (token). Si dejas en blanco el token no se podra acceder a la API.</p>
                <div class="form-horizontal margen-sup">
                  <div class="control-group">
                    <label for="token" class="control-label">token</label>
                    <div class="controls">
                      <input type="text" id="token" name="api_token" value="<?=$cuenta->api_token?>" />
                      <div class="help-block">Especificar un código aleatorio de máximo 32 caracteres.</div>
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
                          <a class="btn btn-link btn-lg" href="<?=site_url('backend/api')?>">Cancelar</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </form>
    </div>
</div>

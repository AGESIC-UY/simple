<script>
$(document).ready(function() {

    <?php if($cuenta->envio_guid_automatico): ?>
        $("#envio_guid_automatico").attr('checked', true);
        $("#envio_guid_automatico").attr('value', '1');
      //  $('#asunto_email_guid').val('< ?= $cuenta->asunto_email_guid ?>');
      //  $('#cuerpo_email_guid').val('< ?= $cuenta->cuerpo_email_guid ?>');
        $('#datos_envio_guid').show();
    <?php else: ?>
      $("#envio_guid_automatico").attr('checked', false);
      $("#envio_guid_automatico").attr('value', '0');
      $('#datos_envio_guid').hide();
    <?php endif ?>

    $('#envio_guid_automatico').change(function() {
        if(this.checked) {
          $(this).attr('value', '1');

          $('#datos_envio_guid').show();
        }
        else{
          $(this).attr('value', '0');

          $('#datos_envio_guid').hide();
          $('.validacion-error').hide();
          $('.error').removeClass('error');
          $('.mensaje_error_campo').remove();
        }
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
            <li class="active">Envío de GUID</li>
        </ul>
        <h2>Trazabilidad: Envío de GUID</h2>
        <form class="ajaxForm" method="post" action="<?= site_url('backend/configuracion/trazabildiad_envio_guid_form/') ?>">
            <fieldset>

                <legend>Configuración</legend>
                <div class="validacion validacion-error"></div>
                <div class="form-horizontal">

                  <div class="control-group">
                    <div class="controls">
                      <label class="checkbox" for="envio_guid_automatico">
                        <input type="checkbox" id="envio_guid_automatico" name="envio_guid_automatico">
                        ¿Envío de GUID automático?
                      </label>
                    </div>
                  </div>

                  <div id="datos_envio_guid" style="display:none;">
                    <div class="control-group">
                      <label for="asunto_email_guid" class="control-label">Asunto</label>
                      <div class="controls">
                        <input id="asunto_email_guid" type="text" name="asunto_email_guid" value="<?=$cuenta->asunto_email_guid?>">
                      </div>
                    </div>
                    <div class="control-group">
                      <label for="cuerpo_email_guid" class="control-label">Cuerpo de email</label>
                      <div class="controls">
                          <textarea name="cuerpo_email_guid" id="cuerpo_email_guid" class="input-xxlarge" ><?=$cuenta->cuerpo_email_guid?></textarea>
                      </div>
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

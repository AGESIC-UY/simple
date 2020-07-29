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
            <li class="active">Envío de Involucrado/Solicitante</li>
        </ul>
        <h2>Trazabilidad: Envío de Involucrado/Solicitante</h2>
        <form class="ajaxForm" method="post" action="<?= site_url('backend/configuracion/trazabildiad_involucrado_form/') ?>">
            <fieldset>

                <legend>Configuración</legend>
                <div class="validacion validacion-error"></div>
                <div class="form-horizontal">

                  <div class="control-group">
                    <div class="controls">
                      <label class="radio" for="trazabilidad_involucrado_1">
                          <input type="radio" <?=($cuenta->traza_involucrado==1)?'checked=""':''?>  value="1" id="trazabilidad_involucrado_1" name="trazabilidad_involucrado">
                        No almacenar nunca el Involucrado/Solicitante
                      </label>
                        <label class="radio" for="trazabilidad_involucrado_2">
                            <input type="radio" <?=($cuenta->traza_involucrado==2)?'checked=""':''?>  value="2" id="trazabilidad_involucrado_2" name="trazabilidad_involucrado">
                        Almacenar Siempre el Involucrado/Solicitante
                      </label>
                        <label class="radio" for="trazabilidad_involucrado_3">
                            <input type="radio" <?=($cuenta->traza_involucrado==3)?'checked=""':''?>  value="3" id="trazabilidad_involucrado_3" name="trazabilidad_involucrado">
                        Usar la configuración de almacenado de Involucrado/Solicitante del trámite
                      </label>
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

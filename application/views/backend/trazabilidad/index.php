<script>
$(document).ready(function() {

    <?php if($cuenta->envio_guid_automatico): ?>
        <?php if($proceso->ProcesoTrazabilidad->envio_guid_automatico): ?>
          $("#envio_guid_automatico").attr('checked', true);
          $("#envio_guid_automatico").attr('value', '1');
          $('#envio_guid_automatico_div').show();
          $('#no_config_cuenta').hide();
          <?php else: ?>
            $("#envio_guid_automatico").attr('checked', false);
            $("#envio_guid_automatico").attr('value', '0');
            $('#envio_guid_automatico_div').hide();
            $('#no_config_cuenta').hide();
          <?php endif; ?>

    <?php else: ?>
        $("#envio_guid_automatico").attr('checked', false);
        $("#envio_guid_automatico").attr('value', '0');
        $('#envio_guid_automatico_div').hide();
        $("#envio_guid_automatico").attr("disabled", true);
        $('#no_config_cuenta').show();
        $('#envio_guid_automatico').hide();
    <?php endif ?>

    $('#envio_guid_automatico').change(function() {
        if(this.checked) {
          $(this).attr('value', '1');

          $('#envio_guid_automatico_div').show();
        }
        else{
          $(this).attr('value', '0');

          $('#envio_guid_automatico_div').hide();
          $('.validacion-error').hide();
          $('.error').removeClass('error');
          $('.mensaje_error_campo').remove();
        }
    });
});
</script>
<ul class="breadcrumb">
  <li>
      <a href="<?= site_url('backend/procesos') ?>">Listado de Procesos</a> <span class="divider">/</span>
  </li>
  <li class="active"><?= $proceso->nombre ?></li>
</ul>
<h2><?=$proceso->nombre?></h2>
<ul class="nav nav-tabs">
    <li><a href="<?=site_url('backend/procesos/editar/'.$proceso->id)?>">Diseñador</a></li>
    <li><a href="<?=site_url('backend/formularios/listar/'.$proceso->id)?>">Formularios</a></li>
    <li><a href="<?= site_url('backend/documentos/listar/' . $proceso->id) ?>">Documentos</a></li>
    <li><a href="<?=site_url('backend/acciones/listar/'.$proceso->id)?>">Acciones</a></li>
    <li class="active"><a href="<?= site_url('backend/trazabilidad/listar/' . $proceso->id) ?>">Trazabilidad</a></li>
    <li><a href="<?= site_url('backend/procesos/editar_codigo_tramite_ws_grep/' . $proceso->id) ?>">Código tramites.gub.uy</a></li>

</ul>
<div class="titulo-form">
  <h3>Trazabilidad</h3>
</div>
<form class="ajaxForm" action="<?=site_url('backend/trazabilidad/editar_form/'.$proceso->id)?>" method="POST">
  <div class="validacion validacion-error"></div>
  <input type="hidden" value="<?= $proceso->ProcesoTrazabilidad->proceso_id ?>" name="proceso_id" />
  <div class="form-horizontal">
    <div class="control-group">
      <label for="organismo" class="control-label">ID de organismo</label>
      <div class="controls">
        <input class="input-xlarge" id="organismo" type="text" value="<?= $proceso->ProcesoTrazabilidad->organismo_id ?>" name="organismo_id" />
      </div>
    </div>
    

    <div class="control-group">
      <div class="controls">
        <label class="checkbox" for="envio_guid_automatico">
          <input type="checkbox" id="envio_guid_automatico" name="envio_guid_automatico">
          ¿Envío de GUID automático?
          <span id="no_config_cuenta" style="display:none;color: #9c9c9c;display: block;">(Opción no configurada para esta cuenta)<span>
        </label>
      </div>
    </div>

    <div class="control-group"  id="envio_guid_automatico_div">
      <label for="email_envio_guid" class="control-label">Variable con e-mail para envío de GUID</label>
      <div class="controls">
        <?php if($proceso->ProcesoTrazabilidad->email_envio_guid):?>
          <input id="email_envio_guid" type="text" name="email_envio_guid" value="<?= $proceso->ProcesoTrazabilidad->email_envio_guid ?>">
        <?php endif ?>

        <?php if(!$proceso->ProcesoTrazabilidad->email_envio_guid && $proceso->Tareas[0]->acceso_modo === 'registrados'):?>
          <input id="email_envio_guid" type="text" name="email_envio_guid" value="@!email">
        <?php endif ?>

        <?php if(!$proceso->ProcesoTrazabilidad->email_envio_guid && $proceso->Tareas[0]->acceso_modo !== 'registrados'):?>
          <input id="email_envio_guid" type="text" name="email_envio_guid" value="">
        <?php endif ?>

      </div>
    </div>

    <div class="control-group">
      <span class="control-label"></span>
      <div class="controls">
        <input class="btn btn-primary" type="submit" value="Guardar" />
      </div>
    </div>
  </div>
</form>

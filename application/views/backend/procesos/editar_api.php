<script>
$(document).ready(function() {

    $('#instanciar_api').change(function() {
        if(this.checked) {
          $(this).attr('value', '1');
        }
        else {
          $(this).attr('value', '0');
        }
    });

    <?php if($proceso->instanciar_api): ?>
        $("#instanciar_api").attr('checked', true);
    <?php else: ?>
      $("#instanciar_api").attr('checked', false);
    <?php endif ?>
});
</script>

<ul class="breadcrumb">
  <li>
      <a href="<?= site_url('backend/procesos') ?>">Listado de Procesos</a> <span class="divider">/</span>
  </li>
  <li class="active"><?= $proceso->nombre ?></li>
</ul>
<?php $this->load->view('backend/proceso_descripcion') ?>
<ul class="nav nav-tabs">
    <li><a href="<?=site_url('backend/procesos/editar/'.$proceso->id)?>">Diseñador</a></li>
    <li><a href="<?= site_url('backend/formularios/listar/comun/' . $proceso->id) ?>">Formularios</a></li>
    <li><a href="<?= site_url('backend/formularios/listar/obn/' . $proceso->id) ?>">Formularios para Tablas de Datos</a></li>
    <li><a href="<?= site_url('backend/documentos/listar/' . $proceso->id) ?>">Documentos</a></li>
    <li ><a href="<?= site_url('backend/validaciones/listar/' . $proceso->id) ?>">Validaciones</a></li>
    <li><a href="<?=site_url('backend/acciones/listar/'.$proceso->id)?>">Acciones</a></li>
    <li><a href="<?= site_url('backend/trazabilidad/listar/' . $proceso->id) ?>">Trazabilidad</a></li>
    <li><a href="<?= site_url('backend/procesos/editar_codigo_tramite_ws_grep/' . $proceso->id) ?>">Código tramites.gub.uy</a></li>
    <li class="active"><a href="<?= site_url('backend/procesos/editar_api/' . $proceso->id) ?>">API</a></li>

</ul>
<div class="titulo-form">
  <h3>API</h3>
</div>
<form class="ajaxForm" action="<?=site_url('backend/procesos/editar_form_api/'.$proceso->id)?>" method="POST">
  <div class="validacion validacion-error"></div>
  <div class="form-horizontal">
    <div class="control-group">
      <div class="controls">
        <label class="checkbox" for="instanciar_api">
          <input type="checkbox" id="instanciar_api" name="instanciar_api">
          ¿Permitir Instanciar este proceso por la API?
      </label>
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

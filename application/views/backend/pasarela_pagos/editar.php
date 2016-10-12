<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('backend/pasarela_pagos') ?>">Pasarela de Pagos</a> <span class="divider">/</span>
    </li>
    <li class="active"><?=$pasarela->nombre ?></li>
</ul>
<h2><?=$pasarela->nombre ?></h2>
<form class="ajaxForm" action="<?=site_url('backend/pasarela_pagos/editar_form/'.$pasarela->id)?>" method="post">
  <div class="validacion validacion-error"></div>
  <fieldset>
    <legend>Datos generales</legend>
    <div class="form-horizontal">
      <div class="control-group">
        <div class="controls">
          <label class="checkbox" for="servicio_activo"><input type="checkbox" id="servicio_activo" name="activo" value="<?= $pasarela->activo ?>" <?= ($pasarela->activo == 1) ? 'checked' : ''; ?> />Activa</label>
        </div>
      </div>
      <div class="control-group">
        <label for="nombre" class="control-label">Nombre</label>
        <div class="controls">
          <input class="input-xlarge" id="nombre" type="text" value="<?= $pasarela->nombre ?>" name="nombre" /><br />
        </div>
      </div>
      <div class="control-group">
        <label for="pasarela_metodo" class="control-label">Método</label>
        <div class="controls">
          <select class="input-xlarge" id="pasarela_metodo" name="metodo">
              <option value="">-- Seleccione el método --</option>
              <option value="antel" <?=($pasarela->metodo == 'antel' ? 'selected' : '')?>>Antel</option>
          </select>
        </div>
      </div>
    </div>
  </fieldset>

  <fieldset id="pasarela_metodo_antel" class="pasarela_metodo_form <?=($pasarela->metodo == 'antel' ? '' : 'hidden')?>">
    <legend>Datos del método</legend>
    <input type="hidden" name="pasarela_metodo_antel_id" value="<?=(isset($pasarela_metodo->id) ? $pasarela_metodo->id : '')?>" />
    <div class="form-horizontal">
      <div class="control-group">
        <label for="id" class="control-label">ID de tramite</label>
        <div class="controls">
          <input type="text" id="id" name="pasarela_metodo_antel_id_tramite" class="input-xlarge" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->id_tramite : '')?>" />
          <input type="hidden" name="pasarela_metodo_antel_cantidad" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->cantidad : 1)?>" />
        </div>
      </div>
      <div class="control-group">
        <label for="tasa_1" class="control-label">Tasa 1</label>
        <div class="controls">
          <input type="text" id="tasa_1" name="pasarela_metodo_antel_tasa_1" class="input-xlarge" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->tasa_1 : '')?>" />
        </div>
      </div>
      <div class="control-group">
        <label for="tasa_2" class="control-label">Tasa 2</label>
        <div class="controls">
          <input type="text" id="tasa_2" name="pasarela_metodo_antel_tasa_2" class="input-xlarge" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->tasa_2 : '')?>" />
        </div>
      </div>
      <div class="control-group">
        <label for="tasa_3" class="control-label">Tasa 3</label>
        <div class="controls">
          <input type="text" id="tasa_3" name="pasarela_metodo_antel_tasa_3" class="input-xlarge" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->tasa_3 : '')?>" />
          <input type="hidden" name="pasarela_metodo_antel_operacion" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->operacion : 'P')?>" />
        </div>
      </div>
      <div class="control-group">
        <span class="control-label">Vencimiento (AAAA/MM/DD)</span>
        <div class="controls">
          <input type="text" id="pasarela_pago_vencimiento" name="pasarela_metodo_antel_vencimiento" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->vencimiento : '')?>" />
        </div>
      </div>
      <div class="control-group">
        <label for="desglose" class="control-label">Códigos de desglose</label>
        <div class="controls">
          <input type="text" id="desglose" name="pasarela_metodo_antel_codigos_desglose" class="input-xlarge" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->codigos_desglose : '')?>" />
        </div>
      </div>
      <div class="control-group">
        <label for="montos_desglose" class="control-label">Montos de desglose</label>
        <div class="controls">
          <input type="text" id="montos_desglose" name="pasarela_metodo_antel_montos_desglose" class="input-xlarge" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->montos_desglose : '')?>" />
        </div>
      </div>
      <div class="control-group">
        <label for="clave_organismo" class="control-label">Clave organismo</label>
        <div class="controls">
          <input type="text" id="clave_organismo" name="pasarela_metodo_antel_clave_organismo" class="input-xlarge" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->clave_organismo : '')?>" />
        </div>
      </div>
      <div class="control-group">
        <label for="clave_tramite" class="control-label">Clave trámite</label>
        <div class="controls">
          <input type="text" id="clave_tramite" name="pasarela_metodo_antel_clave_tramite" class="input-xlarge" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->clave_tramite : '')?>" />
        </div>
      </div>
      <div class="control-group">
        <label for="pasarela_metodo_antel_certificado" class="control-label">Certificado SSL</label>
        <div class="controls">
          <div id="file-uploader-certificado"></div>
          <input id="certificado" type="hidden" name="pasarela_metodo_antel_certificado" value="<?=($pdi->certificado != '' ? $pdi->certificado : '')?>" />
          <script>
              var uploader = new qq.FileUploader({
                  element: document.getElementById('file-uploader-certificado'),
                  action: site_url+'backend/uploader/credenciales_pasarela_pago',
                  onComplete: function(id,filename,respuesta){
                      $("input[name=pasarela_metodo_antel_certificado]").val(respuesta.file_name);
                  }
              });
          </script>
        </div>
      </div>
      <div class="control-group">
        <label for="pasarela_metodo_antel_clave_certificado" class="control-label">Clave privada</label>
        <div class="controls">
          <div id="file-uploader-clave"></div>
          <input id="clave_certificado" type="hidden" name="pasarela_metodo_antel_clave_certificado" value="<?=($pdi->clave_certificado != '' ? $pdi->clave_certificado : '')?>" />
          <script>
              var uploader = new qq.FileUploader({
                  element: document.getElementById('file-uploader-clave'),
                  action: site_url+'backend/uploader/credenciales_pasarela_pago',
                  onComplete: function(id,filename,respuesta){
                      $("input[name=pasarela_metodo_antel_clave_certificado]").val(respuesta.file_name);
                  }
              });
          </script>
        </div>
      </div>
      <div class="control-group">
        <label for="pasarela_metodo_antel_pass_clave_certificado" class="control-label">Contraseña de la clave privada</label>
        <div class="controls">
          <input type="text" id="pass_clave_certificado" name="pasarela_metodo_antel_pass_clave_certificado" class="input-xlarge" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->pass_clave_certificado : '')?>" />
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
                <a class="btn btn-link btn-lg" href="<?=site_url('backend/pasarela_pagos')?>">Cancelar</a>
              </li>
          </ul>
      </li>
  </ul>

</form>

<ul class="breadcrumb">
  <li>Administración <span class="divider">/</span></li>
  <li>
    <a href="<?= site_url('manager/cuentas') ?>">Cuentas</a> <span class="divider">/</span>
  </li>
  <li class="active"><?= $title ?> <?= $cuenta->nombre ?></li>
</ul>

<h2>Cuentas: <?= $cuenta->nombre ?></h2>
<form class="ajaxForm" method="post" action="<?= site_url('manager/cuentas/editar_form/' . $cuenta->id) ?>">
  <fieldset>
    <legend><?= $title ?></legend>
    <div class="form-horizontal">
      <div class="validacion validacion-error"></div>
      <div class="control-group">
        <label class="control-label" for="nombre">Nombre</label>
        <div class="controls">
          <input id="nombre" type="text" name="nombre" value="<?= $cuenta->nombre ?>"/>
          <div class="help-block">En minusculas y sin espacios.</div>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="nombre_largo">Nombre largo</label>
        <div class="controls">
          <input id="nombre_largo" class="input-xxlarge" type="text" name="nombre_largo" value="<?= $cuenta->nombre_largo ?>"/>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="mensaje">Mensaje de bienvenida (Puede contener HTML)</label>
        <div class="controls">
          <textarea id="mensaje" name="mensaje" class="input-xxlarge"><?= $cuenta->mensaje ?></textarea>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="file-uploader">Logo</label>
        <div class="controls">
          <div id="file-uploader"></div>
          <input type="hidden" name="logo" value="<?= $cuenta->logo ?>" />
          <img class="logo" src="<?= $cuenta->logo ? base_url('uploads/logos/' . $cuenta->logo) : base_url('assets/img/img.png') ?>" alt="logo" />
          <script>
              var uploader = new qq.FileUploader({
                  element: document.getElementById('file-uploader'),
                  action: site_url + 'manager/uploader/logo',
                  onComplete: function(id, filename, respuesta) {
                      $("input[name=logo]").val(respuesta.file_name);
                      $("img.logo").attr("src", base_url + "uploads/logos/" + respuesta.file_name);
                  }
              });
          </script>
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
                <a class="btn btn-link btn-lg" href="<?= site_url('manager/cuentas') ?>">Cancelar</a>
              </li>
          </ul>
      </li>
  </ul>
</form>

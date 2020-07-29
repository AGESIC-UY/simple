<h1><?= $proceso->nombre ?></h1>
<div class="validacion validacion-error" style="display: block;">
  <span class="dialog-title">Acceso denegado</span>
  <div class="alert alert-error"><p>No tiene permisos para ejecutar este trámite representando una empresa</p>
    <a href="<?= site_url() ?>">Volver</a>
  </div>
</div>

<h1>Empresas disponibles</h1>
<div class="dialogo validacion-warning">
  <div class="alert alert-alert" id="89363">
    <span class="dialogos_titulo">Existen <?php echo count($empresas); ?> empresas disponibles</span>
    <div class="dialogos_contenido">Actualmente ingresó como:
      <strong><?= UsuarioSesion::usuario()->nombres.' '.UsuarioSesion::usuario()->apellido_paterno.' '.UsuarioSesion::usuario()->apellido_materno; ?></strong>. Puede continuar como este usuario o iniciar sesión representando a otra empresa</div>
        <a href="<?=site_url('autenticacion/continuar_como_usuario_login_con_empresas')?>" class="btn btn-primary"><span class="icon-play icon-white"></span> Continuar como este usuario</a>
  </div>
</div>

  <table class="table">
    <caption class="hide-text">Empresas disponibles</caption>
    <thead>
      <tr>
        <th>Rut</th>
        <th>Razón Social</th>
        <th>Email</th>
        <th>Eres Dueño</th>
        <th class="actions">Acción</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($empresas as $empresa): ?>
        <tr>
            <td><?= $empresa->rutEmpresa ?>  </td>
            <td><?= $empresa->razonSocial ?> </td>
            <td><?= $empresa->correoElectronico ?>  </td>
            <td><?= $empresa->esDuenio == '1'? 'si':'no' ?></td>
            <td class="actions">
              <form method="post" action="<?= site_url('autenticacion/login_o_registrar_empresa') ?>">
                <input type="hidden" name="razonSocial" value="<?= $empresa->razonSocial ?>">
                <input type="hidden" name="rutEmpresa" value="<?= $empresa->rutEmpresa ?>">
                <input type="hidden" name="correoElectronico" value="<?= $empresa->correoElectronico ?>">
                <button type="submit" class="btn btn-primary"><span class="icon-play icon-white"></span> Iniciar Sesión<span class="hide-text"> como: <?= $empresa->razonSocial ?> </span></button>
              </form>
            </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>

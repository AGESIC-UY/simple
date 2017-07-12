<!DOCTYPE html>
<html lang="es">
    <head>
        <?php $this->load->view('head')?>
    </head>
    <body>
      <div class="contenedorGeneral">
        <header class="header-publico">
            <div class="container">
                <div class="row-fluid">
                    <div class="span5">
                        <div id="logo">
                          <a href="<?= site_url() ?>">
                            <img src="<?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->logoADesplegar : base_url('assets/img/logo.svg') ?>" alt="<?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->nombre_largo : 'Simple' ?>" />
                          </a>
                          <span class="nombre-app"><?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->nombre_largo : '' ?></span>
                          <!-- p><?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->mensaje : '' ?></p -->
                        </div>
                    </div>
                    <div class="span7">
                      <div class="logosSecundarios">
                        <ul class="listaHorizontal">
                          <li>
                            <a href="https://www.presidencia.gub.uy/" title="Ir al sitio de Presidencia">
                              <img src="<?= base_url() ?>assets/img/logoPresidencia.png" alt="Presidencia">
                            </a>
                          </li>
                          <li>
                            <a href="http://uruguaydigital.gub.uy/" title="Ir al sitio de Uruguay Digital">
                              <img src="<?= base_url() ?>assets/img/logo-uruguayDigital.png" alt="Uruguay Digital">
                            </a>
                          </li>
                        </ul>
                      </div>
                      <div id="userMenu" class="pull-right userMenu">
                        <?php if (!UsuarioSesion::usuario()->registrado): ?>
                            <a class="btn btn-small btn-link" href="<?= site_url('autenticacion/login') ?>">Iniciar la sesión</a>
                          <?php endif; ?>
                        <?php else: ?>
                          <?php if (UsuarioSesion::registrado_saml()): ?>
                            <span class="btn-small">Bienvenido,</span>
                            <div class="btn-group">
                              <a class="btn btn-small btn-link dropdown-toggle" data-toggle="dropdown" href="#"><span><?= UsuarioSesion::usuario()->displayName() ?></span> <span class="caret"></span></a>
                              <ul class="dropdown-menu pull-right">
                                  <li><a href="<?= site_url('cuentas/editar') ?>"><span class="icon-user"></span> Mi cuenta</a></li>
                                  <li><a href="<?= site_url('autenticacion/logout_saml') ?>"><span class="icon-off"></span> Cerrar sesión</a></li>
                              </ul>
                            </div>
                          <?php else: ?>
                            <span class="btn-small">Bienvenido,</span>
                            <div class="btn-group">
                              <a class="btn btn-small btn-link dropdown-toggle" data-toggle="dropdown" href="#"><span><?= UsuarioSesion::usuario()->displayName() ?></span> <span class="caret"></span></a>
                              <ul class="dropdown-menu pull-right">
                                  <li><a href="<?= site_url('cuentas/editar') ?>"><span class="icon-user"></span> Mi cuenta</a></li>
                                  <li><a href="<?= site_url('autenticacion/logout') ?>"><span class="icon-off"></span> Cerrar sesión</a></li>
                              </ul>
                            </div>
                          <?php endif; ?>
                        <?php endif; ?>
                      </div>
                    </div>
                </div>
            </div>
        </header>
        <div id="main" tabindex="-1">
          <div class="container">
              <div class="row-fluid">
                  <div class="span12 contenido-publico">
                      <?php $this->load->view($content) ?>
                  </div>
              </div>
          </div>
        </div>
      </div>
        <footer>
            <?php $this->load->view('foot') ?>
        </footer>
    </body>
</html>

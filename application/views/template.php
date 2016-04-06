<!DOCTYPE html>
<html lang="es">
    <head>
        <?php $this->load->view('head') ?>
    </head>
    <body>
      <ul id="skip">
        <li><a href="#main">Ir al contenido</a></li>
        <li><a href="#sideMenu">Ir al menú de navegación</a></li>
      </ul>
      <div class="contenedorGeneral">
        <header class="header-publico">
            <div class="container">
                <div class="row-fluid">
                    <div class="span5">
                        <div id="logo">
                          <a href="<?= site_url() ?>">
                            <img src="<?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->logoADesplegar : base_url('assets/img/logo.svg') ?>" alt="<?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->nombre_largo : 'Simple' ?>" />
                          </a>
                          <h1><?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->nombre_largo : '' ?></h1>
                          <p><?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->mensaje : '' ?></p>
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
                            <a class="btn btn-small btn-link" href="<?= site_url('autenticacion/login_saml') ?>">Iniciar la sesión</a>
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
                    <div class="span3">
                        <ul id="sideMenu" class="nav nav-list" tabindex="-1">
                            <li class="iniciar <?= isset($sidebar) && $sidebar == 'disponibles' ? 'active' : '' ?>"><a href="<?= site_url('tramites/disponibles') ?>">Iniciar trámite</a></li>
                            <?php if (UsuarioSesion::usuario()->registrado): ?>
                                <?php
                                $npendientes=Doctrine::getTable('Etapa')->findPendientes(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio())->count();
                                $nsinasignar=Doctrine::getTable('Etapa')->findSinAsignar(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio())->count();
                                $nparticipados=Doctrine::getTable('Tramite')->findParticipados(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio())->count();
                                ?>
                                <li class="<?= isset($sidebar) && $sidebar == 'inbox' ? 'active' : '' ?>"><a href="<?= site_url('etapas/inbox') ?>">Bandeja de Entrada (<?= $npendientes ?>)</a></li>
                                <?php if($nsinasignar): ?><li class="<?= isset($sidebar) && $sidebar == 'sinasignar' ? 'active' : '' ?>"><a href="<?= site_url('etapas/sinasignar') ?>">Sin asignar  (<?=$nsinasignar  ?>)</a></li><?php endif ?>
                                <li class="<?= isset($sidebar) && $sidebar == 'participados' ? 'active' : '' ?>"><a href="<?= site_url('tramites/participados') ?>">Participados  (<?= $nparticipados ?>)</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="span9 contenido-publico">
                        <?php $this->load->view('messages') ?>
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

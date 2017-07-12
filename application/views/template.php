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
                          <?php elseif(UsuarioSesion::registrado_ldap()): ?>
                            <span class="btn-small">Bienvenido,</span>
                            <div class="btn-group">
                              <a class="btn btn-small btn-link dropdown-toggle" data-toggle="dropdown" href="#"><span><?= UsuarioSesion::usuario()->displayName() ?></span> <span class="caret"></span></a>
                              <ul class="dropdown-menu pull-right">
                                  <li><a href="<?= site_url('cuentas/editar') ?>"><span class="icon-user"></span> Mi cuenta</a></li>
                                  <li><a href="<?= site_url('autenticacion/logout') ?>"><span class="icon-off"></span> Cerrar sesión</a></li>
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
                            <li class="iniciar <?= isset($sidebar) && $sidebar == 'disponibles' ? 'active' : '' ?>"><a href="<?= site_url('tramites/disponibles') ?>">Listado de trámites</a></li>
                            <?php if (UsuarioSesion::usuario()->registrado): ?>
                                <?php
                                $npendientes=Doctrine::getTable('Etapa')->cantidadPendientes(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio());
                                $nsinasignar=Doctrine::getTable('Etapa')->cantidadSinAsignar(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio());
                                $nparticipados=Doctrine::getTable('Tramite')->cantidadParticipados(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio());
                                ?>
                                <li class="<?= isset($sidebar) && $sidebar == 'inbox' ? 'active' : '' ?>"><a href="<?= site_url('etapas/inbox?orderby=updated_at&direction=desc') ?>">Bandeja de entrada (<?= $npendientes ?>)</a></li>
                                <li class="<?= isset($sidebar) && $sidebar == 'sinasignar' ? 'active' : '' ?>"><a href="<?= site_url('etapas/sinasignar') ?>">Sin asignar (<?= $nsinasignar ?>)</a></li>
                                <li class="<?= isset($sidebar) && $sidebar == 'participados' ? 'active' : '' ?>"><a href="<?= site_url('tramites/participados?orderby=updated_at&direction=desc') ?>">Mis trámites  (<?= $nparticipados ?>)</a></li>
                                <?php if(UsuarioSesion::usuario()->acceso_reportes && UsuarioSesion::usuario()->cuenta_id): ?>
                                  <li class="<?= isset($sidebar) && $sidebar == 'reportes' ? 'active' : '' ?>"><a href="<?= site_url('tramites/reportes_procesos') ?>">Reportes de trámites</a></li>
                                <?php endif; ?>
                                <?php if(UsuarioSesion::usuarioMesaDeEntrada()): ?>
                                  <li class="<?= isset($sidebar) && $sidebar == 'busqueda_ciudadano' ? 'active' : '' ?>"><a href="<?= site_url('etapas/busqueda_ciudadano') ?>">Trámites de Ciudadano</a></li>
                                <?php endif; ?>
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

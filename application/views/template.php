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
                                    <img src="<?= Cuenta::cuentaSegunDominio() != 'localhost' ? Cuenta::cuentaSegunDominio()->logoADesplegar : base_url('assets/img/logo.svg') ?>" alt="<?= Cuenta::cuentaSegunDominio() != 'localhost' ? Cuenta::cuentaSegunDominio()->nombre_largo : 'Simple' ?>" />
                                </a>
                                <span class="nombre-app"><?= Cuenta::cuentaSegunDominio() != 'localhost' ? Cuenta::cuentaSegunDominio()->nombre_largo : '' ?></span>
                                <!-- p><?= Cuenta::cuentaSegunDominio() != 'localhost' ? Cuenta::cuentaSegunDominio()->mensaje : '' ?></p -->
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
                                </ul>
                            </div>
                            <div id="userMenu" class="pull-right userMenu">
                                <?php if (!UsuarioSesion::usuario()->registrado): ?>
                                    <a class="btn btn-small btn-link" href="<?= site_url('autenticacion/login') ?>">Iniciar la sesión</a>
                                <?php else: ?>
                                    <?php if (UsuarioSesion::registrado_saml()): ?>
                                        <?php if (UsuarioSesion::usuario_actuando_como_empresa()): ?>
                                            <span class="info-usuario"><span class="icn icn-circle-info"></span>
                                                <span class="btn-small">Representante:
                                                    <?php $usuario_real_empresa = Doctrine::getTable('Usuario')->find(UsuarioSesion::usuario_actuando_como_empresa()); ?>
                                                    <?= $usuario_real_empresa->nombres ?>
                                                    <a href="<?= site_url('autenticacion/regresar_usuario_real_grep') ?>" title="Ingresar como <?= $usuario_real_empresa->nombres . ' ' . $usuario_real_empresa->apellido_paterno . ' ' . $usuario_real_empresa->apellido_materno ?>"><i class="icon-share-alt"></i> </a>
                                                </span>
                                            </span>
                                        <?php endif ?>
                                        <span class="btn-small">Bienvenido,</span>
                                        <div class="btn-group">
                                            <a class="btn btn-small btn-link dropdown-toggle" data-toggle="dropdown" href="#"><span><?= UsuarioSesion::usuario()->displayName() ?></span> <span class="caret"></span></a>
                                            <ul class="dropdown-menu pull-right">
                                                <li><a href="<?= site_url('cuentas/editar') ?>"><span class="icon-user"></span> Mi cuenta</a></li>
                                                <li><a href="<?= site_url('autenticacion/logout_saml') ?>"><span class="icon-off"></span> Cerrar sesión</a></li>
                                            </ul>
                                        </div>
                                    <?php elseif (UsuarioSesion::registrado_ldap()): ?>
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
                        <?php if (UsuarioSesion::usuario_con_empresas_luego_login()): ?>
                            <div class="span3">
                                <ul id="sideMenu" class="nav nav-list inactiva" tabindex="-1">
                                    <li class="iniciar <?= isset($sidebar) && $sidebar == 'disponibles' ? 'active' : '' ?>">Listado de trámites</li>
                                    <?php if (UsuarioSesion::usuario()->registrado): ?>
                                        <?php
                                        $npendientes = Doctrine::getTable('Etapa')->cantidadPendientes(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio());
                                        $nsinasignar = Doctrine::getTable('Etapa')->cantidadSinAsignar(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio());
                                        $nparticipados = Doctrine::getTable('Tramite')->cantidadParticipados(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio());
                                        ?>
                                        <li class="<?= isset($sidebar) && $sidebar == 'inbox' ? 'active' : '' ?>">Bandeja de entrada (<?= $npendientes ?>)</li>
                                        <li class="<?= isset($sidebar) && $sidebar == 'sinasignar' ? 'active' : '' ?>">Sin asignar (<?= $nsinasignar ?>)</li>
                                        <li class="<?= isset($sidebar) && $sidebar == 'participados' ? 'active' : '' ?>">Mis trámites  (<?= $nparticipados ?>)</li>
                                        <?php if (UsuarioSesion::usuario()->acceso_reportes && UsuarioSesion::usuario()->cuenta_id): ?>
                                            <li class="<?= isset($sidebar) && $sidebar == 'reportes' ? 'active' : '' ?>">Reportes de trámites</li>
                                        <?php endif; ?>
                                        <?php if (UsuarioSesion::usuarioMesaDeEntrada()): ?>
                                            <li class="<?= isset($sidebar) && $sidebar == 'busqueda_ciudadano' ? 'active' : '' ?>">Trámites de Ciudadano</li>
                                        <?php endif; ?>
                                        <?php if (UsuarioSesion::lista_empresas_usuario()): ?>
                                            <li class="<?= isset($sidebar) && $sidebar == 'empresas_usuario' ? 'active' : '' ?>"><a href="<?= site_url('autenticacion/login_empresa') ?>">Empresas Disponibles</a></li>
                                        <?php endif; ?>
                                        <?php if (UsuarioSesion::usuarioFirmaPorLotes()): ?>
                                            <li class="<?= isset($sidebar) && $sidebar == 'firma_por_lotes' ? 'active' : '' ?>"><a href="<?= site_url('etapas/firma_por_lotes') ?>">Firma Por Lotes</a></li>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div class="span3">
                                <ul id="sideMenu" class="nav nav-list" tabindex="-1">
                                    <li class="iniciar <?= isset($sidebar) && $sidebar == 'disponibles' ? 'active' : '' ?>"><a href="<?= site_url('tramites/disponibles') ?>">Listado de trámites</a></li>
                                    <?php if (UsuarioSesion::usuario()->registrado): ?>
                                        <?php
                                        $npendientes = Doctrine::getTable('Etapa')->cantidadPendientes(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio());
                                        $nsinasignar = Doctrine::getTable('Etapa')->cantidadSinAsignar(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio());
                                        $nparticipados = Doctrine::getTable('Tramite')->cantidadParticipados(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio());
                                        ?>
                                        <li class="<?= isset($sidebar) && $sidebar == 'inbox' ? 'active' : '' ?>"><a href="<?= site_url('etapas/inbox?orderby=updated_at&direction=desc') ?>">Bandeja de entrada (<?= $npendientes ?>)</a></li>
                                        <li class="<?= isset($sidebar) && $sidebar == 'sinasignar' ? 'active' : '' ?>"><a href="<?= site_url('etapas/sinasignar') ?>">Sin asignar (<?= $nsinasignar ?>)</a></li>
                                        <li class="<?= isset($sidebar) && $sidebar == 'participados' ? 'active' : '' ?>"><a href="<?= site_url('tramites/participados?orderby=updated_at&direction=desc') ?>">Mis trámites  (<?= $nparticipados ?>)</a></li>
                                        <?php if (UsuarioSesion::usuario()->acceso_reportes && UsuarioSesion::usuario()->cuenta_id): ?>
                                            <li class="<?= isset($sidebar) && $sidebar == 'reportes' ? 'active' : '' ?>"><a href="<?= site_url('tramites/reportes_procesos') ?>">Reportes de trámites</a></li>
                                        <?php endif; ?>
                                        <?php if (UsuarioSesion::usuarioMesaDeEntrada()): ?>
                                            <li class="<?= isset($sidebar) && $sidebar == 'busqueda_ciudadano' ? 'active' : '' ?>"><a href="<?= site_url('etapas/busqueda_ciudadano') ?>">Trámites de Ciudadano</a></li>
                                        <?php endif; ?>
                                        <?php if (UsuarioSesion::lista_empresas_usuario() || UsuarioSesion::usuario_actuando_como_empresa()): ?>
                                            <li class="<?= isset($sidebar) && $sidebar == 'empresas_usuario' ? 'active' : '' ?>"><a href="<?= site_url('autenticacion/login_empresa') ?>">Empresas Disponibles</a></li>
                                        <?php endif; ?>
                                        <?php if (UsuarioSesion::usuarioFirmaPorLotes()): ?>
                                            <li class="<?= isset($sidebar) && $sidebar == 'firma_por_lotes' ? 'active' : '' ?>"><a href="<?= site_url('etapas/firma_por_lotes') ?>">Firma por lotes</a></li>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
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

<!DOCTYPE html>
<html lang="es">
    <head>
        <?php $this->load->view('backend/head') ?>


    </head>
    <!--[if lt IE 9]>
    <body class="ie_support">
    <![endif]-->
    <!--[if (gte IE 9)|!(IE)] -->
    <body>
        <!--<![endif]-->
        <ul id="skip">
            <li><a href="#main">Ir al contenido</a></li>
            <li><a href="#menu">Ir al menú de navegación</a></li>
        </ul>
        <div class="contenedorGeneral">
            <header>
                <div class="container-fluid">
                    <div class="row-fluid">
                        <div class="span7">
                            <div id="logo">
                                <a href="<?= site_url('backend/portada') ?>">
                                    <img src="<?= Cuenta::cuentaSegunDominio() != 'localhost' ? Cuenta::cuentaSegunDominio()->logoADesplegar : base_url('assets/img/logo-tramites.png') ?>" alt="Tramitador" />
                                </a>
                                <h1><?= Cuenta::cuentaSegunDominio() != 'localhost' ? Cuenta::cuentaSegunDominio()->nombre_largo : '' ?></h1>
                            </div>
                        </div>
                        <div class="span5">
                            <div class="logosSecundarios">
                                <ul class="listaHorizontal">
                                    <li>
                                        <a href="https://www.presidencia.gub.uy/" title="Ir al sitio de Presidencia">
                                            <img src="<?= base_url() ?>assets/img/logoPresidencia.png" alt="Presidencia">
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <?php if (UsuarioBackendSesion::registrado_saml()): ?>
                                <div class="pull-right userMenu" id="userMenu">
                                    <span class="btn-small">Bienvenido,</span>
                                    <div class="btn-group">
                                        <a class="btn btn-small btn-link dropdown-toggle" data-toggle="dropdown" href="#"><span><?= UsuarioBackendSesion::usuario()->email ?></span> <span class="caret"></span></a>
                                        <ul class="dropdown-menu pull-right">
                                            <li><a href="<?= site_url('backend/cuentas') ?>"><span class="icon-user"></span> Mi Cuenta</a></li>
                                            <?php if (strtoupper(TIPO_DE_AUTENTICACION) == 'CDA'): ?>
                                                <li><a href="<?= site_url('autenticacion/logout_saml') ?>"><span class="icon-off"></span> Cerrar sesión</a></li>
                                            <?php elseif (strtoupper(TIPO_DE_AUTENTICACION) == 'LDAP'): ?>
                                                <li><a href="<?= site_url('backend/autenticacion/logout_ldap') ?>"><span class="icon-off"></span> Cerrar sesión</a></li>
                                            <?php else: ?>
                                                <li><a href="<?= site_url('backend/autenticacion/logout') ?>"><span class="icon-off"></span> Cerrar sesión</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="pull-right userMenu" id="userMenu">
                                    <span class="btn-small">Bienvenido,</span>
                                    <div class="btn-group">
                                        <a class="btn btn-small btn-link dropdown-toggle" data-toggle="dropdown" href="#"><span><?= UsuarioBackendSesion::usuario()->email ?></span> <span class="caret"></span></a>
                                        <ul class="dropdown-menu pull-right">
                                            <li><a href="<?= site_url('backend/cuentas') ?>"><span class="icon-user"></span> Mi Cuenta</a></li>
                                            <li><a href="<?= site_url('backend/autenticacion/logout') ?>"><span class="icon-off"></span> Cerrar sesión</a></li>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="container-menu">
                    <div class="container-fluid">
                        <div class="navbar">
                            <ul id="menu1" class="nav" tabindex="-1">
                                <?php if (UsuarioBackendSesion::has_rol('super') || UsuarioBackendSesion::has_rol('gestion')): ?>
                                    <li <?= $this->uri->segment(2) == 'gestion' || !$this->uri->segment(2) ? 'class="active"' : '' ?>><a href="<?= site_url('backend/gestion') ?>">Inicio</a></li>
                                <?php endif ?>
                                <?php if (UsuarioBackendSesion::has_rol('super') || UsuarioBackendSesion::has_rol('modelamiento')): ?>
                                    <li <?=
                                    $this->uri->segment(2) == 'procesos' ||
                                    $this->uri->segment(2) == 'formularios' ||
                                    $this->uri->segment(2) == 'documentos' ||
                                    $this->uri->segment(2) == 'validaciones' ||
                                    $this->uri->segment(2) == 'acciones' ||
                                    $this->uri->segment(2) == 'trazabilidad' ||
                                    $this->uri->segment(2) == 'acciones' ||
                                    $this->uri->segment(3) == 'editar_codigo_tramite_ws_grep' ||
                                    $this->uri->segment(3) == 'editar_api' ? 'class="active"' : ''
                                    ?>><a href="<?= site_url('backend/procesos') ?>">Modelador de Procesos</a></li>
                                    <?php endif ?>
                                    <?php if (UsuarioBackendSesion::has_rol('super') || UsuarioBackendSesion::has_rol('ws_catalogos')): ?>
                                    <li <?= $this->uri->segment(2) == 'ws_catalogos' ? 'class="active"' : '' ?>><a href="<?= site_url('backend/ws_catalogos') ?>">Catálogo de Servicios</a></li>
                                <?php endif ?>
                                <?php if (UsuarioBackendSesion::has_rol('super') || UsuarioBackendSesion::has_rol('bloques')): ?>
                                    <li <?= $this->uri->segment(2) == 'bloques' ? 'class="active"' : '' ?>><a href="<?= site_url('backend/bloques') ?>">Catálogo de Bloques</a></li>
                                <?php endif ?>
                                <?php if (UsuarioBackendSesion::has_rol('super') || UsuarioBackendSesion::has_rol('modelamiento')): ?>
                                    <li <?= $this->uri->segment(2) == 'obns' ? 'class="active"' : '' ?>><a href="<?= site_url('backend/obns/listar') ?>">Objetos de Negocios</a></li>
                                <?php endif ?>
                                <?php if (UsuarioBackendSesion::has_rol('super') || UsuarioBackendSesion::has_rol('modelamiento')): ?>
                                    <li <?= $this->uri->segment(2) == 'pasarela_pagos' ? 'class="active"' : '' ?>><a href="<?= site_url('backend/pasarela_pagos') ?>">Pasarela de Pagos</a></li>
                                <?php endif ?>                                
                                <?php if (UsuarioBackendSesion::has_rol('super') || UsuarioBackendSesion::has_rol('operacion') || UsuarioBackendSesion::has_rol('seguimiento')): ?>
                                    <li <?= $this->uri->segment(2) == 'auditorias' || $this->uri->segment(2) == 'seguimiento' || !$this->uri->segment(2) ? 'class="dropdown active"' : 'class="dropdown"' ?>>
                                        <a href="#">Seguimiento</a>
                                        <ul class="dropdown-menu">
                                            <li><a href="<?= site_url('backend/seguimiento') ?>">Procesos</a></li>
                                            <li><a href="<?= site_url('backend/seguimiento/pagos/') ?>">Pagos</a></li>
                                            <?php if (UsuarioBackendSesion::has_rol('super') || (UsuarioBackendSesion::has_rol('seguimiento') && UsuarioBackendSesion::isAuditor())): ?>
                                                <li class="dropdown-submenu"><a href="#">Auditoría</a>
                                                    <ul class="dropdown-menu">
                                                        <li><a href="<?= site_url('backend/auditorias/procesos') ?>">Procesos</a></li>
                                                        <li><a href="<?= site_url('backend/auditorias/servicios') ?>">Catálogo de servicios</a></li>
                                                        <li><a href="<?= site_url('backend/auditorias/bloques') ?>">Catálogo de bloques</a></li>
                                                        <li><a href="<?= site_url('backend/auditorias/obnstr') ?>">Objetos de Negocios</a></li>
                                                        <li><a href="<?= site_url('backend/auditorias/pasarelas') ?>">Pasarela de pagos</a></li>
                                                        <li><a href="<?= site_url('backend/auditorias/reportes') ?>">Reportes por procesos</a></li>
                                                        <li><a href="<?= site_url('backend/auditorias/usuarios') ?>">Usuarios frontend</a></li>
                                                        <li><a href="<?= site_url('backend/auditorias/backend') ?>">Usuarios backend</a></li>
                                                        <li><a href="<?= site_url('backend/auditorias/grupos') ?>">Grupos de usuarios</a></li>
                                                    </ul>
                                                </li>
                                            <?php endif ?>
                                        </ul>
                                    </li>
                                <?php endif ?>
                                <?php if (UsuarioBackendSesion::has_rol('super') || UsuarioBackendSesion::has_rol('gestion')): ?>
                                    <li <?= $this->uri->segment(2) == 'reportes' || !$this->uri->segment(2) ? 'class="dropdown active"' : 'class="dropdown"' ?>>
                                        <a href="#">Reportes</a>
                                        <ul class="dropdown-menu">
                                            <li><a href="<?= site_url('backend/reportes') ?>">Reportes por procesos</a></li>
                                            <li><a href="<?= site_url('backend/reportes/reporte_usuario') ?>">Reporte de Usuario</a></li>
                                        </ul>
                                    </li>
                                <?php endif ?>
                                <?php if (UsuarioBackendSesion::has_rol('super') || UsuarioBackendSesion::has_rol('desarrollo')): ?>
                                    <li <?= $this->uri->segment(2) == 'api' || !$this->uri->segment(2) ? 'class="active"' : '' ?>><a href="<?= site_url('backend/api') ?>">API</a></li>
                                <?php endif ?>
                                <?php if (UsuarioBackendSesion::has_rol('configuracion') || UsuarioBackendSesion::has_rol('super')): ?>
                                    <li <?= $this->uri->segment(2) == 'configuracion' ? 'class="active"' : '' ?>><a href="<?= site_url('backend/configuracion') ?>">Configuración</a></li>
                                <?php endif ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>
            <div id="main" tabindex="-1">
                <div class="container-fluid"><?php $this->load->view($content) ?></div>
            </div>
        </div>
        <footer>
            <?php $this->load->view('backend/foot') ?>
        </footer>

        <script type="text/javascript">
            $(document).ready(function () {
                // Muestra y oculta los menús
                $('#menu li:has(ul)').hover(
                        function (e)
                        {
                            $(this).find('ul').slideDown('fast');
                        },
                        function (e)
                        {
                            $(this).find('ul').slideUp('fast');
                        }
                );
            });
        </script>
    </body>
</html>

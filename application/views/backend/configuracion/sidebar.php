<ul class="nav nav-list" id="sideMenu">
    <li class="nav-header">
        General
    </li>
    <li class="<?=strpos($this->uri->segment(3),'misitio')===0?'active':''?>">
        <a href="<?=site_url('backend/configuracion/misitio')?>">Cuenta</a>
    </li>
    <li class="<?=strpos($this->uri->segment(3),'correo')===0?'active':''?>">
        <a href="<?=site_url('backend/configuracion/correo')?>">Correo electrónico</a>
    </li>
    <li class="<?=strpos($this->uri->segment(3),'parametros')===0?'active':''?>">
        <a href="<?=site_url('backend/configuracion/parametros')?>">Parámetros</a>
    </li>
    <li class="nav-header">
        Accesos Frontend
    </li>
    <li class="<?=strpos($this->uri->segment(3),'usuario')===0?'active':''?>">
        <a href="<?=site_url('backend/configuracion/usuarios')?>">Usuarios Frontend</a>
    </li>
    <li class="<?=strpos($this->uri->segment(3),'grupo')===0?'active':''?>">
        <a href="<?=site_url('backend/configuracion/grupos_usuarios')?>">Grupos de Usuarios</a>
    </li>
    <li class="nav-header">
        Accesos Backend
    </li>
    <li class="<?=strpos($this->uri->segment(3),'backend_usuario')===0?'active':''?>">
        <a href="<?=site_url('backend/configuracion/backend_usuarios')?>">Usuarios Backend</a>
    </li>
    <li class="nav-header">
        Plataforma de interoperabilidad
    </li>
    <li class="<?=strpos($this->uri->segment(3),'pdi')===0?'active':''?>">
        <a href="<?=site_url('backend/configuracion/pdi')?>">Configuración</a>
    </li>
</ul>

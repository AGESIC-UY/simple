<ul class="nav nav-list" id="sideMenu">
    <li class="nav-header">
        General
    </li>
    <li class="<?=strpos($this->uri->segment(3),'misitio')===0?'active':''?>">
        <a href="<?=site_url('backend/configuracion/misitio')?>">Mi sitio</a>
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

</ul>

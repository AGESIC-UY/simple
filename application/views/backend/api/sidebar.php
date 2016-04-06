<div>
    <ul class="nav nav-list" id="sideMenu">
        <li class="<?=$this->uri->segment(3)==''?'active':''?>"><a href="<?= site_url('backend/api') ?>">Introducción</a></li>
        <li class="nav-header">Autorización</li>
        <li class="<?=$this->uri->segment(3)=='token'?'active':''?>"><a href="<?= site_url('backend/api/token') ?>">Código de Acceso</a></li>
        <li class="nav-header">Tramites</li>
        <li class="<?=$this->uri->segment(3)=='tramites_obtener'?'active':''?>"><a href="<?= site_url('backend/api/tramites_obtener') ?>">obtener</a></li>
        <li class="<?=$this->uri->segment(3)=='tramites_listar'?'active':''?>"><a href="<?= site_url('backend/api/tramites_listar') ?>">listar</a></li>
        <li class="<?=$this->uri->segment(3)=='tramites_listarporproceso'?'active':''?>"><a href="<?= site_url('backend/api/tramites_listarporproceso') ?>">listarPorProceso</a></li>
        <li class="nav-header">Procesos</li>
        <li class="<?=$this->uri->segment(3)=='procesos_obtener'?'active':''?>"><a href="<?= site_url('backend/api/procesos_obtener') ?>">obtener</a></li>
        <li class="<?=$this->uri->segment(3)=='procesos_listar'?'active':''?>"><a href="<?= site_url('backend/api/procesos_listar') ?>">listar</a></li>
    </ul>
</div>

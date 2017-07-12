<div>
  <ul class="nav nav-list" id="sideMenu">
    <li class="<?=$this->uri->segment(3)==''?'active':''?>"><a href="<?= site_url('backend/api') ?>">Introducción</a></li>
    <li class="nav-header">Autorización</li>
    <li class="<?=$this->uri->segment(3)=='token'?'active':''?>"><a href="<?= site_url('backend/api/token') ?>">Código de Acceso</a></li>
    <li class="nav-header">Tramites</li>
    <li class="<?=$this->uri->segment(3)=='tramites_obtener'?'active':''?>"><a href="<?= site_url('backend/api/tramites_obtener') ?>">Obtener trámites</a></li>
    <li class="<?=$this->uri->segment(3)=='tramites_listar'?'active':''?>"><a href="<?= site_url('backend/api/tramites_listar') ?>">Listar trámites</a></li>
    <li class="<?=$this->uri->segment(3)=='tramites_listarporproceso'?'active':''?>"><a href="<?= site_url('backend/api/tramites_listarporproceso') ?>">Listar por proceso</a></li>
    <li class="<?=$this->uri->segment(3)=='tramites_ejecutar'?'active':''?>"><a href="<?= site_url('backend/api/tramites_ejecutar') ?>">Ejecutar tarea</a></li>

    <li class="nav-header">Procesos</li>
    <li class="<?=$this->uri->segment(3)=='procesos_obtener'?'active':''?>"><a href="<?= site_url('backend/api/procesos_obtener') ?>">Obtener procesos</a></li>
    <li class="<?=$this->uri->segment(3)=='procesos_listar'?'active':''?>"><a href="<?= site_url('backend/api/procesos_listar') ?>">Listar procesos</a></li>
  </ul>
</div>

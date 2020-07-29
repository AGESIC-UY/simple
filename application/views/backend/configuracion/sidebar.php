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
        Trazabilidad
    </li>
    <li class="<?=strpos($this->uri->segment(3),'trazabildiad_envio_guid')===0?'active':''?>">
      <a href="<?=site_url('backend/configuracion/trazabildiad_envio_guid')?>">Envío de GUID</a>
    </li>
    <li class="<?=strpos($this->uri->segment(3),'etiquetas')===0?'active':''?>">
        <a href="<?=site_url('backend/configuracion/etiquetas')?>">Etiquetas de Trazabilidad</a>
    </li>
    <li class="<?=strpos($this->uri->segment(3),'involucrado')===0?'active':''?>">
        <a href="<?=site_url('backend/configuracion/involucrado')?>">Involucrado</a>
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
    <li class="nav-header">
        Monitoreo
    </li>
    <li class="<?=strpos($this->uri->segment(3),'monitoreo_trazabilidad_ajax')===0?'active':''?>">
      <a href="<?=site_url('backend/configuracion/monitoreo_trazabilidad_ajax')?>">Trazabilidad</a>
    </li>
    <li class="<?=strpos($this->uri->segment(3),'monitoreo_estado_soap_pdi')===0?'active':''?>">
      <a href="<?=site_url('backend/configuracion/monitoreo_estado_soap_pdi')?>">Servicios SOAP y PDI</a>
    </li>
    <li class="<?=strpos($this->uri->segment(3),'monitoreo_pasarelas_ajax')===0?'active':''?>">
      <a href="<?=site_url('backend/configuracion/monitoreo_pasarelas_ajax')?>">Pasarela de pagos</a>
    </li>
    <li class="<?=strpos($this->uri->segment(3),'monitoreo_notificaciones')===0?'active':''?>">
      <a href="<?=site_url('backend/configuracion/monitoreo_notificaciones')?>">Envio de  Notificaciones</a>
    </li>
</ul>

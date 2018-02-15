<script src="<?= base_url() ?>assets/js/modelador-acciones.js" type="text/javascript"></script>

<ul class="breadcrumb">
    <li>
        <a href="<?=site_url('backend/reportes')?>">Gestión</a> <span class="divider">/</span>
    </li>
    <li class="active"><?=$proceso->nombre?></li>
</ul>
<h2>Gestión de <?=$proceso->nombre?></h2>
<div class="acciones-generales">
  <a class="btn btn-success" href="<?=site_url('backend/reportes/crear/'.$proceso->id)?>"><span class="icon-file"></span> Nuevo</a>
</div>

<table class="table">
  <caption class="hide-text">Reportes</caption>
    <thead>
        <tr>
            <th>Reporte</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($reportes as $p): ?>
        <tr>
            <td><?=$p->nombre?></td>
            <td class="actions">
              <?php if($p->tipo == 'completo') {
                ?>
                <a href="#" class="btn btn-primary solicita_filtro" data-reporte="<?=$p->id?>"><span class="icon-eye-open icon-white"></span> Ver<span class="hidden-accessible"> <?=$p->nombre?></span></a>
                <?php
              }
              else {
                ?>
                <a href="#" class="btn btn-primary solicita_filtro_basico" data-reporte="<?=$p->id?>"><span class="icon-eye-open icon-white"></span> Ver<span class="hidden-accessible"> <?=$p->nombre?></span></a>
                <?php
              }
              ?>
                <a href="<?=site_url('backend/reportes/editar/'.$p->id)?>" class="btn btn-primary"><span class="icon-edit icon-white"></span> Editar<span class="hidden-accessible"> <?=$p->nombre?></span></a>
                <a href="<?=site_url('backend/reportes/eliminar/'.$p->id)?>" class="btn btn-danger" onclick="return confirm('¿Esta seguro que desea eliminar?')"><span class="icon-trash icon-white"></span> Eliminar<span class="hidden-accessible"> <?=$p->nombre?></span></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="modal hide fade" id="modal_filtro_basico">
  <div class="modal-header">
      <button class="close" data-dismiss="modal">×</button>
      <h3>Filtros</h3>
  </div>
  <form id="formAgregarFiltrosReporteBasico" class="ajaxForm form-horizontal" method="GET" action="#">
    <div class="modal-body">
      <div class="control-group" id="filtro_fechas_basico">
        <input type="hidden" id="filtro_reporte_id_basico" />
        <div class="">
          <label for="filtro_fechas_basico" class="">Fecha de inicio</label>
          <input class="datepicker" type="text" id="filtro_desde_basico" name="filtro_desde_basico" placeholder="Desde" />
          <input class="datepicker" type="text" id="filtro_hasta_basico" name="filtro_hasta_basico" placeholder="Hasta" />
          <span class="mensaje_error_campo" id="mensaje_fechas_invalidas" style="display:none;">El rango de fechas es incorrecto</span>
          <span class="mensaje_error_campo" id="mensaje_fechas_requeridas" style="display:none;">Fecha desde y hasta son campos obligatorios</span>
        </div>
      </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="generar_reporte_basico">Generar</button>
        <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
    </div>
  </form>
</div>

<div class="modal hide fade" id="modal_email_basico">
  <div class="modal-header">
      <button class="close" data-dismiss="modal">×</button>
      <h3>Reporte demasiado grande</h3>
  </div>
  <form id="formReporteEmailBasico" class="ajaxForm form-horizontal" method="GET" action="#">
    <div class="modal-body">
      <div class="control-group">
        <p>
            El reporte a generar excede el tamaño máximo definido. Si indica un correo electrónico se generará en segundo plano y una vez finalizado será enviado a la casilla indicada.
        </p>

      </div>

      <div class="control-group" id="email">
        <div class="">
          <label for="email_text_basico" class="">Correo Electrónico</label>
          <input class="" type="text" id="email_text_basico" name="email_text_basico" placeholder="Correo Electrónico" />
        </div>
      </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="generar_reporte_basico_email">Enviar</button>
        <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
    </div>
  </form>
</div>

<div class="modal hide fade" id="modal_filtro">
  <div class="modal-header">
      <button class="close" data-dismiss="modal">×</button>
      <h3>Filtros</h3>
  </div>
  <form id="formAgregarFiltrosReporte" class="ajaxForm form-horizontal" method="GET" action="#">
    <div class="modal-body">
      <div class="control-group">
        <input type="hidden" id="filtro_reporte_id" />
        <div class="">
          <label>Grupos de usuarios</label>
          <select class="filter" id="filtro_grupo" name="filtro_grupo">
            <option value="" selected></option>
          <?php
            if($grupos) {
              if(count($grupos) > 1) {
                ?>
                <option value="">Todos</option>
                <?php
              }

              foreach($grupos as $grupo) {
                ?>
                <option value="<?=$grupo->id?>"><?=$grupo->nombre?></option>
                <?php
              }
            }
          ?>
          </select>
        </div>
      </div>
      <div class="control-group">
        <div class="">
          <label>Usuarios</label>
          <select class="filter" id="filtro_usuario" name="filtro_usuario">
            <option value="" selected></option>
          <?php
            $usuarios = Doctrine_Query::create()
                          ->from('Usuario u')
                          ->where('u.registrado = ?', 1)
                          ->execute();
              foreach($usuarios as $usuario) {
                ?>
                <option value="<?=$usuario->id?>"><?=$usuario->usuario?> - <?=$usuario->nombres?> <?=$usuario->apellido_paterno?></option>
                <?php
              }
              ?>
          </select>
        </div>
      </div>
      <div class="control-group" id="filtro_fechas">
        <div class="">
          <label for="filtro_fecha" class="">Fecha de inicio</label>
          <input class="datepicker" type="text" id="filtro_desde" name="filtro_desde" placeholder="Desde" />
          <input class="datepicker" type="text" id="filtro_hasta" name="filtro_hasta" placeholder="Hasta" />
            <span class="mensaje_error_campo" id="mesage" style="display:none;">El rango de fechas es incorrecto</span>
        </div>
      </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="generar_reporte_completo">Generar</button>
        <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
    </div>
  </form>
</div>

<div class="modal hide fade" id="modal_email">
  <div class="modal-header">
      <button class="close" data-dismiss="modal">×</button>
      <h3>Reporte demasiado grande</h3>
  </div>
  <form id="formReporteEmail" class="ajaxForm form-horizontal" method="GET" action="#">
    <div class="modal-body">
      <div class="control-group">
        <p>
            El reporte a generar excede el tamaño máximo definido. Si indica un correo electrónico se generará en segundo plano y una vez finalizado será enviado a la casilla indicada.
        </p>

      </div>

      <div class="control-group" id="email">
        <div class="">
          <label for="email_text" class="">Correo Electrónico</label>
          <input class="" type="text" id="email_text" name="email_text" placeholder="Correo Electrónico" />
        </div>
      </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="generar_reporte_completo_email">Enviar</button>
        <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
    </div>
  </form>
</div>

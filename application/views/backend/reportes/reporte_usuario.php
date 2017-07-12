<script src="<?= base_url() ?>assets/js/modelador-acciones.js" type="text/javascript"></script>

<ul class="breadcrumb">
    <li class="active">
        Reportes de Usuario
    </li>
</ul>
<h2>Reportes de Usuario</h2>

<div>
  <div class="modal-header">
      <h3>Filtros</h3>
  </div>
  <form id="formAgregarFiltrosReporte" class="ajaxForm form-horizontal" method="GET" action="#">
    <div class="modal-body">
      <div class="control-group">
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
            <?php foreach($usuarios as $usuario): ?>
              <option value="<?=$usuario->id?>"><?=$usuario->usuario?> - <?=$usuario->nombres?> <?=$usuario->apellido_paterno?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="control-group">
        <div class="">
          <label for="filtro_fecha" class="">Fecha de inicio</label>
          <input class="datepicker" type="text" id="filtro_desde" name="filtro_desde" placeholder="Desde" />
          <input class="datepicker" type="text" id="filtro_hasta" name="filtro_hasta" placeholder="Hasta" />
        </div>
      </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="generar_reporte_usuario">Generar</button>
    </div>
  </form>
</div>

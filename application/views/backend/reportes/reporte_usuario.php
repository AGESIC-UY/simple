<script src="<?= base_url() ?>assets/js/modelador-acciones.js" type="text/javascript"></script>
<ul class="breadcrumb">
    <li class="active">
        Reportes de Usuario
    </li>
</ul>
<h2>Reportes de Usuario</h2>

<fieldset>
  <legend>Filtros</legend>
  <form id="formAgregarFiltrosReporte" class="ajaxForm form-horizontal" method="GET" action="#">
    <div class="control-group">
      <div class="">
        <label for="filtro_grupo" class="control-label">Grupos de usuarios</label>
        <div class="controls">
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
    </div>
    <div class="control-group">
      <div class="">
        <label for="filtro_usuario" class="control-label">Usuarios</label>
        <div class="controls">
          <select class="filter" id="filtro_usuario" name="filtro_usuario">
            <option value="" selected></option>
            <?php foreach($usuarios as $usuario): ?>
              <option value="<?=$usuario->id?>"><?=$usuario->usuario?> - <?=$usuario->nombres?> <?=$usuario->apellido_paterno?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>
    <script>
      $(document).ready(function(){
        $('#filtro_hasta').change(function() {
          var filtro_desde = $("#filtro_desde").datepicker('getDate');
          var filtro_hasta = $("#filtro_hasta").datepicker('getDate');

          if (filtro_desde > filtro_hasta){
            $("#fechas").addClass("error");
            document.getElementById("generar_reporte_usuario").disabled = true;
            var contenedor = document.getElementById("mensaje");
            contenedor.style.display = "block";
            return true;
          }else {
            $("#fechas").removeClass("error");
            document.getElementById("generar_reporte_usuario").disabled = false;
            var contenedor = document.getElementById("mensaje");
            contenedor.style.display = "none";
            return true;
          }
        });

    });
    </script>
    <div class="control-group" id="fechas">
        <label class="control-label" for="filtro_desde" class="">Fecha de inicio</label>
        <div class="controls">
          <input class="datepicker form-control-feedback" type="text" id="filtro_desde" name="filtro_desde" placeholder="Desde"/>
          <div class="hidden-accessible"><label for="filtro_hasta">Fecha de inicio hasta</label></div>
          <input class="datepicker" type="text" id="filtro_hasta" name="filtro_hasta" placeholder="Hasta"/>
          <span class="mensaje_error_campo" id="mensaje" style="display:none;">El rango de fechas es incorrecto</span>
      </div>
    </div>
    <div class="busqueda_avanzada">
        <button type="button" class="btn btn-primary" id="generar_reporte_usuario">Generar</button>
    </div>
  </form>
</fieldset>

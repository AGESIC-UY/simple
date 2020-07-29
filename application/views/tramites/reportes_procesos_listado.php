
<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('tramites/reportes_procesos') ?>">Reportes de trámites</a> <span class="divider">/</span>
    </li>
    <li class="active"><?= $proceso->nombre ?></li>
</ul>

<h1>Ver reportes de <?= $proceso->nombre ?></h1>

<?php if (count($reportes) > 0): ?>
    <table id="mainTable" class="table">
      <caption class="hide-text">Ver reportes de <?= $proceso->nombre ?></caption>
        <thead>
            <tr>
              <th>Reporte</th>
              <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reportes as $r): ?>
                <tr>
                  <td class="name" data-title="Nombre"><?= $r->nombre ?></td>
                  <td class="actions" data-title="Acciones">
                    <?php if($r->tipo == 'completo'): ?>
                    <a href="#" class="btn btn-primary solicita_filtro" data-reporte="<?=$r->id?>"><span class="icon-eye-open icon-white"></span> Ver</span></a>
                    <?php else: ?>
                      <a href="#" class="btn btn-primary solicita_filtro_basico" data-reporte="<?=$r->id?>"><span class="icon-eye-open icon-white"></span> Ver<span class="hidden-accessible"> <?=$r->nombre?></span></a>
                  <?php endif; ?>
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
            <input type="hidden" id="filtro_reporte_id_basico" />
          <div class="control-group" id="filtro_fechas_basico">
              <label for="filtro_fechas_basico" class="">Fecha de inicio</label>
              <input class="datepicker" type="text" id="filtro_desde_basico" name="filtro_desde_basico" placeholder="Desde" />
              <input class="datepicker" type="text" id="filtro_hasta_basico" name="filtro_hasta_basico" placeholder="Hasta" />
              <span class="mensaje_error_campo" id="mensaje_fechas_invalidas" style="display:none;">El rango de fechas es incorrecto</span>
              <span class="mensaje_error_campo" id="mensaje_fechas_requeridas" style="display:none;">Fecha desde y hasta son campos obligatorios</span>
            </div>
            <div class="control-group" id="filtro_fechas_cambio_basico">
                <label for="fechaInicialBasico">Fecha de último cambio</label>
                <input type='text' name='updated_at_desde' id="fechaInicialBasico" placeholder='Desde' class='datepicker input-small' value='' />
                <div class="hidden-accessible"><label for="fechaFinalBasico">Fecha de último cambio hasta</label></div>
                <input type='text' name='updated_at_hasta' id="fechaFinalBasico" placeholder='Hasta' class='datepicker input-small' value='' />
                <span class="mensaje_error_campo" id="mesage_cambio_basico" style="display:none;">El rango de fechas es incorrecto</span>
          </div>
            <div class="control-group" id="filtro_estado_basico">
                <span style="margin-bottom: 5px; display: block;">Estado del trámite</span>
                <label class='radio' for="cualquiera"><input id="cualquiera" type='radio' checked="" name='pendiente' value='-1'> Cualquiera</label>
                <label class='radio' for="curso"><input id="curso" type='radio' name='pendiente' value='1'> En curso</label>
                <label class='radio' for="completado"><input id="completado" type='radio' name='pendiente' value='0'> Completado</label>
        </div>
            <span class="mensaje_error_campo" id="mensaje_limite_basico" style="display:block;"></span>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="generar_reporte_basico">Generar</button>
            <a href="#" data-dismiss="modal" class="btn btn-link" id="close_basico">Cerrar</a>
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
          <div class="control-group">
              <label>Usuarios</label>
              <select class="filter" id="filtro_usuario" name="filtro_usuario">
                <option value="" selected></option>
              <?php
                  foreach($usuarios as $usuario) {
                    ?>
                    <option value="<?=$usuario->id?>"><?=$usuario->usuario?> - <?=$usuario->nombres?> <?=$usuario->apellido_paterno?></option>
                    <?php
                  }
                  ?>
              </select>
            </div>
            <div class="control-group" id="filtro_fechas_completo">
                <label for="filtro_fecha_completo" class="">Fecha de inicio</label>
              <input class="datepicker" type="text" id="filtro_desde" name="filtro_desde" placeholder="Desde" />
              <input class="datepicker" type="text" id="filtro_hasta" name="filtro_hasta" placeholder="Hasta" />
                <span class="mensaje_error_campo" id="mesage_completo" style="display:none;">El rango de fechas es incorrecto</span>                
            </div>
            <div class="control-group" id="filtro_fechas_cambio_completo">
                <label for="fechaInicialCompleto">Fecha de último cambio</label>
                <input type='text' name='updated_at_desde_completo' id="fechaInicialCompleto" placeholder='Desde' class='datepicker input-small' value='' />
                <div class="hidden-accessible"><label for="fechaFinalCompleto">Fecha de último cambio hasta</label></div>
                <input type='text' name='updated_at_hasta_completo' id="fechaFinalCompleto" placeholder='Hasta' class='datepicker input-small' value='' />
                <span class="mensaje_error_campo" id="mesage_cambio_completo" style="display:none;">El rango de fechas es incorrecto</span>
          </div>
            <div class="control-group" id="filtro_estado_completo">
                <span style="margin-bottom: 5px; display: block;">Estado del trámite</span>
                <label class='radio' for="cualquiera_completo"><input id="cualquiera_completo" type='radio' checked="" name='pendiente_completo' value='-1'> Cualquiera</label>
                <label class='radio' for="curso_completo"><input id="curso_completo" type='radio' name='pendiente_completo' value='1'> En curso</label>
                <label class='radio' for="completado_completo"><input id="completado_completo" type='radio' name='pendiente_completo' value='0'> Completado</label>
        </div>
            <span class="mensaje_error_campo" id="mensaje_limite_completo" style="display:block;"></span>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="generar_reporte_completo">Generar</button>
            <a href="#" data-dismiss="modal" class="btn btn-link" id="close_completo">Cerrar</a>
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
<?php else: ?>
  <p>No hay reportes disponibles para este proceso.</p>
<?php endif; ?>

  <script>
    $(document).ready(function () {
        
        //Validar fechas REPORTE COMPLETO
        $('#filtro_hasta').change(function () {

            var desde = $("#filtro_desde").datepicker('getDate');
            var hasta = $("#filtro_hasta").datepicker('getDate');

            if (desde > hasta) {
                $("#filtro_fechas_completo").addClass("error");
                document.getElementById("generar_reporte_completo").disabled = true;
                var contenedor = document.getElementById("mesage_completo");
                contenedor.style.display = "block";
                document.getElementById("fechaInicialCompleto").disabled = true;
                document.getElementById("fechaFinalCompleto").disabled = true;
                return true;
            } else {
                $("#filtro_fechas_completo").removeClass("error");
                document.getElementById("generar_reporte_completo").disabled = false;
                document.getElementById("fechaInicialCompleto").disabled = false;
                document.getElementById("fechaFinalCompleto").disabled = false;
                var contenedor = document.getElementById("mesage_completo");
                contenedor.style.display = "none";
                return true;
            }
        });
        
        $('#fechaFinalCompleto').change(function () {
            
            var fechaInicial = $("#fechaInicialCompleto").datepicker('getDate');
            var fechaFinal = $("#fechaFinalCompleto").datepicker('getDate');
            
            if (fechaInicial > fechaFinal) {
                $("#filtro_fechas_cambio_completo").addClass("error");
                document.getElementById("generar_reporte_completo").disabled = true;
                var contenedor = document.getElementById("mesage_cambio_completo");
                document.getElementById("filtro_desde").disabled = true;
                document.getElementById("filtro_hasta").disabled = true;
                contenedor.style.display = "block";
                return true;
            } else {
                $("#fecha_update").removeClass("error");
                document.getElementById("generar_reporte_completo").disabled = false;
                var contenedor = document.getElementById("mesage_cambio_completo");
                document.getElementById("filtro_desde").disabled = false;
                document.getElementById("filtro_hasta").disabled = false;
                contenedor.style.display = "none";
                return true;
            }
        });
        
        //Validar fechas REPORTE BASICO
        $('#filtro_hasta_basico').change(function () {

            var desde = $("#filtro_desde_basico").datepicker('getDate');
            var hasta = $("#filtro_hasta_basico").datepicker('getDate');

            if (desde > hasta) {
                $("#filtro_fechas_basico").addClass("error");
                document.getElementById("generar_reporte_basico").disabled = true;
                var contenedor = document.getElementById("mensaje_fechas_invalidas");
                contenedor.style.display = "block";
                document.getElementById("fechaInicialBasico").disabled = true;
                document.getElementById("fechaFinalBasico").disabled = true;
                return true;
            } else {
                $("#filtro_fechas_completo").removeClass("error");
                document.getElementById("generar_reporte_basico").disabled = false;
                var contenedor = document.getElementById("mensaje_fechas_invalidas");
                contenedor.style.display = "none";
                document.getElementById("fechaInicialBasico").disabled = false;
                document.getElementById("fechaFinalBasico").disabled = false;
                return true;
            }
        });
        
        $('#fechaFinalBasico').change(function () {

            var fechaInicial = $("#fechaInicialBasico").datepicker('getDate');
            var fechaFinal = $("#fechaFinalBasico").datepicker('getDate');
            
            if (fechaInicial > fechaFinal) {
                $("#filtro_fechas_cambio_basico").addClass("error");
                document.getElementById("generar_reporte_basico").disabled = true;
                var contenedor = document.getElementById("mesage_cambio_basico");
                contenedor.style.display = "block";
                document.getElementById("filtro_desde_basico").disabled = true;
                document.getElementById("filtro_hasta_basico").disabled = true;
                return true;
            } else {
                $("#filtro_fechas_cambio_basico").removeClass("error");
                document.getElementById("generar_reporte_basico").disabled = false;
                var contenedor = document.getElementById("mesage_cambio_basico");
                contenedor.style.display = "none";
                document.getElementById("filtro_desde_basico").disabled = false;
                document.getElementById("filtro_hasta_basico").disabled = false;
                return true;
            }
        });       
        
    });
</script>
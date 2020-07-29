<div id="contenido_monitoreo_trazabilidad">
    <fieldset>
      <legend>Estado servicio cabezal</legend>

      <?php if($respuesta_cabezal->error): ?>
        <div class="alert alert-danger" role="alert">
          <p>Respuesta: <strong><?php echo $respuesta_cabezal->mensaje; ?></strong> <span class="icon-remove icon-white"></span></p>
          <a href="" data-toggle="modal" data-target="#cabezal-modal">Ver detalles</a>
        </div>
      <?php else: ?>
        <div class="alert alert-success" role="alert">
          <p>Respuesta: <strong><?php echo $respuesta_cabezal->mensaje; ?></strong> <span class="icon-check icon-white"></span></p>
          <a href="" data-toggle="modal" data-target="#cabezal-modal">Ver detalles</a>
        </div>
      <?php endif; ?>

      <!-- Modal Cabezal-->
      <div class="modal fade" id="cabezal-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="myModalLabel">Cabezal</h4>
            </div>
            <div class="modal-body">
                <p><strong>URL del servicio:</strong> <?php echo WS_AGESIC_TRAZABLIDAD_CABEZAL; ?></p>
                <p><strong>Respuesta:</strong> <?php echo $respuesta_cabezal->mensaje; ?></p>
                <br>
                <strong>Petición:</strong>
                <pre><?php echo htmlspecialchars($respuesta_cabezal->ws_body); ?></pre>
                <br>
                <strong>Respuesta:</strong>
                <pre><?php echo htmlspecialchars($respuesta_cabezal->ws_response); ?></pre>
            </div>
              <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>

    </fieldset>

    <fieldset>
      <legend>Estado servicio línea</legend>

          <?php if($respuesta_linea->error): ?>
            <div class="alert alert-danger" role="alert">
              <p>Respuesta: <strong><?php echo $respuesta_linea->mensaje; ?></strong> <span class="icon-remove icon-white"></span></p>
              <a href="" data-toggle="modal" data-target="#linea-modal">Ver detalles</a>
            </div>
          <?php else: ?>
            <div class="alert alert-success" role="alert">
              <p>Respuesta: <strong><?php echo $respuesta_linea->mensaje; ?></strong> <span class="icon-check icon-white"></span> </p>
              <a href="" data-toggle="modal" data-target="#linea-modal">Ver detalles</a>
            </div>
          <?php endif; ?>

          <!-- Modal Linea-->
          <div class="modal fade" id="linea-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="myModalLabel">Línea</h4>
                </div>
                <div class="modal-body">
                    <p><strong>URL del servicio:</strong> <?php echo WS_AGESIC_TRAZABLIDAD_LINEA; ?></p>
                    <p><strong>Respuesta:</strong> <?php echo $respuesta_linea->mensaje; ?></p>
                    <br>
                    <strong>Petición:</strong>
                    <pre><?php echo htmlspecialchars($respuesta_linea->ws_body); ?></pre>
                    <br>
                    <strong>Respuesta:</strong>
                    <pre><?php echo htmlspecialchars($respuesta_linea->ws_response); ?></pre>
                </div>
                  <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
              </div>
            </div>
          </div>


    </fieldset>

    <ul class="form-action-buttons">
        <li class="action-buttons-primary">
            <ul>
              <li class="float-left">
                <a class="btn btn-primary btn-lg" href="<?=site_url('backend/configuracion/monitoreo_trazabilidad_ajax')?>">Verificar Estado Nuevamente</a>
              </li>
            </ul>
        </li>
        <li class="action-buttons-second">
            <ul>
                <li class="float-left">
                  <a class="btn btn-link btn-lg" href="<?=site_url('backend/configuracion/misitio')?>">Cancelar</a>
                </li>
            </ul>
        </li>
    </ul>
</div>

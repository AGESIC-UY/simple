<div id="contenido_monitoreo_pasarela">
    <fieldset>
        <legend>Estado pasarelas de pagos</legend>

        <?php foreach ($pasarelas_pagos_monitoreo as $pasarela_monitoreo): ?>
              <?php if($pasarela_monitoreo->error_monitoreo): ?>
                <div class="alert alert-danger" role="alert">
                  <p><?php echo $pasarela_monitoreo->pasarela_nombre; ?>: <strong><?php echo $pasarela_monitoreo->error_texto_monitoreo; ?></strong> <span class="icon-remove icon-white"></span></p>
                  <a href="" data-toggle="modal" data-target="#<?php echo $pasarela_monitoreo->id; ?>">Ver detalles</a> |
                  <a href="<?php echo site_url('backend/pasarela_pagos/editar/'.$pasarela_monitoreo->id); ?>" target="_blank"> Ver configuración</a>
                </div>
              <?php else: ?>
                <div class="alert alert-success" role="alert">
                  <p><?php echo $pasarela_monitoreo->pasarela_nombre; ?>: <strong>OK</strong>  <span class="icon-check icon-white"></span></p>
                  <a href="" data-toggle="modal" data-target="#<?php echo $pasarela_monitoreo->id; ?>">Ver detalles</a> |
                  <a href="<?php echo site_url('backend/pasarela_pagos/editar/'.$pasarela_monitoreo->id); ?>" target="_blank"> Ver configuración</a>
                </div>
              <?php endif; ?>


              <!-- Modal Peticion-->
              <div class="modal fade" id="<?php echo $pasarela_monitoreo->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display:none;">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="myModalLabel"><?php echo $proceso->nombre; ?></h4>
                    </div>
                    <div class="modal-body">
                        <p><strong>URL del servicio:</strong> <?php echo $pasarela_monitoreo->url; ?></p>
                        <p><strong>Estado:</strong> <?php echo $pasarela_monitoreo->error_texto_monitoreo; ?></p>
                        <p><strong>Certificado:</strong> <?php echo $pasarela_monitoreo->certificado_ssl_monitoreo; ?></p>
                        <br>
                        <strong>Petición:</strong>
                        <pre class="prettyprint"><?php echo htmlspecialchars($pasarela_monitoreo->ws_body_monitoreo); ?></pre>
                        <br>
                        <strong>Respuesta:</strong>
                        <pre class="prettyprint"><?php echo htmlspecialchars($pasarela_monitoreo->ws_response_monitoreo); ?></pre>
                    </div>
                      <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                  </div>
                </div>
              </div>

        <?php endforeach; ?>
      </fieldset>

      <ul class="form-action-buttons">
          <li class="action-buttons-primary">
              <ul>
                  <li>
                      <a class="btn btn-primary btn-lg"href="<?=site_url('backend/configuracion/monitoreo_pasarelas_ajax')?>">Verificar Estado Nuevamente</a>
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

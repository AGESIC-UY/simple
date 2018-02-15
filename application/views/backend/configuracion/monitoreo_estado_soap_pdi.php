<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?= site_url('backend/configuracion') ?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li class="active">Servicios SOAP y PDI</li>
        </ul>
        <h2>Monitoreo: Servicios SOAP y PDI (Últimas 10 ejecuciones)</h2>

        <fieldset>
            <legend>Ejecuciones SOAP</legend>

            <?php if(count($lista_soap) > 0): ?>
            <table class="table table-bordered">
              <tr>
                <th>Proceso</th>
                <th>URL</th>
                <th>Fecha</th>
                <th>Seguridad</th>
                <th>Error</th>
                <th>Mensaje</th>
                <th>Petición y Respuesta</th>
                <th>Catálogo</th>
              </tr>

              <?php foreach ($lista_soap as $monitoreo_soap): ?>

                <?php $proceso = Doctrine_Query::create()
                        ->from('Proceso p')
                        ->where('p.id = ? ', $monitoreo_soap->proceso_id)
                        ->fetchOne(); ?>

                  <tr>
                    <td><?php echo $proceso->nombre; ?></td>
                    <td style="word-break: break-all;"><?php echo $monitoreo_soap->url_web_service; ?></td>
                    <td><?php echo $monitoreo_soap->fecha; ?></td>
                    <td><?=$monitoreo_soap->seguridad?'Si':'No'?></td>
                    <td><?=$monitoreo_soap->error?'<p style="color:red;">Si</p>':'No'?></td>
                    <td><?php echo $monitoreo_soap->error_texto; ?></td>
                    <td><a href="" data-toggle="modal" data-target="#<?php echo $monitoreo_soap->id; ?>">Ver</a></td>
                    <td> <a href="<?php echo site_url('backend/ws_catalogos/editar/'.$monitoreo_soap->catalogo_id); ?>" target="_blank">Ver</a></td>

                    <!-- Modal Peticion-->
                    <div class="modal fade" id="<?php echo $monitoreo_soap->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Servicio SOAP</h4>
                          </div>
                          <div class="modal-body">
                              <p><strong>URL del servicio:</strong> <?php echo $monitoreo_soap->url_web_service; ?></p>
                              <p><strong>Fecha ejecución:</strong> <?php echo $monitoreo_soap->fecha; ?></p>
                              <p><strong>Seguridad:</strong> <?=$monitoreo_soap->seguridad?'Si':'No'?></p>
                              <p><strong>Mensaje:</strong> <?php echo $monitoreo_soap->error_texto; ?></p>
                              <p><strong>Proceso:</strong> <?php echo $proceso->nombre; ?></p>
                              <br>
                              <strong>Petición:</strong>
                                <pre><?php echo htmlspecialchars($monitoreo_soap->soap_peticion); ?></pre>
                              <br>
                              <strong>Respuesta:</strong>
                                <pre><?php echo htmlspecialchars($monitoreo_soap->soap_respuesta); ?></pre>
                          </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </tr>
              <?php endforeach; ?>

              </table>
            <?php else: ?>
              <p>Aún no se registraron ejecuciones.</p>
            <?php endif; ?>
        </fieldset>

        <fieldset>
            <legend>Ejecuciones PDI</legend>

            <?php if(count($lista_pdi) > 0): ?>
            <table class="table table-bordered">
              <tr>
                <th>Proceso</th>
                <th>URL</th>
                <th>Fecha</th>
                <th>Rol</th>
                <th>Certificado</th>
                <th>Error</th>
                <th>Mensaje</th>
                <th>Petición / Respuesta</th>
                <th>Catalogo</th>
              </tr>

            <?php foreach ($lista_pdi as $monitoreo_pdi): ?>

              <?php $proceso = Doctrine_Query::create()
                      ->from('Proceso p')
                      ->where('p.id = ? ', $monitoreo_pdi->proceso_id)
                      ->fetchOne(); ?>

                <tr>
                  <td><?php echo $proceso->nombre; ?></td>
                  <td style="word-break: break-all;"><?php echo $monitoreo_pdi->url_web_service; ?></td>
                  <td><?php echo $monitoreo_pdi->fecha; ?></td>
                  <td><?php echo $monitoreo_pdi->rol; ?></td>
                  <td><?php echo $monitoreo_pdi->certificado; ?></td>
                  <td><?=$monitoreo_pdi->error?'<p style="color:red;">Si</p>':'No'?></td>
                  <td><?php echo $monitoreo_pdi->error_texto; ?></td>
                  <td><a href="" data-toggle="modal" data-target="#<?php echo $monitoreo_pdi->id;?>">Ver</a></td>
                  <td> <a href="<?php echo site_url('backend/ws_catalogos/editar/'.$monitoreo_pdi->catalogo_id); ?>" target="_blank">Ver</a></td>

                  <!-- Modal Peticion-->
                  <div class="modal fade" id="<?php echo $monitoreo_pdi->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick=""><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="myModalLabel">Servicio PDI</h4>
                        </div>
                        <div class="modal-body">
                            <p><strong>URL del servicio:</strong> <?php echo $monitoreo_pdi->url_web_service; ?></p>
                            <p><strong>Fecha ejecución:</strong> <?php echo $monitoreo_pdi->fecha; ?></p>
                            <p><strong>Mensaje:</strong> <?php echo $monitoreo_pdi->error_texto; ?></p>
                            <p><strong>Rol:</strong> <?php echo $monitoreo_pdi->rol; ?></p>
                            <p><strong>Certificado:</strong> <?php echo $monitoreo_pdi->certificado; ?></p>
                            <p><strong>Proceso:</strong> <?php echo $proceso->nombre; ?></p>
                            <br>
                            <strong>Petición:</strong>
                              <pre><?php echo htmlspecialchars($monitoreo_pdi->soap_peticion); ?></pre>
                            <br>
                            <strong>Respuesta:</strong>
                              <pre><?php echo htmlspecialchars($monitoreo_pdi->soap_respuesta); ?></pre>
                        </div>
                          <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </tr>
            <?php endforeach; ?>

            </table>
          <?php else: ?>
            <p>Aún no se registraron ejecuciones.</p>
          <?php endif; ?>
        </fieldset>


        <ul class="form-action-buttons">
            <li class="action-buttons-primary">
                <ul>
                    <li>
                        <a class="btn btn-primary btn-lg"href="<?=site_url('backend/configuracion/monitoreo_estado_soap_pdi')?>">Verificar Estado Nuevamente</a>
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
</div>

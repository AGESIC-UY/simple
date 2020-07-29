<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?= site_url('backend/configuracion') ?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li class="active">Ejecuciones Servicios SOAP y PDI</li>
        </ul>
        <h2>Monitoreo: Ejecuciones Servicios SOAP y PDI</h2>


            <div class="busqueda_avanzada"><a href='#' id="busqueda_filtro_monitoreo_toggle">Búsqueda avanzada</a></div>
            <br>
            <div id="busqueda_monitoreo_filtro" style="display:none;">
              <fieldset>
              <legend>Filtros de búsqueda</legend>

              <table width="100%">
                <tr>
                  <td>
                    <label for="busqueda_tipo_servicio" class="control-label">Tipo de servicio</label>
                    <select class="filter" data-col="tipo_servicio" id="busqueda_tipo_servicio" name="busqueda_tipo_servicio">
                      <option value="" <?= $busqueda_tipo_servicio == ''?'selected':'' ?> >Seleccionar</option>
                        <option value="soap" <?= $busqueda_tipo_servicio == 'soap'?'selected':'' ?> >SOAP</option>
                        <option value="pdi" <?= $busqueda_tipo_servicio == 'pdi'?'selected':'' ?>>PDI</option>
                    </select>

                    <label for="busqueda_nombre_servicio" class="control-label">Nombre de servicio</label>
                    <input class="filter" data-col="nombre_servicio" type="text" id="busqueda_nombre_servicio" name="busqueda_nombre_servicio" value="<?= $busqueda_nombre_servicio ?>"/>
                  </td>
                  <td>
                    <label for="busqueda_id_tramite" class="control-label">ID Trámite</label>
                    <input class="input-medium filter" data-col="id_tramite" type="number" id="busqueda_id_tramite" name="busqueda_id_tramite" value="<?= $busqueda_id_tramite ?>"/>

                    <label for="busqueda_id_etapa" class="control-label">ID Etapa</label>
                    <input class="input-medium filter" data-col="id_etapa" type="number" id="busqueda_id_etapa" name="busqueda_id_etapa" value="<?= $busqueda_id_etapa ?>"/>
                  </td>
                  <td>
                    <label for="busqueda_fecha" class="control-label">Ejecutados entre Fechas</label>
                    <input class="datepicker_" type="text" id="busqueda_fecha_desde" name="busqueda_fecha_desde" placeholder="Desde" value="<?= $busqueda_fecha_desde ?>"/>
                    <input class="datepicker_" type="text" id="busqueda_fecha_hasta" name="busqueda_fecha_hasta" placeholder="Hasta"  value="<?= $busqueda_fecha_hasta ?>"/>
                    <br><br>
                    <input type="button" id="btn_buscar_monitoreo_filtro" class="btn btn-primary" value="Buscar" />
                    <a id="limpiar_filtro" href="?filtro=1">Limpiar</a>
                  </td>
                </tr>
              </table>
              <div id="error_filtro"></div>
          </div>

          <?php if($filtro_aplicado){?>
          <script>
            $("#busqueda_monitoreo_filtro").fadeIn();
          </script>
          <?php } ?>

          <br><br>

            <?php if(count($lista_monitoreo) > 0 || $filtro_aplicado){ ?>

              <div id="filtros_aplicados" style="margin: 0px;">
                <?php if($filtro_aplicado){?>
                  <p>* Filtros búsqueda avanzada aplicados</p>
                <?php } ?>
              </div>
            </fieldset>
          <div id="contenido">
            <table class="table table-bordered" id="tabla_monitoreo">
              <tr>
                <th>Tipo</th>
                <th>Proceso</th>
                <th>ID Trámite</th>
                <th>ID Etapa</th>
                <th>URL</th>
                <th>Fecha petición</th>
                <th>Error servicio</th>
                <th></th>
                <th></th>
              </tr>

              <?php foreach ($lista_monitoreo as $monitoreo){ ?>

                <?php
                  $proceso = Doctrine::getTable('Proceso')->find($monitoreo->proceso_id);
                  $etapa = Doctrine::getTable('Etapa')->find($monitoreo->etapa_id);
                  $paso = Doctrine::getTable('Paso')->find($monitoreo->paso_id);
                ?>

                  <tr>
                    <td><?= $monitoreo->tipo? strtoupper($monitoreo->tipo):'-' ?></td>
                    <td><?php if ($proceso) { echo $proceso->nombre ? $proceso->nombre:'-'; }else{ echo '-';}?></td>
                    <td><?= $etapa?$etapa->Tramite->id:'-' ?></td>
                    <td><?= $etapa?$etapa->id:'-'?></td>
                    <td style="word-break: break-all;">
                      <?php
                          if(!$monitoreo->url_web_service){
                            echo '-';
                          }
                          else if(strlen($monitoreo->url_web_service) > 40){
                            echo substr($monitoreo->url_web_service,0,40).' <a title="'.$monitoreo->url_web_service.'" href="" data-toggle="modal" data-target="#modal_url_'.$monitoreo->id.'"><span class="icon-ellipsis-horizontal"></span></a>';
                          }
                          else{
                            echo $monitoreo->url_web_service;
                          }
                      ?>
                    </td>
                    <td><?= date('d/m/Y H:i:s',strtotime($monitoreo->fecha)) ?></td>
                    <td style="word-break: break-all;">
                      <?php
                          if(!$monitoreo->error){
                            echo '<span style="color:green;">No</span>';
                          }
                          else if(strlen($monitoreo->error_texto) > 50){
                            echo '<span class="scroll-tabla" style="color:red;">'.substr($monitoreo->error_texto,0,50).'</span> <a title="'.$monitoreo->error_texto.'" href="" data-toggle="modal" data-target="#modal_error_'.$monitoreo->id.'"><span class="icon-ellipsis-horizontal"></span></a>';
                          }
                          else{
                            echo '<span class="scroll-tabla" style="color:red;">'.$monitoreo->error_texto.'</span>';
                          }
                      ?>
                    </td>
                    <td><a href="" data-toggle="modal" data-target="#modal_peticion_<?= $monitoreo->id ?>" style="font-size: 20px;display: block;text-align: center;" class="tooltip-tabla" title="Más detalles"><span class="icon-expand-alt"></span></a></td>
                    <td>
                      <?php
                          if(!$monitoreo->catalogo_id){
                            echo '<span class="tooltip-tabla" title="No se registró catálogo">-</span>';
                          }
                          else{
                            echo '<a href="'.site_url('backend/ws_catalogos/editar/'.$monitoreo->catalogo_id).'" target="_blank" class="tooltip-tabla" title="Catálogo"><span class="icon-external-link" style="text-align: center;display: block;font-weight: 600;margin-top: 6px;"></span></a>';
                          }
                      ?>
                    </td>
                  </tr>

                  <!-- Modal url-->
                  <div class="modal fade" id="modal_url_<?= $monitoreo->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="myModalLabel">URL servicio</h4>
                        </div>
                        <div class="modal-body">
                            <p><strong></strong> <?= $monitoreo->url_web_service; ?></p>
                        </div>
                          <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Modal error-->
                  <div class="modal fade" id="modal_error_<?= $monitoreo->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="myModalLabel">Error servicio</h4>
                        </div>
                        <div class="modal-body">
                            <p><strong></strong><span class="scroll-tabla" style="color:red;"><?= $monitoreo->error_texto; ?></span></p>
                        </div>
                          <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Modal certificado-->
                  <div class="modal fade" id="modal_certificado_<?= $monitoreo->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="myModalLabel">Certificado</h4>
                        </div>
                        <div class="modal-body">
                            <p><strong></strong><span class="scroll-tabla" style="color:red;"><?= $monitoreo->certificado; ?></span></p>
                        </div>
                          <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Modal Peticion-->
                  <div class="modal fade" id="modal_peticion_<?= $monitoreo->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="myModalLabel">Detalle de ejecución</h4>
                        </div>
                        <div class="modal-body">
                            <p><strong>Tipo de servicio:</strong> <?= $monitoreo->tipo? strtoupper($monitoreo->tipo):'-' ?> </p>
                            <p><strong>Nombre del proceso:</strong> <?php if ($proceso) { echo $proceso->nombre ? $proceso->nombre:'-'; }else{ echo '-';}?> </p>
                            <p><strong>Id del Trámite:</strong> <?= $etapa? $etapa->Tramite->id:'-' ?> </p>
                            <p><strong>Id de la Etapa:</strong> <?= $etapa? $etapa->id:'-' ?> </p>
                            <p><strong>Nombre del Paso:</strong>
                              <?php
                                  if($etapa){
                                    if($paso){
                                      echo $paso->nombre.' (paso n° '.$paso->orden.')';
                                    }
                                    else{
                                      echo 'Se ejecutó antes de iniciar o después finalizar de la tarea';
                                    }
                                  }
                                  else { //para casos que no tengan etapas ni pasos registrados
                                    echo '-';
                                  }
                                ?>
                            </p>
                            <p><strong>URL del servicio:</strong> <?= $monitoreo->url_web_service?$monitoreo->url_web_service:'-'; ?></p>
                            <p><strong>Catálogo: </strong>
                              <?php
                                  if(!$monitoreo->catalogo_id){
                                    echo '-';
                                  }
                                  else{
                                    echo '<a href="'.site_url('backend/ws_catalogos/editar/'.$monitoreo->catalogo_id).'" target="_blank"><span class="icon-external-link"></span></a>';
                                  }
                              ?>
                            </p>
                            <p><strong>Rol:</strong> <?=$monitoreo->rol?$monitoreo->rol:'No'?></p>
                            <p><strong>Certificado:</strong> <?=$monitoreo->certificado?$monitoreo->certificado:'No'?></p>
                            <p><strong>Seguridad:</strong> <?=$monitoreo->seguridad?'Si':'No'?></p>
                            <p><strong>Error durante la ejecución:</strong> <?= $monitoreo->error?'<span style="color:red;">'.$monitoreo->error_texto.'</span>':'<span style="color:green;">No</span>'?></p>
                            <br>
                            <h4>Petición</h4>
                            <p><strong>Fecha:</strong> <?= date('d/m/Y H:i:s',strtotime($monitoreo->fecha)) ?> </p>
                              <pre><?= $monitoreo->soap_peticion? htmlspecialchars($monitoreo->soap_peticion):'-' ?></pre>
                            <br>
                            <h4>Respuesta</h4>
                            <p><strong>Fecha:</strong> <?= date('d/m/Y H:i:s',strtotime($monitoreo->fecha_respuesta_servicio)) ?> </p>
                              <pre><?= $monitoreo->soap_respuesta? htmlspecialchars($monitoreo->soap_respuesta):'-' ?></pre>
                        </div>
                          <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>
                      </div>
                    </div>
                  </div>

              <?php } ?>
              </table>
            </div>
              <div id="paginado_div">
                 <?= $paginado ?>
               </div>
            <?php } else { ?>
                <strong>Aún no se registraron ejecuciones.</strong>
              <?php } ?>

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

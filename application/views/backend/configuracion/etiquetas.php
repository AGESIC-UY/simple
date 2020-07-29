<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?= site_url('backend/configuracion') ?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li class="active">Etiquetas de Trazabilidad</li>
        </ul>
        <h2>Trazabilidad: Etiquetas de Trazabilidad</h2>
        <form class="ajaxForm" method="post" action="<?= site_url('backend/configuracion/etiquetas_form/') ?>">
            <fieldset id="accion-etiquetas">
                <legend>Editar etiquetas de trazabilidad</legend>
                <div class="validacion validacion-error"></div>
                <div class="form-horizontal">
                  <div class="control-group">
                    <div class="controls lista_etiquetas">
                    <?php
                    $count = 0;
                    if(count($etiquetas) >= 1) {
                      foreach($etiquetas as $etiqueta) {
                        if($count == 0) { ?>
                          <span class="campo_etiqueta"><input class="etiqueta_id" type="hidden" name="etiqueta[<?=$count?>][id]" value="<?=(!$etiqueta->id ? 'null' : $etiqueta->id)?>" /> <input type="text" name="etiqueta[<?=$count?>][etiqueta]" class="input-medium" placeholder="Etiqueta" value="<?=$etiqueta->etiqueta?>" /> <input type="text" name="etiqueta[<?=$count?>][descripcion]" class="input-large" placeholder="Descripción" value="<?=$etiqueta->descripcion?>" /> <span id="add_etiqueta_configuracion" class="icon-plus btn"></span><br /><br /></span>
                          <?php
                          }
                          else { ?>
                            <span class="campo_etiqueta"><input type="hidden" class="etiqueta_id" name="etiqueta[<?=$count?>][id]" value="<?=(!$etiqueta->id ? 'null' : $etiqueta->id)?>" /> <input type="text" name="etiqueta[<?=$count?>][etiqueta]" class="input-medium" placeholder="Etiqueta" value="<?=$etiqueta->etiqueta?>" /> <input type="text" name="etiqueta[<?=$count?>][descripcion]" class="input-large" placeholder="Descripción" value="<?=$etiqueta->descripcion?>" /> <span class="icon-minus btn remove_etiqueta_configuracion"></span><br /><br /></span>
                          <?php
                          }
                          $count++;
                        }
                      }
                      else { ?>
                        <span class="campo_etiqueta"><input type="hidden" name="etiqueta[<?=$count?>][id]" value="null" /> <input type="text" name="etiqueta[0][etiqueta]" class="input-medium" placeholder="Etiqueta" /> <input type="text" name="etiqueta[0][descripcion]" class="input-large" placeholder="Descripción" /> <span id="add_etiqueta_configuracion" class="icon-plus btn"></span><br /><br /></span>
                      <?php
                    }
                    ?>
                    <input value="<?=$count?>" type="hidden" id="total_etiquetas" />
                    </div>
                  </div>
                </div>
            </fieldset>
            <ul class="form-action-buttons">
                <li class="action-buttons-primary">
                    <ul>
                        <li>
                          <input class="btn btn-primary btn-lg" type="submit" value="Guardar" />
                        </li>
                    </ul>
                </li>
                <li class="action-buttons-second">
                    <ul>
                        <li class="float-left">
                          <a class="btn btn-link btn-lg" href="<?=site_url('backend/configuracion/')?>">Cancelar</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </form>

    </div>
</div>

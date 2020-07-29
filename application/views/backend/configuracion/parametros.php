<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?= site_url('backend/configuracion') ?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li class="active">Parámetros</li>
        </ul>
        <h2>General: Parámetros</h2>
        <form class="ajaxForm" method="post" action="<?= site_url('backend/configuracion/parametros_form/') ?>">
            <fieldset id="accion-parametros">
                <legend>Editar parámetros del sistema</legend>
                <div class="validacion validacion-error"></div>
                <div class="form-horizontal">
                  <div class="control-group">
                    <div class="controls lista_parametros">
                    <?php
                    $count = 0;
                    if(count($parametros) >= 1) {
                      foreach($parametros as $parametro) {
                        if($count == 0) { ?>
                          <span class="campo_parametro"><input class="parametro_id" type="hidden" name="parametro[<?=$count?>][id]" value="<?=(!$parametro->id ? 'null' : $parametro->id)?>" /> <input type="text" name="parametro[<?=$count?>][clave]" class="input-large" placeholder="Clave" value="<?=$parametro->clave?>" /> <input type="text" name="parametro[<?=$count?>][valor]" class="input-medium" placeholder="Valor" value="<?=$parametro->valor?>" /> <span id="add_parametro_configuracion" class="icon-plus btn"></span><br /><br /></span>
                          <?php
                          }
                          else { ?>
                            <span class="campo_parametro"><input type="hidden" class="parametro_id" name="parametro[<?=$count?>][id]" value="<?=(!$parametro->id ? 'null' : $parametro->id)?>" /> <input type="text" name="parametro[<?=$count?>][clave]" class="input-large" placeholder="Clave" value="<?=$parametro->clave?>" /> <input type="text" name="parametro[<?=$count?>][valor]" class="input-medium" placeholder="Valor" value="<?=$parametro->valor?>" /> <span class="icon-minus btn remove_parametro_configuracion"></span><br /><br /></span>
                          <?php
                          }
                          $count++;
                        }
                      }
                      else { ?>
                        <span class="campo_parametro"><input type="hidden" name="parametro[<?=$count?>][id]" value="<?=(!$parametro->id ? 'null' : $parametro->id)?>" /> <input type="text" name="parametro[0][clave]" class="input-medium" placeholder="Clave" /> <input type="text" name="parametro[0][valor]" class="input-medium" placeholder="Valor" /> <span id="add_parametro_configuracion" class="icon-plus btn"></span><br /><br /></span>
                      <?php
                    }
                    ?>
                    <input value="<?=$count?>" type="hidden" id="total_parametros" />
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

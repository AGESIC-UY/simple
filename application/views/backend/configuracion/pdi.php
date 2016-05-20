<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?= site_url('backend/configuracion') ?>">Configuración de PDI</a> <span class="divider">/</span>
            </li>
            <li class="active">Configuración</li>
        </ul>
        <h2>Plataforma de interoperabilidad: Configuración</h2>
        <form class="ajaxForm" method="post" action="<?= site_url('backend/configuracion/pdi_form/') ?>">
            <fieldset>
                <legend>Editar información de PDI</legend>
                <div class="validacion validacion-error"></div>
                <div class="form-horizontal">
                  <div class="control-group">
                    <label for="sts" class="control-label">STS</label>
                    <div class="controls">
                      <input class="input-large" id="sts" type="text" name="sts" value="<?=($pdi->sts != '' ? $pdi->sts : '')?>" />
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="policy" class="control-label">Policy name</label>
                    <div class="controls">
                      <input class="input-large" id="policy" type="text" name="policy" value="<?=($pdi->policy != '' ? $pdi->policy : '')?>" />
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="certificado_organismo" class="control-label">PKC12 organismo</label>
                    <div class="controls">
                      <div id="file-uploader-org"></div>
                      <input  id="certificado_organismo" type="hidden" name="certificado_organismo" value="<?=($pdi->certificado_organismo != '' ? $pdi->certificado_organismo : '')?>" />
                      <script>
                          var uploader = new qq.FileUploader({
                              element: document.getElementById('file-uploader-org'),
                              action: site_url+'backend/uploader/pdi_certificados',
                              onComplete: function(id,filename,respuesta){
                                  $("input[name=certificado_organismo]").val(respuesta.file_name);
                              }
                          });
                      </script>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="clave_organismo" class="control-label">PKC12 organismo clave</label>
                    <div class="controls">
                      <input class="input-large" id="clave_organismo" type="text" name="clave_organismo" value="<?=($pdi->clave_organismo != '' ? $pdi->clave_organismo : '')?>" />
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="certificado_ssl" class="control-label">PEM SSL</label>
                    <div class="controls">
                      <div id="file-uploader-ssl"></div>
                      <input  id="certificado_ssl" type="hidden" name="certificado_ssl" value="<?=($pdi->certificado_ssl != '' ? $pdi->certificado_ssl : '')?>" />
                      <script>
                          var uploader = new qq.FileUploader({
                              element: document.getElementById('file-uploader-ssl'),
                              action: site_url+'backend/uploader/pdi_certificados',
                              onComplete: function(id,filename,respuesta){
                                  $("input[name=certificado_ssl]").val(respuesta.file_name);
                              }
                          });
                      </script>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="clave_ssl" class="control-label">PEM SSL clave</label>
                    <div class="controls">
                      <input class="input-large" id="clave_ssl" type="text" name="clave_ssl" value="<?=($pdi->clave_ssl != '' ? $pdi->clave_ssl : '')?>"/>
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
                          <a class="btn btn-link btn-lg" href="<?=site_url('backend/configuracion/usuarios')?>">Cancelar</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </form>

    </div>
</div>

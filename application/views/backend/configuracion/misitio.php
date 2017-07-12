<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?= site_url('backend/configuracion') ?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li class="active">Cuenta</li>
        </ul>
        <h2>General: Cuenta</h2>
        <form class="ajaxForm" method="post" action="<?= site_url('backend/configuracion/misitio_form/') ?>">
            <fieldset>
                <legend>Editar información de la cuenta</legend>
                <div class="validacion validacion-error"></div>
                <div class="form-horizontal">
                  <div class="control-group">
                    <label for="nombre" class="control-label">Nombre</label>
                    <div class="controls">
                      <input disabled id="nombre" type="text" name="nombre" value="<?=$cuenta->nombre?>"/>
                    </div>
                  </div>
                  <div class="control-group">
                    <label for="nombreL" class="control-label">Nombre largo</label>
                    <div class="controls">
                      <input class="input-xxlarge" id="nombreL" type="text" name="nombre_largo" value="<?=$cuenta->nombre_largo?>"/>
                    </div>
                  </div>
                  <!-- div class="control-group">
                    <label for="mensaje" class="control-label">Mensaje de bienvenida (Puede contener HTML)</label>
                    <div class="controls">
                      <textarea name="mensaje" id="mensaje" class="input-xxlarge"><?=$cuenta->mensaje?></textarea>
                    </div>
                  </div -->
                  <div class="control-group">
                    <label for="codigo_analytics" class="control-label">Código Google Analytics</label>
                    <div class="controls">
                      <textarea name="codigo_analytics" id="codigo_analytics" class="input-xxlarge"><?=json_decode($cuenta->codigo_analytics)?></textarea>
                    </div>
                  </div>
                  <div class="control-group">
                    <span class="control-label">Logo</span>
                    <div class="controls">
                      <div id="file-uploader"></div>
                      <input type="hidden" name="logo" value="<?=$cuenta->logo?>" />
                      <img class="logo" src="<?=$cuenta->logo?base_url('uploads/logos/'.$cuenta->logo):base_url('assets/img/simple.png')?>" alt="logo" />
                      <script>
                          var uploader = new qq.FileUploader({
                              element: document.getElementById('file-uploader'),
                              action: site_url+'backend/uploader/logo',
                              onComplete: function(id,filename,respuesta){
                                  $("input[name=logo]").val(respuesta.file_name);
                                  $("img.logo").attr("src",base_url+"uploads/logos/"+respuesta.file_name);
                              }
                          });
                      </script>
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

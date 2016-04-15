<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('backend/procesos') ?>">Listado de Procesos</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="<?= site_url('backend/formularios/listar/' . $proceso->id) ?>"><?= $proceso->nombre ?></a> <span class="divider">/</span>
    </li>
    <li class="active"><?= $edit ? $documento->nombre : '' ?></li>
</ul>

<h2><?=$proceso->nombre?></h2>
<ul class="nav nav-tabs">
    <li><a href="<?= site_url('backend/procesos/editar/' . $proceso->id) ?>">Diseñador</a></li>
    <li><a href="<?= site_url('backend/formularios/listar/' . $proceso->id) ?>">Formularios</a></li>
    <li class="active"><a href="<?= site_url('backend/documentos/listar/' . $proceso->id) ?>">Documentos</a></li>
    <li><a href="<?= site_url('backend/acciones/listar/' . $proceso->id) ?>">Acciones</a></li>
    <li><a href="<?= site_url('backend/trazabilidad/listar/' . $proceso->id) ?>">Trazabilidad</a></li>
</ul>

<script>
    $(document).ready(function() {
        handleRadio();
        $("input[name=tipo]").change(handleRadio);

        function handleRadio() {
            var value = $("input[name=tipo]:checked").val();
            if (value == "blanco") {
                $("#certificadoArea").hide();
            } else {
                $("#certificadoArea").show();
            }
        }
    });
</script>

<form class="ajaxForm" method="POST" action="<?= site_url('backend/documentos/editar_form/' . ($edit ? $documento->id : '')) ?>">
    <div class="titulo-form">
      <h3><?= $edit ? $documento->nombre : 'Documento' ?></h3>
    </div>
    <div class="validacion validacion-error"></div>
    <?php if (!$edit): ?>
        <input type="hidden" name="proceso_id" value="<?= $proceso->id ?>" />
    <?php endif; ?>
    <fieldset>
        <legend>Datos generales</legend>
        <div class="form-horizontal">
          <div class="control-group">
            <label for="nombre" class="control-label">Nombre</label>
            <div class="controls">
              <input type="text" id="nombre" name="nombre" value="<?= $edit ? $documento->nombre : '' ?>" />
            </div>
          </div>
          <div class="control-group">
            <span class="control-label">Tipo de documento</span>
            <div class="controls">
              <label class="radio" for="blanco"><input type="radio" id="blanco" name="tipo" value="blanco" <?= !$edit || ($edit && $documento->tipo) == 'blanco' ? 'checked' : '' ?> /> En blanco</label>
              <label class="radio" for="certificado"><input id="certificado" type="radio" name="tipo" value="certificado" <?= $edit && $documento->tipo == 'certificado' ? 'checked' : '' ?> /> Certificado</label>
            </div>
          </div>
        </div>
      </fieldset>
      <fieldset>
          <legend>Otros datos</legend>
          <div id="certificadoArea" class="form-horizontal">
            <div class="control-group">
              <label for="titulo" class="control-label">Título</label>
              <div class="controls">
                <input class="input-xxlarge" id="titulo" type="text" name="titulo" value="<?= $edit ? $documento->titulo : '' ?>" placeholder="Ej: Certificado de Educación" />
              </div>
            </div>
            <div class="control-group">
                <label for="subtitulo" class="control-label">Subtítulo</label>
                <div class="controls">
                  <input class="input-xxlarge" id="subtitulo" type="text" name="subtitulo" value="<?= $edit ? $documento->subtitulo : '' ?>" placeholder="Ej: Certificado Gratuito" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="servicio">Servicio que emite el documento</label>
                <div class="controls">
                  <input class="input-xxlarge" id="servicio" type="text" name="servicio" value="<?= $edit ? $documento->servicio : '' ?>" placeholder="Ej: Ministerio Secretaría General de la Presidencia" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="url">URL al sitio web del servicio</label>
                <div class="controls">
                  <input class="input-xxlarge" id="url" type="text" name="servicio_url" value="<?= $edit ? $documento->servicio_url : '' ?>" placeholder="Ej: http://www.minsegpres.gob.cl" />
                </div>
            </div>
            <div class="control-group">
                <span class="control-label">Logo del Servicio (Opcional)</span>
                <div class="controls">
                  <div id="file-uploader-logo"></div>
                  <input type="hidden" id="logoServicio" name="logo" value="<?= $edit ? $documento->logo : '' ?>" />
                  <img class="logo" src="<?= $edit && $documento->logo ? site_url('backend/uploader/logo_certificado_get/' . $documento->logo) : '#' ?>" alt="" width="200" />
                  <script>
                      var uploader = new qq.FileUploader({
                          element: document.getElementById('file-uploader-logo'),
                          action: site_url + 'backend/uploader/logo_certificado',
                          onComplete: function(id, filename, respuesta) {
                              $("input[name=logo]").val(respuesta.file_name);
                              $("img.logo").attr("src", site_url + "backend/uploader/logo_certificado_get/" + respuesta.file_name);
                          }
                      });
                  </script>
                </div>
            </div>
            <div class="control-group">
                <span class="control-label">Imagen del timbre (Opcional)</span>
                <div class="controls">
                  <div id="file-uploader-timbre"></div>
                  <input type="hidden" id="imgTimbre" name="timbre" value="<?= $edit ? $documento->timbre : '' ?>" />
                  <img class="timbre" src="<?= $edit && $documento->timbre ? site_url('backend/uploader/timbre_get/' . $documento->timbre) : '#' ?>" alt="" width="200" />
                  <script>
                      var uploader = new qq.FileUploader({
                          element: document.getElementById('file-uploader-timbre'),
                          action: site_url + 'backend/uploader/timbre',
                          onComplete: function(id, filename, respuesta) {
                              $("input[name=timbre]").val(respuesta.file_name);
                              $("img.timbre").attr("src", site_url + "backend/uploader/timbre_get/" + respuesta.file_name);
                          }
                      });
                  </script>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="nombrePersona">Nombre de la persona que firma (Opcional)</label>
                <div class="controls">
                  <input class="input-xxlarge" id="nombrePersona" type="text" name="firmador_nombre" value="<?= $edit ? $documento->firmador_nombre : '' ?>" placeholder="Ej: Juan Perez" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="firmador_cargo">Cargo de la persona que firma (Opcional)</label>
                <div class="controls">
                  <input id="firmador_cargo" class="input-xxlarge" type="text" name="firmador_cargo" value="<?= $edit ? $documento->firmador_cargo : '' ?>" placeholder="Ej: Jefe de Servicio" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="firmador_servicio">Servicio al que pertenece la persona que firma (Opcional)</label>
                <div class="controls">
                  <input id="firmador_servicio" class="input-xxlarge" type="text" name="firmador_servicio" value="<?= $edit ? $documento->firmador_servicio : '' ?>" placeholder="Ej: Ministerio Secretaría General de la Presidencia" />
                </div>
            </div>
            <div class="control-group">
                <span class="control-label">Imagen de la firma</span>
                <div class="controls">
                  <div id="file-uploader"></div>
                  <input type="hidden" name="firmador_imagen" value="<?= $edit ? $documento->firmador_imagen : '' ?>" />
                  <div id="firmaPreview" class="<?=$edit && $documento->firmador_imagen?'':'hidden'?>">
                      <img src="<?= $edit && $documento->firmador_imagen?site_url('backend/uploader/firma_get/' . $documento->firmador_imagen):'#' ?>" alt="firma" width="200" />
                      <a href="#">Quitar</a>
                  </div>
                  <script>
                      $(document).ready(function(){
                          var uploader = new qq.FileUploader({
                              element: document.getElementById('file-uploader'),
                              action: site_url + 'backend/uploader/firma',
                              onComplete: function(id, filename, respuesta) {
                                  $("input[name=firmador_imagen]").val(respuesta.file_name);
                                  $("#firmaPreview").show();
                                  $("#firmaPreview img").attr("src", site_url + "backend/uploader/firma_get/" + respuesta.file_name);
                              }
                          });

                          $("#firmaPreview a").click(function(){
                              $("input[name=firmador_imagen]").val("");
                              $("#firmaPreview").hide();
                              $("#firmaPreview img").attr("src","#");
                              return false;
                          });
                      });
                  </script>
              </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="validez">Numero de dias de validez (Dejar en blanco para periodo ilimitado)</label>
                <div class="controls">
                  <input id="validez" class="input-mini" type="text" name="validez" value="<?= $edit ? $documento->validez : '' ?>" placeholder="Ej: 90" />
                  <label  style="display: inline-block" class="checkbox" for="validez_habiles"><input id="validez_habiles" type="checkbox" name="validez_habiles" value="1" <?=$edit && $documento->validez_habiles ? 'checked':''?> /> Hábiles</label>
                  <script>
                  $(document).ready(function(){
                    $("input[name=validez]").keyup(function(){
                      if($(this).val().length>0){
                        $("input[name=validez_habiles]").prop("disabled",false);
                      }else{
                        $("input[name=validez_habiles]").prop("disabled",true);
                      }
                    }).keyup();
                  });
                  </script>
                </div>
            </div>
          </div>
          <div class="form-horizontal">
            <div class="control-group">
              <span class="control-label">Tamaño de la Página</span>
              <div class="controls">
                <label class="radio" for="carta"><input type="radio" id="carta" name="tamano" value="letter" <?= !$edit || ($edit && $documento->tamano) == 'letter' ? 'checked' : '' ?> /> Carta</label>
                <label class="radio" for="oficio"><input type="radio" id="oficio" name="tamano" value="legal" <?= $edit && $documento->tamano == 'legal' ? 'checked' : '' ?> /> Oficio</label>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="contenido">Contenido</label>
              <div class="controls">
                <textarea id="contenido" name="contenido" class="input-xxlarge" rows="20"><?= $edit ? $documento->contenido : '' ?></textarea>
                <div class="help-block">
                    <ul>
                        <li>Para incluir un salto de página puede usar: <?=htmlspecialchars('<br pagebreak="true" />')?></li>
                    </ul>
                </div>
              </div>
            </div>

            <?php if ($proceso->Cuenta->HsmConfiguraciones->count()): ?>
              <div class="control-group">
                <label class="control-label" for="hsm_configuracion_id">Firma Electronica Avanzada (HSM)</label>
                <div class="controls">
                  <select id="hsm_configuracion_id" name="hsm_configuracion_id">
                      <option value="">No firmar con HSM</option>
                      <?php foreach ($proceso->Cuenta->HsmConfiguraciones as $h): ?>
                          <option value="<?= $h->id ?>" <?= $edit && $documento->hsm_configuracion_id == $h->id ? 'selected' : '' ?>>Firmar con <?= $h->nombre ?></option>
                      <?php endforeach ?>
                  </select>
                </div>
              </div>
            <?php endif ?>
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
                    <a class="btn btn-link btn-lg" href="<?= site_url('backend/documentos/listar/' . $proceso->id) ?>">Cancelar</a>
                  </li>
              </ul>
          </li>
      </ul>
</form>




</div>

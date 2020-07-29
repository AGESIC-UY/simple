<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?= site_url('backend/configuracion') ?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li class="active">Plataforma de interoperabilidad</li>
        </ul>
        <a href="#" class="btn btn-ayuda btn-secundary" id="ayuda_contextual_pdi"><span class="icon-white icon-question-sign"></span> Ayuda</a>
        <h2>Plataforma de interoperabilidad: Configuración</h2>
        <form class="ajaxForm" method="post" action="<?= site_url('backend/configuracion/pdi_form/') ?>">
            <fieldset id="accion-pdi">
                <legend>Editar información para la conexión con PDI</legend>
                <div class="validacion validacion-error"></div>
                <div class="form-horizontal">
                    <div class="control-group">
                        <label for="sts" class="control-label">URL del servicio STS</label>
                        <div class="controls">
                            <input class="input-large" id="sts" type="text" name="sts" value="<?= ($pdi->sts != '' ? $pdi->sts : '') ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="policy" class="control-label">Nombre de la política (policy name)</label>
                        <div class="controls">
                            <input class="input-large" id="policy" type="text" name="policy" value="<?= ($pdi->policy != '' ? $pdi->policy : '') ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="certificado_organismo" class="control-label">Certificado de organismo (pkc12)</label>
                        <div class="controls">
                            <div id="file-uploader-org"></div>
                            <input  id="certificado_organismo" type="hidden" name="certificado_organismo" value="<?= ($pdi->certificado_organismo != '' ? $pdi->certificado_organismo : '') ?>" />
                            <?php
                            echo (file_exists(UBICACION_CERTIFICADOS_PDI . $pdi->certificado_organismo) && $pdi->certificado_organismo != '' ?
                                    '<span class="qq-upload-file"> <b>Certificado actual:</b> ' . $pdi->certificado_organismo . '</span>' : '');
                            ?>
                            <script>
                                var uploader = new qq.FileUploader({
                                    element: document.getElementById('file-uploader-org'),
                                    action: site_url + 'backend/uploader/pdi_certificados',
                                    onComplete: function (id, filename, respuesta) {
                                        $("input[name=certificado_organismo]").val(respuesta.file_name);
                                    }
                                });
                            </script>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="clave_organismo" class="control-label">Contraseña del certificado de organismo</label>
                        <div class="controls">
                            <input class="input-large" id="clave_organismo" type="password" name="clave_organismo" value="<?= ($pdi->clave_organismo != '' ? $pdi->clave_organismo : '') ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="certificado_ssl" class="control-label">Certificado SSL (pem)</label>
                        <div class="controls">
                            <div id="file-uploader-ssl"></div>
                            <input  id="certificado_ssl" type="hidden" name="certificado_ssl" value="<?= ($pdi->certificado_ssl != '' ? $pdi->certificado_ssl : '') ?>" />
                            <?php
                            echo (file_exists(UBICACION_CERTIFICADOS_PDI . $pdi->certificado_ssl) && $pdi->certificado_ssl != '' ?
                                    '<span class="qq-upload-file"> <b>Certificado SSL actual:</b> ' . $pdi->certificado_ssl . '</span>' : '');
                            ?>
                            <script>
                                var uploader = new qq.FileUploader({
                                    element: document.getElementById('file-uploader-ssl'),
                                    action: site_url + 'backend/uploader/pdi_certificados',
                                    onComplete: function (id, filename, respuesta) {
                                        $("input[name=certificado_ssl]").val(respuesta.file_name);
                                    }
                                });
                            </script>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="clave_ssl" class="control-label">Contraseña del certificado SSL</label>
                        <div class="controls">
                            <input class="input-large" id="clave_ssl" type="password" name="clave_ssl" value="<?= ($pdi->clave_ssl != '' ? $pdi->clave_ssl : '') ?>"/>
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
                            <a class="btn btn-link btn-lg" href="<?= site_url('backend/configuracion/') ?>">Cancelar</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </form>

    </div>
</div>

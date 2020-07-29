<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('backend/pasarela_pagos') ?>">Pasarela de Pagos</a> <span class="divider">/</span>
    </li>
    <li class="active"><?= $pasarela->nombre ?></li>
</ul>
<h2><?= $pasarela->nombre ?></h2>
<form class="ajaxForm" action="<?= site_url('backend/pasarela_pagos/editar_form/' . $pasarela->id) ?>" method="post">
    <div class="validacion validacion-error"></div>
    <fieldset>
        <legend>Datos generales</legend>
        <div class="form-horizontal">
            <div class="control-group">
                <div class="controls">
                    <label class="checkbox" for="servicio_activo"><input type="checkbox" id="servicio_activo" name="activo" value="<?= $pasarela->activo ?>" <?= ($pasarela->activo == 1) ? 'checked' : ''; ?> />Activa</label>
                </div>
            </div>
            <div class="control-group">
                <label for="nombre" class="control-label">Nombre</label>
                <div class="controls">
                    <input class="input-xlarge" id="nombre" type="text" value="<?= $pasarela->nombre ?>" name="nombre" /><br />
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo" class="control-label">Método</label>
                <div class="controls">
                    <select class="input-xlarge" id="pasarela_metodo" name="metodo">
                        <option value="">-- Seleccione el método --</option>
                        <option value="antel" <?= ($pasarela->metodo == 'antel' ? 'selected' : '') ?>>Antel</option>
                        <option value="generico" <?= ($pasarela->metodo == 'generico' ? 'selected' : '') ?>>Genérico</option>
                    </select>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset id="pasarela_metodo_antel" class="pasarela_metodo_form <?= ($pasarela->metodo == 'antel' ? '' : 'hidden') ?>">
        <legend>Datos del método</legend>
        <input type="hidden" name="pasarela_metodo_antel_id" value="<?= (isset($pasarela_metodo->id) ? $pasarela_metodo->id : '') ?>" />
        <div class="form-horizontal">
            <div class="control-group">
                <label for="id" class="control-label">ID de tramite</label>
                <div class="controls">
                    <input type="text" id="id" name="pasarela_metodo_antel_id_tramite" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->id_tramite : '') ?>" />
                    <input type="hidden" name="pasarela_metodo_antel_cantidad" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->cantidad : 1) ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="id" class="control-label">ID de organismo</label>
                <div class="controls">
                    <input type="text" id="id_organismo" name="pasarela_metodo_antel_id_organismo" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->id_organismo : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="tasa_1" class="control-label">Tasa 1</label>
                <div class="controls">
                    <input type="text" id="tasa_1" name="pasarela_metodo_antel_tasa_1" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->tasa_1 : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="tasa_2" class="control-label">Tasa 2</label>
                <div class="controls">
                    <input type="text" id="tasa_2" name="pasarela_metodo_antel_tasa_2" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->tasa_2 : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="tasa_3" class="control-label">Tasa 3</label>
                <div class="controls">
                    <input type="text" id="tasa_3" name="pasarela_metodo_antel_tasa_3" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->tasa_3 : '') ?>" />
                    <input type="hidden" name="pasarela_metodo_antel_operacion" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->operacion : 'P') ?>" />
                </div>
            </div>
            <div class="control-group">
                <span class="control-label">Vencimiento (AAAA/MM/DD HH:mm)</span>
                <div class="controls">
                    <input type="text" id="pasarela_pago_vencimiento" name="pasarela_metodo_antel_vencimiento" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->vencimiento : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="desglose" class="control-label">Códigos de desglose</label>
                <div class="controls">
                    <input type="text" id="desglose" name="pasarela_metodo_antel_codigos_desglose" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->codigos_desglose : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="montos_desglose" class="control-label">Montos de desglose</label>
                <div class="controls">
                    <input type="text" id="montos_desglose" name="pasarela_metodo_antel_montos_desglose" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->montos_desglose : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="clave_organismo" class="control-label">Clave organismo</label>
                <div class="controls">
                    <input type="password" id="clave_organismo" name="pasarela_metodo_antel_clave_organismo" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->clave_organismo : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="clave_tramite" class="control-label">Clave trámite</label>
                <div class="controls">
                    <input type="password" id="clave_tramite" name="pasarela_metodo_antel_clave_tramite" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->clave_tramite : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_certificado" class="control-label">Certificado SSL</label>
                <div class="controls">
                    <div id="file-uploader-certificado"></div>
                    <input id="certificado" type="hidden" name="pasarela_metodo_antel_certificado" value="<?php
                            if ($pasarela->metodo == 'antel') {
                                echo ($pasarela_metodo->certificado != '' ? $pasarela_metodo->certificado : '');
                            }
                            ?>" />
                                                <?php
                           if ($pasarela->metodo == 'antel') {
                               echo (file_exists(UBICACION_CERTIFICADOS_PASARELA . $pasarela_metodo->certificado) && $pasarela_metodo->certificado != '' ?
                                       '<span class="qq-upload-file"> <b>Certificado actual:</b> ' . $pasarela_metodo->certificado . '</span>' : '');
                           }
                           ?>
                    <script>
                        var uploader = new qq.FileUploader({
                            element: document.getElementById('file-uploader-certificado'),
                            action: site_url + 'backend/uploader/credenciales_pasarela_pago',
                            onComplete: function (id, filename, respuesta) {
                                $("input[name=pasarela_metodo_antel_certificado]").val(respuesta.file_name);
                            }
                        });
                    </script>
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_clave_certificado" class="control-label">Clave privada</label>
                <div class="controls">
                    <div id="file-uploader-clave"></div>
                    <input id="clave_certificado" type="hidden" name="pasarela_metodo_antel_clave_certificado" value="<?php
                           if ($pasarela->metodo == 'antel') {
                               echo ($pasarela_metodo->clave_certificado != '' ? $pasarela_metodo->clave_certificado : '');
                           }
                           ?>" />
                    <?php
                           if ($pasarela->metodo == 'antel') {
                               echo (file_exists(UBICACION_CERTIFICADOS_PASARELA . $pasarela_metodo->clave_certificado) && $pasarela_metodo->clave_certificado != '' ?
                                       '<span class="qq-upload-file"> <b>Clave actual:</b> ' . $pasarela_metodo->clave_certificado . '</span>' : '');
                           }
                           ?>
                    <script>
                        var uploader = new qq.FileUploader({
                            element: document.getElementById('file-uploader-clave'),
                            action: site_url + 'backend/uploader/credenciales_pasarela_pago',
                            onComplete: function (id, filename, respuesta) {
                                $("input[name=pasarela_metodo_antel_clave_certificado]").val(respuesta.file_name);
                            }
                        });
                    </script>
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_pass_clave_certificado" class="control-label">Contraseña de la clave privada</label>
                <div class="controls">
                    <input type="password" id="pass_clave_certificado" name="pasarela_metodo_antel_pass_clave_certificado" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->pass_clave_certificado : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_referencia_pago" class="control-label">Referencia de pago</label>
                <div class="controls">
                    <input type="text" id="referencia_pago" name="pasarela_metodo_antel_referencia_pago" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->referencia_pago : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_tema_email_inicio" class="control-label">Tema de email al inicio del pago</label>
                <div class="controls">
                    <input type="text" id="tema_email_inicio" name="pasarela_metodo_antel_tema_email_inicio" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->tema_email_inicio : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_cuerpo_email_inicio" class="control-label">Cuerpo de email al inicio del pago</label>
                <div class="controls">
                    <textarea id="cuerpo_email_inicio" name="pasarela_metodo_antel_cuerpo_email_inicio" class="input-xlarge"><?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->cuerpo_email_inicio : '') ?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_tema_email_ok" class="control-label">Tema de email en pago realizado</label>
                <div class="controls">
                    <input type="text" id="tema_email_ok" name="pasarela_metodo_antel_tema_email_ok" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->tema_email_ok : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_cuerpo_email_ok" class="control-label">Cuerpo de email en pago realizado</label>
                <div class="controls">
                    <textarea id="cuerpo_email_ok" name="pasarela_metodo_antel_cuerpo_email_ok" class="input-xlarge"><?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->cuerpo_email_ok : '') ?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_tema_email_pendiente" class="control-label">Tema de email en pago pendiente (red de cobranzas)</label>
                <div class="controls">
                    <input type="text" id="tema_email_pendiente" name="pasarela_metodo_antel_tema_email_pendiente" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->tema_email_pendiente : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_cuerpo_email_pendiente" class="control-label">Cuerpo de email en pago pendiente (red de cobranzas)</label>
                <div class="controls">
                    <textarea id="cuerpo_email_pendiente" name="pasarela_metodo_antel_cuerpo_email_pendiente" class="input-xlarge"><?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->cuerpo_email_pendiente : '') ?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_tema_email_timeout" class="control-label">Tema de email al fallar estado (timeout)</label>
                <div class="controls">
                    <input type="text" id="tema_email_timeout" name="pasarela_metodo_antel_tema_email_timeout" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->tema_email_timeout : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_cuerpo_email_timeout" class="control-label">Cuerpo de email al fallar estado (timeout)</label>
                <div class="controls">
                    <textarea id="cuerpo_email_timeout" name="pasarela_metodo_antel_cuerpo_email_timeout" class="input-xlarge"><?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->cuerpo_email_timeout : '') ?></textarea>
                </div>
            </div>

            <div class="control-group">
                <label for="pasarela_metodo_antel_descripcion_pendiente_traza" class="control-label">Descripción trazabilidad estado pendiente</label>
                <div class="controls">
                    <input type="text" id="pasarela_metodo_antel_descripcion_pendiente_traza" name="pasarela_metodo_antel_descripcion_pendiente_traza" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->descripcion_pendiente_traza : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_descripcion_iniciado_traza" class="control-label">Descripción trazabilidad estado iniciado</label>
                <div class="controls">
                    <input type="text" id="pasarela_metodo_antel_descripcion_iniciado_traza" name="pasarela_metodo_antel_descripcion_iniciado_traza" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->descripcion_iniciado_traza : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_descripcion_token_solicita_traza" class="control-label">Descripción trazabilidad estado solicita token</label>
                <div class="controls">
                    <input type="text" id="pasarela_metodo_antel_descripcion_token_solicita_traza" name="pasarela_metodo_antel_descripcion_token_solicita_traza" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->descripcion_token_solicita_traza : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_descripcion_realizado_traza" class="control-label">Descripción trazabilidad estado realizado</label>
                <div class="controls">
                    <input type="text" id="pasarela_metodo_antel_descripcion_realizado_traza" name="pasarela_metodo_antel_descripcion_realizado_traza" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->descripcion_realizado_traza : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_descripcion_error_traza" class="control-label">Descripción trazabilidad estado error</label>
                <div class="controls">
                    <input type="text" id="pasarela_metodo_antel_descripcion_error_traza" name="pasarela_metodo_antel_descripcion_error_traza" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->descripcion_error_traza : '') ?>" />
                </div>
            </div>
            <div class="control-group">
                <label for="pasarela_metodo_antel_descripcion_reachazado_traza" class="control-label">Descripción trazabilidad estado rechazado</label>
                <div class="controls">
                    <input type="text" id="pasarela_metodo_antel_ddescripcion_reachazado_traza" name="pasarela_metodo_antel_descripcion_reachazado_traza" class="input-xlarge" value="<?= ($pasarela->metodo == 'antel' ? $pasarela_metodo->descripcion_reachazado_traza : '') ?>" />
                </div>
            </div>
        </div>
    </fieldset>
<?php if ($pasarela->metodo == 'generico') { ?>
        <fieldset id="pasarela_metodo_generico" class="pasarela_metodo_form <?= ($pasarela->metodo == 'generico' ? '' : 'hidden') ?>">
            <legend>Datos del método</legend>
            <input type="hidden" name="pasarela_metodo_generico_id" value="<?= (isset($pasarela_metodo->id) ? $pasarela_metodo->id : '') ?>" />
            <div class="form-horizontal">
                <div class="control-group">
                    <label for="codigo_operacion_soap" class="control-label">Código de operación SOAP (solicitud)</label>
                    <div class="controls">
                        <input type="text" id="codigo_operacion_soap" name="pasarela_metodo_generico_codigo_operacion_soap" class="input-xlarge" value="<?= ($pasarela->metodo == 'generico' ? $pasarela_metodo->codigo_operacion_soap : '') ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="codigo_operacion_soap_consulta" class="control-label">Código de operación SOAP (consulta)</label>
                    <div class="controls">
                        <input type="text" id="codigo_operacion_soap_consulta" name="pasarela_metodo_generico_codigo_operacion_soap_consulta" class="input-xlarge" value="<?= ($pasarela->metodo == 'generico' ? $pasarela_metodo->codigo_operacion_soap_consulta : '') ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="variable_evaluar" class="control-label">Variable a evaluar (consulta)</label>
                    <div class="controls">
                        <input type="text" id="variable_evaluar" name="pasarela_metodo_generico_variable_evaluar" class="input-xlarge" value="<?= ($pasarela->metodo == 'generico' ? $pasarela_metodo->variable_evaluar : '') ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="variable_idsol" class="control-label">Variable de ID con pasarela (por ejemplo: token o ID de solicitud)</label>
                    <div class="controls">
                        <input type="text" id="variable_idsol" name="pasarela_metodo_generico_variable_idsol" class="input-xlarge" value="<?= ($pasarela->metodo == 'generico' ? $pasarela_metodo->variable_idsol : '') ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="variable_idestado" class="control-label">Variable ID de estado de pasarela</label>
                    <div class="controls">
                        <input type="text" id="variable_idestado" name="pasarela_metodo_generico_variable_idestado" class="input-xlarge" value="<?= ($pasarela->metodo == 'generico' ? $pasarela_metodo->variable_idestado : '') ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="url_redireccion" class="control-label">Código de operación SOAP o URL de redirección</label>
                    <div class="controls">
                        <input type="text" id="url_redireccion" name="pasarela_metodo_generico_url_redireccion" class="input-xlarge" value="<?= ($pasarela->metodo == 'generico' ? $pasarela_metodo->url_redireccion : '') ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="variable_redireccion" class="control-label">Variable de redirección</label>
                    <div class="controls">
                        <input type="text" id="variable_redireccion" name="pasarela_metodo_generico_variable_redireccion" class="input-xlarge" value="<?= ($pasarela->metodo == 'generico' ? $pasarela_metodo->variable_redireccion : '') ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="pasarela_metodo_generico_metodo_http" class="control-label">Método HTTP</label>
                    <div class="controls">
                        <select id="pasarela_metodo_generico_metodo_http" name="pasarela_metodo_generico_metodo_http" class="input-xlarge">
                            <option value="get" <?= ($pasarela_metodo->metodo_http == 'get' ? 'selected' : '') ?>>GET</option>
                            <option value="post" <?= ($pasarela_metodo->metodo_http == 'post' ? 'selected' : '') ?>>POST</option>
                        </select>
                    </div>
                </div>
                <div class="control-group <?= (!empty($pasarela_metodo->variables_post && $pasarela_metodo->metodo_http == 'post') ? '' : 'hidden') ?>" id="pasarela_metodo_generico_variables_post">
                    <label for="pasarela_metodo_generico_metodo_http_variable" class="control-label">Variables POST</label>
                    <div class="controls">
    <?php
    if ($pasarela_metodo->variables_post != '""') {
        $variables = json_decode($pasarela_metodo->variables_post);
        $count = 1;
        $se_encontro_algun_valor = false;
        foreach ($variables as $variable) {
            if (strlen($variable->nombre) > 0 || strlen($variable->valor) > 0) {
                $se_encontro_algun_valor = true;
                if ($count == 1) {
                    ?>
                                        <span><input type="text" name="pasarela_metodo_generico_metodo_http_variable[1][nombre]" class="input-medium" placeholder="Nombre" value="<?= $variable->nombre ?>" /> <input type="text" name="pasarela_metodo_generico_metodo_http_variable[1][valor]" class="input-medium" placeholder="Valor" value="<?= $variable->valor ?>" /> <span id="add_pasarela_metodo_generico_metodo_http_variable" class="icon-plus btn"></span><br /><br /></span>
                                        <?php
                                    } else {
                                        ?>
                                        <span><input type="text" name="pasarela_metodo_generico_metodo_http_variable[<?= $count ?>][nombre]" class="input-medium" placeholder="Nombre" value="<?= $variable->nombre ?>" /> <input type="text" name="pasarela_metodo_generico_metodo_http_variable[<?= $count ?>][valor]" class="input-medium" placeholder="Valor" value="<?= $variable->valor ?>" /><br /><br /></span>
                                        <?php
                                    }

                                    $count++;
                                }
                            }
                            if (!$se_encontro_algun_valor) {
                                ?>
                                <span>
                                    <input type="text" name="pasarela_metodo_generico_metodo_http_variable[1][nombre]" class="input-medium" placeholder="Nombre" value="" />
                                    <input type="text" name="pasarela_metodo_generico_metodo_http_variable[1][valor]" class="input-medium" placeholder="Valor" value="" />
                                    <span id="add_pasarela_metodo_generico_metodo_http_variable" class="icon-plus btn"></span>
                                    <br>
                                    <br>
                                </span>

        <?php }
        ?>
                            <input value="<?= $count ?>" type="hidden" id="total_variables" />
                        <?php } else {
                            ?>
                            <span>
                                <input type="text" name="pasarela_metodo_generico_metodo_http_variable[1][nombre]" class="input-medium" placeholder="Nombre" value="" />
                                <input type="text" name="pasarela_metodo_generico_metodo_http_variable[1][valor]" class="input-medium" placeholder="Valor" value="" />
                                <span id="add_pasarela_metodo_generico_metodo_http_variable" class="icon-plus btn"></span>
                                <br>
                                <br>
                            </span>
    <?php } ?>
                    </div>
                </div>
                <div class="control-group">
                    <label for="mensaje_reimpresion_ticket" class="control-label">Mensaje de reimpresión de ticket</label>
                    <div class="controls">
                        <input type="text" id="mensaje_reimpresion_ticket" name="pasarela_metodo_generico_mensaje_reimpresion_ticket" class="input-xlarge" value="<?= ($pasarela->metodo == 'generico' ? $pasarela_metodo->mensaje_reimpresion_ticket : '') ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="url_ticket" class="control-label">URL de reimpresión de ticket</label>
                    <div class="controls">
                        <input type="text" id="url_ticket" name="pasarela_metodo_generico_url_ticket" class="input-xlarge" value="<?= ($pasarela->metodo == 'generico' ? $pasarela_metodo->url_ticket : '') ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="pasarela_metodo_generico_ticket_metodo" class="control-label">Método HTTP de ticket</label>
                    <div class="controls">
                        <select id="pasarela_metodo_generico_ticket_metodo" name="pasarela_metodo_generico_ticket_metodo" class="input-xlarge">
                            <option value="get" <?= ($pasarela_metodo->ticket_metodo == 'get' ? 'selected' : '') ?>>GET</option>
                            <option value="post" <?= ($pasarela_metodo->ticket_metodo == 'post' ? 'selected' : '') ?>>POST</option>
                        </select>
                    </div>
                </div>
                <div class="control-group <?= (!empty($pasarela_metodo->ticket_variables && $pasarela_metodo->ticket_metodo == 'post') ? '' : 'hidden') ?>" id="pasarela_metodo_generico_ticket_variables">
                    <label for="pasarela_metodo_generico_ticket_variables" class="control-label">Variables POST de ticket</label>
                    <div class="controls">
    <?php
    if ($pasarela_metodo->ticket_variables != '""') {
        $variables = json_decode($pasarela_metodo->ticket_variables);
        $count = 1;
        foreach ($variables as $variable) {
            if ($count == 1) {
                ?>
                                    <span><input type="text" name="pasarela_metodo_generico_ticket_variables[1][nombre]" class="input-medium" placeholder="Nombre" value="<?= $variable->nombre ?>" /> <input type="text" name="pasarela_metodo_generico_ticket_variables[1][valor]" class="input-medium" placeholder="Valor" value="<?= $variable->valor ?>" /> <span id="add_pasarela_metodo_generico_ticket_variables" class="icon-plus btn"></span><br /><br /></span>
                                    <?php
                                } else {
                                    ?>
                                    <span><input type="text" name="pasarela_metodo_generico_ticket_variables[<?= $count ?>][nombre]" class="input-medium" placeholder="Nombre" value="<?= $variable->nombre ?>" /> <input type="text" name="pasarela_metodo_generico_ticket_variables[<?= $count ?>][valor]" class="input-medium" placeholder="Valor" value="<?= $variable->valor ?>" /><br /><br /></span>
                                    <?php
                                }

                                $count++;
                            }
                            ?>
                            <input value="<?= $count ?>" type="hidden" id="total_ticket_variables" />
                        <?php }
                        ?>
                    </div>
                </div>
                <div class="control-group">
                    <label for="pasarela_metodo_generico_tema_email_inicio" class="control-label">Tema de email al inicio del pago</label>
                    <div class="controls">
                        <input type="text" id="pasarela_metodo_generico_tema_email_inicio" name="pasarela_metodo_generico_tema_email_inicio" class="input-xlarge" value="<?= ($pasarela->metodo == 'generico' ? $pasarela_metodo->tema_email_inicio : '') ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="pasarela_metodo_generico_cuerpo_email_inicio" class="control-label">Cuerpo de email al inicio del pago</label>
                    <div class="controls">
                        <textarea id="pasarela_metodo_generico_cuerpo_email_inicio" name="pasarela_metodo_generico_cuerpo_email_inicio" class="input-xlarge"><?= ($pasarela->metodo == 'generico' ? $pasarela_metodo->cuerpo_email_inicio : '') ?></textarea>
                    </div>
                </div>

                <div class="control-group" id="pasarela_metodo_generico_descripciones_estados_traza">
                    <label for="pasarela_metodo_generico_descripciones_estados_traza" class="control-label">Descripciones estados trazabilidad</label>
                    <div class="controls">
    <?php
    $count_descp = 0;
    if ($pasarela_metodo->descripciones_estados_traza != null) {
        $variables = json_decode($pasarela_metodo->descripciones_estados_traza);
        $count_descp = 1;
        foreach ($variables as $variable) {
            if ($count_descp == 1) {
                ?>
                                    <span><input type="text" name="pasarela_metodo_generico_descripciones_estados_traza[1][codigo]" class="input-medium" placeholder="Código" value="<?= $variable->codigo ?>" /> <input type="text" name="pasarela_metodo_generico_descripciones_estados_traza[1][valor]" class="input-medium" placeholder="Valor" value="<?= $variable->valor ?>" /> <span id="add_pasarela_metodo_generico_descripciones_estados_traza" class="icon-plus btn"></span><br /><br /></span>
                                    <?php
                                } else {
                                    ?>
                                    <span><input type="text" name="pasarela_metodo_generico_descripciones_estados_traza[<?= $count_descp ?>][codigo]" class="input-medium" placeholder="Código" value="<?= $variable->codigo ?>" /> <input type="text" name="pasarela_metodo_generico_descripciones_estados_traza[<?= $count_descp ?>][valor]" class="input-medium" placeholder="Valor" value="<?= $variable->valor ?>" /><br /><br /></span>
                                    <?php
                                }

                                $count_descp++;
                            }
                        } else {
                            ?>
                            <span>
                                <input type="text" name="pasarela_metodo_generico_descripciones_estados_traza[1][codigo]" class="input-medium" placeholder="Código" value="" />
                                <input type="text" name="pasarela_metodo_generico_descripciones_estados_traza[1][valor]" class="input-medium" placeholder="Valor" value="" />
                                <span id="add_pasarela_metodo_generico_descripciones_estados_traza" class="icon-plus btn"></span>
                                <br>
                                <br>
                            </span>

    <?php } ?>

                        <input value="<?= $count_descp ?>" type="hidden" id="total_descripciones_estados_traza" />
                    </div>
                </div>

            </div>
        </fieldset>
<?php } ?>
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
                    <a class="btn btn-link btn-lg" href="<?= site_url('backend/pasarela_pagos') ?>">Cancelar</a>
                </li>
            </ul>
        </li>
    </ul>

</form>

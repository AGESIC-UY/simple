<h1>Trámites de Ciudadano</h1>

<form class="ajaxForm" method="post" action="<?= site_url('etapas/registro_ciudadano_form/') ?>">
  <div class="aviso_campos_obligatorios">Los campos indicados con * son obligatorios.</div>
    <fieldset>
        <legend>Registro nuevo ciudadano</legend>
        <div class="validacion validacion-error"></div>
          <div class="form-horizontal">
          <div class="control-group">
            <label for="nombres" class="control-label">Nombres*</label>
            <div class="controls">
              <input type="text" id="nombres" name="nombres"/>
            </div>
          </div>

          <div class="control-group">
            <label for="apellido_paterno" class="control-label">Primer Apellido*:</label>
            <div class="controls">
              <input type="text" id="apellido_paterno" name="apellido_paterno"/>
            </div>
          </div>

          <div class="control-group">
            <label for="apellido_materno" class="control-label">Segundo Apellido*:</label>
            <div class="controls">
              <input type="text" id="apellido_materno" name="apellido_materno"/>
            </div>
          </div>

          <div class="control-group">
            <label for="email" class="control-label">Correo electrónico:</label>
            <div class="controls">
              <input type="text" id="email" name="email"/>
            </div>
          </div>

          <div class="control-group">
            <label for="documento" class="control-label">Documento*:</label>
            <div class="controls">
              <input type="text" id="documento" name="documento" value="<?php if(isset($documento)) echo $documento; ?>"/>
            </div>
          </div>

          <div class="control-group">
            <label for="tipo_documento" class="control-label">Tipo de documento*:</label>
            <div class="controls">
              <select id="tipo_documento"  name="tipo_documento">
                <?php if(isset($tipo_documento)): ?>
                  <option value="<?php if(isset($tipo_documento)) echo $tipo_documento; ?>"><?php if(isset($tipo_documento)) echo $tipo_documento; ?></option>
                <?php else: ?>
                  <option value=""selected></option>
                <?php endif;?>
              </select>
            </div>
          </div>

            <div class="control-group">
              <label for="pais" class="control-label">País*:</label>
              <div class="controls">
                <select id="pais"  name="pais">
                  <?php if(isset($pais)): ?>
                      <option value="<?php if(isset($pais)) echo $pais; ?>"><?php if(isset($pais)) echo $pais; ?></option>
                  <?php else: ?>
                    <option value=""selected></option>
                  <?php endif;?>
                </select>
              </div>
            </div>

          </div>
    </fieldset>

    <ul class="form-action-buttons">
        <li class="action-buttons-primary">
            <ul>
                <li>
                  <button class="btn btn-primary btn-lg" type="submit">Registrar</button>
                </li>
            </ul>
        </li>

        <li class="action-buttons-second">
          <ul>
              <li class="float-left">
                <a class="btn btn-link btn-lg" href="<?=site_url('etapas/busqueda_ciudadano')?>">Cancelar</a>
              </li>
          </ul>
        </li>

    </ul>
</form>

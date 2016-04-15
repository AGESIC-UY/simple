<div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3>Seleccione el tipo de acción</h3>
</div>
<div class="modal-body multiple-form">
    <form id="formAgregarAccion" class="ajaxForm form-horizontal" method="POST" action="<?= site_url('backend/acciones/seleccionar_form/'.$proceso_id) ?>">
        <div class="validacion validacion-error"></div>
        <div class="control-group">
          <label class="control-label" for="main_action_selector">Tipo de acción</label>
          <div class="controls">
            <select name="tipo" id="main_action_selector">
                <option value="" disabled selected>-- Seleccione acción --</option>
                <option value="enviar_correo">Enviar correo</option>
                <option value="webservice">Consultar Servicio</option>
                <option value="pasarela_pago">Pasarela de pago</option>
                <option value="variable">Generar Variable</option>
            </select>
          </div>
        </div>
        <div class="modal-footer" id="formAgregarAccion_button">
          <button type="submit" class="btn btn-primary">Continuar</button>
            <!-- a href="#" onclick="javascript:$('#formAgregarAccion').submit();return false;" class="btn btn-primary">Continuar</a -->
            <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
        </div>
    </form>

    <form id="formAgregarAccion_services" class="ajaxForm hidden form-horizontal" method="POST" action="<?= site_url('backend/acciones/seleccionar_form/'.$proceso_id) ?>">
      <div class="control-group">
        <label class="control-label" for="services_action_selector">Servicio</label>
        <div class="controls">
          <select name="tipo" id="services_action_selector">
              <option value="" disabled selected>-- Seleccione servicio --</option>
              <optgroup label="Servicios principales">

                  <?php foreach($servicios as $s) { ?>
                      <option value="<?=$s->id?>"><?=$s->nombre?></option>
                  <?php } ?>
              </optgroup>

              <optgroup label="Otros servicios">
                  <option value="webservice">Webservice REST</option>
              </optgroup>
          </select>
        </div>
      </div>

      <div class="modal-footer hidden" id="formAgregarAccion_button_services">
        <button type="submit" class="btn btn-primary">Continuar</button>
          <!-- a href="#" onclick="javascript:$('#formAgregarAccion_services').submit();return false;" class="btn btn-primary">Continuar</a -->
          <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
      </div>
    </form>

    <form id="formAgregarAccion_operations" class="ajaxForm hidden form-horizontal" method="POST" action="<?= site_url('backend/acciones/seleccionar_form/'.$proceso_id) ?>">
      <div class="control-group">
        <label class="control-label" for="operations_action_selector">Operación</label>
        <div class="controls">
          <select name="tipo" id="operations_action_selector">
              <option value="" disabled selected>-- Seleccione operación --</option>
              <?php foreach($operaciones as $o) { ?>
                  <optgroup label="Operaciones disponibles" class="hidden servicios_operaciones" id="servicios_operacion_<?=$o->catalogo_id?>">
                      <option value="webservice_extended" data-operacion-id="<?=$o->id?>" class="elemento_operacion"><?=$o->nombre?></option>
                  </optgroup>
              <?php } ?>
          </select>
          <input type="hidden" id="servicio_operacion_id" name="operacion" />
        </div>
      </div>
      <div class="modal-footer hidden" id="formAgregarAccion_button_operations">
        <button type="submit" class="btn btn-primary">Continuar</button>
          <!-- a href="#" onclick="javascript:$('#formAgregarAccion_operations').submit();return false;" class="btn btn-primary">Continuar</a -->
          <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
      </div>
    </form>

    <form id="formAgregarAccion_pasarela_pagos" class="ajaxForm hidden form-horizontal" method="POST" action="<?= site_url('backend/acciones/seleccionar_form/'.$proceso_id) ?>">
      <div class="control-group">
        <label class="control-label" for="pasarela_pagos_action_selector">Pasarela de pago</label>
        <div class="controls">
          <select name="tipo" id="pasarela_pagos_action_selector">
              <option value="" disabled selected>-- Seleccione la pasarela --</option>
              <?php foreach($pasarela_pagos as $p) { ?>
                  <option value="pasarela_pago" data-pasarela-id="<?=$p->id?>"><?=$p->nombre?></option>
              <?php } ?>
          </select>
        </div>
      </div>
      <div class="modal-footer hidden" id="formAgregarAccion_button_pasarela_pagos">
        <button type="submit" class="btn btn-primary">Continuar</button>
          <!-- a href="#" onclick="javascript:$('#formAgregarAccion_pasarela_pagos').submit();return false;" class="btn btn-primary">Continuar</a -->
          <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
      </div>
    </form>
</div>

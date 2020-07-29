<?php

require_once('accion.php');

class AccionTraza extends Accion {

    public function displayForm() {

        $display = '<div class="form-horizontal">';

        $display .= '<div class="control-group">';
        $display .= '<label for="descripcion" class="control-label">Descripción</label>';
        $display .= '<div class="controls">';
        $display .= '<input id="descripcion" type="text" name="extra[descripcion]" value="' . (isset($this->extra->descripcion) ? $this->extra->descripcion : '') . '" />';
        $display .= '</div>';
        $display .= '</div>';

        $display .= '<div class="control-group">
                    <label for="tipo_registro" class="control-label">Tipo de registro</label>
                    <div class="controls">
                    <select id="tipo_registro"  name="extra[tipo_registro]">';
        $id_tipo_registro_posibles_traza = unserialize(ID_TIPO_REGISTRO_POSIBLES_TRAZABILIDAD);
        $display .= '<option value="">Seleccionar</option>';
        foreach ($id_tipo_registro_posibles_traza as $estado_k => $estado_v) {
            if ($estado_k == $this->extra->tipo_registro) {
                $display .= '<option value="' . $estado_k . '" selected>' . $estado_v . '</option>';
            } else {
                $display .= '<option value="' . $estado_k . '">' . $estado_v . '</option>';
            }
        }
        $display .= '</select>
                    </div>
                  </div>';

        $display .= '<div class="control-group">
                    <label for="etiqueta_traza" class="control-label">Etiqueta</label>
                    <div class="controls">
                    <select id="etiqueta_traza"  name="extra[etiqueta_traza]">';
        $etiquetas= Doctrine::getTable('EtiquetaTraza')->findAll();
        foreach ($etiquetas as $etiqueta) {
            $display .= '<option value = "'.$etiqueta->etiqueta.'"'.(($etiqueta->etiqueta == $this->extra->etiqueta_traza )? "selected" : "").'> '.$etiqueta->etiqueta.'</option>';
        }
        $display .= '</select>
                    </div>
                     </div>';

        $display .= '<div class="control-group">
    <label for="visible_traza" class="control-label">Visibilidad</label>
    <div class="controls">
        <select id="visible_traza"  name="extra[visible_traza]">';
        $display .= '<option value="">Seleccionar</option>';
        $display .= '<option value="VISIBLE" ' . (("VISIBLE" == $this->extra->visible_traza) ? "selected" : "") . '>Visible</option>';
        $display .= '<option value="USO_INTERNO" ' . (("USO_INTERNO" == $this->extra->visible_traza) ? "selected" : "") . '>Uso interno</option>';
        $display .= '</select>
    </div>
</div>';

        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();

        $CI->form_validation->set_rules('extra[descripcion]', 'Descripción', 'required');
        $CI->form_validation->set_rules('extra[tipo_registro]', 'Tipo de registro', 'required');
    }

    public function ejecutar(Etapa $etapa, $evento = null) {
        $CI = & get_instance();
        $CI->load->helper('trazabilidad_helper');

        preg_match('/(' . $etapa->id . ')\/([0-9]*)/', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $match);
        if (!$match) {
            $secuencia = 0;

            $ejecutar_fin = strpos($_SERVER['REQUEST_URI'], '/ejecutar_fin_form/' . $etapa->id);
            if ($ejecutar_fin) {
                $secuencia = sizeof($etapa->getPasosEjecutables());
            }
        } else {
            $secuencia = (int) $match[2];
        }

        if ($ejecutar_fin) {
            enviar_traza_linea_accion_despues_tarea($etapa, $secuencia, $this->extra->descripcion, $this->extra->tipo_registro,$this->extra->etiqueta_traza, $this->extra->visible_traza);
        } else {
            enviar_traza_linea_accion($etapa, $secuencia, $this->extra->descripcion, $this->extra->tipo_registro,$this->extra->etiqueta_traza, $this->extra->visible_traza);
        }
    }

}

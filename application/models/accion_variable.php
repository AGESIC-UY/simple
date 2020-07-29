<?php
require_once('accion.php');

class AccionVariable extends Accion {

    public function displayForm() {

        $display = '<div class="form-horizontal">';
        $display.= '<div class="control-group">';
        $display.= '<label for="variable" class="control-label">Variable</label>';
        $display.='<div class="controls">';
        $display.='<input id="variable" type="text" name="extra[variable]" value="' . ($this->extra ? $this->extra->variable : '') . '" />';
        $display.='</div>';
        $display.='</div>';
        $display.= '<div class="control-group">';
        $display.= '<label for="expresion" class="control-label">Expresión a evaluar</label>';
        $display.='<div class="controls">';
        $display.='<textarea id="expresion" name="extra[expresion]" class="input-xxlarge">' . ($this->extra ? $this->extra->expresion : '') . '</textarea>';
        $display.='</div>';
        $display.='</div>';
        $display.='</div>';

        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
        $CI->form_validation->set_rules('extra[variable]', 'Variable', 'required');
        $CI->form_validation->set_rules('extra[expresion]', 'Expresión a evaluar', 'required');
    }

    public function ejecutar(Etapa $etapa, $evento = null) {
        $regla=new Regla($this->extra->expresion);
        $valor=$regla->evaluar($etapa->id);

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->extra->variable,$etapa->id);
        if (!$dato)
            $dato = new DatoSeguimiento();
        $dato->nombre = $this->extra->variable;
        $dato->valor = $valor;
        $dato->etapa_id = $etapa->id;
        $dato->save();

        //trazabilidad evento
        $this->trazar($etapa, $evento);
    }

    private function trazar($etapa, $evento){
      if($evento){
        $CI = & get_instance();
        $CI->load->helper('trazabilidad_helper');

        $ejecutar_fin = false;

        preg_match('/('. $etapa->id .')\/([0-9]*)/', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $match);
        if(!$match) {
          $secuencia = 0;

          $ejecutar_fin = strpos( $_SERVER['REQUEST_URI'], '/ejecutar_fin_form/'.$etapa->id);
          if($ejecutar_fin) {
            $secuencia = sizeof($etapa->getPasosEjecutables());
          }
        }
        else {
          $secuencia = (int)$match[2];
        }

        if($ejecutar_fin){
          enviar_traza_linea_evento_despues_tarea($etapa, $secuencia, $evento);
        }
        else{
          enviar_traza_linea_evento($etapa, $secuencia, $evento);
        }

      }
    }
}

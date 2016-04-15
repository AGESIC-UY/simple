<?php
require_once('accion.php');

class AccionEnviarCorreo extends Accion {

    public function displayForm() {

        $display  = '<div class="form-horizontal">';
        $display .= '<div class="control-group">';
        $display .= '<label for="para" class="control-label">Para</label>';
        $display .= '<div class="controls">';
        $display .= '<input id="para" type="text" name="extra[para]" value="' . (isset($this->extra->para) ? $this->extra->para : '') . '" />';
        $display .= '</div>';
        $display .= '</div>';
        $display .= '<div class="control-group">';
        $display .= '<label for="cc" class="control-label">CC</label>';
        $display .= '<div class="controls">';
        $display .= '<input id="cc" type="text" name="extra[cc]" value="' . (isset($this->extra->cc) ? $this->extra->cc : '') . '" />';
        $display .= '</div>';
        $display .= '</div>';
        $display .= '<div class="control-group">';
        $display .= '<label for="cco" class="control-label">CCO</label>';
        $display .= '<div class="controls">';
        $display .= '<input id="cco" type="text" name="extra[cco]" value="' . (isset($this->extra->cco) ? $this->extra->cco : '') . '" />';
        $display .= '</div>';
        $display .= '</div>';
        $display .= '<div class="control-group">';
        $display .= '<label for="tema" class="control-label">Tema</label>';
        $display .= '<div class="controls">';
        $display .= '<input id="tema" type="text" name="extra[tema]" value="' . (isset($this->extra->tema) ? $this->extra->tema : '') . '" />';
        $display .= '</div>';
        $display .= '</div>';
        $display .= '<div class="control-group">';
        $display .= '<label for="contenido" class="control-label">Contenido</label>';
        $display .= '<div class="controls">';
        $display .= '<textarea id="contenido" name="extra[contenido]">' . (isset($this->extra->contenido) ? $this->extra->contenido : '') . '</textarea>';
        $display .= '</div>';
        $display .= '</div>';
        $display .= '</div>';

        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
        $CI->form_validation->set_rules('extra[para]', 'extra[para]', 'required');
        $CI->form_validation->set_rules('extra[tema]', 'extra[tema]', 'required');
        $CI->form_validation->set_rules('extra[contenido]', 'extra[contenido]', 'required');
    }

    public function ejecutar(Etapa $etapa) {
        $regla=new Regla($this->extra->para);
        $to=$regla->getExpresionParaOutput($etapa->id);
        if(isset($this->extra->cc)){
            $regla=new Regla($this->extra->cc);
            $cc=$regla->getExpresionParaOutput($etapa->id);
        }
        if(isset($this->extra->cco)){
            $regla=new Regla($this->extra->cco);
            $bcc=$regla->getExpresionParaOutput($etapa->id);
        }
        $regla=new Regla($this->extra->tema);
        $subject=$regla->getExpresionParaOutput($etapa->id);
        $regla=new Regla($this->extra->contenido);
        $message=$regla->getExpresionParaOutput($etapa->id);

        $CI = & get_instance();
        $cuenta=$etapa->Tramite->Proceso->Cuenta;
        ($CI->config->item('main_domain') == '') ? $from = $cuenta->nombre.'@simple' : $from = $cuenta->nombre.'@'.$CI->config->item('main_domain');

        $CI->email->from($from, $cuenta->nombre_largo);
        $CI->email->to($to);
        if(isset($cc))$CI->email->cc($cc);
        if(isset($bcc))$CI->email->bcc($bcc);
        $CI->email->subject($subject);
        $CI->email->message($message);
        $CI->email->send();
    }

}

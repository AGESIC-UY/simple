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

      $CI = & get_instance();
      $cuenta=$etapa->Tramite->Proceso->Cuenta;
      ($CI->config->item('main_domain') == '') ? $from = $cuenta->nombre.'@simple' : $from = $cuenta->nombre.'@'.$CI->config->item('main_domain');

      $contenido_email = $this->extra->contenido;
      preg_match_all("/(?<=@@).*?([a-zA-Z0-9:\[\]_-]*)/", $contenido_email, $campos_email);

      foreach($campos_email[0] as $campo) {
        if(strpos($campo, '[contenido]')) {
          $contenido_email = str_replace('@@'.$campo, '', $contenido_email);

          $regla = new Regla("@@".str_replace('[contenido]', '', $campo));
          $file_name = $regla->getExpresionParaOutput($etapa->id);

          $file=Doctrine_Query::create()
            ->from('File f, f.Tramite t')
            ->where('f.filename = ? AND t.id = ?', array($file_name, $etapa->Tramite->id))
            ->fetchOne();
          if($file) {
            $folder = $file->tipo=='dato' ? 'datos' : 'documentos';
            if(file_exists('uploads/'.$folder.'/'.$file->filename)) {
              $CI->email->attach('uploads/'.$folder.'/'.$file->filename);
            }
          }
        }
      }

      $regla = new Regla($contenido_email);
      $message = $regla->getExpresionParaOutput($etapa->id);

      $CI->email->from($from, $cuenta->nombre_largo);
      $CI->email->to($to);

      if(isset($cc))$CI->email->cc($cc);
      if(isset($bcc))$CI->email->bcc($bcc);

      $CI->email->subject($subject);
      $CI->email->message($message);
      $CI->email->send();
    }

}

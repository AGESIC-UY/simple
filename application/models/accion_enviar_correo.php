<?php
require_once('accion.php');

class AccionEnviarCorreo extends Accion {

    public function displayForm() {

      $grupos_usuarios =  Doctrine::getTable('GrupoUsuarios')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);

      $display  = '<div class="form-horizontal">';

      $display .= '<div class="control-group">';
      $display .= '<label for="para" class="control-label">Para</label>';
      $display .= '<div class="controls">';
      $display .= '<input id="para" type="text" name="extra[para]" value="' . (isset($this->extra->para) ? $this->extra->para : '') . '" />';
      $display .= '</div>';
      $display .= '</div>';
      $display  .= '<div class="control-group">
                    <label for="grupos_usuarios" class="control-label">Grupos de Usuarios</label>
                    <div class="controls">
                    <select class="chosen" id="grupos_usuarios" name="extra[grupos_usuarios][]" data-placeholder="Seleccione los grupos de usuarios" multiple>';
                      foreach($grupos_usuarios as $g){
                        $econtrado = false;
                        if(isset($this->extra->grupos_usuarios)){

                          foreach ($this->extra->grupos_usuarios as $grupo_id) {
                            if($grupo_id == $g->id){
                              $display  .= '<option value="'.$g->id.'" selected>'.$g->nombre.'</option>';
                              $econtrado = true;
                              break;
                            }
                          }
                          if(!$econtrado){
                            $display  .= '<option value="'.$g->id.'">'.$g->nombre.'</option>';
                          }
                        }
                        else {
                          $display  .= '<option value="'.$g->id.'">'.$g->nombre.'</option>';
                        }

                      }
      $display .= '</select>
                    </div>
                  </div>';
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

      if(!$CI->input->post('extra',false)['para']){
        $CI->form_validation->set_rules('extra[grupos_usuarios][]', 'extra[grupos_usuarios][]', 'required');
      }

      if(!$CI->input->post('extra',false)['grupos_usuarios']){
        $CI->form_validation->set_rules('extra[para]', 'extra[para]', 'required');
      }

      $CI->form_validation->set_rules('extra[tema]', 'extra[tema]', 'required');
      $CI->form_validation->set_rules('extra[contenido]', 'extra[contenido]', 'required');
    }

    public function ejecutar(Etapa $etapa) {
      $to_array = array();

      if(isset($this->extra->para)){
        $regla=new Regla($this->extra->para);
        $to=$regla->getExpresionParaOutput($etapa->id);

        array_push($to_array, $to);
      }

      if(isset($this->extra->grupos_usuarios)){
        foreach($this->extra->grupos_usuarios as $grupo_id){
          $grupo_usuario =  Doctrine::getTable('GrupoUsuarios')->find($grupo_id);

            foreach($grupo_usuario->Usuarios as $u){
              array_push($to_array, $u->email);
            }
        }
      }

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

      if(!$cuenta->correo_remitente) {
        ($CI->config->item('main_domain') == '') ? $from = $cuenta->nombre.'@simple' : $from = $cuenta->nombre.'@'.$CI->config->item('main_domain');
      }
      else {
        $from = $cuenta->correo_remitente;
      }

      $CI->email->clear(TRUE);
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

      //para que los tildes se visualicen de forma correcta
      $config['mailtype'] = 'html';
      $config['priority'] = 1;
      $config['charset'] = 'utf-8';
      $CI->email->initialize($config);

      $CI->email->from($from, $cuenta->nombre_largo);
      $CI->email->to($to_array);

      if(isset($cc))$CI->email->cc($cc);
      if(isset($bcc))$CI->email->bcc($bcc);

      $CI->email->subject($subject);
      $CI->email->message($message);
      if (!$CI->email->send()){
          log_message('ERROR', "send email accion enviar email: ".$CI->email->print_debugger());
      }
    }

}

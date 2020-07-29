<?php

require_once('accion.php');

class AccionEnviarCorreo extends Accion {

    public function displayForm() {

        $grupos_usuarios = Doctrine::getTable('GrupoUsuarios')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);

        $display = '<div class="form-horizontal">';

        $display .= '<div class="control-group">';
        $display .= '<label for="para" class="control-label">Para</label>';
        $display .= '<div class="controls">';
        $display .= '<input id="para" type="text" name="extra[para]" value="' . (isset($this->extra->para) ? $this->extra->para : '') . '" />';
        $display .= '</div>';
        $display .= '</div>';
        $display .= '<div class="control-group">
                    <label for="grupos_usuarios" class="control-label">Grupos de Usuarios</label>
                    <div class="controls">
                    <select class="chosen" id="grupos_usuarios" name="extra[grupos_usuarios][]" data-placeholder="Seleccione los grupos de usuarios" multiple>';
        foreach ($grupos_usuarios as $g) {
            $econtrado = false;
            if (isset($this->extra->grupos_usuarios)) {

                foreach ($this->extra->grupos_usuarios as $grupo_id) {
                    if ($grupo_id == $g->id) {
                        $display .= '<option value="' . $g->id . '" selected>' . $g->nombre . '</option>';
                        $econtrado = true;
                        break;
                    }
                }
                if (!$econtrado) {
                    $display .= '<option value="' . $g->id . '">' . $g->nombre . '</option>';
                }
            } else {
                $display .= '<option value="' . $g->id . '">' . $g->nombre . '</option>';
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
        $extra = $CI->input->post('extra', false);
        if (!isset($extra['para'])) {
            $CI->form_validation->set_rules('extra[grupos_usuarios][]', 'extra[grupos_usuarios][]', 'required');
        }

        if (!isset($extra['grupos_usuarios'])) {
            $CI->form_validation->set_rules('extra[para]', 'extra[para]', 'required');
        }

        $CI->form_validation->set_rules('extra[tema]', 'extra[tema]', 'required');
        $CI->form_validation->set_rules('extra[contenido]', 'extra[contenido]', 'required');
    }

    public function ejecutar(Etapa $etapa, $evento = null) {
        $to_array = array();
        $cc = "";
        $bcc = "";
        $file_dir = "";
        if (isset($this->extra->para)) {
            $regla = new Regla($this->extra->para);
            $to = $regla->getExpresionParaOutput($etapa->id);
            $emails = explode(",", $to);
            foreach ($emails as $email) {
                array_push($to_array, trim($email));
            }
        }

        if (isset($this->extra->grupos_usuarios)) {
            foreach ($this->extra->grupos_usuarios as $grupo_id) {
                $grupo_usuario = Doctrine::getTable('GrupoUsuarios')->find($grupo_id);

                foreach ($grupo_usuario->Usuarios as $u) {
                    array_push($to_array, $u->email);
                }
            }
        }

        if (isset($this->extra->cc)) {
            $regla = new Regla($this->extra->cc);
            $cc = $regla->getExpresionParaOutput($etapa->id);
        }
        if (isset($this->extra->cco)) {
            $regla = new Regla($this->extra->cco);
            $bcc = $regla->getExpresionParaOutput($etapa->id);
        }
        $regla = new Regla($this->extra->tema);
        $subject = $regla->getExpresionParaOutput($etapa->id);

        $CI = & get_instance();
        $cuenta = $etapa->Tramite->Proceso->Cuenta;

        if (!$cuenta->correo_remitente) {
            ($CI->config->item('main_domain') == '') ? $from = $cuenta->nombre . '@simple' : $from = $cuenta->nombre . '@' . $CI->config->item('main_domain');
        } else {
            $from = $cuenta->correo_remitente;
        }

        $contenido_email = $this->extra->contenido;
        preg_match_all("/(?<=@@).*?([a-zA-Z0-9:\[\]_-]*)/", $contenido_email, $campos_email);
        $file_dir = array();
        foreach ($campos_email[0] as $campo) {
            if (strpos($campo, '[contenido]')) {
                $contenido_email = str_replace('@@' . $campo, '', $contenido_email);

                $regla = new Regla("@@" . str_replace('[contenido]', '', $campo));
                $file_name = $regla->getExpresionParaOutput($etapa->id);

                $file = Doctrine_Query::create()
                        ->from('File f, f.Tramite t')
                        ->where('f.filename = ? AND t.id = ?', array($file_name, $etapa->Tramite->id))
                        ->fetchOne();
                if ($file) {
                    $folder = $file->tipo == 'dato' ? 'datos' : 'documentos';
                    if (file_exists('uploads/' . $folder . '/' . $file->filename)) {
                        $file_dir[] = ('uploads/' . $folder . '/' . $file->filename);
                    }
                }
            }
        }

        $regla = new Regla($contenido_email);
        $message = $regla->getExpresionParaOutput($etapa->id);

        $data= new stdClass();
        $data->from=$from;
        $data->from_name=$cuenta->nombre_largo;
        $data->to=$to_array;
        $data->subject=$subject;
        $data->message=$message;
        $data->cc=$cc;
        $data->bcc=$bcc;
        $data->attach=$file_dir;
        $data_json= json_encode($data);
        $b64=base64_encode($data_json);
        $comando = 'php index.php tasks/enviarmails enviar "' . $b64 . '" > /dev/null &';
        exec($comando);
        //trazabilidad evento
        $this->trazar($etapa, $evento);
    }

    private function trazar($etapa, $evento) {
        if ($evento) {
            $CI = & get_instance();
            $CI->load->helper('trazabilidad_helper');

            $ejecutar_fin = false;

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
                enviar_traza_linea_evento_despues_tarea($etapa, $secuencia, $evento);
            } else {
                enviar_traza_linea_evento($etapa, $secuencia, $evento);
            }
        }
    }

}

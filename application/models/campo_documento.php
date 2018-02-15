<?php
require_once('campo.php');
class CampoDocumento extends Campo {

    public $requiere_nombre=true;
    public $requiere_datos=false;
    public $estatico=true;

    function setTableDefinition() {
        parent::setTableDefinition();
        $this->hasColumn('readonly','bool',1,array('default'=>1));
    }

    function setUp() {
        parent::setUp();
        $this->setTableName("campo");
    }

    public function setReadonly($readonly) {
        $this->_set('readonly', 1);
    }

    protected function display($modo, $dato, $etapa_id) {
        if(isset($this->extra->firmar) && $this->extra->firmar)
            return $this->displayFirmador($modo, $dato, $etapa_id);
        else
            return $this->displayDescarga ($modo, $dato, $etapa_id);
    }


    private function displayDescarga($modo, $dato, $etapa_id) {
        $CI = &get_instance();

        if (!$etapa_id) {
            //return '<div class="control-group"><div class="controls"><a class="btn-link" href="#"><span class="icon-download-alt icon-white"></span> ' . $this->etiqueta . '</a></div></div>';

            $display = '<div class="control-group"><div class="controls" data-fieldset="'.$this->fieldset.'"><a class="btn-link" href="#">' . $this->etiqueta . ' (.pdf)</a>';
            // -- Boton disparador de accion del campo
            if($this->requiere_accion == 1 && $modo != 'visualizacion') {
              $display .= ' <button type="button" class="btn requiere_accion_disparador" data-campo="'. $this->id .'">'.$this->requiere_accion_boton.'</button>';
            }
            $display .= '</div></div>';

            return $display;
        }

        $etapa=Doctrine::getTable('Etapa')->find($etapa_id);

        if (!$dato) {   //Generamos el documento, ya que no se ha generado
            $file=$this->Documento->generar($etapa->id, $this->id);

            //se genera el dato de seguimiento del documento
            $dato = new DatoSeguimiento();
            $dato->nombre = $this->nombre;
            $dato->valor = $file->filename;
            $dato->etapa_id = $etapa->id;
            $dato->save();
        }
        else {

            $file=Doctrine::getTable('File')->findOneByTipoAndFilename('documento',$dato->valor);
            if(!($CI->uri->segment(3) == 'ver_etapa')  && $etapa->pendiente && isset($this->extra->regenerar) && $this->extra->regenerar){
                //si se tiene que regenerar el documento
                $file->delete();
                $file=$this->Documento->generar($etapa->id, $this->id);
                $dato->valor = $file->filename;
                $dato->save();
            }
        }

        try {
          $file_size = number_format(filesize(DIRECTORIO_SUBIDA_DOCUMENTOS . $file->filename) / 1024, 2) . 'KB';
        }
        catch(Exception $error) {
          log_error($error);
        }

        $display = '<div class="control-group"><div class="controls" data-fieldset="'.$this->fieldset.'"><a class="btn-link" href="' . site_url('documentos/get/' . $file->id) . '?token='.$file->llave.'">' . $this->etiqueta . ' (.pdf '. $file_size .')</a></div></div>';

        // -- Boton disparador de accion del campo
        if($this->requiere_accion == 1 && $modo != 'visualizacion') {
          $display .= ' <div class="controls"><button type="button" class="btn requiere_accion_disparador" data-campo="'. $this->id .'">'.$this->requiere_accion_boton.'</button></div>';
        }

        return $display;
    }

    private function displayFirmador($modo, $dato, $etapa_id) {
        $CI = &get_instance();

        if (!$etapa_id) {
            return '<p data-fieldset="'.$this->fieldset.'">' . $this->etiqueta . '</p>';
        }

        $etapa=Doctrine::getTable('Etapa')->find($etapa_id);

        if (!$dato) {   // Generamos el documento, ya que no se ha generado
            $file=$this->Documento->generar($etapa->id, $this->id);

            $dato = new DatoSeguimiento();
            $dato->nombre = $this->nombre;
            $dato->valor = $file->filename;
            $dato->etapa_id = $etapa->id;
            $dato->save();
        }
        else {

          //obtenemos el file a partir del dato
          $file=Doctrine::getTable('File')->findOneByTipoAndFilename('documento', $dato->valor);

          //si el dato está generado para una etapa distinta, en general anterior
          //se genera el dato para esta etapa
          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($dato->nombre, $etapa->id);
          if ($dato){
              $dato->delete();
          }
          $dato = new DatoSeguimiento();
          $dato->nombre = $this->nombre;
          $dato->valor = $file->filename;
          $dato->etapa_id = $etapa->id;
          $dato->save();

          //si se tiene que regenerar el documento, se regenera.
          if(!($CI->uri->segment(3) == 'ver_etapa') && !($CI->uri->segment(2) == 'ver') && $etapa->pendiente && isset($this->extra->regenerar) && $this->extra->regenerar){
                $file->delete();
                $file=$this->Documento->generar($etapa->id, $this->id);
                $dato->valor = $file->filename;
                $dato->save();

                //si se regenera se elimina el flag de firmado
                $firmado = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($dato->nombre.'__firmado', $etapa->id);
                if ($firmado){
                  $firmado->delete();
                }


            }
        }

        $display = '<div class="control-group">';
        $error_no_firmado = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($dato->nombre.'__error', $etapa->id);
        if($error_no_firmado) {
          $display  .= '<div class="control-group">';
          $display .= '<div class="campo_error validacion-error">';
          $display .= '<span class="alert alert-error">' . $error_no_firmado->valor . '</span>';
          $display .= '</div>';
          $display .= '</div>';
        }
        $display .= '<span class="control-label" data-fieldset="'.$this->fieldset.'">'.$this->etiqueta.'</span>';
        $display .= '<div id="exito" class="alert alert-success" style="display: none;">Documento fue firmado con éxito.</div>';
        $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'"><a class="btn btn-info" href="' . site_url('documentos/get/' . $file->id) .'?token='.$file->llave. '"><span class="icon-search icon-white"></span> Previsualizar el documento</a></div>';
        $display .= '</div>';
        $display .= '
            <script>
                $(document).ready(function() {
                    $("#firmar_documento_ext_'. $this->id .'").click(function() {
                        $.ajax({
                          type: "post",
                          url: "'. HOST_SISTEMA_COMPLETO .'/etapas/firmar_documento",
                          data: {filename: "'. $file->filename .'", campo: '. $this->id .', etapa_id: '. $etapa->id .   '},
                          cache: false
                        })
                        .done(function(msg) {
                            var a = document.createElement("a");
                            a.style = "display:none;";
                            a.download = "FirmaDocumento.jnlp";
                            var blob = new Blob([msg]);
                            if (navigator.appVersion.toString().indexOf(".NET") > 0){
                              window.navigator.msSaveBlob(blob, "'.  $file->filename .'.jnlp");
                            }
                            else {
                              var url =  window.URL.createObjectURL(blob, {type: "application/x-java-jnlp-file"});
                              a.href = url;
                              document.body.appendChild(a);
                              a.click();
                            }
                        });
                    });
                });
            </script>
            <div id="firmaDiv" class="control-group">';

            $nombre_documento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->nombre, $etapa->id);
            $file = Doctrine::getTable('File')->findOneByTipoAndFilename('documento', $nombre_documento->valor);
            $firmado = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($dato->nombre.'__firmado', $etapa->id);

            //si el funcionario actuó como el ciudadano entonces se generó este dado de seguimieto funcionario_actuando_como_ciudadano
            //si no se tiene esta variable se actua de forma normal.
            $funcionario_actuando_como_ciudadano = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('funcionario_actuando_como_ciudadano', $etapa_id);


            if($funcionario_actuando_como_ciudadano && $this->firma_electronica == 0 && UsuarioSesion::usuarioMesaDeEntrada() && $CI->session->userdata('id_usuario_ciudadano') && $CI->uri->segment(1) != 'backend') {
              if(!$firmado){
                $display .= '<div class="controls"><label class="checkbox" for="check_firma_electronica"><input type="checkbox" name="check_firma_electronica" id="check_firma_electronica" value="1">'.MENSAJE_FIRMA_DOCUMENTO_FUNCIONARIO.'</label></div>';
              }
              else {
                $display .= '<div class="controls"><strong class="green">'.MENSAJE_FIRMA_CONFIRMADO_DOCUMENTO_FUNCIONARIO.'</strong>
                  <label class="checkbox" for="check_cancelar_firma"><input type="checkbox" name="check_cancelar_firma" id="check_cancelar_firma" value="1"><b class="red"> Cancelar</label></b></div>';
              }
            }
            else if($funcionario_actuando_como_ciudadano && $firmado && !UsuarioSesion::usuarioMesaDeEntrada() && !$CI->session->userdata('id_usuario_ciudadano') && $CI->uri->segment(1) != 'backend'){
                $display .= '<div class="controls"><strong class="green">'.MENSAJE_FIRMA_CONFIRMADO_DOCUMENTO_FUNCIONARIO.'</strong></div>';
            }
            else if ($funcionario_actuando_como_ciudadano && $CI->uri->segment(3) == 'ver_etapa'){
              if(!$firmado){
                $display .= '<div class="controls"><strong class="green">'.MENSAJE_FIRMA_NO_CONFIRMADO_DOCUMENTO_FUNCIONARIO.'</strong></div>';
              }
              else {
                $display .= '<div class="controls"><strong class="green">'.MENSAJE_FIRMA_CONFIRMADO_DOCUMENTO_FUNCIONARIO.'</strong></div>';
              }
            }
            else {


              //if (!$firmado && ($CI->uri->segment(2) == 'ejecutar')){
              if (($CI->uri->segment(2) == 'ejecutar')){
                //si no está firmado se deja firmar nuevamente
                if ($this->extra->requerido) {
                  $display .= '<span class="control-label">Seleccione la firma *</span>';
                }
                else {
                  $display .= '<span class="control-label">Seleccione la firma</span>';
                }

                $display .= '<div class="controls"><a class="btn btn-success" id="firmar_documento_ext_'. $this->id .'"><span class="icon-pencil icon-white"></span> Firmar Documento</a></div>';
              }

              $display .= '</div>';
            }

            // -- Boton disparador de accion del campo
            if($this->requiere_accion == 1 && $modo != 'visualizacion') {
              $display .= ' <div class="controls"><button type="button" class="btn requiere_accion_disparador" data-campo="'. $this->id .'">'.$this->requiere_accion_boton.'</button></div>';
            }

        return $display;
    }

    public function backendExtraFields() {
        $regenerar=isset($this->extra->regenerar)?$this->extra->regenerar:null;
        $firmar=isset($this->extra->firmar)?$this->extra->firmar:null;
        $firmar_servidor=isset($this->extra->firmar_servidor)?$this->extra->firmar_servidor:null;
        $firmar_servidor_keystores=isset($this->extra->firmar_servidor_keystores)?$this->extra->firmar_servidor_keystores:null;
        $firmar_servidor_momento=isset($this->extra->firmar_servidor_momento)?$this->extra->firmar_servidor_momento:null;
        $firmado=0;
        $requerido=isset($this->extra->requerido)?$this->extra->requerido:null;

        $html='<label for="documento_id">Documento</label>';
        $html.='<select name="documento_id" id="documento_id">';
        $html.='<option value="">Seleccionar</option>';
        foreach($this->Formulario->Proceso->Documentos as $d)
            $html.='<option value="'.$d->id.'" '.($this->documento_id==$d->id?'selected':'').'>'.$d->nombre.'</option>';
        $html.='</select>';

        if(isset($d)) {
          $html.='<label class="radio" for="primera_'.$d->id.'"><input id="primera_'.$d->id.'" type="radio" name="extra[regenerar]" value="0" '.(!$regenerar?'checked':'').' /> El documento se genera solo la primera vez que se visualiza este campo.</label>';
          $html.='<label class="radio" for="cada_'.$d->id.'"><input id="cada_'.$d->id.'" type="radio" name="extra[regenerar]" value="1" '.($regenerar?'checked':'').' /> El documento se regenera cada vez que se visualiza este campo.</label>';
          $html.='<br /><label class="checkbox" for="firmar"><input type="checkbox" name="extra[firmar]" '.($firmar?'checked':'').' id="firmar" /> Deseo firmar con token en este paso.</label>';
          $html.='<label class="checkbox" for="firmar_servidor"><input type="checkbox" name="extra[firmar_servidor]" '.($firmar_servidor?'checked':'').' id="firmar_servidor" /> Deseo firmar el documento en servidor.</label>';
          $html.='<label class="checkbox" for="firma_requerida"><input type="checkbox" name="extra[requerido]" '.($requerido?'checked':'').' id="firma_requerida" /> Firma requerida.</label>';
          $html.='<br /><div class="well form-horizontal" '.(!$firmar_servidor ? 'style="display:none;"' : '').' class="input" id="firmar_servidor_keystores">';
          $html.='<div class="control-group">';
          $html.='<label class="control-label" for="keystores">Keystores para la firma:</label>';
          $html.='<div class="controls"><input type="text" id="keystores" name="extra[firmar_servidor_keystores]" placeholder="key1, key2, key3" value="'.($firmar_servidor_keystores?$firmar_servidor_keystores:'').'" /></div>';
          $html.='</div>';
          $html.=' <div class="control-group">';
          $html.='<label class="control-label" for="firmar_servidor_momento">Momento de firma:</label>';
          $html.='<div class="controls"><select name="extra[firmar_servidor_momento]" id="firmar_servidor_momento"><option value="antes" id="firmar_servidor_momento_antes" '. ($firmar_servidor_momento == 'antes' ? 'selected' : '') .'>Al generar documento</option><option value="despues" id="firmar_servidor_momento_despues" '.($firmar?'':'style="display:none;"').' '. ($firmar_servidor_momento == 'despues' ? 'selected' : '') .'>Despues de firma de usuario</option></select></div>';
          $html.='</div></div>';
          $html.= '<script>// -- Manejo de checkbox para firmas de documentos en servidor
                  $(document).ready(function() {
                      $("#firmar_servidor").change(function() {
                          if(this.checked) {
                              $("#firmar_servidor_keystores").show();
                              $("#firmar_servidor_momento").show();
                          }
                          else {
                              $("#firmar_servidor_keystores").hide();
                              $("#firmar_servidor_momento").hide();
                          }
                      });

                      $("#firmar").change(function() {
                          if(!this.checked) {
                              $("#firmar_servidor_momento_despues").hide();
                          }
                          else {
                              $("#firmar_servidor_momento_despues").show();
                          }
                      });

                  });</script>';

          return $html;
        }
    }

    public function backendExtraValidate() {
        parent::backendExtraValidate();

        $CI= &get_instance();
        $CI->form_validation->set_rules('documento_id','Documento_id','required');
    }

/*    public function formValidate($etapa_id = null){
      $CI=& get_instance();
      $validacion=$this->validacion;
      if($etapa_id){
          $regla = new Regla($this->validacion);
          $validacion = $regla->getExpresionParaOutput($etapa_id);
      }
      $CI->form_validation->set_rules($this->nombre, ucfirst($this->etiqueta),'callback_documento_firma['.$etapa_id.']');
    }
*/


    //Funcion que retorna si este campo debiera poderse editar de acuerdo al input POST del usuario
    public function documentoIsEditableWithCurrentPOST(){
        $CI=& get_instance();

        $resultado=true;

        if($this->dependiente_campo){
            $nombre_campo=preg_replace('/\[\w*\]$/', '', $this->dependiente_campo);
            $variable=$CI->input->post($nombre_campo);

            //Parche para el caso de campos dependientes con accesores. Ej: ubicacion[comuna]!='Las Condes|Santiago'
            if(preg_match('/\[(\w+)\]$/',$this->dependiente_campo,$matches))
                $variable=$variable[$matches[1]];

            if($variable===false){    //Si la variable dependiente no existe
                $resultado=false;
            }else{
                if(is_array($variable)){ //Es un arreglo
                    if($this->dependiente_tipo=='regex'){
                        foreach($variable as $x){
                            if(!preg_match('/'.$this->dependiente_valor.'/', $x))
                                $resultado = false;
                        }
                    }else{
                        if(!in_array($this->dependiente_valor, $variable))
                            $resultado = false;
                    }
                }else{
                    if($this->dependiente_tipo=='regex'){
                        if(!preg_match('/'.$this->dependiente_valor.'/', $variable))
                            $resultado = false;
                    }else{
                        if($variable!=$this->dependiente_valor)
                            $resultado = false;
                    }

                }

                if($this->dependiente_relacion=='!=')
                    $resultado=!$resultado;
            }
        }

        return $resultado;
    }
}

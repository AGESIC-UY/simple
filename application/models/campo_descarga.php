<?php
require_once('campo.php');
class CampoDescarga extends Campo {

    public $requiere_nombre=true;
    public $requiere_datos=false;
    public $estatico=false;
    public $requiere_validacion = false;

    protected function display($modo, $dato, $etapa_id) {
        return $this->displayDescarga ($modo, $dato, $etapa_id);
    }


    private function displayDescarga($modo, $dato, $etapa_id) {
        $CI = &get_instance();

        if (!$etapa_id) {
            return '<div class="control-group"><div class="controls" data-fieldset="'.$this->fieldset.'"><a class="btn-link" href="#">' . $this->etiqueta . ' (.pdf)</a></div></div>';
        }

        $etapa=Doctrine::getTable('Etapa')->find($etapa_id);

        $regla=new Regla($this->valor_default);
        $valor_default=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->tipo_documentoid);
        $tipo_file = $regla->getExpresionParaOutput($etapa->id);

        if ($valor_default){


          $filename_uniqid = '';

          if ($dato){
            //si ya estaba creada la variable entonces elimino el file anterior.
            $file=Doctrine::getTable('File')->findOneByTipoAndFilenameAndEtapaId('descarga',$dato->valor,$etapa->id);
            if ($file){
                unlink(DIRECTORIO_SUBIDA_DOCUMENTOS . $file->filename);
                $file->delete();

            }
          }


          $filename_uniqid = uniqid() . '.' . $tipo_file;
          while(file_exists(DIRECTORIO_SUBIDA_DOCUMENTOS.$filename_uniqid) ){
              $filename_uniqid = uniqid() . '.' . $tipo_file;
          }

          $file = new File();
          $file->tramite_id = $etapa->tramite_id;
          $file->tipo = 'descarga';
          $file->llave = strtolower(random_string('alnum', 12));
          $file->llave_copia = null;
          $file->llave_firma = strtolower(random_string('alnum', 12));
          $file->filename = $filename_uniqid;
          $file->etapa_id = $etapa->id;
          $file->save();


          if (!$dato) {   //Generamos el dato
              $dato = new DatoSeguimiento();
              $dato->nombre = $this->nombre;
              $dato->valor = $filename_uniqid;
              $dato->etapa_id = $etapa->id;
              $dato->save();
          }

          try {
            //crea el archivo en disco
            $ifp = fopen( DIRECTORIO_SUBIDA_DOCUMENTOS . $file->filename, 'wb' );
            // we could add validation here with ensuring count( $data ) > 1
            fwrite( $ifp, base64_decode( $valor_default ) );
            // clean up the file resource
            fclose( $ifp );

            $file_size = number_format(filesize(DIRECTORIO_SUBIDA_DOCUMENTOS . $file->filename) / 1024, 2) . 'KB';
          }
          catch(Exception $error) {
            log_error($error);
            return '<div class="control-group"><div class="controls" data-fieldset="'.$this->fieldset.'">No se pudo generar el link de descarga</div></div>';
          }

          $display = '<div class="control-group">
                        <div class="controls" data-fieldset="'.$this->fieldset.'">
                          <a class="btn-link" href="' . site_url('documentos/get/' . $file->id) . '?token='.$file->llave.'">' . $this->etiqueta . ' (.'. $tipo_file . ' '. $file_size .')</a>
                          <input type="hidden" name = "'. $this->nombre . '" value = "'. $filename_uniqid . '"/>
                        </div>
                      </div>';

                      log_message('ERROR', $display);

          return $display;

        }else{
          $display = '<div class="control-group"><div class="controls" data-fieldset="'.$this->fieldset.'">No existe el archivo</div></div>';
        }


    }

    public function backendExtraFields() {
        $tipo_documento=isset($this->extra->tipo_documentoid)?$this->extra->tipo_documentoid:null;
        $html.='<label class="control-label" for="tipo_documentoid">Tipo Documento:</label>';
        $html.='<div class="controls"><input type="text" id="tipo_documentoid" name="extra[tipo_documentoid]" placeholder="Tipo de Documento (pdf, xls, etc)" value="'.($tipo_documento?$tipo_documento:'').'" /></div>';
        return $html;

    }

    public function backendExtraValidate() {
        parent::backendExtraValidate();

        $CI= &get_instance();
        //$CI->form_validation->set_message('documento_tipo_id', 'El tipo de documento es obligatorio.');
        $CI->form_validation->set_rules('extra[tipo_documentoid]','Tipo de Documento','required');
        $CI->form_validation->set_rules('valor_default', 'Valor por defecto', 'required');
    }

}

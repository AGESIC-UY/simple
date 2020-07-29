<?php
require_once('accion.php');

class AccionArchivo extends Accion {

    public function displayForm() {

        $display = '<div class="form-horizontal">';
        $display.= '<div class="control-group">';
        $display.= '<label for="variable" class="control-label">Variable</label>';
        $display.='<div class="controls">';
        $display.='<input id="variable" type="text" name="extra[variable]" value="' . ($this->extra ? $this->extra->variable : '') . '" />';
        $display.='</div>';
        $display.='</div>';
        $display.= '<div class="control-group">';
        $display.= '<label for="expresion" class="control-label">Archivo</label>';
        $display.='<div class="controls">';
        $display.='<textarea id="archivo" name="extra[archivo]" class="input-xxlarge">' . ($this->extra ? $this->extra->archivo : '') . '</textarea>';
        $display.='</div>';
        $display.='</div>';
        $display.= '<div class="control-group">';
        $display.= '<label for="expresion" class="control-label">Tipo de Archivo</label>';
        $display.='<div class="controls">';
        $display.='<input id="tipo_archivo" type="text" name="extra[tipo_archivo]" value="' . ($this->extra ? $this->extra->tipo_archivo : '') . '" />';
        $display.='</div>';
        $display.='</div>';
        $display.='</div>';

        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
        $CI->form_validation->set_rules('extra[variable]', 'Variable', 'required');
        $CI->form_validation->set_rules('extra[archivo]', 'Archivo', 'required');
        $CI->form_validation->set_rules('extra[tipo_archivo]', 'Tipo de Archivo', 'required');
    }

    public function ejecutar(Etapa $etapa, $evento = null){
      $CI = & get_instance();
      $CI->load->helper('filename_concurrencia_helper');
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->extra->variable,$etapa->id);
        if (!$dato){
            $dato = new DatoSeguimiento();
        }else{
          // esta creada la variable eliminamos el file si existe
          $file=Doctrine::getTable('File')->findOneByTipoAndFilenameAndEtapaId('accion_archivo',$dato->valor,$etapa->id);
          if ($file){
              unlink(DIRECTORIO_SUBIDA_DOCUMENTOS . $file->filename);
              $file->delete();

          }
        }

        $regla=new Regla($this->extra->tipo_archivo);
        $tipo_file = $regla->getExpresionParaOutput($etapa->id);

        //genera el nuevo nombre de archivo y crea el dato de seguimiento
        $filename_uniqid = obtenerFileName(). '.' . $tipo_file;
        while(file_exists(DIRECTORIO_SUBIDA_DOCUMENTOS.$filename_uniqid) ){
          $filename_uniqid = obtenerFileName(). '.' . $tipo_file;
        }

        $dato->nombre = $this->extra->variable;
        $dato->valor =$filename_uniqid;
        $dato->etapa_id = $etapa->id;
        $dato->save();


        //crea el file
        $file = new File();
        $file->tramite_id = $etapa->tramite_id;
        $file->tipo = 'accion_archivo';
        $file->llave = strtolower(random_string('alnum', 12));
        $file->llave_copia = null;
        $file->llave_firma = strtolower(random_string('alnum', 12));
        $file->filename = $filename_uniqid;
        $file->etapa_id = $etapa->id;
        $file->save();


        try {
          $regla=new Regla($this->extra->archivo);
          $valor=$regla->evaluar($etapa->id);

          //crea el archivo en disco
          $ifp = fopen( DIRECTORIO_SUBIDA_DOCUMENTOS . $file->filename, 'wb' );
          // we could add validation here with ensuring count( $data ) > 1
          fwrite( $ifp, base64_decode( $valor ) );
          // clean up the file resource
          fclose( $ifp );

          $file_size = number_format(filesize(DIRECTORIO_SUBIDA_DOCUMENTOS . $file->filename) / 1024, 2) . 'KB';
        }
        catch(Exception $error) {
          log_error($error);
        }

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

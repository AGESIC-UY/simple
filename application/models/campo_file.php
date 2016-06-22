<?php
require_once('campo.php');
class CampoFile extends Campo {

    public $requiere_datos=false;

    protected function display($modo, $dato,$etapa_id) {
        if(!$etapa_id){
            $display  = '<div class="control-group">';
            $display.='<span class="control-label" data-fieldset="'.$this->fieldset.'">' . $this->etiqueta . (in_array('required', $this->validacion) ? '*:' : ':') . '</span>';
            $display.='<div class="controls" data-fieldset="'.$this->fieldset.'">';
            $display.='<button type="button" class="btn">Subir archivo</button>';
            if($this->ayuda)
                $display.='<span class="help-block">'.$this->ayuda.'</span>';
            $display.='</div>';
            $display.='</div>';
            return $display;
        }

        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

        $display  = '<div class="control-group">';
        $display.='<span class="control-label" data-fieldset="'.$this->fieldset.'">' . $this->etiqueta . (in_array('required', $this->validacion) ? '*:' : ':') . '</span>';
        $display.='<div class="controls" data-fieldset="'.$this->fieldset.'">';
        if($modo!='visualizacion')
            $display.='<div class="file-uploader" data-action="'.site_url('uploader/datos/'.$this->id.'/'.$etapa->id).'"></div>';
        $display.='<input type="hidden" name="' . $this->nombre . '" value="' . ($dato ? htmlspecialchars($dato->valor) : '') . '" />';
        if ($dato){
            $file=Doctrine::getTable('File')->findOneByTipoAndFilename('dato',$dato->valor);
            if($file){
                $display.='<p class="link"><a href="' . site_url('uploader/datos_get/'.$file->filename).'?id='.$file->id.'&amp;token='.$file->llave.'" target="_blank">' . htmlspecialchars ($dato->valor) . '</a>';
                if(!($modo=='visualizacion'))
                    $display.='(<a class="remove" href="#">X</a>)</p>';
            }else{
                $display.='<p class="link">No se ha subido archivo.</p>';
            }
        }
        else
            $display.='<p class="link"></p>';

        if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';

        $display.='</div>';
        $display.='</div>';

        return $display;
    }


    public function extraForm() {
        $filetypes=array();
        if(isset($this->extra->filetypes))
            $filetypes=$this->extra->filetypes;

        $output= '<label for="extensiones">Tipos de archivos permitidos</label>';
        $output.='<select id="extensiones" name="extra[filetypes][]" multiple>';
        $output.='<option value="jpg" '.(in_array('jpg', $filetypes)?'selected':'').'>jpg</option>';
        $output.='<option value="png" '.(in_array('png', $filetypes)?'selected':'').'>png</option>';
        $output.='<option value="gif" '.(in_array('gif', $filetypes)?'selected':'').'>gif</option>';
        $output.='<option value="pdf" '.(in_array('pdf', $filetypes)?'selected':'').'>pdf</option>';
        $output.='<option value="doc" '.(in_array('doc', $filetypes)?'selected':'').'>doc</option>';
        $output.='<option value="docx" '.(in_array('docx', $filetypes)?'selected':'').'>docx</option>';
        $output.='<option value="xls" '.(in_array('xls', $filetypes)?'selected':'').'>xls</option>';
        $output.='<option value="xlsx" '.(in_array('xlsx', $filetypes)?'selected':'').'>xlsx</option>';
        $output.='<option value="mpp" '.(in_array('mpp', $filetypes)?'selected':'').'>mpp</option>';
        $output.='<option value="vsd" '.(in_array('vsd', $filetypes)?'selected':'').'>vsd</option>';
        $output.='<option value="ppt" '.(in_array('ppt', $filetypes)?'selected':'').'>ppt</option>';
        $output.='<option value="pptx" '.(in_array('pptx', $filetypes)?'selected':'').'>pptx</option>';
        $output.='<option value="zip" '.(in_array('zip', $filetypes)?'selected':'').'>zip</option>';
        $output.='<option value="rar" '.(in_array('rar', $filetypes)?'selected':'').'>rar</option>';
        $output.='</select>';

        return $output;
    }
}

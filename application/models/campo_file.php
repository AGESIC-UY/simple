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

            if($this->ayuda_ampliada)
              $display .= '<button title="'. strip_tags($this->ayuda_ampliada) .'" class="tooltip_help" onclick="return false;"><span class="icn icn-circle-help"></span><span class="hide-read">Ayuda</span></button>';

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
        $display.='<input type="hidden" name="' . $this->nombre . '" value="' . ($dato ? htmlspecialchars($dato->valor) : '') . '" id="'. $this->nombre .'" />';
        if ($dato){
            $file=Doctrine::getTable('File')->findOneByTipoAndFilename('dato',$dato->valor);
            if($file){
                $display.='<p class="link"><a href="' . site_url('uploader/datos_get/'.$file->id).'?token='.$file->llave.'" target="_blank">' . htmlspecialchars ($dato->valor) . '</a>';
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

        if($this->ayuda_ampliada)
          $display .= '<button title="'. strip_tags($this->ayuda_ampliada) .'" class="tooltip_help" onclick="return false;"><span class="icn icn-circle-help"></span><span class="hide-read">Ayuda</span></button>';

        $display.='</div>';
        $display.='</div>';

        return $display;
    }


    public function extraForm() {
        $filetypes=array();
        if(isset($this->extra->filetypes))
            $filetypes=$this->extra->filetypes;

        $output= '<label for="extensiones">Tipos de archivos permitidos</label>';
        $output.='<select id="extensiones" name="extra[filetypes][]" multiple style="height:200px;">';
        $output.='<option value="jpg" '.(in_array('jpg', $filetypes)?'selected':'').'>jpg</option>';
        $output.='<option value="jpeg" '.(in_array('jpeg', $filetypes)?'selected':'').'>jpeg</option>';
        $output.='<option value="png" '.(in_array('png', $filetypes)?'selected':'').'>png</option>';
        $output.='<option value="gif" '.(in_array('gif', $filetypes)?'selected':'').'>gif</option>';
        $output.='<option value="pdf" '.(in_array('pdf', $filetypes)?'selected':'').'>pdf</option>';
        $output.='<option value="doc" '.(in_array('doc', $filetypes)?'selected':'').'>doc</option>';
        $output.='<option value="docx" '.(in_array('docx', $filetypes)?'selected':'').'>docx</option>';
        $output.='<option value="odt" '.(in_array('odt', $filetypes)?'selected':'').'>odt</option>';
        $output.='<option value="xls" '.(in_array('xls', $filetypes)?'selected':'').'>xls</option>';
        $output.='<option value="xlsx" '.(in_array('xlsx', $filetypes)?'selected':'').'>xlsx</option>';
        $output.='<option value="mpp" '.(in_array('mpp', $filetypes)?'selected':'').'>mpp</option>';
        $output.='<option value="vsd" '.(in_array('vsd', $filetypes)?'selected':'').'>vsd</option>';
        $output.='<option value="ppt" '.(in_array('ppt', $filetypes)?'selected':'').'>ppt</option>';
        $output.='<option value="pptx" '.(in_array('pptx', $filetypes)?'selected':'').'>pptx</option>';
        $output.='<option value="zip" '.(in_array('zip', $filetypes)?'selected':'').'>zip</option>';
        $output.='<option value="rar" '.(in_array('rar', $filetypes)?'selected':'').'>rar</option>';
        $output.='<option value="wmv" '.(in_array('wmv', $filetypes)?'selected':'').'>wmv</option>';
        $output.='<option value="avi" '.(in_array('avi', $filetypes)?'selected':'').'>avi</option>';
        $output.='<option value="mov" '.(in_array('mov', $filetypes)?'selected':'').'>mov</option>';
        $output.='<option value="aac" '.(in_array('aac', $filetypes)?'selected':'').'>aac</option>';
        $output.='<option value="mp4" '.(in_array('mp4', $filetypes)?'selected':'').'>mp4</option>';
        $output.='<option value="mp3" '.(in_array('mp3', $filetypes)?'selected':'').'>mp3</option>';
        $output.='<option value="3gp" '.(in_array('3gp', $filetypes)?'selected':'').'>3gp</option>';
        $output.='<option value="wma" '.(in_array('wma', $filetypes)?'selected':'').'>wma</option>';
        $output.='<option value="vox" '.(in_array('vox', $filetypes)?'selected':'').'>vox</option>';
        $output.='<option value="ra" '.(in_array('ra', $filetypes)?'selected':'').'>ra</option>';
        $output.='<option value="rm" '.(in_array('rm', $filetypes)?'selected':'').'>rm</option>';
        $output.='<option value="vf" '.(in_array('vf', $filetypes)?'selected':'').'>vf</option>';
        $output.='<option value="mpg" '.(in_array('mpg', $filetypes)?'selected':'').'>mpg</option>';

        $output.='</select>';

        return $output;
    }
}

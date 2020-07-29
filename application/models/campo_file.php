<?php

require_once('campo.php');

class CampoFile extends Campo {

    public $requiere_datos = false;
    public $reporte = true;

    protected function display($modo, $dato, $etapa_id) {
        if (!$etapa_id) {
            $display = '<div class="control-group">';
            $display.='<span class="control-label" data-fieldset="' . $this->fieldset . '">' . $this->etiqueta . (in_array('required', $this->validacion) ? '*:' : ':') . '</span>';
            $display.='<div class="controls" data-fieldset="' . $this->fieldset . '">';
            $display.='<button type="button" class="btn">Subir archivo</button>';

            if ($this->ayuda)
                $display.='<span class="help-block">' . $this->ayuda . '</span>';

            if ($this->ayuda_ampliada) {
                $display .= '<span><button type="button" class="tooltip_help_click tooltip_help_click_radio" onclick="return false;"><span class="icn icn-circle-help"></span><span class="hide-read">Ayuda</span></button>';
                $display .= '<span class="hidden tooltip_help_line">' . strip_tags($this->ayuda_ampliada) . '</span></span>';
            }

            $display.='</div>';
            $display.='</div>';
            return $display;
        }

        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

        $display = '<div class="control-group">';
        $display.='<span class="control-label" data-fieldset="' . $this->fieldset . '">' . $this->etiqueta . (in_array('required', $this->validacion) ? '*:' : ':') . '</span>';
        $display.='<div class="controls" data-fieldset="' . $this->fieldset . '">';
        if ($modo != 'visualizacion')
            $display.='<div class="file-uploader" data-action="' . site_url('uploader/datos/' . $this->id . '/' . $etapa->id) . '"></div>';
        $display.='<input type="hidden" name="' . $this->nombre . '" value="' . ($dato ? htmlspecialchars($dato->valor) : '') . '" />';
        if ($dato) {
            $file = Doctrine::getTable('File')->findOneByTipoAndFilename('dato', $dato->valor);
            if ($file) {
                /* Se incluye el nombre original del archivo al link de formulario */
                $display.='<p class="link"><a href="' . site_url('uploader/datos_get/' . $file->id) . '?token=' . $file->llave . '" target="_blank">' . htmlspecialchars($file->file_origen) . '</a>';
                if (!($modo == 'visualizacion'))
                    $display.=' (<a class="remove" title="' . $this->etiqueta .'" href="#"> Eliminar </a>) </p>';
            } else {
                $display.='<p class="link">No se ha subido archivo.</p>';
            }
        } else
            $display.='<p class="link"></p>';

        if ($this->ayuda)
            $display.='<span class="help-block">' . $this->ayuda . '</span>';

        if ($this->ayuda_ampliada) {
            $display .= '<span><button type="button" class="tooltip_help_click tooltip_help_click_radio" onclick="return false;"><span class="icn icn-circle-help"></span><span class="hide-read">Ayuda</span></button>';
            $display .= '<span class="hidden tooltip_help_line">' . strip_tags($this->ayuda_ampliada) . '</span></span>';
        }

        $display.='</div>';
        if (isset($this->extra->firmar)) {
            $display.='<div class="firma ' . (isset($file) ? "" : "hidden") . '">';
            $CI = &get_instance();
            if (($CI->uri->segment(2) == 'ejecutar')) {
                $display .= '<div class="controls"><a class="btn btn-success" id="firmar_documento_ext_' . $this->id . '"><span class="icon-pencil icon-white"></span> Firmar Documento</a></div>';
            }
            if (isset($file)) {
                if ($file) {
                    $display .= '
            <script>
                $(document).ready(function () {
                    $("#firmar_documento_ext_' . $this->id . '").click(function() {
                        $.ajax({
                          type: "post",
                          url: "' . HOST_SISTEMA_COMPLETO . '/etapas/firmar_documento",
                          data: {filename: "' . $file->filename . '", campo: ' . $this->id . ', etapa_id: ' . $etapa->id . ',file_uploader:true},
                          cache: true
                        })
                        .done(function(msg) {
                            var a = document.createElement("a");
                            a.style = "display:none;";
                            a.download = "FirmaDocumento.jnlp";
                            var blob = new Blob([msg]);
                            if (navigator.appVersion.toString().indexOf(".NET") > 0){
                              window.navigator.msSaveBlob(blob, "' . $file->filename . '.jnlp");
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
            </script>';
                }
            }
            
            $display.='</div>';
        }
        $display .= '
            <script>
                $(document).ready(function () {
                    var $parentDiv = $("div[data-id='. $this->id .']");
                   var input =$parentDiv.find("input[type=file]").attr("title","Subir '. $this->etiqueta .'");
                   //console.log(input);
                });
            </script>';
        $display.='</div>';
        return $display;
    }

    public function extraForm() {
        $filetypes = array();
        if (isset($this->extra->filetypes))
            $filetypes = $this->extra->filetypes;

        $output = '<label for="extensiones">Tipos de archivos permitidos</label>';
        $output.='<select id="extensiones" name="extra[filetypes][]" multiple style="height:200px;">';
        $output.='<option value="jpg" ' . (in_array('jpg', $filetypes) ? 'selected' : '') . '>jpg</option>';
        $output.='<option value="jpeg" ' . (in_array('jpeg', $filetypes) ? 'selected' : '') . '>jpeg</option>';
        $output.='<option value="png" ' . (in_array('png', $filetypes) ? 'selected' : '') . '>png</option>';
        $output.='<option value="gif" ' . (in_array('gif', $filetypes) ? 'selected' : '') . '>gif</option>';
        $output.='<option value="pdf" ' . (in_array('pdf', $filetypes) ? 'selected' : '') . '>pdf</option>';
        $output.='<option value="doc" ' . (in_array('doc', $filetypes) ? 'selected' : '') . '>doc</option>';
        $output.='<option value="docx" ' . (in_array('docx', $filetypes) ? 'selected' : '') . '>docx</option>';
        $output.='<option value="odt" ' . (in_array('odt', $filetypes) ? 'selected' : '') . '>odt</option>';
        $output.='<option value="xls" ' . (in_array('xls', $filetypes) ? 'selected' : '') . '>xls</option>';
        $output.='<option value="xlsx" ' . (in_array('xlsx', $filetypes) ? 'selected' : '') . '>xlsx</option>';
        $output.='<option value="mpp" ' . (in_array('mpp', $filetypes) ? 'selected' : '') . '>mpp</option>';
        $output.='<option value="vsd" ' . (in_array('vsd', $filetypes) ? 'selected' : '') . '>vsd</option>';
        $output.='<option value="ppt" ' . (in_array('ppt', $filetypes) ? 'selected' : '') . '>ppt</option>';
        $output.='<option value="pptx" ' . (in_array('pptx', $filetypes) ? 'selected' : '') . '>pptx</option>';
        $output.='<option value="zip" ' . (in_array('zip', $filetypes) ? 'selected' : '') . '>zip</option>';
        $output.='<option value="rar" ' . (in_array('rar', $filetypes) ? 'selected' : '') . '>rar</option>';
        $output.='<option value="wmv" ' . (in_array('wmv', $filetypes) ? 'selected' : '') . '>wmv</option>';
        $output.='<option value="avi" ' . (in_array('avi', $filetypes) ? 'selected' : '') . '>avi</option>';
        $output.='<option value="mov" ' . (in_array('mov', $filetypes) ? 'selected' : '') . '>mov</option>';
        $output.='<option value="aac" ' . (in_array('aac', $filetypes) ? 'selected' : '') . '>aac</option>';
        $output.='<option value="mp4" ' . (in_array('mp4', $filetypes) ? 'selected' : '') . '>mp4</option>';
        $output.='<option value="mp3" ' . (in_array('mp3', $filetypes) ? 'selected' : '') . '>mp3</option>';
        $output.='<option value="3gp" ' . (in_array('3gp', $filetypes) ? 'selected' : '') . '>3gp</option>';
        $output.='<option value="wma" ' . (in_array('wma', $filetypes) ? 'selected' : '') . '>wma</option>';
        $output.='<option value="vox" ' . (in_array('vox', $filetypes) ? 'selected' : '') . '>vox</option>';
        $output.='<option value="ra" ' . (in_array('ra', $filetypes) ? 'selected' : '') . '>ra</option>';
        $output.='<option value="rm" ' . (in_array('rm', $filetypes) ? 'selected' : '') . '>rm</option>';
        $output.='<option value="vf" ' . (in_array('vf', $filetypes) ? 'selected' : '') . '>vf</option>';
        $output.='<option value="mpg" ' . (in_array('mpg', $filetypes) ? 'selected' : '') . '>mpg</option>';
        $output.='<option value="KML" ' . (in_array('KML', $filetypes) ? 'selected' : '') . '>KML</option>';
        $output.='<option value="KMZ" ' . (in_array('KMZ', $filetypes) ? 'selected' : '') . '>KMZ</option>';

        $output.='</select>';

        return $output;
    }

    public function backendExtraFields() {
        $firmar_servidor = isset($this->extra->firmar_servidor) ? $this->extra->firmar_servidor : null;
        $firmar = isset($this->extra->firmar) ? $this->extra->firmar : null;
        $firmar_servidor_keystores = isset($this->extra->firmar_servidor_keystores) ? $this->extra->firmar_servidor_keystores : null;
        $firmado = 0;
        $requerido = isset($this->extra->requerido) ? $this->extra->requerido : null;
        $html = '';
        $html .= '<script>// -- Manejo de extención para firma de servidor
                  $(document).ready(function() {
                  comprobar_firma();
                      $("#extensiones").change(function() {
                      comprobar_firma();
                      });  
                    function comprobar_firma(){
                    var cantidad_extension=0;
                      $(\'#extensiones :selected\').each(function(i, sel){ 
                         cantidad_extension++;
                      });
                      if(cantidad_extension==1 && $(\'#extensiones :selected\').val()=="pdf"){
                          $("#firmar_servidor_archivo").show();
                          //console.log($(\'#extensiones :selected\').val());
                       }else{
                            $("#firmar_servidor_archivo").hide();                       
                            $("#firmar_servidor").removeAttr(\'checked\');                       
                            $("#firmar").removeAttr(\'checked\');                       
                            $("#keystores").val(\'\');  
                            $("#firmar_servidor_keystores").hide();
                       }
                    }

                  });</script>';
        $html .= '<div id="firmar_servidor_archivo" style="display:none;">';
        $html .= '<br /><label class="checkbox" for="firmar"><input type="checkbox" name="extra[firmar]" ' . ($firmar ? 'checked' : '') . ' id="firmar" /> Deseo firmar con token en este paso.</label>';
        $html .= '<label class="checkbox" for="firmar_servidor"><input type="checkbox" name="extra[firmar_servidor]" ' . ($firmar_servidor ? 'checked' : '') . ' id="firmar_servidor" /> Deseo firmar el documento en servidor.</label>';
        $html .= '<br /><div class="well form-horizontal" ' . (!$firmar_servidor ? 'style="display:none;"' : '') . ' class="input" id="firmar_servidor_keystores">';
        $html .= '<div class="control-group">';
        $html .= '<label class="control-label" for="keystores">Keystores para la firma:</label>';
        $html .= '<div class="controls"><input type="text" id="keystores" name="extra[firmar_servidor_keystores]" placeholder="key1, key2, key3" value="' . ($firmar_servidor_keystores ? $firmar_servidor_keystores : '') . '" /></div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<script>// -- Manejo de checkbox para firmas de documentos en servidor
                  $(document).ready(function() {
                      $("#firmar_servidor").change(function() {
                          if(this.checked) {
                              $("#firmar_servidor_keystores").show();
                          }
                          else {
                              $("#firmar_servidor_keystores").hide();
                          }
                      });                     

                  });</script>';

        return $html;
    }

}

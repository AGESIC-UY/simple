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
        if (!$etapa_id) {
            return '<div class="control-group"><div class="controls"><a class="btn btn-success" href="#"><span class="icon-download-alt icon-white"></span> ' . $this->etiqueta . '</a></div></div>';
        }

        $etapa=Doctrine::getTable('Etapa')->find($etapa_id);

        if (!$dato) {   //Generamos el documento, ya que no se ha generado
            $file=$this->Documento->generar($etapa->id, $this->id);

            $dato = new DatoSeguimiento();
            $dato->nombre = $this->nombre;
            $dato->valor = $file->filename;
            $dato->etapa_id = $etapa->id;
            $dato->save();
        }else{
            $file=Doctrine::getTable('File')->findOneByTipoAndFilename('documento',$dato->valor);
            if($etapa->pendiente && isset($this->extra->regenerar) && $this->extra->regenerar){
                $file->delete();
                $file=$this->Documento->generar($etapa->id, $this->id);
                $dato->valor = $file->filename;
                $dato->save();
            }
        }

        $display = '<div class="control-group"><div class="controls"><a class="btn btn-success" href="' . site_url('documentos/get/' . $file->filename) . '?id='.$file->id.'&amp;token='.$file->llave.'"><span class="icon-download-alt icon-white"></span> ' . $this->etiqueta . '</a></div></div>';

        return $display;
    }

    private function displayFirmador($modo, $dato, $etapa_id) {
        if (!$etapa_id) {
            return '<p>' . $this->etiqueta . '</p>';
        }

        $etapa=Doctrine::getTable('Etapa')->find($etapa_id);

        if (!$dato) {   //Generamos el documento, ya que no se ha generado
            $file=$this->Documento->generar($etapa->id, $this->id);

            $dato = new DatoSeguimiento();
            $dato->nombre = $this->nombre;
            $dato->valor = $file->filename;
            $dato->etapa_id = $etapa->id;
            $dato->save();
        }else{
            $file=Doctrine::getTable('File')->findOneByTipoAndFilename('documento',$dato->valor);
            if($etapa->pendiente && isset($this->extra->regenerar) && $this->extra->regenerar){
                $file->delete();
                $file=$this->Documento->generar($etapa->id, $this->id);
                $dato->valor = $file->filename;
                $dato->save();
            }
        }

        $display = '<p>'.$this->etiqueta.'</p>';
        $display .= '<div id="exito" class="alert alert-success" style="display: none;">Documento fue firmado con éxito.</div>';
        $display .= '<p><a class="btn btn-info" href="' . site_url('documentos/get/' . $dato->valor) .'?id='.$file->id.'&amp;token='.$file->llave. '"><span class="icon-search icon-white"></span> Previsualizar el documento</a></p>';

        $display .= '
            <script>
                $(document).ready(function() {
                    $("#firmar_documento_ext").click(function() {
                        $.ajax({
                          type: "post",
                          url: "'. HOST_SISTEMA .'/etapas/firmar_documento",
                          data: {filename: "'. $file->filename .'", campo: '. $this->id .'},
                          cache: false
                        })
                        .done(function(msg) {
                            var a = document.createElement("a");
                            a.style = "display:none;";
                            a.download = "FirmaDocumento.jnlp";
                            var blob = new Blob([msg]);
                            var url = window.URL.createObjectURL(blob, {type: "application/x-java-jnlp-file"});
                            a.href = url;
                            document.body.appendChild(a);
                            a.click();
                        });
                    });
                });
            </script>
            <div id="firmaDiv">
                <label>Seleccione la firma</label>
                <div style="float: left;"></div>
                <div><button type="button" class="btn btn-success" id="firmar_documento_ext"><span class="icon-pencil icon-white"></span> Firmar Documento</button></div>
            </div>';

        return $display;
    }

    public function backendExtraFields() {
        $regenerar=isset($this->extra->regenerar)?$this->extra->regenerar:null;
        $firmar=isset($this->extra->firmar)?$this->extra->firmar:null;
        $firmar_servidor=isset($this->extra->firmar_servidor)?$this->extra->firmar_servidor:null;
        $firmar_servidor_keystores=isset($this->extra->firmar_servidor_keystores)?$this->extra->firmar_servidor_keystores:null;
        $firmar_servidor_momento=isset($this->extra->firmar_servidor_momento)?$this->extra->firmar_servidor_momento:null;

        $html='<label for="documento_id">Documento</label>';
        $html.='<select name="documento_id" id="documento_id">';
        $html.='<option value="">Seleccionar</option>';
        foreach($this->Formulario->Proceso->Documentos as $d)
            $html.='<option value="'.$d->id.'" '.($this->documento_id==$d->id?'selected':'').'>'.$d->nombre.'</option>';
        $html.='</select>';

        $html.='<label class="radio" for="primera_'.$d->id.'"><input id="primera_'.$d->id.'" type="radio" name="extra[regenerar]" value="0" '.(!$regenerar?'checked':'').' /> El documento se genera solo la primera vez que se visualiza este campo.</label>';
        $html.='<label class="radio" for="cada_'.$d->id.'"><input id="cada_'.$d->id.'" type="radio" name="extra[regenerar]" value="1" '.($regenerar?'checked':'').' /> El documento se regenera cada vez que se visualiza este campo.</label>';
        $html.='<br /><label class="checkbox" for="firmar"><input type="checkbox" name="extra[firmar]" '.($firmar?'checked':'').' id="firmar" /> Deseo firmar con token en este paso.</label>';
        $html.='<label class="checkbox" for="firmar_servidor"><input type="checkbox" name="extra[firmar_servidor]" '.($firmar_servidor?'checked':'').' id="firmar_servidor" /> Deseo firmar el documento en servidor.</label>';
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

    public function backendExtraValidate() {
        parent::backendExtraValidate();

        $CI= &get_instance();
        $CI->form_validation->set_rules('documento_id','Documento','required');
    }
}

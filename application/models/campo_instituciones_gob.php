<?php
require_once('campo.php');
class CampoInstitucionesGob extends Campo{

    public $requiere_datos=false;

    protected function display($modo, $dato) {
        $display = '<div class="control-group">';
        $display.= '<label class="control-label" for="entidades_'.$this->id.'" data-fieldset="'.$this->fieldset.'">' . $this->etiqueta . (in_array('required', $this->validacion) ? '*:' : ' (Opcional):') . '</label>';
        $display.= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
        $display.= '<select class="entidades" id="entidades_'.$this->id.'" data-id="'.$this->id.'" name="' . $this->nombre . '[entidad]" ' . ($modo == 'visualizacion' ? 'readonly' : '') . '>';
        $display.= '<option>Seleccionar</option>';
        $display.= '</select>';
        $display.='</div>';
        $display.='</div>';
        $display.= '<div class="control-group">';
        $display.= '<label class="control-label" for="instituciones_'.$this->id.'">Sub Institución</label>';
        $display.= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
        $display.= '<select class="instituciones" id="instituciones_'.$this->id.'" data-id="'.$this->id.'" name="' . $this->nombre . '[servicio]" ' . ($modo == 'visualizacion' ? 'readonly' : '') . '>';
        $display.= '<option value="">Seleccionar</option>';
        $display.= '</select>';
        if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';
        $display.='</div>';
        $display.='</div>';

        $display.='
            <script>
                $(document).ready(function(){
                    var justLoadedEntidad=true;
                    var justLoadedInstitucion=true;
                    var defaultEntidad="'.($dato && $dato->valor?$dato->valor->entidad:'').'";
                    var defaultInstitucion="'.($dato && $dato->valor?$dato->valor->servicio:'').'";

                    updateEntidades();

                    function updateEntidades(){
                        $.getJSON("https://apis.modernizacion.cl/instituciones/api/entidades?callback=?",function(data){
                            var html="<option>Seleccionar</option>";
                            $(data.items).each(function(i,el){
                                html+="<option value=\""+el.nombre+"\" data-id=\""+el.codigo+"\">"+el.nombre+"</option>";
                            });
                            $("select.entidades[data-id='.$this->id.']").html(html).change(function(event){
                                var selectedId=$(this).find("option:selected").data("id");
                                updateInstituciones(selectedId);
                            });

                            if(justLoadedEntidad){
                                $("select.entidades[data-id='.$this->id.']").val(defaultEntidad).change();
                                justLoadedEntidad=false;
                            }
                        });
                    }

                    function updateInstituciones(entidadId){

                        $.getJSON("https://apis.modernizacion.cl/instituciones/api/entidades/"+entidadId+"/instituciones?callback=?",function(data){
                            var html="<option>Seleccionar</option>";
                            if(data){
                                $(data.items).each(function(i,el){
                                    html+="<option value=\""+el.nombre+"\">"+el.nombre+"</option>";
                                });
                            }
                            $("select.instituciones[data-id='.$this->id.']").html(html);

                            if(justLoadedInstitucion){
                                $("select.instituciones[data-id='.$this->id.']").val(defaultInstitucion).change();
                                justLoadedInstitucion=false;
                            }
                        });
                    }
                });



            </script>';

        return $display;
    }

    public function formValidate() {
        $CI=& get_instance();
        $CI->form_validation->set_rules($this->nombre.'[entidad]', $this->etiqueta.' - Entidad', implode('|', $this->validacion));
        $CI->form_validation->set_rules($this->nombre.'[servicio]', $this->etiqueta.' - Servicio', implode('|', $this->validacion));
    }

}

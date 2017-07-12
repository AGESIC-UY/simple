<?php
require_once('campo.php');

class CampoAgenda extends Campo{

    public $requiere_nombre=false;
    public $requiere_datos=false;
    public $estatico=true;
    public $etiqueta_tamano='xxlarge';

    function setTableDefinition() {
        parent::setTableDefinition();

        $this->hasColumn('readonly','bool',0,array('default'=>0));
    }

    function setUp() {
        parent::setUp();
        $this->setTableName("campo");
    }

    protected function display($modo, $dato, $etapa_id) {
        if($etapa_id) {
            $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
            $regla=new Regla($this->valor_default);
            $valor_default=$regla->getExpresionParaOutput($etapa->id);
        }
        else {
            $valor_default=$this->valor_default;
        }

        if(isset($etapa)) {
          preg_match('/('. $etapa->id .')\/([0-9]*)/', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $match);

          if(!$match) {
            $secuencia = 1;
          }
          else {
            $secuencia = (int)$match[2] + 1;
          }

          $url = parse_url($this->extra->url);
          $nueva_url = $this->extra->url;

          // -- Obtiene el ID de transaccion almacenado en una variable para pasarsela a Agenda.
          $id_transaccion_traza = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_transaccion_traza', $etapa->id);

          if(isset($url['query'])) {
            // -- Obtiene los datos correspondientes de acuerdo a las variables de formulario en el caso de que tenga.
            preg_match_all('/(@@[a-zA-Z]*)/', $nueva_url, $variables);
            if($variables[0]) {
              foreach($variables[0] as $variable) {
                $variable_nombre = str_replace('@@', '', $variable);
                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($variable_nombre, $etapa->id);
                if ($dato) {
                  $nueva_url = str_replace($variable, $dato->valor, str_replace(';=', '', $nueva_url));
                }
              }
            }

            $url = $nueva_url . '&t=' . $id_transaccion_traza->valor . '&u='. site_url('etapas/ejecutar/' . $etapa->id . '/' . $secuencia);
          }
          else {
            $url = $nueva_url . '&t=' . $id_transaccion_traza->valor . '?u='. site_url('etapas/ejecutar/' . $etapa->id . '/' . $secuencia);
          }
        }
        else {
          $url = '';
        }

        $CI = &get_instance();
        if(!UsuarioSesion::usuarioMesaDeEntrada() && !$CI->session->userdata('id_usuario_ciudadano')) { //si el funcionario NO esta actuando como ciudadano
          $display = '<script>$(document).ready(function() {$(".form-action-buttons").find(".btn-primary").attr("disabled", true).addClass("hidden");});</script>';
        }

        if($this->requiere_agendar && UsuarioSesion::usuarioMesaDeEntrada() && $CI->session->userdata('id_usuario_ciudadano')){
          $display = '<script>$(document).ready(function() {$(".form-action-buttons").find(".btn-primary").attr("disabled", true).addClass("hidden");});</script>';
        }

        else if (!$this->requiere_agendar && $CI->uri->segment(3) == 'ver_etapa'){
            return '<div class="controls"><strong style="color:green">'.MENSAJE_AGENDA_CONFIRMADA_FUNCIONARIO.'</strong></div>';
        }

        $display .= '<div class="no-margin-box">';
        $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
        $display .= '<div class="well text-center box-agenda" id="'.$this->id.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . '>';
        $display .= '<p>'. $this->etiqueta .'</p>';
        $display .= '<a class="btn btn-primary" href="'. $url . '">Ir a Agenda</a>';
        $display .= '</div>';
        $display .= '</div>';
        $display .= '</div>';


        return $display;
    }

    public function backendExtraFields() {
        if(isset($this->extra->url)) {
          $url = $this->extra->url;
        }
        else {
          $url = '';
        }

        $display = '<label for="url_agenda">URL de agenda</label> <textarea id="url_agenda" class="input-xxlarge" type="text" name="extra[url]">'. str_replace(';=', '', $url) .'</textarea>';
        return $display;
    }

    public function backendExtraValidate() {
        $CI=&get_instance();
        $CI->form_validation->set_rules('extra[url]', 'extra[url]', 'required|prep_url');
    }
}

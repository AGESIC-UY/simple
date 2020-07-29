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
          $secuencia_real =  $secuencia-1;
          $num_paso_y_secuencia = $this->obtener_numero_paso_y_secuencia_traza($etapa_id,$secuencia_real);
          $paso = ($num_paso_y_secuencia['paso']+1);

            $url = $nueva_url . '&t=' . $id_transaccion_traza->valor.'-'.$paso.'&u='. site_url('etapas/ejecutar/' . $etapa->id . '/' . $secuencia);
          }
          else {
            $url = $nueva_url . '&t=' . $id_transaccion_traza->valor.'-'.$paso.'?u='. site_url('etapas/ejecutar/' . $etapa->id . '/' . $secuencia);
          }
        }
        else {
          $url = '';
        }

        $funcionario_actuando_como_ciudadano = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('funcionario_actuando_como_ciudadano', $etapa_id);

        $CI = &get_instance();
        if(!$CI->session->userdata('id_usuario_ciudadano')) {
          //si el funcionario NO esta actuando como ciudadano
          $display = '<script>$(document).ready(function() {$(".form-action-buttons").find(".btn-primary").attr("disabled", true).addClass("hidden");});</script>';
        }

        $CI->session->set_userdata('url_agenda',$url);

        if($this->requiere_agendar && UsuarioSesion::usuarioMesaDeEntrada() && $CI->session->userdata('id_usuario_ciudadano')){
          $display = '<script>$(document).ready(function() {$(".form-action-buttons").find(".btn-primary").attr("disabled", true).addClass("hidden");});</script>';
        }else if ($funcionario_actuando_como_ciudadano && ($CI->uri->segment(3) == 'ver_etapa' || $modo == 'visualizacion')){
            return '<div class="controls"><strong class="green">'.MENSAJE_AGENDA_CONFIRMADA_FUNCIONARIO.'</strong></div>';
        }else if ($CI->uri->segment(3) == 'ver_etapa' || $modo == 'visualizacion'){
            return '<div class="controls"><strong class="green">La agenda ha sido gestionada por el ciudadano</strong></div>';
        }

        $display .= '<div class="no-margin-box">';
        $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
        $display .= '<div class="well text-center box-agenda" id="'.$this->id.'">';
        $display .= '<p>'. $this->etiqueta .'</p>';
        $display .= '<button type="button" class="btn btn-primary btn-agenda">Ir a Agenda</button>';
        //se comenta por tema de que no modifiquen la URL directamente se tiene que invocar a un controller ahora.
        //$display .= '<a class="btn btn-primary" href="'. $url . '">Ir a Agenda</a>';
        $display .= '</div>';
        $display .= '</div>';
        $display .= '</div>';

        $display .= '<script>
        $(document).ready(function() {

          $(".btn-agenda").click(function(event) {

            //Traza sub-proceso
            $.ajax({
              type: "POST",
              dataType: "text",
              url: document.Constants.host + "/etapas/trazabilidad_sub_proceso_agenda",
              data: {
                "etapa_id":'.$etapa_id.',
                "secuencia":'.$secuencia_real.'
              }
            }).done(function(data){
              //Traza linea
              $.ajax({
                type: "POST",
                dataType: "text",
                url: document.Constants.host + "/etapas/trazabilidad_linea_agenda_externa",
                data: {
                        "etapa_id":'.$etapa_id.',
                        "secuencia":'.$secuencia_real.'
                      }
                  }).fail(function(jqXHR, textStatus, errorThrown) {
                      console.log("La solicitud a fallado al enviar traza");
                  });
                //Termina traza linea
            })
            .done(function(){
              ir_agenda_externa();
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
              console.log("La solicitud a fallado al enviar traza");
            });
            //Termina traza sub-proceso

            });

        });
        </script>';


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

    public function obtener_numero_paso_y_secuencia_traza($etapa_id, $secuencia) {

      $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
      $paso = $etapa->getPasoEjecutable($secuencia);
      $formulario = $paso->Formulario;

      $tarea_inicial = $formulario->Proceso->getTareaInicial();

      $num_paso = $secuencia;
      $num_paso_linea = $secuencia + 1;

      $traza_existente = Doctrine_Query::create()
          ->from('Trazabilidad ts')
          ->where('ts.id_tramite = ? AND ts.id_etapa = ? AND ts.num_paso_real = ?',
            array($etapa->Tramite->id, $etapa->id, $paso->orden))
          ->limit(1)
          ->fetchOne();

          $paso_existe = null;
          if(!empty($traza_existente)) {
            $paso_existe = $traza_existente->num_paso_real;
          }

          $traza_tramite = Doctrine_Query::create()
                  ->from('Trazabilidad ts')
                  ->where('ts.id_tramite = ?', array($etapa->Tramite->id))
                  ->orderBy('secuencia DESC')
                  ->limit(1)
                  ->fetchOne();

          if(empty($traza_tramite)) {
            return;
          }
          else {
            $traza_tramite_actual = Doctrine_Query::create()
                  ->from('Trazabilidad ts')
                  ->where('ts.id_tramite = ? AND ts.id_etapa = ?', array($etapa->Tramite->id, $etapa->id))
                  ->orderBy('secuencia DESC')
                  ->fetchOne();

            $sec = $traza_tramite->secuencia;
            $sec_linea = $sec;

            if(empty($traza_tramite_actual)) {
              $traza_misma_tarea = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso->orden))
                    ->orderBy('secuencia DESC')
                    ->fetchOne();

              $num_paso = $traza_tramite->num_paso;
              $num_paso_linea = (!$traza_misma_tarea ? $num_paso : $traza_misma_tarea->num_paso);
            }
            else {
              $num_paso = (!$paso_existe ? $traza_tramite->num_paso: $paso_existe);
              $num_paso_linea = (!$paso_existe ? $num_paso : $paso_existe);
            }
          }

          $datos = array (
                  'secuencia' => $sec_linea,
                  'paso' => $num_paso_linea,
                );

        return $datos;
    }
}

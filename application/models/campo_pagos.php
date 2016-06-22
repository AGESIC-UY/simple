<?php
require_once('campo.php');

class CampoPagos extends Campo {

    public $requiere_nombre=true;
    public $requiere_datos=false;
    public $estatico=true;
    public $etiqueta_tamano='xxlarge';

    function setTableDefinition() {
        parent::setTableDefinition();

        $this->hasColumn('readonly','bool',1,array('default'=>1));
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

      foreach($this->Formulario->Proceso->Acciones as $accion) {
        if($accion->id == $valor_default) {
          $pasarela = Doctrine_Query::create()
              ->from('PasarelaPagoAntel')
              ->where('pasarela_pago_id = ?', $accion->extra->pasarela_pago_id)
              ->execute();
          $pasarela = $pasarela[0];
        }
      }

      $fecha = str_replace('/', '', $pasarela->vencimiento.'0000');
      $fecha = strtotime($fecha);
      $fecha_vencimiento = date("YmdHi", $fecha);

      $id_sol =  Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_sol_pasarela_pagos', $etapa_id);
      $token =  Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('token_pasarela_pagos', $etapa_id);

      $display = '<div class="no-margin-box">';
      $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
      $display .= '<div class="well text-center">';
      $display .= '<div data-action="'. POST_PASARELA_PAGO .'" id="form_pasarela_pago">';

      $display .= '<input name="IdSol" value="'. (isset($id_sol->valor) ? $id_sol->valor : '') .'" type="hidden" />';
      $display .= '<input name="Token" value="'. (isset($token->valor) ? $token->valor : '') .'" type="hidden" />';

      $display .= '<input name="IdTramite" value="'. $pasarela->id_tramite .'" type="hidden" />';
      $display .= '<input name="ImporteTasa1" value="'. $pasarela->tasa_1 .'" type="hidden" />';
      $display .= '<input name="ImporteTasa2" value="'. $pasarela->tasa_2 .'" type="hidden" />';
      $display .= '<input name="ImporteTasa3" value="'. $pasarela->tasa_3 .'" type="hidden" />';
      $display .= '<input name="FechaVto" value="'. $fecha_vencimiento .'" type="hidden" />';
      $display .= '<input name="UsuarioPeu" value="anonimo" type="hidden" />';
      $display .= '<input name="CodsDesglose" value="'. $pasarela->codigos_desglose .'" type="hidden" />';
      $display .= '<input name="MontosDesglose" value="'. $pasarela->montos_desglose .'" type="hidden" />';
      $display .= '<input name="IdFormaDePago" value="0" type="hidden" />';
      $display .= '<input name="PassOrganismo" value="'. $pasarela->clave_organismo .'" type="hidden" />';

      $display .= '<p>'. $this->etiqueta .'</p>';
      $display .= '<input type="submit" value="Continuar" class="btn btn-primary" />';
      $display .= '</div>';
      $display .= '</div>';
      $display .= '</div>';
      $display .= '</div>';

      if(isset($etapa)) {
        preg_match('/('. $etapa->id .')\/([0-9]*)/', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $match);

        if($match) {
          $secuencia = (int)$match[2] + 1;

          $url_vuelta = site_url('etapas/ejecutar/' . $etapa->id . '/' . $secuencia);
          setcookie('simple_bpm_gwp_redirect', base64_encode($url_vuelta), 0, '/', HOST_SISTEMA_DOMINIO);
        }
      }

      return $display;
    }

    public function backendExtraValidate(){
        $CI=&get_instance();
        $CI->form_validation->set_rules('valor_default', 'Valor_default', 'required');
    }
}

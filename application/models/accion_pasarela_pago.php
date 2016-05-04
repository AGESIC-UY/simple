<?php

require_once('accion.php');

class AccionPasarelaPago extends Accion {

    public function displayForm($operacion_id=null) {
      if (($this->extra) && (!$operacion_id)) {
          $operacion_id = $this->extra->pasarela_pago_id;
      }

      $display = '<input type="hidden" name="extra[pasarela_pago_id]" value="'. $operacion_id .'" />';

      if($operacion_id) {
        $pasarela = Doctrine_Query::create()
            ->from('PasarelaPagoAntel pa')
            ->where('pa.pasarela_pago_id = ?', $operacion_id)
            ->execute();

        $pasarela = $pasarela[0];

        $display .= '<div class="form-horizontal">';

        $display .= '<div class="control-group">';
        $display .= '<label for="operacion" class="control-label">ID de trámite</label>';
        $display .= '<div class="controls">';
        $display .= '<input readonly class="input-large" type="text" name="extra[id_tramite]" value="'. $pasarela->id_tramite .'" />';
        $display .= '</div>';
        $display .= '</div>';

        $display .= '<div class="control-group">';
        $display .= '<label for="operacion" class="control-label">Tasa 1</label>';
        $display .= '<div class="controls">';
        $display .= '<input readonly class="input-large" type="text" name="extra[tasa_1]" value="'. $pasarela->tasa_1 .'" />';
        $display .= '</div>';
        $display .= '</div>';

        $display .= '<div class="control-group">';
        $display .= '<label for="operacion" class="control-label">Tasa 2</label>';
        $display .= '<div class="controls">';
        $display .= '<input readonly class="input-large" type="text" name="extra[tasa_2]" value="'. $pasarela->tasa_2 .'" />';
        $display .= '</div>';
        $display .= '</div>';

        $display .= '<div class="control-group">';
        $display .= '<label for="operacion" class="control-label">Tasa 3</label>';
        $display .= '<div class="controls">';
        $display .= '<input readonly class="input-large" type="text" name="extra[tasa_3]" value="'. $pasarela->tasa_3 .'" />';
        $display .= '</div>';
        $display .= '</div>';

        $display .= '<div class="control-group">';
        $display .= '<label for="operacion" class="control-label">Fecha de vencimiento</label>';
        $display .= '<div class="controls">';

        if(isset($pasarela->vencimiento)) {
            $datetime = date_create_from_format('YmdHi', $pasarela->vencimiento);
            if($datetime) {
                $vencimiento = $datetime->format('d/m/Y H:i');
            }
            else {
              $vencimiento = null;
            }
        }
        else {
            $vencimiento = '';
        }

        $display .= '<div id="pasarela_pago_vencimiento_muestra">';
        $display .= '<span id="pasarela_pago_vencimiento_muestra_texto" class="fecha">';
        $display .= $vencimiento;
        $display .= '</span>';
        $display .= '</div>';
        $display .= '<input readonly type="hidden" id="pasarela_pago_vencimiento" name="extra[vencimiento]" value="'. $pasarela->vencimiento .'" />';
        $display .= '</div>';
        $display .= '</div>';

        $display .= '<div class="control-group">';
        $display .= '<label for="operacion" class="control-label">Códigos de desglose</label>';
        $display .= '<div class="controls">';
        $display .= '<input readonly class="input-large" type="text" name="extra[codigos_desglose]" value="'. $pasarela->codigos_desglose .'" />';
        $display .= '</div>';
        $display .= '</div>';

        $display .= '<div class="control-group">';
        $display .= '<label for="operacion" class="control-label">Montos de desglose</label>';
        $display .= '<div class="controls">';
        $display .= '<input readonly class="input-large" type="text" name="extra[montos_desglose]" value="'. $pasarela->montos_desglose .'" />';
        $display .= '</div>';
        $display .= '</div>';

        $display .= '<input readonly type="hidden" name="extra[operacion]" value="'. $pasarela->operacion .'" />';
        $display .= '<input readonly type="hidden" name="extra[clave_organismo]" value="'. $pasarela->clave_organismo .'" />';
        $display .= '</div>';
      }

      return $display;
    }

    public function validateForm() {
    }

    // -- Solicita token a pasarela y lo almacena en variable @@token_pasarela_pagos
    public function ejecutar(Etapa $etapa, $secuencia = null) {
      $regla = new Regla($this->extra->pasarela_pago_id);
      $pasarela_pago_id = $regla->getExpresionParaOutput($etapa->id);
      $pasarela = Doctrine_Query::create()
                    ->from('PasarelaPagoAntel pa')
                    ->where('pa.pasarela_pago_id = ?', $pasarela_pago_id)
                    ->execute();
      $pasarela = $pasarela[0];

      $id_sol = $etapa->id . mt_rand();

      $parameters_array = array('IdSol' => urlencode($id_sol),
                                'IdTramite' => urlencode($pasarela->id_tramite),
                                'ImporteTasa1' => urlencode($pasarela->tasa_1),
                                'ImporteTasa2' => urlencode($pasarela->tasa_2),
                                'ImporteTasa3' => urlencode($pasarela->tasa_3),
                                'FechaVto' => urlencode($pasarela->vencimiento),
                                'UsuarioPeu' => urlencode('anonimo'),
                                'CodsDesglose' => urlencode($pasarela->codigos_desglose),
                                'MontosDesglose' => urlencode($pasarela->montos_desglose),
                                'IdFormaDePago' => urlencode('0'),
                                'PassOrganismo' => urlencode($pasarela->clave_organismo));

      foreach($parameters_array as $key => $value) { $parameters .= $key . '=' . $value . '&'; }
      rtrim($parameters, '&');

      $header = array(
          "Content-type: text/xml;charset=\"utf-8\"",
          "Accept: text/xml",
          "Cache-Control: no-cache",
          "Pragma: no-cache",
          "Content-length: ".strlen($parameters)
      );

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL,            WS_PASARELA_PAGO);
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, PASARELA_PAGO_TIMEOUT_CONEXION);
      curl_setopt($curl, CURLOPT_TIMEOUT,        PASARELA_PAGO_TIMEOUT_RESPUESTA);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_POST,           true);
      curl_setopt($curl, CURLOPT_POSTFIELDS,     $parameters);
      curl_setopt($curl, CURLOPT_HTTPHEADER,     $header);
      $response = curl_exec($curl);
      $curl_errno = curl_errno($curl); // -- Codigo de error
      $curl_error = curl_error($curl); // -- Descripcion del error
      $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE); // -- Codigo respuesta HTTP
      curl_close($curl);

      // -- Crea la variable id_sol_pasarela_pagos
      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_sol_pasarela_pagos', $etapa->id);
      if ($dato)
          $dato->delete();

      $dato = new DatoSeguimiento();
      $dato->nombre = 'id_sol_pasarela_pagos';
      $dato->valor = (string)$id_sol;
      $dato->etapa_id = $etapa->id;
      $dato->save();

      // -- Crea la variable token_pasarela_pagos
      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('token_pasarela_pagos', $etapa->id);
      if ($dato)
          $dato->delete();

      $dato = new DatoSeguimiento();
      $dato->nombre = 'token_pasarela_pagos';
      $dato->valor = (string)$response;
      $dato->etapa_id = $etapa->id;
      $dato->save();

      echo $response;
    }
}

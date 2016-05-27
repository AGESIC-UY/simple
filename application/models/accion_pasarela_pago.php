<?php

require_once('accion.php');

class AccionPasarelaPago extends Accion {

    public function displayForm($operacion_id=null) {
      if (($this->extra) && (!$operacion_id)) {
          $operacion_id = $this->extra->pasarela_pago_id;

          $id_tramite = ($this->extra ? $this->extra->id_tramite : '');
          $tasa_1 = ($this->extra ? $this->extra->tasa_1 : '');
          $tasa_2 = ($this->extra ? $this->extra->tasa_2 : '');
          $tasa_3 = ($this->extra ? $this->extra->tasa_3 : '');
          $vencimiento = ($this->extra ? $this->extra->vencimiento : '');
          $codigos_desglose = ($this->extra ? $this->extra->codigos_desglose : '');
          $montos_desglose = ($this->extra ? $this->extra->montos_desglose : '');
          $operacion = ($this->extra ? $this->extra->operacion : '');
          $clave_organismo = ($this->extra ? $this->extra->clave_organismo : '');
      }
      else {
        $pasarela = Doctrine_Query::create()
            ->from('PasarelaPagoAntel pa')
            ->where('pa.pasarela_pago_id = ?', $operacion_id)
            ->execute();

        $pasarela = $pasarela[0];

        $id_tramite = $pasarela->id_tramite;
        $tasa_1 = $pasarela->tasa_1;
        $tasa_2 = $pasarela->tasa_2;
        $tasa_3 = $pasarela->tasa_3;
        $vencimiento = $pasarela->vencimiento;
        $codigos_desglose = $pasarela->codigos_desglose;
        $montos_desglose = $pasarela->montos_desglose;
        $operacion = $pasarela->operacion;
        $clave_organismo = $pasarela->clave_organismo;
      }

      $display = '<div class="form-horizontal">';

      $display .= '<input type="hidden" name="extra[pasarela_pago_id]" value="'. $operacion_id .'" />';
      $display .= '<div class="control-group">';
      $display .= '<label for="operacion" class="control-label">ID de trámite</label>';
      $display .= '<div class="controls">';
      $display .= '<input class="input-large" type="text" name="extra[id_tramite]" value="'. $id_tramite .'" />';
      $display .= '</div>';
      $display .= '</div>';

      $display .= '<div class="control-group">';
      $display .= '<label for="operacion" class="control-label">Tasa 1</label>';
      $display .= '<div class="controls">';
      $display .= '<input  class="input-large" type="text" name="extra[tasa_1]" value="'. $tasa_1 .'" />';
      $display .= '</div>';
      $display .= '</div>';

      $display .= '<div class="control-group">';
      $display .= '<label for="operacion" class="control-label">Tasa 2</label>';
      $display .= '<div class="controls">';
      $display .= '<input class="input-large" type="text" name="extra[tasa_2]" value="'. $tasa_2 .'" />';
      $display .= '</div>';
      $display .= '</div>';

      $display .= '<div class="control-group">';
      $display .= '<label for="operacion" class="control-label">Tasa 3</label>';
      $display .= '<div class="controls">';
      $display .= '<input class="input-large" type="text" name="extra[tasa_3]" value="'. $tasa_3 .'" />';
      $display .= '</div>';
      $display .= '</div>';

      $display .= '<div class="control-group">';
      $display .= '<label for="operacion" class="control-label">Fecha de vencimiento</label>';
      $display .= '<div class="controls">';

      if(isset($vencimiento)) {
          $datetime = date_create_from_format('YmdHi', $vencimiento);
          if($datetime) {
              $vencimiento = $datetime->format('d/m/Y H:i');
          }
          else {
            $vencimiento = null;
          }
      }

      $display .= '<div id="pasarela_pago_vencimiento_muestra">';
      $display .= '<span id="pasarela_pago_vencimiento_muestra_texto" class="fecha">';
      $display .= $vencimiento;
      $display .= '</span> ';
      $display .= '<a class="btn calendar" id="pasarela_pago_vencimiento_button" href="#">';
      $display .= '<span class="icon-calendar"></span>';
      $display .= '</a>';
      $display .= '<input type="hidden" id="pasarela_pago_vencimiento" name="extra[vencimiento]" value="'. $vencimiento .'" />';
      $display .= '</div>';
      $display .= '</div>';
      $display .= '</div>';

      $display .= '<div class="control-group">';
      $display .= '<label for="operacion" class="control-label">Códigos de desglose</label>';
      $display .= '<div class="controls">';
      $display .= '<input class="input-large" type="text" name="extra[codigos_desglose]" value="'. $codigos_desglose .'" />';
      $display .= '</div>';
      $display .= '</div>';

      $display .= '<div class="control-group">';
      $display .= '<label for="operacion" class="control-label">Montos de desglose</label>';
      $display .= '<div class="controls">';
      $display .= '<input class="input-large" type="text" name="extra[montos_desglose]" value="'. $montos_desglose .'" />';
      $display .= '</div>';
      $display .= '</div>';

      $display .= '<input  type="hidden" name="extra[operacion]" value="'. $operacion .'" />';
      $display .= '<input readonly type="hidden" name="extra[clave_organismo]" value="'. $clave_organismo .'" />';
      $display .= '</div>';

      return $display;
    }

    public function validateForm() {
    }

    // -- Solicita token a pasarela y lo almacena en variable @@token_pasarela_pagos
    public function ejecutar(Etapa $etapa, $secuencia = null) {
      $pasarela_pago_id = $this->extra->pasarela_pago_id;

      $id_sol = mt_rand() . mt_rand();

      $fecha = str_replace("/", ".", $this->extra->vencimiento);
      $fecha = strtotime($fecha);
      $fecha_vencimiento = date("YmdHi", $fecha);

      $body = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tem="http://tempuri.org/">
               <soap:Header/>
               <soap:Body>
                  <tem:Solicitud>
                     <tem:request>
                        <tem:IdSolicitud>'. $id_sol .'</tem:IdSolicitud>
                        <tem:IdTramite>'. $this->extra->id_tramite .'</tem:IdTramite>
                        <tem:ImporteTasa1>'. $this->extra->tasa_1 .'</tem:ImporteTasa1>
                        <tem:ImporteTasa2>'. $this->extra->tasa_2 .'</tem:ImporteTasa2>
                        <tem:ImporteTasa3>'. $this->extra->tasa_3 .'</tem:ImporteTasa3>
                        <tem:FechaVencimiento>'. $fecha_vencimiento .'</tem:FechaVencimiento>
                        <tem:UsuarioPEU>anonimo</tem:UsuarioPEU>
                        <tem:codigosDesglose>'. $this->extra->codigos_desglose .'</tem:codigosDesglose>
                        <tem:montosDesglose>'. $this->extra->montos_desglose .'</tem:montosDesglose>
                        <tem:IdFormaDePago>0</tem:IdFormaDePago>
                        <tem:Referencia></tem:Referencia>
                        <tem:ConsumidorFinal>0</tem:ConsumidorFinal>
                        <tem:NroFactura></tem:NroFactura>
                        <tem:PassOrganismo>'. $this->extra->clave_organismo .'</tem:PassOrganismo>
                     </tem:request>
                  </tem:Solicitud>
               </soap:Body>
            </soap:Envelope>';

      $header = array(
          "Content-type: text/xml;charset=\"utf-8\"",
          "Accept: text/xml",
          "Cache-Control: no-cache",
          "Pragma: no-cache",
          "Expect: ",
          "Content-length: ".strlen($body)
      );

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL,            WS_PASARELA_PAGO);
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, PASARELA_PAGO_TIMEOUT_CONEXION);
      curl_setopt($curl, CURLOPT_TIMEOUT,        PASARELA_PAGO_TIMEOUT_RESPUESTA);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_POST,           true);
      curl_setopt($curl, CURLOPT_POSTFIELDS,     $body);
      curl_setopt($curl, CURLOPT_HTTPHEADER,     $header);
      $response = curl_exec($curl);
      $curl_errno = curl_errno($curl); // -- Codigo de error
      $curl_error = curl_error($curl); // -- Descripcion del error
      $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE); // -- Codigo respuesta HTTP
      curl_close($curl);

      $xml = new SimpleXMLElement($response);
      if($xml->xpath("//*[local-name() = 'Token']")) {
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

        $token = $xml->xpath("//*[local-name() = 'Token']/text()");

        $dato = new DatoSeguimiento();
        $dato->nombre = 'token_pasarela_pagos';
        $dato->valor = (string)$token[0];
        $dato->etapa_id = $etapa->id;
        $dato->save();
      }
    }
}

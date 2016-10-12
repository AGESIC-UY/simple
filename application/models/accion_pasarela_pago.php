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
          $clave_tramite = ($this->extra ? $this->extra->clave_tramite : '');
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
        $clave_tramite = $pasarela->clave_tramite;
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
      $display .= '<label for="operacion" class="control-label">Fecha de vencimiento (AAAA/MM/DD)</label>';
      $display .= '<div class="controls">';

      $display .= '<input class="input-large" type="text" id="pasarela_pago_vencimiento" name="extra[vencimiento]" value="'. $vencimiento .'" />';
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

      $display .= '<div class="control-group">';
      $display .= '<label for="extra[clave_tramite]" class="control-label">Clave de trámite</label>';
      $display .= '<div class="controls">';
      $display .= '<input class="input-large" type="text" name="extra[clave_tramite]" value="'. $clave_tramite .'" />';
      $display .= '</div>';
      $display .= '</div>';

      $display .= '<input  type="hidden" name="extra[operacion]" value="'. $operacion .'" />';
      $display .= '<input readonly type="hidden" name="extra[clave_organismo]" value="'. $clave_organismo .'" />';
      $display .= '<input readonly type="hidden" name="extra[pasarela_pago_antel_id]" value="'. $pasarela->id .'" />';
      $display .= '</div>';

      return $display;
    }

    public function validateForm() {
    }

    // -- Solicita token a pasarela y lo almacena en variable @@token_pasarela_pagos
    public function ejecutar(Etapa $etapa, $secuencia = null) {
      $pasarela_pago_id = $this->extra->pasarela_pago_id;
      $pasarela_pago_antel_id = $this->extra->pasarela_pago_antel_id;

      try {
        // ID tramite
        preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $this->extra->id_tramite, $variable_encontrada);
        if($variable_encontrada) {
          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa->id);
          $id_tramite = $dato->valor;
        }
        else {
          $id_tramite = $this->extra->id_tramite;
        }

        //  tasa 1
        preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $this->extra->tasa_1, $variable_encontrada);
        if($variable_encontrada) {
          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa->id);
          $tasa_1 = $dato->valor;
        }
        else {
          $tasa_1 = $this->extra->tasa_1;
        }

        //  tasa 2
        preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $this->extra->tasa_2, $variable_encontrada);
        if($variable_encontrada) {
          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa->id);
          $tasa_2 = $dato->valor;
        }
        else {
          $tasa_2 = $this->extra->tasa_2;
        }

        //  tasa 3
        preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $this->extra->tasa_3, $variable_encontrada);
        if($variable_encontrada) {
          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa->id);
          $tasa_3 = $dato->valor;
        }
        else {
          $tasa_3 = $this->extra->tasa_3;
        }

        // vencimiento
        preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $this->extra->vencimiento, $variable_encontrada);
        if($variable_encontrada) {
          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa->id);
          $vencimiento = $dato->valor;
        }
        else {
          $vencimiento = $this->extra->vencimiento;
        }

        $fecha = str_replace('/', '', $vencimiento.'0000');
        $fecha = strtotime($fecha);
        $fecha_vencimiento = date("YmdHi", $fecha);

        //  codigos desglose
        preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $this->extra->codigos_desglose, $variable_encontrada);
        if($variable_encontrada) {
          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa->id);
          $codigos_desglose = $dato->valor;
        }
        else {
          $codigos_desglose = $this->extra->codigos_desglose;
        }

        //  montos desglose
        preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $this->extra->montos_desglose, $variable_encontrada);
        if($variable_encontrada) {
          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa->id);
          $montos_desglose = $dato->valor;
        }
        else {
          $montos_desglose = $this->extra->montos_desglose;
        }

        //  clave de tramite
        preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $this->extra->clave_tramite, $variable_encontrada);
        if($variable_encontrada) {
          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa->id);
          $clave_tramite = $dato->valor;
        }
        else {
          $clave_tramite = $this->extra->clave_tramite;
        }
      }
      catch(Exception $error) {
        log_message('error', $error->getMessage());
      }

      $pago = new Pago();
      $pago->id_tramite = $id_tramite;
      $pago->id_tramite_interno = $etapa->tramite_id;
      $pago->id_etapa = $etapa->id;
      $pago->id_solicitud = 0;
      $pago->estado = 'iniciado';
      $pago->fecha_actualizacion = date('d/m/Y H:i');
      $pago->pasarela = $pasarela_pago_antel_id;
      $pago->save();

      // El id de la solicitud es el autogenerado de la tabla de pagos
      $id_sol = $pago->id;

      $body = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tem="http://tempuri.org/">
               <soap:Header/>
               <soap:Body>
                  <tem:Solicitud>
                     <tem:request>
                        <tem:IdSolicitud>'. $id_sol .'</tem:IdSolicitud>
                        <tem:IdTramite>'. $id_tramite .'</tem:IdTramite>
                        <tem:ImporteTasa1>'. $tasa_1 .'</tem:ImporteTasa1>
                        <tem:ImporteTasa2>'. $tasa_2 .'</tem:ImporteTasa2>
                        <tem:ImporteTasa3>'. $tasa_3 .'</tem:ImporteTasa3>
                        <tem:FechaVencimiento>'. $fecha_vencimiento .'</tem:FechaVencimiento>
                        <tem:UsuarioPEU>anonimo</tem:UsuarioPEU>
                        <tem:codigosDesglose>'. $codigos_desglose .'</tem:codigosDesglose>
                        <tem:montosDesglose>'. $montos_desglose .'</tem:montosDesglose>
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

      if($curl_errno > 0) {
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('ws_error', $etapa->id);
        if($dato)
          $dato->delete();

        $dato = new DatoSeguimiento();
        $dato->nombre = 'ws_error';
        $dato->valor = "Hubo un error al procesar su solicitud. Por favor, vuelva a intentarlo más tarde.";
        $dato->etapa_id = $etapa->id;
        $dato->save();

        log_message('error', $curl_error);
        return false;
      }
      else {
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

          // -- Crea la variable id_tramite_pasarela_pagos
          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_tramite_pasarela_pagos', $etapa->id);
          if ($dato)
              $dato->delete();

          $dato = new DatoSeguimiento();
          $dato->nombre = 'id_tramite_pasarela_pagos';
          $dato->valor = (string)$id_tramite;
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

          $registro_pago = Doctrine_Query::create()
              ->from('Pago p')
              ->where('p.id = ?', $pago->id)
              ->fetchOne();

          $registro_pago->id_solicitud = $pago->id;
          $registro_pago->estado = 'token_solicita';
          $registro_pago->save();

          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(md5($registro_pago->id . '_clave_tramite_pasarela_pagos'), $etapa->id);
          if ($dato)
              $dato->delete();

          $dato = new DatoSeguimiento();
          $dato->nombre = md5($registro_pago->id . '_clave_tramite_pasarela_pagos');
          $dato->valor = (string)$clave_tramite;
          $dato->etapa_id = $etapa->id;
          $dato->save();
        }
      }
    }
}

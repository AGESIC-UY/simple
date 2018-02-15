<?php

function soap_execute($etapa, $servicio, $operacion, $cuerpo_soap) {
  $soap_wsdl = $servicio->wsdl;
  $soap_endpoint_location = $servicio->endpoint_location;

  try {
    preg_match_all("/(?<=@@).*?(?=<\/[a-zA-Z0-9:]*>)/", $cuerpo_soap, $soap_campos);

    $soap_body_new = $cuerpo_soap;
    $regla = new Regla($soap_body_new);
    $cuerpo_soap = $regla->getExpresionParaOutput($etapa->id);
  }
  catch (Exception $e) {
      log_message('error', $e->getMessage());
      show_error($e->getMessage(), $status_code = 500);
  }

  try {
      $soap_header = array(
          "Content-type: text/xml;charset=\"utf-8\"",
          "Accept: text/xml",
          "Cache-Control: no-cache",
          "Pragma: no-cache",
          "Content-length: ".strlen($cuerpo_soap),
          "SOAPAction: ".$operacion->operacion,
          "User-Agent: Mozilla/5.0"
      );

      if ($servicio->requiere_autenticacion == 1) {
        //servicio soap con autenticacion
        switch($servicio->requiere_autenticacion_tipo) {
          case 'autenticacion_basica':
            $soap_do = curl_init();
            curl_setopt($soap_do, CURLOPT_URL, $soap_endpoint_location);
            curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, $servicio->conexion_timeout);
            curl_setopt($soap_do, CURLOPT_TIMEOUT,        $servicio->respuesta_timeout);
            curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($soap_do, CURLOPT_POST,           true);
            curl_setopt($soap_do, CURLOPT_ENCODING,       'gzip');
            curl_setopt($soap_do, CURLOPT_VERBOSE, true);

            if(!empty($servicio->autenticacion_basica_cert)) {
              curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, true);
              curl_setopt($soap_do, CURLOPT_CAINFO, UBICACION_CERTIFICADOS_SOAP . $servicio->autenticacion_basica_cert);
            }
            else {
              curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
            }

            curl_setopt($soap_do, CURLOPT_USERPWD, $servicio->autenticacion_basica_user . ':' . $servicio->autenticacion_basica_pass);
            curl_setopt($soap_do, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            break;
          case 'autenticacion_mutua':
            $soap_do = curl_init();
            curl_setopt($soap_do, CURLOPT_URL, $soap_endpoint_location);
            curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, $servicio->conexion_timeout);
            curl_setopt($soap_do, CURLOPT_TIMEOUT,        $servicio->respuesta_timeout);
            curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($soap_do, CURLOPT_POST,           true);
            curl_setopt($soap_do, CURLOPT_ENCODING,       'gzip');

            //el cliente envia la peticion firmada
            curl_setopt($soap_do, CURLOPT_SSLCERT, UBICACION_CERTIFICADOS_SOAP . $servicio->autenticacion_mutua_client);
            curl_setopt($soap_do, CURLOPT_SSLCERTPASSWD, $servicio->autenticacion_mutua_client_pass);
            curl_setopt($soap_do, CURLOPT_SSLKEY, UBICACION_CERTIFICADOS_SOAP.$servicio->autenticacion_mutua_client_key);

            //verificamos la respuesta
            curl_setopt($soap_do, CURLOPT_CAINFO, UBICACION_CERTIFICADOS_SOAP . $servicio->autenticacion_mutua_server);
            if(!empty($servicio->autenticacion_mutua_user) && !empty($servicio->autenticacion_mutua_pass)) {
              curl_setopt($soap_do, CURLOPT_USERPWD, $servicio->autenticacion_mutua_user . ':' . $servicio->autenticacion_mutua_pass);
            }

            curl_setopt($soap_do, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            break;
          case 'autenticacion_token':
            // --
            break;
        }

        if (!empty(PROXY_WS)){
          curl_setopt($soap_do, CURLOPT_PROXY, PROXY_WS);
        }
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $cuerpo_soap);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER, $soap_header);
        $soap_response = curl_exec($soap_do);
        $curl_errno = curl_errno($soap_do); // -- Codigo de error
        $curl_error = curl_error($soap_do); // -- Descripcion del error
        $http_code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE); // -- Codigo respuesta HTTP
        curl_close($soap_do);
      }
      else {
        //servicio soap sin autenticacion
        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, $soap_endpoint_location);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, $servicio->conexion_timeout);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        $servicio->respuesta_timeout);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST,           true);
        curl_setopt($soap_do, CURLOPT_ENCODING,       'gzip');

        curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $cuerpo_soap);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $soap_header);

        if (!empty(PROXY_WS)){
          curl_setopt($soap_do, CURLOPT_PROXY, PROXY_WS);
        }

        $soap_response = curl_exec($soap_do);
        $curl_errno = curl_errno($soap_do); // -- Codigo de error
        $curl_error = curl_error($soap_do); // -- Descripcion del error
        $http_code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE); // -- Codigo respuesta HTTP
        curl_close($soap_do);
      }

      if($curl_errno > 0 || $http_code != '200') {
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('ws_error', $etapa->id);
        if($dato)
          $dato->delete();

        $dato = new DatoSeguimiento();
        $dato->nombre = 'ws_error';

        if($http_code == '401')
          $dato->valor = "Hubo un error al procesar su solicitud. Error de autenticación.";
        else
          $dato->valor = "Hubo un error al procesar su solicitud. Por favor, vuelva a intentarlo más tarde.";

        $dato->etapa_id = $etapa->id;
        $dato->save();

        log_message('error', "Web service SOAP Body:" . $cuerpo_soap . ' - response:' .$soap_response .' httpcode:' . $http_code . ' curlerrno:' . $curl_errno . ' curlerror:' . $curl_error);
        return true;
      }
  }
  catch (Exception $e) {
    log_message('error', "Web service SOAP Body Exception:" .$e->getMessage() . ' body:' .  $cuerpo_soap . ' - response:' .$soap_response .' httpcode:' . $http_code . ' curlerrno:' . $curl_errno . ' curlerror:' . $curl_error);
    return true;
  }

  $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('ws_error', $etapa->id);
  if($dato)
    $dato->delete();

  $xml = new SimpleXMLElement($soap_response);
  $respuestas = json_decode($operacion->respuestas);

  if($xml->xpath("//*[local-name() = 'faultcode']")) {
    $error_servicio = $xml->xpath("//*[local-name() = 'faultstring']/text()");
    $dato = new DatoSeguimiento();
    $dato->nombre = 'ws_error';
    $dato->valor = (string)$error_servicio[0];
    $dato->etapa_id = $etapa->id;
    $dato->save();

    return true;
  }

  foreach($respuestas->respuestas as $respuesta) {
      $clave = $respuesta->key;
      $xpath = $respuesta->xpath;

      $result = $xml->xpath($xpath);

      // -- Aplica el XSLT al XML obtenido como respuesta SOLO SI la respuesta es de tipo LISTA.
      if($respuesta->tipo == 'lista') {
          $operacion_respuesta = Doctrine_Query::create()->from('WsOperacionRespuesta')->where('respuesta_id = ?', $respuesta->id)->limit(1)->execute();

          $xslt = $operacion_respuesta[0]->xslt;

          // -- Comienza a procesar el XSLT
        $xmldoc = DOMDocument::loadXML($result[0]->saveXML());
        $xsldoc = DOMDocument::loadXML($xslt);

          $proc = new XSLTProcessor();

          $proc->importStyleSheet($xsldoc);

          $xmlobj = $proc->transformToDoc($xmldoc);

          $jsonString = '[';

          $x = 0;
          $j = 0;
          $xmlIterator = new SimpleXMLIterator($xmlobj->saveXML());

          for($xmlIterator->rewind(); $xmlIterator->valid(); $xmlIterator->next()) {
              if ($j == 0) {
                  $jsonString  .= "[";
              }
              else {
                  $jsonString  .= "," . "[";
              }

              $j++;
              $x = 0;

              foreach($xmlIterator->getChildren() as $name => $data) {
                  if($x == 0) {
                      $jsonString .= '"' . $data .'"';
                  }
                  else {
                      $jsonString .= ', "' . $data .'"';
                  }

                  $x++;
              }

              $jsonString .= "]";
          }

          $jsonString .= "]";

          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($clave, $etapa->id);
          if ($dato)
              $dato->delete();

          $dato = new DatoSeguimiento();
          $dato->nombre = $clave;
          $dato->valor = (string)$jsonString;
          $dato->etapa_id = $etapa->id;
          $dato->save();

      }
      elseif($respuesta->tipo == 'xslt') {
          $operacion_respuesta = Doctrine_Query::create()->from('WsOperacionRespuesta')->where('respuesta_id = ?', $respuesta->id)->limit(1)->execute();

          $xslt = $operacion_respuesta[0]->xslt;

          // -- Comienza a procesar el XSLT
        $xmldoc = DOMDocument::loadXML($result[0]->saveXML());
        $xsldoc = DOMDocument::loadXML($xslt);

          $proc = new XSLTProcessor();

          $proc->importStyleSheet($xsldoc);

          $xmlobj = $proc->transformToXml($xmldoc);

          if(!empty($xmlobj)) {
            $xml_limpio = str_replace('<?xml version="1.0"?>', '', trim(preg_replace("/\r|\n/", '', $xmlobj)));
          }
          else {
            $xml_limpio = '';
          }

          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($clave, $etapa->id);
          if ($dato)
              $dato->delete();

          $dato = new DatoSeguimiento();
          $dato->nombre = $clave;
          $dato->valor = $xml_limpio;
          $dato->etapa_id = $etapa->id;
          $dato->save();
      }
      else {
          try {
              $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($clave, $etapa->id);
              if ($dato)
                  $dato->delete();

              $dato = new DatoSeguimiento();

              $dato->nombre = $clave;
              // -- Solo se aceptan valores simples
              if(count($result) > 0) {
                  $dato->valor = (string)$result[0][0];
                  $dato->etapa_id = $etapa->id;
                  $dato->save();
              }
          }
          catch (Exception $e) {
              log_message('error', $e->getMessage());
          }
      }
  }
}

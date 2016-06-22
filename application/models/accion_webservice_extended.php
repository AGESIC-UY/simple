<?php

require_once('accion.php');

class AccionWebserviceExtended extends Accion {

    public function displayForm($operacion_id=null) {
        if (($this->extra) && (!$operacion_id)) {
            $operacion_id = $this->extra->soap_operacion;
        }

        if(isset($operacion_id)) {
            $operacion = Doctrine::getTable('WsOperacion')->find($operacion_id);
            if(isset($operacion->id)) {
              $servicio = Doctrine::getTable('WsCatalogo')->find($operacion->catalogo_id);
            }
        }

        $soap_nombre  = ($this->extra ? $this->extra->soap_nombre : $operacion->nombre);
        $soap_xml  = ($this->extra ? $this->extra->soap_body : $operacion->soap);
        $soap_help  = ($this->extra ? $this->extra->soap_help : $operacion->ayuda);
        $soap_codigo  = ($this->extra ? $this->extra->soap_operacion : $operacion->codigo);
        $soap_operacion  = ($this->extra ? $this->extra->soap_operacion_operacion : $operacion->operacion);

        $display  = '<p class="strong">Esta acción consultará al servicio via SOAP.</p>';
        $display .= '<div class="form-horizontal">';
        $display .= '<div class="control-group">';
        $display .= '<label for="operacion" class="control-label">Nombre de operación</label>';
        $display .= '<div class="controls">';
        $display .= '<input class="input-xxlarge" name="extra[soap_nombre]" type="text" readonly value="' . $soap_nombre . '" />';
        $display .= '</div>';
        $display .= '</div>';
        $display .= '<input class="hidden" type="hidden" name="extra[soap_operacion]" value="' . (isset($soap_codigo) ? $soap_codigo : '')  . '" />';
        $display .= '<input class="hidden" type="hidden" name="extra[soap_operacion_operacion]" value="' . (isset($soap_operacion) ? $soap_operacion : '')  . '" />';
        $display .= '<div class="control-group">';
        $display .= '<label for="cuerpo" class="control-label">Cuerpo SOAP</label>';
        $display .= '<div class="controls">';
        $display .= '<div class="alert alert-info no-margin">Asegúrese de cerrar correctamente los tags antes de guardar los cambios.</div>';
        $display .= '<div class="row-fluid">';
        $display .= '<div class="span9">';
        $display .= '<textarea name="extra[soap_body]" id="soap_body" spellcheck="false" class="large-textarea">' . $soap_xml . '</textarea></div>';
        $display .= '<div class="span3">';
        $display .= '<div class="well">';
        $display .= '<p class="strong"><span class="icon-info-sign"></span> Ayuda</p>';

        $ayuda = explode("\n", $soap_help);
        foreach($ayuda as $a) {
            $display .= '<p class="info">'.$a.'</p>';
        }

        $display .= '</div>';

        $display .= '</div>';
        $display .= '</div>';
        $display .= '</div>';
        $display .= '<textarea name="extra[soap_help]" id="soap_help" spellcheck="false" class="hidden">' . $soap_help . '</textarea>';

        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
        $CI->form_validation->set_rules('extra[soap_body]', 'Cuerpo SOAP', 'required');
    }

    public function ejecutar(Etapa $etapa, $secuencia = null) {
        $regla = new Regla($this->extra->soap_operacion);
        $codigo_operacion = $regla->getExpresionParaOutput($etapa->id);
        $operacion = Doctrine_Query::create()
                      ->from('WsOperacion o')
                      ->where('o.codigo = ?', $codigo_operacion)
                      ->execute();
        $operacion = $operacion[0];

        $servicio = Doctrine::getTable('WsCatalogo')->find($operacion->catalogo_id);

        if($servicio->tipo == 'soap') {
          // WSDL
          $soap_wsdl = $servicio->wsdl;

          // Endpoint loctaion
          $soap_endpoint_location = $servicio->endpoint_location;

          // Cuerpo del SOAP
          $regla = new Regla($this->extra->soap_body);
          $soap_body = $regla->getExpresionParaOutput($etapa->id);

          try {
              preg_match_all("/(?<=@@).*?(?=<\/[a-zA-Z:]*>)/", $soap_body, $soap_campos);
              $campos_encontrados = array();

              foreach($etapa->getPasoEjecutable($secuencia)->Formulario->Campos as $campo) {
                  if(in_array($campo->nombre, $soap_campos[0])) {
                      $campo_dato = Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($campo->nombre, $etapa->id);
                      //$soap_body = str_replace('%'.$campo->nombre.'%', $campo_dato->valor, $soap_body);
                      array_push($campos_encontrados, $campo->nombre .':'.$campo_dato->valor);
                  }
              }
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
                  "Content-length: ".strlen($soap_body)
              );

              $soap_do = curl_init();
              curl_setopt($soap_do, CURLOPT_URL, $soap_endpoint_location);
              curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, $servicio->conexion_timeout);
              curl_setopt($soap_do, CURLOPT_TIMEOUT,        $servicio->respuesta_timeout);
              curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
              curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
              curl_setopt($soap_do, CURLOPT_POST,           true);
              curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $soap_body);
              curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $soap_header);
              $soap_response = curl_exec($soap_do);
              $curl_errno = curl_errno($soap_do); // -- Codigo de error
              $curl_error = curl_error($soap_do); // -- Descripcion del error
              $http_code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE); // -- Codigo respuesta HTTP
              curl_close($soap_do);
          }
          catch (Exception $e) {
            log_message('error', $e->getMessage());
          }
        }
        else {
          try {
            $pdi = Doctrine_Query::create()
                ->from('Pdi c')
                ->where('c.cuenta_id = ?', $etapa->Tarea->Proceso->cuenta_id)
                ->execute();
            $pdi = $pdi[0];

            $uuid = mt_rand();
            $role = $servicio->rol;

            $soapMessagePartOne = "<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:wsa=\"http://www.w3.org/2005/08/addressing\">";
            $soapMessagePartOne .= "<SOAP-ENV:Header><wsa:Action xmlns:env=\"http://schemas.xmlsoap.org/soap/envelope/\" env:mustUnderstand=\"1\">http://schemas.xmlsoap.org/ws/2005/02/trust/Issue</wsa:Action>";
            $soapMessagePartOne .= "<wsa:MessageID>";
            $soapMessagePartOne .= $uuid;
            $soapMessagePartOne .= "</wsa:MessageID>";
            $soapMessagePartOne .= "</SOAP-ENV:Header><SOAP-ENV:Body><wst:RequestSecurityToken xmlns:wst=\"http://schemas.xmlsoap.org/ws/2005/02/trust\">";
            $soapMessagePartOne .= "<wst:TokenType>http://docs.oasis-open.org/wss/oasis-wss-saml-token-profile-1.1#SAMLV1.1</wst:TokenType>";
            $soapMessagePartOne .= "<wsp:AppliesTo xmlns:wsp=\"http://schemas.xmlsoap.org/ws/2004/09/policy\"><wsa:EndpointReference>";
            $soapMessagePartOne .= "<wsa:Address>";
            $soapMessagePartOne .= $servicio->url_logica;
            $soapMessagePartOne .= "</wsa:Address></wsa:EndpointReference></wsp:AppliesTo>";
            $soapMessagePartOne .= "<wst:RequestType>http://schemas.xmlsoap.org/ws/2005/02/trust/Issue</wst:RequestType><wst:Issuer>";
            $soapMessagePartOne .= "<wsa:Address>" . $pdi->policy . "</wsa:Address></wst:Issuer><wst:Base>";

            $date = new DateTime(date("Y-m-d\TH:i:s"));
            $date->setTimezone(new DateTimeZone('GMT'));

            $date->add(new DateInterval('PT15M'));
            $future_date = $date->format("Y-m-d\TH:i:s.000");

            $date = new DateTime(date("Y-m-d\TH:i:s"));
            $date->setTimezone(new DateTimeZone('GMT'));

            $date->sub(new DateInterval('PT15M'));
            $past_date = $date->format("Y-m-d\TH:i:s.000");

            $assertion = '<saml:Assertion xmlns:saml="urn:oasis:names:tc:SAML:1.0:assertion" xmlns="urn:oasis:names:tc:SAML:1.0:assertion" IssueInstant="'. date("Y-m-d\TH:i:s.000") .'Z" Issuer="Agesic" MajorVersion="1" MinorVersion="1" AssertionID="_'. mt_rand().mt_rand() .'"><saml:Conditions NotBefore="'. $past_date .'Z" NotOnOrAfter="'. $future_date .'Z"/><saml:AuthenticationStatement AuthenticationInstant="'. date("Y-m-d\TH:i:s.000") .'Z" AuthenticationMethod="urn:oasis:names:tc:SAML:1.0:am:password"><saml:Subject><saml:NameIdentifier Format="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress">ou=gerencia de proyectos,o=agesic</saml:NameIdentifier><saml:SubjectConfirmation><saml:ConfirmationMethod>urn:oasis:names:tc:SAML:1.0:cm:bearer</saml:ConfirmationMethod></saml:SubjectConfirmation></saml:Subject></saml:AuthenticationStatement><saml:AttributeStatement><saml:Subject><saml:NameIdentifier Format="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress">ou=gerencia de proyectos,o=agesic</saml:NameIdentifier><saml:SubjectConfirmation><saml:ConfirmationMethod>urn:oasis:names:tc:SAML:1.0:cm:bearer</saml:ConfirmationMethod></saml:SubjectConfirmation></saml:Subject><saml:Attribute AttributeName="User" AttributeNamespace="urn:tokensimple"><saml:AttributeValue xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:type="xs:string" /></saml:Attribute></saml:AttributeStatement></saml:Assertion>';

            $assertion_base64 = base64_encode($assertion);
            $raw_signed_assertion = exec("java -jar ". JAR_FIRMA ." ". $pdi->certificado_organismo ." ". $pdi->clave_organismo ." $assertion_base64 2>&1");

            preg_match('/^ERR:/', $raw_signed_assertion, $match);

            if(!empty($match)) {
              echo str_replace('ERR: ', '', $raw_signed_assertion) . "\n";
              return false;
            }

            $signed_assertion = base64_decode(str_replace('OK: ', '', $raw_signed_assertion));

            $signed_assertion = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>', '', $signed_assertion);
            $soapMessagePartTwo = "</wst:Base><wst:SecondaryParameters><wst:SecondaryParameters>";
            $soapMessagePartTwo .= $role;
            $soapMessagePartTwo .= "</wst:SecondaryParameters></wst:SecondaryParameters></wst:RequestSecurityToken></SOAP-ENV:Body></SOAP-ENV:Envelope>";

            $body = $soapMessagePartOne . $signed_assertion . $soapMessagePartTwo;

            // Invoca al servicio para obtener el token
            $header = array("Content-type: text/xml; charset=UTF-8",
                            "Accept: */*",
                            "Cache-Control: no-cache",
                            "Pragma: no-cache",
                            "SOAPAction: \"\"",
            		"Transfer-Encoding: chunked",
            		"Connection: keep-alive",
            		"Expect: "
            );

            $soap_do = curl_init();
            curl_setopt($soap_do, CURLOPT_URL, $pdi->sts);
            curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, $servicio->conexion_timeout);
            curl_setopt($soap_do, CURLOPT_TIMEOUT,        $servicio->respuesta_timeout);
            curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($soap_do, CURLOPT_POST,           true);
            curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $body);
            curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $header);
            curl_setopt($soap_do, CURLOPT_SSLCERT, UBICACION_CERTIFICADOS_PDI.$pdi->certificado_ssl);
            curl_setopt($soap_do, CURLOPT_SSLCERTPASSWD, $pdi->clave_ssl);
            curl_setopt($soap_do, CURLOPT_VERBOSE, 	      true);

            $soap_response = curl_exec($soap_do);
            $curl_errno = curl_errno($soap_do);
            $curl_error = curl_error($soap_do);
            $http_code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
            curl_close($soap_do);

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
              return;
            }

            try {
              // Cuerpo del SOAP
              $regla = new Regla($this->extra->soap_body);
              $soap_body = $regla->getExpresionParaOutput($etapa->id);

              // Action
              $regla = new Regla($this->extra->soap_operacion_operacion);
              $soap_operacion = $regla->getExpresionParaOutput($etapa->id);

              preg_match_all("/(?<=@@).*?(?=<\/[a-zA-Z:]*>)/", $soap_body, $soap_campos);
              $campos_encontrados = array();

              foreach($etapa->getPasoEjecutable($secuencia)->Formulario->Campos as $campo) {
                  if(in_array($campo->nombre, $soap_campos[0])) {
                      $campo_dato = Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($campo->nombre, $etapa->id);
                      // $soap_body = str_replace('%'.$campo->nombre.'%', $campo_dato->valor, $soap_body);
                      array_push($campos_encontrados, $campo->nombre .':'.$campo_dato->valor);
                  }
              }
            }
            catch (Exception $e) {
              log_message('error', $e->getMessage());
              show_error($e->getMessage(), $status_code = 500);
            }

            try {
              $xml = simplexml_load_string($soap_response);
              $samlassertion = $xml->children('soapenv', true)->Body->children('wst', true)->RequestSecurityTokenResponse->RequestedSecurityToken->children('saml', true)->Assertion->asXML();
              $service_soap_init = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><SOAP-ENV:Header xmlns:wsa="http://www.w3.org/2005/08/addressing" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"><wsse:Security xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" SOAP-ENV:mustUnderstand="1">';
              $service_soap_end ='</wsse:Security><wsa:To>'. $servicio->url_logica .'</wsa:To><wsa:Action>'. $soap_operacion .'</wsa:Action><wsa:MessageID>urn:uuid:2411e349-0820-4a70-a638-a04909ff9e4d</wsa:MessageID><wsa:ReplyTo>http://www.w3.org/2005/08/addressing/anonymous</wsa:ReplyTo></SOAP-ENV:Header>'. $soap_body .'</soap:Envelope>';
              $service_soap = $service_soap_init . $samlassertion . $service_soap_end;
            }
            catch (Exception $e) {
              log_message('error', $e->getMessage());
              show_error($e->getMessage(), $status_code = 500);
            }

            // Invoca al servicio
            $header = array("Content-type: text/xml; charset=UTF-8",
                            "Accept: */*",
                            "Cache-Control: no-cache",
                            "Pragma: no-cache",
                            "SOAPAction: ". $servicio->url_logica,
            		"Transfer-Encoding: chunked",
            		"Connection: keep-alive",
            		"Expect: "
            );

            $soap_do = curl_init();
            curl_setopt($soap_do, CURLOPT_URL, $servicio->url_fisica);
            curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, $servicio->conexion_timeout);
            curl_setopt($soap_do, CURLOPT_TIMEOUT,        $servicio->conexion_timeout);
            curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($soap_do, CURLOPT_POST,           true);
            curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $service_soap);
            curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $header);
            curl_setopt($soap_do, CURLOPT_SSLCERT, UBICACION_CERTIFICADOS_PDI.$pdi->certificado_ssl);
            curl_setopt($soap_do, CURLOPT_SSLCERTPASSWD, $pdi->clave_ssl);
            curl_setopt($soap_do, CURLOPT_VERBOSE, 	      true);

            $soap_response = curl_exec($soap_do);
            $curl_errno = curl_errno($soap_do);
            $curl_error = curl_error($soap_do);
            $http_code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
            curl_close($soap_do);
          }
          catch (Exception $e) {
            log_message('error', $e->getMessage());
          }
        }

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
          return;
        }
        else {
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('ws_error', $etapa->id);
            if($dato)
              $dato->delete();

            $xml = new SimpleXMLElement($soap_response);
            $respuestas = json_decode($operacion->respuestas);

            if($xml->xpath("//*[local-name() = 'faultcode']")) {
              $error_servicio = $xml->xpath("//*[local-name() = 'faultstring']/text()");
              $dato = new DatoSeguimiento();
              $dato->nombre = 'ws_error';
              $dato->valor = $error_servicio;
              $dato->etapa_id = $etapa->id;
              $dato->save();

              return false;
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
    }
}

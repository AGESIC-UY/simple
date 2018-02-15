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


        $regla = new Regla($this->extra->soap_operacion_operacion);
        $soap_operacion = $regla->getExpresionParaOutput($etapa->id);

        //previo a la invocacion se limpian las variables
        $respuestas = json_decode($operacion->respuestas);
        foreach($respuestas->respuestas as $respuesta) {
            $clave = $respuesta->key;
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($clave, $etapa->id);
            if($dato)
              $dato->delete();
        }


        if($servicio->tipo == 'soap') {

          /*************************** SOAP******************************
          **************************************************************
          **/



          // WSDL
          $soap_wsdl = $servicio->wsdl;

          // Endpoint loctaion
          $soap_endpoint_location = $servicio->endpoint_location;

          try {
            //preg_match_all("/(?<=@@).*?(?=<\/[a-zA-Z0-9:]*>)/", $this->extra->soap_body, $soap_campos);
            preg_match_all("/(?<=@@).*?(?=<\/[a-zA-Z0-9:_\-]*>)/", $this->extra->soap_body, $soap_campos);

            $soap_body_new = html_entity_decode($this->extra->soap_body);
            foreach($soap_campos[0] as $campo) {
              if(strpos($campo, '[contenido]')) {
                $regla = new Regla("@@".str_replace('[contenido]', '', $campo));
                $file_name = $regla->getExpresionParaOutput($etapa->id);

                $file=Doctrine_Query::create()
                  ->from('File f, f.Tramite t')
                  ->where('f.filename = ? AND t.id = ?', array($file_name, $etapa->Tramite->id))
                  ->fetchOne();
                if($file) {
                  $folder = $file->tipo=='dato' ? 'datos' : 'documentos';
                  if(file_exists('uploads/'.$folder.'/'.$file->filename)) {
                    $str = '<![CDATA['.base64_encode(file_get_contents('uploads/'.$folder.'/'.$file->filename)).']]>';
                    $soap_body_new = str_replace("@@".$campo, $str, $soap_body_new);
                  }
                }
              }
            }

            $regla = new Regla($soap_body_new);
            $soap_body = $regla->getExpresionParaOutput($etapa->id);
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
                  "Content-length: ".strlen($soap_body),
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
                curl_setopt($soap_do, CURLOPT_POSTFIELDS, $soap_body);
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

                curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $soap_body);
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

              $xml = null;
              try{
                if ($soap_response && !empty($soap_response)){
                  $xml = new SimpleXMLElement($soap_response);
                }
              }catch(Exception $e){
                $xml = null;
              }

              $hubo_error = $this->procesar_errores_soap($etapa, $curl_errno,$curl_error,$http_code, $soap_body,$soap_response,$xml,$soap_endpoint_location,$servicio);
              //si hubo error se corta la ejecución
              if ($hubo_error){
                return true;
              }

          }
          catch (Exception $e) {
            log_message('error', "Web service SOAP Body Exception:" .$e->getMessage() . ' body:' .  $soap_body . ' - response:' .$soap_response .' httpcode:' . $http_code . ' curlerrno:' . $curl_errno . ' curlerror:' . $curl_error);
            return true;
          }
        }
        else {
          /*************************** PDI ******************************
          **************************************************************
          **/

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

            $assertion = '<saml:Assertion xmlns:saml="urn:oasis:names:tc:SAML:1.0:assertion" xmlns="urn:oasis:names:tc:SAML:1.0:assertion" IssueInstant="'. date("Y-m-d\TH:i:s.000") .'Z" Issuer="Agesic" MajorVersion="1" MinorVersion="1" AssertionID="_'. mt_rand().mt_rand() .'"><saml:Conditions NotBefore="'. $past_date .'Z" NotOnOrAfter="'. $future_date .'Z"/><saml:AuthenticationStatement AuthenticationInstant="'. date("Y-m-d\TH:i:s.000") .'Z" AuthenticationMethod="urn:oasis:names:tc:SAML:1.0:am:password"><saml:Subject><saml:NameIdentifier Format="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress">'. $servicio->rol .'</saml:NameIdentifier><saml:SubjectConfirmation><saml:ConfirmationMethod>urn:oasis:names:tc:SAML:1.0:cm:bearer</saml:ConfirmationMethod></saml:SubjectConfirmation></saml:Subject></saml:AuthenticationStatement><saml:AttributeStatement><saml:Subject><saml:NameIdentifier Format="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress">'. $servicio->rol .'</saml:NameIdentifier><saml:SubjectConfirmation><saml:ConfirmationMethod>urn:oasis:names:tc:SAML:1.0:cm:bearer</saml:ConfirmationMethod></saml:SubjectConfirmation></saml:Subject><saml:Attribute AttributeName="User" AttributeNamespace="urn:tokensimple"><saml:AttributeValue xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:type="xs:string" /></saml:Attribute></saml:AttributeStatement></saml:Assertion>';

            $assertion_base64 = base64_encode($assertion);
            $raw_signed_assertion = exec("java -jar ". JAR_FIRMA ." firma ". $pdi->certificado_organismo ." ". $pdi->clave_organismo ." $assertion_base64 2>&1");

            preg_match('/^ERR:/', $raw_signed_assertion, $match);

            if(!empty($match)) {
              echo str_replace('ERR: ', '', $raw_signed_assertion) . "\n";
              return true;
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
            //INVOCA AL STS
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
            //curl_setopt($soap_do, CURLOPT_STDERR, fopen('php://stderr', 'w'));

            $soap_response = curl_exec($soap_do);
            $curl_errno = curl_errno($soap_do);
            $curl_error = curl_error($soap_do);
            $http_code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);

            curl_close($soap_do);

            //procesar los errores del sts
            $xml = null;
            try{
              if ($soap_response && !empty($soap_response)){
                $xml = new SimpleXMLElement($soap_response);
              }
            }catch(Exception $e){
              $xml = null;
            }


            $hubo_error = $this->procesar_errores_pdi($etapa, $curl_errno,$curl_error,$http_code, $body,$soap_response,$xml,$servicio,$pdi);
            if ($hubo_error){
              return true;
            }


            try {

              preg_match_all("/(?<=@@).*?(?=<\/[a-zA-Z0-9:_\-]*>)/", $this->extra->soap_body, $soap_campos);

              $soap_body_new = $this->extra->soap_body;

              foreach($soap_campos[0] as $campo) {
                if(strpos($campo, '[contenido]')) {
                  $regla = new Regla("@@".str_replace('[contenido]', '', $campo));
                  $file_name = $regla->getExpresionParaOutput($etapa->id);

                  $file=Doctrine_Query::create()
                    ->from('File f, f.Tramite t')
                    ->where('f.filename = ? AND t.id = ?',array($file_name, $etapa->Tramite->id))
                    ->fetchOne();
                  if($file) {
                    $folder = $file->tipo=='dato' ? 'datos' : 'documentos';
                    if(file_exists('uploads/'.$folder.'/'.$file->filename)) {
                      $str = '<![CDATA['.base64_encode(file_get_contents('uploads/'.$folder.'/'.$file->filename)).']]>';
                      $soap_body_new = str_replace("@@".$campo, $str, $soap_body_new);
                    }
                  }
                }
              }

              $regla = new Regla($soap_body_new);
              $soap_body = $regla->getExpresionParaOutput($etapa->id);
            }
            catch (Exception $e) {
              log_message('error', $e->getMessage());
              show_error($e->getMessage(), $status_code = 500);
            }

            try {
              $message_id = $this->generate_uuid();

              $xml = simplexml_load_string($soap_response);
              $samlassertion = $xml->children('soapenv', true)->Body->children('wst', true)->RequestSecurityTokenResponse->RequestedSecurityToken->children('saml', true)->Assertion->asXML();
              $service_soap_init = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><SOAP-ENV:Header xmlns:wsa="http://www.w3.org/2005/08/addressing" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"><wsse:Security xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" SOAP-ENV:mustUnderstand="1">';
              $service_soap_end ='</wsse:Security><wsa:To>'. $servicio->url_logica .'</wsa:To><wsa:Action>'.$soap_operacion.'</wsa:Action><wsa:MessageID>'. $message_id .'</wsa:MessageID><wsa:ReplyTo>http://www.w3.org/2005/08/addressing/anonymous</wsa:ReplyTo></SOAP-ENV:Header>'. $soap_body .'</soap:Envelope>';
              $service_soap = $service_soap_init . $samlassertion . $service_soap_end;
            }
            catch (Exception $e) {
              log_message('error', $e->getMessage());
              show_error($e->getMessage(), $status_code = 500);
            }

            // Invoca al servicio de pdi YA INVOCO AL sts
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

            $xml = null;
            try{
              if ($soap_response && !empty($soap_response)){
                $xml = new SimpleXMLElement($soap_response);
              }
            }catch(Exception $e){
              $xml = null;
            }

            $hubo_error = $this->procesar_errores_pdi($etapa, $curl_errno,$curl_error,$http_code, $service_soap,$soap_response,$xml,$servicio,$pdi);
            if ($hubo_error){
              return true;
            }
          }
          catch (Exception $e) {
            log_message('error', $e->getMessage());
            return true;
          }
        }

        //tanto para pdi como soap procesa las respuestas en caso de no error
        //tanto PDI como SOAP
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('ws_error', $etapa->id);
        if($dato)
          $dato->delete();

        $xml = new SimpleXMLElement($soap_response);
        $respuestas = json_decode($operacion->respuestas);
        //Procesa las respuestas
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
              $xsldoc = DOMDocument::loadXML($xslt); // FIXME Aca esta el problema, no carga el XSLT

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

        if($servicio->tipo == 'soap'){
          $this->guardar_monitoreo(
                                    $etapa->Tramite->Proceso->id,
                                    $soap_endpoint_location,
                                    $servicio->tipo,
                                    '',//rol no se usa por ser soap
                                    '',//certificado no se usa por ser soap
                                    'OK',
                                    false,
                                    $soap_body,
                                    $soap_response,
                                    $servicio->id,
                                    $servicio->requiere_autenticacion
                                  );
        }
        else {
          //Parametros: ($proceso_id, $url_web_service, $tipo, $rol, $certificado, $error_texto, $error, $soap_peticion, $soap_respuesta, $catalogo_id,$seguridad)
          $this->guardar_monitoreo(
                                    $etapa->Tramite->Proceso->id,
                                    $servicio->url_fisica,
                                    $servicio->tipo,
                                    $servicio->rol,
                                    $pdi->certificado_ssl,
                                    'OK',
                                    false,
                                    $service_soap,
                                    $soap_response,
                                    $servicio->id,
                                    false //seguridad no se usa por ser pdi
                                  );
              }
    }

    public function generate_uuid() {
      return sprintf('urn:uuid:%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
      );
    }

    private function procesar_errores_pdi($etapa, $curl_errno,$curl_error,$http_code, $body,$soap_response,$xml,$servicio,$pdi){

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

        if($http_code != 0){
          $http_code_texto = ' (httpcode:'.$http_code.')';
        }
        else {
          $http_code_texto = '';
        }

        //Parametros: ($proceso_id, $url_web_service, $tipo, $rol, $certificado, $error_texto, $error, $soap_peticion, $soap_respuesta, $catalogo_id,$seguridad)
        $this->guardar_monitoreo(
                                  $etapa->Tramite->Proceso->id,
                                  $pdi->sts,
                                  $servicio->tipo,
                                  $servicio->rol,
                                  $pdi->certificado_ssl,
                                  $curl_error.$http_code_texto,
                                  true,
                                  $body,
                                  $soap_response,
                                  $servicio->id,
                                  false //seguridad no se usa por ser pdi
                                );

        return true;
      }
      else {
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('ws_error', $etapa->id);
        if($dato)
          $dato->delete();



        if($xml->xpath("//*[local-name() = 'faultcode']")) {
          $error_servicio = $xml->xpath("//*[local-name() = 'faultstring']/text()");
          $dato = new DatoSeguimiento();
          $dato->nombre = 'ws_error';

          if ((string)$error_servicio[0] && (string)$error_servicio[0] == 'Internal Error'){
            $dato->valor = "Se ha producido un error, por favor inténtelo nuevamente más tarde";
          }else{
            $dato->valor = (string)$error_servicio[0];
          }

          $dato->etapa_id = $etapa->id;
          $dato->save();

          $this->guardar_monitoreo(
                                    $etapa->Tramite->Proceso->id,
                                    $pdi->sts,
                                    $servicio->tipo,
                                    $servicio->rol,
                                    $pdi->certificado_ssl,
                                    $error_servicio[0],
                                    true,
                                    $body,
                                    $soap_response,
                                    $servicio->id,
                                    false //seguridad no se usa por ser pdi
                                  );

          return true;
        }
      }
    }

    private function procesar_errores_soap($etapa, $curl_errno,$curl_error,$http_code, $soap_body,$soap_response,$xml,$soap_endpoint_location,$servicio){

      $hubo_error = false;

      //si tiene un fault code se genera
      if(isset($xml) && $xml->xpath("//*[local-name() = 'faultcode']")) {
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('ws_error', $etapa->id);
        if($dato)
          $dato->delete();

        $dato = new DatoSeguimiento();
        $dato->nombre = 'ws_error';

        $error_servicio = $xml->xpath("//*[local-name() = 'faultstring']/text()");
        if ((string)$error_servicio[0] && (string)$error_servicio[0] == 'Internal Error'){
          $dato->valor = "Se ha producido un error, por favor inténtelo nuevamente más tarde";
        }else{
          $dato->valor = (string)$error_servicio[0];
        }


        $dato->etapa_id = $etapa->id;
        $dato->save();

        $hubo_error = true;


      }else if($curl_errno > 0 || $http_code != '200') {
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

        log_message('error', "Web service SOAP Body:" . $soap_body . ' - response:' .$soap_response .' httpcode:' . $http_code . ' curlerrno:' . $curl_errno . ' curlerror:' . $curl_error);

        $hubo_error = true;
      }

      if ($hubo_error){
        if($http_code != 0){
          $http_code_texto = ' (httpcode:'.$http_code.')';
        }
        else {
          $http_code_texto = '';
        }
        $this->guardar_monitoreo(
                                  $etapa->Tramite->Proceso->id,
                                  $soap_endpoint_location,
                                  $servicio->tipo,
                                  '',//rol no se usa por ser soap
                                  '',//certificado no se usa por ser soap
                                  $dato->valor.$http_code_texto,
                                  true,
                                  $soap_body,
                                  $soap_response,
                                  $servicio->id,
                                  $servicio->requiere_autenticacion
                                );
      }

      return $hubo_error;

    }

    private function guardar_monitoreo($proceso_id, $url_web_service, $tipo, $rol, $certificado, $error_texto, $error, $soap_peticion, $soap_respuesta, $catalogo_id, $seguridad){

      $lista_ejecuciones = Monitoreo::getListaOrdenadaId();

      if(count($lista_ejecuciones) >= 10){
        $lista_ejecuciones[9]->delete();
      }

      $monitoreo = new Monitoreo();
      $monitoreo->proceso_id = $proceso_id;
      $monitoreo->url_web_service = $url_web_service;
      $monitoreo->fecha = date('Y-m-d', time()).' '.date('H:i:s', time());
      $monitoreo->tipo = $tipo;
      $monitoreo->rol = $rol;
      $monitoreo->certificado = $certificado;
      $monitoreo->error_texto =$error_texto;
      $monitoreo->error = $error;
      $monitoreo->soap_peticion = $soap_peticion;
      $monitoreo->soap_respuesta = $soap_respuesta;
      $monitoreo->catalogo_id = $catalogo_id;
      $monitoreo->seguridad = $seguridad;
      $monitoreo->save();
    }
}

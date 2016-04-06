<?php

require_once('accion.php');

class AccionWebserviceExtended extends Accion {

    public function displayForm($operacion_id=null) {
        if (($this->extra) && (!$operacion_id)) {
            $operacion_id = $this->extra->soap_operacion;
        }

        if(isset($operacion_id)) {
            $operacion = Doctrine::getTable('WsOperacion')->find($operacion_id);
            $servicio = Doctrine::getTable('WsCatalogo')->find($operacion->catalogo_id);
        }

        $soap_nombre  = ($this->extra ? $this->extra->soap_nombre : $operacion->nombre);
        $soap_xml  = ($this->extra ? $this->extra->soap_body : $operacion->soap);
        $soap_help  = ($this->extra ? $this->extra->soap_help : $operacion->ayuda);

        $display  = '<p class="strong">Esta acción consultará al servicio via SOAP.</p>';
        $display .= '<div class="form-horizontal">';
        $display .= '<div class="control-group">';
        $display .= '<label for="operacion" class="control-label">Nombre de operación</label>';
        $display .= '<div class="controls">';
        $display .= '<input class="input-xxlarge" name="extra[soap_nombre]" type="text" readonly value="' . $soap_nombre . '" />';
        $display .= '</div>';
        $display .= '</div>';
        $display .= '<input class="hidden" type="hidden" name="extra[soap_operacion]" value="' . $operacion->codigo . '" />';
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

        // WSDL
        $soap_wsdl = $servicio->wsdl;

        // Endpoint loctaion
        $soap_endpoint_location = $servicio->endpoint_location;

        // Cuerpo del SOAP
        $regla = new Regla($this->extra->soap_body);
        $soap_body = $regla->getExpresionParaOutput($etapa->id);

        try {
            // preg_match_all("/(?<=%).*?(?=%)/", $soap_body, $soap_campos);
            preg_match_all("/(?<=@@).*?(?=<\/[a-zA-Z:]*>)/", $soap_body, $soap_campos);
            $campos_encontrados = array();

            foreach($etapa->getPasoEjecutable($secuencia)->Formulario->Campos as $campo) {
                if(in_array($campo->nombre, $soap_campos[0])) {
                    $campo_dato = Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($campo->nombre, $etapa->id);
                    $soap_body = str_replace('%'.$campo->nombre.'%', $campo_dato->valor, $soap_body);
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
            }
            else {
                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('ws_error', $etapa->id);
                if($dato)
                  $dato->delete();

                $xml = new SimpleXMLElement($soap_response);
                $respuestas = json_decode($operacion->respuestas);

                if($xml->xpath("//*[local-name() = 'faultcode']")) {
                  $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('ws_error', $etapa->id);
                  if($dato)
                    $dato->delete();

                  $dato = new DatoSeguimiento();
                  $dato->nombre = 'ws_error';
                  $dato->valor = "Hubo un error al procesar su solicitud. Por favor, vuelva a intentarlo más tarde.";
                  $dato->etapa_id = $etapa->id;
                  $dato->save();

                  log_message('error', $curl_error);
                }

                foreach($respuestas->respuestas as $respuesta) {
                    $clave = $respuesta->key;
                    $xpath = $respuesta->xpath;

                    $result = $xml->xpath($xpath);

                    // -- Aplica el XSLT al XML obtenido como respuesta SOLO SI la respuesta es de tipo LISTA.
                    if($respuesta->tipo == 'lista') {
                        $operacion_respuesta = Doctrine_Query::create()->from('WsOperacionRespuesta')->where('respuesta_id = ?', $respuesta->id)->limit(1)->execute();

                        // -- FIXME Al tomar el xslt dinamicamente se rompe, hay problemas de caracteres con el xslt guardado...
                        $xslt = $operacion_respuesta[0]->xslt;

                        // -- Comienza a procesar el XSLT
                        $xmldoc = DOMDocument::loadXML($result[0]->saveXML());
                        $xsldoc = DOMDocument::loadXML($xslt);

                        $proc = new XSLTProcessor();
                        $proc->importStyleSheet($xsldoc);

                        $xmlobj = $proc->transformToDoc($xmldoc);

                        $jsonString = '[[""],';

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
                        try{
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
        catch (Exception $e) {
          log_message('error', $e->getMessage());
        }
    }
}

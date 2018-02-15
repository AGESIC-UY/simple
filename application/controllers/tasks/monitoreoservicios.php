<?php

class MonitoreoServicios extends CI_Controller {

  public function __construct() {
    parent::__construct();

    if(!$this->input->is_cli_request()) {
      redirect(site_url());
    }
  }

  public function verificar() {
    $mensaje = "";
    echo 'Verificando estado de trazabilidad ...'.PHP_EOL;
    $mensaje .= $this->trazabilidad();
    echo 'Verificando estado de servicios soap y pdi ...'.PHP_EOL;
    $mensaje .= $this->soap_pdi();
    echo 'Verificando estado de pasarelas de pagos ...'.PHP_EOL;
    $mensaje .= $this->pasarelas_pagos();
    echo 'Verificacion finalizada con exito'.PHP_EOL;

    if($mensaje){
        $this->enviar_correo($mensaje);
        echo 'Se encontraron errores en los servicos: '.$mensaje.PHP_EOL;
    }
    else{
      echo 'No se encontraron errores en los servicios'.PHP_EOL;
    }
  }

  public function trazabilidad() {
    $monitoreo_cabezal = $this->trazabilidad_ping_cabezal_monitoreo();
    $monitore_linea = $this->trazabilidad_ping_linea_monitoreo();

    $mensaje_trazabilidad = "";

    if($monitoreo_cabezal->error){
      $mensaje_trazabilidad .= "<p>Error en monitoreo trazabilidad de cabezal</p> <br>";
    }

    if($monitore_linea->error){
      $mensaje_trazabilidad .= "<p>Error en monitoreo trazabilidad de linea</p> <br>";
    }

    return $mensaje_trazabilidad;
  }

  public function soap_pdi() {
    $lista_pdi= Doctrine_Query::create()
                    ->from('monitoreo m')
                    ->where('m.tipo = ?', 'pdi')
                    ->orderBy("m.id DESC")
                    ->limit(1)
                    ->execute();

    $lista_soap =Doctrine_Query::create()
                    ->from('monitoreo m')
                    ->where('m.tipo = ?', 'soap')
                    ->orderBy("m.id DESC")
                    ->limit(1)
                    ->execute();

    $error_pdi = false;
    $error_soap = false;
    $mensaje_soap_pdi = "";

    foreach ($lista_pdi as $pdi) {
      if($pdi->error){
        $error_pdi = true;
        $mensaje_pdi = $pdi->fecha;
        break;
      }
    }

    foreach ($lista_soap as $soap) {
      if($soap->error){
        $error_soap = true;
        $mensaje_soap = $soap->fecha;
        break;
      }
    }

    if($error_pdi){
      $mensaje_soap_pdi .= "<p>Errores en servicios PDI ejecutado en la fecha: ". $mensaje_pdi."</p><br>";
    }

    if($error_soap){
      $mensaje_soap_pdi .= "<p>Errores en servicios SOAP ejecutado en la fecha: ". $mensaje_soap."</p><br>";
    }

    return $mensaje_soap_pdi;
  }

  public function pasarelas_pagos() {
    $pasarelas = $this->pasarelas_pagos_consulta_monitoreo();
    $mensaje_pasarelas_pagos = "";
    foreach ($pasarelas as $pasarela) {
      if($pasarela->error_monitoreo){
        $mensaje_pasarelas_pagos .= "<p>Error en pasarela de pagos: ".$pasarela->pasarela_nombre."</p><br>";
      }
    }

    return $mensaje_pasarelas_pagos;
  }

  private function enviar_correo($mensaje){
    $monitoreo_notificaciones = Doctrine_Query::create()
        ->from('MonitoreoNotificaciones')
        ->limit(1)
        ->fetchOne();

    if(!empty($monitoreo_notificaciones)){
      $this->load->library('email');

      $this->email->from($monitoreo_notificaciones->email, 'Simple');
      $this->email->to($monitoreo_notificaciones->email);
      $this->email->subject('Simple - Error Monitoreo');
      $this->email->message($mensaje);
      $this->email->send();
    }
  }

  private function trazabilidad_ping_cabezal_monitoreo(){
    try {
      $soap_body_ping_cabezal = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.web.bruto.itramites.agesic.gub.uy/">
                             <soapenv:Header/>
                             <soapenv:Body>
                                <ws:ping/>
                             </soapenv:Body>
                          </soapenv:Envelope>';

      $soap_header_ping_cabezal = array(
        "Content-type: text/xml;charset=\"utf-8\"",
        "Accept: text/xml",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
        "Content-length: ". strlen($soap_body_ping_cabezal)
      );

      $soap_do = curl_init();
      curl_setopt($soap_do, CURLOPT_URL, WS_AGESIC_TRAZABLIDAD_CABEZAL);
      curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
      curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
      curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($soap_do, CURLOPT_POST,           true);
      curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $soap_body_ping_cabezal);
      curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $soap_header_ping_cabezal);
      $soap_response_ping_cabezal = curl_exec($soap_do);
      $curl_errno = curl_errno($soap_do);
      $curl_error = curl_error($soap_do);
      $http_code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
      curl_close($soap_do);

      $xml = new SimpleXMLElement($soap_response_ping_cabezal);

      $monitoreo_return = new stdClass();

      if ($curl_errno > 0 || $http_code != 200){

        if($http_code != 0){
          $http_code = ' (httpcode:'.$http_code.')';
        }
        else {
          $http_code = '';
        }

        $monitoreo_return->error = true;
        $monitoreo_return->mensaje = $curl_error.$http_code;
        $monitoreo_return->ws_body = $soap_body_ping_cabezal;
        $monitoreo_return->ws_response = $soap_response_ping_cabezal;

        return $monitoreo_return;
      }
      else if($xml->xpath("//*[local-name() = 'faultcode']")) {
        $error_servicio = $xml->xpath("//*[local-name() = 'faultstring']/text()");
        $monitoreo_return->error = true;
        $monitoreo_return->mensaje = (string)$error_servicio[0];
        $monitoreo_return->ws_body = $soap_body_ping_cabezal;
        $monitoreo_return->ws_response = $soap_response_ping_cabezal;

        return $monitoreo_return;
      }
      else if($curl_errno == 0 &&  $xml->xpath("//*[local-name() = 'return']/text()")[0] == 'OK'){
        $monitoreo_return->error = false;
        $monitoreo_return->mensaje = 'OK';
        $monitoreo_return->ws_body = $soap_body_ping_cabezal;
        $monitoreo_return->ws_response = $soap_response_ping_cabezal;

        return $monitoreo_return;
      }
      else{
        $monitoreo_return->error = true;
        $monitoreo_return->mensaje = 'Error desconocido.';
        $monitoreo_return->ws_body = $soap_body_ping_cabezal;
        $monitoreo_return->ws_response = $soap_response_ping_cabezal;

        return $monitoreo_return;
      }
    }
    catch(Exception $e) {
      $monitoreo_return->error = true;
      $monitoreo_return->mensaje = $e->getMessage();
      $monitoreo_return->ws_body = $soap_body_ping_cabezal;
      $monitoreo_return->ws_response = $soap_response_ping_cabezal;

      return $monitoreo_return;
    }
  }

  private function trazabilidad_ping_linea_monitoreo(){
    try {
      $soap_body_ping_linea = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:lin="http://ws.web.bruto.itramites.agesic.gub.uy/lineaService">
                                 <soapenv:Header/>
                                 <soapenv:Body>
                                    <lin:ping/>
                                 </soapenv:Body>
                              </soapenv:Envelope>';

      $soap_header_ping_linea = array(
        "Content-type: text/xml;charset=\"utf-8\"",
        "Accept: text/xml",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
        "Content-length: ". strlen($soap_body_ping_linea)
      );

      $soap_do = curl_init();
      curl_setopt($soap_do, CURLOPT_URL, WS_AGESIC_TRAZABLIDAD_LINEA);
      curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
      curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
      curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($soap_do, CURLOPT_POST,           true);
      curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $soap_body_ping_linea);
      curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $soap_header_ping_linea);
      $soap_response_ping_linea = curl_exec($soap_do);
      $curl_errno = curl_errno($soap_do);
      $curl_error = curl_error($soap_do);
      $http_code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
      curl_close($soap_do);

      $xml = new SimpleXMLElement($soap_response_ping_linea);

      $monitoreo_return = new stdClass();

      if ($curl_errno > 0 || $http_code != 200){

        if($http_code != 0){
          $http_code = ' (httpcode:'.$http_code.')';
        }
        else {
          $http_code = '';
        }

        $monitoreo_return->error = true;
        $monitoreo_return->mensaje = $curl_error.$http_code;
        $monitoreo_return->ws_body = $soap_body_ping_linea;
        $monitoreo_return->ws_response = $soap_response_ping_linea;

        return $monitoreo_return;
      }
      else if($xml->xpath("//*[local-name() = 'faultcode']")) {
        $error_servicio = $xml->xpath("//*[local-name() = 'faultstring']/text()");
        $monitoreo_return->error = true;
        $monitoreo_return->mensaje = (string)$error_servicio[0];
        $monitoreo_return->ws_body = $soap_body_ping_linea;
        $monitoreo_return->ws_response = $soap_response_ping_linea;

        return $monitoreo_return;
      }
      else if($curl_errno == 0 && $xml->xpath("//*[local-name() = 'return']/text()")[0] == 'OK'){
        $monitoreo_return->error = false;
        $monitoreo_return->mensaje = 'OK';
        $monitoreo_return->ws_body = $soap_body_ping_linea;
        $monitoreo_return->ws_response = $soap_response_ping_linea;

        return $monitoreo_return;
      }
      else{
        $monitoreo_return->error = true;
        $monitoreo_return->mensaje = 'Error desconcido';
        $monitoreo_return->ws_body = $soap_body_ping_linea;
        $monitoreo_return->ws_response = $soap_response_ping_linea;

        return $monitoreo_return;
      }
    }
    catch(Exception $e) {
      $monitoreo_return->error = true;
      $monitoreo_return->mensaje = $e->getMessage();
      $monitoreo_return->ws_body = $soap_body_ping_linea;
      $monitoreo_return->ws_response = $soap_response_ping_linea;

      return $monitoreo_return;
    }
  }

  private function pasarelas_pagos_consulta_monitoreo(){
    $pasarelas_pagos = Doctrine_Query::create()
        ->from('PasarelaPago p')
        ->where('p.activo = ?', 1)
        ->orderBy('p.metodo')
        ->execute();

      $pasaraleas_array_return = array();

      foreach ($pasarelas_pagos as $pasarela_pago) {

          if($pasarela_pago->metodo === 'antel'){


            $pasarela = $pasarela_pago->PasarelaPagoAntel;

            try{
              $ws_body = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:con="http://tempuri.org/wsorg/Consultas">';
              if (!empty(SOAP_PASARELA_PAGO_CONSULTA)){
                  if (SOAP_PASARELA_PAGO_CONSULTA == '1.1'){
                    $ws_body = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:con="http://tempuri.org/wsorg/Consultas">';
                  }
              }
              $ws_body = $ws_body . '<soap:Header/>
                 <soap:Body>
                    <con:ObtenerDatosTransaccion>
                       <con:pIdSolicitud>-1</con:pIdSolicitud>
                       <con:pIdTramite>-1</con:pIdTramite>
                       <con:pClave>-1</con:pClave>
                    </con:ObtenerDatosTransaccion>
                 </soap:Body>
              </soap:Envelope>';

              $ws_header = array(
                  "Content-type: text/xml;charset=\"utf-8\"",
                  "Accept: text/xml",
                  "Cache-Control: no-cache",
                  "Pragma: no-cache",
                  "Content-length: ".strlen($ws_body),
                  "SOAPAction: \"http://tempuri.org/wsorg/Consultas/ObtenerDatosTransaccion\""
              );


              $ws_do = curl_init();
              curl_setopt($ws_do, CURLOPT_URL, WS_PASARELA_PAGO_CONSULTA);
              curl_setopt($ws_do, CURLOPT_CONNECTTIMEOUT, PASARELA_PAGO_TIMEOUT_CONEXION);
              curl_setopt($ws_do, CURLOPT_TIMEOUT,        PASARELA_PAGO_TIMEOUT_RESPUESTA);
              curl_setopt($ws_do, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ws_do, CURLOPT_SSL_VERIFYPEER, false);
              curl_setopt($ws_do, CURLOPT_SSL_VERIFYHOST, false);
              curl_setopt($ws_do, CURLOPT_POST,           true);
              curl_setopt($ws_do, CURLOPT_SSLVERSION,     1);
              curl_setopt($ws_do, CURLOPT_SSLCERT, UBICACION_CERTIFICADOS_PASARELA.$pasarela->certificado);
              curl_setopt($ws_do, CURLOPT_SSLCERTPASSWD, $pasarela->pass_clave_certificado);
              curl_setopt($ws_do, CURLOPT_SSLKEY, UBICACION_CERTIFICADOS_PASARELA.$pasarela->clave_certificado);

              curl_setopt($ws_do, CURLOPT_ENCODING,       'gzip');
              curl_setopt($ws_do, CURLOPT_POSTFIELDS,     $ws_body);
              curl_setopt($ws_do, CURLOPT_HTTPHEADER,     $ws_header);

              if (!empty(PROXY_PASARELA_PAGO)){
                curl_setopt($ws_do, CURLOPT_PROXY, PROXY_PASARELA_PAGO);
              }

              $ws_response = curl_exec($ws_do);
              $curl_errno = curl_errno($ws_do); // -- Codigo de error
              $curl_error = curl_error($ws_do); // -- Descripcion del error
              $http_code = curl_getinfo($ws_do, CURLINFO_HTTP_CODE); // -- Codigo respuesta HTTP

              curl_close($ws_do);

              $xml = new SimpleXMLElement($ws_response);
              $respuesta = $xml->xpath("//*[local-name() = 'Mensaje']/text()");

              $pasarela_return = new stdClass();

              if($curl_errno > 0 || $http_code != 200) {
                $pasarela_return->id = $pasarela_pago->id;
                $pasarela_return->pasarela_nombre = $pasarela_pago->nombre;
                $pasarela_return->error_monitoreo = true;
                $pasarela_return->error_texto_monitoreo = $curlerror;
                $pasarela_return->ws_body_monitoreo = $ws_body;
                $pasarela_return->ws_response_monitoreo = $ws_response;
                $pasarela_return->certificado_ssl_monitoreo = $pasarela->certificado;
                $pasarela_return->url = WS_PASARELA_PAGO_CONSULTA;

                array_push($pasaraleas_array_return, $pasarela_return);
              }

              else if($curl_errno == 0 && $respuesta[0] == 'ERROR EN WEBSERVICE'){
                $pasarela_return->id = $pasarela_pago->id;
                $pasarela_return->pasarela_nombre = $pasarela_pago->nombre;
                $pasarela_return->error_monitoreo = false;
                $pasarela_return->error_texto_monitoreo = 'OK';
                $pasarela_return->ws_body_monitoreo = $ws_body;
                $pasarela_return->ws_response_monitoreo = $ws_response;
                $pasarela_return->certificado_ssl_monitoreo = $pasarela->certificado;
                $pasarela_return->url = WS_PASARELA_PAGO_CONSULTA;

                array_push($pasaraleas_array_return, $pasarela_return);
              }
              else{
                $pasarela_return->id = $pasarela_pago->id;
                $pasarela_return->pasarela_nombre = $pasarela_pago->nombre;
                $pasarela_return->error_monitoreo = true;
                $pasarela_return->error_texto_monitoreo = 'Error desconocido';
                $pasarela_return->ws_body_monitoreo = $ws_body;
                $pasarela_return->ws_response_monitoreo = $ws_response;
                $pasarela_return->certificado_ssl_monitoreo = $pasarela->certificado;
                $pasarela_return->url = WS_PASARELA_PAGO_CONSULTA;

                array_push($pasaraleas_array_return, $pasarela_return);
              }

            }
            catch(Exception $e) {
              $pasarela_return->id = $pasarela_pago->id;
              $pasarela_return->pasarela_nombre = $pasarela_pago->nombre;
              $pasarela_return->error_monitoreo = true;
              $pasarela_return->error_texto_monitoreo = $e->getMessage();
              $pasarela_return->ws_body_monitoreo = $ws_body;
              $pasarela_return->ws_response_monitoreo = $ws_response;
              $pasarela_return->certificado_ssl_monitoreo = $pasarela->certificado;
              $pasarela_return->url = WS_PASARELA_PAGO_CONSULTA;

              array_push($pasaraleas_array_return, $pasarela_return);
            }
          }

          if($pasarela_pago->metodo === 'generico'){
              $pasarela_generica = $pasarela_pago->PasarelaPagoGenerica;

              $variable_evaluar = $pasarela_generica->variable_evaluar;
              $variable_idsol = $pasarela_generica->variable_idsol;
              $variable_idestado = $pasarela_generica->variable_idestado;
              $codigo_operacion_soap = $pasarela_generica->codigo_operacion_soap_consulta;

              $operacion = Doctrine_Query::create()
                          ->from('WsOperacion o')
                          ->where('o.codigo = ?', $codigo_operacion_soap)
                          ->fetchOne();

              $servicio = Doctrine::getTable('WsCatalogo')->find($operacion->catalogo_id);

              try {
                  $soap_header = array(
                      "Content-type: text/xml;charset=\"utf-8\"",
                      "Accept: text/xml",
                      "Cache-Control: no-cache",
                      "Pragma: no-cache",
                      "Content-length: ".strlen($operacion->soap),
                      "SOAPAction: ".$operacion->operacion,
                      "User-Agent: Mozilla/5.0"
                  );

                  if ($servicio->requiere_autenticacion == 1) {
                    //servicio soap con autenticacion
                    switch($servicio->requiere_autenticacion_tipo) {
                      case 'autenticacion_basica':
                        $soap_do = curl_init();
                        curl_setopt($soap_do, CURLOPT_URL, $servicio->endpoint_location);
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
                        curl_setopt($soap_do, CURLOPT_URL, $servicio->endpoint_location);
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
                    curl_setopt($soap_do, CURLOPT_POSTFIELDS, $operacion->soap);
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
                    curl_setopt($soap_do, CURLOPT_URL, $servicio->endpoint_location);
                    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, $servicio->conexion_timeout);
                    curl_setopt($soap_do, CURLOPT_TIMEOUT,        $servicio->respuesta_timeout);
                    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($soap_do, CURLOPT_POST,           true);
                    curl_setopt($soap_do, CURLOPT_ENCODING,       'gzip');

                    curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $operacion->soap);
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

                  $pasarela_return = new stdClass();

                  if($curl_errno > 0 || $http_code != '200') {

                    if($http_code != 0){
                      $http_code = ' (httpcode:'.$http_code.')';
                    }
                    else {
                      $http_code = '';
                    }

                    $curl_error = $curl_error.$http_code;
                    $pasarela_return->id = $pasarela_pago->id;
                    $pasarela_return->pasarela_nombre = $pasarela_pago->nombre;
                    $pasarela_return->error_monitoreo = true;
                    $pasarela_return->error_texto_monitoreo = $curl_error;
                    $pasarela_return->ws_body_monitoreo = $operacion->soap;
                    $pasarela_return->ws_response_monitoreo = $soap_response;
                    $pasarela_return->certificado_ssl_monitoreo = '-';
                    $pasarela_return->url = $servicio->endpoint_location;

                    array_push($pasaraleas_array_return, $pasarela_return);
                  }

                  else{
                    $pasarela_return->id = $pasarela_pago->id;
                    $pasarela_return->pasarela_nombre = $pasarela_pago->nombre;
                    $pasarela_return->error_monitoreo = false;
                    $pasarela_return->error_texto_monitoreo = 'OK';
                    $pasarela_return->ws_body_monitoreo = $operacion->soap;
                    $pasarela_return->ws_response_monitoreo = $soap_response;
                    $pasarela_return->certificado_ssl_monitoreo = '-';
                    $pasarela_return->url = $servicio->endpoint_location;

                    array_push($pasaraleas_array_return, $pasarela_return);
                  }
              }
              catch (Exception $e) {
                $pasarela_return->id = $pasarela_pago->id;
                $pasarela_return->pasarela_nombre = $pasarela_pago->nombre;
                $pasarela_return->error_monitoreo = true;
                $pasarela_return->error_texto_monitoreo = $e->getMessage();
                $pasarela_return->ws_body_monitoreo = $operacion->soap;
                $pasarela_return->ws_response_monitoreo = $soap_response;
                $pasarela_return->certificado_ssl_monitoreo = '-';
                $pasarela_return->url = $servicio->endpoint_location;

                array_push($pasaraleas_array_return, $pasarela_return);

              }

          }

      }

      return $pasaraleas_array_return;
  }
}

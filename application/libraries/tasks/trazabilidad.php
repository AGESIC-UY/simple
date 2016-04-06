<?php

// -- Incluye constantes requeridas
$path = __DIR__;
$path_array = explode('/', $path);
$path_array = array_slice($path_array, 0, count($path_array)-1);
$path_array = array_slice($path_array, 0, count($path_array)-1);
$path = implode('/', $path_array);
include($path .'/config/constants.php');

class Trazabilidad {
  public function perform() {
    $tramite_id = $this->args['tramite_id'];
    $pasos_ejecutables = $this->args['pasos_ejecutables'];
    $secuencia = $this->args['secuencia'];
    $id_organismo = $this->args['organismo_id'];
    $proceso_externo_id = $this->args['proceso_externo_id'];
    $usuario_id = ($this->args['usuario_id'] ? $this->args['usuario_id'] : 0);

    if($usuario_id == 0)
      $usuario_id = 'NOUSUARIO.' . mt_rand(1000, 9999);

    $id_transaccion = str_replace(" ", "_", strtoupper($id_organismo)) . ':' . str_replace(" ", "_", strtoupper($proceso_externo_id)) . ':' . $tramite_id;

    // -- CABEZAL
  	$soap_body_cabezal = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.web.bruto.itramites.agesic.gub.uy/">
        <soapenv:Header/>
        <soapenv:Body>
          <ws:persist>
             <traza>
                <tipoProceso>TRAMITE</tipoProceso>
                <idProceso>'. $proceso_externo_id .'</idProceso>
                <idTransaccion>'. $id_transaccion .'</idTransaccion>
                <versionModelo>'. WS_VERSION_MODELO .'</versionModelo>
                <medioInicio>WEB</medioInicio>
                <canal>WEB</canal>
                <fechaOrganismo>'. date('Y-m-d', time()).'T'.date('H:i:s', time()) .'</fechaOrganismo>
             </traza>
          </ws:persist>
        </soapenv:Body>
      </soapenv:Envelope>';

  	$soap_header_cabezal = array(
			"Content-type: text/xml;charset=\"utf-8\"",
			"Accept: text/xml",
			"Cache-Control: no-cache",
			"Pragma: no-cache",
			"Content-length: ". strlen($soap_body_cabezal)
		);

    try {
      $soap_do = curl_init();
      curl_setopt($soap_do, CURLOPT_URL, WS_AGESIC_TRAZABLIDAD_CABEZAL);
      curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
      curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
      curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($soap_do, CURLOPT_POST,           true);
      curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $soap_body_cabezal);
      curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $soap_header_cabezal);
      $soap_response_cabezal = curl_exec($soap_do);
      $curl_errno = curl_errno($soap_do);
      curl_close($soap_do);

      if ($curl_errno > 0) {
        // -- FIXME Verificar si se accede correctamente a la clase ResqueScheduler
        // $CI =& get_instance();
        // $CI->load->library('resque/resque');
        // -- TODO ResqueScheduler::enqueueIn(TIEMPO_DELAY, 'default', 'Trazabilidad', $this->args);
      }
    }
    catch (Exception $e) {
      // log_message('error', $e->getMessage());
    }

    try {
      // -- Crea variable con cod de traza obtenido
      $xml = new SimpleXMLElement($soap_response_cabezal);
      $cod_traza = $xml->xpath(WS_XPATH_COD_TRAZABILIDAD);

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(WS_VARIABLE_COD_TRAZABILIDAD, $tramite_id);
      if (!$dato)
        $dato = new DatoSeguimiento();

      $dato->tramite_id = $tramite_id;
      $dato->nombre = WS_VARIABLE_COD_TRAZABILIDAD;
      $dato->valor = (string)$cod_traza[0];
      $dato->save();
    }
    catch (Exception $e) {
      // log_message('error', $e->getMessage());
    }

    // -- WS LINEA
    $soap_body_linea = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:lin="http://ws.web.bruto.itramites.agesic.gub.uy/lineaService">
        <soapenv:Header/>
        <soapenv:Body>
          <lin:persist>
             <traza>
                <idTransaccion>'. $id_transaccion .'</idTransaccion>
                <versionModelo>'. WS_VERSION_MODELO .'</versionModelo>
                <oficina>'. $id_organismo .'</oficina>
                <fechaOrganismo>'. date('c', time()) .'</fechaOrganismo>
                <paso>'. $secuencia .'</paso>
                <tipoTraza>COMUN</tipoTraza>
                <estadoLinea>'. ($secuencia == 0 ? 'INICIO' : ($secuencia == sizeof($pasos_ejecutables) - 1 ? 'FIN' : 'EN_PROCESO')) .'</estadoLinea>
             </traza>
          </lin:persist>
        </soapenv:Body>
      </soapenv:Envelope>';

    $soap_header_linea = array(
      "Content-type: text/xml;charset=\"utf-8\"",
      "Accept: text/xml",
      "Cache-Control: no-cache",
      "Pragma: no-cache",
      "Content-length: ". strlen($soap_body_linea)
    );

    try {
      $soap_do = curl_init();
      curl_setopt($soap_do, CURLOPT_URL, WS_AGESIC_TRAZABLIDAD_LINEA);
      curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
      curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
      curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($soap_do, CURLOPT_POST,           true);
      curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $soap_body_linea);
      curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $soap_header_linea);
      $soap_response_linea = curl_exec($soap_do);
      $curl_errno = curl_errno($soap_do);
      curl_close($soap_do);

      if ($curl_errno > 0) {
        // -- FIXME Verificar si se accede correctamente a la clase ResqueScheduler
        // $CI =& get_instance();
        // $CI->load->library('resque/resque');
        // -- TODO ResqueScheduler::enqueueIn(TIEMPO_DELAY, 'default', 'Trazabilidad', $this->args);
      }
    }
    catch(Exception $e) {
      // log_message('error', $e->getMessage());
    }
  }

  public function verificar_servicios() {
    $servicio_cabezal = WS_AGESIC_TRAZABLIDAD_CABEZAL;
    $servicio_linea = WS_AGESIC_TRAZABLIDAD_LINEA;

    $cabecera_cabezal = @get_headers($servicio_cabezal);
    $cabecera_linea = @get_headers($servicio_linea);

    if(($cabecera_cabezal[0] == 'HTTP/1.1 404 Not Found') || ($cabecera_linea[0] == 'HTTP/1.1 404 Not Found')) {
      return false;
    }
    else {
      return true;
    }
  }
}

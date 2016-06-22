 <?php

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
    $cabezal = $this->args['cabezal'];
    $nombre_tarea = $this->args['nombre_tarea'];

    $id_transaccion = str_replace(" ", "_", strtoupper($id_organismo)) . ':' . str_replace(" ", "_", strtoupper($proceso_externo_id)) . ':' . $tramite_id;

    if($cabezal == 1) {
      // -- CABEZAL
    	$soap_body_cabezal = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.web.bruto.itramites.agesic.gub.uy/">
          <soapenv:Header/>
          <soapenv:Body>
            <ws:persist>
               <traza>
                  <tipoProceso>'. WS_AGESIC_TIPO_PROCESO_TRAZABILIDAD .'</tipoProceso>
                  <idProceso>'. $proceso_externo_id .'</idProceso>
                  <idTransaccion>'. $id_transaccion .'</idTransaccion>
                  <edicionModelo>'. WS_VERSION_MODELO_TRAZABILIDAD .'</edicionModelo>
                  <cantidadPasosProceso>'. count($pasos_ejecutables) .'</cantidadPasosProceso>
                  <canalDeInicio>'. WS_CANAL_INICIO_TRAZABILIDAD .'</canalDeInicio>
                  <fechaHoraOrganismo>'. date('Y-m-d', time()).'T'.date('H:i:s', time()) .'.000Z</fechaHoraOrganismo>
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
          $log = fopen($path .'/logs/trazabilidad.log', "w");
          fwrite($log, 'CABEZAL GENERADO: '.$soap_body_cabezal);
          fwrite($log, 'ERROR: '.$curl_errno);
          fclose($log);

          // $CI =& get_instance();
          // $CI->load->library('resque/resque');
          // ResqueScheduler::enqueueIn(TIEMPO_DELAY, 'default', 'Trazabilidad', $this->args);
        }
      }
      catch (Exception $e) {
        $log = fopen($path .'/logs/trazabilidad.log', "w");
        fwrite($log, 'CABEZAL GENERADO: '.$soap_body_cabezal);
        fwrite($log, 'ERROR: '.$e);
        fclose($log);
      }

	/*
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
        $log = fopen($path .'/logs/trazabilidad.log', "w");
        fwrite($log, 'CABEZAL GENERADO: '.$soap_body_cabezal);
        fwrite($log, 'ERROR: '.$e);
        fclose($log);
      }*/
    }

    // -- WS LINEA
    $soap_body_linea = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:lin="http://ws.web.bruto.itramites.agesic.gub.uy/lineaService">
        <soapenv:Header/>
        <soapenv:Body>
          <lin:persist>
             <traza>
                <idTransaccion>'. $id_transaccion .'</idTransaccion>
                <edicionModelo>'. WS_VERSION_MODELO_TRAZABILIDAD .'</edicionModelo>
                <idOficina>'. $id_organismo .'</idOficina>
                <fechaHoraOrganismo>'. date('c', time()) .'</fechaHoraOrganismo>
                <paso>'. $secuencia .'</paso>
                <descripcionDelPaso>'. $nombre_tarea .'</descripcionDelPaso>
                <estadoProceso>'. ($secuencia == 0 ? '1' : '2') .'</estadoProceso>
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
        $log = fopen($path .'/logs/trazabilidad.log', "w");
        fwrite($log, 'LINEA GENERADA: '.$soap_body_linea);
        fwrite($log, 'ERROR: '.$curl_errno);
        fclose($log);

        // $CI =& get_instance();
        // $CI->load->library('resque/resque');
        // ResqueScheduler::enqueueIn(TIEMPO_DELAY, 'default', 'Trazabilidad', $this->args);
      }
    }
    catch(Exception $e) {
      $log = fopen($path .'/logs/trazabilidad.log', "w");
      fwrite($log, 'LINEA GENERADA: '.$soap_body_linea);
      fwrite($log, 'ERROR: '.$e);
      fclose($log);
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

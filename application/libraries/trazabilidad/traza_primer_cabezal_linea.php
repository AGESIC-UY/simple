<?php

$path = __DIR__;
$path_array = explode('/', $path);
$path_array = array_slice($path_array, 0, count($path_array)-1);
$path_array = array_slice($path_array, 0, count($path_array)-1);
$path = implode('/', $path_array);

include($path .'/config/constants.php');
define('TRAZA_PATH', $path);


class TrazaPrimerCabezalLinea{

  public function perform() {

      $cabezal_enviado = $this->args['cabezal_enviado'];
      $etapa_id = $this->args['etapa_id'];
      $tramite_id = $this->args['tramite_id'];
      $pasos_ejecutables = $this->args['pasos_ejecutables'];
      $secuencia = $this->args['secuencia'];
      $paso = $this->args['paso'];
      $id_organismo = $this->args['organismo_id'];
      $id_oficina = $this->args['oficina_id'];
      $proceso_externo_id = $this->args['proceso_externo_id'];
      $nombre_tarea = $this->args['nombre_tarea'];
      $nombre_paso = $this->args['nombre_paso'];
      $estado = $this->args['estado'];
      $canal_inicio = $this->args['canal_inicio'];
      $id_transaccion = $this->args['id_transaccion'];

      if(!$cabezal_enviado) {
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
                 <canalDeInicio>'. $canal_inicio .'</canalDeInicio>
                 <fechaHoraOrganismo>'. date('Y-m-d', time()).'T'.date('H:i:s', time()) .'</fechaHoraOrganismo>
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
        $curl_error = curl_error($soap_do);
        curl_close($soap_do);

        if ($curl_errno > 0) {
          $cont = 1;
          //intento enviarlo 5 veces mas cada 10 segundos
          while ($curl_errno > 0 && $cont <= 5) {
            sleep(10);
            $cont++;

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
            $curl_error = curl_error($soap_do);
            curl_close($soap_do);
          }
        }

        //si despues de todos los intentos no se logra enviar correcamten, se desencola
        if ($curl_errno > 0) {
          $log = fopen(TRAZA_PATH .'/logs/trazabilidad.log', "a");
          fwrite($log, 'CABEZAL GENERADO: '.$soap_body_cabezal);
          fwrite($log, 'ERROR: '.$curl_error);
          fclose($log);

          throw new Exception('No es posible enviar el cabezal.');
        }

        // -- Crea variable con cod de traza obtenido
        $xml = new SimpleXMLElement($soap_response_cabezal);
        $cod_traza = $xml->xpath(WS_XPATH_COD_TRAZABILIDAD);

        $str_database = file_get_contents(TRAZA_PATH .'/config/database.php');
        preg_match("/'hostname'] = '([a-zA-Z0-9._,;-@$+?%&#!=()*]*)'/", $str_database, $hostname);
        preg_match("/'username'] = '([a-zA-Z0-9._,;-@$+?%&#!=()*]*)'/", $str_database, $username);
        preg_match("/'password'] = '([a-zA-Z0-9._,;-@$+?%&#!=()*]*)'/", $str_database, $password);
        preg_match("/'database'] = '([a-zA-Z0-9._,;-@$+?%&#!=()*]*)'/", $str_database, $database);

        $conn = new mysqli($hostname[1], $username[1], $password[1], $database[1]);

        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }

        $guid = '"'.$cod_traza[0].'"';

        $sql = "insert into dato_seguimiento (etapa_id, nombre, valor) values ('".$etapa_id."', '".WS_VARIABLE_COD_TRAZABILIDAD."', '".$guid."')";

        if (!$conn->query($sql)) {
          throw new Exception('No es posible crear variable de GUID de traza.');
        }

        $conn->close();

      }

      // -- WS LINEA
      $soap_body_linea = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:lin="http://ws.web.bruto.itramites.agesic.gub.uy/lineaService">
       <soapenv:Header/>
       <soapenv:Body>
         <lin:persist>
            <traza>
               <idTransaccion>'. $id_transaccion .'</idTransaccion>
               <edicionModelo>'. WS_VERSION_MODELO_TRAZABILIDAD .'</edicionModelo>
               <idOficina>'. $id_oficina .'</idOficina>
               <oficina>'. $id_oficina .'</oficina>
               <fechaHoraOrganismo>'. date('c', time()) .'</fechaHoraOrganismo>
               <tipoRegistroTrazabilidad>3</tipoRegistroTrazabilidad>
               <paso>'. $paso .'</paso>
               <descripcionDelPaso>Inicio de: '. $nombre_tarea.'</descripcionDelPaso>
               <pasoDelProceso>'. $secuencia .'</pasoDelProceso>
               <estadoProceso>'. $estado .'</estadoProceso>
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
      $curl_error = curl_error($soap_do);
      curl_close($soap_do);

      if ($curl_errno > 0) {
        $cont = 1;
        //intento enviarlo 5 veces mas cada 10 segundos
        while ($curl_errno > 0 && $cont <= 5) {
          sleep(10);
          $cont++;

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
          $curl_error = curl_error($soap_do);
          curl_close($soap_do);
        }
      }

      //si despues de todos los intentos no se logra enviar correcamten, se desencola
      if ($curl_errno > 0) {
        $log = fopen(TRAZA_PATH .'/logs/trazabilidad.log', "a");
        fwrite($log, 'CABEZAL GENERADO: '.$soap_body_linea);
        fwrite($log, 'ERROR: '.$curl_error);
        fclose($log);

        throw new Exception('No es posible enviar la linea.');
      }

      return true;
    }
}

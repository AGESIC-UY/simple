<?php

$path = __DIR__;
$path_array = explode('/', $path);
$path_array = array_slice($path_array, 0, count($path_array) - 1);
$path_array = array_slice($path_array, 0, count($path_array) - 1);
$path = implode('/', $path_array);

include($path . '/config/constants.php');
define('TRAZA_PATH', $path);

class TrazaCabezal {

    public function perform() {

        $etapa = $this->args['etapa'];
        $tramite_id = $this->args['tramite_id'];
        $pasos_ejecutables = $this->args['pasos_ejecutables'];
        $id_organismo = $this->args['organismo_id'];
        $id_oficina = $this->args['oficina_id'];
        $proceso_externo_id = $this->args['proceso_externo_id'];
        $estado = $this->args['estado'];
        $canal_inicio = $this->args['canal_inicio'];
        $inicio_asistido = $this->args['inicio_asistido'];
        $nombre_oficina = $this->args['oficina_nombre'];
        $oid = $this->args['oid'];
        $role = $this->args['role'];
        $fechaOrganismo = $this->args['fechaOrganismo'];

        $soap_body_cabezal = '<soapenv:Envelope 
                xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                xmlns:ws="http://ws.web.captura.trazabilidad.agesic.gub.uy/api/v2/cabezalService">
               <soapenv:Header/>
                <soapenv:Body>
                        <ws:persist>
                           <traza>
                             <idTraza>
                                 <oidOrganismo>' . $id_organismo . '</oidOrganismo>
                                 <idProceso>' . $proceso_externo_id . '</idProceso>
                                 <idInstancia>' . $tramite_id . '</idInstancia>
                                 <tipoProceso>' . WS_AGESIC_TIPO_PROCESO_TRAZABILIDAD . '</tipoProceso>
                             </idTraza>';
        if ($id_oficina != '') {
            $soap_body_cabezal .='
                             <oidOficina>' . $id_oficina . '</oidOficina>';
        }
        $soap_body_cabezal .='
                             <oficina>' . $nombre_oficina . '</oficina>
                             <fechaHoraOrganismo>' . $fechaOrganismo . '</fechaHoraOrganismo>
                             <appOrigen>Simple ' . SIMPLE_VERSION . '</appOrigen>';
        if (!$oid) {
            $soap_body_cabezal .='
                             <involucrados></involucrados>';
        } else {
            $soap_body_cabezal .='
                             <involucrados>
                                <involucrado>
                                    <oid>' . $oid . '</oid>
                                    <role>' . $role . '</role>
                                </involucrado>
                             </involucrados>';
        }
        $soap_body_cabezal .='
                             <datosProceso xsi:type="ws:datosProcesoTramiteDTO">
                                 <datosExtra></datosExtra>
                                 <canalDeInicio>' . $canal_inicio . '</canalDeInicio>
                                 <inicioAsistidoProceso>' . $inicio_asistido . '</inicioAsistidoProceso>
                             </datosProceso>
                             <cantidadPasosProceso>' . $pasos_ejecutables . '</cantidadPasosProceso>
                           </traza>
                        </ws:persist>
                      </soapenv:Body>
                     </soapenv:Envelope>';

        $soap_header_cabezal = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Content-length: " . strlen($soap_body_cabezal)
        );

        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, WS_AGESIC_TRAZABLIDAD_CABEZAL);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $soap_body_cabezal);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER, $soap_header_cabezal);
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
                curl_setopt($soap_do, CURLOPT_TIMEOUT, 10);
                curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($soap_do, CURLOPT_POST, true);
                curl_setopt($soap_do, CURLOPT_POSTFIELDS, $soap_body_cabezal);
                curl_setopt($soap_do, CURLOPT_HTTPHEADER, $soap_header_cabezal);
                $soap_response_cabezal = curl_exec($soap_do);
                $curl_errno = curl_errno($soap_do);
                $curl_error = curl_error($soap_do);
                curl_close($soap_do);
            }
        }

        //si despues de todos los intentos no se logra enviar correcamten, se desencola

        if ($curl_errno > 0) {
            $log = fopen(TRAZA_PATH . '/logs/trazabilidad.log', "a+");
            fwrite($log, 'CABEZAL GENERADO: ' . $soap_body_cabezal);
            fwrite($log, PHP_EOL);
            fwrite($log, 'ERROR: ' . $curl_error);
            fwrite($log, PHP_EOL);
            fclose($log);

            throw new Exception('No es posible enviar el cabezal.');
        }

        // -- Crea variable con cod de traza obtenido
        $xml = new SimpleXMLElement($soap_response_cabezal);
        $cod_traza = $xml->xpath(WS_XPATH_COD_TRAZABILIDAD);
        $cod_estado = $xml->xpath(WS_XPATH_COD_ESTADO);

        $str_database = file_get_contents(TRAZA_PATH . '/config/database.php');
        preg_match("/'hostname'] = '([a-zA-Z0-9._,;-@$+?%&#!=()*]*)'/", $str_database, $hostname);
        preg_match("/'username'] = '([a-zA-Z0-9._,;-@$+?%&#!=()*]*)'/", $str_database, $username);
        preg_match("/'password'] = '([a-zA-Z0-9._,;-@$+?%&#!=()*]*)'/", $str_database, $password);
        preg_match("/'database'] = '([a-zA-Z0-9._,;-@$+?%&#!=()*]*)'/", $str_database, $database);

        $conn = new mysqli($hostname[1], $username[1], $password[1], $database[1]);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if (count($cod_estado) > 0) {
            if ($cod_estado[0] == "OK") {
                $guid = '"' . $cod_traza[0] . '"';
                $sql = "insert into dato_seguimiento (etapa_id, nombre, valor) values ('" . $etapa_id . "', '" . WS_VARIABLE_COD_TRAZABILIDAD . "', '" . $guid . "')";

                if (!$conn->query($sql)) {
                    throw new Exception('No es posible crear variable de GUID de traza.');
                }
            } else {
                $log = fopen(TRAZA_PATH . '/logs/trazabilidad.log', "a+");
                fwrite($log, 'CABEZAL GENERADO: ' . $soap_body_cabezal);
                fwrite($log, PHP_EOL);
                fwrite($log, 'RESPUESTA CABEZAL: ' . $soap_response_cabezal);
                fwrite($log, PHP_EOL);
                fclose($log);
                return false;
            }
            $conn->close();
        } else {
            $log = fopen(TRAZA_PATH . '/logs/trazabilidad.log', "a+");
            fwrite($log, 'CABEZAL GENERADO: ' . $soap_body_cabezal);
            fwrite($log, PHP_EOL);
            fwrite($log, 'RESPUESTA CABEZAL: ' . $soap_response_cabezal);
            fwrite($log, PHP_EOL);
            fclose($log);
            return false;
        }


        return true;
    }

}

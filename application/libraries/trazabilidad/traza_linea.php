<?php

$path = __DIR__;
$path_array = explode('/', $path);
$path_array = array_slice($path_array, 0, count($path_array) - 1);
$path_array = array_slice($path_array, 0, count($path_array) - 1);
$path = implode('/', $path_array);

include($path . '/config/constants.php');
define('TRAZA_PATH', $path);

class TrazaLinea {

    public function perform() {

        $tarea = $this->args['tarea'];
        $tramite_id = $this->args['tramite_id'];
        $secuencia = $this->args['secuencia'];
        $id_organismo = $this->args['organismo_id'];
        $id_oficina = $this->args['oficina_id'];
        $proceso_externo_id = $this->args['proceso_externo_id'];
        $nombre_tarea = $this->args['nombre_tarea'];
        $estado = $this->args['estado'];
        $nombre_oficina = $this->args['oficina_nombre'];
        $oid = $this->args['oid'];
        $role = $this->args['role'];
        $etiqueta = $this->args['etiqueta'];
        $visibilidad = $this->args['visibilidad'];
        $tipoRegistroTrazabilidad = $this->args['tipoRegistroTrazabilidad'];

        /*$nombre_proceso = $this->args['nombre_proceso'];
        $id_transaccion = $this->args['id_transaccion'];
        $canal_inicio = $this->args['canal_inicio'];
        $inicio_asistido = $this->args['inicio_asistido'];
        $pasos_ejecutables = $this->args['pasos_ejecutables'];
        $paso = $this->args['paso'];
        $etapa = $this->args['etapa'];*/

        // -- WS LINEA
        $soap_body_linea = '<soapenv:Envelope
            xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:lin="http://ws.web.captura.trazabilidad.agesic.gub.uy/api/v2/lineaService">
              <soapenv:Header/>
                <soapenv:Body>
                     <lin:persist>
                        <traza>
                            <idTraza>
                                <oidOrganismo>' . $id_organismo . '</oidOrganismo>
                                <idProceso>' . $proceso_externo_id . '</idProceso>
                                <idInstancia>' . $tramite_id . '</idInstancia>
                                <tipoProceso>' . WS_AGESIC_TIPO_PROCESO_TRAZABILIDAD . '</tipoProceso>
                            </idTraza>';
        if ($id_oficina != '') {
            $soap_body_linea .='
                            <oidOficina>' . $id_oficina . '</oidOficina>';
        } $soap_body_linea .='
                            <oficina>' . $nombre_oficina . '</oficina>
                            <fechaHoraOrganismo>' . date('Y-m-d', time()) . 'T' . date('H:i:s', time()) . '</fechaHoraOrganismo>
                            <appOrigen>Simple ' . SIMPLE_VERSION . '</appOrigen>';
        if (!$oid) {
            $soap_body_linea .='
                            <involucrados></involucrados>';
        } else {
            $soap_body_linea .='
                            <involucrados>
                                <involucrado>
                                    <oid>' . $oid . '</oid>
                                    <role>' . $role . '</role>
                                </involucrado>
                            </involucrados>';
        }

        $soap_body_linea .= '
                            <datosProceso xsi:type="lin:datosProcesoTramiteLineaDTO">
                                <datosExtra></datosExtra>
                                <etiqueta>' . ($estado == "FINALIZADO" || $estado == "CANCELADO" ? "" : $etiqueta) . '</etiqueta>
                            </datosProceso>
                            <tipoRegistroTrazabilidad>' . $tipoRegistroTrazabilidad . '</tipoRegistroTrazabilidad>
                            <etapa>' . $tarea . '</etapa>
                            <descripcionDeLaEtapa>' . $nombre_tarea . '</descripcionDeLaEtapa>
                            <pasoDelProceso>' . $secuencia . '</pasoDelProceso>
                            <visibilidad>' . ($estado == "FINALIZADO" || $estado == "CANCELADO" ? "VISIBLE" : $visibilidad) . '</visibilidad>
                            <estadoProceso>' . $estado . '</estadoProceso>
                     </traza>
                     </lin:persist>
                   </soapenv:Body>
                  </soapenv:Envelope>';


        $soap_header_linea = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Content-length: " . strlen($soap_body_linea)
        );

        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, WS_AGESIC_TRAZABLIDAD_LINEA);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $soap_body_linea);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER, $soap_header_linea);
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
                curl_setopt($soap_do, CURLOPT_TIMEOUT, 10);
                curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($soap_do, CURLOPT_POST, true);
                curl_setopt($soap_do, CURLOPT_POSTFIELDS, $soap_body_linea);
                curl_setopt($soap_do, CURLOPT_HTTPHEADER, $soap_header_linea);
                $soap_response_linea = curl_exec($soap_do);
                $curl_errno = curl_errno($soap_do);
                $curl_error = curl_error($soap_do);
                curl_close($soap_do);
            }
        }
        $xml = new SimpleXMLElement($soap_response_linea);
        $cod_estado = $xml->xpath(WS_XPATH_COD_ESTADO);

        //si despues de todos los intentos no se logra enviar correcamten, se desencola
        if ($curl_errno > 0) {
            $log = fopen(TRAZA_PATH . '/logs/trazabilidad.log', "a+");
            fwrite($log, 'LINEA GENERADA: ' . $soap_body_linea);
            fwrite($log, PHP_EOL);
            fwrite($log, 'ERROR: ' . $curl_error);
            fwrite($log, PHP_EOL);
            fclose($log);

            throw new Exception('No es posible enviar la linea.');
        }

        if ($cod_estado[0] == "OK") {
            return true;
        } else {
            $log = fopen(TRAZA_PATH . '/logs/trazabilidad.log', "a+");
            fwrite($log, 'LINEA GENERADA: ' . $soap_body_linea);
            fwrite($log, PHP_EOL);
            fwrite($log, 'RESPUESTA LINEA: ' . $soap_response_linea);
            fwrite($log, PHP_EOL);
            fclose($log);
            throw new Exception('No es posible enviar la linea.');
        }

        return true;
    }

}

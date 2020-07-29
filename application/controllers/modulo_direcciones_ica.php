<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class modulo_direcciones_ica extends MY_Controller {

    public function __construct() {
        parent::__construct();
        header('Content-type: application/json; charset=utf-8');
    }

    public function procesar_busqueda_simple_ajax() {
        try {
            if ($this->input->post('direccion')) {

                $direccion = trim(strtoupper($this->input->post('direccion')));
                $dir = str_replace("  ", " ", $direccion);
                $direccion_split_array = explode(' ', $dir);

                if (count($direccion_split_array) > 0) {

                    for ($i = 0; $i < count($direccion_split_array); $i++) {
            //quito espacios en blanco del array por si los hay, en la posicion 0 no deberia haber por trim()
                        if (strlen($direccion_split_array[$i]) == 0) {
                            unset($direccion_split_array[$i]);
                        }
                    }

                    $pos_cero_direccion = $direccion_split_array[0];

            //CASO RUTA + KM
                    if ($pos_cero_direccion == 'RUTA' || $pos_cero_direccion == 'R.' || $pos_cero_direccion == 'R') {
                        $nombre_ruta = 'Ruta ';
                        $numero_kilometro = '';

                        for ($i = 1; $i < count($direccion_split_array); $i++) {
            //quito espacios en blanco del array por si los hay, en la posicion 0 no deberia haber por trim()
                            if ($direccion_split_array[$i] != 'Km' && $direccion_split_array[$i] != 'KILOMETRO' && $direccion_split_array[$i] != 'KM.' && $direccion_split_array[$i] != 'K.' && $direccion_split_array[$i] != 'KM' && $direccion_split_array[$i] != 'K' && $direccion_split_array[$i] != 'KILÓMETRO') {
                                $nombre_ruta .= $direccion_split_array[$i] . ' ';
                            } else {
                                $numero_kilometro = $direccion_split_array[$i + 1];
                                break;
                            }
                        }

                        $retorno = array(
                            'error' => false,
                            'lista' => $this->ws_modulo_direcciones_ruta_kilometros($nombre_ruta, $numero_kilometro)
                        );

                        echo json_encode($retorno);
                    }
            //CASO MANZANA + SOLAR
                    else if ($pos_cero_direccion == 'MANZANA' || $pos_cero_direccion == 'MANZ.' || $pos_cero_direccion == 'M.' || $pos_cero_direccion == 'MANZ' || $pos_cero_direccion == 'M') {
                        $nombre_manzana = '';
                        $numero_solar = '';

                        for ($i = 1; $i < count($direccion_split_array); $i++) {
            //quito espacios en blanco del array por si los hay, en la posicion 0 no deberia haber por trim()
                            if ($direccion_split_array[$i] != 'SOLAR' && $direccion_split_array[$i] != 'SOL.' && $direccion_split_array[$i] != 'S.' && $direccion_split_array[$i] != 'SOL' && $direccion_split_array[$i] != 'S') {
                                $nombre_manzana .= $direccion_split_array[$i] . ' ';
                            } else {
                                $numero_solar = $direccion_split_array[$i + 1];
                                break;
                            }
                        }

                        $retorno = array(
                            'error' => false,
                            'lista' => $this->ws_modulo_direcciones_manzana_solar($nombre_manzana, $numero_solar)
                        );

                        echo json_encode($retorno);
                    }
            //CASO CALLE (NO EMPIEZA CON NUMERO) + NUMERO
                    else if (!is_numeric($pos_cero_direccion)) {
                        $nombre_calle = '';
                        $numero_puerta = '';

                        for ($i = 0; $i < count($direccion_split_array); $i++) {
                            if (!is_numeric($direccion_split_array[$i])) {
                                $nombre_calle .= $direccion_split_array[$i] . ' ';
                            } else {
                                $numero_puerta = $direccion_split_array[$i] . ' ';
                                break;
                            }
                        }

                        $departamento = $this->input->post('departamento');
                        $localidad = $this->input->post('localidad');

                        $calles_normalizada = $this->ws_normalizar_calle($nombre_calle, $departamento, $localidad, $numero_puerta, $esquina = null);
                        $lista_candidatos = array();
                        foreach ($calles_normalizada as $value) {
                            $candidato = $this->ws_modulo_direcciones_calle_puerta($value->Street, $value->Number, $value->CrossingStreetName, $value->Departament, $value->City);
                            foreach ($candidato as $value) {
                                array_push($lista_candidatos, $value);
                            }
                        }
                        $retorno = array(
                            'error' => false,
                            'lista' => $lista_candidatos
                        );

                        echo json_encode($retorno);
                    }
            //CASO CALLE (SI EMPIEZA CON NUMERO) + NUMERO
                    else if (is_numeric($pos_cero_direccion)) {
                        $nombre_calle = $pos_cero_direccion . ' ';
                        $numero_puerta = '';

                        for ($i = 1; $i < count($direccion_split_array); $i++) {
                            if (is_numeric($direccion_split_array[$i])) {
                                $numero_puerta = $direccion_split_array[$i] . ' ';
                                break;
                            } else {
                                $nombre_calle .= $direccion_split_array[$i] . ' ';
                            }
                        }

                        $departamento = $this->input->post('departamento');
                        $localidad = $this->input->post('localidad');

                        $calles_normalizada = $this->ws_normalizar_calle($nombre_calle, $departamento, $localidad, $numero_puerta, $esquina = null);
                        $lista_candidatos = array();
                        foreach ($calles_normalizada as $value) {
                            $candidato = $this->ws_modulo_direcciones_calle_puerta($value->Street, $value->Number, $value->CrossingStreetName, $value->Departament, $value->City);
                            foreach ($candidato as $value) {
                                array_push($lista_candidatos, $value);
                            }
                        }
                        $retorno = array(
                            'error' => false,
                            'lista' => $lista_candidatos
                        );

                        echo json_encode($retorno);
                    }
                }
            } else {
                $retorno = array(
                    'error' => true,
                    'lista' => ''
                );

                echo json_encode($retorno);
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            $retorno = array(
                'error' => true,
                'lista' => 'error_interno'
            );

            echo json_encode($retorno);
        }
    }

    private function existsInArray($objeto, $lista) {
        foreach ($lista as $compare) {
            if (($compare->X == $objeto->X) && ($compare->Y == $objeto->Y)) {
                return true;
            }
        }
        return false;
    }

    public function procesar_busqueda_punto_mapa_ajax() {
        try {
            $x = trim(strtoupper($this->input->post('x')));
            $y = trim(strtoupper($this->input->post('y')));
            $retorno = array(
                'error' => false,
                'lista' => $this->ws_modulo_direcciones_punto_mapa($x, $y)
            );

            echo json_encode($retorno);
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            $retorno = array(
                'error' => true,
                'lista' => 'error_interno'
            );

            echo json_encode($retorno);
        }
    }

    public function procesar_busqueda_avanzada_ajax() {
        try {

            if ($this->input->post('padron')) {
                $padron = trim(strtoupper($this->input->post('padron')));
                $lista_candidatos = $this->ws_modulo_direcciones_padron($padron);
                if (count($lista_candidatos) > 0) {
                    $retorno = array(
                        'error' => false,
                        'lista' => $lista_candidatos
                    );
                    echo json_encode($retorno);
                    return;
                }
            }
            if ($this->input->post('calle')) {

                $direccion = trim(strtoupper($this->input->post('calle')));
                $direccion_split_array = explode(' ', $direccion);

                if (count($direccion_split_array) > 0) {

                    for ($i = 0; $i < count($direccion_split_array); $i++) {
            //quito espacios en blanco del array por si los hay, en la posicion 0 no deberia haber por trim()
                        if (strlen($direccion_split_array[$i]) == 0) {
                            unset($direccion_split_array[$i]);
                        }
                    }

                    $pos_cero_direccion = $direccion_split_array[0];

            //CASO RUTA + KM
                    if ($pos_cero_direccion == 'RUTA' || $pos_cero_direccion == 'R.' || $pos_cero_direccion == 'R') {
                        $nombre_ruta = 'Ruta ';
                        $numero_kilometro_array = $this->input->post('numero');
                        $numero_kilometro_split_array = explode(' ', $numero_kilometro_array);
                        $numero_kilometro = $numero_kilometro_split_array[0];
                        if ($numero_kilometro_split_array[0] == 'Km' || $numero_kilometro_split_array[0] || 'KILOMETRO' || $numero_kilometro_split_array[0] == 'KM.' || $numero_kilometro_split_array[0] == 'K.' || $numero_kilometro_split_array[0] == 'KM' || $numero_kilometro_split_array[0] == 'K' || $numero_kilometro_split_array[0] == 'KILÓMETRO') {
                            $numero_kilometro = $numero_kilometro_split_array[1];
                        }

                        for ($i = 1; $i < count($direccion_split_array); $i++) {
                            $nombre_ruta .= $direccion_split_array[$i] . ' ';
                        }

                        $retorno = array(
                            'error' => false,
                            'lista' => $this->ws_modulo_direcciones_ruta_kilometros($nombre_ruta, $numero_kilometro)
                        );

                        echo json_encode($retorno);
                        return;
                    }
            //CASO CALLE (NO EMPIEZA CON NUMERO) + NUMERO
                    else if (!is_numeric($pos_cero_direccion)) {
                        $nombre_calle = '';
                        $numero_puerta = $this->input->post('numero');

                        for ($i = 0; $i < count($direccion_split_array); $i++) {
                            if (!is_numeric($direccion_split_array[$i])) {
                                $nombre_calle .= $direccion_split_array[$i] . ' ';
                            }
                        }

                        $departamento = $this->input->post('departamento');
                        $localidad = $this->input->post('localidad');
                        $esquina = $this->input->post('esquina');
                        if (!$esquina && !$numero_puerta) {
                            $retorno = array(
                                'error' => true,
                                'lista' => 'error_interno',
                                'errores' => '<div class="alert alert-error"><strong>Error:</strong> Debe introducir número de puerta o esquina para procesar la búsqueda</div>'
                            );

                            echo json_encode($retorno);
                            return;
                        }
                        $calles_normalizada = $this->ws_normalizar_calle($nombre_calle, $departamento, $localidad, $numero_puerta, $esquina);
                        $lista_candidatos = array();
                        foreach ($calles_normalizada as $value) {
                            $candidato = $this->ws_modulo_direcciones_calle_puerta($value->Street, $value->Number, $value->CrossingStreetName, $value->Departament, $value->City);
                            if ($candidato) {
                                foreach ($candidato as $value) {
                                    if (!$this->existsInArray($value, $lista_candidatos)) {
                                        array_push($lista_candidatos, $value);
                                    }
                                }
                            }
                        }

                        $retorno = array(
                            'error' => false,
                            'lista' => $lista_candidatos
                        );

                        echo json_encode($retorno);
                        return;
                    }
            //CASO CALLE (SI EMPIEZA CON NUMERO) + NUMERO
                    else if (is_numeric($pos_cero_direccion)) {
                        $nombre_calle = $pos_cero_direccion . ' ';
                        $numero_puerta = $this->input->post('numero');
                        $departamento = $this->input->post('departamento');
                        $localidad = $this->input->post('localidad');
                        $esquina = $this->input->post('esquina');


                        for ($i = 1; $i < count($direccion_split_array); $i++) {
                            if (!is_numeric($direccion_split_array[$i])) {
                                $nombre_calle .= $direccion_split_array[$i] . ' ';
                            }
                        }

                        $calles_normalizada = $this->ws_normalizar_calle($nombre_calle, $departamento, $localidad, $numero_puerta, $esquina);
                        $lista_candidatos = array();
                        foreach ($calles_normalizada as $value) {
                            $candidato = $this->ws_modulo_direcciones_calle_puerta($value->Street, $value->Number, $value->CrossingStreetName, $value->Departament, $value->City);
                            if ($candidato) {
                                foreach ($candidato as $value) {
                                    if (!$this->existsInArray($value, $lista_candidatos)) {
                                        array_push($lista_candidatos, $value);
                                    }
                                }
                            }
                        }
                        $retorno = array(
                            'error' => false,
                            'lista' => $lista_candidatos
                        );

                        echo json_encode($retorno);
                        return;
                    }
                }
            }
            //CASO MANZANA + SOLAR
            else if ($this->input->post('manzana') && $this->input->post('solar')) {
                $nombre_manzana = $this->input->post('manzana');
                $numero_solar = $this->input->post('solar');

                $retorno = array(
                    'error' => false,
                    'lista' => $this->ws_modulo_direcciones_manzana_solar($nombre_manzana, $numero_solar)
                );

                echo json_encode($retorno);
                exit(); //termina aca
            } else {
                $retorno = array(
                    'error' => true,
                    'lista' => ''
                );

                echo json_encode($retorno);
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            $retorno = array(
                'error' => true,
                'lista' => 'error_interno'
            );

            echo json_encode($retorno);
        }
    }

    private function ws_modulo_direcciones_manzana_solar($nombre_manzana, $numero_solar) {
        try {
            return "";
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function ws_modulo_direcciones_punto_mapa($x, $y) {
        try {
            $soap_body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
                            <soapenv:Header/>
                            <soapenv:Body>
                               <tem:ReverseGeocode>
                                  <tem:x>' . $x . '</tem:x>
                                  <tem:y>' . $y . '</tem:y>
                                  <!--Optional:-->
                                  <tem:theError>error</tem:theError>
                               </tem:ReverseGeocode>
                            </soapenv:Body>
                         </soapenv:Envelope>';

            $soap_header = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "Content-length: " . strlen($soap_body)
            );

            $soap_do = curl_init();
            curl_setopt($soap_do, CURLOPT_URL, WS_PUNTO_MAPA_ICA);
            curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($soap_do, CURLOPT_TIMEOUT, 10);
            curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($soap_do, CURLOPT_POST, true);
            curl_setopt($soap_do, CURLOPT_POSTFIELDS, $soap_body);
            curl_setopt($soap_do, CURLOPT_HTTPHEADER, $soap_header);
            $soap_response = curl_exec($soap_do);
            $curl_errno = curl_errno($soap_do);
            $curl_error = curl_error($soap_do);
            curl_close($soap_do);

            if ($curl_errno > 0 && $http_code != 200 && $http_code != 500) {
                throw new Exception($curl_error . ' http code:' . $http_code);
            }

            $xml = new SimpleXMLElement($soap_response);
            $datos = $xml->xpath("//*[local-name() = 'ReverseGeocodeResult']");

            $lista_rutas = array();
            $domicilio = new stdClass();
            if ($datos) {
                $domicilio->calle = (string) $datos[0]->Properties[0]->clsProperty[0]->Value;
                $domicilio->numero = (string) $datos[0]->Properties[0]->clsProperty[1]->Value;
                $domicilio->ciudad = (string) $datos[0]->Properties[0]->clsProperty[2]->Value;
                $domicilio->esquina = (string) $datos[0]->Properties[0]->clsProperty[3]->Value;
                $domicilio->operacion = (string) "ReverseGeocode";
            } else {
                $domicilio->calle = null;
                $domicilio->numero = null;
                $domicilio->ciudad = null;
                $domicilio->esquina = null;
                $domicilio->operacion = (string) "ReverseGeocode";
            }
            return $domicilio;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function ws_modulo_direcciones_padron($padron) {
        try {
            $soap_body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
                            <soapenv:Header/>
                            <soapenv:Body>
                               <tem:FindPropertyID>
                                  <tem:PropertyID>' . $padron . '</tem:PropertyID>
                                  <tem:theError></tem:theError>
                               </tem:FindPropertyID>
                            </soapenv:Body>
                         </soapenv:Envelope>';

            $soap_header = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "Content-length: " . strlen($soap_body)
            );

            $soap_do = curl_init();
            curl_setopt($soap_do, CURLOPT_URL, WS_PADRON_ICA);
            curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($soap_do, CURLOPT_TIMEOUT, 10);
            curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($soap_do, CURLOPT_POST, true);
            curl_setopt($soap_do, CURLOPT_POSTFIELDS, $soap_body);
            curl_setopt($soap_do, CURLOPT_HTTPHEADER, $soap_header);
            $soap_response = curl_exec($soap_do);
            $curl_errno = curl_errno($soap_do);
            $curl_error = curl_error($soap_do);
            curl_close($soap_do);

            if ($curl_errno > 0 && $http_code != 200 && $http_code != 500) {
                throw new Exception($curl_error . ' http code:' . $http_code);
            }

            $xml = new SimpleXMLElement($soap_response);
            $datos = $xml->xpath("//*[local-name() = 'FindPropertyIDResult']");

            $lista_rutas = array();
            if ($datos) {
                for ($i = 0; $i < count($datos[0]->clsCandidateGX); $i++) {
                    $ruta_class = new stdClass();
                    $ruta_class->X = trim((string) $datos[0]->clsCandidateGX[$i]->X);
                    $ruta_class->Y = trim((string) $datos[0]->clsCandidateGX[$i]->Y);
                    $ruta_class->MatchAddress = trim((string) $datos[0]->clsCandidateGX[$i]->MatchAddress);
                    $ruta_class->StanAddress = trim((string) $datos[0]->clsCandidateGX[$i]->StanAddress);
                    $ruta_class->Score = trim((string) $datos[0]->clsCandidateGX[$i]->Score);
                    $ruta_class->Operacion = "FindPropertyID";
                    $lista_rutas[$i] = $ruta_class;
                }
            }

            return $lista_rutas;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function ws_modulo_direcciones_ruta_kilometros($nombre_ruta, $numero_kilometro) {
        try {
            $soap_body = '<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
                          <soap12:Body>
                            <FindHighwayKilometer xmlns="http://tempuri.org/">
                              <HighwayName>' . $nombre_ruta . '</HighwayName>
                              <Kilometer>' . $numero_kilometro . '</Kilometer>
                              <theError></theError>
                            </FindHighwayKilometer>
                          </soap12:Body>
                        </soap12:Envelope>';

            $soap_header = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "Content-length: " . strlen($soap_body)
            );

            $soap_do = curl_init();
            curl_setopt($soap_do, CURLOPT_URL, WS_RUTA_KM_ICA);
            curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($soap_do, CURLOPT_TIMEOUT, 10);
            curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($soap_do, CURLOPT_POST, true);
            curl_setopt($soap_do, CURLOPT_POSTFIELDS, $soap_body);
            curl_setopt($soap_do, CURLOPT_HTTPHEADER, $soap_header);
            $soap_response = curl_exec($soap_do);
            $curl_errno = curl_errno($soap_do);
            $curl_error = curl_error($soap_do);
            curl_close($soap_do);

            if ($curl_errno > 0 && $http_code != 200 && $http_code != 500) {
                throw new Exception($curl_error . ' http code:' . $http_code);
            }

            $xml = new SimpleXMLElement($soap_response);
            $datos = $xml->xpath("//*[local-name() = 'FindHighwayKilometerResult']");

            $lista_rutas = array();
            if ($datos) {
                for ($i = 0; $i < count($datos[0]->clsCandidateGX); $i++) {
                    $ruta_class = new stdClass();
                    $ruta_class->X = trim((string) $datos[0]->clsCandidateGX[$i]->X);
                    $ruta_class->Y = trim((string) $datos[0]->clsCandidateGX[$i]->Y);
                    $ruta_class->MatchAddress = trim((string) $datos[0]->clsCandidateGX[$i]->MatchAddress);
                    $ruta_class->StanAddress = trim((string) $datos[0]->clsCandidateGX[$i]->StanAddress);
                    $ruta_class->Score = trim((string) $datos[0]->clsCandidateGX[$i]->Score);
                    $ruta_class->Operacion = "FindHighwayKilometer";
                    $lista_rutas[$i] = $ruta_class;
                }
            }
            return $lista_rutas;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /* private function ws_modulo_direcciones_manzana_solar($nombre_manzana, $numero_solar) {
      //no implementado porque no se tiene el ws aun
      $lista_manzanas = array();
      return $lista_manzanas;
      } */

    private function ws_normalizar_calle($streetName, $department = "", $city = "", $number = "", $crossingStreetName = null) {
//no implementado porque no se tiene el ws aun        
        try {
            $soap_body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
                        <soapenv:Header/>
                        <soapenv:Body>
                            <tem:FindStreetNames>
                                <tem:Department>' . $department . '</tem:Department>
                                <tem:City>' . $city . '</tem:City>
                                <tem:StreetName>' . $streetName . '</tem:StreetName>
                                <tem:theError></tem:theError>
                            </tem:FindStreetNames>
                        </soapenv:Body>
                      </soapenv:Envelope>';

            $soap_header = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "Content-length: " . strlen($soap_body)
            );

            $soap_do = curl_init();
            curl_setopt($soap_do, CURLOPT_URL, WS_STREET_NAME_ICA);
            curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($soap_do, CURLOPT_TIMEOUT, 10);
            curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($soap_do, CURLOPT_POST, true);
            curl_setopt($soap_do, CURLOPT_POSTFIELDS, $soap_body);
            curl_setopt($soap_do, CURLOPT_HTTPHEADER, $soap_header);
            $soap_response = curl_exec($soap_do);
            $curl_errno = curl_errno($soap_do);
            $curl_error = curl_error($soap_do);
            curl_close($soap_do);

            if ($curl_errno > 0 && $http_code != 200 && $http_code != 500) {
                throw new Exception($curl_error . ' http code:' . $http_code);
            }

            $xml = new SimpleXMLElement($soap_response);
            $datos = $xml->xpath("//*[local-name() = 'FindStreetNamesResult']");

            $lista_calles = array();
            if ($datos) {
                for ($i = 0; $i < count($datos[0]); $i++) {
                    $street = explode("||", trim((string) $datos[0]->string[$i]));
                    if ($crossingStreetName == "" || $crossingStreetName == null) {
                        $calles = new stdClass();
                        $calles->Street = $street[0];
                        $calles->Departament = $street[1];
                        $calles->City = $street[2];
                        $calles->Number = $number == "" ? 0 : $number;
                        $calles->CrossingStreetName = "";
                        $calles->Operacion = "FindHighwayKilometer";
                        array_push($lista_calles, $calles);
                    } else {
                        $lista_esquina = $this->ws_normalizar_calle($crossingStreetName, $department, $city);

                        foreach ($lista_esquina as $value) {
                            $calles = new stdClass();
                            $calles->Street = $street[0];
                            $calles->Departament = $street[1];
                            $calles->City = $street[2];
                            $calles->Number = $number == "" ? 0 : $number;
                            $calles->CrossingStreetName = $value->Street;
                            $calles->Operacion = "FindHighwayKilometer";
                            array_push($lista_calles, $calles);
                        }
                    }
                }
            }
            return $lista_calles;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function ws_modulo_direcciones_calle_puerta($nombre_calle, $numero_puerta, $esquina, $departamento, $localidad) {
        try {
            $soap_body = '<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
                        <soap12:Body>
                          <FindAddresses xmlns="http://tempuri.org/">
                            <Department>' . $departamento . '</Department>
                            <City>' . $localidad . '</City>
                            <StreetName>' . $nombre_calle . '</StreetName>
                            <Number>' . $numero_puerta . '</Number>
                            <CrossingStreetName>' . $esquina . '</CrossingStreetName>
                            <theError></theError>
                          </FindAddresses>
                        </soap12:Body>
                      </soap12:Envelope>';

            $soap_header = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "Content-length: " . strlen($soap_body)
            );

            $soap_do = curl_init();
            curl_setopt($soap_do, CURLOPT_URL, WS_CALLE_NUMERO_ICA);
            curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($soap_do, CURLOPT_TIMEOUT, 10);
            curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($soap_do, CURLOPT_POST, true);
            curl_setopt($soap_do, CURLOPT_POSTFIELDS, $soap_body);
            curl_setopt($soap_do, CURLOPT_HTTPHEADER, $soap_header);
            $soap_response = curl_exec($soap_do);
            $curl_errno = curl_errno($soap_do);
            $curl_error = curl_error($soap_do);
            $http_code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
            curl_close($soap_do);

            if ($curl_errno > 0 && $http_code != 200 && $http_code != 500) {
                throw new Exception($curl_error . ' http code:' . $http_code);
            }

            $xml = new SimpleXMLElement($soap_response);
            $datos = $xml->xpath("//*[local-name() = 'FindAddressesResult']");
            $lista_direcciones = array();
            if ($datos) {
                for ($i = 0; $i < count($datos[0]->clsCandidateGX); $i++) {
                    $direccion_class = new stdClass();
                    $direccion_class->X = trim((string) $datos[0]->clsCandidateGX[$i]->X);
                    $direccion_class->Y = trim((string) $datos[0]->clsCandidateGX[$i]->Y);
                    $direccion_class->MatchAddress = trim((string) $datos[0]->clsCandidateGX[$i]->MatchAddress);
                    $direccion_class->StanAddress = trim((string) $datos[0]->clsCandidateGX[$i]->StanAddress);
                    $direccion_class->Score = trim((string) $datos[0]->clsCandidateGX[$i]->Score);
                    $direccion_class->Operacion = "FindAddresses";
                    $lista_direcciones[$i] = $direccion_class;
                }
            }

            return $lista_direcciones;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function guardar_datos_seguimiento_ajax() {
        $datos_guardar = $this->input->post('datos_guardar');
        $etapa_id = $this->input->post('etapa_id');

        $limpiar_datos_seguimiento = $this->input->post('limpiar_datos_seguimiento');

        if ((bool) $limpiar_datos_seguimiento) {
            $nombre_campo = $this->input->post('nombre_campo');

            $datos_limpiar = array(
                $nombre_campo . '_X',
                $nombre_campo . '_Y',
                $nombre_campo . '_MatchAddress',
                $nombre_campo . '_StanAddress',
                $nombre_campo . '_Score',
                $nombre_campo . '_Operacion',
            );

            for ($i = 0; $i < count($datos_limpiar); $i++) {
                $dato_borrar = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($datos_limpiar[$i], $etapa_id);
                $dato_borrar->delete();
            }
        }

        foreach ($datos_guardar as $nombre_dato => $valor_dato) {
            $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_dato, $etapa_id);
            if (!$dato_seguimiento) {
                $dato_seguimiento = new DatoSeguimiento();
            }
            $dato_seguimiento->nombre = $nombre_dato;
            $dato_seguimiento->valor = (string) $valor_dato;
            $dato_seguimiento->etapa_id = $etapa_id;
            $dato_seguimiento->save();
        }
    }

}

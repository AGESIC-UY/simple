<?php

class Pagos_externo extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('cookies_helper');
        $this->load->helper('trazabilidad_helper');
    }

    // -- Metodo para vuelta de pagos genericos
    public function control_generico() {
        //la variable usada para bloquear el cambio de URL
        $this->session->set_userdata('ejecutar_form', 'true');

        $url_vuelta = $this->session->userdata('simple_bpm_gwp_redirect');
        $etapa_id = $this->session->userdata('id_etapa');

        if (empty($url_vuelta)) {
            redirect(site_url());
        } else {
            redirect($url_vuelta);
        }
    }

    // -- Metodo para vuelta de pagos antel
    public function control() {

        //la variable usada para bloquear el camio de URL
        $this->session->set_userdata('ejecutar_form', 'true');

        $url_vuelta = $this->session->userdata('simple_bpm_gwp_redirect');

        if (empty($url_vuelta)) {
            redirect(site_url());
        } else {
            $id_solicitud = $this->session->userdata('id_solicitud');
            $etapa_id = $this->session->userdata('id_etapa');

            $this->generar_variables_pago($id_solicitud, $etapa_id);

            redirect($url_vuelta);
        }
    }

    public function completado() {
        if ($this->input->post('IdSol')) {
            $id_solicitud = $this->input->post('IdSol');
            $registro_pago = Doctrine_Query::create()
                    ->from('Pago p')
                    ->where('p.id_solicitud = ?', $id_solicitud)
                    ->orderBy('p.id', 'DESC')
                    ->fetchOne();
            if (!empty($registro_pago)) {
                if ($registro_pago->estado != 'realizado') {
                    $estado = $this->generar_variables_pago($id_solicitud, $registro_pago->id_etapa);
                    if ($estado == 'ok')
                        $this->envia_email_pago('realizado', $registro_pago->id_etapa, $id_solicitud);
                }

                $registro_pago_new = new Pago();
                $registro_pago_new->id_solicitud = $id_solicitud;
                $registro_pago_new->id_tramite_interno = $registro_pago->id_tramite_interno;
                $registro_pago_new->id_tramite = $registro_pago->id_tramite;
                $registro_pago_new->id_etapa = $registro_pago->id_etapa;
                $registro_pago_new->pasarela = $registro_pago->pasarela;
                $registro_pago_new->estado = 'realizado';
                $registro_pago_new->fecha_actualizacion = date('d/m/Y H:i:s');
                $registro_pago_new->usuario = "Pagos Externos";
                $registro_pago_new->save();

                $accion_ejecutada = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_accion_ejecutada', $registro_pago->id_etapa);
                if ($accion_ejecutada) {
                    $accion = Doctrine::getTable('Accion')->find(trim($accion_ejecutada->valor, '"'));
                    $eventoPago = Doctrine::getTable('EventoPago')->findByAccionIdAndInstante($accion->id, "ok");
                    foreach ($eventoPago as $e) {
                        $r = new Regla($e->regla);
                        if ($r->evaluar($registro_pago->id_etapa)) {
                            $accion_ejecutar = Doctrine::getTable('Accion')->find($e->accion_ejecutar_id);
                            $etapa_ejecutar = Doctrine::getTable('Etapa')->find($registro_pago->id_etapa);
                            $accion_ejecutar->ejecutar($etapa_ejecutar, $e);
                        }
                    }
                }
            }

            echo 'OK';
        } else {
            redirect(site_url());
        }
    }

    public function error() {
        if ($this->input->post('IdSol')) {
            $id_solicitud = $this->input->post('IdSol');
            $registro_pago = Doctrine_Query::create()
                    ->from('Pago p')
                    ->where('p.id_solicitud = ?', $id_solicitud)
                    ->orderBy('p.id', 'DESC')
                    ->fetchOne();

            if (!empty($registro_pago)) {
                $registro_pago_new = new Pago();
                $registro_pago_new->id_solicitud = $id_solicitud;
                $registro_pago_new->id_tramite_interno = $registro_pago->id_tramite_interno;
                $registro_pago_new->id_tramite = $registro_pago->id_tramite;
                $registro_pago_new->id_etapa = $registro_pago->id_etapa;
                $registro_pago_new->pasarela = $registro_pago->pasarela;
                $registro_pago_new->estado = 'error';
                $registro_pago_new->fecha_actualizacion = date('d/m/Y H:i:s');
                $registro_pago_new->usuario = "Pagos Externos";
                $registro_pago_new->save();

                $accion_ejecutada = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_accion_ejecutada', $registro_pago->id_etapa);

                if ($accion_ejecutada) {
                    $accion = Doctrine::getTable('Accion')->find(trim($accion_ejecutada->valor, '"'));
                    $eventoPago = Doctrine::getTable('EventoPago')->findByAccionIdAndInstante($accion->id, "error");
                    foreach ($eventoPago as $e) {
                        $r = new Regla($e->regla);
                        if ($r->evaluar($registro_pago->id_etapa)) {
                            $accion_ejecutar = Doctrine::getTable('Accion')->find($e->accion_ejecutar_id);
                            $etapa_ejecutar = Doctrine::getTable('Etapa')->find($registro_pago->id_etapa);
                            $accion_ejecutar->ejecutar($etapa_ejecutar, $e);
                        }
                    }
                }
            }

            echo 'OK';
        } else {
            redirect(site_url());
        }
    }

    public function pendiente() {
        if ($this->input->post('IdSol')) {
            $id_solicitud = $this->input->post('IdSol');

            $registro_pago = Doctrine_Query::create()
                    ->from('Pago p')
                    ->where('p.id_solicitud = ?', $id_solicitud)
                    ->orderBy('p.id', 'DESC')
                    ->fetchOne();

            if (!empty($registro_pago)) {
                switch ($this->input->post('IdFormaPago')) {
                    case '2': $forma_pago = 'offline';
                        break;
                    case '3': $forma_pago = 'offline';
                        break;
                    case '10': $forma_pago = 'offline';
                        break;
                    default: $forma_pago = 'online';
                }
                if ($forma_pago == 'offline') {
                    if ($registro_pago->estado != 'pendiente') {
                        $estado = $this->generar_variables_pago($id_solicitud, $registro_pago->id_etapa);
                        if ($estado == 'ok')
                            $this->envia_email_pago('pendiente', $registro_pago->id_etapa, $id_solicitud);
                    }
                }

                $registro_pago_new = new Pago();
                $registro_pago_new->id_solicitud = $id_solicitud;
                $registro_pago_new->id_tramite_interno = $registro_pago->id_tramite_interno;
                $registro_pago_new->id_tramite = $registro_pago->id_tramite;
                $registro_pago_new->id_etapa = $registro_pago->id_etapa;
                $registro_pago_new->pasarela = $registro_pago->pasarela;
                $registro_pago_new->estado = 'pendiente';
                $registro_pago_new->fecha_actualizacion = date('d/m/Y H:i:s');
                $registro_pago_new->usuario = "Pagos Externos";
                $registro_pago_new->save();

                $accion_ejecutada = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_accion_ejecutada', $registro_pago->id_etapa);

                if ($accion_ejecutada) {
                    $accion = Doctrine::getTable('Accion')->find(trim($accion_ejecutada->valor, '"'));
                    $eventoPago = Doctrine::getTable('EventoPago')->findByAccionIdAndInstante($accion->id, "pendiente");
                    foreach ($eventoPago as $e) {
                        $r = new Regla($e->regla);
                        if ($r->evaluar($registro_pago->id_etapa)) {
                            $accion_ejecutar = Doctrine::getTable('Accion')->find($e->accion_ejecutar_id);
                            $etapa_ejecutar = Doctrine::getTable('Etapa')->find($registro_pago->id_etapa);
                            $accion_ejecutar->ejecutar($etapa_ejecutar, $e);
                        }
                    }
                }
            }

            echo 'OK';
        } else {
            redirect(site_url());
        }
    }

    public function rechazado() {
        if ($this->input->post('IdSol')) {
            $id_solicitud = $this->input->post('IdSol');

            $registro_pago = Doctrine_Query::create()
                    ->from('Pago p')
                    ->where('p.id_solicitud = ?', $id_solicitud)
                    ->orderBy('p.id', 'DESC')
                    ->fetchOne();

            if (!empty($registro_pago)) {
                $registro_pago_new = new Pago();
                $registro_pago_new->id_solicitud = $id_solicitud;
                $registro_pago_new->id_tramite_interno = $registro_pago->id_tramite_interno;
                $registro_pago_new->id_tramite = $registro_pago->id_tramite;
                $registro_pago_new->id_etapa = $registro_pago->id_etapa;
                $registro_pago_new->pasarela = $registro_pago->pasarela;
                $registro_pago_new->estado = 'rechazado';
                $registro_pago_new->fecha_actualizacion = date('d/m/Y H:i:s');
                $registro_pago_new->usuario = "Pagos Externos";
                $registro_pago_new->save();

                $accion_ejecutada = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_accion_ejecutada', $registro_pago->id_etapa);

                if ($accion_ejecutada) {
                    $accion = Doctrine::getTable('Accion')->find(trim($accion_ejecutada->valor, '"'));
                    $eventoPago = Doctrine::getTable('EventoPago')->findByAccionIdAndInstante($accion->id, "rechazado");
                    foreach ($eventoPago as $e) {
                        $r = new Regla($e->regla);
                        if ($r->evaluar($registro_pago->id_etapa)) {
                            $accion_ejecutar = Doctrine::getTable('Accion')->find($e->accion_ejecutar_id);
                            $etapa_ejecutar = Doctrine::getTable('Etapa')->find($registro_pago->id_etapa);
                            $accion_ejecutar->ejecutar($etapa_ejecutar, $e);
                        }
                    }
                }
            }

            echo 'OK';
        } else {
            redirect(site_url());
        }
    }

    public function envia_email_pago($tipo, $etapa_id, $id_solicitud, $url = false) {
        if ($url) {
            preg_match("/\/ejecutar\/([0-9]*)\/[0-9]*/", $url, $etapa_id);
            $etapa_id = $etapa_id[1];

            $dato = new DatoSeguimiento();
            $dato->nombre = 'url_formulario_pago';
            $dato->valor = (string) $url;
            $dato->etapa_id = $etapa_id;
            $dato->save();
        } else {
            $url = site_url();
        }

        $CI = & get_instance();
        $etapa = Doctrine_Query::create()
                ->from('Etapa e')
                ->where('e.id = ?', $etapa_id)
                ->fetchOne();

        $cuenta = $etapa->Tramite->Proceso->Cuenta;

        $registro_de_pago = Doctrine_Query::create()
                ->from('Pago p')
                ->where('p.id_solicitud = ?', $id_solicitud)
                ->orderBy('p.id', 'DESC')
                ->fetchOne();

        $pasarela = Doctrine_Query::create()
                ->from('PasarelaPagoAntel pa')
                ->where('pa.id = ?', $registro_de_pago->pasarela)
                ->execute();
        $pasarela = $pasarela[0];

        $no_enviar = false;

        $accion_ejecutada = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_accion_ejecutada', $etapa_id);
        $accion = Doctrine::getTable('Accion')->find($accion_ejecutada->valor);

        switch ($tipo) {
            case 'inicio':
                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_LinkEstado', $etapa_id);
                if ($dato)
                    $dato->delete();
                $dato = new DatoSeguimiento();
                $dato->nombre = 'Solicitud_LinkEstado';
                //$dato->valor = '<a href="' . str_replace('ejecutar', 'ejecutar_pago', $url) . "/" . UsuarioSesion::usuario()->id . '">Ver el estado del pago</a>';
                $dato->valor = str_replace('ejecutar', 'ejecutar_pago', $url) . "/" . UsuarioSesion::usuario()->id;
                $dato->etapa_id = $etapa_id;
                $dato->save();

                $regla = new Regla($accion->extra->tema_email_inicio);
                $tema = $regla->getExpresionParaOutput($etapa_id);
                $subject = $tema;

                $regla = new Regla($accion->extra->cuerpo_email_inicio);
                $cuerpo = $regla->getExpresionParaOutput($etapa_id);
                $message = $cuerpo;
                break;
            case 'pendiente':
                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_LinkEstado', $etapa_id);
                if ($dato)
                    $dato->delete();
                $dato = new DatoSeguimiento();
                $dato->nombre = 'Solicitud_LinkEstado';
                //$dato->valor = '<a href="' . site_url() .'etapas\/ejecutar_pago\/'. $etapa_id . '\/0\/' . UsuarioSesion::usuario()->id . '">Ver el estado del pago</a>';
                $dato->valor = $url . "etapas/ejecutar_pago/" . $etapa_id . "/0/" . UsuarioSesion::usuario()->id;
                $dato->etapa_id = $etapa_id;
                $dato->save();

                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_LinkTicket', $etapa_id);
                if ($dato)
                    $dato->delete();
                $dato = new DatoSeguimiento();
                $dato->nombre = 'Solicitud_LinkTicket';
                //$dato->valor = '<a href="' . site_url() .'pagos\/generar_ticket?t='. $id_solicitud .'&e='. $etapa_id .'">Ticket de pago</a>';
                $dato->valor = site_url() . 'pagos/generar_ticket?t=' . $id_solicitud . '&e=' . $etapa_id;
                $dato->etapa_id = $etapa_id;
                $dato->save();

                $regla = new Regla($accion->extra->tema_email_pendiente);
                $tema = $regla->getExpresionParaOutput($etapa_id);
                $subject = $tema;

                $regla = new Regla($accion->extra->cuerpo_email_pendiente);
                $cuerpo = $regla->getExpresionParaOutput($etapa_id);
                $message = $cuerpo;
                break;
            case 'realizado':
                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_LinkEstado', $etapa_id);
                if ($dato)
                    $dato->delete();
                $dato = new DatoSeguimiento();
                $dato->nombre = 'Solicitud_LinkEstado';
                //$dato->valor = '<a href="' . site_url() .'etapas\/ejecutar_pago\/'. $etapa_id . '\/0\/' . UsuarioSesion::usuario()->id . '">Ver el estado del pago</a>';
                $dato->valor = $url . "etapas/ejecutar_pago/" . $etapa_id . "/0/" . UsuarioSesion::usuario()->id;
                $dato->etapa_id = $etapa_id;
                $dato->save();

                $regla = new Regla($accion->extra->tema_email_ok);
                $tema = $regla->getExpresionParaOutput($etapa_id);
                $subject = $tema;

                $regla = new Regla($accion->extra->cuerpo_email_ok);
                $cuerpo = $regla->getExpresionParaOutput($etapa_id);
                $message = $cuerpo;
                break;
            case 'timeout':
                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_LinkEstado', $etapa_id);
                if ($dato)
                    $dato->delete();
                $dato = new DatoSeguimiento();
                $dato->nombre = 'Solicitud_LinkEstado';
                //$dato->valor = '<a href="' . site_url() .'etapas\/ejecutar_pago\/'. $etapa_id . '\/0\/' . UsuarioSesion::usuario()->id . '">Ver el estado del pago</a>';
                $dato->valor = $url . "etapas/ejecutar_pago/" . $etapa_id . "/0/" . UsuarioSesion::usuario()->id;
                $dato->etapa_id = $etapa_id;
                $dato->save();

                $regla = new Regla($accion->extra->tema_email_timeout);
                $tema = $regla->getExpresionParaOutput($etapa_id);
                $subject = $tema;

                $regla = new Regla($accion->extra->cuerpo_email_timeout);
                $cuerpo = $regla->getExpresionParaOutput($etapa_id);
                $message = $cuerpo;
                break;
            default:
                $no_enviar = true;
        }

        if (empty($subject)) {
            $no_enviar = true;
        }

        if (!$no_enviar) {
            $email_tramite = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('email_tramite_inicial__e' . $etapa_id, $etapa_id);
            if ($email_tramite) {
                $campo = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($email_tramite->valor, $etapa_id);
                $destinatario = $campo->valor;
            }

            if (($destinatario) && (strlen($message) > 0)) {
                if (!$cuenta->correo_remitente) {
                    ($CI->config->item('main_domain') == '') ? $from = $cuenta->nombre . '@simple' : $from = $cuenta->nombre . '@' . $CI->config->item('main_domain');
                } else {
                    $from = $cuenta->correo_remitente;
                }

                //Enviar emails
                $data = new stdClass();
                $data->from = $from;
                $data->from_name = $cuenta->nombre_largo;
                $data->to = $destinatario;
                $data->subject = $subject;
                $data->message = $message;
                $data->cc = null;
                $data->bcc = null;
                $data->attach = null;
                $data_json = json_encode($data);
                $b64 = base64_encode($data_json);
                $comando = 'php index.php tasks/enviarmails enviar "' . $b64 . '" > /dev/null &';
                exec($comando);
            }
        }
    }

    function generar_variables_pago($id_solicitud, $etapa_id) {
        $registro_de_pago = Doctrine_Query::create()
                ->from('Pago p')
                ->where('p.id_solicitud = ?', $id_solicitud)
                ->orderBy('p.id', 'DESC')
                ->fetchOne();
        $registro_de_pago_primero = Doctrine_Query::create()
                ->from('Pago p')
                ->where('p.id_solicitud = ?', $id_solicitud)
                ->orderBy('p.id', 'ASC')
                ->fetchOne();
        if (!$registro_de_pago_primero && !$registro_de_pago) {
            exit("Usted no tiene acceso a este recurso");
        }
        $clave_tramite = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(md5($registro_de_pago_primero->id . '_clave_tramite_pasarela_pagos'), $registro_de_pago->id_etapa);

        $ws_body = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:con="http://tempuri.org/wsorg/Consultas">';
        if (!empty(SOAP_PASARELA_PAGO_CONSULTA)) {
            if (SOAP_PASARELA_PAGO_CONSULTA == '1.1') {
                $ws_body = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:con="http://tempuri.org/wsorg/Consultas">';
            }
        }
        $ws_body = $ws_body . '<soap:Header/>
       <soap:Body>
          <con:ObtenerDatosTransaccion>
             <con:pIdSolicitud>' . $id_solicitud . '</con:pIdSolicitud>
             <con:pIdTramite>' . $registro_de_pago->id_tramite . '</con:pIdTramite>
             <con:pClave>' . $clave_tramite->valor . '</con:pClave>
          </con:ObtenerDatosTransaccion>
       </soap:Body>
    </soap:Envelope>';

        $ws_header = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Content-length: " . strlen($ws_body),
            "SOAPAction: \"http://tempuri.org/wsorg/Consultas/ObtenerDatosTransaccion\""
        );

        $pasarela = Doctrine_Query::create()
                ->from('PasarelaPagoAntel pa')
                ->where('pa.id = ?', $registro_de_pago->pasarela)
                ->execute();
        $pasarela = $pasarela[0];

        $ws_do = curl_init();
        curl_setopt($ws_do, CURLOPT_URL, WS_PASARELA_PAGO_CONSULTA);
        curl_setopt($ws_do, CURLOPT_CONNECTTIMEOUT, PASARELA_PAGO_TIMEOUT_CONEXION);
        curl_setopt($ws_do, CURLOPT_TIMEOUT, PASARELA_PAGO_TIMEOUT_RESPUESTA);
        curl_setopt($ws_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ws_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ws_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ws_do, CURLOPT_POST, true);
        curl_setopt($ws_do, CURLOPT_SSLVERSION, 1);
        curl_setopt($ws_do, CURLOPT_SSLCERT, UBICACION_CERTIFICADOS_PASARELA . $pasarela->certificado);
        curl_setopt($ws_do, CURLOPT_SSLCERTPASSWD, $pasarela->pass_clave_certificado);
        curl_setopt($ws_do, CURLOPT_SSLKEY, UBICACION_CERTIFICADOS_PASARELA . $pasarela->clave_certificado);

        curl_setopt($ws_do, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ws_do, CURLOPT_POSTFIELDS, $ws_body);
        curl_setopt($ws_do, CURLOPT_HTTPHEADER, $ws_header);

        if (!empty(PROXY_PASARELA_PAGO)) {
            curl_setopt($ws_do, CURLOPT_PROXY, PROXY_PASARELA_PAGO);
        }

        $ws_response = curl_exec($ws_do);
        $curl_errno = curl_errno($ws_do); // -- Codigo de error
        $curl_error = curl_error($ws_do); // -- Descripcion del error
        $http_code = curl_getinfo($ws_do, CURLINFO_HTTP_CODE); // -- Codigo respuesta HTTP

        curl_close($ws_do);
        if ($curl_errno > 0 || $http_code != 200) {
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdEstado', $etapa_id);
            if ($dato) {
                $dato->valor = 'timeout';
                $dato->etapa_id = $etapa_id;
                $dato->save();
            } else {
                $dato = new DatoSeguimiento();
                $dato->nombre = 'Solicitud_IdEstado';
                $dato->valor = 'timeout';
                $dato->etapa_id = $etapa_id;
                $dato->save();
            }

            //dio un error el consultar se pone en -1 la variable codigo_estado_solicitud_pago
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago', $etapa_id);
            if ($dato) {
                $dato->valor = '-1';
                $dato->etapa_id = $etapa_id;
                $dato->save();
            } else {
                $dato = new DatoSeguimiento();
                $dato->nombre = 'codigo_estado_solicitud_pago';
                $dato->valor = '-1';
                $dato->etapa_id = $etapa_id;
                $dato->save();
            }

            return 'error';
        } else {
            $xml = new SimpleXMLElement($ws_response);

            $pago_estado = $xml->xpath("//*[local-name() = 'IdEstado']/text()");

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago', $etapa_id);
            if ($dato)
                $dato->delete();
            $dato = new DatoSeguimiento();
            $dato->nombre = 'codigo_estado_solicitud_pago';
            $dato->valor = '0';
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdEstado', $etapa_id);
            if ($dato)
                $dato->delete();
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_IdEstado';
            $dato->valor = (string) (count($pago_estado) > 0 ? $pago_estado[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Fecha', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_fecha = $xml->xpath("//*[local-name() = 'Fecha']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_Fecha';
            $dato->valor = (string) (count($pago_fecha) > 0 ? $pago_fecha[0] : date("d-mm-YY H:m:s"));
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Transaccion', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_transaccion = $xml->xpath("//*[local-name() = 'IdTransaccion']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_Transaccion';
            $dato->valor = (string) (count($pago_transaccion) > 0 ? $pago_transaccion[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            /*
              $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdSolicitud', $etapa_id);
              if($dato)
              $dato->delete();
              $pago_solicitud = $xml->xpath("//*[local-name() = 'IdSolicitud']/text()");
              $dato = new DatoSeguimiento();
              $dato->nombre = 'Solicitud_IdSolicitud';
              $dato->valor = (string)$pago_solicitud[0];
              $dato->etapa_id = $etapa_id;
              $dato->save();
             */

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Autorizacion', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_autorizacion = $xml->xpath("//*[local-name() = 'Autorizacion']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_Autorizacion';
            $dato->valor = (string) (count($pago_autorizacion) > 0 ? $pago_autorizacion[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdFormaPago', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_forma = $xml->xpath("//*[local-name() = 'IdFormaPago']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_IdFormaPago';
            $dato->valor = (string) (count($pago_forma) > 0 ? $pago_forma[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_FechaConciliacion', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_concilia = $xml->xpath("//*[local-name() = 'FechaConciliacion']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_FechaConciliacion';
            $dato->valor = (string) (count($pago_concilia) > 0 ? $pago_concilia[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdTasa', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_id_tasa = $xml->xpath("//*[local-name() = 'IdTasa']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_IdTasa';
            $dato->valor = (string) (count($pago_id_tasa) > 0 ? $pago_id_tasa[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_ValorTasa', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_valor_tasa = $xml->xpath("//*[local-name() = 'ValorTasa']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_ValorTasa';
            $dato->valor = (string) (count($pago_valor_tasa) > 0 ? $pago_valor_tasa[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_MontoTotal', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_monto = $xml->xpath("//*[local-name() = 'MontoTotal']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_MontoTotal';
            $dato->valor = (string) (count($pago_monto) > 0 ? $pago_monto[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdTramite', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_id_tramite = $xml->xpath("//*[local-name() = 'IdTramite']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_IdTramite';
            $dato->valor = (string) (count($pago_id_tramite) > 0 ? $pago_id_tramite[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_ImporteTasa1', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_tasa1 = $xml->xpath("//*[local-name() = 'ImporteTasa1']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_ImporteTasa1';
            $dato->valor = (string) (count($pago_tasa1) > 0 ? $pago_tasa1[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_ImporteTasa2', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_tasa2 = $xml->xpath("//*[local-name() = 'ImporteTasa2']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_ImporteTasa2';
            $dato->valor = (string) (count($pago_tasa2) > 0 ? $pago_tasa2[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_ImporteTasa3', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_tasa3 = $xml->xpath("//*[local-name() = 'ImporteTasa3']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_ImporteTasa3';
            $dato->valor = (string) (count($pago_tasa3) > 0 ? $pago_tasa3[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Cantidades', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_cantidades = $xml->xpath("//*[local-name() = 'Cantidades']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_Cantidades';
            $dato->valor = (string) (count($pago_cantidades) > 0 ? $pago_cantidades[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_FechaVto', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_fecha_vto = $xml->xpath("//*[local-name() = 'FechaVto']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_FechaVto';
            $dato->valor = (string) (count($pago_fecha_vto) > 0 ? $pago_fecha_vto[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_CodDesglose', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_desglose = $xml->xpath("//*[local-name() = 'CodDesglose']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_CodDesglose';
            $dato->valor = (string) (count($pago_desglose) > 0 ? $pago_desglose[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_MontoDesglose', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_monto_desglose = $xml->xpath("//*[local-name() = 'MontoDesglose']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_MontoDesglose';
            $dato->valor = (string) (count($pago_monto_desglose) > 0 ? $pago_monto_desglose[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_DesRechazo', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_rechazo = $xml->xpath("//*[local-name() = 'DesRechazo']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_DesRechazo';
            $dato->valor = (string) (count($pago_rechazo) > 0 ? $pago_rechazo[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Ventanilla', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_ventanilla = $xml->xpath("//*[local-name() = 'Ventanilla']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_Ventanilla';
            $dato->valor = (string) (count($pago_ventanilla) > 0 ? $pago_ventanilla[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_CodError', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_cod_error = $xml->xpath("//*[local-name() = 'CodError']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_CodError';
            $dato->valor = (string) (count($pago_cod_error) > 0 ? $pago_cod_error[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_DesError', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_des_error = $xml->xpath("//*[local-name() = 'DesError']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_DesError';
            $dato->valor = (string) (count($pago_des_error) > 0 ? $pago_des_error[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Mensaje', $etapa_id);
            if ($dato)
                $dato->delete();
            $pago_mensaje = $xml->xpath("//*[local-name() = 'Mensaje']/text()");
            $dato = new DatoSeguimiento();
            $dato->nombre = 'Solicitud_Mensaje';
            $dato->valor = (string) (count($pago_mensaje) > 0 ? $pago_mensaje[0] : "");
            $dato->etapa_id = $etapa_id;
            $dato->save();

            return 'ok';
        }
    }

    public function completado_generico() {
        if ($this->input->post('IdSol') && $this->input->post('estado')) {
            $id_solicitud = $this->input->post('IdSol');
            $estado = $this->input->post('estado');
            $registro_pago = Doctrine_Query::create()
                    ->from('Pago p')
                    ->where('p.id_solicitud = ?', $id_solicitud)
                    ->orderBy('p.id', 'DESC')
                    ->fetchOne();
            if (!empty($registro_pago)) {
                $this->generar_variables_pago_generico($id_solicitud, $registro_pago->id_etapa, $registro_pago->pasarela);
                if ($registro_pago->estado != $estado) {
                    $this->envia_email_pago_generico($registro_pago->id_etapa, $registro_pago->pasarela);
                }

                $registro_pago_new = new Pago();
                $registro_pago_new->id_solicitud = $id_solicitud;
                $registro_pago_new->id_tramite_interno = $registro_pago->id_tramite_interno;
                $registro_pago_new->id_tramite = $registro_pago->id_tramite;
                $registro_pago_new->id_etapa = $registro_pago->id_etapa;
                $registro_pago_new->pasarela = $registro_pago->pasarela;
                $registro_pago_new->estado = $estado;
                $registro_pago_new->fecha_actualizacion = date('d/m/Y H:i:s');
                $registro_pago_new->usuario = "Pagos Externos";
                $registro_pago_new->save();
            }
            echo 'OK';
        } else {
            redirect(site_url());
        }
    }

    public function error_generico() {
        if ($this->input->post('IdSol') && $this->input->post('estado')) {
            $id_solicitud = $this->input->post('IdSol');
            $estado = $this->input->post('estado');
            $registro_pago = Doctrine_Query::create()
                    ->from('Pago p')
                    ->where('p.id_solicitud = ?', $id_solicitud)
                    ->orderBy('p.id', 'DESC')
                    ->fetchOne();

            if (!empty($registro_pago)) {
                $registro_pago_new = new Pago();
                $registro_pago_new->id_solicitud = $id_solicitud;
                $registro_pago_new->id_tramite_interno = $registro_pago->id_tramite_interno;
                $registro_pago_new->id_tramite = $registro_pago->id_tramite;
                $registro_pago_new->id_etapa = $registro_pago->id_etapa;
                $registro_pago_new->pasarela = $registro_pago->pasarela;
                $registro_pago_new->estado = $estado;
                $registro_pago_new->fecha_actualizacion = date('d/m/Y H:i:s');
                $registro_pago_new->usuario = "Pagos Externos";
                $registro_pago_new->save();
            }

            echo 'OK';
        } else {
            redirect(site_url());
        }
    }

    public function rechazado_generico() {
        if ($this->input->post('IdSol') && $this->input->post('estado')) {
            $id_solicitud = $this->input->post('IdSol');
            $estado = $this->input->post('estado');
            $registro_pago = Doctrine_Query::create()
                    ->from('Pago p')
                    ->where('p.id_solicitud = ?', $id_solicitud)
                    ->orderBy('p.id', 'DESC')
                    ->fetchOne();

            if (!empty($registro_pago)) {
                $registro_pago_new = new Pago();
                $registro_pago_new->id_solicitud = $id_solicitud;
                $registro_pago_new->id_tramite_interno = $registro_pago->id_tramite_interno;
                $registro_pago_new->id_tramite = $registro_pago->id_tramite;
                $registro_pago_new->id_etapa = $registro_pago->id_etapa;
                $registro_pago_new->pasarela = $registro_pago->pasarela;
                $registro_pago_new->estado = $estado;
                $registro_pago_new->fecha_actualizacion = date('d/m/Y H:i:s');
                $registro_pago_new->usuario = "Pagos Externos";
                $registro_pago_new->save();
            }

            echo 'OK';
        } else {
            redirect(site_url());
        }
    }

    public function pendiente_generico() {
        if ($this->input->post('IdSol') && $this->input->post('estado')) {
            $id_solicitud = $this->input->post('IdSol');
            $estado = $this->input->post('estado');

            $registro_pago = Doctrine_Query::create()
                    ->from('Pago p')
                    ->where('p.id_solicitud = ?', $id_solicitud)
                    ->orderBy('p.id', 'DESC')
                    ->fetchOne();

            if (!empty($registro_pago)) {
                switch ($this->input->post('IdFormaPago')) {
                    case '2': $forma_pago = 'offline';
                        break;
                    case '3': $forma_pago = 'offline';
                        break;
                    case '10': $forma_pago = 'offline';
                        break;
                    default: $forma_pago = 'online';
                }
                if ($forma_pago == 'offline') {
                    $this->generar_variables_pago_generico($id_solicitud, $registro_pago->id_etapa, $registro_pago->pasarela);
                    if ($registro_pago->estado != $estado) {
                        $this->envia_email_pago_generico($registro_pago->id_etapa, $registro_pago->pasarela);
                    }
                }

                $registro_pago_new = new Pago();
                $registro_pago_new->id_solicitud = $id_solicitud;
                $registro_pago_new->id_tramite_interno = $registro_pago->id_tramite_interno;
                $registro_pago_new->id_tramite = $registro_pago->id_tramite;
                $registro_pago_new->id_etapa = $registro_pago->id_etapa;
                $registro_pago_new->pasarela = $registro_pago->pasarela;
                $registro_pago_new->estado = $estado;
                $registro_pago_new->fecha_actualizacion = date('d/m/Y H:i:s');
                $registro_pago_new->usuario = "Pagos Externos";
                $registro_pago_new->save();
            }

            echo 'OK';
        } else {
            redirect(site_url());
        }
    }

    public function envia_email_pago_generico($etapa_id, $id_pasarela, $url = false) {
        if ($url) {
            preg_match("/\/ejecutar\/([0-9]*)\/[0-9]*/", $url, $etapa_id);
            $etapa_id = $etapa_id[1];

            $dato = new DatoSeguimiento();
            $dato->nombre = 'url_formulario_pago';
            $dato->valor = (string) $url;
            $dato->etapa_id = $etapa_id;
            $dato->save();
        } else {
            $url = site_url();
        }

        $CI = & get_instance();
        $etapa = Doctrine_Query::create()
                ->from('Etapa e')
                ->where('e.id = ?', $etapa_id)
                ->fetchOne();

        $pasarela = Doctrine::getTable('PasarelaPagoGenerica')->find($id_pasarela);

        $cuenta = $etapa->Tramite->Proceso->Cuenta;

        $no_enviar = false;

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_LinkEstado', $etapa_id);
//        if ($dato)
//            $dato->delete();
//        $dato = new DatoSeguimiento();
//        $dato->nombre = 'Solicitud_LinkEstado';
//        $dato->valor = str_replace('ejecutar', 'ejecutar_pago', $url) . "/" . UsuarioSesion::usuario()->id;
//        $dato->etapa_id = $etapa_id;
//        $dato->save();

        $regla = new Regla($pasarela->tema_email_inicio);
        $tema = $regla->getExpresionParaOutput($etapa_id);
        $subject = $tema;

        $regla = new Regla($pasarela->cuerpo_email_inicio);
        $cuerpo = $regla->getExpresionParaOutput($etapa_id);
        $message = $cuerpo;

        if (empty($subject)) {
            $no_enviar = true;
        }

        if (!$no_enviar) {
            $email_tramite = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('email_tramite_inicial__e' . $etapa_id, $etapa_id);
            if ($email_tramite) {
                $campo = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($email_tramite->valor, $etapa_id);
                $destinatario = $campo->valor;
            }

            if (($destinatario) && (strlen($message) > 0)) {
                if (!$cuenta->correo_remitente) {
                    ($CI->config->item('main_domain') == '') ? $from = $cuenta->nombre . '@simple' : $from = $cuenta->nombre . '@' . $CI->config->item('main_domain');
                } else {
                    $from = $cuenta->correo_remitente;
                }

                //Enviar emails
                $data = new stdClass();
                $data->from = $from;
                $data->from_name = $cuenta->nombre_largo;
                $data->to = $destinatario;
                $data->subject = $subject;
                $data->message = $message;
                $data->cc = null;
                $data->bcc = null;
                $data->attach = null;
                $data_json = json_encode($data);
                $b64 = base64_encode($data_json);
                $comando = 'php index.php tasks/enviarmails enviar "' . $b64 . '" > /dev/null &';
                exec($comando);
            }
        }
    }

    function generar_variables_pago_generico($id_solicitud, $etapa_id, $pasarela) {
        if ($id_solicitud) {
            $pasarela_id = $this->input->post('IdPasarela');
            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            $pasarela = Doctrine::getTable('PasarelaPagoGenerica')->find($pasarela);

            $variable_evaluar = $pasarela->variable_evaluar;
            $variable_idsol = $pasarela->variable_idsol;
            $variable_idestado = $pasarela->variable_idestado;
            $codigo_operacion_soap = $pasarela->codigo_operacion_soap_consulta;

            $operacion = Doctrine_Query::create()
                    ->from('WsOperacion o')
                    ->where('o.codigo = ?', $codigo_operacion_soap)
                    ->fetchOne();

            $servicio = Doctrine::getTable('WsCatalogo')->find($operacion->catalogo_id);

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago_generico', $etapa_id);
            if ($dato)
                $dato->delete();

            $dato = new DatoSeguimiento();
            $dato->nombre = 'codigo_estado_solicitud_pago_generico';
            $dato->valor = '0';
            $dato->etapa_id = $etapa_id;
            $dato->save();

            $ci = get_instance();
            $ci->load->helper('soap_execute');
            soap_execute($etapa, $servicio, $operacion, $operacion->soap);

            $data = [];

            $error_servicio_pagos = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("ws_error", $etapa->id);

            if ($error_servicio_pagos) {
                //dio un error el consultar se pone en -1 la variable codigo_estado_solicitud_pago
                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago_generico', $etapa->id);
                if ($dato)
                    $dato->delete();

                $dato = new DatoSeguimiento();
                $dato->nombre = 'codigo_estado_solicitud_pago_generico';
                $dato->valor = '-1';
                $dato->etapa_id = $etapa->id;
                $dato->save();

                $data['estado'] = 'timeout';
            }
            else {
                $data['estado'] = 'ok';
            }

            $id_estado = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $variable_idestado), $etapa->id);

            $var_estado = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $variable_evaluar), $etapa->id);
            $data['data'] = $var_estado->valor;

            $secuencia = 0;
            $descripciones_estados_traza = json_decode($pasarela->descripciones_estados_traza);
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago_generico', $etapa->id);

            if (count($descripciones_estados_traza) > 0 && $dato) {
                foreach ($descripciones_estados_traza as $descripcion) {
                    if (trim($descripcion->codigo) != '' && trim($descripcion->valor) != '') {
                        if ((int) $descripcion->codigo == (int) $dato->valor) {
                            $descripcion_traza = $descripcion->valor;
                            break;
                        }
                    }
                }

                if (!$descripcion_traza) {
                    $descripcion_traza = 'Consulta de estado pasarela generica';
                }
            } else {
                $descripcion_traza = 'Consulta de estado pasarela generica';
            }

            if ($data['estado'] == 'ok') {
                $estado_mensaje = $data['data'][2][0];
                $estado_mensaje = explode('=', $estado_mensaje);

                if ($estado_mensaje[0] == 'OK') {
                    $pago_realizado = true;
                } else {
                    $pago_realizado = true;
                }
            } else {
                $pago_realizado = false;
            }
        }
    }

}

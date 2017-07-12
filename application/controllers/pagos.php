<?php

class Pagos extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->library('session');
    $this->load->helper('cookies_helper');
  }

  public function generar_ticket() {
    $etapa_id = $this->input->get('e');
    $id_solicitud = $this->input->get('t');
    $registro_de_pago = Doctrine_Query::create()
        ->from('Pago p')
        ->where('p.id_solicitud = ?', $id_solicitud)
        ->fetchOne();

    $pasarela = Doctrine_Query::create()
      ->from('PasarelaPagoAntel pa')
      ->where('pa.id = ?', $registro_de_pago->pasarela)
      ->fetchOne();

    $etapa = Doctrine_Query::create()
      ->from('Etapa e')
      ->where('e.id = ?', $etapa_id)
      ->fetchOne();

    $fecha_vencimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_FechaVto', $etapa_id);
    $monto_total = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_MontoTotal', $etapa_id);

    if($fecha_vencimiento && $monto_total) {
      $fecha_original = DateTime::createFromFormat('YmdHi', $fecha_vencimiento->valor);
      $fecha_vencimiento_nueva = $fecha_original->format('d/m/Y');

      $body = '<style>@media print{input, #importante{display: none;}}</style>';
      $body .= '<div style="max-width: 800px;margin: auto;font-family:sans;">';
      $body .= "<p>Para realizar el pago imprima este tal&oacute;n y pres&eacute;ntelo en cualquier sucursal de la red de cobranzas seleccionada. </p>";
      $body .= '<div id="importante" style="border: 1px solid #ccc; padding:0 10px; margin-bottom: 30px;"><p style="font-size: 0.9em;"><strong>IMPORTANTE:</strong> este ticket debe ser impreso en buena calidad, preferentemente en una impresora l&aacute;ser.</p></div>';
      $body .= '<p style="margin-bottom: 20px;"><strong>Fecha de vencimiento: '. $fecha_vencimiento_nueva .'</strong></p>';
      $body .= '<table border="0" cellpadding="10" cellspacing="0" height="35" width="100%" align="center">
													<tbody><tr class="texto1" bgcolor="#eeeeee" align="left">
														<td><span id="lblDatosCobro">Datos del Cobro</span></td>
														<td width="150" align="right"><span id="lblImporte" class="texto">Importe($U)</span></td>
													</tr>
                          <tr class="texto1_2" align="left">
														<td><span id="lblDetalle" class="texto" style="font-weight:bold;">'. $etapa->Tramite->Proceso->nombre .'</span></td>
														<td width="150" align="right"><strong>$&nbsp;</strong>
															<span id="lblImporteCont" style="font-weight:bold;">'. $monto_total->valor .'</span></td>
													</tr>
												</tbody></table>';

      $body .= '<br /><img src="'. COD_BARRAS_PASARELA_PAGO .'?o='. $pasarela->id_organismo .'&id='. $id_solicitud  .'" />';
      $body .= '<br /><input style="margin-top:20px;" type="button" class="btn btn-primary" value="Imprimir" onclick="javascript:window.print();">';
      $body .= '</div>';

      echo $body;
    }
    else {
      echo 'No se pudo generar el ticket de pago, por favor vuelva a intentarlo más tarde.';
    }
  }

  public function envia_email_inicio() {
    $this->envia_email_pago('inicio', false, $this->input->post('idSol'), $this->input->post('url'));
  }

  public function envia_email_inicio_generico() {
    $this->envia_email_pago_generico('inicio', false, $this->input->post('idPasarela'), $this->input->post('url'));
  }

  public function envia_email_pago($tipo, $etapa_id, $id_solicitud, $url=false) {
    if($url) {
      preg_match("/\/ejecutar\/([0-9]*)\/[0-9]*/", $url, $etapa_id);
      $etapa_id = $etapa_id[1];

      $dato = new DatoSeguimiento();
      $dato->nombre = 'url_formulario_pago';
      $dato->valor = (string)$url;
      $dato->etapa_id = $etapa_id;
      $dato->save();
    }
    else {
      $url = site_url();
    }

    $CI = & get_instance();
    $etapa = Doctrine_Query::create()
        ->from('Etapa e')
        ->where('e.id = ?', $etapa_id)
        ->fetchOne();

    $cuenta=$etapa->Tramite->Proceso->Cuenta;

    $registro_de_pago = Doctrine_Query::create()
        ->from('Pago p')
        ->where('p.id_solicitud = ?', $id_solicitud)
        ->fetchOne();

    $pasarela = Doctrine_Query::create()
      ->from('PasarelaPagoAntel pa')
      ->where('pa.id = ?', $registro_de_pago->pasarela)
      ->execute();
    $pasarela = $pasarela[0];

    $no_enviar = false;

    $accion_ejecutada = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_accion_ejecutada', $etapa_id);
    $accion = Doctrine::getTable('Accion')->find($accion_ejecutada->valor);

    switch($tipo) {
      case 'inicio':
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_LinkEstado', $etapa_id);
        if($dato)
          $dato->delete();
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_LinkEstado';
        //$dato->valor = '<a href="' . str_replace('ejecutar', 'ejecutar_pago', $url) . "/" . UsuarioSesion::usuario()->id . '">Ver el estado del pago</a>';
        $dato->valor = str_replace('ejecutar', 'ejecutar_pago', $url) . "/" . UsuarioSesion::usuario()->id;
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $regla=new Regla($accion->extra->tema_email_inicio);
        $tema = $regla->getExpresionParaOutput($etapa_id);
        $subject = $tema;

        $regla=new Regla($accion->extra->cuerpo_email_inicio);
        $cuerpo = $regla->getExpresionParaOutput($etapa_id);
        $message = $cuerpo;
        break;
      case 'pendiente':
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_LinkEstado', $etapa_id);
        if($dato)
          $dato->delete();
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_LinkEstado';
        //$dato->valor = '<a href="' . site_url() .'etapas\/ejecutar_pago\/'. $etapa_id . '\/0\/' . UsuarioSesion::usuario()->id . '">Ver el estado del pago</a>';
        $dato->valor = $url . "etapas/ejecutar_pago/" . $etapa_id . "/0/" . UsuarioSesion::usuario()->id;
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_LinkTicket', $etapa_id);
        if($dato)
          $dato->delete();
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_LinkTicket';
        //$dato->valor = '<a href="' . site_url() .'pagos\/generar_ticket?t='. $id_solicitud .'&e='. $etapa_id .'">Ticket de pago</a>';
        $dato->valor = site_url() .'pagos/generar_ticket?t='. $id_solicitud .'&e='. $etapa_id;
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $regla=new Regla($accion->extra->tema_email_pendiente);
        $tema = $regla->getExpresionParaOutput($etapa_id);
        $subject = $tema;

        $regla=new Regla($accion->extra->cuerpo_email_pendiente);
        $cuerpo = $regla->getExpresionParaOutput($etapa_id);
        $message = $cuerpo;
        break;
      case 'realizado':
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_LinkEstado', $etapa_id);
        if($dato)
          $dato->delete();
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_LinkEstado';
        //$dato->valor = '<a href="' . site_url() .'etapas\/ejecutar_pago\/'. $etapa_id . '\/0\/' . UsuarioSesion::usuario()->id . '">Ver el estado del pago</a>';
        $dato->valor = $url . "etapas/ejecutar_pago/" . $etapa_id . "/0/" . UsuarioSesion::usuario()->id;
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $regla=new Regla($accion->extra->tema_email_ok);
        $tema = $regla->getExpresionParaOutput($etapa_id);
        $subject = $tema;

        $regla=new Regla($accion->extra->cuerpo_email_ok);
        $cuerpo = $regla->getExpresionParaOutput($etapa_id);
        $message = $cuerpo;
        break;
      case 'timeout':
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_LinkEstado', $etapa_id);
        if($dato)
          $dato->delete();
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_LinkEstado';
        //$dato->valor = '<a href="' . site_url() .'etapas\/ejecutar_pago\/'. $etapa_id . '\/0\/' . UsuarioSesion::usuario()->id . '">Ver el estado del pago</a>';
        $dato->valor = $url . "etapas/ejecutar_pago/" . $etapa_id . "/0/" . UsuarioSesion::usuario()->id;
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $regla=new Regla($accion->extra->tema_email_timeout);
        $tema = $regla->getExpresionParaOutput($etapa_id);
        $subject = $tema;

        $regla=new Regla($accion->extra->cuerpo_email_timeout);
        $cuerpo = $regla->getExpresionParaOutput($etapa_id);
        $message = $cuerpo;
        break;
      default:
        $no_enviar = true;
    }

    if(empty($subject)) {
      $no_enviar = true;
    }

    if(!$no_enviar) {
      $email_tramite = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('email_tramite_inicial__e'.$etapa_id, $etapa_id);
      if($email_tramite) {
        $campo = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($email_tramite->valor, $etapa_id);
        $destinatario = $campo->valor;
      }

      if(($destinatario) && (strlen($message) > 0)) {
        if(!$cuenta->correo_remitente) {
          ($CI->config->item('main_domain') == '') ? $from = $cuenta->nombre.'@simple' : $from = $cuenta->nombre.'@'.$CI->config->item('main_domain');
        }
        else {
          $from = $cuenta->correo_remitente;
        }

        $CI->email->from($from, $cuenta->nombre_largo);
        $CI->email->to($destinatario);

        //$subject = utf8_decode($subject);
        //$message = utf8_decode($message);

        $CI->email->subject($subject);
        $CI->email->message($message);
        if (!$CI->email->send()){
            log_message('ERROR', "send email envia_email_pago: ".$CI->email->print_debugger());
        }
      }
    }
  }

  public function envia_email_pago_generico($tipo, $etapa_id, $id_pasarela, $url=false) {
    if($url) {
      preg_match("/\/ejecutar\/([0-9]*)\/[0-9]*/", $url, $etapa_id);
      $etapa_id = $etapa_id[1];

      $dato = new DatoSeguimiento();
      $dato->nombre = 'url_formulario_pago';
      $dato->valor = (string)$url;
      $dato->etapa_id = $etapa_id;
      $dato->save();
    }
    else {
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
    if($dato)
      $dato->delete();
    $dato = new DatoSeguimiento();
    $dato->nombre = 'Solicitud_LinkEstado';
    $dato->valor = str_replace('ejecutar', 'ejecutar_pago', $url) . "/" . UsuarioSesion::usuario()->id;
    $dato->etapa_id = $etapa_id;
    $dato->save();

    $regla=new Regla($pasarela->tema_email_inicio);
    $tema = $regla->getExpresionParaOutput($etapa_id);
    $subject = $tema;

    $regla=new Regla($pasarela->cuerpo_email_inicio);
    $cuerpo = $regla->getExpresionParaOutput($etapa_id);
    $message = $cuerpo;

    if(empty($subject)) {
      $no_enviar = true;
    }

    if(!$no_enviar) {
      $email_tramite = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('email_tramite_inicial__e'.$etapa_id, $etapa_id);
      if($email_tramite) {
        $campo = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($email_tramite->valor, $etapa_id);
        $destinatario = $campo->valor;
      }

      if(($destinatario) && (strlen($message) > 0)) {
        if(!$cuenta->correo_remitente) {
          ($CI->config->item('main_domain') == '') ? $from = $cuenta->nombre.'@simple' : $from = $cuenta->nombre.'@'.$CI->config->item('main_domain');
        }
        else {
          $from = $cuenta->correo_remitente;
        }

        $CI->email->from($from, $cuenta->nombre_largo);
        $CI->email->to($destinatario);

        $CI->email->subject($subject);
        $CI->email->message($message);
        if (!$CI->email->send()){
            log_message('ERROR', "send email envia_email_pago_generico: ".$CI->email->print_debugger());
        }
      }
    }
  }

  // -- Metodo para vuelta de pagos genericos
  public function control_generico() {
    $url_vuelta = $this->session->userdata('simple_bpm_gwp_redirect');
    $etapa_id = $this->session->userdata('id_etapa');

    if (empty($url_vuelta)) {
      redirect(site_url());
    }
    else {
      redirect($url_vuelta);
    }
  }

  // -- Metodo para vuelta de pagos antel
  public function control() {
    $url_vuelta = $this->session->userdata('simple_bpm_gwp_redirect');

    if (empty($url_vuelta)) {
      redirect(site_url());
    }
    else {
      $id_solicitud = $this->session->userdata('id_solicitud');
      $etapa_id = $this->session->userdata('id_etapa');

      $this->generar_variables_pago($id_solicitud, $etapa_id);

      redirect($url_vuelta);
    }
  }

  public function completado() {
    if($this->input->post('IdSol')) {
      $id_solicitud = $this->input->post('IdSol');

      $registro_pago = Doctrine_Query::create()
          ->from('Pago p')
          ->where('p.id_solicitud = ?', $id_solicitud)
          ->fetchOne();

      if(!empty($registro_pago)) {
        if($registro_pago->estado != 'realizado') {
          $estado = $this->generar_variables_pago($id_solicitud, $registro_pago->id_etapa);
          if($estado == 'ok')
            $this->envia_email_pago('realizado', $registro_pago->id_etapa, $id_solicitud);
        }

        $registro_pago->estado = 'realizado';
        $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
        $registro_pago->save();
      }

      echo 'OK';
    }
    else {
      redirect(site_url());
    }
  }

  public function error() {
    if($this->input->post('IdSol')) {
      $id_solicitud = $this->input->post('IdSol');
      $registro_pago = Doctrine_Query::create()
          ->from('Pago p')
          ->where('p.id_solicitud = ?', $id_solicitud)
          ->fetchOne();

      if(!empty($registro_pago)) {
        $registro_pago->estado = 'error';
        $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
        $registro_pago->save();
      }

      echo 'OK';
    }
    else {
      redirect(site_url());
    }
  }

  public function pendiente() {
    if($this->input->post('IdSol')) {
      $id_solicitud = $this->input->post('IdSol');

      $registro_pago = Doctrine_Query::create()
          ->from('Pago p')
          ->where('p.id_solicitud = ?', $id_solicitud)
          ->fetchOne();

      if(!empty($registro_pago)) {
        switch($this->input->post('IdFormaPago')) {
          case '2': $forma_pago = 'offline'; break;
          case '3': $forma_pago = 'offline'; break;
          case '10': $forma_pago = 'offline'; break;
          default: $forma_pago = 'online';
        }
        if($forma_pago == 'offline') {
          if($registro_pago->estado != 'pendiente') {
            $estado = $this->generar_variables_pago($id_solicitud, $registro_pago->id_etapa);
            if($estado == 'ok')
              $this->envia_email_pago('pendiente', $registro_pago->id_etapa, $id_solicitud);
          }
        }

        $registro_pago->estado = 'pendiente';
        $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
        $registro_pago->save();
      }

      echo 'OK';
    }
    else {
      redirect(site_url());
    }
  }

  public function rechazado() {
    if($this->input->post('IdSol')) {
      $id_solicitud = $this->input->post('IdSol');

      $registro_pago = Doctrine_Query::create()
          ->from('Pago p')
          ->where('p.id_solicitud = ?', $id_solicitud)
          ->fetchOne();

      if(!empty($registro_pago)) {
        $registro_pago->estado = 'rechazado';
        $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
        $registro_pago->save();
      }

      echo 'OK';
    }
    else {
      redirect(site_url());
    }
  }

  public function consulta_estado() {
    if($this->input->post('IdSol') && $this->input->post('IdTramite')) {
      $id_solicitud = $this->input->post('IdSol');
      $id_tramite = $this->input->post('IdTramite');
      $etapa_id = $this->input->post('IdEtapa');

      $existe_pago = Doctrine_Query::create()
          ->from('Pago p')
          ->where('p.id_solicitud = ?', $id_solicitud)
          ->fetchOne();

      $estado = [];

      if(!empty($existe_pago)) {
        switch($existe_pago->estado) {
          case 'realizado':
            $estado['estado'] = 'ok';
            $estado['titulo'] = 'Pago completado';
            $estado['mensaje'] = 'El pago se ha realizado con éxito.';

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
            if($dato)
              $dato->delete();

            $dato = new DatoSeguimiento();
            $dato->nombre = 'estado_solicitud_pago';
            $dato->valor = (string)$existe_pago->estado;
            $dato->etapa_id = $etapa_id;
            $dato->save();
            break;
          case 'rechazado':
            $estado['estado'] = 'error';
            $estado['titulo'] = 'Pago rechazado';
            $estado['mensaje'] = 'El pago ha sido rechazado por el sistema.';

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
            if($dato)
              $dato->delete();

            $dato = new DatoSeguimiento();
            $dato->nombre = 'estado_solicitud_pago';
            $dato->valor = (string)$existe_pago->estado;
            $dato->etapa_id = $etapa_id;
            $dato->save();

            break;
          case 'error':
            $estado['estado'] = 'error';
            $estado['titulo'] = 'Pago no completado';
            $estado['mensaje'] = 'Ha ocurrido un error al procesar su pago.';

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
            if($dato)
              $dato->delete();

            $dato = new DatoSeguimiento();
            $dato->nombre = 'estado_solicitud_pago';
            $dato->valor = (string)$existe_pago->estado;
            $dato->etapa_id = $etapa_id;
            $dato->save();

            break;
          case 'pendiente' || 'iniciado' || 'token_solicita':
            $clave_tramite = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(md5($existe_pago->id . '_clave_tramite_pasarela_pagos'), $existe_pago->id_etapa);

            $ws_body = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:con="http://tempuri.org/wsorg/Consultas">';
            if (!empty(SOAP_PASARELA_PAGO_CONSULTA)){
                if (SOAP_PASARELA_PAGO_CONSULTA == '1.1'){
                  $ws_body = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:con="http://tempuri.org/wsorg/Consultas">';
                }
            }
            $ws_body = $ws_body . '<soap:Header/>
               <soap:Body>
                  <con:ObtenerDatosTransaccion>
                     <con:pIdSolicitud>'. (integer)$id_solicitud .'</con:pIdSolicitud>
                     <con:pIdTramite>'. (integer)$id_tramite .'</con:pIdTramite>
                     <con:pClave>'. (string)$clave_tramite->valor .'</con:pClave>
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

            $pasarela = Doctrine_Query::create()
              ->from('PasarelaPagoAntel pa')
              ->where('pa.id = ?', $existe_pago->pasarela)
              ->execute();
            $pasarela = $pasarela[0];

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

            if($curl_errno > 0 || $http_code != 200) {
              $estado['estado'] = 'error';
              $estado['titulo'] = 'Ha ocurrido un error';
              $estado['mensaje'] = 'No es posible obtener el estado del pago, por favor, vuelva a intentarlo más tarde.';

              $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdEstado', $etapa_id);
              if($dato)
                $dato->delete();
              $dato = new DatoSeguimiento();
              $dato->nombre = 'Solicitud_IdEstado';
              $dato->valor = 'timeout';
              $dato->etapa_id = $etapa_id;
              $dato->save();

              log_message('error', "Web service SOAP Body:" . $ws_body . ' - response:' .$ws_response .' httpcode:' . $http_code . ' curlerrno:' . $curl_errno . ' curlerror:' . $curl_error);
            }
            else {
              $xml = new SimpleXMLElement($ws_response);
              $nuevo_estado = $xml->xpath("//*[local-name() = 'IdEstado']/text()");
              $nuevo_mensaje = $xml->xpath("//*[local-name() = 'Mensaje']/text()");
              $nuevo_mensaje = (string)$nuevo_mensaje[0];

              if($nuevo_mensaje == 'OK') {
                $nuevo_mensaje = '';
              }

              $this->generar_variables_pago($id_solicitud, $etapa_id);

              // -- Muestra mensaje y genera variable con el estado actual
              switch($nuevo_estado[0]) {
                case '1':
                  $registro_pago = Doctrine_Query::create()
                      ->from('Pago p')
                      ->where('p.id_solicitud = ?', $id_solicitud)
                      ->fetchOne();

                  if(!empty($registro_pago)) {
                    $registro_pago->estado = 'pendiente';
                    $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
                    $registro_pago->save();
                  }

                  if(empty($nuevo_mensaje)) {
                  	$estado['estado'] = 'alerta';
                  }
                  else {
                  	$estado['estado'] = 'error';
                  }

                  $estado['titulo'] = 'Pago no completado';
                  $estado['mensaje'] = $nuevo_mensaje;

                  $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
                  if($dato)
                    $dato->delete();

                  $dato = new DatoSeguimiento();
                  $dato->nombre = 'estado_solicitud_pago';
                  $dato->valor = 'pendiente';
                  $dato->etapa_id = $etapa_id;
                  $dato->save();
                  break;
                case '3':
                  $registro_pago = Doctrine_Query::create()
                      ->from('Pago p')
                      ->where('p.id_solicitud = ?', $id_solicitud)
                      ->fetchOne();

                  if(!empty($registro_pago)) {
                    $registro_pago->estado = 'error';
                    $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
                    $registro_pago->save();
                  }

                  if(empty($nuevo_mensaje)) {
                  	$estado['estado'] = 'alerta';
                  }
                  else {
                  	$estado['estado'] = 'error';
                  }

                  $estado['titulo'] = 'Pago rechazado';
                  $estado['mensaje'] = $nuevo_mensaje;

                  $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
                  if($dato)
                    $dato->delete();

                  $dato = new DatoSeguimiento();
                  $dato->nombre = 'estado_solicitud_pago';
                  $dato->valor = 'error';
                  $dato->etapa_id = $etapa_id;
                  $dato->save();
                  break;
                case '6':
                  $registro_pago = Doctrine_Query::create()
                      ->from('Pago p')
                      ->where('p.id_solicitud = ?', $id_solicitud)
                      ->fetchOne();

                  if(!empty($registro_pago)) {
                    //$registro_pago->estado = 'pendiente';
                    $registro_pago->estado = 'rc';
                    $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
                    $registro_pago->save();
                  }

                  if(empty($nuevo_mensaje)) {
                  	$estado['estado'] = 'alerta';
                  }
                  else {
                  	$estado['estado'] = 'error';
                  }

                  $estado['titulo'] = 'Pago no completado';
                  $estado['mensaje'] = $nuevo_mensaje;

                  $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
                  if($dato)
                    $dato->delete();

                  $dato = new DatoSeguimiento();
                  $dato->nombre = 'estado_solicitud_pago';
                  $dato->valor = 'pendiente';
                  $dato->etapa_id = $etapa_id;
                  $dato->save();
                  break;
                case '12':
                  $registro_pago = Doctrine_Query::create()
                      ->from('Pago p')
                      ->where('p.id_solicitud = ?', $id_solicitud)
                      ->fetchOne();

                  if(!empty($registro_pago)) {
                    $registro_pago->estado = 'error';
                    $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
                    $registro_pago->save();
                  }

                  if(empty($nuevo_mensaje)) {
                  	$estado['estado'] = 'alerta';
                  }
                  else {
                  	$estado['estado'] = 'error';
                  }

                  $estado['titulo'] = 'Error del sistema de pagos';
                  $estado['mensaje'] = $nuevo_mensaje;

                  $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
                  if($dato)
                    $dato->delete();

                  $dato = new DatoSeguimiento();
                  $dato->nombre = 'estado_solicitud_pago';
                  $dato->valor = 'error';
                  $dato->etapa_id = $etapa_id;
                  $dato->save();
                  break;
                case '16':
                  $registro_pago = Doctrine_Query::create()
                      ->from('Pago p')
                      ->where('p.id_solicitud = ?', $id_solicitud)
                      ->fetchOne();

                  if(!empty($registro_pago)) {
                    $registro_pago->estado = 'error';
                    $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
                    $registro_pago->save();
                  }

                  if(empty($nuevo_mensaje)) {
                  	$estado['estado'] = 'alerta';
                  }
                  else {
                  	$estado['estado'] = 'error';
                  }

                  $estado['titulo'] = 'Pago rechazado';
                  $estado['mensaje'] = $nuevo_mensaje;

                  $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
                  if($dato)
                    $dato->delete();

                  $dato = new DatoSeguimiento();
                  $dato->nombre = 'estado_solicitud_pago';
                  $dato->valor = 'error';
                  $dato->etapa_id = $etapa_id;
                  $dato->save();
                  break;
                case '9':
                  $registro_pago = Doctrine_Query::create()
                      ->from('Pago p')
                      ->where('p.id_solicitud = ?', $id_solicitud)
                      ->fetchOne();

                  if(!empty($registro_pago)) {
                    $registro_pago->estado = 'realizado';
                    $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
                    $registro_pago->save();
                  }

                  $estado['estado'] = 'ok';
                  $estado['titulo'] = 'Pago completado';
                  $estado['mensaje'] = 'El pago se ha realizado satisfactoriamente.';

                  $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
                  if($dato)
                    $dato->delete();

                  $dato = new DatoSeguimiento();
                  $dato->nombre = 'estado_solicitud_pago';
                  $dato->valor = 'realizado';
                  $dato->etapa_id = $etapa_id;
                  $dato->save();
                  break;
                case '99':
                  $registro_pago = Doctrine_Query::create()
                      ->from('Pago p')
                      ->where('p.id_solicitud = ?', $id_solicitud)
                      ->fetchOne();

                  if(!empty($registro_pago)) {
                    $registro_pago->estado = 'error';
                    $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
                    $registro_pago->save();
                  }

                  if(empty($nuevo_mensaje)) {
                  	$estado['estado'] = 'alerta';
                  }
                  else {
                  	$estado['estado'] = 'error';
                  }

                  $estado['titulo'] = 'Pago rechazado';
                  $estado['mensaje'] = $nuevo_mensaje;

                  $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
                  if($dato)
                    $dato->delete();

                  $dato = new DatoSeguimiento();
                  $dato->nombre = 'estado_solicitud_pago';
                  $dato->valor = 'rechazado';
                  $dato->etapa_id = $etapa_id;
                  $dato->save();
                  break;
                default:
                  $registro_pago = Doctrine_Query::create()
                      ->from('Pago p')
                      ->where('p.id_solicitud = ?', $id_solicitud)
                      ->fetchOne();

                  if(!empty($registro_pago)) {
                    $registro_pago->estado = 'error';
                    $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
                    $registro_pago->save();
                  }

                  if(empty($nuevo_mensaje)) {
                  	$estado['estado'] = 'alerta';
                  }
                  else {
                  	$estado['estado'] = 'error';
                  }

                  $estado['titulo'] = 'Pago no completado';
                  $estado['mensaje'] = $nuevo_mensaje;

                  $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
                  if($dato)
                    $dato->delete();

                  $dato = new DatoSeguimiento();
                  $dato->nombre = 'estado_solicitud_pago';
                  $dato->valor = 'error';
                  $dato->etapa_id = $etapa_id;
                  $dato->save();
              }
            }
            break;
          default:
            $estado['estado'] = 'error';
            $estado['titulo'] = 'Pago no realizado';
            $estado['mensaje'] = 'El pago no existe o aún no se ha procesado.';

            $dato = new DatoSeguimiento();
            $dato->nombre = 'estado_solicitud_pago';
            $dato->valor = (string)$existe_pago->estado;
            $dato->etapa_id = $etapa_id;
            $dato->save();
        }
      }
      else {
        $estado = [];
        $estado['estado'] = 'error';
        $estado['titulo'] = 'Pago no realizado';
        $estado['mensaje'] = 'El pago no existe o aún no se ha procesado.';
      }
      echo json_encode($estado);
    }
    else {
      $estado = [];
      $estado['estado'] = 'error';
      $estado['titulo'] = 'Pago no realizado';
      $estado['mensaje'] = 'Ha ocurrido un error, vuelva a intentarlo más tarde.';
    }
  }

  public function consulta_estado_directo_generico() {
    if($this->input->post('IdSol')) {
      $id_solicitud = $this->input->post('IdSol');
      $etapa_id = $this->input->post('IdEtapa');
      $pasarela_id = $this->input->post('IdPasarela');
      $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
      $pasarela = Doctrine::getTable('PasarelaPagoGenerica')->find($pasarela_id);

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

      if($error_servicio_pagos) {
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
      $pago = Doctrine_Query::create()
                  ->from('Pago p')
                  ->where('p.pasarela = ? AND p.id_solicitud = ?', array($pasarela_id, $id_solicitud))
                  ->fetchOne();

      if(!$pago) {
        $pago = new Pago();
        $pago->id_tramite = 0;
        $pago->id_tramite_interno = $etapa->tramite_id;
        $pago->id_etapa = $etapa_id;
        $pago->id_solicitud = $id_solicitud;
        $pago->estado = (!$id_estado ? '' : $id_estado->valor);
        $pago->fecha_actualizacion = date('d/m/Y H:i');
        $pago->pasarela = $pasarela_id;
        $pago->save();
      }
      else {
        $pago->estado = (!$id_estado ? '' : $id_estado->valor);
        $pago->fecha_actualizacion = date('d/m/Y H:i');
        $pago->save();
      }

      $var_estado = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $variable_evaluar), $etapa->id);
      $data['data'] = $var_estado->valor;
      echo json_encode($data);
    }
  }

  public function consulta_estado_directo() {
    if($this->input->post('IdSol')) {
      $id_solicitud = $this->input->post('IdSol');
      $etapa_id = $this->input->post('IdEtapa');

      $estado = [];
      $registro_de_pago = Doctrine_Query::create()
          ->from('Pago p')
          ->where('p.id_solicitud = ?', $id_solicitud)
          ->andWhere('p.id_tramite > ?', 0)
          ->fetchOne();

      $clave_tramite = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(md5($registro_de_pago->id . '_clave_tramite_pasarela_pagos'), $registro_de_pago->id_etapa);

      $ws_body = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:con="http://tempuri.org/wsorg/Consultas">';
      if (!empty(SOAP_PASARELA_PAGO_CONSULTA)){
          if (SOAP_PASARELA_PAGO_CONSULTA == '1.1'){
            $ws_body = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:con="http://tempuri.org/wsorg/Consultas">';
          }
      }
      $ws_body = $ws_body . '<soap:Header/>
         <soap:Body>
            <con:ObtenerDatosTransaccion>
               <con:pIdSolicitud>'. $id_solicitud .'</con:pIdSolicitud>
               <con:pIdTramite>'. $registro_de_pago->id_tramite .'</con:pIdTramite>
               <con:pClave>'. $clave_tramite->valor .'</con:pClave>
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

      $pasarela = Doctrine_Query::create()
        ->from('PasarelaPagoAntel pa')
        ->where('pa.id = ?', $registro_de_pago->pasarela)
        ->execute();
      $pasarela = $pasarela[0];

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

      //log_message('error', 'pasarela ws consulta: ' . $ws_body . ' ' . $curl_error .'-'. $curl_errno.  ' MENSAJE WS: ' . $ws_response . ' HTTPCODE: ' .$http_code);
      if($curl_errno > 0 || $http_code != 200) {
        log_message('error', 'pasarela ws consulta: ' . ' ' . $curl_error .'-'. $curl_errno.  ' MENSAJE WS: ' . $ws_response . ' HTTPCODE: ' .$http_code);

        $estado['estado'] = 'timeout';
        $estado['forma_pago'] = '';
        $estado['titulo'] = MENSAJE_PAGO_TIMEOUT_TITULO;
        $estado['mensaje'] = MENSAJE_PAGO_TIMEOUT;

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdEstado', $etapa_id);
        if($dato)
          $dato->delete();
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_IdEstado';
        $dato->valor = 'timeout';
        $dato->etapa_id = $etapa_id;
        $dato->save();

        //dio un error el consultar se pone en -1 la variable codigo_estado_solicitud_pago
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago', $etapa_id);
        if ($dato)
          $dato->delete();
        $dato = new DatoSeguimiento();
        $dato->nombre = 'codigo_estado_solicitud_pago';
        $dato->valor = '-1';
        $dato->etapa_id = $etapa_id;
        $dato->save();

         $this->envia_email_pago('timeout', $etapa_id, $id_solicitud);
      }
      else {
        $xml = new SimpleXMLElement($ws_response);
        $nuevo_estado = $xml->xpath("//*[local-name() = 'IdEstado']/text()");
        $nuevo_mensaje = $xml->xpath("//*[local-name() = 'Mensaje']/text()");
        $codigo_forma_pago = $xml->xpath("//*[local-name() = 'IdFormaPago']/text()");

        switch($codigo_forma_pago[0]) {
          case '2': $forma_pago = 'offline'; break;
          case '3': $forma_pago = 'offline'; break;
          case '10': $forma_pago = 'offline'; break;
          default: $forma_pago = 'online';
        }

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
        if($dato)
          $dato->delete();
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_IdEstado';
        $dato->valor = (string)$pago_estado[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Fecha', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_fecha = $xml->xpath("//*[local-name() = 'Fecha']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_Fecha';
        $dato->valor = (string)$pago_fecha[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Transaccion', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_transaccion = $xml->xpath("//*[local-name() = 'IdTransaccion']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_Transaccion';
        $dato->valor = (string)$pago_transaccion[0];
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
        if($dato)
          $dato->delete();
        $pago_autorizacion = $xml->xpath("//*[local-name() = 'Autorizacion']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_Autorizacion';
        $dato->valor = (string)$pago_autorizacion[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdFormaPago', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_forma = $xml->xpath("//*[local-name() = 'IdFormaPago']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_IdFormaPago';
        $dato->valor = (string)$pago_forma[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_FechaConciliacion', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_concilia = $xml->xpath("//*[local-name() = 'FechaConciliacion']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_FechaConciliacion';
        $dato->valor = (string)$pago_concilia[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdTasa', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_id_tasa = $xml->xpath("//*[local-name() = 'IdTasa']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_IdTasa';
        $dato->valor = (string)$pago_id_tasa[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_ValorTasa', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_valor_tasa = $xml->xpath("//*[local-name() = 'ValorTasa']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_ValorTasa';
        $dato->valor = (string)$pago_valor_tasa[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_MontoTotal', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_monto = $xml->xpath("//*[local-name() = 'MontoTotal']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_MontoTotal';
        $dato->valor = (string)$pago_monto[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdTramite', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_id_tramite = $xml->xpath("//*[local-name() = 'IdTramite']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_IdTramite';
        $dato->valor = (string)$pago_id_tramite[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_ImporteTasa1', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_tasa1 = $xml->xpath("//*[local-name() = 'ImporteTasa1']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_ImporteTasa1';
        $dato->valor = (string)$pago_tasa1[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_ImporteTasa2', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_tasa2 = $xml->xpath("//*[local-name() = 'ImporteTasa2']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_ImporteTasa2';
        $dato->valor = (string)$pago_tasa2[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_ImporteTasa3', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_tasa3 = $xml->xpath("//*[local-name() = 'ImporteTasa3']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_ImporteTasa3';
        $dato->valor = (string)$pago_tasa3[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Cantidades', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_cantidades = $xml->xpath("//*[local-name() = 'Cantidades']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_Cantidades';
        $dato->valor = (string)$pago_cantidades[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_FechaVto', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_fecha_vto = $xml->xpath("//*[local-name() = 'FechaVto']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_FechaVto';
        $dato->valor = (string)$pago_fecha_vto[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_CodDesglose', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_desglose = $xml->xpath("//*[local-name() = 'CodDesglose']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_CodDesglose';
        $dato->valor = (string)$pago_desglose[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_MontoDesglose', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_monto_desglose = $xml->xpath("//*[local-name() = 'MontoDesglose']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_MontoDesglose';
        $dato->valor = (string)$pago_monto_desglose[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_DesRechazo', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_rechazo = $xml->xpath("//*[local-name() = 'DesRechazo']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_DesRechazo';
        $dato->valor = (string)$pago_rechazo[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Ventanilla', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_ventanilla = $xml->xpath("//*[local-name() = 'Ventanilla']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_Ventanilla';
        $dato->valor = (string)$pago_ventanilla[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_CodError', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_cod_error = $xml->xpath("//*[local-name() = 'CodError']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_CodError';
        $dato->valor = (string)$pago_cod_error[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_DesError', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_des_error = $xml->xpath("//*[local-name() = 'DesError']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_DesError';
        $dato->valor = (string)$pago_des_error[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Mensaje', $etapa_id);
        if($dato)
          $dato->delete();
        $pago_mensaje = $xml->xpath("//*[local-name() = 'Mensaje']/text()");
        $dato = new DatoSeguimiento();
        $dato->nombre = 'Solicitud_Mensaje';
        $dato->valor = (string)$pago_mensaje[0];
        $dato->etapa_id = $etapa_id;
        $dato->save();

        // -- Muestra mensaje y genera variable con el estado actual
        switch($nuevo_estado[0]) {
          case '1':
            $registro_pago = Doctrine_Query::create()
                ->from('Pago p')
                ->where('p.id_solicitud = ?', $id_solicitud)
                ->fetchOne();

            if(!empty($registro_pago)) {
              $registro_pago->estado = 'pendiente';
              $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
              $registro_pago->save();
            }

            $estado['estado'] = 'pendiente';
            $estado['forma_pago'] = $forma_pago;
            $estado['titulo'] = MENSAJE_PAGO_PENDIENTE_TITULO;
            $estado['mensaje_1'] = MENSAJE_PAGO_PENDIENTE_1;
            $estado['mensaje_2'] = MENSAJE_PAGO_PENDIENTE_2;

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
            if($dato)
              $dato->delete();

            $dato = new DatoSeguimiento();
            $dato->nombre = 'estado_solicitud_pago';
            $dato->valor = 'pendiente';
            $dato->etapa_id = $etapa_id;
            $dato->save();
            break;
          case '3':
            $registro_pago = Doctrine_Query::create()
                ->from('Pago p')
                ->where('p.id_solicitud = ?', $id_solicitud)
                ->fetchOne();

            if(!empty($registro_pago)) {
              $registro_pago->estado = 'error';
              $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
              $registro_pago->save();
            }

            $estado['estado'] = 'error';
            $estado['forma_pago'] = $forma_pago;
            $estado['titulo'] = MENSAJE_PAGO_RECHAZADO_TITULO;
            $estado['mensaje'] = MENSAJE_PAGO_RECHAZADO;

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
            if($dato)
              $dato->delete();

            $dato = new DatoSeguimiento();
            $dato->nombre = 'estado_solicitud_pago';
            $dato->valor = 'error';
            $dato->etapa_id = $etapa_id;
            $dato->save();
            break;
          case '6':
            $registro_pago = Doctrine_Query::create()
                ->from('Pago p')
                ->where('p.id_solicitud = ?', $id_solicitud)
                ->fetchOne();

            if(!empty($registro_pago)) {
              $estado_anterior = $registro_pago->estado;
              $registro_pago->estado = 'rc';
              $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
              $registro_pago->save();
            }

            $estado['estado'] = 'rc';
            $estado['forma_pago'] = $forma_pago;
            $estado['titulo'] = MENSAJE_PAGO_RC_TITULO;
            $estado['mensaje'] = MENSAJE_PAGO_RC;

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
            if($dato)
              $dato->delete();

            $dato = new DatoSeguimiento();
            $dato->nombre = 'estado_solicitud_pago';
            $dato->valor = 'rc';
            $dato->etapa_id = $etapa_id;
            $dato->save();

            if($forma_pago == 'offline') {
              if($estado_anterior != 'rc') {
                $this->envia_email_pago('pendiente', $etapa_id, $id_solicitud);
              }
            }
            break;
          case '9':
            $registro_pago = Doctrine_Query::create()
                ->from('Pago p')
                ->where('p.id_solicitud = ?', $id_solicitud)
                ->fetchOne();

            if(!empty($registro_pago)) {
              $estado_anterior = $registro_de_pago->estado;
              $registro_pago->estado = 'realizado';
              $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
              $registro_pago->save();
            }

            $estado['estado'] = 'ok';
            $estado['forma_pago'] = $forma_pago;
            $estado['titulo'] = MENSAJE_PAGO_OK_TITULO;
            $estado['mensaje'] = MENSAJE_PAGO_OK;

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
            if($dato)
              $dato->delete();

            $dato = new DatoSeguimiento();
            $dato->nombre = 'estado_solicitud_pago';
            $dato->valor = 'realizado';
            $dato->etapa_id = $etapa_id;
            $dato->save();

            if($estado_anterior != 'realizado') {
              $this->envia_email_pago('realizado', $etapa_id, $id_solicitud);
            }
            break;
          case '12':
            $registro_pago = Doctrine_Query::create()
                ->from('Pago p')
                ->where('p.id_solicitud = ?', $id_solicitud)
                ->fetchOne();

            if(!empty($registro_pago)) {
              $registro_pago->estado = 'error';
              $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
              $registro_pago->save();
            }

            $estado['estado'] = 'error';
            $estado['forma_pago'] = $forma_pago;
            $estado['titulo'] = MENSAJE_PAGO_ERROR_TITULO;
            $estado['mensaje'] = MENSAJE_PAGO_ERROR;

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
            if($dato)
              $dato->delete();

            $dato = new DatoSeguimiento();
            $dato->nombre = 'estado_solicitud_pago';
            $dato->valor = 'error';
            $dato->etapa_id = $etapa_id;
            $dato->save();
            break;
          case '16':
            $registro_pago = Doctrine_Query::create()
                ->from('Pago p')
                ->where('p.id_solicitud = ?', $id_solicitud)
                ->fetchOne();

            if(!empty($registro_pago)) {
              $registro_pago->estado = 'Reversado';
              $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
              $registro_pago->save();
            }

            $estado['estado'] = 'reversado';
            $estado['forma_pago'] = $forma_pago;
            $estado['titulo'] = MENSAJE_PAGO_REVERSADO_TITULO;
            $estado['mensaje_1'] = MENSAJE_PAGO_REVERSADO_1;
            $estado['mensaje_2'] = MENSAJE_PAGO_REVERSADO_2;

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
            if($dato)
              $dato->delete();

            $dato = new DatoSeguimiento();
            $dato->nombre = 'estado_solicitud_pago';
            $dato->valor = 'reversado';
            $dato->etapa_id = $etapa_id;
            $dato->save();
            break;
          case '99':
            $registro_pago = Doctrine_Query::create()
                ->from('Pago p')
                ->where('p.id_solicitud = ?', $id_solicitud)
                ->fetchOne();

            if(!empty($registro_pago)) {
              $registro_pago->estado = 'error';
              $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
              $registro_pago->save();
            }

            $estado['estado'] = 'error';
            $estado['forma_pago'] = $forma_pago;
            $estado['titulo'] = MENSAJE_PAGO_RECHAZADO_TITULO;
            $estado['mensaje'] = MENSAJE_PAGO_RECHAZADO;

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
            if($dato)
              $dato->delete();

            $dato = new DatoSeguimiento();
            $dato->nombre = 'estado_solicitud_pago';
            $dato->valor = 'rechazado';
            $dato->etapa_id = $etapa_id;
            $dato->save();
            break;
          default:
            $registro_pago = Doctrine_Query::create()
                ->from('Pago p')
                ->where('p.id_solicitud = ?', $id_solicitud)
                ->fetchOne();

            if(!empty($registro_pago)) {
              $registro_pago->estado = 'error';
              $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
              $registro_pago->save();
            }

            $estado['estado'] = 'error';
            $estado['forma_pago'] = $forma_pago;
            $estado['titulo'] = MENSAJE_PAGO_ERROR_TITULO;
            $estado['mensaje'] = MENSAJE_PAGO_ERROR;

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
            if($dato)
              $dato->delete();

            $dato = new DatoSeguimiento();
            $dato->nombre = 'estado_solicitud_pago';
            $dato->valor = 'error';
            $dato->etapa_id = $etapa_id;
            $dato->save();
        }
      }

      echo json_encode($estado);
    }
    else {
      $estado = [];
      $estado['estado'] = 'error';
      $estado['forma_pago'] = '';
      $estado['titulo'] = 'Pago no realizado';
      $estado['mensaje'] = 'Ha ocurrido un error, vuelva a intentarlo más tarde.';
    }
  }

  function limpiar_sesion() {
    $etapa_id = $this->input->post('etapa_id');

    $id_sol =  Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_sol_pasarela_pagos', $etapa_id);
    $token =  Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('token_pasarela_pagos', $etapa_id);
    $codigo =  Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago', $etapa_id);

    $codigo->valor = '1';
    $codigo->save();

    $id_sol->delete();
    $token->delete();

    echo json_encode('OK');
  }

  function limpiar_sesion_generico() {
    $etapa_id = $this->input->post('etapa_id');
    $variable_idsol = $this->input->post('variable_idsol');

    $codigo =  Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago_generico', $etapa_id);
    $codigo->delete();

    $id_sol = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_idsol), $etapa_id);
    $id_sol->delete();

    echo json_encode('OK');
  }

  function generar_variables_pago($id_solicitud, $etapa_id) {
    $registro_de_pago = Doctrine_Query::create()
        ->from('Pago p')
        ->where('p.id_solicitud = ?', $id_solicitud)
        ->fetchOne();

    $clave_tramite = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(md5($registro_de_pago->id . '_clave_tramite_pasarela_pagos'), $registro_de_pago->id_etapa);

    $ws_body = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:con="http://tempuri.org/wsorg/Consultas">';
    if (!empty(SOAP_PASARELA_PAGO_CONSULTA)){
        if (SOAP_PASARELA_PAGO_CONSULTA == '1.1'){
          $ws_body = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:con="http://tempuri.org/wsorg/Consultas">';
        }
    }
    $ws_body = $ws_body . '<soap:Header/>
       <soap:Body>
          <con:ObtenerDatosTransaccion>
             <con:pIdSolicitud>'. $id_solicitud .'</con:pIdSolicitud>
             <con:pIdTramite>'. $registro_de_pago->id_tramite .'</con:pIdTramite>
             <con:pClave>'. $clave_tramite->valor .'</con:pClave>
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

    $pasarela = Doctrine_Query::create()
      ->from('PasarelaPagoAntel pa')
      ->where('pa.id = ?', $registro_de_pago->pasarela)
      ->execute();
    $pasarela = $pasarela[0];

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

    if($curl_errno > 0 || $http_code != 200) {
      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdEstado', $etapa_id);
      if($dato)
        $dato->delete();
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_IdEstado';
      $dato->valor = 'timeout';
      $dato->etapa_id = $etapa_id;
      $dato->save();

      //dio un error el consultar se pone en -1 la variable codigo_estado_solicitud_pago
      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago', $etapa_id);
      if ($dato)
        $dato->delete();
      $dato = new DatoSeguimiento();
      $dato->nombre = 'codigo_estado_solicitud_pago';
      $dato->valor = '-1';
      $dato->etapa_id = $etapa_id;
      $dato->save();

      return 'error';
    }
    else {
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
      if($dato)
        $dato->delete();
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_IdEstado';
      $dato->valor = (string)$pago_estado[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Fecha', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_fecha = $xml->xpath("//*[local-name() = 'Fecha']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_Fecha';
      $dato->valor = (string)$pago_fecha[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Transaccion', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_transaccion = $xml->xpath("//*[local-name() = 'IdTransaccion']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_Transaccion';
      $dato->valor = (string)$pago_transaccion[0];
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
      if($dato)
        $dato->delete();
      $pago_autorizacion = $xml->xpath("//*[local-name() = 'Autorizacion']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_Autorizacion';
      $dato->valor = (string)$pago_autorizacion[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdFormaPago', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_forma = $xml->xpath("//*[local-name() = 'IdFormaPago']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_IdFormaPago';
      $dato->valor = (string)$pago_forma[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_FechaConciliacion', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_concilia = $xml->xpath("//*[local-name() = 'FechaConciliacion']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_FechaConciliacion';
      $dato->valor = (string)$pago_concilia[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdTasa', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_id_tasa = $xml->xpath("//*[local-name() = 'IdTasa']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_IdTasa';
      $dato->valor = (string)$pago_id_tasa[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_ValorTasa', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_valor_tasa = $xml->xpath("//*[local-name() = 'ValorTasa']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_ValorTasa';
      $dato->valor = (string)$pago_valor_tasa[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_MontoTotal', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_monto = $xml->xpath("//*[local-name() = 'MontoTotal']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_MontoTotal';
      $dato->valor = (string)$pago_monto[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdTramite', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_id_tramite = $xml->xpath("//*[local-name() = 'IdTramite']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_IdTramite';
      $dato->valor = (string)$pago_id_tramite[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_ImporteTasa1', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_tasa1 = $xml->xpath("//*[local-name() = 'ImporteTasa1']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_ImporteTasa1';
      $dato->valor = (string)$pago_tasa1[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_ImporteTasa2', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_tasa2 = $xml->xpath("//*[local-name() = 'ImporteTasa2']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_ImporteTasa2';
      $dato->valor = (string)$pago_tasa2[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_ImporteTasa3', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_tasa3 = $xml->xpath("//*[local-name() = 'ImporteTasa3']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_ImporteTasa3';
      $dato->valor = (string)$pago_tasa3[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Cantidades', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_cantidades = $xml->xpath("//*[local-name() = 'Cantidades']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_Cantidades';
      $dato->valor = (string)$pago_cantidades[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_FechaVto', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_fecha_vto = $xml->xpath("//*[local-name() = 'FechaVto']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_FechaVto';
      $dato->valor = (string)$pago_fecha_vto[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_CodDesglose', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_desglose = $xml->xpath("//*[local-name() = 'CodDesglose']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_CodDesglose';
      $dato->valor = (string)$pago_desglose[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_MontoDesglose', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_monto_desglose = $xml->xpath("//*[local-name() = 'MontoDesglose']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_MontoDesglose';
      $dato->valor = (string)$pago_monto_desglose[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_DesRechazo', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_rechazo = $xml->xpath("//*[local-name() = 'DesRechazo']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_DesRechazo';
      $dato->valor = (string)$pago_rechazo[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Ventanilla', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_ventanilla = $xml->xpath("//*[local-name() = 'Ventanilla']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_Ventanilla';
      $dato->valor = (string)$pago_ventanilla[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_CodError', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_cod_error = $xml->xpath("//*[local-name() = 'CodError']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_CodError';
      $dato->valor = (string)$pago_cod_error[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_DesError', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_des_error = $xml->xpath("//*[local-name() = 'DesError']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_DesError';
      $dato->valor = (string)$pago_des_error[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_Mensaje', $etapa_id);
      if($dato)
        $dato->delete();
      $pago_mensaje = $xml->xpath("//*[local-name() = 'Mensaje']/text()");
      $dato = new DatoSeguimiento();
      $dato->nombre = 'Solicitud_Mensaje';
      $dato->valor = (string)$pago_mensaje[0];
      $dato->etapa_id = $etapa_id;
      $dato->save();

      return 'ok';
    }
  }
}

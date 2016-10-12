<?php

class Pagos extends MY_Controller {

  public function __construct() {
    parent::__construct();

    $this->load->helper('cookies_helper');
  }

  public function control() {
    if($_COOKIE['simple_bpm_gwp_redirect']) {
      $url_vuelta = base64_decode($_COOKIE['simple_bpm_gwp_redirect']);

      try {
        //set_cookie('simple_bpm_gwp_redirect', '', -1, '/', HOST_SISTEMA_DOMINIO);
      }
      catch(Exception $e) {
        redirect($url_vuelta);
      }

      redirect($url_vuelta);
    }
    else {
      redirect(site_url());
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

      if(!empty($existe_pago)) {
        $estado = [];

        switch($existe_pago->estado) {
          case 'realizado':
            $estado['estado'] = 'ok';
            $estado['titulo'] = 'Pago completado';
            $estado['mensaje'] = 'El pago se ha realizado con éxito.';

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

            $dato = new DatoSeguimiento();
            $dato->nombre = 'estado_solicitud_pago';
            $dato->valor = (string)$existe_pago->estado;
            $dato->etapa_id = $etapa_id;
            $dato->save();

            break;
          case 'pendiente' || 'iniciado' || 'token_solicita':
            $clave_tramite = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(md5($existe_pago->id . '_clave_tramite_pasarela_pagos'), $existe_pago->id_etapa);

            $ws_body = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:con="http://tempuri.org/wsorg/Consultas">
               <soap:Header/>
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
                "Content-length: ".strlen($ws_body)
            );

            $pasarela = Doctrine::getTable('PasarelaPagoAntel')->find($existe_pago->pasarela);

            $ws_do = curl_init();
            curl_setopt($ws_do, CURLOPT_URL, WS_PASARELA_PAGO_CONSULTA);
            curl_setopt($ws_do, CURLOPT_CONNECTTIMEOUT, PASARELA_PAGO_TIMEOUT_CONEXION);
            curl_setopt($ws_do, CURLOPT_TIMEOUT,        PASARELA_PAGO_TIMEOUT_RESPUESTA);
            curl_setopt($ws_do, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ws_do, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ws_do, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ws_do, CURLOPT_POST,           true);
            curl_setopt($ws_do, CURLOPT_SSLVERSION,     3);
            curl_setopt($ws_do, CURLOPT_SSLCERT, UBICACION_CERTIFICADOS_PASARELA.$pasarela->certificado);
            curl_setopt($ws_do, CURLOPT_SSLKEY,        UBICACION_CERTIFICADOS_PASARELA.$pasarela->clave_certificado);
            curl_setopt($ws_do, CURLOPT_SSLCERTPASSWD, $pasarela->pass_clave_certificado);
            curl_setopt($ws_do, CURLOPT_ENCODING,       'gzip');
            curl_setopt($ws_do, CURLOPT_POSTFIELDS,     $ws_body);
            curl_setopt($ws_do, CURLOPT_HTTPHEADER,     $ws_header);

            $ws_response = curl_exec($ws_do);
            $curl_errno = curl_errno($ws_do); // -- Codigo de error
            $curl_error = curl_error($ws_do); // -- Descripcion del error
            $http_code = curl_getinfo($ws_do, CURLINFO_HTTP_CODE); // -- Codigo respuesta HTTP

            curl_close($ws_do);

            if($curl_errno > 0) {
              $estado['estado'] = 'error';
              $estado['titulo'] = 'Ha ocurrido un error';
              $estado['mensaje'] = 'No es posible obtener el estado del pago, por favor, vuelva a intentarlo más tarde.';
            }
            else {
              $xml = new SimpleXMLElement($ws_response);
              $nuevo_estado = $xml->xpath("//*[local-name() = 'IdEstado']/text()");
              $nuevo_mensaje = $xml->xpath("//*[local-name() = 'Mensaje']/text()");

              // -- Muestra mensaje y genera variable con el estado actual
              switch($nuevo_estado[0]) {
                case '6':
                  $registro_pago = Doctrine_Query::create()
                      ->from('Pago p')
                      ->where('p.id_solicitud = ?', $id_solicitud)
                      ->fetchOne();

                  if(!empty($registro_pago)) {
                    $registro_pago->estado = 'pendiente';
                    $registro_pago->fecha_actualizacion = date('d/m/Y H:i');
                    $registro_pago->save();
                  }

                  $estado['estado'] = 'error';
                  $estado['titulo'] = 'Pago no completado';
                  $estado['mensaje'] = (string)$nuevo_mensaje[0];

                  $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
                  if($dato)
                    $dato->delete();

                  $dato = new DatoSeguimiento();
                  $dato->nombre = 'estado_solicitud_pago';
                  $dato->valor = 'pendiente';
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
                  $estado['titulo'] = 'Pago no completado';
                  $estado['mensaje'] = (string)$nuevo_mensaje[0];

                  $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('estado_solicitud_pago', $etapa_id);
                  if($dato)
                    $dato->delete();

                  $dato = new DatoSeguimiento();
                  $dato->nombre = 'estado_solicitud_pago';
                  $dato->valor = 'rechazado';
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
                  $estado['titulo'] = 'Pago no completado';
                  $estado['mensaje'] = (string)$nuevo_mensaje[0];

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
}

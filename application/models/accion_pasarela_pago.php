<?php

require_once('accion.php');

class AccionPasarelaPago extends Accion {

    public function displayForm($operacion_id=null) {
      if (($this->extra) && (!$operacion_id)) {
          $operacion_id = $this->extra->pasarela_pago_id;

          if(!isset($this->extra->metodo)) {
            $extra_metodo = 'antel';
          }
          else {
            $extra_metodo = $this->extra->metodo;
          }

          switch($extra_metodo) {
            case 'generico':
            //*************************************************************************
            //******************************PASARELA GENERICA**************************
            //*************************************************************************

              $pasarela_pago_id = ($this->extra ? $this->extra->pasarela_pago_id : '');
              $metodo = ($this->extra ? $this->extra->metodo : '');
              $cuerpo_soap_operacion = ($this->extra ? $this->extra->cuerpo_soap_operacion : '');
              $pasarela_pago_generica_id = ($this->extra ? $this->extra->pasarela_pago_generica_id : '');
              $codigo_operacion_soap = ($this->extra ? $this->extra->codigo_operacion_soap : '');
              $url_redireccion = ($this->extra ? $this->extra->url_redireccion : '');
              $url_ticket = ($this->extra ? $this->extra->url_ticket : '');
              $metodo_http = ($this->extra ? $this->extra->metodo_http : '');
              $tema_email_inicio = ($this->extra ? $this->extra->tema_email_inicio : '');
              $cuerpo_email_inicio = ($this->extra ? $this->extra->cuerpo_email_inicio : '');

              $display = '<div class="form-horizontal">';

              $display .= '<input type="hidden" name="extra[pasarela_pago_id]" value="'. $pasarela_pago_id .'" />';
              $display .= '<input type="hidden" name="extra[metodo]" value="'. $metodo .'" />';


              $display .= '<div class="control-group">';
              $display .= '<label for="operacion" class="control-label">Cuerpo SOAP de la solicitud</label>';
              $display .= '<div class="controls">';
              $display .= '<textarea  class="input-xlarge" name="extra[cuerpo_soap_operacion]">'. $cuerpo_soap_operacion .'</textarea>';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="extra[tema_email_inicio]" class="control-label">Tema de email al inicio del pago</label>';
              $display .= '<div class="controls">';
              $display .= '<input class="input-large" type="text" name="extra[tema_email_inicio]" value="'. $tema_email_inicio .'" />';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="extra[cuerpo_email_inicio]" class="control-label">Cuerpo de email al inicio del pago</label>';
              $display .= '<div class="controls">';
              $display .= '<textarea class="input-large" name="extra[cuerpo_email_inicio]">'.$cuerpo_email_inicio .'</textarea>';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<input readonly type="hidden" name="extra[pasarela_pago_generica_id]" value="'. $pasarela_pago_generica_id .'" />';
              $display .= '<input readonly type="hidden" name="extra[codigo_operacion_soap]" value="'. $codigo_operacion_soap .'" />';
              $display .= '<input readonly type="hidden" name="extra[url_redireccion]" value="'. $url_redireccion .'" />';
              $display .= '<input readonly type="hidden" name="extra[url_ticket]" value="'. $url_ticket .'" />';
              $display .= '<input readonly type="hidden" name="extra[metodo_http]" value="'. $metodo_http .'" />';
              $display .= '<input readonly type="hidden" name="extra[variable_evaluar]" value="'. $variable_evaluar .'" />';
              $display .= '</div>';

              return $display;
              break;
            default:
              //*************************************************************************
              //******************************PASARELA ITC*******************************
              //*************************************************************************

              $id_tramite = ($this->extra ? $this->extra->id_tramite : '');
              $tasa_1 = ($this->extra ? $this->extra->tasa_1 : '');
              $tasa_2 = ($this->extra ? $this->extra->tasa_2 : '');
              $tasa_3 = ($this->extra ? $this->extra->tasa_3 : '');
              $vencimiento = ($this->extra ? $this->extra->vencimiento : '');
              $codigos_desglose = ($this->extra ? $this->extra->codigos_desglose : '');
              $montos_desglose = ($this->extra ? $this->extra->montos_desglose : '');
              $operacion = ($this->extra ? $this->extra->operacion : '');
              $clave_organismo = ($this->extra ? $this->extra->clave_organismo : '');
              $clave_tramite = ($this->extra ? $this->extra->clave_tramite : '');
              $pasarela_id = ($this->extra ? $this->extra->pasarela_pago_antel_id : '');

              $tema_email_inicio = ($this->extra ? $this->extra->tema_email_inicio : '');
              $cuerpo_email_inicio = ($this->extra ? $this->extra->cuerpo_email_inicio : '');
              $tema_email_pendiente = ($this->extra ? $this->extra->tema_email_pendiente : '');
              $cuerpo_email_pendiente = ($this->extra ? $this->extra->cuerpo_email_pendiente : '');
              $tema_email_ok = ($this->extra ? $this->extra->tema_email_ok : '');
              $cuerpo_email_ok = ($this->extra ? $this->extra->cuerpo_email_ok : '');
              $tema_email_timeout = ($this->extra ? $this->extra->tema_email_timeout : '');
              $cuerpo_email_timeout = ($this->extra ? $this->extra->cuerpo_email_timeout : '');

              $display = '<div class="form-horizontal">';

              $display .= '<input type="hidden" name="extra[pasarela_pago_id]" value="'. $operacion_id .'" />';
              $display .= '<div class="control-group">';
              $display .= '<label for="operacion" class="control-label">ID de trámite</label>';
              $display .= '<div class="controls">';
              $display .= '<input class="input-large" type="text" name="extra[id_tramite]" value="'. $id_tramite .'" />';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="operacion" class="control-label">Tasa 1</label>';
              $display .= '<div class="controls">';
              $display .= '<input  class="input-large" type="text" name="extra[tasa_1]" value="'. $tasa_1 .'" />';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="operacion" class="control-label">Tasa 2</label>';
              $display .= '<div class="controls">';
              $display .= '<input class="input-large" type="text" name="extra[tasa_2]" value="'. $tasa_2 .'" />';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="operacion" class="control-label">Tasa 3</label>';
              $display .= '<div class="controls">';
              $display .= '<input class="input-large" type="text" name="extra[tasa_3]" value="'. $tasa_3 .'" />';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="operacion" class="control-label">Fecha de vencimiento (AAAA/MM/DD HH:mm)</label>';
              $display .= '<div class="controls">';

              $display .= '<input class="input-large" type="text" id="pasarela_pago_vencimiento" name="extra[vencimiento]" value="'. $vencimiento .'" />';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="operacion" class="control-label">Códigos de desglose</label>';
              $display .= '<div class="controls">';
              $display .= '<input class="input-large" type="text" name="extra[codigos_desglose]" value="'. $codigos_desglose .'" />';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="operacion" class="control-label">Montos de desglose</label>';
              $display .= '<div class="controls">';
              $display .= '<input class="input-large" type="text" name="extra[montos_desglose]" value="'. $montos_desglose .'" />';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="extra[clave_tramite]" class="control-label">Clave de trámite</label>';
              $display .= '<div class="controls">';
              $display .= '<input class="input-large" type="password" name="extra[clave_tramite]" value="'. $clave_tramite .'" />';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="extra[tema_email_inicio]" class="control-label">Tema de email al inicio del pago</label>';
              $display .= '<div class="controls">';
              $display .= '<input class="input-large" type="text" name="extra[tema_email_inicio]" value="'. $tema_email_inicio .'" />';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="extra[cuerpo_email_inicio]" class="control-label">Cuerpo de email al inicio del pago</label>';
              $display .= '<div class="controls">';
              $display .= '<textarea class="input-large" name="extra[cuerpo_email_inicio]">'.$cuerpo_email_inicio .'</textarea>';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="extra[tema_email_ok]" class="control-label">Tema de email en pago realizado</label>';
              $display .= '<div class="controls">';
              $display .= '<input class="input-large" type="text" name="extra[tema_email_ok]" value="'. $tema_email_ok .'" />';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="extra[cuerpo_email_ok]" class="control-label">Cuerpo de email en pago realizado</label>';
              $display .= '<div class="controls">';
              $display .= '<textarea class="input-large" name="extra[cuerpo_email_ok]">'.$cuerpo_email_ok .'</textarea>';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="extra[tema_email_pendiente]" class="control-label">Tema de email en pago pendiente (red de cobranzas)</label>';
              $display .= '<div class="controls">';
              $display .= '<input class="input-large" type="text" name="extra[tema_email_pendiente]" value="'. $tema_email_pendiente .'" />';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="extra[cuerpo_email_pendiente]" class="control-label">Cuerpo de email en pago pendiente (red de cobranzas)</label>';
              $display .= '<div class="controls">';
              $display .= '<textarea class="input-large" name="extra[cuerpo_email_pendiente]">'.$cuerpo_email_pendiente .'</textarea>';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="extra[tema_email_timeout]" class="control-label">Tema de email al fallar estado (timeout)</label>';
              $display .= '<div class="controls">';
              $display .= '<input class="input-large" type="text" name="extra[tema_email_timeout]" value="'. $tema_email_timeout .'" />';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<div class="control-group">';
              $display .= '<label for="extra[cuerpo_email_timeout]" class="control-label">Cuerpo de email al fallar estado (timeout)</label>';
              $display .= '<div class="controls">';
              $display .= '<textarea class="input-large" name="extra[cuerpo_email_timeout]">'.$cuerpo_email_timeout .'</textarea>';
              $display .= '</div>';
              $display .= '</div>';

              $display .= '<input  type="hidden" name="extra[operacion]" value="'. $operacion .'" />';
              $display .= '<input readonly type="hidden" name="extra[clave_organismo]" value="'. $clave_organismo .'" />';
              $display .= '<input readonly type="hidden" name="extra[pasarela_pago_antel_id]" value="'. $pasarela_id .'" />';
              $display .= '</div>';

              return $display;
          }
      }
      else {
        $pasarela_origen = Doctrine_Query::create()
            ->from('PasarelaPago p')
            ->where('p.id = ?', $operacion_id)
            ->fetchOne();

        switch($pasarela_origen->metodo) {
          case 'generico':
          //*************************************************************************
          //******************************PASARELA GENERICA**************************
          //*************************************************************************
            $pasarela = Doctrine_Query::create()
                ->from('PasarelaPagoGenerica pg')
                ->where('pg.pasarela_pago_id = ?', $operacion_id)
                ->fetchOne();

            $codigo_operacion_soap = $pasarela->codigo_operacion_soap;
            $url_redireccion = $pasarela->url_redireccion;
            $url_ticket = $pasarela->url_ticket;
            $metodo_http = $pasarela->metodo_http;
            $pasarela_id = $pasarela->id;
            $tema_email_inicio = $pasarela->tema_email_inicio;
            $cuerpo_email_inicio = $pasarela->cuerpo_email_inicio;

            $operacion_soap = Doctrine_Query::create()
                ->from('WsOperacion op')
                ->where('op.codigo = ?', $codigo_operacion_soap)
                ->fetchOne();

            $display = '<div class="form-horizontal">';

            $display .= '<input type="hidden" name="extra[pasarela_pago_id]" value="'. $operacion_id .'" />';
            $display .= '<input type="hidden" name="extra[metodo]" value="'. $pasarela_origen->metodo .'" />';


            $display .= '<div class="control-group">';
            $display .= '<label for="operacion" class="control-label">Cuerpo SOAP de la operación</label>';
            $display .= '<div class="controls">';
            $display .= '<textarea  class="input-xlarge" name="extra[cuerpo_soap_operacion]">'. $operacion_soap->soap .'</textarea>';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="extra[tema_email_inicio]" class="control-label">Tema de email al inicio del pago</label>';
            $display .= '<div class="controls">';
            $display .= '<input class="input-large" type="text" name="extra[tema_email_inicio]" value="'. $tema_email_inicio .'" />';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="extra[cuerpo_email_inicio]" class="control-label">Cuerpo de email al inicio del pago</label>';
            $display .= '<div class="controls">';
            $display .= '<textarea class="input-large" name="extra[cuerpo_email_inicio]">'.$cuerpo_email_inicio .'</textarea>';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<input readonly type="hidden" name="extra[pasarela_pago_generica_id]" value="'. $pasarela_id .'" />';
            $display .= '<input readonly type="hidden" name="extra[codigo_operacion_soap]" value="'. $codigo_operacion_soap .'" />';
            $display .= '<input readonly type="hidden" name="extra[url_redireccion]" value="'. $url_redireccion .'" />';
            $display .= '<input readonly type="hidden" name="extra[url_ticket]" value="'. $url_ticket .'" />';
            $display .= '<input readonly type="hidden" name="extra[metodo_http]" value="'. $metodo_http .'" />';
            $display .= '</div>';

            return $display;

            break;
          default:
          //*************************************************************************
          //******************************PASARELA ITC**************************
          //*************************************************************************
            $pasarela = Doctrine_Query::create()
                ->from('PasarelaPagoAntel pa')
                ->where('pa.pasarela_pago_id = ?', $operacion_id)
                ->fetchOne();

            $id_tramite = $pasarela->id_tramite;
            $tasa_1 = $pasarela->tasa_1;
            $tasa_2 = $pasarela->tasa_2;
            $tasa_3 = $pasarela->tasa_3;
            $vencimiento = $pasarela->vencimiento;
            $codigos_desglose = $pasarela->codigos_desglose;
            $montos_desglose = $pasarela->montos_desglose;
            $operacion = $pasarela->operacion;
            $clave_organismo = $pasarela->clave_organismo;
            $clave_tramite = $pasarela->clave_tramite;
            $pasarela_id = $pasarela->id;

            $tema_email_inicio = $pasarela->tema_email_inicio;
            $cuerpo_email_inicio = $pasarela->cuerpo_email_inicio;
            $tema_email_pendiente = $pasarela->tema_email_pendiente;
            $cuerpo_email_pendiente = $pasarela->cuerpo_email_pendiente;
            $tema_email_ok = $pasarela->tema_email_ok;
            $cuerpo_email_ok = $pasarela->cuerpo_email_ok;
            $tema_email_timeout = $pasarela->tema_email_timeout;
            $cuerpo_email_timeout = $pasarela->cuerpo_email_timeout;

            $display = '<div class="form-horizontal">';

            $display .= '<input type="hidden" name="extra[pasarela_pago_id]" value="'. $operacion_id .'" />';
            $display .= '<input type="hidden" name="extra[metodo]" value="'. $pasarela_origen->metodo .'" />';
            $display .= '<div class="control-group">';
            $display .= '<label for="operacion" class="control-label">ID de trámite</label>';
            $display .= '<div class="controls">';
            $display .= '<input class="input-large" type="text" name="extra[id_tramite]" value="'. $id_tramite .'" />';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="operacion" class="control-label">Tasa 1</label>';
            $display .= '<div class="controls">';
            $display .= '<input  class="input-large" type="text" name="extra[tasa_1]" value="'. $tasa_1 .'" />';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="operacion" class="control-label">Tasa 2</label>';
            $display .= '<div class="controls">';
            $display .= '<input class="input-large" type="text" name="extra[tasa_2]" value="'. $tasa_2 .'" />';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="operacion" class="control-label">Tasa 3</label>';
            $display .= '<div class="controls">';
            $display .= '<input class="input-large" type="text" name="extra[tasa_3]" value="'. $tasa_3 .'" />';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="operacion" class="control-label">Fecha de vencimiento (AAAA/MM/DD HH:mm)</label>';
            $display .= '<div class="controls">';

            $display .= '<input class="input-large" type="text" id="pasarela_pago_vencimiento" name="extra[vencimiento]" value="'. $vencimiento .'" />';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="operacion" class="control-label">Códigos de desglose</label>';
            $display .= '<div class="controls">';
            $display .= '<input class="input-large" type="text" name="extra[codigos_desglose]" value="'. $codigos_desglose .'" />';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="operacion" class="control-label">Montos de desglose</label>';
            $display .= '<div class="controls">';
            $display .= '<input class="input-large" type="text" name="extra[montos_desglose]" value="'. $montos_desglose .'" />';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="extra[clave_tramite]" class="control-label">Clave de trámite</label>';
            $display .= '<div class="controls">';
            $display .= '<input class="input-large" type="password" name="extra[clave_tramite]" value="'. $clave_tramite .'" />';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="extra[tema_email_inicio]" class="control-label">Tema de email al inicio del pago</label>';
            $display .= '<div class="controls">';
            $display .= '<input class="input-large" type="text" name="extra[tema_email_inicio]" value="'. $tema_email_inicio .'" />';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="extra[cuerpo_email_inicio]" class="control-label">Cuerpo de email al inicio del pago</label>';
            $display .= '<div class="controls">';
            $display .= '<textarea class="input-large" name="extra[cuerpo_email_inicio]">'.$cuerpo_email_inicio .'</textarea>';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="extra[tema_email_ok]" class="control-label">Tema de email en pago realizado</label>';
            $display .= '<div class="controls">';
            $display .= '<input class="input-large" type="text" name="extra[tema_email_ok]" value="'. $tema_email_ok .'" />';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="extra[cuerpo_email_ok]" class="control-label">Cuerpo de email en pago realizado</label>';
            $display .= '<div class="controls">';
            $display .= '<textarea class="input-large" name="extra[cuerpo_email_ok]">'.$cuerpo_email_ok .'</textarea>';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="extra[tema_email_pendiente]" class="control-label">Tema de email en pago pendiente (red de cobranzas)</label>';
            $display .= '<div class="controls">';
            $display .= '<input class="input-large" type="text" name="extra[tema_email_pendiente]" value="'. $tema_email_pendiente .'" />';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="extra[cuerpo_email_pendiente]" class="control-label">Cuerpo de email en pago pendiente (red de cobranzas)</label>';
            $display .= '<div class="controls">';
            $display .= '<textarea class="input-large" name="extra[cuerpo_email_pendiente]">'.$cuerpo_email_pendiente .'</textarea>';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="extra[tema_email_timeout]" class="control-label">Tema de email al fallar estado (timeout)</label>';
            $display .= '<div class="controls">';
            $display .= '<input class="input-large" type="text" name="extra[tema_email_timeout]" value="'. $tema_email_timeout .'" />';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<div class="control-group">';
            $display .= '<label for="extra[cuerpo_email_timeout]" class="control-label">Cuerpo de email al fallar estado (timeout)</label>';
            $display .= '<div class="controls">';
            $display .= '<textarea class="input-large" name="extra[cuerpo_email_timeout]">'.$cuerpo_email_timeout .'</textarea>';
            $display .= '</div>';
            $display .= '</div>';

            $display .= '<input  type="hidden" name="extra[operacion]" value="'. $operacion .'" />';
            $display .= '<input readonly type="hidden" name="extra[clave_organismo]" value="'. $clave_organismo .'" />';
            $display .= '<input readonly type="hidden" name="extra[pasarela_pago_antel_id]" value="'. $pasarela_id .'" />';
            $display .= '</div>';

            return $display;
        }
      }
    }

    public function validateForm() {
    }

    // -- Solicita token a pasarela y lo almacena en variable @@token_pasarela_pagos
    public function ejecutar(Etapa $etapa, $secuencia = null) {
      if(!isset($this->extra->metodo)) {
        $extra_metodo = 'antel';
      }
      else {
        $extra_metodo = $this->extra->metodo;
      }

      switch($extra_metodo) {
        case 'generico':
        //*************************************************************************
        //******************************PASARELA GENERICA**************************
        //*************************************************************************
          $cuerpo_soap = $this->extra->cuerpo_soap_operacion;

          $pasarela = Doctrine::getTable('PasarelaPagoGenerica')->find($this->extra->pasarela_pago_generica_id);

          $variable_evaluar = $pasarela->variable_evaluar;
          $variable_idsol = $pasarela->variable_idsol;
          $codigo_operacion_soap = $pasarela->codigo_operacion_soap;

          $url_redireccion = null;
          if(!filter_var($pasarela->url_redireccion, FILTER_VALIDATE_URL)) {
            $codigo_operacion_post = $pasarela->url_redireccion;

            $operacion_post = Doctrine_Query::create()
                        ->from('WsOperacion o')
                        ->where('o.codigo = ?', $codigo_operacion_post)
                        ->fetchOne();

            $servicio_post = Doctrine::getTable('WsCatalogo')->find($operacion_post->catalogo_id);
          }
          else {
            $url_redireccion = $pasarela->url_redireccion;
          }

          $operacion = Doctrine_Query::create()
                      ->from('WsOperacion o')
                      ->where('o.codigo = ?', $codigo_operacion_soap)
                      ->fetchOne();

          $servicio = Doctrine::getTable('WsCatalogo')->find($operacion->catalogo_id);

          //Si existe el token o el id de solicitud  entonces la accion no vuelve a obtener el id de solicitud.
          //Esta variable (token o id de solicitud) se puede limpiar posteriormente en el flujo.
          //codigo_estado_solicitud_pago_generico es una variable interna que toma los valores -1 , 0 o 1.
          //-1 time out (caso ITC lo pone en -1 cuando el ws de consulta de estado le da timeout pero no el de solicitud)
          //0 SE debe  verificar_estado_pago_generico
          //1 NO se debe verificar_estado_pago_generico
          //en 1 no se debe verificar porque se solicito recientemente el id de solicutd es un pago nuevo.
          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_idsol), $etapa->id);
          if($dato) {
            //esta el id de solicitud
            $codigo = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago_generico', $etapa->id);
            if ($codigo) {
              if($codigo->valor != '-1') {
                //la variable codigo_estado_solicitud_pago_generico existe y no esta en estado time out
                //entonces se pone valor 0 a la variable para que SE consulte el estado nuevamente en el boton de pago.
                $codigo->delete();
                $codigo = new DatoSeguimiento();
                $codigo->nombre = 'codigo_estado_solicitud_pago_generico';
                $codigo->valor = '0';
                $codigo->etapa_id = $etapa->id;
                $codigo->save();
              }
            }
            else {
              //no esta el id de solicitud entonces obtiene el id de solicitud y pone la variable codigo_estado_solicitud_pago_generico
              //en 1 para que NO se consulte el estado del pago.
              $codigo = new DatoSeguimiento();
              $codigo->nombre = 'codigo_estado_solicitud_pago_generico';
              $codigo->valor = '1';
              $codigo->etapa_id = $etapa->id;
              $codigo->save();

              //invoca el servicio que obtiene el id de solicitud
              $ci = get_instance();
              $ci->load->helper('soap_execute');
              soap_execute($etapa, $servicio, $operacion, $cuerpo_soap);

              $error_servicio_pagos = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("ws_error", $etapa->id);

              //si debe llamar a otro servicio para obtener la URL para el boton de pago
              //se invoca.
              if(isset($servicio_post) && !$error_servicio_pagos) {
                $ci = get_instance();
                $ci->load->helper('soap_execute');
                soap_execute($etapa, $servicio_post, $operacion_post, $operacion_post->soap);

              }
            }
            return;
          }
          else {
            //no esta el token en la base de datos
            // se genera varibale en 1 para que NO se conulte el etado del pago en el boton de pago y se invoca servicios.
            $codigo = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago_generico', $etapa->id);
            if ($codigo)
              $codigo->delete();

            $codigo = new DatoSeguimiento();
            $codigo->nombre = 'codigo_estado_solicitud_pago_generico';
            $codigo->valor = '1';
            $codigo->etapa_id = $etapa->id;
            $codigo->save();

            //alguna pasarla generica necesita que el id de solicitud lo generemos
            //otras los reotrna el servcio, para soportar ambos casos
            //la generamos y si falla el servicio la limpiamos si no falla el servicio
            //entonces si el servicio lo genera se usa el generado por el servicio
            $id_sol = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_idsol), $etapa->id);
            if ($id_sol)
              $id_sol->delete();

            // Generamos el pago previo a la solicitud, y utilizamos la ID del mismo
            // para generar el ID de solicitud.
            $pago = new Pago();
            //TODO el id_tramite no siempre es el id de la etapa. en algun caso puede
            //ser el que retorna el servicio web
            $pago->id_tramite = $etapa->id;
            $pago->id_tramite_interno = $etapa->tramite_id;
            $pago->id_etapa = $etapa->id;
            $pago->id_solicitud = 0;
            $pago->estado = 'iniciado';
            $pago->fecha_actualizacion = date('d/m/Y H:i');
            $pago->pasarela = $pasarela->id;
            $pago->save();

            $id_sol = new DatoSeguimiento();
            $id_sol->nombre = str_replace("@@", "", $variable_idsol);
            $id_sol->valor = $pago->id;
            $id_sol->etapa_id = $etapa->id;
            $id_sol->save();

            // no esta la variable entonces consulta para obtener la solicitud.
            $ci = get_instance();
            $ci->load->helper('soap_execute');
            soap_execute($etapa, $servicio, $operacion, $cuerpo_soap);

            $error_servicio_pagos = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("ws_error", $etapa->id);

            if($error_servicio_pagos) {
              $id_sol = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_idsol), $etapa->id);

              if ($id_sol)
                $id_sol->delete();
            }

            //si debe llamar a otro servicio para obtener la URL para el boton de pago
            //se invoca.
            if(isset($servicio_post) && !$error_servicio_pagos) {
              $ci = get_instance();
              $ci->load->helper('soap_execute');
              soap_execute($etapa, $servicio_post, $operacion_post, $operacion_post->soap);
            }
          }

          break;
        default:
        //*************************************************************************
        //******************************PASARELA ITC*******************************
        //*************************************************************************
          $pasarela_pago_id = $this->extra->pasarela_pago_id;
          $pasarela_pago_antel_id = $this->extra->pasarela_pago_antel_id;

          try {
            // ID tramite
            preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $this->extra->id_tramite, $variable_encontrada);
            if($variable_encontrada) {
              $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa->id);
              $id_tramite = $dato->valor;
            }
            else {
              $id_tramite = $this->extra->id_tramite;
            }

            //  tasa 1
            preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $this->extra->tasa_1, $variable_encontrada);
            if($variable_encontrada) {
              $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa->id);
              $tasa_1 = $dato->valor;
            }
            else {
              $tasa_1 = $this->extra->tasa_1;
            }

            //  tasa 2
            preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $this->extra->tasa_2, $variable_encontrada);
            if($variable_encontrada) {
              $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa->id);
              $tasa_2 = $dato->valor;
            }
            else {
              $tasa_2 = $this->extra->tasa_2;
            }

            //  tasa 3
            preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $this->extra->tasa_3, $variable_encontrada);
            if($variable_encontrada) {
              $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa->id);
              $tasa_3 = $dato->valor;
            }
            else {
              $tasa_3 = $this->extra->tasa_3;
            }

            // vencimiento
            preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $this->extra->vencimiento, $variable_encontrada);
            if($variable_encontrada) {
              $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa->id);
              $vencimiento = $dato->valor;
            }
            else {
              $vencimiento = $this->extra->vencimiento;
            }

            $fecha = '';
            if (strpos($vencimiento, ':')  !== FALSE){
              //tiene hora
              $fecha  = date('Y/m/d H:i',strtotime($vencimiento));
            }else{
              //no tiene hora se pone por defecto 23:59
              $fecha  = date('Y/m/d H:i',strtotime($vencimiento. ' 23:59'));
            }

            $fecha = strtotime($fecha);
            $fecha_vencimiento = date("YmdHi", $fecha);

            //  codigos desglose
            preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $this->extra->codigos_desglose, $variable_encontrada);
            if($variable_encontrada) {
              $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa->id);
              $codigos_desglose = $dato->valor;
            }
            else {
              $codigos_desglose = $this->extra->codigos_desglose;
            }

            //  montos desglose
            preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $this->extra->montos_desglose, $variable_encontrada);
            if($variable_encontrada) {
              $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa->id);
              $montos_desglose = $dato->valor;
            }
            else {
              $montos_desglose = $this->extra->montos_desglose;
            }

            //  clave de tramite
            preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $this->extra->clave_tramite, $variable_encontrada);
            if($variable_encontrada) {
              $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa->id);
              $clave_tramite = $dato->valor;
            }
            else {
              $clave_tramite = $this->extra->clave_tramite;
            }
          }
          catch(Exception $error) {
            log_message('error', $error->getMessage());
          }

          // -- Si ya existe el token no lo solicita nuevamente, este token se limpia desde el flujo
          //invocando al metodo limpiar_sesion de pagos.php
          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('token_pasarela_pagos', $etapa->id);
          if ($dato) {
            //existe el token_pasarela_pagos
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago', $etapa->id);
            if ($dato) {
              if($dato->valor != '-1') {
                //existe la variable codigo_estado_solicitud_pago y no es -1 (time out) entonces se setea en 0 para qeu se consulte el estado
                //desde el boton de pago
                $dato->delete();
                $dato = new DatoSeguimiento();
                $dato->nombre = 'codigo_estado_solicitud_pago';
                $dato->valor = '0';
                $dato->etapa_id = $etapa->id;
                $dato->save();
              }
            }

            return;
          }
          else {
            //no existe el token_pasarela_pagos
            $pasarela = Doctrine_Query::create()
                ->from('PasarelaPagoAntel pa')
                ->where('pa.pasarela_pago_id = ?', $this->extra->pasarela_pago_id)
                ->execute();
            $pasarela = $pasarela[0];

            $pago = new Pago();
            $pago->id_tramite = $id_tramite;
            $pago->id_tramite_interno = $etapa->tramite_id;
            $pago->id_etapa = $etapa->id;
            $pago->id_solicitud = 0;
            $pago->estado = 'iniciado';
            $pago->fecha_actualizacion = date('d/m/Y H:i');
            $pago->pasarela = $pasarela_pago_antel_id;
            $pago->save();

            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('codigo_estado_solicitud_pago', $etapa->id);
            if ($dato)
              $dato->delete();

            //la variable codigo_estado_solicitud_pago se setea en 1 para que NO se consulte el estado
            //desde el boton de pago
            $dato = new DatoSeguimiento();
            $dato->nombre = 'codigo_estado_solicitud_pago';
            $dato->valor = '1';
            $dato->etapa_id = $etapa->id;
            $dato->save();

            // El id de la solicitud es el autogenerado de la tabla de pagos
            $id_sol = $pago->id;

            $body = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tem="http://tempuri.org/">';
            if (!empty(SOAP_PASARELA_PAGO_SOL)){
                if (SOAP_PASARELA_PAGO_SOL == '1.1'){
                  $body = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">';
                }
            }
            $body = $body . '<soap:Header/>
                     <soap:Body>
                        <tem:Solicitud>
                           <tem:request>
                              <tem:IdSolicitud>'. $id_sol .'</tem:IdSolicitud>
                              <tem:IdTramite>'. $id_tramite .'</tem:IdTramite>
                              <tem:ImporteTasa1>'. $tasa_1 .'</tem:ImporteTasa1>
                              <tem:ImporteTasa2>'. $tasa_2 .'</tem:ImporteTasa2>
                              <tem:ImporteTasa3>'. $tasa_3 .'</tem:ImporteTasa3>
                              <tem:FechaVencimiento>'. $fecha_vencimiento .'</tem:FechaVencimiento>
                              <tem:UsuarioPEU>anonimo</tem:UsuarioPEU>
                              <tem:codigosDesglose>'. $codigos_desglose .'</tem:codigosDesglose>
                              <tem:montosDesglose>'. $montos_desglose .'</tem:montosDesglose>
                              <tem:IdFormaDePago>0</tem:IdFormaDePago>
                              <tem:Referencia></tem:Referencia>
                              <tem:ConsumidorFinal>0</tem:ConsumidorFinal>
                              <tem:NroFactura></tem:NroFactura>
                              <tem:PassOrganismo>'. $this->extra->clave_organismo .'</tem:PassOrganismo>
                           </tem:request>
                        </tem:Solicitud>
                     </soap:Body>
                  </soap:Envelope>';

            $header = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "Expect: ",
                "Content-length: ".strlen($body)
            );

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL,            WS_PASARELA_PAGO);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, PASARELA_PAGO_TIMEOUT_CONEXION);
            curl_setopt($curl, CURLOPT_TIMEOUT,        PASARELA_PAGO_TIMEOUT_RESPUESTA);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_POST,           true);
            curl_setopt($curl, CURLOPT_POSTFIELDS,     $body);
            curl_setopt($curl, CURLOPT_HTTPHEADER,     $header);

            if (!empty(PROXY_PASARELA_PAGO)){
              curl_setopt($curl, CURLOPT_PROXY, PROXY_PASARELA_PAGO);
            }

            curl_setopt($curl, CURLOPT_SSLCERT, UBICACION_CERTIFICADOS_PASARELA.$pasarela->certificado);
            curl_setopt($curl, CURLOPT_SSLCERTPASSWD, $pasarela->pass_clave_certificado);
            curl_setopt($curl, CURLOPT_SSLKEY, UBICACION_CERTIFICADOS_PASARELA.$pasarela->clave_certificado);

            $response = curl_exec($curl);
            $curl_errno = curl_errno($curl); // -- Codigo de error
            $curl_error = curl_error($curl); // -- Descripcion del error
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE); // -- Codigo respuesta HTTP
            curl_close($curl);

            try {
              $xml = new SimpleXMLElement($response);
              $solicitudRespuesta = $xml->xpath("//*[local-name() = 'Ok']/text()");
              $solicitudRespuesta = (string)$solicitudRespuesta[0][0];
              $solicitudRespuesta = filter_var($solicitudRespuesta, FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);
              $mensajeError =  $xml->xpath("//*[local-name() = 'Mensaje']/text()");
              $mensajeError = (string)$mensajeError[0][0];
            }
            catch(Exception $error) {
              log_message('error', 'pasarela ws solicitud exception '. $error . ' response ws: ' . $response);
            }

            if($curl_errno > 0 || !$solicitudRespuesta) {
              $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('ws_error', $etapa->id);
              if($dato)
                $dato->delete();

              $dato = new DatoSeguimiento();
              $dato->nombre = 'ws_error';
              $dato->valor = $solicitudRespuesta. "Hubo un error al procesar su solicitud con identificacion " . $id_sol . " : " . $mensajeError . " . Por favor, vuelva a intentarlo más tarde"  ;
              $dato->etapa_id = $etapa->id;
              $dato->save();
              log_message('error', 'pasarela ws solicitud error curl_error-curl_errno: ' . $curl_error .'-'. $curl_errno.  ' MENSAJE WS: ' . $mensajeError . ' HTTPCODE: ' .$http_code);
            }
            else {
              $CI = &get_instance();
              $CI->session->set_userdata('id_solicitud', $id_sol);
              $CI->session->set_userdata('id_etapa', $etapa->id);

               $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('ws_error', $etapa->id);
               if($dato)
                 $dato->delete();

              if($xml->xpath("//*[local-name() = 'Token']")) {
                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_accion_ejecutada', $etapa->id);
                if ($dato)
                    $dato->delete();

                $dato = new DatoSeguimiento();
                $dato->nombre = 'id_accion_ejecutada';
                $dato->valor = $this->id;
                $dato->etapa_id = $etapa->id;
                $dato->save();

                // -- Crea la variable id_sol_pasarela_pagos
                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_sol_pasarela_pagos', $etapa->id);
                if ($dato)
                    $dato->delete();

                $dato = new DatoSeguimiento();
                $dato->nombre = 'id_sol_pasarela_pagos';
                $dato->valor = (string)$id_sol;
                $dato->etapa_id = $etapa->id;
                $dato->save();

                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('Solicitud_IdSolicitud', $etapa->id);
                if($dato)
                  $dato->delete();

                $dato = new DatoSeguimiento();
                $dato->nombre = 'Solicitud_IdSolicitud';
                $dato->valor = (string)$id_sol;
                $dato->etapa_id = $etapa->id;
                $dato->save();

                // -- Crea la variable id_tramite_pasarela_pagos
                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_tramite_pasarela_pagos', $etapa->id);
                if ($dato)
                    $dato->delete();

                $dato = new DatoSeguimiento();
                $dato->nombre = 'id_tramite_pasarela_pagos';
                $dato->valor = (string)$id_tramite;
                $dato->etapa_id = $etapa->id;
                $dato->save();

                // -- Crea la variable token_pasarela_pagos
                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('token_pasarela_pagos', $etapa->id);
                if ($dato)
                    $dato->delete();

                $token = $xml->xpath("//*[local-name() = 'Token']/text()");

                $dato = new DatoSeguimiento();
                $dato->nombre = 'token_pasarela_pagos';
                $dato->valor = (string)$token[0];
                $dato->etapa_id = $etapa->id;
                $dato->save();

                $registro_pago = Doctrine_Query::create()
                    ->from('Pago p')
                    ->where('p.id = ?', $pago->id)
                    ->fetchOne();

                $registro_pago->id_solicitud = $pago->id;
                $registro_pago->estado = 'token_solicita';
                $registro_pago->save();

                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(md5($registro_pago->id . '_clave_tramite_pasarela_pagos'), $etapa->id);
                if ($dato)
                    $dato->delete();

                $dato = new DatoSeguimiento();
                $dato->nombre = md5($registro_pago->id . '_clave_tramite_pasarela_pagos');
                $dato->valor = (string)$clave_tramite;
                $dato->etapa_id = $etapa->id;
                $dato->save();
              }
            }
          }
      }
    }
}

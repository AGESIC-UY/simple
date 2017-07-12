<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

ERROR - 05-06-2017 14:14:57 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/autenticacion.php 136
ERROR - 05-06-2017 14:15:15 --> Severity: Warning  --> fsockopen(): unable to connect to localhost:9312 (Connection refused) /var/www/html/simple/application/libraries/sphinxclient.php 591
ERROR - 05-06-2017 14:15:15 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 14:15:19 --> Severity: Warning  --> fsockopen(): unable to connect to localhost:9312 (Connection refused) /var/www/html/simple/application/libraries/sphinxclient.php 591
ERROR - 05-06-2017 14:15:20 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 14:15:22 --> Severity: Warning  --> fsockopen(): unable to connect to localhost:9312 (Connection refused) /var/www/html/simple/application/libraries/sphinxclient.php 591
ERROR - 05-06-2017 14:15:22 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 14:15:25 --> Severity: Warning  --> fsockopen(): unable to connect to localhost:9312 (Connection refused) /var/www/html/simple/application/libraries/sphinxclient.php 591
ERROR - 05-06-2017 14:15:25 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 14:15:28 --> Severity: Warning  --> fsockopen(): unable to connect to localhost:9312 (Connection refused) /var/www/html/simple/application/libraries/sphinxclient.php 591
ERROR - 05-06-2017 14:15:28 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 14:16:07 --> Severity: Warning  --> fsockopen(): unable to connect to localhost:9312 (Connection refused) /var/www/html/simple/application/libraries/sphinxclient.php 591
ERROR - 05-06-2017 14:16:07 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 14:16:29 --> Severity: Warning  --> fsockopen(): unable to connect to localhost:9312 (Connection refused) /var/www/html/simple/application/libraries/sphinxclient.php 591
ERROR - 05-06-2017 14:16:29 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 14:42:40 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/autenticacion.php 136
ERROR - 05-06-2017 14:43:38 --> pasarela ws solicitud exception exception 'Exception' with message 'String could not be parsed as XML' in /var/www/html/simple/application/models/accion_pasarela_pago.php:829
Stack trace:
#0 /var/www/html/simple/application/models/accion_pasarela_pago.php(829): SimpleXMLElement->__construct('')
#1 /var/www/html/simple/application/models/etapa.php(481): AccionPasarelaPago->ejecutar(Object(Etapa))
#2 /var/www/html/simple/application/controllers/etapas.php(285): Etapa->iniciarPaso(Object(Paso), '1')
#3 [internal function]: Etapas->ejecutar('3790', '1')
#4 /var/www/html/simple/system/core/CodeIgniter.php(359): call_user_func_array(Array, Array)
#5 /var/www/html/simple/index.php(201): require_once('/var/www/html/s...')
#6 {main} response ws: 
ERROR - 05-06-2017 14:43:38 --> Severity: Notice  --> Undefined variable: solicitudRespuesta /var/www/html/simple/application/models/accion_pasarela_pago.php 847
ERROR - 05-06-2017 14:43:38 --> Severity: Notice  --> Undefined variable: mensajeError /var/www/html/simple/application/models/accion_pasarela_pago.php 847
ERROR - 05-06-2017 14:43:38 --> Severity: Notice  --> Undefined variable: mensajeError /var/www/html/simple/application/models/accion_pasarela_pago.php 850
ERROR - 05-06-2017 14:43:38 --> pasarela ws solicitud error curl_error-curl_errno: Could not resolve host: wp-testing.hg.com.uy; Unknown error-6 MENSAJE WS:  HTTPCODE: 0
ERROR - 05-06-2017 14:43:39 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/models/campo_pagos.php 696
ERROR - 05-06-2017 14:43:39 --> Severity: Notice  --> Indirect modification of overloaded property CampoError::$extra has no effect /var/www/html/simple/application/models/campo_error.php 22
ERROR - 05-06-2017 14:43:39 --> Severity: Warning  --> Creating default object from empty value /var/www/html/simple/application/models/campo_error.php 22
ERROR - 05-06-2017 14:44:29 --> Severity: Notice  --> Indirect modification of overloaded property CampoError::$extra has no effect /var/www/html/simple/application/models/campo_error.php 22
ERROR - 05-06-2017 14:44:29 --> Severity: Warning  --> Creating default object from empty value /var/www/html/simple/application/models/campo_error.php 22
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index: startcolumn /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24139
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index: startx /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24140
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index: startpage /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24143
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index: startpage /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24146
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index:  /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24146
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index: startpage /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24147
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index:  /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24147
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index: startpage /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24179
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index: startx /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24402
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index: startcolumn /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24139
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index: startx /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24140
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index: startpage /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24143
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index: startpage /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24146
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index:  /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24146
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index: startpage /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24147
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index:  /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24147
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index: startpage /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24179
ERROR - 05-06-2017 14:45:59 --> Severity: Notice  --> Undefined index: startx /var/www/html/simple/application/libraries/tcpdf/tcpdf.php 24402
ERROR - 05-06-2017 14:46:06 --> Severity: Notice  --> Undefined property: stdClass::$firmar /var/www/html/simple/application/controllers/etapas.php 507
ERROR - 05-06-2017 14:46:15 --> Severity: Warning  --> fsockopen(): unable to connect to localhost:9312 (Connection refused) /var/www/html/simple/application/libraries/sphinxclient.php 591
ERROR - 05-06-2017 14:46:15 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 14:49:01 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 14:49:02 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 14:49:49 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 14:49:59 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 14:51:24 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 14:51:25 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:02:59 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:06:06 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:06:10 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:06:11 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:06:41 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:07:41 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:07:43 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:07:58 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:08:01 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:08:05 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:08:16 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:08:21 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:13:31 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:13:34 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:14:29 --> Severity: Notice  --> Undefined variable: etapas /var/www/html/simple/application/controllers/etapas.php 63
ERROR - 05-06-2017 15:15:27 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:15:50 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89
ERROR - 05-06-2017 15:15:51 --> Severity: Warning  --> Invalid argument supplied for foreach() /var/www/html/simple/application/views/etapas/inbox.php 89

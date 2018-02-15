<?php

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
|--------------------------------------------------------------------------
| Webservices
|--------------------------------------------------------------------------
|
|
*/

// -- Configuracion para servicios web.
define('WS_TIMEOUT_CONEXION', 30);
define('WS_TIMEOUT_RESPUESTA', 30);
define('WS_FIRMA_DOCUMENTOS', 'http://localhost:8080/AgesicFirmaWS/AgesicFirmaServer'); // -- Tomcat Webapp Firma.
define('WS_AGESIC_FIRMA','http://simple.ws.xxx.com.uy/AgesicFirmaWS/AgesicFirma?wsdl'); // -- Tomcat Webapp Firma.
define('WS_AGESIC_FIRMA_OK','http://simple.xxx.com.uy/etapas/confirmar_firma'); // -- Apache Simple.
define('WS_AGESIC_FIRMA_CODEBASE','http://simple.firma.xxx.com.uy'); // -- Apache Simple.
define('WS_AGESIC_FIRMA_XPATH', "//*[local-name() = 'respuesta']/text()");
define('WS_AGESIC_DOCUMENTO_XPATH', "//*[local-name() = 'doc']/text()");
define('WS_AGESIC_DOCUMENTO_SERVIDOR_XPATH', "//*[local-name() = 'doc']/text()");
define('WS_AGESIC_TRAZABLIDAD_CABEZAL', 'http://10.42.43.215:9800/trazabilidad/cabezal3');
define('WS_AGESIC_TRAZABLIDAD_LINEA', 'http://10.42.43.215:9800/trazabilidad/linea');
define('WS_CANAL_INICIO_TRAZABILIDAD', '1');
define('WS_AGESIC_TIPO_PROCESO_TRAZABILIDAD', '1');
define('WS_VERSION_MODELO_TRAZABILIDAD', '101');
define('WS_XPATH_COD_TRAZABILIDAD', "//*[local-name() = 'guid']/text()");
define('WS_VARIABLE_COD_TRAZABILIDAD', 'guidTrazabilidad'); // -- GUID de traza

//define('WS_PASARELA_PAGO', 'https://wp-testing.hg.com.uy/SolicitudWS/v1_ssl/SolicitudWS.asmx');
define('WS_PASARELA_PAGO', 'https://wp-testing.hg.com.uy/SolicitudWS/v2/SolicitudWS.asmx');
define('WS_PASARELA_PAGO_CONSULTA', 'https://testing1.hg.com.uy/gw_wsorg/consultas.asmx');
define('POST_PASARELA_PAGO', 'http://www.testing1.hg.com.uy/Gateway/Interface/default.aspx');
define('COD_BARRAS_PASARELA_PAGO', 'https://testing1.hg.com.uy/gateway/interface/regenerarcodigobarras.aspx');
define('PASARELA_PAGO_TIMEOUT_CONEXION', 30);
define('PASARELA_PAGO_TIMEOUT_RESPUESTA', 30);

define('PROXY_PASARELA_PAGO','');
define('PROXY_WS','');

define('SOAP_PASARELA_PAGO_SOL','');
define('SOAP_PASARELA_PAGO_CONSULTA','1.1');


// -- Validadores de firmas.
define('JAR_FIRMA', '/var/www/html/simple/vendors/xmlsigner.jar');
define('JAR_VALIDACION', '/var/www/html/simple/vendors/xmlsignaturevalidator.jar');
define('UBICACION_CERTIFICADOS_PDI', '/var/www/html/simple/uploads/pdi/'); // -- uploads/pdi/
define('UBICACION_CERTIFICADOS_PASARELA', '/var/www/html/simple/uploads/pasarela/'); // -- uploads/pasarela/
define('UBICACION_CERTIFICADOS_SOAP', '/var/www/html/simple/uploads/soap/'); // -- uploads/soap/

/*
|--------------------------------------------------------------------------
| Autenticación SAML
|--------------------------------------------------------------------------
|
|
*/
// -- Nombre del AUTHSOURCE utilizado en SimpleSAML. Se recomienda dejar tal cual.
define('SIMPLE_SAML_AUTHSOURCE', 'simplesaml');

// -- URLs de origenes confiables (CSRF).
define('ORIGENES_CONFIABLES', serialize(array(0)));

/*
|--------------------------------------------------------------------------
| Application
|--------------------------------------------------------------------------
|
|
*/
define('DIRECTORIO_SUBIDA_DOCUMENTOS', 'uploads/documentos/');
define('HOST_SISTEMA', (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '');

// -- Dominio.
define('DOMINIO', 'simple.xxx.com.uy');
define('HOST_SISTEMA_DOMINIO', '.'.DOMINIO);

// -- En caso de que se configure un dominio con ruta, por ejemplo DOMINIO.GUB.UY/RUTA se debe ingresar la misma
// al final de la siguiente constante. Quedaria de la siguiente manera:
//
// define('HOST_SISTEMA_COMPLETO', (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/RUTA');
//
define('HOST_SISTEMA_COMPLETO', (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '');

// -- Max registros por pagina para paginacion.
define('MAX_REGISTROS_PAGINA', 5);

// -- Configuracion para Redis.
define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', '6379');

// -- Permitir/denegar remover campos de bloques.
define('DENEGAR_REMOVER_CAMPOS_BLOQUES', FALSE);

// -- Permite acceder a la cuenta principal con el dominio raiz (necesario si CDA redirige a raiz).
define('CUENTA_DEFAULT_RAIZ', TRUE);

// -- Certificado para validar respuesta de CDA.
define('CERTIFICADO_CDA_PUBLICO', 'CDA-Pubico-Testing.cer');

// -- Cantidad maxima de intentos posibles para enviar una traza fallida.
define('MAX_INTENTOS_ENVIO_TRAZA', 10);

// -- Texto de versión de Simple
define('SIMPLE_VERSION', 'Versión 1.4-r6_');

// -- Modo debug
define('MODO_DEBUG', (ENVIRONMENT == 'development' ? true : false));

// -- Mensajes de pasarela de pagos
define('MENSAJE_PAGO_CONSULTA_TITULO', 'Consultando estado');
define('MENSAJE_PAGO_CONSULTA', 'Se está consultando el estado del pago, aguarde por favor...');

define('MENSAJE_PAGO_OK_TITULO', 'Pago realizado');
define('MENSAJE_PAGO_OK', 'El pago se ha realizado con éxito.');

define('MENSAJE_PAGO_ERROR_TITULO', 'Error del sistema de pagos');
define('MENSAJE_PAGO_ERROR', 'No se ha podido obtener el estado del pago. Por favor, reintente más tarde.');

define('MENSAJE_PAGO_TIMEOUT_TITULO', 'Ha ocurrido un error');
define('MENSAJE_PAGO_TIMEOUT', 'No es posible obtener el estado del pago, por favor, vuelva a intentarlo más tarde.<br /><br />Si desea volver a consultar el estado de su pago, presione el siguiente botón: ');

define('MENSAJE_PAGO_PENDIENTE_TITULO', 'Pago pendiente');
define('MENSAJE_PAGO_PENDIENTE_1', 'En caso de ya haber efectuado el pago, el mismo no está confirmado, por favor vuelva a consultar en unos minutos.<br>Si desea volver a consultar el estado de su pago, presione el siguiente botón:');
define('MENSAJE_PAGO_PENDIENTE_2', 'En caso de no haber efectuado el pago, puede realizarlo haciendo clic en el siguiente botón.');

define('MENSAJE_PAGO_RECHAZADO_TITULO', 'Pago rechazado');
define('MENSAJE_PAGO_RECHAZADO', 'La forma de pago ha sido rechazada.');

define('MENSAJE_PAGO_REVERSADO_TITULO', 'Pago rechazado');
define('MENSAJE_PAGO_REVERSADO_1', 'El pago ha sido rechazado por la red de cobranzas.');
define('MENSAJE_PAGO_REVERSADO_2', 'Puede realizar un nuevo pago haciendo clic en el siguiente botón.');

define('MENSAJE_PAGO_RC_TITULO', 'Pago pendiente');
define('MENSAJE_PAGO_RC', 'Su pago se encuentra pendiente, recuerde que debe imprimir el ticket presentado para efectuar el pago en la red de cobranzas.<br />Si desea imprimir el ticket de pago puede hacerlo haciendo clic en el siguiente botón: ');

// -- Mensaje de error con firma requerida
define('ERROR_FIRMA_REQUERIDA', 'Se debe firmar el documento para continuar.');

//1.1
define('NIVEL_CONFIANZA_AG', '10_ag');
define('NIVEL_CONFIANZA_VP', '20_vp');
define('NIVEL_CONFIANZA_VCI', '30_vfe');
define('NIVEL_CONFIANZA_CI', '40_ci');

// -- IDs de estados posibles para trazabilidad
define('ID_ESTADOS_POSIBLES_TRAZABILIDAD', serialize(array('2' => 'En ejecución', '4' => 'Cancelado', '3' => 'Finalizado')));

// -- IDs de estados posibles para trazabilidad
define('ID_ESTADOS_POSIBLES_CONEXION_EVALUACION_TRAZABILIDAD', serialize(array('2' => 'En ejecución', '4' => 'Cancelado', '3' => 'Finalizado')));

define('ID_ESTADOS_POSIBLES_CABEZAL_TRAZABILIDAD', serialize(array('1' => 'Inicio')));

// -- Tipo de autenticacion a utilizar (CDA, LDAP, BASICO)
define('TIPO_DE_AUTENTICACION', 'CDA');

// -- Configuracion LDAP
define('LDAP_HOST', '172.17.0.1');
define('LDAP_PUERTO', 389);
define('LDAP_BASE_DN', 'dc=simple,dc=xxx,dc=com,dc=uy');
define('LDAP_ATTR', 'cn');
define('LDAP_USER', 'cn=admin,dc=simple,dc=xxx,dc=com,dc=uy');
define('LDAP_PASS', 'xxx2uy');
define('LDAP_VERSION', 3);

define('MENSAJE_PAGO_CONFIRMADO_FUNCIONARIO', 'El pago ha sido confirmado por el funcionario actuante.');
define('MENSAJE_PAGO_NO_CONFIRMADO_FUNCIONARIO', 'El pago no ha sido confirmado por el funcionario actuante.');
define('MENSAJE_PAGO_FUNCIONARIO', 'Confirmar pago.');

define('MENSAJE_FIRMA_DOCUMENTO_FUNCIONARIO', 'Confirmar firma.');
define('MENSAJE_FIRMA_CONFIRMADO_DOCUMENTO_FUNCIONARIO', 'La firma del documento ha sido confirmado por el funcionario actuante.');
define('MENSAJE_FIRMA_NO_CONFIRMADO_DOCUMENTO_FUNCIONARIO', 'La firma del documento no ha sido confirmado por el funcionario actuante.');

define('MENSAJE_AGENDA_CONFIRMADA_FUNCIONARIO', 'La agenda ha sido gestionada por el funcionario actuante.');


define('TEXTO_CONFIG_PAGO_ONLINE', '¿Pago online en mesa de entrada?');
define('TEXTO_CONFIG_PAGO_REQUERIDO_ONLINE', '¿Pago requerido en mesa de entrada?');
define('TEXTO_CONFIG_FIRMA_DOCUMENTO', '¿Firma electrónica en mesa de entrada?');
define('TEXTO_CONFIG_AGENDAR', '¿Requiere agendar para avanzar en mesa de entrada?');

define('MENSAJE_PAGO_FUNCIONARIO_CANCELAR', 'Cancelar pago.');

// -- Integracion GREP
define('WS_EMPRESA_USUARIO_GREP', 'http://testgrep.simple.pge.red.uy/gestionRepresentante/v1/wsConsultarEmpresasUsuario');
define('WS_PERMISOS_TRAMITES_USUARIO_GREP', 'http://testgrep.simple.pge.red.uy/gestionRepresentante/v1/wsConsultarTramitesUsuarioEmpresa');

// -- Integracion modulo de direcciones (ICA)
define('WS_RUTA_KM_ICA', 'http://vigilia.ica.com.uy/GWS_ICA_GC1031_DEMO/Geocode.asmx?op=FindHighwayKilometer');
define('WS_CALLE_NUMERO_ICA', 'http://vigilia.ica.com.uy/GWS_ICA_GC1031_DEMO/Geocode.asmx?op=FindAddresses');
define('WS_MANZANA_SOLAR_ICA', '');

//1.4-r3
define('DIRECTORIO_REPORTES_EMAIL', 'uploads/reportesemail/');
define('SUBJECT_REPORTES_EMAIL', 'Se ha generado el reporte solicitado');
define('BODY_REPORTES_EMAIL', 'Estimado, adjunto encontrará el reporte solicitado.<br> Saludos');

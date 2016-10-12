<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller'] = 'portada';
$route['404_override'] = '';

$route['backend'] = 'backend/portada';
$route['manager'] = 'manager/portada';

/* Aliases */
$route['admin'] = 'backend/portada';
$route['gestion'] = 'manager/portada';

/* Frontend */
$route['etapas/firmar_documento'] = 'documentos/firmar_documento';
$route['etapas/confirmar_firma'] = 'documentos/confirmar_firma';
$route['encuesta_satisfaccion/crear'] = 'encuesta_satisfaccion/crear';
$route['cda'] = 'autenticacion/login_saml_respuesta';
$route['pagos/control'] = 'pagos/control';
$route['pagos/ok'] = 'pagos/completado';
$route['pagos/error'] = 'pagos/error';
$route['pagos/pendiente'] = 'pagos/pendiente';
$route['pagos/rechazado'] = 'pagos/rechazado';

/* Backend */
$route['backend/ws_catalogos/(:num)/operaciones'] = 'backend/ws_catalogos/operaciones_index/$1';
$route['backend/ws_catalogos/(:num)/operaciones/crear'] = 'backend/ws_catalogos/operaciones_crear/$1';
$route['backend/ws_catalogos/(:num)/operaciones/editar/(:num)'] = 'backend/ws_catalogos/operaciones_editar/$1/$2';
$route['backend/ws_catalogos/(:num)/operaciones/eliminar/(:num)'] = 'backend/ws_catalogos/operaciones_eliminar/$1/$2';
$route['backend/reportes/reporte_satisfaccion/(:num)'] = 'backend/reportes/reporte_satisfaccion/$1';
$route['backend/acciones/crear/(:num)/(:any)/(:num)'] = 'backend/acciones/crear/$1/$2/$3';
$route['backend/acciones/crear/seleccionar_form/(:num)/(:num)'] = 'backend/acciones/seleccionar_form/$1/$2';
$route['backend/configuracion/usuario_existe'] = 'backend/configuracion/usuario_existe';

<?php

function eliminarTproceso($proceso_id) {
    ini_set('memory_limit', '2048M');
    ini_set('max_execution_time', 0);
    $CI = & get_instance();
    $CI->load->helper('trazabilidad_helper');
    if (UsuarioBackendSesion::has_rol('seguimiento'))
        show_error('No tiene permisos', 401);
    $proceso = Doctrine::getTable('Proceso')->find($proceso_id);
    if (!$proceso)
        show_error('No tiene permisos', 401);
    $tramites = $proceso->Tramites;
    foreach ($tramites as $tramite) {
        enviar_traza_eliminar_tramite($tramite);
        $tramite->delete();
    }
    exit("ok");
}

<?php

function trazabilidad_id() {
    $CI = &get_instance();
    $respuesta = new stdClass();
    $CI->load->helper('device_helper');
    $api = false;
    $cron = false;
    if ($CI->session->userdata('api_exc')) {
        $api = true;
    }
    if ($CI->session->userdata('cron_exc')) {
        $cron = true;
    }
    if ($api || $cron) {
        $respuesta->canal_inicio = "WEB_PC";
        $respuesta->inicioAsistido = "NO";
        $respuesta->oid = false;
    } else if (UsuarioSesion::usuario_actuando_como_empresa()) {
        $respuesta->canal_inicio = "PRESENCIAL";
        $respuesta->inicioAsistido = "SI";
        $respuesta->oid = false;
    } else if (UsuarioSesion::usuarioMesaDeEntrada() && $CI->session->userdata('id_usuario_ciudadano')) {
        $respuesta->canal_inicio = "PRESENCIAL";
        $respuesta->inicioAsistido = "SI";
        if ($CI->session->userdata('send_oid') == false) {
            $respuesta->oid = false;
        } elseif (TIPO_DE_AUTENTICACION == "CDA" && UsuarioSesion::usuario()->registrado == 1 && $CI->session->userdata('send_oid') == true && $CI->session->userdata('id_usuario_ciudadano_oid_enviar')==1) {
            $respuesta->oid = $CI->session->userdata('id_usuario_ciudadano_oid');
        } else {
            $respuesta->oid = false;
        }
    } else {
        $respuesta->canal_inicio = detect_current_device();
        $respuesta->inicioAsistido = "NO";
        if (UsuarioSesion::usuario()->registrado == 1 && TIPO_DE_AUTENTICACION == "CDA" && $CI->session->userdata('send_oid') == true) {
            $respuesta->oid = UsuarioSesion::usuario()->usuario;            
        } else {
            $respuesta->oid = false;
        }
    }

    return $respuesta;
}

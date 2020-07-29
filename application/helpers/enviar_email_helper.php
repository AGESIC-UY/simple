<?php

function enviar_emails($from, $nombre_largo, $to, $subject, $message, $cc = null, $bcc = null, $file = null) {
    $CI = &get_instance();
    $config['mailtype'] = 'html';
    $config['priority'] = 1;
    $config['charset'] = 'utf-8';
    $CI->email->initialize($config);
    $CI->email->clear(TRUE);
    $CI->email->from($from, $nombre_largo);
    $CI->email->subject($subject);
    $CI->email->message($message);
    if (is_array($to)) {
        $CI->email->to($to);
    } else {
        $CI->email->to(array_emails($to));
    }
    if ($cc) {
        $CI->email->cc(array_emails($cc));
    }
    if ($bcc) {
        $CI->email->bcc(array_emails($bcc));
    }
    if ($file) {
        if (is_array($file)) {
            foreach ($file as $value) {
                $CI->email->attach($value);
            }
        } else {
            $CI->email->attach($file);
        }
    }
    $send = $CI->email->send();
    if (!$send) {
        log_message('ERROR', "send email accion enviar email: " . $CI->email->print_debugger());
    }
    return true;
}

function array_emails($string) {
    $array_string = explode(",", $string);
    $result = array();
    foreach ($array_string as $email) {
        array_push($result, trim($email));
    }
    return $result;
}

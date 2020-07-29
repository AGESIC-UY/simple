<?php

function validacionFile($nombre, $validacion) {
    if (is_file(DIR_VALIDACION . $nombre)) {
        unlink(DIR_VALIDACION . $nombre);
    }
    $nuevoarchivo = fopen(DIR_VALIDACION . $nombre, "w+");
    fwrite($nuevoarchivo, "//Validacion: " . $validacion->nombre . "\n");
    fwrite($nuevoarchivo, "var arg= String(arguments[0]);" . "\n");
    fwrite($nuevoarchivo, "var b64= new java.lang.String(java.util.Base64.decoder.decode(arg));" . "\n");
    fwrite($nuevoarchivo, "var json = JSON.parse(String(b64));" . "\n");
    fwrite($nuevoarchivo, "var variables = JSON.parse(json);" . "\n");
    fwrite($nuevoarchivo, $validacion->contenido);
    fclose($nuevoarchivo);
}

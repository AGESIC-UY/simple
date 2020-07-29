<?php

function procesar_archivo_base_64($tramite_id, $nombre_archivo, $extension_archivo, $datos_base64) {

    $CI = & get_instance();
    $CI->load->helper('filename_concurrencia_helper');
    $datos_base64 = base64_decode($datos_base64);
    $nombre_archivo_subir = obtenerFileName();//$nombre_archivo;

    while(file_exists('uploads/datos/'.$nombre_archivo_subir.'.'.$extension_archivo)) {
      $nombre_archivo_subir .= rand(10, 99);
    }

    if($datos_base64){
      file_put_contents('uploads/datos/'.$nombre_archivo_subir.'.'.$extension_archivo, $datos_base64);
      $file = new File();
      $file->tramite_id = $tramite_id;
      $file->filename = $nombre_archivo_subir.'.'.$extension_archivo;
      $file->tipo = 'dato';
      $file->llave = strtolower(random_string('alnum', 12));
      $file->file_origen = $nombre_archivo.'.'.$extension_archivo;
      $file->save();

      return $nombre_archivo_subir;
    }
    else{
      return false;
    }
}

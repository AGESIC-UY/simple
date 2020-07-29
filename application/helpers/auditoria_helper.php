<?php

function auditar($tabla,$operacion,$idObjt,$usuario) {
    ini_set('memory_limit', '2048M');
    ini_set('max_execution_time', 0);
    $CI = & get_instance();
    $obj = Doctrine::getTable($tabla)->find($idObjt);
    $tablaAuditar = 'Aud'.$tabla;
    $resp =$tablaAuditar::auditar($obj,$usuario,$operacion);
    return $resp;  
}

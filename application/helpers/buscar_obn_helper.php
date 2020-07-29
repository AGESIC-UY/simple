<?php

function obtenerOBN($id, $obn_id, $prof = null, $objetos = null) {
    if (is_null($objetos)) {
        $objetos = array();
        $prof = 0;
    }
    $obn = Doctrine::getTable('ObnStructure')->findOneByIdentificador($obn_id);

    $obj_datos = new stdClass();
    if ($obn) {
        $obn_objeto = Doctrine::getTable(crearNombreClaseObjeto($obn->identificador))->find($id);
        if ($obn_objeto) {
            $json = json_decode($obn->json);
            $obj_datos->OBN = $obn->identificador;
            $obj_datos->id = $obn_objeto->id;
            array_push($objetos, buscarObjetoId($obn_objeto->id, $obn->identificador));
            foreach ($json->OBN_ATR as $value) {
                $attr = $value->nombre;
                if ($value->tipo == "obn") {
                    if ($value->multiple == 1) {
                        $id_obns = explode(",", $obn_objeto->$attr);
                        $obj_datos->$attr = array();
                        $clase = crearNombreClaseObjeto($value->valores);
                        $objeto = $clase::obtenerIn($id_obns);
                        $objeto_negocio["sql"] = $clase;
                        $parametro = $objeto->getParams();
                        $objeto_negocio['parametros'] = $parametro['where'];
                        $objeto_negocio['prof'] = $prof + 1;
                        $objeto_negocio['identificador'] = $value->valores;
                        $obj_datos->$attr = $objeto_negocio;
                    } else {
                        $indice = buscarObjeto($objetos, $obn_objeto->$attr, $value->valores);
                        if ($indice != -1 && $prof >= 1) {
                            $obj_datos->$attr = $objetos[$indice];
                        } else if ($prof >= 1) {
                            $obj_datos->$attr = buscarObjetoId($obn_objeto->$attr, $value->valores);
                        } else {
                            $obj_datos->$attr = json_decode(obtenerOBN($obn_objeto->$attr, $value->valores, $prof + 1, $objetos));
                        }
                    }
                } else if ($value->multiple == 1) {
                    $obj_datos->$attr = $obn_objeto->$attr;
                } else if ($value->tipo == "date") {
                    if ($obn_objeto->$attr != null && $obn_objeto->$attr != "0000-00-00" && $obn_objeto->$attr != "") {
                        $obj_datos->$attr = date("d/m/Y", strtotime($obn_objeto->$attr));
                    } else {
                        $obj_datos->$attr = "";
                    }
                } else {
                    $obj_datos->$attr = $obn_objeto->$attr;
                }
            }
        } else {
            $obj_datos = json_decode(obtenerOBNVacio($obn->identificador));
        }
    }
    return json_encode($obj_datos);
}

function obtenerOBNVacio($obn_id, $profundidad = 0) {

    $obn = Doctrine::getTable('ObnStructure')->findOneByIdentificador($obn_id);

    $obj_datos = new stdClass();
    if ($obn && $profundidad == 0) {
        $obj_datos->OBN = $obn->identificador;
        $obj_datos->id = null;
        $json = json_decode($obn->json);
        foreach ($json->OBN_ATR as $value) {
            $attr = $value->nombre;
            if ($value->tipo == "obn") {
                if ($value->multiple == 1) {
                    $obj_datos->$attr = array();
                    $clase = crearNombreClaseObjeto($value->valores);
                    $objeto_negocio["sql"] = $clase;
                    $objeto_negocio['parametros'] = array();
                    $objeto_negocio['prof'] = -1;
                    $objeto_negocio['identificador'] = $value->valores;
                    $obj_datos->$attr = $objeto_negocio;
                } else {
                    $obn_nueva = json_decode(obtenerOBNVacio($value->valores, 1));
                    $obj_datos->$attr = $obn_nueva;
                }
            } else {
                $obj_datos->$attr = "";
            }
        }
    } else if ($obn && $profundidad == 1) {
        $obj_datos->OBN = $obn->identificador;
        $obj_datos->id = null;
        $json = json_decode($obn->json);
        foreach ($json->OBN_ATR as $value) {
            $attr = $value->nombre;
            $obj_datos->$attr = "";
        }
    }
    return json_encode($obj_datos);
}

function buscarObjeto($objetos, $id, $obn) {
    foreach ($objetos as $key => $value) {
        if ($value->OBN == $obn && $value->id == $id) {
            return $key;
        }
    }
    return -1;
}

function buscarObjetoId($id, $obn_id) {
    $obn = Doctrine::getTable('ObnStructure')->findOneByIdentificador($obn_id);
    $obn_objeto = Doctrine::getTable(crearNombreClaseObjeto($obn->identificador))->find($id);
    $obj_var = new stdClass();
    if ($obn_objeto) {
        $json = json_decode($obn->json);
        $obj_var->OBN = $obn->identificador;
        $obj_var->id = $obn_objeto->id;
        foreach ($json->OBN_ATR as $value) {
            $attr = $value->nombre;
            if ($value->clave_logica == 1) {
                $obj_var->$attr = $obn_objeto->$attr;
            }
        }
    }
    return $obj_var;
}

function crearNombreClaseObjeto($identificador) {
    $clase = "Obn";
    $array = explode("_", $identificador);
    foreach ($array as $value) {
        if (is_numeric($value[0])) {
            
        } else {
            $clase.= strtoupper($value[0]) . strtolower(substr($value, 1));
        }
    }
    return $clase;
}

function crearIdentificadorTabla($identificador) {
    $clase = "obn";
    $array = explode("_", strtolower($identificador));
    foreach ($array as $value) {
        if (is_numeric($value[0])) {
            
        } else {
            $clase.= "_" . $value;
        }
    }
    return $clase;
}

function canDeleteOBN($obn) {
    $conn = Doctrine_Manager::connection();

    $stmt = $conn->prepare('select COUNT(*) from ' . crearIdentificadorTabla($obn->identificador));
    $stmt->execute();

    $datos = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    $cantidad_tareas = $datos[0];
    if ($cantidad_tareas > 0) {
        return false;
    }
    return true;
}

function obnAtributo2($obn, $indice) {
    if ($indice) {
        $indice = json_decode($indice);
        if (isset($indice[0])) {
            $clase = json_decode(obtenerOBN($indice[0], $obn));
        } else {
            $clase = json_decode(obtenerOBNVacio($obn));
        }
    } else {
        $clase = json_decode(obtenerOBNVacio($obn));
    }
    return json_encode($clase);
}

function existeClase($obn) {
    return is_file(DIR_OBN . $obn->id_tabla_interna . ".php");
}
<?php

function jsonVariables($etapaID) {
    $conn = Doctrine_Manager::connection();
    $stmt = $conn->prepare('select c.id campo_id, ds.nombre, ds.valor
            FROM dato_seguimiento ds , campo c
            WHERE ds.nombre = c.nombre and ds.nombre not in ("validacion_error_campo","validacion_error") and ds.etapa_id = ' . $etapaID . '
            UNION(
                select null campo_id , ds.nombre, ds.valor
                FROM dato_seguimiento ds
                WHERE ds.etapa_id = ' . $etapaID . '
                and ds.nombre NOT in (SELECT nombre from campo));');
    $stmt->execute();
    $var = $stmt->fetchAll();
    $variables = new stdClass();
    $datos = new stdClass();
    foreach ($var as $value) {
        $nombre = $value['nombre'];
        $valor = trim($value['valor'], '"');
        if (((is_string($valor) &&
                (is_object(json_decode($valor)) ||
                is_array(json_decode($valor)))))) {
            $valor = json_decode($valor);
        }
        $campo = $value['campo_id'];
        $datos->$nombre = new stdClass();
        $datos->$nombre->valor = $valor;
        $datos->$nombre->campo = $campo;
    }
    $variables = $datos;
    $json = json_encode($variables);
    $result = str_replace('"', '\"', $json);
    return $result;
}

<?php

function eliminarTramite($tramiteID) {
    $query = Doctrine_Query::create()
            ->from('Pago p')
            ->where('p.id_tramite_interno = ?', $tramiteID)
            ->orderBy('p.id desc')
            ->limit(1);
    $pagos = $query->execute();
    if (count($pagos)==0) {
        return true;
    }
    if ($pagos[0]->estado == "cancelado" || $pagos[0]->estado == "rechazado") {
        return true;
    }

    return false;
}
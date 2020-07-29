<?php

function obtenerFileName() {
    ini_set('memory_limit', '2048M');
    ini_set('max_execution_time', 0);
    Doctrine_Manager::connection()->beginTransaction();
    $db = Doctrine_Manager::getInstance()->getCurrentConnection();
    $db->execute("SELECT filename FROM file_concurrencia FOR UPDATE; UPDATE file_concurrencia SET filename = filename + 1;")->fetchColumn(0);
    $result = $db->execute("SELECT filename FROM file_concurrencia;")->fetchColumn(0);
    Doctrine_Manager::connection()->commit();
    $file_name = md5(openssl_random_pseudo_bytes(10)).$result;
    return $file_name;
}

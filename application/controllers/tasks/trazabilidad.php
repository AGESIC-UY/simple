<?php

class Trazabilidad extends CI_Controller {

  public function __construct() {
    parent::__construct();

    if(!$this->input->is_cli_request()) {
      redirect(site_url());
    }
  }

  public function reencolar() {
    $CI =& get_instance();
    $CI->load->library('resque/resque');

    // -- Requiere tener instalada la libreria REDIS de PHP
    $redis = new Redis();
    $redis->connect(REDIS_HOST, REDIS_PORT);

    $matched = preg_grep("/^resque:failed:([a-zA-Z0-9]*)/", $redis->keys("resque:failed:*"));

    if(count($matched) < 1) {
      echo PHP_EOL;
      echo 'No se encontraron trazas fallidas.' . PHP_EOL;
      exit;
    }

    $total_trazas = 0;

    foreach($matched as $row) {
      try {
        $obj = $redis->get($row);

        $redis->delete($row);

        preg_match_all("/\"args\";[a-z:0-9]*:[{a-z0-9:]*;[a-z0-9:]*{([-_\";:a-zA-Z0-9. ,]*)}/", $obj, $data);
        $data = preg_replace("/;[a-z]:[0-9]*/", "", $data[1]);
        $data = $data[0];

        preg_match("/\"tramite_id\":(\"[a-zA-Z0-9:._-]*\")/", $data, $tramite_id);
        preg_match("/\"etapa_id\":(\"[a-zA-Z0-9:._-]*\")/", $data, $etapa_id);
        preg_match("/\"id_transaccion\":(\"[a-zA-Z0-9:._-]*\")/", $data, $id_transaccion);
        preg_match("/\"secuencia\":(\"[a-zA-Z0-9:._-]*\")/", $data, $secuencia);
        preg_match("/\"paso\":(\"[a-zA-Z0-9:._-]*\")/", $data, $paso);
        preg_match("/\"organismo_id\":(\"[a-zA-Z0-9:._-]*\")/", $data, $organismo_id);
        preg_match("/\"oficina_id\":(\"[a-zA-Z0-9:._-]*\")/", $data, $oficina_id);
        preg_match("/\"proceso_externo_id\":(\"[a-zA-Z0-9:._-]*\")/", $data, $proceso_externo_id);
        preg_match("/\"usuario_id\":(\"[a-zA-Z0-9:._-]*\")/", $data, $usuario_id);
        preg_match("/\"pasos_ejecutables\":(\"[a-zA-Z0-9:._-]*\")/", $data, $pasos_ejecutables);
        preg_match("/\"nombre_tarea\":(\"[a-zA-Z0-9:._ ()\[\];-]*\")/", $data, $nombre_tarea);
        preg_match("/\"estado\":(\"[a-zA-Z0-9:._-]*\")/", $data, $estado);
        preg_match("/\"canal_inicio\":(\"[a-zA-Z0-9:._-]*\")/", $data, $canal_inicio);
        preg_match("/\"nombre_paso\":(\"[a-zA-Z0-9:._ ()\[\];-]*\")/", $data, $nombre_paso);
        preg_match("/\"cabezal\":(\"[a-zA-Z0-9:._-]*\")/", $data, $cabezal);
        preg_match("/\"intentos_restantes\":(\"[a-zA-Z0-9:._-]*\")/", $data, $intentos_restantes);

        if(array_key_exists(1, $intentos_restantes)) {
          $intentos_restantes = (int)MAX_INTENTOS_ENVIO_TRAZA - 1;
        }
        else {
          if($intentos_restantes[1] > 0) {
            $intentos_restantes = (int)$intentos_restantes[1] - 1;

            $args = array('tramite_id' => str_replace('"', '', $tramite_id[1]),
                          'etapa_id' => str_replace('"', '', $etapa_id[1]),
                          'id_transaccion' => str_replace('"', '', $id_transaccion[1]),
                          'secuencia' => str_replace('"', '', $secuencia[1]),
                          'paso' => str_replace('"', '', $paso[1]),
                          'organismo_id' => str_replace('"', '', $organismo_id[1]),
                          'oficina_id' => str_replace('"', '', $oficina_id[1]),
                          'proceso_externo_id' => str_replace('"', '', $proceso_externo_id[1]),
                          'usuario_id' => str_replace('"', '', $usuario_id[1]),
                          'pasos_ejecutables' => str_replace('"', '', $pasos_ejecutables[1]),
                          'nombre_tarea' => str_replace('"', '', $nombre_tarea[1]),
                          'estado' => str_replace('"', '', $estado[1]),
                          'canal_inicio' => str_replace('"', '', $canal_inicio[1]),
                          'nombre_paso' => str_replace('"', '', $nombre_paso[1]),
                          'cabezal' => str_replace('"', '', $cabezal[1]),
                          'intentos_restantes' => str_replace('"', '', $intentos_restantes),
            	           );

            Resque::enqueue('default', 'Trazabilidad', $args);
          }
        }

        $total_trazas++;
      }
      catch(Exception $error) {
        echo PHP_EOL;
        echo 'No se pudo procesar la traza ' . $row . PHP_EOL;
      }
    }

    echo PHP_EOL;
    echo 'Se procesaron un total de ' . $total_trazas . ' trazas.' . PHP_EOL;
    exit;
  }
}

<?php

class Encuesta_satisfaccion extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function crear() {
        if(UsuarioSesion::usuario()->registrado) {
            $nueva_encuesta = new ReporteSatisfaccion();
            $nueva_encuesta->usuario_id = UsuarioSesion::usuario()->id;
            $nueva_encuesta->fecha = date("Y-m-d H:i:s");
            $nueva_encuesta->reporte = json_encode($this->input->post('reporte'));
            $nueva_encuesta->save();
            echo 0;
        }
        else {
            echo -1;
        }
    }
}

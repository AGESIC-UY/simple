<?php

class EliminarTproceso extends CI_Controller {

    public function __construct() {
        parent::__construct();

        if (!$this->input->is_cli_request()) {
            redirect(site_url());
        }
    }

    public function eliminar($b64) {
        $id_proceso = base64_decode($b64);
        $CI = & get_instance();
        $CI->load->helper('eliminar_tproceso');
        eliminarTproceso($id_proceso);
    }

}

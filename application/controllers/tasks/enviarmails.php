<?php

class EnviarMails extends CI_Controller {

    public function __construct() {
        parent::__construct();

        if (!$this->input->is_cli_request()) {
            redirect(site_url());
        }
    }

    public function enviar($b64) {
        $data = base64_decode($b64);
        $dat = json_decode($data);
        $CI = & get_instance();
        $CI->load->helper('enviar_email');
        enviar_emails($dat->from, $dat->from_name, $dat->to, $dat->subject, $dat->message, $dat->cc, $dat->bcc, $dat->attach);
    }

}

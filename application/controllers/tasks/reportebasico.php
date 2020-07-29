<?php

class ReporteBasico extends CI_Controller {

    public function __construct() {
        parent::__construct();

        if (!$this->input->is_cli_request()) {
            redirect(site_url());
        }
    }

    //public function generar($reporte_id, $filtro_grupo,$filtro_usuario,$filtro_desde,$filtro_hasta, $email) {
    public function generar($data) {


        //12 horas de ejecucion
        ini_set('max_execution_time', 43200);
        //sin limite de memoria, la libera al terminar
        ini_set('memory_limit', '-1');


        echo '$data:' . $data . PHP_EOL;

        $data = $data . "=";
        $data = base64_decode($data);
        $data = json_decode($data);

        $reporte_id = $data->reporte_id;
        $filtro_desde = $data->filtro_desde;
        $filtro_hasta = $data->filtro_hasta;
        $email = $data->email;
        $updated_at_desde = $data->updated_at_desde;
        $updated_at_hasta = $data->updated_at_hasta;
        $pendiente = $data->pendiente;


        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);
        //genera el reporte
        ob_start();
        $reporte->generar_basico($filtro_desde, $filtro_hasta, $updated_at_desde, $updated_at_hasta, $pendiente);
        $salida = ob_get_contents();
        ob_end_clean();


        $CI = & get_instance();
        $cuenta = $reporte->Proceso->Cuenta;

        if (!$cuenta->correo_remitente) {
            ($CI->config->item('main_domain') == '') ? $from = $cuenta->nombre . '@simple' : $from = $cuenta->nombre . '@' . $CI->config->item('main_domain');
        } else {
            $from = $cuenta->correo_remitente;
        }

        //para que los tildes se visualicen de forma correcta
        $config['mailtype'] = 'html';
        $config['priority'] = 1;
        $config['charset'] = 'utf-8';
        $this->email->initialize($config);
        $this->email->from($from, 'Simple');
        $this->email->to($email);
        $this->email->subject(SUBJECT_REPORTES_EMAIL);
        $this->email->message(BODY_REPORTES_EMAIL);

        $random_password = random_string('alnum', 32);
        $filename = DIRECTORIO_REPORTES_EMAIL . $random_password . $data->reporte_id . '.xls';
        while (file_exists($filename)) {
            $random_password = random_string('alnum', 32);
            $filename = DIRECTORIO_REPORTES_EMAIL . $random_password . $data->reporte_id . '.xls';
        }

        file_put_contents($filename, $salida);

        $this->email->attach($filename);
        $this->email->send();

        //ELIMINA EL ARCHIVO
        unlink($filename);
    }

}

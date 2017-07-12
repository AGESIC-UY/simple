<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Plugins extends CI_Controller {

    public function __construct() {
        parent::__construct();
        UsuarioManagerSesion::force_login();
    }

    public function index() {
        $data['title']='Plugins';
        $data['content']='manager/plugins/index';
        $this->load->view('manager/template',$data);
    }

    public function configurar_pago(){

      $configurar_pago = $this->input->post('configurar_pago');
      $configurar_pago_tipo = $this->input->post('configurar_pago_tipo');

      if ($configurar_pago_tipo == '1'){
        //es eliminar
        $output = shell_exec('crontab -l');
        $configurar_pago = null;
        foreach(preg_split("/((\r?\n)|(\r\n?))/", $output) as $line){
            //si tiene el plgun de pagos configurado
            if (strpos($line, 'pagos conciliacion')){
               $pagos = 'Configurado';
               $configurar_pago = $line;
            }
        }
        if ($configurar_pago){
          $configurar_pago = substr ( $configurar_pago, strpos($configurar_pago, 'php') );
          $command = "crontab -l | grep -v '". $configurar_pago. "'  | crontab -";
          log_message('ERROR',$command);
          shell_exec($command);
        }


      }else if ($configurar_pago_tipo == '2' && $configurar_pago){
        //es crear
        shell_exec('(crontab -l 2>/dev/null; echo "'.$configurar_pago .'") | crontab -');
      }

      redirect('manager/plugins');
    }








}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */

<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Estadisticas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        UsuarioManagerSesion::force_login();
    }

    public function index() {
        redirect('manager/estadisticas/cuentas');
    }

    public function cuentas($cuenta_id = null, $proceso_id = null) {
        if (!$cuenta_id) {
            
            //Seleccionamos los tramites que se han avanzado o tienen datos
            $tramites_arr = Doctrine_Query::create()
                    ->from('Tramite t, t.Etapas e, e.DatosSeguimiento d')
                    ->select('t.id')
                    ->where('t.updated_at > DATE_SUB(NOW(),INTERVAL 30 DAY)')
                    ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')  //Mostramos solo los que se han avanzado o tienen datos
                    ->groupBy('t.id')
                    ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
            

            $data['ntramites'] = count($tramites_arr);
            
            
            $cuentas = Doctrine_Query::create()
                    ->from('Cuenta c, c.Procesos.Tramites t')
                    ->select('c.*, COUNT(t.id) as ntramites')
                    ->whereIn('t.id',empty($tramites_arr)?array(-1):$tramites_arr)
                    ->groupBy('c.id')
                    ->execute();

            $data['cuentas'] = $cuentas;

            $data['title'] = 'Cuentas';
            $data['content'] = 'manager/estadisticas/cuentas';
        }else if(!$proceso_id){
            $cuenta=Doctrine::getTable('Cuenta')->find($cuenta_id);
            
            //Seleccionamos los tramites que se han avanzado o tienen datos
            $tramites_arr = Doctrine_Query::create()
                    ->from('Tramite t, t.Etapas e, e.DatosSeguimiento d, t.Proceso.Cuenta c')
                    ->where('c.id = ?',$cuenta_id)
                    ->andWhere('t.updated_at > DATE_SUB(NOW(),INTERVAL 30 DAY)')
                    ->select('t.id')   
                    ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')  //Mostramos solo los que se han avanzado o tienen datos
                    ->groupBy('t.id')
                    ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
            

            $data['ntramites'] = count($tramites_arr);
            
            
            $procesos = Doctrine_Query::create()
                    ->from('Proceso p, p.Tramites t')
                    ->select('p.*, COUNT(t.id) as ntramites')
                    ->whereIn('t.id',$tramites_arr)
                    ->groupBy('p.id')
                    ->execute();

            $data['procesos'] = $procesos;

            $data['title'] = $cuenta->nombre;
            $data['content'] = 'manager/estadisticas/procesos';
        }else{
            $data['proceso']=Doctrine::getTable('Proceso')->find($proceso_id);
            
            $tramites = Doctrine_Query::create()
                    ->from('Tramite t, t.Proceso p, t.Etapas e, e.DatosSeguimiento d')
                    ->where('p.id = ?',$proceso_id)
                    ->andWhere('t.updated_at > DATE_SUB(NOW(),INTERVAL 30 DAY)')
                    ->orderBy('t.updated_at DESC')
                    ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')  //Mostramos solo los que se han avanzado o tienen datos
                    ->groupBy('t.id')
                    ->execute();

            $data['tramites'] = $tramites;

            $data['title'] = $data['proceso']->nombre;
            $data['content'] = 'manager/estadisticas/tramites';
        }

        $this->load->view('manager/template', $data);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cuentas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        UsuarioManagerSesion::force_login();
    }

    public function index() {
        $data['cuentas']=Doctrine::getTable('Cuenta')->findAll();
        
        $data['title']='Cuentas';
        $data['content']='manager/cuentas/index';
        
        $this->load->view('manager/template',$data);
    }
    
    public function editar($cuenta_id=null){
        if($cuenta_id)
            $cuenta=Doctrine::getTable('Cuenta')->find($cuenta_id);
        else
            $cuenta=new Cuenta();
        
        $data['cuenta']=$cuenta;
        $data['title']=$cuenta->id?'Editar':'Crear';
        $data['content']='manager/cuentas/editar';
        
        $this->load->view('manager/template',$data);
    }
    
    public function editar_form($cuenta_id=null){
        if($cuenta_id)
            $cuenta=Doctrine::getTable('Cuenta')->find($cuenta_id);
        else
            $cuenta=new Cuenta();
        
        $this->form_validation->set_rules('nombre','Nombre','required|url_title');
        $this->form_validation->set_rules('nombre_largo','Nombre largo', 'required');
        
        $respuesta=new stdClass();
        if($this->form_validation->run()==true){
            $cuenta->nombre=$this->input->post('nombre');
            $cuenta->nombre_largo=$this->input->post('nombre_largo');
            $cuenta->mensaje=$this->input->post('mensaje');
            $cuenta->logo=$this->input->post('logo');
            $cuenta->save();
            
            $this->session->set_flashdata('message','Cuenta guardada con éxito.');
            $respuesta->validacion=true;
            $respuesta->redirect=site_url('manager/cuentas');
            
        }else{
            $respuesta->validacion=false;
            $respuesta->errores=validation_errors();
        }
        
        echo json_encode($respuesta);
    }
    
    public function eliminar($cuenta_id){
        $cuenta=Doctrine::getTable('Cuenta')->find($cuenta_id);
        $cuenta->delete();
        
        $this->session->set_flashdata('message','Cuenta eliminada con éxito.');
        redirect('manager/cuentas');
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
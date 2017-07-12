<?php

class Usuarios extends CI_Controller{

    public function __construct() {
        parent::__construct();

        UsuarioManagerSesion::force_login();
    }

    public function index() {
        $data['usuarios']=Doctrine::getTable('UsuarioBackend')->findAll();

        $data['title']='Usuarios Backend';
        $data['content']='manager/usuarios/index';

        $this->load->view('manager/template',$data);
    }

    public function editar($usuario_id=null){
        if($usuario_id)
            $usuario=Doctrine::getTable('UsuarioBackend')->find($usuario_id);
        else
            $usuario=new UsuarioBackend();

        $data['usuario']=$usuario;
        $data['cuentas']=Doctrine::getTable('Cuenta')->findAll();

        $data['title']=$usuario->id?'Editar':'Crear';
        $data['content']='manager/usuarios/editar';

        $this->load->view('manager/template',$data);
    }

    public function editar_form($usuario_id=null){
        if($usuario_id)
            $usuario=Doctrine::getTable('UsuarioBackend')->find($usuario_id);
        else
            $usuario=new UsuarioBackend();

        $this->form_validation->set_rules('email','Correo Electrónico','required|valid_email');
        $this->form_validation->set_rules('nombre','Nombre','required');
        $this->form_validation->set_rules('apellidos','Apellidos', 'required');
        $this->form_validation->set_rules('cuenta_id','Cuenta', 'required');
        $this->form_validation->set_rules('rol','Rol', 'required');
        if(!$usuario->id)
            $this->form_validation->set_rules('password','Contraseña', 'required|min_length[6]|matches[password_confirm]');
        if($this->input->post('password')){
            $this->form_validation->set_rules('password','Contraseña', 'required|min_length[6]|matches[password_confirm]');
            $this->form_validation->set_rules('password_confirm','Confirmar contraseña', 'required');
        }

        $respuesta=new stdClass();
        if($this->form_validation->run()==true){
            $usuario->usuario=$this->input->post('usuario');
            $usuario->email=$this->input->post('email');
            $usuario->nombre=$this->input->post('nombre');
            $usuario->apellidos=$this->input->post('apellidos');
            $usuario->Cuenta=Doctrine::getTable('Cuenta')->find($this->input->post('cuenta_id'));
            $usuario->rol=$this->input->post('rol');
            if($this->input->post('password'))
                $usuario->setPasswordWithSalt ($this->input->post('password'));

            $usuario->save();

            $this->session->set_flashdata('message','Usuario guardado con éxito.');
            $respuesta->validacion=true;
            $respuesta->redirect=site_url('manager/usuarios');

        }else{
            $respuesta->validacion=false;
            $respuesta->errores=validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function eliminar($usuario_id){
        $usuario=Doctrine::getTable('UsuarioBackend')->find($usuario_id);
        $usuario->delete();

        $this->session->set_flashdata('message','Usuario eliminado con éxito.');
        redirect('manager/usuarios');
    }

}

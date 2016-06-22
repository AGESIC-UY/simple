<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Configuracion extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if(UsuarioBackendSesion::usuario()->rol!='super' && UsuarioBackendSesion::usuario()->rol!='configuracion'){
            //echo 'No tiene permisos para acceder a esta seccion.';
            //exit;
            redirect('backend');
        }
    }

    public function index() {
        redirect('backend/configuracion/misitio');
    }


    public function grupos_usuarios() {
        $data['grupos_usuarios'] = Doctrine::getTable('GrupoUsuarios')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);

        $data['title'] = 'Configuración de Grupos de Usuarios';
        $data['content'] = 'backend/configuracion/grupos_usuarios';

        $this->load->view('backend/template', $data);
    }

    public function grupo_usuarios_editar($grupo_usuarios_id = NULL) {
        if ($grupo_usuarios_id) {
            $grupo_usuarios = Doctrine::getTable('GrupoUsuarios')->find($grupo_usuarios_id);

            if ($grupo_usuarios->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
                echo 'No tiene permisos para editar este grupo de usuarios';
                exit;
            }

            $data['grupo_usuarios'] = $grupo_usuarios;
        }

        $data['title'] = 'Configuración de Grupo de Usuarios';
        $data['content'] = 'backend/configuracion/grupo_usuarios_editar';

        $this->load->view('backend/template', $data);
    }

    public function grupo_usuarios_editar_form($grupo_usuarios_id = NULL) {
        $grupo_usuarios=NULL;
        if ($grupo_usuarios_id) {
            $grupo_usuarios = Doctrine::getTable('GrupoUsuarios')->find($grupo_usuarios_id);

            if ($grupo_usuarios->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
                echo 'No tiene permisos para editar este grupo de usuarios';
                exit;
            }
        }

        $this->form_validation->set_rules('nombre', 'Nombre', 'required');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            if (!$grupo_usuarios)
                $grupo_usuarios = new GrupoUsuarios();

            $grupo_usuarios->nombre = $this->input->post('nombre');
            $grupo_usuarios->cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
            $grupo_usuarios->setUsuariosFromArray($this->input->post('usuarios'));
            $grupo_usuarios->save();

            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/configuracion/grupos_usuarios');
        }else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function grupo_usuarios_eliminar($grupo_usuarios_id) {
        $grupo_usuarios = Doctrine::getTable('GrupoUsuarios')->find($grupo_usuarios_id);

        if ($grupo_usuarios->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'No tiene permisos para eliminar este grupo de usuarios';
            exit;
        }

        $grupo_usuarios->delete();

        redirect('backend/configuracion/grupos_usuarios');
    }

    public function usuarios() {
        $data['usuarios'] = Doctrine::getTable('Usuario')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);

        $data['title'] = 'Configuración de Usuarios';
        $data['content'] = 'backend/configuracion/usuarios';

        $this->load->view('backend/template', $data);
    }

    public function usuario_existe() {
      if($this->input->post('usuario')) {
        $usuario_sin_cuenta = Doctrine_Query::create()
            ->from('Usuario u')
            ->where('u.usuario = ? AND u.cuenta_id is NULL', $this->input->post('usuario'))
            ->execute();

        if(count($usuario_sin_cuenta) > 0) {
          $usuario = $usuario_sin_cuenta[0];
          $respuesta = array('usuario_id' => $usuario->id,
                             'usuario' => $usuario->usuario,
                             'nombres' => $usuario->nombres,
                             'apellido_paterno' => $usuario->apellido_paterno,
                             'apellido_materno' => $usuario->apellido_materno,
                             'email' => $usuario->email);
        }
        else {
          $usuario = Doctrine_Query::create()
              ->from('Usuario u')
              ->where('u.usuario = ? AND u.cuenta_id = ?', array($this->input->post('usuario'), UsuarioBackendSesion::usuario()->cuenta_id))
              ->execute();

          if(count($usuario) > 0) {
            $usuario = $usuario[0];
            $respuesta = array('usuario_id' => $usuario->id,
                               'usuario' => $usuario->usuario,
                               'nombres' => $usuario->nombres,
                               'apellido_paterno' => $usuario->apellido_paterno,
                               'apellido_materno' => $usuario->apellido_materno,
                               'email' => $usuario->email);
          }
          else {
              $respuesta = array('error' => 'El usuario ya existe en otra cuenta.');
          }
        }
      }

      echo json_encode($respuesta);
    }

    public function usuario_editar($usuario_id = NULL) {
        if ($usuario_id) {
            $usuario = Doctrine::getTable('Usuario')->find($usuario_id);

            if ($usuario->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
                echo 'Usuario no tiene permisos para editar este usuario.';
                exit;
            }

            $data['usuario'] = $usuario;
        }
        $data['grupos_usuarios'] = Doctrine::getTable('GrupoUsuarios')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);

        $data['title'] = 'Configuración de Usuarios';
        $data['content'] = 'backend/configuracion/usuario_editar';

        $this->load->view('backend/template', $data);
    }

    public function usuario_editar_form($usuario_id = NULL) {
        $usuario=NULL;
        if ($usuario_id) {
            $usuario = Doctrine::getTable('Usuario')->find($usuario_id);

            /*
            if ($usuario->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
                echo 'Usuario no tiene permisos para editar este usuario.';
                exit;
            }
            */
        }

        if(!$usuario){
            $this->form_validation->set_rules('usuario', 'Nombre de Usuario', 'required|alpha_dash|callback_check_existe_usuario');
            $this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[6]|matches[password_confirm]');
        }
        if($this->input->post('password')){
            $this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[6]|matches[password_confirm]');
            $this->form_validation->set_rules('password_confirm', 'Confirmar contraseña');
        }
        $this->form_validation->set_rules('nombres', 'Nombres', 'required');
        $this->form_validation->set_rules('apellido_paterno', 'Apellido Paterno', 'required');
        $this->form_validation->set_rules('apellido_materno', 'Apellido Materno', 'required');
        $this->form_validation->set_rules('email', 'Correo electrónico', 'valid_email|callback_check_existe_email['.($usuario?$usuario->id:'').']');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            if (!$usuario){
                $usuario = new Usuario();
                $usuario->usuario = $this->input->post('usuario');
            }

            if($this->input->post('password')) $usuario->setPasswordWithSalt($this->input->post('password'));
            $usuario->usuario = $this->input->post('usuario');
            $usuario->nombres = $this->input->post('nombres');
            $usuario->apellido_paterno = $this->input->post('apellido_paterno');
            $usuario->apellido_materno = $this->input->post('apellido_materno');
            $usuario->email = $this->input->post('email');
            $usuario->vacaciones = $this->input->post('vacaciones');
            $usuario->setGruposUsuariosFromArray($this->input->post('grupos_usuarios'));
            $usuario->cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
            $usuario->save();

            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/configuracion/usuarios');
        }else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function usuario_eliminar($usuario_id) {
        $usuario = Doctrine::getTable('Usuario')->find($usuario_id);

        if ($usuario->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para eliminar este usuario.';
            exit;
        }

        if($usuario->Etapas->count()){
            $this->session->set_flashdata('message_error','No se puede eliminar usuario ya que participa en tramites existentes en el sistema.');
        }else{
            $usuario->delete();
        }



        redirect('backend/configuracion/usuarios');
    }

    public function backend_usuarios() {
        $data['usuarios'] = Doctrine::getTable('UsuarioBackend')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);

        $data['title'] = 'Configuración de Usuarios';
        $data['content'] = 'backend/configuracion/backend_usuarios';

        $this->load->view('backend/template', $data);
    }

    public function backend_usuario_editar($usuario_id = NULL) {
        if ($usuario_id) {
            $usuario = Doctrine::getTable('UsuarioBackend')->find($usuario_id);

            if ($usuario->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
                echo 'Usuario no tiene permisos para editar este usuario.';
                exit;
            }

            $data['usuario'] = $usuario;
        }

        $data['title'] = 'Configuración de Usuarios';
        $data['content'] = 'backend/configuracion/backend_usuario_editar';

        $this->load->view('backend/template', $data);
    }

    public function backend_usuario_editar_form($usuario_id = NULL) {
        $usuario=NULL;
        if ($usuario_id) {
            $usuario = Doctrine::getTable('UsuarioBackend')->find($usuario_id);

            if ($usuario->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
                echo 'Usuario no tiene permisos para editar este usuario.';
                exit;
            }
        }
        if(!$usuario){
            $this->form_validation->set_rules('email', 'EMail', 'required|valid_email|callback_check_existe_usuario_backend');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|matches[password_confirm]');
        }
        if($this->input->post('password')){
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|matches[password_confirm]');
            $this->form_validation->set_rules('password_confirm', 'Password_confirm');
        }
        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('apellidos', 'Apellidos', 'required');
        $this->form_validation->set_rules('rol', 'Rol', 'required');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            if (!$usuario){
                $usuario = new UsuarioBackend();
                $usuario->email = $this->input->post('email');
            }


            if($this->input->post('password')) $usuario->setPasswordWithSalt($this->input->post('password'));
            $usuario->nombre = $this->input->post('nombre');
            $usuario->apellidos = $this->input->post('apellidos');
            $usuario->rol = $this->input->post('rol');
            $usuario->usuario = $this->input->post('usuario');
            $usuario->cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
            $usuario->save();

            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/configuracion/backend_usuarios');
        }else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function backend_usuario_eliminar($usuario_id) {
        $usuario = Doctrine::getTable('UsuarioBackend')->find($usuario_id);

        if ($usuario->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para eliminar este usuario.';
            exit;
        }

        $usuario->delete();

        redirect('backend/configuracion/backend_usuarios');
    }

    public function misitio(){
        $data['cuenta']=Doctrine::getTable('Cuenta')->find(UsuarioBackendSesion::usuario()->cuenta_id);

        $data['title'] = 'Configuración de Usuarios';
        $data['content'] = 'backend/configuracion/misitio';
        $this->load->view('backend/template', $data);
    }

    public function misitio_form() {
        $this->form_validation->set_rules('nombre_largo', 'Nombre largo', 'required');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $cuenta=Doctrine::getTable('Cuenta')->find(UsuarioBackendSesion::usuario()->cuenta_id);

            $cuenta->nombre_largo=$this->input->post('nombre_largo');
            $cuenta->mensaje=$this->input->post('mensaje');
            $cuenta->logo=$this->input->post('logo');
            $cuenta->save();

            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/configuracion/misitio');
        }else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    function check_existe_usuario($email){
        $u=Doctrine::getTable('Usuario')->findOneByUsuario($email);
        if(!$u)
            return TRUE;

        $this->form_validation->set_message('check_existe_usuario','%s ya existe');
        return FALSE;
    }

    function check_existe_email($email,$usuario_id){
        $u=Doctrine::getTable('Usuario')->findOneByEmail($email);

        if(!$u || ($u && $u->id==$usuario_id))
            return TRUE;

        $this->form_validation->set_message('check_existe_email','%s ya esta en uso por otro usuario');
        return FALSE;

    }

    function check_existe_usuario_backend($email){
        $u=Doctrine::getTable('UsuarioBackend')->findOneByEmail($email);
        if(!$u)
            return TRUE;

        $this->form_validation->set_message('check_existe_usuario_backend','%s ya existe en cuenta: '.$u->Cuenta->nombre);
        return FALSE;

    }

    function ajax_get_usuarios(){
        $query=$this->input->get('query');

        $doctrineQuery=Doctrine_Query::create()
            ->from('Usuario u')
            ->select('u.id, CONCAT(IF(u.open_id,u.rut,u.usuario),IF(u.email IS NOT NULL,CONCAT(" - ",u.email),"")) as text');

        if(strlen($query) >= 3){
            $doctrineQuery->having('text LIKE ?','%'.$query.'%')
                ->where('u.registrado = 1');
        }else{
            $doctrineQuery->where('u.cuenta_id = ?',UsuarioBackendSesion::usuario()->cuenta_id);
        }

        $usuarios=$doctrineQuery->execute();

        header('Content-Type: application/json');
        echo json_encode($usuarios->toArray());

    }

    public function pdi(){
      $pdi = Doctrine_Query::create()
          ->from('Pdi c')
          ->where('c.cuenta_id = ?', UsuarioBackendSesion::usuario()->cuenta_id)
          ->execute();

        $data['pdi'] = $pdi[0];
        $data['title'] = 'Configuración de PDI';
        $data['content'] = 'backend/configuracion/pdi';
        $this->load->view('backend/template', $data);
    }

    public function pdi_form() {
        $this->form_validation->set_rules('sts', 'STS', 'required');
        $this->form_validation->set_rules('policy', 'Policy', 'required');
        $this->form_validation->set_rules('certificado_organismo', 'Certificado_organismo', 'required');
        $this->form_validation->set_rules('clave_organismo', 'Clave_organismo', 'required');
        $this->form_validation->set_rules('certificado_ssl', 'Certificado_ssl', 'required');
        $this->form_validation->set_rules('clave_ssl', 'Clave_SSL', 'required');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $cuenta = Doctrine_Query::create()
                ->from('Pdi c')
                ->where('c.cuenta_id = ?', UsuarioBackendSesion::usuario()->cuenta_id)
                ->execute();

            if(count($cuenta) > 0) {
              $cuenta = $cuenta[0];

              $cuenta->cuenta_id=UsuarioBackendSesion::usuario()->cuenta_id;
              $cuenta->policy=$this->input->post('policy');
              $cuenta->sts=$this->input->post('sts');
              $cuenta->certificado_organismo=$this->input->post('certificado_organismo');
              $cuenta->clave_organismo=$this->input->post('clave_organismo');
              $cuenta->certificado_ssl=$this->input->post('certificado_ssl');
              $cuenta->clave_ssl=$this->input->post('clave_ssl');
              $cuenta->save();
            }
            else {
              $cuenta = new Pdi();
              $cuenta->cuenta_id=UsuarioBackendSesion::usuario()->cuenta_id;
              $cuenta->policy=$this->input->post('policy');
              $cuenta->sts=$this->input->post('sts');
              $cuenta->certificado_organismo=$this->input->post('certificado_organismo');
              $cuenta->clave_organismo=$this->input->post('clave_organismo');
              $cuenta->certificado_ssl=$this->input->post('certificado_ssl');
              $cuenta->clave_ssl=$this->input->post('clave_ssl');
              $cuenta->save();
            }

            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/configuracion/pdi');
        }else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }
}

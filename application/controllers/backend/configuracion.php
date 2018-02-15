<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Configuracion extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if(!UsuarioBackendSesion::has_rol('super') && !UsuarioBackendSesion::has_rol('configuracion')){
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

    private function verificar_seguimiento_grupo($usuario, $deleted){
      //si existe este usuario en el backend con rol seguimiento
      //puede ser que se tenga que eliminar un grupo de la lista
      //de grupos que controla para revisar detalle.
      $u = Doctrine::getTable('UsuarioBackend')->findOneByUsuarioAndCuentaId($usuario->usuario,$usuario->cuenta_id);
      // if ($u && $u->rol == 'seguimiento'){
      if($u && UsuarioBackend::user_has_rol($u->id, 'seguimiento')) {

        //se elimino el usuario del front end
        if ($deleted){
          $u->seg_alc_grupos_usuarios = NULL;
          $u->save();
        }else{

          $save_u = NULL;
          $seg_alc_grupos_usuarios = $u->seg_alc_grupos_usuarios;
          foreach ($seg_alc_grupos_usuarios as $key => $value) {
            if ($value == 'todos'){
              continue;
            }
            $encontre = false;
            foreach ($usuario->GruposUsuarios as $key1 => $value1) {
              if ($value == $value1->id){
                $encontre = true;
                break;
              }
            }

            if (!$encontre){
              //se tiene que eliminar
              unset($seg_alc_grupos_usuarios[$key]);
              $save_u = TRUE;
            }
          }

          if ($save_u == TRUE){
            $u->seg_alc_grupos_usuarios = $seg_alc_grupos_usuarios;
            $u->save();
          }

          //si el usuario no tiene grupos entonces se deja null
          if (count ($usuario->GruposUsuarios) == 0){
            $u->seg_alc_grupos_usuarios = NULL;
            $u->save();
          }
          }
        }
    }

    public function grupo_usuarios_editar_form($grupo_usuarios_id = NULL) {
        $grupo_usuarios=NULL;
        if ($grupo_usuarios_id) {
            $grupo_usuarios = Doctrine::getTable('GrupoUsuarios')->find($grupo_usuarios_id);

            if ($grupo_usuarios->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
                echo 'No tiene permisos para editar este grupo de usuarios';
                exit;
            }

            //para cada usuario se tiene que validar si se tiene que sacar
            //el grupo si el usaurio tienen rol seguimiento igual que como se hace en
            //usuario_editar_form
            //se tiene que detectar que usuarios se eliminaron del grupo
            // y para cada uno de estos usuarios llamar a  $this->verificar_seguimiento_grupo($usuario);
            //comparar $grupo_usuarios_originales con $this->input->post('usuarios')
            $array_usu = $this->input->post('usuarios');
            //previo a salvar nos quedamos con la lista inicial de usuarios del grupo.
            $grupo_usuarios_originales = Doctrine::getTable('GrupoUsuariosHasUsuario')->findByGrupoUsuariosId($grupo_usuarios_id);
        }

        if($grupo_usuarios->nombre !== 'UsuarioMesaDeEntrada'){
          $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        }

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE || $grupo_usuarios->nombre == 'UsuarioMesaDeEntrada') {
            if (!$grupo_usuarios)
                $grupo_usuarios = new GrupoUsuarios();

            if($grupo_usuarios->nombre == 'UsuarioMesaDeEntrada'){
              $grupo_usuarios->nombre = 'UsuarioMesaDeEntrada';
            }
            else {
              $grupo_usuarios->nombre = $this->input->post('nombre');
            }

            $grupo_usuarios->cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
            $grupo_usuarios->setUsuariosFromArray($this->input->post('usuarios'));
            $grupo_usuarios->save();

            //dado los usuarios previos asociado al grupo se compara
            //con los nuevos usuarios el que falte se verifica si
            //se tiene que eliminar el grupo si tiene el rol seguimiento
            foreach($grupo_usuarios_originales as $gu){
              if (!in_array($gu->usuario_id,$array_usu, false)){
                $usuario = $usuario = Doctrine::getTable('Usuario')->find($gu->usuario_id);
                if ($usuario){
                  $this->verificar_seguimiento_grupo($usuario, false);
                }
              }
            }

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
        /*$usuario_sin_cuenta = Doctrine_Query::create()
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
            //el usuario existe en la cuenta del usuario logueado.
            $usuario = $usuario[0];
            $respuesta = array('usuario_id' => $usuario->id,
                               'usuario' => $usuario->usuario,
                               'nombres' => $usuario->nombres,
                               'apellido_paterno' => $usuario->apellido_paterno,
                               'apellido_materno' => $usuario->apellido_materno,
                               'email' => $usuario->email);
          }
          else {
            //el usuario existe en otra cuenta pero igual se lo retorna

            //$respuesta = array('error' => 'El usuario ya existe en otra cuenta.');
          }
        }*/

        //solo se valida si existe en la cuenta del usuario logueado
        //si existe se retorna sino vacio.
        $usuario = Doctrine_Query::create()
            ->from('Usuario u')
            ->where('u.usuario = ? AND u.cuenta_id = ?', array($this->input->post('usuario'), UsuarioBackendSesion::usuario()->cuenta_id))
            ->execute();

        if(count($usuario) > 0) {
          //el usuario existe en la cuenta del usuario logueado.
          $usuario = $usuario[0];
          $respuesta = array('usuario_id' => $usuario->id,
                             'usuario' => $usuario->usuario,
                             'nombres' => $usuario->nombres,
                             'apellido_paterno' => $usuario->apellido_paterno,
                             'apellido_materno' => $usuario->apellido_materno,
                             'email' => $usuario->email);
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
        }

        if(!$usuario){
            // se valida mas que el usuario ya existe para la cuenta_id
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
        // $this->form_validation->set_rules('email', 'Correo electrónico', 'valid_email|callback_check_existe_email['.($usuario?$usuario->id:'').']');
        $this->form_validation->set_rules('email', 'Correo electrónico', 'valid_email');

        $respuesta = new stdClass();
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
            $usuario->acceso_reportes = $this->input->post('acceso_reportes');
            $usuario->setGruposUsuariosFromArray($this->input->post('grupos_usuarios'));
            $usuario->cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
            $usuario->save();

            //si existe este usuario en el backend con rol seguimiento
            //puede ser que se tenga que eliminar un grupo de la lista
            //de grupos que controla para revisar detalle.
            $this->verificar_seguimiento_grupo($usuario, false);

            //si existia un usuario ciudadano (con cuenta null) para este usuario
            //puede ser necesario mudar los tramites de esta cuenta
            $this->verificar_tramites_ciudadano($usuario);

            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/configuracion/usuarios');
        }else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    private function verificar_tramites_ciudadano($usuario){
      if ($usuario && $usuario->cuenta_id){
        //buscamos el usuario ciudadano si existe.
        $usuario_sin_cuenta = Doctrine_Query::create()
            ->from('Usuario u')
            ->where('u.usuario = ? AND u.cuenta_id is NULL', $usuario->usuario)
            ->execute();

        if (count($usuario_sin_cuenta)>0){
          $usuario_sin_cuenta= $usuario_sin_cuenta[0];
          //si existe uusario ciudadano, todas las etapas que participo de esta cuenta se mudan
          $conn = Doctrine_Manager::connection();
          $stmt= $conn->prepare('update etapa et set et.usuario_id = '.$usuario->id.' where et.id IN
          (select e.id from (select * from etapa) e, tarea t, proceso p
          where e.usuario_id = '.$usuario_sin_cuenta->id.' and
          t.id = e.tarea_id and
          p.id = t.proceso_id and
          p.cuenta_id = '.$usuario->cuenta_id.');
          )');
          $stmt->execute();
        }

      }

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

            //si el usaurio existe en el backend con rol seguimiento
            //se le tiene que sacar los grupos al usuario del backend
            $this->verificar_seguimiento_grupo($usuario, true);
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
            //los grupos de usuario del  usuario, se tiene que buscar el usuario en el frontend en la tabla Usuario
            $u = Doctrine::getTable('Usuario')->findOneByUsuarioAndCuentaId($usuario->usuario,$usuario->cuenta_id);
            if ($u){
              $data['grupos_usuarios'] =  $u->GruposUsuarios;
            }else{
              $data['grupos_usuarios'] = null;
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
            $this->form_validation->set_rules('email', 'EMail', 'required|valid_email');
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

        if(!$usuario) {
          $usuarios_existen = Doctrine::getTable('UsuarioBackend')->findByEmail($this->input->post('email'));

          if($usuarios_existen) {
            foreach($usuarios_existen as $usuario_existe) {
              if($usuario_existe->cuenta_id == UsuarioBackendSesion::usuario()->cuenta_id) {
                $respuesta->validacion = false;
                $respuesta->errores = ['El email '. $this->input->post('email') .' ya existe en la cuenta actual'];
                echo json_encode($respuesta);
                return false;
              }
            }
          }
        }

        if ($this->form_validation->run() == TRUE) {
            if (!$usuario) {
              $usuario = new UsuarioBackend();
              $usuario->email = $this->input->post('email');
            }

            if($this->input->post('password')) $usuario->setPasswordWithSalt($this->input->post('password'));
            $usuario->nombre = $this->input->post('nombre');
            $usuario->apellidos = $this->input->post('apellidos');
            $usuario->rol = $this->input->post('rol');
            $usuario->usuario = $this->input->post('usuario');

            $usuario->seg_reasginar = $this->input->post('seg_reasginar');
            $usuario->seg_alc_control_total = $this->input->post('seg_alc_control_total');
            if ($this->input->post('seg_alc_grupos_usuarios')){
              if (in_array('todos',$this->input->post('seg_alc_grupos_usuarios'))){
                $usuario->seg_alc_grupos_usuarios = array('todos');
              }else{
                $usuario->seg_alc_grupos_usuarios = $this->input->post('seg_alc_grupos_usuarios');
              }
            }else{
              $usuario->seg_alc_grupos_usuarios = array();
            }

            $usuario->seg_reasginar_usu = $this->input->post('seg_reasginar_usu');

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

            //show_error(var_dump($this->input->post(NULL, TRUE)));
            $cuenta->codigo_analytics=json_encode($_POST['codigo_analytics']);
            //show_error(var_dump($this->input->post('codigo_analytics', FALSE)));

            $cuenta->save();

            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/configuracion/misitio');
        }else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function parametros(){
      $data['parametros'] = Doctrine::getTable('Parametro')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);

        $data['title'] = 'Parámetros del sistema';
        $data['content'] = 'backend/configuracion/parametros';
        $this->load->view('backend/template', $data);
    }

    public function parametros_form() {
      $parametros = $this->input->post('parametro');

      foreach($parametros as $parametro) {
        if(empty($parametro['clave']) || empty($parametro['valor'])) {
          $respuesta=new stdClass();
          $respuesta->validacion = false;
          $respuesta->errores = '<div class="alert alert-error">Los campos <strong>clave</strong> y <strong>valor</strong> no guardarse estar vacíos.</div>';
          $respuesta->redirect = site_url('backend/configuracion/parametros');
          echo json_encode($respuesta);
          exit;
        }

        if($parametro['id'] != 'null') {
          $parametro_obj = Doctrine_Query::create()
              ->from('Parametro p')
              ->where('p.cuenta_id = ? AND p.clave = ?', array(UsuarioBackendSesion::usuario()->cuenta_id, $parametro['clave']))
              ->fetchOne();
        }
        else {
          $parametro_obj = new Parametro();
          $parametro_obj->cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        }

        $parametro_obj->clave = $parametro['clave'];
        $parametro_obj->valor = $parametro['valor'];
        $parametro_obj->save();
      }

      $respuesta=new stdClass();
      $respuesta->validacion = true;
      $respuesta->redirect = site_url('backend/configuracion/parametros');
      echo json_encode($respuesta);
    }

    public function eliminar_parametro() {
      $parametro_id = $this->input->post('parametro_id');

      $parametro = Doctrine::getTable('Parametro')->find($parametro_id);
      $parametro->delete();
    }

    public function correo(){
        $data['cuenta']=Doctrine::getTable('Cuenta')->find(UsuarioBackendSesion::usuario()->cuenta_id);

        $data['title'] = 'Configuración de Usuarios';
        $data['content'] = 'backend/configuracion/correo';
        $this->load->view('backend/template', $data);
    }

    public function correo_form() {
      $this->form_validation->set_rules('correo_remitente', 'Dirección de remitente', 'required|valid_email');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $cuenta=Doctrine::getTable('Cuenta')->find(UsuarioBackendSesion::usuario()->cuenta_id);

            $cuenta->correo_remitente=$this->input->post('correo_remitente');
            $cuenta->save();

            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/configuracion/correo');
        }else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    //no se utiliza mas esta validación porque se permite para
    //una cuenta el mismo usuario que en otra
    function check_existe_usuario($email){
        $u=Doctrine::getTable('Usuario')->findOneByUsuarioAndCuentaId($email, UsuarioBackendSesion::usuario()->cuenta_id);
        if(!$u)
            return TRUE;

        $this->form_validation->set_message('check_existe_usuario','%s ya existe en la cuenta' );
        return FALSE;
    }

    function check_existe_email($email,$usuario_id){
        $u=Doctrine::getTable('Usuario')->findOneByEmail($email);

        if(!$u || ($u && $u->id==$usuario_id))
            return TRUE;

        $this->form_validation->set_message('check_existe_email','%s ya esta en uso por otro usuario');
        return FALSE;

    }

    //sin uso
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
        }
        else {
          $respuesta->validacion = FALSE;
          $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function monitoreo_trazabilidad(){
        $respuesta_cabezal = $this->trazabilidad_ping_cabezal_monitoreo();
        $respuesta_linea = $this->trazabilidad_ping_linea_monitoreo();

        $data['respuesta_cabezal'] = $respuesta_cabezal;
        $data['respuesta_linea'] = $respuesta_linea;

        $data['title'] = 'Estado Trazabilidad';
        $data['content'] = 'backend/configuracion/monitoreo_trazabilidad';
        $this->load->view('backend/template', $data);
    }

    public function monitoreo_trazabilidad_ajax(){
        $data['title'] = 'Estado Trazabilidad';
        $data['content'] = 'backend/configuracion/monitoreo_trazabilidad_ajax';
        $this->load->view('backend/template', $data);
    }

    public function monitoreo_estado_soap_pdi(){
        $data['lista_pdi'] = Monitoreo::getListaEjecuciones('pdi');
        $data['lista_soap'] = Monitoreo::getListaEjecuciones('soap');

        $data['title'] = 'Estado SOAP y PDI';
        $data['content'] = 'backend/configuracion/monitoreo_estado_soap_pdi';
        $this->load->view('backend/template', $data);
    }

    public function monitoreo_pasarelas(){
        $data['pasarelas'] = Doctrine_Query::create()
          ->from('PasarelaPago p')
          ->where('p.activo = ?', 1)
          ->orderBy('p.metodo')
          ->execute();

        $data['pasarelas_pagos_monitoreo'] = $this->pasarelas_pagos_consulta_monitoreo();
        $data['title'] = 'Estado SOAP y PDI';
        $data['content'] = 'backend/configuracion/monitoreo_pasarelas';
        $this->load->view('backend/template', $data);
    }

    public function monitoreo_pasarelas_ajax(){
        $data['pasarelas'] = Doctrine_Query::create()
          ->from('PasarelaPago p')
          ->where('p.activo = ?', 1)
          ->orderBy('p.metodo')
          ->execute();

        $data['title'] = 'Estado SOAP y PDI';
        $data['content'] = 'backend/configuracion/monitoreo_pasarelas_ajax';
        $this->load->view('backend/template', $data);
    }

    public function monitoreo_notificaciones(){
      $monitoreo_notificaciones = Doctrine_Query::create()
          ->from('MonitoreoNotificaciones')
          ->limit(1)
          ->fetchOne();

        $data['email'] = $monitoreo_notificaciones->email;
        $data['title'] = 'Notificaciones';
        $data['content'] = 'backend/configuracion/monitoreo_notificaciones';
        $this->load->view('backend/template', $data);
    }

    public function monitoreo_notificaciones_form(){
      $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
      $respuesta=new stdClass();

      if ($this->form_validation->run() == TRUE) {
        $monitoreo_notificaciones = Doctrine_Query::create()
            ->from('MonitoreoNotificaciones')
            ->limit(1)
            ->fetchOne();

          if(!empty($monitoreo_notificaciones)){
            $monitoreo_notificaciones->email =  $this->input->post('email');
            $monitoreo_notificaciones->save();
          }
          else {
            $monitoreo_notificaciones = new MonitoreoNotificaciones();
            $monitoreo_notificaciones->email =  $this->input->post('email');
            $monitoreo_notificaciones->save();
          }

          $respuesta->validacion = TRUE;
          $respuesta->redirect = site_url('backend/configuracion/monitoreo_notificaciones');
      }
      else {
          $respuesta->validacion = FALSE;
          $respuesta->errores = validation_errors();
      }

      echo json_encode($respuesta);
    }

    private function trazabilidad_ping_cabezal_monitoreo(){
      try {
        $soap_body_ping_cabezal = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.web.bruto.itramites.agesic.gub.uy/">
                               <soapenv:Header/>
                               <soapenv:Body>
                                  <ws:ping/>
                               </soapenv:Body>
                            </soapenv:Envelope>';

        $soap_header_ping_cabezal = array(
          "Content-type: text/xml;charset=\"utf-8\"",
          "Accept: text/xml",
          "Cache-Control: no-cache",
          "Pragma: no-cache",
          "Content-length: ". strlen($soap_body_ping_cabezal)
        );

        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, WS_AGESIC_TRAZABLIDAD_CABEZAL);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST,           true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $soap_body_ping_cabezal);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $soap_header_ping_cabezal);
        $soap_response_ping_cabezal = curl_exec($soap_do);
        $curl_errno = curl_errno($soap_do);
        $curl_error = curl_error($soap_do);
        $http_code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
        curl_close($soap_do);

        $xml = new SimpleXMLElement($soap_response_ping_cabezal);

        $monitoreo_return = new stdClass();

        if ($curl_errno > 0 || $http_code != 200){

          if($http_code != 0){
            $http_code = ' (httpcode:'.$http_code.')';
          }
          else {
            $http_code = '';
          }

          $monitoreo_return->error = true;
          $monitoreo_return->mensaje = $curl_error.$http_code;
          $monitoreo_return->ws_body = $soap_body_ping_cabezal;
          $monitoreo_return->ws_response = $soap_response_ping_cabezal;

          return $monitoreo_return;
        }
        else if($xml->xpath("//*[local-name() = 'faultcode']")) {
          $error_servicio = $xml->xpath("//*[local-name() = 'faultstring']/text()");
          $monitoreo_return->error = true;
          $monitoreo_return->mensaje = (string)$error_servicio[0];
          $monitoreo_return->ws_body = $soap_body_ping_cabezal;
          $monitoreo_return->ws_response = $soap_response_ping_cabezal;

          return $monitoreo_return;
        }
        else if($curl_errno == 0 &&  $xml->xpath("//*[local-name() = 'return']/text()")[0] == 'OK'){
          $monitoreo_return->error = false;
          $monitoreo_return->mensaje = 'OK';
          $monitoreo_return->ws_body = $soap_body_ping_cabezal;
          $monitoreo_return->ws_response = $soap_response_ping_cabezal;

          return $monitoreo_return;
        }
        else{
          $monitoreo_return->error = true;
          $monitoreo_return->mensaje = 'Error desconocido.';
          $monitoreo_return->ws_body = $soap_body_ping_cabezal;
          $monitoreo_return->ws_response = $soap_response_ping_cabezal;

          return $monitoreo_return;
        }
      }
      catch(Exception $e) {
        $monitoreo_return->error = true;
        $monitoreo_return->mensaje = $e->getMessage();
        $monitoreo_return->ws_body = $soap_body_ping_cabezal;
        $monitoreo_return->ws_response = $soap_response_ping_cabezal;

        return $monitoreo_return;
      }
    }

    private function trazabilidad_ping_linea_monitoreo(){
      try {
        $soap_body_ping_linea = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:lin="http://ws.web.bruto.itramites.agesic.gub.uy/lineaService">
                                   <soapenv:Header/>
                                   <soapenv:Body>
                                      <lin:ping/>
                                   </soapenv:Body>
                                </soapenv:Envelope>';

        $soap_header_ping_linea = array(
          "Content-type: text/xml;charset=\"utf-8\"",
          "Accept: text/xml",
          "Cache-Control: no-cache",
          "Pragma: no-cache",
          "Content-length: ". strlen($soap_body_ping_linea)
        );

        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, WS_AGESIC_TRAZABLIDAD_LINEA);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST,           true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $soap_body_ping_linea);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $soap_header_ping_linea);
        $soap_response_ping_linea = curl_exec($soap_do);
        $curl_errno = curl_errno($soap_do);
        $curl_error = curl_error($soap_do);
        $http_code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
        curl_close($soap_do);

        $xml = new SimpleXMLElement($soap_response_ping_linea);

        $monitoreo_return = new stdClass();

        if ($curl_errno > 0 || $http_code != 200){

          if($http_code != 0){
            $http_code = ' (httpcode:'.$http_code.')';
          }
          else {
            $http_code = '';
          }

          $monitoreo_return->error = true;
          $monitoreo_return->mensaje = $curl_error.$http_code;
          $monitoreo_return->ws_body = $soap_body_ping_linea;
          $monitoreo_return->ws_response = $soap_response_ping_linea;

          return $monitoreo_return;
        }
        else if($xml->xpath("//*[local-name() = 'faultcode']")) {
          $error_servicio = $xml->xpath("//*[local-name() = 'faultstring']/text()");
          $monitoreo_return->error = true;
          $monitoreo_return->mensaje = (string)$error_servicio[0];
          $monitoreo_return->ws_body = $soap_body_ping_linea;
          $monitoreo_return->ws_response = $soap_response_ping_linea;

          return $monitoreo_return;
        }
        else if($curl_errno == 0 && $xml->xpath("//*[local-name() = 'return']/text()")[0] == 'OK'){
          $monitoreo_return->error = false;
          $monitoreo_return->mensaje = 'OK';
          $monitoreo_return->ws_body = $soap_body_ping_linea;
          $monitoreo_return->ws_response = $soap_response_ping_linea;

          return $monitoreo_return;
        }
        else{
          $monitoreo_return->error = true;
          $monitoreo_return->mensaje = 'Error desconcido';
          $monitoreo_return->ws_body = $soap_body_ping_linea;
          $monitoreo_return->ws_response = $soap_response_ping_linea;

          return $monitoreo_return;
        }
      }
      catch(Exception $e) {
        $monitoreo_return->error = true;
        $monitoreo_return->mensaje = $e->getMessage();
        $monitoreo_return->ws_body = $soap_body_ping_linea;
        $monitoreo_return->ws_response = $soap_response_ping_linea;

        return $monitoreo_return;
      }
    }

    public function pasarelas_pagos_consulta_monitoreo(){
      $pasarelas_pagos = Doctrine_Query::create()
          ->from('PasarelaPago p')
          ->where('p.activo = ?', 1)
          ->orderBy('p.metodo')
          ->execute();

        $pasaraleas_array_return = array();

        foreach ($pasarelas_pagos as $pasarela_pago) {

            if($pasarela_pago->metodo === 'antel'){


              $pasarela = $pasarela_pago->PasarelaPagoAntel;

              try{
                $ws_body = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:con="http://tempuri.org/wsorg/Consultas">';
                if (!empty(SOAP_PASARELA_PAGO_CONSULTA)){
                    if (SOAP_PASARELA_PAGO_CONSULTA == '1.1'){
                      $ws_body = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:con="http://tempuri.org/wsorg/Consultas">';
                    }
                }
                $ws_body = $ws_body . '<soap:Header/>
                   <soap:Body>
                      <con:ObtenerDatosTransaccion>
                         <con:pIdSolicitud>-1</con:pIdSolicitud>
                         <con:pIdTramite>-1</con:pIdTramite>
                         <con:pClave>-1</con:pClave>
                      </con:ObtenerDatosTransaccion>
                   </soap:Body>
                </soap:Envelope>';

                $ws_header = array(
                    "Content-type: text/xml;charset=\"utf-8\"",
                    "Accept: text/xml",
                    "Cache-Control: no-cache",
                    "Pragma: no-cache",
                    "Content-length: ".strlen($ws_body),
                    "SOAPAction: \"http://tempuri.org/wsorg/Consultas/ObtenerDatosTransaccion\""
                );


                $ws_do = curl_init();
                curl_setopt($ws_do, CURLOPT_URL, WS_PASARELA_PAGO_CONSULTA);
                curl_setopt($ws_do, CURLOPT_CONNECTTIMEOUT, PASARELA_PAGO_TIMEOUT_CONEXION);
                curl_setopt($ws_do, CURLOPT_TIMEOUT,        PASARELA_PAGO_TIMEOUT_RESPUESTA);
                curl_setopt($ws_do, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ws_do, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ws_do, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ws_do, CURLOPT_POST,           true);
                curl_setopt($ws_do, CURLOPT_SSLVERSION,     1);
                curl_setopt($ws_do, CURLOPT_SSLCERT, UBICACION_CERTIFICADOS_PASARELA.$pasarela->certificado);
                curl_setopt($ws_do, CURLOPT_SSLCERTPASSWD, $pasarela->pass_clave_certificado);
                curl_setopt($ws_do, CURLOPT_SSLKEY, UBICACION_CERTIFICADOS_PASARELA.$pasarela->clave_certificado);

                curl_setopt($ws_do, CURLOPT_ENCODING,       'gzip');
                curl_setopt($ws_do, CURLOPT_POSTFIELDS,     $ws_body);
                curl_setopt($ws_do, CURLOPT_HTTPHEADER,     $ws_header);

                if (!empty(PROXY_PASARELA_PAGO)){
                  curl_setopt($ws_do, CURLOPT_PROXY, PROXY_PASARELA_PAGO);
                }

                $ws_response = curl_exec($ws_do);
                $curl_errno = curl_errno($ws_do); // -- Codigo de error
                $curl_error = curl_error($ws_do); // -- Descripcion del error
                $http_code = curl_getinfo($ws_do, CURLINFO_HTTP_CODE); // -- Codigo respuesta HTTP

                curl_close($ws_do);

                $xml = new SimpleXMLElement($ws_response);
                $respuesta = $xml->xpath("//*[local-name() = 'Mensaje']/text()");

                $pasarela_return = new stdClass();

                if($curl_errno > 0 || $http_code != 200) {
                  $pasarela_return->id = $pasarela_pago->id;
                  $pasarela_return->pasarela_nombre = $pasarela_pago->nombre;
                  $pasarela_return->error_monitoreo = true;
                  $pasarela_return->error_texto_monitoreo = $curlerror;
                  $pasarela_return->ws_body_monitoreo = $ws_body;
                  $pasarela_return->ws_response_monitoreo = $ws_response;
                  $pasarela_return->certificado_ssl_monitoreo = $pasarela->certificado;
                  $pasarela_return->url = WS_PASARELA_PAGO_CONSULTA;

                  array_push($pasaraleas_array_return, $pasarela_return);
                }

                else if($curl_errno == 0 && $respuesta[0] == 'ERROR EN WEBSERVICE'){
                  $pasarela_return->id = $pasarela_pago->id;
                  $pasarela_return->pasarela_nombre = $pasarela_pago->nombre;
                  $pasarela_return->error_monitoreo = false;
                  $pasarela_return->error_texto_monitoreo = 'OK';
                  $pasarela_return->ws_body_monitoreo = $ws_body;
                  $pasarela_return->ws_response_monitoreo = $ws_response;
                  $pasarela_return->certificado_ssl_monitoreo = $pasarela->certificado;
                  $pasarela_return->url = WS_PASARELA_PAGO_CONSULTA;

                  array_push($pasaraleas_array_return, $pasarela_return);
                }
                else{
                  $pasarela_return->id = $pasarela_pago->id;
                  $pasarela_return->pasarela_nombre = $pasarela_pago->nombre;
                  $pasarela_return->error_monitoreo = true;
                  $pasarela_return->error_texto_monitoreo = 'Error desconocido';
                  $pasarela_return->ws_body_monitoreo = $ws_body;
                  $pasarela_return->ws_response_monitoreo = $ws_response;
                  $pasarela_return->certificado_ssl_monitoreo = $pasarela->certificado;
                  $pasarela_return->url = WS_PASARELA_PAGO_CONSULTA;

                  array_push($pasaraleas_array_return, $pasarela_return);
                }

              }
              catch(Exception $e) {
                $pasarela_return->id = $pasarela_pago->id;
                $pasarela_return->pasarela_nombre = $pasarela_pago->nombre;
                $pasarela_return->error_monitoreo = true;
                $pasarela_return->error_texto_monitoreo = $e->getMessage();
                $pasarela_return->ws_body_monitoreo = $ws_body;
                $pasarela_return->ws_response_monitoreo = $ws_response;
                $pasarela_return->certificado_ssl_monitoreo = $pasarela->certificado;
                $pasarela_return->url = WS_PASARELA_PAGO_CONSULTA;

                array_push($pasaraleas_array_return, $pasarela_return);
              }
            }

            if($pasarela_pago->metodo === 'generico'){
                $pasarela_generica = $pasarela_pago->PasarelaPagoGenerica;

                $variable_evaluar = $pasarela_generica->variable_evaluar;
                $variable_idsol = $pasarela_generica->variable_idsol;
                $variable_idestado = $pasarela_generica->variable_idestado;
                $codigo_operacion_soap = $pasarela_generica->codigo_operacion_soap_consulta;

                $operacion = Doctrine_Query::create()
                            ->from('WsOperacion o')
                            ->where('o.codigo = ?', $codigo_operacion_soap)
                            ->fetchOne();

                $servicio = Doctrine::getTable('WsCatalogo')->find($operacion->catalogo_id);

                try {
                    $soap_header = array(
                        "Content-type: text/xml;charset=\"utf-8\"",
                        "Accept: text/xml",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache",
                        "Content-length: ".strlen($operacion->soap),
                        "SOAPAction: ".$operacion->operacion,
                        "User-Agent: Mozilla/5.0"
                    );

                    if ($servicio->requiere_autenticacion == 1) {
                      //servicio soap con autenticacion
                      switch($servicio->requiere_autenticacion_tipo) {
                        case 'autenticacion_basica':
                          $soap_do = curl_init();
                          curl_setopt($soap_do, CURLOPT_URL, $servicio->endpoint_location);
                          curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, $servicio->conexion_timeout);
                          curl_setopt($soap_do, CURLOPT_TIMEOUT,        $servicio->respuesta_timeout);
                          curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
                          curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
                          curl_setopt($soap_do, CURLOPT_POST,           true);
                          curl_setopt($soap_do, CURLOPT_ENCODING,       'gzip');
                          curl_setopt($soap_do, CURLOPT_VERBOSE, true);

                          if(!empty($servicio->autenticacion_basica_cert)) {
                            curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, true);
                            curl_setopt($soap_do, CURLOPT_CAINFO, UBICACION_CERTIFICADOS_SOAP . $servicio->autenticacion_basica_cert);
                          }
                          else {
                            curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
                          }

                          curl_setopt($soap_do, CURLOPT_USERPWD, $servicio->autenticacion_basica_user . ':' . $servicio->autenticacion_basica_pass);
                          curl_setopt($soap_do, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                          break;
                        case 'autenticacion_mutua':
                          $soap_do = curl_init();
                          curl_setopt($soap_do, CURLOPT_URL, $servicio->endpoint_location);
                          curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, $servicio->conexion_timeout);
                          curl_setopt($soap_do, CURLOPT_TIMEOUT,        $servicio->respuesta_timeout);
                          curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
                          curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, true);
                          curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
                          curl_setopt($soap_do, CURLOPT_POST,           true);
                          curl_setopt($soap_do, CURLOPT_ENCODING,       'gzip');

                          //el cliente envia la peticion firmada
                          curl_setopt($soap_do, CURLOPT_SSLCERT, UBICACION_CERTIFICADOS_SOAP . $servicio->autenticacion_mutua_client);
                          curl_setopt($soap_do, CURLOPT_SSLCERTPASSWD, $servicio->autenticacion_mutua_client_pass);
                          curl_setopt($soap_do, CURLOPT_SSLKEY, UBICACION_CERTIFICADOS_SOAP.$servicio->autenticacion_mutua_client_key);

                          //verificamos la respuesta
                          curl_setopt($soap_do, CURLOPT_CAINFO, UBICACION_CERTIFICADOS_SOAP . $servicio->autenticacion_mutua_server);
                          if(!empty($servicio->autenticacion_mutua_user) && !empty($servicio->autenticacion_mutua_pass)) {
                            curl_setopt($soap_do, CURLOPT_USERPWD, $servicio->autenticacion_mutua_user . ':' . $servicio->autenticacion_mutua_pass);
                          }

                          curl_setopt($soap_do, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                          break;
                        case 'autenticacion_token':
                          // --
                          break;
                      }

                      if (!empty(PROXY_WS)){
                        curl_setopt($soap_do, CURLOPT_PROXY, PROXY_WS);
                      }
                      curl_setopt($soap_do, CURLOPT_POSTFIELDS, $operacion->soap);
                      curl_setopt($soap_do, CURLOPT_HTTPHEADER, $soap_header);
                      $soap_response = curl_exec($soap_do);
                      $curl_errno = curl_errno($soap_do); // -- Codigo de error
                      $curl_error = curl_error($soap_do); // -- Descripcion del error
                      $http_code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE); // -- Codigo respuesta HTTP
                      curl_close($soap_do);
                    }
                    else {
                      //servicio soap sin autenticacion
                      $soap_do = curl_init();
                      curl_setopt($soap_do, CURLOPT_URL, $servicio->endpoint_location);
                      curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, $servicio->conexion_timeout);
                      curl_setopt($soap_do, CURLOPT_TIMEOUT,        $servicio->respuesta_timeout);
                      curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
                      curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
                      curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
                      curl_setopt($soap_do, CURLOPT_POST,           true);
                      curl_setopt($soap_do, CURLOPT_ENCODING,       'gzip');

                      curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $operacion->soap);
                      curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $soap_header);

                      if (!empty(PROXY_WS)){
                        curl_setopt($soap_do, CURLOPT_PROXY, PROXY_WS);
                      }

                      $soap_response = curl_exec($soap_do);
                      $curl_errno = curl_errno($soap_do); // -- Codigo de error
                      $curl_error = curl_error($soap_do); // -- Descripcion del error
                      $http_code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE); // -- Codigo respuesta HTTP
                      curl_close($soap_do);
                    }

                    $pasarela_return = new stdClass();

                    if($curl_errno > 0 || $http_code != '200') {

                      if($http_code != 0){
                        $http_code = ' (httpcode:'.$http_code.')';
                      }
                      else {
                        $http_code = '';
                      }

                      $curl_error = $curl_error.$http_code;
                      $pasarela_return->id = $pasarela_pago->id;
                      $pasarela_return->pasarela_nombre = $pasarela_pago->nombre;
                      $pasarela_return->error_monitoreo = true;
                      $pasarela_return->error_texto_monitoreo = $curl_error;
                      $pasarela_return->ws_body_monitoreo = $operacion->soap;
                      $pasarela_return->ws_response_monitoreo = $soap_response;
                      $pasarela_return->certificado_ssl_monitoreo = '-';
                      $pasarela_return->url = $servicio->endpoint_location;

                      array_push($pasaraleas_array_return, $pasarela_return);
                    }

                    else{
                      $pasarela_return->id = $pasarela_pago->id;
                      $pasarela_return->pasarela_nombre = $pasarela_pago->nombre;
                      $pasarela_return->error_monitoreo = false;
                      $pasarela_return->error_texto_monitoreo = 'OK';
                      $pasarela_return->ws_body_monitoreo = $operacion->soap;
                      $pasarela_return->ws_response_monitoreo = $soap_response;
                      $pasarela_return->certificado_ssl_monitoreo = '-';
                      $pasarela_return->url = $servicio->endpoint_location;

                      array_push($pasaraleas_array_return, $pasarela_return);
                    }
                }
                catch (Exception $e) {
                  $pasarela_return->id = $pasarela_pago->id;
                  $pasarela_return->pasarela_nombre = $pasarela_pago->nombre;
                  $pasarela_return->error_monitoreo = true;
                  $pasarela_return->error_texto_monitoreo = $e->getMessage();
                  $pasarela_return->ws_body_monitoreo = $operacion->soap;
                  $pasarela_return->ws_response_monitoreo = $soap_response;
                  $pasarela_return->certificado_ssl_monitoreo = '-';
                  $pasarela_return->url = $servicio->endpoint_location;

                  array_push($pasaraleas_array_return, $pasarela_return);

                }

            }

        }

        return $pasaraleas_array_return;
    }

    public function trazabildiad_envio_guid(){

        $data['cuenta'] =  Doctrine::getTable('Cuenta')->find(UsuarioBackendSesion::usuario()->cuenta_id);
        $data['title'] = 'Notificaciones';
        $data['content'] = 'backend/configuracion/trazabildiad_envio_guid';
        $this->load->view('backend/template', $data);
    }

    public function trazabildiad_envio_guid_form(){

      $envio_guid_automatico = $this->input->post('envio_guid_automatico');

      $respuesta = new stdClass();

      if($envio_guid_automatico){
        $this->form_validation->set_rules('asunto_email_guid','Asunto','required');
        $this->form_validation->set_rules('cuerpo_email_guid','Cuerpo del email','required');

        if($this->form_validation->run() == true){
          $asunto_email_guid = $this->input->post('asunto_email_guid');
          $cuerpo_email_guid = $this->input->post('cuerpo_email_guid');

          $cuenta = Doctrine::getTable('Cuenta')->find(UsuarioBackendSesion::usuario()->cuenta_id);

          $cuenta->envio_guid_automatico = $envio_guid_automatico;
          $cuenta->asunto_email_guid = $asunto_email_guid;
          $cuenta->cuerpo_email_guid = $cuerpo_email_guid;
          $cuenta->save();

          $respuesta->validacion = true;
          $respuesta->redirect = site_url('backend/configuracion/trazabildiad_envio_guid');
        }
        else{
          $respuesta->validacion = false;
          $respuesta->errores = validation_errors();
        }
      }
      else{
          $cuenta = Doctrine::getTable('Cuenta')->find(UsuarioBackendSesion::usuario()->cuenta_id);

          $cuenta->envio_guid_automatico = $envio_guid_automatico;
          $cuenta->asunto_email_guid = null;
          $cuenta->cuerpo_email_guid = null;
          $cuenta->save();

          $respuesta->validacion = true;
          $respuesta->redirect = site_url('backend/configuracion/trazabildiad_envio_guid');
      }

      echo json_encode($respuesta);
    }
}

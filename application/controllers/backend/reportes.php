<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reportes extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if(!UsuarioBackendSesion::has_rol('super') && !UsuarioBackendSesion::has_rol('gestion')){
            redirect('backend');
        }

        $this->load->helper('excel_helper');
    }

    public function index(){
        $procesos = Doctrine_Query::create()
            ->from('Proceso p')
            ->where('p.cuenta_id = ?', UsuarioBackendSesion::usuario()->cuenta_id)
            ->where('p.nombre != ?', 'BLOQUE')
            ->orderBy('p.id')
            ->execute();

        $data['procesos']=$procesos;

        $data['title'] = 'Gestión';
        $data['content'] = 'backend/reportes/index';

        $this->load->view('backend/template', $data);
    }

    public function listar($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);
        $reportes = Doctrine_Query::create()
            ->from('Reporte r')
            ->where('r.proceso_id = ?', $proceso_id)
            ->execute();

        $grupos = Doctrine::getTable('GrupoUsuarios')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar los formularios de este proceso';
            exit;
        }
        $data['proceso'] = $proceso;
        $data['reportes'] = $reportes;
        $data['grupos'] = $grupos;

        $data['title'] = 'Documentos';
        $data['content'] = 'backend/reportes/listar';

        $this->load->view('backend/template', $data);
    }

    public function generar_basico($reporte_id) {

      $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

      if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
          echo 'Usuario no tiene permisos';
          exit;
      }

      $cantidad = $reporte->contar_generar_basico($this->input->get('filtro_desde'), $this->input->get('filtro_hasta'));
      $cuenta_segun_dominio = Cuenta::cuentaSegunDominio();

      $cantidad_maxima = Doctrine_Query::create()
          ->from('Parametro p')
          ->where('p.cuenta_id = ? AND p.clave = ?', array($cuenta_segun_dominio->id, 'reporte_basico_cantidad_maxima'))
          ->fetchOne();

     $respuesta=new stdClass();

      if (!$cantidad_maxima){
        $cantidad_maxima = -1;
      }
      else{
        $cantidad_maxima = $cantidad_maxima->valor;
      }

      if ($cantidad >  $cantidad_maxima){
        $respuesta->email = true;
      }
      else{
        $respuesta->email = false;
      }

      echo json_encode($respuesta);
    }

    //operacion que se llama para validar si se tiene que generar por email o se genera online
    //al realizar clic en el boton generar se llama a esta funcion que retorna true si se debe generar y enviar por email o false
    //si se puede generar online
    public function generar_completo($reporte_id) {

      $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

      if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
          echo 'Usuario no tiene permisos';
          exit;
      }

      $cantidad = $reporte->contar_generar_completo($this->input->get('filtro_grupo'), $this->input->get('filtro_usuario'), $this->input->get('filtro_desde'), $this->input->get('filtro_hasta'));
      $cuenta_segun_dominio = Cuenta::cuentaSegunDominio();

      $cantidad_maxima = Doctrine_Query::create()
          ->from('Parametro p')
          ->where('p.cuenta_id = ? AND p.clave = ?', array($cuenta_segun_dominio->id, 'reporte_completo_cantidad_maxima'))
          ->fetchOne();

     $respuesta=new stdClass();
      if (!$cantidad_maxima){
        $cantidad_maxima = -1;
      }else{
        $cantidad_maxima = $cantidad_maxima->valor;
      }
      if ($cantidad >  $cantidad_maxima){
        $respuesta->email = true;
      }else{
        $respuesta->email = false;
      }

      echo json_encode($respuesta);

    }

    //metodo que atiende el boton generar en el caso que se este en generación
    //del reporte completo por email
    public function  ver_completo_email($reporte_id){
      $reporte = Doctrine::getTable('Reporte')->find($reporte_id);
      if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
          echo 'Usuario no tiene permisos';
          exit;
      }
      //llama al command line para que se ejecute en el background
      $filtro_grupo= $this->input->get('filtro_grupo');
      $filtro_usuario= $this->input->get('filtro_usuario');
      $filtro_desde= $this->input->get('filtro_desde');
      $filtro_hasta= $this->input->get('filtro_hasta');
      $email= $this->input->get('email');

      //ejecuta el comando en background con dev/null &
      $arr = array(
        "reporte_id" => $reporte_id,
        "filtro_grupo" => $filtro_grupo,
        "filtro_usuario" => $filtro_usuario,
        "filtro_desde" => $filtro_desde,
        "filtro_hasta" => $filtro_hasta,
        "email" => $email,
      );

      //$comando = 'php index.php tasks/reportecompleto generar "'. $reporte_id.'"  "'. $filtro_grupo.'" "'. $filtro_usuario.'" "'. $filtro_desde.'" "'. $filtro_hasta.'" "'. $email.'" > /dev/null &';


      $data =json_encode($arr);
      $data = str_replace('=', '', base64_encode($data));
      $comando = 'php index.php tasks/reportecompleto generar "'. $data.'" > /dev/null &';
      //show_error($comando);
      exec($comando);
    }

    //metodo que atiende el boton generar en el caso que se este en generación
    //del reporte completo por email
    public function  ver_basico_email($reporte_id){
      $reporte = Doctrine::getTable('Reporte')->find($reporte_id);
      if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
          echo 'Usuario no tiene permisos';
          exit;
      }
      //llama al command line para que se ejecute en el background
      $filtro_desde= $this->input->get('filtro_desde');
      $filtro_hasta= $this->input->get('filtro_hasta');
      $email= $this->input->get('email');

      //ejecuta el comando en background con dev/null &
      $arr = array(
        "reporte_id" => $reporte_id,
        "filtro_desde" => $filtro_desde,
        "filtro_hasta" => $filtro_hasta,
        "email" => $email,
      );

      //$comando = 'php index.php tasks/reportecompleto generar "'. $reporte_id.'"  "'. $filtro_grupo.'" "'. $filtro_usuario.'" "'. $filtro_desde.'" "'. $filtro_hasta.'" "'. $email.'" > /dev/null &';

      $data =json_encode($arr);
      $data = str_replace('=', '', base64_encode($data));
      $comando = 'php index.php tasks/reportebasico generar "'. $data.'" > /dev/null &';
      //show_error($comando);
      exec($comando);
    }

    //metodo que atiende la generación del reporte cuando se genera online
    public function ver($reporte_id) {
        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos';
            exit;
        }

        if($reporte->tipo == 'completo') {
          $reporte->generar_completo($this->input->get('filtro_grupo'), $this->input->get('filtro_usuario'), $this->input->get('filtro_desde'), $this->input->get('filtro_hasta'));
        }
        else {
          $reporte->generar_basico($this->input->get('filtro_desde'), $this->input->get('filtro_hasta'));
        }
    }

    public function ver_reporte_usuario() {
        $grupo = $this->input->get('filtro_grupo');
        $usuario = $this->input->get('filtro_usuario');
        $desde = $this->input->get('filtro_desde');
        $hasta = $this->input->get('filtro_hasta');

        set_time_limit(1600);
        ini_set('memory_limit', '-1');

        $CI=& get_instance();
        $CI->load->library('Excel_XML');

        $header=array('Tramite Id','Proceso Nombre','Etapa', 'Estado', 'Fecha Creacion','Fecha Finalizada', 'Usuario', 'Nombre');

        $excel[]=$header;

        $tramites_query=Doctrine_Query::create()
                ->select('t.*, p.*, e.*')
                ->from('Tramite t, t.Proceso p, t.Etapas e, e.Usuario u')
                ->where('u.cuenta_id = ?', UsuarioBackendSesion::usuario()->cuenta_id)
                ->andWhere('u.registrado = 1');

        if($grupo) {
          $tramites_query->andWhere('e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ?', array('^' . $grupo . ',', ',' . $grupo . ',', '^' . $grupo . '$', ',' . $grupo . '$'));
        }

        if($usuario) {
          $tramites_query->andWhere('u.id = ?', $usuario);
        }

        if($desde) {
          $desde = $desde . ' 00:00:00';
          $tramites_query->andWhere('e.created_at >= ?', date("Y-m-d H:i:s", strtotime($desde)));
        }

        if($hasta) {
          $hasta = $hasta . ' 23:59:59';
          $tramites_query->andWhere('e.created_at <= ?', date("Y-m-d H:i:s", strtotime($hasta)));
        }

        $tramites = $tramites_query->orderBy('t.id','desc')
        ->orderBy('e.created_at','desc')
        ->fetchArray();

        foreach($tramites as $t) {
          foreach($t['Etapas'] as $etapa) {
            $etapa_linea = Doctrine_Core::getTable('Etapa')->find($etapa['id']);

            $usuario_asignado = quitar_caracteres_especiales($etapa_linea->Usuario->usuario);

            $tramite_id = $t['id'];
            $proceso_nombre = quitar_caracteres_especiales($t['Proceso']['nombre']);
            $etapa = quitar_caracteres_especiales($etapa_linea->Tarea->nombre);
            $fecha_creacion =  $etapa_linea->created_at;
            $fecha_finalizada = $etapa_linea->ended_at;
            $etapa_estado = $etapa_linea->pendiente?'pendiente':'completado';
            $nombre_apellido = quitar_caracteres_especiales($etapa_linea->Usuario->nombres.' '.$etapa_linea->Usuario->apellido_paterno);

            $row = array(
                          $tramite_id,
                          $proceso_nombre,
                          $etapa,
                          $etapa_estado,
                          $fecha_creacion,
                          $fecha_finalizada,
                          $usuario_asignado,
                          $nombre_apellido
                        );

            $excel[]=$row;
          }
        }
        $CI->excel_xml->addArray($excel);
        $CI->excel_xml->generateXML('actividad_usuarios_'.date("m_d_y"));
    }

    public function crear($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'No tiene permisos para crear este documento';
            exit;
        }

        $data['edit'] = FALSE;
        $data['proceso'] = $proceso;
        $data['title'] = 'Edición de Documento';
        $data['content'] = 'backend/reportes/editar';

        $this->load->view('backend/template', $data);
    }

    public function editar($reporte_id) {
        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'No tiene permisos para editar este documento';
            exit;
        }

        $data['reporte'] = $reporte;
        $data['edit'] = TRUE;
        $data['proceso'] = $reporte->Proceso;
        $data['title'] = 'Edición de Reporte';
        $data['content'] = 'backend/reportes/editar';

        $this->load->view('backend/template', $data);
    }

    public function editar_form($reporte_id = NULL) {
        $reporte = NULL;
        if ($reporte_id) {
            $reporte = Doctrine::getTable('Reporte')->find($reporte_id);
        } else {
            $reporte = new Reporte();
            $reporte->proceso_id = $this->input->post('proceso_id');
        }

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este documento.';
            exit;
        }

        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('campos', 'Campos[]', 'required');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $reporte->nombre = $this->input->post('nombre');
            $reporte->tipo = $this->input->post('tipo');
            $reporte->campos = $this->input->post('campos');
            $reporte->save();

            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/reportes/listar/' . $reporte->Proceso->id);
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function eliminar($reporte_id) {
        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para eliminar este documento.';
            exit;
        }

        $proceso = $reporte->Proceso;
        $reporte->delete();

        redirect('backend/reportes/listar/' . $proceso->id);
    }

    public function reporte_satisfaccion($reporte_id = null) {
        $page_size = MAX_REGISTROS_PAGINA;
        $page_num = (int)($this->input->get('p') == null ? 1 : $this->input->get('p'));
        $offset = $page_size * ($page_num - 1);

        if(!UsuarioBackendSesion::usuario()->cuenta_id) {
            redirect('/backend');
        }

        if($reporte_id) {
            $reporte = Doctrine::getTable('ReporteSatisfaccion')->find($reporte_id);
            $reporte->reporte = json_decode($reporte->reporte);

            $data['detalle'] = $reporte;
            $data['title'] = 'Reporte de satisfacción';
            $data['content'] = 'backend/reportes/reporte_satisfaccion_detalle';

            $this->load->view('backend/template', $data);
        }
        else {
            $data['reportes'] = Doctrine_Query::create()
                ->from('ReporteSatisfaccion rs')
                ->orderBy('rs.id')
                ->offset($offset)
                ->limit($page_size)
                ->orderBy('rs.id DESC')
                ->execute();

            $data['reportes_chart'] = Doctrine_Query::create()
                ->from('ReporteSatisfaccion rs')
                ->orderBy('rs.id')
                ->limit(100)
                ->orderBy('rs.id DESC')
                ->execute();

            $total = Doctrine_Query::create()
                ->from('ReporteSatisfaccion rs')
                ->count();

            $data['reportes_total'] = $total;
            $data['pagina_actual'] = $page_num;
            $data['tam_pagina'] = $page_size;

            $data['title'] = 'Reporte de satisfacción';
            $data['content'] = 'backend/reportes/reporte_satisfaccion';

            $this->load->view('backend/template', $data);
        }
    }

    public function reporte_usuario () {
      $grupos = Doctrine::getTable('GrupoUsuarios')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);
      $usuarios = Doctrine::getTable('Usuario')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);

      $data['grupos'] = $grupos;
      $data['usuarios'] = $usuarios;

      $data['title'] = 'Reportes de Usuario';
      $data['content'] = 'backend/reportes/reporte_usuario';

      $this->load->view('backend/template', $data);
    }
}

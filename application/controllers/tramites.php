<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tramites extends MY_Controller {

    public function __construct() {
      parent::__construct();

      $this->load->helper('cookies_helper');


      UsuarioSesion::limpiar_sesion();
    }

    public function index() {
      redirect('etapas/inbox');
    }

    public function participados() {

      $usuario = UsuarioSesion::usuario();

      if(!$usuario->registrado){
        redirect('autenticacion/login');
      }

      $usuario_id = $usuario->id;
      $cuenta_segun_dominio = Cuenta::cuentaSegunDominio();

      $paginado = Doctrine_Query::create()
          ->from('Parametro p')
          ->where('p.cuenta_id = ? AND p.clave = ?', array($cuenta_segun_dominio->id, 'resultados_por_pagina'))
          ->fetchOne();

          if ($paginado){
            $per_page = $paginado->valor;
          } else{
            $per_page = 50;
          }

      $offset = $this->input->get('offset');

      $resultado_query = Doctrine::getTable('Tramite')->findParticipadosConPaginacion($usuario_id, $cuenta_segun_dominio, $offset, $per_page);
      $cantidad_tramites = $resultado_query->cantidad;

      $this->load->library('pagination');
      $this->pagination->initialize(array(
          'base_url'=>site_url('tramites/participados?'),
          'total_rows'=> $cantidad_tramites,
          'per_page'=> $per_page
      ));

      $query = $this->input->post('termino');

      if($query) {
        $tramites_particiapados = $resultado_query->tramites;
        $tramites_id = [];

        foreach($tramites_particiapados as $tramite_participado) {
          array_push($tramites_id, $tramite_participado->id);
        }

        $doctrine_query = Doctrine_Query::create()
                          ->from('Etapa e, e.DatosSeguimiento d, e.Tramite t')
                          ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')
                          ->orderBy('e.id desc')
                          ->groupBy('t.id');

        $this->load->library('sphinxclient');
        $this->sphinxclient->setServer($this->config->item('sphinx_host'), $this->config->item('sphinx_port'));

        $this->sphinxclient->setFilter('tramite_id', $tramites_id);
        $result = $this->sphinxclient->query($query, 'etapas');

        if($result['total'] > 0) {
          $matches = array_keys($result['matches']);
          $doctrine_query->whereIn('e.id', $matches);
        }
        else {
          $doctrine_query->where('0');
        }

        $etapas = $doctrine_query->execute();
        if(count($etapas) < 1) {
          $tramites = 0;
        }
        else {
          $tramites = [];
          foreach($etapas as $etapa) {
            $tramite = Doctrine::getTable('Tramite')->find($etapa->tramite_id);
            array_push($tramites, $tramite);
          }
        }
      }
      else {
        $tramites = $resultado_query->tramites;
      }

      $data['tramites']= $tramites;
      $data['sidebar']='participados';
      $data['content'] = 'tramites/participados';
      $data['title'] = 'Bienvenido';
      $this->load->view('template', $data);
    }

    public function busqueda_filtros_participados(){
      $usuario = UsuarioSesion::usuario();

      $usuario_id = $usuario->id;
      $cuenta_segun_dominio = Cuenta::cuentaSegunDominio();

      $busqueda_modificacion_desde = $this->input->get('busqueda_modificacion_desde');
      $busqueda_modificacion_hasta = $this->input->get('busqueda_modificacion_hasta');
      $busqueda_id_tramite = $this->input->get('busqueda_id_tramite');
      $busqueda_etapa = $this->input->get('busqueda_etapa');
      $busqueda_grupo = $this->input->get('busqueda_grupo');
      $busqueda_nombre = $this->input->get('busqueda_nombre');
      $busqueda_documento = $this->input->get('busqueda_documento');

      $orderby = $this->input->get('orderby') && $this->input->get('orderby') != ''? $this->input->get('orderby') : 'updated_at';
      $direction = $this->input->get('direction') && $this->input->get('direction')  != '' ? $this->input->get('direction')  == 'desc' ? 'desc' : 'asc' : 'desc';

      $paginado = Doctrine_Query::create()
          ->from('Parametro p')
          ->where('p.cuenta_id = ? AND p.clave = ?', array($cuenta_segun_dominio->id, 'resultados_por_pagina'))
          ->fetchOne();

          if ($paginado){
            $per_page = $paginado->valor;
          } else{
            $per_page = 50;
          }

      $offset = $this->input->get('offset');

      $resultado_query =  Doctrine::getTable('Tramite')->findParticipadosFiltro($usuario_id,
      $cuenta_segun_dominio,
      $orderby,
      $direction,
      $busqueda_id_tramite,
      $busqueda_etapa,
      $busqueda_grupo,
      $busqueda_nombre,
      $busqueda_documento,
      $busqueda_modificacion_desde,
      $busqueda_modificacion_hasta,
      $per_page,
      $offset,false);

      $query = $this->input->get('termino');

      if($query) {
        $tramites_particiapados = $resultado_query;
        $tramites_id = [];

        foreach($tramites_particiapados as $tramite_participado) {
          array_push($tramites_id, $tramite_participado->id);
        }
        $tramites = 0;
        $cantidad_tramites = 0;

        if ($tramites_id && count($tramites_id)>0){
            $doctrine_query = Doctrine_Query::create()
                              ->from('Etapa e, e.DatosSeguimiento d, e.Tramite t')
                              ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')
                              ->orderBy('e.id desc')
                              ->groupBy('t.id');

            $this->load->library('sphinxclient');
            $this->sphinxclient->setServer($this->config->item('sphinx_host'), $this->config->item('sphinx_port'));

            $this->sphinxclient->setFilter('tramite_id', $tramites_id);
            $result = $this->sphinxclient->query($query, 'etapas');

            if($result['total'] > 0) {
              $matches = array_keys($result['matches']);
              $doctrine_query->whereIn('e.id', $matches);
            }
            else {
              $doctrine_query->where('0');
            }

            $etapas = $doctrine_query->execute();
            if(count($etapas) < 1) {
              $tramites = 0;
            }
            else {
              $tramites = [];
              foreach($etapas as $etapa) {
                $tramite = Doctrine::getTable('Tramite')->find($etapa->tramite_id);
                array_push($tramites, $tramite);
              }
            }

            $cantidad_tramites = count($tramites);
        }

      }else{
          $cantidad_tramites = Doctrine::getTable('Tramite')->findParticipadosFiltro($usuario_id,
                        $cuenta_segun_dominio,
                        $orderby,
                        $direction,
                        $busqueda_id_tramite,
                        $busqueda_etapa,
                        $busqueda_grupo,
                        $busqueda_nombre,
                        $busqueda_documento,
                        $busqueda_modificacion_desde,
                        $busqueda_modificacion_hasta,
                        $per_page,
                        $offset,true);
        $tramites = $resultado_query;
      }


      $this->load->library('pagination');

      $this->pagination->initialize(array(
          'base_url'=>site_url('tramites/busqueda_filtros_participados?'.
                                                                        'busqueda_id_tramite='.$busqueda_id_tramite.
                                                                        '&busqueda_etapa='.$busqueda_etapa.
                                                                        '&busqueda_grupo='.$busqueda_grupo.
                                                                        '&busqueda_nombre='.$busqueda_nombre.
                                                                        '&busqueda_documento='.$busqueda_documento.
                                                                        '&busqueda_modificacion_desde='.$busqueda_modificacion_desde.
                                                                        '&busqueda_modificacion_hasta='.$busqueda_modificacion_hasta),
          'total_rows'=> $cantidad_tramites,
          'per_page'=> $per_page
      ));


      $data['tramites']=$tramites;

      $data['busqueda_id_tramite']= $busqueda_id_tramite;
      $data['busqueda_etapa']= $busqueda_etapa;
      $data['busqueda_grupo']= $busqueda_grupo;
      $data['busqueda_nombre']= $busqueda_nombre;
      $data['busqueda_documento']= $busqueda_documento;
      $data['busqueda_modificacion_desde']= $busqueda_modificacion_desde;
      $data['busqueda_modificacion_hasta']= $busqueda_modificacion_hasta;

      $data['sidebar']='participados';
      $data['content'] = 'tramites/participados';
      $data['title'] = 'Bienvenido';
      $this->load->view('template', $data);

    }

    public function reportes() {
      if(!UsuarioSesion::usuario()->acceso_reportes || !UsuarioSesion::usuario()->cuenta_id) {
        redirect('/');
        exit;
      }

      $data['tramites']=Doctrine::getTable('Tramite')->findParticipados(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio(),0,0);

      $data['sidebar']='reportes';
      $data['content'] = 'tramites/reportes';
      $data['title'] = 'Bienvenido';
      $this->load->view('template', $data);
    }

    public function reportes_procesos() {
      if(!UsuarioSesion::usuario()->acceso_reportes || !UsuarioSesion::usuario()->cuenta_id) {
        redirect('/');
        exit;
      }

      $data['procesos']=$procesos = Doctrine_Query::create()
          ->from('Proceso p')
          ->where('p.cuenta_id = ?', UsuarioSesion::usuario()->cuenta_id)
          ->where('p.nombre != ?', 'BLOQUE')
          ->orderBy('p.id')
          ->execute();

      $data['sidebar']='reportes';
      $data['content'] = 'tramites/reportes_procesos';
      $data['title'] = 'Bienvenido';
      $this->load->view('template', $data);
    }

    public function ver_reportes($proceso_id) {
      if(!UsuarioSesion::usuario()->acceso_reportes || !UsuarioSesion::usuario()->cuenta_id) {
        redirect('/');
        exit;
      }

      $proceso = Doctrine::getTable('Proceso')->find($proceso_id);
      $reportes = Doctrine_Query::create()
          ->from('Reporte r')
          ->where('r.proceso_id = ?', $proceso_id)
          ->execute();

      if ($proceso->cuenta_id != UsuarioSesion::usuario()->cuenta_id) {
          echo 'Usuario no tiene permisos para listar los formularios de este proceso';
          exit;
      }

      $data['proceso'] = $proceso;
      $data['reportes'] = $reportes;

      $data['sidebar']='reportes';
      $data['content'] = 'tramites/reportes_procesos_listado';
      $data['title'] = 'Bienvenido';
      $this->load->view('template', $data);
    }

    public function ver_reporte($reporte_id) {
      if(!UsuarioSesion::usuario()->acceso_reportes || !UsuarioSesion::usuario()->cuenta_id) {
        redirect('/');
        exit;
      }

      $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

      if ($reporte->Proceso->cuenta_id != UsuarioSesion::usuario()->cuenta_id) {
        echo 'Usuario no tiene permisos';
        exit;
      }

      $reporte->generar();
    }

    public function generar_reporte($tramite_id) {
      if(UsuarioSesion::usuario()->acceso_reportes && UsuarioSesion::usuario()->cuenta_id) {
        $tramite = Doctrine_Query::create()
                ->from('Tramite t')
                ->where('t.id = ?', $tramite_id)
                ->execute();

        $tramite = $tramite[0];

        set_time_limit(600);

        $CI=& get_instance();

        $CI->load->library('Excel_XML');

        $campos=array();
        foreach($tramite->Proceso->getNombresDeDatos() as $c) {
          $campos[]=$c;
        }

        $header=array_merge(array('id','estado','etapa_actual','fecha_inicio','fecha_modificacion','fecha_termino'),$campos);

        $excel[]=$header;

        $etapas_actuales=$tramite->getEtapasActuales();
        $etapas_actuales_arr=array();
        foreach($etapas_actuales as $e)
            $etapas_actuales_arr[]=$e->Tarea->nombre;
        $etapas_actuales_str=implode(',', $etapas_actuales_arr);
        $row=array($tramite->id, $tramite->pendiente?'pendiente':'completado', $etapas_actuales_str,$tramite->created_at,$tramite->updated_at,$tramite->ended_at);

        $datos = Doctrine_Core::getTable('DatoSeguimiento')->findByTramite($tramite->id);

        foreach($datos as $d){
            if(in_array($d['nombre'],$campos)){
                $val=$d->valor;
                if(!is_string($val))
                    $val=json_encode($val,JSON_UNESCAPED_UNICODE);

                $colindex=array_search($d->nombre,$header);
                $row[$colindex]=$val;
            }
        }

        //Rellenamos con espacios en blanco los campos que no existen.
        for($i=0; $i<count($row); $i++)
            if(!isset($row[$i]))
                $row[$i]='';

        //Ordenamos
        ksort($row);

        $excel[]=$row;

        $CI->excel_xml->addArray($excel);
        $CI->excel_xml->generateXML('Reporte');
      }
    }

    public function disponibles() {
      //verifico si el usuario pertenece el grupo MesaDeEntrada y esta actuando como un ciudadano
      if(!$this->input->get('funcionario_ciudadano') == 1){
        $this->session->unset_userdata('id_usuario_ciudadano');
      }
      if(UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
        $usuario_sesion = Doctrine_Query::create()
            ->from('Usuario u')
            ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
            ->fetchOne();
            $funcionario_actuando_como_ciudadano = true;
      }
      else {
        $usuario_sesion = UsuarioSesion::usuario();
        $funcionario_actuando_como_ciudadano = false;
      }

      UsuarioSesion::registrar_acceso();

      // -- Verifica si debe redireccionar
      /*if(isset($_COOKIE['redirect'])) {
        $redirect = $_COOKIE['redirect'];
        set_cookie('redirect', 0, time()-1, '/', HOST_SISTEMA_DOMINIO);
        redirect($redirect);
      }*/

      $orderby = 'nombre';
      $direction = $this->input->get('direction') && $this->input->get('direction')  != '' ? $this->input->get('direction')  == 'desc' ? 'desc' : 'asc' : 'desc';

      $data['procesos']=Doctrine::getTable('Proceso')->findProcesosDisponiblesParaIniciar($usuario_sesion->id, Cuenta::cuentaSegunDominio(),$orderby,$direction);

      $data['orderby']=$orderby;
      $data['direction']=$direction;

      if(!$funcionario_actuando_como_ciudadano){
        $data['sidebar']='disponibles';
      }
      else{
        $data['sidebar'] = 'busqueda_ciudadano';
      }
      $data['funcionario_actuando_como_ciudadano'] = $funcionario_actuando_como_ciudadano;
      $data['usuario_nombres'] = $usuario_sesion->nombres;

      $data['content'] = 'tramites/disponibles';
      $data['title'] = 'Trámites disponibles a iniciar';
      $this->load->view('template', $data);
    }

    public function iniciar($proceso_id) {
        //verifico si el usuario pertenece el grupo MesaDeEntrada y esta actuando como un ciudadano
        if(UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
          $usuario_sesion = Doctrine_Query::create()
              ->from('Usuario u')
              ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
              ->fetchOne();
              $funcionario_actuando_como_ciudadano = true;
        }
        else {
          $usuario_sesion = UsuarioSesion::usuario();
          $funcionario_actuando_como_ciudadano = false;
        }

        $proceso=Doctrine::getTable('Proceso')->find($proceso_id);

          if(!$proceso->canUsuarioIniciarlo($usuario_sesion->id)){
            redirect(site_url());
        }

        //Vemos si es que usuario ya tiene un tramite de proceso_id ya iniciado, y que se encuentre en su primera etapa.
        //Si es asi, hacemos que lo continue. Si no, creamos uno nuevo
        $tramite=Doctrine_Query::create()
                ->from('Tramite t, t.Proceso p, t.Etapas e, e.Tramite.Etapas hermanas')
                ->where('t.pendiente=1 AND p.id = ? AND e.usuario_id = ?',array($proceso_id, $usuario_sesion->id))
                ->groupBy('t.id')
                ->having('COUNT(hermanas.id) = 1')
                ->fetchOne();

        if(!$tramite || !$tramite->getEtapasActuales()->get(0)->id) {
            $tramite=new Tramite();
            $tramite->iniciar($proceso->id);
        }

        $qs=$this->input->server('QUERY_STRING');
        redirect('etapas/ejecutar/'.$tramite->getEtapasActuales()->get(0)->id.($qs?'?'.$qs:''));
    }

    public function eliminar($tramite_id){
        //verifico si el usuario pertenece el grupo MesaDeEntrada y esta actuando como un ciudadano
        if(UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
          $usuario_sesion = Doctrine_Query::create()
              ->from('Usuario u')
              ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
              ->fetchOne();
        }
        else {
          $usuario_sesion = UsuarioSesion::usuario();
        }

        $tramite=Doctrine::getTable('Tramite')->find($tramite_id);

        if($tramite->Etapas->count()>1){
            echo 'Tramite no se puede eliminar, ya ha avanzado mas de una etapa';
            exit;
        }

        if($usuario_sesion->id != $tramite->Etapas[0]->usuario_id){
            redirect(site_url());
        }

        $tramite->delete();
        redirect($this->input->server('HTTP_REFERER'));
    }
}

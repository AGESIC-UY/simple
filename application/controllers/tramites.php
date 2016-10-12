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
        $data['tramites']=Doctrine::getTable('Tramite')->findParticipados(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio());

        $data['sidebar']='participados';
        $data['content'] = 'tramites/participados';
        $data['title'] = 'Bienvenido';
        $this->load->view('template', $data);
    }

    public function disponibles() {
      UsuarioSesion::registrar_acceso();

      // -- Verifica si debe redireccionar
      if(isset($_COOKIE['redirect'])) {
        $redirect = $_COOKIE['redirect'];
        set_cookie('redirect', 0, time()-1, '/', HOST_SISTEMA_DOMINIO);
        redirect($redirect);
      }

      $orderby = 'nombre';
      $direction = $this->input->get('direction') == 'desc' ? 'desc' : 'asc';

      $data['procesos']=Doctrine::getTable('Proceso')->findProcesosDisponiblesParaIniciar(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio(),$orderby,$direction);

      $data['orderby']=$orderby;
      $data['direction']=$direction;
      $data['sidebar']='disponibles';
      $data['content'] = 'tramites/disponibles';
      $data['title'] = 'Trámites disponibles a iniciar';
      $this->load->view('template', $data);
    }

    public function iniciar($proceso_id) {
        $proceso=Doctrine::getTable('Proceso')->find($proceso_id);

          if(!$proceso->canUsuarioIniciarlo(UsuarioSesion::usuario()->id)){
            redirect(site_url());
        }

        //Vemos si es que usuario ya tiene un tramite de proceso_id ya iniciado, y que se encuentre en su primera etapa.
        //Si es asi, hacemos que lo continue. Si no, creamos uno nuevo
        $tramite=Doctrine_Query::create()
                ->from('Tramite t, t.Proceso p, t.Etapas e, e.Tramite.Etapas hermanas')
                ->where('t.pendiente=1 AND p.id = ? AND e.usuario_id = ?',array($proceso_id, UsuarioSesion::usuario()->id))
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
        $tramite=Doctrine::getTable('Tramite')->find($tramite_id);

        if($tramite->Etapas->count()>1){
            echo 'Tramite no se puede eliminar, ya ha avanzado mas de una etapa';
            exit;
        }

        if(UsuarioSesion::usuario()->id!=$tramite->Etapas[0]->usuario_id){
            redirect(site_url());
        }

        $tramite->delete();
        redirect($this->input->server('HTTP_REFERER'));
    }
}

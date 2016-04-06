<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Seguimiento extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if(UsuarioBackendSesion::usuario()->rol!='super' && UsuarioBackendSesion::usuario()->rol!='operacion' && UsuarioBackendSesion::usuario()->rol!='seguimiento'){
            echo 'No tiene permisos para acceder a esta seccion.';
            exit;
        }
    }

    public function index() {
        $procesos = Doctrine_Query::create()
            ->from('Proceso p')
            ->where('p.cuenta_id = ?', UsuarioBackendSesion::usuario()->cuenta_id)
            ->where('p.nombre != ?', 'BLOQUE')
            ->orderBy('p.id')
            ->execute();

        $data['procesos']=$procesos;

        $data['title'] = 'Listado de Procesos';
        $data['content'] = 'backend/seguimiento/index';
        $this->load->view('backend/template', $data);
    }

    public function index_proceso($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if (UsuarioBackendSesion::usuario()->cuenta_id != $proceso->cuenta_id) {
            echo 'Usuario no tiene permisos';
            exit;
        }

        $query = $this->input->get('query');
        $offset=$this->input->get('offset');
        $order=$this->input->get('order')?$this->input->get('order'):'updated_at';
        $direction = $this->input->get('direction') == 'desc' ? 'desc' : 'asc';
        $created_at_desde=$this->input->get('created_at_desde');
        $created_at_hasta=$this->input->get('created_at_hasta');
        $updated_at_desde=$this->input->get('updated_at_desde');
        $updated_at_hasta=$this->input->get('updated_at_hasta');
        $pendiente=$this->input->get('pendiente')!==false?$this->input->get('pendiente'):-1;
        $per_page=100;
        $busqueda_avanzada=$this->input->get('busqueda_avanzada');

        $doctrine_query = Doctrine_Query::create()
                ->from('Tramite t, t.Proceso p, t.Etapas e, e.DatosSeguimiento d')
                ->where('p.id = ?', $proceso_id)
                ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')  //Mostramos solo los que se han avanzado o tienen datos
                ->groupBy('t.id')
                ->orderBy($order.' '.$direction)
                ->limit($per_page)
                ->offset($offset);

        if($created_at_desde)
            $doctrine_query->andWhere ('created_at >= ?',array(date('Y-m-d',strtotime($created_at_desde))));
        if($created_at_hasta)
            $doctrine_query->andWhere ('created_at <= ?',array(date('Y-m-d',strtotime($created_at_hasta))));
        if($updated_at_desde)
            $doctrine_query->andWhere ('updated_at >= ?',array(date('Y-m-d',strtotime($updated_at_desde))));
        if($updated_at_hasta)
            $doctrine_query->andWhere ('updated_at <= ?',array(date('Y-m-d',strtotime($updated_at_hasta))));
        if($pendiente!=-1)
            $doctrine_query->andWhere ('pendiente = ?',array($pendiente));


        if ($query) {
            $this->load->library('sphinxclient');
            $this->sphinxclient->setServer($this->config->item('sphinx_host'),$this->config->item('sphinx_port'));
            $this->sphinxclient->setFilter('proceso_id', array($proceso_id));
            $result = $this->sphinxclient->query($query, 'tramites');
            if($result['total']>0){
                $matches = array_keys($result['matches']);
                $doctrine_query->whereIn('t.id',$matches);
            }else{
                $doctrine_query->where('0');
            }
        }

        $tramites=$doctrine_query->execute();
        $ntramites=$doctrine_query->count();

        $this->load->library('pagination');
        $this->pagination->initialize(array(
            'base_url'=>site_url('backend/seguimiento/index_proceso/'.$proceso_id.'?order='.$order.'&direction='.$direction.'&pendiente='.$pendiente.'&created_at_desde='.$created_at_desde.'&created_at_hasta='.$created_at_hasta.'&updated_at_desde='.$updated_at_desde.'&updated_at_hasta='.$updated_at_hasta),
            'total_rows'=>$ntramites,
            'per_page'=>$per_page
        ));

        $data['query'] = $query;
        $data['order']=$order;
        $data['direction']=$direction;
        $data['created_at_desde']=$created_at_desde;
        $data['created_at_hasta']=$created_at_hasta;
        $data['updated_at_desde']=$updated_at_desde;
        $data['updated_at_hasta']=$updated_at_hasta;
        $data['pendiente']=$pendiente;
        $data['busqueda_avanzada']=$busqueda_avanzada;
        $data['proceso'] = $proceso;
        $data['tramites'] = $tramites;

        $data['title'] = 'Seguimiento de ' . $proceso->nombre;
        $data['content'] = 'backend/seguimiento/index_proceso';
        $this->load->view('backend/template', $data);
    }

    public function ver($tramite_id) {
        $tramite = Doctrine::getTable('Tramite')->find($tramite_id);

        if (UsuarioBackendSesion::usuario()->cuenta_id != $tramite->Proceso->cuenta_id) {
            echo 'No tiene permisos para hacer seguimiento a este tramite.';
            exit;
        }

        $data['tramite'] = $tramite;
        $data['etapas'] = Doctrine_Query::create()->from('Etapa e, e.Tramite t')->where('t.id = ?', $tramite->id)->orderBy('id desc')->execute();

        $data['title'] = 'Seguimiento - ' . $tramite->Proceso->nombre;
        $data['content'] = 'backend/seguimiento/ver';
        $this->load->view('backend/template', $data);
    }

    public function ajax_ver_etapas($tramite_id, $tarea_identificador) {
        $tramite = Doctrine::getTable('Tramite')->find($tramite_id);

        if (UsuarioBackendSesion::usuario()->cuenta_id != $tramite->Proceso->cuenta_id) {
            echo 'No tiene permisos para hacer seguimiento a este tramite.';
            exit;
        }

        $etapas = Doctrine_Query::create()
                ->from('Etapa e, e.Tarea tar, e.Tramite t')
                ->where('t.id = ? AND tar.identificador = ?', array($tramite_id, $tarea_identificador))
                ->execute();


        $data['etapas'] = $etapas;

        $this->load->view('backend/seguimiento/ajax_ver_etapas', $data);
    }

    public function ver_etapa($etapa_id, $secuencia = 0) {
        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
        $paso = $etapa->getPasoEjecutable($secuencia);

        if (UsuarioBackendSesion::usuario()->cuenta_id != $etapa->Tramite->Proceso->cuenta_id) {
            echo 'No tiene permisos para hacer seguimiento a este tramite.';
            exit;
        }

        $data['etapa'] = $etapa;
        $data['paso']=$paso;
        $data['secuencia'] = $secuencia;

        $data['title'] = 'Seguimiento - ' . $etapa->Tarea->nombre;
        $data['content'] = 'backend/seguimiento/ver_etapa';
        $this->load->view('backend/template', $data);
    }

    public function reasignar_form($etapa_id) {
        $this->form_validation->set_rules('usuario_id', 'Usuario', 'required');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $usuario=Doctrine::getTable('Usuario')->find($this->input->post('usuario_id'));

            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            $etapa->Usuario = $usuario;
            $etapa->save();

            $this->email->from('simple@'.$this->config->item('main_domain'), 'Simple');
            $this->email->to($usuario->email);
            $this->email->subject('Tarea reasignada');
            $this->email->message('<p>Atención. Se le ha reasignado una tarea "'.$etapa->Tarea->nombre.'" del proceso "'.$etapa->Tramite->Proceso->nombre.'".</p>');
            $this->email->send();

            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/seguimiento/ver_etapa/' . $etapa->id);
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function borrar_tramite($tramite_id) {
        if(UsuarioBackendSesion::usuario()->rol=='seguimiento')
            show_error('No tiene permisos',401);


        $tramite = Doctrine::getTable('Tramite')->find($tramite_id);

        if (UsuarioBackendSesion::usuario()->cuenta_id != $tramite->Proceso->cuenta_id) {
            echo 'No tiene permisos para hacer seguimiento a este tramite.';
            exit;
        }

        $tramite->delete();

        redirect($this->input->server('HTTP_REFERER'));
    }

    public function borrar_proceso($proceso_id) {
        if(UsuarioBackendSesion::usuario()->rol=='seguimiento')
            show_error('No tiene permisos',401);

        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if (UsuarioBackendSesion::usuario()->cuenta_id != $proceso->cuenta_id) {
            echo 'No tiene permisos para hacer seguimiento a este tramite.';
            exit;
        }

        $proceso->Tramites->delete();

        redirect($this->input->server('HTTP_REFERER'));
    }

    public function ajax_editar_vencimiento($etapa_id){
        $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
        $data['etapa']=$etapa;

        $this->load->view('backend/seguimiento/ajax_editar_vencimiento',$data);
    }

    public function editar_vencimiento_form($etapa_id){
        $this->form_validation->set_rules('vencimiento_at','Fecha de vencimiento','required');
        $respuesta=new stdClass();
        if($this->form_validation->run()==TRUE){
            $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
            $etapa->vencimiento_at=date('Y-m-d',strtotime($this->input->post('vencimiento_at')));
            $etapa->save();

            $respuesta->validacion=TRUE;
            $respuesta->redirect=  site_url('backend/seguimiento/index_proceso/'.$etapa->Tarea->proceso_id);
        }else{
            $respuesta->validacion=FALSE;
            $respuesta->errores=  validation_errors();
        }

        echo json_encode($respuesta);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */

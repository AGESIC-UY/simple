<?php

class API extends MY_BackendController {

    public function _auth(){
        UsuarioBackendSesion::force_login();

        if(!UsuarioBackendSesion::has_rol('super') && !UsuarioBackendSesion::has_rol('desarrollo')) {
            redirect('backend');
        }
    }

    /*
     * Documentacion de la API
     */

    public function index(){
        $this->_auth();

        $data['title']='API';
        $data['content']='backend/api/index';
        $this->load->view('backend/template',$data);
    }

    public function token(){
        $this->_auth();

        $data['cuenta']=UsuarioBackendSesion::usuario()->Cuenta;

        $data['title']='Configurar Código de Acceso';
        $data['content']='backend/api/token';
        $this->load->view('backend/template',$data);
    }

    public function token_form(){
        $this->_auth();

        $cuenta=UsuarioBackendSesion::usuario()->Cuenta;

        $this->form_validation->set_rules('api_token','Token','max_length[32]');

        $respuesta=new stdClass();
        if($this->form_validation->run()==true){
            $cuenta->api_token=$this->input->post('api_token');
            $cuenta->save();

            $respuesta->validacion=true;
            $respuesta->redirect=site_url('backend/api');

        }else{
            $respuesta->validacion=false;
            $respuesta->errores=validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function tramites_recurso(){
        $this->_auth();

        $data['title']='Tramites';
        $data['content']='backend/api/tramites_recurso';
        $this->load->view('backend/template',$data);
    }

    public function tramites_obtener(){
        $this->_auth();

        $data['title']='Tramites: obtener';
        $data['content']='backend/api/tramites_obtener';
        $this->load->view('backend/template',$data);
    }

    public function tramites_listar(){
        $this->_auth();

        $data['title']='Tramites: listar';
        $data['content']='backend/api/tramites_listar';
        $this->load->view('backend/template',$data);
    }

    public function tramites_listarporproceso(){
        $this->_auth();

        $data['title']='Tramites: listar por proceso';
        $data['content']='backend/api/tramites_listarporproceso';
        $this->load->view('backend/template',$data);
    }


    public function tramites_ejecutar(){
        $this->_auth();

        $data['title']='Tramites: ejecutar';
        $data['content']='backend/api/tramites_ejecutar';
        $this->load->view('backend/template',$data);
    }

    public function procesos_recurso(){
        $this->_auth();

        $data['title']='Procesos';
        $data['content']='backend/api/procesos_recurso';
        $this->load->view('backend/template',$data);
    }

    public function procesos_obtener(){
        $this->_auth();

        $data['title']='Procesos: obtener';
        $data['content']='backend/api/procesos_obtener';
        $this->load->view('backend/template',$data);
    }

    public function procesos_listar(){
        $this->_auth();

        $data['title']='Procesos: listar';
        $data['content']='backend/api/procesos_listar';
        $this->load->view('backend/template',$data);
    }


    /*
     * Llamadas de la API
     */

    public function tramites($tramite_id = null) {
        $api_token=$this->input->get('token');

        $cuenta = Cuenta::cuentaSegunDominio();

        if(!$cuenta->api_token)
            show_404 ();

        if($cuenta->api_token!=$api_token)
            show_error ('No tiene permisos para acceder a este recurso.', 401);

        $respuesta = new stdClass();
        if ($tramite_id) {
            $tramite = Doctrine::getTable('Tramite')->find($tramite_id);

            if (!$tramite)
                show_404();

            if ($tramite->Proceso->Cuenta != $cuenta)
                show_error('No tiene permisos para acceder a este recurso.', 401);


            $respuesta->tramite = $tramite->toPublicArray();
        } else {
            $offset = $this->input->get('pageToken') ? 1 * base64_decode(urldecode($this->input->get('pageToken'))) : null;
            $limit = ($this->input->get('maxResults') && $this->input->get('maxResults') <= 20) ? 1 * $this->input->get('maxResults') : 10;

            $query = Doctrine_Query::create()
                    ->from('Tramite t, t.Proceso.Cuenta c')
                    ->where('c.id = ?', array($cuenta->id))
                    ->orderBy('id desc');
            if ($offset)
                $query->andWhere('id < ?', $offset);

            $ntramites_restantes = $query->count() - $limit;

            $query->limit($limit);
            $tramites = $query->execute();

            $nextPageToken = null;
            if ($ntramites_restantes > 0)
                $nextPageToken = urlencode(base64_encode($tramites[count($tramites) - 1]->id));

            $respuesta->tramites = new stdClass();
            $respuesta->tramites->titulo = 'Listado de Trámites';
            $respuesta->tramites->tipo = '#tramitesFeed';
            $respuesta->tramites->nextPageToken = $nextPageToken;
            $respuesta->tramites->items = null;
            foreach ($tramites as $t)
                $respuesta->tramites->items[] = $t->toPublicArray();
        }

        header('Content-type: application/json');
        echo json_indent(json_encode($respuesta));
    }

    public function procesos($proceso_id = null, $recurso = null) {
        $api_token=$this->input->get('token');

        $cuenta = Cuenta::cuentaSegunDominio();

        if(!$cuenta->api_token)
            show_404 ();

        if($cuenta->api_token!=$api_token)
            show_error ('No tiene permisos para acceder a este recurso.', 401);

        if ($proceso_id) {
            $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

            if (!$proceso)
                show_404();

            if ($proceso->Cuenta != $cuenta)
                show_error('No tiene permisos para acceder a este recurso.', 401);

            if ($recurso == 'tramites') {
                $offset = $this->input->get('pageToken') ? 1 * base64_decode(urldecode($this->input->get('pageToken'))) : null;
                $limit = ($this->input->get('maxResults') && $this->input->get('maxResults') <= 20) ? 1 * $this->input->get('maxResults') : 10;

                $query = Doctrine_Query::create()
                        ->from('Tramite t, t.Proceso p')
                        ->where('p.id = ?', array($proceso->id))
                        ->orderBy('id desc');
                if ($offset)
                    $query->andWhere('id < ?', $offset);

                $ntramites_restantes = $query->count() - $limit;

                $query->limit($limit);
                $tramites = $query->execute();

                $nextPageToken = null;
                if ($ntramites_restantes > 0)
                    $nextPageToken = urlencode(base64_encode($tramites[count($tramites) - 1]->id));

                $respuesta = new stdClass();
                $respuesta->tramites->titulo = 'Listado de Trámites';
                $respuesta->tramites->tipo = '#tramitesFeed';
                $respuesta->tramites->nextPageToken = $nextPageToken;
                $respuesta->tramites->items = null;
                foreach ($tramites as $t)
                    $respuesta->tramites->items[] = $t->toPublicArray();
            } else {

                $respuesta = new stdClass();
                $respuesta->proceso = $proceso->toPublicArray();
            }
        } else {

            $procesos = Doctrine::getTable('Proceso')->findByCuentaId($cuenta->id);

            $respuesta = new stdClass();
            $respuesta->procesos->titulo = 'Listado de Procesos';
            $respuesta->procesos->tipo = '#procesosFeed';
            $respuesta->procesos->items = null;
            foreach ($procesos as $t)
                $respuesta->procesos->items[] = $t->toPublicArray();
        }

        header('Content-type: application/json');
        echo json_indent(json_encode($respuesta));
    }

    //avanzar Tarea - 2017 v 1.1
    public function ejecutar_tarea($tramite_id = null) {
        $api_token=$this->input->get('token');

        $cuenta = Cuenta::cuentaSegunDominio();

        if(!$cuenta->api_token)
            show_404 ();

        if($cuenta->api_token!=$api_token)
            show_error ('No tiene permisos para acceder a este recurso.', 401);

        $respuesta = new stdClass();
        if ($tramite_id) {
            $tramite = Doctrine::getTable('Tramite')->find($tramite_id);

            if (!$tramite)
                show_404();

            if ($tramite->Proceso->Cuenta != $cuenta)
                show_error('No tiene permisos para acceder a este recurso.', 401);


            $respuesta = array(
                    "resultado" => "ERROR",
                    "mensaje" => "Sin etapa pendiente o automatica"
            );


            foreach($tramite->getEtapasActuales() as $etapa){
                $etapa_automatica = $etapa->Tarea->automatica;
                //$resultado = $resultado.' ' .$etapa->id  .' ' . $etapa_automatica . ' ' .$etapa->pendiente;
                if ($etapa_automatica){
                  //$resultado = $resultado.' ' .' es automatica ';
                  if ($etapa->pendiente){
                    //$resultado = $resultado.' ' .' es pendiente ';
                    //la etapa esta pendiente y es automatica se avanza
                    $etapa->avanzar();
                    $respuesta = array(
                            "resultado" => "OK",
                            "mensaje" => "Se avanzó la etapa con id ". $etapa->id
                    );
                  //break;, solo obtiene las etapas aactuales no es necesario el break
                  }
                }
            }
        }

        header('Content-type: application/json');
        echo json_indent(json_encode($respuesta));
    }

}

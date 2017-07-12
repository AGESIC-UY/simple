<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Etapas extends MY_Controller {

    public function __construct() {
        parent::__construct();

        UsuarioSesion::limpiar_sesion();
    }

    public function busqueda_termino() {

      $usuario = UsuarioSesion::usuario();
      $usuario_id = $usuario->id;
      $cuenta_segun_dominio = Cuenta::cuentaSegunDominio();

      $orderby = 'updated_at';
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

      $resultado_query = Doctrine::getTable('Etapa')->findPendientesConPaginacion($usuario_id, $cuenta_segun_dominio, $orderby, $direction, $per_page, $offset);
      $cantidad_etapas = $resultado_query->cantidad;

      $this->load->library('pagination');
      $this->pagination->initialize(array(
          'base_url'=>site_url('etapas/busqueda_termino?'),
          'total_rows'=> $cantidad_etapas,
          'per_page'=> $per_page
      ));

      $query = $this->input->post('termino');

      if($query) {
        $etapas_pendientes = $resultado_query->etapas;
        $tramites_id = [];

        foreach($etapas_pendientes as $etapa_pendiente) {
          array_push($tramites_id, $etapa_pendiente->tramite_id);
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
          $etapas = [];
        }
      }
      else {
        //$etapas = Doctrine::getTable('Etapa')->findPendientes(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio(), $orderby, $direction);
        $etapas = $resultado_query->etapas;
      }

      $data['orderby'] = $orderby;
      $data['direction'] = $direction;

      $data['etapas'] = $etapas;
      $data['sidebar'] = 'inbox';
      $data['content'] = 'etapas/inbox';
      $data['title'] = 'Bandeja de Entrada';
      $this->load->view('template', $data);
    }

    public function inbox() {
      if(!UsuarioSesion::usuario()->registrado){
        redirect('autenticacion/login');
      }

      if(!$this->input->get('funcionario_ciudadano') == 1){
        $this->session->unset_userdata('id_usuario_ciudadano');
      }

      //verifico si el usuario pertenece el grupo MesaDeEntrada y hay esta actuando como un ciudadano
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

      $orderby = $this->input->get('orderby') && $this->input->get('orderby') != ''? $this->input->get('orderby') : 'updated_at';
      $direction = $this->input->get('direction') && $this->input->get('direction')  != '' ? $this->input->get('direction')  == 'desc' ? 'desc' : 'asc' : 'desc';

      $resultado_query= Doctrine::getTable('Etapa')->findPendientesConPaginacion($usuario_sesion->id, Cuenta::cuentaSegunDominio(), $orderby, $direction, $per_page, $offset);
      $cantidad_etapas = $resultado_query->cantidad;

      $this->load->library('pagination');
      $this->pagination->initialize(array(
          'base_url'=>site_url('etapas/inbox?orderby= '.$orderby . '&direction='.$direction),
          'total_rows'=> $cantidad_etapas,
          'per_page'=> $per_page
      ));

      $data['orderby'] = $orderby;
      $data['direction'] = $direction;

      $data['etapas'] = $resultado_query->etapas;
      $data['funcionario_actuando_como_ciudadano'] = $funcionario_actuando_como_ciudadano;
      $data['usuario_nombres'] = $usuario_sesion->nombres;
      if(!$funcionario_actuando_como_ciudadano){
        $data['sidebar'] = 'inbox';
      }
      else{
        $data['sidebar'] = 'busqueda_ciudadano';
      }
      $data['content'] = 'etapas/inbox';
      $data['title'] = 'Bandeja de Entrada';
      $this->load->view('template', $data);
    }

    public function busqueda_filtros_pendientes(){
      $usuario = UsuarioSesion::usuario();

      $usuario_id = $usuario->id;
      $cuenta_segun_dominio = Cuenta::cuentaSegunDominio();

      $busqueda_etapa = $this->input->get('busqueda_etapa');
      $busqueda_grupo = $this->input->get('busqueda_grupo');
      $busqueda_nombre = $this->input->get('busqueda_nombre');
      $busqueda_documento = $this->input->get('busqueda_documento');
      $busqueda_modificacion_desde = $this->input->get('busqueda_modificacion_desde');
      $busqueda_modificacion_hasta = $this->input->get('busqueda_modificacion_hasta');
      $busqueda_id_tramite = $this->input->get('busqueda_id_tramite');

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

      $resultado_query = Doctrine::getTable('Etapa')->findPendientesFiltro($usuario_id,
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
      $offset);

      $query = $this->input->get('termino');

      if($query) {
        $etapas_pendientes = $resultado_query->etapas;
        $tramites_id = [];

        foreach($etapas_pendientes as $etapa_pendiente) {
          array_push($tramites_id, $etapa_pendiente->tramite_id);
        }
        $etapas = 0;
        $cantidad_etapas = 0;
        if($tramites_id && count($tramites_id)>0){
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
            $etapas = [];
          }
        }
      }
      else{
        $cantidad_etapas = $resultado_query->cantidad;
        $etapas = $resultado_query->etapas;
      }

      $this->load->library('pagination');

      $this->pagination->initialize(array(
          'base_url'=>site_url('etapas/busqueda_filtros_pendientes?'.
                                                                        'busqueda_id_tramite='.$busqueda_id_tramite.
                                                                        '&busqueda_etapa='.$busqueda_etapa.
                                                                        '&busqueda_grupo='.$busqueda_grupo.
                                                                        '&busqueda_nombre='.$busqueda_nombre.
                                                                        '&busqueda_documento='.$busqueda_documento.
                                                                        '&busqueda_modificacion_desde='.$busqueda_modificacion_desde.
                                                                        '&busqueda_modificacion_hasta='.$busqueda_modificacion_hasta).
                                                                        '&orderby='.$orderby.
                                                                        '&direction='.$direction,
          'total_rows'=> $cantidad_etapas,
          'per_page'=> $per_page
      ));


      $data['etapas']= $etapas;

      $data['busqueda_id_tramite']= $busqueda_id_tramite;
      $data['busqueda_etapa']= $busqueda_etapa;
      $data['busqueda_grupo']= $busqueda_grupo;
      $data['busqueda_nombre']= $busqueda_nombre;
      $data['busqueda_documento']= $busqueda_documento;
      $data['busqueda_modificacion_desde']= $busqueda_modificacion_desde;
      $data['busqueda_modificacion_hasta']= $busqueda_modificacion_hasta;


      $data['orderby'] = $orderby;
      $data['direction'] = $direction;

      $data['sidebar'] = 'inbox';
      $data['content'] = 'etapas/inbox';
      $data['title'] = 'Bandeja de Entrada';
      $this->load->view('template', $data);

    }

    public function busqueda_filtros_sinasignar(){
      $usuario = UsuarioSesion::usuario();

      $usuario_id = $usuario->id;
      $cuenta_segun_dominio = Cuenta::cuentaSegunDominio();

      $busqueda_etapa = $this->input->get('busqueda_etapa');
      $busqueda_grupo = $this->input->get('busqueda_grupo');
      $busqueda_nombre = $this->input->get('busqueda_nombre');
      $busqueda_documento = $this->input->get('busqueda_documento');
      $busqueda_modificacion_desde = $this->input->get('busqueda_modificacion_desde');
      $busqueda_modificacion_hasta = $this->input->get('busqueda_modificacion_hasta');
      $busqueda_id_tramite = $this->input->get('busqueda_id_tramite');

      $orderby = 'updated_at';
      $direction = 'asc';

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

      $resultado_query = Doctrine::getTable('Etapa')->findSinAsignarFiltro($usuario_id,
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
      $offset,
      false);


      $query = $this->input->get('termino');

      if($query) {
        $etapas_pendientes = $resultado_query;
        $tramites_id = [];

        foreach($etapas_pendientes as $etapa_pendiente) {
          array_push($tramites_id, $etapa_pendiente->tramite_id);
        }
        $etapas = 0;
        $cantidad_etapas = 0;
        if($tramites_id && count($tramites_id)>0){
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
            $etapas = [];
          }
        }
      }
      else{
        $cantidad_etapas = Doctrine::getTable('Etapa')->findSinAsignarFiltro($usuario_id,
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
        $offset,
        true);
        $etapas = $resultado_query;
      }

      $this->load->library('pagination');

      $this->pagination->initialize(array(
          'base_url'=>site_url('etapas/busqueda_filtros_sinasignar?'.
                                                                        'busqueda_id_tramite='.$busqueda_id_tramite.
                                                                        '&busqueda_etapa='.$busqueda_etapa.
                                                                        '&busqueda_grupo='.$busqueda_grupo.
                                                                        '&busqueda_nombre='.$busqueda_nombre.
                                                                        '&busqueda_documento='.$busqueda_documento.
                                                                        '&busqueda_modificacion_desde='.$busqueda_modificacion_desde.
                                                                        '&busqueda_modificacion_hasta='.$busqueda_modificacion_hasta),
          'total_rows'=> $cantidad_etapas,
          'per_page'=> $per_page
      ));

      $data['busqueda_id_tramite']= $busqueda_id_tramite;
      $data['busqueda_etapa']= $busqueda_etapa;
      $data['busqueda_grupo']= $busqueda_grupo;
      $data['busqueda_nombre']= $busqueda_nombre;
      $data['busqueda_documento']= $busqueda_documento;
      $data['busqueda_modificacion_desde']= $busqueda_modificacion_desde;
      $data['busqueda_modificacion_hasta']= $busqueda_modificacion_hasta;


      $data['orderby'] = $orderby;
      $data['direction'] = $direction;

      $data['etapas'] = $etapas;
      $data['sidebar'] = 'sinasignar';
      $data['content'] = 'etapas/sinasignar';
      $data['title'] = 'Sin Asignar';
      $this->load->view('template', $data);

    }


    public function sinasignar() {
      if (!UsuarioSesion::usuario()->registrado) {
        $this->session->set_flashdata('redirect', current_url());
        redirect('autenticacion/login');
      }

      if(!UsuarioSesion::usuario()->registrado){
        redirect('autenticacion/login');
      }

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

      $orderby = 'updated_at';
      $direction = $this->input->get('direction') && $this->input->get('direction')  != '' ? $this->input->get('direction')  == 'desc' ? 'desc' : 'asc' : 'desc';

      $resultado_query= Doctrine::getTable('Etapa')->findSinAsignarConPaginacion(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio(), $orderby, $direction, $per_page, $offset);
      $cantidad_etapas = Doctrine::getTable('Etapa')->cantidadSinAsignar(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio());

      $this->load->library('pagination');
      $this->pagination->initialize(array(
          'base_url'=>site_url('etapas/sinasignar?'),
          'total_rows'=> $cantidad_etapas,
          'per_page'=> $per_page
      ));

      $data['orderby'] = $orderby;
      $data['direction'] = $direction;

      //show_error(var_dump($resultado_query));

      $data['etapas'] = $resultado_query;
      $data['sidebar'] = 'sinasignar';
      $data['content'] = 'etapas/sinasignar';
      $data['title'] = 'Sin Asignar';
      $this->load->view('template', $data);
    }

    public function ejecutar_pago($etapa_id, $secuencia, $usuario_id) {
      UsuarioSesion::logout();

      $usuario = Doctrine_Query::create()
              ->from('Usuario u')
              ->where('u.id = ?', $usuario_id)
              ->fetchOne();

      if(!$usuario->registrado) {
        UsuarioSesion::login_usuario_pago($usuario->usuario);
        redirect('/etapas/ejecutar/'.$etapa_id . "/" . $secuencia);
      }
      else {
        redirect('/autenticacion/login?redirect='.site_url().'etapas/ejecutar/'.$etapa_id . "/" . $secuencia);
      }
    }

    public function ejecutar($etapa_id, $secuencia=0) {
        //verifico si el usuario pertenece el grupo MesaDeEntrada y esta actuando como un ciudadano
        if(UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
          $usuario_sesion = Doctrine_Query::create()
              ->from('Usuario u')
              ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
              ->fetchOne();
              $funcionario_actuando_como_ciudadano = true;
              $necesita_nivel_de_confianza = false;
        }
        else {
          $usuario_sesion = UsuarioSesion::usuario();
          $necesita_nivel_de_confianza = true;
          $funcionario_actuando_como_ciudadano = false;
        }

        $iframe = $this->input->get('iframe');

        if($this->input->get('iframe')) {
          $template = 'template_iframe';
        }
        elseif ($this->input->get('iframe_nc')) {
          $template = 'template_iframe_sin_contexto';
        }
        else {
          $template = 'template';
        }

        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
        if(!$etapa) {
            show_404();
        }

        $paso = $etapa->getPasoEjecutable($secuencia);
        $formulario = $paso->Formulario;

        // -- Se genera dato de seguimieto en el caso de que el funcionario este actuando como ciudadano
        if($funcionario_actuando_como_ciudadano){
            $dato_funcionario = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('funcionario_actuando_como_ciudadano', $etapa->id);

            if (!$dato_funcionario){
              $dato_funcionario = new DatoSeguimiento();
            }

            $dato_funcionario->nombre = 'funcionario_actuando_como_ciudadano';
            $dato_funcionario->valor = (string)UsuarioSesion::usuario()->id;
            $dato_funcionario->etapa_id = $etapa->id;
            $dato_funcionario->save();
        }

        // -- Se genera el ID de transaccion correspondiente para trazabilidad.
        $id_transaccion = str_replace(" ", "_", strtoupper($formulario->Proceso->ProcesoTrazabilidad->organismo_id)) . ':' . str_replace(" ", "_", strtoupper($formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id)) . ':' . $etapa->tramite_id;

        // -- Genera variable con el ID de transaccion
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_transaccion_traza', $etapa->id);
        if (!$dato)
          $dato = new DatoSeguimiento();

        $dato->etapa_id = $etapa->id;
        $dato->nombre = 'id_transaccion_traza';
        $dato->valor = (string)$id_transaccion;
        $dato->save();

        // -- Se genera el GUID (ID de traza publico)
        /*$dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('guidTrazabilidad', $etapa->id);
        if (!$dato)
          $dato = new DatoSeguimiento();

        $dato->etapa_id = $etapa->id;
        $dato->nombre = 'guidTrazabilidad';
        //$dato->valor = (string)$id_transaccion;
        $dato->save();*/

        // -- Genera variable con el ID de estado para traza (idEstadoTrazabilidad)
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('idEstadoTrazabilidad', $etapa->id);
        if (!$dato) {
          $dato = new DatoSeguimiento();
          $dato->etapa_id = $etapa->id;
          $dato->nombre = 'idEstadoTrazabilidad';
          $dato->valor = (string)$etapa->Tarea->trazabilidad_estado;
          $dato->save();
        }

        if($necesita_nivel_de_confianza){
          //si la tarea tiene acceso registrado se verifica nivel de confianza del usuario
          if($etapa->Tarea->acceso_modo == 'registrados'){
            if ($etapa->Tarea->nivel_confianza){
              if (UsuarioSesion::getNivel_confianza() < $etapa->Tarea->nivel_confianza){
                $data['error'] = 'Usuario no tiene el nivel de confianza requerido para ejecutar esta etapa';
                $data['content'] = 'etapas/error';
                $data['title'] = $data['error'];
                $this->load->view($template, $data);
                return;
              }
            }
          }
        }


        //la etapa siempre tiene usuario id o el ficticio (no registrado) o el registrado
        //en caso de ser el ficticio tambien esta en la session con el mismo id.
        if ($etapa->usuario_id != $usuario_sesion->id) {
            if (!$usuario_sesion->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                //TODO modificado antes llamaba a login_saml
                redirect('autenticacion/login?redirect='.current_url());
            }

            $data['error'] = 'Usuario no tiene permisos para ejecutar esta etapa.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view($template, $data);
            return;
        }
        if (!$etapa->pendiente) {
            $data['error'] = 'Esta etapa ya fue completada';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view($template, $data);
            return;
        }
        if (!$etapa->Tarea->activa()) {
            $data['error'] = 'Esta etapa no se encuentra activa';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view($template, $data);
            return;
        }
        if ($etapa->vencida()) {
            $data['error'] = 'Esta etapa se encuentra vencida';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view($template, $data);
            return;
        }

        $qs = $this->input->server('QUERY_STRING');
        $paso = $etapa->getPasoEjecutable($secuencia);
        if (!$paso) {
            redirect('etapas/ejecutar_fin/' . $etapa->id . ($qs ? '?' . $qs : ''));
        }
        else if (($etapa->Tarea->final || !$etapa->Tarea->paso_confirmacion) && $paso->getReadonly() && end($etapa->getPasosEjecutables()) == $paso) { //No se requiere mas input
            $etapa->iniciarPaso($paso, $secuencia);
            $etapa->finalizarPaso($paso, $secuencia);
            $etapa->avanzar();
            redirect('etapas/ver/' . $etapa->id . '/' . (count($etapa->getPasosEjecutables())-1));
        }
        else {
            $etapa->iniciarPaso($paso, $secuencia);


            $data['funcionario_actuando_como_ciudadano'] = $funcionario_actuando_como_ciudadano;
            $data['usuario_nombres'] = $usuario_sesion->nombres;

            $data['secuencia'] = $secuencia;
            $data['etapa'] = $etapa;
            $data['paso'] = $paso;
            $data['qs'] = $this->input->server('QUERY_STRING');

            if(!$funcionario_actuando_como_ciudadano){
              $data['sidebar'] = $usuario_sesion->registrado ? 'inbox' : 'disponibles';
            }
            else{
              $data['sidebar'] = 'busqueda_ciudadano';
            }

            $data['content'] = 'etapas/ejecutar';
            $data['title'] = $etapa->Tarea->nombre;

            // Paso actual (se utiliza diferente de 'secuencia' ya que se incrementará en la vista).
            $data['step_position'] = $secuencia;

            $this->load->view($template, $data);
        }
    }

    public function ejecutar_form($etapa_id, $secuencia) {
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

        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

        if ($etapa->usuario_id != $usuario_sesion->id) {
            $data['error'] = 'Usuario no tiene permisos para ejecutar esta etapa.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
            return;
        }
        if (!$etapa->pendiente) {
            $data['error'] = 'Esta etapa ya fue completada';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
            return;
        }
        if (!$etapa->Tarea->activa()) {
            $data['error'] = 'Esta etapa no se encuentra activa';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
            return;
        }
        if ($etapa->vencida()) {
            $data['error'] = 'Esta etapa se encuentra vencida';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
            return;
        }

        $paso = $etapa->getPasoEjecutable($secuencia);
        $formulario = $paso->Formulario;
        $modo = $paso->modo;

        $respuesta = new stdClass();

        if ($modo == 'edicion') {
            $organismo_id = null;

            $validar_formulario = FALSE;
            foreach ($formulario->Campos as $c) {
                //Validamos los campos que no sean readonly y que esten disponibles (que su campo dependiente se cumpla)
                if ($c->isEditableWithCurrentPOST()) {
                  $c->formValidate($etapa->id);
                  $validar_formulario = TRUE;
                }
            }

            // Si se requiere guardado parcial.
            if($this->input->post('no_advance') == 1) {
              $validado = true;
            }
            else {
              if($this->form_validation->run() == TRUE) {
                $validado = true;
              }
              else {
                $validado = false;
              }
            }

            if (!$validar_formulario || $validado) {
              if($this->input->post('documento_tramite_inicial__e'.$etapa->id)) {
                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('documento_tramite_inicial__e'.$etapa->tramite_id, $etapa->id);
                if (!$dato)
                  $dato = new DatoSeguimiento();

                $dato->nombre = 'documento_tramite_inicial__e'.$etapa->tramite_id;
                $dato->valor = $this->input->post('documento_tramite_inicial__e'.$etapa->id);
                $dato->etapa_id = $etapa->id;
                $dato->save();
              }

              if($this->input->post('email_tramite_inicial__e'.$etapa->id)) {
                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('email_tramite_inicial__e'.$etapa->id, $etapa->id);
                if (!$dato)
                  $dato = new DatoSeguimiento();

                $dato->nombre = 'email_tramite_inicial__e'.$etapa->id;
                $dato->valor = $this->input->post('email_tramite_inicial__e'.$etapa->id);
                $dato->etapa_id = $etapa->id;
                $dato->save();
              }

                //Almacenamos los campos
                foreach ($formulario->Campos as $c) {

                    if($c->tipo == 'pagos' && $c->pago_online == 0 && UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
                      $pago = Doctrine_Query::create()
                                  ->from('Pago p')
                                  ->where('p.id_etapa = ?', $etapa->id)
                                  ->andWhere('p.estado = ?', 'pago_func')
                                  ->fetchOne();

                      if($this->input->post('check_pago_online') == 1 && !$pago) {
                            $pago = new Pago();
                            $pago->id_tramite = 0;
                            $pago->id_tramite_interno = $etapa->tramite_id;
                            $pago->id_etapa = $etapa->id;
                            $pago->id_solicitud = '';
                            $pago->estado = 'pago_func';
                            $pago->fecha_actualizacion = date('d/m/Y H:i');
                            $pago->pasarela = '';
                            $pago->save();
                      }

                      if($this->input->post('check_cancelar_pago') == 1 && $pago) {//este caso es cuando cancelan el pago
                          $pago->delete();
                      }
                    }

                    //Almacenamos los campos que no sean readonly y que esten disponibles (que su campo dependiente se cumpla)
                    if ($c->isEditableWithCurrentPOST()) {
                        if(($c->tipo != 'error') && ($c->tipo != 'dialogo')) {
                          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($c->nombre, $etapa->id);
                          if (!$dato)
                            $dato = new DatoSeguimiento();

                          $dato->nombre = $c->nombre;
                          $dato->valor = $this->input->post($c->nombre);
                          $dato->etapa_id = $etapa->id;
                          $dato->save();

                          if(($c->tipo == 'radio') || ($c->tipo == 'select')) {
                            $array_datos = $c->datos;
                            $array_flat = array();
                            $count = 0;

                            foreach($array_datos as $array3) {
                              $array_flat[$count] = [$array3->etiqueta => $array3->valor];

                              $count = $count + 1;
                            }

                            $dato_encontrado = false;
                            for($i = 0; $i < count($array_flat); $i++) {
                              $dato_buscado = array_search($this->input->post($c->nombre), $array_flat[$i]);
                              if($dato_buscado) {
                                $dato_encontrado = $dato_buscado;
                              }
                            }

                            if($dato_encontrado) {
                              $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($c->nombre.'__etiqueta', $etapa->id);
                              if ($dato)
                                $dato->delete();

                              $dato = new DatoSeguimiento();
                              $dato->nombre = $c->nombre.'__etiqueta';
                              $dato->valor = $dato_encontrado;
                              $dato->etapa_id = $etapa->id;
                              $dato->save();
                            }
                          }

                          if($c->tipo == 'checkbox') {
                            $array_datos = $c->datos;
                            $array_flat = array();
                            $buscar_array = $this->input->post($c->nombre);

                            $dato_encontrado = [];
                            foreach($buscar_array as $buscar) {
                              $count = 0;

                              foreach($array_datos as $array3) {
                                $array_flat[$count] = [$array3->etiqueta => $array3->valor];

                                $count = $count + 1;
                              }

                              for($i = 0; $i < count($array_flat); $i++) {
                                $dato_buscado = array_search($buscar, $array_flat[$i]);
                                if($dato_buscado) {
                                  array_push($dato_encontrado, $dato_buscado);
                                }
                              }
                            }

                            if($dato_encontrado) {
                              $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($c->nombre.'__etiqueta', $etapa->id);
                              if ($dato)
                                $dato->delete();

                              $dato = new DatoSeguimiento();
                              $dato->nombre = $c->nombre.'__etiqueta';
                              $dato->valor = json_encode($dato_encontrado);
                              $dato->etapa_id = $etapa->id;
                              $dato->save();
                            }
                          }
                        }
                    }
                }
                $etapa->save();

                // Si se requiere guardado parcial.
                if($this->input->post('no_advance') == 1) {
                  $respuesta->validacion = TRUE;
                  if($funcionario_actuando_como_ciudadano){
                    $respuesta->redirect = site_url('/etapas/inbox?funcionario_ciudadano=1');
                  }
                  else{
                    $respuesta->redirect = site_url('/etapas/inbox');
                  }
                }
                else {
                  $etapa->finalizarPaso($paso, $secuencia);

                  // -- Si encuentra variables de errores avisa que se ha registrado un error de parte de una acción.
                  $errors = false;
                  foreach ($formulario->Campos as $c) {
                    if(($c->tipo == 'error') || ($c->tipo == 'dialogo')) {
                      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $c->valor_default), $etapa->id);

                      if($dato) {
                        if($dato->valor != "") {
                          $errors = true;
                        }
                      }
                    }
                    elseif($c->tipo == 'documento') {
                      $nombre_documento = $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($c->nombre, $etapa->id);
                      $file = Doctrine::getTable('File')->findOneByTipoAndFilename('documento', $nombre_documento->valor);

                      if($c->extra->firmar == 'on' && $c->extra->requerido == 'on' && !UsuarioSesion::usuarioMesaDeEntrada() && !$this->session->userdata('id_usuario_ciudadano')) {

                        if(!$file->firmado) {
                          $errors = true;

                          $debe_firmar = $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($file->filename.'__error', $etapa->id);
                          if($debe_firmar)
                            $debe_firmar->delete();

                          $dato = new DatoSeguimiento();
                          $dato->nombre = $file->filename.'__error';
                          $dato->valor = ERROR_FIRMA_REQUERIDA;
                          $dato->etapa_id = $etapa->id;
                          $dato->save();
                        }
                      }

                      if($c->extra->firmar == 'on' && $c->extra->requerido == 'on' && $c->firma_electronica == 0 && UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')){

                        $debe_firmar = $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($file->filename.'__error', $etapa->id);
                        if($debe_firmar)
                          $debe_firmar->delete();

                          if(!$this->input->post('check_firma_electronica') && !$file->firmado) {
                                $errors = true;

                                $debe_firmar = $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($file->filename.'__error', $etapa->id);
                                if($debe_firmar)
                                  $debe_firmar->delete();

                                $dato = new DatoSeguimiento();
                                $dato->nombre = $file->filename.'__error';
                                $dato->valor = ERROR_FIRMA_REQUERIDA;
                                $dato->etapa_id = $etapa->id;
                                $dato->save();
                          }

                          if($this->input->post('check_firma_electronica') == 1 && !$file->firmado) {
                                $file->firmado = 1;
                                $file->save();
                          }

                          if($this->input->post('check_cancelar_firma') == 1 && $file->firmado) {
                            //este caso es cuando cancelan el pago
                              $file->firmado = 0;
                              $file->save();
                          }
                      }
                    }
                  }

                  $respuesta->validacion = TRUE;

                  // ----- traza inicio
                  if($etapa->Tarea->trazabilidad) {
                    $tarea_inicial = $formulario->Proceso->getTareaInicial();

										$cabezal = '0';
										$num_paso = $secuencia;
										$num_paso_linea = $secuencia + 1;

										if((sizeof($etapa->getPasosEjecutables())-1) == $secuencia && !$tarea_inicial) {
											$estado = '2';
											$estado_linea = 'F';
										}
										else {
											if(($tarea_inicial->id == $etapa->Tarea->id) && ($secuencia == 0)) {
												$cabezal = '1';
												$estado = '1';
											}
											else {
												$estado = '2';
											}

											$estado_linea = 'I';
										}

                    try {
      								$traza_existente = Doctrine_Query::create()
													->from('Trazabilidad ts')
													->where('ts.id_tramite = ? AND ts.id_etapa = ? AND ts.num_paso_real = ?',
														array($etapa->Tramite->id, $etapa->id, $paso->orden))
													->limit(1)
													->fetchOne();

											$paso_existe = null;
											if(!empty($traza_existente)) {
												$paso_existe = $traza_existente->num_paso_real;
											}

											$traza_tramite = Doctrine_Query::create()
															->from('Trazabilidad ts')
															->where('ts.id_tramite = ?', array($etapa->Tramite->id))
															->orderBy('secuencia DESC')
															->limit(1)
															->fetchOne();

											if(empty($traza_tramite)) {
												$traza_cabezal = Doctrine_Query::create()
															->from('Trazabilidad ts')
															->where('ts.id_tramite = ? AND ts.estado = ?', array($etapa->Tramite->id, 'C'))
															->orderBy('secuencia ASC')
															->fetchOne();

												$sec = 0;
												$sec_linea = $sec + 1;

												if(empty($traza_cabezal)) {
													$traza = new Trazabilidad();
													$traza->id_etapa = $etapa->id;
													$traza->id_tramite = $etapa->tramite_id;
													$traza->id_tarea = $etapa->Tarea->id;
													$traza->num_paso = 0;
													$traza->secuencia = $sec;
													$traza->estado = 'C';
													$traza->save();
												}
												else {
													$cabezal = 0;
												}

												$traza = new Trazabilidad();
												$traza->id_etapa = $etapa->id;
												$traza->id_tramite = $etapa->tramite_id;
												$traza->id_tarea = $etapa->Tarea->id;
												$traza->num_paso = $num_paso + 1;
												$traza->secuencia = $sec + 1;
												$traza->estado = $estado_linea;
												$traza->num_paso_real = $paso->orden;
												$traza->save();
											}
											else {
												$traza_cabezal = Doctrine_Query::create()
															->from('Trazabilidad ts')
															->where('ts.id_tramite = ? AND ts.estado = ?', array($etapa->Tramite->id, 'C'))
															->orderBy('secuencia ASC')
															->fetchOne();

												if(!empty($traza_cabezal)) {
													$cabezal = '0';
												}

												$traza_tramite_actual = Doctrine_Query::create()
															->from('Trazabilidad ts')
															->where('ts.id_tramite = ? AND ts.id_etapa = ?', array($etapa->Tramite->id, $etapa->id))
															->orderBy('secuencia DESC')
															->fetchOne();

												$sec = $traza_tramite->secuencia + 1;
												$sec_linea = $sec;

												$traza = new Trazabilidad();
												$traza->id_etapa = $etapa->id;
												$traza->id_tramite = $etapa->tramite_id;
												$traza->id_tarea = $etapa->Tarea->id;

												if(empty($traza_tramite_actual)) {
													$traza_misma_tarea = Doctrine_Query::create()
																->from('Trazabilidad ts')
																->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso->orden))
																->orderBy('secuencia DESC')
																->fetchOne();

													$traza->num_paso = (!$traza_misma_tarea ? $traza_tramite->num_paso + 1 : $traza_misma_tarea->num_paso);
													$num_paso = $traza_tramite->num_paso + 1;
													$num_paso_linea = (!$traza_misma_tarea ? $num_paso : $traza_misma_tarea->num_paso);
													$traza->secuencia = $sec;
												}
												else {
													$traza->num_paso = (!$paso_existe ? $traza_tramite->num_paso + 1 : $traza_tramite->num_paso);
													$num_paso = (!$paso_existe ? $traza_tramite->num_paso + 1 : $paso_existe);
													$num_paso_linea = (!$paso_existe ? $num_paso : $paso_existe);
													$traza->secuencia = $traza_tramite->secuencia + 1;
												}

												$traza->estado = $estado_linea;
												$traza->num_paso_real = $paso->orden;
												$traza->save();
											}

											$this->load->helper('device_helper');
											$canal_inicio = detect_current_device();

											$cantidad_total_pasos = 0;
											foreach($formulario->Proceso->Tareas as $tarea) {
												$cantidad_total_pasos = $cantidad_total_pasos + count($tarea->Pasos);
											}

											(empty($etapa->Tarea->trazabilidad_id_oficina) ? $oficina_id = $formulario->Proceso->ProcesoTrazabilidad->organismo_id : $oficina_id = $etapa->Tarea->trazabilidad_id_oficina);

                      // -- Toma la variable con el ID de estado para traza (idEstadoTrazabilidad)
                      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('idEstadoTrazabilidad', $etapa->id);

                      if($dato) {
                        // -- Verifica primero que el ID de estado sea valido, si no lo es se toma el ID de estado de la tarea misma.
                        $estados_posibles = unserialize(ID_ESTADOS_POSIBLES_TRAZABILIDAD);
                        if(array_key_exists($dato->valor, $estados_posibles)) {
                          $estado = $dato->valor;
                        }
                        else {
                          $estado = $etapa->Tarea->trazabilidad_estado;
                        }
                      }
                      else {
                        $estado = $etapa->Tarea->trazabilidad_estado;
                      }

                      if($secuencia == 0) {
                        ($etapa->Tarea->trazabilidad_cabezal ? $cabezal = '1' : $cabezal = '0');
                      }

			$id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("id_transaccion_traza", $etapa->id);
			$id_transaccion = $id_transaccion->valor;


			if (($secuencia == 0) || ($secuencia == sizeof($etapa->getPasosEjecutables()) - 1)) {
												$args = array('tramite_id' => (string)$etapa->tramite_id, 'secuencia' => (string)$sec_linea, 'paso' => (string)$num_paso_linea,
																		  'organismo_id' => (string)$formulario->Proceso->ProcesoTrazabilidad->organismo_id, 'oficina_id' => (string)$oficina_id,
																		  'proceso_externo_id' => (string)$formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id,
																		  'usuario_id' => (string)$usuario_sesion->id, 'pasos_ejecutables' => (string)$cantidad_total_pasos,
																		  'cabezal' => (string)$cabezal, 'nombre_tarea' => (string)$etapa->Tarea->nombre, 'estado' => (string)$estado,
                                      'etapa_id' => (string)$etapa->id, 'canal_inicio' => (string)$canal_inicio, 'nombre_paso' => (string)$paso->nombre,
                                      'id_transaccion' => (string)$id_transaccion);

												// -- Encola la operacion
												$CI =& get_instance();
												$CI->load->library('resque/resque');
												Resque::enqueue('default', 'Trazabilidad', $args);
											}
                    }
                    catch(Exception $e) {
                      log_message('error', $e->getMessage());
                    }
                  }
                  // ----- traza fin

                  $qs = $this->input->server('QUERY_STRING');
                  $prox_paso = $etapa->getPasoEjecutable($secuencia + 1);

                  // Si hay registro de error de parte de una acción invocada vuelve a mostrar la secuencia actual, de lo contrario avanza.
                  if($errors) {
                      $respuesta->redirect = site_url('etapas/ejecutar/' . $etapa_id . '/' . ($secuencia)) . ($qs ? '?' . $qs : '');
                  }
                  else {
                    if (!$prox_paso) {
                        $respuesta->redirect = site_url('etapas/ejecutar_fin/' . $etapa_id) . ($qs ? '?' . $qs : '');
                    } else if ($etapa->Tarea->final && $prox_paso->getReadonly() && end($etapa->getPasosEjecutables()) == $prox_paso) { //Cerrado automatico
                        $etapa->iniciarPaso($prox_paso, $secuencia);
                        $etapa->finalizarPaso($prox_paso, $secuencia);
                        $etapa->avanzar();
                        $respuesta->redirect = site_url('etapas/ver/' . $etapa->id . '/' . (count($etapa->getPasosEjecutables())-1));
                    } else {
                        $respuesta->redirect = site_url('etapas/ejecutar/' . $etapa_id . '/' . ($secuencia + 1)) . ($qs ? '?' . $qs : '');
                    }
                  }
              }
            } else {
                $respuesta->validacion = FALSE;
                $respuesta->errores = validation_errors();
            }
        } else if ($modo == 'visualizacion') {
            $respuesta->validacion = TRUE;

            $qs = $this->input->server('QUERY_STRING');
            $prox_paso = $etapa->getPasoEjecutable($secuencia + 1);

            if (!$prox_paso) {
                $respuesta->redirect = site_url('etapas/ejecutar_fin/' . $etapa_id) . ($qs ? '?' . $qs : '');
            } else if ($etapa->Tarea->final && $prox_paso->getReadonly() && end($etapa->getPasosEjecutables()) == $prox_paso) { //Cerrado automatico
                $etapa->iniciarPaso($prox_paso, $secuencia);
                $etapa->finalizarPaso($prox_paso, $secuencia);
                $etapa->avanzar();
                $respuesta->redirect = site_url('etapas/ver/' . $etapa->id . '/' . (count($etapa->getPasosEjecutables())-1));
            } else {
                $respuesta->redirect = site_url('etapas/ejecutar/' . $etapa_id . '/' . ($secuencia + 1)) . ($qs ? '?' . $qs : '');
            }
        }

        echo json_encode($respuesta);
    }

    public function asignar($etapa_id) {
        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

        if ($etapa->usuario_id) {
            $data['error'] = 'Etapa ya fue asignada.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
            return;
        }

        if (!$etapa->canUsuarioAsignarsela(UsuarioSesion::usuario()->id)) {
            $data['error'] = 'Usuario no puede asignarse esta etapa.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
            return;
        }

        $etapa->asignar(UsuarioSesion::usuario()->id);

        redirect('etapas/inbox');
    }

    public function ejecutar_fin($etapa_id) {
        //verifico si el usuario pertenece el grupo MesaDeEntrada y hay actuando como un ciudadano
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

        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

        if ($etapa->usuario_id != $usuario_sesion->id) {
            $data['error'] = 'Usuario no tiene permisos para ejecutar esta etapa.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
            return;
        }
        if (!$etapa->pendiente) {
            $data['error'] = 'Esta etapa ya fue completada';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
            return;
        }
        if (!$etapa->Tarea->activa()) {
            $data['error'] = 'Esta etapa no se encuentra activa';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
            return;
        }

        //if($etapa->Tarea->asignacion!='manual'){
        //    $etapa->Tramite->avanzarEtapa();
        //    redirect();
        //    exit;
        //}

        $hay_pasos_generar_pdf = false;
        $pasos_ejecutables = $etapa->getPasosEjecutables();
        foreach ($pasos_ejecutables as $paso) {
          if($paso->generar_pdf == 1){
            $hay_pasos_generar_pdf = true;
            break;
          }
        }


        $data['link_pdf'] = $this->imprimir_pasos_pdf($etapa->id);
        $data['hay_pasos_generar_pdf'] = $hay_pasos_generar_pdf;

        $data['etapa'] = $etapa;
        $data['tareas_proximas'] = $etapa->getTareasProximas();
        $data['qs'] = $this->input->server('QUERY_STRING');

        if(!$funcionario_actuando_como_ciudadano){
          $data['sidebar'] = $usuario_sesion->registrado ? 'inbox' : 'disponibles';
        }
        else{
          $data['sidebar'] = 'busqueda_ciudadano';
        }

        $data['funcionario_actuando_como_ciudadano'] = $funcionario_actuando_como_ciudadano;
        $data['usuario_nombres'] = $usuario_sesion->nombres;
        $data['content'] = 'etapas/ejecutar_fin';

        $data['title'] = $etapa->Tarea->nombre;
        $template = $this->input->get('iframe') ? 'template_iframe' : 'template';

        $this->load->view($template, $data);
    }

    public function ejecutar_fin_form($etapa_id) {
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

        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

        if ($etapa->usuario_id != $usuario_sesion->id) {
            $data['error'] = 'Usuario no tiene permisos para ejecutar esta etapa.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
            return;
        }
        if (!$etapa->pendiente) {
            $data['error'] = 'Esta etapa ya fue completada';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
            return;
        }
        if (!$etapa->Tarea->activa()) {
            $data['error'] = 'Esta etapa no se encuentra activa';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
            return;
        }

        $etapa->avanzar($this->input->post('usuarios_a_asignar'));

        // -- Si encuentra variables de errores avisa que se ha registrado un error de parte de una acción.
        $errors = false;
        $errors_msg = '';
        $error_servicio_com = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("ws_error", $etapa->id);
        $error_servicio = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("error", $etapa->id);

        if($error_servicio_com) {
          if($error_servicio_com->valor != "") {
            $errors = true;
            $errors_msg = $error_servicio_com->valor;
          }
        }
        if($error_servicio) {
          if($error_servicio->valor != "") {
            $errors = true;
            $errors_msg = $error_servicio->valor;
          }
        }

        // Si hay registro de error de parte de una acción invocada vuelve a mostrar la secuencia actual, de lo contrario avanza.
        if($errors) {
          $respuesta->redirect = site_url('etapas/ejecutar_fin/' . $etapa_id);
          $respuesta->error_paso_final = $errors_msg;
        }
        else {
          $respuesta = new stdClass();
          $respuesta->validacion = TRUE;

          if($funcionario_actuando_como_ciudadano){
            $respuesta->redirect = site_url('etapas/inbox?funcionario_ciudadano=1');
          }
          else if(!$funcionario_actuando_como_ciudadano && $this->input->get('iframe')){
            $respuesta->redirect = site_url('etapas/ejecutar_exito');
          }
          else {
            $respuesta->redirect = site_url();
          }
        }

        echo json_encode($respuesta);
    }

    //Pagina que indica que la etapa se completo con exito. Solamente la ven los que acceden mediante iframe.
    public function ejecutar_exito() {
        $data['content'] = 'etapas/ejecutar_exito';
        $data['title'] = 'Etapa completada con éxito';

        $this->load->view('template_iframe', $data);
    }

    public function ver($etapa_id, $secuencia = 0) {
        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

        if (UsuarioSesion::usuario()->id != $etapa->usuario_id) {
            $data['error'] = 'No tiene permisos para hacer seguimiento a este tramite.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
            return;
        }

        $paso = $etapa->getPasoEjecutable($secuencia);

        if (($etapa->Tarea->final || !$etapa->Tarea->paso_confirmacion) && $paso->getReadonly() && end($etapa->getPasosEjecutables()) == $paso && !($secuencia + 1 < count($etapa->getPasosEjecutables()))){
           $data['link_pdf'] = $this->imprimir_pasos_pdf($etapa->id);
        }

        $data['etapa'] = $etapa;
        $data['paso'] = $paso;
        $data['secuencia'] = $secuencia;

        $data['sidebar'] = 'participados';
        $data['title'] = 'Historial - ' . $etapa->Tarea->nombre;
        $data['content'] = 'etapas/ver';
        $this->load->view('template', $data);
    }

    public function imprimir_pasos_pdf($etapa_id) {
      if($etapa_id) {
          $documento = new Documento();
          $documento->tipo='pasos';
          $documento->tamano = 'letter';
          $array_etapas_para_pdf = array();

          $etapa_actual = Doctrine::getTable('Etapa')->find($etapa_id);

          $tramite = $etapa_actual->Tramite;

          $etapas_compleatas = $tramite->getEtapasCompletadas($etapa_id);

          $primera_etapa = true;

          foreach ($etapas_compleatas as $etapa) {

            if(!$primera_etapa){
              $documento->contenido .= '<br pagebreak="true" />';
            }

            $array_pasos_ejecutables = $etapa->getPasosEjecutables();
            $array_formularios_ejecutables = array();
            $hay_pasos_generar_pdf = false;

            foreach ($array_pasos_ejecutables as $paso_ejecutable) {

              if($paso_ejecutable->generar_pdf == 1) {

                $hay_pasos_generar_pdf = true;

                foreach($etapa->Tramite->Proceso->Formularios as $formulario) {
                  //si el formulario tiene campos
                  if(count($formulario->Campos) > 0){
                    //si el formulario de la etapa esta dentro de los pasos ejecutables, recorro su lista de campos
                     if($paso_ejecutable->formulario_id == $formulario->id) {
                        $array_campos_ejecutables = array();

                        foreach($formulario->Campos as $campo) {
                          //guardo un array de campos ejecutables (if con campos omitidos)
                          if($campo->tipo != 'agenda' && $campo->tipo != 'pagos' && $campo->tipo != 'documento' && $campo->tipo != 'fieldset'){

                            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo->nombre, $etapa->id);

                            if($dato && $dato->valor != ''){
                              array_push($array_campos_ejecutables, $campo);
                            }

                            if($campo->valor_default != '' && $campo->readonly == 1 && !($campo->tipo == 'paragraph' || $campo->tipo == 'dialogo')){
                              array_push($array_campos_ejecutables, $campo);
                            }

                            if($campo->tipo == 'dialogo'){
                              array_push($array_campos_ejecutables, $campo);
                            }

                            if($campo->tipo == 'paragraph'){
                              array_push($array_campos_ejecutables, $campo);
                            }

                          }
                        }

                        $formulario_ejecutable = new stdClass();
                        $formulario_ejecutable->formulario = $formulario;
                        $formulario_ejecutable->campos = $array_campos_ejecutables;
                        $formulario_ejecutable->nombre_paso = $paso_ejecutable->nombre;
                        //Guardo en un array el formulario ejecutable con sus campos
                        array_push($array_formularios_ejecutables, $formulario_ejecutable);
                     }
                  }
                }
              }
            }

            if($hay_pasos_generar_pdf) {
              $cont = 0;
              foreach($array_formularios_ejecutables as $formulario_ejecutable) {

                  $documento->contenido .= '<h1 style="text-align:center">'.$formulario_ejecutable->nombre_paso.'</h1>';

                  foreach ($formulario_ejecutable->campos as $campo_ejecutable) {

                    if(($campo_ejecutable->valor_default != '' && $campo_ejecutable->readonly == 1) || ($campo_ejecutable->tipo == 'paragraph') || ($campo_ejecutable->esVisibleParaLaEtapaActual($etapa->id))) {
                      $variable_campo = '@@'.$campo_ejecutable->nombre;

                      if ($campo_ejecutable->tipo == 'tabla-responsive'){
                          $documento->contenido .= '<strong>'.$campo_ejecutable->etiqueta.': </strong><br>'.$variable_campo.'<br><br>';
                      }
                      else if($campo_ejecutable->tipo == 'radio' || $campo_ejecutable->tipo == 'select'){
                          $variable_campo = '@@'.$campo_ejecutable->nombre.'__etiqueta';
                          $documento->contenido .= '<strong>'.$campo_ejecutable->etiqueta.': </strong>'.$variable_campo.'<br><br>';
                      }
                      else if($campo_ejecutable->tipo == 'dialogo'){
                          $documento->contenido .=  $campo_ejecutable->valor_default.'<br><br>';
                      }
                      else if($campo_ejecutable->tipo == 'paragraph'){
                          $documento->contenido .=  $campo_ejecutable->etiqueta.'<br><br>';
                      }
                      else if($campo_ejecutable->valor_default != '' && $campo_ejecutable->readonly == 1 ){
                          $documento->contenido .= '<strong>'.$campo_ejecutable->etiqueta.': </strong>'.$campo_ejecutable->valor_default.'<br><br>';
                      }
                      else {
                          $documento->contenido .= '<strong>'.$campo_ejecutable->etiqueta.':</strong> '.$variable_campo.'<br>';
                      }
                    }
                  }

                  $cont++;

                  if($cont != count($array_formularios_ejecutables)){
                    $documento->contenido .= '<br pagebreak="true" />';
                  }
                  /*else{
                    $documento->contenido .= '<p style="color:Red;text-align:center"> ----- Fin etapa '.$etapa->id.' ---- </p>';
                  }*/
              }

              array_push($array_etapas_para_pdf, $etapa);
              $primera_etapa = false;

            }
        }
        $cuenta = Cuenta::cuentaSegunDominio();

        $link_pdf = $documento->generar_pasos_pdf($etapa_actual , $etapa->Tramite->Proceso->nombre, $cuenta);

        return $link_pdf;
      }
      else{
        redirect(site_url());
      }
    }

    //callback function para validacion de clausula informada.
     //Como es una validacion de un campo el callback tienen que estar en esta clase.
     public function required_clausula($str) {
       //para que se lista el mensaje en el campo es necesario que el mensaje contenga <strong>%s</strong>
       $this->form_validation->set_message('required_clausula',"Para continuar con el trámite, debe aceptar los <strong>%s</strong> de Consentimiento ");
       return (trim($str) == '') ? FALSE : TRUE;
     }

     public function busqueda_ciudadano() {
       if(!UsuarioSesion::usuario()->registrado && !UsuarioSesion::usuarioMesaDeEntrada()) {
         redirect(site_url());
       }

       $data['sidebar'] = 'busqueda_ciudadano';
       $data['content'] = 'etapas/busqueda_ciudadano';
       $data['title'] = 'Búsqueda Ciudadano';
       $this->load->view('template', $data);
     }

     public function busqueda_ciudadano_form() {
       if(!UsuarioSesion::usuario()->registrado && !UsuarioSesion::usuarioMesaDeEntrada()) {
         redirect(site_url());
       }
       $respuesta = new stdClass();

       $this->form_validation->set_rules('documento','Documento','required|ci');
       $this->form_validation->set_rules('tipo_documento','Tipo de documento','required');
       $this->form_validation->set_rules('pais','País','required');

       if ($this->form_validation->run() == TRUE) {
           $respuesta->validacion = TRUE;

           $usuario_busqueda = $this->input->post('pais').'-'.$this->input->post('tipo_documento').'-'.$this->input->post('documento');
           $usuario_econtrado = Doctrine_Query::create()
               ->from('Usuario u')
               ->where('u.usuario = ?', $usuario_busqueda)
               ->fetchOne();

           $doc_usuario_logueado = split("-", UsuarioSesion::usuario()->usuario)[2];
           $doc_usuario_ecnotrado = $this->input->post('documento');

          if($usuario_econtrado && trim($doc_usuario_logueado)  != trim($doc_usuario_ecnotrado)){
            $respuesta->redirect = site_url('etapas/inbox?funcionario_ciudadano=1');
            $this->session->set_userdata('id_usuario_ciudadano', $usuario_econtrado->id);
          }
          else if($usuario_econtrado && trim($doc_usuario_logueado)  == trim($doc_usuario_ecnotrado)){
            $respuesta->validacion = FALSE;
            $respuesta->errores = 'El docmuento ingresado es el mismo que el del usuario logueado.';
          }
          else {
              $respuesta->validacion = TRUE;
              $respuesta->redirect = site_url('etapas/registro_ciudadano?doc='.$this->input->post('documento').'&tipo='.$this->input->post('tipo_documento').'&pais='.$this->input->post('pais'));
          }

       } else {
           $respuesta->validacion = FALSE;
           $respuesta->errores = validation_errors();
       }

       echo json_encode($respuesta);
     }

     public function registro_ciudadano() {
        if(!UsuarioSesion::usuario()->registrado && !UsuarioSesion::usuarioMesaDeEntrada()) {
          redirect(site_url());
        }

        $data['documento'] = $this->input->get('doc');
        $data['tipo_documento'] = $this->input->get('tipo');
        $data['pais'] = $this->input->get('pais');

        $data['sidebar'] = 'busqueda_ciudadano';
        $data['content'] = 'etapas/registro_ciudadano';
        $data['title'] = 'Registro Ciudadano';
        $this->load->view('template', $data);
      }

      public function registro_ciudadano_form() {
        if(!UsuarioSesion::usuario()->registrado && !UsuarioSesion::usuarioMesaDeEntrada()) {
          redirect(site_url());
        }

        $respuesta = new stdClass();

        $this->form_validation->set_rules('nombres','Nombres','required');
        $this->form_validation->set_rules('apellido_paterno','Apellido Paterno','required');
        $this->form_validation->set_rules('apellido_materno','Apellido Materno','required');
        $this->form_validation->set_rules('email','Correo electrónico','required');
        $this->form_validation->set_rules('documento','Documento','required|ci');
        $this->form_validation->set_rules('tipo_documento','Tipo de documento','required');
        $this->form_validation->set_rules('pais','País','required');

        if ($this->form_validation->run() == TRUE) {

          $usuario_busqueda = $this->input->post('pais').'-'.$this->input->post('tipo_documento').'-'.$this->input->post('documento');

          $usuario_econtrado = Doctrine_Query::create()
              ->from('Usuario u')
              ->where('u.usuario = ?', $usuario_busqueda)
              ->fetchOne();

            if(!$usuario_econtrado){
              $usuario = new Usuario();
              $usuario->usuario = $usuario_busqueda;
              $usuario->nombres = $this->input->post('nombres');
              $usuario->apellido_paterno = $this->input->post('apellido_paterno');
              $usuario->apellido_materno = $this->input->post('apellido_materno');
              $usuario->email = $this->input->post('email');
              $usuario->registrado = 1;
              $usuario->save();

              $this->session->unset_userdata('id_usuario_ciudadano');
              $this->session->set_userdata('id_usuario_ciudadano', $usuario->id);

              $respuesta->validacion = TRUE;
              $respuesta->redirect = site_url('etapas/inbox?funcionario_ciudadano=1');
            }
            else{
              $respuesta->validacion = FALSE;
              $respuesta->errores = 'Ya existe un usuario registrado con este documento.';
            }

        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }
}

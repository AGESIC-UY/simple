<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Seguimiento extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if(!UsuarioBackendSesion::has_rol('super') && !UsuarioBackendSesion::has_rol('operacion') && !UsuarioBackendSesion::has_rol('seguimiento')){
            redirect('backend');
        }
        $this->load->helper('trazabilidad_helper');
    }

    public function index() {
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $query_proceso = Doctrine_Query::create()
                ->select('p.*')
                ->from('Proceso p, p.Cuenta c')
                ->addSelect('(SELECT count(t.id)
                        FROM Tramite t
                        WHERE t.proceso_id = p.id
                        LIMIT 1) as ntramites')
                ->where('p.activo=1 AND p.estado!="arch" AND c.id = ? 
                AND ((SELECT COUNT(proc.id) FROM Proceso proc WHERE proc.cuenta_id = ? AND (proc.root = p.id OR proc.root = p.root) AND proc.estado = "draft") = 0 
                OR p.estado = "draft")
                ', array($cuenta_id, $cuenta_id))
                ->andWhere('p.nombre != "BLOQUE"')
                ->orderBy('p.nombre asc');
        $procesos = $query_proceso->execute();

        $data['procesos']=$procesos;
        
        $data['title'] = 'Listado de Procesos';
        $data['content'] = 'backend/seguimiento/index';
        $this->load->view('backend/template', $data);
    }

    public function index_proceso($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);
        $procesosArchivados = $proceso->findProcesosArchivados((($proceso->root) ? $proceso->root : $proceso->id));
        if (UsuarioBackendSesion::usuario()->cuenta_id != $proceso->cuenta_id) {
            echo 'Usuario no tiene permisos';
            exit;
        }

        $query = $this->input->get('query');
        $offset = $this->input->get('offset');
        if (!$this->input->get('order')){
          $order = 'updated_at';
        }else{
          $order = $this->input->get('order');
        }

        $direction = $this->input->get('direction') == 'desc' ? 'desc' : 'asc';
        $created_at_desde=$this->input->get('created_at_desde');
        $created_at_hasta=$this->input->get('created_at_hasta');
        $updated_at_desde=$this->input->get('updated_at_desde');
        $updated_at_hasta=$this->input->get('updated_at_hasta');
        $pendiente=$this->input->get('pendiente')!==false?$this->input->get('pendiente'):-1;
        $per_page=100;
        $busqueda_avanzada=$this->input->get('busqueda_avanzada');

        $documento = $this->input->get('documento');
        $pais = $this->input->get('pais');
        $tipo_documento = $this->input->get('tipo_documento');


        $doctrine_query = Doctrine_Query::create()
                ->from('Tramite t, t.Proceso p, t.Etapas e, e.Usuario u, e.DatosSeguimiento d')
                ->where('p.id = ?', $proceso_id)
                ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')  //Mostramos solo los que se han avanzado o tienen datos
                ->groupBy('t.id')
                ->orderBy($order.' '.$direction)
                ->limit($per_page)
                ->offset($offset);


        if($created_at_desde)
            $doctrine_query->andWhere ('created_at >= ?',array(date('Y-m-d H:i:s',strtotime($created_at_desde.' 00:00:00'))));
        if($created_at_hasta)
            $doctrine_query->andWhere ('created_at <= ?',array(date('Y-m-d H:i:s',strtotime($created_at_hasta.' 23:59:60'))));
        if($updated_at_desde)
            $doctrine_query->andWhere ('updated_at >= ?',array(date('Y-m-d H:i:s',strtotime($updated_at_desde.' 00:00:00'))));
        if($updated_at_hasta)
            $doctrine_query->andWhere ('updated_at <= ?',array(date('Y-m-d H:i:s',strtotime($updated_at_hasta.' 23:59:60'))));
        if($pendiente!=-1)
            $doctrine_query->andWhere ('pendiente = ?',array($pendiente));

        if ($documento && $pais && $tipo_documento){
          $usuario = $pais. '-'.$tipo_documento .'-'.$documento;
          $doctrine_query->andWhere ('u.usuario = ?',array($usuario));
        }



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

        $data['documento']=$documento;
        $data['pais']=$pais;
        $data['tipo_documento']=$tipo_documento;
        $data['procesos_arch'] = $procesosArchivados;
        $data['busqueda_avanzada']=$busqueda_avanzada;
        $data['proceso'] = $proceso;
        $data['tramites'] = $tramites;

        $data['title'] = 'Seguimiento de ' . $proceso->nombre;
        $data['content'] = 'backend/seguimiento/index_proceso';
        $this->load->view('backend/template', $data);
    }

    public function ver($tramite_id) {
        $tramite = Doctrine::getTable('Tramite')->find($tramite_id);
        
        if (!$tramite) {
            echo 'El trámite ya no se encuentra disponible.';
            exit;
        }

        if (UsuarioBackendSesion::usuario()->cuenta_id != $tramite->Proceso->cuenta_id) {
            echo 'No tiene permisos para hacer seguimiento a este trámite.';
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

        if(!$etapa->canUsuarioRevisarDetalle(UsuarioBackendSesion::usuario())){
          echo 'No tiene permisos para hacer seguimiento a esta etapa.';
          exit;
        }

        $data['etapa'] = $etapa;
        $data['paso']=$paso;
        $data['secuencia'] = $secuencia;

        $data['title'] = 'Seguimiento - ' . $etapa->Tarea->nombre;
        $data['content'] = 'backend/seguimiento/ver_etapa';
        $this->load->view('backend/template', $data);
    }


    public function buscar_ciudadano($etapa_id){
      $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

      if (UsuarioBackendSesion::usuario()->cuenta_id != $etapa->Tramite->Proceso->cuenta_id) {
          echo 'No tiene permisos para hacer seguimiento a este tramite.';
          exit;
      }

      if(!$etapa->canUsuarioReasignarCiudadano(UsuarioBackendSesion::usuario())){
        echo 'No tiene permisos para hacer seguimiento a esta etapa.';
        exit;
      }

      $documento = $this->input->post('documento');
      $pais = $this->input->post('pais');
      $tipo_documento = $this->input->post('tipo_documento');
      $uid = $pais. '-'.$tipo_documento .'-'.$documento;

      //$usuario=Doctrine::getTable('Usuario')->findOneByUsuario($uid);
      $usuario=Doctrine::getTable('Usuario')->findUsuarioEnCuentaOrCiudadano($uid,UsuarioBackendSesion::usuario()->cuenta_id);
      if ($usuario){
        $results = array(
               'nombres' => $usuario->nombres,
               'apellido_paterno' => $usuario->apellido_paterno,
               'apellido_materno' => $usuario->apellido_materno,
               'email' => $usuario->email
           );

        $toReturn = json_encode($results);
        echo $toReturn;

      }else{
        echo "";
      }



    }

    public function reasignar_form_ciudadano($etapa_id) {

            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

            if (UsuarioBackendSesion::usuario()->cuenta_id != $etapa->Tramite->Proceso->cuenta_id) {
                echo 'No tiene permisos para hacer seguimiento a este tramite.';
                exit;
            }

            if(!$etapa->canUsuarioReasignarCiudadano(UsuarioBackendSesion::usuario())){
              echo 'No tiene permisos para hacer seguimiento a esta etapa.';
              exit;
            }

            $this->form_validation->set_rules('documento', 'Documento del Usuario', 'required');
            $this->form_validation->set_rules('pais', 'Pais', 'required');
            $this->form_validation->set_rules('tipo_documento', 'Tipo de Documento', 'required');

            $documento = $this->input->post('documento');
            $pais = $this->input->post('pais');
            $tipo_documento = $this->input->post('tipo_documento');

            $uid = $pais. '-'.$tipo_documento .'-'.$documento;
            $usuario=Doctrine::getTable('Usuario')->findUsuarioEnCuentaOrCiudadano($uid,UsuarioBackendSesion::usuario()->cuenta_id);
            //$usuario=Doctrine::getTable('Usuario')->findOneByUsuario($uid);

            if (!$usuario){
              $this->form_validation->set_rules('nombres', 'Nombre', 'required');
              $this->form_validation->set_rules('apellido_paterno', 'Apellido Paterono', 'required');
              $this->form_validation->set_rules('apellido_materno', 'Apellido Materno', 'required');
              $this->form_validation->set_rules('email', 'Email', 'valid_email');
            }

            $respuesta=new stdClass();
            if ($this->form_validation->run() == TRUE) {
              if (!$usuario){
                  $usuario = new Usuario();
                  $usuario->usuario = $uid;
                  $random_password = random_string('alnum', 32);
                  $usuario->setPasswordWithSalt($random_password);
                  $usuario->nombres = $this->input->post('nombres');
                  $usuario->apellido_paterno = $this->input->post('apellido_paterno');
                  $usuario->apellido_materno =$this->input->post('apellido_materno');
                  $usuario->email =$this->input->post('email');
                  $usuario->save();
              }


              if ($etapa->Usuario && $etapa->Usuario->id){
                $usuarioOriginal=Doctrine::getTable('Usuario')->find($etapa->Usuario->id);
                if ($usuarioOriginal){
                  $etapa->UsuarioOriginal =   $usuarioOriginal;
                }

              }

              if (!$usuarioOriginal){
                $usuarioOriginal = new Usuario();
              }


              if ( $etapa->usuario_original_historico && !empty($etapa->usuario_original_historico) ){
                $data = json_decode($etapa->usuario_original_historico, TRUE);
                array_push($data, array('fecha' =>date("Y-m-d H:i:s"), 'usuarioOriginal'=> $usuarioOriginal->id,'usuario'=> $usuario->id, 'reasignacion' => UsuarioBackendSesion::usuario()->id));
              }else{
                $data= array();
                array_push($data, array('fecha' =>date("Y-m-d H:i:s"), 'usuarioOriginal'=> $usuarioOriginal->id,'usuario'=> $usuario->id, 'reasignacion' => UsuarioBackendSesion::usuario()->id));
              }

              $data_json = json_encode($data);
              $etapa->usuario_original_historico =$data_json;


              $etapa->Usuario = $usuario;
              $etapa->save();

              //reasigna la variable de usuario generada
              if ($etapa->Tarea->almacenar_usuario) {
                  $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($etapa->Tarea->almacenar_usuario_variable,$etapa->id);
                  if (!$dato)
                      $dato = new DatoSeguimiento();

                  $dato->nombre = $etapa->Tarea->almacenar_usuario_variable;
                  $dato->valor = $usuario->id;
                  $dato->etapa_id = $etapa->id;
                  $dato->save();
              }

              $respuesta->validacion = TRUE;
              $respuesta->redirect = site_url('backend/seguimiento/ver_etapa/' . $etapa->id);


            } else {
                //no valida
                $respuesta->validacion = FALSE;
                $respuesta->errores = validation_errors();
            }


        echo json_encode($respuesta);
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
                if (!$this->email->send()){
                    log_message('ERROR', "send email reasignar_form: ".$this->email->print_debugger());
                }

                $respuesta->validacion = TRUE;
                $respuesta->redirect = site_url('backend/seguimiento/ver_etapa/' . $etapa->id);
            } else {
                $respuesta->validacion = FALSE;
                $respuesta->errores = validation_errors();
            }


        echo json_encode($respuesta);
    }

    public function borrar_tramite($tramite_id) {
        if(UsuarioBackendSesion::has_rol('seguimiento'))
            show_error('No tiene permisos',401);


        $tramite = Doctrine::getTable('Tramite')->find($tramite_id);

        if (UsuarioBackendSesion::usuario()->cuenta_id != $tramite->Proceso->cuenta_id) {
            echo 'No tiene permisos para hacer seguimiento a este tramite.';
            exit;
        }
        
        enviar_traza_eliminar_tramite($tramite);
        $tramite->delete();

        redirect($this->input->server('HTTP_REFERER'));
    }

    public function borrar_proceso($proceso_id) {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
        if(UsuarioBackendSesion::has_rol('seguimiento'))
            show_error('No tiene permisos',401);

        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if (UsuarioBackendSesion::usuario()->cuenta_id != $proceso->cuenta_id) {
            echo 'No tiene permisos para hacer seguimiento a este tramite.';
            exit;
        }

        $b64=base64_encode($proceso->id);
        $comando = 'php index.php tasks/eliminartproceso eliminar "' . $b64 . '" > /dev/null &';
        exec($comando);
        //$proceso->Tramites->delete();

        redirect(site_url('backend/seguimiento'));
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

    /**
     *
     */
    public function pagos() {
        $paginado = Doctrine_Query::create()->from('Parametro p')->where('p.cuenta_id = ? AND p.clave = ?', array(Cuenta::cuentaSegunDominio()->id, 'resultados_por_pagina'))->fetchOne();
        if($paginado) {
          $per_page = $paginado->valor;
        } else {
          $per_page = 50;
        }

        $query = $this->input->get('query');
        $offset = $this->input->get('offset');
        /*Datos del filtro*/
        $updated_at_desde=$this->input->get('updated_at_desde');
        $updated_at_hasta=$this->input->get('updated_at_hasta');
        $busqueda_avanzada=$this->input->get('busqueda_avanzada');
        $estado=$this->input->get('estado');
        $idTramiteInterno=$this->input->get('idTramiteInterno');
        $idSolicitud=$this->input->get('idSolicitud');
        $desde = date('d/m/Y', strtotime($updated_at_desde));
        $hasta = date('d/m/Y', strtotime($updated_at_hasta));


        /*Carga los datos en el select*/
        $estados = Doctrine_Query::create()
            ->select('p.estado')
            ->from('Pago p')            
            ->groupBy('p.estado')
            ->where('p.estado <> " "')
            ->execute();


        /*Carga los datos en general*/
        $doctrine_query = Doctrine_Query::create()
            ->select('*')
            ->from('Pago p')
            ->orderBy('p.id_tramite_interno DESC, p.fecha_actualizacion DESC');

        /*Valida el estado y la fecha del filtro*/
            if ($estado){
                $doctrine_query ->where('p.estado = ?', $estado);
            }
            if ($updated_at_desde) {
                $doctrine_query->andWhere("str_to_date(fecha_actualizacion, '%d/%m/%Y') >= str_to_date('" . $desde . "', '%d/%m/%Y')")
                    ->andWhere("str_to_date(fecha_actualizacion, '%d/%m/%Y') <= str_to_date('" . $hasta . "', '%d/%m/%Y')");
            }
            if ($idTramiteInterno) {
                $doctrine_query->andWhere("p.id_tramite_interno=?", trim($idTramiteInterno));
            }
            if ($idSolicitud) {
                $doctrine_query->andWhere("p.id_solicitud=?", trim($idSolicitud));
            }
            $doctrine_query->limit($per_page)
            ->offset($offset);

        $registros=$doctrine_query->execute();
        $nregistros = $doctrine_query->count();

        /*Cuenta la cantidad de pagos para cargar el limite definido*/
        $count_pagos = Doctrine_Query::create()
        ->from('Pago p')
        ->limit($per_page)
        ->offset($offset)
        ->count();

        $this->load->library('pagination');

        if($estado || $updated_at_desde || $idSolicitud || $idTramiteInterno){
            $this->pagination->initialize(array(
                'base_url'=>site_url('backend/seguimiento/pagos?busqueda_avanzada=1&estado='.$estado.'&updated_at_desde='.$updated_at_desde.'&updated_at_hasta='.$updated_at_hasta.'&idTramiteInterno='.$idTramiteInterno.'&idSolicitud='.$idSolicitud),
                'total_rows'=> $nregistros,
                'per_page'=> $per_page
            ));
        }else {
            $this->pagination->initialize(array(
                'base_url'=>site_url('backend/seguimiento/pagos/?'),
                'total_rows'=> $count_pagos,
                'per_page'=> $per_page
            ));
        }

        $data['query'] = $query;
        $data['updated_at_desde']=$updated_at_desde;
        $data['updated_at_hasta']=$updated_at_hasta;
        $data['busqueda_avanzada']=$busqueda_avanzada;
        $data['idSolicitud']=$idSolicitud;
        $data['idTramiteInterno']=$idTramiteInterno;

        $data['estado'] = $estados;
        $data['registros'] = $registros;
        $data['title'] = 'Seguimiento de pagos';
        $data['content'] = 'backend/seguimiento/pagos';
        $this->load->view('backend/template', $data);
    }


    public function ver_pago($id_pago) {
        $registro = Doctrine_Query::create()
            ->from('Pago p')
            ->where('p.id = ?', $id_pago)
            ->orderBy('p.id', 'DESC')
            ->fetchOne();

        $pasarela = Doctrine::getTable('PasarelaPago')->find($registro->pasarela);

        $data['pasarela_nombre'] = $pasarela->nombre;
        $data['registro'] = $registro;
        $data['title'] = 'Seguimiento de pagos';
        $data['content'] = 'backend/seguimiento/ver_pago';
        $this->load->view('backend/template', $data);
    }

    public function liberar($etapa_id) {
      $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

      if (UsuarioBackendSesion::usuario()->cuenta_id != $etapa->Tramite->Proceso->cuenta_id) {
          echo 'No tiene permisos para hacer seguimiento a este tramite.';
          exit;
      }

      if(!$etapa->canUsuarioLiberar(UsuarioBackendSesion::usuario())){
        echo 'No tiene permisos para liberar la etapa.';
        exit;
      }
      if(!$etapa->pendiente){
        echo 'La etapa no está pendiente.';
        exit;
      }
      //libera la etapa

      if ($etapa && $etapa->canUsuarioReasignar(UsuarioBackendSesion::usuario())){
        $etapa->Usuario = NULL;
        $etapa->usuario_id = NULL;
        $etapa->save();
        $respuesta = new stdClass();
        $respuesta->validacion = TRUE;
        $respuesta->redirect = site_url('backend/seguimiento/ver_etapa/' . $etapa->id);

        echo json_encode($respuesta);
      }
    }
}

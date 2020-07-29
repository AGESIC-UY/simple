<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Etapas extends MY_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->helper('pasos_pdf_helper');
        $this->load->helper('trazabilidad_helper');
        $this->load->helper('eliminartramite_helper');
        $this->load->helper('auditoria_helper');
        $this->load->helper('buscar_obn_helper');
        UsuarioSesion::limpiar_sesion();

        if (UsuarioSesion::usuario_con_empresas_luego_login()) {
            redirect('autenticacion/login_empresa');
        }
    }

    public function busqueda_termino() {

        $usuario = UsuarioSesion::usuario();
        $usuario_id = $usuario->id;
        $cuenta_segun_dominio = Cuenta::cuentaSegunDominio();

        $orderby = 'updated_at';
        $direction = $this->input->get('direction') && $this->input->get('direction') != '' ? $this->input->get('direction') == 'desc' ? 'desc' : 'asc' : 'desc';

        $paginado = Doctrine_Query::create()
                ->from('Parametro p')
                ->where('p.cuenta_id = ? AND p.clave = ?', array($cuenta_segun_dominio->id, 'resultados_por_pagina'))
                ->fetchOne();

        if ($paginado) {
            $per_page = $paginado->valor;
        } else {
            $per_page = 50;
        }

        $offset = $this->input->get('offset');

        $resultado_query = Doctrine::getTable('Etapa')->findPendientesConPaginacion($usuario_id, $cuenta_segun_dominio, $orderby, $direction, $per_page, $offset);
        $cantidad_etapas = $resultado_query->cantidad;

        $this->load->library('pagination');
        $this->pagination->initialize(array(
            'base_url' => site_url('etapas/busqueda_termino?'),
            'total_rows' => $cantidad_etapas,
            'per_page' => $per_page
        ));

        $query = $this->input->post('termino');

        if ($query) {
            $etapas_pendientes = $resultado_query->etapas;
            $tramites_id = [];

            foreach ($etapas_pendientes as $etapa_pendiente) {
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

            if ($result['total'] > 0) {
                $matches = array_keys($result['matches']);
                $doctrine_query->whereIn('e.id', $matches);
            } else {
                $doctrine_query->where('0');
            }

            $etapas = $doctrine_query->execute();
            if (count($etapas) < 1) {
                $etapas = [];
            }
        } else {
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
        if (!UsuarioSesion::usuario()->registrado) {
            redirect('autenticacion/login');
        }

        if (!$this->input->get('funcionario_ciudadano') == 1) {
            $this->session->unset_userdata('id_usuario_ciudadano');
        }

        //verifico si el usuario pertenece el grupo MesaDeEntrada y  esta actuando como un ciudadano
        if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
            //el usuario se saca desde la session con el id_usuario_ciudadano
            $usuario_sesion = Doctrine_Query::create()
                    ->from('Usuario u')
                    ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                    ->fetchOne();

            $funcionario_actuando_como_ciudadano = true;
        } else {
            //caso normal el usuario es el logueado
            $usuario_sesion = UsuarioSesion::usuario();
            $funcionario_actuando_como_ciudadano = false;
        }

        $cuenta_segun_dominio = Cuenta::cuentaSegunDominio();

        $paginado = Doctrine_Query::create()
                ->from('Parametro p')
                ->where('p.cuenta_id = ? AND p.clave = ?', array($cuenta_segun_dominio->id, 'resultados_por_pagina'))
                ->fetchOne();

        if ($paginado) {
            $per_page = $paginado->valor;
        } else {
            $per_page = 50;
        }

        $offset = $this->input->get('offset');

        $orderby = $this->input->get('orderby') && $this->input->get('orderby') != '' ? $this->input->get('orderby') : 'updated_at';
        $direction = $this->input->get('direction') && $this->input->get('direction') != '' ? $this->input->get('direction') == 'desc' ? 'desc' : 'asc' : 'desc';

        if ($funcionario_actuando_como_ciudadano) {
            //caso funcionario actuando como ciudadano.
            //En caso de ser un usuario un ciudadanos (no tienen cuenta) se actual de forma normal si es un ciudadano funcionario (tiene cuenta)
            //entonces solo se presentan las tareas iniciales sin asignacion de grupo| no las intermedias

            $es_funcionario = ($usuario_sesion->cuenta_id > 0);
            //por defensa se valida que el usuario oara considerarlo como ciudadano no este en ningun grupo
            if (!$es_funcionario) {
                if ($usuario_sesion->GruposUsuarios && count($usuario_sesion->GruposUsuarios) > 0) {
                    $es_funcionario = true;
                }
            }

            if ($es_funcionario) {
                //ciudadano que es funcionario se tiene que crear una nueva query
                $resultado_query = Doctrine::getTable('Etapa')->findPendientesCiudadanoFuncionarioConPaginacion($usuario_sesion->id, Cuenta::cuentaSegunDominio(), $orderby, $direction, $per_page, $offset);
                $cantidad_etapas = $resultado_query->cantidad;
            } else {
                //ciudadano que no es funcionario
                $resultado_query = Doctrine::getTable('Etapa')->findPendientesConPaginacion($usuario_sesion->id, Cuenta::cuentaSegunDominio(), $orderby, $direction, $per_page, $offset);
                $cantidad_etapas = $resultado_query->cantidad;
            }
        } else {
            //caso normal se hace la query del inbox
            $resultado_query = Doctrine::getTable('Etapa')->findPendientesConPaginacion($usuario_sesion->id, Cuenta::cuentaSegunDominio(), $orderby, $direction, $per_page, $offset);
            $cantidad_etapas = $resultado_query->cantidad;
        }



        $this->load->library('pagination');
        $this->pagination->initialize(array(
            'base_url' => site_url('etapas/inbox?orderby= ' . $orderby . '&direction=' . $direction),
            'total_rows' => $cantidad_etapas,
            'per_page' => $per_page
        ));

        $data['orderby'] = $orderby;
        $data['direction'] = $direction;

        $data['etapas'] = $resultado_query->etapas;
        $data['funcionario_actuando_como_ciudadano'] = $funcionario_actuando_como_ciudadano;
        $data['usuario_nombres'] = $usuario_sesion->nombres . ' ' . $usuario_sesion->apellido_paterno;
        if (!$funcionario_actuando_como_ciudadano) {
            $data['sidebar'] = 'inbox';
        } else {
            $data['sidebar'] = 'busqueda_ciudadano';
        }
        $data['content'] = 'etapas/inbox';
        $data['title'] = 'Bandeja de Entrada';
        $this->load->view('template', $data);
    }

    public function busqueda_filtros_pendientes() {
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

        $orderby = $this->input->get('orderby') && $this->input->get('orderby') != '' ? $this->input->get('orderby') : 'updated_at';
        $direction = $this->input->get('direction') && $this->input->get('direction') != '' ? $this->input->get('direction') == 'desc' ? 'desc' : 'asc' : 'desc';


        $paginado = Doctrine_Query::create()
                ->from('Parametro p')
                ->where('p.cuenta_id = ? AND p.clave = ?', array($cuenta_segun_dominio->id, 'resultados_por_pagina'))
                ->fetchOne();


        if ($paginado) {
            $per_page = $paginado->valor;
        } else {
            $per_page = 50;
        }

        $offset = $this->input->get('offset');

        $resultado_query = Doctrine::getTable('Etapa')->findPendientesFiltro($usuario_id, $cuenta_segun_dominio, $orderby, $direction, $busqueda_id_tramite, $busqueda_etapa, $busqueda_grupo, $busqueda_nombre, $busqueda_documento, $busqueda_modificacion_desde, $busqueda_modificacion_hasta, $per_page, $offset);

        $query = $this->input->get('termino');

        if ($query) {
            $etapas_pendientes = $resultado_query->etapas;
            $tramites_id = [];

            foreach ($etapas_pendientes as $etapa_pendiente) {
                array_push($tramites_id, $etapa_pendiente->tramite_id);
            }
            $etapas = 0;
            $cantidad_etapas = 0;
            if ($tramites_id && count($tramites_id) > 0) {
                $doctrine_query = Doctrine_Query::create()
                        ->from('Etapa e, e.DatosSeguimiento d, e.Tramite t')
                        ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')
                        ->orderBy('e.id desc')
                        ->groupBy('t.id');

                $this->load->library('sphinxclient');
                $this->sphinxclient->setServer($this->config->item('sphinx_host'), $this->config->item('sphinx_port'));

                $this->sphinxclient->setFilter('tramite_id', $tramites_id);
                //$this->sphinxclient->SetLimits ( $offset, $per_page );
                $result = $this->sphinxclient->query($query, 'etapas');

                if ($result['total'] > 0) {
                    $matches = array_keys($result['matches']);
                    $doctrine_query->whereIn('e.id', $matches);
                } else {
                    $doctrine_query->where('0');
                }

                $etapas = $doctrine_query->execute();
                if (count($etapas) < 1) {
                    $etapas = [];
                }
            }
        } else {
            $cantidad_etapas = $resultado_query->cantidad;
            $etapas = $resultado_query->etapas;
        }

        $this->load->library('pagination');

        $this->pagination->initialize(array(
            'base_url' => site_url('etapas/busqueda_filtros_pendientes?' .
                    'busqueda_id_tramite=' . $busqueda_id_tramite .
                    '&busqueda_etapa=' . $busqueda_etapa .
                    '&busqueda_grupo=' . $busqueda_grupo .
                    '&busqueda_nombre=' . $busqueda_nombre .
                    '&busqueda_documento=' . $busqueda_documento .
                    '&busqueda_modificacion_desde=' . $busqueda_modificacion_desde .
                    '&busqueda_modificacion_hasta=' . $busqueda_modificacion_hasta) .
            '&orderby=' . $orderby .
            '&direction=' . $direction,
            'total_rows' => $cantidad_etapas,
            'per_page' => $per_page
        ));


        $data['etapas'] = $etapas;

        $data['busqueda_id_tramite'] = $busqueda_id_tramite;
        $data['busqueda_etapa'] = $busqueda_etapa;
        $data['busqueda_grupo'] = $busqueda_grupo;
        $data['busqueda_nombre'] = $busqueda_nombre;
        $data['busqueda_documento'] = $busqueda_documento;
        $data['busqueda_modificacion_desde'] = $busqueda_modificacion_desde;
        $data['busqueda_modificacion_hasta'] = $busqueda_modificacion_hasta;


        $data['orderby'] = $orderby;
        $data['direction'] = $direction;

        $data['sidebar'] = 'inbox';
        $data['content'] = 'etapas/inbox';
        $data['title'] = 'Bandeja de Entrada';
        $this->load->view('template', $data);
    }

    public function busqueda_filtros_sinasignar() {
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

        if ($paginado) {
            $per_page = $paginado->valor;
        } else {
            $per_page = 50;
        }

        $offset = $this->input->get('offset');

        $resultado_query = Doctrine::getTable('Etapa')->findSinAsignarFiltro($usuario_id, $cuenta_segun_dominio, $orderby, $direction, $busqueda_id_tramite, $busqueda_etapa, $busqueda_grupo, $busqueda_nombre, $busqueda_documento, $busqueda_modificacion_desde, $busqueda_modificacion_hasta, $per_page, $offset, false);


        $query = $this->input->get('termino');

        if ($query) {
            $etapas_pendientes = $resultado_query;
            $tramites_id = [];

            foreach ($etapas_pendientes as $etapa_pendiente) {
                array_push($tramites_id, $etapa_pendiente->tramite_id);
            }
            $etapas = 0;
            $cantidad_etapas = 0;
            if ($tramites_id && count($tramites_id) > 0) {
                $doctrine_query = Doctrine_Query::create()
                        ->from('Etapa e, e.DatosSeguimiento d, e.Tramite t')
                        ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')
                        ->orderBy('e.id desc')
                        ->groupBy('t.id');

                $this->load->library('sphinxclient');
                $this->sphinxclient->setServer($this->config->item('sphinx_host'), $this->config->item('sphinx_port'));

                $this->sphinxclient->setFilter('tramite_id', $tramites_id);
                $result = $this->sphinxclient->query($query, 'etapas');

                if ($result['total'] > 0) {
                    $matches = array_keys($result['matches']);
                    $doctrine_query->whereIn('e.id', $matches);
                } else {
                    $doctrine_query->where('0');
                }

                $etapas = $doctrine_query->execute();
                if (count($etapas) < 1) {
                    $etapas = [];
                }
            }
        } else {
            $cantidad_etapas = Doctrine::getTable('Etapa')->findSinAsignarFiltro($usuario_id, $cuenta_segun_dominio, $orderby, $direction, $busqueda_id_tramite, $busqueda_etapa, $busqueda_grupo, $busqueda_nombre, $busqueda_documento, $busqueda_modificacion_desde, $busqueda_modificacion_hasta, $per_page, $offset, true);
            $etapas = $resultado_query;
        }

        $this->load->library('pagination');

        $this->pagination->initialize(array(
            'base_url' => site_url('etapas/busqueda_filtros_sinasignar?' .
                    'busqueda_id_tramite=' . $busqueda_id_tramite .
                    '&busqueda_etapa=' . $busqueda_etapa .
                    '&busqueda_grupo=' . $busqueda_grupo .
                    '&busqueda_nombre=' . $busqueda_nombre .
                    '&busqueda_documento=' . $busqueda_documento .
                    '&busqueda_modificacion_desde=' . $busqueda_modificacion_desde .
                    '&busqueda_modificacion_hasta=' . $busqueda_modificacion_hasta),
            'total_rows' => $cantidad_etapas,
            'per_page' => $per_page
        ));

        $data['busqueda_id_tramite'] = $busqueda_id_tramite;
        $data['busqueda_etapa'] = $busqueda_etapa;
        $data['busqueda_grupo'] = $busqueda_grupo;
        $data['busqueda_nombre'] = $busqueda_nombre;
        $data['busqueda_documento'] = $busqueda_documento;
        $data['busqueda_modificacion_desde'] = $busqueda_modificacion_desde;
        $data['busqueda_modificacion_hasta'] = $busqueda_modificacion_hasta;


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

        if (!UsuarioSesion::usuario()->registrado) {
            redirect('autenticacion/login');
        }

        $cuenta_segun_dominio = Cuenta::cuentaSegunDominio();

        $paginado = Doctrine_Query::create()
                ->from('Parametro p')
                ->where('p.cuenta_id = ? AND p.clave = ?', array($cuenta_segun_dominio->id, 'resultados_por_pagina'))
                ->fetchOne();

        if ($paginado) {
            $per_page = $paginado->valor;
        } else {
            $per_page = 50;
        }


        $offset = $this->input->get('offset');

        $orderby = 'updated_at';
        $direction = $this->input->get('direction') && $this->input->get('direction') != '' ? $this->input->get('direction') == 'desc' ? 'desc' : 'asc' : 'desc';

        $resultado_query = Doctrine::getTable('Etapa')->findSinAsignarConPaginacion(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio(), $orderby, $direction, $per_page, $offset);
        $cantidad_etapas = Doctrine::getTable('Etapa')->cantidadSinAsignar(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio());

        $this->load->library('pagination');
        $this->pagination->initialize(array(
            'base_url' => site_url('etapas/sinasignar?'),
            'total_rows' => $cantidad_etapas,
            'per_page' => $per_page
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

        //la variable usada para que el usuario no navegue directo
        $this->session->set_userdata('ejecutar_form', 'true');

        $usuario = Doctrine_Query::create()
                ->from('Usuario u')
                ->where('u.id = ?', $usuario_id)
                ->fetchOne();

        if (!$usuario->registrado) {
            UsuarioSesion::login_usuario_pago($usuario->usuario);
            redirect('/etapas/ejecutar/' . $etapa_id . "/" . $secuencia);
        } else {
            redirect('/autenticacion/login?redirect=' . site_url() . 'etapas/ejecutar/' . $etapa_id . "/" . $secuencia);
        }
    }

    public function ejecutar($etapa_id, $secuencia = 0) {
        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
        if (!$this->verificar_permisos_ejecucion_grep($etapa->Tarea->Proceso->id)) {
            $data['proceso'] = $etapa->Tarea->Proceso;
            $data['title'] = "Error";
            $data['content'] = 'etapas/permisos_ejecucion_grep';
            $this->load->view('template', $data);
            return;
        }

        //inicio - validacion de no ir directamente al paso
        if ($secuencia > 0) {
            $var_s = $this->session->userdata('ejecutar_form');
            if (!$var_s) {
                //verificamos que la secuencia no sea menor a la acual (un volver)
                $secuencia_actual = (int) $this->session->userdata('secuencia_actual');
                if ($secuencia_actual < $secuencia) {
                    $data['proceso'] = Doctrine_Query::create()
                            ->from('Proceso p')
                            ->where('p.id = ?', $etapa->Tarea->Proceso->id)
                            ->fetchOne();
                    $data['content'] = 'etapas/uso_no_permitido';
                    $this->load->view('template', $data);
                    return;
                }
            }
        }

        //en secuencia actual se almacena la secuencia en la cual se está
        $this->session->set_userdata('secuencia_actual', $secuencia);
        //en ejecutar form se almacena un booleano que indica si se puede ejecutar o no esta secuencia, usada para no ir directamente al paso
        $this->session->unset_userdata('ejecutar_form');
        //Fin - validacion de no ir directamente al paso
        //verifico si el usuario pertenece el grupo MesaDeEntrada y esta actuando como un ciudadano, si es asi entonces
        //como esta ejecutando un tramite se genera la variable de seguimiento funcionario_actuando_como_ciudadano que luego
        //se utiliza en seguimiento.
        if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
            $usuario_sesion = Doctrine_Query::create()
                    ->from('Usuario u')
                    ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                    ->fetchOne();
            $funcionario_actuando_como_ciudadano = true;
            $necesita_nivel_de_confianza = false;
        } else {
            $usuario_sesion = UsuarioSesion::usuario();
            $necesita_nivel_de_confianza = true;
            $funcionario_actuando_como_ciudadano = false;
        }

        $me = $this->input->post('_method');
        $aa = $_SERVER['REQUEST_METHOD'];


        $iframe = $this->input->get('iframe');

        if ($this->input->get('iframe')) {
            $template = 'template_iframe';
        } elseif ($this->input->get('iframe_nc')) {
            $template = 'template_iframe_sin_contexto';
        } else {
            $template = 'template';
        }


        if (!$etapa) {
            show_404();
        }

        //la etapa siempre tiene usuario id o el ficticio (no registrado) o el registrado
        //en caso de ser el ficticio tambien esta en la session con el mismo id.
        if ($etapa->usuario_id != $usuario_sesion->id) {
            if (!$usuario_sesion->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                //TODO modificado antes llamaba a login_saml
                redirect('autenticacion/login?redirect=' . current_url());
            }

            $data['error'] = 'Usuario no tiene permisos para ejecutar esta etapa.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view($template, $data);
            return;
        }

        //si la cuenta de la etapa no es la cuenta del dominio
        $cuenta = Cuenta::cuentaSegunDominio();
        if ($cuenta) {
            if ($etapa->Tarea->Proceso->cuenta_id != $cuenta->id) {
                $data['error'] = 'Esta etapa no pertenece a la cuenta actual';
                $data['content'] = 'etapas/error';
                $data['title'] = $data['error'];
                $this->load->view($template, $data);
                return;
            }
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

        $paso = $etapa->getPasoEjecutable($secuencia);
        $formulario = $paso->Formulario;

        $this->guardar_historial_ejecuciones($etapa_id, $secuencia, $paso->nombre);

        // -- Se genera dato de seguimieto en el caso de que el funcionario este actuando como ciudadano
        if ($funcionario_actuando_como_ciudadano) {
            $dato_funcionario = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('funcionario_actuando_como_ciudadano', $etapa->id);

            if (!$dato_funcionario) {
                $dato_funcionario = new DatoSeguimiento();
            }
            $dato_funcionario->nombre = 'funcionario_actuando_como_ciudadano';
            //el funcionario que está actuando en nombre del ciudadano
            $dato_funcionario->valor = (string) UsuarioSesion::usuario()->id;
            $dato_funcionario->etapa_id = $etapa->id;
            $dato_funcionario->save();
        }

        if (UsuarioSesion::usuario_actuando_como_empresa()) {
            $dato_empresa = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('usuario_actuando_como_empresa', $etapa->id);

            if (!$dato_empresa) {
                $dato_empresa = new DatoSeguimiento();
            }
            $dato_empresa->nombre = 'usuario_actuando_como_empresa';
            //el usuario que está actuando en nombre de la empresa
            $dato_empresa->valor = (string) UsuarioSesion::usuario_actuando_como_empresa();
            $dato_empresa->etapa_id = $etapa->id;
            $dato_empresa->save();
        }

        // -- Se genera el ID de transaccion correspondiente para trazabilidad.
        $id_transaccion = str_replace(" ", "_", strtoupper($formulario->Proceso->ProcesoTrazabilidad->organismo_id)) . ':' . str_replace(" ", "_", strtoupper($formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id)) . ':' . $etapa->tramite_id;

        // -- Genera variable con el ID de transaccion
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_transaccion_traza', $etapa->id);
        if (!$dato)
            $dato = new DatoSeguimiento();

        $dato->etapa_id = $etapa->id;
        $dato->nombre = 'id_transaccion_traza';
        $dato->valor = (string) $id_transaccion;
        $dato->save();

        //--- TRAZABILIDAD
        enviar_traza_inicio_tarea($etapa, $secuencia);

        // -- Genera variable con el ID de estado para traza (idEstadoTrazabilidad)
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('idEstadoTrazabilidad', $etapa->id);
        if (!$dato) {
            $dato = new DatoSeguimiento();
            $dato->etapa_id = $etapa->id;
            $dato->nombre = 'idEstadoTrazabilidad';
            $dato->valor = (string) $etapa->Tarea->trazabilidad_estado;
            $dato->save();
        }

        if ($necesita_nivel_de_confianza) {
            //si la tarea tiene acceso registrado se verifica nivel de confianza del usuario
            if ($etapa->Tarea->acceso_modo == 'registrados' && TIPO_DE_AUTENTICACION == 'CDA') {
                if ($etapa->Tarea->nivel_confianza) {
                    if (UsuarioSesion::getNivel_confianza() < $etapa->Tarea->nivel_confianza) {
                        $data['error'] = 'Usuario no tiene el nivel de confianza requerido para ejecutar esta etapa';
                        $data['content'] = 'etapas/error';
                        $data['title'] = $data['error'];
                        $this->load->view($template, $data);
                        return;
                    }
                }
            }
        }

        $qs = $this->input->server('QUERY_STRING');
        $paso = $etapa->getPasoEjecutable($secuencia);
        if (!$paso) {
            redirect('etapas/ejecutar_fin/' . $etapa->id . ($qs ? '?' . $qs : ''));
        } else if (($etapa->Tarea->final || !$etapa->Tarea->paso_confirmacion) && $paso->getReadonly() && end($etapa->getPasosEjecutables()) == $paso) { //No se requiere mas input //Cerrado automatico
            $etapa->iniciarPaso($paso, $secuencia);
            $etapa->finalizarPaso($paso, $secuencia);
            $etapa->avanzar();
            $this->generarUsuarioFinEtapa($etapa);

            imprimir_pasos_pdf($etapa->id);

            //--- TRAZABILIDAD
            enviar_traza_cierre_automatico($etapa, $secuencia);

            redirect('etapas/ver/' . $etapa->id . '/' . (count($etapa->getPasosEjecutables()) - 1));
        } else {
            $etapa->iniciarPaso($paso, $secuencia);


            $data['funcionario_actuando_como_ciudadano'] = $funcionario_actuando_como_ciudadano;
            $data['usuario_nombres'] = $usuario_sesion->nombres . ' ' . $usuario_sesion->apellido_paterno;

            $data['secuencia'] = $secuencia;
            $data['etapa'] = $etapa;
            $data['paso'] = $paso;
            $data['qs'] = $this->input->server('QUERY_STRING');

            if (!$funcionario_actuando_como_ciudadano) {
                $data['sidebar'] = $usuario_sesion->registrado ? 'inbox' : 'disponibles';
            } else {
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
        if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
            $usuario_sesion = Doctrine_Query::create()
                    ->from('Usuario u')
                    ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                    ->fetchOne();

            $funcionario_actuando_como_ciudadano = true;
        } else {
            $usuario_sesion = UsuarioSesion::usuario();
            $funcionario_actuando_como_ciudadano = false;
        }

        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
        $this->eliminarErrorValidacion($etapa->id);

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

        //se tiene que verificar que no se está ejecutando desde la URL

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
            if ($this->input->post('no_advance') == 1) {
                $validado = true;
            } else {
                if ($this->form_validation->run() == TRUE) {
                    $validado = true;
                } else {
                    $validado = false;
                }
            }


            if (!$validar_formulario || $validado) {
                //se indica que se ejecutó el formulario para evitar la navegación directa desde la URL
                $this->session->set_userdata('ejecutar_form', 'true');

                if ($this->input->post('documento_tramite_inicial__e' . $etapa->id)) {
                    $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('documento_tramite_inicial__e' . $etapa->tramite_id, $etapa->id);
                    if (!$dato)
                        $dato = new DatoSeguimiento();

                    $dato->nombre = 'documento_tramite_inicial__e' . $etapa->tramite_id;
                    $dato->valor = $this->input->post('documento_tramite_inicial__e' . $etapa->id);
                    $dato->etapa_id = $etapa->id;
                    $dato->save();
                }

                if ($this->input->post('email_tramite_inicial__e' . $etapa->id)) {
                    $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('email_tramite_inicial__e' . $etapa->id, $etapa->id);
                    if (!$dato)
                        $dato = new DatoSeguimiento();

                    $dato->nombre = 'email_tramite_inicial__e' . $etapa->id;
                    $dato->valor = $this->input->post('email_tramite_inicial__e' . $etapa->id);
                    $dato->etapa_id = $etapa->id;
                    $dato->save();
                }

                //Almacenamos los campos
                foreach ($formulario->Campos as $c) {

                    if ($c->tipo == 'pagos' && $c->pago_online == 0 && UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
                        $pago = Doctrine_Query::create()
                                ->from('Pago p')
                                ->where('p.id_etapa = ?', $etapa->id)
                                ->andWhere('p.estado = ?', 'pago_func')
                                ->fetchOne();

                        if ($this->input->post('check_pago_online') == 1 && !$pago) {
                            $usuario = "Invitado";
                            $api = $this->session->userdata('api_exc');
                            $cron = $this->session->userdata('cron_exc');
                            if ($api) {
                                $usuario = "Sistema - API";
                            } elseif ($cron) {
                                $usuario = "Sistema - CRON";
                            } else {
                                $usuario_login = UsuarioSesion::usuario();
                                if ($usuario_login->email) {
                                    $usuario = $usuario_login->id . ' - ' . $usuario_login->usuario;
                                }
                            }
                            $pago = new Pago();
                            $pago->id_tramite = 0;
                            $pago->id_tramite_interno = $etapa->tramite_id;
                            $pago->id_etapa = $etapa->id;
                            $pago->id_solicitud = '';
                            $pago->estado = 'pago_func';
                            $pago->fecha_actualizacion = date('d/m/Y H:i:s');
                            $pago->pasarela = '';
                            $pago->usuario = $usuario;
                            $pago->save();
                        }

                        if ($this->input->post('check_cancelar_pago') == 1 && $pago) {//este caso es cuando cancelan el pago
                            $pago->delete();
                        }
                    }

                    //Almacenamos los campos que no sean readonly y que esten disponibles (que su campo dependiente se cumpla)
                    if ($c->isEditableWithCurrentPOST()) {
                        if (($c->tipo != 'error') && ($c->tipo != 'dialogo') && ($c->tipo != 'agenda_sae') && ($c->tipo != 'domicilio_ica')) {

                            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($c->nombre, $etapa->id);
                            if (!$dato)
                                $dato = new DatoSeguimiento();

                            $dato->nombre = $c->nombre;
                            $dato->valor = $this->input->post($c->nombre);
                            $dato->etapa_id = $etapa->id;
                            $dato->save();

                            /* Se valida si el tipo de campo es file, busca según el nombre del input y agrega el nuevo registro del nombre
                              original del archivo */
                            if ($c->tipo == 'file') {
                                $dato_file_origen = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($c->nombre . '__origen', $etapa->id);
                                if (!$dato_file_origen)
                                    $dato_file_origen = new DatoSeguimiento();

                                $file = Doctrine_Query::create()
                                        ->from('file f')
                                        ->where('f.filename = ?', $this->input->post($c->nombre))
                                        ->fetchOne();

                                $dato_file_origen->nombre = $c->nombre . '__origen';
                                $dato_file_origen->valor = isset($file->file_origen) ? $file->file_origen : "";
                                $dato_file_origen->etapa_id = $etapa->id;
                                $dato_file_origen->save();
                            }

                            if (($c->tipo == 'radio') || ($c->tipo == 'select')) {
                                $array_datos = $c->datos;
                                $array_flat = array();
                                $count = 0;
                                if (is_array($array_datos)) {
                                    foreach ($array_datos as $array3) {
                                        $array_flat[$count] = [$array3->etiqueta => $array3->valor];

                                        $count = $count + 1;
                                    }
                                }
                                $dato_encontrado = false;
                                for ($i = 0; $i < count($array_flat); $i++) {
                                    $dato_buscado = array_search($this->input->post($c->nombre), $array_flat[$i]);
                                    if ($dato_buscado) {
                                        $dato_encontrado = $dato_buscado;
                                    }
                                }

                                if ($dato_encontrado) {
                                    $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($c->nombre . '__etiqueta', $etapa->id);
                                    if ($dato)
                                        $dato->delete();

                                    $dato = new DatoSeguimiento();
                                    $dato->nombre = $c->nombre . '__etiqueta';
                                    $dato->valor = $dato_encontrado;
                                    $dato->etapa_id = $etapa->id;
                                    $dato->save();
                                }
                            }

                            if ($c->tipo == 'checkbox') {
                                $array_datos = $c->datos;
                                $array_flat = array();
                                $buscar_array = $this->input->post($c->nombre);

                                $dato_encontrado = [];
                                if (is_array($buscar_array)) {
                                    foreach ($buscar_array as $buscar) {
                                        $count = 0;

                                        foreach ($array_datos as $array3) {
                                            $array_flat[$count] = [$array3->etiqueta => $array3->valor];

                                            $count = $count + 1;
                                        }

                                        for ($i = 0; $i < count($array_flat); $i++) {
                                            $dato_buscado = array_search($buscar, $array_flat[$i]);
                                            if ($dato_buscado) {
                                                array_push($dato_encontrado, $dato_buscado);
                                            }
                                        }
                                    }
                                }

                                if ($dato_encontrado) {
                                    $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($c->nombre . '__etiqueta', $etapa->id);
                                    if ($dato)
                                        $dato->delete();

                                    $dato = new DatoSeguimiento();
                                    $dato->nombre = $c->nombre . '__etiqueta';
                                    $dato->valor = json_encode($dato_encontrado);
                                    $dato->etapa_id = $etapa->id;
                                    $dato->save();
                                }
                            }

                            if ($c->variable_obn != "" && $c->tipo != "tabla_datos" && $c->tipo != "tabla-responsive") {
                                $nombre_obn = explode(".", $c->variable_obn);
                                $nombre_variable = trim(substr($nombre_obn[0], 1));
                                $nombre_atributo = isset($nombre_obn[1]) ? trim($nombre_obn[1]) : null;
                                $dato_obn = Doctrine::getTable('ObnDatosSeguimiento')->findByNombreHastaEtapa($nombre_variable, $etapa->id);
                                if ($dato_obn) {
                                    $obn = json_decode($dato_obn->valor);
                                    if (isset($obn->$nombre_atributo)) {
                                        $obn->$nombre_atributo = $this->input->post($c->nombre);
                                    }
                                    $dato_obn->valor = json_encode($obn);
                                    $dato_obn->save();
                                }
                            } else if ($c->variable_obn != "" && $c->tipo == "tabla_datos") {
                                $nombre_obn = explode(".", $c->variable_obn);
                                $nombre_variable = trim(substr($nombre_obn[0], 1));
                                $nombre_atributo = isset($nombre_obn[1]) ? trim($nombre_obn[1]) : null;
                                if ($nombre_atributo) {
                                    $dato_obn = Doctrine::getTable('ObnDatosSeguimiento')->findByNombreHastaEtapa($nombre_variable, $etapa->id);
                                    if ($dato_obn) {
                                        $obn = json_decode($dato_obn->valor);
                                        if (isset($obn->$nombre_atributo)) {
                                            $valor_actual = $obn->$nombre_atributo;
                                            if (isset($valor_actual->parametros)) {
                                                $nuevos_param = json_decode(htmlspecialchars_decode($this->input->post($c->nombre . '_idobn')));
                                                $valor_actual->parametros = $nuevos_param;
                                            } else if (isset($valor_actual->OBN)) {
                                                $nuevos_param = json_decode(htmlspecialchars_decode($this->input->post($c->nombre . '_idobn')));
                                                if (isset($nuevos_param[0])) {
                                                    $valor_actual = json_decode(obtenerOBN($nuevos_param[0], $valor_actual->OBN, 1));
                                                }
                                            }
                                            $obn->$nombre_atributo = $valor_actual;
                                        }
                                        $dato_obn->valor = json_encode($obn);
                                        $dato_obn->save();
                                    }
                                }
                            } else if ($c->variable_obn != "" && $c->tipo == "tabla-responsive") {

                                $nombre_obn = explode(".", $c->variable_obn);
                                $nombre_variable = trim(substr($nombre_obn[0], 1));
                                $nombre_atributo = isset($nombre_obn[1]) ? trim($nombre_obn[1]) : null;

                                $dato_obn = Doctrine::getTable('ObnDatosSeguimiento')->findByNombreHastaEtapa($nombre_variable, $etapa->id);
                                if ($dato_obn) {
                                    $obn = json_decode($dato_obn->valor);
                                    if (isset($obn->$nombre_atributo)) {
                                        $obn->$nombre_atributo = $this->input->post($c->nombre);
                                    }

                                    $dato_obn->valor = json_encode($obn);
                                    $dato_obn->save();
                                }
                            }
                        }
                    }
                }
                $etapa->save();

                // Si se requiere guardado parcial.
                if ($this->input->post('no_advance') == 1) {
                    if ($this->input->post('require_accion') == 1) {
                        $require_campo = Doctrine::getTable('Campo')->find($this->input->post('require_accion_campo_id'));
                        $ejecutar_accion = Doctrine::getTable('Accion')->find($require_campo->requiere_accion_id);
                        $ejecutar_accion->ejecutar($etapa);
                        //se debe verificar si la accion generó el ws_errror y en ese caso retornar algo distinto para que el js lo procese y despliegue mensaje.
                        $dato_error = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('ws_error', $etapa->id);
                        if ($dato_error) {
                            $respuesta->error = $dato_error->valor;
                        } else {
                            //se verifica si la tabla tiene seteada la variable de error y la invocacion la generó
                            if ($require_campo->requiere_accion_var_error) {
                                $dato_error = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $require_campo->requiere_accion_var_error), $etapa->id);
                                if ($dato_error) {
                                    $respuesta->error = $dato_error->valor;
                                }
                            }
                        }
                        //como se está ejecutando la accion desde una botn en un componente la variable @@error y ws_error se tiene que limpiar
                        //porque esta variable se utiliza en ejecutar_fin para validar si se deja finalizar la tarea o no
                        $error_servicio = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("error", $etapa->id);
                        if ($error_servicio) {
                            $error_servicio->delete();
                        }
                        $error_servicio_ws = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("ws_error", $etapa->id);
                        if ($error_servicio_ws) {
                            $error_servicio_ws->delete();
                        }
                    }

                    $respuesta->validacion = TRUE;
                    if ($funcionario_actuando_como_ciudadano) {
                        $respuesta->redirect = site_url('/etapas/inbox?funcionario_ciudadano=1');
                    } else {
                        $respuesta->redirect = site_url('/etapas/inbox');
                    }
                } else {
                    //--- TRAZABILIDAD
                    enviar_traza_linea_paso_con_cierre_automatico($etapa, $secuencia);

                    $etapa->finalizarPaso($paso, $secuencia);

                    // -- Si encuentra variables de errores avisa que se ha registrado un error de parte de una acción.
                    $errors = false;
                    foreach ($formulario->Campos as $c) {
                        if (($c->tipo == 'error') || ($c->tipo == 'dialogo')) {
                            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $c->valor_default), $etapa->id);

                            if ($dato) {
                                if ($dato->valor != "") {
                                    $errors = true;
                                }
                            }
                        } elseif ($c->tipo == 'documento') {
                            $nombre_documento = $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($c->nombre, $etapa->id);
                            $file = Doctrine::getTable('File')->findOneByTipoAndFilename('documento', $nombre_documento->valor);

                            //se elmina la validacion de firma obligatoria, se procesa nuevamente
                            $firmado = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($c->nombre . '__firmado', $etapa->id);
                            $debe_firmar = $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($c->nombre . '__error', $etapa->id);
                            if ($debe_firmar)
                                $debe_firmar->delete();

                            $isVisibleCampoFirma = $c->documentoIsEditableWithCurrentPOST();
                            if ($isVisibleCampoFirma && isset($c->extra->firmar) && isset($c->extra->requerido) && !$this->session->userdata('id_usuario_ciudadano')) {
                                //es visible y no es un funcionario actuando como mesa de entrada no esta seteado el id_usuario_ciudadano en la sesion, se valida que el documento este firmado.
                                if (!$firmado) {
                                    $errors = true;
                                    $debe_firmar = $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($c->nombre . '__error', $etapa->id);
                                    if ($debe_firmar)
                                        $debe_firmar->delete();

                                    $dato = new DatoSeguimiento();
                                    $dato->nombre = $c->nombre . '__error';
                                    $dato->valor = ERROR_FIRMA_REQUERIDA;
                                    $dato->etapa_id = $etapa->id;
                                    $dato->save();
                                }
                            }

                            if ($isVisibleCampoFirma && isset($c->extra->firmar) && isset($c->extra->requerido) && $c->firma_electronica == 0 && UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
                                //valida que se firmo el doc para el caso de funcionario actuando como ciudadano
                                if (!$this->input->post('check_firma_electronica') && !$firmado) {
                                    $errors = true;

                                    $debe_firmar = $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($c->nombre . '__error', $etapa->id);
                                    if ($debe_firmar)
                                        $debe_firmar->delete();

                                    $dato = new DatoSeguimiento();
                                    $dato->nombre = $c->nombre . '__error';
                                    $dato->valor = ERROR_FIRMA_REQUERIDA;
                                    $dato->etapa_id = $etapa->id;
                                    $dato->save();
                                }
                            }

                            if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
                                if ($this->input->post('check_firma_electronica') == 1 && !$firmado) {

                                    $firmado = new DatoSeguimiento();
                                    $firmado->nombre = $c->nombre . '__firmado';
                                    $firmado->valor = "1";
                                    $firmado->etapa_id = $etapa->id;
                                    $firmado->save();

                                    $file->firmado = 1;
                                    $file->save();
                                }

                                if ($this->input->post('check_cancelar_firma') == 1 && $firmado) {
                                    //este caso es cuando cancelan la firma
                                    $firmado->delete();
                                    $file->firmado = 0;
                                    $file->save();
                                }
                            }
                        }
                    }

                    $respuesta->validacion = TRUE;

                    $qs = $this->input->server('QUERY_STRING');
                    $prox_paso = $etapa->getPasoEjecutable($secuencia + 1);

                    // Si hay registro de error de parte de una acción invocada vuelve a mostrar la secuencia actual, de lo contrario avanza.
                    if ($errors) {
                        $respuesta->redirect = site_url('etapas/ejecutar/' . $etapa_id . '/' . ($secuencia)) . ($qs ? '?' . $qs : '');
                    } else {
                        if (!$prox_paso) {
                            $respuesta->redirect = site_url('etapas/ejecutar_fin/' . $etapa_id) . ($qs ? '?' . $qs : '');
                        } else if ($etapa->Tarea->final && $prox_paso->getReadonly() && end($etapa->getPasosEjecutables()) == $prox_paso) { //Cerrado automatico
                            $etapa->iniciarPaso($prox_paso, $secuencia);
                            $etapa->finalizarPaso($prox_paso, $secuencia);
                            $etapa->avanzar();
                            $this->generarUsuarioFinEtapa($etapa);

                            imprimir_pasos_pdf($etapa->id);

                            $respuesta->redirect = site_url('etapas/ver/' . $etapa->id . '/' . (count($etapa->getPasosEjecutables()) - 1));
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

            //se indica que se ejecutó el formulario para evitar la navegación directa desde la URL
            $this->session->set_userdata('ejecutar_form', 'true');

            $respuesta->validacion = TRUE;

            //--- TRAZABILIDAD
            enviar_traza_linea_paso_con_cierre_automatico($etapa, $secuencia);

            $qs = $this->input->server('QUERY_STRING');
            $prox_paso = $etapa->getPasoEjecutable($secuencia + 1);

            if (!$prox_paso) {
                $respuesta->redirect = site_url('etapas/ejecutar_fin/' . $etapa_id) . ($qs ? '?' . $qs : '');
            } else if ($etapa->Tarea->final && $prox_paso->getReadonly() && end($etapa->getPasosEjecutables()) == $prox_paso) { //Cerrado automatico
                $etapa->iniciarPaso($prox_paso, $secuencia);
                $etapa->finalizarPaso($prox_paso, $secuencia);
                $etapa->avanzar();
                $this->generarUsuarioFinEtapa($etapa);

                imprimir_pasos_pdf($etapa->id);

                $respuesta->redirect = site_url('etapas/ver/' . $etapa->id . '/' . (count($etapa->getPasosEjecutables()) - 1));
            } else {
                $respuesta->redirect = site_url('etapas/ejecutar/' . $etapa_id . '/' . ($secuencia + 1)) . ($qs ? '?' . $qs : '');
            }
        }
        $dato_seguimiento_error = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("validacion_error", $etapa->id);
        if ($dato_seguimiento_error) {
            $dato_seguimiento_error_campo = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("validacion_error_campo", $etapa->id);
            $array_campos = $dato_seguimiento_error_campo->valor;
            if (is_array($array_campos)) {
                if (!isset($respuesta->errores)) {
                    $respuesta->errores = "";
                }
                foreach ($array_campos as $value) {
                    foreach ($value as $key => $value) {
                        //echo $value;
                        $dato_campo = Doctrine::getTable('Campo')->find($key);
                        if ($dato_campo) {
                            $respuesta->errores .= "<div class='alert alert-error'>Error en el campo <strong>" . $dato_campo->nombre . "@" . $dato_campo->etiqueta . "</strong> : " . $value . " </div>";
                        } else {
                            $respuesta->errores .= "<div class='alert alert-error'>Error: <strong>" . $value . " </strong> </div>";
                        }
                    }
                }
            }
            $respuesta->validacion = FALSE;
        }
        echo json_encode($respuesta);
    }

    private function eliminarErrorValidacion($etapaID) {
        $conn = Doctrine_Manager::connection();
        $stmt = $conn->prepare('delete FROM dato_seguimiento WHERE (etapa_id = ' . $etapaID . ' and nombre like \'validacion_error%\');');
        $stmt->execute();
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
        //inicio validacion URL directa
        $var_s = $this->session->userdata('ejecutar_form');
        if (!$var_s) {
            //puede ser que sea una etapa sin pasos
            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            if (count($etapa->getPasosEjecutables()) > 0) {
                $data['content'] = 'etapas/uso_no_permitido';
                $data['title'] = "Error";
                $this->load->view('template', $data);
                return;
            }
        }
        $this->session->unset_userdata('ejecutar_form');
        //fin validacion URL directa
        //verifico si el usuario pertenece el grupo MesaDeEntrada y hay actuando como un ciudadano
        if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
            $usuario_sesion = Doctrine_Query::create()
                    ->from('Usuario u')
                    ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                    ->fetchOne();

            $funcionario_actuando_como_ciudadano = true;
        } else {
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
        $link_pdf_ = imprimir_pasos_pdf($etapa->id);
        if (!is_null($link_pdf_)) {
            $hay_pasos_generar_pdf = true;
        }


        $data['link_pdf'] = $link_pdf_;
        $data['hay_pasos_generar_pdf'] = $hay_pasos_generar_pdf;

        $data['etapa'] = $etapa;
        $data['tareas_proximas'] = $etapa->getTareasProximas();
        $data['qs'] = $this->input->server('QUERY_STRING');

        if (!$funcionario_actuando_como_ciudadano) {
            $data['sidebar'] = $usuario_sesion->registrado ? 'inbox' : 'disponibles';
        } else {
            $data['sidebar'] = 'busqueda_ciudadano';
        }

        $data['funcionario_actuando_como_ciudadano'] = $funcionario_actuando_como_ciudadano;
        $data['usuario_nombres'] = $usuario_sesion->nombres . ' ' . $usuario_sesion->apellido_paterno;
        $data['content'] = 'etapas/ejecutar_fin';

        $data['title'] = $etapa->Tarea->Proceso->nombre;
        $template = $this->input->get('iframe') ? 'template_iframe' : 'template';

        $this->load->view($template, $data);
    }

    public function ejecutar_fin_form($etapa_id) {
        //verifico si el usuario pertenece el grupo MesaDeEntrada y esta actuando como un ciudadano
        if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
            $usuario_sesion = Doctrine_Query::create()
                    ->from('Usuario u')
                    ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                    ->fetchOne();

            $funcionario_actuando_como_ciudadano = true;
        } else {
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

        // -- Si encuentra variables de errores avisa que se ha registrado un error de parte de una acción.
        $errors = false;
        $errors_msg = '';
        $error_servicio_com = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("ws_error", $etapa->id);
        //si se genero la variable @@error, esto es por los ws que no retornan fault entonces si la variable existe se considera como el ws_error
        //el moldeador la tuvo que definir segun la logica del servicio.
        $error_servicio = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("error", $etapa->id);

        if ($error_servicio_com) {
            if ($error_servicio_com->valor != "") {
                $errors = true;
                $errors_msg = $error_servicio_com->valor;
            }
        }
        if ($error_servicio) {
            if ($error_servicio->valor != "") {
                $errors = true;
                $errors_msg = $error_servicio->valor;
            }
        }

        // Si hay registro de error de parte de una acción invocada vuelve a mostrar la secuencia actual, de lo contrario avanza.
        if ($errors) {
            $respuesta->redirect = site_url('etapas/ejecutar_fin/' . $etapa_id);
            $respuesta->error_paso_final = $errors_msg;
        } else {
            //--- TRAZABILIDAD
            enviar_traza_final_tarea($etapa);

            $etapa->avanzar($this->input->post('usuarios_a_asignar'));

            enviar_traza_final_proceso($etapa);
            $this->generarUsuarioFinEtapa($etapa);

            $respuesta = new stdClass();
            $respuesta->validacion = TRUE;

            if ($funcionario_actuando_como_ciudadano) {
                $respuesta->redirect = site_url('etapas/inbox?funcionario_ciudadano=1');
            } else if (!$funcionario_actuando_como_ciudadano && $this->input->get('iframe')) {
                $respuesta->redirect = site_url('etapas/ejecutar_exito');
            } else {
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

        $funcionario_actuando_como_ciudadano = false;
        if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
            $funcionario_actuando_como_ciudadano = true;
        }

        $usuario_sesion = UsuarioSesion::usuario();

        if ($funcionario_actuando_como_ciudadano) {
            $usuario_sesion = Doctrine_Query::create()
                    ->from('Usuario u')
                    ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                    ->fetchOne();
        }
        if ($usuario_sesion->id != $etapa->usuario_id) {
            $data['error'] = 'No tiene permisos para hacer seguimiento a este tramite.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
            return;
        }

        $paso = $etapa->getPasoEjecutable($secuencia);

        /* if (($etapa->Tarea->final || !$etapa->Tarea->paso_confirmacion) && $paso->getReadonly() && end($etapa->getPasosEjecutables()) == $paso && !($secuencia + 1 < count($etapa->getPasosEjecutables()))){
          $data['link_pdf'] = imprimir_pasos_pdf($etapa->id);
          } */

        // Paso actual (se utiliza diferente de 'secuencia' ya que se incrementará en la vista).
        $data['step_position'] = $secuencia;

        $data['etapa'] = $etapa;
        $data['paso'] = $paso;
        $data['secuencia'] = $secuencia;

        $data['sidebar'] = 'participados';
        $data['title'] = 'Historial - ' . $etapa->Tarea->nombre;
        $data['content'] = 'etapas/ver';
        $data['funcionario_actuando_como_ciudadano'] = $funcionario_actuando_como_ciudadano;
        $data['usuario_nombres'] = $usuario_sesion->nombres . ' ' . $usuario_sesion->apellido_paterno;
        $this->load->view('template', $data);
    }

    public function ver_firma($etapa_id, $secuencia = 0) {
        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

        if (!$etapa) {
            $data['error'] = 'El tr&aacute;mite no existe..';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $data['sidebar'] = 'firma_por_lotes';
            $this->load->view('template', $data);
            return;
        }
        $funcionario_actuando_como_ciudadano = false;
        if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
            $funcionario_actuando_como_ciudadano = true;
        }

        $usuario_sesion = UsuarioSesion::usuario();

        if ($funcionario_actuando_como_ciudadano) {
            $usuario_sesion = Doctrine_Query::create()
                    ->from('Usuario u')
                    ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                    ->fetchOne();
        }

        $etapas_firmar = Doctrine::getTable('Etapa')->findEtapasFirmaPorLote(UsuarioSesion::usuario()->id, $etapa->Tramite->Proceso->id, true);

        $esta = false;
        foreach ($etapas_firmar as $key => $value) {
            if ($etapa_id == $value['id'])
                $esta = true;
        }
        if (!UsuarioSesion::usuarioFirmaPorLotes() || !$esta) {
            $data['error'] = 'No tiene permisos para hacer seguimiento a este tramite.';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $data['sidebar'] = 'firma_por_lotes';
            $this->load->view('template', $data);
            return;
        }

        $paso = $etapa->getPasoEjecutable($secuencia);

        $data['step_position'] = $secuencia;

        $data['etapa'] = $etapa;
        $data['paso'] = $paso;
        $data['secuencia'] = $secuencia;

        $data['sidebar'] = 'firma_por_lotes';
        $data['title'] = 'Historial - ' . $etapa->Tarea->nombre;
        $data['content'] = 'etapas/ver';
        $data['funcionario_actuando_como_ciudadano'] = $funcionario_actuando_como_ciudadano;
        $data['usuario_nombres'] = $usuario_sesion->nombres . ' ' . $usuario_sesion->apellido_paterno;
        $this->load->view('template', $data);
    }

    public function busqueda_ciudadano() {
        if (!UsuarioSesion::usuario()->registrado && !UsuarioSesion::usuarioMesaDeEntrada()) {
            redirect(site_url());
        }

        $data['sidebar'] = 'busqueda_ciudadano';
        $data['content'] = 'etapas/busqueda_ciudadano';
        $data['title'] = 'Búsqueda Ciudadano';
        $this->load->view('template', $data);
    }

    public function firma_por_lotes() {
        if (!UsuarioSesion::usuario()->registrado && !UsuarioSesion::usuarioFirmaPorLotes()) {
            redirect(site_url());
        }

        $orderby = 'nombre';
        $direction = $this->input->get('direction') && $this->input->get('direction') != '' ? $this->input->get('direction') == 'desc' ? 'desc' : 'asc' : 'desc';

        $data['procesos'] = Doctrine_Query::create()
                ->from('Proceso p')
                ->where('p.cuenta_id = ?', UsuarioSesion::usuario()->cuenta_id)
                ->andWhere('p.nombre != ?', 'BLOQUE')
                ->andWhere('p.activo=1 AND p.estado!="draft"')
                ->orderBy($orderby . ' ' . $direction)
                ->execute();
        $data['orderby'] = $orderby;
        $data['direction'] = $direction;
        $data['sidebar'] = 'firma_por_lotes';
        $data['content'] = 'etapas/firma_por_lotes';
        $data['title'] = 'Firma por lotes';
        $this->load->view('template', $data);
    }

    public function busqueda_ciudadano_form() {
        if (!UsuarioSesion::usuario()->registrado && !UsuarioSesion::usuarioMesaDeEntrada()) {
            redirect(site_url());
        }
        $respuesta = new stdClass();

        if ($this->input->post('tipo_documento') == 'ci' && $this->input->post('pais') == 'uy') {
            $this->form_validation->set_rules('documento', 'Documento', 'required|ci');
        } else {
            $this->form_validation->set_rules('documento', 'Documento', 'required');
        }

        $this->form_validation->set_rules('tipo_documento', 'Tipo de documento', 'required');
        $this->form_validation->set_rules('pais', 'País', 'required');

        if ($this->form_validation->run() == TRUE) {
            $respuesta->validacion = TRUE;

            $usuario_busqueda = $this->input->post('pais') . '-' . $this->input->post('tipo_documento') . '-' . $this->input->post('documento');

            $usuario_econtrado = Doctrine::getTable('Usuario')->findUsuarioEnCuentaOrCiudadano($usuario_busqueda, UsuarioSesion::usuario()->cuenta_id);

            $doc_usuario_logueado = explode("-", UsuarioSesion::usuario()->usuario)[2];
            $doc_usuario_ecnotrado = $this->input->post('documento');

            if ($usuario_econtrado && trim($doc_usuario_logueado) != trim($doc_usuario_ecnotrado)) {
                $respuesta->redirect = site_url('etapas/inbox?funcionario_ciudadano=1');
                //setea en la sesion la variable con el ciudadano para el cual se esta realizando el tramite.
                $this->session->set_userdata('id_usuario_ciudadano', $usuario_econtrado->id);
                if ($this->input->post('omnicanalidad_oid')) {
                    $this->session->set_userdata('id_usuario_ciudadano_oid', $usuario_econtrado->usuario);
                    $this->session->set_userdata('id_usuario_ciudadano_oid_enviar', 1);
                }
            } else if ($usuario_econtrado && trim($doc_usuario_logueado) == trim($doc_usuario_ecnotrado)) {
                $respuesta->validacion = FALSE;
                $respuesta->errores = 'El docmuento ingresado es el mismo que el del usuario logueado.';
            } else {
                $respuesta->validacion = TRUE;
                $respuesta->redirect = site_url('etapas/registro_ciudadano?doc=' . $this->input->post('documento') . '&tipo=' . $this->input->post('tipo_documento') . '&pais=' . $this->input->post('pais'));
            }
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function registro_ciudadano() {
        if (!UsuarioSesion::usuario()->registrado && !UsuarioSesion::usuarioMesaDeEntrada()) {
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
        if (!UsuarioSesion::usuario()->registrado && !UsuarioSesion::usuarioMesaDeEntrada()) {
            redirect(site_url());
        }

        $respuesta = new stdClass();

        $this->form_validation->set_rules('nombres', 'Nombres', 'required');
        $this->form_validation->set_rules('apellido_paterno', 'Apellido Paterno', 'required');
        $this->form_validation->set_rules('apellido_materno', 'Apellido Materno', 'required');
        //$this->form_validation->set_rules('email','Correo electrónico','required');
        if ($this->input->post('tipo_documento') == 'ci' && $this->input->post('pais') == 'uy') {
            $this->form_validation->set_rules('documento', 'Documento', 'required|ci');
        } else {
            $this->form_validation->set_rules('documento', 'Documento', 'required');
        }

        $this->form_validation->set_rules('tipo_documento', 'Tipo de documento', 'required');
        $this->form_validation->set_rules('pais', 'País', 'required');

        if ($this->form_validation->run() == TRUE) {

            $usuario_busqueda = $this->input->post('pais') . '-' . $this->input->post('tipo_documento') . '-' . $this->input->post('documento');

            $usuario_econtrado = Doctrine::getTable('Usuario')->findUsuarioEnCuentaOrCiudadano($usuario_busqueda, UsuarioSesion::usuario()->cuenta_id);
            /* $usuario_econtrado = Doctrine_Query::create()
              ->from('Usuario u')
              ->where('u.usuario = ?', $usuario_busqueda)
              ->fetchOne(); */

            if (!$usuario_econtrado) {
                $usuario = new Usuario();
                $usuario->usuario = $usuario_busqueda;
                $usuario->nombres = $this->input->post('nombres');
                $usuario->apellido_paterno = $this->input->post('apellido_paterno');
                $usuario->apellido_materno = $this->input->post('apellido_materno');
                $usuario->email = $this->input->post('email');
                $usuario->registrado = 1;
                $usuario->save();
                auditar('Usuario', "insert", $usuario->id, UsuarioSesion::usuario()->usuario);

                $this->session->unset_userdata('id_usuario_ciudadano');
                //setea en la sesion el ciudadano para el cual se esta realizando el tramite.
                $this->session->set_userdata('id_usuario_ciudadano', $usuario->id);
                $this->session->set_userdata('id_usuario_ciudadano', $usuario_econtrado->id);
                if ($this->input->post('omnicanalidad_oid')) {
                    $this->session->set_userdata('id_usuario_ciudadano_oid', $usuario_econtrado->usuario);
                    $this->session->set_userdata('id_usuario_ciudadano_oid_enviar', 1);
                }
                $respuesta->validacion = TRUE;
                $respuesta->redirect = site_url('etapas/inbox?funcionario_ciudadano=1');
            } else {
                $respuesta->validacion = FALSE;
                $respuesta->errores = 'Ya existe un usuario registrado con este documento.';
            }
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    private function generarUsuarioFinEtapa($etapa) {
        $usuario_fin_etapa_generado = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("usuario_fin_etapa_generado", $etapa->id);
        if ($usuario_fin_etapa_generado) {
            $usuario_fin_etapa_generado->delete();
        }
        //siempre el usuario logueado es el que se registra como que cierra la tarea.
        $usuario_sesion = UsuarioSesion::usuario()->id;


        $dato = new DatoSeguimiento();
        $dato->nombre = 'usuario_fin_etapa_generado';
        $dato->valor = $usuario_sesion;
        $dato->etapa_id = $etapa->id;
        $dato->save();
    }

    public function imprimir_pasos_tarea_sin_asignar($etapa_id) {
        if ($etapa_id) {
            $documento = new Documento();
            $documento->tipo = 'pasos';
            $documento->tamano = 'letter';
            $array_etapas_para_pdf = array();

            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

            if (!$etapa) {
                redirect(site_url());
            }

            $generar_file = false;

            $array_pasos_ejecutables = $etapa->getPasosEjecutables();
            $array_formularios_ejecutables = array();
            $hay_pasos_generar_pdf = false;

            foreach ($array_pasos_ejecutables as $paso_ejecutable) {

                $hay_pasos_generar_pdf = true;

                foreach ($etapa->Tramite->Proceso->Formularios as $formulario) {
                    //si el formulario tiene campos
                    if (count($formulario->Campos) > 0) {
                        //si el formulario de la etapa esta dentro de los pasos ejecutables, recorro su lista de campos
                        if ($paso_ejecutable->formulario_id == $formulario->id) {
                            $array_campos_ejecutables = array();

                            foreach ($formulario->Campos as $campo) {
                                //guardo un array de campos ejecutables (if con campos omitidos)
                                if ($campo->tipo != 'agenda' && $campo->tipo != 'pagos' && $campo->tipo != 'documento' && $campo->tipo != 'fieldset' && $campo->tipo != 'estado_pago' && $campo->tipo != 'javascript') {
                                    array_push($array_campos_ejecutables, $campo);
                                }
                            }

                            if (count($array_campos_ejecutables) > 0) {
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

            if ($hay_pasos_generar_pdf) {

                $generar_file = true;
                $cont = 0;
                foreach ($array_formularios_ejecutables as $formulario_ejecutable) {

                    $documento->contenido .= '<h1 style="text-align:center">' . $formulario_ejecutable->nombre_paso . '</h1>';

                    foreach ($formulario_ejecutable->campos as $campo_ejecutable) {


                        if (($campo_ejecutable->valor_default != '' && $campo_ejecutable->readonly == 1) || ($campo_ejecutable->tipo == 'paragraph' || $campo->tipo == 'subtitle') || ($campo_ejecutable->esVisibleParaLaEtapaActual($etapa->id))) {
                            $variable_campo = '@@' . $campo_ejecutable->nombre;

                            if ($campo_ejecutable->tipo == 'tabla-responsive') {
                                $documento->contenido .= '<strong>' . $campo_ejecutable->etiqueta . ': </strong><br>' . $variable_campo . '<br><br>';
                            } else if ($campo_ejecutable->tipo == 'radio' || $campo_ejecutable->tipo == 'select') {
                                $variable_campo = '@@' . $campo_ejecutable->nombre . '__etiqueta';
                                $documento->contenido .= '<strong>' . $campo_ejecutable->etiqueta . ': </strong>' . $variable_campo . '<br><br>';
                            } else if ($campo_ejecutable->tipo == 'dialogo' || $campo_ejecutable->tipo == 'error') {

                                if ($campo_ejecutable->dependiente_campo == '') {
                                    $documento->contenido .= $campo_ejecutable->valor_default . '<br><br>';
                                } else {
                                    $dependiente_tipo = $campo_ejecutable->dependiente_tipo;
                                    $dependiente_relacion = $campo_ejecutable->dependiente_relacion;
                                    $dependiente_campo = $campo_ejecutable->dependiente_campo;
                                    $dependiente_valor = $campo_ejecutable->dependiente_valor;

                                    foreach ($formulario_ejecutable->campos as $campo_ejecutable_dependiente) {

                                        if ($campo_ejecutable_dependiente->nombre == $dependiente_campo) {
                                            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_ejecutable_dependiente->nombre, $etapa->id);

                                            if ($dependiente_tipo == "regex") {

                                                if ($dependiente_relacion == '==' && preg_match($dependiente_valor, $dato->valor)) {
                                                    $documento->contenido .= $campo_ejecutable->valor_default . '<br><br>';
                                                }

                                                if ($dependiente_relacion == '!=' && !preg_match($dependiente_valor, $dato->valor)) {
                                                    $documento->contenido .= $campo_ejecutable->valor_default . '<br><br>';
                                                }
                                            }

                                            if ($dependiente_tipo == 'string') {

                                                if ($dependiente_relacion == '==' && $dependiente_valor == $dato->valor) {
                                                    $documento->contenido .= $campo_ejecutable->valor_default . '<br><br>';
                                                }

                                                if ($dependiente_relacion == '!=' && $dependiente_valor != $dato->valor) {
                                                    $documento->contenido .= $campo_ejecutable->valor_default . '<br><br>';
                                                }
                                            }
                                        }
                                    }
                                }
                            } else if ($campo_ejecutable->tipo == 'paragraph' || $campo_ejecutable->tipo == 'subtitle') {

                                if ($campo_ejecutable->dependiente_campo == '') {
                                    $documento->contenido .= '<br>' . $campo_ejecutable->etiqueta . '<br><br>';
                                } else {
                                    $dependiente_tipo = $campo_ejecutable->dependiente_tipo;
                                    $dependiente_relacion = $campo_ejecutable->dependiente_relacion;
                                    $dependiente_campo = $campo_ejecutable->dependiente_campo;
                                    $dependiente_valor = $campo_ejecutable->dependiente_valor;

                                    foreach ($formulario_ejecutable->campos as $campo_ejecutable_dependiente) {

                                        if ($campo_ejecutable_dependiente->nombre == $dependiente_campo) {
                                            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_ejecutable_dependiente->nombre, $etapa->id);

                                            if ($dependiente_tipo == "regex") {

                                                if ($dependiente_relacion == '==' && preg_match($dependiente_valor, $dato->valor)) {
                                                    $documento->contenido .= '<br>' . $campo_ejecutable->etiqueta . '<br><br>';
                                                }

                                                if ($dependiente_relacion == '!=' && !preg_match($dependiente_valor, $dato->valor)) {
                                                    $documento->contenido .= '<br>' . $campo_ejecutable->etiqueta . '<br><br>';
                                                }
                                            }

                                            if ($dependiente_tipo == 'string') {

                                                if ($dependiente_relacion == '==' && $dependiente_valor == $dato->valor) {
                                                    $documento->contenido .= '<br>' . $campo_ejecutable->etiqueta . '<br><br>';
                                                }

                                                if ($dependiente_relacion == '!=' && $dependiente_valor != $dato->valor) {
                                                    $documento->contenido .= '<br>' . $campo_ejecutable->etiqueta . '<br><br>';
                                                }
                                            }
                                        }
                                    }
                                }
                            } else if ($campo_ejecutable->valor_default != '' && $campo_ejecutable->readonly == 1) {
                                $documento->contenido .= '<strong>' . $campo_ejecutable->etiqueta . ': </strong>' . $campo_ejecutable->valor_default . '<br><br>';
                            } else {
                                $documento->contenido .= '<strong>' . $campo_ejecutable->etiqueta . ':</strong> ' . $variable_campo . '<br><br>';
                            }
                        }
                    }

                    $cont++;

                    if ($cont != count($array_formularios_ejecutables)) {
                        $documento->contenido .= '<br pagebreak="true" />';
                    }
                }

                array_push($array_etapas_para_pdf, $etapa);
            }
            $cuenta = Cuenta::cuentaSegunDominio();

            if ($generar_file) {
                $documento->generar_pasos_pdf_tarae_sin_asignar($etapa, $etapa->Tramite->Proceso->nombre, $cuenta);
            }
        } else {
            redirect(site_url());
        }
    }

    //Funcion para eliminar las fechas de la agenda que no tienen cupo disponible
    public function eliminar_sin_disponibilidad($result) {
        $x = json_decode($result, true);
        $arr = $x['disponibilidades'];
        $contar_cupo = 0;
        $cont = 0;
        foreach ($x['disponibilidades'] as $key => $value) {
            foreach ($value as $keys => $val) {
                foreach ($val as $v) {
                    $contar_cupo += $v['cupo'];
                }
                if ($contar_cupo == 0) {
                    array_splice($x['disponibilidades'], $key - $cont, true);
                    $cont++;
                }
                $contar_cupo = 0;
            }
        }
        // var_dump($x);
        $result = json_encode($x, true);
        return $result;
    }

    public function agenda_sae_api_disponibilidades() {
        try {

            $url = $this->input->post('url');

            $token = openssl_decrypt($this->input->post('token'), 'bf-ofb', 'simple_1058');

            $data_array = array(
                "token" => $token,
                "idEmpresa" => $this->input->post('id_empresa'),
                "idAgenda" => $this->input->post('id_agenda'),
                "idRecurso" => $this->input->post('id_recurso'),
                "idioma" => $this->input->post('idioma')
            );

            $data = json_encode($data_array);

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
            );

            $result = curl_exec($curl);

            $curl_errno = curl_errno($curl);
            $curl_error = curl_error($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            if ($curl_errno > 0 || $http_code != 200) {
                $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
                fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR CURL: ' . $curl_error . ' (http code: ' . $http_code . ')' . "\n");
                fclose($log);
                throw new Exception($curl_error);
            }
            $res = $this->eliminar_sin_disponibilidad($result);
            header('Content-type: application/json; charset=utf-8');
            echo $res;
            exit();
        } catch (Exception $e) {
            $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
            fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR EXCEPTION: ' . $e->getMessage() . "\n");
            fclose($log);
            throw new Exception($e->getMessage());
        }
    }

    public function agenda_sae_api_confirmar_reserva() {
        try {

            $url = $this->input->post('url');
            $etapa_id = $this->input->post('etapa_id');

            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            if (!$etapa) {
                show_404();
            }

            //verifico si el usuario pertenece el grupo MesaDeEntrada y  esta actuando como un ciudadano
            if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
                //el usuario se saca desde la session con el id_usuario_ciudadano
                $usuario_sesion = Doctrine_Query::create()
                        ->from('Usuario u')
                        ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                        ->fetchOne();

                $funcionario_actuando_como_ciudadano = true;
            } else {
                //caso normal el usuario es el logueado
                $usuario_sesion = UsuarioSesion::usuario();
                $funcionario_actuando_como_ciudadano = false;
            }

            //la etapa siempre tiene usuario id o el ficticio (no registrado) o el registrado
            //en caso de ser el ficticio tambien esta en la session con el mismo id.
            if ($etapa->usuario_id != $usuario_sesion->id) {
                if (!$usuario_sesion->registrado) {
                    $this->session->set_flashdata('redirect', current_url());
                    //TODO modificado antes llamaba a login_saml
                    redirect('autenticacion/login?redirect=' . current_url());
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


            $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_transaccion_traza', $etapa_id);

            $token = openssl_decrypt($this->input->post('token'), 'bf-ofb', 'simple_1058');

            $paso = $etapa->getPasoEjecutable((int) $this->input->post('secuencia'));

            $traza_paso = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso->orden - 1))
                    ->orderBy('secuencia DESC')
                    ->fetchOne();

            $numero_paso = $traza_paso->num_paso + 2;

            $data_array = array(
                "token" => $token,
                "idEmpresa" => $this->input->post('id_empresa'),
                "idAgenda" => $this->input->post('id_agenda'),
                "idRecurso" => $this->input->post('id_recurso'),
                "idDisponibilidad" => $this->input->post('id_disponibilidad'),
                "idTransaccionPadre" => $id_transaccion->valor,
                "pasoTransaccionPadre" => $numero_paso,
                "datosReserva" => $this->input->post('datos_reserva'),
                "idioma" => $this->input->post('idioma')
            );


            $data = json_encode($data_array);

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
            );

            $result = curl_exec($curl);

            $curl_errno = curl_errno($curl);
            $curl_error = curl_error($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            if ($curl_errno > 0 || $http_code != 200) {
                $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
                fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR CURL: ' . $curl_error . ' (http code: ' . $http_code . ')' . "\n");
                fclose($log);
                throw new Exception($curl_error);
            }

            $resultado_class = json_decode($result);

            //guardo datos de seguimiento para la agenda
            if ($resultado_class->resultado == '1') {

                $nombre_campo = $this->input->post('nombre_campo');

                $datos_guardar = array(
                    $nombre_campo . '_id' => $resultado_class->id,
                    $nombre_campo . '_serieNumero' => $resultado_class->serieNumero,
                    $nombre_campo . '_codigoCancelacion' => $resultado_class->codigoCancelacion,
                    $nombre_campo . '_codigoTrazabilidad' => $resultado_class->codigoTrazabilidad,
                    $nombre_campo . '_textoTicket' => $resultado_class->textoTicket
                );

                foreach ($datos_guardar as $nombre_dato => $valor_dato) {
                    $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_dato, $etapa_id);
                    if (!$dato_seguimiento) {
                        $dato_seguimiento = new DatoSeguimiento();
                    }
                    $dato_seguimiento->nombre = $nombre_dato;
                    $dato_seguimiento->valor = (string) $valor_dato;
                    $dato_seguimiento->etapa_id = $etapa_id;
                    $dato_seguimiento->save();
                }
            }

            header('Content-type: application/json; charset=utf-8');
            echo $result;
            exit();
        } catch (Exception $e) {
            $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
            fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR EXCEPTION: ' . $e->getMessage() . "\n");
            fclose($log);
            throw new Exception($e->getMessage());
        }
    }

    public function confirmar_reserva_agenda_sae_interno_simple() {
        $etapa_id = $this->input->post('etapa_id');
        $campo_nombre = $this->input->post('campo_nombre');

        $dato_agenda = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_nombre, $etapa_id);

        if (!$dato_agenda) {
            $datos = json_decode($this->input->post('datos'), true);
            $fecha = $this->input->post('fecha');

            $dato_agenda = new DatoSeguimiento();
            $dato_agenda->nombre = $campo_nombre;

            $datos["fecha_confirmacion"] = (string) $fecha;
            $datos["fecha_confirmacion_reserva"] = (string) strtotime(date('Y/m/d'));

            $dato_agenda->valor = (string) json_encode($datos);

            $dato_agenda->etapa_id = $etapa_id;
            $dato_agenda->save();
        }
    }

    public function confirmar_reserva_agenda__multiple_sae_interno_simple() {
        $etapa_id = $this->input->post('etapa_id');
        $campo_nombre = $this->input->post('campo_nombre');

        $dato_agenda = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_nombre, $etapa_id);

        if (!$dato_agenda) {
            $datos = json_decode($this->input->post('datos'), true);
            $fecha = $this->input->post('fecha');

            $dato_agenda = new DatoSeguimiento();
            $dato_agenda->nombre = $campo_nombre;

            $datos["fecha_confirmacion"] = (string) $fecha;
            $datos["fecha_confirmacion_reserva"] = (string) strtotime(date('Y/m/d'));

            $dato_agenda->valor = (string) json_encode($datos);

            $dato_agenda->etapa_id = $etapa_id;
            $dato_agenda->save();
        }
    }

    public function agenda_multiple_sae_api_anadir_reserva() {
        try {

            $url = $this->input->post('url');
            $etapa_id = $this->input->post('etapa_id');

            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            if (!$etapa) {
                show_404();
            }

            //verifico si el usuario pertenece el grupo MesaDeEntrada y  esta actuando como un ciudadano
            if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
                //el usuario se saca desde la session con el id_usuario_ciudadano
                $usuario_sesion = Doctrine_Query::create()
                        ->from('Usuario u')
                        ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                        ->fetchOne();

                $funcionario_actuando_como_ciudadano = true;
            } else {
                //caso normal el usuario es el logueado
                $usuario_sesion = UsuarioSesion::usuario();
                $funcionario_actuando_como_ciudadano = false;
            }

            //la etapa siempre tiene usuario id o el ficticio (no registrado) o el registrado
            //en caso de ser el ficticio tambien esta en la session con el mismo id.
            if ($etapa->usuario_id != $usuario_sesion->id) {
                if (!$usuario_sesion->registrado) {
                    $this->session->set_flashdata('redirect', current_url());
                    //TODO modificado antes llamaba a login_saml
                    redirect('autenticacion/login?redirect=' . current_url());
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

            $nombre_campo = $this->input->post('nombre_campo');
            $total_personas = $this->input->post('cantidad_personas');
            $lista_personas = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__cantidad_total_personas', $etapa_id);
            if ($lista_personas) {
                $lista_personas->delete();
            }
            $lista_personas = new DatoSeguimiento();
            $lista_personas->nombre = $nombre_campo . '__cantidad_total_personas';
            $lista_personas->valor = $total_personas;
            $lista_personas->etapa_id = $etapa_id;
            $lista_personas->save();

            $token = openssl_decrypt($this->input->post('token'), 'bf-ofb', 'simple_1058');

            $data_array = array(
                "token" => $token,
                "idEmpresa" => $this->input->post('id_empresa'),
                "idAgenda" => $this->input->post('id_agenda'),
                "idRecurso" => $this->input->post('id_recurso'),
                "idDisponibilidad" => $this->input->post('id_disponibilidad'),
                "datosReserva" => $this->input->post('datos_reserva'),
                "tokenReserva" => $this->input->post('token_reserva'),
                "idioma" => $this->input->post('idioma')
            );


            $data = json_encode($data_array);

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
            );

            $result = curl_exec($curl);

            $curl_errno = curl_errno($curl);
            $curl_error = curl_error($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            //print_r($result);
            curl_close($curl);

            if ($curl_errno > 0 || $http_code != 200) {
                $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
                fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR CURL: ' . $curl_error . ' (http code: ' . $http_code . ')' . "\n");
                fclose($log);
                throw new Exception($curl_error);
            }

            $resultado_class = json_decode($result);

            //guardo datos de seguimiento para la agenda
            if ($resultado_class->resultado == '1') {
                $id_persona = $this->input->post('id_persona');
                $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__json_reserva', $etapa_id);
                $json_reservar = new stdClass();
                if ($datos_seguimiento) {
                    $json_reservar = $datos_seguimiento->valor;
                    $datos_seguimiento->delete();
                }

                $json_reservar->$id_persona = new stdClass();
                $json_reservar->$id_persona->id_reserva = $resultado_class->id;
                $json_reservar->$id_persona->fecha = $this->input->post('fecha');
                $json_reservar->$id_persona->hora = $this->input->post('hora');
                $json_reservar->$id_persona->serieNumero = $resultado_class->serieNumero;
                $json_reservar->$id_persona->codigoCancelacion = $resultado_class->codigoCancelacion;
                $json_reservar->$id_persona->codigoTrazabilidad = $resultado_class->codigoTrazabilidad;
                $datos_seguimiento = new DatoSeguimiento();
                $datos_seguimiento->nombre = $nombre_campo . '__json_reserva';
                $datos_seguimiento->valor = json_encode($json_reservar);
                $datos_seguimiento->etapa_id = $etapa_id;
                $datos_seguimiento->save();

                $datos_seguimiento_fecha = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__fecha_modificacion', $etapa_id);
                //$fecha_actual=$datos_seguimiento_fecha->valor;
                $fecha_respuesta = $resultado_class->fechaHora;
                $fecha = date_parse_from_format("YmdHi", $fecha_respuesta);
                $fecha_time = strtotime($fecha['year'] . '/' . $fecha['month'] . '/' . $fecha['day'] . ' ' . $fecha['hour'] . ':' . $fecha['minute']);
                if ($datos_seguimiento_fecha) {
                    $fecha_actual = $datos_seguimiento_fecha->valor;
                    if (count(get_object_vars($json_reservar)) == 1) {
                        $datos_seguimiento_fecha->valor = $fecha_time;
                        $datos_seguimiento_fecha->save();
                    } else {
                        $fecha_actual = $fecha_time;
                        foreach ($json_reservar as $persona) {
                            $dia_base = explode('/', $persona->fecha);
                            $fecha_base = trim($dia_base[2]) . '/' . trim($dia_base[1]) . '/' . trim($dia_base[0]) . ' ' . $persona->hora;
                            $fecha_base_time = strtotime($fecha_base);
                            if ($fecha_base_time < $fecha_actual) {
                                $fecha_actual = $fecha_base_time;
                            }
                        }
                        $datos_seguimiento_fecha->valor = $fecha_actual;
                        $datos_seguimiento_fecha->save();
                    }
                } else {
                    $datos_seguimiento_fecha = new DatoSeguimiento();
                    $datos_seguimiento_fecha->nombre = $nombre_campo . '__fecha_modificacion';
                    $datos_seguimiento_fecha->valor = $fecha_time;
                    $datos_seguimiento_fecha->etapa_id = $etapa_id;
                    $datos_seguimiento_fecha->save();
                }

                $datos_seguimiento_fechaUltima = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__fechaUltimaResera', $etapa_id);
                $fecha_respuesta = $resultado_class->fechaHora;
                $fecha = date_parse_from_format("YmdHi", $fecha_respuesta);
                $fecha_time = strtotime($fecha['year'] . '/' . $fecha['month'] . '/' . $fecha['day'] . ' ' . $fecha['hour'] . ':' . $fecha['minute']);
                if ($datos_seguimiento_fechaUltima) {
                    $fecha_actual = $datos_seguimiento_fechaUltima->valor;
                    if (count(get_object_vars($json_reservar)) == 1) {
                        $datos_seguimiento_fechaUltima->valor = $fecha_time;
                        $datos_seguimiento_fechaUltima->save();
                    } else {
                        $fecha_actual = $fecha_time;
                        foreach ($json_reservar as $persona) {
                            $dia_base = explode('/', $persona->fecha);
                            $fecha_base = trim($dia_base[2]) . '/' . trim($dia_base[1]) . '/' . trim($dia_base[0]) . ' ' . $persona->hora;
                            $fecha_base_time = strtotime($fecha_base);
                            if ($fecha_base_time > $fecha_actual) {
                                $fecha_actual = $fecha_base_time;
                            }
                        }
                        $datos_seguimiento_fechaUltima->valor = $fecha_actual;
                        $datos_seguimiento_fechaUltima->save();
                    }
                } else {
                    $datos_seguimiento_fechaUltima = new DatoSeguimiento();
                    $datos_seguimiento_fechaUltima->nombre = $nombre_campo . '__fechaUltimaResera';
                    $datos_seguimiento_fechaUltima->valor = $fecha_time;
                    $datos_seguimiento_fechaUltima->etapa_id = $etapa_id;
                    $datos_seguimiento_fechaUltima->save();
                }
            }
            header('Content-type: application/json; charset=utf-8');
            echo $result;
            exit();
        } catch (Exception $e) {
            $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
            fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR EXCEPTION: ' . $e->getMessage() . "\n");
            fclose($log);
            throw new Exception($e->getMessage());
        }
    }

    public function agenda_multiple_sae_api_editar_reserva() {
        try {

            $url = $this->input->post('url');
            $etapa_id = $this->input->post('etapa_id');

            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            if (!$etapa) {
                show_404();
            }

            //verifico si el usuario pertenece el grupo MesaDeEntrada y  esta actuando como un ciudadano
            if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
                //el usuario se saca desde la session con el id_usuario_ciudadano
                $usuario_sesion = Doctrine_Query::create()
                        ->from('Usuario u')
                        ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                        ->fetchOne();

                $funcionario_actuando_como_ciudadano = true;
            } else {
                //caso normal el usuario es el logueado
                $usuario_sesion = UsuarioSesion::usuario();
                $funcionario_actuando_como_ciudadano = false;
            }

            //la etapa siempre tiene usuario id o el ficticio (no registrado) o el registrado
            //en caso de ser el ficticio tambien esta en la session con el mismo id.
            if ($etapa->usuario_id != $usuario_sesion->id) {
                if (!$usuario_sesion->registrado) {
                    $this->session->set_flashdata('redirect', current_url());
                    //TODO modificado antes llamaba a login_saml
                    redirect('autenticacion/login?redirect=' . current_url());
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


            $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_transaccion_traza', $etapa_id);

            $token = openssl_decrypt($this->input->post('token'), 'bf-ofb', 'simple_1058');

            $paso = $etapa->getPasoEjecutable((int) $this->input->post('secuencia'));

            $traza_paso = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso->orden - 1))
                    ->orderBy('secuencia DESC')
                    ->fetchOne();

            //$numero_paso = $traza_paso->num_paso + 2;

            $data_array = array(
                "token" => $token,
                "idEmpresa" => $this->input->post('id_empresa'),
                "idAgenda" => $this->input->post('id_agenda'),
                "idRecurso" => $this->input->post('id_recurso'),
                "idDisponibilidad" => $this->input->post('id_disponibilidad'),
                "idReserva" => $this->input->post('id_reserva'),
                "codigoSeguridad" => $this->input->post('id_cancelacion'),
                "idioma" => $this->input->post('idioma')
            );


            $data = json_encode($data_array);

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
            );

            $result = curl_exec($curl);

            $curl_errno = curl_errno($curl);
            $curl_error = curl_error($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            //print_r($result);
            curl_close($curl);

            if ($curl_errno > 0 || $http_code != 200) {
                $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
                fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR CURL: ' . $curl_error . ' (http code: ' . $http_code . ')' . "\n");
                fclose($log);
                throw new Exception($curl_error);
            }

            $resultado_class = json_decode($result);

            //guardo datos de seguimiento para la agenda
            if ($resultado_class->resultado == '1') {
                $nombre_campo = $this->input->post('nombre_campo');
                $id_persona = $this->input->post('id_persona');
                $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__json_reserva', $etapa_id);
                $json_reservar = new stdClass();
                if ($datos_seguimiento) {
                    $json_reservar = $datos_seguimiento->valor;
                    $datos_seguimiento->delete();
                }
                $json_reservar->$id_persona = new stdClass();
                $json_reservar->$id_persona->id_reserva = $resultado_class->id;
                $json_reservar->$id_persona->fecha = $this->input->post('fecha');
                $json_reservar->$id_persona->hora = $this->input->post('hora');
                $json_reservar->$id_persona->serieNumero = $resultado_class->serieNumero;
                $json_reservar->$id_persona->codigoCancelacion = $resultado_class->codigoCancelacion;
                $json_reservar->$id_persona->codigoTrazabilidad = $resultado_class->codigoTrazabilidad;
                $datos_seguimiento = new DatoSeguimiento();
                $datos_seguimiento->nombre = $nombre_campo . '__json_reserva';
                $datos_seguimiento->valor = json_encode($json_reservar);
                $datos_seguimiento->etapa_id = $etapa_id;
                $datos_seguimiento->save();

                $datos_seguimiento_fecha = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__fecha_modificacion', $etapa_id);
                $fecha_respuesta = $resultado_class->fechaHora;
                $fecha = date_parse_from_format("YmdHi", $fecha_respuesta);
                $fecha_time = strtotime($fecha['year'] . '/' . $fecha['month'] . '/' . $fecha['day'] . ' ' . $fecha['hour'] . ':' . $fecha['minute']);
                if ($datos_seguimiento_fecha) {
                    $fecha_actual = $datos_seguimiento_fecha->valor;
                    if (count(get_object_vars($json_reservar)) == 1) {
                        $datos_seguimiento_fecha->valor = $fecha_time;
                        $datos_seguimiento_fecha->save();
                    } else {
                        $fecha_actual = $fecha_time;
                        foreach ($json_reservar as $persona) {
                            $dia_base = explode('/', $persona->fecha);
                            $fecha_base = trim($dia_base[2]) . '/' . trim($dia_base[1]) . '/' . trim($dia_base[0]) . ' ' . $persona->hora;
                            $fecha_base_time = strtotime($fecha_base);
                            if ($fecha_base_time < $fecha_actual) {
                                $fecha_actual = $fecha_base_time;
                            }
                        }
                        $datos_seguimiento_fecha->valor = $fecha_actual;
                        $datos_seguimiento_fecha->save();
                    }
                } else {
                    $datos_seguimiento_fecha = new DatoSeguimiento();
                    $datos_seguimiento_fecha->nombre = $nombre_campo . '__fecha_modificacion';
                    $datos_seguimiento_fecha->valor = $fecha_time;
                    $datos_seguimiento_fecha->etapa_id = $etapa_id;
                    $datos_seguimiento_fecha->save();
                }

                $datos_seguimiento_fechaUltima = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__fechaUltimaResera', $etapa_id);
                $fecha_respuesta = $resultado_class->fechaHora;
                $fecha = date_parse_from_format("YmdHi", $fecha_respuesta);
                $fecha_time = strtotime($fecha['year'] . '/' . $fecha['month'] . '/' . $fecha['day'] . ' ' . $fecha['hour'] . ':' . $fecha['minute']);
                if ($datos_seguimiento_fechaUltima) {
                    $fecha_actual = $datos_seguimiento_fechaUltima->valor;
                    if (count(get_object_vars($json_reservar)) == 1) {
                        $datos_seguimiento_fechaUltima->valor = $fecha_time;
                        $datos_seguimiento_fechaUltima->save();
                    } else {
                        $fecha_actual = $fecha_time;
                        foreach ($json_reservar as $persona) {
                            $dia_base = explode('/', $persona->fecha);
                            $fecha_base = trim($dia_base[2]) . '/' . trim($dia_base[1]) . '/' . trim($dia_base[0]) . ' ' . $persona->hora;
                            $fecha_base_time = strtotime($fecha_base);
                            if ($fecha_base_time > $fecha_actual) {
                                $fecha_actual = $fecha_base_time;
                            }
                        }
                        $datos_seguimiento_fechaUltima->valor = $fecha_actual;
                        $datos_seguimiento_fechaUltima->save();
                    }
                } else {
                    $datos_seguimiento_fechaUltima = new DatoSeguimiento();
                    $datos_seguimiento_fechaUltima->nombre = $nombre_campo . '__fechaUltimaResera';
                    $datos_seguimiento_fechaUltima->valor = $fecha_time;
                    $datos_seguimiento_fechaUltima->etapa_id = $etapa_id;
                    $datos_seguimiento_fechaUltima->save();
                }
            }
            header('Content-type: application/json; charset=utf-8');
            echo $result;
            exit();
        } catch (Exception $e) {
            $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
            fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR EXCEPTION: ' . $e->getMessage() . "\n");
            fclose($log);
            throw new Exception($e->getMessage());
        }
    }

    public function agenda_multiple_sae_api_cancelar_reserva() {
        try {
            $etapa_id = $this->input->post('etapa_id');
            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            if (!$etapa) {
                show_404();
            }
            //verifico si el usuario pertenece el grupo MesaDeEntrada y  esta actuando como un ciudadano
            if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
                //el usuario se saca desde la session con el id_usuario_ciudadano
                $usuario_sesion = Doctrine_Query::create()
                        ->from('Usuario u')
                        ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                        ->fetchOne();

                $funcionario_actuando_como_ciudadano = true;
            } else {
                //caso normal el usuario es el logueado
                $usuario_sesion = UsuarioSesion::usuario();
                $funcionario_actuando_como_ciudadano = false;
            }

            $url = $this->input->post('url');
            $token = openssl_decrypt($this->input->post('token'), 'bf-ofb', 'simple_1058');
            $data_array = array(
                "token" => $token,
                "idEmpresa" => $this->input->post('id_empresa'),
                "idAgenda" => $this->input->post('id_agenda'),
                "idRecurso" => $this->input->post('id_recurso'),
                "idReserva" => $this->input->post('id_reserva'),
                "codigoCancelacion" => $this->input->post('codigo_cancelacion'),
                "idioma" => $this->input->post('idioma')
            );


            $data = json_encode($data_array);

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
            );

            $result = curl_exec($curl);
            $curl_errno = curl_errno($curl);
            $curl_error = curl_error($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);
            //print_r($result);
            if ($curl_errno > 0 || $http_code != 200) {
                $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
                fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR CURL: ' . $curl_error . ' (http code: ' . $http_code . ')' . "\n");
                fclose($log);
                throw new Exception($curl_error);
            }

            $resultado_class = json_decode($result);
            if ($resultado_class->resultado == '1') {
                $id_persona = $this->input->post('id_persona');
                $nombre_campo = $this->input->post('nombre_campo');
                $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__json_reserva', $etapa_id);
                $json_reservar = new stdClass();
                if ($datos_seguimiento) {
                    $json_reservar = $datos_seguimiento->valor;
                    unset($json_reservar->$id_persona);
                    $datos_seguimiento->valor = json_encode($json_reservar);
                    $datos_seguimiento->save();
                }
                $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_token_reservas', $etapa_id);
                if ($datos_seguimiento) {
                    $datos_seguimiento->delete();
                }
/*
                $datos_seguimiento_fecha = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__fecha_modificacion', $etapa_id);
                $fecha = date("YmdHi");
                $fecha_time = strtotime($fecha['year'] . '/' . $fecha['month'] . '/' . $fecha['day'] . ' ' . $fecha['hour'] . ':' . $fecha['minute']);
                if ($datos_seguimiento_fecha) {
                    $fecha_actual = $fecha_time;
                    foreach ($json_reservar as $persona) {
                        $dia_base = explode('/', $persona->fecha);
                        $fecha_base = trim($dia_base[2]) . '/' . trim($dia_base[1]) . '/' . trim($dia_base[0]) . ' ' . $persona->hora;
                        $fecha_base_time = strtotime($fecha_base);
                        if ($fecha_base_time < $fecha_actual) {
                            $fecha_actual = $fecha_base_time;
                        }
                    }
                    $datos_seguimiento_fecha->valor = $fecha_actual;
                    $datos_seguimiento_fecha->save();
                }
                
                $datos_seguimiento_fechaUltima = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__fechaUltimaResera', $etapa_id);
                $fecha = date("YmdHi");
                $fecha_time = strtotime($fecha['year'] . '/' . $fecha['month'] . '/' . $fecha['day'] . ' ' . $fecha['hour'] . ':' . $fecha['minute']);
                if ($datos_seguimiento_fechaUltima) {
                    $fecha_actual = $datos_seguimiento_fechaUltima->valor;
                    foreach ($json_reservar as $persona) {
                        $dia_base = explode('/', $persona->fecha);
                        $fecha_base = trim($dia_base[2]) . '/' . trim($dia_base[1]) . '/' . trim($dia_base[0]) . ' ' . $persona->hora;
                        $fecha_base_time = strtotime($fecha_base);
                        if ($fecha_base_time > $fecha_actual) {
                            $fecha_actual = $fecha_base_time;
                        }
                    }
                    $datos_seguimiento_fecha->valor = $fecha_actual;
                    $datos_seguimiento_fecha->save();
                }*/
            }

            header('Content-type: application/json; charset=utf-8');
            echo $result;
            exit();
        } catch (Exception $e) {
            $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
            fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR EXCEPTION: ' . $e->getMessage() . "\n");
            fclose($log);
            throw new Exception($e->getMessage());
        }
    }

    public function reserva_multiple_confirmar_reservas() {
        try {
            $etapa_id = $this->input->post('etapa_id');

            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            if (!$etapa) {
                show_404();
            }

            //verifico si el usuario pertenece el grupo MesaDeEntrada y  esta actuando como un ciudadano
            if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
                //el usuario se saca desde la session con el id_usuario_ciudadano
                $usuario_sesion = Doctrine_Query::create()
                        ->from('Usuario u')
                        ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                        ->fetchOne();

                $funcionario_actuando_como_ciudadano = true;
            } else {
                //caso normal el usuario es el logueado
                $usuario_sesion = UsuarioSesion::usuario();
                $funcionario_actuando_como_ciudadano = false;
            }

            //la etapa siempre tiene usuario id o el ficticio (no registrado) o el registrado
            //en caso de ser el ficticio tambien esta en la session con el mismo id.
            if ($etapa->usuario_id != $usuario_sesion->id) {
                if (!$usuario_sesion->registrado) {
                    $this->session->set_flashdata('redirect', current_url());
                    //TODO modificado antes llamaba a login_saml
                    redirect('autenticacion/login?redirect=' . current_url());
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


            $paso = $etapa->getPasoEjecutable((int) $this->input->post('secuencia'));

            $traza_paso = Doctrine_Query::create()
                    ->from('Trazabilidad ts')
                    ->where('ts.id_tramite = ? AND ts.id_tarea = ? AND ts.num_paso_real = ?', array($etapa->Tramite->id, $etapa->Tarea->id, $paso->orden - 1))
                    ->orderBy('secuencia DESC')
                    ->fetchOne();
            if ($traza_paso) {
                $id_transaccion = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_transaccion_traza', $etapa->id);
                $id_transaccion = $id_transaccion->id;
                $numero_paso = $traza_paso->num_paso + 2;
            } else {
                $numero_paso = "";
                $id_transaccion = null;
            }
            $nombre_campo = $this->input->post('nombre_campo');
            $id_campo = $this->input->post('id_campo');
            $campo = Doctrine::getTable('Campo')->find($id_campo);
            $json_confirmado = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . "__json_reserva", $etapa->id);
            $json_total = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . "__cantidad_total_personas", $etapa->id);

            if ($json_total->valor == count(json_decode(json_encode($json_confirmado->valor), true))) {
                $url = $this->input->post('url');
                $token = openssl_decrypt($this->input->post('token'), 'bf-ofb', 'simple_1058');
                $data_array = array(
                    "token" => $token,
                    "idEmpresa" => $this->input->post('id_empresa'),
                    "idAgenda" => $this->input->post('id_agenda'),
                    "idRecurso" => $this->input->post('id_recurso'),
                    "tokenReserva" => $this->input->post('token_reserva'),
                    "idTransaccionPadre" => $id_transaccion,
                    "pasoTransaccionPadre" => $numero_paso,
                    "idioma" => $this->input->post('idioma')
                );


                $data = json_encode($data_array);

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }

                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
                );

                $result = curl_exec($curl);
                $curl_errno = curl_errno($curl);
                $curl_error = curl_error($curl);
                $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                curl_close($curl);

                if ($curl_errno > 0 || $http_code != 200) {
                    $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
                    fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR CURL: ' . $curl_error . ' (http code: ' . $http_code . ')' . "\n");
                    fclose($log);
                    throw new Exception($curl_error);
                }

                $resultado_class = json_decode($result);
//                print_r($resultado_class);
//                show_error(1);

                if ($resultado_class->resultado == '1') {

                    $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_confirmada_reservas', $etapa_id);
                    if ($dato_seguimiento) {
                        $dato_seguimiento->delete();
                    }

                    $dato_seguimiento = new DatoSeguimiento();
                    $dato_seguimiento->nombre = $nombre_campo . '_confirmada_reservas';
                    $dato_seguimiento->valor = (string) $resultado_class->resultado;
                    $dato_seguimiento->etapa_id = $etapa_id;
                    $dato_seguimiento->save();

                    $nombre_campo = $this->input->post('nombre_campo');
                    $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_fecha_confirmacion', $etapa_id);
                    if ($dato_seguimiento) {
                        $dato_seguimiento->delete();
                    }

                    $dato_seguimiento = new DatoSeguimiento();
                    $dato_seguimiento->nombre = $nombre_campo . '_fecha_confirmacion';
                    $dato_seguimiento->valor = (string) strtotime(date("Y/m/d"));
                    $dato_seguimiento->etapa_id = $etapa_id;
                    $dato_seguimiento->save();

                    $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_reservas', $etapa_id);
                    if ($dato_seguimiento) {
                        $dato_seguimiento->delete();
                    }
                    $dato_seguimiento = new DatoSeguimiento();
                    $dato_seguimiento->nombre = $nombre_campo . '_reservas';
                    $dato_seguimiento->valor = (string) json_encode($resultado_class->reservas);
                    $dato_seguimiento->etapa_id = $etapa_id;
                    $dato_seguimiento->save();

                    $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_recurso', $etapa_id);
                    if ($dato_seguimiento) {
                        $dato_seguimiento->delete();
                    }
                    $dato_seguimiento = new DatoSeguimiento();
                    $dato_seguimiento->nombre = $nombre_campo . '_recurso';
                    $dato_seguimiento->valor = (string) $this->input->post('id_recurso');
                    $dato_seguimiento->etapa_id = $etapa_id;
                    $dato_seguimiento->save();
                }
                header('Content-type: application/json; charset=utf-8');
                echo $result;
                exit();
            } else {
                $result = new stdClass();
                $result->error = -2;
                echo json_encode($result);
                exit();
            }
        } catch (Exception $e) {
            $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
            fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR EXCEPTION: ' . $e->getMessage() . "\n");
            fclose($log);
            throw new Exception($e->getMessage());
        }
    }

    public function reserva_multiple_eliminar_reservas() {
        try {
            $etapa_id = $this->input->post('etapa_id');

            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            if (!$etapa) {
                show_404();
            }

            //verifico si el usuario pertenece el grupo MesaDeEntrada y  esta actuando como un ciudadano
            if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
                //el usuario se saca desde la session con el id_usuario_ciudadano
                $usuario_sesion = Doctrine_Query::create()
                        ->from('Usuario u')
                        ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                        ->fetchOne();

                $funcionario_actuando_como_ciudadano = true;
            } else {
                //caso normal el usuario es el logueado
                $usuario_sesion = UsuarioSesion::usuario();
                $funcionario_actuando_como_ciudadano = false;
            }

            //la etapa siempre tiene usuario id o el ficticio (no registrado) o el registrado
            //en caso de ser el ficticio tambien esta en la session con el mismo id.
            if ($etapa->usuario_id != $usuario_sesion->id) {
                if (!$usuario_sesion->registrado) {
                    $this->session->set_flashdata('redirect', current_url());
                    //TODO modificado antes llamaba a login_saml
                    redirect('autenticacion/login?redirect=' . current_url());
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


            $url = $this->input->post('url');
            $token = openssl_decrypt($this->input->post('token'), 'bf-ofb', 'simple_1058');
            $data_array = array(
                "token" => $token,
                "idEmpresa" => $this->input->post('id_empresa'),
                "idAgenda" => $this->input->post('id_agenda'),
                "idRecurso" => $this->input->post('id_recurso'),
                "tokenReserva" => $this->input->post('token_reserva'),
                "idioma" => $this->input->post('idioma')
            );


            $data = json_encode($data_array);

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
            );

            $result = curl_exec($curl);
            $curl_errno = curl_errno($curl);
            $curl_error = curl_error($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            if ($curl_errno > 0 || $http_code != 200) {
                $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
                fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR CURL: ' . $curl_error . ' (http code: ' . $http_code . ')' . "\n");
                fclose($log);
                throw new Exception($curl_error);
            }

            $resultado_class = json_decode($result);

            if ($resultado_class->resultado == '1') {

                $nombre_campo = $this->input->post('nombre_campo');
                $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_confirmada_reservas', $etapa_id);
                if ($dato_seguimiento) {
                    $dato_seguimiento->delete();
                }

                $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_reservas', $etapa_id);
                if ($dato_seguimiento) {
                    $dato_seguimiento->delete();
                }

                $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_recurso', $etapa_id);
                if ($dato_seguimiento) {
                    $dato_seguimiento->delete();
                }

                $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_agenda', $etapa_id);
                if ($dato_seguimiento) {
                    $dato_seguimiento->delete();
                }

                $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_codTramite', $etapa_id);
                if ($dato_seguimiento) {
                    $dato_seguimiento->delete();
                }

                $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_empresa', $etapa_id);
                if ($dato_seguimiento) {
                    $dato_seguimiento->delete();
                }

                $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_token_reservas', $etapa_id);
                if ($dato_seguimiento) {
                    $dato_seguimiento->delete();
                }
            }
            header('Content-type: application/json; charset=utf-8');
            echo $result;
            exit();
        } catch (Exception $e) {
            $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
            fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR EXCEPTION: ' . $e->getMessage() . "\n");
            fclose($log);
            throw new Exception($e->getMessage());
        }
    }

    public function agenda_multiple_sae_api_obtener_token() {
        try {
            $etapa_id = $this->input->post('etapa_id');

            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            if (!$etapa) {
                show_404();
            }
            $nombre_campo = $this->input->post('nombre_campo');
            $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_token_reservas', $etapa_id);
            if ($dato_seguimiento) {
                $dato_seguimiento_recurso = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_recurso', $etapa_id);
                if ($dato_seguimiento_recurso) {
                    if ($dato_seguimiento_recurso->valor == $this->input->post('id_recurso')) {
                        $result = new stdClass();
                        $result->resultado = 1;
                        $result->token_reservas = $dato_seguimiento->valor;
                        $result->token_base = 1;
                        header('Content-type: application/json; charset=utf-8');
                        echo json_encode($result);
                        exit();
                    }
                }
            }
            //verifico si el usuario pertenece el grupo MesaDeEntrada y  esta actuando como un ciudadano
            if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
                //el usuario se saca desde la session con el id_usuario_ciudadano
                $usuario_sesion = Doctrine_Query::create()
                        ->from('Usuario u')
                        ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                        ->fetchOne();

                $funcionario_actuando_como_ciudadano = true;
            } else {
                //caso normal el usuario es el logueado
                $usuario_sesion = UsuarioSesion::usuario();
                $funcionario_actuando_como_ciudadano = false;
            }

            $url = $this->input->post('url');
            $token = openssl_decrypt($this->input->post('token'), 'bf-ofb', 'simple_1058');
            $data_array = array(
                "token" => $token,
                "idEmpresa" => $this->input->post('id_empresa'),
                "idAgenda" => $this->input->post('id_agenda'),
                "idRecurso" => $this->input->post('id_recurso'),
                "documento" => $this->input->post('documento'),
                "nombre" => $this->input->post('nombre'),
                "email" => $this->input->post('email'),
                "codigoTramite" => $this->input->post('codigoTramite'),
                "idioma" => $this->input->post('idioma')
            );


            $data = json_encode($data_array);

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
            );

            $result = curl_exec($curl);
            //print_r($result);
            $curl_errno = curl_errno($curl);
            $curl_error = curl_error($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            if ($curl_errno > 0 || $http_code != 200) {
                $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
                fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR CURL: ' . $curl_error . ' (http code: ' . $http_code . ')' . "\n");
                fclose($log);
                throw new Exception($curl_error);
            }

            $resultado_class = json_decode($result);
            if ($resultado_class->resultado == '1') {


                $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_token_reservas', $etapa_id);
                if ($dato_seguimiento) {
                    $dato_seguimiento->delete();
                }
                $dato_seguimiento = new DatoSeguimiento();
                $dato_seguimiento->nombre = $nombre_campo . '_token_reservas';
                $dato_seguimiento->valor = (string) $resultado_class->token_reservas;
                $dato_seguimiento->etapa_id = $etapa_id;
                $dato_seguimiento->save();

                $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_recurso', $etapa_id);
                if ($dato_seguimiento) {
                    $dato_seguimiento->delete();
                }
                $dato_seguimiento = new DatoSeguimiento();
                $dato_seguimiento->nombre = $nombre_campo . '_recurso';
                $dato_seguimiento->valor = (string) $this->input->post('id_recurso');
                $dato_seguimiento->etapa_id = $etapa_id;
                $dato_seguimiento->save();

                $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_empresa', $etapa_id);
                if ($dato_seguimiento) {
                    $dato_seguimiento->delete();
                }
                $dato_seguimiento = new DatoSeguimiento();
                $dato_seguimiento->nombre = $nombre_campo . '_empresa';
                $dato_seguimiento->valor = (string) $this->input->post('id_empresa');
                $dato_seguimiento->etapa_id = $etapa_id;
                $dato_seguimiento->save();

                $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_agenda', $etapa_id);
                if ($dato_seguimiento) {
                    $dato_seguimiento->delete();
                }
                $dato_seguimiento = new DatoSeguimiento();
                $dato_seguimiento->nombre = $nombre_campo . '_agenda';
                $dato_seguimiento->valor = (string) $this->input->post('id_agenda');
                $dato_seguimiento->etapa_id = $etapa_id;
                $dato_seguimiento->save();

                $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_codTramite', $etapa_id);
                if ($dato_seguimiento) {
                    $dato_seguimiento->delete();
                }
                $dato_seguimiento = new DatoSeguimiento();
                $dato_seguimiento->nombre = $nombre_campo . '_codTramite';
                $dato_seguimiento->valor = (string) $this->input->post('codigoTramite');
                $dato_seguimiento->etapa_id = $etapa_id;
                $dato_seguimiento->save();

                $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__fecha_modificacion', $etapa_id);
                if ($dato_seguimiento) {
                    $dato_seguimiento->delete();
                }

                $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__fechaUltimaResera', $etapa_id);
                if ($dato_seguimiento) {
                    $dato_seguimiento->delete();
                }


                $datos_personas_json = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . "__json_reserva", $etapa_id);
                if ($datos_personas_json) {
                    $datos_personas_json->delete();
                }

                $lista_personas = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__cantidad_total_personas', $etapa_id);
                if ($lista_personas) {
                    $lista_personas->delete();
                }
            } else {
                $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
                fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR CURL: ' . $curl_error . ' (http code: ' . $http_code . ')' . "\n");
                fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR CURL: ' . json_encode($resultado_class). "\n");
                fclose($log);
                throw new Exception(json_encode($resultado_class));
            }

            header('Content-type: application/json; charset=utf-8');
            $resultado_class->token_base = 0;
            echo json_encode($resultado_class);
            exit();
        } catch (Exception $e) {
            $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
            fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR EXCEPTION: ' . $e->getMessage() . "\n");
            fclose($log);
            throw new Exception($e->getMessage());
        }
    }

    public function reserva_multiple_cancelar_todas_reservas() {
        try {
            $etapa_id = $this->input->post('etapa_id');
            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            if (!$etapa) {
                show_404();
            }
            //verifico si el usuario pertenece el grupo MesaDeEntrada y  esta actuando como un ciudadano
            if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
                //el usuario se saca desde la session con el id_usuario_ciudadano
                $usuario_sesion = Doctrine_Query::create()
                        ->from('Usuario u')
                        ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                        ->fetchOne();

                $funcionario_actuando_como_ciudadano = true;
            } else {
                //caso normal el usuario es el logueado
                $usuario_sesion = UsuarioSesion::usuario();
                $funcionario_actuando_como_ciudadano = false;
            }
            $nombre_campo = $this->input->post('nombre_campo');
            $url = $this->input->post('url');
            $token = openssl_decrypt($this->input->post('token'), 'bf-ofb', 'simple_1058');

            $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_empresa', $etapa_id);
            $id_empresa = $datos_seguimiento->valor;

            $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_recurso', $etapa_id);
            $id_recurso = $datos_seguimiento->valor;

            $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_agenda', $etapa_id);
            $id_agenda = $datos_seguimiento->valor;

            $idioma = $this->input->post('idioma');

            $id_cancelados = array();
            $id_error = array();
            $datos_seguimiento_json = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__json_reserva', $etapa_id);
            if ($datos_seguimiento_json) {
                $json_reservar = $datos_seguimiento_json->valor;
                foreach ($json_reservar as $key => $value) {
                    $id_reserva = $value->id_reserva;
                    $codigoCancelacion = $value->codigoCancelacion;
                    $data_array = array(
                        "token" => $token,
                        "idEmpresa" => $id_empresa,
                        "idAgenda" => $id_agenda,
                        "idRecurso" => $id_recurso,
                        "idReserva" => $id_reserva,
                        "codigoCancelacion" => $codigoCancelacion,
                        "idioma" => $idioma
                    );

                    $data = json_encode($data_array);

                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_POST, 1);

                    if ($data) {
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                    }

                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data))
                    );

                    $result = curl_exec($curl);
                    $curl_errno = curl_errno($curl);
                    $curl_error = curl_error($curl);
                    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                    curl_close($curl);
                    //print_r($result);
                    $resultado_class = json_decode($result);
                    if ($resultado_class->resultado == '1') {
                        $id_cancelados[] = $key;
                    } else {
                        $id_error[] = $key;
                    }
                }
                foreach ($id_cancelados as $value) {
                    unset($json_reservar->$value);
                }
                $datos_seguimiento_json->valor = json_encode($json_reservar);
                $datos_seguimiento_json->save();
            }
            $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_confirmada_reservas', $etapa_id);
            if ($datos_seguimiento && count($id_error) == 0) {
                $datos_seguimiento->valor = 0;
                $datos_seguimiento->save();
                $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_reservas', $etapa_id);
                if ($datos_seguimiento) {
                    $datos_seguimiento->valor = "";
                    $datos_seguimiento->save();
                    //$datos_seguimiento->delete();
                }
                $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__fecha_modificacion', $etapa_id);
                if ($datos_seguimiento) {
                    $datos_seguimiento->valor = "";
                    $datos_seguimiento->save();
                    //$datos_seguimiento->delete();
                }
                $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__fechaUltimaResera', $etapa_id);
                if ($datos_seguimiento) {
                    $datos_seguimiento->valor = "";
                    $datos_seguimiento->save();
                    //$datos_seguimiento->delete();
                }

                $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '_fecha_confirmacion', $etapa_id);
                if ($datos_seguimiento) {
                    $datos_seguimiento->valor = "";
                    $datos_seguimiento->save();
                    //$datos_seguimiento->delete();
                }
            }

            $result = array();
            $result["cancelados"] = $id_cancelados;
            $result["error"] = $id_error;
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($result);
            exit();
        } catch (Exception $e) {
            $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
            fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR EXCEPTION: ' . $e->getMessage() . "\n");
            fclose($log);
            throw new Exception($e->getMessage());
        }
    }

    public function agenda_multiple_sae_api_eliminar_reserva() {
        try {
            $etapa_id = $this->input->post('etapa_id');
            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            if (!$etapa) {
                show_404();
            }
            //verifico si el usuario pertenece el grupo MesaDeEntrada y  esta actuando como un ciudadano
            if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
                //el usuario se saca desde la session con el id_usuario_ciudadano
                $usuario_sesion = Doctrine_Query::create()
                        ->from('Usuario u')
                        ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                        ->fetchOne();

                $funcionario_actuando_como_ciudadano = true;
            } else {
                //caso normal el usuario es el logueado
                $usuario_sesion = UsuarioSesion::usuario();
                $funcionario_actuando_como_ciudadano = false;
            }

            $url = $this->input->post('url');
            $token = openssl_decrypt($this->input->post('token'), 'bf-ofb', 'simple_1058');
            $data_array = array(
                "token" => $token,
                "idEmpresa" => $this->input->post('id_empresa'),
                "idAgenda" => $this->input->post('id_agenda'),
                "idRecurso" => $this->input->post('id_recurso'),
                "idReserva" => $this->input->post('id_reserva'),
                "tokenReserva" => $this->input->post('token_reserva'),
                "idioma" => $this->input->post('idioma')
            );


            $data = json_encode($data_array);

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
            );

            $result = curl_exec($curl);
            $curl_errno = curl_errno($curl);
            $curl_error = curl_error($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            if ($curl_errno > 0 || $http_code != 200) {
                $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
                fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR CURL: ' . $curl_error . ' (http code: ' . $http_code . ')' . "\n");
                fclose($log);
                throw new Exception($curl_error);
            }

            $resultado_class = json_decode($result);
            if ($resultado_class->resultado == '1') {
                $id_persona = $this->input->post('id_persona');
                $nombre_campo = $this->input->post('nombre_campo');
                $datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo . '__json_reserva', $etapa_id);
                $json_reservar = new stdClass();
                if ($datos_seguimiento) {
                    $json_reservar = $datos_seguimiento->valor;
                    if (isset($json_reservar->$id_persona)) {
                        unset($json_reservar->$id_persona);
                    }
                    $datos_seguimiento->valor = json_encode($json_reservar);
                    $datos_seguimiento->save();
                }
            }

            header('Content-type: application/json; charset=utf-8');
            echo $result;
            exit();
        } catch (Exception $e) {
            $log = fopen(__DIR__ . '/../logs/agenda_sae.log', "a");
            fwrite($log, date("Y-m-d H:i:s") . ' --> ERROR EXCEPTION: ' . $e->getMessage() . "\n");
            fclose($log);
            throw new Exception($e->getMessage());
        }
    }

    //----- TRAZABILIDAD SUBPROCESO AGENDA (EMBEBIDA Y EXTERNA)
    public function trazabilidad_sub_proceso_agenda() {
        $etapa_id = $this->input->post('etapa_id');
        $secuencia = $this->input->post('secuencia');

        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
        $paso = $etapa->getPasoEjecutable($secuencia);
        enviar_traza_sub_proceso_agenda($etapa, $paso);
    }

    //----- TRAZABILIDAD LINEA AGENDA (SOLO EXTERNA)
    public function trazabilidad_linea_agenda_externa() {
        $etapa_id = $this->input->post('etapa_id');
        $secuencia = $this->input->post('secuencia');

        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
        $paso = $etapa->getPasoEjecutable($secuencia);

        enviar_traza_agenda_externa($etapa, $paso);
    }

    public function verificar_permisos_ejecucion_grep($proceso_id) {

        if (UsuarioSesion::usuario_actuando_como_empresa()) {
            $documento_usuario_real = Doctrine::getTable('Usuario')->find(UsuarioSesion::usuario_actuando_como_empresa())->usuario;
            $rut_usuario_empresa = UsuarioSesion::usuario()->usuario;
            $lista_codigos_tramite_ws_grep = UsuarioSesion::ws_permisos_tramites_usuario_grep($documento_usuario_real, $rut_usuario_empresa);

            if ($lista_codigos_tramite_ws_grep) {
                $proceso = Doctrine_Query::create()->from('Proceso p')->where('p.id = ?', $proceso_id)->fetchOne();

                if (empty($proceso)) {
                    return false;
                }

                $tiene_permisos = false;

                for ($i = 0; $i < count($lista_codigos_tramite_ws_grep); $i++) {
                    if ($proceso->ProcesoTrazabilidad->proceso_externo_id == $lista_codigos_tramite_ws_grep[$i]) {
                        $tiene_permisos = true;
                        break;
                    }
                }

                return $tiene_permisos;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function guardar_historial_ejecuciones($etapa_id, $secuencia, $nombre_paso) {

        $usuario_real_ejecuta_tramite = false;

        if (UsuarioSesion::usuario_actuando_como_empresa()) {
            //usuario actuando como empresa
            $usuario_real_ejecuta_tramite = $this->session->userdata('usuario_actuando_como_empresa');
            $descripcion = 'UAE';
        } else if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
            //funcionario actuando como ciudadano
            $usuario_real_ejecuta_tramite = $this->session->userdata('usuario_id');
            $descripcion = 'FAC';
        }

        if ($usuario_real_ejecuta_tramite) {
            //guardo hisotrial de el usuario real que ejecuto el tramite
            try {
                $historial_ejecuciones_etapas = Doctrine::getTable('EtapaHistorialEjecuciones')->findOneByUsuarioIdAndEtapaIdAndSecuencia($usuario_real_ejecuta_tramite, $etapa_id, $secuencia);

                if (!$historial_ejecuciones_etapas) {
                    $historial_ejecuciones_etapas = new EtapaHistorialEjecuciones();
                }

                $historial_ejecuciones_etapas->etapa_id = $etapa_id;
                $historial_ejecuciones_etapas->usuario_id = $usuario_real_ejecuta_tramite;
                $historial_ejecuciones_etapas->descripcion = $descripcion;
                $historial_ejecuciones_etapas->fecha = date("Y-m-d H:i:s");
                $historial_ejecuciones_etapas->secuencia = $secuencia;
                $historial_ejecuciones_etapas->nombre_paso = $nombre_paso;
                $historial_ejecuciones_etapas->save();
            } catch (Exception $e) {
                log_message('error', $e->getMessage());
            }
        }
    }

    //calback de validaciones de campos que se setean desde la logica y no se declaran para ser usadas al usuario.
    //callback function para validacion de clausula informada.
    //Como es una validacion de un campo el callback tienen que estar en esta clase.
    public function required_clausula($str) {
        //para que se lista el mensaje en el campo es necesario que el mensaje contenga <strong>%s</strong>
        $this->form_validation->set_message('required_clausula', "Para continuar con el trámite, debe aceptar los <strong>%s</strong> de Consentimiento ");
        return (trim($str) == '') ? FALSE : TRUE;
    }

    public function exportCSV() {
        //$this->load->helper('ejecutar_etapa_helper');
        $filename = 'resumen_firma_' . date('Ymd') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");

        // $usuario_real_ejecuta_tramite = UsuarioSesion::usuario()->id;
        //$descripcion = 'FAC';
        // get data
        $doc_funcionario = json_decode(base64_decode($this->input->post('doc_funcionario')));
        $doc_organismo = json_decode(base64_decode($this->input->post('doc_organismo')));
        $doc_ambos = json_decode(base64_decode($this->input->post('doc_ambos')));

        // file creation
        $file = fopen('php://output', 'w');
        $respuesta = new stdClass();
        $header = array("Documento", "Campo", "Etapa", "Tramite", "Firmado por");
        fputcsv($file, $header);
        foreach ($doc_funcionario as $line) {
            $campo = Doctrine::getTable('Campo')->find($line->campo);
            $etapa = Doctrine::getTable('Etapa')->find($line->etapa);
            $id = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo->nombre . '__firmado', $etapa->id);
            if ($id) {
                fputcsv($file, array($line->file, $campo->nombre, $etapa->Tarea->nombre, $etapa->Tramite->id, "Funcionario"));
            }
        }
        foreach ($doc_organismo as $line) {
            $campo = Doctrine::getTable('Campo')->find($line->campo);
            $etapa = Doctrine::getTable('Etapa')->find($line->etapa);
            fputcsv($file, array($line->file, $campo->nombre, $etapa->Tarea->nombre, $etapa->Tramite->id, "Organismo"));
        }
        foreach ($doc_ambos as $line) {
            $campo = Doctrine::getTable('Campo')->find($line->campo);
            $etapa = Doctrine::getTable('Etapa')->find($line->etapa);
            $id = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo->nombre . '__firmado', $etapa->id);
            if ($id) {
                fputcsv($file, array($line->file, $campo->nombre, $etapa->Tarea->nombre, $etapa->Tramite->id, "Funcionario y Organismo"));
            } else {
                fputcsv($file, array($line->file, $campo->nombre, $etapa->Tarea->nombre, $etapa->Tramite->id, "Organismo"));
            }
        }

        if (!isset($respuesta->error)) {
            foreach ($doc_organismo as $value) {
                $etapa = Doctrine::getTable('Etapa')->find($value->etapa);
                if ($etapa->pendiente) {
                    $secuencia = 0;
                    $paso = $etapa->getPasoEjecutable($secuencia);
                    $formulario = $paso->Formulario;
                    $this->traza_etapas_pasos($etapa, $formulario, $secuencia);
                    $this->guardar_historial_ejecuciones($etapa->id, $secuencia, $paso->nombre);
                    while ($paso) {
                        if (end($etapa->getPasosEjecutables()) == $paso) {
                            $etapa->iniciarPaso($paso, $secuencia);
                            $etapa->finalizarPaso($paso, $secuencia);
                            $etapa->avanzar();
                            enviar_traza_cierre_automatico($etapa, $secuencia);
                        } else {
                            $etapa->iniciarPaso($paso, $secuencia);
                            enviar_traza_linea_paso_con_cierre_automatico($etapa, $secuencia);
                            $etapa->finalizarPaso($paso, $secuencia);
                        }
                        $secuencia++;
                        $paso = $etapa->getPasoEjecutable($secuencia);
                    }
                }
            }
            foreach ($doc_ambos as $value) {
                $etapa = Doctrine::getTable('Etapa')->find($value->etapa);
                if ($etapa->pendiente) {
                    $secuencia = 0;
                    $paso = $etapa->getPasoEjecutable($secuencia);
                    $formulario = $paso->Formulario;
                    $this->traza_etapas_pasos($etapa, $formulario, $secuencia);
                    $this->guardar_historial_ejecuciones($etapa->id, $secuencia, $paso->nombre);
                    while ($paso) {
                        if (end($etapa->getPasosEjecutables()) == $paso) {
                            $etapa->iniciarPaso($paso, $secuencia);
                            $etapa->finalizarPaso($paso, $secuencia);
                            $etapa->avanzar();
                            enviar_traza_cierre_automatico($etapa, $secuencia);
                        } else {
                            $etapa->iniciarPaso($paso, $secuencia);
                            enviar_traza_linea_paso_con_cierre_automatico($etapa, $secuencia);
                            $etapa->finalizarPaso($paso, $secuencia);
                        }
                        $secuencia++;
                        $paso = $etapa->getPasoEjecutable($secuencia);
                    }
                }
            }
            foreach ($doc_funcionario as $value) {
                $etapa = Doctrine::getTable('Etapa')->find($value->etapa);
                if ($etapa->pendiente) {
                    $secuencia = 0;
                    $paso = $etapa->getPasoEjecutable($secuencia);
                    $formulario = $paso->Formulario;
                    $this->traza_etapas_pasos($etapa, $formulario, $secuencia);
                    $this->guardar_historial_ejecuciones($etapa->id, $secuencia, $paso->nombre);
                    while ($paso) {
                        if (end($etapa->getPasosEjecutables()) == $paso) {
                            $etapa->iniciarPaso($paso, $secuencia);
                            $etapa->finalizarPaso($paso, $secuencia);
                            $etapa->avanzar();
                            enviar_traza_cierre_automatico($etapa, $secuencia);
                        } else {
                            $etapa->iniciarPaso($paso, $secuencia);
                            enviar_traza_linea_paso_con_cierre_automatico($etapa, $secuencia);
                            $etapa->finalizarPaso($paso, $secuencia);
                        }
                        $secuencia++;
                        $paso = $etapa->getPasoEjecutable($secuencia);
                    }
                }
            }
        }
        echo stream_get_contents($file);
        fclose($file);
        exit;
    }

    public function traza_etapas_pasos($etapa, $formulario, $secuencia) {
        // -- Se genera el ID de transaccion correspondiente para trazabilidad.
        $id_transaccion = str_replace(" ", "_", strtoupper($formulario->Proceso->ProcesoTrazabilidad->organismo_id)) . ':' . str_replace(" ", "_", strtoupper($formulario->Proceso->ProcesoTrazabilidad->proceso_externo_id)) . ':' . $etapa->tramite_id;

        // -- Genera variable con el ID de transaccion
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_transaccion_traza', $etapa->id);
        if (!$dato)
            $dato = new DatoSeguimiento();

        $dato->etapa_id = $etapa->id;
        $dato->nombre = 'id_transaccion_traza';
        $dato->valor = (string) $id_transaccion;
        $dato->save();

        //--- TRAZABILIDAD
        enviar_traza_inicio_tarea($etapa, $secuencia);

        // -- Genera variable con el ID de estado para traza (idEstadoTrazabilidad)
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('idEstadoTrazabilidad', $etapa->id);
        if (!$dato) {
            $dato = new DatoSeguimiento();
            $dato->etapa_id = $etapa->id;
            $dato->nombre = 'idEstadoTrazabilidad';
            $dato->valor = (string) $etapa->Tarea->trazabilidad_estado;
            $dato->save();
        }
    }

}

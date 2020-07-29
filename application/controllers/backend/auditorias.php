<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Auditorias extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if (!UsuarioBackendSesion::has_rol('super') && !(UsuarioBackendSesion::has_rol('seguimiento') && UsuarioBackendSesion::isAuditor())) {
            redirect('backend');
        }
    }

    public function usuarios() {
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudUsuario x')
                ->where('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudUsuario au GROUP BY au.id)')
                ->andWhere('x.cuenta_id= ?', $cuenta_id);

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $data['usuarios'] = $reporte;
        $data['title'] = 'Auditoría Usuarios Frontend';
        $data['content'] = 'backend/auditorias/usuarios';

        $this->load->view('backend/template', $data);
    }

    public function usuario_auditar($usuario_id) {
        if (!$usuario_id) {
            show_404();
        }
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudUsuario x')
                ->where('x.id= ?', $usuario_id)
                ->andWhere('x.cuenta_id= ?', $cuenta_id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $data['usuarios'] = json_decode(json_encode($reporte), FALSE);
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/usuario_auditar';

        $this->load->view('backend/template', $data);
    }

    public function backend() {
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudUsuarioBackend x')
                ->where('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudUsuarioBackend au GROUP BY au.id)')
                ->andWhere('x.cuenta_id= ?', $cuenta_id);

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $data['usuarios'] = $reporte;
        $data['title'] = 'Auditoría Usuarios Backend';
        $data['content'] = 'backend/auditorias/backend';

        $this->load->view('backend/template', $data);
    }

    public function backend_auditar($usuario_id) {
        if (!$usuario_id) {
            show_404();
        }
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudUsuarioBackend x')
                ->where('x.id= ?', $usuario_id)
                ->andWhere('x.cuenta_id= ?', $cuenta_id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();


        $data['usuarios'] = json_decode(json_encode($reporte), FALSE);
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/backend_auditar';

        $this->load->view('backend/template', $data);
    }

    public function grupos() {
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudGrupoUsuarios x')
                ->where('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudGrupoUsuarios au GROUP BY au.id)')
                ->andWhere('x.cuenta_id= ?', $cuenta_id);

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $data['grupos'] = $reporte;
        $data['title'] = 'Auditoría Grupos de Usuarios';
        $data['content'] = 'backend/auditorias/grupos';

        $this->load->view('backend/template', $data);
    }

    public function grupos_auditar($usuario_id) {
        if (!$usuario_id) {
            show_404();
        }
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));

        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudGrupoUsuarios x')
                ->where('x.id= ?', $usuario_id)
                ->andWhere('x.cuenta_id= ?', $cuenta_id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $data['grupos'] = json_decode(json_encode($reporte), FALSE);
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/grupos_auditar';

        $this->load->view('backend/template', $data);
    }

    public function reportes() {
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudReporte x')
                ->where('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudReporte au GROUP BY au.id)');

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $data['lista'] = json_decode(json_encode($reporte), FALSE);
        $data['title'] = 'Auditoría Reportes';
        $data['content'] = 'backend/auditorias/reportes';

        $this->load->view('backend/template', $data);
    }

    public function reportes_auditar($id) {
        if (!$id) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudReporte x')
                ->where('x.id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $data['lista'] = json_decode(json_encode($reporte), FALSE);
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/reportes_auditar';

        $this->load->view('backend/template', $data);
    }

    public function pasarelas() {
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudPasarelaPago x')
                ->where('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudPasarelaPago au GROUP BY au.id)')
                ->andWhere('x.cuenta_id= ?', $cuenta_id);

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $data['lista'] = json_decode(json_encode($reporte), FALSE);
        $data['title'] = 'Auditoría Pasarelas de Pagos';
        $data['content'] = 'backend/auditorias/pasarelas';

        $this->load->view('backend/template', $data);
    }

    public function pasarelas_auditar($id) {
        if (!$id) {
            show_404();
        }
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudPasarelaPago x')
                ->where('x.id= ?', $id)
                ->andWhere('x.cuenta_id= ?', $cuenta_id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $rep = json_decode(json_encode($reporte), FALSE);
        foreach ($rep as $value) {
            if ($value->metodo == "antel") {
                $query = Doctrine_Query::create()
                        ->from('AudPasarelaPagoAntel pa')
                        ->where('pa.pasarela_pago_id = ?', $value->id_aud)
                        ->orderBy('id_aud desc')
                        ->execute();
                $r = json_encode($query->toArray());
            } else {
                $query = Doctrine_Query::create()
                        ->from('AudPasarelaPagoGenerica pg')
                        ->where('pg.pasarela_pago_id = ?', $value->id_aud)
                        ->orderBy('id_aud desc')
                        ->execute();
                $r = json_encode($query->toArray());
            }
            $value->pasarela = ($r);
        }
        $data['lista'] = $rep;
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/pasarelas_auditar';

        $this->load->view('backend/template', $data);
    }

    public function bloques() {

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudBloque x')
                ->where('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudBloque au GROUP BY au.id)');

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $rep = json_decode(json_encode($reporte), FALSE);
        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudFormulario f')
                    ->where('f.bloque_id=?', $value->id_aud)
                    ->orderBy('f.id_aud desc')
                    ->execute();
            $r = ($query->toArray());

            $value->formulario = json_decode(json_encode($r[0]), FALSE);
        }

        $data['lista'] = $rep;
        $data['title'] = 'Auditoría Catálos de Bloques';
        $data['content'] = 'backend/auditorias/bloques';

        $this->load->view('backend/template', $data);
    }

    public function bloques_auditar($id) {
        if (!$id) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudBloque x')
                ->where('x.id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $rep = json_decode(json_encode($reporte), FALSE);
        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudFormulario f')
                    ->where('f.bloque_id=?', $value->id_aud)
                    ->orderBy('f.id_aud desc')
                    ->execute();
            $r = ($query->toArray());

            $value->formulario = json_decode(json_encode($r[0]), FALSE);
        }
        foreach ($rep as $value) {
            $for = $value->formulario;
            $query = Doctrine_Query::create()
                    ->from('AudCampo c')
                    ->where('c.formulario_id = ?', $for->id_aud)
                    ->orderBy('id_aud desc')
                    ->execute();
            $r = $query->toArray();
            $value->formulario->campos = (count($r));
        }
        $data['lista'] = $rep;
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/bloques_auditar';

        $this->load->view('backend/template', $data);
    }

    public function campos_auditar($id) {
        if (!$id) {
            show_404();
        }
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudCampo x')
                ->where('x.formulario_id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $rep = json_decode(json_encode($reporte), FALSE);


        $query = Doctrine_Query::create()
                ->from('AudFormulario x')
                ->where('x.id_aud= ?', $id)
                ->orderBy('x.fecha_aud DESC');
        $query->execute();
        $form = $query->fetchArray();
        $formulario = json_decode(json_encode($form), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudBloque x')
                ->where('x.id_aud= ?', $formulario[0]->bloque_id)
                ->orderBy('x.fecha_aud DESC');
        $query->execute();
        $b = $query->fetchArray();
        $bloque = json_decode(json_encode($b), FALSE);

        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudCampo c')
                    ->where('c.id_aud=?', $value->id_aud)
                    ->orderBy('c.id_aud desc')
                    ->execute();
            $r = ($query->toArray());

            $value->campo = json_encode($r[0]);
        }
        $data['lista'] = $rep;
        $data['bloque'] = $bloque[0]->id;
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/campos_auditar';

        $this->load->view('backend/template', $data);
    }

    public function servicios() {
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudWsCatalogo x')
                ->where('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudWsCatalogo au GROUP BY au.id)');

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $rep = json_decode(json_encode($reporte), FALSE);

        $data['lista'] = $rep;
        $data['title'] = 'Auditoría Catálogos de Servicios';
        $data['content'] = 'backend/auditorias/catalogos';

        $this->load->view('backend/template', $data);
    }

    public function servicios_auditar($id) {
        if (!$id) {
            show_404();
        }
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudWsCatalogo x')
                ->where('x.id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $rep = json_decode(json_encode($reporte), FALSE);
        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudWsCatalogo ws')
                    ->where('ws.id_aud=?', $value->id_aud)
                    ->orderBy('ws.id_aud desc')
                    ->execute();
            $r = ($query->toArray());

            $value->ws = json_encode($r[0]);
        }
        $data['lista'] = $rep;
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/catalogos_auditar';

        $this->load->view('backend/template', $data);
    }

    public function operaciones($id, $id_c) {
        if (!$id || !$id_c) {
            show_404();
        }
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudWsOperacion x')
                ->where('x.catalogo_id =?', $id)
                ->andWhere('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudWsOperacion au WHERE au.catalogo_id = ? GROUP BY au.id)', $id);

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $rep = json_decode(json_encode($reporte), FALSE);
        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudWsOperacionRespuesta ws')
                    ->where('ws.operacion_id=?', $value->id_aud)
                    ->orderBy('ws.id_aud desc')
                    ->execute();
            $r = ($query->toArray());

            $value->detalle = json_encode($r);
        }
        $data['lista'] = $rep;
        $data['servicio'] = $id_c;
        $data['title'] = 'Auditoría Operaciones del servicio';
        $data['content'] = 'backend/auditorias/operaciones';

        $this->load->view('backend/template', $data);
    }

    public function procesos() {
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.nombre!= "BLOQUE" and p.id_aud IN (SELECT MAX(au.id_aud) FROM AudProceso au GROUP BY au.id)');

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('p.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('p.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('p.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('p.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(p.fecha_aud) DESC , p.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        $data['lista'] = $rep;
        $data['title'] = 'Auditoría Procesos';
        $data['content'] = 'backend/auditorias/procesos';

        $this->load->view('backend/template', $data);
    }

    public function procesos_auditar($id) {
        if (!$id) {
            show_404();
        }
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudProceso x')
                ->where('x.id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $rep = json_decode(json_encode($reporte), FALSE);
        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudProceso p')
                    ->where('p.id_aud=?', $value->id_aud)
                    ->orderBy('DATE(p.fecha_aud) DESC , p.id_aud DESC')
                    ->execute();
            $r = ($query->toArray());

            $value->proceso = json_encode($r[0]);
        }
        $data['lista'] = $rep;
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/procesos_auditar';

        $this->load->view('backend/template', $data);
    }

    public function tareas($proceso) {
        if (!$proceso) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudTarea x')
                ->where('x.proceso_id= ?', $proceso)
                ->andWhere('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudTarea au WHERE au.proceso_id = ? GROUP BY au.id)', $proceso);

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
//        print_r($query->getSqlQuery());
//        show_error(2);
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $proceso);
        $query->orderBy('DATE(p.fecha_aud) DESC , p.id_aud DESC')
                ->execute();
        $p = $query->fetchArray();

        $pro = json_decode(json_encode($p), FALSE);
        $data['lista'] = $rep;
        $data['proceso'] = $pro[0]->id;
        $data['title'] = 'Auditoría Procesos';
        $data['content'] = 'backend/auditorias/tareas';

        $this->load->view('backend/template', $data);
    }

    public function tareas_auditar($id) {
        if (!$id) {
            show_404();
        }
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudTarea x')
                ->where('x.id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudTarea a')
                    ->where('a.id_aud=?', $value->id_aud)
                    ->orderBy('a.id_aud desc')
                    ->execute();
            $r = ($query->toArray());

            $value->tarea = json_encode($r[0]);
        }

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $rep[0]->proceso_id);
        $query->orderBy('DATE(p.fecha_aud) DESC , p.id_aud DESC')
                ->execute();
        $p = $query->fetchArray();

        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0]->id;
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/tareas_auditar';

        $this->load->view('backend/template', $data);
    }

    public function formularios($proceso) {
        if (!$proceso) {
            show_404();
        }
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudFormulario x')
                ->where('x.proceso_id= ?', $proceso)
                ->andWhere('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudFormulario au WHERE au.proceso_id = ? GROUP BY au.id)', $proceso);

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $proceso);
        $query->orderBy('DATE(p.fecha_aud) DESC , p.id_aud DESC')
                ->execute();
        $p = $query->fetchArray();
        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0]->id;
        $data['title'] = 'Auditoría Procesos';
        $data['content'] = 'backend/auditorias/formularios';

        $this->load->view('backend/template', $data);
    }

    public function formularios_auditar($id) {
        if (!$id) {
            show_404();
        }
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudFormulario x')
                ->where('x.id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudFormulario a')
                    ->where('a.id_aud=?', $value->id_aud)
                    ->orderBy('a.id_aud desc')
                    ->execute();
            $r = ($query->toArray());

            $value->json = json_encode($r[0]);
        }

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $rep[0]->proceso_id);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $p = $query->fetchArray();
        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0]->id;
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/formularios_auditar';

        $this->load->view('backend/template', $data);
    }

    public function documentos($proceso) {
        if (!$proceso) {
            show_404();
        }
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudDocumento x')
                ->where('x.proceso_id= ?', $proceso)
                ->andWhere('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudDocumento au WHERE au.proceso_id = ? GROUP BY au.id)', $proceso);

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $proceso);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $p = $query->fetchArray();
        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0]->id;
        $data['title'] = 'Auditoría Procesos';
        $data['content'] = 'backend/auditorias/documentos';

        $this->load->view('backend/template', $data);
    }

    public function documentos_auditar($id) {
        if (!$id) {
            show_404();
        }
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');


        $query = Doctrine_Query::create()
                ->from('AudDocumento x')
                ->where('x.id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudDocumento a')
                    ->where('a.id_aud=?', $value->id_aud)
                    ->orderBy('a.id_aud desc')
                    ->execute();
            $r = ($query->toArray());

            $value->json = json_encode($r[0]);
        }

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $rep[0]->proceso_id);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $p = $query->fetchArray();
        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0]->id;
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/documentos_auditar';

        $this->load->view('backend/template', $data);
    }

    public function validaciones($proceso) {
        if (!$proceso) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudValidacion x')
                ->where('x.proceso_id= ?', $proceso)
                ->andWhere('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudValidacion au WHERE au.proceso_id = ? GROUP BY au.id)', $proceso);

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $proceso);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $p = $query->fetchArray();
        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0]->id;
        $data['title'] = 'Auditoría Procesos';
        $data['content'] = 'backend/auditorias/validaciones';

        $this->load->view('backend/template', $data);
    }

    public function validaciones_auditar($id) {
        if (!$id) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudValidacion x')
                ->where('x.id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudValidacion a')
                    ->where('a.id_aud=?', $value->id_aud)
                    ->orderBy('a.id_aud desc')
                    ->execute();
            $r = ($query->toArray());

            $value->json = json_encode($r[0]);
        }

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $rep[0]->proceso_id);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $p = $query->fetchArray();
        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0]->id;
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/validaciones_auditar';

        $this->load->view('backend/template', $data);
    }

    public function acciones($proceso) {
        if (!$proceso) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudAccion x')
                ->where('x.proceso_id= ?', $proceso)
                ->andWhere('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudAccion au  WHERE au.proceso_id = ? GROUP BY au.id)', $proceso);

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $proceso);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $p = $query->fetchArray();
        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0]->id;
        $data['title'] = 'Auditoría Procesos';
        $data['content'] = 'backend/auditorias/acciones';

        $this->load->view('backend/template', $data);
    }

    public function acciones_auditar($id) {
        if (!$id) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudAccion x')
                ->where('x.id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        foreach ($rep as $value) {
            IF ($value->tipo == "pasarela_pago") {
                $extra = json_decode($value->extra);
                if (!isset($extra->metodo)) {
                    $query = Doctrine_Query::create()
                            ->from('AudEventoPago a')
                            ->where('a.accion_id=?', $value->id)
                            ->orderBy('a.id_aud desc')
                            ->execute();
                    $ep = ($query->toArray());
                    $value->evento_pago = json_encode($ep);
                }
            }
            $query = Doctrine_Query::create()
                    ->from('AudAccion a')
                    ->where('a.id_aud=?', $value->id_aud)
                    ->orderBy('a.id_aud desc')
                    ->execute();
            $r = ($query->toArray());

            $value->json = json_encode($r[0]);
        }

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $rep[0]->proceso_id);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $p = $query->fetchArray();
        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0]->id;
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/acciones_auditar';

        $this->load->view('backend/template', $data);
    }

    public function form_campos($formulario) {
        if (!$formulario) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudCampo x')
                ->where('x.formulario_id= ?', $formulario)
                ->andWhere('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudCampo au WHERE au.formulario_id = ? GROUP BY au.id)', $formulario);

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudFormulario p')
                ->where('p.id_aud=?', $formulario);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $f = $query->fetchArray();
        $form = json_decode(json_encode($f), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $form[0]->proceso_id);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $p = $query->fetchArray();
        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0]->id;
        $data['formulario'] = $form[0]->id;
        $data['title'] = 'Auditoría Procesos';
        $data['content'] = 'backend/auditorias/form_campos';

        $this->load->view('backend/template', $data);
    }

    public function form_campos_auditar($id) {
        if (!$id) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudCampo x')
                ->where('x.id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudCampo a')
                    ->where('a.id_aud=?', $value->id_aud)
                    ->orderBy('a.id_aud desc')
                    ->execute();
            $r = ($query->toArray());

            $value->json = json_encode($r[0]);
        }

        $query = Doctrine_Query::create()
                ->from('AudFormulario p')
                ->where('p.id_aud=?', $rep[0]->formulario_id);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $f = $query->fetchArray();
        $form = json_decode(json_encode($f), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $form[0]->proceso_id);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $p = $query->fetchArray();
        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0];
        $data['formulario'] = $form[0];
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/form_campos_auditar';

        $this->load->view('backend/template', $data);
    }

    public function pasos($tarea) {
        if (!$tarea) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudPaso x')
                ->where('x.tarea_id= ?', $tarea)
                ->andWhere('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudPaso au  WHERE au.tarea_id = ? GROUP BY au.id)', $tarea);

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudTarea p')
                ->where('p.id_aud=?', $tarea);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $t = $query->fetchArray();
        $tar = json_decode(json_encode($t), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $tar[0]->proceso_id);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $p = $query->fetchArray();
        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0];
        $data['tarea'] = $tar[0];
        $data['title'] = 'Auditoría Procesos';
        $data['content'] = 'backend/auditorias/pasos';
        $this->load->view('backend/template', $data);
    }

    public function pasos_auditar($id) {
        if (!$id) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudPaso x')
                ->where('x.id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudPaso a')
                    ->where('a.id_aud=?', $value->id_aud)
                    ->orderBy('a.id_aud desc')
                    ->execute();
            $r = ($query->toArray());

            $value->json = json_encode($r[0]);
        }

        $query = Doctrine_Query::create()
                ->from('AudTarea p')
                ->where('p.id_aud=?', $rep[0]->tarea_id);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $t = $query->fetchArray();
        $tar = json_decode(json_encode($t), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $tar[0]->proceso_id);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $p = $query->fetchArray();
        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0];
        $data['tarea'] = $tar[0];
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/pasos_auditar';

        $this->load->view('backend/template', $data);
    }

    public function eventos($tarea) {
        if (!$tarea) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudEvento x')
                ->where('x.tarea_id= ?', $tarea)
                ->andWhere('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudEvento au WHERE au.tarea_id = ? GROUP BY au.id)', $tarea);

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudTarea p')
                ->where('p.id_aud=?', $tarea);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $t = $query->fetchArray();
        $tar = json_decode(json_encode($t), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $tar[0]->proceso_id);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $p = $query->fetchArray();
        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0];
        $data['tarea'] = $tar[0];
        $data['title'] = 'Auditoría Procesos';
        $data['content'] = 'backend/auditorias/eventos';
        $this->load->view('backend/template', $data);
    }

    public function eventos_auditar($id) {
        if (!$id) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudEvento x')
                ->where('x.id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudEvento a')
                    ->where('a.id_aud=?', $value->id_aud)
                    ->orderBy('a.id_aud desc')
                    ->execute();
            $r = ($query->toArray());

            $value->json = json_encode($r[0]);
        }

        $query = Doctrine_Query::create()
                ->from('AudTarea p')
                ->where('p.id_aud=?', $rep[0]->tarea_id);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $t = $query->fetchArray();
        $tar = json_decode(json_encode($t), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $tar[0]->proceso_id);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $p = $query->fetchArray();
        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0];
        $data['tarea'] = $tar[0];
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/eventos_auditar';

        $this->load->view('backend/template', $data);
    }

    public function validacionesjs($tarea) {
        if (!$tarea) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudEjecutarValidacion x')
                ->where('x.tarea_id= ?', $tarea)
                ->andWhere('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudEjecutarValidacion au WHERE au.tarea_id = ? GROUP BY au.id)', $tarea);

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudTarea p')
                ->where('p.id_aud=?', $tarea);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $t = $query->fetchArray();
        $tar = json_decode(json_encode($t), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $tar[0]->proceso_id);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $p = $query->fetchArray();
        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0];
        $data['tarea'] = $tar[0];
        $data['title'] = 'Auditoría Procesos';
        $data['content'] = 'backend/auditorias/validacionesjs';
        $this->load->view('backend/template', $data);
    }

    public function validacionesjs_auditar($id) {
        if (!$id) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudEjecutarValidacion x')
                ->where('x.id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudEjecutarValidacion a')
                    ->where('a.id_aud=?', $value->id_aud)
                    ->orderBy('a.id_aud desc')
                    ->execute();
            $r = ($query->toArray());

            $value->json = json_encode($r[0]);
        }

        $query = Doctrine_Query::create()
                ->from('AudTarea p')
                ->where('p.id_aud=?', $rep[0]->tarea_id);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $t = $query->fetchArray();
        $tar = json_decode(json_encode($t), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudProceso p')
                ->where('p.id_aud=?', $tar[0]->proceso_id);
        $query->orderBy('fecha_aud DESC')
                ->execute();
        $p = $query->fetchArray();
        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['proceso'] = $pro[0];
        $data['tarea'] = $tar[0];
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/validacionesjs_auditar';

        $this->load->view('backend/template', $data);
    }

    public function obnstr() {
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudObnStructure obn')
                ->where('obn.id_aud IN (SELECT MAX(au.id_aud) FROM AudObnStructure au GROUP BY au.id)');

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('obn.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('obn.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('obn.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('obn.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(obn.fecha_aud) DESC , obn.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        $data['lista'] = $rep;
        $data['title'] = 'Auditoría Estructura Obn';
        $data['content'] = 'backend/auditorias/obns';

        $this->load->view('backend/template', $data);
    }

    public function obnstr_auditar($id) {
        if (!$id) {
            show_404();
        }
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudObnStructure x')
                ->where('x.id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();

        $rep = json_decode(json_encode($reporte), FALSE);
        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudObnStructure p')
                    ->where('p.id_aud=?', $value->id_aud)
                    ->orderBy('DATE(p.fecha_aud) DESC , p.id_aud DESC')
                    ->execute();
            $r = ($query->toArray());
            $r[0]['json'] = json_decode($r[0]['json']);

            $value->obn = json_encode($r[0]);
        }
        $data['lista'] = $rep;
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/obns_auditar';

        $this->load->view('backend/template', $data);
    }

    public function obnattr($obn) {
        if (!$obn) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudObnAttributes x')
                ->where('x.id_obn= ?', $obn)
                ->andWhere('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudObnAttributes au WHERE au.id_obn = ? GROUP BY au.id)', $obn);

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudObnStructure p')
                ->where('p.id_aud=?', $obn);
        $query->orderBy('DATE(p.fecha_aud) DESC , p.id_aud DESC')
                ->execute();
        $p = $query->fetchArray();

        $obn = json_decode(json_encode($p), FALSE);
        $data['lista'] = $rep;
        $data['obn'] = $obn[0]->id;
        $data['title'] = 'Auditoría Obn';
        $data['content'] = 'backend/auditorias/obnattr';

        $this->load->view('backend/template', $data);
    }

    public function obnattr_auditar($id) {
        if (!$id) {
            show_404();
        }
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudObnAttributes x')
                ->where('x.id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudObnAttributes a')
                    ->where('a.id_aud=?', $value->id_aud)
                    ->orderBy('a.id_aud desc')
                    ->execute();
            $r = ($query->toArray());

            $value->attributo = json_encode($r[0]);
        }

        $query = Doctrine_Query::create()
                ->from('AudObnStructure p')
                ->where('p.id_aud=?', $rep[0]->id_obn);
        $query->orderBy('DATE(p.fecha_aud) DESC , p.id_aud DESC')
                ->execute();
        $p = $query->fetchArray();

        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['obn'] = $pro[0]->id;
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/obnattr_auditar';

        $this->load->view('backend/template', $data);
    }
    
    public function obnquery($obn) {
        if (!$obn) {
            show_404();
        }

        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudObnQueries x')
                ->where('x.id_obn= ?', $obn)
                ->andWhere('x.id_aud IN (SELECT MAX(au.id_aud) FROM AudObnQueries au WHERE au.id_obn = ? GROUP BY au.id)', $obn);

        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        $query = Doctrine_Query::create()
                ->from('AudObnStructure p')
                ->where('p.id_aud=?', $obn);
        $query->orderBy('DATE(p.fecha_aud) DESC , p.id_aud DESC')
                ->execute();
        $p = $query->fetchArray();

        $obn = json_decode(json_encode($p), FALSE);
        $data['lista'] = $rep;
        $data['obn'] = $obn[0]->id;
        $data['title'] = 'Auditoría Obn';
        $data['content'] = 'backend/auditorias/obnquery';

        $this->load->view('backend/template', $data);
    }
    
    public function obnquery_auditar($id) {
        if (!$id) {
            show_404();
        }
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $desde = date("Y-m-d 0:0:0", strtotime($this->input->post('desde')));
        $hasta = date("Y-m-d 23:59:59", strtotime($this->input->post('hasta')));
        $operacion = $this->input->post('operacion');

        $query = Doctrine_Query::create()
                ->from('AudObnQueries x')
                ->where('x.id= ?', $id);
        $query->execute();
        if (!$query->fetchArray()) {
            show_404();
        }
        if ($this->input->post('desde') && $this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud BETWEEN ? AND ?', array($desde, $hasta));
        } else if ($this->input->post('desde')) {
            $query->andWhere('x.fecha_aud >= ? ', $desde);
        } else if ($this->input->post('hasta')) {
            $query->andWhere('x.fecha_aud <= ? ', $hasta);
        }
        if ($this->input->post('operacion')) {
            $query->andWhere('x.tipo_operacion_aud = ?', $operacion);
        }
        $query->orderBy('DATE(x.fecha_aud) DESC , x.id_aud DESC')
                ->execute();
        $reporte = $query->fetchArray();
        $rep = json_decode(json_encode($reporte), FALSE);

        foreach ($rep as $value) {
            $query = Doctrine_Query::create()
                    ->from('AudObnQueries a')
                    ->where('a.id_aud=?', $value->id_aud)
                    ->orderBy('a.id_aud desc')
                    ->execute();
            $r = ($query->toArray());

            $value->query = json_encode($r[0]);
        }

        $query = Doctrine_Query::create()
                ->from('AudObnStructure p')
                ->where('p.id_aud=?', $rep[0]->id_obn);
        $query->orderBy('DATE(p.fecha_aud) DESC , p.id_aud DESC')
                ->execute();
        $p = $query->fetchArray();

        $pro = json_decode(json_encode($p), FALSE);

        $data['lista'] = $rep;
        $data['obn'] = $pro[0]->id;
        $data['title'] = 'Auditoría';
        $data['content'] = 'backend/auditorias/obnquery_auditar';

        $this->load->view('backend/template', $data);
    }

}

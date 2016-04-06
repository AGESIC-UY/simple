<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Procesos extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if(UsuarioBackendSesion::usuario()->rol!='super' && UsuarioBackendSesion::usuario()->rol!='modelamiento'){
            echo 'No tiene permisos para acceder a esta seccion.';
            exit;
        }
    }

    public function index() {
        $data['procesos'] = Doctrine_Query::create()
                ->from('Proceso p, p.Cuenta c')
                ->where('c.id = ?', UsuarioBackendSesion::usuario()->cuenta_id)
                ->where('p.nombre != ?', 'BLOQUE')
                ->orderBy('p.nombre asc')
                ->execute();

        $data['title'] = 'Listado de Procesos';
        $data['content'] = 'backend/procesos/index';
        $this->load->view('backend/template', $data);
    }

    public function crear(){
        $proceso=new Proceso();
        $proceso->nombre='Proceso';
        $proceso->cuenta_id=UsuarioBackendSesion::usuario()->cuenta_id;
        $proceso->save();

        $proceso_trazabilidad=new ProcesoTrazabilidad();
        $proceso_trazabilidad->proceso_id = $proceso->id;
        $proceso_trazabilidad->organismo_id = 'Organismo ID';
        $proceso_trazabilidad->proceso_externo_id = 'Proceso ID';
        $proceso_trazabilidad->save();

        redirect('backend/procesos/editar/'.$proceso->id);
    }

    public function eliminar($proceso_id){
        $proceso=Doctrine::getTable('Proceso')->find($proceso_id);

        if($proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para eliminar este proceso';
            exit;
        }

        $proceso->delete();

        redirect('backend/procesos/index/');
    }

    public function editar($proceso_id) {
        $proceso=Doctrine::getTable('Proceso')->find($proceso_id);

        if($proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para editar este proceso';
            exit;
        }

        $data['proceso'] = $proceso;

        $data['title'] = 'Modelador';
        $data['content'] = 'backend/procesos/editar';
        $this->load->view('backend/template', $data);
    }

    public function ajax_editar($proceso_id){
        $proceso=Doctrine::getTable('Proceso')->find($proceso_id);

        if($proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para editar este proceso';
            exit;
        }

        $proceso->width = str_replace('%', '', $proceso->width);
        $proceso->height = str_replace('%', '', $proceso->height);

        $data['proceso']=$proceso;

        $this->load->view('backend/procesos/ajax_editar',$data);
    }

    public function editar_form($proceso_id){
        $proceso=Doctrine::getTable('Proceso')->find($proceso_id);

        if($proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para editar este proceso';
            exit;
        }

        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('width', 'Width', 'numeric');
        $this->form_validation->set_rules('height', 'Height', 'numeric');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $proceso->nombre=$this->input->post('nombre');
            $proceso->width=$this->input->post('width') . '%';
            $proceso->height=$this->input->post('height') . '%';
            $proceso->save();

            $respuesta->validacion=TRUE;
            $respuesta->redirect=site_url('backend/procesos/editar/'.$proceso->id);

        }else{
            $respuesta->validacion=FALSE;
            $respuesta->errores=validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function ajax_crear_tarea($proceso_id,$tarea_identificador){
        $proceso=Doctrine::getTable('Proceso')->find($proceso_id);

        if($proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para crear esta tarea.';
            exit;
        }

        $tarea=new Tarea();
        $tarea->proceso_id=$proceso->id;
        $tarea->identificador=$tarea_identificador;
        $tarea->nombre=$this->input->post('nombre');
        $tarea->posx=$this->input->post('posx');
        $tarea->posy=$this->input->post('posy');
        $tarea->save();

    }

    public function ajax_editar_tarea($proceso_id,$tarea_identificador){
        $tarea=Doctrine::getTable('Tarea')->findOneByProcesoIdAndIdentificador($proceso_id,$tarea_identificador);

        if($tarea->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para editar esta tarea.';
            exit;
        }

        $data['tarea'] = $tarea;
        $data['formularios']=Doctrine::getTable('Formulario')->findByProcesoId($proceso_id);
        $data['acciones']=Doctrine::getTable('Accion')->findByProcesoId($proceso_id);

        $this->load->view('backend/procesos/ajax_editar_tarea',$data);
    }

    public function editar_tarea_form($tarea_id){
        $tarea=Doctrine::getTable('Tarea')->find($tarea_id);

        if($tarea->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para editar esta tarea.';
            exit;
        }

        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        if($this->input->post('vencimiento')){
            $this->form_validation->set_rules('vencimiento_valor','Valor de Vencimiento','required|is_natural_no_zero');
            if($this->input->post('vencimiento_notificar')){
                $this->form_validation->set_rules('vencimiento_notificar_dias','Días para notificar vencimiento','required|is_natural_no_zero');
                $this->form_validation->set_rules('vencimiento_notificar_email','Correo electronico para notificar vencimiento','required');
            }
        }

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $tarea->nombre=$this->input->post('nombre');
            $tarea->inicial=$this->input->post('inicial');
            $tarea->final=$this->input->post('final');
            $tarea->asignacion=$this->input->post('asignacion');
            $tarea->asignacion_usuario=$this->input->post('asignacion_usuario');
            $tarea->asignacion_notificar=$this->input->post('asignacion_notificar');
            $tarea->setGruposUsuariosFromArray($this->input->post('grupos_usuarios'));
            $tarea->setPasosFromArray($this->input->post('pasos',false));
            $tarea->paso_confirmacion=$this->input->post('paso_confirmacion');
            $tarea->setEventosFromArray($this->input->post('eventos',false));
            $tarea->almacenar_usuario=$this->input->post('almacenar_usuario');
            $tarea->almacenar_usuario_variable=$this->input->post('almacenar_usuario_variable');
            $tarea->acceso_modo=$this->input->post('acceso_modo');
            $tarea->activacion=$this->input->post('activacion');
            $tarea->activacion_inicio=strtotime($this->input->post('activacion_inicio'));
            $tarea->activacion_fin=strtotime($this->input->post('activacion_fin'));
            $tarea->vencimiento=$this->input->post('vencimiento');
            $tarea->vencimiento_valor=$this->input->post('vencimiento_valor');
            $tarea->vencimiento_unidad=$this->input->post('vencimiento_unidad');
            $tarea->vencimiento_habiles=$this->input->post('vencimiento_habiles');
            $tarea->vencimiento_notificar=$this->input->post('vencimiento_notificar');
            $tarea->vencimiento_notificar_dias=$this->input->post('vencimiento_notificar_dias');
            $tarea->vencimiento_notificar_email=$this->input->post('vencimiento_notificar_email');
            $tarea->previsualizacion=$this->input->post('previsualizacion');
            $tarea->trazabilidad=$this->input->post('trazabilidad');
            $tarea->save();

            $respuesta->validacion=TRUE;
            $respuesta->redirect=site_url('backend/procesos/editar/'.$tarea->Proceso->id);

        }else{
            $respuesta->validacion=FALSE;
            $respuesta->errores=validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function eliminar_tarea($tarea_id){
        $tarea=Doctrine::getTable('Tarea')->find($tarea_id);

        if($tarea->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para eliminar esta tarea.';
            exit;
        }

        $proceso=$tarea->Proceso;
        $tarea->delete();

        redirect('backend/procesos/editar/'.$proceso->id);
    }

    public function ajax_crear_conexion($proceso_id){
        $proceso=Doctrine::getTable('Proceso')->find($proceso_id);
        $tarea_origen=Doctrine::getTable('Tarea')->findOneByProcesoIdAndIdentificador($proceso_id,$this->input->post('tarea_id_origen'));
        $tarea_destino=Doctrine::getTable('Tarea')->findOneByProcesoIdAndIdentificador($proceso_id,$this->input->post('tarea_id_destino'));

        if($proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para crear esta conexion.';
            exit;
        }
        if($tarea_origen->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para crear esta conexion.';
            exit;
        }
        if($tarea_destino->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para crear esta conexion.';
            exit;
        }

        //El tipo solamente se setea en la primera conexion creada para esa tarea.
        $tipo=$this->input->post('tipo');
        if($tarea_origen->ConexionesOrigen->count())
            $tipo=$tarea_origen->ConexionesOrigen[0]->tipo;

        $conexion=new Conexion();
        $conexion->tarea_id_origen=$tarea_origen->id;
        $conexion->tarea_id_destino=$tarea_destino->id;
        $conexion->tipo=$tipo;
        $conexion->save();
    }

    public function ajax_editar_conexiones($proceso_id,$tarea_origen_identificador){
        $conexiones=  Doctrine_Query::create()
                ->from('Conexion c, c.TareaOrigen t')
                ->where('t.proceso_id=? AND t.identificador=?',array($proceso_id,$tarea_origen_identificador))
                ->execute();

        if($conexiones[0]->TareaOrigen->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para editar estas conexiones.';
            exit;
        }

        $data['conexiones'] = $conexiones;

        $this->load->view('backend/procesos/ajax_editar_conexiones',$data);
    }

    public function editar_conexiones_form($tarea_id){
        $tarea=Doctrine::getTable('Tarea')->find($tarea_id);

        if($tarea->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para editar estas conexiones.';
            exit;
        }

        $this->form_validation->set_rules('conexiones', 'Conexiones','required');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $tarea->setConexionesFromArray($this->input->post('conexiones',false));
            $tarea->save();

            $respuesta->validacion=TRUE;
            $respuesta->redirect=site_url('backend/procesos/editar/'.$tarea->Proceso->id);

        }else{
            $respuesta->validacion=FALSE;
            $respuesta->errores=validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function eliminar_conexiones($tarea_id){
        $tarea=Doctrine::getTable('Tarea')->find($tarea_id);

        if($tarea->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para eliminar esta conexion.';
            exit;
        }

        $proceso=$tarea->Proceso;
        $tarea->ConexionesOrigen->delete();

        redirect('backend/procesos/editar/'.$proceso->id);
    }

    public function ajax_editar_modelo($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if($proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para editar este proceso';
            exit;
        }

        $modelo=$this->input->post('modelo');

        $proceso->updateModelFromJSON($modelo);
    }

    public function exportar($proceso_id){
        $proceso=Doctrine::getTable('Proceso')->find($proceso_id);

        $json=$proceso->exportComplete();

        header("Content-Disposition: attachment; filename=\"".mb_convert_case(str_replace(' ','-',$proceso->nombre),MB_CASE_LOWER).".simple\"");
        header('Content-Type: application/json');
        echo $json;
    }

    public function importar(){
        $file_path=$_FILES['archivo']['tmp_name'];

        if($file_path){
            $input=file_get_contents($_FILES['archivo']['tmp_name']);
            // -- Verifica si el documento a importar contiene acciones que requieren servicios
            preg_match_all("/(?<=\"soap_operacion\":\").*?(?=\")/", $input, $webservices_encontrados);
            preg_match_all("/pasarela_pago/", $input, $pasarelas_encontradas);

            $mensajes = '';

            if(count($webservices_encontrados[0])) {
              $mensajes .= '<div class="alert alert-info"><i class="icon-exclamation-sign"></i> La importación realizada requiere de servicios del <strong>Catálogo de Servicios</strong> que podrían no estar disponibles en esta instalación. <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;A continuación se listan los servicios requeridos: <br /><br />';
              $mensajes .= '<dl class="lista_codigos_servicios">';
              foreach($webservices_encontrados[0] as $codigo) {
                $mensajes .= '<dt>Código de servicio: </dt><dd>'. $codigo .'</dd>';
              }
              $mensajes .= '</dl>';
              $mensajes .= '</div>';
            }

            if(count($pasarelas_encontradas[0])) {
              $mensajes .= '<div class="alert alert-info"><i class="icon-exclamation-sign"></i> La importación realizada requiere de servicios de <strong>Pasarela de Pagos</strong> que podrían no estar dispoibles en esta instalación. <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;Por favor, verifique en el catálogo que cuenta con dichos servicios para su correcto funcionamiento.</div>';
            }

            if($mensajes != '') {
              $data['mensajes'] = $mensajes;
            }

            $proceso=Proceso::importComplete($input);
            $proceso->save();
        }

        $data['procesos'] = Doctrine_Query::create()
                ->from('Proceso p, p.Cuenta c')
                ->where('c.id = ?', UsuarioBackendSesion::usuario()->cuenta_id)
                ->where('p.nombre != ?', 'BLOQUE')
                ->orderBy('p.nombre asc')
                ->execute();

        $data['title'] = 'Listado de Procesos';
        $data['content'] = 'backend/procesos/index';
        $this->load->view('backend/template', $data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */

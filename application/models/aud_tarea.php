<?php

class AudTarea extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('identificador');
        $this->hasColumn('inicial');
        $this->hasColumn('final');
        $this->hasColumn('proceso_id');
        $this->hasColumn('nombre');
        $this->hasColumn('posx');
        $this->hasColumn('posy');
        $this->hasColumn('asignacion');                     //Modo de asignacion
        $this->hasColumn('asignacion_usuario');             //Id de usuario al que se le va a asignar en caso que modo de asignacion sea 'usuario'
        $this->hasColumn('asignacion_notificar');             //Indica si se le debe notificar via email al usuario que se le asigna esta tarea
        $this->hasColumn('almacenar_usuario');              //Se almacena el usuario o no
        $this->hasColumn('almacenar_usuario_variable');     //Nombre de la variable con que se debe almacenar
        $this->hasColumn('acceso_modo');                    //Quienes pueden acceder: grupos_usuarios, publico o registrados
        $this->hasColumn('grupos_usuarios');                //En caso que el modo de acceso sea grupos_usuarios, aqui se listan separados por coma los grupos.
        $this->hasColumn('activacion');                     //'si','no','entre_fechas'
        $this->hasColumn('activacion_inicio');              //Si es que la activacion es entre_fechas, esta seria la fecha de inicio
        $this->hasColumn('activacion_fin');                 //Si es que la activacion es entre_fechas, esta seria la fecha de fin
        $this->hasColumn('vencimiento');                    //Indica si tiene o no vencimiento.
        $this->hasColumn('vencimiento_valor');              //Entero que indica el valor del vencimiento.
        $this->hasColumn('vencimiento_unidad');             //String que indica la unidad del vencimiento. Ej: days, weeks, months, etc.
        $this->hasColumn('vencimiento_habiles');
        $this->hasColumn('vencimiento_notificar');          //Indica si se debe notificar en caso de que se acerque la fecha de vencimiento
        $this->hasColumn('vencimiento_notificar_dias');     //Indica desde cuantos dias de anticipacion se debe notificar la fecha de vencimiento
        $this->hasColumn('vencimiento_notificar_email');    //Cual es el email donde se debe notificar
        $this->hasColumn('paso_confirmacion');              //Boolean que indica si se debe incorporar una ultima pantalla de confirmacion antes de avanzar la tarea
        $this->hasColumn('previsualizacion');               //Texto de previsualizacion de la tarea al aparecer en las bandejas de entrada.
        $this->hasColumn('trazabilidad');
        $this->hasColumn('trazabilidad_id_oficina');
        $this->hasColumn('trazabilidad_cabezal');
        $this->hasColumn('trazabilidad_estado');
        $this->hasColumn('etiqueta_traza');
        $this->hasColumn('visible_traza');
        $this->hasColumn('trazabilidad_nombre_oficina');
        $this->hasColumn('asignacion_notificar_mensaje');   //Texto personalizado que se envia por correo si se debe notificar la asignacion de la tarea al usuario.
        $this->hasColumn('automatica'); //Boolean que indica si es una tarea automatica o no
        $this->hasColumn('nivel_confianza'); //Texto  que indica el nivel de confianza en el caso de acceso_modo registrado
        $this->hasColumn('paso_final_pendiente');
        $this->hasColumn('paso_final_standby');
        $this->hasColumn('paso_final_completado');
        $this->hasColumn('paso_final_sincontinuacion');
        $this->hasColumn('texto_boton_paso_final');
        $this->hasColumn('texto_boton_generar_pdf');
        $this->hasColumn('escalado_automatico');
        $this->hasColumn('vencimiento_a_partir_de_variable');
        $this->hasColumn('notificar_vencida');
        $this->hasColumn('id_x_tarea');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }
    
    public static function auditar($obj, $usuario, $operacion) {
        $new = new AudTarea();
        $conn = Doctrine_Manager::connection();
        
        if (is_numeric($obj->proceso_id)) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_proceso WHERE id=$obj->proceso_id ORDER BY id_aud DESC;");
            $stmt->execute();
            $proceso = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $proceso[0] = null;
        }

        $new->id = $obj->id;
        $new->identificador = $obj->identificador;
        $new->inicial = $obj->inicial;
        $new->nombre = $obj->nombre;
        $new->posx = $obj->posx;
        $new->posy = $obj->posy;
        $new->asignacion = $obj->asignacion;
        $new->asignacion_usuario = $obj->asignacion_usuario;
        $new->asignacion_notificar = $obj->asignacion_notificar;
        $new->almacenar_usuario = $obj->almacenar_usuario;
        $new->almacenar_usuario_variable = $obj->almacenar_usuario_variable;
        $new->acceso_modo = $obj->acceso_modo;
        $new->grupos_usuarios = $obj->grupos_usuarios;
        $new->activacion = $obj->activacion;
        $new->activacion_inicio = $obj->activacion_inicio;
        $new->activacion_fin = $obj->activacion_fin;
        $new->vencimiento = $obj->vencimiento;
        $new->vencimiento_valor = $obj->vencimiento_valor;
        $new->vencimiento_unidad = $obj->vencimiento_unidad;
        $new->vencimiento_habiles = $obj->vencimiento_habiles;
        $new->vencimiento_notificar = $obj->vencimiento_notificar;
        $new->vencimiento_notificar_dias = $obj->vencimiento_notificar_dias;
        $new->vencimiento_notificar_email = $obj->vencimiento_notificar_email;
        $new->paso_confirmacion = $obj->paso_confirmacion;
        $new->previsualizacion = $obj->previsualizacion;
        $new->trazabilidad = $obj->trazabilidad;
        $new->trazabilidad_id_oficina = $obj->trazabilidad_id_oficina;
        $new->trazabilidad_cabezal = $obj->trazabilidad_cabezal;
        $new->trazabilidad_estado = $obj->trazabilidad_estado;
        $new->etiqueta_traza = $obj->etiqueta_traza;
        $new->visible_traza = $obj->visible_traza;
        $new->trazabilidad_nombre_oficina = $obj->trazabilidad_nombre_oficina;
        $new->asignacion_notificar_mensaje = $obj->asignacion_notificar_mensaje;
        $new->automatica = $obj->automatica;
        $new->nivel_confianza = $obj->nivel_confianza;
        $new->paso_final_pendiente = $obj->paso_final_pendiente;
        $new->paso_final_standby = $obj->paso_final_standby;
        $new->paso_final_completado = $obj->paso_final_completado;
        $new->paso_final_sincontinuacion = $obj->paso_final_sincontinuacion;
        $new->texto_boton_paso_final = $obj->texto_boton_paso_final;
        $new->texto_boton_generar_pdf = $obj->texto_boton_generar_pdf;
        $new->escalado_automatico = $obj->escalado_automatico;
        $new->vencimiento_a_partir_de_variable = $obj->vencimiento_a_partir_de_variable;
        $new->notificar_vencida = $obj->notificar_vencida;
        $new->id_x_tarea = $obj->id_x_tarea;
        $new->final = $obj->getFinal();

        $new->proceso_id = $proceso[0];
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();

        $cx_o = Doctrine::getTable("Tarea")->find($obj->id)->ConexionesOrigen;
        foreach ($cx_o as $cx) {
            AudConexion::auditar($cx, $usuario, $operacion);
        }
        $cx_d = Doctrine::getTable("Tarea")->find($obj->id)->ConexionesDestino;
        foreach ($cx_d as $cx) {
            AudConexion::auditar($cx, $usuario, $operacion);
        }
        $pasos = Doctrine::getTable("Tarea")->find($obj->id)->Pasos;
        foreach ($pasos as $paso) {
            AudPaso::auditar($paso, $usuario, $operacion);
        }
        $eventos = Doctrine::getTable("Tarea")->find($obj->id)->Eventos;
        foreach ($eventos as $evento) {
            AudEvento::auditar($evento, $usuario, $operacion);
        }
        $ej_v = Doctrine::getTable("Tarea")->find($obj->id)->Validaciones;
        foreach ($ej_v as $val) {
            AudEjecutarValidacion::auditar($val, $usuario, $operacion);
        }
        return $new;
    }

}

<?php

class AudCampo extends Doctrine_Record {

    public $requiere_datos = true;    //Indica si requiere datos seleccionables. Como las opciones de un checkbox, select, etc.
    public $estatico = false; //Indica si es un campo estatico, es decir que no es un input con informacion. Ej: Parrafos, titulos, etc.
    public $etiqueta_tamano = 'large'; //Indica el tamaño default que tendra el campo de etiqueta. Puede ser large o xxlarge.
    public $requiere_nombre = true;    //Indica si requiere que se le ingrese un nombre (Es decir, no generarlo aleatoriamente)
    public $requiere_validacion = true; // Indica si se requiere validacion para el campo.
    public $sin_etiqueta = false; // Indica que no se debe mostrar la etiqueta.
    public $valor_default_tamano = 'small'; // Indica si el campo valor_default debe ser mas grande y se debe quitar el campo etiqueta. Se utilza en dialogos.
    public $dialogo = false; // Indica si el campo es de tipo DIALOGO.
    public $reporte = false; // Indica si el campo puede ser tulizado para generar el repore

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('posicion');
        $this->hasColumn('tipo');
        $this->hasColumn('formulario_id');
        $this->hasColumn('etiqueta');
        $this->hasColumn('validacion');
        $this->hasColumn('ayuda');
        $this->hasColumn('dependiente_tipo');
        $this->hasColumn('dependiente_campo');
        $this->hasColumn('dependiente_valor');
        $this->hasColumn('dependiente_relacion');
        $this->hasColumn('datos');
        $this->hasColumn('readonly');
        $this->hasColumn('valor_default');
        $this->hasColumn('documento_id');
        $this->hasColumn('fieldset');
        $this->hasColumn('extra');
        $this->hasColumn('ayuda_ampliada');
        $this->hasColumn('documento_tramite');
        $this->hasColumn('email_tramite');
        $this->hasColumn('pago_online');
        $this->hasColumn('requiere_agendar');
        $this->hasColumn('firma_electronica');
        $this->hasColumn('requiere_accion');
        $this->hasColumn('requiere_accion_id');
        $this->hasColumn('requiere_accion_boton');
        $this->hasColumn('requiere_accion_var_error');
        $this->hasColumn('exponer_campo');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }

    public static function auditar($obj, $usuario, $operacion) {
        $new = new AudCampo();
        $conn = Doctrine_Manager::connection();
        if (is_numeric($obj->formulario_id)) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_formulario WHERE id=$obj->formulario_id ORDER BY id_aud DESC;");
            $stmt->execute();
            $formulario_id = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $formulario_id[0] = null;
        }
        if (is_numeric($obj->documento_id)) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_documento WHERE id=$obj->documento_id ORDER BY id_aud DESC;");
            $stmt->execute();
            $documento_id = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $documento_id[0] = null;
        }
        if (is_numeric($obj->requiere_accion_id) && $obj->requiere_accion_id > 0) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_accion WHERE id=$obj->requiere_accion_id ORDER BY id_aud DESC;");
            $stmt->execute();
            $requiere_accion_id = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            if (!$requiere_accion_id)
                $requiere_accion_id[0] = "";
        } else {
            $requiere_accion_id[0] = "";
        }

        $new->id = $obj->id;
        $new->nombre = $obj->nombre;
        $new->posicion = $obj->posicion;
        $new->tipo = $obj->tipo;
        $new->etiqueta = $obj->etiqueta;
        $new->validacion = implode('|', $obj->validacion);
        $new->ayuda = $obj->ayuda;
        $new->dependiente_tipo = $obj->dependiente_tipo;
        $new->dependiente_campo = $obj->dependiente_campo;
        $new->dependiente_valor = $obj->dependiente_valor;
        $new->dependiente_relacion = $obj->dependiente_relacion;
        $new->datos = json_encode($obj->datos);
        $new->readonly = $obj->readonly;
        $new->valor_default = $obj->valor_default;
        $new->fieldset = $obj->fieldset;
        $new->extra = json_encode($obj->extra);
        $new->ayuda_ampliada = $obj->ayuda_ampliada;
        $new->documento_tramite = $obj->documento_tramite;
        $new->email_tramite = $obj->email_tramite;
        $new->pago_online = $obj->pago_online;
        $new->requiere_agendar = $obj->requiere_agendar;
        $new->firma_electronica = $obj->firma_electronica;
        $new->requiere_accion = $obj->requiere_accion;
        $new->requiere_accion_boton = $obj->requiere_accion_boton;
        $new->requiere_accion_var_error = $obj->requiere_accion_var_error;
        $new->exponer_campo = $obj->exponer_campo;
        $new->requiere_accion_id = isset($requiere_accion_id[0])?$requiere_accion_id[0]:null;
        $new->formulario_id = isset($formulario_id[0])?$formulario_id[0]:null;
        $new->documento_id = isset($documento_id[0])?$documento_id[0]:null;
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();
        return $new;
    }

}

<?php

class AudPasarelaPagoGenerica extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('pasarela_pago_id');
        $this->hasColumn('codigo_operacion_soap');
        $this->hasColumn('codigo_operacion_soap_consulta');
        $this->hasColumn('variable_evaluar');
        $this->hasColumn('variable_idsol');
        $this->hasColumn('url_redireccion');
        $this->hasColumn('variable_redireccion');
        $this->hasColumn('url_ticket');
        $this->hasColumn('ticket_metodo');
        $this->hasColumn('ticket_variables');
        $this->hasColumn('metodo_http');
        $this->hasColumn('variables_post');
        $this->hasColumn('mensaje_reimpresion_ticket');
        $this->hasColumn('variable_idestado');
        $this->hasColumn('tema_email_inicio');
        $this->hasColumn('cuerpo_email_inicio');
        $this->hasColumn('descripciones_estados_traza');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }

   
    public static function auditar($obj, $usuario, $operacion,$pasarela_id) { 
        $new = new AudPasarelaPagoGenerica();
        $new->id = $obj->id;
        $new->pasarela_pago_id = $pasarela_id;
        $new->codigo_operacion_soap = $obj->codigo_operacion_soap;
        $new->codigo_operacion_soap_consulta = $obj->codigo_operacion_soap_consulta;
        $new->variable_evaluar = $obj->variable_evaluar;
        $new->variable_idsol = $obj->variable_idsol;
        $new->url_redireccion = $obj->url_redireccion;
        $new->variable_redireccion = $obj->variable_redireccion;
        $new->url_ticket = $obj->url_ticket;
        $new->ticket_metodo = $obj->ticket_metodo;
        $new->ticket_variables = $obj->ticket_variables;
        $new->metodo_http = $obj->metodo_http;
        $new->variables_post = $obj->variables_post;
        $new->mensaje_reimpresion_ticket = $obj->mensaje_reimpresion_ticket;
        $new->variable_idestado = $obj->variable_idestado;
        $new->tema_email_inicio = $obj->tema_email_inicio;
        $new->cuerpo_email_inicio = $obj->cuerpo_email_inicio;
        $new->descripciones_estados_traza = $obj->descripciones_estados_traza;
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();

        return $new;
    }

}

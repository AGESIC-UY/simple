<?php

class PasarelaPagoGenerica extends Doctrine_Record {

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
    }
}

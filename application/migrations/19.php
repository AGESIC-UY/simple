<?php

class Migration_19 extends Doctrine_Migration_Base {

    public function up() {
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();

        //aud_bloque
        $q->execute("ALTER TABLE aud_bloque ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_bloque', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_bloque', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');

        //aud_usuario
        $q->execute("ALTER TABLE aud_usuario ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_usuario', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_usuario', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');

        //aud_accion
        $q->execute("ALTER TABLE aud_accion ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_accion', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_accion', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');

        //aud_grupo_usuarios
        $q->execute("ALTER TABLE aud_grupo_usuarios ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_grupo_usuarios', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_grupo_usuarios', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');

        //aud_usuario_backend
        $q->execute("ALTER TABLE aud_usuario_backend ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_usuario_backend', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_usuario_backend', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');

        //aud_campo
        $q->execute("ALTER TABLE aud_campo ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_campo', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_campo', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');

        //aud_conexion
        $q->execute("ALTER TABLE aud_conexion ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_conexion', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_conexion', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');

        //aud_documento
        $q->execute("ALTER TABLE aud_documento ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_documento', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_documento', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');

        //aud_proceso
        $q->execute("ALTER TABLE aud_proceso ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_proceso', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_proceso', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        $this->changeColumn('aud_proceso', 'traza', 'TEXT');        

        //aud_reporte
        $q->execute("ALTER TABLE aud_reporte ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_reporte', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_reporte', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        
        //aud_formulario
        $q->execute("ALTER TABLE aud_formulario ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_formulario', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_formulario', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        
        //aud_pasarela_pago
        $q->execute("ALTER TABLE aud_pasarela_pago ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_pasarela_pago', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_pasarela_pago', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        
        //aud_validacion
        $q->execute("ALTER TABLE aud_validacion ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_validacion', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_validacion', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        
        //aud_ws_operacion_respuesta
        $q->execute("ALTER TABLE aud_ws_operacion_respuesta ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_ws_operacion_respuesta', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_ws_operacion_respuesta', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        
        //aud_ejecutar_validacion
        $q->execute("ALTER TABLE aud_ejecutar_validacion ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_ejecutar_validacion', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_ejecutar_validacion', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        
        //aud_evento
        $q->execute("ALTER TABLE aud_evento ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_evento', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_evento', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        
        //aud_evento_pago
        $q->execute("ALTER TABLE aud_evento_pago ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_evento_pago', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_evento_pago', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        
        //aud_pasarela_pago_antel
        $q->execute("ALTER TABLE aud_pasarela_pago_antel ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_pasarela_pago_antel', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_pasarela_pago_antel', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        
        //aud_pasarela_pago_generica
        $q->execute("ALTER TABLE aud_pasarela_pago_generica ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_pasarela_pago_generica', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_pasarela_pago_generica', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        
        //aud_tarea
        $q->execute("ALTER TABLE aud_tarea ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_tarea', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_tarea', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        $this->changeColumn('aud_tarea', 'final', 'TINYINT(1) DEFAULT 0');
        $tareas=Doctrine::getTable("Tarea")->findAll();
        foreach ($tareas as $tarea) {
            $t=Doctrine::getTable("AudTarea")->find($tarea->id);
            $t->final=$tarea->getFinal();
            $t->save();
        }
        
        //aud_ws_catalogo
        $q->execute("ALTER TABLE aud_ws_catalogo ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_ws_catalogo', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_ws_catalogo', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        
        //aud_ws_operacion
        $q->execute("ALTER TABLE aud_ws_operacion ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_ws_operacion', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_ws_operacion', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        
         //aud_paso
        $q->execute("ALTER TABLE aud_paso ADD PRIMARY KEY (id_aud);");
        $this->changeColumn('aud_paso', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        $this->changeColumn('aud_paso', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
    }

    public function down() {
        
    }

}

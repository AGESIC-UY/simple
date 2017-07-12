<?php
class Migration_19 extends Doctrine_Migration_Base {
    public function up(){
       //migraciones necesarias para la versión 1.2

        // --
        // -- Modifica tabla paso
        // --
        $this->addColumn("paso", "generar_pdf", "TINYINT(1) default 1");

        // --
        // -- Modifica tabla tarea
        // --
        $this->addColumn("tarea", "texto_boton_generar_pdf", "VARCHAR(255) default 'Imprimir'");

        // --
        // -- Modifica tabla file
        // --
        $this->addColumn("file", "etapa_id", "INT(10) default NULL");
        $this->changeColumn('file', 'tipo',"ENUM('dato','documento','etapa_pdf')");

    }

    public function down(){

    }
}

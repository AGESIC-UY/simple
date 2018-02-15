<?php
class Migration_21 extends Doctrine_Migration_Base {
    public function up(){
       //migraciones necesarias para la versión 1.2 r1 y r2

       // -- Modifica tabla tarea
       // --
       $this->changeColumn("tarea", "trazabilidad_cabezal", "INT(1) default 1");

       // -- Modifica tabla paso
       // --
       $this->addColumn("paso", "enviar_traza", "TINYINT(1) default 0");

       // -- Modifica tabla conexion
       // --
       $this->addColumn("conexion", "estado_fin_trazabilidad", "INT(1) default 2");

       // -- Modifica tabla trazabilidad
       // --
       $this->addColumn("trazabilidad", "enviar_correo", "TINYINT(1) default 0");

       // --
       // -- Modifica tabla file para agregar nuevos tipos, necesaria en la r2
       // --
       $this->changeColumn('file', 'tipo',"ENUM('dato','documento','etapa_pdf','descarga','accion_archivo')");
    }

    public function down(){

    }
}

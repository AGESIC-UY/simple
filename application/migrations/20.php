<?php
class Migration_20 extends Doctrine_Migration_Base {
    public function up(){
       //migraciones necesarias para la versión 1.2

        // --
        // -- Modifica tabla campo
        // --
        $this->addColumn("campo", "pago_online", "TINYINT(1) default 1");
        $this->addColumn("campo", "requiere_agendar", "TINYINT(1) default 1");
        $this->addColumn("campo", "firma_electronica", "TINYINT(1) default 1");
    }

    public function down(){

    }
}

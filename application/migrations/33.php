<?php

class Migration_33 extends Doctrine_Migration_Base {

    public function up() {
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        $this->changeColumn('aud_conexion', 'estado_fin_trazabilidad', 'varchar(100)');
        $this->changeColumn('conexion', 'estado_fin_trazabilidad', 'varchar(100)');
        $q->execute("commit;");
    }

    public function down() {
        
    }

}

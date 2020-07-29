<?php

class Migration_35 extends Doctrine_Migration_Base {

    public function up() {
        Doctrine_Manager::connection()->beginTransaction();
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        $this->changeColumn('documento', 'validez', 'varchar(100)');
        $this->changeColumn('aud_documento', 'validez', 'varchar(100)');
        Doctrine_Manager::connection()->commit();
    }

    public function down() {
        
    }

}

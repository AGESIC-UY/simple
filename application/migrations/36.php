<?php

class Migration_36 extends Doctrine_Migration_Base {

    public function up() {
        Doctrine_Manager::connection()->beginTransaction();
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        $this->addColumn('documento', 'unir_pdf', 'TINYINT(1)');
        $this->addColumn('aud_documento', 'unir_pdf', 'TINYINT(1)');
        $this->addColumn('documento', 'lista_pdf', 'varchar(100)');
        $this->addColumn('aud_documento', 'lista_pdf', 'varchar(100)');
        Doctrine_Manager::connection()->commit();
    }

    public function down() {
        
    }

}

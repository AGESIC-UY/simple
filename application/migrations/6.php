<?php

class Migration_6 extends Doctrine_Migration_Base {

    public function up() {
        $this->addColumn('documento', 'titulo', 'string', 128, array('notnull'=>1));
    }

    public function postUp() {
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        $q->execute("UPDATE documento SET titulo=nombre WHERE tipo='certificado'");
    }

    public function down() {
        $this->removeColumn('documento', 'titulo');
    }

}
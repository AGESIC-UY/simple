<?php
class Migration_9 extends Doctrine_Migration_Base {
    public function up(){
        $this->addColumn( 'campo', 'exponer_campo', 'integer' , null, array( 'notnull' => 1,'default'=>0));
        $this->addColumn( 'accion', 'exponer_variable', 'integer' , null, array( 'notnull' => 1,'default'=>0));
        $this->addColumn('proceso', 'activo', 'boolean', null, array('notnull'=>1,'default'=>1));

    }

    public function postUp() {
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        $q->execute("UPDATE proceso SET activo=1");
    }

    public function down(){
        $this->removeColumn( 'campo', 'exponer_campo' );
        $this->removeColumn( 'accion', 'exponer_variable' );
        $this->removeColumn('proceso', 'activo');
    }
}

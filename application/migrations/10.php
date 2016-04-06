<?php
class Migration_10 extends Doctrine_Migration_Base {
    public function up(){
        $this->changeColumn('evento', 'regla',"VARCHAR(512)",null,array('notnull'=>1));
        $this->changeColumn('paso', 'regla',"VARCHAR(512)",null,array('notnull'=>1));
    }
    public function down(){
        $this->changeColumn('evento', 'regla',"VARCHAR(256)",null,array('notnull'=>1));
        $this->changeColumn('paso', 'regla',"VARCHAR(256)",null,array('notnull'=>1));
    }
}
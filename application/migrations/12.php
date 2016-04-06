<?php
class Migration_12 extends Doctrine_Migration_Base {
    public function up(){
        $this->addColumn( 'documento', 'tamano', 'enum' , null, array('values'=>array('letter','legal'),'default'=>'letter'));
    }
    public function down(){
        $this->removeColumn('documento', 'tamano');
    }
}
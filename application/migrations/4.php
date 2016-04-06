<?php
class Migration_4 extends Doctrine_Migration_Base {
    public function up(){
        $this->addColumn( 'campo', 'dependiente_relacion', 'enum' , null, array('values'=>array('==','!='),'default'=>'=='));
        
    }
    
    public function down(){
        $this->removeColumn( 'campo', 'dependiente_relacion' );
    }
}
<?php
class Migration_11 extends Doctrine_Migration_Base {
    public function up(){
        $this->addIndex( 'usuario', 'email', array(
            'fields'=>array('email','open_id')
        ));
    }
    public function down(){
        $this->removeIndex( 'usuario', 'email' );
    }
}
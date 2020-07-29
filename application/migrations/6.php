<?php
class Migration_6 extends Doctrine_Migration_Base {
    public function up(){
        $this->addIndex( 'pago', 'id_solicitud', array(
            'fields'=>array('id_solicitud')
        ));
    }
    public function down(){
    }
}

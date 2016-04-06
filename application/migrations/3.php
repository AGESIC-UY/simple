<?php
class Migration_3 extends Doctrine_Migration_Base {
    public function up(){
        $this->addColumn( 'tarea', 'vencimiento_notificar_dias', 'integer' , 4, array( 'notnull' => 1,'unsigned'=>1,'default'=>1));
        
    }
    
    public function down(){
        $this->removeColumn( 'tarea', 'vencimiento_notificar_dias' );
    }
}
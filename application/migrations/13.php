<?php

class Migration_13 extends Doctrine_Migration_Base {

    public function up() {
        //pasarela_antel
        $this->addColumn('pasarela_pago_antel', 'referencia_pago', 'varchar(255) default ""');
       
    }

    public function down() {
        
    }

}

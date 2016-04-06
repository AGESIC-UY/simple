<?php
class Migration_1 extends Doctrine_Migration_Base {
    public function up(){
        $this->addColumn( 'cuenta', 'api_token', 'string' , 32, array( 'notnull' => 1));
        $this->changeColumn('usuario_backend', 'rol',"ENUM('super','modelamiento','operacion','gestion','desarrollo')");
    }
    public function down(){
        $this->removeColumn( 'cuenta', 'api_token' );
        $this->changeColumn('usuario_backend', 'rol',"ENUM('super','modelamiento','operacion','gestion')");
    }
}
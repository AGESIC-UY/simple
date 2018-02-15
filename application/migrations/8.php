<?php
class Migration_8 extends Doctrine_Migration_Base {
    public function up(){
        $this->changeColumn('usuario_backend', 'rol',"ENUM('super','modelamiento','operacion','seguimiento','gestion','desarrollo','configuracion')");
    }
    public function down(){
        $this->changeColumn('usuario_backend', 'rol',"ENUM('super','modelamiento','operacion','gestion','desarrollo','configuracion')");
    }
}

<?php
class Migration_18 extends Doctrine_Migration_Base {
    public function up(){
       //migraciones necesarias para la versión 1.1 P

        // --
        // -- Modifica tabla tarea
        // --
        $this->addColumn('tarea', 'texto_boton_paso_final', 'VARCHAR(1000) default "Finalizar"');

    }

    public function down(){

    }
}

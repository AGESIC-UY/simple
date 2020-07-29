<?php

class Migration_34 extends Doctrine_Migration_Base {

    public function up() {
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        
        $q->execute("UPDATE aud_conexion SET estado_fin_trazabilidad='INICIO' WHERE estado_fin_trazabilidad=1");
        $q->execute("UPDATE aud_conexion SET estado_fin_trazabilidad='EN_EJECUCION' WHERE estado_fin_trazabilidad=2");
        $q->execute("UPDATE aud_conexion SET estado_fin_trazabilidad='CERRADO' WHERE estado_fin_trazabilidad=3");
        $q->execute("UPDATE aud_conexion SET estado_fin_trazabilidad='FINALIZADO' WHERE estado_fin_trazabilidad=4");
        
        $q->execute("UPDATE conexion SET estado_fin_trazabilidad='INICIO' WHERE estado_fin_trazabilidad=1");
        $q->execute("UPDATE conexion SET estado_fin_trazabilidad='EN_EJECUCION' WHERE estado_fin_trazabilidad=2");
        $q->execute("UPDATE conexion SET estado_fin_trazabilidad='CERRADO' WHERE estado_fin_trazabilidad=3");
        $q->execute("UPDATE conexion SET estado_fin_trazabilidad='FINALIZADO' WHERE estado_fin_trazabilidad=4");
    }

    public function down() {
        
    }

}

<?php

class Migration_15 extends Doctrine_Migration_Base {

    public function up() {
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        //migraciones necesarias para la versión 2.0
        
        //trazabilidad v2
        //tabla tarea
         $q->execute("UPDATE tarea SET trazabilidad_estado='INICIO' WHERE trazabilidad_estado=1");
         $q->execute("UPDATE tarea SET trazabilidad_estado='EN_EJECUCION' WHERE trazabilidad_estado=2");
         $q->execute("UPDATE tarea SET trazabilidad_estado='CERRADO' WHERE trazabilidad_estado=3");
         $q->execute("UPDATE tarea SET trazabilidad_estado='FINALIZADO' WHERE trazabilidad_estado=4");
         $q->execute("UPDATE tarea AS t SET t.trazabilidad_nombre_oficina = t.trazabilidad_id_oficina where t.trazabilidad_nombre_oficina='' OR t.trazabilidad_nombre_oficina=NULL");
        
        //tabla evento
        
        $q->execute("UPDATE evento SET tipo_registro_traza='COMUN' WHERE tipo_registro_traza=3");
        
        //tabla paso
        
        //tabla evento_pago
        
        $q->execute("UPDATE evento_pago SET tipo_registro_traza='COMUN' WHERE tipo_registro_traza=3");
        
    }

    public function down() {
        
    }

}

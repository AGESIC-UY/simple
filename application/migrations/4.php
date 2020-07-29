<?php
class Migration_4 extends Doctrine_Migration_Base {
    public function up(){
       //migraciones necesarias para la versión 1.4 r7
       $q = Doctrine_Manager::getInstance()->getCurrentConnection();
       $r = $q->execute("SELECT * FROM parametro WHERE clave='reporte_limite_permitido_completo' LIMIT 1")->fetchAll();
       if(!$r) {
         $cuentas = $q->execute("SELECT * FROM cuenta")->fetchAll();

         foreach($cuentas as $cuenta) {
           $q->execute("INSERT INTO parametro (cuenta_id, clave, valor) VALUES ('".$cuenta['id']."', 'reporte_limite_permitido_completo', '5000')");
         }
       }

       $r = $q->execute("SELECT * FROM parametro WHERE clave='reporte_limite_permitido_basico' LIMIT 1")->fetchAll();
       if(!$r) {
         $cuentas = $q->execute("SELECT * FROM cuenta")->fetchAll();

         foreach($cuentas as $cuenta) {
           $q->execute("INSERT INTO parametro (cuenta_id, clave, valor) VALUES ('".$cuenta['id']."', 'reporte_limite_permitido_basico', '5000')");
         }
       }

       $r = $q->execute("SELECT * FROM parametro WHERE clave='reporte_completo_cantidad_maxima' LIMIT 1")->fetchAll();
       if(!$r) {
         $cuentas = $q->execute("SELECT * FROM cuenta")->fetchAll();

         foreach($cuentas as $cuenta) {
           $q->execute("INSERT INTO parametro (cuenta_id, clave, valor) VALUES ('".$cuenta['id']."', 'reporte_completo_cantidad_maxima', '25')");
         }
       }

       $r = $q->execute("SELECT * FROM parametro WHERE clave='reporte_basico_cantidad_maxima' LIMIT 1")->fetchAll();
       if(!$r) {
         $cuentas = $q->execute("SELECT * FROM cuenta")->fetchAll();

         foreach($cuentas as $cuenta) {
           $q->execute("INSERT INTO parametro (cuenta_id, clave, valor) VALUES ('".$cuenta['id']."', 'reporte_basico_cantidad_maxima', '25')");
         }
       }
    }

    public function down(){

    }
}

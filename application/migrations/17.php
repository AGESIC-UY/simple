<?php
class Migration_17 extends Doctrine_Migration_Base {
    public function up(){
       //migraciones necesarias para la versión 1.1
       $q = Doctrine_Manager::getInstance()->getCurrentConnection();
       $r = $q->execute("SELECT * FROM parametro WHERE clave='resultados_por_pagina' LIMIT 1")->fetchAll();
       if(!$r) {
         $cuentas = $q->execute("SELECT * FROM cuenta")->fetchAll();

         foreach($cuentas as $cuenta) {
           $q->execute("INSERT INTO parametro (cuenta_id, clave, valor) VALUES ('".$cuenta['id']."', 'resultados_por_pagina', '50')");
         }
       }
    }

    public function down(){

    }
}

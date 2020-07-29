<?php

class Migration_38 extends Doctrine_Migration_Base {

    public function up() {
        //migraciones necesarias para la versión 2.1-R5
        Doctrine_Manager::connection()->beginTransaction();
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();        
        try {
            $q->execute("ALTER TABLE etapa DROP FOREIGN KEY fk_etapa_usuario1; ALTER TABLE etapa ADD CONSTRAINT fk_etapa_usuario1 FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE SET NULL ON UPDATE SET NULL;");
        } catch (Exception $exc) {
        }
        Doctrine_Manager::connection()->commit();       
        
    }

    public function down() {
        
    }

}

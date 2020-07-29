<?php

class Migration_21 extends Doctrine_Migration_Base {

    public function up() {

        //pasarela_antel
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        if ($q->getTable('aud_pasarela_pago_antel')->hasColumn('referencia_pago') != 1) {
            try {
                $this->addColumn('aud_pasarela_pago_antel', 'referencia_pago', 'varchar(255) default ""');
                $q->execute("commit;");
                $pasarela_antel = $q->execute("SELECT * FROM pasarela_pago_antel")->fetchAll();
                if ($pasarela_antel) {
                    foreach ($pasarela_antel as $value) {
                        $q->execute("UPDATE aud_pasarela_pago_antel pa SET pa.referencia_pago='" . $value['referencia_pago'] . "' WHERE pa.id =" . $value['id'] . ";");
                    }
                }
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
    }

    public function down() {
        
    }

}

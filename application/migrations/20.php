<?php

class Migration_20 extends Doctrine_Migration_Base {

    public function up() {
        //migraciones necesarias para la versión 1.6-R1
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        try {
            $q->execute("INSERT INTO etiqueta_traza(etiqueta, descripcion) VALUES ('INICIO CIUDADANO', 'Se asigna a la primer línea de traza donde haya actuación del ciudadano');");
        } catch (Exception $exc) {
            
        }
        try {
            $q->execute("INSERT INTO etiqueta_traza(etiqueta, descripcion) VALUES ('FIN CIUDADANO', 'Se asigna a la última línea de traza donde haya actuación del ciudadano');");
        } catch (Exception $exc) {
            
        }
        try {
            $q->execute("INSERT INTO etiqueta_traza(etiqueta, descripcion) VALUES ('INICIO ORGANISMO', 'Se asigna a la primer línea de traza donde haya actuación del organismo y a la primera luego de cada actuación ciudadana');");
        } catch (Exception $exc) {
            
        }
        try {
            $q->execute("INSERT INTO etiqueta_traza(etiqueta, descripcion) VALUES ('FIN ORGANISMO', 'Se asigna a la última línea de traza donde haya actuación del organismo y a la última antes de una actuación ciudadana');");
        } catch (Exception $exc) {
            
        }
        try {
            $q->execute("INSERT INTO etiqueta_traza(etiqueta, descripcion) VALUES ('AGENDA', 'Se asigna a la línea donde se indica que se está invocando a una Agenda');");
        } catch (Exception $exc) {
            
        }
        try {
            $q->execute("INSERT INTO etiqueta_traza(etiqueta, descripcion) VALUES ('PAGO', 'Se asigna a la línea donde se indica que se está invocando un Pago');");
        } catch (Exception $exc) {
            
        }
        try {
            $q->execute("INSERT INTO etiqueta_traza(etiqueta, descripcion) VALUES ('FIRMA', 'Se asigna a la línea donde se indica que se está invocando una Firma');");
        } catch (Exception $exc) {
            
        }        
    }

    public function down() {
        
    }

}

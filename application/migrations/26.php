<?php
class Migration_26 extends Doctrine_Migration_Base
{
    public function up()
    {
      // --migraciones necesarias para la versión 1.4 r2

      // modifico tabla docmuento
      /*
          TINYTEXT |           255 (2 8−1) bytes
          TEXT |        65,535 (216−1) bytes = 64 KiB  -->COMO ESTA ANTES DE MODIFICAR
          MEDIUMTEXT |    16,777,215 (224−1) bytes = 16 MiB
          LONGTEXT | 4,294,967,295 (232−1) bytes =  4 GiB
      */
      $this->changeColumn('documento', 'contenido', 'LONGTEXT NOT NULL');

      $this->changeColumn('datos_seguimiento', 'valor', 'LONGTEXT NOT NULL');

      $this->changeColumn('monitoreo', 'soap_peticion', 'LONGTEXT NOT NULL');
      $this->changeColumn('monitoreo', 'soap_respuesta', 'LONGTEXT NOT NULL');

    }

    public function down()
    {
    }
}

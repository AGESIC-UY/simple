<?php

class Migration_15 extends Doctrine_Migration_Base {
  public function up() {
    // --
    // -- Modifica tabla Campo
    // --
    $this->addColumn('campo', 'fieldset', 'varchar', 100, array('notnull' => 1));
    $this->changeColumn('campo', 'nombre', 'VARCHAR(100)', null, array('notnull' => 1));

    // --
    // -- Modifica tabla Formulario
    // --
    $this->addColumn('formulario', 'bloque_id', 'integer', 10, array('notnull' => 0));

    // --
    // -- Modifica tabla Paso
    // --
    $this->addColumn('paso', 'nombre', 'varchar', 255, array('notnull' => 0));

    // --
    // -- Modifica tabla Tarea
    // --
    $this->addColumn('tarea', 'trazabilidad', 'integer', 1, array('notnull' => 1, 'default' => 1));

    // --
    // -- Modifica tabla UsuarioBackend
    // --
    $this->addColumn('usuario_backend', 'usuario', 'varchar', 128, array('notnull' => 0));

    // --
    // -- Modifica tabla PasarelaPagoAntel
    // --
    $this->addColumn('pasarela_pago_antel', 'clave_organismo', 'varchar', 60, array('notnull' => 0));
  }

  public function down() {

    $this->removeColumn('campo', 'fieldset');

    $this->removeColumn('campo', 'nombre');

    $this->removeColumn('formulario', 'bloque_id');

    $this->removeColumn('paso', 'nombre');

    $this->removeColumn('tarea', 'trazabilidad');

    $this->removeColumn('usuario_backend', 'usuario');

    $this->removeColumn('pasarela_pago_antel', 'clave_organismo');
  }
}

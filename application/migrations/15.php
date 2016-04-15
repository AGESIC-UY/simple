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
  }

  public function down() {
    // --
    // -- Elimina columna fieldset de tabla Campo
    // --
    $this->removeColumn('campo', 'fieldset');
    $this->removeColumn('campo', 'nombre');

    // --
    // -- Elimina columna bloque_id de tabla Formulario
    // --
    $this->removeColumn('formulario', 'bloque_id');

    // --
    // -- Elimina columna nombre de tabla Paso
    // --
    $this->removeColumn('paso', 'nombre');

    // --
    // -- Elimina columna trazabilidad de tabla Tarea
    // --
    $this->removeColumn('tarea', 'trazabilidad');

    $this->removeColumn('usuario_backend', 'usuario');
  }
}

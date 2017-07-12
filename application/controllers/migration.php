<?php

class Migration extends CI_Controller{

  public function __construct() {
    parent::__construct();

    if(!$this->input->is_cli_request())
      exit;
  }

  public function migrate($version = null) {
    $migration = new Doctrine_Migration( 'application/migrations' );
    $migration->migrate($version);
  }
}

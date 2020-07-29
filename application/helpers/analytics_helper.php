<?php

function display_codigo_analytics() {
  $cuenta = Doctrine::getTable('Cuenta')->find(Cuenta::cuentaSegunDominio()->id);
  return json_decode($cuenta->codigo_analytics);
}

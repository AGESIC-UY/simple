<?php

function obtener_cantidad_elementos_por_pagina(){
  $paginado = Doctrine_Query::create()
                          ->from('Parametro p')
                          ->where('p.cuenta_id = ? AND p.clave = ?', array(Cuenta::cuentaSegunDominio()->id, 'resultados_por_pagina'))
                          ->fetchOne();
  if ($paginado){
    $cantidad_elementos_por_pagina = $paginado->valor;
  }
  else{
    $cantidad_elementos_por_pagina = 50;
  }

  return $cantidad_elementos_por_pagina;
}

function obtener_offset($this_controller){
  return $this_controller->input->get('offset');
}

function paginar($this_controller, $url_controller, $cantidad_total_elementos, $cantidad_elementos_por_pagina){

  $this_controller->load->library('pagination');
  $this_controller->pagination->initialize(array(
      'base_url'=> site_url($url_controller),
      'total_rows'=> $cantidad_total_elementos,
      'per_page'=> $cantidad_elementos_por_pagina
  ));

}

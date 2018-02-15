<?php
require_once('campo.php');
class CampoDomicilioIca extends Campo{

    public $requiere_datos=false;
    public $estatico=false;

    function setTableDefinition() {
        parent::setTableDefinition();

        $this->hasColumn('readonly','bool',0,array('default'=>0));
    }

    function setUp() {
        parent::setUp();
        $this->setTableName("campo");
    }

    protected function display($modo, $dato,$etapa_id) {
        $CI = & get_instance();

        $display .= '<fieldset class="custom-fieldset" id="fieldset_domicilio'. $this->id .'">';
        $display .= '<legend><span class="custom-fieldset-legend">'.$this->etiqueta.'</span></legend>';

        $display .= ' <div class="control-group">';

        //select departamento
        $dato_departamento_nombre = Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($this->nombre.'_departamento',$etapa_id)->valor;
        $dato_departamento_localidad = Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($this->nombre.'_localidad',$etapa_id)->valor;
        if (!$dato_departamento_localidad || empty($dato_departamento_localidad)){
          $dato_departamento_localidad = "''";
        }else{
          $dato_departamento_localidad = "'".$dato_departamento_localidad."'";
        }
        $display .= ' <div class="control-group">';
        $display .= '    <label class="control-label" for="'. $this->id .'_departamento_select_id">Departamento:</label>';
        $display .= '   <div class="controls">';
        $display .= '       <select id="'. $this->id .'_departamento_select_id" name="departamento" ' . ($modo == 'visualizacion' || $CI->uri->segment(3) == 'ver_etapa' || $CI->uri->segment(2) == 'ver_etapa' ? 'disabled' : '') . '>';
        $departamentos_array = array(
                                      'artigas' => 'Artigas',
                                      'canelones' => 'Canelones',
                                      'cerro_largo' => 'Cerro Largo',
                                      'colonia' => 'Colonia',
                                      'durazno' => 'Durazno',
                                      'flores' => 'Flores',
                                      'florida' => 'Florida',
                                      'lavalleja' => 'Lavalleja',
                                      'maldonado' => 'Maldonado',
                                      'montevideo' => 'Montevideo',
                                      'paysandu' => 'Paysandú',
                                      'rio_negro' => 'Rio Negro',
                                      'rivera' => 'Rivera',
                                      'rocha' => 'Rocha',
                                      'salto' => 'Salto',
                                      'soriano' => 'Soriano',
                                      'san_jose' => 'San José',
                                      'tacuarembo' => 'Tacuarembó',
                                      'treinta_y_tres' => 'Treinta y Tres'
                                );
          $display .= '<option value="">Seleccionar</option>';

          foreach ($departamentos_array as $key => $value) {
            if($dato_departamento_nombre == $key){
              $display .= '<option value="'.$key.'" selected>'.$value.'</option>';
            }
            else{
              $display .= '<option value="'.$key.'">'.$value.'</option>';
            }
          }

        $display .= '       </select>';
        $display .= '   </div>';
        $display .= ' </div>';

        //select localidad
        $display .= ' <div class="control-group">';
        $display .= '    <label class="control-label" for="'. $this->id .'_localidad_select_id">Localidad:</label>';
        $display .= '   <div class="controls">';
        $display .= '       <select id="'. $this->id .'_localidad_select_id" name="localidad" ' . ($modo == 'visualizacion' || $CI->uri->segment(3) == 'ver_etapa' || $CI->uri->segment(2) == 'ver_etapa' ?'disabled' : '') . '>';
        $display .= '       </select>';
        $display .= '   </div>';
        $display .= ' </div>';

        //empeiza datos domicilio
        $display .= ' <div id="datos_domicilio">';

        //input domicilio
        $display .= ' <div class="control-group">';
        $display .= '    <label class="control-label" for="'. $this->id .'_domicilio_input_id">Domicilio*:</label>';
        $display .= '   <div class="controls">';
        $display .= '       <input id="'. $this->id .'_domicilio_input_id" name="'. $this->id .'_domicilio_input_name" data-modo="'.$modo.'" type="text"  value="'.Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($this->nombre.'_domicilio_busqueda',$etapa_id)->valor.'" '. ($modo == 'visualizacion' || $CI->uri->segment(3) == 'ver_etapa' || $CI->uri->segment(2) == 'ver_etapa' ? 'disabled' : '') . '>';
        $display .= '   </div>';
        $display .= ' </div>';

        $display .= '   <div class="controls">';
        // se quita de seguimiento frontend y backend
        if($modo == 'edicion' && $CI->uri->segment(3) != 'ver_etapa' && $CI->uri->segment(2) != 'ver_etapa'){
          //boton procesar busqeuda SIMPLE
          $display .= '     <button class="btn btn-secundary btn-lg no-margin-box" id="'. $this->id .'_procesar_busqueda_simple">Confirmar domicilio</button>';
        }
        $display .= ' </div>';

        //terminan datos domicilio
        $display .= ' </div>';

        //DATOS EXTRA
        $display .= ' <div id="datos_domicilio_extra" style="display:none;">';
        //input calle
        $display .= ' <div class="control-group">';
        $display .= '    <label class="control-label" for="'. $this->id .'_calle_input_id">Calle*:</label>';
        $display .= '   <div class="controls">';
        $display .= '       <input id="'. $this->id .'_calle_input_id" name="calle" data-modo="'.$modo.'" type="text" value="'.Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($this->nombre.'_calle',$etapa_id)->valor.'" ' . ($modo == 'visualizacion' || $CI->uri->segment(3) == 'ver_etapa' || $CI->uri->segment(2) == 'ver_etapa' ? 'disabled' : '') . '>';
    		$display .= '		<span class="help-block">Ejemplo: 18 de Julio o Ruta 8</span>';
    		$display .= '   </div>';
        $display .= ' </div>';

        //input número
        $display .= ' <div class="control-group">';
        $display .= '    <label class="control-label" for="'. $this->id .'_numero_input_id">Número:</label>';
        $display .= '   <div class="controls">';
        $display .= '       <input id="'. $this->id .'_numero_input_id" name="numero" data-modo="'.$modo.'" type="text" value="'.Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($this->nombre.'_numero',$etapa_id)->valor.'" ' . ($modo == 'visualizacion' || $CI->uri->segment(3) == 'ver_etapa' || $CI->uri->segment(2) == 'ver_etapa' ? 'disabled' : '') . '>';
        $display .= '		<span class="help-block">Ejemplo: 1242 o Km 80</span>';
	      $display .= '   </div>';
        $display .= ' </div>';

		      //input esquina
        $display .= ' <div class="control-group">';
        $display .= '    <label class="control-label" for="'. $this->id .'_esquina_input_id">Esquina:</label>';
        $display .= '   <div class="controls">';
        $display .= '       <input id="'. $this->id .'_esquina_input_id" name="esquina" data-modo="'.$modo.'" type="text" value="'.Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($this->nombre.'_esquina',$etapa_id)->valor.'" ' . ($modo == 'visualizacion' || $CI->uri->segment(3) == 'ver_etapa' || $CI->uri->segment(2) == 'ver_etapa' ? 'disabled' : '') . '>';
        $display .= '   </div>';
        $display .= ' </div>';

		      //input manzana
        $display .= ' <div class="control-group">';
        $display .= '    <label class="control-label" for="'. $this->id .'_manzana_input_id">Manzana:</label>';
        $display .= '   <div class="controls">';
        $display .= '       <input id="'. $this->id .'_manzana_input_id" name="manzana" data-modo="'.$modo.'" type="text" value="'.Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($this->nombre.'_manzana',$etapa_id)->valor.'" ' . ($modo == 'visualizacion' || $CI->uri->segment(3) == 'ver_etapa' || $CI->uri->segment(2) == 'ver_etapa' ? 'disabled' : '') . '>';
        $display .= '   </div>';
        $display .= ' </div>';

		      //input solar
        $display .= ' <div class="control-group">';
        $display .= '    <label class="control-label" for="'. $this->id .'_solar_input_id">Solar:</label>';
        $display .= '   <div class="controls">';
        $display .= '       <input id="'. $this->id .'_solar_input_id" name="solar" data-modo="'.$modo.'" type="text" value="'.Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($this->nombre.'_solar',$etapa_id)->valor.'" ' . ($modo == 'visualizacion' || $CI->uri->segment(3) == 'ver_etapa' || $CI->uri->segment(2) == 'ver_etapa' ? 'disabled' : '') . '>';
        $display .= '   </div>';
        $display .= ' </div>';

	      //input otros datos
        $display .= ' <div class="control-group">';
        $display .= '    <label class="control-label" for="'. $this->id .'_otros_input_id">Otros datos:</label>';
        $display .= '   <div class="controls">';
        $display .= '       <input id="'. $this->id .'_otros_input_id" name="otros" data-modo="'.$modo.'" type="text" value="'.Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($this->nombre.'_otros',$etapa_id)->valor.'" ' . ($modo == 'visualizacion' || $CI->uri->segment(3) == 'ver_etapa' || $CI->uri->segment(2) == 'ver_etapa' ? 'disabled' : '') . '>';
        $display .= '		<span class="help-block">Apto, bloque, etc.</span>';
	      $display .= '   </div>';
        $display .= ' </div>';

        $display .= ' <div class="controls">';
        // se quita de seguimiento frontend y backend
        if($modo == 'edicion' && $CI->uri->segment(3) != 'ver_etapa' && $CI->uri->segment(2) != 'ver_etapa'){
          //boton agregar procesar busqueda AVANZADA
          $display .= '     <button class="btn btn-secundary btn-lg no-margin-box" id="'. $this->id .'_procesar_busqueda_avanzada">Confirmar domicilio</button>';
          $display .= '   <button class="ingresar-mas-datos btn-link" id="ir_form_simplificado">Volver al formulario simplificado</button>';
        }


        $display .= ' </div>';

        //termina datos extra
        $display .= ' </div>';

        $display .= ' <div class="controls">';
        //lista de domicilios disponibles para seleccionar
        $display .= '     <div id="'. $this->id .'_div_texto_candidatas" style="display:none">';
        $display .= '     </div>';

        $display .= '     <div id="'. $this->id .'_div_direcciones_candidatas" class="mapa-direccion" style="display:none">';
        $display .= '     </div>';

        //domicilio NO encontrado
        $display .= '     <div id="'.$this->id .'_domicilio_no_encontrado" class="mapa-direccion" style="display:none">';
        $display .= '       <div class="pin-no-ubicado"><img src="'.base_url().'assets/img/pin-no-ubicado.png" alt="domicilio no encontrado"><span>El domicilio no pudo ser ubicado en el mapa</span></div>';
        $display .= '       <button class="btn btn-secundary btn-lg ingresar-mas-datos" id="ir_form_avanzado_no_encontrada">Agregar más datos</button>';
        $display .= '     </div>';

        //mapa
        $display .= '<div id="'.$this->id .'_mapa" class="map modulo-direcciones"></div>';
        //texto mapa
        $display .= '<div id="'.$this->id .'_texto_direccion" class="map modulo-direcciones"></div>';

        if($modo == 'edicion' && $CI->uri->segment(3) != 'ver_etapa' && $CI->uri->segment(2) != 'ver_etapa'){
          $display .= '<div id="'.$this->id .'_texto_mapa" class="map modulo-direcciones" style="display:none"><span>Este no es mi domicilio.</span><button class="ingresar-mas-datos btn-link" id="ir_form_avanzado">Ingresar más datos</button></div>';
        }
        $display .= ' </div>';

        //campo para validacion form
        $display .=     '<div class="controls"><input id="'.$this->id.'" type="hidden" name="'.$this->nombre.'" value="'.$this->nombre.'|'.$etapa_id.'"></div>';

        $display .= ' </div>'; //cierra control group para mensaje error

        $display .= '</fieldset>';


        $busqueda_avanzada = Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($this->nombre.'_busqueda_avazada',$etapa_id)->valor;

        if((bool)$busqueda_avanzada){
          $display .= '<script>
                          $(document).ready(function() {
                            $("#datos_domicilio").hide();
                            $("#ir_form_simplificado").show();
                            $("#'.$this->id.'_texto_mapa").hide();
                            $("#datos_domicilio_extra").show();
                          });
                        </script>';
          }

          else{
              $display .= '<script>
                              $(document).ready(function() {
                                $("#datos_domicilio").show();
                                $("#datos_domicilio_extra").hide();
                              });
                            </script>';
          }

        //scripts solo si estoy en una etapa de un tramite
        if ($etapa_id) {

          $display .= '<script type="text/javascript">var Paysandu = {option1:{value:\'PAYSANDU\',text:\'PAYSANDU\'}, option2:{value:\'CHAPICUY\',text:\'CHAPICUY\'}, option3:{value:\'NUEVO_PAYSANDU\',text:\'NUEVO PAYSANDU\'}, option4:{value:\'PIEDRAS_COLORADAS\',text:\'PIEDRAS COLORADAS\'}, option5:{value:\'TAMBORES\',text:\'TAMBORES\'}, option6:{value:\'QUEBRACHO\',text:\'QUEBRACHO\'}, option7:{value:\'PORVENIR\',text:\'PORVENIR\'}, option8:{value:\'GUICHON\',text:\'GUICHON\'}, option9:{value:\'LA_CORONILLA_P\',text:\'LA CORONILLA\'}, option10:{value:\'PUEBLO_GALLINAL\',text:\'PUEBLO GALLINAL\'}};
                var Rio_Negro = {option1:{value:\'FRY_BENTOS\',text:\'FRAY BENTOS\'}, option2:{value:\'GRECCO\',text:\'GRECCO\'}, option3:{value:\'NUEVO_BERLIN\',text:\'NUEVO BERLIN\'}, option4:{value:\'PARAJE_EL_AGUILA\',text:\'PARAJE EL AGUILA\'}, option5:{value:\'PARAJE_LAS_VIBORAS\',text:\'PARAJE LAS VIBORAS\'}, option6:{value:\'PASO_DE_LA_CRUZ\',text:\'PASO DE LA CRUZ\'}, option7:{value:\'PASO_DE_LOS_MELLIZOS\',text:\'PASO DE LOS MELLIZOS\'}, option8:{value:\'PUEBLO_SANCHEZ_GRANDE\',text:\'PUEBLO SANCHEZ GRANDE\'}, option9:{value:\'SAN_JAVIER\',text:\'SAN JAVIER\'}, option10:{value:\'SARANDI_DE_NAVARRO\',text:\'SARANDI DE NAVARRO\'}, option11:{value:\'YOUNG\',text:\'YOUNG\'}};
                var Rivera = {option1:{value:\'RIVERA\',text:\'RIVERA\'}, option2:{value:\'PASO_ATAQUES\',text:\'PASO ATAQUES\'}, option3:{value:\'VICHADERO\',text:\'VICHADERO\'}, option4:{value:\'MINAS_DE_CORRALES\',text:\'MINAS DE CORRALES\'}, option5:{value:\'LAPUENTE\',text:\'LAPUENTE\'}, option6:{value:\'MOIRONES\',text:\'MOIRONES\'}, option7:{value:\'TRANQUERAS\',text:\'TRANQUERAS\'}, option8:{value:\'ESTACION_ATAQUES\',text:\'ESTACION ATAQUES\'}};
                var Rocha = {option1:{value:\'19_DE_ABRIL\',text:\'19 DE ABRIL\'}, option2:{value:\'CHUY\',text:\'CHUY\'}, option3:{value:\'ROCHA\',text:\'ROCHA\'}, option4:{value:\'LA_CORONILLA_R\',text:\'LA CORONILLA\'}, option5:{value:\'CASTILLOS\',text:\'CASTILLOS\'}, option6:{value:\'EL_CANELON\',text:\'EL CANELON\'}, option7:{value:\'18_DE_JULIO\',text:\'18 DE JULIO\'}, option8:{value:\'VELAZQUEZ\',text:\'VELAZQUEZ\'}, option9:{value:\'LA_PALOMA_R\',text:\'LA PALOMA\'}, option10:{value:\'LASCANO\',text:\'LASCANO\'}, option11:{value:\'CEBOLLATI\',text:\'CEBOLLATI\'}};
                var Salto = {option1:{value:\'COLONIA_ITAPEBI\',text:\'COLONIA ITAPEBI\'}, option2:{value:\'SALTO\',text:\'SALTO\'}, option3:{value:\'CONSTITUCION\',text:\'CONSTITUCION\'}, option4:{value:\'BELEN\',text:\'BELEN\'}, option5:{value:\'BIASSINI\',text:\'BIASSINI\'}, option6:{value:\'SAN_ANTONIO_S\',text:\'SAN ANTONIO\'}, option7:{value:\'LAURELES\',text:\'LAURELES\'}, option8:{value:\'QUINTANA\',text:\'QUINTANA\'}, option9:{value:\'COLONIA_LAVALLEJA\',text:\'COLONIA LAVALLEJA\'}, option10:{value:\'PASO_CEMENTERO\',text:\'PASO CEMENTERO\'}, option11:{value:\'CERROS_DE_VERA\',text:\'CERROS DE VERA\'}, option12:{value:\'SARANDI_DEL_ARAPEY\',text:\'SARANDI DEL ARAPEY\'}};
                var Soriano = {option1:{value:\'SACACHISPAS\',text:\'SACACHISPAS\'}, option2:{value:\'DOLORES\',text:\'DOLORES\'}, option3:{value:\'MERCEDES\',text:\'MERCEDES\'}, option4:{value:\'VILLA_SORIANO\',text:\'VILLA SORIANO\'}, option5:{value:\'PALMITAS\',text:\'PALMITAS\'}, option6:{value:\'SANTA_CATALINA\',text:\'SANTA CATALINA\'}, option7:{value:\'AGRACIADA\',text:\'AGRACIADA\'}, option8:{value:\'JOSE_ENTRIQUE_RODO\',text:\'JOSE ENTRIQUE RODO\'}, option9:{value:\'CARDONA\',text:\'CARDONA\'}, option10:{value:\'PALMAR\',text:\'PALMAR\'}};
                var San_Jose = {option1:{value:\'SAN_JOSE\',text:\'SAN JOSE\'}, option2:{value:\'JUAN_SOLER\',text:\'JUAN SOLER\'}, option3:{value:\'ECILDA_PAULLIER\',text:\'ECILDA PAULLIER\'}, option4:{value:\'MAL_ABRIGO\',text:\'MAL ABRIGO\'}, option5:{value:\'CHAMIZO\',text:\'CHAMIZO\'}, option6:{value:\'VILLA_RODRIGUEZ\',text:\'VILLA RODRIGUEZ\'}, option7:{value:\'CIUDAD_DEL_PLATA\',text:\'CIUDAD DEL PLATA\'}, option8:{value:\'RAFAEL_PERAZA\',text:\'RAFAEL PERAZA\'}, option9:{value:\'LIBERTAD\',text:\'LIBERTAD\'}};
                var Tacuarembo = {option1:{value:\'TACUAREMBO\',text:\'TACUAREMBO\'}, option2:{value:\'ANSINA\',text:\'ANSINA\'}, option3:{value:\'PASO_DE_LOS_TOROS\',text:\'PASO DE LOS TOROS\'}, option4:{value:\'SAN_GREGORIO_DE_POLANCO\',text:\'SAN GREGORIO DE POLANCO\'}, option5:{value:\'CLARA\',text:\'CLARA\'}, option6:{value:\'BARRIO_LOPEZ\',text:\'BARRIO LOPEZ\'}, option7:{value:\'PASO_BONILLA\',text:\'PASO BONILLA\'}, option8:{value:\'CLAVIJO\',text:\'CLAVIJO\'}, option9:{value:\'PERALTA\',text:\'PERALTA\'}, option10:{value:\'ACHAR\',text:\'ACHAR\'}, option11:{value:\'CURTINA\',text:\'CURTINA\'}, option12:{value:\'PASO_DEL_CERRO\',text:\'PASO DEL CERRO\'}, option13:{value:\'CARAGUATA\',text:\'CARAGUATA\'}};
                var aux_m = {option1:{value:\'MONTEVIDEO\',text:\'MONTEVIDEO\'}};
                var Artigas = {option1:{value:\'BELLA_UNION\',text:\'BELLA UNION\'}, option2:{value:\'TOMAS_GOMENSORO\',text:\'TOMAS GOMENSORO\'}, option3:{value:\'PASO_FARIAS\',text:\'PASO FARIAS\'}, option4:{value:\'PUEBLO_SEQUEIRA\',text:\'PUEBLO SEQUEIRA\'}, option5:{value:\'JAVIER_DE_VIANA\',text:\'JAVIER DE VIANA\'}, option6:{value:\'CERRO_AMARILLO\',text:\'CERRO AMARILLO\'}, option7:{value:\'PINTADO_GRANDE\',text:\'PINTADO GRANDE\'}, option8:{value:\'ARTIGAS\',text:\'ARTIGAS\'}, option9:{value:\'CATALAN_GRANDE\',text:\'CATALAN GRANDE\'}, option10:{value:\'BERNABE_RIVERA\',text:\'BERNABE RIVERA\'}, option11:{value:\'BALTASAR_BRUM\',text:\'BALTASAR BRUM\'}, option12:{value:\'CAINSA\',text:\'CAINSA\'}, option13:{value:\'CALNU\',text:\'CALNU\'}, option14:{value:\'CERRO_EJIDO\',text:\'CERRO EJIDO\'} , option15:{value:\'CERRO_SAN_EUGENIO\',text:\'CERRO SAN EUGENIO\'}};
                var Can = {option1:{value:\'SAN_RAMON\',text:\'SAN RAMON\'}, option2:{value:\'TALA\',text:\'TALA\'}, option3:{value:\'SAN_ANTONIO_C\',text:\'SAN ANTONIO\'}, option4:{value:\'EL_PINAR\',text:\'EL PINAR\'}, option5:{value:\'CIUDAD_DE_LA_COSTA\',text:\'CIUDAD DE LA COSTA\'}, option6:{value:\'PASO_CARRASCO\',text:\'PASO CARRASCO\'}, option7:{value:\'BARROS_BLANCOS\',text:\'BARROS BLANCOS\'}, option8:{value:\'SALINAS\',text:\'SALINAS\'}, option9:{value:\'ATLANTIDA\',text:\'ATLANTIDA\'}, option10:{value:\'PARQUE_DEL_PLATA\',text:\'PARQUE DEL PLATA\'}, option11:{value:\'LA_FLORESTA\',text:\'LA FLORESTA\'}, option12:{value:\'SOCA\',text:\'SOCA\'}, option13:{value:\'COLONIA_NICOLICH\',text:\'COLONIA NICOLICH\'}, option14:{value:\'MIGUES\',text:\'MIGUES\'}, option15:{value:\'SAN_JACINTO\',text:\'SAN JACINTO\'}, option16:{value:\'SAN_BAUTISTA\',text:\'SAN BAUTISTA\'}, option17:{value:\'SANTA_ROSA\',text:\'SANTA ROSA\'}, option18:{value:\'PANDO\',text:\'PANDO\'}, option19:{value:\'TOLEDO\',text:\'TOLEDO\'}, option20:{value:\'SUAREZ\',text:\'SUAREZ\'}, option21:{value:\'SAUCE\',text:\'SAUCE\'}, option22:{value:\'SANTA_LUCIA\',text:\'SANTA LUCIA\'}, option23:{value:\'CANELONES\',text:\'CANELONES\'}, option24:{value:\'LOS_CERRILLOS\',text:\'LOS CERRILLOS\'}, option25:{value:\'PROGRESO\',text:\'PROGRESO\'}, option26:{value:\'LA_PAZ\',text:\'LA PAZ\'}, option27:{value:\'LAS_PIEDRAS\',text:\'LAS PIEDRAS\'}};
                var Cerro_Largo = {option1:{value:\'PLACIDO_ROSAS\',text:\'PLACIDO ROSAS\'}, option2:{value:\'MELO\',text:\'MELO\'}, option3:{value:\'CONVENTOS\',text:\'CONVENTOS\'}, option4:{value:\'LAGO_MERIN\',text:\'LAGO MERIN\'}, option5:{value:\'RIO_BRANCO\',text:\'RIO BRANCO\'}, option6:{value:\'ISIDORO_NOBLIA\',text:\'ISIDORO NOBLIA\'}, option7:{value:\'TRES_BOLICHES\',text:\'TRES BOLICHES\'}, option8:{value:\'BANADO_DE_PAJAS\',text:\'BANADO DE PAJAS\'}, option9:{value:\'ARBOLITO\',text:\'ARBOLITO\'}, option10:{value:\'FRAILE_MUERTO\',text:\'FRAILE MUERTO\'}, option11:{value:\'CERRO_DE_LAS_CUENTAS\',text:\'CERRO DE LAS CUENTAS\'}, option12:{value:\'TUPAMBAE\',text:\'TUPAMBAE\'}, option13:{value:\'CENTURION\',text:\'CENTURION\'}, option14:{value:\'CUCHILLA_DEL_CARMEN\',text:\'CUCHILLA DEL CARMEN\'}};
                var Col = {option1:{value:\'COLONIA_DEL_SACRAMENTO\',text:\'COLONIA DEL SACRAMENTO\'}, option2:{value:\'ROSARIO\',text:\'ROSARIO\'}, option3:{value:\'CARMELO\',text:\'CARMELO\'}, option4:{value:\'NUEVA_PALMIRA\',text:\'NUEVA PALMIRA\'}, option5:{value:\'NUEVA_HELVECIA\',text:\'NUEVA HELVECIA\'}, option6:{value:\'COLONIA_VALDENSE\',text:\'COLONIA VALDENSE\'}, option7:{value:\'REAL_DE_SAN_CARLOS\',text:\'REAL DE SAN CARLOS\'}, option8:{value:\'JUAN_LACAZE\',text:\'JUAN LACAZE\'}, option9:{value:\'CONCHILLAS\',text:\'CONCHILLAS\'}, option10:{value:\'EL_CERRO_CARMELO\',text:\'EL CERRO (CARMELO)\'}, option11:{value:\'POLANCOS\',text:\'POLANCOS\'}, option12:{value:\'OMBUES_DE_LAVALLE\',text:\'OMBUES DE LAVALLE\'}, option13:{value:\'MIGUELETE\',text:\'MIGUELETE\'}, option14:{value:\'TARARIRAS\',text:\'TARARIRAS\'}, option15:{value:\'RIACHUELO\',text:\'RIACHUELO\'}, option16:{value:\'GRANJA_SAN_JOSE\',text:\'GRANJA SAN JOSE\'}, option17:{value:\'CUFRE\',text:\'CUFRE\'}, option18:{value:\'FLORENCIO_SANCHEZ\',text:\'FLORENCIO SANCHEZ\'}};
                var Durazno = {option1:{value:\'DURAZNO\',text:\'DURAZNO\'}, option2:{value:\'SANTA_BERNARDINA\',text:\'SANTA BERNARDINA\'}, option3:{value:\'VILLA_DEL_CARMEN\',text:\'VILLA DEL CARMEN\'}, option4:{value:\'CERRO_CHATO_D\',text:\'CERRO CHATO\'}, option5:{value:\'SARANDI_DEL_YI\',text:\'SARANDI DEL YI\'}, option6:{value:\'COLONIA_ROSSELL_Y_RIUS\',text:\'COLONIA ROSSELL Y RIUS\'}, option7:{value:\'CENTENARIO\',text:\'CENTENARIO\'}, option8:{value:\'FELICIANO\',text:\'FELICIANO\'}, option9:{value:\'CHACRAS_DE_DURAZNO\',text:\'CHACRAS DE DURAZNO\'}, option10:{value:\'CHACRAS_DE_SARANDI_DEL_YI\',text:\'CHACRAS DE SARANDI DEL YI\'}, option11:{value:\'CARLOS_REYLES\',text:\'CARLOS REYLES\'}, option12:{value:\'SAN_JORGE\',text:\'SAN JORGE\'}, option13:{value:\'BLANQUILLO\',text:\'BLANQUILLO\'}, option14:{value:\'LA_PALOMA_D\',text:\'LA PALOMA\'},};
                var Flores = {option1:{value:\'TRINIDAD\',text:\'TRINIDAD\'}, option2:{value:\'ANDRESITO\',text:\'ANDRESITO\'}, option3:{value:\'SAN_GREGORIO\',text:\'SAN GREGORIO\'}, option4:{value:\'ISMAEL_CORTINAS\',text:\'ISMAEL CORTINAS\'}, option5:{value:\'LA_CASILLA\',text:\'LA CASILLA\'}, option6:{value:\'CAMINO_A_LA_AVIACION\',text:\'CAMINO A LA AVIACION\'}, option7:{value:\'LA_UNION\',text:\'LA UNION\'}, option8:{value:\'JUAN_JOSE_CASTRO\',text:\'JUAN JOSE CASTRO\'},};
                var Florida = {option1:{value:\'FLORIDA\',text:\'FLORIDA\'}, option2:{value:\'25_DE_MAYO\',text:\'25 DE MAYO\'}, option3:{value:\'SARANDI_GRANDE\',text:\'SARANDI GRANDE\'}, option4:{value:\'LA_CRUZ\',text:\'LA CRUZ\'}, option5:{value:\'MENDOZA_GRANDE\',text:\'MENDOZA GRANDE\'}, option6:{value:\'CARDAL\',text:\'CARDAL\'}, option7:{value:\'JUNCAL\',text:\'JUNCAL\'}, option8:{value:\'ALEJANDRO_GALLINAL\',text:\'ALEJANDRO GALLINAL\'}, option9:{value:\'25_DE_AGOSTO\',text:\'25 DE AGOSTO\'}, option10:{value:\'POLANCO_DEL_YI\',text:\'POLANCO DEL YI\'}, option11:{value:\'CAPPILLA_DEL_SAUCE\',text:\'CAPPILLA DEL SAUCE\'}, option12:{value:\'GONI\',text:\'GONI\'}, option13:{value:\'NICO_PEREZ\',text:\'NICO PEREZ\'}, option14:{value:\'FRAY_MARCOS\',text:\'FRAY MARCOS\'}, option15:{value:\'CASUPA\',text:\'CASUPA\'}};
                var Lavalleja = {option1:{value:\'VILLA_DEL_ROSARIO\',text:\'VILLA DEL ROSARIO\'}, option2:{value:\'MINAS\',text:\'MINAS\'}, option3:{value:\'SOLIS_DE_MATAOJO\',text:\'SOLIS DE MATAOJO\'}, option4:{value:\'LA_CALERA\',text:\'LA CALERA\'}, option5:{value:\'POLANCO\',text:\'POLANCO\'}, option6:{value:\'MARMARAJA\',text:\'MARMARAJA\'}, option7:{value:\'PIRARAJA\',text:\'PIRARAJA\'}, option8:{value:\'JOSE_PEDRO_VARELA\',text:\'JOSE PEDRO VARELA\'}, option9:{value:\'JOSE_BATLLE_Y_ORDONEZ\',text:\'JOSE BATLLE Y ORDONEZ\'}, option10:{value:\'ZAPICAN\',text:\'ZAPICAN\'}, option11:{value:\'MARISCALA\',text:\'MARISCALA\'}, option12:{value:\'BARRIGA_NEGRA\',text:\'BARRIGA NEGRA\'}};
                var Mal = {option1:{value:\'JOSE_IGNACIO\',text:\'JOSE IGNACIO\'}, option2:{value:\'LA_BARRA\',text:\'LA BARRA\'}, option3:{value:\'PIRIAPOLIS\',text:\'PIRIAPOLIS\'}, option4:{value:\'PUNTA_DEL_ESTE\',text:\'PUNTA DEL ESTE\'}, option5:{value:\'LA_CORONILLA_M\',text:\'LA CORONILLA\'}, option6:{value:\'AIGUA\',text:\'AIGUA\'}, option7:{value:\'MALDONADO_NUEVO\',text:\'MALDONADO NUEVO\'}, option8:{value:\'BALNEARIO_SOLIS\',text:\'BALNEARIO SOLIS\'}, option9:{value:\'PUEBLO_EDEN\',text:\'PUEBLO EDEN\'}, option10:{value:\'PAN_DE_AZUCAR\',text:\'PAN DE AZUCAR\'}, option11:{value:\'SAN_CARLOS\',text:\'SAN CARLOS\'}, option12:{value:\'MALDONADO\',text:\'MALDONADO\'}, option13:{value:\'PUEBLO_GARZON\',text:\'PUEBLO GARZON\'}};
                var Treinta_y_Tres = {option1:{value:\'CERRO_CHATO_T\',text:\'CERRO CHATO\'}, option2:{value:\'CUCHILLA_DE_DIONISIO\',text:\'CUCHILLA DE DIONISIO\'}, option3:{value:\'VERGARA\',text:\'VERGARA\'}, option4:{value:\'JOSE_ENRIQUE_MARTINEZ_LA_CHARQUEADA\',text:\'JOSE ENRIQUE MARTINEZ (LA CHARQUEADA)\'},option5:{value:\'TREINTA_Y_TRES\',text:\'TREINTA Y TRES\'}, option6:{value:\'VILLA_SARA\',text:\'VILLA SARA\'}, option7:{value:\'COLONIA_DIONISIO_DIAZ\',text:\'COLONIA DIONISIO DIAZ\'}, option8:{value:\'RINCON\',text:\'RINCON\'}, option9:{value:\'SANTA_CLARA_DE_OLIMAR\',text:\'SANTA CLARA DE OLIMAR\'}, option10:{value:\'ISLA_PATRULLA\',text:\'ISLA PATRULLA\'}};
                $(document).on("change","[name=\'departamento\']",function(){
                 $("[name=\'localidad\']").children().remove().end().append(\'<option value="">Seleccionar</option>\');
                 if($("[name=\'departamento\']").val()!=""){
                  var items;
                  switch($("[name=\'departamento\']").val()) {
                  case \'montevideo\':items = aux_m; break;
                  case \'artigas\':items = Artigas; break;
                  case \'canelones\':items = Can; break;
                  case \'cerro_largo\':items = Cerro_Largo; break;
                  case \'colonia\':items = Col; break;
                  case \'durazno\':items = Durazno; break;
                  case \'flores\':items = Flores; break;
                  case \'florida\': items = Florida; break;
                  case \'lavalleja\':items = Lavalleja; break;
                  case \'maldonado\':items = Mal; break;
                  case \'treinta_y_tres\':items = Treinta_y_Tres; break;
                  case \'paysandu\':items = Paysandu; break;
                  case \'rio_negro\':items = Rio_Negro; break;
                  case \'rivera\':items = Rivera; break;
                  case \'rocha\':items = Rocha; break;
                  case \'salto\':items = Salto; break;
                  case \'san_jose\':items = San_Jose; break;
                  case \'soriano\':items = Soriano; break;
                  case \'tacuarembo\':items = Tacuarembo; break;
                  default:
                  $("[name=\'localidad\']").children().remove().end().append(\'<option value="">Seleccionar</option>\');
                  break;
                   };
                   $.each(items, function (i, item) {
                  $("[name=\'localidad\']").append($(\'<option>\', {
                    value: item.value,
                    text : item.text
                  }));
                   });
                 }
                });
                $(\'body\').ready(function(){
                  $("[name=\'localidad\']").children().remove().end().append(\'<option value="">Seleccionar</option>\');
                  if($("[name=\'departamento\']").val()!=""){
                  var items = [];
                  switch($("[name=\'departamento\']").val()) {
                   case \'montevideo\':items = aux_m; break;
                   case \'artigas\':items = Artigas; break;
                   case \'canelones\':items = Can; break;
                   case \'cerro_largo\':items = Cerro_Largo; break;
                   case \'colonia\':items = Col; break;
                   case \'durazno\':items = Durazno; break;
                   case \'flores\':items = Flores; break;
                   case \'florida\': items = Florida; break;
                   case \'lavalleja\':items = Lavalleja; break;
                   case \'maldonado\':items = Mal; break;
                   case \'treinta_y_tres\':items = Treinta_y_Tres; break;
                   case \'paysandu\':items = Paysandu; break;
                   case \'rio_negro\':items = Rio_Negro; break;
                   case \'rivera\':items = Rivera; break;
                   case \'rocha\':items = Rocha; break;
                   case \'salto\':items = Salto; break;
                   case \'san_jose\':items = San_Jose; break;
                   case \'soriano\':items = Soriano; break;
                   case \'tacuarembo\':items = Tacuarembo; break;
                   default:
                    $("[name=\'localidad\']").children().remove().end().append(\'<option value="">Seleccionar</option>\');
                    break;
                  };
                  $.each(items, function (i, item) {
                  var localidad_valor = '. $dato_departamento_localidad .';
                  if (localidad_valor == item.value){
                      $("[name=\'localidad\']").append($(\'<option>\', {
                      value: item.value,
                      text : item.text,
                      selected : true
                      }));
                  }else{
                    $("[name=\'localidad\']").append($(\'<option>\', {
                     value: item.value,
                     text : item.text
                    }));
                  }

                  });
                };});
</script>';

          $display .= '<script type="text/javascript">';

          $display .= '$(document).ready(function() {';

          //cargo script para mostrar departamentos y localidades
        /*  $display .= 'jQuery.getScript("'.base_url().'assets/js/modulo-direcciones-ica.js")
                          .done(function() {
                          })
                          .fail(function(jqXHR, textStatus, errorThrown) {
                          });';*/

          //mostrar mapa dato de seguimiento
          $x = Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($this->nombre.'_X',$etapa_id);
          $y = Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($this->nombre.'_Y',$etapa_id);
          $matchAddress = Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($this->nombre.'_MatchAddress',$etapa_id);

          if(!empty($x) && !empty($y)){
            //mapa con dato de seguimiento
            $display .= 'jQuery.getScript("'.base_url().'assets/js/ol-3.5.js")
                            .done(function() {
                              //MOSTRAR MAPA
                                $("#'. $this->id .'_mapa").html("");
                                var utm = "+proj=utm +zone=21 +south";
                                var yacare = "+proj=longlat +ellps=intl +towgs84=-155.0,171.0,37.0,0.0,0.0,0.0,0.0 +no_defs";
                                var xy = proj4(utm,yacare,['.$x->valor.', '.$y->valor.']);
                                lon2 = xy[0];
                                lat2= xy[1];
                                zoom2 = 17;

                                if((lat2=="" || lat2=="0") && (lon2=="" || lon2=="0")) {
                                  lat2 = "-32.5476626";
                                  lon2 = "-55.4411862";
                                  zoom2 = 2;
                                }
                                var coord2 = ol.proj.fromLonLat([parseFloat(lon2), parseFloat(lat2)]);
                                //Capa del mapa
                                var mapLayer = new ol.layer.Tile({
                                  source: new ol.source.OSM()
                                });
                                //Capa del punto
                                var iconStyle = new ol.style.Style({
                                  image: new ol.style.Icon(({
                                    anchor: [0.5, 46],
                                    anchorXUnits: "fraction",
                                    anchorYUnits: "pixels",
                                    opacity: 0.75,
                                    src: "'.base_url().'assets/img/pin.png"
                                  }))
                                });
                                var iconFeature = new ol.Feature({
                                  geometry: new ol.geom.Point(coord2),
                                  name: "X"
                                });
                                iconFeature.setStyle(iconStyle);
                                var vectorSource = new ol.source.Vector({
                                  features: [iconFeature]
                                });
                                var vectorLayer = new ol.layer.Vector({
                                  source: vectorSource
                                });

                                //Dibujar el mapa con las dos layers
                                var map = new ol.Map({
                                  target: "'. $this->id .'_mapa",
                                  layers: [mapLayer, vectorLayer],
                                  view: new ol.View({
                                    center: coord2,
                                    zoom: zoom2
                                  })
                                });

                                $("#'. $this->id .'_texto_mapa").show();
                                $("#'. $this->id .'_texto_direccion").show().html("<p><strong>'.$matchAddress->valor.'</strong></p>");
                                //TERMINA MOSTRAR MAPA
                            })
                            .fail(function(jqXHR, textStatus, errorThrown) {
                            });';
                      }

          //boton procesar busqeuda SIMPLE click
          $display .= '$("#'. $this->id .'_procesar_busqueda_simple").click(function(event) {
                          event.preventDefault();
                          $("#'. $this->id .'_mapa").html("");
                          $("#'. $this->id .'_texto_mapa").hide();
                          $(".validacion-error").html("").hide();
                          $("#'. $this->id .'_div_direcciones_candidatas").html("").hide();
                          $("#'. $this->id .'_div_texto_candidatas").hide();
                          $("#'. $this->id .'_domicilio_no_encontrado").hide();
                          $(".mensaje_error_campo").hide();
                          $("#'. $this->id .'_texto_direccion").hide()

                          //$(this).parent().parent().parent().parent().removeClass("error");

                          $(".control-group.error").each(function(index, element) {
                            $(element).removeClass("error");
                          });

                          if($("#'.$this->id.'_domicilio_input_id").val().trim() == ""){
                            $(".control-group.error").each(function(index, element) {
                              $(element).removeClass("error");
                            });
                            $("html, body").animate({ scrollTop: 0 }, "fast");
                            $(".validacion-error").html("<span class=\'dialog-title\'>Hay <strong>1 error</strong> en el formulario</span><div class=\'alert alert-error\'>1. <a href=\'#'.$this->id.'_domicilio_input_id\' class=\'error_link\'>El campo \'<strong>Domicilio</strong>\' es obligatorio.</a></div><div class=\'alert alert-error\'>").fadeIn();
                            $("#'.$this->id.'_domicilio_input_id").parent().parent().addClass("error");
                            $("#'.$this->id.'_domicilio_input_id").parent().append("<div class=\'mensaje_error_campo\'>El campo \'Domicilio\' es obligatorio.</div>");
                            return;
                          }

                          $.blockUI({
                            message: \'<img src="'.site_url().'assets/img/ajax-loader.gif"></img>\',
                             css: {
                               width: \'70px\',
                               height: \'60px\',
                                border: \'none\',
                                padding: \'15px\',
                                backgroundColor: \'#000\',
                                textAlign: \'center\',
                                color: \'#fff\',
                                top: \'40%\',
                                left: \'50%\',
                           }});

                          $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: document.Constants.host + "/modulo_direcciones_ica/procesar_busqueda_simple_ajax",
                            data: {
                                    "method": "POST",
                                    "departamento": $("#'. $this->id .'_departamento_select_id").val(),
                                    "localidad": $("#'. $this->id .'_localidad_select_id").val(),
                                    "direccion": $("#'. $this->id .'_domicilio_input_id").val()
                                  }
                              })
                              .done(function(data) {
                                $.unblockUI();
                                if(data.error){
                                  $(".control-group.error").each(function(index, element) {
                                    $(element).removeClass("error");
                                  });

                                  if(data.lista == "error_interno"){
                                    $("html, body").animate({ scrollTop: 0 }, "fast");
                                    $(".validacion-error").html("Ocurrió un error interno al procesar la información, por favor intente nuevamente.").fadeIn();
                                  }
                                  else{
                                    $("html, body").animate({ scrollTop: 0 }, "fast");
                                    $(".validacion-error").html("<span class=\'dialog-title\'>Hay <strong>1 error</strong> en el formulario</span><div class=\'alert alert-error\'>1. <a href=\'#'.$this->id.'_domicilio_input_id\' class=\'error_link\'>El campo \'<strong>Domicilio</strong>\' es obligatorio.</a></div><div class=\'alert alert-error\'>").fadeIn();
                                    $("#'.$this->id.'_domicilio_input_id").parent().parent().addClass("error");
                                    $("#'.$this->id.'_domicilio_input_id").parent().append("<div class=\'mensaje_error_campo\'>El campo \'Domicilio\' es obligatorio.</div>");
                                  }
                                }//termina errores de respuesta
                                else if(data.lista.length == 0){
                                    $("#'. $this->id .'_domicilio_no_encontrado").show();
                                }
                                else{
                                  $("#'. $this->id .'_div_direcciones_candidatas").html("").hide();
                                  $("#'. $this->id .'_div_texto_candidatas").html("").hide();

                                  $("#'. $this->id .'_div_texto_candidatas").append("<p>Se encontraron " + data.lista.length +" ubicaciones posibles, indique la correcta:</p>").show();

                                  jQuery.each(data.lista, function(key, domicilio_candidato) {
                                    //se muestran botones con direcciones posbiles
                                    $("#'. $this->id .'_div_direcciones_candidatas").append(\'<button class="btn btn-secundary btn-lg" id="'. $this->id .'_seleccionar_domicilio_\'+ key + \'" ><span class="icon-ok icon-white"></span> \' + domicilio_candidato.MatchAddress + \'</button>\').show();

                                    //CLICK BOTON SELECCIONAR DOMICILIO
                                    $("#'. $this->id .'_seleccionar_domicilio_"+ key).click(function(event) {
                                      event.preventDefault();
                                      $(".mensaje_error_campo").hide();

                                      $("#'. $this->id .'_div_direcciones_candidatas").html("").hide();
                                      $("#'. $this->id .'_div_texto_candidatas").hide();
                                      $.blockUI({
                                        message: \'<img src="'.site_url().'assets/img/ajax-loader.gif"></img>\',
                                         css: {
                                           width: \'70px\',
                                           height: \'60px\',
                                            border: \'none\',
                                            padding: \'15px\',
                                            backgroundColor: \'#000\',
                                            textAlign: \'center\',
                                            color: \'#fff\',
                                            top: \'40%\',
                                            left: \'50%\',
                                       }});

                                      var datos_seguimiento_guardar = {
                                                    "'. $this->nombre .'_domicilio_busqueda": $("#'.$this->id.'_domicilio_input_id").val(),
                                                    "'. $this->nombre .'_X": domicilio_candidato.X,
                                                    "'. $this->nombre .'_Y": domicilio_candidato.Y,
                                                    "'. $this->nombre .'_MatchAddress": domicilio_candidato.MatchAddress,
                                                    "'. $this->nombre .'_StanAddress": domicilio_candidato.StanAddress,
                                                    "'. $this->nombre .'_Score": domicilio_candidato.Score,
                                                    "'. $this->nombre .'_departamento": $("select[name=departamento]").val(),
                                                    "'. $this->nombre .'_localidad": $("select[name=localidad]").val(),
                                                    "'. $this->nombre .'_validacion_siguiente": "1",
                                                  };

                                      $.ajax({
                                        type: "POST",
                                        dataType: "json",
                                        url: document.Constants.host + "/modulo_direcciones_ica/guardar_datos_seguimiento_ajax",
                                        data: {
                                                "method": "POST",
                                                "datos_guardar": datos_seguimiento_guardar,
                                                "etapa_id": "'.$etapa_id.'"
                                              }
                                          })
                                          .done(function(data) {
                                            $.unblockUI();
                                          })
                                          .fail(function(jqXHR, textStatus, errorThrown) {
                                            $.unblockUI();
                                          });

                                      //MOSTRAR MAPA
                                      $("#'. $this->id .'_mapa").html("");

                                      var utm = "+proj=utm +zone=21 +south";
                                      var yacare = "+proj=longlat +ellps=intl +towgs84=-155.0,171.0,37.0,0.0,0.0,0.0,0.0 +no_defs";
                                      var xy = proj4(utm,yacare,[domicilio_candidato.X, domicilio_candidato.Y]);
                                      lon2 = xy[0];
                                      lat2= xy[1];
                                      zoom2 = 17;

                                      if((lat2=="" || lat2=="0") && (lon2=="" || lon2=="0")) {
                                        lat2 = "-32.5476626";
                                        lon2 = "-55.4411862";
                                        zoom2 = 2;
                                      }
                                      var coord2 = ol.proj.fromLonLat([parseFloat(lon2), parseFloat(lat2)]);
                                      //Capa del mapa
                                      var mapLayer = new ol.layer.Tile({
                                        source: new ol.source.OSM()
                                      });
                                      //Capa del punto
                                      var iconStyle = new ol.style.Style({
                                        image: new ol.style.Icon(({
                                          anchor: [0.5, 46],
                                          anchorXUnits: "fraction",
                                          anchorYUnits: "pixels",
                                          opacity: 0.75,
                                          src: "'.base_url().'assets/img/pin.png"
                                        }))
                                      });
                                      var iconFeature = new ol.Feature({
                                        geometry: new ol.geom.Point(coord2),
                                        name: "X"
                                      });
                                      iconFeature.setStyle(iconStyle);
                                      var vectorSource = new ol.source.Vector({
                                        features: [iconFeature]
                                      });
                                      var vectorLayer = new ol.layer.Vector({
                                        source: vectorSource
                                      });

                                      //Dibujar el mapa con las dos layers
                                      var map = new ol.Map({
                                        target: "'. $this->id .'_mapa",
                                        layers: [mapLayer, vectorLayer],
                                        view: new ol.View({
                                          center: coord2,
                                          zoom: zoom2
                                        })
                                      });

                                      $("#'. $this->id .'_texto_direccion").show().html("<strong>"+domicilio_candidato.MatchAddress+"</strong>");
                                      $("#'. $this->id .'_texto_mapa").show();
                                      // TERMINA MOSTRAR MAPA
                                    });//TERMINA CLICK BOTON SELECCIONAR DOMICILIO
                                  });
                                }//termina else
                              }) //finaliza .done
                              .fail(function(jqXHR, textStatus, errorThrown) {
                                $.unblockUI();
                              });
                        });';

                        //boton procesar busqeuda AVANZADA click
                        $display .= '$("#'. $this->id .'_procesar_busqueda_avanzada").click(function(event) {
                                        event.preventDefault();
                                        $("#'. $this->id .'_mapa").html("");
                                        $("#'. $this->id .'_texto_mapa").hide();
                                        $(".validacion-error").html("").hide();
                                        $("#'. $this->id .'_div_direcciones_candidatas").html("").hide();
                                        $("#'. $this->id .'_div_texto_candidatas").hide();
                                        $("#'. $this->id .'_domicilio_no_encontrado").hide();
                                        $(".mensaje_error_campo").hide();
                                        $("#'. $this->id .'_texto_direccion").hide()

                                        //$(this).parent().parent().parent().parent().removeClass("error");

                                        $(".control-group.error").each(function(index, element) {
                                          $(element).removeClass("error");
                                        });

                                        if($("#'. $this->id .'_calle_input_id").val().trim() == ""){
                                          $(".control-group.error").each(function(index, element) {
                                            $(element).removeClass("error");
                                          });
                                          $("html, body").animate({ scrollTop: 0 }, "fast");
                                          $(".validacion-error").html("<span class=\'dialog-title\'>Hay <strong>1 error</strong> en el formulario</span><div class=\'alert alert-error\'>1. <a href=\'#'.$this->id.'_calle_input_id\' class=\'error_link\'>El campo \'<strong>Calle</strong>\' es obligatorio.</a></div><div class=\'alert alert-error\'>").fadeIn();
                                          $("#'.$this->id.'_calle_input_id").parent().parent().addClass("error");
                                          $("#'.$this->id.'_calle_input_id").parent().append("<div class=\'mensaje_error_campo\'>El campo \'Calle\' es obligatorio.</div>");
                                          return;
                                        }

                                        $.blockUI({
                                          message: \'<img src="'.site_url().'assets/img/ajax-loader.gif"></img>\',
                                           css: {
                                             width: \'70px\',
                                             height: \'60px\',
                                              border: \'none\',
                                              padding: \'15px\',
                                              backgroundColor: \'#000\',
                                              textAlign: \'center\',
                                              color: \'#fff\',
                                              top: \'40%\',
                                              left: \'50%\',
                                         }});

                                        $.ajax({
                                          type: "POST",
                                          dataType: "json",
                                          url: document.Constants.host + "/modulo_direcciones_ica/procesar_busqueda_avanzada_ajax",
                                          data: {
                                                  "method": "POST",
                                                  "departamento": $("#'. $this->id .'_departamento_select_id").val(),
                                                  "localidad": $("#'. $this->id .'_localidad_select_id").val(),
                                                  "calle": $("#'. $this->id .'_calle_input_id").val(),
                                                  "numero": $("#'. $this->id .'_numero_input_id").val(),
                                                  "esquina": $("#'. $this->id .'_esquina_input_id").val(),
                                                  "manzana": $("#'. $this->id .'_manzana_input_id").val(),
                                                  "solar": $("#'. $this->id .'_solar_input_id").val(),
                                                  "otros": $("#'. $this->id .'_otros_input_id").val()
                                                }
                                            })
                                            .done(function(data) {
                                              $.unblockUI();
                                              if(data.error){
                                                $(".control-group.error").each(function(index, element) {
                                                  $(element).removeClass("error");
                                                });

                                                if(data.lista == "error_interno"){
                                                  $("html, body").animate({ scrollTop: 0 }, "fast");
                                                  $(".validacion-error").html("Ocurrió un error interno al procesar la información, por favor intente nuevamente.").fadeIn();
                                                }
                                                else{
                                                  $("html, body").animate({ scrollTop: 0 }, "fast");
                                                  $(".validacion-error").html("<span class=\'dialog-title\'>Hay <strong>1 error</strong> en el formulario</span><div class=\'alert alert-error\'>1. <a href=\'#'.$this->id.'_domicilio_input_id\' class=\'error_link\'>El campo \'<strong>Domicilio</strong>\' es obligatorio.</a></div><div class=\'alert alert-error\'>").fadeIn();
                                                  $("#'.$this->id.'_domicilio_input_id").parent().parent().addClass("error");
                                                  $("#'.$this->id.'_domicilio_input_id").parent().append("<div class=\'mensaje_error_campo\'>El campo \'Domicilio\' es obligatorio.</div>");
                                                }
                                              }//termina errores de respuesta
                                              else if(data.lista.length == 0){
                                                  $("#'. $this->id .'_domicilio_no_encontrado").show();
                                              }
                                              else{
                                                $("#'. $this->id .'_div_direcciones_candidatas").html("").hide();
                                                $("#'. $this->id .'_div_texto_candidatas").html("").hide();

                                                $("#'. $this->id .'_div_texto_candidatas").append("<p>Se encontraron " + data.lista.length +" ubicaciones posibles, indique la correcta:</p>").show();

                                                jQuery.each(data.lista, function(key, domicilio_candidato) {
                                                  //se muestran botones con direcciones posbiles
                                                  $("#'. $this->id .'_div_direcciones_candidatas").append(\'<button class="btn btn-secundary btn-lg" id="'. $this->id .'_seleccionar_domicilio_\'+ key + \'" ><span class="icon-ok icon-white"></span> \' + domicilio_candidato.MatchAddress + \'</button>\').show();

                                                  //CLICK BOTON SELECCIONAR DOMICILIO
                                                  $("#'. $this->id .'_seleccionar_domicilio_"+ key).click(function(event) {
                                                    event.preventDefault();
                                                    $(".mensaje_error_campo").hide();

                                                    $("#'. $this->id .'_div_direcciones_candidatas").html("").hide();
                                                    $("#'. $this->id .'_div_texto_candidatas").hide();
                                                    $.blockUI({
                                                      message: \'<img src="'.site_url().'assets/img/ajax-loader.gif"></img>\',
                                                       css: {
                                                         width: \'70px\',
                                                         height: \'60px\',
                                                          border: \'none\',
                                                          padding: \'15px\',
                                                          backgroundColor: \'#000\',
                                                          textAlign: \'center\',
                                                          color: \'#fff\',
                                                          top: \'40%\',
                                                          left: \'50%\',
                                                     }});

                                                    var datos_seguimiento_guardar = {
                                                                  "'. $this->nombre .'_X": domicilio_candidato.X,
                                                                  "'. $this->nombre .'_Y": domicilio_candidato.Y,
                                                                  "'. $this->nombre .'_MatchAddress": domicilio_candidato.MatchAddress,
                                                                  "'. $this->nombre .'_StanAddress": domicilio_candidato.StanAddress,
                                                                  "'. $this->nombre .'_Score": domicilio_candidato.Score,
                                                                  "'. $this->nombre .'_departamento": $("select[name=departamento]").val(),
                                                                  "'. $this->nombre .'_localidad": $("select[name=localidad]").val(),
                                                                  "'. $this->nombre .'_calle": $("input[name=calle]").val(),
                                                                  "'. $this->nombre .'_numero": $("input[name=numero]").val(),
                                                                  "'. $this->nombre .'_esquina": $("input[name=esquina]").val(),
                                                                  "'. $this->nombre .'_solar": $("input[name=solar]").val(),
                                                                  "'. $this->nombre .'_manzana": $("input[name=manzana]").val(),
                                                                  "'. $this->nombre .'_otros": $("input[name=otros]").val(),
                                                                  "'. $this->nombre .'_validacion_siguiente": "1",
                                                                };

                                                    $.ajax({
                                                      type: "POST",
                                                      dataType: "json",
                                                      url: document.Constants.host + "/modulo_direcciones_ica/guardar_datos_seguimiento_ajax",
                                                      data: {
                                                              "method": "POST",
                                                              "datos_guardar": datos_seguimiento_guardar,
                                                              "etapa_id": "'.$etapa_id.'"
                                                            }
                                                        })
                                                        .done(function(data) {
                                                          $.unblockUI();
                                                        })
                                                        .fail(function(jqXHR, textStatus, errorThrown) {
                                                          $.unblockUI();
                                                        });

                                                    //MOSTRAR MAPA
                                                    $("#'. $this->id .'_mapa").html("");

                                                    var utm = "+proj=utm +zone=21 +south";
                                                    var yacare = "+proj=longlat +ellps=intl +towgs84=-155.0,171.0,37.0,0.0,0.0,0.0,0.0 +no_defs";
                                                    var xy = proj4(utm,yacare,[domicilio_candidato.X, domicilio_candidato.Y]);
                                                    lon2 = xy[0];
                                                    lat2= xy[1];
                                                    zoom2 = 17;


                                                    if((lat2=="" || lat2=="0") && (lon2=="" || lon2=="0")) {
                                                      lat2 = "-32.5476626";
                                                      lon2 = "-55.4411862";
                                                      zoom2 = 2;
                                                    }
                                                    var coord2 = ol.proj.fromLonLat([parseFloat(lon2), parseFloat(lat2)]);
                                                    //Capa del mapa
                                                    var mapLayer = new ol.layer.Tile({
                                                      source: new ol.source.OSM()
                                                    });
                                                    //Capa del punto
                                                    var iconStyle = new ol.style.Style({
                                                      image: new ol.style.Icon(({
                                                        anchor: [0.5, 46],
                                                        anchorXUnits: "fraction",
                                                        anchorYUnits: "pixels",
                                                        opacity: 0.75,
                                                        src: "'.base_url().'assets/img/pin.png"
                                                      }))
                                                    });
                                                    var iconFeature = new ol.Feature({
                                                      geometry: new ol.geom.Point(coord2),
                                                      name: "X"
                                                    });
                                                    iconFeature.setStyle(iconStyle);
                                                    var vectorSource = new ol.source.Vector({
                                                      features: [iconFeature]
                                                    });
                                                    var vectorLayer = new ol.layer.Vector({
                                                      source: vectorSource
                                                    });

                                                    //Dibujar el mapa con las dos layers
                                                    var map = new ol.Map({
                                                      target: "'. $this->id .'_mapa",
                                                      layers: [mapLayer, vectorLayer],
                                                      view: new ol.View({
                                                        center: coord2,
                                                        zoom: zoom2
                                                      })
                                                    });

                                                    $("#'. $this->id .'_texto_direccion").show().html("<strong>"+domicilio_candidato.MatchAddress+"</strong>");
                                                    $("#'. $this->id .'_texto_mapa").show();
                                                    // TERMINA MOSTRAR MAPA
                                                  });//TERMINA CLICK BOTON SELECCIONAR DOMICILIO
                                                });
                                              }//termina else
                                            }) //finaliza .done
                                            .fail(function(jqXHR, textStatus, errorThrown) {
                                              $.unblockUI();
                                            });
                                      });';

          //boton ir formulario busuqeda avanzada encontrado
          $display .= '$("#ir_form_avanzado").click(function(event) {
                          event.preventDefault();
                          $("#datos_domicilio").hide();
                          $("#datos_domicilio_extra").show();
                          $(".mensaje_error_campo").hide();

                          $.blockUI({
                            message: \'<img src="'.site_url().'assets/img/ajax-loader.gif"></img>\',
                             css: {
                               width: \'70px\',
                               height: \'60px\',
                                border: \'none\',
                                padding: \'15px\',
                                backgroundColor: \'#000\',
                                textAlign: \'center\',
                                color: \'#fff\',
                                top: \'40%\',
                                left: \'50%\',
                           }});

                          var datos_seguimiento_guardar = {
                                                            "'. $this->nombre .'_busqueda_avazada": "1",
                                                            "'. $this->nombre .'_validacion_siguiente": "0",
                                                          };

                          $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: document.Constants.host + "/modulo_direcciones_ica/guardar_datos_seguimiento_ajax",
                            data: {
                                    "method": "POST",
                                    "datos_guardar": datos_seguimiento_guardar,
                                    "etapa_id": "'.$etapa_id.'",
                                    "limpiar_datos_seguimiento": "1",
                                    "nombre_campo": "'.$this->nombre.'"
                                  }
                              })
                              .done(function(data) {
                                $.unblockUI();
                                $("#'. $this->id .'_mapa").html("");
                                $("#'. $this->id .'_texto_mapa").hide();
                                $("#'. $this->id .'_texto_direccion").hide();
                              })
                              .fail(function(jqXHR, textStatus, errorThrown) {
                                $.unblockUI();
                                $("#'. $this->id .'_mapa").html("");
                                $("#'. $this->id .'_texto_mapa").hide();
                                $("#'. $this->id .'_texto_direccion").hide();
                              });
                        });';

                        //boton ir formulario busuqeda avanzada NO direccion encontrada
                        $display .= '$("#ir_form_avanzado_no_encontrada").click(function(event) {
                                        event.preventDefault();
                                        $("#datos_domicilio").hide();
                                        $("#datos_domicilio_extra").show();
                                        $(".mensaje_error_campo").hide();

                                        $.blockUI({
                                          message: \'<img src="'.site_url().'assets/img/ajax-loader.gif"></img>\',
                                           css: {
                                             width: \'70px\',
                                             height: \'60px\',
                                              border: \'none\',
                                              padding: \'15px\',
                                              backgroundColor: \'#000\',
                                              textAlign: \'center\',
                                              color: \'#fff\',
                                              top: \'40%\',
                                              left: \'50%\',
                                         }});

                                        var datos_seguimiento_guardar = {
                                                                          "'. $this->nombre .'_busqueda_avazada": "1",
                                                                          "'. $this->nombre .'_validacion_siguiente": "0"
                                                                        };

                                        $.ajax({
                                          type: "POST",
                                          dataType: "json",
                                          url: document.Constants.host + "/modulo_direcciones_ica/guardar_datos_seguimiento_ajax",
                                          data: {
                                                  "method": "POST",
                                                  "datos_guardar": datos_seguimiento_guardar,
                                                  "etapa_id": "'.$etapa_id.'",
                                                  "limpiar_datos_seguimiento": "1",
                                                  "nombre_campo": "'.$this->nombre.'"
                                                }
                                            })
                                            .done(function(data) {
                                              $.unblockUI();
                                              $("#'. $this->id .'_mapa").html("");
                                              $("#'. $this->id .'_texto_mapa").hide();
                                              $("#'. $this->id .'_texto_direccion").hide();
                                            })
                                            .fail(function(jqXHR, textStatus, errorThrown) {
                                              $.unblockUI();
                                              $("#'. $this->id .'_mapa").html("");
                                              $("#'. $this->id .'_texto_mapa").hide();
                                              $("#'. $this->id .'_texto_direccion").hide();
                                            });

                                        //CLICK BOTON COFIRMAR DATOS EXTRA
                                        $("#'. $this->id .'_agregar_datos_extra").click(function(event) {
                                          event.preventDefault();
                                          $(".mensaje_error_campo").hide();

                                          $.blockUI({
                                            message: \'<img src="'.site_url().'assets/img/ajax-loader.gif"></img>\',
                                             css: {
                                               width: \'70px\',
                                               height: \'60px\',
                                                border: \'none\',
                                                padding: \'15px\',
                                                backgroundColor: \'#000\',
                                                textAlign: \'center\',
                                                color: \'#fff\',
                                                top: \'40%\',
                                                left: \'50%\',
                                           }});

                                          var datos_seguimiento_guardar = {
                                                                            "'. $this->nombre .'_calle_input_extra": $("input[name=calle_input_extra]").val(),
                                                                            "'. $this->nombre .'_numero_input_extra": $("input[name=numero_input_extra]").val(),
                                                                            "'. $this->nombre .'_esquina_input_extra": $("input[name=esquina_input_extra]").val(),
                                                                            "'. $this->nombre .'_manzana_input_extra": $("input[name=manzana_input_extra]").val(),
                                                                            "'. $this->nombre .'_solar_input_extra": $("input[name=solar_input_extra]").val(),
                                                                            "'. $this->nombre .'_otros_input_extra": $("input[name=otros_input_extra]").val(),
                                                                            "'. $this->nombre .'_departamento": $("select[name=departamento]").val(),
                                                                            "'. $this->nombre .'_localidad": $("select[name=localidad]").val(),
                                                                            "'. $this->nombre .'_validacion_siguiente": "1",
                                                                          };

                                          $.ajax({
                                            type: "POST",
                                            dataType: "json",
                                            url: document.Constants.host + "/modulo_direcciones_ica/guardar_datos_seguimiento_ajax",
                                            data: {
                                                    "method": "POST",
                                                    "datos_guardar": datos_seguimiento_guardar,
                                                    "etapa_id": "'.$etapa_id.'"
                                                  }
                                              })
                                              .done(function(data) {
                                                $.unblockUI();
                                              })
                                              .fail(function(jqXHR, textStatus, errorThrown) {
                                                $.unblockUI();
                                              });

                                        });//TERMINA CLICK BOTON COFIRMAR DATOS EXTRA
                                      });';

                      //boton ir formulario busuqeda simplificado
                      $display .= '$("#ir_form_simplificado").click(function(event) {
                                      event.preventDefault();
                                      $("#datos_domicilio").hide();
                                      $("#datos_domicilio_extra").show();
                                      $(".mensaje_error_campo").hide();

                                      $.blockUI({
                                        message: \'<img src="'.site_url().'assets/img/ajax-loader.gif"></img>\',
                                         css: {
                                           width: \'70px\',
                                           height: \'60px\',
                                            border: \'none\',
                                            padding: \'15px\',
                                            backgroundColor: \'#000\',
                                            textAlign: \'center\',
                                            color: \'#fff\',
                                            top: \'40%\',
                                            left: \'50%\',
                                       }});

                                      var datos_seguimiento_guardar = {
                                                                        "'. $this->nombre .'_busqueda_avazada": "0",
                                                                        "'. $this->nombre .'_validacion_siguiente": "0"
                                                                      };

                                      $.ajax({
                                        type: "POST",
                                        dataType: "json",
                                        url: document.Constants.host + "/modulo_direcciones_ica/guardar_datos_seguimiento_ajax",
                                        data: {
                                                "method": "POST",
                                                "datos_guardar": datos_seguimiento_guardar,
                                                "etapa_id": "'.$etapa_id.'",
                                                "limpiar_datos_seguimiento": "1",
                                                "nombre_campo": "'.$this->nombre.'"
                                              }
                                          })
                                          .done(function(data) {
                                            $.unblockUI();
                                            $("#datos_domicilio").show();
                                            $("#datos_domicilio_extra").hide();
                                            $("#'. $this->id .'_mapa").html("");
                                            $("#'. $this->id .'_texto_mapa").hide();
                                            $("#'. $this->id .'_texto_direccion").hide();
                                          })
                                          .fail(function(jqXHR, textStatus, errorThrown) {
                                            $.unblockUI();
                                            $("#datos_domicilio").show();
                                            $("#datos_domicilio_extra").hide();
                                            $("#'. $this->id .'_mapa").html("");
                                            $("#'. $this->id .'_texto_mapa").hide();
                                            $("#'. $this->id .'_texto_direccion").hide();
                                          });
                                    });';

          //finaliza $(document).ready(function()
          $display .=   '});';

          $display .= '</script>';

        }

        return $display;
    }
}

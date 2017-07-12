<?php

class Bloques extends CI_Controller {

  public function __construct() {
    parent::__construct();

    if(!$this->input->is_cli_request()) {
      redirect(site_url());
    }
  }

  public function impactar() {
    echo 'Iniciando modificaciones en bloques de datos, aguarde por favor...' . PHP_EOL;

    // --
    // -- Bloque: VALORACION
    // --
    $bloques_valoracion = Doctrine_Query::create()
        ->from('Campo c')
        ->where('c.nombre = ? and c.fieldset = ?', array('valoracion', 'ayudanos_a_mejorar'))
        ->execute();
    foreach($bloques_valoracion as $bloque_valoracion) {
      if($bloque_valoracion->Formulario->bloque_id) {
        echo "valoracion: " . $bloque_valoracion->id;
        $bloque_valoracion->validacion = '';
        $bloque_valoracion->save();
      }
    }

    $bloques_comentarios = Doctrine_Query::create()
        ->from('Campo c')
        ->where('c.nombre = ? and c.fieldset = ?', array('comentarios', 'ayudanos_a_mejorar'))
        ->execute();
    foreach($bloques_comentarios as $bloque_comentarios) {
      if($bloque_comentarios->Formulario->bloque_id) {
        echo "comentarios: " . $bloque_comentarios->id;
        $bloque_comentarios->validacion = '';
        $bloque_comentarios->save();
      }
    }

    $campos_valoracion = Doctrine_Query::create()
        ->from('Campo c')
        ->where('c.nombre = ? and c.fieldset regexp ?', array('valoracion', '.(.ayudanos_a_mejorar)'))
        ->execute();
    if($campos_valoracion) {
      foreach($campos_valoracion as $campo_valoracion) {
        $campo_valoracion->validacion = '';
        $campo_valoracion->save();
      }
    }

    $campos_comentarios = Doctrine_Query::create()
        ->from('Campo c')
        ->where('c.nombre = ? and c.fieldset regexp ?', array('comentarios', '.(.ayudanos_a_mejorar)'))
        ->execute();
    if($campos_comentarios) {
      foreach($campos_comentarios as $campo_comentarios) {
        $campo_comentarios->validacion = '';
        $campo_comentarios->save();
      }
    }

    // --
    // -- Bloque: DATOS DE CONTACTO
    // --
    $bloques_otro_telefono = Doctrine_Query::create()
        ->from('Campo c')
        ->where('c.nombre = ? and c.fieldset = ?', array('otro_telefono', 'datos_de_contacto'))
        ->execute();
        foreach ($bloques_otro_telefono as $bloque_otro_telefono) {
          if($bloque_otro_telefono->Formulario->bloque_id) {
            echo "otro telefono: " . $bloque_otro_telefono->id;
            $bloque_otro_telefono->validacion = explode('|', 'numeric');
            $bloque_otro_telefono->save();
          }
        }

    $campos_otro_telefono = Doctrine_Query::create()
        ->from('Campo c')
        ->where('c.nombre = ? and c.fieldset regexp ?', array('otro_telefono', '.(.datos_de_contacto)'))
        ->execute();
    if($campos_otro_telefono) {
      foreach($campos_otro_telefono as $campo_otro_telefono) {
        $campo_otro_telefono->validacion = explode('|', 'numeric');
        $campo_otro_telefono->save();
      }
    }

    // --
    // -- Bloque: IDENTIFICACION UY e IDENTIFICACION EXTRANJEROS
    // --
    $bloques_numero_de_documento = Doctrine_Query::create()
        ->from('Campo c')
        ->where('c.nombre = ? and c.fieldset = ?', array('numero_de_documento', 'datos_personales'))
        ->execute();
      foreach($bloques_numero_de_documento as $bloque_numero_de_documento) {
        if($bloque_numero_de_documento->Formulario->bloque_id) {
          echo "num de documento: " . $bloque_numero_de_documento->id;
          $bloque_numero_de_documento->etiqueta = 'Número de documento';
          $bloque_numero_de_documento->ayuda = 'Incluir dígito verificador';
          $bloque_numero_de_documento->save();
        }
      }

    $campos_numero_de_documento = Doctrine_Query::create()
        ->from('Campo c')
        ->where('c.nombre = ? and c.fieldset regexp ?', array('numero_de_documento', '.(.datos_personales)'))
        ->execute();
    if($campos_numero_de_documento) {
      foreach($campos_numero_de_documento as $campo_numero_de_documento) {
        $campo_numero_de_documento->etiqueta = 'Número de documento';
        $campo_numero_de_documento->ayuda = 'Incluir dígito verificador';
        $campo_numero_de_documento->save();
      }
    }

    // --
    // -- Bloque: Domicilio
    // --
    $codigo_js_localidad = "var Paysandu = {option1:{value:'PAYSANDU',text:'PAYSANDU'}, option2:{value:'CHAPICUY',text:'CHAPICUY'}, option3:{value:'NUEVO_PAYSANDU',text:'NUEVO PAYSANDU'}, option4:{value:'PIEDRAS_COLORADAS',text:'PIEDRAS COLORADAS'}, option5:{value:'TAMBORES',text:'TAMBORES'}, option6:{value:'QUEBRACHO',text:'QUEBRACHO'}, option7:{value:'PORVENIR',text:'PORVENIR'}, option8:{value:'GUICHON',text:'GUICHON'}, option9:{value:'LA_CORONILLA_P',text:'LA CORONILLA'}, option10:{value:'PUEBLO_GALLINAL',text:'PUEBLO GALLINAL'}};
      var Rio_Negro = {option1:{value:'FRY_BENTOS',text:'FRAY BENTOS'}, option2:{value:'GRECCO',text:'GRECCO'}, option3:{value:'NUEVO_BERLIN',text:'NUEVO BERLIN'}, option4:{value:'PARAJE_EL_AGUILA',text:'PARAJE EL AGUILA'}, option5:{value:'PARAJE_LAS_VIBORAS',text:'PARAJE LAS VIBORAS'}, option6:{value:'PASO_DE_LA_CRUZ',text:'PASO DE LA CRUZ'}, option7:{value:'PASO_DE_LOS_MELLIZOS',text:'PASO DE LOS MELLIZOS'}, option8:{value:'PUEBLO_SANCHEZ_GRANDE',text:'PUEBLO SANCHEZ GRANDE'}, option9:{value:'SAN_JAVIER',text:'SAN JAVIER'}, option10:{value:'SARANDI_DE_NAVARRO',text:'SARANDI DE NAVARRO'}, option11:{value:'YOUNG',text:'YOUNG'}};
      var Rivera = {option1:{value:'RIVERA',text:'RIVERA'}, option2:{value:'PASO_ATAQUES',text:'PASO ATAQUES'}, option3:{value:'VICHADERO',text:'VICHADERO'}, option4:{value:'MINAS_DE_CORRALES',text:'MINAS DE CORRALES'}, option5:{value:'LAPUENTE',text:'LAPUENTE'}, option6:{value:'MOIRONES',text:'MOIRONES'}, option7:{value:'TRANQUERAS',text:'TRANQUERAS'}, option8:{value:'ESTACION_ATAQUES',text:'ESTACION ATAQUES'}};
      var Rocha = {option1:{value:'19_DE_ABRIL',text:'19 DE ABRIL'}, option2:{value:'CHUY',text:'CHUY'}, option3:{value:'ROCHA',text:'ROCHA'}, option4:{value:'LA_CORONILLA_R',text:'LA CORONILLA'}, option5:{value:'CASTILLOS',text:'CASTILLOS'}, option6:{value:'EL_CANELON',text:'EL CANELON'}, option7:{value:'18_DE_JULIO',text:'18 DE JULIO'}, option8:{value:'VELAZQUEZ',text:'VELAZQUEZ'}, option9:{value:'LA_PALOMA_R',text:'LA PALOMA'}, option10:{value:'LASCANO',text:'LASCANO'}, option11:{value:'CEBOLLATI',text:'CEBOLLATI'}};
      var Salto = {option1:{value:'COLONIA_ITAPEBI',text:'COLONIA ITAPEBI'}, option2:{value:'SALTO',text:'SALTO'}, option3:{value:'CONSTITUCION',text:'CONSTITUCION'}, option4:{value:'BELEN',text:'BELEN'}, option5:{value:'BIASSINI',text:'BIASSINI'}, option6:{value:'SAN_ANTONIO_S',text:'SAN ANTONIO'}, option7:{value:'LAURELES',text:'LAURELES'}, option8:{value:'QUINTANA',text:'QUINTANA'}, option9:{value:'COLONIA_LAVALLEJA',text:'COLONIA LAVALLEJA'}, option10:{value:'PASO_CEMENTERO',text:'PASO CEMENTERO'}, option11:{value:'CERROS_DE_VERA',text:'CERROS DE VERA'}, option12:{value:'SARANDI_DEL_ARAPEY',text:'SARANDI DEL ARAPEY'}};
      var Soriano = {option1:{value:'SACACHISPAS',text:'SACACHISPAS'}, option2:{value:'DOLORES',text:'DOLORES'}, option3:{value:'MERCEDES',text:'MERCEDES'}, option4:{value:'VILLA_SORIANO',text:'VILLA SORIANO'}, option5:{value:'PALMITAS',text:'PALMITAS'}, option6:{value:'SANTA_CATALINA',text:'SANTA CATALINA'}, option7:{value:'AGRACIADA',text:'AGRACIADA'}, option8:{value:'JOSE_ENTRIQUE_RODO',text:'JOSE ENTRIQUE RODO'}, option9:{value:'CARDONA',text:'CARDONA'}, option10:{value:'PALMAR',text:'PALMAR'}};
      var San_Jose = {option1:{value:'SAN_JOSE',text:'SAN JOSE'}, option2:{value:'JUAN_SOLER',text:'JUAN SOLER'}, option3:{value:'ECILDA_PAULLIER',text:'ECILDA PAULLIER'}, option4:{value:'MAL_ABRIGO',text:'MAL ABRIGO'}, option5:{value:'CHAMIZO',text:'CHAMIZO'}, option6:{value:'VILLA_RODRIGUEZ',text:'VILLA RODRIGUEZ'}, option7:{value:'CIUDAD_DEL_PLATA',text:'CIUDAD DEL PLATA'}, option8:{value:'RAFAEL_PERAZA',text:'RAFAEL PERAZA'}, option9:{value:'LIBERTAD',text:'LIBERTAD'}};
      var Tacuarembo = {option1:{value:'TACUAREMBO',text:'TACUAREMBO'}, option2:{value:'ANSINA',text:'ANSINA'}, option3:{value:'PASO_DE_LOS_TOROS',text:'PASO DE LOS TOROS'}, option4:{value:'SAN_GREGORIO_DE_POLANCO',text:'SAN GREGORIO DE POLANCO'}, option5:{value:'CLARA',text:'CLARA'}, option6:{value:'BARRIO_LOPEZ',text:'BARRIO LOPEZ'}, option7:{value:'PASO_BONILLA',text:'PASO BONILLA'}, option8:{value:'CLAVIJO',text:'CLAVIJO'}, option9:{value:'PERALTA',text:'PERALTA'}, option10:{value:'ACHAR',text:'ACHAR'}, option11:{value:'CURTINA',text:'CURTINA'}, option12:{value:'PASO_DEL_CERRO',text:'PASO DEL CERRO'}, option13:{value:'CARAGUATA',text:'CARAGUATA'}};
      var aux_m = {option1:{value:'MONTEVIDEO',text:'MONTEVIDEO'}};
      var Artigas = {option1:{value:'BELLA_UNION',text:'BELLA UNION'}, option2:{value:'TOMAS_GOMENSORO',text:'TOMAS GOMENSORO'}, option3:{value:'PASO_FARIAS',text:'PASO FARIAS'}, option4:{value:'PUEBLO_SEQUEIRA',text:'PUEBLO SEQUEIRA'}, option5:{value:'JAVIER_DE_VIANA',text:'JAVIER DE VIANA'}, option6:{value:'CERRO_AMARILLO',text:'CERRO AMARILLO'}, option7:{value:'PINTADO_GRANDE',text:'PINTADO GRANDE'}, option8:{value:'ARTIGAS',text:'ARTIGAS'}, option9:{value:'CATALAN_GRANDE',text:'CATALAN GRANDE'}, option10:{value:'BERNABE_RIVERA',text:'BERNABE RIVERA'}, option11:{value:'BALTASAR_BRUM',text:'BALTASAR BRUM'},};
      var Can = {option1:{value:'SAN_RAMON',text:'SAN RAMON'}, option2:{value:'TALA',text:'TALA'}, option3:{value:'SAN_ANTONIO_C',text:'SAN ANTONIO'}, option4:{value:'EL_PINAR',text:'EL PINAR'}, option5:{value:'CIUDAD_DE_LA_COSTA',text:'CIUDAD DE LA COSTA'}, option6:{value:'PASO_CARRASCO',text:'PASO CARRASCO'}, option7:{value:'BARROS_BLANCOS',text:'BARROS BLANCOS'}, option8:{value:'SALINAS',text:'SALINAS'}, option9:{value:'ATLANTIDA',text:'ATLANTIDA'}, option10:{value:'PARQUE_DEL_PLATA',text:'PARQUE DEL PLATA'}, option11:{value:'LA_FLORESTA',text:'LA FLORESTA'}, option12:{value:'SOCA',text:'SOCA'}, option13:{value:'COLONIA_NICOLICH',text:'COLONIA NICOLICH'}, option14:{value:'MIGUES',text:'MIGUES'}, option15:{value:'SAN_JACINTO',text:'SAN JACINTO'}, option16:{value:'SAN_BAUTISTA',text:'SAN BAUTISTA'}, option17:{value:'SANTA_ROSA',text:'SANTA ROSA'}, option18:{value:'PANDO',text:'PANDO'}, option19:{value:'TOLEDO',text:'TOLEDO'}, option20:{value:'SUAREZ',text:'SUAREZ'}, option21:{value:'SAUCE',text:'SAUCE'}, option22:{value:'SANTA_LUCIA',text:'SANTA LUCIA'}, option23:{value:'CANELONES',text:'CANELONES'}, option24:{value:'LOS_CERRILLOS',text:'LOS CERRILLOS'}, option25:{value:'PROGRESO',text:'PROGRESO'}, option26:{value:'LA_PAZ',text:'LA PAZ'}, option27:{value:'LAS_PIEDRAS',text:'LAS PIEDRAS'}};
      var Cerro_Largo = {option1:{value:'PLACIDO_ROSAS',text:'PLACIDO ROSAS'}, option2:{value:'MELO',text:'MELO'}, option3:{value:'CONVENTOS',text:'CONVENTOS'}, option4:{value:'LAGO_MERIN',text:'LAGO MERIN'}, option5:{value:'RIO_BRANCO',text:'RIO BRANCO'}, option6:{value:'ISIDORO_NOBLIA',text:'ISIDORO NOBLIA'}, option7:{value:'TRES_BOLICHES',text:'TRES BOLICHES'}, option8:{value:'BANADO_DE_PAJAS',text:'BANADO DE PAJAS'}, option9:{value:'ARBOLITO',text:'ARBOLITO'}, option10:{value:'FRAILE_MUERTO',text:'FRAILE MUERTO'}, option11:{value:'CERRO_DE_LAS_CUENTAS',text:'CERRO DE LAS CUENTAS'}, option12:{value:'TUPAMBAE',text:'TUPAMBAE'}, option13:{value:'CENTURION',text:'CENTURION'}, option14:{value:'CUCHILLA_DEL_CARMEN',text:'CUCHILLA DEL CARMEN'}};
      var Col = {option1:{value:'COLONIA_DEL_SACRAMENTO',text:'COLONIA DEL SACRAMENTO'}, option2:{value:'ROSARIO',text:'ROSARIO'}, option3:{value:'CARMELO',text:'CARMELO'}, option4:{value:'NUEVA_PALMIRA',text:'NUEVA PALMIRA'}, option5:{value:'NUEVA_HELVECIA',text:'NUEVA HELVECIA'}, option6:{value:'COLONIA_VALDENSE',text:'COLONIA VALDENSE'}, option7:{value:'REAL_DE_SAN_CARLOS',text:'REAL DE SAN CARLOS'}, option8:{value:'JUAN_LACAZE',text:'JUAN LACAZE'}, option9:{value:'CONCHILLAS',text:'CONCHILLAS'}, option10:{value:'EL_CERRO_CARMELO',text:'EL CERRO (CARMELO)'}, option11:{value:'POLANCOS',text:'POLANCOS'}, option12:{value:'OMBUES_DE_LAVALLE',text:'OMBUES DE LAVALLE'}, option13:{value:'MIGUELETE',text:'MIGUELETE'}, option14:{value:'TARARIRAS',text:'TARARIRAS'}, option15:{value:'RIACHUELO',text:'RIACHUELO'}, option16:{value:'GRANJA_SAN_JOSE',text:'GRANJA SAN JOSE'}, option17:{value:'CUFRE',text:'CUFRE'}, option18:{value:'FLORENCIO_SANCHEZ',text:'FLORENCIO SANCHEZ'}};
      var Durazno = {option1:{value:'DURAZNO',text:'DURAZNO'}, option2:{value:'SANTA_BERNARDINA',text:'SANTA BERNARDINA'}, option3:{value:'VILLA_DEL_CARMEN',text:'VILLA DEL CARMEN'}, option4:{value:'CERRO_CHATO_D',text:'CERRO CHATO'}, option5:{value:'SARANDI_DEL_YI',text:'SARANDI DEL YI'}, option6:{value:'COLONIA_ROSSELL_Y_RIUS',text:'COLONIA ROSSELL Y RIUS'}, option7:{value:'CENTENARIO',text:'CENTENARIO'}, option8:{value:'FELICIANO',text:'FELICIANO'}, option9:{value:'CHACRAS_DE_DURAZNO',text:'CHACRAS DE DURAZNO'}, option10:{value:'CHACRAS_DE_SARANDI_DEL_YI',text:'CHACRAS DE SARANDI DEL YI'}, option11:{value:'CARLOS_REYLES',text:'CARLOS REYLES'}, option12:{value:'SAN_JORGE',text:'SAN JORGE'}, option13:{value:'BLANQUILLO',text:'BLANQUILLO'}, option14:{value:'LA_PALOMA_D',text:'LA PALOMA'},};
      var Flores = {option1:{value:'TRINIDAD',text:'TRINIDAD'}, option2:{value:'ANDRESITO',text:'ANDRESITO'}, option3:{value:'SAN_GREGORIO',text:'SAN GREGORIO'}, option4:{value:'ISMAEL_CORTINAS',text:'ISMAEL CORTINAS'}, option5:{value:'LA_CASILLA',text:'LA CASILLA'}, option6:{value:'CAMINO_A_LA_AVIACION',text:'CAMINO A LA AVIACION'}, option7:{value:'LA_UNION',text:'LA UNION'}, option8:{value:'JUAN_JOSE_CASTRO',text:'JUAN JOSE CASTRO'},};
      var Florida = {option1:{value:'FLORIDA',text:'FLORIDA'}, option2:{value:'25_DE_MAYO',text:'25 DE MAYO'}, option3:{value:'SARANDI_GRANDE',text:'SARANDI GRANDE'}, option4:{value:'LA_CRUZ',text:'LA CRUZ'}, option5:{value:'MENDOZA_GRANDE',text:'MENDOZA GRANDE'}, option6:{value:'CARDAL',text:'CARDAL'}, option7:{value:'JUNCAL',text:'JUNCAL'}, option8:{value:'ALEJANDRO_GALLINAL',text:'ALEJANDRO GALLINAL'}, option9:{value:'25_DE_AGOSTO',text:'25 DE AGOSTO'}, option10:{value:'POLANCO_DEL_YI',text:'POLANCO DEL YI'}, option11:{value:'CAPPILLA_DEL_SAUCE',text:'CAPPILLA DEL SAUCE'}, option12:{value:'GONI',text:'GONI'}, option13:{value:'NICO_PEREZ',text:'NICO PEREZ'}, option14:{value:'FRAY_MARCOS',text:'FRAY MARCOS'}, option15:{value:'CASUPA',text:'CASUPA'}};
      var Lavalleja = {option1:{value:'VILLA_DEL_ROSARIO',text:'VILLA DEL ROSARIO'}, option2:{value:'MINAS',text:'MINAS'}, option3:{value:'SOLIS_DE_MATAOJO',text:'SOLIS DE MATAOJO'}, option4:{value:'LA_CALERA',text:'LA CALERA'}, option5:{value:'POLANCO',text:'POLANCO'}, option6:{value:'MARMARAJA',text:'MARMARAJA'}, option7:{value:'PIRARAJA',text:'PIRARAJA'}, option8:{value:'JOSE_PEDRO_VARELA',text:'JOSE PEDRO VARELA'}, option9:{value:'JOSE_BATLLE_Y_ORDONEZ',text:'JOSE BATLLE Y ORDONEZ'}, option10:{value:'ZAPICAN',text:'ZAPICAN'}, option11:{value:'MARISCALA',text:'MARISCALA'}, option12:{value:'BARRIGA_NEGRA',text:'BARRIGA NEGRA'}};
      var Mal = {option1:{value:'JOSE_IGNACIO',text:'JOSE IGNACIO'}, option2:{value:'LA_BARRA',text:'LA BARRA'}, option3:{value:'PIRIAPOLIS',text:'PIRIAPOLIS'}, option4:{value:'PUNTA_DEL_ESTE',text:'PUNTA DEL ESTE'}, option5:{value:'LA_CORONILLA_M',text:'LA CORONILLA'}, option6:{value:'AIGUA',text:'AIGUA'}, option7:{value:'MALDONADO_NUEVO',text:'MALDONADO NUEVO'}, option8:{value:'BALNEARIO_SOLIS',text:'BALNEARIO SOLIS'}, option9:{value:'PUEBLO_EDEN',text:'PUEBLO EDEN'}, option10:{value:'PAN_DE_AZUCAR',text:'PAN DE AZUCAR'}, option11:{value:'SAN_CARLOS',text:'SAN CARLOS'}, option12:{value:'MALDONADO',text:'MALDONADO'}, option13:{value:'PUEBLO_GARZON',text:'PUEBLO GARZON'}};
      var Treinta_y_Tres = {option1:{value:'CERRO_CHATO_T',text:'CERRO CHATO'}, option2:{value:'CUCHILLA_DE_DIONISIO',text:'CUCHILLA DE DIONISIO'}, option3:{value:'VERGARA',text:'VERGARA'}, option4:{value:'JOSE_ENRIQUE_MARTINEZ_LA_CHARQUEADA',text:'JOSE ENRIQUE MARTINEZ (LA CHARQUEADA)'},option5:{value:'TREINTA_Y_TRES',text:'TREINTA Y TRES'}, option6:{value:'VILLA_SARA',text:'VILLA SARA'}, option7:{value:'COLONIA_DIONISIO_DIAZ',text:'COLONIA DIONISIO DIAZ'}, option8:{value:'RINCON',text:'RINCON'}, option9:{value:'SANTA_CLARA_DE_OLIMAR',text:'SANTA CLARA DE OLIMAR'}, option10:{value:'ISLA_PATRULLA',text:'ISLA PATRULLA'}};
      $(document).on(\"change\",\"[name='departamento']\",function(){
       $(\"[name='localidad']\").children().remove().end().append('<option selected value=\"\">Seleccionar</option>');
       if($(\"[name='departamento']\").val()!=\"\"){
        var items;
        switch($(\"[name='departamento']\").val()) {
        case 'montevideo':items = aux_m; break;
        case 'artigas':items = Artigas; break;
        case 'canelones':items = Can; break;
        case 'cerro_largo':items = Cerro_Largo; break;
        case 'colonia':items = Col; break;
        case 'durazno':items = Durazno; break;
        case 'flores':items = Flores; break;
        case 'florida': items = Florida; break;
        case 'lavalleja':items = Lavalleja; break;
        case 'maldonado':items = Mal; break;
        case 'treinta_y_tres':items = Treinta_y_Tres; break;
        case 'paysandu':items = Paysandu; break;
        case 'rio_negro':items = Rio_Negro; break;
        case 'rivera':items = Rivera; break;
        case 'rocha':items = Rocha; break;
        case 'salto':items = Salto; break;
        case 'san_jose':items = San_Jose; break;
        case 'soriano':items = Soriano; break;
        case 'tacuarembo':items = Tacuarembo; break;
        default:
        $(\"[name='localidad']\").children().remove().end().append('<option selected value=\"\">Seleccionar</option>');
        break;
         };
         $.each(items, function (i, item) {
        $(\"[name='localidad']\").append($('<option>', {
          value: item.value,
          text : item.text
        }));
         });
       }
      });
      $('body').ready(function(){
        $(\"[name='localidad']\").children().remove().end().append('<option selected value=\"\">Seleccionar</option>');
        if($(\"[name='departamento']\").val()!=\"\"){
        var items;
        switch($(\"[name='departamento']\").val()) {
         case 'montevideo':items = aux_m; break;
         case 'artigas':items = Artigas; break;
         case 'canelones':items = Can; break;
         case 'cerro_largo':items = Cerro_Largo; break;
         case 'colonia':items = Col; break;
         case 'durazno':items = Durazno; break;
         case 'flores':items = Flores; break;
         case 'florida': items = Florida; break;
         case 'lavalleja':items = Lavalleja; break;
         case 'maldonado':items = Mal; break;
         case 'treinta_y_tres':items = Treinta_y_Tres; break;
         case 'paysandu':items = Paysandu; break;
         case 'rio_negro':items = Rio_Negro; break;
         case 'rivera':items = Rivera; break;
         case 'rocha':items = Rocha; break;
         case 'salto':items = Salto; break;
         case 'san_jose':items = San_Jose; break;
         case 'soriano':items = Soriano; break;
         case 'tacuarembo':items = Tacuarembo; break;
         default:
          $(\"[name='localidad']\").children().remove().end().append('<option selected value=\"\">Seleccionar</option>');
          break;
        };
        $.each(items, function (i, item) {
        $(\"[name='localidad']\").append($('<option>', {
         value: item.value,
         text : item.text
        }));
        });
      };});";

    $bloques_localidad = Doctrine_Query::create()
        ->from('Campo c')
        ->where('c.nombre = ? and c.fieldset = ?', array('localidad', 'domicilio'))
        ->execute();
        foreach ($bloques_localidad as $bloque_localidad) {
          if($bloque_localidad->Formulario->bloque_id) {
            $bloque_localidad->tipo = 'select';
            $bloque_localidad->save();

            $campo=Campo::factory('javascript');
            $campo->formulario_id=$bloque_localidad->formulario_id;
            $campo->nombre='JS Localidad';
            $campo->etiqueta=$codigo_js_localidad; // -- Codigo JS
            $campo->readonly=1;
            $campo->valor_default='';
            $campo->ayuda='';
            $campo->ayuda_ampliada='';
            $campo->validacion='';
            $campo->dependiente_tipo='string';
            $campo->dependiente_campo='';
            $campo->dependiente_valor='';
            $campo->dependiente_relacion='';
            $campo->datos='';
            $campo->documento_id='';
            $campo->fieldset='domicilio.';
            $campo->extra='';
            $campo->documento_tramite='';
            $campo->email_tramite='';
            $campo->save();
          }
        }

    $campos_localidad = Doctrine_Query::create()
        ->from('Campo c')
        ->where('c.nombre = ? and c.fieldset regexp ?', array('localidad', '.(.domicilio)'))
        ->execute();
    if($campos_localidad) {
      foreach($campos_localidad as $campo_localidad) {
        $campo_localidad->tipo = 'select';
        $campo_localidad->save();

        try {
        $campo=new Campo();
        $campo->formulario_id=$campo_localidad->formulario_id;
        $campo->tipo='javascript';
        $campo->nombre='JS Localidad';
        $campo->etiqueta=$codigo_js_localidad; // -- Codigo JS
        $campo->readonly=1;
        $campo->valor_default='';
        $campo->ayuda='';
        $campo->ayuda_ampliada='';
        $campo->validacion='';
        $campo->dependiente_tipo='string';
        $campo->dependiente_campo='';
        $campo->dependiente_valor='';
        $campo->dependiente_relacion='';
        $campo->datos='';
        $campo->documento_id='';
        $campo->fieldset=$campo_localidad->fieldset;
        $campo->extra='';
        $campo->documento_tramite='';
        $campo->email_tramite='';
        $campo->save();
      }
      catch(Exception $e) {
        echo $e;
      }
      }
    }

    echo PHP_EOL;
    echo 'Proceso completado' . PHP_EOL;
  }
}

<?php

class FiebreAmarilla extends CI_Controller {

  public function __construct() {
    parent::__construct();

    //obliga a que se ejecute solo por linea de comandos
    if(!$this->input->is_cli_request()) {
      redirect(site_url());
    }
  }

  public function index(){
    echo "Falta invocar a la operacion procesar".PHP_EOL;
    return;
  }

  //******************** COMIENZA FUNCIONES PRIVADAS DE LA CLASE *************************************************/

  private function ejecutar_eventos_paso_antes($paso, $etapa){
    //Ejecutamos los eventos iniciales del paso
    $eventos_inicio_paso = Doctrine_Query::create()->from('Evento e')
            ->where('e.paso_id = ? AND e.instante = ?',array($paso->id,'antes'))
            ->execute();
    foreach ($eventos_inicio_paso as $e) {
            $r = new Regla($e->regla);
            if ($r->evaluar($etapa->id))
                $e->Accion->ejecutar($etapa, $e);
    }
  }

  private function ejecutar_eventos_paso_despues($paso, $etapa){

    //Ejecutamos los eventos finales del paso
    $eventos_fin_paso = Doctrine_Query::create()->from('Evento e')
            ->where('e.paso_id = ? AND e.instante = ?',array($paso->id,'despues'))
            ->execute();
    foreach ($eventos_fin_paso as $e) {
        $r = new Regla($e->regla);
        if ($r->evaluar($etapa->id))
            $e->Accion->ejecutar($etapa, $e);
      }
  }

  private function ejecutar_eventos_tarea_despues($etapa){
    //ejecuta los eventos despues de ejecutar la tarea
    $eventos_despues_tarea = Doctrine_Query::create()->from('Evento e')
            ->where('e.tarea_id = ? AND e.instante = ? AND e.paso_id IS NULL',array($etapa->Tarea->id,'despues'))
            ->execute();
    foreach ($eventos_despues_tarea as $e) {
        $r = new Regla($e->regla);
        if ($r->evaluar($etapa->id)) {
          $e->Accion->ejecutar($etapa, $e);
        }
    }
  }

  private function ejecutar_eventos_tarea_antes($etapa){
    //ejecuta los eventos antes de la ejecutar la tarea
    $eventos_antes_tarea =Doctrine_Query::create()->from('Evento e')
            ->where('e.tarea_id = ? AND e.instante = ? AND e.paso_id IS NULL',array($etapa->Tarea->id,'antes'))
            ->execute();
    foreach ($eventos_antes_tarea as $e) {
      $r = new Regla($e->regla);
      if ($r->evaluar($etapa->id)) {
        $e->Accion->ejecutar($etapa);
      }
    }
  }

  private function ejecutar_eventos($etapa){
    $this->ejecutar_eventos_tarea_antes($etapa);
    $secuencia = 0;
    //ejecuta eventos de los pasos
    foreach($etapa->getPasosEjecutables() as $paso) {
      $paso_final = sizeof($etapa->getPasosEjecutables())-1 == $secuencia;

        if($secuencia == 0 && !$paso_final){
          $this->ejecutar_eventos_paso_antes($paso, $etapa);
          $this->ejecutar_eventos_paso_despues($paso, $etapa);
        }
        //caso en que tiene 1 solo paso
        else if($secuencia == 0 && $paso_final){
          $this->ejecutar_eventos_paso_antes($paso, $etapa);
          $this->ejecutar_eventos_paso_despues($paso, $etapa);
          $this->ejecutar_eventos_tarea_despues($etapa);
          break;
        }
        //paso final de la etapa
        else if($secuencia != 0 && $paso_final){
          $this->ejecutar_eventos_paso_antes($paso, $etapa);
          $this->ejecutar_eventos_paso_despues($paso, $etapa);
          $this->ejecutar_eventos_tarea_despues($etapa);
          break;
        }
        else {
          $this->ejecutar_eventos_paso_antes($paso, $etapa);
          $this->ejecutar_eventos_paso_despues($paso, $etapa);
        }

        $secuencia++;
    }
  }

  private function verificar_condiciones_iniciales($usuario, $nombre_cuenta,$lista_tramites_avanzar){
    if(!$usuario){
      echo "Falta el parametro usuario".PHP_EOL;
      return false;
    }

    if(!$nombre_cuenta){
      echo "Falta el parametro cuenta".PHP_EOL;
      return false;
    }

    $cuenta = Doctrine::getTable('Cuenta')->findOneByNombre($nombre_cuenta);

    $usuario_encontrado = Doctrine::getTable('Usuario')->findOneByUsuarioAndCuentaId($usuario,$cuenta->id);

    if(!$usuario_encontrado){
      echo "El usuario {$usuario} no existe en la cuenta {$nombre_cuenta}".PHP_EOL;
      return false;
    }
    else{
        $tramite_para_obtenter_proceso = null;

        for ($i=0; $i < count($lista_tramites_avanzar); $i++) {
          $tramite_para_obtenter_proceso = Doctrine::getTable('Tramite')->find($lista_tramites_avanzar[$i]);

          if($tramite_para_obtenter_proceso){
            break;
          }
        }

        if(!$tramite_para_obtenter_proceso){
          echo "No se logro obtener el id del proceso, verificar lista de id de tramites".PHP_EOL;
          return false;;
        }

        if(!strpos($tramite_para_obtenter_proceso->Proceso->nombre, 'fiebre amarilla')){
          echo "El proceso solo se aplica para vacunacion de fiebre amarilla".PHP_EOL;
          return false;;
        }

        foreach ($tramite_para_obtenter_proceso->Proceso->Tareas as $tareas_proceso) {
          if(strpos($tareas_proceso->nombre, 'lisis de solicitud')){
            $tarea_encontrada = $tareas_proceso;
            break;
          }
        }

        if(!$tarea_encontrada){
          echo "No se encuentra la tarea analisis de solicitud".PHP_EOL;
          return false;;
        }

        $tiene_permisos = false;

        $grupos_tarea = explode(',', $tarea_encontrada->grupos_usuarios);

        foreach ($usuario_encontrado->GruposUsuarios as $grupo_del_usuario) {
          foreach ($grupos_tarea as $grupo_de_tarea_id) {
            if($grupo_del_usuario->id ==  $grupo_de_tarea_id){
              $tiene_permisos = true;
              break;
            }
          }
          if($tiene_permisos){
            break;
          }
        }

        if(!$tiene_permisos){
          echo "El usuario {$usuario} no tiene permisos".PHP_EOL;
          return false;
        }
    }

    return true;
  }

  private function guardar_dato_seguimiento($etapa_id, $nombre_dato, $valor_dato){
    $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_dato, $etapa_id);
    if ($dato) {
      $dato->delete();
    }
    $dato = new DatoSeguimiento();
    $dato->etapa_id = $etapa_id;
    $dato->nombre = $nombre_dato;
    $dato->valor = (string)$valor_dato;
    $dato->save();
  }

  //******************** TERMINA FUNCIONES PRIVADAS DE LA CLASE *************************************************/

  public function procesar($usuario, $nombre_cuenta){
    $lista_tramites_avanzar = array(21609,21472,21552,21560,21666,21705,21710,21752,21982,21992,22003,22004,22074,22153,22186,22257,22261,22329,22348,22359,22385,
    22390,22396,22503,22609,21553,21672,21725,21565,21997,22019,22080,22092,22185,22249,22559,22572,22611,22622,22625,22631,22734,22765,21459,21474,21483,21535,
    21653,21683,21688,21723,21942,21936,21974,22064,22113,22177,22240,22315,22312,22318,22393,22654,22682,22687,22789,21428,21462,21642,21724,22025,22022,21691,
    22102,22114,22123,22138,22176,22344,22483,22497,22510,22540,22756,22826,22895,21747,21801,21836,22051,22067,22192,22173,22198,22187,22365,22373,22404,22496,
    22573,22614,22623,22673,22779,21608,21646,21755,21510,21673,21758,21763,21756,21822,21852,21862,21960,21965,21973,22000,22065,22082,21951,22324,22343,21607,
    21480,22443,22465,22505,22507,22511,22519,22619,22679,22711,22776,22783,22787,22812,21371,21399,21411,21585,21670,21966,22263,22475,22516,22583,22629,22639,
    22643,22770,22772,22773,22809,22886,22159,21509,21511,21554,21695,21722,21766,21762,21648,21986,21996,22028,22059,22083,22124,22156,22262,22281,22282,22289,
    22297,22363,22438,22460,22668,22671,22713,22740,22831,22869,21434,21449,21633,21668,21682,21693,21736,21818,21865,21817,21892,21902,21919,21915,21958,21987,
    21995,22069,22273,22283,22287,22296,22308,22330,22337,22368,22376,22379,22386,22384,22446,22502,22391,22579,22692,22727,22897,22899,21389,21415,21454,21467,
    21477,21484,21612,21621,21355,21630,21643,21661,21685,21687,21703,21765,21805,21830,22032,22050,22137,22183,22120,22169,22184,22210,22228,22239,22286,22322,
    22351,22415,22450,22463,22613,22616,22628,22867,22876,22712,21447,21464,21660,21839,21976,22005,22079,22084,22158,22221,22305,22323,22409,22459,22481,22506,
    22596,22714,22720,22722,22808,21875,21962,22111,22335,22486,21650,22258,22782,22797,21804,21872,22195,22250,21437,21546,21469,21665,21726,21848,22119,22133,
    22164,22225,22233,22479,22488,22633,22638,20697,22680,22683,22859,22863,21514,21519,21520,21610,21712,22251,22584,22603,22829,22835,21536,21679,21684,21713,
    21721,21733,21739,21757,21930,22097,22294,22434,22621,22656,22703,22705,22706,22709,22724,22453,21718,21767,21742,21808,21835,22191,22222,22311,22325,22340,
    22035,22526,22515,22562,21429,21443,21567,21677,21702,21720,21738,21753,21807,21883,22094,22103,22155,22171,22256,22264,22272,22352,22520,22536,21571,22563,
    22599,22624,22632,22649,22691,22754,22837,22667,22875,22100,21460,21591,21611,21649,21811,21834,21904,21921,21931,21933,22180,22214,22212,22242,22252,22317,
    22196,22381,22331,22484,22493,22499,22558,22645,22644,22774,22840,22861,21400,21569,22018,21913,22482,22518,22531,22547,22689,22725,22728,22847,21421,21479,
    21573,21458,21681,21743,21773,21855,21890,21891,21907,21917,22105,22291,22274,22241,22382,22541,22554,22595,22585,22666,22674,22715,22732,22801,22842,22846,
    22891,21867,21420,22085,22132,22470,22472,22500,22512,22807,22818,21470,21634,21959,21971,21978,21984,21988,22122,22170,22182,22392,22401,22576,22685,22686,
    22760,22761,22766,22771,22784,22791,22798,21793,22023,22042,21564,21599,22075,22523,22723,22904,22165,22302,22328,22581,22635,22708,22811,21662,21667,22154,
    22237,22406,22428,21422,21603,21618,22478,22489,22498,22517,22551,22735,22743,22785,22208,21575,21870,21956,22061,22745,22762,22543,22145,22089,22527,22575,
    21455,22078,22417,22433,22439,21792,21823,22071,22267,22314,21979,22091,22456,21934,21954,22046,21499,22143,22140,21478,21488,22135,22399,22651,22676,22555,
    22843,22008,22026,22027,22710,22719,22877,22902,22227,21711,21927,21990,22047,22168,21735,22333,22608,22657,21968,22054,22076,22112,22236,22309,22421,21538,
    21615,22349,22366,22370,21874,21940,22068,22115,22134,22175,22190,22204,22218,22199,22229,22380,21764,21844,21860,21866,21877,21910,21916,22036,22058,22166,
    22226,22234,22244,22292,22293,22320,21559,21583,21594,21647,21696,21715,21728,21729,21732,22345,22419,22744,22820,22259,22878,22884,21963,22039,21821,21947,
    21967,22378,22235,22467,22474,22521,22533,22557,20252,21994,22002,22462,22167,22473,22852,22854,21714,21903,21911,21975,22574,22593,22677,21841,22755,21741,
    22040,21556,22872,21788,22718,21923,21953,21889,22316,22752,21985,21584,24017,23988,23951,23867,23857,23822,23808,23805,23785,23771,23752,23744,23730,23728,
    23712,23643,23638,23631,23630,23610,23602,23583,23581,23556,23535,23520,23512,23507,23506,23494,23489,23487,23481,23471,23469,23450,23444,23422,23408,23382,
    23314,23194,23147,23141,23124,23075,23066,23047,23001,22998,22997,22994,22952,22949,24059,24058,24057,24052,24040,24038,24035,24034,24032,24029,24027,24026,
    24024,24023,24022,24021,24018,24016,24012,24008,24006,24004,24000,23999,23998,23996,23995,23993,23987,23984,23982,23979,23975,23971,23969,23967,23964,23962,
    23959,23958,23956,23955,23954,23953,23952,23950,23948,23947,23944,23943,23942,23940,23941,23939,23935,23934,23931,23930,23929,23927,23926,23919,23913,23912,
    23911,23905,23904,23903,23902,23901,23900,23899,23896,23893,23892,23890,23889,23888,23886,23879,23878,23877,23875,23874,23866,23864,23861,23858,23856,23853,
    23850,23848,23847,23843,23841,23840,23839,23838,23832,23831,23829,23828,23827,23826,23824,23823,23820,23818,23817,23816,23815,23814,23813,23811,23809,23807,
    23804,23802,23800,23796,23792,23791,23789,23788,23787,23781,23780,23779,23778,23776,23775,23772,23769,23768,23767,23764,23762,23759,23756,23755,23754,23750,
    23748,23747,23743,23742,23738,23736,23734,23733,23732,23731,23727,23726,23722,23720,23719,23718,23716,23715,23711,23710,23708,23707,23703,23700,23699,23698,
    23697,23696,23695,23694,23693,23691,23689,23688,23686,23680,23678,23677,23676,23673,23672,23669,23668,23666,23665,23664,23662,23661,23658,23657,23656,23655,
    23652,23650,23649,23648,23647,23641,23639,23637,23635,23634,23629,23628,23626,23625,23624,23623,23621,23620,23619,23615,23613,23608,23605,23604,23603,23601,
    23600,23599,23598,23597,23596,23594,23591,23590,23588,23587,23585,23578,23576,23573,23571,23567,23568,23561,23560,23558,23555,23554,23553,23551,23545,23543,
    23540,23539,23538,23536,23534,23533,23528,23527,23526,23525,23518,23516,23513,23503,23502,23501,23500,23499,23496,23493,23491,23488,23485,23483,23477,23476,
    23475,23472,23468,23466,23464,23461,23460,23459,23457,23456,23454,23453,23451,23449,23445,23441,23439,23438,23434,23433,23429,23425,23424,23423,23419,23417,
    23415,23414,23413,23411,23410,23405,23404,23403,23400,23399,23397,23396,23394,23393,23392,23390,23383,23379,23378,23375,23374,23373,23368,23366,23365,23363,
    23362,23356,23355,23352,23351,23350,23347,23345,23342,23336,23335,23332,23325,23317,23315,23312,23309,23305,23303,23301,23299,23298,23297,23296,23295,23294,
    23291,23290,23289,23288,23287,23286,23285,23283,23282,23281,23280,23279,23274,23270,23269,23268,23265,23262,23260,23259,23258,23257,23251,23249,23248,23246,
    23245,23241,23240,23239,23234,23230,23225,23218,23215,23211,23208,23207,23201,23198,23197,23193,23192,23191,23190,23185,23184,23177,23176,23175,23174,23171,
    23169,23168,23166,23163,23158,23156,23155,23154,23152,23151,23150,23149,23145,23143,23139,23137,23134,23132,23130,23129,23123,23118,23117,23116,23115,23113,
    23112,23111,23109,23107,23106,23105,23104,23103,23102,23101,23100,23099,23098,23097,23095,23094,23093,23091,23090,23089,23088,23087,23085,23084,23082,23079,
    23078,23071,23070,23069,23068,23065,23064,23063,23061,23060,23055,23054,23053,23052,23051,23049,23045,23044,23043,23042,23041,23040,23038,23036,23031,23029,
    23024,23023,23022,23016,23015,23013,23012,23011,23009,23007,23006,23005,23004,23003,22999,22996,22995,22991,22990,22987,22986,22985,22984,22983,22981,22980,
    22979,22974,22970,22967,22964,22962,22961,22958,22957,22956,22955,22953,22948,22946,22944,22943,22928,22925,22920,22918,22914,22912,22911,22909,22908,22905);

      if(!$this->verificar_condiciones_iniciales($usuario, $nombre_cuenta, $lista_tramites_avanzar)){
        return;
      }

    foreach ($lista_tramites_avanzar as $tramite_id) {

      $tramite = Doctrine::getTable('Tramite')->find($tramite_id);

      if(!$tramite) {
        echo "El tramite {$tramite_id} no existe".PHP_EOL;
      }
      else if(count($tramite->getTodasEtapas()) > 2){
        echo "El tramite {$tramite_id} no se encuentra en la etapa de analisis de solicitud".PHP_EOL;
      }
      else {
        echo "Comienza tramite {$tramite_id} ".date('h:i:s A').PHP_EOL;
        $ultima_etapa = $tramite->getUltimaEtapa();

        $this->guardar_dato_seguimiento($ultima_etapa->id, '041_resolucion', 'R');
        $this->guardar_dato_seguimiento($ultima_etapa->id, '044_requiere_certificado', 'no');

        //generico para todas las etapas en Simple
        $id_transaccion = str_replace(" ", "_", strtoupper($ultima_etapa->Tarea->Proceso->ProcesoTrazabilidad->organismo_id)) . ':' . str_replace(" ", "_", strtoupper($ultima_etapa->Tarea->Proceso->ProcesoTrazabilidad->proceso_externo_id)) . ':' . $ultima_etapa->tramite_id;
        $this->guardar_dato_seguimiento($ultima_etapa->id, 'id_transaccion_traza', $id_transaccion);

        $this->ejecutar_eventos($ultima_etapa);

        $cuenta = Doctrine::getTable('Cuenta')->findOneByNombre($nombre_cuenta);

        $usuario_encontrado = Doctrine::getTable('Usuario')->findOneByUsuarioAndCuentaId($usuario,$cuenta->id);

        $ultima_etapa->asignar($usuario_encontrado->id);
        //avanza la tarea verificando condiciones de cierre y ejectua evento seteados en el instante despues de la tarea
        $ultima_etapa->avanzar();

        echo "Finaliza tramite {$tramite_id} ".date('h:i:s A').PHP_EOL;
      }
    }
  }

}

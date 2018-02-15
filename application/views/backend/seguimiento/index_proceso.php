<script>
    function editarVencimiento(etapaId) {
        $("#modal").load(site_url + "backend/seguimiento/ajax_editar_vencimiento/" + etapaId);
        $("#modal").modal();
        return false;
    }

    function toggleBusquedaAvanzada() {
        $("#busquedaAvanzada").slideToggle();
        return false;
    }
</script>

<ul class="breadcrumb">
    <li><a href="<?= site_url('backend/seguimiento/index') ?>">Seguimiento de Procesos</a><span class="divider">/</span></li>
    <li class="active"><?= $proceso->nombre ?></li>
</ul>
<h2><?= $proceso->nombre ?></h2>
<div class="row-fluid acciones-generales">
    <div class='pull-right'>
        <form class="form-search" method="GET" action="<?= current_url() ?>">
            <div class="input-append">
                <div class="hidden-accessible"><label for="buscar">Buscar</label></div>
                <input name="query" id="buscar" value="<?= $query ?>" type="text" class="search-query" />
                <button type="submit" class="btn">Buscar</button><!--Busqueda General-->
            </div>
        </form>
        <div class="busqueda_avanzada"><a href='#' onclick='toggleBusquedaAvanzada()'>Busqueda avanzada</a></div>
    </div>

    <?php //if(UsuarioBackendSesion::usuario()->rol!='seguimiento'): ?>
    <?php if(!UsuarioBackendSesion::has_rol('seguimiento')): ?>
    <div class="btn-group pull-left">
        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
            Operaciones
            <span class="caret"></span>
        </a>

        <ul class="dropdown-menu">
            <li><a href="<?= site_url('backend/seguimiento/borrar_proceso/' . $proceso->id) ?>" onclick="if (confirm('¿Esta seguro que desea eliminar todos los tramites de este proceso?'))
            return confirm('Atención. Esta operación no se podra deshacer y borrara todos los tramites en curso de este proceso. ¿Esta seguro que desea continuar?');
        else
            return false;">Borrar todo</a></li>
        </ul>
    </div>
    <?php endif ?>
</div>

<div id='busquedaAvanzada' style='display: <?=$busqueda_avanzada?'block':'none'?>;'>
  <form class='form-horizontal'>
    <fieldset>
      <legend>Filtros de búsqueda</legend>
            <input type='hidden' name='busqueda_avanzada' value='1' />
            <div class='row-fluid'>
                <div class='span6'>
                    <div class='control-group'>
                        <label class='control-label' for="termino">Término a buscar</label>
                        <div class='controls'>
                            <input name="query" value="<?= $query ?>" id="termino" type="text" />
                        </div>
                    </div>
                </div>
                <div class='span6'>
                    <div class='control-group'>
                        <span class='control-label'>Estado del trámite</span>
                        <div class='controls'>
                            <label class='radio' for="cualquiera"><input id="cualquiera" type='radio' name='pendiente' value='-1' <?= $pendiente == -1 ? 'checked' : '' ?>> Cualquiera</label>
                            <label class='radio' for="curso"><input id="curso" type='radio' name='pendiente' value='1' <?= $pendiente == 1 ? 'checked' : '' ?>> En curso</label>
                            <label class='radio' for="completado"><input id="completado" type='radio' name='pendiente' value='0' <?= $pendiente == 0 ? 'checked' : '' ?>> Completado</label>
                        </div>
                    </div>
                </div>
              </div>
              <div class='row-fluid'>
                <div class='span6'>
                  <script>
                    $(document).ready(function(){
                      $('#hasta').change(function() {

                        var desde = $("#desde").datepicker('getDate');
                        var hasta = $("#hasta").datepicker('getDate');

                        if (desde > hasta){
                          $("#fechas").addClass("error");
                          document.getElementById("save").disabled = true;
                          document.getElementById("fechaInicial").disabled = true;
                          document.getElementById("fechaFinal").disabled = true;
                          document.getElementById("fechaInicial").value = "";
                          document.getElementById("fechaFinal").value = "";
                          $("#fecha_update").removeClass("error");
                          var contenedor = document.getElementById("mensaje");
                          var pepe = document.getElementById("mesage");
                              contenedor.style.display = "block";
                              pepe.style.display = "none";
                          return true;
                        }else {
                          $("#fechas").removeClass("error");
                            document.getElementById("save").disabled = false;
                            document.getElementById("fechaInicial").disabled = false;
                            document.getElementById("fechaFinal").disabled = false;
                          var contenedor = document.getElementById("mensaje");
                            contenedor.style.display = "none";
                            return true;
                        }
                      });
                      $('#fechaFinal').change(function() {

                          var fechaInicial = $("#fechaInicial").datepicker('getDate');
                          var fechaFinal = $("#fechaFinal").datepicker('getDate');
                      if (fechaInicial > fechaFinal){

                          $("#fecha_update").addClass("error");
                          document.getElementById("save").disabled = true;
                          var contenedor = document.getElementById("mesage");
                          contenedor.style.display = "block";
                          return true;
                      }else {
                          $("#fecha_update").removeClass("error");
                          document.getElementById("save").disabled = false;
                        var contenedor = document.getElementById("mesage");
                            contenedor.style.display = "none";
                            return true;
                      }
                    });
                  });
                  </script>
                    <div class='control-group' id="fechas">
                        <label class='control-label' for="desde">Fecha de creación</label>
                        <div class='controls'>
                            <input type='text' name='created_at_desde' id="desde" placeholder='Desde' class='datepicker input-small' value='<?= $created_at_desde ?>' />
                            <div class="hidden-accessible"><label for="hasta">Fecha de creación hasta</label></div>
                            <input type='text' name='created_at_hasta' id="hasta" title="fecha de creación hasta" placeholder='Hasta' class='datepicker input-small' value='<?= $created_at_hasta ?>' />
                            <span class="mensaje_error_campo" id="mensaje" style="display:none;">El rango de fechas es incorrecto</span>
                        </div>
                    </div>
                  </div>
                  <div class='span6'>
                    <div class='control-group' id="fecha_update" >
                        <label class='control-label' for="fechaInicial">Fecha de último cambio</label>
                        <div class='controls'>
                            <input type='text' name='updated_at_desde' id="fechaInicial" placeholder='Desde' class='datepicker input-small' value='<?= $updated_at_desde ?>' />
                            <div class="hidden-accessible"><label for="fechaFinal">Fecha de último cambio hasta</label></div>
                            <input type='text' name='updated_at_hasta' id="fechaFinal" placeholder='Hasta' class='datepicker input-small' value='<?= $updated_at_hasta ?>' />
                            <span class="mensaje_error_campo" id="mesage" style="display:none;">El rango de fechas es incorrecto</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row-fluid'>
              <div class='span6'>
                  <div class='control-group'>
                      <label class='control-label' for="desde">Documento del Usuario</label>
                      <div class='controls'>
                          <input name="documento" value="<?= $documento ?>" id="documento" type="text" />
                      </div>
                  </div>
              </div>
              <div class='span6'>
                  <div class='control-group'>
                    <label for="pais" class="control-label">País*  </label>
                      <div class='controls'>
                        <select class="filter" id="pais"  name="pais">
                          <option value="uy" selected>Uruguay</option>
                          <option value="ar">Argentina</option>
                          <option value="br">Brasil</option>
                          <option value="py">Paraguay</option>
                          <option value="bo">Bolivia</option>
                          <option value="cl">Chile</option>
                          <option value="co">Colombia</option>
                          <option value="ec">Ecuador</option>
                          <option value="pe">Perú</option>
                          <option value="ve">Venezuela</option>
                          <option value="af">	Afganistán</option>
                          <option value="al">	Albania</option>
                          <option value="de">	Alemania</option>
                          <option value="dz">	Algeria</option>
                          <option value="ad">	Andorra</option>
                          <option value="ao">	Angola</option>
                          <option value="ai">	Anguila</option>
                          <option value="aq">	Antártida</option>
                          <option value="ag">	Antigua y Barbuda</option>
                          <option value="an">	Antillas Neerlandesas</option>
                          <option value="sa">	Arabia Saudita</option>
                          <option value="am">	Armenia</option>
                          <option value="aw">	Aruba</option>
                          <option value="au">	Australia</option>
                          <option value="at">	Austria</option>
                          <option value="az">	Azerbayán</option>
                          <option value="be">	Bélgica</option>
                          <option value="bs">	Bahamas</option>
                          <option value="bh">	Bahrein</option>
                          <option value="bd">	Bangladesh</option>
                          <option value="bb">	Barbados</option>
                          <option value="bz">	Belice</option>
                          <option value="bj">	Benín</option>
                          <option value="bt">	Bhután</option>
                          <option value="by">	Bielorrusia</option>
                          <option value="mm">	Birmania</option>
                          <option value="ba">	Bosnia y Herzegovina</option>
                          <option value="bw">	Botsuana</option>
                          <option value="bn">	Brunéi</option>
                          <option value="bg">	Bulgaria</option>
                          <option value="bf">	Burkina Faso</option>
                          <option value="bi">	Burundi</option>
                          <option value="cv">	Cabo Verde</option>
                          <option value="kh">	Camboya</option>
                          <option value="cm">	Camerún</option>
                          <option value="ca">	Canadá</option>
                          <option value="td">	Chad</option>
                          <option value="cn">	China</option>
                          <option value="cy">	Chipre</option>
                          <option value="va">	Ciudad del Vaticano</option>
                          <option value="km">	Comoras</option>
                          <option value="cg">	Congo</option>
                          <option value="cd">	Congo</option>
                          <option value="kp">	Corea del Norte</option>
                          <option value="kr">	Corea del Sur</option>
                          <option value="ci">	Costa de Marfil</option>
                          <option value="cr">	Costa Rica</option>
                          <option value="hr">	Croacia</option>
                          <option value="cu">	Cuba</option>
                          <option value="dk">	Dinamarca</option>
                          <option value="dm">	Dominica</option>
                          <option value="eg">	Egipto</option>
                          <option value="sv">	El Salvador</option>
                          <option value="ae">	Emiratos Árabes Unidos</option>
                          <option value="er">	Eritrea</option>
                          <option value="sk">	Eslovaquia</option>
                          <option value="si">	Eslovenia</option>
                          <option value="es">	España</option>
                          <option value="us">	Estados Unidos de América</option>
                          <option value="ee">	Estonia</option>
                          <option value="et">	Etiopía</option>
                          <option value="ph">	Filipinas</option>
                          <option value="fi">	Finlandia</option>
                          <option value="fj">	Fiyi</option>
                          <option value="fr">	Francia</option>
                          <option value="ga">	Gabón </option>
                          <option value="gm">	Gambia</option>
                          <option value="ge">	Georgia</option>
                          <option value="gh">	Ghana </option>
                          <option value="gi">	Gibraltar</option>
                          <option value="gd">	Granada</option>
                          <option value="gr">	Grecia </option>
                          <option value="gl">	Groenlandia </option>
                          <option value="gp">	Guadalupe </option>
                          <option value="gu">	Guam </option>
                          <option value="gt">	Guatemala </option>
                          <option value="gf">	Guayana Francesa</option>
                          <option value="gg">	Guernsey </option>
                          <option value="gn">	Guinea  </option>
                          <option value="gq">	Guinea Ecuatorial  </option>
                          <option value="gw">	Guinea-Bissau</option>
                          <option value="gy">	Guyana  </option>
                          <option value="ht">	Haití </option>
                          <option value="hn">	Honduras </option>
                          <option value="hk">	Hong kong</option>
                          <option value="hu">	Hungría </option>
                          <option value="in">	India </option>
                          <option value="id">	Indonesia </option>
                          <option value="ir">	Irán   </option>
                          <option value="iq">	Irak  </option>
                          <option value="ie">	Irlanda   </option>
                          <option value="bv">	Isla Bouvet </option>
                          <option value="im">	Isla de Man </option>
                          <option value="cx">	Isla de Navidad </option>
                          <option value="nf">	Isla Norfolk </option>
                          <option value="is">	Islandia  </option>
                          <option value="bm">	Islas Bermudas</option>
                          <option value="ky">	Islas Caimán   </option>
                          <option value="cc">	Islas Cocos (Keeling)  </option>
                          <option value="ck">	Islas Cook    </option>
                          <option value="ax">	Islas de Åland </option>
                          <option value="fo">	Islas Feroe    </option>
                          <option value="gs">	Islas Georgias del Sur y Sandwich del Sur</option>
                          <option value="hm">	Islas Heard y McDonald</option>
                          <option value="mv">	Islas Maldivas   </option>
                          <option value="fk">	Islas Malvinas</option>
                          <option value="mp">	Islas Marianas del Norte </option>
                          <option value="mh">	Islas Marshall  </option>
                          <option value="pn">	Islas Pitcairn</option>
                          <option value="sb">	Islas Salomón </option>
                          <option value="tc">	Islas Turcas y Caicos </option>
                          <option value="um">	Islas Ultramarinas Menores de Estados Unidos</option>
                          <option value="vg">	Islas Vírgenes Británicas </option>
                          <option value="vi">	Islas Vírgenes de los Estados Unidos</option>
                          <option value="il">	Israel</option>
                          <option value="it">	Italia </option>
                          <option value="jm">	Jamaica</option>
                          <option value="jp">	Japón </option>
                          <option value="je">	Jersey</option>
                          <option value="jo">	Jordania</option>
                          <option value="kz">	Kazajistán </option>
                          <option value="ke">	Kenia </option>
                          <option value="kg">	Kirgizstán</option>
                          <option value="ki">	Kiribati</option>
                          <option value="kw">	Kuwait</option>
                          <option value="lb">	Líbano</option>
                          <option value="la">	Laos</option>
                          <option value="ls">	Lesoto </option>
                          <option value="lv">	Letonia </option>
                          <option value="lr">	Liberia </option>
                          <option value="ly">	Libia  </option>
                          <option value="li">	Liechtenstein </option>
                          <option value="lt">	Lituania   </option>
                          <option value="lu">	Luxemburgo </option>
                          <option value="mx">	México </option>
                          <option value="mc">	Mónaco</option>
                          <option value="mo">	Macao</option>
                          <option value="mk">	Macedônia </option>
                          <option value="mg">	Madagascar </option>
                          <option value="my">	Malasia  </option>
                          <option value="mw">	Malawi  </option>
                          <option value="ml">	Mali   </option>
                          <option value="mt">	Malta  </option>
                          <option value="ma">	Marruecos</option>
                          <option value="mq">	Martinica</option>
                          <option value="mu">	Mauricio  </option>
                          <option value="mr">	Mauritania </option>
                          <option value="yt">	Mayotte </option>
                          <option value="fm">	Micronesia </option>
                          <option value="md">	Moldavia </option>
                          <option value="mn">	Mongolia  </option>
                          <option value="me">	Montenegro </option>
                          <option value="ms">	Montserrat</option>
                          <option value="mz">	Mozambique</option>
                          <option value="na">	Namibia </option>
                          <option value="nr">	Nauru </option>
                          <option value="np">	Nepal </option>
                          <option value="ni">	Nicaragua  </option>
                          <option value="ne">	Niger  </option>
                          <option value="ng">	Nigeria  </option>
                          <option value="nu">	Niue  </option>
                          <option value="no">	Noruega </option>
                          <option value="nc">	Nueva Caledonia </option>
                          <option value="nz">	Nueva Zelanda  </option>
                          <option value="om">	Omán     </option>
                          <option value="nl">	Países Bajos</option>
                          <option value="pk">	Pakistán</option>
                          <option value="pw">	Palau   </option>
                          <option value="ps">	Palestina  </option>
                          <option value="pa">	Panamá  </option>
                          <option value="pg">	Papúa Nueva Guinea</option>
                          <option value="pf">	Polinesia Francesa</option>
                          <option value="pl">	Polonia </option>
                          <option value="pt">	Portugal</option>
                          <option value="pr">	Puerto Rico </option>
                          <option value="qa">	Qatar  </option>
                          <option value="gb">	Reino Unido </option>
                          <option value="cf">	República Centroafricana </option>
                          <option value="cz">	República Checa   </option>
                          <option value="do">	República Dominicana  </option>
                          <option value="re">	Reunión</option>
                          <option value="rw">	Ruanda</option>
                          <option value="ro">	Rumanía </option>
                          <option value="ru">	Rusia  </option>
                          <option value="eh">	Sahara Occidental </option>
                          <option value="ws">	Samoa       </option>
                          <option value="as">	Samoa Americana  </option>
                          <option value="bl">	San Bartolomé  </option>
                          <option value="kn">	San Cristóbal y Nieves</option>
                          <option value="sm">	San Marino      </option>
                          <option value="mf">	San Martín (Francia)  </option>
                          <option value="pm">	San Pedro y Miquelón </option>
                          <option value="vc">	San Vicente y las Granadinas </option>
                          <option value="sh">	Santa Elena   </option>
                          <option value="lc">	Santa Lucía   </option>
                          <option value="st">	Santo Tomé y Príncipe </option>
                          <option value="sn">	Senegal   </option>
                          <option value="rs">	Serbia   </option>
                          <option value="sc">	Seychelles </option>
                          <option value="sl">	Sierra Leona  </option>
                          <option value="sg">	Singapur  </option>
                          <option value="sy">	Siria   </option>
                          <option value="so">	Somalia  </option>
                          <option value="lk">	Sri lanka  </option>
                          <option value="za">	Sudáfrica </option>
                          <option value="sd">	Sudán   </option>
                          <option value="se">	Suecia   </option>
                          <option value="ch">	Suiza   </option>
                          <option value="sr">	Surinám     </option>
                          <option value="sj">	Svalbard y Jan Mayen  </option>
                          <option value="sz">	Swazilandia  </option>
                          <option value="tj">	Tadjikistán </option>
                          <option value="th">	Tailandia  </option>
                          <option value="tw">	Taiwán    </option>
                          <option value="tz">	Tanzania </option>
                          <option value="io">	Territorio Británico del Océano Índico </option>
                          <option value="tf">	Territorios Australes y Antárticas Franceses   </option>
                          <option value="tl">	Timor Oriental</option>
                          <option value="tg">	Togo  </option>
                          <option value="tk">	Tokelau </option>
                          <option value="to">	Tonga  </option>
                          <option value="tt">	Trinidad y Tobago </option>
                          <option value="tn">	Tunez     </option>
                          <option value="tm">	Turkmenistán  </option>
                          <option value="tr">	Turquía   </option>
                          <option value="tv">	Tuvalu   </option>
                          <option value="ua">	Ucrania   </option>
                          <option value="ug">Uganda   </option>
                          <option value="uz">Uzbekistán </option>
                          <option value="vu">	Vanuatu  </option>
                          <option value="vn">	Vietnam  </option>
                          <option value="wf">	Wallis y Futuna  </option>
                          <option value="ye">	Yemen   </option>
                          <option value="dj">	Yibuti  </option>
                          <option value="zm">	Zambia  </option>
                          <option value="zw">	Zimbabue</option>
                        </select>
                      </div>
                  </div>
                </div>
          </div>

          <div class='row-fluid'>
            <div class='span6'>

                  <div class='control-group'>
                    <label for="tipo_documento" class="control-label">Tipo de documento*</label>
                    <div class="controls">
                      <select class="filter" id="tipo_documento"  name="tipo_documento">
                        <option value="ci" selected>CI</option>
                      </select>
                    </div>
                  </div>
              </div>
          </div>
      </fieldset>
      <div class='busqueda_avanzada'><button type="submit" class="btn btn-primary" id="save">Buscar</button></div>
      <!-- Buscar de Busqueda Avanzada -->
    </form>
</div>

<?= $this->pagination->create_links() ?>

<table class="table">
  <caption class="hide-text">Procesos</caption>
    <thead>
        <tr>
            <th><a href="<?= current_url() . '?query=' . $query . '&pendiente=' . $pendiente . '&busqueda_avanzada=' . $busqueda_avanzada . '&pais=' . $pais. '&documento=' . $documento. '&tipo_documento=' . $tipo_documento. '&created_at_desde=' . $created_at_desde . '&created_at_hasta=' . $created_at_hasta . '&updated_at_desde=' . $updated_at_desde . '&updated_at_hasta=' . $updated_at_hasta . '&order=id&direction=' . ($direction == 'asc' ? 'desc' : 'asc') ?>">Id <?= $order == 'id' ? $direction == 'asc' ? '<span class="icon-chevron-down"></span>' : '<span class="icon-chevron-up"></span>'  : '' ?></a></th>
            <th><a href="<?= current_url() . '?query=' . $query . '&pendiente=' . $pendiente . '&busqueda_avanzada=' . $busqueda_avanzada .  '&pais=' . $pais. '&documento=' . $documento. '&tipo_documento=' . $tipo_documento. '&created_at_desde=' . $created_at_desde . '&created_at_hasta=' . $created_at_hasta . '&updated_at_desde=' . $updated_at_desde . '&updated_at_hasta=' . $updated_at_hasta . '&order=pendiente&direction=' . ($direction == 'asc' ? 'desc' : 'asc') ?>">Estado <?= $order == 'pendiente' ? $direction == 'asc' ? '<span class="icon-chevron-down"></span>' : '<span class="icon-chevron-up"></span>'  : '' ?></a></th>
            <th>Etapa actual</th>
            <th>Documento</th>
            <th><a href="<?= current_url() . '?query=' . $query . '&pendiente=' . $pendiente . '&busqueda_avanzada=' . $busqueda_avanzada .  '&pais=' . $pais. '&documento=' . $documento. '&tipo_documento=' . $tipo_documento. '&created_at_desde=' . $created_at_desde . '&created_at_hasta=' . $created_at_hasta . '&updated_at_desde=' . $updated_at_desde . '&updated_at_hasta=' . $updated_at_hasta . '&order=created_at&direction=' . ($direction == 'asc' ? 'desc' : 'asc') ?>">Fecha de creación <?= $order == 'created_at' ? $direction == 'asc' ? '<span class="icon-chevron-down"></span>' : '<span class="icon-chevron-up"></span>'  : '' ?></th>
            <th><a href="<?= current_url() . '?query=' . $query . '&pendiente=' . $pendiente . '&busqueda_avanzada=' . $busqueda_avanzada .  '&pais=' . $pais. '&documento=' . $documento. '&tipo_documento=' . $tipo_documento. '&created_at_desde=' . $created_at_desde . '&created_at_hasta=' . $created_at_hasta . '&updated_at_desde=' . $updated_at_desde . '&updated_at_hasta=' . $updated_at_hasta . '&order=updated_at&direction=' . ($direction == 'asc' ? 'desc' : 'asc') ?>">Fecha de Último cambio <?= $order == 'updated_at' ? $direction == 'asc' ? '<span class="icon-chevron-down"></span>' : '<span class="icon-chevron-up"></span>'  : '' ?></a></th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tramites as $t): ?>
            <tr>
                <td><?= $t->id ?></td>
                <td><?= $t->pendiente ? 'En curso' : 'Completado' ?></td>
                <td>
                  <?php
                    $etapas_array = array();
                    $c=1;
                    //$documento = null;
                    $documento = $t->getTodasEtapas()[0]->getUsuarioInicial();
                  //show_error($t->getTodasEtapas()[0]->getUsuarioInicial());
                    foreach ($t->getEtapasActuales() as $e) {
                      if($c == 1) {
                        $documento = $e->getUsuarioInicial();
                      }

                      $c++;

                      $etapas_array[] = $e->Tarea->nombre . ($e->vencimiento_at ? ' <a href="#" onclick="return editarVencimiento(' . $e->id . ')" title="Cambiar fecha de vencimiento">(' . $e->getFechaVencimientoAsString() . ')</a>' : '');
                    }

                    echo implode(', ', $etapas_array);
                  ?>
                </td>
                <td><?= $documento; ?></td>
                <td><?= strftime('%c', mysql_to_unix($t->created_at)) ?></td>
                <td><?= strftime('%c', mysql_to_unix($t->updated_at)) ?></td>
                <td class="actions">
                    <a class="btn btn-primary" href="<?= site_url('backend/seguimiento/ver/' . $t->id) ?>"><span class="icon-white icon-eye-open"></span> Seguimiento<span class="hide-text"> de <?= $t->id ?></span></a>
                    <?php if(!UsuarioBackendSesion::has_rol('seguimiento')): ?>
                      <a class="btn btn-danger" href="<?= site_url('backend/seguimiento/borrar_tramite/' . $t->id) ?>" onclick="return confirm('¿Esta seguro que desea borrar estre trámite?')">
                      <span class="icon-white icon-trash"></span> Eliminar<span class="hide-text"> <?= $t->id ?></span></a>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->pagination->create_links() ?>

<div id="modal" class="modal hide fade" >

</div>

<script>
  $('#pais').change(function() {

    switch($(this).val()) {
      case 'uy':
            $('#tipo_documento').html($('<option>', {
              value: 'ci',
              text : 'CI'
            }));

          break;
      case 'ar':
          $('#tipo_documento').html($('<option>', {
            value: 'dni',
            text : 'DNI'
          }));
          $('#tipo_documento').append($('<option>', {
            value: 'psp',
            text : 'PSP'
          }));
          break;
        case 'br':
              $('#tipo_documento').html($('<option>', {
                value: 'ric',
                text : 'RIC'
              }));
              $('#tipo_documento').append($('<option>', {
                value: 'ci',
                text : 'CI'
              }));
              $('#tipo_documento').append($('<option>', {
                value: 'cie',
                text : 'CIE'
              }));
              $('#tipo_documento').append($('<option>', {
                value: 'psp',
                text : 'PSP'
              }));
              break;
          case 'py':
                $('#tipo_documento').html($('<option>', {
                  value: 'ci',
                  text : 'CI'
                }));
                $('#tipo_documento').append($('<option>', {
                  value: 'psp',
                  text : 'PSP'
                }));

              break;
          case 'bo':
                $('#tipo_documento').html($('<option>', {
                  value: 'cin',
                  text : 'CIN'
                }));
                $('#tipo_documento').append($('<option>', {
                  value: 'cie',
                  text : 'CIE'
                }));
                $('#tipo_documento').append($('<option>', {
                  value: 'psp',
                  text : 'PSP'
                }));

              break;

          case 'cl':
                $('#tipo_documento').html($('<option>', {
                  value: 'ci',
                  text : 'CI'
                }));
                $('#tipo_documento').append($('<option>', {
                  value: 'psp',
                  text : 'PSP'
                }));
              break;

          case 'co':
                $('#tipo_documento').html($('<option>', {
                  value: 'cc',
                  text : 'CC'
                }));
                $('#tipo_documento').append($('<option>', {
                  value: 'ti',
                  text : 'TI'
                }));
                $('#tipo_documento').append($('<option>', {
                  value: 'ce',
                  text : 'CE'
                }));
                $('#tipo_documento').append($('<option>', {
                  value: 'psp',
                  text : 'PSP'
                }));
                break;
            case 'ec':
                  $('#tipo_documento').html($('<option>', {
                    value: 'cc',
                    text : 'CC'
                  }));

                  $('#tipo_documento').append($('<option>', {
                    value: 'cie',
                    text : 'I'
                  }));
                  $('#tipo_documento').append($('<option>', {
                    value: 'psp',
                    text : 'PSP'
                  }));
                  break;

              case 'pe':
                    $('#tipo_documento').html($('<option>', {
                      value: 'dni',
                      text : 'DNI '
                    }));
                    $('#tipo_documento').append($('<option>', {
                      value: 'ce',
                      text : 'ce'
                    }));
                    $('#tipo_documento').append($('<option>', {
                      value: 'psp',
                      text : 'PSP'
                    }));

                    break;
            case 've':
                  $('#tipo_documento').html($('<option>', {
                    value: 'ci',
                    text : 'CI '
                  }));
                  $('#tipo_documento').append($('<option>', {
                    value: 'psp',
                    text : 'PSP'
                  }));

                  break;
            default:
                $('#tipo_documento').html($('<option>', {
                  value: 'psp',
                  text : 'PSP'
                }));
            break;

          }
  });
</script>

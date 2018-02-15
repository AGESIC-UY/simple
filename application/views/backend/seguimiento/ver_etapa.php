<ul class="breadcrumb">
    <li><a href="<?=site_url('backend/seguimiento')?>">Seguimiento de Procesos</a> <span class="divider">/</span></li>
    <li><a href="<?=site_url('backend/seguimiento/index_proceso/'.$etapa->Tramite->proceso_id)?>"><?=$etapa->Tramite->Proceso->nombre?></a> <span class="divider">/</span></li>
    <li><a href="<?=site_url('backend/seguimiento/ver/'.$etapa->tramite_id)?>">Trámite # <?= $etapa->tramite_id ?></a> <span class="divider">/</span></li>
    <li><a href="<?=site_url('backend/seguimiento/ver_etapa/'.$etapa->id)?>"><?=$etapa->Tarea->nombre?></a> <span class="divider">/</span></li>
    <li class="active">Paso <?=$secuencia+1?></li>
</ul>
<h2><?=$etapa->Tramite->Proceso->nombre?> - Trámite # <?= $etapa->tramite_id ?> - <?=$etapa->Tarea->nombre?></h2>
<div class="row-fluid">
    <div class="span3">
      <div class="validacion validacion-error"></div>
        <div class="well">
            <p>Estado: <?= $etapa->pendiente == 0 ? 'Completado' : 'Pendiente' ?></p>
            <p><?= $etapa->created_at ? 'Inicio: ' . strftime('%c', mysql_to_unix($etapa->created_at)) : '' ?></p>
            <p><?= $etapa->ended_at ? 'Término: ' . strftime('%c', mysql_to_unix($etapa->ended_at)) : '' ?></p>
            <script>
                $(document).ready(function(){
                    $("#reasignarLink").click(function(){
                        $("#reasignarFormCiudadano").hide();
                        $("#reasignarForm").show();
                        return false;
                    });

                    $("#reasignarLinkCiudadano").click(function(){
                        $("#reasignarForm").hide();
                        $("#reasignarCiudadano").hide();
                        $("#reasignarFormCiudadano").show();
                        $(".data-usuario").hide();

                        return false;
                    });

                    $("#liberarLink").click(function(){
                        $("#reasignarForm").hide();
                        $("#reasignarCiudadano").hide();
                        $("#reasignarFormCiudadano").hide();
                        $(".data-usuario").hide();
                        liberarFuncionario();
                        return false;
                    });
                });

                function liberarFuncionario() {
                  $.blockUI({
                    message: '<img src="'+ document.Constants.host + '/assets/img/ajax-loader.gif"></img>',
                     css: {
                       width: '70px',
                       height: '60px',
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        textAlign: 'center',
                        color: '#fff',
                        top: '40%',
                        left: '50%',
                   }});

                   $.ajax({
                     type: "post",
                     url: document.Constants.host + '/backend/seguimiento/liberar/'+ '<?= $etapa->id ?>',
                     data: $("#reasignarFormCiudadano").serialize(),
                     complete: function(resultado) {
                       $.unblockUI();
                        window.location.reload();
                      }
                    });
                }

                function buscarCiudadano() {
                  $.blockUI({
                    message: '<img src="'+ document.Constants.host + '/assets/img/ajax-loader.gif"></img>',
                     css: {
                       width: '70px',
                       height: '60px',
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        textAlign: 'center',
                        color: '#fff',
                        top: '40%',
                        left: '50%',
                   }});

                   $.ajax({
                     type: "post",
                     url: document.Constants.host + '/backend/seguimiento/buscar_ciudadano/'+ '<?= $etapa->id ?>',
                     data: $("#reasignarFormCiudadano").serialize(),
                     complete: function(resultado) {
                       $(".data-usuario").show();
                       $("#reasignarCiudadano").show();
                       if (resultado.responseText != ''){
                         //setea el resultado en el input

                         resultado = $.parseJSON(resultado.responseText);
                         $("#nombres").val(resultado.nombres);
                         $("#apellido_paterno").val(resultado.apellido_paterno);
                         $("#apellido_materno").val(resultado.apellido_materno);
                         $("#email").val(resultado.email);

                         $('#nombres').attr("disabled", true);
                         $('#nombres').attr("readonly", true);

                         $('#apellido_paterno').attr("disabled", true);
                         $('#apellido_paterno').attr("readonly", true);

                         $('#apellido_materno').attr("disabled", true);
                         $('#apellido_materno').attr("readonly", true);

                         $('#email').attr("disabled", true);
                         $('#email').attr("readonly", true);



                       }else{
                         $('#nombres').attr("disabled", false);
                         $('#nombres').attr("readonly", false);

                         $('#apellido_paterno').attr("disabled", false);
                         $('#apellido_paterno').attr("readonly", false);

                         $('#apellido_materno').attr("disabled", false);
                         $('#apellido_materno').attr("readonly", false);

                         $('#email').attr("disabled", false);
                         $('#email').attr("readonly", false);

                         $("#nombres").val("");
                         $("#apellido_paterno").val("");
                         $("#apellido_materno").val("");
                         $("#email").val("");

                       }
                      $.unblockUI();
                }
              });
            }
            </script>
            <p>Asignado a : <?= empty($etapa->usuario_id) ?'Ninguno': (!$etapa->Usuario->registrado?'No registrado':'<abbr class="tt" title="'.$etapa->Usuario->displayInfo().'">'.$etapa->Usuario->displayUsername().'</abbr>')?>
              <?php if($etapa->pendiente && $etapa->canUsuarioReasignar(UsuarioBackendSesion::usuario())):?>(<a id="reasignarLink" href="<?=site_url('seguimiento/reasignar')?>">Reasignar Funcionario</a>)<?php endif?>
              <?php if(!empty($etapa->usuario_id) && $etapa->pendiente && $etapa->canUsuarioLiberar(UsuarioBackendSesion::usuario())):?>(<a id="liberarLink" href="<?=site_url('backend/seguimiento/liberar/'.$etapa->id)?>">Liberar Funcionario</a>)<?php endif?>
              <?php if($etapa->canUsuarioReasignarCiudadano(UsuarioBackendSesion::usuario())):?>(<a id="reasignarLinkCiudadano" href="<?=site_url('seguimiento/reasignar')?>">Reasignar Ciudadano</a>)<?php endif?>
            </p>
            <?php if($etapa->usuario_original_id > 0 || ($etapa->usuario_original_historico != NULL && $etapa->usuario_original_historico != '')): ?>
              <p>Usuario Original (último) :  <?=!$etapa->usuario_original_id?'Ninguno':!$etapa->UsuarioOriginal->registrado?'No registrado':'<abbr class="tt" title="'.$etapa->UsuarioOriginal->displayInfo().'">'.$etapa->UsuarioOriginal->displayUsername().'</abbr>'?>
                (<abbr class="tt tooltip-tabla" title='<?=$etapa->displayHistoricoUsuarios()?>'>Ver más</abbr>)
              </p>

            <?php endif?>
            <?php $dato_funcionario = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('funcionario_actuando_como_ciudadano', $etapa->id); ?>
            <?php if($dato_funcionario) :?>
              <?php $funcionario = Doctrine::getTable('Usuario')->findOneById($dato_funcionario->valor); ?>
              <p>Funcionario actuante: <?php echo $funcionario->nombres.' '.$funcionario->apellido_paterno ?></p>
            <?php endif?>
            <?php $dato_usuario_empresa = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('usuario_actuando_como_empresa', $etapa->id); ?>
            <?php if($dato_usuario_empresa) :?>
              <?php $usuario_empresa = Doctrine::getTable('Usuario')->findOneById($dato_usuario_empresa->valor); ?>
              <p>Usuario actuante: <?php echo $usuario_empresa->nombres.' '.$usuario_empresa->apellido_paterno ?>
                (<abbr class="tt tooltip-tabla" title='<?=$etapa->displayHistoricoEjecucionesUsuarios()?>'>Ver más</abbr>)
              </p>
            <?php endif?>
            <?php $datos_cerrado_por = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('usuario_fin_etapa_generado', $etapa->id); ?>
            <?php if($datos_cerrado_por && $dato_funcionario) :?>
              <?php $funcionario = Doctrine::getTable('Usuario')->findOneById($datos_cerrado_por->valor); ?>
              <p>Tarea Finalizada por: <?php echo $funcionario->nombres.' '.$funcionario->apellido_paterno?></p>
            <?php endif?>
            <form id="reasignarForm" method="POST" action="<?=site_url('backend/seguimiento/reasignar_form/'.$etapa->id)?>" class="ajaxForm hide">
                <div class="validacion validacion-error"></div>
                <label for="usuario_id">¿A quien deseas asignarle esta etapa?</label>
                <select name="usuario_id" id="usuario_id">
                    <?php foreach($etapa->getUsuariosFromGruposDeUsuarioDeCuenta() as $u):?>
                    <option value="<?=$u->id?>" <?=$u->id==$etapa->usuario_id?'selected':''?>><?=$u->open_id?$u->nombres.' '.$u->apellido_paterno:$u->usuario.' '.$u->nombres.' '.$u->apellido_paterno?></option>
                    <?php endforeach?>
                </select>
                <button class="btn btn-primary" type="submit">Reasignar Funcionario</button>
            </form>
            <form id="reasignarFormCiudadano" method="POST" action="<?=site_url('backend/seguimiento/reasignar_form_ciudadano/'.$etapa->id)?>" class="ajaxForm hide">

                <label for="documento">¿A quien deseas asignarle esta etapa?</label>
                <div class='control-group'>
                     <label class='control-label' for="documento">Documento del Ciudadano</label>
                    <div class='controls'>
                      <input name="documento" value="<?= $documento ?>" id="documento" type="text" />
                     </div>
                </div>
                <div class='control-group'>
                    <label for="nombre" class="control-label">País*  </label>
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

              <div class='control-group'>
                <label for="nombre" class="control-label">Tipo de documento*</label>
                <select class="filter"id="tipo_documento"  name="tipo_documento">
                  <option value="ci" selected>CI</option>
                </select>
              </div>

              <div class='control-group'>
                <button class="btn btn-primary" type="button" onclick="buscarCiudadano();">Buscar Ciudadano</button>
              </div>

              <div class="control-group data-usuario">
                <label for="nombres" class="control-label">Nombres*</label>
                <div class="controls">
                  <input type="text" id="nombres" name="nombres"/>
                </div>
              </div>

              <div class="control-group data-usuario">
                <label for="apellido_paterno" class="control-label">Primer Apellido*:</label>
                <div class="controls">
                  <input type="text" id="apellido_paterno" name="apellido_paterno"/>
                </div>
              </div>

              <div class="control-group data-usuario">
                <label for="apellido_materno" class="control-label">Segundo Apellido*:</label>
                <div class="controls">
                  <input type="text" id="apellido_materno" name="apellido_materno"/>
                </div>
              </div>

              <div class="control-group data-usuario">
                <label for="email" class="control-label">Correo electrónico:</label>
                <div class="controls">
                  <input type="text" id="email" name="email"/>
                </div>
              </div>
              <button id="reasignarCiudadano" class="btn btn-primary" type="submit">Reasignar Ciudadano</button>
            </form>

          </div>
        </div>
        <div class="span9">
            <form class="form-horizontal dynaForm" onsubmit="return false;">
                <fieldset>
                  <?php if(isset($paso)): ?>
                    <legend><?= $paso->Formulario->nombre ?></legend>
                    <div class="validacion validacion-error"></div>
                    <?php foreach ($paso->Formulario->Campos as $c): ?>
                        <div class="control-group campo" data-id="<?= $c->id ?>" <?= $c->dependiente_campo ? 'data-dependiente-campo="' . $c->dependiente_campo.'" data-dependiente-valor="' . $c->dependiente_valor .'" data-dependiente-tipo="' . $c->dependiente_tipo.'" data-dependiente-relacion="'.$c->dependiente_relacion.'"' : '' ?> data-readonly="<?=$paso->modo=='visualizacion' || $c->readonly?>" >
                            <?=$c->displayConDatoSeguimiento($etapa->id,$paso->modo)?>
                        </div>
                    <?php endforeach ?>
                  <?php else: ?>
                    <div class="alert alert-info">La tarea no tiene pasos.</div>
                  <?php endif; ?>
                </fieldset>
                <ul class="form-action-buttons">
                    <li class="action-buttons-primary">
                        <ul>
                            <li>
                              <button class="hidden-accessible" type="submit">No hace nada</button>
                              <?php if ($secuencia + 1 < count($etapa->getPasosEjecutables())): ?><a class="btn btn-primary btn-lg" href="<?= site_url('backend/seguimiento/ver_etapa/' . $etapa->id . '/' . ($secuencia + 1)) ?>">Siguiente <span class="icon-chevron-right icon-white"></a><?php endif; ?>
                            </li>
                        </ul>
                    </li>
                    <li class="action-buttons-second">
                        <ul>
                            <li class="float-left">
                              <?php if ($secuencia>0): ?><a class="btn btn-link btn-lg" href="<?= site_url('backend/seguimiento/ver_etapa/' . $etapa->id . '/' . ($secuencia - 1)) ?>"><span class="icon-chevron-left"></span> Volver</a><?php endif; ?>
                              <!-- button class="btn-lg btn-link">&lt;&lt; Volver al paso anterior</button -->
                            </li>
                        </ul>
                    </li>
                </ul>
            </form>
        </div>
    </div>
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

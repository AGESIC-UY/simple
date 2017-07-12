<!--
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/js/clippy/clippy.css" media="all">
<script src="<?= base_url() ?>assets/js/clippy/clippy.js"></script>
<script type="text/javascript">
    var clip;
    var intervalId;
    var ocultarAsistenteHTML="<br /><br /><button onclick='javascript:clip_hide()'>Ocultar Asistente</button>";
    var textos=[
        "Veo que estas escribiendo una carta. Necesitas ayuda?",
        "Veo que necesitas ayuda.",
        "Necesitas que te de una mano?",
        "Estas seguro que no necesitas ayuda?",
        "Yo soy tu amigo. Tu quieres ser mi amigo?",
        "A veces yo aparezco por ninguna razon en particular. Como ahora.",
        "Tu computador parece estar prendido.",
        "Veo que estas tratando de trabajar. Necesitas que te moleste?",
        "Veo que tu vida no tiene sentido. Necesitas consejo?",
        "Parece que estas conectado a internet.",
        "Veo que has estado usando el mouse.",
        "Tu productividad ha ido decreciendo con el tiempo. Espero que estes bien.",
        "He detectado un movimiento del mouse. Esto es normal.",
        "Veo que tu postura no es la adecuada. Por favor sientate bien.",
        "Tu monitor se encuentra 100% operacional.",
        "Si necesitas ayuda, por favor pidemela.",
        "Tu mouse esta sucio. Limpialo para un rendimiento optimo.",
        "¿Quieres que me oculte?<br /><br /><button onclick='javascript:clip_hide()'>Si, por favor!</button><button>No, gracias</button>"
    ];
    clippy.load('Clippy', function(agent) {
        clip=agent;
        clip_start(false);

        //var animaciones=agent.animations();

        // Do anything with the loaded agent

    });

    function clip_start(vengativo){
        clip.show();


        if(!vengativo){
            intervalId=setInterval(function(){
                clip.animate();
                var randomTextId=Math.floor((Math.random()*textos.length));
                clip.speak(textos[randomTextId]+ocultarAsistenteHTML);
            },10000);
        }else{
            clip.speak("Volviiiiii! Te echaba de menos.");
            setTimeout(function(){
                clip.speak("¡Por que me dejaste! ¿Guardaste tu proceso? jajajaj");

            },5000);
            setTimeout(function(){
                $(".box").hide();
                clip.speak("Upppps");
            },10000);
            setTimeout(function(){
                $(".box").show();
            },15000);

            setTimeout(function(){

                intervalId=setInterval(function(){

                    clip.animate();
                    var randomTextId=Math.floor((Math.random()*textos.length));
                    clip.speak(textos[randomTextId]);
                },5000);
            },15000);

        }
    }

    function clip_hide(){
        $('#modalClip').modal();
        /*
        clip.stop();
        clip.hide();
        clearInterval(intervalId);

        setTimeout(function(){
            clip_start(true);
        },10000);
        */
    }
</script>

<div id="modalClip" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Ocultar Asistente Interactivo</h3>
    </div>
    <div class="modal-body">
        <p>Para ocultar el asistente interactivo debemos verificar de que eres humano. Para ello completa las letras del siguiente código Captcha:</p>

        <p id="captchaResult" style="color: red; display:none;">Código invalido.</p>

        <div style="text-align: center;"><iframe src="https://apis.modernizacion.cl/captcha" frameborder="0" scrolling="0" width="300" height="150"></iframe></div>
    </div>

    <div class="modal-footer">
        <button class="btn btn-primary" onclick="javascript:$('#captchaResult').show()">Validar Captcha</button>
        <button class="btn btn-link" data-dismiss="modal" aria-hidden="true">Cerrar</button>
    </div>
</div>
-->


<?php if($this->config->item('js_diagram')=='gojs'):?>
<link href="<?= base_url() ?>assets/css/diagrama-procesos2.css" property='stylesheet' rel="stylesheet">
<script src="<?= base_url() ?>assets/js/go/go.js" type="text/javascript"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/diagrama-procesos2.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/modelador-procesos2.js"></script>
<?php else: ?>
<link href="<?= base_url() ?>assets/css/diagrama-procesos.css" property='stylesheet' rel="stylesheet">
<script src="<?= base_url() ?>assets/js/jquery.jsplumb/jquery.jsPlumb-1.3.16-all-min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/diagrama-procesos.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/modelador-procesos.js"></script>
<?php endif ?>

<script type="text/javascript">
    $(document).ready(function(){
        procesoId=<?= $proceso->id ?>;
        drawFromModel(<?= $proceso->getJSONFromModel()?>,"<?=$proceso->width?>","<?=$proceso->height?>");
    });

</script>

<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('backend/procesos') ?>">Listado de Procesos</a> <span class="divider">/</span>
    </li>
    <li class="active"><?= $proceso->nombre ?></li>
</ul>
<a href="#" class="btn btn-ayuda btn-secundary" id="ayuda_contextual_modelador"><span class="icon-white icon-question-sign"></span> Ayuda</a>
<h2><?= $proceso->nombre ?></h2>
<ul class="nav nav-tabs">
    <li class="active"><a href="<?= site_url('backend/procesos/editar/' . $proceso->id) ?>">Diseñador</a></li>
    <li><a href="<?= site_url('backend/formularios/listar/' . $proceso->id) ?>">Formularios</a></li>
    <li><a href="<?= site_url('backend/documentos/listar/' . $proceso->id) ?>">Documentos</a></li>
    <li><a href="<?= site_url('backend/acciones/listar/' . $proceso->id) ?>">Acciones</a></li>
    <li id="accion-modelador"><a href="<?= site_url('backend/trazabilidad/listar/' . $proceso->id) ?>">Trazabilidad</a></li>
</ul>


<div id="areaDibujo">
  <div class="titulo-form">
    <h3><?= $proceso->nombre ?> <a href="#" title="Editar"><span class="icon-edit" style="vertical-align:middle;"></span></a></h3>
  </div>
    <div class="botonera btn-toolbar">
        <div class="btn-group">
            <button class="btn createBox" title="Crear tarea"><img src="<?= base_url() ?>assets/img/tarea.png" alt="tarea" /></button>
        </div>
        <div class="btn-group">
            <button class="btn createConnection" data-tipo="secuencial" title="Crear conexión secuencial" ><img src="<?= base_url() ?>assets/img/secuencial-bar.gif" alt="secuencial" /></button>
            <button class="btn createConnection" data-tipo="evaluacion" title="Crear conexión por evaluación" ><img src="<?= base_url() ?>assets/img/evaluacion.gif" alt="evaluación" /></button>
            <button class="btn createConnection" data-tipo="paralelo" title="Crear conexión paralela" ><img src="<?= base_url() ?>assets/img/paralelo.gif" alt="paralelo" /></button>
            <button class="btn createConnection" data-tipo="paralelo_evaluacion" title="Crear conexión paralela con evaluación" ><img src="<?= base_url() ?>assets/img/paralelo_evaluacion.gif" alt="paralelo con evaluación" /></button>
            <button class="btn createConnection" data-tipo="union" title="Crear conexión de unión" ><img src="<?= base_url() ?>assets/img/union.gif" alt="unión" /></button>
        </div>
    </div>
</div>
<div id="drawWrapper"><div id="draw"></div></div>
<div class="modal hide fade" id="modal">

</div>

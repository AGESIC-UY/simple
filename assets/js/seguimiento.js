$(document).ready(function(){
    });

function drawSeguimiento(actuales, completadas, automaticas, escaladas, avanzadas_cron_pagos, avanzadas_cron_agenda, avanzadas_cron_pagos_agendas){
    $(completadas).each(function(i,el){
        $("#draw #"+el.identificador).addClass("completado");
    });

    $(actuales).each(function(i,el){
        $("#draw #"+el.identificador).removeClass("completado");
        $("#draw #"+el.identificador).removeClass("automatica");
        $("#draw #"+el.identificador).removeClass("avanzada_cron_pagos");
        $("#draw #"+el.identificador).removeClass("escalada");
        $("#draw #"+el.identificador).removeClass("avanzada_cron_agenda");
        $("#draw #"+el.identificador).removeClass("avanzada_cron_pagos_agendas");
        $("#draw #"+el.identificador).addClass("actual");
    });

    $(automaticas).each(function(i,el){
      $("#draw #"+el.identificador).removeClass("completado");
      $("#draw #"+el.identificador).removeClass("actual");
      $("#draw #"+el.identificador).removeClass("avanzada_cron_pagos");
      $("#draw #"+el.identificador).removeClass("escalada");
      $("#draw #"+el.identificador).removeClass("avanzada_cron_agenda");
      $("#draw #"+el.identificador).removeClass("avanzada_cron_pagos_agendas");
      $("#draw #"+el.identificador).addClass("automatica");
    });

    $(escaladas).each(function(i,el){
      $("#draw #"+el.identificador).removeClass("completado");
      $("#draw #"+el.identificador).removeClass("actual");
      $("#draw #"+el.identificador).removeClass("automatica");
      $("#draw #"+el.identificador).removeClass("avanzada_cron_pagos");
      $("#draw #"+el.identificador).removeClass("avanzada_cron_agenda");
      $("#draw #"+el.identificador).removeClass("avanzada_cron_pagos_agendas");
      $("#draw #"+el.identificador).addClass("escalada");
    });

    $(avanzadas_cron_pagos).each(function(i,el){
      $("#draw #"+el.identificador).removeClass("completado");
      $("#draw #"+el.identificador).removeClass("actual");
      $("#draw #"+el.identificador).removeClass("automatica");
      $("#draw #"+el.identificador).removeClass("escalada");
      $("#draw #"+el.identificador).removeClass("avanzada_cron_agenda");
      $("#draw #"+el.identificador).removeClass("avanzada_cron_pagos_agendas");
      $("#draw #"+el.identificador).addClass("avanzada_cron_pagos");
    });
    
    $(avanzadas_cron_agenda).each(function(i,el){
      $("#draw #"+el.identificador).removeClass("completado");
      $("#draw #"+el.identificador).removeClass("actual");
      $("#draw #"+el.identificador).removeClass("automatica");
      $("#draw #"+el.identificador).removeClass("escalada");
      $("#draw #"+el.identificador).removeClass("avanzada_cron_pagos");
      $("#draw #"+el.identificador).removeClass("avanzada_cron_pagos_agendas");
      $("#draw #"+el.identificador).addClass("avanzada_cron_agenda");
    });
    
    $(avanzadas_cron_pagos_agendas).each(function(i,el){
      $("#draw #"+el.identificador).removeClass("completado");
      $("#draw #"+el.identificador).removeClass("actual");
      $("#draw #"+el.identificador).removeClass("automatica");
      $("#draw #"+el.identificador).removeClass("escalada");
      $("#draw #"+el.identificador).removeClass("avanzada_cron_pagos");
      $("#draw #"+el.identificador).addClass("avanzada_cron_pagos_agendas");
      $("#draw #"+el.identificador).removeClass("avanzada_cron_agenda");
    });

    $('#draw .box.actual, #draw .box.completado, #draw .box.automatica, #draw .box.escalada, #draw .box.avanzada_cron_pagos , #draw .box.avanzada_cron_agenda, #draw .box.avanzada_cron_pagos_agendas').each(
        function(){
            var el=this;
            $.get(site_url+"backend/seguimiento/ajax_ver_etapas/"+tramiteId+"/"+el.id,function(d){
                $(el).unbind('hover').popover({
                    html: true,
                    title: "Etapas ejecutadas",
                    content: d
                });
            });
        });
}

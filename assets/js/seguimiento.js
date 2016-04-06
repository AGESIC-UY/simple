$(document).ready(function(){
    
    });

function drawSeguimiento(actuales, completadas){
    $(completadas).each(function(i,el){
        $("#draw #"+el.identificador).addClass("completado");
    });
    $(actuales).each(function(i,el){
        $("#draw #"+el.identificador).removeClass("completado");
        $("#draw #"+el.identificador).addClass("actual");
    });
    
    $('#draw .box.actual,#draw .box.completado').each(
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
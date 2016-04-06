$(document).ready(function(){
    $("#dashboard").sortable({
        items: ".widget",
        handle: ".cabecera",
        revert: true,
        stop: widgetChangePositions
    });
    
    
});

function widgetChangePositions(){
    var widgets=new Array();
    $("#dashboard .widget").each(function(i,e){
        widgets.push($(e).data('id'));
    });
    var json=JSON.stringify(widgets);
    
    $.post(site_url+"backend/gestion/widget_change_positions/","posiciones="+json);
}

function widgetConfig(button){
    var widget=$(button).closest(".widget");
    $(widget).addClass('flip');
    return false;
}

function widgetConfigOk(form){ 
    var widget=$(form).closest(".widget");
    var widgetId=$(widget).data("id");
    $(widget).removeClass('flip');
    
    //Damos tiempo para que termine la animacion
    setTimeout(function(){$(widget).load(site_url+"backend/gestion/widget_load/"+widgetId)},1000);
}
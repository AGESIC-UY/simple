$(document).ready(function() {

});

function drawSeguimiento(actuales, completadas) {
    $(completadas).each(function(i, el) {
        var nodedata = diagram.model.findNodeDataForKey(el.identificador);
        diagram.model.setDataProperty(nodedata, "status", "completed");

        $.get(site_url + "backend/seguimiento/ajax_ver_etapas/" + tramiteId + "/" + el.identificador, function(d) {
            $("#draw").append("<div data-id='" + el.identificador + "' class='popover'><div class='arrow'></div><h3 class='popover-title'>Etapas ejecutadas</h3><div class='popover-content'>" + d + "</div></div>");
        });

        

    });
    $(actuales).each(function(i, el) {
        var nodedata = diagram.model.findNodeDataForKey(el.identificador);
        diagram.model.setDataProperty(nodedata, "status", "current");
        
        $.get(site_url + "backend/seguimiento/ajax_ver_etapas/" + tramiteId + "/" + el.identificador, function(d) {
            $("#draw").append("<div data-id='" + el.identificador + "' class='popover'><div class='arrow'></div><h3 class='popover-title'>Etapas ejecutadas</h3><div class='popover-content'>" + d + "</div></div>");
        });
    });
    



    diagram.addDiagramListener("ObjectSingleClicked", function(e) {
        var part = e.subject.part;
        var id;
        if (!(part instanceof go.Link)) {
            id = part.data.key;
            //console.log(part);
            //part.locationSpot=new go.Spot(1,0.5,0,0);
            $(".popover[data-id="+id+"]").css("left",part.location.x-diagram.position.x+200).css("top",part.location.y-diagram.position.y).toggle();
        }
    });


    /*
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
     */

}
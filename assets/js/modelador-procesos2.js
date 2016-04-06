$(document).ready(function(){
    var elements=new Array();
    var modo=null;
    var tipo=null;

    $("#areaDibujo .botonera").on("click",function(event){
        event.stopPropagation();
    });


    $("#areaDibujo .botonera .createBox").on("click",function(){
        $(this).addClass("disabled");
        modo="createBox";
    });

    $("#areaDibujo .botonera .createConnection").on("click",function(){
        $(this).addClass("disabled");
        $("#areaDibujo .box").css("cursor","crosshair")
        modo="createConnection";
        tipo=$(this).data("tipo");
    });

    $("#draw").on("click",function(event){
        if(modo=="createBox"){
            var left=event.pageX - $(this).position().left;
            var top=event.pageY - $(this).position().top;


            //Buscamos un id para asignarle


            var id=1;
            for (var i in diagram.model.nodeDataArray){
                if(id<=parseInt(diagram.model.nodeDataArray[i].key))
                    id=1+parseInt(diagram.model.nodeDataArray[i].key);
            }


            diagram.model.addNodeData({
                key: id,
                name: "Tarea",
                loc: new go.Point(parseInt(left),parseInt(top)),
                status: "pending"
            });
            modo=null;
            $("#areaDibujo .botonera .createBox").removeClass("disabled");
            $.post(site_url+"backend/procesos/ajax_crear_tarea/"+procesoId+"/"+id,"nombre=Tarea&posx="+left+"&posy="+top);
        }
    });


    diagram.addDiagramListener("ObjectSingleClicked", function(e) {
        var part = e.subject.part;

        if(modo=="createConnection"){
            elements.push(part.data.key);
            if(elements.length==2){
                var c=new Object();
                c.tipo=tipo;
                c.source=elements[0];
                c.target=elements[1];


                //Validaciones
                if(tipo=="secuencial" && diagram.findNodeForKey(c.source).findLinksOutOf().count){
                    alert("Las conexiones secuenciales no pueden ir hacia mas de una tarea");
                    return;
                }


                diagram.model.addLinkData({
                    from:c.source,
                    to:c.target,
                    type:c.tipo
                });




                modo=null;
                elements.length=0;
                $("#areaDibujo .botonera .createConnection").removeClass("disabled");
                $.post(site_url+"backend/procesos/ajax_crear_conexion/"+procesoId,"tarea_id_origen="+c.source+"&tarea_id_destino="+c.target+"&tipo="+c.tipo);

            }
        }
    });


    diagram.addDiagramListener("ObjectDoubleClicked", function(e) {
      var part = e.subject.part;
      var id;
      if (!(part instanceof go.Link)){
          id= part.data.key;
          $('#modal').load(site_url+"backend/procesos/ajax_editar_tarea/"+procesoId+"/"+id);
        $('#modal').modal({backdrop: 'static', keyboard: false, show: true});
      }else{
          id=part.data.from;
          $('#modal').load(site_url+"backend/procesos/ajax_editar_conexiones/"+procesoId+"/"+id);
          $('#modal').modal({backdrop: 'static', keyboard: false, show: true});

      }




  });

  //Asigno el evento para editar el proceso al hacerle click al titulo
    $(document).on("click","#areaDibujo h1 a",function(event){
        $('#modal').load(site_url+"backend/procesos/ajax_editar/"+procesoId);
        $('#modal').modal({backdrop: 'static', keyboard: false, show: true});
        return false;
    });

    diagram.addDiagramListener("SelectionMoved",updateModel);




});

function updateModel(){
    var model=new Object();
    //model.nombre=$("#areaDibujo h1").text();
    model.elements=new Array();
    //model.connections=new Array();


     var nodes = diagram.model.nodeDataArray;
 for (var i in nodes) {
     var node=nodes[i];
     var tmp=new Object();
        tmp.id=node.key;
        tmp.left=diagram.findNodeForKey(node.key).location.x;
        tmp.top=diagram.findNodeForKey(node.key).location.y;
        model.elements.push(tmp);
 }

/*
    $(diagram.model.nodeDataArray).each(function(i,e){

        //console.log(diagram.model.findNodeDataForKey(e.key))
        var tmp=new Object();
        tmp.id=e.key;
        tmp.left=$(e).position().left;
        tmp.top=$(e).position().top;
        model.elements.push(tmp);
    });
    */

    /*
    var connections=jsPlumb.getConnections();
    for(var i in connections){
        var tmp=new Object();
        tmp.id=connections[i].id;
        tmp.source=connections[i].sourceId;
        tmp.target=connections[i].targetId;
        model.connections.push(tmp);
    }
    */

    json=JSON.stringify(model);


    $.post(site_url+"backend/procesos/ajax_editar_modelo/"+procesoId,"modelo="+json);
}

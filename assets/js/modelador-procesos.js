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
        $( "#areaDibujo .box" ).draggable({disabled: true});
        $("#areaDibujo .box").css("cursor","crosshair")
       modo="createConnection";
       tipo=$(this).data("tipo");
    });

    $("#draw").on("click",function(event){
        if(modo=="createBox"){
            var left=event.pageX - $(this).position().left;
            var top=event.pageY - $(this).position().top;

            //Buscamos un id para asignarle
            var i=0;
            while(true){
                i++;
                var id="box_"+i;
                if($("#"+id).size()==0){
                    break;
                }
            }

            $(this).append("<div id='"+id+"' class='box' style='top: "+top+"px; left: "+left+"px;'>Tarea</div>");
            jsPlumb.draggable($("#areaDibujo .box"));
            modo=null;
            $("#areaDibujo .botonera .createBox").removeClass("disabled");
            $.post(site_url+"backend/procesos/ajax_crear_tarea/"+procesoId+"/"+id,"nombre=Tarea&posx="+left+"&posy="+top);
        }
    });
    $("#draw").on("click",".box",function(event){
        event.stopPropagation();
        if(modo=="createConnection"){
            elements.push(this.id);
            if(elements.length==2){
                var c=new Object();
                c.tipo=tipo;
                c.source=elements[0];
                c.target=elements[1];

                //Validaciones
                if(tipo=="secuencial" && jsPlumb.getConnections({source:c.source}).length){
                    alert("Las conexiones secuenciales no pueden ir hacia mas de una tarea");
                    return;
                }

                drawConnection(c);

                modo=null;
                elements.length=0;
                $("#areaDibujo .botonera .createConnection").removeClass("disabled");
                $("#areaDibujo .box").removeClass("selected");
                $( "#areaDibujo .box" ).draggable({disabled: false});
                $("#areaDibujo .box").css("cursor","move")
                //setJSPlumbEvents();
                $.post(site_url+"backend/procesos/ajax_crear_conexion/"+procesoId,"tarea_id_origen="+c.source+"&tarea_id_destino="+c.target+"&tipo="+c.tipo);

            }else{
                $(this).addClass("selected");
            }
        }
    });

    //Asigno los eventos a los boxes tareas
    $(document).on("dblclick doubletap","#draw .box",function(event){
        var id=$(this).attr("id");
        $('#modal').load(site_url+"backend/procesos/ajax_editar_tarea/"+procesoId+"/"+id);
        $('#modal').modal({backdrop: 'static', keyboard: false, show: true});
    });

    //Asigno los eventos a las lineas conectoras
    $(document).on("dblclick doubletap","#draw ._jsPlumb_connector",function(event){
        window.getSelection().removeAllRanges() //Previene bug de firefox que selecciona el texto de toda la pantalla.
        var conectorSeleccionado=$(event.target).closest("._jsPlumb_connector").get(0);
        var connections=jsPlumb.getConnections();
        $(connections).each(function(i,connection){
            if(connection.canvas==conectorSeleccionado){
                var id=$(connection.source).attr("id");
                $('#modal').load(site_url+"backend/procesos/ajax_editar_conexiones/"+procesoId+"/"+id);
                $('#modal').modal({backdrop: 'static', keyboard: false, show: true});
            }
        });
    });

    //Asigno los eventos a los conectores
    $(document).on("dblclick doubletap","#draw .conector",function(event){
        event.stopPropagation();
        var id=$(this).closest(".box").attr("id");
        $('#modal').load(site_url+"backend/procesos/ajax_editar_conexiones/"+procesoId+"/"+id);
        $('#modal').modal({backdrop: 'static', keyboard: false, show: true});
    });

    //Asigno el evento para editar el proceso al hacerle click al titulo
    $(document).on("click","#areaDibujo h3 a",function(event){
        $('#modal').load(site_url+"backend/procesos/ajax_editar/"+procesoId);
        $('#modal').modal({backdrop: 'static', keyboard: false, show: true});
        return false;
    });

    $( "#draw .box" ).liveDraggable({
        stop: updateModel
    });
});

function updateModel(){
    var model=new Object();
    //model.nombre=$("#areaDibujo h1").text();
    model.elements=new Array();
    //model.connections=new Array();

    $("#draw .box").each(function(i,e){
        var tmp=new Object();
        tmp.id=e.id;
        //tmp.name=$(e).text();
        tmp.left=$(e).position().left;
        tmp.top=$(e).position().top;
        model.elements.push(tmp);
    });

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

function dblClickConnectionEvent(connection){
    window.getSelection().removeAllRanges() //Previene bug de firefox que selecciona el texto de toda la pantalla.
    var tareaOrigenId=$(connection.source).attr("id");
                //var id=connection.getParameter("id");
                $('#modal').load(site_url+"backend/procesos/ajax_editar_conexiones/"+procesoId+"/"+tareaOrigenId);
                $('#modal').modal({backdrop: 'static', keyboard: false, show: true});
}

function dblClickEndpointEvent(endpoint){
    var tareaOrigenId=$(endpoint.element[0]).attr("id");
    $('#modal').load(site_url+"backend/procesos/ajax_editar_conexiones/"+procesoId+"/"+tareaOrigenId);
    $('#modal').modal({backdrop: 'static', keyboard: false, show: true});
}

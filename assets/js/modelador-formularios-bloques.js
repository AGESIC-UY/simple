$(document).ready(function(){
    $('#areaFormulario .edicionFormulario').sortable({
        //handle: '.handler',
        revert: true,
        stop: editarPosicionCampos
    });
});

function editarFormulario(formularioId){
    var bloque_id = $('input[name="bloque_id"]').val();
    $("#modal").load(site_url+"backend/bloques/ajax_editar/"+formularioId,"bloque_id="+bloque_id);
    $("#modal").modal({backdrop: 'static', keyboard: false});
    return false;
}

function editarPosicionCampos(){
    var campos=new Array();
    $("#areaFormulario .edicionFormulario .campo").each(function(i,e){
        campos.push($(e).data('id'));
    });
    var json=JSON.stringify(campos);

    $.post(site_url+"backend/bloques/editar_posicion_campos/"+formularioId,"posiciones="+json);
}

function editarCampo(campoId){
    var bloque_id = $('input[name="bloque_id"]').val();
    $("#modal").load(site_url+"backend/bloques/ajax_editar_campo/"+campoId,"bloque_id="+bloque_id);
    $("#modal").modal({backdrop: 'static', keyboard: false});
    return false;
}
function agregarCampo(formularioId,tipo){
    var bloque_id = $('input[name="bloque_id"]').val();
    $("#modal").load(site_url+"backend/bloques/ajax_agregar_campo/"+formularioId+"/"+tipo,"bloque_id="+bloque_id);
    $("#modal").modal({backdrop: 'static', keyboard: false});
    return false;
}

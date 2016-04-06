$(document).ready(function(){
    $('#areaFormulario .edicionFormulario').sortable({
        //handle: '.handler',
        revert: true,
        stop: editarPosicionCampos
    });
});

function editarFormulario(formularioId){
    $("#modal").load(site_url+"backend/formularios/ajax_editar/"+formularioId);
    $("#modal").modal({backdrop: 'static', keyboard: false});
    return false;
}

function editarOrganismo(formularioId){
    $("#modal").load(site_url+"backend/formularios/ajax_editar_organismo/"+formularioId);
    $("#modal").modal({backdrop: 'static', keyboard: false});
    return false;
}

function editarProcesoExterno(formularioId){
    $("#modal").load(site_url+"backend/formularios/ajax_editar_proceso_externo/"+formularioId);
    $("#modal").modal({backdrop: 'static', keyboard: false});
    return false;
}

function editarPosicionCampos(){
    var campos=new Array();
    $("#areaFormulario .edicionFormulario .campo").each(function(i,e){
        campos.push($(e).data('id'));
    });
    var json=JSON.stringify(campos);

    $.post(site_url+"backend/formularios/editar_posicion_campos/"+formularioId,"posiciones="+json);
}

function editarCampo(campoId){
    $("#modal").load(site_url+"backend/formularios/ajax_editar_campo/"+campoId);
    $("#modal").modal({backdrop: 'static', keyboard: false});
    return false;
}
function agregarCampo(formularioId,tipo){
    $("#modal").load(site_url+"backend/formularios/ajax_agregar_campo/"+formularioId+"/"+tipo);
    $("#modal").modal({backdrop: 'static', keyboard: false});
    return false;
}

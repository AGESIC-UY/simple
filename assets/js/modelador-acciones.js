function seleccionarAccion(procesoId){
    $("#modal").load(site_url+"backend/acciones/ajax_seleccionar/"+procesoId);
    $("#modal").modal({backdrop: 'static', keyboard: false});
    return false;
}

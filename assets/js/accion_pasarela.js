
$(document).ready(function () {

    function escapeHtml(text) {
        return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
    }
// -- Para que el tooltip funcione en los contenidos cargados con ajax
    $(".tab-eventos").on("click", ".delete", function () {
        $(this).closest("tr").remove();
        return false;
    });
    //Permite agregar nuevos eventos
    $(".tab-eventos .form-agregar-acciones button").click(function () {
        var $form = $(".tab-eventos .form-agregar-acciones");

var pos_total = $("#pos_total").val();
        var pos = (1+parseInt(pos_total));
        var accionId = $form.find(".eventoAccion option:selected").val();
        var accionNombre = $form.find(".eventoAccion option:selected").text();
        var regla = $form.find("#condicion").val();
        var instante = $form.find("#instante option:selected").val();
        var variableErrorSoap = $form.find(".eventoVariableErrorSoap").val();
        var variableDescripcionErrorSoap = $form.find(".eventoDescripcionErrorSoap").val();
        var variableDescripcionTraza = accionNombre;
        var variableTipoRegistroTraza = $form.find(".eventoTipoRegistroTraza option:selected").val();
        var variableTipoRegistroTrazaText = $form.find(".eventoTipoRegistroTraza option:selected").text();
        var variableTraza = $form.find(".eventoTraza option:selected").val();
        var variableTrazaText = $form.find(".eventoTraza option:selected").text();
        var variableTrazaEtiqueta = $form.find(".eventoEtiquetaTraza option:selected").val();
        var variableTrazaEtiquetaText = $form.find(".eventoEtiquetaTraza option:selected").text();
        var variableTrazaVisibilidad = $form.find(".eventoVisibleTraza option:selected").val();
        var variableTrazaVisibilidadText = $form.find(".eventoVisibleTraza option:selected").text();
        if(variableTraza!=="1"){
            variableDescripcionTraza="";
            variableTipoRegistroTrazaText="";            
        }
        var html = "<tr>";
        html += '<td><a title="Editar" target="_blank" href="' + site_url + 'backend/acciones/editar/' + accionId + '">' + accionNombre + '</td>';
        html += "<td>" + regla + "</td>";
        html += "<td>" + instante + "</td>";
        html += "<td>" + variableTrazaText + "</td>";
        html += "<td>" + variableTipoRegistroTrazaText + "</td>";
        //html += "<td>" + variableDescripcionTraza + "</td>";
        html += "<td>" + variableTrazaEtiquetaText + "</td>";
        html += "<td>" + variableTrazaVisibilidadText + "</td>";
        html += "<td>" + variableErrorSoap + "</td>";
        html += '<td>';
        html += '<input type="hidden" name="eventos[' + pos + '][accion_id]" value="' + accionId + '" />';
        html += '<input type="hidden" name="eventos[' + pos + '][regla]" value="' + escapeHtml(regla) + '" />';
        html += '<input type="hidden" name="eventos[' + pos + '][instante]" value="' + instante + '" />';
        html += '<input type="hidden" name="eventos[' + pos + '][traza]" value="' + variableTraza + '" />';
        html += '<input type="hidden" name="eventos[' + pos + '][tipo_registro_traza]" value="' + variableTipoRegistroTraza + '" />';
        html += '<input type="hidden" name="eventos[' + pos + '][descripcion_traza]" value="' + variableDescripcionTraza + '" />';
        html += '<input type="hidden" name="eventos[' + pos + '][etiqueta_traza]" value="' + variableTrazaEtiqueta + '" />';
        html += '<input type="hidden" name="eventos[' + pos + '][visible_traza]" value="' + variableTrazaVisibilidad + '" />';
        html += '<input type="hidden" name="eventos[' + pos + '][descripcion_error_soap]" value="' + variableDescripcionErrorSoap + '" />';
        html += '<input type="hidden" name="eventos[' + pos + '][variable_error_soap]" value="' + variableErrorSoap + '" />';
        html += '<a class="delete" title="Eliminar" href="#"><span class="icon-trash"></span></a>';
        html += '</td>';
        html += "</tr>";
        $("#pos_total").val(pos);
        console.log(html);
        $(".tab-eventos tbody").append(html);

        return false;
    });
});

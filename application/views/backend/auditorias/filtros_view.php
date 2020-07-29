<br />
<fieldset id="busqueda_filtro" style="display:block;">
    <legend>Filtros de búsqueda</legend>
    <table width="100%"  class="auditoria">
        <tr>
            <td>
                <label for="busqueda_modificacion" class="control-label">Fecha de último cambio</label>
                <input class="datepicker" type="text" id="busqueda_modificacion_desde" name="busqueda_modificacion_desde" placeholder="Desde" value=""/>
                <input class="datepicker" type="text" id="busqueda_modificacion_hasta" name="busqueda_modificacion_hasta" placeholder="Hasta"  value=""/>
            </td>
            <td>
                <label for="tipo_operacion" class="control-label">Operación realizada</label>
                <select id="tipo_operacion_auditoria" class="span3">
                    <option value=""></option>
                    <option value="insert">Alta</option>
                    <option value="update">Modificación</option>
                    <option value="delete">Baja</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>

            </td>
            <td>
                <input type="text" class="hidden" value="<?= site_url(uri_string()) ?>" id="url_auditoria"/>
                <input type="button" id="btn_buscar_filtro_auditoria" class="btn btn-primary" value="Buscar" />                        
                <a id="limpiar_filtro" href="?filtro=1">Limpiar</a>
            </td>
        </tr>
    </table>
    <div id="lbl_error_filtro"></div>
</fieldset>

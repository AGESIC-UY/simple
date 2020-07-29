<h1><?= $proceso->nombre ?></h1>
<?php if (count($etapas) > 0): ?>

    <div class="busqueda_avanzada"><a href='#' id="busqueda_filtro_toggle">Búsqueda avanzada</a></div>
    <br />
    <fieldset id="busqueda_filtro" style="display:none;">
        <legend>Filtros de búsqueda</legend>

        <table width="100%">
            <tr>
                <td>
                    <label for="busqueda_id_tramite" class="control-label">Id Trámite</label>
                    <input class="filter" data-col="id" type="text" id="busqueda_id_tramite" name="busqueda_id_tramite" value="<?php if (isset($busqueda_id_tramite)) echo $busqueda_id_tramite; ?>"/>

                    <label for="busqueda_id_etapa" class="control-label">Id Etapa</label>
                    <input class="filter" data-col="id_etapa" type="text" id="busqueda_id_etapa" name="busqueda_id_etapa" value="<?php if (isset($busqueda_id_etapa)) echo $busqueda_id_etapa; ?>"/>

                </td>
                <td>
                    <label for="busqueda_nombre_tarea" class="control-label">Etapa</label>
                    <input class="filter" data-col="nombre_tarea" type="text" id="busqueda_nombre_tarea" name="busqueda_nombre_tarea"  value="<?php if (isset($busqueda_nombre_tarea)) echo $busqueda_nombre_tarea; ?>"/>

                    <label for="busqueda_modificacion" class="control-label">Fecha de último cambio</label>
                    <input class="datepicker_" type="text" id="busqueda_modificacion_desde" name="busqueda_modificacion_desde" placeholder="Desde" value="<?php if (isset($busqueda_modificacion_desde)) echo $busqueda_modificacion_desde; ?>"/>
                    <input class="datepicker_" type="text" id="busqueda_modificacion_hasta" name="busqueda_modificacion_hasta" placeholder="Hasta"  value="<?php if (isset($busqueda_modificacion_hasta)) echo $busqueda_modificacion_hasta; ?>"/>
                    <input class="filter hidden" data-col="modificación" type="text" id="busqueda_modificacion" name="busqueda_modificacion" />
                    <input class="filter hidden" data-col="id_proceso" type="text" value="<?= $proceso->id ?>" id="id_proceso" name="id_proceso" />

                    <br /><br />
                    <input type="button" id="btn_buscar_filtro_firma" class="btn btn-primary" value="Buscar" />
                    <a id="limpiar_filtro_firma" href="?filtro=1">Limpiar</a>
                </td>
            </tr>
        </table>
        <div id="lbl_error_filtro"></div>
    </fieldset>
    <div class="ajaxForm dynaForm form-horizontal">
        <table id="mainTable" class="table">
            <caption class="hide-text"></caption>
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectall" style="display: block;margin: auto;"></th>
                    <th>Id Trámite</th>
                    <th>Id Etapa</th>
                    <th>Nombre Etapa</th>
                    <th>Modificación</th>
                    <th>Vencimiento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="list">
                <?php foreach ($etapas as $e): ?>
                    <?php $e = Doctrine::getTable('Etapa')->find($e['id']); ?>
                    <tr <?= $e->getPrevisualizacion() ? 'data-toggle="popover" data-html="true" data-title="<h4>Previsualización</h4>" data-content="' . htmlspecialchars($e->getPrevisualizacion()) . '" data-trigger="hover" data-placement="bottom"' : '' ?>>
                        <td><input type="checkbox" class="check_firma" name="check_firma[]" value="1" style="display: block;margin: auto;"></td>
                        <td data-title="id_tramite" class="list_id_tramite"><?= $e->Tramite->id ?></td>
                        <td data-title="id_etapa" class="list_id_tramite"><?= $e->id ?></td>
                        <td data-title="tarea_nombre" class="list_etapa"><?= $e->Tarea->nombre ?></td>
                        <td class="time list_modificacion" data-title="Modificación"><?= strftime('%d.%b.%Y', mysql_to_unix($e->updated_at)) ?> <br /><?= strftime('%H:%M:%S', mysql_to_unix($e->updated_at)) ?></td>
                        <td data-title="Vencimiento" class="list_vencimiento"><?= $e->vencimiento_at ? strftime('%c', strtotime($e->vencimiento_at)) : 'N/A' ?></td>
                        <td class="actions" data-title="Acciones">
                            <a href="<?= site_url('etapas/ver_firma/' . $e->id) ?>" class="btn btn-primary preventDoubleRequest"><span class="icon-edit icon-eye-open"></span> Ver <span class="hide-text"><?= $e->Tramite->Proceso->nombre ?></span></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div id="paginado_div">
            <?php if (isset($this->pagination)) echo $this->pagination->create_links(); ?>
        </div>
    </div>
    <ul class="form-action-buttons">
        <li class="action-buttons-primary">
            <ul>
                <li>
                    <button class="btn btn-secundary btn-lg" type="submit" id="btn_siguiente_firma_lote"><span class="icon-edit icon-white"></span> Firmar documentos</button>
                    <button class="btn btn-primary btn-lg" type="submit" disabled id="btn_finalizar_firma_lote"><span class="icon-download-alt icon-white"></span> Confirmar y Exportar</button>
                    <!--<button class="btn btn-primary btn-lg" type="submit" id="btn_update_firma_lote"><span class="icon-refresh icon-white"></span> Actualizar</button>-->
                    <a href="<?= site_url('etapas/firma_por_lotes/') ?>" class="btn btn-primary btn-lg"><i class="icon-white icon-th-list"></i> Finalizar</a>
                </li>
            </ul>
        </li>
    </ul>
<?php else: ?>
    <table id="mainTable" class="table"></table>
    <p>No hay documentos por firmar para el tramite seleccionado.</p>
<?php endif; ?>

<input type="text" id="limite_firma_lote" value="<?= $limite_firma_lote ?>" style="display: none;">
<script>
    // add multiple select/unselect functionality
    $("#selectall").on("click", function () {
        $(".check_firma").prop("checked", this.checked);
    });

// if all checkbox are selected, check the selectall checkbox and viceversa
    $(".check_firma").on("click", function () {
        if ($(".check_firma").length == $(".check_firma:checked").length) {
            $("#selectall").prop("checked", true);
        } else {
            $("#selectall").prop("checked", false);
        }
    });

</script>
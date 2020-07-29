<!DOCTYPE html>
<?php
$array_columnas = json_decode($columnas);
if ($array_columnas):
    ?>
    <?php if (is_array($array_columnas)): ?>   
        <div class="modal-body">
            <table id="tablaObn_<?= $campo->id ?>" class="" role="">
                <caption class="hide-read"><?= $campo->id ?>
                </caption>
                <thead>
                    <tr>
                        <th name="">Asociar</th>
                        <?php foreach ($array_columnas as $value): ?>            
                            <th name="<?= $value ?>"><?= $value ?></th>            
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="modal-body">
            <div class="dialogo validacion-warning">
                <h3 class="dialogos_titulo">No es posible continuar</h3>
                <div class="alert alert-warning">"No se pudo cargar la tabla del OBN Asociado"</div>
            </div>
        </div>
    <?php endif; ?>    
    <script type="text/javascript">

        $(document).ready(function () {            
            var columns = [
                {name: ""},
    <?php foreach (json_decode($columnas) as $value): ?>
                    {name: "<?= $value ?>"},
    <?php endforeach; ?>
            ];
            var headers = columns.map(function (c) {
                return c.header;
            });
            $("#tablaObn_<?= $campo->id ?>").DataTable({
                data: dataSet,
                columns: columns,
                responsive: false,
                iDisplayLength: 8,
                bLengthChange: false,
                ordering: false,
                info: false,
                select: false,
                orderCellsTop: true,
                fixedHeader: true,
                bDestroy: true,
                language: {
                    emptyTable: "Sin datos disponibles",
                    paginate: {
                        "first": "Primero",
                        "last": "Último",
                        "next": "»",
                        "previous": "«"
                    },
                    "processing": "Procesando solicitud",
                    "infoEmpty": "Sin datos disponibles",
                    "zeroRecords": "Sin datos disponibles",
                },
                autoWidth: false,
                bProcessing: true,
                bServerSide: true,
                ajax: {
                    url: "<?= site_url('obn_consultas/lista_obn_atributo') ?>", // json datasource
                    type: "post", // type of method  , by default would be get
                    data: {
                        "variable_obn": "<?= ($campo->variable_obn != "" ? $campo->variable_obn : $variable_obn) ?>",
                        "etapa": "<?= $etapa ?>",
                    },
                    error: function () {

                    }
                },
                "columnDefs": [{
                        searchable: true,
                        defaultContent: "-",
                        targets: "_all",
                        "createdCell": function (td, cellData, rowData, row, col) {
                            if (true) {
                                var atributo = columns[col].name;
                                $(td).attr({"data-title": columns[col].name});
                                if (columns[col].name != "") {
                                    $(td).html(rowData[atributo]);
                                } else {
                                    $(td).html('<a href="#" onClick="appendRow<?= $campo->id ?>(' + rowData['id'] + ');" data-dismiss="modal" class="button-no-style icn icn-plus-sm hide-text-read">Asociar</a>');
                                }
                            }
                        }
                    }]
            });
            var table = $("#tablaObn_<?= $campo->id ?>").DataTable();
            $('#tablaObn_<?= $campo->id ?> thead tr').clone(true).appendTo('#tablaObn_<?= $campo->id ?> thead');
            $('#tablaObn_<?= $campo->id ?> thead tr:eq(1) th').each(function (i) {
                if (i > 0) {
                    var title = $(this).text();
                    $(this).addClass("filter-table");
                    $(this).html('<input type="text" placeholder="Buscar por ' + title + '" />');
                    $('input', this).on('keyup change', function () {
                        table.columns(i)
                                .search(this.value)
                                .draw();                        
                    });
                } else {
                    $(this).html('');
                }
            });
            $("#tablaObn_<?= $campo->id ?>_filter").addClass("hidden");
        });
    </script>
<?php else: ?>
    <div class="modal-body">
        <div class="dialogo validacion-warning">
            <h3 class="dialogos_titulo">No es posible continuar</h3>
            <div class="alert alert-warning">"No se pudo cargar la tabla del OBN Asociado"</div>
        </div>
    </div>
<?php endif; ?>
<div class="modal-footer" >        
    <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
</div>
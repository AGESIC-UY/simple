<script>
    /*function eliminarProceso(procesoId) {
     $("#modal").load(site_url + "backend/procesos/ajax_auditar_eliminar_proceso/" + procesoId);
     $("#modal").modal();
     return false;
     }
     
     function activarProceso(procesoId) {
     $("#modal").load(site_url + "backend/procesos/ajax_auditar_activar_proceso/" + procesoId);
     $("#modal").modal();
     return false;
     }
     
     function mostrarEliminados() {
     $(".procesos_eliminados").slideToggle('slow', callbackEliminadosFn);
     return false;
     }
     
     function callbackEliminadosFn() {
     var $link = $("#link_eliminados");
     $(this).is(":visible") ? $link.text("Ocultar Eliminados «") : $link.text("Mostrar Eliminados »");
     }
     */
    function publicarProceso(procesoId) {
        $("#modal").load(site_url + "backend/procesos/ajax_publicar_proceso/" + procesoId);
        $("#modal").modal();
        return false;
    }

    function editarProceso(procesoId) {
        $("#modal").load(site_url + "backend/procesos/ajax_editar_proceso/" + procesoId);
        console.log(site_url + "backend/procesos/ajax_editar_proceso/" + procesoId);
        $("#modal").modal();
        return false;
    }
    $(document).ready(function () {
        $("#accion-clonar-proceso").click(function () {
            $("#btn_clonar").attr("disabled", true);
            $("#lista_versiones").hide();
            $("#proceso_clonar").empty();
            $('#proceso_root option[value=""]').attr("selected", true);
        });
        $("#proceso_root").change(function () {
            $("#proceso_clonar").empty();
            if ($("#proceso_root option:selected").val() != "") {
                $("#btn_clonar").removeAttr("disabled");
                $.ajax({
                    url: "<?= site_url("backend/procesos/get_version") ?>",
                    data: {
                        'proceso_root': $("#proceso_root option:selected").val(),
                    },
                    type: "POST",
                    dataType: "json",
                    success: function (response) {
                        if (response.versiones) {
                            var versiones = response.versiones
                            $.each(versiones, function (i, value) {
                                $("#proceso_clonar").append("<option value='" + value.id + "'>Versión: " + value.version +" Estado: "+ (value.estado=='public'?'Público':(value.estado=='arch'?'Archivado':"Borrador")) + "</option>");
                            });
                            $("#lista_versiones").show();
                        } else {
                            $("#lista_versiones").hide();
                        }
                    },
                    error: function () {
                    }
                });
            } else if ($("#proceso_root option:selected").val() == "") {
                $("#btn_clonar").attr("disabled", true);
                $("#lista_versiones").hide();
            }
        });
    });
</script>
<ul class="breadcrumb">
    <li>
        Listado de Procesos
    </li>
</ul>
<a href="#" class="btn btn-ayuda btn-secundary" id="ayuda_contextual_procesos"><span class="icon-white icon-question-sign"></span> Ayuda</a>
<h2>Listado de Procesos</h2>
<div class="acciones-generales">
    <a class="btn btn-success" id="accion-nuevo-proceso" href="<?= site_url('backend/procesos/crear/') ?>"><span class="icon-file"></span> Nuevo</a>
    <a class="btn btn-default" id="accion-importar-proceso" href="#modalImportar" data-toggle="modal" ><span class="icon-upload icon"></span> Importar</a>
    <a class="btn btn-default" id="accion-importar-proceso_version" href="#modalImportarVersion" data-toggle="modal" ><span class="icon-upload-alt icon"></span> Importar versión</a>
    <a class="btn btn-default" id="accion-clonar-proceso" href="#modalClonar" data-toggle="modal" ><span class="icon-copy icon"></span> Clonar</a>
</div>

<?php
if (isset($mensajes)) {
    echo $mensajes;
}
?>

<table class="table">
    <caption class="hide-text">Procesos</caption>
    <thead>
        <tr>
            <th id="accion-lista-procesos">Proceso</th>
            <th>Estado</th>
            <th id="accion-lista-procesos_version">Versión</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($procesos as $p): ?>
            <tr>
                <td><?= $p->nombre ?></td>
                <td><?= $p->estado == 'public' ? 'Publicado' : 'Borrador' ?></td>
                <td><?= $p->version ?></td>
                <td class="actions">
                    <?php if ($editar_proceso && $p->estado == 'public') { ?>
                        <a class="btn btn-primary" onclick="return editarProceso(<?= $p->id ?>);" href="#"><i class="icon-white icon-edit"></i> Editar</a>
                    <?php } else { ?>
                        <a class="btn btn-primary" href="<?= site_url('backend/procesos/editar/' . $p->id) ?>"><i class="icon-white icon-edit"></i> Editar</a>
                    <?php } ?>
                    <a class="btn btn-primary" href="<?= site_url('backend/procesos/exportar/' . $p->id) ?>"><span class="icon-white icon-share"></span> Exportar<span class="hide-text"> <?= $p->id ?></span></a>
                    <?php if ($p->estado == 'draft') { ?>
                        <a class="btn btn-primary" href="#" onclick="return publicarProceso(<?= $p->id ?>);"><i class="icon-white icon-eye-open"></i> Publicar</a>
                    <?php } ?>
                    <?php if ($p->estado == 'public') { ?>
                        <a class="btn btn-danger" href="<?= site_url('backend/procesos/ocultar/' . $p->id) ?>" onclick="return confirm('¿Esta seguro que desea ocultar el proceso?')"><span class="icon-white icon-eye-close"></span> Ocultar<span class="hide-text"> <?= $p->id ?></span></a>
                    <?php } ?>
                    <?php if ($p->ntramites == 0 && $p->estado != 'public') { ?>
                        <a class="btn btn-danger" href="<?= site_url('backend/procesos/eliminar/' . $p->id) ?>" onclick="return confirm('¿Esta seguro que desea eliminar?')"><span class="icon-white icon-trash"></span> Eliminar<span class="hide-text"> <?= $p->id ?></span></a>
                    <?php } ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div id="modalImportar" class="modal hide fade">
    <form method="POST" enctype="multipart/form-data" action="<?= site_url('backend/procesos/importar') ?>">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Importar Proceso</h3>
        </div>
        <div class="modal-body">
            <label for="file">Cargue a continuación el archivo .simple donde exportó su proceso.</label>
            <input type="file" name="archivo" id="file"/>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Importar</button>
            <button class="btn btn-link" data-dismiss="modal" aria-hidden="true">Cerrar</button>
        </div>
    </form>
</div>

<div id="modalImportarVersion" class="modal hide fade">
    <form method="POST" enctype="multipart/form-data" action="<?= site_url('backend/procesos/importar') ?>">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Importar nueva versión del proceso</h3>
        </div>
        <div class="modal-body">
            <label for="root_id">Seleccione un proceso</label>
            <select name="root_id">
                <?php foreach ($procesos as $p): ?>
                    <option value="<?= $p->id ?>"><?= $p->nombre . " Versión: " . $p->version ?> Estado:<?= $p->estado == 'public' ? 'Publicado' : 'Borrador' ?></option>                
                <?php endforeach; ?>                
            </select>
            <label for="file">Cargue a continuación el archivo .simple donde exportó su proceso.</label>
            <input type="file" name="archivo" id="file"/>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Importar</button>
            <button class="btn btn-link" data-dismiss="modal" aria-hidden="true">Cerrar</button>
        </div>
    </form>
</div>

<div id="modalClonar" class="modal hide fade">
    <form method="POST" enctype="multipart/form-data" action="<?= site_url('backend/procesos/clonar') ?>">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Clonar Proceso</h3>
        </div>
        <div class="modal-body">
            <label for="proceso_root">Seleccione el proceso a clonar</label>
            <select name="proceso_root" id="proceso_root">
                <option value="">Selecciones...</option>
                <?php foreach ($procesos_root as $p): ?>
                    <option value="<?= $p->root ?>"><?= $p->nombre ?></option>                
                <?php endforeach; ?>                
            </select>
            <div id="lista_versiones" style="display: none">
                <label for="root_id">Seleccione la versión</label>
                <select name="proceso_clonar" id="proceso_clonar">            
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" disabled="" id="btn_clonar" >Clonar</button>
            <button class="btn btn-link" data-dismiss="modal" aria-hidden="true">Cerrar</button>
        </div>
    </form>
</div>
<div id="modal" class="modal hide fade"></div>
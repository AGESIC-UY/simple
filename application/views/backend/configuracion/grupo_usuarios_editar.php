<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?= site_url('backend/configuracion') ?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/configuracion/grupos_usuarios') ?>">Grupos de Usuarios</a> <span class="divider">/</span>
            </li>
            <li class="active"><?= isset($grupo_usuarios) ? $grupo_usuarios->nombre : 'Crear' ?></li>
        </ul>
        <h2 class="active"><?= isset($grupo_usuarios) ? $grupo_usuarios->nombre : 'Crear' ?></h2>
        <form class="ajaxForm" method="post" action="<?= site_url('backend/configuracion/grupo_usuarios_editar_form/' . (isset($grupo_usuarios) ? $grupo_usuarios->id : '')) ?>">
            <fieldset>
                <legend>Editar grupo de usuario</legend>
                <div class="validacion validacion-error"></div>
                <div class="form-horizontal">
                    <?php if (isset($grupo_usuarios)): ?>
                        <div class="control-group">
                            <label for="id" class="control-label">Id</label>
                            <div class="controls">
                                <input type='text' id="id" class="input-small" value='<?= $grupo_usuarios->id ?>' disabled />
                            </div>
                        </div>
                    <?php endif ?>
                    <?php if ($es_grupo_pre_definido): ?>
                        <div class="control-group">
                            <label class="control-label">Nombre</label>
                            <div class="controls" style="margin-top: 7px;">
                                <strong><?= $grupo_usuarios->nombre ?></strong>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="control-group">
                            <label for="nombre" class="control-label">Nombre</label>
                            <div class="controls">
                                <input type="text" id="nombre" class="input-xlarge" name="nombre" value="<?= isset($grupo_usuarios) ? $grupo_usuarios->nombre : '' ?>"/>
                            </div>
                        </div>
                    <?php endif ?>
                    <div class="control-group">
                        <label for="select-usuarios" class="control-label">Este grupo lo componen</label>
                        <div class="controls">
                            <select id="select-usuarios" class="input-xlarge" name="usuarios[]" data-placeholder="Seleccione los usuarios" multiple>
                                <?php foreach ($grupo_usuarios->Usuarios as $g): ?>
                                    <option value="<?= $g->id ?>" selected><?= $g->displayUsername(true) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <script>
                                $(document).ready(function () {
                                    $("#select-usuarios").select2({
                                        ajax: {
                                            url: site_url + "backend/configuracion/ajax_get_usuarios",
                                            cache: true,
                                            data: function (params) {
                                                return {query: params.term};
                                            },
                                            processResults: function (data, page) {
                                                return {
                                                    results: data
                                                }
                                            }
                                        }

                                    });
                                })
                            </script>
                        </div>
                    </div>
                </div>
            </fieldset>
            <ul class="form-action-buttons">
                <li class="action-buttons-primary">
                    <ul>
                        <li>
                            <button class="btn btn-primary btn-lg" type="submit">Guardar</button>
                        </li>
                    </ul>
                </li>
                <li class="action-buttons-second">
                    <ul>
                        <li class="float-left">
                            <a class="btn btn-link btn-lg" href="<?= site_url('backend/configuracion/grupos_usuarios') ?>">Cancelar</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </form>
    </div>
</div>

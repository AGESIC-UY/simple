<script src="<?= base_url() ?>assets/js/modelador-formularios-bloques.js" type="text/javascript"></script>

<script type="text/javascript">
    var formularioId=<?= $formulario->id ?>;
</script>

<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('backend/bloques') ?>">Catálogo de Bloques</a> <span class="divider">/</span>
    </li>
    <li class="active"><?=$bloque->nombre ?></li>
</ul>
<div class="titulo-form">
  <h2><?= $bloque->nombre ?></h2>
</div>
<form action="<?=site_url('backend/bloques/editar_form/'.$bloque->id)?>" method="post" class="form-horizontal dynaForm debugForm">
	<input type="hidden" name="bloque_id" value="<?= $bloque->id ?>" />
  <div class="control-group">
    <label for="nombre" class="control-label">Nombre</label>
    <div class="controls">
      <input id="nombre" class="input-xlarge" type="text" value="<?= $bloque->nombre ?>" name="nombre" />
      <input class="btn btn-primary" type="submit" value="Guardar" />
    </div>
  </div>

    <div id="areaFormulario">
	    <div class="btn-toolbar toolbar-formulario">
	        <div class="btn-group">
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'title')">Título</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'subtitle')">Subtítulo</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'step_title')">Título del paso</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'paragraph')">Parrafo</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'documento')">Documento</button>
	        </div>
	        <div class="btn-group">
	            <button class="btn btn-inverse campo_no_requerido" onclick="return agregarCampo(<?= $formulario->id ?>,'error')">Mensaje de error</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'fieldset')">Fieldset</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'text')">Textbox</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'textarea')">Textarea</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'select')">Select</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'radio')">Radio</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'checkbox')">Checkbox</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'file')">File</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'date')">Date</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'grid')">Grilla</button>
	        </div>
	        <div class="btn-group">
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'encuesta')">Encuesta</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'instituciones_gob')">Instituciones</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'paises')">Paises</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'comunas')">Comunas</button>
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'moneda')">Moneda</button>
	        </div>
	        <div class="btn-group">
	            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'javascript')">Javascript</button>
	        </div>
	    </div>

	      <div class="edicionFormulario">
          <!-- fieldset>
            <legend><?= $bloque->nombre ?></legend -->
            <?php foreach ($formulario->Campos as $c): ?>
                <div class="arrastrable">
                  <div class="campo" data-id="<?= $c->id ?>" <?= $c->dependiente_campo ? 'data-dependiente-campo="' . $c->dependiente_campo.'" data-dependiente-valor="' . $c->dependiente_valor .'" data-dependiente-tipo="' . $c->dependiente_tipo.'" data-dependiente-relacion="'.$c->dependiente_relacion.'"' : '' ?> >
                      <div class="grupo-dato">
                        <div class="botones-edit">
                          <div class="buttons">
                            <a href="#" class="btn btn-primary" onclick="return editarCampo(<?= $c->id ?>)"><span class="icon-edit icon-white"></span><span class="hide-text">Editar <?=$c->id?></a>
                            <a href="<?= site_url('backend/bloques/eliminar_campo/' . $c->id . '?bloque_id=' .$bloque->id) ?>" class="btn btn-danger" onclick="return confirm('¿Esta seguro que desea eliminar?')"><span class="icon-trash icon-white"></span><span class="hide-text">Eliminar <?=$c->id?></a>
                          </div>
                        </div>
                        <div class="campo-form">
                            <div><?= $c->displaySinDato() ?></div>
                        </div>
                      </div>
                  </div>
                </div>
            <?php endforeach; ?>
          <!-- /fieldset -->
	      </div>
	</div>
</form>
<div class="modal hide fade" id="modal"></div>

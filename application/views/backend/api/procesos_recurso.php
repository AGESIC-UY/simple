<div class="row-fluid">
  <div class="span3">
      <?php $this->load->view('backend/api/sidebar') ?>
  </div>
  <div class="span9">
    <ul class="breadcrumb">
        <li>
            <a href="<?= site_url('backend/api') ?>">Api</a> <span class="divider">/</span>
        </li>
        <li class="active"><?=$title?></li>
    </ul>
    <h2><?= $title ?></h2>

    <p>Procesos es un listado de procesos de SIMPLE. Los métodos permiten obtener información de un proceso o listar una serie de procesos.</p>

    <h3>Métodos</h3>
    <dl class="dl-horizontal">
        <dt><a href="<?=site_url('backend/api/procesos_obtener')?>">obtener</a></dt>
        <dd>Obtiene un recurso proceso.</dd>
        <dt><a href="<?=site_url('backend/api/procesos_listar')?>">listar</a></dt>
        <dd>Obtiene el listado completo de procesos de la cuenta.</dd>
    </dl>
    <h3>Representación del recurso</h3>

    <p>Un recurso es representado como una estructura json. Este es un ejemplo de cómo se vería un recurso.</p>

    <pre>{
    "proceso":{
        "id":10,
        "nombre":"Proceso de Inscripción a Beca Educacional"
    }
}</pre>

  </div>
</div>

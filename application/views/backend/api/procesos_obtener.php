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
        <h2><?=$title?></h2>

        <p>Obtiene un proceso.</p>

        <h3>Request HTTP</h3>

        <pre>GET <?= site_url('backend/api/procesos/{procesoId}') ?>?token={token}</pre>

        <h3>Parámetros</h3>

        <table class="table table-bordered">
          <caption class="hide-text">Parámetros</caption>
          <thead>
            <tr>
                <th>Nombre del Parámetro</th>
                <th>Valor</th>
                <th>Descripción</th>
            </tr>
          </thead>
          <tbody>
            <tr>
                <td>procesoId</td>
                <td>int</td>
                <td>Identificador único de un proceso en SIMPLE.</td>
            </tr>
          </tbody>
        </table>

        <h3>Response HTTP</h3>

        <p>Si el request es correcto, se devuelve un <a href="<?=site_url('backend/api/procesos_recurso')?>">recurso proceso</a>.</p>
    </div>
</div>

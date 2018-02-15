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

          <p>Dado el tramite busca las tareas actuales y en caso de existir una automática y estar en estado pendiente la avanza (ejecutando sus eventos y cerrando la tarea automática).</p>

        <h3>Request HTTP</h3>

        <pre>GET <?= site_url('backend/api/ejecutar_tarea/{tramiteId}') ?>?token={token}</pre>

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
                <td>tramiteId</td>
                <td>int</td>
                <td>Identificador único de un trámite en SIMPLE.</td>
            </tr>
          </tbody>
        </table>

        <h3>Response HTTP</h3>

        <p>Retorna un JSON con el siguiente formato:</p>

        <pre>
{
  "resultado":"OK o ERROR",
  "mensaje":"Mensaje que describe del error",
}
Donde resultado puede ser "OK" en caso de que se pudo avanzar la tarea o "ERROR" si no la pudo avanzar.
En caso de "ERROR" en "mensaje" se informa el motivo.
En caso de no existir el tramite se retorna el código HTTP 404.
      </pre>
    </div>
</div>

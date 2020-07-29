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

          <p>Dado el trámite busca las tareas actuales, en caso de existir una en espera de señal y estar en estado pendiente la avanza (ejecutando sus eventos y cerrando la tarea automática).</p>

        <h3>Request HTTP</h3>

        <pre>GET <?= site_url('backend/api/ejecutar_tarea/{tramiteId}') ?>?token={token}&&data={data}</pre>

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
                <td>token</td>
                <td>int</td>
                <td>Token de acceso.</td>
            </tr>
            <tr>
                <td>tramiteId</td>
                <td>int</td>
                <td>Identificador único de un trámite en SIMPLE.</td>
            </tr>
            <tr>
                <td>data</td>
                <td>json</td>
                <td>Las variables a generar para la tarea</td>
            </tr>
          </tbody>
        </table>

        <h3>Ejemplo parámetro data</h3>
        <p>
            Puntos a tener en cuenta del ejemplo:<br><br>
              - Se guardan datos de seguimiento de las variables definidas para cada tarea.<br>
              - En caso de pasar el parámetro "usuario" debe ser un un usuario existente en la base de simple registrado para la cuenta del trámite a ejecutar.<br>
              - Si el dato de alguna de las variables es un archivo en base 64 se genera el archivo correspondiente, se debe pasar la extension del archivo.<br>
              - Para los campos checkbox, radio y select se debe agregar un nuevo parametro a las variables llamado "etiqueta".
              <br><br>
        </p>
<pre>
{
  "datos": {
    "usuario": "uy-ci-9191182",
    "variables": [
      {
        "nombre": "un_nombre_campo",
        "valor": "valor del campo"
      },
      {
        "nombre": "otro_nombre_campo",
        "valor": "otro valor para el campo"
      },
      {
        "nombre": "ultimo_nombre_campo",
        "valor": "valor prueba"
      }
    ]
  }
}
</pre>

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

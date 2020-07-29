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

          <p>Dado un proceso, busca la versión del proceso activa y crea una instancia del mismo, luego avanza las tareas hasta la indicada.<br><br>
              Puntos a tener en cuenta:<br><br>
                - El proceso debe estar configurado para poder instanciarse con la api.<br>
                - Ejecuta todos los eventos según como fueron modelados para cada tarea y que están configurados para ejecutarse con la api.<br>
                - Se toma en cuenta la trazabilidad y generación de pdf para cada etapa con sus pasos.<br><br>
          </p>

        <h3>Request HTTP</h3>

        <pre>GET <?= site_url('backend/api/instanciar/{procesoId}') ?>?token={token}&&data={data}</pre>

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
                <td>procesoId</td>
                <td>int</td>
                <td>Identificador único de un proceso en SIMPLE.</td>
            </tr>
            <tr>
                <td>data</td>
                <td>json</td>
                <td>Para cada tarea las variables a generar.</td>
            </tr>
          </tbody>
        </table>

        <h3>Ejemplo parámetro data</h3>

        <p>
            Puntos a tener en cuenta del ejemplo:<br><br>
              - La tarea indicada como "tarea_hasta" queda pendiente y no se ejecuta.<br>
                 - El usuario debe cumplir con el formato definido en SIMPLE. <br>
              - Se guardan datos de seguimiento de las variables definidas para cada tarea. <br>
              - Si la alguna dato de las variables es un archivo en base 64 se genera el archivo correspondiente, se debe pasar la extension del archivo.<br>
              - Para los campos checkbox, radio y select se debe agregar un nuevo parámetro a las variables llamado "etiqueta".
              <br><br>
        </p>

<pre style="max-height: 400px;overflow-y: auto;">
{
  "usuario": "uy-ci-9191182",
  "tarea_hasta": "Tarea4",
  "datos": [
    {
      "tarea": {
        "nombre": "Tarea1",
        "variables": [
          {
            "nombre": "campo_radio",
            "valor": "valor de radio seleccionado",
            "etiqueta": "etiqueta radio"
          },
          {
            "nombre": "campo_archivo",
            "valor": "archivo en base 64",
            "extension": "pdf"
          },
          {
            "nombre": "email",
            "valor": "fp@gmail.com"
          }
        ]
      }
    },
    {
      "tarea": {
        "nombre": "Tarea2",
        "variables": [
          {
            "nombre": "otro",
            "valor": "otro datos extra"
          },
          {
            "nombre": "campo_extra",
            "valor": "una dir"
          },
          {
            "nombre": "cedula",
            "valor": "46616"
          }
        ]
      }
    },
    {
      "tarea": {
        "nombre": "Tarea3",
        "variables": [
          {
            "nombre": "otros",
            "valor": "otro datos extra"
          },
          {
            "nombre": "direccion",
            "valor": "una dir"
          },
          {
            "nombre": "un_campo",
            "valor": "46616"
          }
        ]
      }
    }
  ]
}
</pre>

        <h3>Response HTTP</h3>

        <p>Retorna un JSON con el siguiente formato:</p>

        <pre>
{
  "resultado":"OK o ERROR",
  "mensaje":"Mensaje que describe del error",
  "tramiteId":"El número de tramite generado",

}
Donde resultado puede ser "OK" en caso de que se pudo avanzar la tarea o "ERROR" si no la pudo avanzar.
En caso de "ERROR" en "mensaje" se informa el motivo.
En caso de no existir el trámite se retorna el código HTTP 404.
      </pre>
    </div>
</div>

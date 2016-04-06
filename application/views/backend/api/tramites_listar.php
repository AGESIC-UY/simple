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

        <p>Lista todos los trámites.</p>

        <h3>Request HTTP</h3>

        <pre>GET <?= site_url('backend/api/tramites') ?>?token={token}</pre>

        <h3>Parámetros</h3>

        <table class="table table-bordered">
          <thead>
            <tr>
                <th>Nombre del Parámetro</th>
                <th>Valor</th>
                <th>Descripción</th>
            </tr>
            <tr>
                <th colspan="3" style="text-align: center">Parametros Opcionales</th>
            </tr>
          </thead>
          <tbody>
            <tr>
                <td>maxResults</td>
                <td>int</td>
                <td>El número máximo de resultados que debería contener la respuesta. Valores aceptables son del 1 al 20. Por defecto: 10.</td>
            </tr>
            <tr>
                <td>pageToken</td>
                <td>string</td>
                <td>El token de continuación. Usado para la paginación entre varios sets de resultados. Para obtener la próxima página de resultados se debe setear este parámetro con el valor de "nextPageToken" entregado en la respuesta previa.</td>
            </tr>
          </tbody>
        </table>

        <h3>Response HTTP</h3>

        <p>Si el request es correcto, se devuelve la siguiente estructura:</p>

        <pre>{
    "tramites":{
        "titulo":"Listado de Tramites",
        "tipo":"#tramitesFeed",
        "nextPageToken":{string},
        "items":[
            <a href="<?=site_url('backend/api/tramites_recurso')?>">recurso tramite</a>
        ]
    }
}</pre>
        <p>Las propiedades que incorpora esta respuesta son:</p>

        <table class="table table-bordered">
          <thead>
            <tr>
                <th>Nombre del Parámetro</th>
                <th>Valor</th>
                <th>Descripción</th>
            </tr>
          </thead>
          <tbody>
            <tr>
                <td>titulo</td>
                <td>string</td>
                <td>El título de este listado de trámites.</td>
            </tr>
            <tr>
                <td>tipo</td>
                <td>string</td>
                <td>Identifica el nombre de este recurso.</td>
            </tr>
            <tr>
                <td>nextPageToken</td>
                <td>string</td>
                <td>El token de continuación. Usado para paginar entre varios sets de resultados. Proveer este valor en requests subsiguientes para obtener la próxima página de resultados.</td>
            </tr>
            <tr>
                <td>items</td>
                <td>array</td>
                <td>El listado de trámites.</td>
            </tr>
          </tbody>
        </table>
    </div>
</div>

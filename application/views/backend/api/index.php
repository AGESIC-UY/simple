<div class="row-fluid">
    <div class="span3">
        <?php $this->load->view('backend/api/sidebar') ?>
    </div>
    <div class="span9">
      <ul class="breadcrumb">
          <li>
              <a href="<?= site_url('backend/api') ?>">Api</a> <span class="divider">/</span>
          </li>
          <li class="active">Introducción</li>
      </ul>
        <h2>API</h2>
        <p>La API de SIMPLE te permite generar tus propios reportes e informes, extrayendo la información de la plataforma en tiempo real.</p>

        <h3>Autorización</h3>

        <p>Para hacer llamadas a esta API se requerirá un código de acceso (token) que se enviará como parámetro en cada request.</p>

        <?php if (UsuarioBackendSesion::usuario()->Cuenta->api_token): ?>
            <p>El codigo de acceso de esta cuenta es: <strong><?= UsuarioBackendSesion::usuario()->Cuenta->api_token ?></strong> <a href="<?= site_url('backend/api/token') ?>">(Cambiar código de acceso)</a></p>
        <?php else: ?>
            <p>No se ha configurado un codigo de acceso. <a href="<?= site_url('backend/api/token') ?>">Para poder acceder a la API debera configurar un codigo de acceso aquí.</a></p>
        <?php endif ?>

        <h3>Llamadas a la API</h3>

        <p>El diseño de la API de este portal sigue un modelo REST. Eso significa que se utilizan los métodos estándares HTTP para obtener la información. Por ejemplo, si deseas obtener una ficha en particular, deberías enviar un request HTTP como el siguiente:</p>

        <pre>GET <?= site_url('backend/api/tramites/{tramiteId}') ?>?token={token}</pre>

        <h3>Parámetros comunes</h3>

        <p>Los diferentes métodos de esta interfaz de programación requieren distintos atributos como parte de la URL, como parámetros de la consulta. Adicionalmente hay parámetros que son comunes para todos los métodos:</p>

        <table class="table table-bordered">
          <caption class="hide-text">Parámetros comunes</caption>
          <thead>
            <tr>
                <th>Nombre del parámetro</th>
                <th>Valor</th>
                <th>Descripción</th>
            </tr>
          </thead>
          <tbody>
            <tr>
                <td>token</td>
                <td>string</td>
                <td>Código de acceso para acceder a los métodos de esta API.</td>
            </tr>
          </tbody>
        </table>

        <h2>Formatos de los datos</h2>

        <p>Los recursos de la API de este portal vienen en formato json. Este es un ejemplo de cómo se vería un trámite.</p>

        <pre>{
    "tramite":{
        "id":496,
        "estado":"completado",
        "proceso_id":11,
        "fecha_inicio":"2013-07-12 11:13:56",
        "fecha_modificacion":"2013-07-12 11:14:00",
        "fecha_termino":"2013-07-12 11:14:00",
        "etapas":[
            {
                "id":704,
                "estado":"completado",
                "usuario_asignado":{
                    "usuario":"jperez",
                    "email":"jperez@ejemplo.com",
                    "nombres":"Juan",
                    "apellido_paterno":"Perez",
                    "apellido_materno":"Cotapo"
                },
                "fecha_inicio":"2013-07-12 11:13:56",
                "fecha_modificacion":"2013-07-12 11:14:00",
                "fecha_termino":"2013-07-12 11:14:00",
                "fecha_vencimiento":null
            }
        ],
        "datos":[
            {
                "511bb6183cea8":"51e021b44939e.pdf"
            },
            {
                "materno":"COTAPO"
            },
            {
                "nombres":"JUAN"
            },
            {
                "paterno":"PEREZ"
            },
            {
                "situacion":true
            }
        ]
    }
}</pre>
    </div>
</div>

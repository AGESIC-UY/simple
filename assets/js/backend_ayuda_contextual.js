$(document).ready(function() {

  /*
  * AYUDA CONTEXTUAL
  */

  // -- Muestra el tour
  var template = "<div class='popover tour'>";
  template += "<div class='arrow'></div>";
  template += "<h3 class='popover-title'></h3>";
  template += "<div class='popover-content'></div>";
  template += "<div class='popover-navigation'><div class='btn-toolbar'><div class='btn-group'>";
  template += "    <button class='btn btn-secundary' data-role='prev'><span class='icon-chevron-left'></span> Anterior</button>";
  template += "    <span data-role='separator'> </span>";
  template += "    <button class='btn btn-primary' data-role='next'>Siguiente <span class='icon-chevron-right'></span></button>";
  template += "</div><div class='btn-group'>";
  template += "<button class='btn btn-link' data-role='end'>Cerrar</button></div></div></div>";
  template += "</div>";

  // -- Ayuda contextual PROCESOS
  $('#ayuda_contextual_procesos').click(function() {
    var tour = new Tour({
      steps: [
        {
          element: "#accion-nuevo-proceso",
          title: "Crear un proceso",
          content: "Haciendo clic en este botón usted prodrá crear un nuevo proceso."
        },
        {
          element: "#accion-importar-proceso",
          title: "Importar un proceso",
          content: "Haciendo clic en este botón usted prodrá importar un proceso anteriormente exportado."
        },
        {
          element: "#accion-lista-procesos",
          title: "Listado de procesos",
          content: "En este listado usted podrá ver todos los procesos creados. Para cada proceso verá los siguientes botones: <br><br><ul><li><b>Editar:</b> Permite modificar el proceso (sus acciones, documentos, formularios y tareas).</li><li><b>Exportar:</b> Permite exportar un proceso completo para ser importado en la misma u otra instalación.</li><li><b>Eliminar:</b> Permite eliminar el proceso. Tenga en cuenta que una vez eliminado el proceso no podrá recuperarse.</li></ul>",
          placement: 'top'
        }
      ],
      template: template,
      storage: false
    });

    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();
  });

  // -- Ayuda contextual SERVICIOS
  $('#ayuda_contextual_servicios').click(function() {
    var tour = new Tour({
      steps: [
        {
          element: "#accion-servicios",
          title: "Catálogo de Servicios",
          content: "En el catálogo de servicios se podrán definir los Servicios Web que se utilizarán en los procesos.</br></br> Estos podrán ser SOAP o PDI. Los servicios PDI son los que están publicados en la Plataforma de Interoperabilidad de AGESIC.</br> Una vez ingresado un Servicio al catálogo se debe ingresar las operaciones del Servicio con la funcionalidad 'Ver Operaciones'",
          placement: "top"
        },
        {
          element: "#accion-nuevo-servicio",
          title: "Crear un servicio",
          content: "Haciendo clic en este botón usted prodrá crear un nuevo servicio en el catálogo."
        },
        {
          element: "#accion-lista-servicios",
          title: "Listado de servicios",
          content: "En este listado usted podrá ver todos los servicios creados. Para cada servicio verá los siguientes botones: <br><br><ul><li><b>Ver operaciones:</b> Lista las operaciones creadas para el servicio.</li><li><b>Editar:</b> Permite modificar el servicio.</li><li><b>Eliminar:</b> Permite eliminar el servicio. Tenga en cuenta que una vez eliminado el servicio no podrá recuperarse. Adicionalmente, tenga en cuenta si alguna operación del servicio esta en uso en algún proceso este dejará de funcionar correctamente.</li></ul>",
          placement: 'top'
        }
      ],
      template: template,
      storage: false
    });

    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();
  });

    // -- Ayuda contextual SERVICIOS EDITAR
    $('#ayuda_contextual_servicios_editar').click(function() {
      var tour = new Tour({
        steps: [
          {
            element: "#accion-tipo-servicio",
            title: "Tipo de Servicio",
            content: "Como primer paso debe seleccionar el tipo de servicio que consumirá el o los procesos.</br></br> Estos podrán ser SOAP o PDI. Seleccione  SOAP si consumirá un proceso que siga el estandar SOAP 1.1/1.2. Seleccione PDI si consumirá un proceso publicado en la Plataforma de Interoperabilidad de AGESIC.",
            placement: "right"
          },
          {
            element: "#form_pdi",
            title: "Datos del Servicio",
            content: "En nombre se debe ingresar el nombre del servicio. Es el nombre que se utilizará para seleccionar el servicio en el proceso.</br>En timeout de conexión y respuesta se debe ingresar el tiempo en segundos que se debe aguardar por la conexión y respuesta del servicio.</br>En URL Física, Lógica y ROL la información proporcionada por la PDI.",
            placement: 'top'
          },
          {
            element: "#form_soap",
            title: "Datos del Servicio",
            content: "En nombre se debe ingresar el nombre del servicio. Es el nombre que se utilizará para seleccionar el servicio en el proceso.</br> En WSDL se se debe ingresar la URL del WSDL del servicio.</br>En timeout de conexión y respuesta se debe ingresar el tiempo en segundos que se debe aguardar por la conexión y respuesta del servicio.</br>En Endpoint Location se debe ingresar la URL del Endpoint del servicio Web.",
            placement: 'top'
          }
        ],
        template: template,
        storage: false
      });

      // Initialize the tour
      tour.init();

      // Start the tour
      tour.start();
    });



    // -- Ayuda contextual SERVICIOS Operaciones
    $('#ayuda_contextual_servicios_operaciones').click(function() {
      var tour = new Tour({
        steps: [
          {
            element: "#accion-operaciones",
            title: "Operaciones del Servicio",
            content: "Un Servicio Web puede tener 1 o más operaciones a invocar.</br></br> Desde está funcionalidad podrá definir las operaciones disponibles para el servicio web.",
            placement: "top"
          },
          {
            element: "#accion-nueva-operacion",
            title: "Crear una nueva Operación",
            content: "Haciendo clic en este botón usted prodrá agregar una nueva operación al Servicio.",
            placement: "right"
          },
          {
            element: "#accion-lista-operaciones",
            title: "Listado de servicios",
            content: "En este listado usted podrá ver todos las operaciones creadas. Para cada Operación verá los siguientes botones: <br><br><ul><li><b>Editar:</b> Permite modificar la operación.</li><li><b>Eliminar:</b> Permite eliminar la operación. Tenga en cuenta que una vez eliminado el servicio no podrá recuperarse. Adicionalmente, tenga en cuenta que si alguna operación del servicio esta en uso en algún proceso este dejará de funcionar correctamente.</li></ul>",
            placement: 'top'
          }
        ],
        template: template,
        storage: false
      });

      // Initialize the tour
      tour.init();

      // Start the tour
      tour.start();
    });

    // -- Ayuda contextual SERVICIOS EDITAR Operaciones
    $('#ayuda_contextual_operacion_editar').click(function() {

      var soap_body = "<pre>";
          soap_body += "&lt;soap:Body xmlns:ser='http://name/'&gt;<br>";
          soap_body += "  &lt;ser:paramPeriodo&gt;<br>";
          soap_body += "    &lt;cod&gt;&lt;@@codigo/cod&gt;<br>";
          soap_body += "    &lt;fecha1&gt;2012-07-11&lt;/fecha1&gt;<br>";
          soap_body += "    &lt;fecha2&gt;2012-07-12&lt;/fecha2&gt;<br>";
          soap_body += "    &lt;idOrigen&gt;999&lt;/idOrigen&gt;<br>";
          soap_body += "  &lt;/ser:paramPeriodo&gt;<br>";
          soap_body += "&lt;/soap:Body&gt;";
          soap_body += "</pre>";

      var soap_retorno = "<pre>";
          soap_retorno += "&lt;soap:Body xmlns:ser='http://name/'&gt;<br>";
          soap_retorno += "  &lt;ser:response&gt;<br>";
          soap_retorno += "    &lt;cod&gt;&lt;234234/cod&gt;<br>";
          soap_retorno += "  &lt;/ser:response&gt;<br>";
          soap_retorno += "&lt;/soap:Body&gt;";
          soap_retorno += "</pre>";

      var tour = new Tour({
        steps: [
          {
            element: "#nombre",
            title: "Datos de la Operación",
            content: "Los datos requeridos de la operación son: <br><br><ul><li><b>Código:</b> Cuando se exporta un proceso, las invocaciones a los servicios web se asocian con el catálogo usando este código. Por lo tanto cuando se importe un proceso, deberá existir este código en el catálogo de servicios.</li><li><b>Nombre:</b> Es el nombre que se utilizará para seleccionar la operación en el proceso.</li><li><b>Nombre real de operación:</b> Es el WSAction de la operación</li></ul>",
            placement: "right"
          },
          {
            element: "#soap",
            title: "Datos de la Operación",
            content: "Cuerpo del mensaje SOAP que se enviará al servicio web. Puede incluir variables de Simple. Debe contener los namespaces correspondientes, por ejemplo:<br><br>" + soap_body,
            placement: "left"
          },
          {
            element: "#ayuda",
            title: "Datos de la Operación",
            content: "Texto de ayuda que se mostrará al modelador al momento de crear la acción basada en la operación.",
            placement: "left"
          },
          {
            element: "#respuestas_visual",
            title: "Datos de la Operación",
            content: "Las respuestas permiten procesar el XML resultado de la invocación y generar variables de Simple. Para definir una respuesta se debe utilizar XPATH o XSL para procesar el resultado. Por ejemplo si el servicio retorna: <br><br>" + soap_retorno + "Para generar una variable con el valor del elemento cod de la respuesta se crea una respuesta con nombre codigo (es el nombre de la variable que se genera en simple), tipo Texto y en el campo xpath la expresión: <br><br>//*[local-name() = 'cod'].</br><br> El tipo Lista solicita que se ingrese un XSLT para transformar una colección a un XML conocido por Simple. Por más información consulte el Manual de Usuario. ",
            placement: "top"
          }
        ],
        template: template,
        storage: false
      });

      // Initialize the tour
      tour.init();

      // Start the tour
      tour.start();
    });


  // -- Ayuda contextual BLOQUES
  $('#ayuda_contextual_bloques').click(function() {
    var tour = new Tour({
      steps: [
        {
          element: "#accion-bloques",
          title: "Catálogo de Bloques",
          content: "El catálogo de bloques permite crear grupos de campos y componentes que podrán insertarse posteriormente en formularios de tareas.",
          placement: "top"
        },
        {
          element: "#accion-nuevo-bloque",
          title: "Crear nuevo bloque",
          content: "Haciendo clic en este botón usted podrá crear un nuevo bloque."
        },
        {
          element: "#accion-lista-bloques",
          title: "Listado de bloques",
          content: "En este listado usted podrá ver todos los bloques creados. Para cada bloque verá los siguientes botones: <br><br><ul><li><b>Editar:</b> Permite modificar el bloque.</li><li><b>Eliminar:</b> Permite eliminar el bloque. Tenga en cuenta que una vez eliminado el bloque no podrá ser recuperado.</li></ul>",
          placement: "top"
        }
      ],
      template: template,
      storage: false
    });

    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();
  });

  // -- Ayuda contextual PASARELA
  $('#ayuda_contextual_pasarela').click(function() {
    var tour = new Tour({
      steps: [
        {
          element: "#accion-pasarela",
          title: "Pasarela de Pagos",
          content: "El catálogo de Pasarela de Pagos permite crear pasarelas para poder insertalas posteriormente en procesos.",
          placement: "top"
        },
        {
          element: "#accion-nueva-pasarela",
          title: "Crear nueva pasarela de pagos",
          content: "Haciendo clic en este botón usted podrá crear una nueva pasarela de pagos."
        },
        {
          element: "#accion-lista-pasarelas",
          title: "Listado de pasarelas de pago",
          content: "En este listado usted podrá ver todas las pasarelas de pago creadas. Para cada pasarela verá los siguientes botones: <br><br><ul><li><b>Editar:</b> Permite modificar la configuración de la pasarela de pagos.</li><li><b>Eliminar:</b> Permite eliminar la pasarela. Tenga en cuenta que una vez eliminada la pasarela no podrá ser recuperada.</li></ul>",
          placement: "top"
        }
      ],
      template: template,
      storage: false
    });

    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();
  });

  // -- Ayuda contextual MODELADOR
  $('#ayuda_contextual_modelador').click(function() {
    var tour = new Tour({
      steps: [
        {
          element: "#accion-modelador",
          title: "Modelador de procesos",
          content: "El modelador de procesos permite crear tareas, formularios, documentos y acciones que serán parte del proceso. A continuación se listan las pestañas que forman parte del modelador: <br><br><ul><li><b>Diseñador:</b> Permite agregar tareas, conectarlas entre sí mediante conexiones secuenciales, conexiones con evaluación, etc.</li><li><b>Formularios:</b> Permite crear formularios para ser incluidos como pasos dentro de las tareas.</li><li><b>Documentos:</b> Permite crear documentos (documentos y certificados) que podrán ser insertados posteriormente en formularios.</li><li><b>Acciones:</b> Permite crear acciones para ser insertadas entre pasos. Estos son los tipos de acciones disponibles: <br><ul><li>Catálogo de servicios.</li><li>Pasarela de pago.</li><li>Envío de correo.</li><li>Generar variable.</li></ul></li><li><b>Trazabilidad:</b> Permite configurar los datos requeridos por trazabilidad para poder trazar tareas.</li></ul>",
          placement: "right"
        }
      ],
      template: template,
      storage: false
    });

    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();
  });

  // -- Ayuda contextual PROCESOS
  $('#ayuda_contextual_pdi').click(function() {
    var tour = new Tour({
      steps: [
        {
          element: "#accion-pdi",
          title: "Configuración de plataforma de interoperabilidad",
          content: "En este formulario usted podrá configurar las credenciales para utilizar la plataforma de interoperabilidad. Los datos requeridos son los siguientes: <br><br><ul><li><b>STS:</b> URL provista por PDI.</li><li><b>Policy name:</b> Dato provisto por PDI.</li><li><b>PKC12 de Organismo:</b> Certificado con el cual se firmaran las invocaciones a PDI, debe estar en formato PKC12.</li><li><b>PKC12 de Organismo clave:</b> Clave correspondiente al certificado anterior.</li><li><b>PEM SSL:</b> Certificado en formato PEM para la conexión SSL con PDI. Debe ser tramitado con Agesic.</li><li><b>PEM SSL clave:</b> Clave correspondiente al certificado anterior.</li></ul>",
          placement: "top"
        }
      ],
      template: template,
      storage: false
    });

    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();
  });
});

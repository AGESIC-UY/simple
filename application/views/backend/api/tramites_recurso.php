<div class="row-fluid">
    <div class="span3">
        <?php $this->load->view('backend/api/sidebar') ?>
    </div>
    <div class="span9">
        <h2><?= $title ?></h2>

        <p>Tramites es un listado de tramites de SIMPLE. Los métodos permiten obtener información de un trámite, listar una serie de trámites o listar trámites que son parte de un proceso en particular.</p>
        
        <h3>Métodos</h3>
        
        <dl>
            <dt><a href="<?=site_url('backend/api/tramites_obtener')?>">obtener</a></dt>
            <dd>Obtiene un recurso trámite.</dd>
            <dt><a href="<?=site_url('backend/api/tramites_listar')?>">listar</a></dt>
            <dd>Obtiene el listado completo de trámites de la cuenta.</dd>
            <dt><a href="<?=site_url('backend/api/tramites_listarporproceso')?>">listarPorProceso</a></dt>
            <dd>Obtiene el listado de trámites pertenecientes a un proceso en particular.</dd>
        </dl>
        
        <h3>Representación del recurso</h3>
        
        <p>Un recurso es representado como una estructura json. Este es un ejemplo de cómo se vería un recurso.</p>
        
        <pre>{
    "tramite":{
        "id":502,
        "estado":"pendiente",
        "proceso_id":9,
        "fecha_inicio":"2013-08-08 16:51:56",
        "fecha_modificacion":"2013-08-08 16:51:56",
        "fecha_termino":null,
        "etapas":[
            {
                "id":710,
                "estado":"pendiente",
                "usuario_asignado":{
                    "usuario":"jperez",
                    "email":"jperez@ejemplo.com",
                    "nombres":"Juan",
                    "apellido_paterno":"Perez",
                    "apellido_materno":"Cotapo"
                },
                "fecha_inicio":"2013-08-08 16:51:56",
                "fecha_modificacion":"2013-08-08 16:51:56",
                "fecha_termino":null,
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
<?php
require_once('accion.php');

class AccionWebservice extends Accion {

    public function displayForm() {
        $display ='<p class="strong">Esta accion consultara via REST la siguiente URL. Los resultados, seran almacenados como variables.</p>';
        $display.='<p>Los resultados esperados deben venir en formato JSON siguiendo este formato:</p>';
        $display.='<pre>
{
    "variable1": "valor1",
    "variable2": "valor2",
    ...
}</pre>';
        $display.='<div class="form-horizontal">';
        $display.='<div class="control-group">';
        $display.='<label for="url" class="control-label">URL</label>';
        $display.='<div class="controls">';
        $display.='<input id="url" type="text" class="input-xxlarge" name="extra[url]" value="' . ($this->extra ? $this->extra->url : '') . '" />';
        $display.='</div>';
        $display.='</div>';
        $display.='</div>';

        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
        $CI->form_validation->set_rules('extra[url]', 'URL', 'required');
    }

    public function ejecutar(Etapa $etapa) {
        $r = new Regla($this->extra->url);
        $url = $r->getExpresionParaOutput($etapa->id);

        //Hacemos encoding a la url
        $url = preg_replace_callback('/([\?&][^=]+=)([^&]+)/', function($matches) {
            $key = $matches[1];
            $value = $matches[2];
            return $key . urlencode($value);
        }, $url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );   //Ticket #458793

        if (!empty(PROXY_WS)) {
            curl_setopt($ch, CURLOPT_PROXY, PROXY_WS);
        }

        $curl_errno = curl_errno($ch); // -- Codigo de error
        $curl_error = curl_error($ch); // -- Descripcion del error
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // -- Codigo respuesta HTTP
        $result = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($result);
        foreach ($json as $key => $value) {
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($key, $etapa->id);
            if (!$dato)
                $dato = new DatoSeguimiento();
            $dato->nombre = $key;
            $dato->valor = $value;
            $dato->etapa_id = $etapa->id;
            $dato->save();
        }
    }

}

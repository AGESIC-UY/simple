<?php
require_once('campo.php');
class CampoPaises extends Campo{

    public $requiere_datos=false;

    protected function display($modo, $dato) {
        $display = '<div class="control-group">';
        $display.= '<label class="control-label" for="'.$this->id.'" data-fieldset="'.$this->fieldset.'">' . $this->etiqueta . (in_array('required', $this->validacion) ? '*:' : ' (Opcional):') . '</label>';
        $display.='<div class="controls" data-fieldset="'.$this->fieldset.'">';
        $display.='<select class="paises" id="'.$this->id.'" data-id="'.$this->id.'" name="' . $this->nombre . '" ' . ($modo == 'visualizacion' ? 'readonly' : '') . '>';
        $display.='<option value="">Seleccione país</option>';
        $display.='</select>';
        if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';
        $display.='</div>';
        $display.='</div>';

        $display.='
            <script>
                $(document).ready(function(){
                    var justLoadedPais=true;
                    var defaultPais="'.($dato && $dato->valor?$dato->valor:'').'";

                    updatePaises();

                    function updatePaises(){
                            var data={"UY":"Uruguay","AF":"Afganist\u00e1n","AL":"Albania","DE":"Alemania","AD":"Andorra","AO":"Angola","AI":"Anguila","AG":"Antigua y Barbuda","AN":"Antillas Holandesas","AQ":"Ant\u00e1rtida","SA":"Arabia Saudita","DZ":"Argelia","AR":"Argentina","AM":"Armenia","AW":"Aruba","AU":"Australia","AT":"Austria","AZ":"Azerbay\u00e1n","BS":"Bahamas","BH":"Bahr\u00e9in","BD":"Bangladesh","BB":"Barbados","BZ":"Belice","BJ":"Ben\u00edn","BM":"Bermudas","BY":"Bielorrusia","BO":"Bolivia","BA":"Bosnia-Herzegovina","BW":"Botsuana","BR":"Brasil","BN":"Brun\u00e9i","BG":"Bulgaria","BF":"Burkina Faso","BI":"Burundi","BT":"But\u00e1n","BE":"B\u00e9lgica","CV":"Cabo Verde","KH":"Camboya","CM":"Camer\u00fan","CA":"Canad\u00e1","TD":"Chad","CL":"Chile","CN":"China","CY":"Chipre","VA":"Ciudad del Vaticano","CO":"Colombia","KM":"Comoras","CG":"Congo","KP":"Corea del Norte","KR":"Corea del Sur","CR":"Costa Rica","CI":"Costa de Marfil","HR":"Croacia","CU":"Cuba","DK":"Dinamarca","DM":"Dominica","EC":"Ecuador","EG":"Egipto","SV":"El Salvador","AE":"Emiratos \u00c1rabes Unidos","ER":"Eritrea","SK":"Eslovaquia","SI":"Eslovenia","ES":"Espa\u00f1a","US":"Estados Unidos","EE":"Estonia","ET":"Etiop\u00eda","PH":"Filipinas","FI":"Finlandia","FJ":"Fiyi","FR":"Francia","GA":"Gab\u00f3n","GM":"Gambia","GE":"Georgia","GH":"Ghana","GI":"Gibraltar","GD":"Granada","GR":"Grecia","GL":"Groenlandia","GP":"Guadalupe","GU":"Guam","GT":"Guatemala","GF":"Guayana Francesa","GG":"Guernsey","GN":"Guinea","GQ":"Guinea Ecuatorial","GW":"Guinea-Bissau","GY":"Guyana","HT":"Hait\u00ed","HN":"Honduras","HU":"Hungr\u00eda","IN":"India","ID":"Indonesia","IQ":"Iraq","IE":"Irlanda","IR":"Ir\u00e1n","BV":"Isla Bouvet","CX":"Isla Christmas","NU":"Isla Niue","NF":"Isla Norfolk","IM":"Isla de Man","IS":"Islandia","KY":"Islas Caim\u00e1n","CC":"Islas Cocos","CK":"Islas Cook","FO":"Islas Feroe","GS":"Islas Georgia del Sur y Sandwich del Sur","HM":"Islas Heard y McDonald","FK":"Islas Malvinas","MP":"Islas Marianas del Norte","MH":"Islas Marshall","SB":"Islas Salom\u00f3n","TC":"Islas Turcas y Caicos","VG":"Islas V\u00edrgenes Brit\u00e1nicas","VI":"Islas V\u00edrgenes de los Estados Unidos","UM":"Islas menores alejadas de los Estados Unidos","AX":"Islas \u00c5land","IL":"Israel","IT":"Italia","JM":"Jamaica","JP":"Jap\u00f3n","JE":"Jersey","JO":"Jordania","KZ":"Kazajist\u00e1n","KE":"Kenia","KG":"Kirguist\u00e1n","KI":"Kiribati","KW":"Kuwait","LA":"Laos","LS":"Lesoto","LV":"Letonia","LR":"Liberia","LY":"Libia","LI":"Liechtenstein","LT":"Lituania","LU":"Luxemburgo","LB":"L\u00edbano","MK":"Macedonia","MG":"Madagascar","MY":"Malasia","MW":"Malaui","MV":"Maldivas","ML":"Mali","MT":"Malta","MA":"Marruecos","MQ":"Martinica","MU":"Mauricio","MR":"Mauritania","YT":"Mayotte","FM":"Micronesia","MD":"Moldavia","MN":"Mongolia","ME":"Montenegro","MS":"Montserrat","MZ":"Mozambique","MM":"Myanmar","MX":"M\u00e9xico","MC":"M\u00f3naco","NA":"Namibia","NR":"Nauru","NP":"Nepal","NI":"Nicaragua","NG":"Nigeria","NO":"Noruega","NC":"Nueva Caledonia","NZ":"Nueva Zelanda","NE":"N\u00edger","OM":"Om\u00e1n","PK":"Pakist\u00e1n","PW":"Palau","PA":"Panam\u00e1","PG":"Pap\u00faa Nueva Guinea","PY":"Paraguay","NL":"Pa\u00edses Bajos","PE":"Per\u00fa","PN":"Pitcairn","PF":"Polinesia Francesa","PL":"Polonia","PT":"Portugal","PR":"Puerto Rico","QA":"Qatar","HK":"Regi\u00f3n Administrativa Especial de Hong Kong de la Rep\u00fablica Popular China","MO":"Regi\u00f3n Administrativa Especial de Macao de la Rep\u00fablica Popular China","ZZ":"Regi\u00f3n desconocida o no v\u00e1lida","GB":"Reino Unido","CF":"Rep\u00fablica Centroafricana","CZ":"Rep\u00fablica Checa","CD":"Rep\u00fablica Democr\u00e1tica del Congo","DO":"Rep\u00fablica Dominicana","RE":"Reuni\u00f3n","RW":"Ruanda","RO":"Rumania","RU":"Rusia","EH":"Sahara Occidental","WS":"Samoa","AS":"Samoa Americana","BL":"San Bartolom\u00e9","KN":"San Crist\u00f3bal y Nieves","SM":"San Marino","MF":"San Mart\u00edn","PM":"San Pedro y Miquel\u00f3n","VC":"San Vicente y las Granadinas","SH":"Santa Elena","LC":"Santa Luc\u00eda","ST":"Santo Tom\u00e9 y Pr\u00edncipe","SN":"Senegal","RS":"Serbia","CS":"Serbia y Montenegro","SC":"Seychelles","SL":"Sierra Leona","SG":"Singapur","SY":"Siria","SO":"Somalia","LK":"Sri Lanka","SZ":"Suazilandia","ZA":"Sud\u00e1frica","SD":"Sud\u00e1n","SE":"Suecia","CH":"Suiza","SR":"Surinam","SJ":"Svalbard y Jan Mayen","TH":"Tailandia","TW":"Taiw\u00e1n","TZ":"Tanzan\u00eda","TJ":"Tayikist\u00e1n","IO":"Territorio Brit\u00e1nico del Oc\u00e9ano \u00cdndico","PS":"Territorio Palestino","TF":"Territorios Australes Franceses","TL":"Timor Oriental","TG":"Togo","TK":"Tokelau","TO":"Tonga","TT":"Trinidad y Tobago","TM":"Turkmenist\u00e1n","TR":"Turqu\u00eda","TV":"Tuvalu","TN":"T\u00fanez","UA":"Ucrania","UG":"Uganda","UY":"Uruguay","UZ":"Uzbekist\u00e1n","VU":"Vanuatu","VE":"Venezuela","VN":"Vietnam","WF":"Wallis y Futuna","YE":"Yemen","DJ":"Yibuti","ZM":"Zambia","ZW":"Zimbabue"};
                            var html="<option value=\'\'>Seleccione país</option>";
                            $.each(data,function(i,el){
                                html+="<option value=\""+el+"\">"+el+"</option>";
                            });

                            $("select.paises[data-id='.$this->id.']").html(html);

                            if(justLoadedPais){
                                $("select.paises[data-id='.$this->id.']").val(defaultPais).change();
                                justLoadedPais=false;
                            }

                    }

                });

            </script>';

        return $display;
    }
}

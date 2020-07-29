<?php

require_once('tcpdf/config/lang/spa.php');
require_once('tcpdf/tcpdf.php');
require_once('fpdi/Fpdi.php');

class PdfConcat extends setasign\Fpdi\Fpdi {

    var $files = array();

    function setFiles($files) {
        $this->files = $files;
    }

    function concat() {
        try {
            foreach ($this->files AS $file) {
                $pagecount = $this->setSourceFile($file);
                for ($i = 1; $i <= $pagecount; $i++) {
                    $tplidx = $this->ImportPage($i);
                    $s = $this->getTemplatesize($tplidx);
                    $this->AddPage($s['orientation'], array($s['width'], $s['height']));
                    $this->useTemplate($tplidx);
                }
            }
        } catch (Exception $exc) {
            
        }
    }

}

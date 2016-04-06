<?php

require_once 'tcpdf/tcpdf.php';

define('PAGE_MARGIN', 10);

class BlancoPDF extends TCPDF {

    public $content='';
    

    function __construct($size = 'letter') {
        parent::__construct('P', 'mm', $size, true, 'UTF-8', false, false);

        $this->SetMargins(PAGE_MARGIN, PAGE_MARGIN, PAGE_MARGIN);
    }

    public function Header() {

    }

    public function Content() {
        $this->addPage();
        $this->SetFont('helvetica', '', 10);
        $this->writeHTML($this->content);
    }

    public function Footer() {
       
    }

    public function Output($name = 'doc.pdf', $dest = 'I') {
        $this->Content();
        return parent::Output($name, $dest);
    }

}
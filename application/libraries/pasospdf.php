<?php

require_once 'tcpdf/tcpdf.php';

define('PAGE_MARGIN', 10);
define('CELL_WIDTH', 33);
define('GRID_WIDTH', 8);

class PasosPDF extends TCPDF {

    public $logo='assets/img/certificados/logo-tramites.png';

    public $content='';


    function __construct($size = 'letter') {
        parent::__construct('P', 'mm', $size, true, 'UTF-8', false, false);

        $this->SetMargins(PAGE_MARGIN, PAGE_MARGIN, PAGE_MARGIN);
        $this->SetTopMargin(55);
    }

    public function Header() {
      if($this->cuenta->logo){
        $logo = 'uploads/logos/'.$this->cuenta->logo;
      }
      else{
        $logo = 'assets/img/simple.png';
      }
      $this->Image($logo, PAGE_MARGIN, PAGE_MARGIN, CELL_WIDTH, 30, '', '', 'T',true,300,'',false,false,0,true);

      $this->SetFont('dejavusans', 'B', 10);
      $this->MultiCell(2 * CELL_WIDTH + GRID_WIDTH, 24, ' | '.$this->cuenta->nombre_largo, 0, 'L', false, 1, 42, -85, true, 0, false, true, 110, 'B');

      $this->SetFont('dejavusans', 'B', 16);
      $this->MultiCell(2 * CELL_WIDTH + GRID_WIDTH, 24, $this->proceso_nombre, 0, 'C', false, 1, PAGE_MARGIN + CELL_WIDTH + GRID_WIDTH, 15, true, 0, false, true, 24, 'B');

      $this->SetFont('dejavusans', 'B', 10);
      $this->MultiCell(CELL_WIDTH, 5, $this->id, 0, 'L', false, 1,PAGE_MARGIN + 4 * CELL_WIDTH + 4 * GRID_WIDTH);
      $this->write1DBarcode($this->id, 'C128',PAGE_MARGIN + 4 * CELL_WIDTH + 4 * GRID_WIDTH,'',CELL_WIDTH,7,'',array('text'=>true));
      $this->SetFont('dejavusans', '', 10);
      $this->MultiCell(55, 5, 'Página '.$this->getPage().' de '.$this->getAliasNbPages(), 0, 'L', false, 1,PAGE_MARGIN + 4 * CELL_WIDTH + 4 * GRID_WIDTH,PAGE_MARGIN+26);

      $this->Line(PAGE_MARGIN, PAGE_MARGIN + 35, PAGE_MARGIN + 5 * CELL_WIDTH + 4 * GRID_WIDTH, PAGE_MARGIN + 35, array('width' => 1));
    }

    public function Content() {
        $this->addPage();
        $this->SetFont('dejavusans', '', 10);
        $this->writeHTML($this->content);

    }

    public function Footer() {

    }

    public function Output($name = 'doc.pdf', $dest = 'I') {
        $this->Content();
        return parent::Output($name, $dest);
    }

}

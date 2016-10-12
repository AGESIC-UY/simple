<?php

require_once 'tcpdf/tcpdf.php';
ini_set('memory_limit', '64M');

define('PAGE_MARGIN', 10);
define('CELL_WIDTH', 33);
define('GRID_WIDTH', 8);

class CertificadoPDF extends TCPDF {

    public $id='123456789';
    public $key='abcdefghijkl';
    public $servicio='Servicio';
    public $servicio_url='https://ejemplo.gub.uy';
    public $logo='assets/img/certificados/logo-tramites.png';
    public $titulo='Título';
    public $subtitulo='Subtítulo';
    public $validez=null;
    public $validez_habiles=false;
    public $timbre='';
    public $firmador_nombre='Juan Perez';
    public $firmado_cargo='Director';
    public $firmador_servicio='Tramites.gub.uy';
    public $firmador_imagen=null;
    public $firma_electronica=false;
    public $copia=false;
    public $content='';


    function __construct($size = 'letter') {
        parent::__construct('P', 'mm', $size, true, 'UTF-8', false, false);

        $this->SetMargins(PAGE_MARGIN, PAGE_MARGIN, PAGE_MARGIN);
        $this->SetTopMargin(75);
        $this->SetAutoPageBreak(TRUE, 90);
    }

    public function Header() {
        $this->Image($this->logo, PAGE_MARGIN, PAGE_MARGIN, CELL_WIDTH, 30, '', '', 'T',true,300,'',false,false,0,true);

        $this->SetFont('helvetica', 'B', 16);
        $this->MultiCell(2 * CELL_WIDTH + GRID_WIDTH, 24, $this->servicio, 0, 'L', false, 1, PAGE_MARGIN + CELL_WIDTH + GRID_WIDTH, PAGE_MARGIN, true, 0, false, true, 24, 'B');

        $this->SetFont('helvetica', '', 10);
        $this->MultiCell(2 * CELL_WIDTH + GRID_WIDTH, 6, $this->servicio_url, 0, 'L', false, 1, PAGE_MARGIN + CELL_WIDTH + GRID_WIDTH, PAGE_MARGIN + 26);

        $this->Line(PAGE_MARGIN + 4 * CELL_WIDTH + 4 * GRID_WIDTH,PAGE_MARGIN,PAGE_MARGIN + 5 * CELL_WIDTH + 4 * GRID_WIDTH,PAGE_MARGIN,array('width'=>1));
        $this->SetFont('helvetica', '', 10);
        $this->MultiCell(CELL_WIDTH, 5, 'Folio:', 0, 'L', false, 1, PAGE_MARGIN + 4 * CELL_WIDTH + 4 * GRID_WIDTH, PAGE_MARGIN+1);
        $this->SetFont('helvetica', 'B', 10);
        $this->MultiCell(CELL_WIDTH, 5, $this->id, 0, 'L', false, 1,PAGE_MARGIN + 4 * CELL_WIDTH + 4 * GRID_WIDTH);
        $this->write1DBarcode($this->id, 'C128',PAGE_MARGIN + 4 * CELL_WIDTH + 4 * GRID_WIDTH,'',CELL_WIDTH,7,'',array('text'=>true));
        $this->SetFont('helvetica', '', 10);
        $this->MultiCell(CELL_WIDTH, 5, 'Pagina '.$this->getPage().' de '.$this->getAliasNbPages(), 0, 'L', false, 1,PAGE_MARGIN + 4 * CELL_WIDTH + 4 * GRID_WIDTH,PAGE_MARGIN+26);

        $this->Line(PAGE_MARGIN, PAGE_MARGIN + 35, PAGE_MARGIN + 5 * CELL_WIDTH + 4 * GRID_WIDTH, PAGE_MARGIN + 35, array('width' => 1));

        $this->SetFont('helvetica', '', 18);
        $this->MultiCell(5 * CELL_WIDTH + 4 * GRID_WIDTH, 10, $this->titulo, 0, 'L', false, 1, PAGE_MARGIN, PAGE_MARGIN + 40);
        $this->SetFont('helvetica', '', 10);
        $this->MultiCell(5 * CELL_WIDTH + 4 * GRID_WIDTH, 10, $this->subtitulo, 0, 'L', false, 1);

        $this->Line(PAGE_MARGIN, PAGE_MARGIN + 60, PAGE_MARGIN + 5 * CELL_WIDTH + 4 * GRID_WIDTH, PAGE_MARGIN + 60, array('width' => 0.5));

        if($this->copia)
            $this->Image('assets/img/copia.png', PAGE_MARGIN, 75);
    }

    public function Content() {
        $this->addPage();
        $this->SetFont('helvetica', '', 10);
        $this->writeHTML($this->content);
    }

    public function Footer() {
        $this->SetY(-85);
        $y=$this->GetY();

        $this->Line(PAGE_MARGIN,$y,PAGE_MARGIN+5*CELL_WIDTH+4*GRID_WIDTH,$y);
        $this->SetFont('helvetica', '', 11);
        $this->MultiCell(CELL_WIDTH, 8, 'Fecha de Emisión:', 0, 'L', false, 1, PAGE_MARGIN, $y,true,0,false,true,8,'M');
        $this->MultiCell(2*CELL_WIDTH+GRID_WIDTH, 8, strftime('%d %B %Y, %k:%M'), 0, 'L', false, 1, PAGE_MARGIN+CELL_WIDTH+GRID_WIDTH, $y,true,0,false,true,8,'M');
        $this->MultiCell(2*CELL_WIDTH+GRID_WIDTH, 8, $this->validez===null?'Válido indefinidamente':'Válido por '.$this->validez.' días'.($this->validez_habiles?' hábiles':'').'.', 0, 'R', false, 1, PAGE_MARGIN+3*CELL_WIDTH+3*GRID_WIDTH, $y,true,0,false,true,8,'M');
        $this->Line(PAGE_MARGIN,$y+8,PAGE_MARGIN+5*CELL_WIDTH+4*GRID_WIDTH,$y+8);

        $this->SetFont('helvetica', '', 10);
        $this->MultiCell(CELL_WIDTH, 6, 'Código Verificación:', 0, 'L', false, 1, PAGE_MARGIN, $y+10);
        $this->write2DBarcode(site_url('validador/documento?id='.$this->id.'&key='.$this->key), 'QRCODE,H','','',30,'');

        $this->Line(PAGE_MARGIN+CELL_WIDTH,$y+10,PAGE_MARGIN+CELL_WIDTH,$y+48);

        if($this->timbre)
            $this->Image($this->timbre, PAGE_MARGIN+1*CELL_WIDTH+1*GRID_WIDTH, $y+10, 2*CELL_WIDTH+GRID_WIDTH, 40, '', '', 'T',true,300,'',false,false,0,true);

        if($this->firmador_imagen)
            $this->Image($this->firmador_imagen, PAGE_MARGIN+3*CELL_WIDTH+2*GRID_WIDTH, $y+10, 2*CELL_WIDTH+GRID_WIDTH, 25, '', '', 'T',true,300,'',false,false,0,true);
        $this->SetFont('helvetica', 'B', 10);
        $this->MultiCell(2*CELL_WIDTH+GRID_WIDTH, 10, $this->firmador_nombre."\n".$this->firmado_cargo, 0, 'L', false, 1, PAGE_MARGIN+3*CELL_WIDTH+2*GRID_WIDTH, $y+35);
        $this->SetFont('helvetica', '', 10);
        $this->MultiCell(2*CELL_WIDTH+GRID_WIDTH, 10, $this->firmador_servicio, 0, 'L', false, 1, PAGE_MARGIN+3*CELL_WIDTH+2*GRID_WIDTH,$y+44);

        $this->Line(PAGE_MARGIN,$y+50,PAGE_MARGIN+5*CELL_WIDTH+4*GRID_WIDTH,$y+50);

        $this->SetFont('helvetica', '', 11);
        $this->MultiCell(4*CELL_WIDTH+3*GRID_WIDTH, '', 'Código de Verificación:', 0, 'L', false, 1, PAGE_MARGIN, $y+50);
        $this->SetFont('helvetica', 'B', 11);
        $this->MultiCell(4*CELL_WIDTH+3*GRID_WIDTH, '', implode(' ',str_split($this->key,4)), 0, 'L', false, 1, PAGE_MARGIN);
        $this->SetFont('helvetica', '', 10);
        $this->MultiCell(4*CELL_WIDTH+3*GRID_WIDTH, '', 'Verifique la validez de este documento en:', 0, 'L', false, 1, PAGE_MARGIN);
        $this->MultiCell(4*CELL_WIDTH+3*GRID_WIDTH, '', site_url('validador'), 0, 'L', false, 1, PAGE_MARGIN);

        if($this->firma_electronica){
            $this->Image('assets/img/certificados/candado.png', PAGE_MARGIN+4*CELL_WIDTH+4*GRID_WIDTH, $y+55, 5, '', '', '', 'T',true);
            $this->SetFont('helvetica', '', 8);
            $this->MultiCell(CELL_WIDTH-5, '', 'Incorpora Firma Electrónica Avanzada', 0, 'L', false, 1, PAGE_MARGIN+4*CELL_WIDTH+4*GRID_WIDTH+5, $y+55);
        }

        // $this->Image('assets/img/certificados/color_gobierno-chile.png', PAGE_MARGIN, $y+70, CELL_WIDTH, 5, '', '', 'T',true);

    }

    public function Output($name = 'doc.pdf', $dest = 'I') {
        $this->Content();
        return parent::Output($name, $dest);
    }

}

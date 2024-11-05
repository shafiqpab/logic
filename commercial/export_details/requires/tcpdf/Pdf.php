<?php
require_once 'tcpdf.php';
class Pdf extends TCPDF
{ 
    private $header=array();
	private $footer=array();
	function __construct($header=array(),$footer=array()) 
	{ 
	    $this->header=$header;
	    $this->footer=$footer;
		parent::__construct(); 
	}
	 //Page header
    public function Header() {
        // Logo
       // $image_file = K_PATH_IMAGES.'tcpdf_logo.jpg';
       //$this->Image($image_file, 15, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 14);
		$this->SetTextColor(70, 163, 8);
		$this->SetY(15);
        // Title
		$cell_height = round(($this->cell_height_ratio * 20) / $this->k, 2);
		if ($this->getRTL()) {
				$header_x = $this->original_rMargin + (15 * 1.1);
			} else {
				$header_x = $this->original_lMargin + (15 * 1.1);
			}
        $this->Cell(0, 15, $this->header['company'], 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->SetX($header_x);
		$this->SetY(19);
		$this->SetFont('helvetica', 'I', 8);
		$this->SetTextColor(0, 0, 0);
		$this->MultiCell('', $cell_height, $this->header['address'], 0, 'C', 0, 1, '', '', true, 0, false, true, 0, 'T', false);
    }

    // Page footer
    public function Footer() {
		$this->SetY(-20);
		if ($this->rtl) {
		$this->SetX($this->original_rMargin);
		} else {
		$this->SetX($this->original_lMargin);
		}
		$this->Cell(($this->w - $this->original_lMargin - $this->original_rMargin), 0, '', 'T', 0, 'C');
		$this->SetLineStyle(array('width' => 0.85 / $this->k, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		
		// Position at 15 mm from bottom
		
		$this->SetY(-17);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		//$this->MultiCell('', 5, $this->header['slogan'], 0, 'C', 0, 1, '', '', true, 0, false, true, 0, 'T', false);
		//$this->SetY(-10);
		$this->Cell(0, 5, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages()."          Print Date: ".date('d-m-Y h:i:s'), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
	public function clean() {
		ob_end_clean();
	}
}
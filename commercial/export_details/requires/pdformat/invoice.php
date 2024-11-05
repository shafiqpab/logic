<?
class invoice {
	private $html=0;
	private $pdf="";
	private $file="";
	private $vendor="tcpdf";
	private $title = "Commercial Invoice";
	private $keywords = "Commercial Invoice, Commercial Invoice from System";
	private $header=array();
	private $footer=array();
	function __construct($html,$header=array(),$footer=array(),$is_header=true,$is_fotter=true,$is_title=true) 
	{
	    $this->html=$html;
		$this->header=$header;
		$this->pdf=$this->load();
		$this->pdf->SetCreator(PDF_CREATOR);
		$this->pdf->SetAuthor($_SESSION['logic_erp']['user_id']);
		$this->pdf->SetTitle($this->header['company']);
		//$this->pdf->SetSubject($this->title);
		$this->pdf->SetKeywords($this->keywords);
		
		$this->pdf->setPrintHeader($is_header);
        $this->pdf->setPrintFooter($is_fotter);
		
		$this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH,$this->header['company'], '');
		$this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$this->pdf->SetFont('helvetica', 'B', 12);
		$this->pdf->AddPage();
		$this->pdf->SetY(25);
		if($is_title){
		 $this->pdf->Write(0, $this->title, '', 0, 'C', true, 0, false, false, 0);
		}
		$this->pdf->SetFont('helvetica', 'R', 8);
		$this->pdf->SetY(30);
		//$txt = " EXPORT REGD. NO. RA.-53979";
		//$this->pdf->Write(0, $txt, '', 0, 'R', true, 0, false, false, 0);
		$this->pdf->SetFont('helvetica', '', 8);
		$this->pdf->writeHTML($this->html, true, false, false, false, '');
		$this->file="invoice".$update_id.time()."pdf";
	}
	public function show(){
		$this->pdf->clean();
		$this->pdf->Output($this->file, 'I');
	}
	private function load(){
		require_once $this->vendor.'/Pdf.php';
		return new Pdf($this->header);
	}
}
?>
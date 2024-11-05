<?php
require_once('../config/lang/eng.php');
require_once('../tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
/*
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 049');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
*/

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 049', PDF_HEADER_STRING);

// set header and footer fonts
//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
//$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('times', '', 11);

// add a page
//$pdf->AddPage();
$pdf->AddPage('L', 'A4');


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

IMPORTANT:
If you are printing user-generated content, tcpdf tag can be unsafe.
You can disable this tag by setting to false the K_TCPDF_CALLS_IN_HTML
constant on TCPDF configuration file.

For security reasons, the parameters for the 'params' attribute of TCPDF 
tag must be prepared as an array and encoded with the 
serializeTCPDFtagParameters() method (see the example below).

 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


$html .= '<h3>Test TCPDF Methods in HTML</h3>';

$html .= '<table border="1">';
for($i=1; $i<2;$i++):
	$html .= '<tr>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
		<td>Color Type</td>
	</tr>';
endfor;
$html .= '</table>';


$html .= '<p>ddd</p>';

ob_start();
?>
<table border="1" width="100%">
	<thead>
        <tr><th colspan="6" align="center"> Header Title </th></tr>
	</thead>
	<tr>
        <td rowspan="2">1</td><td>Dhaka</td>
        <td>2</td><td>Barisal</td>
        <td>3</td><td>Raj</td>
	</tr>
	<tr>
        <td>2</td><td>Barisal</td>
        <td>3</td><td>Raj</td>
	</tr>
	<tfoot>
        <tr><th colspan="6" align="center"> 
        <?php
	$params = $pdf->serializeTCPDFtagParameters(array('CODE 39', 'C39', '', '', 60, 20, 0.2, array('position'=>'S', 'border'=>false, 'padding'=>4, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'times', 'fontsize'=>11, 'stretchtext'=>3), 'N'));
	echo '<tcpdf method="write1DBarcode" params="'.$params.'" />';
		
		?>
        
        
        </th></tr>
	</tfoot>
    
</table>
<?php
	$html.=ob_get_contents();
	ob_clean();



$params = $pdf->serializeTCPDFtagParameters(array('CODE 39', 'C39', '', '', 60, 20, 0.2, array('position'=>'S', 'border'=>false, 'padding'=>4, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'times', 'fontsize'=>11, 'stretchtext'=>3), 'N'));
$html .= '<tcpdf method="write1DBarcode" params="'.$params.'" />';


/*$params = $pdf->serializeTCPDFtagParameters(array('CODE 128', 'C128', '', '', 80, 30, 0.4, array('position'=>'S', 'border'=>true, 'padding'=>4, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'helvetica', 'fontsize'=>8, 'stretchtext'=>4), 'N'));
$html .= '<tcpdf method="write1DBarcode" params="'.$params.'" />';


$html .= '<tcpdf method="AddPage" /><h2>Graphic Functions</h2>';

$params = $pdf->serializeTCPDFtagParameters(array(0));
$html .= '<tcpdf method="SetDrawColor" params="'.$params.'" />';

$params = $pdf->serializeTCPDFtagParameters(array(50, 50, 40, 10, 'DF', array(), array(0,128,255)));
$html .= '<tcpdf method="Rect" params="'.$params.'" />';
*/

// output the HTML content
$pdf->writeHTML($html, true, 0, true, 0);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_049.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Knitting Production Import
Functionality	:	
JS Functions	:
Created by		:	 
Creation date 	: 	27-12-2022
Updated by 		: 		
Update date		: 
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Import","../", 1, 1, $unicode,1,'');

//include 'excel_reader.php';       // include the class
//$excel = new Spreadsheet_Excel_Reader();     
// creates object instance of the class
/*$excel->read('Puma-ExcelUpload.xls');   // reads and stores the excel file data

// Test to see the excel data stored in $sheets property
echo '<table border="1" class="rpt_table">';
$x=1;
while($x<=$excel->sheets[0]['numRows']) { // reading row by row 
  echo "\t<tr>\n";
  $y=1;
  while($y<=$excel->sheets[0]['numCols']) {// reading column by column 
	$cell = isset($excel->sheets[0]['cells'][$x][$y]) ? $excel->sheets[0]['cells'][$x][$y] : '';
	echo "\t\t<td>$cell</td>\n";  // get each cells values
	$y++;
  }  
  echo "\t</tr>\n";
  $x++;
}
echo '</table>';*/
?>

</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
<!-- Important Field outside Form -->  
    <? echo load_freeze_divs ("../",$permission);  ?>
            <h3 style="width:510px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Excel File Browse (.xls Only)</h3> 
         	<div id="content_search_panel" style="width:500px" >
            <fieldset style="width:500px;">
                <form name="excelImport_1" id="excelImport_1" action="excel_knitting_production_import.php" enctype="multipart/form-data" method="post"> 
				<table width="500" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                <tr>
                	<td width="90"><b></b></td>
                    <td width="120"><input style="width:110px;" type="text" title="Double Click to Search" class="text_boxes" placeholder="" name="txt_job_no" id="txt_job_no" readonly disabled/> </td>
                    <td width="90" class="must_entry_caption"><b>Select File</b></td>
                    <td width="200">
                        <input type="file" id="uploadfile" name="uploadfile" class="image_uploader" required style="width:200px" />
                    </td>
                    <td>
                    	<input type="submit" name="submit" value="Upload" class="formbutton" style="width:60px" />
                    </td>
                </tr>
                </table>
                </form>
        </fieldset>
    </div>
	</div>
	</body>
	<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?


?>

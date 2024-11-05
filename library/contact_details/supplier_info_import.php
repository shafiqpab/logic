<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Supplier Import
Functionality	:	
JS Functions	:
Created by		:	Rakib 
Creation date 	: 	30-06-2021
Updated by 		: 		
Update date		: 
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Item Import","../../", 1, 1, $unicode,1,'');
?>
<script>
	
</script>

</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
<!-- Important Field outside Form -->  
    <? echo load_freeze_divs ("../../",$permission);  ?>
            <h3 style="width:670px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Excel File Browse (.xls Only)</h3> 
         	<div id="content_search_panel" style="width:670px" >
            <fieldset style="width:670px;">
                <form onSubmit="check_format();" name="excelImport_1" id="excelImport_1" action="supplier_info_import_excel.php" enctype="multipart/form-data" method="post" > 
				<table width="670" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                <tr class="general">
                    <td width="200" class="must_entry_caption"><b>Select File</b></td>
                    <td width="300" align="center"><input type="file" id="uploadfile" name="uploadfile" class="image_uploader" required style="width:250px" /></td>
                    <td><input type="submit" name="submit" value="Upload" class="formbutton" style="width:100px" /></td>
                </tr>
                </table>
                </form>
        </fieldset>
    </div>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
	
	</script>
</html>

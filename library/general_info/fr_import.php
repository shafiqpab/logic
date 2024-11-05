<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Fast React Import
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	06-01-2019	
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
echo load_html_head_contents("Fast React Import","../../", 1, 1, $unicode,1,'');

?>
<script>
	
</script>

</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
<!-- Important Field outside Form -->  
    <? echo load_freeze_divs ("../../",$permission);  ?>
            <h3 style="width:400px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Excel File Browse (.xls Only)</h3> 
         	<div id="content_search_panel" style="width:400px" >
            <fieldset style="width:400px;">
                <form name="excelImport_1" id="excelImport_1" action="fastreact_import.php" enctype="multipart/form-data" method="post"> 
				<table width="400" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                <tr>
                    <td width="90" class="must_entry_caption"><b>Select File</b></td>
                    <td width="200">
                        <input type="file" id="uploadfile" name="uploadfile" class="image_uploader" required style="width:200px" />
                    </td>
                    <td>
                    	<input type="submit" name="submit" value="Upload & Save" class="formbutton" style="width:100px" />
                    </td>
                </tr>
                </table>
                </form>
        </fieldset>
    </div>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

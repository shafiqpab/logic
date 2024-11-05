<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Item Creation
				
Functionality	:	
JS Functions	:
Created by		:	Rakib
Creation date 	: 	08-05-2022
Updated by 		:   Didar		
Update date		: 	04-07-2022	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
/*print_r($_SESSION['logic_erp']['mandatory_field'][420]);die;*/
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Yarn Stock Info", "../../", 1, 1,$unicode,'','');
$user_id=$_SESSION['logic_erp']['user_id'];
?> 
</head>
<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission); ?>
<div align="center">
    <h3 style="width:600px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Excel File Browse (.xls Only)</h3>
    <fieldset style="width:600px;">
        <form name="excelImport_1" id="excelImport_1" action="yarn_stock_import_excel.php" enctype="multipart/form-data" method="post">
            <table cellpadding="0" cellspacing="2" width="600" style="padding-left: 5px; padding-right: 5px;">
                <tr>
                    <td width="200" align="left"><input type="file" id="uploadfile" name="uploadfile" class="image_uploader" required style="width:200px" /></td>
                    <td width="200" align="left"><input type="submit" name="submit" value="Excel File Upload" class="formbutton" style="width:110px" /></td>                
                    <td width="200" align="right"><a href="../../excel_format/yarn_up_requirement.xls"><input type="button" value="Excel Format Download" name="excel" id="excel" class="formbutton" style="width:150px"/></a></td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

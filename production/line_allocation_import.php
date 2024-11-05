<?php
/*-------------------------------------------- Comments
Purpose			: 	This form for Next to Ex-Factory Entry
				
Functionality	:	
JS Functions	:
Created by		:	Md. Nuruzzaman 
Creation date 	: 	05.06.2016
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
echo load_html_head_contents("Line Allocation Import","../", 1, 1, $unicode,1,'');
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
		<?php echo load_freeze_divs ("../",$permission);  ?>
        <h3 style="width:350px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')">-Browse .csv file only</h3> 
        <div id="content_search_panel" style="width:840px" >
            <fieldset style="width:250px;">
                <form name="lineAllocationImport_1" id="lineAllocationImport_1" enctype="multipart/form-data" method="post"> 
                    <table width="350" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="90" class="must_entry_caption"><b>Select File</b></td>
                            <td width="260"><input type="file" id="uploadfile" name="uploadfile" class="image_uploader" style="width:260px" /></td>
                            <!--<td><input type="submit" name="submit" value="Upload" class="formbutton" style="width:60px" /></td>-->
                        </tr>
                        <tr>
                        	<td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="center" colspan="2" valign="middle" class="button_container">
                            	<input type="submit" name="submit" value="Save" class="formbutton" style="width:60px" />
                            </td>
                        </tr>
                    </table>
                </form>
            </fieldset>
        </div>
        <div id="data_conatainer"></div>
    </div>
</body>
<script>
	$('#lineAllocationImport_1').submit( function( e ){
		//alert("su..re"); return;
		e.preventDefault();
		$.ajax({
			url: 'requires/line_allocation_import_controller.php?action=action_save',
			type: 'POST',
			data: new FormData( this ),
			processData: false,
			contentType: false,
			success: function(data) 
			{
				//alert(data);
				var reponse=trim(data).split('**');
				show_msg(reponse[0]);
				//alert(reponse[1]);
				$('#data_conatainer').text(reponse[1]);
			}
		}); 
	});
</script>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
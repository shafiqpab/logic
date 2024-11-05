<?
/*-------------------------------------------- Comments 
Version          : 
Purpose			 : 
Functionality	 :	
JS Functions	 :
Created by		 : Monir Hossain
Creation date 	 :29/03/2016
Requirment Client: 
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
DB Script        : 
Updated by 		 : 
Update date		 : 
QC Performed BY	 :		
QC Date			 :	
Comments		 : 
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
//function load_html_head_contents($title, $path, $filter, $popup, $unicode, $multi_select, $am_chart)

echo load_html_head_contents("Sample Reports", "../", 1, 1,$unicode,'','');

?>	
<script>

	if ($('#index_page', window.parent.document).val() != 1)
	window.location.href = "../../logout.php";
	
	var permission = '<? echo $permission; ?>';
	
function  fnc_sample_issue(operation)
{
	if (form_validation('item_barcode','Scan') == false)
	{
		return;
	}
		var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('item_barcode*update_id', "../");
		//alert(data);
		freeze_window(operation);
		http.open("POST","requires/sample_report_controller.php", true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_sample_issue_response;
}

function fnc_sample_issue_response()
{
	if (http.readyState == 4)
	{
		//alert(http.responseText);
		var response = trim(http.responseText).split('**');
		//alert (reponse);
		show_msg(response[0]);
		release_freezing();
		if (response[0] == 0 || response[0] == 1 || response[0] == 2 )
		{
			//alert(reponse[0]);
			set_button_status(1, permission, 'fnc_sample_issue', 1);
			show_list_view('','sample_list_view','search_list_view','requires/sample_report_controller','setFilterGrid("list_view",-1)');
		}


	}
	
}


function sample_list()
{
	if (form_validation('item_barcode','Scan Barcode') == false)
	
	{
		return;
	}
	else
	{
		var barcodes=$("#item_barcode").val();
		show_list_view(barcodes,'sample_list_view','search_list_view','requires/sample_report_controller','setFilterGrid("list_view",-1)');	
	}
}


function sample_report_pop()
{
	//alert(receive_image_arr);
	  	//reset_form('sampleissue_1','','');
	var page_link = 'requires/sample_report_controller.php?action=barcode_list_view';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'sample List View', 'width=720px, height=280px, center=1, resize=0, scrolling=0', '');
        emailwindow.onclose = function()
        {
			
			var bar_code = this.contentDoc.getElementById("update_id").value;
			$('#item_barcode').val(bar_code);
			
			sample_list();
        }

}

$('#item_barcode').live('keydown', function(e) 
{
	if (e.keyCode === 13) {
	e.preventDefault();
	
	var bar=populate_data($('#item_barcode').val());
	//alert(populate_data($('#item_barcode').val()))
}

});


</script>
</head>
<body onLoad="set_hotkey();">
<div align="center">
<? echo load_freeze_divs ("../",$permission); ?>
<fieldset style="width:850px;">
    <form name="sampleissue_1" id="sampleissue_1">	

		<table cellpadding="0" cellspacing="1" width="320" align="center" id="sample_re">
		    <tr>
			    <td align="right" width="100" class="must_entry_caption">Barcode:</td>
			    <td width='100'>
			    <input type="text" name="item_barcode" id="item_barcode" class="text_boxes_numeric" style="width:200px" placeholder="Scan/Browse/Write" onDblClick="sample_report_pop()" maxlength="8" />	
			    <input type="hidden" name="update_id" id="update_id"  />					
			    </td>
                <td> <input type="button" class="formbutton" id="" value="Show" onClick="sample_list()"></td>
		    </tr>	
		</table>
	</form>
</fieldset>	

</div>

<div id="search_list_view" align="center"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
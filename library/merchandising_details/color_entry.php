<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Color List
Functionality	:
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	17-02-2014
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
echo load_html_head_contents("Color Information", "../../", 1, 1,$unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<?=$permission; ?>';
	var str_color = [<?=substr(return_library_autocomplete( "select color_name from lib_color order by color_name ASC", "color_name" ), 0, -1); ?>];
	
	function fnc_color_info( operation )
	{
		freeze_window(operation);
		var is_disable=$("#txt_color_name").attr('isused')*1;
		if (form_validation('txt_color_name*txt_tag_buyer','Color Name*Tag Buyer Name')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			var upneed=0;
			if(is_disable==1 && operation==1)
			{
				var r=confirm("Press \"OK\" to Update only Buyer Data.\n Else Press \"Cancel\".");
				if (r==true) upneed="1"; 
				else
				{
					upneed="0";
					var update_id = document.getElementById('update_id').value;
					var response=return_global_ajax_value( update_id, 'is_used_color', '', 'requires/color_entry_controller');
					if(response == 1){
						alert("Update or Delete Restricted! Because this color used in another table.");
						release_freezing();
						return;
					}
				}
			}
			else
			{
				if(operation!=0){
					var update_id = document.getElementById('update_id').value;
					var response=return_global_ajax_value( update_id, 'is_used_color', '', 'requires/color_entry_controller');
					if(response == 1){
						alert("Update or Delete Restricted! Because this color used in another table.");
						release_freezing();
						return;
					}
				}
			}
		
			var data="action=save_update_delete&operation="+operation+'&upneed='+upneed+get_submitted_data_string('txt_color_name*cbo_status*txt_tag_buyer*txt_tag_buyer_id*update_id',"../../");
			
			http.open("POST","requires/color_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_color_info_reponse;
		}
	}
	
	function fnc_color_info_reponse()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			//alert(reponse[0])
			show_msg(reponse[0]);
			
			if(reponse[0]==1 || reponse[0]==0)
			{
				$('#txt_color_name').removeAttr('disabled','disabled');
				$('#cbo_status').removeAttr('disabled','disabled');
				show_list_view('','color_list_view','color_list_view','requires/color_entry_controller','setFilterGrid("list_view",-1)');
				
				set_button_status(0, permission, 'fnc_color_info',1);
				
				reset_form('colorinfo_1','','');
				document.getElementById('usedmsg').innerHTML ='';
			}
			
			release_freezing();
		}
	}
	function openmypage_tag_buyer()
	{
		var is_disable=$("#txt_color_name").attr('isused')*1;
		//alert(is_diable);
		var txt_tag_buyer_id = $('#txt_tag_buyer_id').val();
		var title = 'Tag Buyer Selection Form';
		var page_link='requires/color_entry_controller.php?action=buyer_name_popup&txt_tag_buyer_id='+txt_tag_buyer_id+'&is_disable='+is_disable;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var buyer_id=this.contentDoc.getElementById("hidden_buyer_id").value;	 //Access form field with id="emailfield"
			var buyer_name=this.contentDoc.getElementById("hidden_buyer_name").value;
			$('#txt_tag_buyer_id').val(buyer_id);
			$('#txt_tag_buyer').val(buyer_name);
		}
	}
	function refresh(){
	$("#txt_color_name").removeAttr('disabled');
	$("#cbo_status").removeAttr('disabled');
// alert("ok");
}
</script>
</head>
<body  onload="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:100%;">
    <fieldset style="width:500px;">
        <legend>Color Info</legend>
        <form name="colorinfo_1" id="colorinfo_1"  autocomplete="off">
            <table cellpadding="0" cellspacing="2" width="500px">
                <tr>
                    <td width="80" class="must_entry_caption">Color Name</td>
                    <td width="150"><input type="text" name="txt_color_name" id="txt_color_name" class="text_boxes" style="width:140px" isused="0" /></td>
                    <td width="50">Status</td>
                    <td><?=create_drop_down( "cbo_status", 140, $row_status,'', $is_select, $select_text, 1, $onchange_func, '','','','',3 ); ?></td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Tag Buyer Name</td>
                    <td>
                        <input type="text" name="txt_tag_buyer" id="txt_tag_buyer" class="text_boxes" style="width:140px;" placeholder="Click To Search" onClick="openmypage_tag_buyer();" readonly />
                        <input type="hidden" name="txt_tag_buyer_id" id="txt_tag_buyer_id" value="" />
                    </td>
                    <td colspan="2" id="usedmsg" style="font-size:12px; color:#F00"></td>
                </tr>
                <tr>
                    <td colspan="4" align="center" class="button_container" onclick="refresh()">
						<?=load_submit_buttons( $permission, "fnc_color_info", 0,0 ,"reset_form('colorinfo_1','','')",1); ?>
                        <input type="hidden" name="update_id" id="update_id" >
                    </td>
                </tr>
                <tr>
                    <td colspan="4" id="color_list_view">
						<?
                        $arr=array (1=>$row_status);
                        echo create_list_view ( "list_view","Color Name,Status", "250,200","500","220",0, "select color_name, status_active, id from lib_color where is_deleted=0 order by color_name", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,status_active", $arr , "color_name,status_active", "requires/color_entry_controller", 'setFilterGrid("list_view",-1);' ) ;//\\192.168.11.252\logic_erp_3rd_version\prod_planning\cutting_plan\requires
                        ?>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
	</div>
</div>
</body>
<script>
	/*set_multiselect('cbo_tag_buyer','0','0','','0');

	$( "#txt_color_name" ).autocomplete({
		 source: function( request, response ) {
			  var matcher =  new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
			  response( $.grep( str_color, function( item ){
				  return matcher.test( item );
			  }) );
		  }
	});*/

	/*$("#txt_color_name").autocomplete({
					source: str_color
				});
				 */


</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>

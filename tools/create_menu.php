<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("User Creation","../", 1, 1, $unicode,'','');
 
?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';
	
function fnc_menu_create( operation )
{
	if (form_validation('cbo_module_name*txt_menu_name*txt_menu_seq*cbo_menu_sts','Module Name*Menu Name*Menu Sequence*Menu Status')==false)
	{
		return;
	}
	
	else
	{
		if( $("#chk_report_menu").attr("checked") ) var chk_report_menu=1; else var chk_report_menu=0;
		if( $("#chk_mobile_menu").attr("checked") ){
			if (form_validation('txt_mobile_menu_link','Module Menu Link')==false)
			{
				return;
			}
			var chk_mobile_menu=1;
		}
		else{
			var chk_mobile_menu=0;
		}
		
  		eval(get_submitted_variables('cbo_module_name*txt_menu_name*txt_menu_link*cbo_root_menu*cbo_root_menu_under*txt_menu_seq*cbo_menu_sts*txt_page_link*txt_short_name*update_id'));
		
		var data="action=save_update_delete&chk_report_menu="+chk_report_menu+"&chk_mobile_menu="+chk_mobile_menu+"&operation="+operation+get_submitted_data_string('cbo_module_name*cbo_root_menu*txt_menu_name*txt_menu_link*txt_mobile_menu_link*cbo_root_menu_under*txt_menu_seq*txt_page_link*txt_short_name*cbo_menu_sts*cbo_fabric_nature*update_id',"../");
		freeze_window(operation);
		http.open("POST","requires/menu_create_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_on_submit_reponse;
	}
}

function fnc_on_submit_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		set_button_status(0, permission, 'fnc_menu_create',1);
		release_freezing();
	}
}	 
</script>
<body onLoad="set_hotkey()">
<?   echo load_freeze_divs ("../", $permission); ?>
<div align="center" style="width:800px;">
		<fieldset style="width:500px">
			<legend> Menu Creation</legend>
			<form name="menucreate_1" id="menucreate_1" autocomplete="off">
				<table>
					<tr>
						<td>Main Module Name</td>
						<td><? echo create_drop_down( "cbo_module_name", 150, "select m_mod_id, main_module from main_module where status=1 order by main_module","m_mod_id,main_module", 1, "-- Select Module --", '0', "load_drop_down( 'requires/menu_create_controller', this.value, 'cbo_root_menu', 'root_menu_div' )" ); ?>
                        	Approval Menu <input type="checkbox" id="chk_report_menu" name="chk_report_menu" >
						</td>
					</tr>
					<tr>
						<td>Menu Name</td>
						<td><input type="text" name="txt_menu_name" id="txt_menu_name" class="text_boxes" style="width:238px" /></td>
					</tr>
					<tr>
						<td>Menu Link</td>
						<td><input type="text" name="txt_menu_link" id="txt_menu_link" class="text_boxes" style="width:238px" /></td>
					</tr>
					<tr>
						<td>Mobile Menu Link</td>
						<td><input type="text" name="txt_mobile_menu_link" id="txt_mobile_menu_link" class="text_boxes" style="width:238px" /></td>
					</tr>
					<tr>
						<td>Root Menu</td>
						<td id="root_menu_div"><? echo create_drop_down( "cbo_root_menu", 250, "select m_menu_id,menu_name from main_menu where position='1' and status_active=1 and is_deleted=0 order by menu_name","m_menu_id,menu_name", 1, "-- Select Menu Name --", $selected, "load_drop_down( 'requires/menu_create_controller', this.value, 'cbo_root_menu_under', 'subrootdiv' )" ); ?>
						</td>
					</tr>
					<tr>
						<td>Root Menu Under</td>
						<td id="subrootdiv"><? echo create_drop_down( "cbo_root_menu_under", 250, "select m_menu_id,menu_name from main_menu where position='2' order by menu_name","m_menu_id,menu_name", 1, "-- Select Menu Name --", $selected, "" ); ?></td>
					</tr>
                    <tr>
						<td>Product Nature</td>
						<td id="subrootdiv"><? echo create_drop_down( "cbo_fabric_nature", 250, $item_category,"", 1,"--All Fabrics--",$selected, "","","2,3,100" ); ?></td>
					</tr>
					<tr>
						<td>Sequence</td>
						<td>
							<div style="float:left" id="menu_seq_menu_create">
                                <div style="float:left"><input type="text" name="txt_menu_seq" id="txt_menu_seq" class="text_boxes_numeric" style="width:80px" /></div>
                                <div style="float:left">&nbsp;&nbsp;&nbsp;Status&nbsp;<? echo create_drop_down( "cbo_menu_sts", 97, $row_status,'', '', '', 1 ); ?></div>
							</div>
						</td>
					</tr>
					<tr>
						<td>Mobile Menu</td>
						<td><input type="checkbox" id="chk_mobile_menu" name="chk_mobile_menu" ></td>
					</tr>
					<tr>
						<td>Page Link</td>
						<td><input type="text" name="txt_page_link" id="txt_page_link" class="text_boxes" style="width:238px" /></td>
					</tr>
					<tr>
						<td>Page Short Name</td>
						<td><input type="text" name="txt_short_name" id="txt_short_name" class="text_boxes" style="width:238px" /></td>
					</tr>
					<tr>
						<td align="left" style="padding-top:10px;" colspan="2">
                      		<input type="hidden" name="update_id" id="update_id" />
							<? echo load_submit_buttons( $permission, "fnc_menu_create", 0,0 ,"reset_form('menucreate_1','','',1)"); ?> 
						</td>
					</tr>
                     <tr>
						<td align="center" style="padding-top:10px;" colspan="2" >
                      		 <input type="text" size="30" class="text_boxes" onKeyUp="show_list_view (this.value+'_'+document.getElementById('cbo_module_name').value, 'create_menu_search_list_view', 'search_div', 'requires/menu_create_controller', 'setFilterGrid(\'list_view\',-1)')" id="txt_search_item" style="width:200px;" />
						</td>
					</tr>
				</table>
			</form>
		</fieldset>
        <div align="center" style="padding-top:10px;" id="search_div"></div>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
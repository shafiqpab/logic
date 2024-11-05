<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Division under Buyer

Functionality	:


JS Functions	:

Created by		:	Md. Helal Uddin
Creation date 	: 	23-01-2021
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
echo load_html_head_contents("Division Information", "../../", 1, 1,$unicode,1,'');
?>
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';




function fnc_color_info( operation )
{
	if(operation!=0){
		var update_id = document.getElementById('update_id').value;
		var response=return_global_ajax_value( update_id, 'is_used_department', '', 'requires/department_entry_controller');
		if(response == 1){
			alert("Update or Delete restricted ! Because this Department used in style ref entry.");
			return;
		}
	}
	if (form_validation('txt_department_name*cbo_buyer_name*cbo_division_name','Division Name*Buyer Name*Division Name')==false)
	{
		return;
	}
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_department_name*cbo_buyer_name*cbo_division_name*update_id',"../../");
		freeze_window(operation);
		http.open("POST","requires/department_entry_controller.php",true);
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
		show_list_view('','division_list_view','division_list_view','../merchandising_details/requires/department_entry_controller','setFilterGrid("list_view",-1)');
		reset_form('divisioninfo_1','','');
		if(reponse[0]==1 || reponse[0]==0)
		{
			$('#txt_department_name').removeAttr('disabled','disabled');
			$('#cbo_buyer_name').removeAttr('disabled','disabled');
			$('#cbo_department_name').removeAttr('disabled','disabled');
		}
		set_button_status(0, permission, 'fnc_color_info',1);
		release_freezing();
	}
}


</script>
</head>
<body  onload="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center" style="width:100%;">

	<fieldset style="width:500px;">
		<legend>Color Info</legend>
		<form name="divisioninfo_1" id="divisioninfo_1"  autocomplete="off">
			<table cellpadding="0" cellspacing="2" width="550px">
			 	
				<tr>

                    <td width="100" class="must_entry_caption">Buyer Name</td>
					<td >
						 <?
						 echo create_drop_down( "cbo_buyer_name", 150, "SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-Select-", '', "load_drop_down( 'requires/department_entry_controller',this.value, 'load_drop_down_division', 'division_td' );", '','','','',3 );
						 ?>
					</td>
					<td width="100" class="must_entry_caption">Division</td>
					<td id="division_td" >
						 <?
						 echo create_drop_down( "cbo_division_name", 150, $blank_array,"", 1, "-Select-", 1, $onchange_func, '','','','',3 );
						 ?>
					</td>
                  </tr>
                  <tr >
					<td class="must_entry_caption">Department Name</td>
					<td colspan="3" >
						<input type="text" name="txt_department_name" id="txt_department_name" class="text_boxes" style="width:415px" />
					</td>
				</tr>
                  
                  <tr>
					<td colspan="4" align="center" class="button_container">
						<?
					     echo load_submit_buttons( $permission, "fnc_color_info", 0,0 ,"reset_form('divisioninfo_1','','')",1);
				        ?>
                        <input type="hidden" name="update_id" id="update_id" >
					</td>
				</tr>
                <tr>
						<td colspan="4" id="division_list_view">
							<?
							$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
							$division_arr=return_library_array( "select id,division_name from lib_division_name",'id','division_name');
							$arr=array (1=>$buyer_arr,2=>$division_arr);
							echo  create_list_view ( "list_view", "Department Name,Buyer,Division", "170,150,150","550","220",0, "select  department_name,buyer_id,id,division_id from  lib_department_name where is_deleted=0 order by department_name", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,buyer_id,division_id", $arr , "department_name,buyer_id,division_id", "../merchandising_details/requires/department_entry_controller", 'setFilterGrid("list_view",-1);' ) ;
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
	
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>

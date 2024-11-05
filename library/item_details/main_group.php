<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Item Sub Group Info
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	11.02.2021
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
echo load_html_head_contents("Item Sub-Group Info", "../../", 1, 1,$unicode,1,1);
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';
			
	function fnc_main_group( operation )
	{
		if(operation==2)
		{
			alert("Delete Not Allow");return;
		}
		
		if (form_validation('cbo_item_category*txt_main_group*cbo_user','Item Category*Main Group*User Id Tag')==false)
		{
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_item_category*txt_main_group*txt_item_group_id*cbo_user*cbo_status*hidden_main_group_id',"../../");
			 
		freeze_window(operation);
		http.open("POST","requires/main_group_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_main_group_reponse;
	}
	
	function fnc_main_group_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);
			show_list_view(reponse[1],'main_group_list_view','main_group_list','../item_details/requires/main_group_controller','setFilterGrid("list_view",-1)');
			reset_form('itemgroup_1','','','','','');
			set_button_status(0, permission, 'fnc_main_group',1);
			release_freezing();
		}
	}
	
	function fn_list_show(item_id)
	{
		//alert(item_id);return;
		//if(item_id>0)
		//{
			item_id=0+"**"+item_id;
			show_list_view(item_id,'main_group_list_view','main_group_list','../item_details/requires/main_group_controller','setFilterGrid("list_view",-1)');
		//}
	}


	function openmypage_item_group()
	{
		if (form_validation('cbo_item_category','Item Category')==false)
		{
			return;
		}

		//var is_disable=$("#txt_main_group").attr('isused')*1;
		var txt_item_group_id = $('#txt_item_group_id').val();
		var cbo_item_category = $('#cbo_item_category').val();
		var title = 'Tag Item Group Selection Form';
		var page_link='requires/main_group_controller.php?action=item_group_popup&txt_item_group_id='+txt_item_group_id+'&cbo_item_category='+cbo_item_category;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var item_group_id=this.contentDoc.getElementById("hidden_item_group_id").value;	 //Access form field with id="emailfield"
			var item_group_name=this.contentDoc.getElementById("hidden_item_group_name").value;
			$('#txt_item_group_id').val(item_group_id);
			$('#txt_item_group').val(item_group_name);
		}
	}

</script>
</head>
<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center">
	<fieldset style="width:850px;">
		<legend>Main Group Info</legend>
		<form name="itemgroup_1" id="itemgroup_1">	
			<table cellpadding="0" cellspacing="2" >
				<tr>
					<td colspan="2" valign="top">
						<table  cellpadding="0" cellspacing="2" width="100%">
			 				<tr>
								<td width="100" class="must_entry_caption">Item Category</td>
								<td width="170"><? echo create_drop_down( "cbo_item_category", 155,$item_category,"", '1', '---- Select ----', 0, "fn_list_show(this.value)","","","","","1,2,3,12,13,14,24,25"); ?></td>

								<td width="100" class="must_entry_caption">Main Group</td>
								<td width="170">
                    				<input name="txt_main_group" id="txt_main_group" style="width:145px" value="" class="text_boxes" placeholder="Write" />
                    				<input type="hidden" name="hidden_main_group_id" id="hidden_main_group_id" value="" />
                                </td>
                                <td width="100">Item Group Tag</td>
                                <td>
                                	<input type="text" name="txt_item_group" id="txt_item_group" class="text_boxes" style="width:145px;" placeholder="Click To Search" onClick="openmypage_item_group();" readonly />
                        			<input type="hidden" name="txt_item_group_id" id="txt_item_group_id" value="" />
                        		</td>	
							</tr>	 
							<tr>   
							    <td  class="must_entry_caption">User ID Tag</td>
                                <td><? echo create_drop_down( "cbo_user", 155, "select id,user_name from  user_passwd where valid=1 order by user_name","id,user_name", 0,"-- Select --", $selected, "",0,"" ); ?></td>
                                <td>Status</td>
								<td><? echo create_drop_down( "cbo_status", 155, $row_status,"", "", "", 1, "" ); ?></td>	
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
							</tr> 
			    		</table>
					</td>
			  	</tr>
				<tr>
				  <td colspan="4" align="center" class="button_container">
						<? 
						//$dd="disable_enable_fields( 'txt_group_code',0)";
						echo load_submit_buttons( $permission, "fnc_main_group", 0,0 ,"reset_form('itemgroup_1','','','','')",1);
						?>
					</td>				
				</tr>
			</table>
		</form>	
	</fieldset>	
	<div style="width:100%; float:left; margin:auto" align="center">
		<fieldset style="width:900px; margin-top:20px">
			<legend>List View</legend>
			<div style="width:900px; margin-top:10px" id="main_group_list" align="left">
			<?
				
				if ($category!=0) $item_category_list=" and item_category_id='$category'";
				$itemGroupArr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');
				$userArr=return_library_array( "select id,user_name from user_passwd",'id','user_name');
				
				$sql="select id,main_group_name,item_category_id,item_group_id,user_id,status_active from lib_main_group where is_deleted=0 $item_category_list order by id desc";
				$sql_res=sql_select($sql);
				$item_group_arr=array();
				$user_arr=array();
				foreach($sql_res as $row)
				{
					$item_group_ids=explode(',',$row[csf('item_group_id')]);
					$item_groups='';
					foreach ($item_group_ids as  $id) {
						$item_groups.=$itemGroupArr[$id].', ';
					}
					$item_group_arr[$row[csf('id')]]= rtrim($item_groups,', ');

					$user_ids=explode(',',$row[csf('user_id')]);
					$users='';
					foreach ($user_ids as  $id) {
						$users .= $userArr[$id].', ';
					}
					$user_arr[$row[csf('id')]]= rtrim($users,', ');
				}
				$arr=array (1=>$item_category,2=>$item_group_arr,3=>$user_arr,4=>$row_status);
				echo create_list_view ( "list_view", "Main Group,Item Catagory,Item Group,User,Status", "150,150,200,200,100","890","320",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,item_category_id,id,id,status_active", $arr , "main_group_name,item_category_id,id,id,status_active", "../item_details/requires/main_group_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0' );
			?>
			</div>
		</fieldset>	
	</div>
</div>
</body>
<script>
	set_multiselect('cbo_user','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

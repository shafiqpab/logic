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
echo load_html_head_contents("Item Sub-Group Info", "../../", 1, 1,$unicode,'','');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';
	
	function openmypage()
	{
		
		if ( form_validation('cbo_item_category','Item Category')==false )
		{
			return;
		}
		else
		{
			var category=document.getElementById('cbo_item_category').value;
			alert(category);
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/item_sub_group_controller.php?category='+category+'&action=order_popup','Search Group Code', 'width=950px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("item_id").value.split("_");
				$('#item_group_id').val(theemail[0]);
				$('#txt_item_group').val(theemail[1]+"-"+theemail[2]);
			}
		}
	}
			
	function fnc_item_group( operation )
	{
		if(operation==2)
		{
			alert("Delete Not Allow");return;
		}
		
		if (form_validation('cbo_item_category*txt_item_group*txt_subgroup_name','Item Category*Item Group*Sub Group')==false)
		{
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_item_category*txt_item_group*item_group_id*txt_subgroup_name*txt_subgroup_code*cbo_status*item_sub_group_id',"../../");
			 
		freeze_window(operation);
		http.open("POST","requires/item_sub_group_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_item_group_reponse;
	}
	
	function fnc_item_group_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);
			show_list_view(reponse[1],'item_group_list_view','item_group_list_view','../item_details/requires/item_sub_group_controller','setFilterGrid("list_view",-1)');
			reset_form('itemgroup_1','','','','','');
			set_button_status(0, permission, 'fnc_item_group',1);
			release_freezing();
		}
	}
	
	function fn_list_show(item_id)
	{
		//alert(item_id);return;
		if(item_id>0)
		{
			show_list_view(item_id,'item_group_list_view','item_group_list_view','requires/item_sub_group_controller','setFilterGrid("list_view",-1)');
		}
	}
	
	function set_con_factor_value()
	{
		var order_uom=document.getElementById('cbo_order_uom').value;
		var cons_uom=document.getElementById('cbo_cons_uom').value;
		if( cons_uom*1 == order_uom*1)
		{
			document.getElementById('txt_conversion_factor').value=1;
		}
		else
		{
			document.getElementById('txt_conversion_factor').value="";
		}
	}
	
	function item_category_add(id,type)
	{		
		get_php_form_data(id,type,'requires/item_sub_group_controller');
	}

	function trim_fancy_disable(category)
	{
		if (category==4)
		{
			$('#cbo_trim_type').val('');
			//$('#cbo_trim_type').attr('disabled','disabled');
			$('#cbo_fancy_item').val('2');
			//$('#cbo_fancy_item').attr('disabled','disabled');
		}
		else
		{
			$('#cbo_trim_type').removeAttr('disabled','disabled');
			$('#cbo_fancy_item').removeAttr('disabled','disabled');
		}
	}
	
	function open_rate_popup(id)
	{ 
		if(id=="")
		{
		 	alert("Save Data First");
			return;
		}
		var  cbo_item_category= document.getElementById('cbo_item_category').value;
		
		var page_link="requires/item_sub_group_controller.php?action=open_rate_popup_view&mst_id="+trim(id)+"&cbo_item_category="+trim(cbo_item_category);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Rate Pop Up", 'width=400px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}		
	}

</script>
</head>
<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center">
	<fieldset style="width:850px;">
		<legend>Item Group Info</legend>
		<form name="itemgroup_1" id="itemgroup_1">	
			<table cellpadding="0" cellspacing="2" >
				<tr>
					<td colspan="2" valign="top">
						<table  cellpadding="0" cellspacing="2" width="100%">
			 				<tr>
								<td width="140" class="must_entry_caption">Item Category</td>
								<td width="170"><? echo create_drop_down( "cbo_item_category", 155,$item_category,"", '1', '---- Select ----', 0, "fn_list_show(this.value)","","","","","1,2,3,12,13,14,24,25"); ?></td>
								<td width="130" class="must_entry_caption">Item Group</td>
								<td id="group_code" width="170">
                                <input type="hidden" id="item_group_id" name="item_group_id" />
                    			<Input name="txt_item_group" id="txt_item_group"   style="width:145px" value="" class="text_boxes" autocomplete="off" maxlength="50" title="Maximum 50" placeholder="Double Click to Search" onDblClick="openmypage()"  readonly />
                                </td>
								<td width="130">Status</td>
								<td><? echo create_drop_down( "cbo_status", 155, $row_status,"", "", "", 1, "" ); ?></td>								
							</tr>	 
							<tr>
								<td>Sub Group Code</td>
                                <td><Input name="txt_subgroup_code" id="txt_subgroup_code" style="width:145px" class="text_boxes" autocomplete="off"  maxlength="50" title="Maximum 50 Character" ></td>
                                <td class="must_entry_caption">Sub Group Name</td>
                                <td><Input name="txt_subgroup_name" id="txt_subgroup_name"  style="width:145px" class="text_boxes" autocomplete="off"  maxlength="50" title="Maximum 50 Character"></td>
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
						echo load_submit_buttons( $permission, "fnc_item_group", 0,0 ,"reset_form('itemgroup_1','','','','')",1);
						?>
                        <input type="hidden" id="item_sub_group_id" name="item_sub_group_id" />	
					</td>				
				</tr>
			</table>
		</form>	
	</fieldset>	
	<div style="width:100%; float:left; margin:auto" align="center">
		<fieldset style="width:900px; margin-top:20px">
			<legend>List View</legend>
			<div style="width:900px; margin-top:10px" id="item_group_list_view" align="left">
			<?
				
				$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
				$arr=array (0=>$item_category,1=>$item_group_arr,4=>$row_status);
				if ($category!=0) $item_category_list=" and item_category_id='$category'";
				$sql="select id,item_category_id,item_group_id,sub_group_code,sub_group_name,status_active from lib_item_sub_group where is_deleted=0 $item_category_list";
				echo  create_list_view ( "list_view", "Item Catagory,Item Group,Sub Group Code,Sub Group Name,Status", "200,200,100,200","890","320",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'", 1, "item_category_id,item_group_id,0,0,status_active", $arr , "item_category_id,item_group_id,sub_group_code,sub_group_name,status_active", "../item_details/requires/item_sub_group_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0' ) ;
				
			?>
			</div>
		</fieldset>	
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Item Creation
				
Functionality	:	
JS Functions	:
Created by		:	CTO/sohel 
Creation date 	: 	08-04-2013
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

echo load_html_head_contents("Item Creation Info", "../../", 1, 1,$unicode,'','');
?> 
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';
	
	//var company_id=document.getElementById('cbo_company_name').value;
	//var item_category_id=document.getElementById('cbo_item_category').value;
	//var item_group_id=document.getElementById('item_group_id').value;
	var str_group = [<? echo substr(return_library_autocomplete( "select sub_group_code from product_details_master  group by sub_group_code", "sub_group_code" ), 0, -1); ?>];

	$(document).ready(function(e){
            $("#txt_subgroup_code").autocomplete({
			 source: str_group
		  });
     });

	var str_group_name = [<? echo substr(return_library_autocomplete( "select sub_group_name from product_details_master  group by sub_group_name", "sub_group_name" ), 0, -1); ?>];

	$(document).ready(function(e){
            $("#txt_item_code").autocomplete({
			 source: str_group_name
		  });
     });


	var str_item_code = [<? echo substr(return_library_autocomplete( "select item_code from product_details_master  group by item_code", "item_code" ), 0, -1); ?>];

	$(document).ready(function(e){
            $("#txt_subgroup_name").autocomplete({
			 source: str_item_code
		  });
     });

  /* var str_description = [<? //echo substr(return_library_autocomplete( "select item_description from product_details_master  group by item_description", "item_description" ), 0, -1); ?>];

	$(document).ready(function(e){
            $("#txt_description").autocomplete({
			 source: str_description
		  });
     });*/


     var str_item_size = [<? echo substr(return_library_autocomplete( "select item_size from product_details_master  group by item_size", "item_size" ), 0, -1); ?>];

	$(document).ready(function(e){
            $("#txt_item_size").autocomplete({
			 source: str_item_size
		  });
     });

	function openmypage()
	{
		if ( form_validation('cbo_company_name*cbo_item_category','Company Name*Item Category')==false )
		{
			return;
		}
		else
		{
			var category=document.getElementById('cbo_item_category').value;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/item_creation_controller.php?category='+category+'&action=order_popup','Search Group Code', 'width=950px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("item_id");
				document.getElementById('item_group_id').value = theemail.value;
				get_php_form_data(theemail.value, "load_php_popup_to_form", "requires/item_creation_controller" );
				set_button_status(0, permission, 'fnc_item_creation',1);
				release_freezing();
			}
		}
	}
			
	function fnc_item_creation( operation )
	{
		/*if(operation==2)
		{
			show_msg('13');
			return;
		}*/
		if (form_validation('cbo_company_name*cbo_item_category*txt_item_group','Company*Item Catagory*Grop Code')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_item_category*txt_item_group*item_group_id*txt_subgroup_code*txt_subgroup_name*txt_item_code*txt_description*txt_item_size*txt_reorder_label*txt_min_label*txt_max_label*cbo_cons_uom*txt_item_account*cbo_status*update_id*update_status_active',"../../");
			
			freeze_window(operation);
			http.open("POST","requires/item_creation_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_item_creation_reponse;
		}
	}

	function fnc_item_creation_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			
			if(reponse[0]==50)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			if(reponse[0]==101 || reponse[0]==102)
			 {
				 alert(" You can not cancell or InActive This Item.This Information is used in another Table");
				 document.getElementById('cbo_status').value =1;
				 release_freezing();
			 }
	  else
	      {
			document.getElementById('update_id').value =reponse[1];
			show_msg(reponse[0]);
			show_list_view(reponse[2],'item_creation_list_view','item_creation_list_view','../item_details/requires/item_creation_controller','setFilterGrid("list_view",-1)');
			//reset_form('itemcreation_1','','');
			reset_form('','','txt_subgroup_code*txt_subgroup_name*txt_item_code*txt_description*txt_item_size*txt_reorder_label*txt_min_label*txt_max_label*txt_item_account*cbo_status','','');
			$('#txt_subgroup_name').focus();
			//$('#'+txt_subgroup_name).focus();
			disable_enable_fields('cbo_company_name*cbo_item_category*txt_item_group*txt_subgroup_code*txt_item_code*txt_item_account');
			set_button_status(0, permission, 'fnc_item_creation',1);
			release_freezing();
		 }
		}
	}

	function fnc_set_namecode(val)
	{
		var item_category=document.getElementById('cbo_item_category').value;
		var item_group=document.getElementById('txt_item_group').value.split('-');
		var subgroup_code=document.getElementById('txt_subgroup_code').value;
		var item_code=item_category+'-'+item_group[0]+'-'+subgroup_code+'-'+val;
		document.getElementById('txt_item_account').value = item_code;
	}
	
	function fnc_item_category_add(category,type)
	{
		get_php_form_data(category,type,'requires/item_creation_controller');
	}
	
	
	function check_save_data(size_val)
	{
		var list_view_orders=return_global_ajax_value($('#cbo_company_name').val()+"**"+$('#cbo_item_category').val()+"**"+$('#item_group_id').val()+"**"+$('#txt_subgroup_code').val()+"**"+$('#txt_subgroup_name').val()+"**"+$('#txt_item_code').val()+"**"+$('#txt_description').val()+"**"+size_val, "load_check_value_to_form",'', "requires/item_creation_controller" );
		if(list_view_orders==12)
		{
			alert ("Duplicate Value Found.");
			return;
		}
/*		else if(list_view_orders==12)
		{
			//alert ("Duplicate Value Found.");
			//return;
		}*/
	}

	function fn_list_show(item_id)
	{
		show_list_view(item_id,'item_creation_list_view','item_creation_list_view','requires/item_creation_controller','setFilterGrid("list_view",-1)');
	}
</script>
</head>
<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission); ?>
<div align="center">
	<fieldset style="width:900px;">
		<legend>Item Creation Info</legend>
		<form name="itemcreation_1" id="itemcreation_1">	
        <p id="demo_message"></p>
		<table cellpadding="1" cellspacing="1" width="100%" >
            <tr>
                <td width="140" class="must_entry_caption">Company</td>
                <td width="170"> <input type="hidden" name="txt_hidden_item_group" id="txt_hidden_item_group" value="" ><input type="hidden" name="update_id" id="update_id" > <input type="hidden" name="update_status_active" id="update_status_active" > 
					<? 
						echo create_drop_down( "cbo_company_name", 155, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 $company_cond  order by company_name",'id,company_name', 1, '--- Select Company ---', 0, "" );
                    ?> 
                </td>
                <td width="130" class="must_entry_caption">Item Category</td>
                <td width="170"><input type="hidden" name="set_id" id="set_id" >                                 
					<?
						echo create_drop_down( "cbo_item_category", 155, $item_category,"", "1", "--- Select---", 0, "fnc_item_category_add(this.value,'category_add');fn_list_show(this.value);","","","","","1,2,3,12,13,14,24,25" );
						//fnc_item_category_add(this.value,category_add);
                    ?>
                </td>
                <td width="130" class="must_entry_caption">Item Group</td>
                <td width="190"><input type="hidden" id="item_group_id" />
                    <Input name="txt_item_group" ID="txt_item_group"   style="width:145px" value="" class="text_boxes" autocomplete="off" maxlength="50" title="Maximum 50" placeholder="Double Click to Search" onKeyUp="if (this.value!='') get_php_form_data(this.value+'_blur'+document.getElementById('cbo_item_category').value, 'load_php_popup_to_form', 'requires/item_creation_controller')" onDblClick="openmypage()"  readonly />
                </td>
            </tr>
            <tr>
                <td>Sub Group Code</td>
                <td><Input name="txt_subgroup_code" ID="txt_subgroup_code" style="width:145px" class="text_boxes" autocomplete="off"  maxlength="50" title="Maximum 50 Character"></td>
                <td>Sub Group Name</td>
                <td><Input name="txt_subgroup_name" ID="txt_subgroup_name"  style="width:145px" class="text_boxes" autocomplete="off"  maxlength="50" title="Maximum 50 Character"></td>
                <td>Item Code</td>
                <td><Input name="txt_item_code" ID="txt_item_code"  onBlur="fnc_set_namecode(this.value)" style="width:145px"  class="text_boxes" autocomplete="off" /></td>
            </tr> 
            <tr>
                <td>Item Description</td>
                <td><Input name="txt_description" ID="txt_description"  style="width:145px"  class="text_boxes" autocomplete="off"  maxlength="250" title="Maximum 250 Character"></td>
                <td>Item Size</td>
                <td><Input name="txt_item_size" ID="txt_item_size"  style="width:145px" class="text_boxes" autocomplete="off" onBlur="check_save_data(this.value);"></td>
                <td>Re-Order Label</td>
                <td><Input name="txt_reorder_label" ID="txt_reorder_label"  style="width:145px" class="text_boxes_numeric" autocomplete="off"/></td>
            </tr>
            <tr>
                <td>Min Label</td>
                <td><Input name="txt_min_label" ID="txt_min_label"  style="width:145px" class="text_boxes_numeric" autocomplete="off" /></td>
                <td>Max Level</td>
                <td><Input name="txt_max_label" ID="txt_max_label"  style="width:145px" class="text_boxes_numeric" autocomplete="off" /></td>
                <td>Order UOM </td>
                <td>
					<?
						echo create_drop_down( "cbo_order_uom", 155, $unit_of_measurement,"", "1", "--- Select---", 0, "","1" );
                    ?>
                </td>
            </tr>
            <tr>
                <td >Cons UOM</td>
                <td id="cons_td"> 
					<?
						echo create_drop_down( "cbo_cons_uom", 155, $unit_of_measurement,"", "1", "--- Select---", 0, "","1" );
                    ?>
                </td>
                <td>Item Account</td>
                <td>
                    <Input name="txt_item_account" ID="txt_item_account" style="width:145px" class="text_boxes" readonly   maxlength="50" title="Maximum 50 Character">                </td>
                <td>Status </td>
                <td>
					<?
						echo create_drop_down( "cbo_status", 155, $row_status,"", "", "", 1, "" );
                    ?>
                </td>		
            </tr>
            <tr>
                <td colspan="4" align="center">&nbsp;						
                    <input type="hidden" name="hide_item_code" id="hide_item_code">
                </td>					
            </tr>
		</table>
        <table>
            <tr>
                <td width="670"></td>
                <td width="157"></td>
            </tr>
            <tr><td colspan="6"></td></tr>
            <tr>
                <td colspan="4" align="center" class="button_container">
					<? 
                        $dd="disable_enable_fields('cbo_company_name*cbo_item_category*txt_item_group*txt_subgroup_code*txt_item_code*txt_item_account',0)";
                        echo load_submit_buttons( $permission, "fnc_item_creation", 0,0 ,"reset_form('itemcreation_1','','','',$dd)",1);//itemcreation_1
                    ?>	
                </td>				
            </tr>
        </table>
		</form>	
	</fieldset>	
    <fieldset style="width:1080px;">
        <legend>Item Creation List View</legend>
            <div style="width:1080px;"id="item_creation_list_view"></div>
    </fieldset>	
</div>
</body>
<script>
	/*$(document).ready(function() {
		setFilterGrid("list_view",-1);
		//show_list_view('','item_creation_list_view','item_creation_list_view','requires/item_creation_controller','setFilterGrid("list_view",-1)');
	});*/
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

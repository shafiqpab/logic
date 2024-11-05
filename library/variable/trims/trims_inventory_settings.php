<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Inventory Variable Settings
Functionality	:	Must fill Company, Variable List
JS Functions	:
Created by		:	Sohel
Creation date 	: 	25-03-2013
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//----------------------------------------------------------------------------------------------------------------
 echo load_html_head_contents("Location Details", "../../../", 1, 1,$unicode,'','');

?>

<script type="text/javascript" charset="utf-8">
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php"; 
var permission='<? echo $permission; ?>';

function fnc_variable_settings_inventory(operation)
{

	// if(operation==1){
	// 	alert("Update Is Restricted in Variable Settings");
	// 	return;
	// }
	// if(operation==2){
	// 	alert("Delete Is Restricted in Variable Settings");
	// 	return;
	// }
	if (document.getElementById('cbo_variable_list').value*1==16 || document.getElementById('cbo_variable_list').value*1==19 || document.getElementById('cbo_variable_list').value*1==33 || document.getElementById('cbo_variable_list').value*1==30 || document.getElementById('cbo_variable_list').value*1==31)
	{
		if ( form_validation('cbo_company_name*cbo_item_category*cbo_item_status','Company Name*Select item category*Select item status')==0 )
		{
			return;
		}
		else
		{	
			//nocache = Math.random();
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_item_category*cbo_item_status*update_id',"../../../");
			// alert(data);
			freeze_window(operation);
			http.open("POST","requires/trims_inventory_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_inventory_reponse;
		}
	}
	
	if (document.getElementById('cbo_variable_list').value*1==20)
	{
		if ( form_validation('cbo_company_name*cbo_variable_list*txt_menu_name','Company Name*Variable List*Page Name')==0 )
		{
			return;
		}
		else
		{	
			//nocache = Math.random();
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*txt_menu_name*txt_menu_id*cbo_independent_con*cbo_rate_opption*cbo_rate_hide*cbo_rate_con*update_id',"../../../");
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/trims_inventory_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_inventory_reponse;
		}
	}
	/*if (document.getElementById('cbo_variable_list').value*1==22)
	{
		if ( form_validation('cbo_company_name*cbo_variable_list*cbo_page_neme','Company Name*Variable List*Page Name')==0 )
		{
			return;
		}
		else
		{	
			//nocache = Math.random();
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_module*cbo_page_neme*cbo_independent_con*update_id',"../../../");
			
			freeze_window(operation);
			http.open("POST","requires/trims_inventory_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_inventory_reponse;
		}
	}*/
	if (document.getElementById('cbo_variable_list').value*1==10)
	{
		if ( form_validation('cbo_company_name*cbo_variable_list*cbo_category','Company Name*Variable List*Item Category')==0 )
		{
			return;
		}
		else
		{	
			//nocache = Math.random();
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_category*cbo_rate_optional*cbo_editable*update_id',"../../../");
			
			freeze_window(operation);
			http.open("POST","requires/trims_inventory_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_inventory_reponse;
		}
	}
	
}

function fnc_variable_settings_inventory_reponse()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var reponse=trim(http.responseText).split('**');
		//if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		//document.getElementById('update_id').value  = reponse[2];
		show_list_view(document.getElementById('cbo_variable_list').value+'_'+document.getElementById('cbo_company_name').value,'on_change_data_list','list_view_con','../variable/requires/trims_inventory_settings_controller','');
		set_button_status(0, permission, 'fnc_variable_settings_inventory',1);
		if(document.getElementById('cbo_variable_list').value==20)
		{
			reset_form('','','txt_menu_name*txt_menu_id*cbo_independent_con*cbo_rate_opption*cbo_rate_hide*cbo_rate_con');
		}
		else if(document.getElementById('cbo_variable_list').value==20)
		{
			reset_form('','','cbo_page_neme*cbo_independent_con');
		}
		else
		{
			reset_form('','','cbo_item_category*cbo_item_status');
		}
		release_freezing();return;
	}
}	

function populate_data()
{
	if(form_validation('cbo_company_name','Company Name')==false )
	{
		$("#cbo_variable_list").val(0);
		return;
	}
	else
	{
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_variable_list=document.getElementById('cbo_variable_list').value;
		show_list_view(cbo_variable_list+'_'+cbo_company_name,'on_change_data','variable_settings_container','../../variable/trims/requires/trims_inventory_settings_controller','');
		set_hotkey();
	}
}



function populate_data_search()
{
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbo_variable_list=document.getElementById('cbo_variable_list').value;
	show_list_view(cbo_variable_list+'_'+cbo_company_name,'on_change_data','variable_settings_container','../../variable/trims/requires/trims_inventory_settings_controller','');
	//show_list_view(document.getElementById('cbo_variable_list').value+'_'+document.getElementById('cbo_company_name').value,'on_change_data_list','list_view_con','../variable/requires/trims_inventory_settings_controller','');
}


//------------------------ ile save here------------------------------//
//--------------------------------------------------------------------//

function add_variable_row( rowID ) 
{
	//$("#txt_standard"+rowID).val()=="" ||$("#cbo_item_group"+rowID).val()==0 ||
	var row = $("#tbl_variable_list tr").length-1; 
	if( $("#cbo_category"+rowID).val()==0 ||  $("#cbo_source"+rowID).val()==0 ||  row!=rowID )
	{
		return;
	}	
	var responseHtml = return_ajax_request_value(rowID, 'append_load_details_container', 'requires/trims_inventory_settings_controller');
	$("#tbl_variable_list").append(responseHtml);
	set_hotkey();
}

 


function fnc_variable_settings_inventory_ile( operation )
{
 // 	if(operation==1){
	// 	alert("Update Is Restricted in Variable Settings");
	// 	return;
	// }
	// if(operation==2){
	// 	alert("Delete Is Restricted in Variable Settings");
	// 	return;
	// }
	
	if ( form_validation('cbo_company_name*cbo_variable_list','Company Name*Variable List')==false )
	{
		return;
	}
	else
	{	
		var row = $("#tbl_variable_list tr").length-1;
		var detailsData="";
		for(var i=1;i<=row;i++)
		{
			try
			{
				if( $('#cbo_category'+i).val()!=0 || $('#cbo_item_group'+i).val()!=0 || $('#cbo_source'+i).val()!=0 ||  $('#txt_standard'+i).val()!="" )
				{
							  
					detailsData+='*cbo_category'+i+'*cbo_item_group'+i+'*cbo_source'+i+'*txt_standard'+i;
				}
			}
			catch(err){}
		}
		 
		if(detailsData=="" && ( form_validation('cbo_category1','ILE/Landed Cost Standard') || form_validation('cbo_item_group1','ILE/Landed Cost Standard') || form_validation('cbo_source1','ILE/Landed Cost Standard') || form_validation('txt_standard1','ILE/Landed Cost Standard') )==false )
		{ 
			return; 
		}
		//nocache = Math.random();
		var data="action=save_update_delete_ile&operation="+operation+'&row='+row+get_submitted_data_string('cbo_company_name*cbo_variable_list*update_id'+detailsData,"../../../");
		freeze_window(operation);
		http.open("POST","requires/trims_inventory_settings_controller.php", true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_variable_settings_inventory_ile_reponse;
	}
	
}

function fnc_variable_settings_inventory_ile_reponse()
{
	if(http.readyState == 4) 
	{
		
 		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
 			show_msg(reponse[0]);
			set_button_status(1, permission, 'fnc_variable_settings_inventory_ile',1);
		}		
 		release_freezing();
	}
}	

//--------------------------- ile save end----------------------------//
//--------------------------------------------------------------------//


//--------------store method save------------------------//
function fnc_variable_settings_inventory_store_method(operation)
{
	
	if(form_validation('cbo_company_name*cbo_item_category*cbo_store_method','Company Name*Item Category*Store Method')==0 )
	{
		return;
	}
	else
	{	
		var data="action=save_update_delete_store_method&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_item_category*cbo_store_method*update_id',"../../../");
		freeze_window(operation);
		http.open("POST","requires/trims_inventory_settings_controller.php", true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_variable_settings_inventory_store_method_reponse;
	} 
	
}

function fnc_variable_settings_inventory_store_method_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');		
		release_freezing();
		if(reponse[0]==11)
		{
			show_msg(reponse[0]);
			return;
		}
		show_msg(reponse[0]);		
		populate_data();
		
	}
 	
}
//-------------------end---------------------------------//


//--------------Allocated Quantity save------------------------//
function fnc_variable_settings_inventory_allocation(operation)
{
	
	var variable_list=$('#cbo_variable_list').val();
	if(variable_list==21)
	{
		if(form_validation('cbo_company_name*cbo_item_category*cbo_rack_balance*cbo_up_to','Company Name*Item Category*Rack Balance*Up To')==0 )
		{
			return;
		}
		else
		{	
			var data="action=save_update_delete_allocated&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_item_category*cbo_rack_balance*cbo_up_to*update_id',"../../../");
			
			freeze_window(operation);
			http.open("POST","requires/trims_inventory_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_inventory_allocation_reponse;
		} 
	}
	else
	{
		if(form_validation('cbo_company_name*cbo_item_category*cbo_allocated','Company Name*Item Category*Allocated')==0 )
		{
			return;
		}
		else
		{	
			var data="action=save_update_delete_allocated&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_item_category*cbo_allocated*cbo_smn_allocated*cbo_sales_allocated*update_id',"../../../");
			
			freeze_window(operation);
			http.open("POST","requires/trims_inventory_settings_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_variable_settings_inventory_allocation_reponse;
		} 
	}
	
	
}

function fnc_variable_settings_inventory_allocation_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');		
		release_freezing();
		if(reponse[0]==11)
		{
			show_msg(trim(reponse[0]));
			return;
		}
		show_msg(trim(reponse[0]));		
		populate_data();
		
	}
 	
}


function fnc_material_over_receive_control( operation )
{
 	
	if ( form_validation('cbo_company_name*cbo_variable_list','Company Name*Variable List')==false )
	{
		return;
	}
	else
	{	
		var row = $("#tbl_variable_list tr").length-1;
		var detailsData="";
		for(var i=1;i<=row;i++)
		{
			try
			{
				if( $('#cbo_category'+i).val()!=0 && $('#txt_over_rcv_percent'+i).val()!="")
				{
							  
					detailsData+='*cbo_category'+i+'*txt_over_rcv_percent'+i+'*txt_over_rcv_payment'+i;
				}
			}
			catch(err){}
		}
		 
		 
		 
		if(detailsData=="" && ( form_validation('cbo_category1','ILE/Landed Cost Standard') || form_validation('txt_over_rcv_percent1','Over Rcv. Percent') )==false )
		{ 
			return; 
		}
		//nocache = Math.random();
		var data="action=save_update_delete_material_over_receive_control&operation="+operation+'&row='+row+get_submitted_data_string('cbo_company_name*cbo_variable_list*update_id'+detailsData,"../../../");
		freeze_window(operation);
		http.open("POST","requires/trims_inventory_settings_controller.php", true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_material_over_receive_control_reponse;
	}
	
}

function fnc_material_over_receive_control_reponse()
{
	if(http.readyState == 4) 
	{
		
 		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
 			show_msg(reponse[0]);
			set_button_status(1, permission, 'fnc_material_over_receive_control',1);
		}		
 		release_freezing();
	}
}	

function fn_add_new_row( rowID,ref_from ) 
{
	//alert(1);
	if(ref_from==1)
	{
		if($("#txt_over_rcv_percent"+rowID).val()==0){$("#txt_over_rcv_payment"+rowID).val(2);}
	}

	var row = $("#tbl_variable_list tr").length-1; 
	if( $("#cbo_category"+rowID).val()==0 || $("#txt_over_rcv_percent"+rowID).val()=="" ||  row!=rowID )
	{
		return;
	}	
	var responseHtml = return_ajax_request_value(rowID, 'append_load_material_over_receive_control', 'requires/trims_inventory_settings_controller');
	$("#tbl_variable_list").append(responseHtml);
	set_hotkey();
}

function fn_over_rcv_percent_check(rowID,val,ref_from)
{
	if(ref_from==1)
	{
		if(val==0){
			$("#txt_over_rcv_payment"+rowID).val(2);
			
		}
		else{
			$("#txt_over_rcv_payment"+rowID).val(1);
		}
		fn_add_new_row( rowID, ref_from )
	}
	else
	{
		if(val==1){
			//$("#txt_over_rcv_percent"+rowID).val(2);
		}
		else{
			$("#txt_over_rcv_percent"+rowID).val(0);
		}
		fn_add_new_row( rowID, ref_from )
	}
}

function fu_check_duplicate_item(rowID){
	var row = $("#tbl_variable_list tr").length;
	for(var i=1;i<row;i++){
		if($("#cbo_category"+rowID).val()==$("#cbo_category"+i).val() && rowID!=i){
			alert("Duplicate Item Not Allowed.");
			$("#cbo_category"+rowID).val(0);
		}
	}
}




function fnc_variable_settings_requisition_mandatory(operation)
{
    if(operation == 2){
        alert("delete not allowed");
        return;
    }
    if ( form_validation('cbo_company_name*cbo_variable_list*cbo_independent_con','Company Name*Variable List*status')==0 )
    {
            return;
    }
    else
    {	
            //nocache = Math.random();
            var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_independent_con*update_id',"../../../");

            freeze_window(operation);
            http.open("POST","requires/trims_inventory_settings_controller.php", true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_variable_settings_requisition_mandatory_reponse;
    }
}
function fnc_variable_settings_requisition_mandatory_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
 			show_msg(reponse[0]);
			set_button_status(1, permission, 'fnc_variable_settings_requisition_mandatory',1);
		}		
 		release_freezing();
	}
}

function fnc_variable_settings_auto_transfer_rcv(operation)
{
    if(operation == 2){
        alert("delete not allowed");
        return;
    }
    if ( form_validation('cbo_company_name*cbo_variable_list*cbo_item_category*cbo_independent_con','Company Name*Variable List*status')==0 )
    {
            return;
    }
    else
    {	
            //nocache = Math.random();
            var data="action=save_update_delete_auto_transfer_rcv&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_item_category*cbo_independent_con*update_id',"../../../");

            freeze_window(operation);
            http.open("POST","requires/trims_inventory_settings_controller.php", true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_variable_settings_auto_transfer_rcv_reponse;
    }
}
function fnc_variable_settings_auto_transfer_rcv_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
 			show_msg(reponse[0]);
			set_button_status(1, permission, 'fnc_variable_settings_auto_transfer_rcv',1);
		}		
 		release_freezing();
	}
}


/*yarn issue status starts here*/

function fnc_variable_settings_yarn_issue_basis(operation)
{
    if(operation == 2){
        alert("delete not allowed");
        return;
    }
    if ( form_validation('cbo_company_name*cbo_variable_list*cbo_independent_con','Company Name*Variable List*status')==0 )
    {
            return;
    }
    else
    {	
            //nocache = Math.random();
            var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_independent_con*update_id',"../../../");

            freeze_window(operation);
            http.open("POST","requires/trims_inventory_settings_controller.php", true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_variable_settings_yarn_issue_status_response;
    }
}
function fnc_variable_settings_yarn_issue_status_response()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
 			show_msg(reponse[0]);
			set_button_status(1, permission, 'fnc_variable_settings_yarn_issue_basis',1);
		}		
 		release_freezing();
	}
}



function fnc_during_issue(operation)
{
    if(operation == 2){
        alert("delete not allowed");
        return;
    }
    if ( form_validation('cbo_company_name*cbo_variable_list*cbo_during_issue','Company Name*Variable List*During Issue')==0 )
    {
            return;
    }
    else
    {	
            //nocache = Math.random();
            var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_variable_list*cbo_during_issue*update_id',"../../../");

            freeze_window(operation);
            http.open("POST","requires/trims_inventory_settings_controller.php", true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_during_issue_reponse;
    }
}
function fnc_during_issue_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
 			show_msg(reponse[0]);
			set_button_status(1, permission, 'fnc_during_issue',1);
		}		
 		release_freezing();
	}
}



function fn_menu_page()
{
	var txt_menu_name = $("#txt_menu_name").val();
	var txt_menu_id = $("#txt_menu_id").val();
	var page_link = 'requires/trims_inventory_settings_controller.php?action=menu_popup&txt_menu_name='+txt_menu_name+'&txt_menu_id='+txt_menu_id;

	var title = "Service  Booking Search";
	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px, height=350px, center=1, resize=0, scrolling=0', '')
	emailwindow.onclose = function ()
	{
		var txt_menu_id=this.contentDoc.getElementById("txt_menu_id").value;
		var txt_menu_name=this.contentDoc.getElementById("txt_menu_name").value;
		$('#txt_menu_id').val(txt_menu_id);
		$('#txt_menu_name').val(txt_menu_name);
	}
}


//-------------------end---------------------------------//

</script>

</head>

<body onLoad="set_hotkey()" >
	<form name="inventoryvariablesettings_1" id="inventoryvariablesettings_1" autocomplete="off">	
    <div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../../",$permission);  ?>
        
		<fieldset style="width:900px;">
		<legend>Inventory Variable Settings</legend>		
            <table width="900" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td width="150" align="center" class="must_entry_caption">Company</td>
                    <td width="300">
                    <? 
                    echo create_drop_down( "cbo_company_name", 250, "select company_name,id from lib_company where is_deleted=0  and status_active=1 $company_name order by company_name",'id,company_name', 1, '--- Select Company ---', 0, "populate_data_search()");
                    ?>
                    </td>
                    <td width="150" align="center">Variable List</td>
                    <td width="300">
                    <? 
                    	echo create_drop_down( "cbo_variable_list", 250, $inventory_module,'', '1', '---- Select ----', '0',"populate_data()",'',"27");
                    ?>
                    </td>
                </tr>
            </table>		
		</fieldset>
     	<div style="width:895px;" align="center" id="variable_settings_container"></div>	
    	</div>    
    </form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    


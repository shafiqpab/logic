<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create General Item Transfer Entry
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	01-02-2015
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


//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id,item_cate_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}
if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;  
}
//========== user credential end ==========
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("General Item Transfer Info","../../", 1, 1, '','',''); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function active_inactive(str)
{
	//reset_form('transferEntry_1','div_transfer_item_list','','',"",'cbo_transfer_criteria');
	$('#cbo_company_id').val(0);
	$('#cbo_company_id_to').val(0);
	if(str==1)
	{
		$('#cbo_company_id_to').removeAttr('disabled','disabled');	
	}
	else
	{
		$('#cbo_company_id_to').attr('disabled','disabled');
	}
}

function openmypage_systemId()
{
	var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_company_to = $('#cbo_company_id_to').val();

	if (form_validation('cbo_transfer_criteria*cbo_company_id','Transfer Criteria*Company')==false)
	{
		return;
	}
	
	var title = 'Item Transfer Info';	
	var page_link = 'requires/general_item_transfer_controller.php?cbo_transfer_criteria='+cbo_transfer_criteria+'&cbo_company_id='+cbo_company_id+'&cbo_company_to='+cbo_company_to+'&action=itemTransfer_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		
		reset_form('transferEntry_1','div_transfer_item_list','','','','txt_variable_status');
		
		var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
		$("#is_posted_account").val(posted_in_account);
		if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
		else 					 document.getElementById("accounting_posted_status").innerHTML="";
		
		get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/general_item_transfer_controller" );
		show_list_view(transfer_id,'show_transfer_listview','div_transfer_item_list','requires/general_item_transfer_controller','');
		var hidden_req_id = $('#hidden_req_id').val();
		if(hidden_req_id!="" && hidden_req_id>0)
		{
			var company_id=$('#cbo_company_id').val();
			var store_id=$('#cbo_store_name').val();
			show_list_view(hidden_req_id+"_"+company_id+"_"+store_id,'show_item_requisition_listview','div_item_requisition_listview','requires/general_item_transfer_controller','');
		}
		set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
	}
}

function openmypage_itemDescription()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_item_category = $('#cbo_item_category').val();
	var cbo_store_name = $('#cbo_store_name').val();

	if (form_validation('cbo_company_id*cbo_store_name*cbo_item_category','Company*From Store*Item Category')==false)
	{
		return;
	}
	
	var title = 'Item Description Info';	
	var page_link = 'requires/general_item_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_item_category='+cbo_item_category+'&cbo_store_name='+cbo_store_name+'&action=itemDescription_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var product_id=this.contentDoc.getElementById("product_id").value; //Access form field with id="emailfield"
		get_php_form_data(product_id+"_"+cbo_store_name+"_"+cbo_company_id, "populate_data_from_product_master", "requires/general_item_transfer_controller" );

		$('#cbo_store_name').attr('disabled','disabled');
		$('#cbo_floor').attr('disabled','disabled');
		$('#cbo_room').attr('disabled','disabled');
		$('#txt_rack').attr('disabled','disabled');
		$('#txt_shelf').attr('disabled','disabled');
		$('#cbo_bin').attr('disabled','disabled');
	}
}

function calculate_value()
{
	var trans_qnty=$('#txt_transfer_qnty').val()*1;
	var stock_qnty=$('#txt_current_stock').val()*1;
	if(trans_qnty>stock_qnty)
	{
		alert("Transfer Quantity Not Allow Over Stock");
		$('#txt_transfer_qnty').val("");
		$('#txt_transfer_value').val("");
		$('#txt_transfer_qnty').focus();
		return;
	}
	var txt_transfer_qnty = $('#txt_transfer_qnty').val()*1;
	var txt_rate = $('#txt_rate').val()*1;
	
	var transfer_value=txt_transfer_qnty*txt_rate;
	$('#txt_transfer_value').val(transfer_value.toFixed(4));
}
 
function fnc_yarn_transfer_entry(operation)
{
	if(operation==4)
	{
		var show_val_column = "0";
    	var r = confirm("Press \"OK\" to Hide Rate and Amount.\nPress \"Cancel\" to Show Rate and Amount.");
    	if (r == true) show_val_column = "1";

		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+show_val_column, "yarn_transfer_print", "requires/general_item_transfer_controller" ) 
		return;
	}
	if(operation==2)
	{		 
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title, "yarn_transfer_print2", "requires/general_item_transfer_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if ($("#is_posted_account").val()*1 == 1) {
			alert("Already Posted In Accounting. Save Update Delete Restricted.");
			return;
		}

		var txt_variable_status=$("#txt_variable_status").val();
		if (txt_variable_status==1){
			if ($("#txt_requisition_no").val() == ""){
				alert("Plz Browse Requisition No.");
				return;				
			}			
		}
		
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
			alert("Transfer Date Can not Be Greater Than Current Date");
			return;
		}
		
		if( form_validation('cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_transfer_date*cbo_store_name*cbo_store_name_to*txt_item_desc*txt_transfer_qnty*cbo_item_category','Transfer Criteria*Company*To Company*Transfer Date*From Store*To Store*Item Description*Transfered Qnty*Item Category')==false )
		{
			return;
		}	
		
		var variable_lot=$('#variable_lot').val()*1;
		var cbo_item_category=$('#cbo_item_category').val()*1;
		var txt_lot=$('#txt_lot').val();
		if(variable_lot==1 && cbo_item_category==22 && txt_lot=="")
		{
			alert("Lot Maintain Mandatory.");
			$('#txt_lot').focus();
			return;
		}
		
		/*if($("#txt_transfer_qnty").val()*1>$("#hidden_current_stock").val()*1)
		{
			alert("Trasfer Quantity Can not be Greater Than Current Stock.");
			$("#txt_transfer_qnty").focus();
			return;
		}*/
		
		

		// Store upto validation start
		var store_update_upto=$('#store_update_upto').val()*1;
		var txt_floor=$('#cbo_floor').val()*1;
		var txt_room=$('#cbo_room').val()*1;
		var txt_rack=$('#txt_rack').val()*1;
		var txt_shelf=$('#txt_shelf').val()*1;
		var txt_bin=$('#cbo_bin').val()*1;
		
		if(store_update_upto > 1)
		{
			if(store_update_upto==6 && (txt_floor==0 || txt_room==0 || txt_rack==0 || txt_shelf==0 || txt_bin==0))
			{
				alert("Up To Bin Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==5 && (txt_floor==0 || txt_room==0 || txt_rack==0 || txt_shelf==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==4 && (txt_floor==0 || txt_room==0 || txt_rack==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==3 && (txt_floor==0 || txt_room==0))
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==2 && txt_floor==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
		}

		var store_update_upto_to=$('#store_update_upto_to').val()*1;
		var txt_floor_to=$('#cbo_floor_to').val()*1;
		var txt_room_to=$('#cbo_room_to').val()*1;
		var txt_rack_to=$('#txt_rack_to').val()*1;
		var txt_shelf_to=$('#txt_shelf_to').val()*1;
		var txt_bin_to=$('#cbo_bin_to').val()*1;
		
		if(store_update_upto_to > 1)
		{
			if(store_update_upto_to==6 && (txt_floor_to==0 || txt_room_to==0 || txt_rack_to==0 || txt_shelf_to==0 || txt_bin_to==0))
			{
				alert("Up To Bin Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto_to==5 && (txt_floor_to==0 || txt_room_to==0 || txt_rack_to==0 || txt_shelf_to==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto_to==4 && (txt_floor_to==0 || txt_room_to==0 || txt_rack_to==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto_to==3 && (txt_floor_to==0 || txt_room_to==0))
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto_to==2 && txt_floor_to==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
		}
		// Store upto validation End
		
		
		if($("#cbo_transfer_criteria").val()==1)
		{
			if($("#cbo_company_id").val()*1==$("#cbo_company_id_to").val()*1)
			{
				alert("Same Company Not Allow.");
				return;
			}
			
			if($("#cbo_company_id_to").val()==0)
			{
				alert("Please Select To Company.");
				$("#cbo_company_id_to").focus();
				return;
			}
		}
		else
		{
			if(store_update_upto_to > 1)
			{
				if( ($("#cbo_store_name").val()*1==$("#cbo_store_name_to").val()*1) && (txt_floor==txt_floor_to) && (txt_room==txt_room_to) && (txt_rack==txt_rack_to) && (txt_shelf==txt_shelf_to) && (txt_bin==txt_bin_to))
				{
					alert("Same Store Not Allow.");
					return;
				}
			}
			else
			{
				if($("#cbo_store_name").val()*1==$("#cbo_store_name_to").val()*1)
				{
					alert("Same Store Not Allow.");
					return;
				}
			}
		}
		
		var dataString = "txt_system_id*cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_transfer_date*txt_challan_no*cbo_item_category*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to*cbo_bin_to*txt_transfer_qnty*txt_rate*txt_transfer_value*cbo_uom*update_id*hidden_product_id*update_dtls_id*update_trans_issue_id*update_trans_recv_id*previous_from_prod_id*previous_to_prod_id*hidden_transfer_qnty*txt_item_desc*variable_string_inventory*variable_lot*txt_lot*txt_requisition_no*hidden_req_id*hidden_req_dtls_id*txt_serial_no*store_update_upto_to*store_update_upto";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/general_item_transfer_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_transfer_entry_reponse;
	}
}

function fnc_yarn_transfer_entry_reponse()
{	
	if(http.readyState == 4) 
	{
		//alert(http.responseText);release_freezing();return;	  		
		var reponse=trim(http.responseText).split('**');	
                
        if(reponse[0]*1==20*1)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
		else if(reponse[0]*1==16*1)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
		else if(reponse[0]*1==17*1)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
                
		show_msg(reponse[0]); 	
			
		if(reponse[0]==0 || reponse[0]==1)
		{
			$("#update_id").val(reponse[1]);
			$("#txt_system_id").val(reponse[2]);
			disable_enable_fields( 'cbo_transfer_criteria*cbo_company_id*cbo_company_id_to', 1, "", "" );
			//$('#cbo_company_id').attr('disabled','disabled');
			disable_enable_fields('cbo_store_name*cbo_store_name_to*cbo_item_category*txt_item_desc',0);
			//disable_enable_fields('cbo_store_name_to',0);
			reset_form('transferEntry_1','','','','','update_id*txt_system_id*cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_transfer_date*txt_challan_no*store_update_upto*store_update_upto_to*txt_requisition_no*txt_variable_status*hidden_req_id*cbo_item_category*cbo_store_name*cbo_store_name_to');
			show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/general_item_transfer_controller','');
			var hidden_req_id = $('#hidden_req_id').val();
			if(hidden_req_id!="" && hidden_req_id>0)
			{
				var company_id=$('#cbo_company_id').val();
				var store_id=$('#cbo_store_name').val();
				show_list_view(hidden_req_id+"_"+company_id+"_"+store_id,'show_item_requisition_listview','div_item_requisition_listview','requires/general_item_transfer_controller','');
			}
			set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
		}	
		if(reponse[0]==2)
		{
			if(reponse[4]==1)
			{
				show_msg(reponse[0]);
				release_freezing();
				location.reload();
			}
			if(reponse[4]==2)
			{
				$("#update_id").val(reponse[1]);
				$("#txt_system_id").val(reponse[2]);
				disable_enable_fields( 'cbo_transfer_criteria*cbo_company_id*cbo_company_id_to', 1, "", "" );
				//$('#cbo_company_id').attr('disabled','disabled');
				disable_enable_fields('cbo_store_name*cbo_store_name_to*cbo_item_category*txt_item_desc',0);
				//disable_enable_fields('cbo_store_name_to',0);
				reset_form('transferEntry_1','','','','','update_id*txt_system_id*cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_transfer_date*txt_challan_no*store_update_upto*store_update_upto_to*txt_requisition_no*txt_variable_status*hidden_req_id');
				show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/general_item_transfer_controller','');
				var hidden_req_id = $('#hidden_req_id').val();
				if(hidden_req_id!="" && hidden_req_id>0)
				{
					var company_id=$('#cbo_company_id').val();
					var store_id=$('#cbo_store_name').val();
					show_list_view(hidden_req_id+"_"+company_id+"_"+store_id,'show_item_requisition_listview','div_item_requisition_listview','requires/general_item_transfer_controller','');
				}
				set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
			}

		}	
		release_freezing();
	}
}

function fn_to_store(company_id)
{
	var transfer_criteria=$('#cbo_transfer_criteria').val();
	
	if(transfer_criteria==2)
	{
		load_drop_down( 'requires/general_item_transfer_controller', company_id, 'load_drop_down_store_to', 'store_td' );
	}
}

function company_onchange(company) 
{
	reset_form('transferEntry_1','div_transfer_item_list','','',"",'cbo_company_id*cbo_transfer_criteria*txt_transfer_date');
	// if($("#cbo_transfer_criteria").val() != 1)
	// {
	// 	$("#cbo_company_id_to").val(company);
	// }
     
	var company = $("#cbo_company_id").val();
	var transfer_criteria = $("#cbo_transfer_criteria").val();

	if (transfer_criteria == 1){
		load_drop_down( 'requires/general_item_transfer_controller',company+"_"+transfer_criteria, 'load_drop_down_to_company', 'to_company_td' );
	}
	else if(transfer_criteria == 2)
	{    		
		load_drop_down( 'requires/general_item_transfer_controller',company+"_"+transfer_criteria, 'load_drop_down_to_company', 'to_company_td' );
		$("#cbo_company_id_to").val(company);
		$('#cbo_company_id_to').attr('disabled',true);
	}
	else
	{
		$("#cbo_company_id_to").val(company);
	}    

	// ==============Start Floor Room Rack Shelf Bin upto variable Settings============
    /*var data='cbo_company_id='+company+'&action=upto_variable_settings';
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("store_update_upto").value = this.responseText;	
            if($("#cbo_transfer_criteria").val() != 1)
			{
				$('#store_update_upto_to').val(this.responseText);
			}			
        }
    }
    xmlhttp.open("POST", "requires/general_item_transfer_controller.php", true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(data);*/
	
	var varible_string=return_global_ajax_value( company, 'varible_inventory', '', 'requires/general_item_transfer_controller');
	var varible_string_ref=varible_string.split("**");
	if(varible_string_ref[0])
	{
		$('#variable_string_inventory').val(varible_string_ref[1]);
		if(varible_string_ref[1]==1)
		{
			$('#rate_td').css("display", "none");
			$('#amount_td').css("display", "none");
		}
		else
		{
			$('#rate_td').css("display", "");
			$('#amount_td').css("display", "");
		}
			
	}
	else
	{
		$('#variable_string_inventory').val("");
		$('#rate_td').css("display", "");
		$('#amount_td').css("display", "");
	}
	$('#store_update_upto').val(varible_string_ref[2]);
	$('#store_update_upto_to').val(varible_string_ref[2]);
	$('#variable_lot').val(varible_string_ref[3]);
	
    // ==============End Floor Room Rack Shelf Bin upto variable Settings============
}

function to_company_on_change(to_company)
{
	// ==============Start Floor Room Rack Shelf Bin upto variable Settings============
    var data='cbo_company_id='+to_company+'&action=upto_variable_settings';
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("store_update_upto_to").value = this.responseText;				
        }
    }
    xmlhttp.open("POST", "requires/general_item_transfer_controller.php", true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(data);
    // ==============End Floor Room Rack Shelf Bin upto variable Settings============

}

// ==============End Floor Room Rack Shelf Bin upto disable============
function storeUpdateUptoDisable() 
{
	var store_update_upto_to=$('#store_update_upto_to').val()*1;	
	if(store_update_upto_to==5)
	{
		$('#cbo_bin_to').prop("disabled", true);
	}
	if(store_update_upto_to==4)
	{
		$('#txt_shelf_to').prop("disabled", true);
		$('#cbo_bin_to').prop("disabled", true);
	}
	else if(store_update_upto_to==3)
	{
		$('#txt_rack_to').prop("disabled", true);
		$('#txt_shelf_to').prop("disabled", true);
		$('#cbo_bin_to').prop("disabled", true);
	}
	else if(store_update_upto_to==2)
	{	
		$('#cbo_room_to').prop("disabled", true);
		$('#txt_rack_to').prop("disabled", true);
		$('#txt_shelf_to').prop("disabled", true);	
		$('#cbo_bin_to').prop("disabled", true);
	}
	else if(store_update_upto_to==1)
	{
		$('#cbo_floor_to').prop("disabled", true);
		$('#cbo_room_to').prop("disabled", true);
		$('#txt_rack_to').prop("disabled", true);
		$('#txt_shelf_to').prop("disabled", true);	
		$('#cbo_bin_to').prop("disabled", true);	
	}
}
// ==============End Floor Room Rack Shelf Bin upto disable============

function reset_on_change(id)
{
	
	if(id =="cbo_store_name_to")
	{
		// var unRefreshId = "cbo_company_id*cbo_location*cbo_store_name_to*txt_delivery_date*store_update_upto";
		load_drop_down('requires/general_item_transfer_controller', '0', 'load_drop_floor','to_floor_td');
		load_drop_down('requires/general_item_transfer_controller', '0', 'load_drop_room','to_room_td');
		load_drop_down('requires/general_item_transfer_controller', '0', 'load_drop_rack','to_rack_td');
		load_drop_down('requires/general_item_transfer_controller', '0', 'load_drop_shelf','to_shelf_td');
		load_drop_down('requires/general_item_transfer_controller', '0', 'load_drop_bin','to_bin_td');
	}
	else if(id =="cbo_company_id_to")
	{
		// var unRefreshId = "cbo_company_id*txt_delivery_date*store_update_upto";
		load_drop_down('requires/general_item_transfer_controller', '0', 'load_drop_down_store','store_td_to');
		load_drop_down('requires/general_item_transfer_controller', '0', 'load_drop_floor','to_floor_td');
		load_drop_down('requires/general_item_transfer_controller', '0', 'load_drop_room','to_room_td');
		load_drop_down('requires/general_item_transfer_controller', '0', 'load_drop_rack','to_rack_td');
		load_drop_down('requires/general_item_transfer_controller', '0', 'load_drop_shelf','to_shelf_td');
		load_drop_down('requires/general_item_transfer_controller', '0', 'load_drop_bin','to_bin_td');
	}
	// reset_form('finishFabricEntry_1', 'list_container_finishing*roll_details_list_view*list_fabric_desc_container', '', '', '', unRefreshId);

}

function chk_issue_requisition_variable(company)
{
	
   var status_data = return_global_ajax_value(company, 'chk_issue_requisition_variable', '', 'requires/general_item_transfer_controller').trim();
   if(status_data== 1)
   {
	   $('#txt_variable_status').val(status_data);
       $("#txt_requisition_no").prop('readonly',true);
       $("#txt_requisition_no").attr('placeholder',"Browse").attr('onDblClick','fnc_items_sys_popup()');
	   $("#txt_item_desc").prop('readonly',true).prop('disabled',true);
	   $("#cbo_item_category").prop('readonly',true).prop('disabled',true);
   }
   else
   {	
   		$('#txt_variable_status').val(null);
        $("#txt_requisition_no").prop('readonly', false);
        $("#txt_requisition_no").attr('placeholder',"write").removeAttr('onDblClick');
		$("#txt_item_desc").prop('readonly',false).prop('disabled',false);
		$("#cbo_item_category").prop('readonly',false).prop('disabled',false);
   }
}

function fnc_items_sys_popup()
{
    var cbo_company_id=$('#cbo_company_id').val();
    var cbo_transfer_criteria=$('#cbo_transfer_criteria').val();
	var cbo_company_id_to=$('#cbo_company_id_to').val();
    if( form_validation('cbo_transfer_criteria*cbo_company_id','Transfer Criteria*Company Name')==false )
    {
        return;
    }

    var page_link='requires/general_item_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=item_requisition_popup_search&cbo_company_id_to='+cbo_company_id_to;
    var title='Issue Req. No Pop up'
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=1,scrolling=0','../');

    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
		var requisition_info=this.contentDoc.getElementById("requisition_info").value; 

        var data=requisition_info.split("_");

        if(trim(requisition_info)!="")
        {
			freeze_window(5);
			reset_form('transferEntry_1','','','','','update_id*txt_system_id*cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_transfer_date*txt_challan_no*store_update_upto*store_update_upto_to*txt_requisition_no*txt_variable_status*hidden_req_id');//*cbo_item_category*cbo_store_name*cbo_store_name_to
			$('#hidden_req_id').val(data[0]);
			$('#txt_requisition_no').val(data[1]);
			$('#cbo_company_id_to').val(data[2]);
			$('#cbo_store_name').val(data[5]);
			//alert(data[5]);

			$('#cbo_transfer_criteria').attr('disabled',true);
			$('#cbo_company_id').attr('disabled',true);
			$('#cbo_company_id_to').attr('disabled',true);
			$('#txt_requisition_no').attr('disabled',true);
			$('#cbo_store_name').attr('disabled',true);
			if(data[2]!=0 && data[2]!='')
			{
				load_drop_down('requires/general_item_transfer_controller', data[2], 'load_drop_down_store','store_td_to');
			}
			var company_id=$('#cbo_company_id').val();
			//alert(company_id);
			var store_id=data[5];
			show_list_view(data[0]+"_"+company_id+"_"+store_id,'show_item_requisition_listview','div_item_requisition_listview','requires/general_item_transfer_controller','');
        }
        release_freezing();
    }
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
    <div style="width:880px; float: left;position: relative;">   
        <fieldset style="width:880px;">
        <legend>General Item Transfer Entry</legend>
        <br>
        	<fieldset style="width:850px;">
                <table width="800" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>Transfer System ID</strong></td>
                        <td colspan="3" align="left">
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                            <input type="hidden" name="update_id" id="update_id" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Transfer Criteria</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_transfer_criteria", 160,$item_transfer_criteria,"", 1,"-- Select --",'0',"active_inactive(this.value);",'','1,2');
                            ?>
                        </td>
                        <td class="must_entry_caption">Company</td>
                        <td>
                            <? 
								echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_room_rack_self_bin('requires/general_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'store','from_store_td', this.value);reset_on_change(this.id);load_drop_down('requires/general_item_transfer_controller', this.value, 'load_drop_down_store','store_td_to');company_onchange(this.value);chk_issue_requisition_variable(this.value);" );
								//load_drop_down( 'requires/general_item_transfer_controller', this.value, 'load_drop_down_store', 'store_td_from' );fn_to_store(this.value);
							?>
                            <input type="hidden" name="variable_string_inventory" id="variable_string_inventory" />
                            <input type="hidden" id="variable_lot" name="variable_lot" />
                        </td>
                        <td  class="must_entry_caption">To Company</td>
                        <td id="to_company_td">
                            <?
								//$company_credential_cond 
								echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "reset_on_change(this.id);load_drop_down('requires/general_item_transfer_controller', this.value, 'load_drop_down_store','store_td_to');to_company_on_change(this.value);",1 );
								//load_drop_down( 'requires/general_item_transfer_controller', this.value, 'load_drop_down_store_to', 'store_td' );
							?>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Requisition No</td>
                        <td id="td_requisition_no">
                            <input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:148px;" placeholder="Write" />
							<input type="hidden" id="txt_variable_status" name="txt_variable_status" value="" />
							<input type="hidden" name="hidden_req_id" id="hidden_req_id" />
                        </td>
                        <td class="must_entry_caption">Transfer Date</td>
                        <td>
                            <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" value="<? echo date("d-m-Y");?>"/>
                        </td>
                        <td>Challan No.</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
                        </td>
                    </tr>
                </table>
            </fieldset>
            <br>
            <table width="850" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
                <tr>
                    <td width="65%" valign="top">
                        <fieldset>
                        <legend>Item Info</legend>
                            <table id="tbl_item_info"  cellpadding="0" cellspacing="1" width="50%" style="float: left;">		
                                <!-- <tr>
                                	<td width="30%" class="must_entry_caption">From Store</td>
                                    <td id="store_td_from">
                                        <?
                                            //echo create_drop_down( "cbo_store_name", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type in (4,8,9,10,11,15,16,17,18,19,20,21,22,32,34,35,36,37,38,39) $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
                                        ?>	
                                    </td>
                                </tr>
                                <tr>	
                                	<td class="must_entry_caption">To Store</td>
                                    <td id="store_td">
                                   		<?
											//echo create_drop_down( "cbo_store_name_to", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and  b.category_type in (4,8,9,10,11,15,16,17,18,19,20,21,22,32,34,35,36,37,38,39) $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
										?>	
                                	</td>
                                </tr> -->
                                <tr>
                                <td class="must_entry_caption">Item Category</td>
			                        <td>
										<?
			                            	echo create_drop_down( "cbo_item_category", 160, $general_item_category,'', 1, '--Select Category--', '', '','0',$item_cate_credential_cond );
			                            ?>
			                        </td>
                        		</tr>						
                                <tr>
                                    <td class="must_entry_caption">Item Description</td>
                                    <td>
                                    	<input type="text" name="txt_item_desc" id="txt_item_desc" class="text_boxes" style="width:148px;" readonly placeholder="Double Click To Search" onDblClick="openmypage_itemDescription();" /></td>
                                </tr>
                                <tr style="display:none;">
                                    <td> Lot</td>						
                                    <td>                       
                                        <input type="text" name="txt_yarn_lot" id="txt_yarn_lot" class="text_boxes" style="width:148px" disabled="disabled" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Transfered Qnty</td>
                                    <td>
                                    	<input type="text" name="txt_transfer_qnty" id="txt_transfer_qnty" class="text_boxes_numeric" style="width:148px;" onKeyUp="calculate_value( );" /></td>
                                </tr>
                                <tr>
                                    <td>Supplier</td>						
                                    <td>
                                    	<input type="text" name="txt_supplier" id="txt_supplier" class="text_boxes" style="width:148px" disabled="disabled" />
                                        <input type="hidden" name="hide_supplier_id" id="hide_supplier_id" disabled="disabled" />
                                    </td>
                                </tr>
                                <tr>    
                                    <td id="lot_caption">Lot</td>
                                    <td><input type="text" name="txt_lot" id="txt_lot" class="text_boxes" style="width:148px;" readonly disabled /></td> 
                                </tr>
								<tr>
									<td> Serial No</td>
									<td>
									<input name="txt_serial_no" id="txt_serial_no" class="text_boxes" type="text" style="width:148px;" />								
									</td>
								</tr>
							</table>
							<div style="float: right;">
								<fieldset>
	                        		<legend>From Store</legend>
	                        		<table>
	                        			 <tr>
		                                	<td width="100" class="must_entry_caption">From Store</td>
		                                    <td id="from_store_td">
		                                        <?
		                                           echo create_drop_down( "cbo_store_name", 152, $blank_array,"", 1, "--Select store--", 0, "" );
		                                        ?>	
		                                    </td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Floor</td>
											<td id="floor_td">
												<? echo create_drop_down( "cbo_floor", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Room</td>
											<td id="room_td">
												<? echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Rack</td>
											<td id="rack_td">
												<? echo create_drop_down( "txt_rack", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Shelf</td>
											<td id="shelf_td">
												<? echo create_drop_down( "txt_shelf", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Bin Box</td>
	                                        <td id="bin_td">
												<? echo create_drop_down( "cbo_bin", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
	                        		</table>
	                        	</fieldset>
	                        	<fieldset>
	                        		<legend>To Store</legend>
	                        		<table>
	                        			 <tr>
		                                	<td width="100" class="must_entry_caption">To Store</td>
		                                    <td id="store_td_to">
		                                        <?
		                                           echo create_drop_down( "cbo_store_name_to", 152, $blank_array,"", 1, "--Select store--", 0, "" );
		                                        ?>	
		                                    </td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Floor</td>
											<td id="to_floor_td">
												<? echo create_drop_down( "cbo_floor_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Room</td>
											<td id="to_room_td">
												<? echo create_drop_down( "cbo_room_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Rack</td>
											<td id="to_rack_td">
												<? echo create_drop_down( "txt_rack_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Shelf</td>
											<td id="to_shelf_td">
												<? echo create_drop_down( "txt_shelf_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Bin Box</td>
	                                        <td id="to_bin_td">
												<? echo create_drop_down( "cbo_bin_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
	                        		</table>
	                        	</fieldset>
                        	</div>
						</fieldset>
					</td>
					<td width="2%" valign="top"></td>
					<td width="40%" valign="top">
						<fieldset>
                        <legend>Display</legend>					
                            <table id="tbl_display_info"  cellpadding="0" cellspacing="1" width="100%" >				
                                <tr>
                                    <td>Current Stock</td>						
                                	<td>
                                    	<input type="text" name="txt_current_stock" id="txt_current_stock" class="text_boxes_numeric" style="width:150px" disabled />
                                    	<input type="hidden" name="hidden_current_stock" id="hidden_current_stock" readonly>
                                    </td>
								</tr>
                                <tr id="rate_td">
                                    <td>Avg. Rate</td>						
                                    <td><input type="text" name="txt_rate" id="txt_rate" class="text_boxes_numeric" style="width:150px" disabled /></td>
                                </tr>
                                <tr id="amount_td">
                                    <td>Transfer Value </td>
                                    <td><input type="text" name="txt_transfer_value" id="txt_transfer_value" class="text_boxes_numeric" style="width:150px" disabled /></td>
                                </tr>					
                                <tr>
                                    <td>UOM</td>
                                    <td>
                                    	<?
										//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
											echo create_drop_down( "cbo_uom", 160, $unit_of_measurement,'', 1, "Select", '', "",1);
											
										?>
                                    </td>
                                </tr>											
                            </table>                  
                       </fieldset>	
              		</td>
				</tr>	 	
                <tr>
                    <td align="center" colspan="3" class="button_container" width="100%">
                        <?
                            echo load_submit_buttons($permission, "fnc_yarn_transfer_entry", 0,1,"reset_form('transferEntry_1','div_transfer_item_list*div_item_requisition_listview','','','disable_enable_fields(\'cbo_company_id*txt_requisition_no*cbo_transfer_criteria\');active_inactive(0);')",1);
                        ?>
						<input type="button" style="width:100px;" name="print2" id="print2"  onClick="fnc_yarn_transfer_entry(2)" class="formbutton" value="Print 2" />

                        <input type="hidden" id="hidden_product_id" name="hidden_product_id" value="" >
                        <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                        <input type="hidden" name="update_trans_issue_id" id="update_trans_issue_id" readonly>
                        <input type="hidden" name="update_trans_recv_id" id="update_trans_recv_id" readonly>
                        <input type="hidden" name="previous_from_prod_id" id="previous_from_prod_id" readonly>
                        <input type="hidden" name="previous_to_prod_id" id="previous_to_prod_id" readonly>
                        <input type="hidden" name="hidden_transfer_qnty" id="hidden_transfer_qnty" readonly>
                        <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                        <input type="hidden" name="store_update_upto" id="store_update_upto" readonly>
                        <input type="hidden" name="store_update_upto_to" id="store_update_upto_to" readonly>
						<input type="hidden" name="hidden_req_dtls_id" id="hidden_req_dtls_id" />
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center">
                        <div id="accounting_posted_status" style=" color:red; font-size:24px;";  ></div>
                    </td>
                </tr>
            </table>
            <div style="width:850px;" id="div_transfer_item_list"></div>
		</fieldset>
	</div>
	<div style="width:420px; margin-left:15px;float: left;position: relative;" id="div_item_requisition_listview" align="left"></div>
	</form>
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

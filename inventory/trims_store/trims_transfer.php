<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Transfer Entry
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	27-10-2016
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

$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id,item_cate_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Trims Transfer Info","../../", 1, 1, '','',''); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function active_inactive(str)
{
	$('#cbo_company_id_to').val(0);
	if(str==1)
	{
		$('#cbo_company_id_to').removeAttr('disabled','disabled');
		//cbo_currency
		$('#cbo_currency').val(1);	
		//$('#cbo_location_to').removeAttr('disabled','disabled');
	}
	else
	{
		$('#cbo_company_id_to').attr('disabled','disabled');
		$('#cbo_currency').val(2);	
		//$('#cbo_location_to').val('0').attr('disabled','disabled');
	}
}

function openmypage_systemId()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
	if(cbo_transfer_criteria==1)
	{
		if (form_validation('cbo_transfer_criteria*cbo_company_id*cbo_company_id_to','Criteria*From Company*To Company')==false)
		{
			return;
		}
	}
	else
	{
		if (form_validation('cbo_transfer_criteria*cbo_company_id','Criteria*From Company*To Company')==false)
		{
			return;
		}
	}
	
	
	var title = 'Item Transfer Info';	
	var page_link = 'requires/trims_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=itemTransfer_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		
		reset_form('transferEntry_1','div_transfer_item_list','','');
		
		var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
		$("#is_posted_account").val(posted_in_account);
		if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
		else 					 document.getElementById("accounting_posted_status").innerHTML="";
		
		get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/trims_transfer_controller" );
		show_list_view(transfer_id,'show_transfer_listview','div_transfer_item_list','requires/trims_transfer_controller','');
		set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
	}
}

function openmypage_itemDescription()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_store_name = $('#cbo_store_name').val();
	var cbo_item_category = $('#cbo_item_category').val();
	var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
	if(cbo_transfer_criteria==0)
	{
		alert("Please Select Transfer Criteria.");return;
	}
	else
	{
		if(cbo_transfer_criteria==1)
		{
			if (form_validation('cbo_transfer_criteria*cbo_company_id*cbo_item_category','Transfer Criteria*Company* Item Category')==false)
			{
				return;
			}
			
			var title = 'Item Description Info';	
			var page_link = 'requires/trims_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_store_name='+cbo_store_name+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=itemDescription_com_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=450px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
				var product_id=this.contentDoc.getElementById("product_id").value; //Access form field with id="emailfield"
				
				var floor_id=this.contentDoc.getElementById("floor_id").value;
				var room=this.contentDoc.getElementById("room").value;
				var rack=this.contentDoc.getElementById("rack").value;
				var shelf=this.contentDoc.getElementById("shelf").value;
				var bin=this.contentDoc.getElementById("bin").value;
				// alert(product_id+'='+floor_id+'='+room+'='+rack+'='+shelf+'='+bin);
				reset_form('','','txt_style_no*txt_order_no*job_id*hidden_product_id*txt_item_desc*txt_transfer_qnty*txt_current_stock*hidden_current_stock*txt_rate*cbo_supplier*cbo_uom','','','');
				$('#cbo_company_id').attr('disabled',true);
				$('#cbo_item_category').attr('disabled',true);
				$('#cbo_transfer_criteria').attr('disabled',true);
				var job_id="";var order_no="";
				get_php_form_data(product_id+"**"+job_id+"**"+order_no+"**"+cbo_store_name+"**"+cbo_transfer_criteria, "populate_data_from_product_master", "requires/trims_transfer_controller" );
			}
		}
		else
		{
			if (form_validation('cbo_transfer_criteria*cbo_company_id*cbo_store_name*cbo_item_category','Transfer Criteria*Company*From Store* Item Category')==false)
			{
				return;
			}
			var title = 'Item Description Info';	
			var page_link = 'requires/trims_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_store_name='+cbo_store_name+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=itemDescription_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=370px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
				var product_id=this.contentDoc.getElementById("product_id").value; //Access form field with id="emailfield"
				var style_no=this.contentDoc.getElementById("txt_style_no").value;
				var job_id=this.contentDoc.getElementById("job_id").value;
				
				var floor_id=this.contentDoc.getElementById("floor_id").value;
				var room=this.contentDoc.getElementById("room").value;
				var rack=this.contentDoc.getElementById("rack").value;
				var shelf=this.contentDoc.getElementById("shelf").value;
				var bin=this.contentDoc.getElementById("bin").value;

				var order_no=this.contentDoc.getElementById("txt_order_no").value;
				var po_id=this.contentDoc.getElementById("po_id").value;
				// alert(product_id+'='+job_id+'='+floor_id+'='+room+'='+rack+'='+shelf+'='+bin);
				reset_form('','','txt_style_no*txt_order_no*job_id*hidden_product_id*txt_item_desc*txt_transfer_qnty*txt_current_stock*hidden_current_stock*txt_rate*cbo_supplier*cbo_uom','','','');
				//$('#txt_style_no').val(style_no);
				$('#txt_order_no').val(order_no);
				//$('#job_id').val(job_id);
				
				$('#cbo_company_id').attr('disabled',true);
				$('#cbo_store_name').attr('disabled',true);
				$('#cbo_item_category').attr('disabled',true);
				
				get_php_form_data(product_id+"**"+job_id+"**"+order_no+"**"+cbo_store_name+"**"+cbo_transfer_criteria+"**"+floor_id+"**"+room+"**"+rack+"**"+shelf+"**"+bin+"**"+po_id, "populate_data_from_product_master", "requires/trims_transfer_controller" );
				$('#cbo_floor').attr('disabled','disabled');
				$('#cbo_room').attr('disabled','disabled');
				$('#txt_rack').attr('disabled','disabled');
				$('#txt_shelf').attr('disabled','disabled');
				$('#cbo_bin').attr('disabled','disabled');
			}
		}
	}
	
	
}

function calculate_value()
{
	var txt_transfer_qnty = $('#txt_transfer_qnty').val()*1;
	var txt_rate = $('#txt_rate').val()*1;
	
	var transfer_value=txt_transfer_qnty*txt_rate;
	$('#txt_transfer_value').val(transfer_value.toFixed(4));
}
 
function fnc_yarn_transfer_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "trims_transfer_print", "requires/trims_transfer_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if ($("#is_posted_account").val()*1 == 1) {
			alert("Already Posted In Accounting. Save Update Delete Restricted.");
			return;
		}
		/*if(operation==2)
		{
			show_msg('13');
			return;
		}*/
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
			alert("Transfer Date Can not Be Greater Than Current Date");
			return;
		}
		
		if($("#cbo_transfer_criteria").val()==1)
		{
			var from_company=$('#cbo_company_id').val()*1;
			var to_company=$('#cbo_company_id_to').val()*1;
			if(from_company==to_company)
			{
				alert("Transfer Not Allow In The Same Company.");
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
			if($("#cbo_store_name_to").val()==0)
			{
				alert("Please Select To Store.");
				$("#cbo_store_name_to").focus();
				return;
			}
			var from_store=$('#cbo_store_name').val();
			var to_store=$('#cbo_store_name_to').val();
			
			if(from_store==to_store)
			{
				alert("Transfer Not Allow In The Same Store.");
				return;
			}
		}
		
		var txt_transfer_qnty=$('#txt_transfer_qnty').val()*1;
		var txt_current_stock=$('#txt_current_stock').val()*1;
		if(txt_transfer_qnty>txt_current_stock)
		{
			alert("Transfer Quantity Not Allow Over Balance Quantity.");
			return;
		}
		
		if( form_validation('cbo_transfer_criteria*cbo_company_id*txt_transfer_date*cbo_store_name*txt_item_desc*txt_transfer_qnty*cbo_item_category','Transfer Criteria*Company*Transfer Date*From Store*Item Description*Transfered Qnty*Item Category')==false )
		{
			return;
		}	

		// Store upto validation start
		var store_update_upto=$('#store_update_upto').val()*1;
		var cbo_floor=$('#cbo_floor').val()*1;
		var cbo_room=$('#cbo_room').val()*1;
		var txt_rack=$('#txt_rack').val()*1;
		var txt_shelf=$('#txt_shelf').val()*1;
		var cbo_bin=$('#cbo_bin').val()*1;

		var cbo_floor_to=$('#cbo_floor_to').val()*1;
		var cbo_room_to=$('#cbo_room_to').val()*1;
		var txt_rack_to=$('#txt_rack_to').val()*1;
		var txt_shelf_to=$('#txt_shelf_to').val()*1;
		var cbo_bin_to=$('#cbo_bin_to').val()*1;
		if(store_update_upto > 1)
		{
			if(store_update_upto==6 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0 || cbo_bin==0))
			{
				alert("Up To Bin Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==5 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==4 && (cbo_floor==0 || cbo_room==0 || txt_rack==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==3 && (cbo_floor==0 || cbo_room==0))
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==2 && cbo_floor==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
			// ===============================================================================
			if(store_update_upto==6 && (cbo_floor_to==0 || cbo_room_to==0 || txt_rack_to==0 || txt_shelf_to==0 || cbo_bin_to==0))
			{
				alert("Up To Bin Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==5 && (cbo_floor_to==0 || cbo_room_to==0 || txt_rack_to==0 || txt_shelf_to==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==4 && (cbo_floor_to==0 || cbo_room_to==0 || txt_rack_to==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==3 && cbo_floor_to==0 || cbo_room_to==0)
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==2 && cbo_floor_to==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
		}
		// Store upto validation End
		
		
		
		var dataString = "txt_system_id*cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_transfer_date*txt_challan_no*cbo_item_category*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to*cbo_bin_to*txt_transfer_qnty*txt_rate*txt_transfer_value*cbo_uom*update_id*hidden_product_id*update_dtls_id*update_trans_issue_id*update_trans_recv_id*previous_from_prod_id*previous_to_prod_id*hidden_transfer_qnty*txt_item_desc*txt_style_no*txt_order_no*job_id*txt_remarks*cbo_location*cbo_location_to*store_update_upto*txt_order_id";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		
		// alert(data);return;
		//alert(dataString);
		freeze_window(operation);
		http.open("POST","requires/trims_transfer_controller.php",true);
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
		if(reponse[0]*1==35*1)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		else if(reponse[0]*1==17*1)
		{
			alert(reponse[1]);
			release_freezing(); 
			return;
		}
		show_msg(reponse[0]); 
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			$("#update_id").val(reponse[1]);
			$("#txt_system_id").val(reponse[2]);
			$('#cbo_company_id').attr('disabled','disabled');
			
			reset_form('transferEntry_1','','','','','update_id*txt_system_id*cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_transfer_date*txt_challan_no*cbo_item_category*cbo_currency*txt_remarks*store_update_upto*cbo_location*cbo_location_to*cbo_store_name*cbo_store_name_to*is_posted_account*store_update_upto');
			$('#cbo_location').attr('disabled',true);
			$('#cbo_location_to').attr('disabled',true);
			$('#cbo_store_name').attr('disabled',true);
			$('#cbo_store_name_to').attr('disabled',true);
			$('#cbo_item_category').attr('disabled',false);
			show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/trims_transfer_controller','');
			set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
			
		}	
		release_freezing();
	}
}

function fn_to_store(company_id)
{
	/*var transfer_criteria=$('#cbo_transfer_criteria').val();
	
	if(transfer_criteria==2)
	{
		load_drop_down( 'requires/trims_transfer_controller', company_id, 'load_drop_down_store_to', 'store_td' );
	}*/
}
function fn_store_wise_location_from(store_id)
{
	var from_company=$('#cbo_company_id').val()*1;
	get_php_form_data(store_id + "**" + from_company, "populate_data_location_from", "requires/trims_transfer_controller");	
}
function fn_store_wise_location_to(store_id)
{
	var from_company=$('#cbo_company_id').val()*1;
	get_php_form_data(store_id + "**" + from_company, "populate_data_location_to", "requires/trims_transfer_controller");	
}
function fn_test(str)
{
	alert();
}
function fnc_get_from_com(fromComp){
	$transfer_criteria=$('#cbo_transfer_criteria').val();
	if ($transfer_criteria==2) {
		$('#cbo_company_id_to').val(fromComp);
		load_drop_down( 'requires/trims_transfer_controller',fromComp, 'load_drop_down_location_to', 'to_location_td' );
	}

	var data='cbo_company_id='+fromComp+'&action=upto_variable_settings';   
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) 
		{
            document.getElementById("store_update_upto").value = this.responseText;
        }
    }
    xmlhttp.open("POST", "requires/trims_transfer_controller.php", true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(data);
}

// ==============End Floor Room Rack Shelf Bin upto disable============
function storeUpdateUptoDisable() 
{
	var store_update_upto=$('#store_update_upto').val()*1;	
	if(store_update_upto==5)
	{
		$('#cbo_bin_to').prop("disabled", true);
	}
	if(store_update_upto==4)
	{
		$('#txt_shelf_to').prop("disabled", true);
		$('#cbo_bin_to').prop("disabled", true);
	}
	else if(store_update_upto==3)
	{
		$('#txt_rack_to').prop("disabled", true);
		$('#txt_shelf_to').prop("disabled", true);
		$('#cbo_bin_to').prop("disabled", true);
	}
	else if(store_update_upto==2)
	{	
		$('#cbo_room_to').prop("disabled", true);
		$('#txt_rack_to').prop("disabled", true);
		$('#txt_shelf_to').prop("disabled", true);	
		$('#cbo_bin_to').prop("disabled", true);
	}
	else if(store_update_upto==1)
	{
		$('#cbo_floor_to').prop("disabled", true);
		$('#cbo_room_to').prop("disabled", true);
		$('#txt_rack_to').prop("disabled", true);
		$('#txt_shelf_to').prop("disabled", true);	
		$('#cbo_bin_to').prop("disabled", true);	
	}
}
// ==============End Floor Room Rack Shelf Bin upto disable============

	function independence_basis_controll_function(data)
	{
	    /*var independent_control_arr = JSON.parse('<? //echo json_encode($independent_control_arr); ?>');
	    $("#cbo_receive_basis").val(0);
	    $("#cbo_receive_basis option[value='4']").show();
	    if(independent_control_arr[data]==1)
	    {
	        $("#cbo_receive_basis option[value='4']").hide();
	    }*/
		
		var varible_string=return_global_ajax_value( data, 'varible_inventory', '', 'requires/trims_transfer_controller');
		
		var varible_string_ref=varible_string.split("**");
		//alert(varible_string_ref[0]);
		if(varible_string_ref[0])
		{
			$('#variable_string_inventory').val(varible_string_ref[1]+"**"+varible_string_ref[2]+"**"+varible_string_ref[3]+"**"+varible_string_ref[4]);
			/*if(varible_string_ref[1]==1)
			{
				$("#cbo_receive_basis option[value='4']").hide();
			}
			else
			{
				$("#cbo_receive_basis option[value='4']").show();
			}*/
			$('#is_rate_optional').val(varible_string_ref[2]);
			/*if(varible_string_ref[4]==2)
			{
				$('#txt_rate').attr("readonly",true);
			}
			else
			{
				$('#txt_rate').attr("readonly",false);
			}*/
			
			if(varible_string_ref[3]==1)
			{
				$('#rate_td').css("display", "none");
				$('#amount_td').css("display", "none");
				//$('#book_currency_td').css("display", "none");
			}
			else
			{
				$('#rate_td').css("display", "");
				$('#amount_td').css("display", "");
				//$('#book_currency_td').css("display", "");
			}
			
		}
		else
		{
			$('#variable_string_inventory').val("");
			//$("#cbo_receive_basis option[value='4']").show();
			$('#is_rate_optional').val("");
			//$('#txt_rate').attr("readonly",false);
			$('#rate_td').css("display", "");
			$('#amount_td').css("display", "");
			//$('#book_currency_td').css("display", "");
		}
		
		
		//alert(varible_string);return;

	    // ==============Start Floor Room Rack Shelf Bin upto variable Settings============
		
		//$('#store_update_upto').val(varible_string_ref[5]);
		//$('#variable_lot').val(varible_string_ref[6]);
		
	    /*var data='cbo_company_id='+data+'&action=upto_variable_settings';
	    var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() 
	    {
	        if (this.readyState == 4 && this.status == 200) {
	            document.getElementById("store_update_upto").value = this.responseText;				
	        }
	    }
	    xmlhttp.open("POST", "requires/general_item_receive_controller.php", true);
	    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	    xmlhttp.send(data);*/
	    // ==============End Floor Room Rack Shelf Bin upto variable Settings============
	}
</script>

</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
    <div style="width:100%;">   
        <fieldset style="width:1000px;">        	
        <legend>General Item Transfer Entry</legend>
        <br>
        	<fieldset style="width:900px;">
                <table width="880" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
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
                                echo create_drop_down("cbo_transfer_criteria", 160,$item_transfer_criteria,"", 1,"-- Select --",'0',"active_inactive(this.value);",'','2');
                            ?>
                        </td>
                        <td class="must_entry_caption">Company</td>
                        <td>
                            <? 
								echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "if (form_validation('cbo_transfer_criteria','cbo_transfer_criteria')==false) return;load_drop_down( 'requires/trims_transfer_controller',this.value, 'load_drop_down_location', 'from_location_td' );fnc_get_from_com(this.value);independence_basis_controll_function(this.value);" );
									//load_drop_down( 'requires/trims_transfer_controller', this.value, 'load_drop_down_store', 'store_td_from' );
							?>
							<input type="hidden" name="variable_string_inventory" id="variable_string_inventory" />
	                        <input type="hidden" id="is_rate_optional" name="is_rate_optional">
	                        <input type="hidden" id="variable_lot" name="variable_lot" />
                            <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                        	<input type="hidden" name="store_update_upto" id="store_update_upto">
                        </td>
                        <td>To Company</td>
                        <td>
						<? 
                            echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "if($('#cbo_company_id').val()*1 == this.value){alert('Same Company Transfer is not allowed!!'); $('#cbo_company_id_to').val('0'); return;}; load_drop_down( 'requires/trims_transfer_controller',this.value, 'load_drop_down_location_to', 'to_location_td' );",1 );
                            //load_drop_down( 'requires/trims_transfer_controller', this.value, 'load_drop_down_store_to', 'store_td' )
                        ?>
                        </td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Transfer Date</td>
                        <td>
                            <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" value="<? echo date('d-m-Y');?>" />
                        </td>
                         <td width="100" class="must_entry_caption">Location</td>
                        <td id="from_location_td">
                            <?
                               echo create_drop_down( "cbo_location", 160, $blank_array,"", 1, "--Select store--", 0, "" );
                            ?>	
                        </td>
                        <td width="100" class="">To Location</td>
                        <td id="to_location_td">
                            <?
                               echo create_drop_down( "cbo_location_to", 160, $blank_array,"", 1, "--Select store--", 0, "",0 );
                            ?>	
                        </td>
                       
                        
                    </tr>
                    <tr>
                    	<td>Challan No.</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
                        </td>
                    </tr>
                </table>
            </fieldset>
            <br>
            <table width="910" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
                <tr>
                    <td width="65%" valign="top">
                        <fieldset>
                        <legend>Item Info</legend>
                            <table id="tbl_item_info"  cellpadding="0" cellspacing="1" width="50%" style="float: left;">										
                               <!--  <tr>
                                	<td width="30%" class="must_entry_caption">From Store</td>
                                    <td id="store_td_from" width="30%">
                                        <?
                                           // echo create_drop_down( "cbo_store_name", 160, $blank_array,"", 1, "--Select store--", 0, "get_php_form_data(this.value, 'get_from_store_location', 'requires/trims_transfer_controller' );" );
                                        ?>
                                    </td>
                                    <td><input type="text" name="from_store_location" id="from_store_location" class="text_boxes" style="width:150px" disabled="disabled" /></td>
                                </tr>
                                <tr>	
                                	<td >To Store</td>
                                    <td id="store_td">
                                   		<?
											//echo create_drop_down( "cbo_store_name_to", 160, $blank_array,"", 1, "--Select store--", 0, "get_php_form_data(this.value, 'get_to_store_location', 'requires/trims_transfer_controller' );" );
										?>
                                	</td>
                                    <td><input type="text" name="to_store_location" id="to_store_location" class="text_boxes" style="width:150px" disabled="disabled" /></td>
                                </tr> -->
                                <tr>
                                <td class="must_entry_caption">Item Category</td>
		                        <td colspan="2">
									<?
									//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
									
		                            	echo create_drop_down( "cbo_item_category", 160, $item_category,'', 1, '--Select Category--', 0, '','0','4');
		                            ?>
		                        </td>
		                        </tr>						
		                        <tr>
		                            <td class="must_entry_caption">Item Description</td>
		                            <td colspan="2">
		                                <input type="text" name="txt_item_desc" id="txt_item_desc" class="text_boxes" style="width:150px;" readonly placeholder="Double Click To Search" onDblClick="openmypage_itemDescription();" /></td>
		                        </tr>
		                        <tr>
		                            <td>UOM</td>
		                            <td colspan="2">
		                                <?
											
		                                    echo create_drop_down( "cbo_uom", 160, $unit_of_measurement,'', 1, "Select UOM", '', "",1,"" );
		                                ?>
		                            </td>
		                        </tr>
		                        <tr style="display:none;">
		                            <td> Lot</td>						
		                            <td colspan="2">                       
		                                <input type="text" name="txt_yarn_lot" id="txt_yarn_lot" class="text_boxes" style="width:150px" disabled="disabled" />
		                            </td>
		                        </tr>
		                        <tr>
		                            <td class="must_entry_caption">Transfered Qnty</td>
		                            <td colspan="2">
		                                <input type="text" name="txt_transfer_qnty" id="txt_transfer_qnty" class="text_boxes_numeric" style="width:150px;" onKeyUp="calculate_value( );" /></td>
		                        </tr>
		                        <tr>
		                            <td>Remarks</td>
		                            <td colspan="2">
		                                <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:150px;" /></td>
		                        </tr>
		                        <tr style="display:none;">
		                            <td>Supplier</td>						
		                            <td colspan="2">
		                                <?
		                                //function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
		                                echo create_drop_down( "cbo_supplier", 160, "select a.id, a.supplier_name from lib_supplier a where a.status_active=1 and a.is_deleted=0 ","id,store_name", 1, "--Select supplier--", 0, "",1);
		                                ?>
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
                               			  <!-- <tr>
		                                	<td width="100">Location</td>
		                                    <td>
		                                       <input type="text" name="from_store_location" id="from_store_location" class="text_boxes" style="width:140px" disabled="disabled" />	
		                                    </td>
                               			 </tr> -->
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
                               			 	<td>Box/Bin</td>
											<td id="bin_td">
												<? echo create_drop_down( "cbo_bin", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td> 
                               			 </tr>
	                        		</table>
	                        	</fieldset>
	                        	<fieldset>
	                        		<legend>To Store</legend>
	                        		<table>
	                        			 <tr>
		                                	<td width="100" class="must_entry_caption">To Store</td>
		                                    <td id="to_store_td">
		                                        <?
		                                           echo create_drop_down( "cbo_store_name_to", 152, $blank_array,"", 1, "--Select store--", 0, "" );
		                                        ?>	
		                                    </td>
                               			 </tr>
                               			 <!-- <tr>
                               			 	<td>Location</td>
                               			 	<td><input type="text" name="to_store_location" id="to_store_location" class="text_boxes" style="width:140px" disabled="disabled" /></td>
                               			 </tr> -->
                               			 <tr>
                               			 	<td>Floor</td>
											<td id="floor_td_to">
												<? echo create_drop_down( "cbo_floor_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Room</td>
											<td id="room_td_to">
												<? echo create_drop_down( "cbo_room_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Rack</td>
											<td id="rack_td_to">
												<? echo create_drop_down( "txt_rack_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Shelf</td>
											<td id="shelf_td_to">
												<? echo create_drop_down( "txt_shelf_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Box/Bin</td>
											<td id="bin_td_to">
												<? echo create_drop_down( "cbo_bin_to", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
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
                                <td>Style No</td>						
                                <td>
                                    <input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes_numeric" style="width:150px" disabled />
                                </td>
                            </tr>
                            <tr>
                                <td>Order No</td>						
                                <td>
                                    <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes_numeric" style="width:150px" disabled />
                                    <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes_numeric" style="width:150px" disabled />
                                    <input type="hidden" name="job_id" id="job_id" class="text_boxes_numeric" style="width:150px" disabled />
                                </td>
                            </tr>
                            <tr>
                                <td>Current Stock</td>						
                                <td>
                                    <input type="text" name="txt_current_stock" id="txt_current_stock" class="text_boxes_numeric" style="width:150px" disabled />
                                    <input type="hidden" name="hidden_current_stock" id="hidden_current_stock" readonly>
                                </td>
                            </tr>
                            <tr id="rate_td">
                                <td>Ord. Avg. Rate</td>						
                                <td><input type="text" name="txt_rate" id="txt_rate" class="text_boxes_numeric" style="width:82px" disabled />
                                <?
                                    echo create_drop_down( "cbo_currency", 65, $currency,'', 1, "Select Currency", 0, "",1,"1,2" );
                                ?>
                                </td>
                            </tr>
                            <tr id="amount_td">
                                <td>Transfer Value </td>
                                <td><input type="text" name="txt_transfer_value" id="txt_transfer_value" class="text_boxes_numeric" style="width:150px" disabled /></td>
                            </tr>					
                        </table>                  
                   </fieldset>	
                </td>
				</tr>	 	
                <tr>
                    <td align="center" colspan="3" class="button_container" width="100%">
                        <?
                            echo load_submit_buttons($permission, "fnc_yarn_transfer_entry", 0,1,"reset_form('transferEntry_1','div_transfer_item_list','','','disable_enable_fields(\'cbo_company_id\');active_inactive(0);')",1);
                        ?>
                        <input type="hidden" id="hidden_product_id" name="hidden_product_id" value="" >
                        <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                        <input type="hidden" name="update_trans_issue_id" id="update_trans_issue_id" readonly>
                        <input type="hidden" name="update_trans_recv_id" id="update_trans_recv_id" readonly>
                        <input type="hidden" name="previous_from_prod_id" id="previous_from_prod_id" readonly>
                        <input type="hidden" name="previous_to_prod_id" id="previous_to_prod_id" readonly>
                        <input type="hidden" name="hidden_transfer_qnty" id="hidden_transfer_qnty" readonly>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center">
                        <div id="accounting_posted_status" style=" color:red; font-size:24px;";  ></div>
                    </td>
                </tr>
            </table>
            <div style="width:880px;" id="div_transfer_item_list"></div>
		</fieldset>
	</div>
	</form>
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

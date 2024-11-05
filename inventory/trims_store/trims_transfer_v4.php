<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Transfer Entry
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	20-11-2023
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
echo load_html_head_contents("Trims Transfer Info","../../", 1, 1, '','',''); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function openmypage_systemId()
{
	var cbo_company_id = $('#cbo_company_id').val();
    var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	
	var title = 'Item Transfer Info';	
	var page_link = 'requires/trims_transfer_v4_controller.php?cbo_company_id='+cbo_company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=orderToorderTransfer_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=835px,height=400px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		
		reset_form('transferEntry_1','','','');
		var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
		$("#is_posted_account").val(posted_in_account);
		if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
		else 					 document.getElementById("accounting_posted_status").innerHTML="";
        load_drop_down( 'requires/trims_transfer_v4_controller', '', 'load_drop_down_to_company', 'cbo_company_id_to' );
        get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/trims_transfer_v4_controller" );
		//alert(2+"="+$("#txt_from_order_id").val()+"="+$("#cbo_store_name").val()+"="+$("#cbo_floor").val()+"="+$("#cbo_room").val()+"="+$("#txt_rack").val());
		
		//var order_id=$("#txt_from_order_id").val();
		var style_order_data_ref = return_global_ajax_value(transfer_id, 'style_order_transf_data', '', 'requires/trims_transfer_v4_controller').split("*");
		$("#txt_from_style_ref").val(style_order_data_ref[1]);
		$("#cbo_from_buyer_name").val(style_order_data_ref[2]);
		if(style_order_data_ref[0]!="")
		{
			$("#cbo_store_name").val();
			var mst_id=$("#update_id").val();
			var cbo_store_name=$("#cbo_store_name").val();
			var cbo_floor=$("#cbo_floor").val();
			var cbo_room=$("#cbo_room").val();
			var txt_rack=$("#txt_rack").val();
			var txt_shelf=$("#txt_shelf").val();
			var cbo_bin=$("#cbo_bin").val();
			var store_update_upto=$("#store_update_upto").val();
			
			//alert(order_id+"__"+cbo_store_name+"__"+cbo_floor+"__"+cbo_room+"__"+txt_rack+"__"+txt_shelf+"__"+cbo_bin+"__"+store_update_upto+"__"+reponse[2]);
			//reset_form('','','update_dtls_id*cbo_item_desc*txt_transfer_qnty*update_trans_issue_id*update_trans_recv_id','','','');
			show_list_view(style_order_data_ref[0]+"__"+cbo_store_name+"__"+cbo_floor+"__"+cbo_room+"__"+txt_rack+"__"+txt_shelf+"__"+cbo_bin+"__"+store_update_upto+"__"+mst_id,'show_dtls_list_view_update','body_dtls_part','requires/trims_transfer_v4_controller','setFilterGrid(\'tbl_list_search\',-1);');
		
		}
		disable_enable_fields( 'cbo_company_id*cbo_store_name*txt_from_order_no*cbo_store_name_to*txt_to_order_no', 1, '', '' );
		set_button_status(1, permission, 'fnc_trims_transfer_entry',1,1);
	}
}

function openmypage_orderNo(type)
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_store_name = $('#cbo_store_name').val();
	var cbo_company_id_to = $('#cbo_company_id_to').val();
	var cbo_store_name_to = $('#cbo_store_name_to').val();
	var store_update_upto = $('#store_update_upto').val();
	
	var cbo_floor = $('#cbo_floor').val();
	var cbo_room = $('#cbo_room').val();
	var txt_rack = $('#txt_rack').val();
	var txt_shelf = $('#txt_shelf').val();
	var cbo_bin = $('#cbo_bin').val();
	
	var cbo_floor_to = $('#cbo_floor_to').val();
	var cbo_room_to = $('#cbo_room_to').val();
	var txt_rack_to = $('#txt_rack_to').val();
	var txt_shelf_to = $('#txt_shelf_to').val();
	var cbo_bin_to = $('#cbo_bin_to').val();
	
	if(type=='from')
	{
		if(store_update_upto==6)
		{
			if (form_validation('cbo_company_id*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*cbo_company_id_to*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to*cbo_bin_to','Company*From Store*Floor*Room*Rack*Shelf*Bin*Company To*To Store *Floor To*Room To*Rack To*Shelf To*Bin To')==false)
			{
				return;
			}
		}
		else if(store_update_upto==5)
		{
			if (form_validation('cbo_company_id*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_company_id_to*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to','Company*From Store*Floor*Room*Rack*Shelf*cbo_company_id_to*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to')==false)
			{
				return;
			}
		}
		else if(store_update_upto==4)
		{
			if (form_validation('cbo_company_id*cbo_store_name*cbo_floor*cbo_room*txt_rack*cbo_company_id_to*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to','Company*From Store*Floor*Room*Rack*Company To*To Store*To Floor*To Room*To Rack')==false)
			{
				return;
			}
		}
		else if(store_update_upto==3)
		{
			if (form_validation('cbo_company_id*cbo_store_name*cbo_floor*cbo_room*cbo_company_id_to*cbo_store_name_to*cbo_floor_to*cbo_room_to','Company*From Store*Floor*Room*To Company*To Store*To Floor*To Room')==false)
			{
				return;
			}
		}
		else if(store_update_upto==2)
		{
			if (form_validation('cbo_company_id*cbo_store_name*cbo_floor*cbo_company_id_to*cbo_store_name_to*cbo_floor_to','Company*From Store*Floor*To Company*To Store*To Floor')==false)
			{
				return;
			}
		}
		else
		{
			if (form_validation('cbo_company_id*cbo_store_name*cbo_company_id_to*cbo_store_name_to','Company*From Store*To Company*To Store')==false)
			{
				return;
			}
		}
		
	}
	
	
	var title = 'Order Info';	
	var page_link = 'requires/trims_transfer_v4_controller.php?cbo_company_id='+cbo_company_id+'&cbo_store_name='+cbo_store_name+'&type='+type+'&cbo_company_id_to='+cbo_company_id_to+'&cbo_floor='+cbo_floor+'&cbo_room='+cbo_room+'&txt_rack='+txt_rack+'&txt_shelf='+txt_shelf+'&cbo_bin='+cbo_bin+'&store_update_upto='+store_update_upto+'&action=order_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=380px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var order_id=this.contentDoc.getElementById("hdn_order_id").value; //Access form field with id="emailfield"
		var hdn_style_ref_no=this.contentDoc.getElementById("hdn_style_ref_no").value;
		var hdn_buyer_id=this.contentDoc.getElementById("hdn_buyer_id").value;
		var transfer_criteria = $("#cbo_transfer_criteria").val();
		if(type=='from')
		{
			$('#txt_from_style_ref').val(hdn_style_ref_no);
			$('#cbo_from_buyer_name').val(hdn_buyer_id);
			//alert(order_id);return;
			show_list_view(order_id+"__"+cbo_store_name+"__"+cbo_floor+"__"+cbo_room+"__"+txt_rack+"__"+txt_shelf+"__"+cbo_bin+"__"+store_update_upto,'show_dtls_list_view','body_dtls_part','requires/trims_transfer_v4_controller','setFilterGrid(\'tbl_list_search\',-1);');
			$("#cbo_store_name").attr("disabled",true);
			storeUpdateUptoDisable();
		}
	}
}

function fnc_trims_transfer_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_company_id_to').val(), "trims_store_order_to_order_transfer_print", "requires/trims_transfer_v4_controller" ) 
		return;
	}
	else if(operation==5)
	{
		if ($('#update_id').val() == ''){
			alert("Please Save Master Part First");
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_company_id_to').val(), "trims_store_order_to_order_transfer_print2", "requires/trims_transfer_v4_controller");
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if(operation==2)
		{
			alert("Delete Not Allow");return;
		}
		
		if ($("#is_posted_account").val()*1 == 1) {
			alert("Already Posted In Accounting. Save Update Delete Restricted.");
			return;
		}
		
		if( form_validation('cbo_company_id*cbo_company_id_to*txt_transfer_date*cbo_store_name*cbo_store_name_to*txt_from_style_ref','Company*To Company*Transfer Date*From Store*To Store*Style No')==false )
		{
			return;
		}
		var transfer_criteria=$('#cbo_transfer_criteria').val();
		if(transfer_criteria==1 || transfer_criteria==2){
			if( form_validation('cbo_store_name_to','To Store')==false )
			{
				return;
			}
		}
        
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
				alert("Transfer Date Can not Be Greater Than Current Date");
				return;
		}
		
		// Store upto validation start
		var store_update_upto=$('#store_update_upto').val()*1;
		var cbo_floor=$('#cbo_floor').val()*1;
		var cbo_room=$('#cbo_room').val()*1;
		var txt_rack=$('#txt_rack').val()*1;
		var txt_shelf=$('#txt_shelf').val()*1;
		var cbo_bin=$('#cbo_bin').val()*1;

		var store_update_upto_to=$('#store_update_upto_to').val()*1;
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
		}
		// ===============================================================================
		if(store_update_upto_to > 1)
		{
			if(store_update_upto_to==6 && (cbo_floor_to==0 || cbo_room_to==0 || txt_rack_to==0 || txt_shelf_to==0 || cbo_bin_to==0))
			{
				alert("Up To Bin Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto_to==5 && (cbo_floor_to==0 || cbo_room_to==0 || txt_rack_to==0 || txt_shelf_to==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto_to==4 && (cbo_floor_to==0 || cbo_room_to==0 || txt_rack_to==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto_to==3 && cbo_floor_to==0 || cbo_room_to==0)
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto_to==2 && cbo_floor_to==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
		}
		// Store upto validation End
		var j=0; var i=1; var dataString='';
		$("#tbl_item_info").find('tbody tr').each(function()
		{
			var prod_id=$('#tdProdID_'+i).attr('title');
			var po_id=$('#tdOrderID_'+i).attr('title');
			var itemGroupId=$('#tdItemGroup_'+i).attr('title');
			var itemDescription=$('#tdItemDescription_'+i).attr('title');
			var brandSupp=$('#tdBrandSupp_'+i).attr('title');
			var itemColorId=$('#tdItemColor_'+i).attr('title');
			var itemSize=$('#tdItemSize_'+i).attr('title');
			var gmtsColorId=$('#tdGmtsColor_'+i).attr('title');
			var gmtsSizeId=$('#tdGmtsSize_'+i).attr('title');			
			
			var uom=$('#tdUom_'+i).attr('title');
			var txtTransferQnty=$('#txtTransferQnty_'+i).val()*1;
			var txtRate=$('#txtRate_'+i).val();
			var txtOrdRate=$('#txtOrdRate_'+i).val();
			var txtRemarks=$('#txtRemarks_'+i).val();
			var updateDtlsId=$('#updateDtlsId_'+i).val();
			var updateTransIssueId=$('#updateTransIssueId_'+i).val();
			var updateTransRecvId=$('#updateTransRecvId_'+i).val();
			
			if(txtTransferQnty>0)	
			{
				j++;
				dataString+='&prod_id' + j + '=' + prod_id + '&po_id' + j + '=' + po_id + '&itemGroupId' + j + '=' + itemGroupId + '&itemDescription' + j + '=' + itemDescription + '&brandSupp' + j + '=' + brandSupp + '&itemColorId' + j + '=' + itemColorId + '&itemSize' + j + '=' + itemSize + '&gmtsColorId' + j + '=' + gmtsColorId + '&gmtsSizeId' + j + '=' + gmtsSizeId + '&uom' + j + '=' + uom + '&txtTransferQnty' + j + '=' + txtTransferQnty + '&txtRate' + j + '=' + txtRate + '&txtOrdRate' + j + '=' + txtOrdRate + '&txtRemarks' + j + '=' + txtRemarks+ '&updateDtlsId' + j + '=' + updateDtlsId + '&updateTransIssueId' + j + '=' + updateTransIssueId  + '&updateTransRecvId' + j + '=' + updateTransRecvId;
			
			}
			i++;
		});
		
		if(j<1)
		{
			alert("NO Data");return;
		}
		
		var mst_data = "txt_system_id*cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_transfer_date*cbo_location*cbo_location_to*txt_challan_no*txt_from_order_id*txt_to_order_id*update_id*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to*cbo_bin_to*store_update_upto*store_update_upto_to";
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string(mst_data,"../../")+dataString;;
		
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/trims_transfer_v4_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_trims_transfer_entry_reponse;
	}
}

function fnc_trims_transfer_entry_reponse()
{	
	if(http.readyState == 4) 
	{	  		
		var reponse=trim(http.responseText).split('**');		
		//alert(http.responseText);
                
		if (reponse[0] * 1 == 20 * 1) 
		{
			 alert(reponse[1]);
			 release_freezing();
			 return;
		} 
		show_msg(reponse[0]); 	
		if(reponse[0]==11)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			$("#update_id").val(reponse[1]);
			$("#txt_system_id").val(reponse[2]);
			
			var order_id=$("#txt_from_order_id").val();
			var cbo_store_name=$("#cbo_store_name").val();
			var cbo_floor=$("#cbo_floor").val();
			var cbo_room=$("#cbo_room").val();
			var txt_rack=$("#txt_rack").val();
			var txt_shelf=$("#txt_shelf").val();
			var cbo_bin=$("#cbo_bin").val();
			var store_update_upto=$("#store_update_upto").val();
			
			//reset_form('','','update_dtls_id*cbo_item_desc*txt_transfer_qnty*update_trans_issue_id*update_trans_recv_id','','','');
			show_list_view(order_id+"__"+cbo_store_name+"__"+cbo_floor+"__"+cbo_room+"__"+txt_rack+"__"+txt_shelf+"__"+cbo_bin+"__"+store_update_upto+"__"+reponse[1],'show_dtls_list_view_update','body_dtls_part','requires/trims_transfer_v4_controller','setFilterGrid(\'tbl_list_search\',-1);');
			disable_enable_fields( 'cbo_transfer_criteria*cbo_company_id*cbo_location*cbo_location_to*cbo_store_name*txt_from_style_ref*cbo_store_name_to', 1, '', '' );
			set_button_status(1, permission, 'fnc_trims_transfer_entry',1,1);
		}	
		release_freezing();
	}
}




function active_inactive(str)
{
	if(str==2) // Store to Store
	{
		$('#cbo_company_id_to').attr('disabled','disabled');	
		$('#txt_to_order_no').attr('disabled','disabled');	
	}
	else if(str==4) // Order to Order
	{
		$('#cbo_company_id_to').attr('disabled','disabled');
		$('#txt_to_order_no').removeAttr('disabled','disabled');
	}
	else
	{
		$('#cbo_company_id_to').removeAttr('disabled','disabled');
		$('#txt_to_order_no').removeAttr('disabled','disabled');
    }
    load_drop_down( 'requires/trims_transfer_v4_controller', '', 'load_drop_down_to_company', 'cbo_company_id_to' );

    $('#cbo_company_id').val(0);
	$('#cbo_company_id_to').val(0);
	$('#cbo_location').val(0);
	$('#cbo_store_name').val(0);
	$('#cbo_location_to').val(0);
	$('#cbo_store_name_to').val(0);

	$('#txt_to_order_no').val('');
	$('#txt_to_order_id').val('');
	$('#txt_to_po_qnty').val('');
	$('#cbo_to_buyer_name').val('');
	$('#txt_to_style_ref').val('');
	$('#txt_to_job_no').val('');
	$('#txt_to_gmts_item').val('');
	$('#txt_to_shipment_date').val('');

	$('#txt_from_order_no').val('');
	$('#txt_from_order_id').val('');
	$('#txt_from_po_qnty').val('');
	$('#cbo_from_buyer_name').val('');
	$('#txt_from_style_ref').val('');
	$('#txt_from_job_no').val('');
	$('#txt_from_gmts_item').val('');
	$('#txt_from_shipment_date').val('');	
}

function company_on_change(fromComp)
{
	var transfer_criteria=$('#cbo_transfer_criteria').val();
	if (transfer_criteria!=1) {
		$('#cbo_company_id_to').val(fromComp);
		load_drop_down( 'requires/trims_transfer_v4_controller',fromComp, 'load_drop_down_location_to', 'to_location_td' );
	}
	
    if(transfer_criteria == 1){
        load_drop_down( 'requires/trims_transfer_v4_controller',fromComp, 'load_drop_down_to_company_not_selected', 'cbo_company_id_to' );
    }
	
    var data='cbo_company_id='+fromComp+'&action=upto_variable_settings';    
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("store_update_upto").value = this.responseText;	
            $('#store_update_upto_to').val(this.responseText);		
        }
    }
    xmlhttp.open("POST", "requires/trims_transfer_v4_controller.php", true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(data);

    //to_company_on_change(fromComp);
}

function to_company_on_change(to_company)
{
	// ==============Start Floor Room Rack Shelf Bin upto variable Settings============
    // if($('#cbo_company_id').val() != '' || $('#cbo_company_id').val() != 0){
    //     $transfer_criteria=$('#cbo_transfer_criteria').val();
    //     if($transfer_criteria == 1){
    //         load_drop_down( 'requires/trims_transfer_v4_controller',fromComp, 'load_drop_down_from_company_not_selected', 'cbo_company_id' );
    //     }
    // }
    var data='cbo_company_id='+to_company+'&action=upto_variable_settings';
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("store_update_upto_to").value = this.responseText;				
        }
    }
    xmlhttp.open("POST", "requires/trims_transfer_v4_controller.php", true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(data);
    // ==============End Floor Room Rack Shelf Bin upto variable Settings============
}

// ==============End Floor Room Rack Shelf Bin upto disable============
function storeUpdateUptoDisable() 
{
	var store_update_upto_to=$('#store_update_upto_to').val()*1;	
	$('#cbo_store_name').prop("disabled", true);	
	$('#cbo_floor').prop("disabled", true);
	$('#cbo_room').prop("disabled", true);
	$('#txt_rack').prop("disabled", true);
	$('#txt_shelf').prop("disabled", true);	
	$('#cbo_bin').prop("disabled", true);
	
	$('#cbo_store_name_to').prop("disabled", true);
	$('#cbo_floor_to').prop("disabled", true);
	$('#cbo_room_to').prop("disabled", true);
	$('#txt_rack_to').prop("disabled", true);
	$('#txt_shelf_to').prop("disabled", true);	
	$('#cbo_bin_to').prop("disabled", true);
}


function fn_stock_check(row_num)
{
	var stock_qnty=$("#txtCurrentStock_"+row_num).val()*1;
	var trans_qnty=$("#txtTransferQnty_"+row_num).val()*1;
	if(trans_qnty>stock_qnty)
	{
		alert("Transfer Quantity Not Allow Over Stock Quantity");
		$("#txtTransferQnty_"+row_num).val("").focus();return;
	}
}

</script>

</head>

<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
        <div style="width:1260px; float:left" align="center">   
            <fieldset style="width:1060px;">
            <legend>Trims Transfer Entry</legend>
            <br>
            <fieldset style="width:1250px;">
                <table width="1240" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>Transfer System ID</strong></td>
                        <td colspan="3" align="left">
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                            <input type="hidden" name="update_id" id="update_id" />
                            <input type="hidden" name="is_posted_account" id="is_posted_account" readonly>
                            <input type="hidden" name="store_update_upto" id="store_update_upto">
                            <input type="hidden" name="store_update_upto_to" id="store_update_upto_to" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Transfer Criteria</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_transfer_criteria", 160,$item_transfer_criteria,"", 1,"-- Select --",'0',"active_inactive(this.value);",'','2');
                            ?>
                        </td>
                        <td class="must_entry_caption">From Company</td>
                        <td>
                            <? 
								echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "if (form_validation('cbo_transfer_criteria','Transfer Criteria')==false) return;load_drop_down( 'requires/trims_transfer_v4_controller',this.value, 'load_drop_down_location', 'from_location_td' );company_on_change(this.value);" );
									//load_drop_down( 'requires/trims_transfer_v4_controller', this.value, 'load_drop_down_store', 'store_td_from' );
							?>
                        </td>
                        <td>To Company</td>
                        <td>
							<? 
                            echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "if($('#cbo_company_id').val()*1 == this.value){alert('Same Company Transfer is not allowed!!'); $('#cbo_company_id_to').val('0'); return;}; load_drop_down( 'requires/trims_transfer_v4_controller',this.value, 'load_drop_down_location_to', 'to_location_td' );to_company_on_change(this.value);",1 );
                            //load_drop_down( 'requires/trims_transfer_v4_controller', this.value, 'load_drop_down_store_to', 'store_td' )
                        	?>
                        </td>
                        <td class="must_entry_caption">Transfer Date</td>
                        <td>
                            <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" value="<? echo date('d-m-Y');?>" />
                        </td>
                        <td>Challan No.</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
                        </td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Location</td>
                        <td id="from_location_td">
                            <?
                               echo create_drop_down( "cbo_location", 160, $blank_array,"", 1, "--Select store--", 0, "" );
                            ?>	
                        </td>
                        <td class="">To Location</td>
                        <td id="to_location_td">
                            <?
                               echo create_drop_down( "cbo_location_to", 160, $blank_array,"", 1, "--Select store--", 0, "",0 );
                            ?>	
                        </td>
                    </tr>
                </table>

            </fieldset>
            <br>
                <table width="1250" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
                    <tr>
                        <td width="49%" valign="top">
                            <fieldset>
                            <legend>From Store</legend>
                                <table id="from_order_info"  cellpadding="0" cellspacing="1" width="100%">										
                                     <tr>
	                                	<td width="90" class="must_entry_caption">From Store</td>
	                                    <td id="from_store_td" width="160">
	                                        <?
	                                           echo create_drop_down( "cbo_store_name", 152, $blank_array,"", 1, "--Select store--", 0, "" );
	                                        ?>	
	                                    </td>
                                        <td width="90">Floor</td>
										<td id="floor_td">
											<? echo create_drop_down( "cbo_floor", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                                        
                           			 </tr>
                           			 <tr>
                                     	<td>Room</td>
                                     	<td id="room_td">
											<? echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
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
                                        <td>Bin</td>
										<td id="bin_td">
											<? echo create_drop_down( "cbo_bin", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                           			 </tr>
                                    <tr style="display:none">
                                        <td>Order No</td>
                                        <td>
                                            <input type="text" name="txt_from_order_no" id="txt_from_order_no" class="text_boxes" style="width:140px;" placeholder="Double click to search" />
                                            <input type="hidden" name="txt_from_order_id" id="txt_from_order_id" readonly>
                                        </td>
                                        <td>Order Qnty</td>
                                        <td>
                                            <input type="text" name="txt_from_po_qnty" id="txt_from_po_qnty" class="text_boxes" style="width:140px;" disabled="disabled" placeholder="Display" />
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td class="must_entry_caption">Style Ref.</td>
                                        <td>
                                            <input type="text" name="txt_from_style_ref" id="txt_from_style_ref" class="text_boxes" style="width:140px;" onDblClick="openmypage_orderNo('from');" placeholder="Double click to search" readonly />
                                        </td>
                                    	<td>Buyer</td>
                                        <td>
                                             <? 
                                                echo create_drop_down( "cbo_from_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);   
                                            ?>	  	
                                        </td>
                                        
                                    </tr>
                                    <tr style="display:none">
                                        <td>Job No</td>						
                                        <td>                       
                                            <input type="text" name="txt_from_job_no" id="txt_from_job_no" class="text_boxes" style="width:140px" disabled="disabled" placeholder="Display" />
                                        </td>
                                        <td>Gmts Item</td>
                                        <td>
                                            <input type="text" name="txt_from_gmts_item" id="txt_from_gmts_item" class="text_boxes" style="width:140px;" disabled="disabled" placeholder="Display" />
                                        </td>
                                    </tr>
                                    <tr style="display:none">
                                        <td>Shipment Date</td>						
                                        <td>
                                            <input type="text" name="txt_from_shipment_date" id="txt_from_shipment_date" class="datepicker" style="width:140px" disabled="disabled" placeholder="Display" />
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td width="2%" valign="top"></td>
                        <td width="49%" valign="top">
                            <fieldset>
                            <legend>To Store</legend>					
                                <table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >				
                                    <tr>
	                                	<td width="90" class="must_entry_caption">To Store</td>
	                                    <td width="160" id="to_store_td">
	                                        <?
	                                           echo create_drop_down( "cbo_store_name_to", 152, $blank_array,"", 1, "--Select store--", 0, "" );
	                                        ?>	
	                                    </td>
                                        <td width="90">Floor</td>
										<td id="floor_td_to">
											<? echo create_drop_down( "cbo_floor_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                           			 </tr>
                           			 <tr>
                           			 	<td>Room</td>
										<td id="room_td_to">
											<? echo create_drop_down( "cbo_room_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
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
                                        <td>Bin</td>
										<td id="bin_td_to">
											<? echo create_drop_down( "cbo_bin_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                           			 </tr>
                                    <tr style="display:none">
                                        <td class="must_entry_caption">Order No</td>
                                        <td>
                                            <input type="text" name="txt_to_order_no" id="txt_to_order_no" class="text_boxes" style="width:140px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('to');" readonly />
                                            <input type="hidden" name="txt_to_order_id" id="txt_to_order_id" readonly>
                                        </td>
                                        <td>Order Qnty</td>
                                        <td>
                                            <input type="text" name="txt_to_po_qnty" id="txt_to_po_qnty" class="text_boxes" style="width:140px;" disabled="disabled" placeholder="Display" />
                                        </td>
                                    </tr>
                                    <tr style="display:none">	
                                        <td>Buyer</td>
                                        <td>
                                             <? 
                                                echo create_drop_down( "cbo_to_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);   
                                            ?>	  	
                                        </td>
                                        <td>Style Ref.</td>
                                        <td>
                                            <input type="text" name="txt_to_style_ref" id="txt_to_style_ref" class="text_boxes" style="width:140px;" disabled="disabled" placeholder="Display" />
                                        </td>
                                    </tr>						
                                    <tr style="display:none">
                                        <td>Job No</td>						
                                        <td>                       
                                            <input type="text" name="txt_to_job_no" id="txt_to_job_no" class="text_boxes" style="width:140px" disabled="disabled" placeholder="Display"/>
                                        </td>
                                        <td>Gmts Item</td>
                                        <td>
                                            <input type="text" name="txt_to_gmts_item" id="txt_to_gmts_item" class="text_boxes" style="width:140px;" disabled="disabled" placeholder="Display" />
                                        </td>
                                    </tr>
                                    <tr style="display:none">
                                        <td>Shipment Date</td>						
                                        <td>
                                            <input type="text" name="txt_to_shipment_date" id="txt_to_shipment_date" class="datepicker" style="width:140px" disabled="disabled" placeholder="Display" />
                                        </td>
                                    </tr>											
                                </table>                  
                           </fieldset>	
                        </td>
                    </tr>	
                    <tr>
                        <td colspan="3">
                            <fieldset style="margin-top:10px">
                            <legend>Item Info</legend>
                                <table id="tbl_item_info" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="2" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="30">SL</th>
                                            <th width="50">Prod Id</th>
                                            <th width="120">Order</th>
                                            <th width="130">Item Group</th>
                                            <th width="130">Item Description</th>
                                            <th width="80">Brand/ Sup Ref</th>
                                            <th width="100">Item Color</th>
                                            <th width="80">Item Size</th>
                                            <th width="100">Gmts Color</th>
                                            <th width="80">Gmts Size</th>
                                            <th width="60">UOM</th>
                                            <th width="85">Current Stock</th>
                                            <th width="85">Transfered Qnty</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody id="body_dtls_part">
                                    	<tr>
                                        	<td id="tdSl_1"></td>
                                            <td id="tdOrderID_1"></td>
                                            <td id="tdProdID_1"></td>
                                            <td id="tdItemGroup_1"></td>
                                            <td id="tdItemDescription_1"></td>
                                            <td id="tdBrandSupp_1"></td>
                                            <td id="tdItemColor_1"></td>
                                            <td id="tdItemSize_1"></td>
                                            <td id="tdGmtsColor_1"></td>
                                            <td id="tdGmtsSize_1"></td>
                                            <td id="tdUom_1"></td>
                                            <td id="tdCurrentStock_1" align="center"><input type="text" name="txtCurrentStock[]" id="txtCurrentStock_1" class="text_boxes" style="width:70px" disabled="disabled"/></td>
                                            <td id="tdTransferQnty_1" align="center">
                                            <input type="text" name="txtTransferQnty[]" id="txtTransferQnty_1" class="text_boxes_numeric" style="width:70px;" />
                                            <input type="hidden" name="txtRate[]" id="txtRate_1"/>
                                            </td>
                                            <td id="tdRemarks_1" align="center">
                                            <input type="text" name="txtRemarks[]" id="txtRemarks_1" class="text_boxes" style="width:80px;" />
                                            <input type="hidden" name="updateDtlsId[]" id="updateDtlsId_1" readonly>
                                            <input type="hidden" name="updateTransIssueId[]" id="updateTransIssueId_1" readonly>
                                            <input type="hidden" name="updateTransRecvId[]" id="updateTransRecvId_1" readonly>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </fieldset>
                        </td>
                    </tr> 	
                    <tr>
                        <td align="center" colspan="3" class="button_container" width="100%">
                            <?
                                echo load_submit_buttons($permission, "fnc_trims_transfer_entry", 0,1,"reset_form('transferEntry_1','','','','disable_enable_fields(\'cbo_company_id\');')",1);
                            ?>
                            <input type="button" id="print2" class="formbutton" style="width: 80px;"  onClick="fnc_trims_transfer_entry(5)" name="print2" value="Print 2">
                            
                        </td>
                    </tr>
                    <tr>
                    <td colspan="3" align="center">
                        <div id="accounting_posted_status" style=" color:red; font-size:24px;";  ></div>
                    </td>
                </tr>
                </table>
            </fieldset>
        </div>
	</form>
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

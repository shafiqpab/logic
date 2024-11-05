<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Woven Finish Fabric Receive
				
Functionality	:	
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	07-05-2013
Updated by 		: 	Kausar (Creating Report), Didar (Add roll maintend feature)
Update date		: 	14-12-2013,01-16-2018	   
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
echo load_html_head_contents("Woven Fabric Receive Info","../../", 1, 1, $unicode,1,1); 

?>	

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color where status_active=1 group by color_name", "color_name" ), 0, -1); ?>];

	$(function() {	
		$("#txt_color").autocomplete({
			source: str_color
		});
	});


	function rcv_basis_reset()
	{
		document.getElementById('cbo_receive_basis').value=0;
	} 
	
	
// popup for WO/PI----------------------	
function openmypage(page_link,title)
{
	if( form_validation('cbo_company_id*cbo_receive_basis','Company Name*Receive Basis')==false )
	{
		return;
	}
	
	var company = $("#cbo_company_id").val();
	var receive_basis = $("#cbo_receive_basis").val();

	page_link='requires/woven_finish_fabric_receive_controller.php?action=wopi_popup&company='+company+'&receive_basis='+receive_basis;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1210px, height=400px, center=1, resize=0, scrolling=0','../')
	emailwindow.onclose=function()
	{
			var theform=this.contentDoc.forms[0];
			var rowID=this.contentDoc.getElementById("hidden_tbl_id").value; // wo/pi table id
			var wopiNumber=this.contentDoc.getElementById("hidden_wopi_number").value; // wo/pi number
			var fabric_source=this.contentDoc.getElementById("hidden_fabric_source").value; // wo/pi number 
			var hidden_is_non_ord_sample=this.contentDoc.getElementById("hidden_is_non_ord_sample").value; // wo/pi number 
			var hidden_basis = this.contentDoc.getElementById("hidden_basis").value;

			var chk_approve_status = this.contentDoc.getElementById("chkApproveStatus").value;

			if(chk_approve_status==1)
			{
				alert("Booking is not approved yet");
				return ; 
			}

			if (rowID!="")
			{
				freeze_window(5);
				$('#txt_wo_pi').val('');
				$('#save_data').val('');
				$('#txt_receive_qty').val('');
				$('#all_po_id').val('');
				$('#distribution_method_id').val('');
				$('#txt_deleted_id').val('');
				
				$('#txt_fabric_description').val('');
				$('#original_fabric_description').val('');
				
				$('#fabric_desc_id').val('');
				$('#txt_rate').val('');
				$('#txt_color').val('');
				$('#txt_width').val('');
				$("#txt_wo_pi").val(wopiNumber);
				$("#txt_wo_pi_id").val(rowID);
				if (hidden_is_non_ord_sample==1)  // without order 1
				{
					//alert(hidden_is_non_ord_sample);
					$('#txt_roll').attr('disabled',false);
					// $('#txt_wo_pi').removeAttr('disabled','disabled');
				}
				else{ // with order 0
					//alert(hidden_is_non_ord_sample);
					$('#txt_roll').attr('disabled','disabled');
				}
				// alert(hidden_is_non_ord_sample);

				$("#booking_without_order").val(hidden_is_non_ord_sample);
				$("#fabric_source").val(fabric_source);
				
				get_php_form_data(receive_basis+"**"+rowID+"**"+hidden_is_non_ord_sample+"**"+wopiNumber+"**"+hidden_basis, "populate_data_from_wopi_popup", "requires/woven_finish_fabric_receive_controller" );
				
				show_list_view(receive_basis+"**"+rowID+"**"+hidden_is_non_ord_sample+"**"+wopiNumber,'show_product_listview','list_product_container','requires/woven_finish_fabric_receive_controller','');
				
				var currency_id=$('#cbo_currency').val()*1;
				exchange_rate(currency_id);
				
				release_freezing();	 
			}
		}		
	}

// enable disable field for independent
function fn_independent(val)
{
	$('#txt_wo_pi').val('');
	$('#save_data').val('');
	$('#txt_receive_qty').val('');
	$('#all_po_id').val('');
	$('#distribution_method_id').val('');
	$('#txt_deleted_id').val('');
	
	$('#txt_fabric_description').val('');
	$('#original_fabric_description').val('');
	
	$('#fabric_desc_id').val('');
	$('#txt_rate').val('');
	$('#txt_color').val('');
	$('#txt_width').val('');
	$('#txt_wo_pi').val('');
	$('#txt_wo_pi_id').val('');
	$('#txt_bla_order_qty').val('');
	$('#cbo_supplier').val(0);
	reset_form('yarn_receive_1','list_product_container','','','','cbo_company_id*cbo_receive_basis*txt_receive_date*roll_maintained*barcode_generation*cbouom');
	//$('#list_product_container').text('');
	
	if(val==1)
	{
		$("#txt_lc_no").attr("disabled",true);
		$('#txt_lc_no').attr('placeholder','Display');
		$("#cbo_currency").attr("disabled",true);
		$("#cbo_source").attr("disabled",true);
		$("#txt_wo_pi").attr("disabled",false);
		$('#txt_wo_pi').removeAttr('placeholder','No Need');
		$('#txt_wo_pi').attr('placeholder','Double Click');
		$('#txt_fabric_description').attr('disabled','disabled');
		$('#txt_color').attr('disabled','disabled');
		$('#cbo_supplier').attr('disabled','disabled');
		$('#txt_rate').attr('disabled','disabled');
		$("#cbo_body_part").attr("disabled",false);
		$("#cbo_buyer_name").attr("disabled",true);
	}
	if(val==2)
	{
		$("#txt_lc_no").attr("disabled",true);
		$('#txt_lc_no').removeAttr('placeholder','Display');
		$("#cbo_currency").attr("disabled",true);
		$("#cbo_source").attr("disabled",true);
		$("#txt_wo_pi").attr("disabled",false);
		$('#txt_wo_pi').removeAttr('placeholder','No Need');
		$('#txt_wo_pi').attr('placeholder','Double Click');
		$('#txt_fabric_description').attr('disabled','disabled');
		$('#txt_color').attr('disabled','disabled');
		$('#cbo_supplier').attr('disabled','disabled');
		$('#txt_rate').attr('disabled','disabled');
		$('#cbo_body_part').attr('disabled','disabled');
		$("#cbo_buyer_name").attr("disabled",true);
		
	}
	
	if(val==4)
	{
		$("#txt_lc_no").attr("disabled",true);
		$('#txt_lc_no').removeAttr('placeholder','Display');
		$("#cbo_currency").attr("disabled",false);
		//$('#txt_exchange_rate').removeAttr('disabled','disabled');
		//$('#txt_exchange_rate').attr('disabled','disabled');
		$("#cbo_source").attr("disabled",false);
		$("#txt_wo_pi").attr("disabled",true);
		$('#txt_wo_pi').attr('placeholder','No Need');
		$('#txt_fabric_description').removeAttr('disabled','disabled');
		$('#txt_fabric_description').attr('readonly','readonly');
		$('#txt_color').removeAttr('disabled','disabled');
		$('#cbo_supplier').removeAttr('disabled','disabled');
		$('#txt_rate').removeAttr('disabled','disabled');
		$("#cbo_body_part").attr("disabled",false);
		$("#cbo_buyer_name").attr("disabled",false);
	}
	if(val==6)
	{
		$("#txt_lc_no").removeAttr("disabled",true);
		$('#txt_lc_no').attr('placeholder','Click');
		$("#cbo_currency").attr("disabled",false);
		//$('#txt_exchange_rate').removeAttr('disabled','disabled');
		//$('#txt_exchange_rate').attr('disabled','disabled');
		$("#cbo_source").attr("disabled",false);
		$("#txt_wo_pi").attr("disabled",true);
		$('#txt_wo_pi').attr('placeholder','No Need');
		$('#txt_fabric_description').removeAttr('disabled','disabled');
		$('#txt_fabric_description').attr('readonly','readonly');
		$('#txt_color').removeAttr('disabled','disabled');
		$('#cbo_supplier').removeAttr('disabled','disabled');
		$('#txt_rate').removeAttr('disabled','disabled');
		//$('#txt_exchange_rate').attr('disabled','disabled');
		$("#cbo_body_part").attr("disabled",false);
		$("#cbo_buyer_name").attr("disabled",false);
	}
}

/*function set_exchange_rate(currency_id)
{
	if(currency_id==1)
	{
		$('#txt_exchange_rate').val(1);
		$('#txt_exchange_rate').attr('disabled','disabled');
	}
	if(currency_id!=1)
	{
		var response=return_global_ajax_value( currency_id, 'set_exchange_rate', '', 'requires/woven_finish_fabric_receive_controller');
		$('#txt_exchange_rate').val(response);
		//$('#txt_exchange_rate').removeAttr('disabled','disabled');
		$('#txt_exchange_rate').attr('disabled','disabled');
	}
}*/

function exchange_rate(currency_id)
{
	var company_id=$("#cbo_company_id").val();
	if(currency_id==1)
	{
		$("#txt_exchange_rate").val(1);
		//$("#txt_exchange_rate").attr("disabled",true);
	}
	else
	{
		var recv_date = $('#txt_receive_date').val();
		var response=return_global_ajax_value( currency_id+"**"+recv_date+"**"+company_id, 'check_conversion_rate', '', 'requires/woven_finish_fabric_receive_controller').split("_");
		$('#txt_exchange_rate').val(response[1]);
		//$("#txt_exchange_rate").attr("disabled",false);
	}
}


// LC pop up script here-----------------------------------
function popuppage_lc()
{
	
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();	
	var page_link='requires/woven_finish_fabric_receive_controller.php?action=lc_popup&company='+company; 
	var title="Search LC Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=0,scrolling=0','../ ')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var rowID=this.contentDoc.getElementById("hidden_tbl_id").value; // lc table id
		var wopiNumber=this.contentDoc.getElementById("hidden_wopi_number").value; // lc number
		$("#txt_lc_no").val(wopiNumber);
		$("#hidden_lc_id").val(rowID);		  
	}

}


// calculate ILE ---------------------------
function fn_calile()
{
	
	var company=$('#cbo_company_id').val()*1;	
	var source=$('#cbo_source').val()*1;	
	var rate=$('#txt_rate').val()*1;
	//*txt_rate*Rate
	if( form_validation('cbo_company_id*cbo_source','Company Name*Source')==false )
	{
		return;
	}
	else
	{
		var responseHtml = return_ajax_request_value(company+'**'+source+'**'+rate, 'show_ile', 'requires/woven_finish_fabric_receive_controller');
		var splitResponse="";
		if(responseHtml!="")
		{
			splitResponse = responseHtml.split("**");
			$("#ile_td").html('ILE% '+splitResponse[0]);
			$("#txt_ile").val(splitResponse[1]);
		}
		else
		{
			$("#ile_td").html('ILE% 0');
			$("#txt_ile").val(0);
		}
		
		//amount and book currency calculate--------------//
		var quantity 		= $("#txt_receive_qty").val();
		var exchangeRate 	= $("#txt_exchange_rate").val();
		var ile_cost 		= $("#txt_ile").val();
		var amount = quantity*1*(rate*1+ile_cost*1); 
		var bookCurrency = (rate*1+ile_cost*1)*exchangeRate*1*quantity*1;
		$("#txt_amount").val(number_format_common(amount,"","",1));
		$("#txt_book_currency").val(number_format_common(bookCurrency,"","",1));
	}
}


function fn_room_rack_self_box()
{ 
	if( $("#cbo_room").val()*1 > 0 )  
		disable_enable_fields( 'txt_rack', 0, '', '' ); 
	else
	{
		reset_form('','','txt_rack*txt_shelf*cbo_bin','','','');
		disable_enable_fields( 'txt_rack*txt_shelf*cbo_bin', 1, '', '' ); 
	}
	if( $("#txt_rack").val()*1 > 0 )  
		disable_enable_fields( 'txt_shelf', 0, '', '' ); 
	else
	{
		reset_form('','','txt_shelf*cbo_bin','','','');
		disable_enable_fields( 'txt_shelf*cbo_bin', 1, '', '' ); 	
	}
	if( $("#txt_shelf").val()*1 > 0 )  
		disable_enable_fields( 'cbo_bin', 0, '', '' ); 
	else
	{
		reset_form('','','cbo_bin','','','');
		disable_enable_fields( 'cbo_bin', 1, '', '' ); 	
	}
}


function fn_comp_new(val)
{	
	
	if(document.getElementById(val).value=='N') // when new(N) button click
	{											
		load_drop_down( 'requires/woven_finish_fabric_receive_controller', 1, 'load_drop_down_composition', 'composition_td' );		 		
	}
	else // When F button click
	{			
		load_drop_down( 'requires/woven_finish_fabric_receive_controller', 2, 'load_drop_down_composition', 'composition_td' );
	}

}



function fn_color_new(val)
{
	if(document.getElementById(val).value=='N') // when new(N) button click
	{											
		document.getElementById('color_td_id').innerHTML=' <input type="text" name="cbo_color" id="cbo_color" class="text_boxes" style="width:100px" /><input type="button" class="formbutton" name="btn_color" id="btn_color" width="15" onClick="fn_color_new(this.id)" value="F" />';	
	}
	else // When F button click
	{		
		load_drop_down( 'requires/woven_finish_fabric_receive_controller', '', 'load_drop_down_color', 'color_td_id' );
	}
}


function fnc_woben_finish_fab_receive_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "gwoven_finish_fabric_receive_print", "requires/woven_finish_fabric_receive_controller" ) 
		return;
	}
	else if(operation==2)
	{
		show_msg('13');
		return;
	}
	else
	{
		var fabric_source = $("#fabric_source").val();
		if(fabric_source == 3){
			if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_challan_no*cbo_store_name*cbo_supplier*cbo_currency*txt_exchange_rate*cbo_source*txt_fabric_description*txt_color*txt_batch_lot*txt_receive_qty*cbo_body_part*txt_roll','Company Name*Receive Basis*Receive Date*Challan No*Store Name*Supplier*Currency*Exchange Rate*Source*Fabric Desc*Color*Batch/Lot*Receive Quantity*Body Part*Roll')==false )
			{
				return;
			}
		}else{
			if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_challan_no*cbo_store_name*cbo_supplier*cbo_currency*txt_exchange_rate*cbo_source*txt_fabric_description*txt_color*txt_batch_lot*txt_receive_qty*txt_rate*cbo_body_part*txt_roll','Company Name*Receive Basis*Receive Date*Challan No*Store Name*Supplier*Currency*Exchange Rate*Source*Fabric Desc*Color*Batch/Lot*Receive Quantity*Rate*Body Part*Roll')==false )
			{
				return;
			}
		}

		
		
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_receive_date').val(), current_date)==false)
		{
			alert("Receive Date Can not Be Greater Than Current Date");
			return;
		}
		else if( $("#txt_rate").val()=="" || $("#txt_rate").val()==0)
		{
			if(fabric_source != 3){
				$("#txt_rate").val('');
				form_validation('txt_rate','Rate');
				return;
			}
			
		}

		var dataString = "txt_mrr_no*update_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_challan_no*cbo_location*cbo_store_name*txt_lc_no*hidden_lc_id*cbo_supplier*cbo_currency*txt_exchange_rate*cbo_source*txt_wo_pi*txt_wo_pi_id*booking_without_order*txt_fabric_description*original_fabric_description*fabric_desc_id*txt_color*txt_width*txt_weight*txt_width_edit*txt_weight_edit*txt_batch_lot*cbouom*txt_receive_qty*txt_rate*txt_ile*txt_amount*txt_book_currency*txt_bla_order_qty*txt_prod_code*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*save_data*update_dtls_id*update_trans_id*previous_prod_id*hidden_receive_qnty*all_po_id*roll_maintained*distribution_method_id*txt_deleted_id*txt_roll*txt_remarks*cbo_dyeing_source*cbo_cutting_unit_no*cbo_body_part*hidden_batch_id*update_finish_fabric_id*fabric_source*cbo_buyer_name*hdn_booking_no*hdn_booking_id*txt_fabric_ref*txt_rd_no*cbo_weight_type*txt_cutable_width";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		//alert(data);
		freeze_window(operation);
		http.open("POST","requires/woven_finish_fabric_receive_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_woben_finish_fab_receive_entry_reponse;
	}
}

function fnc_woben_finish_fab_receive_entry_reponse()
{	
	if(http.readyState == 4) 
	{
		//release_freezing();	
		 //alert(http.responseText);return;
		 var reponse=trim(http.responseText).split('**');	
		 show_msg(trim(reponse[0]));

		 if((reponse[0]==0 || reponse[0]==1))
		 {
		 	document.getElementById('update_id').value = reponse[1];
		 	document.getElementById('txt_mrr_no').value = reponse[2];
		 	$('#cbo_company_id').attr('disabled','disabled');
		 	$('#txt_receive_date').attr('disabled','disabled');
		 	$('#cbo_receive_basis').attr('disabled','disabled');
		 	$('#txt_wo_pi').attr('disabled','disabled');

		 	show_list_view(reponse[2]+'**'+reponse[1],'show_dtls_list_view','list_container_yarn','requires/woven_finish_fabric_receive_controller','');
		 	set_button_status(reponse[3], permission, 'fnc_woben_finish_fab_receive_entry',1,1);

		 	reset_form('','','txt_fabric_description*original_fabric_description*fabric_desc_id*txt_color*txt_width*txt_weight*txt_batch_lot*txt_receive_qty*txt_rate*txt_ile*txt_amount*txt_book_currency*txt_bla_order_qty*txt_prod_code*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*save_data*update_dtls_id*update_trans_id*previous_prod_id*hidden_receive_qnty*all_po_id*cbo_body_part*txt_roll*txt_remarks*hdn_booking_no*hdn_booking_id*txt_fabric_ref*txt_rd_no*cbo_weight_type*txt_cutable_width*update_finish_fabric_id*txt_weight_edit*txt_width_edit','','','');
		 }
		else if(reponse[0]==20)
		{
			alert(reponse[1]);
			show_msg(trim(reponse[0]));
			release_freezing();
			return;
		}
		 release_freezing();	
	}
}

	function open_mrrpopup()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var page_link='requires/woven_finish_fabric_receive_controller.php?action=mrr_popup&company='+company; 
		var title="Search MRR Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			reset_form('yarn_receive_1','list_product_container','','','','cbo_company_id*roll_maintained*barcode_generation');
			var theform=this.contentDoc.forms[0]; 
		var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value; // mrr number
		$("#txt_mrr_no").val(mrrNumber);
		// master part call here
		$("#tbl_master").find('input,select').attr("disabled", true);
		
		$("#btn_fileadd").prop("disabled", false);// new add 21-12-2020

		
		disable_enable_fields( 'txt_mrr_no', 0, "", "" );
		get_php_form_data(mrrNumber, "populate_data_from_data", "requires/woven_finish_fabric_receive_controller");	
		
		set_button_status(0, permission, 'fnc_woben_finish_fab_receive_entry',1,1);	
	}
}

function fnResetForm()
{
	$("#tbl_master").find('input').attr("disabled", false);	
	
	set_button_status(0, permission, 'fnc_woben_finish_fab_receive_entry',1);
	//reset_form('yarn_receive_1','list_container_yarn*list_product_container','','','','cbo_uom*cbo_currency*txt_exchange_rate*cbo_color');
	reset_form('','list_product_container*list_container_yarn','txt_mrr_no*update_id*txt_wo_pi*txt_wo_pi_id*booking_without_order*fabric_source*txt_challan_no*cbo_supplier*cbo_currency*txt_exchange_rate*cbo_source*cbo_dyeing_source*cbo_buyer_name*txt_fabric_description*original_fabric_description*fabric_desc_id*txt_color*txt_width*txt_weight*txt_width_edit*txt_weight_edit*txt_batch_lot*txt_receive_qty*txt_rate*txt_ile*txt_amount*txt_book_currency*txt_bla_order_qty*txt_prod_code*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*save_data*update_dtls_id*update_trans_id*previous_prod_id*hidden_receive_qnty*all_po_id*cbo_body_part*txt_roll*txt_remarks*hdn_booking_no*hdn_booking_id*update_finish_fabric_id','','','');
	disable_enable_fields( 'cbo_company_id*cbo_receive_basis*cbo_store_name*cbo_location*cbo_dyeing_source', 0, "", "" );
	disable_enable_fields( 'txt_exchange_rate*txt_lc_no', 1, "", "" );

}


function openmypage_po()
{
	var receive_basis=$('#cbo_receive_basis').val();
	var cbo_source=$('#cbo_source').val();
	var booking_no=$('#txt_wo_pi').val();
	var cbo_company_id = $('#cbo_company_id').val();
	var dtls_id = $('#update_id').val();
	var roll_maintained = $('#roll_maintained').val();

	var save_data = $('#save_data').val();
	var all_po_id = $('#all_po_id').val();
	var txt_receive_qnty = $('#txt_receive_qty').val(); 
	var txt_bla_order_qty = $('#txt_bla_order_qty').val(); 
	var cbo_body_part_id = $('#cbo_body_part').val(); 


	var distribution_method = $('#distribution_method_id').val();
	var txt_deleted_id=$('#txt_deleted_id').val();
	var hidden_pi_id=$('#hidden_pi_id').val();
	var hdn_buyer_id = $('#hdn_buyer_id').val();
	var hidden_color_id = $('#hidden_color_id').val();
	var hidden_dia_width = $('#hidden_dia_width').val();
	var hidden_gsm_weight = $('#hidden_gsm_weight').val();
	var txt_width_edit = $('#txt_width_edit').val();
	var txt_weight_edit = $('#txt_weight_edit').val();
	var fabric_desc_id = $('#fabric_desc_id').val();
	var txt_rate = $('#txt_rate').val();

	var txt_fabric_ref = $('#txt_fabric_ref').val();
	var txt_rd_no = $('#txt_rd_no').val();
	var cbo_weight_type = $('#cbo_weight_type').val();
	var txt_cutable_width = $('#txt_cutable_width').val();
	
	var update_hdn_transaction_id = $('#update_dtls_id').val(); //here update_dtls_id is transaction id

	if(receive_basis==0 )
	{
		alert("Please Select Receive Basis.");
		$('#cbo_receive_basis').focus();
		return false;
	}
	if(cbo_source==0 )
	{
		alert("Please Select Source");
		$('#cbo_receive_basis').focus();
		return false;
	}

	if(receive_basis==2 && booking_no=="")
	{
		alert("Please Select Booking No.");
		$('#txt_wo_pi').focus();
		return false;
	}
	if(receive_basis==1 && booking_no=="")
	{
		alert("Please Select PI No.");
		$('#txt_wo_pi').focus();
		return false;
	}
	else if((receive_basis==4 || receive_basis==6) && cbo_company_id==0)
	{
		alert("Please Select Company.");
		$('#cbo_company_id').focus();
		return false;
	}
	
	if(roll_maintained==1) 
	{
		popup_width='1200px';
	}
	else
	{
		popup_width='1010px';
	}
	var po_popup_patern_variable=2;
	if (po_popup_patern_variable==1) {var actionName="po_popup";}else{var actionName="po_popup_booking_wise";}
	var title = 'PO Info';	
	var page_link = 'requires/woven_finish_fabric_receive_controller.php?receive_basis='+receive_basis+'&cbo_company_id='+cbo_company_id+'&booking_no='+booking_no+'&dtls_id='+dtls_id+'&all_po_id='+all_po_id+'&roll_maintained='+roll_maintained+'&save_data='+save_data+'&txt_receive_qnty='+txt_receive_qnty+'&prev_distribution_method='+distribution_method+'&txt_deleted_id='+txt_deleted_id+'&hidden_pi_id='+hidden_pi_id+'&hdn_buyer_id='+hdn_buyer_id+'&hidden_color_id='+hidden_color_id+'&txt_bla_order_qty='+txt_bla_order_qty+'&cbo_body_part_id='+cbo_body_part_id+'&hidden_dia_width='+hidden_dia_width+'&fabric_desc_id='+fabric_desc_id+'&update_hdn_transaction_id='+update_hdn_transaction_id+'&hidden_gsm_weight='+hidden_gsm_weight+'&txt_rate='+txt_rate+'&txt_fabric_ref='+txt_fabric_ref+'&txt_rd_no='+txt_rd_no+'&cbo_weight_type='+cbo_weight_type+'&txt_cutable_width='+txt_cutable_width+'&txt_weight_edit='+txt_weight_edit+'&txt_width_edit='+txt_width_edit+'&action='+actionName;   
  	// alert(page_link);
  	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=370px,center=1,resize=1,scrolling=0','../');
  	emailwindow.onclose=function()
  	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var save_string=this.contentDoc.getElementById("save_string").value;	 //Access form field with id="emailfield"
		//alert(save_string);
		var tot_grey_qnty=this.contentDoc.getElementById("tot_grey_qnty").value; //Access form field with id="emailfield"
		var tot_rollNo=this.contentDoc.getElementById("tot_rollNo").value; //Access form field with id="emailfield"
		var number_of_roll=this.contentDoc.getElementById("number_of_roll").value; //Access form field with id="emailfield"
		var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
		var distribution_method=this.contentDoc.getElementById("distribution_method").value;
		var hide_deleted_id=this.contentDoc.getElementById("hide_deleted_id").value;
		
		//===========new didar 06-08-2018 start========================
		var blance = $("#txt_bla_order_qty").val()*1;
		var previous_recv_qty = $("#txt_receive_qty").val()*1;

		if(previous_recv_qty>0 && previous_recv_qty>=tot_grey_qnty)
		{
			var new_balance = (previous_recv_qty+blance)- tot_grey_qnty;
		}else {
			if(tot_grey_qnty>0)
			{
				new_balance = (blance-(tot_grey_qnty-previous_recv_qty));
			}
		}
		if(receive_basis==4 )
		{
			$("#txt_bla_order_qty").val('');
		}
		else{
			$("#txt_bla_order_qty").val(new_balance);
		}
		//===========new didar 06-08-2018 end========================

		$('#save_data').val(save_string);
		//$('#txt_receive_qty').val(tot_grey_qnty);
		$('#txt_receive_qty').val(number_format_common(tot_grey_qnty,"","",1));
		$('#txt_roll').val(tot_rollNo);
		$('#all_po_id').val(all_po_id);
		$('#distribution_method_id').val(distribution_method);
		
		if(roll_maintained==1)
		{
			$('#txt_roll').val(number_of_roll);
			$('#txt_deleted_id').val(hide_deleted_id);
		}
		else
		{
			$('#txt_deleted_id').val('');
		}
		
		fn_calile();
	}
}

function openmypage_fabricDescription()
{
	var title = 'Fabric Description Info';	
	var page_link = 'requires/woven_finish_fabric_receive_controller.php?action=fabricDescription_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("hidden_desc_id").value;	 //Access form field with id="emailfield"
		var theename=this.contentDoc.getElementById("hidden_desc_no").value; //Access form field with id="emailfield"
		var theegsm=this.contentDoc.getElementById("hidden_gsm").value; //Access form field with id="emailfield"
		
		$('#txt_fabric_description').val(theename);
		$('#original_fabric_description').val(theename);
		
		$('#fabric_desc_id').val(theemail);
		$('#txt_gsm').val(theegsm);
		//fn_fabric_descriptin_variable_check();
	}
}

//print 2 
function fn_report_generated(type)
{
	var rec_basic=$('#cbo_receive_basis').val();
	if(type==2){
		
		if(rec_basic==1){
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+ $('#txt_wo_pi_id').val()+'*'+report_title, "gwoven_finish_fabric_receive_print_2", "requires/woven_finish_fabric_receive_controller" ) 
			return;
		}
		else{
			alert('Print 2 generate by PI Basis');
		}
	}
	else if(type==3)
	{
		var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+ $('#txt_wo_pi_id').val()+'*'+report_title, "gwoven_finish_fabric_receive_print_3", "requires/woven_finish_fabric_receive_controller" ) 
			return;
	}
}


function check_exchange_rate()
{
	var cbo_currercy=$('#cbo_currency').val();
	var receive_date = $('#txt_receive_date').val();
	var company_id=$("#cbo_company_id").val();
	if(receive_date=="")
	{
		receive_date="<? echo date("d-m-Y");?>";
	}
	if(cbo_currercy==0)
	{
		cbo_currercy=2;
	}
	//alert(cbo_currercy+"**"+receive_date);
	var response=return_global_ajax_value( cbo_currercy+"**"+receive_date+"**"+company_id, 'check_conversion_rate', '', 'requires/woven_finish_fabric_receive_controller');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);	
}


function fn_fabric_descriptin_variable_check()
{
	var company_id=$('#cbo_company_id').val()*1;
	var response=return_global_ajax_value( company_id, 'fn_fabric_descriptin_variable_check', '', 'requires/woven_finish_fabric_receive_controller').split("_");
	if(response[1]==1){
		$('#txt_fabric_description').attr('disabled',false);
		$('#txt_fabric_description').removeAttr('readonly','readonly');
	}else{
		$("#txt_fabric_description").attr("disabled",true);
		$('#txt_fabric_description').attr('readonly','readonly');
	}
}
function fnc_reset_data()
{
	$("#save_data").val("");
	$("#txt_receive_qty").val("");
	$("#all_po_id").val("");
	$("#distribution_method_id").val("");
}

function fnc_resticted_item_update_mode(data)
{
	var chck_btn_disble =$("#update_dtls_id").val();
	if (chck_btn_disble) {
      alert("This item can not select in update mode");
       return;
    } 
	//restricted duplicate item in save mrr no
	var updateID=$('#update_id').val();
    var system_id=$('#txt_mrr_no').val();
    get_php_form_data(updateID+"**"+data, "check_same_item_found_inSameMRR", "requires/woven_finish_fabric_receive_controller" );
    var hidden_chk_saved_item=$('#hidden_chk_saved_item').val();
    if(hidden_chk_saved_item==updateID && system_id!="")
    {
		alert("This item already saved in this MRR");
		$('#hidden_chk_saved_item').val('');
       	return;
    }

	var data=encodeURIComponent(data);
    fn_fabric_descriptin_variable_check();
    fnc_reset_data();
    get_php_form_data(data,"wo_pi_product_form_input","requires/woven_finish_fabric_receive_controller");


	/*var chck_btn_disble =$('#update1').hasClass('formbutton');
    if (chck_btn_disble==true) {
      alert("This item can not select in update mode");
       return;
    } */
}
function put_data_dtls_part(datas)
{
	var data = trim(datas).split('**')
	var receivedId = data[0];
	var wopi = data[1];
	var receivedNumber = data[2];
	var pro_roll_dtlsId = data[3];
	var transectionID = data[4];

	var company_id = $('#cbo_company_id').val();
	var roll_maintained=$('#roll_maintained').val();
	var barcode_generation = $('#barcode_generation').val();
	var booking_without_order = $('#booking_without_order').val();	
	var datas=encodeURIComponent(datas);

	get_php_form_data(datas, "child_form_input_data", "requires/woven_finish_fabric_receive_controller");

	if(roll_maintained==1)
	{
		show_list_view("'"+receivedId+"**"+barcode_generation+"**"+booking_without_order+"**"+pro_roll_dtlsId+"'",'show_roll_listview','roll_details_list_view','requires/woven_finish_fabric_receive_controller','');
	}
	else
	{
		$('#roll_details_list_view').html('');
	}
}


function check_all_report()
{
	$("input[name=chkBundle]").each(function(index, element) 
	{ 
		if( $('#check_all').prop('checked')==true) 
			$(this).attr('checked','true');
		else
			$(this).removeAttr('checked');
	});
}	

function fnc_send_printer_text()
{
	var dtls_id=$('#update_dtls_id').val();
	
	var mst_id=$('#update_id').val();
	var booking_no=$('#txt_wo_pi').val();
	if(dtls_id=="")
	{
		alert("Save First");	
		return;
	}
	var data="";
	var error=1;
	$("input[name=chkBundle]").each(function(index, element) {
		if( $(this).prop('checked')==true)
		{
			error=0;
			var idd=$(this).attr('id').split("_");
			var roll_id=$('#txtRollTableId_'+idd[1] ).val();
			if(roll_id!="")
			{
				if(data=="") data=$('#txtRollTableId_'+idd[1] ).val(); else data=data+","+$('#txtRollTableId_'+idd[1] ).val();
			}
			else
			{
				$(this).prop('checked',false);
			}
		}
	});

	if( error==1 )
	{
		alert('No data selected');
		return;
	}
	
	data=data+"***"+dtls_id+"***"+booking_no+"******"+mst_id;
	var url=return_ajax_request_value(data, "report_barcode_text_file", "requires/woven_finish_fabric_receive_controller");
	window.open("requires/"+trim(url)+".zip","##");
}

function fnc_barcode_generation()
{
	var dtls_id=$('#update_dtls_id').val();
	var mst_id=$('#update_id').val();
	var booking_no=$('#txt_wo_pi').val();

	if(dtls_id=="")
	{
		alert("Save First");	
		return;
	}
	var data="";
	var error=1;
	$("input[name=chkBundle]").each(function(index, element) {
		if( $(this).prop('checked')==true)
		{
			error=0;
			var idd=$(this).attr('id').split("_");
			var roll_id=$('#txtRollTableId_'+idd[1] ).val();
			if(roll_id!="")
			{
				if(data=="") data=$('#txtRollTableId_'+idd[1] ).val(); else data=data+","+$('#txtRollTableId_'+idd[1] ).val();
			}
			else
			{
				$(this).prop('checked',false);
			}
		}
	});

	if( error==1 )
	{
		alert('No data selected');
		return;
	}
	
	data=data+"***"+dtls_id+"***"+booking_no+"******"+mst_id;
	window.open("requires/woven_finish_fabric_receive_controller.php?data=" + data+'&action=report_barcode_generation', true );
}

function change_color(v_id,e_color)
{
	var i=1;
	$("#tbl_id").find('tr').each(function()
	{
		if( $('#trId_'+i).attr('bgcolor')=='#c3e6cb')
	   	{
	   		$('#trId_'+i).attr('bgcolor','#E9F3FF');
	   	}
	   	else
	   	{
	   		$('#trId_'+v_id).attr('bgcolor','#c3e6cb');
	   	}
	   	i++;
	});
	/*if( $('#trId_'+v_id).attr('bgcolor')=='#FF9900')
		$('#trId_'+v_id).attr('bgcolor',e_color)
	else
		$('#trId_'+v_id).attr('bgcolor','#FF9900')*/
}
</script>
</head>
<body onLoad="set_hotkey();check_exchange_rate()">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
		<form name="yarn_receive_1" id="yarn_receive_1" autocomplete="off" > 
			<div style="width:880px;">       
				<fieldset style="width:880px; float:left;">
					<legend>Woven Finish Fabric Receive</legend>
					<br />
					<fieldset style="width:880px;">                                       
						<table width="870" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
							<tr>
								<td colspan="3" align="right">&nbsp;<b>MRR Number</b>
								</td>
								<td colspan="3" align="left">
									<input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:148px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly /> 
									<input type="hidden" name="update_id" id="update_id" />
								</td>
							</tr>
							<tr>
								<td width="130" align="right" class="must_entry_caption">Company Name </td>
								<td width="170">
									<? 
									echo create_drop_down( "cbo_company_id", 160, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "rcv_basis_reset();load_drop_down( 'requires/woven_finish_fabric_receive_controller', this.value, 'load_drop_down_supplier', 'supplier' );load_drop_down( 'requires/woven_finish_fabric_receive_controller', this.value, 'load_drop_down_location', 'location_td');load_drop_down( 'requires/woven_finish_fabric_receive_controller', this.value, 'load_drop_down_buyer', 'buyer_td');load_room_rack_self_bin('requires/woven_finish_fabric_receive_controller*3', 'store','store_td', this.value);get_php_form_data( this.value, 'company_wise_report_button_setting','requires/woven_finish_fabric_receive_controller' );get_php_form_data(this.value,'roll_maintained','requires/woven_finish_fabric_receive_controller' )" );
                                 //load_drop_down( 'requires/woven_finish_fabric_receive_controller', this.value, 'load_drop_down_store', 'store_td' );
									?>
								</td>
								<td width="94" align="right" class="must_entry_caption"> Receive Basis </td>
								<td width="160">
									<? 
									echo create_drop_down( "cbo_receive_basis", 160, $receive_basis_arr,"", 1, "- Select Receive Basis -", $selected, "fn_independent(this.value)","","1,2,4,6" );
									?>
								</td>
								<td width="80" align="right">WO / PI </td>
								<td width="140">
									<input class="text_boxes"  type="text" name="txt_wo_pi" id="txt_wo_pi" onDblClick="openmypage('xx','Order Search')"  placeholder="Double Click" style="width:148px;"  readonly disabled /> 
									<input type="hidden" id="txt_wo_pi_id" name="txt_wo_pi_id" value="" />
									<input type="hidden" name="booking_without_order" id="booking_without_order"/>
									<input type="hidden" name="fabric_source" id="fabric_source"/>
								</td>
							</tr>
							<tr>
								<td width="130" align="right" class="must_entry_caption">Receive Date </td>
								<td width="170">
									<input type="text" name="txt_receive_date" id="txt_receive_date" onChange="check_exchange_rate();" class="datepicker" style="width:148px;" value="<? echo date("d-m-Y"); ?>" placeholder="Select Date" readonly />
								</td>
								<td width="94" align="right" class="must_entry_caption"> Challan No </td>
								<td width="160">
									<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px" >
								</td>
								<td  width="130" align="right" >Location </td>
								<td width="170" id="location_td">
									<? 
									echo create_drop_down( "cbo_location", 160, $blank_array,"", 1, "-- Select Location --", 0, "" );
									?>
								</td>
							</tr>
							<tr>
								<td width="130" align="right" class="must_entry_caption">Store Name</td>
								<td width="170" id="store_td">
									<? 
									echo create_drop_down( "cbo_store_name", 160, "select id,store_name from lib_store_location where status_active=1 and is_deleted=0 and FIND_IN_SET(1,item_category_id) order by store_name","id,store_name", 1, "-- Select Store --", '', "" );
									?>
								</td>
								<td width="94" align="right" class="must_entry_caption"> Supplier </td>
								<td id="supplier" width="160"> 
									<?
									echo create_drop_down( "cbo_supplier", 160, "select id,supplier_name from lib_supplier where FIND_IN_SET(2,party_type) order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",1 );
									?>
								</td>
								<td width="130" align="right"> L/C No </td>
								<td id="lc_no" width="170">
									<input class="text_boxes"  type="text" name="txt_lc_no" id="txt_lc_no" style="width:148px;" placeholder="Display" onDblClick="popuppage_lc()" readonly disabled  />  
									<input type="hidden" name="hidden_lc_id" id="hidden_lc_id" />
								</td>
							</tr>
							<tr>
								<td width="130" align="right" class="must_entry_caption">Currency</td>
								<td width="170" id="currency"> 
									<?
									echo create_drop_down( "cbo_currency", 160, $currency,"", 1, "-- Select Currency --", '', "exchange_rate(this.value)",1 );
									?>
								</td>
								<td  width="130" align="right" class="must_entry_caption">Exchange Rate</td>
								<td width="170">
									<input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:148px" value="" onBlur="fn_calile()" disabled readonly/>	
								</td>
								<td width="94" align="right" class="must_entry_caption">Source</td>
								<td width="160" id="sources">  
									<?
									echo create_drop_down( "cbo_source", 160, $source,"", 1, "-- Select --", $selected, "",1 );
									?>
								</td>
							</tr>
							<tr>
								<td width="94" align="right">Dyeing Source</td>
								<td width="160">  
									<?
									echo create_drop_down( "cbo_dyeing_source", 160, $knitting_source,"", 1, "-- Select --", $selected, "",0 );
									?>
								</td>
								<td width="100" align="right">Buyer</td>
                                <td width="160" id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 160, $blank_array,"", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                                  <td align="right">file</td> 
                                   <td> <input type="button" class="image_uploader" style="width:140px" value="CLICK TO ADD FILE" id="btn_fileadd"onClick="file_uploader ( '../../', document.getElementById('txt_mrr_no').value,'', 'woven_finish_fabric_receive', 2 ,1)"> </td>
							</tr>
						</table>
					</fieldset>
					<br />


					<table cellpadding="0" cellspacing="1" width="910" border="0" id="tbl_child">
						<fieldset style="width:880px;">  
							<legend>New Receive Item</legend>       

							<tr>
								<td width="49%" valign="top" >

									<table width="220" cellspacing="2" cellpadding="0" border="0" style="float:left;">
										<tr>    
											<td align="right" class="must_entry_caption">Body Part</td>
											<td id="body_part_td">
												<?
												echo create_drop_down( "cbo_body_part", 167, $body_part,"", 1, "-- Select Body Part --", 0, "","1" );
												?>
											</td>
										</tr> 
										<!--
										<tr>    
											<td align="right">Fabric Type</td>
											<td>         
												<input type="text" name="txt_fabric_type" id="txt_fabric_type" class="text_boxes" style="width:140px;" maxlength="20" title="Maximum 20 Character"  />
											</td>
										</tr>
										-->

										<tr>
											<td align="right" class="must_entry_caption">Fabric Desc </td>
											<td colspan="3">
												<input type="text" name="txt_fabric_description" id="txt_fabric_description" class="text_boxes" style="width:155px; height:40px" onDblClick="openmypage_fabricDescription()" placeholder="Double Click To Search"  readonly disabled/>
                                                <input type="hidden" name="original_fabric_description" id="original_fabric_description" class="text_boxes" style="width:140px; height:40px"  readonly disabled/>
												<input type="hidden" name="fabric_desc_id" id="fabric_desc_id" class="text_boxes" style="width:397px">
											</td>
										</tr>
										<tr>
											<td align="right">Fabric Ref</td>
											<td>
												<input type="text" name="txt_fabric_ref" id="txt_fabric_ref" class="text_boxes" style="width:50px;" disabled />
												<span style="width: 20px;">RD No</span>
												<span style="width: 55px;"><input type="text" name="txt_rd_no" id="txt_rd_no" class="text_boxes" style="width:55px;" disabled /></span>
											</td>
										</tr>
										<tr>   
											<td align="right" class="must_entry_caption">Color</td>
											<td id="color_td_id">
												<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:155px;" maxlength="20" title="Maximum 20 Character" disabled />
												<input type="hidden" name="hidden_color_id" id="hidden_color_id" class="text_boxes"/>
												<input type="hidden" name="hidden_dia_width" id="hidden_dia_width" class="text_boxes"/>
												<input type="hidden" name="hidden_gsm_weight" id="hidden_gsm_weight" class="text_boxes"/>
											</td>
										</tr> 
										<tr>    
											<td align="right">Weight</td>
											<td>
												<input type="text" name="txt_weight" id="txt_weight" class="text_boxes_numeric" style="width:25px;" disabled/>
												<input type="text" name="txt_weight_edit" id="txt_weight_edit" class="text_boxes_numeric" style="width:25px;" />
												<span style="width: 10px;">Type</span>
												<span style="width: 60px;">
												<?
												echo create_drop_down( "cbo_weight_type", 60, $fabric_weight_type,"", 1, "-Select-", 0, "",1 );
												?>
											</span>
											</td>
										</tr>
										<tr>    
											<td align="right">Full Width</td>
											<td>
												<input type="text" name="txt_width" id="txt_width" class="text_boxes" style="width:25px;" disabled />
												<input type="text" name="txt_width_edit" id="txt_width_edit" class="text_boxes_numeric" style="width:25px;"  />
												<span style="width: 20px;">C.Width</span>
												<span style="width: 30px;">
												<input type="text" name="txt_cutable_width" id="txt_cutable_width" class="text_boxes" style="width:30px;" disabled />
												</span>
											</td>
										</tr> 
										<tr>
											<td width="110" align="right" class="must_entry_caption">Batch/Lot</td>
											<td width="130">
												<input type="text" name="txt_batch_lot" id="txt_batch_lot" class="text_boxes" style="width:155px;" />
												<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes"/>
											</td> 
										</tr>

										<tr>
											<td width="" align="right">Product ID</td>
											<td width=""><input class="text_boxes"  name="txt_prod_code" id="txt_prod_code" type="text" style="width:155px;" readonly  disabled /></td>
										</tr> 
										
										<tr>                 
											<td width="140" align="right">UOM</td>
											<td>
												<?
                                       // echo create_drop_down( "cbo_uom", 152, $unit_of_measurement,"", 0, "--Select--", 27, "",1,27 );
												echo create_drop_down( "cbouom", 167, $unit_of_measurement,"", 0, "-Uom-", 27, "","1,12,23,27" );
												?>
											</td>
										</tr>
										<tr>    
											<td align="right" class="must_entry_caption">Recv. Qnty.</td>   
											<td>
												<input name="txt_receive_qty" id="txt_receive_qty"  class="text_boxes_numeric" type="text" style="width:155px;" onClick="openmypage_po()" placeholder="Single Click" onFocus="fn_calile()" readonly />
												<input type="hidden" name="hidden_pi_id" id="hidden_pi_id"  class="text_boxes_numeric"  style="width:140px;"/>
												<input type="hidden" name="hidden_pi_ids" id="hidden_pi_ids"  class="text_boxes_numeric"  style="width:140px;"/>
											</td> 
										</tr>
										<tr>    
											<td align="right" class="must_entry_caption">Rate</td>   
											<td >
												<input name="txt_rate" id="txt_rate" class="text_boxes_numeric" type="text" style="width:155px;" onBlur="fn_calile()" value="0" disabled />
											</td>
										</tr>

										<tr>   
											<td align="right" id="ile_td">ILE%</td>   
											<td >
												<input name="txt_ile" id="txt_ile" class="text_boxes_numeric" type="text" style="width:155px;" placeholder="ILE COST" readonly disabled />
											</td>
										</tr>  

									</table>

									<table width="220" cellspacing="2" cellpadding="0" border="0" style="float:left">

										<tr> 
											<td align="right">Amount</td>
											<td><input title="Rcv Quantity(Rate+ ILE Cost)" type="text" name="txt_amount" id="txt_amount" class="text_boxes_numeric" style="width:140px;" readonly disabled /></td>
										</tr>
										<tr> 
											<td align="right">Book Currency.</td>
											<td>
												<input title="Amount * Exchange Rate" type="text" name="txt_book_currency" id="txt_book_currency" class="text_boxes_numeric" style="width:140px;" readonly disabled />
											</td>
										</tr>
										<tr> 
											<td align="right">Balance PI/ WO </td>
											<td>
												<input class="text_boxes_numeric"  name="txt_bla_order_qty" id="txt_bla_order_qty" type="text" style="width:140px;" readonly disabled />
												<input  name="hdn_booking_no" id="hdn_booking_no" type="hidden" style="width:140px;" />
												<input  name="hdn_booking_id" id="hdn_booking_id" type="hidden" style="width:140px;" />
											</td>
										</tr>


										<tr>    
											<td align="right" class="must_entry_caption">Roll</td>   
											<td >
												<input name="txt_roll" id="txt_roll" class="text_boxes_numeric" type="text" style="width:140px;" disabled="disabled" />
											</td>
										</tr>

										<tr>
											<td width="" align="right">Cutting Unit Name</td>
											<td >
												<?
												echo create_drop_down( "cbo_cutting_unit_no", 152, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=1 order by floor_name","id,floor_name", 1, "-- Select Cutting Unit --", $selected, "",0 );
												?>
											</td>
										</tr>
										<tr> 
											<td align="right">Floor</td>
											<td id="floor_td">
												<? echo create_drop_down( "cbo_floor", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>
										<tr> 
											<td align="right">Room</td>
											<td id="room_td">
												<? echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>
										<tr> 
											<td align="right">Rack</td>
											<td id="rack_td">
												<? echo create_drop_down( "txt_rack", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>
										<tr> 
											<td align="right">Shelf</td>
											<td id="shelf_td">
												<? echo create_drop_down( "txt_shelf", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>
										<tr> 
											<td align="right">Bin/Box</td>
											<td id="bin_td">
												<? echo create_drop_down( "cbo_bin", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>

									</table>



								</td>

								<td width="48%" valign="top">
									<div id="roll_details_list_view" style="padding-right:20px;">
										<!--- Data would be load here !-->
									</div>
								</td>

							</tr>

							<tr style="margin-left:-30px;">                   
								<td width="49%">
									<table width="100%" cellspacing="2" cellpadding="0" border="0">
										<tr> 
											<td align="left" width="10" style="padding-left:20px;">Remarks </td>
											<td >
												<input class="text_boxes"  name="txt_remarks" id="txt_remarks" type="text" style="width:92%;"/>
											</td>
										</tr>  
									</table>
								</td>
							</tr>

						</fieldset>                    
					</table>



					<table cellpadding="0" cellspacing="1" width="100%">
						<tr> 
							<td colspan="6" align="center"></td>				
						</tr>
						<tr>
							<td align="center" colspan="6" valign="middle" class="button_container">
								<input type="hidden" name="save_data" id="save_data" readonly>
								<input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
								<input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
								<input type="hidden" name="update_finish_fabric_id" id="update_finish_fabric_id" readonly />
								<input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
								<input type="hidden" name="hidden_receive_qnty" id="hidden_receive_qnty" readonly>
								<input type="hidden" name="all_po_id" id="all_po_id" readonly>
								<input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
								<input type="hidden" name="hdn_buyer_id" id="hdn_buyer_id" readonly>
								<input type="hidden" name="barcode_generation" id="barcode_generation" readonly>
								<input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
								<input type="hidden" name="txt_deleted_id" id="txt_deleted_id" readonly />
								<input type="hidden" name="hidden_chk_saved_item" id="hidden_chk_saved_item" readonly />


								<? echo load_submit_buttons( $permission, "fnc_woben_finish_fab_receive_entry", 0,0,"fnResetForm()",1);?>
								<input type="button" name="print" id="print" value="Print" onClick="fnc_woben_finish_fab_receive_entry(4)" style="width: 80px; display:none;" class="formbutton">
								<input type="button" id="show_button" class="formbutton" style="width: 80px; display:none;" value="Print 2" onClick="fn_report_generated(2)" />
								<input type="button" id="show_button" class="formbutton" style="width: 80px;" value="Print 3" onClick="fn_report_generated(3)" />
							</td>
						</tr> 
					</table>

					<div style="width:870px;" id="list_container_yarn"></div>                 
				</fieldset>
			</div>
			<div id="list_product_container" style="max-height:500px; width:390px; overflow:auto; float:left; margin-left:5px; margin-top:5px; position:relative;"></div>  
		</form>
	</div>  
</body> 
<script>	
$(document).ready(function() {
  	$('#cbo_store_name').val(0);
});
</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

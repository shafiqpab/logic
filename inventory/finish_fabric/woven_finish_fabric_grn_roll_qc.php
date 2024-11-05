<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Woven Finish Fabric GRN roll
				
Functionality	:	
JS Functions	:
Created by		:	 
Creation date 	: 	03-09-2022
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
echo load_html_head_contents("Woven Fabric Receive Info","../../", 1, 1, $unicode,1,1); 

?>	

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	/* var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color where status_active=1 group by color_name", "color_name" ), 0, -1); ?>];

	$(function() {	
		$("#txt_color").autocomplete({
			source: str_color
		});
	});
 */

	function rcv_basis_reset()
	{
		document.getElementById('cbo_receive_basis').value=0;
	} 

	<?
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][17] );
		echo "var field_level_data= ". $data_arr . ";\n";
	?>

	
	
// popup for WO/PI----------------------	
function openmypage(page_link,title)
{
	if( form_validation('cbo_company_id*cbo_receive_basis','Company Name*Receive Basis')==false )
	{
		return;
	}
	
	var company = $("#cbo_company_id").val();
	var receive_basis = $("#cbo_receive_basis").val();

	page_link='requires/woven_finish_fabric_grn_roll_qc_controller.php?action=wopi_popup&company='+company+'&receive_basis='+receive_basis;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1410px, height=400px, center=1, resize=0, scrolling=0','../')
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
				
				get_php_form_data(receive_basis+"**"+rowID+"**"+hidden_is_non_ord_sample+"**"+wopiNumber+"**"+hidden_basis, "populate_data_from_wopi_popup", "requires/woven_finish_fabric_grn_roll_qc_controller" );
				
				show_list_view(receive_basis+"**"+rowID+"**"+hidden_is_non_ord_sample+"**"+wopiNumber,'show_product_listview','list_product_container','requires/woven_finish_fabric_grn_roll_qc_controller','setFilterGrid(\'table_body\',-1);');
				
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
	reset_form('yarn_receive_1','list_product_container','','','','cbo_company_id*cbo_receive_basis*txt_receive_date*roll_maintained*barcode_generation*cbouom*hdn_batch_control_variable');
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

		$("#cbouom").attr('disabled',true);
		$("#txt_cutable_width").attr('disabled',true);
		$("#cbo_weight_type").attr('disabled',true);
		$("#txt_fabric_ref").attr('disabled',true);
		$("#txt_rd_no").attr('disabled',true);

		$("#cbouom").val(27);
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

		$("#cbouom").attr('disabled',true);
		$("#txt_cutable_width").attr('disabled',true);
		$("#cbo_weight_type").attr('disabled',true);
		$("#txt_fabric_ref").attr('disabled',true);
		$("#txt_rd_no").attr('disabled',true);

		$("#cbouom").val(27);
		
	}
	
}

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
		var response=return_global_ajax_value( currency_id+"**"+recv_date+"**"+company_id, 'check_conversion_rate', '', 'requires/woven_finish_fabric_grn_roll_qc_controller').split("_");
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
	var page_link='requires/woven_finish_fabric_grn_roll_qc_controller.php?action=lc_popup&company='+company; 
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
function fn_calile(i)
{
	
	var company=$('#cbo_company_id').val()*1;	
	var source=$('#cbo_source').val()*1;	
	var rate=$('#txtRate_'+i).val()*1;
	//*txt_rate*Rate
	if( form_validation('cbo_company_id*cbo_source','Company Name*Source')==false )
	{
		return;
	}
	else
	{
		var responseHtml = return_ajax_request_value(company+'**'+source+'**'+rate, 'show_ile', 'requires/woven_finish_fabric_grn_roll_qc_controller');
		var splitResponse="";
		if(responseHtml!="")
		{
			splitResponse = responseHtml.split("**");
			//$("#ile_td").html('ILE% '+splitResponse[0]);
			$("#txtIle"+i).val(splitResponse[1]);
		}
		else
		{
			//$("#ile_td").html('ILE% 0');
			$("#txtIle"+i).val(0);
		}
		
		//amount and book currency calculate--------------//
		var quantity 		= $("#recvQty_"+i).val();
		var exchangeRate 	= $("#txt_exchange_rate").val();
		var ile_cost 		= $("#txtIle_"+i).val();
		var amount = quantity*1*(rate*1+ile_cost*1); 
		var bookCurrency = (rate*1+ile_cost*1)*exchangeRate*1*quantity*1;
		$("#txtAmount_"+i).val(number_format_common(amount,"","",1));
		$("#txtBookCurrency_"+i).val(number_format_common(bookCurrency,"","",1));
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
		load_drop_down( 'requires/woven_finish_fabric_grn_roll_qc_controller', 1, 'load_drop_down_composition', 'composition_td' );		 		
	}
	else // When F button click
	{			
		load_drop_down( 'requires/woven_finish_fabric_grn_roll_qc_controller', 2, 'load_drop_down_composition', 'composition_td' );
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
		load_drop_down( 'requires/woven_finish_fabric_grn_roll_qc_controller', '', 'load_drop_down_color', 'color_td_id' );
	}
}


function fnc_woben_finish_fab_receive_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "gwoven_finish_fabric_receive_print", "requires/woven_finish_fabric_grn_roll_qc_controller" ) 
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
		var hdn_batch_control_variable = $("#hdn_batch_control_variable").val();
		if( form_validation('cbo_company_id*txt_receive_date*txt_grn_no','Company Name*Receive Date*GRN No')==false )
		{
			return;
		}
		
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_receive_date').val(), current_date)==false)
		{
			alert("Receive Date Can not Be Greater Than Current Date");
			return;
		}
		var comp_id = $("#cbo_company_id").val();

		var j=0; var dataString=''; var txtActualWgtValidation=0;
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var txtActualWgt=$(this).find('input[name="txtActualWgt[]"]').val();
			var recvQty=$(this).find('input[name="recvQty[]"]').val();
			var txtRejQty=$(this).find('input[name="txtRejQty[]"]').val();
			var rollData=$(this).find('input[name="rollData[]"]').val();
			var updateDtlsId=$(this).find('input[name="updateDtlsId[]"]').val();
			var updateGRNDtlsId=$(this).find('input[name="updateGRNDtlsId[]"]').val();

			
			if((txtActualWgt=="" || txtActualWgt==0) && (recvQty !=0 || txtRejQty !=0))
			{
				txtActualWgtValidation +=1;
			}
			
			j++;
			dataString+='&txtActualWgt_' + j + '=' + txtActualWgt  +'&recvQty_' + j + '=' + recvQty + '&rollData_' + j + '=' + rollData + '&txtRejQty_' + j + '=' + txtRejQty + '&updateDtlsId_' + j + '=' + updateDtlsId  + '&updateGRNDtlsId_' + j + '=' + updateGRNDtlsId;
		});

		if(txtActualWgtValidation >0)
		{
			alert("Please give actual weight");
			return;
		}


		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_mrr_no*update_id*cbo_company_id*txt_grn_no*hdn_grn_id*txt_receive_date*cbo_location*cbo_store_name*cbo_supplier',"../../")+dataString;
		//alert(data);
		freeze_window(operation);
		http.open("POST","requires/woven_finish_fabric_grn_roll_qc_controller.php",true);
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
			$('#txt_grn_no').attr('disabled','disabled');

			//show_list_view(reponse[2]+'**'+reponse[1],'show_dtls_list_view','list_container_yarn','requires/woven_finish_fabric_grn_roll_qc_controller','');
			
			show_list_view($("#update_id").val(),'show_product_listview_update','list_product_container','requires/woven_finish_fabric_grn_roll_qc_controller','');


			set_button_status(1, permission, 'fnc_woben_finish_fab_receive_entry',1,1);

			//reset_form('','','txt_fabric_description*original_fabric_description*fabric_desc_id*txt_color*txt_width*txt_weight*txt_batch_lot*txt_receive_qty*txt_rate*txt_ile*txt_amount*txt_book_currency*txt_bla_order_qty*txt_prod_code*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*save_data*update_dtls_id*update_trans_id*previous_prod_id*hidden_receive_qnty*all_po_id*cbo_body_part*txt_roll*txt_remarks*hdn_booking_no*hdn_booking_id*txt_fabric_ref*txt_rd_no*cbo_weight_type*txt_cutable_width*update_finish_fabric_id*txt_weight_edit*txt_width_edit','','','');

			var comp_id = $("#cbo_company_id").val();
			//wvn_finish_fabric_auto_batch_maintain_fnc(comp_id);
		}
		else if(reponse[0]==2)
		{
			if(reponse[3]==1)
			{
				release_freezing();
				location.reload();
			}
			if(reponse[3]==2)
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_mrr_no').value = reponse[2];

				show_list_view(reponse[2]+'**'+reponse[1],'show_dtls_list_view','list_container_yarn','requires/woven_finish_fabric_grn_roll_qc_controller','');
				set_button_status(reponse[3], permission, 'fnc_woben_finish_fab_receive_entry',1,1);

				reset_form('','','txt_fabric_description*original_fabric_description*fabric_desc_id*txt_color*txt_width*txt_weight*txt_batch_lot*txt_receive_qty*txt_rate*txt_ile*txt_amount*txt_book_currency*txt_bla_order_qty*txt_prod_code*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*save_data*update_dtls_id*update_trans_id*previous_prod_id*hidden_receive_qnty*all_po_id*cbo_body_part*txt_roll*txt_remarks*hdn_booking_no*hdn_booking_id*txt_fabric_ref*txt_rd_no*cbo_weight_type*txt_cutable_width*update_finish_fabric_id*txt_weight_edit*txt_width_edit','','','');
			}
			
		}
		else if(reponse[0]==20)
		{
			alert(reponse[1]);
			show_msg(trim(reponse[0]));
			release_freezing();
			return;
		}else if(reponse[0]==50)
        {
			alert(reponse[1]);
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
	var page_link='requires/woven_finish_fabric_grn_roll_qc_controller.php?action=mrr_popup&company='+company; 
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		reset_form('yarn_receive_1','list_product_container','','','','cbo_company_id*roll_maintained*barcode_generation*hdn_batch_control_variable');
		var theform=this.contentDoc.forms[0]; 
		var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value; // mrr number
		$("#txt_mrr_no").val(mrrNumber);
		// master part call here
		//$("#tbl_master").find('input,select').attr("disabled", true);

		disable_enable_fields( 'txt_mrr_no', 0, "", "" );
		get_php_form_data(mrrNumber, "populate_data_from_data_update", "requires/woven_finish_fabric_grn_roll_qc_controller");
		show_list_view($("#update_id").val(),'show_product_listview_update','list_product_container','requires/woven_finish_fabric_grn_roll_qc_controller','');
		$("#cbo_company_id").attr("disabled", true);
		$("#txt_grn_no").attr("disabled", true);
		$("#cbo_location").attr("disabled", true);
		$("#cbo_store_name").attr("disabled", true);
		$("#cbo_supplier").attr("disabled", true);

		var total_qc_qty= 0; var  total_reject_qty=0;
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var recvQty = $(this).find('input[name="recvQty[]"]').val();
			var txtRejQty =  $(this).find('input[name="txtRejQty[]"]').val();

			total_qc_qty +=recvQty*1;
			total_reject_qty +=txtRejQty*1;
		});
		$("#total_qc_qty").text(total_qc_qty);
		$("#total_reject_qty").text(total_reject_qty);

		set_button_status(1, permission, 'fnc_woben_finish_fab_receive_entry',1);
		
	}
}

function open_grnpopup()
{
	var company = $("#cbo_company_id").val();	
	var page_link='requires/woven_finish_fabric_grn_roll_qc_controller.php?action=grn_popup&company='+company; 
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		reset_form('yarn_receive_1','list_product_container','','','','cbo_company_id');
		var theform=this.contentDoc.forms[0]; 
		var grnNumber=this.contentDoc.getElementById("hidden_recv_number").value; 

		$("#txt_grn_no").val(grnNumber);
		disable_enable_fields( 'txt_mrr_no', 0, "", "" );
		get_php_form_data(grnNumber, "populate_data_from_data", "requires/woven_finish_fabric_grn_roll_qc_controller");
		show_list_view($("#hdn_grn_id").val(),'show_product_listview','list_product_container','requires/woven_finish_fabric_grn_roll_qc_controller','');
		$("#cbo_company_id").attr("disabled", true);
		$("#cbo_location").attr("disabled", true);
		$("#cbo_store_name").attr("disabled", true);
		$("#cbo_supplier").attr("disabled", true);

		var total_qc_qty= 0; var  total_reject_qty=0;
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var recvQty = $(this).find('input[name="recvQty[]"]').val();
			var txtRejQty =  $(this).find('input[name="txtRejQty[]"]').val();

			total_qc_qty +=recvQty*1;
			total_reject_qty +=txtRejQty*1;
		});
		$("#total_qc_qty").text(total_qc_qty);
		$("#total_reject_qty").text(total_reject_qty);

		set_button_status(0, permission, 'fnc_woben_finish_fab_receive_entry',1);	
	}
}

function fnResetForm()
{
	$("#tbl_master").find('input').attr("disabled", false);	
	
	set_button_status(0, permission, 'fnc_woben_finish_fab_receive_entry',1);
	//reset_form('yarn_receive_1','list_container_yarn*list_product_container','','','','cbo_uom*cbo_currency*txt_exchange_rate*cbo_color');
	reset_form('','list_product_container*list_container_yarn','txt_mrr_no*update_id*txt_wo_pi*txt_wo_pi_id*booking_without_order*fabric_source*txt_challan_no*cbo_supplier*cbo_currency*txt_exchange_rate*cbo_source*cbo_dyeing_source*cbo_buyer_name','','','');
	disable_enable_fields( 'cbo_company_id*cbo_receive_basis*cbo_store_name*cbo_location*cbo_dyeing_source', 0, "", "" );
	disable_enable_fields( 'txt_exchange_rate*txt_lc_no', 1, "", "" );

	var comp_id = $("#cbo_company_id").val();
	//wvn_finish_fabric_auto_batch_maintain_fnc(comp_id);


}


function openmypage_po(i)
{
	var updateDtlsId=$('#updateDtlsId_'+i).val();

	var rollData = $('#rollData_'+i).val();
	//var all_po_id = $('#all_po_id').val();
	var txt_receive_qnty = $('#recvQty_'+i).val(); 
	
	
	var popup_width='800px';

	var actionName="po_popup_booking_wise";
	var title = 'Style Info';	
	var page_link = 'requires/woven_finish_fabric_grn_roll_qc_controller.php?save_data='+rollData+'&txt_receive_qnty='+txt_receive_qnty+'&updateDtlsId='+updateDtlsId+'&action='+actionName;   

  	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=370px,center=1,resize=1,scrolling=0','../');
  	emailwindow.onclose=function()
  	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var save_string=this.contentDoc.getElementById("save_string").value;
		var qc_quantity=this.contentDoc.getElementById("total_qc_quantity").value;
		var reject_qnty=this.contentDoc.getElementById("total_reject_quantity").value;
		
		$('#rollData_'+i).val(save_string);
		$('#recvQty_'+i).val(number_format_common(qc_quantity,"","",1));   
		$('#txtRejQty_'+i).val(reject_qnty);

		var total_qc_qty= 0; var  total_reject_qty=0;
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var recvQty = $(this).find('input[name="recvQty[]"]').val();
			var txtRejQty =  $(this).find('input[name="txtRejQty[]"]').val();

			total_qc_qty +=recvQty*1;
			total_reject_qty +=txtRejQty*1;

		});
		$("#total_qc_qty").text(total_qc_qty);
		$("#total_reject_qty").text(total_reject_qty);
	}
}

function openmypage_fabricDescription()
{
	var title = 'Fabric Description Info';	
	var page_link = 'requires/woven_finish_fabric_grn_roll_qc_controller.php?action=fabricDescription_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1450px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("hidden_desc_id").value;	 //Access form field with id="emailfield"
		var theename=this.contentDoc.getElementById("hidden_desc_no").value; //Access form field with id="emailfield"
		var theegsm=this.contentDoc.getElementById("hidden_gsm").value; //Access form field with id="emailfield"
		var hidden_weight_type=this.contentDoc.getElementById("hidden_weight_type").value; //Access form field with id="emailfield"
		var hidden_full_width=this.contentDoc.getElementById("hidden_full_width").value; //Access form field with id="emailfield"
		var hidden_cutable_width=this.contentDoc.getElementById("hidden_cutable_width").value; //Access form field with id="emailfield"
		var hidden_fabric_ref=this.contentDoc.getElementById("hidden_fabric_ref").value; //Access form field with id="emailfield"
		var hidden_rd_no=this.contentDoc.getElementById("hidden_rd_no").value; //Access form field with id="emailfield"


		$('#txt_fabric_description').val(theename);
		$('#original_fabric_description').val(theename);
		
		$('#fabric_desc_id').val(theemail);
		$('#txt_weight').val(theegsm);
		$('#txt_weight_edit').val(theegsm);

		$('#cbo_weight_type').val(hidden_weight_type);
		$('#txt_width').val(hidden_full_width);
		$('#txt_width_edit').val(hidden_full_width);
		$('#txt_cutable_width').val(hidden_cutable_width);
		$('#txt_fabric_ref').val(hidden_fabric_ref);
		$('#txt_rd_no').val(hidden_rd_no);
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
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+ $('#txt_wo_pi_id').val()+'*'+report_title, "gwoven_finish_fabric_receive_print_2", "requires/woven_finish_fabric_grn_roll_qc_controller" ) 
			return;
		}
		else{
			alert('Print 2 generate by PI Basis');
		}
	}
	else if(type==3)
	{
		var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+ $('#txt_wo_pi_id').val()+'*'+report_title, "gwoven_finish_fabric_receive_print_3", "requires/woven_finish_fabric_grn_roll_qc_controller" ) 
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
	var response=return_global_ajax_value( cbo_currercy+"**"+receive_date+"**"+company_id, 'check_conversion_rate', '', 'requires/woven_finish_fabric_grn_roll_qc_controller');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);	
}


function fn_fabric_descriptin_variable_check()
{
	var company_id=$('#cbo_company_id').val()*1;
	var response=return_global_ajax_value( company_id, 'fn_fabric_descriptin_variable_check', '', 'requires/woven_finish_fabric_grn_roll_qc_controller').split("_");
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
    get_php_form_data(updateID+"**"+data, "check_same_item_found_inSameMRR", "requires/woven_finish_fabric_grn_roll_qc_controller" );
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
    get_php_form_data(data,"wo_pi_product_form_input","requires/woven_finish_fabric_grn_roll_qc_controller");

    openmypage_po();


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

	get_php_form_data(datas, "child_form_input_data", "requires/woven_finish_fabric_grn_roll_qc_controller");

	if(roll_maintained==1)
	{
		show_list_view("'"+receivedId+"**"+barcode_generation+"**"+booking_without_order+"**"+pro_roll_dtlsId+"'",'show_roll_listview','roll_details_list_view','requires/woven_finish_fabric_grn_roll_qc_controller','');
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
	var url=return_ajax_request_value(data, "report_barcode_text_file", "requires/woven_finish_fabric_grn_roll_qc_controller");
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
	window.open("requires/woven_finish_fabric_grn_roll_qc_controller.php?data=" + data+'&action=report_barcode_generation', true );
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
function wvn_finish_fabric_auto_batch_maintain_fnc(data)
{
	var varible_data=return_global_ajax_value( data, 'varible_inv_auto_batch_maintain', '', 'requires/woven_finish_fabric_grn_roll_qc_controller');
	if(varible_data==1)
	{
		$("#txt_batch_lot").attr("disabled",true);
		$("#hdn_batch_control_variable").val(varible_data);
	}
	else
	{
		$("#txt_batch_lot").attr("disabled",false);
		$("#hdn_batch_control_variable").val("");
	}
}

function upto_rack_variable_chk_fnc(data)
{
	var varible_string=return_global_ajax_value( data, 'varible_inventory_upto_rack', '', 'requires/woven_finish_fabric_grn_roll_qc_controller');
	var varible_string_ref=varible_string.split("**");
	if(varible_string_ref[0]>0)
	{
		if(varible_string_ref[1]==1)
		{
			if( form_validation('cbo_store_name','Store Name')==false )
			{
				return 1;
			}
		}
		else if (varible_string_ref[1]==2) 
		{
			if( form_validation('cbo_store_name*cbo_floor','Store Name*Floor Name')==false )
			{
				return 1;
			}
		}
		else if (varible_string_ref[1]==3) 
		{
			if( form_validation('cbo_store_name*cbo_floor*cbo_room','Store Name*Floor Name*Room Name')==false )
			{
				return 1;
			}
		}
		else if (varible_string_ref[1]==4) 
		{
			if( form_validation('cbo_store_name*cbo_floor*cbo_room*txt_rack','Store Name*Floor Name*Room Name*Rack Name')==false )
			{
				return 1;
			}
			return;
		}
		else if (varible_string_ref[1]==5) 
		{
			if( form_validation('cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf','Store Name*Floor Name*Room Name*Rack Name*Shelf Name')==false )
			{
				return 1;
			}
		}
		else if (varible_string_ref[1]==6) 
		{
			if( form_validation('cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin','Store Name*Floor Name*Room Name*Rack Name*Shelf Name*Bin Name')==false )
			{
				return 1;
			}
		}
	}
	else
	{
		return 0;
	}
}

function independence_basis_controll_function(data)
{
    /*var independent_control_arr = JSON.parse('<? //echo json_encode($independent_control_arr); ?>');
    $("#cbo_receive_basis").val(0);
    $("#cbo_receive_basis option[value='4']").show();
    if(independent_control_arr[data]==1)
    {
        $("#cbo_receive_basis option[value='4']").hide();
    }*/
	
	var varible_string=return_global_ajax_value( data, 'varible_inventory', '', 'requires/woven_finish_fabric_grn_roll_qc_controller');
	
	var varible_string_ref=varible_string.split("**");
	//alert(varible_string_ref[0]);
	if(varible_string_ref[0])
	{
		$('#variable_string_inventory').val(varible_string_ref[1]+"**"+varible_string_ref[2]+"**"+varible_string_ref[3]+"**"+varible_string_ref[4]);
		if(varible_string_ref[1]==1)
		{
			$("#cbo_receive_basis option[value='4']").hide();
		}
		else
		{
			$("#cbo_receive_basis option[value='4']").show();
		}
		$('#is_rate_optional').val(varible_string_ref[2]);
		if(varible_string_ref[4]==2)
		{
			$('#txt_rate').attr("readonly",true);
		}
		else
		{
			$('#txt_rate').attr("readonly",false);
		}
		
		if(varible_string_ref[3]==1)
		{
			$('#rate_td').css("display", "none");
			$('#amount_td').css("display", "none");
			$('#book_currency_td').css("display", "none");
		}
		else
		{
			$('#rate_td').css("display", "");
			$('#amount_td').css("display", "");
			$('#book_currency_td').css("display", "");
		}
		
	}
	else
	{
		$('#variable_string_inventory').val("");
		$("#cbo_receive_basis option[value='4']").show();
		$('#is_rate_optional').val("");
		$('#txt_rate').attr("readonly",false);
		$('#rate_td').css("display", "");
		$('#amount_td').css("display", "");
		$('#book_currency_td').css("display", "");
	}
	//alert(varible_string);return;
    // ==============Start Floor Room Rack Shelf Bin upto variable Settings============
	//$('#store_update_upto').val(varible_string_ref[5]);
}
function openmypage_addiInfo()
{
	var title = "Additional Info Details";
	var pre_addi_info = $('#txt_addi_info').val();
	page_link='requires/woven_finish_fabric_grn_roll_qc_controller.php?action=addi_info_popup&pre_addi_info='+pre_addi_info;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px, height=350px, center=1, resize=0, scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var addi_info_string=this.contentDoc.getElementById("txt_string").value;
		$('#txt_addi_info').val(addi_info_string);
	}
}
    function remove_audited(){
        $('#audited').html('');
    }

	function fn_load_floor(store_id)
	{
		var com_id=$('#cbo_company_id').val();
		var all_data=com_id + "__" + store_id;
		//alert(all_data);return;
		var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/woven_finish_fabric_grn_roll_qc_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		var JSONObject = JSON.parse(floor_result);
		for(var i=1; i<=tbl_length; i++)
		{
			$('#cboFloor_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject))
			{
				$('#cboFloor_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function fn_load_room(floor_id, sequenceNo)
	{
	
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=0;
		//var location_id=$('#cbo_location').val();
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
		//alert(all_data);return;
		var room_result = return_global_ajax_value(all_data, 'room_list', '', 'requires/woven_finish_fabric_grn_roll_qc_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(room_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(room_result);
		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#cboRoom_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#cboRoom_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function fn_load_rack(room_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=0;
		//var location_id=$('#cbo_location').val();
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + room_id;
		//alert(all_data);return;
		var rack_result = return_global_ajax_value(all_data, 'rack_list', '', 'requires/woven_finish_fabric_grn_roll_qc_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(rack_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(rack_result);

		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#txtRack_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txtRack_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function fn_load_shelf(rack_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=0;
		//var location_id=$('#cbo_location').val();
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + rack_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'shelf_list', '', 'requires/woven_finish_fabric_grn_roll_qc_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(shelf_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(shelf_result);

		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#txtShelf_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txtShelf_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function fn_load_bin(shelf_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + shelf_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'bin_list', '', 'requires/woven_finish_fabric_grn_roll_qc_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(shelf_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(shelf_result);

		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#txtBin_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txtBin_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}
	function reset_room_rack_shelf(id,fieldName)
	{
		var numRow=$('#table_body tbody tr').length;
		if (fieldName=="cbo_store_name") 
		{			
			$("#scanning_tbl").find('tbody tr').each(function()
			{
 				$(this).find('select[name="cboFloor[]"]').val("");
				$(this).find('select[name="cboRoom[]"]').val("");
				$(this).find('select[name="txtRack[]"]').val("");
				$(this).find('select[name="txtShelf[]"]').val("");
				$(this).find('select[name="txtBin[]"]').val("");
			});
		}
		else if (fieldName=="cbo_floor_to") 
		{
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				$(this).find('select[name="cboRoom[]"]').val("");
				$(this).find('select[name="txtRack[]"]').val("");
				$(this).find('select[name="txtShelf[]"]').val("");
				$(this).find('select[name="txtBin[]"]').val("");
			});
		}
		else if (fieldName=="cbo_room_to")  
		{
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				$(this).find('select[name="txtRack[]"]').val("");
				$(this).find('select[name="txtBin[]"]').val("");
			});
		}
		else if (fieldName=="txt_rack_to")  
		{
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				$(this).find('select[name="txtBin[]"]').val("");
			});
		}
	}

	function copy_all(str)
	{
		var data=str.split("_");
		
		var trall=$('#scanning_tbl tbody tr').length;
		
		var copy_tr=parseInt(trall);
	
		if($('#floorIds').is(':checked'))
		{
			if(data[1]==0) data_value=$("#cboFloor_"+data[0]).val();
		}
		if($('#roomIds').is(':checked'))
		{
			if(data[1]==1) data_value=$("#cboRoom_"+data[0]).val();
		}
		if($('#rackIds').is(':checked'))
		{
			if(data[1]==2) data_value=$("#txtRack_"+data[0]).val();
		}
		if($('#shelfIds').is(':checked'))
		{
			if(data[1]==3) data_value=$("#txtShelf_"+data[0]).val();
		}
		if($('#binIds').is(':checked'))
		{
			if(data[1]==4) data_value=$("#txtBin_"+data[0]).val();
		}

		var first_tr=parseInt(data[0])+1;
		
		for(var k=first_tr; k<=copy_tr; k++)
		{
			if($('#floorIds').is(':checked'))
			{
				console.log(data_value);
				if(data[1]==0) 	$("#cboFloor_"+k).val(data_value);
			}
			if($('#roomIds').is(':checked'))
			{
				if(data[1]==1) 	$("#cboRoom_"+k).val(data_value);
			}
			if($('#rackIds').is(':checked'))
			{
				if(data[1]==2) 	$("#txtRack_"+k).val(data_value);
			}
			if($('#shelfIds').is(':checked'))
			{
				if(data[1]==3) 	$("#txtShelf_"+k).val(data_value);
			}
			if($('#binIds').is(':checked'))
			{
				if(data[1]==4) 	$("#txtBin_"+k).val(data_value);
			}	
		}
	}

	function company_wise_load(company_id) 
	{
		rcv_basis_reset();
		//load_drop_down( 'requires/woven_finish_fabric_grn_roll_qc_controller', company_id, 'load_drop_down_supplier', 'supplier' );
		//load_drop_down( 'requires/woven_finish_fabric_grn_roll_qc_controller', company_id, 'load_drop_down_location', 'location_td');
		//load_drop_down( 'requires/woven_finish_fabric_grn_roll_qc_controller', company_id, 'load_drop_down_buyer', 'buyer_td');
		get_php_form_data( company_id,'company_wise_load' ,'requires/woven_finish_fabric_grn_roll_qc_controller');
		//get_php_form_data( company_id, 'company_wise_report_button_setting','requires/woven_finish_fabric_grn_roll_qc_controller' );//get_php_form_data(company_id,'roll_maintained','requires/woven_finish_fabric_grn_roll_qc_controller' );
		independence_basis_controll_function(company_id);
		wvn_finish_fabric_auto_batch_maintain_fnc(company_id);
	}

	function store_change(location)
	{
		var cbo_company_id = $("#cbo_company_id").val();
		load_drop_down( 'requires/woven_finish_fabric_grn_roll_qc_controller', cbo_company_id+'_'+location, 'load_drop_down_store', 'store_td');
	}

</script>
</head>
<body onLoad="set_hotkey();check_exchange_rate()">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
		<form name="yarn_receive_1" id="yarn_receive_1" autocomplete="off" > 
			<div style="width:880px;">       
				<fieldset style="width:1100px; float:left;">
					<legend>Woven Finish Fabric Receive</legend>
					<br />
					<fieldset style="width:1100px;">                                       
						<table width="1090" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
							<tr>
								<td colspan="4" align="right">&nbsp;<b>QC Number</b>&nbsp;&nbsp;
								</td>
								<td colspan="3" align="left">
									<input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:148px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly /> 
									<input type="hidden" name="update_id" id="update_id" />
								</td>
							</tr>
							<tr>
								<td colspan="10">&nbsp;</td>
							</tr>

							<tr>
								<td width="90" align="right" class="must_entry_caption"> GRN Number</td>
								<td align="left">
									<input type="text" name="txt_grn_no" id="txt_grn_no" class="text_boxes" style="width:148px" placeholder="Double Click To Search" onDblClick="open_grnpopup()" readonly /> 
									<input type="hidden" name="hdn_grn_id" id="hdn_grn_id" />
								</td>
								<td width="100" align="right" class="must_entry_caption">Company Name </td>
								<td width="140">
									<? 
									echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" ); 
									?>
								</td>
								<td  width="130" align="right" >Location </td>
								<td width="130" id="location_td">
									<? 
									echo create_drop_down( "cbo_location", 130, $blank_array,"", 1, "-- Select Location --", 0, "" );
									?>
								</td>
								<td width="100" align="right" class="must_entry_caption">Store Name</td>
								<td width="130" id="store_td">
									<? 
									echo create_drop_down( "cbo_store_name", 152, $blank_array,"", 1, "--Select store--", 0, "fn_load_floor(this.value);" );
									?>
								</td>
							</tr>
							<tr>
								<td width="94" align="right" class="must_entry_caption"> Supplier </td>
								<td id="supplier" width="130"> 
									<?
									echo create_drop_down( "cbo_supplier", 160, "select id,supplier_name from lib_supplier where FIND_IN_SET(2,party_type) order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",1 );
									?>
								</td>
								<td width="" align="right" class="must_entry_caption">QC Date </td>
								<td width="120">
									<input type="text" name="txt_receive_date" id="txt_receive_date" class="datepicker" style="width:120px;" value="<? echo date("d-m-Y"); ?>" placeholder="Select Date" readonly />
								</td>
							</tr>
						</table>
					</fieldset>
					<br />

					<fieldset style="width:1240px;">
						<style>
							#scanning_tbl tr td
							{
								
								color:#000;
								border: 1px solid #666666;
								line-height:12px;
								height:20px;
								overflow:auto;
							}
							.wrap_break {
								word-break: break-all;
								word-wrap: break-word;
							}
						</style>
						<table cellpadding="0" cellspacing="0" width="1220" border="1" id="tbl_child" class="rpt_table" rules="all">
							<thead>
								<th width="120">Body Part</th>
								<th width="120">Fabrication</th>
								<th width="80">UOM</th>
								<th width="100">Color</th>
								<th width="100">Weight</th>
								<th width="100">Actual Weight</th>
								<th width="100">Weight Type</th>
								<th width="100">Full Width</th>
								<th width="100">Cut. Width</th>
								<th width="100">Recv. Qnty.</th>
								<th width="100">Reject Qty</th>
								<th width="100">Balance PI/WO</th>
							</thead>
						</table>
						<div style="width:1240px; max-height:250px; overflow-y:scroll" align="left">
							<table cellpadding="0" cellspacing="0" width="1220" border="1" id="scanning_tbl" rules="all" class="rpt_table">
							<tbody id="list_product_container">
								<tr id="tr_1" align="center" valign="middle">
									<td width="120">&nbsp;</td>
									<td width="120">&nbsp;</td>
									<td width="80">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
								</tr>
							</tbody>
							</table>
						</div>
						<table cellpadding="0" cellspacing="0" width="1220" border="1" id="scanning_tbl" rules="all" class="rpt_table">
							<tfoot >
								<tr id="tr_1" align="center" valign="middle">
									<th width="120">&nbsp;</th>
									<th width="120">&nbsp;</th>
									<th width="80">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="100">Total :</th>
									<th width="100" id="total_qc_qty">&nbsp;</th>
									<th width="100" id="total_reject_qty">&nbsp;</th>
									<th width="100">&nbsp;</th>
								</tr>
							</tfoot>
							</table>
					</fieldset>
					<table cellpadding="0" cellspacing="1" width="100%">
						<tr> 
							<td colspan="6" align="center"></td>				
						</tr>
						<tr>
							<td align="center" colspan="6" valign="middle" class="button_container">
                                <div id="audited" style="float:left; font-size:24px; color:#FF0000;"></div>
								<input type="hidden" name="save_data" id="save_data" readonly>
								<input type="hidden" name="hdn_batch_control_variable" id="hdn_batch_control_variable" readonly>
								<input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>

								<? echo load_submit_buttons( $permission, "fnc_woben_finish_fab_receive_entry", 0,0,"fnResetForm();remove_audited();",1);?>
								<input type="button" name="print" id="print" value="Print" onClick="fnc_woben_finish_fab_receive_entry(4)" style="width: 80px; display:none;" class="formbutton">
							</td>
						</tr> 
					</table>

					<div style="width:870px;" id="list_container_yarn"></div>                 
				</fieldset>
			</div>
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

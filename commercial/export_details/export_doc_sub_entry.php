<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for Doc. Submission to Bank
					
Functionality	:	
				

JS Functions	:

Created by		:	Bilas 
Creation date 	: 	 
Updated by 		: 	Bilas/Jahid 
Update date		: 		   	   

QC Performed BY	:		

QC Date			:	

Comments		: according to requirment of Sayed bai such as validation change (Lc no popup & Bank Ref no popup) .

*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
//echo load_html_head_contents("Pre Export Finance Form", "../../", 1, 1,'','1','');
$commercial_head_reverse=array();
foreach($commercial_head as $key=>$val)
{
	$commercial_head_reverse[csf($val)]=csf($key);
	//$new_array[$result[csf($id_fld_name)]] = $result[csf($data_fld_name)];
}
echo load_html_head_contents("Doc. Submission to Bank","../../", 1, 1, $unicode,'','');
?>	
 
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';

var str_commercial_head = [<?  echo substr(return_library_autocomplete_fromArr( $commercial_head ), 0, -1); ?>];
function add_auto_complete(i)
{
	$("#cbo_account_head_"+i).autocomplete({
		source: str_commercial_head
	});
}
//
function fn_value_check(seq_no,fld_value,fld_names)
{
	//alert(fld_names+"="+seq_no+"="+commercial_head_reverse[fld_value]);return;
	if (form_validation('cbo_company_name','Company')==false )
	{
		$('#'+fld_names+'_'+seq_no).val("");
		return;
	}
	fld_value=fld_value.toUpperCase();
	
	var commercial_head_reverse = JSON.parse('<?  echo json_encode($commercial_head_reverse); ?>');
	//alert(fld_value+"="+commercial_head_reverse[fld_value]);
	if(commercial_head_reverse[fld_value]=="" || commercial_head_reverse[fld_value]==undefined)
	{
		alert("Transaction Account Head Not Found");
		$('#'+fld_names+'_'+seq_no).val("").attr('title','0').focus();
	}
	else
	{
		//alert(fld_names+"="+seq_no+"="+commercial_head_reverse[fld_value]);
		$('#'+fld_names+'_'+seq_no).attr('title',commercial_head_reverse[fld_value]);
	}
	get_php_form_data(commercial_head_reverse[fld_value]+'**'+seq_no+'**'+document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_lien_bank').value, 'populate_acc_loan_no_data', 'requires/export_doc_sub_entry_controller' );
}

function fn_commercial_head_display(seq_no,fld_name)
{
	if (form_validation('cbo_company_name','Company')==false )
	{
		return;
	}
	var page_link='requires/export_doc_sub_entry_controller.php?action=commercial_head_popup';
	var title='Account Head';
	$('#'+fld_name+"_"+seq_no).removeAttr('onblur');
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=380px,height=400px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var hdn_head_id=this.contentDoc.getElementById("hdn_head_id").value;
		var hdn_head_val=this.contentDoc.getElementById("hdn_head_val").value;
		$('#'+fld_name+"_"+seq_no).val(hdn_head_val).attr('title',hdn_head_id).attr("onblur","fn_value_check("+seq_no+",this.value,"+"'"+fld_name+"')");
		get_php_form_data(hdn_head_id+'**'+seq_no+'**'+document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_lien_bank').value, 'populate_acc_loan_no_data', 'requires/export_doc_sub_entry_controller' );
		//$('#'+fld_name+"_"+seq_no).val(hdn_head_val).attr("onblur","fn_value_check("+seq_no+","+head_from+",this.value,"+"'"+fld_name+"')");
	}
}


function openmypage_LcSc()
{
	var companyID = $("#cbo_company_name").val();
	var buyerID = $("#cbo_buyer_name").val();
 	var invoice_id_string = $("#invoice_id_string").val();
	var mst_tbl_id = $("#mst_tbl_id").val();
	var dtls_sub_ref_id = $("#dtls_sub_ref_id").val();
	//alert(mst_tbl_id);return;	  
	if (form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	//var page_link='requires/export_doc_sub_entry_controller.php?action=lcSc_popup_search&companyID='+companyID+'&buyerID='+buyerID+'&invoice_id_string='+invoice_id_string+'&mst_tbl_id='+mst_tbl_id+'&dtls_sub_ref_id='+dtls_sub_ref_id;
	var page_link='requires/export_doc_sub_entry_controller.php?action=lcSc_popup_search&companyID='+companyID+'&buyerID='+buyerID+'&mst_tbl_id='+mst_tbl_id;
	var title='Document Submission Form';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=430px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{ 
		var theform=this.contentDoc.forms[0];
		var all_invoice_id=this.contentDoc.getElementById("all_invoice_id").value;
		var all_sub_dtls_id=this.contentDoc.getElementById("all_sub_dtls_id").value;
		var import_btb_id=this.contentDoc.getElementById("import_btb_id").value;
		var hedden_lc_sc=this.contentDoc.getElementById("hedden_lc_sc").value;
		//var hidden_pay_term=this.contentDoc.getElementById("hidden_pay_term").value;
		
		//alert(all_invoice_id+"="+all_sub_dtls_id);
		$("#cbo_submission_type").val('');
		$("#cbo_submission_type").attr("disabled",false);
		$("#txt_negotiation_date").attr("disabled",false);
		if(all_sub_dtls_id!="")
		{
			//alert("jahid");
			$("#cbo_submission_type").val(1);
			fn_negotiation();
			//$("#cbo_submission_type").attr("disabled",true);
		}
 		$("#invoice_id_string").val(all_invoice_id);
		//$("#cbo_pay_term").val(hidden_pay_term);
		//alert(all_invoice_id);
		if(trim(all_invoice_id)!="")
		{
			freeze_window(5); 
			change_caption(import_btb_id)
			get_php_form_data(all_invoice_id+'**'+mst_tbl_id+'**'+hedden_lc_sc, "show_invoice_list_view", "requires/export_doc_sub_entry_controller" );
 			release_freezing();
		}
	}
}

function change_caption(import_btb)
{
	if(import_btb==1)
	{
		document.getElementById('buyer_td').innerHTML='<? echo create_drop_down( "cbo_buyer_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, " Display ", 0, "",1 ); ?>';
	}
	else
	{
		document.getElementById('buyer_td').innerHTML='<? echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy where buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, " Display ", 0, "",1 ); ?>';
	}
}


	

 
function fnc_export_doc_sub_entry(operation)
{
	
	
	if($("#is_posted_account").val()==1) {
		alert("This Information Already Posted In Accounting. Save Update and Delete Restricted.");
		return;
	}
	
	var sub_type=$('#cbo_submission_type').val();
	
	if(sub_type==2)
	{
		if (form_validation('cbo_company_name*cbo_buyer_name*lcsc_no*txt_submit_date*txt_negotiation_date','Company Name*Buyer Name*LC/SC No*Submission Date*Nagotiate Date')==false )
		{
			return;
		}
	}
	else
	{
		if (form_validation('cbo_company_name*cbo_buyer_name*lcsc_no*txt_submit_date*cbo_submission_type','Company Name*Buyer Name*LC/SC No*Submission Date*Submission Type')==false )
		{
			return;
		}
	}


	if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][40]); ?>') 
	{
		if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][40]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][40]); ?>')==false) {return;}
	}

	
	
	var submission_type=$('#cbo_submission_type').val();
	if(submission_type==2)
	{
		var txt_total=$('#txt_total').val()*1;
		var total_foreign_curr_hid=$('#total_foreign_curr_hid').val()*1;
		
		if(total_foreign_curr_hid>txt_total)
		{
			alert("Total LC/SC Currency Exceeds Total Net Invoice Value.");
			return;
		}
	}
	
	var dataString="";$('ss').attr('title')
	var invoiceRow = $('#invo_table tbody tr').length-1;
	for(var i=1;i<=invoiceRow;i++)
	{
		try 
		{
			dataString += "&txt_invoice_no"+i+"="+$("#txt_invoice_no"+i).text();
			dataString += "&hidden_invoice_id"+i+"="+$("#txt_invoice_no"+i).attr('title');
			dataString += "&hidden_sub_dtls_id"+i+"="+$("#txt_invoice_no"+i).attr('subdtls');
			dataString += "&txt_lcsc_id"+i+"="+$("#txt_lcsc_no"+i).attr('title');
			dataString += "&hidden_is_lc"+i+"="+$("#txt_lcsc_no"+i).attr('islc');
			dataString += "&txt_lcsc_no"+i+"="+$("#txt_lcsc_no"+i).text();
			dataString += "&txt_bl_no"+i+"="+$("#txt_bl_no"+i).text();
			dataString += "&txt_bl_date"+i+"="+$("#txt_bl_date"+i).text();
			dataString += "&txt_invoice_date"+i+"="+$("#txt_invoice_date"+i).text();
			dataString += "&txt_net_invo_value"+i+"="+$("#txt_net_invo_value"+i).text();
			dataString += "&hidden_po_numbers_id"+i+"="+$("#txt_po_numbers"+i).attr('title');
		}
		catch(e) 
		{
			//got error no operation
		}
	}
	//alert(dataString);return;
	var transRow = $('#trans_details tbody tr:last').attr('id');
	if(transRow && (transRow!="" || transRow!=null || transRow!="undefined") )
	{
		transRow = transRow.substring(2,transRow.length);
	}
	//alert(transRow);//return;
	for(var i=1;i<=transRow;i++)
	{
		try
		{
			if (form_validation('cbo_account_head_'+i+'*'+'txt_domestic_curr_'+i+'*'+'txt_conversion_rate_'+i,'Account Head*Domestic Currency*Conversation Rate')==false )
			{
				return;
			}
			dataString += "&cbo_account_head_"+i+"="+$("#cbo_account_head_"+i).attr('title');
			dataString += "&txt_ac_loan_no_"+i+"="+$("#txt_ac_loan_no_"+i).val();
			dataString += "&txt_loan_no_"+i+"="+$("#txt_loan_no_"+i).val();
			dataString += "&txt_domestic_curr_"+i+"="+$("#txt_domestic_curr_"+i).val();
			dataString += "&txt_conversion_rate_"+i+"="+$("#txt_conversion_rate_"+i).val();
			dataString += "&txt_lcsc_currency_"+i+"="+$("#txt_lcsc_currency_"+i).val();
		}
		catch(e)
		{
			//got error no operation
		}
	}
	//alert(dataString);return;
	//get_submitted_data_string('cbo_company_name*cbo_buyer_name*lcsc_no*lc_sc_id*txt_submit_date*cbo_submit_to*txt_bank_ref*txt_bank_ref_date*cbo_submission_type*txt_negotiation_date*txt_day_to_realize*txt_possible_reali_date*courier_receipt_no*txt_courier_company*txt_courier_date*txt_bnk_to_bnk_cour_no*txt_bnk_to_bnk_cour_date*cbo_lien_bank*cbo_currency*txt_remarks*total_dom_curr_hid*total_foreign_curr_hid*mst_tbl_id*invoice_tbl_id',"../../");		
	var data="action=save_update_delete&operation="+operation+'&invoiceRow='+invoiceRow+'&transRow='+transRow+dataString+get_submitted_data_string('cbo_company_name*cbo_buyer_name*lcsc_no*lc_sc_id*txt_submit_date*cbo_submit_to*txt_bank_ref*txt_bank_ref_date*cbo_submission_type*txt_negotiation_date*txt_day_to_realize*txt_possible_reali_date*courier_receipt_no*txt_courier_company*txt_courier_date*txt_bnk_to_bnk_cour_no*txt_bnk_to_bnk_cour_date*cbo_lien_bank*cbo_currency*cbo_pay_term*txt_remarks*total_dom_curr_hid*total_foreign_curr_hid*mst_tbl_id*invoice_tbl_id*all_buyer_sub_dtls_id*invoice_id_string*txt_import_btb*txt_issue_bank_info_dtls',"../../");
	//alert(data);return;
	freeze_window(operation);		
	http.open("POST","requires/export_doc_sub_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_export_doc_sub_entry_Reply_info;
}


function fnc_export_doc_sub_entry_Reply_info()
{
	$("#btn_print_letter").removeClass( "formbutton_disabled"); //To make disable print to button
	$("#btn_print_letter").addClass( "formbutton"); //To make enable print to button
	if(http.readyState == 4) 
	{
		//release_freezing();alert(http.responseText);return;
		var reponse=http.responseText.split('**');	
		if(reponse[0]==20)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}		 
		
		if( reponse[0]==0 || reponse[0]==1)
		{		
			get_php_form_data(reponse[1], "populate_master_from_data", "requires/export_doc_sub_entry_controller");
			set_button_status(1, permission, 'fnc_export_doc_sub_entry',1); 
			show_msg(trim(reponse[0])); 
			release_freezing();			 
		}
		if(reponse[0]==2)
		{
			$('#transaction_container').html("");
			$('#invoice_container').html("");
			$('#total_dom_curr_hid').val("");
			$('#total_foreign_curr_hid').val("");
			reset_form('docsubmFrm_1','','','','$(\'#transaction_container tr:not(:first)\').remove();','');
			set_button_status(0, permission, 'fnc_export_doc_sub_entry',1); 
			show_msg(trim(reponse[0])); 
			release_freezing();			 
		}
		else if(reponse[0]==35)
		{
			alert(reponse[1]);release_freezing();return;
		}
		else
		{
			show_msg(trim(reponse[0])); 
			release_freezing();
		}
		//fnResetForm();
		release_freezing();
		$(".printReport").removeClass( "formbutton_disabled");//To make disable print to button
		$(".printReport").addClass( "formbutton");//To make enable print to button
		
	}
}


function pop_doc_submission()
{
	if (form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var company_name = $("#cbo_company_name").val();
	var buyer_name = $("#cbo_buyer_name").val();
	var page_link='requires/export_doc_sub_entry_controller.php?action=doc_sub_popup&company_name='+company_name+'&buyer_name='+buyer_name; 
	var title="Search System Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var mstID=this.contentDoc.getElementById("hidden_system_number").value.split("**"); // master table id
		$("#cbo_submission_type").val('');
		$("#cbo_submission_type").attr("disabled",false);
		$("#txt_negotiation_date").attr("disabled",false);
		$("#is_posted_account").val(mstID[2]);
		if( mstID[2]==1 ) $("#posted_status_td").text("Already Posted In Accounting.");
		else $("#posted_status_td").text("");
		
		//alert(mstID[2]);
		/*if(mstID[2]==2)
		{
			$("#cbo_submission_type").val(mstID[2]);
			//fn_negotiation();
			$("#cbo_submission_type").attr("disabled",true);
			
		}*/
  		// master part call here		
		
		get_php_form_data(mstID[0], "populate_master_from_data", "requires/export_doc_sub_entry_controller");
		$('#dtls_sub_ref_id').val(mstID[1]);
		set_button_status(1, permission, 'fnc_export_doc_sub_entry',1);	
		release_freezing();
		//$("#id_print_to_button").removeClass( "formbutton_disabled"); //To make disable print to button
		//$("#id_print_to_button").addClass( "formbutton"); //To make enable print to button
		$(".printReport").removeClass( "formbutton_disabled"); //To make disable print to button
		$(".printReport").addClass( "formbutton"); //To make enable print to button	
  	}
}
	 
function fn_negotiation()
{	     
	var submit_type = $('#cbo_submission_type').val();
	if(submit_type=='1')
	{
		$('#txt_negotiation_date').attr('disabled','disabled');
		$('#txt_negotiated_ammount').attr('disabled','disabled');
		$('#transaction_container').find('tr').remove();
		$('#transaction_container').append('<tr id="tr0"><td colspan="7"><b><center>Please Select LC/SC to view Transaction</center></b></td> </tr>');
	}                       
	else if(submit_type=='2')
	{
		var rowNo=0;
		$('#txt_negotiation_date').removeAttr('disabled');
		$('#txt_negotiated_ammount').removeAttr('disabled');
		var response = return_global_ajax_value(rowNo, "transaction_add_row", "", "requires/export_doc_sub_entry_controller" );
		$('#transaction_container').find('tr').remove();
		$('#transaction_container').append(response);
	}
}
	
function fn_inc_decr_row(DelrowNo,type)
{
	if(type=='decrease')
	{
		$('#total_foreign_curr_hid').val(number_format(($('#total_foreign_curr_hid').val()*1)-($('#txt_lcsc_currency_'+DelrowNo).val()*1),4,".",""));
		$('#total_dom_curr_hid').val(number_format(($('#total_dom_curr_hid').val()*1)-($('#txt_domestic_curr_'+DelrowNo).val()*1),4,".",""));
	}
	
	var rownumber = $("#transaction_container tr:last").attr('id');
	var rowNo = rownumber.substr(2,rownumber.length);
	if(type=='increase')
	{
		var last_rate=$('#txt_conversion_rate_'+DelrowNo).val();
		var response = return_global_ajax_value(rowNo, "transaction_add_row", "", "requires/export_doc_sub_entry_controller" );
			$('#transaction_container').append(response);
		var last_row_no=DelrowNo+1;
		$('#txt_conversion_rate_'+last_row_no).val(last_rate);
	}
	else if(type=='decrease')
	{
       var tblRow = $("#transaction_container tr").length; 
	   if(tblRow*1!=1 && rowNo==DelrowNo)
	   { 
	   		$("#tr"+DelrowNo).remove();
	   }
	}		
}	
	
function realy_days(field_id)
{   
   var from_date = $('#txt_bank_ref_date').val();
   var days_to_realize = $('#txt_day_to_realize').val();
   var to_date = $('#txt_possible_reali_date').val();	  
   days_to_realize = days_to_realize*1-1;
   
   if(days_to_realize=="" || days_to_realize*1==-1) return;
   if(from_date !="" )
   {		
		if( (field_id == 'txt_day_to_realize' || field_id == 'txt_bank_ref_date') && days_to_realize!="")
		{ 
			var res_date = add_days( from_date, days_to_realize );
			$('#txt_possible_reali_date').val(res_date);
		}
		else if(field_id == 'txt_possible_reali_date' &&  to_date!="")
		{ 				 
			var datediff = date_diff( 'd', from_date, to_date )+1;					 
			$('#txt_day_to_realize').val(datediff);
		}
		
   }               
}	
	
function fn_calculate(id,rowNo)
{
	var domesticCurr 	= $("#txt_domestic_curr_"+rowNo).val();
	var domesticRate 	= $("#txt_conversion_rate_"+rowNo).val();
	var lcscCurr 		= $("#txt_lcsc_currency_"+rowNo).val();
		  
	if(id=="txt_domestic_curr_"+rowNo)
	{
		if( ($("#txt_conversion_rate_"+rowNo).val()=="" || $("#txt_conversion_rate_"+rowNo).val()==0) && ( $("#txt_lcsc_currency_"+rowNo).val()!="" || $("#txt_lcsc_currency_"+rowNo).val()>0) )
		{
			domesticRate = domesticCurr*1/lcscCurr*1;
			$("#txt_conversion_rate_"+rowNo).val(number_format_common(domesticRate,4,'',''));
		}
		else if( ($("#txt_conversion_rate_"+rowNo).val()!="" || $("#txt_conversion_rate_"+rowNo).val()>0) && ($("#txt_lcsc_currency_"+rowNo).val()=="" || $("#txt_lcsc_currency_"+rowNo).val()==0))
		{
			lcscCurr = domesticCurr*1/domesticRate*1;
			$("#txt_lcsc_currency_"+rowNo).val(number_format_common(lcscCurr,5,'',''));
		}
		else
		{
			lcscCurr = domesticCurr*1/domesticRate*1;
			$("#txt_lcsc_currency_"+rowNo).val(number_format_common(lcscCurr,5,'',''));
		}
	} 
	else if(id=="txt_conversion_rate_"+rowNo)
	{
		if( ($("#txt_domestic_curr_"+rowNo).val()=="" || $("#txt_domestic_curr_"+rowNo).val()==0) && ($("#txt_lcsc_currency_"+rowNo).val()!="" || $("#txt_lcsc_currency_"+rowNo).val()>0) )
		{
			domesticCurr = domesticRate*1*lcscCurr*1;
			$("#txt_domestic_curr_"+rowNo).val(number_format_common(domesticCurr,4,'',''));
		}
		else if( ($("#txt_domestic_curr_"+rowNo).val()!="" || $("#txt_domestic_curr_"+rowNo).val()>0) && ($("#txt_lcsc_currency_"+rowNo).val()=="" || $("#txt_lcsc_currency_"+rowNo).val()==0) )
		{
			lcscCurr = domesticCurr*1/domesticRate*1;
			$("#txt_lcsc_currency_"+rowNo).val(number_format_common(lcscCurr,5,'',''));
		}
		else
		{
			lcscCurr = domesticCurr*1/domesticRate*1;
			$("#txt_lcsc_currency_"+rowNo).val(number_format_common(lcscCurr,5,'',''));
		}
	} 
	else if(id=="txt_lcsc_currency_"+rowNo)
	{ 
		if( ($("#txt_domestic_curr_"+rowNo).val()=="" || $("#txt_domestic_curr_"+rowNo).val()==0) && ($("#txt_conversion_rate_"+rowNo).val()!="" || $("#txt_conversion_rate_"+rowNo).val()>0) )
		{
			domesticCurr = lcscCurr*1*domesticRate*1;
			$("#txt_domestic_curr_"+rowNo).val(number_format_common(domesticCurr,5,'',''));
		}
		else if( ($("#txt_domestic_curr_"+rowNo).val()!="" || $("#txt_domestic_curr_"+rowNo).val()>0) && ($("#txt_conversion_rate_"+rowNo).val()=="" || $("#txt_conversion_rate_"+rowNo).val()==0) )
		{
			domesticRate = domesticCurr*1/lcscCurr*1;
			$("#txt_conversion_rate_"+rowNo).val(number_format_common(domesticRate,4,'',''));
		}
		else
		{
			domesticCurr = lcscCurr*1*domesticRate*1;
			$("#txt_domestic_curr_"+rowNo).val(number_format_common(domesticCurr,5,'',''));
		}
	} 
	
	//total domestic and foreign currency function call
		sum_of_currency();
}	
	
function sum_of_currency()
{
	//total domestic and foreign currency
	var totalDomCurr = totalForeignCurr = 0;
	$("#trans_details tbody tr").each(function() {
         totalDomCurr += $(this).find("input[name='txt_domestic_curr[]']").val()*1; 
		 totalForeignCurr += $(this).find("input[name='txt_lcsc_currency[]']").val()*1; 
    });
	$("#total_dom_curr_hid").val(number_format_common(totalDomCurr,4,'',''));
	$("#total_foreign_curr_hid").val(number_format_common(totalForeignCurr,5,'',''));
}	
	
//reset/refresh function 
function fnResetForm()
{
	reset_form('docsubmFrm_1','','','','$(\'#transaction_container tr:not(:first)\').remove();','');
	$('#invo_table').find('tr:gt(0)').remove();
	$('#invoice_container').append('<tr id="tr0"><td colspan="6"><b><center>Please Select LC/SC to view invoice List</center></b></td></tr>');
	$('#trans_details').find('tr:gt(0)').remove();
	$('#trans_details thead').after('<tbody id="transaction_container"><tr id="tr0"><td colspan="7"><b><center>Please Select LC/SC to view Transaction</center></b></td></tr></tbody><tfoot><th colspan="3">Sum&nbsp;&nbsp;</th><td><input type="text" id="total_dom_curr_hid" class="text_boxes_numeric" style="width:100px" disabled readonly /></td><th>&nbsp;</th>	<td><input type="text" id="total_foreign_curr_hid" class="text_boxes_numeric" style="width:100px" disabled readonly /></td><th></th></tfoot>');
		set_button_status(0, permission, 'fnc_export_doc_sub_entry',1);	
	$("#posted_status_td").text("");
}
	
                    	 	                        	
                        
 /*   function print_to_html_report(type)
	{
		if (form_validation('mst_tbl_id','Save Data First')==false)
		{
			alert("Save Data First");
			return;
		}*/
		//else
		//{
			
		//	print_report( $('#mst_tbl_id').val()+"__"+$('#cbo_company_name').val()+"__"+type, "bank_submit_letter", "requires/export_doc_sub_entry_controller" );
			
			/*if(type==1)
			{
			 	print_report( $('#mst_tbl_id').val()."__".type, "bank_submit_letter", "requires/export_doc_sub_entry_controller" );
			}
         	else if(type==2)
			{
			 	print_report( $('#mst_tbl_id').val()."__".type, "bank_submit_letter_2", "requires/export_doc_sub_entry_controller" ) ;
			}*/
		//}
	//}
       
	   
	   
function print_to_html_report(type)
{
	var msg="";
	if (type != 8)
	{
		if (type != 7)
		{
			if (confirm("Press  \"Cancel\"  to hide  Discounting As  Per  LC Clause \nPress  \"OK\"  to Show Discounting As  Per  LC Clause ")) 
			{
				msg=1;
			} 
			else 
			{
				msg=0;
			}
		}
	}
	
	if (form_validation('mst_tbl_id','Save Data First')==false)
	{
		alert("Save Data First");
		return;
	}
	else
	{
		// var report_title=$( "div.form_caption" ).html();
		// window.open("requires/export_doc_sub_entry_controller.php?data=" + $('#mst_tbl_id').val()+'__'+$('#cbo_company_name').val()+'__'+type+'__'+report_title+'__'+msg+'&action='+"bank_submit_letter", true );
		if(type==9){
			var action='bank_submit_letter'
			var report_title=$( "div.form_caption" ).html();
			var data="action="+action+get_submitted_data_string('mst_tbl_id*cbo_company_name*txt_bank_ref*txt_submit_date*cbo_lien_bank*cbo_pay_term*cbo_buyer_name',"../../")+'&report_title='+report_title+'&type='+type+'&msg='+msg;
			http.open("POST","requires/export_doc_sub_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;	
		}
		else{
			var action='bank_submit_letter'
			var report_title=$( "div.form_caption" ).html();
			var data="action="+action+get_submitted_data_string('mst_tbl_id*cbo_company_name',"../../")+'&report_title='+report_title+'&type='+type+'&msg='+msg;
			http.open("POST","requires/export_doc_sub_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;	
		}
	}
}	


function generate_trim_report_reponse(){
	if(http.readyState == 4){
        release_freezing();
		var file_data=http.responseText.split("####");
        //  alert(file_data[0]);
		$('#data_panel').html(file_data[0]);
		if(file_data[2]==8){
			$('#print_excel19').removeAttr('href').attr('href','requires/'+trim(file_data[1]));
				//$('#print_excel19')[0].click();
			document.getElementById('print_excel19').click();
		}
        var report_title=$( "div.form_caption" ).html();
        var w = window.open("Surprise", "_blank");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title>'+report_title+'</title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
        d.close();
	}
}

	                   	
function open_terms_acc_popup(title)
{
	var txt_bank_ref=document.getElementById('txt_bank_ref').value;
	if (txt_bank_ref=="")
	{
		alert("Save The Data First")
		return;
	}	
	else
	{
		page_link='requires/export_doc_sub_entry_controller.php?action=acc_popup'+get_submitted_data_string('txt_bank_ref*mst_tbl_id','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title,'width=720px,height=470px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			//var theform=this.contentDoc.forms[0];
			//var theemail=this.contentDoc.getElementById("selected_data").value;
			//document.getElementById('trims_acc_hidden_data').value=theemail;
		}
	}
}   
       
	   
function print_button_setting()
{
	//$('#button_data_panel').html('');
	get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/export_doc_sub_entry_controller' );
}

function fn_bank_info()
{
	var txt_issue_bank_info_dtls=$('#txt_issue_bank_info_dtls').val();
	//var page_link='requires/export_doc_sub_entry_controller.php?action=doc_sub_popup&company_name='+company_name+'&buyer_name='+buyer_name; 
	var page_link='requires/export_doc_sub_entry_controller.php?action=bank_info_popup&txt_issue_bank_info_dtls='+txt_issue_bank_info_dtls;
	var title="Issuing Bank Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title,'width=720px,height=300px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("hdn_issue_bank_info_dtls").value;
		document.getElementById('txt_issue_bank_info_dtls').value=theemail;
		var txt_issue_bank_info_dtls_ref=theemail.split("__");
		document.getElementById('txt_issue_bank_info').value=txt_issue_bank_info_dtls_ref[0];
	}
}

	                 
</script>
 
 
</head> 
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">																	
     	<? echo load_freeze_divs ("../../",$permission); ?><br/>
        <fieldset style="width:930px; margin-bottom:10px;">
        <form name="docsubmFrm_1" id="docsubmFrm_1" autocomplete="off" method="POST"  >
        	 
            	<!-- 1st form Start here -->
          <fieldset style="width:950px;">
            <legend>Commercial Module</legend>
              <table  width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master"> 
                 <tr>
                      <td  width="130" align="right" class="must_entry_caption">Company Name </td>
                      <td width="170">
                          <?
								echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "print_button_setting();" );//load_drop_down( 'requires/export_doc_sub_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );
								
								
									//echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "load_drop_down( 'requires/yarn_work_order_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );print_button_setting();" );
							?> 
                      </td>
                      <td width="130" align="right" class="must_entry_caption" >Buyer</td>
                      <td width="160" id="buyer_td">
                            <? 
								//echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select --", $selected, "" );
								echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, " Display ", 0, "",1 );
							?>
                      </td>
                      <td width="130" align="right" class="must_entry_caption">LC/SC No </td>
                      <td width="170">
                          <input type="text" name="lcsc_no" id="lcsc_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search"  readonly onDblClick="openmypage_LcSc();" />
                          <input type="hidden" name="lc_sc_id" id="lc_sc_id" readonly />
                          <input type="hidden" name="invoice_id_string" id="invoice_id_string" readonly />
                          <input type="hidden" name="is_posted_account" id="is_posted_account"/>
                          <input type="hidden" name="txt_import_btb" id="txt_import_btb"/>
                       </td>
                </tr>
                <tr>                                       
                     <td  width="130" align="right" class="must_entry_caption">Submission Date</td>
                     <td width="170">
                         <input style="width:140px " name="txt_submit_date" id="txt_submit_date" value="<?echo date('d-m-Y')?>" class="datepicker"  placeholder="Select Date" />
                     </td>
                     <td width="130" align="right" > Submitted To </td>
                     <td width="160">
                       	  <? 
                                echo create_drop_down( "cbo_submit_to", 150, $submited_to,"", 1, "-- Select --", 1, "",1 );
                                ?>
                     </td>
                     <td width="130" align="right">Bank Ref/ Bill No </td>
                     <td width="170">
                          <input style="width:140px " name="txt_bank_ref" id="txt_bank_ref" class="text_boxes" placeholder="Double Click to Update"  ondblclick="pop_doc_submission();"><input type="hidden" name="dtls_sub_ref_id" id="dtls_sub_ref_id" readonly />
                     </td>
                </tr>
                <tr>
                  
                        <td width="130" align="right">Bank Ref Date </td>
                        <td width="170">
                          <input type="text" name="txt_bank_ref_date" id="txt_bank_ref_date" class="datepicker" onChange="realy_days(this.id)" style="width:140px"   />
                        </td>
                       <td width="130" align="right" class="must_entry_caption">Submission Type </td>
                       <td width="160" > 
                           	<? 
                                echo create_drop_down( "cbo_submission_type", 150, $submission_type,"", 1, "-- Select --", 0, "fn_negotiation()" );
                            ?>
                       </td>
                       <td width="130" align="right">Negotiation Date</td>
                       <td width="170"> 
                         <input type="text" name="txt_negotiation_date" id="txt_negotiation_date" class="datepicker" style="width:140px"   />
                       </td>
                </tr> 
                <tr>                                   
                        <td  width="130" align="right">Days to Realize </td>
                        <td width="170">
                            <input type="text" name="txt_day_to_realize" id="txt_day_to_realize" class="text_boxes_numeric" style="width:140px" onChange="realy_days(this.id)" />	
                        </td>
                        <td width="130" align="right" >Possible Reali Date</td>
                       <td width="160">  
                         <input type="text" name="txt_possible_reali_date" id= "txt_possible_reali_date" class="datepicker" onChange="realy_days(this.id)" style="width:140px" />
                       </td>
                       <td width="130" align="right">Courier Receipt No.</td>
                       <td width="170"><input type="text" name="courier_receipt_no" id="courier_receipt_no" class="text_boxes" style="width:140px" /></td>                                   
                </tr>
                <tr>                                   
                       	<td width="130" align="right">Courier Company </td>
                        <td width="170"><input type="text" name="txt_courier_company" id="txt_courier_company" class="text_boxes" style="width:140px" /></td>
                        <td width="130" align="right" >GSP Courier Date</td>
                       	<td width="160">  <input type="text" name="txt_courier_date" id="txt_courier_date" class="datepicker" style="width:140px" /> </td>
                        <td width="130" align="right">Bnk-2-Bnk Cour No</td>
                        <td width="170"><input type="text" name="txt_bnk_to_bnk_cour_no" id="txt_bnk_to_bnk_cour_no" class="text_boxes" style="width:140px" /></td>                                   
                </tr>
                <tr>                                   
                        <td  width="130" align="right">Bnk-2-Bnk Cour Date</td>
                        <td width="170"><input type="text" name="txt_bnk_to_bnk_cour_date" id="txt_bnk_to_bnk_cour_date" class="datepicker" style="width:140px" /></td>
                        <td width="130" align="right" >Lien Bank </td>
                        <td width="160">  
                          	<?  
								if ($db_type==0)
								{
									echo create_drop_down( "cbo_lien_bank", 150, "select id,concat(a.bank_name,' (', a.branch_name,')') as bank_name from lib_bank where lien_bank=1 and is_deleted = 0 AND status_active = 1 order by bank_name","id,bank_name", 1, "-- Select --", $selected, "",1 );
								}
								else
								{
									echo create_drop_down( "cbo_lien_bank", 150, "select id,(bank_name || ' (' || branch_name || ')' ) as bank_name from lib_bank where lien_bank=1 and is_deleted = 0 AND status_active = 1 order by bank_name","id,bank_name", 1, "-- Select --", $selected, "",1 );
								}
                            ?>
                       </td>
                       <td width="130" align="right">LC/SC Currency</td>
                       <td width="170"><?
                               echo create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select Currency --", $currencyID, "",1 );
                            ?></td>                                   
                </tr>
                <tr>                                   
                        <td  width="130" align="right">Remarks</td>
                        <td> <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px" /></td>
                        <td align="right">Pay Term</td>
                        <td>
                            <?
                            echo create_drop_down("cbo_pay_term", 150, $pay_term, "", 1, "--- Select ---", 0, "", 0, '1,2');
                            ?>
                        </td>
                        <td align="right">Issuing Bank Info</td>
                        <td>
                        <input type="text" name="txt_issue_bank_info" id="txt_issue_bank_info" class="text_boxes" style="width:140px" onDblClick="fn_bank_info()" placeholder="Browse" readonly />
                        <input type="hidden" name="txt_issue_bank_info_dtls" id="txt_issue_bank_info_dtls" />
                        </td>
                </tr>
                <tr>
                		<td>&nbsp;</td>                                   
                        <td><input type="button" id="set_button" class="image_uploader" style="width:140px;" value="Terms" onClick="open_terms_acc_popup('Terms Dtls')" /></td>                       
                        <td>&nbsp;</td>
                        <td><input type="button" id="image_button" class="image_uploader" style="width:151px;" value="CLICK TO ADD FILE" onClick="file_uploader('../../', document.getElementById('mst_tbl_id').value, '', 'Doc. Submission to Bank', 2, 1)" /></td>
                        <td id="posted_status_td" style="color:red; font-size:24px; text-align:center"></td>
                        <td>&nbsp;</td>
                </tr>
           
            </table>
            </fieldset>
            
            <br /> 
            
            <fieldset>
                <legend>Invoice List</legend>
                <table id="invo_table" width="900" class="rpt_table" rules="all">
                     <thead>
                        <tr>
                        	<input type="hidden" id="all_buyer_sub_dtls_id">
                            <th width="50">SL</th>
                            <th width="120">Invoice No</th>
                            <th width="200">LC/SC No</th>
                            <th width="90">BL No</th>
                            <th width="80">BL Date</th>
                            <th width="80">Invoice Date</th>
                            <th width="100">Net Inv. Value</th>
                            <th width="200"> Order Numbers</th>
                        </tr>    
                    </thead>
    				<tbody id="invoice_container">
                        <tr id="tr0">
                            <td colspan="7">
                            <b><center>Please Select LC/SC to view invoice List</center></b>
                            </td>
                        </tr>
                    </tbody>    
                </table>   
            </fieldset>         
            
            <br /> 
                   
            <fieldset>
                <legend>Transaction Details</legend>
                <table id='trans_details' width="900" class="rpt_table" rules="all">
                    <thead id='trans'>
                    	<tr>
                            <th width="220" >Account Head</th>
                            <th width="120" >AC No.</th>
                            <th width="120" >Loan No.</th>
                            <th width="120" >Domestic Currency</th>
                            <th width="120" >Conversation Rate</th>
                            <th width="120" >LC/SC Currency</th>
                            <th width="80" >Action</th>
                        </tr>    
                    </thead>
                    <tbody id="transaction_container">                         
                        <tr id="tr0">
                            <td colspan="7"><b><center>Please Select LC/SC to view Transaction</center></b></td>
                        </tr>
                    </tbody> 
                    <tfoot>
                    	<th colspan="3">Sum&nbsp;&nbsp;</th> 	                        	
                        <td><input type="text" id="total_dom_curr_hid" class="text_boxes_numeric" style="width:100px" disabled readonly /></td>	
                        <th>&nbsp;</th>	
                        <td><input type="text" id="total_foreign_curr_hid" class="text_boxes_numeric" style="width:100px" disabled readonly /></td>	
                        <th></th>
                     </tfoot>
                 </table>
            </fieldset>         
                      
            <table cellpadding="0" cellspacing="1" width="100%">
                <tr> 
                   <td colspan="7" align="center"></td>
                </tr>
                <tr>
                    <td align="center" colspan="7" valign="middle" class="button_container">
                          <!-- details table id for update -->
                          <input type="hidden" id="mst_tbl_id" name="mst_tbl_id" value="" />
                          <input type="hidden" id="invoice_tbl_id" name="invoice_tbl_id" value="" />&nbsp;<a id="print_excel19" href="" style="text-decoration:none" download hidden>PP</a>					
                           <!-- -->
                         <? echo load_submit_buttons( $permission, "fnc_export_doc_sub_entry", 0,0,"fnResetForm()",1);?>
                         
                         <span id="button_data_panel"></span>
                         <!--<input type="button" class="formbutton" id="btn_print_letter" value="Print letter" style="width:100px;" onClick="fn_print_letter(1)" >
                         <input type="button" class="formbutton" id="btn_print_letter2" value="Print letter 2" style="width:100px;" onClick="fn_print_letter(2)" >-->
                    </td>
               </tr> 
            </table>   
        	
         </form>
        </fieldset> 
 	</div>
	 <div id="data_panel"></div>
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
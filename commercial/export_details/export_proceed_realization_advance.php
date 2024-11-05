<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for Export Proceed Realization
					
Functionality	:	
				

JS Functions	:

Created by		:	Fuad Shahriar	 
Creation date 	: 	04-06-2013
Updated by 		: 			
Update date		: 	Jahid	   	   

QC Performed BY	:		

QC Date			:	

Comments		: according to requirment of Sayed bhai such as validation change (entry search popup & Invoice no popup) & Store value for next (deducttion &distribution) convertion rate.

*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//asort($commercial_head);
//$commercial_head_reverse=array_flip($commercial_head);
unset($commercial_head[177]);
$commercial_head_reverse=array();
foreach($commercial_head as $key=>$val)
{
	$commercial_head_reverse[csf($val)]=csf($key);
	//$new_array[$result[csf($id_fld_name)]] = $result[csf($data_fld_name)];
}
//$commercial_head_reverse=$commercial_head;
//print_r($commercial_head_reverse);die;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Export Proceeds Realization Form", "../../", 1, 1,'','1','');
?>	
 
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';
var str_commercial_head = [<?  echo substr(return_library_autocomplete_fromArr( $commercial_head ), 0, -1); ?>];
function add_auto_complete(i)
{
	$("#cbodeductionHead_"+i).autocomplete({
		source: str_commercial_head
	});
	$("#cbodistributionHead_"+i).autocomplete({
		source: str_commercial_head
	});
}

function openmypage_realizationUpdate()
{
	var buyerID = $("#cbo_buyer_name").val();
	var beneficiary_name = $("#cbo_beneficiary_name").val();
	
	if (form_validation('cbo_beneficiary_name','Company')==false )
	{
		return;
	}
	//alert(buyerID);
	var page_link='requires/export_proceed_realization_advance_controller.php?action=proceed_realization_popup_search&beneficiary_name='+beneficiary_name+'&buyerID='+buyerID;
	var title='Export Proceeds Realization Entry Form';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=400px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var realization_id=this.contentDoc.getElementById("hidden_realization_id").value;
		var invoice_bill_id=this.contentDoc.getElementById("hidden_invoice_bill_id").value;
		var is_invoiceBill=this.contentDoc.getElementById("hidden_is_invoiceBill").value;
		var is_posted_accounts=this.contentDoc.getElementById("hidden_is_posted_account").value;
	
		if( is_posted_accounts==1 ) $("#posted_status_td").text("Already Posted In Accounting.");
		else $("#posted_status_td").text("");
			//alert(is_posted_accounts);
		
		if(trim(realization_id)!="")
		{
			freeze_window(5);
			reset_form('exportProceedRealizationFrm_1','','','deduction_tot_row,1*distribution_tot_row,1','$(\'#tbl_deduction tbody tr:not(:first)\').remove();$(\'#tbl_distribution tbody tr:not(:first)\').remove();','cbo_beneficiary_name*cbo_buyer_name');
			get_php_form_data(invoice_bill_id+"**"+is_invoiceBill+"**"+realization_id, "populate_data_from_invoice_bill", "requires/export_proceed_realization_advance_controller" );
			
			var submit_type=$('#submit_type').val();
			$("#hidden_posted_account").val(is_posted_accounts);
			show_list_view( realization_id+"**0", 'details_list_view', 'deduction_list_view', 'requires/export_proceed_realization_advance_controller', '' ) ;
			var variable_distribution=$("#hdn_variable_distribution").val();
			var bank_id=$("#is_invoice_bill_lien_bank").val();
			//fn_set_variable(beneficiary_name,bank_id);
			show_list_view( realization_id+"**1**"+submit_type+"**"+variable_distribution, 'details_list_view', 'distribution_list_view', 'requires/export_proceed_realization_advance_controller', '' ) ;	
			
			var deduction_tot_row = $('#tbl_deduction tbody tr').length; 
			var distribution_tot_row = $('#tbl_distribution tbody tr').length; 
			
			$('#deduction_tot_row').val(deduction_tot_row);
			$('#distribution_tot_row').val(distribution_tot_row);
			
			calculate_total('tbl_deduction','deduction');
			calculate_total('tbl_distribution','distribution');
			calculate_grand_total();			
			release_freezing();
		}
					 
	}
}


function openmypage_AccountNo(ac_id)
{
	var ex_id = ac_id.split("_");
	var beneficiary_name = $("#cbo_beneficiary_name").val();
	var lien_bank = $("#is_invoice_bill_lien_bank").val();
	var acheadval = $("#cbodistributionHead_"+ex_id[1]).attr("acheadval");

	if (form_validation('cbo_beneficiary_name','Beneficiary')==false )
	{
		return;
	}
	
	var page_link='requires/export_proceed_realization_advance_controller.php?action=AccountNo_popup&beneficiary_name='+beneficiary_name+'&lien_bank='+lien_bank+'&acheadval='+acheadval;
	var title='Account No Form';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=300px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var hdn_account_no=this.contentDoc.getElementById("hdn_account_no").value;
		$("#"+ac_id).val(hdn_account_no);
	}
}

function openmypage_InvoiceBill()
{
	var buyerID = $("#cbo_buyer_name").val();
	var beneficiary_name = $("#cbo_beneficiary_name").val();
	var import_btb = $("#txt_import_btb").val();
	
	if (form_validation('cbo_beneficiary_name','Beneficiary')==false )
	{
		return;
	}
	
	var page_link='requires/export_proceed_realization_advance_controller.php?action=invoice_bill_popup_search&beneficiary_name='+beneficiary_name+'&buyerID='+buyerID+'&import_btb='+import_btb;
	var title='Export Proceeds Realization Entry Form';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px,height=400px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var invoice_bill_id=this.contentDoc.getElementById("hidden_invoice_bill_id").value;
		var is_invoiceBill=this.contentDoc.getElementById("hidden_is_invoiceBill").value;
		var lc_id=this.contentDoc.getElementById("hidden_lc_id").value;
		var lc_type=this.contentDoc.getElementById("hidden_lc_type").value;
		var is_invoiceLienBank=this.contentDoc.getElementById("hidden_is_invoiceLienBank").value;
		//var partial_rlz_permission=this.contentDoc.getElementById("partial_rlz_permission").value;
		
		//alert(lc_id+"__"+lc_type);

		if(trim(invoice_bill_id)!="")
		{
			freeze_window(5);
			reset_form('exportProceedRealizationFrm_1','','','deduction_tot_row,1*distribution_tot_row,1','$(\'#tbl_deduction tbody tr:not(:first)\').remove();$(\'#tbl_distribution tbody tr:not(:first)\').remove();','cbo_beneficiary_name*cbo_buyer_name');
			$("#lc_id").val(lc_id);
			$("#lc_type").val(lc_type);
			get_php_form_data(invoice_bill_id+"**"+is_invoiceBill+"****"+lc_id+"**"+lc_type, "populate_data_from_invoice_bill", "requires/export_proceed_realization_advance_controller" );
			//var com_id=$("#cbo_beneficiary_name").val();
			//var bank_id=$("#is_invoice_bill_lien_bank").val();
			//$("#buyer_partial_rlz").val(partial_rlz_permission);
			//fn_set_variable(com_id,bank_id);
			calculate_total('tbl_distribution','distribution');
			calculate_grand_total();
			release_freezing();
		}
	}
}

function fnc_invoiceDetails()
{
	var invoice_bill_id = $("#invoice_bill_id").val();
	var buyerID = $("#cbo_buyer_name").val();
	var beneficiary_name = $("#cbo_beneficiary_name").val();
	var import_btb = $("#txt_import_btb").val();

	if (invoice_bill_id)
	{
		var page_link='requires/export_proceed_realization_advance_controller.php?action=invoice_details_popup&invoice_bill_id='+invoice_bill_id+'&beneficiary_name='+beneficiary_name+'&buyerID='+buyerID;
		var title='Invoice Details';	
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=540px,height=300px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{}
	}
	else
	{
		alert("Please Browse Bill/Invoice No Popup !!");
		return;
	}	
}

function fnc_export_proceed_realization(operation) 
{ 
	
	
	if($("#hidden_posted_account").val()==1) {
		alert("This Information Already Posted In Accounting. Save Update and Delete Restricted.");
		return;
	}
		
	if ( form_validation('cbo_beneficiary_name*cbo_buyer_name*txt_invoice_bill_no*txt_received_date*cbo_currency_name','Beneficiary*Buyer*Bill/Invoice No*Received Date*Currency')==false )
	{
		return;
	}
	else
	{
		var received_date=$('#txt_received_date').val().split("-");
		if(received_date[1]==undefined && received_date[2]==undefined)
		{
			alert("Please Insert Receive Date With Right Format");return;
		}
		
		var imv_amt=$('#txt_bill_invoice_amnt').val()*1;
		var grand_document_amt=$('#grand_total_document_currency').val()*1;
		//function number_format (number, decimals, dec_point, thousands_sep) 
		
		if(operation==2)
		{
			if(confirm("Do You Want To Delete")==false)
			{
				return;
			}
		}
		else
		{
			if(grand_document_amt >imv_amt)
			{
				alert("Realized Amount Not Allow More Then Bill Amount");return;
			}
		}
		
		var dataString_deduction=""; var dataString_distribution="";
		var j=0; var z=0; var tot_row=0;
		$("#tbl_deduction").find('tbody tr').each(function()
		{
			var cbodeductionHead=$(this).find('input[name="cbodeductionHead[]"]').attr('acHeadVal');
			
			var deductionDocumentCurrency=$(this).find('input[name="deductionDocumentCurrency[]"]').val();
			var deductionConversionRate=$(this).find('input[name="deductionConversionRate[]"]').val();
			var deductionDomesticCurrency=$(this).find('input[name="deductionDomesticCurrency[]"]').val();
			if(deductionDomesticCurrency>0)
			{
				if(cbodeductionHead==0 || cbodeductionHead=="" || cbodeductionHead==undefined)
				{
					//alert("please Select Deductions Account Head");		
					$(this).find('input[name="cbodeductionHead[]"]').focus();		
					return;
				}
				else
				{
					j++;
					tot_row++;
					dataString_deduction+='&cbodeductionHead_' + j + '=' + cbodeductionHead + '&deductionDocumentCurrency_' + j + '=' + deductionDocumentCurrency + '&deductionConversionRate_' + j + '=' + deductionConversionRate+ '&deductionDomesticCurrency_' + j + '=' + deductionDomesticCurrency;
				}
			}
			
		});
		
		$("#tbl_distribution").find('tbody tr').each(function()
		{
			var cbodistributionHead=$(this).find('input[name="cbodistributionHead[]"]').attr('acHeadVal');
			var acLoanNo=$(this).find('input[name="acLoanNo[]"]').val();
			var txtdispersent=$(this).find('input[name="txtdispersent[]"]').val();
			var distributionDocumentCurrency=$(this).find('input[name="distributionDocumentCurrency[]"]').val();
			var distributionConversionRate=$(this).find('input[name="distributionConversionRate[]"]').val();
			var distributionDomesticCurrency=$(this).find('input[name="distributionDomesticCurrency[]"]').val();
			if(distributionDomesticCurrency>0)
			{
				if(cbodistributionHead==0 || cbodistributionHead=="" || cbodistributionHead==undefined)
				{
					//alert("please Select Distributions Account Head");		
					$(this).find('input[name="cbodistributionHead[]"]').focus();		
					return;
				}
				else
				{
					z++;
					tot_row++;
					dataString_distribution+='&cbodistributionHead_' + z + '=' + cbodistributionHead + '&acLoanNo_' + z + '=' + acLoanNo+ '&txtdispersent_' + z + '=' + txtdispersent + '&distributionDocumentCurrency_' + z + '=' + distributionDocumentCurrency + '&distributionConversionRate_' + z + '=' + distributionConversionRate+ '&distributionDomesticCurrency_' + z + '=' + distributionDomesticCurrency;
				}
			}
			
		});
		
		if(tot_row<1)
		{
			alert("No Deductions or Distributions Data Insert");	
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&deduction_tot_row='+j+'&distribution_tot_row='+z+get_submitted_data_string('cbo_beneficiary_name*cbo_buyer_name*txt_invoice_bill_no*invoice_bill_id*is_invoice_bill*txt_received_date*txt_remarks*update_id*hdn_variable_distribution*buyer_partial_rlz*grand_total_document_currency',"../../")+dataString_deduction+dataString_distribution;
		//alert(data);return;
		freeze_window(operation);
		
		http.open("POST","requires/export_proceed_realization_advance_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_export_proceed_realization_Reply_info;
		
		
	}	

}

function fnc_export_proceed_realization_Reply_info()
{
	if(http.readyState == 4) 
	{
		// alert(http.responseText);
		var reponse=trim(http.responseText).split('**');	
			
		show_msg(trim(reponse[0]));
		if(reponse[0]==20)
		{
			alert(reponse[1]); release_freezing();return;
		}
		else if(reponse[0]==2)
		{
			location.reload();
			release_freezing();return;
		}		
		else if((reponse[0]==0 || reponse[0]==1))
		{
			document.getElementById('update_id').value = reponse[1];
			
			var submit_type=$('#submit_type').val();
			var variable_distribution=$("#hdn_variable_distribution").val();
			show_list_view( reponse[1]+"**0", 'details_list_view', 'deduction_list_view', 'requires/export_proceed_realization_advance_controller', '' ) ;
			show_list_view( reponse[1]+"**1**"+submit_type+"**"+variable_distribution, 'details_list_view', 'distribution_list_view', 'requires/export_proceed_realization_advance_controller', '' ) ;	
			
			var deduction_tot_row = $('#tbl_deduction tbody tr').length; 
			var distribution_tot_row = $('#tbl_distribution tbody tr').length; 
			
			$('#deduction_tot_row').val(deduction_tot_row);
			$('#distribution_tot_row').val(distribution_tot_row);
			
			calculate_total('tbl_deduction','deduction');
			calculate_total('tbl_distribution','distribution');
			calculate_grand_total();
			set_button_status(1, permission, 'fnc_export_proceed_realization',1);	
		}
		release_freezing();	
	}
}


 // function :: calculate rate, discount, amount         
function calculate(i,field_id,table_id,prefix)
{
	var DocumentCurrency= $('#'+prefix+'DocumentCurrency_'+i).val()*1; 
	var ConversionRate= $('#'+prefix+'ConversionRate_'+i).val()*1;
	var DomesticCurrency= $('#'+prefix+'DomesticCurrency_'+i).val()*1;
	
	if(field_id=="DocumentCurrency_")
	{
		if(ConversionRate!="" && DomesticCurrency!="")
		{
			var DomsCurr=DocumentCurrency*ConversionRate;
			$('#'+prefix+'DomesticCurrency_'+i).val(DomsCurr.toFixed(2));
		}
		else if(ConversionRate=="" && DomesticCurrency!="")
		{
			var ConvRate=DomesticCurrency/DocumentCurrency;
			$('#'+prefix+'ConversionRate_'+i).val(ConvRate.toFixed(4));
		}
		else if(ConversionRate!="" && DomesticCurrency=="")
		{
			var DomsCurr=DocumentCurrency*ConversionRate;
			$('#'+prefix+'DomesticCurrency_'+i).val(DomsCurr.toFixed(2));
		}
		
	}
	else if(field_id=="ConversionRate_")
	{
		if(DocumentCurrency!="" && DomesticCurrency!="")
		{
			var DomsCurr=DocumentCurrency*ConversionRate;
			$('#'+prefix+'DomesticCurrency_'+i).val(DomsCurr.toFixed(2));
		}
		else if(DocumentCurrency=="" && DomesticCurrency!="")
		{
			var DocCurr=DomesticCurrency/ConversionRate;
			$('#'+prefix+'DocumentCurrency_'+i).val(DocCurr.toFixed(4));
		}
		else if(DocumentCurrency!="" && DomesticCurrency=="")
		{
			var DomsCurr=DocumentCurrency*ConversionRate;
			$('#'+prefix+'DomesticCurrency_'+i).val(DomsCurr.toFixed(2));
		}
		
	}
	else if(field_id=="DomesticCurrency_")
	{
		if(DocumentCurrency!="" && ConversionRate!="")
		{
			var DocCurr=DomesticCurrency/ConversionRate;
			$('#'+prefix+'DocumentCurrency_'+i).val(DocCurr.toFixed(2));
		}
		else if(DocumentCurrency=="" && ConversionRate!="")
		{
			var DocCurr=DomesticCurrency*ConversionRate;
			$('#'+prefix+'DocumentCurrency_'+i).val(DocCurr.toFixed(2));
		}
		else if(DocumentCurrency!="" && ConversionRate=="")
		{
			var ConvRate=DocumentCurrency/DocumentCurrency;
			$('#'+prefix+'ConversionRate_'+i).val(ConvRate.toFixed(4));
		}
		
	}
	/*if(field_id=="deductionDocumentCurrency_")
	{
		if(deductionDocumentCurrency!="" && deductionConversionRate!="")
		{
			var deductionDomsCurr=deductionDocumentCurrency*deductionConversionRate;
			$('#deductionDomesticCurrency_'+i).val(deductionDomsCurr.toFixed(2));
		}
		else if(deductionDocumentCurrency!="" && deductionDomesticCurrency!="")
		{
			var deductionConvRate=deductionDocumentCurrency/deductionDomesticCurrency;
			$('#deductionConversionRate_'+i).val(deductionConvRate.toFixed(4));
		}
		
	}
	else if(field_id=="deductionConversionRate_")
	{
		if(deductionConversionRate!="" && deductionDocumentCurrency!="")
		{
			var deductionDomsCurr=deductionDocumentCurrency*deductionConversionRate;
			$('#deductionDomesticCurrency_'+i).val(deductionDomsCurr.toFixed(2));
		}
		else if(deductionConversionRate!="" && deductionDomesticCurrency!="")
		{
			var deductionDocCurr=deductionDomesticCurrency/deductionConversionRate;
			$('#deductionDocumentCurrency_'+i).val(deductionDocCurr.toFixed(4));
		}
		
	}
	else if(field_id=="deductionDomesticCurrency_")
	{
		if(deductionDomesticCurrency!="" && deductionConversionRate!="")
		{
			var deductionDocCurr=deductionDomesticCurrency/deductionConversionRate;
			$('#deductionDocumentCurrency_'+i).val(deductionDocCurr.toFixed(2));
		}
		else if(deductionDomesticCurrency!="" && deductionDocumentCurrency!="")
		{
			var deductionConvRate=deductionDocumentCurrency/deductionDomesticCurrency;
			$('#deductionConversionRate_'+i).val(deductionConvRate.toFixed(4));
		}
	}*/
	
	
	calculate_total(table_id,prefix);
	calculate_grand_total();
}

function calculate_total(table_id,prefix)
{
	var total_document_currency=0; var total_domestic_currency=0;
	$("#"+table_id).find('tbody tr').each(function()
	{
		var DocumentCurrency=$(this).find('input[name="'+prefix+'DocumentCurrency[]"]').val()*1;
		var DomesticCurrency=$(this).find('input[name="'+prefix+'DomesticCurrency[]"]').val()*1;

		total_document_currency=(total_document_currency*1)+DocumentCurrency;
		total_domestic_currency=(total_domestic_currency*1)+DomesticCurrency;
		
		$('#total_'+prefix+'_document_currency').val(total_document_currency);
		$('#total_'+prefix+'_domestic_currency').val(total_domestic_currency);
	});

}

function calculate_grand_total()
{
	var total_deduction_document_currency= $('#total_deduction_document_currency').val()*1;
	var total_deduction_domestic_currency= $('#total_deduction_domestic_currency').val()*1;
	var total_distribution_document_currency= $('#total_distribution_document_currency').val()*1;
	var total_distribution_domestic_currency= $('#total_distribution_domestic_currency').val()*1;
	
	var grand_total_document_currency=total_deduction_document_currency+total_distribution_document_currency;
	var grand_total_domestic_currency=total_deduction_domestic_currency+total_distribution_domestic_currency;
	var bill_ballance=(($('#txt_bill_invoice_amnt').val()*1)-grand_total_document_currency);
	
	$('#grand_total_document_currency').val(grand_total_document_currency);
	$('#grand_total_domestic_currency').val(grand_total_domestic_currency);
	$('#cumilitive_bill_balance').val(bill_ballance.toFixed(5));
}

function fnc_addRow( i, table_id, tr_id )
{ 
	var prefix=tr_id.substr(0, tr_id.length-1);
	//var row_num=$('#'+table_id+' tbody tr').length;
	var row_num=$('#'+prefix+'_tot_row').val();
	
	//alert($('#cbodistributionHead_'+i).val());
	//alert(prefix);
	if(prefix=="distribution")
	{
		var con_rate_distribute="";
		var selected_value="";
		if(row_num==1)
		{
			con_rate_distribute=$('#distributionConversionRate_1').val();
			selected_value=$('#cbodistributionHead_1').attr('acHeadVal');
			//alert(selected_value);
		}
		else
		{
			con_rate_distribute=$('#distributionConversionRate_'+row_num).val();
			
		}
	}
	else
	{
		var coversation_rate="";
		if(row_num==1)
		{
			coversation_rate=$('#deductionConversionRate_1').val();
		}
		else
		{
			coversation_rate=$('#deductionConversionRate_'+row_num).val();
		}
	}
	row_num++;
	//selected_value=$('#cbodistributionHead_'+row_num).val();
	var clone= $("#"+tr_id+i).clone();
	clone.attr({
		id: tr_id + row_num,
	});
	
	clone.find("input,select").each(function(){
		$(this).attr({ 
		  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
		  'name': function(_, name) { return name },
		  'value': function(_, value) { return value }              
		});
	}).end();
	
	//$('#cbodistributionHead_'+row_num).val(selected_value)

	$("#"+tr_id+i).after(clone);
	
	$('#'+prefix+'DocumentCurrency_'+row_num).removeAttr("value").attr("value","");
	$('#'+prefix+'ConversionRate_'+row_num).removeAttr("value").attr("value","");
	$('#'+prefix+'DomesticCurrency_'+row_num).removeAttr("value").attr("value","");
	$('#cbo'+prefix+'Head_'+row_num).val(0);
	
	$('#'+prefix+'DocumentCurrency_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","calculate("+row_num+",'DocumentCurrency_','"+table_id+"','"+prefix+"');");
	$('#'+prefix+'ConversionRate_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","calculate("+row_num+",'ConversionRate_','"+table_id+"','"+prefix+"');");
	$('#'+prefix+'DomesticCurrency_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","calculate("+row_num+",'DomesticCurrency_','"+table_id+"','"+prefix+"');");
	
	if(prefix=="distribution")
	{
		//$('#cbodistributionHead_'+row_num).removeAttr("onChange").attr("onChange","get_php_form_data(this.value+'**"+row_num+"**'+$('#cbo_beneficiary_name').val()+'**'+$('#is_invoice_bill_lien_bank').val(), 'populate_acc_loan_no_data','requires/export_proceed_realization_advance_controller');check_duplication("+row_num+");fn_loan_type_change(this.value,"+row_num+");");
		//onFocus="add_auto_complete( 1 )" onDblClick="fn_commercial_head_display(1,2,'cbodistributionHead')"
		$('#cbodistributionHead_'+row_num).removeAttr("onBlur").attr("onBlur","fn_value_check("+row_num+",2,this.value,'cbodistributionHead')").removeAttr("onFocus").attr("onFocus","add_auto_complete("+row_num+")").removeAttr("onDblClick").attr("onDblClick","fn_commercial_head_display("+row_num+",2,'cbodistributionHead')");
		$('#cbodistributionHead_'+row_num).val('').attr('acHeadVal','');
		
		$('#cbo'+prefix+'Head_'+i).removeAttr("disabled").removeAttr("readonly");
		$('#acLoanNo_'+row_num).val('');
		$('#acLoanNo_'+row_num).removeAttr("disabled").removeAttr("readonly");
		$('#'+prefix+'DocumentCurrency_'+row_num).removeAttr("disabled").removeAttr("readonly");
		$('#'+prefix+'ConversionRate_'+row_num).removeAttr("disabled").removeAttr("readonly");
		$('#'+prefix+'DomesticCurrency_'+row_num).removeAttr("disabled").removeAttr("readonly");
	}
	else
	{
		$('#cbodeductionHead_'+row_num).removeAttr("onBlur").attr("onBlur","fn_value_check("+row_num+",1,this.value,'cbodeductionHead')").removeAttr("onFocus").attr("onFocus","add_auto_complete("+row_num+")").removeAttr("onDblClick").attr("onDblClick","fn_commercial_head_display("+row_num+",1,'cbodeductionHead')");
		$('#cbodeductionHead_'+row_num).val('').attr('acHeadVal','');
	}
	
	$('#'+prefix+'increase_'+row_num).removeAttr("value").attr("value","+");
	$('#'+prefix+'decrease_'+row_num).removeAttr("value").attr("value","-");
	$('#'+prefix+'increase_'+row_num).removeAttr("onclick").attr("onclick","fnc_addRow("+row_num+",'"+table_id+"','"+tr_id+"');");
	$('#'+prefix+'decrease_'+row_num).removeAttr("onclick").attr("onclick","fnc_deleteRow("+row_num+",'"+table_id+"','"+tr_id+"');");
	
	$('#'+prefix+'_tot_row').val(row_num);
	
	if(prefix=="distribution")
	{
		$('#'+prefix+'ConversionRate_'+row_num).val(con_rate_distribute);
	}
	else
	{
		$('#'+prefix+'ConversionRate_'+row_num).val(coversation_rate);
	}
	
	set_all_onclick();
	//$('#deductionConversionRate_'+row_num).val(coversation_rate);
	//$('#distributionConversionRate_'+row_num).val(con_rate_distribute);
	
}

function fnc_deleteRow(rowNo,table_id,tr_id) 
{ 
	var numRow = $('#'+table_id+' tbody tr').length; 
	var prefix=tr_id.substr(0, tr_id.length-1);
	
	//var isDisbled=$('#'+tr_id+rowNo).find('select[name="'+'cbo'+prefix+'Head[]'+'"]').is(":disabled");// Both Works Perfectly
	var isDisbled=$('#cbo'+prefix+'Head_'+rowNo).is(":disabled");// Both Works Perfectly

	if(numRow!=1 && isDisbled==false)
	{
		$("#"+tr_id+rowNo).remove();
		
		calculate_total(table_id,prefix);
		calculate_grand_total();
	}
}

function check_duplication(rowNo)
{
	var cbodistributionHead=$("#cbodistributionHead_"+rowNo).attr('acHeadVal');
	var i=0;
	if(cbodistributionHead==1)
	{
		$("#tbl_distribution").find('tbody tr').each(function()
		{
			var distributionHead=$(this).find('input[name="cbodistributionHead[]"]').attr('acHeadVal');
			if(distributionHead==1)
			{
				i=i+1;
			}
			if(i*1>1)
			{
				alert("Duplicate Account Head For Negotiation");
				$("#cbodistributionHead_"+rowNo).val("").attr("acHeadVal","");
				$("#acLoanNo_"+rowNo).val('');
				$("#distributionDocumentCurrency_"+rowNo).val('');
				$("#distributionConversionRate_"+rowNo).val('');
				$("#distributionDomesticCurrency_"+rowNo).val('');
				/*$(this).find('select[name="cbodistributionHead[]"]').val(0);
				$(this).find('input[name="acLoanNo[]"]').val('');
				$(this).find('input[name="distributionDocumentCurrency[]"]').val('');
				$(this).find('input[name="distributionConversionRate[]"]').val('');
				$(this).find('input[name="distributionDomesticCurrency[]"]').val('');*/
				return false;
			}
		});	
	}
	
}
/**
 * function to swich browse and entry option
 * added by shafiq-sumon
 */
function fn_loan_type_change(loan_type, trow_id)
{
	if(loan_type == 20 || loan_type ==22)
	{
		if (form_validation('cbo_beneficiary_name*txt_invoice_bill_no','Beneficiary Name*Bill/Invoice No')==false )
		{
			return;
		}
		var lc_id = $("#lc_id").val();
		var lc_type = $("#lc_type").val();
		var cbo_beneficiary_name = $("#cbo_beneficiary_name").val();
		var data="action=check_loan_type&lc_id="+lc_id+'&lc_type='+lc_type+'&cbo_beneficiary_name='+cbo_beneficiary_name;
		//alert(data);return;
		freeze_window(5);
		
		http.open("POST","requires/export_proceed_realization_advance_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fn_loan_type_change_reply_info;
		$("#acLoanNo_"+trow_id).attr({placeholder: "Browse", onDblClick: "open_loan_number_popup(this.id)", readonly: true});
	}
	else
	{
		// ##### Due To bank submission type nagotiation data this code disable 
		/*$("#acLoanNo_"+trow_id).attr({placeholder: "Entry", onDblClick: ""});
		$("#distributionDocumentCurrency_"+trow_id).removeAttr("disabled").val("");
		//$("#distributionDocumentCurrency_"+trow_id);
		$("#distributionConversionRate_"+trow_id).removeAttr("disabled").val("");
		$("#distributionDomesticCurrency_"+trow_id).removeAttr("disabled").val("");*/
	}
}
function fn_loan_type_change_reply_info() {
	if(http.readyState == 4) 
	{
		//alert(http.responseText);return;
		var response = trim(http.responseText);
		if(response !="")
		{

			$("#pre_export_dtls_id").val(response);
			$("#acLoanNo_1").attr({ readonly: "readonly" });
			$("#distributionDocumentCurrency_1").attr({ readonly: "readonly", disabled: true });
			$("#distributionConversionRate_1").attr({ readonly: true, disabled:true });
			$("#distributionDomesticCurrency_1").attr({ readonly: true, disabled:true });
		}
		release_freezing();	
	}
}
/**
 * function to brows paking credit and export cash credit type loan
 * added by shafiq-sumon
 */
function open_loan_number_popup (element_id) {
	if (form_validation('cbo_beneficiary_name','Beneficiary Name')==false )
	{
		return;
	}
	var row_id = element_id.slice(-1);
	var beneficiary = $("#cbo_beneficiary_name").val();
	var loan_type = $("#cbodistributionHead_"+row_id).attr("acHeadVal");
	var pre_export_dtls_id = $("#pre_export_dtls_id").val();
	var lc_id = $("#lc_id").val();
	var lc_type = $("#lc_type").val();
	//alert(row_id);

	var page_link='requires/export_proceed_realization_advance_controller.php?action=loan_number_popup&beneficiary_name='+beneficiary+'&loan_type='+loan_type+'&pre_export_dtls_id='+pre_export_dtls_id+'&lc_id='+lc_id+'&lc_type='+lc_type;
	var title="Loan Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=760px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var loan_dtls_id=this.contentDoc.getElementById("hidden_loan_dtls_id").value; 
		var loan_number=this.contentDoc.getElementById("hidden_loan_number").value; 
		var conversion_rate=this.contentDoc.getElementById("hidden_conversion_rate").value; 
		var equivalent_fc=this.contentDoc.getElementById("hidden_amount").value; 
		//var row_id=this.contentDoc.getElementById("hidden_row_id").value; 
		var domestic_currency = (equivalent_fc*1)*(conversion_rate*1);
		var row_id = element_id.substr(element_id.length-1);
		//alert(row_id);
		$("#acLoanNo_"+row_id).val(loan_number).attr({disabled: true});
		$("#distributionDocumentCurrency_"+row_id).val(equivalent_fc).attr({disabled: true});
		$("#distributionConversionRate_"+row_id).val(conversion_rate).attr({disabled: true});
		$("#distributionDomesticCurrency_"+row_id).val(domestic_currency).attr({disabled: true});

	}
}

function fn_value_check(seq_no,source_from,fld_value,fld_names)
{
	if (form_validation('cbo_beneficiary_name','Company')==false )
	{
		$('#'+fld_names+'_'+seq_no).val("");
		return;
	}
	fld_value=fld_value.toUpperCase();
	var commercial_head_reverse = JSON.parse('<?  echo json_encode($commercial_head_reverse); ?>');
	//alert(fld_value+"="+commercial_head_reverse[fld_value]);
	if(commercial_head_reverse[fld_value]=="" || commercial_head_reverse[fld_value]==undefined)
	{
		var msg="";
		if(source_from==1)  
		{
			msg="Deductions Account Head Not Found"; 
		}
		else  
		{
			msg="Distributions Account Head Not Found";
		}
		alert(msg);
		$('#'+fld_names+'_'+seq_no).val("").attr('acHeadVal','0').focus();
	}
	else
	{
		//alert(commercial_head_reverse[fld_value]);
		$('#'+fld_names+'_'+seq_no).attr('acHeadVal',commercial_head_reverse[fld_value]);
	}
	
	if(source_from==2)
	{
		get_php_form_data( commercial_head_reverse[fld_value]+'**'+seq_no+'**'+$('#cbo_beneficiary_name').val()+'**'+$('#is_invoice_bill_lien_bank').val(), 'populate_acc_loan_no_data', 'requires/export_proceed_realization_advance_controller' );
		check_duplication(seq_no);
		fn_loan_type_change(commercial_head_reverse[fld_value],seq_no);
	}
}

function fn_commercial_head_display(seq_no,head_from,fld_name)
{
	if (form_validation('cbo_beneficiary_name','Beneficiary')==false )
	{
		return;
	}
	$('#'+fld_name+"_"+seq_no).removeAttr('onblur');
	var page_link='requires/export_proceed_realization_advance_controller.php?action=commercial_head_popup';
	var title='Account Head';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=380px,height=400px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var hdn_head_id=this.contentDoc.getElementById("hdn_head_id").value;
		var hdn_head_val=this.contentDoc.getElementById("hdn_head_val").value;
		$('#'+fld_name+"_"+seq_no).val(hdn_head_val).attr('acHeadVal',hdn_head_id);
		if(head_from==2)
		{
			get_php_form_data( hdn_head_id+'**'+seq_no+'**'+$('#cbo_beneficiary_name').val()+'**'+$('#is_invoice_bill_lien_bank').val(), 'populate_acc_loan_no_data', 'requires/export_proceed_realization_advance_controller' );
			check_duplication(seq_no);
			fn_loan_type_change(hdn_head_id,seq_no);
		}
		$('#'+fld_name+"_"+seq_no).val(hdn_head_val).attr("onblur","fn_value_check("+seq_no+","+head_from+",this.value,"+"'"+fld_name+"')");
	}
}

function fn_set_variable(com_id,bank_id)
{
	//alert(com_id+"="+bank_id);
	var variable_distribution = trim(return_global_ajax_value(com_id+"**"+bank_id, 'populate_data_form_lib', '', 'requires/export_proceed_realization_advance_controller'));
	//alert(variable_distribution);
	//alert(bill_amount);
	if(variable_distribution!="")
	{
		$("#distribute_dtls").show();
		$("#dis_per_cap").show();
		$("#dis_per_td").show();
		$("#tot_distribute").attr('colspan',3);
		$("#hdn_variable_distribution").val(variable_distribution);
		$("#gt_total").attr('width',340);
		//$("#gt_total_th").attr('colspan',2);
	}
	else
	{
		$("#distribute_dtls").hide();
		$("#dis_per_cap").hide();
		$("#dis_per_td").hide();
		$("#tot_distribute").attr('colspan',2);
		$("#hdn_variable_distribution").val("");
		$("#gt_total").attr('width',260);
		//$("#gt_total_th").attr('colspan',1);
	}
	//get_php_form_data( this.value, 'set_variable', 'requires/export_proceed_realization_advance_controller' );
}

function fn_distribution_generate()
{
	var variable_distribution=$("#hdn_variable_distribution").val();
	var deduction_total=$("#total_deduction_document_currency").val()*1;
	var distribution_total=$("#total_distribution_document_currency").val()*1;
	var bill_amount=$("#txt_bill_invoice_amnt").val()*1;
	var tot_row=$("#tbl_distribution tbody tr").length;
	//alert(tot_row);return;
	
	var net_bill_amount=(bill_amount-(deduction_total+distribution_total) );
	//var bill_amount=$("#txt_bill_invoice_amnt").val();
	if(net_bill_amount>0)
	{
		var prev_data=trim($("#cbodistributionHead_1").val());
		if(prev_data!="") tot_row=tot_row; else tot_row=0;
		var variable_distribution = return_global_ajax_value(variable_distribution+"**"+net_bill_amount+"**"+tot_row, 'populate_data_form_lib_dis', '', 'requires/export_proceed_realization_advance_controller');
		
		if(prev_data!="")
		{
			$("#tbl_distribution tbody:last").append(variable_distribution);
		}
		else
		{
			$("#distribution_list_view").html(variable_distribution);
		}
		//$("#distribution_list_view").html(variable_distribution);
		
		calculate_total('tbl_distribution','distribution');
		calculate_grand_total();
	}
}


function fn_print_report(action)
{
	/*var update_id = $('#update_id').val();
	if(update_id=='')
	{
		alert('Please Save Invoice First');
		return false;
	}*/
	if (form_validation('update_id','Save Data First')==false)
	{
		alert("Please Save Invoice First");
		return;
	}
	else
	{
		print_report( $('#update_id').val(), action, "requires/export_proceed_realization_advance_controller" ) ;
	}
}

</script>

</head>
 
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">																	
     	<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="exportProceedRealizationFrm_1" id="exportProceedRealizationFrm_1" autocomplete="off" method="POST"  >
        	<fieldset style="width:850px; margin-bottom:10px;">
            <legend>Export Proceeds Realization Entry</legend>
                <table cellpadding="2" cellspacing="1" width="100%">
                	<tr>
                    	<td colspan="6" align="center"><b>Previous Entry Search: </b> 
                    	    <input type="text" name="search_realization" id="search_realization" class="text_boxes" placeholder="Double click for update" onDblClick="openmypage_realizationUpdate();" readonly />
                        </td>
                    </tr>
                    <tr height="10"><td></td></tr>
                  	<tr>
                    	<td width="100" class="must_entry_caption">Beneficiary</td>
                       	<td> 
                        	<?
							  echo create_drop_down( "cbo_beneficiary_name", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Beneficiary --", 0, "load_drop_down( 'requires/export_proceed_realization_advance_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );",0 );
 							?>
                            <input type="hidden" id="hdn_variable_distribution" name="hdn_variable_distribution" />
                        </td>
                    	<td width="100" class="must_entry_caption">Buyer</td>
                        <td id="buyer_td_id">
                        	<?
								echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- Select Buyer --", 0, "" );
							?>                          
                        </td>
                        <td width="100" class="must_entry_caption">SC No</td>
                    	<td>
                        	<input type="text" name="txt_invoice_bill_no" id="txt_invoice_bill_no" style="width:110px" class="text_boxes" placeholder="Double Click to Search" onDblClick="openmypage_InvoiceBill()" readonly />
                        	<input type="button" onClick="fnc_invoiceDetails();" style="width: 65px; background: #8DAFDA; border-radius: 7px; display:none;" value="Invoice Dtls">
                            <input type="hidden" name="invoice_bill_id" id="invoice_bill_id" readonly/>
                            <input type="hidden" name="lc_id" id="lc_id" readonly/>
                            <input type="hidden" name="lc_type" id="lc_type" readonly/>
                            <input type="hidden" name="is_invoice_bill" id="is_invoice_bill" readonly />
                            <input type="hidden" name="hidden_posted_account" id="hidden_posted_account" value=""/>
                            <input type="hidden" name="is_invoice_bill_lien_bank" id="is_invoice_bill_lien_bank" readonly />
                            <input type="hidden" name="buyer_partial_rlz" id="buyer_partial_rlz" readonly />
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Received Date</td>
                        <td><input name="txt_received_date" style="width:140px" id="txt_received_date" value="<?echo date('d-m-Y')?>" class="datepicker" ></td>
                        <td>
                            SC Value</td>
                        <td>
                            <input type="text" name="txt_bill_invoice_amnt" id="txt_bill_invoice_amnt" placeholder="Display" class="text_boxes_numeric" style="width:140px;" readonly />
                        </td>
                        <td class="must_entry_caption">Currency</td>
                        <td>
							<?
							   	echo create_drop_down( "cbo_currency_name", 122, $currency,"", 1, "Display", 0, "",1 );
							?>
                        </td>
                    </tr>
                    <tr style="display:none;">
                    	<td>Bill/Invoice Date</td>
                        <td>
                            <input type="text" name="txt_bill_invoice_date" id="txt_bill_invoice_date" placeholder="Display" class="datepicker" style="width:140px" readonly />
                        </td>
                        <td>LC/SC No</td>
                        <td>
                        	<input type="text" name="txt_lc_sc_no" id="txt_lc_sc_no" style="width:140px" class="text_boxes" readonly placeholder="Display" />
                        	<input type="hidden" name="txt_import_btb" id="txt_import_btb" value="" readonly />
                        </td>
                        <td>Negotiated Amount</td>
						<td>
                        	<input type="text" name="txt_negotiated_amount" id="txt_negotiated_amount" placeholder="Display" class="text_boxes_numeric" style="width:140px;" readonly />
                            <input type="hidden" name="submit_type" id="submit_type" value="" readonly />
                        </td>
                    </tr>
                    <tr>
                        <td>Remarks</td>
                        <td colspan="5"><input name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:700px"></td>
                    </tr>
                     <tr>
                        <td colspan="6" id="posted_status_td" style="color:red; font-size:24px; text-align:center"></td>
                    </tr>
                </table>
			</fieldset>   
            <br>
			<fieldset style="width:850px; display:none" >
				<legend>Deductions at Source</legend>
				<table id="tbl_deduction" width="100%" border="0" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<thead>						
                        <th class="must_entry_caption">Account Head</th>
                        <th>Document Currency</th>
                        <th>Conversion Rate</th>
                        <th>Domestic Currency</th>
                        <th></th>						
					</thead>
					<tbody id="deduction_list_view">
						<tr class="general" id='deduction_1' align="center">
							<td>
                            	<!--<select class="combo_boxes" id="cbodeductionHead_1" name="cbodeductionHead[]" style="width:172px">
                                	<option value="0">-- Select Account Head --</option>
										<?
											/*asort($commercial_head);
											foreach($commercial_head as $key=>$value)
											{
											?>
                                            	<option value="<? echo $key; ?>"><? echo $value; ?></option>
                                            <?	
											}*/
											//echo create_drop_down( "cbodeductionHead_1", 172, $commercial_head,"", 1, "-- Select Account Head --", 0, "" ); onChange onBlur
                                        ?>
                                </select>-->
                                <input type="text" name="cbodeductionHead[]" id="cbodeductionHead_1" class="text_boxes" style="width:170px;" onFocus="add_auto_complete( 1 )" onDblClick="fn_commercial_head_display(1,1,'cbodeductionHead')" onBlur="fn_value_check(1,1,this.value,'cbodeductionHead')" placeholder="Browse Or Write" />
							</td>
							<td>
                            	<input type="text" name="deductionDocumentCurrency[]" id="deductionDocumentCurrency_1" class="text_boxes_numeric" style="width:177px;" onKeyUp="calculate(1,'DocumentCurrency_','tbl_deduction','deduction')"/>
                            </td>
							<td>
                            	<input type="text" name="deductionConversionRate[]" id="deductionConversionRate_1" class="text_boxes_numeric" style="width:177px;" onKeyUp="calculate(1,'ConversionRate_','tbl_deduction','deduction')"/>
                            </td>
							<td>
                            	<input type="text" name="deductionDomesticCurrency[]" id="deductionDomesticCurrency_1" class="text_boxes_numeric" style="width:177px;" onKeyUp="calculate(1,'DomesticCurrency_','tbl_deduction','deduction')" />
                            </td>
                            <td width="65">
                                <input type="button" id="deductionincrease_1" name="deductionincrease[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_deduction','deduction_')" />
                                <input type="button" id="deductiondecrease_1" name="deductiondecrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_deduction','deduction_');" />
                            </td>
						</tr>
					</tbody>
                    <tfoot class="tbl_bottom">
                    	<tr>
                        	<td>Total</td>
                            <td><input style="width:177px;" type="text" class="text_boxes_numeric" id="total_deduction_document_currency" readonly /></td>
                            <td></td>
                            <td><input style="width:177px;" type="text" class="text_boxes_numeric" id="total_deduction_domestic_currency" readonly /></td>
                        </tr>
                    </tfoot>
				</table>
			</fieldset> 
            <br>  
            <fieldset style="width:850px;">
				<legend>Distributions <input type="button" id="distribute_dtls" name="distribute_dtls" class="formbutton" style="width:200px; display:none;" value="Generate" onClick="fn_distribution_generate();" /> </legend>
				<table id="tbl_distribution" width="100%" border="0" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<thead>						
                        <th class="must_entry_caption" width="175">Account Head</th>
                        <th width="130">AC / Loan No</th>
                        <th id="dis_per_cap" style="display:none;">Distribution Percent</th>
                        <th width="140">Document Currency</th>
                        <th width="100">Conversion Rate</th>
                        <th width="140">Domestic Currency</th>
                        <th width="50"></th>						
					</thead>
					<tbody id="distribution_list_view">
						<tr class="general" id='distribution_1' align="center">
							<td> 
                            	<!--<select class="combo_boxes" name="cbodistributionHead[]" id="cbodistributionHead_1" onChange="get_php_form_data( this.value+'**1**'+$('#cbo_beneficiary_name').val()+'**'+$('#is_invoice_bill_lien_bank').val(), 'populate_acc_loan_no_data', 'requires/export_proceed_realization_advance_controller' );check_duplication(1);fn_loan_type_change(this.value,1);" style="width:172px">
                                	<option value="0">-- Select Account Head --</option>
										<?
											/*foreach($commercial_head as $key=>$value)
											{
											?>
                                            	<option value="<? echo $key; ?>"><? echo $value; ?></option>
                                            <?	
											}*/
											//echo create_drop_down( "cbodistributionHead_1", 172, $commercial_head,"", 1, "-- Select Account Head --", 0, "get_php_form_data( this.value+'**1**'+$('#cbo_beneficiary_name').val(), 'populate_acc_loan_no_data', 'requires/export_proceed_realization_advance_controller' );" );
                                        ?>
                                </select>-->
                                <input type="text" name="cbodistributionHead[]" id="cbodistributionHead_1" class="text_boxes" style="width:170px;" onFocus="add_auto_complete( 1 )"  onBlur="fn_value_check(1,2,this.value,'cbodistributionHead')"  onDblClick="fn_commercial_head_display(1,2,'cbodistributionHead')"  placeholder="Browse Or Write" />
							</td>
                            <td>
                            	<input type="text" name="acLoanNo[]" id="acLoanNo_1" class="text_boxes" style="width:120px;" onDblClick="openmypage_AccountNo(this.id)" placeholder="Browse" readonly/>
                            	<input type="hidden" name="pre_export_dtls_id" id="pre_export_dtls_id" />
                            </td>
                            <td id="dis_per_td" style="display:none">
                            <input type="text" id="txtdispersent_1" name="txtdispersent[]" class="text_boxes_numeric" style="width:50px"  readonly />
                            </td>
							<td>
                            	<input type="text" name="distributionDocumentCurrency[]" id="distributionDocumentCurrency_1" class="text_boxes_numeric" style="width:130px;" onKeyUp="calculate(1,'DocumentCurrency_','tbl_distribution','distribution')"/>
                            </td>
							<td>
                            	<input type="text" name="distributionConversionRate[]" id="distributionConversionRate_1" class="text_boxes_numeric" style="width:90px;" onKeyUp="calculate(1,'ConversionRate_','tbl_distribution','distribution')"/>
                            </td>
							<td>
                            	<input type="text" name="distributionDomesticCurrency[]" id="distributionDomesticCurrency_1" class="text_boxes_numeric" style="width:130px;" onKeyUp="calculate(1,'DomesticCurrency_','tbl_distribution','distribution')" />
                            </td>
                            <td width="65">
                                <input type="button" id="distributionincrease_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_distribution','distribution_')" />
                                <input type="button" id="distributiondecrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_distribution','distribution_');" />
                            </td>
						</tr>
					</tbody>
                    <tfoot class="tbl_bottom">
                    	<tr>
                        	<td colspan="2" id="tot_distribute">Total</td>
                            <td><input style="width:130px;" type="text" class="text_boxes_numeric" id="total_distribution_document_currency" readonly /></td>
                            <td></td>
                            <td><input style="width:130px;" type="text" class="text_boxes_numeric" id="total_distribution_domestic_currency" readonly /></td>
                        </tr>
                    </tfoot>
				</table>
                <br>
                <table width="850" border="0" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <thead>						
                            <th id="gt_total_th"></th>
                            <th>Document Currency</th>
                            <th>Cumulative Bill Balance</th>
                            <th>Domestic Currency</th>						
                    </thead>
                    <tfoot class="tbl_bottom">					
                        <tr>
                            <td align="right" id="gt_total"><b>Grand Total</b></td>
                            <td><input style="width:120px;" type="text" class="text_boxes_numeric" id="grand_total_document_currency" readonly /></td>
                            <td><input style="width:120px;" type="text" class="text_boxes_numeric" id="cumilitive_bill_balance" readonly /></td>
                            <td><input style="width:120px;" type="text" class="text_boxes_numeric" id="grand_total_domestic_currency" readonly /></td>						
                        </tr>
                    </tfoot>
                </table> 
			</fieldset>
            <br>         
            <table width="850">
                <tr>
                    <td colspan="6" height="50" valign="middle" align="center" class="button_container">
                        <? 
							echo load_submit_buttons( $permission, "fnc_export_proceed_realization", 0,0 ,"reset_form('exportProceedRealizationFrm_1','posted_status_td','','deduction_tot_row,1*distribution_tot_row,1','disable_enable_fields(\'cbodistributionHead_1*acLoanNo_1*distributionDocumentCurrency_1*distributionConversionRate_1*distributionDomesticCurrency_1\',0)');$('#tbl_deduction tbody tr:not(:first)').remove();$('#tbl_distribution tbody tr:not(:first)').remove();",1) ;
                        ?>
                        <input type="hidden" name="update_id" id="update_id" readonly />
                        <input type="hidden" name="deduction_tot_row" id="deduction_tot_row" value="1" readonly />
                        <input type="hidden" name="distribution_tot_row" id="distribution_tot_row" value="1" readonly />
                        <input type="button" class="formbutton" id="btn_rlz_rpt" value="Print" style="width:100px;" onClick="fn_print_report('realization_report_print')" >
                    </td>
                </tr>
            </table>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
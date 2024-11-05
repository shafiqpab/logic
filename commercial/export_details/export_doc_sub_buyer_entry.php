<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for Pre Export Finance entry
					
Functionality	:	
				

JS Functions	:

Created by		:	Aziz 
Creation date 	: 	 
Updated by 		: 	Jahid
Update date		: 	15-06-2015	   	   

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
echo load_html_head_contents("Pre Export Finance Form", "../../", 1, 1,'','1','');
?>	
 
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';


function openmypage_LcSc()
{
	var companyID = $("#cbo_company_name").val();
	var buyerID = $("#cbo_buyer_name").val();
 	var invoice_id_string = $("#invoice_id_string").val();
	var mst_tbl_id = $("#mst_tbl_id").val();	  
	if (form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	
	var page_link='requires/export_doc_sub_buyer_entry_controller.php?action=lcSc_popup_search&companyID='+companyID+'&buyerID='+buyerID+'&invoice_id_string='+invoice_id_string+'&mst_tbl_id='+mst_tbl_id;
	var title='Document Submission Form';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=930px,height=400px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{ 
		var theform=this.contentDoc.forms[0];
		var all_invoice_id=this.contentDoc.getElementById("all_invoice_id").value;
 		$("#invoice_id_string").val(all_invoice_id);
		
		if(trim(all_invoice_id)!="")
		{
			freeze_window(5); 
			get_php_form_data(all_invoice_id, "show_invoice_list_view", "requires/export_doc_sub_buyer_entry_controller" );
 			release_freezing();
		}
					 
	}
}

function fnc_export_doc_sub_entry(operation)
{

		if (form_validation('cbo_company_name*cbo_buyer_name*lcsc_no*txt_submit_date','Company Name*Buyer Name*LC/SC No*Submission Date')==false )
		{
			return;
		}

		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][39]); ?>') 
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][39]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][39]); ?>')==false) {return;}
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
		
		var dataString="";
		var invoiceRow = $('#invo_table tbody tr').length-1;
		for(var i=1;i<=invoiceRow;i++)
		{
			try 
			{
 				dataString += "&txt_invoice_no"+i+"="+$("#txt_invoice_no"+i).val();
				dataString += "&hidden_invoice_id"+i+"="+$("#hidden_invoice_id"+i).val();
				dataString += "&txt_lcsc_id"+i+"="+$("#txt_lcsc_id"+i).val();
				dataString += "&hidden_is_lc"+i+"="+$("#hidden_is_lc"+i).val();
				dataString += "&txt_lcsc_no"+i+"="+$("#txt_lcsc_no"+i).val();
				dataString += "&txt_bl_no"+i+"="+$("#txt_bl_no"+i).val();
				dataString += "&txt_invoice_date"+i+"="+$("#txt_invoice_date"+i).val();
				dataString += "&txt_net_invo_value"+i+"="+$("#txt_net_invo_value"+i).val();
				dataString += "&hidden_po_numbers_id"+i+"="+$("#hidden_po_numbers_id"+i).val();
				dataString += "&txt_feeder_vessel"+i+"="+$("#txt_feeder_vessel"+i).val();
				dataString += "&txt_forwarder_name"+i+"="+$("#txt_forwarder_name"+i).val();
			}
			catch(e) 
			{
				//got error no operation
			}
		}
		
 		/*var transRow = $('#trans_details tbody tr:last').attr('id');
		if(transRow && (transRow!="" || transRow!=null || transRow!="undefined") )
		{
			transRow = transRow.substring(2,transRow.length);
		}
		
		for(var i=1;i<=transRow;i++)
		{
			try
			{
				if (form_validation('cbo_account_head_'+i+'*'+'txt_domestic_curr_'+i+'*'+'txt_conversion_rate_'+i,'Account Head*Domestic Currency*Conversation Rate')==false )
				{
					return;
				}
				dataString += "&cbo_account_head_"+i+"="+$("#cbo_account_head_"+i).val();
				dataString += "&txt_ac_loan_no_"+i+"="+$("#txt_ac_loan_no_"+i).val();
				dataString += "&txt_domestic_curr_"+i+"="+$("#txt_domestic_curr_"+i).val();
				dataString += "&txt_conversion_rate_"+i+"="+$("#txt_conversion_rate_"+i).val();
				dataString += "&txt_lcsc_currency_"+i+"="+$("#txt_lcsc_currency_"+i).val();
 			}
			catch(e)
			{
				//got error no operation
			}
		}*/
		
		//get_submitted_data_string('cbo_company_name*cbo_buyer_name*lcsc_no*lc_sc_id*txt_submit_date*cbo_submit_to*txt_bank_ref*txt_bank_ref_date*cbo_submission_type*txt_negotiation_date*txt_day_to_realize*txt_possible_reali_date*courier_receipt_no*txt_courier_company*txt_courier_date*txt_bnk_to_bnk_cour_no*txt_bnk_to_bnk_cour_date*cbo_lien_bank*cbo_currency*txt_remarks*total_dom_curr_hid*total_foreign_curr_hid*mst_tbl_id*invoice_tbl_id',"../../");	
			
 		var data="action=save_update_delete&operation="+operation+'&invoiceRow='+invoiceRow+dataString+get_submitted_data_string('cbo_company_name*cbo_buyer_name*lcsc_no*lc_sc_id*txt_submit_date*txt_day_to_realize*txt_possible_reali_date*courier_receipt_no*txt_courier_company*txt_courier_date*cbo_lien_bank*cbo_currency*txt_remarks*mst_tbl_id*invoice_tbl_id*prev_submitted_inv',"../../");
		//alert(data)//;return;
		freeze_window(operation);		
		http.open("POST","requires/export_doc_sub_buyer_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_export_doc_sub_entry_Reply_info;
}


function fnc_export_doc_sub_entry_Reply_info()
{
	
	if(http.readyState == 4) 
	{
		// alert(http.responseText);
		var reponse=http.responseText.split('**');	
		if(trim(reponse[0])==20)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		if(trim(reponse[0])==50)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}		 
		show_msg(trim(reponse[0])); 
		if( reponse[0]==0 || reponse[0]==1)
		{		
			get_php_form_data(trim(reponse[1]), "populate_master_from_data", "requires/export_doc_sub_buyer_entry_controller");
			set_button_status(1, permission, 'fnc_export_doc_sub_entry',1); 			 
		}
		else if(reponse[0]==2)
		{
			//show_msg(reponse[0]);
			location.reload();
		}
		//fnResetForm();
		release_freezing();
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
	var page_link='requires/export_doc_sub_buyer_entry_controller.php?action=doc_sub_popup&company_name='+company_name+'&buyer_name='+buyer_name; 
	var title="Search System Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var mstID=this.contentDoc.getElementById("hidden_system_number").value; // master table id
  		// master part call here		
		get_php_form_data(mstID, "populate_master_from_data", "requires/export_doc_sub_buyer_entry_controller");
		set_button_status(1, permission, 'fnc_export_doc_sub_entry',1);		
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
			$('#transaction_container').append('<tr id="tr0"><td colspan="6"><b><center>Please Select LC/SC to view Transaction</center></b></td> </tr>');
		}                       
		else if(submit_type=='2')
		{
			var rowNo=0;
			$('#txt_negotiation_date').removeAttr('disabled');
			$('#txt_negotiated_ammount').removeAttr('disabled');
			var response = return_global_ajax_value(rowNo, "transaction_add_row", "", "requires/export_doc_sub_buyer_entry_controller" );
			$('#transaction_container').find('tr').remove();
			$('#transaction_container').append(response);
		}
	}

	
	function fn_inc_decr_row(DelrowNo,type)
	{		
		var rownumber = $("#transaction_container tr:last").attr('id');
		var rowNo = rownumber.substr(2,rownumber.length);
		if(type=='increase')
		{
			var response = return_global_ajax_value(rowNo, "transaction_add_row", "", "requires/export_doc_sub_buyer_entry_controller" );
 			$('#transaction_container').append(response);
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
	
	
	
	
	
	/*function fn_calculate(id,rowNo)
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
		//$("#total_dom_curr_hid").val(number_format_common(totalDomCurr,4,'',''));
		$("#total_foreign_curr_hid").val(number_format_common(totalForeignCurr,5,'',''));
	}*/
	
	//reset/refresh function 
	function fnResetForm()
	{
		reset_form('docsubmFrm_1','','','','$(\'#transaction_container tr:not(:first)\').remove();','');
		$('#invo_table').find('tr:gt(0)').remove();
		$('#invoice_container').append('<tr id="tr0"><td colspan="6"><b><center>Please Select LC/SC to view invoice List</center></b></td></tr>');
		$('#trans_details').find('tr:gt(0)').remove();
		$('#trans_details thead').after('<tbody id="transaction_container"><tr id="tr0"><td colspan="6"><b><center>Please Select LC/SC to view Transaction</center></b></td></tr></tbody><tfoot><th colspan="2">Sum&nbsp;&nbsp;</th><td><input type="text" id="total_dom_curr_hid" class="text_boxes_numeric" style="width:100px" disabled readonly /></td><th>&nbsp;</th>	<td><input type="text" id="total_foreign_curr_hid" class="text_boxes_numeric" style="width:100px" disabled readonly /></td><th></th></tfoot>');
 		set_button_status(0, permission, 'fnc_export_doc_sub_entry',1);	
	}
      
	  function fn_add_date_field_time(days_realize)
	{
		$("#txt_possible_reali_date").val(add_days($('#txt_submit_date').val(),days_realize));
	}
	
	function fn_print_letter(type)
	{
		if (form_validation('mst_tbl_id','Save Data First')==false)
		{
			alert("Save Data First");
			return;
		}
		else
		{
			 print_report( $('#mst_tbl_id').val()+'**'+type+"**"+$('#cbo_company_name').val(), "buyer_submit_letter", "requires/export_doc_sub_buyer_entry_controller" ) ;
		}
	}
	               
</script>
 
 
</head> 
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">																	
     	<? echo load_freeze_divs ("../../",$permission); ?><br/>
        <fieldset style="width:930px; margin-bottom:10px;">
        <form name="docsubmFrm_1" id="docsubmFrm_1" autocomplete="off" method="POST"  >
        	 
            	<!------------------------------ 1st form Start here ------------------------------------------->
          <fieldset style="width:950px;">
            <legend>Commercial Module</legend>
              <table  width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master"> 
              <tr>
              <td colspan="6" align="center"> System ID &nbsp;<input type="text" name="mst_tbl_id" id="mst_tbl_id" class="text_boxes" placeholder="Double Click to Update" onDblClick="pop_doc_submission();" readonly /></td>
              </tr>
               <tr>
              <td colspan="6" align="center">&nbsp;</td>
              </tr>
                 <tr>
                      <td  width="130" align="right" class="must_entry_caption">Company Name </td>
                      <td width="170">
                          <?
								echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "" );//load_drop_down( 'requires/export_doc_sub_buyer_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );
							?> 
                      </td>
                      <td width="130" align="right" class="must_entry_caption" >Buyer</td>
                      <td width="160" id="buyer_td">
                          <? 
								//echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select --", $selected, "" );
								echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, " Display ", 0, "",1 );
							?>
                      </td>
                      <td width="130" align="right" class="must_entry_caption">Sales Contract No </td>
                      <td width="170">
                          <input type="text" name="lcsc_no" id="lcsc_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search"  readonly onDblClick="openmypage_LcSc();" />
                          <input type="hidden" name="lc_sc_id" id="lc_sc_id" readonly />
                          <input type="hidden" name="invoice_id_string" id="invoice_id_string" readonly />
                       </td>
                </tr>
                <tr>                                       
                     <td  width="130" align="right" class="must_entry_caption">Submission Date</td>
                     <td width="170">
                         <input style="width:140px " name="txt_submit_date" id="txt_submit_date" class="datepicker"  placeholder="Select Date" />
                     </td>
                     <td  width="130" align="right">Days to Realize </td>
                        <td width="170">
                            <input type="text" name="txt_day_to_realize" id="txt_day_to_realize" class="text_boxes_numeric" style="width:140px" onBlur="fn_add_date_field_time(this.value);" />	
                        </td>
                        <td width="130" align="right" >Possible Reali Date</td>
                       <td width="160">  
                         <input type="text" name="txt_possible_reali_date" id= "txt_possible_reali_date" class="datepicker"  style="width:140px" />
                       </td>
                </tr>
                
                <tr>                                   
                        <td width="130" align="right">Courier Company </td>
                        <td width="170"><input type="text" name="txt_courier_company" id="txt_courier_company" class="text_boxes" style="width:140px" /></td>
                        <td width="130" align="right" >GSP Courier Date</td>
                       	<td width="160">  <input type="text" name="txt_courier_date" id="txt_courier_date" class="datepicker" style="width:140px" /> </td>
                       <td width="130" align="right">Courier Receipt No.</td>
                       <td width="170"><input type="text" name="courier_receipt_no" id="courier_receipt_no" class="text_boxes" style="width:140px" /></td>                                   
                </tr>
               
                <tr>                                   
                        
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
                        <td colspan="5"> <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:455px" /></td>
                </tr>
            </table>
            </fieldset>
            
            <br /> 
            
            <fieldset>
                <legend>
                <input type="hidden" id="prev_submitted_inv">
                Invoice List</legend>
                <table id="invo_table" width="950" class="rpt_table" rules="all">
                     <thead>
                        <tr>
                        	<th width="50">SL</th>
                            <th width="100">Invoice No</th>
                            <th width="100">Sales ContractNo</th>
                            <th width="90">BL No</th>
                            <th width="93">Invoice Date</th>
                            <th width="103">Net Inv. Value</th>
                            <th width="180"> Order Numbers</th>
                            <th width="100"> Feeder Vessel</th>
                            <th width="100"> Forwarder Name</th>
                        </tr>    
                    </thead>
    				<tbody id="invoice_container">
                        <tr id="tr0">
                            <td colspan="9"><b><center>Please Select LC/SC to view invoice List</center></b></td>
                        </tr>
                    </tbody>    
                </table>   
            </fieldset>         
            
            <table cellpadding="0" cellspacing="1" width="100%">
                <tr> 
                   <td colspan="8" align="center"></td>				
                </tr>
                <tr>
                    <td align="center" colspan="8" valign="middle" class="button_container">
                          <!-- details table id for update 
                          <input type="text" id="mst_tbl_id" name="mst_tbl_id" value="" />-->
                          <input type="hidden" id="invoice_tbl_id" name="invoice_tbl_id" value="" />
                           <!-- -->
                         <? echo load_submit_buttons( $permission, "fnc_export_doc_sub_entry", 0,0,"fnResetForm()",1);?>
                         <input type="button" class="formbutton" id="btn_print_letter" value="Print letter" style="width:100px;" onClick="fn_print_letter(1)" >&nbsp;
                         <input type="button" class="formbutton" id="btn_print_letter" value="Print letter2" style="width:100px;" onClick="fn_print_letter(2)" >
                         <input type="button" class="formbutton" id="btn_print_letter" value="Print letter3" style="width:100px;" onClick="fn_print_letter(3)" >
						 <input type="button" class="formbutton" id="btn_print_letter" value="Print letter4" style="width:100px;" onClick="fn_print_letter(4)" >
						 <input type="button" class="formbutton" id="btn_print_letter" value="Shipment Details" style="width:100px;" onClick="fn_print_letter(5)" >
                    </td>
               </tr> 
            </table>   
        	
         </form>
        </fieldset> 
 	</div>
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
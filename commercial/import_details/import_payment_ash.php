<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for Import Payment
					
Functionality	:	
				

JS Functions	:

Created by		:	Monzu 
Creation date 	: 	05/06/2013
Updated by 		: 	Jahid	
Update date		: 	 

QC Performed BY	:		

QC Date			:	

Comments		: according to requirment of Sayed bai such as validation change ( Bank Ref no popup) .

*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("BTB /Import Payment","../../", 1, 1, $unicode,'',''); 

?> 	

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission='<? echo $permission; ?>';
	function open_invoice_popup(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("hidden_btb_id");
			var theemail_invoice=this.contentDoc.getElementById("hidden_invoice_id");
			if (theemail.value!="")
			{
				freeze_window(5);
			    reset_form('importpayment_1','','','','');
				get_php_form_data(theemail.value+'_'+theemail_invoice.value, "populate_data_from_btb_lc", "requires/import_payment_controller" );
				show_list_view(theemail_invoice.value,'create_payment_list_view','grid_show','requires/import_payment_controller','');
				release_freezing();
			}
		}
	}
	
	function adjust_dom_currency()
        {
			var head_cum_value_int="";
			var invoice_id = $('#invoice_id').val();
			if(invoice_id=="")
			{
				alert("Please Insert Invoice NO");
				return;
			}
			
			var payment_head=$('#cbo_payment_head_id').val();
			
			if(payment_head==40 )
			{
				var head_cum_value=return_global_ajax_value(invoice_id+'_'+payment_head, 'set_head_cum_value', '', 'requires/import_payment_controller');
				head_cum_value_int=head_cum_value*1;
			}
			
            var conver_rate =($('#import_payment_conver_rate').val())*1;
            var accepted_ammount=($('#import_payment_accepted_ammount').val())*1;
			if(conver_rate!="" && accepted_ammount!="")
            {
			if(payment_head==40)
			{
				if( accepted_ammount>head_cum_value_int)
				{
					/*alert('Paid amount greater than invoice value not allowes'); 
					accepted_ammount=$('#import_payment_accepted_ammount').val("")
					$('#import_payment_dom_currency').val("");
					return;*/
					
					 var domistic_currency= conver_rate* accepted_ammount; 
                     $('#import_payment_dom_currency').val(domistic_currency);
				}
				else
				{
					 var domistic_currency= conver_rate* accepted_ammount; 
                     $('#import_payment_dom_currency').val(domistic_currency);
				}
			}
			else
			{
				 var domistic_currency= conver_rate* accepted_ammount; 
                $('#import_payment_dom_currency').val(domistic_currency);
			}
		}
			
            /*if(conver_rate!="" && accepted_ammount!="")
            {
                var domistic_currency= conver_rate* accepted_ammount; 
                $('#import_payment_dom_currency').val(domistic_currency);
            }*/
        }
		
function fnc_import_payment (operation)
{
	if (form_validation('txt_bank_ref*import_payment_date*cbo_payment_head_id*cbo_adj_source*adj_source_ref*import_payment_accepted_ammount','Bank Ref No.*Payment Date*Payment Head*Adj. Source*Adj. Source Ref.*Accepted Amount')==false)
	{
		return;   
	}	
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('update_id*invoice_id*btb_lc_id*import_payment_date*cbo_payment_head_id*cbo_adj_source*adj_source_ref*import_payment_conver_rate*import_payment_accepted_ammount*import_payment_dom_currency*txt_remarks',"../../");
		freeze_window(operation);
		http.open("POST","requires/import_payment_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_import_payment_reponse;
	}
			
}
		
function fnc_import_payment_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=http.responseText.split('**');
		show_msg(trim(reponse[0]));
		show_list_view(trim(reponse[1]),'create_payment_list_view','grid_show','requires/import_payment_controller','');
		reset_form('','','update_id*cbo_payment_head_id*cbo_adj_source*adj_source_ref*import_payment_conver_rate*import_payment_accepted_ammount*import_payment_dom_currency*txt_remarks','','');

		set_button_status(0, permission, 'fnc_import_payment',1);
		release_freezing();
	}
}

</script>

<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <div>
            <form name="importpayment_1" id="importpayment_1" autocomplete="off"> 
                <fieldset style="width:1024px;">
                    <legend> Import Payment</legend>
                    <table width="100%" border="0" cellpadding="0" cellspacing="2" id="tbl_importdocumentacceptance">
                        <tr height="10"></tr>
                        <tr> 
                            <td width="150" class="must_entry_caption">Bank Ref. No</td>
                            <td width="120">
                            	<input type="text" name="txt_bank_ref" id="txt_bank_ref" class="text_boxes" placeholder="Double Click for Invoice No" onDblClick="open_invoice_popup( 'requires/import_payment_controller.php?action=open_invoice_popup','Import Ref No' );" readonly style="width:140px" />
                            </td>
                            <td width="150">Importer</td>
                            <td width="120">
                            	<?php echo create_drop_down( "cbo_importer_id", 152,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name",'id,company_name', 1, 'Display',0,"",1); ?>
                            </td>
                            <td width="150">Supplier</td>
                            <td width="120" id="supplier_td">
                                <? echo create_drop_down("cbo_supplier_id",152,"select id, supplier_name from lib_supplier",'id,supplier_name', 1,'Display',0,0,1); ?> 
                            </td>
                        </tr>
                        <tr>
                        <td width="150">Invoice No</td>
                            <td width="120">
                                <input type="text" name="txt_invoice_number" id="txt_invoice_number" class="text_boxes" style="width:140px" placeholder="Display" disabled   />
                                <input type="hidden" name="invoice_id" id="invoice_id" value="" readonly/> 
                            </td>
                            <td width="150">BTB/Import LC No</td>
                            <td width="120">
                                <input type="text" name="txt_lc_number" id="txt_lc_number" class="text_boxes" style="width:140px" placeholder="Display" disabled />
                                <input type="hidden" name="btb_lc_id" id="btb_lc_id" readonly />
                                <input type="hidden" name="is_posted_account" id="is_posted_account" readonly />
                            </td>
                            <td width="150">Internal File No</td>
                            <td width="120">
                            	<input type="text" name="internal_file_no" id="internal_file_no" class="text_boxes" placeholder="Display" style="width:140px" value="" disabled/>
                            </td>	
                        </tr>
                        <tr>
                        <td width="150">Invoice Value</td>
                            <td>	
                                <input type="text" name="txt_invoice_value" id="txt_invoice_value" class="text_boxes_numeric" style="width:140px" placeholder="Display" disabled />  
                            </td>
                            <td width="150">BTB/Import LC Value</td>
                            <td width="120">
                            	<input type="text" name="txt_lc_value" id="txt_lc_value" class="text_boxes_numeric" style="width:140px" placeholder="Display"  disabled />
                            </td>
                            <td width="150">Currency</td>
                            <td>	
                                <?php echo create_drop_down( "cbo_lc_currency_id",152,$currency,'',1,'Display','',0,1); ?>  
                            </td>
                        </tr>
                        <tr>
                            <td width="150">Shipment Date</td>
                            <td width="120">
                            	<input type="text" name="txt_shipment_date" id="txt_shipment_date" class="datepicker" value="" style="width:140px" placeholder="Display" disabled /> 
                            </td>
                            <td width="150">Bank Acceptance Date</td>
                            <td>	
                              <input type="text" name="txt_bank_acceptance_date" id="txt_bank_acceptance_date" class="datepicker" style="width:140px" placeholder="Display" disabled /> 
                            </td>	
                            <td width="150">BL/Cargo Date</td>
                            <td>	
                             	<input type="text" name="bill_date" id="bill_date" class="datepicker" value="" style="width:140px" placeholder="Display" disabled />
                            </td>	
                        </tr>
                        <tr>
                            <td width="150">Issuing Bank</td>
                            <td width="120">
                            	<?php echo create_drop_down( "cbo_issuing_bank", 152,"select id,bank_name from lib_bank where is_deleted=0 and status_active=1 and issusing_bank = 1 order by bank_name",'id,bank_name', 1, 'Display',0,0,1); ?>
                            </td>
                            <td width="150">Maturity From</td>
                            <td>	
                          		<?php echo create_drop_down( "cbo_maturit_from_id",152,$maturity_from,'',1,'Display',0,0,1); ?> 
                            </td>	
                            <td width="150">Maturity Date</td>
                            <td>	
                            	<input type="text" name="maturity_date" id="maturity_date" class="datepicker" style="width:140px" placeholder="Display" disabled   />
                            </td>	
                        </tr>
                        <tr>
                            <td width="150">Issuing Bank</td>
                            <td width="120">
                            	<?php echo create_drop_down( "cbo_issuing_bank", 152,"select id,bank_name from lib_bank where is_deleted=0 and status_active=1 and issusing_bank = 1 order by bank_name",'id,bank_name', 1, 'Display',0,0,1); ?>
                            </td>
                            <td width="150">Maturity From</td>
                            <td>	
                          		<?php echo create_drop_down( "cbo_maturit_from_id",152,$maturity_from,'',1,'Display',0,0,1); ?> 
                            </td>	
                            <td width="150">Maturity Date</td>
                            <td>	
                            	<input type="text" name="maturity_date" id="maturity_date" class="datepicker" style="width:140px" placeholder="Display" disabled   />
                            </td>	
                        </tr>
                    </table>
                </fieldset>
                <fieldset style="width:1024px;">
                    <legend>Payment Entry</legend> 
                    <table width="100%" border="0" cellpadding="0" cellspacing="2" id="tbl_importdocumentacceptance">
                        <tr height="10"></tr>
                        <tr> 
                            <td width="150" class="must_entry_caption">Payment Date</td>
                            <td width="120">
                            	<input type="text"  class="datepicker" id="import_payment_date" style="width:140px"/>
                            </td>
                            <td width="150" class="must_entry_caption">Payment Head</td>
                            <td width="120">
								<?php echo create_drop_down( "cbo_payment_head_id", 152,$commercial_head,'', 1, '-Select-','','adjust_dom_currency()','','40,45,70'); ?>
                            </td>
                            <td width="150" class="must_entry_caption">Adj. Source</td>
                            <td>	
                                <?php echo create_drop_down( "cbo_adj_source", 152,$commercial_head,'', 1, '-Select-','','','','5,6,10,11,15,16,30,31,32,33,34,35,36,71,75,76,80,81,82,83'); ?>  
                            </td>						
                        </tr>
                        <tr> 
                        	<td width="150" class="must_entry_caption">Adj. Source Ref.</td>
                            <td width="120">
								<input type="text" class="text_boxes" id="adj_source_ref" style="width:140px" />
                            </td>
                            <td width="150">Conversion Rate</td>
                            <td width="120">
                            	<input type="text" class="text_boxes_numeric" id="import_payment_conver_rate" style="width:140px"   onBlur="adjust_dom_currency()" />
                            </td>
                            <td width="150" class="must_entry_caption">Accepted Amount</td>
                            <td width="120">
								<input type="text" class="text_boxes_numeric" id="import_payment_accepted_ammount" style="width:140px"  onBlur="adjust_dom_currency()" />
                            </td>
                        </tr>
                        <tr> 
                        	<td width="150">Dom Currency</td>
                            <td>	
                                <input type="text" class="text_boxes" readonly id="import_payment_dom_currency" style="width:140px" disabled /> 
                            </td>
                            <td width="150">Remarks</td>
                            <td width="120" colspan="3">
                            	<input type="text" class="text_boxes"  id="txt_remarks" name="txt_remarks" style="width:500px;" />
                            </td>
                        </tr>
                        <tr>
                    		<td colspan="6" valign="middle" align="center" class="button_container">						
								<? echo load_submit_buttons( $permission, "fnc_import_payment", 0,0 ,"reset_form('importpayment_1','','','','')",1) ; ?>
                             	<input type="hidden" class="text_boxes"  id="update_id" name="update_id"  />
                           </td>   				
                        </tr>
                    </table>           
                    <fieldset><div id='grid_show' style="height:auto; overflow:auto"></div></fieldset>
                         <p>&nbsp;</p>
                    </fieldset>				
                 </fieldset>
            </form>
        </div>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
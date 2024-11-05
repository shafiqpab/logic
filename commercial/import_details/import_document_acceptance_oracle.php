<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for Import Document Acceptance
					
Functionality	:	
				

JS Functions	:

Created by		:	Monzu 
Creation date 	: 	01/06/2013
Updated by 		: 		
Update date		: 	jahid 

QC Performed BY	:		

QC Date			:	

Comments		: according to requirment of Sayed bai such as validation change (Lc no popup & search panel & list view) .

*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("BTB /Import Document Acceptance","../../", 1, 1, $unicode,'',''); 

?> 	

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission='<? echo $permission; ?>';
	
	function open_import_lc_popup(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1070px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("hidden_btb_id");
			if (theemail.value!="")
			{
				freeze_window(5);
			    reset_form('importdocumentacceptance_1','','','','');
				get_php_form_data(theemail.value+'_'+1, "populate_data_from_btb_lc", "requires/import_document_acceptance_controller" );
				show_list_view(theemail.value+'_'+1,'pi_listview','pi_list','requires/import_document_acceptance_controller','');
				//set_button_status(1, permission, 'fnc_order_entry',1);
				release_freezing();
			}
		}
	}
	function check_duplicate_invoice(invoice_no)
	{
	  if(invoice_no!="")
	  {
	   var btb_lc_id=document.getElementById('btb_lc_id').value;
	   var invoice_no_found=return_global_ajax_value(invoice_no+'_'+btb_lc_id, 'check_duplicate_invoice', '', 'requires/import_document_acceptance_controller');
	   if(invoice_no==invoice_no_found)
	   {
		  alert("Same Invoice Number Found");
		  document.getElementById('txt_invoice_number').value="";
		  document.getElementById('invoice_id').value="";
		  reset_form('importdocumentacceptance_1','','','','');
		  get_php_form_data(btb_lc_id+'_'+1, "populate_data_from_btb_lc", "requires/import_document_acceptance_controller" );
		  show_list_view(btb_lc_id+'_'+1,'pi_listview','pi_list','requires/import_document_acceptance_controller','');
		  document.getElementById('txt_invoice_number').focus();
	   }
	  }

	}
	
	function open_invoice_popup(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("hidden_btb_id");
			var theemail_invoice=this.contentDoc.getElementById("hidden_invoice_id");
			if (theemail.value!="")
			{
				freeze_window(5);
			    reset_form('importdocumentacceptance_1','','','','');
				get_php_form_data(theemail.value+'_'+2, "populate_data_from_btb_lc", "requires/import_document_acceptance_controller" );
				show_list_view(theemail.value+'_'+2+'_'+theemail_invoice.value,'pi_listview','pi_list','requires/import_document_acceptance_controller','');
				get_php_form_data(theemail_invoice.value, "populate_data_from_invoice", "requires/import_document_acceptance_controller" );
				//show_list_view(theemail.value,'pi_listview','pi_list','requires/import_document_acceptance_controller','');
				set_button_status(1, permission, 'fnc_import_document_acceptance',1);
				release_freezing();
			}
		}
	}
	
	function fnc_import_document_acceptance( operation )
	{
		var data_all="";
		var pay_term=$('#cbo_payterm_id').val();
		if(pay_term==1)
		{
			var hid_maturity_from=document.getElementById('hid_maturity_from').value;
			 if(hid_maturity_from==1)
			 {
				 if (form_validation('txt_lc_number*txt_invoice_number*txt_invoice_date*txt_bank_acceptance_date*cbo_retire_source','Lc Number*Invoice Number*Invoice Date*Acceptance Date*Retire Source')==false)
				{
					return;
				}
			 }
			 else if(hid_maturity_from==2)
			 {
				 if (form_validation('txt_lc_number*txt_invoice_number*txt_invoice_date*txt_shipment_date*cbo_retire_source','Lc Number*Invoice Number*Invoice Date*Ship Date*Retire Source')==false)
				{
					return;
				}
			 }
			 else if(hid_maturity_from==3)
			 {
				 if (form_validation('txt_lc_number*txt_invoice_number*txt_invoice_date*nagotiate_date*cbo_retire_source','Lc Number*Invoice Number*Invoice Date*Nagotiate Date*Retire Source')==false)
				{
					return;
				}
				
				/*if (form_validation('txt_lc_number*txt_invoice_number*txt_invoice_date*cbo_retire_source','Lc Number*Invoice Number*Invoice Date*Retire Source')==false)
				{
					return;
				}*/
			 }
			 else if(hid_maturity_from==4)
			 {
				 if (form_validation('txt_lc_number*txt_invoice_number*txt_invoice_date*bill_date*cbo_retire_source','Lc Number*Invoice Number*Invoice Date*BL Date*Retire Source')==false)
				{
					return;
				}
			 }
			
		}
		else
		{
			if (form_validation('txt_lc_number*txt_invoice_number*txt_invoice_date','Lc Number*Invoice Number*Invoice Date')==false)
			{
				return;
			}
		}
		
		data_all=data_all+get_submitted_data_string('btb_lc_id*txt_invoice_number*invoice_id*txt_invoice_date*txt_document_value*txt_shipment_date*txt_company_acc_date*txt_bank_acceptance_date*txt_bank_ref*cbo_acceptance_time*cbo_retire_source*txt_remarks*cbo_lc_type_id*bill_no*bill_date*cbo_shipment_mode*cbo_document_status*copy_doc_receive_date*original_doc_receive_date*doc_to_cnf*feeder_vessel*mother_vessel*eta_date*ic_receive_date*shipping_bill_no*cbo_inco_term*inco_term_place*port_of_loading*port_of_discharge*bill_of_entry_no*psi_reference_no*maturity_date*container_no*pkg_quantity*tot_current_acceptance_value*cbo_payterm_id*hid_maturity_from*nagotiate_date',"../../");
		var row_num=$('#tbl_list_search tr').length-1;
		var accep_val_check="";
		for (var i=1; i<=row_num; i++)
		{
			/*if (form_validation('pi_number_'+i+'*current_acceptance_value_'+i,'PI Number*Current Acceptance Value')==false)
			{
				return;
			}
			else
			{
				data_all=data_all+get_submitted_data_string('pi_id_'+i+'*current_acceptance_value_'+i+'*invoice_dtls_id_'+i,"../../",i);
			}*/
			if($("#current_acceptance_value_"+i).val()>0 && accep_val_check=="")
			{
				accep_val_check=$("#current_acceptance_value_"+i).val();
			}
			data_all=data_all+get_submitted_data_string('pi_id_'+i+'*current_acceptance_value_'+i+'*invoice_dtls_id_'+i,"../../",i);
		}
		
		if(accep_val_check=="")
		{
			alert("Pleaze Fill Up At Least One Field Of Current Acceptance Value Collum");
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all;
		//alert (data);return;
		freeze_window(operation);
		http.open("POST","requires/import_document_acceptance_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_import_document_acceptance_reponse;
	}
	 
	function fnc_import_document_acceptance_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);release_freezing();return;
			 var reponse=trim(http.responseText).split('**');
			 if(reponse[0]==30) 
		     {
				 alert(reponse[1]);release_freezing();return;
			 }
			 if(reponse[0]==15) 
		     { 
				setTimeout('fnc_import_document_acceptance('+ reponse[1]+')',8000); 
			 }
			 else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			 {
				show_msg(reponse[0]);
				get_php_form_data(reponse[1]+'_'+2, "populate_data_from_btb_lc", "requires/import_document_acceptance_controller" );
				show_list_view(reponse[1]+'_'+2+'_'+reponse[2],'pi_listview','pi_list','requires/import_document_acceptance_controller','');
				get_php_form_data(reponse[2], "populate_data_from_invoice", "requires/import_document_acceptance_controller" );
				set_button_status(1, permission, 'fnc_import_document_acceptance',1);
				release_freezing();
			 }
			 else
			 {
				show_msg(reponse[0]);
				release_freezing();return; 
			 }
		}
	}
	
	
	function calculate(field_id)
    {
        var total_acc_ammount        = "";
		var total_cumulative_ammount = "";
        value                        = $('#current_acceptance_value_'+field_id).val();
        prev_val                     =  $('#hide_current_acceptance_value_'+field_id).val();
        prev_cum_val                 = $('#hide_cumulative_accept_amount_'+field_id).val(); 
        new_cum_val                  = ((value*1)-( prev_val*1))+(prev_cum_val*1);        
        $('#cumulative_accept_amount_'+field_id).val(new_cum_val);
        tolarance                    = $('#hid_tolarance').val();
		lcvalue                      = $('#txt_lc_value').val();
		lcvalue_with_tolarance       = (lcvalue*1)+(((lcvalue*1)*(tolarance*1))/100);
		
		 for( var i = 1; i <= $('#pi_list div table tbody tr').length; i++ ) 
         {
            total_acc_ammount        = (total_acc_ammount*1) +($('#current_acceptance_value_'+i).val()*1);
			total_cumulative_ammount = (total_cumulative_ammount*1) +($('#cumulative_accept_amount_'+i).val()*1);
		 }
		 if(total_cumulative_ammount>lcvalue_with_tolarance)
		 {
			alert("Cumulative Accepted Amount Exceeds LC Value") ;
			total_acc_ammount=(total_acc_ammount-(document.getElementById('current_acceptance_value_'+field_id).value)*1)+(prev_val*1)
			total_cumulative_ammount=(total_cumulative_ammount-(document.getElementById('cumulative_accept_amount_'+field_id).value)*1)+(prev_cum_val*1)
			document.getElementById('current_acceptance_value_'+field_id).value=prev_val;
			document.getElementById('cumulative_accept_amount_'+field_id).value=prev_cum_val;
		 }
		   
        $('#tot_current_acceptance_value').val(total_acc_ammount);  
		$('#tot_cumula_acceptance_value').val(total_cumulative_ammount); 
		var total_balance=0;
		var balance=($('#pi_value_'+field_id).val()*1)-($('#cumulative_accept_amount_'+field_id).val()*1);
		$('#cumulative_accept_balance_'+field_id).val(balance);
		for( var i = 1; i <= $('#pi_list div table tbody tr').length; i++ ) 
         {
            total_balance        = (total_balance*1) +($('#cumulative_accept_balance_'+i).val()*1);
		 }
		 $('#tot_cumula_balance_value').val(total_balance); 

    }
	
	function set_maturity_date(date,type)
	{
		 var payterm_id=document.getElementById('cbo_payterm_id').value;
		 if(payterm_id==0)
		 {
			alert("Select LC") ;
			return;
		 }
	
		 var hid_tenor=document.getElementById('hid_tenor').value;
		 var hid_maturity_from=document.getElementById('hid_maturity_from').value;
		/* if(payterm_id==2)
		 {
			 if(type=="bank_acc_date" && hid_maturity_from==1)
			 {
				 var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_controller');
				 document.getElementById('maturity_date').value=maturity_date
	
			 }
			 else if(type=="shipment_date" && hid_maturity_from==2 )
			 {
				 //alert(hid_maturity_from) 
				 var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_controller');
				 document.getElementById('maturity_date').value=maturity_date
			 }
			 
			 else if(type=="bl_date" && hid_maturity_from==4 )
			 {
				 
				 var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_controller');
				 document.getElementById('maturity_date').value=maturity_date
			 }
		 }*/
	 
	 	if(type=="bank_acc_date" && hid_maturity_from==1)
		 {
			 var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_controller');
             document.getElementById('maturity_date').value=maturity_date

		 }
		 else if(type=="shipment_date" && hid_maturity_from==2 )
		 {
			 //alert(hid_maturity_from) 
			 var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_controller');
             document.getElementById('maturity_date').value=maturity_date
		 }
		 else if(type=="nagotiate_date" && hid_maturity_from==3 )
		 {
			 //alert(hid_maturity_from) 
			 var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_controller');
             document.getElementById('maturity_date').value=maturity_date
		 }
		 
		 else if(type=="bl_date" && hid_maturity_from==4 )
		 {
			 
			 var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_controller');
             document.getElementById('maturity_date').value=maturity_date
		 }

	}
	
	
	function show_me_cumu_stat(pi_id)
	{
			var page_link='requires/import_document_acceptance_controller.php?action=cumulative_details_popup&pi_id='+pi_id
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Cumulative Details", 'width=580px,height=450px,center=1,resize=0,scrolling=0','../')
	}
	
	//fnc_ship_grnt()
	function fnc_ship_grnt()
	{
		//alert("su..re");
		if (form_validation('invoice_id','Back To Back')==false )
		{
			alert("Please fill up invoice number");
			$('#txt_invoice_number').focus();
			return;
		}
		print_report(1+'**'+$('#invoice_id').val()+'**'+$('#txt_lc_number').val()+'**'+$('#cbo_importer_id').val(),'import_document_acceptance_letter','requires/import_document_acceptance_controller');
	}
	
	//fnc_consignment()
	function fnc_consignment()
	{
		//alert("Waitting for decision of dada"); return;
		if (form_validation('invoice_id','Back To Back')==false )
		{
			alert("Please fill up invoice number");
			$('#txt_invoice_number').focus();
			return;
		}
		print_report(2+'**'+$('#invoice_id').val()+'**'+$('#txt_lc_number').val()+'**'+$('#cbo_importer_id').val(),'import_document_acceptance_letter','requires/import_document_acceptance_controller');
	}
</script>


 
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <div>
        	
            <form name="importdocumentacceptance_1" id="importdocumentacceptance_1" autocomplete="off"> 
                <fieldset style="width:1024px;">
                    <legend>BTB / Import LC Invoice Details</legend>
                    <table width="100%" border="0" cellpadding="0" cellspacing="1" id="tbl_importdocumentacceptance">
                        <tr height="10"></tr>
                        <tr> 
                            <td width="150" class="must_entry_caption">LC Number</td>
                            <td width="120">
                            <input type="text" name="txt_lc_number" id="txt_lc_number" class="text_boxes" placeholder="Double Click for LC No" onDblClick="open_import_lc_popup( 'requires/import_document_acceptance_controller.php?action=open_import_lc_popup','BTB / Import LC List' );" style="width:140px" readonly/>
                             <input type="hidden" name="btb_lc_id" id="btb_lc_id" value="" />
                             
                            </td>
                            <td width="150" class="must_entry_caption">Invoice Number</td>
                            <td width="120"><input type="text" name="txt_invoice_number" id="txt_invoice_number" class="text_boxes" placeholder="Double Click for Invoice No" onDblClick="open_invoice_popup( 'requires/import_document_acceptance_controller.php?action=open_invoice_popup','Import Invoice List' );" onChange="check_duplicate_invoice(this.value)" style="width:139px" />
                            <input type="hidden" name="invoice_id" id="invoice_id" value=""/> </td>
                            <td width="150">Issuing Bank</td>
                            <td>	
                                <?php echo create_drop_down( "cbo_issuing_bank", 165,"select id,bank_name from lib_bank where is_deleted=0 and status_active=1 and issusing_bank = 1 order by bank_name",'id,bank_name', 1, 'Display',0,0,1); ?>       
                            </td>						
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Invoice Date</td>
                            <td> 
                                 <input type="text" name="txt_invoice_date" id="txt_invoice_date" class="datepicker" style="width:140px" value="" />  
                            </td>
                        	<td>Document Value</td>
                            <td> 
                            <input type="text" name="txt_document_value" id="txt_document_value" class="text_boxes_numeric" style="width:139px" /></td>
                            <td>LC Value & Currency</td>
                            <td>
                            <input type="text" name="txt_lc_value" id="txt_lc_value" class="text_boxes_numeric" placeholder="Display"  style="width:80px" disabled />
                            <?php echo create_drop_down( "cbo_lc_currency_id",70,$currency,'',1,'Display','',0,1); ?>
                            <input type="hidden" name="hid_tolarance" id="hid_tolarance" value="" readonly /> 
                            <input type="hidden" name="hid_tenor" id="hid_tenor" value="" readonly /> 
                            <input type="hidden" name="hid_maturity_from" id="hid_maturity_from" value="" readonly/> 
                            </td>
                        </tr>
                        <tr>
                        	<td>Shipment Date</td>
                            <td> 
                                 <input type="text" name="txt_shipment_date" id="txt_shipment_date" class="datepicker" style="width:140px" onChange="set_maturity_date(this.value,'shipment_date')" />  
                            </td>
                        	<td>Company Acc. Date</td>
                            <td> 
                                 <input type="text" name="txt_company_acc_date" id="txt_company_acc_date" class="datepicker" style="width:139px" />  
                            </td>
                            <td>Supplier</td>
                            <td id="supplier_td">	
                                <?php echo create_drop_down( "cbo_supplier_id", 165,$blank_array,'', 1, 'Display',0,0,1); ?>  
                                     
                            </td>		
                        </tr>
                        <tr>
                        	<td>Bank Acc. Date</td>
                            <td> 
                                 <input type="text" name="txt_bank_acceptance_date" id="txt_bank_acceptance_date" class="datepicker" style="width:140px"  onChange="set_maturity_date(this.value,'bank_acc_date')"/>  
                            </td>
                            <td>Bank Ref</td>
                            <td><input type="text" name="txt_bank_ref" id="txt_bank_ref" class="text_boxes" style="width:139px"/></td>
                            <td width="150">Importer</td>
                            <td width="120">
                                 <?php echo create_drop_down( "cbo_importer_id", 165,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name",'id,company_name', 1, 'Display',0,"",1); ?>       
                            </td>
                        </tr>
                        <tr>
                        	<td height="24">Acceptance Time</td>
                          <td><?php echo create_drop_down( "cbo_acceptance_time",152,$acceptance_time,'',0,'',"",0,0); ?> </td>
                            <td>Retire Source</td>
                            <td>
                            	<?php echo create_drop_down( "cbo_retire_source",150,$commercial_head,'',1,'--Select--',"",0,0,'5,6,10,11,15,16,30,31,32,33,34,35,71'); ?>  
                            </td>
                            <td>Pay Term</td>
							<td><?php echo create_drop_down( "cbo_payterm_id",165,$pay_term,'',1,'Display',0,"",1); ?> </td>
                        </tr>
                        <tr>
                        	<td>Nagotiate Date</td>
                            <td>
                        	<input type="text" name="nagotiate_date" id="nagotiate_date" class="datepicker" value="" style="width:140px" onChange="set_maturity_date(this.value,'nagotiate_date')" />
                            </td>
                        	<td>Remarks</td>
							<td ><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" value="" style="width:140px"  maxlength="200"/></td>
                              
                            <td>L/C Type</td>
                             <td>
                                 <?php echo create_drop_down( "cbo_lc_type_id",165,$lc_type,'',1,'Display',"","",1); ?>  
                            </td>
                        </tr>
                    </table>
                </fieldset>
                
                  <div id="pi_list"> Pi List </div><br/>
                <fieldset style="width:1024px; margin-bottom:10px;">
				<legend>BTB / Import LC Shipment Details</legend>
				<table width="100%" border="0" cellpadding="0" cellspacing="1">
					<tr>
						<td>BL/Cargo No</td>
						<td><input type="text" name="bill_no" id="bill_no" class="text_boxes" style="width:140px" /></td>
						<td>BL/Cargo Date</td>
						<td><input type="text" name="bill_date" id="bill_date" class="datepicker" value="" style="width:140px" onChange="set_maturity_date(this.value,'bl_date')" /></td>
						<td>Shipment Mode</td>
						<td>
							 <?php echo create_drop_down( "cbo_shipment_mode",152,$shipment_mode,'',1,'-Select',"","",""); ?> 
						</td>
					</tr>
					<tr>
						<td>Document Status</td>
						<td>
							<?php echo create_drop_down( "cbo_document_status",152,$document_status,'',1,'-Select',"","",""); ?> 
						</td>
						<td>Copy Doc Receive Date</td>
						<td><input type="text" name="copy_doc_receive_date" style="width:140px" id="copy_doc_receive_date" class="datepicker" value=""  /></td>
						<td>Original Doc Receive Date</td>
						<td><input type="text" name="original_doc_receive_date" style="width:140px" id="original_doc_receive_date" class="datepicker" value=""  /></td>
					</tr>
					<tr>
						<td>Document to C&amp;F</td>
						<td><input type="text" name="doc_to_cnf" id="doc_to_cnf" class="datepicker" style="width:140px" value="" /></td>
						<td>Feeder Vessel </td>
						<td><input type="text" name="feeder_vessel" id="feeder_vessel" class="text_boxes" style="width:140px" value="" /></td>
						<td>Mother Vessel</td>
						<td><input type="text" name="mother_vessel" id="mother_vessel" class="text_boxes" style="width:140px" value="" /></td>
					</tr>
					<tr>
						<td>ETA Date</td>
						<td><input type="text" name="eta_date" id="eta_date" class="datepicker" style="width:140px" value="" /></td>
						<td>IC Received Date</td>
						<td><input type="text" name="ic_receive_date" id="ic_receive_date" style="width:140px" class="datepicker" value=""  /></td>
						<td>Shipping Bill No</td>
						<td><input type="text" name="shipping_bill_no" id="shipping_bill_no" style="width:140px" class="text_boxes" value="" /></td>
					</tr>
					<tr>
						<td>Inco term</td>
						<td>
							<?php echo create_drop_down( "cbo_inco_term",152,$incoterm,'',1,'-Select',"","",""); ?>
						</td>
						<td>Inco term Place</td>
						<td><input type="text" name="inco_term_place" id="inco_term_place" style="width:140px" class="text_boxes" value="" /></td>
						<td>Port of Loading</td>
						<td><input type="text" name="port_of_loading" id="port_of_loading" style="width:140px" class="text_boxes" value="" /></td>
					</tr>
					<tr>
						<td>Port of Discharge</td>
						<td><input type="text" name="port_of_discharge" id="port_of_discharge" style="width:140px" class="text_boxes" value="" /></td>
						<td>Internal File No</td>
						<td><input type="text" name="internal_file_no" id="internal_file_no" style="width:140px" class="text_boxes" placeholder="Display" value="" disabled/></td>
						<td>Bill Of Entry No</td>
						<td><input type="text" name="bill_of_entry_no" id="bill_of_entry_no" style="width:140px" class="text_boxes" value="" /></td>
					</tr>
					<tr>
						<td>PSI Reference No</td>
						<td><input type="text" name="psi_reference_no" id="psi_reference_no" style="width:140px" class="text_boxes" value="" /></td>
                        <td>Maturity Date</td>
						<td><input type="text" name="maturity_date" id="maturity_date" style="width:140px" class="datepicker" value=""  /></td>
                        <td>Container No</td>
						<td><input type="text" name="container_no" id="container_no" style="width:140px" class="text_boxes" value="" /></td>
					</tr>
                    <tr>
                         <td>Pakg Quantity</td>  
						<td><input type="text" name="pkg_quantity" id="pkg_quantity" style="width:140px" class="text_boxes" value="" /></td>
					</tr>
                    <tr>
                         	<td colspan="6" height="15"></td>
                         </tr>
                        <tr>
                            <td colspan="6" height="50" valign="middle" align="center" class="button_container">						
                                <? 
							  		echo load_submit_buttons( $permission, "fnc_import_document_acceptance", 0,0 ,"reset_form('importdocumentacceptance_1','pi_list','','','')",1) ; 
							    ?>
                                <input type="button" value="Ship Grnt." id="btn_ship_grnt" name="btn_ship_grnt" class="formbutton" style="width:100px;" onClick="fnc_ship_grnt()" /> &nbsp;
                                <input type="button" value="Consignment" id="btn_consignment" name="btn_consignment" class="formbutton" style="width:100px;" onClick="fnc_consignment()" />
                             </td>                          			
                        </tr> 
				</table>
			</fieldset>
            </form>
             
        </div>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
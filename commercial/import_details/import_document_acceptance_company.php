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

$item_category_without_general=array_diff($item_category,$general_item_category);
$genarel_item_arr=array(4=>"Accessories",8=>"General Item");
$item_category_with_gen=$item_category_without_general+$genarel_item_arr;
ksort($item_category_with_gen);

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("BTB /Import Document Acceptance","../../", 1, 1, $unicode,'','');

?>
<?
	//Array for Newly added fiels (shafiq-sumon);
	$container_status = array(1=>"FCL", 2=>"LCL", 3=>"AWB");
	$container_size = array(1=>"20 ft GP", 2=>"20 ft HQ", 3=>"40 ft GP", 4=>"40 ft HQ");
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
			var impoter_id=this.contentDoc.getElementById("hidden_impoter_id").value;
			var ref_closing_status=this.contentDoc.getElementById("hidden_ref_closing_staus").value;
			if (theemail.value!="")
			{
				freeze_window(5);
			    reset_form('importdocumentacceptance_1','','','','');
				var lc_popup=1;
				get_php_form_data(theemail.value+'_'+lc_popup, "populate_data_from_btb_lc", "requires/import_document_acceptance_company_controller" );
				show_list_view(theemail.value+'_'+lc_popup+'___'+impoter_id,'pi_listview','pi_list','requires/import_document_acceptance_company_controller','');
				$("#check_account_posted_td").html('');
				validate_after_validate(0);
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
	   var impoter_id=document.getElementById('cbo_importer_id').value;
	   var invoice_no_found=return_global_ajax_value(invoice_no+'_'+btb_lc_id, 'check_duplicate_invoice', '', 'requires/import_document_acceptance_company_controller');
	   if(invoice_no==invoice_no_found)
	   {
		  alert("Same Invoice Number Found");
		  document.getElementById('txt_invoice_number').value="";
		  document.getElementById('invoice_id').value="";
		  reset_form('importdocumentacceptance_1','','','','');
		  get_php_form_data(btb_lc_id+'_'+1, "populate_data_from_btb_lc", "requires/import_document_acceptance_company_controller" );
		  show_list_view(btb_lc_id+'_'+1+'___'+impoter_id,'pi_listview','pi_list','requires/import_document_acceptance_company_controller','');
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
			var posted_in_account=this.contentDoc.getElementById("posted_in_account").value;
			var impoter_id=this.contentDoc.getElementById("impoter_id").value;
			var ref_closing_staus=this.contentDoc.getElementById("hidden_ref_closing_staus").value;

			if (theemail.value!="")
			{
				freeze_window(5);
			    reset_form('importdocumentacceptance_1','','','','');
				var invoice_popup=2;
				get_php_form_data(theemail.value+'_'+invoice_popup, "populate_data_from_btb_lc", "requires/import_document_acceptance_company_controller" );
				show_list_view(theemail.value+'_'+invoice_popup+'_'+theemail_invoice.value+'_'+posted_in_account+'_'+impoter_id+'_'+ref_closing_staus,'pi_listview','pi_list','requires/import_document_acceptance_company_controller','');
				get_php_form_data(theemail_invoice.value, "populate_data_from_invoice", "requires/import_document_acceptance_company_controller" );
				//show_list_view(theemail.value,'pi_listview','pi_list','requires/import_document_acceptance_company_controller','');
				if( 1==posted_in_account ) $("#check_account_posted_td").text("Already Posted In Accounting.");
				else $("#check_account_posted_td").text("");
				validate_after_validate(posted_in_account);
				$("#is_posted_account").val(posted_in_account);
				set_button_status(1, permission, 'fnc_import_document_acceptance',1);
				release_freezing();
			}
		}
	}

	function validate_after_validate(posting_type)
	{
		if(posting_type==1)
		{
			$('#txt_invoice_number').attr('readonly', true);
			$('#txt_bank_ref').attr('readonly', true);
			$('#txt_exchange_rate').attr('readonly', true);
			$('#txt_bank_acceptance_date').attr('disabled', true);
			$('#cbo_acceptance_time').attr('disabled', true);
			$('#nagotiate_date').attr('disabled', true);
			$('#cbo_retire_source').attr('disabled', true);
		}
		else
		{
			$('#txt_invoice_number').attr('readonly', false);
			$('#txt_bank_ref').attr('readonly', false);
			$('#txt_exchange_rate').attr('readonly', false);
			$('#txt_bank_acceptance_date').attr('disabled', false);
			$('#cbo_acceptance_time').attr('disabled', false);
			$('#nagotiate_date').attr('disabled', false);
			$('#cbo_retire_source').attr('disabled', false);
		}
	}


	function fnc_import_document_acceptance( operation )
	{
		var data_all="";
		var pay_term=$('#cbo_payterm_id').val();

		/*if($("#is_posted_account").val()==1){
			alert("Already Posted In Accounting. Update and Delete Restricted.");
			return;
		}*/

		if(pay_term==1 || pay_term==2)
		{
			if (form_validation('txt_lc_number*txt_invoice_number*txt_invoice_date*txt_company_acc_date*txt_exchange_rate','Lc Number*Invoice Number*Invoice Date*Company Acceptance Date*Exchange Rate')==false)
			{
				return;
			}
		}
		else
		{
			if (form_validation('txt_lc_number*txt_invoice_number*txt_invoice_date','Lc Number*Invoice Number*Invoice Date')==false)
			{
				return;
			}
		}

		

		/*var adjoust_source_id=$("#cbo_retire_source").val();
		if(adjoust_source_id==30 || adjoust_source_id==142 || adjoust_source_id==31 || adjoust_source_id==32 || adjoust_source_id==33 || adjoust_source_id==34 || adjoust_source_id==35 || adjoust_source_id==36)
		{
			if (form_validation('txt_loan_reff','Loan Ref.')==false)
			{
				return;
			}
		}
		
		var document_value=$("#txt_document_value").val()*1;
		var bill_entry_value=$("#txt_bill_entry_value").val()*1;
		if(document_value!=bill_entry_value && bill_entry_value>0)
		{
			alert("Document Value and Bill of Entry Value Must Be Same");return;
		}*/

		data_all=data_all+get_submitted_data_string('btb_lc_id*txt_invoice_number*invoice_id*txt_invoice_date*txt_document_value*txt_shipment_date*txt_company_acc_date*txt_bank_acceptance_date*txt_bank_ref*cbo_acceptance_time*cbo_retire_source*txt_remarks*txt_edf_tenor*cbo_lc_type_id*bill_no*bill_date*cbo_shipment_mode*cbo_document_status*cbo_forwarder_name*copy_doc_receive_date*original_doc_receive_date*doc_to_cnf*feeder_vessel*mother_vessel*eta_date*ic_receive_date*shipping_bill_no*cbo_inco_term*inco_term_place*port_of_loading*port_of_discharge*bill_of_entry_no*psi_reference_no*maturity_date*container_no*pkg_quantity*pkg_quantity_breakdown*tot_current_acceptance_value*cbo_payterm_id*hid_maturity_from*nagotiate_date*edf_paid_date*etd_actual*pakg_uom*eta_advice*eta_actual*cbo_container_status*cbo_container_size*release_date*boe_date*txt_exchange_rate*txt_loan_reff*txt_doc_rcv_date*txt_local_date*cbo_ready_to_approved*hide_approved_status*txt_bill_entry_value*txt_submit_date*txt_courier_no*cbo_source_id*txt_courier_date',"../../");
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
			data_all=data_all+get_submitted_data_string('pi_id_'+i+'*current_acceptance_value_'+i+'*invoice_dtls_id_'+i+'*item_category_id_'+i,"../../",i);
		}

		if(accep_val_check=="")
		{
			alert("Please Fill Up At Least One Field Of Current Acceptance Value Collum");
			return;
		}

		var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all;
		//alert (data);return;
		freeze_window(operation);
		http.open("POST","requires/import_document_acceptance_company_controller.php",true);
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
			 if(reponse[0]==2)
			 {
				show_msg(reponse[0]);
				location.reload();
				release_freezing();return;
			 }
			 
			 if(reponse[0]==30)
		     {
				 alert(reponse[1]);release_freezing();return;
			 }
			 if(reponse[0]==15)
		     {
				setTimeout('fnc_import_document_acceptance('+ reponse[1]+')',8000);
				release_freezing();return;
			 }
			 else if(reponse[0]==11)
			 {
				show_msg(reponse[0]);
				alert(reponse[1]);release_freezing();return;
			 }
			 else if(reponse[0]==0 || reponse[0]==1)
			 {
				var inpoter_id=$('#cbo_importer_id').val();
				show_msg(reponse[0]);
				get_php_form_data(reponse[1]+'_'+2, "populate_data_from_btb_lc", "requires/import_document_acceptance_company_controller" );
				show_list_view(reponse[1]+'_'+2+'_'+reponse[2]+'__'+inpoter_id,'pi_listview','pi_list','requires/import_document_acceptance_company_controller','');
				get_php_form_data(reponse[2], "populate_data_from_invoice", "requires/import_document_acceptance_company_controller" );
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
        var value					= $('#current_acceptance_value_'+field_id).val();
		var ref_closing_status		= $('#hide_current_acceptance_value_'+field_id).attr('title');

		var prev_val				=  $('#hide_current_acceptance_value_'+field_id).val();
        var prev_cum_val			= $('#hide_cumulative_accept_amount_'+field_id).val();
        
		if(ref_closing_status==1)
		{
			alert("This PI Already Closed. Modification not Allowed.");
			$('#current_acceptance_value_'+field_id).val(prev_cum_val);
			$('#current_acceptance_value_'+field_id).attr("readonly",true);
			$('#current_acceptance_value_'+field_id).attr("disabled",true);
			return;
		}
        var new_cum_val				= ((value*1)-( prev_val*1))+(prev_cum_val*1);
        $('#cumulative_accept_amount_'+field_id).val(new_cum_val);
        var tolarance				= $('#hid_tolarance').val();
		var lcvalue					= $('#txt_lc_value').val();
		var lcvalue_with_tolarance	= (lcvalue*1)+(((lcvalue*1)*(tolarance*1))/100);

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
		$('#txt_document_value').val(total_acc_ammount);
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
				 var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_company_controller');
				 document.getElementById('maturity_date').value=maturity_date

			 }
			 else if(type=="shipment_date" && hid_maturity_from==2 )
			 {
				 //alert(hid_maturity_from)
				 var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_company_controller');
				 document.getElementById('maturity_date').value=maturity_date
			 }

			 else if(type=="bl_date" && hid_maturity_from==4 )
			 {

				 var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_company_controller');
				 document.getElementById('maturity_date').value=maturity_date
			 }
		 }*/

	 	if(type=="bank_acc_date" && hid_maturity_from==1)
		 {
			 var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_company_controller');
             document.getElementById('maturity_date').value=maturity_date

		 }
		 else if(type=="shipment_date" && (hid_maturity_from==2 ||  hid_maturity_from==5))
		 {
			 //alert(hid_maturity_from)
			 var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_company_controller');
             document.getElementById('maturity_date').value=maturity_date
		 }
		 else if(type=="nagotiate_date" && hid_maturity_from==3 )
		 {
			 //alert(hid_maturity_from)
			 var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_company_controller');
             document.getElementById('maturity_date').value=maturity_date
		 }

		 else if(type=="bl_date" && hid_maturity_from==4 )
		 {

			 var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_company_controller');
             document.getElementById('maturity_date').value=maturity_date
		 }

	}


	function show_me_cumu_stat(pi_id)
	{
			var page_link='requires/import_document_acceptance_company_controller.php?action=cumulative_details_popup&pi_id='+pi_id
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Cumulative Details", 'width=580px,height=450px,center=1,resize=0,scrolling=0','../')
	}

	//fnc_ship_grnt()
	function fnc_ship_grnt_______off()
	{
		//alert("su..re");
		if (form_validation('invoice_id','Back To Back')==false )
		{
			alert("Please fill up invoice number");
			$('#txt_invoice_number').focus();
			return;
		}
		print_report(1+'**'+$('#invoice_id').val()+'**'+$('#txt_lc_number').val()+'**'+$('#cbo_importer_id').val(),'import_document_acceptance_letter','requires/import_document_acceptance_company_controller');
	}

	//fnc_consignment()
	function fnc_consignment_________off()
	{
		//alert("Waitting for decision of dada"); return;
		if (form_validation('invoice_id','Back To Back')==false )
		{
			alert("Please fill up invoice number");
			$('#txt_invoice_number').focus();
			return;
		}
		print_report(2+'**'+$('#invoice_id').val()+'**'+$('#txt_lc_number').val()+'**'+$('#cbo_importer_id').val(),'import_document_acceptance_letter','requires/import_document_acceptance_company_controller');
	}

	function fnc_letter_print(type)
	{
		var letter_type=$('#cbo_letter_type').val();
		/*if(letter_type==1){//Shipping Guarantee;
			if (form_validation('invoice_id','Back To Back')==false )
			{
				alert("Please fill up invoice number");
				$('#txt_invoice_number').focus();
				return;
			}
			print_report(1+'**'+$('#invoice_id').val()+'**'+$('#txt_lc_number').val()+'**'+$('#cbo_importer_id').val(),'import_document_acceptance_letter','requires/import_document_acceptance_company_controller');
		}
		else if(letter_type==2){//Delivery of Consignment;
			if (form_validation('invoice_id','Back To Back')==false )
			{
				alert("Please fill up invoice number");
				$('#txt_invoice_number').focus();
				return;
			}
			print_report(2+'**'+$('#invoice_id').val()+'**'+$('#txt_lc_number').val()+'**'+$('#cbo_importer_id').val(),'import_document_acceptance_letter','requires/import_document_acceptance_company_controller');
				
		}
		else if(letter_type==7){//Forwording Letter
			
			
		}
		else if(letter_type==8){//Acceptance Letter
			
			
		}*/
		
		if (form_validation('invoice_id','Back To Back')==false )
		{
			alert("Please fill up invoice number");
			$('#txt_invoice_number').focus();
			return;
		}
		if(type==1){
		print_report(letter_type+'**'+$('#invoice_id').val()+'**'+$('#txt_lc_number').val()+'**'+$('#cbo_importer_id').val(),'import_document_acceptance_letter','requires/import_document_acceptance_company_controller');
		}
		if(type==2){
			print_report($('#invoice_id').val(),'import_document_acceptance_forwarding2','requires/import_document_acceptance_company_controller');
		
		}
		if(type==3){
			print_report($('#invoice_id').val(),'document_advance_payment_letter','requires/import_document_acceptance_company_controller');
		
		}
		if(type==4){
			print_report($('#invoice_id').val(),'btb_acceptance_letter','requires/import_document_acceptance_company_controller');
		
		}
		if(type==5){
			print_report($('#invoice_id').val(),'forwarding_letter3','requires/import_document_acceptance_company_controller');
		
		}
		if(type==6){
			print_report($('#invoice_id').val(),'undertaking_ific','requires/import_document_acceptance_company_controller');
		
		}
		if(type==7){
			print_report($('#invoice_id').val(),'payment_letter','requires/import_document_acceptance_company_controller');
		
		}
		if(type==8){
			print_report($('#invoice_id').val(),'forwarding_letter4','requires/import_document_acceptance_company_controller');
		}
		if(type==9){
			print_report($('#invoice_id').val(),'forwarding_letter5','requires/import_document_acceptance_company_controller');
		}
	
	}
	
	function maturity_date_check(edf_id)
	{
		if(edf_id==30 || edf_id==142)
		{
			//$('#maturity_date').attr("disabled",true);
			//$('#maturity_date').val("");
			$('#txt_edf_tenor').attr("disabled",false);
			$('#txt_edf_tenor').val(180);
		}
		else
		{
			//$('#maturity_date').attr("disabled",false);
			$('#txt_edf_tenor').attr("disabled",true);
			$('#txt_edf_tenor').val("");
		}
	}

	function calculate_maturity_date()
	{
		var retire_source=$('#cbo_retire_source').val();
		var cbo_payterm_id=document.getElementById('cbo_payterm_id').value;
		var hid_maturity_from=document.getElementById('hid_maturity_from').value;
		var tanor_val=document.getElementById('txt_edf_tenor').value;
		if((retire_source==30 || retire_source==142) && (cbo_payterm_id==1 || cbo_payterm_id==2))
		{
			/*var accep_date=$('#txt_bank_acceptance_date').val();
			var tanor_val=$('#txt_edf_tenor').val();
			if(accep_date!="" && tanor_val!="")
			{
				var maturity_date = add_days( accep_date, tanor_val );
				$('#maturity_date').val(maturity_date);
			}
			else
			{
				if(accep_date=="")
				{
					alert("Please Fill Up Bank Acc. Date");
					$('#txt_bank_acceptance_date').focus();
				}
				else if(tanor_val=="")
				{
					alert("Please Fill Up Edf Tenor");
					$('#txt_edf_tenor').focus();
				}
			}

			var pay_term=$('#cbo_payterm_id').val();



			 if(payterm_id==2)
			{
			if(type=="bank_acc_date" && hid_maturity_from==1)
			{
			var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_company_controller');
			document.getElementById('maturity_date').value=maturity_date

			}
			else if(type=="shipment_date" && hid_maturity_from==2 )
			{
			//alert(hid_maturity_from)
			var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_company_controller');
			document.getElementById('maturity_date').value=maturity_date
			}

			else if(type=="bl_date" && hid_maturity_from==4 )
			{

			var maturity_date=return_global_ajax_value(date+'_'+hid_tenor, 'set_maturity_date', '', 'requires/import_document_acceptance_company_controller');
			document.getElementById('maturity_date').value=maturity_date
			}
			}*/

			if(hid_maturity_from==1)
			{
				var date_calculate_from=document.getElementById('txt_bank_acceptance_date').value;
				//alert(date_calculate_from);
				
				if(date_calculate_from!="")
				{
					var maturity_date = add_days( date_calculate_from, tanor_val );
					document.getElementById('maturity_date').value=maturity_date;
				}
				
				
			}
			else if(hid_maturity_from==2 || hid_maturity_from==5)
			{
				var date_calculate_from=document.getElementById('txt_shipment_date').value;
				if(date_calculate_from!="")
				{
					var maturity_date = add_days( date_calculate_from, tanor_val );
					document.getElementById('maturity_date').value=maturity_date;
				}
			}
			else if(hid_maturity_from==3 )
			{
				var date_calculate_from=document.getElementById('nagotiate_date').value;
				if(date_calculate_from!="")
				{
					var maturity_date = add_days( date_calculate_from, tanor_val );
					document.getElementById('maturity_date').value=maturity_date;
				}
				
				
			}

			else if(hid_maturity_from==4 )
			{
				var date_calculate_from=document.getElementById('bill_date').value;
				if(date_calculate_from!="")
				{
					var maturity_date = add_days( date_calculate_from, tanor_val );
					document.getElementById('maturity_date').value=maturity_date;
				}
			}
		}
	}

	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_lc_currency_id').val();
		var booking_date = $('#txt_company_acc_date').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/import_document_acceptance_company_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}

	function loanRef_validation(id)
	{
		if(id==30 || id==31 || id==32 || id==33 || id==34 || id==35 || id==36 || id==142)
		{
			$("#loan_Ref_td").css('color','blue');
		}
		else
		{
			$("#loan_Ref_td").css('color','black');
		}
	}

	function open_mrr_details(receive_basis,mrr_ids,pi_id,inv_dtls_id,is_service_category,item_category_id,wo_pi_id)
	{
		
		var title= "MRR Details";
		var invoice_id=trim($("#invoice_id").val());
		var btb_id=trim($("#btb_lc_id").val());
		var page_link="requires/import_document_acceptance_company_controller.php?action=open_mrr_details&receive_basis="+receive_basis+'&mrr_ids='+mrr_ids+'&invoice_id='+invoice_id+'&btb_id='+btb_id+'&pi_id='+pi_id+'&inv_dtls_id='+inv_dtls_id+'&is_service_category='+is_service_category+'&item_category_id='+item_category_id+'&wo_pi_id='+wo_pi_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=710px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			/*var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("hidden_btb_id");
			var impoter_id=this.contentDoc.getElementById("hidden_impoter_id").value;
			var ref_closing_status=this.contentDoc.getElementById("hidden_ref_closing_staus").value;
			if (theemail.value!="")
			{
				freeze_window(5);
			    reset_form('importdocumentacceptance_1','','','','');
				var lc_popup=1;
				get_php_form_data(theemail.value+'_'+lc_popup, "populate_data_from_btb_lc", "requires/import_document_acceptance_company_controller" );
				show_list_view(theemail.value+'_'+lc_popup+'___'+impoter_id,'pi_listview','pi_list','requires/import_document_acceptance_company_controller','');
				$("#check_account_posted_td").html('');
				validate_after_validate(0);
				//set_button_status(1, permission, 'fnc_order_entry',1);
				release_freezing();
			}*/
		}
	}
	
	function fn_pkz_qnty()
	{
		var all_pi_ids="";
		$("#tbl_list_search").find('tbody tr').each(function() {
			var pi_id = $(this).find('input[name="pi_id[]"]').val(); 				
			if(all_pi_ids!="") all_pi_ids+=",";
			all_pi_ids +=pi_id;
		});
		//alert(all_pi_ids);
		if(all_pi_ids!="")
		{
			var pkg_quantity_breakdown=trim($("#pkg_quantity_breakdown").val());
			var title="Package Quantity Breakdown";
			var page_link="requires/import_document_acceptance_company_controller.php?action=open_pi_item_details&all_pi_ids="+all_pi_ids+'&pkg_quantity_breakdown='+pkg_quantity_breakdown;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=710px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var total_pkz_qnty=this.contentDoc.getElementById("total_pkz_qnty").value;
				var dtls_data_string=this.contentDoc.getElementById("dtls_data_string").value;
				$("#pkg_quantity").val(total_pkz_qnty);
				$("#pkg_quantity_breakdown").val(dtls_data_string);
			}
		}
		
	}
	
</script>



<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <div>

            <form name="importdocumentacceptance_1" id="importdocumentacceptance_1" autocomplete="off" data-entry_form="">
                <fieldset style="width:1024px;">
                    <legend>BTB / Import LC Invoice Details</legend>
                    <table width="100%" border="0" cellpadding="0" cellspacing="1" id="tbl_importdocumentacceptance">
                        <tr height="10"></tr>
                        <tr>
                            <td width="150" class="must_entry_caption">LC Number</td>
                            <td width="150">
                            <input type="text" name="txt_lc_number" id="txt_lc_number" class="text_boxes" placeholder="Double Click for LC No" onDblClick="open_import_lc_popup( 'requires/import_document_acceptance_company_controller.php?action=open_import_lc_popup','BTB / Import LC List' );" style="width:140px" readonly/>
                             <input type="hidden" name="btb_lc_id" id="btb_lc_id" value="" />

                            </td>
                            <td width="150" class="must_entry_caption">Invoice Number</td>
                            <td width="150"><input type="text" name="txt_invoice_number" id="txt_invoice_number" class="text_boxes" placeholder="Double Click for Invoice No" onDblClick="open_invoice_popup( 'requires/import_document_acceptance_company_controller.php?action=open_invoice_popup','Import Invoice List' );" onChange="check_duplicate_invoice(this.value)" style="width:139px" />
                            <input type="hidden" name="invoice_id" id="invoice_id" value=""/> </td>
                            <td width="150">Issuing Bank</td>
                            <td>
                                <?php 
									if ($db_type==0)
									{
										echo create_drop_down( "cbo_issuing_bank", 165,"select id,concat(a.bank_name,' (', a.branch_name,')') as bank_name from lib_bank where is_deleted=0 and status_active=1 and issusing_bank = 1 order by bank_name",'id,bank_name', 1, 'Display',0,0,1);
									}
									else
									{ 
										echo create_drop_down( "cbo_issuing_bank", 165,"select id,(bank_name || ' (' || branch_name || ')' ) as bank_name from lib_bank where is_deleted=0 and status_active=1 and issusing_bank = 1 order by bank_name",'id,bank_name', 1, 'Display',0,0,1);
									}
								?>
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Invoice Date</td>
                            <td>
                                 <input type="text" name="txt_invoice_date" id="txt_invoice_date" class="datepicker" value="<? echo date('d-m-Y') ?>" style="width:140px" />
                            </td>
                            <td>Company Acc. Date</td>
                            <td>
                                 <input type="text" name="txt_company_acc_date" id="txt_company_acc_date" class="datepicker" style="width:139px"  onChange="check_exchange_rate();" />
                            </td>
                        	
                            <td>LC Value & Currency</td>
                            <td>
                            <input type="text" name="txt_lc_value" id="txt_lc_value" class="text_boxes_numeric" placeholder="Display"  style="width:80px" disabled />
                            <?php echo create_drop_down( "cbo_lc_currency_id",70,$currency,'',1,'Display','',0,1); ?>
                            <input type="hidden" name="hid_tolarance" id="hid_tolarance" value="" readonly />
                            <input type="hidden" name="hid_tenor" id="hid_tenor" value="" readonly />
                            <input type="hidden" name="hid_maturity_from" id="hid_maturity_from" value="" readonly/>
                            <input type="hidden" name="is_posted_account" id="is_posted_account" value="" readonly/>
                            </td>
                        </tr>
						<tr>
                        	<td>Doc Received date</td>
							<td><input type="text" name="txt_doc_rcv_date" id="txt_doc_rcv_date" style="width:140px" class="datepicker" value=""  /></td>
                            <td>Local Doc. Send Date</td>
							<td><input type="text" name="txt_local_date" id="txt_local_date" style="width:140px" class="datepicker" value=""  /></td>
                            <td>L/C Type</td>
                            <td>
                                 <?php echo create_drop_down( "cbo_lc_type_id",165,$lc_type,'',1,'Display',"","",1); ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td width="150">Importer</td>
                            <td width="120">
                                 <?php echo create_drop_down( "cbo_importer_id", 152,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name",'id,company_name', 1, 'Display',0,"",1); ?>
                            </td>
                            <td>Supplier</td>
                            <td id="supplier_td"><?php echo create_drop_down( "cbo_supplier_id", 152,$blank_array,'', 1, 'Display',0,0,1); ?></td>
                            <td>Pay Term</td>
							<td><?php echo create_drop_down( "cbo_payterm_id",165,$pay_term,'',1,'Display',0,"",1); ?> </td>
                        </tr>
                        <tr>
                            <td>Ready To Approved</td>
                    		<td><? echo create_drop_down( "cbo_ready_to_approved", 152, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?>
                            <input type="hidden" name="hide_approved_status" id="hide_approved_status" value="" readonly />
                            </td>
                            <td>Document Value</td>
                            <td>
                            <input type="text" name="txt_document_value" id="txt_document_value" class="text_boxes_numeric" style="width:139px" /></td>
                            <td>Acceptance Time</td>
                          	<td><?php echo create_drop_down( "cbo_acceptance_time",165,$acceptance_time,'',0,'',"",0,0); ?> </td>  
                        </tr>
                        <tr> 
                       		<td>Courier No.</td>
                    		<td>   <input type="text" name="txt_courier_no" id="txt_courier_no" class="text_boxes" value="" style="width:140px"/></td>
                            <td>Courier Date</td>
                            <td> <input type="text" name="txt_courier_date" id="txt_courier_date" class="datepicker" value="" style="width:140px" /></td>
                        	<td>Remarks</td>
							<td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" value="" style="width:155px"  maxlength="200"/></td>
                        </tr>
                        <tr>
                        	<td>Retire Source</td>
                            <td>
                            	<?php echo create_drop_down( "cbo_retire_source",150,$commercial_head,'',1,'--Select--',"","maturity_date_check(this.value);loanRef_validation(this.value);",0,'5,6,10,11,15,16,30,31,32,33,34,35,71,142,188'); ?>
                            </td>
                            <td id="loan_Ref_td">Loan Ref.</td>
                            <td ><input type="text" name="txt_loan_reff" id="txt_loan_reff" style="width:150px" class="text_boxes" /></td>
                            <td class="must_entry_caption">Exchange Rate</td>
							<td><input type="text" name="txt_exchange_rate" id="txt_exchange_rate" style="width:140px" class="text_boxes_numeric" onBlur="calculate_maturity_date(this.value);" value="" /></td>
                        </tr>
                        <tr>
                            <td>EDF/UPASS Tenor</td>
							<td><input type="text" name="txt_edf_tenor" id="txt_edf_tenor" style="width:140px" class="text_boxes_numeric" value="" onBlur=" calculate_maturity_date();" disabled /></td>
							<td class="must_entry_caption">Source</td>
							<td><? echo create_drop_down( "cbo_source_id", 151, $source,'', 1, 'Display',0,0,1); ?></td>
                        </tr>
                        
                        
                        <tr style="display:none;">
                            <td>Bank Ref</td>
                            <td><input type="text" name="txt_bank_ref" id="txt_bank_ref" class="text_boxes" style="width:139px"/></td>
                            <td>Shipment Date</td>
                            <td>
                                 <input type="text" name="txt_shipment_date" id="txt_shipment_date" class="datepicker" style="width:140px" onChange="set_maturity_date(this.value,'shipment_date'); calculate_maturity_date();" />
                            </td>
                            <td>Bank Acc. Date</td>
                            <td>
                                 <input type="text" name="txt_bank_acceptance_date" id="txt_bank_acceptance_date" class="datepicker" style="width:140px"  onChange="set_maturity_date(this.value,'bank_acc_date');  calculate_maturity_date();check_exchange_rate();"/>
                            </td>
                        </tr>
                        <tr style="display:none;">
                            <td>Nagotiate Date</td>
                            <td>
                        	<input type="text" name="nagotiate_date" id="nagotiate_date" class="datepicker" value="" style="width:140px" onChange="set_maturity_date(this.value,'nagotiate_date');  calculate_maturity_date();" />
                            </td>
                            
                        </tr>
                        	
                        <tr>
                            <td colspan="6" id="check_account_posted_td" style=" color:red; font-size:20px;">&nbsp;</td>
                        </tr>
                        <div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div> 
                    </table>
                </fieldset>

                  <div id="pi_list" style="margin: 10px 0; font-size: 16px; font-weight: bold; color: #0172BE;"> PI List </div>
                <fieldset style="width:1024px; margin-bottom:10px;">
				<legend>BTB / Import LC Shipment Details</legend>
				<table width="100%" border="0" cellpadding="0" cellspacing="1">
					<tr>
						<td>BL/Cargo No</td>
						<td><input type="text" name="bill_no" id="bill_no" class="text_boxes" style="width:140px" /></td>
						<td>BL/Cargo Date</td>
						<td><input type="text" name="bill_date" id="bill_date" class="datepicker" value="" style="width:140px" onChange="set_maturity_date(this.value,'bl_date');  calculate_maturity_date();" /></td>
                    	<td>Shipment Mode</td>
						<td id="shipment_td">
							 <?php echo create_drop_down( "cbo_shipment_mode",152,$shipment_mode,'',1,'-Select',"","",""); ?>
						</td>
					</tr>
					<tr>
						<td>Document Status</td>
						<td>
							<?php echo create_drop_down( "cbo_document_status",152,$document_status,'',1,'-Select',"","",""); ?>
						</td>
						<td>Custom Forwarder Name</td>
                        <td>
						<?
                            echo create_drop_down( "cbo_forwarder_name", 152, "select s.id, s.supplier_name from lib_supplier s, lib_supplier_tag_company b where s.status_active =1 and s.is_deleted=0 and b.supplier_id=s.id and s.id in (select supplier_id from lib_supplier_party_type where party_type in (30,31,32)) group by s.id, s.supplier_name order by supplier_name","id,supplier_name", 1, "--Select Frowarder--", $selected, "" );
                        ?>
                        </td>
                    	<td>Original Doc Receive Date</td>
						<td><input type="text" name="original_doc_receive_date" style="width:140px" id="original_doc_receive_date" class="datepicker" value=""  /></td>
					</tr>
					<tr>
						<td>Document to C&amp;F</td>
						<td><input type="text" name="doc_to_cnf" id="doc_to_cnf" class="datepicker" style="width:140px" value="" /></td>
						<td>Copy Doc Receive Date</td>
						<td><input type="text" name="copy_doc_receive_date" style="width:140px" id="copy_doc_receive_date" class="datepicker" value=""  /></td>
                    	<td>Mother Vessel</td>
						<td><input type="text" name="mother_vessel" id="mother_vessel" class="text_boxes" style="width:140px" value="" /></td>
					</tr>
					<tr>
						<td>ETA Date</td>
						<td><input type="text" name="eta_date" id="eta_date" class="datepicker" style="width:140px" value="" /></td>
						<td>Feeder Vessel </td>
						<td><input type="text" name="feeder_vessel" id="feeder_vessel" class="text_boxes" style="width:140px" value="" /></td>
                    	<td>Shipping Bill No</td>
						<td><input type="text" name="shipping_bill_no" id="shipping_bill_no" style="width:140px" class="text_boxes" value="" /></td>
					</tr>
					<tr>
						<td>Incoterm</td>
						<td>
							<?php echo create_drop_down( "cbo_inco_term",152,$incoterm,'',1,'-Select',"","",""); ?>
						</td>
						<td>IC Received Date</td>
						<td><input type="text" name="ic_receive_date" id="ic_receive_date" style="width:140px" class="datepicker" value=""  /></td>
                    	<td>Port of Loading</td>
						<td><input type="text" name="port_of_loading" id="port_of_loading" style="width:140px" class="text_boxes" value="" /></td>
					</tr>
					<tr>
						<td>Port of Discharge</td>
						<td><input type="text" name="port_of_discharge" id="port_of_discharge" style="width:140px" class="text_boxes" value="" /></td>
						<td>Incoterm Place</td>
						<td><input type="text" name="inco_term_place" id="inco_term_place" style="width:140px" class="text_boxes" value="" /></td>
                    	<td>Bill Of Entry No</td>
						<td><input type="text" name="bill_of_entry_no" id="bill_of_entry_no" style="width:140px" class="text_boxes" value="" /></td>
					</tr>
					<tr>
                    	<td>Bill Of Entry Date</td>
						<td><input type="text" name="boe_date" id="boe_date" style="width:140px" class="datepicker" value=""  /></td>
						<td>Internal File No</td>
						<td><input type="text" name="internal_file_no" id="internal_file_no" style="width:140px" class="text_boxes" placeholder="Display" value="" disabled/></td>
                        <td>Maturity Date</td>
						<td><input type="text" name="maturity_date" id="maturity_date" style="width:140px" class="datepicker" value=""  /></td>
					</tr>
                    <tr>
                    	<td>Container No</td>
						<td><input type="text" name="container_no" id="container_no" style="width:140px" class="text_boxes" value="" /></td>
						<td>PSI Reference No</td>
						<td><input type="text" name="psi_reference_no" id="psi_reference_no" style="width:140px" class="text_boxes" value="" /></td>
                        
                        <td>EDF Paid Date</td>
						<td><input type="text" name="edf_paid_date" id="edf_paid_date" style="width:140px" class="datepicker" value="" /></td>
					</tr>
					<tr>
						<td>ETD Actual</td>
						<td><input type="text" name="etd_actual" id="etd_actual" class="datepicker" style="width:140px" value="" /></td>
						<td>Package Quantity</td>
						<td>
							<input type="text" name="pkg_quantity" id="pkg_quantity" style="width:80px" class="text_boxes_numeric" value="" onDblClick="fn_pkz_qnty();" placeholder="Browse/Write" />
                            <input type="hidden" name="pkg_quantity_breakdown" id="pkg_quantity_breakdown" />
							<? echo create_drop_down( "pakg_uom",60,$unit_of_measurement,'',1,'--UOM--',"","",""); ?>
							<!-- <input type="text" name="pakg_uom" id="pakg_uom" class="text_boxes" style="width:60px" value="" /> -->
						</td>
                        <td>ETA Actual</td>
						<td><input type="text" name="eta_actual" id="eta_actual" class="datepicker" style="width:140px" value="" /></td>
					</tr>
					<tr>
                    	<td>Container Status</td>
						<td>
							<? echo create_drop_down("cbo_container_status",152,$container_status,'',1,'-Select',"","","");?>
						</td>
						<td>ETA Advice </td>
						<td><input type="text" name="eta_advice" id="eta_advice" class="datepicker" style="width:140px" value=""  /></td> 
                        <td>Release Date</td>
						<td><input type="text" name="release_date" id="release_date" style="width:140px" class="datepicker" value="" /></td>
					</tr>
                    <tr>
                    	<td>Bill of Entry Value</td>
						<td><input type="text" name="txt_bill_entry_value" id="txt_bill_entry_value" class="text_boxes_numeric" style="width:140px" /></td>
						<td>Container Size</td>
						<td><? echo create_drop_down("cbo_container_size",152,$container_size,'',1,'-Select',"","","");?></td>
                        <td>Bill Submission date</td>
						<td><input type="text" name="txt_submit_date" id="txt_submit_date" style="width:140px" class="datepicker" value="" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="left">
							<input type="button" id="image_button" class="image_uploader" style="width:152px;" value="CLICK TO ADD FILE" onClick="file_uploader('../../', document.getElementById('invoice_id').value, '', 'importdocumentacceptance_1', 2, 1)" />
						</td>
					</tr>
                    <tr>
						<td colspan="6" height="15"></td>
					</tr>
					<tr>
						<td colspan="6" height="50" valign="middle" align="center" class="button_container">
							<?
								echo load_submit_buttons( $permission, "fnc_import_document_acceptance", 0,0 ,"reset_form('importdocumentacceptance_1','pi_list*check_account_posted_td','','','')",1) ;
							?>
							
							<?
							//
							echo create_drop_down( "cbo_letter_type", 150, $letter_type_arr,"", 1, "-- select --", 0, "","","1,2,7,8" );
							?> 
							
							
							<!--   <input type="button" value="Ship Grnt." id="btn_ship_grnt" name="btn_ship_grnt" class="formbutton" style="width:100px;" onClick="fnc_ship_grnt()" /> &nbsp;
							<input type="button" value="Consignment" id="btn_consignment" name="btn_consignment" class="formbutton" style="width:100px;" onClick="fnc_consignment()" />-->
							
							<input type="button" value="Letter Print" id="btn_consignment" name="btn_consignment" class="formbutton" style="width:100px;" onClick="fnc_letter_print(1)" />
                            <input type="button" value="Forwarding letter2" id="btn_leter" name="btn_leter" class="formbutton" style="width:120px;" onClick="fnc_letter_print(2)" />
                            <input type="button" value="Advance Payment letter" id="btn_leter" name="btn_leter" class="formbutton" style="width:150px;" onClick="fnc_letter_print(3)" />
                            <input type="button" value="BTB Acceptance Letter" id="btn_leter" name="btn_leter" class="formbutton" style="width:150px;" onClick="fnc_letter_print(4)" />
							<input type="button" value="Forwarding letter3" id="btn_leter" name="btn_leter" class="formbutton" style="width:150px;" onClick="fnc_letter_print(5)" />
							<input type="button" value="UNDERTAKING(IFIC)" id="btn_leter" name="btn_leter" class="formbutton" style="width:150px;" onClick="fnc_letter_print(6)" />
							<input type="button" value="payment letter" id="btn_leter" name="btn_leter" class="formbutton" style="width:150px;" onClick="fnc_letter_print(7)" />
							<input type="button" value="Forwarding letter4" id="btn_letter" name="btn_letter" class="formbutton" style="width:150px;" onClick="fnc_letter_print(8)" />
							<input type="button" value="Forwarding letter5" id="btn_letter" name="btn_letter" class="formbutton" style="width:150px;" onClick="fnc_letter_print(9)" />
							
							
							
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

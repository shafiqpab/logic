<?
/*-------------------------------------------- Comments ----------------------------------------
Version (Oracle)         :
Purpose			         :  This form will create Multi Job wise Additional Fabric Booking
Functionality	         :
JS Functions	         :
Created by		         :  Kausar & zakaria Joy
Creation date 	         :  15-01-2023
Requirment Client        :  
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         :
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_id=$_SESSION['logic_erp']['user_id'];
echo load_html_head_contents("Multi Job wise Additional Fabric Booking", "../../", 1, 1,$unicode,'','');

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';
	
	function fnc_process_data()
	{
		if (form_validation('cbo_company_name*txt_booking_no','Company*Booking No')==false){
			return;
		}
		else{
			var txt_booking_no=document.getElementById('txt_booking_no').value;
			var txt_booking_date=document.getElementById('txt_booking_date').value;
			var garments_nature=document.getElementById('garments_nature').value;
			var cbo_company_name=document.getElementById('cbo_company_name').value;
			var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
			var cbo_currency=document.getElementById('cbo_currency').value;
			var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
			var cbo_level=document.getElementById('cbo_level').value;
			var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;	
            var cbo_source=document.getElementById('cbo_source').value;
			var cbo_item_from=document.getElementById('cbo_item_from').value;
			
			var page_link='requires/multi_job_additional_fabric_booking_controller.php?action=fnc_process_data';
			var title='PO Search For Fabric Booking';
			page_link=page_link+'&cbo_item_from='+cbo_item_from+'&company_id='+cbo_company_name+'&garments_nature='+garments_nature+'&cbo_currency='+cbo_currency+'&cbo_supplier_name='+cbo_supplier_name+'&cbo_buyer_name='+cbo_buyer_name+'&txt_booking_no='+txt_booking_no+'&cbo_level='+cbo_level+'&txt_booking_date='+txt_booking_date+'&cbo_source='+cbo_source+'&cbo_pay_mode='+cbo_pay_mode;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1240px,height=450px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function(){
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("txt_selected_id");
				var theemail2=this.contentDoc.getElementById("txt_job_id");
				var theemail3=this.contentDoc.getElementById("txt_selected_po");
				var theemail4=this.contentDoc.getElementById("txt_pre_cost_dtls_id");
				if (theemail.value!=""){
					document.getElementById('txt_select_item').value=theemail.value;
					document.getElementById('txt_selected_po').value=theemail3.value;
					document.getElementById('txt_selected_fabric_id').value=theemail4.value;
					//get_php_form_data(theemail3.value+"_save_"+cbo_company_name+"_"+cbo_pay_mode+"_"+cbo_level, "set_delivery_date_from_tna", "requires/multi_job_additional_fabric_booking_controller" );
					//var tna_date=$('#txt_tna_date').val();
					fnc_generate_booking(3);
				}
			}
		}
	}
	
	function compare_date(str)
	{
		var row_num=$('#tbl_list_search tr').length;
		//alert(str);
		for (var i=1; i<=row_num; i++){
			var txt_delevary_date_data=document.getElementById('txtddate_'+i).value;
			var txt_tna_date_data=document.getElementById('txttnadate_'+i).value;
			var booking_date=document.getElementById('txt_booking_date').value;
		if(txt_delevary_date_data=='')
		{
			txt_delevary_date_data=document.getElementById('txttnadate_'+i).value;
		}
		txt_delevary_date_data= txt_delevary_date_data.split('-');
		var txt_delevary_date_inv=txt_delevary_date_data[2]+"-"+txt_delevary_date_data[1]+"-"+txt_delevary_date_data[0];
		txt_tna_date_data = txt_tna_date_data.split('-');
		var txt_tna_date_inv=txt_tna_date_data[2]+"-"+txt_tna_date_data[1]+"-"+txt_tna_date_data[0];
		booking_date = booking_date.split('-');
		var booking_date_inv=booking_date[2]+"-"+booking_date[1]+"-"+booking_date[0];
		
		var txt_delevary_date = new Date(txt_delevary_date_inv);
		var txt_tna_date = new Date(txt_tna_date_inv);
		var txt_booking_date = new Date(booking_date_inv);
		var lib_tna_intregrate=$('#lib_tna_intregrate').val();
		//alert(lib_tna_intregrate);
		var cbo_isshort=2;
		if(cbo_isshort==1)
		{
			if(txt_delevary_date < txt_booking_date)
			{
				txt_delevary_date_data=document.getElementById('txttnadate_'+i).value;
			}
		}
		else
		{
			if(str==1)
			{
				if(txt_tna_date_data !='')
				{
					if( lib_tna_intregrate==1)
					{
						if(txt_delevary_date > txt_tna_date)
						{
							alert('Delivery Date Not Allowed Greater Than TNA Date');
							if(txt_tna_date>txt_booking_date)
							{
								document.getElementById('txtddate_'+i).value=document.getElementById('txttnadate_'+i).value;
							}
							else
							{
								document.getElementById('txtddate_'+i).value='';
							}
							//return;
						}
						else if((txt_delevary_date < txt_booking_date) ||  (txt_booking_date > txt_tna_date))
						{
							alert('Delivery Date Not Allowed Less than Booking Date');
							//document.getElementById('txt_delevary_date').value=document.getElementById('txt_booking_date').value;
							document.getElementById('txtddate_'+i).value='';
						}
					}
					else
					{
						if((txt_delevary_date < txt_booking_date))
						{
							//alert('Delivery Date Not Allowed Less than Booking Date');
							//document.getElementById('txt_delevary_date').value=document.getElementById('txt_booking_date').value;
							//document.getElementById('txtddate_'+i).value='';
						}
					}
				}
				else
				{
					if(txt_delevary_date < txt_booking_date )
					{
						//alert('Delivery Date Not Allowed Less than Booking Date');
						
						//document.getElementById('txt_delevary_date').value=document.getElementById('txt_booking_date').value;
						document.getElementById('txtddate_'+i).value=document.getElementById('txt_booking_date').value;
					}
				}
			}
			if(str==2)
			{
				if(lib_tna_intregrate==1)
				{
					//alert(txt_tna_date);
					if(txt_tna_date !='')
					{
						if(txt_tna_date < txt_booking_date)
						{
							alert('TNA Date is Less than Booking Date');
							//document.getElementById('txt_delevary_date').value='';
							document.getElementById('txtddate_'+i).value='';
							//document.getElementById('txt_tna_date').value='';
							return;
						}
						else
						{
							//document.getElementById('txt_delevary_date').value=document.getElementById('txt_tna_date').value;
							document.getElementById('txtddate_'+i).value=document.getElementById('txttnadate_'+i).value;
							return;
						}
					}
				}
			}
		  }
		} //Loop End
	}

	function fnc_generate_booking(operation){
		freeze_window(operation);
		if (form_validation('txt_booking_no*txt_select_item*cbo_fabric_natu*cbo_fabric_source*cbo_item_from','Booking No*Order No*Fabric Nature*Fabric Source*Item From')==false){
			return;
		}
		else{
			var data="action=generate_fabric_booking"+get_submitted_data_string('txt_booking_no*txt_selected_po*cbo_fabric_natu*cbo_fabric_source*cbo_company_name*cbo_buyer_name*cbouom*txt_selected_fabric_id*cbo_level*cbo_item_from',"../../");
			
			http.open("POST","requires/multi_job_additional_fabric_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_generate_booking_reponse;
		}
	}
	
	function fnc_generate_booking_reponse(){
		if(http.readyState == 4){
			document.getElementById('booking_list_view').innerHTML=http.responseText;
			$("#cbo_currency").attr("disabled",true);
			compare_date(1);
			set_all_onclick();
			release_freezing();
		}
	}
	
	function openmypage_booking(page_link,title){
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var page_link=page_link+'&company_id='+cbo_company_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1030px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_booking");
			if (theemail.value!=""){
				reset_form('additionalfabricbooking_1','booking_list_view','id_approved_id','txt_booking_date,<? echo date("d-m-Y"); ?>','','cbo_currency*copy_val');
				document.getElementById('copy_val').checked=true;
				document.getElementById('booking_list_view').innerHTML='<font id="save_sms" style="color:#F00">Select new Item</font>';
				$("#cbo_currency").attr("disabled",true);
				get_php_form_data( theemail.value, "populate_data_from_search_popup_booking", "requires/multi_job_additional_fabric_booking_controller" );
				set_button_status(1, permission, 'fnc_additional_fabric_booking',1);
				fnc_show_booking_list();
			}
		}
	}
	
	function fnc_additional_fabric_booking( operation ){
		freeze_window(operation);
		var data_all="";
		if (form_validation('cbo_company_name*cbo_buyer_name*cbo_fabric_natu*cbouom*cbo_fabric_source*txt_booking_date*cbo_pay_mode*cbo_supplier_name*txt_delivery_date*cbo_source*cbo_item_from*cbo_level','Company Name*Buyer Name*Fabric Nature*UoM*Fabric Source*Booking Date*Pay Mode*Supplier*Delivery Date*Source*Item From*Level')==false){
			release_freezing();
			return;
		}
		var delete_cause=''; var delete_type=0;
		if(operation==2){
			var al_magg="Press OK to delete master and details part.\n Press CANCEL to delete only details part.";
			var r=confirm(al_magg);
	
			if(r==true)
			{
				delete_cause = prompt("Please enter your delete cause", "");
				if(delete_cause==""){
					alert("You have to enter a delete cause");
					release_freezing();
					return;
				}
				if(delete_cause==null){
					release_freezing();
					return;
				}
				delete_type=1;
			}
			else
			{
				delete_type=0;
			}
			var q=confirm("Press OK to Delete Or Press Cancel");
			if(q==false){
				release_freezing();
				return;
			}
		}

		data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_company_name*cbo_buyer_name*cbo_fabric_natu*cbouom*cbo_fabric_source*txt_booking_date*cbo_pay_mode*cbo_supplier_name*cbo_currency*txt_exchange_rate*txt_delivery_date*cbo_source*txt_attention*txtdelivery_address*txt_tenor*cbo_item_from*cbo_level*cbo_ready_to_approved*txt_remarks*update_id',"../../")+"&delete_type="+delete_type;
	
		var data="action=save_update_delete&operation="+operation+data_all+'&delete_cause='+delete_cause;
		http.open("POST","requires/multi_job_additional_fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_additional_fabric_booking_reponse;
	}
	
	function fnc_additional_fabric_booking_reponse(){
		if(http.readyState == 4){
			var reponse=trim(http.responseText).split('**');
			//alert(reponse)
			if(trim(reponse[0])=='10'){
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='app1'){
				alert("This booking is approved")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='recv1'){
				alert("Receive Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			
			if(trim(reponse[0])=='pi1'){
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			
			
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(trim(reponse[0]));
			if(trim(reponse[0])==0 || trim(reponse[0])==1){
				document.getElementById('txt_booking_no').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
				$("#cbo_company_name").attr("disabled",true);
				$("#cbo_supplier_name").attr("disabled",true);
				$("#cbo_buyer_name").attr("disabled",true);
				$("#cbo_level").attr("disabled",true);
				$("#cbo_item_from").attr("disabled",true);
				set_button_status(1, permission, 'fnc_additional_fabric_booking',1);
			}
			else if(trim(reponse[0])==2){
				location.reload();
			}
			release_freezing();
		}
	}
	
	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/multi_job_additional_fabric_booking_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);

	}
	
	function fnc_additional_fabric_booking_dtls( operation ){
		freeze_window(operation);
		var delete_cause='';
		if(operation==2){
			delete_cause = prompt("Please enter your delete cause", "");
			if(delete_cause==""){
				alert("You have to enter a delete cause");
				release_freezing();
				return;
			}
			if(delete_cause==null){
				release_freezing();
				return;
			}
			var r=confirm("Press OK to Delete Or Press Cancel");
			if(r==false){
				release_freezing();
				return;
			}
		}
	
		var data_all="";
		if (form_validation('txt_booking_no','Booking No')==false){
			release_freezing();
			return;
		}	
		var row_num=$('#tbl_fabric_booking tr').length;
		if(row_num <1){
			alert("Select Item");
			release_freezing();
			return;
		}
		var reg=/[^a-zA-Z0-9!@#$%^,;.:<>{}?\+|\[\]\- \/]/g;		
		var cbo_item_from=document.getElementById('cbo_item_from').value;
		var z=1;
		for (var i=1; i<=row_num; i++){
			var txtrate=document.getElementById('txtrate_'+i).value
			var txt_booking_date=$('#txt_booking_date').val();
			var cbo_pay_mode=$("#cbo_pay_mode").val();
			var delivery_date=$('#txt_delivery_date').val();
			var lib_tna_intregrate=$('#lib_tna_intregrate').val();
			
			if(lib_tna_intregrate==1  && delivery_date=="")
			{
				alert('Delivery date is empty');
				release_freezing();
				return;
			}
			if(lib_tna_intregrate==2  && delivery_date=="")  //28170 for Islam Group
			{
				alert('Delivery date is empty');
				release_freezing();
				return;
			}
			//alert(lib_tna_intregrate+'='+delivery_date);
			if((lib_tna_intregrate==2 || lib_tna_intregrate==0 ) && delivery_date!="") //28170 for Islam Group
			{
				if(date_compare(txt_booking_date, delivery_date)==false)
				{
					alert('Delivery Date Not Allowed Less than Booking Date');
					document.getElementById('txtddate_'+i).value='';
					release_freezing();
					return;
				}
			}			
			data_all+="&txtbookingid_" + z + "='" + $('#txtbookingid_'+i).val()+"'"+"&txtjob_" + z + "='" + $('#txtjob_'+i).val()+"'"+"&txtpoid_" + z + "='" + $('#txtpoid_'+i).val()+"'"+"&txtprecostfabriccostdtlsid_" + z + "='" + $('#txtprecostfabriccostdtlsid_'+i).val()+"'"+"&txtbodypart_" + z + "='" + $('#txtbodypart_'+i).val()+"'"+"&txtcolortype_" + z + "='" + $('#txtcolortype_'+i).val()+"'"+"&txtwidthtype_" + z + "='" + $('#txtwidthtype_'+i).val()+"'"+"&txtconstruction_" + z + "='" + $('#txtconstruction_'+i).val()+"'"+"&txtcompositi_" + z + "='" + $('#txtcompositi_'+i).val()+"'"+"&txtgsmweight_" + z + "='" + $('#txtgsmweight_'+i).val()+"'"+"&txtgmtcolor_" + z + "='" + $('#txtgmtcolor_'+i).val()+"'"+"&txtitemcolor_" + z + "='" + $('#txtitemcolor_'+i).val()+"'"+"&txtdia_" + z + "='" + $('#txtdia_'+i).val()+"'"+"&txthscode_" + z + "='" + $('#txthscode_'+i).val()+"'"+"&process_" + z + "='" + $('#process_'+i).val()+"'"+"&txtbalqnty_" + z + "='" + $('#txtbalqnty_'+i).val()+"'"+"&txtreqqnty_" + z + "='" + $('#txtreqqnty_'+i).val()+"'"+"&txtfinreqqnty_" + z + "='" + $('#txtfinreqqnty_'+i).val()+"'"+"&cuqnty_" + z + "='" + $('#cuqnty_'+i).val()+"'"+"&preconskg_" + z + "='" + $('#preconskg_'+i).val()+"'"+"&txtwoq_" + z + "='" + $('#txtwoq_'+i).val()+"'"+"&txtwoqprev_" + z + "='" + $('#txtwoqprev_'+i).val()+"'"+"&txtadj_" + z + "='" + $('#txtadj_'+i).val()+"'"+"&txtacwoq_" + z + "='" + $('#txtacwoq_'+i).val()+"'"+"&txtrate_" + z + "='" + $('#txtrate_'+i).val()+"'"+"&txtamount_" + z + "='" + $('#txtamount_'+i).val()+"'"+"&txtremark_" + z + "='" + $('#txtremark_'+i).val()+"'";
			z++;
		}
		//alert(data_all); release_freezing(); return;
		var cbo_level=document.getElementById('cbo_level').value;
		if(cbo_level==1){
			var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*txt_booking_no*strdata*cbo_level*cbo_pay_mode*cbo_item_from*update_id',"../../")+data_all+'&delete_cause='+delete_cause;
		}
		if(cbo_level==2){
			var data="action=save_update_delete_dtls_job_level&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*txt_booking_no*strdata*cbo_level*cbo_pay_mode*cbo_item_from*update_id',"../../")+data_all+'&delete_cause='+delete_cause;
		}
		// alert(data);
		http.open("POST","requires/multi_job_additional_fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_additional_fabric_booking_dtls_reponse;
	}
	
	function fnc_additional_fabric_booking_dtls_reponse(){
		if(http.readyState == 4){
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='app1'){
				alert("This booking is approved")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='lockAnotherProcess'){
				alert("This booking is Attached In Trims Order Receive (Trims ERP). Ref :"+trim(reponse[1])+" \n So Update/Delete Not Allowed.")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='recvRate1'){
				alert("Receive Rate Change Found, Receive No :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			if(reponse[3]>0)
			{
				if(trim(reponse[0])=='recv1'){
					alert("Receive Qty  :"+trim(reponse[3])+" Found in Receive Number "+ trim(reponse[2])+" \n So WOQ Less Then Receive Qty/Update or Delete Not Possible")
					release_freezing();
					return;
				}
				if(trim(reponse[0])=='pi1'){
					alert("PI Qty  :"+number_format(trim(reponse[3]),2,'.','' )+" Found in PI Number "+ trim(reponse[2])+" \n So WOQ Less Then PI Qty/Update or Delete Not Possible")
					release_freezing();
					return;
				}
			}
			else
			{
				if(trim(reponse[0])=='recv1'){
					alert("Receive Number Found "+ trim(reponse[2])+" \n So Update or Delete Not Possible")
					release_freezing();
					return;
				}
				if(trim(reponse[0])=='pi1'){
					alert(" PI Number Found"+ trim(reponse[2])+" \n So Update or Delete Not Possible")
					release_freezing();
					return;
				}
			}			
			if(trim(reponse[0])=='orderFound')
			{
				alert("WO Qty is not less than Trims Order Receive (Trims ERP)\n Receive No:"+trim(reponse[2])+". \n Delete Not Allowed");
				release_freezing();
				return;
			}	
			if(trim(reponse[0])=='vad1'){
				alert("Wo Amount less than Budget amount not allowed.")
				$('#search'+reponse[2]).css('background-color', 'red');
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='vad2'){
					alert("Wo Qty less than Budget Qty not allowed.")
				$('#search'+reponse[2]).css('background-color', 'red');
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='﻿﻿delQtyExeed'){
				alert("Quantity Exeed Delivery Quantity. Delivery ID:"+trim(reponse[1])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
	
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(trim(reponse[0]));
			if(trim(reponse[0])==0 || trim(reponse[0])==1 || trim(reponse[0])==2){
				var str="";
				if(trim(reponse[0])==0){
					str='Saved';
					$("#cbo_supplier_name").attr("disabled",true);
				}
				if(trim(reponse[0])==1){
					str='Updated';
					$("#cbo_supplier_name").attr("disabled",true);
	
				}
				if(trim(reponse[0])==2){
					str='Deleted';
				}
				document.getElementById('txt_select_item').value='';
				document.getElementById('booking_list_view').innerHTML=''
				document.getElementById('booking_list_view').innerHTML='<font id="save_sms" style="color:#F00">Data '+str+', Select new Item</font>';
				fnc_show_booking_list()
			}
			release_freezing();
		}
	}
	function claculate_amount(i){
		var woq=document.getElementById('txtacwoq_'+i).value*1;
		var rate=document.getElementById('txtrate_'+i).value*1;
		var pre_cost_rate=$('#txtrate_'+i).attr('data-pre-cost-rate');
		var current_cost_rate=$('#txtrate_'+i).attr('data-current-rate');
		pre_cost_rate=pre_cost_rate*1;
		current_cost_rate=current_cost_rate*1;
		if(rate>pre_cost_rate){
			alert("Rate greater than precost rate not allowed");
			document.getElementById('txtrate_'+i).value=current_cost_rate;
			return;
		}
		var amount=number_format_common(woq*rate, 5, 0);
		document.getElementById('txtamount_'+i).value=amount;
		//claculate_acwoQty(i)
	}
	function claculate_acwoQty(i){
		var woq=document.getElementById('txtwoq_'+i).value*1;
		var txtadj=document.getElementById('txtadj_'+i).value*1;
		var acwoq=number_format_common(woq-txtadj, 5, 0);
		document.getElementById('txtacwoq_'+i).value=acwoq;
		claculate_amount(i)
	}
	
	function fnc_show_booking_list(){
		var garments_nature=document.getElementById('garments_nature').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_currency=document.getElementById('cbo_currency').value;
		var data=document.getElementById('txt_selected_po').value;
		var precost_id=document.getElementById('txt_selected_fabric_id').value;
		var param=document.getElementById('txt_select_item').value;
		var cbo_item_from=document.getElementById('cbo_item_from').value*1;
	
		var param="'"+param+"'"
		var data="'"+data+"'"
		var precost_id="'"+precost_id+"'"
	
		var cbo_level=document.getElementById('cbo_level').value;
	
		var data="action=show_fabric_booking_list"+get_submitted_data_string('txt_booking_no',"../../")+'&cbo_company_name='+cbo_company_name+'&garments_nature='+garments_nature+'&data='+data+'&param='+param+'&pre_cost_id='+precost_id+'&cbo_level='+cbo_level+'&cbo_currency='+cbo_currency+'&cbo_item_from='+cbo_item_from;
		http.open("POST","requires/multi_job_additional_fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_booking_list_reponse;
	}
	
	function fnc_show_booking_list_reponse(){
		if(http.readyState == 4){
			$("#cbo_currency").attr("disabled",true);
			document.getElementById('booking_list_view_list').innerHTML=http.responseText;
			set_button_status(1, permission, 'fnc_additional_fabric_booking',2);
			set_all_onclick();
		}
	}
	
	function fnc_show_booking(wo_pre_cost_trim_id,po_id,booking_id,job_no){
		freeze_window(operation);
		var garments_nature=document.getElementById('garments_nature').value;
		var cbo_item_from=document.getElementById('cbo_item_from').value;
		//var cbo_booking_year=document.getElementById('cbo_booking_year').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_currency=document.getElementById('cbo_currency').value;
		var data=document.getElementById('txt_selected_po').value;
		var precost_id=document.getElementById('txt_selected_fabric_id').value;
		var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
		var param=document.getElementById('txt_select_item').value;
		var cbo_item_from=document.getElementById('cbo_item_from').value*1;
		var permission='<? echo $permission; ?>';
	
		var cbo_level=document.getElementById('cbo_level').value;
	
		var data="action=show_trim_booking"+get_submitted_data_string('txt_booking_no',"../../")+'&cbo_company_name='+cbo_company_name+'&cbo_item_from='+cbo_item_from+'&cbo_item_from='+cbo_item_from+'&garments_nature='+garments_nature+'&data='+po_id+'&booking_id='+booking_id+'&pre_cost_id='+wo_pre_cost_trim_id+'&cbo_level='+cbo_level+'&cbo_currency='+cbo_currency+'&job_no='+job_no+'&cbo_pay_mode='+cbo_pay_mode+'&cbo_item_from='+cbo_item_from+'&permission='+permission;
		http.open("POST","requires/multi_job_additional_fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_booking_reponse;
	}
	
	function fnc_show_booking_reponse(){
		if(http.readyState == 4){
			$("#cbo_currency").attr("disabled",true);
			document.getElementById('booking_list_view').innerHTML=http.responseText;
				//compare_date(2);
			set_all_onclick();
			release_freezing();
		}
	}
	
	function generate_trim_report(action,report_type,mail_send_data){
		if (form_validation('txt_booking_no','Booking No')==false){
			return;
		}
		else
		{
			if(action=='show_fabric_booking_report3')
			{
				var show_comment='';
				var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
				if (r==true) show_comment="1"; else show_comment="0";
			}
			else if(action=='show_fabric_booking_report12')
			{
				var show_comment='';
				var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
				if (r==true) show_comment="1"; else show_comment="0";
			}
			else if(action=='show_fabric_booking_report_wg')
			{
				var show_comment='';
				var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
				if (r==true) show_comment="1"; else show_comment="0";
			}
			else if(action=='show_fabric_booking_report5')
			{
				var show_comment='';
				var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
				if (r==true) show_comment="1"; else show_comment="0";
			}
			else if(action=='show_fabric_booking_report13')
			{
				var show_comment='';
				var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
				if (r==true) show_comment="1"; else show_comment="0";
			}
			else if(action=='show_fabric_booking_report9')
			{
				var show_comment='';
				var r=confirm("Press  \"Cancel\"  to hide Rate\nPress  \"OK\"  to Show Rate");
				if (r==true) show_comment="1"; else show_comment="0";
			}
			else if(action=='show_fabric_booking_report16')
			{
				var show_comment='';
				var r=confirm("Press  \"Cancel\"  to hide Rate & Amount\nPress  \"OK\"  to Show Rate & Amount");
				if (r==true) show_comment="1"; else show_comment="0";
			}
			else if(action=='show_fabric_booking_report17')
			{
				var show_comment='';
				var r=confirm("Press  \"Cancel\"  to hide Rate, Amount & Remarks\nPress  \"OK\"  to Show Rate, Amount & Remarks");
				if (r==true) show_comment="1"; else show_comment="0";
			}
			else if(action=='show_fabric_booking_report6')
			{
				var show_comment=1;
			}
			else if(action=='show_fabric_booking_report18')
			{
				var show_comment=1;
			}
			else
			{
				var show_comment='';
				var r=confirm("Press  \"Cancel\"  to hide  Remarks\nPress  \"OK\"  to Show Remarks");
				if (r==true) show_comment="1"; else show_comment="0";
			}
	
	
			var report_title=$( "div.form_caption" ).html();
			// freeze_window();
			var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id',"../../")+'&report_title='+report_title+'&show_comment='+show_comment+'&report_type='+report_type+'&mail_send_data='+mail_send_data;
			http.open("POST","requires/multi_job_additional_fabric_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;
		
		}
	}
	
	function generate_trim_report_reponse(){
		if(http.readyState == 4){
			release_freezing();
			var file_data=http.responseText.split("####");
		   //  alert(file_data[2]);
			if(file_data[2]==100)
			{
			$('#data_panel').html(file_data[0]);
			$('#print_report6').removeAttr('href').attr('href','requires/'+trim(file_data[1]));
				//$('#print_report4')[0].click();
			document.getElementById('print_report6').click();
			}
			 if(file_data[2]==101)
			{
				// alert(file_data[1]);
			$('#data_panel').html(file_data[0]);
			$('#print_excel19').removeAttr('href').attr('href','requires/'+trim(file_data[1]));
				//$('#print_excel19')[0].click();
			document.getElementById('print_excel19').click();
			}
			else{
			  $('#pdf_file_name').html(file_data[1]);
			  $('#data_panel').html(file_data[0]);
			}
			
			var report_title=$( "div.form_caption" ).html();
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title>'+report_title+'</title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
			d.close();
		}
	}
	
	function generate_fabric_excel_report(action,report_type)
	{
		if (form_validation('txt_booking_no','Booking No')==false){
				return;
		}
		else
		{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  Remarks\nPress  \"OK\"  to Show Remarks");
			if (r==true) show_comment="1"; else show_comment="0";
		
			var report_title=$( "div.form_caption" ).html();
			  // freeze_window();
		
		
			var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id',"../../")+'&report_title='+report_title+'&show_comment='+show_comment+'&report_type='+report_type;
			var excel_check=0;
	
			if(action=='show_fabric_booking_report20')
			{
				freeze_window(5);
				var user_id = "<? echo $user_id; ?>";
				$.ajax({
					url: 'requires/multi_job_additional_fabric_booking_controller.php',
					type: 'POST',
					data: data,
					success: function(data){
						window.open('../../auto_mail/tmp/multiple_trims_booking_v2_'+user_id+'.pdf');
						release_freezing();
					}
				});
				var excel_check=1;
			}
			if (excel_check==1){
	
				freeze_window(5);
				http.open("POST","requires/multi_job_additional_fabric_booking_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = generate_fabric_report_reponse2;
			}
		}
	}
	
	function generate_fabric_report_reponse2(){
			if(http.readyState == 4){
			release_freezing();
			var file_data=http.responseText.split("####");
			if(file_data[2]==101)
			  {
				 $('#data_panel').html(file_data[0]);
				 $('#print_excel20').removeAttr('href').attr('href','requires/'+trim(file_data[1]));
				 //$('#print_excel20')[0].click();
				 document.getElementById('print_excel20').click();
			  }
			else{
				$('#data_panel').html(file_data[0]);
			}
		}
	}
	
	function call_print_button_for_mail(mail_address,mail_body,type){
		/*var response=return_global_ajax_value(document.getElementById('cbo_company_name').value, 'send_mail_report_setting_first_select', '', 'requires/multi_job_additional_fabric_booking_controller');
		var report_id=response.split(",");
		var mail_address= mail_address+'___'+mail_body;
		
		if(report_id[0]==67) generate_trim_report('show_fabric_booking_report2',1,'1___'+mail_address);
		else if(report_id[0]==183) generate_trim_report('show_fabric_booking_report3',1,'1___'+mail_address);
		else if(report_id[0]==85)  generate_trim_report('show_fabric_booking_report8',1,'1___'+mail_address);
		else if(report_id[0]==209) generate_trim_report('show_fabric_booking_report4',1,'1___'+mail_address);
		else if(report_id[0]==235) generate_trim_report('show_fabric_booking_report5',1,'1___'+mail_address);
        else if(report_id[0]==176) generate_trim_report('show_fabric_booking_report6',1,'1___'+mail_address);
		else if(report_id[0]==746) generate_trim_report('show_fabric_booking_report7',1,'1___'+mail_address);
		else if(report_id[0]==177) generate_trim_report('show_fabric_booking_report9',1,'1___'+mail_address);
		else if(report_id[0]==241) generate_trim_report('show_fabric_booking_report11',1,'1___'+mail_address);
		else if(report_id[0]==274) generate_trim_report('show_fabric_booking_report10',1,'1___'+mail_address);
        else if(report_id[0]==269) generate_trim_report('show_fabric_booking_report12',1,'1___'+mail_address);
		else if(report_id[0]==28)  generate_trim_report('show_fabric_booking_report13',1,'1___'+mail_address);
		else if(report_id[0]==280) generate_trim_report('show_fabric_booking_report14',1,'1___'+mail_address);
		else if(report_id[0]==304) generate_trim_report('show_fabric_booking_report15',1,'1___'+mail_address);
		else if(report_id[0]==14)  generate_trim_report('show_fabric_booking_report16',0,'1___'+mail_address);
		else if(report_id[0]==719) generate_trim_report('show_fabric_booking_report17',1,'1___'+mail_address);
		else if(report_id[0]==339) generate_trim_report('show_fabric_booking_report18',1,'1___'+mail_address);
		else if(report_id[0]==433) generate_trim_report('show_fabric_booking_report19',1,'1___'+mail_address);
		else if(report_id[0]==768) generate_fabric_excel_report('show_fabric_booking_report20',1,'1___'+mail_address);
		else if(report_id[0]==404) generate_trim_report('show_fabric_booking_report21',1,'1___'+mail_address);
		else if(report_id[0]==419) generate_trim_report('show_fabric_booking_report22',1,'1___'+mail_address);
		else if(report_id[0]==774) generate_trim_report('show_fabric_booking_report_wg',1,'1___'+mail_address);
		else if(report_id[0]==786) generate_trim_report('show_fabric_booking_report25',1,'1___'+mail_address);*/

	}
	
	function generate_trim_report(action,report_type,mail_send_data){
		if (form_validation('txt_booking_no','Booking No')==false){
			return;
		}
		else
		{
			if(action=='show_fabric_booking_report')
			{
				var show_comment='';
				var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
				if (r==true) show_comment="1"; else show_comment="0";
			}else
			{
				var show_comment='';
				var r=confirm("Press  \"Cancel\"  to hide  Remarks\nPress  \"OK\"  to Show Remarks");
				if (r==true) show_comment="1"; else show_comment="0";
			}
	
			var report_title=$( "div.form_caption" ).html();
			// freeze_window();
			var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id',"../../")+'&report_title='+report_title+'&show_comment='+show_comment+'&report_type='+report_type+'&mail_send_data='+mail_send_data;
			http.open("POST","requires/multi_job_additional_fabric_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;
		
		}
	}

	function generate_trim_report_reponse(){
		if(http.readyState == 4){
			release_freezing();
			var file_data=http.responseText.split("####");
		   //  alert(file_data[2]);
			if(file_data[2]==100)
			{
			$('#data_panel').html(file_data[0]);
			$('#print_report6').removeAttr('href').attr('href','requires/'+trim(file_data[1]));
				//$('#print_report4')[0].click();
			document.getElementById('print_report6').click();
			}
			 if(file_data[2]==101)
			{
				// alert(file_data[1]);
			$('#data_panel').html(file_data[0]);
			$('#print_excel19').removeAttr('href').attr('href','requires/'+trim(file_data[1]));
				//$('#print_excel19')[0].click();
			document.getElementById('print_excel19').click();
			}
			else{
			  $('#pdf_file_name').html(file_data[1]);
			  $('#data_panel').html(file_data[0]);
			}
			
			var report_title=$( "div.form_caption" ).html();
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title>'+report_title+'</title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
			d.close();
		}
	}
</script>
<?
//----------------------------------------------------------------------------------------------------------------------------------
$level_arr             = array(1=>"PO Level",2=>"Job Level");
$item_from_arr=array(1=>"Pre-Costing",2=>"Library");
					
$buyer_cond            = set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond          = set_user_lavel_filtering(' and comp.id','company_id');

$endis                 = "disable_enable_fields( 'cbo_currency*cbo_company_name*cbo_supplier_name*cbo_level*cbo_buyer_name', 0 )";
$buttons               = load_submit_buttons( $permission, "fnc_additional_fabric_booking", 0,0 ,"reset_form('additionalfabricbooking_1','booking_list_view*booking_list_view_list*app_sms2*pdf_file_name','id_approved_id*txt_select_item','txt_booking_date,".date('d-m-Y')."*cbo_ready_to_approved,2',$endis,'cbo_currency*cbo_booking_year*cbo_booking_month*copy_val*cbo_pay_mode*cbo_source*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_level*cbo_company_name*cbo_buyer_name*cbo_material_source')",1);
?>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="additionalfabricbooking_1"  autocomplete="off" id="additionalfabricbooking_1">
        <fieldset style="width:1110px;">
            <legend title="V3">Multi Job wise Additional Fabric Booking &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <font id="app_sms" style="color:#F00"></font></legend>
            <table  width="1100" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td align="right" class="must_entry_caption" colspan="5"><b>Booking No</b></td>
                    <td colspan="5">
                        <input class="text_boxes" type="text" style="width:140px" onDblClick="openmypage_booking('requires/multi_job_additional_fabric_booking_controller.php?action=fabric_booking_popup','Fabric Booking Search');" placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no" readonly/>
                        <input type="hidden" id="id_approved_id">
                        <input type="hidden" id="exeed_budge_qty">
                        <input type="hidden" id="exeed_budge_amount">
                        <input type="hidden" id="amount_exceed_level">
                        <input type="hidden" id="report_ids" />
                        <input type="hidden" id="cbo_currency_job"  />
                        <input type="hidden" id="lib_tna_intregrate" />
                        <input type="hidden" id="update_id" />
                    </td>
                </tr>
                <tr>
                    <td width="80" class="must_entry_caption">Company</td>
					<td width="140"><?=create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "get_php_form_data( this.value, 'populate_variable_setting_data', 'requires/multi_job_additional_fabric_booking_controller' ); check_exchange_rate();","","" ); ?></td>
                    <td width="70" class="must_entry_caption">Buyer</td>
                    <td width="140" id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "check_paymode(this.value);","" ); ?></td>
                    <td width="80" class="must_entry_caption">Fabric Nature</td>
                    <td width="140">
						<?
                        echo create_drop_down( "cbo_fabric_natu", 70, $item_category,"", 1, "-- Select --", 1,$onchange_func, $is_disabled, "2,3");
                        echo create_drop_down( "cbouom", 50, $unit_of_measurement,'', 1, '-Uom-', $row[csf('uom')], "",$disabled,"1,12,23,27" );
                        ?>
                    </td>
                    <td width="90" class="must_entry_caption">Fabric Source</td>
                    <td width="140"><?=create_drop_down( "cbo_fabric_source", 120, $fabric_source,"", 1, "-- Select --", "","", "", ""); ?></td>
                    <td width="90" class="must_entry_caption">Booking Date</td>
                    <td><input class="datepicker" type="text" style="width:110px" name="txt_booking_date" id="txt_booking_date" value="<?=date('d-m-Y'); ?>" onChange="check_exchange_rate();" disabled /></td>
                </tr>
                <tr>
                	<td class="must_entry_caption">Pay Mode</td>
                    <td><?=create_drop_down( "cbo_pay_mode", 120, $pay_mode,"", 1, "-- Select Pay Mode --", "", "load_drop_down( 'requires/multi_job_additional_fabric_booking_controller', this.value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_supplier', 'supplier_td' )","" );; ?></td>
                    <td class="must_entry_caption">Supplier</td>
                    <td id="supplier_td"><?=create_drop_down( "cbo_supplier_name", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in (9) and a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-Select Supplier-", $selected, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/multi_job_additional_fabric_booking_controller');",0 ); ?></td>
                    <td>Currency</td>
                    <td><?=create_drop_down( "cbo_currency", 120, $currency,"", 1, "-- Select --", 2, "",0 ); ?></td>
                    <td>Exchange Rate</td>
                    <td><input style="width:110px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  /></td>
                	<td  class="must_entry_caption">Delivery Date</td>
                    <td><input class="datepicker" type="text" style="width:110px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                </tr>
                <tr>
                	<td class="must_entry_caption"> Source </td>
                    <td><?=create_drop_down( "cbo_source", 120, $source,"", 1, "-- Select Source --", "", "","" ); ?> </td>
                    <td>Attention</td>
                    <td colspan="3"><input class="text_boxes" type="text" style="width:330px;" name="txt_attention" id="txt_attention" placeholder="Attention" /></td>
                    <td>Delivery To</td>
                    <td colspan="3"><input id="txtdelivery_address" name="txtdelivery_address" class="text_boxes" type="text" style="width:340px;" placeholder="Delivery Address" /></td>
                </tr>
                <tr>
                	<td>Tenor</td>
                    <td><input style="width:110px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
                	<td class="must_entry_caption">Item From</td>
                    <td><?=create_drop_down( "cbo_item_from", 120, $item_from_arr,"", 1, "-- Select --", "", "","" ); ?></td>
                    <td class="must_entry_caption">Level</td>
                    <td><?=create_drop_down( "cbo_level", 120, $level_arr,"", 0, "", 2, "","","" ); ?></td>
                    <td>Ready To App.</td>
                    <td><?=create_drop_down( "cbo_ready_to_approved", 120, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
                    <td>Un-app.request</td>
                    <td><Input name="txt_un_appv_request" class="text_boxes" readonly placeholder="Double Click for Brows" ID="txt_un_appv_request" style="width:110px"  onClick="openmypage_unapprove_request();"></td>
                </tr>
                <tr>
					<td>Remarks</td>
                    <td colspan="3"><input id="txt_remarks" name="txt_remarks" class="text_boxes" type="text" style="width:320px;" placeholder="Remarks"/></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><input type="button" class="image_uploader" style="width:90px" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('txt_booking_no').value,'', 'multiple_job_wise_additional_fabric_booking', 2 ,1)"> </td>
                    <td>
                      <?
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(555,'txt_booking_no','../../','');
                      ?>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" colspan="10" valign="top" id="app_sms2" style="font-size:18px; color:#F00"></td>
                </tr>
                <tr>
                    <td align="center" colspan="10" valign="middle" class="button_container">
                    <?=$buttons ; ?>
					<input type="button" value="Send" onClick="fnSendMail('../../','update_id',1,0,0,0,0)"  style="width:80px;" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="10" height="10">
                    <input type="button" value="Print Booking" onClick="generate_trim_report('show_fabric_booking_report',1)"  style="width:100px;" name="print_booking" id="print_booking" class="formbutton" />
                    Copy:<input type="checkbox" id="copy_val"  name="copy_val" checked/>
                    <input class="text_boxes" type="hidden" style="width:160px"  readonly  name="txt_tot_req_amount" id="txt_tot_req_amount"/>
                    <input class="text_boxes" type="hidden" style="width:160px"  readonly  name="txt_tot_cu_amount" id="txt_tot_cu_amount"/>
                    <div style="width:950px;word-break:break-all" id="pdf_file_name"></div>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    <fieldset style="width:950px;">
            <legend title="V3">Fabric Booking Item Form &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            Select Item: <input class="text_boxes" type="text" style="width:160px" onDblClick="fnc_process_data();" readonly placeholder="Double Click" name="txt_select_item" id="txt_select_item"/>
            <input class="text_boxes" type="hidden" style="width:160px"  readonly placeholder="Double Click" name="txt_selected_po" id="txt_selected_po"/>
            <input class="text_boxes" type="hidden" style="width:160px"  readonly placeholder="Double Click" name="txt_selected_fabric_id" id="txt_selected_fabric_id"/></legend>
    <div id="booking_list_view"><font id="save_sms" style="color:#F00">Select New Item</font></div>
    </fieldset>
    <div id="booking_list_view_list"></div>
</div>
<div style="display:none" id="data_panel"></div>
</body>
<script>

function copy_value(value,field_id,i){
	var copy_val=document.getElementById('copy_val').checked;
	var txttrimgroup=document.getElementById('txttrimgroup_'+i).value;
	if(txttrimgroup>0)
	{
		var uom_id=return_global_ajax_value(txttrimgroup, 'item_group_uom', '', 'requires/multi_job_additional_fabric_booking_controller');
		//alert(uom_id);
		document.getElementById('txtuom_'+i).value=uom_id;
	}
	var rowCount = $('#tbl_list_search tr').length;

	if(copy_val==true){
		freeze_window(operation);
		for(var j=i; j<=rowCount; j++){
			if(field_id=='txtdescription_'){
				if( txttrimgroup==document.getElementById('txttrimgroup_'+j).value){
					document.getElementById(field_id+j).value=value;
				}
			}
			if(field_id=='txtbrandsupref_'){
				if( txttrimgroup==document.getElementById('txttrimgroup_'+j).value){
					document.getElementById(field_id+j).value=value;
				}
			}
			if(field_id=='cbocolorsizesensitive_'){

				if( txttrimgroup==document.getElementById('txttrimgroup_'+j).value){
					document.getElementById(field_id+j).value=value;
				}
			}
		}
		release_freezing();
	}
}

function open_consumption_popup(page_link,title,po_id,i)
{
	var garments_nature=document.getElementById('garments_nature').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var txt_job_no=document.getElementById('txtjob_'+i).value;
	var txt_po_id =document.getElementById(po_id).value;
	
	var cbo_trim_precost_id=document.getElementById('txttrimcostid_'+i).value;
	//alert(cbo_trim_precost_id);
	var txt_trim_group_id=document.getElementById('txttrimgroup_'+i).value;
	var txt_update_dtls_id=document.getElementById('txtbookingid_'+i).value;
	var cbo_colorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
	var txt_req_quantity=document.getElementById('txtreqqnty_'+i).value;
	var txtwoq=document.getElementById('txtwoq_'+i).value;
	var txt_req_amount=document.getElementById('txtreqamount_'+i).value;
	var txt_avg_price=document.getElementById('txtrate_'+i).value;
	var txt_country=document.getElementById('txtcountry_'+i).value;
	var txt_pre_des=document.getElementById('txtdesc_'+i).value;
	var txt_pre_brand_sup=document.getElementById('txtbrandsup_'+i).value;
	var txtcuwoq=document.getElementById('txtcuwoq_'+i).value;
	var txtcuamount=document.getElementById('txtcuamount_'+i).value*1;
	var cbo_level=document.getElementById('cbo_level').value*1;
	var cbo_item_from=document.getElementById('cbo_item_from').value*1;
	var cons_breck_downn=document.getElementById('consbreckdown_'+i).value;
	if(cbo_item_from==2)
	{
	var txtgmtsqty=document.getElementById('txtgmtsqty_'+i).value;
	}
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	
	if(txt_trim_group_id==0 ){
		alert("Select Trim Group");
		$("#txttrimgroup_"+i).focus();
		return;
	}
	
	if(po_id==0 ){
		alert("Select Po Id")
	}
	else{
		var page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&txt_job_no='+txt_job_no+'&txt_po_id='+txt_po_id+'&cbo_trim_precost_id='+cbo_trim_precost_id+'&cbo_item_from='+cbo_item_from+'&txt_trim_group_id='+txt_trim_group_id+'&txt_update_dtls_id='+txt_update_dtls_id+'&cbo_colorsizesensitive='+cbo_colorsizesensitive+'&txt_req_quantity='+txt_req_quantity+'&txt_avg_price='+txt_avg_price+'&txt_country='+txt_country+'&txt_pre_des='+txt_pre_des+'&txt_pre_brand_sup='+txt_pre_brand_sup+"&cbo_level="+cbo_level+"&txtwoq="+txtwoq+"&txtgmtsqty="+txtgmtsqty+"&txt_booking_no="+txt_booking_no;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1280px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
			var woq=this.contentDoc.getElementById("woqty_sum");
			var rate=this.contentDoc.getElementById("rate_sum");
			var amount=this.contentDoc.getElementById("amount_sum");
			var json_data=this.contentDoc.getElementById("json_data");
			document.getElementById('consbreckdown_'+i).value=cons_breck_down.value;
			document.getElementById('txtwoq_'+i).value=woq.value;
			document.getElementById('txtrate_'+i).value=rate.value;
			document.getElementById('txtamount_'+i).value=amount.value;
			document.getElementById('jsondata_'+i).value=json_data.value;
			calculate_amount(i);
		}
	}
}

function set_cons_break_down(i){

	document.getElementById('consbreckdown_'+i).value="";
	document.getElementById('jsondata_'+i).value="";

	var garments_nature=document.getElementById('garments_nature').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var txt_job_no=document.getElementById('txtjob_'+i).value;
	var txt_po_id =document.getElementById('txtpoid_'+i).value;
	var cbo_trim_precost_id=document.getElementById('txttrimcostid_'+i).value;
	var txt_trim_group_id=document.getElementById('txttrimgroup_'+i).value;
	var txt_update_dtls_id=document.getElementById('txtbookingid_'+i).value;
	var cbo_colorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
	var txt_req_quantity=document.getElementById('txtreqqnty_'+i).value;
	var txt_avg_price=document.getElementById('txtrate_'+i).value;
	var txt_country=document.getElementById('txtcountry_'+i).value;
	var txt_pre_des=document.getElementById('txtdesc_'+i).value;
	var txt_pre_brand_sup=document.getElementById('txtbrandsup_'+i).value;
	var txtwoq=document.getElementById('txtbalwoq_'+i).value;
	var txtcurwoq=document.getElementById('txtwoq_'+i).value*1;
	var cbo_level=document.getElementById('cbo_level').value*1;
	var cons_breack_down=trim(return_global_ajax_value(garments_nature+"_"+cbo_company_name+"_"+txt_job_no+"_"+txt_po_id+"_"+cbo_trim_precost_id+"_"+txt_trim_group_id+"_"+txt_update_dtls_id+"_"+cbo_colorsizesensitive+"_"+txt_req_quantity+"_"+txt_avg_price+"_"+txt_country+"_"+txt_pre_des+"_"+txt_pre_brand_sup+"_"+cbo_level+"_"+txtcurwoq, 'set_cons_break_down', '', 'requires/multi_job_additional_fabric_booking_controller'));
	//alert(cons_breack_down);
	cons_breack_down=cons_breack_down.split("**");
    document.getElementById('consbreckdown_'+i).value=trim(cons_breack_down[0]);
    document.getElementById('jsondata_'+i).value=cons_breack_down[1];
}

function calculate_amount(i){
	var txtrate_precost=(document.getElementById('txtrate_precost_'+i).value)*1
	var txtrate=(document.getElementById('txtrate_'+i).value)*1
	var txtexchrate=(document.getElementById('txtexchrate_'+i).value)*1
	var txtwoq=(document.getElementById('txtwoq_'+i).value)*1
	document.getElementById('txtamount_'+i).value=number_format_common((txtrate*txtwoq),5,0);
	var tot_amount=0
	var row_num=$('#tbl_list_search tr').length;
	for (var j=1; j<=row_num; j++){
		var amount=document.getElementById('txtamount_'+j).value*1
		tot_amount+=amount;
	}
	document.getElementById('tot_amount').value=number_format_common(tot_amount,5,0);
}

//for print button
function print_report_button_setting(report_ids)
{
	$("#print_booking1").hide();
	$("#print_booking2").hide();
	$("#print_booking4").hide();
	$("#print_booking5").hide();
	$("#print_booking6").hide();
	$("#print_booking7").hide();
	$("#print_booking8").hide();
	$("#print_booking9").hide();
	$("#print_booking10").hide();
	$("#print_booking11").hide();
    $("#print_booking12").hide();
	$("#print_booking13").hide();
	$("#print_booking14").hide();
	$("#print_booking15").hide();
	$("#print_booking16").hide();
	$("#print_booking17").hide();
	$("#print_booking18").hide();
	$("#print_booking19").hide();
	$("#print_booking20").hide();
	$("#print_booking21").hide();
	$("#print_booking22").hide();
	$("#print_booking23").hide();
	$("#print_booking24").hide();
	$("#print_booking_wg").hide();
	$("#print_booking25").hide();
	
	var report_id=report_ids.split(",");
	for (var k=0; k<report_id.length; k++)
	{
		//177
		//alert(report_id[k])
		if(report_id[k]==67) $("#print_booking1").show();
		else if(report_id[k]==183) $("#print_booking2").show();		
		else if(report_id[k]==209) $("#print_booking3").show();
		else if(report_id[k]==235) $("#print_booking5").show();
        else if(report_id[k]==176) $("#print_booking6").show();
		else if(report_id[k]==746) $("#print_booking7").show();
		else if(report_id[k]==85) $("#print_booking8").show();
		else if(report_id[k]==177) $("#print_booking9").show();
		else if(report_id[k]==241) $("#print_booking11").show();
		else if(report_id[k]==274) $("#print_booking10").show();
        else if(report_id[k]==269) $("#print_booking12").show();
		else if(report_id[k]==28) $("#print_booking13").show();
		else if(report_id[k]==280) $("#print_booking14").show();
		else if(report_id[k]==304) $("#print_booking15").show();
		else if(report_id[k]==14) $("#print_booking16").show();
		else if(report_id[k]==719) $("#print_booking17").show();
		else if(report_id[k]==339) $("#print_booking18").show();
		else if(report_id[k]==433) $("#print_booking19").show();
		else if(report_id[k]==768) $("#print_booking20").show();
		else if(report_id[k]==404) $("#print_booking21").show();
		else if(report_id[k]==419) $("#print_booking22").show();
		else if(report_id[k]==426) $("#print_booking23").show();
		else if(report_id[k]==774) $("#print_booking_wg").show();
		else if(report_id[k]==452) $("#print_booking24").show();
		else if(report_id[k]==786) $("#print_booking25").show();
	}
}

function check_paymode()
{
	$('#cbo_pay_mode').val('');
}

function openmypage_unapprove_request()
{
	if (form_validation('txt_booking_no','Booking Number')==false)
	{
		return;
	}

	var txt_booking_no=document.getElementById('txt_booking_no').value;
	var txt_un_appv_request=document.getElementById('txt_un_appv_request').value;
	var data=txt_booking_no+"_"+txt_un_appv_request;
	var title = 'Un Approval Request';
	var page_link = 'requires/multi_job_additional_fabric_booking_controller.php?data='+data+'&action=unapp_request_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
		$('#txt_un_appv_request').val(unappv_request.value);
	}
}

function openlabeldtls_popup(trimitem,i)
{
	var title = 'Label Details';
	
	var page_link = 'requires/multi_job_additional_fabric_booking_controller.php?data='+trimitem+'&action=labeldtls_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var labeldtlsdata=this.contentDoc.getElementById("hidd_dtlsdata").value;
		
		$('#hiddlabeldtlsdata_'+i).val(labeldtlsdata);
		
	}
}

function deletedata(booking_id){
		var operation=2;
		freeze_window(operation);
		
		var delete_cause='';
		if(operation==2){
			delete_cause = prompt("Please enter your delete cause", "");
			if(delete_cause==""){
				alert("You have to enter a delete cause");
				release_freezing();
				return;
			}
			if(delete_cause==null){
				release_freezing();
				return;
			}
			var r=confirm("Press OK to Delete Or Press Cancel");
			if(r==false){
				release_freezing();
				return;
			}
		}

		var txt_booking_no=document.getElementById('txt_booking_no').value;
		var check_is_booking_used_id=return_global_ajax_value(txt_booking_no, 'check_is_booking_used', '', 'requires/multi_job_additional_fabric_booking_controller');
		var reponse=trim(check_is_booking_used_id).split('**');
		if(trim(reponse[0])!="")
		{
			if(trim(reponse[0])=='approved'){
				alert("This booking is approved");
				release_freezing();
				return;
			}

			if(trim(reponse[0])=='pi1'){
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}

			if(trim(reponse[0])=='rec1'){
				alert("Receive  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}

			if(trim(reponse[0])=='iss1'){
				alert("Issue found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			release_freezing();
			//alert("This booking used in PI Table. So Adding or removing order is not allowed")
			return;
		}

		var row_num=1;
		if (form_validation('txt_booking_no','Booking No')==false){
			release_freezing()
			return;
		}
		
        var i=1; var dltsid=""; var z=1;
		var data_all=get_submitted_data_string('txt_booking_no',"../../",i);
		var listrows =$('#list_view tbody tr').length; 
		//alert(listrows);
		if(document.getElementById('chkdeleteall').checked==true)
		{
			for (var i = 1; i <= listrows; i++)
			{
				dltsid+="&txtbookingid_"+z+"='" + $('#txtdelete'+i).val()+"'";
				z++;
			}
		}
		else
		{
			for (var i = 1; i <= listrows; i++)
			{
				if(document.getElementById('chkdelete_'+i).checked==true)
				{
					dltsid+="&txtbookingid_"+z+"='" + $('#txtdelete'+i).val()+"'";
					z++;
				}
			}
		}
		if(z==1 && dltsid=="")
		{
			alert("Please Select minimum 1 row.");
			release_freezing()
			return;
		}
		var data="action=delete_dtls_data&operation="+operation+'&total_row='+z+dltsid+data_all+"&delete_cause="+delete_cause;
		
		/*alert(data);release_freezing()
			return;*/
		http.open("POST","requires/multi_job_additional_fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_additional_fabric_booking_dtls_reponse;
	}
	 

</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
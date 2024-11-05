<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Short Fabric Booking
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	27-12-2012
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
Comments		         :  From this version oracle conversion is start
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
$user_id=$_SESSION['logic_erp']['user_id'];
$department_id=return_field_value("department_id", "user_passwd", "id = $user_id AND VALID = 1");
	//echo $department_id;
	if($department_id)
	{
		$department_id=$department_id;	
	}
	else $department_id='';	

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Short Fabric Booking [Knit]", "../../", 1, 1,$unicode,1,'');
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';
<?
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][88] );
echo "var field_level_data= ". $data_arr . ";\n";
//echo "var mandatory_field = '".implode('*',$_SESSION['logic_erp']['mandatory_field'][88]). "';\n";
//echo "var field_message = '".implode('*',$_SESSION['logic_erp']['field_message'][88]). "';\n";
?>
	function openmypage_order(page_link,title)
	{
		if(document.getElementById('id_approved_id').value==1 || document.getElementById('id_approved_id').value==3)
		{
			alert("This booking is approved")
			return;
		}
		var month_check=$('#month_id').val();
		//alert(month_check);
		if(month_check==1)
		{
			if (form_validation('cbo_booking_month*cbo_booking_year*cbo_fabric_natu*cbo_fabric_source','Booking Month*Booking Year*Fabric Nature*Fabric Source')==false)
			{
				return;
			}	
		}
		else
		{
			if (form_validation('cbo_booking_year*cbo_fabric_natu*cbo_fabric_source','Booking Year*Fabric Nature*Fabric Source')==false)
			{
				return;
			}
		}
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		var check_is_booking_used_id=return_global_ajax_value(txt_booking_no, 'check_is_booking_used', '', 'requires/short_fabric_booking_controller');
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
			if(trim(reponse[0])=='PPL'){
				alert("Plan Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='sal1'){
				alert("Sales Order Approved found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
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
			return;
		}
		else
		{
			if(txt_booking_no=="")
			{
			page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year*txt_booking_date','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1300px,height=470px,center=1,resize=1,scrolling=0','../')
			}
			else
			{
				var r=confirm("Existing Item against these Order  Will be Deleted")
				if(r==true)
				{
					var delete_booking_item=return_global_ajax_value(txt_booking_no, 'delete_booking_item', '', 'requires/short_fabric_booking_controller');
					show_list_view(txt_booking_no,'show_fabric_booking','booking_list_view','requires/short_fabric_booking_controller','setFilterGrid(\'list_view\',-1)');
					page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year*txt_booking_date','../../');
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1300px,height=470px,center=1,resize=1,scrolling=0','../')
				}
				else
				{
					return;
				}
			}
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];;
				var id=this.contentDoc.getElementById("po_number_id");
				var po=this.contentDoc.getElementById("po_number");
				if (id.value!="")
				{
					freeze_window(5);
					resetDtlsForm();
					//reset_form('','booking_list_view','','',"$('#details_list tr:not(:first)').remove();",'');
					//set_multiselect('cbo_responsible_dept','0','1','<? //=$department_id;?>','0');
					document.getElementById('txt_order_no_id').value=id.value;
					document.getElementById('txt_order_no').value=po.value;
					var cbo_fabric_natu =document.getElementById('cbo_fabric_natu').value;
					var cbo_fabric_source=document.getElementById('cbo_fabric_source').value;
					var cbouom=document.getElementById('cbouom').value;
					//var fabricdescription_id=$('#cbo_fabricdescription_id').val();
					get_php_form_data( id.value, "populate_order_data_from_search_popup", "requires/short_fabric_booking_controller" );
					check_month_setting();
					var reportId=document.getElementById('report_ids').value;
					print_report_button_setting(reportId);
					
					fnc_get_po_config(id.value+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom+'_1');
					
					release_freezing();
					//fnc_generate_booking()
				}
			}
		}
	}
	
	function fnc_get_po_config(data)
	{
		//alert(data); return
		var exdata=data.split('_');
		var po_id=exdata[0];
		var fabricnature=exdata[1];
		var fabricsource=exdata[2];
		var fabricuom=exdata[3];
		var rowid=exdata[4];
		get_php_form_data(po_id+'_'+fabricnature+'_'+fabricsource+'_'+fabricuom+'_'+rowid,'get_po_config','requires/short_fabric_booking_controller' );
	}


	function openmypage_department()
	{
		
		var txt_department_no = $('#txt_department_no').val();
		var cbo_company_name = $('#cbo_company_name').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/short_fabric_booking_controller.php?cbo_company_name='+cbo_company_name+'&txt_department_num='+txt_department_no+'&action=department_no_popup', 'Department No Search', 'width=370px,height=420px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theemailid=this.contentDoc.getElementById("txt_department_id");
			var theemailval=this.contentDoc.getElementById("txt_department_val");
			if (theemailid.value!="" || theemailval.value!="")
			{
				//alert (theemailid.value);
				freeze_window(5);
				$("#hidd_department_id").val(theemailid.value);
				$("#txt_department_no").val(theemailval.value);
				release_freezing();
			}
		}
	}


	function openmypage_profit_center() {
        if (form_validation('cbo_company_name', 'Company') == false) {
            return;
        } 
        var cbo_name = $("#cbo_profit_center").val();
      
        // txt_profit_center_hidden_id
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/short_fabric_booking_controller.php?action=profit_center_popup&company_id=' + document.getElementById('cbo_company_name').value+'&txt_profit_center_hidden='+$("#txt_profit_center_hidden_id").val()+'&txt_profit_center='+$("#cbo_profit_center").val(), 'Profit Center Popup', 'width=390px,height=250px,center=1,resize=0', '../');
        emailwindow.onclose = function() { //txt_erotion_value
            freeze_window(5);
            var profit_center_id = this.contentDoc.getElementById("txt_profit_center_id").value;
            var profit_center_name = this.contentDoc.getElementById("txt_profit_center_name").value;
            document.getElementById("txt_profit_center_hidden_id").value = profit_center_id;
            document.getElementById("cbo_profit_center").value = profit_center_name;

            release_freezing();
        }
    }



	/* function profit_center_pop()
	{
		var txt_profit_id = $('#txt_profit_id').val();
		var txt_profit_seq = $('#txt_profit_seq').val();
		var cbo_company_name = $('#cbo_company_name').val();
		//var data = $("#cbo_company_name").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/short_fabric_booking_controller.php?cbo_company_name='+cbo_company_name+'&txt_profit_id='+txt_profit_id+'&txt_profit_seq='+txt_profit_seq+'&action=profit_center_popup', 'Profit Center Search', 'width=640px,height=420px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
			var process_name=this.contentDoc.getElementById("hidden_process_name").value;
			var process_seq=this.contentDoc.getElementById("hidden_process_seq").value;
			$('#txt_profit_id').val(process_id);
			$('#txt_profit_center').val(process_name);
			$('#txt_profit_seq').val(process_seq);
		}
	} */

	function set_process_loss(str)
	{
		var row_id=str;
		
		var fabric_id=$('#cbofabricdescriptionid_'+row_id).val();
		
		var prosess_loss=return_global_ajax_value(fabric_id, 'prosess_loss_set', '', 'requires/short_fabric_booking_controller');
		document.getElementById('txtprocessloss_'+row_id).value=trim(prosess_loss);
		calculate_requirement(row_id);
	}
	
	function calculate_requirement(row_id)
	{
		var cbo_company_name= document.getElementById('cbo_company_name').value;
		var cbo_fabric_natu= document.getElementById('cbo_fabric_natu').value
		var process_loss_method_id=return_global_ajax_value(cbo_company_name+'_'+cbo_fabric_natu, 'process_loss_method_id', '', 'requires/short_fabric_booking_controller');
		var txt_finish_qnty=(document.getElementById('txtfinishqnty_'+row_id).value)*1;
		var processloss=(document.getElementById('txtprocessloss_'+row_id).value)*1;
		var WastageQty='';
		if(process_loss_method_id==1)
		{
			WastageQty=txt_finish_qnty+txt_finish_qnty*(processloss/100);
		}
		else if(process_loss_method_id==2)
		{
			var devided_val = 1-(processloss/100);
			var WastageQty=parseFloat(txt_finish_qnty/devided_val);
		}
		else
		{
			WastageQty=0;
		}
		WastageQty= number_format_common( WastageQty, 5, 0) ;	
		document.getElementById('txtgreyqnty_'+row_id).value= WastageQty;
		document.getElementById('txtamount_'+row_id).value=number_format_common((document.getElementById('txtrate_'+row_id).value)*1*WastageQty,5,0);
	}

	function openmypage_booking(page_link,title)
	{
		// emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1250px,height=450px,center=1,resize=1,scrolling=0','../')
		// emailwindow.onclose=function()
		var company=$("#cbo_company_name").val()*1;
		//alert(company);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company, title, 'width=1190px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			if (theemail.value!="")
			{
				freeze_window(5);
				reset_form('fabricbooking_1','booking_list_view','', 'txt_booking_date,<? echo date("d-m-Y"); ?>');
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/short_fabric_booking_controller" );			
				check_month_setting();
				var reportId=document.getElementById('report_ids').value;
				
				print_report_button_setting(reportId);
				resetDtlsForm();
				//reset_form('','booking_list_view','','',"$('#details_list tr:not(:first)').remove();");
				var txt_order_no_id=document.getElementById('txt_order_no_id').value;
				
				var cbo_fabric_natu =document.getElementById('cbo_fabric_natu').value;
				var cbo_fabric_source=document.getElementById('cbo_fabric_source').value;
				var cbouom=document.getElementById('cbouom').value;
				
				fnc_get_po_config(txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom+'_1');
				
				show_list_view(theemail.value,'show_fabric_booking','booking_list_view','requires/short_fabric_booking_controller','setFilterGrid(\'list_view\',-1)');
				set_button_status(1, permission, 'fnc_fabric_booking',1);
				release_freezing();
			}
		}
	}

	function fnc_fabric_booking( operation )
	{
		freeze_window(operation);
		
		var profit_center=$('#cbo_profit_center').val()*1;
		var department=$('#txt_department_no').val()*1;
		var final_comment=$('#txt_final_comment').val()*1;
		var update_type=$('#txt_update_type').val()*1; 
		var approval_check="";
		if(profit_center) var approval_check=1;
		if(department) var approval_check=1;
		if(final_comment) var approval_check=1; 

		if(document.getElementById('id_approved_id').value==1)
		{
			if(update_type!=1 && approval_check=="") // Issue id - 21154 
			{
				alert("This booking is approved");
				release_freezing();
				return;
			}
		}
		var month_set_id=$('#month_id').val();
		if(month_set_id==1)
		{
			if (form_validation('cbo_booking_month','Booking Month')==false)
			{
				release_freezing();
				return;
			}	
		}
		
		var delivery_date=$('#txt_delivery_date').val();
		
		if(date_compare($('#txt_booking_date').val(), delivery_date)==false)
		{
			alert("Delivery Date Not Allowed Less than Booking Date");
			release_freezing();
			return;
		}

		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][88]);?>'){
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][88]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][88]);?>')==false)
			{
				release_freezing();
				return;
			}
		}
		
		if (form_validation('cbo_buyer_name*txt_order_no_id*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_supplier_name*txt_exchange_rate','Buyer*Order No*Booking Date*Delivery Date*Pay Mode*Supplier*Exchange Rate')==false)
		{
			release_freezing();
			return;
		}	
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_order_no_id*update_id*cbo_company_name*cbo_buyer_name*txt_job_no*txt_booking_no*cbo_fabric_natu*cbo_fabric_source*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_booking_month*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_source*cbo_booking_year*cbo_ready_to_approved*cbo_short_booking_type*cbouom*txt_remark*cbo_supplier_location*hiddshippingmark_breck_down*cbo_shipmode*cbo_payterm*txt_tenor*hidd_mainbooking_id*txt_mainbooking_no*txt_department_no*hidd_department_id*cbo_profit_center*txt_profit_center_hidden_id*txt_final_comment*txt_update_type*cbo_provider_name*txt_reqsn_no*txt_reqsn_id*cbo_brand_id*cbo_season_year*cbo_season_id',"../../");
			
			http.open("POST","requires/short_fabric_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_booking_reponse;
		}
	}
	 
	function fnc_fabric_booking_reponse(){
		if(http.readyState == 4){
			 var reponse=trim(http.responseText).split('**');
			 if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1 || parseInt(trim(reponse[0]))==2){
				 show_msg(trim(reponse[0]));
				 $('#cbo_company_name').attr('disabled','disabled');
				 document.getElementById('txt_booking_no').value=reponse[1]; 
				 document.getElementById('update_id').value=reponse[2];
				 set_button_status(1, permission, 'fnc_fabric_booking',1);
				 release_freezing();
			 }
			 if(parseInt(trim(reponse[0]))==2)
			 {
				 show_msg(trim(reponse[0]));
				 set_button_status(0, permission, 'fnc_fabric_booking',1);
				
				 reset_form('fabricbooking_1', '', 'booking_list_view', 'cbo_pay_mode,3*cbo_booking_year,2024*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_date,<?=date("d-m-Y"); ?>',"$('#details_list tr:not(:first)').remove();");
				 release_freezing();
			 }
			if(trim(reponse[0])=='approved'){
				if(reponse[2]==1 && reponse[3]=="" ) //// Issue id - 21154 
				{
					alert("This booking is approved");
					release_freezing();
					return;
				}
			}
			if(trim(reponse[0])=='pi1'){
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='PPL'){
				alert("Plan Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='sal1'){
				alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
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
			if(trim(reponse[0])=='cause'){
				alert("CAUSES OF SUPPLEMENTARY FOUND\n So Please Delete CAUSES OF SUPPLEMENTARY. ");
				release_freezing();
				return;
			}
			release_freezing();
		}
	}

	function fnc_fabric_booking_dtls( operation )
	{
		freeze_window(operation);
		if(operation==2)
		{
			//alert("Delete Restricted");
			release_freezing();
			//return;
			//var show_comment='';
		//	var r=confirm("Press  \"Cancel\"  to Cancel  Delete\nPress  \"OK\"  to Are you sure?");
			var r=confirm("Press OK to Delete Or Press Cancel");
			if(r==false){
				release_freezing();
				return;
			}
		}
		
		if(document.getElementById('id_approved_id').value==1)
		{
			alert("This booking is approved");
			release_freezing();
			return;
		}
		

		/*if('<? //echo implode('*',$_SESSION['logic_erp']['mandatory_field'][88]);?>'){
			if (form_validation('<? //echo implode('*',$_SESSION['logic_erp']['mandatory_field'][88]);?>','<? //echo implode('*',$_SESSION['logic_erp']['field_message'][88]);?>')==false)
			{
				release_freezing();
				return;
			}
		}*/
		
		if (form_validation('txt_booking_no*txt_order_no_id*txt_booking_date','Booking No*Order No*Booking Date')==false)
		{
			release_freezing();
			return;
		}
		
		var row_num=$('#details_list tr').length;
		//alert(row_num); release_freezing();  return;
		var data_all=""; var z=1;
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('cboorderid_'+i+'*cbofabricdescriptionid_'+i+'*cbogarmentscolorid_'+i+'*cbofabriccolorid_'+i+'*cbogarmentssizeid_'+i+'*txtdiawidth_'+i+'*txtfinishqnty_'+i+'*cboresponsibledept_'+i+'*txtresponsibleperson_'+i+'*txtreason_'+i,'Po No*Fabric Description*Garments Color*Fabric Color*Garments size*Dia Width*Finish Fabric*Responsible Dept*Responsible Person*Reason')==false)
			{
				release_freezing();
				return;
			}
			
			data_all+="&cboorderid_" + z + "='" + $('#cboorderid_'+i).val()+"'"+"&cbofabricdescriptionid_" + z + "='" + $('#cbofabricdescriptionid_'+i).val()+"'"+"&cbogarmentscolorid_" + z + "='" + $('#cbogarmentscolorid_'+i).val()+"'"+"&cbofabriccolorid_" + z + "='" + $('#cbofabriccolorid_'+i).val()+"'"+"&cbogarmentssizeid_" + z + "='" + $('#cbogarmentssizeid_'+i).val()+"'"+"&cboitemsizeid_" + z + "='" + $('#cboitemsizeid_'+i).val()+"'"+"&txtdiawidth_" + z + "='" + $('#txtdiawidth_'+i).val()+"'"+"&txtfinishqnty_" + z + "='" + $('#txtfinishqnty_'+i).val()+"'"+"&txtprocessloss_" + z + "='" + $('#txtprocessloss_'+i).val()+"'"+"&txtgreyqnty_" + z + "='" + $('#txtgreyqnty_'+i).val()+"'"+"&txtrate_" + z + "='" + $('#txtrate_'+i).val()+"'"+"&txtamount_" + z + "='" + $('#txtamount_'+i).val()+"'"+"&txtrmgqty_" + z + "='" + $('#txtrmgqty_'+i).val()+"'"+"&cbodivisionid_" + z + "='" + $('#cbodivisionid_'+i).val()+"'"+"&cboresponsibledept_" + z + "='" + $('#cboresponsibledept_'+i).val()+"'"+"&txtresponsibleperson_" + z + "='" + $('#txtresponsibleperson_'+i).val()+"'"+"&txtreason_" + z + "='" + $('#txtreason_'+i).val()+"'"+"&txtremarks_" + z + "='" + $('#txtremarks_'+i).val()+"'"+"&hiddencollarCuffdata_" + z + "='" + $('#hiddencollarCuffdata_'+i).val()+"'"+"&hiddencolorType_" + z + "='" + $('#hiddencolorType_'+i).val()+"'"+"&updateiddetails_" + z + "='" + $('#updateiddetails_'+i).val()+"'";
			z++;		
		}
		
		var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('txt_booking_no*update_id*txt_job_no*txt_reqsn_no*txt_reqsn_id*cbo_pay_mode',"../../")+data_all;
		
		//alert(data); release_freezing();  return;
		
		http.open("POST","requires/short_fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
	}
	 
	function fnc_fabric_booking_dtls_reponse(){
		if(http.readyState == 4){
			 var reponse=http.responseText.split('**');
			 if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1 || parseInt(trim(reponse[0]))==2){
				 show_msg(trim(reponse[0]));
				resetDtlsForm();
				 set_button_status(0, permission, 'fnc_fabric_booking_dtls',2);
				 show_list_view(reponse[1],'show_fabric_booking','booking_list_view','requires/short_fabric_booking_controller','setFilterGrid(\'list_view\',-1)');
			 }
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
			if(trim(reponse[0])=='PPL'){
				alert("Plan Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='sal1'){
				alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
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
			if(trim(reponse[0])=='cause'){
				alert("CAUSES OF SUPPLEMENTARY FOUND\n So Please Delete CAUSES OF SUPPLEMENTARY. ");
				release_freezing();
				return;
			}
			release_freezing();
		}
	}
	
	function resetDtlsForm()
	{
		$('#booking_list_view').html('');
		$('#details_list tr:not(:first)').remove();
		/*$('#cboorderid_1').val(0);
		$('#cbofabricdescriptionid_1').val(0);
		$('#cbogarmentscolorid_1').val(0);
		$('#cbofabriccolorid_1').val(0);*/
		$('#cbogarmentssizeid_1').val(0);
		$('#cboitemsizeid_1').val(0);
		$('#txtdiawidth_1').val('');
		$('#txtfinishqnty_1').val('');
		$('#txtprocessloss_1').val('');
		$('#txtgreyqnty_1').val('');
		$('#txtrate_1').val('');
		$('#txtamount_1').val('');
		$('#txtrmgqty_1').val('');
		$('#cbodivisionid_1').val(0);
		$('#cboresponsibledept_1').val(0);
		$('#txtresponsibleperson_1').val('');
		$('#txtreason_1').val('');
		$('#txtremarks_1').val('');
		$('#updateiddetails_1').val('');
		$('#hiddencollarCuffdata_1').val('');
		$('#hiddencolorType_1').val('');
	}
	
	function enable_disable(value)
	{
		/*if(value==2){
			document.getElementById('txt_rate').disabled=false;
		}
		else
		{
			document.getElementById('txt_rate').disabled=true;
		}*/
	}

	function generate_fabric_report(type,report_type)
	{
		//alert(report_type);
		if (form_validation('txt_booking_no','Booking No')==false)
		{
			return;
		}
		else
		{
			var fabric_source=$("#cbo_fabric_source").val();	
			if(type=="show_fabric_booking_report3" && fabric_source==2){

				var show_yarn_rate='';
				var r=confirm("Press  \"Cancel\"  to hide  Fabric Rate\nPress  \"OK\"  to Show Fabric Rate");
				if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";

			}else{
				var show_yarn_rate='';
				var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
				if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";
			}

			var report_title=$("div.form_caption").html(); 
			var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no*cbouom',"../../")+'&report_title='+report_title+'&show_yarn_rate='+show_yarn_rate+'&report_type='+report_type+'&path=../../';
			freeze_window(5);
			http.open("POST","requires/short_fabric_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
		}	
	}

	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4) 
		{	
			var file_data=http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
		release_freezing();
	}

	function print_report_button_setting(report_ids)
	{
		$("#print").hide();	
		$("#print_booking3").hide();	
		$("#print_booking4").hide();	
		$("#print_booking_urmi").hide();	
		$("#print_booking_ntg").hide();	
		$("#print_booking_3").hide();	
		$("#print_booking_ntg_2").hide();
		$("#print_booking_4").hide();
		$("#print_booking_5").hide();
		$("#print_booking_6").hide();
		$("#print_booking_7").hide();
		$("#print_booking_8").hide();
		$("#po_wise").hide();
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			//alert(report_id[k]);
			if(report_id[k]==8) $("#print").show();	 
			if(report_id[k]==9) $("#print_booking3").show();	 
			if(report_id[k]==10) $("#print_booking4").show();
			if(report_id[k]==45) $("#print_booking_4").show();	 
			if(report_id[k]==46) $("#print_booking_urmi").show();
			if(report_id[k]==53) $("#print_booking_5").show();	 
			if(report_id[k]==244) $("#print_booking_ntg").show();
			if(report_id[k]==244) $("#print_booking_ntg_2").show();
			if(report_id[k]==136) $("#print_booking_3").show();	  
			if(report_id[k]==72) $("#print_booking_6").show();	 
			if(report_id[k]==191) $("#print_booking_7").show();	
			if(report_id[k]==220) $("#print_booking_8").show();	
			if(report_id[k]==124) $("#po_wise").show();	 
		}
	}
    
	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/short_fabric_booking_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}

	function load_dia(i){
		
		var garmentssize_id=$('#cbogarmentssizeid_'+i).val();
		var order_id=$('#cboorderid_'+i).val();
		var fabricdescription_id = $('#cbofabricdescriptionid_'+i).val();
		var garmentscolor_id = $('#cbogarmentscolorid_'+i).val();
		var response=return_global_ajax_value( order_id+"**"+fabricdescription_id+"**"+garmentscolor_id+"**"+garmentssize_id, 'load_fabric_dia', '', 'requires/short_fabric_booking_controller');
		var response=response.split("_");
		if(response[0]==1)
		{
			$('#txtdiawidth_'+i).val(response[1]);
		}
	}
	
	function check_month_setting()
	{
		var cbo_company_name=$('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_company_name, 'check_month_maintain', '', 'requires/short_fabric_booking_controller');
		
		var response=response.split("_");
		if(response[0]==1)
		{
			$('#month_id').val(1);
			$('#booking_td').css('color','blue');	
		}
		else
		{
			$('#month_id').val(2);
			$('#booking_td').css('color','black');
			$('#cbo_booking_month').val('');		
		}
	}
	
	function validate_suplier(){
		var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
		var company=document.getElementById('cbo_company_name').value;
		var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
	}
	
	function fnc_causes(i)
	{
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		var finish_qnty=document.getElementById('txtfinishqnty_'+i).value*1;
		var grey_qnty=document.getElementById('txtgreyqnty_'+i).value*1;
		var dtls_id=document.getElementById('updateiddetails_'+i).value*1;
		if(txt_booking_no=="")
		{
			alert("Please Browse Booking.");
			return;
		}
		else if(dtls_id==0 || finish_qnty==0)
		{
			alert("Please save Finish Fabric Qty.");
			return;
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/short_fabric_booking_controller.php?action=short_booking_causes&txt_booking_no='+txt_booking_no+'&finish_qnty='+finish_qnty+'&grey_qnty='+grey_qnty+'&dtls_id='+dtls_id+'&permission='+permission, 'CAUSES OF SUPPLEMENTARY', 'width=1080px,height=400px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()//issue id--10264
			{
				var hid_finish_qnty=this.contentDoc.getElementById("hid_finish_qnty");
				$("#txtfinishqnty_"+i).val(hid_finish_qnty.value);
				//$('#txtfinishqnty_'+i).attr('disabled','disabled');
				var hid_grey_qnty=this.contentDoc.getElementById("hid_grey_qnty");
				$("#txtgreyqnty_"+i).val(hid_grey_qnty.value);
				$('#txtgreyqnty_'+i).attr('disabled','disabled');
			}
		}
	}
	
	function fncChangeYdButtonShowHide(colortype)
	{
		if(colortype==2 || colortype==3 || colortype==4 || colortype==6 || colortype==31 || colortype==32 || colortype==33 || colortype==34)
		{
			$("#btnColorType").show();
		}
		else
		{
			$("#btnColorType").hide();
		}
	}
	
	function fncChangeYdButton()
	{
		var company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		var dtls_id=document.getElementById('update_id_details').value;
		var fabric_cost_id=document.getElementById('cbo_fabricdescription_id').value;
		var garmentscolor_id=document.getElementById('cbo_garmentscolor_id').value;
		var fabriccolor_id=document.getElementById('cbo_fabriccolor_id').value;
		var grey_qnty=document.getElementById('txt_grey_qnty').value;
		var fabdesc=get_dropdown_text('cbo_fabricdescription_id');
		
		var colortype=document.getElementById('hidden_colorType').value;
		
		if(colortype==2 || colortype==3 || colortype==4 || colortype==6 || colortype==31 || colortype==32 || colortype==33 || colortype==34)
		{
			var page_link="requires/short_fabric_booking_controller.php?action=stripe_popup&txt_job_no="+trim(txt_job_no)+"&txt_booking_no="+txt_booking_no+"&dtls_id="+dtls_id+"&fabric_cost_id="+fabric_cost_id+"&fabdesc="+fabdesc+"&garmentscolor_id="+garmentscolor_id+'&fabriccolor_id='+fabriccolor_id+'&grey_qnty='+grey_qnty+'&company_name='+company_name+'&cbo_buyer_name='+cbo_buyer_name;
	
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Stripe Details", 'width=750px,height=400px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				show_list_view(txt_job_no+'_'+fabric_cost_id+'_'+cbo_color_name,'stripe_color_list_view','stripe_color_list_view_container','requires/stripe_color_measurement_controller_urmi','');
			}
		}
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
		var page_link = 'requires/short_fabric_booking_controller.php?data='+data+'&action=unapp_request_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");

			$('#txt_un_appv_request').val(unappv_request.value);
		}
	}
	
    function openmypage_refusing_cause()
	{
		if (form_validation('txt_booking_no','Booking Number')==false)
		{
			return;
		}

		var txt_booking_no=document.getElementById('txt_booking_no').value;
		var txt_refusing_cause=document.getElementById('txt_refusing_cause').value;

		var data=txt_booking_no+"_"+txt_refusing_cause;

		var title = 'Refusing Cause';
		var page_link = 'requires/short_fabric_booking_controller.php?data='+data+'&action=refusing_cause_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var refusing_cause=this.contentDoc.getElementById("hidden_appv_cause");

			$('#txt_refusing_cause').val(refusing_cause.value);
		}
	}
	
	function open_shipping_mark_popup(page_link,title){
		var shippingmark_breck_down=document.getElementById('hiddshippingmark_breck_down').value;
		var compnay_id=$("#cbo_company_name").val();
		page_link=page_link+'&compnay_id='+compnay_id+'&shippingmark_breck_down='+shippingmark_breck_down;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=230px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("hiddshippingmark_breck_down");
			if (theemail.value!=""){
				document.getElementById('hiddshippingmark_breck_down').value=theemail.value;
			}
		}
	}

	function call_print_button_for_mail(mail){
		var data = "action=ready_to_app_notification&operation=4&mail_data="+mail+"**1" + get_submitted_data_string('txt_order_no_id*update_id*cbo_company_name*cbo_buyer_name*txt_job_no*txt_booking_no*cbo_fabric_natu*cbo_fabric_source*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_booking_month*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_source*cbo_booking_year*cbo_ready_to_approved*cbo_short_booking_type*cbouom*txt_remark*cbo_supplier_location*hiddshippingmark_breck_down*cbo_shipmode*cbo_payterm*txt_tenor*cbo_provider_name',"../../");

		//freeze_window(operation);
		http.open("POST", "requires/short_fabric_booking_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =()=>{
			if (http.readyState == 4) {
				//release_freezing();
				alert(http.responseText);
			}
		}
	}

	function openmypage_mainbooking(page_link,title)
	{
		if( form_validation('cbo_company_name*txt_order_no','Company Name*Selected Order No')==false)
		{
			return;
		}
		else
		{
			var cbo_company_name=$('#cbo_company_name').val();
			var order_no_id=$('#txt_order_no_id').val();
			page_link=page_link+'&cbo_company_name='+cbo_company_name+'&order_no_id='+order_no_id;
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1190px,height=450px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_id").value;
				if (theemail!="")
				{
					freeze_window(5);
					
					var mbookingdata=theemail.split('_'); 
					$('#hidd_mainbooking_id').val(mbookingdata[0]);
					$('#txt_mainbooking_no').val(mbookingdata[1]);
					release_freezing();
				}
			}
		}
	}

	function open_colur_cuff_popup(i) {
		var txt_booking_no=document.getElementById('txt_booking_no').value;
    	var bodypartid = $('#cbofabricdescriptionid_'+i).val();
    	var bodyparttype =return_global_ajax_value( bodypartid, 'body_part_type', '', 'requires/short_fabric_booking_controller');
    	if(bodyparttype==40 || bodyparttype==50)
    	{
    		var orderid = $('#cboorderid_'+i).val();
			var colorid = $('#cbogarmentscolorid_'+i).val();
			var responsible_dept = $('#cboresponsibledept_'+i).val();
			var responsible_person = $('#txtresponsibleperson_'+i).val();
			var reason = $('#txtreason_'+i).val();
			var rate = $('#txtrate_+i').val();
    		//var update_dtls_id = $('#updateidRequiredDtl').val();
    		var collarCuff_data = $('#hiddencollarCuffdata_'+i).val();
    		var mst_id = $('#update_id').val();

	    	var page_link = 'requires/short_fabric_booking_controller.php?action=collarCuff_info_popup&bodypartid=' + bodypartid + '&bodyparttype='+bodyparttype+'&orderid='+orderid+'&colorid='+colorid+'&responsible_dept='+responsible_dept+'&responsible_person='+responsible_person+'&reason='+reason+'&rate='+rate+'&collarCuff_data='+collarCuff_data;
	    	var title = 'Collar and Cuff Measurement Info';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=670px,height=300px,center=1,resize=1,scrolling=0', '../');
	    	emailwindow.onclose = function () {
	    		var theform = this.contentDoc.forms[0];
	    		var hidden_collarCuff_data = this.contentDoc.getElementById("hidden_collarCuff_data").value;

	    		$('#hiddencollarCuffdata_'+i).val(hidden_collarCuff_data);
	    	}

	    	/* emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=570px,height=300px,center=1,resize=1,scrolling=0', '../');
	    	emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];;
				var id=this.contentDoc.getElementById("txt_booking_no");
				if (id.value!="")
				{
					freeze_window(5);
					resetDtlsForm();
					set_button_status(0, permission, 'fnc_fabric_booking_dtls',1);
					show_list_view(txt_booking_no,'show_fabric_booking','booking_list_view','requires/short_fabric_booking_controller','setFilterGrid(\'list_view\',-1)');
					release_freezing();
				}
			} */
    	}
    	else{
    		return;
    	}
    }

	function dtm_popup(page_link,title)
	{
		var job_no=$('#txt_job_no').val();
		var booking_no=$('#txt_booking_no').val();
		var selected_no=$('#txt_order_no_id').val();
	
		if(booking_no=='')
		{
			alert('Booking  Not Found.');
			$('#txt_booking_no').focus();
			return;
		}
	
		page_link=page_link+'&job_no='+job_no+'&booking_no='+booking_no+'&selected_no='+selected_no;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=1,scrolling=0','../')
	}
	
	function fnc_openmypage_requisition(page_link,title)
	{
		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var page_link=page_link+'&company_id='+cbo_company_name+'&buyer_id='+cbo_buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=455px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_booking").value;
			//alert(theemail)
			if(theemail!=""){
				var exdata=theemail.split("_");
				$("#txt_reqsn_no").val( exdata[0] );
				$("#txt_reqsn_id").val( exdata[1] );
				
				//reset_form('trimsbooking_1','booking_list_view','id_approved_id','txt_booking_date,<? echo date("d-m-Y"); ?>','','cbo_company_name*cbo_buyer_name*cbo_basis_id');
				
				get_php_form_data( exdata[0], "populate_data_from_search_popup_requisition", "requires/short_fabric_booking_controller");
				
				$('#cbo_company_name').attr('disabled',true);
				$('#cbo_buyer_name').attr('disabled',true);
				
				var reportId=document.getElementById('report_ids').value;
				
				print_report_button_setting(reportId);
				
				var txt_order_no_id=document.getElementById('txt_order_no_id').value;
				var cbo_fabric_natu =document.getElementById('cbo_fabric_natu').value;
				var cbo_fabric_source=document.getElementById('cbo_fabric_source').value;
				var cbouom=document.getElementById('cbouom').value;
				
				//fnc_get_po_config(txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom+'_1');
				var data=exdata[1]+'_'+txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom+'_1';
				
				var list_view_orders = return_global_ajax_value( data, 'load_php_reqdtls_form', '', 'requires/short_fabric_booking_controller');
				if(list_view_orders!='')
				{
					$("#details_list tr").remove();
					$("#details_list").append(list_view_orders);
				}
			}
		}
	}
	
	function fnc_get_buyer_config(buyer_id)
	{
		get_php_form_data(buyer_id+'*'+1,'get_buyer_config','requires/short_fabric_booking_controller' );
	}
	

</script>
</head>
<body onLoad="set_hotkey(); check_exchange_rate(); check_month_setting();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="fabricbooking_1" autocomplete="off" id="fabricbooking_1">
        <fieldset style="width:1280px;">
        <legend>Short Fabric Booking</legend>
            <table width="1280" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td colspan="5" align="right" class="must_entry_caption"><b>BOOKING NO</b></td>
                    <td colspan="5">
                        <input class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_booking('requires/short_fabric_booking_controller.php?action=fabric_booking_popup','fabric Booking Search');" readonly placeholder="Browse" name="txt_booking_no" id="txt_booking_no"/>
                        <input type="hidden" id="id_approved_id"> 
                        <input type="hidden" id="update_id">
                        <input type="hidden" id="month_id" class="text_boxes"  style="width:20px" >
                        <input type="hidden" id="txt_reqsn_id"/> 
                    </td>
                </tr>
                <tr>
                    <td width="100" class="must_entry_caption">Company Name</td>
                    <td width="150"><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/short_fabric_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/short_fabric_booking_controller',this.value, 'load_drop_down_department', 'department_td' ); load_drop_down( 'requires/short_fabric_booking_controller',this.value, 'load_drop_down_profit_center', 'profit_center_td'); check_month_setting(); validate_suplier(); check_exchange_rate(); ",0,"" ); //load_drop_down( 'requires/short_fabric_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/short_fabric_booking_controller',this.value', 'load_drop_down_department', 'department_td' ); load_drop_down( 'requires/short_fabric_booking_controller',this.value', 'load_drop_down_profit_center', 'profit_center_td'); check_month_setting(); validate_suplier(); check_exchange_rate();?>
                        <input type="hidden" id="report_ids">	  
                    </td>
                    <td width="100">Buyer Name</td>   
                    <td width="150" id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                    <td width="100">Brand</td>
                    <td width="150" id="brand_td"><? echo create_drop_down( "cbo_brand_id", 130, $blank_array,'', 1, "--Brand--",$selected, "" ); ?></td>
                    <td width="100">Season &nbsp;<?=create_drop_down("cbo_season_year",50,create_year_array(),"",1,"-Year-", $selected, "" ); ?></td>
                    <td width="150" id="season_td"><? echo create_drop_down( "cbo_season_id", 130, $blank_array,'', 1, "-- Select Season--",$selected, "" ); ?>
                    
                    <td width="100" id="booking_td">Booking Month</td>   
                    <td><?=create_drop_down( "cbo_booking_month", 80, $months,"", 1, "-- Select --", "", "",0 );
						echo create_drop_down( "cbo_booking_year", 50, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?>
                    </td>
                </tr>
                <tr>
                	<td class="must_entry_caption">Booking Date</td>
                    <td><input class="datepicker" type="text" style="width:120px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled /></td>
                    <td class="must_entry_caption">Delivery Date</td>
                    <td><input class="datepicker" type="text" style="width:120px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                    <td>Currency</td>
                    <td><? echo create_drop_down( "cbo_currency", 130, $currency,"", 1, "-- Select --", 2, "check_exchange_rate();",0 ); ?></td>
                    <td class="must_entry_caption">Exchange Rate</td>
                    <td><input style="width:120px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  /></td>
                    <td>Source</td>
                    <td><? echo create_drop_down( "cbo_source", 130, $source,"", 1, "-- Select Source --", "", "","" ); ?></td>
                </tr>
                <tr>   
                	<td class="must_entry_caption">Fabric Nature</td>
                    <td>
						<? 
                        echo create_drop_down( "cbo_fabric_natu", 80, $item_category,"", 1, "-- Select --", 1,$onchange_func, $is_disabled, "2,3");
                        echo create_drop_down( "cbouom", 50, $unit_of_measurement,'', 1, '-Uom-', $row[csf('uom')], "",$disabled,"1,12,23,27" );
                        ?>	
                    </td>
                    <td class="must_entry_caption">Fabric Source</td>
                    <td><? echo create_drop_down( "cbo_fabric_source", 130, $fabric_source,"", 1, "-- Select --", "","enable_disable(this.value);", "", ""); ?></td>
                    
                	<td class="must_entry_caption">Requisition No</td>
                    <td><input name="txt_reqsn_no" id="txt_reqsn_no" class="text_boxes" type="text" style="width:120px" onDblClick="fnc_openmypage_requisition( 'requires/short_fabric_booking_controller.php?action=requisition_popup','Short Fabric Requisition Search');" readonly placeholder="Browse" /></td>
                    <td class="must_entry_caption">Selected PO No</td>   
                    <td colspan="3"><input class="text_boxes" type="text" style="width:372px;" placeholder="Double click for Order" onDblClick="openmypage_order( 'requires/short_fabric_booking_controller.php?action=order_search_popup','Order Search');" name="txt_order_no" id="txt_order_no"/>
                    	<input class="text_boxes" type="hidden" style="width:172px;" name="txt_order_no_id" id="txt_order_no_id"/>
                    </td> 
                </tr>
                <tr>
                    <td>Job No.</td>
                    <td><input style="width:120px;" type="text" class="text_boxes"  name="txt_job_no" id="txt_job_no" disabled /></td> 
                    <td class="must_entry_caption">Pay Mode</td>
                    <td><?=create_drop_down( "cbo_pay_mode", 130, $pay_mode,"", 1, "-- Select Pay Mode --", 5, "load_drop_down( 'requires/short_fabric_booking_controller', this.value, 'load_drop_down_suplier','sup_td'); load_drop_down( 'requires/short_fabric_booking_controller', this.value, 'load_drop_down_short_provider', 'prov_td');","","1,2,3,5" ); ?></td>
                    <td class="must_entry_caption">Supplier Name</td>
                    <td id="sup_td"><? echo create_drop_down( "cbo_supplier_name", 130, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/short_fabric_booking_controller'); load_drop_down( 'requires/short_fabric_booking_controller', this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_location', 'sup_location_td');",0 ); ?> 
                    </td> 
                    <td>Supplier Location</td>
                    <td id="sup_location_td"><? echo create_drop_down( "cbo_supplier_location", 130, $blank_array,"", 1, "-- Select Supp Location --", "", "","" ); ?></td>
                    <td>Ship Mode</td>
                    <td><?=create_drop_down( "cbo_shipmode", 130, $shipment_mode,"", 1, "--Select--", 0, "","","" ); ?></td> 
                </tr>
                <tr>
                	<td>Tenor</td>
                    <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
                	<td>Pay Term</td>
                    <td><?=create_drop_down( "cbo_payterm", 130, $pay_term,"", 1, "--Select--", 0, "","","" ); ?></td>   
                    <td>Internal Ref No</td>  
                    <td><Input name="txt_intarnal_ref" class="text_boxes" readonly placeholder="Display" ID="txt_intarnal_ref" style="width:120px" ></td>
                    <td>File no</td>  
                    <td><Input name="txt_file_no" class="text_boxes" readonly placeholder="Display" ID="txt_file_no" style="width:120px" ></td>
                    <td>Short Booking Type</td>  
                    <td><?=create_drop_down( "cbo_short_booking_type", 130, $short_booking_type,"", 1, "-- Select--", "", "","","" ); ?></td>
                </tr>
                <tr>
                	<td>Main Booking No</td>
                	<td><input class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_mainbooking('requires/short_fabric_booking_controller.php?action=main_booking_popup','Main Fabric Booking Search');" readonly placeholder="Browse for Main Booking" name="txt_mainbooking_no" id="txt_mainbooking_no"/>
                    <input type="hidden" id="hidd_mainbooking_id">
                    </td>
					<td>Profit Center</td>
                        <td>
                            <input type="text" onDblClick="openmypage_profit_center();" class="text_boxes" name="cbo_profit_center" id="cbo_profit_center" readonly style="width:120px;" placeholder="Browse">
                            <input type="hidden" name="txt_profit_center_hidden_id" id="txt_profit_center_hidden_id" class="text_boxes" value="" style="width:150px;"/>
                            <?
                            // echo create_drop_down("cbo_profit_center", 190, [], "", 1, "-- Select Profit Center --", $selected, 0);
                            ?>
                        </td>
                	<!-- <td>Profit Center</td>
					<td>
						<input type="text" name="txt_profit_center" id="txt_profit_center" class="text_boxes" style="width:120px;" placeholder="Browse" onDblClick="profit_center_pop();" />
						<input type="hidden" name="txt_profit_id" id="txt_profit_id" value="" />
						<input type="hidden" name="txt_profit_seq" id="txt_profit_seq" value="" />
					</td> -->
                    <td>Department</td> 
					<td>
						<input type="text" name="txt_department_no" id="txt_department_no" class="text_boxes" style="width:120px" placeholder="Browse" onChange="fnRemoveHidden('hidd_department_id');" onDblClick="openmypage_department();" />
						<input type="hidden" id="hidd_department_id" name="hidd_department_id" style="width:40px" />
					</td> 
                    <td>Final Comments</td>
                	<td><input class="text_boxes" type="text" maxlength="150" style="width:120px"  name="txt_final_comment" id="txt_final_comment" placeholder="Write"  />
					<input type="hidden" readonly id="txt_update_type">
					<td>Short Provide Com</td>
                    <td id="prov_td"><? echo create_drop_down( "cbo_provider_name", 130, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Short Provider --", $selected, "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/short_fabric_booking_controller');load_drop_down( 'requires/short_fabric_booking_controller',this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_location', 'sup_location_td' )",0 ); ?> 
                    </td>
				</tr>
                <tr>
                	<td>Attention</td>   
                    <td colspan="3">
                        <input class="text_boxes" type="text" style="width:372px;"  name="txt_attention" id="txt_attention" />
                        <input type="hidden" class="image_uploader" style="width:150px" value="Lab DIP No" onClick="openmypage( 'requires/short_fabric_booking_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">
                    </td>
                	<td>Remarks</td>   
                    <td colspan="3"><input class="text_boxes" type="text" maxlength="600" style="width:372px;"  name="txt_remark" id="txt_remark"/></td>
                    <td colspan="2" align="center">
						<? 
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(88,'txt_booking_no','../../');
                        ?>
                    </td>
                </tr>
                <tr>
                	<td>Ready To Appr.</td>  
                    <td><? echo create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
                    <td>Un-approve request</td>
                    <td><Input name="txt_un_appv_request" class="text_boxes" readonly placeholder="Browse" ID="txt_un_appv_request" style="width:120px" onClick="openmypage_unapprove_request();"></td>
                    <td>Refusing Cause</td>
                	<td ><input class="text_boxes" type="text" maxlength="150" style="width:120px"  name="txt_refusing_cause" id="txt_refusing_cause"  readonly placeholder="Browse" onClick="openmypage_refusing_cause();"/></td>
                	
                	<td align="center">
                        <input type="button" id="btnshippingmark" class="image_uploader" style="width:80px;" value="Shipping Mark" onClick="open_shipping_mark_popup('requires/short_fabric_booking_controller.php?action=shipping_mark_popup','Shipping Mark');" />
                        <input style="width:40px;" type="hidden" class="text_boxes" name="hiddshippingmark_breck_down" id="hiddshippingmark_breck_down" />
                    </td>
					<td align="center">
						<input type="button" id="set_button" class="image_uploader" style="width:100px;" value="Trims Dye To Match" onClick="dtm_popup('requires/short_fabric_booking_controller.php?action=dtm_popup','DTM');"  />
					</td>
                    <td><input type="button" class="image_uploader" style="width:100px" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'short_fab_booking', 2 ,1)"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" colspan="10" valign="top" id="app_sms2" style="font-size:18px; color:#F00"></td>
                </tr>
                <tr>
                    <td align="center" colspan="10" valign="middle" class="button_container">
                    <? $date=date('d-m-Y'); echo load_submit_buttons( $permission, "fnc_fabric_booking", 0,0 ,"reset_form('fabricbooking_1','','booking_list_view','cbo_pay_mode,3*cbo_booking_year,2024*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_date,".$date."')",1) ; ?>
					<input class="formbutton" type="button" onClick="fnSendMail('../../','',1,1,0,1)" value="Mail Send" style="width:80px;">
					<input type="button" value="Po Wise" onClick="generate_fabric_report('po_wise',1)" style="width:70px;display:none;" name="po_wise" id="po_wise" class="formbutton" />
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    <br>
    <form name="orderdetailsentry_2" autocomplete="off" id="orderdetailsentry_2">
        <fieldset style="width:1600px;">
        <legend>Details</legend>
        	<table width="1600" cellspacing="2" cellpadding="0" border="1" class="rpt_table" rules="all">
            	<thead>
                	<th width="20">SL</th>
                    <th width="80" class="must_entry_caption">PO No</th>
                    <th width="200" class="must_entry_caption">Fabric Description</th>
                    <th width="80" class="must_entry_caption">GMTS Color</th>
                    <th width="80">Fab.Color</th>
                    <th width="60" class="must_entry_caption">GMTS Size</th>
                    <th width="60">Item Size</th>
                    <th width="60" class="must_entry_caption">Dia/ Width</th>
                    <th width="60" class="must_entry_caption">Fin Fab Qty</th>
                    <th width="50">Process Loss %</th>
                    <th width="60">Gray Fab Qty</th>
                    <th width="50">Rate</th>
                    <th width="60">Amount</th>
                    <th width="60">RMG Qty</th>
                    <th width="70">Division</th>
                    <th width="70" class="must_entry_caption">Respon. Dept.</th>
                    <th width="70" class="must_entry_caption">Respon. Person</th>
                    <th width="70" class="must_entry_caption">Reason</th>
                    <th width="70">Remarks</th>
                    <th width="100">CAUSE OF SUPPL</th>
                    <th width="90">Collar & Cuff</th>
                    <th>&nbsp;</th>
                </thead>
                <tbody id="details_list">
                	<tr id="trid_1">
                    	<td>
                        	<input name="txtsl_1" id="txtsl_1" class="text_boxes_numeric" type="text" style="width:10px" value="1" readonly disabled/>
                        	<input type="hidden" id="updateiddetails_1">
                        </td>
                        <td id="orderdropdowntd_1"><?=create_drop_down("cboorderid_1", 80, $blank_array,"", 1, "-Select-", $selected, "" ); ?></td>
                        <td id="fabricdescriptionidtd_1"><?=create_drop_down("cbofabricdescriptionid_1", 200, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                        <td id="garmentscoloridtd_1"><?=create_drop_down("cbogarmentscolorid_1", 80, $blank_array,"", 1, "-Select-", $selected, "" ); ?></td>
                        <td id="fabriccoloridtd_1"><?=create_drop_down("cbofabriccolorid_1", 80, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                        <td id="garmentssizeidtd_1"><?=create_drop_down( "cbogarmentssizeid_1", 60, $blank_array,"", 1, "-Select-", $selected, "" ); ?></td>
                        <td id="itemsizeidtd_1"><?=create_drop_down( "cboitemsizeid_1", 60, $blank_array,"", 1, "-Select-", $selected, "" ); ?></td>
                        <td><input name="txtdiawidth_1" id="txtdiawidth_1" class="text_boxes" type="text" placeholder="Write" style="width:50px "/></td>
                        <td><input name="txtfinishqnty_1" id="txtfinishqnty_1" class="text_boxes_numeric" type="text" onChange="calculate_requirement(1);" style="width:50px"/></td>
                        <td><input name="txtprocessloss_1" id="txtprocessloss_1" class="text_boxes_numeric" type="text" onChange="calculate_requirement(1);" style="width:40px"/></td>
                        <td><input name="txtgreyqnty_1" id="txtgreyqnty_1" class="text_boxes_numeric" type="text" style="width:50px" readonly/></td>
                        <td><input name="txtrate_1" id="txtrate_1" class="text_boxes_numeric" type="text" onChange="calculate_requirement(1);" style="width:40px" /></td>
                        <td><input name="txtamount_1" id="txtamount_1" class="text_boxes_numeric" type="text" style="width:50px" readonly/></td>
                        <td><input name="txtrmgqty_1" id="txtrmgqty_1" class="text_boxes_numeric" type="text" style="width:50px" /></td>
                        <td><?=create_drop_down("cbodivisionid_1", 70, $short_division_array,"", 1, "--Select--", $selected, "" ); ?></td>
                        <td><?=create_drop_down( "cboresponsibledept_1", 70,"select id, department_name from  lib_department where status_active=1 and is_deleted=0 order by  department_name", "id,department_name", 0, "", '', '', '',''); ?></td>
                        <td><input name="txtresponsibleperson_1" id="txtresponsibleperson_1" class="text_boxes" type="text" style="width:60px" /></td>
                        <td><input name="txtreason_1" id="txtreason_1" class="text_boxes" type="text" style="width:60px"/></td>
                        <td><input name="txtremarks_1" id="txtremarks_1" class="text_boxes" type="text" style="width:60px"/></td>
                        <td><input type="button" id="btncauses_1" class="image_uploader" style="width:100px" value="CAUSE OF SUPPL" onClick="fnc_causes(1);"/></td>
                        <td>
                        	<input type="button" id="btncollarcuff_1" class="image_uploader" style="width:90px;" value="Collar & Cuff" onClick="open_colur_cuff_popup(1);" />
                            <input type="hidden" name="hiddencollarCuffdata_1" id="hiddencollarCuffdata_1" value="">
                        </td>
                        <td>
                            <input type="button" id="btnColorType_1" value="STRIPE YD" width="100" class="image_uploader" onClick="fncChangeYdButton(1);" style="display:none"/>
                            <input type="hidden" width="100" id="hiddencolorType_1">
                    	</td>
                        
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td align="center" colspan="22" valign="middle" class="button_container">
                            <?=load_submit_buttons( $permission, "fnc_fabric_booking_dtls", 0,0 ,"reset_form('','','','','$('#details_list tr:not(:first)').remove();')",2) ; ?>
                            <input type="button" value="Print B1" onClick="generate_fabric_report('show_fabric_booking_report',1)"  style="width:70px; display:none;" name="print" id="print" class="formbutton" />
                            <input type="button"  value="Print B2" onClick="generate_fabric_report('show_fabric_booking_report3',1)"  style="width:70px; display:none;" name="print_booking3" id="print_booking3" class="formbutton" /> 
                            <input type="button"  value="F.Booking" onClick="generate_fabric_report('show_fabric_booking_report4',1)"  style="width:70px; display:none;" name="print_booking4" id="print_booking4" class="formbutton" />
                            <input type="button"  value="Print Urmi" onClick="generate_fabric_report('show_fabric_booking_report_urmi',1)"  style="width:70px; display:none;" name="print_booking_urmi" id="print_booking_urmi" class="formbutton" />
                             <input type="button"  value="Fabric NTG" onClick="generate_fabric_report('show_fabric_booking_report_ntg',1)"  style="width:70px; display:none;" name="print_booking_ntg" id="print_booking_ntg" class="formbutton" />                        
                            <input type="button" value="Print B3" onClick="generate_fabric_report('print_booking_3',1)"  style="width:70px;display:none;" name="print_booking_3" id="print_booking_3" class="formbutton" />
                            <input type="button" value="NTG" onClick="generate_fabric_report('print_booking_ntg',1)"  style="width:70px;display:none;"  name="print_booking_ntg_2" id="print_booking_ntg_2" class="formbutton" />
                            <input type="button" value="Print B4" onClick="generate_fabric_report('print_booking_4',1)" style="width:70px;display:none;" name="print_booking_4" id="print_booking_4" class="formbutton" />
                            <input type="button" value="Print B5" onClick="generate_fabric_report('print_booking_5',1)" style="width:70px;display:none;" name="print_booking_5" id="print_booking_5" class="formbutton" />
                            <input type="button" value="Print B6" onClick="generate_fabric_report('print_booking_6',1)" style="width:70px;display:none;" name="print_booking_6" id="print_booking_6" class="formbutton" />
                            <input type="button" value="Print B7" onClick="generate_fabric_report('print_booking_7',1)" style="width:70px;display:none;" name="print_booking_7" id="print_booking_7" class="formbutton" />
                            <input type="button" value="Print B8" onClick="generate_fabric_report('print_booking_8',1)"  style="width:70px;display:none;" name="print_booking_8" id="print_booking_8" class="formbutton" />
                            
                            <div id="pdf_file_name"></div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </form>
    <br>
    <fieldset style="width:1280px;">
        <legend>List View</legend>
        <table style="border:none" cellpadding="0" cellspacing="2" border="0">
            <tr align="center">
            	<td colspan="12" id="booking_list_view"></td>	
            </tr>
        </table>
    </fieldset>
    </div>
    <div style="display:none" id="data_panel"></div>
</body>
<script>
	//set_multiselect('cboresponsibledept_1','0','0','','0');
	//set_multiselect('cboresponsibledept_1','0','1','<?=$department_id;?>','0');
	$( document ).ready(function() {
load_drop_down( 'requires/short_fabric_booking_controller', document.getElementById('cbo_pay_mode').value, 'load_drop_down_suplier', 'sup_td')
load_drop_down( 'requires/short_fabric_booking_controller', document.getElementById('cbo_pay_mode').value, 'load_drop_down_short_provider', 'prov_td')
});
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
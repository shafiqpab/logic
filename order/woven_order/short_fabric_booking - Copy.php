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
					reset_form('orderdetailsentry_2','booking_list_view','','','','');
					set_multiselect('cbo_responsible_dept','0','1','<?=$department_id;?>','0');
					document.getElementById('txt_order_no_id').value=id.value;
					document.getElementById('txt_order_no').value=po.value;
					var cbo_fabric_natu =document.getElementById('cbo_fabric_natu').value
					var cbo_fabric_source=document.getElementById('cbo_fabric_source').value
					var cbouom=document.getElementById('cbouom').value
					var fabricdescription_id=$('#cbo_fabricdescription_id').val();
					get_php_form_data( id.value, "populate_order_data_from_search_popup", "requires/short_fabric_booking_controller" );
					check_month_setting();
					var reportId=document.getElementById('report_ids').value;
					print_report_button_setting(reportId);
					load_drop_down( 'requires/short_fabric_booking_controller', id.value, 'load_drop_down_po_number', 'order_drop_down_td');
					
					//load_drop_down( 'requires/short_fabric_booking_controller', id.value+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom, 'load_drop_down_fabric_description', 'fabricdescription_id_td' )
					//load_drop_down( 'requires/short_fabric_booking_controller', id.value+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom, 'load_drop_down_fabric_color', 'fabriccolor_id_id_td' )
					//load_drop_down( 'requires/short_fabric_booking_controller', id.value+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom+'_'+fabricdescription_id, 'load_drop_down_gmts_color', 'garmentscolor_id_id_td' )
					
					//load_drop_down( 'requires/short_fabric_booking_controller', id.value+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom, 'load_drop_down_item_size', 'itemsize_id_td' )
					//load_drop_down( 'requires/short_fabric_booking_controller', id.value+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom, 'load_drop_down_gmts_size', 'garmentssize_id_td' )
					release_freezing();
					fnc_generate_booking()
				}
			}
		}
	}

	function set_process_loss(str)
	{
		var prosess_loss=return_global_ajax_value(str, 'prosess_loss_set', '', 'requires/short_fabric_booking_controller');
		document.getElementById('txt_process_loss').value=trim(prosess_loss);
		calculate_requirement();
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
				reset_form('orderdetailsentry_2','booking_list_view','','','')
				var txt_order_no_id=document.getElementById('txt_order_no_id').value
				load_drop_down( 'requires/short_fabric_booking_controller', txt_order_no_id, 'load_drop_down_po_number', 'order_drop_down_td' )
				//load_drop_down( 'requires/short_fabric_booking_controller', txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom, 'load_drop_down_fabric_description', 'fabricdescription_id_td' )
				//load_drop_down( 'requires/short_fabric_booking_controller', txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom, 'load_drop_down_fabric_color', 'fabriccolor_id_id_td' )
				//load_drop_down( 'requires/short_fabric_booking_controller', txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom, 'load_drop_down_gmts_color', 'garmentscolor_id_id_td' )
				//load_drop_down( 'requires/short_fabric_booking_controller', txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom, 'load_drop_down_item_size', 'itemsize_id_td' )
				//load_drop_down( 'requires/short_fabric_booking_controller', txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom, 'load_drop_down_gmts_size', 'garmentssize_id_td' )
				show_list_view(theemail.value,'show_fabric_booking','booking_list_view','requires/short_fabric_booking_controller','setFilterGrid(\'list_view\',-1)');
				set_button_status(1, permission, 'fnc_fabric_booking',1);
				release_freezing();
			}
		}
	}

	function calculate_requirement()
	{
		var cbo_company_name= document.getElementById('cbo_company_name').value;
		var cbo_fabric_natu= document.getElementById('cbo_fabric_natu').value
		var process_loss_method_id=return_global_ajax_value(cbo_company_name+'_'+cbo_fabric_natu, 'process_loss_method_id', '', 'requires/short_fabric_booking_controller');
		var txt_finish_qnty=(document.getElementById('txt_finish_qnty').value)*1;
		var processloss=(document.getElementById('txt_process_loss').value)*1;
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
		document.getElementById('txt_grey_qnty').value= WastageQty;
		document.getElementById('txt_amount').value=number_format_common((document.getElementById('txt_rate').value)*1*WastageQty,5,0);
	}

	function fnc_fabric_booking( operation )
	{
		/*if(operation==2)
		{
			alert("Delete Restricted")
			return;
		}*/
		var profit_center=$('#cbo_profit_center').val()*1;
		var department=$('#cbo_department').val()*1;
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
				return;
			}
			
		}
		var month_set_id=$('#month_id').val();
		if(month_set_id==1)
		{
			if (form_validation('cbo_booking_month','Booking Month')==false)
			{
				return;
			}	
		}
		
		var delivery_date=$('#txt_delivery_date').val();
		
		if(date_compare($('#txt_booking_date').val(), delivery_date)==false)
		{
			alert("Delivery Date Not Allowed Less than Booking Date");
			return;
		}

		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][88]);?>'){
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][88]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][88]);?>')==false)
			{
				return;
			}
		}
		
		
		if (form_validation('cbo_buyer_name*txt_order_no_id*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_supplier_name*txt_exchange_rate','Buyer*Order No*Booking Date*Delivery Date*Pay Mode*Supplier*Exchange Rate')==false)
		{
			return;
		}	
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_order_no_id*update_id*cbo_company_name*cbo_buyer_name*txt_job_no*txt_booking_no*cbo_fabric_natu*cbo_fabric_source*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_booking_month*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_source*cbo_booking_year*cbo_ready_to_approved*cbo_short_booking_type*cbouom*txt_remark*cbo_supplier_location*hiddshippingmark_breck_down*cbo_shipmode*cbo_payterm*txt_tenor*hidd_mainbooking_id*txt_mainbooking_no*cbo_department*cbo_profit_center*txt_final_comment*txt_update_type*cbo_provider_name',"../../");
			freeze_window(operation);
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
				
				 reset_form('fabricbooking_1*orderdetailsentry_2','','booking_list_view','cbo_pay_mode,3*cbo_booking_year,2014*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_date,<? echo date("d-m-Y"); ?>')
				 release_freezing();
			 }
			if(trim(reponse[0])=='approved'){
			//	var approv_check_omit="";

			//if(reponse[3])   approv_check_omit="1";
		 
				
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
		if(operation==2)
		{
			//alert("Delete Restricted")
			//return;
			//var show_comment='';
		//	var r=confirm("Press  \"Cancel\"  to Cancel  Delete\nPress  \"OK\"  to Are you sure?");
			var r=confirm("Press OK to Delete Or Press Cancel");
			if(r==false){
				return;
			}
		}
		
		if(document.getElementById('id_approved_id').value==1)
		{
			alert("This booking is approved")
			return;
		}
		if(document.getElementById('cbo_order_id').value==0)
		{
			alert("Select Po No")
			return;
		}

		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][88]);?>'){
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][88]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][88]);?>')==false)
			{
				return;
			}
		}

		if (form_validation('txt_order_no_id*txt_booking_date*txt_booking_no*cbo_order_id*cbo_fabricdescription_id*cbo_garmentscolor_id*cbo_fabriccolor_id*txt_dia_width*cbo_responsible_dept*cbo_responsible_person*txt_reason','Order No*Booking Date*Booking No*Po No*Fabric Description*Garments Color*Fabric Color*Garments size*Dia Width*Responsible Dept*Responsible Person*Reason')==false)
		{
			return;
		}		

		var data="action=save_update_delete_dtls&operation="+operation+get_submitted_data_string('txt_booking_no*update_id*txt_job_no*cbo_order_id*cbo_fabricdescription_id*cbo_fabriccolor_id*cbo_garmentscolor_id*cbo_itemsize_id*cbo_garmentssize_id*txt_dia_width*txt_finish_qnty*txt_process_loss*txt_grey_qnty*txt_rate*txt_amount*txt_rmg_qty*cbo_responsible_dept*cbo_responsible_person*txt_reason*update_id_details*cbo_pay_mode*cbo_division_id',"../../");
		freeze_window(operation);
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
				 reset_form('orderdetailsentry_2','booking_list_view','','','','cbo_order_id*cbo_fabricdescription_id*cbo_fabriccolor_id*cbo_garmentscolor_id')
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

	function open_terms_condition_popup(page_link,title)
	{
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		if (txt_booking_no=="")
		{
			alert("Save The Booking First")
			return;
		}	
		else
		{
			page_link=page_link+get_submitted_data_string('txt_booking_no','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
			}
		}
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

	function load_dia(garmentssize_id){
		var order_id=$('#cbo_order_id').val();
		var fabricdescription_id = $('#cbo_fabricdescription_id').val();
		var garmentscolor_id = $('#cbo_garmentscolor_id').val();
		//var garmentssize_id = $('#cbo_garmentssize_id').val();
		var response=return_global_ajax_value( order_id+"**"+fabricdescription_id+"**"+garmentscolor_id+"**"+garmentssize_id, 'load_fabric_dia', '', 'requires/short_fabric_booking_controller');
		var response=response.split("_");
		if(response[0]==1)
		{
			$('#txt_dia_width').val(response[1]);
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
	
	function fnc_causes()
	{
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		var finish_qnty=document.getElementById('txt_finish_qnty').value*1;
		var grey_qnty=document.getElementById('txt_grey_qnty').value*1;
		var dtls_id=document.getElementById('update_id_details').value*1;
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
				$("#txt_finish_qnty").val(hid_finish_qnty.value);
				//$('#txt_finish_qnty').attr('disabled','disabled');
				var hid_grey_qnty=this.contentDoc.getElementById("hid_grey_qnty");
				$("#txt_grey_qnty").val(hid_grey_qnty.value);
				$('#txt_grey_qnty').attr('disabled','disabled');
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

</script>
</head>
<body onLoad="set_hotkey(); check_exchange_rate(); check_month_setting();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="fabricbooking_1" autocomplete="off" id="fabricbooking_1">
        <fieldset style="width:1050px;">
        <legend>Master</legend>
            <table width="1050" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td colspan="4" align="right">Booking No</td>
                    <td colspan="4">
                        <input class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_booking('requires/short_fabric_booking_controller.php?action=fabric_booking_popup','fabric Booking Search');" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                        <input type="hidden" id="id_approved_id"> 
                        <input type="hidden" id="update_id">
                        <input type="hidden" id="month_id" class="text_boxes"  style="width:20px" >
                    </td>
                </tr>
                <tr>
                    <td width="120">Company Name</td>
                    <td width="140"><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/short_fabric_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/short_fabric_booking_controller',this.value', 'load_drop_down_department', 'department_td' );load_drop_down( 'requires/short_fabric_booking_controller',this.value', 'load_drop_down_profit_center', 'profit_center_td' ); check_month_setting(); validate_suplier(); check_exchange_rate();",0,"" ); ?>
                        <input type="hidden" id="report_ids">	  
                    </td>
                    <td width="120">Buyer Name</td>   
                    <td width="140" id="buyer_td"> 
                    <? echo create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,"" ); ?>
                    </td>
                    <td width="120">Job No.</td>
                    <td width="140"><input style="width:120px;" type="text" class="text_boxes"  name="txt_job_no" id="txt_job_no" disabled /></td>
                    <td width="120" id="booking_td">Booking Month</td>   
                    <td><?=create_drop_down( "cbo_booking_month", 80, $months,"", 1, "-- Select --", "", "",0 );
						echo create_drop_down( "cbo_booking_year", 50, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?>
                    </td>
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
                    <td class="must_entry_caption">Selected Order No</td>   
                    <td colspan="3"><input class="text_boxes" type="text" style="width:382px;" placeholder="Double click for Order"  onDblClick="openmypage_order('requires/short_fabric_booking_controller.php?action=order_search_popup','Order Search')" name="txt_order_no" id="txt_order_no"/>
                    	<input class="text_boxes" type="hidden" style="width:172px;"  name="txt_order_no_id" id="txt_order_no_id"/>
                    </td>  
                </tr>
                <tr>
                    <td class="must_entry_caption">Booking Date</td>
                    <td><input class="datepicker" type="text" style="width:120px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled /></td>
                     <td>Currency</td>
                    <td><? echo create_drop_down( "cbo_currency", 130, $currency,"", 1, "-- Select --", 2, "check_exchange_rate();",0 ); ?></td>
                    <td class="must_entry_caption">Exchange Rate</td>
                    <td><input style="width:120px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  /></td>
                    <td>Source</td>
                    <td><? echo create_drop_down( "cbo_source", 130, $source,"", 1, "-- Select Source --", "", "","" ); ?></td> 
                                              
                </tr>
                <tr>
                    <td class="must_entry_caption">Delivery Date</td>
                    <td><input class="datepicker" type="text" style="width:120px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                    <td class="must_entry_caption">Pay Mode</td>
                    <td><? echo create_drop_down( "cbo_pay_mode", 130, $pay_mode,"", 1, "-- Select Pay Mode --", 5, "load_drop_down( 'requires/short_fabric_booking_controller', this.value, 'load_drop_down_suplier';load_drop_down( 'requires/short_fabric_booking_controller', this.value, 'load_drop_down_short_provider', 'prov_td' )","","1,2,3,5" ); ?> 
                    </td>
                    <td class="must_entry_caption">Supplier Name</td>
                    <td id="sup_td"><? echo create_drop_down( "cbo_supplier_name", 130, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/short_fabric_booking_controller');load_drop_down( 'requires/short_fabric_booking_controller',this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_location', 'sup_location_td' )",0 ); ?> 
                    </td> 
                    <td>Supplier Location</td>
                    <td id="sup_location_td"><? echo create_drop_down( "cbo_supplier_location", 130, $blank_array,"", 1, "-- Select Supp Location --", "", "","" ); ?></td> 
                </tr>
                <tr>
                    <td>Attention</td>   
                    <td colspan="3">
                        <input class="text_boxes" type="text" style="width:382px;"  name="txt_attention" id="txt_attention" />
                        <input type="hidden" class="image_uploader" style="width:150px" value="Lab DIP No" onClick="openmypage( 'requires/short_fabric_booking_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">
                    </td>
                    <td>Ship Mode</td>
                    <td><?=create_drop_down( "cbo_shipmode", 130, $shipment_mode,"", 1, "--Select--", 0, "","","" ); ?></td>
                    <td>Tenor</td>
                    <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
                </tr>
                <tr>
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
                	<td><input class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_mainbooking('requires/short_fabric_booking_controller.php?action=main_booking_popup','Main Fabric Booking Search');" readonly placeholder="Browe for Main Booking" name="txt_mainbooking_no" id="txt_mainbooking_no"/>
                    <input type="hidden" id="hidd_mainbooking_id">
                    </td>
                    <td>Ready To Approved</td>  
                    <td><? echo create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
                    <td>Un-approve request</td>
                    <td><Input name="txt_un_appv_request" class="text_boxes" readonly placeholder="Double Click for Brows" ID="txt_un_appv_request" style="width:120px" onClick="openmypage_unapprove_request();"></td>
                    <td>Refusing Cause</td>
                	<td ><input class="text_boxes" type="text" maxlength="150" style="width:120px"  name="txt_refusing_cause" id="txt_refusing_cause"  readonly placeholder="Double Click for Brows" onClick="openmypage_refusing_cause()"/></td>
                </tr>
				<tr>
                	<td>Profit Center</td>
					<td id="profit_center_td"><?  echo create_drop_down("cbo_profit_center", 130, [], "", 1, "-- Select Profit Center --", $selected, 0,1); ?></td>
                    <td>Department</td>  
                    <td id="department_td"><? echo create_drop_down("cbo_department", 130, [], "", 1, "-- Select Department --", $selected, 0,1); ?></td>
                    <td>Final Comments</td>
                	<td ><input class="text_boxes" type="text" maxlength="150" style="width:120px"  name="txt_final_comment" id="txt_final_comment"  placeholder="Write" disabled  />
					<input type="hidden" readonly id="txt_update_type">
					<td>Short Provide Com</td>
                    <td id="prov_td"><? echo create_drop_down( "cbo_provider_name", 130, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Short Provider --", $selected, "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/short_fabric_booking_controller');load_drop_down( 'requires/short_fabric_booking_controller',this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_location', 'sup_location_td' )",0 ); ?> 
                    </td>
				</td>
                </tr>

                <tr>
                    <td>Remarks</td>   
                    <td colspan="3"><input class="text_boxes" type="text" maxlength="200" style="width:360px;"  name="txt_remark" id="txt_remark"/></td>
                	<td>
                        <input type="button" id="btnshippingmark" class="image_uploader" style="width:80px;" value="Shipping Mark" onClick="open_shipping_mark_popup('requires/short_fabric_booking_controller.php?action=shipping_mark_popup','Shipping Mark');" />
                        <input style="width:40px;" type="hidden" class="text_boxes" name="hiddshippingmark_breck_down" id="hiddshippingmark_breck_down" />
                    </td>
                    <td colspan="4">
						<? 
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(88,'txt_booking_no','../../');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="8" valign="top" id="app_sms2" style="font-size:18px; color:#F00"></td>
                </tr>
                <tr>
                    <td align="center" colspan="8" valign="middle" class="button_container">
                    <? $date=date('d-m-Y'); echo load_submit_buttons( $permission, "fnc_fabric_booking", 0,0 ,"reset_form('fabricbooking_1','','booking_list_view','cbo_pay_mode,3*cbo_booking_year,2021*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_date,".$date."')",1) ; ?>
					<input class="formbutton" type="button" onClick="fnSendMail('../../','',1,1,0,1)" value="Mail Send" style="width:80px;">
					<input type="button" value="Po Wise" onClick="generate_fabric_report('po_wise',1)" style="width:70px;display:none;" name="po_wise" id="po_wise" class="formbutton" />
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    <br>
    <form name="orderdetailsentry_2"  autocomplete="off" id="orderdetailsentry_2">
        <fieldset style="width:960px;">
        <legend>Details</legend>
            <table  width="950" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td width="100" class="must_entry_caption">PO No</td>   
                    <td width="150" id="order_drop_down_td"><?=create_drop_down( "cbo_order_id", 130, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                    <td width="100" class="must_entry_caption" >Fabric Description</td>
                    <td colspan="3" id="fabricdescription_id_td"><?=create_drop_down( "cbo_fabricdescription_id", 360, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                    <td width="100" class="must_entry_caption">Garments Color</td>
                    <td id="garmentscolor_id_id_td" ><?=create_drop_down( "cbo_garmentscolor_id", 130, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                </tr>
                <tr>
                    <td>Fabric Color</td>
                    <td id="fabriccolor_id_id_td"><? echo create_drop_down( "cbo_fabriccolor_id", 130, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                    <td class="must_entry_caption">Garments size</td>   
                    <td id="garmentssize_id_td"><? echo create_drop_down( "cbo_garmentssize_id", 130, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                    <td width="100">Item size</td>
                    <td width="150" id="itemsize_id_td"><? echo create_drop_down( "cbo_itemsize_id", 130, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                    <td class="must_entry_caption">Dia/ Width</td>
                    <td><input name="txt_dia_width" id="txt_dia_width" class="text_boxes" type="text" placeholder="Write" value="" style="width:120px "/></td>
                </tr>
                <tr>
                    <td>Finish Fabric</td>
                    <td><input name="txt_finish_qnty" id="txt_finish_qnty" class="text_boxes_numeric" type="text" onChange="calculate_requirement();" style="width:120px "/></td>  
                    <td>Process loss</td>
                    <td><input name="txt_process_loss" id="txt_process_loss" class="text_boxes_numeric" type="text" onChange="calculate_requirement();"   style="width:120px "/></td>
                    <td>Gray Fabric</td>
                    <td><input name="txt_grey_qnty" id="txt_grey_qnty" class="text_boxes_numeric" type="text" style="width:120px " readonly/></td>
                    <td>Rate</td>
                    <td><input name="txt_rate" id="txt_rate" class="text_boxes_numeric" type="text" onChange="calculate_requirement();" style="width:120px " /></td> 
                </tr>
                <tr>
                    <td>Amount</td>
                    <td>
                        <input name="txt_amount" id="txt_amount" class="text_boxes_numeric" type="text" value=""  style="width:120px " readonly/>
                        <input type="hidden" id="update_id_details">
                    </td>
                    <td>RMG Qty</td>
                    <td><input name="txt_rmg_qty" id="txt_rmg_qty" class="text_boxes_numeric" type="text" value=""  style="width:120px " /></td>
                    <td class="must_entry_caption">Responsible Dept.</td>
                    <td><?=create_drop_down( "cbo_responsible_dept", 130,"select id,department_name from  lib_department where status_active=1 and is_deleted=0 order by  department_name", "id,department_name", 0, "", '', '', $onchange_func_param_db,$onchange_func_param_sttc  ); ?></td>
                    <td class="must_entry_caption">Res. person</td>
                    <td><input name="cbo_responsible_person" id="cbo_responsible_person" class="text_boxes" type="text" value="" style="width:120px "/></td>
                </tr>
                <tr>
                	<td>Division</td>
                    <td id=""><? echo create_drop_down( "cbo_division_id", 130, $short_division_array,"", 1, "--Select--", $selected, "" ); ?></td>
                    <td class="must_entry_caption">Reason</td>
                    <td colspan="3"><input name="txt_reason" id="txt_reason" class="text_boxes" type="text"  style="width:350px "/></td>
                    <td colspan="2"><input type="button" id="causes" value="CAUSES OF SUPPLEMENTARY" style="width:180px" class="image_uploader" onClick="fnc_causes();"/></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="button" id="btnColorType" value="STRIPE YD" width="100" class="image_uploader" onClick="fncChangeYdButton();" style="display:none"/>
                    	<input type="hidden" width="100" id="hidden_colorType">
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="8" valign="middle" class="button_container">
						<?=load_submit_buttons( $permission, "fnc_fabric_booking_dtls", 0,0 ,"reset_form('orderdetailsentry_2','','','','')",2) ; ?>
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
						
                        <div id="pdf_file_name"></div>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
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
	<? 
	
	 
	
	
	?>
</body>
<script>
	set_multiselect('cbo_responsible_dept','0','0','','0');
	set_multiselect('cbo_responsible_dept','0','1','<?=$department_id;?>','0');
	$( document ).ready(function() {
load_drop_down( 'requires/short_fabric_booking_controller', document.getElementById('cbo_pay_mode').value, 'load_drop_down_suplier', 'sup_td' )
load_drop_down( 'requires/short_fabric_booking_controller', document.getElementById('cbo_pay_mode').value, 'load_drop_down_short_provider', 'prov_td' )
});
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
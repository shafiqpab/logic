<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create fabric Requisition
Functionality	         :
JS Functions	         :
Created by		         :	Zakaria Joy
Creation date 	         : 	06-06-2023
Requirment Client        :
Requirment By            :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         : 
-----------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_id=$_SESSION['logic_erp']['user_id'];


echo load_html_head_contents("Woven Fabric Booking", "../../", 1, 1,$unicode,1,'');
//--------------------------------------------------------------------------------------------------------------------
$date                  = date('d-m-Y');
$current_month         = date('n');
$level_arr             = array(1=>"PO Level",2=>"Job Level");
$basis             	   = array(1=>"Requisition");
$buyer_cond            = set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond          = set_user_lavel_filtering(' and comp.id','company_id');
/*$cbo_booking_month     = create_drop_down( "cbo_booking_month", 90, $months,"", 1, "-- Select --", "", "",0 );
$cbo_booking_year      = create_drop_down( "cbo_booking_year", 60, $year,"", 1, "-- Select --", date('Y'), "",0 );*/
$cbo_booking_month     = create_drop_down( "cbo_booking_month", 85, $months,"", 1, "-- Select --", "", "",0 );
$cbo_booking_year      = create_drop_down( "cbo_booking_year", 55, $year,"", 1, "-- Select --", date('Y'), "",0 );

$cbo_company_name      = create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company  comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", "", "load_drop_down( 'requires/fabric_booking_by_requisition_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/fabric_booking_by_requisition_controller', $('#cbo_pay_mode').val()+'_'+this.value, 'load_drop_down_suplier', 'sup_td' ); check_month_setting(); validate_suplier(); get_php_form_data( this.value, 'company_wise_report_button_setting','requires/fabric_booking_by_requisition_controller' );check_exchange_rate();",0,"" );
$cbo_buyer_name		   = create_drop_down( "cbo_buyer_name", 140, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "","","" );
$cbo_brand_name		   = create_drop_down("cbo_brand_id", 140, $blank_array,"",1, "-Brand-", $selected,"");
$cbo_fabric_natu       = create_drop_down( "cbo_fabric_natu", 90, $item_category,"", 1, "-- Select --", 1,$onchange_func, $is_disabled, "2,3");
$cbouom                = create_drop_down( "cbouom", 50, $unit_of_measurement,'', 1, '-Uom-', $row[csf('uom')], "",$disabled,"1,12,23,27" );
$cbo_fabric_source     = create_drop_down( "cbo_fabric_source", 140, $fabric_source,"", 1, "-- Select --", "","", "", "");
$cbo_pay_mode          = create_drop_down( "cbo_pay_mode", 140, $pay_mode,"", 1, "-- Select Pay Mode --", 0, "load_drop_down( 'requires/fabric_booking_by_requisition_controller', this.value+'_'+$('#cbo_company_name').val(), 'load_drop_down_suplier', 'sup_td'); fnc_greyFabPurchase(this.value);","","1,2,3,4,5" );
$cbo_supplier_name     = create_drop_down( "cbo_supplier_name", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=9 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "fill_attention(this.value)",0 );
$cbo_currency          = create_drop_down( "cbo_currency", 140, $currency,"",1, "-- Select --", 2, "check_exchange_rate()",0 );
$cbo_basis            = create_drop_down( "cbo_basis", 140, $basis,"", 1, "-- Select Basis --", 1, "",1 );
$cbo_source            = create_drop_down( "cbo_source", 140, $source,"", 1, "-- Select Source --", "", "","" );
$cbo_ready_to_approved = create_drop_down( "cbo_ready_to_approved", 140, $yes_no,"", 1, "-- Select--", 2, "","","" );
$cbo_level             = create_drop_down( "cbo_level", 140, $level_arr,"", 0, "", 2, "","","" );
$buttons               = load_submit_buttons( $permission, "fnc_fabric_booking", 0,0 ,"reset_form('fabricbooking_1','booking_list_view*booking_list_view_list*print_list', '', 'cbo_booking_year,2022*cbo_booking_month, ".$current_month."*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_percent,100*txt_booking_date, ".$date."', disable_enable_fields( 'cbo_currency*cbo_company_name*cbo_supplier_name*cbo_level*cbo_buyer_name', 0), 'cbo_currency*cbo_booking_year*cbo_booking_month*cbo_pay_mode*cbo_source*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_level*cbo_company_name*cbo_buyer_name*cbo_fabric_natu*cbo_fabric_source*cbouom')",1) ;

$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<?=$permission; ?>';
<?
if(isset($_SESSION['logic_erp']['data_arr'][108]));
{
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][108] );
}
//print_r($_SESSION['logic_erp']['mandatory_field'][108]);
echo "var mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][108]) . "';\n";
echo "var mandatory_message = '". implode('*',$_SESSION['logic_erp']['mandatory_message'][108]) . "';\n";
echo "var field_level_data= ". $data_arr . ";\n";
?>
	//alert(mandatory_field)
	function openmypage_booking(page_link,title)
	{
		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var company=$("#cbo_company_name").val()*1;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company, title, 'width=1190px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			if (theemail.value!="")
			{
				reset_form('fabricbooking_1','booking_list_view','','txt_booking_date,<?=date("d-m-Y"); ?>');
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/fabric_booking_by_requisition_controller" );
				set_button_status(1, permission, 'fnc_fabric_booking',1);
				fnc_show_booking_list();
				get_php_form_data( document.getElementById('cbo_company_name').value, 'company_wise_report_button_setting','requires/fabric_booking_by_requisition_controller' );
				check_month_setting();
			}
			$("#cbo_company_name").attr("disabled","disabled");
			$("#cbo_buyer_name").attr("disabled","disabled");
		}
	}

	function openmypage_order(page_link,title)
	{
		if(document.getElementById('id_approved_id').value==1){
			alert("This booking is approved")
			return;
		}
		if (form_validation('txt_booking_no*cbo_fabric_natu*cbo_fabric_source','Booking No*Fabric Nature*Fabric Source')==false){
			return;
		}
		page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_currency*cbo_fabric_natu*cbouom*cbo_fabric_source','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1170px,height=420px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];;
			var id=this.contentDoc.getElementById("po_number_id");
			var po=this.contentDoc.getElementById("po_number");
			if (id.value!=""){
				freeze_window(5);
				document.getElementById('txt_order_no_id').value=id.value;
				document.getElementById('txt_order_no').value=po.value;
				var cbo_fabric_natu=document.getElementById('cbo_fabric_natu').value;
				var cbouom=document.getElementById('cbouom').value;
				var cbo_fabric_source=document.getElementById('cbo_fabric_source').value;
				get_php_form_data( id.value+"_"+cbo_fabric_natu+"_"+cbouom+"_"+cbo_fabric_source, "populate_order_data_from_search_popup", "requires/fabric_booking_by_requisition_controller" );
				check_month_setting();
				release_freezing();
			}
		}
	}

	function fnc_generate_booking(){ 

		
		if (form_validation('txt_booking_no*txt_select_item','Booking No*Item NO')==false){
			return;

		}
		else{
			var data="action=generate_fabric_booking"+get_submitted_data_string('txt_booking_no*txt_select_item*cbo_company_name*cbo_buyer_name',"../../");
			http.open("POST","requires/fabric_booking_by_requisition_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_generate_booking_reponse;
		}
	}

	function fnc_generate_booking_reponse(){
		if(http.readyState == 4){
			document.getElementById('booking_list_view').innerHTML=http.responseText;
					set_button_status(0, permission, 'fnc_fabric_booking_dtls',2,"reset_form('','booking_list_view','','','')");
				set_all_onclick();
		}
	}

	function fnc_fabric_booking( operation ){
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

		if(document.getElementById('id_approved_id').value==1){
			alert("This booking is approved")
			release_freezing();
			return;
		}
		if(operation!=2)
		{
			if (form_validation('txt_delivery_date','Delivery Date')==false){
				release_freezing();
				return;

		    }
			var delivery_date=$('#txt_delivery_date').val();
			if(date_compare($('#txt_booking_date').val(), delivery_date)==false){
				alert("Delivery Date Not Allowed Less than Booking Date.");
				release_freezing();
				return;
			}
		}
		
		if (form_validation('cbo_company_name*cbo_buyer_name*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_supplier_name*cbo_source*cbo_basis','Company Name*Buyer Name*Booking Date*Delivery Date*Pay Mode*Supplier Name*Source*Basis')==false){
			release_freezing();
			return;
		}
		if (document.getElementById('cbo_pay_mode').value!=3 && document.getElementById('cbo_supplier_name').value==0){
			alert("Select Supplier Name")
			release_freezing();
			return;
		}
		else{
			var data="action=save_update_delete&operation="+operation+"&delete_cause="+delete_cause+get_submitted_data_string('txt_booking_no*cbo_company_name*cbo_buyer_name*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_supplier_name*cbo_source*cbo_basis*txt_colar_excess_percent*txt_cuff_excess_percent*cbo_currency*txt_exchange_rate*cbo_greyfab_purch*cbo_shipmode*cbo_payterm*txt_tenor*txt_fabriccomposition*txt_attention*txt_remark*cbo_ready_to_approved*txt_un_appv_request*hiddshippingmark_breck_down*update_id',"../../");
			http.open("POST","requires/fabric_booking_by_requisition_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_booking_reponse;
		}
	}

	function fnc_fabric_booking_reponse(){
		if(http.readyState == 4){
			var reponse=trim(http.responseText).split('**');
			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1) {
				document.getElementById('txt_booking_no').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
				set_button_status(1, permission, 'fnc_fabric_booking',1);
				show_msg(trim(reponse[0]));
				$("#cbo_company_name").attr("disabled","disabled");
				$("#cbo_buyer_name").attr("disabled","disabled");
			}

			if(parseInt(trim(reponse[0]))==2) {
				show_msg(trim(reponse[0]));
				fnc_show_booking_list();
				reset_form('fabricbooking_1','booking_list_view','','txt_booking_date,<? echo date("d-m-Y"); ?>');
				//reset_form('','booking_list_view','','');
				release_freezing();
			}
			if(trim(reponse[0])=='approved'){
				alert("This booking is approved So Update/Delete Not Possible");
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
			release_freezing();
		}
	}

	function fnc_fabric_booking_dtls( operation ){
		freeze_window(operation);
		if(document.getElementById('id_approved_id').value==1){
			alert("This booking is approved")
			release_freezing();
			return;
		}
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

		var row_num=$('#tbl_fabric_booking tr').length;
		var data_all=""; var z=1;
		for (var i=1; i<=row_num; i++){
			if (form_validation('txt_select_item*txt_booking_no','Req Item*Booking No')==false){
				release_freezing()
				return;
			}
			var wo_qty=document.getElementById('txt_wo_qty_'+i).value
			if(wo_qty=="" || wo_qty==0){
				alert("Insert WO Qty.")
				release_freezing();
				return;
			}
			data_all+="&txtjobno_" + z + "='" + $('#txtjobno_'+i).val()+"'"+"&txtpoid_" + z + "='" + $('#txtpoid_'+i).val()+"'"+"&dtlsid_" + z + "='" + $('#dtlsid_'+i).val()+"'"+"&diawidth_" + z + "='" + $('#diawidth_'+i).val()+"'"+"&gsmweight_" + z + "='" + $('#gsmweight_'+i).val()+"'"+"&gmtscolorid_" + z + "='" + $('#gmtscolorid_'+i).val()+"'"+"&fabriccolorid_" + z + "='" + $('#fabriccolorid_'+i).val()+"'"+"&txtwoq_" + z + "='" + $('#txt_wo_qty_'+i).val()+"'"+"&txtrate_" + z + "='" + $('#txt_rate_'+i).val()+"'"+"&txtamount_" + z + "='" + $('#txt_amount_'+i).val()+"'"+"&txtconst_" + z + "='" + $('#txtconst_'+i).val()+"'"+"&txtcomp_" + z + "='" + $('#txtcomp_'+i).val()+"'"+"&dtlsid_" + z + "='" + $('#dtlsid_'+i).val()+"'"+"&txtcolortype_" + z + "='" + $('#txtcolortype_'+i).val()+"'"+"&bookingid_" + z + "='" + $('#bookingid_'+i).val()+"'"+"&reqid_" + z + "='" + $('#reqid_'+i).val()+"'";
			z++;
		}
		var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*txt_booking_no*update_id',"../../")+data_all;

		http.open("POST","requires/fabric_booking_by_requisition_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
	}

	function fnc_fabric_booking_dtls_reponse(){
		if(http.readyState == 4){
			 var reponse=trim(http.responseText).split('**');
			 if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1 || parseInt(trim(reponse[0]))==2){
				fnc_show_booking_list();
				reset_form('','booking_list_view','','');
				release_freezing();
				show_msg(trim(reponse[0]));
			 }
			if(trim(reponse[0])==10 || trim(reponse[0])==11)
			{
				release_freezing();
				show_msg(trim(reponse[0]));
				return;
			}
			if(trim(reponse[0])=='approved'){
				alert("This booking is approved");
				release_freezing();
				return;
			}
			
			 release_freezing();
		}
	}

	function fnc_show_booking_list(){
		var data="action=show_fabric_booking_list"+get_submitted_data_string('txt_booking_no*txt_select_item*cbo_company_name*cbo_buyer_name',"../../");
		http.open("POST","requires/fabric_booking_by_requisition_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_booking_list_reponse;
	}

	function fnc_show_booking_list_reponse(){
		if(http.readyState == 4){
			document.getElementById('booking_list_view_list').innerHTML=http.responseText;
		}
	}

	function fnc_show_booking(){
		if (form_validation('txt_booking_no','Booking No')==false){
			return;
		}
		else{
			freeze_window(5);
			var data="action=show_fabric_booking"+get_submitted_data_string('txt_booking_no*txt_order_no_id*cbo_company_name*cbo_buyer_name*cbo_fabric_description',"../../");
			http.open("POST","requires/fabric_booking_by_requisition_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_show_booking_reponse;
		}
	}

	function fnc_show_booking_reponse(){
		if(http.readyState == 4){
			document.getElementById('booking_list_view').innerHTML=http.responseText;
			set_button_status(1, permission, 'fnc_fabric_booking_dtls',2,"reset_form('','booking_list_view','','','')");
			set_all_onclick();
			release_freezing();
		}
	}

	function open_terms_condition_popup(page_link,title){
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		if (txt_booking_no==""){
			alert("Save The Booking First")
			return;
		}
		else{
			page_link=page_link+get_submitted_data_string('txt_booking_no','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
			}
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

	function open_rmg_process_loss_popup(page_link,title){
		var processloss_breck_down=document.getElementById('processloss_breck_down').value
		page_link=page_link+'&processloss_breck_down='+processloss_breck_down;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=230px,height=230px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("processloss_breck_down");
			if (theemail.value!=""){
				document.getElementById('processloss_breck_down').value=theemail.value;
			}
		}
	}

	function open_colur_cuff_popup(page_link,title){
		var txt_booking_no=trim(document.getElementById('txt_booking_no').value)
		var cbo_level=document.getElementById('cbo_level').value
		page_link=page_link+'&txt_booking_no='+txt_booking_no+'&cbo_level='+cbo_level;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=400px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			/*var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("processloss_breck_down");
			if (theemail.value!=""){
				document.getElementById('processloss_breck_down').value=theemail.value;
			}*/
		}
	}

	function open_adjust_qty_popup(page_link,title){
		var adjust_qty_breck_down=document.getElementById('adjust_qty_breck_down').value
		page_link=page_link+'&adjust_qty_breck_down='+adjust_qty_breck_down;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("processloss_breck_down");
			if (theemail.value!=""){
				document.getElementById('processloss_breck_down').value=theemail.value;
			}
		}
	}


	function open_size_wise_cuff_popup(page_link,title){
		var processloss_breck_down=document.getElementById('processloss_breck_down').value
		page_link=page_link+'&processloss_breck_down='+processloss_breck_down;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=230px,height=230px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("processloss_breck_down");
			if (theemail.value!=""){
				document.getElementById('processloss_breck_down').value=theemail.value;
			}
		}
	}

	function open_size_wise_colur_popup(page_link,title){
		var processloss_breck_down=document.getElementById('processloss_breck_down').value
		page_link=page_link+'&processloss_breck_down='+processloss_breck_down;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=230px,height=230px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("processloss_breck_down");
			if (theemail.value!=""){
				document.getElementById('processloss_breck_down').value=theemail.value;
			}
		}
	}

	function validate_value( i , type){
		if(type=='finish'){
			var real_value =document.getElementById('finscons_'+i).placeholder;
			var user_given =document.getElementById('finscons_'+i).value;
			if(user_given> real_value){
				alert("Over booking than budget not allowed");
				document.getElementById('finscons_'+i).value=real_value;
				document.getElementById('finscons_'+i).focus();
			}
		}
		if(type=='grey'){
			var real_value =document.getElementById('greycons_'+i).placeholder;
			var user_given =document.getElementById('greycons_'+i).value;
			document.getElementById('amount_'+i).value=(document.getElementById('rate_'+i).value)*1*user_given;
			if(user_given > real_value ){
				alert("Over booking than budget not allowed");
				document.getElementById('greycons_'+i).value=real_value;
				document.getElementById('greycons_'+i).focus();
				document.getElementById('amount_'+i).value=(document.getElementById('rate_'+i).value)*1*real_value;
			}
		}
		if(type=='rate'){
			document.getElementById('amount_'+i).value=(document.getElementById('rate_'+i).value)*1*(document.getElementById('greycons_'+i).value)*1;
		}
	}

	function openmypage_unapprove_request(){
		if (form_validation('txt_booking_no','Booking Number')==false){
			return;
		}
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		var txt_un_appv_request=document.getElementById('txt_un_appv_request').value;
		var data=txt_booking_no+"_"+txt_un_appv_request;
		var title = 'Un Approval Request';
		var page_link = 'requires/fabric_booking_by_requisition_controller.php?data='+data+'&action=unapp_request_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function(){
			var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
			$('#txt_un_appv_request').val(unappv_request.value);
		}
	}

	function check_exchange_rate(){
		var cbo_currercy=$('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var cbo_company_name = $('#cbo_company_name').val();
		/*var cbo_fabric_sourceId = $('#cbo_fabric_source').val();
		if(cbo_fabric_sourceId==2 && cbo_currercy==1)
		{
			$('#cbo_currency').val(cbo_currercy);
		}*/
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/fabric_booking_by_requisition_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}

	function copy_colarculfpercent(count){
		var rowCount = $('#tbl_fabric_booking tr').length;
		var bodypartid=document.getElementById('bodypartid_'+count).value;
		var gmtssizeid=document.getElementById('gmtssizeid_'+count).value;
		var colarculfpercent=document.getElementById('colarculfpercent_'+count).value;
		for(var j=count; j<=rowCount; j++){
			if(document.getElementById('bodypartid_'+j).value==2 || document.getElementById('bodypartid_'+j).value==3){
				if( gmtssizeid==document.getElementById('gmtssizeid_'+j).value){
					document.getElementById('colarculfpercent_'+j).value=colarculfpercent;
				}
			}
		}
	}

	function check_month_setting(){
		var cbo_company_name=$('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_company_name, 'check_month_maintain', '', 'requires/fabric_booking_by_requisition_controller');
		var response=response.split("_");
		if(response[0]==1){
			$('#month_id').val(1);
			$('#booking_td').css('color','blue');
			$('#lib_tna_intregrate').val(1);
		}
		else{
			$('#month_id').val(2);
			$('#booking_td').css('color','black');
			$('#cbo_booking_month').val('');
			$('#lib_tna_intregrate').val(0);
		}
	}

	function validate_suplier(){
		var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
		var company=document.getElementById('cbo_company_name').value;
		var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
		/*if(company==cbo_supplier_name && cbo_pay_mode==5){
			alert("Same Company Not Allowed");
			document.getElementById('cbo_supplier_name').value=0;
			return;
		}*/
		fill_attention(cbo_supplier_name)
	}

	function fill_attention(supplier_id){
		if(supplier_id==0){
			document.getElementById('txt_attention').value='';
			return;
		}
		var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
		var attention=return_global_ajax_value(supplier_id+"_"+cbo_pay_mode, 'get_attention_name', '', 'requires/fabric_booking_by_requisition_controller');
		document.getElementById('txt_attention').value=trim(attention);
	}

	function compare_date(){
		var txt_delevary_date_data=document.getElementById('txt_delivery_date').value;
		txt_delevary_date_data= txt_delevary_date_data.split('-');
		var txt_delevary_date_inv=txt_delevary_date_data[2]+"-"+txt_delevary_date_data[1]+"-"+txt_delevary_date_data[0];
		var txt_tna_date_data=document.getElementById('txt_tna_date').value;
		txt_tna_date_data = txt_tna_date_data.split('-');
		var txt_tna_date_inv=txt_tna_date_data[2]+"-"+txt_tna_date_data[1]+"-"+txt_tna_date_data[0];

		var txt_delevary_date = new Date(txt_delevary_date_inv);
		var txt_tna_date = new Date(txt_tna_date_inv);
		//var delivery_date_tna = new Date(txt_delivery_date_tna_data_inv);
		var tna_intregrate=document.getElementById('lib_tna_intregrate').value;
		if(txt_tna_date_data !=''){
			if(tna_intregrate==1){
				if(txt_delevary_date > txt_tna_date){
					alert('Delivery Date is greater than TNA Date');
					document.getElementById('txt_delivery_date').value=document.getElementById('txt_tna_date').value;
				}
			}
		}
	}
	function fnResetForm(){
		reset_form('','booking_list_view*booking_list_view_list*print_list','','','');
	}

	function set_data(po_id,po_number,precost_id,booking_id){
		document.getElementById('txt_order_no_id').value=po_id;
		document.getElementById('cbo_fabric_description').value=booking_id;
		document.getElementById('txt_select_item').value=precost_id;
		fnc_show_booking()
	}
	function deletedata(booking_id){
		var operation=2;
		freeze_window(operation);
		if(document.getElementById('id_approved_id').value==1){
			alert("This booking is approved")
			release_freezing();
			return;
		}
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

		var row_num=1;
		if (form_validation('txt_booking_no','Booking No')==false){
			release_freezing()
			return;
		}
		
        var i=1; var dltsid=""; var z=1;
		var data_all=get_submitted_data_string('txt_booking_no*cbo_pay_mode',"../../",i);
		var listrows =$('#tbl_fabric_booking_list tbody tr').length; 
		//alert(listrows);
		if(document.getElementById('chkdeleteall').checked==true)
		{
			for (var i = 1; i <= listrows; i++)
			{
				dltsid+="&bookingid_"+z+"='" + $('#txtdelete'+i).val()+"'";
				z++;
			}
		}
		else
		{
			for (var i = 1; i <= listrows; i++)
			{
				if(document.getElementById('chkdelete_'+i).checked==true)
				{
					dltsid+="&bookingid_"+z+"='" + $('#txtdelete'+i).val()+"'";
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
		var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+z+dltsid+data_all+"&delete_cause="+delete_cause;
		http.open("POST","requires/fabric_booking_by_requisition_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
	}

	function fnc_delivery_date()
	{
		var isDisabled = $('txt_delivery_date').is(':disabled');
		//alert(isDisabled)isDisabled
		if(isDisabled=="false")
		{
			$('#txt_delivery_date').val('');
		}
		else
		{
			$('#txt_delivery_date').val(<? echo date("d-m-Y"); ?>);
		}
	}
	
	function fnc_greyFabPurchase(val)
	{
		if( val==1)
		{
			$('#cbo_greyfab_purch').removeAttr('disabled', false);
		}
		else
		{
			$('#cbo_greyfab_purch').attr('disabled', true);
			$('#cbo_greyfab_purch').val(2);
		}
	}
	function dtm_popup(page_link,title)
	{
		var job_no=$('#txt_job_no_str').val();
		var booking_no=$('#txt_booking_no').val();
		var selected_no=$('#order_no_id_str').val();
	
		if(booking_no=='')
		{
			alert('Booking  Not Found.');
			$('#txt_booking_no').focus();
			return;
		}
		if(job_no=='' || selected_no=='')
		{
			alert('Booking Details Not Found.');
			return;
		}
	
		page_link=page_link+'&job_no='+job_no+'&booking_no='+booking_no+'&selected_no='+selected_no;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=1,scrolling=0','../')
	}
	function calculate_amount(inc,type,value){
		if(type==1){
			var balance=$('#txt_balance_'+inc).val()*1;
			if(value>balance){
				alert("WO Qty Can not greater than Balance");
				$('#txt_wo_qty_'+inc).val(0);
				$('#txt_amount_'+inc).val(0);
				return;
			}
			var rate=$('#txt_rate_'+inc).val();
			var amount =value*rate;
			$('#txt_amount_'+inc).val(amount)
		}
		if(type==2){
			var wo_qty=$('#txt_wo_qty_'+inc).val();
			var amount =value*wo_qty;
			$('#txt_amount_'+inc).val(amount)
		}
	}
	function generate_fabric_report(type,mail_send_data,mail_body){
		if ( form_validation('txt_booking_no','Booking No')==false ){
			return;
		}
		else{
			var show_yarn_rate='';				
			var fabric_source=$("#cbo_fabric_source").val();
			var report_title=$( "div.form_caption" ).html();
			var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*id_approved_id',"../../")+'&report_title='+report_title+'&show_yarn_rate='+show_yarn_rate+'&mail_send_data='+mail_send_data+'&mail_body='+mail_body+'&path=../../';
			//alert(data); return; cbo_fabric_natu*cbo_fabric_source*
			freeze_window(5);
			http.open("POST","requires/fabric_booking_by_requisition_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
		}
	}

	function generate_fabric_report_reponse(){
		if(http.readyState == 4){
			var file_data=http.responseText.split('****');
			if(file_data[2]==100)
			{
			$('#data_panel').html(file_data[1]);
			$('#aal_report4').removeAttr('href').attr('href','requires/'+trim(file_data[0]));
				//$('#print_report4')[0].click();
			document.getElementById('aal_report4').click();
			}
			else
			{
				$('#pdf_file_name').html(file_data[1]);
				$('#data_panel').html(file_data[0] );
			}
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
			d.close();
			var content=document.getElementById('data_panel').innerHTML;
			release_freezing();
		}
	}
</script>
<style>
 /* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

 /* Modal Header */
.modal-header {
    padding: 2px 16px;
    background-color: #999;
    color: white;
}

/* Modal Body */
.modal-body {padding: 2px 16px;}

/* Modal Footer */
.modal-footer {
    padding: 2px 16px;
    background-color: #999;
    color: white;
}

/* Modal Content */
.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 80%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

/* Add Animation */
@-webkit-keyframes animatetop {
    from {top: 300px; opacity: 0}
    to {top: 0; opacity: 1}
}

@keyframes animatetop {
    from {top: 300px; opacity: 0}
    to {top: 0; opacity: 1}
}

/* The Close Button */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

@media print {
    .gg {page-break-after: always;}
}

</style>
</head>
<body onLoad="set_hotkey(); check_exchange_rate(); check_month_setting(); ">
    <div style="width:100%;" align="center">
   		<? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="fabricbooking_1"  autocomplete="off" id="fabricbooking_1">
            <fieldset style="width:1020px;">
            <legend>Fabric Booking By Requisition</legend>
                <table  width="1000" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td colspan="4" align="right" class="must_entry_caption"> Booking No </td>
                        <td colspan="4">
                            <input class="text_boxes" type="text" style="width:130px" onDblClick="openmypage_booking('requires/fabric_booking_by_requisition_controller.php?action=fabric_booking_popup','fabric Booking Search')" readonly placeholder="Double Click for Booking"  name="txt_booking_no" id="txt_booking_no"/>
                            <input type="hidden" id="id_approved_id">
                            <input type="hidden" id="update_id">
                            <input type="hidden" id="txt_job_no_str" value="">
                            <input type="hidden" id="order_no_id_str" value="">
                        </td>
                    </tr>
                    <tr>
                        <td width="100" class="must_entry_caption">Company Name </td>
                        <td width="150"><?=$cbo_company_name; ?></td>
                        <td width="100" class="must_entry_caption">Buyer Name</td>
                        <td width="150" id="buyer_td"><?=$cbo_buyer_name; ?></td>
                        <td class="must_entry_caption">Booking Date</td>
                        <td><input class="datepicker" type="text" style="width:130px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<?=$date?>" disabled /></td>
                        <td class="must_entry_caption">Delivery Date</td>
                        <td><input class="datepicker" type="hidden" style="width:130px" name="txt_tna_date" id="txt_tna_date"/>
                        <input class="datepicker" type="text" style="width:130px" name="txt_delivery_date" id="txt_delivery_date" onChange="compare_date();" value="<?=date("d-m-Y"); ?>"/></td>
                    </tr>
                    <tr>
                    <td class="must_entry_caption">Pay Mode</td>
                        <td><?=$cbo_pay_mode; ?></td>
                    	<td class="must_entry_caption">Supplier Name</td>
                        <td id="sup_td"><?=$cbo_supplier_name; ?></td>
                        <td class="must_entry_caption">Source</td>
                        <td><?=$cbo_source; ?></td>
                        <td class="must_entry_caption">Basis</td>
                        <td><?=$cbo_basis; ?></td>                        
                    </tr>
                    <tr>
                    	<td>Collar Ex.Cut %</td>
                        <td><input style="width:130px;" type="text" class="text_boxes_numeric"  name="txt_colar_excess_percent" id="txt_colar_excess_percent"/> </td>
                        <td>Cuff Ex. Cut %</td>
                        <td><input style="width:130px;" type="text" class="text_boxes_numeric"  name="txt_cuff_excess_percent" id="txt_cuff_excess_percent"/></td>
                        <td>Currency</td>
                        <td><?=$cbo_currency; ?></td>
                        <td>Exchange Rate</td>
                        <td><input style="width:130px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  /></td>
                    </tr>
                    <tr>
                    	
                        <td>Grey Fab. Pur.</td>
                        <td><?=create_drop_down( "cbo_greyfab_purch", 140, $yes_no,"", 0, "-- Select--", 2, "",1,"" ); ?></td>
                        <td>Ship Mode</td>
                        <td><?=create_drop_down( "cbo_shipmode", 140, $shipment_mode,"", 1, "--Select--", 0, "","","" ); ?></td>
                    	<td>Pay Term</td>
                        <td><?=create_drop_down( "cbo_payterm", 140, $pay_term,"", 1, "--Select--", 0, "","","" ); ?></td>
                        <td>Tenor</td>
                    	<td><input style="width:130px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
                    </tr>
                    <tr>
                        <td>Fabric Comp.</td>
                        <td colspan="3"><input class="text_boxes" type="text" maxlength="200" style="width:380px;"  name="txt_fabriccomposition" id="txt_fabriccomposition"/></td>
                        <td>Attention</td>
                        <td colspan="3"><input class="text_boxes" type="text" style="width:380px;" name="txt_attention" id="txt_attention"/></td>
                    </tr>
                    <tr>
                    	<td>Remarks</td>
                        <td colspan="3"><input class="text_boxes" type="text" maxlength="200" style="width:380px;"  name="txt_remark" id="txt_remark"/></td>
                    	<td>Ready To App.</td>
                        <td><?=$cbo_ready_to_approved; ?></td>
                        <td>Un-App. Req.</td>
                        <td><Input name="txt_un_appv_request" class="text_boxes" readonly placeholder="Double Click for Brows" ID="txt_un_appv_request" style="width:130px"  onClick="openmypage_unapprove_request();" /></td>
                    </tr>
                    <tr>
                    	<td>
                            <input type="button" id="btnshippingmark" class="image_uploader" style="width:95px;" value="Shipping Mark" onClick="open_shipping_mark_popup('requires/fabric_booking_by_requisition_controller.php?action=shipping_mark_popup','Shipping Mark');" />
                            <input style="width:45px;" type="hidden" class="text_boxes" name="hiddshippingmark_breck_down" id="hiddshippingmark_breck_down" />
                        </td>
                    	<td align="center">
                            <input type="button" id="set_button2" class="image_uploader" style="width:95px;" value="Process Loss %" onClick="open_rmg_process_loss_popup('requires/fabric_booking_by_requisition_controller.php?action=rmg_process_loss_popup','Process Loss %');" />
                            <input style="width:45px;" type="hidden" class="text_boxes"  name="processloss_breck_down" id="processloss_breck_down" />
                            <input style="width:40px;" type="hidden" class="text_boxes" name="adjust_qty_breck_down" id="adjust_qty_breck_down" />
                        </td>
                        <td>&nbsp;</td>
                        <td>
                        	<?
                            include("../../terms_condition/terms_condition.php");
                            terms_condition(108,'txt_booking_no','../../');
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td align="center" colspan="8" valign="top" id="app_sms2" style="font-size:18px; color:#F00"></td>
                    </tr>
                    <tr>
                    	<td align="center" colspan="8" valign="top" id="app_sms3" style="font-size:18px; color:#F00"></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="8" valign="middle" class="button_container">
							<?=$buttons; ?>
                            <input class="text_boxes" name="lib_tna_intregrate" id="lib_tna_intregrate" type="hidden" style="width:100px"/>
                            <input type="hidden" style="width:100px" id="selected_id_for_delete">
                            <div id="pdf_file_name"></div>
                            <input type="button" id="set_button3" class="image_uploader" style="width:130px;" value="Collar & Cuff" onClick="open_colur_cuff_popup('requires/fabric_booking_by_requisition_controller.php?action=colur_cuff_popup&permissions=<? echo $permission?>','Collar & Cuff')" />

							<input type="button" value="Send" onClick="fnSendMail('../../','update_id',1,0,0,0,0)"  style="width:80px;" class="formbutton" />


                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
        <form name="servicebookingknitting_2"  autocomplete="off" id="servicebookingknitting_2">
            <fieldset style="width:950px;">
            <legend title="V3">Booking Item Form &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            Select Item: <input class="text_boxes" type="text" style="width:160px" onDblClick="fnc_process_data();" readonly placeholder="Double Click" name="txt_select_item" id="txt_select_item"/>
            <input class="text_boxes" type="hidden" style="width:160px"  readonly placeholder="Double Click" name="txt_order_no_id" id="txt_order_no_id"/>
            <input class="text_boxes" type="hidden" style="width:160px"  readonly placeholder="Double Click" name="cbo_fabric_description" id="cbo_fabric_description"/></legend>
            <div id="booking_list_view"><font id="save_sms" style="color:#F00">Select new Item</font></div>
            <?=load_submit_buttons( $permission, "fnc_fabric_booking_dtls", 0,0 ,"reset_form('','booking_list_view','','','')",2) ; ?>
			<div id="print_list">
				<input type="button" value="Print" onClick="generate_fabric_report('print_booking')"  style="width:100px;" name="print" id="print" class="formbutton" />
				<input type="button" value="Print 2" onClick="generate_fabric_report('print_booking_2')"  style="width:100px;" name="print_2" id="print_2" class="formbutton" />
			</div>
            </fieldset>
        </form>
        <div id="booking_list_view_list"></div>
        <div id="data_panel" style="display:none"></div>
    </div>
<!-- The Modal -->
<input type="button" id="myBtn" value="OPen" style="display:none"/>
<div id="myModal" class="modal">

  <div class="modal-content">
  <div class="modal-header">
    <span class="close">×</span>
    <h2>Po Number</h2>
  </div>
  <div class="modal-body">
    <p id="ccc">Some text in the Modal Body</p>

  </div>
  <div class="modal-footer">
    <h3></h3>
  </div>
</div>

</div>

</body>
<script>
	var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function setdata(data){

	document.getElementById('ccc').innerHTML=data;
	document.getElementById('myBtn').click();
}
function fnc_process_data(){
	if (form_validation('cbo_company_name*txt_booking_no','Company*Booking No')==false){
		return;
	}
	else{
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_booking_no=document.getElementById('txt_booking_no').value;
	    var page_link='requires/fabric_booking_by_requisition_controller.php?action=fabric_search_popup';
		var title='Fabric Booking By Requisition';
		page_link=page_link+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&txt_booking_no='+txt_booking_no;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_selected_id");
			if (theemail.value!=""){
				document.getElementById('txt_select_item').value=theemail.value;
				fnc_generate_booking();
			}
		}
	}
}
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$( document ).ready(function() {
	$('#cbo_buyer_name').val(0);
	load_drop_down( 'requires/fabric_booking_by_requisition_controller', document.getElementById('cbo_pay_mode').value+'_'+$('#cbo_company_name').val(), 'load_drop_down_suplier', 'sup_td' )
});
</script>
</html>
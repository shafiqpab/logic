<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Multiple Job Wise Additional Fabric booking
Functionality	         :
JS Functions	         :
Created by		         :	Zakaria joy
Creation date 	         : 	05-02-2023
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
$item_from_arr		   =array(1=>"Pre-Costing",2=>"Library");
$buyer_cond            = set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond          = set_user_lavel_filtering(' and comp.id','company_id');
/*$cbo_booking_month     = create_drop_down( "cbo_booking_month", 90, $months,"", 1, "-- Select --", "", "",0 );
$cbo_booking_year      = create_drop_down( "cbo_booking_year", 60, $year,"", 1, "-- Select --", date('Y'), "",0 );*/
$cbo_booking_month     = create_drop_down( "cbo_booking_month", 85, $months,"", 1, "-- Select --", "", "",0 );
$cbo_booking_year      = create_drop_down( "cbo_booking_year", 55, $year,"", 1, "-- Select --", date('Y'), "",0 );

$cbo_company_name      = create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company  comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", "", "load_drop_down( 'requires/multi_job_additional_fabric_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/multi_job_additional_fabric_booking_controller', $('#cbo_pay_mode').val()+'_'+this.value, 'load_drop_down_suplier', 'sup_td' ); check_month_setting(); validate_suplier(); get_php_form_data( this.value, 'company_wise_report_button_setting','requires/multi_job_additional_fabric_booking_controller' );check_exchange_rate();",0,"" );
$cbo_buyer_name		   = create_drop_down( "cbo_buyer_name", 140, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "","","" );
$cbo_brand_name		   = create_drop_down("cbo_brand_id", 140, $blank_array,"",1, "-Brand-", $selected,"");
$cbo_fabric_natu       = create_drop_down( "cbo_fabric_natu", 90, $item_category,"", 1, "-- Select --", 1,$onchange_func, $is_disabled, "2,3");
$cbouom                = create_drop_down( "cbouom", 50, $unit_of_measurement,'', 1, '-Uom-', $row[csf('uom')], "",$disabled,"1,12,23,27" );
$cbo_fabric_source     = create_drop_down( "cbo_fabric_source", 140, $fabric_source,"", 1, "-- Select --", "","", "", "");
$cbo_pay_mode          = create_drop_down( "cbo_pay_mode", 140, $pay_mode,"", 1, "-- Select Pay Mode --", 0, "load_drop_down( 'requires/multi_job_additional_fabric_booking_controller', this.value+'_'+$('#cbo_company_name').val(), 'load_drop_down_suplier', 'sup_td'); fnc_greyFabPurchase(this.value);","","1,2,3,4,5" );
$cbo_supplier_name     = create_drop_down( "cbo_supplier_name", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=9 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "fill_attention(this.value)",0 );
$cbo_currency          = create_drop_down( "cbo_currency", 140, $currency,"",1, "-- Select --", 2, "check_exchange_rate()",0 );
$cbo_source            = create_drop_down( "cbo_source", 140, $source,"", 1, "-- Select Source --", "", "","" );
$cbo_ready_to_approved = create_drop_down( "cbo_ready_to_approved", 140, $yes_no,"", 1, "-- Select--", 2, "","","" );
$cbo_level             = create_drop_down( "cbo_level", 140, $level_arr,"", 0, "", 2, "","","" );
$buttons               = load_submit_buttons( $permission, "fnc_additional_fabric_booking", 0,0 ,"reset_form('fabricbooking_1','booking_list_view*booking_list_view_list*print_list', '', 'cbo_booking_year,2022*cbo_booking_month, ".$current_month."*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_percent,100*txt_booking_date, ".$date."', disable_enable_fields( 'cbo_currency*cbo_company_name*cbo_supplier_name*cbo_level*cbo_buyer_name', 0), 'cbo_currency*cbo_booking_year*cbo_booking_month*cbo_pay_mode*cbo_source*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_level*cbo_company_name*cbo_buyer_name*cbo_fabric_natu*cbo_fabric_source*cbouom')",1) ;

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
		hide_left_menu("Button1");
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
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/multi_job_additional_fabric_booking_controller" );
				set_button_status(1, permission, 'fnc_additional_fabric_booking',1);
				fnc_show_booking_list();
				get_php_form_data( document.getElementById('cbo_company_name').value, 'company_wise_report_button_setting','requires/multi_job_additional_fabric_booking_controller' );
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
				get_php_form_data( id.value+"_"+cbo_fabric_natu+"_"+cbouom+"_"+cbo_fabric_source, "populate_order_data_from_search_popup", "requires/multi_job_additional_fabric_booking_controller" );
				check_month_setting();
				release_freezing();
			}
		}
	}

	function fnc_generate_booking(){ 

		if (form_validation('txt_booking_no*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source','Booking No*Order No*Fabric Nature*Fabric Source')==false){
			return;
		}
		else{
			var data="action=generate_fabric_booking"+get_submitted_data_string('txt_booking_no*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*cbo_company_name*cbo_buyer_name*cbouom*cbo_fabric_description*cbo_level*cbo_item_from',"../../"); 
			http.open("POST","requires/multi_job_additional_fabric_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_generate_booking_reponse;
		}
	}

	function fnc_generate_booking_reponse(){
		if(http.readyState == 4){
			document.getElementById('booking_list_view').innerHTML=http.responseText;
					set_button_status(0, permission, 'fnc_additional_fabric_booking_dtls',2);
				set_all_onclick();
		}
	}

	function fnc_additional_fabric_booking( operation ){
		freeze_window(operation);
		var data_all="";
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

		if(document.getElementById('id_approved_id').value==1){
			alert("This booking is approved")
			release_freezing();
			return;
		}
		var month_set_id=$('#month_id').val();
		if(month_set_id==1){
			if (form_validation('cbo_booking_month','Booking Month')==false){
				release_freezing();
				return;
			}
		}

		/* if(mandatory_field) 
		{
			if (form_validation(mandatory_field,mandatory_message)==false)
			{
				release_freezing();
				return;
			}
		} */
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
		
		if (form_validation('cbo_company_name*cbo_buyer_name*cbo_fabric_natu*cbouom*cbo_fabric_source*txt_booking_date*cbo_pay_mode*cbo_supplier_name*txt_delivery_date*cbo_source*cbo_item_from*cbo_level','Company Name*Buyer Name*Fabric Nature*UoM*Fabric Source*Booking Date*Pay Mode*Supplier*Delivery Date*Source*Item From*Level')==false){
			release_freezing();
			return;
		}
		if (document.getElementById('cbo_pay_mode').value!=3 && document.getElementById('cbo_supplier_name').value==0){
			alert("Select Supplier Name")
			release_freezing();
			return;
		}
		else{
			data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_company_name*cbo_buyer_name*cbo_fabric_natu*cbouom*cbo_fabric_source*txt_booking_date*cbo_pay_mode*cbo_supplier_name*cbo_currency*txt_exchange_rate*txt_delivery_date*cbo_source*txt_attention*txtdelivery_address*txt_tenor*cbo_item_from*cbo_level*cbo_ready_to_approved*txt_remarks*update_id',"../../")+"&delete_type="+delete_type;
			var data="action=save_update_delete&operation="+operation+data_all+'&delete_cause='+delete_cause;
			http.open("POST","requires/multi_job_additional_fabric_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_additional_fabric_booking_reponse;
		}
	}

	function fnc_additional_fabric_booking_reponse(){
		if(http.readyState == 4){
			var reponse=trim(http.responseText).split('**');
			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1) {
				document.getElementById('txt_booking_no').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
				set_button_status(1, permission, 'fnc_additional_fabric_booking',1);
				show_msg(trim(reponse[0]));
				$("#cbo_company_name").attr("disabled","disabled");
				$("#cbo_buyer_name").attr("disabled","disabled");
				$("#cbo_item_from").attr("disabled","disabled");
				$("#cbo_level").attr("disabled","disabled");
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

	function fnc_additional_fabric_booking_dtls( operation ){
		freeze_window(operation);
		var cbo_item_from=document.getElementById('cbo_item_from').value;
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
		if(cbo_item_from==1){
			if(document.getElementById('id_approved_id').value==1){
				alert("This booking is approved")
				release_freezing();
				return;
			}
			var row_num=$('#tbl_fabric_booking tr').length;
			var data_all=""; var z=1;
			for (var i=1; i<=row_num; i++){
				if (form_validation('txt_order_no_id*cbo_fabric_description*txt_booking_no','Order No*Fabric Description*Booking No')==false){
					release_freezing()
					return;
				}

				var cuqnty=(document.getElementById('cuqnty_'+i).value)*1
				var txtwoq=(document.getElementById('txtwoq_'+i).value)*1
				var txtbalqnty=(document.getElementById('txtbalqnty_'+i).value)*1
				var txtreqqnty=(document.getElementById('txtreqqnty_'+i).value)*1
				var txtwoqprev=(document.getElementById('txtwoqprev_'+i).value)*1

				if(operation==0){
					var totwoqty=number_format(cuqnty+txtwoq,4,'.','');
					if(totwoqty>txtreqqnty){
						alert("You are exceeding your balance.");
						release_freezing();
						return;
					}
				}
				else if(operation==1){
					var totwoqty=(cuqnty-txtwoqprev)+txtwoq;
					var ttotwoqty=number_format(totwoqty,4,'.','');
					if(ttotwoqty>txtreqqnty){
						alert("You are exceeding your balance.");
						release_freezing();
						return;
					}
				}			
				data_all+="&txtjob_" + z + "='" + $('#txtjob_'+i).val()+"'"+"&txtpoid_" + z + "='" + $('#txtpoid_'+i).val()+"'"+"&txtgmtcolor_" + z + "='" + $('#txtgmtcolor_'+i).val()+"'"+"&txtitemcolor_" + z + "='" + $('#txtitemcolor_'+i).val()+"'"+"&txtbalqnty_" + z + "='" + $('#txtbalqnty_'+i).val()+"'"+"&txtreqqnty_" + z + "='" + $('#txtreqqnty_'+i).val()+"'"+"&txtfinreqqnty_" + z + "='" + $('#txtfinreqqnty_'+i).val()+"'"+"&txtwoq_" + z + "='" + $('#txtwoq_'+i).val()+"'"+"&txtrate_" + z + "='" + $('#txtrate_'+i).val()+"'"+"&txtamount_" + z + "='" + $('#txtamount_'+i).val()+"'"+"&txtadj_" + z + "='" + $('#txtadj_'+i).val()+"'"+"&txtremark_" + z + "='" + $('#txtremark_'+i).val()+"'"+"&bookingid_" + z + "='" + $('#bookingid_'+i).val()+"'"+"&txtpre_cost_fabric_cost_dtls_id_" + z + "='" + $('#txtpre_cost_fabric_cost_dtls_id_'+i).val()+"'"+"&txtcolortype_" + z + "='" + $('#txtcolortype_'+i).val()+"'"+"&txtconstruction_" + z + "='" + $('#txtconstruction_'+i).val()+"'"+"&txtitrmref_" + z + "='" + $('#txtitrmref_'+i).val()+"'"+"&txtcompositi_" + z + "='" + $('#txtcompositi_'+i).val()+"'"+"&txtgsm_weight_" + z + "='" + $('#txtgsm_weight_'+i).val()+"'"+"&txtdia_" + z + "='" + $('#txtdia_'+i).val()+"'"+"&txtacwoq_" + z + "='" + $('#txtacwoq_'+i).val()+"'"+"&process_" + z + "='" + $('#process_'+i).val()+"'"+"&hscode_" + z + "='" + $('#txthscode_'+i).val()+"'"+"&preconskg_" + z + "='" + $('#preconskg_'+i).val()+"'";
				z++;
			}
			var cbo_level=document.getElementById('cbo_level').value;
			var json_data=document.getElementById('json_data').value;
			if(cbo_level==1){
				var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*cbo_fabric_description*txt_booking_no*update_id*cbo_pay_mode*lib_tna_intregrate*cbo_item_from',"../../")+data_all;
			}
			if(cbo_level==2){
				var data="action=save_update_delete_dtls_job_level&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*cbo_fabric_description*txt_booking_no*update_id*cbo_pay_mode*lib_tna_intregrate*cbo_item_from',"../../")+data_all+'&json_data='+json_data+"&delete_cause="+delete_cause;
			}
		}
		if(cbo_item_from==2){
			var row_num=$('#tbl_fabric_booking tr').length;
			var data_all=""; var z=1;
			for (var i=1; i<=row_num; i++){
				if (form_validation('uom_'+i,'UOM')==false)
				{
					release_freezing();
					return;
				}
				var rate=$('#txtrate_'+i).val()*1;
				if(rate <= 0){
					alert("Rate Can not be null");
					release_freezing();
					return;
				}
				var cuqnty=(document.getElementById('cuqnty_'+i).value)*1
				var txtwoq=(document.getElementById('txtwoq_'+i).value)*1
				var txtbalqnty=(document.getElementById('txtbalqnty_'+i).value)*1
				var txtreqqnty=(document.getElementById('txtreqqnty_'+i).value)*1
				var txtwoqprev=(document.getElementById('txtwoqprev_'+i).value)*1
			
				data_all+="&txtjob_" + z + "='" + $('#txtjob_'+i).val()+"'"+"&txtpoid_" + z + "='" + $('#txtpoid_'+i).val()+"'"+"&txtgmtcolor_" + z + "='" + $('#txtgmtcolor_'+i).val()+"'"+"&txtitemcolor_" + z + "='" + $('#txtitemcolor_'+i).val()+"'"+"&txtbalqnty_" + z + "='" + $('#txtbalqnty_'+i).val()+"'"+"&txtreqqnty_" + z + "='" + $('#txtreqqnty_'+i).val()+"'"+"&txtfinreqqnty_" + z + "='" + $('#txtfinreqqnty_'+i).val()+"'"+"&txtwoq_" + z + "='" + $('#txtwoq_'+i).val()+"'"+"&txtrate_" + z + "='" + $('#txtrate_'+i).val()+"'"+"&txtamount_" + z + "='" + $('#txtamount_'+i).val()+"'"+"&txtadj_" + z + "='" + $('#txtadj_'+i).val()+"'"+"&txtremark_" + z + "='" + $('#txtremark_'+i).val()+"'"+"&bookingid_" + z + "='" + $('#bookingid_'+i).val()+"'"+"&lib_yarn_id_" + z + "='" + $('#lib_yarn_id_'+i).val()+"'"+"&txtcolortype_" + z + "='" + $('#txtcolortype_'+i).val()+"'"+"&txtconstruction_" + z + "='" + $('#txtconstruction_'+i).val()+"'"+"&txtcompositi_" + z + "='" + $('#txtcompositi_'+i).val()+"'"+"&txtgsm_weight_" + z + "='" + $('#txtgsm_weight_'+i).val()+"'"+"&txtdia_" + z + "='" + $('#txtdia_'+i).val()+"'"+"&txtacwoq_" + z + "='" + $('#txtacwoq_'+i).val()+"'"+"&process_" + z + "='" + $('#process_'+i).val()+"'"+"&hscode_" + z + "='" + $('#txthscode_'+i).val()+"'"+"&preconskg_" + z + "='" + $('#preconskg_'+i).val()+"'"+"&txtwidthtype_" + z + "='" + $('#txtwidthtype_'+i).val()+"'"+"&txtbodypart_" + z + "='" + $('#txtbodypart_'+i).val()+"'"+"&uom_" + z + "='" + $('#uom_'+i).val()+"'";
				z++;
			}
			var data="action=save_update_delete_library_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*cbo_fabric_description*txt_booking_no*update_id*cbo_pay_mode*lib_tna_intregrate*cbo_item_from',"../../")+data_all;

		}
		//uom_
		http.open("POST","requires/multi_job_additional_fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_additional_fabric_booking_dtls_reponse;
	}

	function fnc_additional_fabric_booking_dtls_reponse(){
		if(http.readyState == 4){
			 var reponse=trim(http.responseText).split('**');
			 if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1 || parseInt(trim(reponse[0]))==2){
				fnc_show_booking_list();
				reset_form('','booking_list_view','','');
				release_freezing();
				show_msg(trim(reponse[0]));
			 }
			if(trim(reponse[0])==10)
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
			if(trim(reponse[0])=='budget_updated'){
				alert("Budget Fabric Sensitive Changed Found,\n Please go to Select Item: Popup and call the Job/Fabric again.");
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

	function fnc_show_booking_list(){
		var data="action=show_fabric_booking_list"+get_submitted_data_string('txt_booking_no*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*cbo_company_name*cbo_buyer_name*cbouom*cbo_fabric_description*cbo_level*cbo_item_from',"../../");
		http.open("POST","requires/multi_job_additional_fabric_booking_controller.php",true);
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
			var data="action=show_fabric_booking"+get_submitted_data_string('txt_booking_no*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*cbo_company_name*cbo_buyer_name*cbouom*cbo_fabric_description*cbo_level*cbo_item_from',"../../");
			http.open("POST","requires/multi_job_additional_fabric_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_show_booking_reponse;
		}
	}

	function fnc_show_booking_reponse(){
		if(http.readyState == 4){
			document.getElementById('booking_list_view').innerHTML=http.responseText;
			set_button_status(1, permission, 'fnc_additional_fabric_booking_dtls',2);
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

	function generate_fabric_report(type,mail_send_data,mail_body){
		if ( form_validation('txt_booking_no','Booking No')==false ){
			return;
		}
		else{
			var show_yarn_rate='';
			var report_title=$( "div.form_caption" ).html();
			var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*cbo_item_from',"../../")+'&report_title='+report_title+'&show_yarn_rate='+show_yarn_rate+'&mail_send_data='+mail_send_data+'&mail_body='+mail_body+'&path=../../';
			freeze_window(5);
			http.open("POST","requires/multi_job_additional_fabric_booking_controller.php",true);
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
	function openmypage_unapprove_request(){
		if (form_validation('txt_booking_no','Booking Number')==false){
			return;
		}
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		var txt_un_appv_request=document.getElementById('txt_un_appv_request').value;
		var data=txt_booking_no+"_"+txt_un_appv_request;
		var title = 'Un Approval Request';
		var page_link = 'requires/multi_job_additional_fabric_booking_controller.php?data='+data+'&action=unapp_request_popup';
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
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/multi_job_additional_fabric_booking_controller');
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
		var response=return_global_ajax_value( cbo_company_name, 'check_month_maintain', '', 'requires/multi_job_additional_fabric_booking_controller');
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
		var attention=return_global_ajax_value(supplier_id+"_"+cbo_pay_mode, 'get_attention_name', '', 'requires/multi_job_additional_fabric_booking_controller');
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
		var cbo_fabric_natu=document.getElementById('cbo_fabric_natu').value;
		var cbouom=document.getElementById('cbouom').value;
		var cbo_fabric_source=document.getElementById('cbo_fabric_source').value;
		//get_php_form_data( po_id+"_"+cbo_fabric_natu+"_"+cbouom+"_"+cbo_fabric_source, "populate_order_data_from_search_popup", "requires/multi_job_additional_fabric_booking_controller" );
		//document.getElementById('txt_order_no').value=po_number;
		document.getElementById('txt_order_no_id').value=po_id;
		document.getElementById('cbo_fabric_description').value=precost_id;
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
			//alert("This booking used in PI Table. So Adding or removing order is not allowed")
			return;
		}

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
		//data_all+="&txtjob_1=&txtpoid_1="+po_id+"&txtpre_cost_fabric_cost_dtls_id_1="+precost_id+"&bookingid_1="+booking_id+"&cbo_fabric_description="+precost_id;
		var cbo_level=document.getElementById('cbo_level').value;
		if(cbo_level==1){
			var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+z+dltsid+data_all+"&delete_cause="+delete_cause;
		}
		else if(cbo_level==2){
			var data="action=save_update_delete_dtls_job_level&operation="+operation+'&total_row='+z+dltsid+data_all+"&delete_cause="+delete_cause;
		}
		/*alert(data);release_freezing()
			return;*/
		http.open("POST","requires/multi_job_additional_fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_additional_fabric_booking_dtls_reponse;
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

	function call_print_button_for_mail(mail_address,mail_body,type){
		var response=return_global_ajax_value(document.getElementById('cbo_company_name').value, 'send_mail_report_setting_first_select', '', 'requires/multi_job_additional_fabric_booking_controller');
		var report_id=response.split(",");

		if(report_id[0]==143){generate_fabric_report('show_fabric_booking_report_urmi','1___'+mail_address,mail_body);}


	}
	function check_paymode()
	{
		$('#cbo_pay_mode').val('');
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
            <legend>Multi Job wise Additional Fabric Booking</legend>
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
						<td width="140"><?=create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "get_php_form_data( this.value, 'populate_variable_setting_data', 'requires/multi_job_additional_fabric_booking_controller' ); check_exchange_rate(); get_php_form_data( this.value, 'company_wise_report_button_setting','requires/multi_job_additional_fabric_booking_controller' )","","" ); ?></td>
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
							terms_condition(608,'txt_booking_no','../../','');
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
        <form name="servicebookingknitting_2"  autocomplete="off" id="servicebookingknitting_2">
            <fieldset style="width:950px;">
            <legend title="V3">Booking Item Form &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            Select Item: <input class="text_boxes" type="text" style="width:160px" onDblClick="fnc_process_data();" readonly placeholder="Double Click" name="txt_select_item" id="txt_select_item"/>
            <input class="text_boxes" type="hidden" style="width:160px"  readonly placeholder="Double Click" name="txt_order_no_id" id="txt_order_no_id"/>
            <input class="text_boxes" type="hidden" style="width:160px"  readonly placeholder="Double Click" name="cbo_fabric_description" id="cbo_fabric_description"/></legend>
            <div id="booking_list_view"><font id="save_sms" style="color:#F00">Select new Item</font></div>
            <?=load_submit_buttons( $permission, "fnc_additional_fabric_booking_dtls", 0,0 ,"",2) ; ?>
			<div id="print_list">
				<input type="button" value="Print" onClick="generate_fabric_report('print_booking_report')"  style="width:100px;" name="print" id="print" class="formbutton" />
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
function claculate_amount(i){
	var woq=document.getElementById('txtacwoq_'+i).value*1;
	var rate=document.getElementById('txtrate_'+i).value*1;
	var pre_cost_rate=$('#txtrate_'+i).attr('data-pre-cost-rate');
	var current_cost_rate=$('#txtrate_'+i).attr('data-current-rate');
	pre_cost_rate=pre_cost_rate*1;
	current_cost_rate=current_cost_rate*1;
	var cbo_item_from=document.getElementById('cbo_item_from').value;
	if(cbo_item_from==1){
		if(rate==pre_cost_rate || rate<pre_cost_rate){
			alert("Rate Can not be Equal Or less than precost rate");
			document.getElementById('txtrate_'+i).value=current_cost_rate;
			return;
		}
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


function fnc_process_data(){
	if (form_validation('cbo_company_name*txt_booking_no','Company*Booking No')==false){
		return;
	}
	else{
		var garments_nature=document.getElementById('garments_nature').value;
		//var cbo_booking_month=document.getElementById('cbo_booking_month').value;
		//var cbo_booking_year=document.getElementById('cbo_booking_year').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var cbo_currency=document.getElementById('cbo_currency').value;
		var cbo_fabric_natu=document.getElementById('cbo_fabric_natu').value;
		var cbouom=document.getElementById('cbouom').value;
		//var cbo_brand_id=document.getElementById('cbo_brand_id').value;
		var cbo_fabric_source=document.getElementById('cbo_fabric_source').value;
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		var cbo_item_from=document.getElementById('cbo_item_from').value; 
	    var page_link='requires/multi_job_additional_fabric_booking_controller.php?action=fabric_search_popup';
		var title='Partial Fabric Booking Search';
		page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&cbo_currency='+cbo_currency+'&cbo_fabric_natu='+cbo_fabric_natu+'&cbouom='+cbouom+'&cbo_fabric_source='+cbo_fabric_source+'&txt_booking_no='+txt_booking_no+'&cbo_item_from='+cbo_item_from;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_selected_id");
			var theemail2=this.contentDoc.getElementById("txt_pre_cost_dtls_id");
			var theemail3=this.contentDoc.getElementById("txt_selected_po");
			if (theemail.value!=""){
				document.getElementById('txt_select_item').value=theemail.value;
				document.getElementById('txt_order_no_id').value=theemail3.value;
				document.getElementById('cbo_fabric_description').value=theemail2.value;
				fnc_generate_booking();
				fnc_tna_date();
			}
		}
	}
}

function fnc_tna_date(){
	var order_no_id=document.getElementById('txt_order_no_id').value;
	var fabric_source=document.getElementById('cbo_fabric_source').value;
	var fabric_natu=document.getElementById('cbo_fabric_natu').value;
		var booking_date=document.getElementById('txt_booking_date').value;
		var delivery_date=document.getElementById('txt_delivery_date').value;
		var txt_tna_date=document.getElementById('txt_tna_date').value;
		
		get_php_form_data( order_no_id+'_'+fabric_source+'_'+fabric_natu, "populate_order_data_from_search_popup", "requires/multi_job_additional_fabric_booking_controller" );
	
			var delivery_date_chk=document.getElementById('txt_delivery_date').value;
			var txt_tna_date=document.getElementById('txt_tna_date').value;
			if(delivery_date_chk!="")
			{
				//alert(txt_delivery_date_chk+'='+txt_booking_date_chk+'='+txt_tna_date_chk);
				if(date_compare($('#txt_booking_date').val(), delivery_date_chk)==false){
					alert("Delivery Date Not Allowed Less than Booking Date.");
					document.getElementById('txt_delivery_date').value='';
					//document.getElementById('txt_tna_date').value='';
					//release_freezing();
					//return;
				}
			}
}
function open_body_part_popup(i){
	var cbofabricnature=document.getElementById('cbo_fabric_natu').value;
	var libyarncountdeterminationid ='';
	//var libyarncountdeterminationid =document.getElementById('libyarncountdeterminationid_'+i).value
	var page_link='requires/multi_job_additional_fabric_booking_controller.php?action=body_part_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Body Part List', 'width=460px,height=450px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var id=this.contentDoc.getElementById("gid");
		var name=this.contentDoc.getElementById("gname");
		var type=this.contentDoc.getElementById("gtype");
		document.getElementById('txtbodyparttext_'+i).value=name.value;
		document.getElementById('txtbodypart_'+i).value=id.value;
		document.getElementById('txtbodyparttype_'+i).value=type.value;
		//sum_yarn_required()
	}
}
function open_fabric_decription_popup(i)
{
	var cbo_company_name=$('#cbo_company_name').val();
	var txtbodypart=$('#txtbodypart_'+i).val();
	var cbocolortype=$('#txtcolortype_'+i).val();
	var cbofabricsource=$('#cbo_fabric_source').val();
	var txt_job_no=$('#txtjob_'+i).val();
	var cbofabricnature=document.getElementById('cbo_fabric_natu').value;
	
	var libyarncountdeterminationid =document.getElementById('lib_yarn_id_'+i).value
	var page_link='requires/multi_job_additional_fabric_booking_controller.php?action=fabric_description_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid+'&cbo_company_name='+cbo_company_name+'&txtbodypart='+txtbodypart+'&cbocolortype='+cbocolortype+'&cbofabricsource='+cbofabricsource+'&txt_job_no='+txt_job_no;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=960px,height=450px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var fab_des_id=this.contentDoc.getElementById("fab_des_id");
		var fab_nature_id=this.contentDoc.getElementById("fab_nature_id");
		var fab_desctiption=this.contentDoc.getElementById("fab_desctiption");
		var fab_gsm=this.contentDoc.getElementById("fab_gsm");
		var yarn_desctiption=this.contentDoc.getElementById("yarn_desctiption");
		var construction=this.contentDoc.getElementById("construction");
		var composition=this.contentDoc.getElementById("composition");
		document.getElementById('lib_yarn_id_'+i).value=fab_des_id.value;
		/* document.getElementById('fabricdescription_'+i).value=fab_desctiption.value;
		//document.getElementById('fabricdescription_'+i).title=fab_desctiption.value; */
		//document.getElementById('cbofabricnature_'+i).value=fab_nature_id.value;
		document.getElementById('txtgsm_weight_'+i).value=fab_gsm.value;
		//document.getElementById('yarnbreackdown_'+i).value=yarn_desctiption.value;
		document.getElementById('txtconstruction_'+i).value=construction.value;
		document.getElementById('txtcompositi_'+i).value=composition.value;
		//sum_yarn_required()
	}
}

</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$( document ).ready(function() {
	$('#cbo_buyer_name').val(0);
	load_drop_down( 'requires/multi_job_additional_fabric_booking_controller', document.getElementById('cbo_pay_mode').value+'_'+$('#cbo_company_name').val(), 'load_drop_down_suplier', 'sup_td' )
});
</script>
</html>
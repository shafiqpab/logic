<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Woven Garments Partial Fabric Booking
Functionality	         :
JS Functions	         :
Created by		         :	zakaria joy
Creation date 	         : 	30-07-2020
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
//echo count($_SESSION['logic_erp']['data_arr'][271]);
//echo $_SESSION['logic_erp']['mandatory_field'][271][2].'=AAAAAAAAA';


echo load_html_head_contents("Woven Fabric Booking", "../../", 1, 1,$unicode,1,'');
//--------------------------------------------------------------------------------------------------------------------
$date                  = date('d-m-Y');
$level_arr             = array(1=>"PO Level",2=>"Job Level");
$buyer_cond            = set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond          = set_user_lavel_filtering(' and comp.id','company_id');
$cbo_booking_month     = create_drop_down( "cbo_booking_month", 90, $months,"", 1, "-- Select --", "", "",0 );
$cbo_booking_year      = create_drop_down( "cbo_booking_year", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
$cbo_booking_month     = create_drop_down( "cbo_booking_month", 90, $months,"", 1, "-- Select --", "", "",0 );
$cbo_booking_year      = create_drop_down( "cbo_booking_year", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
$cbo_company_name      = create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company  comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", "", "load_drop_down( 'requires/woven_partial_fabric_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );check_month_setting();validate_suplier();get_php_form_data( this.value, 'company_wise_report_button_setting','requires/woven_partial_fabric_booking_controller' );",0,"" );
$cbo_buyer_name		   = create_drop_down( "cbo_buyer_name", 172, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "","","" );
$cbo_fabric_natu       = create_drop_down( "cbo_fabric_natu", 110, $item_category,"", 1, "-- Select --", 3,$onchange_func, $is_disabled, "2,3");
$cbouom                = create_drop_down( "cbouom", 60, $unit_of_measurement,'', 1, '-Uom-', $row[csf('uom')], "",$disabled,"1,12,23,27" );
$cbo_fabric_source     = create_drop_down( "cbo_fabric_source", 172, $fabric_source,"", 1, "-- Select --", 2,"", "", "2,3,4");
$cbo_pay_mode          = create_drop_down( "cbo_pay_mode", 172, $pay_mode,"", 1, "-- Select Pay Mode --", 2, "load_drop_down( 'requires/woven_partial_fabric_booking_controller', this.value, 'load_drop_down_suplier', 'sup_td' )","","1,2,3,4,5" );
$cbo_supplier_name     = create_drop_down( "cbo_supplier_name", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=9 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "fill_attention(this.value)",0 );
$cbo_currency          = create_drop_down( "cbo_currency", 172, $currency,"",1, "-- Select --", 2, "check_exchange_rate()",0 );
$cbo_source            = create_drop_down( "cbo_source", 172, $source,"", 1, "-- Select Source --", "", "","" );
$cbo_ready_to_approved = create_drop_down( "cbo_ready_to_approved", 172, $yes_no,"", 1, "-- Select--", 2, "","","" );
$cbo_level             = create_drop_down( "cbo_level", 172, $level_arr,"", 0, "", 2, "","","" );
$cbo_season_year       = create_drop_down( "cbo_season_year", 172, create_year_array(),"", 1,"-Year-", 1, "",0,"" );
$cbo_season_id       = create_drop_down( "cbo_season_id", 172, $blank_array,"", 1,"-Season-", 1, "",0,"" );
$cbo_brand_id       = create_drop_down( "cbo_brand_id", 172, $blank_array,"", 1,"-Brand-", 1, "",0,"" );

$buttons               = load_submit_buttons( $permission, "fnc_fabric_booking", 0,0 ,"fnResetForm();reset_form('fabricbooking_1','','booking_list_view*booking_list_view_list','cbo_pay_mode,5*cbo_booking_year,2014*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_percent,100*cbo_level,2*txt_booking_date,".$date."','disable_enable_fields(\'cbo_company_name*cbo_buyer_name*txt_colar_excess_percent*txt_cuff_excess_percent*cbo_level*txt_un_appv_request\',0)');",1) ;

$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';
	var mandatory_field ='';
	var mandatory_message='';
	var mandatory_field=new Array();
	var mandatory_message=new Array();
	var field_level_data=new Array();

	<?
	if(count($_SESSION['logic_erp']['mandatory_field'][271])>0)
	{
	echo "var mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][271]) . "';\n";
	echo "var mandatory_message = '". implode('*',$_SESSION['logic_erp']['mandatory_message'][271]) . "';\n";

	}
	if(count($_SESSION['logic_erp']['data_arr'][271])>0)
	{
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][271] );
	echo "var field_level_data= ". $data_arr . ";\n";
	}
	?>
	function math_operation_custom( target_fld, value_fld, operator, fld_range, dec_point)
	{
		//number_format_common( number, dec_type, comma, path, currency )
		//var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
		//	math_operation( des_fil_id, field_id, '+', rowCount,ddd);

		if (!dec_point) var dec_point=0;
		//alert(fld_range);
		//alert(dec_point.dec_type);

		if(!fld_range) var fld_range="";
		if (fld_range=="")
		{
			value_fld=value_fld.split('*');
			var tot="";
			if (operator=="+")
			{

				for (var i=1;i<value_fld.length; i++)
				{
					tot=(tot*1)+(document.getElementById(value_fld[i]).value*1)
				}
				document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma, dec_point.currency) ; //(document.getElementById(value_fld[0]).value*1)+(document.getElementById(value_fld[1]).value*1)
			}

			else if (operator=="-")
			{
				var tot =(document.getElementById(value_fld[0]).value*1)-(document.getElementById(value_fld[1]).value*1);
				document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma, dec_point.currency);
			}
			else if (operator=="*")
			{
				var tot=1;
				for (var i=0;i<value_fld.length; i++)
				{
					tot=(tot*1)*(document.getElementById(value_fld[i]).value*1)
				}
				//document.getElementById(target_fld).value=tot.toFixed(4); //(document.getElementById(value_fld[0]).value*1)*(document.getElementById(value_fld[1]).value*1)
				document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma, dec_point.currency); //(document.getElementById(value_fld[0]).value*1)*(document.getElementById(value_fld[1]).value*1)
			}
			else if (operator=="/")
			{
				//document.getElementById(target_fld).value=((document.getElementById(value_fld[0]).value*1)/(document.getElementById(value_fld[1]).value*1)).toFixed(4)
				var tot=((document.getElementById(value_fld[0]).value*1)/(document.getElementById(value_fld[1]).value*1))
				document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma, dec_point.currency) ;
			}
		}
		else
		{
			//alert(dec_point);
			//alert(value_fld);
			//alert(target_fld);
			var tot=0;
			for (var i=1; i<=fld_range; i++)
			{
				tot=(tot*1) + (document.getElementById(value_fld+i).value*1);
			}
			//document.getElementById(target_fld).value=tot.toFixed(2);
			document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma,dec_point.currency);

		}
	}
	function openmypage_booking(page_link,title){
		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var company=$("#cbo_company_name").val()*1;
		var cbo_buyer_name=$("#cbo_buyer_name").val()*1;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company+'&cbo_buyer_name='+cbo_buyer_name, title, 'width=1090,height=480px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			if (theemail.value!=""){
				reset_form('fabricbooking_1','booking_list_view','','txt_booking_date,<? echo date("d-m-Y"); ?>');
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/woven_partial_fabric_booking_controller" );
				set_button_status(1, permission, 'fnc_fabric_booking',1);
				fnc_show_booking_list();
				get_php_form_data( document.getElementById('cbo_company_name').value, 'company_wise_report_button_setting','requires/woven_partial_fabric_booking_controller' );
			}
			$("#cbo_company_name").attr("disabled","disabled");
			$("#cbo_buyer_name").attr("disabled","disabled");
		}
	}
	function openmypage_from_style(page_link,title){
		var company=$("#cbo_company_name").val()*1;
		var cbo_buyer_name=$("#cbo_buyer_name").val()*1;
		var cbo_fabric_source=$("#cbo_fabric_source").val()*1;
		if(cbo_fabric_source !=4){
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company+'&cbo_buyer_name='+cbo_buyer_name, title, 'width=1090,height=480px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_style");
			if (theemail.value!=""){
				var style_data=theemail.value.split("_");
				$('#txt_from_style').val(style_data[0]);
				$('#from_style_id').val(style_data[1]);
			}
		}
	}

	function openmypage_order(page_link,title){
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
				get_php_form_data( id.value+"_"+cbo_fabric_natu+"_"+cbouom+"_"+cbo_fabric_source, "populate_order_data_from_search_popup", "requires/woven_partial_fabric_booking_controller" );
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
			var data="action=generate_fabric_booking"+get_submitted_data_string('txt_booking_no*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*cbo_company_name*cbo_supplier_name*cbo_buyer_name*cbouom*cbo_fabric_description*cbo_level*from_style_id',"../../");
			http.open("POST","requires/woven_partial_fabric_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_generate_booking_reponse;
		}
	}
	function fnc_generate_booking_reponse(){
		if(http.readyState == 4){
			document.getElementById('booking_list_view').innerHTML=http.responseText;
					set_button_status(0, permission, 'fnc_fabric_booking_dtls',2);
				set_all_onclick();
		}
	}
	function fnc_fabric_booking( operation ){
		freeze_window(operation);
		/*if(operation==2){
			alert('Delete Restricted');
			release_freezing();
			return;
	    }*/
	    if(operation==1 || operation==2)
	    {
	    	var approved_status = trim(return_global_ajax_value(document.getElementById('txt_booking_no').value, 'check_approved_status', '', 'requires/woven_partial_fabric_booking_controller'));
	    	console.log(approved_status);
	    	var expt=approved_status.split("***");
	    	var approved_yes_no=expt[0]*1;
	    	if(approved_yes_no==1)
	    	{
	    		alert('Booking is approved any change not allowed');
	    		release_freezing();
	    		return;
	    	}
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

		if(mandatory_field !='')
		{
			if (form_validation(mandatory_field,mandatory_message)==false)
			{
				release_freezing();
				return;
			}
		}
		var delivery_date=$('#txt_delivery_date').val();
		if(date_compare($('#txt_booking_date').val(), delivery_date)==false){
			alert("Delivery Date Not Allowed Less than Booking Date.");
			release_freezing();
			return;
		}
		if (form_validation('cbo_company_name*cbo_buyer_name*txt_booking_date*txt_delivery_date*cbo_supplier_name*cbo_pay_mode*cbo_fabric_source*cbo_fabric_natu','Company Name*Buyer Name*Booking Date*Delivery Date*Supplier Name*Pay Mode*Fabric Source* Fabric Nature')==false){
			release_freezing();
			return;
		}
		if (document.getElementById('cbo_pay_mode').value!=3 && document.getElementById('cbo_supplier_name').value==0){
			alert("Select Supplier Name")
			release_freezing();
			return;
		}
		else{
			var data="action=save_update_delete&operation="+operation+"&delete_cause="+delete_cause+get_submitted_data_string('cbo_company_name*cbo_buyer_name*update_id*txt_booking_no*cbo_fabric_natu*cbo_fabric_source*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_booking_month*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_source*cbo_booking_year*txt_booking_percent*txt_colar_excess_percent*txt_cuff_excess_percent*cbo_ready_to_approved*processloss_breck_down*txt_fabriccomposition*txt_intarnal_ref*txt_file_no*cbouom*txt_remark*cbo_level*delivery_address*cbo_season_year*cbo_season_id*cbo_brand_id*cbo_payterm_id*txt_sup_rev_date*txt_tenor*from_style_id',"../../");
			http.open("POST","requires/woven_partial_fabric_booking_controller.php",true);
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
				$("#cbo_supplier_name").attr("disabled","disabled");
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
				alert("This booking is approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='sal1'){
				alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='pi1'){
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='recv1'){
			alert("Receive Number Found :"+trim(reponse[2])+"\n So Delete Not Possible")
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
		if(operation==1 || operation==2)
		{
			var approved_status = trim(return_global_ajax_value(document.getElementById('txt_booking_no').value, 'check_approved_status', '', 'requires/woven_partial_fabric_booking_controller'));
			//console.log(approved_status);
			var expt=approved_status.split("***");
			var approved_yes_no=expt[0]*1;
			if(approved_yes_no==1)
			{
				alert('Booking is approved any change not allowed');
				release_freezing();
				return;
			}
		}
		if(operation==0)
		{
			var txt_select_item=$("#txt_select_item").val();
			var booking_no_chk=$("#txt_booking_no").val();
			if(txt_select_item=='' || booking_no_chk=='')
			{
				 
				if (form_validation('txt_booking_no*txt_select_item','Booking No*Select Item')==false){
				release_freezing()
				return;
				}
			}
		}

		var row_num=$('#tbl_fabric_booking tr').length;
		if(row_num==0)
		{
			alert('Details Part not found.');
			release_freezing()
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
		var cbo_fabric_source=$('#cbo_fabric_source').val();
		var row_num=$('#tbl_fabric_booking tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++){
			if(cbo_fabric_source==4){
				if (form_validation('txtadj_'+i,'Adjust Qty.')==false){
				release_freezing()
				return;
			}
			}
			if (form_validation('txt_order_no_id*cbo_fabric_description*txt_booking_no','Order No*Fabric Description*Booking No')==false){
			release_freezing()
			return;
			}

			// CTO========
			/*if( (document.getElementById('txtbalqnty_'+i).value)*1<  (document.getElementById('txtwoq_'+i).value)*1)
			{
			alert("You are exceeding your balance.");
			release_freezing()
			return;
			}*/

			var cuqnty=(document.getElementById('cuqnty_'+i).value)*1
			var txtwoq=(document.getElementById('txtwoq_'+i).value)*1
			var txtbalqnty=(document.getElementById('txtbalqnty_'+i).value)*1
			var txtreqqnty=(document.getElementById('txtreqqnty_'+i).value)*1
			var txtwoqprev=(document.getElementById('txtwoqprev_'+i).value)*1
			
			var txtrate=(document.getElementById('txtrate_'+i).value)*1
			var csapprate=(document.getElementById('csapprate_'+i).value)*1
			if(csapprate>0)
			{
				if(txtrate>csapprate){
					 
						alert("You are exceeding your CS Approve Rate.\n Booking Rate="+txtrate+',CS Rate='+csapprate);
				        release_freezing()
				        return;
					 
				}	
			}
			
			

			if(operation==0){
				var totwoqty=cuqnty+txtwoq;
				console.log(`${totwoqty}  >${txtreqqnty}`)
				if(totwoqty>txtreqqnty){
					if((totwoqty-txtreqqnty)>0.0001)
					{
						alert("You are exceeding your balance.");
				        release_freezing()
				        return;
					}
				}
				var data=document.getElementById('cbo_level').value+'***'+document.getElementById('txtpoid_'+i).value+'***'+document.getElementById('txtpre_cost_fabric_cost_dtls_id_'+i).value+'***'+document.getElementById('txtgmtcolor_'+i).value+'***'+document.getElementById('txtdia_'+i).value+'***'+document.getElementById('process_'+i).value+'***'+document.getElementById('cbo_fabric_natu').value+'***'+document.getElementById('bookingid_'+i).value ; 
				var server_qnty = trim(return_global_ajax_value(data, 'server_side_validation_qnt', '', 'requires/woven_partial_fabric_booking_controller'));
				console.log(server_qnty);
				
				
				

				if(number_format(txtwoq, 6,".", "")>(server_qnty*1))
				{
					if((txtwoq-(server_qnty*1))>0.0001)
					{
						alert("You are exceeding your balance.");
				        release_freezing();
				        return;
					}
					
				}
				
				var po_level = trim(return_global_ajax_value(data, 'job_po_level_validation', '', 'requires/woven_partial_fabric_booking_controller'));
				
				 var level_reponse=po_level.split('**');
				 if(level_reponse[0]==100)
				 {
						 alert(level_reponse[1]);
						 release_freezing();
				        return;
				 }
				
				
			}
			if(operation==1){
				var totwoqty=(cuqnty-txtwoqprev)+txtwoq;
				
				if(totwoqty>txtreqqnty){
					
					if((totwoqty-txtreqqnty)>0.0001)
					{
						alert("You are exceeding your balance Qty.");
				       release_freezing()
				        return;
					}
					
				}
				var data=document.getElementById('cbo_level').value+'***'+document.getElementById('txtpoid_'+i).value+'***'+document.getElementById('txtpre_cost_fabric_cost_dtls_id_'+i).value+'***'+document.getElementById('txtgmtcolor_'+i).value+'***'+document.getElementById('txtdia_'+i).value+'***'+document.getElementById('process_'+i).value+'***'+document.getElementById('cbo_fabric_natu').value+'***'+document.getElementById('bookingid_'+i).value ; 
				var server_qnty = trim(return_global_ajax_value(data, 'server_side_validation_qnt', '', 'requires/woven_partial_fabric_booking_controller'));
				console.log(server_qnty);


				if(number_format (txtwoq, 6,".", "")>(server_qnty*1))
				{
					if((txtwoq-(server_qnty*1))>0.0001)
					{
						alert("You are exceeding your balance.");
				       release_freezing()
				      return;
					}
					
				}
				
			}
			/*	var txtbalqnty=(document.getElementById('txtbalqnty_'+i).value)*1
			if((txtbalqnty < 0 || txtbalqnty=="") && operation==0){
				alert("No balance Qty Found");
				release_freezing()
				return;
			}*/
			data_all=data_all+get_submitted_data_string('cbo_fabric_description*update_id*txt_booking_no*cbo_pay_mode*txtjob_'+i+'*txtpoid_'+i+'*txtgmtcolor_'+i+'*txtitemcolor_'+i+'*txtbalqnty_'+i+'*txtreqqnty_'+i+'*txtwoq_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtadj_'+i+'*txtremark_'+i+'*txtshrinkagel_'+i+'*txtshrinkagew_'+i+'*bookingid_'+i+'*txtpre_cost_fabric_cost_dtls_id_'+i+'*txtcolortype_'+i+'*txtconstruction_'+i+'*txtcompositi_'+i+'*txtgsm_weight_'+i+'*txtdia_'+i+'*txtacwoq_'+i+'*process_'+i+'*txtcutablewidth_'+i+'*cbouom_'+i,"../../",i);
		}

		var cbo_level=document.getElementById('cbo_level').value;
		var json_data=document.getElementById('json_data').value;
		//alert(json_data);
		if(cbo_level==1){
		var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+data_all+"&delete_cause="+delete_cause;
		}
		if(cbo_level==2){
		var data="action=save_update_delete_dtls_job_level&operation="+operation+'&total_row='+row_num+data_all+'&json_data='+json_data+"&delete_cause="+delete_cause+'&fabric_source='+cbo_fabric_source;
		}
		http.open("POST","requires/woven_partial_fabric_booking_controller.php",true);
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
			 if(trim(reponse[0])=='approved'){
				 alert("This booking is approved");
				 release_freezing();
				 return;
			 }
			  if(trim(reponse[0])=='Exceed'){
				 alert(reponse[1]);
				 release_freezing();
				 return;
			 }
			 if(trim(reponse[0])=='sal1'){
				 alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				 release_freezing();
				 return;
			 }
			 if(trim(reponse[0])=='pi1'){
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='recv1'){
			alert("Receive Number Found :"+trim(reponse[2])+"\n So Delete Not Possible")
		    release_freezing();
		    return;
		    }
		    if(trim(reponse[0])=='rec1'){
			alert("Receive Number Found :"+trim(reponse[2])+"\n So Delete Not Possible")
		    release_freezing();
		    return;
		    }
			 release_freezing();
		}
	}

	function fnc_show_booking_list(){
		var data="action=show_fabric_booking_list"+get_submitted_data_string('txt_booking_no*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*cbo_company_name*cbo_buyer_name*cbouom*cbo_fabric_description*cbo_level',"../../");
		http.open("POST","requires/woven_partial_fabric_booking_controller.php",true);
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
			var data="action=show_fabric_booking"+get_submitted_data_string('txt_booking_no*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*cbo_company_name*cbo_buyer_name*cbouom*cbo_fabric_description*cbo_level*cbo_supplier_name*from_style_id',"../../");
			http.open("POST","requires/woven_partial_fabric_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_show_booking_reponse;
		}
	}

	function fnc_show_booking_reponse(){
		if(http.readyState == 4){
			document.getElementById('booking_list_view').innerHTML=http.responseText;
			set_button_status(1, permission, 'fnc_fabric_booking_dtls',2);
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

	function generate_fabric_report(type,mail_data=0){
		if ( form_validation('txt_booking_no','Booking No')==false ){
			return;
		}
		else{
				var show_yarn_rate='';
			if(type!='print_booking_5' && type != 'fabric_booking_report')
			{

				var r=confirm("Press  \"Cancel\"  to hide  Rate/Amount\nPress  \"OK\"  to Show Rate/Amount");
				if (r==true){
					show_yarn_rate="1";
				}
				else{
					show_yarn_rate="0";
				}
			}
			$report_title=$( "div.form_caption" ).html();
			var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id',"../../")+'&report_title='+$report_title+'&mail_data='+mail_data+'&path=../../';
			freeze_window(5);
			http.open("POST","requires/woven_partial_fabric_booking_controller.php",true);
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
		$('#printbooking').removeAttr('href').attr('href','requires/'+trim(file_data[0]));
			//$('#print_report4')[0].click();
		document.getElementById('printbooking').click();
		}
		else
		{
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0]);
		}
			// $('#pdf_file_name').html(file_data[1]);
			// $('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
			d.close();
			var content=document.getElementById('data_panel').innerHTML;
			release_freezing();
		}
	}

	function generate_fabric_report4(type,mail_data=0){
	if (form_validation('txt_booking_no','Booking No')==false){
		return;
	}
	else{
		var show_comment='';
		var r=confirm("Press  \"Cancel\"  to hide  Rate/Amount\nPress  \"OK\"  to Show Rate/Amount");
		if (r==true){
			show_comment="1";
		}
		else{
			show_comment="0";
		}
		$report_title=$( "div.form_caption" ).html();
			var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id',"../../")+'&report_title='+$report_title+'&mail_data='+mail_data+'&path=../../';
			freeze_window(5);
			http.open("POST","requires/woven_partial_fabric_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report4_reponse;
	}
}

function generate_fabric_report4_reponse(){
	if(http.readyState == 4){
		freeze_window(5);
		var file_data=http.responseText.split("****");
		//alert(file_data[2]);
		if(file_data[2]==100)
		{
		$('#data_panel').html(file_data[1]);
		$('#printbooking').removeAttr('href').attr('href','requires/'+trim(file_data[0]));
			//$('#print_report4')[0].click();
		document.getElementById('printbooking').click();
		}
		else
		{
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0]);
		}


		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/prt.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
		var content=document.getElementById('data_panel').innerHTML;

	}
}

	function generate_fabric_report_gr(type){
		var booking_option = $("#booking_option").val();
		var booking_option_id = $("#booking_option_id").val();
		var booking_option_no = $("#booking_option_no").val();
		var page_link='requires/woven_partial_fabric_booking_controller.php?action=booking_surch_option&booking_option='+booking_option+'&booking_option_id='+booking_option_id+'&booking_option_no='+booking_option_no;
		var title="Booking Search Option";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=510px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var option_des=this.contentDoc.getElementById("txt_selected").value;
			var option_id=this.contentDoc.getElementById("txt_selected_id").value;
			var serial_no=this.contentDoc.getElementById("txt_selected_no").value;
			$("#booking_option").val(option_des);
			$("#booking_option_id").val(option_id);
			$("#booking_option_no").val(serial_no);
			if (form_validation('txt_booking_no*booking_option_id','Booking No*Report Option')==false){
				var txt_booking_no=$('#booking_option_id').val();
				if(txt_booking_no==""){
					alert("Please Select At Least One Report Option");
					$('#show_textcbo_booking_gr').focus();
				}
				return;
			}
			else{
				var show_yarn_rate='';
				var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
				if (r==true){
					show_yarn_rate="1";
				}
				else{
					show_yarn_rate="0";
				}
				$report_title=$( "div.form_caption" ).html();
				var cbo_booking_gr=$('#booking_option_id').val();
				var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../")+'&report_title='+$report_title+'&show_yarn_rate='+show_yarn_rate+'&cbo_booking_gr='+cbo_booking_gr+'&path=../../';
				freeze_window(5);
				http.open("POST","requires/woven_partial_fabric_booking_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = generate_fabric_report_gr_reponse;
			}
		}
	}

	function generate_fabric_report_gr_reponse(){
		if(http.readyState == 4) {
			var file_data=http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
			d.close();
			var content=document.getElementById('data_panel').innerHTML;
			release_freezing();
		}
	}

	

	function generate_fabric_report2(){
		if (form_validation('txt_booking_no','Booking No')==false){
			return;
		}
		else{
			$report_title=$( "div.form_caption" ).html();
			var data="action=show_fabric_booking_report2"+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../");
			http.open("POST","requires/woven_partial_fabric_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report2_reponse;
		}
	}

	function generate_fabric_report2_reponse(){
		if(http.readyState == 4){
			var file_data=http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
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
		var page_link = 'requires/woven_partial_fabric_booking_controller.php?data='+data+'&action=unapp_request_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function(){
			var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
			$('#txt_un_appv_request').val(unappv_request.value);
		}
	}

	function check_exchange_rate(){
		var cbo_currercy=$('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/woven_partial_fabric_booking_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}

	function copy_colarculfpercent(count){
		var rowCount = $('#tbl_fabric_booking tr').length-1;
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
		var response=return_global_ajax_value( cbo_company_name, 'check_month_maintain', '', 'requires/woven_partial_fabric_booking_controller');
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
		var attention=return_global_ajax_value(supplier_id+"_"+cbo_pay_mode, 'get_attention_name', '', 'requires/woven_partial_fabric_booking_controller');
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
		reset_form('','booking_list_view*booking_list_view_list','','','');
		get_php_form_data( 0, 'company_wise_report_button_setting','requires/woven_partial_fabric_booking_controller' );
	}

	function fnResetFormDtls(){
		reset_form('','booking_list_view','','');
		set_button_status(0, permission, 'fnc_fabric_booking_dtls',2);
	}

	function set_data(po_id,po_number,precost_id,booking_id){
		var cbo_fabric_natu=document.getElementById('cbo_fabric_natu').value;
		var cbouom=document.getElementById('cbouom').value;
		var cbo_fabric_source=document.getElementById('cbo_fabric_source').value;
		get_php_form_data( po_id+"_"+cbo_fabric_natu+"_"+cbouom+"_"+cbo_fabric_source, "populate_order_data_from_search_popup", "requires/woven_partial_fabric_booking_controller" );
		//document.getElementById('txt_order_no').value=po_number;
		document.getElementById('txt_order_no_id').value=po_id;
		document.getElementById('cbo_fabric_description').value=precost_id;
		fnc_show_booking()
	}
	function deletedata(po_id,po_number,precost_id,booking_id){
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

		var row_num=1;
		if (form_validation('txt_booking_no','Booking No')==false){
		release_freezing()
		return;
		}
        var i=1;
		var data_all=get_submitted_data_string('txt_booking_no*cbo_pay_mode',"../../",i);
		data_all+="&txtjob_1=&txtpoid_1="+po_id+"&txtpre_cost_fabric_cost_dtls_id_1="+precost_id+"&bookingid_1="+booking_id+"&cbo_fabric_description="+precost_id;
		var cbo_level=document.getElementById('cbo_level').value;
		if(cbo_level==1){
		var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+data_all+"&delete_cause="+delete_cause;
		}
		if(cbo_level==2){
		var data="action=save_update_delete_dtls_job_level&operation="+operation+'&total_row='+row_num+data_all+"&delete_cause="+delete_cause;
		}
		http.open("POST","requires/woven_partial_fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
	}
	
	
	function call_print_button_for_mail(mail,mail_body,type){
		var company=document.getElementById('cbo_company_name').value;
		var firstButtonId=return_global_ajax_value( company, 'get_first_selected_print_button', '', 'requires/woven_partial_fabric_booking_controller');

		if(firstButtonId==143){generate_fabric_report('show_fabric_booking_report_urmi',mail+'**1'+'**'+mail_body);}
		else if(firstButtonId==84){generate_fabric_report('show_fabric_booking_report_urmi_per_job',mail+'**1'+'**'+mail_body);}
		else if(firstButtonId==85){generate_fabric_report('print_booking_3',mail+'**1'+'**'+mail_body);}
		else if(firstButtonId==151){generate_fabric_report('show_fabric_booking_report_advance_attire_ltd',mail+'**1'+'**'+mail_body);}
		else if(firstButtonId==160){generate_fabric_report('print_booking_5',mail+'**1'+'**'+mail_body);}
		else if(firstButtonId==175){generate_fabric_report('print_booking_6',mail+'**1'+'**'+mail_body);}
		else if(firstButtonId==241){generate_fabric_report('print_booking_11',mail+'**1'+'**'+mail_body);}
		else if(firstButtonId==155){generate_fabric_report('fabric_booking_report',mail+'**1'+'**'+mail_body);}
		else if(firstButtonId==274){generate_fabric_report('print_booking_10',mail+'**1'+'**'+mail_body);}
		else if(firstButtonId==72){generate_fabric_report('print6booking',mail+'**1'+'**'+mail_body);}
		else if(firstButtonId==428){generate_fabric_report('print_booking_eg1',mail+'**1'+'**'+mail_body);}

	}
	function fnc_adjust_qty_data(id){
		var fabric_id=$("#txtpre_cost_fabric_cost_dtls_id_"+id).val();
		var gmtcolor=$("#txtgmtcolor_"+id).val();
		var itemcolor=$("#txtitemcolor_"+id).val();
		var reqqnty=$("#txtreqqnty_"+id).val();
		var adj_qty=$("#txtadj_"+id).val();
		var balqnty=$("#txtbalqnty_"+id).val();
		var title = 'Adjust Qty Pop Up:';
		var page_link = 'requires/woven_partial_fabric_booking_controller.php?fabric_id='+fabric_id+'&gmtcolor='+gmtcolor+'&itemcolor='+itemcolor+'&reqqnty='+reqqnty+'&adj_qty='+adj_qty+'&balqnty='+balqnty+'&action=adjust_qty_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=250px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function(){
			var request_qty=this.contentDoc.getElementById("current_adjust_qty");
			$("#txtadj_"+id).val(request_qty.value);
			$("#txtacwoq_"+id).val(request_qty.value);

			var woq=request_qty.value;
			var rate=document.getElementById('txtrate_'+id).value*1;
			var amount=number_format_common(woq*rate, 5, 0);
			document.getElementById('txtamount_'+id).value=amount;
			set_sum_value( 'total_bal_amt', 'txtamount_', 'tbl_fabric_booking' );
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
<body onLoad="set_hotkey();check_exchange_rate();check_month_setting();">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="fabricbooking_1"  autocomplete="off" id="fabricbooking_1">
            <fieldset style="width:950px;">
                <legend>Fabric Booking</legend>
                <table  width="900" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="right"></td>
                        <td align="right"></td>
                        <td  width="130" height="" align="right" class="must_entry_caption"> Booking No </td>
                        <td  width="170">
                        <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/woven_partial_fabric_booking_controller.php?action=fabric_booking_popup','fabric Booking Search')" readonly placeholder="Double Click for Booking"  name="txt_booking_no" id="txt_booking_no"/>
                        </td>
                        <td align="right" width="130" >
                        <input type="hidden" id="id_approved_id">
                        <input type="hidden" id="update_id">
                        <input type="hidden" id="month_id" class="text_boxes"  style="width:20px" >
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td align="right" id="booking_td">Booking Month</td>
                        <td><? echo $cbo_booking_month.$cbo_booking_year; ?> </td>
                        <td  align="right" class="must_entry_caption">Company Name</td>
                        <td><? echo $cbo_company_name; ?></td>
                        <td align="right" class="must_entry_caption">Buyer Name</td>
                        <td id="buyer_td"> <? echo $cbo_buyer_name;?></td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Fabric Nature</td>
                        <td><? echo $cbo_fabric_natu . $cbouom; ?></td>
                        <td align="right" width="130" class="must_entry_caption">Fabric Source</td>
                        <td><?  echo $cbo_fabric_source;?></td>
                        <td  width="130" align="right" class="must_entry_caption">Booking Date</td>
                        <td width="170"> <input class="datepicker" type="text" style="width:160px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo $date?>" disabled />
                        </td>
                    </tr>
                    <tr>
                        <td  width="130" align="right" class="must_entry_caption">Delivery Date</td>
                        <td width="170"><input class="datepicker" type="hidden" style="width:160px" name="txt_tna_date" id="txt_tna_date"/>
                        <input class="datepicker" type="text" style="width:160px" name="txt_delivery_date" id="txt_delivery_date" onChange="compare_date()"/></td>
                        <td  align="right" class="must_entry_caption">Pay Mode</td>
                        <td><? echo $cbo_pay_mode;?> </td>
                        <td  align="right" class="must_entry_caption">Supplier Name</td>
                        <td id="sup_td"><? echo $cbo_supplier_name;?> </td>
                    </tr>
                    <tr>
                        <td align="right">Currency</td>
                        <td><? echo $cbo_currency;?></td>
                        <td align="right">Exchange Rate</td>
                        <td><input style="width:160px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  /></td>
                        <td  width="130" height="" align="right"> Source </td>              <!-- 11-00030  -->
                        <td  width="170"><? echo $cbo_source;?></td>
                    </tr>
                     <tr>
                        <td align="right">Season Year</td>
                        <td><? echo $cbo_season_year;?></td>
                        <td align="right" >Season</td>
                        <td id="season_td">   <? echo $cbo_season_id;?></td>
                       
                        <td  width="130" height="" align="right"> Brand </td>              <!-- 11-00030  -->
                        <td  width="170" id="brand_td"><? echo $cbo_brand_id;?></td>
                    </tr>
                    <tr>
                        <td align="right">Attention</td>
                        <td align="left" height="10" colspan="3"><input class="text_boxes" type="text" style="width:466px;"  name="txt_attention" id="txt_attention"/></td>
                        <td align="right"  style="display:none">Booking Percent</td>
                        <td style="display: none;"><input style="width:160px;" type="hidden" class="text_boxes_numeric"  name="txt_booking_percent" id="txt_booking_percent" value="100"  /> </td>
                        <td align="right">Ready To Approved</td>
                        <td align="center" height="10"><? echo $cbo_ready_to_approved;?></td>
                    </tr>
                    <tr>
                        <td align="right">Collar Excess Cut %</td>
                        <td><input style="width:160px;" type="text" class="text_boxes_numeric"  name="txt_colar_excess_percent" id="txt_colar_excess_percent"/> </td>
                        <td align="right">Cuff Excess Cut %</td>
                        <td><input style="width:160px;" type="text" class="text_boxes_numeric"  name="txt_cuff_excess_percent" id="txt_cuff_excess_percent"/></td>
                        <td align="right">Un-approve request</td>
                        <td align="center"><Input name="txt_un_appv_request" class="text_boxes" readonly placeholder="Double Click for Brows" ID="txt_un_appv_request" style="width:160px"  onClick="openmypage_unapprove_request()" /></td>
                        
                    </tr>
                    <tr>
                        <td align="right">Internal Ref No</td>
                        <td align="center"><Input name="txt_intarnal_ref" class="text_boxes" readonly placeholder="Display" ID="txt_intarnal_ref" style="width:160px" ></td>
                        <td align="right">File no</td>
                        <td align="center"><Input name="txt_file_no" class="text_boxes" readonly placeholder="Display" ID="txt_file_no" style="width:160px" ></td>
                        <td align="right">Refusing cause</td>
                        <td align="center"><textarea class="text_boxes" readonly  ID="txt_refusing_cause" ame="txt_refusing_cause" style="width: 162px; height: 36px;"></textarea> </td>
                       
                    </tr>
                    <tr>
                        <td align="right">Fabric Composition</td>
                        <td align="left" height="10" colspan="3"><input class="text_boxes" type="text" maxlength="200" style="width:466px;"  name="txt_fabriccomposition" id="txt_fabriccomposition"/></td>
                        <td align="right">Level</td>
                        <td align="left" height="10"><? echo $cbo_level; ?></td>
                    </tr>
                    <tr>
                        <td align="right">Remarks</td>
                        <td align="left" height="10" colspan="3"><input class="text_boxes" type="text" maxlength="200" style="width:466px;"  name="txt_remark" id="txt_remark"/></td>
                        <td align="right" height="10">
                        <input type="button" id="set_button2" class="image_uploader" style="width:110px;" value="Process Loss %" onClick="open_rmg_process_loss_popup('requires/woven_partial_fabric_booking_controller.php?action=rmg_process_loss_popup','Process Loss %')" />
                        <input style="width:60px;" type="hidden" class="text_boxes"  name="processloss_breck_down" id="processloss_breck_down" />
                        </td>
                        <td height="10">
                       <!-- <input type="button" id="set_button1" class="image_uploader" style="width:140px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup('requires/partial_fabric_booking_controller.php?action=terms_condition_popup&permissions=<? echo $permission;?>','Terms Condition')" />-->

							<?
                            include("../../terms_condition/terms_condition.php");
                            terms_condition(271,'txt_booking_no','../../');
                            ?>


                        <input type="hidden" class="text_boxes"  name="adjust_qty_breck_down" id="adjust_qty_breck_down" />
                        </td>
                    </tr>
                    <tr>
                    	<td align="right">Delivery Address</td>
                    	<td colspan="3">
                    		<textarea id="delivery_address" class="text_area" style="width:466px; height:40px;" placeholder="Delivery Address" title="Allowed Characters: abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/<>?+[]{};: "></textarea>
                    	</td>
                    	<td align="right">Pay Term</td>
 						<td><? echo create_drop_down("cbo_payterm_id", 170, $pay_term, '', 1, '-Select-', 0, "", 0, ''); ?></td>
                    </tr>
                    <tr>
						<td  width="130" align="right">Supp. Dlv. Revised Date</td>
						<td  width="170">
							<input name="txt_sup_rev_date" id="txt_sup_rev_date" class="datepicker" type="text" style="width:160px; text-align:left"  />
						</td>
						<td align="right">From Style Ref.</td>
						<td>
							<input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_from_style('requires/woven_partial_fabric_booking_controller.php?action=from_style_popup','from Style Search')" readonly placeholder="Double Click for Style"  name="txt_from_style" id="txt_from_style"/>
							<input type="hidden" id="from_style_id" value="" /> 
						</td>
                    	<td align="right">Tenor</td>
						<td><input type="text"  name="txt_tenor" style="width:160px" id="txt_tenor" class="text_boxes_numeric" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="6" valign="top" id="app_sms2" style="font-size:18px; color:#F00"></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="6" valign="top" id="app_sms3" style="font-size:18px; color:#F00"></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="6" valign="middle" class="button_container">
						<?  echo $buttons  ; ?>
                        <input class="text_boxes" name="lib_tna_intregrate" id="lib_tna_intregrate" type="hidden" value=""  style="width:100px"/>
                        <div id="pdf_file_name" style="display: none;"></div>
                       <input type="button" id="set_button3" class="image_uploader" style="width:130px;" value="Collar & Cuff" onClick="open_colur_cuff_popup('requires/woven_partial_fabric_booking_controller.php?action=colur_cuff_popup&permissions=<? echo $permission?>','Collar & Cuff')" />

                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="6" height="10"><input type="hidden" class="" style="width:200px" id="selected_id_for_delete"></td>
                    </tr>
                </table>
            </fieldset>
        </form>
        <form name="servicebookingknitting_2"  autocomplete="off" id="servicebookingknitting_2">
            <fieldset style="width:950px;">
            <legend title="V3">Booking Item Form &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            Select Item: <input class="text_boxes" type="text" style="width:160px" onDblClick="fnc_process_data();" readonly placeholder="Double Click" name="txt_select_item" id="txt_select_item"/>
            <input class="text_boxes" type="hidden" style="width:160px" readonly placeholder="Double Click" name="txt_order_no_id" id="txt_order_no_id"/>
            <input class="text_boxes" type="hidden" style="width:160px" readonly placeholder="Double Click" name="cbo_fabric_description" id="cbo_fabric_description"/></legend>
            <div id="booking_list_view"><font id="save_sms" style="color:#F00">Select new Item</font></div>
              <? echo load_submit_buttons( $permission, "fnc_fabric_booking_dtls", 0,0 ,"fnResetFormDtls();",2) ; ?>
                <input type="button" value="Print 1" onClick="generate_fabric_report('show_fabric_booking_report_urmi');" style="width:100px; display:none;" name="print_booking_urmi" id="print_1" class="formbutton" /><!--Fabric booking Urmi-->
                <input type="button" value="Print 2" onClick="generate_fabric_report('show_fabric_booking_report_urmi_per_job');" style="width:100px; display:none;" name="print_booking_urmi" id="print_2" class="formbutton" /><!--Fabric booking Urmi Per Job-->
                <input type="button" value="Print 3" onClick="generate_fabric_report('print_booking_3');" style="width:100px; display:none;" name="print_booking_urmi" id="print_3" class="formbutton" />
                <input type="button" value="AAL Print" onClick="generate_fabric_report('show_fabric_booking_report_advance_attire_ltd');" style="width:100px; display:none;" name="print_booking_aal" id="print_4" class="formbutton" />
                <input type="button" value="Print 4" onClick="generate_fabric_report('print_booking_5');" style="width:100px; display:none;" name="print_booking_urmi" id="print_5" class="formbutton" />
                <input type="button" value="Print 5" onClick="generate_fabric_report('print_booking_6');" style="width:100px; display:none;" name="print_booking_urmi" id="print_6" class="formbutton" />
                <input type="button" value="Print 6" onClick="generate_fabric_report('print6booking');" style="width:100px; display:none;" name="print6booking" id="print6booking" class="formbutton" />
                <input type="button" value="Fabric Booking" onClick="generate_fabric_report('fabric_booking_report');" style="width:100px;display:none;" name="fabric_booking_report" id="fabric_booking_report" class="formbutton" />
                <input type="button" value="Print 10" onClick="generate_fabric_report('print_booking_10');" style="width:100px; display:none;" name="print_booking_urmi" id="print_7" class="formbutton" />
				<input type="button" value="Print 11" onClick="generate_fabric_report('print_booking_11');" style="width:100px; display:none;" name="print_11" id="print_11" class="formbutton" />
                <input class="formbutton" type="button" onClick="fnSendMail('../../','',1,1,0,1)" value="Mail Send" style="width:80px;">
				<input type="button" value="EG 1" onClick="generate_fabric_report('print_booking_eg1');" style="width:100px; display:none;" name="print_eg1" id="print_eg1" class="formbutton" />
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
	if(rate>pre_cost_rate){
		alert("Rate greater than precost rate not allowed");
		document.getElementById('txtrate_'+i).value=current_cost_rate;
		return;
	}
	var amount=number_format_common(woq*rate, 5, 0);
	document.getElementById('txtamount_'+i).value=amount;
	set_sum_value( 'total_bal_amt', 'txtamount_', 'tbl_fabric_booking' );
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
		var cbo_booking_month=document.getElementById('cbo_booking_month').value;
		var cbo_booking_year=document.getElementById('cbo_booking_year').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var cbo_currency=document.getElementById('cbo_currency').value;
		var cbo_fabric_natu=document.getElementById('cbo_fabric_natu').value;
		var cbouom=document.getElementById('cbouom').value;
		var cbo_fabric_source=document.getElementById('cbo_fabric_source').value;
		var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
		var cbo_level=document.getElementById('cbo_level').value;
		var cbo_season_year=document.getElementById('cbo_season_year').value;
		var cbo_season_id=document.getElementById('cbo_season_id').value;
		var cbo_brand_id=document.getElementById('cbo_brand_id').value;
	    var page_link='requires/woven_partial_fabric_booking_controller.php?action=fabric_search_popup';
		var title='Fabric Booking Search';
		page_link=page_link+'&garments_nature='+garments_nature+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&cbo_currency='+cbo_currency+'&cbo_fabric_natu='+cbo_fabric_natu+'&cbouom='+cbouom+'&cbo_fabric_source='+cbo_fabric_source+'&cbo_supplier_name='+cbo_supplier_name+'&cbo_level='+cbo_level+'&cbo_season_year='+cbo_season_year+'&cbo_season_id='+cbo_season_id+'&cbo_brand_id='+cbo_brand_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1040px,height=450px,center=1,resize=1,scrolling=0','../')
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
			}
		}
	}
}
function set_sum_value(des_fil_id,field_id,table_id){
	if(table_id=='tbl_fabric_booking')
	{
 		var rowCount = $('#tbl_fabric_booking  tr').length-1;

 		var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currency').value}
		//math_operation( des_fil_id, field_id, '+', rowCount,ddd);
		math_operation_custom( des_fil_id, field_id, '+', rowCount,ddd);
		//document.getElementById('txt_lab_test_pre_cost').value=document.getElementById('txtratelabtest_sum').value;
		//calculate_main_total();

	}
}
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$( document ).ready(function() {
load_drop_down( 'requires/woven_partial_fabric_booking_controller', document.getElementById('cbo_pay_mode').value, 'load_drop_down_suplier', 'sup_td' )
});
jQuery("#delivery_address").keyup(function(e)
	{
		var c = String.fromCharCode(e.which);
		var evt = (e) ? e : window.event;
		var key = (evt.keyCode) ? evt.keyCode : evt.which;
		// var key = e.keyCode;
		 //alert (key )
		if (key == 13)
		{
			var text = $("#delivery_address").val();
			var lines = text.split(/\r|\r\n|\n/);
			var count = (lines.length*1)+1;
			//document.getElementById("delivery_address").value =document.getElementById("delivery_address").value + "\n"+count+". ";
			document.getElementById("delivery_address").value =document.getElementById("delivery_address").value+ "\n";
			return false;
		}
		else {
			return true;
		}
	});
</script>
</html>
<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Woven Grey Fabric Purchase Booking
Functionality	         :
JS Functions	         :
Created by		         :	Md. Helal Uddin
Creation date 	         : 	20-08-2022
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
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_id=$_SESSION['logic_erp']['user_id'];
echo load_html_head_contents("Woven Fabric Booking", "../", 1, 1,$unicode,1,'');
//--------------------------------------------------------------------------------------------------------------------
$date                  = date('d-m-Y');
$level_arr             = array(1=>"PO Level",2=>"Job Level");
$buyer_cond            = set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond          = set_user_lavel_filtering(' and comp.id','company_id');
$cbo_company_name      = create_drop_down( "cbo_company_name", 152, "select id,company_name from lib_company  comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name",1, "-- Select Company --", "", "load_drop_down( 'requires/woven_grey_fabric_purchase_booking_controller',document.getElementById('cbo_within_group').value+'_'+ this.value, 'load_drop_down_buyer', 'buyer_td' );validate_suplier();get_php_form_data( this.value, 'company_wise_report_button_setting','requires/woven_grey_fabric_purchase_booking_controller' );",0,"" );
$cbo_buyer_name		   = create_drop_down( "cbo_buyer_name", 152, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "","","" );
$cbouom                = create_drop_down( "cbouom", 60, $unit_of_measurement,'', 1, '-Uom-', $row[csf('uom')], "",$disabled,"1,12,23,27" );
$cbo_pay_mode          = create_drop_down( "cbo_pay_mode", 152, $pay_mode,"", 1, "-- Select Pay Mode --", 5, "load_drop_down( 'requires/woven_grey_fabric_purchase_booking_controller', this.value, 'load_drop_down_suplier', 'sup_td' )","","1,2,3,5" );
$cbo_supplier_name     = create_drop_down( "cbo_supplier_name", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=9 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "fill_attention(this.value)",0 );
$cbo_currency          = create_drop_down( "cbo_currency", 152, $currency,"",1, "-- Select --", 2, "check_exchange_rate()",0 );
$cbo_source            = create_drop_down( "cbo_source", 152, $source,"", 1, "-- Select Source --", "", "","" );
$cbo_ready_to_approved = create_drop_down( "cbo_ready_to_approved", 152, $yes_no,"", 1, "-- Select--", 2, "","","" );
$cbo_level             = create_drop_down( "cbo_level", 152, $level_arr,"", 0, "", 2, "","","" );
$buttons               = load_submit_buttons( $permission, "fnc_fabric_booking", 0,1 ,"fnResetForm();reset_form('fabricbooking_1','','booking_list_view*booking_list_view_list','cbo_pay_mode,5*cbo_booking_year,2014*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_percent,100*txt_booking_date,".$date."','disable_enable_fields(\'cbo_company_name*cbo_buyer_name*txt_colar_excess_percent*txt_cuff_excess_percent*txt_un_appv_request\',0)');",1) ;

$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
?>
<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var field_level_data='';
    var permission='<? echo $permission; ?>';
    var mandatory_field=new Array(); var mandatory_message=new Array();
    <?
    if(count($_SESSION['logic_erp']['data_arr'][459]))
    {
        $data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][459] );
        echo " mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][459]) . "';\n";
        echo " mandatory_message = '". implode('*',$_SESSION['logic_erp']['mandatory_message'][459]) . "';\n";
		echo " field_level_data= ". $data_arr . ";\n";
    }
    ?>
	function openmypage_booking(page_link,title)
    {
		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var company=$("#cbo_company_name").val()*1;
		var cbo_buyer_name=$("#cbo_buyer_name").val()*1;
		var cbo_within_group=$("#cbo_within_group").val()*1;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company+'&cbo_buyer_name='+cbo_buyer_name+'&cbo_within_group='+cbo_within_group, title, 'width=1090,height=480px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			if (theemail.value!="")
            {
				reset_form('fabricbooking_1','booking_list_view','','txt_booking_date,<? echo date("d-m-Y"); ?>');
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/woven_grey_fabric_purchase_booking_controller" );
				set_button_status(1, permission, 'fnc_fabric_booking',1);
				fnc_show_booking_list();
				get_php_form_data( document.getElementById('cbo_company_name').value, 'company_wise_report_button_setting','requires/woven_grey_fabric_purchase_booking_controller' );
			}
			$("#cbo_company_name").attr("disabled","disabled");
			$("#cbo_buyer_name").attr("disabled","disabled");
		}
	}

	function fnc_generate_booking_reponse(){
		if(http.readyState == 4){
			document.getElementById('booking_list_view').innerHTML=http.responseText;
					set_button_status(0, permission, 'fnc_fabric_booking_dtls',2);
				set_all_onclick();
		}
	}

	function fnc_fabric_booking( operation )
    {
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

		if(operation==4)
        {
            if(form_validation('cbo_company_name*txt_booking_no','Select Company*Booking No')==false)
            {
                return;
            }

            var report_title=$( "div.form_caption" ).html();
            print_report( $('#cbo_company_name').val()+'**'+$('#txt_booking_no').val()+'**'+$('#update_id').val()+'**'+report_title, "print1", "requires/woven_grey_fabric_purchase_booking_controller" );
			release_freezing();
            return;
        }

		if(document.getElementById('id_approved_id').value==1){
			alert("This booking is approved")
			release_freezing();
			return;
		}
		
        // <?
        // if(count($_SESSION['logic_erp']['mandatory_field'][459])>0)
        // {
        //     ?>
        //     if(mandatory_field)
        //     {
        //         if (form_validation(mandatory_field,mandatory_message)==false)
        //         {
        //             release_freezing();
        //             return;
        //         }
        //     }
        //     <?
        // }
        // ?>

		if (form_validation('cbo_company_name*cbo_buyer_name*txt_booking_date*cbo_supplier_name*cbo_pay_mode','Company Name*Buyer Name*Booking Date*Supplier Name*Pay Mode')==false)
		{
			release_freezing();
			return;
		}
		
		if (document.getElementById('cbo_pay_mode').value!=3 && document.getElementById('cbo_supplier_name').value==0)
        {
			alert("Select Supplier Name")
			release_freezing();
			return;
		}
		else
        {
			var data="action=save_update_delete&operation="+operation+"&delete_cause="+delete_cause+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_booking_date*cbo_pay_mode*cbo_supplier_name*cbo_currency*txt_exchange_rate*cbo_source*txt_attention*cbo_ready_to_approved*delivery_address*update_id*cbo_within_group*cbo_agent_name*txt_delivery_date',"../");
			http.open("POST","requires/woven_grey_fabric_purchase_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_booking_reponse;
		}
	}

	function fnc_fabric_booking_reponse()
    {
		if(http.readyState == 4){
			var reponse=trim(http.responseText).split('**');
			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1) {
				if(parseInt(trim(reponse[0]))==0)
				{
					document.getElementById('txt_booking_no').value=reponse[1];
					document.getElementById('update_id').value=reponse[2];
				}
				set_button_status(1, permission, 'fnc_fabric_booking',1);
				show_msg(trim(reponse[0]));
				$("#cbo_company_name").attr("disabled","disabled");
				$("#cbo_buyer_name").attr("disabled","disabled");
				$("#cbo_supplier_name").attr("disabled","disabled");
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
				alert("PI Number Found :[ "+trim(reponse[2])+" ]\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='recv1'){
			alert("Receive Number Found :[ "+trim(reponse[2])+" ]\n So Delete Not Possible")
		    release_freezing();
		    return;
		    }
			release_freezing();
		}
	}

	function fnc_fabric_booking_dtls( operation )
    {
		freeze_window(operation);
		if(document.getElementById('id_approved_id').value==1){
			alert("This booking is approved")
			release_freezing();
			return;
		}
		var delete_cause='';
		if(operation==2)
		{
			delete_cause = prompt("Please enter your delete cause", "");
			if(delete_cause=="")
			{
				alert("You have to enter a delete cause");
				release_freezing();
				return;
			}
			if(delete_cause==null)
			{
				release_freezing();
				return;
			}
			var r=confirm("Press OK to Delete Or Press Cancel");
			if(r==false)
			{
				release_freezing();
				return;
			}
		}

		var row_num=$('#tbl_fabric_booking tbody tr').length;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('txt_fso_id*txt_booking_no','FSO No*Booking No')==false){
			release_freezing()
			return;
			}

			var cuqnty=(document.getElementById('cuqnty_'+i).value)*1
			var txtwoq=(document.getElementById('txtwoq_'+i).value)*1
			var txtbalqnty=(document.getElementById('txtbalqnty_'+i).value)*1
			var txtreqqnty=(document.getElementById('txtreqqnty_'+i).value)*1
			var txtwoqprev=(document.getElementById('txtwoqprev_'+i).value)*1

			if(operation==0)
			{
				var totwoqty=cuqnty+txtwoq;
				var totwoqty=number_format(cuqnty+txtwoq,2,'.','');
				var txtreqqnty=number_format(txtreqqnty,2,'.','');
				
				if((totwoqty*1)>(txtreqqnty*1))
				{
					alert("You are exceeding your balance.\n WoQty="+totwoqty+',ReqQty='+txtreqqnty);
			        release_freezing()
			        return;
				}
			}
			if(operation==1)
			{
				var totwoqty=(cuqnty-txtwoqprev)+txtwoq;
				var totwoqty=number_format(totwoqty,2,'.','');
				var txtreqqnty=number_format(txtreqqnty,2,'.','');
				
				if((totwoqty*1)>(txtreqqnty)*1)
                { 
					alert("You are exceeding your balance.");
			        release_freezing()
			        return;
				}
			}
			data_all=data_all+get_submitted_data_string('txt_booking_no*update_id*cbo_pay_mode*txtjob_'+i+'*txtpoid_'+i+'*txtgmtcolor_'+i+'*txtitemcolor_'+i+'*txtbalqnty_'+i+'*txtreqqnty_'+i+'*txtwoq_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtadj_'+i+'*txtremark_'+i+'*txtcolorsizetableid_'+i+'*txtpre_cost_fabric_cost_dtls_id_'+i+'*txtcolortype_'+i+'*txtconstruction_'+i+'*txtcompositi_'+i+'*txtgsm_weight_'+i+'*txtdia_'+i+'*txtacwoq_'+i+'*process_'+i+'*bookingid_'+i+'*totalends_'+i,"../",i);
		}
		var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+data_all+"&delete_cause="+delete_cause;
		
		http.open("POST","requires/woven_grey_fabric_purchase_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
	}

	function fnc_fabric_booking_dtls_reponse()
    {
		if(http.readyState == 4)
		{
			 var reponse=trim(http.responseText).split('**');
			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1 || parseInt(trim(reponse[0]))==2)
			{
				fnc_show_booking_list();
				reset_form('','booking_list_view','','');
				release_freezing();
				show_msg(trim(reponse[0]));
			}
			if(trim(reponse[0])=='approved')
			{
				alert("This booking is approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='sal1')
			{
				alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='pi1')
			{
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='recv1')
			{
				alert("Receive Number Found :"+trim(reponse[2])+"\n So Delete Not Possible")
		    	release_freezing();
		    	return;
		    }
			release_freezing();
		}
	}
	function fnc_show_booking_list(){
		var data="action=show_fabric_booking_list"+get_submitted_data_string('txt_booking_no*txt_fso_id*cbo_company_name*cbo_buyer_name',"../");
		http.open("POST","requires/woven_grey_fabric_purchase_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_booking_list_reponse;
	}

	function fnc_show_booking_list_reponse(){
		if(http.readyState == 4){
			document.getElementById('booking_list_view_list').innerHTML=http.responseText;
		}
	}

	function set_data(po_id,color_size_id,precost_id,booking_id){
		var cbo_fabric_natu=3;
		var cbouom='';
		var cbo_fabric_source=2;
		get_php_form_data( po_id+"_"+cbo_fabric_natu+"_"+cbouom+"_"+cbo_fabric_source+'_'+booking_id, "populate_order_data_from_search_popup", "requires/woven_grey_fabric_purchase_booking_controller" );
		//document.getElementById('txt_order_no').value=po_number;
		document.getElementById('txt_fso_id').value=precost_id;
		document.getElementById('txt_fso_dtls_id').value=po_id;
		document.getElementById('txt_fso_yarn_id').value=color_size_id;
		//document.getElementById('cbo_fabric_description').value=precost_id;
		fnc_show_booking(booking_id)
	}

	function fnc_show_booking(booking_id){
		if (form_validation('txt_booking_no','Booking No')==false){
			return;
		}
		else{
			freeze_window(5);
			var data="action=show_fabric_booking&booking_dtls_id="+booking_id+get_submitted_data_string('txt_fso_id*txt_fso_dtls_id*txt_fso_yarn_id*txt_booking_no*cbo_company_name*cbo_buyer_name',"../");
			http.open("POST","requires/woven_grey_fabric_purchase_booking_controller.php",true);
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

	function validate_value( i , type)
	{
		if(type=='finish')
		{
			var real_value =document.getElementById('finscons_'+i).placeholder;
			var user_given =document.getElementById('finscons_'+i).value;
			if(user_given> real_value){
				alert("Over booking than budget not allowed");
				document.getElementById('finscons_'+i).value=real_value;
				document.getElementById('finscons_'+i).focus();
			}
		}
		if(type=='grey')
		{
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
		if(type=='rate')
		{
			document.getElementById('amount_'+i).value=(document.getElementById('rate_'+i).value)*1*(document.getElementById('greycons_'+i).value)*1;
		}
	}

	function generate_fabric_report(type,report_type,mail_id,is_mail_send)
	{
		if ( form_validation('txt_booking_no','Booking No')==false )
		{
			return;
		}
		else
		{
				var show_yarn_rate='';
			if(type!='print_booking_5' && type != 'fabric_booking_report')
			{

				var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
				if (r==true){
					show_yarn_rate="1";
				}
				else{
					show_yarn_rate="0";
				}
			}
			$report_title=$( "div.form_caption" ).html();
			var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_source*id_approved_id',"../")+'&report_title='+$report_title+'&show_yarn_rate='+show_yarn_rate+'&path=../'+'&report_type='+report_type+'&mail_id='+mail_id+'&is_mail_send='+is_mail_send+'&cbo_fabric_natu=';
			freeze_window(5);
			http.open("POST","requires/woven_grey_fabric_purchase_booking_controller.php",true);
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
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
			d.close();
			var content=document.getElementById('data_panel').innerHTML;
			release_freezing();
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
		var page_link = 'requires/woven_grey_fabric_purchase_booking_controller.php?data='+data+'&action=unapp_request_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function(){
			var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
			$('#txt_un_appv_request').val(unappv_request.value);
		}
	}

	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/woven_grey_fabric_purchase_booking_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}
	function validate_suplier()
	{
		var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
		var company=document.getElementById('cbo_company_name').value;
		var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
		fill_attention(cbo_supplier_name)
	}

	function fill_attention(supplier_id)
	{
		if(supplier_id==0)
		{
			document.getElementById('txt_attention').value='';
			return;
		}
		var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
		var attention=return_global_ajax_value(supplier_id+"_"+cbo_pay_mode, 'get_attention_name', '', 'requires/woven_grey_fabric_purchase_booking_controller');
		document.getElementById('txt_attention').value=trim(attention);
	}

	function compare_date()
	{
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
		if(txt_tna_date_data !='')
		{
			if(tna_intregrate==1)
			{
				if(txt_delevary_date > txt_tna_date)
				{
					alert('Delivery Date is greater than TNA Date');
					document.getElementById('txt_delivery_date').value=document.getElementById('txt_tna_date').value;
				}
			}
		}
	}
	function fnResetForm()
	{
		reset_form('','booking_list_view*booking_list_view_list','','','');
		get_php_form_data( 0, 'company_wise_report_button_setting','requires/woven_grey_fabric_purchase_booking_controller' );
	}

	function fnResetFormDtls()
	{
		reset_form('','booking_list_view','','');
		set_button_status(0, permission, 'fnc_fabric_booking_dtls',2);
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
		var page_link = 'requires/woven_grey_fabric_purchase_booking_controller.php?data='+data+'&action=refusing_cause_popup';
	
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
	
		emailwindow.onclose=function()
		{
			var refusing_cause=this.contentDoc.getElementById("hidden_appv_cause");
			$('#txt_refusing_cause').val(refusing_cause.value);
		}
	}
	function claculate_amount(i){
		var bookingid=document.getElementById('bookingid_'+i).value*1;
		var txtwoq=document.getElementById('txtwoq_'+i).value*1;
		var rate=document.getElementById('txtrate_'+i).value*1;
		var txtbalqnty=document.getElementById('txtbalqnty_'+i).value*1;
		var txtwoqprev=document.getElementById('txtwoqprev_'+i).value*1;
		var pre_cost_rate=$('#txtrate_'+i).attr('data-pre-cost-rate');
		var current_cost_rate=$('#txtrate_'+i).attr('data-current-rate');
		pre_cost_rate=pre_cost_rate*1;
		current_cost_rate=current_cost_rate*1;
		if( bookingid > 0 )
		{
			if( txtwoq > Number(txtbalqnty + txtwoqprev) )
			{
				alert("Quantity can't greater than sales quantity");
				document.getElementById('txtacwoq_'+i).value=txtwoqprev;
				document.getElementById('txtwoq_'+i).value=txtwoqprev;
				//return;
			}
		}
		else
		{
			if( txtwoq > txtbalqnty )
			{
				alert("Quantity can't greater than sales quantity");
				document.getElementById('txtacwoq_'+i).value=txtbalqnty;
				document.getElementById('txtwoq_'+i).value=txtbalqnty;
				//return;
			}
		}
		txtwoq=document.getElementById('txtwoq_'+i).value*1;
		var amount=number_format_common(txtwoq*rate, 5, 0);
		document.getElementById('txtamount_'+i).value=amount;
		set_sum_value( 'total_bal_amt', 'txtamount_', 'tbl_fabric_booking' );
		//claculate_acwoQty(i)
	}
	function claculate_acwoQty(i){
		// var bookingid=document.getElementById('bookingid_'+i).value*1;
		// var txtwoqprev=document.getElementById('txtwoqprev_'+i).value*1;
		// var woq=document.getElementById('txtwoq_'+i).value*1;
		// var txtadj=document.getElementById('txtadj_'+i).value*1;
		// var acwoq=number_format_common(woq-txtadj, 5, 0);
		// document.getElementById('txtwoq_'+i).value=acwoq;
		claculate_amount(i)
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
		var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+data_all+"&delete_cause="+delete_cause;
		http.open("POST","requires/woven_grey_fabric_purchase_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
	}
</script>
<style>
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
<body onLoad="check_exchange_rate();">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../",$permission);  ?>
        <form name="fabricbooking_1"  autocomplete="off" id="fabricbooking_1">
            <fieldset style="width:1070px;">
                <legend>Fabric Booking </legend>
                <table  width="1050" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td  align="left"></td>
                        <td  align="left"></td>
                        <td   height=""  align="left" class="must_entry_caption"> Booking No </td>
                        <td  >
                            <input class="text_boxes" type="text" style="width:140px" onDblClick="openmypage_booking('requires/woven_grey_fabric_purchase_booking_controller.php?action=fabric_booking_popup','fabric Booking Search')" readonly placeholder="Double Click for Booking"  name="txt_booking_no" id="txt_booking_no"/>
                        </td>
                        <td  align="left"  >
                            <input type="hidden" id="id_approved_id">
                            <input type="hidden" id="update_id">
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td   align="left" class="must_entry_caption">Company Name </td>
                        <td  align="left"><? echo $cbo_company_name; ?></td>
                        <td  class="must_entry_caption">Within Group</td>
	                    <td>
	                    	<?
								echo create_drop_down("cbo_within_group", 152, $yes_no, "", 0, "--  --", 0, "load_drop_down( 'requires/woven_grey_fabric_purchase_booking_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' )");
							?>
	                    </td>
                        <td  align="left" class="must_entry_caption" >Buyer Name</td>
                        <td  align="left" id="buyer_td"> <? echo $cbo_buyer_name;?> </td>
                        <td    align="left" class="must_entry_caption">Booking Date</td>
                        <td  align="left">
                            <input class="datepicker" type="text" style="width:140px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo $date?>" disabled />
                        </td>
                        
                    </tr>
                    <tr>
                    	<td   align="left" class="must_entry_caption">Pay Mode</td>
                        <td  align="left"><? echo $cbo_pay_mode;?> </td>
                        <td  align="left" class="must_entry_caption">Supplier Name</td>
                        <td  align="left" id="sup_td"><? echo $cbo_supplier_name;?> </td>
                        <td align="left">Currency</td>
                        <td><? echo $cbo_currency;?></td>
                        <td align="left">Exchange Rate</td>
                        <td align="left"><input style="width:140px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  /></td>
                        
                        
                        
                    </tr>
                    <tr>
                        <td align="left">Attention</td>
                        <td align="left" ><input class="text_boxes" type="text" style="width:140px;"  name="txt_attention" id="txt_attention"/></td>
                        <td align="left">Ready To Approved</td>
                        <td align="left" height="10"><? echo $cbo_ready_to_approved;?></td>
                        <td align="left">Un-approve request</td>
                        <td align="left">
                            <Input name="txt_un_appv_request" class="text_boxes" readonly placeholder="Double Click for Brows" id="txt_un_appv_request" style="width:140px"  onClick="openmypage_unapprove_request()" />
                        </td>
                        <td align="left">Refusing cause</td>
                        <td align="left">
                            <Input name="txt_refusing_cause" class="text_boxes" readonly placeholder="Double Click for Brows" id="txt_refusing_cause" style="width:140px"  onClick="openmypage_refusing_cause();" />
                        </td>
                    </tr>
					<tr>
						<td align="left" >Agent</td>
                        <td  align="left">
                            <?=create_drop_down( "cbo_agent_name", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in (30,31,32) and   a.status_active =1 and a.is_deleted=0 group by a.id,a.supplier_name  order by supplier_name","id,supplier_name", 1, "-- Select Agent --", $selected, "",0 );?>
                        </td>

						<td align="left" >Delivery Date</td>
                        <td  align="left">
                            <input class="datepicker" type="text" style="width:140px" name="txt_delivery_date" id="txt_delivery_date" value=""  />
                        </td>
						<td   height="" align="left"> Source </td>          
                        <td align="left" ><? echo $cbo_source;?></td>
					</tr>
                    <tr>
                    	
                    	<td align="left">Delivery Address</td>
                    	<td colspan="3">
                    		<textarea id="delivery_address" class="text_area" style="width:430px; height:40px;" placeholder="Delivery Address" ></textarea>
                    	</td>

						
                        
                    </tr>
					<tr>
                        <td align="center" colspan="4" valign="top" id="app_sms2" style="font-size:18px; color:#F00"></td>
						<td colspan="2">
							<?
								include("../terms_condition/terms_condition.php");
									terms_condition(549,'txt_booking_no','../');
							?>
						</td>
                    </tr>
                    <tr>
                        <td align="center" colspan="6" valign="middle" class="button_container">
						<?  echo $buttons  ; ?>
                        <input class="text_boxes" name="lib_tna_intregrate" id="lib_tna_intregrate" type="hidden" value=""  style="width:100px"/>
                        <div id="pdf_file_name" style="display: none;"></div>
                    </tr>
                    <tr>
                        <td align="center" colspan="6" height="10"><input type="hidden" class="" style="width:200px" id="selected_id_for_delete"></td>
                    </tr>
                </table>
            </fieldset>
        </form>
        <form name="servicebookingknitting_2"  autocomplete="off" id="servicebookingknitting_2">
            <fieldset style="width:950px;">

                <legend title="V3">
                    Booking Item Form &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                    Select Item: <input class="text_boxes" type="text" style="width:160px" onDblClick="fnc_process_data()" readonly placeholder="Double Click" name="txt_fso_id" id="txt_fso_id"/>
                    <input class="text_boxes" type="hidden" style="width:160px"  readonly  name="txt_fso_dtls_id" id="txt_fso_dtls_id"/>
                    <input class="text_boxes" type="hidden" style="width:160px"  readonly  name="txt_fso_yarn_id" id="txt_fso_yarn_id"/>
                </legend>
                <div id="booking_list_view"><font id="save_sms" style="color:#F00">Select new Item</font></div>
                    <? echo load_submit_buttons( $permission, "fnc_fabric_booking_dtls", 0,0 ,"fnResetFormDtls();",2) ; ?>

                    <input type="button" value="Print 1" onClick="generate_fabric_report('show_fabric_booking_report_urmi')"  style="width:100px; display:none;" name="print_booking_urmi" id="print_1" class="formbutton" /><!--Fabric booking Urmi-->
                    
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

    function fnc_process_data()
    {
        if (form_validation('cbo_company_name*txt_booking_no','Company*Booking No')==false)
		{
            return;
        }
        else
        {
            var garments_nature=3;
            var cbo_company_name=document.getElementById('cbo_company_name').value;
            var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
            var cbo_currency=document.getElementById('cbo_currency').value;
            var cbo_within_group=document.getElementById('cbo_within_group').value;
            var cbo_fabric_natu=3;
            var cbo_fabric_source=2;
            var cbo_supplier=document.getElementById('cbo_supplier_name').value;
            var page_link='requires/woven_grey_fabric_purchase_booking_controller.php?action=fabric_search_popup';
            var title='Fabric Booking Search';
            page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&cbo_currency='+cbo_currency+'&cbo_fabric_natu='+cbo_fabric_natu+'&cbo_fabric_source='+cbo_fabric_source+'&cbo_supplier='+cbo_supplier+'&cbo_within_group='+cbo_within_group;
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=940px,height=450px,center=1,resize=1,scrolling=0','')
            emailwindow.onclose=function()
			{
                var theform=this.contentDoc.forms[0];
                var txt_fso_id=this.contentDoc.getElementById("txt_fso_id").value;
                var txt_fso_dtls_id=this.contentDoc.getElementById("txt_fso_dtls_id").value;
                var txt_fso_yarn_id=this.contentDoc.getElementById("txt_fso_yarn_id").value;
                if (txt_fso_id!="")
				{
                    document.getElementById('txt_fso_id').value=txt_fso_id;
                    document.getElementById('txt_fso_dtls_id').value=txt_fso_dtls_id;
                    document.getElementById('txt_fso_yarn_id').value=txt_fso_yarn_id;
                    fnc_generate_booking(txt_fso_id,txt_fso_dtls_id,txt_fso_yarn_id);
                }
            }
        }
    }
    function fnc_generate_booking(){
		if (form_validation('txt_booking_no*txt_fso_id','Booking No*Order No')==false)
		{
			return;
		}
		else
		{
			var data="action=generate_fabric_booking"+get_submitted_data_string('txt_booking_no*txt_fso_id*cbo_company_name*cbo_buyer_name*txt_fso_dtls_id*txt_fso_yarn_id*cbo_supplier_name',"../");
			http.open("POST","requires/woven_grey_fabric_purchase_booking_controller.php",true);
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

    function set_sum_value(des_fil_id,field_id,table_id)
    {
        if(table_id=='tbl_fabric_booking')
        {
            var rowCount = $('#tbl_fabric_booking tbody tr').length;

            var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currency').value}
            math_operation( des_fil_id, field_id, '+', rowCount,ddd);
            var unique_uom=$("#uniqe_uom").val();
            if(unique_uom==1)
            {
                math_operation( "total_qo_qnty", "txtwoq_", '+', rowCount,ddd);
                math_operation( "total_ac_qnty", "txtacwoq_", '+', rowCount,ddd);
            }
            
            //document.getElementById('txt_lab_test_pre_cost').value=document.getElementById('txtratelabtest_sum').value;
            //calculate_main_total();

        }
    }
    
</script>

<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
    $( document ).ready(function() {
        load_drop_down( 'requires/woven_grey_fabric_purchase_booking_controller', document.getElementById('cbo_pay_mode').value, 'load_drop_down_suplier', 'sup_td' )
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
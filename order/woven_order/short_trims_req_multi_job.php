<?
/*-------------------------------------------- Comments ----------------------------------------
Purpose			: 	This form will create Multi Job Wise Short Trims Requisition
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	27-12-2023	
Updated by 		: 		
Update date		: 
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Multi Job Wise Short Trims Requisition", "../../", 1, 1,$unicode,1,'');
//----------------------------------------------------------------------------------------------------------------------------------
$buyer_cond    = set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond  = set_user_lavel_filtering(' and comp.id','company_id');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fnc_get_company_config(company_id)
	{
		get_php_form_data(company_id,'get_company_config','requires/short_trims_req_multi_job_controller' );
		/*location_select();
		
		var celid=mst_mandatory_field.split("*")
		//alert( celid.length+"="+celid)
		var a=0;
		for (var i = 1; i <= celid.length; i++)
		{
			var td=$('#'+celid[a]).val();
			//alert(td+'='+celid[a])
			$('#'+celid[a]).closest('td').prev().css('color', 'blue');
			a++;
		}*/
	}
	
	function fnc_process_data(){
		if (form_validation('cbo_company_name*txt_req_no','Company Name*Requisition No')==false){
			return;
		}
		else{
			var garments_nature=document.getElementById('garments_nature').value;
			var cbo_company_name=document.getElementById('cbo_company_name').value;
			var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
			var page_link='requires/short_trims_req_multi_job_controller.php?action=fnc_process_data';
			var title='Job Search For Trim Requisition';
			page_link=page_link+'&company_id='+cbo_company_name+'&garments_nature='+garments_nature+'&cbo_buyer_name='+cbo_buyer_name;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1340px,height=450px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function(){
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("txt_selected_id").value;
				var theemail2=this.contentDoc.getElementById("txt_job_id").value;
				var theemail3=this.contentDoc.getElementById("txt_selected_po").value;
				var theemail4=this.contentDoc.getElementById("itemGroup").value;
				if (theemail!=""){
					document.getElementById('txt_select_item').value=theemail;
					document.getElementById('txt_selected_po').value=theemail3;
					document.getElementById('txt_selected_trim_id').value=theemail4;
					fnc_generate_req(theemail,theemail3,theemail4,cbo_company_name)
				}
			}
		}
	}
	
	function fnc_generate_req(param,po_id,pre_cost_id,cbo_company_name){
		freeze_window(operation);
		var garments_nature=document.getElementById('garments_nature').value;
		var txt_req_date= document.getElementById('txt_req_date').value;
		var cbo_level=document.getElementById('cbo_level').value;
		var param="'"+param+"'";
		var data="'"+po_id+"'";
		var precost_id="'"+pre_cost_id+"'";
		var data="action=generate_trims_requisition&data="+data+'&cbo_company_name='+cbo_company_name+'&txt_req_date='+txt_req_date+'&garments_nature='+garments_nature+'&cbo_level='+cbo_level+'&pre_cost_id='+precost_id+'&param='+param;
		http.open("POST","requires/short_trims_req_multi_job_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_req_reponse;
	}
	
	function fnc_generate_req_reponse(){
		if(http.readyState == 4){
			document.getElementById('requisition_list_view').innerHTML=http.responseText;
			set_all_onclick();
			release_freezing();
		}
	}
	
	function open_consumption_popup(page_link,title,po_id,i){
		var garments_nature=document.getElementById('garments_nature').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var txt_job_no=document.getElementById('txtjob_'+i).value;
		var txt_po_id =document.getElementById(po_id).value;
		var cbo_trim_precost_id=document.getElementById('txttrimcostid_'+i).value;
		var txt_trim_group_id=document.getElementById('txttrimgroup_'+i).value;
		var txt_update_dtls_id=document.getElementById('txtbookingid_'+i).value;
		var cbo_colorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
		var txt_req_quantity=document.getElementById('txtreqqnty_'+i).value;
		var txtwoq=document.getElementById('txtwoq_'+i).value;
		var txt_req_amount=document.getElementById('txtreqamount_'+i).value;
		var txt_avg_price=document.getElementById('txtrate_'+i).value;
		var txt_country=document.getElementById('txtcountry_'+i).value;
		//var txt_pre_des=document.getElementById('txtdesc_'+i).value;
		var txt_pre_des= $('#txtdescid_'+i).val();
		//alert(txt_pre_des+'='+i);
		var txt_pre_brand_sup=document.getElementById('txtbrandsup_'+i).value;
		var txtcuwoq=document.getElementById('txtcuwoq_'+i).value;
		var txtcuamount=document.getElementById('txtcuamount_'+i).value*1;
		var cbo_level=document.getElementById('cbo_level').value*1;
		var cons_breck_downn=document.getElementById('consbreckdown_'+i).value;
		var txt_req_no=document.getElementById('txt_req_no').value;
		
		if(po_id==0 ){
			alert("Select Po Id")
		}
	
		else{
			var page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&txt_job_no='+txt_job_no+'&txt_po_id='+txt_po_id+'&cbo_trim_precost_id='+cbo_trim_precost_id+'&txt_trim_group_id='+txt_trim_group_id+'&txt_update_dtls_id='+txt_update_dtls_id+'&cbo_colorsizesensitive='+cbo_colorsizesensitive+'&txt_req_quantity='+txt_req_quantity+'&txt_avg_price='+txt_avg_price+'&txt_country='+txt_country+'&txt_pre_des='+txt_pre_des+'&txt_pre_brand_sup='+txt_pre_brand_sup+"&cbo_level="+cbo_level+"&txtwoq="+txtwoq+"&txt_req_no="+txt_req_no;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=450px,center=1,resize=1,scrolling=0','../')
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
				calculate_amount(i)
			}	
		}
	}
	
	function set_cons_break_down(i){
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
		var txt_pre_des=document.getElementById('txtdescid_'+i).value;
		var txt_pre_brand_sup=document.getElementById('txtbrandsup_'+i).value;
		var txtwoq=document.getElementById('txtbalwoq_'+i).value;
		var txtcurwoq=document.getElementById('txtwoq_'+i).value;
		var cbo_level=document.getElementById('cbo_level').value*1;
		var cons_breack_down=trim(return_global_ajax_value(garments_nature+"_"+cbo_company_name+"_"+txt_job_no+"_"+txt_po_id+"_"+cbo_trim_precost_id+"_"+txt_trim_group_id+"_"+txt_update_dtls_id+"_"+cbo_colorsizesensitive+"_"+txt_req_quantity+"_"+txt_avg_price+"_"+txt_pre_des+"_"+txt_pre_brand_sup+"_"+cbo_level+"_"+txtcurwoq, 'set_cons_break_down', '', 'requires/short_trims_req_multi_job_controller'));
		cons_breack_down_data=cons_breack_down.split("**");
		//alert(cons_breack_down_data);
		document.getElementById('consbreckdown_'+i).value=trim(cons_breack_down_data[0]);
		document.getElementById('jsondata_'+i).value=cons_breack_down_data[1];
	}
	
	function fnc_short_trims_req( operation ){
		freeze_window(operation);
		var data_all="";
		if (form_validation('cbo_company_name*cbo_buyer_name*txt_req_date*cbo_level','Company Name*Buyer Name*Requisition Date*Level')==false){
			release_freezing();
			return;
		}
		var delete_cause='';
		if(operation==2){
			delete_cause = prompt("Please enter your delete cause", "");
			if(delete_cause==""){
				alert("You have to enter a delete cause.");
				release_freezing();
				return;
			}
			if(delete_cause==null){
				release_freezing();
				return;
			}
			var r=confirm("Press OK to Delete Or Press Cancel.");
			if(r==false){
				release_freezing();
				return;
			}
		}
		//data_all=data_all+get_submitted_data_string('txt_req_no*cbo_company_name*cbo_buyer_name*txt_req_date*cbo_level*cbo_responsible_dept*cbo_responsible_person*cbo_division_id*cbo_ready_to_approved*txt_reason*txt_remarks*booking_mst_id',"../../");
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_req_no*cbo_company_name*cbo_buyer_name*txt_req_date*cbo_level*cbo_responsible_dept*cbo_responsible_person*cbo_division_id*cbo_ready_to_approved*txt_reason*txt_remarks*booking_mst_id',"../../")+'&delete_cause='+delete_cause;
		
		//var data="action=save_update_delete&operation="+operation+data_all+'&delete_cause='+delete_cause;
		//alert(data); release_freezing(); return;
		http.open("POST","requires/short_trims_req_multi_job_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_short_trims_req_reponse;
	}
		 
	function fnc_short_trims_req_reponse(){
		if(http.readyState == 4){
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='app1'){
				alert("This Requisition is approved")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='shortBookingno'){
				alert("Short Booking Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='recv1'){
				alert("Receive Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='pi1'){
				alert("PI Number Found :"+number_format(trim(reponse[2]),2,'.','' )+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='orderFound')
			{
				alert("This booking is Attached In Trims Order Receive (Trims ERP).Receive:"+trim(reponse[1])+". Delete Not Allowed");
				release_freezing();
				return;
			}
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(trim(reponse[0]));
			if(trim(reponse[0])==0 || trim(reponse[0])==1){
				document.getElementById('txt_req_no').value=reponse[1];
				document.getElementById('booking_mst_id').value=reponse[2];
				$("#cbo_company_name").attr("disabled",true);
				$("#cbo_buyer_name").attr("disabled",true);
				$("#cbo_level").attr("disabled",true);
				set_button_status(1, permission, 'fnc_short_trims_req',1);
			}
			else if(trim(reponse[0])==2){
				location.reload(); 
			}
			release_freezing();
		}
	}
	
	function fnc_short_trims_req_dtls( operation ){
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
		
		//var data_all="";
		if (form_validation('txt_req_no','Requisition No')==false){
			release_freezing();
			return;
		}	
		//data_all=data_all+get_submitted_data_string('txt_req_no*strdata*booking_mst_id',"../../");
		
		var row_num=$('#tbl_list_search tr').length;
		if(row_num <1){
			alert("Select Item");
			release_freezing();
			return;
		}
		var data_all=""; var z=1;
		for (var i=1; i<=row_num; i++){
			
			var txtwoq=document.getElementById('txtwoq_'+i).value*1;
			if(txtwoq=="" || txtwoq==0){
				alert("Insert Reqsn. Qty.")	
				release_freezing();
				return;
			}
			
			var txtrate=document.getElementById('txtrate_'+i).value*1;
			if(txtrate=="" || txtrate==0){
				alert("Insert Rate.")	
				release_freezing();
				return;
			}
			var consbreckdown=document.getElementById('consbreckdown_'+i).value
			var txtbookingid=document.getElementById('txtbookingid_'+i).value
			if (consbreckdown==""){  // && txtbookingid==""
				set_cons_break_down(i);
			}
			var consbreckdown=document.getElementById('consbreckdown_'+i).value;
			//alert(consbreckdown);
			if (trim(consbreckdown)=="" && operation ==1){ 
				alert("Unable to create Cons break down for minimum Requisition Qty, Data not saved.");
				release_freezing();
				$('#search'+i).css('background-color', 'red');
				return;
			}
			
			data_all+="&txtbookingid_" + z + "='" + $('#txtbookingid_'+i).val()+"'"+"&txtpoid_" + z + "='" + $('#txtpoid_'+i).val()+"'"+"&txtcountry_" + z + "='" + $('#txtcountry_'+i).val()+"'"+"&txttrimcostid_" + z + "='" + $('#txttrimcostid_'+i).val()+"'"+"&txttrimgroup_" + z + "='" + $('#txttrimgroup_'+i).val()+"'"+"&txtuom_" + z + "='" + $('#txtuom_'+i).val()+"'"+"&cbocolorsizesensitive_" + z + "='" + $('#cbocolorsizesensitive_'+i).val()+"'"+"&txtwoq_" + z + "='" + $('#txtwoq_'+i).val()+"'"+"&txtrate_" + z + "='" + $('#txtrate_'+i).val()+"'"+"&txtamount_" + z + "='" + $('#txtamount_'+i).val()+"'"+"&txtddate_" + z + "='" + $('#txtddate_'+i).val()+"'"+"&consbreckdown_" + z + "='" + $('#consbreckdown_'+i).val()+"'"+"&txtexchrate_" + z + "='" + $('#txtexchrate_'+i).val()+"'"+"&txtjob_" + z + "='" + $('#txtjob_'+i).val()+"'"+"&txtreqqnty_" + z + "='" + $('#txtreqqnty_'+i).val()+"'"+"&txtdescid_" + z + "='" + $('#txtdescid_'+i).val()+"'"+"&jsondata_" + z + "='" + $('#jsondata_'+i).val()+"'";
			
			z++;
		}
		var cbo_level=document.getElementById('cbo_level').value;
		if(cbo_level==1){
			var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+get_submitted_data_string('txt_req_no*strdata*booking_mst_id',"../../")+data_all;
		}
		if(cbo_level==2){
			var data="action=save_update_delete_dtls_job_level&operation="+operation+'&total_row='+row_num+get_submitted_data_string('txt_req_no*strdata*booking_mst_id',"../../")+data_all;
		}
		//alert(data); release_freezing(); return;
		http.open("POST","requires/short_trims_req_multi_job_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_short_trims_req_dtls_reponse;
	}
	
	function fnc_short_trims_req_dtls_reponse(){
		if(http.readyState == 4){
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='app1'){
				alert("This Requisition is approved.")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='shortBookingno'){
				alert("Short Booking Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='recv1'){
				alert("Receive Qty  :"+trim(reponse[3])+" Found in Receive Number "+ trim(reponse[2])+" \n So WOQ Less Then Receive Qty/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='pi1'){
				alert("PI Qty  :"+number_format(trim(reponse[3]),2,'.','' )+"  Found in PI Number "+ trim(reponse[2])+" \n So WOQ Less Then PI Qty/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='orderFound')
			{
				alert("This Requisition is Attached In Trims Order Receive (Trims ERP).Receive:"+trim(reponse[1])+". Delete Not Allowed");
				release_freezing();
				return;
			}
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(trim(reponse[0]));
			if(trim(reponse[0])==0 || trim(reponse[0])==1 || trim(reponse[0])==2){
				var str="";
				
				if(trim(reponse[0])==2){
					str='Deleted';
				}
				document.getElementById('txt_select_item').value='';
				document.getElementById('requisition_list_view').innerHTML=''
				document.getElementById('requisition_list_view').innerHTML='<font id="save_sms" style="color:#F00">Data '+str+', Select new Item</font>';
				fnc_show_booking_list();
			}
			release_freezing();
		}
	}
	
	function openmypage_req(page_link,title){
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var page_link=page_link+'&company_id='+cbo_company_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=455px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_booking");
			//alert(theemail.value)
			if(theemail.value!=""){
				reset_form('trimsreq_1','requisition_list_view','id_approved_id','txt_req_date,<? echo date("d-m-Y"); ?>','','');
				//document.getElementById('copy_val').checked=true;
				document.getElementById('requisition_list_view').innerHTML='<font id="save_sms" style="color:#F00">Select new Item</font>';
				//$("#cbo_currency").attr("disabled",true);
				get_php_form_data( theemail.value, "populate_data_from_search_popup_booking", "requires/short_trims_req_multi_job_controller" );
				set_button_status(1, permission, 'fnc_short_trims_req',1);
			}
		}
	}
	
	function fnc_show_booking_list(){
		var garments_nature=document.getElementById('garments_nature').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var data=document.getElementById('txt_selected_po').value;
		var precost_id=document.getElementById('txt_selected_trim_id').value;
		var param=document.getElementById('txt_select_item').value;
		
		var param="'"+param+"'"
		var data="'"+data+"'"
		var precost_id="'"+precost_id+"'"
		
		var cbo_level=document.getElementById('cbo_level').value;
		
		var data="action=show_trim_reqsn_list"+get_submitted_data_string('txt_req_no',"../../")+'&cbo_company_name='+cbo_company_name+'&garments_nature='+garments_nature+'&data='+data+'&param='+param+'&pre_cost_id='+precost_id+'&cbo_level='+cbo_level;
		http.open("POST","requires/short_trims_req_multi_job_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_booking_list_reponse;
	}
	
	function fnc_show_booking_list_reponse(){
		if(http.readyState == 4){
			$("#cbo_currency").attr("disabled",true);
			document.getElementById('requisition_list_view_list').innerHTML=http.responseText;
			set_button_status(1, permission, 'fnc_trims_booking',2);
			set_all_onclick();
		}
	}
	
	function fnc_show_booking(wo_pre_cost_trim_id,po_id,booking_id){
		freeze_window(operation);
		var garments_nature=document.getElementById('garments_nature').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var data=document.getElementById('txt_selected_po').value;
		var precost_id=document.getElementById('txt_selected_trim_id').value;
		var param=document.getElementById('txt_select_item').value;
		
		var cbo_level=document.getElementById('cbo_level').value;
		
		var data="action=show_trims_requisition"+get_submitted_data_string('txt_req_no',"../../")+'&cbo_company_name='+cbo_company_name+'&garments_nature='+garments_nature+'&data='+po_id+'&booking_id='+booking_id+'&pre_cost_id='+wo_pre_cost_trim_id+'&cbo_level='+cbo_level;
		http.open("POST","requires/short_trims_req_multi_job_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_booking_reponse;
	}
	
	function fnc_show_booking_reponse(){
		if(http.readyState == 4){
			//$("#cbo_currency").attr("disabled",true);
			document.getElementById('requisition_list_view').innerHTML=http.responseText;
			set_all_onclick();
			release_freezing();
		}
	}
</script>	
</head> 
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="trimsreq_1" autocomplete="off" id="trimsreq_1">
        <fieldset style="width:1050px;">
            <legend>Multi Job Wise Short Trims Requisition[Knit] &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;   <font id="app_sms" style="color:#F00"></font></legend>
            <table width="1050" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td colspan="4" align="right" class="must_entry_caption"><b>Requisition No</b></td>              
                    <td colspan="4">
                        <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_req('requires/short_trims_req_multi_job_controller.php?action=trims_requisition_popup','Short Trims Requisition Search')"  placeholder="Browse" name="txt_req_no" id="txt_req_no" readonly/>
                        <input type="hidden" id="id_approved_id">
                        <input type="hidden" id="exeed_budge_qty">
                        <input type="hidden" id="exeed_budge_amount">
                        <input type="hidden" id="amount_exceed_level">
                        <input type="hidden" id="report_ids"/>  
                        <input type="hidden" id="cbo_currency_job"/>    
                        <input type="hidden" id="booking_mst_id"/> 	
                    </td>
                </tr>
                <tr>
                	<td width="100" class="must_entry_caption">Company Name</td>
                    <td width="140"><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "fnc_get_company_config(this.value); ","","" );?></td>
                	<td width="90" class="must_entry_caption">Buyer Name</td>
                    <td width="140" id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "","" ); ?></td>
                    <td width="90" class="must_entry_caption">Requisition Date</td>
                    <td width="140"><input class="datepicker" type="text" style="width:120px" name="txt_req_date" id="txt_req_date" value="<?=date('d-m-Y'); ?>" disabled /></td>
                    <td width="100" class="must_entry_caption">Level</td>   
                    <td><? echo create_drop_down( "cbo_level", 130, $level_arr,"", 0, "", 2, "","","" ); ?></td>
                </tr>
                <tr>
                	<td class="must_entry_caption">Responsible Dept.</td>
                    <td><?=create_drop_down( "cbo_responsible_dept", 130,"select id, department_name from lib_department where status_active=1 and is_deleted=0 order by  department_name", "id,department_name", 0, "", '', '','','' ); ?></td>
                    <td class="must_entry_caption">Res. person</td>
                    <td><input name="cbo_responsible_person" id="cbo_responsible_person" class="text_boxes" type="text" value="" style="width:120px "/></td>
                    <td>Division</td>
                    <td><? echo create_drop_down( "cbo_division_id", 130, $short_division_array,"", 1, "--Select--", $selected, "" ); ?></td>
                    <td>Ready To Approved</td>  
                    <td><? echo create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
                </tr>
                <tr>
                	<td class="must_entry_caption">Reason</td>
                    <td colspan="3"><input name="txt_reason" id="txt_reason" class="text_boxes" type="text"  style="width:350px "/></td>
                    <td>Remarks</td>  
                    <td colspan="3"><input class="text_boxes" type="text" style="width:360px;"  name="txt_remarks" id="txt_remarks"/></td>
                </tr>
                <tr>
                    <td align="center" colspan="8" valign="top" id="app_sms2" style="font-size:18px; color:#F00">
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="8" valign="middle" class="button_container">
                    <? $date=date('d-m-Y'); echo load_submit_buttons( $permission, "fnc_short_trims_req", 0,0 ,"reset_form('trimsreq_1','','requisition_list_view','cbo_ready_to_approved,2*txt_req_date,".$date."')",1) ; ?>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="8">
                    <input type="button" value="Print Req." onClick="generate_trimreq_report('show_requisition_report',1);"  style="width:100px;" name="print_req" id="print_req" class="formbutton" /></td>
                </tr>
            </table>
        </fieldset>
    </form>
    <fieldset style="width:1050px;">
        <legend>Trims Requisition Item Form: &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;    
        Select Item: <input class="text_boxes" type="text" style="width:160px" onDblClick="fnc_process_data();" readonly placeholder="Double Click" name="txt_select_item" id="txt_select_item"/>
        <input class="text_boxes" type="hidden" style="width:160px"  readonly placeholder="Double Click" name="txt_selected_po" id="txt_selected_po"/>
        <input class="text_boxes" type="hidden" style="width:160px"  readonly placeholder="Double Click" name="txt_selected_trim_id" id="txt_selected_trim_id"/></legend>
        <div id="requisition_list_view"><font id="save_sms" style="color:#F00">Select New Item</font></div>
    </fieldset>
    <div id="requisition_list_view_list"></div>
</div>
<div style="display:none" id="data_panel"></div>
</body>
<script>
	set_multiselect('cbo_responsible_dept','0','0','','0');
</script>
<script>

function copy_value(value,field_id,i){
	var copy_val=document.getElementById('copy_val').checked;
	var txttrimgroup=document.getElementById('txttrimgroup_'+i).value;
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

function generate_trim_report(action,report_type){
	if (form_validation('txt_booking_no','Booking No')==false){
		return;
	}
	else{

		var show_comment='';
		if(action=="show_trim_booking_report3"){
			var r=confirm("Press  \"Cancel\"  to hide  Rate,Amount\nPress  \"OK\"  to Show Rate,Amount");
				if (r==true){
					show_comment="1";
				}
				else{
					show_comment="0";
				}
		}else if(action=="show_trim_booking_report5"){
			var r=confirm("Press  \"Cancel\"  to hide  Rate,Amount\nPress  \"OK\"  to Show Rate,Amount");
				if (r==true){
					show_comment="1";
				}
				else{
					show_comment="0";
				}
		}else if(action=="show_trim_booking_report2"){
			var r=confirm("Press  \"Cancel\"  to hide  Rate\nPress  \"OK\"  to Show Rate");
				if (r==true){
					show_comment="1";
				}
				else{
					show_comment="0";
				}
		}else{

			var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
			if (r==true){
				show_comment="1";
			}
			else{
				show_comment="0";
			}
		}
		$report_title=$( "div.form_caption" ).html();
		var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id',"../../")+'&report_title='+$report_title+'&show_comment='+show_comment+'&report_type='+report_type;
		http.open("POST","requires/short_trims_req_multi_job_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;
	}	
}

function generate_trim_report_reponse(){
	if(http.readyState == 4){
		var file_data=http.responseText.split("****");
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0]);
        var report_title=$( "div.form_caption" ).html();
        var w = window.open("Surprise", "_blank");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title>'+report_title+'</title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
        d.close();
	}
}
//for print button

function print_report_button_setting(report_ids){
	var report_id=report_ids.split(",");
	$("#print_booking1").hide();
	$("#print_booking2").hide();
	$("#print_booking3").hide();
	$("#print_booking4").hide();
	for (var k=0; k<report_id.length; k++){
		if(report_id[k]==19){
			$("#print_booking2").show();
		}else if(report_id[k]==67){
			$("#print_booking1").show();
		}else if(report_id[k]==16){
			$("#print_booking3").show();
		}else if(report_id[k]==177){
			$("#print_booking4").show();
		}
	}
}
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
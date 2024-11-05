<?
/*-------------------------------------------- Comments
Purpose			: 	This form will All Approval
Functionality	:	
JS Functions	:
Created by		:	
Creation date 	: 	
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$menu_id=$_SESSION['menu_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("All Approval", "../", 1, 1,'','','');
$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
var permission='<?=$permission; ?>';
	
//======================================function start===================	
	var executeType=0;
	//Pre costing 
	function fn_report_generated(type)
	{	executeType=type;
		//alert("Pre Costing search panel");
		freeze_window(3);
		var approval_setup =<?=$approval_setup; ?>;
		if(approval_setup!=1)
		{
			alert("Electronic Approval Setting First.");
			release_freezing();	
			return;
		}
		if(type==1){ //type 1= Pre-Costing Approval;
			if (form_validation('cbo_company_name_pre','Comapny Name')==false)
			{
				release_freezing();
				return;
			}
			//var cbo_company_name=
			var data="action=report_generate&cbo_company_name="+$('#cbo_company_name_pre').val()+"&cbo_buyer_name="+$('#cbo_buyer_name').val()+"&cbo_get_upto="+$('#cbo_get_upto_pre').val()+"&txt_date='"+$('#txt_date_pre').val()+"'&cbo_approval_type="+$('#cbo_approval_type_pre').val();
			
			http.open("POST","requires/pre_costing_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse_pre;
		}
		else if(type==2){ //type 2 = Fabric Booking Approval;
			
			if (form_validation('cbo_company_name_fabric_booking','Comapny Name')==false)
			{
				release_freezing();
				return;
			}
			//var cbo_company_name=
			var data="action=report_generate&cbo_company_name="+$('#cbo_company_name_fabric_booking').val()+"&cbo_buyer_name="+$('#cbo_buyer_name').val()+"&cbo_get_upto="+$('#cbo_get_upto_fabric_booking').val()+"&txt_date="+$('#txt_date_fabric_booking').val()+"&cbo_approval_type="+$('#cbo_approval_type_fabric_booking').val();
			http.open("POST","requires/fabric_booking_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse_fabric_booking;
		}
		else if(type==3){ //type 3 = Trims Booking;
			if (form_validation('cbo_company_name_trims_bking_aprvl','Comapny Name')==false)
			{
				release_freezing();
				return;
			}
			var data="action=report_generate&cbo_company_name="+$('#cbo_company_name_trims_bking_aprvl').val()+"&cbo_booking_type="+$('#cbo_booking_type_trims_bking_aprvl').val()+"&cbo_approval_type="+$('#cbo_approval_type_trims_bking_aprvl').val();
			//var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_approval_type*cbo_booking_type',"../");
			//alert(data);return;
			http.open("POST","requires/trims_booking_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse_trims_bking_aprvl;
		}
		else if(type==8){ //type 8 = Urmi Trims Booking;
			if (form_validation('cbo_company_name_trims_bking_aprvl','Comapny Name')==false)
			{
				release_freezing();
				return;
			}
			// action report_generate2 change by report_generate . update by ashraful
			var data="action=report_generate&cbo_company_name="+$('#cbo_company_name_trims_bking_aprvl').val()+"&cbo_booking_type="+$('#cbo_booking_type_trims_bking_aprvl').val()+"&cbo_approval_type="+$('#cbo_approval_type_trims_bking_aprvl').val();
			//var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_approval_type*cbo_booking_type',"../");
			//alert(data);return;
			http.open("POST","requires/trims_booking_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse_trims_bking_aprvl;
		}
		else if(type==4){ //type 3 = Short fabric Booking;
			if (form_validation('cbo_company_name_short_feb_booking_aprvl','Comapny Name')==false)
			{
				release_freezing();
				return;
			}


			var data="action=report_generate&cbo_company_name="+$('#cbo_company_name_short_feb_booking_aprvl').val()+"&cbo_buyer_name="+$('#cbo_buyer_name').val()+"&txt_booking_no="+$('#txt_booking_no_short_fab_booking').val()+"&cbo_get_upto="+$('#cbo_get_upto_short_feb_booking_aprvl').val()+"&txt_date='"+$('#txt_date_short_feb_booking_aprvl').val()+"'&cbo_approval_type="+$('#cbo_approval_type_short_feb_booking_aprvl').val();
			
			http.open("POST","requires/short_feb_booking_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse_short_feb_booking_aprvl;
		}
		else if(type==5){
			if (form_validation('cbo_company_name_non_ord_smple_bking_aprvl','Comapny Name')==false)
			{
				release_freezing();
				return;
			}
			var data="action=report_generate&cbo_company_name="+$('#cbo_company_name_non_ord_smple_bking_aprvl').val()+"&cbo_buyer_name="+$('#cbo_buyer_name').val()+"&cbo_approval_type="+$('#cbo_approval_type_non_ord_smple_bking_aprvl').val();
			
			http.open("POST","requires/non_order_sample_booking_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse_non_ord_smple_bking_aprvl;
		}
		else if(type==6){
			if (form_validation('cbo_company_name_smple_bking_apvrl_with_ord','Comapny Name')==false)
			{
				release_freezing();
				return;
			}
			var data="action=report_generate&cbo_company_name="+$('#cbo_company_name_smple_bking_apvrl_with_ord').val()+"&cbo_buyer_name="+$('#cbo_buyer_name').val()+"&cbo_get_upto="+$('#cbo_get_upto_smple_bking_apvrl_with_ord').val()+"&txt_date="+$('#txt_date_smple_bking_apvrl_with_ord').val()+"&cbo_approval_type="+$('#cbo_approval_type_smple_bking_apvrl_with_ord').val();
			
			http.open("POST","requires/sample_feb_booking_wo_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse_smple_bking_apvrl_with_ord;
		}
		else if(type==7){
			if (form_validation('cbo_company_name_pi_aprvl','Comapny Name')==false)
			{
				release_freezing();
				return;
			}
			
			var data="action=report_generate&cbo_company_name="+$('#cbo_company_name_pi_aprvl').val()+"&cbo_supplier_id="+$('#cbo_supplier_id').val()+"&cbo_get_upto="+$('#cbo_get_upto_pi_aprvl').val()+"&txt_date="+$('#txt_date_pi_aprvl').val()+"&cbo_approval_type="+$('#cbo_approval_type_pi_aprvl').val();
			
			http.open("POST","requires/pi_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse_pi_aprvl;
		}//type end;
		else if(type==9){
			if (form_validation('cbo_company_name_pi','Comapny Name')==false)
			{
				release_freezing();
				return;
			}
			
			var data="action=report_generate&cbo_company_name="+$('#cbo_company_name_pi').val()+"&cbo_supplier_id="+$('#cbo_pi_supplier_id').val()+"&cbo_get_upto="+$('#cbo_get_upto_pi').val()+"&txt_date="+$('#txt_date_pi').val()+"&cbo_approval_type="+$('#cbo_approval_type_pi').val();
			
			http.open("POST","requires/pi_approval_new_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse_pi;
		}//type end;
	}
	
	function fn_report_generated_reponse_pi()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container_pi_aprvl').html(response[0]);
			
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
				
			show_msg('3');
			$('#txt_bar_code').focus();
			release_freezing();
		}
	}
	
	function submit_approved_pi_aprvl_new(total_tr,type,permission)
	{ 
		//var operation=4; 
		var booking_nos = "";  var booking_ids = ""; var approval_ids = ""; var appv_instras="";
		freeze_window(0);
        if (permission ==2) {            
            alert('You Have No Authority for signing PI Approval'); 
			release_freezing();
            return false;	    
        }
		// Confirm Message  *********************************************************************************************************
		if($('#cbo_approval_type_pi').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Booking No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
			
		}
		else if($('#cbo_approval_type_pi').val()==0)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to Approved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All Booking No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// Confirm Message End ***************************************************************************************************
		
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				booking_id = $('#booking_id_'+i).val();
				if(booking_ids=="") booking_ids= booking_id; else booking_ids +=','+booking_id;
				
				booking_no = $('#booking_no_'+i).val();
				if(booking_nos=="") booking_nos="'"+booking_no+"'"; else booking_nos +=",'"+booking_no+"'";
				
				approval_id = parseInt($('#approval_id_'+i).val());
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
				appv_instra = $('#txt_appv_instra_'+i).val();
				if(appv_instras=="") appv_instras="'"+appv_instra+"'"; else appv_instras +=",'"+appv_instra+"'";
			}
		}
		
		if(booking_nos=="")
		{
			alert("Please Select At Least One PI");
			release_freezing();
			return;
		}
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+'&appv_instras='+appv_instras+get_submitted_data_string('cbo_company_name_pi',"../");
	
		http.open("POST","requires/pi_approval_new_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_pi_approval_Reply_info;
	}	
	
	function fnc_pi_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');	
			if(reponse[0]=="seq")
			{
				alert("Sequence Not Found.");
				release_freezing();
				return;
			}
			show_msg(trim(reponse[0]));
			
			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
			}
			
			//$('#txt_bar_code').val('');
			//$('#txt_bar_code').focus();
			release_freezing();	
		}
	}
	
	
	//*****************************************PI approval new Finish *********************************************************
	function fn_report_generated_reponse_pre()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container_pre').html(response[0]);
			
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
				
			show_msg('3');
			release_freezing();
		}
	}
	
	function fn_report_generated_reponse_fabric_booking()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container_fabric_booking').html(response[0]);
			
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
				
			show_msg('3');
			$('#txt_bar_code').focus();
			release_freezing();
		}
	}
	
	function fn_report_generated_reponse_trims_bking_aprvl()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container_trims_bking_aprvl').html(response[0]);
			
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
				
			$( "#txt_bar_code" ).focus();
			
			show_msg('3');
			release_freezing();
		}
	}
	
	function fn_report_generated_reponse_short_feb_booking_aprvl()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container_short_feb_booking_aprvl').html(response[0]);
			
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
				
			show_msg('3');
			release_freezing();
		}
		
	}
	
	function fn_report_generated_reponse_non_ord_smple_bking_aprvl()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container_non_ord_smple_bking_aprvl').html(response[0]);
			
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
				
			show_msg('3');
			release_freezing();
		}
		
	}
	
	function fn_report_generated_reponse_smple_bking_apvrl_with_ord()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container_smple_bking_apvrl_with_ord').html(response[0]);
			
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
				
			show_msg('3');
			release_freezing();
		}
		
	}
	
	function fn_report_generated_reponse_pi_aprvl()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container_pi_aprvl').html(response[0]);
			
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
				
			show_msg('3');
			$('#txt_bar_code').focus();
			release_freezing();
		}
	}
	
	
	//common approved function..........................................
	function submit_approved(total_tr,type)
	{
		var xhttp = new XMLHttpRequest();
		  xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
			 if( this.responseText == 0 ){
				 alert("Session time out or you have no permission in this page. Please login again.");
				 release_freezing();
				 window.location.href ='../logout.php';
				 return 0;
				}
				else{
					//---------------------
					if(executeType==1){submit_approved_pre(total_tr,type);}	
					else if(executeType==2){submit_approved_fabric_booking(total_tr,type);}	
					else if(executeType==3){submit_approved_trims_booking(total_tr,type);}
					else if(executeType==4){submit_approved_short_feb(total_tr,type);}	
					else if(executeType==5){submit_approved_non_ord_smple_bking(total_tr,type);}	
					else if(executeType==6){submit_approved_with_ord_smple_bking(total_tr,type);}
					else if(executeType==7){submit_approved_pi_aprvl(total_tr,type);}
					else if(executeType==8){submit_approved_trims_booking(total_tr,type);}
					else if(executeType==9){submit_approved_pi_aprvl_new(total_tr,type);}
					//--------------------------------
				}
			}
		  };
		  xhttp.open("GET", "../tools/valid_user_action.php?menuid="+document.getElementById('active_menu_id').value, true);
		  xhttp.send();	
	}

	//....................... 1
	function submit_approved_pre(total_tr,type)
	{ 
		//var operation=4; 
		var booking_nos = "";  var booking_ids = ""; var approval_ids = ""; 
		freeze_window(0);
		// confirm message  ************************************
		if($('#cbo_approval_type_pre').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Job");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Job");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// confirm message finish *********************************

		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				booking_id = $('#booking_id_'+i).val();
				if(booking_ids=="") booking_ids= booking_id; else booking_ids +=','+booking_id;
				
				booking_no = $('#booking_no_'+i).val();
				if(booking_nos=="") booking_nos="'"+booking_no+"'"; else booking_nos +=",'"+booking_no+"'";
				
				approval_id = parseInt($('#approval_id_'+i).val());
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
			}
		}
		
		
		if(booking_nos=="")
		{
			alert("Please Select At Least One Job");
			release_freezing();
			return;
		}
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+'&cbo_company_name='+$('#cbo_company_name_pre').val();
	  
		http.open("POST","requires/pre_costing_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=submit_approved_pre_Reply;
	}	
	
	function submit_approved_pre_Reply()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]=="seq")
			{
				alert("Sequence Not Found.");
				release_freezing();
				return;
			}	
			show_msg(reponse[0]);
			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
			}
			
			$('#txt_bar_code').val('');
			$('#txt_bar_code').focus();
			release_freezing();	
		}
	}

	//....................... 2
	function submit_approved_fabric_booking(total_tr,type)
	{ 
		//alert(total_tr+"=="+type); return;
		//var operation=4;
		var booking_nos = "";  var booking_ids = ""; var approval_ids = ""; var appv_instras="";
		freeze_window(0);
		// Confirm Message  **********************************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Booking No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
			
		}
		else if($('#cbo_approval_type').val()==0)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to Approved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All Booking No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// Confirm Message End **********************************
		
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				booking_id = $('#booking_id_'+i).val();
				if(booking_ids=="") booking_ids= booking_id; else booking_ids +=','+booking_id;
				
				booking_no = $('#booking_no_'+i).val();
				if(booking_nos=="") booking_nos="'"+booking_no+"'"; else booking_nos +=",'"+booking_no+"'";
				
				approval_id = parseInt($('#approval_id_'+i).val());
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
				appv_instra = $('#txt_appv_instra_'+i).val();
				if(appv_instras=="") appv_instras="'"+appv_instra+"'"; else appv_instras +=",'"+appv_instra+"'";
			}
		}
		
		
		
 		if(type==1){
			var unappReq=return_global_ajax_value( trim(booking_ids)+'**'+booking_nos, 'check_unapprove_req', '', 'requires/fabric_booking_approval_controller');
			var unappReqArr=unappReq.split('***');
			if(trim(unappReqArr[0])!=''){
				alert("Not found any un-approved request for this booking ["+trim(unappReqArr[0])+"]. Please uncheck this booking.");
				release_freezing();	
				return;
			}
			if(unappReqArr[1]){
				alert("Issue/Receive found for this booking ["+unappReqArr[1]+"]. Please uncheck this booking.");
				release_freezing();	
				return;
			}
		} 
		
		 //release_freezing();	return ;
		
		if(booking_nos=="")
		{
			alert("Please Select At Least One Booking");
			release_freezing();
			return;
		}
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+'&appv_instras='+appv_instras+'&cbo_company_name='+$('#cbo_company_name_fabric_booking').val();
	
		http.open("POST","requires/fabric_booking_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=submit_approved_fabric_booking_Reply;
	}	
	
	function submit_approved_fabric_booking_Reply()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]=="seq")
			{
				alert("Sequence Not Found.");
				release_freezing();
				return;
			}	
			show_msg(trim(reponse[0]));
			
			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
			}
			
			$('#txt_bar_code').val('');
			$('#txt_bar_code').focus();
			release_freezing();	
		}
	}
	
	//....................... 3
	function submit_approved_trims_booking(total_tr,type)
	{ 
		//var operation=4;
		var booking_nos = "";  var booking_ids = ""; var approval_ids = ""; 
		freeze_window(0);
		// Confirm Message  ***********************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Booking No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		else if($('#cbo_approval_type').val()==0)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to Approved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All Booking No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// Confirm Message End ***********************************************************
		
		
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				booking_id = $('#booking_id_'+i).val();
				if(booking_ids=="") booking_ids= booking_id; else booking_ids +=','+booking_id;
				
				booking_no = $('#booking_no_'+i).val();
				if(booking_nos=="") booking_nos="'"+booking_no+"'"; else booking_nos +=",'"+booking_no+"'";
				
				approval_id = $('#approval_id_'+i).val();
				if(approval_id>0)
				{
				if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
			}
		}
		

		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+'&cbo_company_name='+$('#cbo_company_name_trims_bking_aprvl').val()+'&cbo_booking_type='+$('#cbo_booking_type_trims_bking_aprvl').val();
	
		http.open("POST","requires/trims_booking_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=submit_approved_trims_booking_Reply;
	}	
	
	function submit_approved_trims_booking_Reply()
	{
		if(http.readyState == 4) 
		{
			
			var reponse=trim(http.responseText).split('**');	
			if(reponse[0]=="seq")
			{
				alert("Sequence Not Found.");
				release_freezing();
				return;
			}
			show_msg(reponse[0]);
			
			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
			}
			
			release_freezing();	
		}
	}
	
	//....................... 4
	function submit_approved_short_feb(total_tr,type)
	{ 
		//var operation=4;
		var booking_nos = "";  var booking_ids = ""; var approval_ids = ""; var appv_instras="";
		freeze_window(0);
		// Confirm Message  ******************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();	
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Booking No");
					if(second_confirmation==false)
					{
						release_freezing();	
						return;					
					}
				}
			}
			
		}
		else if($('#cbo_approval_type').val()==0)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to Approved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();	
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All Booking No");
					if(second_confirmation==false)
					{
						release_freezing();	
						return;					
					}
				}
			}
			
		}
		// Confirm Message End *************************************
		
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				booking_id = $('#booking_id_'+i).val();
				if(booking_ids=="") booking_ids= booking_id; else booking_ids +=','+booking_id;
				
				booking_no = $('#booking_no_'+i).val();
				if(booking_nos=="") booking_nos="'"+booking_no+"'"; else booking_nos +=",'"+booking_no+"'";
				
				approval_id = parseInt($('#approval_id_'+i).val());
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
				appv_instra = $('#txt_appv_instra_'+i).val();
				if(appv_instras=="") appv_instras="'"+appv_instra+"'"; else appv_instras +=",'"+appv_instra+"'";
			}
		}
		
		
		//alert(booking_nos);release_freezing();	return;
		
 		if(type==1){
			var unappReq=return_global_ajax_value( trim(booking_ids)+'**'+booking_nos, 'check_unapprove_req', '', 'requires/fabric_booking_approval_controller');
			var unappReqArr=unappReq.split('***');
			if(trim(unappReqArr[0])!=''){
				alert("Not found any un-approved request for this booking ["+trim(unappReqArr[0])+"]. Please uncheck this booking.");
				release_freezing();	
				return;
			}
			if(unappReqArr[1]){
				alert("Issue/Receive found for this booking ["+unappReqArr[1]+"]. Please uncheck this booking.");
				release_freezing();	
				return;
			}
		} 
		
		//release_freezing();	return;
		

		
		if(booking_nos=="")
		{
			alert("Please Select At Least One Booking");
			release_freezing();	
			return;
		}
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+'&appv_instras='+appv_instras+'&cbo_company_name='+$('#cbo_company_name_short_feb_booking_aprvl').val();
	
		http.open("POST","requires/short_feb_booking_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=submit_approved_short_feb_Reply;
	}	
	
	function submit_approved_short_feb_Reply()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]=="seq")
			{
				alert("Sequence Not Found.");
				release_freezing();
				return;
			}	
			show_msg(trim(reponse[0]));
			
			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
			}
			$('#txt_bar_code').val('');
			$('#txt_bar_code').focus();
			release_freezing();	
		}
	}
	
	//....................... 5
	function submit_approved_non_ord_smple_bking(total_tr,type)
	{ 
		//var operation=4; 
		var booking_nos = "";  var booking_ids = ""; var approval_ids = ""; 
		freeze_window(0);
		// Confirm Message **************************************************
		if($('#cbo_approval_type_non_ord_smple_bking_aprvl').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();	
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Booking No");
					if(second_confirmation==false)
					{
						release_freezing();	
						return;					
					}
				}
			}
		}
		else if($('#cbo_approval_type_non_ord_smple_bking_aprvl').val()==0)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to Approved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();	
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All Booking No");
					if(second_confirmation==false)
					{
						release_freezing();	
						return;					
					}
				}
			}
		}
		// Confirm Message End ***********************************
		
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				booking_id = $('#booking_id_'+i).val();
				if(booking_ids=="") booking_ids= booking_id; else booking_ids +=','+booking_id;
				
				booking_no = $('#booking_no_'+i).val();
				if(booking_nos=="") booking_nos="'"+booking_no+"'"; else booking_nos +=",'"+booking_no+"'";
				
				approval_id = $('#approval_id_'+i).val();
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
			}
		}
		
		
 		if(type==1){
			var unappReq=return_global_ajax_value( trim(booking_ids)+'**'+booking_nos, 'check_unapprove_req', '', 'requires/fabric_booking_approval_controller');
			var unappReqArr=unappReq.split('***');
			if(unappReqArr[1]){
				alert("Issue/Receive found for this booking ["+unappReqArr[1]+"]. Please uncheck this booking.");
				release_freezing();	
				return;
			}
		} 
		
		
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+'&cbo_company_name='+$('#cbo_company_name_non_ord_smple_bking_aprvl').val();
	
		http.open("POST","requires/non_order_sample_booking_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=submit_approved_non_ord_smple_bking_Reply;
	}	
	
	function submit_approved_non_ord_smple_bking_Reply()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]=="seq")
			{
				alert("Sequence Not Found.");
				release_freezing();
				return;
			}	
			show_msg(reponse[0]);
			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
			}
			$('#txt_bar_code').val('');
			$('#txt_bar_code').focus();
			release_freezing();	
		}
	}
	//....................... 6
	function submit_approved_with_ord_smple_bking(total_tr,type)
	{ 
		//var operation=4;
		var booking_nos = "";  var booking_ids = ""; var approval_ids = ""; var appv_instras="";
		freeze_window(0);
		// Confirm Message  **********************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Booking No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
			
		}
		else if($('#cbo_approval_type').val()==0)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to Approved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All Booking No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// Confirm Message End ****************************************************
		
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				booking_id = $('#booking_id_'+i).val();
				if(booking_ids=="") booking_ids= booking_id; else booking_ids +=','+booking_id;
				
				booking_no = $('#booking_no_'+i).val();
				if(booking_nos=="") booking_nos="'"+booking_no+"'"; else booking_nos +=",'"+booking_no+"'";
				
				approval_id = parseInt($('#approval_id_'+i).val());
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
				appv_instra = $('#txt_appv_instra_'+i).val();
				if(appv_instras=="") appv_instras="'"+appv_instra+"'"; else appv_instras +=",'"+appv_instra+"'";
			}
		}
		
		if(booking_nos=="")
		{
			alert("Please Select At Least One Booking");
			release_freezing();
			return;
		}
		
 		if(type==1){
			var unappReq=return_global_ajax_value( trim(booking_ids)+'**'+booking_nos, 'check_unapprove_req', '', 'requires/fabric_booking_approval_controller');
			var unappReqArr=unappReq.split('***');
			if(unappReqArr[1]){
				alert("Issue/Receive found for this booking ["+unappReqArr[1]+"]. Please uncheck this booking.");
				release_freezing();	
				return;
			}
		} 
		
		
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+'&appv_instras='+appv_instras+'&cbo_company_name='+$('#cbo_company_name_smple_bking_apvrl_with_ord').val();
	
		http.open("POST","requires/sample_feb_booking_wo_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=submit_approved_with_ord_smple_bking_Reply;
	}	
	
	function submit_approved_with_ord_smple_bking_Reply()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]=="seq")
			{
				alert("Sequence Not Found.");
				release_freezing();
				return;
			}
			show_msg(trim(reponse[0]));
			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
			}
			
			$('#txt_bar_code').val('');
			$('#txt_bar_code').focus();
			release_freezing();	
		}
	}
	
	//....................... 7
	function submit_approved_pi_aprvl(total_tr,type)
	{ 
		//alert(total_tr+"=="+type); return;
		//var operation=4;
		var booking_nos = "";  var booking_ids = ""; var approval_ids = ""; var appv_instras="";
		freeze_window(0);
		// Confirm Message  *********************************************************************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Booking No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
			
		}
		else if($('#cbo_approval_type_pi_aprvl').val()==0)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to Approved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All Booking No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// Confirm Message End *********************************
		
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				booking_id = $('#booking_id_'+i).val();
				if(booking_ids=="") booking_ids= booking_id; else booking_ids +=','+booking_id;
				
				booking_no = $('#booking_no_'+i).val();
				if(booking_nos=="") booking_nos="'"+booking_no+"'"; else booking_nos +=",'"+booking_no+"'";
				
				approval_id = parseInt($('#approval_id_'+i).val());
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
				appv_instra = $('#txt_appv_instra_'+i).val();
				if(appv_instras=="") appv_instras="'"+appv_instra+"'"; else appv_instras +=",'"+appv_instra+"'";
			}
		}
		
		if(booking_nos=="")
		{
			alert("Please Select At Least One Booking");
			release_freezing();
			return;
		}
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+'&appv_instras='+appv_instras+'&cbo_company_name='+$('#cbo_company_name_pi_aprvl').val();
	
		http.open("POST","requires/pi_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=submit_approved_pi_aprvl_Reply;
	}	
	
	function submit_approved_pi_aprvl_Reply()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');	
			if(reponse[0]=="seq")
			{
				alert("Sequence Not Found.");
				release_freezing();
				return;
			}
			show_msg(trim(reponse[0]));
			
			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
			}
			
			$('#txt_bar_code').val('');
			$('#txt_bar_code').focus();
			release_freezing();	
		}
	}
	
	function fnc_remove_tr()
	{
		var tot_row=$('#tbl_list_search tbody tr').length;
		for(var i=1;i<=tot_row;i++)
		{
			if($('#tbl_'+i).is(':checked'))
			{
				$('#tr_'+i).remove();
			}
		}
		$('#all_check').attr('checked',false);
	}

	
	function openImgFile(id,action)
	{
		var page_link='requires/pre_costing_approval_controller.php?action='+action+'&id='+id;
		if(action=='img') var title='Image View'; else var title='File View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
	}
	
	//=========================================================
	function check_last_update(rowNo)
	{
		if(executeType==2){check_last_update_fabric_booking(rowNo);}
		if(executeType==4){check_last_update_short_fabric_booking(rowNo);}
		if(executeType==5){check_booking_approved(rowNo);}
		if(executeType==6){check_last_update_sample_with_order_booking(rowNo);}
	}
	
	function check_booking_approved(rowNo)
	{
		//alert("su..re"); return;
		var isChecked=$('#tbl_'+rowNo).is(":checked");
		var booking_no=$('#booking_no_'+rowNo).val();
		var approval_type=$('#cbo_approval_type_non_ord_smple_bking_aprvl').val();
		
	
		if(isChecked==true && approval_type==1)
		{
			var salse_order_approved=return_global_ajax_value( trim(booking_no), 'check_sales_order_approved', '', 'requires/non_order_sample_booking_approval_controller');
			if(salse_order_approved==1 || salse_order_approved==3)
			{
				alert("Corresponding Sales Order is Approved.So Booking Unapproved Not Allow.");
				$('#tbl_'+rowNo).attr('checked',false);
			}
		}
	}

	function check_last_update_sample_with_order_booking(rowNo)
	{ 
		//alert("su..re"); return;
		var isChecked=$('#tbl_'+rowNo).is(":checked");
		var dealing_merchant=$('#dealing_merchant_'+rowNo).text();
		var hide_approval_type=$('#cbo_approval_type_smple_bking_apvrl_with_ord').val();
		var booking_no=$('#booking_no_'+rowNo).val();
		
		if(isChecked==true)
		{
			var last_update=return_global_ajax_value( trim(booking_no), 'check_booking_last_update', '', 'requires/sample_feb_booking_wo_approval_controller');
			if(last_update==2)
			{
				alert("Booking ("+trim(booking_no)+") Info not synchronized with order entry and pre-costing. Contact To "+dealing_merchant+".");
				$('#tbl_'+rowNo).attr('checked',false);
			}
			//return;
		}
		
		if(isChecked==true && hide_approval_type==1)
		{
			var salse_order_approved=return_global_ajax_value( trim(booking_no), 'check_sales_order_approved', '', 'requires/sample_feb_booking_wo_approval_controller');
			if(salse_order_approved==1 || salse_order_approved==3)
			{
				alert("Corresponding Sales Order is Approved.So Booking Unapproved Not Allow.");
				$('#tbl_'+rowNo).attr('checked',false);
			}
		}
	}



	function check_last_update_short_fabric_booking(rowNo)
	{ 
		//alert("su..re"); return;
		var isChecked=$('#tbl_'+rowNo).is(":checked");
		var booking_no=$('#booking_no_'+rowNo).val();
		var approval_type=$("#cbo_approval_type_short_feb_booking_aprvl").val();
		if(approval_type==1)
		{
			var salse_order_approved=return_global_ajax_value( trim(booking_no), 'check_sales_order_approved', '', 'requires/short_feb_booking_approval_controller');
			if(salse_order_approved==1 || salse_order_approved==3)
			{
				alert("Corresponding Sales Order is Approved.So Booking Unapproved Not Allow.");
				$('#tbl_'+rowNo).attr('checked', false);
			}
			else 
			{
				//$('#tbl_'+rowNo).attr('checked', true);
			}
		}
	}


	function check_last_update_fabric_booking(rowNo)
	{ 
		//alert("su..re"); return;
		var isChecked=$('#tbl_'+rowNo).is(":checked");
		var dealing_merchant=$('#dealing_merchant_'+rowNo).text();
		//var last_update=$('#last_update_'+rowNo).val();
		var booking_no=$('#booking_no_'+rowNo).val();
		var hide_approval_type=$('#cbo_approval_type_fabric_booking').val();
		
		if(isChecked==true && hide_approval_type!=1)
		{
			var last_update=return_global_ajax_value( trim(booking_no), 'check_booking_last_update', '', 'requires/fabric_booking_approval_controller');
			if(last_update==2)
			{
				alert("Booking ("+trim(booking_no)+") Info not synchronized with order entry and pre-costing. Contact To "+dealing_merchant+".");
				$('#tbl_'+rowNo).attr('checked',false);
			}
			//return;
		}

		if(isChecked==true && hide_approval_type==1)
		{
			var salse_order_approved=return_global_ajax_value( trim(booking_no), 'check_sales_order_approved', '', 'requires/fabric_booking_approval_controller');

			if(salse_order_approved==1 || salse_order_approved==3)
			{

				alert("Corresponding Sales Order is approved.So Booking Unapproved Not Allow.");
				$('#tbl_'+rowNo).attr('checked',false);
			}
		}
	}
		
	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			if(executeType==2){
				var approval_type=$("#cbo_approval_type_fabric_booking").val();
			}
			else if(executeType==4){
				var approval_type=$("#cbo_approval_type_short_feb_booking_aprvl").val();
			}
			else if(executeType==5){
				var approval_type=$("#cbo_approval_type_non_ord_smple_bking_aprvl").val();
			}
			else if(executeType==6){
				var approval_type=$("#cbo_approval_type_smple_bking_apvrl_with_ord").val();
			}

			
			var sl=0;
			$('#tbl_list_search tbody').find('tr').each(function() {
				sl++;
				if(sl>1)
				{
					if(executeType==2 || executeType==4 || executeType==5 || executeType==6)
					{
						if( approval_type==1)
						{
							var booking_no=$(this).find('input[name="booking_no[]"]').val();
							var salse_order_approved=return_global_ajax_value( trim(booking_no), 'check_sales_order_approved', '', 'requires/fabric_booking_approval_controller');
							if(salse_order_approved==1 || salse_order_approved==3)
							{
								alert("Corresponding Sales Order is approved.So Booking Unapproved Not Allow.");
								$(this).find('input[name="tbl[]"]').attr('checked', false);
							}
							else 
							{
								$(this).find('input[name="tbl[]"]').attr('checked', true);
							}
						}else{
							$(this).find('input[name="tbl[]"]').attr('checked', true);
						}
					}
					else
					{
						$(this).find('input[name="tbl[]"]').attr('checked', true);
					}
				}				
			});
		}
		else
		{ 
			$('#tbl_list_search tbody tr').each(function() {
				$('#tbl_list_search tbody tr input:checkbox').attr('checked', false);
			});
		} 
	}
	
	function generate_mkt_report(job_no,booking_no,order_id,fab_nature,fab_source,action)
	{
		var page_link='requires/fabric_booking_approval_controller.php?action='+action+'&job_no='+job_no+'&booking_no='+booking_no+'&order_id='+order_id+'&fab_nature='+fab_nature+'&fab_source='+fab_source;
		var title='Comments View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=1,scrolling=0','');
	}
	
	function generate_comment_popup(booking_no,company,action)
	{
		var page_link='requires/trims_booking_approval_controller.php?action='+action+'&booking_no='+booking_no+'&company='+company;
		var title='Comments View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=370px,center=1,resize=1,scrolling=0','');
	}
	
	function generate_trim_booking_report(txt_booking_no,cbo_company_name,cbo_isshort,id_approved_id,type,i)
	{
		var show_comment='';
		var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
		if (r==true)
		{
			show_comment="1";
		}
		else
		{
			show_comment="0";
		}
		//alert(show_comment);return;
	
		//var show_comment='1';
		var data="action="+type+
					'&txt_booking_no='+"'"+txt_booking_no+"'"+
					'&cbo_company_name='+"'"+cbo_company_name_trims_bking_aprvl+"'"+
					'&report_title='+"Multiple Order Wise Trims Booking"+
					'&show_comment='+"'"+show_comment+"'"+
					'&cbo_isshort='+"'"+cbo_isshort+"'"+
					'&id_approved_id='+"'"+id_approved_id+"'"+
					'&path=../';
						
		http.open("POST","../order/woven_order/requires/trims_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			
			if(http.readyState == 4) 
			{
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
		   }
			
		}
	}	
	
	function generate_worder_report(a,b,c,d,e,f,g,h,i,j,k,l,m,n,o)
	{
		
		if(executeType==1){
			//type,job_no,company_id,buyer_id,style_ref,txt_costing_date;
			generate_worder_report_pre_cost(a,b,c,d,e,f);
			}
		else if(executeType==2){
//txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,type,i;
			generate_worder_report_fabric_booking(a,b,c,d,e,f,g,h,i,j);
			}
		else{
			//is_approved,booking_no,company_id;
			generate_worder_report_sample_booking(a,b,c);
			}
	}
	
	function generate_worder_report_pre_cost2(type,job_no,company_id,buyer_id,style_ref,txt_costing_date)
	{
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true)
		{
			zero_val="1";
		}
		else
		{
			zero_val="0";
		} 
		var data="action="+type+
			'&zero_value='+zero_val+
			'&txt_job_no='+"'"+job_no+"'"+
			'&cbo_company_name='+"'"+company_id+"'"+
			'&cbo_buyer_name='+"'"+buyer_id+"'"+
			'&txt_style_ref='+"'"+style_ref+"'"+
			'&txt_costing_date='+"'"+txt_costing_date+"'";
			http.open("POST","../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = function()
			{
				if(http.readyState == 4) 
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
					d.close();
				}
			}
	}
	
	function generate_worder_report_pre_cost(type,job_no,company_id,buyer_id,style_ref,txt_costing_date,entry_form,quotation_id)
	{
		//$("#txt_style_ref").val(style_ref);
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true)
		{
			zero_val="1";
		}
		else
		{
			zero_val="0";
		}
		if(type=="summary" || type=="budget3_details")
		{
			//alert("i am here.");
			
			if(type=='summary')
			{
				var rpt_type=3;var comments_head=0;
			}
			else if(type=='budget3_details')
			{
				var rpt_type=4;var comments_head=1;
			}
			
			
			var report_title="Budget/Cost Sheet";
			//var comments_head=0;
			var txt_style_ref_id='';
			var sign=0;

			var txt_order=""; var txt_order_id="";  var txt_season_id=""; var txt_season=""; var txt_file_no="";
			var data="action=report_generate&reporttype="+rpt_type+
			'&cbo_company_name='+"'"+company_id+"'"+
			'&cbo_buyer_name='+"'"+buyer_id+"'"+
			'&txt_style_ref='+"'"+style_ref+"'"+
			'&txt_style_ref_id='+"'"+txt_style_ref_id+"'"+
			'&txt_order='+"'"+txt_order+"'"+
			'&txt_order_id='+"'"+txt_order_id+"'"+
			'&txt_season='+"'"+txt_season+"'"+
			'&sign='+"'"+sign+"'"+
			'&txt_season_id='+"'"+txt_season_id+"'"+
			'&txt_file_no='+"'"+txt_file_no+"'"+
			'&txt_quotation_id='+quotation_id+
			'&txt_hidden_quot_id='+quotation_id+
			'&comments_head='+"'"+comments_head+"'"+
			'&report_title='+"'"+report_title+"'"+
			'&path=../../../';
		//	alert(data)
			http.open("POST","../reports/management_report/merchandising_report/requires/cost_breakup_report2_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = function()
			{
				if(http.readyState == 4) 
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
					d.close();
				}
			}
		}
		else
		{
			if(entry_form==111)
			{
				var data="action="+type+
						'&zero_value='+zero_val+
						'&txt_job_no='+"'"+job_no+"'"+
						'&cbo_company_name='+"'"+company_id+"'"+
						'&cbo_buyer_name='+"'"+buyer_id+"'"+
						'&txt_style_ref='+"'"+style_ref+"'"+
						'&print_option_id='+'1,2,3,4,5,6,7,8'+
						'&txt_po_breack_down_id='+''+
						'&txt_costing_date='+"'"+txt_costing_date+"'";
						http.open("POST","../order/woven_order/requires/pre_cost_entry_controller.php",true);
						http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
						http.send(data);
						http.onreadystatechange = function()
						{
							if(http.readyState == 4) 
							{
								var w = window.open("Surprise", "#");
								var d = w.document.open();
								d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
						'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
								d.close();
							}
						}
			}
			else
			{
				var data="action="+type+
				'&zero_value='+zero_val+
				'&txt_job_no='+"'"+job_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&cbo_buyer_name='+"'"+buyer_id+"'"+
				'&txt_style_ref='+"'"+style_ref+"'"+
				//'&txt_style_ref='+"'"+style_ref+"'"+
				'&txt_costing_date='+"'"+txt_costing_date+"'";//+get_submitted_data_string('txt_style_ref',"../../");
				//alert(data)
				http.open("POST","../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = function()
				{
					if(http.readyState == 4) 
					{
						var w = window.open("Surprise", "#");
						var d = w.document.open();
						d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
						d.close();
					}
				}
			}
		}
	}
	
	
	
	function generate_worder_report(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,print_id,type,action,i)
	{ 
		
		if(print_id==45 || print_id==53 ){
		//var report_title='Budget Wise Fabric Booking';
		var data="action="+action+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+"Main Fabric Booking V2"+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		
		'&path=../';
	} else {
		var data="action="+action+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+"Partial Fabric Booking"+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		
		'&path=../';
	}
		//alert(data);return;
		//var data="action="+show_fabric_booking_report_gr+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../")+'&report_title='+$report_title+'&show_yarn_rate='+show_yarn_rate+'&path=../../';
		
		//$report_title=$( "div.form_caption" ).html();
		
		// var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no*i',"../../")+'&path=../../';
			
		freeze_window(5);
		//http.open("POST","requires/fabric_booking_controller.php",true);
		if(print_id==45 || print_id==53 )
		{
			http.open("POST","../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
		}
		else
		{
			http.open("POST","../order/woven_order/requires/partial_fabric_booking_controller.php",true);
		}
		
						
					
		/*if(type==1)	
		{			
			http.open("POST","../order/woven_order/requires/short_fabric_booking_controller.php",true);
		}
		else if(action=='show_fabric_booking_report_gr')
		{
			http.open("POST","../order/woven_order/requires/fabric_booking_controller.php",true);
		}
		else
		{
			http.open("POST","../order/woven_order/requires/sample_booking_controller.php",true);
		}*/
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
				release_freezing();
		   }
			
		}
	}

	function generate_worder_report11(type,report_type,txt_booking_no,cbo_company_name,txt_order_no_id,item_category,cbo_fabric_source,txt_job_no,id_approved_id,action,i)
	{  
		var data="action="+action+
		'&type='+"'"+type+"'"+
		'&report_type='+"'"+report_type+"'"+
        '&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&item_category='+"'"+item_category+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+"Short Fabric Booking"+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		
		
		'&path=../';
			
		freeze_window(5);
		http.open("POST","../order/woven_order/requires/short_fabric_booking_controller.php",true);
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
				release_freezing();
		   }
			
		}
	}

	function generate_worder_report12(type,txt_booking_no,cbo_company_name,txt_order_no_id,item_category,cbo_fabric_source,txt_job_no,id_approved_id,action,i)
	{  
		var data="action="+action+
		'&type='+"'"+type+"'"+
        '&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&item_category='+"'"+item_category+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+"Sample Fabric Booking With-Order"+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		
		'&path=../';
			
		freeze_window(5);
		http.open("POST","../order/woven_order/requires/sample_booking_controller.php",true);
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
				release_freezing();
		   }
			
		}
	}

	function generate_worder_report13(txt_booking_no,cbo_company_name,id_approved_id,item_category,action,i)
	{  
		var data="action="+action+
        '&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&item_category='+"'"+item_category+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		
		'&path=../';
			
		freeze_window(5);
		http.open("POST","../order/woven_order/requires/sample_booking_non_order_controller.php",true);
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
				release_freezing();
		   }
			
		}
	}



	
	
	function generate_worder_report_fabric_booking2222(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,type,i)
	{
		//var report_title='Budget Wise Fabric Booking';
		var data="action="+type+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+"Budget Wise Fabric Booking"+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		
		'&path=../';
		http.open("POST","../order/woven_order/requires/fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
		   }
			
		}
	}
	
	function generate_worder_report_sample_booking(is_approved,booking_no,company_id)
	{
		var data="action=show_fabric_booking_report"+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&id_approved_id='+"'"+is_approved+"'";
					
		if(executeType==1){
		http.open("POST","../order/woven_order/requires/sample_booking_non_order_controller.php",true);
		}
		else{
		http.open("POST","../order/woven_order/requires/sample_booking_non_order_controller.php",true);
		}
		
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
			}
		}
	}
//================================================end	
	
</script>
<script>

function fnc_close(str){
     $('#report_container_pre').html('');
     $('#report_container_fabric_booking').html('');
     $('#report_container_trims_bking_aprvl').html('');
     $('#report_container_short_feb_booking_aprvl').html('');
     $('#report_container_non_ord_smple_bking_aprvl').html('');
     $('#report_container_smple_bking_apvrl_with_ord').html('');
     $('#report_container_pi_aprvl').html('');
	 //$('#content_search_panel_pi_aprvl_new').html('');
	 


	if(str!='content_search_panel_pre')$('#content_search_panel_pre').hide(500);
	if(str!='content_search_panel_fabric_booking')$('#content_search_panel_fabric_booking').hide(500);
	if(str!='content_search_panel_short_feb_booking_aprvl')$('#content_search_panel_short_feb_booking_aprvl').hide(500);
	if(str!='content_search_panel_non_ord_smple_bking_aprvl')$('#content_search_panel_non_ord_smple_bking_aprvl').hide(500);
	if(str!='content_search_panel_smple_bking_apvrl_with_ord')$('#content_search_panel_smple_bking_apvrl_with_ord').hide(500);
	if(str!='content_search_panel_trims_bking_aprvl')$('#content_search_panel_trims_bking_aprvl').hide(500);
	if(str!='content_search_panel_pi_aprvl')$('#content_search_panel_pi_aprvl').hide(500);
	//if(str!='content_search_panel_pi_aprvl_new')$('#content_search_panel_pi_aprvl_new').hide(500);

}


function fnc_hide_show()
{
	accordion_menu( 'accordion_h2','content_search_panel_pre', '');
	accordion_menu( 'accordion_h3','content_search_panel_fabric_booking', '');
	accordion_menu( 'accordion_h4','content_search_panel_short_feb_booking_aprvl','');
	accordion_menu( 'accordion_h5','content_search_panel_non_ord_smple_bking_aprvl', '');
	accordion_menu( 'accordion_h6','content_search_panel_smple_bking_apvrl_with_ord', '');
	accordion_menu( 'accordion_h9','content_search_panel_trims_bking_aprvl', '');
	accordion_menu( 'accordion_h10','content_search_panel_pi_aprvl', '');
	
}

function generate_trim_booking_report(txt_booking_no,cbo_company_name,cbo_isshort,id_approved_id,entry_form,type,i)
	{
		
		//alert(entry_form);
		var show_comment='';
		var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
		if (r==true)
		{
			show_comment="1";
		}
		else
		{
			show_comment="0";
		}
		//alert(show_comment);return;
	//var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_isshort',"../../")+'&report_title='+$report_title+'&show_comment='+show_comment;
		//var show_comment='1';
		var data="action="+type+
					'&txt_booking_no='+"'"+txt_booking_no+"'"+
					'&cbo_company_name='+"'"+cbo_company_name+"'"+
					'&report_title='+"Multiple Order Wise Trims Booking Urmi"+
					'&show_comment='+"'"+show_comment+"'"+
					'&cbo_isshort='+"'"+cbo_isshort+"'"+
					'&id_approved_id='+"'"+id_approved_id+"'"+
					
						'&path=../';
						
		if(entry_form==87)
		{
		http.open("POST","../order/woven_order/requires/trims_booking_multi_job_controllerurmi.php",true);
		}
		else if(entry_form==43)
		{
		http.open("POST","../order/woven_order/requires/trims_booking_urmi_controller.php",true);
		}
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
		   }
			
		}
	}	
</script>
</head>

<body>
	
      <!-- Pre-Costing -->
    <div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:800px;margin-top:10px;" align="left" id="accordion_h2" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel_pre','fnc_close(this.id)')">-Pre Costing Approval</h3> 
         <div id="content_search_panel_pre">
             <fieldset style="width:800px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr> 
                            	<th colspan="6">Barcode Scan: <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes" /></th>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
                                <th>Buyer</th>
                                <th>Get Upto</th>
                                <th>Costing  Date</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container_pre','','','')" class="formbutton" style="width:100px" /></th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name_pre", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/pre_costing_approval_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id_pre' );" );
                                    ?>
                                </td>
                                <td id="buyer_td_id_pre"> 
									<?
                                       echo create_drop_down( "cbo_buyer_name_pre", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
                                    ?>
                                </td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                       echo create_drop_down( "cbo_get_upto_pre", 130, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_date_pre" id="txt_date_pre" class="datepicker" readonly style="width:80px"/></td>
                                <td> 
                                    <?
									  $pre_cost_approval_type=array(2=>"Un-Approved",1=>"Approved");
                                        echo create_drop_down( "cbo_approval_type_pre", 130, $pre_cost_approval_type,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated(1)"/></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container_pre" align="center"></div>
  
    <!--Fabric Booking Approval --> 
    <div style="width:100%;" align="center">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:800px;margin-top:10px;" align="left" id="accordion_h3" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel_fabric_booking','fnc_close(this.id)')">-Fabric Booking Approval</h3> 
         <div id="content_search_panel_fabric_booking">      
             <fieldset style="width:800px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr> 
                            	<th colspan="6">Barcode Scan: <input type="text" id="txt_bar_code_fabric_booking" name="txt_bar_code_fabric_booking" class="text_boxes"  /></th>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
                                <th>Buyer</th>
                                <th>Get Upto</th>
                                <th>Booking Date</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res_fabric_booking" id="res_fabric_booking" value="Reset" onClick="reset_form('requisitionApproval_1','report_container_fabric_booking','','','')" class="formbutton" style="width:100px" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name_fabric_booking", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/fabric_booking_approval_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id_fabric_booking' );" );
                                    ?>
                                </td>
                                <td id="buyer_td_id_fabric_booking"> 
									<?
                                       echo create_drop_down( "cbo_buyer_name_fabric_booking", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
                                    ?>
                                </td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                       echo create_drop_down( "cbo_get_upto_fabric_booking", 130, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_date_fabric_booking" id="txt_date_fabric_booking" class="datepicker" readonly style="width:80px"/></td>
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_approval_type_fabric_booking", 130, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated(2)"/></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container_fabric_booking" align="center"></div>
    
    <!--Trims Booking-->
    <div style="width:100%;" align="center">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:800px;margin-top:10px;" align="left" id="accordion_h9" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel_trims_bking_aprvl','fnc_close(this.id)')">-Trims Booking </h3> 
         <div id="content_search_panel_trims_bking_aprvl">      
             <fieldset style="width:650px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr> 
                            	<th colspan="4">Barcode Scan: <input type="text" id="txt_bar_code_trims_bking_aprvl" name="txt_bar_code_trims_bking_aprvl" class="text_boxes" /></th>
                            </tr>
                            <tr>
                            <th class="must_entry_caption">Company Name</th>
                            <th class="must_entry_caption">Booking Type</th>
                            
                            <th>Approval Type</th>
                            <th><input type="reset" name="res_trims_bking_aprvl" id="res_trims_bking_aprvl" value="Reset" onClick="reset_form('requisitionApproval_1','report_container_trims_bking_aprvl','','','')" class="formbutton" style="width:100px" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name_trims_bking_aprvl", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                    ?>
                                </td>
                                
                                
                                <td> 
                                    <?
									$booking_type_arr=array(1=>'With Order',2=>'WithOut Order');
                                        echo create_drop_down( "cbo_booking_type_trims_bking_aprvl", 140, $booking_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_approval_type_trims_bking_aprvl", 140, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="hidden" value="Show" name="show" id="show" class="formbutton" style="width:50px" onClick="fn_report_generated(3)"/>
                                <input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated(8)"/>
                                </td>                	
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container_trims_bking_aprvl" align="center"></div>
    
    <!-- Short Fabric Booking approval -->
   	<div style="width:100%;" align="center">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:800px;margin-top:10px;" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel_short_feb_booking_aprvl','fnc_close(this.id)')">-Short Fabric Booking Approval</h3> 
         <div id="content_search_panel_short_feb_booking_aprvl">      
             <fieldset style="width:800px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr> 
                            	<th colspan="6">Barcode Scan: <input type="text" id="txt_bar_code_short_feb_booking_aprvl" name="txt_bar_code_short_feb_booking_aprvl" class="text_boxes"  /></th>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
                                <th>Buyer</th>
                                <th>Booking No</th>
                                <th>Get Upto</th>
                                <th>Booking Date</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res_short_feb_booking_aprvl" id="res_short_feb_booking_aprvl" value="Reset" onClick="reset_form('requisitionApproval_1','report_container_short_feb_booking_aprvl','','','')" class="formbutton" style="width:100px" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name_short_feb_booking_aprvl", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/short_feb_booking_approval_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id_short_feb_booking_aprvl' );" );
                                    ?>
                                </td>
                                <td id="buyer_td_id_short_feb_booking_aprvl"> 
									<?
                                       echo create_drop_down( "cbo_buyer_name_short_feb_booking_aprvl", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
                                    ?>
                                </td>
                                <td><input name="txt_booking_no_short_fab_booking" id="txt_booking_no_short_fab_booking" class="text_boxes" style="width:60px"></td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                       echo create_drop_down( "cbo_get_upto_short_feb_booking_aprvl", 100, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_date_short_feb_booking_aprvl" id="txt_date_short_feb_booking_aprvl" class="datepicker" readonly style="width:65px"/></td>
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_approval_type_short_feb_booking_aprvl", 130, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated(4)"/></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container_short_feb_booking_aprvl" align="center"></div>
    
    <!-- None Order(without order) Sample Booking approval -->
   	 <div style="width:100%;" align="center">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:800px;margin-top:10px;" align="left" id="accordion_h5" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel_non_ord_smple_bking_aprvl','fnc_close(this.id)')">-Without Order Sample Booking Approval</h3> 
         <div id="content_search_panel_non_ord_smple_bking_aprvl">      
             <fieldset style="width:650px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr> 
                            	<th colspan="4">Barcode Scan: <input type="text" id="txt_bar_code_non_ord_smple_bking_aprvl" name="txt_bar_code_non_ord_smple_bking_aprvl" class="text_boxes"  /></th>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
                                <th>Buyer</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res_non_ord_smple_bking_aprvl" id="res_non_ord_smple_bking_aprvl" value="Reset" onClick="reset_form('requisitionApproval_1','report_container_non_ord_smple_bking_aprvl','','','')" class="formbutton" style="width:100px" /></th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name_non_ord_smple_bking_aprvl", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/non_order_sample_booking_approval_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id_non_ord_smple_bking_aprvl' );" );
                                    ?>
                                </td>
                                <td id="buyer_td_id_non_ord_smple_bking_aprvl"> 
									<?
                                       echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
                                    ?>
                                </td>
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_approval_type_non_ord_smple_bking_aprvl", 140, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated(5)"/></td>                	
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container_non_ord_smple_bking_aprvl" align="center"></div>
    
    <!--Sample Booking approval (With Order) -->
   	 <div style="width:100%;" align="center">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:800px;margin-top:10px;" align="left" id="accordion_h6" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel_smple_bking_apvrl_with_ord','fnc_close(this.id)')">-Sample Booking Approval (With Order)</h3> 
         <div id="content_search_panel_smple_bking_apvrl_with_ord">      
             <fieldset style="width:800px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr> 
                            	<th colspan="6">Barcode Scan: <input type="text" id="txt_bar_code_smple_bking_apvrl_with_ord" name="txt_bar_code_smple_bking_apvrl_with_ord" class="text_boxes"  /></th>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
                                <th>Buyer</th>
                                <th>Get Upto</th>
                                <th>Booking Date</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res_smple_bking_apvrl_with_ord" id="res_smple_bking_apvrl_with_ord" value="Reset" onClick="reset_form('requisitionApproval_1','report_container_smple_bking_apvrl_with_ord','','','')" class="formbutton" style="width:100px" /></th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name_smple_bking_apvrl_with_ord", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_feb_booking_wo_approval_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id_smple_bking_apvrl_with_ord' );" );
                                    ?>
                                </td>
                                <td id="buyer_td_id_smple_bking_apvrl_with_ord"> 
									<?
                                       echo create_drop_down( "cbo_buyer_name_smple_bking_apvrl_with_ord", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
                                    ?>
                                </td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                       echo create_drop_down( "cbo_get_upto_smple_bking_apvrl_with_ord", 130, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_date_smple_bking_apvrl_with_ord" id="txt_date_smple_bking_apvrl_with_ord" class="datepicker" readonly style="width:80px"/></td>
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_approval_type_smple_bking_apvrl_with_ord", 130, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated(6)"/></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container_smple_bking_apvrl_with_ord" align="center"></div>
    


    
     <!--PI Approval New -->
	<div style="width:100%;" align="center">
		 <form name="piApproval_1" id="piApproval_1"> 
         <h3 style="width:800px;margin-top:10px;" align="left" id="accordion_h10" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel_pi_aprvl','fnc_close(this.id)')">-PI Approval</h3> 
         <div id="content_search_panel_pi_aprvl">      
             <fieldset style="width:800px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr>
                                <th class="must_entry_caption">Company</th>
                                <th>Supplier</th>
                                <th>Get Upto</th>
                                <th>PI Date</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container_pi_aprvl','','','')" class="formbutton" style="width:100px" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name_pi", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/pi_approval_new_controller',this.value, 'load_supplier_dropdown_pi_new', 'supplier_td_pi_new' );" );
                                    ?>
                                </td>
                                <td id="supplier_td_pi_new"> 
									<?
                                       echo create_drop_down( "cbo_pi_supplier_id", 152, $blank_array,"", 1, "-- All Supplier --", 0, "" );
                                    ?>
                                </td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                       echo create_drop_down( "cbo_get_upto_pi", 130, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_date_pi" id="txt_date_pi" class="datepicker" readonly style="width:80px"/></td>
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_approval_type_pi", 130, $approval_type_arr,"", 0, "", 0,"","", "" );
										 // echo create_drop_down( "cbo_approval_type_smple_bking_apvrl_with_ord", 130, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated(9)"/></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container_pi_aprvl" align="center"></div>
    
    
    
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_approval_type_pre').val(0);
$('#cbo_approval_type_fabric_booking').val(0);
$('#cbo_approval_type_short_feb_booking_aprvl').val(0);
$('#cbo_approval_type_non_ord_smple_bking_aprvl').val(0);
$('#cbo_approval_type_smple_bking_apvrl_with_ord').val(0);
$('#cbo_approval_type_trims_bking_aprvl').val(0);
$('#cbo_approval_type_pi').val(0);
fnc_hide_show();
</script>
</html>
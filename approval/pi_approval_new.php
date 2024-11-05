<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create PI Approval
Functionality	:	
JS Functions	:
Created by		:	Md. Didarul Alam
Creation date 	: 	09-04-2016
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
$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("PI Approval", "../", 1, 1,'','','');
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fn_report_generated(type)
	{
		var approval_setup =<? echo $approval_setup; ?>;
		freeze_window(3);
		if(approval_setup!=1)
		{
			alert("Electronic Approval Setting First.");	
			release_freezing();	
			return;
		}

		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			release_freezing();	
			return;
		}
		var previous_approved=0;
		if($('#previous_approved').is(":checked")) previous_approved=1;
		var data="action=report_generate&type="+type+"&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_supplier_id*cbo_get_upto*txt_date*cbo_approval_type*txt_alter_user_id*txt_pi_no*txt_pi_sys_id_no*cbo_buyer_name*cbo_brand_id*cbo_season_name*cbo_season_year*txt_style_ref',"../");
		//alert(data);
		
		http.open("POST","requires/pi_approval_new_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container').html(response[0]);
			
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
				
			show_msg('3');
			$('#txt_bar_code').focus();
			release_freezing();
		}
	}
	
	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$("#tbl_list_search tbody").find('tr').each(function()
			{
				try 
				{
					var dealing_merchant=$(this).find("td:eq(8)").text();
					var booking_no=$(this).find('input[name="booking_no[]"]').val();
					var hide_approval_type=parseInt($('#hide_approval_type').val());
					
					if(!(hide_approval_type==1))
					{
						var last_update=return_global_ajax_value( trim(booking_no), 'check_booking_last_update', '', 'requires/pi_approval_controller');
						if(last_update==2)
						{
							alert("Booking ("+trim(booking_no)+") Info not synchronized with order entry and pre-costing. Contact To "+dealing_merchant+".");
							$(this).find('input[name="tbl[]"]').attr('checked', false);
						}
						else
						{
							$(this).find('input[name="tbl[]"]').attr('checked', true);
						}
					}
					else
					{
						$(this).find('input[name="tbl[]"]').attr('checked', true);
					}
				}
				catch(e) 
				{
					//got error no operation
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
	// ==============================
	function check_on_scan(scan_no)
	{
		var row_no=$('#'+scan_no).val();
		var dealing_merchant=$('#dealing_merchant_'+row_no).html();
		var hide_approval_type=parseInt($('#hide_approval_type').val());
		var tbl_len=$("#tbl_list_search tbody tr").length;
		if(!(hide_approval_type==1))
		{
			var last_update=return_global_ajax_value( trim(scan_no), 'check_booking_last_update', '', 'requires/pi_approval_new_controller');
			if(last_update==2)
			{
				alert("Booking ("+trim(scan_no)+") Info not synchronized with order entry and pre-costing. Contact To "+dealing_merchant+".");
				$('#tbl_'+row_no).attr('checked', false);
			}
			else
			{
				$('#tbl_'+row_no).attr('checked', true);
				//submit_approved(tbl_len,$('#cbo_approval_type').val());
			}
		}
		else
		{
			$('#tbl_'+row_no).attr('checked', true);
			//submit_approved(tbl_len, $('#cbo_approval_type').val());
		}
		
		//new
		if($('#tbl_'+row_no).is(":checked")==false)
		{
			alert("No data found");
			$('#txt_bar_code').val('');
			$('#txt_bar_code').focus();
			return;
		} 
		else
		{
			submit_approved(tbl_len, $('#cbo_approval_type').val());
		}
	}
	
	function check_last_update(rowNo)
	{
		var isChecked=$('#tbl_'+rowNo).is(":checked");
		var dealing_merchant=$('#dealing_merchant_'+rowNo).text();
		var booking_no=$('#booking_no_'+rowNo).val();
		var booking_id=$('#booking_id_'+rowNo).val();
		var hide_approval_type=$('#hide_approval_type').val();
		
		if(isChecked==true && hide_approval_type!=1)
		{
			var last_update=return_global_ajax_value( trim(booking_id), 'check_booking_last_update', '', 'requires/pi_approval_new_controller');
			if(last_update==2)
			{
				alert("Booking ("+trim(booking_no)+") Info not synchronized with order entry and pre-costing. Contact To "+dealing_merchant+".");
				$('#tbl_'+rowNo).attr('checked',false);
			}
			//return;
		}
	}
	// ==============================================
	
	function submit_approved(total_tr,type,permission)
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
		
	
		// Confirm Message End ***************************************************************************************************
		var unappv_cause_arr=Array();
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				
				var unappv_cause = $('#txt_unappv_cause_'+i).val();
				if(unappv_cause!=''){
					unappv_cause_arr.push(unappv_cause);
				}
				
				
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
		
		if(type==5 && unappv_cause_arr.length==0)
		{
			alert("Deny is not allowed without Refusing cause");
			release_freezing();
			return;
		}
		
		if(type==5 || type==0){
			$('#txt_selected_id').val(booking_ids);
			fnSendMail('../','',1,0,0,1,type,$("#cbo_company_name").val()+'_63_1');
		}
		
		
		var alterUserID = $('#txt_alter_user_id').val();
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+'&txt_alter_user_id='+alterUserID+'&appv_instras='+appv_instras+get_submitted_data_string('cbo_company_name',"../");
	
		
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
			show_msg(reponse[0]);
			
			if(reponse[0]==19 || reponse[0]==20 || reponse[0]==50)
			{
				fnc_remove_tr();
			}
			if(reponse[0]==25)
			{
				fnc_remove_tr();
				alert("You Have No Authority To Approved this.");
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
	
	function generate_worder_report(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,type,i)
	{
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
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
				d.close();
		   }
			
		}
	}
	
	$('#txt_bar_code').live('keydown', function(e) {
		if  (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code').val();
			check_on_scan(bar_code);
		}
	});
	
	function change_user()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var title = 'PI Approval New';	
		var page_link = 'requires/pi_approval_new_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"
			
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
		
			//load_drop_down( 'requires/pi_approval_new_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
			$("#report_container").html('');
		}
	}
	
	function change_approval_type(value)
	{
	
		if(value==0)
		{
			$("#previous_approved").val(1);
			$("#cbo_approval_type").val(1);
			$("#cbo_approval_type").attr("disabled",true);	
		}
		else
		{
			$("#previous_approved").val(0);
			$("#cbo_approval_type").val(0);
			$("#cbo_approval_type").attr("disabled",false);
		}		
	}
	
	function downloiadFile(id,company_name)
	{
		var title = 'PI Approval New File Download';	
		var page_link = 'requires/pi_approval_new_controller.php?action=get_user_pi_file&id='+id+'&company_name='+company_name;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			
		}

	}

	function fnc_pi_cross_check(pi_id,company_id) {
		
		var title = 'Cross Check Details';
		var page_link = 'requires/pi_approval_new_controller.php?item_category=1&importer_id='+company_id+'&pi_id='+pi_id+'&action=cross_check_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=250px,center=1,resize=1,scrolling=0','../');
	}

	function fnc_file_popup(pi_id,company_id) {
		
		var title = 'File Details';
		var page_link = 'requires/pi_approval_new_controller.php?company_id='+company_id+'&pi_id='+pi_id+'&action=file_no_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');
	}
	
	
	function openmypage_app_cause(wo_id,app_type,i,app_from,user_id)
	{
		if(app_from==1)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();
		}
		else
		{
			var txt_appv_cause = $("#txt_unappv_cause_"+i).val();
		}
		//alert(txt_appv_cause);
		var approval_id = $("#approval_id_"+i).val();

		var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id+"_"+app_from+"_"+user_id;
		//alert(data);return;
		var title = 'Approval Cause Info';
		var page_link = 'requires/pi_approval_new_controller.php?data='+data+'&action=appcause_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');

		emailwindow.onclose=function()
		{
			var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
			if(app_from==1)
				$('#txt_appv_cause_'+i).val(appv_cause.value);
			else
				$('#txt_unappv_cause_'+i).val(appv_cause.value);
		}
	}
	
	/*function openmypage_unapp_cause(wo_id,app_type,i)
	{
		var txt_appv_cause = $("#txt_appv_cause_"+i).val();
		var approval_id = $("#approval_id_"+i).val();

		var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
		//alert(data);return;
		var title = 'Approval Cause Info';
		var page_link = 'requires/pi_approval_new_controller.php?data='+data+'&action=appcause_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');

		emailwindow.onclose=function()
		{
			var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
			$('#txt_unappv_cause_'+i).val(appv_cause.value);
		}
	}*/
	
	
	
	function openPopup(pi_id)
	{
		
		
		var title = 'All Job List';
		var page_link = 'requires/pi_approval_new_controller.php?action=all_job_by_pi_popup&pi_id='+pi_id+'&company_name='+$('#cbo_company_name').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
		}
	}	
	
	function call_print_button_for_mail(mail,mail_body,type){
		 
		var booking_id=$('#txt_selected_id').val();
		var sysIdArr=booking_id.split(',');
		
		var mail=mail.split(',');
		
		if(type==0){//approve
			var ret_data=return_global_ajax_value(sysIdArr.join('*')+'__'+mail.join('*')+'__'+type+'__'+$('#txt_alter_user_id').val(), 'pi_approval_mail', '', '../auto_mail/approval/pi_approval_new_auto_mail');
		}
		else{//type ==5 deny
			var ret_data=return_global_ajax_value(sysIdArr.join('*')+'__'+mail.join('*')+'__'+type+'__'+$('#txt_alter_user_id').val(), 'deny_pi_approval_mail', '', '../auto_mail/approval/pi_approval_new_auto_mail');
		}
		
		alert(ret_data);
	}
	
	
	//action=preCostRpt2&zero_value=1&rate_amt=2&&txt_job_no='SSL-21-00281'&cbo_company_name='20'&cbo_buyer_name='253'&txt_style_ref='Knitting%20Receive%20Check'&txt_costing_date='26-Aug-2021'&txt_po_breack_down_id=''&cbo_costing_per='1'
	function generate_report(job_no)
	{
		var data=return_global_ajax_value( job_no, 'get_print_button_data', '', 'requires/pi_approval_new_controller');
		var rate_amt=2; var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true) zero_val="1"; else zero_val="0";
		freeze_window(3);
		http.open("POST","../order/sweater/requires/pre_cost_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}

	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
			show_msg('3');
			release_freezing();
			$('#data_panel').html('');
		} 
	}

	function print_button_setting(company)
	{
		$('#button_data_panel').html('');
		// alert(company);
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/pi_approval_new_controller' );
	}

	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{

			if(report_id[k]==108)
			{
				$('#button_data_panel')
					.append( ' <input type="button" value="Show" name="show" id="show" class="formbutton" style="width:70px" onClick="fn_report_generated(1)"/>&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==195)
			{
				$('#button_data_panel').append( ' <input type="button" value="Show 2" name="show" id="show" class="formbutton" style="width:70px" onClick="fn_report_generated(2)"/>&nbsp;&nbsp;&nbsp;' );
			}
			
			if(report_id[k]==242)
			{
				$('#button_data_panel').append( ' <input type="button" value="Show 3" name="show" id="show" class="formbutton" style="width:70px" onClick="fn_report_generated(3)"/>&nbsp;&nbsp;&nbsp;' );
			}	
		
		}
	}

</script>




</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:1350px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:1350px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr> 
                            	<th colspan="8">Barcode Scan: <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes"  /></th>
                                 <th colspan="3" align="center">
									<?php 
										$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
										if( $user_lavel==2)
										{
											?>
											Previous Approved: <input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes"  value="0"  onChange="change_approval_type(this.value)" />

									<?php
										}
										else
										{
									?>
											<input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes" style="display:none"  />
									<?php	
										}
									?> 
                                 
								</th>
                                <th colspan="3">
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
								?>
                                		<!--<input type="button" class="image_uploader" style="width:100px" value="CHANGE USER" onClick="change_user()">-->
                                        Alter User:
                                        <input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user()"/ placeholder="Browse " readonly>
                                <?php 
									}
									
								?>
                                 <input type="hidden" id="txt_selected_id" name="txt_selected_id" />
                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                                </th>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company</th>
                                <th>Buyer</th>
                                <th>Brand</th>
                                <th>Season</th>
                                <th>Season Year</th>
                                <th>Style Ref.</th>
                                <th>Supplier</th>
                                <th>Get Upto</th>
								<th>PI System ID</th>
                                <th>PI No</th>
                                <th>PI Date</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 100, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/pi_approval_new_controller',this.value, 'load_supplier_dropdown', 'supplier_td_id' );load_drop_down( 'requires/pi_approval_new_controller',this.value, 'load_dropdown_buyer', 'buyer_td' );print_button_setting();" );
                                    ?>
                                </td>
                                
                                <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                                <td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 100, $blank_array,'', 1, "--Brand--",$selected, "" ); ?></td>
                                <td id="season_td"><? echo create_drop_down( "cbo_season_name", 100, $blank_array,"", 1, "-- Select Season --", $selected, "" ); ?></td>
                                <td><? echo create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                                <td><input type="text" id="txt_style_ref" class="text_boxes" style="width:100px"/></td>
                                <td id="supplier_td_id"> 
									<?
                                       echo create_drop_down( "cbo_supplier_id", 100, $blank_array,"", 1, "-- All Supplier --", 0, "" );
                                    ?>
                                </td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                       echo create_drop_down( "cbo_get_upto", 100, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
								<td><input type="text" name="txt_pi_sys_id_no" id="txt_pi_sys_id_no" class="text_boxes_numeric" style="width:100px"/></td>
                                <td><input type="text" name="txt_pi_no" id="txt_pi_no" class="text_boxes" style="width:100px"/></td>
                                <td><input type="text" name="txt_date" id="txt_date" class="datepicker" readonly style="width:80px"/></td>
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_approval_type", 100, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>                              
					         	<td id="button_data_panel" align="center"> </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div style="display:none;" id="data_panel"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_approval_type').val(0);
</script>
</html>
﻿<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Lab Test Approval
Functionality	:	
JS Functions	:
Created by		:	
Creation date 	: 	01-10-2020
Updated by 		: 	Kausar	
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
//$menu_id=$_SESSION['menu_id'];
//$user_id=$_SESSION['logic_erp']['user_id'];
//--------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Lab Test Approval", "../", 1, 1,$unicode,1,'');
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fn_report_generated()
	{
		//var approval_setup =<?// echo $approval_setup; ?>;
		freeze_window(3);
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var approval_setup=return_ajax_request_value($("#cbo_company_name").val()+'__'+$("#active_menu_id").val(), 'approval_setupCheck', 'requires/lab_test_approval_controller');
		if(approval_setup!=1)
		{
			alert("Electronic Approval Setting First.");
			release_freezing();
			return;
		}
		if(($("#txt_date").val()!="" && $("#cbo_get_upto").val()==0) || ($("#txt_date").val()!="" && $("#cbo_get_upto").val()=="") )
		{
			alert("Select Get Upto First.");
			release_freezing();	
			$("#cbo_get_upto").focus();
			return;
		}

		var previous_approved=0;
		var menu_id=$("#active_menu_id").val();
		if($('#previous_approved').is(":checked")) previous_approved=1;
		var data="action=report_generate&previous_approved="+previous_approved+'&menu_id='+menu_id+get_submitted_data_string('cbo_company_name*cbo_get_upto*txt_date*txt_booking_no*cbo_approval_type*cbo_booking_type*cbo_booking_year*txt_alter_user_id*active_menu_id',"../");
		//alert(data);return;
		http.open("POST","requires/lab_test_approval_controller.php",true);
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
				
			$( "#txt_bar_code" ).focus();
			
			show_msg('3');
			release_freezing();
		}
	}
	
	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$('#tbl_list_search tbody tr').each(function() {
				$('#tbl_list_search tbody tr input:checkbox').attr('checked', true);
			});
		}
		else
		{ 
			$('#tbl_list_search tbody tr').each(function() {
				$('#tbl_list_search tbody tr input:checkbox').attr('checked', false);
			});
		} 
	}
		
	function submit_approved(total_tr,type)
	{ 
		 var booking_nos = "";  var booking_ids = ""; var approval_ids = ""; var appv_instras="";
		freeze_window(3);
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
				appv_instra = $('#txt_appv_instra_'+i).val();
				if(appv_instras=="") appv_instras="'"+appv_instra+"'"; else appv_instras +=",'"+appv_instra+"'";
			}
		}
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+'&txt_alter_user_id='+$('#txt_alter_user_id').val()+'&appv_instras='+appv_instras+get_submitted_data_string('cbo_company_name*cbo_booking_type*active_menu_id',"../");
		//alert(data); return;
		
		http.open("POST","requires/lab_test_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}	
	
	function fnc_purchase_requisition_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{
			// alert(http.responseText);
			var reponse=trim(http.responseText).split('**');	
			
			show_msg(reponse[0]);
			
			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
			}
			
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
	}
	
	function generate_trim_booking_report(txt_booking_no,report_type,cbo_company_name,cbo_isshort,id_approved_id,entry_form,type,i)
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
		
		var requestFile='';var title='';var path='';
		if(entry_form==44){
			requestFile="../order/woven_order/requires/trims_booking_controller_v2.php";
			title="Main Trims Booking V2";
			path='../';
		}
		else if(entry_form==43){
			requestFile="../order/woven_order/requires/trims_booking_urmi_controller.php";
			title="Main Trims Booking V2";
			path='../';
		}
		else if(entry_form==5){
			requestFile="../order/woven_order/requires/trims_sample_booking_with_order_controller.php";
			title="Sample Trims Booking";
			path='../';
		}
		else if(entry_form==262){
			requestFile="../order/woven_order/requires/short_trims_booking_multi_job_controllerurmi.php";
			title=" Multiple Job Short Trims Booking";
			path='../';
		}
		else{
			requestFile="../order/woven_order/requires/trims_booking_multi_job_controllerurmi.php";
			title="Multiple Job Wise Trims Booking";
			path='../';
		}
		//alert(show_comment);return; trims_booking_multi_job_controller
	
		//var show_comment='1';
		var data="action="+type+
					'&txt_booking_no='+"'"+txt_booking_no+"'"+
					'&cbo_company_name='+"'"+cbo_company_name+"'"+
					'&report_title='+title+
					'&show_comment='+"'"+show_comment+"'"+
					'&cbo_isshort='+"'"+cbo_isshort+"'"+
					'&id_approved_id='+"'"+id_approved_id+"'"+
					'&entry_form='+"'"+entry_form+"'"+
					'&report_type='+"'"+report_type+"'"+
					'&path='+path;
		
		http.open("POST",requestFile,true);
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
	
	function generate_comment_popup(booking_no,company,action)
	{
		//alert(booking_no);return;
		var page_link='requires/lab_test_approval_controller.php?action='+action+'&booking_no='+booking_no+'&company='+company;
		var title='Comments View';
		
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=370px,center=1,resize=1,scrolling=0','');
	}
	
	function check_on_scan(scan_no)
	{
		var row_no=$('#'+scan_no).val();
		var tbl_len=$("#tbl_list_search tbody tr").length;
		$('#tbl_'+row_no).attr('checked', true);
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
	
	$('#txt_bar_code').live('keydown', function(e) {
		if  (e.keyCode === 13) 
		{
			//alert("su..re"); return;
			e.preventDefault();
			var bar_code=$('#txt_bar_code').val();
			check_on_scan(bar_code);
		}
	});
	
	
	//---------------	
	function generate_fabric_report(txt_booking_no,cbo_company_name,id_approved_id,title,img_path)
	{
		var data="action=show_fabric_booking_report"+
					'&txt_booking_no='+"'"+txt_booking_no+"'"+
					'&cbo_company_name='+"'"+cbo_company_name+"'"+
					'&report_title='+"'"+title+"'"+
					'&id_approved_id='+"'"+id_approved_id+"'"+
					'&img_path='+"'"+img_path+"'"+
					'&path=../../';
		
		// alert(data);

		http.open("POST","../order/woven_order/requires/trims_sample_booking_without_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;
	}
	
	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var file_data=http.responseText.split('****');
			//$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}	
	
	function change_user()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var title = 'Trims Booking Approval';	
		var page_link = 'requires/fabric_booking_approval_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"
			
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			//load_drop_down( 'requires/pre_costing_approval_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
			load_drop_down( 'requires/fabric_booking_approval_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
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
</script>
</head>
<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:810px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:810px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr> 
                            	<th colspan="2">Barcode Scan:- <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes"  /></th>
                                 <th colspan="3" align="center">
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
										?>
                                		Previous Approved: <input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes"  value="0"  onChange="change_approval_type(this.value);"/>
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
                                       Alter User:<input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user();"/ placeholder="Browse " readonly>
                                <?php 
									}
								?>
                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                                </th>
                            </tr>
                            <tr>
                                <th width="160" class="must_entry_caption">Company Name</th>
                                <th width="100" class="must_entry_caption">WO Type</th>
                                <th width="60">WO Year</th>
                                <th width="100">WO No</th>
                                <th width="100">Get Upto</th>
                                <th width="70">WO Date</th>
                                <th width="120">Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:80px" /></th>
                                </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><?=create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" ); ?></td>
                                <td> 
                                    <?
										$booking_type_arr=array(1=>'With Order',2=>'WithOut Order');
                                        echo create_drop_down( "cbo_booking_type", 100, $booking_type_arr,"", 0, "", 1,"","", "" );
                                    ?>
                                </td>
                                <td><? echo create_drop_down( "cbo_booking_year", 60, $year,"", 1, "-- Select --", "", "" ); ?></td>
                                <td><input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes"  style="width:80px" placeholder="Write"/></td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                        echo create_drop_down( "cbo_get_upto", 100, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_date" id="txt_date" class="datepicker" style="width:60px"/></td>
                                <td><?=create_drop_down( "cbo_approval_type", 120, $approval_type_arr,"", 0, "", $selected,0,"", "" ); ?></td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:80px" onClick="fn_report_generated();"/></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div style="display:none" id="data_panel"></div>

</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	$("#cbo_approval_type").val(0);
</script>
</html>
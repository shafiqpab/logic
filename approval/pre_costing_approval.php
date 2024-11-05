<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Pre Costing Approval
Functionality	:	
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	14-10-2014
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
echo load_html_head_contents("Fabric Booking Approval", "../", 1, 1,'','','');
$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fn_report_generated(type)
	{
		var approval_setup ='<? echo $approval_setup; ?>';
		//	alert(approval_setup);
		freeze_window(3);
		if(approval_setup!=1)
		{
			alert("Electronic Approval Setting First.");
			release_freezing();	
			return;
		}
		
		/*if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			release_freezing();
			return;
		}*/
		var previous_approved=0;
		if($('#previous_approved').is(":checked")) previous_approved=1;
		
		if(type==1)
		{
			var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_working_company*cbo_buyer_name*txt_internal_ref*txt_file_no*cbo_get_upto*txt_date*cbo_approval_type*txt_job_no*cbo_year*txt_alter_user_id',"../");
		}
		else if(type==2){
			var data="action=report_generate_2&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_working_company*cbo_buyer_name*txt_internal_ref*txt_file_no*cbo_get_upto*txt_date*cbo_approval_type*txt_job_no*cbo_year*txt_alter_user_id',"../");
		}
		
		http.open("POST","requires/pre_costing_approval_controller.php",true);
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
			release_freezing();
		}
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
		var page_link = 'requires/pre_costing_approval_controller.php?data='+data+'&action=appcause_popup';

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
	function history_print()
	{

	}
		
	function submit_approved(total_tr,type)
	{ 
		//var operation=4;
		var booking_nos = "";  var booking_ids = ""; var approval_ids = ""; 
		freeze_window(0);
		// confirm message  *********************************************************************************************************
		if($('#cbo_approval_type').val()==1)
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
		// confirm message finish ***************************************************************************************************
		var mst_id_company_ids='';
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				var cm_cost_id = $('#cm_cost_id_'+i).val()*1;
				var fabric_id_cost = $('#fabric_id_cost_'+i).val()*1;
				var trims_id_cost = $('#trims_id_cost_'+i).val()*1;
				var copy_quot = $('#copy_quot_'+i).val()*1;
				var garments_nature = $('#garments_nature_'+i).val()*1;
				
				var cm_compulsory=$('#txt_cm_compulsory').val()*1;
				var sourcing_approved_id=$('#sourcing_approved_id_'+i).val()*1;
			//	alert(sourcing_approved_id+'_'+$('#cbo_approval_type').val());
				if($('#cbo_approval_type').val()==1 && sourcing_approved_id==1) //Budget Sourcing Un Appoved Validation
				{
					alert('Should be at first un approve sourcing');	
					release_freezing();
					 return;
				}
				
				if($('#cbo_approval_type').val()==2)
				{
					
					if(copy_quot==1 && garments_nature==3) //Copy Quotation yes
					{
						//alert(copy_quot+'='+trims_id_cost*1+'='+fabric_id_cost*1);
						if( (trims_id_cost*1)==0  ||  (fabric_id_cost*1)==0 )
						{
							alert('Details part is not save');	
							release_freezing();
							return;
						}
					}
					
					if(cm_compulsory==1)
					{
						//alert('MM');
						if(cm_cost_id<0 || cm_cost_id==0)
						{
							alert('Without CM Cost Approving not allowed');	
							release_freezing();
							return;
						}
					}
				}
				
				booking_id = $('#booking_id_'+i).val();
				if(booking_ids=="") booking_ids= booking_id; else booking_ids +=','+booking_id;
				
				var mst_id_company_id = $('#mst_id_company_id_'+i).val();
				if(mst_id_company_ids==""){mst_id_company_ids = mst_id_company_id;}
				else{mst_id_company_ids +=','+mst_id_company_id;}
				
				
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
		
		if($('#cbo_approval_type').val()==2) //Mail Send
		{
			if(type==5)//Deny
			{
				$('#txt_selected_id').val(booking_ids);
				fnSendMail('../','',1,0,0,1);
	
			  /*var email = prompt("Please enter your Email", "");
				if (email != null)
				 {
					 
					   var mailformat = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
						if(email.match(mailformat))
						{
						//alert("Valid email address!");
						 sendMail(booking_ids,email);
						}
						else
						{
						   alert("You have entered an invalid email address!");
						   release_freezing();
							return;
						}
				 }*/
									 
			//release_freezing();
			//return;
			}
		}
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+'&mst_id_company_ids='+mst_id_company_ids+get_submitted_data_string('cbo_company_name*txt_alter_user_id',"../");
	   //alert(data);
		
		http.open("POST","requires/pre_costing_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}	
	
	function fnc_purchase_requisition_approval_Reply_info()
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
			if((reponse[0]==19 || reponse[0]==20 || reponse[0]==50))
			{
				fnc_remove_tr();
			}	
				
			$('#txt_bar_code').val('');
			$('#txt_bar_code').focus();
			release_freezing();	
		}
	}
	
	function call_print_button_for_mail(mail) //Mail Send
	{
		var booking_id=$('#txt_selected_id').val();
		var sysIdArr=booking_id.split(',');
		
		var mail=mail.split(',');
		var ret_data=return_global_ajax_value(sysIdArr.join('*')+'__'+mail.join('*'), 'fabric_app', '', '../auto_mail/approval/pre_costing_approval_controller_auto_mail');
		alert(ret_data);
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
	
	function generate_worder_report_pre_cost(type,job_no,company_id,buyer_id,style_ref,txt_costing_date,entry_form,quotation_id,garments_nature)
	{
		$("#txt_style_ref").val(style_ref);
		var zero_val=''; 
		var img_show=1;
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true)
		{
			zero_val="1";
		}
		else
		{
			zero_val="0";
		}
		if(type=="summary" || type=="budget3_details" || type=="budget_4" || type=="budgetsheet3")
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
				else if(type=='budget_4')
				{
					var rpt_type=7;var comments_head=1;
				}else if(type=='budgetsheet3')
				{
					var rpt_type=8; comments_head=1;
					
				}
			
			var report_title="Budget/Cost Sheet";
			//var comments_head=0;
			var txt_style_ref_id='';
			var sign=0;
			var txt_style_ref_id=quotation_id;
			var txt_order=""; var txt_order_id="";  var txt_season_id=""; var txt_season=""; var txt_file_no="";
			var data="action="+type+
					'&zero_value='+zero_val+
					'&txt_job_no='+"'"+job_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&cbo_buyer_name='+"'"+buyer_id+"'"+
					'&txt_style_ref='+"'"+style_ref+"'"+
					'&txt_style_ref='+"'"+style_ref+"'"+
					'&img_show='+img_show+
					'&txt_po_breack_down_id='+''+
					'&txt_costing_date='+"'"+txt_costing_date+"'"+//order\sweater
					'&path='+"'"+path+"'";
			//.'&path=../../../';
			//alert(data)
			http.open("POST","requires/pre_costing_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
		}
		else
		{
			if(garments_nature==100)
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
					http.open("POST","../order/sweater/requires/pre_cost_entry_controller.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_fabric_report_reponse;
				}
				else if(entry_form==158)
				{
					var data="action="+type+
					'&zero_value='+zero_val+
					'&txt_job_no='+"'"+job_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&cbo_buyer_name='+"'"+buyer_id+"'"+
					'&txt_style_ref='+"'"+style_ref+"'"+
					'&print_option_id='+'1,2,3,4,5,6,7,8'+
					'&txt_po_breack_down_id='+''+
					'&txt_costing_date='+"'"+txt_costing_date+"'";//order\sweater
					http.open("POST","../order/sweater/requires/pre_cost_entry_controller_v2.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_fabric_report_reponse;
				}
				else
				{
					var data="action="+type+
					'&zero_value='+zero_val+
					'&txt_job_no='+"'"+job_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&cbo_buyer_name='+"'"+buyer_id+"'"+
					//'&txt_style_ref='+"'"+style_ref+"'"+
					'&txt_costing_date='+"'"+txt_costing_date+"'"+get_submitted_data_string('txt_style_ref',"../../");
					http.open("POST","../order/sweater/requires/pre_cost_entry_controller_v2.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_fabric_report_reponse;
				}
			}
			else if(garments_nature==3)
			{
				//alert(garments_nature+'==='+entry_form);
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
					http.open("POST","../order/woven_gmts/requires/pre_cost_entry_controller.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_fabric_report_reponse;
				}
				else if(entry_form==158)
				{
					//type='preCostRpt2';
					var path="../";
					var data="action="+type+
					'&zero_value='+zero_val+
					'&txt_job_no='+"'"+job_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&cbo_buyer_name='+"'"+buyer_id+"'"+
					'&txt_style_ref='+"'"+style_ref+"'"+
					'&print_option_id='+'1,2,3,4,5,6,7,8'+
					'&txt_po_breack_down_id='+''+
					'&txt_costing_date='+"'"+txt_costing_date+"'"+
					'&path='+"'"+path+"'";//order\sweater
					http.open("POST","../order/woven_gmts/requires/pre_cost_entry_controller_v2.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_fabric_report_reponse;
				}
				else
				{
					var data="action="+type+
					'&zero_value='+zero_val+
					'&txt_job_no='+"'"+job_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&cbo_buyer_name='+"'"+buyer_id+"'"+
					//'&txt_style_ref='+"'"+style_ref+"'"+
					'&path='+"'"+path+"'";
					'&txt_costing_date='+"'"+txt_costing_date+"'"+get_submitted_data_string('txt_style_ref',"../../");
					http.open("POST","../order/woven_gmts/requires/pre_cost_entry_controller_v2.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_fabric_report_reponse;
				}
			}
			else
			{
				var path="../";
				//alert(entry_form);
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
					'&txt_costing_date='+"'"+txt_costing_date+"'"+
					'&path='+"'"+path+"'";
					 
					http.open("POST","../order/woven_order/requires/pre_cost_entry_controller.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_fabric_report_reponse;
				}
				else if(entry_form==158)
				{
					if(type=='budget5'){

						var data="action=report_generate"+
						'&sign=0'+
						'&report_title=Budget/Cost Sheet'+
						'&comments_head=1'+
						'&reporttype=8'+
						'&txt_style_ref_id='+quotation_id+
						'&cbo_company_name='+"'"+company_id+"'"+
						'&cbo_buyer_name='+"'"+buyer_id+"'"+
						'&txt_style_ref='+"'"+style_ref+"'"+
						'&img_show='+img_show+
						'&txt_po_breack_down_id='+''+
						'&txt_costing_date='+"'"+txt_costing_date+"'"+//order\sweater
						'&path='+"'"+path+"'";
						//alert(data);
						http.open("POST","../reports/management_report/merchandising_report/requires/cost_breakup_report2_controller.php",true);
						http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
						http.send(data);
						http.onreadystatechange = generate_fabric_report_reponse;
					}
					else{
						var data="action="+type+
						'&zero_value='+zero_val+
						'&txt_job_no='+"'"+job_no+"'"+
						'&cbo_company_name='+"'"+company_id+"'"+
						'&cbo_buyer_name='+"'"+buyer_id+"'"+
						'&txt_style_ref='+"'"+style_ref+"'"+
						'&txt_style_ref='+"'"+style_ref+"'"+
						'&img_show='+img_show+
						'&txt_po_breack_down_id='+''+
						'&txt_costing_date='+"'"+txt_costing_date+"'"+//order\sweater
						'&path='+"'"+path+"'";
						//alert(data);
						http.open("POST","../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
						http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
						http.send(data);
						http.onreadystatechange = generate_fabric_report_reponse;
					}
					
					
				}
				else
				{
					var data="action="+order/woven_order/requires/pre_cost_entry_report_controller_v2.php+
					'&zero_value='+zero_val+
					'&txt_job_no='+"'"+job_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&cbo_buyer_name='+"'"+buyer_id+"'"+
					//'&txt_style_ref='+"'"+style_ref+"'"+
					'&path='+"'"+path+"'"+
					'&txt_costing_date='+"'"+txt_costing_date+"'"+get_submitted_data_string('txt_style_ref',"../../");
					//alert(data)
					http.open("POST","../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_fabric_report_reponse;
				}
			}
			
		}
	}
		
	function generate_fabric_report_reponse()
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

	function history_print_job(job_no,cbo_company_name,buyer_name,style_ref_no,costing_date,entry_from,quotation_id,revised_no)
	{
		var data="action=preCostRpt2&txt_job_no="+job_no+"&revised_no="+revised_no+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+buyer_name+"&txt_style_ref="+style_ref_no+"&txt_costing_date="+costing_date;
	//	alert(data)
		http.open("POST","requires/pre_costing_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;
	}
	
	function history_budget_sheet(job_no,cbo_company_name,buyer_name,style_ref_no,costing_date,entry_from,quotation_id,revised_no)
	{
		var rate_amt=2; var zero_val='';
		
		var excess_per_val="";
		
		var r=confirm("Press  \"OK\" to Show Budget, \nPress  \"Cancel\"  to Show Management Budget");

		if (r==true) zero_val="1"; else zero_val="0";
		var data="action=budgetsheet&txt_job_no="+job_no+"&revised_no="+revised_no+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+buyer_name+"&txt_style_ref="+style_ref_no+"&txt_costing_date="+costing_date+"&zero_value="+zero_val+"&rate_amt="+rate_amt+"&excess_per_val="+excess_per_val;
	//	alert(data)
		http.open("POST","requires/pre_costing_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;
	}
		
	function history_print_job_reponse()
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
	
	function openImgFile(id,action)
	{
		var page_link='requires/pre_costing_approval_controller.php?action='+action+'&id='+id;
		if(action=='img') var title='Image View'; else var title='File View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
	}

	//barcode scan
	$('#txt_bar_code').live('keydown', function(e) {
		if  (e.keyCode === 13) 
		{
			//alert("su..re"); return;
			e.preventDefault();
			var bar_code=$('#txt_bar_code').val();
			check_on_scan(bar_code);
		}
	});
	
	function fnc_load_cm_compulsory(data)
	{
		$('#txt_cm_compulsory').val('');
		var cm_compulsory = return_global_ajax_value( data, 'populate_cm_compulsory', '', 'requires/pre_costing_approval_controller');
		$('#txt_cm_compulsory').val(cm_compulsory);
		get_php_form_data(data,'report_formate_setting','requires/pre_costing_approval_controller');
	}
	
	function change_user()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var title = 'Alter User Info';	
		var page_link = 'requires/pre_costing_approval_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"
			
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			$("#cbo_approval_type").val(2);
			//load_drop_down( 'requires/pre_costing_approval_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
			load_drop_down( 'requires/pre_costing_approval_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
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
			$("#cbo_approval_type").val(2);
			$("#cbo_approval_type").attr("disabled",false);
		}		
	}
	
	function generat_print_report(type,company_name,buyer_name,date_from,date_to,job_no,job_id,order_id,order_no,year,order_status,search_date,season,season_id,file_no,internal_ref)
	{
		var data="action=report_generate"+
			'&reporttype='+type+
			'&cbo_company_name='+"'"+company_name+"'"+
			'&cbo_buyer_name='+"'"+buyer_name+"'"+
			'&txt_date_from='+"'"+date_from+"'"+
			'&txt_date_to='+"'"+date_to+"'"+
			'&txt_job_no='+"'"+job_no+"'"+
			'&txt_job_id='+"'"+job_id+"'"+
			'&txt_order_id='+"'"+order_id+"'"+
			'&txt_order_no='+"'"+order_no+"'"+
			'&cbo_year='+"'"+year+"'"+
			'&cbo_order_status='+"'"+order_status+"'"+
			'&cbo_search_date='+"'"+search_date+"'"+
			'&txt_season='+"'"+season+"'"+
			'&txt_season_id='+"'"+season_id+"'"+
			'&txt_file_no='+"'"+file_no+"'"+
			'&txt_internal_ref='+"'"+internal_ref+"'";
					
		freeze_window(3);
		/*if(type==1 || type==2 || type==3 || type==4 || type==7)
		{
			http.open("POST","../reports/management_report/merchandising_report/requires/order_wise_budget_report_controller.php",true);
		}
		else if (type==5 || type==6)
		{
			http.open("POST","../reports/management_report/merchandising_report/requires/order_wise_budget_report2_controller.php",true);
		}*/ //Pre_cost old class 3
		if(type==1 || type==2 || type==3 || type==4 || type==7)////Pre_cost-v2 class 4
		{
			http.open("POST","../reports/management_report/merchandising_report/requires/order_wise_budget_report_controller2.php",true);
		}
		else if (type==5 || type==6)
		{
			http.open("POST","../reports/management_report/merchandising_report/requires/order_wise_budget_report2_controller2.php",true);
		}
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generat_print_report_reponse;
	}
	
	function generat_print_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			$('#report_container2').html(reponse[0]); 	
			
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'<link rel="stylesheet" href="../css/style_common.css" type="text/css" /></body</html>');//
			d.close();
			
			$('#report_container2').html(''); 	
			
			release_freezing();
			show_msg('3');
		}
	}
	
	function openmypage_refusing_cause(page_link,title,quo_id)
	{
		var cause=document.getElementById("txtCause_"+quo_id).value;
		
		var page_link = page_link + "&quo_id="+quo_id + "&cause="+cause;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=280px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var cause=this.contentDoc.getElementById("txt_refusing_cause").value;
			document.getElementById("txtCause_"+quo_id).value=cause;
			/*if (cause!="")
			{
				fn_report_generated();
			}*/
		}
	 }
	
	 function print_report_button_setting(report_ids)
	{
		$("#show").hide();
		$("#show2").hide();
		
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==108) $("#show").show();
			if(report_id[k]==195) $("#show2").show();
		}
	}
	
	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{

		}
	}
	
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1">

         <h3 style="width:1200px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:1200px;">
             	<input type="hidden" name="txt_style_ref" id="txt_style_ref">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr> 
                            	<th colspan="7">Barcode Scan: <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes" /></th>
                                <th colspan="2" align="center">
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
                                <th colspan="2">
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
								?>
                                        Alter User:
                                        <input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user()"/ placeholder="Browse " readonly>
                                <?php 
									}
								?>
                                	<input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                                 	<input type="hidden" id="txt_selected_id" name="txt_selected_id" /> 
                                </th>
                            </tr>
                            <tr>
                                <th width="150">Company Name</th>
                                <th width="150">Working Company</th>
                                <th width="150">Buyer</th>
                                <th width="70">Job Year</th>
                                <th width="75">Job No.</th>
                                <th width="75">Internal Ref.</th>
                   				<th width="75">File No</th>
                                <th width="130">Get Upto</th>
                                <th width="70">Costing  Date</th>
                                <th width="80">Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:70px" /> <input style="width:50px;" type="hidden" name="txt_cm_compulsory" id="txt_cm_compulsory"/></th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><?=create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business=1 $company_cond order by company_name","id,company_name", 1, "-- All --", $selected, "load_drop_down( 'requires/pre_costing_approval_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );fnc_load_cm_compulsory(this.value);" ); ?></td>
                                <td><?=create_drop_down( "cbo_working_company", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business=1  order by company_name","id,company_name", 1, "-- All --", $selected, "" ); ?></td>
                                <td id="buyer_td_id"><?=create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", 0, "" ); ?></td>
                                <td><?=create_drop_down( "cbo_year", 70, create_year_array(),"", 1, "-- Select --", date("Y",time()), "" ); ?></td>
                                <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:65px"></td>
                                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:65px"></td>
                      			<td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:65px"></td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                       echo create_drop_down( "cbo_get_upto", 100, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_date" id="txt_date" class="datepicker" readonly style="width:60px"/></td>
                                <td> 
                                    <?
									  $pre_cost_approval_type=array(2=>"Un-Approved",1=>"Approved");
                                        echo create_drop_down( "cbo_approval_type", 80, $pre_cost_approval_type,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td>
                                	<input type="button"  value="Show" name="show" id="show" class="formbutton" style="width:70px;float: left;display: none;" onClick="fn_report_generated(1);"/>
                                	<input type="button" value="Show2" name="show2" id="show2" class="formbutton" style="width:70px;float: left;display: none;" onClick="fn_report_generated(2);"  />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
    
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_approval_type').val(0);
$('#cbo_working_company').val(0);
</script>
</html>
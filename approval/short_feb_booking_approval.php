<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Fabric Booking Approval
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	22-12-2013
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
echo load_html_head_contents("Fabric Booking Approval", "../", 1, 1,'','','');
$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );
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

		if(type==1)
		{
			var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_internal_ref*txt_file_no*cbo_get_upto*txt_date*cbo_approval_type*txt_booking_no*txt_alter_user_id',"../");
		}
		else if(type==2)
		{
			var data="action=report_generate_2&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_internal_ref*txt_file_no*cbo_get_upto*txt_date*cbo_approval_type*txt_booking_no*txt_alter_user_id',"../");
		}

		http.open("POST","requires/short_feb_booking_approval_controller.php",true);
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
	
	function check_all(tot_check_box_id)
	{
		var approval_type=$("#cbo_approval_type").val();
		var i=1;
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$("#tbl_list_search tbody").find('tr').each(function()
			{
				if(i>1)
				{
					var tr_row=$(this).attr('id').split('_');
					var rowNo=tr_row[1];
					var dealing_merchant=$(this).find("td:eq(8)").text();
					var booking_no=$('#booking_no_'+rowNo).val();
					
					var last_update=return_global_ajax_value( trim(booking_no), 'check_booking_last_update', '', 'requires/short_feb_booking_approval_controller');
	
					if(last_update==2)
					{
						alert("Booking ("+trim(booking_no)+") Info not synchronized with order entry and pre-costing. Contact To "+dealing_merchant+".");
						$(this).find('input[name="tbl[]"]').attr('checked', false);
					}
					else
					{
						$(this).find('input[name="tbl[]"]').attr('checked', true);
						//$('#tbl_list_search tbody tr input:checkbox').attr('checked', true);
					} 
					if(approval_type==1)
					{
						var salse_order_approved=return_global_ajax_value( trim(booking_no), 'check_sales_order_approved', '', 'requires/short_feb_booking_approval_controller');
						if(salse_order_approved==1 || salse_order_approved==3)
						{
							alert("Corresponding Sales Order is Approved.So Booking Unapproved Not Allow.");
							$(this).find('input[name="tbl[]"]').attr('checked', false);
						}
						else 
						{
							$(this).find('input[name="tbl[]"]').attr('checked', true);
						}	
						
					}
				}
				i++;
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
		var dealing_merchant=$('#dealing_merchant_'+row_no).html();
		var tbl_len=$("#tbl_list_search tbody tr").length;
		
		var last_update=return_global_ajax_value( trim(scan_no), 'check_booking_last_update', '', 'requires/fabric_booking_approval_controller');
		if(last_update==2)
		{
			alert("Booking ("+trim(scan_no)+") Info not synchronized with order entry and pre-costing. Contact To "+dealing_merchant+".");
			$('#tbl_'+row_no).attr('checked', false);
		}
		else
		{
			$('#tbl_'+row_no).attr('checked', true);
		}
		var approval_type=$("#cbo_approval_type").val();
		if(approval_type==1)
		{
			var salse_order_approved=return_global_ajax_value( trim(scan_no), 'check_sales_order_approved', '', 'requires/short_feb_booking_approval_controller');
			if(salse_order_approved==1 || salse_order_approved==3)
			{
				alert("Corresponding Sales Order is Approved.So Booking Unapproved Not Allow.");
				$('#tbl_'+row_no).attr('checked', false);
			}
			else 
			{
				$('#tbl_'+row_no).attr('checked', true);
			}
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
		//var last_update=$('#last_update_'+rowNo).val();
		var booking_no=$('#booking_no_'+rowNo).val();
		
		if(isChecked==true)
		{
			var last_update=return_global_ajax_value( trim(booking_no), 'check_booking_last_update', '', 'requires/short_feb_booking_approval_controller');
			if(last_update==2)
			{
				alert("Booking ("+trim(booking_no)+") Info not synchronized with order entry and pre-costing. Contact To "+dealing_merchant+".");
				$('#tbl_'+rowNo).attr('checked',false);
			}
			var approval_type=$("#cbo_approval_type").val();
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
					$('#tbl_'+rowNo).attr('checked', true);
				}
			}
			//return;
		}
	}
		
	function submit_approved(total_tr,type)
	{ 
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
		
		//Cause validation start.....................................
		if(type == 5){
			var app_caue_field_arr=Array();
			var app_caue_field_msg_arr=Array();
			for(i=1; i<total_tr; i++)
			{
				if ($('#tbl_'+i).is(":checked"))
				{
					app_caue_field_arr.push('txt_appv_cause_'+i);
					app_caue_field_msg_arr.push('Approval Cause');
				}

			}

			if(app_caue_field_arr.length >0 ){
				if (form_validation(app_caue_field_arr.join('*'),app_caue_field_msg_arr.join('*'))==false)
				{
					release_freezing();
					return;
				}
			}

		}
		//Cause validation end.....................................
		
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

		if(type == 0 || type == 5){
			$('#txt_selected_id').val(booking_ids);
			fnSendMail('../','',1,0,0,1,type);
		}


		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+'&appv_instras='+appv_instras+get_submitted_data_string('cbo_company_name*txt_alter_user_id',"../");
	
		
		
		http.open("POST","requires/short_feb_booking_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}	
	
	function fnc_purchase_requisition_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{ 
		
			//release_freezing();	
			//alert(http.responseText);return;
		
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]=="seq")
			{
				alert("Sequence Not Found.");
				release_freezing();
				return;
			}	
			show_msg(trim(reponse[0]));
			
			if((reponse[0]==19 || reponse[0]==20 || reponse[0]==50))
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
	
	function generate_worder_report(type,report_type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,action,i)
	{
		
		var report_title='Short Fabric Booking';
		var data="action="+action+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&txt_order_no_id='+"'"+order_id+"'"+
					'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
					'&cbo_fabric_source='+"'"+fabric_source+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&report_title='+"'"+report_title+"'"+
					'&report_type='+"'"+report_type+"'"+
					'&txt_job_no='+"'"+job_no+"'";

				
		if(type==1)	
		{			
			http.open("POST","../order/woven_order/requires/short_fabric_booking_controller.php",true);
		}
		else if(type==2)
		{
			http.open("POST","../order/woven_order/requires/fabric_booking_controller.php",true);
		}
		else
		{
			http.open("POST","../order/woven_order/requires/sample_booking_controller.php",true);
		}
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
			
			
			
		   }
			
		}
	}
	function generate_worder_report_history(type,report_type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,action,i,revised_no)
	{
		
		var report_title='Short Fabric Booking';
		var data="action="+action+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&txt_order_no_id='+"'"+order_id+"'"+
					'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
					'&cbo_fabric_source='+"'"+fabric_source+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&report_title='+"'"+report_title+"'"+
					'&report_type='+"'"+report_type+"'"+
					'&txt_job_no='+"'"+job_no+"'"+
					'&revised_no='+"'"+revised_no+"'";

				
		http.open("POST","requires/short_feb_booking_approval_controller.php",true);
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
			
			
			
		   }
			
		}
	}
	function generate_worder_report2(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,action)
	{
		
		var data="action="+action+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&txt_order_no_id='+"'"+order_id+"'"+
					'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
					'&cbo_fabric_source='+"'"+fabric_source+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&txt_job_no='+"'"+job_no+"'";
					
		if(type==1)	
		{			
			http.open("POST","../order/woven_order/requires/short_fabric_booking_controller.php",true);
		}
		else if(type==2)
		{
			http.open("POST","../order/woven_order/requires/fabric_booking_controller.php",true);
		}
		else
		{
			http.open("POST","../order/woven_order/requires/sample_booking_controller.php",true);
		}
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
			
			
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
	
	function openImgFile(job_no,action)
	{
		var page_link='requires/short_feb_booking_approval_controller.php?action='+action+'&job_no='+job_no;
		if(action=='img') var title='Image View'; else var title='File View';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
	}

	//barcode scan
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
		var title = 'Approval user list';	
		var page_link = 'requires/short_feb_booking_approval_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"
			
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			load_drop_down( 'requires/short_feb_booking_approval_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
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
	function generate_mkt_report(job_no,booking_no,order_id,fab_nature,fab_source,action)
	{
		//alert(action);return;
		var page_link='requires/fabric_booking_approval_controller.php?action='+action+'&job_no='+job_no+'&booking_no='+booking_no+'&order_id='+order_id+'&fab_nature='+fab_nature+'&fab_source='+fab_source;
		var title='Comments View';
		//alert(page_link);return;
		//if(action=='img') var title='Image View'; else var title='File View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=1,scrolling=0','');
	}

	function generate_worder_report_pre_cost(type,job_no,company_id,buyer_id,style_ref,txt_costing_date,entry_form,quotation_id,garments_nature)
	{   
          
			// alert(type+"**"+job_no+"**"+company_id+"**"+buyer_id+"**"+style_ref+"**"+txt_costing_date+"**"+entry_form+"**"+quotation_id+"**"+garments_nature);

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
					http.open("POST","..order/woven_order/requires/pre_cost_entry_report_controller_v2.php",true);
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
					http.open("POST","../order/woven_order/requires/pre_cost_entry_report_controller_v2.php",true);
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
					'&txt_costing_date='+"'"+txt_costing_date+"'"+get_submitted_data_string('txt_style_ref',"../../");
					http.open("POST","../order/woven_gmts/requires/pre_cost_entry_controller_v2.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_fabric_report_reponse;
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
					http.open("POST","../order/woven_order/requires/pre_cost_entry_report_controller_v2.php",true);
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
					'&txt_style_ref='+"'"+style_ref+"'"+
					'&print_option_id='+'1,2,3,4,5,6,7,8'+
					'&txt_po_breack_down_id='+''+
					'&txt_costing_date='+"'"+txt_costing_date+"'";
					//alert(data)
					if(type == 'budgetsheet3')
					{
						http.open("POST","../order/woven_order/requires/pre_cost_entry_report_controller_v2.php",true);
					}else{
					http.open("POST","../order/woven_order/requires/pre_cost_entry_report_controller_v2.php",true);
					}
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_fabric_report_reponse;
				}
			}
			
		}
	}

	function generate_worder_report_main(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,print_id,entry_form,type,i,fabric_nature)
	{  
		
		if(print_id==85 || print_id==53 || print_id==143){
			var show_yarn_rate='';
			var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
			
			if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";
		}
		var report_title="";
	
		if(print_id==143 || print_id==160 || print_id==274 || print_id==155) report_title='Partial Fabric Booking'; else report_title='Main Fabric Booking';
		
		var data="action="+type+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+report_title+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		'&show_yarn_rate='+show_yarn_rate+
		'&path=../';
			
		freeze_window(5);
		//alert(entry_form);
		if(fabric_nature == 3){

			if(entry_form==118 ) //(print_id==45 || print_id==53 || print_id==93 || print_id==73 || || print_id==2)
			{
				http.open("POST","../order/woven_gmts/requires/fabric_booking_urmi_controller.php",true);
			}
			else if( entry_form==108) //&& (print_id==85 || print_id==143 || print_id==160)
			{
				http.open("POST","../order/woven_gmts/requires/partial_fabric_booking_controller.php",true);
			}
			else if( entry_form==271) //&& (print_id==85 || print_id==143 || print_id==160)
			{
			
				http.open("POST","../order/woven_gmts/requires/woven_partial_fabric_booking_controller.php",true);
			}			
			else if(entry_form==86)
			{
				http.open("POST","../order/woven_order/requires/fabric_booking_controller.php",true);
			}
		}
		else{
		
		//	alert(entry_form);
			if(entry_form==118 ) //print_id==45 || print_id==53 || print_id==93 || print_id==73
			{
				http.open("POST","../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
			}
			else if(entry_form==108 ) //print_id==85 || print_id==143
			{
				http.open("POST","../order/woven_order/requires/partial_fabric_booking_controller.php",true);
			}
			else if(entry_form==271) //print_id==85 || print_id==143
			{
				http.open("POST","../order/woven_gmts/requires/woven_partial_fabric_booking_controller.php",true);
			}			
			else if(entry_form==86)
			{
				http.open("POST","../order/woven_order/requires/fabric_booking_controller.php",true);
			}
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
				release_freezing();
		   }
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


	function call_print_button_for_mail(mail,mail_body,type){
		 
		 var booking_id=$('#txt_selected_id').val();
		 var txt_alter_user_id=$('#txt_alter_user_id').val();
		 var cbo_company_name=$('#cbo_company_name').val();
		 var sysIdArr=booking_id.split(',');
		 
		 var mail=mail.split(',');
		 var ret_data=return_global_ajax_value(sysIdArr.join(',')+'__'+mail.join(',')+'__'+txt_alter_user_id+'__'+cbo_company_name+'__'+type, 'app_mail_notification', '', 'requires/short_feb_booking_approval_controller');
		 //alert(ret_data);
	 }


</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:1103px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:1100px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr> 
                            	<th colspan="3">Barcode Scan: <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes"  /></th>
                            	<th colspan="3" align="center">
                                
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
								?>
                                		Previous Approved: <input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes"  value="0"  onChange="change_approval_type(this.value)"/>
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
									if( $user_lavel==2)
									{
								?> 
                                        Alter User:
                                        <input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user()"/ placeholder="Browse " readonly>
                                <?php 
									}
									
								?>
                                
                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                                </th>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
                                <th>Buyer</th>
                                <th width="70">Internal Ref.</th>
                                <th width="70">File No</th>
                                 <th>Booking No</th>
                                <th>Get Upto</th>
                                <th>Booking Date</th>
                                <th>Approval Type</th>
                                <th style="width: 200px;"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/short_feb_booking_approval_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );get_php_form_data(this.value,'report_formate_setting','requires/short_feb_booking_approval_controller')" );
                                    ?>
                                </td>
                                <td id="buyer_td_id"> 
									<?
                                       echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
                                    ?>
                                </td>
                                
                                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:65px"></td>
                      			<td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:65px"></td>

                                 <td><input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:65px"></td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                       echo create_drop_down( "cbo_get_upto", 130, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
                               
                                <td><input type="text" name="txt_date" id="txt_date" class="datepicker" readonly style="width:80px"/></td>
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_approval_type", 130, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td>
                                	<input type="button" value="Show" name="show" id="show" class="formbutton" style="width:90px; display:none;" onClick="fn_report_generated(1);" />
                                	<input type="button" value="Show2" name="show2" id="show2" class="formbutton" style="width:90px; display:none;" onClick="fn_report_generated(2);" />
									<input type="hidden" id="txt_selected_id">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_approval_type').val(0);
</script>
</html>
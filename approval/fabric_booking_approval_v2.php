<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Fabric Booking Approval
Functionality	:	
JS Functions	:
Created by		:	Shajib Jaman 
Creation date 	: 	20-05-2023
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

$approval_setup=is_duplicate_field( "entry_form", "electronic_approval_setup", "entry_form=7 and is_deleted=0" );
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
			var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_internal_ref*txt_file_no*cbo_get_upto*txt_date*cbo_approval_type*txt_booking_no*cbo_booking_year*txt_alter_user_id*cbo_brand_id*cbo_season_id*cbo_season_year*cbo_style_owner_id',"../");
		}
		else if(type==2)
		{   
			var data="action=generate_show_2&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_internal_ref*txt_file_no*cbo_get_upto*txt_date*cbo_approval_type*txt_booking_no*cbo_booking_year*txt_alter_user_id*cbo_brand_id*cbo_season_id*cbo_season_year*cbo_style_owner_id',"../");
		}

		
		http.open("POST","requires/fabric_booking_approval_controller_v2.php",true);
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

	function openmypage_refusing_cause(page_link,title,quo_id)
	{
		var cause=$("#comments_"+quo_id).text();
		var txt_alter_user_id=$("#txt_alter_user_id").val();
		var page_link = page_link + "&quo_id="+quo_id + "&cause="+cause + "&txt_alter_user_id="+txt_alter_user_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=280px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var cause=this.contentDoc.getElementById("txt_refusing_cause").value;
			// alert(cause+"="+quo_id);
			// document.getElementById("comments_"+quo_id).innerHTML=cause;
			$('#comments_'+quo_id).val(cause);
		}
	 }
	
	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			var i
			$("#tbl_list_search tbody").find('tr').each(function()
			{
				try 
				{
					var dealing_merchant=$(this).find("td:eq(8)").text();
					var booking_no=$(this).find('input[name="booking_no[]"]').val();
					var hide_approval_type=parseInt($('#hide_approval_type').val());

					if( hide_approval_type==1)
					{
						var salse_order_approved=return_global_ajax_value( trim(booking_no), 'check_sales_order_approved', '', 'requires/fabric_booking_approval_controller_v2');
						if(salse_order_approved==1 || salse_order_approved==3)
						{
							alert("Corresponding Sales Order is approved.So Booking Unapproved Not Allow.");
							$(this).find('input[name="tbl[]"]').attr('checked', false);
						}
						else if(salse_order_approved==4)
						{
							alert("PI Found.So Booking Unapproved Not Allow.");
							$(this).find('input[name="tbl[]"]').attr('checked', false);
						}
						else 
						{
							$(this).find('input[name="tbl[]"]').attr('checked', true);
						}
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
	
		
	function submit_approved(total_tr,type)
	{ 

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
		
		//if(type==5 || type==1){
			
			$('#txt_selected_id').val(booking_ids+'***'+type);
			fnSendMail('../','',1,0,0,1);
		//}
		
		
		
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+'&appv_instras='+appv_instras+get_submitted_data_string('cbo_company_name*txt_alter_user_id',"../");
		
		
		http.open("POST","requires/fabric_booking_approval_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
		fnc_remove_tr();
	}	
	
    function fnc_purchase_requisition_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{
			 //alert(http.responseText);
			var reponse=trim(http.responseText).split('**');	
			
			show_msg(reponse[0]);
			
			if((reponse[0]==19 || reponse[0]==20 || reponse[0]==50))
			{
				fnc_remove_tr();
			}
			else if (reponse[0]==25) 
			{
				alert(reponse[1]);
			}
			else if(reponse[0] == 21){alert(reponse[0]);}
			
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
	
	function generate_worder_report(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,print_id,entry_form,type,i,fabric_nature)
	{ 
		
		
		var show_yarn_rate='';
		if(print_id==85 || print_id==53 || print_id==143){
			var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
			if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";
		}
		if(print_id==426 && type=='show_fabric_booking_report_print23')
		{
			var r=confirm("Press  \"Cancel\"  to hide  Yarn Required Summary\nPress  \"OK\"  to Show Yarn Required Summary");
			if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";
		}
		var report_title="";
	
		if(print_id==143 || print_id==160 || print_id==274 || print_id==155 || print_id==28 || print_id==723){ report_title='Partial Fabric Booking';} else{ report_title='Main Fabric Booking';}
		if(entry_form==271){ report_title='Woven Partial Fabric Booking-Purchase';}
	
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
		
		if(fabric_nature == 3){

			if(entry_form==118 ) //(print_id==45 || print_id==53 || print_id==93 || print_id==73 || || print_id==2)
			{
				http.open("POST","../order/woven_gmts/requires/fabric_booking_urmi_controller.php",true);
			}
			else if( entry_form==108) //&& (print_id==85 || print_id==143 || print_id==160)
			{
				http.open("POST","../order/woven_order/requires/partial_fabric_booking_controller.php",true);
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

	function generate_worder_report11(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,print_id,entry_form,type,i,fabric_nature)
	{ 
		var data="action="+type+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+" Main Fabric Booking "+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		//'&show_yarn_rate='+show_yarn_rate+
		'&path=../';
			
		freeze_window(5);
		http.open("POST","../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
		
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
	
	function generate_worder_report2(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,action)
	{
		var data="action="+action+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&txt_order_no_id='+"'"+order_id+"'"+
					'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
					'&cbo_fabric_source='+"'"+fabric_source+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&txt_job_no='+"'"+job_no+"'"+
					'&path=../';
					
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
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
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

	function generate_qc_report(qc_no,cost_sheet_no,action)
	{
		var report_title="Short Quotation";
		generate_report_file( qc_no+'*'+cost_sheet_no+'*'+report_title, action,'../order/spot_costing/requires/quick_costing_v2_controller.php');
		
		/*var data="action="+action+
					'&hid_qc_no='+"'"+qc_no+"'"+
					'&txt_costSheetNo='+"'"+cost_sheet_no+"'"+
					'&report_title='+report_title+
					'&path=../';
					
		http.open("POST","../order/spot_costing/requires/quick_costing_v2_controller.php",true);
		
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
		}*/
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("../order/spot_costing/requires/quick_costing_v2_controller.php?data=" + data+'&action='+action, true );
	}

	function generate_fabric_report_history(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,revised_no,txt_job_no) {
            var path = '../';
            print_report(txt_booking_no + '**'+cbo_company_name+ '**'+txt_order_no_id+ '**'+cbo_fabric_natu+ '**'+cbo_fabric_source+ '**'+revised_no+ '**'+txt_job_no+'**' + path , "show_fabric_booking_report_libas", "requires/fabric_booking_approval_controller_v2");
    }
	
	function generate_worder_report4(job_no)
	{ 
		var job_data=return_global_ajax_value( trim(job_no), 'pre_cost_data', '', 'requires/fabric_booking_approval_controller_v2');
		var job_data_arr=job_data.split('***');
	//a.job_no, a.company_name, a.buyer_name, a.style_ref_no,b.costing_date,b.costing_per

		if(job_data_arr[6]==425){
			var format_ids=return_global_ajax_value( job_data_arr[1]+'**122', 'get_pre_cost_print_button', '', 'requires/fabric_booking_approval_controller_v2');
			if(format_ids==50){action ='bom_epm_woven';}
			else if(format_ids==51){action ='preCostRpt2';}
			else if(format_ids==158){action ='preCostRptWoven';}
			else if(format_ids==159){action ='bomRptWoven';}
			else if(format_ids==170){action ='preCostRpt3';}
			else if(format_ids==192){action ='checkListRpt';}
			else if(format_ids==307){action ='basic_cost';}
			else if(format_ids==313){action ='mkt_source_cost';}
			else if(format_ids==381){action ='mo_sheet_2';}
			else if(format_ids==260){action ='bomRptWoven_2';}
			else if(format_ids==761){action ='bom_pcs_woven';}
			else if(format_ids==403){action ='mo_sheet_3';}
			else if(format_ids==770){action='bom_pcs_woven2';}
			else if(format_ids==473){action='slgCostRpt';}
			//else if(format_ids==120){action ='budgetsheet3';}
		}
		else if(job_data_arr[6]==520){
			var format_ids=return_global_ajax_value( job_data_arr[1]+'**161', 'get_pre_cost_print_button', '', 'requires/fabric_booking_approval_controller_v2');
			if(format_ids==50){action ='preCostRpt';}
			if(format_ids==51){action ='preCostRpt2';}
			if(format_ids==52){action ='bomRpt';}
			if(format_ids==63){action ='bomRpt2';}
			if(format_ids==156){action ='accessories_details';}
			if(format_ids==157){action ='accessories_details2';}
			if(format_ids==158){action ='preCostRptWoven';}
			if(format_ids==159){action ='bomRptWoven';}
			if(format_ids==170){action ='preCostRpt3';}
			if(format_ids==171){action ='preCostRpt4';}
			if(format_ids==142){action ='preCostRptBpkW';}
			if(format_ids==192){action ='checkListRpt';}
			if(format_ids==197){action ='bomRpt3';}
			if(format_ids==211){action ='mo_sheet';}
			if(format_ids==221){action ='fabric_cost_detail';}
			if(format_ids==173){action ='preCostRpt5';}
			if(format_ids==238){action ='summary';}
			if(format_ids==215){action ='budget3_details';}
			if(format_ids==270){action ='preCostRpt6';}
			if(format_ids==581){action ='costsheet';}
			if(format_ids==730){action ='budgetsheet';}
			if(format_ids==759){action =' materialSheet';}
			if(format_ids==351){action ='bomRpt4';}
			if(format_ids==268){action ='budget_4';}
			if(format_ids==381){action ='mo_sheet_2';}
			if(format_ids==405){action ='materialSheet2';}
			if(format_ids==765){action ='bomRpt5';}
			if(format_ids==403){action ='mo_sheet_3';}
			//if(format_ids==120){action ='budgetsheet3';}
		}
		else if(job_data_arr[6]==158){
			var format_ids=return_global_ajax_value( job_data_arr[1]+'**43', 'get_pre_cost_print_button', '', 'requires/fabric_booking_approval_controller_v2');
			
			if(format_ids==50){action ='preCostRpt';}
			else if(format_ids==51){action ='preCostRpt2';} 
			else if(format_ids==52){action ='bomRpt';}  
			else if(format_ids==63){action ='bomRpt2';}  
			else if(format_ids==156){action ='accessories_details';}  
			else if(format_ids==157){action ='accessories_details2';} 
			else if(format_ids==158){action ='preCostRptWoven';} 
			else if(format_ids==159){action ='bomRptWoven';}     
			else if(format_ids==170){action ='preCostRpt3';}    
			else if(format_ids==171){action ='preCostRpt4';}   
			else if(format_ids==142){action ='preCostRptBpkW';}    
			else if(format_ids==192){action ='checkListRpt';}  
			else if(format_ids==197){action ='bomRpt3';}  
			else if(format_ids==211){action ='mo_sheet';}    
			else if(format_ids==221){action ='fabric_cost_detail';}    
			else if(format_ids==173){action ='preCostRpt5';}   
			else if(format_ids==238){action ='summary';}   
			else if(format_ids==215){action ='budget3_details';} 
			else if(format_ids==270){action ='preCostRpt6';}  
			else if(format_ids==581){action ='costsheet';} 
			else if(format_ids==730){action ='budgetsheet';}  
			else if(format_ids==351){action ='bomRpt4';} 
			else if(format_ids==381){action ='mo_sheet_1';}   
			else if(format_ids==268){action ='budget_4';}   
			else if(format_ids==403){action ='mo_sheet_3';}    
			else if(format_ids==769){action ='preCostRpt7';}
			else if(format_ids==445){action ='preCostRpt8';}
			else if(format_ids==460){action ='trims_check_list';}
			else if(format_ids==129){action ='budget5';} 
			else if(format_ids==235){action ='preCostRpt9';} 
			else if(format_ids==25){action ='budgetsheet2';} 
			else if(format_ids==120){action ='budgetsheet3';}
		}
		
		else{
			action ='preCostRpt4';
		}




		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true) zero_val="1"; else zero_val="0";
		
		var data="action="+action+"&zero_value="+zero_val+"&txt_job_no='"+trim(job_data_arr[0])+"'&cbo_company_name="+job_data_arr[1]+"&cbo_buyer_name="+job_data_arr[2]+"&txt_style_ref='"+trim(job_data_arr[3])+"'&txt_costing_date='"+job_data_arr[4]+"'&txt_po_breack_down_id=''&cbo_costing_per="+job_data_arr[5];
		
		if(job_data_arr[6]==425){
			http.open("POST","../order/woven_gmts/requires/pre_cost_entry_controller_v2.php",true);
		}
		else if(job_data_arr[6]==158){
			if(action =='budgetsheet'){
				http.open("POST","../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
			}
			else if(action =='budgetsheet3'){
				http.open("POST","../order/woven_order/requires/pre_cost_entry_report_controller_v2.php",true);
			}
			else{
				http.open("POST","../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
			}
			
		}
		else if(job_data_arr[6]==520){
			http.open("POST","../order/woven_order/requires/pre_cost_entry_controller_v3.php",true);
		}
		else{
			http.open("POST","../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
		}

		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_worder_report4_reponse;
	}
	
	function generate_worder_report4_reponse()
	{
		if(http.readyState == 4) 
		{
			//$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
		}
	}
		
	function generate_worder_report3(job_no,action)
	{
		var job_data=return_global_ajax_value( trim(job_no), 'pre_cost_data', '', 'requires/fabric_booking_approval_controller_v2');
		var job_data_arr=job_data.split('***');
	//a.job_no, a.company_name, a.buyer_name, a.style_ref_no,b.costing_date,b.costing_per
	
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true) zero_val="1"; else zero_val="0";
		
		var data="action="+action+"&zero_value="+zero_val+"&txt_job_no='"+trim(job_data_arr[0])+"'&cbo_company_name="+job_data_arr[1]+"&cbo_buyer_name="+job_data_arr[2]+"&txt_style_ref='"+trim(job_data_arr[3])+"'&txt_costing_date='"+job_data_arr[4]+"'&txt_po_breack_down_id=''&cbo_costing_per="+job_data_arr[5]+"&path=../";
		
		http.open("POST","../order/woven_gmts/requires/pre_cost_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_worder_report3_reponse;
	}
	
	function generate_worder_report3_reponse()
	{
		if(http.readyState == 4) 
		{
			//$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
		}
	}

	
	function openImgFile(job_no,action)
	{
		var page_link='requires/fabric_booking_approval_controller_v2.php?action='+action+'&job_no='+job_no;
		if(action=='img') var title='Image View'; else var title='File View';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
	}
	
	function generate_mkt_report(job_no,booking_no,order_id,fab_nature,fab_source,action)
	{
		//alert(action);return;
		var company_name=$('#cbo_company_name').val();
		var page_link='requires/fabric_booking_approval_controller_v2.php?action='+action+'&company_name='+company_name+'&job_no='+job_no+'&booking_no='+booking_no+'&order_id='+order_id+'&fab_nature='+fab_nature+'&job_ids='+fab_source;
		if(action=='show_fabric_approval_report')
		{
			var title='Fabric Approval Dtls View';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=500px,center=1,resize=1,scrolling=0','');
		}
		else
		{
			var title='Comments View';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=370px,center=1,resize=1,scrolling=0','');
		}
		//alert(page_link);return;
		//if(action=='img') var title='Image View'; else var title='File View';
		
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
		var title = 'User Info';	
		var page_link = 'requires/fabric_booking_approval_controller_v2.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"			
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			$("#cbo_approval_type").val(0);
			//load_drop_down( 'requires/pre_costing_approval_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
			load_drop_down( 'requires/fabric_booking_approval_controller_v2',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
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
	
	
	function call_print_button_for_mail(mail){
		 
		var booking_id_str=$('#txt_selected_id').val();
		var txt_alter_user_id=$('#txt_alter_user_id').val();
		var booking_id_arr=booking_id_str.split('***');
		var booking_id=booking_id_arr[0];
		var sysIdArr=booking_id.split(',');
		var mail=mail.split(',');
		var action='send_app_unapp_notification';
		var ret_data=return_global_ajax_value(sysIdArr.join('*')+'__'+mail.join('*')+'__'+txt_alter_user_id, action, '', 'requires/fabric_booking_approval_controller_v2');
		//alert(ret_data);
	}
	
	function openmypage_refusing_cause(page_link,title,quo_id)
	{
			var page_link = page_link + "&quo_id="+quo_id;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=280px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var cause=this.contentDoc.getElementById("txt_refusing_cause").value;
 				if (cause!="")
				{
					fn_report_generated(2);
				}
			}
	 }

	function generate_fabric_report(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,revised_no,id_approved_id,txt_job_no,entry_form)
	{
		var show_yarn_rate='';
		var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
		if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";
		var report_title="Main Fabric Booking";
		var path = '../';
		var action="show_fabric_booking_report_print23";
		if(entry_form==108)
		{
			action="print_booking_10";
			report_title="Partial Fabric Booking";
		}
		print_report(txt_booking_no + '**'+cbo_company_name+ '**'+txt_order_no_id+ '**'+cbo_fabric_natu+ '**'+cbo_fabric_source+ '**'+revised_no+ '**'+txt_job_no+ '**'+show_yarn_rate+ '**'+report_title+'**' + path+'**'+id_approved_id , action, "requires/fabric_booking_approval_controller_v2");
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
	<?=load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:1400px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:1400px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr> 
                            	<th colspan="9">Barcode Scan : <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes"  /></th>
                                 <th colspan="2" align="center">
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
										?>Previous Approved: <input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes"  value="0"  onChange="change_approval_type(this.value)" /> <?php
									}
									else
									{
										?><input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes" style="display:none" /><?php	
									} ?> 
                                 </th>
                                <th colspan="7">
                                <?php  
									if( $user_lavel==2)
									{  
										?>Alter User:<input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user()" placeholder="Browse " readonly>
								<?php } ?>
                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" />
                                <input type="hidden" id="txt_selected_id" name="txt_selected_id" />
                                </th>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
								<th>Style Owner</th>
                                <th>Buyer</th>
								<th>Brand</th>
								<th>Season</th>
								<th>Season Year</th>
                                <th>Master Style/Internal Ref.</th>
                                <th>File No</th>
                                <th>Year</th>
                                <th>Booking No</th>
                                <th>Get Upto</th>
                                <th>Booking Date</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:70px" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><?
                                        echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/fabric_booking_approval_controller_v2',this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
                                    ?></td>

								<td><? echo create_drop_down( "cbo_style_owner_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- All --", $selected, "" ); ?></td>


                                <td id="buyer_td_id"><?=create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- All Buyer --", 0, "" ); ?></td>
								<td id="brand_td"><?= create_drop_down( "cbo_brand_id", 60, $blank_array,'', 1, "--Brand--",$selected );?></td>
								<td id="season_td"><?echo create_drop_down( "cbo_season_id", 70, $blank_array,'', 1, "--Season--",$selected, "" );                     
                        ?></td>
								<td><? echo create_drop_down( "cbo_season_year", 60, create_year_array(),"", 1,"-Year-", 1, "",0,"" );?></td>
                                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:100px"></td>
                      			<td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:65px"></td>
                                <td><?=create_drop_down( "cbo_booking_year",60, $year,"", 1, "-- Year --", 0, "" ); ?></td>
								<td><input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:65px"></td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                       echo create_drop_down( "cbo_get_upto", 80, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_date" id="txt_date" class="datepicker" readonly style="width:80px"/></td>
                                <td><?=create_drop_down( "cbo_approval_type", 90, $approval_type_arr,"", 0, "", $selected,"","", "" ); ?></td>
                                <td align="center">
                                	<input type="button" value="Show" name="show" id="show" class="formbutton" style="width:70px;float: left;" onClick="fn_report_generated(1);" />
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
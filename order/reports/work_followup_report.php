<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Work Followup Report.
Functionality	:	
JS Functions	:
Created by		:	Shariar
Creation date 	: 	01-08-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Work Followup Report", "../../", 1, 1, $unicode, '1', '');
?>

<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	function fn_report_generated() {
		if (form_validation('cbo_company_name', 'Company Name') == false) //*txt_date_from*txt_date_to*From Date*To Date
		{
			return;
		} else {
			var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_style_ref*cbo_search_by*txt_date_from*txt_date_to*cbo_year_selection', "../../");
			//alert(data);
			freeze_window(3);
			http.open("POST", "requires/work_followup_report_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}


	function fn_report_generated_reponse() {
		if (http.readyState == 4) {

			var reponse = trim(http.responseText).split("****");
			var tot_rows = reponse[2];
			var search_by = reponse[3];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(' + tot_rows + ')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			/*if(tot_rows*1>1)
			{
			if(search_by==1)
			    {
			
				 var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["total_order_qnty","total_order_qnty_in_pcs","value_req_qnty","value_pre_costing","value_wo_qty","value_in_qty","value_rec_qty","value_issue_qty","value_leftover_qty"],
					   col: [5,7,14,15,16,17,18,19,20],
					   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}	
				 }
				}
			if(search_by==2)
			    {
			
				 var tableFilters = 
				 {
					 
					col_operation: {
					   id: ["total_order_qnty","value_rec_qty","value_issue_qty","value_leftover_qty"],
					   col: [5,8,9,10],
					   operation: ["sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}	
				 }
				}
				setFilterGrid("table_body",-1);
			}*/
			//setFilterGrid("table_body_style",-1);
			show_msg('3');
			release_freezing();
		}
	}

	function change_color(v_id, e_color) {
		if (document.getElementById(v_id).bgColor == "#33CC00") {
			document.getElementById(v_id).bgColor = e_color;
		} else {
			document.getElementById(v_id).bgColor = "#33CC00";
		}
	}


	function new_window(html_filter_print) {
		document.getElementById('scroll_body').style.overflow = "auto";
		document.getElementById('scroll_body').style.maxHeight = "none";

		if (html_filter_print * 1 > 1) $("#table_body tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
			'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY = "scroll";
		document.getElementById('scroll_body').style.maxHeight = "400px";

		if (html_filter_print * 1 > 1) $("#table_body tr:first").show();
	}


	function generate_report(company, job_no, type) {
		var data = "action=" + type + "&txt_job_no='" + job_no + "'&cbo_company_name=" + company;
		http.open("POST", "../woven_order/requires/pre_cost_entry_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}

	function fnc_generate_report_reponse() {
		if (http.readyState == 4) {
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><body>' + http.responseText + '</body</html>');
			d.close();
		}
	}
	function generate_worder_report(type,job_no,company_id,buyer_id,style_ref,txt_costing_date,entry_form,quotation_id,garments_nature)
	{
			
			
		var ref=$("#txt_style_ref").val(style_ref);
		 var style_ref_no=encodeURIComponent(""+$("#txt_style_ref").val()+"");
		//alert(style_ref+'='+style_ref_no);
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true) zero_val="1"; else zero_val="0";
		
		if(type=="summary" || type=="budget3_details")
		{
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
			'&txt_style_ref='+"'"+style_ref_no+"'"+
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
			http.setRequestHeader("Access-Control-Allow-Origin","*");
			http.setRequestHeader("Access-Control-Allow-Headers","Content-Type");
			http.setRequestHeader("Access-Control-Allow-Methods","application/x-www-form-urlencoded");
			http.setRequestHeader("Content-type","GET, POST, PUT, DELETE, OPTIONS");

			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
		}
		else
		{
			if(garments_nature==3)
			{
				//alert(garments_nature+'==='+entry_form);
				
				
				if(entry_form==111)
				{
					var data="action="+type+
					'&zero_value='+zero_val+
					'&txt_job_no='+"'"+job_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&cbo_buyer_name='+"'"+buyer_id+"'"+
					'&txt_style_ref='+"'"+style_ref_no+"'"+
					'&print_option_id='+'1,2,3,4,5,6,7,8'+
					'&txt_po_breack_down_id='+''+
					'&txt_costing_date='+"'"+txt_costing_date+"'";
					http.open("POST","../woven_gmts/requires/pre_cost_entry_controller.php",true);
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
					'&txt_style_ref='+"'"+style_ref_no+"'"+
					'&print_option_id='+'1,2,3,4,5,6,7,8'+
					'&txt_po_breack_down_id='+''+
					'&txt_costing_date='+"'"+txt_costing_date+"'"+
					'&path='+"'"+path+"'";//order\sweater
					http.open("POST","../woven_gmts/requires/pre_cost_entry_controller_v2.php",true);
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
					http.open("POST","../woven_gmts/requires/pre_cost_entry_controller_v2.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_fabric_report_reponse;
				}
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
					//alert(data)
					if(type == 'preCostRpt11')
					{
						http.open("POST","../woven_order/requires/pre_cost_entry_report_controller_v2.php",true);
					}else{
					http.open("POST","../woven_order/requires/pre_cost_entry_controller_v2.php",true);
					}
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_fabric_report_reponse;
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

	 function generate_worder_report3(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,print_id,entry_form,type,i,fabric_nature,cbouom){ 
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
		'&cbouom='+cbouom+
		'&path=../../';
			
		freeze_window(5);
		
		if(fabric_nature == 3){

			if(entry_form==118 ) 
			{
				http.open("POST","../woven_gmts/requires/fabric_booking_urmi_controller.php",true);
			}else if(entry_form==271){
				http.open("POST","../woven_gmts/requires/woven_partial_fabric_booking_controller.php",true);
				
			}
		}
		else{
			if(entry_form==118 ) 
			{  
				http.open("POST","../woven_order/requires/fabric_booking_urmi_controller.php",true);
			}else if(entry_form==108){
				http.open("POST","../woven_order/requires/partial_fabric_booking_controller.php",true);
				
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
	function generate_fabric_report(booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,action_type,i)
	{
	
				report_title ='Sample Booking';
				
				var data="action="+action_type+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&txt_order_no_id='+"'"+order_id+"'"+
				'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
				'&cbo_fabric_source='+"'"+fabric_source+"'"+
				'&id_approved_id='+"'"+approved+"'"+
				'&report_title='+report_title+
				//'&show_comment='+show_comment+
				'&txt_job_no='+"'"+job_no+"'"+
				//'&revised_no='+"'"+revised_no+"'";
				'&path=../../';
				// alert(revised_no);
				freeze_window(5);
	
					http.open("POST","../woven_order/requires/sample_booking_controller.php",true);

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
	function generate_trims_report(type,booking_no,company_id,order_id,category,fabric_source,job_no,approved,action_type,i)
	{
		var show_comment='';
				var r=confirm("Press  \"Cancel\"  to hide  Remarks\nPress  \"OK\"  to Show Remarks");
				if (r==true){
					show_comment="1";
				}
				else{
					show_comment="0";
				}
				report_title ='Multiple Job Wise Trims Booking V2';
				var data="action="+action_type+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					//'&cbo_isshort='+"'"+is_short+"'"+
					//'&cbo_level='+"'"+cbo_level+"'"+
					'&show_comment='+"'"+show_comment+"'"+
					'&report_title='+"'"+report_title+"'"+
					'&report_type=1&path=../../';
					
					freeze_window(5);
					http.open("POST","../woven_order/requires/trims_booking_multi_job_controllerurmi.php",true);
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

	function generate_emb_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,category,is_short,emb_name,item_number_id,action_type,i,supplier_id,cbo_level,revised_no)
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
		var data="action="+action_type+
							'&txt_booking_no='+"'"+booking_no+"'"+
							'&txt_job_no='+"'"+job_no+"'"+
							'&cbo_company_name='+"'"+company_id+"'"+
							'&txt_order_no_id='+"'"+order_id+"'"+
							'&cbo_supplier_name='+"'"+supplier_id+"'"+
							'&cbo_booking_natu='+"'"+emb_name+"'"+
							'&cbo_gmt_item='+"'"+item_number_id+"'"+
							'&show_comment='+"'"+show_comment+"'"+
							'&cbo_template_id=1'+
							'&report_title= Embellishment Work Order '+
							'&id_approved_id='+"'"+approved+"'"
							'&path=../../';
				freeze_window(5);
				http.open("POST","../woven_order/requires/print_booking_multijob_controller.php",true);
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

	function generate_service_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,category,is_short,emb_name,item_number_id,action_type,i,supplier_id,cbo_level,revised_no)
	{
		if(action='show_trim_booking_report2')
			{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to Show  Rate and Amount\nPress  \"OK\"  to Hide Rate and Amount");
			if (r==true)
			{
				show_rate="1";
			}
			else
			{
				show_rate="0";
			}
			}
			report_title ='Service Booking Sheet';	
			var data="action="+action_type+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&txt_job_no='+"'"+job_no+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&show_rate='+"'"+show_rate+"'"+
					'&report_title='+"'"+report_title+"'"+
					'&cbo_isshort='+"'"+is_short+"'";
					freeze_window(5);
				
					http.open("POST","../woven_order/requires/service_booking_controller.php",true);
				

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
	function generate_aop_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,category,is_short,emb_name,item_number_id,action_type,i,supplier_id,cbo_level,revised_no)
	{
		if(action='show_trim_booking_report2')
			{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to Show  Rate and Amount\nPress  \"OK\"  to Hide Rate and Amount");
			if (r==true)
			{
				show_rate="1";
			}
			else
			{
				show_rate="0";
			}
			}
			report_title ='Service Booking For AOP V2';	
			var data="action="+action_type+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&txt_job_no='+"'"+job_no+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&show_rate='+"'"+show_rate+"'"+
					'&report_title='+"'"+report_title+"'"+
					'&cbo_isshort='+"'"+is_short+"'";
					freeze_window(5);
				
					http.open("POST","../woven_order/requires/service_booking_aop_urmi_controller.php",true);
				

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
	function generate_yarn_dying_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,category,is_short,emb_name,item_number_id,action_type,i,supplier_id,cbo_level,revised_no)
	{
		var data="action="+action_type+
			 			'&txt_booking_no='+"'"+booking_no+"'"+
			 			'&cbo_company_name='+"'"+company_id+"'"+
			 			'&show_comment=1'+						
			 			'&update_id='+"'"+order_id+"'";
			 	freeze_window(5);
			 	http.open("POST","../woven_order/requires/yarn_dyeing_charge_booking_sales_controller.php",true);
				

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
	

	function openmypage(po_id, item_name, job_no, book_num, trim_dtla_id, action) { //alert(book_num);
		var popup_width = '900px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/work_followup_report_controller.php?po_id=' + po_id + '&item_name=' + item_name + '&job_no=' + job_no + '&book_num=' + book_num + '&trim_dtla_id=' + trim_dtla_id + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}

	function openmypage_inhouse(po_id, item_name, action) {
		var popup_width = '900px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/work_followup_report_controller.php?po_id=' + po_id + '&item_name=' + item_name + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}

	function openmypage_issue(po_id, item_name, action) {
		var popup_width = '900px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/work_followup_report_controller.php?po_id=' + po_id + '&item_name=' + item_name + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}

	function order_qty_popup(company, job_no, po_id, buyer, action) {
		//alert(po_id);
		var popup_width = '800px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/work_followup_report_controller.php?company=' + company + '&job_no=' + job_no + '&po_id=' + po_id + '&buyer=' + buyer + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}

	function order_req_qty_popup(company, job_no, po_id, buyer, rate, item_group, boook_no, description, country_id, trim_dtla_id, start_date, end_date, action) {
		//alert(country_id);
		var popup_width = '800px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/work_followup_report_controller.php?company=' + company + '&job_no=' + job_no + '&po_id=' + po_id + '&buyer=' + buyer + '&rate=' + rate + '&item_group=' + item_group + '&boook_no=' + boook_no + '&description=' + description + '&country_id_string=' + country_id + '&trim_dtla_id=' + trim_dtla_id + '&start_date=' + start_date + '&end_date=' + end_date + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}

	$(document).ready(function(){
		$("#cbo_search_by").change(function(){
			if($(this).val()==1)
			{
				$("#dateRange").text('Shipment Date Wise');
			}
			else if($(this).val()==2) 
			{
				$("#dateRange").text('Po Rcvd Date Wise');
			}
			else if($(this).val()==3) 
			{
				$("#dateRange").text('PO Insert Date');
			}
			$("#dateRange").css({'color':'blue'});
		})
	});

	function search_populate(str) {

		if (str == 1) {
			document.getElementById('search_by_th_up').innerHTML = "Shipment Date";
			//document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"	value=""  />';
		} else if (str == 2) {
			document.getElementById('search_by_th_up').innerHTML = "Precost Date";
			///document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"	value=""  />';
		}

	}
</script>

</head>

<body onLoad="set_hotkey();">
	<form id="accessoriesFollowup_report">
		<div style="width:100%;" align="center">
			<? echo load_freeze_divs("../../", ''); ?>
			<h3 align="left" id="accordion_h1" style="width:900px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel">
				<fieldset style="width:856px;">
					<table class="rpt_table" width="856" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
						<thead>
							<tr>
								<th>Company Name</th>
								<th>Buyer Name</th>
								<th>Job No</th>
								<th>Styel Ref</th>
								<th>Date Category</th>
								<th align="center" id="dateRange">Date Range</th>
								<th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
							</tr>
						</thead>
						<tbody>
							<tr class="general">
								<td>
									<?
									echo create_drop_down("cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/work_followup_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );");
									?>
								</td>
								<td id="buyer_td">
									<?
									echo create_drop_down("cbo_buyer_name", 130, $blank_array, "", 1, "-- All Buyer --", $selected, "", 0, "");
									?>
								</td>

								<td align="center">
									<input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Job No">
								</td>

								<td align="center">
									<input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" placeholder="Style Ref">
								</td>
								<td align="center">
									<?
									$search_by_arr1 = array(1 => "Shipment Date Wise", 2 => "Po Rcvd Date Wise", 3 => "PO Insert Date Wise");
									echo create_drop_down( "cbo_search_by", 100, $search_by_arr1,"", 1, "--Select-- ", 1, "" );
									//echo create_drop_down("cbo_search_by", 100, $search_by_arr1, "", 0, "", "", '', 0); //search_by(this.value)
									?>
								</td>

								<td id="search_by_td">
									<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date">&nbsp; To
									<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date">
								</td>
								</td>

								<td>
									<input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" />
								</td>
							</tr>
						</tbody>
					</table>
					<table>
						<tr>
							<td>
								<? echo load_month_buttons(1); ?>
							</td>
						</tr>
					</table>
				</fieldset>
			</div>
		</div>

		<div id="report_container" align="center"></div>
		<div id="report_container2"></div>
	</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
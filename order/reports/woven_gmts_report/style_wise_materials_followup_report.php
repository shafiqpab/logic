<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Style Wise materials Follow up Report (Woven)
Functionality	:	
JS Functions	:
Created by		:	Zakaria joy
Creation date 	: 	22-05-2021
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
$based_on_arr = array(1 => "Shipment Date ",2 => "PCD Date");
echo load_html_head_contents("Accessories Followup Report[Budget 2]", "../../../", 1, 1,$unicode,'1','');
?>	

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated(action)
	{
		if(form_validation('cbo_company_name','Company Name')==false)//*txt_date_from*txt_date_to*From Date*To Date
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*txt_style_ref*cbo_based_on*txt_date_from*txt_date_to',"../../../")+"&report_title="+report_title;
			//alert(data);
			//return;
			freeze_window(3);
			http.open("POST","requires/style_wise_materials_followup_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	function useState(report_id,txt_booking_no,cbo_company_name,id_approved_id,cbo_level,cbo_buyer_name)
	{
		
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  Remarks\nPress  \"OK\"  to Show Remarks");
			if (r==true){
				show_comment="1";
			}
			else{
				show_comment="0";
			}
			var action ="";
			var report_title = "Multiple Job Wise Trims Booking V2";
			if(report_id==67)
			{
				action = "show_trim_booking_report";
				
			}
			else if(report_id==183)
			{
				action = "show_trim_booking_report2";
				
			}
			else if(report_id==177)
			{
				action = "show_trim_booking_report4";
			
			}
			else if(report_id==175)
			{
				action = "show_trim_booking_report5";
				
			}
			else if(report_id==235)
			{
				action = "show_trim_booking_report9";
				
			}
			else if(report_id==85)
			{
				action = "print_t";
				
			}
			else if(report_id==746)
			{
				action = "print_t7";
				
			}
			else if(report_id==774)
			{
				action = "show_trim_booking_report_wg";
			
			}
			else if(report_id==14)
			{
				action = "show_trim_booking_report16";
			
			}
			else if(report_id==72)
			{
				action = "show_trim_booking_report6";
				
			}else{
				action = "show_trim_booking_report";
			}
		
			freeze_window();
			var data="action="+action+'&show_comment='+show_comment+'&txt_booking_no='+txt_booking_no+'&cbo_company_name='+cbo_company_name+'&id_approved_id='+id_approved_id+'&cbo_level='+cbo_level+'&cbo_buyer_name='+cbo_buyer_name+'&report_title='+report_title;
			http.open("POST","../../woven_gmts/requires/trims_booking_multi_job_controllerurmi.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;
		
	}
	
	function generate_trim_report_reponse(){
		if(http.readyState == 4){
			release_freezing();
			var file_data=http.responseText.split("****");
			//alert(file_data[2]);
			if(file_data[2]==100)
			{
			$('#data_panel').html(file_data[1]);
			$('#print_report4').removeAttr('href').attr('href','requires/'+trim(file_data[0]));
				//$('#print_report4')[0].click();
			document.getElementById('print_report4').click();
			}
			else
			{
				$('#pdf_file_name').html(file_data[1]);
				$('#data_panel').html(file_data[0]);
			}
	
	
		var report_title=$( "div.form_caption" ).html();
        var w = window.open("Surprise", "_blank");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title>'+report_title+'</title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
        d.close();
	
		}
	}
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+tot_rows+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}

	function job_report_generate(company,job_no,buyer_name,style_ref_no,costing_date,po_id,costing_per,type,entry_from)
	{
		
		var zero_val='';var rate_amt=2; 
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true)
		{
			zero_val="1";
		}
		else
		{
			zero_val="0";
		} 
		var data="action="+type+"&zero_value="+zero_val
			+"&txt_job_no='"+job_no
			+"'&cbo_company_name='"+company
			+"'&cbo_buyer_name="+buyer_name
			+"&txt_style_ref='"+style_ref_no
			+"'&txt_costing_date='"+costing_date
			+"'&txt_po_breack_down_id='"+po_id
			+"'&cbo_costing_per="+costing_per
			+"&cbo_template_id=1"
			
		;
		if(type=='bom_pcs_woven4'){
				http.open("POST","../../../order/woven_gmts/requires/pre_cost_entry_report_controller_v2.php",true);				
			}
			else{
				http.open("POST","../../../order/woven_gmts/requires/pre_cost_entry_controller_v2.php",true);
			}
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_job_report_generate_reponse= function(){
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
				release_freezing();
		   }
		};
	}

	function booking_report_generate(company,booking_no,fabric_natu,fabric_source,approved_id,po_id,type,entry_from)
	{	
		if(entry_from==271){
			
			var report_title='Woven Partial Fabric Booking';
			var data="action="+type
			+"&txt_booking_no='"+booking_no
			+"'&cbo_company_name="+company
			+"'&cbo_fabric_natu="+fabric_natu
			+"'&cbo_fabric_source="+fabric_source
			+"'&id_approved_id="+approved_id
			+"'&txt_order_no_id="+po_id
			+"'&report_title="+report_title
			+"'&mail_data=0"
			+"'&path=../../";
			//alert(data);
			http.open("POST","../../../order/woven_gmts/requires/woven_partial_fabric_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_report_generate_reponse= function(){
					if(http.readyState == 4) 
					{
						var w = window.open("Surprise", "_blank");
						var d = w.document.open();
						d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
						d.close();
						release_freezing();
				}
			};
		}
		else if(entry_from==272){
			
			var report_title='Multiple Job Wise Trims Booking V2';
			var data="action="+type
			+"&txt_booking_no='"+booking_no
			+"'&cbo_company_name="+company
			+"'&cbo_buyer_name="+fabric_natu
			+"'&cbo_level="+fabric_source			
			+"'&id_approved_id="+approved_id
			+"'&report_title="+report_title
			+"'&is_mail_send="
			+"'&report_type=1"
			+"'&show_comment=0"
			+"'&mail_id=0";
			http.open("POST","../../../order/woven_gmts/requires/trims_booking_multi_job_controllerurmi.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_report_generate_reponse= function(){
					if(http.readyState == 4) 
					{
						var w = window.open("Surprise", "_blank");
						var d = w.document.open();
						d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
						d.close();
						release_freezing();
				}
			};
		}		
	}

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}

	function new_window(html_filter_print)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		//if(html_filter_print*1>1) $("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";
		
		if(html_filter_print*1>1) $("#table_body tr:first").show();
	}	
	
	
	
	function generate_report(company,job_no,buyer_name,style_ref_no,costing_date,po_id,costing_per,type)
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
		var data="action="+type+"&zero_value="+zero_val
			+"&txt_job_no='"+job_no
			+"'&cbo_company_name="+company
			+"'&cbo_buyer_name="+buyer_name
			+"'&txt_style_ref="+style_ref_no
			+"'&txt_costing_date="+costing_date
			+"'&txt_po_breack_down_id="+po_id
			+"'&cbo_costing_per="+costing_per
		;
		http.open("POST","../woven_order/requires/pre_cost_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}
	
	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><body>'+http.responseText+'</body</html>');
			d.close();
		}
	}
	function openmypage_jobstyle(type)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var page_link='requires/style_wise_materials_followup_report_controller.php?action=job_style_popup&companyID='+company+'&buyer_name='+buyer+'&txt_style_ref='+txt_style_ref+'&cbo_year='+cbo_year+'&type='+type+'&from_date='+from_date+'&to_date='+to_date;
		var title="Search Job/Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_data=this.contentDoc.getElementById("txt_selected_data").value;
			var selected_type=this.contentDoc.getElementById("txt_selected_type").value;
			var paramArr = selected_data.split("_");
			if(selected_type==2)
			{
				$("#txt_style_ref").val(paramArr[2]);
			}
			if(selected_type==1)
			{
				$("#txt_job_no").val(paramArr[1]);
			}
			//$("#txt_style_ref").val(style_des);
			//$("#txt_style_ref_id").val(style_id); 
			//$("#txt_style_ref_no").val(style_no); 
		}
	}
	function openmypage_job_popup()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		/*		else
				{	
		*/			var data = $("#cbo_company_name").val()+"_"+$("#cbo_buyer_name").val()+"_"+$("#txt_job_no").val()+"_"+$("#cbo_year").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?data='+data+'&action=job_no_popup', 'Job No Search', 'width=500px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_job_id");
				var theemailval=this.contentDoc.getElementById("txt_job_val");
				if (theemailid.value!="" || theemailval.value!="")
				{
					// alert (theemailid.value+"=>"+theemailval.value);
					freeze_window(5);
					$("#hidd_job_id").val(theemailid.value);
					$("#txt_job_no").val(theemailval.value);
					release_freezing();
				}
			}
		//}
	}
	function openmypage(po_id,item_name,job_no,book_num,trim_dtla_id,action)
	{ //alert(book_num);
		var cbo_company_name=$("#cbo_company_name").val();
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_budget2_report_controller.php?po_id='+po_id+'&item_name='+item_name+'&job_no='+job_no+'&book_num='+book_num+'&trim_dtla_id='+trim_dtla_id+'&action='+action+'&cbo_company_name='+cbo_company_name, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	function openmypage_inhouse_btn3(job_id,yarn_id,po_id,color,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?po_id='+po_id+'&yarn_id='+yarn_id+'&job_id='+job_id+'&color='+color+'&action='+action, 'Recevied Details', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_inhouse(job_id,yarn_id,po_id,color,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?po_id='+po_id+'&yarn_id='+yarn_id+'&job_id='+job_id+'&color='+color+'&action='+action, 'Recevied Details', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	function openmypage_inhouse2(job_id,yarn_id,po_id,from_po_id,color,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?po_id='+po_id+'&yarn_id='+yarn_id+'&job_id='+job_id+'&color='+color+'&from_po_id='+from_po_id+'&action='+action, 'Recevied Details', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_issue_btn3(job_id,yarn_id,po_id,color,rate,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?po_id='+po_id+'&yarn_id='+yarn_id+'&job_id='+job_id+'&color='+color+'&rate='+rate+'&action='+action, 'Issue Details', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_issue(job_id,yarn_id,po_id,color,rate,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?po_id='+po_id+'&yarn_id='+yarn_id+'&job_id='+job_id+'&color='+color+'&rate='+rate+'&action='+action, 'Issue Details', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	function openmypage_issue2(job_id,yarn_id,po_id,to_po_id,color,rate,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?po_id='+po_id+'&yarn_id='+yarn_id+'&job_id='+job_id+'&color='+color+'&to_po_id='+to_po_id+'&rate='+rate+'&action='+action, 'Issue Details', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	
	function order_summery_popup(job_id,action)
	{
		var popup_width='800px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?job_id='+job_id+'&action='+action, 'Job Color Size Wise Summery', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	function order_qty_popup(job_id,action)
	{
		var popup_width='800px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?job_id='+job_id+'&action='+action, 'Order Details', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	
	function order_req_qty_popup(job_id, yarn_id, color_id, action)
	{
		var popup_width='800px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?job_id='+job_id+'&yarn_id='+yarn_id+'&color_id='+color_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	function trim_req_qty_popup(job_id, trim_id, action)
	{
		var popup_width='800px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?job_id='+job_id+'&trim_id='+trim_id+'&action='+action, 'Trims Details', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	function trim_wo_qty_popup(job_id, trim_id, action)
	{
		var popup_width='800px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?job_id='+job_id+'&trim_id='+trim_id+'&action='+action, 'PO Summary', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	function order_wo_qty_popup(job_id, yarn_id, fcolor_id,color_id, action)
	{
		var popup_width='800px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?job_id='+job_id+'&yarn_id='+yarn_id+'&fcolor_id='+fcolor_id+'&color_id='+color_id+'&action='+action, 'PO Summary', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	function openmypage_trim_inhouse(po_id,item_name,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?po_id='+po_id+'&item_name='+item_name+'&action='+action, 'Received Details', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	function openmypage_trim_inhouse2(po_id,from_po_id,item_name,description,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?po_id='+po_id+'&item_name='+item_name+'&description='+description+'&from_po_id='+from_po_id+'&action='+action, 'Received Details', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	function openmypage_trim_issue(po_id,item_name,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?po_id='+po_id+'&item_name='+item_name+'&action='+action, 'Received Details', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	function openmypage_trim_issue2(po_id,to_po_id,item_name,description,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_materials_followup_report_controller.php?po_id='+po_id+'&item_name='+item_name+'&to_po_id='+to_po_id+'&description='+description+'&action='+action, 'Received Details', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	
	function search_populate(str)
	{
		if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Shipment Date";
		}
		else if(str==2)
		{
			document.getElementById('search_by_th_up').innerHTML="Precost Date";
		}
	}

	function print_report_button_setting(report_ids)
	{
		//alert(report_ids);
		$("#report_btn_1").hide();
		$("#report_btn_2").hide();
		$("#report_btn_3").hide();
		$("#report_btn_4").hide();
		$("#report_btn_5").hide();
	
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==108) $("#report_btn_1").show();
			if(report_id[k]==195) $("#report_btn_2").show();
			if(report_id[k]==242) $("#report_btn_3").show();
			if(report_id[k]==359) $("#report_btn_4").show();
			if(report_id[k]==306) $("#report_btn_5").show();
		}
	}

	function change_date_caption(id){
		if(id==1){
			$('#date_caption').html("<span style='color:blue'>Shipment Date Range</span>");
		}
		else if(id==2){
			$('#date_caption').html("<span style='color:blue'>PCD Date Range</span>");
		}
	}
	
</script>

</head>

<body onLoad="set_hotkey();">
    <form id="materialsFollowup_report">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",''); ?>
    <h3 align="left" id="accordion_h1" style="width:1200px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width: 12px;00px;">
                <table class="rpt_table" width="1200" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                    <thead>
                        <tr>                    
                            <th width="160" class="must_entry_caption">Company Name</th>
                            <th width="130">Buyer Name</th>
                            <th width="50">Year</th>
                            <th width="90">Job No</th>
                            <th width="120">Style Ref.</th>
							<th width="100">Date Type</th>
							<th width="160" id="date_caption">Shipment Date Range</th>
                            <th width="180"><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td><? echo create_drop_down( "cbo_company_name", 180, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/style_wise_materials_followup_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'report_formate_setting','requires/style_wise_materials_followup_report_controller')" ); ?></td>
                            <td id="buyer_td"> <? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                            <td><? echo create_drop_down( "cbo_year", 90, create_year_array(),"", 1,"-All Year-", date('Y'), "",0,"" ); ?></td>
                            <td>
								<input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:90px" placeholder="Wr./Browse" onDblClick="openmypage_job_popup()">
							    <input type="hidden"name="hidd_job_id" id="hidd_job_id" class="text_boxes" style="width:90px" value="">
						    </td>
                            <td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:120px" placeholder="Wr./Browse" onDblClick="openmypage_jobstyle(2)" ></td>
							<td><? echo create_drop_down( "cbo_based_on", 100, $based_on_arr, "", 0,"All Type", $selected, "change_date_caption(this.value)" ); ?></td>
                            <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>
                            <td>
                                <input type="button" id="report_btn_1" class="formbutton" style="width:80px;display:none;" value="Show" onClick="fn_report_generated('report_generate')"/>
								
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center"><? echo load_month_buttons(1); ?></td>
                            <td align="center"><input type="button" id="report_btn_2" class="formbutton" style="width:60px;display:none;" value="Show 2" onClick="fn_report_generated('report_generate2')"/>	
							<input type="button" id="report_btn_3" class="formbutton" style="width:60px;display:none;" value="Show 3" onClick="fn_report_generated('report_generate3')"/>
							<input type="button" id="report_btn_4" class="formbutton" style="width:60px;display:none;" value="Show 4" onClick="fn_report_generated('report_generate4')"/>
							<input type="button" id="report_btn_5" class="formbutton" style="width:60px;display:none;" value="PCD" onClick="fn_report_generated('report_generate5')"/>
						    </td>
						
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
		<div style="display:none" id="data_panel"></div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" ></div>
    </form>    

</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

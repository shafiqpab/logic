<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Date wise Pre Cost report controller.
Functionality	:
JS Functions	:
Created by		:	Shariar Ahmed
Creation date 	: 	31-12-2023
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
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

$booking_status_arr = array(1 => "Approved",2 => "Un-Approved");
$based_on_arr = array(1 => "Budget Date",2 => "Shipment Date ",3 => "Last Approved Date");
echo load_html_head_contents("Date Wise Pre Cost", "../../", 1, 1,$unicode,1,1);
?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	function fn_report_generated(type)
	{
			//var booking_no = $("#txt_booking_no").val();
			console.log(type);
			freeze_window(3);
			if( form_validation('cbo_company_id','Company Name')==false )
			{
				release_freezing();
				return;
			}	
			
			console.log("here");
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_date_from*txt_date_to*txt_style*hdn_style*txt_job*hdn_job*txt_order*hdn_order*txt_ir_no*cbo_based_on',"../../")+'&report_title='+report_title+'&type='+type;
			
			http.open("POST","requires/date_wise_pre_cost_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		
	}
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			//var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
	 		show_msg('3');
			release_freezing();
		}
	}
	function new_window()
	{
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
			release_freezing();
	}
	function func_onDblClick_style()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_id").val();
		var buyerID = $("#cbo_buyer_id").val();
		var page_link='requires/date_wise_pre_cost_report_controller.php?action=action_style_popup&companyID='+companyID+'&buyerID='+buyerID;
		var title='Style Ref. Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_ref=this.contentDoc.getElementById("hide_style_ref").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			
			$('#txt_style').val(style_ref);
			$('#hdn_style').val(job_id);	 
		}
	}

	//func_onDblClick_job
	function func_onDblClick_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var page_link='requires/date_wise_pre_cost_report_controller.php?action=action_job_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=830px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hdn_job_no").value;
			var job_id=this.contentDoc.getElementById("hdn_job_id").value;
			
			$('#txt_job').val(job_no);
			$('#hdn_job').val(job_id);	 
		}
	}

	function generate_worder_report(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,print_id,entry_form,type,i,fabric_nature,cbouom){ 
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
	
		if(print_id==143 || print_id==160 || print_id==274 || print_id==155 || print_id==28 || print_id==723){ report_title='Partial Fabric Booking';} else if(print_id==72 || print_id==191 || print_id==45 || print_id==53){ report_title='Short Fabric Booking';}else{ report_title='Main Fabric Booking';}
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
			}else if(entry_form==88){
				http.open("POST","../woven_order/requires/short_fabric_booking_controller.php",true);
				
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
	function generate_trim_report(action,txt_booking_no,cbo_company_name,id_approved_id,cbo_isshort,entry_form)
	{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  remarks\nPress  \"OK\"  to Show remarks");
			if (r==true) show_comment="1"; else show_comment="0";

			
				var data="action="+action+'&report_title=Multiple Job Wise Trim Booking&show_comment='+show_comment+'&txt_booking_no='+"'"+txt_booking_no+"'"+'&cbo_company_name='+cbo_company_name+'&id_approved_id='+id_approved_id+'&cbo_isshort='+cbo_isshort+'&link=1';
				//freeze_window(5);
				if(entry_form==87) //Knit
				{
					http.open("POST","../woven_order/requires/trims_booking_multi_job_controllerurmi.php",true);
				}else if(entry_form==262){
					http.open("POST","../woven_order/requires/short_trims_booking_multi_job_controllerurmi.php",true);
				}else
				{
					http.open("POST","../woven_gmts/requires/trims_booking_multi_job_controllerurmi.php",true);
				}
			
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;

	}

	function generate_trim_report_reponse()
	{
		if(http.readyState == 4)
		{
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
			release_freezing();
		}
	}

	function generate_report_v3(company_name,job_no,style_ref_no,buyer_name,costing_date,po_ids,type)
	{
		freeze_window(3);
		if(type=="summary" || type=="budget3_details" || type=="budget_4")
		{
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
				var rpt_type=7; comments_head=1;
			}

			var report_title="Budget/Cost Sheet";
			//	var comments_head=0;
			var cbo_company_name=company_name;
			var cbo_buyer_name=buyer_name;
			var txt_style_ref=style_ref_no;
			var txt_style_ref_id="";
			var txt_quotation_id="";
			var sign=0;
			var txt_order=""; var txt_order_id="";  var txt_season_id=""; var txt_season=""; var txt_file_no="";
			var data="action=report_generate&reporttype="+rpt_type+
			'&cbo_company_name='+"'"+cbo_company_name+"'"+
			'&cbo_buyer_name='+"'"+cbo_buyer_name+"'"+
			'&txt_style_ref='+"'"+txt_style_ref+"'"+
			'&txt_style_ref_id='+"'"+txt_style_ref_id+"'"+
			'&txt_order='+"'"+txt_order+"'"+
			'&txt_order_id='+"'"+txt_order_id+"'"+
			'&txt_season='+"'"+txt_season+"'"+

			'&txt_season_id='+"'"+txt_season_id+"'"+
			'&txt_file_no='+"'"+txt_file_no+"'"+
			'&txt_quotation_id='+"'"+txt_quotation_id+"'"+
			'&txt_hidden_quot_id='+"'"+txt_quotation_id+"'"+
			'&comments_head='+"'"+comments_head+"'"+
			'&sign='+"'"+sign+"'"+
			'&report_title='+"'"+report_title+"'"+
			'&path=../../../';

			http.open("POST","../../reports/management_report/merchandising_report/requires/cost_breakup_report2_controller.php",true);

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
		else
		{
			var rate_amt=2; var zero_val='';
			if(type!='mo_sheet' && type != 'budgetsheet' && type != 'materialSheet' && type != 'materialSheet2'   && type!='mo_sheet_3')
			{
				var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
			}

			if(type=='materialSheet' ||  type == 'materialSheet2')
			{
				var r=confirm("Press \"OK\" to show Qty  Excluding Allowance.\nPress \"Cancel\" to show Qty Including Allowance.");
			}

			var excess_per_val="";

			if(type=='mo_sheet')
			{
				excess_per_val = prompt("Please enter your Excess %", "0");
				if(excess_per_val==null) excess_per_val=0;else excess_per_val=excess_per_val;
			}

			if(type == 'budgetsheet')
			{
				var r=confirm("Press  \"OK\" to Show Budget, \nPress  \"Cancel\"  to Show Management Budget");
			}

			if(type=='mo_sheet_3')
			{
				excess_per_val = prompt("Please enter your Excess %", "0");
				if(excess_per_val==null) excess_per_val=0;else excess_per_val=excess_per_val;
			}

			if (r==true) zero_val="1"; else zero_val="0";
			var print_option_id="";
			//company_name,job_no,style_ref_no,buyer_name,costing_date,po_ids,type
			//eval(get_submitted_variables('txt_job_no*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_costing_date'));
			if(type == 'preCostRpt7' ||type == 'preCostRpt8' || type == 'trims_check_list' || type == 'budgetsheet2'  || type == 'budgetsheet4' || type == 'budgetsheet2v3'|| type == 'budgetsheet3' || type == 'ocsReport' || type == 'preCostRpt10' || type == 'preCostRpt11' || type == 'preCostRpt12' || type =='accessories_details3' || type =='preCostRpt13' || type =='fabricBom' ){
				var data="action="+type+"&zero_value="+zero_val+"&rate_amt="+rate_amt+"&excess_per_val="+excess_per_val+"&txt_job_no='"+job_no+"'&cbo_company_name="+company_name+"&cbo_buyer_name="+buyer_name+"&txt_style_ref='"+style_ref_no+"'&cbo_costing_per="+costing_date+"&print_option_id="+print_option_id+"&txt_po_breack_down_id="+po_ids;
				http.open("POST","../woven_order/requires/pre_cost_entry_report_controller_v2.php",true);
			}
			else{
				var data="action="+type+"&zero_value="+zero_val+"&rate_amt="+rate_amt+"&excess_per_val="+excess_per_val+"&txt_job_no='"+job_no+"'&cbo_company_name="+company_name+"&cbo_buyer_name="+buyer_name+"&txt_style_ref='"+style_ref_no+"'&cbo_costing_per="+costing_date+"&print_option_id="+print_option_id+"&txt_po_breack_down_id="+po_ids;
				http.open("POST","../woven_order/requires/pre_cost_entry_controller_v2.php",true);
			}

			freeze_window(3);
			
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_generate_report_v3_reponse;
		}
		
	}

	function fnc_generate_report_v3_reponse()
	{
		if(http.readyState == 4)
		{
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
			release_freezing();
		}
	}
	
	//func_onDblClick_order
	function func_onDblClick_order()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_id").val();
		var buyerID = $("#cbo_buyer_id").val();
		var page_link='requires/date_wise_pre_cost_report_controller.php?action=action_order_popup&companyID='+companyID+'&buyerID='+buyerID;
		var title='Order No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			
			$('#txt_order').val(order_no);
			$('#hdn_order').val(order_id);
		}
	}
	

	
	function fnRemoveHidden(str){
		document.getElementById(str).value='';
	}
	
	function change_date_caption(id){
		if(id==1){
			$('#date_caption').html("<span style='color:blue'>Booking Date Range</span>");
		}
		else if(id==2){
			$('#date_caption').html("<span style='color:blue'>Shipment Date Range</span>");
		}
		else{
			$('#date_caption').html("<span style='color:blue'>Last Approved Date Range</span>");
		}
	}
</script>

</head>
<body onLoad="set_hotkey();">
<form id="bookingreport_1">
<div style="width:100%; margin:1px auto;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <h3 style="width:1140px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:1140px" >
            <fieldset style="width:1140px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th width="150" class="must_entry_caption">Company Name</th>
                    <th width="150">Buyer</th>
                    <th width="130">Style</th>
                    <th width="130">Job No</th>
                    <th width="130">Order No</th>
					<th width="130">IR/IB</th>
                   	<th width="100">Based On</th>
                    <th width="140" class="must_entry_caption" colspan="2" id="date_caption">Job Date Range</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" onClick="reset_form('bookingreport_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/date_wise_pre_cost_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 150, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, ""); ?></td>                         
						<td>
                            <input type="text" name="txt_style" id="txt_style" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="func_onDblClick_style();" />
                            <input type="hidden" id="hdn_style" name="hdn_style" style="width:70px" />
                        </td>
                        <td>
                           <input type="text" style="width:130px" class="text_boxes" name="txt_job" id="txt_job" onDblClick="func_onDblClick_job()" placeholder="Browse" />
                            <input type="hidden" id="hdn_job" name="hdn_job" style="width:70px" />
                        </td>
                        <td>
                           <input type="text" style="width:130px" class="text_boxes" name="txt_order" id="txt_order" onDblClick="func_onDblClick_order()" placeholder="Browse" />
                            <input type="hidden" id="hdn_order" name="hdn_order" style="width:70px" />
                        </td>
						<td>
                           <input type="text" style="width:130px" class="text_boxes" name="txt_ir_no" id="txt_ir_no" placeholder="Write" />
                        </td>
						<td><? echo create_drop_down( "cbo_based_on", 100, $based_on_arr, "", 0,"All Type", $selected, "change_date_caption(this.value)" ); ?></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"/><input type="hidden" name="cbo_approval_status" id="cbo_approval_status" value="0"></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" /></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(0);" /></td>
                    </tr>
                    <tr>
						<a href="" id="aa1"></a>
                        <td align="center" colspan="8"><?=load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
        	</fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </div>
</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	set_multiselect('cbo_company_id','0','0','','0');
	set_multiselect('cbo_buyer_id','0','0','','0');	
    </script>
</html>
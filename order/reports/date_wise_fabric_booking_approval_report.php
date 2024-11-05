<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Date wise fabric booking approval report controller.
Functionality	:
JS Functions	:
Created by		:	Shariar Ahmed
Creation date 	: 	09-02-2023
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

$fabric_booking_type = array( 0 =>'-- Select --',1 => "Main Fabric Booking",2=>'Partial Fabric Booking', 3 => "Short Fabric Booking", 4 => "Sample Fabric Booking - With Order", 5 => 'Sample Fabric Booking - Without Order');
$booking_status_arr = array(1 => "Approved",2 => "Un-Approved");
$based_on_arr = array(1 => "Booking Date",2 => "Shipment Date ",3 => "Last Approved Date");
echo load_html_head_contents("Date Wise Fabric Booking", "../../", 1, 1,$unicode,1,1);
?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	function fn_report_generated(type)
	{
			console.log(type);
			freeze_window(3);
			if(type !=4){
				if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*date from*date to')==false )
				{
					release_freezing();
					return;
				}
			}else{
				if( form_validation('cbo_company_id','Company Name')==false )
				{
					release_freezing();
					return;
				}	
			}
			console.log("here");
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_fab_booking_type*cbo_fabric_source*cbo_party_id*cbo_pay_mode*txt_date_from*txt_date_to*txt_booking_no_id*txt_booking_no*cbofabricnature*cbo_based_on',"../../")+'&report_title='+report_title+'&type='+type;
			
			http.open("POST","requires/date_wise_fabric_booking_approval_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		
	}
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			release_freezing();
			var reponse=trim(http.responseText).split("####");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//alert(reponse[3])
			if(trim(reponse[3])==0 || trim(reponse[3])==4)
			{
				//document.getElementById('report_container').innerHTML=report_convert_button('../../');
				if(tot_rows*1>1){
					var tableFilters = {
						col_operation: {
						   id: ["total_booking_qty_kg","total_booking_qty_yds","total_booking_qty_mtr","total_booking_amount"],
						   col: [19,20,21,22],
						   operation: ["sum","sum","sum","sum"],
						   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					 }
					
					 setFilterGrid("table_body",-1,tableFilters);
				}
			}
			else if(trim(reponse[3])==2)
			{
				 //document.getElementById('report_container').innerHTML=report_convert_button('../../');
				if(tot_rows*1>1){
					var tableFilters = {
						col_operation: {
						   id: ["value_qtykg","value_qtyyds","value_qtymtr","value_amount"],
						   col: [22,23,24,25],
						   operation: ["sum","sum","sum","sum"],
						   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					 }
					 setFilterGrid("table_body",-1,tableFilters);
				}
			}
			else if(trim(reponse[3])==1)
			{
				if(tot_rows*1>1){
					
					 setFilterGrid("table_body",-1);
				}
			}
			//alert(type);
			else if(trim(reponse[3])==3) //Color wise
			{
				//document.getElementById('report_container').innerHTML=report_convert_button('../../');
				//$("#report_container2").html(reponse[0]);
				append_report_checkbox('table_header_1',1);
				if(tot_rows*1>1){
						var tableFilters = {
							col_operation: {
							   id: ["value_qtykg","value_qtyyds","value_qtymtr","value_amount"],
							   col: [29,30,31,32],
							   operation: ["sum","sum","sum","sum"],
							   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
							}
						 }
						 setFilterGrid("table_body",-1,tableFilters);
					}
			 
			}
			else
			{
				//document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;';
			}
	
	 		show_msg('3');
			release_freezing();
		}
	}


	
	function openmypage_fabricBooking()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_fab_booking_type = $('#cbo_fab_booking_type').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Booking Selection Form';
			var page_link = 'requires/date_wise_fabric_booking_approval_report_controller.php?cbo_company_id='+cbo_company_id+'&cbo_fab_booking_type='+cbo_fab_booking_type+'&action=fabricBooking_popup';
			var popup_width="1070px";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=400px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("hidden_booking_id").value;	
				var theename=this.contentDoc.getElementById("hidden_booking_no").value; 
				if(theemail!="")
				{
					freeze_window(5);
					
					$('#txt_booking_no').val(theename);
					$('#txt_booking_no_id').val(theemail);
					release_freezing();
				} 
			}
		}
	}
	
	function fnRemoveHidden(str){
		document.getElementById(str).value='';
	}
	
	function fn_on_change(type)
	{
		var cbo_company_name = $("#cbo_company_id").val();
		var cbo_pay_mode = $("#cbo_pay_mode").val();
		if(type==1)
		{
		load_drop_down( 'requires/date_wise_fabric_booking_approval_report_controller', cbo_company_name, 'load_drop_down_buyer', 'buyer_td' );
		set_multiselect('cbo_buyer_id','0','0','','0','');
		}
		if(type==2)
		{
		load_drop_down( 'requires/date_wise_fabric_booking_approval_report_controller', cbo_pay_mode, 'load_drop_down_suplier', 'sup_td' );
		set_multiselect('cbo_party_id','0','0','','0');
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
			}
			else if(entry_form==108){
				http.open("POST","../woven_order/requires/partial_fabric_booking_controller.php",true);	
			}
			else if(entry_form==88){
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
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
				d.close();
				release_freezing();
		   }
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
					d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
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
			if(type == 'preCostRpt7' ||type == 'preCostRpt8' || type == 'trims_check_list' || type == 'budgetsheet2'  || type == 'budgetsheet4' || type == 'budgetsheet3' || type == 'ocsReport' || type == 'preCostRpt10' || type == 'preCostRpt11' || type == 'preCostRpt12')
				{
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
	function change_date_caption(id){
		if(id==1){
			$('#date_caption').html("<span style='color:blue'>Booking Date Range</span>");
		}
		else{
			$('#date_caption').html("<span style='color:blue'>Shipment Date Range</span>");
		}
	}
</script>

</head>
<body onLoad="set_hotkey();">
<form id="bookingreport_1">
<div style="width:100%; margin:1px auto;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <h3 style="width:1100px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:1100px" >
            <fieldset style="width:1100px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th width="150" class="must_entry_caption">Company Name</th>
                    <th width="150">Buyer</th>
                    <th width="100">Fab Nature</th>
                    <th width="120">Booking Type</th>
                    <th width="100">Source</th>
                    <th width="100">Pay Mode</th>
                    <th width="150">Party Name</th>
                    <th width="100">Internal Booking no</th>
                   <th width="100">Based On</th>
                    <th width="140" class="must_entry_caption" colspan="2" id="date_caption">Booking Date Range</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" onClick="reset_form('bookingreport_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/date_wise_fabric_booking_approval_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 150, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, ""); ?></td>
                        <td><? echo create_drop_down( "cbofabricnature", 100, $item_category, "", 1,"All Type", $selected, "","","2,3" ); ?></td>
                        <td><? echo create_drop_down( "cbo_fab_booking_type", 120, $fabric_booking_type, "--Select Type--", $selected, ""); ?></td>
                        <td><? echo create_drop_down( "cbo_fabric_source", 100, $fabric_source,"",1,"--Select Source--", $selected, ""); ?></td>
                        <td><? echo create_drop_down( "cbo_pay_mode", 100, $pay_mode,"", 1, "-- Select Pay Mode --", $selected, "fn_on_change(2);","","1,2,3,5" ); ?></td>
                        <td id="sup_td"><? echo create_drop_down( "cbo_party_id", 150, $blank_array, "", 1,"-- Select Party --", $selected, "" ); ?></td>       
                       
						<td><input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:90px" placeholder="Browse/Write" onChange="fnRemoveHidden('txt_booking_no_id');" onDblClick="openmypage_fabricBooking();" > <input type="hidden" name="txt_booking_no_id" id="txt_booking_no_id" value=""></td>
						<td><? echo create_drop_down( "cbo_based_on", 100, $based_on_arr, "", 0,"All Type", $selected, "change_date_caption(this.value)" ); ?></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"/><input type="hidden" name="cbo_approval_status" id="cbo_approval_status" value="0"></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" /></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(4);" /></td>
                    </tr>
					<tr>
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
	set_multiselect('cbo_company_id','0','0','','0','fn_on_change(1)');
	set_multiselect('cbo_buyer_id','0','0','','0');
	set_multiselect('cbofabricnature','0','0','','0');
	set_multiselect('cbo_fabric_source','0','0','','0');
	set_multiselect('cbo_party_id','0','0','','0');
	
    </script>
</html>
<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Lab Dip Status Report V2.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	25-10-2023
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

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Lab Dip Status Report V2", "../../", 1, 1,$unicode,'1','');
?>	

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated()
	{
		freeze_window(3);
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			release_freezing();
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_req_no*cbo_date_type*txt_date_from*txt_date_to*cbo_labappstatus*cbo_req_for*cbo_year_selection',"../../")+'&report_title='+report_title;
			//alert(data);
			
			http.open("POST","requires/lab_dip_report_v2_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			var search_by=reponse[3];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+tot_rows+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			tableFilters="";
			 
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
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
		
		if(html_filter_print*1>1) $("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";
		
		if(html_filter_print*1>1) $("#table_body tr:first").show();
	}
	function fn_on_change()
	{
		var cbo_company_name = $("#cbo_company_name").val();
		load_drop_down( 'requires/lab_dip_report_v2_report_controller', cbo_company_name, 'load_drop_down_buyer', 'buyer_td' );
	}

	function generate_report(data)
	{ 
		var data="action=sample_requisition_print&data="+data;
		http.open("POST","../woven_order/requires/sample_requisition_controller.php",true);		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}
		
	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<form id="labdipappv2report">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:880px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:880px;">
                <table class="rpt_table" width="880" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>                    
                            <th width="140" class="must_entry_caption">Company Name</th>
                            <th width="140">Buyer Name</th>
                            <th width="100">Requisition No.</th>
                            <th width="100">Date Type</th>
                            <th width="120" colspan="2">Date Range</th>
                            <th width="80">Approval Status</th>
                            <th width="100">Requisiton For</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td><?=create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" ); ?></td>
                            <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                            <td><input name="txt_req_no" id="txt_req_no" class="text_boxes" style="width:90px" placeholder="Req. No" ></td>
                            <td>
                            <?
                            $date_type_arr = array(1 => "Requisition Date", 2 => "Planned Delivery Date", 3 => "Submission Date", 4 => "Action Date");
                            echo create_drop_down( "cbo_date_type", 100, $date_type_arr,"", 1, "-Date Type-", 1, "" );
                            ?>
                            </td>
                            <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" ></td>
                            <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" ></td>
                            <td>
                            <?
                            // $labapp_status_arr = array(1 => "Requisition Date", 2 => "Planned Delivery Date", 3 => "Submission Date", 4 => "Action Date");
                            echo create_drop_down( "cbo_labappstatus", 80, $approval_status,"", 1, "-App. Status-", $selected, "" );
                            ?>
                            </td>
                            <td><?=create_drop_down( "cbo_req_for", 100, $sample_req_for_arr,"", 1, "-Req. For-", $selected, "" ); ?></td>
                            <td><input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(1);" /></td>
                        </tr>
                    </tbody>
                    <tfoot>
                    	<tr>
                        	<td colspan="9" align="center"><? echo load_month_buttons(1); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
        </div>
    </div>
    
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script>
	//set_multiselect('cbo_item_group','0','0','0','0');
	set_multiselect('cbo_company_name','0','0','0','0', 'fn_on_change()');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This file for daily order entry report info.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	24-04-2016
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
echo load_html_head_contents("Daily Order Entry","../../../", 1, 1, $unicode);
?>	
<script> 
	var permission = '<? echo $permission; ?>';	
		
	function fn_report_generated()
	{
		if (form_validation('cbo_company_name','Plsease Select Comapny')==false)
		{
			return;
		}
		else
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_style_ref*cbo_order_status*txt_order_no*cbo_date_type*txt_date_from*txt_date_to',"../../../");
			//alert(data);
			freeze_window();
			http.open("POST","requires/daily_order_info_report_controller.php",true);
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
			$('#data_panel').html( '<br><b>Convert To </b><a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>' );
			$('#data_panel').append( '&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>' );		
			$('#report_container').html(reponse[0]);
			var tableFilters = {
					col_operation: {
					id: ["tot_smv","tot_val","tot_po_qty"],
					col: [8,11,12],
					operation: ["sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML"]
					}
				}
			setFilterGrid("report_tbl",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('report_div').style.overflow="auto";
		document.getElementById('report_div').style.maxHeight="none";
		
		$("#report_tbl tr:first").hide();
		 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('report_div').style.overflowY="scroll";
		document.getElementById('report_div').style.maxHeight="350px";
		
		$("#report_tbl tr:first").show();
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
	
	
	function date_fill_change(str)
	{
		if (str==1)
		{
			document.getElementById('search_date_td').innerHTML='OPD Date';
		}
		else
		{
			document.getElementById('search_date_td').innerHTML='Insert Date';
		}
	}
	
function generate_worder_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved)
{
	var data="action=show_fabric_booking_report"+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
				'&cbo_fabric_source='+"'"+fabric_source+"'"+
				'&id_approved_id='+"'"+approved+"'"+
				'&txt_job_no='+"'"+job_no+"'"+
				'&txt_order_no_id='+"'"+order_id+"'";
				//javascript:generate_worder_report('2','FFL-Fb-16-01064','1','6614','2','1','FFL-16-00719','1');
	
	
	
	if(type==1)	
	{			
		http.open("POST","../../../order/woven_order/requires/short_fabric_booking_controller.php",true);
	}
	else if(type==2)
	{
		http.open("POST","../../../order/woven_order/requires/fabric_booking_controller.php",true);
	}
	else if(type==3)
	{
	var data="action=show_fabric_booking_report"+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
				'&cbo_fabric_source='+"'"+fabric_source+"'"+
				'&id_approved_id='+"'"+approved+"'"+
				'&txt_job_no='+"'"+job_no+"'"+
				'&id_approved_id='+"'"+approved+"'"+
				'&show_yarn_rate='+"'0'"+
				'&report_title='+"'Short Fabric Booking'"+
				'&txt_order_no_id='+"'"+order_id+"'";
		
		http.open("POST","../../../order/woven_order/requires/short_fabric_booking_controller.php",true);
	}
	else
	{
		http.open("POST","../../../order/woven_order/requires/sample_booking_controller.php",true);
	}
	
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_fabric_report_reponse;
}

function generate_fabric_report_reponse()
{
	if(http.readyState == 4) 
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
		d.close();
	}
}
	

function print_gsd_report(str)
{
	var data_arr=str.split('*');
	print_report( data_arr[0]+'*'+data_arr[1]+'*'+data_arr[2], "print_gsd_report", "../../../prod_planning/requires/gsd_entry_controller") 
	show_msg("3");
}



</script>
</head>

<body onLoad="set_hotkey();">
<form id="Price_Quotation_Statment">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <h3 style="width:1100px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1100px;">
                <table class="rpt_table" width="1100px" cellpadding="1" cellspacing="0" rules="all" border="1">
                    <thead>
                        <th width="150" class="must_entry_caption">Company</th>
                        <th width="120">Buyer</th>                  	
                        <th width="100">Job</th>                  	
                        <th id="search_text_td">Style Ref.</th>
                        <th width="100">Order</th>                 	
                        <th width="100">Order Status</th> 
                        <th width="100">Date Type</th>                 	
                        <th id="search_date_td">OPD Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                    </thead>
                    <tr class="general">
                        <td> 
							<?
                           	 echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/daily_order_info_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
							<? 
                            	echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td>
                        	<input type="text"  id="txt_job_no" class="text_boxes" style="width:100px">
                        </td>
                        <td>
                        	<input type="text"  id="txt_style_ref" class="text_boxes" style="width:100px">
                        </td>
                        <td>
                        	<input type="text"  id="txt_order_no" class="text_boxes" style="width:100px">
                        </td>
                        <td>
							<? echo create_drop_down( "cbo_order_status", 100, $order_status, "", 1, "----All----",1, "",0,"" ); ?>
                        </td>
                       	<td>
							<? 
							$date_type_arr=array(1=>'OPD Date',2=>'Insert Date');
							echo create_drop_down( "cbo_date_type", 100, $date_type_arr, "", 1, "----Select----",1, "date_fill_change(this.value);",0,"" ); 
							?>
                        </td>
                       
                        <td>
                           <?
							$current_date = date("d-m-Y",strtotime(add_time(date("H:i:s",time()),0)));
							$previous_date = date('d-m-Y', strtotime('-4 day', strtotime($current_date))); 
						   ?>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" value="<? echo $previous_date;?>" >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" value="<? echo $current_date;?>"  ></td>
                        <td>
                        	<input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table> 
            <br /> 
            </fieldset>
        </div>
    </div>
    <div id="data_panel" align="center"></div>
    <div id="report_container" align="center"></div>
</form>   
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
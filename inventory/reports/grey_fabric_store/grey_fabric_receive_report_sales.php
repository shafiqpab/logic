<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create grey_fabric_receive_report_sales
Functionality	:	
JS Functions	:
Created by		:	Md. Abdul Barik Tipu
Creation date 	: 	09/02/2023
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
echo load_html_head_contents("grey_fabric_receive_report_sales","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters =
	{
		col_operation:
		{
			id: ["value_total_receive_qnty"],
			col: [15],
			operation: ["sum"],
			write_method: ["innerHTML"]
		}
	}

	function openmypage_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/grey_fabric_receive_report_sales_controller.php?action=order_no_search_popup&companyID='+company;  
		var title="Search Sales Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_data=this.contentDoc.getElementById("hidden_booking_data").value;
			if (order_data!="")
			{
				var exdata=order_data.split("**");
				$('#txt_order').val(exdata[1]);
				$('#txt_order_id').val(exdata[0]);	 
			}
		}
	}

	function reset_field()
	{
		reset_form('item_receive_issue_1','report_container2','','','','');
	}

	function  generate_report(rptType)
	{
		var cbo_company_name    = $("#cbo_company_name").val();
		var cbo_year           = $("#cbo_year").val();
		var txt_order           = $("#txt_order").val();
		var txt_order_id        = $("#txt_order_id").val();
		var txt_date_from       = $("#txt_date_from").val(); 
		var txt_date_to         = $("#txt_date_to").val();
		var cbo_floor_id        	= $("#cbo_floor_id").val();
		var cbo_knitting_source = $("#cbo_knitting_source").val();

		if(txt_order=="")
		{
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*Form Date*To Date')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_name','Company')==false )
			{
				return;
			}
		}

		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_year="+cbo_year+"&txt_order="+txt_order+"&txt_order_id="+txt_order_id+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_floor_id="+cbo_floor_id+"&cbo_knitting_source="+cbo_knitting_source+"&rptType="+rptType;
		var data="action=generate_report_receive"+dataString;

		freeze_window(5);
		http.open("POST","requires/grey_fabric_receive_report_sales_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse; 
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1,tableFilters);
			release_freezing();
			show_msg('3');
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide(); 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
		$('#table_body tr:first').show();
	}
</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
		<form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
			<h3 align="left" id="accordion_h1" style="width:770px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div style="width:770px;" align="center" id="content_search_panel">
				<fieldset style="width:770px;">
					<table class="rpt_table" width="770" cellpadding="0" cellspacing="0" rules="all">
						<thead>
							<tr>
								<th width="150" class="must_entry_caption">Company</th>                               
								<th width="50">Year</th>
								<th width="100">FSO No.</th>
								<th width="100">Knitting Source</th>
								<th width="100">Floor</th>
								<th width="170" class="must_entry_caption" id="date_header">Receive Date Range</th>
								<th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_field()" /></th>
							</tr>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/grey_fabric_receive_report_sales_controller',this.value, 'load_drop_down_floor', 'floor_td' );" );
								?>                          
							</td>
							<td>
				            	<? 
				            	echo create_drop_down( "cbo_year", 50, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); 
				            	?>
				            </td>
				            <td align="center">
								<input type="text" style="width:100px;"  name="txt_order" id="txt_order"  ondblclick="openmypage_order()"  class="text_boxes" placeholder="Browse"  readonly />   
								<input type="hidden" name="txt_order_id" id="txt_order_id"/>               
							</td>
							<td>
								<?
								echo create_drop_down( "cbo_knitting_source", 100, $knitting_source,"", 1, "ALL", 0, "","","1,3" );
								?>
							</td>

							<td id="floor_td">
								<?
								echo create_drop_down( "cbo_floor_id", 100, $blank_array,"", 1, "-- All Floor --", $selected, "",0,"" );
								?>
							</td>
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px;" placeholder="From Date" readonly/>&nbsp; TO &nbsp;
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:55px;" readonly/>
							</td>

							<td>
								<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:55px" class="formbutton" />
							</td>
						</tr>
						<tr>
							<td colspan="11" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
						</tr>

					</table> 
				</fieldset> 

			</div>
			<div id="report_container" align="center"></div>
			<div id="report_container2"></div> 
		</form>    
	</div>    
</body>

<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

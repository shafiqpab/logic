<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Roll Splitting Summary Report (Before Issue)
Functionality	:	
JS Functions	:
Created by		:	Tipu
Creation date 	: 	11-12-2022
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
echo load_html_head_contents("Grey Order To Order Transfer Report Sales","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters =
	{
		col_operation:
		{
			id: ["value_total_original_wgt_qnty","value_total_child_qty"],
			col: [8,10],
			operation: ["sum","sum"],
			write_method: ["innerHTML","innerHTML"]
		}
	}

	function openmypage_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/splitting_summary_report_controller.php?action=order_no_search_popup&companyID='+company+'&buyer_name='+buyer;  
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

	function openmypage_booking()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/splitting_summary_report_controller.php?action=booking_no_search_popup&companyID='+company+'&buyer_name='+buyer;  
		var title="Sales Booking No. Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_data=this.contentDoc.getElementById("hidden_booking_data").value;
			if (order_data!="")
			{
				var exdata=order_data.split("**");
				$('#txt_booking_no').val(exdata[0]); 
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
		var cbo_buyer_name      = $("#cbo_buyer_name").val();
		var cbo_cust_buyer_name = $("#cbo_cust_buyer_name").val();
        var txt_booking_no      = $("#txt_booking_no").val();
		var txt_order           = $("#txt_order").val();
		var txt_order_id        = $("#txt_order_id").val();
		var txt_date_from       = $("#txt_date_from").val(); 
		var txt_date_to         = $("#txt_date_to").val();
		var txt_barcode_no      = $("#txt_barcode_no").val();
		var cbo_order_type      = $("#cbo_order_type").val();
		var cbo_knitting_source = $("#cbo_knitting_source").val();
		var txt_style_no      	= $("#txt_style_no").val();

		if(txt_booking_no=="") 
		{
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
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

		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&txt_order="+txt_order+"&txt_order_id="+txt_order_id+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&txt_barcode_no="+txt_barcode_no+"&cbo_order_type="+cbo_order_type+"&cbo_knitting_source="+cbo_knitting_source+"&cbo_cust_buyer_name="+cbo_cust_buyer_name+"&txt_booking_no="+txt_booking_no+"&rptType="+rptType+"&txt_style_no="+txt_style_no;

		var data="action=generate_report"+dataString;

		freeze_window(5);
		http.open("POST","requires/splitting_summary_report_controller.php",true);
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

	function fn_change_base(base)
	{
		if(base == 1){
			$("#date_header").html("<p style='color:blue;'>Transaction Date Range</p");
		}else{
			$("#date_header").html("<p style='color:blue;'>Insert Date Range</p>");
		}
	}
</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
		<form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
			<h3 align="left" id="accordion_h1" style="width:1100px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div style="width:1115px;" align="center" id="content_search_panel">
				<fieldset style="width:1115px;">
					<table class="rpt_table" width="1115" cellpadding="0" cellspacing="0" rules="all">
						<thead>
							<tr>
								<th width="100" class="must_entry_caption">Company</th>                               
								<th width="100">Customer Name</th>
								<th width="100">Cust. Buyer </th>
								<th width="80">Order Type</th>
								<th width="80">Grey Source</th>
								<th width="80">Style No</th>
								<th width="100">Sales Order No.</th>
								<th width="100" class="must_entry_caption">Sales Booking No.</th>
								<th width="80">Barcode No</th>
								<th width="170" id="date_header">Date Range</th>
								<th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_field()" /></th>
							</tr>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/splitting_summary_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/splitting_summary_report_controller',this.value, 'load_drop_down_cust_buyer', 'cust_buyer_td' );" );
								?>                          
							</td>
							<td id="buyer_td">
								<? 
								echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
								?>
							</td>
                            <td id="cust_buyer_td">
								<? 
								echo create_drop_down( "cbo_cust_buyer_name", 100, $blank_array,"", 1, "-- All Cust Buyer --", $selected, "",0,"" );
								?>
							</td>
						<td>
							<?
							$order_type=array(1=>"With Order",2=>"Without Order");
							echo create_drop_down( "cbo_order_type", 80, $order_type,"", 1, "ALL", 0, "",0 );
							?>
						</td>
						<td>
							<?
							echo create_drop_down( "cbo_knitting_source", 80, $knitting_source,"", 1, "ALL", 0, "","","1,3" );
							?>
						</td>
						<td align="center">
							<input type="text" style="width:80px;"  name="txt_style_no" id="txt_style_no" class="text_boxes" placeholder="Write"/>            
						</td>
						<td align="center">
							<input type="text" style="width:100px;"  name="txt_order" id="txt_order"  ondblclick="openmypage_order()"  class="text_boxes" placeholder="Browse"  readonly />   
							<input type="hidden" name="txt_order_id" id="txt_order_id"/>               
						</td>

                        <td align="center">
							<input type="text" style="width:100px;"  name="txt_booking_no" id="txt_booking_no" class="text_boxes"  ondblclick="openmypage_booking()" placeholder="Browse/Write"/>            
						</td>
						<td align="center">
							<input type="text" style="width:80px;"  name="txt_barcode_no" id="txt_barcode_no" class="text_boxes" placeholder="Write"/>            
						</td>
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px;" placeholder="From Date"/>&nbsp; To &nbsp;
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:55px;"/>
						</td>

						<td>
							<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:55px" class="formbutton" />
						</td>
					</tr>
					<tr>
						<td colspan="12" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
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

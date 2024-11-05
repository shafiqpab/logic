<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Order To Order Transfer Report Sales
Functionality	:	
JS Functions	:
Created by		:	Tipu
Creation date 	: 	07-12-2022
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
			id: ["value_total_transfer_qnty"],
			col: [17],
			operation: ["sum"],
			write_method: ["innerHTML"]
		}
	}
	var tableFilters2 =
	{
		col_operation:
		{
			id: ["value_total_roll","value_total_transfer_qnty"],
			col: [19,20],
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
		var buyer = $("#cbo_cust_buyer_name").val();	
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/grey_fabric_transfer_report_sales_v2_controller.php?action=order_no_search_popup&companyID='+company+'&buyer_name='+buyer;  
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
		var buyer = $("#cbo_cust_buyer_name").val();	
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/grey_fabric_transfer_report_sales_v2_controller.php?action=booking_no_search_popup&companyID='+company+'&buyer_name='+buyer;  
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

	function  fun_generate_report(rptType)
	{
		var cbo_company_name    = $("#cbo_company_name").val();
		var cbo_cust_buyer_name = $("#cbo_cust_buyer_name").val();
        var txt_booking_no      = $("#txt_booking_no").val();
		var txt_order           = $("#txt_order").val();
		var txt_order_id        = $("#txt_order_id").val();		
		var cbo_location_id     = $("#cbo_location_id").val();
		var txt_int_ref_no     = $("#txt_int_ref_no").val();
		var txt_barcode_no     = $("#txt_barcode_no").val();
		var txt_date_from       = $("#txt_date_from").val(); 
		var txt_date_to         = $("#txt_date_to").val();

		/*if(cbo_company_name==0)
		{			
			alert("Please Select Company");
			return;			
		}*/
		if ((txt_date_from=='' || txt_date_to=='') && txt_int_ref_no=="" && txt_barcode_no=="" && txt_order==0 && txt_booking_no==0)
		{
			if( form_validation('txt_date_from*txt_date_to*txt_int_ref_no*txt_barcode_no','Form Date*To Date*IB/IR*Barcode No')==false )
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
		if(rptType == 1)
		{
			var action="generate_report";
		}
		else if(rptType == 2)
		{
			var action="generate_report2";
		}

		// alert();return;
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_location_id="+cbo_location_id+"&txt_order="+txt_order+"&txt_order_id="+txt_order_id+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_cust_buyer_name="+cbo_cust_buyer_name+"&txt_booking_no="+txt_booking_no+"&txt_int_ref_no="+txt_int_ref_no+"&txt_barcode_no="+txt_barcode_no+"&rptType="+rptType;

		var data="action="+action+dataString;

		freeze_window(5);
		http.open("POST","requires/grey_fabric_transfer_report_sales_v2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse; 
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			//alert(reponse[2]);
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			if(reponse[2]==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			else if(reponse[2]==2)
			{
				setFilterGrid("table_body2",-1,tableFilters2);
			}
			
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

	function generate_sys_challan_report(transfer_id,action)
	{
		var company_id = $("#cbo_company_name").val();
		var report_title = "Roll Wise Grey Order To Order Transfer Report";
		var path = "../";
		
		print_report( company_id+'*'+transfer_id+'*'+report_title+'*'+path, action, "../../grey_fabric/requires/grey_sales_order_to_order_roll_trans_controller" );
	}
</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
		<form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
			<h3 align="left" id="accordion_h1" style="width:1100px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div style="width:1100px;" align="center" id="content_search_panel">
				<fieldset style="width:1100px;">
					<table class="rpt_table" width="1100" cellpadding="0" cellspacing="0" rules="all">
						<thead>
							<tr>
								<th class="must_entry_caption">Company</th>
								<th>Location</th>
								<th title="Cust. Buyer">Buyer</th>
								<th>IB/IR</th>
								<th>Sales Order No.</th>
								<th>Sales Booking No.</th>
								<th>Barcode No</th>
								<th class="must_entry_caption">Transfer Date</th>
								<th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_field()" /></th>
							</tr>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/grey_fabric_transfer_report_sales_v2_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/grey_fabric_transfer_report_sales_v2_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/grey_fabric_transfer_report_sales_v2_controller',this.value, 'load_drop_down_cust_buyer', 'cust_buyer_td' );" );
								?>                          
							</td>
							<td id="location_td">
	                        	<?
									echo create_drop_down( "cbo_location_id", 130,$blank_array,"", 1, "-- Select Location--", $selected, "","","","","","");
	                            ?>
	                   		</td>
                            <td id="cust_buyer_td">
								<? 
								echo create_drop_down( "cbo_cust_buyer_name", 100, $blank_array,"", 1, "-- All Cust Buyer --", $selected, "",0,"" );
								?>
							</td>
							<td align="center">
								<input type="text" style="width:80px;"  name="txt_int_ref_no" id="txt_int_ref_no" class="text_boxes" placeholder="Write"/>            
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
								<input type="button" name="search" id="search" value="Show" onClick="fun_generate_report(1)" style="width:55px" class="formbutton" />
								<input type="button" name="search" id="search" value="Show 2" onClick="fun_generate_report(2)" style="width:55px" class="formbutton" />
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

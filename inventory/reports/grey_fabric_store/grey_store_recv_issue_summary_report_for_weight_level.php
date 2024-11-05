<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Store Wise Receive Issue Summary Report
Functionality	:	
JS Functions	:
Created by		:	Tipu
Creation date 	: 	28-08-2022
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
echo load_html_head_contents("Date Wise Item Receive Issue","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters =
	{
		col_operation:
		{
			id: ["value_total_receive_qnty","value_total_trans_in_qnty","value_total_no_of_roll"],
			col: [36,37,38],
			operation: ["sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML"]
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
		var cbo_is_sales = $("#cbo_is_sales").val();
		var page_link='requires/grey_store_recv_issue_summary_report_for_weight_level_controller.php?action=order_no_search_popup&companyID='+company+'&buyer_name='+buyer+'&cbo_is_sales_type='+cbo_is_sales;  
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
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_buyer_name = $("#cbo_buyer_name").val();

		var txt_order = $("#txt_order").val();
		var txt_order_id = $("#txt_order_id").val();

		var txt_date_from = $("#txt_date_from").val(); 
		var txt_date_to = $("#txt_date_to").val();

		var cbo_based_on = $("#cbo_based_on").val();
		var cbo_knitting_source = $("#cbo_knitting_source").val();
		var cbo_knitting_company = $("#cbo_knitting_company").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var cbo_is_sales = $("#cbo_is_sales").val();

		if(txt_order=="")
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to', 'Company*Form Date*To Date')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name', 'Company')==false)
			{
				return;
			}
		}

		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&txt_order="+txt_order+"&txt_order_id="+txt_order_id+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_based_on="+cbo_based_on+"&cbo_knitting_source="+cbo_knitting_source+"&cbo_knitting_company="+cbo_knitting_company+"&cbo_store_name="+cbo_store_name+"&cbo_is_sales="+cbo_is_sales+"&rptType="+rptType;

		if(rptType == 1)
		{
			var data="action=generate_report_receive"+dataString;
		}
		else if(rptType == 2)
		{
			var data="action=generate_report_issue"+dataString;
		}
		else if(rptType == 3)
		{
			var data="action=generate_report_transfer_in"+dataString;
		}
		else
		{
			var data="action=generate_report_transfer_out"+dataString;
		}

		freeze_window(5);
		http.open("POST","requires/grey_store_recv_issue_summary_report_for_weight_level_controller.php",true);
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

	function open_mypage_roll(barcode_nos)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/grey_store_recv_issue_summary_report_for_weight_level_controller.php?barcode_nos='+barcode_nos+'&action=barcode_popup', 'Details Veiw', 'width=450, height=350px,center=1,resize=1,scrolling=0','../../');
	}
	function fn_change_base(base)
	{
		if(base == 1){
			$("#date_header").html("<p style='color:blue;'>Transaction Date Range</p");
		}else{
			$("#date_header").html("<p style='color:blue;'>Insert Date Range</p>");
		}
	}

	function generate_recv_print_report(company_id, system_id,booking_no, receive_basis, location, report_print_btn) 
	{
		report_print_btn=1;
        if(report_print_btn==1) // Print
        {
        	var report_title="Knit Grey Fabric Receive";
            var data = company_id + '*' + system_id + '*' + report_title + '*' + booking_no+'*'+receive_basis+'*'+location;
            window.open("../../../inventory/grey_fabric/requires/grey_fabric_receive_controller.php?data=" + data + '&action=grey_fabric_receive_print', true);
        }
        return;
    }

	function generate_print_report(company_id, system_id, issue_number,report_print_btn) 
	{
		var print_with_vat=0;
		var report_title=$( "div.form_caption" ).html();
		var show_item='';
		// alert(report_print_btn);
		// 109,66, 85,137,95,909,863 // old 94,95,35,36,37,64
        if(report_print_btn==109) // Print
        {
        	var op=4;
        	var r=confirm("Press  \"Cancel\"  to hide  Comments\nPress  \"OK\"  to Show Comments");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
            var data = company_id + '*' + system_id + '*' + issue_number + '*' + report_title + '*' + print_with_vat+'*'+op+'*'+show_item;
            window.open("../../../inventory/grey_fabric/requires/grey_fabric_issue_controller.php?data=" + data + '&action=grey_fabric_issue_print', true);
        }
        else if(report_print_btn==66) // Print 2
        {
        	var op=6;
        	var r=confirm("Press  \"OK\"  to Show  Buyer and Style\nPress  \"Cancel\"  to hide Buyer and Style");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			//generate_report_file( $('#cbo_company_id').val()+'*'+$('#hidden_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title+'*'+print_with_vat+'*'+operation+'*'+show_item+'*'+$('#hidden_is_sales').val(),'grey_fabric_issue_print','requires/grey_fabric_issue_controller');

            var data = company_id + '*' + system_id + '*' + issue_number + '*' + report_title + '*' + print_with_vat+'*'+op+'*'+show_item;
            window.open("../../../inventory/grey_fabric/requires/grey_fabric_issue_controller.php?data=" + data + '&action=grey_fabric_issue_print', true);
        }
        else if(report_print_btn==85) // Print 3
        {
        	var op=7;
        	//generate_report_file( $('#cbo_company_id').val()+'*'+$('#hidden_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title+'*'+print_with_vat+'*'+operation+'*'+show_item,'grey_fabric_issue_print_7','requires/grey_fabric_issue_controller');

            var data = company_id + '*' + system_id + '*' + issue_number + '*' + report_title + '*' + print_with_vat+'*'+op+'*'+show_item;
            window.open("../../../inventory/grey_fabric/requires/grey_fabric_issue_controller.php?data=" + data + '&action=grey_fabric_issue_print_7', true);
        }
        else if(report_print_btn==137) // Print 4
        {
			//generate_report_file( $('#cbo_company_id').val()+'*'+$('#hidden_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title+'*'+print_with_vat,'grey_fabric_issue_print_8','requires/grey_fabric_issue_controller');

        	var op=8;
            var data = company_id + '*' + system_id + '*' + issue_number + '*' + report_title + '*' + print_with_vat+'*'+op+'*'+show_item;
            window.open("../../../inventory/grey_fabric/requires/grey_fabric_issue_controller.php?data=" + data + '&action=grey_fabric_issue_print_8', true);
        }
        else if(report_print_btn==95) // Print With VAT
        {
			var print_with_vat=1;
			//generate_report_file( $('#cbo_company_id').val()+'*'+$('#hidden_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title+'*'+print_with_vat,'grey_fabric_issue_print','requires/grey_fabric_issue_controller');

            var op=5;
            var data = company_id + '*' + system_id + '*' + issue_number + '*' + report_title + '*' + print_with_vat+'*'+op+'*'+show_item;
            window.open("../../../inventory/grey_fabric/requires/grey_fabric_issue_controller.php?data=" + data + '&action=grey_fabric_issue_print', true);
        }
        else if(report_print_btn==909) // Print SB
        {
            var op=9;
            var data = company_id + '*' + system_id + '*' + issue_number + '*' + report_title + '*' + print_with_vat+'*'+op+'*'+show_item;
            window.open("../../../inventory/grey_fabric/requires/grey_fabric_issue_controller.php?data=" + data + '&action=grey_fabric_issue_print_9', true);
        }
        else // Print Multi Issue No
        {
			//print_report( issue_id +'*'+$('#cbo_company_id').val(), 'multiple_issue_no_print', 'requires/grey_fabric_issue_controller' );

            var data = system_id + '*' + company_id;
            window.open("../../../inventory/grey_fabric/requires/grey_fabric_issue_controller.php?data=" + data + '&action=multiple_issue_no_print', true);
        }
        return;
    }

    function change_caption(type) 
    {
    	$('#txt_order').val('');
    	$('#txt_order_id').val('');
		if(type==1)
		{
			$('#td_search').html('FSO No.');
		}
		else if(type==2)
		{
			$('#td_search').html('Order No.');
		}
    }

</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
		<form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
			<h3 align="left" id="accordion_h1" style="width:1140px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div style="width:1140px;" align="center" id="content_search_panel">
				<fieldset style="width:1140px;">
					<table class="rpt_table" width="1140" cellpadding="0" cellspacing="0" rules="all">
						<thead>
							<tr>
								<th width="100" class="must_entry_caption">Company</th>
								<th width="100">Buyer Name</th>
								<th width="100">Store Name</th>
								<th width="80">Grey Source</th>
								<th width="80">Party</th>
								<th width="80">Is Sales</th>
								<th width="100" id="td_search">Order No.</th>
								<th width="80">Based On</th>
								<th width="170" class="must_entry_caption" id="date_header">Transaction Date Range</th>
								<th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_field()" /></th>
							</tr>
						</thead>
						<tr class="general">
							<td>
							<?
							echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/grey_store_recv_issue_summary_report_for_weight_level_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/grey_store_recv_issue_summary_report_for_weight_level_controller',this.value, 'load_drop_down_store', 'store_td' );" );
							?>                    
							</td>
							<td id="buyer_td">
							<? 
							echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
							?>
							</td>
							<td id="store_td">
							<? 
							echo create_drop_down( "cbo_store_name", 100, $blank_array,"", 1, "-- All Store --", $selected, "",0,"" );
							?>
						</td>
						<td>
							<?
							echo create_drop_down( "cbo_knitting_source", 80, $knitting_source,"", 1, "ALL", 0, "load_drop_down( 'requires/grey_store_recv_issue_summary_report_for_weight_level_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_knitting_com','knitting_com');","","1,3" );
							?>
						</td>
						<td id="knitting_com">
							<?
							echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 1, "" );
							?>
						</td>
						<td align="center">
							<? echo create_drop_down( "cbo_is_sales", 80, $yes_no,"",0, "--Select--", 2,"change_caption(this.value)",0 );?>
						</td>
						<td align="center">
							<input type="text" style="width:100px;"  name="txt_order" id="txt_order"  ondblclick="openmypage_order()"  class="text_boxes" placeholder="Browse"  readonly />   
							<input type="hidden" name="txt_order_id" id="txt_order_id"/>               
						</td>
						<td>
							<?
							$base_on_arr=array(1=>"Transaction Date",2=>"Insert Date");
							echo create_drop_down( "cbo_based_on", 80, $base_on_arr,"", 0, "", 1, "fn_change_base(this.value);",0 );
							?>
						</td>
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px;" placeholder="From Date" readonly/>&nbsp; TO &nbsp;
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:55px;" readonly/>
						</td>
						<td>
							<input type="button" name="search" id="search" value="Receive" onClick="generate_report(1)" style="width:55px" class="formbutton" />
							<input type="button" name="search" id="search" value="Issue" onClick="generate_report(2)" style="width:55px" class="formbutton" />
							<input type="button" name="search" id="search" value="Transfer In" onClick="generate_report(3)" style="width:70px" class="formbutton" />
							<input type="button" name="search" id="search" value="Transfer Out" onClick="generate_report(4)" style="width:70px" class="formbutton" />
						</td>
					</tr>
					<tr>
						<td colspan="9" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
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

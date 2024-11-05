<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Knitting Program Wise Grey Fabric Stock Report [Sales Order].
Functionality	:
JS Functions	:
Created by		:	Md Didarul Alam
Creation date 	: 	25-03-2018
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
echo load_html_head_contents("Knitting Program Wise Grey Fabric Stock Report [Sales Order]", "../../../", 1, 1,'',1,1);
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var permission = '<? echo $permission; ?>';
	var tableFilters =
	{
		col_operation:
		{
			id: ["value_html_opening_qnty","value_html_recv_qnty","value_html_issue_rtn_qnty","value_html_trans_qty_in","value_html_total_recv","value_html_issue_qty","value_html_rcv_rtn_qnty","value_html_trans_qty_out","value_html_toal_issue","value_html_total_stock"],
			col: [23,24,25,26,27,28,29,30,31,32],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function openmypage_booking_no()
	{
		if( form_validation('cbo_pocompany_id','Po Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_pocompany_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = "";
		var page_link='requires/style_wise_grey_fabric_stock_report_controller.php?action=fabricBooking_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=420px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_id=this.contentDoc.getElementById("txt_selected_id")
			var selected_name=this.contentDoc.getElementById("txt_selected")

			$('#txt_booking_no').val(selected_name.value);
			$('#txt_booking_id').val(selected_id.value);

		}
	}

	function openmypage_order()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year = $("#cbo_year").val();

		var page_link='requires/style_wise_grey_fabric_stock_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year='+cbo_year;

		var title='Sales Order No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=420px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_data=this.contentDoc.getElementById("hidden_booking_data").value;
			if (order_data!="")
			{
				var exdata=order_data.split("**");
				$('#txt_order_no').val(exdata[1]);
				$('#hide_order_id').val(exdata[0]);
			}
		}
	}

	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_id','Comapny Name')==false)
		{
			return;
		}

		var cbo_get_upto = $("#cbo_get_upto").val();
		var txt_days = $("#txt_days").val();
		var cbo_get_upto_qnty = $("#cbo_get_upto_qnty").val();
		var txt_qnty = $("#txt_qnty").val();

		if(cbo_get_upto!=0 && txt_days*1<=0)
		{
			alert("Please Insert Days.");
			$("#txt_days").focus();
			return;
		}

		if(cbo_get_upto_qnty!=0 && txt_qnty*1<=0)
		{
			alert("Please Insert Qty.");
			$("#txt_qnty").focus();
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_within_group*cbo_pocompany_id*cbo_buyer_id*cbo_year*txt_booking_no*txt_booking_id*txt_order_no*hide_order_id*txt_program_no*txt_date_from*txt_date_to*cbo_value_with*cbo_get_upto*txt_days*cbo_get_upto_qnty*txt_qnty*cbo_store_wise*cbo_store_name',"../../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/style_wise_grey_fabric_stock_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("tbl_list_search",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		$(".flt").css("display","none");
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$(".flt").css("display","block");
	}

	function openmy_popup_page(data,action)
	{
		var companyID = $("#cbo_company_id").val();
		var popup_width='760px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_grey_fabric_stock_report_controller.php?companyID='+companyID+'&data='+data+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}

	function fnc_bookingpopup(val)
	{
		var companyID = $("#cbo_company_id").val();
		if(val==1)
		{
			$('#txt_booking_no').attr('placeholder','Browse/Write');
			$('#txt_booking_no').removeAttr("onDblClick").attr("onDblClick","openmypage_booking_no();");
			load_drop_down( 'requires/style_wise_grey_fabric_stock_report_controller', val, 'load_drop_down_po_company', 'po_company_td' );
			load_drop_down( 'requires/style_wise_grey_fabric_stock_report_controller', 0, 'load_drop_down_buyer', 'buyer_td' );
		}
		else
		{
			$('#txt_booking_no').attr('placeholder','Write');
			$('#txt_booking_no').removeAttr('onDblClick','onDblClick');
			load_drop_down( 'requires/style_wise_grey_fabric_stock_report_controller', val, 'load_drop_down_po_company', 'po_company_td' );
			load_drop_down( 'requires/style_wise_grey_fabric_stock_report_controller', companyID, 'load_drop_down_buyer', 'buyer_td' );
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
	<form id="knittingStatusReport_1">
		<div style="width:100%;" align="center">
			<? echo load_freeze_divs ("../../../",'');  ?>
			<h3 style="width:1510px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
			<div id="content_search_panel" >
				<fieldset style="width:1510px;">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							<th class="must_entry_caption">Company Name</th>
							<th>Within Group</th>
							<th>Po Company</th>
							<th>Po Buyer</th>
							<th>Year</th>
							<th>Booking No</th>
							<th>Sales Order No</th>
							<th>Program No</th>
							<th>Value</th>
							<th>Date Range</th>
							<th>Store Wise</th>
							<th>Store Name</th>
							<th>Get Upto</th>
							<th>Days</th>
							<th>Get Upto</th>
							<th>Qty.</th>
							<th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
						</thead>
						<tbody>
							<tr align="center" class="general">
								<td><? echo create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "" ); ?>
							</td>
							<td><? echo create_drop_down( "cbo_within_group", 70, $yes_no,"", 1, "-Select-", 0, "fnc_bookingpopup(this.value);get_php_form_data( this.value, 'eval_multi_select', 'requires/style_wise_grey_fabric_stock_report_controller' );" ); ?></td>
							<td id="po_company_td"><? echo create_drop_down("cbo_pocompany_id", 130, $blank_array,"", 1,"-Po Company-", $selected, "",0,""); ?>
						</td>
						<td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-All Buyer-", $selected, "",0,"" ); ?></td>
						<td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"- All -", 0, "",0,"" ); //date("Y",time())?></td>
						<td>
							<input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:80px" onDblClick="openmypage_booking_no();" placeholder="Write/Browse" />
							<input type="hidden" id="txt_booking_id" name="txt_booking_id"/>
						</td>
						<td>
							<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:80px" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');">
							<input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
						</td>
						<td><input name="txt_program_no" id="txt_program_no" class="text_boxes_numeric" style="width:60px"></td>
						<td>
							<?
							$valueWithArr=array(1=>'Value With 0',2=>'Value Without 0');
							echo create_drop_down( "cbo_value_with", 102, $valueWithArr,"",0,"",2,"","","");
							?>
						</td>
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:50px;" />
							To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px;"/>
						</td>
						<td>
							<?
							echo create_drop_down( "cbo_store_wise", 50, $yes_no,"", 0, "--Select--", 2, "load_drop_down( 'requires/style_wise_grey_fabric_stock_report_controller', document.getElementById('cbo_company_id').value+'**'+this.value, 'load_drop_down_store', 'store_td' );",0 );
							?>
						</td>
						<td id="store_td">
							<?
							echo create_drop_down( "cbo_store_name", 100, $blank_array,"", 1, "-- All Store --", $storeName, "",1 );
							?>
						</td>
						<td>
							<?
							$get_upto=array(1=>"Greater Than",2=>"Less Than",3=>"Greater/Equal",4=>"Less/Equal",5=>"Equal");
							echo create_drop_down( "cbo_get_upto", 70, $get_upto,"", 1, "- All -", 0, "",0 );
							?>
						</td>
						<td>
							<input type="text" id="txt_days" name="txt_days" class="text_boxes_numeric" style="width:30px" value="" />
						</td>
						<td>
							<?
							echo create_drop_down( "cbo_get_upto_qnty", 70, $get_upto,"", 1, "- All -", 0, "",0 );
							?>
						</td>
						<td>
							<input type="text" id="txt_qnty" name="txt_qnty" class="text_boxes_numeric" style="width:30px" value="" />
						</td>

						<td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" /></td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="15" align="center"><? echo load_month_buttons(1);  ?></td>
					</tr>
				</tfoot>
			</table>
		</fieldset>
	</div>
</div>
<div id="report_container" align="center"></div>
<div id="report_container2" align="left"></div>
</form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">set_multiselect('cbo_buyer_id','0','0','0');</script>
</html>

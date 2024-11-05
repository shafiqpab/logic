<?php
header('Content-type:text/html; charset=utf-8');
session_start();
$user_id = $_SESSION['logic_erp']['user_id'];
if ($user_id == "") header("location:login.php");
require_once('../../../includes/common.php');
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$permission = $_SESSION['page_permission'];

// get buyer condition according to priviledge
if ($_SESSION['logic_erp']["data_level_secured"] == 1)
{
	if ($_SESSION['logic_erp']["buyer_id"] != "")
	{
		$buyer_id_cond = " and buy.id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
	}
	else
	{
		$buyer_id_cond = "";
	}
}
else
{
	$buyer_id_cond = "";
}

//actn_job_popup
if ($action == "actn_job_popup")
{
	echo load_html_head_contents("Style Reference / Job No. Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		/*function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				js_set_value(i);
			}
		}*/

		function toggle(x, origColor)
		{
			var newColor = 'yellow';
			if (x.style)
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str)
		{
			toggle(document.getElementById('search' + str), '#FFFFCC');
			if (jQuery.inArray($('#txt_job_id' + str).val(), selected_id) == -1)
			{
				selected_id.push($('#txt_job_id' + str).val());
				selected_name.push($('#txt_job_no' + str).val());
			}
			else
			{
				for (var i = 0; i < selected_id.length; i++)
				{
					if (selected_id[i] == $('#txt_job_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++)
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:440px;">
				<table width="420" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
				<thead>
					<th>PO Buyer</th>
					<th>Search By</th>
					<th id="search_by_td_up">Please Enter Sales Order No</th>
					<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
					<input type="hidden" name="hide_job_no" id="hide_job_no" value=""/>
					<input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
				</thead>
				<tbody>
					<tr>
						<td id="buyer_td">
							<?
							echo create_drop_down("cbo_po_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Sales Order No", 2 => "Style Ref");
							$dd = "change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down("cbo_search_by", 120, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:110px" class="text_boxes" name="txt_search_common" id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'actn_job_popup_listview', 'search_div', 'tube_ref_breakdown_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:90px;"/>
						</td>
					</tr>
				</tbody>
			</table>
			<div style="margin-top:10px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//actn_job_popup_listview
if ($action == "actn_job_popup_listview")
{
	$data = explode('**', $data);
	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	$company_id = $data[0];
	$buyer_id = $data[1];
	$po_buyer_id = $data[2];
	$within_group = $data[3];
	$search_by = $data[4];
	$search_string = trim($data[5]);

	$search_field_cond = '';
	if ($search_string != "")
	{
		if ($search_by == 1)
		{
			$search_field_cond = " AND C.JOB_NO LIKE '%".$search_string."'";
		}
		else
		{
			$search_field_cond = " AND LOWER(C.STYLE_REF_NO) LIKE LOWER('".$search_string."%')";
		}
	}

	//for po company
	$po_company_cond = '';
	if($within_group == 1 && $buyer_id != 0)
	{
		$po_company_cond = " AND C.PO_COMPANY_ID = ".$buyer_id;
	}
	
	//for po buyer
	$po_buyer_cond = '';
	if ($po_buyer_id == 0)
	{
		if ($_SESSION['logic_erp']["buyer_id"] != "")
		{
			$po_buyer_cond = " AND C.BUYER_ID IN(".$_SESSION['logic_erp']["buyer_id"].")";
		}
	}
	else
	{
		$po_buyer_cond = " AND C.PO_BUYER = ".$po_buyer_id;
	}

	$sql = "SELECT A.BOOKING_NO, A.WITHIN_GROUP, TO_CHAR(A.INSERT_DATE, 'YYYY') AS YEAR, C.ID, C.JOB_NO, C.STYLE_REF_NO, C.BUYER_ID, C.PO_BUYER, C.PO_COMPANY_ID FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B, FABRIC_SALES_ORDER_MST C WHERE A.ID = B.MST_ID AND A.BOOKING_NO = C.SALES_BOOKING_NO AND A.IS_SALES = 1 AND B.IS_SALES = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND A.COMPANY_ID = ".$company_id." AND A.WITHIN_GROUP = ".$within_group.$search_field_cond.$po_company_cond.$po_buyer_cond;
	
	/*
	if ($within_group == 1)
	{
		$sql = " select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no = b.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and within_group=$within_group $search_field_cond $po_buyer_id_cond and b.company_id=$buyer_id and fabric_source in(1,2)
		union all
		select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num from fabric_sales_order_mst a,wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls c where a.sales_booking_no = b.booking_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and within_group=$within_group $search_field_cond $po_buyer_id_cond  and b.company_id=$buyer_id and (b.fabric_source in(1,2) or c.fabric_source in(1,2)) group by a.id, a.insert_date, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id,b.booking_no_prefix_num,c.fabric_source";

	}
	else
	{
		$sql = " select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no booking_no_prefix_num, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  and within_group=$within_group $search_field_cond order by a.id";
	}
	*/
	//echo $sql; die;
	$sql_rslt = sql_select($sql);
	$data_arr = array();
	foreach($sql_rslt as $row)
	{
		$data_arr[$row['JOB_NO']]['ID'] = $row['ID'];
		$data_arr[$row['JOB_NO']]['YEAR'] = $row['YEAR'];
		$data_arr[$row['JOB_NO']]['WITHIN_GROUP'] = $yes_no[$row['WITHIN_GROUP']];
		$data_arr[$row['JOB_NO']]['BOOKING_NO'] = $row['BOOKING_NO'];
		$data_arr[$row['JOB_NO']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
		
		//for buyer
		if ($row['WITHIN_GROUP'] == 1)
		{
			$po_buyer = $buyer_arr[$row['PO_BUYER']];
			$po_company = $company_arr[$row['PO_COMPANY_ID']];
		}
		else
		{
			$po_buyer = $buyer_arr[$row['BUYER_ID']];
			$po_company = $buyer_arr[$row['BUYER_ID']];
		}
		$data_arr[$row['JOB_NO']]['PO_BUYER'] = $po_buyer;
		$data_arr[$row['JOB_NO']]['PO_COMPANY'] = $po_company;
		
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="120">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">PO Buyer</th>
			<th width="70">PO Company</th>
			<th width="120">Booking No</th>
			<th>Style Ref.</th>
		</thead>
	</table>
	<div style="width:700px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="680" class="rpt_table" id="tbl_list_search">
		<?
		$i = 0;
		foreach ($data_arr as $job_no=>$row)
		{
			$i++;
			if ($i % 2 == 0) $bgcolor = "#E9F3FF";
			else $bgcolor = "#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
				<td width="40"><? echo $i; ?>
                    <input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>" value="<? echo $row['ID']; ?>"/>
                    <input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $job_no; ?>"/>
				</td>
				<td width="120" align="center"><p><? echo $job_no; ?></p></td>
				<td width="60" align="center"><p><? echo $row['YEAR']; ?></p></td>
				<td width="80" align="center"><p><? echo $row['WITHIN_GROUP']; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $row['PO_BUYER']; ?>&nbsp;</p></td>
				<td width="70" align="center"><p><? echo $row['PO_COMPANY']; ?>&nbsp;</p></td>
				<td width="120" align="center"><p><? echo $row['BOOKING_NO']; ?></p></td>
				<td><p><? echo $row['STYLE_REF_NO']; ?></p></td>
			</tr>
			<?
        }
        ?>
    </table>
</div>
<!--<table width="700" cellspacing="0" cellpadding="0" style="border:none" align="center">
	<tr>
		<td align="center" height="30" valign="bottom">
			<div style="width:100%">
				<div style="width:50%; float:left" align="left">
					<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/> Check /
					Uncheck All
				</div>
				<div style="width:50%; float:left" align="left">
					<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton"
					value="Close" style="width:100px"/>
				</div>
			</div>
		</td>
	</tr>
</table>-->
<?
exit();
}

if ($action == "booking_no_search_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(booking_no)
		{
			$('#hidden_booking_no').val(booking_no);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:750px;">
		<form name="searchwofrm" id="searchwofrm" autocomplete=off>
			<fieldset style="width:100%;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="835" class="rpt_table" border="1" rules="all">
					<thead>
						<th>Po Buyer</th>
						<th>Booking Date</th>
						<th>Booking Type</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="150">Please Enter Booking No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:90px"
							class="formbutton"/>
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
							value="<? echo $companyID; ?>">
							<input type="hidden" name="cbo_within_group" id="cbo_within_group" class="text_boxes"
							value="<? echo $cbo_within_group; ?>">
							<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes"
							value="">
						</th>
					</thead>
					<tr>
						<td align="center">
							<?
							echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
							?>
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
							readonly>
						</td>
						<td align="center">
							<?
							$booking_type_arr = array(1 => "Fabric Booking", 2 => "Sample Booking");
							echo create_drop_down("cbo_booking_type", 100, $booking_type_arr, "", 0, '', '', '');
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Booking No", 2 => "Job No");
							$dd = "change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_booking_type').value, 'create_booking_search_list_view', 'search_div', 'tube_ref_breakdown_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:90px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="6" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px; margin-left:3px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_booking_search_list_view")
{
	$data = explode("_", $data);
	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$buyer_id = $data[3];
	$date_from = trim($data[4]);
	$date_to = trim($data[5]);
	$cbo_within_group = trim($data[6]);
	$booking_type = trim($data[7]);

	if ($buyer_id == 0)
	{
		$buyer_id_cond = "";
	}
	else
	{
		$buyer_id_cond = " and a.buyer_id=$buyer_id";
	}

	$search_field_cond = "";
	$search_field_cond_2 = "";

	if (trim($data[0]) != "")
	{
		if ($search_by == 1)
		{
			if ($cbo_within_group == 1)
			{
				$search_field_cond = "and a.booking_no like '$search_string'";
				$search_field_cond_2 = "and a.booking_no like '$search_string'";
			}
			else
			{
				$search_field_cond = "and c.sales_booking_no like '$search_string'";
				$search_field_cond_2 = "and b.sales_booking_no like '$search_string'";
			}
		}
		else
		{
			$search_field_cond = "and a.job_no like '$search_string'";
		}
	}

	$date_cond = '';
	if ($cbo_within_group == 1)
	{

	}
	$date_field = ($cbo_within_group == 2) ? "c.booking_date" : "a.booking_date";
	if ($date_from != "" && $date_to != "")
	{
		if ($db_type == 0)
		{
			$date_cond = "and $date_field between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		}
		else
		{
			$date_cond = "and $date_field between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	if ($cbo_within_group == 1)
	{
		//for fabric booking
		if($booking_type == 1)
		{
			$sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, b.job_no, b.style_ref_no from wo_booking_mst a, wo_po_details_master b, fabric_sales_order_mst c where a.job_no=b.job_no and a.booking_no=c.sales_booking_no and a.supplier_id=$company_id and a.pay_mode=5 and a.fabric_source in(1,2) and a.status_active =1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, b.job_no,b.style_ref_no";
		}
		//for sample booking
		else
		{
			$sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.job_no, d.style_ref_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, fabric_sales_order_mst c, sample_development_mst d where a.booking_no=b.booking_no and a.booking_no=c.sales_booking_no and b.style_id = d.id and a.supplier_id=$company_id and a.pay_mode=5 and a.fabric_source in(1,2) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.job_no, d.style_ref_no";
		}
	}
	else
	{
		$sql = "select c.id, c.sales_booking_no booking_no, c.booking_date,c.buyer_id, c.company_id,c.job_no, c.style_ref_no from fabric_sales_order_mst c where c.company_id=$company_id and c.status_active =1 and c.is_deleted=0 $date_cond $search_field_cond and c.within_group=2 group by c.id, c.sales_booking_no, c.booking_date, c.buyer_id, c.company_id, c.job_no, c.style_ref_no";
	}
	//echo $sql;
	
	$result = sql_select($sql);
	$poArr = array();
	$buyerArr = array();
	foreach ($result as $row)
	{
		//for buyer
		$buyerArr[$row[csf('buyer_id')]] = $row[csf('buyer_id')];
		
		//for po
		if ($row[csf('po_break_down_id')] != "")
		{
			$po_ids = explode(",", $row[csf('po_break_down_id')]);
			foreach ($po_ids as $po_id)
			{
				$poArr[$po_id] = $po_id;
			}
		}
	}
	
	//for partial
	if($db_type==0)
	{
		$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, group_concat(c.po_break_down_id) as po_break_down_id, c.job_no from wo_booking_mst a, wo_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond and a.entry_form=108 group by a.id, a.booking_no,a.booking_date,a.buyer_id,a.company_id,a.delivery_date,a.currency_id,c.job_no";
	}
	else
	{
		//for fabric booking
		if($booking_type == 1)
		{
			$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, c.job_no from wo_booking_mst a, wo_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond and a.entry_form=108 group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, c.job_no";
		}
		//for sample booking
		else
		{
			$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, c.job_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond and a.entry_form=108 group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, c.job_no";
		}
	}
	//echo $sql_partial;
	$result_partial = sql_select($sql_partial);
	foreach ($result_partial as $row)
	{
		//for buyer
		$buyerArr[$row[csf('buyer_id')]] = $row[csf('buyer_id')];
		
		//for po
		if ($row[csf('po_break_down_id')] != "")
		{
			$po_ids = explode(",", $row[csf('po_break_down_id')]);
			foreach ($po_ids as $po_id)
			{
				$poArr[$po_id] = $po_id;
			}
		}
	}
	//echo "<pre>";
	//print_r($buyerArr);
	
	//for company details
	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	
	//for buyer details
	//$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$buyer_arr = array();
	if(!empty($buyerArr))
	{
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where 1=1".where_con_using_array($buyerArr,0,'id'), "id", "buyer_name");
	}
	
	//for buyer details
	//$po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "buyer_name");
	$po_arr = array();
	if(!empty($poArr))
	{
		$po_arr = return_library_array("select id, po_number from wo_po_break_down where 1=1".where_con_using_array($poArr,0,'id'), "id", "buyer_name");
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="80">PO Buyer</th>
			<th width="120">Booking No</th>
			<th width="90">Job No</th>
			<th width="120">Style Ref.</th>
			<th width="80">Booking Date</th>
			<th>PO No.</th>
		</thead>
	</table>
	<div style="width:740px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">
		<?
		$i = 1;
		$j = 1;
		foreach ($result as $row)
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			if ($row[csf('po_break_down_id')] != "")
			{
				$po_no = '';
				$po_ids = explode(",", $row[csf('po_break_down_id')]);
				foreach ($po_ids as $po_id)
				{
					if ($po_no == "")
						$po_no = $po_arr[$po_id];
					else
						$po_no .= "," . $po_arr[$po_id];
				}
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>')">
				<td width="40"><? echo $i; ?></td>
				<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				<td><p><? echo $po_no; ?>&nbsp;</p></td>
			</tr>
			<?
			$i++;
		}

		//for partial
		foreach ($result_partial as $row)
		{
			if ($j % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			if ($row[csf('po_break_down_id')] != "")
			{
				$po_no = '';
				$po_ids = array_unique(explode(",", $row[csf('po_break_down_id')]));
				foreach ($po_ids as $po_id)
				{
					if ($po_no == "")
						$po_no = $po_arr[$po_id];
					else
						$po_no .= "," . $po_arr[$po_id];
				}
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>')">
				<td width="40"><? echo $j; ?>p</td>
				<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				<td><p><? echo $po_no; ?>&nbsp;</p></td>
			</tr>
			<?
			$j++;
		}
		?>
	</table>
</div>
<?
exit();
}

//actn_show_details
if ($action == "actn_show_details")
{
	//echo 'su..re';
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$color_dtls = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	
	//var data = "action=actn_show_details" + get_submitted_data_string('cbo_company_name*cbo_within_group*cbo_buyer_name*txt_job_no*hide_job_id*txt_program_no*txt_date_from*txt_date_to', "../") + '&type=' + type;
	$company_name = str_replace("'", "", $cbo_company_name);
	$within_group = str_replace("'", "", $cbo_within_group);
	$buyer_name = str_replace("'", "", $cbo_buyer_name);
	$sales_order_no = str_replace("'", "", $txt_job_no);
	$sales_order_id = str_replace("'", "", $hide_job_id);
	$booking_no = str_replace("'", "", $txt_booking_no);
	$batch_no = str_replace("'", "", $txt_batch_no);
	$program_no = str_replace("'", "", $txt_program_no);
	$date_from = str_replace("'", "", $txt_date_from);
	$date_to = str_replace("'", "", $txt_date_to);
	
	//for sales order no
	$sales_order_cond = '';
	if($sales_order_id != '')
	{
		$sales_order_cond = " AND D.ID = ".$sales_order_id;
	}
	
	//for booking no
	$booking_no_cond = '';
	if($booking_no != '')
	{
		$booking_no_cond = " AND A.BOOKING_NO = '".$booking_no."'";
	}
	
	//for batch no
	$batch_no_cond = '';
	if($batch_no != '')
	{
		$batch_no_cond = " AND E.BATCH_NO = '".$batch_no."'";
	}
	
	//for program no
	$program_no_cond = '';
	if($program_no != '')
	{
		$program_no_cond = " AND B.ID = ".$program_no;
	}
	
	//for po company
	$po_company_cond = '';
	if($within_group == 1)
	{
		$po_company_cond = " AND D.PO_COMPANY_ID = ".$buyer_name;
	}
	
	//for program date
	$program_date_cond = '';
	if($date_from != '' && $date_to != '')
	{
		$program_date_cond = " AND E.PLANNED_DATE BETWEEN '".change_date_format(trim($date_from), '', '', 1)."' AND '".change_date_format(trim($date_to), '', '', 1)."'";
	}

	$sql = "SELECT A.BOOKING_NO, B.ID AS PROG_NO, B.PROGRAM_DATE, B.COLOR_ID, C.ID, C.BODY_PART_ID, C.FABRIC_DESC, C.GSM_WEIGHT, C.DIA, C.WIDTH_DIA_TYPE, C.COLOR_TYPE_ID, C.PROGRAM_QNTY, D.ID AS FSO_ID, D.JOB_NO, D.WITHIN_GROUP, E.PLANNED_DATE, E.BATCH_NO, E.MACHINE_ID, E.MACHINE_CAPACITY, E.MACHINE_EFFICIENCY, E.NO_OF_TUBE, E.CAPACITY_PER_TUBE, E.REFERENCE_NO, E.REFERENCE_QTY, E.ID as REF_ID
	FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, FABRIC_SALES_ORDER_MST D, PPL_REFERENCE_CREATION E 
	WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND B.MST_ID = C.MST_ID AND A.BOOKING_NO = D.SALES_BOOKING_NO AND B.ID = E.PROGRAM_NO AND C.DTLS_ID = E.PROGRAM_NO AND A.IS_SALES = 1 AND B.IS_SALES = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND D.STATUS_ACTIVE = 1 AND D.IS_DELETED = 0 AND E.STATUS_ACTIVE = 1 AND E.IS_DELETED = 0 AND A.COMPANY_ID = ".$company_name." AND A.WITHIN_GROUP = ".$within_group.$sales_order_cond.$booking_no_cond.$batch_no_cond.$program_no_cond.$po_company_cond.$program_date_cond;
	//echo $sql; die;
	$sql_rslt = sql_select($sql);
	$data_arr = array();
	$prog_no_arr = array();
	$booking_no_arr = array();
	$duplicate_check = array();
	$batch_tot_arr = array();
	$row_span_arr = array();
	foreach($sql_rslt as $row)
	{
		if($duplicate_check[$row['ID']][$row['BATCH_NO']] != $row['ID'])
		{
			$duplicate_check[$row['ID']][$row['BATCH_NO']] = $row['ID'];
			$prog_no_arr[$row['PROG_NO']] = $row['PROG_NO'];
			$booking_no_arr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
			$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['PROGRAM_QNTY'] += $row['PROGRAM_QNTY'];
			$row_span_arr[$row['BATCH_NO']][$row['JOB_NO']]++;
		}
			
		$exp_clr = array();
		$exp_clr = explode(',', $row['COLOR_ID']);
		$clr = array();
		foreach($exp_clr as $key=>$val)
		{
			$clr[$val] = $color_dtls[$val];
		}
		
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['PROG_NO'] = $row['PROG_NO'];
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['BOOKING_NO'] = $row['BOOKING_NO'];
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['PLANNED_DATE'] = date('d-m-Y', strtotime($row['PLANNED_DATE']));
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['JOB_NO'] = $row['JOB_NO'];
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['BODY_PART_ID'] = $body_part[$row['BODY_PART_ID']];
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['COLOR'] = implode(', ', $clr);
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['COLOR_TYPE_ID'] = $color_type[$row['COLOR_TYPE_ID']];
		
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['GSM_WEIGHT'] = $row['GSM_WEIGHT'];
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['DIA'] = $row['DIA'];
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['WIDTH_DIA_TYPE'] = $fabric_typee[$row['WIDTH_DIA_TYPE']];
		
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['MACHINE_ID'] = $row['MACHINE_ID'];
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['MACHINE_CAPACITY'] = $row['MACHINE_CAPACITY'];
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['MACHINE_EFFICIENCY'] = $row['MACHINE_EFFICIENCY'];
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['NO_OF_TUBE'] = $row['NO_OF_TUBE'];
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['CAPACITY_PER_TUBE'] = $row['CAPACITY_PER_TUBE'];
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['FSO_ID'] = $row['FSO_ID'];
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['WITHIN_GROUP'] = $row['WITHIN_GROUP'];
		
		$exp_ref = array();
		$exp_ref = explode('-', $row['REFERENCE_NO']);
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['REFERENCE_NO'][$exp_ref[3]] = $row['REFERENCE_QTY'];
		$data_arr[$row['BATCH_NO']][$row['JOB_NO']][$row['PROG_NO']]['REFERENCE_ID'][$exp_ref[3]] = $row['REF_ID'];
		$batch_tot_arr[$row['BATCH_NO']] += $row['REFERENCE_QTY'];
	}
	/*echo "<pre>";
	print_r($data_arr);
	echo "</pre>";*/
	
	$con = connect();
	execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID = ".$user_id);
	oci_commit($con);
	
	//for product id
	$con = connect();
	foreach($booking_no_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_BOOKING_ID(BOOKING_NO, USERID) VALUES('".$val."', '".$user_id."')");
	}
	oci_commit($con);
	
	$sql_booking = "SELECT A.ID, A.BOOKING_NO, A.COMPANY_ID, A.PO_BREAK_DOWN_ID, A.ITEM_CATEGORY, A.FABRIC_SOURCE, A.JOB_NO, A.ENTRY_FORM, A.IS_APPROVED
	FROM WO_BOOKING_MST A, TMP_BOOKING_ID B WHERE A.BOOKING_NO = B.BOOKING_NO AND B.USERID=".$user_id." AND A.BOOKING_TYPE IN(1,4) AND A.IS_SHORT IN(1,2) 
	AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
	UNION ALL 
	SELECT A.ID, A.BOOKING_NO, A.COMPANY_ID, NULL AS PO_BREAK_DOWN_ID, A.ITEM_CATEGORY, A.FABRIC_SOURCE, A.JOB_NO, NULL AS ENTRY_FORM, A.IS_APPROVED
	FROM WO_NON_ORD_SAMP_BOOKING_MST A, TMP_BOOKING_ID B WHERE A.BOOKING_NO = B.BOOKING_NO AND B.USERID=".$user_id." 
	AND A.BOOKING_TYPE IN(4) AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0";
    //echo $sql_booking;
	$sql_booking_rslt = sql_select($sql_booking);
    $booking_Arr=array();
    foreach($sql_booking_rslt as $row)
    {
        $booking_Arr[$row['BOOKING_NO']]['id'] = $row['ID'];
        $booking_Arr[$row['BOOKING_NO']]['booking_company_id'] = $row['COMPANY_ID'];
        $booking_Arr[$row['BOOKING_NO']]['booking_entry_form'] = $row['ENTRY_FORM'];
        $booking_Arr[$row['BOOKING_NO']]['booking_order_id'] = $row['PO_BREAK_DOWN_ID'];
        $booking_Arr[$row['BOOKING_NO']]['booking_fabric_natu'] = $row['ITEM_CATEGORY'];
        $booking_Arr[$row['BOOKING_NO']]['booking_fabric_source'] = $row['FABRIC_SOURCE'];
        $booking_Arr[$row['BOOKING_NO']]['booking_job_no'] = $row['JOB_NO'];
        $booking_Arr[$row['BOOKING_NO']]['is_approved'] = $row['IS_APPROVED'];
    }
	
	//for ref qty
	$sql_ref = "SELECT PROGRAM_NO, REFERENCE_QTY FROM PPL_REFERENCE_CREATION WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0";
	//echo $sql_ref;
	$sql_ref_rslt = sql_select($sql_ref);
	$ref_data_arr = array();
	foreach($sql_ref_rslt as $row)
	{
		$ref_data_arr[$row['PROGRAM_NO']]['REF_QTY'] += $row['REFERENCE_QTY'];
	}
	
	//for rpt template
	$sql_rpt_tmplt = sql_select("select format_id, template_name, report_id from lib_report_template where module_id=2 and status_active=1 and is_deleted=0 and report_id in(1,2,3,4)");
	$rpt_tmplt_arr = array();
	foreach($sql_rpt_tmplt as $trow)
	{
		$exp_frmt = array();
		$exp_frmt = explode(",", $trow[csf('format_id')]);
		if($trow[csf('report_id')] == 1)
		{
			$rpt_tmplt_arr[$trow[csf('template_name')]][1] = $exp_frmt[0];
		}
		elseif($trow[csf('report_id')] == 2)
		{
			$rpt_tmplt_arr[$trow[csf('template_name')]][2] = $exp_frmt[0];
		}
		elseif ($trow[csf('report_id')] == 3) 
		{
			$rpt_tmplt_arr[$trow[csf('template_name')]][3] = $exp_frmt[0];
		}
		elseif ($trow[csf('report_id')] == 4) 
		{
			$rpt_tmplt_arr[$trow[csf('template_name')]][4] = $exp_frmt[0];
		}
	}
	
	$tbl_width = 2000;
	ob_start();
	?>
    <div>
        <table width="<? echo $tbl_width;?>px" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
            <thead>
                <tr>
                    <th width="40" rowspan="2">SL</th>
                    <th width="120" rowspan="2">Batch No</th>
                    <th width="100" rowspan="2">Batch Total Qty</th>
                    <th width="120" rowspan="2">Booking No</th>
                    <th width="120" rowspan="2">Sales Order No</th>
                    <th width="70" rowspan="2">Planned Date</th>
                    <th width="70" rowspan="2">Prog. No</th>
                    <th width="100" rowspan="2">Body Part</th>
                    <th width="150" rowspan="2">Fabric Desc.</th>
                    <th width="60" rowspan="2">Gsm</th>
                    <th width="60" rowspan="2">Dia</th>
                    <th width="70" rowspan="2">Color Type</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="70" rowspan="2">M/C No</th>
                    <th width="70" rowspan="2">Capacity</th>
                    <th width="70" rowspan="2">Effi%</th>
                    <th width="70" rowspan="2">No. of Tube</th>
                    <th width="70" rowspan="2">Capacity Per Tube/Ref.</th>
                    <th width="70" rowspan="2">Fab. Qnty</th>
                    <th width="240" colspan="4">Tube/Ref. No</th>
                    <th width="70" rowspan="2">Prog. Qnty</th>
                    <th rowspan="2">Balance Qnty</th>
                </tr>
                <tr>
                    <th width="60">R1</th>
                    <th width="60">R2</th>
                    <th width="60">R3</th>
                    <th width="60">R4</th>
                </tr>
            </thead>
        </table>
        <div style="width:2020px; overflow-y:scroll; max-height:220px" id="scroll_body" >
            <table width="<? echo $tbl_width;?>px" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
                <tbody>
                <?
                $sl = 0;
                $i = 0;
                foreach($data_arr as $k_batch_no=>$v_batch_no)
                {
                    foreach($v_batch_no as $k_sales_no=>$v_sales_no)
                    {
                        $j = 1;
                        foreach($v_sales_no as $k_prog_no=>$row)
                        {
                            $i++;
							if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";
                            //$bgcolor = "#FFFFFF";
                                
                            $row['BATCH_TOT_QTY'] = $batch_tot_arr[$k_batch_no];
                            $row['FAB_QNTY'] = array_sum($row['REFERENCE_NO']);
                            $row['BALANCE_QTY'] = decimal_format($row['PROGRAM_QNTY'], '1', '') - decimal_format($row['FAB_QNTY'], '1', '');
						    
                           	$fso_no = $row['JOB_NO'];
                           	$fso_booking_no = $row['BOOKING_NO'];
							$row["BOOKING_ID"] = $booking_Arr[$fso_booking_no]['id'];
							$booking_company = $booking_Arr[$fso_booking_no]['booking_company_id'];
							$booking_entry_form = $booking_Arr[$fso_booking_no]['booking_entry_form'];
							$booking_order_id = $booking_Arr[$fso_booking_no]['booking_order_id'];
							$booking_fabric_natu = $booking_Arr[$fso_booking_no]['booking_fabric_natu'];
							$booking_fabric_source = $booking_Arr[$fso_booking_no]['booking_fabric_source'];
							$booking_job_no = $booking_Arr[$fso_booking_no]['booking_job_no'];
							$is_approved_id = $booking_Arr[$fso_booking_no]['is_approved'];
							
							$booking=array();
							$booking=explode('-', $fso_booking_no);
							// Budget Wise Fabric Booking and Main Fabric Booking V2
							$fReportId2 = $rpt_tmplt_arr[$booking_company][1];
	
							// Short Fabric Booking
							$fReportId3 = $rpt_tmplt_arr[$booking_company][2];
							
							// Sample with order Booking
							$fReportId4 = $rpt_tmplt_arr[$booking_company][3];
	
							// Sample without order Booking
							$fReportId5 = $rpt_tmplt_arr[$booking_company][4];
	
							if ($booking_entry_form==86 || $booking_entry_form==118) 
							{// Budget Wise Fabric Booking and Main Fabric Booking V2
								$fbReportId=$fReportId2;
							}
							else if($booking_entry_form==88)
							{
								$fbReportId=$fReportId3;// Short Fabric Booking
							}
							else if($booking_entry_form=="" && $booking[1]=="SM")
							{
								$booking_entry_form="SM";
								$fbReportId=$fReportId4;// Sample with order Booking
							}
							else if($booking_entry_form=="" && $booking[1]=="SMN")
							{
								$booking_entry_form="SMN";
								$fbReportId=$fReportId5;// Sample without order Booking
							}
							
						    $rspan = $row_span_arr[$k_batch_no][$k_sales_no];					
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" valign="middle" id="tr_<?= $i; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" style="cursor:pointer;">
                                <?
                                if($j == 1)
                                {
                                    $sl++;
                                    $j++;
                                    ?>
                                    <td width="40" align="center" rowspan="<? echo $rspan; ?>"><? echo $sl; ?></td>
                                    <td width="120" align="center" rowspan="<? echo $rspan; ?>"><p><? echo $k_batch_no; ?></p></td>
                                    <td width="100" align="center" rowspan="<? echo $rspan; ?>"><p><? echo decimal_format($row['BATCH_TOT_QTY'], '1', ','); ?></p></td>
									<? if ($row['WITHIN_GROUP'] == 1)
                                    {
                                        ?>
                                        <td width="120" title="Booking Entry Form:<? echo $booking_entry_form;?>" style="vertical-align: middle;" align="center" rowspan="<? echo $rspan;?>"><p><? echo "<a href='##' onclick=\"generate_booking_report('".$sale_booking_no."',".$booking_company.",'".$booking_order_id."',".$booking_fabric_natu.",".$booking_fabric_source.",".$is_approved_id.",'".$booking_job_no."','".$booking_entry_form."','".$fbReportId."' )\">$fso_booking_no</a>"; ?>&nbsp;</p></td>
                                        <?
                                    }
                                    else
                                    {
                                        ?>
                                    	<td width="120" align="center" rowspan="<? echo $rspan; ?>"><p><? echo $row['BOOKING_NO']; ?></p></td>
                                        <?
                                    }
									?>	            	
                                    <td width="120" align="center" rowspan="<? echo $rspan; ?>"><p><? echo "<a href='##' onclick=\"generate_fso_report($company_name, '".$row["BOOKING_ID"]."','".$row['BOOKING_NO']."','".$row["JOB_NO"]."', '".$row["FSO_ID"]."' )\">$fso_no</a>"; ?>&nbsp;</p></td>
                                    <td width="70" align="center" rowspan="<? echo $rspan; ?>"><p><? echo $row['PLANNED_DATE']; ?></p></td>
                                    <?
                                }
                                ?>
                                <td width="70" align="center" id="program_no_<? echo $sl; ?>"><? echo $row['PROG_NO']; ?></td>
                                <td width="100" align="center"><p><? echo $row['BODY_PART_ID']; ?></p></td>
                                <td width="150" align="left"><p><? echo $row['FABRIC_DESC']; ?></p></td>
                                <td width="60" align="center"><? echo $row['GSM_WEIGHT']; ?></td>
                                <td width="60" align="center"><p><? echo $row['DIA']; ?></p></td>
                                <td width="70" align="center"><p><? echo $row['COLOR_TYPE_ID']; ?></p></td>
                                <td width="100" align="center"><p><? echo $row['COLOR']; ?></p></td>
                                <td width="70" align="center"><p><? echo $machine_arr[$row['MACHINE_ID']]; ?></p></td>
                                <td width="70" align="center"><p><? echo $row['MACHINE_CAPACITY']; ?></p></td>
                                <td width="70" align="center"><p><? echo $row['MACHINE_EFFICIENCY']; ?></p></td>
                                <td width="70" align="center"><p><? echo $row['NO_OF_TUBE']; ?></p></td>
                                <td width="70" align="center"><p><? echo decimal_format($row['CAPACITY_PER_TUBE'], '1', ','); ?></p></td>
                                <td width="70" align="right"><p><? echo "<a href='##' onclick=\"func_fab_qty('".$k_batch_no."', '".$row["PROG_NO"]."', ".$row['MACHINE_ID'].")\">".decimal_format($row['FAB_QNTY'], '1', ',')."</a>"; ?>&nbsp;</p></td>
                                <td width="60" align="right" title="<? echo "reference id : ".$row['REFERENCE_ID']['R1'];?>"><p>
								<?
								$ref_r1 = decimal_format($row['REFERENCE_NO']['R1'], '1', ',');
								$ref_mst_id = $row['REFERENCE_ID']['R1'];
								echo "<a href='##' onclick=\"generate_ref_report($ref_mst_id )\">$ref_r1</a>"; 
								 //echo decimal_format($row['REFERENCE_NO']['R1'], '1', ',');
								  ?>&nbsp;
								</p></td>
                                <td width="60" align="right" title="<? echo $row['REFERENCE_ID']['R2'];?>"><p>
									<? 
									$ref_r2 = decimal_format($row['REFERENCE_NO']['R2'], '1', ',');
									$ref_mst_id = $row['REFERENCE_ID']['R2'];
									echo "<a href='##' onclick=\"generate_ref_report($ref_mst_id )\">$ref_r2</a>"; 
									//echo decimal_format($row['REFERENCE_NO']['R2'], '1', ','); 
									?>
								</p></td>
                                <td width="60" align="right" title="<? echo $row['REFERENCE_ID']['R3'];?>"><p>
									<? 
									$ref_r3 = decimal_format($row['REFERENCE_NO']['R3'], '1', ',');
									$ref_mst_id = $row['REFERENCE_ID']['R3'];
									echo "<a href='##' onclick=\"generate_ref_report($ref_mst_id )\">$ref_r3</a>"; 
									//echo decimal_format($row['REFERENCE_NO']['R3'], '1', ','); 
									?>
								</p></td>
                                <td width="60" align="right" title="<? echo $row['REFERENCE_ID']['R4'];?>"><p>
									<?
									$ref_r4 = decimal_format($row['REFERENCE_NO']['R4'], '1', ',');
									$ref_mst_id = $row['REFERENCE_ID']['R4'];
									echo "<a href='##' onclick=\"generate_ref_report($ref_mst_id )\">$ref_r4</a>"; 
									// echo decimal_format($row['REFERENCE_NO']['R4'], '1', ','); 
									?>
								</p></td>
                                <td width="70" align="right"><? echo decimal_format($row['PROGRAM_QNTY'], '1', ','); ?></td>
                                <td align="right"><? echo decimal_format($row['BALANCE_QTY'], '1', ','); ?></td>
                            </tr>
                            <?
                            $tot_fab_qty += decimal_format($row['FAB_QNTY'], '1', '');
                            $tot_r1_qty += decimal_format($row['REFERENCE_NO']['R1'], '1', '');
                            $tot_r2_qty += decimal_format($row['REFERENCE_NO']['R2'], '1', '');
                            $tot_r3_qty += decimal_format($row['REFERENCE_NO']['R3'], '1', '');
                            $tot_r4_qty += decimal_format($row['REFERENCE_NO']['R4'], '1', '');
                        }
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <table width="<? echo $tbl_width;?>px" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
            <tfoot>
                <tr>
                    <th width="42">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="150">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">Total</th>
                    <th width="70" align="right"><? echo decimal_format($tot_fab_qty, '1', ','); ?></th>
                    <th width="60" align="right"><? echo decimal_format($tot_r1_qty, '1', ','); ?></th>
                    <th width="60" align="right"><? echo decimal_format($tot_r2_qty, '1', ','); ?></th>
                    <th width="60" align="right"><? echo decimal_format($tot_r3_qty, '1', ','); ?></th>
                    <th width="60" align="right"><? echo decimal_format($tot_r4_qty, '1', ','); ?></th>
                    <th width="70">&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) 
	{		
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w+');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename";
	exit();
}

if($action == 'actn_fab_qty')
{
	echo load_html_head_contents("Fab Qty", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$color_dtls = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	
	$sql = "SELECT B.COLOR_ID, C.BODY_PART_ID, 
	E.REFERENCE_NO, E.REFERENCE_QTY 
	FROM PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, PPL_REFERENCE_CREATION E 
	WHERE B.ID = C.DTLS_ID 
	AND B.MST_ID = C.MST_ID AND B.ID = E.PROGRAM_NO AND C.DTLS_ID = E.PROGRAM_NO AND B.IS_SALES = 1 
	AND B.STATUS_ACTIVE = 1 
	AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND E.STATUS_ACTIVE = 1 AND E.IS_DELETED = 0 
	AND B.ID = ".$prog_no." AND E.BATCH_NO = '".$batch_no."' AND E.MACHINE_ID = ".$machine_id;
	//echo $sql;
	$sql_rslt = sql_select($sql);
	$pdata_arr = array();
	$batch_qty = 0;
	foreach($sql_rslt as $row)
	{
		$exp_clr = array();
		$exp_clr = explode(',', $row['COLOR_ID']);
		$clr = array();
		foreach($exp_clr as $key=>$val)
		{
			$clr[$val] = $color_dtls[$val];
		}
		
		$batch_qty += decimal_format($row['REFERENCE_QTY'], '1', '');
		$pdata_arr[$row['BODY_PART_ID']][$row['REFERENCE_NO']]['COLOR_ID'] = implode(', ', $clr);
		$pdata_arr[$row['BODY_PART_ID']][$row['REFERENCE_NO']]['REFERENCE_QTY'] = $row['REFERENCE_QTY'];
	}
	
	//for production qty
	$sql_prod = "SELECT B.TUBE_REF_NO, B.QC_PASS_QNTY
	FROM INV_RECEIVE_MASTER A, PRO_ROLL_DETAILS B
	WHERE A.ID = B.MST_ID AND A.BOOKING_NO = B.BOOKING_NO AND A.ENTRY_FORM = 2 AND A.ITEM_CATEGORY = 13 AND A.RECEIVE_BASIS = 2 AND A.BOOKING_NO = '".$prog_no."' AND B.BOOKING_NO = '".$prog_no."' AND BATCH_NO = '".$batch_no."' AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0";
	$sql_prod_rslt = sql_select($sql_prod);
	$prod_qty_arr = array();
	foreach($sql_prod_rslt as $row)
	{
		$prod_qty_arr[$row['TUBE_REF_NO']]['PRODUCTION_QTY'] += $row['QC_PASS_QNTY'];
	}
	
	?>
    <table width="610" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
    	<thead>
        	<tr>
            	<th colspan="3" style="text-align:left; padding-left:5px;">Batch No. : <? echo $batch_no; ?></th>
                <th colspan="4" style="text-align:left; padding-left:5px;">Batch Weight [kg] : <? echo decimal_format($batch_qty, '1', ','); ?></th>
            </tr>
        	<tr>
            	<th width="30">Sl</th>
            	<th width="100">Body Part</th>
            	<th width="120">Color</th>
            	<th width="120">Tube/Ref. No</th>
            	<th width="80">Tube/Ref. Qty</th>
            	<th width="80">Grey Prod. Qty</th>
            	<th width="80">Grey Balance</th>
            </tr>
        </thead>
        <tbody>
        	<?
			$sl = 0;
			foreach($pdata_arr as $k_body_part=>$v_body_part)
			{
				foreach($v_body_part as $k_ref_no=>$row)
				{
					$sl++;
					$row['PRODUCTION_QTY'] = $prod_qty_arr[$k_ref_no]['PRODUCTION_QTY'];
					$row['BALANCE_QTY'] = decimal_format($row['REFERENCE_QTY'], '1', '') - $row['PRODUCTION_QTY'];
					?>
					<tr>
                    	<td align="center"><? echo $sl; ?></td>
                    	<td><p><? echo $body_part[$k_body_part]; ?></p></td>
                    	<td><p><? echo $row['COLOR_ID']; ?></p></td>
                    	<td><p><? echo $k_ref_no; ?></p></td>
                    	<td align="right"><? echo decimal_format($row['REFERENCE_QTY'], '1', ','); ?></td>
                    	<td align="right"><? echo decimal_format($row['PRODUCTION_QTY'], '1', ','); ?></td>
                    	<td align="right"><? echo decimal_format($row['BALANCE_QTY'], '1', ','); ?></td>
                    </tr>
					<?
					$tot_ref_qty += decimal_format($row['REFERENCE_QTY'], '1', '');
					$tot_prod_qty += decimal_format($row['PRODUCTION_QTY'], '1', '');
					$tot_bal_qty += decimal_format($row['BALANCE_QTY'], '1', '');
				}
			}
			?>
        </tbody>
        <tfoot>
        	<tr>
            	<th colspan="4">Total</th>
                <th><? echo decimal_format($tot_ref_qty, '1', ','); ?></th>
                <th><? echo decimal_format($tot_prod_qty, '1', ','); ?></th>
                <th><? echo decimal_format($tot_bal_qty, '1', ','); ?></th>
            </tr>
        </tfoot>
    </table>
    <?
	exit();
}
?>
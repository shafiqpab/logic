<?php
header('Content-type:text/html; charset=utf-8');
session_start();
$user_id = $_SESSION['logic_erp']['user_id'];
if ($user_id == "") header("location:login.php");
require_once('../../includes/common.php');
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
	echo load_html_head_contents("Style Reference / Job No. Info", "../../", 1, 1, '', '', '');
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
							$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 120, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:110px" class="text_boxes" name="txt_search_common" id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'actn_job_popup_listview', 'search_div', 'program_wise_ref_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:90px;"/>
						</td>
					</tr>
				</tbody>
			</table>
			<div style="margin-top:10px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
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
	echo load_html_head_contents("Booking Info", "../../", 1, 1, '', '', '');
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
							$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_booking_type').value, 'create_booking_search_list_view', 'search_div', 'program_wise_ref_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
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
	
	//var data = "action=actn_show_details" + get_submitted_data_string('cbo_company_name*cbo_within_group*cbo_buyer_name*txt_job_no*hide_job_id*txt_program_no*txt_date_from*txt_date_to', "../") + '&type=' + type;
	$company_name = str_replace("'", "", $cbo_company_name);
	$within_group = str_replace("'", "", $cbo_within_group);
	$buyer_name = str_replace("'", "", $cbo_buyer_name);
	$sales_order_no = str_replace("'", "", $txt_job_no);
	$sales_order_id = str_replace("'", "", $hide_job_id);
	$booking_no = str_replace("'", "", $txt_booking_no);
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
		$program_date_cond = " AND B.PROGRAM_DATE BETWEEN '".change_date_format(trim($date_from), '', '', 1)."' AND '".change_date_format(trim($date_to), '', '', 1)."'";
	}

	$sql = "SELECT A.BOOKING_NO, B.ID AS PROG_NO, B.PROGRAM_DATE, B.COLOR_ID, C.ID, C.BODY_PART_ID, C.FABRIC_DESC, C.GSM_WEIGHT, C.DIA, C.WIDTH_DIA_TYPE, C.COLOR_TYPE_ID, C.PROGRAM_QNTY, D.JOB_NO
	FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, FABRIC_SALES_ORDER_MST D 
	WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND B.MST_ID = C.MST_ID AND A.BOOKING_NO = D.SALES_BOOKING_NO AND A.IS_SALES = 1 AND B.IS_SALES = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND D.STATUS_ACTIVE = 1 AND D.IS_DELETED = 0 AND A.COMPANY_ID = ".$company_name." AND A.WITHIN_GROUP = ".$within_group.$sales_order_cond.$booking_no_cond.$program_no_cond.$po_company_cond.$program_date_cond;
	//echo $sql; die;
	$sql_rslt = sql_select($sql);
	$data_arr = array();
	$prog_no_arr = array();
	$duplicate_check = array();
	foreach($sql_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])
		{
			$duplicate_check[$row['ID']] = $row['ID'];
			$prog_no_arr[$row['PROG_NO']] = $row['PROG_NO'];
			
			$exp_clr = array();
			$exp_clr = explode(',', $row['COLOR_ID']);
			$clr = array();
			foreach($exp_clr as $key=>$val)
			{
				$clr[$val] = $color_dtls[$val];
			}
			
			
			$data_arr[$row['PROG_NO']]['PROG_NO'] = $row['PROG_NO'];
			$data_arr[$row['PROG_NO']]['BOOKING_NO'] = $row['BOOKING_NO'];
			$data_arr[$row['PROG_NO']]['PROGRAM_DATE'] = date('d-m-Y', strtotime($row['PROGRAM_DATE']));
			$data_arr[$row['PROG_NO']]['JOB_NO'] = $row['JOB_NO'];
			$data_arr[$row['PROG_NO']]['BODY_PART_ID'] = $body_part[$row['BODY_PART_ID']];
			$data_arr[$row['PROG_NO']]['COLOR'] = implode(', ', $clr);
			$data_arr[$row['PROG_NO']]['COLOR_TYPE_ID'] = $color_type[$row['COLOR_TYPE_ID']];
			
			$data_arr[$row['PROG_NO']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
			$data_arr[$row['PROG_NO']]['GSM_WEIGHT'] = $row['GSM_WEIGHT'];
			$data_arr[$row['PROG_NO']]['DIA'] = $row['DIA'];
			$data_arr[$row['PROG_NO']]['WIDTH_DIA_TYPE'] = $fabric_typee[$row['WIDTH_DIA_TYPE']];
			$data_arr[$row['PROG_NO']]['PROGRAM_QNTY'] += $row['PROGRAM_QNTY'];
		}
	}
	
	//for ref qty
	//$sql_ref = "SELECT PROGRAM_NO, REFERENCE_QTY FROM PPL_REFERENCE_CREATION WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND PROGRAM_NO IN(".implode(',',$prog_no_arr).")";
	$sql_ref = "SELECT PROGRAM_NO, REFERENCE_QTY FROM PPL_REFERENCE_CREATION WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0";
	//echo $sql_ref;
	$sql_ref_rslt = sql_select($sql_ref);
	$ref_data_arr = array();
	foreach($sql_ref_rslt as $row)
	{
		$ref_data_arr[$row['PROGRAM_NO']]['REF_QTY'] += $row['REFERENCE_QTY'];
	}
	
	$tbl_width = 1260;
	?>
    
	<form name="refCreation_2" id="refCreation_2">
        <div>
            <table width="<? echo $tbl_width;?>px" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="40">SL</th>
                    <th width="70">Prog. No</th>
                    <th width="120">Booking No</th>
                    <th width="70">Program Date</th>
                    <th width="120">Sales Order No</th>
                    <th width="100">Body Part</th>
                    <th width="100">Color</th>
                    <th width="70">Color Type</th>
                    <th width="150">Fabric Desc.</th>
                    <th width="60">Gsm</th>
                    <th width="60">Dia</th>
                    <th width="70">Width/Dia Type</th>
                    <th width="70">Prog. Qnty</th>
                    <th width="70">Ref. Qnty</th>
                    <th>Balance Qnty</th>
                </thead>
            </table>
            <div style="width:1280px; overflow-y:scroll; max-height:220px" id="scroll_body" >
                <table width="<? echo $tbl_width;?>px" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
                    <tbody>
                    <?
                    $sl = 0;
                    foreach($data_arr as $row)
                    {
                        $sl++;
                        if ($sl % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";
                            
                        $row['REF_QTY'] = $ref_data_arr[$row['PROG_NO']]['REF_QTY'];
                        $row['BALANCE_QTY'] = decimal_format($row['PROGRAM_QNTY'], '1', '') - decimal_format($row['REF_QTY'], '1', '');					
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" valign="middle" style="text-decoration:none;cursor:pointer;" onClick="selected_row('<? echo $sl; ?>')" id="tr_<? echo $sl; ?>">
                            <td width="40" align="center"><? echo $sl; ?></td>
                            <td width="70" align="center" id="program_no_<? echo $sl; ?>"><? echo $row['PROG_NO']; ?></td>
                            <td width="120" align="center"><? echo $row['BOOKING_NO']; ?></td>
                            <td width="70" align="center"><? echo $row['PROGRAM_DATE']; ?></td>
                            <td width="120" align="center"><? echo $row['JOB_NO']; ?></td>
                            <td width="100" align="center"><p><? echo $row['BODY_PART_ID']; ?></p></td>
                            <td width="100" align="center"><p><? echo $row['COLOR']; ?></p></td>
                            <td width="70" align="center"><p><? echo $row['COLOR_TYPE_ID']; ?></p></td>
                            <td width="150" align="left"><p><? echo $row['FABRIC_DESC']; ?></p></td>
                            <td width="60" align="center"><? echo $row['GSM_WEIGHT']; ?></td>
                            <td width="60" align="center"><p><? echo $row['DIA']; ?></p></td>
                            <td width="70" align="center"><? echo $row['WIDTH_DIA_TYPE']; ?></td>
                            <td width="70" align="right"><? echo decimal_format($row['PROGRAM_QNTY'], '1', ','); ?></td>
                            <td width="70" align="right"><? echo decimal_format($row['REF_QTY'], '1', ','); ?></td>
                            <td align="right"><? echo decimal_format($row['BALANCE_QTY'], '1', ','); ?></td>
                        </tr>
                        <?
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
	</form>
    <?
}

//for actn_tube_ref_popup
if ($action == "actn_tube_ref_popup") 
{
	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		var permission = '<? echo $permission; ?>';
		function add_break_down_tr(j, tr)
		{
			var i = $('#tbl_tube_ref tbody tr').length;
			i++;
			var index = $(tr).closest("tr").index();
			var tr = $("#tbl_tube_ref tbody tr:eq("+index+")");
			var cln = $("#tbl_tube_ref tbody tr:eq("+index+")").clone();
			var z = 0;
			cln.find('td').each(function(){
				if(z == 12)
				{
					$(this).removeAttr('id').attr('id','tdtuberefno_'+i)
				}
				
				if(z == 13)
				{
					$(this).removeAttr('id').attr('id','tdtuberefqty_'+i)
				}
				
				if(z == 14)
				{
					$(this).removeAttr('id').attr('id','tdincreasedecrease_'+i)
				}
				
				var el = $(this).find(':first-child');
				var id = el.attr('id') || null;
				if(id)
				{
					var id=id.split("_");
					el.attr('id', id[0] +"_"+ i);
					el.attr('name', id[0] +"_"+ i);
					if(z == 7 || z == 8 || z == 9 || z == 10 || z == 11 || z == 12 || z == 13 )
					{
						el.attr('value', '');
					}
				}
				z++;
			});
			cln.end();
			tr.after(cln);
			
			/*$('#txtbatchno_'+i).val('');
			$('#hdnmachineid_'+i).val('');
			$('#txtmachineno_'+i).val('');
			$('#txtcapacity_'+i).val('');
			$('#txtefficiency_'+i).val('');
			$('#txtnooftube_'+i).val('');
			$('#txtcapacitypertube_'+i).val('');*/
			
			$('#txtplanneddate_'+i).removeAttr("class").attr("class","datepicker");
			$('#txtmachineno_'+i).removeAttr("onClick").attr("onClick","func_machine_popup('"+i+"')");
			$('#txtcapacitypertube_'+i).removeAttr("onKeyUp").attr("onKeyUp","func_onkeyup_capacity_per_tube('"+i+"', this.value)");
			
			document.getElementById('tdtuberefno_'+i).innerHTML = "<input type='text' name='txttuberefno_"+i+"' id='txttuberefno_"+i+"' class='text_boxes' style='width:110px;text-align:center;' value='' readonly disabled />"+"<input type='hidden' name='txtupdateid_"+i+"' id='txtupdateid_"+i+"' class='text_boxes' style='width:60px;text-align:center;' value='' readonly disabled />";
			
			document.getElementById('tdtuberefqty_'+i).innerHTML = "<input type='text' name='txttuberefqty_"+i+"' id='txttuberefqty_"+i+"' class='text_boxes' style='width:60px;text-align:right;' value='' />"+"<input type='hidden' name='txtrolldata_"+i+"' id='txtrolldata_"+i+"' class='text_boxes' style='width:60px;text-align:center;' value='' readonly disabled />";
			
			document.getElementById('tdincreasedecrease_'+i).innerHTML = "<input type='button' name='increase_"+i+"' id='increase_"+i+"' class='formbutton' style='width:25px;' value='+' />"+"<input type='button' name='decrease_"+i+"' id='decrease_"+i+"' class='formbutton' style='width:25px;' value='-' />"+"<input type='hidden' name='hdnmachineid_"+i+"' id='hdnmachineid_"+i+"' class='text_boxes' style='width:60px;text-align:center;' value='' readonly disabled />"+"<input type='hidden' name='hdnisupdate_"+i+"' id='hdnisupdate_"+i+"' class='text_boxes' style='width:60px;text-align:center;' value='0' readonly disabled />"+"<input type='hidden' name='hdnisremove_"+i+"' id='hdnisremove_"+i+"' class='text_boxes' style='width:60px;text-align:center;' value='0' readonly disabled />";
			
			$('#increase_'+i).attr("onClick","add_break_down_tr('"+i+"',this);");
			$('#decrease_'+i).attr("onClick","fn_deletebreak_down_tr('"+i+"','tbl_tube_ref',this);");
			var batch_no = ($('#txtbatchno_'+i).val()).split('-');
			$('#txtbatchno_'+i).val(batch_no[0]+'-'+batch_no[1]+'-');
			set_all_onclick();
		}
		
		function fn_deletebreak_down_tr(rowNo, table_id, tr)
		{
			if($('#hdnisupdate_'+rowNo).val() == 0)
			{
				$('#hdnisremove_'+rowNo).val(rowNo);
				var index = $(tr).closest("tr").index();
				//$("table#tbl_tube_ref tbody tr:eq("+index+")").remove();
				$("table#tbl_tube_ref tbody tr:eq("+index+")").hide();
			}
			else
			{
				var r=confirm("Are you sure want to remove?");
				if (r==true)
				{
					var data_str = '';
					var i = 1;
					var no_of_tube = $('#txtnooftube_'+rowNo).val();
					for(i; i<=no_of_tube; i++)
					{
						if(data_str != '')
						{
							data_str += '__';	
						}
						data_str += $('#txtupdateid_'+rowNo+i).val();
					}
					var operation = 2;
					var data = 'action=actn_save_update_delete&operation='+operation+'&data_str='+data_str;
					freeze_window(operation);
					http.open("POST", "program_wise_ref_creation_controller.php", true);
					http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = response_delete;
				}
				
				function response_delete()
				{
					if (http.readyState == 4)
					{
						var response = trim(http.responseText).split('**');
						show_msg(response[0]);
						if(response[0] == 2)
						{
							alert('Data Deleted Successfuly.');
							$('#hdnisremove_'+rowNo).val(rowNo);
							var index = $(tr).closest("tr").index();
							$("table#tbl_tube_ref tbody tr:eq("+index+")").hide();
						}
						release_freezing();
					}
				}
			}
		}

		function func_machine_popup(row_no)
		{
			var batch_no = $('#txtbatchno_'+row_no).val();
			var split_batch_no = batch_no.split('-');
			if(batch_no == '' || split_batch_no[2] == '')
			{
				$('#txtbatchno_'+row_no).val('');
				if (form_validation('txtbatchno_'+row_no, 'Batch No') == false)
				{
					$('#txtbatchno_'+row_no).val(batch_no);
					return;
				}
			}
			var pretxtbatchno = $('#pretxtbatchno_'+row_no).val();
			var data_str = '';
			var tube_refs = '';
			var i = 1;
			var no_of_tube = $('#txtnooftube_'+row_no).val();
			for(i; i<=no_of_tube; i++)
			{
				if($('#txtupdateid_'+row_no+i).val() !=""){
					if(tube_refs != '')
					{
						tube_refs += '__';	
					}
					tube_refs += "'"+$('#txttuberefno_'+row_no+i).val()+"'";
				}
			}

			if(tube_refs != "")
			{
				var response=return_global_ajax_value( tube_refs+'**'+pretxtbatchno, 'check_knit_production', '', 'program_wise_ref_creation_controller');
				var response=response.split("**");
				if(response[0]==1)
				{
					alert(response[1]);
					return;
				}
			}
			//alert(tube_refs);//return;
			
			var page_link = 'program_wise_ref_creation_controller.php?action=actn_machine_popup&company_id='+'<? echo $company_id; ?>';
			var title = 'Machine Info';
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=640px,height=300px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function ()
			{
				var theform = this.contentDoc.forms[0];
        		var hidden_machine_data = this.contentDoc.getElementById("hidden_machine_data").value;
				var split_machine_data = hidden_machine_data.split('*');
				var z = 0;
				for(z; z<split_machine_data.length; z++)
				{
					var split_data = split_machine_data[z].split('__');
					$('#hdnmachineid_'+row_no).val(split_data[0]);
					$('#txtmachineno_'+row_no).val(split_data[1]);
					$('#txtcapacity_'+row_no).val(split_data[2]);
					$('#txtefficiency_'+row_no).val(split_data[3]);
					$('#txtnooftube_'+row_no).val(split_data[4]);
					$('#txtcapacitypertube_'+row_no).val(split_data[5]);
					
					var batch_no = $('#txtbatchno_'+row_no).val();
					var split_batch_no = batch_no.split('-');
					
					var booking_no = $('#txtbookingno_'+row_no).val();
					var split_booking_no = booking_no.split('-');
	
					var i = 1;
					var tube_ref_no = '';
					var tube_ref_qty = '';
					var ref_qty = '';
					var prog_blnc_qty = $('#txtprogqty_'+row_no).attr('data-prog_blns_qty');
					
					/*
					|------------------------------------------------------------------------
					| if program balance qty is less than total ref. qty then
					| tube/ref. qty will proportionate of program balance qty
					|------------------------------------------------------------------------
					*/
					var total_prog_qty = $('#txtprogqty_'+row_no).attr('data-prog_blns_qty');
					var total_ref_qty = split_data[4]*split_data[5];
					if(total_prog_qty*1 < total_ref_qty*1)
					{
						var per_tube_qty = (total_prog_qty/split_data[4]).toFixed(2);
						var blnc_qty = total_prog_qty - (per_tube_qty*split_data[4]);
						var tube_cap_qty = (per_tube_qty*1 + blnc_qty*1).toFixed(2);
						$('#txtcapacitypertube_'+row_no).val(tube_cap_qty);
					}
					//end
					
					for(i; i<=split_data[4]; i++)
					{
						if(total_prog_qty*1 >= total_ref_qty*1)
						{
							if(prog_blnc_qty*1 > split_data[5]*1)
							{
								prog_blnc_qty = prog_blnc_qty - split_data[5];
								ref_qty = split_data[5];
							}
							else
							{
								ref_qty = prog_blnc_qty.toFixed(2);
							}
						}
						else
						{
							if(i != split_data[4])
							{
								ref_qty = per_tube_qty;
							}
							else
							{
								ref_qty = tube_cap_qty;
							}
						}
						
						var ref_no = split_booking_no[0]+split_booking_no[2]+'-'+(split_booking_no[3]*1)+'-'+split_batch_no[2]+'-R'+i;
						tube_ref_no = tube_ref_no+"<input type='text' name='txttuberefno_"+row_no+i+"' id='txttuberefno_"+row_no+i+"' class='text_boxes' style='width:110px;text-align:center;' value="+ref_no+" readonly disabled />"+"<input type='hidden' name='txtupdateid_"+row_no+i+"' id='txtupdateid_"+row_no+i+"' class='text_boxes' style='width:60px;text-align:center;' value='' readonly disabled />";
						tube_ref_qty = tube_ref_qty+"<input type='text' name='txttuberefqty_"+row_no+i+"' id='txttuberefqty_"+row_no+i+"' class='text_boxes' style='width:60px;text-align:right;' value="+ref_qty+" onDblClick='func_yarn_popup("+row_no+i+','+row_no+")' />"+"<input type='hidden' name='txtrolldata_"+row_no+i+"' id='txtrolldata_"+row_no+i+"' class='text_boxes' style='width:60px;text-align:center;' value='' readonly disabled />";
					}
					
					document.getElementById('tdtuberefno_'+row_no).innerHTML = tube_ref_no;
					document.getElementById('tdtuberefqty_'+row_no).innerHTML = tube_ref_qty;
					//set_all_onclick();
				}
			}
		}
		
		function func_save_update_delete(operation)
		{
			var data_str = '';
			var batch_no_str = '';
			var i = 1;
			var no_of_row = $('#tbl_tube_ref tbody tr').length;
			for(i; i<=no_of_row; i++)
			{
				var remove_row = $('#hdnisremove_'+i).val()*1;
				var no_of_tube = $('#txtnooftube_'+i).val()*1;
				if(no_of_tube > 0 && i != remove_row)
				{
					var j = 1;
					for(j; j<=no_of_tube; j++)
					{
						if(data_str != '')
						{
							data_str += '__';	
						}
						
						data_str += $('#txtprogno_'+i).val()+','+$('#txtplanneddate_'+i).val()+','+$('#txtbatchno_'+i).val()+','+$('#hdnmachineid_'+i).val()+','+$('#txtcapacitypertube_'+i).val()+','+$('#txttuberefno_'+i+j).val()+','+$('#txttuberefqty_'+i+j).val()+','+$('#txtupdateid_'+i+j).val()+','+$('#txtcapacity_'+i).val()+','+$('#txtefficiency_'+i).val()+','+$('#txtnooftube_'+i).val()+','+$('#txtcapacitypertube_'+i).val()+','+$('#hdnFSO_'+i).val()+','+$('#txtcolorID_'+i).val()+','+$('#txtrolldata_'+i+j).val()+','+$('#pretxtplanneddate_'+i).val()+','+$('#pretxtbatchno_'+i).val()+','+$('#pretxtcapacitypertube_'+i).val();  
					}
					
					//for batch no
					if(batch_no_str != '')
					{
						batch_no_str += ',';
					}
					batch_no_str += $('#txtbatchno_'+i).val();
				}
			}

        	var data = 'action=actn_save_update_delete&operation='+operation+'&data_str='+data_str+'&batch_no_str='+batch_no_str;
        	freeze_window(operation);
        	http.open("POST", "program_wise_ref_creation_controller.php", true);
        	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        	http.send(data);
        	http.onreadystatechange = response_save_update_delete;
		}
		
		function response_save_update_delete()
		{
        	if (http.readyState == 4)
			{
                var response = trim(http.responseText).split('**');
                console.log(http.responseText);
                show_msg(response[0]);
				

                if(response[0] == 0 || response[0] == 1 || response[0] == 2)
				{
					alert('Data Save/Update Successfuly.');
					parent.emailwindow.hide();
                }

                if (response[0] == 11)
				{
                	alert(response[1]);
                }
                release_freezing();
            }
        }
		
		//func_onkeyup_capacity_per_tube
		function func_onkeyup_capacity_per_tube(row_no, val)
		{
			var no_of_tube = $('#txtnooftube_'+row_no).val();
			var i = 1;
			for(i; i<=no_of_tube; i++)
			{
				$('#txttuberefqty_'+row_no+i).val(val)
			}
		}
		
		//func_yarn_popup
		function func_yarn_popup(i, j)
		{
			var mst_id = $('#txtupdateid_'+i).val();
			var tube_ref_no = $('#txttuberefno_'+i).val();
			var tube_ref_qty = $('#txttuberefqty_'+i).val();
			var txtrolldata = $('#txtrolldata_'+i).val();
			var max_roll_weight = $('#max_roll_weight').val();
			
			var prog_no = $('#txtprogno_'+j).val();
			var page_link = 'program_wise_ref_creation_controller.php?action=actn_yarn_popup&mst_id='+mst_id+'&prog_no='+prog_no+'&tube_ref_no='+tube_ref_no+'&tube_ref_qty='+tube_ref_qty+'&txtrolldata='+txtrolldata+'&max_roll_weight='+max_roll_weight;
			var title = 'Tube Ref. Popup';
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=400px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose=function()
			{
				var theform = this.contentDoc.forms[0];
        		var roll_data = this.contentDoc.getElementById("save_data_roll").value;
				$('#txtrolldata_'+i).val(roll_data);
			}
		}		
	</script>
	</head>
	<body>
	<div align="center">
		<?
		echo load_freeze_divs("../../../", $permission, 1);
		$color_dtls = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
		$max_roll_weight = return_field_value("max_roll_weight", "variable_settings_production", "status_active=1 and company_name=$company_id and variable_list=49");
		
		/*
		|--------------------------------------------------------------------------
		| for machine information
		|--------------------------------------------------------------------------
		|
		*/
		$sql_machine = "SELECT ID, MACHINE_NO, PROD_CAPACITY, EFFICIENCY, NO_OF_FEEDER FROM LIB_MACHINE_NAME WHERE CATEGORY_ID = 2 AND COMPANY_ID = ".$company_id;
		$sql_machine_rslt = sql_select($sql_machine);
		$machine_data_arr = array();
		foreach ($sql_machine_rslt as $row) 
		{
			$capacity_per_tube = decimal_format(((($row['PROD_CAPACITY']*$row['EFFICIENCY'])/100)/$row['NO_OF_FEEDER']), '1', ',');			
			$machine_data_arr[$row['ID']]['MACHINE_NO'] = $row['MACHINE_NO'];
			$machine_data_arr[$row['ID']]['PROD_CAPACITY'] = $row['PROD_CAPACITY'];
			$machine_data_arr[$row['ID']]['EFFICIENCY'] = $row['EFFICIENCY'];
			$machine_data_arr[$row['ID']]['NO_OF_FEEDER'] = $row['NO_OF_FEEDER'];
			$machine_data_arr[$row['ID']]['CAPACITY_PER_TUBE'] = $capacity_per_tube;
		}
		unset($sql_machine_rslt);		

		/*
		|--------------------------------------------------------------------------
		| for reference information
		|--------------------------------------------------------------------------
		|
		*/
		$sql_ref = "SELECT A.BOOKING_NO, B.ID AS PROG_NO, B.COLOR_ID, C.ID, C.COLOR_TYPE_ID, C.PROGRAM_QNTY, D.ID AS REF_ID, D.PLANNED_DATE, D.BATCH_NO, D.MACHINE_ID, D.MACHINE_CAPACITY, D.MACHINE_EFFICIENCY, D.NO_OF_TUBE, D.CAPACITY_PER_TUBE, D.REFERENCE_NO, D.REFERENCE_QTY, E.JOB_NO FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, PPL_REFERENCE_CREATION D, FABRIC_SALES_ORDER_MST E WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND B.MST_ID = C.MST_ID AND B.ID = D.PROGRAM_NO AND C.DTLS_ID = D.PROGRAM_NO AND A.IS_SALES = 1 AND B.IS_SALES = 1 AND C.IS_SALES = 1 AND C.PO_ID=E.ID AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND D.STATUS_ACTIVE = 1 AND D.IS_DELETED = 0 AND B.ID IN(".$prog_no.") ORDER BY D.ID ASC";
		$sql_ref_rslt = sql_select($sql_ref);
		$duplicate_check = array();
		$ref_data_arr = array();
		$ref_id_arr = array();
		foreach($sql_ref_rslt as $row)
		{
			if($duplicate_check[$row['REF_ID']] != $row['REF_ID'])
			{
				$duplicate_check[$row['REF_ID']] = $row['REF_ID'];
				$ref_id_arr[$row['REF_ID']] = $row['REF_ID'];

				//for color
				$exp_color_arr = array();
				$color_arr = array();
				$exp_color_arr = explode(',',$row['COLOR_ID']);
				foreach($exp_color_arr as $key=>$val)
				{
					$color_arr[$val] = $color_dtls[$val];
				}
				//end for color

				$ref_data_arr[$row['BOOKING_NO']][$row['PROG_NO']][$row['BATCH_NO']]['REF_ID'][$row['REF_ID']] = $row['REF_ID'];
				$ref_data_arr[$row['BOOKING_NO']][$row['PROG_NO']][$row['BATCH_NO']]['COLOR_ID'] = $row['COLOR_ID'];
				$ref_data_arr[$row['BOOKING_NO']][$row['PROG_NO']][$row['BATCH_NO']]['COLOR_NAME'] = implode(', ', $color_arr);
				$ref_data_arr[$row['BOOKING_NO']][$row['PROG_NO']][$row['BATCH_NO']]['COLOR_TYPE_ID'] = $color_type[$row['COLOR_TYPE_ID']];
				$ref_data_arr[$row['BOOKING_NO']][$row['PROG_NO']][$row['BATCH_NO']]['PROGRAM_QNTY'] = decimal_format($row['PROGRAM_QNTY'], '1', '');
				$ref_data_arr[$row['BOOKING_NO']][$row['PROG_NO']][$row['BATCH_NO']]['PLANNED_DATE'] = date('d-m-Y', strtotime($row['PLANNED_DATE']));
				$ref_data_arr[$row['BOOKING_NO']][$row['PROG_NO']][$row['BATCH_NO']]['BATCH_NO'] = $row['BATCH_NO'];
				$ref_data_arr[$row['BOOKING_NO']][$row['PROG_NO']][$row['BATCH_NO']]['MACHINE_ID'] = $row['MACHINE_ID'];
				$ref_data_arr[$row['BOOKING_NO']][$row['PROG_NO']][$row['BATCH_NO']]['MACHINE_NO'] = $machine_data_arr[$row['MACHINE_ID']]['MACHINE_NO'];
				$ref_data_arr[$row['BOOKING_NO']][$row['PROG_NO']][$row['BATCH_NO']]['PROD_CAPACITY'] = $row['MACHINE_CAPACITY'];
				$ref_data_arr[$row['BOOKING_NO']][$row['PROG_NO']][$row['BATCH_NO']]['EFFICIENCY'] = $row['MACHINE_EFFICIENCY'];
				$ref_data_arr[$row['BOOKING_NO']][$row['PROG_NO']][$row['BATCH_NO']]['NO_OF_FEEDER'] = $row['NO_OF_TUBE'];
				$ref_data_arr[$row['BOOKING_NO']][$row['PROG_NO']][$row['BATCH_NO']]['CAPACITY_PER_TUBE'] = $row['CAPACITY_PER_TUBE'];
				$ref_data_arr[$row['BOOKING_NO']][$row['PROG_NO']][$row['BATCH_NO']]['REFERENCE_NO'][$row['REF_ID']] = $row['REFERENCE_NO'];
				$ref_data_arr[$row['BOOKING_NO']][$row['PROG_NO']][$row['BATCH_NO']]['REFERENCE_QTY'][$row['REF_ID']] = $row['REFERENCE_QTY'];
				$ref_data_arr[$row['BOOKING_NO']][$row['PROG_NO']][$row['BATCH_NO']]['SALES_ORDER'] = $row['JOB_NO'];
			}
		}
		unset($sql_ref_rslt);
		/*echo "<pre>";
		print_r($ref_data_arr);
		echo "</pre>";*/		
		
		/*
		|--------------------------------------------------------------------------
		| for program information
		|--------------------------------------------------------------------------
		|
		*/
		$sql_prog = "SELECT A.BOOKING_NO, B.ID AS PROG_NO, B.COLOR_ID, C.ID, C.COLOR_TYPE_ID, C.PROGRAM_QNTY, D.JOB_NO FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, FABRIC_SALES_ORDER_MST D WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND B.MST_ID = C.MST_ID AND C.PO_ID=D.ID AND A.IS_SALES = 1 AND B.IS_SALES = 1 AND C.IS_SALES = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND B.ID IN(".$prog_no.")";
		//echo $sql; die;
		$sql_prog_rslt = sql_select($sql_prog);
		$data_arr = array();
		$duplicate_check = array();
		foreach($sql_prog_rslt as $row)
		{
			if($duplicate_check[$row['ID']] != $row['ID'])
			{
				$duplicate_check[$row['ID']] = $row['ID'];
				$data_arr[$row['BOOKING_NO']][$row['PROG_NO']]['COLOR_TYPE_ID'] = $color_type[$row['COLOR_TYPE_ID']];
				$data_arr[$row['BOOKING_NO']][$row['PROG_NO']]['PROGRAM_QNTY'] += $row['PROGRAM_QNTY'];
				
				//for color
				$exp_color_arr = array();
				$color_arr = array();
				$exp_color_arr = explode(',',$row['COLOR_ID']);
				foreach($exp_color_arr as $key=>$val)
				{
					$color_arr[$val] = $color_dtls[$val];
				}
				$data_arr[$row['BOOKING_NO']][$row['PROG_NO']]['COLOR_NAME'] = implode(', ', $color_arr);
				$data_arr[$row['BOOKING_NO']][$row['PROG_NO']]['COLOR_ID'] = $row['COLOR_ID'];
				$data_arr[$row['BOOKING_NO']][$row['PROG_NO']]['SALES_ORDER'] = $row['JOB_NO'];
			}
		}
		unset($sql_prog_rslt);
		
		//for yarn
		$sql_yrn = "SELECT MST_ID, YARN_ORIGIN, YARN_LOT, YARN_COUNT FROM PPL_REFERENCE_CREATION_YARN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND MST_ID IN(".implode(',', $ref_id_arr).")";
		//echo $sql_yrn;
		$sql_yrn_rslt = sql_select($sql_yrn);
		$yrn_data_arr = array();
		foreach($sql_yrn_rslt as $row)
		{
			if($yrn_data_arr[$row['MST_ID']] != '')
			{
				$yrn_data_arr[$row['MST_ID']] .= '***';
			}
			$yrn_data_arr[$row['MST_ID']] .= $row['YARN_ORIGIN'].'**'.$row['YARN_LOT'].'**'.$row['YARN_COUNT'];			
		}		
		
		//for roll
		$sql_roll = "SELECT MST_ID, ROLL_NO, ROLL_WEIGHT, QTY_IN_PCS, QTY_IN_SIZE, BARCODE_NO FROM PPL_REFERENCE_CREATION_ROLL WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND MST_ID IN(".implode(',', $ref_id_arr).")";
		//echo $sql_roll;
		$sql_roll_rslt = sql_select($sql_roll);
		$roll_data_arr = array();
		foreach($sql_roll_rslt as $row)
		{
			if($roll_data_arr[$row['MST_ID']] != '')
			{
				$roll_data_arr[$row['MST_ID']] .= '!!!!';
			}
			$roll_data_arr[$row['MST_ID']] .= $row['ROLL_NO'].'**'.$row['ROLL_WEIGHT'].'**'.$row['QTY_IN_PCS'].'**'.$row['QTY_IN_SIZE'].'**'.$row['BARCODE_NO'];	
		}		
		?>
		<form name="programQnty_1" id="programQnty_1">
			<fieldset style="width:1260px;">
				<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1260" align="center" id="tbl_tube_ref">
                    <thead>
                        <th width="120">Booking No</th>
                        <th width="70">Prog. No</th>
                        <th width="70">Prog. Qty</th>
                        <th width="70">Color Type</th>
                        <th width="120">Color</th>
                        <th width="70">Planned Date</th>
                        <th width="70">Batch No</th>
                        <th width="70">M/C No</th>
                        <th width="70">Capacity</th>
                        <th width="70">Effi%</th>
                        <th width="70">No. of Tube</th>
                        <th width="70">Capacity Per Tube</th>
                        <th width="120">Tube/Ref. No</th>
                        <th width="70">Tube/Ref. Qty</th>
                        <th></th>
                    </thead>
                    <tbody>
                    <?
					/*
					|--------------------------------------------------------------------------
					| for update
					|--------------------------------------------------------------------------
					|
					*/
					$i = 0;
					foreach($ref_data_arr as $booking_no=>$booking_no_arr)
					{
						foreach($booking_no_arr as $prog_no=>$prog_no_arr)
						{
							foreach($prog_no_arr as $batch_no=>$row)
							{
								$i++;
								?>
								<tr>
									<td align="center">
									<input type="text" name="txtbookingno_<? echo $i; ?>" id="txtbookingno_<? echo $i; ?>" class="text_boxes" style="width:110px; text-align:center;" value="<? echo $booking_no; ?>" readonly disabled />
									<input type="hidden" name="hdnFSO_<? echo $i; ?>" id="hdnFSO_<? echo $i; ?>" class="text_boxes" style="width:110px; text-align:center;" value="<? echo $row['SALES_ORDER']; ?>" />
									</td>
									<td align="center"><input type="text" name="txtprogno_<? echo $i; ?>" id="txtprogno_<? echo $i; ?>" class="text_boxes" style="width:60px; text-align:center;" value="<? echo $prog_no; ?>" readonly disabled /></td>
									<td align="center"><input type="text" name="txtprogqty_<? echo $i; ?>" id="txtprogqty_<? echo $i; ?>" class="text_boxes" style="width:60px; text-align:center;" value="<? echo decimal_format($row['PROGRAM_QNTY'], '1', ''); ?>" readonly disabled /></td>
									<td align="center"><input type="text" name="txtcolortype_<? echo $i; ?>" id="txtcolortype_<? echo $i; ?>" class="text_boxes" style="width:60px; text-align:center;" value="<? echo $row['COLOR_TYPE_ID']; ?>" readonly disabled /></td>
									<td align="left">
									<input type="text" name="txtcolor_<? echo $i; ?>" id="txtcolor_<? echo $i; ?>" class="text_boxes" style="width:110px; text-align:center;" value="<? echo $row['COLOR_NAME']; ?>" readonly disabled />
									<input type="hidden" name="txtcolorID_<? echo $i; ?>" id="txtcolorID_<? echo $i; ?>" class="text_boxes" style="width:110px; text-align:center;" value="<? echo $row['COLOR_ID']; ?>" readonly disabled />
									</td>
									<td>
										<input type="text" name="txtplanneddate_<? echo $i; ?>" id="txtplanneddate_<? echo $i; ?>" class="datepicker" style="width:60px; text-align:center;" value="<? echo $row['PLANNED_DATE']; ?>" readonly />
										<input type="hidden" name="pretxtplanneddate_<? echo $i; ?>" id="pretxtplanneddate_<? echo $i; ?>" class="datepicker" style="width:60px; text-align:center;" value="<? echo $row['PLANNED_DATE']; ?>" readonly />
									</td>
									<td>
										<input type="text" name="txtbatchno_<? echo $i; ?>" id="txtbatchno_<? echo $i; ?>" class="text_boxes" style="width:100px; text-align:center;" value="<? echo $row['BATCH_NO']; ?>" />
										<input type="hidden" name="pretxtbatchno_<? echo $i; ?>" id="pretxtbatchno_<? echo $i; ?>" class="text_boxes" style="width:100px; text-align:center;" value="<? echo $row['BATCH_NO']; ?>" />
									</td>
									<td>
										<input type="text" name="txtmachineno_<? echo $i; ?>" id="txtmachineno_<? echo $i; ?>" class="text_boxes" style="width:60px" placeholder="Browse" onClick="func_machine_popup('<? echo $i; ?>');" value="<? echo $row['MACHINE_NO']; ?>" readonly />
										<input type="hidden" name="pretxtmachineno_<? echo $i; ?>" id="pretxtmachineno_<? echo $i; ?>" class="text_boxes" style="width:60px" placeholder="Browse" value="<? echo $row['MACHINE_NO']; ?>" readonly />
									</td>
									<td><input type="text" name="txtcapacity_<? echo $i; ?>" id="txtcapacity_<? echo $i; ?>" class="text_boxes" style="width:60px; text-align:center;" value="<? echo $row['PROD_CAPACITY']; ?>" readonly disabled /></td>
									<td><input type="text" name="txtefficiency_<? echo $i; ?>" id="txtefficiency_<? echo $i; ?>" class="text_boxes" style="width:60px; text-align:center;" value="<? echo $row['EFFICIENCY']; ?>" readonly disabled /></td>
									<td><input type="text" name="txtnooftube_<? echo $i; ?>" id="txtnooftube_<? echo $i; ?>" class="text_boxes" style="width:60px; text-align:center;" value="<? echo $row['NO_OF_FEEDER']; ?>" readonly disabled /></td>
									<td>
										<input type="text" name="txtcapacitypertube_<? echo $i; ?>" id="txtcapacitypertube_<? echo $i; ?>" class="text_boxes" style="width:60px; text-align:center;" onKeyUp="func_onkeyup_capacity_per_tube('<? echo $i; ?>', this.value)" value="<? echo decimal_format($row['CAPACITY_PER_TUBE'], '1', ''); ?>" />
										<input type="hidden" name="pretxtcapacitypertube_<? echo $i; ?>" id="pretxtcapacitypertube_<? echo $i; ?>" class="text_boxes" style="width:60px; text-align:center;"  value="<? echo decimal_format($row['CAPACITY_PER_TUBE'], '1', ''); ?>" />
									</td>
									<td id="tdtuberefno_<? echo $i; ?>">                                
									<?
									$nrf = 0;
									foreach($row['REFERENCE_NO'] as $key=>$val)
									{
										$nrf++;
										?>
										<input type="text" name="txttuberefno_<? echo $i.$nrf; ?>" id="txttuberefno_<? echo $i.$nrf; ?>" class="text_boxes" style="width:110px;" value="<? echo $val; ?>" readonly disabled />
										<input type="hidden" name="txtupdateid_<? echo $i.$nrf; ?>" id="txtupdateid_<? echo $i.$nrf; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $key; ?>" readonly disabled />

										<input type="hidden" name="txtupdateidDataString_<? echo $i.$nrf; ?>" id="txtupdateidDataString_<? echo $i.$nrf; ?>" class="text_boxes" value="<? echo $key; ?>" readonly disabled />
										<?
									}
									?>
									<td id="tdtuberefqty_<? echo $i; ?>">                                
									<?
									$nrf = 0;
									foreach($row['REFERENCE_QTY'] as $key=>$val)
									{
										$nrf++;
										$yrn_data = $yrn_data_arr[$key];
										$roll_data = $roll_data_arr[$key];
										?>
										<input type="text" name="txttuberefqty_<? echo $i.$nrf; ?>" id="txttuberefqty_<? echo $i.$nrf; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo decimal_format($val, '1', ''); ?>" onDblClick="func_yarn_popup('<? echo $i.$nrf; ?>','<? echo $i; ?>')" />
										<input type="hidden" name="txtrolldata_<? echo $i.$nrf; ?>" id="txtrolldata_<? echo $i.$nrf; ?>" class="text_boxes" style="width:60px;" value="<? echo $roll_data; ?>" readonly disabled />
										<?
									}
									?>
									<td id="tdincreasedecrease_<? echo $i; ?>">
										<input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:25px" class="formbutton" value="+" onClick="add_break_down_tr('<? echo $i; ?>',this)" />
										<input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:25px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr('<? echo $i; ?>','tbl_tube_ref',this);" />
                                        <input type="hidden" name="hdnmachineid_<? echo $i; ?>" id="hdnmachineid_<? echo $i; ?>" class="text_boxes" style="width:60px" value="<? echo $row['MACHINE_ID']; ?>" readonly disabled />
                                        <input type="hidden" name="hdnisupdate_<? echo $i; ?>" id="hdnisupdate_<? echo $i; ?>" class="text_boxes" style="width:60px" value="1" readonly disabled />
                                        <input type="hidden" name="hdnisremove_<? echo $i; ?>" id="hdnisremove_<? echo $i; ?>" class="text_boxes" style="width:60px" value="0" readonly disabled />
									</td>
								</tr>
								<?
							}
						}						
					}
					
					/*
					|--------------------------------------------------------------------------
					| for new entry
					|--------------------------------------------------------------------------
					|
					*/
					foreach($data_arr as $booking_no=>$booking_no_arr)
					{
						foreach($booking_no_arr as $prog_no=>$row)
						{
							$ref_qty = 0;
							if(!empty($ref_data_arr[$booking_no][$prog_no]))
							{
								foreach($ref_data_arr[$booking_no][$prog_no] as $bch=>$bch_arr)
								{
									$ref_qty += array_sum(($bch_arr['REFERENCE_QTY']));
								}
							}

							if($row['PROGRAM_QNTY'] > $ref_qty)
							{
								$i++;
								//for batch no
								$batch_no = '';
								if($within_group == 1)
								{
									$exp_booking_no = explode('-', $booking_no);
									$batch_no = $exp_booking_no[0].$exp_booking_no[2].'-'.($exp_booking_no[3]*1).'-';
								}
								$prog_blns_qty = $row['PROGRAM_QNTY']-$ref_qty;
								?>
								<tr>
									<td align="center">
									<input type="text" name="txtbookingno_<? echo $i; ?>" id="txtbookingno_<? echo $i; ?>" class="text_boxes" style="width:110px; text-align:center;" value="<? echo $booking_no; ?>" readonly disabled />
									<input type="hidden" name="hdnFSO_<? echo $i; ?>" id="hdnFSO_<? echo $i; ?>" class="text_boxes" style="width:110px; text-align:center;" value="<? echo $row['SALES_ORDER']; ?>" />
									</td>
									<td align="center"><input type="text" name="txtprogno_<? echo $i; ?>" id="txtprogno_<? echo $i; ?>" class="text_boxes" style="width:60px; text-align:center;" value="<? echo $prog_no; ?>" readonly disabled /></td>
									<td align="center"><input type="text" name="txtprogqty_<? echo $i; ?>" id="txtprogqty_<? echo $i; ?>" class="text_boxes" style="width:60px; text-align:center;" data-prog_blns_qty="<?php echo decimal_format($prog_blns_qty, '1', ''); ?>" value="<? echo decimal_format($row['PROGRAM_QNTY'], '1', ''); ?>" readonly disabled /></td>
									<td align="center"><input type="text" name="txtcolortype_<? echo $i; ?>" id="txtcolortype_<? echo $i; ?>" class="text_boxes" style="width:60px; text-align:center;" value="<? echo $row['COLOR_TYPE_ID']; ?>" readonly disabled /></td>
									<td align="left">
									<input type="text" name="txtcolor_<? echo $i; ?>" id="txtcolor_<? echo $i; ?>" class="text_boxes" style="width:110px; text-align:center;" value="<? echo $row['COLOR_NAME']; ?>" readonly disabled />
									<input type="hidden" name="txtcolorID_<? echo $i; ?>" id="txtcolorID_<? echo $i; ?>" class="text_boxes" style="width:110px; text-align:center;" value="<? echo $row['COLOR_ID']; ?>"  />
									</td>
									<td>
										<input type="text" name="txtplanneddate_<? echo $i; ?>" id="txtplanneddate_<? echo $i; ?>" class="datepicker" style="width:60px; text-align:center;" value="<? echo date("d-m-Y");?>" readonly />
										<input type="hidden" name="pretxtplanneddate_<? echo $i; ?>" id="txtplanneddate_<? echo $i; ?>" class="datepicker" style="width:60px; text-align:center;" value="<? //echo date("d-m-Y");?>" readonly />
									</td>
									<td>
										<input type="text" name="txtbatchno_<? echo $i; ?>" id="txtbatchno_<? echo $i; ?>" class="text_boxes" style="width:100px; text-align:center;" value="<? echo $batch_no; ?>" />
										<input type="hidden" name="pretxtbatchno_<? echo $i; ?>" id="txtbatchno_<? echo $i; ?>" class="text_boxes" style="width:100px; text-align:center;" value="<? //echo $batch_no; ?>" />
									</td>
									<td>
										<input type="text" name="txtmachineno_<? echo $i; ?>" id="txtmachineno_<? echo $i; ?>" class="text_boxes" style="width:60px" placeholder="Browse" onClick="func_machine_popup('<? echo $i; ?>');" readonly />
										<input type="hidden" name="pretxtmachineno_<? echo $i; ?>" id="txtmachineno_<? echo $i; ?>" class="text_boxes" style="width:60px" placeholder="Browse"  readonly />
									</td>
									<td><input type="text" name="txtcapacity_<? echo $i; ?>" id="txtcapacity_<? echo $i; ?>" class="text_boxes" style="width:60px; text-align:center;" readonly disabled /></td>
									<td><input type="text" name="txtefficiency_<? echo $i; ?>" id="txtefficiency_<? echo $i; ?>" class="text_boxes" style="width:60px; text-align:center;" readonly disabled /></td>
									<td><input type="text" name="txtnooftube_<? echo $i; ?>" id="txtnooftube_<? echo $i; ?>" class="text_boxes" style="width:60px; text-align:center;" readonly disabled /></td>
									<td>
										<input type="text" name="txtcapacitypertube_<? echo $i; ?>" id="txtcapacitypertube_<? echo $i; ?>" class="text_boxes" style="width:60px; text-align:center;" onKeyUp="func_onkeyup_capacity_per_tube('<? echo $i; ?>', this.value)" />
										<input type="hidden" name="pretxtcapacitypertube_<? echo $i; ?>" id="txtcapacitypertube_<? echo $i; ?>" class="text_boxes" style="width:60px; text-align:center;" />
									</td>

									<td id="tdtuberefno_<? echo $i; ?>"><input type="text" name="txttuberefno_<? echo $i; ?>" id="txttuberefno_<? echo $i; ?>" class="text_boxes" style="width:110px;" readonly disabled /><input type="hidden" name="txtupdateid_<? echo $i; ?>" id="txtupdateid_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" readonly disabled /></td>
									<td id="tdtuberefqty_<? echo $i; ?>">
                                    	<input type="text" name="txttuberefqty_<? echo $i; ?>" id="txttuberefqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" placeholder="<? echo decimal_format($prog_blns_qty, '1', '');?>" />
										<input type="hidden" name="txtrolldata_<? echo $i; ?>" id="txtrolldata_<? echo $i; ?>" class="text_boxes" style="width:60px;" value="" readonly disabled />
                                        </td>
									<td id="tdincreasedecrease_<? echo $i; ?>">
										<input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:25px" class="formbutton" value="+" onClick="add_break_down_tr('<? echo $i; ?>',this)" />
										<input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:25px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr('<? echo $i; ?>','tbl_tube_ref',this);" />
                                        <input type="hidden" name="hdnmachineid_<? echo $i; ?>" id="hdnmachineid_<? echo $i; ?>" class="text_boxes" style="width:60px" readonly disabled />
                                        <input type="hidden" name="hdnisupdate_<? echo $i; ?>" id="hdnisupdate_<? echo $i; ?>" class="text_boxes" style="width:60px" value="0" readonly disabled />
                                        <input type="hidden" name="hdnisremove_<? echo $i; ?>" id="hdnisremove_<? echo $i; ?>" class="text_boxes" style="width:60px" value="0" readonly disabled />
									</td>
								</tr>
								<?
							}
						}						
					}
					?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="15" align="center" valign="top" class="button_container" style="border-left:hidden;"><input type="button" name="save_create" class="formbutton" value="Save&Create" id="save_create" onClick="func_save_update_delete('0');" style="width:80px;"/>&nbsp;<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:80px;"/>

                            	<input type="hidden" id="max_roll_weight" name="max_roll_weight" value="<? echo $max_roll_weight;?>">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script type="text/javascript"></script>
	</html>
	<?
	exit();
}

//actn_yarn_popup
if ($action == "actn_yarn_popup") 
{
	echo load_html_head_contents("Yarn Popup", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		var permission = '<? echo $permission; ?>';
		//func_add_roll_tr
		function func_add_roll_tr()
		{
			var i = $('#tbd_roll tr').length + 1;			
			$("#tbd_roll tr:last").clone().find('input').each(function() {
				$(this).attr({
				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
				'value': function(_, value) { return value }              
				});
			}).end().appendTo("#tbd_roll");
			
			$('#txtrollno_'+i).val(i);
			$('#rollincrease_'+i).removeAttr("onClick").attr("onClick","func_add_roll_tr("+i+");");
			$('#rolldecrease_'+i).removeAttr("onClick").attr("onClick","func_delete_roll_tr("+i+");");
			func_calculate_qty();
		}
		
		//func_delete_roll_tr
		function func_delete_roll_tr()
		{
			var i = $('#tbd_roll tr').length + 1;
			if(i != 1)
			{
				var r=confirm("Are you sure want to remove last row?");
				if (r==true)
				{
					$('#tbd_roll tr:last').remove();
					func_calculate_qty();
				}
			}
		}
		
		//func_onclose
		function func_onclose()
		{
			var rollRow = $('#tbd_roll tr').length;
			var roll_data = '';
			var r = 1;
			for(r; r <= rollRow; r++)
			{
				//&& $('#txtqtypcs_'+r).val()*1 > 0 && $('#txtqtysize_'+r).val() != ''
				if($('#txtrollno_'+r).val()*1 > 0 && $('#txtrollweight_'+r).val()*1 > 0 )
				{
					if(roll_data != '')
					{
						roll_data += '!!!!';
					}
					
					roll_data += $('#txtrollno_'+r).val()+'**'+$('#txtrollweight_'+r).val()+'**'+$('#txtqtypcs_'+r).val()+'**'+$('#txtqtysize_'+r).val()+'**'+$('#txtbarcode_'+r).val();
				}
			}
			
			$('#save_data_roll').val(roll_data);
			parent.emailwindow.hide();
		}
		
		function func_calculate_qty(i)
		{
			
			var max_roll_weight_pop = $('#max_roll_weight_pop').val()*1;
			var tube_ref_qnty = $('#tube_ref_qnty').val()*1;
			var refrollweight= $('#txtrollweight_'+i).val()*1;

			if(refrollweight > max_roll_weight_pop){
				alert("Roll Weight can not greter than library max Weight.\nmax Weight:"+max_roll_weight_pop);
				var added_row = document.getElementsByClassName('added_row');
				if (typeof(added_row) != 'undefined' && added_row != null) {
					$(".added_row").remove();
				}
				$("#txtrollweight_1").val("");
				//setTimeout(function() { $('input[name="txtrollweight_1"]').focus() }, 3000);
				//$('#txtrollweight_1').get(0).focus();
					
				return;
			}

			var balance=0;

			if(tube_ref_qnty - refrollweight> 0)
			{
				balance = tube_ref_qnty - refrollweight;
			}else{
				alert("Roll Weight can not greter than Tube ref qnty =" + tube_ref_qnty +"-"+ refrollweight);
				return;
			}

			$(".added_row").remove();
			var text=''; var another=0;
			while (balance > 0) 
			{
				++i;
				if(refrollweight > balance){
					distributed_qty =balance;
				}else{
					distributed_qty =refrollweight;
				}

				text +='<tr class="added_row">'+
                    '<td><input type="text" name="txtrollno_'+ i +'" id="txtrollno_'+ i +'" class="text_boxes_numeric" style="width:40px;" value="'+ i +'" /></td>' +
                    '<td align="right">'+ distributed_qty +
                    	'<input type="hidden" name="txtrollweight_'+ i +'" id="txtrollweight_'+ i +'" class="text_boxes_numeric" style="width:90px;" value="'+distributed_qty+'" />' +
                    '</td>' +
                    '<td align="right" style="width:90px;">' +
                    	'<input type="text" name="txtqtypcs_'+ i +'" id="txtqtypcs_'+ i +'" class="text_boxes_numeric" style="width:90px;" onKeyUp="fnc_row_sum()"/>' +
                    '</td>' +
                    '<td align="right"><input type="text" name="txtqtysize_'+ i +'" id="txtqtysize_'+ i +'" class="text_boxes" style="width:90px;" /></td>' +
                    '<td align="right"><input type="text" name="txtbarcode_'+ i +'" id="txtbarcode_'+ i +'" class="text_boxes_numeric" style="width:90px;" /></td>' +
                '</tr>';

				balance = balance - refrollweight;
			}

			$("#tbd_roll").append(text);
			fnc_row_sum()
		}

		function fnc_row_sum()
		{
			var rollRow = $('#tbd_roll tr').length;
			var total_roll_weight = 0;
			var total_qty_pcs = 0;
			var r = 1;
			for(r; r <= rollRow; r++)
			{
				total_roll_weight = total_roll_weight + $('#txtrollweight_'+r).val()*1;
				total_qty_pcs = total_qty_pcs + $('#txtqtypcs_'+r).val()*1;
			}
			$('#txttotalrollweight').val(total_roll_weight);
			$('#txttotalqtypcs').val(total_qty_pcs);
		}
		
		//func_print
		function func_print(ref_id)
		{
			print_report(ref_id, "print", "program_wise_ref_creation_controller");
		}
	</script>
</head>
<body>
	<div align="center">
		<?
		//echo load_freeze_divs("../../../", $permission, 1);
		$color_dtls = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

		//for roll
		$sql_roll = "SELECT MST_ID, ROLL_NO, ROLL_WEIGHT, QTY_IN_PCS, QTY_IN_SIZE, BARCODE_NO FROM PPL_REFERENCE_CREATION_ROLL WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND MST_ID = ".$mst_id;
		$sql_roll_rslt = sql_select($sql_roll);
		
		//for program info
		$sql_prog = "SELECT B.COLOR_ID, C.FABRIC_DESC, D.ID FROM PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, PPL_REFERENCE_CREATION D WHERE B.ID = C.DTLS_ID AND B.MST_ID = C.MST_ID AND C.DTLS_ID = D.PROGRAM_NO AND B.IS_SALES = 1 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND D.STATUS_ACTIVE = 1 AND D.IS_DELETED = 0 AND D.ID = ".$mst_id;
		$sql_prog_rslt = sql_select($sql_prog);
		$prog_data = array();
		foreach($sql_prog_rslt as $row)
		{
			$prog_data[$row['ID']]['COLOR_ID'] = $color_dtls[$row['COLOR_ID']];
			$prog_data[$row['ID']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
		}

		//echo $txtrolldata;
		if($txtrolldata !=""){
			$txtrolldataArr = explode('!!!!', $txtrolldata);
		}


		?>
		<form name="yrn_1" id="yrn_1">
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="570" align="center" id="tbl_roll" style="margin-top: 10px; float:left;">
                <thead>
                    <tr>
                        <th colspan="3">Tube/Ref. No : <? echo $tube_ref_no; ?></th>
                        <th colspan="2">Tube/Ref. Qty : <? echo $tube_ref_qty; ?></th>
                    </tr>
                    <tr>
                        <th colspan="5">Roll Information</th>
                    </tr>
                    <tr>
                        <th width="50">Roll No</th>
                        <th width="100">Roll No Weight[Kg]</th>
                        <th width="100">Qty. In Pcs</th>
                        <th width="100">Qty. In Size</th>
                        <th width="100">Barcode</th>
                        
                    </tr>
                </thead>
                <tbody id="tbd_roll">
                    <?
					$total_roll_weight = 0;
					$total_qty_pcs = 0;

					if($txtrolldata !="")
					{
						$i = 0;
						foreach($txtrolldataArr as $row)
                        {
                            $i++;
                           	$rollString = explode('**', $row);

                           	$txtrollno = $rollString[0];
                           	$txtrollweight = $rollString[1];
                           	$txtqtypcs = $rollString[2];
                           	$txtqtysize = $rollString[3];
                           	$txtbarcode = $rollString[4];

                            //$('#txtrollno_'+r).val()+'**'+$('#txtrollweight_'+r).val()+'**'+$('#txtqtypcs_'+r).val()+'**'+$('#txtqtysize_'+r).val()+'**'+$('#txtbarcode_'+r).val();
                            if($i ==1)
                            {
                            ?>
                            <tr>
                                <td><input type="text" name="txtrollno_<? echo $i; ?>" id="txtrollno_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $txtrollno; ?>" /></td>
                                <td align="right">
                                	<input type="text" name="txtrollweight_<? echo $i; ?>" id="txtrollweight_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $txtrollweight; ?>" onchange="func_calculate_qty(<? echo $i; ?>)" />
                                </td>
                                <td align="right">
                                	<input type="text" name="txtqtypcs_<? echo $i; ?>" id="txtqtypcs_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $txtqtypcs; ?>" onKeyUp="fnc_row_sum()" />
                                </td>
                                <td align="right">
                                	<input type="text" name="txtqtysize_<? echo $i; ?>" id="txtqtysize_<? echo $i; ?>" class="text_boxes" style="width:90px;" value="<? echo $txtqtysize; ?>" />
                                </td>
                                <td align="right">
                                	<input type="text" name="txtbarcode_<? echo $i; ?>" id="txtbarcode_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $txtbarcode; ?>" />
                                </td>
                            </tr>
                            <?
                        	}
                        	else
                        	{
                            ?>
                            <tr class="added_row">
                                <td><input type="text" name="txtrollno_<? echo $i; ?>" id="txtrollno_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $txtrollno; ?>" /></td>
                                <td align="right">
                                	<? echo $txtrollweight; ?>
                                	<input type="hidden" name="txtrollweight_<? echo $i; ?>" id="txtrollweight_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $txtrollweight; ?>" />
                                </td>
                                <td align="right">
                                	<input type="text" name="txtqtypcs_<? echo $i; ?>" id="txtqtypcs_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $txtqtypcs; ?>" onKeyUp="fnc_row_sum()" />
                                </td>
                                <td align="right">
                                	<input type="text" name="txtqtysize_<? echo $i; ?>" id="txtqtysize_<? echo $i; ?>" class="text_boxes" style="width:90px;" value="<? echo $txtqtysize; ?>" />
                                </td>
                                <td align="right">
                                	<input type="text" name="txtbarcode_<? echo $i; ?>" id="txtbarcode_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $txtbarcode; ?>" />
                                </td>
                            </tr>
                            <?
                        	}

							$total_roll_weight += $txtrollweight;
							$total_qty_pcs += $txtqtypcs;
                        }
					}

                    else if(empty($sql_roll_rslt))
                    {
                        ?>
                        <tr>
                            <td>
								<input type="text" name="txtrollno_1" id="txtrollno_1" class="text_boxes_numeric" style="width:40px;" value="1"/>
							</td>
                            <td align="right"><input type="text" name="txtrollweight_1" id="txtrollweight_1" class="text_boxes_numeric" style="width:90px;" onchange="func_calculate_qty(1)" /></td>
                            <td align="right"><input type="text" name="txtqtypcs_1" id="txtqtypcs_1" class="text_boxes_numeric" style="width:90px;" onKeyUp="fnc_row_sum()" /></td>
                            <td align="right"><input type="text" name="txtqtysize_1" id="txtqtysize_1" class="text_boxes" style="width:90px;" /></td>
                            <td align="right"><input type="text" name="txtbarcode_1" id="txtbarcode_1" class="text_boxes_numeric" style="width:90px;" /></td>
                        </tr>
                        <?
                    }
                    else
                    {
                        $i = 0;
                        foreach($sql_roll_rslt as $row)
                        {
                            $i++;
                            if($i ==1)
                            {
                            ?>
                            <tr>
                                <td><input type="text" name="txtrollno_<? echo $i; ?>" id="txtrollno_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $row['ROLL_NO']; ?>" /></td>
                                <td align="right">
                                	<input type="text" name="txtrollweight_<? echo $i; ?>" id="txtrollweight_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $row['ROLL_WEIGHT']; ?>" onchange="func_calculate_qty(<? echo $i; ?>)" />
                                </td>
                                <td align="right">
                                	<input type="text" name="txtqtypcs_<? echo $i; ?>" id="txtqtypcs_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $row['QTY_IN_PCS']; ?>" onKeyUp="fnc_row_sum()" />
                                </td>
                                <td align="right"><input type="text" name="txtqtysize_<? echo $i; ?>" id="txtqtysize_<? echo $i; ?>" class="text_boxes" style="width:90px;" value="<? echo $row['QTY_IN_SIZE']; ?>" /></td>
                                <td align="right"><input type="text" name="txtbarcode_<? echo $i; ?>" id="txtbarcode_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $row['BARCODE_NO']; ?>" /></td>
                            </tr>
                            <?
                        	}
                        	else
                        	{
                        	?>
                            <tr class="added_row">
                                <td><input type="text" name="txtrollno_<? echo $i; ?>" id="txtrollno_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $row['ROLL_NO']; ?>" /></td>
                                <td align="right">
                                	<? echo $row['ROLL_WEIGHT']; ?>
                                	<input type="hidden" name="txtrollweight_<? echo $i; ?>" id="txtrollweight_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $row['ROLL_WEIGHT']; ?>" />
                                </td>
                                <td align="right">
                                	<input type="text" name="txtqtypcs_<? echo $i; ?>" id="txtqtypcs_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $row['QTY_IN_PCS']; ?>" onKeyUp="fnc_row_sum()" />
                                </td>
                                <td align="right"><input type="text" name="txtqtysize_<? echo $i; ?>" id="txtqtysize_<? echo $i; ?>" class="text_boxes" style="width:90px;" value="<? echo $row['QTY_IN_SIZE']; ?>" /></td>
                                <td align="right"><input type="text" name="txtbarcode_<? echo $i; ?>" id="txtbarcode_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $row['BARCODE_NO']; ?>" /></td>
                            </tr>
                            <?
                        	}

							$total_roll_weight += $row['ROLL_WEIGHT'];
							$total_qty_pcs += $row['QTY_IN_PCS'];
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th>Total</th>
                        <th><input type="text" name="txttotalrollweight" id="txttotalrollweight" class="text_boxes" style="width:90px; text-align:right;" value="<? echo $total_roll_weight;?>" readonly /></th>
                        <th><input type="text" name="txttotalqtypcs" id="txttotalqtypcs" class="text_boxes" style="width:90px; text-align:right;" value="<? echo $total_qty_pcs;?>" readonly /></th>
                        <th colspan="2"></th>
                    </tr>
                	<tr>
                        <th colspan="5" align="center" valign="top" class="button_container">
                            <input type="button" name="print" id="print" class="formbutton" value="Print" onClick="func_print('<? echo $mst_id; ?>')" style="width:70px"/>
                            <input type="button" name="close" id="main_close" class="formbutton" value="Close" onClick="func_onclose()" style="width:100px;"/>
                            <input type="hidden" name="save_data_roll" id="save_data_roll" class="text_boxes" />
                            <input type="hidden" name="max_roll_weight_pop" id="max_roll_weight_pop" value="<? echo $max_roll_weight;?>" class="text_boxes" />
                            <input type="hidden" name="tube_ref_qnty" id="tube_ref_qnty" value="<? echo $tube_ref_qty;?>" class="text_boxes" />
                        </th>
                    </tr>
                </tfoot>
            </table>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "actn_yarn_popup_07022022") 
{
	echo load_html_head_contents("Yarn Popup", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		var permission = '<? echo $permission; ?>';
		//func_add_yrn_tr
		function func_add_yrn_tr()
		{
			var i = $('#tbd_yrn tr').length + 1;			
			$("#tbd_yrn tr:last").clone().find('input').each(function() {
				$(this).attr({
				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
				'value': function(_, value) { return value }              
				});
			}).end().appendTo("#tbd_yrn");
			
			$('#yrnincrease_'+i).removeAttr("onClick").attr("onClick","func_add_yrn_tr("+i+");");
			$('#yrndecrease_'+i).removeAttr("onClick").attr("onClick","func_delete_yrn_tr("+i+");");
		}
		
		//func_delete_yrn_tr
		function func_delete_yrn_tr()
		{
			var i = $('#tbd_yrn tr').length + 1;			
			if(i != 1)
			{
				var r=confirm("Are you sure want to remove last row?");
				if (r==true)
				{
					$('#tbd_yrn tr:last').remove();
				}
			}
		}
		
		//func_add_roll_tr
		function func_add_roll_tr()
		{
			var i = $('#tbd_roll tr').length + 1;			
			$("#tbd_roll tr:last").clone().find('input').each(function() {
				$(this).attr({
				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
				'value': function(_, value) { return value }              
				});
			}).end().appendTo("#tbd_roll");
			
			$('#rollincrease_'+i).removeAttr("onClick").attr("onClick","func_add_roll_tr("+i+");");
			$('#rolldecrease_'+i).removeAttr("onClick").attr("onClick","func_delete_roll_tr("+i+");");
		}
		
		//func_delete_roll_tr
		function func_delete_roll_tr()
		{
			var i = $('#tbd_roll tr').length + 1;
			if(i != 1)
			{
				var r=confirm("Are you sure want to remove last row?");
				if (r==true)
				{
					$('#tbd_roll tr:last').remove();
				}
			}
		}
		
		//func_onclose
		function func_onclose()
		{
			var yrnRow = $('#tbd_yrn tr').length;
			var rollRow = $('#tbd_roll tr').length;
			
			var yrn_data = '';
			var y = 1;
			for(y; y <= yrnRow; y++)
			{
				if($('#txtyrnorigin_'+r).val() != '' && $('#txtyrnlot_'+r).val() != '' && $('#txtyrncount_'+r).val() != '')
				{
					if(yrn_data != '')
					{
						yrn_data += '***';
					}
					
					yrn_data += $('#txtyrnorigin_'+y).val()+'**'+$('#txtyrnlot_'+y).val()+'**'+$('#txtyrncount_'+y).val();
				}
			}
			
			var roll_data = '';
			var r = 1;
			for(r; r <= rollRow; r++)
			{
				if($('#txtrollno_'+r).val()*1 > 0 && $('#txtrollweight_'+r).val()*1 > 0 && $('#txtqtypcs_'+r).val()*1 > 0 && $('#txtqtysize_'+r).val()*1 > 0 && $('#txtbarcode_'+r).val()*1 > 0)
				{
					if(roll_data != '')
					{
						roll_data += '***';
					}
					
					roll_data += $('#txtrollno_'+r).val()+'**'+$('#txtrollweight_'+r).val()+'**'+$('#txtqtypcs_'+r).val()+'**'+$('#txtqtysize_'+r).val()+'**'+$('#txtbarcode_'+r).val();
				}
			}
			
			$('#save_data_yrn').val(yrn_data);
			$('#save_data_roll').val(roll_data);
			parent.emailwindow.hide();
		}
		
		//func_print
		function func_print(ref_id)
		{
			print_report(ref_id, "print", "program_wise_ref_creation_controller");
		}
	</script>
</head>
<body>
	<div align="center">
		<?
		//echo load_freeze_divs("../../../", $permission, 1);
		$color_dtls = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
		
		//for yarn
		$sql_yrn = "SELECT MST_ID, YARN_ORIGIN, YARN_LOT, YARN_COUNT FROM PPL_REFERENCE_CREATION_YARN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND MST_ID = ".$mst_id;
		$sql_yrn_rslt = sql_select($sql_yrn);
		
		//for roll
		$sql_roll = "SELECT MST_ID, ROLL_NO, ROLL_WEIGHT, QTY_IN_PCS, QTY_IN_SIZE, BARCODE_NO FROM PPL_REFERENCE_CREATION_ROLL WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND MST_ID = ".$mst_id;
		$sql_roll_rslt = sql_select($sql_roll);
		
		//for program info
		$sql_prog = "SELECT B.COLOR_ID, C.FABRIC_DESC, D.ID FROM PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, PPL_REFERENCE_CREATION D WHERE B.ID = C.DTLS_ID AND B.MST_ID = C.MST_ID AND C.DTLS_ID = D.PROGRAM_NO AND B.IS_SALES = 1 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND D.STATUS_ACTIVE = 1 AND D.IS_DELETED = 0 AND D.ID = ".$mst_id;
		$sql_prog_rslt = sql_select($sql_prog);
		$prog_data = array();
		foreach($sql_prog_rslt as $row)
		{
			$prog_data[$row['ID']]['COLOR_ID'] = $color_dtls[$row['COLOR_ID']];
			$prog_data[$row['ID']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
		}
		?>
		<form name="yrn_1" id="yrn_1">
			<!--<fieldset style="width:550px; margin-left:10px; float:left;">
            	<legend>Yarn Information</legend>-->
				<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="570" align="center" id="tbl_yrn" style="margin-top:5px; float:left;">
                    <thead>
                    	<tr>
                            <th colspan="3">Tube/Ref. No : <? echo $tube_ref_no; ?></th>
                            <th colspan="3">Tube/Ref. Qty : <? echo $tube_ref_qty; ?></th>
                        </tr>
                        <tr>
                        	<th colspan="6">Yarn Information</th>
                        </tr>
                    	<tr>
                            <th width="100">Yarn Origin</th>
                            <th width="100">Lot</th>
                            <th width="70">Count</th>
                            <th width="100">Composition</th>
                            <th width="100">Color</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tbd_yrn">
                    	<?
                        if(empty($sql_yrn_rslt))
						{
							//for program info
							$sql_prog = "SELECT B.COLOR_ID, C.FABRIC_DESC FROM PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C WHERE B.ID = C.DTLS_ID AND B.MST_ID = C.MST_ID AND B.IS_SALES = 1 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND B.ID = ".$prog_no;
							$sql_prog_rslt = sql_select($sql_prog);
							$prog_data = array();
							foreach($sql_prog_rslt as $row)
							{
								$color = $color_dtls[$row['COLOR_ID']];
								$fabric_desc = $row['FABRIC_DESC'];
							}
							
							?>
							<tr>
								<td><input type="text" name="txtyrnorigin_1" id="txtyrnorigin_1" class="text_boxes" style="width:90px;" /></td>
								<td><input type="text" name="txtyrnlot_1" id="txtyrnlot_1" class="text_boxes" style="width:90px;" /></td>
								<td><input type="text" name="txtyrncount_1" id="txtyrncount_1" class="text_boxes" style="width:60px;" /></td>
								<td><input type="text" name="txtyrncomposition_1" id="txtyrncomposition_1" class="text_boxes" style="width:90px;" value="<? echo $fabric_desc; ?>" /></td>
								<td><input type="text" name="txtyrncolor_1" id="txtyrncolor_1" class="text_boxes" style="width:90px;" value="<? echo $color; ?>" /></td>
								<td>
									<input type="button" id="yrnincrease_1" name="yrnincrease_1" style="width:25px" class="formbutton" value="+" onClick="func_add_yrn_tr()" />
									<input type="button" id="yrndecrease_1" name="yrndecrease_1" style="width:25px" class="formbutton" value="-" onClick="func_delete_yrn_tr()" />
								</td>
							</tr>
							<?
						}
						else
						{
							$i = 0;
							foreach($sql_yrn_rslt as $row)
							{
								$i++;
								$row['COLOR_ID'] = $prog_data[$row['MST_ID']]['COLOR_ID'];
								$row['FABRIC_DESC'] = $prog_data[$row['MST_ID']]['FABRIC_DESC'];
								?>
                                <tr>
                                    <td><input type="text" name="txtyrnorigin_<? echo $i; ?>" id="txtyrnorigin_<? echo $i; ?>" class="text_boxes" style="width:90px;" value="<? echo $row['YARN_ORIGIN']; ?>" /></td>
                                    <td><input type="text" name="txtyrnlot_<? echo $i; ?>" id="txtyrnlot_<? echo $i; ?>" class="text_boxes" style="width:90px;" value="<? echo $row['YARN_LOT']; ?>" /></td>
                                    <td><input type="text" name="txtyrncount_<? echo $i; ?>" id="txtyrncount_<? echo $i; ?>" class="text_boxes" style="width:60px;" value="<? echo $row['YARN_COUNT']; ?>" /></td>
                                    <td><input type="text" name="txtyrncomposition_<? echo $i; ?>" id="txtyrncomposition_<? echo $i; ?>" class="text_boxes" style="width:90px;" value="<? echo $row['FABRIC_DESC']; ?>" /></td>
                                    <td><input type="text" name="txtyrncolor_<? echo $i; ?>" id="txtyrncolor_<? echo $i; ?>" class="text_boxes" style="width:90px;" value="<? echo $row['COLOR_ID']; ?>" /></td>
                                    <td>
                                        <input type="button" id="yrnincrease_<? echo $i; ?>" name="yrnincrease_<? echo $i; ?>" style="width:25px" class="formbutton" value="+" onClick="func_add_yrn_tr()" />
                                        <input type="button" id="yrndecrease_<? echo $i; ?>" name="yrndecrease_<? echo $i; ?>" style="width:25px" class="formbutton" value="-" onClick="func_delete_yrn_tr()" />
                                    </td>
                                </tr>
                                <?								
							}	
						}
						?>
                    </tbody>
                </table>
            <!--</fieldset>
			<fieldset style="width:250px; margin-top:10px; margin-left:10px; float:left;">
            	<legend>Roll Information</legend>-->
				<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="570" align="center" id="tbl_roll" style="margin-top: 10px; float:left;">
                    <thead>
                    	<tr>
                        	<th colspan="6">Roll Information</th>
                        </tr>
                    	<tr>
                            <th width="50">Roll No</th>
                            <th width="100">Roll No Weight[Kg]</th>
                            <th width="100">Qty. In Pcs</th>
                            <th width="100">Qty. In Size</th>
                            <th width="100">Barcode</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tbd_roll">
                    	<?
                        if(empty($sql_yrn_rslt))
						{
							?>
                            <tr>
                                <td><input type="text" name="txtrollno_1" id="txtrollno_1" class="text_boxes_numeric" style="width:40px;" /></td>
                                <td><input type="text" name="txtrollweight_1" id="txtrollweight_1" class="text_boxes_numeric" style="width:90px;" /></td>
                                <td><input type="text" name="txtqtypcs_1" id="txtqtypcs_1" class="text_boxes_numeric" style="width:90px;" /></td>
                                <td><input type="text" name="txtqtysize_1" id="txtqtysize_1" class="text_boxes_numeric" style="width:90px;" /></td>
                                <td><input type="text" name="txtbarcode_1" id="txtbarcode_1" class="text_boxes_numeric" style="width:90px;" /></td>
                                <td>
                                    <input type="button" id="rollincrease_1" name="rollincrease_1" style="width:25px" class="formbutton" value="+" onClick="func_add_roll_tr()" />
                                    <input type="button" id="rolldecrease_1" name="rolldecrease_1" style="width:25px" class="formbutton" value="-" onClick="func_delete_roll_tr()" />
                                </td>
                            </tr>
                            <?
						}
						else
						{
							$i = 0;
							foreach($sql_roll_rslt as $row)
							{
								$i++;
								?>
								<tr>
									<td><input type="text" name="txtrollno_<? echo $i; ?>" id="txtrollno_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $row['ROLL_NO']; ?>" /></td>
									<td><input type="text" name="txtrollweight_<? echo $i; ?>" id="txtrollweight_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $row['ROLL_WEIGHT']; ?>" /></td>
									<td><input type="text" name="txtqtypcs_<? echo $i; ?>" id="txtqtypcs_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $row['QTY_IN_PCS']; ?>" /></td>
									<td><input type="text" name="txtqtysize_<? echo $i; ?>" id="txtqtysize_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $row['QTY_IN_SIZE']; ?>" /></td>
									<td><input type="text" name="txtbarcode_<? echo $i; ?>" id="txtbarcode_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo $row['BARCODE_NO']; ?>" /></td>
									<td>
										<input type="button" id="rollincrease_<? echo $i; ?>" name="rollincrease_<? echo $i; ?>" style="width:25px" class="formbutton" value="+" onClick="func_add_roll_tr()" />
										<input type="button" id="rolldecrease_<? echo $i; ?>" name="rolldecrease_<? echo $i; ?>" style="width:25px" class="formbutton" value="-" onClick="func_delete_roll_tr()" />
									</td>
								</tr>
								<?
							}
						}
						?>
                    </tbody>
                    <tfoot>
						<td colspan="6" align="center" valign="top" class="button_container">
							<input type="button" name="print" id="print" class="formbutton" value="Print" onClick="func_print('<? echo $mst_id; ?>')" style="width:70px"/>
                            <input type="button" name="close" id="main_close" class="formbutton" value="Close" onClick="func_onclose()" style="width:100px;"/>
							<input type="hidden" name="save_data_yrn" id="save_data_yrn" class="text_boxes" />
							<input type="hidden" name="save_data_roll" id="save_data_roll" class="text_boxes" />
						</td>
                    </tfoot>
                </table>
            <!--</fieldset>-->
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//actn_machine_popup
if ($action == 'actn_machine_popup')
{
	echo load_html_head_contents("Machine Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array();
		var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = $('#tbl_list_search tbody tr').length;
			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++)
			{
				js_set_value(i);
			}
		}

		function toggle(x, origColor)
		{
			var newColor = 'yellow';
			if (x.style)
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		//js_set_value
		function js_set_value(str) 
		{
			toggle(document.getElementById('search' + str), '#FFFFCC');
			if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1)
			{
				selected_id.push($('#txt_individual_id' + str).val());
				selected_name.push($('#txt_individual' + str).val());
			}
			else
			{
				for (var i = 0; i < selected_id.length; i++)
				{
					if (selected_id[i] == $('#txt_individual_id' + str).val())
					{
						break;
					}
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			
			//var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++)
			{
				//id += selected_id[i] + ',';
				name += selected_name[i]+'*';
			}
			//id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			//$('#txt_selected_id').val(id);
			$('#hidden_machine_data').val(name);
			parent.emailwindow.hide();
		}
		
		function func_close()
		{
			//$('#hidden_machine_data').val('hello');
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:600px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:600px; margin-top:10px; margin-left:20px">
				<div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="450" class="rpt_table">
						<thead>
							<th width="30">SL</th>
							<th width="100">M/C No</th>
							<th width="100">Capacity</th>
							<th width="100">Effi%</th>
							<th>No. of Tube</th>
							<input type="hidden" name="hidden_machine_data" id="hidden_machine_data" class="text_boxes" value="">
						</thead>
					</table>
					<div style="width:450px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="430" class="rpt_table" id="tbl_list_search">
							<tbody>
							<?
							$sql = "SELECT ID, MACHINE_NO, PROD_CAPACITY, EFFICIENCY, NO_OF_FEEDER FROM LIB_MACHINE_NAME WHERE CATEGORY_ID = 2 AND COMPANY_ID = ".$company_id;
							//echo $sql;
							$sql_rslt = sql_select($sql);
							$data_arr = array();
							foreach ($sql_rslt as $row) 
							{
								$data_arr[$row['MACHINE_NO']]['ID'] = $row['ID'];
								$data_arr[$row['MACHINE_NO']]['MACHINE_NO'] = $row['MACHINE_NO'];
								$data_arr[$row['MACHINE_NO']]['PROD_CAPACITY'] = $row['PROD_CAPACITY'];
								$data_arr[$row['MACHINE_NO']]['EFFICIENCY'] = $row['EFFICIENCY'];
								$data_arr[$row['MACHINE_NO']]['NO_OF_FEEDER'] = $row['NO_OF_FEEDER'];
							}

							$i = 0;
							foreach ($data_arr as $row)
							{
								$i++;								
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
									
								//for capacity per tube
								$capacity_per_tube = decimal_format(((($row['PROD_CAPACITY']*$row['EFFICIENCY'])/100)/$row['NO_OF_FEEDER']), '1', ',');
									
								$str = $row['ID'].'__'.$row['MACHINE_NO'].'__'.$row['PROD_CAPACITY'].'__'.$row['EFFICIENCY'].'__'.$row['NO_OF_FEEDER'].'__'.$capacity_per_tube;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i; ?>" onClick="js_set_value('<? echo $i; ?>')">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="100" align="center"><? echo $row['MACHINE_NO']; ?></td>
									<td width="100" align="center"><? echo $row['PROD_CAPACITY']; ?></td>
									<td width="100" align="center"><? echo $row['EFFICIENCY']; ?></td>
									<td align="center">
										<? echo $row['NO_OF_FEEDER']; ?>
                                        <input type="hidden" name="txt_individual_id[]" id="txt_individual_id<?php echo $i; ?>" value="<? echo $row['ID']; ?>"/>
                                        <input type="hidden" name="txt_individual[]" id="txt_individual<?php echo $i; ?>" value="<? echo $str; ?>"/>
                                    </td>
                                </tr>
                                <?
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!--<div style="width:100%; margin-left:10px; margin-top:5px">
                <div style="width:43%; float:left" align="left">
                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();"/>Check/Uncheck All
                </div>
                <div style="width:57%; float:left" align="left">
                    <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px"/>
                </div>
            </div>-->        
        </fieldset>
    </form>
    </div>
</body>
<script>
	setFilterGrid('tbl_list_search', -1);
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action == "check_knit_production")
{
	$data_arr = explode("**", $data);
	$tube_ref_nos_str = $data_arr[0];
	$batch_no = $data_arr[1];
	$tube_ref_nos = implode(",",explode("__",$tube_ref_nos_str));

	$production_batch_sql  = sql_select("select  a.batch_no, d.job_no , e.recv_number from ppl_reference_creation a, ppl_planning_entry_plan_dtls b, pro_roll_details c, fabric_sales_order_mst d, inv_receive_master e where a.program_no=b.dtls_id and a.batch_no=c.batch_no and b.po_id=c.po_breakdown_id and b.po_id=d.id and c.mst_id=e.id and c.entry_form=2 and e.entry_form=2 and a.status_active=1 and b.status_active=1 and c.tube_ref_no in ($tube_ref_nos) and c.batch_no ='$batch_no' group by a.batch_no, d.job_no, e.recv_number");

	if(!empty($production_batch_sql))
	{
		echo "1**Batch found in production.\nBatch no :".$production_batch_sql[0][csf("batch_no")]."\nproduction no :".$production_batch_sql[0][csf("recv_number")];
		die;
	}else{
		echo "0";
	}
}

//actn_save_update_delete
if($action == 'actn_save_update_delete')
{//echo "10**";die;
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$data_str = str_replace("'", "", $data_str);
	$color_arr  = return_library_array("select color_name,id from lib_color","id","color_name");
	if ($operation == 0)
	{
		$con = connect();
		$field = "ID,PROGRAM_NO,PLANNED_DATE,BATCH_NO,MACHINE_ID,MACHINE_CAPACITY,MACHINE_EFFICIENCY,NO_OF_TUBE,CAPACITY_PER_TUBE,REFERENCE_NO,REFERENCE_QTY,INSERTED_BY,INSERT_DATE";
		$update_field = "PLANNED_DATE*BATCH_NO*MACHINE_ID*MACHINE_CAPACITY*MACHINE_EFFICIENCY*NO_OF_TUBE*CAPACITY_PER_TUBE*REFERENCE_NO*REFERENCE_QTY*UPDATED_BY*UPDATE_DATE";
		$yrn_field ="ID, MST_ID, YARN_ORIGIN, YARN_LOT, YARN_COUNT, INSERTED_BY, INSERT_DATE";
		$roll_field ="ID, MST_ID, ROLL_NO, ROLL_WEIGHT, QTY_IN_PCS, QTY_IN_SIZE, BARCODE_NO, INSERTED_BY, INSERT_DATE";
		
		$values = '';
		$yrn_values = '';
		$roll_values = '';
		$update_values = array();
		$dlt_id = array();
		if ($data_str != "")
		{
			/*
			|--------------------------------------------------------------------------
			| per tube/reference qty checking
			|--------------------------------------------------------------------------
			|
			*/
			$data_arr = explode("__", $data_str);
			$is_error = array();
			$prog_ref_qty = array();
			$prog_no_arr = array();
			$insert_duplicate_check_arr = array();
			$update_duplicate_check_arr = array();
			$all_fso_no_arr = array();
			$pre_program_batch_data = array();
			for ($i = 0; $i < count($data_arr); $i++)
			{
				$exp_data = array();
				$exp_data = explode(",", $data_arr[$i]);
				$PROGRAM_NO = $exp_data[0];
				$BATCH_NO = $exp_data[2];
				$CAPACITY_PER_TUBE = $exp_data[4];
				$REFERENCE_QTY = $exp_data[6];
				$UPDATE_ID = $exp_data[7];
				$prog_ref_qty[$PROGRAM_NO] += $REFERENCE_QTY;
				$prog_no_arr[$PROGRAM_NO] = $PROGRAM_NO;
				
				if($REFERENCE_QTY > $CAPACITY_PER_TUBE)
				{
					$is_error['per_tube_qty'] = 1;
				}

				$FSO_NO = $exp_data[12];
				$COLOR_ID = $exp_data[13];
				$all_fso_no_arr["'".$FSO_NO."'"] = "'".$FSO_NO."'";
				//$batch_fso_color_arr[$COLOR_ID][$BATCH_NO]["FSO_NO"] = $FSO_NO;

				//for batch no
				/*if($UPDATE_ID == '')
				{
					$insert_duplicate_check_arr['batch_no'][$BATCH_NO] = $BATCH_NO;
				}
				else
				{
					$update_duplicate_check_arr['update_id'][$UPDATE_ID] = $UPDATE_ID;
					$update_duplicate_check_arr['batch_no'][$BATCH_NO] = $BATCH_NO;
				}*/
			}
			
			if(!empty($is_error))
			{
				if($is_error['per_tube_qty'] == 1)
				{
					oci_rollback($con);
					echo '11**Tube/Reference qty can not be greater than capacity qty.';
					disconnect($con);
					die;
				}
			}
			//end per tube/reference qty checking
			
			/*
			|--------------------------------------------------------------------------
			| duplicate batch no checking
			|--------------------------------------------------------------------------
			|
			*/
			$all_fso_no_arr = array_filter($all_fso_no_arr);
			if(!empty($all_fso_no_arr))
			{
				$all_fso_nos = implode(",", $all_fso_no_arr);
				$all_fso_no_cond=""; $fsoCond="";
				$all_sales_order_cond=""; $salesCond="";
				if($db_type==2 && count($all_fso_no_arr)>999)
				{
					$all_fso_no_arr_chunk=array_chunk($all_fso_no_arr,999) ;
					foreach($all_fso_no_arr_chunk as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$fsoCond.=" d.job_no in($chunk_arr_value) or ";
						$salesCond.=" sales_order_no in($chunk_arr_value) or ";
					}

					$all_fso_no_cond.=" and (".chop($fsoCond,'or ').")";
					$all_sales_order_cond.=" and (".chop($salesCond,'or ').")";
				}
				else
				{
					$all_fso_no_cond=" and d.job_no in($all_fso_nos)";
					$all_sales_order_cond=" and sales_order_no in($all_fso_nos)";
				}

				$pre_program_batch_sql  = sql_select("select  a.batch_no, b.color_id, d.job_no from ppl_reference_creation a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, fabric_sales_order_mst d where a.program_no=b.id and b.id=c.dtls_id and c.po_id=d.id and b.status_active=1 and c.status_active=1 $all_fso_no_cond");
				foreach ($pre_program_batch_sql as $val) 
				{
					//$pre_program_batch_data[$val[csf("color_id")]][$val[csf("batch_no")]][$val[csf("job_no")]] = $val[csf("job_no")];
					$pre_program_batch_data[$val[csf("batch_no")]][] = "'".$val[csf("color_id")]."*".$val[csf("job_no")]."'";
				}

				$pre_batch_sql  = sql_select("select batch_no, sales_order_no, color_id from pro_batch_create_mst where entry_form=0 and status_active=1 and is_deleted=0 and is_sales=1 $all_sales_order_cond");

				foreach ($pre_batch_sql as $val) 
				{
					//$pre_program_batch_data[$val[csf("color_id")]][$val[csf("batch_no")]][$val[csf("sales_order_no")]] = $val[csf("sales_order_no")];
					$pre_program_batch_data[$val[csf("batch_no")]][] = "'".$val[csf("color_id")]."*".$val[csf("sales_order_no")]."'";
				}

				$production_batch_sql  = sql_select("select  a.batch_no, d.job_no , e.recv_number from ppl_reference_creation a, ppl_planning_entry_plan_dtls b, pro_roll_details c, fabric_sales_order_mst d, inv_receive_master e where a.program_no=b.dtls_id and a.batch_no=c.batch_no and b.po_id=c.po_breakdown_id and b.po_id=d.id and c.mst_id=e.id and c.entry_form=2 and e.entry_form=2 and a.status_active=1 and b.status_active=1 $all_fso_no_cond group by a.batch_no, d.job_no, e.recv_number");

				if(!empty($production_batch_sql))
				{
					/* echo "11**Batch found in production.\nBatch no :".$production_batch_sql[0][csf("batch_no")]."\nproduction no :".$production_batch_sql[0][csf("recv_number")];
					oci_rollback($con);
					disconnect($con);
					die; */

					foreach ($production_batch_sql  as  $val) 
					{
						$knit_production_batch_arr[$val[csf("batch_no")]] = $val[csf("recv_number")];
					}
				}
				//echo "10**==";print_r($knit_production_batch_arr);die;
			}

			/*echo "10**"."select  a.batch_no, b.color_id, d.job_no from ppl_reference_creation a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, fabric_sales_order_mst d where a.program_no=b.id and b.id=c.dtls_id and c.po_id=d.id and b.status_active=1 and c.status_active=1 $all_fso_no_cond <br><br>"."select batch_no, sales_order_no, color_id from pro_batch_create_mst where entry_form=0 and status_active=1 and is_deleted=0 and is_sales=1 $all_sales_order_cond";
			print_r($pre_program_batch_data);
			die;*/


			//for insert
			/*
				if(!empty($insert_duplicate_check_arr))
				{
					if (is_duplicate_field( 'BATCH_NO', 'PPL_REFERENCE_CREATION', "BATCH_NO IN('".implode("','",$update_duplicate_check_arr['batch_no'])."') AND STATUS_ACTIVE = 1 AND IS_DELETED = 0" ) == 1)
					{
						$is_error['batch_no'] = 1;
					}
				}
				
				//for update
				if(!empty($update_duplicate_check_arr))
				{
					if (is_duplicate_field( 'BATCH_NO', 'PPL_REFERENCE_CREATION', "BATCH_NO IN('".implode("','", $insert_duplicate_check_arr['batch_no'])."') AND ID NOT IN(".implode(',', $update_duplicate_check_arr['update_id']).") AND STATUS_ACTIVE = 1 AND IS_DELETED = 0" ) == 1)
					{
						$is_error['batch_no'] = 1;
					}
				}
				
				//for new insert
				$duplicate_batch_no_check = array();
				$exp_batch_no = explode(",", $batch_no_str);
				for ($z = 0; $z < count($exp_batch_no); $z++)
				{
					if($duplicate_batch_no_check[$exp_batch_no[$z]] != $exp_batch_no[$z])
					{
						$duplicate_batch_no_check[$exp_batch_no[$z]] = $exp_batch_no[$z];
					}
					else
					{
						$is_error['batch_no'] = 1;
					}
				}
				
				if(!empty($is_error))
				{
					if($is_error['batch_no'] == 1)
					{
						oci_rollback($con);
						echo '11**Duplicate batch no.';
						disconnect($con);
						die;
					}
				}
			*/
			//end duplicate batch no checking
			
			/*
			|--------------------------------------------------------------------------
			| program qty checking
			|--------------------------------------------------------------------------
			|
			*/
			$sql_prog = "SELECT C.DTLS_ID, C.PROGRAM_QNTY FROM PPL_PLANNING_ENTRY_PLAN_DTLS C WHERE C.IS_SALES = 1 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND C.DTLS_ID IN(".implode(',', $prog_no_arr).")";
			$sql_prog_rslt = sql_select($sql_prog);
			$prog_data_arr = array();
			foreach($sql_prog_rslt as $row)
			{
				$prog_data_arr[$row['DTLS_ID']] += $row['PROGRAM_QNTY'];
			}
			
			for ($i = 0; $i < count($data_arr); $i++)
			{
				$exp_data = array();
				$exp_data = explode(",", $data_arr[$i]);
				$PROGRAM_NO = $exp_data[0];
				if(number_format($prog_ref_qty[$PROGRAM_NO], 2, '.', '') > number_format($prog_data_arr[$PROGRAM_NO], 2, '.', ''))
				{
					$is_error['ref_qty'] = 1;
				}
			}
			
			if(!empty($is_error))
			{
				if($is_error['ref_qty'] == 1)
				{
					oci_rollback($con);
					echo '11**Total tube/reference qty can not be greater than program qty.'.number_format($prog_ref_qty[$PROGRAM_NO], 2, '.', '')."===".number_format($prog_data_arr[$PROGRAM_NO], 2, '.', '');
					disconnect($con);
					die;
				}
			}
			//end program qty checking

			for ($i = 0; $i < count($data_arr); $i++)
			{
				$exp_data = array();
				$exp_data = explode(",", $data_arr[$i]);
				$PROGRAM_NO = $exp_data[0];
				$PLANNED_DATE = change_date_format($exp_data[1], '', '', 1);
				$BATCH_NO = $exp_data[2];
				$MACHINE_ID = $exp_data[3];
				$CAPACITY_PER_TUBE = $exp_data[4];
				$REFERENCE_NO = $exp_data[5];
				$REFERENCE_QTY = $exp_data[6];
				$UPDATE_ID = $exp_data[7];
				$MACHINE_CAPACITY = $exp_data[8];
				$MACHINE_EFFICIENCY = $exp_data[9];
				$NO_OF_TUBE = $exp_data[10];
				$CAPACITY_PER_TUBE = $exp_data[11];
				$FSO_NO = $exp_data[12];
				$COLOR_ID = $exp_data[13];
				$ROLL_DATA = $exp_data[14];

				$PRE_PLANNED_DATE = $exp_data[15];
				$PRE_BATCH_NO = $exp_data[16];
				$PRE_CAPACITY_PER_TUBE = $exp_data[17];
				
				//echo "10**dd".$PRE_BATCH_NO;oci_rollback($con);die;
				if($knit_production_batch_arr[$PRE_BATCH_NO] != "")
				{
					//echo "10**dd";oci_rollback($con);die;
					if($PRE_BATCH_NO != $BATCH_NO || date("d-m-Y",strtotime($PRE_PLANNED_DATE)) != date("d-m-Y",strtotime($PLANNED_DATE)) || number_format($PRE_CAPACITY_PER_TUBE,2,".","") != number_format($CAPACITY_PER_TUBE,2,".",""))
					{
						echo "11**Batch found in production. \nPlanned Date, Batch no, M/C no, Capacity Per Tube Change not allowed.\nBatch no : ".$PRE_BATCH_NO."\nproduction no : ".$knit_production_batch_arr[$PRE_BATCH_NO]."\n\n"."$PRE_BATCH_NO != $BATCH_NO || $PRE_PLANNED_DATE != $PLANNED_DATE || $PRE_CAPACITY_PER_TUBE != $CAPACITY_PER_TUBE";
						oci_rollback($con);
						disconnect($con);
						die;
					}
				}

				if(!empty($pre_program_batch_data[$BATCH_NO]))
				{
					foreach ($pre_program_batch_data[$BATCH_NO] as $colorFSO ) 
					{
						$colorFSO_arr  = explode("*", str_replace("'", "", $colorFSO));
						if($colorFSO != "'".$COLOR_ID."*".$FSO_NO."'"){
							echo "11**Batch no attached in another fabric Sales order and color\nBatch No:".$BATCH_NO."\nFabric sales order : ".$colorFSO_arr[1]."\nColor : ".$color_arr[$colorFSO_arr[0]];
							oci_rollback($con);
							die;
						}
					}
				}
				$pre_program_batch_data[$BATCH_NO][] = "'".$COLOR_ID."*".$FSO_NO."'";

				//echo "10**";print_r($pre_program_batch_data);oci_rollback($con);die;

				if($UPDATE_ID == '')
				{
					$id= return_next_id_by_sequence('PPL_REFERENCE_CREATION_SEQ', 'PPL_REFERENCE_CREATION', $con );
					$mst_id = $id;
					if ($values != "") $values .= ",";
					$values .= "(".$id.",".$PROGRAM_NO.",'".$PLANNED_DATE."','".$BATCH_NO."',".$MACHINE_ID.",".$MACHINE_CAPACITY.",".$MACHINE_EFFICIENCY.",".$NO_OF_TUBE.",".$CAPACITY_PER_TUBE.",'".$REFERENCE_NO."',".$REFERENCE_QTY.",".$user_id.",'".$pc_date_time."')";
				}
				else
				{
					$update_id_arr[] = $UPDATE_ID;
					$mst_id = $UPDATE_ID;
					$dlt_id[$UPDATE_ID] = $UPDATE_ID;
					$update_values[$UPDATE_ID] = explode("*", ("'".$PLANNED_DATE."'*'".$BATCH_NO."'*".$MACHINE_ID."*".$MACHINE_CAPACITY."*".$MACHINE_EFFICIENCY."*".$NO_OF_TUBE."*".$CAPACITY_PER_TUBE."*'".$REFERENCE_NO."'*".$REFERENCE_QTY."*".$user_id."*'".$pc_date_time."'"));
				}
				
				//for roll info
				if($ROLL_DATA != '')
				{
					$exp_roll_data = array();
					$exp_roll_data = explode('!!!!', $ROLL_DATA);
					foreach($exp_roll_data as $rkey=>$rval)
					{
						$exp_roll = array();
						$exp_roll = explode('**', $rval);
						$roll_id= return_next_id_by_sequence('PPL_REFERENCE_CREATION_ROLL_SEQ', 'PPL_REFERENCE_CREATION_ROLL', $con );
						if ($roll_values != "") $roll_values .= ",";
						$roll_values .= "(".$roll_id.",".$mst_id.",'".$exp_roll[0]."','".$exp_roll[1]."','".$exp_roll[2]."','".$exp_roll[3]."','".$exp_roll[4]."',".$user_id.",'".$pc_date_time."')";
					}
				}
			}
		}
		
		/*
		|--------------------------------------------------------------------------
		| PPL_REFERENCE_CREATION
		| for insert
		|--------------------------------------------------------------------------
		|
		*/
		$insert_rslt = true;
		if($values != '')
		{
			$insert_rslt = sql_insert('PPL_REFERENCE_CREATION', $field, $values, 0);
		}
		
		/*
		|--------------------------------------------------------------------------
		| PPL_REFERENCE_CREATION
		| for update
		|--------------------------------------------------------------------------
		|
		*/
		$update_rslt = true;
		if (!empty($update_values))
		{
			$update_rslt = execute_query(bulk_update_sql_statement('PPL_REFERENCE_CREATION', 'ID', $update_field, $update_values, $update_id_arr));
		}
		
		/*
		|--------------------------------------------------------------------------
		| PPL_REFERENCE_CREATION_ROLL
		| for insert
		|--------------------------------------------------------------------------
		|
		*/
		if(!empty($dlt_id))
		{
			execute_query("DELETE FROM PPL_REFERENCE_CREATION_ROLL WHERE MST_ID IN(".implode(',',$dlt_id).")");
		}
		
		$roll_insert_rslt = true;
		if($roll_values != '')
		{
			$roll_insert_rslt = sql_insert('PPL_REFERENCE_CREATION_ROLL', $roll_field, $roll_values, 0);
		}
		//echo "10**";oci_rollback($con);die;
		/*oci_rollback($con);
		echo "10**".$insert_rslt.'='.$update_rslt.'='.$roll_insert_rslt;
		//echo "10**INSERT INTO PPL_REFERENCE_CREATION(".$field.") VALUES".$values;
		//echo "10**".bulk_update_sql_statement('PPL_REFERENCE_CREATION', 'ID', $update_field, $update_values, $update_id_arr);
		disconnect($con);
		die;*/

		if ($insert_rslt && $update_rslt && $roll_insert_rslt)
		{
			oci_commit($con);
			echo "0**";
		}
		else
		{
			oci_rollback($con);
			echo "10**";
		}
	}
	elseif($operation == 2)
	{
		$con = connect();
		$update_field = "STATUS_ACTIVE*IS_DELETED*UPDATED_BY*UPDATE_DATE";
		$data_arr = explode("__", $data_str);
		for ($i = 0; $i < count($data_arr); $i++)
		{
			$exp_data = array();
			$exp_data = explode(",", $data_arr[$i]);
			$UPDATE_ID = $exp_data[0];
			
			$update_id_arr[] = $UPDATE_ID;
			$update_values[$UPDATE_ID] = explode("*", ("0*1*".$user_id."*'".$pc_date_time."'"));
		}
		
		//check if production found or not

		$all_update_id_arr = array_filter($update_id_arr);
		if(!empty($all_update_id_arr))
		{
			$all_update_ids = implode(",", $all_update_id_arr);
			$all_id_cond=""; $idCond="";
			if($db_type==2 && count($all_update_id_arr)>999)
			{
				$all_update_id_arr_chunk=array_chunk($all_update_id_arr,999) ;
				foreach($all_update_id_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$idCond.=" a.id in($chunk_arr_value) or ";
				}

				$all_id_cond.=" and (".chop($idCond,'or ').")";
			}
			else
			{
				$all_id_cond=" and a.id in($all_update_ids)";
			}

			$production_data  = sql_select("select a.batch_no, b.mst_id, c.recv_number from ppl_reference_creation a, pro_roll_details b, inv_receive_master c where a.batch_no=b.batch_no and a.reference_no=b.tube_ref_no and b.entry_form=2 and b.mst_id=c.id $all_id_cond and a.status_active=1 and b.status_active=1 and c.status_active=1");

			if(!empty($production_data)){
				echo "20**Batch found in production.\Batch no :".$production_data[0][csf("batch_no")]."\nproduction no :".$production_data[0][csf("recv_number")];
				oci_rollback($con);
				disconnect($con);
				die;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| PPL_REFERENCE_CREATION
		| for update
		|--------------------------------------------------------------------------
		|
		*/
		$update_rslt = true;
		$update_rslt_roll = true;
		if (!empty($update_values))
		{
			$update_rslt = execute_query(bulk_update_sql_statement('PPL_REFERENCE_CREATION', 'ID', $update_field, $update_values, $update_id_arr));
			$update_rslt_roll = execute_query(bulk_update_sql_statement('PPL_REFERENCE_CREATION_ROLL', 'MST_ID', $update_field, $update_values, $update_id_arr));
		}
		
		if ($update_rslt && $update_rslt_roll)
		{
			oci_commit($con);
			echo "2**";
		}
		else
		{
			oci_rollback($con);
			echo "10**";
		}
	}
	
	disconnect($con);
	die;
}

//print
if($action == 'print')
{
	echo load_html_head_contents("Ref. Wise Pallet Sheet", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$mst_id = $data;
	$company_dtls = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$color_dtls = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$machine_dtls = return_library_array("select id, machine_no from lib_machine_name where category_id = 2", 'id', 'machine_no');
	$buyer_dtls = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$user_dtls = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
	$imge_arr = return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$supplier_dtls = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

	//for roll
	$sql_roll = "SELECT MST_ID, ROLL_NO, ROLL_WEIGHT, QTY_IN_PCS, QTY_IN_SIZE, BARCODE_NO FROM PPL_REFERENCE_CREATION_ROLL WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND MST_ID = ".$mst_id;
	$sql_roll_rslt = sql_select($sql_roll);
	
	//for prog reference
	$sql_ref = "SELECT A.BOOKING_NO, B.ID AS PROG_NO, B.COLOR_ID, B.KNITTING_SOURCE, B.KNITTING_PARTY, B.REMARKS, C.ID, C.BOOKING_NO, C.FABRIC_DESC, C.WIDTH_DIA_TYPE, C.GSM_WEIGHT, C.COLOR_TYPE_ID, C.PROGRAM_QNTY, D.ID AS REF_ID, D.PLANNED_DATE, D.BATCH_NO, D.MACHINE_ID, D.MACHINE_CAPACITY, D.MACHINE_EFFICIENCY, D.NO_OF_TUBE, D.CAPACITY_PER_TUBE, D.REFERENCE_NO, D.REFERENCE_QTY, D.INSERTED_BY, E.JOB_NO, E.COMPANY_ID, E.BUYER_ID, E.WITHIN_GROUP, E.PO_BUYER, E.DELIVERY_DATE FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, PPL_REFERENCE_CREATION D, FABRIC_SALES_ORDER_MST E WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND B.MST_ID = C.MST_ID AND B.ID = D.PROGRAM_NO AND C.DTLS_ID = D.PROGRAM_NO AND A.IS_SALES = 1 AND B.IS_SALES = 1 AND C.IS_SALES = 1 AND C.PO_ID=E.ID AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND D.STATUS_ACTIVE = 1 AND D.IS_DELETED = 0 AND D.ID IN(".$mst_id.") ORDER BY D.ID ASC";
	//echo $sql_ref;
	$sql_ref_rslt = sql_select($sql_ref);
	$duplicate_check = array();
	$prog_data = array();
	foreach($sql_ref_rslt as $row)
	{
		if($duplicate_check[$row['REF_ID']] != $row['REF_ID'])
		{
			$duplicate_check[$row['REF_ID']] = $row['REF_ID'];
			
			//for color
			$exp_color_arr = array();
			$color_arr = array();
			$exp_color_arr = explode(',',$row['COLOR_ID']);
			foreach($exp_color_arr as $key=>$val)
			{
				$color_arr[$val] = $color_dtls[$val];
			}
			//end for color

			$prog_data['COMPANYID'] = $row['COMPANY_ID'];
			$prog_data['COMPANY_ID'] = $company_dtls[$row['COMPANY_ID']];
			$prog_data['REFERENCE_NO'] = $row['REFERENCE_NO'];
			$prog_data['SALES_ORDER'] = $row['JOB_NO'];
			$prog_data['BATCH_NO'] = $row['BATCH_NO'];
			
			$prog_data['PROG_NO'] = $row['PROG_NO'];
			$prog_data['BOOKING_NO'] = $row['BOOKING_NO'];
			
			$prog_data['FABRIC_DESC'] = $row['FABRIC_DESC'];
			$prog_data['COLOR_ID'] = implode(', ', $color_arr);
			$prog_data['WIDTH_DIA_TYPE'] = $fabric_typee[$row['WIDTH_DIA_TYPE']];
			$prog_data['GSM_WEIGHT'] = $row['GSM_WEIGHT'];
			$prog_data['WIDTH_DIA_TYPE'] = $row['WIDTH_DIA_TYPE'];
			$prog_data['KNITTING_SOURCE'] = $knitting_source[$row['KNITTING_SOURCE']];
			
			//for knitting source
			if($row['KNITTING_SOURCE'] == 1)
			{
				$prog_data['KNITTING_PARTY'] = $company_dtls[$row['KNITTING_PARTY']];
			}
			else
			{
				$prog_data['KNITTING_PARTY'] = $supplier_dtls[$row['KNITTING_PARTY']];
			}
			
			$prog_data['REMARKS'] = $row['REMARKS'];
			$prog_data['MACHINE_ID'] = $machine_dtls[$row['MACHINE_ID']];

			if($row['WITHIN_GROUP']==2){
				$prog_data['BUYER_ID'] = $buyer_dtls[$row['BUYER_ID']];
			}else{
				$prog_data['BUYER_ID'] = $buyer_dtls[$row['PO_BUYER']];
			}
			$prog_data['DELIVERY_DATE'] = ($row['DELIVERY_DATE']!=''? date('d-m-Y', strtotime($row['DELIVERY_DATE'])):'');
			$prog_data['PLANNED_DATE'] = ($row['PLANNED_DATE']!=''? date('d-m-Y', strtotime($row['PLANNED_DATE'])):'');
			$prog_data['REFERENCE_QTY'] = $row['REFERENCE_QTY'];
			$prog_data['INSERTED_BY'] = $user_dtls[$row['INSERTED_BY']];
			$prog_data[$row['REF_ID']]['COLOR_ID'] = implode(', ', $color_arr);
			$prog_data[$row['REF_ID']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
		}
	}
	unset($sql_ref_rslt);
	
	//for booking type
	$sql_bkn = "SELECT BOOKING_TYPE, IS_SHORT FROM WO_BOOKING_MST WHERE BOOKING_NO = '".$prog_data['BOOKING_NO']."' AND STATUS_ACTIVE =1 AND IS_DELETED = 0";
	$sql_bkn_rslt = sql_select($sql_bkn);
	$bkn_type = '';
	foreach($sql_bkn_rslt as $row)
	{
		if ($row['BOOKING_TYPE'] == 4)
		{
			$bkn_type = 'Sample';
		}
		else
		{
			if ($row['IS_SHORT'] == 1)
			{
				$bkn_type = 'Short';
			}
			else
			{
				$bkn_type = 'Main';
			}
		}
	}
	unset($sql_bkn_rslt);
	//end for booking type
	
	//for requisition
	$sql_req = "SELECT A.REQUISITION_NO, B.ID, B.LOT, B.YARN_COUNT_ID, B.YARN_COMP_TYPE1ST, B.YARN_COMP_PERCENT1ST, B.YARN_COMP_TYPE2ND, B.YARN_COMP_PERCENT2ND, B.COLOR, B.BRAND FROM PPL_YARN_REQUISITION_ENTRY A, PRODUCT_DETAILS_MASTER B WHERE A.PROD_ID = B.ID AND A.KNIT_ID = ".$prog_data['PROG_NO']." AND A.STATUS_ACTIVE =1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE =1 AND B.IS_DELETED = 0";
	//echo $sql_req;
	$sql_req_rslt = sql_select($sql_req);
	$req_data = array();
	foreach($sql_req_rslt as $row)
	{
		$req_data[$row['ID']]['REQUISITION_NO'] = $row['REQUISITION_NO'];
		$req_data[$row['ID']]['LOT'] = $row['LOT'];
		$req_data[$row['ID']]['YARN_COUNT_ID'] = $count_arr[$row['YARN_COUNT_ID']];
		$req_data[$row['ID']]['COLOR'] = $color_dtls[$row['COLOR']];
		$req_data[$row['ID']]['BRAND'] = $brand_arr[$row['BRAND']];
		
		//for composition
		$arr = array();
		$arr['yarn_comp_type1st'] = $row['YARN_COMP_TYPE1ST'];
		$arr['yarn_comp_percent1st'] = $row['YARN_COMP_PERCENT1ST'];
		$arr['yarn_comp_type2nd'] = $row['YARN_COMP_TYPE2ND'];
		$arr['yarn_comp_percent2nd'] = $row['YARN_COMP_PERCENT2ND'];
		$req_data[$row['ID']]['COMPOSITION'] = get_composition($arr);
		//end for composition
	}
	unset($sql_req_rslt);
	//end for requisition no
	?>
    <style type="text/css">
		#tbl tbody tr td{
			padding-left:3px;
		}
		#tbl tbody tr{
			vertical-align:middle; 
			height:20px;
		}
	</style>
	<table border="0" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="800" align="center">
    	<caption>
        	<tr>
                <th width="70" align="right" rowspan="4"><img  src='../../<? echo $imge_arr[$prog_data['COMPANYID']]; ?>' height='100%' width='100%' /></th>
                <th width="730" valign="top">
				<span style="font-size:16px; font-weight:bold;"><? echo $prog_data['COMPANY_ID']; ?></span><br>
                <span style="font-size:13px;"><? echo show_company($prog_data['COMPANYID'], '', ''); ?></span>
                <span style="font-size:16px; font-weight:bold;"><br>Knitting Pallet Card<br>Tube/Ref. Wise Pallet Sheet</span>
                </th>
            </tr>
        </caption>
	</table>    
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="800" align="center" id="tbl">
    	<tbody>
        	<tr>
            	<td id="barcode_img_id" rowspan="3" colspan="3"></td>
                <td style="text-align:left;" width="100">Program By</td>
                <td style="text-align:left;" width="100"><? echo $prog_data['INSERTED_BY']; ?></td>
        	</tr>
        	<tr>
                <td style="text-align:left;">Print Date</td>
                <td style="text-align:left;"><? echo date('d-m-Y'); ?></td>
            </tr>
        	<tr>
                <td style="text-align:left;">Print Time</td>
                <td style="text-align:left;"><? echo date("h:ia"); ?></td>
            </tr>
        	<tr>
            	<td>Tube/Ref. No</td>
                <td><? echo $prog_data['REFERENCE_NO']; ?></td>
            	<td>Pallet No</td>
                <td colspan="2"></td>
            </tr>
        	<tr>
            	<td>FSO No</td>
                <td><? echo $prog_data['SALES_ORDER']; ?></td>
            	<td>Batch No</td>
                <td colspan="2"><? echo $prog_data['BATCH_NO']; ?></td>
            </tr>
        	<tr>
            	<td>Booking No</td>
                <td><? echo $prog_data['BOOKING_NO']; ?></td>
            	<td>Program No</td>
                <td colspan="2"><? echo $prog_data['PROG_NO']; ?></td>
            </tr>
        	<tr>
            	<td>Tube/Ref. Date</td>
                <td><? echo $prog_data['PLANNED_DATE']; ?></td>
            	<td>GSM</td>
                <td colspan="2"><? echo $prog_data['GSM_WEIGHT']; ?></td>
            </tr>
        	<tr>
            	<td>Buyer</td>
                <td><? echo $prog_data['BUYER_ID']; ?></td>
            	<td>Dia/ W. Type</td>
                <td colspan="2"><? echo $prog_data['WIDTH_DIA_TYPE']; ?></td>
            </tr>
        	<tr>
            	<td>Fabrication</td>
                <td><? echo $prog_data['FABRIC_DESC']; ?></td>
            	<td>Knitting Source</td>
                <td colspan="2"><? echo $prog_data['KNITTING_SOURCE']; ?></td>
            </tr>
        	<tr>
            	<td>Color</td>
                <td><? echo $prog_data['COLOR_ID']; ?></td>
            	<td>Knitting Party</td>
                <td colspan="2"><? echo $prog_data['KNITTING_PARTY']; ?></td>
            </tr>
        	<tr>
            	<td>FSO Delivery Date</td>
                <td><? echo $prog_data['DELIVERY_DATE']; ?></td>
            	<td>Fabric Headset-Y/N</td>
                <td colspan="2"><? //echo $prog_data['KNITTING_PARTY']; ?></td>
            </tr>
        	<tr>
            	<td>DYE M/C No</td>
                <td><? echo $prog_data['MACHINE_ID']; ?></td>
            	<td>Booking Type</td>
                <td colspan="2"><? echo $bkn_type; ?></td>
            </tr>
        	<tr>
            	<td>TOT WGTREQ [kg]</td>
                <td><? echo $prog_data['REFERENCE_QTY']; ?></td>
            	<td>Remarks</td>
                <td colspan="2"><? echo $prog_data['REMARKS']; ?></td>
            </tr>
        </tbody>
    </table>
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="800" align="center" style="margin-top:10px;" id="tbl">
    	<thead>
            <tr>
                <th width="100">Yarn Origin</th>
                <th width="80">Lot</th>
                <th width="50">Count</th>
                <th width="320">Composition</th>
                <th width="100">Requisition No</th>
                <th width="150">Color</th>
            </tr>
        </thead>
        <tbody>
        <?
		if(!empty($req_data))
		{
			foreach($req_data as $prd=>$row)
			{
				?>
				<tr>
					<td><? echo $row['BRAND']; ?></td>
					<td><? echo $row['LOT']; ?></td>
					<td><? echo $row['YARN_COUNT_ID']; ?></td>
					<td><? echo $row['COMPOSITION']; ?></td>
					<td><? echo $row['REQUISITION_NO']; ?></td>
					<td><? echo $row['COLOR']; ?></td>
				</tr>
				<?
			}
		}
		else
		{
			?>
            <tr>
            	<td colspan="6" align="center"><? echo get_empty_data_msg(); ?></td>
            </tr>
			<!--<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>-->
			<?
		}
		?>
        </tbody>
    </table>
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="800" align="center" style="margin-top:10px;" id="tbl">
    	<thead>
            <tr>
                <th width="80" rowspan="2">Roll No</th>
                <th width="80" rowspan="2">Roll Weight</th>
                <th width="160" colspan="2">Trims Weight(Kg)</th>
                <th width="120" rowspan="2">Barcode No</th>
                <th width="360" colspan="2">Hand Writing Area</th>
            </tr>
            <tr>
                <th width="80">(Qty. In Pcs)</th>
                <th width="80">(Qty. In Size)</th>
                <th width="180">Scanned Weight [Kg]</th>
                <th width="180">Shift</th>
            </tr>
        </thead>
        <tbody>
        <?
		foreach($sql_roll_rslt as $row)
		{
			?>
			<tr>
            	<td align="center"><? echo $row['ROLL_NO']; ?></td>
            	<td align="center"><? echo $row['ROLL_WEIGHT']; ?></td>
            	<td align="center"><? echo $row['QTY_IN_PCS']; ?></td>
            	<td align="center"><? echo $row['QTY_IN_SIZE']; ?></td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            </tr>
            <?
			$tot_roll_weight += $row['ROLL_WEIGHT'];
			$tot_qty_pcs += $row['QTY_IN_PCS'];
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
            	<th style="text-align:left;">Total Wgt. on Pallet (kg.)</th>
            	<th style="text-align:center;"><? echo $tot_roll_weight; ?></th>
            	<th style="text-align:center;"><? echo $tot_qty_pcs; ?></th>
            	<th></th>
            	<th></th>
            	<th></th>
            	<th></th>
            </tr>
        	<tr>
            	<th colspan="7" style="text-align:left;">Trims (Fab. Desc, amount, Uom, measurement):</th>
            </tr>
        </tfoot>
    </table>
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="800" align="center" style="margin-top:10px;" id="tbl">
    	<caption>
        	<span style="font-size:14px; font-weight:bold;">Hand Writing Area</span><br>
            <span style="font-size:13px; font-weight:bold;">Trims (Fab. Desc, Amount, UOM, Measurement) & (Quality Data- Checksheet For Last 4 Meters Checking of fabric)</span>
        </caption>
        <tbody>
			<tr>
            	<td width="150">Roll No</td>
            	<td width="250"></td>
            	<td width="150">Missing Lycrs</td>
            	<td width="250"></td>
            </tr>
			<tr>
            	<td>Horizontal Stripes</td>
            	<td></td>
            	<td>Iro Mark</td>
            	<td></td>
            </tr>
			<tr>
            	<td>Vartical Lines/Rays</td>
            	<td></td>
            	<td>Wrong Pattern</td>
            	<td></td>
            </tr>
			<tr>
            	<td>Oil Stain</td>
            	<td></td>
            	<td>Team Player Sign.</td>
            	<td></td>
            </tr>
			<tr>
            	<td>Needles out for stenter Fabric</td>
            	<td></td>
            	<td>Team Co-ord. Sign.</td>
            	<td></td>
            </tr>
			<tr>
            	<td>Broken Needle Line</td>
            	<td></td>
            	<td>No of Needles Requested</td>
            	<td></td>
            </tr>
			<tr>
            	<td>Drop Stitch</td>
            	<td></td>
            	<td>Operator Name</td>
            	<td></td>
            </tr>
			<tr>
            	<td>Knit M/C No</td>
            	<td></td>
            	<td></td>
            	<td></td>
            </tr>
			<tr>
            	<td>Non Conference Reference of Product defect</td>
            	<td></td>
            	<td>Quality Stamping</td>
            	<td></td>
            </tr>
    	</tbody>
        <tfoot>
            <tr>
            	<td></td>
            	<td></td>
            	<td></td>
            	<td></td>
            </tr>        
        </tfoot>
    </table>
	<table border="0" cellpadding="0" cellspacing="0" width="800" align="center" style="margin-top:10px;">
        <tbody>
            <tr height="100" valign="bottom">
            	<td width="200" align="center" style="text-decoration:overline;">Supervisor</td>
            	<td width="200" align="center" style="text-decoration:overline;">Planning</td>
            	<td width="200" align="center" style="text-decoration:overline;">Knitting Manager</td>
            	<td width="200" align="center" style="text-decoration:overline;">Approved By</td>
            </tr>        
        </tbody>
    </table>
	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
        function generateBarcode( valuess,id ){		   
            var value = valuess;//$("#barcodeValue").val();
            // alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer ='bmp';// $("input[name=renderer]:checked").val();
             
            var settings = {
              output:renderer,
              bgColor: '#FFFFFF',
              color: '#000000',
              barWidth: 1,
              barHeight: 30,
              moduleSize:5,
              posX: 10,
              posY: 20,
              addQuietZone: 1
            };
            $("#barcode_img_id").html('11');
             value = {code:value, rect: false};
            
            $("#barcode_img_id").barcode(value, btype, settings);
        } 
        generateBarcode('<? echo $prog_data['REFERENCE_NO']; ?>');
     </script>
    <?
	exit();
}

if($action == 'print_03022022')
{
	echo load_html_head_contents("Ref. Wise Pallet Sheet", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$mst_id = $data;
	$company_dtls = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$color_dtls = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$machine_dtls = return_library_array("select id, machine_no from lib_machine_name where category_id = 2", 'id', 'machine_no');
	$buyer_dtls = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$user_dtls = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');

	//for yarn
	$sql_yrn = "SELECT MST_ID, YARN_ORIGIN, YARN_LOT, YARN_COUNT FROM PPL_REFERENCE_CREATION_YARN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND MST_ID = ".$mst_id;
	$sql_yrn_rslt = sql_select($sql_yrn);
	
	//for roll
	$sql_roll = "SELECT MST_ID, ROLL_NO, ROLL_WEIGHT FROM PPL_REFERENCE_CREATION_ROLL WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND MST_ID = ".$mst_id;
	$sql_roll_rslt = sql_select($sql_roll);
	
	
	$sql_ref = "SELECT 
	A.BOOKING_NO, 
	B.ID AS PROG_NO, B.COLOR_ID, 
	C.ID, C.FABRIC_DESC, C.WIDTH_DIA_TYPE, C.GSM_WEIGHT, C.COLOR_TYPE_ID, C.PROGRAM_QNTY, 
	D.ID AS REF_ID, D.PLANNED_DATE, D.BATCH_NO, D.MACHINE_ID, D.MACHINE_CAPACITY, D.MACHINE_EFFICIENCY, D.NO_OF_TUBE, D.CAPACITY_PER_TUBE, D.REFERENCE_NO, D.REFERENCE_QTY, D.INSERTED_BY, 
	E.JOB_NO, E.COMPANY_ID, E.BUYER_ID, E.DELIVERY_DATE 
	FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, PPL_REFERENCE_CREATION D, FABRIC_SALES_ORDER_MST E 
	WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND B.MST_ID = C.MST_ID AND B.ID = D.PROGRAM_NO AND C.DTLS_ID = D.PROGRAM_NO AND A.IS_SALES = 1 AND B.IS_SALES = 1 AND C.IS_SALES = 1 AND C.PO_ID=E.ID AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND D.STATUS_ACTIVE = 1 AND D.IS_DELETED = 0 AND D.ID IN(".$mst_id.") ORDER BY D.ID ASC";
	//echo $sql_ref;
	$sql_ref_rslt = sql_select($sql_ref);
	$duplicate_check = array();
	$prog_data = array();
	foreach($sql_ref_rslt as $row)
	{
		if($duplicate_check[$row['REF_ID']] != $row['REF_ID'])
		{
			$duplicate_check[$row['REF_ID']] = $row['REF_ID'];
			
			//for color
			$exp_color_arr = array();
			$color_arr = array();
			$exp_color_arr = explode(',',$row['COLOR_ID']);
			foreach($exp_color_arr as $key=>$val)
			{
				$color_arr[$val] = $color_dtls[$val];
			}
			//end for color

			$prog_data['COMPANY_ID'] = $company_dtls[$row['COMPANY_ID']];
			$prog_data['REFERENCE_NO'] = $row['REFERENCE_NO'];
			$prog_data['SALES_ORDER'] = $row['JOB_NO'];
			$prog_data['BATCH_NO'] = $row['BATCH_NO'];
			
			$prog_data['PROG_NO'] = $row['PROG_NO'];
			$prog_data['FABRIC_DESC'] = $row['FABRIC_DESC'];
			$prog_data['COLOR_ID'] = implode(', ', $color_arr);
			$prog_data['WIDTH_DIA_TYPE'] = $fabric_typee[$row['WIDTH_DIA_TYPE']];
			$prog_data['GSM_WEIGHT'] = $row['GSM_WEIGHT'];
			$prog_data['WIDTH_DIA_TYPE'] = $row['WIDTH_DIA_TYPE'];
			$prog_data['MACHINE_ID'] = $machine_dtls[$row['MACHINE_ID']];
			$prog_data['BUYER_ID'] = $buyer_dtls[$row['BUYER_ID']];
			$prog_data['DELIVERY_DATE'] = ($row['DELIVERY_DATE']!=''? date('d-m-Y', strtotime($row['DELIVERY_DATE'])):'');
			$prog_data['PLANNED_DATE'] = ($row['PLANNED_DATE']!=''? date('d-m-Y', strtotime($row['PLANNED_DATE'])):'');
			$prog_data['REFERENCE_QTY'] = $row['REFERENCE_QTY'];
			$prog_data['INSERTED_BY'] = $user_dtls[$row['INSERTED_BY']];
			$prog_data[$row['REF_ID']]['COLOR_ID'] = implode(', ', $color_arr);
			$prog_data[$row['REF_ID']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
		}
	}
	unset($sql_ref_rslt);
	?>
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="560" align="center">
    	<thead>
        	<tr>
            	<th colspan="5"><? echo $prog_data['COMPANY_ID']; ?></th>
            </tr>
        	<tr>
            	<th colspan="3" rowspan="3">Ref. Wise Pallet Sheet</th>
                <th style="text-align:left;" width="60">By</th>
                <th style="text-align:left;" width="80"><? echo $prog_data['INSERTED_BY']; ?></th>
            </tr>
        	<tr>
                <th style="text-align:left;">Print Date</th>
                <th style="text-align:left;"><? echo date('d-m-Y'); ?></th>
            </tr>
        	<tr>
                <th style="text-align:left;">Print Time</th>
                <th style="text-align:left;"><? echo date("h:ia"); ?></th>
            </tr>
        </thead>
    	<tbody>
        	<tr>
            	<td>Tube/Ref. No</td>
                <td><? echo $prog_data['REFERENCE_NO']; ?></td>
            	<td>Pallet No</td>
                <td colspan="2"></td>
            </tr>
        	<tr>
            	<td>FSO No</td>
                <td><? echo $prog_data['SALES_ORDER']; ?></td>
            	<td>Batch No</td>
                <td colspan="2"><? echo $prog_data['BATCH_NO']; ?></td>
            </tr>
        	<tr>
            	<td>Created Date</td>
                <td><? echo $prog_data['PLANNED_DATE']; ?></td>
            	<td>Program No</td>
                <td colspan="2"><? echo $prog_data['PROG_NO']; ?></td>
            </tr>
        	<tr>
            	<td>Buyer</td>
                <td><? echo $prog_data['BUYER_ID']; ?></td>
            	<td>GSM</td>
                <td colspan="2"><? echo $prog_data['GSM_WEIGHT']; ?></td>
            </tr>
        	<tr>
            	<td>Fabrication</td>
                <td><? echo $prog_data['FABRIC_DESC']; ?></td>
            	<td>Dia/ W. Type</td>
                <td colspan="2"><? echo $prog_data['WIDTH_DIA_TYPE']; ?></td>
            </tr>
        	<tr>
            	<td>Color</td>
                <td colspan="4"><? echo $prog_data['COLOR_ID']; ?></td>
            </tr>
        	<tr>
            	<td>FSO Delivery Date</td>
                <td colspan="4"><? echo $prog_data['DELIVERY_DATE']; ?></td>
            </tr>
        	<tr>
            	<td>DYE M/C No</td>
                <td colspan="4"><? echo $prog_data['MACHINE_ID']; ?></td>
            </tr>
        	<tr>
            	<td>TOT WGTREQ [kg]</td>
                <td colspan="4"><? echo $prog_data['REFERENCE_QTY']; ?></td>
            </tr>
        </tbody>
    </table>
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="560" align="center" style="margin-top:10px;">
    	<thead>
            <tr>
                <th width="100">Yarn Origin</th>
                <th width="80">Lot</th>
                <th width="50">Count</th>
                <th width="200">Composition</th>
                <th width="100">Color</th>
            </tr>
        </thead>
        <tbody>
        <?
		foreach($sql_yrn_rslt as $row)
		{
			$row['COLOR_ID'] = $prog_data[$row['MST_ID']]['COLOR_ID'];
			$row['FABRIC_DESC'] = $prog_data[$row['MST_ID']]['FABRIC_DESC'];
			?>
			<tr>
            	<td><? echo $row['YARN_ORIGIN']; ?></td>
            	<td><? echo $row['YARN_LOT']; ?></td>
            	<td><? echo $row['YARN_COUNT']; ?></td>
            	<td><? echo $row['FABRIC_DESC']; ?></td>
            	<td><? echo $row['COLOR_ID']; ?></td>
            </tr>
            <?
		}
		?>
        </tbody>
    </table>
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="560" align="center" style="margin-top:10px;">
    	<thead>
            <tr>
                <th width="140">Roll No</th>
                <th width="150">Roll Weight</th>
                <th width="150">Scanned Weight [Kg]</th>
                <th width="120">Shift</th>
            </tr>
        </thead>
        <tbody>
        <?
		foreach($sql_roll_rslt as $row)
		{
			$row['COLOR_ID'] = $prog_data[$row['MST_ID']]['COLOR_ID'];
			$row['FABRIC_DESC'] = $prog_data[$row['MST_ID']]['FABRIC_DESC'];
			?>
			<tr>
            	<td align="center"><? echo $row['ROLL_NO']; ?></td>
            	<td align="center"><? echo $row['ROLL_WEIGHT']; ?></td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            </tr>
            <?
			$tot_roll_weight += $row['ROLL_WEIGHT'];
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
            	<th style="text-align:left;">Total Wgt. on Pallet (kg.)</th>
            	<th style="text-align:center;"><? echo $tot_roll_weight; ?></th>
            	<th></th>
            	<th></th>
            </tr>
        	<tr>
            	<th colspan="4" style="text-align:left;">Trims (Fab. Desc, amount, Uom, measurement):</th>
            </tr>
        </tfoot>
    </table>
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1300" align="center" style="margin-top:10px;">
    	<thead>
        	<tr>
            	<th colspan="13" style="text-align:left;">Quality Data- Checksheet For Last 4 Meters Checking of fabric</th>
            </tr>
            <tr>
                <th width="100">BALE NO</th>
                <th width="100">Horizontal Stripes</th>
                <th width="100">Vartical Lines/Rays</th>
                <th width="100">Oil Stain</th>
                <th width="100">Needles out for stenter Fabric</th>
                <th width="100">Broken Needle Line</th>
                <th width="100">Drop Stitch</th>
                <th width="100">Missing Lycrs</th>
                <th width="100">Iro Mark</th>
                <th width="100">Wrong Pattern</th>
                <th width="100">Team Player Sign.</th>
                <th width="100">Team Co-ord. Sign.</th>
                <th width="100">No of Needles </th>
            </tr>
        </thead>
        <tbody>
        	<?
			for($i=1; $i<=2; $i++)
			{
				?>
				<tr height="30">
                	<?
                    for($j=1; $j<=13; $j++)
					{
						?>
                        <td>&nbsp;</td>
                        <?
					}
					?>
                </tr>
				<?
			}
			?>
    	</tbody>
        <tfoot>
        	<tr>
            	<th colspan="7" style="text-align:left;">Non Conference reference of product defect:</th>
            	<th colspan="6" style="text-align:left;">Quality Stamping:</th>
            </tr>
        </tfoot>
    </table>
    <?
}

function get_composition($arr)
{
	global $composition;
	if ($arr['yarn_comp_percent2nd'] != 0)
	{
		$data = $composition[$arr['yarn_comp_type1st']] . " " . $arr['yarn_comp_percent1st'] . "%" . " " . $composition[$arr['yarn_comp_type2nd']] . " " . $arr['yarn_comp_percent2nd'] . "%";
	}
	else
	{
		$data = $composition[$arr['yarn_comp_type1st']] . " " . $arr['yarn_comp_percent1st'] . "%" . " " . $composition[$arr['yarn_comp_type2nd']];
	}
	return $data;
}
?>
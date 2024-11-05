<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
extract($_REQUEST);
$permission = $_SESSION['page_permission'];
include('../../includes/common.php');
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

/*
|--------------------------------------------------------------------------
| load_drop_down_buyer
|--------------------------------------------------------------------------
|
*/
if ($action == "load_drop_down_buyer")
{
	echo create_drop_down("cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "", 0);
	exit();
}

$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
$machine_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
//$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");

/*
|--------------------------------------------------------------------------
| approval_needed_or_not
|--------------------------------------------------------------------------
|
*/
if ($action == "approval_needed_or_not")
{
	$approval_needed_or_not = return_field_value("auto_update", "variable_settings_production", "company_name ='$data' and variable_list=31 and is_deleted=0 and status_active=1");
	if ($approval_needed_or_not == 1)
		$approval_needed_or_not = $approval_needed_or_not;
	else
		$approval_needed_or_not = 0;
	echo "document.getElementById('approval_needed_or_not').value = '" . $approval_needed_or_not . "';\n";
	exit();
}

/*
|--------------------------------------------------------------------------
| style_ref_search_popup
|--------------------------------------------------------------------------
|
*/
if ($action == "style_ref_search_popup")
{
	echo load_html_head_contents("Style Reference Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$buyerID = str_replace("'", "", $buyerID);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_ir = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
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

		function js_set_value(str)
		{
			if (str != "")
				str = str.split("_");
			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');
			if (jQuery.inArray(str[1], selected_id) == -1)
			{
				selected_id.push(str[1]);
				selected_name.push(str[2]);
				selected_ir.push(str[3]);

			}
			else
			{
				for (var i = 0; i < selected_id.length; i++)
				{
					if (selected_id[i] == str[1]) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
				selected_ir.splice(i, 1);
			}

			var id = '';
			var name = '';
			var ir = '';
			for (var i = 0; i < selected_id.length; i++)
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				ir += selected_ir[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			ir = ir.substr(0, ir.length - 1);
			//alert(ir);
			$('#hdn_style_id').val(id);
			$('#hide_style_ref').val(name);
			$('#hide_ir').val(ir);
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:580px;">
				<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
				class="rpt_table" id="tbl_list">
				<thead>
					<th>Buyer</th>
					<th>Search By</th>
					<th id="search_by_td_up" width="170">Please Enter Style Ref.</th>
					<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
					<input type="hidden" name="hide_style_ref" id="hide_style_ref" value=""/>
					<input type="hidden" name="hdn_style_id" id="hdn_style_id" value=""/>
					<input type="hidden" name="hide_ir" id="hide_ir" value=""/>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<?
							echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", $buyerID, "", 0);
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Style Ref");
							$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 130, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show"
							onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_style_ref_search_list_view', 'search_div', 'planning_info_entry_for_sample_without_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
							style="width:100px;"/>
						</td>
					</tr>
				</tbody>
			</table>
			<div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| create_style_ref_search_list_view
|--------------------------------------------------------------------------
|
*/
if ($action == "create_style_ref_search_list_view")
{
	$data = explode('**', $data);
	$company_id = $data[0];

	if ($data[1] == 0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"] == 1)
		{
			if ($_SESSION['logic_erp']["buyer_id"] >0 )
				$buyer_id_cond = " AND buyer_name IN (".$_SESSION['logic_erp']["buyer_id"].")";
			else
				$buyer_id_cond = "";
		}
		else
		{
			$buyer_id_cond = "";
		}
	}
	else
	{
		$buyer_id_cond = " AND buyer_name = ".$data[1]."";
	}

	$search_by = $data[2];
	$search_string = "%".trim($data[3])."%";
	if ($search_by == 1)
		$search_field = "style_ref_no";

	if ($db_type == 0)
	{
		$year_field = "YEAR(b.insert_date) as year,";
		$groupbyYear = "YEAR(b.insert_date),";
	}
	else if ($db_type == 2)
	{
		$year_field = "TO_CHAR(b.insert_date,'YYYY') AS year,";
		$groupbyYear = "TO_CHAR(b.insert_date,'YYYY'),";
	}
	else
	{
		//defined Later
		$groupbyYear = "";
		$year_field = "";
	}

	//wo_non_ord_samp_booking_dtls
	//$sql= "select  a.id, a.style_ref_no, a.company_id, a.buyer_name, a.article_no, b.id as bid, b.sample_name, b.sample_color, a.requisition_number_prefix_num, b.working_factory, b.receive_date_from_factory, b.sent_to_factory_date, b.sent_to_buyer_date, b.approval_status, b.status_date, b.recieve_date_from_buyer from sample_development_mst a, sample_development_dtls b where a.id=b.sample_mst_id and a.company_id ='$cbo_company_name' and buyer_name ='$cbo_buyer_name' $sample_id_cond $style_ref_cond $sample_type_cond $sample_color_cond and a.is_deleted=0 and b.is_deleted=0 order by a.id";

	//$sql = "SELECT id, ".$year_field." job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no FROM wo_po_details_master WHERE status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond order by job_no";

	$sql = "
		SELECT
			a.style_ref_no,
			b.style_id, ".$year_field."
			c.company_id, c.buyer_id,
			a.internal_ref
		FROM
			sample_development_mst a
			INNER JOIN wo_non_ord_samp_booking_dtls b ON a.id = b.style_id
			INNER JOIN wo_non_ord_samp_booking_mst c ON b.booking_no = c.booking_no
		WHERE
			a.status_active = 1
			AND a.is_deleted = 0
			AND c.booking_type = 4
			AND c.item_category = 2
			AND c.status_active = 1
			AND c.is_deleted = 0
			AND a.style_ref_no LIKE '%".trim($data[3])."%'
		GROUP BY
			a.style_ref_no,
			b.style_id, ".$groupbyYear."
			c.company_id, c.buyer_id, a.internal_ref
	";
	//echo $sql;
	$arr = array(0 => $company_arr, 1 => $buyer_arr);
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Style Ref. No", "120,120,70", "540", "240", 0, $sql, "js_set_value", "style_id,style_ref_no,internal_ref", "", 1, "company_id,buyer_id,0,0", $arr, "company_id,buyer_id,year,style_ref_no", "", '', '0,0,0,0', '', 1);
	exit();
}

/*
|--------------------------------------------------------------------------
| Internal_ref_search_popup
|--------------------------------------------------------------------------
|
*/

if ($action == "internal_ref_search_popup")
{
	echo load_html_head_contents("Internal Reference Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$buyerID = str_replace("'", "", $buyerID);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_ir = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
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

		function js_set_value(str)
		{
			if (str != "")
				str = str.split("_");
			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');
			if (jQuery.inArray(str[1], selected_id) == -1)
			{
				selected_id.push(str[1]);
				selected_name.push(str[2]);
				selected_ir.push(str[3]);

			}
			else
			{
				for (var i = 0; i < selected_id.length; i++)
				{
					if (selected_id[i] == str[1]) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
				selected_ir.splice(i, 1);
			}

			var id = '';
			var name = '';
			var ir = '';
			for (var i = 0; i < selected_id.length; i++)
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				ir += selected_ir[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			ir = ir.substr(0, ir.length - 1);
			//alert(ir);
			$('#hdn_style_id').val(id);
			$('#hide_style_ref').val(name);
			$('#hide_ir').val(ir);
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:580px;">
					<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
					class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Style Ref.</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
						<input type="hidden" name="hide_style_ref" id="hide_style_ref" value=""/>
						<input type="hidden" name="hdn_style_id" id="hdn_style_id" value=""/>
						<input type="hidden" name="hide_ir" id="hide_ir" value=""/>
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", $buyerID, "", 0);
								?>
							</td>
							<td align="center">
								<?
								$search_by_arr = array(1 => "Interlan Ref");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 130, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show"
								onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_internal_ref_search_list_view', 'search_div', 'planning_info_entry_for_sample_without_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
								style="width:100px;"/>
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

/*
|--------------------------------------------------------------------------
| create_internal_ref_search_list_view
|--------------------------------------------------------------------------
|
*/
if ($action == "create_internal_ref_search_list_view")
{
	$data = explode('**', $data);
	$company_id = $data[0];

	if ($data[1] == 0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"] == 1)
		{
			if ($_SESSION['logic_erp']["buyer_id"] >0 )
				$buyer_id_cond = " AND buyer_name IN (".$_SESSION['logic_erp']["buyer_id"].")";
			else
				$buyer_id_cond = "";
		}
		else
		{
			$buyer_id_cond = "";
		}
	}
	else
	{
		$buyer_id_cond = " AND buyer_name = ".$data[1]."";
	}

	$search_by = $data[2];
	$search_string = "%".trim($data[3])."%";
	if ($search_by == 1)
		$search_field = "style_ref_no";

	if ($db_type == 0)
	{
		$year_field = "YEAR(b.insert_date) as year,";
		$groupbyYear = "YEAR(b.insert_date),";
	}
	else if ($db_type == 2)
	{
		$year_field = "TO_CHAR(b.insert_date,'YYYY') AS year,";
		$groupbyYear = "TO_CHAR(b.insert_date,'YYYY'),";
	}
	else
	{
		//defined Later
		$groupbyYear = "";
		$year_field = "";
	}

	$sql = "
		SELECT
			a.style_ref_no,
			b.style_id, ".$year_field."
			c.company_id, c.buyer_id,
			a.internal_ref
		FROM
			sample_development_mst a
			INNER JOIN wo_non_ord_samp_booking_dtls b ON a.id = b.style_id
			INNER JOIN wo_non_ord_samp_booking_mst c ON b.booking_no = c.booking_no
		WHERE
			a.status_active = 1
			AND a.is_deleted = 0
			AND c.booking_type = 4
			AND c.item_category = 2
			AND c.status_active = 1
			AND c.is_deleted = 0
			AND a.internal_ref LIKE '%".trim($data[3])."%'
		GROUP BY
			a.style_ref_no,
			b.style_id, ".$groupbyYear."
			c.company_id, c.buyer_id, a.internal_ref
	";
	//echo $sql;
	$arr = array(0 => $company_arr, 1 => $buyer_arr);
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Internal Ref. No", "120,120,70", "540", "240", 0, $sql, "js_set_value", "style_id,style_ref_no,internal_ref", "", 1, "company_id,buyer_id,0,0", $arr, "company_id,buyer_id,year,internal_ref", "", '', '0,0,0,0', '', 1);
	exit();
}

/*
|--------------------------------------------------------------------------
| booking_no_search_popup
|--------------------------------------------------------------------------
|
*/
if ($action == "booking_no_search_popup")
{
	echo load_html_head_contents("Booking Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$buyerID = str_replace("'", "", $buyerID);
	?>
	<script>
		function js_set_value(data)
		{
			var data = data.split("_");
			var id = data[0];
			var booking_no = data[1];
			$('#hidden_booking_id').val(id);
			$('#hidden_booking_no').val(booking_no);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:600px;">
				<table width="590" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
				class="rpt_table" id="tbl_list">
				<thead>
					<th>Buyer</th>
					<th>Search By</th>
					<th>Year</th>
					<th id="search_by_td_up" width="160">Please Enter Booking No.</th>
					<th>
                        <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;">
                        <input type="hidden" name="hidden_booking_no" id="hidden_booking_no" value=""/>
                        <input type="hidden" name="hidden_booking_id" id="hidden_booking_id" value=""/>
                    </th>
				</thead>
				<tbody>
					<tr class="general">
						<td>
							<?
							echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", $buyerID, "", 0);
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Booking No.");
							$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td>
							<?
							echo create_drop_down("cbo_year", 65, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, "");
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show"
							onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value+'**'+'<? echo $cbo_booking_type; ?>', 'create_booking_search_list_view', 'search_div', 'planning_info_entry_for_sample_without_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
							style="width:100px;"/>
						</td>
					</tr>
				</tbody>
			</table>
			<div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| create_booking_search_list_view
|--------------------------------------------------------------------------
|
*/
if ($action == "create_booking_search_list_view")
{
	$data = explode('**', $data);
	$company_id = $data[0];

	if ($data[1] == 0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"] == 1)
		{
			if ($_SESSION['logic_erp']["buyer_id"] > 0)
				$buyer_id_cond = " AND a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyer_id_cond = "";
		}
		else
		{
			$buyer_id_cond = "";
		}
	}
	else
	{
		$buyer_id_cond = " AND a.buyer_id=$data[1]";
	}

	$search_by = $data[2];
	$search_string = "%".trim($data[3])."%";
	$cbo_year = $data[4];

	if (trim($cbo_year) != 0)
	{
		if ($db_type == 0)
			$year_cond = " AND YEAR(b.insert_date) = ".$cbo_year."";
		else if ($db_type == 2)
			$year_cond = " AND TO_CHAR(b.insert_date,'YYYY') = ".$cbo_year."";
		else
			$year_cond = "";
	}
	else
		$year_cond = "";

	if ($search_by == 1)
		$search_cond = "AND b.booking_no LIKE '".$search_string."'";

	$cbo_booking_type_short = explode("_",$data[5]);
	$cbo_booking_type = $cbo_booking_type_short[0];
	$is_short = $cbo_booking_type_short[1];

	if($cbo_booking_type>0)
	{
		$booking_type_cond = "AND a.booking_type = ".$cbo_booking_type."";
	}
	else
	{
		$booking_type_cond = "";
	}

	//$arr = array(0 => $buyer_arr, 4 => $supllier_arr); $company_arr
	/*
	$sql = "select id, booking_no, booking_date, buyer_id, job_no, pay_mode, supplier_id from wo_booking_mst where company_id=$company_id and item_category=2 and fabric_source!=2 $search_cond $buyer_id_cond $year_cond $booking_type_cond
	union all
	select id, booking_no, booking_date, buyer_id, job_no, pay_mode, supplier_id
	from wo_non_ord_samp_booking_mst
	where company_id=3 and item_category=2 and fabric_source!=2 $search_cond $buyer_id_cond $year_cond $booking_type_cond
	order by id desc";
	*/
	$sql = "
		SELECT
			a.buyer_id, a.booking_date, a.pay_mode, a.supplier_id,
			b.id, b.booking_no
		FROM
			wo_non_ord_samp_booking_mst a
			INNER JOIN wo_non_ord_samp_booking_dtls b ON a.booking_no = b.booking_no
		WHERE
			a.company_id = ".$company_id."
			AND a.item_category = 2
			and (a.fabric_source=1 or b.fabric_source=1)
			".$search_cond."
			".$buyer_id_cond."
			".$year_cond."
			".$booking_type_cond."
		GROUP BY
			a.buyer_id, a.booking_date, a.pay_mode, a.supplier_id,
			b.id, b.booking_no
		ORDER BY
			b.id DESC
	";
	//echo $sql;
	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="500" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="120">Buyer Name</th>
				<th width="120">Booking No</th>
				<th width="80">Booking Date</th>
				<th>Supplier</th>
			</thead>
		</table>
	</div>
	<div style="width:500px; max-height:270px;overflow-y:scroll;" >
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="480" class="rpt_table" id="tbl_list_search">
			<?
			$sql_res=sql_select($sql);
			$printData = array();
			foreach( $sql_res as $row )
			{
				$printData[$row[csf("buyer_id")]][$row[csf("booking_no")]][$row[csf("booking_date")]][$row[csf("supplier_id")]]['id'] = $row[csf("id")];
				$printData[$row[csf("buyer_id")]][$row[csf("booking_no")]][$row[csf("booking_date")]][$row[csf("supplier_id")]]['pay_mode'] = $row[csf("pay_mode")];
			}


			$i=1;
			foreach( $printData as $buyerId=>$buyerArr )
			{
				foreach( $buyerArr as $bookingNo=>$bookingArr )
				{
					foreach( $bookingArr as $bookingDate=>$bookingDateArr )
					{
						foreach( $bookingDateArr as $supplierId=>$row )
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

							$supplier_name="";
							if($row['pay_mode']==3 || $row['pay_mode']==5)
								$supplier_name=$company_arr[$supplierId];
							else
								$supplier_name=$supllier_arr[$supplierId];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row['id'].'_'.$bookingNo; ?>');" >
								<td width="30" align="center"><?php echo $i; ?></td>
								<td width="120"><?php echo $buyer_arr[$buyerId]; ?></td>
								<td width="120" align="center"><?php echo $bookingNo; ?></td>
								<td width="80" align="center"><?php echo change_date_format($bookingDate); ?></td>
								<td><?php echo $supplier_name; ?></td>
							</tr>
							<?
							$i++;
						}
					}
				}
			}
			?>
		</table>
	</div>
	<?
	exit();
}

/*
|--------------------------------------------------------------------------
| booking_item_details
|--------------------------------------------------------------------------
|
*/
if ($action == "booking_item_details")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_name = str_replace("'", "", $cbo_company_name);
	$planning_status = str_replace("'", "", $cbo_planning_status);
	$approval_needed_or_not = str_replace("'", "", $approval_needed_or_not);

	//buyerIdCondition
	$buyerIdCondition = '';
	if (str_replace("'", "", $cbo_buyer_name) == 0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"] == 1)
		{
			if ($_SESSION['logic_erp']["buyer_id"] >0)
				$buyerIdCondition = " AND a.buyer_id IN (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyerIdCondition = '';
		}
		else
		{
			$buyerIdCondition = '';
		}
	}
	else
	{
		$buyerIdCondition = " AND a.buyer_id = ".$cbo_buyer_name."";
	}

	//styleIdCondition
	$styleIdCondition = '';
	if (str_replace("'", "", $hdn_style_id) != '')
	{
		$styleIdCondition = "AND b.style_id IN(".str_replace("'", "", $hdn_style_id).")";
	}

	//bookingNoCondition
	$bookingNoCondition = '';
	$pr_booking_cond = '';
	if(str_replace("'","",trim($txt_booking_no)) != '')
	{
		$txt_booking = "%".str_replace("'", "", trim($txt_booking_no))."%";
		$bookingNoCondition = "AND a.booking_no LIKE '".$txt_booking."'";
		$pr_booking_cond = "AND a.booking_no LIKE '".$txt_booking."'";
	}

	//bookingDateCondition
	$bookingDateCondition = '';
	$date_from = str_replace("'", "", trim($txt_date_from));
	$date_to = str_replace("'", "", trim($txt_date_to));
	if ($date_from != '' && $date_to != '')
	{
		if ($db_type == 0)
		{
			$bookingDateCondition = "AND a.booking_date BETWEEN '".change_date_format(trim($date_from), "yyyy-mm-dd", "-")."' AND '".change_date_format(trim($date_to), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$bookingDateCondition = "AND a.booking_date BETWEEN '".change_date_format(trim($date_from), '', '', 1)."' AND '".change_date_format(trim($date_to), '', '', 1)."'";
		}
	}

	$cbo_booking_type_short = explode("_",str_replace("'", "", trim($cbo_booking_type)));
	$cbo_booking_type = $cbo_booking_type_short[0];
	$is_short = $cbo_booking_type_short[1];

	if($cbo_booking_type>0)
	{
		$booking_type_cond = "AND a.booking_type = ".$cbo_booking_type."";
	}
	else
	{
		$booking_type_cond = "";
	}

	/*
	|--------------------------------------------------------------------------
	| main query
	| type = 1 = show button
	|--------------------------------------------------------------------------
	|
	*/
	if ($type == 1)
	{
		$sql = "
			SELECT
				a.id AS bmid, a.booking_no, a.booking_date, a.buyer_id,b.entry_form_id,
				b.id AS bdid, b.style_id, b.body_part, b.color_type_id, b.lib_yarn_count_deter_id, b.fabric_description, b.gsm_weight, b.grey_fabric AS finish_fabric, b.yarn_details, b.dtls_id, b.fabric_color,b.gmts_color, b.width_dia_type,(CASE WHEN b.entry_form_id = 140
				THEN b.dia  ELSE b.dia_width END) dia_width,b.remarks 
			FROM
				wo_non_ord_samp_booking_mst a
				INNER JOIN wo_non_ord_samp_booking_dtls b ON a.booking_no = b.booking_no
			WHERE
				a.company_id = ".$company_name."
				AND a.item_category = 2
				AND a.status_active=1
				AND a.is_deleted=0
				and (a.fabric_source=1 or b.fabric_source=1)
				AND b.status_active=1
				AND b.is_deleted=0
				".$buyerIdCondition."
				".$styleIdCondition."
				".$bookingNoCondition."
				".$bookingDateCondition."
				".$booking_type_cond."
			GROUP BY
				a.id, a.booking_no, a.booking_date, a.buyer_id,
				b.id, b.style_id, b.body_part, b.color_type_id, b.lib_yarn_count_deter_id, b.fabric_description, b.gsm_weight, b.dia_width, b.grey_fabric, b.yarn_details, b.dtls_id, b.fabric_color,gmts_color, b.width_dia_type,b.entry_form_id,b.dia,b.remarks 
			ORDER BY
				b.dia,
				b.dia_width,
				a.booking_no
		";
	}
	else
	{
		$sql = "
			SELECT
				a.id AS bmid, a.booking_no, a.booking_date, a.buyer_id,b.entry_form_id,
				b.id AS bdid, b.style_id, b.body_part, b.color_type_id, b.lib_yarn_count_deter_id, b.fabric_description, b.gsm_weight, b.grey_fabric AS finish_fabric, b.yarn_details, b.dtls_id, b.fabric_color,b.gmts_color, b.width_dia_type,(CASE WHEN b.entry_form_id = 140
				THEN b.dia  ELSE b.dia_width END) dia_width,b.remarks 
			FROM
				wo_non_ord_samp_booking_mst a
				INNER JOIN wo_non_ord_samp_booking_dtls b ON a.booking_no = b.booking_no
			WHERE
				a.company_id = ".$company_name."
				AND a.item_category = 2
				AND a.status_active=1
				AND a.is_deleted=0
				and (a.fabric_source=1 or b.fabric_source=1)
				AND b.status_active=1
				AND b.is_deleted=0
				".$buyerIdCondition."
				".$styleIdCondition."
				".$bookingNoCondition."
				".$bookingDateCondition."
				".$booking_type_cond."
			GROUP BY
				a.id, a.booking_no, a.booking_date, a.buyer_id,
				b.id, b.style_id, b.body_part, b.color_type_id, b.lib_yarn_count_deter_id, b.fabric_description, b.gsm_weight, b.dia_width, b.grey_fabric, b.yarn_details, b.dtls_id, b.fabric_color,b.gmts_color, b.width_dia_type,b.entry_form_id,b.dia,b.remarks 
			ORDER BY
				b.dia,
				b.dia_width,
				a.booking_no
		";
		/*
		if ($db_type == 0)
		{
			$sql = "SELECT a.id, a.company_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as qnty, c.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$company_name and a.item_category=2 and a.fabric_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 $booking_cond $booking_date $booking_type_cond group by a.booking_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.dia_width order by cast(b.dia_width as unsigned),a.booking_no";// and a.buyer_id like '$buyer_name'
		}
		else
		{
			$sql = "
				SELECT
					a.id, a.company_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved,
					b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as qnty,
					c.style_ref_no
				FROM
					wo_booking_mst a,
					wo_booking_dtls b,
					wo_po_details_master c
				WHERE
					a.booking_no=b.booking_no
					and b.job_no=c.job_no
					and a.company_id=$company_name
					and a.item_category=2
					and a.fabric_source=1
					and a.status_active=1
					and a.is_deleted=0
					and b.status_active=1
					and b.is_deleted=0
					and b.grey_fab_qnty>0
					".$booking_cond."
					".$booking_date."
					".$booking_type_cond ."
				GROUP BY
					a.id, a.company_id, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, a.item_category,
					b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width,
					c.style_ref_no
				ORDER BY
					b.dia_width,
					a.booking_no
			";
		}
		*/
	}

	//echo $sql;die;
	$resultSet = sql_select($sql);
	if(empty($resultSet))
	{
		echo "<div style='width:100%;margin-top:10px;text-align:center'>".get_empty_data_msg()."</div>";
		die;
	}

	/*
	|--------------------------------------------------------------------------
	| for plan information
	|--------------------------------------------------------------------------
	|
	*/
	$bookingNoArr = array();
	$styleIdArr = array();
	$yarnCountDeterIdArr = array();
	foreach ($resultSet as $row)
	{
		$styleIdArr[$row[csf('style_id')]] = $row[csf('style_id')];
		$yarnCountDeterIdArr[$row[csf('lib_yarn_count_deter_id')]] = $row[csf('lib_yarn_count_deter_id')];
		$bookingNoArr[$row[csf('booking_no')]] = $row[csf('booking_no')];
		$bookingNoEntryFrmArr[$row[csf('booking_no')]]["entry_form_id"] = $row[csf('entry_form_id')];
	}

	if ($db_type == 0)
	{
		$queryProgNo = " GROUP_CONCAT(c.dtls_id) AS prog_no,";
	}
	elseif ($db_type == 2)
	{
		$queryProgNo = " LISTAGG(c.dtls_id, ',') WITHIN GROUP (ORDER BY c.dtls_id) AS prog_no,";
	}

	$sqlPlan = "SELECT a.booking_no, c.id, c.mst_id, c.body_part_id, c.color_type_id, c.determination_id, c.fabric_desc, c.gsm_weight, c.dia, ".$queryProgNo." SUM(c.program_qnty) AS program_qnty, c.status_active
	FROM
		ppl_planning_info_entry_mst a,
		ppl_planning_info_entry_dtls b,
		ppl_planning_entry_plan_dtls c
	WHERE
		a.id = b.mst_id
		AND b.id = c.dtls_id
		AND a.booking_no IN('".implode("','",$bookingNoArr)."')
		AND a.is_sales = 2
		AND b.status_active = 1
		AND b.is_deleted = 0
		AND b.is_sales = 2
		AND c.is_revised=0
		AND c.is_sales = 2
	GROUP BY
		a.booking_no,
		c.id, c.mst_id, c.yarn_desc, c.body_part_id, c.color_type_id, c.determination_id, c.fabric_desc, c.gsm_weight, c.dia, c.status_active";
	//echo $sqlPlan;
	$resultPlan = sql_select($sqlPlan);
	$program_data_array = array();
	$planIdArr = array();
	$progNoArr = array();
	$planQty = array();
	foreach ($resultPlan as $rowPlan)
	{
		$planIdArr[$rowPlan[csf('booking_no')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]] = $rowPlan[csf('mst_id')];
		$progNoArr[$rowPlan[csf('booking_no')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][] = $rowPlan[csf('prog_no')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]]['program_qnty'] += $rowPlan[csf('program_qnty')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]]['fabricDtls'] = $rowPlan[csf('fabric_desc')];

		$planQty[$rowPlan[csf('mst_id')]][$rowPlan[csf('booking_no')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]]['program_qnty'] += $rowPlan[csf('program_qnty')];

		//for program qty
		$exp_prog = explode(',',$rowPlan[csf('prog_no')]);
		foreach($exp_prog as $key=>$val)
		{
			$programQtyArr[$val][$rowPlan[csf('booking_no')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]]['program_qnty'] = $rowPlan[csf('program_qnty')];
		}
	}
	unset($resultPlan);
	/*echo "<pre>";
	print_r($program_data_array);
	echo "</pre>";*/

	/*
	|--------------------------------------------------------------------------
	| for yarn details
	|--------------------------------------------------------------------------
	|
	*/
	$sqlYarn = "select b.booking_no, c.id, c.lot, c.yarn_count_id, c.yarn_type, c.yarn_comp_type1st, c.yarn_comp_type2nd, c.yarn_comp_percent1st, c.yarn_comp_percent2nd from inv_material_allocation_mst a, inv_material_allocation_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and b.booking_no in('".implode("','",$bookingNoArr)."') and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.booking_no, c.id, c.lot, c.yarn_count_id, c.yarn_type, c.yarn_comp_type1st, c.yarn_comp_type2nd, c.yarn_comp_percent1st, c.yarn_comp_percent2nd";
	$resultYarn = sql_select($sqlYarn);
	$yarnDetailsArr = array();
	foreach ($resultYarn as $row)
	{
		if ($row[csf('yarn_comp_percent2nd')] != 0)
		{
			$compostion = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		}
		else
		{
			$compostion = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$yarnDetailsArr[$row[csf('booking_no')]] = $row[csf('lot')] . " " . $yarn_count_details[$row[csf('yarn_count_id')]] . " " . $compostion . " " . $yarn_type[$row[csf('yarn_type')]];
	}
	//echo "<pre>";
	//print_r($yarnDetailsArr);

	/*
	|--------------------------------------------------------------------------
	| for style ref no
	|--------------------------------------------------------------------------
	|
	*/
	$styleDetails = return_library_array("SELECT id, style_ref_no FROM sample_development_mst WHERE 1=1".where_con_using_array($styleIdArr, '0', 'id'), 'id', 'style_ref_no');
	$irDetails = return_library_array("SELECT id, internal_ref FROM sample_development_mst WHERE 1=1".where_con_using_array($styleIdArr, '0', 'id'), 'id', 'internal_ref');

	$articleDetails=	sql_select("select b.booking_no,b.style_id,b.body_part,b.color_type_id,b.lib_yarn_count_deter_id,b.gsm_weight,(CASE WHEN b.entry_form_id = 140 THEN b.dia  ELSE b.dia_width END) dia_width,d.article_no 
	FROM
	wo_non_ord_samp_booking_mst a , wo_non_ord_samp_booking_dtls b,sample_development_mst c,sample_development_dtls d WHERE a.booking_no = b.booking_no and b.style_id=c.id and c.id=d.sample_mst_id and a.company_id = ".$company_name." AND a.item_category = 2 AND a.status_active=1 AND a.is_deleted=0 and (a.fabric_source=1 or b.fabric_source=1) AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0".$buyerIdCondition." ".$styleIdCondition." ".$bookingNoCondition." ".$bookingDateCondition." ".$booking_type_cond."
	GROUP BY b.booking_no,b.style_id,b.body_part,b.color_type_id,b.lib_yarn_count_deter_id,b.gsm_weight,b.dia_width,d.article_no,b.entry_form_id,b.dia"); 
	$articleDetailsArr=array();
	foreach ($articleDetails as $row)
	{
		$articleDetailsArr[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['article_no'].= $row[csf('article_no')].",";
	}

	/*echo "<pre>";
	print_r($articleDetailsArr);
	echo "</pre>";*/
	 
	
	$yarnCountDetails = return_library_array("SELECT a.id, c.fabric_composition_name FROM lib_yarn_count_determina_mst a JOIN lib_yarn_count_determina_dtls b ON a.id = b.mst_id LEFT JOIN lib_fabric_composition c ON  c.id = a.fabric_composition_id AND c.status_active = 1 AND c.is_deleted = 0 WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 ".where_con_using_array($yarnCountDeterIdArr, '0', 'a.id')."GROUP BY a.id,c.fabric_composition_name ORDER BY a.id", 'id', 'fabric_composition_name');
	/*
	|--------------------------------------------------------------------------
	| data preparing here
	|--------------------------------------------------------------------------
	|
	*/
	$rptData = array();
	$bookingQtyArr = array();
	foreach ($resultSet as $row)
	{
		$bookingQtyArr[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bookingQty'] += $row[csf('finish_fabric')];
	}
	//echo "<pre>";
	//print_r($bookingQtyArr);

	$dataPlanQty = array();
	foreach($planQty as $planId=>$planArr)
	{
		foreach($planArr as $bookingNo=>$bookingArr)
		{
			foreach($bookingArr as $bodyPart=>$bodyPartArr)
			{
				foreach($bodyPartArr as $colorTypeId=>$colorTypeArr)
				{
					foreach($colorTypeArr as $determinationId=>$determinationArr)
					{
						foreach($determinationArr as $gsm=>$gsmArr)
						{
							foreach($gsmArr as $dia=>$row)
							{
								$dataPlanQty[$planId]['planQty'] = number_format($row['program_qnty'], 2, '.', '');
								$dataPlanQty[$planId]['bookingQty'] = number_format($bookingQtyArr[$bookingNo][$bodyPart][$colorTypeId][$determinationId][$gsm][$dia]['bookingQty'], 2, '.', '');
							}
						}
					}
				}
			}
		}
	}
	//echo "<pre>";
	//print_r($dataPlanQty);

	foreach ($resultSet as $row)
	{
		//program Qty
		if(empty($program_data_array[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['program_qnty']))
		{
			$programQty = 0;
		}
		else
		{
			$programQty = $program_data_array[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['program_qnty'];
		}

		$bookingQty = $bookingQtyArr[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bookingQty'];
		$balanceQty = $bookingQty - $programQty;

		$plan_id = $planIdArr[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]];

		if (($planning_status == 2 && $dataPlanQty[$plan_id]['bookingQty'] <= $dataPlanQty[$plan_id]['planQty'] &&  $plan_id != '') || ($planning_status == 1 && ($dataPlanQty[$plan_id]['bookingQty'] > $dataPlanQty[$plan_id]['planQty'] || $plan_id == '')))
		{
			$i++;
			$isRequisition = 0;
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bookingNo'] = $row[csf('booking_no')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bookingDate'] = date('d-m-Y', strtotime($row[csf('booking_date')]));

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['buyerId'] = $row[csf('buyer_id')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['buyerDtls'] = $buyer_arr[$row[csf('buyer_id')]];

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['og'] = $row[csf('dtls_id')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['styleId'] = $row[csf('style_id')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['styleDtls'] = $styleDetails[$row[csf('style_id')]];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['irRef'] = $irDetails[$row[csf('style_id')]];
			

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bodyPartId'] = $row[csf('body_part')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bodyPartDtls'] = $body_part[$row[csf('body_part')]];

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['colorTypeId'] = $row[csf('color_type_id')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['colorTypeDtls'] = $color_type[$row[csf('color_type_id')]];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['fabricId'] = $row[csf('lib_yarn_count_deter_id')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['fabricDtls'] = $row[csf('fabric_description')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['fabricComp'] = $yarnCountDetails[$row[csf('lib_yarn_count_deter_id')]];

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['gsm'] = $row[csf('gsm_weight')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['dia'] = $row[csf('dia_width')];

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['dia_data'] = $row[csf('dia_data')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['dia_w'] = $row[csf('dia_w')];


			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['diaWidthTypeId'] = $row[csf('width_dia_type')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['diaWidthTypeDtls'] = $fabric_typee[$row[csf('width_dia_type')]];
			//$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['yarnDtls'] = $row[csf('yarn_details')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['yarnDtls'] = $yarnDetailsArr[$row[csf('booking_no')]];

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bookingQty'] = $bookingQty;
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['programQty'] = $programQty;
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['balanceQty'] = $balanceQty;

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['remarks'] = $row[csf('remarks')];

			if($row[csf('fabric_color')]==0) //if fabric color 0 its menas this booking comes from requisition booking and requistion gmts color act as fabric color // issue id = 6199
			{
				$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['colorId'][$row[csf('gmts_color')]] = $row[csf('gmts_color')];
				$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['colorBookingQty'][$row[csf('gmts_color')]] += $row[csf('finish_fabric')];
			}
			else
			{
				$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['colorId'][$row[csf('fabric_color')]] = $row[csf('fabric_color')];
				$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['colorBookingQty'][$row[csf('fabric_color')]] += $row[csf('finish_fabric')];
			}

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bookingMstId'][$row[csf('bmid')]] = $row[csf('bmid')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bookingDtlsId'][$row[csf('bdid')]] = $row[csf('bdid')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bookingDtlsIds'] = $row[csf('dtls_id')];


			//planId
			if(empty($planIdArr[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]))
			{
				$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['planId'] = '';
			}
			else
			{
				$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['planId'] = $planIdArr[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]];
			}

			//porgNo
			if(empty($progNoArr[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]))
			{
				$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['progNo'] = '';
			}
			else
			{
				$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['progNo'] = $progNoArr[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]];
			}
		}
	}
	unset($resultSet);
	/* echo "<pre>";
	print_r($rptData); die; */

	/*
	|--------------------------------------------------------------------------
	| for show button
	|--------------------------------------------------------------------------
	|
	*/
	if ($type == 1)
	{
		if(empty($rptData))
		{
			echo "<div style='width:100%;margin-top:10px;text-align:center'>".get_empty_data_msg()."</div>";
			die;
		}

		$sl = 0;
		$rptPData = array();
		$subTotal = array();
		$grandTotal = array();
		foreach ($rptData as $bookingNo=>$bookingArr)
		{
			foreach ($bookingArr as $bodyPartId=>$bodyPartArr)
			{
				foreach ($bodyPartArr as $colorType=>$colorTypeArr)
				{
					foreach ($colorTypeArr as $febId=>$febArr)
					{
						foreach ($febArr as $gsmNo=>$gsmArr)
						{
							foreach ($gsmArr as $diaNo=>$row)
							{
								if($diaNo != '')
								{
									$sl++;
									$rptPData[$diaNo][$sl] = $row;

									//subTotal
									$subTotal[$diaNo]['bookingQty'] += $row['bookingQty'];
									$subTotal[$diaNo]['programQty'] += $row['programQty'];
									$subTotal[$diaNo]['balanceQty'] += $row['balanceQty'];

									//grandTotal
									$grandTotal['bookingQty'] += $row['bookingQty'];
									$grandTotal['programQty'] += $row['programQty'];
									$grandTotal['balanceQty'] += $row['balanceQty'];
								}
							}
						}
					}
				}
			}
		}

		/*
		|--------------------------------------------------------------------------
		| for report print
		|--------------------------------------------------------------------------
		|
		*/
		?>
		<form name="palnningEntry_2" id="palnningEntry_2">
			<fieldset style="width:2310px;">
				<legend>Fabric Description Details</legend>
				<input type="hidden" value="<? echo $type; ?>" name="txt_type" id="txt_type">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2300" class="rpt_table" id="tbl_list_search">
						<thead>
							<th width="40">SL</th>
							<th width="70">Plan Id</th>
							<th width="70">Prog. No</th>
							<th width="115">Booking No</th>
							<th width="100">Article/Style No</th>
							<th width="100">IR/CN</th>
							<th width="80">Booking Date</th>
							<th width="80">Start Date</th>
							<th width="80">T.O.D</th>
							<th width="70">Buyer</th>
							<th width="70"><? echo $company_arr[$company_name]; ?></th>
							<th width="120">Style</th>
							<th width="110">Body Part</th>
							<th width="100">Color Type</th>
							<th width="130">Fabric Desc.</th>
							<th width="130">Fabric Comp.</th>
							<th width="70">Fabric Gsm</th>
							<th width="80">Fabric Dia</th>
							<th width="80">Width/Dia Type</th>
							<th width="90">Booking Qnty</th>
							<th width="110">Prog. Qnty</th>
							<th width="90">Balance Prog. Qnty</th>
							<th width="200">Desc.Of Yarn</th>
							<th>Remarks</th>
						</thead>
						<tbody>
						<?php
						$noOfTd = 24;
						$noOfColspanTd = 19;
						$rowNo = 0;
						$chkDia = array();

						foreach ($rptPData as $diaNo=>$diaArr)
						{
							foreach ($diaArr as $sl=>$row)
							{
								$rowNo++;
								$rowColor = ($sl % 2 == 0 ? "#E9F3FF" : "#FFFFFF");

								//for color qty
								$colorQtyStr = '';
								foreach($row['colorBookingQty'] as $colorId=>$colorValue)
								{
									if($colorQtyStr != '')
										$colorQtyStr .= ',';

									$colorQtyStr .= $colorId.'-'.number_format($colorValue,2, '.', '');
								}

								if(empty($chkDia[$diaNo]))
								{
									$chkDia[$diaNo] = $diaNo;
									?>
									<tr bgcolor="#EFEFEF" id="tr_<? echo $rowNo; ?>">
										<td colspan="<?php echo $noOfTd; ?>"><b>Dia/Width: <?php echo $row['dia']; ?></b></td>
									</tr>
									<?php
									$rowNo++;
								}
								
								$articleNo=chop($articleDetailsArr[$row['bookingNo']][$row['bodyPartId']][$row['colorTypeId']][$row['fabricId']][$row['gsm']][$row['dia']]['article_no'],",");


								?>
								<tr bgcolor="<?php echo $rowColor; ?>" style="text-decoration:none; cursor:pointer; vertical-align:middle;" onClick="fnc_selected_row('<?php echo $rowNo; ?>', '')" id="tr_<?php echo $rowNo; ?>">
									<td align="center"><?php echo $sl; ?></td>
									<td><?php echo $row['planId']; ?></td>
									<td>
									<?php
									$print_program_no = "";
									foreach ($row['progNo'] as $prog)
									{
										if($prog != "")
										{
											if($program_info_format_id==""){$program_info_format_id=0;}
											$smn_booking_requistion_wise=1;
											$print_program_no .= "<a href='##' onclick=\"generate_report2(".$company_name.",".$prog.",".$program_info_format_id.",0,0".",".$smn_booking_requistion_wise.")\">".$prog."</a>,";
										}
									}
									echo rtrim($print_program_no,", ");
									?>
									</td>
									<td>
										<?
										 echo "<a href='#'   onClick=\"generate_smn_report('" . $company_name . "','" . $row['styleId'] . "','" . $row['bookingNo'] . "',1)\">" . $row['bookingNo'] . "<a/>";
										 ?>
									</td>

									

									<td align="center"><?php echo $articleNo; ?></td>
									<td align="center"><?php echo $row['irRef']; ?></td>
									<td><?php echo date('d-m-Y', strtotime($row['bookingDate'])); ?></td>
									<td><?php //echo $row['partyDtls']; ?></td>
									<td><?php //echo $row['partyDtls']; ?></td>
									<td><?php echo $row['buyerDtls']; ?></td>
									<td title="Booking Details ID"><?php echo $row['og']; ?></td>
									<td align="center"><?php echo $row['styleDtls']; ?></td>
									<td><?php echo $row['bodyPartDtls']; ?></td>
									<td><?php echo $row['colorTypeDtls']; ?></td>
									<td><?php echo $row['fabricDtls']; ?></td>
									<td><?php echo $row['fabricComp']; ?></td>
									<td align="center"><?php echo $row['gsm']; ?></td>
									<td align="center"><?php echo $row['dia']; ?></td>
									<td><?php echo $row['diaWidthTypeDtls']; ?></td>
									<td align="right"><?php echo number_format($row['bookingQty'],2); ?></td>
									<td align="right"><?php echo number_format($row['programQty'],2); ?></td>
									<td align="right"><?php echo number_format($row['balanceQty'],2); ?></td>
									<td><p><?php echo $row['yarnDtls']; ?></p></td>
									<td><?php echo $row['remarks']; ?></td>

									<input type="hidden" name="hdnBuyerId[]" id="hdnBuyerId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['buyerId']; ?>" />
									<input type="hidden" name="hdnBookingNo[]" id="hdnBookingNo_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['bookingNo']; ?>" />
									<input type="hidden" name="hdnFabricId[]" id="hdnFabricId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['fabricId']; ?>" />
									<input type="hidden" name="hdnFabricDtls[]" id="hdnFabricDtls_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['fabricDtls']; ?>" />
									<input type="hidden" name="hdnBodyPartId[]" id="hdnBodyPartId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['bodyPartId']; ?>" />
									<input type="hidden" name="hdnGsm[]" id="hdnGsm_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['gsm']; ?>" />
									<input type="hidden" name="hdnDia[]" id="hdnDia_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['dia']; ?>" />


									<input type="hidden" name="hdnDiaWidthType[]" id="hdnDiaWidthType_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['diaWidthTypeId']; ?>" />

									<input type="hidden" name="hdnBookingQty[]" id="hdnBookingQty_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['bookingQty']; ?>" />
									<input type="hidden" name="hdnProgramQty[]" id="hdnProgramQty_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['programQty']; ?>" />
									<input type="hidden" name="hdnBalanceQty[]" id="hdnBalanceQty_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['balanceQty']; ?>" />

									<input type="hidden" name="hdnColorId[]" id="hdnColorId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo implode(",", $row['colorId']); ?>" />
									<input type="hidden" name="hdnColorTypeId[]" id="hdnColorTypeId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['colorTypeId']; ?>" />
									<input type="hidden" name="hdnIsRequisition[]" id="hdnIsRequisition_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['isRequisition']; ?>" />
									<input type="hidden" name="hdnOrderMstId[]" id="hdnOrderMstId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo implode(",", $row['orderMstId']); ?>" />
									<input type="hidden" name="hdnOrderDtlsId[]" id="hdnOrderDtlsId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo implode(",", $row['orderDtlsId']); ?>" />
									<input type="hidden" name="hdnOrderBrkDownId[]" id="hdnOrderBrkDownId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo implode(",", $row['orderBrkDownId']); ?>" />
									<input type="hidden" name="hdnPlanId[]" id="hdnPlanId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['planId']; ?>" />
									<input type="hidden" name="hdnJobNo[]" id="hdnJobNo_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['jobNo']; ?>" />
									<input type="hidden" name="hdnColorQty[]" id="hdnColorQty_<? echo $rowNo; ?>" class="text_boxes" value="<?php echo $colorQtyStr; ?>"/>
									<input type="hidden" name="hdnBookingEntryForm[]" id="hdnBookingEntryForm_<? echo $rowNo; ?>" class="text_boxes" value="<?php echo $bookingNoEntryFrmArr[$row['bookingNo']]["entry_form_id"]; ?>"/>
									<input type="hidden" name="hdnBookingDtlsId[]" id="hdnBookingDtlsId_<? echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['bookingDtlsIds'];?>"/>

								</tr>
								<?php
							}
							$rowNo++;
							?>
							<tr bgcolor="#CCCCCC" id="tr_<? echo $rowNo; ?>">
								<th colspan="<?php echo $noOfColspanTd; ?>" align="right">Sub Total</th>
								<th align="right"><? echo number_format($subTotal[$diaNo]['bookingQty'], 2, '.', ''); ?></th>
								<th align="right"><? echo number_format($subTotal[$diaNo]['programQty'], 2, '.', ''); ?></th>
								<th align="right"><? echo number_format($subTotal[$diaNo]['balanceQty'], 2, '.', ''); ?></th>
								<th></th>
								<th></th>
							</tr>
							<?php
						}
						?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="<?php echo $noOfColspanTd; ?>" align="right">Grand Total<input type="hidden" name="company_id" id="company_id" value="<?php echo $company_name; ?>"/></th>
								<th align="right"><? echo number_format($grandTotal['bookingQty'], 2, '.', ''); ?></th>
								<th align="right"><? echo number_format($grandTotal['programQty'], 2, '.', ''); ?></th>
								<th align="right"><? echo number_format($grandTotal['balanceQty'], 2, '.', ''); ?></th>
								<th></th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
		</form>
		<?
	}
	/*
	|--------------------------------------------------------------------------
	| for revise button
	|--------------------------------------------------------------------------
	|
	*/
	else
	{
		$rptPData = array();
		$prog_no_arr = array();
		foreach ($progNoArr as $bookingNo=>$bookingArr)
		{
			foreach ($bookingArr as $bodyPartId=>$bodyPartArr)
			{
				foreach ($bodyPartArr as $colorType=>$colorTypeArr)
				{
					foreach ($colorTypeArr as $febId=>$febArr)
					{
						foreach ($febArr as $gsmNo=>$gsmArr)
						{
							foreach ($gsmArr as $diaNo=>$row)
							{
								if($rptData[$bookingNo][$bodyPartId][$colorType][$febId][$gsmNo][$diaNo]['progNo'] == '')
								{
									foreach ($row as $key=>$progNo)
									{
										$prog_no_arr[$progNo] = $progNo;
										foreach ($rptData[$bookingNo] as $body_partId=>$body_partArr)
										{
											foreach ($body_partArr as $color_types=>$color_typesArr)
											{
												foreach ($color_typesArr as $feb_id=>$feb_idArr)
												{
													foreach ($feb_idArr as $gsm_no=>$gsm_noArr)
													{
														foreach ($gsm_noArr as $dia_no=>$diaArr)
														{
															$rptPData[$bookingNo][$bodyPartId][$colorType][$febId][$gsmNo][$diaNo][$progNo] = $diaArr;
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		/*echo "<pre>";
		print_r($rptPData);
		echo "</pre>";*/


		if(empty($rptPData))
		{
			echo "<div style='width:100%;margin-top:10px;text-align:center'>".get_empty_data_msg()."</div>";
			die;
		}

		//for knitting qty
		$knit_qnty_array = return_library_array("SELECT a.booking_id, sum(b.grey_receive_qnty) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 and a.booking_id in(".implode(',', $prog_no_arr).") group by a.booking_id", "booking_id", "knitting_qnty");
		/*echo "<pre>";
		print_r($knit_qnty_array);
		echo "</pre>";*/
		?>
		<form name="palnningEntry_2" id="palnningEntry_2">
			<fieldset style="width:1840px;">
				<legend>Fabric Description Details</legend>
                <input type="button" value="Delete Program" name="generate" id="generate" class="formbutton" style="width:100px" onClick="func_delete()"/>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1820" class="rpt_table" id="tbl_list_search">
                    <thead>
                        <th width="40"></th>
                        <th width="40">SL</th>
                        <th width="70">Plan Id</th>
                        <th width="70">Prog. No</th>
                        <th width="115">Booking No</th>
                        <th width="100">IR/CN</th>
                        <th width="80">Booking Date</th>
                        <th width="70">Buyer</th>
                        <th width="70"><? echo $company_arr[$company_name]; ?></th>
                        <th width="120">Style</th>
                        <th width="110">Body Part</th>
                        <th width="100">Color Type</th>
                        <th width="200">Fabric Desc.</th>
                        <th width="70">Fabric Gsm</th>
                        <th width="80">Fabric Dia</th>
                        <th width="80">Width/Dia Type</th>
                        <th width="80">Prog. Qnty</th>
                        <th width="80">Knitting Qnty</th>
                        <th width="200">Desc.Of Yarn</th>
                        <th width="200">Remarks</th>
                        <th></th>
                    </thead>
                    <tbody>
                    <?php
                    $sl = 0;
                    $noOfTd = 21;
                    $noOfColspanTd = 16;
                    $rowNo = 0;
                    $chkDia = array();
                    foreach ($rptPData as $bookingNo=>$bookingArr)
                    {
                        foreach ($bookingArr as $bodyPartId=>$bodyPartArr)
                        {
                            foreach ($bodyPartArr as $colorType=>$colorTypeArr)
                            {
                                foreach ($colorTypeArr as $febId=>$febArr)
                                {
                                    foreach ($febArr as $gsmNo=>$gsmArr)
                                    {
                                        foreach ($gsmArr as $diaNo=>$diaNoArr)
                                        {
                                            foreach ($diaNoArr as $progNo=>$row)
                                            {
                                                $sl++;
                                                $rowNo++;
                                                $rowColor = ($sl % 2 == 0 ? "#E9F3FF" : "#FFFFFF");

                                                $row['planId'] = $planIdArr[$bookingNo][$bodyPartId][$colorType][$febId][$gsmNo][$diaNo];
                                                $row['programQty'] = $programQtyArr[$progNo][$bookingNo][$bodyPartId][$colorType][$febId][$gsmNo][$diaNo]['program_qnty'];
												$fabricDtls = $program_data_array[$bookingNo][$bodyPartId][$colorType][$febId][$gsmNo][$diaNo]['fabricDtls'];
                                                //echo $progNo.'='.$bookingNo.'='.$bodyPartId.'='.$colorType.'='.$febId.'='.$gsmNo.'='.$diaNo."<br>";
                                                //for color qty
                                                $colorQtyStr = '';
                                                foreach($row['colorBookingQty'] as $colorId=>$colorValue)
                                                {
                                                    if($colorQtyStr != '')
                                                        $colorQtyStr .= ',';

                                                    $colorQtyStr .= $colorId.'-'.number_format($colorValue,2, '.', '');
                                                }

                                                if(empty($chkDia[$diaNo]))
                                                {
                                                    $chkDia[$diaNo] = $diaNo;
                                                    ?>
                                                    <tr bgcolor="#EFEFEF" id="tr_<? echo $rowNo; ?>">
                                                        <td colspan="<?php echo $noOfTd; ?>"><b>Dia/Width: <?php echo $row['dia']; ?></b></td>
                                                    </tr>
                                                    <?php
                                                    $rowNo++;
                                                }

                                                //for check box enable disable
                                                if ($knit_qnty_array[$progNo]*1 > 0)
												{
                                                    $disabled = "disabled='disabled'";
                                                    $disabled_1 = '';
												}
                                                else
												{
                                                    $disabled = "";
                                                    $disabled_1 = "disabled='disabled'";
												}
												//$rptData[$bookingNo][$bodyPartId][$colorType][$febId][$gsmNo][$diaNo]['progNo']
                                                ?>
                                                <tr bgcolor="<?php echo $rowColor; ?>" style="text-decoration:none; cursor:pointer; vertical-align:middle;" id="tr_<?php echo $rowNo; ?>">
                                                    <td width="40" align="center" valign="middle">
                                                        <input type="checkbox" id="tbl_<? echo $sl; ?>" name="check[]" value="<?php echo $rowNo; ?>" <? echo $disabled; ?> />
                                                    </td>
                                                    <td align="center"><?php echo $sl; ?></td>
                                                    <td><?php echo $row['planId']; ?></td>
                                                    <td><?php echo $progNo; ?></td>
                                                    <td><?php echo $bookingNo; ?></td>
													<td align="center"><?php echo $row['irRef']; ?></td>
                                                    <td><?php echo date('d-m-Y', strtotime($row['bookingDate'])); ?></td>
                                                    <td><?php echo $row['buyerDtls']; ?></td>
                                                    <td><?php echo $row['og']; ?></td>
                                                    <td align="center"><?php echo $row['styleDtls']; ?></td>
                                                    <td><?php echo $body_part[$bodyPartId]; ?></td>
                                                    <td><?php echo $color_type[$colorType]; ?></td>
                                                    <td><?php echo $fabricDtls; ?></td>
                                                    <td align="center"><?php echo $gsmNo; ?></td>
                                                    <td align="center"><?php echo $diaNo; ?></td>
                                                    <td><?php echo $row['diaWidthTypeDtls']; ?></td>
                                                    
                                                    <td>
                                                    	<input type="text" class="text_boxes_numeric" name="prog_qty[]" id="prog_qty_<?php echo $sl; ?>" value="<?php echo number_format($row['programQty'], 2, '.', ''); ?>" onDblClick="func_program_qty('<?php echo $progNo; ?>', '<?php echo $sl; ?>')" style="width:80px" <?php echo $disabled_1; ?> readonly />
														<input type="hidden" name="hdn_color_data[]" id="hdn_color_data_<?php echo $sl; ?>" value="" readonly />
                                                        </td>
                                                    <td align="right"><?php echo number_format($knit_qnty_array[$progNo], 2, '.', ''); ?></td>
                                                    <td><p><?php echo $row['yarnDtls']; ?></p></td>
                                                    <td><?php echo $row['remarks']; ?></td>
                                                    <td>
                                                    <input type="hidden" id="promram_id_<? echo $sl; ?>" name="promram_id[]" value="<? echo $progNo; ?>" />
                                                    <input type="button" value="Update" onClick="func_update(<?php echo $sl; ?>);" class="formbutton" style="width:70px" <?php echo $disabled_1; ?> />
                                                    </td>
                                                </tr>
                                                <?php
                                                $grandTotal['programQty'] += number_format($row['programQty'], 2, '.', '');
                                                $grandTotal['knittingQty'] += number_format($knit_qnty_array[$progNo], 2, '.', '');
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    ?>
                    </tbody>
                    <!--<tfoot>
                        <tr>
                            <th colspan="<?php echo $noOfColspanTd; ?>" align="right">Grand Total<input type="hidden" name="company_id" id="company_id" value="<?php echo $company_name; ?>"/></th>
                            <th align="right"><? echo number_format($grandTotal['programQty'], 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($grandTotal['knittingQty'], 2, '.', ''); ?></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>-->
                </table>
			</fieldset>
		</form>
		<?
	}
	die;
}

if ($action == "booking_item_details_18052021")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_name = str_replace("'", "", $cbo_company_name);
	$planning_status = str_replace("'", "", $cbo_planning_status);
	$approval_needed_or_not = str_replace("'", "", $approval_needed_or_not);

	//buyerIdCondition
	$buyerIdCondition = '';
	if (str_replace("'", "", $cbo_buyer_name) == 0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"] == 1)
		{
			if ($_SESSION['logic_erp']["buyer_id"] >0)
				$buyerIdCondition = " AND a.buyer_id IN (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyerIdCondition = '';
		}
		else
		{
			$buyerIdCondition = '';
		}
	}
	else
	{
		$buyerIdCondition = " AND a.buyer_id = ".$cbo_buyer_name."";
	}

	//styleIdCondition
	$styleIdCondition = '';
	if (str_replace("'", "", $hdn_style_id) != '')
	{
		$styleIdCondition = "AND b.style_id IN(".str_replace("'", "", $hdn_style_id).")";
	}

	//bookingNoCondition
	$bookingNoCondition = '';
	$pr_booking_cond = '';
	if(str_replace("'","",trim($txt_booking_no)) != '')
	{
		$txt_booking = "%".str_replace("'", "", trim($txt_booking_no))."%";
		$bookingNoCondition = "AND a.booking_no LIKE '".$txt_booking."'";
		$pr_booking_cond = "AND a.booking_no LIKE '".$txt_booking."'";
	}

	//bookingDateCondition
	$bookingDateCondition = '';
	$date_from = str_replace("'", "", trim($txt_date_from));
	$date_to = str_replace("'", "", trim($txt_date_to));
	if ($date_from != '' && $date_to != '')
	{
		if ($db_type == 0)
		{
			$bookingDateCondition = "AND a.booking_date BETWEEN '".change_date_format(trim($date_from), "yyyy-mm-dd", "-")."' AND '".change_date_format(trim($date_to), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$bookingDateCondition = "AND a.booking_date BETWEEN '".change_date_format(trim($date_from), '', '', 1)."' AND '".change_date_format(trim($date_to), '', '', 1)."'";
		}
	}

	$cbo_booking_type_short = explode("_",str_replace("'", "", trim($cbo_booking_type)));
	$cbo_booking_type = $cbo_booking_type_short[0];
	$is_short = $cbo_booking_type_short[1];

	if($cbo_booking_type>0)
	{
		$booking_type_cond = "AND a.booking_type = ".$cbo_booking_type."";
	}
	else
	{
		$booking_type_cond = "";
	}

	/*
	|--------------------------------------------------------------------------
	| main query
	| type = 1 = show button
	|--------------------------------------------------------------------------
	|
	*/
	if ($type == 1)
	{
		$sql = "
			SELECT
				a.id AS bmid, a.booking_no, a.booking_date, a.buyer_id,
				b.id AS bdid, b.style_id, b.body_part, b.color_type_id, b.lib_yarn_count_deter_id, b.fabric_description, b.gsm_weight, b.dia_width, b.grey_fabric AS finish_fabric, b.yarn_details, b.dtls_id, b.fabric_color, b.width_dia_type
			FROM
				wo_non_ord_samp_booking_mst a
				INNER JOIN wo_non_ord_samp_booking_dtls b ON a.booking_no = b.booking_no
			WHERE
				a.company_id = ".$company_name."
				AND a.item_category = 2
				AND a.status_active=1
				AND a.is_deleted=0
				AND b.fabric_source = 1
				AND b.status_active=1
				AND b.is_deleted=0
				".$buyerIdCondition."
				".$styleIdCondition."
				".$bookingNoCondition."
				".$bookingDateCondition."
				".$booking_type_cond."
			GROUP BY
				a.id, a.booking_no, a.booking_date, a.buyer_id,
				b.id, b.style_id, b.body_part, b.color_type_id, b.lib_yarn_count_deter_id, b.fabric_description, b.gsm_weight, b.dia_width, b.grey_fabric, b.yarn_details, b.dtls_id, b.fabric_color, b.width_dia_type
			ORDER BY
				b.dia_width,
				a.booking_no
		";
	}
	else
	{
		if ($db_type == 0)
		{
			$sql = "SELECT a.id, a.company_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as qnty, c.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$company_name and a.item_category=2 and a.fabric_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 $booking_cond $booking_date $booking_type_cond group by a.booking_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.dia_width order by cast(b.dia_width as unsigned),a.booking_no";// and a.buyer_id like '$buyer_name'
		}
		else
		{
			$sql = "
				SELECT
					a.id, a.company_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved,
					b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as qnty,
					c.style_ref_no
				FROM
					wo_booking_mst a,
					wo_booking_dtls b,
					wo_po_details_master c
				WHERE
					a.booking_no=b.booking_no
					and b.job_no=c.job_no
					and a.company_id=$company_name
					and a.item_category=2
					and a.fabric_source=1
					and a.status_active=1
					and a.is_deleted=0
					and b.status_active=1
					and b.is_deleted=0
					and b.grey_fab_qnty>0
					".$booking_cond."
					".$booking_date."
					".$booking_type_cond ."
				GROUP BY
					a.id, a.company_id, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, a.item_category,
					b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width,
					c.style_ref_no
				ORDER BY
					b.dia_width,
					a.booking_no
			";
		}
	}

	//echo $sql;die;
	$resultSet = sql_select($sql);
	if(empty($resultSet))
	{
		echo "<div style='width:100%;margin-top:10px;text-align:center'>".get_empty_data_msg()."</div>";
		die;
	}
	$styleDetails = return_library_array("SELECT id, style_ref_no FROM sample_development_mst", 'id', 'style_ref_no');


	/*
	|--------------------------------------------------------------------------
	| for plan information
	|--------------------------------------------------------------------------
	|
	*/
	$bookingNoArr = array();
	foreach ($resultSet as $row)
	{
		$bookingNoArr[$row[csf('booking_no')]] = $row[csf('booking_no')];
	}

	if ($db_type == 0)
	{
		$queryProgNo = " GROUP_CONCAT(c.dtls_id) AS prog_no,";
	}
	elseif ($db_type == 2)
	{
		$queryProgNo = " LISTAGG(c.dtls_id, ',') WITHIN GROUP (ORDER BY c.dtls_id) AS prog_no,";
	}

	$sqlPlan = "SELECT a.booking_no, c.id, c.mst_id, c.body_part_id, c.color_type_id, c.determination_id, c.gsm_weight, c.dia, ".$queryProgNo." SUM(c.program_qnty) AS program_qnty, c.status_active
	FROM
		ppl_planning_info_entry_mst a,
		ppl_planning_info_entry_dtls b,
		ppl_planning_entry_plan_dtls c
	WHERE
		a.id = b.mst_id
		AND b.id = c.dtls_id
		AND a.booking_no IN('".implode("','",$bookingNoArr)."')
		AND a.is_sales = 2
		AND b.status_active = 1
		AND b.is_deleted = 0
		AND b.is_sales = 2
		AND c.is_revised=0
		AND c.is_sales = 2
	GROUP BY
		a.booking_no,
		c.id, c.mst_id, c.yarn_desc, c.body_part_id, c.color_type_id, c.determination_id, c.gsm_weight, c.dia, c.status_active";
	//echo $sqlPlan;
	$resultPlan = sql_select($sqlPlan);
	$program_data_array = array();
	$planIdArr = array();
	$progNoArr = array();
	$planQty = array();
	foreach ($resultPlan as $rowPlan)
	{
		$planIdArr[$rowPlan[csf('booking_no')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]] = $rowPlan[csf('mst_id')];
		$progNoArr[$rowPlan[csf('booking_no')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][] = $rowPlan[csf('prog_no')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]]['program_qnty'] += $rowPlan[csf('program_qnty')];

		$planQty[$rowPlan[csf('mst_id')]][$rowPlan[csf('booking_no')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]]['program_qnty'] += $rowPlan[csf('program_qnty')];
	}
	unset($resultPlan);
	//echo "<pre>";
	//print_r($program_data_array);

	/*
	|--------------------------------------------------------------------------
	| for yarn details
	|--------------------------------------------------------------------------
	|
	*/
	$sqlYarn = "select b.booking_no, c.id, c.lot, c.yarn_count_id, c.yarn_type, c.yarn_comp_type1st, c.yarn_comp_type2nd, c.yarn_comp_percent1st, c.yarn_comp_percent2nd from inv_material_allocation_mst a, inv_material_allocation_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and b.booking_no in('".implode("','",$bookingNoArr)."') and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.booking_no, c.id, c.lot, c.yarn_count_id, c.yarn_type, c.yarn_comp_type1st, c.yarn_comp_type2nd, c.yarn_comp_percent1st, c.yarn_comp_percent2nd";
	$resultYarn = sql_select($sqlYarn);
	$yarnDetailsArr = array();
	foreach ($resultYarn as $row)
	{
		if ($row[csf('yarn_comp_percent2nd')] != 0)
		{
			$compostion = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		}
		else
		{
			$compostion = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$yarnDetailsArr[$row[csf('booking_no')]] = $row[csf('lot')] . " " . $yarn_count_details[$row[csf('yarn_count_id')]] . " " . $compostion . " " . $yarn_type[$row[csf('yarn_type')]];
	}
	//echo "<pre>";
	//print_r($yarnDetailsArr);

	/*
	|--------------------------------------------------------------------------
	| data preparing here
	|--------------------------------------------------------------------------
	|
	*/
	$rptData = array();
	$bookingQtyArr = array();
	foreach ($resultSet as $row)
	{
		$bookingQtyArr[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bookingQty'] += $row[csf('finish_fabric')];
	}
	//echo "<pre>";
	//print_r($bookingQtyArr);

	$dataPlanQty = array();
	foreach($planQty as $planId=>$planArr)
	{
		foreach($planArr as $bookingNo=>$bookingArr)
		{
			foreach($bookingArr as $bodyPart=>$bodyPartArr)
			{
				foreach($bodyPartArr as $colorTypeId=>$colorTypeArr)
				{
					foreach($colorTypeArr as $determinationId=>$determinationArr)
					{
						foreach($determinationArr as $gsm=>$gsmArr)
						{
							foreach($gsmArr as $dia=>$row)
							{
								$dataPlanQty[$planId]['planQty'] = number_format($row['program_qnty'], 2, '.', '');
								$dataPlanQty[$planId]['bookingQty'] = number_format($bookingQtyArr[$bookingNo][$bodyPart][$colorTypeId][$determinationId][$gsm][$dia]['bookingQty'], 2, '.', '');
							}
						}
					}
				}
			}
		}
	}
	//echo "<pre>";
	//print_r($dataPlanQty);

	foreach ($resultSet as $row)
	{
		//program Qty
		if(empty($program_data_array[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['program_qnty']))
		{
			$programQty = 0;
		}
		else
		{
			$programQty = $program_data_array[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['program_qnty'];
		}

		$bookingQty = $bookingQtyArr[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bookingQty'];
		$balanceQty = $bookingQty - $programQty;

		$plan_id = $planIdArr[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]];

		//if(($planning_status == 2 && $balanceQty <= 0) || ($planning_status == 1 && $balanceQty > 0))
		if (($planning_status == 2 && $dataPlanQty[$plan_id]['bookingQty'] <= $dataPlanQty[$plan_id]['planQty'] &&  $plan_id != '') || ($planning_status == 1 && ($dataPlanQty[$plan_id]['bookingQty'] > $dataPlanQty[$plan_id]['planQty'] || $plan_id == '')))
		{
			$i++;
			$isRequisition = 0;
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bookingNo'] = $row[csf('booking_no')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bookingDate'] = date('d-m-Y', strtotime($row[csf('booking_date')]));

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['buyerId'] = $row[csf('buyer_id')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['buyerDtls'] = $buyer_arr[$row[csf('buyer_id')]];

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['og'] = $row[csf('dtls_id')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['styleId'] = $row[csf('style_id')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['styleDtls'] = $styleDetails[$row[csf('style_id')]];

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bodyPartId'] = $row[csf('body_part')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bodyPartDtls'] = $body_part[$row[csf('body_part')]];

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['colorTypeId'] = $row[csf('color_type_id')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['colorTypeDtls'] = $color_type[$row[csf('color_type_id')]];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['fabricId'] = $row[csf('lib_yarn_count_deter_id')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['fabricDtls'] = $row[csf('fabric_description')];

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['gsm'] = $row[csf('gsm_weight')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['dia'] = $row[csf('dia_width')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['diaWidthTypeId'] = $row[csf('width_dia_type')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['diaWidthTypeDtls'] = $fabric_typee[$row[csf('width_dia_type')]];
			//$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['yarnDtls'] = $row[csf('yarn_details')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['yarnDtls'] = $yarnDetailsArr[$row[csf('booking_no')]];

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bookingQty'] = $bookingQty;
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['programQty'] = $programQty;
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['balanceQty'] = $balanceQty;

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['colorId'][$row[csf('fabric_color')]] = $row[csf('fabric_color')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['colorBookingQty'][$row[csf('fabric_color')]] += $row[csf('finish_fabric')];

			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bookingMstId'][$row[csf('bmid')]] = $row[csf('bmid')];
			$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['bookingDtlsId'][$row[csf('bdid')]] = $row[csf('bdid')];

			//planId
			if(empty($planIdArr[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]))
			{
				$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['planId'] = '';
			}
			else
			{
				$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['planId'] = $planIdArr[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]];
			}

			//porgNo
			if(empty($progNoArr[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]))
			{
				$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['progNo'] = '';
			}
			else
			{
				$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['progNo'] = $progNoArr[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]];
			}
		}
	}
	unset($resultSet);
	//echo "<pre>";
	//print_r($rptData); die;

	if(empty($rptData))
	{
		echo "<div style='width:100%;margin-top:10px;text-align:center'>".get_empty_data_msg()."</div>";
		die;
	}

	$sl = 0;
	$rptPData = array();
	$subTotal = array();
	$grandTotal = array();
	foreach ($rptData as $bookingNo=>$bookingArr)
	{
		foreach ($bookingArr as $bodyPartId=>$bodyPartArr)
		{
			foreach ($bodyPartArr as $colorType=>$colorTypeArr)
			{
				foreach ($colorTypeArr as $febId=>$febArr)
				{
					foreach ($febArr as $gsmNo=>$gsmArr)
					{
						foreach ($gsmArr as $diaNo=>$row)
						{
							if($diaNo != '')
							{
								$sl++;
								$rptPData[$diaNo][$sl] = $row;

								//subTotal
								$subTotal[$diaNo]['bookingQty'] += $row['bookingQty'];
								$subTotal[$diaNo]['programQty'] += $row['programQty'];
								$subTotal[$diaNo]['balanceQty'] += $row['balanceQty'];

								//grandTotal
								$grandTotal['bookingQty'] += $row['bookingQty'];
								$grandTotal['programQty'] += $row['programQty'];
								$grandTotal['balanceQty'] += $row['balanceQty'];
							}
						}
					}
				}
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| for report print
	|--------------------------------------------------------------------------
	|
	*/
	?>
	<form name="palnningEntry_2" id="palnningEntry_2">
		<fieldset style="width:1880px;">
			<legend>Fabric Description Details</legend>
			<input type="hidden" value="<? echo $type; ?>" name="txt_type" id="txt_type">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1870" class="rpt_table" id="tbl_list_search">
                    <thead>
                        <th width="40">SL</th>
                        <th width="70">Plan Id</th>
                        <th width="70">Prog. No</th>
                        <th width="115">Booking No</th>
                        <th width="80">Booking Date</th>
                        <th width="80">Start Date</th>
                        <th width="80">T.O.D</th>
                        <th width="70">Buyer</th>
                        <!--<th width="110">Order No</th>-->
                        <th width="70"><? echo $company_arr[$company_name]; ?></th>
                        <th width="120">Style</th>
                        <!--<th width="100">Internal Ref</th>
                        <th width="100">File No</th>-->
                        <th width="110">Body Part</th>
                        <th width="100">Color Type</th>
                        <th width="130">Fabric Desc.</th>
                        <th width="70">Fabric Gsm</th>
                        <th width="80">Fabric Dia</th>
                        <th width="80">Width/Dia Type</th>
                        <th width="90">Booking Qnty</th>
                        <th width="110">Prog. Qnty</th>
                        <th width="90">Balance Prog. Qnty</th>
                        <th>Desc.Of Yarn</th>
                    </thead>
                    <tbody>
                    <?php
					$noOfTd = 20;
					$noOfColspanTd = 16;
                    $rowNo = 0;
                    $chkDia = array();
                    foreach ($rptPData as $diaNo=>$diaArr)
                    {
                        foreach ($diaArr as $sl=>$row)
                        {
                            $rowNo++;
                            $rowColor = ($sl % 2 == 0 ? "#E9F3FF" : "#FFFFFF");

							//for color qty
							$colorQtyStr = '';
							foreach($row['colorBookingQty'] as $colorId=>$colorValue)
							{
								if($colorQtyStr != '')
									$colorQtyStr .= ',';

								$colorQtyStr .= $colorId.'-'.number_format($colorValue,2, '.', '');
							}

                            if(empty($chkDia[$diaNo]))
                            {
                                $chkDia[$diaNo] = $diaNo;
                                ?>
                                <tr bgcolor="#EFEFEF" id="tr_<? echo $rowNo; ?>">
                                    <td colspan="<?php echo $noOfTd; ?>"><b>Dia/Width: <?php echo $row['dia']; ?></b></td>
                                </tr>
                                <?php
                                $rowNo++;
                            }
                            ?>
                            <tr bgcolor="<?php echo $rowColor; ?>" style="text-decoration:none; cursor:pointer; vertical-align:middle;" onClick="fnc_selected_row('<?php echo $rowNo; ?>', '')" id="tr_<?php echo $rowNo; ?>">
                                <td align="center"><?php echo $sl; ?></td>
                                <td><?php echo $row['planId']; ?></td>
                                <td>
                                <?php
                                $print_program_no = "";
                                foreach ($row['progNo'] as $prog)
                                {
                                    if($prog != "")
                                    {
                                        $print_program_no .= "<a href='##' onclick=\"generate_report2(".$company_name.",".$prog.",".$program_info_format_id.")\">".$prog."</a>,";
                                    }
                                }
                                echo rtrim($print_program_no,", ");
                                ?>
                                </td>
                                <td><?php echo $row['bookingNo']; ?></td>
                                <td><?php echo date('d-m-Y', strtotime($row['bookingDate'])); ?></td>
                                <td><?php //echo $row['partyDtls']; ?></td>
                                <td><?php //echo $row['partyDtls']; ?></td>
                                <td><?php echo $row['buyerDtls']; ?></td>
                                <td><?php echo $row['og']; ?></td>
                                <td align="center"><?php echo $row['styleDtls']; ?></td>
                                <td><?php echo $row['bodyPartDtls']; ?></td>
                                <td><?php echo $row['colorTypeDtls']; ?></td>
                                <td><?php echo $row['fabricDtls']; ?></td>
                                <td align="center"><?php echo $row['gsm']; ?></td>
                                <td align="center"><?php echo $row['dia']; ?></td>
                                <td><?php echo $row['diaWidthTypeDtls']; ?></td>
                                <td align="right"><?php echo $row['bookingQty']; ?></td>
                                <td align="right"><?php echo number_format($row['programQty'],2); ?></td>
                                <td align="right"><?php echo number_format($row['balanceQty'],2); ?></td>
                                <td><p><?php echo $row['yarnDtls']; ?></p></td>
<input type="hidden" name="hdnBuyerId[]" id="hdnBuyerId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['buyerId']; ?>" />
<input type="hidden" name="hdnBookingNo[]" id="hdnBookingNo_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['bookingNo']; ?>" />
<input type="hidden" name="hdnFabricId[]" id="hdnFabricId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['fabricId']; ?>" />
<input type="hidden" name="hdnFabricDtls[]" id="hdnFabricDtls_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['fabricDtls']; ?>" />
<input type="hidden" name="hdnBodyPartId[]" id="hdnBodyPartId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['bodyPartId']; ?>" />
<input type="hidden" name="hdnGsm[]" id="hdnGsm_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['gsm']; ?>" />
<input type="hidden" name="hdnDia[]" id="hdnDia_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['dia']; ?>" />
<input type="hidden" name="hdnDiaWidthType[]" id="hdnDiaWidthType_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['diaWidthTypeId']; ?>" />

<input type="hidden" name="hdnBookingQty[]" id="hdnBookingQty_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['bookingQty']; ?>" />
<input type="hidden" name="hdnProgramQty[]" id="hdnProgramQty_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['programQty']; ?>" />
<input type="hidden" name="hdnBalanceQty[]" id="hdnBalanceQty_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['balanceQty']; ?>" />

<input type="hidden" name="hdnColorId[]" id="hdnColorId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo implode(",", $row['colorId']); ?>" />
<input type="hidden" name="hdnColorTypeId[]" id="hdnColorTypeId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['colorTypeId']; ?>" />
<input type="hidden" name="hdnIsRequisition[]" id="hdnIsRequisition_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['isRequisition']; ?>" />
<input type="hidden" name="hdnOrderMstId[]" id="hdnOrderMstId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo implode(",", $row['orderMstId']); ?>" />
<input type="hidden" name="hdnOrderDtlsId[]" id="hdnOrderDtlsId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo implode(",", $row['orderDtlsId']); ?>" />
<input type="hidden" name="hdnOrderBrkDownId[]" id="hdnOrderBrkDownId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo implode(",", $row['orderBrkDownId']); ?>" />
<input type="hidden" name="hdnPlanId[]" id="hdnPlanId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['planId']; ?>" />
<input type="hidden" name="hdnJobNo[]" id="hdnJobNo_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['jobNo']; ?>" />
<input type="hidden" name="hdnColorQty[]" id="hdnColorQty_<? echo $rowNo; ?>" class="text_boxes" value="<?php echo $colorQtyStr; ?>"/>
                            </tr>
                            <?php
                        }
                        $rowNo++;
                        ?>
                        <tr bgcolor="#CCCCCC" id="tr_<? echo $rowNo; ?>">
                            <th colspan="<?php echo $noOfColspanTd; ?>" align="right">Sub Total</th>
                            <th align="right"><? echo number_format($subTotal[$diaNo]['bookingQty'], 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($subTotal[$diaNo]['programQty'], 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($subTotal[$diaNo]['balanceQty'], 2, '.', ''); ?></th>
                            <th></th>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="<?php echo $noOfColspanTd; ?>" align="right">Grand Total<input type="hidden" name="company_id" id="company_id" value="<?php echo $company_name; ?>"/></th>
                            <th align="right"><? echo number_format($grandTotal['bookingQty'], 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($grandTotal['programQty'], 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($grandTotal['balanceQty'], 2, '.', ''); ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
				</table>
			</div>
		</fieldset>
	</form>
	<?
	die;
}

/*
|--------------------------------------------------------------------------
| actn_program
|--------------------------------------------------------------------------
|
*/
if ($action == "actn_program")
{
	//echo 'su..re'; die;
	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<script>
			function fnc_image_upload()
			{
				var img_ref_id = $("#update_dtls_id").val();
				//alert(img_ref_id);return;
				if(img_ref_id == "")
				{
					alert('Please Select or Save any Information before File Upload.');return;
				}
				file_uploader ( '../../', img_ref_id,'', 'Planning Info Entry For Sample Without Order', 0,1);
			}
			function window_close(){
			parent.emailwindow.hide();
			}
		</script>

	<?
	$current_date = date("d-m-Y");
	$plan_id = $planId;

	$start_date = trim($start_date);
	$end_date = trim($end_date);
	$dataArray = sql_select("select id, machine_dia, machine_gg, fabric_dia, stitch_length from fabric_mapping where mst_id = ".$fabricId." and status_active=1 and is_deleted=0");
	$color_mixing_in_knittingplan = return_field_value("color_mixing_in_knitting_plan", "variable_settings_production", "company_name = ".$companyId." and variable_list=53");

	if($color_mixing_in_knittingplan==1)
	{
		$color_mixing_in_knittingplan_yes = 1;
	}
	else
	{
		$color_mixing_in_knittingplan_yes = 0;
	}
	?>
	<script>
		var permission = '<?php echo $permission; ?>';
		var dataPre = '<?php echo $data; ?>';
		var companyId = '<?php echo $companyId; ?>';
		var hdnBookingEntryForm = '<?php echo $hdnBookingEntryForm; ?>';
		var buyerId = '<?php echo $buyerId; ?>';
		var bookingNo = '<?php echo trim($bookingNo); ?>';
		var fabricId = '<?php echo $fabricId; ?>';
		var fabricDtls = '<?php echo trim($fabricDtls); ?>';
		var gsm = '<?php echo trim($gsm); ?>';
		var dia = '<?php echo trim($dia); ?>';
		var Diadata = '<?php echo trim($Diadata); ?>';
		var Diaw = '<?php echo trim($Diaw); ?>';

		var diaWidthType = '<?php echo $diaWidthType; ?>';

		var bookingQty = '<?php echo trim($bookingQty); ?>';
		var programQty = '<?php echo $programQty; ?>';
		var balanceQty = '<?php echo $balanceQty; ?>';

		var colorId = '<?php echo $colorId; ?>';
		var colorTypeId = '<?php echo $colorTypeId; ?>';
		var bodyPartId = '<?php echo $bodyPartId; ?>';
		var bookingMstId = '<?php echo $bookingMstId; ?>';
		var bookingDtlsId = '<?php echo $bookingDtlsId; ?>';
		var planId = '<?php echo $planId; ?>';
		var color_mixing_in_knittingplan_yes = '<? echo $color_mixing_in_knittingplan_yes; ?>';

		var colorIdZS = '<?php echo $colorIdZS; ?>';
		var colorTypeIdZS = '<?php echo $colorTypeIdZS; ?>';
		var bodyPartIdZS = '<?php echo $bodyPartIdZS; ?>';

		function openpage_machine()
		{
			var save_string = $('#save_data').val();
			var txt_machine_dia = $('#txt_machine_dia').val();
			var update_dtls_id = $('#update_dtls_id').val();
			var companyId = $('#cbo_knitting_party').val();  <? //echo $companyId; ?>
			var allowed_date_qnty_string = $('#allowed_date_qnty_string').val();
			var title = 'Machine Info';
			var page_link = 'planning_info_entry_for_sample_without_order_controller.php?action=machine_info_popup&save_string=' + save_string + '&companyId=' + companyId + '&txt_machine_dia=' + txt_machine_dia + '&update_dtls_id=' + update_dtls_id + '&allowed_date_qnty_string=' +allowed_date_qnty_string;
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=300px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function ()
			{
				var theform = this.contentDoc.forms[0];
				var hidden_machine_no = this.contentDoc.getElementById("hidden_machine_no").value;
				var hidden_machine_id = this.contentDoc.getElementById("hidden_machine_id").value;
				var save_string = this.contentDoc.getElementById("save_string").value;
				var hidden_machine_capacity = this.contentDoc.getElementById("hidden_machine_capacity").value;
				var hidden_distribute_qnty = this.contentDoc.getElementById("hidden_distribute_qnty").value;
				var hidden_min_date = this.contentDoc.getElementById("hidden_min_date").value;
				var hidden_max_date = this.contentDoc.getElementById("hidden_max_date").value;
				var allowed_date_qnty_string = this.contentDoc.getElementById("hidden_all_allowed_date_qnty").value;

				$('#txt_machine_no').val(hidden_machine_no);
				$('#machine_id').val(hidden_machine_id);
				$('#save_data').val(save_string);
				$('#txt_machine_capacity').val(hidden_machine_capacity);
				$('#txt_distribution_qnty').val(hidden_distribute_qnty);
				$('#txt_start_date').val(hidden_min_date);
				$('#txt_end_date').val(hidden_max_date);
				$('#allowed_date_qnty_string').val(allowed_date_qnty_string);

                //var days_req=hidden_distribute_qnty*1/hidden_machine_capacity*1;
                //$('#txt_days_req').val(days_req.toFixed(2));
                days_req();
            }
        }

        function days_req()
		{
        	txt_start_date = $('#txt_start_date').val();
        	txt_end_date = $('#txt_end_date').val();

        	if (txt_start_date != "" && txt_end_date != "")
			{
        		var days_req = date_diff('d', txt_start_date, txt_end_date);
        		$('#txt_days_req').val(days_req + 1);
        	}
        	else
			{
        		$('#txt_days_req').val('');
        	}
        }

        function openpage_color()
		{
        	var hidden_color_id = $('#hidden_color_id').val();
        	var prog_no = $('#update_dtls_id').val();
        	var save_color_wise_prog_data = $('#hidden_color_wise_prog_data').val();
        	var title = 'Color Info';
        	var page_link = "planning_info_entry_for_sample_without_order_controller.php?action=color_info_popup&companyId="+<? echo $companyId; ?>+"&booking_no="+"<? echo trim($bookingNo); ?>"+"&dia="+"<?php echo $dia; ?>"+"&hidden_color_id="+colorIdZS +"&save_color_wise_prog_data="+save_color_wise_prog_data +"&color_mixing_in_knittingplan_yes="+color_mixing_in_knittingplan_yes+"&plan_id="+"<?php echo $plan_id; ?>"+ '&prog_no=' + prog_no+"&bodyPartId="+"<?php echo $bodyPartIdZS; ?>"+"&colorTypeId="+"<?php echo $colorTypeIdZS; ?>"+"&fabricId="+"<?php echo $fabricId; ?>"+"&gsm="+"<?php echo $gsm; ?>"+"&hdnBookingEntryForm="+"<?php echo $hdnBookingEntryForm; ?>";
        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title,"width=670px,height=300px,center=1,resize=1,scrolling=0", '../');
        	emailwindow.onclose = function ()
        	{
        		var theform = this.contentDoc.forms[0];
        		var hidden_color_no = this.contentDoc.getElementById("txt_selected").value;
        		var hidden_color_id = this.contentDoc.getElementById("txt_selected_id").value;
        		var hidden_color_prog_blance = this.contentDoc.getElementById("txt_selected_color_bl_qty").value;
        		var hidden_color_wise_prog_data = this.contentDoc.getElementById("hidden_color_wise_prog_data").value;
        		var hidden_total_prog_qty = this.contentDoc.getElementById("hidden_total_prog_qty").value;

        		$('#txt_color').val(hidden_color_no);
        		$('#hidden_color_id').val(hidden_color_id);
        		$('#txt_program_qnty').val(hidden_color_prog_blance);
        		$('#hidden_color_wise_prog_data').val(hidden_color_wise_prog_data);
        		$('#txt_program_qnty').val(hidden_total_prog_qty);
        		$('#hidden_color_wise_total').val(hidden_total_prog_qty);
        	}
        }

        function fnc_program_entry(operation)
        {
        	var knit_source = $("#cbo_knitting_source").val();

			var knit_source = $("#cbo_knitting_source").val();

			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][428]);?>' && knit_source != 3){
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][428]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][428]);?>')==false)
				{
					return;
				}
			}



			if(knit_source == 1)
			{
        		if (form_validation('cbo_knitting_party*txt_color*cbo_color_range*txt_machine_dia*txt_machine_gg*txt_program_qnty*txt_stitch_length*cbo_location_name*cbo_dia_width_type','cbo_knitting_party*Color*Color Range*Machine Dia*Machine GG*Program Quantity*Stitch Length*Location*Dia Width')==false)
        		{
        			return;
        		}
        	}
			else
			{
        		if (form_validation('cbo_knitting_party*txt_color*cbo_color_range*txt_machine_dia*txt_machine_gg*txt_program_qnty*txt_stitch_length*cbo_dia_width_type','knitting party*Color*Color Range*Machine Dia*Machine GG*Program Quantity*Stitch Length*Dia Width')==false)
        		{
        			return;
        		}
        	}

    		var hiddenProgramQnty = $("#hiddenProgramQnty").val()*1;
    		var balanceProgramQnty = $("#balanceProgramQnty").val()*1;
    		var program_qnty = $("#txt_program_qnty").val()*1;

    		if(operation == 0)
    		{
    			if(balanceProgramQnty  <  program_qnty)
    			{
    				alert("Program Qnty Cann't exceed Balance Qnty");
    				$("#txt_program_qnty").val(balanceProgramQnty);
    				return;
    			}
    		}
    		else if(operation == 1)
    		{
    			if((program_qnty - hiddenProgramQnty) > balanceProgramQnty)
    			{
    				alert("Program Qnty Cann't exceed Balance Qnty");
    				$("#txt_program_qnty").val(hiddenProgramQnty);
    				return;
    			}
    		}

        	if(operation == 0 || operation == 1)
			{
        		var hidden_color_wise_total = $('#hidden_color_wise_total').val()*1;
        		var txt_program_qnty = $('#txt_program_qnty').val()*1;

        		if( hidden_color_wise_total != txt_program_qnty  )
        		{
        			alert('Mismatch Program quantity and Color Wise program quantity');
        			$('#txt_program_qnty').focus();
        			return;
        		}
        	}

        	var booking_qnty = $("#hdn_booking_qnty").val() * 1;
        	//var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('cbo_knitting_source*cbo_knitting_party*cbo_subcontract_party*txt_color*txt_machine_dia*txt_machine_gg*txt_program_qnty*txt_stitch_length*txt_spandex_stitch_length*txt_draft_ratio*machine_id*txt_machine_capacity*txt_distribution_qnty*cbo_knitting_status*txt_start_date*txt_end_date*txt_program_date*cbo_feeder*txt_remarks*txt_attention*save_data*updateId*update_dtls_id*cbo_color_range*cbo_dia_width_type*hidden_color_id*txt_fabric_dia*hidden_no_of_feeder_data*cbo_location_name*hidden_advice_data*hidden_collarCuff_data*txt_grey_dia*hiddenProgramQnty*balanceProgramQnty*hidden_count_feeding_data*txt_co_efficient*hidden_bodypartID_data*allowed_date_qnty_string*pic_up_po_ids*upd_plan_po_ids*txt_batch_no*hidden_color_wise_prog_data', "../../") + '&companyId='+<? echo $companyId; ?>+'&gsm=' + '<? echo trim($gsm); ?>' + '&dia=' + '<? echo trim($dia); ?>' + '&desc=' + '<? echo trim($desc); ?>' + '&start_date=' + '<? echo $start_date; ?>' + '&end_date=' + '<? echo $end_date; ?>' + '&determination_id='+'<? echo $determination_id; ?>'+'&booking_no=' + '<? echo trim($booking_no); ?>' + '&data='+'<? echo $data; ?>'+'&body_part_id='+'<? echo $body_part_id; ?>'+'&color_type_id='+'<? echo $color_type_id; ?>'+ '&fabric_typee='+'<? echo $fabric_type; ?>'+ '&tot_booking_qnty='+'<? echo trim($booking_qnty); ?>'+'&buyer_id=' +'<? echo $buyer_id; ?>' +'&hdn_booking_qnty=' + booking_qnty+"&pre_cost_id="+"<? echo $pre_cost_id; ?>"+"&po_id="+"<? echo $po_id; ?>";

			var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('cbo_knitting_source*cbo_knitting_party*cbo_subcontract_party*txt_color*txt_machine_dia*txt_machine_gg*txt_program_qnty*txt_stitch_length*txt_spandex_stitch_length*txt_draft_ratio*machine_id*txt_machine_capacity*txt_distribution_qnty*cbo_knitting_status*txt_start_date*txt_end_date*txt_program_date*cbo_feeder*txt_remarks*txt_attention*save_data*updateId*update_dtls_id*cbo_color_range*cbo_dia_width_type*hidden_color_id*txt_fabric_dia*hidden_no_of_feeder_data*cbo_location_name*hidden_advice_data*hidden_collarCuff_data*txt_grey_dia*hiddenProgramQnty*balanceProgramQnty*hidden_count_feeding_data*txt_co_efficient*hidden_bodypartID_data*allowed_date_qnty_string*pic_up_po_ids*upd_plan_po_ids*txt_batch_no*hidden_color_wise_prog_data*txt_fabric_type', "../../") + '&companyID='+companyId+'&gsm='+gsm+'&dia='+dia+'&desc='+fabricDtls+'&start_date='+'<? echo $start_date; ?>'+'&end_date='+'<? echo $end_date; ?>'+'&determination_id='+fabricId+'&booking_no='+bookingNo+'&dataPre='+dataPre+'&body_part_id='+bodyPartId+'&color_type_id='+colorTypeId+ '&fabric_typee='+diaWidthType+ '&tot_booking_qnty='+bookingQty+'&buyer_id=' +buyerId +'&hdn_booking_qnty=' + booking_qnty;
			//alert(data);
        	freeze_window(operation);
        	http.open("POST", "planning_info_entry_for_sample_without_order_controller.php", true);
        	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        	http.send(data);
        	http.onreadystatechange = fnc_program_entry_Reply_info;
        }

        function fnc_program_entry_Reply_info()
		{
        	if (http.readyState == 4) {
                //release_freezing();return;//alert(http.responseText);
                var reponse = trim(http.responseText).split('**');

                show_msg(reponse[0]);

                if ((reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2)) {
                	var progBalance = 0;
                	var PreProgramQnty = $("#hiddenProgramQnty").val()*1;
                	if(reponse[0] == 0 ){
                		progBalance = $("#balanceProgramQnty").val()*1 - $("#txt_program_qnty").val()*1;
                	}
                	else if(reponse[0] == 1 )
                	{
                		progBalance = $("#balanceProgramQnty").val()*1 + PreProgramQnty - $("#txt_program_qnty").val()*1 ;
                	}
                	reset_form('programQnty_1', '', '', 'txt_start_date,<? echo $start_date; ?>*txt_end_date,<? echo $end_date;?>*txt_program_date,<? echo $current_date;?>', '', 'hdn_booking_qnty');
                	$('#updateId').val(reponse[1]);
                	show_list_view(reponse[1]+'_'+'<?php echo trim($dia); ?>', 'planning_info_details', 'list_view', 'planning_info_entry_for_sample_without_order_controller', '');
                	set_button_status(0, permission, 'fnc_program_entry', 1);

                    $("#txt_program_qnty").val(progBalance.toFixed(2));
                    $("#balanceProgramQnty").val(progBalance.toFixed(2));

                    $('#cbo_knitting_source').attr('disabled',false);
                    $('#cbo_knitting_party').attr('disabled',false);

                }
                else if (reponse[0] == 13 || reponse[0] == 14) {
              	alert(reponse[1]);
                }
                release_freezing();
            }
        }

        function active_inactive()
		{
        	var knitting_source = document.getElementById('cbo_knitting_source').value;

        	reset_form('', '', 'txt_machine_no*machine_id*txt_machine_capacity*txt_distribution_qnty*txt_days_req*cbo_location_name', 'txt_start_date,<? echo $start_date; ?>*txt_end_date,<? echo $end_date; ?>*txt_program_date,<? echo $current_date; ?>', '', '');

        	if (knitting_source == 1) {
        		document.getElementById('txt_machine_no').disabled = false;
        		document.getElementById('cbo_location_name').disabled = false;
        	}
        	else {
        		document.getElementById('txt_machine_no').disabled = true;
        		document.getElementById('cbo_location_name').disabled = true;
        	}
        }

        function openpage_feeder()
		{
        	var no_of_feeder_data = $('#hidden_no_of_feeder_data').val();
        	//var color_type_id ='<? echo $color_type_id; ?>';
        	var color_type_id ='<? echo $colorTypeId; ?>';

        	if (!(color_type_id == 2 || color_type_id == 3 || color_type_id == 4 || color_type_id == 33)) {
        		alert("Only for Stripe");
        		return;
        	}

        	//var page_link = 'planning_info_entry_for_sample_without_order_controller.php?action=feeder_info_popup&no_of_feeder_data=' + no_of_feeder_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>';
        	var page_link = 'planning_info_entry_for_sample_without_order_controller.php?action=feeder_info_popup&no_of_feeder_data=' + no_of_feeder_data + '&pre_cost_id=' + '<? echo $bookingDtlsId; ?>';

        	var title = 'Stripe Measurement Info';

        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=640px,height=300px,center=1,resize=1,scrolling=0', '../');
        	emailwindow.onclose = function () {
        		var theform = this.contentDoc.forms[0];
        		var hidden_no_of_feeder_data = this.contentDoc.getElementById("hidden_no_of_feeder_data").value;

        		$('#hidden_no_of_feeder_data').val(hidden_no_of_feeder_data);
        	}
        }

        function openpage_collarCuff()
		{
        	var collarCuff_data = $('#hidden_collarCuff_data').val();
        	var hidden_bodypartID_data = $('#hidden_bodypartID_data').val();
        	var update_dtls_id = $('#update_dtls_id').val();
        	if (update_dtls_id == "") {
        		alert("Save Data First");
        		return;
        	}
        	var page_link = 'planning_info_entry_for_sample_without_order_controller.php?action=collarCuff_info_popup&collarCuff_data=' + collarCuff_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>&update_dtls_id=' + update_dtls_id +'&hidden_bodypartID_data='+hidden_bodypartID_data;
        	var title = 'Collar & Cuff Measurement Info';

        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=540px,height=300px,center=1,resize=1,scrolling=0', '../');
        	emailwindow.onclose = function () {
        		var theform = this.contentDoc.forms[0];
        		var hidden_collarCuff_data = this.contentDoc.getElementById("hidden_collarCuff_data").value;

        		$('#hidden_collarCuff_data').val(hidden_collarCuff_data);
        	}
        }

        function openpage_advice()
		{
        	var hidden_advice_data = $('#hidden_advice_data').val();

        	var page_link = 'planning_info_entry_for_sample_without_order_controller.php?action=advice_info_popup&hidden_advice_data=' + hidden_advice_data;
        	var title = 'Advice Info';

        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=200px,center=1,resize=1,scrolling=0', '../');
        	emailwindow.onclose = function ()
			{
        		var theform = this.contentDoc.forms[0];
        		var advice_data = this.contentDoc.getElementById("txt_advice").value;
        		$('#hidden_advice_data').val(advice_data);
        	}
        }

		function openpage_count_feeding()
		{
			var count_feeding_data = $('#hidden_count_feeding_data').val();
			var update_dtls_id = $('#update_dtls_id').val();
			if (update_dtls_id == "") {
				alert("Save Data First");
				return;
			}
			var page_link = 'planning_info_entry_for_sample_without_order_controller.php?action=count_feeding_data_popup&count_feeding_data=' + count_feeding_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>&update_dtls_id=' + update_dtls_id;
			var title = 'Count Feeding';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=300px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var hidden_count_feeding_data = this.contentDoc.getElementById("hidden_count_feeding_data").value;
				$('#hidden_count_feeding_data').val(hidden_count_feeding_data);
			}
		}

		function balance_cal()
		{
			$("#hiddenProgramQnty").val($("#txt_program_qnty").val()*1);
		}

		function getValutAttention(knit_source_id)
		{
				//alert(knit_source_id);  return;
				var knit_source_id=$("#cbo_knitting_source").val()*1;

				if(knit_source_id==1)
				{
					var inHouse_knit_party_id=$("#cbo_knitting_party").val()*1;
					get_php_form_data(knit_source_id+'*'+inHouse_knit_party_id, "check_last_attention_action", "planning_info_entry_for_sample_without_order_controller" );
				}
				else
				{
					var outBound_knit_party_id=$("#cbo_knitting_party").val()*1;
					get_php_form_data(knit_source_id+'*'+outBound_knit_party_id, "check_last_attention_action", "planning_info_entry_for_sample_without_order_controller" );

				}
				return;
		}

		function fn_knit_production(knit_source_id)
		{
			if(knit_source_id==1)
			{
				$("#location_caption").removeClass("change_color2");
				$("#location_caption").addClass("change_color");
			}
			else
			{
				$("#location_caption").removeClass("change_color");
				$("#location_caption").addClass("change_color2");
			}
			var knit_sys = $("#hidden_knit_sys").val();
			var hidden_knit_source = $("#hidden_knitting_source").val();
			var companyId = $('#hidden_company').val();
			if(knit_sys != "")
			{
				alert("Knitting Source Can't Change.Knitting Production Found! ID-"+knit_sys)
				$("#cbo_knitting_source").val(hidden_knit_source);
        	load_drop_down( 'planning_info_entry_for_sample_without_order_controller', hidden_knit_source+'**'+companyId, 'load_drop_down_knitting_party','knitting_party');//class="must_entry_caption"
    		}
		    return;
		}

		$(document).ready(function (){
			$('#txt_stitch_length').keyup(function(){
	        	var th = $(this);
		        th.val( th.val().replace(/[^a-zA-Z0-9,. -]+$/, function(str){
		        	return '';
	        	}));
	    	});

			//Disable Ctrl+V
	    	var ctrlDown = false,
	        ctrlKey = 17,
	        cmdKey = 91,
	        vKey = 86,
	        cKey = 67;

		    $(document).keydown(function(e) {
		        if (e.keyCode == ctrlKey || e.keyCode == cmdKey) ctrlDown = true;
		    }).keyup(function(e) {
		        if (e.keyCode == ctrlKey || e.keyCode == cmdKey) ctrlDown = false;
		    });

		    $("#txt_stitch_length").keydown(function(e) {
		        if (ctrlDown && (e.keyCode == vKey || e.keyCode == cKey)) return false;
		    });

		    //Disable part of page
		    $('#txt_stitch_length').bind('cut copy paste', function (e) {
		        e.preventDefault();
		    });
    	});
	</script>
</head>
<body>
	<div align="center">
		<?php
			echo load_freeze_divs("../../", $permission, 1);
			$current_date = date("d-m-Y");
		?>
		<form name="programQnty_1" id="programQnty_1">
			<fieldset style="width:900px;">
                <table border="0" cellpadding="0" cellspacing="0" rules="all" align="center" width="890">
                    <!--<thead>
                        <th width="300">Fabric Description</th>
                        <th width="80">GSM</th>
                        <th width="80">Dia</th>
                        <th>Booking Qnty</th>
                    </thead>
                    <tr bgcolor="#FFFFFF">
                        <td>
                            <p><? echo $fabricDtls; ?></p>
                            <input type="hidden" name="hdn_fab_desc" id="hdn_fab_desc" value="<? echo trim($fabricDtls); ?>" readonly/>
                        </td>
                        <td><? echo $gsm; ?></td>
                        <td><? echo $dia; ?></td>
                        <td align="right"><? echo number_format($orderQty, 2); ?></td>
                    </tr>-->
                    <tbody style="font-weight:bold;">
                        <tr>
                            <td width="90" align="right">Dia</td>
                            <td width="10" align="center">:</td>
                            <td width="790"><?php echo $dia; ?></td>
                        </tr>
                        <tr>
                            <td align="right">GSM</td>
                            <td align="center">:</td>
                            <td><?php echo $gsm; ?></td>
                        </tr>
                        <tr>
                            <td align="right">Booking Qty</td>
                            <td align="center">:</td>
                            <td><?php echo number_format($bookingQty, 2); ?></td>
                        </tr>
                        <tr>
                            <td align="right">Fabric Description</td>
                            <td align="center">:</td>
                            <td><?php echo $fabricDtls; ?><input type="hidden" name="hdn_fab_desc" id="hdn_fab_desc" value="<? echo trim($fabricDtls); ?>" readonly/></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
            <fieldset style="width:900px; margin-top:5px;">
                <legend>New Entry</legend>
                <input type="hidden" id="hdn_booking_qnty" name="hdn_booking_qnty" value="<? echo $bookingQty; ?>"/>
                <table width="900" align="center" border="0">
                    <tr>
                        <td>Knitting Source</td>
                        <td>
                            <?
							echo create_drop_down("cbo_knitting_source", 152, $knitting_source, "", 1, "-- Select --", 0, "active_inactive();load_drop_down( 'planning_info_entry_for_sample_without_order_controller', this.value+'**'+$companyId, 'load_drop_down_knitting_party','knitting_party'); load_drop_down( 'planning_info_entry_for_sample_without_order_controller',$companyId, 'load_drop_down_location', 'location_td' );getValutAttention(this.value);fn_knit_production(this.value);", 0, '1,3');
                            ?>
                            <input type="hidden" name="hidden_knitting_source" id="hidden_knitting_source" value="">
                            <input type="hidden" name="hidden_company" id="hidden_company" value="<? echo $companyId ?>">
                            <input type="hidden" name="hidden_knit_sys" id="hidden_knit_sys" value="">
                            <input type="hidden" name="pic_up_po_ids" id="pic_up_po_ids" value="<? echo $po_id; ?>">
                            <input type="hidden" name="upd_plan_po_ids" id="upd_plan_po_ids" value="">

                        </td>
                        <td class="must_entry_caption">Knitting Party</td>
                        <td id="knitting_party">
                            <?
                            echo create_drop_down("cbo_knitting_party", 152, $blank_array, "", 1, "--Select Knit Party--", 1, "load_drop_down( 'planning_info_entry_for_sample_without_order_controller', this.value, 'load_drop_down_location', 'location_td' );getValutAttention(this.value);");
                            ?>
                        </td>
                        <td>Sub-Subcontract</td>
                        <td id="subContract">
                            <?
                            echo create_drop_down("cbo_subcontract_party", 175, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 and a.is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Party--","", "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Color</td>
                        <td>
                            <input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:140px;" placeholder="Browse" onClick="openpage_color();" readonly/>
                            <input type="hidden" name="hidden_color_id" id="hidden_color_id" readonly/>
                            <input type="hidden" name="hidden_color_wise_prog_data" id="hidden_color_wise_prog_data" readonly>
                            <input type="hidden" name="hidden_color_wise_total" id="hidden_color_wise_total" readonly>
                        </td>
                        <td class="must_entry_caption">Color Range</td>
                        <td>
                            <?
                            echo create_drop_down("cbo_color_range", 152, $color_range, "", 1, "-- Select --", 0, "");
                            ?>
                        </td>
                        <td class="must_entry_caption">Machine Dia</td>
                        <td>
                            <input type="text" name="txt_machine_dia" id="txt_machine_dia" class="text_boxes_numeric" style="width:60px;" maxlength="3" title="Maximum 3 Character" value="<? echo $dataArray[0][csf('machine_dia')]; ?>"/>
                            <?
                            echo create_drop_down("cbo_dia_width_type", 100, $fabric_typee, "", 1, "-- Select --", $fabric_type, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Machine GG</td>
                        <td>
                            <input type="text" name="txt_machine_gg" id="txt_machine_gg" class="text_boxes"
                            style="width:140px;" value="<? echo $dataArray[0][csf('machine_gg')]; ?>"/>
                        </td>
                        <td>Finish Fabric Dia</td>
                        <td>
                            <input type="text" name="txt_fabric_dia" id="txt_fabric_dia" class="text_boxes"
                            style="width:140px;" value="<? echo $dataArray[0][csf('fabric_dia')]; ?>"/>
                        </td>
                        <td class="must_entry_caption">Program Qnty</td>
                        <td>
                            <input type="hidden" value="<? echo number_format($programQty, 2, '.', ''); ?>" id="hiddenProgramQnty">
                            <input type="hidden" value="<? echo number_format($balanceQty, 2, '.', ''); ?>" id="balanceProgramQnty">
                            <input type="text" name="txt_program_qnty" id="txt_program_qnty" value="<? echo number_format($balanceQty, 2, '.', '');?>" class="text_boxes_numeric" style="width:165px;" readonly/>
                        </td>
                    </tr>
                    <tr>
                        <td>Program Date</td>
                        <td>
                            <input type="text" name="txt_program_date" id="txt_program_date" class="datepicker"
                            style="width:140px" value="<? echo $current_date; ?>" readonly>
                        </td>
                        <td class="must_entry_caption">Stitch Length</td>
                        <td>
                            <input type="text" name="txt_stitch_length" id="txt_stitch_length" class="text_boxes"
                            style="width:140px;" value="<? echo $dataArray[0][csf('stitch_length')]; ?>" />
                        </td>
                        <td>Spandex Stitch Length</td>
                        <td>
                            <input type="text" name="txt_spandex_stitch_length" id="txt_spandex_stitch_length"
                            class="text_boxes" style="width:165px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Draft Ratio</td>
                        <td>
                            <input type="text" name="txt_draft_ratio" id="txt_draft_ratio" class="text_boxes_numeric"
                            style="width:140px;"/>
                        </td>
                        <td>Machine No</td>
                        <td>
                            <input type="text" name="txt_machine_no" id="txt_machine_no" class="text_boxes"
                            placeholder="Double Click For Search" style="width:140px;"
                            onDblClick="openpage_machine();" disabled="disabled" readonly/>
                            <input type="hidden" name="machine_id" id="machine_id" class="text_boxes" readonly/>
                        </td>
                        <td>Machine Capacity</td>
                        <td>
                            <input type="text" name="txt_machine_capacity" id="txt_machine_capacity"
                            placeholder="Display" class="text_boxes_numeric" style="width:165px;"
                            disabled="disabled"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Distribution Qnty</td>
                        <td>
                            <input type="text" name="txt_distribution_qnty" id="txt_distribution_qnty"
                            placeholder="Display" class="text_boxes_numeric" style="width:65px;"
                            disabled="disabled"/>
                            <input type="text" name="txt_days_req" id="txt_days_req" placeholder="Days Req."
                            class="text_boxes_numeric" style="width:60px;" disabled="disabled"/>
                        </td>
                        <td>Start Date</td>
                        <td>
                            <input type="text" name="txt_start_date" id="txt_start_date" class="datepicker"
                            style="width:140px" value="<? echo $start_date; ?>" readonly>
                        </td>
                        <td>End Date & Batch No</td>
                        <td >
                            <input type="text" name="txt_end_date" id="txt_end_date" class="datepicker"
                            style="width:75px" value="<? echo $end_date; ?>" readonly>
                            <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:75px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>
                            <?
                            echo create_drop_down("cbo_knitting_status", 152, $knitting_program_status, "", 1, "--Select Status--", 0, "");
                            ?>
                        </td>

                        <td>Feeder</td>
                        <td>
                            <?
                            $feeder = array(1 => "Full Feeder", 2 => "Half Feeder");
                            echo create_drop_down("cbo_feeder", 152, $feeder, "", 1, "--Select Feeder--", 0, "");
                            ?>
                        </td>
                        <td colspan="2">
                            <input type="button" name="feeder" class="formbuttonplasminus" value="No Of Feeder"
                            onClick="openpage_feeder();" style="width:100px"/>
                            <input type="hidden" name="hidden_no_of_feeder_data" id="hidden_no_of_feeder_data"
                            class="text_boxes"><b>  &emsp; &nbsp; &nbsp;Program No.</b>
                            <input type="text" name="txt_program_no" id="txt_program_no" class="text_boxes"
                            placeholder="Display" disabled style="width:100px">
                        </td>
                    </tr>
                    <tr>
                        <td>Remarks</td>
                        <td>
                            <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes"
                            style="width:140px">
                        </td>
                        <td class="must_entry_caption" id="location_caption">Location</td>
                        <td id="location_td">
                            <?
                            echo create_drop_down("cbo_location_name", 152, $blank_array, 1, "-- Select Location --", 0, "");
                            ?>
                        </td>
                        <td>
                            <input type="button" name="feeder" class="formbuttonplasminus" value="Advice"
                            onClick="openpage_advice();" style="width:100px"/>
                            <input type="hidden" name="hidden_advice_data" id="hidden_advice_data" class="text_boxes">

                        </td>

                        <td colspan="2">
                            <span style="padding-right: 5px;"><b>Knitting Coefficient</b></span>
                            <input type="text" name="txt_co_efficient" id="txt_co_efficient" class="text_boxes_numeric"style="width:58px" >
                        </td>

                    </tr>
                    <tr>
                        <td>Grey fabric Dia</td>
                        <td>
                            <input type="text" name="txt_grey_dia" id="txt_grey_dia" class="text_boxes"
                            style="width:140px">
                        </td>
                        <td>Attention</td>
                        <td>
                            <input type="text" name="txt_attention" id="txt_attention" class="text_boxes"
                            style="width:140px">
                        </td>

                        <td>
                            <input type="button" name="feeder" class="formbuttonplasminus" value="Count Feeding"
                            onClick="openpage_count_feeding();" style="width:100px"/>
                            <input type="hidden" name="hidden_count_feeding_data" id="hidden_count_feeding_data"
                            class="text_boxes">
                        </td>

                        <td>
                            <input type="button" name="feeder" class="formbuttonplasminus" value="Collar & Cuff"
                            onClick="openpage_collarCuff();" style="width:100px"/>
                            <input type="hidden" name="hidden_collarCuff_data" id="hidden_collarCuff_data"
                            class="text_boxes">
                            <input type="hidden" name="hidden_bodypartID_data" id="hidden_bodypartID_data"
                            class="text_boxes" value="<? echo $body_part_id; ?>">
							<input type="button" class="image_uploader" id="uploader" style="width:60px" value="Add Image" onClick="fnc_image_upload();">
							
                        </td>

                    </tr>
					<tr>
                        <td>Fabric Type</td>
                        <td>
                            <input type="text" name="txt_fabric_type" id="txt_fabric_type" class="text_boxes"
                            style="width:140px">
                        </td>
					</tr>
                    <tr>
                        <td colspan="4" align="right" class="button_container">
                            <?
                            echo load_submit_buttons($permission, "fnc_program_entry", 0, 0, "reset_form('programQnty_1','','','txt_start_date,$start_date*txt_end_date,$end_date*txt_program_date,$current_date','','updateId*txt_color');", 1);
                            ?>
                        </td>
                        <td colspan="2" align="left" valign="top" class="button_container">
                            <input type="button" name="close" class="formbutton" value="Close" id="main_close"
                            onClick="parent.emailwindow.hide();" style="width:100px;"/>
                            <input type="hidden" name="save_data" id="save_data" class="text_boxes">
                            <input type="hidden" name="allowed_date_qnty_string" id="allowed_date_qnty_string" class="text_boxes">
                            <input type="hidden" name="updateId" id="updateId" class="text_boxes"
                            value="<? echo str_replace("'", '', $plan_id); ?>">
                            <input type="hidden" name="update_dtls_id" id="update_dtls_id" class="text_boxes">
                        </td>
                    </tr>
                </table>
            </fieldset>
            <div id="list_view" style="margin-top:5px">
            <?
            if (str_replace("'", '', $plan_id) != "")
            {
                ?>
                <script>
                    show_list_view('<? echo str_replace("'", '', $plan_id)."_".trim($dia); ?>', 'planning_info_details', 'list_view', 'planning_info_entry_for_sample_without_order_controller', '');
                </script>
                <?
            }
            ?>
            </div>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| save_update_delete
|--------------------------------------------------------------------------
|
*/
if ($action == "save_update_delete")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	//for color wise program qty distribution
	$colorPopupDataArr = array();
	$hdnColorWiseProgramData = str_replace("'", "", $hidden_color_wise_prog_data);
	if ($hdnColorWiseProgramData != "")
	{
		$expColorWiseProgramData = explode(",", $hdnColorWiseProgramData);
		for ($i = 0; $i < count($expColorWiseProgramData); $i++)
		{
			$colorWiseProgramData = explode("_", $expColorWiseProgramData[$i]);
			$colorPopupDataArr[$colorWiseProgramData[0]]['program_qty'] = $colorWiseProgramData[1];
			$colorPopupDataArr[$colorWiseProgramData[0]]['booking_qty'] = $colorWiseProgramData[3];
		}
	}
	//echo "<pre>";
	//print_r($colorPopupDataArr); die;

	/*
	|--------------------------------------------------------------------------
	| Insert
	|--------------------------------------------------------------------------
	|
	*/
	if ($operation == 0)
	{
		$con = connect();
		/*
		if ($db_type == 0)
		{
			mysql_query("BEGIN");
			$start_date = change_date_format(str_replace("'", "", trim($start_date)), "yyyy-mm-dd", "");
			$end_date = change_date_format(str_replace("'", "", trim($end_date)), "yyyy-mm-dd", "");
		}
		else
		{
			$start_date = change_date_format(str_replace("'", "", trim($start_date)), '', '', 1);
			$end_date = change_date_format(str_replace("'", "", trim($end_date)), '', '', 1);
		}
		*/

		if (str_replace("'", '', $updateId) != "")
		{
			if($po_id!="")
			{
				$total_program_qnty = return_field_value("sum(program_qnty) program_qnty", "ppl_planning_info_entry_dtls", "mst_id=".$updateId." and status_active=1 and is_deleted=0 and po_id in(".$upd_plan_po_ids.")", "program_qnty");
			}

			$program_balance = $total_program_qnty + str_replace("'", "", $txt_program_qnty)*1;
			$hdn_booking_qnty = number_format($hdn_booking_qnty,2,'.','');
			$program_balance = number_format($program_balance,2,'.','');

			if ( $program_balance  > $hdn_booking_qnty )
			{
				echo "14**Program quantity can not be greater than Booking quantity=".$program_balance.'=='.$hdn_booking_qnty."=".$txt_program_qnty;
				disconnect($con);
				exit();
			}

		}
		else
		{

			if (str_replace("'", "", $txt_program_qnty) > $hdn_booking_qnty)
			{
				echo "14**Program quantity can not be greater than Booking quantity*".$program_balance.'=='.$hdn_booking_qnty."=".$txt_program_qnty;
				disconnect($con);
				exit();
			}
		}

		$data_array = sql_select("select machine_id, distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where distribution_date >= '$start_date' group by machine_id, distribution_date order by distribution_date");
		foreach ($data_array as $row)
		{
			$distribution_date = date('d-m-Y',strtotime($row[csf('distribution_date')]));
			$previous_occupied_arr[$row[csf('machine_id')]][$distribution_date]["days_complete"] = $row[csf('days_complete')];
			$previous_occupied_arr[$row[csf('machine_id')]][$distribution_date]["distribution_date"] = $distribution_date;
		}

		$booking_no=str_replace("'","",$booking_no);
		$knit_qty = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_no='$booking_no' and b.status_active=1 and b.is_deleted=0", "knitting_qnty");
		if ($knit_qty > str_replace("'", "", $txt_program_qnty))
		{
			echo "14**Program Qty. Can not Be Less Than Knitting Qty.";
			disconnect($con);
			exit();
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_info_entry_mst
		| data preparing for
		| $data_array
		|--------------------------------------------------------------------------
		|
		*/
		$id = '';
		if (str_replace("'", '', $updateId) == "")
		{
			$id = return_next_id("id", "ppl_planning_info_entry_mst", 1);
			$field_array = "id,company_id,buyer_id,booking_no,body_part_id,color_type_id,determination_id,fabric_desc,gsm_weight,dia,width_dia_type,is_sales,inserted_by,insert_date";
			$data_array = "(" . $id . "," . $companyID . "," . $buyer_id . ",'" . $booking_no . "'," . $body_part_id . "," . $color_type_id . "," . $determination_id . ",'" . $desc . "','" . $gsm . "','" . $dia . "'," . $fabric_typee . ",2," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
		}
		else
		{
			$id = str_replace("'", '', $updateId);
		}


		/*
		|--------------------------------------------------------------------------
		| ppl_planning_info_entry_dtls
		| data preparing for
		| $data_array_dtls
		|--------------------------------------------------------------------------
		|
		*/
		$dtls_id = return_next_id("id", "ppl_planning_info_entry_dtls", 1);
		$field_array_dtls = "id,mst_id,knitting_source,knitting_party,subcontract_party,color_id,color_range,machine_dia,width_dia_type,machine_gg,fabric_dia,program_qnty,stitch_length,spandex_stitch_length,draft_ratio,machine_id,machine_capacity,distribution_qnty,status,start_date,end_date,program_date,feeder,remarks,save_data,no_fo_feeder_data,collar_cuff_data,location_id,advice,grey_dia,attention,fabric_type,co_efficient,batch_no,is_sales,inserted_by,insert_date";

		$data_array_dtls = "(" . $dtls_id . "," . $id . "," . $cbo_knitting_source . "," . $cbo_knitting_party . "," . $cbo_subcontract_party . "," . $hidden_color_id . "," . $cbo_color_range . "," . $txt_machine_dia . "," . $cbo_dia_width_type . "," . $txt_machine_gg . "," . $txt_fabric_dia . "," . $txt_program_qnty . "," . $txt_stitch_length . "," . $txt_spandex_stitch_length . "," . $txt_draft_ratio . "," . $machine_id . "," . $txt_machine_capacity . "," . $txt_distribution_qnty . "," . $cbo_knitting_status . "," . $txt_start_date . "," . $txt_end_date . "," . $txt_program_date . "," . $cbo_feeder . "," . $txt_remarks . "," . $save_data . "," . $hidden_no_of_feeder_data . "," . $hidden_bodypartID_data . "," . $cbo_location_name . "," . $hidden_advice_data . "," . $txt_grey_dia . "," . $txt_attention . "," . $txt_fabric_type . ",". $txt_co_efficient . ",". $txt_batch_no . ",2," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_entry_plan_dtls
		| data preparing for
		| $data_array_plan_dtls
		|--------------------------------------------------------------------------
		|
		*/
		$plan_dtls_id = return_next_id("id", "ppl_planning_entry_plan_dtls", 1);
		$field_array_plan_dtls = "id,mst_id,dtls_id,company_id,buyer_id,booking_no,po_id,start_date,finish_date,body_part_id,color_type_id,determination_id,fabric_desc,gsm_weight,dia,width_dia_type,color_id,program_qnty,is_sales,inserted_by,insert_date";

		$data = str_replace("'", "", $dataPre);
		if ($data != '')
		{
			$data = explode("_", $data);
			for ($i = 0; $i < count($data); $i++)
			{
				$plan_data = explode("**", $data[$i]);
				$start_date = '';
				$end_date = '';
				$po_id = 0;
				$buyer_id = $plan_data[0];
				$booking_no = $plan_data[1];
				$determination_id = $plan_data[2];
				$desc = $plan_data[3];
				$gsm = $plan_data[4];
				$dia = $plan_data[5];
				$dia_width_type = $plan_data[6];
				$booking_qnty = $plan_data[7];
				//$gsm_weight = $plan_data[8];
				//$dia_width = $plan_data[9];
				//$gsm_weight = $plan_data[10];
				//$dia_width = $plan_data[11];
				$color_id = $plan_data[12];
				$color_type_id = $plan_data[13];
				$body_part_id = $plan_data[14];

				/*if ($db_type == 0)
				{
					$start_date = change_date_format($start_date, "yyyy-mm-dd", "-");
					$end_date = change_date_format($end_date, "yyyy-mm-dd", "-");
				}
				else
				{
					$start_date = change_date_format($start_date, '', '', 1);
					$end_date = change_date_format($end_date, '', '', 1);
				}*/

				//$perc = ($booking_qnty / $tot_booking_qnty) * 100;
				//$prog_qnty = ($perc * str_replace("'", '', $txt_program_qnty)) / 100;

				//for color wise program qty distribution
				$prog_qnty = 0;
				$colorBookingQty = 0;
				$colorPopupBookingQty = 0;
				$colorPopupProgramQty = 0;
				$expColorBookingData = explode(",", $plan_data[15]);
				for ($z = 0; $z < count($expColorBookingData); $z++)
				{
					$colorBookingData = explode("-", $expColorBookingData[$z]);
					$colorBookingQty = $colorBookingData[1]*1;
					$colorPopupBookingQty = $colorPopupDataArr[$colorBookingData[0]]['booking_qty']*1;
					$colorPopupProgramQty = $colorPopupDataArr[$colorBookingData[0]]['program_qty']*1;
					if($colorPopupBookingQty !=0)
					{
						$prog_qnty += ($colorPopupProgramQty*$colorBookingQty)/$colorPopupBookingQty;
					}
					//echo "14**".$colorPopupProgramQty."=".$colorBookingQty."=".$colorPopupBookingQty."=".$prog_qnty."=";
				}

				if ($data_array_plan_dtls != "")
					$data_array_plan_dtls .= ",";

				$data_array_plan_dtls .= "(" . $plan_dtls_id . "," . $id . "," . $dtls_id . "," . $companyID . "," . $buyer_id . ",'" . $booking_no . "'," . $po_id . ",'" . $start_date . "','" . $end_date . "'," . $body_part_id . "," . $color_type_id . "," . $determination_id . ",'" . $desc . "','" . $gsm . "','" . $dia . "'," . $dia_width_type . ",0," . $prog_qnty . ",2," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$plan_dtls_id = $plan_dtls_id + 1;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_info_machine_dtls
		| data preparing for
		| $data_array_machine_dtls
		|--------------------------------------------------------------------------
		|
		*/
		$machine_dtls_id = return_next_id("id", "ppl_planning_info_machine_dtls", 1);
		$field_array_machine_dtls = "id,mst_id,dtls_id,machine_id,dia,capacity,distribution_qnty,no_of_days,start_date,end_date,date_wise_breakdown,inserted_by,insert_date";

		/*
		|--------------------------------------------------------------------------
		| ppl_entry_machine_datewise
		| data preparing for
		| $data_array_machine_dtls_datewise
		|--------------------------------------------------------------------------
		|
		*/
		$machine_dtls_datewise_id = return_next_id("id", "ppl_entry_machine_datewise", 1);
		$field_array_machine_dtls_datewise = "id,mst_id,dtls_id,machine_id,distribution_date,fraction_date,days_complete,qnty,inserted_by,insert_date";

		$allowed_date_qnty_string = str_replace("'", "", $allowed_date_qnty_string);
		if($allowed_date_qnty_string != "")
		{
			$allowed_date_qnty_string = explode("__", $allowed_date_qnty_string);
			for ($i = 0; $i < count($allowed_date_qnty_string); $i++)
			{
				$machine_date_qnty_data = explode("=", $allowed_date_qnty_string[$i]);
				$machine_no = $machine_date_qnty_data[0];
				$date_qnty_data_str = $machine_date_qnty_data[1];
				$date_wise_machine_ref_data[$machine_no] = $date_qnty_data_str;
			}
		}

		$data_array_machine_dtls_datewise ="";
		$save_data = str_replace("'", "", $save_data);
		if ($save_data != "")
		{
			$save_data = explode(",", $save_data);
			for ($i = 0; $i < count($save_data); $i++)
			{
				$machine_wise_data = explode("_", $save_data[$i]);
				$machine_id = $machine_wise_data[0];
				$dia = $machine_wise_data[1];
				$capacity = $machine_wise_data[2];
				$qnty = $machine_wise_data[3];
				$noOfDays = $machine_wise_data[4];

				$dateWise_qnty = 0;
				$bl_qnty = $qnty;

				if ($machine_wise_data[5] != "")
					$startDate = date("Y-m-d", strtotime($machine_wise_data[5]));
				if ($machine_wise_data[6] != "")
					$endDate = date("Y-m-d", strtotime($machine_wise_data[6]));

				if ($startDate != "" && $endDate != "")
				{
					$date_wise_breakdown_str="";
					if($date_wise_machine_ref_data[$machine_id] != "")
					{
						$date_wise_breakdown_str = $date_wise_machine_ref_data[$machine_id];
						$date_wise_breakdown_arr =  explode("*", $date_wise_breakdown_str);
						for ($j = 0; $j < count($date_wise_breakdown_arr); $j++)
						{
							$machine_date_data = explode(",", $date_wise_breakdown_arr[$j]);

							$curr_date = $machine_date_data[0];
							$dateWise_qnty = $machine_date_data[1];
							$days_complete = $machine_date_data[2];

							if($days_complete >= 1)
							{
								$fraction = 0;
							}
							else
							{
								$fraction = 1;
							}

							$previous_occupied_days = $previous_occupied_arr[$machine_id][date("d-m-Y",strtotime($curr_date))]["days_complete"];
							if($previous_occupied_days)
							{
								if($days_complete != (1-$previous_occupied_days))
								{
									echo "14** Date : ".$curr_date .", Machine No: ".$machine_arr[$machine_id] ." already occupied other program $days_complete != (1-$previous_occupied_days) -- $machine_id == $curr_date";
									disconnect($con);
									die;
								}
							}

							if ($db_type == 0)
								$curr_date = change_date_format($curr_date, "yyyy-mm-dd", "-");
							else
								$curr_date = change_date_format($curr_date, '', '', 1);

							if ($data_array_machine_dtls_datewise != "")
								$data_array_machine_dtls_datewise .= ",";
							$data_array_machine_dtls_datewise .= "(" . $machine_dtls_datewise_id . "," . $id . "," . $dtls_id . ",'" . $machine_id . "','" . $curr_date . "','" . $fraction . "','" . $days_complete . "','" . $dateWise_qnty . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
							$machine_dtls_datewise_id = $machine_dtls_datewise_id + 1;
						}
					}
				}


				if ($db_type == 0)
				{
					$mstartDate = $startDate;
					$mendDate = $endDate;
				}
				else
				{
					$mstartDate = change_date_format($startDate, '', '', 1);
					$mendDate = change_date_format($endDate, '', '', 1);
				}

				if ($data_array_machine_dtls != "")
					$data_array_machine_dtls .= ",";
				$data_array_machine_dtls .= "(" . $machine_dtls_id . "," . $id . "," . $dtls_id . ",'" . $machine_id . "','" . $dia . "','" . $capacity . "','" . $qnty . "','" . $noOfDays . "','" . $mstartDate . "','" . $mendDate ."','". $date_wise_breakdown_str. "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$machine_dtls_id = $machine_dtls_id + 1;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_feeder_dtls
		| data preparing for
		| $data_array_feeder_dtls
		|--------------------------------------------------------------------------
		|
		*/
		$feeder_dtls_id = return_next_id("id", "ppl_planning_feeder_dtls", 1);
		$field_array_feeder_dtls = "id,mst_id,dtls_id,pre_cost_id,color_id,stripe_color_id,no_of_feeder,inserted_by,insert_date";
		$hidden_no_of_feeder_data = str_replace("'", "", $hidden_no_of_feeder_data);
		if ($hidden_no_of_feeder_data != "")
		{
			$hidden_no_of_feeder_data = explode(",", $hidden_no_of_feeder_data);
			for ($i = 0; $i < count($hidden_no_of_feeder_data); $i++)
			{
				$color_wise_data = explode("_", $hidden_no_of_feeder_data[$i]);
				$pre_cost_id = $color_wise_data[0];
				$color_id = $color_wise_data[1];
				$stripe_color_id = $color_wise_data[2];
				$no_of_feeder = $color_wise_data[3];

				if ($data_array_feeder_dtls != "")
					$data_array_feeder_dtls .= ",";

				$data_array_feeder_dtls .= "(" . $feeder_dtls_id . "," . $id . "," . $dtls_id . ",'" . $pre_cost_id . "','" . $color_id . "','" . $stripe_color_id . "','" . $no_of_feeder . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$feeder_dtls_id = $feeder_dtls_id + 1;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_collar_cuff_dtls
		| data preparing for
		| $data_array_collar_cuff_dtls
		|--------------------------------------------------------------------------
		|
		*/
		$hidden_collarCuff_data = str_replace("'", "", $hidden_collarCuff_data);
		if ($hidden_collarCuff_data != "")
		{
			$collar_cuff_dtls_id = return_next_id("id", "ppl_planning_collar_cuff_dtls", 1);
			$field_array_collar_cuff_dtls = "id,mst_id,dtls_id,body_part_id,grey_size,finish_size,qty_pcs,inserted_by,insert_date";
			$hidden_collarCuff_data = explode(",", $hidden_collarCuff_data);
			for ($i = 0; $i < count($hidden_collarCuff_data); $i++)
			{
				$collarCuff_wise_data = explode("_", $hidden_collarCuff_data[$i]);
				$body_part_id = $collarCuff_wise_data[0];
				$grey_size = $collarCuff_wise_data[1];
				$finish_size = $collarCuff_wise_data[2];
				$qty_pcs = $collarCuff_wise_data[3];

				if ($data_array_collar_cuff_dtls != "")
					$data_array_collar_cuff_dtls .= ",";

				$data_array_collar_cuff_dtls .= "(" . $collar_cuff_dtls_id . "," . $id . "," . $dtls_id . ",'" . $body_part_id . "','" . $grey_size . "','" . $finish_size . "','" . $qty_pcs . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$collar_cuff_dtls_id = $collar_cuff_dtls_id + 1;
			}
		}


		/*
		|--------------------------------------------------------------------------
		| ppl_color_wise_break_down
		| data preparing for
		| $data_array_color_wise_break_down
		|--------------------------------------------------------------------------
		|
		*/
		$hidden_color_wise_prog_data = str_replace("'", "", $hidden_color_wise_prog_data);
		if( $hidden_color_wise_prog_data != "" )
		{
			$color_wise_break_down_id = return_next_id("id", "ppl_color_wise_break_down", 1);
			$field_array_color_wise_break_down = "id,plan_id,program_no,color_id,color_prog_qty,inserted_by,insert_date";
			$color_wise_prog_data = explode(",", $hidden_color_wise_prog_data);
			for ($i = 0; $i < count($color_wise_prog_data); $i++)
			{
				$colorWiseProgData = explode("_", $color_wise_prog_data[$i]);
				$color_id = $colorWiseProgData[0];
				$color_prog_qty = $colorWiseProgData[1];

				if($color_prog_qty>0)
				{
					if ($data_array_color_wise_break_down != "")
						$data_array_color_wise_break_down .= ",";

					$data_array_color_wise_break_down .= "(" . $color_wise_break_down_id . "," . $id . "," . $dtls_id . ",'" . $color_id . "','" . $color_prog_qty . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$color_wise_break_down_id = $color_wise_break_down_id + 1;
				}
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_info_entry_mst
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if (str_replace("'", '', $updateId) == "")
		{

			//echo "10**INSERT INTO ppl_planning_info_entry_mst (".$field_array.") VALUES".$data_array; die;
			$rsltPlanningMst = sql_insert("ppl_planning_info_entry_mst", $field_array, $data_array, 0);
			if($rsltPlanningMst)
				$flag = 1;
			else
				$flag = 0;
		}
		else
		{
			$flag = 1;
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_info_entry_dtls
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1)
		{
			//echo "10**INSERT INTO ppl_planning_info_entry_dtls (".$field_array_dtls.") VALUES".$data_array_dtls;
			$rsltPlanningDtls = sql_insert("ppl_planning_info_entry_dtls", $field_array_dtls, $data_array_dtls, 1);
			if ($rsltPlanningDtls)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_entry_plan_dtls
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1)
		{
			if ($data_array_plan_dtls != "")
			{
				//echo "10**INSERT INTO ppl_planning_entry_plan_dtls (".$field_array_plan_dtls.") VALUES".$data_array_plan_dtls;
				$rsltPlanningPlanDtls = sql_insert("ppl_planning_entry_plan_dtls", $field_array_plan_dtls, $data_array_plan_dtls, 0);
				if ($rsltPlanningPlanDtls)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_info_machine_dtls
		| ppl_entry_machine_datewise
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if ($save_data != "")
		{
			if ($data_array_machine_dtls != "")
			{
				if ($flag == 1)
				{
					//echo "10**INSERT INTO ppl_planning_info_machine_dtls (".$field_array_machine_dtls.") VALUES ".$data_array_machine_dtls."";die;
					$rsltMachineDtls = sql_insert("ppl_planning_info_machine_dtls", $field_array_machine_dtls, $data_array_machine_dtls, 0);
					if ($rsltMachineDtls)
						$flag = 1;
					else
						$flag = 0;
				}
			}

			if ($data_array_machine_dtls_datewise != "")
			{
				if ($flag == 1)
				{
					//echo "10**INSERT INTO ppl_entry_machine_datewise (".$field_array_machine_dtls_datewise.") VALUES ".$data_array_machine_dtls_datewise."";die;
					$rsltMachineDatewise = sql_insert("ppl_entry_machine_datewise", $field_array_machine_dtls_datewise, $data_array_machine_dtls_datewise, 0);
					if ($rsltMachineDatewise)
						$flag = 1;
					else
						$flag = 0;
				}
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_feeder_dtls
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if ($hidden_no_of_feeder_data != "")
		{
			if ($data_array_feeder_dtls != "")
			{
				if ($flag == 1)
				{
					//echo "10**INSERT INTO ppl_planning_feeder_dtls (".$field_array_feeder_dtls.") VALUES ".$data_array_feeder_dtls.""; die;
					$rsltFeeder = sql_insert("ppl_planning_feeder_dtls", $field_array_feeder_dtls, $data_array_feeder_dtls, 0);
					if ($rsltFeeder)
						$flag = 1;
					else
						$flag = 0;
				}
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_collar_cuff_dtls
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if ($data_array_collar_cuff_dtls != "")
		{
			if ($flag == 1)
			{
				//echo "10**INSERT INTO ppl_planning_collar_cuff_dtls (".$field_array_collar_cuff_dtls.") VALUES ".$data_array_collar_cuff_dtls."";die;
				$rsltCollarCuff = sql_insert("ppl_planning_collar_cuff_dtls", $field_array_collar_cuff_dtls, $data_array_collar_cuff_dtls, 0);
				if ($rsltCollarCuff)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_color_wise_break_down
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if ($data_array_color_wise_break_down != "")
		{
			//echo "10**INSERT INTO ppl_color_wise_break_down (".$field_array_collar_cuff_dtls.") VALUES ".$field_array_color_wise_break_down."";die;
			$rsltColorWise = sql_insert("ppl_color_wise_break_down", $field_array_color_wise_break_down, $data_array_color_wise_break_down, 0);
			if ($flag == 1)
			{
				if ($rsltColorWise)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if ($db_type == 0)
		{
			if ($flag == 1)
			{
				mysql_query("COMMIT");
				echo "0**" . $id . "**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ORACLE Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		else if ($db_type == 2 || $db_type == 1)
		{
			if ($flag == 1)
			{
				oci_commit($con);
				echo "0**" . $id . "**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}

	/*
	|--------------------------------------------------------------------------
	| Update
	|--------------------------------------------------------------------------
	|
	*/
	else if ($operation == 1)
	{
		$con = connect();
		if ($db_type == 0)
		{
			mysql_query("BEGIN");
		}

		/*
		|--------------------------------------------------------------------------
		| program qty. is checking
		|--------------------------------------------------------------------------
		|
		*/
		$programQnty = str_replace("'", "", $txt_program_qnty)*1;
		$preProgramQnty = str_replace("'", "", $hiddenProgramQnty)*1;
		$balanceQnty = str_replace("'", "", $balanceProgramQnty)*1;

		if(($programQnty - $preProgramQnty) > $balanceQnty )
		{
			echo "14**Program qty. can not be greater than booking balance qty.";
			disconnect($con);
			exit();
		}

		/*
		|--------------------------------------------------------------------------
		| Knitting qty. is checking
		|--------------------------------------------------------------------------
		|
		*/
		$knit_qty = (return_field_value("SUM(b.grey_receive_qnty) AS knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id = b.mst_id AND a.item_category = 13 AND a.entry_form = 2 AND a.receive_basis = 2 AND a.booking_id = ".$update_dtls_id." AND b.status_active = 1 AND b.is_deleted = 0", "knitting_qnty")*1);
		if ($knit_qty > str_replace("'", "", $txt_program_qnty))
		{
			echo "14**Program Qty. Can not Be Less Than Knitting Qty.";
			disconnect($con);
			exit();
		}

		/*
		|--------------------------------------------------------------------------
		| production is checking
		|--------------------------------------------------------------------------
		|
		*/
		$sql = sql_select("SELECT id, recv_number FROM inv_receive_master WHERE entry_form = 2 AND receive_basis = 2 AND booking_no = ".$update_dtls_id." AND status_active = 1 AND is_deleted = 0");
		$productData = array();
		$productionID = array();
		foreach($sql as $prow)
		{
			$productionID[$prow[csf('id')]] = $prow[csf('id')];
			$productData[$prow[csf('id')]] = $prow[csf('recv_number')];
		}

		$productionIds  = implode(',', $productionID);
		if($productionIds != "")
		{
			$grey_recv_arr = return_library_array("SELECT mst_id, SUM(grey_receive_qnty) AS recv FROM pro_grey_prod_entry_dtls WHERE status_active = 1 AND is_deleted = 0 AND mst_id IN(".$productionIds.") GROUP BY mst_id", 'mst_id', 'recv');
		}

		if(!empty($productData))
		{
			$sqlRslt = sql_select("SELECT machine_dia, fabric_dia FROM ppl_planning_info_entry_dtls WHERE status_active = 1 AND is_deleted = 0 AND id = ".$update_dtls_id."");
			$existingMachineDia = 0;
			$existingFinishDia = 0;
			foreach($sqlRslt as $row)
			{
				$existingMachineDia = $row[csf('machine_dia')];
				$existingFinishDia = $row[csf('fabric_dia')];
			}

			if($existingMachineDia != str_replace("'","",$txt_machine_dia))
			{
				foreach ($productData as $proid => $proid_no)
				{
					echo "14**You can not change machine dia, cause production had been made allready. production id is ".$proid_no." and quantity is ".$grey_recv_arr[$proid];
					disconnect($con);

				}
				exit();
			}

			if($existingFinishDia != str_replace("'","",$txt_fabric_dia))
			{
				foreach ($productData as $proid => $proid_no)
				{
					echo "14**You can not change Finish Fabric Dia, cause production had been made allready. production id is ".$proid_no." and quantity is ".$grey_recv_arr[$proid];
					disconnect($con);
				}
				exit();
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_entry_machine_datewise information
		|--------------------------------------------------------------------------
		|
		*/
		$allowed_date_qnty_string = str_replace("'", "", $allowed_date_qnty_string);
		if($allowed_date_qnty_string != "")
		{
			$allowed_date_qnty_string = explode("__", $allowed_date_qnty_string);
			for ($i = 0; $i < count($allowed_date_qnty_string); $i++)
			{
				$machine_date_qnty_data = explode("=", $allowed_date_qnty_string[$i]);
				$machine_no = $machine_date_qnty_data[0];
				$date_qnty_data_str = $machine_date_qnty_data[1];
				$date_wise_machine_ref_data[$machine_no] = $date_qnty_data_str;
			}
		}

		if ($db_type == 0)
		{
			$txt_start_date = change_date_format(str_replace("'", "", trim($txt_start_date)), "yyyy-mm-dd", "");
		}
		else
		{
			$txt_start_date = change_date_format(str_replace("'", "", trim($txt_start_date)), '', '', 1);
		}

		$data_array = sql_select("SELECT machine_id, distribution_date, SUM(days_complete) AS days_complete FROM ppl_entry_machine_datewise WHERE distribution_date >= '".$txt_start_date."' GROUP BY machine_id, distribution_date ORDER BY distribution_date");
		foreach ($data_array as $row)
		{
			$distribution_date = date('d-m-Y',strtotime($row[csf('distribution_date')]));
			$previous_occupied_arr[$row[csf('machine_id')]][$distribution_date]["days_complete"] = $row[csf('days_complete')];
			$previous_occupied_arr[$row[csf('machine_id')]][$distribution_date]["distribution_date"] = $distribution_date;
		}

		if ($knit_qty > 0)
		{
			/*
			|--------------------------------------------------------------------------
			| ppl_planning_info_entry_dtls
			| data preparing for
			| $data_array_update
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_update = "machine_dia*width_dia_type*color_range*machine_gg*stitch_length*spandex_stitch_length*draft_ratio*program_qnty*machine_id*machine_capacity*distribution_qnty*status*start_date*end_date*remarks*attention*fabric_type*co_efficient*batch_no*save_data*advice*updated_by*update_date";
			$data_array_update = $txt_machine_dia . "*" . $cbo_dia_width_type . "*" . $cbo_color_range . "*" . $txt_machine_gg . "*" . $txt_stitch_length . "*" . $txt_spandex_stitch_length . "*" . $txt_draft_ratio . "*" . $txt_program_qnty . "*" . $machine_id . "*" . $txt_machine_capacity . "*" . $txt_distribution_qnty . "*" . $cbo_knitting_status . "*'" . $txt_start_date . "'*" . $txt_end_date . "*" . $txt_remarks . "*" . $txt_attention . "*" . $txt_fabric_type . "*" . $txt_co_efficient . "*" . $txt_batch_no . "*" . $save_data . "*" .$hidden_advice_data . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_entry_plan_dtls
			| data preparing for
			| $data_array_plan_dtls
			|--------------------------------------------------------------------------
			|
			*/
			$plan_dtls_id = return_next_id("id", "ppl_planning_entry_plan_dtls", 1);
			$field_array_plan_dtls = "id,mst_id,dtls_id,company_id,buyer_id,booking_no,po_id,start_date,finish_date,body_part_id,color_type_id,determination_id,fabric_desc,gsm_weight,dia,width_dia_type,color_id,program_qnty,is_sales,inserted_by,insert_date";

			$data = str_replace("'", "", $dataPre);
			if ($data != '')
			{
				$data = explode("_", $data);
				for ($i = 0; $i < count($data); $i++)
				{
					$plan_data = explode("**", $data[$i]);
					$start_date = '';
					$end_date = '';
					$po_id = 0;
					$buyer_id = $plan_data[0];
					$booking_no = $plan_data[1];
					$determination_id = $plan_data[2];
					$desc = $plan_data[3];
					$gsm = $plan_data[4];
					$dia = $plan_data[5];
					$dia_width_type = $plan_data[6];
					$booking_qnty = $plan_data[7];
					//$gsm_weight = $plan_data[8];
					//$dia_width = $plan_data[9];
					//$gsm_weight = $plan_data[10];
					//$dia_width = $plan_data[11];
					$color_id = $plan_data[12];
					$color_type_id = $plan_data[13];
					$body_part_id = $plan_data[14];

					/*if ($db_type == 0)
					{
						$start_date = change_date_format($start_date, "yyyy-mm-dd", "-");
						$end_date = change_date_format($end_date, "yyyy-mm-dd", "-");
					}
					else
					{
						$start_date = change_date_format($start_date, '', '', 1);
						$end_date = change_date_format($end_date, '', '', 1);
					}*/

					//$perc = ($booking_qnty / $tot_booking_qnty) * 100;
					//$prog_qnty = ($perc * str_replace("'", '', $txt_program_qnty)) / 100;

					//for color wise program qty distribution
					$prog_qnty = 0;
					$colorBookingQty = 0;
					$colorPopupBookingQty = 0;
					$colorPopupProgramQty = 0;
					$expColorBookingData = explode(",", $plan_data[15]);
					for ($z = 0; $z < count($expColorBookingData); $z++)
					{
						$colorBookingData = explode("-", $expColorBookingData[$z]);
						$colorBookingQty = $colorBookingData[1]*1;
						$colorPopupBookingQty = $colorPopupDataArr[$colorBookingData[0]]['booking_qty']*1;
						$colorPopupProgramQty = $colorPopupDataArr[$colorBookingData[0]]['program_qty']*1;
						if($colorPopupBookingQty !=0)
						{
							$prog_qnty += ($colorPopupProgramQty*$colorBookingQty)/$colorPopupBookingQty;
						}
						//echo "14**".$colorPopupProgramQty."=".$colorBookingQty."=".$colorPopupBookingQty."=".$prog_qnty."=";
					}


					if ($data_array_plan_dtls != "")
						$data_array_plan_dtls .= ",";

					$data_array_plan_dtls .= "(" . $plan_dtls_id . "," . $updateId . "," . $update_dtls_id . "," . $companyID . "," . $buyer_id . ",'" . $booking_no . "'," . $po_id . ",'" . $start_date . "','" . $end_date . "'," . $body_part_id . "," . $color_type_id . "," . $determination_id . ",'" . $desc . "','" . $gsm . "','" . $dia . "'," . $dia_width_type . ",0," . $prog_qnty . ",2," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$plan_dtls_id = $plan_dtls_id + 1;
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_info_machine_dtls
			| data preparing for
			| $data_array_machine_dtls
			|--------------------------------------------------------------------------
			|
			*/
			$machine_dtls_id = return_next_id("id", "ppl_planning_info_machine_dtls", 1);
			$field_array_machine_dtls = "id,mst_id,dtls_id,machine_id,dia,capacity,distribution_qnty,no_of_days,start_date,end_date,date_wise_breakdown,inserted_by,insert_date";

			/*
			|--------------------------------------------------------------------------
			| ppl_entry_machine_datewise
			| data preparing for
			| $data_array_machine_dtls_datewise
			|--------------------------------------------------------------------------
			|
			*/
			$machine_dtls_datewise_id = return_next_id("id", "ppl_entry_machine_datewise", 1);
			$field_array_machine_dtls_datewise = "id,mst_id,dtls_id,machine_id,distribution_date,fraction_date,days_complete,qnty,inserted_by,insert_date";

			$save_data = str_replace("'", "", $save_data);
			if ($save_data != "")
			{
				$save_data = explode(",", $save_data);
				for ($i = 0; $i < count($save_data); $i++)
				{
					$machine_wise_data = explode("_", $save_data[$i]);
					$machine_id = $machine_wise_data[0];
					$dia = $machine_wise_data[1];
					$capacity = $machine_wise_data[2];
					$qnty = $machine_wise_data[3];
					$noOfDays = $machine_wise_data[4];

					$dateWise_qnty = 0;
					$bl_qnty = $qnty;

					if ($machine_wise_data[5] != "")
						$startDate = date("Y-m-d", strtotime($machine_wise_data[5]));
					if ($machine_wise_data[6] != "")
						$endDate = date("Y-m-d", strtotime($machine_wise_data[6]));

					$date_wise_breakdown_str="";
					if($date_wise_machine_ref_data[$machine_id] != "")
					{
						$date_wise_breakdown_str = $date_wise_machine_ref_data[$machine_id];
						$date_wise_breakdown_arr =  explode("*", $date_wise_breakdown_str);

						for ($j = 0; $j < count($date_wise_breakdown_arr); $j++)
						{
							$machine_date_data = explode(",", $date_wise_breakdown_arr[$j]);
							$curr_date = $machine_date_data[0];
							$dateWise_qnty = $machine_date_data[1];
							$days_complete = $machine_date_data[2];
							$days_complete = number_format($days_complete,2,".","");

							if($days_complete >= 1)
							{
								$fraction = 0;
							}
							else
							{
								$fraction = 1;
							}

							$previous_occupied_days = $previous_occupied_arr[$machine_id][date("d-m-Y",strtotime($curr_date))]["days_complete"];
							if($previous_occupied_days)
							{
								if($days_complete != (1-$previous_occupied_days))
								{
									echo "14** Date : ".$curr_date .", Machine No: ".$machine_arr[$machine_id] ." already occupied other program $days_complete != (1-$previous_occupied_days) -- $machine_id == $curr_date";
									disconnect($con);
									die;
								}
							}

							if ($db_type == 0)
								$curr_date = change_date_format($curr_date, "yyyy-mm-dd", "-");
							else
								$curr_date = change_date_format($curr_date, '', '', 1);

							if ($data_array_machine_dtls_datewise != "")
								$data_array_machine_dtls_datewise .= ",";
							$data_array_machine_dtls_datewise .= "(" . $machine_dtls_datewise_id . "," . $updateId . "," . $update_dtls_id . ",'" . $machine_id . "','" . $curr_date . "','" . $fraction . "','" . $days_complete . "','" . $dateWise_qnty . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
							$machine_dtls_datewise_id = $machine_dtls_datewise_id + 1;
						}
					}
					else
					{
						$distribute_qnty = $qnty;
						$capacity_qnty = $capacity;
						$no_of_days = $noOfDays;
						if ($db_type == 0)
						{
							$start_date = change_date_format(trim($startDate), "yyyy-mm-dd", "");
						}
						else
						{
							$start_date = change_date_format(trim($startDate), '', '', 1);
						}

						$pre_sql = "SELECT distribution_date, SUM(days_complete) AS days_complete FROM ppl_entry_machine_datewise WHERE machine_id='".$machine_id."' and distribution_date >= '".$start_date."' GROUP BY distribution_date ORDER BY distribution_date";
						$data_array = sql_select($pre_sql);
						if (count($data_array) > 0)
						{
							foreach ($data_array as $row)
							{
								$distribution_date = date('d-m-Y',strtotime($row[csf('distribution_date')]));
								$previous_occupied_arr_without_ref[$distribution_date]["days_complete"] = $row[csf('days_complete')];
								$previous_occupied_arr_without_ref[$distribution_date]["distribution_date"] = $distribution_date;
							}
						}

						$present_day = $start_date;
						$without_ref_dateWise_qnty = "";
						$end_date_ref = "";
						while($distribute_qnty > 0)
						{
							$present_day = date("d-m-Y",strtotime($present_day));
							if($previous_occupied_arr_without_ref[$present_day]["distribution_date"])
							{
								if($previous_occupied_arr_without_ref[$present_day]["distribution_date"] == $present_day)
								{
									if ($previous_occupied_arr_without_ref[$present_day]["days_complete"] >= 1)
									{
										$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
									}
									else
									{
										if($distribute_qnty > ($capacity_qnty * (1 - $previous_occupied_arr_without_ref[$present_day]["days_complete"])))
										{
											$dist_qnty = $capacity_qnty * (1 - $previous_occupied_arr_without_ref[$present_day]["days_complete"]);
											$without_ref_dateWise_qnty .= $present_day.",".$dist_qnty.",".($dist_qnty/$capacity_qnty)."*";
										}
										else
										{
											$without_ref_dateWise_qnty .= $present_day.",".$distribute_qnty.",".($distribute_qnty/$capacity_qnty)."*";
										}

										$distribute_qnty = $distribute_qnty - ($capacity_qnty * (1 - $previous_occupied_arr_without_ref[$present_day]["days_complete"]));
										$end_date_ref =  $present_day;
										$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
									}
								}
							}
							else
							{
								if($distribute_qnty > ($capacity_qnty * (1 - $previous_occupied_arr_without_ref[$present_day]["days_complete"])))
								{
									$without_ref_dateWise_qnty .= $present_day.",".$capacity_qnty.",".($capacity_qnty/$capacity_qnty)."*";
								}
								else
								{
									$without_ref_dateWise_qnty .= $present_day.",".$distribute_qnty.",".($distribute_qnty/$capacity_qnty)."*";
								}

								$end_date_ref =  $present_day;
								$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
								$distribute_qnty = $distribute_qnty - $capacity_qnty;
							}
						}

						$without_ref_dateWise_qnty = chop($without_ref_dateWise_qnty,"*");
						$without_ref_arr =  array_filter(explode("*", $without_ref_dateWise_qnty));
						foreach ($without_ref_arr as  $ref_value)
						{
							$ref_value = explode(",", $ref_value);
							$curr_date =  $ref_value[0];
							$dateWise_qnty =  $ref_value[1];
							$days_complete =  $ref_value[2];
							$days_complete = number_format($days_complete,2,".","");

							if($days_complete >= 1)
							{
								$fraction =0;
							}
							else
							{
								$fraction =1;
							}

							$previous_occupied_days = $previous_occupied_arr[$machine_id][date("d-m-Y",strtotime($curr_date))]["days_complete"];
							if($previous_occupied_days)
							{
								if($days_complete != (1-$previous_occupied_days))
								{
									echo "14** Date ffff: ".$curr_date .", Machine No: ".$machine_arr[$machine_id] ." already occupied other program $days_complete != (1-$previous_occupied_days) -- $machine_id == $curr_date"; disconnect($con); die;
									//14** Date : 11-03-2020, Machine No: 1 already occupied other program 0.03 != (1-.033333333333333) -- 93 == 11-03-2020
								}
							}

							if ($db_type == 0)
								$curr_date = change_date_format($curr_date, "yyyy-mm-dd", "-");
							else
								$curr_date = change_date_format($curr_date, '', '', 1);

							if ($data_array_machine_dtls_datewise != "")
								$data_array_machine_dtls_datewise .= ",";
							$data_array_machine_dtls_datewise .= "(" . $machine_dtls_datewise_id . "," . $updateId . "," . $update_dtls_id . ",'" . $machine_id . "','" . $curr_date . "','" . $fraction . "','" . $days_complete . "','" . $dateWise_qnty . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
							$machine_dtls_datewise_id = $machine_dtls_datewise_id + 1;
						}
					}

					if ($db_type == 0)
					{
						$mstartDate = $startDate;
						$mendDate = $endDate;
					}
					else
					{
						$mstartDate = change_date_format($startDate, '', '', 1);
						$mendDate = change_date_format($endDate, '', '', 1);
					}

					if ($data_array_machine_dtls != "")
						$data_array_machine_dtls .= ",";
					$data_array_machine_dtls .= "(" . $machine_dtls_id . "," . $updateId . "," . $update_dtls_id . "," . $machine_id . ",'" . $dia . "','" . $capacity . "','" . $qnty . "','" . $noOfDays . "','" . $mstartDate . "','" . $mendDate . "','" .$date_wise_breakdown_str. "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$machine_dtls_id = $machine_dtls_id + 1;
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_color_wise_break_down
			| data preparing for
			| $data_array_color_wise_prog_update
			| data_array_color_wise_break_down
			|--------------------------------------------------------------------------
			|
			*/
			$hidden_color_wise_prog_data = str_replace("'", "", $hidden_color_wise_prog_data);
			if ($hidden_color_wise_prog_data != "")
			{
				$color_wise_break_down_id = return_next_id("id", "ppl_color_wise_break_down", 1);
				$field_array_color_wise_break_down = "id, plan_id, program_no, color_id, color_prog_qty, inserted_by, insert_date";
				$field_array_color_wise_prog_update = "color_id*color_prog_qty*updated_by*update_date*status_active*is_deleted";
				$color_wise_prog_data = explode(",", $hidden_color_wise_prog_data);
				for ($i = 0; $i < count($color_wise_prog_data); $i++)
				{
					$colorWiseProgData = explode("_", $color_wise_prog_data[$i]);
					$color_id = $colorWiseProgData[0];
					$color_prog_qty = $colorWiseProgData[1];
					$color_wise_break_down_udpdateId = $colorWiseProgData[2];

					if ($color_wise_break_down_udpdateId>0)
					{
						$status_active = 1;
						$is_deleted = 0;
						if($color_prog_qty <= 0)
						{
							$status_active = 0;
							$is_deleted = 1;
						}

						$colorprog_upd_id_arr[] = $color_wise_break_down_udpdateId;
						$data_array_color_wise_prog_update[$color_wise_break_down_udpdateId] = explode("*", ("'" . $color_id . "'*'". $color_prog_qty . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'". $status_active . "'*'". $is_deleted . "'"));
					}
					else
					{
						if($color_prog_qty>0)
						{
							if ($data_array_color_wise_break_down != "")
								$data_array_color_wise_break_down .= ",";

							$data_array_color_wise_break_down .= "(" . $color_wise_break_down_id . "," . $updateId . "," . $update_dtls_id . ",'" . $color_id . "','" . $color_prog_qty . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
							$color_wise_break_down_id = $color_wise_break_down_id + 1;
						}
					}
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_entry_plan_dtls
			| data deleting
			|--------------------------------------------------------------------------
			|
			*/
			$delete = execute_query("DELETE FROM ppl_planning_entry_plan_dtls WHERE dtls_id = ".$update_dtls_id."", 0);
			if ($delete)
				$flag = 1;
			else
				$flag = 0;

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_entry_plan_dtls
			| data inserting
			|--------------------------------------------------------------------------
			|
			*/
			if ($data != "")
			{
				if ($data_array_plan_dtls != "")
				{
					if ($flag == 1)
					{
						//echo "10**insert into ppl_planning_entry_plan_dtls (".$field_array_plan_dtls.") Values ".$data_array_plan_dtls."";die;
						$rID2 = sql_insert("ppl_planning_entry_plan_dtls", $field_array_plan_dtls, $data_array_plan_dtls, 0);
						if ($rID2)
							$flag = 1;
						else
							$flag = 0;
					}
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_info_entry_dtls
			| data updating
			|--------------------------------------------------------------------------
			|
			*/
			$rID = sql_update("ppl_planning_info_entry_dtls", $field_array_update, $data_array_update, "id", $update_dtls_id, 0);

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_info_machine_dtls
			| data deleting
			|--------------------------------------------------------------------------
			|
			*/
			$deletem = execute_query("DELETE FROM ppl_planning_info_machine_dtls WHERE dtls_id = ".$update_dtls_id."", 0);

			/*
			|--------------------------------------------------------------------------
			| ppl_entry_machine_datewise
			| data deleting
			|--------------------------------------------------------------------------
			|
			*/
			$delete_datewise = execute_query("DELETE FROM ppl_entry_machine_datewise WHERE dtls_id = ".$update_dtls_id."", 0);

			$rID3 = true;
			$rID4 = true;

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_info_machine_dtls
			| ppl_entry_machine_datewise
			| data inserting
			|--------------------------------------------------------------------------
			|
			*/
			if ($save_data != "")
			{
				if ($data_array_machine_dtls != "")
				{
					//echo"insert into ppl_planning_info_machine_dtls (".$field_array_machine_dtls.") Values ".$data_array_machine_dtls.""; die;
					$rID3 = sql_insert("ppl_planning_info_machine_dtls", $field_array_machine_dtls, $data_array_machine_dtls, 0);
				}

				if ($data_array_machine_dtls_datewise != "")
				{
					//echo "10**insert into ppl_entry_machine_datewise (".$field_array_machine_dtls_datewise.") Values ".$data_array_machine_dtls_datewise.""; die;
					$rID4 = sql_insert("ppl_entry_machine_datewise", $field_array_machine_dtls_datewise, $data_array_machine_dtls_datewise, 0);
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_color_wise_break_down
			| data updating
			|--------------------------------------------------------------------------
			|
			*/
			$rID7 = true;
			$rID8 = true;
			if ($hidden_color_wise_prog_data != "")
			{
				if (count($colorprog_upd_id_arr)>0)
				{
					if (count($data_array_color_wise_prog_update) > 0)
					{
						$rID7 = execute_query(bulk_update_sql_statement("ppl_color_wise_break_down", "id", $field_array_color_wise_prog_update, $data_array_color_wise_prog_update, $colorprog_upd_id_arr));
					}
				}

				/*
				|--------------------------------------------------------------------------
				| ppl_color_wise_break_down
				| data inserting
				|--------------------------------------------------------------------------
				|
				*/
				if ($data_array_color_wise_break_down != "")
				{
					$rID8 = sql_insert("ppl_color_wise_break_down", $field_array_color_wise_break_down, $data_array_color_wise_break_down, 0);
				}
			}


			/*
			|--------------------------------------------------------------------------
			| MYSQL Database
			| data COMMIT here
			|--------------------------------------------------------------------------
			|
			*/
			if ($db_type == 0)
			{
				if ($rID && $rID2 && $deletem && $delete_datewise && $rID3 && $rID4 && $rID7 && $rID8)
				{
					mysql_query("COMMIT");
					echo "1**" . str_replace("'", "", $updateId) . "**0";
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "6**0**1";
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ORACLE Database
			| data COMMIT here
			|--------------------------------------------------------------------------
			|
			*/
			else if ($db_type == 2 || $db_type == 1)
			{
				if ($rID && $rID2 && $deletem && $delete_datewise && $rID3 && $rID4 && $rID7 && $rID8)
				{
					oci_commit($con);
					echo "1**" . str_replace("'", "", $updateId) . "**0";
				}
				else
				{
					oci_rollback($con);
					echo "6**0**1";
				}
			}
		}
		else
		{
			/*
			|--------------------------------------------------------------------------
			| ppl_planning_info_entry_dtls
			| data preparing for
			| $data_array_update
			|--------------------------------------------------------------------------
			|
			*/
			$color_id = 0;
			$field_array_update = "knitting_source*knitting_party*subcontract_party*color_id*color_range*machine_dia*width_dia_type*machine_gg*fabric_dia*program_qnty*stitch_length*spandex_stitch_length*draft_ratio*machine_id*machine_capacity*distribution_qnty*status*start_date*end_date*program_date*feeder*remarks*attention*fabric_type*co_efficient*batch_no*save_data*no_fo_feeder_data*collar_cuff_data*location_id*advice*grey_dia*updated_by*update_date";
			$data_array_update = $cbo_knitting_source . "*" . $cbo_knitting_party . "*" . $cbo_subcontract_party . "*" . $hidden_color_id . "*" . $cbo_color_range . "*" . $txt_machine_dia . "*" . $cbo_dia_width_type . "*" . $txt_machine_gg . "*" . $txt_fabric_dia . "*" . $txt_program_qnty . "*" . $txt_stitch_length . "*" . $txt_spandex_stitch_length . "*" . $txt_draft_ratio . "*" . $machine_id . "*" . $txt_machine_capacity . "*" . $txt_distribution_qnty . "*" . $cbo_knitting_status . "*'" . $txt_start_date . "'*" . $txt_end_date . "*" . $txt_program_date . "*" . $cbo_feeder . "*" . $txt_remarks . "*" . $txt_attention . "*" . $txt_fabric_type ."*". $txt_co_efficient ."*" . $txt_batch_no ."*" .$save_data . "*" . $hidden_no_of_feeder_data . "*" . $hidden_collarCuff_data . "*" . $cbo_location_name . "*" . $hidden_advice_data . "*" . $txt_grey_dia . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_entry_plan_dtls
			| data preparing for
			| $data_array_plan_dtls
			|--------------------------------------------------------------------------
			|
			*/
			$plan_dtls_id = return_next_id("id", "ppl_planning_entry_plan_dtls", 1);
			$field_array_plan_dtls = "id,mst_id,dtls_id,company_id,buyer_id,booking_no,po_id,start_date,finish_date,body_part_id,color_type_id,determination_id,fabric_desc,gsm_weight,dia,width_dia_type,color_id,program_qnty,is_sales,inserted_by,insert_date";

			$data = str_replace("'", "", $dataPre);
			if ($data != '')
			{
				$data = explode("_", $data);
				for ($i = 0; $i < count($data); $i++)
				{
					$plan_data = explode("**", $data[$i]);
					$start_date = '';
					$end_date = '';
					$po_id = 0;
					$buyer_id = $plan_data[0];
					$booking_no = $plan_data[1];
					$determination_id = $plan_data[2];
					$desc = $plan_data[3];
					$gsm = $plan_data[4];
					$dia = $plan_data[5];
					$dia_width_type = $plan_data[6];
					$booking_qnty = $plan_data[7];
					//$gsm_weight = $plan_data[8];
					//$dia_width = $plan_data[9];
					//$gsm_weight = $plan_data[10];
					//$dia_width = $plan_data[11];
					$color_id = $plan_data[12];
					$color_type_id = $plan_data[13];
					$body_part_id = $plan_data[14];

					/*if ($db_type == 0)
					{
						$start_date = change_date_format($start_date, "yyyy-mm-dd", "-");
						$end_date = change_date_format($end_date, "yyyy-mm-dd", "-");
					}
					else
					{
						$start_date = change_date_format($start_date, '', '', 1);
						$end_date = change_date_format($end_date, '', '', 1);
					}*/

					//$perc = ($booking_qnty / $tot_booking_qnty) * 100;
					//$prog_qnty = ($perc * str_replace("'", '', $txt_program_qnty)) / 100;
					//$prog_qnty = $txt_program_qnty;

					//for color wise program qty distribution
					$prog_qnty = 0;
					$colorBookingQty = 0;
					$colorPopupBookingQty = 0;
					$colorPopupProgramQty = 0;
					$expColorBookingData = explode(",", $plan_data[15]);
					for ($z = 0; $z < count($expColorBookingData); $z++)
					{
						$colorBookingData = explode("-", $expColorBookingData[$z]);
						$colorBookingQty = $colorBookingData[1]*1;
						$colorPopupBookingQty = $colorPopupDataArr[$colorBookingData[0]]['booking_qty']*1;
						$colorPopupProgramQty = $colorPopupDataArr[$colorBookingData[0]]['program_qty']*1;
						if($colorPopupBookingQty !=0)
						{
							$prog_qnty += ($colorPopupProgramQty*$colorBookingQty)/$colorPopupBookingQty;
						}
						//echo "14**".$colorPopupProgramQty."=".$colorBookingQty."=".$colorPopupBookingQty."=".$prog_qnty."=";
					}

					if ($data_array_plan_dtls != "")
						$data_array_plan_dtls .= ",";

					$data_array_plan_dtls .= "(" . $plan_dtls_id . "," . $updateId . "," . $update_dtls_id . "," . $companyID . "," . $buyer_id . ",'" . $booking_no . "'," . $po_id . ",'" . $start_date . "','" . $end_date . "'," . $body_part_id . "," . $color_type_id . "," . $determination_id . ",'" . $desc . "','" . $gsm . "','" . $dia . "'," . $dia_width_type . ",0," . $prog_qnty . ",2," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$plan_dtls_id = $plan_dtls_id + 1;
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_info_machine_dtls
			| data preparing for
			| $data_array_machine_dtls
			|--------------------------------------------------------------------------
			|
			*/
			$machine_dtls_id = return_next_id("id", "ppl_planning_info_machine_dtls", 1);
			$field_array_machine_dtls = "id,mst_id,dtls_id,machine_id,dia,capacity,distribution_qnty,no_of_days,start_date,end_date,date_wise_breakdown,inserted_by,insert_date";

			/*
			|--------------------------------------------------------------------------
			| ppl_entry_machine_datewise
			| data preparing for
			| $data_array_machine_dtls
			|--------------------------------------------------------------------------
			|
			*/
			$machine_dtls_datewise_id = return_next_id("id", "ppl_entry_machine_datewise", 1);
			$field_array_machine_dtls_datewise = "id,mst_id,dtls_id,machine_id,distribution_date,fraction_date,days_complete,qnty,inserted_by,insert_date";

			$save_data = str_replace("'", "", $save_data);
			if ($save_data != "")
			{
				$save_data = explode(",", $save_data);
				for ($i = 0; $i < count($save_data); $i++)
				{
					$machine_wise_data = explode("_", $save_data[$i]);
					$machine_id = $machine_wise_data[0];
					$dia = $machine_wise_data[1];
					$capacity = $machine_wise_data[2];
					$qnty = $machine_wise_data[3];
					$noOfDays = $machine_wise_data[4];

					$dateWise_qnty = 0;
					$bl_qnty = $qnty;

					if ($machine_wise_data[5] != "")
						$startDate = date("Y-m-d", strtotime($machine_wise_data[5]));

					if ($machine_wise_data[6] != "")
						$endDate = date("Y-m-d", strtotime($machine_wise_data[6]));

					if ($startDate != "" && $endDate != "")
					{

						$date_wise_breakdown_str="";
						if($date_wise_machine_ref_data[$machine_id] != "")
						{
							$date_wise_breakdown_str = $date_wise_machine_ref_data[$machine_id];
							$date_wise_breakdown_arr =  explode("*", $date_wise_breakdown_str);

							for ($j = 0; $j < count($date_wise_breakdown_arr); $j++)
							{
								$machine_date_data = explode(",", $date_wise_breakdown_arr[$j]);

								$curr_date = $machine_date_data[0];
								$dateWise_qnty = $machine_date_data[1];
								$dateWise_qnty =number_format($dateWise_qnty,2,".","");
								$days_complete = $machine_date_data[2];
								$days_complete =number_format($days_complete,2,".","");

								if($days_complete >= 1)
								{
									$fraction = 0;
								}
								else
								{
									$fraction = 1;
								}

								$previous_occupied_days = $previous_occupied_arr[$machine_id][date("d-m-Y",strtotime($curr_date))]["days_complete"];
								if($previous_occupied_days)
								{
									if($days_complete != (1-$previous_occupied_days))
									{
										echo "14** Date : ".$curr_date .", Machine No: ".$machine_arr[$machine_id] ." already occupied other program $days_complete != (1-$previous_occupied_days) -- $machine_id == $curr_date"; disconnect($con); die;
									}
								}

								if ($db_type == 0)
									$curr_date = change_date_format($curr_date, "yyyy-mm-dd", "-");
								else
									$curr_date = change_date_format($curr_date, '', '', 1);

								if ($data_array_machine_dtls_datewise != "")
									$data_array_machine_dtls_datewise .= ",";
								$data_array_machine_dtls_datewise .= "(" . $machine_dtls_datewise_id . "," . $updateId . "," . $update_dtls_id . ",'" . $machine_id . "','" . $curr_date . "','" . $fraction . "','" . $days_complete . "','" . $dateWise_qnty . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
								$machine_dtls_datewise_id = $machine_dtls_datewise_id + 1;
							}
						}
						else
						{
							$distribute_qnty = $qnty;
							$capacity_qnty = $capacity;
							$no_of_days = $noOfDays;
							if ($db_type == 0)
							{
								$start_date = change_date_format(trim($startDate), "yyyy-mm-dd", "");
							}
							else
							{
								$start_date = change_date_format(trim($startDate), '', '', 1);
							}

							$pre_sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date >= '$start_date' group by distribution_date order by distribution_date";

							$data_array = sql_select($pre_sql);
							if (count($data_array) > 0)
							{
								foreach ($data_array as $row)
								{
									$distribution_date = date('d-m-Y',strtotime($row[csf('distribution_date')]));
									$previous_occupied_arr_without_ref[$distribution_date]["days_complete"] = $row[csf('days_complete')];
									$previous_occupied_arr_without_ref[$distribution_date]["distribution_date"] = $distribution_date;
								}
							}

							$present_day = $start_date;
							$without_ref_dateWise_qnty = ""; $end_date_ref = "";
							while($distribute_qnty > 0)
							{
								$present_day = date("d-m-Y",strtotime($present_day));
								if($previous_occupied_arr_without_ref[$present_day]["distribution_date"])
								{
									if($previous_occupied_arr_without_ref[$present_day]["distribution_date"] == $present_day)
									{
										if ($previous_occupied_arr_without_ref[$present_day]["days_complete"] >= 1)
										{
											$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
										}
										else
										{
											if($distribute_qnty > ($capacity_qnty * (1 - $previous_occupied_arr_without_ref[$present_day]["days_complete"])))
											{
												$dist_qnty = $capacity_qnty * (1 - $previous_occupied_arr_without_ref[$present_day]["days_complete"]);
												$without_ref_dateWise_qnty .= $present_day.",".$dist_qnty.",".($dist_qnty/$capacity_qnty)."*";
											}
											else
											{
												$without_ref_dateWise_qnty .= $present_day.",".$distribute_qnty.",".($distribute_qnty/$capacity_qnty)."*";
											}

											$distribute_qnty = $distribute_qnty - ($capacity_qnty * (1 - $previous_occupied_arr_without_ref[$present_day]["days_complete"]));
											$end_date_ref =  $present_day;
											$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
										}
									}
								}
								else
								{
									if($distribute_qnty > ($capacity_qnty * (1 - $previous_occupied_arr_without_ref[$present_day]["days_complete"])))
									{
										$without_ref_dateWise_qnty .= $present_day.",".$capacity_qnty.",".($capacity_qnty/$capacity_qnty)."*";
									}
									else
									{
										$without_ref_dateWise_qnty .= $present_day.",".$distribute_qnty.",".($distribute_qnty/$capacity_qnty)."*";
									}

									$end_date_ref =  $present_day;
									$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
									$distribute_qnty = $distribute_qnty - $capacity_qnty;
								}
							}

							$without_ref_dateWise_qnty = chop($without_ref_dateWise_qnty,"*");
							$date_wise_breakdown_str =$without_ref_dateWise_qnty;
							$without_ref_arr =  array_filter(explode("*", $without_ref_dateWise_qnty));
							foreach ($without_ref_arr as  $ref_value)
							{
								$ref_value = explode(",", $ref_value);
								$curr_date =  $ref_value[0];
								$dateWise_qnty =  $ref_value[1];
								$days_complete =  $ref_value[2];
								$days_complete =number_format($days_complete,2,".","");
								if($days_complete >= 1)
								{
									$fraction =0;
								}
								else
								{
									$fraction =1;
								}

								$previous_occupied_days = $previous_occupied_arr[$machine_id][date("d-m-Y",strtotime($curr_date))]["days_complete"];
								if($previous_occupied_days)
								{
									if($days_complete != (1-$previous_occupied_days))
									{
										echo "14** Date : ".$curr_date .", Machine No: ".$machine_arr[$machine_id] ." already occupied other program $days_complete != (1-$previous_occupied_days) -- $machine_id == $curr_date";disconnect($con);die;
									}
								}

								if ($db_type == 0)
									$curr_date = change_date_format($curr_date, "yyyy-mm-dd", "-");
								else
									$curr_date = change_date_format($curr_date, '', '', 1);

								if ($data_array_machine_dtls_datewise != "")
									$data_array_machine_dtls_datewise .= ",";
								$data_array_machine_dtls_datewise .= "(" . $machine_dtls_datewise_id . "," . $updateId . "," . $update_dtls_id . ",'" . $machine_id . "','" . $curr_date . "','" . $fraction . "','" . $days_complete . "','" . $dateWise_qnty . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
								$machine_dtls_datewise_id = $machine_dtls_datewise_id + 1;
							}
						}
					}

					if ($db_type == 0)
					{
						$mstartDate = $startDate;
						$mendDate = $endDate;
					}
					else
					{
						$mstartDate = change_date_format($startDate, '', '', 1);
						$mendDate = change_date_format($endDate, '', '', 1);
					}

					if ($data_array_machine_dtls != "")
						$data_array_machine_dtls .= ",";
					$data_array_machine_dtls .= "(" . $machine_dtls_id . "," . $updateId . "," . $update_dtls_id . ",'" . $machine_id . "','" . $dia . "','" . $capacity . "','" . $qnty . "','" . $noOfDays . "','" . $mstartDate . "','" . $mendDate . "','" . $date_wise_breakdown_str . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$machine_dtls_id = $machine_dtls_id + 1;
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_feeder_dtls
			| data preparing for
			| $data_array_feeder_dtls
			|--------------------------------------------------------------------------
			|
			*/
			$feeder_dtls_id = return_next_id("id", "ppl_planning_feeder_dtls", 1);
			$field_array_feeder_dtls = "id,mst_id,dtls_id,pre_cost_id,color_id,stripe_color_id,no_of_feeder,inserted_by,insert_date";

			$hidden_no_of_feeder_data = str_replace("'", "", $hidden_no_of_feeder_data);
			if ($hidden_no_of_feeder_data != "")
			{
				$hidden_no_of_feeder_data = explode(",", $hidden_no_of_feeder_data);
				for ($i = 0; $i < count($hidden_no_of_feeder_data); $i++)
				{
					$color_wise_data = explode("_", $hidden_no_of_feeder_data[$i]);
					$pre_cost_id = $color_wise_data[0];
					$color_id = $color_wise_data[1];
					$stripe_color_id = $color_wise_data[2];
					$no_of_feeder = $color_wise_data[3];

					if ($data_array_feeder_dtls != "")
						$data_array_feeder_dtls .= ",";

					$data_array_feeder_dtls .= "(" . $feeder_dtls_id . "," . $updateId . "," . $update_dtls_id . ",'" . $pre_cost_id . "','" . $color_id . "','" . $stripe_color_id . "','" . $no_of_feeder . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$feeder_dtls_id = $feeder_dtls_id + 1;
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_collar_cuff_dtls
			| data preparing for
			| $data_array_collar_cuff_dtls
			|--------------------------------------------------------------------------
			|
			*/
			$hidden_collarCuff_data = str_replace("'", "", $hidden_collarCuff_data);
			if ($hidden_collarCuff_data != "")
			{
				$collar_cuff_dtls_id = return_next_id("id", "ppl_planning_collar_cuff_dtls", 1);
				$field_array_collar_cuff_dtls = "id,mst_id,dtls_id,body_part_id,grey_size,finish_size,qty_pcs,inserted_by,insert_date";

				$hidden_collarCuff_data = explode(",", $hidden_collarCuff_data);
				for ($i = 0; $i < count($hidden_collarCuff_data); $i++)
				{
					$collarCuff_wise_data = explode("_", $hidden_collarCuff_data[$i]);
					$body_part_id = $collarCuff_wise_data[0];
					$grey_size = $collarCuff_wise_data[1];
					$finish_size = $collarCuff_wise_data[2];
					$qty_pcs = $collarCuff_wise_data[3];

					if ($data_array_collar_cuff_dtls != "")
						$data_array_collar_cuff_dtls .= ",";

					$data_array_collar_cuff_dtls .= "(" . $collar_cuff_dtls_id . "," . $updateId . "," . $update_dtls_id . ",'" . $body_part_id . "','" . $grey_size . "','" . $finish_size . "','" . $qty_pcs . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$collar_cuff_dtls_id = $collar_cuff_dtls_id + 1;
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_count_feed_dtls
			| data preparing for
			| $data_array_count_feeding_dtls
			|--------------------------------------------------------------------------
			|
			*/
			$hidden_count_feeding_data = str_replace("'", "", $hidden_count_feeding_data);
			if($hidden_count_feeding_data  != "")
			{
				$count_feeding_id = return_next_id("id", "ppl_planning_count_feed_dtls", 1);
				$field_array_count_feeding_dtls = "id, mst_id, dtls_id, seq_no, count_id,feeding_id, inserted_by, insert_date";

				$hidden_count_feeding_data_arr = explode(",", $hidden_count_feeding_data);
				for ($i = 0; $i < count($hidden_count_feeding_data_arr); $i++)
				{
					$count_feeding_data_arr = explode("_", $hidden_count_feeding_data_arr[$i]);
					$seq_no = $count_feeding_data_arr[0];
					$count_id = $count_feeding_data_arr[1];
					$feeding_id = $count_feeding_data_arr[2];
					if ($data_array_count_feeding_dtls != "") $data_array_count_feeding_dtls .= ",";
					$data_array_count_feeding_dtls .= "(" . $count_feeding_id . "," . $updateId . "," . $update_dtls_id . "," . $seq_no . "," . $count_id . "," . $feeding_id. "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$count_feeding_id = $count_feeding_id + 1;
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_color_wise_break_down
			| data preparing for
			| $data_array_color_wise_prog_update
			| data_array_color_wise_break_down
			|--------------------------------------------------------------------------
			|
			*/
			$hidden_color_wise_prog_data = str_replace("'", "", $hidden_color_wise_prog_data);
			if ($hidden_color_wise_prog_data != "")
			{
				$color_wise_break_down_id = return_next_id("id", "ppl_color_wise_break_down", 1);
				$field_array_color_wise_break_down = "id, plan_id, program_no, color_id, color_prog_qty, inserted_by, insert_date";
				$field_array_color_wise_prog_update = "color_id*color_prog_qty*updated_by*update_date*status_active*is_deleted";
				$color_wise_prog_data = explode(",", $hidden_color_wise_prog_data);
				for ($i = 0; $i < count($color_wise_prog_data); $i++)
				{
					$colorWiseProgData = explode("_", $color_wise_prog_data[$i]);
					$color_id = $colorWiseProgData[0];
					$color_prog_qty = $colorWiseProgData[1];
					$color_wise_break_down_udpdateId = $colorWiseProgData[2];

					if ($color_wise_break_down_udpdateId>0)
					{
						$status_active = 1;
						$is_deleted = 0;
						if($color_prog_qty <= 0)
						{
							$status_active = 0;
							$is_deleted = 1;
						}

						$colorprog_upd_id_arr[] = $color_wise_break_down_udpdateId;
						$data_array_color_wise_prog_update[$color_wise_break_down_udpdateId] = explode("*", ("'" . $color_id . "'*'". $color_prog_qty . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'". $status_active . "'*'". $is_deleted . "'"));
					}
					else
					{
						if($color_prog_qty>0)
						{
							if ($data_array_color_wise_break_down != "")
								$data_array_color_wise_break_down .= ",";

							$data_array_color_wise_break_down .= "(" . $color_wise_break_down_id . "," . $updateId . "," . $update_dtls_id . ",'" . $color_id . "','" . $color_prog_qty . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
							$color_wise_break_down_id = $color_wise_break_down_id + 1;
						}
					}
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_info_machine_dtls
			| data deleting
			|--------------------------------------------------------------------------
			|
			*/
			$delete = execute_query("DELETE FROM ppl_planning_entry_plan_dtls WHERE dtls_id = ".$update_dtls_id."", 0);
			if ($delete)
				$flag = 1;
			else
				$flag = 0;

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_info_entry_dtls
			| data updating
			|--------------------------------------------------------------------------
			|
			*/
			$rID = sql_update("ppl_planning_info_entry_dtls", $field_array_update, $data_array_update, "id", $update_dtls_id, 1);
			if ($flag == 1)
			{
				if ($rID)
					$flag = 1;
				else
					$flag = 0;
			}

			if($hidden_count_feeding_data != "")
			{
				/*
				|--------------------------------------------------------------------------
				| ppl_planning_count_feed_dtls
				| data deleting
				|--------------------------------------------------------------------------
				|
				*/
				$delete_feeding = execute_query("DELETE FROM ppl_planning_count_feed_dtls WHERE dtls_id = ".$update_dtls_id."", 0);
				if($delete_feeding)
					$flag = 1;
				else
					$flag = 0;

				/*
				|--------------------------------------------------------------------------
				| ppl_planning_count_feed_dtls
				| data inserting
				|--------------------------------------------------------------------------
				|
				*/
				if($flag == 1 && $data_array_count_feeding_dtls)
				{
					$rID2 = sql_insert("ppl_planning_count_feed_dtls", $field_array_count_feeding_dtls, $data_array_count_feeding_dtls, 0);
					if ($rID2)
						$flag = 1;
					else
						$flag = 0;
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_entry_plan_dtls
			| data inserting
			|--------------------------------------------------------------------------
			|
			*/
			if ($data != "")
			{
				if ($data_array_plan_dtls != "")
				{
					if ($flag == 1)
					{
						//echo "10**INSERT INTO ppl_planning_entry_plan_dtls(".$field_array_plan_dtls.") VALUES".$data_array_plan_dtls;
						$rID2 = sql_insert("ppl_planning_entry_plan_dtls", $field_array_plan_dtls, $data_array_plan_dtls, 0);
						if ($rID2)
							$flag = 1;
						else
							$flag = 0;
					}
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_info_machine_dtls
			| data deleting
			|--------------------------------------------------------------------------
			|
			*/
			if ($flag == 1)
			{
				$deletem = execute_query("DELETE FROM ppl_planning_info_machine_dtls WHERE dtls_id = ".$update_dtls_id."", 0);
				if ($deletem)
					$flag = 1;
				else
					$flag = 0;
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_entry_machine_datewise
			| data deleting
			|--------------------------------------------------------------------------
			|
			*/
			if ($flag == 1)
			{
				$delete_datewise = execute_query("DELETE FROM ppl_entry_machine_datewise WHERE dtls_id = ".$update_dtls_id."", 0);
				if ($delete_datewise)
					$flag = 1;
				else
					$flag = 0;
			}

			if ($save_data != "")
			{
				/*
				|--------------------------------------------------------------------------
				| ppl_planning_info_machine_dtls
				| data inserting
				|--------------------------------------------------------------------------
				|
				*/
				if ($data_array_machine_dtls != "")
				{
					$rID3 = sql_insert("ppl_planning_info_machine_dtls", $field_array_machine_dtls, $data_array_machine_dtls, 0);
					if ($flag == 1)
					{
						if ($rID3)
							$flag = 1;
						else
							$flag = 0;
					}
				}

				/*
				|--------------------------------------------------------------------------
				| ppl_entry_machine_datewise
				| data inserting
				|--------------------------------------------------------------------------
				|
				*/
				if ($data_array_machine_dtls_datewise != "")
				{
					$rID4 = sql_insert("ppl_entry_machine_datewise", $field_array_machine_dtls_datewise, $data_array_machine_dtls_datewise, 0);
					if ($flag == 1)
					{
						if ($rID4)
							$flag = 1;
						else
							$flag = 0;
					}
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_feeder_dtls
			| data deleting
			|--------------------------------------------------------------------------
			|
			*/
			if ($flag == 1)
			{
				$delete_feeder = execute_query("DELETE FROM ppl_planning_feeder_dtls WHERE dtls_id = ".$update_dtls_id."", 0);
				if ($delete_feeder)
					$flag = 1;
				else
					$flag = 0;
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_feeder_dtls
			| data inserting
			|--------------------------------------------------------------------------
			|
			*/
			if ($hidden_no_of_feeder_data != "")
			{
				if ($data_array_feeder_dtls != "")
				{
					$rID5 = sql_insert("ppl_planning_feeder_dtls", $field_array_feeder_dtls, $data_array_feeder_dtls, 0);
					if ($flag == 1)
					{
						if ($rID5)
							$flag = 1;
						else
							$flag = 0;
					}
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_collar_cuff_dtls
			| data deleting
			|--------------------------------------------------------------------------
			|
			*/
			$delete_collar_cuff = execute_query("DELETE FROM ppl_planning_collar_cuff_dtls WHERE dtls_id = ".$update_dtls_id."", 0);
			if ($flag == 1)
			{
				if ($delete_collar_cuff)
					$flag = 1;
				else
					$flag = 0;
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_planning_collar_cuff_dtls
			| data inserting
			|--------------------------------------------------------------------------
			|
			*/
			if ($data_array_collar_cuff_dtls != "")
			{
				$rID6 = sql_insert("ppl_planning_collar_cuff_dtls", $field_array_collar_cuff_dtls, $data_array_collar_cuff_dtls, 0);
				if ($flag == 1)
				{
					if ($rID6)
						$flag = 1;
					else
						$flag = 0;
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ppl_color_wise_break_down
			| data updating
			|--------------------------------------------------------------------------
			|
			*/
			if ($hidden_color_wise_prog_data != "")
			{
				if (count($colorprog_upd_id_arr)>0)
				{
					if (count($data_array_color_wise_prog_update) > 0)
					{
						if ($flag == 1)
						{
							$rID7 = execute_query(bulk_update_sql_statement("ppl_color_wise_break_down", "id", $field_array_color_wise_prog_update, $data_array_color_wise_prog_update, $colorprog_upd_id_arr));
							if ($rID7)
								$flag = 1;
							else
								$flag = 0;
						}
					}
				}

				/*
				|--------------------------------------------------------------------------
				| ppl_color_wise_break_down
				| data inserting
				|--------------------------------------------------------------------------
				|
				*/
				if ($data_array_color_wise_break_down != "")
				{
					if ($flag == 1)
					{
						$rID8 = sql_insert("ppl_color_wise_break_down", $field_array_color_wise_break_down, $data_array_color_wise_break_down, 0);
						if ($rID8)
							$flag = 1;
						else
							$flag = 0;
					}
				}
			}

			/*
			|--------------------------------------------------------------------------
			| MYSQL Database
			| data COMMIT here
			|--------------------------------------------------------------------------
			|
			*/
			if ($db_type == 0)
			{
				if ($flag == 1)
				{
					mysql_query("COMMIT");
					echo "1**" . str_replace("'", "", $updateId) . "**0";
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "6**0**1";
				}
			}

			/*
			|--------------------------------------------------------------------------
			| ORACLE Database
			| data COMMIT here
			|--------------------------------------------------------------------------
			|
			*/
			else if ($db_type == 2 || $db_type == 1)
			{
				if ($flag == 1)
				{
					oci_commit($con);
					echo "1**" . str_replace("'", "", $updateId) . "**0";
				}
				else
				{
					oci_rollback($con);
					echo "6**0**1";
				}
			}
		}
		disconnect($con);
		die;
	}

	/*
	|--------------------------------------------------------------------------
	| Delete
	|--------------------------------------------------------------------------
	|
	*/
	else if ($operation == 2)
	{
		$con = connect();
		if ($db_type == 0)
		{
			mysql_query("BEGIN");
		}

		$check_issue = return_field_value("c.issue_number", "ppl_yarn_requisition_entry a,inv_transaction b,inv_issue_master c", "a.requisition_no = b.requisition_no AND b.mst_id = c.id AND b.item_category = 1 AND c.item_category = 1 AND a.status_active = 1 AND b.status_active = 1 AND a.knit_id = ".$update_dtls_id."", "issue_number");

		if ($check_issue != "")
		{
			echo "13**Yarn Issue Found.Program can not be deleted.\nIssue ID: ".$check_issue;
			disconnect($con);
			exit();
		}

		$knit_qty = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id=$update_dtls_id and b.status_active=1 and b.is_deleted=0", "knitting_qnty");

		if ($knit_qty > 0)
		{
			echo "13**Production Found. Delete Not Allowed.";
			disconnect($con);
			exit();
		}

		$field_array_update = "status_active*is_deleted*updated_by*update_date";
		$data_array_update = "0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_info_entry_dtls
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		$rID = sql_update("ppl_planning_info_entry_dtls", $field_array_update, $data_array_update, "id", $update_dtls_id, 0);
		if ($rID)
			$flag = 1;
		else
			$flag = 0;

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_entry_plan_dtls
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1)
		{
			$rID2 = sql_update("ppl_planning_entry_plan_dtls", $field_array_update, $data_array_update, "dtls_id", $update_dtls_id, 0);
			if ($rID2)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_color_wise_break_down
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1)
		{
			$rID3 = sql_update("ppl_color_wise_break_down", $field_array_update, $data_array_update, "program_no", $update_dtls_id, 0);
			if ($rID3)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_info_machine_dtls
		| data deleting
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1)
		{
			$delete = execute_query("DELETE FROM ppl_planning_info_machine_dtls WHERE dtls_id = ".$update_dtls_id."", 0);
			if ($delete)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_count_feed_dtls
		| data deleting
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1)
		{
			$delete = execute_query("DELETE FROM ppl_planning_count_feed_dtls WHERE dtls_id = ".$update_dtls_id."", 0);
			if ($delete)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_entry_machine_datewise
		| data deleting
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1)
		{
			$delete_datewise = execute_query("DELETE FROM ppl_entry_machine_datewise WHERE dtls_id = ".$update_dtls_id."", 0);
			if ($delete_datewise)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_feeder_dtls
		| data deleting
		|--------------------------------------------------------------------------
		|
		*/
		$delete_feeder = execute_query("DELETE FROM ppl_planning_feeder_dtls WHERE dtls_id = ".$update_dtls_id."", 0);
		if ($flag == 1)
		{
			if ($delete_feeder)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_planning_collar_cuff_dtls
		| data deleting
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1)
		{
			$delete_collar_cuff = execute_query("DELETE FROM ppl_planning_collar_cuff_dtls WHERE dtls_id = ".$update_dtls_id."", 0);
			if ($delete_collar_cuff)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if ($db_type == 0)
		{
			if ($flag == 1)
			{
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $updateId) . "**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "7**0**1";
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ORACLE Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if ($db_type == 2 || $db_type == 1)
		{
			if ($flag == 1)
			{
				oci_commit($con);
				echo "2**" . str_replace("'", "", $updateId) . "**0";
			}
			else
			{
				oci_rollback($con);
				echo "7**0**1";
			}
		}
		disconnect($con);
		die;
	}
}

/*
|--------------------------------------------------------------------------
| load_drop_down_knitting_party
|--------------------------------------------------------------------------
|
*/
if ($action == "load_drop_down_knitting_party")
{
	$data = explode("**", $data);
	if ($data[0] == 1)
	{
		$users_working_unit_cond = '';
		$sql_user_rslt = sql_select("SELECT working_unit_id AS WORKING_UNIT_ID FROM user_passwd WHERE id = '".$_SESSION['logic_erp']['user_id']."' AND valid = 1");
		$users_working_unit = $sql_user_rslt[0]['WORKING_UNIT_ID'];
		if($users_working_unit != '')
		{
			echo create_drop_down("cbo_knitting_party", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  AND comp.id IN(".$users_working_unit.") order by comp.company_name", "id,company_name", 1, "--Select Knit Party--", $data[1], "load_drop_down( 'planning_info_entry_for_sample_without_order_controller', this.value, 'load_drop_down_location', 'location_td' );", "");
		}
		else
		{
			echo create_drop_down("cbo_knitting_party", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Party--", $data[1], "load_drop_down( 'planning_info_entry_for_sample_without_order_controller', this.value, 'load_drop_down_location', 'location_td' );", "");
		}
	}
	else if ($data[0] == 3)
	{
		if ($data[2] == 1)
			$selected_id = $data[1];
		else
			$selected_id = 0;
		echo create_drop_down("cbo_knitting_party", 152, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 and a.is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Party--", $selected_id, "getValutAttention(this.value);");
	}
	else
	{
		echo create_drop_down("cbo_knitting_party", 152, $blank_array, "", 1, "--Select Knit Party--", 0, "getValutAttention(this.value);");
	}
	exit();
}

/*
|--------------------------------------------------------------------------
| load_drop_down_location
|--------------------------------------------------------------------------
|
*/
if ($action == "load_drop_down_location")
{
	echo create_drop_down("cbo_location_name", 152, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "");
	exit();
}

/*
|--------------------------------------------------------------------------
| check_last_attention_action
|--------------------------------------------------------------------------
|
*/
if($action=="check_last_attention_action")
{
	$data=explode("*",$data);
	$data_array_attention=sql_select("select id,attention from ppl_planning_info_entry_dtls where knitting_source=$data[0] and knitting_party=$data[1] and is_deleted=0 and status_active=1 order by id desc ");
	foreach ($data_array_attention as $row)
	{
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		exit();
	}
}

/*
|--------------------------------------------------------------------------
| color_info_popup
|--------------------------------------------------------------------------
|
*/
if ($action == "color_info_popup")
{
	echo load_html_head_contents("Color Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		$(document).ready(function (e)
		{
			setFilterGrid('tbl_list_search', -1);
			set_all();
		});

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

		function set_all()
		{
			var old = document.getElementById('txt_color_row_id').value;
			if (old != "")
			{
				old = old.split(",");
				for (var i = 0; i < old.length; i++)
				{
					js_set_value(old[i])
				}
			}
		}

		/*
		function js_set_value2(str)
		{
			var id = $('#txt_individual_id' + str).val()
			var name = $('#txt_individual' + str).val();
			var color_balance_qty = $('#txt_individual_color_blqty' + str).val();
			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
			$('#txt_selected_color_bl_qty').val(color_balance_qty);
			//parent.emailwindow.hide();
		}*/

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
					if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++)
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
		}

		function fnc_close(colorMixing)
		{
			var save_string = "";
			var breakOut = true;
			var total_prog_qty = 0;
			var color_name_string = '';
			var color_id_string = '';
			var color_prog_qty_string = '';
			var allowed_qty = 0;
			var colorQtyArr = [];

			$("#tbl_list_search").find('tbody tr').not(":first").each(function ()
			{
				var coloProgUpdateId = $(this).find('input[name="colo_prog_update_id[]"]').val();
				var txtColorId = $(this).find('input[name="text_colorid_[]"]').val();
				var txtColorName = $(this).find('input[name="text_color_name_[]"]').val().trim();
				var txtColorProgQty = $(this).find('input[name="text_color_prog_qty[]"]').val() * 1;
				var hidden_color_allowed_qty = $(this).find('input[name="hidden_color_allowed_qty[]"]').val() * 1;
				var hidden_color_prev_prog_qty = $(this).find('input[name="hidden_color_prev_prog_qty[]"]').val() * 1;
				var txt_individual_color_blqty = $(this).find('input[name="txt_individual_color_blqty[]"]').val() * 1;
				var updateQty = $(this).find('input[name="text_color_prog_qty[]"]').attr('data-update-qty')*1;

				if(txtColorProgQty>0 || (coloProgUpdateId !=0 && updateQty > 0))
				{
					if (save_string == "")
					{
						save_string = txtColorId + "_" + txtColorProgQty+ "_" + coloProgUpdateId+ "_" + hidden_color_allowed_qty;
						color_name_string = txtColorName;
						color_id_string = txtColorId;
						//color_prog_qty_string = txtColorProgQty;
					}
					else
					{
						save_string += "," + txtColorId + "_" + txtColorProgQty+ "_" + coloProgUpdateId+ "_" + hidden_color_allowed_qty;
						color_name_string += "," + txtColorName;
						color_id_string += "," + txtColorId;
						//color_prog_qty_string += "," + txtColorProgQty;
					}

					if(txtColorProgQty>0)
					{
						colorQtyArr.push(txtColorProgQty);
					}

					total_prog_qty += txtColorProgQty;

					if(hidden_color_allowed_qty<(hidden_color_prev_prog_qty+txtColorProgQty))
					{
						alert("Program quantity can not be greater than Balance quantity");
						$(this).find('input[name="text_color_prog_qty[]"]').focus();
						return;
					}
				}

			});


			if (total_prog_qty < 1)
			{
				alert("Program quantity zero is not allowed");
				$('#text_color_prog_qty_1').focus();
				return false;
			}

			if(colorMixing!=1)
			{
				if(colorQtyArr.length>1)
				{
					alert('Color Mixing is not allowed');
					return;
				}
			}

			$('#hidden_color_wise_prog_data').val(save_string);
			$('#hidden_total_prog_qty').val(total_prog_qty);
			$('#txt_selected_id').val(color_id_string);
			$('#txt_selected').val(color_name_string);
			parent.emailwindow.hide();
		}

		//func_onkeyup_color_qty
		function func_onkeyup_color_qty(rowId)
		{
			var bookingQty = $('#hidden_color_allowed_qty'+rowId).val()*1;
			var previousQty = $('#hidden_color_prev_prog_qty_'+rowId).val()*1;
			var qty = $('#text_color_prog_qty_'+rowId).val()*1;
			var updateQty = $('#text_color_prog_qty_'+rowId).attr('data-update-qty');

			if(qty < 0)
			{
				alert("Program quantity can't be less than zero.");
				$('#text_color_prog_qty_'+rowId).val(updateQty);
				return;
			}

			if(bookingQty < (previousQty+qty))
			{
				alert("Program quantity can't exceed booking quantity");
				$('#text_color_prog_qty_'+rowId).val(updateQty);
				return;
			}
		}
	</script>
</head>
<body>
	<div align="center" style="width:630px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:630px; margin-top:10px; margin-left:20px">
				<div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
						<thead>
							<th width="40">SL</th>
							<th width="220">Color</th>
							<th width="80">Qnty</th>
							<th width="80">Prog. Qty</th>
							<th width="80">Prev. Prog. Qty</th>
							<th>Balance</th>
							<input type="hidden" name="txt_selected_id" id="txt_selected_id" value=""/>
							<input type="hidden" name="txt_selected" id="txt_selected" value=""/>
							<input type="hidden" name="txt_selected_color_bl_qty" id="txt_selected_color_bl_qty" value=""/>
							<input type="hidden" name="hidden_color_wise_prog_data" id="hidden_color_wise_prog_data" class="text_boxes" value="">
							<input type="hidden" name="hidden_total_prog_qty" id="hidden_total_prog_qty" class="text_boxes" value="">
						</thead>
					</table>
					<div style="width:600px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="580" class="rpt_table"
						id="tbl_list_search">
						<tbody>
						<?
						$hdnColorId = $hidden_color_id;
						$hidden_color_id = explode(",", $hidden_color_id);
						//$pre_cost_id = explode(",", $pre_cost_id);
						//$pre_cost_id = implode(",", array_unique($pre_cost_id));
						//$color_library = return_library_array("SELECT id, color_name FROM lib_color", "id", "color_name");
						$color_library = return_library_array("SELECT id, color_name FROM lib_color WHERE id IN(".$hdnColorId.")", "id", "color_name");

						//for gsm
						if ($gsm != '')
						{
							$gsm_cond = " AND b.gsm_weight = '".$gsm."'";
						}
						else
						{
							if($db_type == 0)
							{
								$gsm_cond = " AND b.gsm_weight = ''";
							}
							else
							{
								$gsm_cond = " AND b.gsm_weight IS NULL";
							}
						}

						//for dia

						if ($dia != '')
						{
							if($hdnBookingEntryForm==140)
							{
								$dia_cond = " AND (b.dia = '".$dia."' or b.dia_width = '".$dia."') ";
							}
							else
							{
								$dia_cond = " AND b.dia_width = '".$dia."'";
							}
						}
						else
						{
							if($db_type == 0)
							{
								if($hdnBookingEntryForm==140)
								{
									$dia_cond = " AND (b.dia = '' or b.dia_width = '') ";
								}
								else
								{
									$dia_cond = " AND b.dia_width = ''";
								}
							}
							else
							{
								if($hdnBookingEntryForm==140)
								{
									$dia_cond = " AND ( b.dia IS NULL or b.dia_width IS NULL ) ";
								}
								else
								{
									$dia_cond = " AND b.dia_width IS NULL";
								}
							}
						}

						$sql = "
							SELECT
								a.booking_no,
								b.body_part, b.color_type_id, b.lib_yarn_count_deter_id, b.gsm_weight, (CASE WHEN b.entry_form_id = 140 THEN b.dia ELSE b.dia_width END) dia_width, b.fabric_color, SUM(b.grey_fabric) AS finish_fabric
							FROM
								wo_non_ord_samp_booking_mst a
								INNER JOIN wo_non_ord_samp_booking_dtls b ON a.booking_no = b.booking_no
							WHERE
								a.company_id = ".$companyId."
								AND a.booking_no = '".$booking_no."'
								AND b.body_part IN(".$bodyPartId.")
								AND b.color_type_id IN(".$colorTypeId.")
								AND b.lib_yarn_count_deter_id = '".$fabricId."'
								".$gsm_cond."
								".$dia_cond."
								AND b.fabric_color IN(".$hdnColorId.")
								AND a.item_category = 2
								AND a.status_active=1
								AND a.is_deleted=0
								and (a.fabric_source=1 or b.fabric_source=1)
								AND b.status_active=1
								AND b.is_deleted=0
							GROUP BY
								a.booking_no,
								b.body_part, b.color_type_id, b.lib_yarn_count_deter_id, b.gsm_weight, b.dia_width, b.fabric_color, b.entry_form_id, b.dia 
							union all 
							SELECT
								a.booking_no,
								b.body_part, b.color_type_id, b.lib_yarn_count_deter_id, b.gsm_weight, (CASE WHEN b.entry_form_id = 140 THEN b.dia ELSE b.dia_width END) dia_width, b.gmts_color as fabric_color, SUM(b.grey_fabric) AS finish_fabric
							FROM
								wo_non_ord_samp_booking_mst a
								INNER JOIN wo_non_ord_samp_booking_dtls b ON a.booking_no = b.booking_no
							WHERE
								a.company_id = ".$companyId."
								AND a.booking_no = '".$booking_no."'
								AND b.body_part IN(".$bodyPartId.")
								AND b.color_type_id IN(".$colorTypeId.")
								AND b.lib_yarn_count_deter_id = '".$fabricId."'
								".$gsm_cond."
								".$dia_cond."
								AND b.fabric_color not IN(".$hdnColorId.")
								AND b.gmts_color IN(".$hdnColorId.")
								AND a.item_category = 2
								AND a.status_active=1
								AND a.is_deleted=0
								and (a.fabric_source=1 or b.fabric_source=1)
								AND b.status_active=1
								AND b.is_deleted=0
							GROUP BY
								a.booking_no,
								b.body_part, b.color_type_id, b.lib_yarn_count_deter_id, b.gsm_weight, b.dia_width, b.gmts_color, b.entry_form_id, b.dia
						"; 
						$result = sql_select($sql);
						/*if(count($result)==0)
						{
							$sql = "
							SELECT
								a.booking_no,
								b.body_part, b.color_type_id, b.lib_yarn_count_deter_id, b.gsm_weight, (CASE WHEN b.entry_form_id = 140 THEN b.dia ELSE b.dia_width END) dia_width, b.gmts_color as fabric_color, SUM(b.grey_fabric) AS finish_fabric
							FROM
								wo_non_ord_samp_booking_mst a
								INNER JOIN wo_non_ord_samp_booking_dtls b ON a.booking_no = b.booking_no
							WHERE
								a.company_id = ".$companyId."
								AND a.booking_no = '".$booking_no."'
								AND b.body_part IN(".$bodyPartId.")
								AND b.color_type_id IN(".$colorTypeId.")
								AND b.lib_yarn_count_deter_id = '".$fabricId."'
								".$gsm_cond."
								".$dia_cond."
								AND b.gmts_color IN(".$hdnColorId.")
								AND a.item_category = 2
								AND a.status_active=1
								AND a.is_deleted=0
								and (a.fabric_source=1 or b.fabric_source=1)
								AND b.status_active=1
								AND b.is_deleted=0
							GROUP BY
								a.booking_no,
								b.body_part, b.color_type_id, b.lib_yarn_count_deter_id, b.gsm_weight, b.dia_width, b.gmts_color, b.entry_form_id, b.dia
						";
						}*/

						/*$sql = "
							SELECT
								b.fabric_color, sum(b.finish_fabric) as qnty
							FROM
								wo_non_ord_samp_booking_mst a
								INNER JOIN wo_non_ord_samp_booking_dtls b ON a.booking_no = b.booking_no
							WHERE
								a.company_id = ".$companyId."
								AND a.booking_no = '".$booking_no."'
								AND b.fabric_color IN(".$hdnColorId.")
								AND a.item_category = 2
								AND a.status_active=1
								AND a.is_deleted=0
								AND b.fabric_source = 1
								AND b.status_active=1
								AND b.is_deleted=0
							GROUP BY
								b.fabric_color
						";*/
						//echo $sql;
						$result = sql_select($sql);
						foreach($result as $row)
						{
							//$rptData[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('color_type_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color')]]['bookingQty'] += $row[csf('finish_fabric')];
							$rptData[$row[csf('fabric_color')]]['bookingQty'] += $row[csf('finish_fabric')];
						}
						//echo "<pre>";
						//print_r($rptData);

						/*foreach ($rptData as $bookingNo=>$bookingArr)
						{
							foreach ($bookingArr as $bodyPartId=>$bodyPartArr)
							{
								foreach ($bodyPartArr as $colorType=>$colorTypeArr)
								{
									foreach ($colorTypeArr as $febId=>$febArr)
									{
										foreach ($febArr as $gsmNo=>$gsmArr)
										{
											foreach ($gsmArr as $diaNo=>$diaArr)
											{
												foreach ($diaArr as $colorId=>$row)
												{
													$sl++;
													$rptPData[$colorId][$sl] = $row;
												}
											}
										}
									}
								}
							}
						}		*/
						//echo "<pre>";
						//print_r($rptPData);

						if($plan_id!="")
						{
							$plan_sql = "select id, plan_id, program_no, color_id, color_prog_qty from ppl_color_wise_break_down where plan_id = $plan_id and status_active=1 and is_deleted=0";
							$plan_data = sql_select($plan_sql);
							$color_prog_data = array();
							foreach ($plan_data as $row)
							{
								$color_plan_data[$row[csf('plan_id')]][$row[csf('color_id')]]['color_prog_qty_total'] += $row[csf('color_prog_qty')];
								$color_plan_data[$row[csf('plan_id')]][$row[csf('color_id')]]['colo_prog_update_id'] = $row[csf('id')];
								$color_prog_data[$row[csf('plan_id')]][$row[csf('program_no')]][$row[csf('color_id')]]['colo_prog_update_id'] = $row[csf('id')];
								$color_prog_data[$row[csf('plan_id')]][$row[csf('program_no')]][$row[csf('color_id')]]['color_prog_qty'] += $row[csf('color_prog_qty')];
							}
						}

						$i = 1;
						$tot_qnty = 0;
						//foreach ($rptPData as $colorId=>$colorArr)
						foreach ($rptData as $colorId=>$row)
						{
							//foreach ($colorArr as $sl=>$row)
							//{
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$row[csf('qnty')] = $row['bookingQty'];
								$tot_qnty += $row[csf('qnty')];
								$row[csf('fabric_color_id')] = $colorId;
								if (in_array($row[csf('fabric_color_id')], $hidden_color_id))
								{
									if ($color_row_id == "")
										$color_row_id = $i;
									else
										$color_row_id .= "," . $i;
								}
								//echo $plan_id."==".$row[csf('fabric_color_id')];

								//$colo_prog_update_id = $color_plan_data[$plan_id][$row[csf('fabric_color_id')]]['colo_prog_update_id'];
								$colo_prog_update_id = $color_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]]['colo_prog_update_id'];
								$color_prog_qty = $color_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]]['color_prog_qty'];
								$color_total_prog_qty = $color_plan_data[$plan_id][$row[csf('fabric_color_id')]]['color_prog_qty_total'];
								$blance = ($row[csf('qnty')]-($color_total_prog_qty));
								$previous_color_prog_qty = ($color_total_prog_qty-$color_prog_qty);
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
									<td width="40" align="center"><? echo $i; ?>
										<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<? echo $row[csf('fabric_color_id')]; ?>"/>
										<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<? echo $color_library[$row[csf('fabric_color_id')]]; ?>"/>
										<input type="hidden" name="colo_prog_update_id[]" id="colo_prog_update_id_<?php echo $i; ?>" value="<? echo  $update_id= ($colo_prog_update_id!="")?$colo_prog_update_id:"0"; ?>"/>
									</td>
									<td width="220">
										<p><? echo $color_library[$row[csf('fabric_color_id')]]; ?></p>
										<input type="hidden" name="text_colorid_[]" id="text_colorid_<? echo $i;?>" value="<? echo $row[csf('fabric_color_id')]; ?>"/>
										<input type="hidden" name="text_color_name_[]" id="text_color_name_<? echo $i;?>" value="<? echo $color_library[$row[csf('fabric_color_id')]]; ?>"/>
									</td>
									<td width="80" align="right">
										<? echo number_format($row[csf('qnty')], 2); ?>
										<input type="hidden" name="hidden_color_allowed_qty[]" id="hidden_color_allowed_qty<? echo $i;?>" value="<? echo number_format($row[csf('qnty')], 2, '.', ''); ?>"/>
									</td>
									<td width="80" align="right">
										<input type="text" name="text_color_prog_qty[]" id="text_color_prog_qty_<? echo $i;?>" value="<? $text_color_prog_qty= ($color_prog_qty>0)?$color_prog_qty:""; echo number_format($text_color_prog_qty, 2, '.', '');?>" style="max-width: 80px; text-align: center;" placeholder="Write" data-update-qty="<?php echo number_format($text_color_prog_qty, 2, '.', ''); ?>" onKeyUp="func_onkeyup_color_qty('<?php echo $i; ?>')" onBlur="func_onkeyup_color_qty('<?php echo $i; ?>')" />
									</td>
									<td width="80" align="right">
									<p><? echo $text_prev_qty = ($previous_color_prog_qty>0)?number_format(($previous_color_prog_qty),2):"0"; ?></p>
										<input type="hidden" name="hidden_color_prev_prog_qty[]" id="hidden_color_prev_prog_qty_<? echo $i;?>" value="<? echo $text_prev_qty = ($previous_color_prog_qty>0)?number_format(($previous_color_prog_qty),2, '.', ''):"0"; ?>"/>
									</td>
									<td align="right"><p><? echo $balanceQty = ($blance>0)?number_format($blance ,2):"0" ; ?></p>
										<input type="hidden" name="txt_individual_color_blqty[]" id="txt_individual_color_blqty<?php echo $i; ?>" value="<? echo $balanceQty = ($blance>0)?number_format($blance ,2, '.', ''):"0" ; ?>"/>
									</td>
								</tr>
								<?
								$i++;
							//}
						}
						?>
						<input type="hidden" name="txt_color_row_id" id="txt_color_row_id"
						value="<?php echo $color_row_id; ?>"/>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="2" align="right"><b>Total</b></th>
							<th align="right"><? echo number_format($tot_qnty, 2); ?></th>
							<th align="right">&nbsp;</th>
							<th align="right">&nbsp;</th>
							<th align="right">&nbsp;</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<div style="width:100%; margin-left:10px; margin-top:5px">
			<div style="width:43%; float:left" align="left">
				<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();"/> Check /
				Uncheck All
			</div>
			<div style="width:57%; float:left" align="left">
				<input type="button" name="close" onClick="fnc_close(<? echo $color_mixing_in_knittingplan_yes;?>);" class="formbutton"
				value="Close" style="width:100px"/>
			</div>
		</div>
	</fieldset>
</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "color_info_popup--")
{
	echo load_html_head_contents("Color Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		$(document).ready(function (e)
		{
			setFilterGrid('tbl_list_search', -1);
			set_all();
		});

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

		function set_all()
		{
			var old = document.getElementById('txt_color_row_id').value;
			if (old != "")
			{
				old = old.split(",");
				for (var i = 0; i < old.length; i++)
				{
					js_set_value(old[i])
				}
			}
		}

		/*
		function js_set_value2(str)
		{
			var id = $('#txt_individual_id' + str).val()
			var name = $('#txt_individual' + str).val();
			var color_balance_qty = $('#txt_individual_color_blqty' + str).val();
			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
			$('#txt_selected_color_bl_qty').val(color_balance_qty);
			//parent.emailwindow.hide();
		}*/

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
					if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++)
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
		}

		function fnc_close(colorMixing)
		{
			var save_string = "";
			var breakOut = true;
			var total_prog_qty = 0;
			var color_name_string = '';
			var color_id_string = '';
			var color_prog_qty_string = '';
			var allowed_qty = 0;
			var colorQtyArr = [];

			$("#tbl_list_search").find('tbody tr').not(":first").each(function ()
			{
				var coloProgUpdateId = $(this).find('input[name="colo_prog_update_id[]"]').val();
				var txtColorId = $(this).find('input[name="text_colorid_[]"]').val();
				var txtColorName = $(this).find('input[name="text_color_name_[]"]').val().trim();
				var txtColorProgQty = $(this).find('input[name="text_color_prog_qty[]"]').val() * 1;
				var hidden_color_allowed_qty = $(this).find('input[name="hidden_color_allowed_qty[]"]').val() * 1;
				var hidden_color_prev_prog_qty = $(this).find('input[name="hidden_color_prev_prog_qty[]"]').val() * 1;
				var txt_individual_color_blqty = $(this).find('input[name="txt_individual_color_blqty[]"]').val() * 1;

				if(txtColorProgQty>0)
				{
					if (save_string == "")
					{
						save_string = txtColorId + "_" + txtColorProgQty+ "_" + coloProgUpdateId ;
						color_name_string = txtColorName;
						color_id_string = txtColorId;
						//color_prog_qty_string = txtColorProgQty;
					}
					else
					{
						save_string += "," + txtColorId + "_" + txtColorProgQty+ "_" + coloProgUpdateId ;
						color_name_string += "," + txtColorName;
						color_id_string += "," + txtColorId;
						//color_prog_qty_string += "," + txtColorProgQty;
					}

					if(txtColorProgQty>0)
					{
						colorQtyArr.push(txtColorProgQty);
					}

					total_prog_qty += txtColorProgQty;

					if(hidden_color_allowed_qty<(hidden_color_prev_prog_qty+txtColorProgQty))
					{
						alert("Program quantity can not be greater than Balance quantity");
						$(this).find('input[name="text_color_prog_qty[]"]').focus();
						return;
					}
				}

			});


			if (total_prog_qty < 1)
			{
				alert("Program quantity zero is not allowed");
				$('#text_color_prog_qty_1').focus();
				return false;
			}

			if(colorMixing!=1)
			{
				if(colorQtyArr.length>1)
				{
					alert('Color Mixing is not allowed');
					return;
				}
			}

			$('#hidden_color_wise_prog_data').val(save_string);
			$('#hidden_total_prog_qty').val(total_prog_qty);
			$('#txt_selected_id').val(color_id_string);
			$('#txt_selected').val(color_name_string);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:630px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:630px; margin-top:10px; margin-left:20px">
				<div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
						<thead>
							<th width="40">SL</th>
							<th width="160">Color</th>
							<th width="80">Qnty</th>
							<th width="80">Prog. Qty</th>
							<th width="80">Prev. Prog. Qty</th>
							<th>Balance</th>
							<input type="hidden" name="txt_selected_id" id="txt_selected_id" value=""/>
							<input type="hidden" name="txt_selected" id="txt_selected" value=""/>
							<input type="hidden" name="txt_selected_color_bl_qty" id="txt_selected_color_bl_qty" value=""/>
							<input type="hidden" name="hidden_color_wise_prog_data" id="hidden_color_wise_prog_data" class="text_boxes" value="">
							<input type="hidden" name="hidden_total_prog_qty" id="hidden_total_prog_qty" class="text_boxes" value="">
						</thead>
					</table>
					<div style="width:600px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="580" class="rpt_table"
						id="tbl_list_search">
						<tbody>
						<?
						$hdnColorId = $hidden_color_id;
						$hidden_color_id = explode(",", $hidden_color_id);
						//$pre_cost_id = explode(",", $pre_cost_id);
						//$pre_cost_id = implode(",", array_unique($pre_cost_id));
						//$color_library = return_library_array("SELECT id, color_name FROM lib_color", "id", "color_name");
						$color_library = return_library_array("SELECT id, color_name FROM lib_color WHERE id IN(".$hdnColorId.")", "id", "color_name");


						if ($dia != "" || $db_type == 0)
						{
							$dia_cond = "b.dia_width LIKE '%".$dia."%'";
						}
						else
							$dia_cond = "b.dia_width IS NULL";

						//$sql = "select b.fabric_color_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond and b.po_break_down_id in ($po_id) and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id";

						$sql = "
							SELECT
								b.fabric_color, sum(b.finish_fabric) as qnty
							FROM
								wo_non_ord_samp_booking_mst a
								INNER JOIN wo_non_ord_samp_booking_dtls b ON a.booking_no = b.booking_no
							WHERE
								a.company_id = ".$companyId."
								AND a.booking_no = '".$booking_no."'
								AND b.fabric_color IN(".$hdnColorId.")
								AND a.item_category = 2
								AND a.status_active=1
								AND a.is_deleted=0
								AND b.fabric_source = 1
								AND b.status_active=1
								AND b.is_deleted=0
							GROUP BY
								b.fabric_color
						";

						if($plan_id!="")
						{
							$plan_sql = "select id, plan_id, program_no, color_id, color_prog_qty from ppl_color_wise_break_down where plan_id = $plan_id and status_active=1 and is_deleted=0";

							$plan_data = sql_select($plan_sql);

							$color_prog_data = array();
							foreach ($plan_data as $row)
							{
								$color_plan_data[$row[csf('plan_id')]][$row[csf('color_id')]]['color_prog_qty_total'] += $row[csf('color_prog_qty')];
								$color_plan_data[$row[csf('plan_id')]][$row[csf('color_id')]]['colo_prog_update_id'] = $row[csf('id')];

								$color_prog_data[$row[csf('plan_id')]][$row[csf('program_no')]][$row[csf('color_id')]]['color_prog_qty'] = $row[csf('color_prog_qty')];
							}
						}

						$result = sql_select($sql);
						$i = 1;
						$tot_qnty = 0;
						foreach ($result as $row)
						{
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$tot_qnty += $row[csf('qnty')];
							$row[csf('fabric_color_id')] = $row[csf('fabric_color')];
							if (in_array($row[csf('fabric_color_id')], $hidden_color_id))
							{
								if ($color_row_id == "")
									$color_row_id = $i;
								else
									$color_row_id .= "," . $i;
							}
							//echo $plan_id."==".$row[csf('fabric_color_id')];

							$colo_prog_update_id = $color_plan_data[$plan_id][$row[csf('fabric_color_id')]]['colo_prog_update_id'];
							$color_prog_qty = $color_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]]['color_prog_qty'];
							$color_total_prog_qty = $color_plan_data[$plan_id][$row[csf('fabric_color_id')]]['color_prog_qty_total'];
							$blance = ($row[csf('qnty')]-($color_total_prog_qty));
							$previous_color_prog_qty = ($color_total_prog_qty-$color_prog_qty);
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
									id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">

								<td width="40" align="center"><? echo $i; ?>
									<input type="hidden" name="txt_individual_id"
									id="txt_individual_id<?php echo $i; ?>"
									value="<? echo $row[csf('fabric_color_id')]; ?>"/>
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>"
									value="<? echo $color_library[$row[csf('fabric_color_id')]]; ?>"/>

									<input type="hidden" name="colo_prog_update_id[]"
									id="colo_prog_update_id_<?php echo $i; ?>"
									value="<? echo  $update_id= ($colo_prog_update_id!="")?$colo_prog_update_id:"0"; ?>"/>
								</td>

								<td width="160">
									<p><? echo $color_library[$row[csf('fabric_color_id')]]; ?></p>

									<input type="hidden" name="text_colorid_[]" id="text_colorid_<? echo $i;?>" value="<? echo $row[csf('fabric_color_id')]; ?>"/>
									<input type="hidden" name="text_color_name_[]" id="text_color_name_<? echo $i;?>" value="<? echo $color_library[$row[csf('fabric_color_id')]]; ?>"/>
								</td>
								<td width="80" align="right">
									<? echo number_format($row[csf('qnty')], 2); ?>
									<input type="hidden" name="hidden_color_allowed_qty[]" id="hidden_color_allowed_qty<? echo $i;?>" value="<? echo number_format($row[csf('qnty')], 2); ?>"/>
								</td>
								<td width="80" align="right">
									<input type="text" name="text_color_prog_qty[]" id="text_color_prog_qty_<? echo $i;?>" value="<? echo  $text_color_prog_qty= ($color_prog_qty>0)?$color_prog_qty:""; ?>" style="max-width: 80px; text-align: center;" placeholder="Write"/>
								</td>
								<td width="80" align="right">
									<p><? echo $text_prev_qty = ($previous_color_prog_qty>0)?number_format(($previous_color_prog_qty),2):"0"; ?></p>
									<input type="hidden" name="hidden_color_prev_prog_qty[]" id="hidden_color_prev_prog_qty_<? echo $i;?>" value="<? echo $text_prev_qty = ($previous_color_prog_qty>0)?number_format(($previous_color_prog_qty),2):"0"; ?>"/>
								</td>
								<td align="right"><p><? echo $balanceQty = ($blance>0)?number_format($blance ,2):"0" ; ?></p>
								<input type="hidden" name="txt_individual_color_blqty[]"
								id="txt_individual_color_blqty<?php echo $i; ?>"
								value="<? echo $balanceQty = ($blance>0)?number_format($blance ,2):"0" ; ?>"/>
								</td>
							</tr>
							<?
							$i++;
						}
						?>
						<input type="hidden" name="txt_color_row_id" id="txt_color_row_id"
						value="<?php echo $color_row_id; ?>"/>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="2" align="right"><b>Total</b></th>
							<th align="right"><? echo number_format($tot_qnty, 2); ?></th>
							<th align="right">&nbsp;</th>
							<th align="right">&nbsp;</th>
							<th align="right">&nbsp;</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<div style="width:100%; margin-left:10px; margin-top:5px">
			<div style="width:43%; float:left" align="left">
				<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();"/> Check /
				Uncheck All
			</div>
			<div style="width:57%; float:left" align="left">
				<input type="button" name="close" onClick="fnc_close(<? echo $color_mixing_in_knittingplan_yes;?>);" class="formbutton"
				value="Close" style="width:100px"/>
			</div>
		</div>
	</fieldset>
</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| machine_info_popup
|--------------------------------------------------------------------------
|
*/
if ($action == "machine_info_popup")
{
	echo load_html_head_contents("Machine Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		function calculate_qnty(tr_id)
		{
			var distribution_qnty = $('#txt_distribution_qnty_' + tr_id).val() * 1;
			if (distribution_qnty > 0)
			{
				$('#search' + tr_id).css('background-color', 'yellow');
			}
			else
			{
				$('#search' + tr_id).css('background-color', '#FFFFCC');
			}
			calculate_total_qnty('txt_distribution_qnty_', 'txt_total_distribution_qnty');
		}

		function calculate_total_qnty(field_id, total_field_id)
		{
			var tot_row = $("#tbl_list_search tbody tr").length - 1;
			var ddd = {dec_type: 2, comma: 0, currency: ''}
			math_operation(total_field_id, field_id, "+", tot_row, ddd);
		}

		function fnc_close()
		{
			var save_string = '';
			var allMachineId = '';
			var allMachineNo = '';
			var tot_capacity = '';
			var tot_distribution_qnty = '';
			var min_date = '';
			var max_date = '';
			var tot_row = $("#tbl_list_search tbody tr").length - 1;
			var allowed_date_qnty_string = '';

			for (var i = 1; i <= tot_row; i++)
			{
				var machineId = $('#txt_individual_id' + i).val();
				var machineNo = $('#txt_individual' + i).val();
				var capacity = $('#txt_capacity_' + i).val();
				var distributionQnty = $('#txt_distribution_qnty_' + i).val();
				var noOfDays = $('#txt_noOfDays_' + i).val();
				var startDate = $('#txt_startDate_' + i).val();
				var endDate = $('#txt_endDate_' + i).val();
				var allowedDateQnty = $('#txt_allowedDateQnty_' + i).val();

				if (distributionQnty * 1 > 0)
				{
					if (save_string == "")
					{
						save_string = machineId + "_" + machineNo + "_" + capacity + "_" + distributionQnty + "_" + noOfDays + "_" + startDate + "_" + endDate;
						allMachineId = machineId;
						allMachineNo = machineNo;
						allowed_date_qnty_string  =  allowedDateQnty;
					}
					else
					{
						save_string += "," + machineId + "_" + machineNo + "_" + capacity + "_" + distributionQnty + "_" + noOfDays + "_" + startDate + "_" + endDate;
						allMachineId += "," + machineId;
						allMachineNo += "," + machineNo;

						allowed_date_qnty_string += "__" + allowedDateQnty;
					}

					if (min_date == '')
					{
						min_date = startDate;
					}

					if (date_compare(min_date, startDate) == false)
					{
						min_date = startDate;
					}

					if (date_compare(min_date, endDate) == false)
					{
						min_date = endDate;
					}

					if (max_date == '')
					{
						max_date = startDate;
					}

					if (date_compare(max_date, startDate) == true)
					{
						max_date = startDate;
					}

					if (date_compare(max_date, endDate) == true)
					{
						max_date = endDate;
					}

					tot_capacity = tot_capacity * 1 + capacity * 1;
					tot_distribution_qnty = tot_distribution_qnty * 1 + distributionQnty * 1;
				}
			}

			$('#hidden_machine_id').val(allMachineId);
			$('#hidden_machine_no').val(allMachineNo);
			$('#save_string').val(save_string);
			$('#hidden_machine_capacity').val(tot_capacity);
			$('#hidden_distribute_qnty').val(tot_distribution_qnty);
			$('#hidden_min_date').val(min_date);
			$('#hidden_max_date').val(max_date);
			$('#hidden_all_allowed_date_qnty').val(allowed_date_qnty_string);
			parent.emailwindow.hide();
		}

		function fn_add_date_field(row_no)
		{
			var distribute_qnty = $('#txt_distribution_qnty_' + row_no).val() * 1;
			if (distribute_qnty == 0 || distribute_qnty < 0)
			{
				alert("Please Insert Distribution Qnty First.");
				$('#txt_startDate_' + row_no).val('');
				$('#txt_distribution_qnty_' + row_no).focus();
				return;
			}

			if ($('#txt_startDate_' + row_no).val() != "")
			{
				var txt_startDate = $('#txt_startDate_' + row_no).val();
				var txt_endDate = $('#txt_endDate_' + row_no).val();
				var machine_id = $('#txt_individual_id' + row_no).val();
				var capacity_qnty = $('#txt_capacity_' + row_no).val();
				var no_of_days = $('#txt_noOfDays_' + row_no).val();

				var data = machine_id + "**" + txt_startDate + "**" + distribute_qnty + "**" + capacity_qnty + "**" + no_of_days + "**" + '<? echo $update_dtls_id; ?>';
				var response = return_global_ajax_value(data, 'date_duplication_check', '', 'planning_info_entry_for_sample_without_order_controller');
				var response = response.split("=");

				var days_req = $('#txt_noOfDays_' + row_no).val();

				days_req = Math.ceil(days_req);
				if (days_req > 0)
				{
					days_req = days_req - 1;
					$("#txt_endDate_" + row_no).val(add_days($('#txt_startDate_' + row_no).val(), days_req));
				}

				var first_date = response[0].split(",");
				first_date = first_date[0];

				if(txt_startDate != first_date)
				{
					alert("Date Overlaping for this machine.");
					$('#txt_startDate_' + row_no).val('');
					$('#txt_endDate_' + row_no).val('');
					return;
				}
				else
				{
					$("#txt_endDate_" + row_no).val(response[1]);
					$("#txt_allowedDateQnty_" + row_no).val( machine_id + "=" + response[0] );  //date, qnty, time , machine no
				}
			}
		}

		function calculate_noOfDays(row_no)
		{
			var distribute_qnty = $('#txt_distribution_qnty_' + row_no).val();
			var machine_capacity = $('#txt_capacity_' + row_no).val();

			var days_req = distribute_qnty * 1 / machine_capacity * 1;
			$('#txt_noOfDays_' + row_no).val(days_req.toFixed(2));

			$('#txt_startDate_' + row_no).val('');
			$('#txt_endDate_' + row_no).val('');
			$('#txt_allowedDateQnty_' + row_no).val('');

        	/*if (distribute_qnty * 1 > 0) {
        		fn_add_date_field(row_no);
        	}
        	else {
        		$('#txt_noOfDays_' + row_no).val('');
        		$('#txt_startDate_' + row_no).val('');
        		$('#txt_endDate_' + row_no).val('');
        	}*/
        }
    </script>
</head>
<body>
	<div style="width:830px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:820px; margin-top:10px; margin-left:5px">
				<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_id" id="hidden_machine_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_no" id="hidden_machine_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_capacity" id="hidden_machine_capacity" class="text_boxes"
				value="">
				<input type="hidden" name="hidden_distribute_qnty" id="hidden_distribute_qnty" class="text_boxes"
				value="">
				<input type="hidden" name="hidden_min_date" id="hidden_min_date" class="text_boxes" value="">
				<input type="hidden" name="hidden_max_date" id="hidden_max_date" class="text_boxes" value="">
				<input type="hidden" name="hidden_all_allowed_date_qnty" id="hidden_all_allowed_date_qnty" class="text_boxes" value="">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="80">Floor</th>
						<th width="60">Machine No</th>
						<th width="60">Dia</th>
						<th width="60">GG</th>
						<th width="80">Group</th>
						<th width="90">Capacity</th>
						<th width="90">Distribution Qnty</th>
						<th width="60">No. Of Days</th>
						<th width="80">Start Date</th>
						<th>End Date</th>
					</thead>
				</table>
				<div style="width:818px; overflow-y:scroll; max-height:220px;" id="buyer_list_view">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table"
					id="tbl_list_search">
					<tbody>
						<?
						$qnty_array = array();
						$save_string = explode(",", $save_string);

						for ($i = 0; $i < count($save_string); $i++)
						{
							$machine_wise_data = explode("_", $save_string[$i]);
							$machine_id = $machine_wise_data[0];
							$capacity = $machine_wise_data[2];
							$distribution_qnty = $machine_wise_data[3];
							$noOfDays = $machine_wise_data[4];
							$startDate = $machine_wise_data[5];
							$endDate = $machine_wise_data[6];

							$qnty_array[$machine_id]['capacity'] = $capacity;
							$qnty_array[$machine_id]['distribution'] = $distribution_qnty;
							$qnty_array[$machine_id]['noOfDays'] = $noOfDays;
							$qnty_array[$machine_id]['startDate'] = $startDate;
							$qnty_array[$machine_id]['endDate'] = $endDate;
						}

						$allowed_date_qnty_string = explode("__", $allowed_date_qnty_string);
						for ($i = 0; $i < count($allowed_date_qnty_string); $i++)
						{
							$machine_date_qnty = explode("=", $allowed_date_qnty_string[$i]);
							$machine_id = $machine_date_qnty[0];
							$qnty_array[$machine_id]['date_qnty'] = $machine_date_qnty[0] ."=". $machine_date_qnty[1];
						}
						/*echo "<pre>";
						print_r($qnty_array);*/

						$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
						$sql = "select id, machine_no, dia_width, gauge, machine_group, prod_capacity, floor_id from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and company_id = $companyId order by seq_no";
						// and dia_width='$txt_machine_dia'company_id=$companyID and
						$result = sql_select($sql);

						$i = 1;
						$tot_capacity = 0;
						$tot_distribution_qnty = 0;
						foreach ($result as $row)
						{
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$capacity = $qnty_array[$row[csf('id')]]['capacity'];
							if ($capacity == "")
							{
								$capacity = $row[csf('prod_capacity')];
							}

							$distribution_qnty = $qnty_array[$row[csf('id')]]['distribution'];

							if ($distribution_qnty > 0)
								$bgcolor = "yellow";
							else
								$bgcolor = $bgcolor;

							$noOfDays = $qnty_array[$row[csf('id')]]['noOfDays'];
							$startDate = $qnty_array[$row[csf('id')]]['startDate'];
							$endDate = $qnty_array[$row[csf('id')]]['endDate'];
							$allowed_date_qnty =  $qnty_array[$row[csf('id')]]['date_qnty'];

							$tot_capacity += $capacity;
							$tot_distribution_qnty += $distribution_qnty;

							?>
							<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
								<td width="40" align="center"><? echo $i; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>"
								value="<? echo $row[csf('id')]; ?>"/>
								<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>"
								value="<? echo $row[csf('machine_no')]; ?>"/>
							</td>
							<td width="80"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
							<td width="60"><p><? echo $row[csf('machine_no')]; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('gauge')]; ?></p></td>
							<td width="80" align="center"><p><? echo $row[csf('machine_group')]; ?></p></td>
							<td width="90" align="center">
								<input type="text" name="txt_capacity[]" id="txt_capacity_<? echo $i; ?>"
								class="text_boxes_numeric" style="width:75px" value="<? echo $capacity; ?>"
								onKeyUp="calculate_total_qnty('txt_capacity_','txt_total_capacity');calculate_noOfDays(<? echo $i; ?>);"/>
							</td>
							<td align="center" width="90">
								<input type="text" name="txt_distribution_qnty[]"
								id="txt_distribution_qnty_<? echo $i; ?>" class="text_boxes_numeric"
								style="width:75px" value="<? echo $distribution_qnty; ?>"
								onKeyUp="calculate_qnty(<? echo $i; ?>);calculate_noOfDays(<? echo $i; ?>);"/>
							</td>
							<td align="center" width="60">
								<input type="text" name="txt_noOfDays[]" id="txt_noOfDays_<? echo $i; ?>"
								class="text_boxes_numeric" style="width:45px" value="<? echo $noOfDays; ?>"
								onKeyUp="calculate_noOfDays(<? echo $i; ?>);" disabled="disabled"/>
							</td>
							<td align="center" width="80">
								<input type="text" name="txt_startDate[]" id="txt_startDate_<? echo $i; ?>"
								class="datepicker" style="width:67px" value="<? echo $startDate; ?>"
								onChange="fn_add_date_field(<? echo $i; ?>);"/>
							</td>
							<td align="center">
								<input type="text" name="txt_endDate[]" id="txt_endDate_<? echo $i; ?>"
								class="datepicker" style="width:67px" value="<? echo $endDate; ?>"
								disabled="disabled"/>

								<input type="hidden" name="txt_allowedDateQnty[]" id="txt_allowedDateQnty_<? echo $i; ?>"
								class="datepicker" style="width:67px" value="<? echo $allowed_date_qnty; ?>" />
							</td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6" align="right"><b>Total</b></th>
						<th align="center"><input type="text" name="txt_total_capacity" id="txt_total_capacity"
							class="text_boxes_numeric" style="width:75px" readonly
							disabled="disabled" value="<? echo $tot_capacity; ?>"/></th>
							<th align="center"><input type="text" name="txt_total_distribution_qnty"
								id="txt_total_distribution_qnty" class="text_boxes_numeric"
								style="width:75px" readonly disabled="disabled"
								value="<? echo $tot_distribution_qnty; ?>"/></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
						</tfoot>
					</table>
				</div>
				<table width="700" id="tbl_close">
					<tr>
						<td align="center">
							<input type="button" name="close" class="formbutton" value="Close" id="main_close"
							onClick="fnc_close();" style="width:100px"/>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| planning_info_details
|--------------------------------------------------------------------------
|
*/
if ($action == "planning_info_details")
{
	$expData = explode('_',$data);
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$sql = "
		SELECT
			id, knitting_source, knitting_party, mst_id, color_range, machine_dia, machine_gg, program_qnty, stitch_length, spandex_stitch_length, draft_ratio, status, program_date, color_id
		FROM
			ppl_planning_info_entry_dtls
		WHERE
			mst_id = ".$expData[0]."
			AND status_active = '1'
			AND is_deleted = '0'
			AND is_sales = 2
		";
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table">
		<thead>
			<th width="90">Knitting Source</th>
			<th width="100">Knitting Company</th>
			<th width="90">Color</th>
			<th width="90">Color Range</th>
			<th width="70">Machine Dia</th>
			<th width="70">Machine GG</th>
			<th width="80">Program Qnty</th>
			<th width="75">Stitch Length</th>
			<th width="80">Span. Stitch Length</th>
			<th width="70">Draft Ratio</th>
			<th width="75">Program Date</th>
			<th width="75">Program No.</th>
			<th>Status</th>
		</thead>
	</table>
	<div style="width:1100px; max-height:140px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1082" class="rpt_table" id="tbl_list_search">
		<?
		$i = 1;
		$result = sql_select($sql);
		foreach ($result as $row)
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";
			if ($row[csf('knitting_source')] == 1)
				$knit_party = $company_arr[$row[csf('knitting_party')]];
			else
				$knit_party = $supllier_arr[$row[csf('knitting_party')]];

			$color_name=explode(",",$row[csf('color_id')]);
			$color_id="";
			foreach ($color_name as $val)
			{
				if($val>0)
					$color_id .= $color_library[$val].",";
			}
			$color=chop($color_id,",");
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="get_php_form_data('<? echo $row[csf('id')]."_".$expData[1]; ?>','populate_data_from_planning_info', 'planning_info_entry_for_sample_without_order_controller' );balance_cal();">
				<td width="90"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
				<td width="100"><p><? echo $knit_party; ?></p></td>
				<td width="90"><p><? echo $color; ?></p></td>
				<td width="90"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
				<td width="70"><p><? echo $row[csf('machine_dia')]; ?></p></td>
				<td width="70"><? echo $row[csf('machine_gg')]; ?></td>
				<td width="80" align="right"><? echo number_format($row[csf('program_qnty')], 2); ?></td>
				<td width="75"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $row[csf('spandex_stitch_length')]; ?>&nbsp;</p></td>
				<td width="70" align="right"><? echo number_format($row[csf('draft_ratio')], 2); ?></td>
				<td width="75" align="right"><? echo change_date_format($row[csf('program_date')]); ?></td>
				<td width="75" align="right"><? echo $row[csf('id')]; ?></td>
				<td><p><? echo $knitting_program_status[$row[csf('status')]]; ?>&nbsp;</p></td>
			</tr>
			<?
			$i++;
		}
		?>
	</table>
</div>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| populate_data_from_planning_info
|--------------------------------------------------------------------------
|
*/
if ($action == "populate_data_from_planning_info")
{
	//echo $data;
	$expData = explode('_', $data);
	$data = $expData[0];
	$dia = $expData[1];

	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$sql = "select seq_no,count_id,feeding_id from ppl_planning_count_feed_dtls where dtls_id=".$data." order by seq_no";
	$data_array = sql_select($sql);
	foreach ($data_array as $row)
	{
		$count_feeding_data_arr[]=$row[csf('seq_no')].'_'.$row[csf('count_id')].'_'.$row[csf('feeding_id')];
	}
	$count_feeding_data_arr_str=implode(',',$count_feeding_data_arr);

	$knit_sys = return_field_value("a.recv_number", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id=".$data." and b.status_active=1 and b.is_deleted=0", "recv_number");

	/*
	$sql ="
		select
			a.id, a.knitting_source, a.knitting_party, a.subcontract_party, a.color_id, a.color_range,a.machine_dia, a.width_dia_type, a.machine_gg, a.fabric_dia, a.program_qnty, a.stitch_length, a.spandex_stitch_length, a.draft_ratio, a.machine_id, a.machine_capacity, a.distribution_qnty, a.status,a.start_date, a.end_date, a.program_date, a.feeder, a.remarks, a.attention, a.co_efficient, a.batch_no, a.save_data, a.no_fo_feeder_data, a.location_id, a.advice, a.collar_cuff_data, a.grey_dia,listagg(b.po_id, ',') within group (order by b.po_id) as po_id
		from
			ppl_planning_info_entry_dtls a,
			ppl_planning_entry_plan_dtls b,
			wo_po_break_down c
		where
			a.id = b.dtls_id
			and b.po_id = c.id
			and a.id = ".$data."
			and a.status_active = 1
			and a.is_deleted = 0
			and b.status_active = 1
			and b.is_deleted = 0
			and c.status_active = 1
			and c. is_deleted = 0
		group by
			a.id, a.knitting_source, a.knitting_party, a.subcontract_party, a.color_id, a.color_range,a.machine_dia, a.width_dia_type, a.machine_gg, a.fabric_dia, a.program_qnty, a.stitch_length, a.spandex_stitch_length, a.draft_ratio, a.machine_id, a.machine_capacity, a.distribution_qnty, a.status,a.start_date, a.end_date, a.program_date, a.feeder, a.remarks, a.attention, a.co_efficient, a.batch_no, a.save_data, a.no_fo_feeder_data, a.location_id, a.advice, a.collar_cuff_data, a.grey_dia
	";
	*/
	$sql ="
		SELECT
			a.id, a.knitting_source, a.knitting_party, a.subcontract_party, a.color_id, a.color_range,a.machine_dia, a.width_dia_type, a.machine_gg, a.fabric_dia, a.program_qnty, a.stitch_length, a.spandex_stitch_length, a.draft_ratio, a.machine_id, a.machine_capacity, a.distribution_qnty, a.status,a.start_date, a.end_date, a.program_date, a.feeder, a.remarks, a.attention, a.fabric_type, a.co_efficient, a.batch_no, a.save_data, a.no_fo_feeder_data, a.location_id, a.advice, a.collar_cuff_data, a.grey_dia,listagg(b.po_id, ',') within group (order by b.po_id) as po_id, listagg(b.yarn_desc, ',') within group (order by b.yarn_desc) as pre_cost_id, b.company_id, b.booking_no
		FROM
			ppl_planning_info_entry_dtls a
			INNER JOIN ppl_planning_entry_plan_dtls b ON a.id = b.dtls_id
		where
			a.id = ".$data."
			AND a.status_active = 1
			AND a.is_deleted = 0
			AND a.is_sales = 2
			AND b.status_active = 1
			AND b.is_deleted = 0
			AND b.is_sales = 2
		GROUP BY
			a.id, a.knitting_source, a.knitting_party, a.subcontract_party, a.color_id, a.color_range,a.machine_dia, a.width_dia_type, a.machine_gg, a.fabric_dia, a.program_qnty, a.stitch_length, a.spandex_stitch_length, a.draft_ratio, a.machine_id, a.machine_capacity, a.distribution_qnty, a.status,a.start_date, a.end_date, a.program_date, a.feeder, a.remarks, a.attention, a.fabric_type, a.co_efficient, a.batch_no, a.save_data, a.no_fo_feeder_data, a.location_id, a.advice, a.collar_cuff_data, a.grey_dia, b.company_id, b.booking_no
	";
	//echo $sql;
	$data_array = sql_select($sql);
	foreach ($data_array as $row)
	{
		echo "document.getElementById('upd_plan_po_ids').value 				= '" . $row[csf("po_id")] . "';\n";
		echo "document.getElementById('cbo_knitting_source').value 			= '" . $row[csf("knitting_source")] . "';\n";
		echo "document.getElementById('hidden_knitting_source').value 		= '" . $row[csf("knitting_source")] . "';\n";
		echo "document.getElementById('hidden_knit_sys').value 				= '" . $knit_sys . "';\n";
		echo "load_drop_down('planning_info_entry_for_sample_without_order_controller', " . $row[csf("knitting_source")] . "+'**'+" . $row[csf("knitting_party")] . "+'**1', 'load_drop_down_knitting_party','knitting_party');\n";

		$color = '';
		$color_id = explode(",", $row[csf("color_id")]);
		foreach ($color_id as $val)
		{
			if ($color == "")
				$color = $color_library[$val];
			else
				$color .= "," . $color_library[$val];
		}
		echo "document.getElementById('knitting_party').value 				= '" . $row[csf("knitting_party")] . "';\n";
		echo "load_drop_down('planning_info_entry_for_sample_without_order_controller', " .$row[csf("knitting_party")] . ", 'load_drop_down_location','location_td');\n";
		echo "document.getElementById('cbo_subcontract_party').value        = '" . $row[csf("subcontract_party")] . "';\n";
		echo "document.getElementById('txt_color').value 					= '" . $color . "';\n";
		echo "document.getElementById('hidden_color_id').value 				= '" . $row[csf("color_id")] . "';\n";
		echo "document.getElementById('cbo_color_range').value 				= '" . $row[csf("color_range")] . "';\n";
		echo "document.getElementById('txt_machine_dia').value 				= '" . $row[csf("machine_dia")] . "';\n";
		echo "document.getElementById('cbo_dia_width_type').value 			= '" . $row[csf("width_dia_type")] . "';\n";
		echo "document.getElementById('txt_machine_gg').value 				= '" . $row[csf("machine_gg")] . "';\n";
		echo "document.getElementById('txt_fabric_dia').value 				= '" . $row[csf("fabric_dia")] . "';\n";
		echo "document.getElementById('txt_program_qnty').value 			= '" . $row[csf("program_qnty")] . "';\n";
		echo "document.getElementById('hidden_color_wise_total').value 		= '" . $row[csf("program_qnty")] . "';\n";
		echo "document.getElementById('txt_stitch_length').value 			= '" . $row[csf("stitch_length")] . "';\n";
		echo "document.getElementById('txt_spandex_stitch_length').value 	= '" . $row[csf("spandex_stitch_length")] . "';\n";
		echo "document.getElementById('txt_draft_ratio').value 				= '" . $row[csf("draft_ratio")] . "';\n";
		echo "document.getElementById('txt_attention').value 				= '" . $row[csf("attention")] . "';\n";
		echo "document.getElementById('txt_fabric_type').value 				= '" . $row[csf("fabric_type")] . "';\n";
		echo "document.getElementById('txt_co_efficient').value 			= '" . $row[csf("co_efficient")] . "';\n";
		echo "document.getElementById('txt_batch_no').value 				= '" . $row[csf("batch_no")] . "';\n";
		echo "active_inactive();\n";
		echo "document.getElementById('machine_id').value 					= '" . $row[csf("machine_id")] . "';\n";
		$machine_no = '';
		$machine_id = explode(",", $row[csf("machine_id")]);
		foreach ($machine_id as $val)
		{
			if ($machine_no == '')
				$machine_no = $machine_arr[$val];
			else
				$machine_no .= "," . $machine_arr[$val];
		}
		echo "document.getElementById('txt_machine_no').value 				= '" . $machine_no . "';\n";
		echo "document.getElementById('txt_machine_capacity').value 		= '" . $row[csf("machine_capacity")] . "';\n";
		echo "document.getElementById('txt_distribution_qnty').value 		= '" . $row[csf("distribution_qnty")] . "';\n";
		echo "document.getElementById('cbo_knitting_status').value 			= '" . $row[csf("status")] . "';\n";
		echo "document.getElementById('txt_start_date').value 				= '" . change_date_format($row[csf("start_date")]) . "';\n";
		echo "document.getElementById('txt_end_date').value 				= '" . change_date_format($row[csf("end_date")]) . "';\n";
		echo "document.getElementById('txt_program_date').value 			= '" . change_date_format($row[csf("program_date")]) . "';\n";
		echo "document.getElementById('cbo_feeder').value 					= '" . $row[csf("feeder")] . "';\n";
		echo "document.getElementById('txt_remarks').value 					= '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('save_data').value 					= '" . $row[csf("save_data")] . "';\n";
		echo "document.getElementById('hidden_no_of_feeder_data').value 	= '" . $row[csf("no_fo_feeder_data")] . "';\n";
		echo "document.getElementById('hidden_collarCuff_data').value 		= '" . $row[csf("collar_cuff_data")] . "';\n";
		echo "document.getElementById('cbo_location_name').value 			= '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('hidden_bodypartID_data').value 		= '" . $row[csf("collar_cuff_data")] . "';\n";

		if($db_type ==0)
		{
			$date_wise_breakdown_cond = " and date_wise_breakdown =''";
		}
		else
		{
			$date_wise_breakdown_cond = " and date_wise_breakdown is not null";
		}

		$machine_sql = sql_select("SELECT machine_id, date_wise_breakdown FROM ppl_planning_info_machine_dtls WHERE status_active = 1 AND dtls_id = ".$data." ".$date_wise_breakdown_cond."");
		if(!empty($machine_sql))
		{
			$machine_data_string = "";
			foreach ($machine_sql as  $val)
			{
				$machine_data_string .= $val[csf("machine_id")]."=".$val[csf("date_wise_breakdown")]."__";
			}
			$machine_data_string = chop($machine_data_string,"__");
			echo "document.getElementById('allowed_date_qnty_string').value = '" . $machine_data_string . "';\n";
		}

		$advice = str_replace("\n", ";", $row[csf("advice")]);
		echo "document.getElementById('hidden_advice_data').value 			= '" .$advice. "';\n";
		echo "document.getElementById('hidden_count_feeding_data').value	= '" .$count_feeding_data_arr_str. "';\n";
		echo "document.getElementById('txt_grey_dia').value					= '" . $row[csf("grey_dia")] . "';\n";
		echo "document.getElementById('update_dtls_id').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('txt_program_no').value 				= '" . $row[csf("id")] . "';\n";
		echo "days_req();\n";
		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_program_entry',1);\n";

		$booking_no = $row[csf("booking_no")];
		$company_id = $row[csf("company_id")];

		$expPoId = explode(',', $row[csf("po_id")]);
		$poIdArr = array();
		for($zs = 0; $zs < count($expPoId); $zs ++)
		{
			$poIdArr[$expPoId[$zs]] = $expPoId[$zs];
		}
		$po_id = implode(',', $poIdArr);

		$expPrecostId = explode(',', $row[csf("pre_cost_id")]);
		$precostIdArr = array();
		for($zss = 0; $zss < count($expPrecostId); $zss ++)
		{
			$precostIdArr[$expPrecostId[$zss]] = $expPrecostId[$zss];
		}
		$pre_cost_id = implode(',', $precostIdArr);
	}

	//for booking qty
	if ($dia!= "" || $db_type == 0)
	{
		$dia_cond = "b.dia_width like '%$dia%'";
	}
	else
	{
		$dia_cond = "b.dia_width is null";
	}

	$sqlBookingQty = "select b.fabric_color_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=".$company_id." and a.item_category=2 and a.booking_no='".$booking_no."' and ".$dia_cond." and b.po_break_down_id in (".$po_id.") and b.pre_cost_fabric_cost_dtls_id in (".$pre_cost_id.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id";
	$dataBookingQty = sql_select($sqlBookingQty);
	$bookingQtyArr = array();
	foreach($dataBookingQty as $zasu)
	{
		$bookingQtyArr[$zasu[csf("fabric_color_id")]] = $zasu[csf("qnty")];
	}

	//for color
	$sql_color_prog = "SELECT id, plan_id, program_no, color_id, color_prog_qty FROM ppl_color_wise_break_down WHERE program_no = ".$data." AND status_active = 1 AND is_deleted = 0";
	$color_prog_data = sql_select($sql_color_prog);
	if(count($color_prog_data>0))
	{
		$saveString = "";
		$totalProgQty = 0;

		foreach ($color_prog_data as $colorRow)
		{
			if($saveString=="")
			{
				$saveString =  $colorRow[csf("color_id")] . "_" . $colorRow[csf("color_prog_qty")]. "_" . $colorRow[csf("id")]. "_" . $bookingQtyArr[$colorRow[csf("color_id")]];
			}
			else
			{
				$saveString .= "," . $colorRow[csf("color_id")] . "_" . $colorRow[csf("color_prog_qty")]. "_" . $colorRow[csf("id")]. "_" . $bookingQtyArr[$colorRow[csf("color_id")]];
			}

			$totalProgQty += $colorRow[csf("color_prog_qty")];
		}
		echo "document.getElementById('hidden_color_wise_prog_data').value 	= '" . $saveString . "';\n";
		//echo "document.getElementById('hidden_total_prog_qty').value 	= '" . $totalProgQty . "';\n";
	}

	$foundYarnIssue = sql_select("select a.id from  ppl_yarn_requisition_entry a,inv_transaction b where a.requisition_no=b.requisition_no and a.knit_id=$data and b.item_category=1 and b.transaction_type=2");
	if( !empty($foundYarnIssue))
	{
		echo "$('#cbo_knitting_source').attr('disabled','disabled');\n";
		echo "$('#cbo_knitting_party').attr('disabled','disabled');\n";
	}
	exit();
}

/*
|--------------------------------------------------------------------------
| update_program
|--------------------------------------------------------------------------
|
*/
if ($action == "update_program")
{
	$con = connect();
	if ($db_type == 0)
	{
		mysql_query("BEGIN");
	}

	//checking production qty
	$knit_qty = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id=".$program_id." and b.status_active=1 and b.is_deleted=0", "knitting_qnty");

	if ($knit_qty > $prog_qty)
	{
		echo "20";
		disconnect($con);
		exit();
	}

	//for ppl_planning_entry_plan_dtls tbl
	$sql = "SELECT a.program_qnty AS PROGRAM_QNTY, b.id AS ID, b.po_id AS PO_ID, b.program_qnty AS PO_QTY from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id = b.dtls_id and a.id=".$program_id." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_rlst = sql_select($sql);
	foreach($sql_rlst as $row)
	{
		$program_qty = number_format((($row['PO_QTY']*$prog_qty)/$row['PROGRAM_QNTY']), 2, '.', '');

		$plan_dtls_id_arr[] = $row['ID'];
		$data_plan_dtls_tbl[$row['ID']] = explode("*", ($program_qty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
	}

	//for ppl_color_wise_break_down tbl
	$exp_color_data = explode(',', $color_data);
	foreach($exp_color_data as $key=>$val)
	{
		$exp_data = array();
		$exp_data = explode('_', $val);
		//save_string = txtColorId + "_" + txtColorProgQty+ "_" + coloProgUpdateId;
		$color_breakdown_id_arr[] = $exp_data[2];
		$data_color_breakdown_tbl[$exp_data[2]] = explode("*", ($exp_data[1]."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
	}

	//for ppl_planning_info_entry_dtls tbl
	$field_dtls_tbl = "program_qnty*updated_by*update_date";
	$data_dtls_tbl = $prog_qty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
	$rslt_dtls_tbl = sql_update("ppl_planning_info_entry_dtls", $field_dtls_tbl, $data_dtls_tbl, 'id', $program_id, 0);

	//for ppl_planning_entry_plan_dtls tbl
	$field_plan_dtls_tbl = "program_qnty*updated_by*update_date";
	$rslt_plan_dtls_tbl = execute_query(bulk_update_sql_statement("ppl_planning_entry_plan_dtls", 'id', $field_plan_dtls_tbl, $data_plan_dtls_tbl, $plan_dtls_id_arr));

	//for ppl_color_wise_break_down tbl
	$field_color_breakdown_tbl = "color_prog_qty*updated_by*update_date";
	$rslt_color_breakdown_tbl = execute_query(bulk_update_sql_statement("ppl_color_wise_break_down", 'id', $field_color_breakdown_tbl, $data_color_breakdown_tbl, $color_breakdown_id_arr));

	if ($db_type == 0)
	{
		if ($rslt_dtls_tbl && $rslt_plan_dtls_tbl && $rslt_color_breakdown_tbl)
		{
			mysql_query("COMMIT");
			echo "1";
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "6";
		}
	}
	else if ($db_type == 2 || $db_type == 1)
	{
		if ($rslt_dtls_tbl && $rslt_plan_dtls_tbl && $rslt_color_breakdown_tbl)
		{
			oci_commit($con);
			echo "1";
		}
		else
		{
			oci_rollback($con);
			echo "6";
		}
	}
	disconnect($con);
	die;
}

/*
|--------------------------------------------------------------------------
| delete_program
|--------------------------------------------------------------------------
|
*/
if ($action == "delete_program")
{
	$con = connect();
	if ($db_type == 0)
	{
		mysql_query("BEGIN");
	}

	//for ppl_planning_info_entry_dtls tbl
	$rID = execute_query("update ppl_planning_info_entry_dtls set status_active=0, is_deleted=1, updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where id in(".$program_id.")", 0);
	if ($rID)
		$flag = 1;
	else
		$flag = 0;

	//for ppl_planning_entry_plan_dtls tbl
	$rID2 = execute_query("update ppl_planning_entry_plan_dtls set status_active=0, is_deleted=1, updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where dtls_id in(".$program_id.")", 0);
	if ($flag == 1)
	{
		if ($rID2)
			$flag = 1;
		else
			$flag = 0;
	}

	//for ppl_color_wise_break_down tbl
	$rID3 = execute_query("update ppl_color_wise_break_down set status_active=0, is_deleted=1, updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where program_no in(".$program_id.")", 0);
	if ($flag == 1)
	{
		if ($rID3)
			$flag = 1;
		else
			$flag = 0;
	}

	//for ppl_planning_info_machine_dtls tbl
	$delete = execute_query("delete from ppl_planning_info_machine_dtls where dtls_id in(".$program_id.")", 0);
	if ($flag == 1)
	{
		if ($delete)
			$flag = 1;
		else
			$flag = 0;
	}

	//for ppl_entry_machine_datewise tbl
	$delete_datewise = execute_query("delete from ppl_entry_machine_datewise where dtls_id in(".$program_id.")", 0);
	if ($flag == 1)
	{
		if ($delete_datewise)
			$flag = 1;
		else
			$flag = 0;
	}

	//for ppl_planning_feeder_dtls tbl
	$delete_feeder = execute_query("delete from ppl_planning_feeder_dtls where dtls_id in(".$program_id.")", 1);
	if ($flag == 1)
	{
		if ($delete_feeder)
			$flag = 1;
		else
			$flag = 0;
	}

	if ($db_type == 0)
	{
		if ($flag == 1)
		{
			mysql_query("COMMIT");
			echo "2**0";
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "7**0**1";
		}
	}
	else if ($db_type == 2 || $db_type == 1)
	{
		if ($flag == 1)
		{
			oci_commit($con);
			echo "2**0";
		}
		else
		{
			oci_rollback($con);
			echo "7**0**1";
		}
	}
	disconnect($con);
	die;
}

/*
|--------------------------------------------------------------------------
| program_qty_popup
|--------------------------------------------------------------------------
|
*/
if ($action == "program_qty_popup")
{
	echo load_html_head_contents("Color Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		$(document).ready(function (e){
			setFilterGrid('tbl_list_search', -1);
			set_all();
		});

		function func_onKeyUp_qty()
		{
			var total_prog_qty = 0;
			var production_qty = $('#hdn_production_qty').val();
			$("#tbl_list_search").find('tbody tr').not(":first").each(function ()
			{
				var txtColorProgQty = ($(this).find('input[name="text_color_prog_qty[]"]').val()*1).toFixed(2);
				total_prog_qty = (total_prog_qty*1)+(txtColorProgQty*1);
			});

			$('#hdn_total_prog_qty').val(total_prog_qty);
		}

		function func_close()
		{
			var save_string = "";
			var production_qty = $('#hdn_production_qty').val();
			var total_prog_qty = $('#hdn_total_prog_qty').val();

			if (total_prog_qty < 1)
			{
				alert("Program quantity zero is not allowed");
				$('#text_color_prog_qty_1').focus();
				return false;
			}

			if((total_prog_qty*1) < (production_qty*1))
			{
				alert("Program quantity can't be less than production quantity");
				return false;
			}

			$("#tbl_list_search").find('tbody tr').not(":first").each(function ()
			{
				var coloProgUpdateId = $(this).find('input[name="hdn_update_id[]"]').val();
				var txtColorId = $(this).find('input[name="hdn_color_id[]"]').val();
				var txtColorProgQty = ($(this).find('input[name="text_color_prog_qty[]"]').val()*1).toFixed(2);

				if (save_string == "")
				{
					save_string = txtColorId + "_" + txtColorProgQty+ "_" + coloProgUpdateId;
				}
				else
				{
					save_string += "," + txtColorId + "_" + txtColorProgQty+ "_" + coloProgUpdateId;
				}
			});

			$('#hdn_color_wise_prog_data').val(save_string);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:630px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:350px; margin-top:10px; margin-left:20px">
				<div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="340" class="rpt_table">
						<thead>
							<th width="40">SL</th>
							<th width="180">Color</th>
							<th>Program Qty</th>
							<input type="hidden" name="txt_selected_id" id="txt_selected_id" value=""/>
							<input type="hidden" name="txt_selected" id="txt_selected" value=""/>
							<input type="hidden" name="txt_selected_color_bl_qty" id="txt_selected_color_bl_qty" value=""/>
							<input type="hidden" name="hidden_color_wise_prog_data" id="hidden_color_wise_prog_data" class="text_boxes" value="">
							<input type="hidden" name="hidden_total_prog_qty" id="hidden_total_prog_qty" class="text_boxes" value="">
						</thead>
					</table>
					<div style="width:340px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="320" class="rpt_table"
						id="tbl_list_search">
						<tbody>
						<?
						$sql = "SELECT id AS ID, plan_id AS PLAN_ID, program_no AS PROGRAM_NO, color_id AS COLOR_ID, color_prog_qty AS COLOR_PROG_QTY FROM ppl_color_wise_break_down WHERE program_no = ".$progNo." and status_active=1 and is_deleted=0";
						$sql_rslt = sql_select($sql);
						$color_id_arr = array();
						$data_arr = array();
						foreach ($sql_rslt as $row)
						{
							$color_id_arr[$row['COLOR_ID']] = $row['COLOR_ID'];
							$data_arr[$row['COLOR_ID']]['id'] = $row['ID'];
							$data_arr[$row['COLOR_ID']]['qty'] = $row['COLOR_PROG_QTY'];
						}

						$knit_qnty_array = return_library_array("SELECT a.booking_id, sum(b.grey_receive_qnty) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 and a.booking_id = ".$progNo." group by a.booking_id", "booking_id", "knitting_qnty");

						//for color details
						$color_library = return_library_array("SELECT id, color_name FROM lib_color WHERE id IN(".implode(',', $color_id_arr).")", "id", "color_name");
						$i = 0;
						foreach($data_arr as $colorId=>$row)
						{
							$i++;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="40" align="center"><? echo $i; ?></td>
								<td width="180">
									<p><? echo $color_library[$colorId]; ?></p>
									<input type="hidden" name="hdn_color_id[]" id="hdn_color_id_<? echo $i;?>" value="<? echo $colorId; ?>" readonly />
									<input type="hidden" name="hdn_update_id[]" id="hdn_update_id_<? echo $i;?>" value="<? echo $row['id']; ?>" readonly />
								</td>
								<td align="right">
									<input type="text" class="text_boxes_numeric" name="text_color_prog_qty[]" id="text_color_prog_qty_<? echo $i;?>" value="<? echo  number_format($row['qty'], 2, '.', ''); ?>" style="max-width: 88px; text-align: right;" placeholder="Write" onKeyUp="func_onKeyUp_qty();" />
								</td>
							</tr>
							<?
							$tot_qnty += number_format($row['qty'], 2, '.', '');
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="2" align="right"><b>Total</b></th>
							<th align="right">
                            <input type="text" name="hdn_total_prog_qty" id="hdn_total_prog_qty" value="<? echo number_format($tot_qnty, 2); ?>" class="text_boxes_numeric" style="max-width: 88px; text-align: right;" readonly />
                            <input type="hidden" name="hdn_production_qty" id="hdn_production_qty" value="<? echo number_format($knit_qnty_array[$progNo], 2, '.', ''); ?>"/>
                            <input type="hidden" name="hdn_color_wise_prog_data" id="hdn_color_wise_prog_data" value="" readonly />
                            </th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<div style="width:100%; margin-left:10px; margin-top:5px">
			<div style="width:100%;" align="center">
				<input type="button" name="close" onClick="func_close();" class="formbutton"
				value="Close" style="width:100px"/>
			</div>
		</div>
	</fieldset>
</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}


//----------old-------------

if ($action == "order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$buyerID = str_replace("'", "", $buyerID);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++) {
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str) {

			if (str != "") str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			}
			else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1]) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_order_id').val(id);
			$('#hide_order_no').val(name);
		}


		function fn_change_caption(str)
		{
			if(str==1)
			{
				$('#from_date_html').html('');
				$('#from_date_html').html('Shipment From Date');
				$('#to_date_html').html('');
				$('#to_date_html').html('Shipment To Date');
			}
			else if(str==2)
			{
				$('#from_date_html').html('');
				$('#from_date_html').html('TNA Start From Date');
				$('#to_date_html').html('');
				$('#to_date_html').html('TNA Start To Date');
			}
			else if(str==3)
			{
				$('#from_date_html').html('');
				$('#from_date_html').html('TNA Finish From Date');
				$('#to_date_html').html('');
				$('#to_date_html').html('TNA Finish To Date');
			}
			else
			{
				$('#from_date_html').html('');
				$('#from_date_html').html('Shipment From Date');
				$('#to_date_html').html('');
				$('#to_date_html').html('Shipment To Date');

			}
		}

	</script>

</head>

<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:980px;">
				<table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
				class="rpt_table" id="tbl_list">
				<thead>
					<th>Buyer</th>
					<th>Search By</th>
					<th id="search_by_td_up" width="170">Please Enter Order No</th>

					<th width="90" >Date Category</th>
					<th width="70"  id="from_date_html">Shipment From Date</th>
					<th id="to_date_html">Shipment To Date</th>

					<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
					<input type="hidden" name="hide_order_no" id="hide_order_no" value=""/>
					<input type="hidden" name="hide_order_id" id="hide_order_id" value=""/>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<?
							echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", $buyerID, "", 0);
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Order No", 2 => "Style Ref", 3 => "Job No", 4 => "Internal Ref", 5 => "File No");
							$dd = "change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>

						<td align="center">
							<?
							$search_type=array(1=>'Shipment Date',2=>'Knit TNA Start Date',3=>'Knit TNA Finish');
							echo create_drop_down( "cbo_date_type", 90, $search_type, "",0, "-- Select --", $selected, "fn_change_caption(this.value)" );
							?>
						</td>
						<td align="center"><input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px"  value="" placeholder="From Date" /></td>
						<td align="center"><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  style="width:55px"  value="" placeholder="To Date" /></td>

						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show"
							onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_date_type').value, 'create_order_no_search_list_view', 'search_div', 'planning_info_entry_for_sample_without_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
							style="width:100px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="7" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</tbody>
			</table>
			<div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_order_no_search_list_view")
{
	$data = explode('**', $data);
	$company_id = $data[0];

	if ($data[1] == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] >0 ) $buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")"; else $buyer_id_cond = "";
		} else {
			$buyer_id_cond = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_name=$data[1]";
	}

	$search_by = $data[2];
	$search_string = "%" . trim($data[3]) . "%";

	if ($search_by == 1)
		$search_field = "b.po_number";
	else if ($search_by == 2)
		$search_field = "a.style_ref_no";
	else if ($search_by == 4)
		$search_field = "b.grouping";
	else if ($search_by == 5)
		$search_field = "b.file_no";
	else
		$search_field = "a.job_no";

	if (trim($data[3]) != "") {
		$search_field_cond = " and $search_field like '$search_string'";
	} else {
		$search_field_cond = "";
	}


	$start_date = trim($data[4]);
	$end_date = trim($data[5]);
	$cbo_date_category = str_replace("'", "", trim($data[6]));

	if($cbo_date_category==1)
	{
		if ($start_date != "" && $end_date != "") {
			if ($db_type == 0) {
				$date_cond = "and b.pub_shipment_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-") . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'";
			} else {
				$date_cond = "and b.pub_shipment_date between '" . change_date_format($start_date, '', '', 1) . "' and '" . change_date_format($end_date, '', '', 1) . "'";
			}
		} else {
			$date_cond = "";
		}
	}
	else if($cbo_date_category==2)
	{
		if ($start_date != "" && $end_date != "") {
			if ($db_type == 0) {
				$date_cond2 = "and c.task_start_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-") . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'";
			} else {
				$date_cond2 = "and c.task_start_date between '" . change_date_format($start_date, '', '', 1) . "' and '" . change_date_format($end_date, '', '', 1) . "'";
			}

			$tnaTaskNameCond = "and c.task_number=60";
		} else {
			$date_cond2 = "";
			$tnaTaskNameCond = "";
		}
	}
	else if($cbo_date_category==3)
	{
		if ($start_date != "" && $end_date != "") {
			if ($db_type == 0) {
				$date_cond2 = "and c.task_finish_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-") . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'";
			} else {
				$date_cond2 = "and c.task_finish_date between '" . change_date_format($start_date, '', '', 1) . "' and '" . change_date_format($end_date, '', '', 1) . "'";
			}
			$tnaTaskNameCond = "and c.task_number=60";
		} else {
			$date_cond2 = "";
			$tnaTaskNameCond = "";
		}
	}



	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = "";//defined Later

	$arr = array(0 => $company_arr, 1 => $buyer_arr);

	//$sql = "select b.id, $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b,tna_process_mst c where a.job_no=b.job_no_mst and b.id=c.po_number_id and a.company_name=$company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $search_field_cond $buyer_id_cond $date_cond $tnaTaskNameCond order by b.id, b.pub_shipment_date";

	$sql = "select b.id, to_char(a.insert_date,'YYYY') as year, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b
		left join tna_process_mst c on b.id=c.po_number_id and c.status_active=1 and c.is_deleted=0 $date_cond2 $tnaTaskNameCond where a.job_no=b.job_no_mst and a.company_name=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Internal Ref, File No, Shipment Date", "70,70,50,60,130,130,90,90", "860", "220", 0, $sql, "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,grouping,file_no,pub_shipment_date", "", '', '0,0,0,0,0,0,0,0,3', '', 1);

	exit();
}

if ($action == "prog_qnty_popup")
{
	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$current_date = date("d-m-Y");

	$start_date = trim($start_date);
	$end_date = trim($end_date);
	$dataArray = sql_select("select id, machine_dia, machine_gg, fabric_dia, stitch_length from fabric_mapping where mst_id=$determination_id and status_active=1 and is_deleted=0");

	$color_mixing_in_knittingplan = return_field_value("color_mixing_in_knitting_plan", "variable_settings_production", "company_name=$companyID and variable_list=53");

	if($color_mixing_in_knittingplan==1)
	{
		$color_mixing_in_knittingplan_yes = 1;
	}else{
		$color_mixing_in_knittingplan_yes = 0;
	}
	?>
	<script>
		var permission = '<? echo $permission; ?>';
		var color_mixing_in_knittingplan_yes = '<? echo $color_mixing_in_knittingplan_yes; ?>';

		function openpage_machine() {

			var save_string = $('#save_data').val();
			var txt_machine_dia = $('#txt_machine_dia').val();
			var update_dtls_id = $('#update_dtls_id').val();
			var companyID = $('#cbo_knitting_party').val();  <? //echo $companyID; ?>
			var allowed_date_qnty_string = $('#allowed_date_qnty_string').val();
			var page_link = 'planning_info_entry_for_sample_without_order_controller.php?action=machine_info_popup&save_string=' + save_string + '&companyID=' + companyID + '&txt_machine_dia=' + txt_machine_dia + '&update_dtls_id=' + update_dtls_id + '&allowed_date_qnty_string=' +allowed_date_qnty_string;
			var title = 'Machine Info';
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=300px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var hidden_machine_no = this.contentDoc.getElementById("hidden_machine_no").value;
				var hidden_machine_id = this.contentDoc.getElementById("hidden_machine_id").value;
				var save_string = this.contentDoc.getElementById("save_string").value;
				var hidden_machine_capacity = this.contentDoc.getElementById("hidden_machine_capacity").value;
				var hidden_distribute_qnty = this.contentDoc.getElementById("hidden_distribute_qnty").value;
				var hidden_min_date = this.contentDoc.getElementById("hidden_min_date").value;
				var hidden_max_date = this.contentDoc.getElementById("hidden_max_date").value;
				var allowed_date_qnty_string = this.contentDoc.getElementById("hidden_all_allowed_date_qnty").value;

				$('#txt_machine_no').val(hidden_machine_no);
				$('#machine_id').val(hidden_machine_id);
				$('#save_data').val(save_string);
				$('#txt_machine_capacity').val(hidden_machine_capacity);
				$('#txt_distribution_qnty').val(hidden_distribute_qnty);
				$('#txt_start_date').val(hidden_min_date);
				$('#txt_end_date').val(hidden_max_date);
				$('#allowed_date_qnty_string').val(allowed_date_qnty_string);

                //var days_req=hidden_distribute_qnty*1/hidden_machine_capacity*1;
                //$('#txt_days_req').val(days_req.toFixed(2));
                days_req();
            }
        }

        function days_req() {
        	txt_start_date = $('#txt_start_date').val();
        	txt_end_date = $('#txt_end_date').val();

        	if (txt_start_date != "" && txt_end_date != "") {
        		var days_req = date_diff('d', txt_start_date, txt_end_date);
        		$('#txt_days_req').val(days_req + 1);
        	}
        	else {
        		$('#txt_days_req').val('');
        	}
        }

        function openpage_color() {
        	var hidden_color_id = $('#hidden_color_id').val();
        	var prog_no = $('#update_dtls_id').val();
        	var save_color_wise_prog_data = $('#hidden_color_wise_prog_data').val();

        	var page_link = "planning_info_entry_for_sample_without_order_controller.php?action=color_info_popup&companyID="+<? echo $companyID; ?>+"&po_id="+"<? echo $po_id; ?>"+"&pre_cost_id="+"<? echo $pre_cost_id; ?>"+"&booking_no="+"<? echo trim($booking_no); ?>"+"&dia="+"<?php echo $dia; ?>"+"&hidden_color_id="+hidden_color_id +"&save_color_wise_prog_data="+save_color_wise_prog_data +"&color_mixing_in_knittingplan_yes="+color_mixing_in_knittingplan_yes+"&plan_id="+"<?php echo $plan_id; ?>"+ '&prog_no=' + prog_no;
        	var title = 'Color Info';

        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title,"width=670px,height=300px,center=1,resize=1,scrolling=0", '../');

        	emailwindow.onclose = function ()
        	{
        		var theform = this.contentDoc.forms[0];
        		var hidden_color_no = this.contentDoc.getElementById("txt_selected").value;
        		var hidden_color_id = this.contentDoc.getElementById("txt_selected_id").value;
        		var hidden_color_prog_blance = this.contentDoc.getElementById("txt_selected_color_bl_qty").value;
        		var hidden_color_wise_prog_data = this.contentDoc.getElementById("hidden_color_wise_prog_data").value;
        		var hidden_total_prog_qty = this.contentDoc.getElementById("hidden_total_prog_qty").value;

        		$('#txt_color').val(hidden_color_no);
        		$('#hidden_color_id').val(hidden_color_id);
        		$('#txt_program_qnty').val(hidden_color_prog_blance);
        		$('#hidden_color_wise_prog_data').val(hidden_color_wise_prog_data);
        		$('#txt_program_qnty').val(hidden_total_prog_qty);
        		$('#hidden_color_wise_total').val(hidden_total_prog_qty);

        	}
        }

        function fnc_program_entry(operation)
        {


			if(knit_source == 1){
				if (form_validation('cbo_knitting_party*txt_color*cbo_color_range*txt_machine_dia*txt_machine_gg*txt_program_qnty*txt_stitch_length*cbo_location_name*cbo_dia_width_type','cbo_knitting_party*Color*Color Range*Machine Dia*Machine GG*Program Quantity*Stitch Length*Location*Dia Width')==false)
        		{
        			return;
        		}
        	}
			else{
        		if (form_validation('cbo_knitting_party*txt_color*cbo_color_range*txt_machine_dia*txt_machine_gg*txt_program_qnty*txt_stitch_length*cbo_dia_width_type','knitting party*Color*Color Range*Machine Dia*Machine GG*Program Quantity*Stitch Length*Dia Width')==false)
        		{
        			return;
        		}
        	}

    		var hiddenProgramQnty = $("#hiddenProgramQnty").val()*1;
    		var balanceProgramQnty = $("#balanceProgramQnty").val()*1;
    		var program_qnty = $("#txt_program_qnty").val()*1;

    		if(operation == 0)
    		{
    			if(balanceProgramQnty  <  program_qnty)
    			{
    				alert("Program Qnty Cann't exceed Balance Qnty");
    				$("#txt_program_qnty").val(balanceProgramQnty);
    				return;
    			}
    		}
    		else if(operation == 1)
    		{
    			if((program_qnty - hiddenProgramQnty) > balanceProgramQnty)
    			{
    				alert("Program Qnty Cann't exceed Balance Qnty");
    				$("#txt_program_qnty").val(hiddenProgramQnty);
    				return;
    			}
    		}

        	if(operation == 0 || operation == 1){
        		var hidden_color_wise_total = $('#hidden_color_wise_total').val()*1;
        		var txt_program_qnty = $('#txt_program_qnty').val()*1;

        		if( hidden_color_wise_total != txt_program_qnty  )
        		{
        			alert('Mismatch Program quantity and Color Wise program quantity');
        			$('#txt_program_qnty').focus();
        			return;
        		}
        	}

        	var booking_qnty = $("#hdn_booking_qnty").val() * 1;
        	var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('cbo_knitting_source*cbo_knitting_party*cbo_subcontract_party*txt_color*txt_machine_dia*txt_machine_gg*txt_program_qnty*txt_stitch_length*txt_spandex_stitch_length*txt_draft_ratio*machine_id*txt_machine_capacity*txt_distribution_qnty*cbo_knitting_status*txt_start_date*txt_end_date*txt_program_date*cbo_feeder*txt_remarks*txt_attention*save_data*updateId*update_dtls_id*cbo_color_range*cbo_dia_width_type*hidden_color_id*txt_fabric_dia*hidden_no_of_feeder_data*cbo_location_name*hidden_advice_data*hidden_collarCuff_data*txt_grey_dia*hiddenProgramQnty*balanceProgramQnty*hidden_count_feeding_data*txt_co_efficient*hidden_bodypartID_data*allowed_date_qnty_string*pic_up_po_ids*upd_plan_po_ids*txt_batch_no*hidden_color_wise_prog_data', "../../") + '&companyID='+<? echo $companyID; ?>+
        	'&gsm=' + '<? echo trim($gsm); ?>' + '&dia=' + '<? echo trim($dia); ?>' + '&desc=' + '<? echo trim($desc); ?>' + '&start_date=' + '<? echo $start_date; ?>' + '&end_date=' + '<? echo $end_date; ?>' + '&determination_id='+'<? echo $determination_id; ?>'+'&booking_no=' + '<? echo trim($booking_no); ?>' + '&data='+'<? echo $data; ?>'+'&body_part_id='+'<? echo $body_part_id; ?>'+'&color_type_id='+'<? echo $color_type_id; ?>'+ '&fabric_typee='+'<? echo $fabric_type; ?>'+ '&tot_booking_qnty='+'<? echo trim($booking_qnty); ?>'+'&buyer_id=' +'<? echo $buyer_id; ?>' +'&hdn_booking_qnty=' + booking_qnty+"&pre_cost_id="+"<? echo $pre_cost_id; ?>"+"&po_id="+"<? echo $po_id; ?>";

        	freeze_window(operation);


        	http.open("POST", "planning_info_entry_for_sample_without_order_controller.php", true);
        	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        	http.send(data);
        	http.onreadystatechange = fnc_program_entry_Reply_info;
        }

        function fnc_program_entry_Reply_info() {
        	if (http.readyState == 4) {
                //release_freezing();return;//alert(http.responseText);
                var reponse = trim(http.responseText).split('**');

                show_msg(reponse[0]);

                if ((reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2)) {
                	var progBalance = 0;
                	var PreProgramQnty = $("#hiddenProgramQnty").val()*1;
                	if(reponse[0] == 0 ){
                		progBalance = $("#balanceProgramQnty").val()*1 - $("#txt_program_qnty").val()*1;
                	}
                	else if(reponse[0] == 1 )
                	{
                		progBalance = $("#balanceProgramQnty").val()*1 + PreProgramQnty - $("#txt_program_qnty").val()*1 ;
                	}
                	reset_form('programQnty_1', '', '', 'txt_start_date,<? echo $start_date; ?>*txt_end_date,<? echo $end_date;?>*txt_program_date,<? echo $current_date;?>', '', 'hdn_booking_qnty');
                	$('#updateId').val(reponse[1]);
                	show_list_view(reponse[1], 'planning_info_details', 'list_view', 'planning_info_entry_for_sample_without_order_controller', '');
                	set_button_status(0, permission, 'fnc_program_entry', 1);

                    $("#txt_program_qnty").val(progBalance.toFixed(2));
                    $("#balanceProgramQnty").val(progBalance.toFixed(2));

                }
                else if (reponse[0] == 13 || reponse[0] == 14) {
              	alert(reponse[1]);
                }
                release_freezing();
            }
        }

        function active_inactive() {
        	var knitting_source = document.getElementById('cbo_knitting_source').value;

        	reset_form('', '', 'txt_machine_no*machine_id*txt_machine_capacity*txt_distribution_qnty*txt_days_req*cbo_location_name', 'txt_start_date,<? echo $start_date; ?>*txt_end_date,<? echo $end_date; ?>*txt_program_date,<? echo $current_date; ?>', '', '');

        	if (knitting_source == 1) {
        		document.getElementById('txt_machine_no').disabled = false;
        		document.getElementById('cbo_location_name').disabled = false;
        	}
        	else {
        		document.getElementById('txt_machine_no').disabled = true;
        		document.getElementById('cbo_location_name').disabled = true;
        	}
        }

        function openpage_feeder() {
        	var no_of_feeder_data = $('#hidden_no_of_feeder_data').val();
        	var color_type_id ='<? echo $color_type_id; ?>';

        	if (!(color_type_id == 2 || color_type_id == 3 || color_type_id == 4 || color_type_id == 33)) {
        		alert("Only for Stripe");
        		return;
        	}

        	var page_link = 'planning_info_entry_for_sample_without_order_controller.php?action=feeder_info_popup&no_of_feeder_data=' + no_of_feeder_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>';
        	var title = 'Stripe Measurement Info';

        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=640px,height=300px,center=1,resize=1,scrolling=0', '../');
        	emailwindow.onclose = function () {
        		var theform = this.contentDoc.forms[0];
        		var hidden_no_of_feeder_data = this.contentDoc.getElementById("hidden_no_of_feeder_data").value;

        		$('#hidden_no_of_feeder_data').val(hidden_no_of_feeder_data);
        	}
        }

        function openpage_collarCuff() {
        	var collarCuff_data = $('#hidden_collarCuff_data').val();
        	var hidden_bodypartID_data = $('#hidden_bodypartID_data').val();
        	var update_dtls_id = $('#update_dtls_id').val();
        	if (update_dtls_id == "") {
        		alert("Save Data First");
        		return;
        	}
        	var page_link = 'planning_info_entry_for_sample_without_order_controller.php?action=collarCuff_info_popup&collarCuff_data=' + collarCuff_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>&update_dtls_id=' + update_dtls_id +'&hidden_bodypartID_data='+hidden_bodypartID_data;
        	var title = 'Collar & Cuff Measurement Info';

        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=540px,height=300px,center=1,resize=1,scrolling=0', '../');
        	emailwindow.onclose = function () {
        		var theform = this.contentDoc.forms[0];
        		var hidden_collarCuff_data = this.contentDoc.getElementById("hidden_collarCuff_data").value;

        		$('#hidden_collarCuff_data').val(hidden_collarCuff_data);
        	}
        }

        function openpage_advice() {
        	var hidden_advice_data = $('#hidden_advice_data').val();

        	var page_link = 'planning_info_entry_for_sample_without_order_controller.php?action=advice_info_popup&hidden_advice_data=' + hidden_advice_data;
        	var title = 'Advice Info';

        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=200px,center=1,resize=1,scrolling=0', '../');
        	emailwindow.onclose = function () {
        		var theform = this.contentDoc.forms[0];
        		var advice_data = this.contentDoc.getElementById("txt_advice").value;
        		$('#hidden_advice_data').val(advice_data);
        	}
        }

		function openpage_count_feeding(){
			var count_feeding_data = $('#hidden_count_feeding_data').val();
			var update_dtls_id = $('#update_dtls_id').val();
			if (update_dtls_id == "") {
				alert("Save Data First");
				return;
			}
			var page_link = 'planning_info_entry_for_sample_without_order_controller.php?action=count_feeding_data_popup&count_feeding_data=' + count_feeding_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>&update_dtls_id=' + update_dtls_id;
			var title = 'Count Feeding';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=300px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var hidden_count_feeding_data = this.contentDoc.getElementById("hidden_count_feeding_data").value;
				$('#hidden_count_feeding_data').val(hidden_count_feeding_data);
			}
		}

		function balance_cal()
		{
			$("#hiddenProgramQnty").val($("#txt_program_qnty").val()*1);
		}

		function getValutAttention(knit_source_id)
		{
				//alert(knit_source_id);  return;
				var knit_source_id=$("#cbo_knitting_source").val()*1;

				if(knit_source_id==1)
				{
					var inHouse_knit_party_id=$("#cbo_knitting_party").val()*1;
					get_php_form_data(knit_source_id+'*'+inHouse_knit_party_id, "check_last_attention_action", "planning_info_entry_for_sample_without_order_controller" );
				}
				else
				{
					var outBound_knit_party_id=$("#cbo_knitting_party").val()*1;
					get_php_form_data(knit_source_id+'*'+outBound_knit_party_id, "check_last_attention_action", "planning_info_entry_for_sample_without_order_controller" );

				}
				return;
		}
		function fn_knit_production(knit_source_id){
			if(knit_source_id==1){
				$("#location_caption").removeClass("change_color2");
				$("#location_caption").addClass("change_color");
			}else{
				$("#location_caption").removeClass("change_color");
				$("#location_caption").addClass("change_color2");
			}
			var knit_sys = $("#hidden_knit_sys").val();
			var hidden_knit_source = $("#hidden_knitting_source").val();
			var companyID = $('#hidden_company').val();
			if(knit_sys != ""){
				alert("Knitting Source Can't Change.Knitting Production Found! ID-"+knit_sys)
				$("#cbo_knitting_source").val(hidden_knit_source);
        	load_drop_down( 'planning_info_entry_for_sample_without_order_controller', hidden_knit_source+'**'+companyID, 'load_drop_down_knitting_party','knitting_party');//class="must_entry_caption"
    		}
		    return;
		}

		$(document).ready(function () {
			$('#txt_stitch_length').keyup(function() {
	        	var th = $(this);
		        th.val( th.val().replace(/[^a-zA-Z0-9,. -]+$/, function(str) {
		        	return '';
	        	}));
	    	});

			//Disable Ctrl+V
	    	var ctrlDown = false,
	        ctrlKey = 17,
	        cmdKey = 91,
	        vKey = 86,
	        cKey = 67;

		    $(document).keydown(function(e) {
		        if (e.keyCode == ctrlKey || e.keyCode == cmdKey) ctrlDown = true;
		    }).keyup(function(e) {
		        if (e.keyCode == ctrlKey || e.keyCode == cmdKey) ctrlDown = false;
		    });

		    $("#txt_stitch_length").keydown(function(e) {
		        if (ctrlDown && (e.keyCode == vKey || e.keyCode == cKey)) return false;
		    });

		    //Disable part of page
		    $('#txt_stitch_length').bind('cut copy paste', function (e) {
		        e.preventDefault();
		    });
    	});


</script>


</head>

<body>
	<div align="center">
		<? echo load_freeze_divs("../../", $permission, 1); ?>
		<form name="programQnty_1" id="programQnty_1">
			<fieldset style="width:900px;">
				<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="890"
				align="center">
				<thead>
					<th width="160">Fabric Description</th>
					<th width="60">GSM</th>
					<th width="60">Dia</th>
					<th width="80">Booking Qnty</th>
					<th width="80">TNA Start Date</th>
					<th width="80">TNA Finish Date</th>
					<th>Description Of Yarn</th>
				</thead>
				<tr bgcolor="#FFFFFF">
					<td><p><? echo $desc; ?></p></td>
					<td><? echo $gsm; ?></td>
					<td><? echo $dia; ?></td>
					<td align="right"><? echo number_format($booking_qnty, 2); ?></td>
					<td align="center"><? echo $start_date; ?></td>
					<td align="center"><? echo $end_date; ?></td>
					<td><p><? echo $desc_of_yarn; ?></p></td>
				</tr>
			</table>
		</fieldset>
		<fieldset style="width:900px; margin-top:5px;">
			<legend>New Entry</legend>
			<input type="hidden" id="hdn_booking_qnty" name="hdn_booking_qnty" value="<? echo $booking_qnty; ?>"/>
			<table width="900" align="center" border="0">
				<tr>
					<td>Knitting Source</td>
					<td>
						<?
						echo create_drop_down("cbo_knitting_source", 152, $knitting_source, "", 1, "-- Select --", 0, "active_inactive();load_drop_down( 'planning_info_entry_for_sample_without_order_controller', this.value+'**'+$companyID, 'load_drop_down_knitting_party','knitting_party'); load_drop_down( 'planning_info_entry_for_sample_without_order_controller',$companyID, 'load_drop_down_location', 'location_td' );getValutAttention(this.value);fn_knit_production(this.value);", 0, '1,3');
						?>
						<input type="hidden" name="hidden_knitting_source" id="hidden_knitting_source" value="">
						<input type="hidden" name="hidden_company" id="hidden_company" value="<? echo $companyID ?>">
						<input type="hidden" name="hidden_knit_sys" id="hidden_knit_sys" value="">
						<input type="hidden" name="pic_up_po_ids" id="pic_up_po_ids" value="<? echo $po_id; ?>">
						<input type="hidden" name="upd_plan_po_ids" id="upd_plan_po_ids" value="">

					</td>
					<td class="must_entry_caption">Knitting Party</td>
					<td id="knitting_party">
						<?
						echo create_drop_down("cbo_knitting_party", 152, $blank_array, "", 1, "--Select Knit Party--", 1, "load_drop_down( 'planning_info_entry_for_sample_without_order_controller', this.value, 'load_drop_down_location', 'location_td' );getValutAttention(this.value);");
						?>
					</td>
					<td>Sub-Subcontract</td>
					<td id="subContract">
						<?
						echo create_drop_down("cbo_subcontract_party", 175, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 and a.is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Party--","", "");
						?>
					</td>
				</tr>
				<tr>
					<td class="must_entry_caption">Color</td>
					<td>
						<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:140px;"
						placeholder="Browse" onClick="openpage_color();" readonly/>
						<input type="hidden" name="hidden_color_id" id="hidden_color_id" readonly/>
						<input type="hidden" name="hidden_color_wise_prog_data" id="hidden_color_wise_prog_data"
						readonly>
						<input type="hidden" name="hidden_color_wise_total" id="hidden_color_wise_total"
						readonly>
					</td>
					<td class="must_entry_caption">Color Range</td>
					<td>
						<?
						echo create_drop_down("cbo_color_range", 152, $color_range, "", 1, "-- Select --", 0, "");
						?>
					</td>
					<td class="must_entry_caption">Machine Dia</td>
					<td>
						<input type="text" name="txt_machine_dia" id="txt_machine_dia" class="text_boxes_numeric"
						style="width:60px;" maxlength="3" title="Maximum 3 Character"
						value="<? echo $dataArray[0][csf('machine_dia')]; ?>"/>
						<?
						echo create_drop_down("cbo_dia_width_type", 100, $fabric_typee, "", 1, "-- Select --", $fabric_type, "");
						?>
					</td>
				</tr>
				<tr>
					<td class="must_entry_caption">Machine GG</td>
					<td>
						<input type="text" name="txt_machine_gg" id="txt_machine_gg" class="text_boxes"
						style="width:140px;" value="<? echo $dataArray[0][csf('machine_gg')]; ?>"/>
					</td>
					<td>Finish Fabric Dia</td>
					<td>
						<input type="text" name="txt_fabric_dia" id="txt_fabric_dia" class="text_boxes"
						style="width:140px;" value="<? echo $dataArray[0][csf('fabric_dia')]; ?>"/>
					</td>
					<td class="must_entry_caption">Program Qnty</td>
					<td>
						<input type="hidden" value="" id="hiddenProgramQnty">
						<input type="hidden" value="<? echo number_format($balance_qnty, 2, '.', '');?>" id="balanceProgramQnty">
						<input type="text" name="txt_program_qnty" id="txt_program_qnty" value="<? echo number_format($balance_qnty, 2, '.', '');?>" class="text_boxes_numeric"
						style="width:165px;" readonly/>
					</td>
				</tr>
				<tr>
					<td>Program Date</td>
					<td>
						<input type="text" name="txt_program_date" id="txt_program_date" class="datepicker"
						style="width:140px" value="<? echo $current_date; ?>" readonly>
					</td>
					<td class="must_entry_caption">Stitch Length</td>
					<td>
						<input type="text" name="txt_stitch_length" id="txt_stitch_length" class="text_boxes"
						style="width:140px;" value="<? echo $dataArray[0][csf('stitch_length')]; ?>" />
					</td>
					<td>Spandex Stitch Length</td>
					<td>
						<input type="text" name="txt_spandex_stitch_length" id="txt_spandex_stitch_length"
						class="text_boxes" style="width:165px;"/>
					</td>
				</tr>
				<tr>
					<td>Draft Ratio</td>
					<td>
						<input type="text" name="txt_draft_ratio" id="txt_draft_ratio" class="text_boxes_numeric"
						style="width:140px;"/>
					</td>
					<td>Machine No</td>
					<td>
						<input type="text" name="txt_machine_no" id="txt_machine_no" class="text_boxes"
						placeholder="Double Click For Search" style="width:140px;"
						onDblClick="openpage_machine();" disabled="disabled" readonly/>
						<input type="hidden" name="machine_id" id="machine_id" class="text_boxes" readonly/>
					</td>
					<td>Machine Capacity</td>
					<td>
						<input type="text" name="txt_machine_capacity" id="txt_machine_capacity"
						placeholder="Display" class="text_boxes_numeric" style="width:165px;"
						disabled="disabled"/>
					</td>
				</tr>
				<tr>
					<td>Distribution Qnty</td>
					<td>
						<input type="text" name="txt_distribution_qnty" id="txt_distribution_qnty"
						placeholder="Display" class="text_boxes_numeric" style="width:65px;"
						disabled="disabled"/>
						<input type="text" name="txt_days_req" id="txt_days_req" placeholder="Days Req."
						class="text_boxes_numeric" style="width:60px;" disabled="disabled"/>
					</td>
					<td>Start Date</td>
					<td>
						<input type="text" name="txt_start_date" id="txt_start_date" class="datepicker"
						style="width:140px" value="<? echo $start_date; ?>" readonly>
					</td>
					<td>End Date & Batch No</td>
					<td width="20">
						<input type="text" name="txt_end_date" id="txt_end_date" class="datepicker"
						style="width:75px" value="<? echo $end_date; ?>" readonly>
						<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:75px;"/>
					</td>
				</tr>
				<tr>
					<td>Status</td>
					<td>
						<?
						echo create_drop_down("cbo_knitting_status", 152, $knitting_program_status, "", 1, "--Select Status--", 0, "");
						?>
					</td>

					<td>Feeder</td>
					<td>
						<?
						$feeder = array(1 => "Full Feeder", 2 => "Half Feeder");
						echo create_drop_down("cbo_feeder", 152, $feeder, "", 1, "--Select Feeder--", 0, "");
						?>
					</td>
					<td colspan="2">
						<input type="button" name="feeder" class="formbuttonplasminus" value="No Of Feeder"
						onClick="openpage_feeder();" style="width:100px"/>
						<input type="hidden" name="hidden_no_of_feeder_data" id="hidden_no_of_feeder_data"
						class="text_boxes"><b> &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp;  &emsp; &emsp; &nbsp;Program No.</b>
						<input type="text" name="txt_program_no" id="txt_program_no" class="text_boxes"
						placeholder="Display" disabled style="width:100px">
					</td>
				</tr>
				<tr>
					<td>Remarks</td>
					<td>
						<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes"
						style="width:140px">
					</td>
					<td id="location_caption">Location</td>
					<td id="location_td">
						<?
						echo create_drop_down("cbo_location_name", 152, $blank_array, 1, "-- Select Location --", 0, "");
						?>
					</td>
					<td>
						<input type="button" name="feeder" class="formbuttonplasminus" value="Advice"
						onClick="openpage_advice();" style="width:100px"/>
						<input type="hidden" name="hidden_advice_data" id="hidden_advice_data" class="text_boxes">
					</td>

					<td colspan="2">
						<span style="padding-right: 5px;"><b>Knitting Coefficient</b></span>
						<input type="text" name="txt_co_efficient" id="txt_co_efficient" class="text_boxes_numeric"style="width:58px" >
					</td>

				</tr>
				<tr>
					<td>Grey fabric Dia</td>
					<td>
						<input type="text" name="txt_grey_dia" id="txt_grey_dia" class="text_boxes"
						style="width:140px">
					</td>
					<td>Attention</td>
					<td>
						<input type="text" name="txt_attention" id="txt_attention" class="text_boxes"
						style="width:140px">
					</td>

					<td>
						<input type="button" name="feeder" class="formbuttonplasminus" value="Count Feeding"
						onClick="openpage_count_feeding();" style="width:100px"/>
						<input type="hidden" name="hidden_count_feeding_data" id="hidden_count_feeding_data"
						class="text_boxes">
					</td>

					<td>
						<input type="button" name="feeder" class="formbuttonplasminus" value="Collar & Cuff"
						onClick="openpage_collarCuff();" style="width:100px"/>
						<input type="hidden" name="hidden_collarCuff_data" id="hidden_collarCuff_data"
						class="text_boxes">
						<input type="hidden" name="hidden_bodypartID_data" id="hidden_bodypartID_data"
						class="text_boxes" value="<? echo $body_part_id; ?>">
					</td>

				</tr>
				<tr>
					<td colspan="4" align="right" class="button_container">
						<?
						echo load_submit_buttons($permission, "fnc_program_entry", 0, 0, "reset_form('programQnty_1','','','txt_start_date,$start_date*txt_end_date,$end_date*txt_program_date,$current_date','','updateId*txt_color');", 1);
						?>
					</td>
					<td colspan="2" align="left" valign="top" class="button_container">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close"
						onClick="parent.emailwindow.hide();" style="width:100px;"/>
						<input type="hidden" name="save_data" id="save_data" class="text_boxes">
						<input type="hidden" name="allowed_date_qnty_string" id="allowed_date_qnty_string" class="text_boxes">
						<input type="hidden" name="updateId" id="updateId" class="text_boxes"
						value="<? echo str_replace("'", '', $plan_id); ?>">
						<input type="hidden" name="update_dtls_id" id="update_dtls_id" class="text_boxes">
					</td>
				</tr>
			</table>
		</fieldset>
		<div id="list_view" style="margin-top:5px">
			<?
			if (str_replace("'", '', $plan_id) != "") {
				?>
				<script>
					show_list_view('<? echo str_replace("'", '', $plan_id); ?>', 'planning_info_details', 'list_view', 'planning_info_entry_for_sample_without_order_controller', '');
				</script>
				<?
			}
			?>
		</div>
	</form>
</div>
<style type="text/css">
	.change_color{color: blue;}
	.change_color2{color: black;}
</style>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "date_duplication_check_old")
{
	$data = explode("**", $data);
	$machine_id = $data[0];
	if ($db_type == 0) {
		$startDate = change_date_format(trim($data[1]), "yyyy-mm-dd", "");
		$endDate = change_date_format(trim($data[2]), "yyyy-mm-dd", "");
	} else {
		$startDate = change_date_format(trim($data[1]), '', '', 1);
		$endDate = change_date_format(trim($data[2]), '', '', 1);
	}
	$update_dtls_id = $data[3];

	if ($update_dtls_id == "") {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date between '$startDate' and '$endDate' group by distribution_date";
	} else {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date between '$startDate' and '$endDate' and dtls_id<>$update_dtls_id group by distribution_date";
	}
	//echo $sql;die;
	$data_array = sql_select($sql);
	$data = '';
	if (count($data_array) > 0)
	{
		foreach ($data_array as $row) {
			if ($row[csf('days_complete')] >= 1) {
				if ($data == '') $data = change_date_format($row[csf('distribution_date')]); else $data .= "," . change_date_format($row[csf('distribution_date')]);
			}
		}

		if ($data == '') echo "0_"; else echo "1" . "_" . $data;
	} else {
		echo "0_";
	}

	exit();
}

if ($action == "date_duplication_check_old_2")
{
	$data = explode("**", $data);
	$machine_id = $data[0];
	$distribute_qnty = $data[2];
	$capacity_qnty = $data[3];
	$no_of_days = $data[4];
	if ($db_type == 0) {
		$startDate = change_date_format(trim($data[1]), "yyyy-mm-dd", "");
	} else {
		$startDate = change_date_format(trim($data[1]), '', '', 1);
	}


	$update_dtls_id = $data[5];

	if ($update_dtls_id == "") {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date >= '$startDate' group by distribution_date order by distribution_date";
	} else {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date >= '$startDate' and dtls_id<>$update_dtls_id group by distribution_date order by distribution_date";
	}
	//echo $sql;die;
	$data_array = sql_select($sql);
	$data = ''; $end_date = "";
	if (count($data_array) > 0)
	{
		foreach ($data_array as $row)
		{
			if(date("d-m-Y",strtotime($row[csf('distribution_date')])) == date('d-m-Y',strtotime($startDate)))
			{
				if ($row[csf('days_complete')] >= 1)
				{
					if ($data == '') $data = change_date_format($row[csf('distribution_date')]); else $data .= "," . change_date_format($row[csf('distribution_date')]);
					if ($data == '') echo "0_"; else echo "1" . "_" . $data; exit();
				}
				else
				{
					$curr_remain_day = 1-$row[csf('days_complete')];
					$curr_day_capacity_qnty =  $capacity_qnty * $curr_remain_day;
					$remain_dist =  $distribute_qnty - $curr_day_capacity_qnty;

					if($distribute_qnty >= $curr_day_capacity_qnty)
					{
						$end_date = $row[csf('distribution_date')];
						$distribute_qnty =  $distribute_qnty - $curr_day_capacity_qnty;
						$dateWise_qnty .= "".$row[csf('distribution_date')].",".$curr_day_capacity_qnty."*";
						$no_of_days -= $curr_remain_day;
					}

					if($distribute_qnty ==0)
					{
						$dateWise_qnty = chop($dateWise_qnty,"*");
						echo "1_".$dateWise_qnty."#".$end_date;
						exit();
					}
				}
			}
			else
			{
				if ($row[csf('days_complete')] < 1)
				{
					$curr_remain_day = 1-$row[csf('days_complete')];
					$curr_day_capacity_qnty =  $capacity_qnty * $curr_remain_day;
					$remain_dist =  $distribute_qnty - $curr_day_capacity_qnty;

					if($distribute_qnty >= $curr_day_capacity_qnty)
					{
						$end_date = $row[csf('distribution_date')];
						$distribute_qnty =  $distribute_qnty - $curr_day_capacity_qnty;
						$dateWise_qnty .= "".$row[csf('distribution_date')].",".$curr_day_capacity_qnty."*";
						$no_of_days =- $curr_remain_day;
					}
					if($distribute_qnty ==0)
					{
						$dateWise_qnty = chop($dateWise_qnty,"*");
						echo "1_".$dateWise_qnty."#".$end_date;
						exit();
					}
				}
			}

		}

		if($no_of_days > 0)
		{
			$ceiled_noOfDays =  ceil($no_of_days);
			for($i = 0; $i <$ceiled_noOfDays; $i++)
			{
				$nxt_day = date('d-m-Y',strtotime($end_date . "+1 days"));
				if($no_of_days >1)
				{
					$dateWise_qnty .= $nxt_day.",".$capacity_qnty."*";

				}else{

					$dateWise_qnty .= $nxt_day.",".$no_of_days*$capacity_qnty."*";
				}
				$no_of_days -= 1;
				$end_date =$nxt_day;
			}
			$dateWise_qnty = chop($dateWise_qnty,"*");

		}

		echo "1_".$dateWise_qnty."#".$end_date;
		exit();
		if ($data == '') echo "0_"; else echo "1" . "_" . $data;
	}

	exit();
}

if ($action == "date_duplication_check")
{
	$data = explode("**", $data);
	$machine_id = $data[0];
	$distribute_qnty = $data[2];
	$capacity_qnty = $data[3];
	$no_of_days = $data[4];
	if ($db_type == 0) {
		$startDate = change_date_format(trim($data[1]), "yyyy-mm-dd", "");
	} else {
		$startDate = change_date_format(trim($data[1]), '', '', 1);
	}

	$update_dtls_id = $data[5];

	if ($update_dtls_id == "") {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date >= '$startDate' group by distribution_date order by distribution_date";
	} else {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date >= '$startDate' and dtls_id<>$update_dtls_id group by distribution_date order by distribution_date";
	}
	//echo $sql;die;
	$data_array = sql_select($sql);
	$data = ''; $end_date = "";
	if (count($data_array) > 0)
	{
		foreach ($data_array as $row)
		{
			$distribution_date = date('d-m-Y',strtotime($row[csf('distribution_date')]));
			$previous_occupied_arr[$distribution_date]["days_complete"] = $row[csf('days_complete')];
			$previous_occupied_arr[$distribution_date]["distribution_date"] = $distribution_date;
		}
	}

	$present_day = $startDate;
	$dateWise_qnty = ""; $end_date = "";
	while($distribute_qnty > 0)
	{
		$present_day = date("d-m-Y",strtotime($present_day));

		if($previous_occupied_arr[$present_day]["distribution_date"])
		{
			if($previous_occupied_arr[$present_day]["distribution_date"] == $present_day)
			{
				if ($previous_occupied_arr[$present_day]["days_complete"] >= 1)
				{
					$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
				}
				else
				{
					if($distribute_qnty > ($capacity_qnty * (1 - $previous_occupied_arr[$present_day]["days_complete"])))
					{
						$dist_qnty = $capacity_qnty * (1 - $previous_occupied_arr[$present_day]["days_complete"]);
						$dateWise_qnty .= $present_day.",".$dist_qnty.",".($dist_qnty/$capacity_qnty)."*";
					}
					else
					{
						$dateWise_qnty .= $present_day.",".$distribute_qnty.",".($distribute_qnty/$capacity_qnty)."*";
					}

					$distribute_qnty = $distribute_qnty - ($capacity_qnty * (1 - $previous_occupied_arr[$present_day]["days_complete"]));
					$end_date =  $present_day;
					$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
				}
			}
		}
		else
		{
			if($distribute_qnty > ($capacity_qnty * (1 - $previous_occupied_arr[$present_day]["days_complete"])))
			{
				$dateWise_qnty .= $present_day.",".$capacity_qnty.",".($capacity_qnty/$capacity_qnty)."*";
			}
			else
			{
				$dateWise_qnty .= $present_day.",".$distribute_qnty.",".($distribute_qnty/$capacity_qnty)."*";
			}

			$end_date =  $present_day;
			$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
			$distribute_qnty = $distribute_qnty - $capacity_qnty;

		}


	}

	$dateWise_qnty = chop($dateWise_qnty,"*");
	echo $dateWise_qnty."=".$end_date;

	exit();
}

if ($action == "feeder_info_popup")
{
	echo load_html_head_contents("Machine Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		function fnc_close() {
			var save_string = '';
			var tot_row = $("#tbl_list_search tbody tr").length;

			for (var i = 1; i <= tot_row; i++) {
				var txtPreCostId = $('#txtPreCostId_' + i).val();
				var txtColorId = $('#txtColorId_' + i).val();
				var txtStripeColorId = $('#txtStripeColorId_' + i).val();
				var txtNoOfFeeder = $('#txtNoOfFeeder_' + i).val();

				if (save_string == "") {
					save_string = txtPreCostId + "_" + txtColorId + "_" + txtStripeColorId + "_" + txtNoOfFeeder;
				}
				else {
					save_string += "," + txtPreCostId + "_" + txtColorId + "_" + txtStripeColorId + "_" + txtNoOfFeeder;
				}

			}

			$('#hidden_no_of_feeder_data').val(save_string);

			parent.emailwindow.hide();
		}

		function calculate_total() {
			var tot_row = $("#tbl_list_search tbody tr").length;

			var ddd = {dec_type: 6, comma: 0, currency: ''}

			math_operation("txtTotFeeder", "txtNoOfFeeder_", "+", tot_row, ddd);
		}

	</script>

</head>

<body>
	<div style="width:630px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:620px; margin-top:10px; margin-left:5px">
				<input type="hidden" name="hidden_no_of_feeder_data" id="hidden_no_of_feeder_data" class="text_boxes"
				value="">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="140">Color</th>
						<th width="130">Stripe Color</th>
						<th width="90">Measurement</th>
						<th width="70">UOM</th>
						<th>No Of Feeder</th>
					</thead>
				</table>
				<div style="width:618px; overflow-y:scroll; max-height:230px;" id="buyer_list_view">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table"
					id="tbl_list_search">
					<tbody>
						<?
						$noOfFeeder_array = array();
						$no_of_feeder_data = explode(",", $no_of_feeder_data);
						$pre_cost_id = explode(",", $pre_cost_id);
						$pre_cost_id = implode(",", array_unique($pre_cost_id));

						$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

						for ($i = 0; $i < count($no_of_feeder_data); $i++) {
							$color_wise_data = explode("_", $no_of_feeder_data[$i]);
							$pre_cost_fabric_cost_dtls_id = $color_wise_data[0];
							$color_id = $color_wise_data[1];
							$stripe_color = $color_wise_data[2];
							$no_of_feeder = $color_wise_data[3];

							$noOfFeeder_array[$pre_cost_fabric_cost_dtls_id][$color_id][$stripe_color][$i]=$no_of_feeder;
							//$noOfFeeder_array[$i] = $no_of_feeder;
						}

						//$sql = "select pre_cost_fabric_cost_dtls_id as pre_cost_id, color_number_id, stripe_color, measurement, uom from wo_pre_stripe_color where pre_cost_fabric_cost_dtls_id in($pre_cost_id) and status_active=1 and is_deleted=0 order by color_number_id,id";
						$sql = "select sample_fab_dtls_id as pre_cost_id, color_number_id, stripe_color, measurement, uom from wo_sample_stripe_color where sample_fab_dtls_id in($pre_cost_id) and status_active=1 and is_deleted=0 order by color_number_id,id";

						$result = sql_select($sql);

						$i = 1;
						$tot_feeder = 0;
						$kl = 0;
						foreach ($result as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

							//$no_of_feeder = $noOfFeeder_array[$kl];
							$no_of_feeder=$noOfFeeder_array[$row[csf('pre_cost_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]][$kl];
							$tot_feeder += $no_of_feeder;
							$kl++;
							?>
							<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
								<td width="40" align="center"><? echo $i; ?>
								<input type="hidden" name="txtPreCostId[]" id="txtPreCostId_<?php echo $i ?>"
								value="<? echo $row[csf('pre_cost_id')]; ?>"/>
								<input type="hidden" name="txtColorId[]" id="txtColorId_<?php echo $i ?>"
								value="<? echo $row[csf('color_number_id')]; ?>"/>
								<input type="hidden" name="txtStripeColorId[]"
								id="txtStripeColorId_<?php echo $i ?>"
								value="<? echo $row[csf('stripe_color')]; ?>"/>
							</td>
							<td width="140"><p><? echo $color_library[$row[csf('color_number_id')]]; ?></p></td>
							<td width="130"><p><? echo $color_library[$row[csf('stripe_color')]]; ?></p></td>
							<td width="90"><input type="text" name="txtMeasurement[]"
								id="txtMeasurement_<? echo $i; ?>" class="text_boxes_numeric"
								style="width:80px" value="<? echo $row[csf('measurement')]; ?>"
								disabled/></td>
								<td width="70" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p>
								</td>
								<td align="center">
									<input type="text" name="txtNoOfFeeder[]" id="txtNoOfFeeder_<? echo $i; ?>"
									class="text_boxes_numeric" style="width:90px"
									value="<? echo $no_of_feeder; ?>" onKeyUp="calculate_total();"/>
								</td>
							</tr>
							<?
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<th colspan="5">Total</th>
						<th style="text-align:center"><input type="text" name="txtTotFeeder" id="txtTotFeeder"
							class="text_boxes_numeric" style="width:90px"
							value="<? echo $tot_feeder; ?>" disabled/></th>
						</tfoot>
					</table>
				</div>
				<table width="600" id="tbl_close">
					<tr>
						<td align="center">
							<input type="button" name="close" class="formbutton" value="Close" id="main_close"
							onClick="fnc_close();" style="width:100px"/>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "advice_info_popup")
{
	echo load_html_head_contents("Advice Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

</head>

<body>
	<div style="width:430px;" align="center">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:400px; margin-top:10px;">
				<input type="hidden" name="hidden_advice_data" id="hidden_advice_data" class="text_boxes" value="">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table">
					<tr>
						<td><textarea name="txt_advice" id="txt_advice" class="text_area"
							style="width:385px; height:120px;"><? echo $hidden_advice_data; ?></textarea></td>
						</tr>
					</table>
					<table width="400" id="tbl_close">
						<tr>
							<td align="center">
								<input type="button" name="close" class="formbutton" value="Close" id="main_close"
								onClick="parent.emailwindow.hide();" style="width:100px"/>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "collarCuff_info_popup")
{
	echo load_html_head_contents("Collar & Cuff Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		function add_break_down_tr(i) {
			var row_num = $('#txt_tot_row').val();
			row_num++;

			var clone = $("#tr_" + i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});

			clone.find("input,select").each(function () {

				$(this).attr({
					'id': function (_, id) {
						var id = id.split("_");
						return id[0] + "_" + row_num
					},
					'name': function (_, name) {
						return name
					},
					'value': function (_, value) {
						return value
					}
				});

            }).end();//.appendTo("#tbl_list_search")

			$("#tr_" + i).after(clone);

			$('#txtGrey_' + row_num).removeAttr("value").attr("value", "");
			$('#txtFinish_' + row_num).removeAttr("value").attr("value", "");
			$('#txtQtyPcs_' + row_num).removeAttr("value").attr("value", "");
			$('#txtQtyPcs_' + row_num).removeAttr("onKeyUp").attr("onKeyUp", "calculate_tot_qnty(" + row_num + ");");

			$('#increase_' + row_num).removeAttr("value").attr("value", "+");
			$('#decrease_' + row_num).removeAttr("value").attr("value", "-");
			$('#increase_' + row_num).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + row_num + ");");
			$('#decrease_' + row_num).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + row_num + ");");

			$('#txt_tot_row').val(row_num);
			reArrangeSl();
			set_all_onclick();
		}

		function reArrangeSl() {
			var i = 0;
			$("#tbl_list_search").find('tbody tr').each(function () {
				i++;
				$(this).find("td:eq(0)").text(i);
			});
		}

		function fn_deleteRow(rowNo) {
			if (rowNo != 1) {
				$("#tr_" + rowNo).remove();
				reArrangeSl();
				calculate_tot_qnty();
			}
		}

		function fnc_close() {
			var save_string = "";
			var breakOut = true;
			$("#tbl_list_search").find('tbody tr').each(function () {
				if (breakOut == false) {
					return;
				}

				var bodyPartId = $(this).find('input[name="bodyPartId[]"]').val();
				var txtGrey = $(this).find('input[name="txtGrey[]"]').val();
				var txtFinish = $(this).find('input[name="txtFinish[]"]').val();
				var txtQtyPcs = $(this).find('input[name="txtQtyPcs[]"]').val() * 1;

				if (txtQtyPcs < 1) {
					alert("Please Insert Qty. Pcs");
					$(this).find('input[name="txtQtyPcs[]"]').focus();
					breakOut = false;
					return false;
				}

				if (save_string == "") {
					save_string = bodyPartId + "_" + txtGrey + "_" + txtFinish + "_" + txtQtyPcs;
				}
				else {
					save_string += "," + bodyPartId + "_" + txtGrey + "_" + txtFinish + "_" + txtQtyPcs;
				}
			});

			if (breakOut == false) {
				return;
			}
			$('#hidden_collarCuff_data').val(save_string);
			parent.emailwindow.hide();
		}

		function calculate_tot_qnty() {
			var txtTotQtyPcs = '';
			$("#tbl_list_search").find('tbody tr').each(function () {
				var txtQtyPcs = $(this).find('input[name="txtQtyPcs[]"]').val() * 1;
				txtTotQtyPcs = txtTotQtyPcs * 1 + txtQtyPcs * 1;
			});

			$('#txtTotQtyPcs').val(Math.round(txtTotQtyPcs));
		}

	</script>

	</head>

	<body>
		<div style="width:530px;" align="center">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:530px; margin-top:5px">
					<input type="hidden" name="hidden_collarCuff_data" id="hidden_collarCuff_data" class="text_boxes"
					value="">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="525" class="rpt_table">
						<thead>
							<th width="30">SL</th>
							<th width="100">Body Part</th>
							<th width="100">Grey Size</th>
							<th width="100">Finish Size</th>
							<th width="100">Qty. Pcs</th>
							<th></th>
						</thead>
					</table>
					<div style="width:525px; overflow-y:scroll; max-height:230px;" id="buyer_list_view">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="505" class="rpt_table"
						id="tbl_list_search">
						<tbody>
							<?
							$collarCuff_data = ($collarCuff_data != "") ? explode(",", $collarCuff_data) : array();
							if (!empty($collarCuff_data))
							{
								$sl = 1;
								for ($i = 0; $i < count($collarCuff_data); $i++)
								{
									$body_part_wise_data = explode("_", $collarCuff_data[$i]);
									$body_part_id = $body_part_wise_data[0];
									$grey = $body_part_wise_data[1];
									$finish = $body_part_wise_data[2];
									$qty = $body_part_wise_data[3];
									$totQtyPcs += $qty;
									?>
									<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
										<td width="30" align="center"><? echo $sl++; ?></td>
										<td width="100">
											<input type="text" name="txtBodyPartId[]" id="txtBodyPartId_<?php echo $i ?>"
											value="<? echo $body_part[$body_part_id]; ?>" class="text_boxes"
											style="width:80px" disabled/>
											<input type="hidden" name="bodyPartId[]" id="bodyPartId_<?php echo $i ?>"
											value="<? echo $body_part_id; ?>"/>
										</td>
										<td width="100"><input type="text" name="txtGrey[]" id="txtGrey_<? echo $i; ?>"
											class="text_boxes" style="width:80px"
											value="<? echo $grey; ?>"/>
										</td>
										<td width="100"><input type="text" name="txtFinish[]" id="txtFinish_<? echo $i; ?>"
											class="text_boxes" style="width:80px"
											value="<? echo $finish; ?>"/>
										</td>
										<td width="100">
											<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $i; ?>"
											class="text_boxes_numeric" style="width:80px" value="<? echo $qty; ?>"
											onKeyUp="calculate_tot_qnty();"/>
										</td>
										<td>
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]"
											style="width:30px" class="formbuttonplasminus" value="+"
											onClick="add_break_down_tr( <? echo $i; ?> )"/>
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]"
											style="width:30px" class="formbuttonplasminus" value="-"
											onClick="fn_deleteRow(<? echo $i; ?>);"/>
										</td>
										</tr>
										<?
									}
							}
							else
							{
								$sql = "select collar_cuff_data from ppl_planning_info_entry_dtls where id=$update_dtls_id";
								$collar_cuff_data_arr = sql_select($sql);
								$collar_cuff_data = explode(",", $collar_cuff_data_arr[0]["collar_cuff_data"]);

								$i = 1;
								$totQtyPcs = 0;
								$sl = 1;
								foreach ($collar_cuff_data as $row)
								{
									$collar_data = explode("_", $row);
									$totQtyPcs += $collar_data[3];
									?>
									<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl++; ?>">
										<td width="30" align="center"><? echo $sl; ?></td>
										<td width="100">
											<input type="text" name="txtBodyPartId[]" id="txtBodyPartId_<?php echo $i ?>"
											value="<? echo $body_part[$hidden_bodypartID_data]; //$body_part[$collar_data[0]]; ?>" class="text_boxes"
											style="width:80px" disabled/>
											<input type="hidden" name="bodyPartId[]" id="bodyPartId_<?php echo $i ?>"
											value="<? echo $hidden_bodypartID_data; ?>"/>
										</td>
										<td width="100"><input type="text" name="txtGrey[]" id="txtGrey_<? echo $i; ?>"
											class="text_boxes" style="width:80px"
											value="<?php echo $collar_data[1]; ?>"/>
										</td>
										<td width="100"><input type="text" name="txtFinish[]" id="txtFinish_<? echo $i; ?>"
											class="text_boxes" style="width:80px"
											value="<?php echo $collar_data[2]; ?>"/>
										</td>
										<td width="100">
											<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $i; ?>"
											class="text_boxes_numeric" style="width:80px"
											value="<?php echo $collar_data[3]; ?>" onKeyUp="calculate_tot_qnty();"/>
										</td>
										<td>
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]"
											style="width:30px" class="formbuttonplasminus" value="+"
											onClick="add_break_down_tr( <? echo $i; ?> )"/>
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]"
											style="width:30px" class="formbuttonplasminus" value="-"
											onClick="fn_deleteRow(<? echo $i; ?>);"/>
										</td>
									</tr>
									<?
									$i++;
								}
							}
							?>
						</tbody>
						<tfoot>
							<th colspan="4">Total</th>
							<th style="text-align:center"><input type="text" name="txtTotQtyPcs" id="txtTotQtyPcs"
								class="text_boxes_numeric" style="width:80px"
								value="<? echo $totQtyPcs; ?>" disabled/><input
								type="hidden" name="txt_tot_row" id="txt_tot_row" value="<? echo $i - 1; ?>"/></th>
								<th></th>
							</tfoot>
						</table>
					</div>
					<table width="500" id="tbl_close">
						<tr>
							<td align="center">
								<input type="button" name="close" class="formbutton" value="Close" id="main_close"
								onClick="fnc_close();" style="width:100px"/>
							</td>
						</tr>
					</table>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "count_feeding_data_popup")
{
	echo load_html_head_contents("Count Feeding", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		function add_break_down_tr(i) {
			var row_num = $('#tbl_list_search tr').length;
			row_num++;

			var clone = $("#tr_" + i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});

			clone.find("input,select").each(function () {

				$(this).attr({
					'id': function (_, id) {
						var id = id.split("_");
						return id[0] + "_" + row_num
					},
					'name': function (_, name) {
						return name
					},
					'value': function (_, value) {
						return value
					}
				});

			}).end();

			$("#tr_" + i).after(clone);
//$('#txtSeqNo_' + row_num).removeAttr("value").attr("value", row_num);
$('#cboCount_' + row_num).removeAttr("value").attr("value", 0);
$('#cboFeeding_' + row_num).removeAttr("value").attr("value", 0);

$('#increase_' + row_num).removeAttr("value").attr("value", "+");
$('#decrease_' + row_num).removeAttr("value").attr("value", "-");
$('#increase_' + row_num).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + row_num + ");");
$('#decrease_' + row_num).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + row_num + ");");

reArrangeSl();
set_all_onclick();
}

function reArrangeSl() {
var i = 0;
$("#tbl_list_search").find('tbody tr').each(function () {
	i++;
	$(this).find("td:eq(0)").text(i);
	$(this).find("td:eq(1) input").val(i);
});
}

function fn_deleteRow(rowNo) {
if (rowNo != 1) {
	$("#tr_" + rowNo).remove();
	reArrangeSl();
}
}

function fnc_close() {
var save_string = "";
var breakOut = true;
$("#tbl_list_search").find('tbody tr').each(function () {
	if (breakOut == false) {
		return;
	}

	var txtSeqNo = $(this).find('input[name="txtSeqNo[]"]').val();
	var cboCount = $(this).find('select[name="cboCount[]"]').val();
	var cboFeeding = $(this).find('select[name="cboFeeding[]"]').val();


	if (save_string == "") {
		save_string = txtSeqNo + "_" + cboCount + "_" + cboFeeding;
	}
	else {
		save_string += "," + txtSeqNo + "_" + cboCount + "_" + cboFeeding;
	}


});

if (breakOut == false) {
	return;
}

$('#hidden_count_feeding_data').val(save_string);
parent.emailwindow.hide();
}





</script>

</head>

<body>
<div style="width:430px;" align="center">
<form name="searchwofrm" id="searchwofrm">
<fieldset style="width:430px; margin-top:5px">
	<input type="hidden" name="hidden_count_feeding_data" id="hidden_count_feeding_data" class="text_boxes"
	value="">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="425" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="100">Seq. No</th>
			<th width="100">Count</th>
			<th width="100">Feeding</th>
			<th></th>
		</thead>
	</table>
	<div style="width:425px; overflow-y:scroll; max-height:230px;" id="buyer_list_view">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="405" class="rpt_table"
		id="tbl_list_search">
		<tbody>
			<?
			$yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by yarn_count","id","yarn_count");


			$count_feeding_data_arr = ($count_feeding_data != "") ? explode(",", $count_feeding_data) : array();
			if (!empty($count_feeding_data)){
				$sl = 1;
				for ($i = 0; $i < count($count_feeding_data_arr); $i++) {
					$count_feeding_data = explode("_", $count_feeding_data_arr[$i]);
					$seq = $count_feeding_data[0];
					$count_id = $count_feeding_data[1];
					$feeding_id = $count_feeding_data[2];
					?>
					<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
						<td width="30" align="center"><? echo $sl++; ?></td>
						<td width="100">
							<input type="text" name="txtSeqNo[]" id="txtSeqNo_<?php echo $i ?>" value="<? echo $seq;?>" class="text_boxes" style="width:80px"/>
						</td>
						<td width="100">
							<?
							echo create_drop_down( "cboCount_".$i, 80, $yarn_count_arr,"", 1, "-- Count --",$count_id, "",0,"","","","","","","cboCount[]");
							?>
						</td>
						<td width="100">
							<?
							echo create_drop_down( "cboFeeding_".$i, 80, $feeding_arr,"", 1, "-- Feeding --",$feeding_id, "",0,"","","","","","","cboFeeding[]");
							?>
						</td>
						<td>
							<input type="button" id="increase_<? echo $i; ?>" name="increase[]"
							style="width:30px" class="formbuttonplasminus" value="+"
							onClick="add_break_down_tr( <? echo $i; ?> )"/>
							<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]"
							style="width:30px" class="formbuttonplasminus" value="-"
							onClick="fn_deleteRow(<? echo $i; ?>);"/>
						</td>
					</tr>
					<?
				}
			}
			else
			{
				$i=0;$sl=0;
				?>
				<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
					<td width="30" align="center"><? echo $sl++;; ?></td>
					<td width="100">
						<input type="text" name="txtSeqNo[]" id="txtSeqNo_<?php echo $i ?>" value="1" class="text_boxes" style="width:80px"/>
					</td>
					<td width="100">
						<?
						echo create_drop_down( "cboCount_".$i, 80, $yarn_count_arr,"", 1, "-- Count --", $selected, "",0,"","","","","","","cboCount[]");
						?>
					</td>
					<td width="100">
						<?
						echo create_drop_down( "cboFeeding_".$i, 80, $feeding_arr,"", 1, "-- Feeding --", $selected, "",0,"","","","","","","cboFeeding[]");
						?>
					</td>
					<td>
						<input type="button" id="increase_<? echo $i; ?>" name="increase[]"
						style="width:30px" class="formbuttonplasminus" value="+"
						onClick="add_break_down_tr(<? echo $i; ?>)"/>
						<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]"
						style="width:30px" class="formbuttonplasminus" value="-"
						onClick="fn_deleteRow(<? echo $i; ?>);"/>
					</td>
				</tr>
				<?
			}
			?>
		</tbody>
	</table>
</div>
<table width="400" id="tbl_close">
	<tr>
		<td align="center">
			<input type="button" name="close" class="formbutton" value="Close" id="main_close"
			onClick="fnc_close();" style="width:100px"/>
		</td>
	</tr>
</table>
</fieldset>
</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
?>
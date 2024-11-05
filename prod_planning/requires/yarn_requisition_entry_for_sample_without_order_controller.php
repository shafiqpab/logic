<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
extract($_REQUEST);
$permission = $_SESSION['page_permission'];

$user_id = $_SESSION['logic_erp']["user_id"];

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
	echo create_drop_down("cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}

$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");

/*
|--------------------------------------------------------------------------
| booking_no_search_popup
|--------------------------------------------------------------------------
|
*/
if ($action == "booking_no_search_popup")
{
	echo load_html_head_contents("Booking No Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$buyerID = str_replace("'", "", $buyerID);
	?>
	<script>
		function js_set_value(booking_no)
		{
            //alert(booking_no)
            document.getElementById('hide_booking_no').value = booking_no;
            parent.emailwindow.hide();
        }
    </script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
				class="rpt_table" id="tbl_list">
				<thead>
					<th>Buyer</th>
					<th id="search_by_td_up" width="170">Please Enter Booking No</th>
					<th>Shipment Date</th>
					<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
					<input type="hidden" name="hide_booking_no" id="hide_booking_no" value=""/>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<?
							echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", $buyerID, "", 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
							readonly>
						</td>
						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show"
							onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'yarn_requisition_entry_for_sample_without_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
							style="width:100px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="4" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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
| create_booking_no_search_list_view
|--------------------------------------------------------------------------
|
*/
if ($action == "create_booking_no_search_list_view")
{
	$data = explode('**', $data);
	$company_id = $data[0];

	//buyer_id_cond
	$buyer_id_cond = '';
	if ($data[1] == 0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"] == 1)
		{
			if ($_SESSION['logic_erp']["buyer_id"] >0)
				$buyer_id_cond = " AND a.buyer_id IN (" . $_SESSION['logic_erp']["buyer_id"] . ")";
		}
	}
	else
	{
		$buyer_id_cond = " AND buyer_id = ".$data[1]."";
	}

	//booking_cond
	$booking_cond = '';
	if (trim($data[2]) != '')
		$booking_cond = " AND a.booking_no LIKE '%".$data[2]."%'";

	//date_cond
	$date_cond = '';
	$start_date = trim($data[3]);
	$end_date = trim($data[4]);
	if ($start_date != "" && $end_date != "")
	{
		if ($db_type == 0)
		{
			$date_cond = " AND b.program_date BETWEEN '".change_date_format($start_date, "yyyy-mm-dd", "-")."' AND '".change_date_format($end_date, "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond = " AND b.program_date BETWEEN '".change_date_format($start_date, '', '', 1)."' AND '".change_date_format($end_date, '', '', 1)."'";
		}
	}

	//year_field
	//groupby_year
	if ($db_type == 0)
	{
		$year_field = "YEAR(a.insert_date) AS year,";
		$groupby_year = "YEAR(a.insert_date),";
	}
	else if ($db_type == 2)
	{
		$year_field = "TO_CHAR(a.insert_date,'YYYY') AS year,";
		$groupby_year = "TO_CHAR(a.insert_date,'YYYY'),";
	}
	else
	{
		$year_field = "";
	}

	$booking_type = array(1 => "Short", 2 => "Main");
	$arr = array(0 => $buyer_arr, 4 => $booking_type);

	$sql = "
		SELECT
			a.id, a.buyer_id, a.booking_no, ".$year_field."
			b.program_date
		FROM
			ppl_planning_info_entry_mst a
			INNER JOIN ppl_planning_info_entry_dtls b ON a.id = b.mst_id
		WHERE
			a.status_active = 1
			AND a.is_deleted = 0
			AND a.is_sales = 2
			AND a.company_id = ".$company_id."
			AND b.status_active = 1
			AND b.is_deleted = 0
			AND b.is_sales = 2
			".$buyer_id_cond."
			".$booking_cond."
		GROUP BY
			a.id, a.buyer_id, a.booking_no, ".$groupby_year."
			b.program_date
		ORDER BY
			a.id DESC
	";
	//echo $sql;
	echo create_list_view("tbl_list_search", "Buyer Name,Year,Booking No,Program Date", "150,80,120,100", "500", "220", 0, $sql, "js_set_value", "id,booking_no", "", 1, "buyer_id,0,0,0", $arr, "buyer_id,year,booking_no,program_date", "", '', '0,0,0,0', '', 1);

	exit();
}

/*
|--------------------------------------------------------------------------
| report_generate
|--------------------------------------------------------------------------
|
*/
if ($action == "report_generate")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$company_name = $cbo_company_name;

	//buyer_id_cond
	$buyer_id_cond = '';
	if (str_replace("'", "", $cbo_buyer_name) == 0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"] == 1)
		{
			if ($_SESSION['logic_erp']["buyer_id"] >0)
				$buyer_id_cond = " AND a.buyer_id IN (".$_SESSION['logic_erp']["buyer_id"].")";
		}
	}
	else
	{
		$buyer_id_cond = " AND a.buyer_id = ".$cbo_buyer_name."";
	}

	//booking_no_cond
	$booking_no_cond = '';
	$booking_no = str_replace("'", "", $txt_booking_no);
	if ($booking_no != '')
		$booking_no_cond = " AND a.booking_no = '".trim($booking_no)."'";

	$type = str_replace("'", "", $cbo_type);
	$planning_status = str_replace("'", "", $cbo_planning_status);

	//machine_dia
	$machine_dia = '%%';
	if (str_replace("'", "", $txt_machine_dia) != "")
		$machine_dia = "%".str_replace("'", "", $txt_machine_dia)."%";

	//internal reference
	$internal_ref_cond = '';
	$internal_ref = str_replace("'", "", $txt_internal_ref);
	if ($internal_ref != '')
		$internal_ref_cond = " AND d.internal_ref = '".trim($internal_ref)."'";

	// $sql = "select a.company_id, a.buyer_id, a.booking_no, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.status, b.start_date, b.end_date,b.color_id,$po_id_list from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_name and b.knitting_source=$type and machine_dia like '$machine_dia' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.is_sales!=1 $buyer_id_cond  $booking_no_cond $po_to_booking_no_cond group by a.company_id, a.buyer_id, a.booking_no, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.status, b.start_date, b.end_date,b.color_id order by b.machine_dia,b.machine_gg";
	//main query
	
	$sql = "SELECT a.company_id, a.buyer_id, a.booking_no, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.status, b.start_date, b.end_date, b.color_id, d.internal_ref FROM ppl_planning_info_entry_mst a INNER JOIN ppl_planning_info_entry_dtls b ON a.id = b.mst_id INNER JOIN ppl_planning_entry_plan_dtls c ON b.id = c.dtls_id LEFT JOIN sample_development_yarn_dtls e ON  e.booking_no = c.booking_no LEFT JOIN sample_development_mst d on e.mst_id = d.id  WHERE   a.status_active = 1 AND a.is_deleted = 0 AND a.is_sales = 2 AND a.company_id = ".$company_name." AND b.status_active = 1 AND b.is_deleted = 0 AND b.is_sales = 2 AND b.knitting_source = ".$type." AND b.machine_dia LIKE '".$machine_dia."' AND c.is_deleted = 0 AND c.status_active = 1 AND c.is_sales = 2 ".$buyer_id_cond." ".$booking_no_cond." ".$internal_ref_cond." GROUP BY a.company_id, a.buyer_id, a.booking_no, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.status, b.start_date, b.end_date, b.color_id, d.internal_ref ORDER BY b.machine_dia, b.machine_gg";
	// echo $sql; die;
	$nameArray = sql_select($sql);
	if(empty($nameArray))
	{
		?>
        <div style="width:100%; margin-top:10px;" align="center"><?php echo get_empty_data_msg(); ?></div>
        <?php
		die;
	}

	foreach ($nameArray as $row)
	{
		$plan_arr[$row[csf("id")]] = $row[csf("id")];
		$po_id_arr[$row[csf("po_id")]] = $row[csf("po_id")];
	}

	if(!empty($plan_arr))
	{
		if ($db_type == 0)
		{
			$plan_details_array = return_library_array("select dtls_id, group_concat(distinct(po_id)) as po_id from ppl_planning_entry_plan_dtls where company_id=$company_name and dtls_id in(".implode(",",$plan_arr).") group by dtls_id", "dtls_id", "po_id");
		}
		else
		{
			$plan_details_array = return_library_array("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company_name and dtls_id in(".implode(",",$plan_arr).") group by dtls_id", "dtls_id", "po_id");
		}
	}

	$po_array = array();
	if(!empty($po_id_arr))
	{
		$costing_sql = sql_select("select a.job_no, a.style_ref_no,file_no,grouping, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_name and b.id in(".implode(",",$po_id_arr).")");
		foreach ($costing_sql as $row) {
			$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
			$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
			$po_array[$row[csf('id')]]['file'] = $row[csf('file_no')];
			$po_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		}
	}

	if($plan_arr!="")
	{

		$reqs_sql = sql_select("select knit_id, requisition_no as reqs_no, sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 and knit_id in(".implode(",",$plan_arr).") group by knit_id, requisition_no");
		$reqs_array = array();
		foreach ($reqs_sql as $row)
		{
			$reqs_array[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
			$reqs_array[$row[csf('knit_id')]]['qnty'] = $row[csf('yarn_req_qnty')];
		}
	}

	$color = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	?>
	<fieldset style="width:1840px; margin-top:10px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1840" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="80">Program No</th>
				<th width="80">Program Date</th>
				<th width="70">Buyer</th>
				<th width="80"><? echo $company_arr[str_replace("'", "", $company_name)]; ?></th>
				<th width="110">Style</th>
				<th width="100">Booking No.</th>
				<th width="80">IR/CN</th>
				<th width="80">Dia / GG</th>
				<th width="145">Fabric Desc.</th>
				<th width="70">Fabric Gsm</th>
				<th width="70">Fabric Dia</th>
				<th width="80">Width/Dia Type</th>
				<th width="80">Fab Color</th>
				<th width="90">Color Range</th>
				<th width="100">Program Qnty</th>
				<th width="105">Yarn Req. Qnty</th>
				<th width="70">Req. No</th>
				<th width="80">Start Date</th>
				<th width="80">T.O.D</th>
				<th>Status</th>
			</thead>
		</table>
		<div style="width:1840px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1840" class="rpt_table"
			id="tbl_list_search">
			<tbody>
			<?
            $i = 1;
            $k = 1;
            $tot_program_qnty = 0;
            $tot_yarn_req_qnty = 0;
            $machine_dia_gg_array = array();
            foreach ($nameArray as $row)
            {
                $machine_dia_gg = $row[csf('machine_dia')] . 'X' . $row[csf('machine_gg')];
                $yarn_req_qnty = $reqs_array[$row[csf('id')]]['qnty'];
                $reqs_no = $reqs_array[$row[csf('id')]]['reqs_no'];
                $balance_qnty = $row[csf('program_qnty')] - $yarn_req_qnty;

                if (($planning_status == 3 && $balance_qnty <= 0) || ($planning_status == 1 && $balance_qnty > 0))
                {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    if (!in_array($machine_dia_gg, $machine_dia_gg_array))
                    {
                        if ($k != 1)
                        {
                            ?>
                            <tr bgcolor="#CCCCCC">
                            <td colspan="13" align="right"><b>Sub Total</b></td>
                            <td align="right"><b><? echo number_format($sub_tot_program_qnty, 2, '.', ''); ?></b></td>
                            <td align="right">
                                <b><? echo number_format($sub_tot_yarn_req_qnty, 2, '.', ''); ?></b></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <?
                            $sub_tot_program_qnty = 0;
                            $sub_tot_yarn_req_qnty = 0;
                        }

                        ?>
                        <tr bgcolor="#EFEFEF">
                            <td colspan="23"><b>Machine Dia:- <?php echo $machine_dia_gg; ?></b></td>
                        </tr>
                        <?
                        $machine_dia_gg_array[] = $machine_dia_gg;
                        $k++;
                    }

                    $po_id = array_unique(explode(",", $plan_details_array[$row[csf('id')]]));
                    $po_no = '';
                    $style_ref = '';
                    $job_no = '';
                    $int_ref = '';
                    $file_no = '';

                    foreach ($po_id as $val)
                    {
                        if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= "," . $po_array[$val]['no'];
                        if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
                        $job_no .= $po_array[$val]['job_no'].",";
                        if ($int_ref == '') $int_ref = $po_array[$val]['ref']; else $int_ref .= "," . $po_array[$val]['ref'];
                        if ($file_no == '') $file_no = $po_array[$val]['file']; else $file_no .= "," . $po_array[$val]['file'];
                    }
                    $job_no = rtrim(implode(",", array_unique(explode(",", $job_no))),", ");
                    $fab_color = explode(",", $row[csf('color_id')]);
                    $color_arr=array();
                    foreach ($fab_color as $value)
                    {
                        $color_arr[] =$color[$value];
                    }
                    $cons_comps = explode(",", $row[csf('fabric_desc')]);
                    //have to work
                    $comps = $cons_comps[1];
                    $comps = $cons_comps[0];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"
                        onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="80">&nbsp;&nbsp;<? echo $row[csf('id')]; ?>&nbsp;</td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('program_date')]); ?></td>
                        <td width="70"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>

                        <td width="80"><p><? echo $plan_details_array[$row[csf('id')]]; ?></p></td>
                        <td width="110"><p><? echo $style_ref; ?></p></td>

                        <td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="80"><p><? echo $row[csf('internal_ref')]; ?></p></td>
                        <td width="80"><p><? echo $machine_dia_gg; ?></p></td>
                        <td width="145"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
                        <td width="70"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
                        <td width="70"><p><? echo $row[csf('dia')]; ?></p></td>
                        <td width="80"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
                        <td width="80" style="word-break: break-all;"><? echo implode(",",$color_arr); ?></td>
                        <td width="90"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
                        <td align="right" width="100"><? echo number_format($row[csf('program_qnty')], 2); ?></td>
                        <td align="right" width="105">
                            <input type="text" name="txt_yarn_req_qnty[]" id="txt_yarn_req_qnty_<? echo $i; ?>"
                            style="width:90px" class="text_boxes_numeric" readonly
                            value="<? if ($yarn_req_qnty > 0) echo number_format($yarn_req_qnty, 2); ?>"
                            placeholder="Single Click"
                            onClick="openmypage_yarnReq(<? echo $i; ?>,'<? echo $row[csf('id')]; ?>',<? echo $company_name; ?>,'<? echo $comps; ?>','<? echo $reqs_no; ?>','<? echo $row[csf('booking_no')]; ?>','<? echo $plan_details_array[$row[csf('id')]]; ?>','<? echo $row[csf('program_qnty')]; ?>')"/>
                        </td>
                        <td align="center"
                        width="70"><? echo "<a href='##' onclick=\"generate_report2(" . $row[csf('company_id')] . "," . $row[csf('id')] . ")\">$reqs_no </a>"
                        ?></td>
                        <td width="80" align="center">
                            &nbsp;<? if ($row[csf('start_date')] != "" && $row[csf('start_date')] != "0000-00-00") echo change_date_format($row[csf('start_date')]); ?></td>
                            <td width="80" align="center">
                                &nbsp;<? if ($row[csf('end_date')] != "" && $row[csf('end_date')] != "0000-00-00") echo change_date_format($row[csf('end_date')]); ?></td>
                                <td><p><? echo $knitting_program_status[$row[csf('status')]]; ?>&nbsp;</p></td>
                            </tr>
                            <?
                            $sub_tot_program_qnty += $row[csf('program_qnty')];
                            $sub_tot_yarn_req_qnty += $yarn_req_qnty;

                            $tot_program_qnty += $row[csf('program_qnty')];
                            $tot_yarn_req_qnty += $yarn_req_qnty;

                            $i++;
                        }
                    }
                    if ($i > 1) {
                        ?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="15" align="right"><b>Sub Total</b></td>
                            <td align="right"><b><? echo number_format($sub_tot_program_qnty, 2, '.', ''); ?></b></td>
                            <td align="right"><b><? echo number_format($sub_tot_yarn_req_qnty, 2, '.', ''); ?></b></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <?
                    }
                    ?>
                </tbody>
                <tfoot>
                    <th colspan="15" align="right">Grand Total</th>
                    <th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?></th>
                    <th align="right"><? echo number_format($tot_yarn_req_qnty, 2, '.', ''); ?></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    exit();
}

/*
|--------------------------------------------------------------------------
| yarn_req_qnty_popup
|--------------------------------------------------------------------------
|
*/
if ($action == "yarn_req_qnty_popup")
{
	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$auto_allocate_yarn_from_requis = return_field_value("auto_allocate_yarn_from_requis", "variable_settings_production", "company_name = ".$companyID." AND variable_list = 6 AND status_active = 1 AND is_deleted = 0", "auto_allocate_yarn_from_requis");
	?>
	<script>
		var permission = '<? echo $permission; ?>';

		function calculate(field_id)
		{
			var txt_no_of_cone = $('#txt_no_of_cone').val() * 1;
			var txt_weight_per_cone = $('#txt_weight_per_cone').val() * 1;
			var txt_yarn_qnty = $('#txt_yarn_qnty').val() * 1;

			if (field_id == "txt_yarn_qnty")
			{
				if (txt_no_of_cone > 0)
				{
					var weightPerCone = txt_yarn_qnty / txt_no_of_cone;
					$('#txt_weight_per_cone').val(weightPerCone.toFixed(2));
				}
				else
				{
					$('#txt_weight_per_cone').val('');
				}
			}
			else
			{
				if (txt_weight_per_cone == "" && txt_yarn_qnty != "")
				{
					if (txt_no_of_cone > 0)
					{
						var weightPerCone = txt_yarn_qnty / txt_no_of_cone;
						$('#txt_weight_per_cone').val(weightPerCone.toFixed(2));
					}
					else
					{
						$('#txt_weight_per_cone').val('');
					}
				}
				else
				{
					var yarnQnty = txt_no_of_cone * txt_weight_per_cone;
					$('#txt_yarn_qnty').val(yarnQnty);
				}
			}
		}

		function openpage_lot()
		{
			var is_auto_allocation_from_requisition = $("#is_auto_allocation_from_requisition").val();
			var title = 'Lot Info';
			var page_link = 'yarn_requisition_entry_for_sample_without_order_controller.php?action=lot_info_popup&companyID=' + '<? echo $companyID; ?>' + '&knit_dtlsId=' + '<? echo $knit_dtlsId; ?>' + '&comps=' + '<? echo $comps; ?>' + '&job_no=' + '<? echo $job_no; ?>' + '&is_auto_allocation_from_requisition=' + is_auto_allocation_from_requisition+'&selected_booking_no=' + '<? echo $booking_no;?>';
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=350px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function ()
			{
				var theform = this.contentDoc.forms[0];
				var prod_id = this.contentDoc.getElementById("hidden_prod_id").value;
				var data = this.contentDoc.getElementById("hidden_data").value.split("**");


				$('#prod_id').val(prod_id);
				$('#txt_lot').val(data[0]);
				$('#cbo_yarn_count').val(data[1]);
				$('#cbo_yarn_type').val(data[2]);
				$('#txt_color').val(data[3]);
				$('#txt_composition').val(data[4]);
				$('#is_dyed_yarn').val(data[5]);

				if($('#prod_id').val() != $('#original_prod_id').val())
				{
					$('#txt_yarn_qnty').val('');
				}

				//$data = $row[csf('lot')] . "**" . $row[csf('yarn_count_id')] . "**" . $row[csf('yarn_type')] . "**" . $color_library[$row[csf('color')]] . "**" . $compos . "**2**" . $bal_alloc_qnty;

				if(is_auto_allocation_from_requisition==1) // yes
				{
					$('#available_qnty').val(data[6]);
				}
				else
				{
					if (data[6] != "" || data[6] != 0)
					{
						$('#hidden_yarn_req_qnty').val(data[6]);
					}
				}

				$('#dyed_yarn_qnty_from').val(data[7]);
			}
		}

		function open_qnty_popup()
		{
			var txt_company_id = document.getElementById('txt_company_id').value;
			var txt_order_id = document.getElementById('txt_order_id').value;
			var txt_item = document.getElementById('txt_lot').value;
			var prod_id = document.getElementById('prod_id').value;
			var is_dyed_yarn = document.getElementById('is_dyed_yarn').value;
			var available_qnty = document.getElementById('available_qnty').value;


			var txt_qnty = document.getElementById('txt_yarn_qnty').value;
			var qnty_breck_down = document.getElementById('qnty_breck_down').value;
			var txt_booking_qnty = document.getElementById('txt_booking_qnty').value;
			var txt_job_no = document.getElementById('txt_job_no').value;
			var txt_booking_no = document.getElementById('txt_booking_no').value;
			var txt_old_qnty = document.getElementById('txt_old_qnty').value;
			var txt_yarn_qnty = document.getElementById('txt_yarn_qnty').value;

	/*if(update_id == ""){
		var txt_selectted_fabric = document.getElementById('txt_selectted_fabric').value;
		var txt_fabric_po = document.getElementById('txt_fabric_po').value;
		var txt_fab_booking_qnty = document.getElementById('txt_fab_booking_qnty').value;
	}else{
		var txt_selectted_fabric = "";
		var txt_fabric_po = "";
		var txt_fab_booking_qnty = "";
	}*/

	/*if (txt_entry_form != 108) {
		if (txt_order_id == 0) {
			alert("Select Order No");
			return;
		}
	}*/

	if (txt_item == "") {
		alert("You did not select any Yarn");
		document.getElementById('txt_lot').focus();
		return;
	}

	var title = 'Qnty List';
	page_link = 'yarn_requisition_entry_for_sample_without_order_controller.php?action=open_qnty_popup';

	page_link = page_link + '&txt_order_id=' + txt_order_id + '&txt_booking_qnty=' + txt_booking_qnty + '&txt_job_no=' + txt_job_no + '&txt_booking_no=' + txt_booking_no + '&txt_company_id='+ txt_company_id+ '&available_qnty=' + available_qnty + '&txt_qnty=' + txt_qnty +'&txt_old_qnty=' + txt_old_qnty +'&qnty_breck_down=' + qnty_breck_down + '&txt_item='+txt_item + '&prod_id='+prod_id + '&is_dyed_yarn='+is_dyed_yarn;
	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=350px,center=1,resize=0,scrolling=0', '../')
	emailwindow.onclose = function () {
		var theform = this.contentDoc.forms[0];
		var theemail = this.contentDoc.getElementById("allocated_qnty");
		var theemail_number = this.contentDoc.getElementById("qnty_breck_down");
		if (theemail.value != "") {
			freeze_window(5);
			document.getElementById('txt_yarn_qnty').value = theemail.value;
			document.getElementById('qnty_breck_down').value = theemail_number.value;
			release_freezing();
		}
	}
		}

		function openpage_distribution()
		{
			var prod_id = $('#prod_id').val();
			var txt_yarn_qnty = $('#txt_yarn_qnty').val();
			var hdn_distribution_qnty_breakdown = $('#hdn_distribution_qnty_breakdown').val();
			var txt_distribution_qnty = $('#txt_distribution_qnty').val();
			if(prod_id == ""){
				alert("You did not select any Lot");
				return;
			}
			if(txt_yarn_qnty == ""){
				$('#txt_yarn_qnty').focus();
				alert("Requisition quantity can not be empty");
				return;
			}

			var page_link = 'yarn_requisition_entry_for_sample_without_order_controller.php?action=distribution_popup&companyID=' + '<? echo $companyID; ?>' + '&requisition_no=' + '<? echo $reqs_no; ?>' + '&program_qnty=' + '<? echo $program_qnty; ?>' + '&po_ids=' + '<? echo $po_ids; ?>' + '&prod_id=' + prod_id+ '&hdn_distribution_qnty_breakdown=' + hdn_distribution_qnty_breakdown+'&knit_dtlsId=' + '<? echo $knit_dtlsId; ?>'+'&txt_distribution_qnty='+txt_distribution_qnty+'&yarn_req_qnty='+txt_yarn_qnty;

			var title = 'Distribution Info';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=200px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var hidden_prog_qnty = this.contentDoc.getElementById("hidden_prog_qnty").value;
				var hidden_dist_qnty = this.contentDoc.getElementById("hidden_dist_qnty").value;
				var hidden_dist_qnty_breakdown = this.contentDoc.getElementById("hidden_dist_qnty_breakdown").value;
				if(hidden_dist_qnty*1 > hidden_prog_qnty*1){
					return;
				}
				$("#txt_distribution_qnty,#hdn_distribution_qnty").val(hidden_dist_qnty);
				$("#hdn_distribution_qnty_breakdown").val(hidden_dist_qnty_breakdown);
			}
		}

		function fnc_yarn_req_entry(operation)
		{
			var is_auto_allocation_from_requisition = $("#is_auto_allocation_from_requisition").val();
			var qnty_breck_down = $("#qnty_breck_down").val();
			var txt_old_qnty = $("#txt_old_qnty").val();

			var hidden_yarn_req_qnty = parseFloat(document.getElementById("hidden_yarn_req_qnty").value);
			var txt_yarn_qnty = parseFloat(document.getElementById("txt_yarn_qnty").value);

			var is_dist_qnty_valiable_set = parseFloat(document.getElementById("is_dist_qnty_valiable_set").value);
			if (operation == 1) {
				if (txt_yarn_qnty > (txt_yarn_qnty + hidden_yarn_req_qnty)) {
					alert("Requisition Quantity is not available");
					return;
				}
			} else {
				if (txt_yarn_qnty > hidden_yarn_req_qnty) {
					alert("Requisition Quantity is not available");
					return;
				}
			}

			// if(txt_yarn_qnty<1 || txt_yarn_qnty=="")
			// {
			// 	$("#txt_yarn_qnty").val('');
			// 	$("#txt_yarn_qnty").focus();
			// 	return;
			// }

			if(is_dist_qnty_valiable_set == 1)
			{
				if(operation==0)
				{
					if (form_validation('txt_lot*txt_yarn_qnty*txt_reqs_date*txt_distribution_qnty', 'Lot*Yarn Qnty*Requisition Date* Distribution Qnty') == false){
						return;
					}
				}
				if(operation==1)
				{
					if (form_validation('txt_lot*txt_reqs_date*txt_distribution_qnty', 'Lot*Requisition Date* Distribution Qnty') == false)
					{
						return;
					}
				}
				
				var data = "action=save_update_delete&operation=" + operation + "&is_auto_allocation_from_requisition="+is_auto_allocation_from_requisition+ "&qnty_breck_down="+qnty_breck_down+ "&txt_old_qnty="+txt_old_qnty+ get_submitted_data_string('prod_id*txt_no_of_cone*txt_reqs_date*txt_yarn_qnty*updateId*update_dtls_id*txt_requisition_no*is_dyed_yarn*companyID*booking_no*txt_job_no*original_prod_id*txt_po_ids*hdn_distribution_qnty*hdn_distribution_qnty_breakdown*is_dist_qnty_valiable_set*pre_qnty_breck_down*dyed_yarn_qnty_from', "../../");
			}
			else
			{
				if(operation==0)
				{
					if (form_validation('txt_lot*txt_yarn_qnty*txt_reqs_date', 'Lot*Yarn Qnty*Requisition Date* Distribution Qnty') == false)
					{
						return;
					}
				}
				if(operation==1)
				{
					if (form_validation('txt_lot*txt_reqs_date', 'Lot*Requisition Date* Distribution Qnty') == false)
					{
						return;
					}
				}
				var data = "action=save_update_delete&operation=" + operation + "&is_auto_allocation_from_requisition="+is_auto_allocation_from_requisition+"&qnty_breck_down="+qnty_breck_down+ "&txt_old_qnty="+txt_old_qnty+  get_submitted_data_string('prod_id*txt_no_of_cone*txt_reqs_date*txt_yarn_qnty*updateId*update_dtls_id*txt_requisition_no*is_dyed_yarn*companyID*booking_no*txt_job_no*original_prod_id*txt_po_ids*is_dist_qnty_valiable_set*pre_qnty_breck_down*dyed_yarn_qnty_from', "../../");
			}

			freeze_window(operation);

			http.open("POST", "yarn_requisition_entry_for_sample_without_order_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_req_entry_Reply_info;
		}

		function fnc_yarn_req_entry_Reply_info()
		{
			if (http.readyState == 4) {
				var reponse = trim(http.responseText).split('**');

				if (reponse[0] == 11) {
					alert("Duplicate Item Not Allowed");
				}
				else if (reponse[0] == 17) {
					if(reponse[3]==1) // Dyed Yarn
					{
						alert("Requisition Quantity can not be greater than Allocation Quantity.\nAllocation quantity = " + reponse[1]);
					}else{
						alert("Requisition Quantity can not be greater than Allocation Quantity.\nOrder wise Allocation quantity = " + reponse[1]);
					}
				}
				else if (reponse[0] == 18) {
					alert(reponse[1]);
				}
				else if (reponse[0] == 7) {
					alert(reponse[1]);
				}
				else if (reponse[0] == 6) {
					//alert(reponse[1]);
				}
				else if(reponse[0] == 12)
				{
					alert('Distribution quantity can not be greater than requisition quantity');
				}
				else {
					show_msg(reponse[0]);
					if ((reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2)) {
						reset_form('yarnReqQnty_1', '', '', '', '', 'updateId*booking_no*txt_job_no*txt_po_ids*companyID*is_dist_qnty_valiable_set*txt_booking_no*txt_company_id*txt_order_id*txt_booking_qnty*is_auto_allocation_from_requisition');
						$('#txt_requisition_no').val(reponse[3]);
						$('#hide_req_no').val(reponse[3]);
						show_list_view(reponse[1], 'requisition_info_details', 'list_view', 'yarn_requisition_entry_for_sample_without_order_controller', '');
					}
				}
				set_button_status(reponse[2], permission, 'fnc_yarn_req_entry', 1);
				release_freezing();
			}
		}

		function fnc_close()
		{
			parent.emailwindow.hide();
		}

		function generate_report_print(company_id, program_id,$type)
		{
			var cbo_template_id = $('#cbo_template_id').val();
			var req_no = $('#hide_req_no').val();
			if (req_no != "") {

				if($type==1)
				{
					print_report(company_id + '*' + program_id+ '*' + cbo_template_id, "print_popup", "yarn_requisition_entry_for_sample_without_order_controller");
				}else{
					print_report(company_id + '*' + program_id+ '*' + cbo_template_id, "requisition_print", "yarn_requisition_entry_for_sample_without_order_controller");
				}
			}
			else {
				alert("Save Data First");
				return;
			}
		}
		</script>
	</head>
    <body>
        <div align="center">
        <div><? echo load_freeze_divs("../../", $permission, 1); ?></div>
        <form name="yarnReqQnty_1" id="yarnReqQnty_1">
        <?
		$distribute_qnty_variable ="";
		if(trim($companyID)!="")
		{
        $distribute_qnty_variable = return_field_value("distribute_qnty", "variable_settings_production", "company_name='$companyID' and variable_list=5 and status_active=1 and is_deleted=0", "distribute_qnty");
		}
        ?>
        <input type="hidden" id="is_dist_qnty_valiable_set" value="<? echo $distribute_qnty_variable;?>" readonly/>
        <input type="hidden" id="is_auto_allocation_from_requisition" value="<? echo $auto_allocate_yarn_from_requis;?>" readonly/>
        <input type="hidden" id="dyed_yarn_qnty_from" value="" readonly/>

        <fieldset style="width:800px; margin-top:10px">
            <legend>New Entry</legend>
            <table width="800" align="center" border="0">
                <tr>
                    <td colspan="3" align="right"><strong>Requisition No</strong></td>
                    <td colspan="3" align="left">
                        <input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes"	style="width:130px;" placeholder="Display" disabled/>
                        <input type="hidden" name="hide_req_no" id="hide_req_no" class="text_boxes" value="<? echo $reqs_no; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Lot</td>
                    <td>
                        <input type="text" name="txt_lot" id="txt_lot" class="text_boxes" placeholder="Double Click" style="width:130px;" onDblClick="openpage_lot();" readonly/>
                        <input type="hidden" name="prod_id" id="prod_id" class="text_boxes" readonly/>
                        <input type="hidden" name="old_prod_id" id="old_prod_id" class="text_boxes" readonly/>
                        <input type="hidden" name="original_prod_id" id="original_prod_id" class="text_boxes" readonly/>
                        <input type="hidden" name="hidden_yarn_req_qnty" id="hidden_yarn_req_qnty" class="text_boxes" readonly/>
                        <input type="hidden" name="hidden_lot_available_qnty" id="hidden_lot_available_qnty" readonly/>
                        <input type="hidden" name="companyID" id="companyID" value="<? echo $companyID; ?>" readonly/>
                    </td>
                    <td>Yarn Count</td>
                    <td>
                    <?
                    echo create_drop_down("cbo_yarn_count", 142, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC", "id,yarn_count", 1, "Display", 0, "", 1);
                    ?>
                    </td>
                    <td>Yarn Type</td>
                    <td>
                    <? echo create_drop_down("cbo_yarn_type", 142, $yarn_type, "", 1, "Display", 0, "", 1); ?>
                    </td>
                </tr>
                <tr>
                    <td>Composition</td>
                    <td colspan="3">
                    <input type="text" name="txt_composition" id="txt_composition" class="text_boxes"
                    placeholder="Display" style="width:390px;" disabled/>
                    </td>
                    <td>Color</td>
                    <td>
                    <input type="text" name="txt_color" id="txt_color" class="text_boxes" placeholder="Display"
                    style="width:130px;" disabled/>
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Yarn Reqs. Qnty</td>
                    <td>
                    <?php
                    if($auto_allocate_yarn_from_requis==1) // Yes
                    {
						?>
                        <input type="hidden" name="txt_company_id" id="txt_company_id"  value="<?php echo $companyID; ?>"  readonly="" />
                        <input type="hidden" name="txt_booking_no" id="txt_booking_no"  value="<?php echo $booking_no; ?>"  readonly="" />
                        <input type="hidden" name="txt_booking_qnty" id="txt_booking_qnty"  value="<?php echo $program_qnty; ?>"  readonly="" />
                        <input type="hidden" name="txt_job_no" id="txt_job_no"  value="<?php echo $job_no; ?>"  readonly="" />
                        <input type="hidden" name="txt_order_id" id="txt_order_id"  value="<?php echo $po_ids; ?>"  readonly="" />
                        <input type="hidden" name="txt_knit_dtls_id" id="txt_knit_dtls_id"  value="<?php echo $knit_dtlsId; ?>"  readonly="" />

                        <input type="hidden" name="txt_fab_booking_qnty" id="txt_fab_booking_qnty" value="" />
                        <input type="hidden" name="txt_selectted_fabric" id="txt_selectted_fabric" value="" />
                        <input type="hidden" name="txt_fabric_po" id="txt_fabric_po" value="" />

                    	<!--<input name="txt_yarn_qnty" id="txt_yarn_qnty" style="width:130px;text-align: left;" value="" class="text_boxes_numeric" placeholder="Click" onClick="open_qnty_popup()" type="text">-->
						<input type="text" name="txt_yarn_qnty" id="txt_yarn_qnty" class="text_boxes_numeric" style="width:130px;"/>
                    <?
                    }
                    else
                    {
						?>
						<input type="text" name="txt_yarn_qnty" id="txt_yarn_qnty" class="text_boxes_numeric" style="width:130px;"/>
						<?
                    }
                    ?>

                    <input type="hidden" name="available_qnty" id="available_qnty" style="width:90px;" value="" class="text_boxes_numeric" readonly/>
                    <input type="hidden" name="txt_old_qnty" id="txt_old_qnty" style="width:90px " value="" class="text_boxes_numeric"/>
                    <input type="hidden" name="qnty_breck_down" id="qnty_breck_down" style="width:90px;" class="text_boxes" />
                    <input type="hidden" name="pre_qnty_breck_down" id="pre_qnty_breck_down" style="width:90px;" class="text_boxes" />
                    </td>
                    <td>No of Cone</td>
                    <td>
                    <input type="text" name="txt_no_of_cone" id="txt_no_of_cone" class="text_boxes_numeric" style="width:130px;"/>
                    </td>
                    <td class="must_entry_caption">Requisition Date</td>
                    <td>
                    <input type="text" name="txt_reqs_date" id="txt_reqs_date" class="datepicker" value="<? echo date("d-m-Y")?>" style="width:130px;" readonly/>
                    </td>
                </tr>
                <?
				//$distribute_qnty_variable = 1;
                if($distribute_qnty_variable == 1)
				{
                ?>
                <tr>
                    <td class="must_entry_caption">Distribution Qnty</td>
                    <td>
                    <input type="text" name="txt_distribution_qnty" id="txt_distribution_qnty" class="text_boxes_numeric" placeholder="Double Click" style="width:130px;" onDblClick="openpage_distribution();" readonly />
                    <input type="hidden" name="hdn_distribution_qnty" id="hdn_distribution_qnty" readonly/>
                    <input type="hidden" name="hdn_distribution_qnty_breakdown" id="hdn_distribution_qnty_breakdown" readonly/>
                    </td>
                </tr>
                <?
                }
                else
                {
					?>
					<input type="hidden" name="txt_distribution_qnty" id="txt_distribution_qnty" placeholder="Double Click" style="width:130px;" readonly />
					<input type="hidden" name="hdn_distribution_qnty" id="hdn_distribution_qnty" readonly/>
					<input type="hidden" name="hdn_distribution_qnty_breakdown" id="hdn_distribution_qnty_breakdown" readonly/>
					<?
                }
                ?>
                <tr>
                    <td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="6" align="center" class="button_container">
                    <?php echo load_submit_buttons($permission, "fnc_yarn_req_entry", 0, 0, "reset_form('yarnReqQnty_1','','','','','updateId');", 1); ?>

                    <? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "check_company();");?>

                    <input type="button" name="close" class="formbutton" value="Close" id="main_close"
                    onClick="fnc_close();" style="width:100px"/>

                    <input type="button" name="btn_print" class="formbutton" value="Print" id="btn_print"
                    onClick="generate_report_print('<? echo str_replace("'",'',$companyID); ?>','<? echo str_replace("'", '', $knit_dtlsId); ?>',1)"
                    style="width:100px"/>

                    <input type="button" name="btn_print_2" class="formbutton" value="Print2" id="btn_print_2"
                    onClick="generate_report_print('<? echo str_replace("'",'',$companyID); ?>','<? echo str_replace("'", '', $knit_dtlsId); ?>',2)"
                    style="width:100px"/>

                    <input type="hidden" name="updateId" id="updateId" class="text_boxes"
                    value="<? echo str_replace("'", '', $knit_dtlsId); ?>">
                    <input type="hidden" name="update_dtls_id" id="update_dtls_id" class="text_boxes">
                    <input type="hidden" name="is_dyed_yarn" id="is_dyed_yarn" class="text_boxes">
                    <input type="hidden" name="booking_no" id="booking_no" class="text_boxes" value="<? echo str_replace("'",'',$booking_no); ?>">
                    <input type="hidden" name="txt_job_no" id="txt_job_no" class="text_boxes" value="<? echo str_replace("'", '', $job_no); ?>">
                    <input type="hidden" name="txt_po_ids" id="txt_po_ids" class="text_boxes" value="<? echo str_replace("'", '', $po_ids); ?>">
                    </td>
                </tr>
			</table>
        </fieldset>
        <div id="list_view" style="margin-top:10px">
        <?
        if (str_replace("'", '', $knit_dtlsId) != "")
		{
			?>
			<script>
			show_list_view('<? echo str_replace("'", '', $knit_dtlsId); ?>', 'requisition_info_details', 'list_view', 'yarn_requisition_entry_for_sample_without_order_controller', '');
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
| lot_info_popup
|--------------------------------------------------------------------------
|
*/
if ($action == "lot_info_popup")
{
		echo load_html_head_contents("Lot Info", "../../", 1, 1, '', '', '');
		extract($_REQUEST);
		?>
		<script>
			$(document).ready(function (e) {
				setFilterGrid('tbl_list_search', -1);
				setFilterGrid('tbl_list_search_dyied', -1);
			});

			function js_set_value(id, data)
			{
				$('#hidden_prod_id').val(id);
				$('#hidden_data').val(data);
				parent.emailwindow.hide();
			}
		</script>
	</head>
	<body>
	<?php
	if($is_auto_allocation_from_requisition == 1) // Yes
	{
		$count_arr = return_library_array("SELECT id, yarn_count FROM lib_yarn_count", 'id', 'yarn_count');
		$supplier_arr = return_library_array("SELECT id, supplier_name FROM lib_supplier", 'id', 'supplier_name');
		$prod_data = sql_select("SELECT id, supplier_id, yarn_count_id, yarn_type FROM product_details_master WHERE item_category_id = 1 AND company_id = ".$companyID." AND status_active = 1 AND is_deleted = 0");
		foreach ($prod_data as $row)
		{
			$supplierArr[$row[csf('supplier_id')]] = $supplier_arr[$row[csf('supplier_id')]];
			$countArr[$row[csf('yarn_count_id')]] = $count_arr[$row[csf('yarn_count_id')]];
			$yarn_type_arr[$row[csf('yarn_type')]] = $yarn_type[$row[csf('yarn_type')]];
		}
		?>
		<div align="center" style="">

			<form name="searchfrm" id="searchfrm">
				<fieldset>
					<input type="hidden" name="hidden_prod_id" id="hidden_prod_id" class="text_boxes" value="">
					<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">
					<div><b><? echo $comps; ?></b></div>
					<table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
					class="rpt_table" id="tbl_list">
					<thead>
						<th>Supplier</th>
						<th>Count</th>
						<th>Yarn Description</th>
						<th>Type</th>
						<th>Lot</th>
						<th> Dyed Yarn Qty From </th>
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;"></th>
					</thead>
					<tbody>
						<tr align="center">
							<td><? echo create_drop_down("cbo_supplier", 130, $supplierArr, "", 1, "-- Select --", '', "", 0); ?></td>
							<td><? echo create_drop_down("cbo_count", 80, $countArr, "", 1, "-- Select --", '', "", 0); ?></td>
							<td><input type="text" name="txt_desc" id="txt_desc" class="text_boxes" style="width:150px">
							</td>
							<td><? echo create_drop_down("cbo_type", 110, $yarn_type_arr, "", 1, "-- Select --", '', "", 0); ?></td>
							<td><input type="text" name="txt_lot_no" id="txt_lot_no" class="text_boxes" style="width:70px">
							</td>


							<td>
								<?
								$cbo_dyed_yarn_qty_arr = array("1"=>"Allocated","2"=>"Available");
								echo create_drop_down("cbo_dyed_yarn_qty", 142, $cbo_dyed_yarn_qty_arr, "","0","--Select--","1");
								?>
							</td>

							<td>
								<input type="button" name="button" class="formbutton" value="Show"
								onClick="show_list_view (document.getElementById('cbo_supplier').value+'**'+document.getElementById('cbo_count').value+'**'+document.getElementById('txt_desc').value+'**'+document.getElementById('cbo_type').value+'**'+document.getElementById('txt_lot_no').value+'**'+<? echo $companyID; ?>+'**'+document.getElementById('cbo_dyed_yarn_qty').value , 'create_product_search_list_view', 'search_div', 'yarn_requisition_entry_for_sample_without_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
								style="width:70px;"/>
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:02px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	<?
	}
	else // No
	{
		$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		?>
		<div align="center" style="width:840px;">
			<form name="searchfrm" id="searchfrm">
				<fieldset style="width:830px;">
					<input type="hidden" name="hidden_prod_id" id="hidden_prod_id" class="text_boxes" value="">
					<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">
					<input type="hidden" name="available_qnty" id="available_qnty" value="" readonly />
					<div><b><? echo $comps; ?></b></div>
					<div style="float:left"><b><u>Allocated Grey Yarn</u></b></div>
					<table width="100%" border="1" rules="all" class="rpt_table">
						<thead>
							<th width="40">Sl No</th>
							<th width="120">Supplier</th>
							<th width="60">Count</th>
							<th width="230">Composition</th>
							<th width="80">Type</th>
							<th width="80">Color</th>
							<th width="80">Lot No</th>
							<th>Allocated Bl Qnty</th>
						</thead>
					</table>
					<div style="width:100%; overflow-y:scroll; max-height:140px;" id="scroll_body" align="left">
						<table class="rpt_table" rules="all" border="1" width="810" id="tbl_list_search">
							<?
							$job_nos = explode(",", rtrim($job_no,", "));
							foreach ($job_nos as $job)
							{
								$jobs .= "'".$job."',";
							}

							$sql_allo = "
								SELECT
									a.booking_no,b.item_id as prod_id, b.qnty as allocated_qnty,
									c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type, c.dyed_type
								FROM
									inv_material_allocation_mst a
									INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id
									INNER JOIN product_details_master c ON b.item_id = c.id
								WHERE
									a.status_active = 1
									AND a.is_deleted = 0
									AND a.booking_no = '".$selected_booking_no."'
									AND b.status_active = 1
									AND b.is_deleted = 0
									AND b.item_category = 1
									AND c.status_active = 1
									AND c.is_deleted = 0
									$testprodcond";
							//echo $sql_allo;
							$data_array = sql_select($sql_allo);
							$all_prod_id = '';
							foreach ($data_array as $row_allo)
							{
								$booking_no = $row_allo[csf('booking_no')];
								$yarn_count_id = $row_allo[csf('yarn_count_id')];
								$yarn_comp_type1st = $row_allo[csf('yarn_comp_type1st')];
								$yarn_comp_percent1st = $row_allo[csf('yarn_comp_percent1st')];
								$yarn_type_id = $row_allo[csf('yarn_type')];
								$product_type_arr[$row_allo[csf('prod_id')]] = $row_allo[csf('dyed_type')];

								if($row_allo[csf('dyed_type')] !=1 )
								{
									if ($all_prod_id == '')
										$all_prod_id = $row_allo[csf('prod_id')];
									else
										$all_prod_id .= "," . $row_allo[csf('prod_id')];

									if($row_allo[csf('booking_no')] != '' )
									{
										$booking_alocation_arr[$row_allo[csf('booking_no')]][$row_allo[csf('prod_id')]] += $row_allo[csf('allocated_qnty')];
									}
								}
							}

							/*echo "<pre>";
							print_r($booking_alocation_arr); die();*/

							$all_prod_id = implode(",",array_unique(explode(",", $all_prod_id)));
							if($all_prod_id !="" )
							{
								$prod_id_cond = "AND b.product_id IN(".$all_prod_id.")";
							}

							if($yarn_count_id!="")
							{
								$count_id_cond = "AND b.count = ".$yarn_count_id."";
							}

							if($yarn_comp_type1st!="")
							{
								$yarn_comp_type1st_cond = "AND b.yarn_comp_type1st = ".$yarn_comp_type1st."";
							}

							if($yarn_comp_percent1st!="")
							{
								$yarn_comp_percent1st_cond = "AND b.yarn_comp_percent1st = ".$yarn_comp_percent1st."";
							}

							$ydsw_sql="select x.booking_no,x.product_id,sum(x.yarn_wo_qty) yarn_wo_qty from(select b.booking_no,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(41,42,114,135,94) and b.entry_form in(41,42,114,135,94) and b.booking_no = '".$selected_booking_no."' $prod_id_cond group by b.booking_no,b.product_id
							union all
							select b.booking_no,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(125,340) and b.entry_form in(125,340) and b.booking_no = '".$selected_booking_no."' $prod_id_cond $count_id_cond $yarn_comp_type1st_cond $yarn_comp_percent1st_cond group by b.booking_no,b.product_id )x group by x.booking_no,x.product_id";

							//echo $ydsw_sql;
							$check_ydsw = sql_select($ydsw_sql);
							$prod_wise_ydsw=array();
							foreach ($check_ydsw as $row)
							{
								$prod_wise_ydsw[$row[csf("booking_no")]][$row[csf("product_id")]] = $row[csf("yarn_wo_qty")];
							}

							$all_booking_no = '';
							$get_job_booking = sql_select("select a.booking_no from wo_non_ord_samp_booking_mst a where a.booking_no='".$booking_no."' AND a.status_active=1 AND a.is_deleted=0 GROUP BY booking_no");
							foreach ($get_job_booking as $booking_row)
							{
								$all_booking_no .= "'" . $booking_row[csf('booking_no')] . "',";
							}
							$booking_nos = rtrim($all_booking_no, ',');

							if ($db_type == 0)
							{
								$all_knit_id = return_field_value("GROUP_CONCAT(DISTINCT(b.id)) AS knit_id", "ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b", "a.id = b.mst_id AND a.booking_no IN(".$booking_nos.") AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0", "knit_id");
							}
							else
							{
								$all_knit_id = return_field_value("LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as knit_id", "ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b", "a.id = b.mst_id AND a.booking_no IN(".$booking_nos.") AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0", "knit_id");
								$all_knit_id = implode(",", array_unique(explode(",", $all_knit_id)));
							}

							if ($all_prod_id != "")
							{
								$req_sql = "SELECT a.booking_no, c.knit_id, c.prod_id, c.requisition_no, c.yarn_qnty
								FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c
								WHERE a.id = b.mst_id AND b.id = c.knit_id AND b.id IN(".$all_knit_id.") AND c.prod_id IN(".$all_prod_id.") AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0";
								//echo $req_sql;
								$req_result = sql_select($req_sql);
								foreach($req_result as $row)
								{
									$product_type = $product_type_arr[$row[csf("prod_id")]];

									if($product_type!=1)
									{
										$booking_requsition_arr[$row[csf("booking_no")]][$row[csf("prod_id")]] += $row[csf("yarn_qnty")];
									}
								}


								$sql = "SELECT id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color FROM product_details_master WHERE company_id = ".$companyID." AND current_stock>0 AND id IN(".$all_prod_id.") AND item_category_id=1 AND status_active=1 AND is_deleted=0 ORDER BY id";
								//echo $sql;
								$result = sql_select($sql);

								$i = 1;
								$ydw_qty = 0;
								$job_total_allocation_qty = 0;
								$booking_total_allocation_qty = 0;
								$existing_requsition_qty = 0;
								$booking_issue_rtn_qty = 0;
								$balance = 0;
								$bal_alloc_qnty = 0;

								foreach ($result as $row)
								{
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";

									$compos = '';
									if ($row[csf('yarn_comp_percent2nd')] != 0)
									{
										$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
									}
									else
									{
										$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
									}

									//echo $selected_booking_requsition_qty."-".$selected_booking_issue_rtn_qty."<br>";

									$ydsw_qty = $prod_wise_ydsw[$selected_booking_no][$row[csf("id")]]*1;
									$existing_requsition_qty = $booking_requsition_arr[$selected_booking_no][$row[csf("id")]]*1;
									$booking_total_allocation_qty = $booking_alocation_arr[$selected_booking_no][$row[csf('id')]]*1;
									//echo $booking_total_allocation_qty."-".$ydsw_qty."+".$existing_requsition_qty;
									$balance = ( $booking_total_allocation_qty - ($ydsw_qty + $existing_requsition_qty) );
									if($balance>$booking_total_allocation_qty)
									{
										$cumalative_balance = $booking_total_allocation_qty;

									}
									else
									{
										if($balance>0)
										{
											$cumalative_balance = $balance;
										}
										else
										{
											$cumalative_balance = 0;
										}
									}

									$balance_title = "PROD ID->".$row[csf('id')]." WYDS QTY->".$ydsw_qty." Booking T.ALL QTY->".$booking_total_allocation_qty." PREV RQTY->".$existing_requsition_qty." Balance->".$balance ."\nBalance Formula (" .$booking_total_allocation_qty."-(".$ydsw_qty."+".$existing_requsition_qty."))" ;

									$bal_alloc_qnty = $cumalative_balance;
									$data = $row[csf('lot')] . "**" . $row[csf('yarn_count_id')] . "**" . $row[csf('yarn_type')] . "**" . $color_library[$row[csf('color')]] . "**" . $compos . "**2**" . $bal_alloc_qnty;
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="cursor:pointer"
										onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $data; ?>');">
										<td width="40" align="center"><? echo $i; ?></td>
										<td width="120" align="center"><p><? echo $supllier_arr[$row[csf('supplier_id')]]; ?></p></td>
                                        <td width="60" align="center"><p><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p></td>
                                        <td width="230" align="center"><p><? echo $compos; ?></p></td>
                                        <td width="80" align="center"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                                        <td width="80" align="center"><p><? echo $color_library[$row[csf('color')]]; ?></p></td>
                                        <td width="80" align="center"><p><? echo $row[csf('lot')]; ?></p></td>
                                        <td align="right" title="<? echo $balance_title;?>"><? echo number_format($bal_alloc_qnty, 2); ?></td>
                                    </tr>
                                    <?
                                    $i++;
                                }
                            }
							else
								echo "<tr><td colspan='8' align='center'>No Item Found</td></tr>";
							?>
							</table>
						</div>
                    <div style="float:left"><b><u>Dyed Yarn</u></b></div>
                    <table width="100%" border="1" rules="all" class="rpt_table">
                        <thead>
                            <th width="30">Sl</th>
                            <th width="100">Supplier</th>
                            <th width="60">Count</th>
                            <th width="200">Composition</th>
                            <th width="70">Type</th>
                            <th width="70">Color</th>
                            <th width="80">Lot No</th>
                            <th width="70">Allocation Qty</th>
                            <th>Cu.Bal.Qty</th>
                        </thead>
                    </table>
                    <div style="width:100%; overflow-y:scroll; max-height:140px;" id="scroll_body" align="left">
                        <table class="rpt_table" rules="all" border="1" width="810" id="tbl_list_search_dyied">
                            <?
							if ($db_type == 0)
							{
								$all_knit_id = return_field_value("GROUP_CONCAT(DISTINCT(b.id)) AS knit_id", "ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b", "a.id = b.mst_id AND a.booking_no IN(".$booking_nos.") AND a.status_active = 1 AND a.is_deleted =0  AND b.status_active = 1 AND b.is_deleted =0", "knit_id");
							}
							else
							{
								$knit_ids = return_field_value("LISTAGG(b.id, ',') WITHIN GROUP (GROUP BY b.id) AS knit_id", "ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b","a.id = b.mst_id AND a.booking_no IN(".$booking_nos.") AND a.GROUP=1 AND a.is_deleted = 0 and b.status_active=1 AND b.is_deleted=0","knit_id");
								$all_knit_id = implode(",", array_unique(explode(",", $knit_ids)));
							}

							$req_qnty_array = array();
							$sql_requs = "SELECT c.prod_id, SUM(c.yarn_qnty) AS yarn_qnty FROM ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b, ppl_yarn_requisition_entry c WHERE b.id = a.mst_id AND a.id = c.knit_id AND a.id IN (".$all_knit_id.") AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 GROUP BY c.prod_id";
							//$product_id = "";
							$sql_requs_result = sql_select($sql_requs);
							foreach ($sql_requs_result as $row)
							{
								$req_qnty_array[$row[csf('prod_id')]]['req'] = $row[csf('yarn_qnty')];
							}

							if($db_type==0)
							{
								$check_ysw=sql_select("select x.wo_num,x.product_id,sum(x.yarn_wo_qty) yarn_wo_qty from(select group_concat(distinct(a.yarn_dyeing_prefix_num)) as wo_num,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(94,340) and b.entry_form in(94,340) and a.service_type not in(7) and b.booking_no IN(".$booking_nos.") group by b.product_id)x group by x.wo_num,x.product_id");
							}
							else
							{
								$check_ysw=sql_select("select x.wo_num,x.product_id,sum(x.yarn_wo_qty) yarn_wo_qty from(select LISTAGG(a.yarn_dyeing_prefix_num, ',') WITHIN GROUP (ORDER BY b.id) as wo_num,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(94,340) and b.entry_form in(94,340) and a.service_type not in(7) and b.booking_no IN(".$booking_nos.") group by b.product_id)x group by x.wo_num,x.product_id");
							}

							$ysw_qnty_arr = array();
							foreach ($check_ysw as $row)
							{
								$ysw_qnty_arr[$row[csf('product_id')]] = $row[csf('yarn_wo_qty')];
							}

							$i = 1;
							$bal_alloc_qnty = 0;

							//$sql_dyied_yarn_sql = "select a.id as sid, a.id as id,a.job_no,a.po_break_down_id,a.item_id,a.qnty,b.company_name,b.buyer_name,b.location_name,c.id prod_id,c.lot,c.yarn_count_id, c.yarn_type, c.yarn_comp_percent1st,c.yarn_comp_percent2nd,c.yarn_comp_type1st,c.yarn_comp_type2nd,c.color,c.allocated_qnty allo_qty, c.supplier_id from inv_material_allocation_mst a,wo_po_details_master b,product_details_master c where a.job_no=b.job_no and a.booking_no IN(".$booking_nos.") and a.item_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_dyied_yarn=1";
							$sql_dyied_yarn_sql = "select a.id as sid, a.id as id,a.job_no,a.po_break_down_id,a.item_id,a.qnty,b.company_id as company_name,b.buyer_id as buyer_name,c.id prod_id,c.lot,c.yarn_count_id, c.yarn_type, c.yarn_comp_percent1st,c.yarn_comp_percent2nd,c.yarn_comp_type1st,c.yarn_comp_type2nd,c.color,c.allocated_qnty allo_qty, c.supplier_id from inv_material_allocation_mst a, wo_non_ord_samp_booking_mst b,product_details_master c where a.booking_no=b.booking_no and a.booking_no IN(".$booking_nos.") and a.item_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_dyied_yarn=1";
							//echo $sql_dyied_yarn_sql;
							$dyedYarnData = sql_select($sql_dyied_yarn_sql);

							if(empty($dyedYarnData))
							{
								echo get_empty_data_msg();
								die;
							}

							foreach ($dyedYarnData as $row)
							{
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$compos = '';
								if ($row[csf('yarn_comp_percent2nd')] != 0)
								{
									$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
								}
								else
								{
									$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
								}

								$allocation_qty = $row[csf('qnty')];
								$requstion_qty = $req_qnty_array[$row[csf('prod_id')]]['req'];
								$ysw_qnty = $ysw_qnty_arr[$row[csf('prod_id')]];
								$cu_balance_qty = ($allocation_qty - ($requstion_qty+$ysw_qnty));

								$balance_title = "PROD ID->".$row[csf('prod_id')]." YSW QTY->".$ysw_qnty.", PREV RQTY->".$requstion_qty.", Balance=($allocation_qty - ($requstion_qty+$ysw_qnty))";

								$data = $row[csf('lot')] . "**" . $row[csf('yarn_count_id')] . "**" . $row[csf('yarn_type')] . "**" . $color_library[$row[csf('color')]] . "**" . $compos . "**1**" . $cu_balance_qty;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="searchf<? echo $i; ?>" style="cursor:pointer"
									onClick="js_set_value(<? echo $row[csf('prod_id')]; ?>,'<? echo $data; ?>');">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="100" align="center"><? echo $supllier_arr[$row[csf('supplier_id')]]; ?></td>
									<td width="60" align="center"><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
									<td width="200" align="center"><? echo $compos; ?></td>
									<td width="70" align="center"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
									<td width="70" align="center"><? echo $color_library[$row[csf('color')]]; ?></td>
									<td width="80" align="center" title="<? echo $row[csf('prod_id')]; ?>" ><? echo $row[csf('lot')]; ?></td>
									<td width="70" align="right"><? echo number_format($allocation_qty, 2); ?></td>
									<td align="right" title="<? echo $balance_title; ?>"><? echo number_format($cu_balance_qty, 2); ?></td>
								</tr>
								<?
								$i++;
							}
							?>
						</table>
					</div>
                </fieldset>
            </form>
        </div>
        <?php
    }
	?>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

/*
|--------------------------------------------------------------------------
| create_product_search_list_view
|--------------------------------------------------------------------------
|
*/
if ($action == "create_product_search_list_view")
{
	$data = explode('**', $data);

	if ($data[0] == 0) $supp_cond = ""; else $supp_cond = " and a.supplier_id='" . trim($data[0]) . "' ";
	if ($data[1] == 0) $yarn_count_cond = ""; else $yarn_count_cond = " and a.yarn_count_id='" . trim($data[1]) . "' ";
	if ($data[3] == 0) $yarn_type_cond = ""; else $yarn_type_cond = " and a.yarn_type='" . trim($data[3]) . "' ";

	$yarn_desc_cond = " and a.supplier_id like '%" . trim($data[2]) . "%'";
	$lot_no_cond = " and lot like '%" . trim($data[4]) . "%'";
	$companyID = $data[5];
	$cbo_dyed_yarn_qty_from = $data[6];


	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	?>
	<table width="1150" border="1" rules="all" class="rpt_table">
		<thead>
			<th width="40">Sl No</th>
			<th width="70">Buyer ID</th>
			<th width="70">Count</th>
			<th width="170">Composition</th>
			<th width="80">Type</th>
			<th width="80">Color</th>
			<th width="80">Lot No</th>
			<th width="140">Supplier</th>
			<th width="80">Wgt. Bag/Cone</th>
			<th width="80">Current Stock</th>
			<th width="80">Allocated Qnty</th>
			<th width="80">Available For Req.</th>
			<th width="80">Age (Days)</th>
			<th width="80">DOH</th>
		</thead>
	</table>
	<table width="1150" class="rpt_table" rules="all" border="1" id="tbl_list_search">
		<?
		$date_array = array();

		if($db_type==0)
		{
			$buyer_id_list = "group_concat(buyer_id) as buyer_id";
			$weight_per_bag_list = "group_concat(weight_per_bag) as weight_per_bag";

		}else {
			$buyer_id_list = "listagg(buyer_id, ',') within group (order by buyer_id) as buyer_id";
			$weight_per_bag_list = "listagg(weight_per_bag, ',') within group (order by weight_per_bag) as weight_per_bag";
		}

		$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date,$buyer_id_list,$weight_per_bag_list from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 and receive_basis in(1,2,4) group by prod_id";

		$result_returnRes_date = sql_select($returnRes_date);
		foreach ($result_returnRes_date as $row) {
			$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
			$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
			$trans_info_arr[$row[csf("prod_id")]]['buyer_id'] = $row[csf("buyer_id")];
			$trans_info_arr[$row[csf("prod_id")]]['weight_per_bag'] = $row[csf("weight_per_bag")];
			$trans_info_arr[$row[csf("prod_id")]]['weight_per_cone'] = $row[csf("weight_per_cone")];
		}

		$sql = "select a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty,a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id, a.yarn_type,a.color,a.dyed_type, sum(b.yarn_qnty) yarn_qnty
		from product_details_master a left join ppl_yarn_requisition_entry b on a.id=b.prod_id
		where a.company_id=$companyID and a.current_stock>0 and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $supp_cond $yarn_count_cond $yarn_type_cond $yarn_desc_cond $lot_no_cond group by a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty, a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id,a.yarn_type,a.color,a.dyed_type order by a.lot";
		//echo $sql;
		$result = sql_select($sql);
		$i = 1;
		foreach ($result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
			$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));

			$buyer = implode(",",array_unique(explode(",",$trans_info_arr[$row[csf("id")]]['buyer_id'])));
			$weight_per_bag = implode(",",array_unique(explode(",",$trans_info_arr[$row[csf("id")]]['buyer_id'])));
			$weight_per_cone = implode(",",array_unique(explode(",",$trans_info_arr[$row[csf("id")]]['buyer_id'])));

			$compos = '';
			if ($row[csf('yarn_comp_percent2nd')] != 0) {
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
			} else {
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
			}

			if( $row[csf('dyed_type')]==1)
			{
				if($cbo_dyed_yarn_qty_from==1)
				{
					$available_qnty = $row[csf('allocated_qnty')];
				}else{
					$available_qnty = $row[csf('available_qnty')];
				}
			}else {
				$available_qnty = $row[csf('available_qnty')];
			}
			//$available_qnty = ($row[csf('dyed_type')]==1)?$row[csf('allocated_qnty')]:$row[csf('available_qnty')];
			$data = $row[csf('lot')] . "**" . $row[csf('yarn_count_id')] . "**" . $row[csf('yarn_type')] . "**" . $color_library[$row[csf('color')]] . "**" . $compos . "**".$row[csf('dyed_type')]."**" . $available_qnty."**".$cbo_dyed_yarn_qty_from;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="cursor:pointer"
				onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $data; ?>');">
				<td width="40" align="center"><? echo $i; ?></td>
				<td width="70" align="center"><? echo $buyer_arr[$buyer]; ?></td>
				<td width="70" align="center"><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
				<td width="170" align="center"><p><? echo $compos; ?></p></td>
				<td width="80" align="center"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
				<td width="80" align="center"><? echo $color_library[$row[csf('color')]]; ?></td>
				<td width="80" align="center"><? echo $row[csf('lot')]; ?></td>
				<td width="140" align="center"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></td>
				<td width="80" align="center"><? echo 'Bg:' .$weight_per_bag . '; ' .'<br>'. 'Cn:' . $weight_per_cone; ?></td>
				<td width="80" align="right"><? echo number_format($row[csf('current_stock')], 2); ?></td>
				<td width="80" align="right"><? echo number_format($row[csf('allocated_qnty')], 2); ?></td>
				<td width="80" align="right"><? echo number_format($available_qnty, 2); ?></td>
				<td width="80" align="center"><? echo $ageOfDays; ?></td>
				<td width="80" align="center"><? echo $daysOnHand; ?></td>
			</tr>
			<?
			$i++;
		}
		?>
	</table>
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

	$is_dist_qnty_valiable_set = str_replace("'", "", $is_dist_qnty_valiable_set);
	$is_dyed_yarn = str_replace("'", "", $is_dyed_yarn);
	$dyed_yarn_qnty_from = str_replace("'", "", $dyed_yarn_qnty_from);

	//for booking id
	$booking_id = return_field_value('id', 'wo_non_ord_samp_booking_mst', "booking_no = ".$booking_no." AND status_active = 1 AND is_deleted =0", 'id');

	if($is_auto_allocation_from_requisition !=1 ) // no
	{
		/*
		$job_nos = explode(",", str_replace("'", "", $txt_job_no));
		foreach ($job_nos as $job)
		{
			$jobs .= "'".$job."',";
		}
		$jobs = rtrim($jobs,", ");
		*/
		$txt_job_no = str_replace("'", "", $txt_job_no);
		$selectedBookingNo = str_replace("'", "", $booking_no);
		$prod_id = str_replace("'", "", $prod_id);

		if($is_dyed_yarn !=1 ) // grey yarn
		{
			//$sql_allo = "select b.booking_no, b.job_no, b.item_id as prod_id,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_type,c.dyed_type, b.qnty  as allocated_qnty from inv_material_allocation_mst a,inv_material_allocation_dtls b ,product_details_master c where b.job_no in(".rtrim($jobs,", ").") and b.item_id=$prod_id and a.id=b.mst_id and b.item_id=c.id and dyed_type!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=1 and c.status_active=1 and c.is_deleted=0";
			$sql_allo = "
				SELECT
					a.booking_no, b.job_no, b.item_id as prod_id, b.qnty  as allocated_qnty,
					c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type, c.dyed_type
				FROM
					inv_material_allocation_mst a
					INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id
					INNER JOIN product_details_master c ON b.item_id = c.id
				WHERE
					a.booking_no = '".$selectedBookingNo."'
					AND b.item_id = ".$prod_id."
					AND dyed_type != 1
					AND a.status_active = 1
					AND a.is_deleted = 0
					AND b.status_active = 1
					AND b.is_deleted = 0
					AND b.item_category = 1
					AND c.status_active = 1
					AND c.is_deleted = 0
			";
			//echo "10**",$sql_allo; die;
			$data_array = sql_select($sql_allo);
			foreach ($data_array as $row_allo)
			{
				//$job_no = $row_allo[csf('job_no')];
				$yarn_count_id = $row_allo[csf('yarn_count_id')];
				$yarn_comp_type1st = $row_allo[csf('yarn_comp_type1st')];
				$yarn_comp_percent1st = $row_allo[csf('yarn_comp_percent1st')];
				$yarn_type = $row_allo[csf('yarn_type')];

				if($row_allo[csf('booking_no')]!="")
				{
					$booking_alocation_arr[$row_allo[csf('booking_no')]][$row_allo[csf('prod_id')]] += $row_allo[csf('allocated_qnty')];
				}

				//$job_total_allocation_arr[$row_allo[csf('job_no')]][$row_allo[csf('prod_id')]] += $row_allo[csf('allocated_qnty')];
			}

			//echo "10**<pre>";
			//print_r($booking_alocation_arr);
			//print_r($job_total_allocation_arr);
			//die();

			/*
			$ydsw_sql="select x.job_no,x.product_id,sum(x.yarn_wo_qty) yarn_wo_qty from(select b.job_no,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(41,42,94,114,135) and b.entry_form in(41,42,94,114,135) and b.job_no='$job_no' and b.product_id in($prod_id) group by b.job_no,b.product_id
			union all
			select b.job_no,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(125,340) and b.entry_form in(125,340) and b.job_no='$job_no' and b.count=$yarn_count_id and b.yarn_comp_type1st=$yarn_comp_type1st and b.yarn_comp_percent1st=$yarn_comp_percent1st and b.yarn_type=7 group by b.job_no,b.product_id )x group by x.job_no,x.product_id";
			*/
			$ydsw_sql="
				select x.job_no, x.product_id, sum(x.yarn_wo_qty) yarn_wo_qty from(select b.job_no, b.product_id, sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(41,42,94,114,135) and b.entry_form in(41,42,94,114,135) and b.booking_no = '".$selectedBookingNo."' and b.product_id in(".$prod_id.") group by b.job_no,b.product_id
			union all
			select b.job_no,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(125,340) and b.entry_form in(125,340) and b.booking_no = '".$selectedBookingNo."' and b.count=".$yarn_count_id." and b.yarn_comp_type1st=".$yarn_comp_type1st." and b.yarn_comp_percent1st=".$yarn_comp_percent1st." and b.yarn_type=7 group by b.job_no,b.product_id )x group by x.job_no,x.product_id";
			//echo "17**5**".$ydsw_sql; die;
			$check_ydsw = sql_select($ydsw_sql);
			$prod_wise_ydsw=array();
			foreach ($check_ydsw as $row)
			{
				$prod_wise_ydsw[$row[csf("job_no")]][$row[csf("product_id")]] = $row[csf("yarn_wo_qty")];
			}
			//echo "<pre>";
			//print_r($prod_wise_ydsw); die;

			/*
			$req_sql = "select a.booking_no,c.knit_id,c.prod_id,c.requisition_no, c.yarn_qnty
			from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c
			where a.id=b.mst_id and b.id=c.knit_id and c.prod_id in($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			*/

			$req_sql = "
				SELECT
					a.booking_no, c.knit_id, c.prod_id, c.requisition_no, c.yarn_qnty
				FROM
					ppl_planning_info_entry_mst a
					INNER JOIN ppl_planning_info_entry_dtls b ON a.id = b.mst_id
					INNER JOIN ppl_yarn_requisition_entry c ON b.id = c.knit_id
				WHERE
					a.status_active = 1
					AND a.is_deleted = 0
					AND a.is_sales = 2
					AND a.booking_no = '".$selectedBookingNo."'
					AND b.status_active = 1
					AND b.is_deleted = 0
					AND b.is_sales = 2
					AND c.status_active = 1
					AND c.is_deleted = 0
					AND c.prod_id IN(".$prod_id.")
			";

			//echo "10**".$req_sql; die;
			$req_result = sql_select($req_sql);
			foreach($req_result as $row)
			{
				$booking_requsition_arr[$row[csf("booking_no")]][$row[csf("prod_id")]] += $row[csf("yarn_qnty")];
			}
			//echo "10**". $job_total_allocation_qty."-".$ydsw_qty."+".$existing_requsition_qty; die();

			$ydsw_qty = $prod_wise_ydsw[$txt_job_no][$prod_id]*1;
			//$job_total_allocation_qty = $job_total_allocation_arr[$txt_job_no][$prod_id]*1;
			$existing_requsition_qty = $booking_requsition_arr[$selectedBookingNo][$prod_id]*1;
			$booking_total_allocation_qty = (($booking_alocation_arr[$selectedBookingNo][$prod_id]*1)-$existing_requsition_qty);
			$balance = ( $booking_total_allocation_qty - $ydsw_qty );
			//$balance = ( $job_total_allocation_qty - ( $ydsw_qty + $existing_requsition_qty ) );
			//echo "10**".$job_total_allocation_qty. "_". $ydsw_qty."_".$existing_requsition_qty."_".$balance."_*".$booking_total_allocation_qty; die();
			if($balance>$booking_total_allocation_qty)
			{
				$cumalative_balance = $booking_total_allocation_qty;

			}
			else
			{
				if($balance>0)
				{
					$cumalative_balance = $balance;
				}
				else
				{
					$cumalative_balance = 0;
				}
			}
		}
		else
		{ // dyed yarn

			if($db_type==0)
			{
				//$check_ysw=sql_select("select x.wo_num,x.product_id,sum(x.yarn_wo_qty) yarn_wo_qty from(select group_concat(distinct(a.yarn_dyeing_prefix_num)) as wo_num,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(94,340) and b.entry_form in(94,340) and a.service_type not in(7) and b.job_no in(".rtrim($jobs,", ").") and b.product_id=$prod_id group by b.product_id)x group by x.wo_num,x.product_id");
				$check_ysw=sql_select("select x.wo_num,x.product_id,sum(x.yarn_wo_qty) yarn_wo_qty from(select group_concat(distinct(a.yarn_dyeing_prefix_num)) as wo_num,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(94,340) and b.entry_form in(94,340) and a.service_type not in(7) and b.booking_no = '".$selectedBookingNo."' and b.product_id=".$prod_id." group by b.product_id)x group by x.wo_num,x.product_id");
			}
			else
			{
				$check_ysw=sql_select("select x.wo_num,x.product_id,sum(x.yarn_wo_qty) yarn_wo_qty from(select LISTAGG(a.yarn_dyeing_prefix_num, ',') WITHIN GROUP (ORDER BY b.id) as wo_num,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(94,340) and b.entry_form in(94,340) and a.service_type not in(7) and b.booking_no = '".$selectedBookingNo."' and b.product_id=".$prod_id." group by b.product_id)x group by x.wo_num,x.product_id");
			}

			$ysw_qnty_arr = array();
			foreach ($check_ysw as $row)
			{
				$ysw_qnty_arr[$row[csf('product_id')]] = $row[csf('yarn_wo_qty')];
			}

			$all_booking_no = '';
			//$get_job_booking = sql_select("select a.booking_no from wo_booking_dtls a where a.job_no in(".rtrim($jobs,", ").") and a.status_active=1 and a.is_deleted=0 group by  booking_no");

			$get_job_booking = sql_select("select a.booking_no from wo_non_ord_samp_booking_mst a where a.booking_no = '".$selectedBookingNo."' AND a.status_active=1 AND a.is_deleted=0 GROUP BY booking_no");
			foreach ($get_job_booking as $booking_row)
			{
				$all_booking_no .= "'" . $booking_row[csf('booking_no')] . "',";
			}
			$booking_nos = rtrim($all_booking_no, ',');

			if ($db_type == 0)
			{
				$all_knit_id = return_field_value("GROUP_CONCAT(DISTINCT(b.id)) AS knit_id", "ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b", "a.id = b.mst_id AND a.booking_no = '".$selectedBookingNo."' AND a.status_active = 1 AND a.is_deleted =0  AND b.status_active = 1 AND b.is_deleted =0", "knit_id");
			}
			else
			{
				$knit_ids = sql_select("SELECT LISTAGG(b.id, ',') WITHIN GROUP (GROUP BY b.id) as knit_id FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b WHERE a.id=b.mst_id AND a.booking_no = '".$selectedBookingNo."' AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0");
				$all_knit_id = implode(",", array_unique(explode(",", $knit_ids[0][csf('knit_id')])));
			}

			$req_qnty_array = array();
			$sql_requs = "select b.booking_no, c.prod_id, sum(c.yarn_qnty) as yarn_qnty from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b, ppl_yarn_requisition_entry c where b.id=a.mst_id and a.id=c.knit_id and a.id in (".$all_knit_id.") and c.prod_id=".$prod_id."  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.prod_id, b.booking_no";
			$sql_requs_result = sql_select($sql_requs);
			foreach ($sql_requs_result as $row)
			{
				$req_qnty_array[$row[csf('prod_id')]]['req'] += $row[csf('yarn_qnty')];
			}

			$i = 1;
			$bal_alloc_qnty = 0;

			$sql_dyied_yarn_sql = "select a.job_no, a.item_id, sum(a.qnty) as qnty, c.allocated_qnty, c.id prod_id from inv_material_allocation_mst a, product_details_master c where a.booking_no = '".$selectedBookingNo."' and a.item_id=c.id and a.item_id=".$prod_id." and a.status_active=1 and a.is_deleted=0 and a.is_dyied_yarn=1 and c.dyed_type=1 group by a.job_no,a.item_id,c.allocated_qnty,c.id";
			//echo "17**".$check_ysw; die();
			$dyedYarnData = sql_select($sql_dyied_yarn_sql);
			foreach ($dyedYarnData as $row)
			{
				$allocation_qty = $row[csf('qnty')]*1;
				$requstion_qty = $req_qnty_array[$row[csf('prod_id')]]['req']*1;
				$ysw_qnty = $ysw_qnty_arr[$row[csf('prod_id')]]*1;
				//echo "10**$allocation_qty - $requstion_qty";
				$cumalative_balance = ($allocation_qty - ($requstion_qty+$ysw_qnty));
			}
		}

		$req_qnty = 0;
		$req_qnty =  str_replace("'", "", $txt_yarn_qnty);
		//echo "10**".$allocation_qty. "_". $requstion_qty."_".$ysw_qnty."_".$req_qnty."_".$cumalative_balance; die();


		if ($operation == 0)
		{
			$cumalative_balance = $cumalative_balance;
			if (round($req_qnty,2) > round($cumalative_balance,2))
			{
				echo "17**" . $cumalative_balance . "**0**$is_dyed_yarn";
				exit();
			}
		}
		elseif($operation == 1)
		{
			$text_old_qnty = str_replace("'", "", $txt_old_qnty);
			$cumalative_balance = ($cumalative_balance + $text_old_qnty);
			if (round($req_qnty,2) > round($cumalative_balance,2))
			{
				echo "17**" . $cumalative_balance . "**0**$is_dyed_yarn";
				exit();
			}
		}
	}

	if ( $operation == 0 ||  $operation==1 )
	{
		if( (str_replace("'", "", $hdn_distribution_qnty)!="") && (str_replace("'", "", $hdn_distribution_qnty)>0) )
		{
			if(str_replace("'", "", $hdn_distribution_qnty) > str_replace("'", "", $txt_yarn_qnty) )
			{
				echo "12**".$hdn_distribution_qnty."**".$txt_yarn_qnty;
				exit();
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Insert
	| ppl_yarn_requisition_entry
	| ppl_yarn_req_distribution
	| inv_material_allocation_mst
	| inv_material_allocation_dtls
	| product_details_master
	|--------------------------------------------------------------------------
	|
	*/
	if ($operation == 0)
	{
		$con = connect();
		if ($db_type == 0)
		{
			mysql_query("BEGIN");
			$date_cond = change_date_format(str_replace("'", "", $txt_reqs_date), "yyyy-mm-dd", "-");
		}
		else
		{
			$date_cond = change_date_format(str_replace("'", "", $txt_reqs_date), '', '', 1);
		}

		/*
		|--------------------------------------------------------------------------
		| duplicate checking
		|--------------------------------------------------------------------------
		|
		*/
		if (is_duplicate_field("prod_id", "ppl_yarn_requisition_entry", "knit_id=".$updateId." and prod_id=".$prod_id." and status_active=1 and is_deleted=0") == 1)
		{
			echo "11**" . str_replace("'", "", $updateId) . "**0";
			disconnect($con);
			exit();
		}

		$requisition_no = return_field_value("requisition_no", "ppl_yarn_requisition_entry", " knit_id=".$updateId." and status_active=1 and is_deleted=0  " );
		if ($requisition_no == "")
		{
			$requisition_no = return_next_id("requisition_no", "ppl_yarn_requisition_entry", 1);
		}
		else
		{
			$requisition_no = $requisition_no;
		}

		$hdn_distribution_qnty_breakdown = $requisition_no."_".$booking_id."_".str_replace("'", "", $prod_id)."_".str_replace("'", "", $txt_yarn_qnty);
		$qnty_breck_down = str_replace("'", "", $txt_yarn_qnty)."_".$booking_id."_";

		$id = return_next_id("id", "ppl_yarn_requisition_entry", 1);
		$id_breakdown = return_next_id("id", "ppl_yarn_requisition_breakdown", 1);
		if($is_dist_qnty_valiable_set == 1)
		{
			/*
			|--------------------------------------------------------------------------
			| ppl_yarn_requisition_entry
			| data preparing for
			| $data_array
			|--------------------------------------------------------------------------
			|
			*/
			$field_array = "id,knit_id,requisition_no,prod_id,no_of_cone,requisition_date,yarn_qnty,is_dyed_yarn,inserted_by,insert_date,total_distribution_qnty,allocation_qnty_breakdown,distribution_qnty_breakdown,dyed_yarn_qnty_from";
			$data_array = "(".$id.",".$updateId.",".$requisition_no.",".$prod_id.",".$txt_no_of_cone.",'".$date_cond."',".$txt_yarn_qnty.",'".str_replace("'","",$is_dyed_yarn)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_yarn_qnty.",'".$qnty_breck_down."','".$hdn_distribution_qnty_breakdown."','".$dyed_yarn_qnty_from."')";

			/*
			|--------------------------------------------------------------------------
			| ppl_yarn_requisition_breakdown
			| data preparing for
			| $dataRequisitionBreakdown
			|--------------------------------------------------------------------------
			|
			*/
			$fieldRequisitionBreakdown = 'id, requisition_id, program_id, order_id, item_id, order_requisition_qty, requisition_qty, distribution_method, booking_no, inserted_by, insert_date, updated_by, update_date';
			$dataRequisitionBreakdown = "(".$id_breakdown.",".$requisition_no . ",".$updateId.",".$booking_id.",".$prod_id.",".$txt_yarn_qnty.",".$txt_yarn_qnty.",1,".$booking_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			/*
			|--------------------------------------------------------------------------
			| ppl_yarn_req_distribution
			| data preparing for
			| $data_array
			|--------------------------------------------------------------------------
			|
			*/
			$dist_id = return_next_id("id", "ppl_yarn_req_distribution", 1);
			$field_dist_array = "id,requisition_no,po_break_down_id,prod_id,distribution_qnty,inserted_by,insert_date";
			$data_dist_array = "(".$dist_id.",".$requisition_no.",".$booking_id.",".$prod_id.",".$hdn_distribution_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		else
		{
			/*
			|--------------------------------------------------------------------------
			| ppl_yarn_requisition_entry
			| data preparing for
			| $data_array
			|--------------------------------------------------------------------------
			|
			*/
			$field_array = "id,knit_id,requisition_no,prod_id,no_of_cone,requisition_date,yarn_qnty,is_dyed_yarn,inserted_by,insert_date,allocation_qnty_breakdown,dyed_yarn_qnty_from";
			$data_array = "(".$id.",".$updateId.",".$requisition_no.",".$prod_id.",".$txt_no_of_cone.",'".$date_cond."',".$txt_yarn_qnty.",'".str_replace("'","",$is_dyed_yarn)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$qnty_breck_down."','$dyed_yarn_qnty_from')";

			/*
			|--------------------------------------------------------------------------
			| ppl_yarn_requisition_breakdown
			| data preparing for
			| $dataRequisitionBreakdown
			|--------------------------------------------------------------------------
			|
			*/
			$fieldRequisitionBreakdown = 'id, requisition_id, program_id, order_id, item_id, order_requisition_qty, requisition_qty, distribution_method, booking_no, inserted_by, insert_date, updated_by, update_date';
			$dataRequisitionBreakdown = "(".$id_breakdown.",".$requisition_no . ",".$updateId.",".$booking_id.",".$prod_id.",".$txt_yarn_qnty.",".$txt_yarn_qnty.",1,".$booking_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}

		/*
		|--------------------------------------------------------------------------
		| check valiable if auto allocation is set to Yes and Yarn is grey yarn
		|--------------------------------------------------------------------------
		|
		*/
		if( $is_auto_allocation_from_requisition == 1 && ( $is_dyed_yarn !=1 || $dyed_yarn_qnty_from==2 ) )
		{
			/*
			|--------------------------------------------------------------------------
			| allocation checking
			|--------------------------------------------------------------------------
			|
			*/
			//$check_allocation = sql_select("SELECT a.id, a.job_no, a.mst_id, a.po_break_down_id, a.qnty FROM inv_material_allocation_dtls a WHERE a.item_id = ".$prod_id." AND a.booking_no = ".$booking_no." AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY a.id, a.job_no, a.mst_id, a.po_break_down_id, a.qnty");
			$check_allocation = sql_select("SELECT a.id, a.mst_id, a.po_break_down_id, a.qnty FROM inv_material_allocation_dtls a WHERE a.item_id = ".$prod_id." AND a.booking_no = ".$booking_no." AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY a.id, a.mst_id, a.po_break_down_id, a.qnty");
			$check_allocation = array();
			if(!empty($check_allocation))
			{
				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_dtls
				| data preparing for
				| $data_allocation_dtls
				|--------------------------------------------------------------------------
				|
				*/
				$qnty_break_down="";
				$allocation_qnty=0;
				$field_allocation_dtls = "qnty*inserted_by*insert_date";
				foreach ($check_allocation as $dtls_allocation)
				{
					$dtls_id = $dtls_allocation[csf('id')];
					$dtls_allocation_qnty = $dtls_allocation[csf('qnty')] + str_replace("'", "", $txt_yarn_qnty);
					$data_allocation_dtls = "".$dtls_allocation_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					$qnty_break_down .= number_format($dtls_allocation_qnty,2,'.','') . "_" . $dtls_allocation[csf('po_break_down_id')] . "_,";
					$allocation_qnty += $dtls_allocation_qnty;
					$material_alocation_update_id = $dtls_allocation[csf('mst_id')];
					$allocation_dtls_update = sql_update("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, "id", "" . $dtls_id . "", 0);
				}

				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_mst
				| data preparing for
				| $data_allocation
				|--------------------------------------------------------------------------
				|
				*/
				$qnty_break_down = rtrim($qnty_break_down, ", ");
				$field_allocation = "qnty*qnty_break_down*updated_by*update_date";
				$data_allocation = "".$allocation_qnty."*'".$qnty_break_down."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$allocation_mst_update = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", "".$material_alocation_update_id."", 0);
				if ($allocation_mst_update)
				{
					$alocationFlag = 1;
				}
				else
				{
					$alocationFlag = 0;
				}
			}
			else
			{
				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_mst
				| data preparing for
				| $data_allocation
				|--------------------------------------------------------------------------
				|
				*/
				$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
				$field_allocation = "id,mst_id,entry_form,booking_no,po_break_down_id,item_category,allocation_date,item_id,qnty,qnty_break_down,is_dyied_yarn,is_sales,inserted_by,insert_date";
				$is_dyed_yarn = (str_replace("'", "", $is_dyed_yarn)!=1)?0:1;
				$data_allocation = "(".$allocation_id.",".$id.",385,".$booking_no.",".$booking_id.",1".",".$txt_reqs_date.",".$prod_id.",".$txt_yarn_qnty.",'".$qnty_breck_down."',0,2,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_dtls
				| data preparing for
				| $data_allocation_dtls
				|--------------------------------------------------------------------------
				|
				*/
				$field_allocation_dtls = "id,mst_id,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,is_sales,inserted_by,insert_date";
				$dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
				$data_allocation_dtls = "(".$dtls_id.",".$allocation_id.",".$booking_id.",".$booking_no.",1,".$txt_reqs_date.",".$prod_id.",".$txt_yarn_qnty.",".$is_dyed_yarn.",2,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_mst
				| inv_material_allocation_dtls
				| data inserting
				|--------------------------------------------------------------------------
				|
				*/
				//oci_rollback($con);
				//echo "10**INSERT INTO inv_material_allocation_mst (".$field_allocation.") VALUES ".$data_allocation.""; die;
				$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);
				if ($allocation_mst_insert)
				{
					$alocationFlag = 1;
					//oci_rollback($con);
					//echo "10**INSERT INTO inv_material_allocation_dtls (".$field_allocation_dtls.") VALUES ".$data_allocation_dtls.""; die;
					$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
					if($allocation_dtls_insert)
						$alocationFlag = 1;
					else
						$alocationFlag = 0;
				}
				else
				{
					$alocationFlag = 0;
				}
			}

			/*
			|--------------------------------------------------------------------------
			| product_details_master
			| data updating
			|--------------------------------------------------------------------------
			|
			*/
			$txt_yarn_qnty = str_replace("'", "", $txt_yarn_qnty);
			$product_allocation_update = execute_query("UPDATE product_details_master SET allocated_qnty=(allocated_qnty+$txt_yarn_qnty) WHERE id = ".$prod_id."",0);
			$product_available_update = execute_query("UPDATE product_details_master SET available_qnty=(current_stock-allocated_qnty) WHERE id = ".$prod_id."", 0);
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_yarn_requisition_entry
		| data inserting
		| done
		|--------------------------------------------------------------------------
		|
		*/
		$rID = sql_insert("ppl_yarn_requisition_entry", $field_array, $data_array, 1);

		/*
		|--------------------------------------------------------------------------
		| ppl_yarn_requisition_breakdown
		| data inserting
		| done
		|--------------------------------------------------------------------------
		|
		*/
		$rID2 = sql_insert("ppl_yarn_requisition_breakdown", $fieldRequisitionBreakdown, $dataRequisitionBreakdown, 1);

		/*
		|--------------------------------------------------------------------------
		| ppl_yarn_req_distribution
		| data inserting
		| done
		|--------------------------------------------------------------------------
		|
		*/
		$rID3 = true;
		if($is_dist_qnty_valiable_set == 1)
		{
			$rID3 = sql_insert("ppl_yarn_req_distribution", $field_dist_array, $data_dist_array, 1);
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
			if($is_auto_allocation_from_requisition==1 && str_replace("'", "", $is_dyed_yarn) != 1)
			{
				if ($rID && $rID2 && $alocationFlag)
				{
					mysql_query("COMMIT");
					echo "0**" . str_replace("'", "", $updateId) . "**0**" . $requisition_no;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "5**" . str_replace("'", "", $updateId) . "**0";
				}
			}
			else
			{
				if ($rID && $rID2)
				{
					mysql_query("COMMIT");
					echo "0**" . str_replace("'", "", $updateId) . "**0**" . $requisition_no;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "5**" . str_replace("'", "", $updateId) . "**0";
				}
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
			if($is_auto_allocation_from_requisition==1 && str_replace("'", "", $is_dyed_yarn) != 1) // yes
			{
				if ($rID && $rID2 && $rID3 && $alocationFlag)
				{
					oci_commit($con);
					echo "0**" . str_replace("'", "", $updateId) . "**0**" . $requisition_no;
				}
				else
				{
					oci_rollback($con);
					echo "5**" . str_replace("'", "", $updateId) . "**0";
				}
			}
			else
			{
				if ($rID && $rID2 && $rID3)
				{
					oci_commit($con);
					echo "0**" . str_replace("'", "", $updateId) . "**0**" . $requisition_no;
				}
				else
				{
					oci_rollback($con);
					echo "5**" . str_replace("'", "", $updateId) . "**0";
				}
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

		$txt_yarn_qnty = str_replace("'", '', $txt_yarn_qnty);
		$txt_old_qnty = str_replace("'", '', $txt_old_qnty);
		$originalProductId = str_replace("'", "", $original_prod_id);
		$hdn_distribution_qnty_breakdown = str_replace("'", "", $txt_requisition_no)."_".$booking_id."_".str_replace("'", "", $prod_id)."_".str_replace("'", "", $txt_yarn_qnty);
		$qnty_breck_down = str_replace("'", "", $txt_yarn_qnty)."_".$booking_id."_";

		if (is_duplicate_field("prod_id", "ppl_yarn_requisition_entry", "knit_id=$updateId and prod_id=$prod_id and id<>$update_dtls_id and status_active=1 and is_deleted=0") == 1)
		{
			echo "11**" . str_replace("'", "", $updateId) . "**1";
			disconnect($con);
			exit();
		}

		// check if issue found against Requisition and Requisition Quantity can not be less than Issue Quantity
		if($db_type == 0)
		{
			$check_issue_sql_select = " group_concat( p.id ) AS issue_id, group_concat( p.issue_number ) AS issue_number ";
		}
		else
		{
			$check_issue_sql_select = " LISTAGG(cast(p.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY p.id) as issue_id,LISTAGG(cast(p.issue_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY p.issue_number) as issue_number ";
		}

		$check_issue = sql_select("select $check_issue_sql_select,sum(p.issue_qnty) issue_qnty from(select b.issue_number ,b.id,(case when a.transaction_type=2 then a.cons_quantity else 0 end) issue_qnty from inv_transaction a,inv_issue_master b where a.mst_id=b.id and a.transaction_type in(2,4) and a.requisition_no=$txt_requisition_no and a.item_category=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id=$prod_id) p");

		$check_issue_return = sql_select("select sum(b.cons_quantity) issue_return_qnty from inv_transaction b where issue_id in(select a.mst_id from inv_transaction a where a.transaction_type in(2) and a.requisition_no in($txt_requisition_no) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id=$prod_id) and b.transaction_type=4 and b.receive_basis in (3,8) and b.prod_id=$prod_id");
		
		// echo "<pre>";
		// print_r($check_issue_return); die;
		// echo "qty".$txt_yarn_qnty."<br>";
		// echo "issue".$check_issue[0][csf('issue_qnty')]."<br>";
		// echo "issue_return".$check_issue_return[0][csf('issue_return_qnty')]."<br>";
		

		if (!empty($check_issue) && (str_replace("'", "", $txt_yarn_qnty) < ($check_issue[0][csf('issue_qnty')]-$check_issue_return[0][csf('issue_return_qnty')])))
		{
			echo "18**Issue Found. Requisition Quantity can not be less than Issue Quantity.\n\nIssue ID=".$check_issue[0][csf('issue_number')]."\nIssue Quantity=".number_format(($check_issue[0][csf('issue_qnty')]-$check_issue_return[0][csf('issue_return_qnty')]),2,'.','');
			disconnect($con);
			exit();
		}

		// check if demand found against requisition
		if($db_type == 0)
		{
			$check_demand_entry_sql_select = " group_concat( b.demand_system_no ) AS demand_system_no ";
		}
		else
		{
			$check_demand_entry_sql_select = " listagg(cast(b.demand_system_no as varchar2(4000)), ',') within group (order by b.demand_system_no) as demand_system_no ";
		}

		$check_demand_entry = sql_select("select $check_demand_entry_sql_select,sum(a.yarn_demand_qnty) yarn_demand_qnty from ppl_yarn_demand_reqsn_dtls a,ppl_yarn_demand_entry_mst b where a.mst_id=b.id and a.requisition_no=$txt_requisition_no and a.prod_id=$original_prod_id and a.status_active=1 and a.is_deleted=0");
		if(str_replace("'", "", $prod_id) != str_replace("'", "", $original_prod_id))
		{
			if(!empty($check_demand_entry) && $check_demand_entry[0][csf("demand_system_no")] != ""){
				echo "18**Demand found. Lot can not be changed.\nDemand Id=".$check_demand_entry[0][csf("demand_system_no")];
				disconnect($con);
				exit();
			}
		}
		else
		{
			if(($check_demand_entry[0][csf("yarn_demand_qnty")] != "") && (str_replace("'", "", $txt_yarn_qnty) < $check_demand_entry[0][csf("yarn_demand_qnty")])){
				echo "18**Requisition quantity can not be less than daily yarn demand entry.\n\nDemand ID=".$check_demand_entry[0][csf("demand_system_no")]."\nDemand quantity=".$check_demand_entry[0][csf("yarn_demand_qnty")];
				disconnect($con);
				exit();
			}
		}

		if(str_replace("'", "", $prod_id) != str_replace("'", "", $original_prod_id))
		{
			if ($db_type == 0)
			{
				$check_issue_against_requisition_lot=sql_select("select group_concat(a.issue_number) as issue_number from inv_issue_master a,inv_transaction b where a.id=b.mst_id and b.receive_basis=3 and b.transaction_type=2 and b.item_category=1 and b.requisition_no=$txt_requisition_no and b.prod_id=$original_prod_id and b.status_active=1 and b.is_deleted=0");
			}else{
				$check_issue_against_requisition_lot=sql_select("select listagg(cast(a.issue_number as varchar2(4000)), ',') within group (order by a.issue_number) as issue_number from inv_issue_master a,inv_transaction b where a.id=b.mst_id and b.receive_basis=3 and b.transaction_type=2 and b.item_category=1 and b.requisition_no=$txt_requisition_no and b.prod_id=$original_prod_id and b.status_active=1 and b.is_deleted=0");
			}
			if($check_issue_against_requisition_lot[0][csf("issue_number")] != "" || $check_issue_against_requisition_lot[0][csf("issue_number")] != null){
				echo "18**Issue found.You can not change this lot.\nIssue ID = ".$check_issue_against_requisition_lot[0][csf("issue_number")];
				disconnect($con);
				exit();
			}
		}

		if($is_dist_qnty_valiable_set == 1)
		{
			/*
			|--------------------------------------------------------------------------
			| ppl_yarn_requisition_entry
			| data preparing for
			| $data_array_update
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_update = "prod_id*no_of_cone*requisition_date*yarn_qnty*updated_by*update_date*total_distribution_qnty*dyed_yarn_qnty_from*distribution_qnty_breakdown*allocation_qnty_breakdown";
			$data_array_update = $prod_id . "*" . $txt_no_of_cone . "*" . $txt_reqs_date . "*" . $txt_yarn_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*".$hdn_distribution_qnty."*'".$dyed_yarn_qnty_from."'*'".$hdn_distribution_qnty_breakdown."'*'".$qnty_breck_down."'";

			/*
			|--------------------------------------------------------------------------
			| ppl_yarn_requisition_breakdown
			| sql preparing for
			| $sqlRequisitionBreakdown
			|--------------------------------------------------------------------------
			|
			*/
			$sqlRequisitionBreakdown = "UPDATE ppl_yarn_requisition_breakdown SET item_id = ".$prod_id.", order_requisition_qty = ".$txt_yarn_qnty.", requisition_qty = ".$txt_yarn_qnty.", updated_by = ".$_SESSION['logic_erp']['user_id'].", update_date = '".$pc_date_time."' WHERE requisition_id = ".$txt_requisition_no." AND program_id = ".$updateId." AND item_id = ".$original_prod_id." AND order_id = ".$booking_id." AND status_active = 1 AND is_deleted = 0";

			/*
			|--------------------------------------------------------------------------
			| ppl_yarn_req_distribution
			| sql preparing for
			| $sqlDistribution
			|--------------------------------------------------------------------------
			|
			*/
			$sqlDistribution = "UPDATE ppl_yarn_req_distribution SET prod_id = ".$prod_id.", distribution_qnty = ".$hdn_distribution_qnty.", updated_by = ".$_SESSION['logic_erp']['user_id'].", update_date = '".$pc_date_time."' WHERE requisition_no = ".$txt_requisition_no." AND prod_id = ".$original_prod_id." AND po_break_down_id = ".$booking_id." AND status_active = 1 AND is_deleted = 0";
		}
		else
		{
			/*
			|--------------------------------------------------------------------------
			| ppl_yarn_requisition_entry
			| data preparing for
			| $data_array_update
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_update = "prod_id*no_of_cone*requisition_date*yarn_qnty*updated_by*update_date*dyed_yarn_qnty_from*allocation_qnty_breakdown";
			$data_array_update = $prod_id . "*" . $txt_no_of_cone . "*" . $txt_reqs_date . "*" . $txt_yarn_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'" . $dyed_yarn_qnty_from . "'*'".$qnty_breck_down."'";

			/*
			|--------------------------------------------------------------------------
			| ppl_yarn_requisition_breakdown
			| sql preparing for
			| $sqlRequisitionBreakdown
			|--------------------------------------------------------------------------
			|
			*/
			$sqlRequisitionBreakdown = "UPDATE ppl_yarn_requisition_breakdown SET item_id = ".$prod_id.", order_requisition_qty = ".$txt_yarn_qnty.", requisition_qty = ".$txt_yarn_qnty.", updated_by = ".$_SESSION['logic_erp']['user_id'].", update_date = '".$pc_date_time."' WHERE requisition_id = ".$txt_requisition_no." AND program_id = ".$updateId." AND item_id = ".$original_prod_id." AND order_id = ".$booking_id." AND status_active = 1 AND is_deleted = 0";
		}

		/*
		|--------------------------------------------------------------------------
		| check valiable if auto allocation is set to Yes and Yarn is grey yarn
		|--------------------------------------------------------------------------
		|
		*/
		//$prev_datas = explode(",", str_replace("'", '', $qnty_breck_down));
		$prod_id = str_replace("'", "", $prod_id);
		if( $is_auto_allocation_from_requisition == 1 &&  ($is_dyed_yarn != 1  || $dyed_yarn_qnty_from==2) )
		{
			$available_qnty = return_field_value("available_qnty", "product_details_master", "id=".$prod_id." and status_active=1", "available_qnty");
			//for same product id
			if($prod_id = $originalProductId)
			{
				if($txt_yarn_qnty > ($available_qnty + $txt_old_qnty))
				{
					echo "18**Requisiiton Quantity is not available.\nAvailable quantity = ".$available_qnty;
					disconnect($con);
					exit();
				}
			}
			else
			{
				if($txt_yarn_qnty > $available_qnty)
				{
					echo "18**Requisiiton Quantity is not available.\nAvailable quantity = ".$available_qnty;
					disconnect($con);
					exit();
				}
			}

			$check_allocation = sql_select("SELECT a.id, a.mst_id, a.po_break_down_id, a.qnty from inv_material_allocation_dtls a WHERE a.item_id=".$prod_id." AND a.booking_no=".$booking_no." AND a.status_active=1 AND a.is_deleted=0 GROUP BY a.id, a.mst_id, a.po_break_down_id, a.qnty");
			if(!empty($check_allocation))
			{
				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_dtls
				| data updating
				|--------------------------------------------------------------------------
				|
				*/
				$field_allocation_dtls = "qnty*inserted_by*insert_date";
				$qnty_break_down="";
				$allocation_qnty=0;
				foreach ($check_allocation as $dtls_allocation)
				{
					$dtls_id = $dtls_allocation[csf('id')];
					if($prod_id != $originalProductId)
					{
						$dtls_allocation_qnty = $dtls_allocation[csf('qnty')] + $txt_old_qnty;
					}
					else
					{
						$dtls_allocation_qnty = $dtls_allocation[csf('qnty')] + ($txt_yarn_qnty - $txt_old_qnty);
					}

					$data_allocation_dtls = "" . $dtls_allocation_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
					$qnty_break_down = number_format($dtls_allocation_qnty,2,'.','') . "_" . $dtls_allocation[csf('po_break_down_id')] . "_";
					$allocation_qnty += $dtls_allocation_qnty;
					$material_alocation_update_id = $dtls_allocation[csf('mst_id')];
					$allocation_dtls_update = sql_update("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, "id", "" . $dtls_id . "", 0);
				}

				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_mst
				| data updating
				|--------------------------------------------------------------------------
				|
				*/
				$field_allocation = "qnty*qnty_break_down*updated_by*update_date";
				$data_allocation = "" . $allocation_qnty . "*'" . $qnty_break_down . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
				$allocation_mst_update = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", "" . $material_alocation_update_id . "", 0);
				if ($allocation_mst_update)
				{
					$alocationFlag = 1;
				}
				else
				{
					$alocationFlag = 0;
				}

				if($prod_id != $originalProductId)
				{
					$allocation_qnty = $txt_yarn_qnty;
				}
				else
				{
					$allocation_qnty = $txt_yarn_qnty-$txt_old_qnty;
				}

				/*
				|--------------------------------------------------------------------------
				| product_details_master
				| data updating
				|--------------------------------------------------------------------------
				|
				*/
				$product_allocation_update = execute_query("UPDATE product_details_master SET allocated_qnty = (allocated_qnty+$allocation_qnty) WHERE id = ".$prod_id."",0);
				$product_available_update = execute_query("UPDATE product_details_master SET available_qnty = (current_stock-allocated_qnty) WHERE id = ".$prod_id."", 0);
			}
			else
			{
				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_mst
				| data preparing for
				| $data_allocation
				|--------------------------------------------------------------------------
				|
				*/
				$is_dyed_yarn = (str_replace("'", "", $is_dyed_yarn)!=1)?0:1;
				$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
				$field_allocation = "id,mst_id,entry_form,booking_no,po_break_down_id,item_category,allocation_date,item_id,qnty,qnty_break_down,is_dyied_yarn,is_sales,inserted_by,insert_date";
				$data_allocation = "(".$allocation_id.",".$id.",385,".$booking_no.",".$booking_id.",1".",".$txt_reqs_date.",".$prod_id.",".$txt_yarn_qnty.",'".$qnty_breck_down."',0,2,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_dtls
				| data preparing for
				| $data_allocation_dtls
				|--------------------------------------------------------------------------
				|
				*/
				$field_allocation_dtls = "id,mst_id,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
				$dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
				$data_allocation_dtls = "(" . $dtls_id . "," . $allocation_id . "," . $booking_id . "," . $booking_no . ",1," . $txt_reqs_date . "," . $prod_id . "," . $txt_yarn_qnty . ",". $is_dyed_yarn ."," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_mst
				| inv_material_allocation_dtls
				| data inserting
				|--------------------------------------------------------------------------
				|
				*/
				$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);
				if ($allocation_mst_insert)
				{
					$alocationFlag = 1;
					$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
					if($allocation_dtls_insert) $alocationFlag = 1; else $alocationFlag = 0;
				}
				else
				{
					$alocationFlag = 0;
				}

				/*
				|--------------------------------------------------------------------------
				| product_details_master
				| data updating
				|--------------------------------------------------------------------------
				|
				*/
				$product_allocation_update = execute_query("UPDATE product_details_master SET allocated_qnty = (allocated_qnty+$txt_yarn_qnty) WHERE id = ".$prod_id."",0);
				$product_available_update = execute_query("UPDATE product_details_master SET available_qnty = (current_stock-allocated_qnty) WHERE id = ".$prod_id."", 0);
			}

			if($prod_id != $originalProductId)
			{
				$check_allocation = sql_select("SELECT a.id, a.mst_id, a.po_break_down_id, a.qnty FROM inv_material_allocation_dtls a WHERE a.item_id = ".$originalProductId." AND a.booking_no = ".$booking_no." AND a.status_active=1 AND a.is_deleted=0 GROUP BY a.id, a.mst_id, a.po_break_down_id, a.qnty");
				if(!empty($check_allocation))
				{
					/*
					|--------------------------------------------------------------------------
					| inv_material_allocation_dtls
					| data updating
					|--------------------------------------------------------------------------
					|
					*/
					$field_allocation_dtls = "qnty*inserted_by*insert_date";
					$qnty_break_down="";
					$allocation_qnty=0;
					foreach ($check_allocation as $dtls_allocation)
					{
						$dtls_id = $dtls_allocation[csf('id')];
						$dtls_allocation_qnty = $dtls_allocation[csf('qnty')] - $txt_old_qnty;
						$data_allocation_dtls = "" . $dtls_allocation_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
						$qnty_break_down = number_format($dtls_allocation_qnty,2,'.','') . "_" . $dtls_allocation[csf('po_break_down_id')] . "_";
						$allocation_qnty += $dtls_allocation_qnty;
						$material_alocation_update_id = $dtls_allocation[csf('mst_id')];
						$allocation_dtls_update = sql_update("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, "id", "" . $dtls_id . "", 0);
					}

					/*
					|--------------------------------------------------------------------------
					| inv_material_allocation_mst
					| data updating
					|--------------------------------------------------------------------------
					|
					*/
					$field_allocation = "qnty*qnty_break_down*updated_by*update_date";
					$data_allocation = "" . $allocation_qnty . "*'" . $qnty_break_down . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
					$allocation_mst_update = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", "" . $material_alocation_update_id . "", 0);
					if ($allocation_mst_update)
					{
						$alocationFlag = 1;
					}
					else
					{
						$alocationFlag = 0;
					}
				}

				/*
				|--------------------------------------------------------------------------
				| product_details_master
				| data updating
				|--------------------------------------------------------------------------
				|
				*/
				//echo "10**update product_details_master set allocated_qnty=(allocated_qnty-$txt_old_qnty) where id=$original_prod_id";die;
				$pre_product_allocation_update = execute_query("UPDATE product_details_master SET allocated_qnty=(allocated_qnty-$txt_old_qnty) WHERE id = ".$originalProductId."", 0);
				$pre_product_available_update = execute_query("UPDATE product_details_master SET available_qnty=(current_stock-allocated_qnty) WHERE id = ".$originalProductId."", 0);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_yarn_requisition_entry
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		$rID = sql_update("ppl_yarn_requisition_entry", $field_array_update, $data_array_update, "id", $update_dtls_id, 1);

		/*
		|--------------------------------------------------------------------------
		| ppl_yarn_requisition_breakdown
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		$reqBreakdown1 = true;
		if($prod_id != $originalProductId)
		{
			$reqNo = str_replace("'", "", $txt_requisition_no);
			$reqBreakdown1 = execute_query("DELETE FROM ppl_yarn_requisition_breakdown WHERE requisition_id = ".$reqNo." AND program_id = ".$updateId.") AND order_id = ".$booking_id." AND item_id =".$originalProductId."", 0);
		}

		$rID2 = execute_query($sqlRequisitionBreakdown, 0);

		/*
		|--------------------------------------------------------------------------
		| ppl_yarn_req_distribution
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		$rID3 = true;
		if($is_dist_qnty_valiable_set == 1)
		{
			$rID3 = execute_query($sqlDistribution, 0);
		}

		//oci_rollback($con);
		//echo "6**0**1**".$rID.'='.$rID2.'='.$rID3.'='.$alocationFlag.'='.$sqlDistribution; die;
		//6**0**1**1=1=0=1

		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if ($db_type == 0)
		{
			if($is_auto_allocation_from_requisition==1 && str_replace("'", "", $is_dyed_yarn) != 1)
			{
				if ($rID && $rID2 && $rID3 && $reqBreakdown1 && $alocationFlag)
				{
					mysql_query("COMMIT");
					echo "1**" . str_replace("'", "", $updateId) . "**0**" . str_replace("'", "", $txt_requisition_no);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "6**0**1";
				}
			}
			else
			{

				if ($rID && $rID2 && $rID3 && $reqBreakdown1)
				{
					mysql_query("COMMIT");
					echo "1**" . str_replace("'", "", $updateId) . "**0**" . str_replace("'", "", $txt_requisition_no);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "6**0**1";
				}
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
			if($is_auto_allocation_from_requisition==1 && str_replace("'", "", $is_dyed_yarn) != 1)
			{

				if ($rID && $rID2 && $rID3 && $reqBreakdown1 && $alocationFlag)
				{
					oci_commit($con);
					echo "1**" . str_replace("'", "", $updateId) . "**0**" . str_replace("'", "", $txt_requisition_no)."**".$alocationFlag;
				}
				else
				{
					oci_rollback($con);
					echo "6**0**1";
				}
			}
			else
			{
				if ($rID && $rID2 && $rID3 && $reqBreakdown1)
				{
					oci_commit($con);
					echo "1**" . str_replace("'", "", $updateId) . "**0**" . str_replace("'", "", $txt_requisition_no);
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

		// check if issue found against Requisition and Requisition can not be deleted
		if ($db_type == 0)
		{
			$check_issue_against_requisition_lot=sql_select("select group_concat(a.issue_number) as issue_number,sum(cons_quantity) as cons_quantity from inv_issue_master a,inv_transaction b where a.id=b.mst_id and b.receive_basis=3 and b.transaction_type=2 and b.item_category=1 and b.requisition_no=$txt_requisition_no and b.prod_id=$original_prod_id and b.status_active=1 and b.is_deleted=0");
		}
		else
		{
			$check_issue_against_requisition_lot=sql_select("select listagg(cast(a.issue_number as varchar2(4000)), ',') within group (order by a.issue_number) as issue_number,sum(cons_quantity) as cons_quantity from inv_issue_master a,inv_transaction b where a.id=b.mst_id and b.receive_basis=3 and b.transaction_type=2 and b.item_category=1 and b.requisition_no=$txt_requisition_no and b.prod_id=$original_prod_id and b.status_active=1 and b.is_deleted=0");
		}

		$check_issue_return = sql_select("select listagg(cast(a.recv_number as varchar2(4000)), ',') within group (order by a.recv_number) as issue_return_number, sum(b.cons_quantity) issue_return_qnty from inv_receive_master a,inv_transaction b where a.id=b.mst_id and a.receive_basis=3 and b.issue_id in(select a.mst_id from inv_transaction a where a.transaction_type in(2) and a.requisition_no in($txt_requisition_no) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id=$original_prod_id) and b.transaction_type=4 and b.receive_basis=3 and b.prod_id=$original_prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		$issue_balance = number_format($check_issue_against_requisition_lot[0][csf("cons_quantity")],2,".","")-number_format($check_issue_return[0][csf("issue_return_qnty")],2,".","");

		if( ($check_issue_against_requisition_lot[0][csf("issue_number")] != "" || $check_issue_against_requisition_lot[0][csf("issue_number")] != null) && ($issue_balance>0) )
		{

			echo "18**Issue found. Requisition can not be deleted.\nIssue ID = ".$check_issue_against_requisition_lot[0][csf("issue_number")]."\nIssue Quantity = ". number_format($check_issue_against_requisition_lot[0][csf("cons_quantity")],2,".","")."\nIssue Return ID =".$check_issue_return[0][csf('issue_return_number')]."\nIssue Return Quantity = ". number_format($check_issue_return[0][csf("issue_return_qnty")],2,".","")."\nIssue Balance =".number_format($issue_balance,2,".","");
			disconnect($con);
			exit();
		}

		// check if demand found against requisition
		if($db_type == 0)
		{
			$check_demand_entry_sql_select = " group_concat( b.demand_system_no ) AS demand_system_no ";
		}
		else
		{
			$check_demand_entry_sql_select = " listagg(cast(b.demand_system_no as varchar2(4000)), ',') within group (order by b.demand_system_no) as demand_system_no ";
		}

		$check_demand_entry = sql_select("select $check_demand_entry_sql_select,sum(a.yarn_demand_qnty) yarn_demand_qnty from ppl_yarn_demand_reqsn_dtls a,ppl_yarn_demand_entry_mst b,ppl_yarn_demand_entry_dtls c where a.dtls_id=c.id and a.mst_id=b.id and a.requisition_no=$txt_requisition_no and a.prod_id=$original_prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");

		if(($check_demand_entry[0][csf("yarn_demand_qnty")] != "" || $check_demand_entry[0][csf("yarn_demand_qnty")] != null) && (str_replace("'", "", $txt_yarn_qnty) != ($check_issue_return[0][csf('issue_return_qnty')])))
		{
			echo "18**Daily yarn demand found.Requisition can not be deleted.\nDemand Id=".$check_demand_entry[0][csf("demand_system_no")];
			disconnect($con);
			exit();
		}

		/*
		|--------------------------------------------------------------------------
		| ppl_yarn_requisition_entry
		| ppl_yarn_requisition_breakdown
		| ppl_yarn_req_distribution
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_update = "status_active*is_deleted*updated_by*update_date";
		$data_array_update = "0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
		$rID = sql_update("ppl_yarn_requisition_entry", $field_array_update, $data_array_update, "id", $update_dtls_id, 1);
		$rID3 = sql_update("ppl_yarn_requisition_breakdown", $field_array_update, $data_array_update, "requisition_id*program_id*item_id*order_id", $txt_requisition_no.'*'.$updateId.'*'.$original_prod_id.'*'.$booking_id, 1);
		if($is_dist_qnty_valiable_set == 1)
		{
			$rID2 = sql_update("ppl_yarn_req_distribution", $field_array_update, $data_array_update, "requisition_no*prod_id", $txt_requisition_no.'*'.$original_prod_id, 1);
		}
		else
		{
			$rID2=1;
		}


		/*
		|--------------------------------------------------------------------------
		| check valiable if auto allocation is set to Yes and Yarn is grey yarn
		|--------------------------------------------------------------------------
		|
		*/
		$alocationFlag = 1;
		if( $is_auto_allocation_from_requisition == 1 &&  ($is_dyed_yarn!=1 || $dyed_yarn_qnty_from==2)  )// yes
		{
			$check_allocation = sql_select("SELECT a.id, a.mst_id, a.po_break_down_id, a.qnty FROM inv_material_allocation_dtls a WHERE a.item_id = ".$original_prod_id." AND a.booking_no = ".$booking_no." AND a.status_active=1 AND a.is_deleted=0 GROUP BY a.id, a.mst_id, a.po_break_down_id, a.qnty");
			if(!empty($check_allocation))
			{
				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_dtls
				| data updating
				|--------------------------------------------------------------------------
				|
				*/
				$field_allocation_dtls = "qnty*inserted_by*insert_date";
				$qnty_break_down="";
				$allocation_qnty=0;
				foreach ($check_allocation as $dtls_allocation)
				{
					$dtls_id = $dtls_allocation[csf('id')];
					$dtls_allocation_qnty = $dtls_allocation[csf('qnty')] - $txt_old_qnty;
					$data_allocation_dtls = "" . $dtls_allocation_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
					$qnty_break_down = number_format($dtls_allocation_qnty,2,'.','') . "_" . $dtls_allocation[csf('po_break_down_id')] . "_";
					$allocation_qnty += $dtls_allocation_qnty;
					$material_alocation_update_id = $dtls_allocation[csf('mst_id')];
					$allocation_dtls_update = sql_update("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, "id", "" . $dtls_id . "", 0);
				}

				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_mst
				| data updating
				|--------------------------------------------------------------------------
				|
				*/
				$field_allocation = "qnty*qnty_break_down*updated_by*update_date";
				$data_allocation = "" . $allocation_qnty . "*'" . $qnty_break_down . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
				$allocation_mst_update = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", "" . $material_alocation_update_id . "", 0);

				if ($allocation_mst_update)
				{
					$alocationFlag = 1;
				}
				else
				{
					$alocationFlag = 0;
				}
			}

			/*
			|--------------------------------------------------------------------------
			| product_details_master
			| data updating
			|--------------------------------------------------------------------------
			|
			*/
			//echo "10**update product_details_master set allocated_qnty=(allocated_qnty-$txt_old_qnty) where id=$original_prod_id"; die;
			$pre_product_allocation_update = execute_query("UPDATE product_details_master SET allocated_qnty=(allocated_qnty-$txt_old_qnty) WHERE id = ".$original_prod_id."", 0);
			$pre_product_available_update = execute_query("UPDATE product_details_master SET available_qnty=(current_stock-allocated_qnty) WHERE id = ".$original_prod_id."", 0);
		}
		//echo "10**".$rID.'&&'.$rID2.'&&'.$rID3.'&&'.$alocationFlag; die;

		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if ($db_type == 0)
		{
			if ($rID && $rID2 && $rID3 && $alocationFlag)
			{
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $updateId) . "**0**" . str_replace("'", "", $txt_requisition_no);
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
		else if ($db_type == 2 || $db_type == 1)
		{
			if ($rID && $rID2 && $rID3 && $alocationFlag)
			{
				oci_commit($con);
				echo "2**" . str_replace("'", "", $updateId) . "**0**" . str_replace("'", "", $txt_requisition_no);
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

if ($action == "print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_id = $data[0];
	$program_id = $data[1];
	$path = $data[2];
	//echo $path;die;
	echo load_html_head_contents("Program Qnty Info", $path, 1, 1, '', '', '');
	//echo $company_id;die;

	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$buyer_details = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	$Sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");

	$machine_arr=array();
	$sql_mc=sql_select("select id, machine_no, floor_id from lib_machine_name");
	foreach( $sql_mc as $row)
	{
		$machine_arr[$row[csf('id')]]['machine_no']=$row[csf('machine_no')];
		$machine_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
	}
	unset($sql_mc);

	if ($db_type == 0)
	{
		$plan_details_array = return_library_array("select dtls_id, group_concat(po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company_id group by dtls_id", "dtls_id", "po_id");
	}
	else
	{
		$plan_details_array = return_library_array("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company_id group by dtls_id", "dtls_id", "po_id");
	}

	//$po_dataArray = sql_select("select id, grouping, file_no, po_number, job_no_mst from wo_po_break_down");
	$po_dataArray = sql_select("select a.id, a.grouping, a.file_no, a.po_number, a.job_no_mst,b.style_ref_no  from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no");
	foreach ($po_dataArray as $row)
	{
		$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no_mst')];
		$po_array[$row[csf('id')]]['file'] = $row[csf('file_no')];
		$po_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
	}
	unset($po_dataArray);

	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row)
	{
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0)
		{
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		}
		else
		{
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
		$product_details_array[$row[csf('id')]]['comp'] = $compos;
		$product_details_array[$row[csf('id')]]['type'] = $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}
	unset($result);
	?>
	<div style="width:860px">
		<div style="margin-left:20px; width:850px">
			<div style="width:200px;float:left;position:relative;margin-top:10px">
				<? $image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$company_id' and form_name='company_details' and is_deleted=0"); ?>
				<img src="<? echo $path . $image_location; ?>" height='100%' width='100%'/>
			</div>
			<div style="width:50px;float:left;position:relative;margin-top:10px"></div>
			<div style="float:left;position:relative; width:500px;">
				<table width="50%" style="margin-top:10px; float:left;">
					<tr>
						<td align="center" style="font-size:16px;">
							<? echo $company_details[$company_id]; ?>
						</td>
					</tr>
					<tr>
						<td align="center" style="font-size:14px">

							<?
							$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");
							foreach ($nameArray as $result) {
								if ($result[csf('plot_no')]) {echo $result[csf('plot_no')].', ';}
								if ($result[csf('level_no')]) {echo $result[csf('level_no')].', ';}
								if ($result[csf('road_no')]) {echo $result[csf('road_no')].', ';};
								if ($result[csf('block_no')]) {echo $result[csf('block_no')].', ';}
								if ($result[csf('city')]) {echo $result[csf('city')].', ';}
								if ($result[csf('zip_code')]) {echo $result[csf('zip_code')].', ';}
								if ($result[csf('country_id')]) {echo $country_arr[$result[csf('country_id')]];} ?><br>
								Email Address: <? echo $result[csf('email')]; ?>
								Website No: <? echo $result[csf('website')];
							}

							$dataArray = sql_select("select id, mst_id, knitting_source, knitting_party, subcontract_party, program_date, color_range, stitch_length, machine_dia, machine_gg, program_qnty, machine_id, remarks, location_id, advice, feeder, width_dia_type, color_id,attention,fabric_dia from ppl_planning_info_entry_dtls where id=$program_id");

							echo $buyer_details[$buyer_id];
							$po_id = array_unique(explode(",", $plan_details_array[$dataArray[0][csf('id')]]));
							$po_no = '';
							$job_no = '';
							$ref_cond = '';
							$file_cond = '';
							$styleRef="";
							foreach ($po_id as $val) {
								if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= "," . $po_array[$val]['no'];
								if ($job_no == '') $job_no = $po_array[$val]['job_no'];
								if ($ref_cond == "") $ref_cond = $po_array[$val]['ref']; else $ref_cond .= "," . $po_array[$val]['ref'];
								if ($file_cond == "") $file_cond = $po_array[$val]['file']; else $file_cond .= "," . $po_array[$val]['file'];
								if ($styleRef == '') $styleRef = $po_array[$val]['style_ref']; else $styleRef .= "," . $po_array[$val]['style_ref'];

							}

							$machine_no = ''; $floor_id_all='';
							$machine_id = explode(",", $dataArray[0][csf("machine_id")]);
							foreach ($machine_id as $val) {
								if ($machine_no == '') $machine_no = $machine_arr[$val]['machine_no']; else $machine_no .= "," . $machine_arr[$val]['machine_no'];
								if ($floor_id_all == '') $floor_id_all = $machine_arr[$val]['floor_id']; else $floor_id_all .= "," . $machine_arr[$val]['floor_id'];
							}

							$floor_name="";
							$floor_ids = array_filter(array_unique(explode(",", $floor_id_all)));
							foreach ($floor_ids as $ids) {
								if ($floor_name == '') $floor_name = $floor_arr[$ids]; else $floor_name .= "," . $floor_arr[$ids];
							}
							?>

						</td>

					</tr>
					<tr>
						<td height="10"></td>
					</tr>
					<tr>
						<td width="100%" align="center" style="font-size:18px;"><b><u>Knitting Program - <? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?> </u></b></td>
					</tr>
				</table>
				<table style="width:45%; float:right; margin-top:10px;">
					<tr>
						<td>PROGRAM NO: </td>
						<td><? echo $dataArray[0][csf('id')]; ?></td>
					</tr>
					<tr>
						<td>PROGRAM DATE:</td>
						<td><? echo change_date_format($dataArray[0][csf('program_date')]); ?></td>
					</tr>
					<tr>
						<td>JOB NO:</td>
						<td><? echo $job_no; ?></td>
					</tr>
				</table>
			</div>
		</div>
		<div style="margin-left:10px;float:left; width:850px;">
			<?


			$location = return_field_value("location_name", "lib_location", "id='" . $dataArray[0][csf('location_id')] . "'");
			$advice = $dataArray[0][csf('advice')];

			$mst_dataArray = sql_select("select a.booking_no,a.buyer_id,a.fabric_desc,a.gsm_weight,b.fabric_dia from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and b.id=" . $dataArray[0][csf('id')]);
			$booking_no = $mst_dataArray[0][csf('booking_no')];
			$buyer_id = $mst_dataArray[0][csf('buyer_id')];
			$fabric_desc = $mst_dataArray[0][csf('fabric_desc')];
			$gsm_weight = $mst_dataArray[0][csf('gsm_weight')];
			$dia = $mst_dataArray[0][csf('fabric_dia')];


			$irDetails = return_library_array("SELECT a.booking_no,b.internal_ref from wo_non_ord_samp_booking_dtls a,sample_development_mst b where a.style_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='$booking_no'", 'booking_no', 'internal_ref');

			?>
			<div style="width:300px; float:left;">
				<table width="100%" style="margin-top:5px" cellspacing="7">
					<tr>
						<td>To</td>
					</tr>
					<tr>
						<!-- <td width="140"><b>Program No:</b></td>-->
						<td width=""><? echo $dataArray[0][csf('attention')]; ?></td>
					</tr>
					<tr>
						<td width="">
							<?
							if ($dataArray[0][csf('knitting_source')] == 1) echo $company_details[$dataArray[0][csf('knitting_party')]];
							else if ($dataArray[0][csf('knitting_source')] == 3) echo $supllier_arr[$dataArray[0][csf('knitting_party')]];
							?>
						</td>
					</tr>
					<tr>
						<td width="">
							<?
							$address_knit = '';
							if ($dataArray[0][csf('knitting_source')] == 1) {
								$addressArray_knit = sql_select("select plot_no,level_no,road_no,block_no,country_id,city from lib_company where id=$company_id");
								foreach ($addressArray_knit as $result) {
									if ($result[csf('plot_no')]) {echo $result[csf('plot_no')].', ';}
									if ($result[csf('road_no')]) {echo $result[csf('road_no')].', ';}
									if ($result[csf('block_no')]) {echo $result[csf('block_no')].', ';}
									if ($result[csf('city')]) {echo $result[csf('city')].', ';}
								}
							} else if ($dataArray[0][csf('knitting_source')] == 3) {
								$address_knit = return_field_value("address_1", "lib_supplier", "id=" . $dataArray[0][csf('knitting_party')]);
								echo $address_knit;
							}
							?>
						</td>
					</tr>
					<tr>
						<td width="200">Knitting Location:</td>
						<td width=""><? echo $location; ?></td>
					</tr>
					<tr>
						<td width="">Machine No:</td>
						<td width=""><? echo $machine_no; ?></td>
					</tr>
				</table>
			</div>
			<div style="float:left; width:550px;">
				<table width="100%" style="margin-top:5px" cellspacing="7">
					<tr>
						<td><b>Fabrication & FGSM:</b></td>
						<td><? echo $fabric_desc . " & " . $gsm_weight; ?></td>
					</tr>
					<tr>
						<td><b>Buyer Name:</b></td>
						<td><? echo $buyer_details[$buyer_id]; ?></td>
					</tr>
					<tr>
						<td><b>Booking No:</b></td>
						<td><b><? echo $booking_no; ?></b></td>
					</tr>
					<tr>
						<td><b>Style Ref:</b></td>
						<td><b><? echo implode(",", array_unique(explode(",", $styleRef))); ?></b></td>
					</tr>
					<?
					$booking_data_arr = explode("-",$booking_no);
					if($booking_data_arr[1] !='SMN'){?>
					<tr>
						<td><b>Order No:</b></td>
						<td><? echo implode(",", array_unique(explode(",", $po_no))); ?></td>
					</tr>
					<tr>
						<td><b>File No:</b></td>
						<td><b><? echo implode(",", array_unique(explode(",", $file_cond))); ?></b></td>
					</tr>
					<? } ?>
					<tr>
						<td><b>IR/CN:</b></td>
						<td><b>
							<?
							if($ref_cond !='')
							{
								echo implode(",", array_unique(explode(",", $ref_cond)));
							}
							else
							{
								if($booking_data_arr[1] =='SMN')
								{
									echo $irDetails[$booking_no];
								}
							}
							 echo implode(",", array_unique(explode(",", $ref_cond)));
							 ?>
						</b></td>
					</tr>
					<tr>
						<td><b>Remarks: </b></td>
						<td><? echo $dataArray[0][csf("remarks")]; ?></td>
					</tr>
				</table>
			</div>


			<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
			class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="70">Requisition No</th>
				<th width="70">Lot No</th>
				<th width="225">Yarn Description</th>
				<th width="110">Brand</th>
				<th width="80">Requisition Qnty</th>
				<th width="120">Yarn Color</th>
				<th>Remarks</th>
			</thead>
			<?
			$i = 1;
			$tot_reqsn_qnty = 0;
			$sql = "select requisition_no, prod_id, yarn_qnty, inserted_by from ppl_yarn_requisition_entry where knit_id='" . $dataArray[0][csf('id')] . "' and status_active=1 and is_deleted=0";
			//echo $sql;
			$nameArray = sql_select($sql);
			foreach ($nameArray as $selectResult) {
				?>
				<tr>
					<td align="center"><? echo $i; ?></td>
					<td align="center"><p><? echo $selectResult[csf('requisition_no')]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?>
				&nbsp;</p></td>
				<td>
					<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?>
				&nbsp;</p></td>
				<td><p><? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?>&nbsp;</p></td>
				<td align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?></td>
				<td><p>
					&nbsp;&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['color']; ?></p>
				</td>
				<td>&nbsp;</td>
			</tr>
			<?
			$tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
			$i++;
		}
		?>
		<tfoot>
			<th colspan="5" align="right"><b>Total</b></th>
			<th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?>&nbsp;&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tfoot>
	</table>
	<table width="850" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top:20px;"
	class="rpt_table">
	<tr>
		<td width="120"><b>Colour Range:</b></td>
		<td width="150"><p><? echo $color_range[$dataArray[0][csf('color_range')]]; ?>&nbsp;</p></td>
		<td width="120"><b>GGSM OR S/L:</b></td>
		<td width="150"><p><? echo $dataArray[0][csf('stitch_length')]; ?>&nbsp;</p></td>
		<td width="120"><b>FGSM:</b></td>
		<td><p><? echo $gsm_weight; ?>&nbsp;</p></td>
	</tr>
	<tr>
		<td><b>Finish Dia</b></td>
		<td><p><? echo $dia . "  (" . $fabric_typee[$dataArray[0][csf('width_dia_type')]] . ")"; ?>
	&nbsp;</p></td>
	<td><b>Machine Dia & Gauge:</b></td>
	<td><p><? echo $dataArray[0][csf('machine_dia')] . "X" . $dataArray[0][csf('machine_gg')]; ?>
&nbsp;</p></td>
<td><b>Program Qnty:</b></td>
<td><p><? echo number_format($dataArray[0][csf('program_qnty')], 2); ?>&nbsp;</p></td>
</tr>
<tr>
<td><b>Feeder:</b></td>
<td><p>
	<?
	$feeder_array = array(1 => "Full Feeder", 2 => "Half Feeder");
	echo $feeder_array[$dataArray[0][csf('feeder')]];
	?>&nbsp;</p></td>
	<td><b>Garments Color</b></td>
	<td><p>
		<?
		$color_id_arr = array_unique(explode(",", $dataArray[0][csf('color_id')]));
		$all_color = "";
		foreach ($color_id_arr as $color_id) {
			$all_color .= $color_library[$color_id] . ",";
		}
		$all_color = chop($all_color, ",");
		echo $all_color;

		?>&nbsp;</p></td>
		<td><b>Remarks</b></td>
		<td><p><? echo $dataArray[0][csf('remarks')]; ?>&nbsp;</p></td>
	</tr>
	<tr>
		<td><b> Finish Dia[Plan Wise]:</b></td>
		<td><p><? echo $dataArray[0][csf('fabric_dia')]; ?>&nbsp;</p></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
</table>

<!-- <table style="margin-top:20px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
	<thead>
		<th width="110">Finish Dia</th>
		<th width="220">Machine Dia & Gauge</th>
		 <th width="110">Program Qnty</th>
		<th>Remarks</th>
	</thead>
	<tr>
		<td width="150">&nbsp;&nbsp;</td>
		<td width="280">&nbsp;&nbsp;<?// echo $dataArray[0][csf('machine_dia')]."X".$dataArray[0][csf('machine_gg')];
?></td>
		<td width="150" align="right">&nbsp;&nbsp;<?// echo number_format( $dataArray[0][csf('program_qnty')],2);
?>&nbsp;&nbsp;</td>
		<td><?// echo $dataArray[0][csf('remarks')];
?>&nbsp;</td>
	</tr>
</table>-->
<?
//$sql_fedder=sql_select("select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and a.dtls_id=$program_id and a.no_of_feeder>0 group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder order by a.id");
//echo "select pre_cost_id, color_id, stripe_color_id, no_of_feeder from ppl_planning_feeder_dtls where dtls_id = $program_id";die;
$sql = "select pre_cost_id, color_id, stripe_color_id,no_of_feeder from ppl_planning_feeder_dtls where status_active=1 and is_deleted=0 and dtls_id = $program_id order by id";
$sql_fedder = sql_select($sql);

$$preCostIdArray = Array();
$i = 0;
foreach ($sql_fedder as $preCostIdRows) {

	$preCostIdArray[$preCostIdRows[csf("pre_cost_id")]] = $preCostIdRows[csf("pre_cost_id")];
	$no_of_feeders[$i] = $preCostIdRows[csf('no_of_feeder')];
	//$no_of_feeders[$preCostIdRows[csf('pre_cost_id')]][$preCostIdRows[csf('color_id')]][$preCostIdRows[csf('stripe_color_id')]] = $preCostIdRows[csf('no_of_feeder')];

	$i++;

}

$preCostIdAr = implode(",", $preCostIdArray);
if ($preCostIdAr == "") {
	$preCostIdAr = 0;
}

$sql_measurementUOM = sql_select("select pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color, measurement, uom from wo_pre_stripe_color where is_deleted = 0 and status_active = 1 and pre_cost_fabric_cost_dtls_id in ($preCostIdAr) order by color_number_id,id");

if (count($sql_fedder) > 0) {
	?>
	<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
	class="rpt_table">
	<thead>
		<tr>
			<th width="50">SL</th>
			<th width="200">Color</th>
			<th width="200">Stripe Color</th>
			<th width="120">Measurement</th>
			<th width="120">UOM</th>
			<th>No Of Feeder</th>
		</tr>
	</thead>
	<tbody>
		<?
		$i = 1;
		$total_feeder = 0;
		$k = 0;

		foreach ($sql_measurementUOM as $row) {

			$no_of_feeder = $no_of_feeders[$k];
			//$no_of_feeder = $no_of_feeders[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]];

			$k++;

			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";
			?>
			<tr>
				<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
				<td><p><? echo $color_library[$row[csf('color_number_id')]]; ?>&nbsp;</p></td>
				<td><p><? echo $color_library[$row[csf('stripe_color')]]; ?>&nbsp;</p></td>
				<td align="right"><p><? echo number_format($row[csf('measurement')], 2); ?>&nbsp;</p></td>
				<td align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
				<td align="right"><p><? echo number_format($no_of_feeder, 0);
				$total_feeder += $no_of_feeder; ?>&nbsp;</p></td>
			</tr>
			<?
			$i++;
		}
		?>
	</tbody>
	<tfoot>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th align="right">Total:</th>
			<th align="right"><? echo number_format($total_feeder, 0); ?></th>
		</tr>
	</tfoot>
</table>
<br>
<?
}

$sql_color_size_data = "SELECT a.id, a.body_part_id, a.grey_size, a.finish_size, a.qty_pcs from ppl_planning_collar_cuff_dtls a where a.dtls_id = ".$dataArray[0][csf('id')]." order by a.grey_size";
//echo $sql_color_size_data;

$color_size_data_rslt = sql_select($sql_color_size_data);

?>

<div style="width: 1300px; margin-top: 10px; ">

		<div style="width: 500px; float: left;">
			<table cellspacing="0" width="400"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center" style="font-size:12px">
					<tr>
						<th colspan="5">Collor and Cuff measurement Info</th>
					</tr>
					<tr>
						<th width="30" style="text-align: center;">SL</th>
						<th width="150" style="text-align: center;">Body Part</th>
						<th width="150" style="text-align: center;">Grey Size</th>
						<th width="150" style="text-align: center;">Finish Size</th>
						<th width="100" style="text-align: center;">Qty. Pcs</th>
					</tr>
				</thead>
				<? $total_qty = 0;
				$i=1;
				foreach ($color_size_data_rslt as $row) { ?>
					<tr>
						<td style="text-align: center;"><? echo $i ?></td>
						<td style="text-align: center;"><? echo $row[csf('body_part_id')] ?></td>
						<td style="text-align: center;"><? echo $row[csf('grey_size')] ?></td>
						<td style="text-align: center;"><? echo $row[csf('finish_size')] ?></td>
						<td style="text-align: right;"><? echo $row[csf('qty_pcs')]; $total_qty += $row[csf('qty_pcs')]; ?></td>
					</tr>

				<?
				$i++;
				 } ?>
				<tr>
					<td colspan="4" align="right"><strong>Total:</strong></td>
					<td align="right" style="font-weight:bold"><? echo $total_qty; ?></td>
				</tr>
			</table>
		</div>

</div>
<br><br>
<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
class="rpt_table">
<tr>
	<td colspan="4" style="word-wrap:break-word"><b>Advice:</b><? echo str_replace(array('\n',';'),'</br>',$advice); ?></td>
</tr>
</table>

<?
echo signature_table(305, $data[0], "850px",'',0,$nameArray[0][csf('inserted_by')]);
?>

<!-- <table width="850">
<tr>
<td width="100%" height="90" colspan="4"></td>
</tr>
<tr>
<td width="25%" align="center"><strong style="text-decoration:overline">Checked By</strong></td>
<td width="25%" align="center"><strong style="text-decoration:overline">Receive By</strong></td>
<td width="25%" align="center"><strong style="text-decoration:overline">Knitting Manager</strong>
	<td width="25%" align="center"><strong style="text-decoration:overline">Knitting GM</strong>
	</td>
	<td width="25%" align="center"><strong style="text-decoration:overline">Authorised By</strong></td>
</tr>
</table> -->
</div>
</div>
<?
exit();
}

if ($action == "print_popup")
{
	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_id = $data[0];
	$program_id = $data[1];
	$template_id = $data[2];
	//echo $company_id;die;

	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$buyer_details = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");

	if ($db_type == 0) {
		$plan_details_array = return_library_array("select dtls_id, group_concat(distinct(po_id)) as po_id from ppl_planning_entry_plan_dtls where company_id=$company_id group by dtls_id", "dtls_id", "po_id");
	} else {
		$plan_details_array = return_library_array("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company_id group by dtls_id", "dtls_id", "po_id");
	}

	$po_dataArray = sql_select("select id, grouping,file_no,po_number, job_no_mst from wo_po_break_down");
	foreach ($po_dataArray as $row) {
		$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no_mst')];
		$po_array[$row[csf('id')]]['file'] = $row[csf('file_no')];
		$po_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
	}

	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row) {
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
		$product_details_array[$row[csf('id')]]['comp'] = $compos;
		$product_details_array[$row[csf('id')]]['type'] = $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}

	?>
	<div style="width:860px">
		<div style="margin-left:20px; width:850px">
			<div style="width:100px;float:left;position:relative;margin-top:10px">
				<? $image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$company_id' and form_name='company_details' and is_deleted=0"); ?>
				<img src="../../<? echo $image_location; ?>" height='100%' width='100%'/>
			</div>
			<div style="width:50px;float:left;position:relative;margin-top:10px"></div>
			<div style="float:left;position:relative;">
				<table width="100%" style="margin-top:10px">
					<tr>
						<td align="center" style="font-size:16px;">
							<? echo $company_details[$company_id]; ?>
						</td>
					</tr>
					<tr>
						<td align="center" style="font-size:14px">
							<?
							$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");
							foreach ($nameArray as $result) {
								?>
								Plot No: <? echo $result['plot_no']; ?>
								Level No: <? echo $result['level_no'] ?>
								Road No: <? echo $result['road_no']; ?>
								Block No: <? echo $result['block_no']; ?>
								City No: <? echo $result['city']; ?>
								Zip Code: <? echo $result['zip_code']; ?>
								Country: <? echo $country_arr[$result['country_id']]; ?><br>
								Email Address: <? echo $result['email']; ?>
								Website No: <? echo $result['website'];
							}
							?>
						</td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>
					<tr>
						<td width="100%" align="center" style="font-size:14px;"><b><u>Knitting Program</u></b></td>
					</tr>
				</table>
			</div>
		</div>
		<div style="margin-left:20px;float:left; width:850px;">
			<?
			$dataArray = sql_select("select id, mst_id, knitting_source, knitting_party, program_date, color_range, stitch_length, machine_dia, machine_gg, program_qnty, machine_id, remarks, location_id, advice, feeder, width_dia_type, color_id from ppl_planning_info_entry_dtls where id=$program_id");

			$location = return_field_value("location_name", "lib_location", "id='" . $dataArray[0][csf('location_id')] . "'");
			$advice = $dataArray[0][csf('advice')];

			$mst_dataArray = sql_select("select booking_no, buyer_id, fabric_desc, gsm_weight, dia from ppl_planning_info_entry_mst where id=" . $dataArray[0][csf('mst_id')]);
			$booking_no = $mst_dataArray[0][csf('booking_no')];
			$buyer_id = $mst_dataArray[0][csf('buyer_id')];
			$fabric_desc = $mst_dataArray[0][csf('fabric_desc')];
			$gsm_weight = $mst_dataArray[0][csf('gsm_weight')];
			$dia = $mst_dataArray[0][csf('dia')];

			?>
			&nbsp;&nbsp;<b>Attention- Knitting Manager</b>
			<table width="100%" style="margin-top:20px" cellspacing="7">
				<tr>
					<td width="140"><b>Program No:</b></td>
					<td width="170"><? echo $dataArray[0][csf('id')]; ?></td>
					<td width="170"><b>Program Date:</b></td>
					<td><? echo change_date_format($dataArray[0][csf('program_date')]); ?></td>
				</tr>
				<tr>
					<td><b>Factory:</b></td>
					<td>
						<?
						if ($dataArray[0][csf('knitting_source')] == 1) echo $company_details[$dataArray[0][csf('knitting_party')]];
						else if ($dataArray[0][csf('knitting_source')] == 3) echo $supllier_arr[$dataArray[0][csf('knitting_party')]];
						?>
					</td>
					<td><b>Fabrication & FGSM:</b></td>
					<td><? echo $fabric_desc . " & " . $gsm_weight; ?></td>
				</tr>
				<tr>
					<td><b>Address:</b></td>
					<td colspan="3">
						<?
						$address = '';
						if ($dataArray[0][csf('knitting_source')] == 1) {
							$addressArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,city from lib_company where id=$company_id");
							foreach ($nameArray as $result) {
								?>
								Plot No: <? echo $result[csf('plot_no')]; ?>
								Road No: <? echo $result[csf('road_no')]; ?>
								Block No: <? echo $result[csf('block_no')]; ?>
								City No: <? echo $result[csf('city')];
							}
						} else if ($dataArray[0][csf('knitting_source')] == 3) {
							$address = return_field_value("address_1", "lib_supplier", "id=" . $dataArray[0][csf('knitting_party')]);
							echo $address;
						}

						$po_id = array_unique(explode(",", $plan_details_array[$dataArray[0][csf('id')]]));
						$po_no = '';
						$job_no = '';
						$ref_cond = '';
						$file_cond = '';
						foreach ($po_id as $val) {
							if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= "," . $po_array[$val]['no'];
							if ($job_no == '') $job_no = $po_array[$val]['job_no'];
							if ($ref_cond == "") $ref_cond = $po_array[$val]['ref']; else $ref_cond .= "," . $po_array[$val]['ref'];
							if ($file_cond == "") $file_cond = $po_array[$val]['file']; else $file_cond .= "," . $po_array[$val]['file'];
						}

						$machine_no = '';
						$machine_id = explode(",", $dataArray[0][csf("machine_id")]);
						foreach ($machine_id as $val) {
							if ($machine_no == '') $machine_no = $machine_arr[$val]; else $machine_no .= "," . $machine_arr[$val];
						}
						?>
					</td>
				</tr>
				<tr>
					<td><b>Order No:</b></td>
					<td><? echo implode(",", array_unique(explode(",", $po_no))); ?></td>
					<td><b>Booking No:</b></td>
					<td><b><? echo $booking_no; ?></b></td>
				</tr>
				<tr>
					<td><b>Job No:</b></td>
					<td><b><? echo $job_no; ?></b></td>
					<td><b>Machine No:</b></td>
					<td><b><? echo $machine_no; ?></b></td>
				</tr>

				<tr>
					<td><b>Internal Ref:</b></td>
					<td><b><? echo implode(",", array_unique(explode(",", $ref_cond))); ?></b></td>
					<td><b>File No:</b></td>
					<td><b><? echo implode(",", array_unique(explode(",", $file_cond))); ?></b></td>
				</tr>
			</table>
			<?
			$distribute_qnty_variable ="";
			if(trim($company_id)!="")
			{
			$distribute_qnty_variable = return_field_value("distribute_qnty", "variable_settings_production", "company_name='$company_id' and variable_list=5 and status_active=1 and is_deleted=0", "distribute_qnty");
			}
			?>
			<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
			class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="70">Requisition No</th>
				<th width="70">Lot No</th>
				<th width="220">Yarn Description</th>
				<th width="110">Brand</th>
				<? if($distribute_qnty_variable == 1){?>
					<th width="80">Distribution Qnty</th>
				<? } ?>
				<th width="80">Requisition Qnty</th>
				<? if($distribute_qnty_variable == 1){?>
					<th width="80">Returnable Qnty</th>
				<? } ?>
				<th width="120">Yarn Color</th>
				<th>Remarks</th>
			</thead>
			<?
			$i = 1;
			$tot_reqsn_qnty = 0;

			$sql = "select requisition_no, prod_id, yarn_qnty from ppl_yarn_requisition_entry where knit_id='" . $dataArray[0][csf('id')] . "' and status_active=1 and is_deleted=0";
			$nameArray = sql_select($sql);
			foreach ($nameArray as $selectResult) {
				?>
				<tr>
					<td align="center"><? echo $i; ?></td>
					<td align="center"><p><? echo $selectResult[csf('requisition_no')]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?>
				&nbsp;</p></td>
				<td>
					<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?>
				&nbsp;</p></td>
				<td><p><? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?>&nbsp;</p></td>
				<?
				if($distribute_qnty_variable == 1){
					$existing_dist = return_field_value("sum(distribution_qnty) as exis_distribution_qnty","ppl_yarn_req_distribution","requisition_no=".$selectResult[csf('requisition_no')]." and prod_id=".$selectResult[csf('prod_id')]." and status_active=1 and is_deleted=0 ",'exis_distribution_qnty');
					?>
					<td align="right"><? echo number_format($existing_dist, 2); ?></td>
					<?
				}
				?>
				<td align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?></td>
				<?
				if($distribute_qnty_variable == 1){
					?>
					<td align="right"><? echo $returnable = (($selectResult[csf('yarn_qnty')]-$existing_dist) > 0)?number_format($selectResult[csf('yarn_qnty')]-$existing_dist, 2):""; ?></td>
					<?
				}
				?>
				<td><? echo $product_details_array[$selectResult[csf('prod_id')]]['color']; ?></td>
				<td>&nbsp;</td>
			</tr>
			<?
			$tot_dist_qnty += $existing_dist;
			$tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
			$tot_returnable_qnty += $returnable;
			$i++;
		}
		?>
		<tfoot>
			<th colspan="5" align="right"><b>Total</b></th>
			<? if($distribute_qnty_variable == 1){ ?>
				<th align="right"><? echo number_format($tot_dist_qnty, 2); ?></th>
			<? } ?>
			<th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?></th>
			<? if($distribute_qnty_variable == 1){ ?>
				<th align="right"><? echo number_format($tot_returnable_qnty, 2); ?></th>
			<? } ?>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tfoot>
	</table>
	<table width="850" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top:20px;"
	class="rpt_table">
	<tr>
		<td width="120"><b>Colour Range:</b></td>
		<td width="150"><p><? echo $color_range[$dataArray[0][csf('color_range')]]; ?>&nbsp;</p></td>
		<td width="120"><b>GGSM OR S/L:</b></td>
		<td width="150"><p><? echo $dataArray[0][csf('stitch_length')]; ?>&nbsp;</p></td>
		<td width="120"><b>FGSM:</b></td>
		<td><p><? echo $gsm_weight; ?>&nbsp;</p></td>
	</tr>
	<tr>
		<td><b>Finish Dia</b></td>
		<td><p><? echo $dia . "  (" . $fabric_typee[$dataArray[0][csf('width_dia_type')]] . ")"; ?>
	&nbsp;</p></td>
	<td><b>Machine Dia & Gauge:</b></td>
	<td><p><? echo $dataArray[0][csf('machine_dia')] . "X" . $dataArray[0][csf('machine_gg')]; ?>
&nbsp;</p></td>
<td><b>Program Qnty:</b></td>
<td><p><? echo number_format($dataArray[0][csf('program_qnty')], 2); ?>&nbsp;</p></td>
</tr>
<tr>
	<td><b>Feeder:</b></td>
	<td><p>
		<?
		$feeder_array = array(1 => "Full Feeder", 2 => "Half Feeder");
		echo $feeder_array[$dataArray[0][csf('feeder')]];
		?>&nbsp;</p></td>
		<td><b>Garments Color</b></td>
		<td><p>
			<?
			$color_id_arr = array_unique(explode(",", $dataArray[0][csf('color_id')]));
			$all_color = "";
			foreach ($color_id_arr as $color_id) {
				$all_color .= $color_library[$color_id] . ",";
			}
			$all_color = chop($all_color, ",");
			echo $all_color;

			?>&nbsp;</p></td>
			<td><b>Remarks</b></td>
			<td><p><? echo $dataArray[0][csf('remarks')]; ?>&nbsp;</p></td>
		</tr>
	</table>

            <!--<table style="margin-top:20px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
                <thead>
                    <th width="110">Finish Dia</th>
                    <th width="220">Machine Dia & Gauge</th>
                     <th width="110">Program Qnty</th>
                    <th>Remarks</th>
                </thead>
                <tr>
                    <td width="150">&nbsp;&nbsp;<?// echo $dia;
			?></td>
                    <td width="280">&nbsp;&nbsp;<?// echo $dataArray[0][csf('machine_dia')]."X".$dataArray[0][csf('machine_gg')];
			?></td>
                    <td width="150" align="right">&nbsp;&nbsp;<?// echo number_format( $dataArray[0][csf('program_qnty')],2);
			?>&nbsp;&nbsp;</td>
                    <td><?// echo $dataArray[0][csf('remarks')];
			?></td>
                </tr>
            </table>-->
            <?
			//$sql_fedder=sql_select("select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and a.dtls_id=$program_id and a.no_of_feeder>0 group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder order by a.id");

            $sql = "select pre_cost_id, color_id, stripe_color_id,no_of_feeder from ppl_planning_feeder_dtls where status_active=1 and is_deleted=0 and dtls_id = $program_id order by id";
            $sql_fedder = sql_select($sql);

            $$preCostIdArray = Array();
            $i = 0;
            foreach ($sql_fedder as $preCostIdRows) {

            	$preCostIdArray[$preCostIdRows[csf("pre_cost_id")]] = $preCostIdRows[csf("pre_cost_id")];
            	$no_of_feeders[$i] = $preCostIdRows[csf('no_of_feeder')];
            	//$no_of_feeders[$preCostIdRows[csf("pre_cost_id")]][$preCostIdRows[csf("color_id")]][$preCostIdRows[csf("stripe_color_id")]] = $preCostIdRows[csf('no_of_feeder')];
            	$i++;

            }

            $preCostIdAr = implode(",", $preCostIdArray);
            if ($preCostIdAr == "") {
            	$preCostIdAr = 0;
            }

            $sql_measurementUOM = sql_select("select pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color, measurement, uom from wo_pre_stripe_color where is_deleted = 0 and status_active = 1 and pre_cost_fabric_cost_dtls_id in ($preCostIdAr) order by color_number_id,id");


            if (count($sql_fedder) > 0) {
            	?>
            	<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
            	class="rpt_table">
            	<thead>
            		<tr>
            			<th width="50">SL</th>
            			<th width="200">Color</th>
            			<th width="200">Stripe Color</th>
            			<th width="120">Measurement</th>
            			<th width="120">UOM</th>
            			<th>No Of Feeder</th>
            		</tr>
            	</thead>
            	<tbody>
            		<?
            		$i = 1;
            		$total_feeder = 0;
            		$k = 0;

            		foreach ($sql_measurementUOM as $row) {
            			$no_of_feeder = $no_of_feeders[$k];
            			//$no_of_feeder = $no_of_feeders[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]];
            			$k++;

            			if ($i % 2 == 0)
            				$bgcolor = "#E9F3FF";
            			else
            				$bgcolor = "#FFFFFF";
            			?>

            			<tr>
            				<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
            				<td><p><? echo $color_library[$row[csf('color_number_id')]]; ?>&nbsp;</p></td>
            				<td><p><? echo $color_library[$row[csf('stripe_color')]]; ?>&nbsp;</p></td>
            				<td align="right"><p><? echo number_format($row[csf('measurement')], 2); ?>&nbsp;</p></td>
            				<td align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
            				<td align="right"><p><? echo number_format($no_of_feeder, 0);
            				$total_feeder += $no_of_feeder; ?>&nbsp;</p></td>
            			</tr>

            			<?
            			$i++;
            		}
            		?>
            	</tbody>
            	<tfoot>
            		<tr>
            			<th></th>
            			<th></th>
            			<th></th>
            			<th></th>
            			<th align="right">Total:</th>
            			<th align="right"><? echo number_format($total_feeder, 0); ?></th>
            		</tr>
            	</tfoot>
            </table>
            <?
        }
        ?>
        <table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
        class="rpt_table">
        <tr>
        	<td colspan="4" style="word-wrap:break-word"><b>Advice:</b> <? echo str_replace(array('\n',';'),'</br>',$advice); ?></td>
        </tr>
    </table>

    <? //echo signature_table(41, $company_id, "850px",$template_id);?>
    <? echo signature_table(100, $company_id, "850px",$template_id);?>

    <!--<table width="850">
    	<tr>
    		<td width="100%" height="90" colspan="4"></td>
    	</tr>
    	<tr>
    		<td width="25%" align="center"><strong style="text-decoration:overline">Checked By</strong></td>
    		<td width="25%" align="center"><strong style="text-decoration:overline">Receive By</strong></td>
    		<td width="25%" align="center"><strong style="text-decoration:overline">Knitting Manager</strong>
    			<td width="25%" align="center"><strong style="text-decoration:overline">Knitting GM</strong>
    			</td>
    			<td width="25%" align="center"><strong style="text-decoration:overline">Authorised By</strong></td>
    		</tr>
    	</table>-->
    </div>
</div>
<?
exit();
}

// =======================

if ($action == "requisition_print")
{
	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$data = explode('*', $data);
	$company_id = $data[0];
	$program_ids = $data[1];
	$template_id = $data[2];

	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

	if ($db_type == 0) {
		$plan_details_array = return_library_array("select dtls_id, group_concat(distinct(po_id)) as po_id from ppl_planning_entry_plan_dtls where dtls_id in($program_ids) group by dtls_id", "dtls_id", "po_id");
	} else {
		$plan_details_array = return_library_array("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where dtls_id in($program_ids) group by dtls_id", "dtls_id", "po_id");
	}

	$po_ids = implode(",",$plan_details_array);
	if($po_ids!="")
	{
		$po_dataArray = sql_select("select id, po_number, job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 and id in($po_ids)");
		foreach ($po_dataArray as $row) {
			$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no_mst')];
		}
	}


	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row) {
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}


	$knit_id_array = array();
	$prod_id_array = array();
	$rqsn_array = array();
	$reqsn_dataArray = sql_select("select knit_id, requisition_no, prod_id,sum(no_of_cone) as no_of_cone , sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where knit_id in($program_ids) and status_active=1 and is_deleted=0 group by knit_id, prod_id, requisition_no");
	foreach ($reqsn_dataArray as $row) {
		$prod_id_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
		$knit_id_array[$row[csf('knit_id')]] .= $row[csf('prod_id')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsn'] .= $row[csf('requisition_no')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['qnty'] += $row[csf('yarn_qnty')];
		$rqsn_array[$row[csf('prod_id')]]['no_of_cone'] += $row[csf('no_of_cone')];
	}

	$order_no = '';
	$buyer_name = '';
	$knitting_factory = '';
	$job_no = '';
	$booking_no = '';
	$company = '';
	if ($db_type == 0) {
		$dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id, group_concat(distinct(b.po_id)) as po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id");
	} else {
		$dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id, LISTAGG(cast(b.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_id) as po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id");
	}
	foreach ($dataArray as $row)
	{
		if ($duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] == "") {
			$duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] = $row[csf('knitting_party')];

			if ($row[csf('knitting_source')] == 1)
				$knitting_factory .= $company_details[$row[csf('knitting_party')]] . ",";
			else if ($row[csf('knitting_source')] == 3)
				$knitting_factory .= $supplier_details[$row[csf('knitting_party')]] . ",";
		}

		if ($buyer_name == "")
			$buyer_name = $buyer_arr[$row[csf('buyer_id')]];
		if ($booking_no == "")
			$booking_no = $row[csf('booking_no')];
		if ($company == "")
			$company = $company_details[$row[csf('company_id')]];

		$po_id = explode(",", $row[csf('po_id')]);

		foreach ($po_id as $val) {
			$order_no .= $po_array[$val]['no'] . ",";
			if ($job_no == "")
				$job_no = $po_array[$val]['job_no'];
		}
	}

	$order_no = array_unique(explode(",", substr($order_no, 0, -1)));
	?>
	<div style="width:1200px; margin-left:5px">
		<table width="100%" style="margin-top:10px">
			<tr>
				<td width="100%" align="center" style="font-size:20px;"><b><? echo $company; ?></b></td>
				<td>
					<div style="float:right;width:24px; margin-right:80px; text-align:right">
				 		<div style="height:13px; width:15px;" id="qrcode"></div> 
           			</div>
				</td>
			</tr>
			<tr>
				<td width="100%" align="center" style="font-size:20px;"><b><u>Knitting Program</u></b></td>
			</tr>
		</table>
		<div style="border:1px solid;margin-top:10px; width:950px">
			<table width="100%" cellpadding="2" cellspacing="5">
				<tr>
					<td width="140"><b>Knitting Factory </b></td>
					<td>:</td>
					<td><? echo substr($knitting_factory, 0, -1); ?></td>
				</tr>
				<tr>
					<td><b>Buyer Name</b></td>
					<td>:</td>
					<td><? echo $buyer_name; ?></td>
				</tr>
				<tr>
					<td><b>Style </b></td>
					<td>:</td>
					<td><?
					if ($job_no != '') {
						$style_val = return_field_value("style_ref_no", "wo_po_details_master", "job_no='$job_no'", "style_ref_no");
					}

					echo $style_val;
					?></td>
				</tr>
				<tr>
					<td><b>Order No </b></td>
					<td>:</td>
					<td><? echo implode(",", $order_no); ?></td>
				</tr>
				<tr>
					<td><b>Job No </b></td>
					<td>:</td>
					<td><? echo $job_no; ?></td>
				</tr>
				<tr>
					<td><b>Booking No </b></td>
					<td>:</td>
					<td><? echo $booking_no; ?></td>
				</tr>
			</table>
		</div>
		<table width="950" style="margin-top:10px" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="100">Requisition No</th>
				<th width="100">Brand</th>
				<th width="100">Lot No</th>
				<th width="200">Yarn Description</th>
				<th width="100">Color</th>
				<th width="100">Requisition Qty.</th>
				<th>No Of Cone</th>
			</thead>
			<?
			$j = 1;
			$tot_reqsn_qty = 0;
			foreach ($rqsn_array as $prod_id => $data)
			{
				if ($j % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="30"><? echo $j; ?></td>
					<td width="100"><? $rqsn_no = substr($data['reqsn'], 0, -1);  echo $rqsn_no; ?></td>
					<td width="100"><p><? echo $product_details_array[$prod_id]['brand']; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $product_details_array[$prod_id]['lot']; ?></p></td>
					<td width="200"><p><? echo $product_details_array[$prod_id]['desc']; ?></p></th>
						<td width="100"><p><? echo $product_details_array[$prod_id]['color']; ?>&nbsp;</p></td>
						<td width="100" align="right"><p><? echo number_format($data['qnty'], 2, '.', ''); ?></p></td>
						<td align="right"><? echo number_format($data['no_of_cone']); ?></td>
					</tr>
					<?
					$tot_reqsn_qty += $data['qnty'];
					$tot_no_of_cone += $data['no_of_cone'];
					$j++;
				}
				?>
				<tfoot>
					<th colspan="6" align="right">Total</th>
					<th align="right"><? echo number_format($tot_reqsn_qty, 2, '.', ''); ?></th>
					<th><? echo number_format($tot_no_of_cone); ?></th>
				</tfoot>
			</table>

			<table style="margin-top:10px;" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
				<thead>
					<th width="25">SL</th>
					<th width="60">Program No & Date</th>
					<th width="120">Fabrication</th>
					<th width="50">GSM</th>
					<th width="50">F. Dia</th>
					<th width="60">Dia Type</th>
					<th width="50">S/L</th>
					<th width="50">Spandex S/L</th>
					<th width="50">Feeder</th>
					<th width="60">Color</th>
					<th width="60">Color Range</th>
					<th width="60">Machine No</th>
					<th width="70">Machine Dia & GG</th>
					<th width="70">Knit Plan Date</th>
					<th width="70">Prpgram Qty.</th>
					<th width="110">Yarn Description</th>
					<th width="50">Lot</th>
					<th width="70">Yarn Qty.(KG)</th>
					<th>Remarks</th>
				</thead>
				<?
				$i = 1;
				$s = 1;
				$tot_program_qnty = 0;
				$tot_yarn_reqsn_qnty = 0;
				$company_id = '';
				$sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				$nameArray = sql_select($sql);
				foreach ($nameArray as $row)
				{
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					$color = '';
					$color_id = explode(",", $row[csf('color_id')]);

					foreach ($color_id as $val)
					{
						if ($color == '')
							$color = $color_library[$val];
						else
							$color .= "," . $color_library[$val];
					}

					if ($company_id == '')
						$company_id = $row[csf('company_id')];

					$machine_no = '';
					$machine_id = explode(",", $row[csf('machine_id')]);

					foreach ($machine_id as $val)
					{
						if ($machine_no == '')
							$machine_no = $machine_arr[$val];
						else
							$machine_no .= "," . $machine_arr[$val];
					}

					if ($knit_id_array[$row[csf('program_id')]] != "")
					{
						$all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
						$row_span = count($all_prod_id);
						$z = 0;
						foreach ($all_prod_id as $prod_id)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<?
								if ($z == 0)
								{
									?>
									<td width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
									<td width="60" rowspan="<? echo $row_span; ?>" align="center"><? echo $row[csf('program_id')] . '<br>' . change_date_format($row[csf('program_date')]); ?></td>
									<td width="120" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
									<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
										<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('stitch_length')]; ?></p></td>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
										<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color; ?></p></td>
										<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
										<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $machine_no; ?></p></td>
										<td width="70" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
										<td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]) . " to " . change_date_format($row[csf('end_date')]); ?></td>
										<td width="70" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>&nbsp;</td>
										<?
										$tot_program_qnty += $row[csf('program_qnty')];
										$i++;
									}
									?>
									<td width="110"><p><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</p></td>
									<td width="50"><p><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</p></td>
									<td width="70" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id], 2, '.', ''); ?></td>
									<?
									if ($z == 0) {
										?>
										<td rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
										<?
									}
									?>
								</tr>
								<?
								$tot_yarn_reqsn_qnty += $prod_id_array[$row[csf('program_id')]][$prod_id];
								$z++;
							}
						}
						else
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="25"><? echo $i; ?></td>
								<td width="60" align="center"><? echo $row[csf('program_id')] . '<br>' . change_date_format($row[csf('program_date')]); ?></td>
								<td width="120"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
								<td width="50"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
									<td width="50"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
									<td width="60"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>
									<td width="50"><p><? echo $row[csf('stitch_length')]; ?></p></td>
									<td width="50"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
									<td width="50"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
									<td width="60"><p><? echo $color; ?></p></td>
									<td width="60"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
									<td width="60"><p><? echo $machine_no; ?></p></td>
									<td width="70"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
									<td width="70"><? echo change_date_format($row[csf('start_date')]) . " to " . change_date_format($row[csf('end_date')]); ?></td>
									<td width="70" align="right"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>&nbsp;</td>
									<td width="110"><p>&nbsp;</p></td>
									<td width="50"><p>&nbsp;</p></td>
									<td width="70" align="right">&nbsp;</td>
									<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
								</tr>
								<?
								$tot_program_qnty += $row[csf('program_qnty')];
								$i++;
							}
						}
						?>
						<tfoot>
							<th colspan="14" align="right"><b>Total</b></th>
							<th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
					<br>
					<?
					$sql_strip = "select a.color_number_id, a.stripe_color, a.measurement, a.uom, b.dtls_id, b.no_of_feeder as no_of_feeder from wo_pre_stripe_color a, ppl_planning_feeder_dtls b where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_id and a.color_number_id=b.color_id and a.stripe_color=b.stripe_color_id and b.dtls_id in($program_ids) and b.no_of_feeder>0 and a.status_active=1 and a.is_deleted=0";
					$result_stripe = sql_select($sql_strip);
					if (count($result_stripe) > 0)
					{
						?>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
							<thead>
								<tr>
									<th colspan="7">Stripe Measurement</th>
								</tr>
								<tr>
									<th width="30">SL</th>
									<th width="60">Prog. no</th>
									<th width="140">Color</th>
									<th width="130">Stripe Color</th>
									<th width="70">Measurement</th>
									<th width="50">UOM</th>
									<th>No Of Feeder</th>
								</tr>
							</thead>
							<?
							$i = 1;
							$tot_feeder = 0;
							foreach ($result_stripe as $row)
							{
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								$tot_feeder += $row[csf('no_of_feeder')];
								?>
								<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="50" align="center"><? echo $row[csf('dtls_id')]; ?></td>
									<td width="140"><p><? echo $color_library[$row[csf('color_number_id')]]; ?></p></td>
									<td width="130"><p><? echo $color_library[$row[csf('stripe_color')]]; ?></p></td>
									<td width="70" align="center"><? echo $row[csf('measurement')]; ?></td>
									<td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
									<td align="right" style="padding-right:10px"><? echo $row[csf('no_of_feeder')]; ?>&nbsp;</td>
								</tr>
								<?
								$tot_masurement += $row[csf('measurement')];
								$i++;
							}
							?>
						</tbody>
						<tfoot>
							<th colspan="4">Total</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th style="padding-right:10px"><? echo $tot_feeder; ?>&nbsp;</th>
						</tfoot>
					</table>
					<?
				}
				//echo signature_table(41, $company_id, "1180px",$template_id);
				echo signature_table(100, $company_id, "1180px",$template_id);
				?>
			</div>
			<script type="text/javascript" src="../../js/jquery.js"></script>
			<script type="text/javascript" src="../../js/jquery.qrcode.min.js"></script>
			<script>
				var main_value='<? echo $rqsn_no; ?>'+'***3***8';
				//alert(main_value);
				$('#qrcode').qrcode(main_value);

			</script>
			<?
			exit();
		}

if ($action == "open_qnty_popup")
{
	echo load_html_head_contents("Item List", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	if($is_dyed_yarn==1)
	{
		$sqlyarn_req = "select requisition_no,sum(yarn_qnty) as requisition_qty from ppl_yarn_requisition_entry where prod_id= $prod_id and status_active=1 and is_deleted=0 and is_dyed_yarn=1  group by requisition_no order by requisition_no ";

		$sqlyarn_result = sql_select($sqlyarn_req);
		$requisition_no_string = "";

		if(!empty($sqlyarn_result))
		{
			foreach ($sqlyarn_result as $row) {

				$total_yar_requisition_qty += $row[csf('requisition_qty')];

				if($requisition_no_string!="")
				{
					$requisition_no_string .= ",Requisition No ".$row[csf('requisition_no')].",Qantity=".$row[csf('requisition_qty')];
				}else{
					$requisition_no_string = "Requisition No ".$row[csf('requisition_no')].",Qantity=".$row[csf('requisition_qty')];
				}

			}
		}
	}

	?>
	<script>
		function distribution_value(mehtod) {
			if (mehtod == 1) {
				$('#tbl_order_qnty_list input[name="txt_qnty[]"]').removeAttr('disabled', 'disabled');
				$('#allocated_qnty').attr('disabled', 'disabled');
			}
			else {
				$('#tbl_order_qnty_list input[name="txt_qnty[]"]').attr('disabled', 'disabled');
				$('#allocated_qnty').removeAttr('disabled', 'disabled');
			}
		}

		function set_sum_value(des_fil_id, field_id, table_id) {
			var rowCount = $('#tbl_order_qnty_list tr').length - 2;
			var ddd = {dec_type: 6, comma: 0, currency: 1};
			math_operation(des_fil_id, field_id, '+', rowCount, ddd);
		}

		function js_set_value_qnty() {

			var rowCount = $('#tbl_order_qnty_list tr').length - 2;
			var qnty_breck_down = "";
			for (var i = 1; i <= rowCount; i++) {
				if (qnty_breck_down == "") {
					qnty_breck_down = $('#txt_qnty_' + i).val() + "_" + $('#txt_order_id_' + i).val() + "_" + $('#txt_job_no_' + i).val();
				}
				else {
					qnty_breck_down += "," + $('#txt_qnty_' + i).val() + "_" + $('#txt_order_id_' + i).val() + "_" + $('#txt_job_no_' + i).val();
				}
			}

			document.getElementById('qnty_breck_down').value = qnty_breck_down;
			var allocated_qnty = document.getElementById('allocated_qnty').value;
			var hide_allocated_qnty = document.getElementById('hide_allocated_qnty').value;
			var available_qnty = document.getElementById('available_qnty').value;
			var is_dyed_yarn = '<? echo $is_dyed_yarn; ?>';
			var old_alocated_qty = '<? echo $txt_old_qnty; ?>';


			if(old_alocated_qty>0 && old_alocated_qty!="")
			{
				var available_qnty_curr =((available_qnty*1 +old_alocated_qty*1));
			}else{
				var available_qnty_curr = available_qnty*1;
			}

			if (allocated_qnty * 1 > available_qnty_curr * 1)
			{
				alert("Allocation quantity is not available.\nAvailable quantity = "+available_qnty_curr);

				return;
			}else{
				parent.emailwindow.hide();
			}

		}

		function calculate_poportion(value) {
			var is_fabric_level = $('is_fabric_level').val();
			if(is_fabric_level == 1){
				var tot_po_qnty = (document.getElementById('tot_fab_booking_qnty').value) * 1;
			}else{
				var tot_po_qnty = (document.getElementById('tot_po_qnty').value) * 1;
			}
			var rowCount = $('#tbl_order_qnty_list tr').length - 2;
			var len  = totalProp = 0;
			for (var i = 1; i <= rowCount; i++) {
				len = len + 1;
				if(is_fabric_level == 1){
					var txt_order_qnty = ($('#txt_fab_booking_qnty_' + i).val()) * 1;
				}else{
					var txt_order_qnty = ($('#txt_order_qnty_' + i).val()) * 1;
				}

				var proportionate_qnty = number_format_common((((value / tot_po_qnty) * txt_order_qnty)), 2, 0, 1);
				totalProp += (proportionate_qnty * 1);
				if (rowCount == len) {
					var balance = value - totalProp;
					proportionate_qnty = (proportionate_qnty*1) + (balance*1);
				}
				$('#txt_qnty_' + i).val(number_format_common(proportionate_qnty, 2, 0, 1));
			}
		}
	</script>
</head>
<body>
	<?
	$prev_datas = explode(",", $qnty_breck_down);
	?>
	<div align="center">
		<strong>Distribution Method:</strong>
		<input type="radio" name="distribution_type" id="distribution_type_0" value="0" onClick="distribution_value(this.value)" checked/>
		<label for="distribution_type_0">Proportionately</label>
		<input type="radio" name="distribution_type" id="distribution_type_1" value="1" onClick="distribution_value(this.value)"/>
		<label for="distribution_type_1">Manually</label>
		<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
			<input type="hidden" id="is_fabric_level" value="<? echo ($txt_fabric_po != "")?1:0; ?>"/>
			<table id="tbl_order_qnty_list" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>
						<th width="150" colspan="7">
							Available Qnty:<input type="text" name="available_qnty" id="available_qnty" style="width:60px;" value="<? echo $available_qnty; ?>" class="text_boxes_numeric" disabled/>
							Allocated Qnty:<input type="text" name="allocated_qnty" id="allocated_qnty" style="width:60px;" class="text_boxes_numeric" value="<? echo ($txt_old_qnty != "")?$txt_old_qnty:$txt_qnty; ?>" onChange="calculate_poportion(this.value)"/>
							<input type="hidden" name="hide_allocated_qnty" id="hide_allocated_qnty" style="width:60px;" class="text_boxes" value="<? echo ($txt_old_qnty != "")?($txt_qnty + ($txt_old_qnty-$txt_qnty)):''; ?>"/>
							<input type="hidden" name="qnty_breck_down" id="qnty_breck_down" style="width:60px;" class="text_boxes" value="<? echo $qnty_breck_down; ?>"/>
							Booking Qnty:<input type="text" name="booking_qnty" id="booking_qnty" style="width:60px;" class="text_boxes_numeric" value="<? echo ($txt_fab_booking_qnty != "") ? $txt_fab_booking_qnty : $txt_booking_qnty; ?>" readonly/>
						</th>
					</tr>
					<tr>
						<th>Job No</th>
						<th width="150">Order No</th>
						<th width="100">Internal Ref</th>
						<th width="100">File No</th>
						<th width="100">Order Qnty</th>
						<? if($txt_fabric_po != ""){ ?>
							<th width="100">Booking Qnty</th>
						<? } ?>
						<th width="150" class="must_entry_caption">Qnty</th>
					</tr>
				</thead>
				<tbody>
					<?
					$sl = 1;
					$tot_po_qnty = 0;
					$qnty_array = array();
					foreach ($prev_datas as $prev_data) {
						$po_wise_data = explode("_", $prev_data);
						$qnty_array[$po_wise_data[1]] = $po_wise_data[0];
					}

					$booking_arr = array();
					$booking_no_arr = array();

					if($txt_fabric_po != ""){
						$po_cond = " and b.id in($txt_fabric_po)";
						$fabric_data = explode("_",$txt_selectted_fabric);
						$sql = "select a.booking_no_prefix_num, b.grouping, b.file_no, $year_field a.booking_no, a.booking_date, a.booking_type, a.is_short,sum(c.grey_fab_qnty) grey_fab_qnty, a.company_id, a.buyer_id, c.job_no, c.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id,b.po_number,b.plan_cut from wo_booking_mst a,wo_booking_dtls c,wo_po_break_down b,wo_pre_cost_fabric_cost_dtls d where c.po_break_down_id=b.id and a.booking_no=c.booking_no and c.po_break_down_id=b.id and c.pre_cost_fabric_cost_dtls_id=d.id  and a.booking_no='$txt_booking_no' $po_cond and d.item_number_id=$fabric_data[0] and d.gsm_weight=$fabric_data[1] and d.width_dia_type=$fabric_data[2] and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no_prefix_num,a.insert_date,a.booking_no, a.booking_date, a.company_id, a.buyer_id, c.job_no, a.booking_type, a.is_short, c.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id, b.grouping, b.file_no,b.po_number,b.plan_cut order by a.booking_date,a.booking_no_prefix_num desc";
					}else{
						$sql = "select a.booking_no_prefix_num, b.grouping, b.file_no, $year_field a.booking_no, a.booking_date, a.booking_type, a.is_short, a.company_id, a.buyer_id, c.job_no, c.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id,b.po_number,b.plan_cut from wo_booking_mst a,wo_booking_dtls c,wo_po_break_down b where c.po_break_down_id=b.id and a.booking_no=c.booking_no and c.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and a.company_id=$txt_company_id and a.booking_type in(1,4) and b.id in($txt_order_id)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no_prefix_num,a.insert_date,a.booking_no, a.booking_date, a.company_id, a.buyer_id, c.job_no, a.booking_type, a.is_short, c.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id, b.grouping, b.file_no,b.po_number,b.plan_cut order by a.booking_date,a.booking_no_prefix_num desc";
					}

					$result = sql_select($sql);
					foreach ($result as $order_data) {
						$tot_po_qnty += $order_data[csf('plan_cut')];
						$tot_booking_qnty += $order_data[csf('grey_fab_qnty')];
						?>
						<tr>
							<td>
								<input type="text" class="text_boxes" name="txt_job_no[]" id="txt_job_no_<? echo $sl; ?>" value="<? echo $order_data[csf('job_no')]; ?>" disabled/>
							</td>
							<td width="150">
								<input type="text" class="text_boxes" name="txt_order_no[]" id="txt_order_no_<? echo $sl; ?>" style="width:150px "
								value="<? echo $order_data[csf('po_number')]; ?>" disabled/>
								<input type="hidden" name="txt_order_id[]" id="txt_order_id_<? echo $sl; ?>" style="width:160px " value="<? echo $order_data[csf('po_break_down_id')]; ?>"
								disabled/>
							</td>
							<td width="90" align="right">
								<input type="text" class="text_boxes" name="txt_ref[]" id="txt_ref_<? echo $sl; ?>" style="width:90px " value="<? echo $order_data[csf('grouping')]; ?>" disabled/>
							</td>
							<td width="90" align="right">
								<input type="text" class="text_boxes" name="txt_file[]" id="txt_file_<? echo $sl; ?>" style="width:90px " value="<? echo $order_data[csf('file_no')]; ?>" disabled/>
							</td>
							<td width="100">
								<input type="text" name="txt_order_qnty[]" id="txt_order_qnty_<? echo $sl; ?>" style="width:100px " class="text_boxes_numeric" value="<? echo $order_data[csf('plan_cut')]; ?>" disabled/>
							</td>
							<? if($txt_fabric_po != ""){ ?>
								<td width="100">
									<input type="text" name="txt_fab_booking_qnty[]" id="txt_fab_booking_qnty_<? echo $sl; ?>" style="width:100px " class="text_boxes_numeric" value="<? echo $order_data[csf('grey_fab_qnty')]; ?>" disabled/>
								</td>
							<? } ?>
							<td width="150">
								<input type="text" name="txt_qnty[]" id="txt_qnty_<? echo $sl; ?>" style="width:150px "
								value="<? echo $qnty_array[$order_data[csf('po_break_down_id')]]; ?>"
								class="text_boxes_numeric"
								onChange="set_sum_value('allocated_qnty','txt_qnty_','tbl_order_qnty_list')"
								disabled/>
							</td>
						</tr>
						<?
						$sl++;
					}
					?>
				</tbody>

			</table>
			<table width="98%" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
				<tr>
					<td align="center" width="100%" class="button_container">
						<input type="button" class="formbutton" value="Close" onClick="js_set_value_qnty()"/>
						<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" value="<? echo $tot_po_qnty; ?>"/>
						<input type="hidden" name="tot_fab_booking_qnty" id="tot_fab_booking_qnty" value="<? echo ($txt_fabric_po != "")?$tot_booking_qnty:''; ?>"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "distribution_popup")
{
	echo load_html_head_contents("Distribution Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function validateWithProgQnty()
		{
			var hidden_prog_qnty = $("#hidden_prog_qnty").val();
			var hidden_pre_dist_qnty = $("#hidden_pre_dist_qnty").val()*1;
			var hidden_exis_dist_qnty = $("#hidden_exis_dist_qnty").val()*1;
			var dist_qnty=0;
			var i=1;

			$("#distribution_tbl tbody tr").each(function(){
				var qnty_field = $(this).find("#txtDisQuantity_"+i).val();
				var pre_dist_qnty = $(this).find("#txtDisQuantity_"+i).attr("data-dist-qnty");
				dist_qnty = dist_qnty + qnty_field*1;
				if((pre_dist_qnty-(pre_dist_qnty-qnty_field)) > hidden_prog_qnty){
					$(this).find("#txtDisQuantity_"+i).val("");
				}
				i++;
			});

			if((dist_qnty+(hidden_pre_dist_qnty-hidden_exis_dist_qnty)) > hidden_prog_qnty){
				alert("Distribution quantity can not be greater than Program quantity");
				return;
			}
		}

		function fnc_close()
		{
			var dist_qnty=po_qnty=0;
			var po_id = hidden_dist_qnty_breakdown="";
			var i=1;

			$("#distribution_tbl tbody tr").each(function(){
				var qnty_field = $(this).find("#txtDisQuantity_"+i).val();
				dist_qnty = dist_qnty + qnty_field*1;
				po_qnty = qnty_field*1;
				po_id = $(this).find("#txtDisQuantity_"+i).attr("data-po-id");
				hidden_dist_qnty_breakdown += "<? echo $requisition_no; ?>" + "_" + po_id + "_" + "<? echo $prod_id; ?>" + "_" + po_qnty + ",";
				i++;
			});

			if(dist_qnty==0 || dist_qnty<0)
			{
				alert("Distribution quantity can not be zero");
				return;
			}

			var yarn_requisition_qty = '<? echo $yarn_req_qnty; ?>';
			if(dist_qnty>yarn_requisition_qty)
			{
				$('#txtDisQuantity_1').val('');
				alert("Distribution quantity can not be greater than requisition quantity");
				return;
			}

			$("#hidden_dist_qnty").val(dist_qnty);
			$("#hidden_dist_qnty_breakdown").val(hidden_dist_qnty_breakdown);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:320px;">
		<form name="searchfrm" id="searchfrm">
			<fieldset style="width:310px;">
				<?
				$existing_dist_arr = array();
				if($requisition_no != "")
				{
					$distribution_qnty = return_field_value("sum(total_distribution_qnty) distribution_qnty","ppl_yarn_requisition_entry", "knit_id = ".$knit_dtlsId." and requisition_no = ".$requisition_no." and status_active = 1 and is_deleted = 0","distribution_qnty");
					$distribution_qnty = ($distribution_qnty=="") ? 0 : $distribution_qnty;

					$existing_dist = return_field_value("sum(distribution_qnty) as exis_distribution_qnty","ppl_yarn_req_distribution","requisition_no = ".$requisition_no." and prod_id = ".$prod_id." and status_active=1 and is_deleted=0",'exis_distribution_qnty');
				}
				else
				{
					$distribution_qnty = 0;
					$existing_dist = 0;
				}
				$i = 1;
				?>
				<input type="hidden" name="hidden_prog_qnty" id="hidden_prog_qnty" class="text_boxes" value="<?php echo $program_qnty; ?>">
				<input type="hidden" name="hidden_dist_qnty" id="hidden_dist_qnty" class="text_boxes" value="">
				<input type="hidden" name="hidden_dist_qnty_breakdown" id="hidden_dist_qnty_breakdown" class="text_boxes" value="<? echo $hdn_distribution_qnty_breakdown;?>">
				<input type="hidden" name="hidden_pre_dist_qnty" id="hidden_pre_dist_qnty" class="text_boxes" value="<? echo $distribution_qnty;?>">
				<input type="hidden" name="hidden_exis_dist_qnty" id="hidden_exis_dist_qnty" class="text_boxes" value="<? echo $existing_dist;?>">

				<!-- auto distribution start -->
				<div style="width:320px; margin-top:25px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300">
						<thead>
							<th>Total Distribution Qnty</th>
							<th>Distribution Method</th>
						</thead>
						<tr class="general">
							<td><input type="text" name="txt_prop_qnty" id="txt_prop_qnty" class="text_boxes_numeric" value="" readonly style="width:120px">
							</td>
							<td>
								<?
								$distribiution_method=array(2=>"Manually");
								echo create_drop_down( "cbo_distribiution_method", 140, $distribiution_method,"",0, "",2, "",0 );
								?>
							</td>
						</tr>
					</table>
				</div>
				<table width="300" border="1" rules="all" class="rpt_table" id="distribution_tbl">
					<thead>
						<th width="40">Sl No</th>
						<th width="120">Program Qty</th>
						<th width="120">Distributed Qty</th>
					</thead>
					<tbody>
                        <tr>
                            <td align="center"><?php echo $i; ?></td>
                            <td align="center"><?php echo $program_qnty; ?>
                            <input type="hidden" name="textProgQty[]" id="textProgQty" class="text_boxes" value="<? echo $program_qnty; ?>">
                        </td>
                        <td>
                            <input type="text" class="text_boxes" id="txtDisQuantity_<?php echo $i; ?>" name="txtDisQuantity[]" onKeyUp="validateWithProgQnty()" data-po-id="0" data-dist-qnty="<? echo $txt_distribution_qnty; ?>" value="<? echo $txt_distribution_qnty;?>" style="width:140px;text-align: right;" />
                        </td>
                    </tr>
                </tbody>
                <tfoot>
					<th>Total<input type="hidden" name="tot_prog_qnty" id="tot_prog_qnty" class="text_boxes" value="<? echo $program_qnty; ?>"></th>
					<th align="center"><? echo $program_qnty;?></th>
					<th id="total_distributed_qty"><? echo $totaldist_qnty; ?></th>
				</tfoot>
		</table>
		<table width="350" id="table_id">
			<tr>
				<td align="center">
					<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px"/>
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

if ($action == "requisition_info_details")
{
	$supllier_short_arr = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');
	?>
	<table width="960" border="1" rules="all" class="rpt_table">
		<thead>
			<th width="70">Lot No</th>
			<th width="60">Count</th>
			<th width="70">Supplier</th>
			<th width="80">Type</th>
			<th width="150">Composition</th>
			<th width="90">Color</th>
			<th width="60">No of Cone</th>
			<th width="70">Requisition Date</th>
			<th width="70">Distribution Qnty</th>
			<th width="120">Yarn Reqs. Qnty</th>
			<th>Returnable Qnty</th>
		</thead>
	</table>
	<div style="width:960px; overflow-y:auto; max-height:300px;" id="scroll_body" align="left">
		<table class="rpt_table" rules="all" border="1" width="960" id="tbl_list_search">
			<?
			$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
			$query = sql_select("select id,knit_id,requisition_no,prod_id,no_of_cone,requisition_date,total_distribution_qnty,yarn_qnty from ppl_yarn_requisition_entry where knit_id=$data and status_active = '1' and is_deleted = '0'");
			$i = 1;
			$tot_yarn_qnty = 0;
			foreach ($query as $selectResult) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				$dataArray = sql_select("select supplier_id, lot, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color from product_details_master where id=" . $selectResult[csf('prod_id')] . "");

				$compos = '';
				if ($dataArray[0][csf('yarn_comp_percent2nd')] != 0) {
					$compos = $composition[$dataArray[0][csf('yarn_comp_type1st')]] . " " . $dataArray[0][csf('yarn_comp_percent1st')] . "%" . " " . $composition[$dataArray[0][csf('yarn_comp_type2nd')]] . " " . $dataArray[0][csf('yarn_comp_percent2nd')] . "%";
				} else {
					$compos = $composition[$dataArray[0][csf('yarn_comp_type1st')]] . " " . $dataArray[0][csf('yarn_comp_percent1st')] . "%" . " " . $composition[$dataArray[0][csf('yarn_comp_type2nd')]];
				}

				$tot_yarn_qnty += $selectResult[csf('yarn_qnty')];
				$total_distribution_qnty += $selectResult[csf('total_distribution_qnty')];
				$total_returnable_qnty += ($selectResult[csf('yarn_qnty')] - $selectResult[csf('total_distribution_qnty')]);

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="cursor:pointer"
					onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>+'_'+document.getElementById('companyID').value, 'populate_requisition_data', 'yarn_requisition_entry_for_sample_without_order_controller' );">
					<td width="70"><p><? echo $dataArray[0][csf('lot')]; ?></p></td>
					<td width="60" align="center"><p><? echo $count_arr[$dataArray[0][csf('yarn_count_id')]]; ?></p>
					</td>
					<td width="70"><p><? echo $supllier_short_arr[$dataArray[0][csf('supplier_id')]]; ?></p></td>
					<td width="80"><p><? echo $yarn_type[$dataArray[0][csf('yarn_type')]]; ?></p></td>
					<td width="150"><p><? echo $compos; ?></p></td>
					<td width="90"><p><? echo $color_library[$dataArray[0][csf('color')]]; ?></p></td>
					<td align="right" width="60"><? echo number_format($selectResult[csf('no_of_cone')], 0); ?></td>
					<td align="center" width="70"><? echo change_date_format($selectResult[csf('requisition_date')]); ?></td>
					<td align="right" width="70"><? echo number_format($selectResult[csf('total_distribution_qnty')], 2); ?></td>
					<td align="right" width="120"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?></td>
					<td align="right"><? echo number_format($selectResult[csf('yarn_qnty')] - $selectResult[csf('total_distribution_qnty')], 2); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
			<tfoot>
				<th colspan="8">Total</th>
				<th><? echo number_format($total_distribution_qnty, 2); ?></th>
				<th><? echo number_format($tot_yarn_qnty, 2); ?></th>
				<th><? echo number_format($total_returnable_qnty, 2); ?></th>
			</tfoot>
		</table>
	</div>
	<?
	exit();
}

if ($action == "populate_requisition_data")
{
	$data = explode("_",$data);
	$distribute_qnty_variable ="";
	if(trim($data[1])!="")
	{
	$distribute_qnty_variable = return_field_value("distribute_qnty", "variable_settings_production", "company_name='$data[1]' and variable_list=5 and status_active=1 and is_deleted=0", "distribute_qnty");

	$is_auto_allocation_from_requisition = return_field_value("auto_allocate_yarn_from_requis", "variable_settings_production", "company_name='$data[1]' and variable_list=6 and status_active=1 and is_deleted=0", "auto_allocate_yarn_from_requis");
	}

	$sql = "select a.id, a.knit_id, a.requisition_no, a.prod_id, a.no_of_cone, a.requisition_date, a.yarn_qnty,a.total_distribution_qnty,a.distribution_qnty_breakdown,a.is_dyed_yarn,b.allocated_qnty,b.available_qnty,a.allocation_qnty_breakdown,a.dyed_yarn_qnty_from from ppl_yarn_requisition_entry a,product_details_master b where a.prod_id=b.id and a.id=$data[0]";

	$data_array = sql_select($sql);
	foreach ($data_array as $row) {
		$dataArray = sql_select("select lot, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color from product_details_master where id=" . $row[csf('prod_id')]);

		$compos = '';
		if ($dataArray[0][csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$dataArray[0][csf('yarn_comp_type1st')]] . " " . $dataArray[0][csf('yarn_comp_percent1st')] . "%" . " " . $composition[$dataArray[0][csf('yarn_comp_type2nd')]] . " " . $dataArray[0][csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$dataArray[0][csf('yarn_comp_type1st')]] . " " . $dataArray[0][csf('yarn_comp_percent1st')] . "%" . " " . $composition[$dataArray[0][csf('yarn_comp_type2nd')]];
		}



		echo "document.getElementById('is_auto_allocation_from_requisition').value = '" . $is_auto_allocation_from_requisition . "';\n";
		echo "document.getElementById('txt_requisition_no').value 			= '" . $row[csf("requisition_no")] . "';\n";
		echo "document.getElementById('txt_lot').value 						= '" . $dataArray[0][csf("lot")] . "';\n";
		echo "document.getElementById('cbo_yarn_count').value 				= '" . $dataArray[0][csf("yarn_count_id")] . "';\n";
		echo "document.getElementById('cbo_yarn_type').value 				= '" . $dataArray[0][csf("yarn_type")] . "';\n";

		echo "document.getElementById('txt_composition').value 				= '" . $compos . "';\n";

		echo "document.getElementById('txt_color').value 					= '" . $color_library[$dataArray[0][csf("color")]] . "';\n";
		echo "document.getElementById('txt_no_of_cone').value 				= '" . $row[csf("no_of_cone")] . "';\n";
		echo "document.getElementById('txt_reqs_date').value 				= '" . change_date_format($row[csf("requisition_date")]) . "';\n";
		echo "document.getElementById('txt_yarn_qnty').value 				= '" . $row[csf("yarn_qnty")] . "';\n";

		echo "document.getElementById('dyed_yarn_qnty_from').value 			= '" . $row[csf("dyed_yarn_qnty_from")] . "';\n";



		if($is_auto_allocation_from_requisition == 1 && $row[csf("is_dyed_yarn")] == 1)
		{
			if( $row[csf("dyed_yarn_qnty_from")] == 1 ){
				echo "document.getElementById('available_qnty').value 				= '" . $row[csf("allocated_qnty")] . "';\n";
			}else{
				echo "document.getElementById('available_qnty').value 				= '" . $row[csf("available_qnty")] . "';\n";
			}
		}
		else
		{
			echo "document.getElementById('available_qnty').value 				= '" . $row[csf("available_qnty")] . "';\n";
		}

		echo "document.getElementById('txt_old_qnty').value 				= '" . $row[csf("yarn_qnty")] . "';\n";
		echo "document.getElementById('prod_id').value 						= '" . $row[csf("prod_id")] . "';\n";
		echo "document.getElementById('original_prod_id').value 			= '" . $row[csf("prod_id")] . "';\n";
		echo "document.getElementById('update_dtls_id').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('is_dyed_yarn').value 				= '" . $row[csf("is_dyed_yarn")] . "';\n";
		echo "document.getElementById('qnty_breck_down').value 				= '" . $row[csf("allocation_qnty_breakdown")] . "';\n";
		echo "document.getElementById('pre_qnty_breck_down').value 			= '" . $row[csf("allocation_qnty_breakdown")] . "';\n";
		echo "document.getElementById('hidden_yarn_req_qnty').value 		= '';\n";

		if($distribute_qnty_variable == 1){
			echo "document.getElementById('txt_distribution_qnty').value 		= '" . $row[csf("total_distribution_qnty")] . "';\n";
			echo "document.getElementById('hdn_distribution_qnty').value 		= '" . $row[csf("total_distribution_qnty")] . "';\n";
			echo "document.getElementById('hdn_distribution_qnty_breakdown').value 		= '" . $row[csf("distribution_qnty_breakdown")] . "';\n";
		}
		/*echo "document.getElementById('cbocomposition1').value 			= '".$dataArray[0][csf("yarn_comp_type1st")]."';\n";
		echo "document.getElementById('txt_percentage1').value 				= '".$dataArray[0][csf("yarn_comp_percent1st")]."';\n";
		echo "document.getElementById('cbocomposition2').value 				= '".$dataArray[0][csf("yarn_comp_type2nd")]."';\n";
		echo "document.getElementById('txt_percentage2').value 				= '".$dataArray[0][csf("yarn_comp_percent2nd")]."';\n";*/
		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_yarn_req_entry',1);\n";
		exit();
	}
}

function count_type_rate_validate($job,$prodId)
{
	$job_no=str_replace("'","",$job);
	$countP='';
	$typeP='';
	$sqlP = sql_select("select id,yarn_count_id,yarn_type from product_details_master where id=$prodId");
	foreach($sqlP as $rowP){
		$countP=$rowP[csf('yarn_count_id')];
		$typeP=$rowP[csf('yarn_type')];
	}
	$ratep=number_format(return_itemWise_usdRate($prodId),4,".","");
	$sqlY = sql_select("select count_id,type_id,rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and count_id='$countP' and type_id='$typeP' and rate >= $ratep and status_active=1 and is_deleted=0");
	if(count($sqlY)==0){
		return false;
	}else{
		return true;
	}
}
?>
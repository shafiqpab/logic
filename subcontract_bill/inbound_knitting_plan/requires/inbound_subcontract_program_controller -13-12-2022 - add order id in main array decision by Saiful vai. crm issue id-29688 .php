<?php
include('../../../includes/common.php');
session_start();
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission = $_SESSION['page_permission'];

if( $_SESSION['logic_erp']['user_id'] == "" )
{
	header("location:login.php");
	die;
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
		echo create_drop_down("cbo_knitting_party", 177, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Party--", $data[1], "", "");
	}
	else if ($data[0] == 3)
	{
		if ($data[2] == 1)
			$selected_id = $data[1];
		else
			$selected_id = 0;
		echo create_drop_down("cbo_knitting_party", 177, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 and a.is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Party--", $selected_id, "");
	}
	else
	{
		echo create_drop_down("cbo_knitting_party", 177, $blank_array, "", 1, "--Select Knit Party--", 0, "");
	}
	exit();
}

/*
|--------------------------------------------------------------------------
| load_drop_down_party
|--------------------------------------------------------------------------
|
*/
if ($action=="load_drop_down_party")
{
	echo create_drop_down( "cbo_party_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "" );
	exit();
}

/*
|--------------------------------------------------------------------------
| actn_order_no
|--------------------------------------------------------------------------
|
*/
if ($action == "actn_order_no")
{
	echo load_html_head_contents("Style Reference / Job No. Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
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

		function js_set_value(str)
		{
			toggle(document.getElementById('search' + str), '#FFFFCC');
			if (jQuery.inArray($('#txtOrderId' + str).val(), selected_id) == -1)
			{
				selected_id.push($('#txtOrderId' + str).val());
				selected_name.push($('#txtOrderNo' + str).val());

			}
			else
			{
				for (var i = 0; i < selected_id.length; i++)
				{
					if (selected_id[i] == $('#txtOrderId' + str).val())
					{
						break;
					}
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

			$('#hdnOrderId').val(id);
			$('#hdnOrderNo').val(name);
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:800px;">
				<table width="480" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
				class="rpt_table" id="tbl_list">
				<thead>
					<th>Party Name</th>
					<th>Search By</th>
					<th id="search_by_td_up" width="130">Please Enter Order No</th>
					<th>
                        <input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;">
                        <input type="hidden" name="hdnOrderId" id="hdnOrderId" value=""/>
                        <input type="hidden" name="hdnOrderNo" id="hdnOrderNo" value=""/>
                   	</th>
				</thead>
				<tbody>
					<tr>
						<td id="party_td">
							<?php
							echo create_drop_down("cbo_partyId", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=".$companyId." and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $partyId, "");
							?>
						</td>
						<td align="center">
							<?php
							$search_by_arr = array(1 => "Order No", 2 => "Job No");
							$dd = "change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down("cbo_search_by", 120, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show"
							onClick="show_list_view ('<?php echo $companyId.'**'.$partyId; ?>**'+document.getElementById('cbo_partyId').value + '**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'list_view_order_no', 'search_div', 'inbound_subcontract_program_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
							style="width:90px;"/>
						</td>
					</tr>
				</tbody>
			</table>
			<div style="margin-top:05px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?php
exit();
}

/*
|--------------------------------------------------------------------------
| create_job_search_list_view
|--------------------------------------------------------------------------
|
*/
if ($action == "list_view_order_no")
{
	$data = explode('**', $data);
	$companyId = $data[0];
	$partyId = $data[1];
	$popupPartyId = $data[2];
	$searchBy = $data[3];
	$searchString = trim($data[4]);

	$partyCondition = '';	
	if ($popupPartyId != 0)
	{
		$partyCondition = " AND m.party_id = ".$popupPartyId."";
	}
	else
	{
		if($partyId != 0)
		{
			$partyCondition = " AND m.party_id = ".$partyId."";
		}
	}
	
	$searchByCondition = '';
	if ($searchString != "")
	{
		if ($searchBy == 1)
		{
			$searchByCondition = " AND d.order_no LIKE '%".$searchString."'";
			//$searchByCondition = " AND d.order_no = '".$searchString."'";
		}
		else
		{
			$searchByCondition = " AND d.job_no_mst LIKE '%".$searchString."'";
			//$searchByCondition = " AND m.subcon_job = '".$searchString."'";
		}
	}

	if ($db_type == 0)
	{
		$yearField = "YEAR(m.insert_date) AS year";
	}
	elseif ($db_type == 2)
	{
		$yearField = "TO_CHAR(m.insert_date,'YYYY') AS year";
	}
	else
	{
		$yearField = "";
	}
	
	$sql = "
			SELECT 
				m.id, m.subcon_job, m.party_id, m.company_id, m.within_group, ".$yearField.",
				d.id AS order_id, d.cust_style_ref, d.order_no
			FROM 
				subcon_ord_mst m
				INNER JOIN subcon_ord_dtls d ON m.id = d.mst_id
			WHERE
				m.subcon_job = d.job_no_mst
				--AND m.main_process_id = 2
				AND m.status_active=1 
				AND d.status_active in (1,2)
				AND m.entry_form = 238
				AND m.company_id = ".$companyId."
				".$partyCondition." 
				".$searchByCondition."
			";
	//main_process_id, process_id
	//$production_process = array(1 => "Cutting", 2 => "Knitting", 3 => "Dyeing", 4 => "Finishing", 5 => "Sewing", 6 => "Fabric Printing", 7 => "Washing", 8 => "Gmts Printing", 9 => "Embroidery", 10 => "Iron", 11 => "Gmts Finishing", 12 => "Gmts Dyeing", 13 => "Poly", 14 => "Re Conning", 15 => "Common", 16=> "Knit Finish Fabric",17=> "Dyeing process",18=> "Trims");

	//echo $sql;
	$resultSet = sql_select($sql);
	if(empty($resultSet))
	{
		echo get_empty_data_msg();
		die;
	}
	
	//$sql="select a.id,a.party_id as buyer_name,c.item_id as gmts_item_id,b.cust_style_ref as style_ref_no,b.order_no as po_number,a.job_no_prefix_num as job_prefix,$year_field from  subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_id  $sub_buyer_name_cond $year_cond and a.is_deleted = 0 group by a.id,a.party_id,b.cust_style_ref,b.order_no ,a.job_no_prefix_num,a.insert_date,c.item_id";

	
	$firstTblWidth=720;
	$secondTblWidth=700;
	$company_details = get_company_array();
	$buyer_details = get_buyer_array();
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?php echo $firstTblWidth; ?>" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="80">Job No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="100">Party Name</th>
			<th width="100">Company Name(Short)</th>
			<th width="120">Order No</th>
			<th>Cust Style Ref.</th>
		</thead>
	</table>
	<div style="width:<?php echo $firstTblWidth; ?>px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?php echo $secondTblWidth; ?>" class="rpt_table" id="tbl_list_search">
		<?php
		$i = 1;
		foreach ($resultSet as $row)
		{
			if ($i % 2 == 0)
			{
				$bgcolor = "#E9F3FF";
			}
			else
			{
				$bgcolor = "#FFFFFF";
			}
			?>
			<tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<?php echo $i; ?>);" id="search<?php echo $i; ?>">
				<td width="40" align="center"><?php echo $i; ?>
				<input type="hidden" name="txtOrderId[]" id="txtOrderId<?php echo $i ?>" value="<?php echo $row[csf('order_id')]; ?>"/>
				<input type="hidden" name="txtOrderNo[]" id="txtOrderNo<?php echo $i ?>" value="<?php echo $row[csf('order_no')]; ?>"/>
                </td>
                <td width="80"><p>&nbsp;<?php echo $row[csf('subcon_job')]; ?></p></td>
                <td width="60" align="center"><p><?php echo $row[csf('year')]; ?></p></td>
                <td width="80" align="center"><p><?php echo ($row[csf('within_group')] != '' ? $yes_no[$row[csf('within_group')]]:''); ?>&nbsp;</p></td>
                <td width="100"><p><?php echo $buyer_details[$row[csf('party_id')]]; ?>&nbsp;</p></td>
                <td width="100" align="center"><p><?php echo $company_details[$row[csf('company_id')]]['shortname']; ?>&nbsp;</p></td>
                <td width="120" align="center"><p><?php echo $row[csf('order_no')]; ?></p></td>
                <td><p><?php echo $row[csf('cust_style_ref')]; ?></p></td>
            </tr>
            <?php
            $i++;
        }
        ?>
        </table>
    </div>
    <table width="<?php echo $secondTblWidth; ?>" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
    </table>
    <?php
    exit();
}

/*
|--------------------------------------------------------------------------
| actn_show_details
|--------------------------------------------------------------------------
|
*/
if ($action == "actn_show_details")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name = str_replace("'", "", $cbo_company_name);
	$cbo_party_name = str_replace("'", "", $cbo_party_name);
	$txt_order_no = str_replace("'", "", $txt_order_no);
	$hdn_order_id = str_replace("'", "", $hdn_order_id);
	$txt_date_from = str_replace("'", "", $txt_date_from);
	$txt_date_to = str_replace("'", "", $txt_date_to);
	$cbo_planning_status = str_replace("'", "", $cbo_planning_status);
	$txt_barcode_no = str_replace("'", "", $txt_barcode_no);

	$partyCondition = '';
	if ($cbo_party_name != 0)
	{
		$partyCondition = " AND sm.party_id = ".$cbo_party_name."";
	}
	
	$orderNoCondition = '';
	if ($txt_order_no != '')
	{
		$orderNoCondition = " AND sd.order_no = '".$txt_order_no."'";
	}
	
	$receiveDateCondition = '';
	if ($txt_date_from != '' && $txt_date_to != '')
	{
		if ($db_type == 0)
		{
			//$receiveDateCondition = " AND sm.receive_date BETWEEN '".$txt_date_from."' AND '".$txt_date_to."'";
			$receiveDateCondition = " AND sd.order_rcv_date BETWEEN '".$txt_date_from."' AND '".$txt_date_to."'";
		}
		else
		{
			//$receiveDateCondition = " AND sm.receive_date BETWEEN '".date('d-M-Y',strtotime($txt_date_from))."' AND '".date('d-M-Y',strtotime($txt_date_to))."'";
			$receiveDateCondition = " AND sd.order_rcv_date BETWEEN '".date('d-M-Y',strtotime($txt_date_from))."' AND '".date('d-M-Y',strtotime($txt_date_to))."'";
		}
	}
	//$planningStatusCondition = " AND sd.order_no = '".$cbo_planning_status."'";

	/*
	|--------------------------------------------------------------------------
	| main query
	|--------------------------------------------------------------------------
	|
	*/
	$sql = "
		SELECT 
			sm.id AS smid, sm.party_id, sm.subcon_job,
            sd.id AS sdid, sd.order_no, sd.order_quantity, sd.order_rcv_date,
			sb.id AS sbid, sb.mst_id, sb.order_id, sb.item_id, sb.color_id, sb.gsm, sb.dia_width_type, sb.finish_dia, sb.qnty,sb.grey_dia 
		FROM 
			subcon_ord_mst sm
			INNER JOIN subcon_ord_dtls sd ON sm.id = sd.mst_id
			INNER JOIN subcon_ord_breakdown sb ON sm.id = sb.mst_id
		WHERE
			sm.subcon_job = sd.job_no_mst
			AND sd.id = sb.order_id
			AND sm.entry_form = 238
			AND sm.status_active = 1 
			AND sm.is_deleted = 0
			AND sd.status_active = 1 
			AND sm.company_id = ".$company_name."
			".$partyCondition."
			".$orderNoCondition."
			".$receiveDateCondition."
		GROUP BY
			sm.id, sm.party_id, sm.subcon_job,
            sd.id, sd.order_no, sd.order_quantity, sd.order_rcv_date,
			sb.id, sb.mst_id, sb.order_id, sb.item_id, sb.color_id, sb.gsm, sb.dia_width_type, sb.finish_dia, sb.qnty,sb.grey_dia
		ORDER BY sb.finish_dia ASC			
		";
		//--AND sm.main_process_id = 2
	
	//echo $sql;
	$resultSet = sql_select($sql);
	if(empty($resultSet))
	{
		echo get_empty_data_msg();
		die;
	}
	
	$company_details = get_company_array();
	$buyer_details = get_buyer_array();
	//$fabric_details = return_library_array( "SELECT id, const_comp FROM lib_subcon_charge WHERE comapny_id = ".$company_name."", "id", "const_comp");
	$fabric_details = return_library_array( "SELECT id, const_comp FROM lib_subcon_charge", "id", "const_comp");
	
	/*
	|--------------------------------------------------------------------------
	| for plan information
	|--------------------------------------------------------------------------
	|
	*/
	$mstIdArr = array();
	$poIdsArr = array();
	foreach ($resultSet as $row)
	{
		$mstIdArr[$row[csf('smid')]] = $row[csf('smid')];
		$poIdsArr[$row[csf('order_id')]] = $row[csf('order_id')];
	}
	
	if ($db_type == 0)
	{
		$queryProgNo = " GROUP_CONCAT(c.dtls_id) AS prog_no,";
	}
	elseif ($db_type == 2)
	{
		$queryProgNo = " LISTAGG(c.dtls_id, ',') WITHIN GROUP (ORDER BY c.dtls_id) AS prog_no,";
	}
	
	$sqlPlan = "SELECT a.subcon_order_id, c.id, c.mst_id, c.determination_id, c.gsm_weight, c.dia, ".$queryProgNo." SUM(c.program_qnty) AS program_qnty, c.status_active, c.po_id,b.machine_gg FROM subcon_planning_mst a, subcon_planning_dtls b, subcon_planning_plan_dtls c WHERE a.id = b.mst_id AND b.id = c.dtls_id AND a.subcon_order_id IN(".implode(",",$mstIdArr).") AND c.po_id IN(".implode(",",$poIdsArr).")  AND b.status_active = 1 AND b.is_deleted = 0 AND c.is_revised=0 GROUP BY a.subcon_order_id, c.id, c.mst_id, c.yarn_desc, c.body_part_id, c.determination_id, c.gsm_weight, c.dia, c.status_active, c.po_id,b.machine_gg";

	
	$resultPlan = sql_select($sqlPlan);
	$program_data_array = array();	
	$planIdArr = array();
	$progNoArr = array();
	foreach ($resultPlan as $rowPlan)
	{
		$planIdArr[$rowPlan[csf('subcon_order_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('po_id')]]= $rowPlan[csf('mst_id')];

		$progNoArr[$rowPlan[csf('subcon_order_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('po_id')]].=$rowPlan[csf('prog_no')].",";
		$program_data_array[$rowPlan[csf('subcon_order_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]]['program_qnty'] += $rowPlan[csf('program_qnty')];
		$program_data_array[$rowPlan[csf('subcon_order_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]]['machine_gg'] = $rowPlan[csf('machine_gg')];
		
	}
	/* echo "<pre>";
	print_r($progNoArr); die; */
	unset($resultPlan);
	
	/*
	|--------------------------------------------------------------------------
	| data preparing here
	|--------------------------------------------------------------------------
	|
	*/
	$orderQtyArr = array();
	foreach ($resultSet as $row)
	{
		$orderQtyArr[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['orderQty'] += $row[csf('qnty')];
	}
	//echo "<pre>";
	//print_r($orderQtyArr);
	
	$rptData = array();
	foreach ($resultSet as $row)
	{
		//program Qty
		if(empty($program_data_array[$row[csf('smid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['program_qnty']))
		{
			$programQty = 0;
		}
		else
		{
			$programQty = $program_data_array[$row[csf('smid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['program_qnty'];
		}
		$machine_gg = $program_data_array[$row[csf('smid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['machine_gg'];
		
		//$orderQty = $row[csf('order_quantity')];
		//$orderQty = $row[csf('qnty')];
		$orderQty = $orderQtyArr[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['orderQty'];
		$balanceQty = $orderQty - $programQty;

		if(($cbo_planning_status == 2 && $balanceQty <= 0) || ($cbo_planning_status == 1 && $balanceQty > 0))
		{
			$i++;
			$isRequisition = 0;
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['partyId'] = $row[csf('party_id')];
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['partyDtls'] = $buyer_details[$row[csf('party_id')]];
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['orderRcvDate'] = date('d-m-Y', strtotime($row[csf('order_rcv_date')]));
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['orderNo'] = $row[csf('order_no')];
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['fabricId'] = $row[csf('item_id')];
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['fabricDtls'] = $fabric_details[$row[csf('item_id')]];
			
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['machine_gg'] = $machine_gg;
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['grey_dia'] = $row[csf('grey_dia')];
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['gsm'] = $row[csf('gsm')];
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['dia'] = $row[csf('finish_dia')];
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['diaWidthTypeId'] = $row[csf('dia_width_type')];
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['diaWidthTypeDtls'] = $fabric_typee[$row[csf('dia_width_type')]];
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['orderQty'] = $orderQty;
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['programQty'] = $programQty;
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['balanceQty'] = $balanceQty;
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['isRequisition'] = $isRequisition;
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['jobNo'] = $row[csf('subcon_job')];
			
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['colorId'][$row[csf('color_id')]] = $row[csf('color_id')];
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['orderMstId'][$row[csf('smid')]] = $row[csf('smid')];
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['orderDtlsId'][$row[csf('sdid')]] = $row[csf('sdid')];
			$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['orderBrkDownId'][$row[csf('sbid')]] = $row[csf('sbid')];

			//planId
			if(empty($planIdArr[$row[csf('smid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]))
			{
				$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['planId'] = '';
			}
			else
			{
				$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['planId'] = $planIdArr[$row[csf('smid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]][$row[csf('sdid')]];
			}
			
			//porgNo
			if(empty($progNoArr[$row[csf('smid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]))
			{
				$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['progNo'] = '';
			}
			else
			{
				$rptData[$row[csf('sdid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]]['progNo'] = chop($progNoArr[$row[csf('smid')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('finish_dia')]][$row[csf('sdid')]],",");
			}
		}
	}
	unset($resultSet);
	//echo "<pre>";
	//print_r($rptData); die;
	
	if(empty($rptData))
	{
		echo get_empty_data_msg();
		die;
	}
	
	$sl = 0;
	$rptPData = array();
	$subTotal = array();
	$grandTotal = array();
	foreach ($rptData as $ordId=>$ordArr)
	{
		foreach ($ordArr as $febId=>$febArr)
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
						$subTotal[$diaNo]['orderQty'] += $row['orderQty'];
						$subTotal[$diaNo]['programQty'] += $row['programQty'];
						$subTotal[$diaNo]['balanceQty'] += $row['balanceQty'];
						
						//grandTotal
						$grandTotal['orderQty'] += $row['orderQty'];
						$grandTotal['programQty'] += $row['programQty'];
						$grandTotal['balanceQty'] += $row['balanceQty'];
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
	$print_report_format =return_field_value("format_id"," lib_report_template","template_name ='".$company_name."' and module_id=4 and report_id=88 and is_deleted=0 and status_active=1");
	$print_report_format_arr = explode(",",$print_report_format);
	if( $print_report_format_arr[0] != "" )
	{
		if( $print_report_format_arr[0] == 272 )
		{
			$program_info_format_id = 272;

		}
		else if($print_report_format_arr[0] == 273)
		{
			$program_info_format_id = 273;
		}
	}
	else
	{
		$program_info_format_id = 272;
	}
	
	$noOfTd = 14;
	$noOfColspanTd = 11;
	?>
    <form name="palnningEntry_2" id="palnningEntry_2">
        <fieldset style="width:1100px;">
            <legend>Program Details</legend>
            <table width="1070" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
                <thead>
                    <th width="40">SL</th>
                    <th width="50">Plan Id</th>
                    <th width="60">Prog. No</th>
                    <th width="120">Party Name</th>
                    <th width="70">Order Receive Date</th>
                    <th width="60">Order No</th>
                    <th width="180">Fabric Desc.</th>
					<th width="80">M/C Dia X Gauge</th>
                    <th width="50">Gsm</th>
                    <th width="50">Dia</th>
                    <th width="70">Width/Dia Type</th>
                    <th width="70">Order Qty</th>
                    <th width="70">Prog. Qty</th>
                    <th>Balance Qnty
                    	<input type="hidden" name="hdnType" id="hdnType" class="text_boxes" value="<?php echo $type; ?>">
                        <input type="hidden" name="action_type" id="action_type" class="text_boxes" value="<?php echo $type; ?>"/>
                    </th>
                </thead>
                <tbody>
				<?php
				$rowNo = 0;
				$chkDia = array();
				foreach ($rptPData as $diaNo=>$diaArr)
				{
					foreach ($diaArr as $sl=>$row)
					{
						$rowNo++;
						$rowColor = ($sl % 2 == 0 ? "#E9F3FF" : "#FFFFFF");
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
							<td style="word-break: break-word;">
                                <?php
                                $print_program_no1 = "";
                                if ($row['progNo'] != '') {
                                    $program_arr_id = explode(',', $row['progNo']);
                                    foreach ($program_arr_id as $id) {
                                        $print_program_no1 .= "<a href='##' onclick=\"generate_report2('" . $company_name . "','" . $id . "','" . $program_info_format_id . "')\">" . $id . "</a>, ";
                                    }
                                }
                                echo rtrim($print_program_no1, ", ");
                                $print_program_no1 = "";
                                ?>
                            </td>
							<td><?php echo $row['partyDtls']; ?></td>
							<td align="center"><?php echo $row['orderRcvDate']; ?></td>
							<td><?php echo $row['orderNo']; ?></td>
							<td><?php echo $row['fabricDtls']; ?></td>
							<td align="center"><?php echo $row['grey_dia']; ?></td>
							<td align="center"><?php echo $row['gsm']; ?></td>
							<td align="center"><?php echo $row['dia']; ?></td>
							<td><?php echo $row['diaWidthTypeDtls']; ?></td>
							<td align="right"><?php echo number_format($row['orderQty'],2); ?></td>
							<td align="right"><?php echo number_format($row['programQty'],2); ?></td>
							<td align="right">
								<?php echo number_format($row['balanceQty'],2); ?>
								<input type="hidden" name="hdnPartyId[]" id="hdnPartyId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['partyId']; ?>" />
								<input type="hidden" name="hdnOrderNo[]" id="hdnOrderNo_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['orderNo']; ?>" />
								<input type="hidden" name="hdnFabricId[]" id="hdnFabricId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['fabricId']; ?>" />
								<input type="hidden" name="hdnFabricDtls[]" id="hdnFabricDtls_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['fabricDtls']; ?>" />
								<input type="hidden" name="hdnMachineGg[]" id="hdnMachineGg_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['grey_dia']; ?>" />
								<input type="hidden" name="hdnGsm[]" id="hdnGsm_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['gsm']; ?>" />
								<input type="hidden" name="hdnDia[]" id="hdnDia_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['dia']; ?>" />
								<input type="hidden" name="hdnDiaWidthType[]" id="hdnDiaWidthType_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['diaWidthTypeId']; ?>" />
								<input type="hidden" name="hdnOrderQty[]" id="hdnOrderQty_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['orderQty']; ?>" />
								<input type="hidden" name="hdnColorId[]" id="hdnColorId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo implode(",", $row['colorId']); ?>" />
								<input type="hidden" name="hdnIsRequisition[]" id="hdnIsRequisition_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['isRequisition']; ?>" />
								<input type="hidden" name="hdnOrderMstId[]" id="hdnOrderMstId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo implode(",", $row['orderMstId']); ?>" />
								<input type="hidden" name="hdnOrderDtlsId[]" id="hdnOrderDtlsId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo implode(",", $row['orderDtlsId']); ?>" />
								<input type="hidden" name="hdnOrderBrkDownId[]" id="hdnOrderBrkDownId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo implode(",", $row['orderBrkDownId']); ?>" />
								<input type="hidden" name="hdnPlanId[]" id="hdnPlanId_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['planId']; ?>" />
								<input type="hidden" name="hdnJobNo[]" id="hdnJobNo_<?php echo $rowNo; ?>" class="text_boxes" value="<?php echo $row['jobNo']; ?>" />
							</td>
						</tr>
						<?php
						//progNo
					}
					$rowNo++;
					?>
					<tr bgcolor="#CCCCCC" id="tr_<? echo $rowNo; ?>">
						<th colspan="<?php echo $noOfColspanTd; ?>" align="right">Sub Total</th>
						<th align="right"><? echo number_format($subTotal[$diaNo]['orderQty'], 2, '.', ''); ?></th>
						<th align="right"><? echo number_format($subTotal[$diaNo]['programQty'], 2, '.', ''); ?></th>
						<th align="right"><? echo number_format($subTotal[$diaNo]['balanceQty'], 2, '.', ''); ?></th>
					</tr>
					<?php
				}
				?>
				</tbody>
                <tfoot>
                	<tr>
                    	<th colspan="<?php echo $noOfColspanTd; ?>" align="right">Grand Total<input type="hidden" name="company_id" id="company_id" value="<?php echo $company_name; ?>"/></th>
                        <th align="right"><? echo number_format($grandTotal['orderQty'], 2, '.', ''); ?></th>
                        <th align="right"><? echo number_format($grandTotal['programQty'], 2, '.', ''); ?></th>
                        <th align="right"><? echo number_format($grandTotal['balanceQty'], 2, '.', ''); ?></th>
                    </tr>
                </tfoot>
			</table>
        </div>
    	</fieldset>
	</form>
	<?php
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
	echo load_html_head_contents("Program Qnty Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$current_date = date("d-m-Y");
	$plan_id = $planId;
	?>
	<script>
		var permission = '<?php echo $permission; ?>';
		var dataPre = '<?php echo $data; ?>';
		var companyId = '<?php echo $companyId; ?>';
		var partyId = '<?php echo $partyId; ?>';
		var orderNo = '<?php echo $orderNo; ?>';
		var orderId = '<?php echo $orderId; ?>';
		var fabricId = '<?php echo $fabricId; ?>';
		var fabricDtls = '<?php echo $fabricDtls; ?>';
		var gsm = '<?php echo $gsm; ?>';
		var dia = '<?php echo $dia; ?>';
		var diaWidthType = '<?php echo $diaWidthType; ?>';
		var orderQty = '<?php echo $orderQty; ?>';
		var colorId = '<?php echo $colorId; ?>';
		var orderMstId = '<?php echo $orderMstId; ?>';
		var orderDtlsId = '<?php echo $orderDtlsId; ?>';
		var orderBrkDownId = '<?php echo $orderBrkDownId; ?>';
		var planId = '<?php echo $planId; ?>';
		var jobNo = '<?php echo $jobNo; ?>';

		function fnc_color()
		{
			var hidden_color_id = $('#hidden_color_id').val();
			var program_color_id = $('#txt_hdn_colors').val();
        	var prog_no = $('#update_dtls_id').val();

			var title = 'Color Info';
			var page_link = 'inbound_subcontract_program_controller.php?action=actn_color'
			+ '&orderMstId=' + orderMstId 
			+ '&orderDtlsId=' + orderDtlsId 
			+ '&orderBrkDownId=' + orderBrkDownId 
			+ '&companyId=' + companyId
			+ '&gsm=' + gsm
			+ '&dia=' + dia
			+ '&colorId=' + colorId
			+ '&orderQty=' + orderQty
			+ '&planId=' + planId
			+ '&prog_no=' + prog_no
			+ '&hidden_color_id=' + hidden_color_id;

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=670px,height=300px,center=1,resize=1,scrolling=0', '../../');
			emailwindow.onclose = function ()
			{
				var theform = this.contentDoc.forms[0];
				var hidden_color_no = this.contentDoc.getElementById("txt_selected").value;
				var hidden_color_id = this.contentDoc.getElementById("txt_selected_id").value;
				var hidden_color_qnty = this.contentDoc.getElementById("txt_selected_qnty").value;
				/*
				var qnty_arr = new Array();
				var total_color_qnty = 0;
				qnty_arr = hidden_color_qnty.split(",");
				$.each(qnty_arr, function (i){
					total_color_qnty += parseFloat(qnty_arr[i]);
				});
				*/
				var hidden_color_wise_prog_data = this.contentDoc.getElementById("hidden_color_wise_prog_data").value;        		
				var hidden_total_prog_qty = this.contentDoc.getElementById("hidden_total_prog_qty").value;
				$('#txt_color').val(hidden_color_no);
				$('#hidden_color_id').val(hidden_color_id);
				//$('#txt_program_qnty').val(total_color_qnty);
				$('#txt_program_qnty').val(hidden_total_prog_qty);
				$('#hidden_color_wise_prog_data').val(hidden_color_wise_prog_data);
        		$('#hidden_color_wise_total').val(hidden_total_prog_qty);
			}
			
			/*emailwindow.onclose = function () 
        	{
        		//var theform = this.contentDoc.forms[0];
        		//var hidden_color_no = this.contentDoc.getElementById("txt_selected").value;
        		//var hidden_color_id = this.contentDoc.getElementById("txt_selected_id").value;
        		var hidden_color_prog_blance = this.contentDoc.getElementById("txt_selected_color_bl_qty").value;
        		var hidden_color_wise_prog_data = this.contentDoc.getElementById("hidden_color_wise_prog_data").value;	
        		var hidden_total_prog_qty = this.contentDoc.getElementById("hidden_total_prog_qty").value;

        		//$('#txt_color').val(hidden_color_no);
        		//$('#hidden_color_id').val(hidden_color_id);
        		$('#txt_program_qnty').val(hidden_color_prog_blance);
        		$('#hidden_color_wise_prog_data').val(hidden_color_wise_prog_data);
        		$('#txt_program_qnty').val(hidden_total_prog_qty);
        		$('#hidden_color_wise_total').val(hidden_total_prog_qty);
        		
        	}*/
		}
		
		function fnc_machine()
		{
			var save_string = $('#save_data').val();
			var txt_machine_dia = $('#txt_machine_dia').val();
			var update_dtls_id = $('#update_dtls_id').val();
			var txt_program_qnty = $('#txt_program_qnty').val();
			var txt_machine_gg = $('#txt_machine_gg').val();

			var title = 'Machine Info';
			var page_link = 'inbound_subcontract_program_controller.php?action=actn_machine&save_string=' + save_string + '&companyId=' + '<? echo $companyId; ?>' + '&txt_machine_dia=' + txt_machine_dia + '&update_dtls_id=' + update_dtls_id + '&txt_program_qnty='+txt_program_qnty + '&txt_machine_gg='+txt_machine_gg;
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=300px,center=1,resize=1,scrolling=0', '../../');
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
				var hidden_machine_dia = this.contentDoc.getElementById("hidden_machine_dia").value;
				var hidden_machine_gg = this.contentDoc.getElementById("hidden_machine_gg").value;
				

				$('#txt_machine_no').val(hidden_machine_no);
				$('#machine_id').val(hidden_machine_id);
				$('#save_data').val(save_string);
				$('#txt_machine_capacity').val(hidden_machine_capacity);
				$('#txt_distribution_qnty').val(hidden_distribute_qnty);
				$('#txt_start_date').val(hidden_min_date);
				$('#txt_end_date').val(hidden_max_date);
				$('#txt_machine_dia').val(hidden_machine_dia);
				$('#txt_machine_gg').val(hidden_machine_gg);
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

		function fnc_program_entry(operation)
		{
			if (form_validation('txt_machine_dia*txt_machine_gg*txt_fabric_dia*txt_program_qnty*txt_machine_no', 'Machine Dia*Machine GG*Finish Fabric Dia*Program Qnty*Machine No') == false)
			{
				return;
			}

			var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('cbo_knitting_source*cbo_knitting_party*txt_color*txt_machine_dia*txt_machine_gg*txt_program_qnty*txt_stitch_length*txt_spandex_stitch_length*txt_draft_ratio*machine_id*txt_machine_capacity*txt_distribution_qnty*cbo_knitting_status*txt_start_date*txt_end_date*txt_program_date*cbo_feeder*txt_remarks*save_data*updateId*update_dtls_id*cbo_color_range*cbo_dia_width_type*hidden_color_id*txt_fabric_dia*cbo_location_name*hidden_advice_data*hidden_no_of_feeder_data*hidden_collarCuff_data*hidden_count_feeding_data*hidden_came_dsign_string_data*hdn_fab_desc*hidden_color_wise_prog_data*hidden_yarn_qty_breakdown', "../../../")
			+ '&planId='+planId
			+ '&orderMstId='+orderMstId
			+ '&companyId='+companyId
			+ '&gsm='+gsm 
			+ '&dia='+dia 
			+ '&determination_id='+fabricId
			+ '&diaWidthType='+diaWidthType
			+ '&orderQty='+orderQty
			+ '&booking_no='+orderNo
			+ '&fabricDtls='+fabricDtls
			+ '&buyer_id='+partyId
			+ '&jobNo='+jobNo
			+ '&dataPre='+dataPre;

			freeze_window(operation);
			http.open("POST", "inbound_subcontract_program_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_program_entry_Reply_info;
		}

		function fnc_program_entry_Reply_info()
		{
			if (http.readyState == 4)
			{
				var reponse = trim(http.responseText).split('**');
				show_msg(reponse[0]);
				if ((reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2))
				{
					reset_form('programQnty_1', '', '', 'txt_program_date,<? echo $current_date;?>', '', 'hdn_booking_qnty*cbo_dia_width_type');
					$('#updateId').val(reponse[1]);
					show_list_view(reponse[1], 'actn_planning_details', 'list_view', 'inbound_subcontract_program_controller', '');
					set_button_status(0, permission, 'fnc_program_entry', 1);
				}
				if (reponse[0] == 14)
				{
					alert(reponse[1]);
				}
				release_freezing();
			}
		}

		function fnc_active_inactive()
		{
			var knitting_source = document.getElementById('cbo_knitting_source').value;
			reset_form('', '', 'txt_machine_no*machine_id*txt_machine_capacity*txt_distribution_qnty*txt_days_req*cbo_location_name', 'txt_program_date,<? echo $current_date; ?>', '', '');
			if (knitting_source == 1)
			{
				document.getElementById('txt_machine_no').disabled = false;
				document.getElementById('cbo_location_name').disabled = false;
			}
			else
			{
				document.getElementById('txt_machine_no').disabled = true;
				document.getElementById('cbo_location_name').disabled = true;
			}
		}
		
		function fnc_advice()
		{
			var hidden_advice_data = $('#hidden_advice_data').val();
			var title = 'Advice Info';
			var page_link = 'inbound_subcontract_program_controller.php?action=actn_advice&hidden_advice_data=' + hidden_advice_data;
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=200px,center=1,resize=1,scrolling=0', '../../');
			emailwindow.onclose = function ()
			{
				var theform = this.contentDoc.forms[0];
				var advice_data = this.contentDoc.getElementById("txt_advice").value;
				$('#hidden_advice_data').val(advice_data);
			}
		}

		function fnc_feeder()
		{
			//alert('under construction'); return;
			var no_of_feeder_data = $('#hidden_no_of_feeder_data').val();
			var hidden_color_id = $('#hidden_color_id').val();
			/*var color_type_id =<? echo $color_type_id; ?>;
			if (!(color_type_id == 2 || color_type_id == 3 || color_type_id == 4))
			{
				alert("Only for Stripe");
				return;
			}*/

			//var page_link = 'inbound_subcontract_program_controller.php?action=actn_feeder&no_of_feeder_data=' + no_of_feeder_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>'+'&hidden_color_id='+hidden_color_id;
			var title = 'Stripe Measurement Info';
			var page_link = 'inbound_subcontract_program_controller.php?action=actn_feeder&no_of_feeder_data='+no_of_feeder_data+'&hidden_color_id='+hidden_color_id+'&orderMstId='+orderMstId;
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=640px,height=300px,center=1,resize=1,scrolling=0', '../../');
			emailwindow.onclose = function ()
			{
				var theform = this.contentDoc.forms[0];
				var hidden_no_of_feeder_data = this.contentDoc.getElementById("hidden_no_of_feeder_data").value;

				$('#hidden_no_of_feeder_data').val(hidden_no_of_feeder_data);
			}
		}
		
		function fnc_collarCuff()
		{
			alert('under construction');
			
			var collarCuff_data = $('#hidden_collarCuff_data').val();
			var update_dtls_id = $('#update_dtls_id').val();
			//if (update_dtls_id == "") {
				//alert("Save Data First");
				//return;
			//}
			//var page_link = 'inbound_subcontract_program_controller.php?action=actn_collarCuff&collarCuff_data=' + collarCuff_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>&update_dtls_id=' + update_dtls_id +'&body_part_id='+'<? echo $body_part_id; ?>'
			
			var title = 'Collar & Cuff Measurement Info';
			var page_link = 'inbound_subcontract_program_controller.php?action=actn_collarCuff&collarCuff_data=' + collarCuff_data + '&update_dtls_id=' + update_dtls_id +'&body_part_id='+'<? echo $body_part_id; ?>'
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=640px,height=300px,center=1,resize=1,scrolling=0', '../../');
			emailwindow.onclose = function ()
			{
				var theform = this.contentDoc.forms[0];
				var hidden_collarCuff_data = this.contentDoc.getElementById("hidden_collarCuff_data").value;
				$('#hidden_collarCuff_data').val(hidden_collarCuff_data);
			}
		}

		function fnc_count_feeding()
		{
			//alert('under construction');
			var count_feeding_data = $('#hidden_count_feeding_data').val();
			var update_dtls_id = $('#update_dtls_id').val();
			if (update_dtls_id == "")
			{
				alert("Save Data First");
				return;
			}
			//var page_link = 'inbound_subcontract_program_controller.php?action=count_feeding_data_popup&count_feeding_data=' + count_feeding_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>&update_dtls_id=' + update_dtls_id;
			var title = 'Count Feeding';
			var page_link = 'inbound_subcontract_program_controller.php?action=actn_count_feeding&count_feeding_data=' + count_feeding_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>&update_dtls_id=' + update_dtls_id;

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=540px,height=300px,center=1,resize=1,scrolling=0', '../../');
			emailwindow.onclose = function ()
			{
				var theform = this.contentDoc.forms[0];
				var hidden_count_feeding_data = this.contentDoc.getElementById("hidden_count_feeding_data").value;
				$('#hidden_count_feeding_data').val(hidden_count_feeding_data);
			}
		}
		
		function fnc_cam_design()
		{
			//alert('under construction'); return;
			var updateDtlsId = $('#update_dtls_id').val();
			var mstId = $("#updateId").val();
			var came_dsign_string_data = $('#hidden_came_dsign_string_data').val();

			//var page_link = 'inbound_subcontract_program_controller.php?action=cam_design_info_popup&hidden_came_dsign_string_data=' + came_dsign_string_data + '&update_dtls_id='+updateDtlsId;
			var title = 'Cam Design Information';
			var page_link = 'inbound_subcontract_program_controller.php?action=actn_cam_design&hidden_came_dsign_string_data=' + came_dsign_string_data + '&update_dtls_id='+updateDtlsId;
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=250px,center=1,resize=1,scrolling=0', '../../');
			emailwindow.onclose = function ()
			{
				var theform = this.contentDoc.forms[0];
				var came_dsign_string_data = this.contentDoc.getElementById("hidden_came_dsign_string_data").value;
				$('#hidden_came_dsign_string_data').val(came_dsign_string_data);
			}
		}
	function fnc_Yarn_details(){

   
		var update_dtls_id=document.getElementById('update_dtls_id').value;
		var qty=document.getElementById('txt_program_qnty').value;
		page_link='inbound_subcontract_program_controller.php?action=yarn_details_popup&data='+update_dtls_id+'&qty='+qty	
		emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Qnty Dtls Popup', 'width=650px, height=300px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function()
		{
				
			var hidden_yarn_qty_breakdown=this.contentDoc.getElementById("hidden_yarn_qty_breakdown"); 
			
			document.getElementById('hidden_yarn_qty_breakdown').value=hidden_yarn_qty_breakdown.value;
			// document.getElementById('hidden_qtytbl_id').value=hidden_qtytbl_id.value;
			// document.getElementById('txtorderquantity_'+dataarr[1]).value=hiddenqty.value;
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
        	var page_link = 'inbound_subcontract_program_controller.php?action=collarCuff_info_popup&collarCuff_data=' + collarCuff_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>&update_dtls_id=' + update_dtls_id +'&hidden_bodypartID_data='+hidden_bodypartID_data;
        	var title = 'Collar & Cuff Measurement Info';

        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=540px,height=300px,center=1,resize=1,scrolling=0', '../');
        	emailwindow.onclose = function () {
        		var theform = this.contentDoc.forms[0];
        		var hidden_collarCuff_data = this.contentDoc.getElementById("hidden_collarCuff_data").value;

        		$('#hidden_collarCuff_data').val(hidden_collarCuff_data);
        	}
        }
	</script>
</head>
<body>
	<div align="center">
		<?php
			echo load_freeze_divs("../../../", $permission, 1);
			$current_date = date("d-m-Y");
		?>
		<form name="programQnty_1" id="programQnty_1">
			<fieldset style="width:900px;">
                <table border="1" cellpadding="0" cellspacing="0" rules="all" align="center" width="890" class="rpt_table">
                    <thead>
                        <th>Fabric Description</th>
                        <th width="80">GSM</th>
                        <th width="80">Dia</th>
                        <th width="100">Order Qty</th>
                    </thead>
                    <tr bgcolor="#FFFFFF">
                        <td>
                            <p><? echo $fabricDtls; ?></p>
                            <input type="hidden" name="hdn_fab_desc" id="hdn_fab_desc" value="<? echo trim($fabricDtls); ?>" readonly/>
                        </td>
                        <td align="center"><? echo $gsm; ?></td>
                        <td align="center"><? echo $dia; ?></td>
                        <td align="center"><? echo number_format($orderQty, 2); ?></td>
                    </tr>
                    <!--
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
                            <td align="right">Order Qty</td>
                            <td align="center">:</td>
                            <td><?php echo number_format($orderQty, 2); ?></td>
                        </tr>
                        <tr>
                            <td align="right">Fabric Description</td>
                            <td align="center">:</td>
                            <td><?php echo $fabricDtls; ?><input type="hidden" name="hdn_fab_desc" id="hdn_fab_desc" value="<? echo trim($fabricDtls); ?>" readonly/></td>
                        </tr>
                    </tbody>
                    -->
                </table>
            </fieldset>
            <fieldset style="width:900px; margin-top:5px;">
                <legend>New Entry</legend>
                <input type="hidden" id="hdn_booking_qnty" name="hdn_booking_qnty" value="<? echo $booking_qnty; ?>"/>
                <table width="900" align="center" border="0">
                    <tr>
                        <td>Knitting Source</td>
                        <td>
                            <?php
                            echo create_drop_down('cbo_knitting_source', 152, $knitting_source, '', 0, '', 1, '', 0, '1');
                            ?>
                        </td>
                        <td>Knitting Company</td>
                        <td id="knitting_party">
                            <?
							echo create_drop_down("cbo_knitting_party", 177, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Party--", $companyId, "", "");

                            ?>
                        </td>
                        <td>Color</td>
                        <td>
                            <input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:140px;" placeholder="Browse" onClick="fnc_color();" readonly/>
                            <input type="hidden" name="hidden_color_id" id="hidden_color_id" readonly/>
                            <input type="hidden" name="hidden_color_wise_prog_data" id="hidden_color_wise_prog_data" readonly>
                            <input type="hidden" name="hidden_color_wise_total" id="hidden_color_wise_total" readonly>
                        </td>
                    </tr>
                    <tr>
				    	<td class="must_entry_caption">Machine No</td>
                        <td>
                            <input type="text" name="txt_machine_no" id="txt_machine_no" class="text_boxes" placeholder="Double Click For Search" style="width:140px;" onDblClick="fnc_machine();" readonly/>
                            <input type="hidden" name="machine_id" id="machine_id" class="text_boxes" readonly/>
                        </td>
                        
                        <td class="must_entry_caption">Machine Dia</td>
                        <td>
                            <input type="text" name="txt_machine_dia" id="txt_machine_dia" class="text_boxes_numeric" style="width:60px;" maxlength="3" title="Maximum 3 Character" value=""/>
                            <?
                            echo create_drop_down("cbo_dia_width_type", 100, $fabric_typee, "", 1, "-- Select --", $diaWidthType, "");
                            ?>
                        </td>
                        <td class="must_entry_caption">Machine GG</td>
                        <td>
                            <input type="text" name="txt_machine_gg" id="txt_machine_gg" class="text_boxes" style="width:140px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Finish Fabric Dia</td>
                        <td>
                            <input type="text" name="txt_fabric_dia" id="txt_fabric_dia" class="text_boxes" style="width:140px;"/>
                        </td>
                        <td class="must_entry_caption">Program Qnty</td>
                        <td>
                            <input type="text" name="txt_program_qnty" id="txt_program_qnty" class="text_boxes_numeric" style="width:165px;"  readonly />
                        </td>
                        <td>Program / Entry date</td>
                        <td>
                            <input type="text" name="txt_program_date" id="txt_program_date" class="datepicker" style="width:140px" value="<?php echo $current_date; ?>" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td>Stitch Length</td>
                        <td>
                            <input type="text" name="txt_stitch_length" id="txt_stitch_length" class="text_boxes" style="width:140px;"/>
                        </td>
                        <td>Spandex Stitch Length</td>
                        <td>
                            <input type="text" name="txt_spandex_stitch_length" id="txt_spandex_stitch_length" class="text_boxes" style="width:165px;"/>
                        </td>
                        <td>Draft Ratio</td>
                        <td>
                            <input type="text" name="txt_draft_ratio" id="txt_draft_ratio" class="text_boxes_numeric" style="width:140px;"/>
                        </td>
                    </tr>
                    <tr>
					   <td>Color Range</td>
                        <td>
                            <?
                            echo create_drop_down("cbo_color_range", 152, $color_range, "", 1, "-- Select --", 0, "");
                            ?>
                        </td>
                        <td>Machine Capacity</td>
                        <td>
                            <input type="text" name="txt_machine_capacity" id="txt_machine_capacity" placeholder="Display" class="text_boxes_numeric" style="width:165px;" disabled="disabled"/>
                        </td>
                        <td>Distribution Qnty</td>
                        <td>
                            <input type="text" name="txt_distribution_qnty" id="txt_distribution_qnty" placeholder="Display" class="text_boxes_numeric" style="width:65px;" disabled="disabled"/>
                            <input type="text" name="txt_days_req" id="txt_days_req" placeholder="Days Req." class="text_boxes_numeric" style="width:60px;" disabled="disabled"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Start Date</td>
                        <td>
                            <input type="text" name="txt_start_date" id="txt_start_date" class="datepicker" style="width:140px" value="<? echo $start_date; ?>" readonly>
                        </td>
                        <td>End Date</td>
                        <td>
                            <input type="text" name="txt_end_date" id="txt_end_date" class="datepicker" style="width:165px" value="<? echo $end_date; ?>" readonly>
                        </td>
                        <td>Status</td>
                        <td>
                            <?
                            echo create_drop_down("cbo_knitting_status", 152, $knitting_program_status, "", 1, "--Select Status--", 0, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Feeder</td>
                        <td>
                        <?
                        $feeder = array(1 => "Full Feeder", 2 => "Half Feeder");
                        echo create_drop_down("cbo_feeder", 152, $feeder, "", 1, "--Select Feeder--", 0, "");
                        ?>
                        </td>
                        <td><b>Program No/Tube</b></td>
                        <td><input type="text" name="txt_program_no" id="txt_program_no" class="text_boxes" placeholder="Display" disabled style="width:165px"></td>
                        <td>Remarks</td>
                        <td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px"></td>
                    </tr>
                    <tr>
                        <td>Location</td>
                        <td id="location_td">
                            <?
                            echo create_drop_down("cbo_location_name", 152, "select id,location_name from lib_location where company_id='$companyId' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "");
                            ?>
                        </td>
                        <td colspan="4">
                            
                            <input type="button" name="feeder" class="formbuttonplasminus" value="Advice" onClick="fnc_advice();" style="width:100px"/>
                            <!--
                            <input type="button" name="feeder" class="formbuttonplasminus" value="No Of Feeder" onClick="fnc_feeder();" style="width:100px;"/>
                            <input type="button" name="feeder" class="formbuttonplasminus" value="Collar & Cuff" onClick="fnc_collarCuff();" style="width:100px"/>
                            -->
                            <input type="button" name="feeder" class="formbuttonplasminus" value="Count Feeding" onClick="fnc_count_feeding();" style="width:100px"/>
                            <input type="button" name="feeder" class="formbuttonplasminus" value="Cam Design" onClick="fnc_cam_design();" style="width:100px"/>
							<input type="button" name="feeder" class="formbuttonplasminus" value="Yarn Details" onClick="fnc_Yarn_details();" style="width:100px"/>
							<input type="button" name="feeder" class="formbuttonplasminus" value="Collar & Cuff"
							onClick="openpage_collarCuff();" style="width:100px"/>
                            <input type="hidden" name="hidden_advice_data" id="hidden_advice_data" class="text_boxes">
                            <input type="hidden" name="hidden_no_of_feeder_data" id="hidden_no_of_feeder_data" class="text_boxes">
                            <input type="hidden" name="hidden_collarCuff_data" id="hidden_collarCuff_data" class="text_boxes">
                            <input type="hidden" name="hidden_count_feeding_data" id="hidden_count_feeding_data" class="text_boxes">
                            <input type="hidden" name="hidden_came_dsign_string_data" id="hidden_came_dsign_string_data" value="" class="text_boxes">
							<input type="hidden" name="hidden_yarn_qty_breakdown" id="hidden_yarn_qty_breakdown" value="" class="text_boxes">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" align="right" class="button_container">
                            <?
                            echo load_submit_buttons($permission, "fnc_program_entry", 0, 0, "reset_form('programQnty_1','','','txt_start_date,$start_date*txt_end_date,$end_date*txt_program_date,$current_date','','updateId*txt_color')", 1);
                            ?>
                        </td>
                        <td colspan="2" align="left" valign="top" class="button_container">
                            <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px;"/>
                            <input type="hidden" name="save_data" id="save_data" class="text_boxes">
                            <input type="hidden" name="updateId" id="updateId" class="text_boxes" value="<? echo trim(str_replace("'", '', $plan_id)); ?>">
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
                    show_list_view('<? echo str_replace("'", '', $plan_id); ?>', 'actn_planning_details', 'list_view', 'inbound_subcontract_program_controller', '');
                </script>
                <?
            }
            ?>
            </div>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	//load_drop_down( 'inbound_subcontract_program_controller', 1+'**'+ <?php echo $companyId; ?>,'load_drop_down_knitting_party','knitting_party');
</script>
</html>
<?
exit();
}
if ($action == "collarCuff_info_popup")
{
	echo load_html_head_contents("Collar & Cuff Info", "../../../", 1, 1, '', '', '');
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
			$('#txtFinish_' + row_num).removeAttr("onDblClick").attr("onDblClick", "func_onDblClick_finishSize(" + row_num + ");");
			
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

				var bodyPart = $(this).find('input[name="txtBodyPartId[]"]').val();
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
					save_string = bodyPart + "_" + txtGrey + "_" + txtFinish + "_" + txtQtyPcs;
				}
				else {
					save_string += "," + bodyPart + "_" + txtGrey + "_" + txtFinish + "_" + txtQtyPcs;
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
		
		//func_onDblClick_finishSize
		function func_onDblClick_finishSize(rowNo)
		{
			//alert('su..re');
        	var page_link = 'planning_info_entry_controller.php?action=action_finishSize&pre_cost_id=' + '<? echo $pre_cost_id; ?>';
        	var title = 'Finish Size';
        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=250px,center=1,resize=1,scrolling=0', '../../');
        	emailwindow.onclose = function ()
			{
        		var theform = this.contentDoc.forms[0];
        		var item_size = this.contentDoc.getElementById("hdn_item_size").value;
				var item_size_arr = item_size.split(',');

				if(item_size_arr.length > 1)
				{
					var i = 1;
					for(i; i<item_size_arr.length; i++)
					{
						var rowNo = $('#txt_tot_row').val();					
						add_break_down_tr(rowNo);
					}
					
					//value assigning here
					var row_num = $('#txt_tot_row').val();
					var r = 0;
					for(r; r<=row_num; r++)
					{
						$('#txtFinish_' + r).val(item_size_arr[r]);
					}
				}
				else
				{
					var rowNo = $('#txt_tot_row').val();
					$('#txtFinish_' + rowNo).val(item_size);
				}
        	}
		}
		
		function add_break_down_tr_zs(i)
		{
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
			$('#txtFinish_' + row_num).removeAttr("onDblClick").attr("onDblClick", "func_onDblClick_finishSize(" + row_num + ");");
			
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
									$body_part = $body_part_wise_data[0];
									$grey = $body_part_wise_data[1];
									$finish = $body_part_wise_data[2];
									$qty = $body_part_wise_data[3];
									$totQtyPcs += $qty;
									?>
									<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
										<td width="30" align="center"><? echo $sl; ?></td>
										<td width="100">
											<input type="text" name="txtBodyPartId[]" id="txtBodyPartId_<?php echo $i ?>"
											value="<? echo $body_part; ?>" class="text_boxes"
											style="width:80px" />
											
										</td>
										<td width="100"><input type="text" name="txtGrey[]" id="txtGrey_<? echo $i; ?>"
											class="text_boxes" style="width:80px"
											value="<? echo $grey; ?>"/>
										</td>
										<td width="100"><input type="text" name="txtFinish[]" id="txtFinish_<? echo $i; ?>"
											class="text_boxes" style="width:80px" value="<? echo $finish; ?>" />
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
										$sl++;
									}
							}
							else
							{
								$sql = "select collar_cuff_data from subcon_planning_dtls where id=$update_dtls_id";
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
									<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl; ?>">
										<td width="30" align="center"><? echo $sl; ?></td>
										<td width="100">
											<input type="text" name="txtBodyPartId[]" id="txtBodyPartId_<?php echo $i ?>"
											value="<? echo $collar_data[0];  ?>" class="text_boxes"
											style="width:80px" />
											
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
									$i++;$sl++;
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
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="yarn_details_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	

	?>
	<script>
		

		

		function add_share_row( i )
		{
			
			var row_num=$('#tbl_share_details_entry tr').length-1;
			if (row_num!=i)
			{
				return false;
			}
			i++;
			$("#tbl_share_details_entry tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_share_details_entry");

			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_share_row("+i+");");	
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");		

			$('#txtsl_'+i).val(i);
			$('#txtyarncount_'+i).val('');
			$('#txtyarnlot_'+i).val('');
			$('#txtbrand_'+i).val('');
			$('#txtqnty_'+i).val('');
			
			
			

			
		}
		function fn_deletebreak_down_tr(rowNo)
		{
			var numRow = $('table#tbl_share_details_entry tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				var updateIdDtls=$('#hiddenid_'+rowNo).val();
				var txt_deleted_id=$('#txt_deleted_id').val();
				var selected_id='';

				if(updateIdDtls!='')
				{
					if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
					$('#txt_deleted_id').val( selected_id );
				}

				$('#tbl_share_details_entry tbody tr:last').remove();
			}
			else
			{
				return false;
			}

		
		}

		function fnc_close()
		{
			var tot_row=$('#tbl_share_details_entry tbody tr').length;

			var yarncount=""; var yarnlot=""; 
			
			var yarn_break="";var brand=""; 
		
			var qnty="";

			for(var i=1; i<=tot_row; i++)
			{		
		
				
				var yarncount=$('#cboyarncount_'+i).val();
				var yarnlot=$('#txtyarnlot_'+i).val();
				var brand=$('#txtbrand_'+i).val();
				var qnty=$('#txtqnty_'+i).val();
				var hiddenqtyid=$('#hiddenqtyid_'+i).val()*1;
			
				if(i==1){
					yarn_break =yarncount+"_"+yarnlot+"_"+brand+"_"+qnty;
				}else{
					yarn_break +="__"+yarncount+"_"+yarnlot+"_"+brand+"_"+qnty;
				}
			}		
		
	
			document.getElementById('hidden_yarn_qty_breakdown').value=yarn_break;	
		
			
			
			parent.emailwindow.hide();
		}

		

	              

        </script>
		</head>
		
		<body >
		<div align="center" style="width:100%;" >
            <form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
                <table class="rpt_table" width="650px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
                    <thead>
				    	<th>SL</th> 
                        <th>Yarn Count</th>                      
                        <th>Yarn Lot</th>  
						<th>Brand</th>                      
                        <th> Qty</th>                      
                        <th>&nbsp;</th>
                    </thead>
                <tbody>
                	<input type="hidden" name="hiddencheckrcvqty" id="hiddencheckrcvqty" value="<? echo $check_recive_qty;?>">
                  
                   
                    <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes_numeric" style="width:90px" readonly />
			      	<?
					$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count where status_active=1 and is_deleted=0",'id','yarn_count');

						if($data > 0){
						$sql="select id, subcon_planning_dtls_id, yarn_count_id, yarn_lot, brand, qnty from subcon_planning_yarn_dtls_breakdown where subcon_planning_dtls_id=$data";
						}
					
						$sql_data=sql_select($sql);

							if(count($sql_data)>0){
														
								
								$k=0;

								for($i=0; $i<count($sql_data); $i++)
								{
									$k++;
								// foreach($sql_data as $val){
								// 	$k++;?>
								
							<tr>								
								<td><input type="text"  id="txtsl_<? echo $k;?>" name="txtsl_<? echo $k;?>" class="text_boxes" style="width:30px" value="<? echo $k; ?>" /></td>		

								<td>
								<?
										echo create_drop_down("cboyarncount_$k",150,"select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",1, "-- Select --", $sql_data[$i][csf('yarn_count_id')], "","","","","","",18);
                                    ?>
							</td>								
								<td>
									<input type="text" id="txtyarnlot_<? echo $k;?>" name="txtyarnlot_<? echo $k;?>" class="text_boxes" style="width:70px"  value="<? echo $sql_data[$i][csf('yarn_lot')]; ?>" />
									
								</td>
								<td>
									<input type="text" id="txtbrand_<? echo $k;?>" name="txtbrand_<? echo $k;?>" class="text_boxes" style="width:70px"  value="<? echo $sql_data[$i][csf('brand')]; ?>" />
									
								</td>
								<td>
									<input type="text" id="txtqnty_<? echo $k;?>" name="txtqnty_<? echo $k;?>" class="text_boxes" style="width:70px"  value="<? echo $sql_data[$i][csf('qnty')]; ?>" />
									
								</td>
								
								 <td>
                                 	<input type="hidden" id="hiddenqtyid_<? echo $k;?>" name="hiddenqtyid_<? echo $k;?>"  value="<?=$sql_data[$i][csf('id')];?>" style="width:15px;" class="text_boxes"/>
									 

                                    <input type="button" id="increaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="+" onClick="add_share_row(<? echo $k;?>)" />
                                    <input type="button" id="decreaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k;?> ,'tbl_share_details_entry' );"/>
								</td>
							</tr>
							 <?
							  }
							}else{
								$k=0;						
								$k++;
								?>
							<tr>								
								<td><input type="text"  id="txtsl_<? echo $k;?>" name="txtsl_<? echo $k;?>" class="text_boxes" style="width:30px" value="<? echo $k; ?>" /></td>		
								<td><?
										echo create_drop_down("cboyarncount_$k",150,"select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",1, "-- Select --", $selected, "","","","","","",18);
                                    ?></td>									
								<td>
									<input type="text" id="txtyarnlot_<? echo $k;?>" name="txtyarnlot_<? echo $k;?>" class="text_boxes" style="width:70px"   value="<? echo $break_qunty[$i]; ?>" />
									
								</td>
								<td>
									<input type="text" id="txtbrand_<? echo $k;?>" name="txtbrand_<? echo $k;?>" class="text_boxes" style="width:70px"   value="<? echo $break_qunty[$i]; ?>" />
									
								</td>
								<td>
									<input type="text" id="txtqnty_<? echo $k;?>" name="txtqnty_<? echo $k;?>" class="text_boxes" style="width:70px"   value="<? echo $break_qunty[$i]; ?>" />
									
								</td>
								 <td>
                                 	<input type="hidden" id="hiddenqtyid" name="hiddenqtyid"  value="" style="width:15px;" class="text_boxes"/>
								
                                    <input type="button" id="increaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="+" onClick="add_share_row(<? echo $k;?>)" />
                                    <input type="button" id="decreaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k;?> ,'tbl_share_details_entry' );"/>
								</td>
							</tr>


									

							<?}
					
				?>
				</tbody>
            </table>
       		
            
            <table>
                <tr>
                    <td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
					<input type="hidden" name="hidden_yarn_qty_breakdown" id="hidden_yarn_qty_breakdown">
                </tr>
            </table>
             
             <input type="hidden" name="hidden_qtytbl_id" id="hidden_qtytbl_id">
      
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}
/*
|--------------------------------------------------------------------------
| actn_cam_design
|--------------------------------------------------------------------------
|
*/
if ($action == "actn_cam_design")
{
	echo load_html_head_contents("Cam Design Information", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>
		function fnc_close()
		{
			var isFind = 0;
			var save_came_dsign_string = "";
			$("#tbl_came_design").find('tbody tr').each(function () {
				var updateId = $(this).find('input[name="updateid[]"]').val().toUpperCase().trim();
				var cmd1 = $(this).find('input[name="cmd1[]"]').val().toUpperCase().trim();
				var cmd2 = $(this).find('input[name="cmd2[]"]').val().toUpperCase().trim();
				var cmd3 = $(this).find('input[name="cmd3[]"]').val().toUpperCase().trim();
				var cmd4 = $(this).find('input[name="cmd4[]"]').val().toUpperCase().trim();
				var cmd5 = $(this).find('input[name="cmd5[]"]').val().toUpperCase().trim();
				var cmd6 = $(this).find('input[name="cmd6[]"]').val().toUpperCase().trim();
				var cmd7 = $(this).find('input[name="cmd7[]"]').val().toUpperCase().trim();
				var cmd8 = $(this).find('input[name="cmd8[]"]').val().toUpperCase().trim();
				var cmd9 = $(this).find('input[name="cmd9[]"]').val().toUpperCase().trim();
				var cmd10 = $(this).find('input[name="cmd10[]"]').val().toUpperCase().trim();
				var cmd11 = $(this).find('input[name="cmd11[]"]').val().toUpperCase().trim();
				var cmd12 = $(this).find('input[name="cmd12[]"]').val().toUpperCase().trim();
				var cmd13 = $(this).find('input[name="cmd13[]"]').val().toUpperCase().trim();
				var cmd14 = $(this).find('input[name="cmd14[]"]').val().toUpperCase().trim();
				var cmd15 = $(this).find('input[name="cmd15[]"]').val().toUpperCase().trim();
				var cmd16 = $(this).find('input[name="cmd16[]"]').val().toUpperCase().trim();
				var cmd17 = $(this).find('input[name="cmd17[]"]').val().toUpperCase().trim();
				var cmd18 = $(this).find('input[name="cmd18[]"]').val().toUpperCase().trim();
				var cmd19 = $(this).find('input[name="cmd19[]"]').val().toUpperCase().trim();
				var cmd20 = $(this).find('input[name="cmd20[]"]').val().toUpperCase().trim();
				var cmd21 = $(this).find('input[name="cmd21[]"]').val().toUpperCase().trim();
				var cmd22 = $(this).find('input[name="cmd22[]"]').val().toUpperCase().trim();
				var cmd23 = $(this).find('input[name="cmd23[]"]').val().toUpperCase().trim();
				var cmd24 = $(this).find('input[name="cmd24[]"]').val().toUpperCase().trim();
				
				if(isFind != 1)
				{
					if(cmd1 != '' || cmd2 != '' || cmd3 != '' || cmd4 != '' || cmd5 != '' || cmd6 != '' || cmd7 != '' || cmd8 != '' || cmd9 != '' || cmd10 != '' || cmd11 != '' || cmd12 != '' || cmd13 != '' || cmd14 != '' || cmd15 != '' || cmd16 != '' || cmd17 != '' || cmd18 != '' || cmd19 != '' || cmd20 != '' || cmd21 != '' || cmd22 != '' || cmd23 != '' || cmd24 != '' )
					{
						isFind = 1;
					}
				}

				if (save_came_dsign_string == "")
				{
					save_came_dsign_string = updateId +"_"+ cmd1 + "_" + cmd2 + "_" + cmd3 + "_" + cmd4+ "_" + cmd5+ "_" + cmd6+ "_" + cmd7+ "_" + cmd8+ "_" + cmd9+ "_" + cmd10+ "_" + cmd11+ "_" + cmd12+ "_" + cmd13+ "_" + cmd14+ "_" + cmd15+ "_" + cmd16+ "_" + cmd17+ "_" + cmd18+ "_" + cmd19+ "_" + cmd20+ "_" + cmd21+ "_" + cmd22+ "_" + cmd23+ "_" + cmd24;
				}
				else
				{
					save_came_dsign_string += "," + updateId +"_"+ cmd1 + "_" + cmd2 + "_" + cmd3 + "_" + cmd4+ "_" + cmd5+ "_" + cmd6+ "_" + cmd7+ "_" + cmd8+ "_" + cmd9+ "_" + cmd10+ "_" + cmd11+ "_" + cmd12+ "_" + cmd13+ "_" + cmd14+ "_" + cmd15+ "_" + cmd16+ "_" + cmd17+ "_" + cmd18+ "_" + cmd19+ "_" + cmd20+ "_" + cmd21+ "_" + cmd22+ "_" + cmd23+ "_" + cmd24;
				}
			});

			if(isFind == 1)
			{
				$('#hidden_came_dsign_string_data').val("'"+save_came_dsign_string+"'");
			}
			else
			{
				$('#hidden_came_dsign_string_data').val('');
			}
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div style="width:900px;" align="center">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:900px; margin-top:5px">
				<input type="hidden" name="hidden_came_dsign_string_data" id="hidden_came_dsign_string_data" value="" >
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table">
					<thead>
						<th width="4%">SL</th>
						<?
						for ($i=1; $i<=24; $i++)
						{
							?>
							<th width="4%"><? echo $i; ?></th>
							<?
						}
						?>
					</thead>
				</table>
				<div style="width:890px;" id="buyer_list_view">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table" id="tbl_came_design">
                        <tbody>
                            <?
                            $sql_camdesign = "
                                SELECT 
                                    b.id, b.mst_id, b.dtls_id, b.cmd1, b.cmd2, b.cmd3, b.cmd4, b.cmd5, b.cmd6, b.cmd7, b.cmd8, b.cmd9, b.cmd10, b.cmd11, b.cmd12, b.cmd13, b.cmd14, b.cmd15, b.cmd16, b.cmd17, b.cmd18, b.cmd19, b.cmd20, b.cmd21, b.cmd22, b.cmd23, b.cmd24 
                                FROM 
                                    subcon_planning_dtls a, 
                                    subcon_planning_camdesign_dtls b 
                                WHERE 
                                    a.id=b.dtls_id 
                                    AND a.mst_id=b.mst_id 
                                    AND a.status_active=1 
                                    AND a.is_deleted=0 
                                    AND b.status_active=1 
                                    AND b.is_deleted=0 
                                    --AND a.id=".$update_dtls_id." 
                                    AND b.dtls_id=".$update_dtls_id."";
    
                            $sql_camdesign_data = sql_select($sql_camdesign);
                            if(empty($sql_camdesign_data))
                            {
                                $row_data =  array(1,2,3,4,5,6,7,8);
                            }
							else
							{
                                $row_data = $sql_camdesign_data;
                            }
                            $i=1;
                            foreach ($row_data as $row)
                            {
                                ?>
                                <tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i ;?>">
                                    <td width="4%" align="center"><? echo $i; ?>
                                    <input type="hidden" name="updateid[]" id="updateid_<?echo $i?>"  value="<? echo $id = ($row[csf('id')]!="")?$row[csf('id')]:""; ?>"/>
                                </td>
                                <td width="4%">
                                    <input type="text" name="cmd1[]" id="cmd1_<?php echo $i ?>" value="<? echo $cmd1 = ($row[csf('cmd1')]!="")?$row[csf('cmd1')]:""; ?>" class="text_boxes" style="width:70%; text-transform: uppercase;" />
                                </td>
                                <td width="4%">
                                    <input type="text" name="cmd2[]" id="cmd2_<? echo $i; ?>"
                                    class="text_boxes " style="width:70%; text-transform: uppercase; text-align: center;"
                                    value="<? echo $cmd1 = ($row[csf('cmd2')]!="")?$row[csf('cmd2')]:""; ?>"/>
                                </td>
                                <td width="4%">
                                    <input type="text" name="cmd3[]" id="cmd3_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase; text-align: center;"
                                    value="<? echo $cmd3 = ($row[csf('cmd3')]!="")?$row[csf('cmd3')]:""; ?>"/>
                                </td>
                                <td width="4%">
                                    <input type="text" name="cmd4[]" id="cmd4_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase; text-align: center;"
                                    value="<? echo $cmd4 = ($row[csf('cmd4')]!="")?$row[csf('cmd4')]:""; ?>"/>
                                </td>
                                <td width="4%">
                                    <input type="text" name="cmd5[]" id="cmd5_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase; text-align: center;"
                                    value="<? echo $cmd5 = ($row[csf('cmd5')]!="")?$row[csf('cmd5')]:""; ?>"/>
                                </td>
                                <td width="4%">
                                    <input type="text" name="cmd6[]" id="cmd6_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd6 = ($row[csf('cmd6')]!="")?$row[csf('cmd6')]:""; ?>"/>
                                </td>
                                <td width="4%">
                                    <input type="text" name="cmd7[]" id="cmd7_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd7 = ($row[csf('cmd7')]!="")?$row[csf('cmd7')]:""; ?>"/>
                                </td>
                                <td width="4%">
                                    <input type="text" name="cmd8[]" id="cmd8_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd8 = ($row[csf('cmd8')]!="")?$row[csf('cmd8')]:""; ?>"/>
                                </td>
                                <td width="4%">
                                    <input type="text" name="cmd9[]" id="cmd9_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd9 = ($row[csf('cmd9')]!="")?$row[csf('cmd9')]:""; ?>">
                                </td>
                                <td width="4%">
                                    <input type="text" name="cmd10[]" id="cmd10_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd10 = ($row[csf('cmd10')]!="")?$row[csf('cmd10')]:""; ?>"/>
                                </td>
                                <td width="4%">
                                    <input type="text" name="cmd11[]" id="cmd11_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd11 = ($row[csf('cmd11')]!="")?$row[csf('cmd11')]:""; ?>"/>
                                </td>
                                <td width="4%">
                                    <input type="text" name="cmd12[]" id="cmd12_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd12 = ($row[csf('cmd12')]!="")?$row[csf('cmd12')]:""; ?>"/>
                                </td>
                                <td width="4%"><input type="text" name="cmd13[]" id="cmd13_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd13 = ($row[csf('cmd13')]!="")?$row[csf('cmd13')]:""; ?>"/>
                                </td>
                                <td width="4%"><input type="text" name="cmd14[]" id="cmd14_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd14 = ($row[csf('cmd14')]!="")?$row[csf('cmd14')]:""; ?>"/>
                                </td>
                                <td width="4%"><input type="text" name="cmd15[]" id="cmd15_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd15 = ($row[csf('cmd15')]!="")?$row[csf('cmd15')]:""; ?>"/>
                                </td>
                                <td width="4%"><input type="text" name="cmd16[]" id="cmd16_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd16 = ($row[csf('cmd16')]!="")?$row[csf('cmd16')]:""; ?>"/>
                                </td>
                                <td width="4%"><input type="text" name="cmd17[]" id="cmd17_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd17 = ($row[csf('cmd17')]!="")?$row[csf('cmd17')]:""; ?>"/>
                                </td>
                                <td width="4%"><input type="text" name="cmd18[]" id="cmd18_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd18 = ($row[csf('cmd18')]!="")?$row[csf('cmd18')]:""; ?>"/>
                                </td>
                                <td width="4%"><input type="text" name="cmd19[]" id="cmd19_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd19 = ($row[csf('cmd19')]!="")?$row[csf('cmd19')]:""; ?>"/>
                                </td>
                                <td width="4%"><input type="text" name="cmd20[]" id="cmd20_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd20 = ($row[csf('cmd20')]!="")?$row[csf('cmd20')]:""; ?>"/>
                                </td>
                                <td width="4%"><input type="text" name="cmd21[]" id="cmd21_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd21 = ($row[csf('cmd21')]!="")?$row[csf('cmd21')]:""; ?>"/>
                                </td>
                                <td width="4%"><input type="text" name="cmd22[]" id="cmd22_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd22 = ($row[csf('cmd22')]!="")?$row[csf('cmd22')]:""; ?>"/>
                                </td>
                                <td width="4%"><input type="text" name="cmd23[]" id="cmd23_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd23 = ($row[csf('cmd23')]!="")?$row[csf('cmd23')]:""; ?>"/>
                                </td>
                                <td width="4%"><input type="text" name="cmd24[]" id="cmd24_<? echo $i; ?>"
                                    class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
                                    value="<? echo $cmd24 = ($row[csf('cmd24')]!="")?$row[csf('cmd24')]:""; ?>"/>
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
                        ?>
                    </tbody>
					</table>
                </div>
                <table width="890" id="tbl_close">
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| actn_count_feeding
|--------------------------------------------------------------------------
|
*/
if ($action == "actn_count_feeding")
{
	echo load_html_head_contents("Count Feeding", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function add_break_down_tr(i)
		{
			var row_num = $('#tbl_list_search tr').length;
			row_num++;

			var clone = $("#tr_" + i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});

			clone.find("input,select").each(function () {
				$(this).attr({
					'id': function (_, id)
					{
						var id = id.split("_");
						return id[0] + "_" + row_num
					},
					'name': function (_, name)
					{
						return name
					},
					'value': function (_, value)
					{
						return value
					}
				});
			}).end();

			$("#tr_" + i).after(clone);
			//$('#txtSeqNo_' + row_num).removeAttr("value").attr("value", row_num);
			$('#cboCount_' + row_num).removeAttr("value").attr("value", 0);
			$('#cboFeeding_' + row_num).removeAttr("value").attr("value", 0);
			$('#yarnlot_' + row_num).removeAttr("value").attr("value", '');

			$('#increase_' + row_num).removeAttr("value").attr("value", "+");
			$('#decrease_' + row_num).removeAttr("value").attr("value", "-");
			$('#increase_' + row_num).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + row_num + ");");
			$('#decrease_' + row_num).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + row_num + ");");

			reArrangeSl();
			set_all_onclick();
		}

		function reArrangeSl()
		{
			var i = 0;
			$("#tbl_list_search").find('tbody tr').each(function (){
				i++;
				$(this).find("td:eq(0)").text(i);
				$(this).find("td:eq(1) input").val(i);
			});
		}

		function fn_deleteRow(rowNo)
		{
			if (rowNo != 1)
			{
				$("#tr_" + rowNo).remove();
				reArrangeSl();
			}
		}

		function fnc_close()
		{
			var save_string = "";
			var breakOut = true;
			$("#tbl_list_search").find('tbody tr').each(function () {
				if (breakOut == false)
				{
					return;
				}

				var txtSeqNo = $(this).find('input[name="txtSeqNo[]"]').val();
				var cboCount = $(this).find('select[name="cboCount[]"]').val();
 				var yarnlot = $(this).find('input[name="yarnlot[]"]').val();
				var cboFeeding = $(this).find('select[name="cboFeeding[]"]').val();

				if(cboCount != 0 && cboFeeding != 0)
				{
					if (save_string == "")
					{
						save_string = txtSeqNo + "_" + cboCount + "_" + cboFeeding+ "_" + yarnlot;
					}
					else
					{
						save_string += "," + txtSeqNo + "_" + cboCount + "_" + cboFeeding+ "_" + yarnlot;
					}
				}
			});

			if (breakOut == false)
			{
				return;
			}

			$('#hidden_count_feeding_data').val(save_string);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div style="width:530px;" align="center">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:530px; margin-top:5px">
				<input type="hidden" name="hidden_count_feeding_data" id="hidden_count_feeding_data" class="text_boxes"
				value="">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="525" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="80">Seq. No</th>
						<th width="100">Count</th>
                        <th width="100">Lot</th>
						<th width="100">Feeding</th>
						<th></th>
					</thead>
				</table>
				<div style="width:525px; overflow-y:scroll; max-height:230px;" id="buyer_list_view">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="505" class="rpt_table" id="tbl_list_search">
					<tbody>
						<?
						$yarn_count_arr=return_library_array("SELECT id, yarn_count FROM lib_yarn_count WHERE status_active=1 AND is_deleted=0 ORDER BY yarn_count","id","yarn_count");
						$count_feeding_data_arr = ($count_feeding_data != "") ? explode(",", $count_feeding_data) : array();
						if (!empty($count_feeding_data))
						{
							$sl = 1;
							for ($i = 0; $i < count($count_feeding_data_arr); $i++)
							{
								$count_feeding_data = explode("_", $count_feeding_data_arr[$i]);
								$seq = $count_feeding_data[0];
								$count_id = $count_feeding_data[1];
								$feeding_id = $count_feeding_data[2];
								$lot_number = $count_feeding_data[3];
								?>
								<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="30" align="center"><? echo $sl++; ?></td>
									<td width="80">
										<input type="text" name="txtSeqNo[]" id="txtSeqNo_<?php echo $i ?>" value="<? echo $seq;?>" class="text_boxes" style="width:65px"/>
									</td>
									<td width="100">
										<?
										echo create_drop_down( "cboCount_".$i, 100, $yarn_count_arr,"", 1, "-- Count --",$count_id, "",0,"","","","","","","cboCount[]");
										?>
									</td>
                                    <td width="100">
                                         <input type="text" name="yarnlot[]" id="yarnlot_<?php echo $i ?>" value="<? echo $lot_number;  ?>" class="text_boxes" style="width:87px">
									</td>
									<td width="100">
										<?
										echo create_drop_down( "cboFeeding_".$i, 100, $feeding_arr,"", 1, "-- Feeding --",$feeding_id, "",0,"","","","","","","cboFeeding[]");
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
							$i=0;$sl=1;
							?>
							<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								<td width="30" align="center"><? echo $sl++; ?></td>
								<td width="80">
									<input type="text" name="txtSeqNo[]" id="txtSeqNo_<?php echo $i ?>" value="1" class="text_boxes" style="width:65px"/>
								</td>
								<td width="100">
									<?
									echo create_drop_down( "cboCount_".$i, 100, $yarn_count_arr,"", 1, "-- Count --", $selected, "",0,"","","","","","","cboCount[]");
									?>
								</td>
                                <td width="100">
									<input type="text" name="yarnlot[]" id="yarnlot_<?php echo $i ?>" class="text_boxes" style="width:87px">
								</td>
								<td width="100">
									<?
									echo create_drop_down( "cboFeeding_".$i, 100, $feeding_arr,"", 1, "-- Feeding --", $selected, "",0,"","","","","","","cboFeeding[]");
									?>
								</td>
								<td>
									<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>)"/>
									<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| actn_feeder
|--------------------------------------------------------------------------
|
*/
if ($action == "actn_collarCuff")
{
	echo load_html_head_contents("Collar & Cuff Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
	function add_break_down_tr(i)
	{
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
		
	function reArrangeSl()
	{
		var i = 0;
		$("#tbl_list_search").find('tbody tr').each(function () {
			i++;
			$(this).find("td:eq(0)").text(i);
		});
	}

	function fn_deleteRow(rowNo)
	{
		if (rowNo != 1)
		{
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
				//$sql = "select collar_cuff_data from ppl_planning_info_entry_dtls where id=$update_dtls_id and is_sales=1";
				//$collar_cuff_data_arr = sql_select($sql);
				//$collar_cuff_data = explode(",", $collar_cuff_data_arr[0]["collar_cuff_data"]);
				$pre_cost_id = implode(",", array_unique(explode(",", $pre_cost_id)));
				$sql = "
					SELECT 
						a.body_part_id, b.item_size 
					FROM 
						wo_pre_cost_fabric_cost_dtls a, 
						wo_pre_cos_fab_co_avg_con_dtls b 
					WHERE 
						a.id=b.pre_cost_fabric_cost_dtls_id 
						--AND a.id in(".$pre_cost_id.") 
						AND a.body_part_id in(".$body_part_id.") 
					group by 
						a.body_part_id, b.item_size order by a.body_part_id";

				$result = sql_select($sql);
				$i = 1;
				foreach ($result as $row)
				{
					$finish = $row[csf('item_size')];
					?>
					<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl++; ?>">
						<td width="30" align="center"><? echo $sl; ?></td>
						<td width="100">
							<input type="text" name="txtBodyPartId[]" id="txtBodyPartId_<?php echo $i ?>"
							value="<? echo $body_part[$row[csf('body_part_id')]]; ?>" class="text_boxes"
							style="width:80px" disabled/>
							<input type="hidden" name="bodyPartId[]" id="bodyPartId_<?php echo $i ?>"
							value="<? echo $row[csf('body_part_id')]; ?>"/>
						</td>
						<td width="100"><input type="text" name="txtGrey[]" id="txtGrey_<? echo $i; ?>"
							class="text_boxes" style="width:80px"
							value=""/>
						</td>
						<td width="100"><input type="text" name="txtFinish[]" id="txtFinish_<? echo $i; ?>"
							class="text_boxes" style="width:80px"
							value="<? echo $finish; ?>"/>
						</td>
						<td width="100">
							<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $i; ?>"
							class="text_boxes_numeric" style="width:80px"
							value="" onKeyUp="calculate_tot_qnty();"/>
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| actn_feeder
|--------------------------------------------------------------------------
|
*/
if ($action == "actn_feeder")
{
	echo load_html_head_contents("Machine Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function fnc_close()
		{
			var save_string = '';
			var tot_row = $("#tbl_list_search tbody tr").length;

			for (var i = 1; i <= tot_row; i++)
			{
				var txtPreCostId = $('#txtPreCostId_' + i).val();
				var txtColorId = $('#txtColorId_' + i).val();
				var txtStripeColorId = $('#txtStripeColorId_' + i).val();
				var txtNoOfFeeder = $('#txtNoOfFeeder_' + i).val();

				if (save_string == "")
				{
					save_string = txtPreCostId + "_" + txtColorId + "_" + txtStripeColorId + "_" + txtNoOfFeeder;
				}
				else
				{
					save_string += "," + txtPreCostId + "_" + txtColorId + "_" + txtStripeColorId + "_" + txtNoOfFeeder;
				}
			}

			$('#hidden_no_of_feeder_data').val(save_string);
			parent.emailwindow.hide();
		}

		function calculate_total()
		{
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
					<input type="hidden" name="hidden_no_of_feeder_data" id="hidden_no_of_feeder_data" class="text_boxes" value="">
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
							$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
							$noOfFeeder_array = array();
							$no_of_feeder_data = explode(",", $no_of_feeder_data);
							//$pre_cost_id = explode(",", $pre_cost_id);
							//$pre_cost_id = implode(",", array_unique($pre_cost_id));

							for ($i = 0; $i < count($no_of_feeder_data); $i++)
							{
								$color_wise_data = explode("_", $no_of_feeder_data[$i]);
								$pre_cost_fabric_cost_dtls_id = $color_wise_data[0];
								$color_id = $color_wise_data[1];
								$stripe_color = $color_wise_data[2];
								$no_of_feeder = $color_wise_data[3];
								$noOfFeeder_array[$pre_cost_fabric_cost_dtls_id][$color_id][$stripe_color] = $no_of_feeder;
							}

							if($hidden_color_id!="")
							{
								//$colorCondition = "AND color_number_id IN(".$hidden_color_id.")";
								$colorCondition = "AND color_id IN(".$hidden_color_id.")";
							}

							/*
							$sql = "
								SELECT 
									pre_cost_fabric_cost_dtls_id as pre_cost_id, color_number_id, stripe_color, measurement, uom 
								FROM 
									wo_pre_stripe_color 
								WHERE 
									status_active = 1 
									AND is_deleted = 0 
									".$colorCondition."
									--AND pre_cost_fabric_cost_dtls_id in(".$pre_cost_id.")
								";
							*/
							
							$sql = "
								SELECT 
							   		id, mst_id, order_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss, gsm, embellishment_type, description, dia_width_type, grey_dia, finish_dia, body_part, job_no_mst, book_con_dtls_id, delivery_status, booked_qty, process, is_revised, prod_sequence_no
								FROM 
								subcon_ord_breakdown
								WHERE
									mst_id = ".$orderMstId."
									".$colorCondition."
							";
							$result = sql_select($sql);
							$i = 1;
							$tot_feeder = 0;
							/*
							foreach ($result as $row)
							{
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$no_of_feeder = $noOfFeeder_array[$row[csf('pre_cost_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]];
								$tot_feeder += $no_of_feeder;
								?>
								<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
									<td width="40" align="center"><? echo $i; ?>
								</td>
								<td width="140"><p><? echo $color_library[$row[csf('color_number_id')]]; ?></p></td>
								<td width="130"><p><? echo $color_library[$row[csf('stripe_color')]]; ?></p></td>
								<td width="90"><input type="text" name="txtMeasurement[]" id="txtMeasurement_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf('measurement')]; ?>" disabled/></td>
									<td width="70" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p>
									</td>
									<td align="center"><input type="text" name="txtNoOfFeeder[]" id="txtNoOfFeeder_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" value="<? echo $no_of_feeder; ?>" onKeyUp="calculate_total();"/></td>
								</tr>
								<?
								$i++;
							}
							*/
							foreach ($result as $row)
							{
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$no_of_feeder = $noOfFeeder_array[$row[csf('pre_cost_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]];
								$tot_feeder += $no_of_feeder;
								?>
								<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
									<td width="40" align="center"><? echo $i; ?>
								</td>
								<td width="140"><p><? echo $color_library[$row[csf('color_id')]]; ?></p></td>
								<td width="130"><p><? //echo $color_library[$row[csf('stripe_color')]]; ?></p></td>
								<td width="90"><input type="text" name="txtMeasurement[]" id="txtMeasurement_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? //echo $row[csf('measurement')]; ?>" disabled/></td>
									<td width="70" align="center"><p><? //echo $unit_of_measurement[$row[csf('uom')]]; ?></p>
									</td>
									<td align="center"><input type="text" name="txtNoOfFeeder[]" id="txtNoOfFeeder_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" value="<? echo $no_of_feeder; ?>" onKeyUp="calculate_total();"/></td>
								</tr>
								<?
								$i++;
							}
							?>
						</tbody>
						<tfoot>
							<th colspan="5">Total</th>
							<th style="text-align:center"><input type="text" name="txtTotFeeder" id="txtTotFeeder" class="text_boxes_numeric" style="width:90px" value="<? echo $tot_feeder; ?>" disabled/></th>
							</tfoot>
						</table>
					</div>
					<table width="600" id="tbl_close">
						<tr>
							<td align="center">
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px"/>
                                <input type="hidden" name="txtPreCostId[]" id="txtPreCostId_<?php echo $i ?>" value="<? echo $row[csf('pre_cost_id')]; ?>"/>
                                <input type="hidden" name="txtColorId[]" id="txtColorId_<?php echo $i ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                                <input type="hidden" name="txtStripeColorId[]" id="txtStripeColorId_<?php echo $i ?>" value="<? echo $row[csf('stripe_color')]; ?>"/>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

/*
|--------------------------------------------------------------------------
| actn_advice
|--------------------------------------------------------------------------
|
*/
if ($action == "actn_advice")
{
	echo load_html_head_contents("Advice Info", "../../../", 1, 1, '', '', '');
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
						<td><textarea name="txt_advice" id="txt_advice" class="text_area" style="width:385px; height:120px;"><? echo $hidden_advice_data; ?></textarea></td>
                    </tr>
                </table>
                <table width="400" id="tbl_close">
                    <tr>
                        <td align="center"><input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px"/></td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| actn_planning_details
|--------------------------------------------------------------------------
|
*/
if ($action == "actn_planning_details")
{
	$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$sql = "SELECT id, knitting_source, knitting_party, color_range, machine_dia, machine_gg, program_qnty, stitch_length, spandex_stitch_length, draft_ratio, status, program_date,color_id FROM subcon_planning_dtls WHERE mst_id in (".$data.") AND status_active=1 AND is_deleted=0 ORDER BY id DESC";
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="960" class="rpt_table">
		<thead>
			<th width="90">Knitting Source</th>
			<th width="100">Knitting Company</th>
			<th width="60">Prog. No</th>
			<th width="90">Color Range</th>
			<th width="70">Machine Dia</th>
			<th width="70">Machine GG</th>
			<th width="80">Program Qnty</th>
			<th width="75">Stitch Length</th>
			<th width="80">Span. Stitch Length</th>
			<th width="70">Draft Ratio</th>
			<th width="75">Program Date</th>
			<th>Status</th>
		</thead>
	</table>
	<div style="width:960px; max-height:140px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="942" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		$colors = "";
		$result = sql_select($sql);
		foreach ($result as $row)
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";
			/*	
			if ($row[csf('knitting_source')] == 1)
				$knit_party = $company_arr[$row[csf('knitting_party')]];
			else
				$knit_party = $supllier_arr[$row[csf('knitting_party')]];
			*/
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'actn_set_planning_data', 'inbound_subcontract_program_controller' );">
				<td width="90"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
				<td width="100"><p><? echo $company_arr[$row[csf('knitting_party')]]; ?></p></td>
				<td width="60" align="center"><p><? echo $row[csf('id')];?></p></td>
				<td width="90" align="center"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
				<td width="70" align="center"><p><? echo $row[csf('machine_dia')]; ?></p></td>
				<td width="70" align="center"><? echo $row[csf('machine_gg')]; ?></td>
				<td width="80" align="center"><? echo number_format($row[csf('program_qnty')], 2); ?></td>
				<td width="75" align="center"><p><? echo $row[csf('stitch_length')]; ?></p></td>
				<td width="80" align="center"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
				<td width="70" align="center"><? echo number_format($row[csf('draft_ratio')], 2); ?></td>
				<td width="75" align="center"><? echo change_date_format($row[csf('program_date')]); ?></td>
				<td align="center"><p><? echo $knitting_program_status[$row[csf('status')]]; ?></p></td>
			</tr>
			<?
			$colors .= $row[csf('color_id')] . ",";
			$i++;
		}
		?>
	</table>
	<input type="hidden" id="txt_hdn_colors" value="<?php echo $colors; ?>"/>
</div>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| actn_set_planning_data
|--------------------------------------------------------------------------
|
*/
if ($action == "actn_set_planning_data")
{
	$machine_arr = return_library_array("SELECT id, machine_no FROM lib_machine_name", 'id', 'machine_no');
	$color_library = return_library_array("SELECT id,color_name FROM lib_color", "id", "color_name");
	
	/*
	|--------------------------------------------------------------------------
	| subcon_planning_feeding_dtls
	|--------------------------------------------------------------------------
	|
	*/
	$sql_count_feed = "SELECT seq_no, count_id, feeding_id,yarn_lot FROM subcon_planning_feeding_dtls WHERE dtls_id=".$data." ORDER BY seq_no";
	$data_array_count_feed = sql_select($sql_count_feed);
	foreach ($data_array_count_feed as $row)
	{
		$count_feeding_data_arr[]=$row[csf('seq_no')].'_'.$row[csf('count_id')].'_'.$row[csf('feeding_id')].'_'.$row[csf('yarn_lot')];
	}
	$count_feeding_data_arr_str=implode(',',$count_feeding_data_arr);

	/*
	|--------------------------------------------------------------------------
	| subcon_planning_dtls
	|--------------------------------------------------------------------------
	|
	*/
	$sql = "SELECT id, knitting_source, knitting_party, color_id, color_range, machine_dia, width_dia_type, machine_gg, fabric_dia, program_qnty, stitch_length, spandex_stitch_length, draft_ratio, machine_id, machine_capacity, distribution_qnty, status, start_date, end_date, program_date, feeder, remarks, save_data, no_fo_feeder_data, location_id, advice FROM subcon_planning_dtls WHERE id=".$data."";
	//echo $sql;
	$data_array = sql_select($sql);
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_knitting_source').value 			= '" . $row[csf("knitting_source")] . "';\n";
		//echo "load_drop_down('inbound_subcontract_program_controller', " . $row[csf("knitting_source")] . "+'**'+" . $row[csf("knitting_party")] . "+'**1', 'load_drop_down_knitting_party','knitting_party');\n";
		$color = '';
		$color_id = explode(",", $row[csf("color_id")]);
		foreach ($color_id as $val)
		{
			if ($color == "")
				$color = $color_library[$val];
			else
				$color .= "," . $color_library[$val];
		}

		//echo "document.getElementById('knitting_party').value 				= '" . $row[csf("knitting_party")] . "';\n";
		echo "document.getElementById('cbo_knitting_party').value 				= '" . $row[csf("knitting_party")] . "';\n";
		echo "document.getElementById('txt_color').value 					= '" . $color . "';\n";
		echo "document.getElementById('hidden_color_id').value 				= '" . $row[csf("color_id")] . "';\n";
		echo "document.getElementById('cbo_color_range').value 				= '" . $row[csf("color_range")] . "';\n";
		echo "document.getElementById('txt_machine_dia').value 				= '" . $row[csf("machine_dia")] . "';\n";
		echo "document.getElementById('cbo_dia_width_type').value 			= '" . $row[csf("width_dia_type")] . "';\n";
		echo "document.getElementById('txt_machine_gg').value 				= '" . $row[csf("machine_gg")] . "';\n";
		echo "document.getElementById('txt_fabric_dia').value 				= '" . $row[csf("fabric_dia")] . "';\n";
		echo "document.getElementById('txt_program_qnty').value 			= '" . $row[csf("program_qnty")] . "';\n";
		echo "document.getElementById('txt_stitch_length').value 			= '" . $row[csf("stitch_length")] . "';\n";
		echo "document.getElementById('txt_spandex_stitch_length').value 	= '" . $row[csf("spandex_stitch_length")] . "';\n";
		echo "document.getElementById('txt_draft_ratio').value 				= '" . $row[csf("draft_ratio")] . "';\n";

		//echo "fnc_active_inactive();\n";
		echo "document.getElementById('machine_id').value 					= '" . $row[csf("machine_id")] . "';\n";
		$machine_ids = $row[csf("machine_id")];
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

		if($machine_ids!="")
		{
			$save_data = '';
			
			//echo "SELECT id, machine_id, dia, capacity, distribution_qnty, no_of_days, start_date, end_date FROM subcon_planning_machine_dtls WHERE dtls_id='".$data."' AND machine_id IN(".$machine_ids.") AND status_active=1 AND is_deleted=0";
			
			$data_machine_array = sql_select("SELECT id, machine_id, dia, capacity, distribution_qnty, no_of_days, start_date, end_date FROM subcon_planning_machine_dtls WHERE dtls_id='".$data."' AND machine_id IN(".$machine_ids.") AND status_active=1 AND is_deleted=0");
			foreach ($data_machine_array as $row_m)
			{
				$start_date = change_date_format($row_m[csf("start_date")]);
				$end_date = change_date_format($row_m[csf("end_date")]);

				if ($save_data == "")
				{
					$save_data = $row_m[csf("machine_id")]."_".$row_m[csf("dia")]."_".$row_m[csf("capacity")]."_".$row_m[csf("distribution_qnty")]."_".$row_m[csf("no_of_days")]."_".$start_date."_".$end_date."_".$row_m[csf("id")];
				}
				else
				{
					$save_data .= ",".$row_m[csf("machine_id")]."_".$row_m[csf("dia")]."_".$row_m[csf("capacity")]."_".$row_m[csf("distribution_qnty")]."_".$row_m[csf("no_of_days")]."_".$start_date."_".$end_date."_".$row_m[csf("id")];
				}
			}
		}

		$str = '';
		$data_machine_array = sql_select("SELECT id, mst_id, dtls_id, pre_cost_id, color_id, stripe_color_id, no_of_feeder FROM subcon_planning_feeder_dtls WHERE dtls_id='".$data."' AND status_active=1 AND is_deleted=0");
		foreach ($data_machine_array as $row_m)
		{
			if ($str == '')
				$str = $row_m[csf("pre_cost_id")] . "_" . $row_m[csf("color_id")] . "_" . $row_m[csf("stripe_color_id")] . "_" . $row_m[csf("no_of_feeder")];
			else
				$str .= "," . $row_m[csf("pre_cost_id")] . "_" . $row_m[csf("color_id")] . "_" . $row_m[csf("stripe_color_id")] . "_" . $row_m[csf("no_of_feeder")];
		}
		echo "document.getElementById('hidden_no_of_feeder_data').value 					= '" . $str . "';\n";//$row[csf("save_data")]
		echo "document.getElementById('save_data').value 					= '" . $save_data . "';\n";//$row[csf("save_data")]
		echo "document.getElementById('cbo_location_name').value 			= '" . $row[csf("location_id")] . "';\n";
		$advice = str_replace("\n","\\n",$row[csf("advice")]); //die();
		echo "document.getElementById('hidden_advice_data').value 			= '" . $advice . "';\n";
		echo "document.getElementById('update_dtls_id').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('txt_program_no').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('hidden_count_feeding_data').value	= '" .$count_feeding_data_arr_str. "';\n";
		echo "days_req();\n";
		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_program_entry',1);\n";

		$str_collar = '';
		$data_collar_cuff = sql_select("SELECT id, mst_id, dtls_id, body_part, grey_size,finish_size, qty_pcs FROM subcon_planning_collar_cuff_dtls WHERE dtls_id='".$data."' AND status_active=1");
		foreach ($data_collar_cuff as $row_collar)
		{
			if ($str_collar == '') $str_collar = $row_collar[csf("body_part")] . "_" . $row_collar[csf("grey_size")] . "_" . $row_collar[csf("finish_size")] . "_" . $row_collar[csf("qty_pcs")];
			else   $str_collar .= "," . $row_collar[csf("body_part")] . "_" . $row_collar[csf("grey_size")] . "_" . $row_collar[csf("finish_size")] . "_" . $row_collar[csf("qty_pcs")];
		}
		echo "document.getElementById('hidden_collarCuff_data').value 		= '" . $str_collar . "';\n";//$row[csf("save_data")]
		exit();
	}
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

	/*
	|--------------------------------------------------------------------------
	| Insert
	|--------------------------------------------------------------------------
	|
	*/
	if ($operation == 0)
	{
		$con = connect();
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

		if (str_replace("'", '', $updateId) != "")
		{
			$get_existing_program_qty = return_field_value("SUM(program_qnty) AS program_qnty", "subcon_planning_plan_dtls", "mst_id=".$updateId." AND status_active=1 AND is_deleted=0", "program_qnty");
			if ((str_replace("'", "", $txt_program_qnty)+($get_existing_program_qty*1)) > ceil(str_replace("'", "", $orderQty)))
			{
				echo "14**Program quantity can not be greater than order quantitys.";
				disconnect($con);
				exit();
			}
		}
		else
		{
			if (str_replace("'", "", $txt_program_qnty) > ceil(str_replace("'", "", $orderQty)))
			{
				echo "14**Program quantity can not be greater than order quantity";
				disconnect($con);
				exit();
			}
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_planning_mst
		| data preparing for
		| $data_array
		|--------------------------------------------------------------------------
		|
		*/
		$id = '';
		if (str_replace("'", '', $updateId) == "")
		{
			$id = return_next_id("id", "subcon_planning_mst", 1);
			$field_array = "id,company_id,buyer_id,determination_id,fabric_desc,gsm_weight,dia,width_dia_type,subcon_order_id,job_no,inserted_by,insert_date";
			$data_array = "('".$id."','".$companyId."','".$buyer_id."','".$determination_id."','".$fabricDtls."','".$gsm."','".$dia."','".$diaWidthType."',".$orderMstId.",'".$jobNo."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
		}
		else
		{
			$id = str_replace("'", '', $updateId);
			$flag = 1;
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_planning_dtls
		| data preparing for
		| $data_array_dtls
		|--------------------------------------------------------------------------
		|
		*/
		$dtls_id = return_next_id("id", "subcon_planning_dtls", 1);
		$field_array_dtls = "id,mst_id,knitting_source,knitting_party,color_id,color_range,machine_dia,width_dia_type,machine_gg,fabric_dia,program_qnty,stitch_length,spandex_stitch_length,draft_ratio,machine_id,machine_capacity,distribution_qnty,status,start_date,end_date,program_date,feeder,remarks,save_data,location_id,advice,yarn_details_breakdown,is_sales,inserted_by,insert_date";

		$data_array_dtls = "(".$dtls_id.",".$id.",".$cbo_knitting_source.",".$cbo_knitting_party.",".$hidden_color_id.",".$cbo_color_range.",".$txt_machine_dia.",".$cbo_dia_width_type.",".$txt_machine_gg.",".$txt_fabric_dia.",".$txt_program_qnty.",".$txt_stitch_length.",".$txt_spandex_stitch_length.",".$txt_draft_ratio.",".$machine_id.",".$txt_machine_capacity.",".$txt_distribution_qnty.",".$cbo_knitting_status.",".$txt_start_date.",".$txt_end_date.",".$txt_program_date.",".$cbo_feeder.",".$txt_remarks.",".$save_data.",".$cbo_location_name.",".$hidden_advice_data.",".$hidden_yarn_qty_breakdown.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		
		$yarn_qty_breakdown_arr=explode("__",str_replace("'",'',$hidden_yarn_qty_breakdown));		

		$yid=return_next_id( "id", "subcon_planning_yarn_dtls_breakdown", 1 ) ;
		$yarn_field_array="id, subcon_planning_dtls_id, yarn_count_id,  yarn_lot, brand, qnty, status_active, inserted_by, insert_date";
		
		foreach ($yarn_qty_breakdown_arr as $yarndtlsdata) {
			$yarndtls=explode("_",$yarndtlsdata);	

				if ($comma!=0) $yarn_data_array .=",";

				$yarn_data_array .="(".$yid.",".$dtls_id.",".$yarndtls[0].",'".$yarndtls[1]."','".$yarndtls[2]."','".$yarndtls[3]."','1','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
				$comma++;$yid++;
			
		}
	
		$breakID=sql_insert("subcon_planning_yarn_dtls_breakdown",$yarn_field_array,$yarn_data_array,1);


		
		
		/*
		|--------------------------------------------------------------------------
		| subcon_planning_plan_dtls
		| data preparing for
		| $data_array_plan_dtls
		|--------------------------------------------------------------------------
		|
		*/
		$data = str_replace("'", "", $dataPre);
		if ($data != "")
		{
			$plan_dtls_id = return_next_id("id", "subcon_planning_plan_dtls", 1);
			$field_array_plan_dtls = "id,mst_id,dtls_id,company_id,buyer_id,po_id,determination_id,fabric_desc,gsm_weight,dia,width_dia_type,color_id,program_qnty,inserted_by,insert_date";
			$hidden_color_wise_prog_data = str_replace("'", "", $hidden_color_wise_prog_data);
			$expColorData = explode(",", $hidden_color_wise_prog_data);
			
			$data = explode("_", $data);
			for ($i = 0; $i < count($data); $i++)
			{
				$z = 0;
				for ($z; $z < count($expColorData); $z++)
				{
					$colorDataArr = explode("_",$expColorData[$z]);
					//$colorDataArr[0] = txtColorId
					//$colorDataArr[1] = txtColorProgQty
					//$colorDataArr[2] = coloProgUpdateId
					//insert
					if($colorDataArr[2] == 0)
					{
						if($colorDataArr[1] > 0)
						{
							$plan_data = explode("**", $data[$i]);
							$partyId = $plan_data[0];
							$orderNo = $plan_data[1];
							$fabricId = $plan_data[2];
							$fabricDtls = $plan_data[3];
							$gsm = $plan_data[4];
							$dia = $plan_data[5];
							$diaWidthType = trim($plan_data[6]);
							$orderQty = $plan_data[7];
							$orderMstId = $plan_data[8];
							$orderDtlsId = $plan_data[9];
							$orderBrkDownId = $plan_data[10];
							$colorId = $plan_data[11];
							
							if ($db_type == 0)
							{
								$start_date = change_date_format($start_date, "yyyy-mm-dd", "-");
								$end_date = change_date_format($end_date, "yyyy-mm-dd", "-");
							}
							else
							{
								$start_date = change_date_format($start_date, '', '', 1);
								$end_date = change_date_format($end_date, '', '', 1);
							}
			
							$perc = ($booking_qnty / $tot_booking_qnty) * 100;
							$prog_qnty = number_format(($perc * str_replace("'", '', $txt_program_qnty) / 100), 2, '.', '');
			
							if ($data_array_plan_dtls != "")
								$data_array_plan_dtls .= ",";
			
							$data_array_plan_dtls .= "('".$plan_dtls_id."',".$id.",'".$dtls_id."','".$companyId."','".$partyId."','".$orderDtlsId."','".$fabricId."','".$fabricDtls."','".$gsm."','".$dia."','".$diaWidthType."',".$colorDataArr[0].",".$colorDataArr[1].",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
							$plan_dtls_id = $plan_dtls_id + 1;
						}
					}
				}
			}
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_planning_machine_dtls
		| data preparing for
		| $data_array_machine_dtls
		|--------------------------------------------------------------------------
		|
		*/
		$machine_dtls_id = return_next_id("id", "subcon_planning_machine_dtls", 1);
		$field_array_machine_dtls = "id,mst_id,dtls_id,machine_id,dia,capacity,distribution_qnty,no_of_days,start_date,end_date,inserted_by,insert_date";

		/*
		|--------------------------------------------------------------------------
		| subcon_planning_machine_dtwise
		| data preparing for
		| $data_array_machine_dtls_datewise
		|--------------------------------------------------------------------------
		|
		*/
		$machine_dtls_datewise_id = return_next_id("id", "subcon_planning_machine_dtwise", 1);
		$field_array_machine_dtls_datewise = "id,mst_id,dtls_id,machine_id,distribution_date,fraction_date,days_complete,qnty,machine_plan_id,inserted_by,insert_date";

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
					$sCurrentDate = date("Y-m-d", strtotime("-1 day", strtotime($startDate)));
					$days = $noOfDays;
					$fraction = 0;
					$days_complete = 0;
					while ($sCurrentDate < $endDate)
					{
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
						if ($days >= 1)
						{
							$fraction = 0;
							$days_complete = 1;
							$dateWise_qnty = $capacity;
						}
						else
						{
							$fraction = 1;
							$days_complete = $days;
							$dateWise_qnty = $bl_qnty;
						}

						$days = $days - 1;
						$bl_qnty = $bl_qnty - $capacity;

						if ($db_type == 0)
							$curr_date = $sCurrentDate;
						else
							$curr_date = change_date_format($sCurrentDate, '', '', 1);

						if ($data_array_machine_dtls_datewise != "")
							$data_array_machine_dtls_datewise .= ",";
						$data_array_machine_dtls_datewise .= "(".$machine_dtls_datewise_id.",".$id.",".$dtls_id.",'".$machine_id."','".$curr_date."','".$fraction."','".$days_complete."','".$dateWise_qnty."','".$machine_dtls_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
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
				$data_array_machine_dtls .= "(".$machine_dtls_id.",".$id.",".$dtls_id.",'".$machine_id."','".$dia."','".$capacity."','".$qnty."','".$noOfDays."','".$mstartDate."','".$mendDate."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$machine_dtls_id = $machine_dtls_id + 1;
			}
		}




		/*
		|--------------------------------------------------------------------------
		| subcon_planning_camdesign_dtls
		| data preparing for
		| $data_array_cam_design_dtls
		|--------------------------------------------------------------------------
		|
		*/
		$hidden_came_dsign_string_data = str_replace("'", "", $hidden_came_dsign_string_data);
		if ($hidden_came_dsign_string_data != "")
		{
			$cam_design_dtls_id = return_next_id("id", "subcon_planning_camdesign_dtls", 1);
			$field_array_cam_design_dtls = "id,mst_id,dtls_id,cmd1,cmd2,cmd3,cmd4,cmd5,cmd6,cmd7,cmd8,cmd9,cmd10,cmd11,cmd12,cmd13,cmd14,cmd15,cmd16,cmd17,cmd18,cmd19,cmd20,cmd21,cmd22,cmd23,cmd24,inserted_by,insert_date";
			$came_dsign_string_data = explode(",", $hidden_came_dsign_string_data);
			for ($i = 0; $i < count($came_dsign_string_data); $i++) {
				$came_dsign_data = explode("_", $came_dsign_string_data[$i]);
				$udpdateId = $came_dsign_data[0];
				$cmd1 = $came_dsign_data[1];
				$cmd2 = $came_dsign_data[2];
				$cmd3 = $came_dsign_data[3];
				$cmd4 = $came_dsign_data[4];
				$cmd5 = $came_dsign_data[5];
				$cmd6 = $came_dsign_data[6];
				$cmd7 = $came_dsign_data[7];
				$cmd8 = $came_dsign_data[8];
				$cmd9 = $came_dsign_data[9];
				$cmd10 = $came_dsign_data[10];
				$cmd11 = $came_dsign_data[11];
				$cmd12 = $came_dsign_data[12];
				$cmd13 = $came_dsign_data[13];
				$cmd14 = $came_dsign_data[14];
				$cmd15 = $came_dsign_data[15];
				$cmd16 = $came_dsign_data[16];
				$cmd17 = $came_dsign_data[17];
				$cmd18 = $came_dsign_data[18];
				$cmd19 = $came_dsign_data[19];
				$cmd20 = $came_dsign_data[20];
				$cmd21 = $came_dsign_data[21];
				$cmd22 = $came_dsign_data[22];
				$cmd23 = $came_dsign_data[23];
				$cmd24 = $came_dsign_data[24];

				if ($data_array_cam_design_dtls != "")
					$data_array_cam_design_dtls .= ",";

				$data_array_cam_design_dtls .= "(".$cam_design_dtls_id.",".$id.",".$dtls_id.",'".$cmd1."','".$cmd2."','".$cmd3."','".$cmd4."','".$cmd5."','".$cmd6."','".$cmd7."','".$cmd8."','".$cmd9."','".$cmd10."','".$cmd11."','".$cmd12."','".$cmd13."','".$cmd14."','".$cmd15."','".$cmd16."','".$cmd17."','".$cmd18."','".$cmd19."','".$cmd20."','".$cmd21."','".$cmd22."','".$cmd23."','".$cmd24."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$cam_design_dtls_id = $cam_design_dtls_id + 1;
			}
		}
		
		/*
		|--------------------------------------------------------------------------
		| subcon_planning_mst
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if (str_replace("'", '', $updateId) == "")
		{
			$rsltPlanningMst = sql_insert("subcon_planning_mst", $field_array, $data_array, 0);
			if ($rsltPlanningMst)
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
		| subcon_planning_dtls
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1)
		{
			$rsltPlanningDtls = sql_insert("subcon_planning_dtls", $field_array_dtls, $data_array_dtls, 0);
			if ($rsltPlanningDtls)
				$flag = 1;
			else
				$flag = 0;
		}
		
		/*
		|--------------------------------------------------------------------------
		| subcon_planning_plan_dtls
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1 && $data != '' && $data_array_plan_dtls != '')
		{
			$rsltPlanningPlanDtls = sql_insert("subcon_planning_plan_dtls", $field_array_plan_dtls, $data_array_plan_dtls, 0);
			if ($rsltPlanningPlanDtls)
				$flag = 1;
			else
				$flag = 0;
		}
		
		/*
		|--------------------------------------------------------------------------
		| subcon_planning_machine_dtls
		| subcon_planning_machine_dtwise
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if ($save_data != "")
		{
			if ($data_array_machine_dtls != "")
			{
				$rsltMachineDtls = sql_insert("subcon_planning_machine_dtls", $field_array_machine_dtls, $data_array_machine_dtls, 0);
				if ($flag == 1)
				{
					if ($rsltMachineDtls)
						$flag = 1;
					else
						$flag = 0;
				}
			}

			if ($data_array_machine_dtls_datewise != "")
			{
				$rsltMachineDatewise = sql_insert("subcon_planning_machine_dtwise", $field_array_machine_dtls_datewise, $data_array_machine_dtls_datewise, 0);
				if ($flag == 1)
				{
					if ($rsltMachineDatewise)
						$flag = 1;
					else
						$flag = 0;
				}
			}
		}
		
		/*
		|--------------------------------------------------------------------------
		| subcon_planning_camdesign_dtls
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1 && $data_array_cam_design_dtls != '')
		{
			$rsltPlanningCamDesignDtls = sql_insert("subcon_planning_camdesign_dtls", $field_array_cam_design_dtls, $data_array_cam_design_dtls, 0);
			if ($rsltPlanningCamDesignDtls)
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

		$update_dtls_id = str_replace("'", "", $update_dtls_id);
		//echo "10**".$hidden_color_id; die;
		
		$get_existing_program_qty = return_field_value("SUM(program_qnty) AS program_qnty", "subcon_planning_plan_dtls", "mst_id=".$updateId." AND dtls_id != ".$update_dtls_id." AND status_active=1 AND is_deleted=0", "program_qnty");
		if ((str_replace("'", "", $txt_program_qnty)+($get_existing_program_qty*1)) > ceil(str_replace("'", "", $orderQty)))
		{
			echo "14**Program quantity can not be greater than order quantitys.";
			disconnect($con);
			exit();
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_planning_dtls
		| data preparing for
		| $data_array_dtls
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_dtls = "knitting_source*knitting_party*color_id*color_range*machine_dia*width_dia_type*machine_gg*fabric_dia*program_qnty*stitch_length*spandex_stitch_length*draft_ratio*machine_id*machine_capacity*distribution_qnty*status*start_date*end_date*program_date*feeder*remarks*save_data*location_id*advice*yarn_details_breakdown*collar_cuff_data*updated_by*update_date";
		$data_array_dtls = $cbo_knitting_source."*".$cbo_knitting_party."*".$hidden_color_id."*".$cbo_color_range."*".$txt_machine_dia."*".$cbo_dia_width_type."*".$txt_machine_gg."*".$txt_fabric_dia."*".$txt_program_qnty."*".$txt_stitch_length."*".$txt_spandex_stitch_length."*".$txt_draft_ratio."*".$machine_id."*".$txt_machine_capacity."*".$txt_distribution_qnty."*".$cbo_knitting_status."*".$txt_start_date."*".$txt_end_date."*".$txt_program_date."*".$cbo_feeder."*".$txt_remarks."*".$save_data."*".$cbo_location_name."*".$hidden_advice_data."*".$hidden_yarn_qty_breakdown."*".$hidden_collarCuff_data."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		
		$yarn_qty_breakdown_arr=explode("__",str_replace("'",'',$hidden_yarn_qty_breakdown));		

		$yid=return_next_id( "id", "subcon_planning_yarn_dtls_breakdown", 1 ) ;
		$yarn_field_array="id, subcon_planning_dtls_id, yarn_count_id,  yarn_lot, brand, qnty, status_active, inserted_by, insert_date";
		
		if(str_replace("'",'',$hidden_yarn_qty_breakdown)!="")
		{
	
		foreach ($yarn_qty_breakdown_arr as $yarndtlsdata) {
			$yarndtls=explode("_",$yarndtlsdata);	
			
			$rId6=execute_query( "delete from subcon_planning_yarn_dtls_breakdown where subcon_planning_dtls_id=$update_dtls_id",0);
			
				if ($comma!=0) $yarn_data_array .=",";

				$yarn_data_array .="(".$yid.",".$update_dtls_id.",".$yarndtls[0].",'".$yarndtls[1]."','".$yarndtls[2]."','".$yarndtls[3]."','1','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
				$comma++;$yid++;
			
		}
	
		$breakID=sql_insert("subcon_planning_yarn_dtls_breakdown",$yarn_field_array,$yarn_data_array,1);
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_planning_plan_dtls
		| data preparing for
		| $data_array_plan_dtls
		|--------------------------------------------------------------------------
		|
		*/
		$rmsintegretion = return_field_value("rms_integretion", "variable_settings_production","company_name='".$companyId."' AND variable_list=7 AND status_active=1 AND is_deleted=0");
		$update_sl = 0;
		if($rmsintegretion == 1)
		{
			$exists_sl = sql_select("SELECT MAX(a.update_sl) AS update_sl_no FROM ppl_planning_entry_plan_dtls a WHERE a.booking_no='$booking_no' AND a.status_active=1 AND a.is_deleted=0");
			$update_sl = $exists_sl[0][csf('update_sl_no')]+1;
		}

		$plan_dtls_id = return_next_id("id", "subcon_planning_plan_dtls", 1);
		$field_array_plan_dtls = "id,mst_id,dtls_id,company_id,buyer_id,po_id,determination_id,fabric_desc,gsm_weight,dia,width_dia_type,color_id,program_qnty,update_sl,inserted_by,insert_date";
		$data = str_replace("'", "", $dataPre);
		if ($data != "")
		{
			$plan_dtls_id = return_next_id("id", "subcon_planning_plan_dtls", 1);
			$field_array_plan_dtls = "id,mst_id,dtls_id,company_id,buyer_id,po_id,determination_id,fabric_desc,gsm_weight,dia,width_dia_type,color_id,program_qnty,update_sl,inserted_by,insert_date";
			$field_array_plan_dtls_update = "color_id*program_qnty*updated_by*update_date";
			$hidden_color_wise_prog_data = str_replace("'", "", $hidden_color_wise_prog_data);
			$expColorData = explode(",", $hidden_color_wise_prog_data);
			
			$data = explode("_", $data);
			for ($i = 0; $i < count($data); $i++)
			{
				$z = 0;
				for ($z; $z < count($expColorData); $z++)
				{
					$colorDataArr = explode("_",$expColorData[$z]);
					//$colorDataArr[0] = txtColorId
					//$colorDataArr[1] = txtColorProgQty
					//$colorDataArr[2] = coloProgUpdateId
					//insert
					if($colorDataArr[2] == 0)
					{
						if($colorDataArr[1] > 0)
						{
							$plan_data = explode("**", $data[$i]);
							$partyId = $plan_data[0];
							$orderNo = $plan_data[1];
							$fabricId = $plan_data[2];
							$fabricDtls = $plan_data[3];
							$gsm = $plan_data[4];
							$dia = $plan_data[5];
							$diaWidthType = trim($plan_data[6]);
							$orderQty = $plan_data[7];
							$orderMstId = $plan_data[8];
							$orderDtlsId = $plan_data[9];
							$orderBrkDownId = $plan_data[10];
							$colorId = $plan_data[11];
							
							if ($db_type == 0)
							{
								$start_date = change_date_format($start_date, "yyyy-mm-dd", "-");
								$end_date = change_date_format($end_date, "yyyy-mm-dd", "-");
							}
							else
							{
								$start_date = change_date_format($start_date, '', '', 1);
								$end_date = change_date_format($end_date, '', '', 1);
							}
			
							$perc = ($booking_qnty / $tot_booking_qnty) * 100;
							$prog_qnty = number_format(($perc * str_replace("'", '', $txt_program_qnty) / 100), 2, '.', '');
			
							if ($data_array_plan_dtls != "")
								$data_array_plan_dtls .= ",";
			
							$data_array_plan_dtls .= "('".$plan_dtls_id."',".$updateId.",'".$update_dtls_id."','".$companyId."','".$partyId."','".$orderDtlsId."','".$fabricId."','".$fabricDtls."','".$gsm."','".$dia."','".$diaWidthType."',".$colorDataArr[0].",".$colorDataArr[1].",'".$update_sl."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
							$plan_dtls_id = $plan_dtls_id + 1;
						}
					}
					else
					{
						//echo "10**";
						$colorprog_upd_id_arr[] = $colorDataArr[2];
						$data_array_plan_dtls_update[$colorDataArr[2]] = explode("*", ("'" . $colorDataArr[0] . "'*'". $colorDataArr[1] . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
					}
				}
			}
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_planning_machine_dtls
		| data preparing for
		| $data_array_machine_dtls
		|--------------------------------------------------------------------------
		|
		*/
		$machine_dtls_id = return_next_id("id", "subcon_planning_machine_dtls", 1);
		$field_array_machine_dtls = "id,mst_id,dtls_id,machine_id,dia,capacity,distribution_qnty,no_of_days,start_date,end_date,inserted_by,insert_date";

		/*
		|--------------------------------------------------------------------------
		| subcon_planning_machine_dtwise
		| data preparing for
		| $data_array_machine_dtls_datewise
		|--------------------------------------------------------------------------
		|
		*/
		$machine_dtls_datewise_id = return_next_id("id", "subcon_planning_machine_dtwise", 1);
		$field_array_machine_dtls_datewise = "id,mst_id,dtls_id,machine_id,distribution_date,fraction_date,days_complete,qnty,machine_plan_id,inserted_by,insert_date";

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
					$sCurrentDate = date("Y-m-d", strtotime("-1 day", strtotime($startDate)));
					$days = $noOfDays;
					$fraction = 0;
					$days_complete = 0;
					while ($sCurrentDate < $endDate)
					{
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
						if ($days >= 1)
						{
							$fraction = 0;
							$days_complete = 1;
							$dateWise_qnty = $capacity;
						}
						else
						{
							$fraction = 1;
							$days_complete = $days;
							$dateWise_qnty = $bl_qnty;
						}

						$days = $days - 1;
						$bl_qnty = $bl_qnty - $capacity;

						if ($db_type == 0)
							$curr_date = $sCurrentDate;
						else
							$curr_date = change_date_format($sCurrentDate, '', '', 1);

						if ($data_array_machine_dtls_datewise != "")
							$data_array_machine_dtls_datewise .= ",";
						$data_array_machine_dtls_datewise .= "(".$machine_dtls_datewise_id.",".$updateId.",".$update_dtls_id.",'".$machine_id."','".$curr_date."','".$fraction."','".$days_complete."','".$dateWise_qnty."','".$machine_dtls_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
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
				$data_array_machine_dtls .= "(".$machine_dtls_id.",".$updateId.",".$update_dtls_id.",'".$machine_id."','".$dia."','".$capacity."','".$qnty."','".$noOfDays."','".$mstartDate."','".$mendDate."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$machine_dtls_id = $machine_dtls_id + 1;
			}
		}
		
		/*
		|--------------------------------------------------------------------------
		| subcon_planning_feeding_dtls
		| data preparing for
		| $data_array_count_feeding_dtls
		|--------------------------------------------------------------------------
		|
		*/
		$hidden_count_feeding_data = str_replace("'", "", $hidden_count_feeding_data);
		if($hidden_count_feeding_data  != "")
		{
			$count_feeding_id = return_next_id("id", "subcon_planning_feeding_dtls", 1);
			$field_array_count_feeding_dtls = "id, mst_id, dtls_id, seq_no, count_id,feeding_id,yarn_lot, inserted_by, insert_date";
			$hidden_count_feeding_data_arr = explode(",", $hidden_count_feeding_data);
			for ($i = 0; $i < count($hidden_count_feeding_data_arr); $i++)
			{
				$count_feeding_data_arr = explode("_", $hidden_count_feeding_data_arr[$i]);
				$seq_no = $count_feeding_data_arr[0];
				$count_id = $count_feeding_data_arr[1];
				$feeding_id = $count_feeding_data_arr[2];
				$yarn_lot_no = $count_feeding_data_arr[3];
				
				if ($data_array_count_feeding_dtls != "")
					$data_array_count_feeding_dtls .= ",";
				$data_array_count_feeding_dtls .= "(".$count_feeding_id.",".$updateId.",".$update_dtls_id.",".$seq_no.",".$count_id.",".$feeding_id.",'".$yarn_lot_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$count_feeding_id = $count_feeding_id + 1;
			}
		}
		
		/*
		|--------------------------------------------------------------------------
		| subcon_planning_camdesign_dtls
		| data preparing for
		| $data_array_cam_design_dtls
		|--------------------------------------------------------------------------
		|
		*/
		$hidden_came_dsign_string_data = str_replace("'", "", $hidden_came_dsign_string_data);
		if ($hidden_came_dsign_string_data != "")
		{
			$field_array_cam_design_dtls_update = "cmd1*cmd2*cmd3*cmd4*cmd5*cmd6*cmd7*cmd8*cmd9*cmd10*cmd11*cmd12*cmd13*cmd14*cmd15*cmd16*cmd17*cmd18*cmd19*cmd20*cmd21*cmd22*cmd23*cmd24*updated_by*update_date*status_active*is_deleted";
			$cam_design_dtls_id = return_next_id("id", "subcon_planning_camdesign_dtls", 1);
			$field_array_cam_design_dtls = "id,mst_id,dtls_id,cmd1,cmd2,cmd3,cmd4,cmd5,cmd6,cmd7,cmd8,cmd9,cmd10,cmd11,cmd12,cmd13,cmd14,cmd15,cmd16,cmd17,cmd18,cmd19,cmd20,cmd21,cmd22,cmd23,cmd24,inserted_by,insert_date";

			$came_dsign_string_data = explode(",", $hidden_came_dsign_string_data);
			for ($i = 0; $i < count($came_dsign_string_data); $i++)
			{
				$came_dsign_data = explode("_", $came_dsign_string_data[$i]);
				$came_udpdateId = $came_dsign_data[0];
				$cmd1 = $came_dsign_data[1];
				$cmd2 = $came_dsign_data[2];
				$cmd3 = $came_dsign_data[3];
				$cmd4 = $came_dsign_data[4];
				$cmd5 = $came_dsign_data[5];
				$cmd6 = $came_dsign_data[6];
				$cmd7 = $came_dsign_data[7];
				$cmd8 = $came_dsign_data[8];
				$cmd9 = $came_dsign_data[9];
				$cmd10 = $came_dsign_data[10];
				$cmd11 = $came_dsign_data[11];
				$cmd12 = $came_dsign_data[12];
				$cmd13 = $came_dsign_data[13];
				$cmd14 = $came_dsign_data[14];
				$cmd15 = $came_dsign_data[15];
				$cmd16 = $came_dsign_data[16];
				$cmd17 = $came_dsign_data[17];
				$cmd18 = $came_dsign_data[18];
				$cmd19 = $came_dsign_data[19];
				$cmd20 = $came_dsign_data[20];
				$cmd21 = $came_dsign_data[21];
				$cmd22 = $came_dsign_data[22];
				$cmd23 = $came_dsign_data[23];
				$cmd24 = $came_dsign_data[24];

				if ($came_udpdateId !="")
				{
					$cam_upd_id_arr[] = $came_udpdateId;
					$data_array_came_design_update[$came_udpdateId] = explode("*", ("'".$cmd1."'*'".$cmd2."'*'".$cmd3."'*'".$cmd4."'*'".$cmd5."'*'".$cmd6."'*'".$cmd7."'*'".$cmd8."'*'".$cmd9."'*'".$cmd10."'*'".$cmd11."'*'".$cmd12."'*'".$cmd13."'*'".$cmd14."'*'".$cmd15."'*'".$cmd16."'*'".$cmd17."'*'".$cmd18."'*'".$cmd19."'*'".$cmd20."'*'".$cmd21."'*'".$cmd22."'*'".$cmd23."'*'".$cmd24."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0"));
				}
				else
				{
					if ($data_array_cam_design_dtls != "")
						$data_array_cam_design_dtls .= ",";

					$data_array_cam_design_dtls .= "(".$cam_design_dtls_id.",".$updateId.",".$update_dtls_id.",'".$cmd1."','".$cmd2."','".$cmd3."','".$cmd4."','".$cmd5."','".$cmd6."','".$cmd7."','".$cmd8."','".$cmd9."','".$cmd10."','".$cmd11."','".$cmd12."','".$cmd13."','".$cmd14."','".$cmd15."','".$cmd16."','".$cmd17."','".$cmd18."','".$cmd19."','".$cmd20."','".$cmd21."','".$cmd22."','".$cmd23."','".$cmd24."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$cam_design_dtls_id = $cam_design_dtls_id + 1;
				}
			}
		}
		
		$flag = 1;
		/*
		|--------------------------------------------------------------------------
		| subcon_planning_plan_dtls
		| data deleting
		|--------------------------------------------------------------------------
		|
		*/
		/*
		$rsltDeletePlanDtls = execute_query("DELETE FROM subcon_planning_plan_dtls WHERE dtls_id = ".$update_dtls_id."", 0);
		if ($rsltDeletePlanDtls)
			$flag = 1;
		else
			$flag = 0;
		
		*/
		/*
		|--------------------------------------------------------------------------
		| subcon_planning_dtls
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1)
		{
			$rsltUpdateDtls = sql_update("subcon_planning_dtls", $field_array_dtls, $data_array_dtls, "id", $update_dtls_id, 1);
			if ($rsltUpdateDtls)
				$flag = 1;
			else
				$flag = 0;
		}
		
		/*
		|--------------------------------------------------------------------------
		| subcon_planning_plan_dtls
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1 && $data != '' && $data_array_plan_dtls != '')
		{
			$rsltPlanningPlanDtls = sql_insert("subcon_planning_plan_dtls", $field_array_plan_dtls, $data_array_plan_dtls, 0);
			if ($rsltPlanningPlanDtls)
				$flag = 1;
			else
				$flag = 0;
		}
		
		if ($flag == 1 && !empty($data_array_plan_dtls_update))
		{
			$rsltPlanningPlanDtlsUpdate = execute_query(bulk_update_sql_statement("subcon_planning_plan_dtls", "id", $field_array_plan_dtls_update, $data_array_plan_dtls_update, $colorprog_upd_id_arr));
			if ($rsltPlanningPlanDtlsUpdate)
				$flag = 1;
			else
				$flag = 0;
		}
		/*
		if ($hidden_color_wise_prog_data != "")
		{
			// Color wise 
			//echo "10**";
			//print_r($colorprog_upd_id_arr); die();

			if (count($colorprog_upd_id_arr)>0) { // update 

				if (count($data_array_color_wise_prog_update) > 0) {

					//echo "10**".bulk_update_sql_statement("ppl_color_wise_break_down", "id", $field_array_color_wise_prog_update, $data_array_color_wise_prog_update, $colorprog_upd_id_arr);

					$rID7 = execute_query(bulk_update_sql_statement("ppl_color_wise_break_down", "id", $field_array_color_wise_prog_update, $data_array_color_wise_prog_update, $colorprog_upd_id_arr));

					if ($flag == 1) {
						if ($rID7) $flag = 1; else $flag = 0;
					}
				}
			}
			

			if ($data_array_color_wise_break_down != "") { // new color insert
					//echo "10**insert into ppl_color_wise_break_down (".$field_array_color_wise_break_down.") Values ".$data_array_color_wise_break_down."";die;
				$rID8 = sql_insert("ppl_color_wise_break_down", $field_array_color_wise_break_down, $data_array_color_wise_break_down, 0);
				if ($flag == 1) {
					if ($rID8) $flag = 1; else $flag = 0;
				}
			}
			
		}
		*/

		/*
		|--------------------------------------------------------------------------
		| subcon_planning_machine_dtwise
		| data deleting
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1)
		{
			$rsltDeleteMachineDatewise = execute_query("DELETE FROM subcon_planning_machine_dtwise WHERE dtls_id = ".$update_dtls_id."", 0);
			if ($rsltDeleteMachineDatewise)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_planning_machine_dtls
		| data deleting
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1)
		{
			$rsltDeleteMachineDtls = execute_query("DELETE FROM subcon_planning_machine_dtls WHERE dtls_id = ".$update_dtls_id."", 0);
			if ($rsltDeleteMachineDtls)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_planning_machine_dtls
		| subcon_planning_machine_dtwise
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if ($save_data != "")
		{
			if ($data_array_machine_dtls != "")
			{
				$rsltMachineDtls = sql_insert("subcon_planning_machine_dtls", $field_array_machine_dtls, $data_array_machine_dtls, 0);
				if ($flag == 1)
				{
					if ($rsltMachineDtls)
						$flag = 1;
					else
						$flag = 0;
				}
			}

			if ($data_array_machine_dtls_datewise != "")
			{
				$rsltMachineDatewise = sql_insert("subcon_planning_machine_dtwise", $field_array_machine_dtls_datewise, $data_array_machine_dtls_datewise, 0);
				if ($flag == 1)
				{
					if ($rsltMachineDatewise)
						$flag = 1;
					else
						$flag = 0;
				}
			}
		}
		
		/*
		|--------------------------------------------------------------------------
		| subcon_planning_feeding_dtls
		| data deleting
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1)
		{
			$rsltDeleteCountFeeding = execute_query("DELETE FROM subcon_planning_feeding_dtls WHERE dtls_id=".$update_dtls_id."", 0);
			if($rsltDeleteCountFeeding)
				$flag = 1;
			else
				$flag = 0;
		}
		
		/*
		|--------------------------------------------------------------------------
		| subcon_planning_feeding_dtls
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if($flag == 1 && $data_array_count_feeding_dtls != '')
		{
			$rsltCountFeeding = sql_insert("subcon_planning_feeding_dtls", $field_array_count_feeding_dtls, $data_array_count_feeding_dtls, 0);
			if ($rsltCountFeeding)
				$flag = 1;
			else
				$flag = 0;
		}
		
		/*
		|--------------------------------------------------------------------------
		| subcon_planning_collar_cuff_dtls
		| data preparing for
		| Collar And Cuff Data
		|--------------------------------------------------------------------------
		|
		*/
		$hidden_collarCuff_data = str_replace("'", "", $hidden_collarCuff_data);
		if ($hidden_collarCuff_data != "") 
		{
			$collarCuff_data_delete = execute_query("DELETE FROM subcon_planning_collar_cuff_dtls WHERE dtls_id=".$update_dtls_id."", 0);
			//echo "10**".$collarCuff_data_delete;die;
			if($collarCuff_data_delete)
				$flag = 1;
			else
				$flag = 0;
			
				
			$collar_cuff_dtls_id = return_next_id("id", "subcon_planning_collar_cuff_dtls", 1);
			$field_array_collar_cuff_dtls = "id, mst_id, dtls_id, body_part, grey_size, finish_size, qty_pcs, inserted_by, insert_date";
			$hidden_collarCuff_data = explode(",", $hidden_collarCuff_data);
			for ($i = 0; $i < count($hidden_collarCuff_data); $i++) {
				$collarCuff_wise_data = explode("_", $hidden_collarCuff_data[$i]);
				$body_part = $collarCuff_wise_data[0];
				$grey_size = $collarCuff_wise_data[1];
				$finish_size = $collarCuff_wise_data[2];
				$qty_pcs = $collarCuff_wise_data[3];
				
				
				if ($data_array_collar_cuff_dtls != "") $data_array_collar_cuff_dtls .= ",";

				$data_array_collar_cuff_dtls .= "(" . $collar_cuff_dtls_id . "," . $updateId . "," . $update_dtls_id . ",'" . $body_part . "','" . $grey_size . "','" . $finish_size . "','" . $qty_pcs . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$collar_cuff_dtls_id = $collar_cuff_dtls_id + 1;
			}
		}

		if ($data_array_collar_cuff_dtls != "") {
		//echo "10**insert into subcon_planning_collar_cuff_dtls (".$field_array_collar_cuff_dtls.") Values ".$data_array_collar_cuff_dtls."";die;
			$rID6 = sql_insert("subcon_planning_collar_cuff_dtls", $field_array_collar_cuff_dtls, $data_array_collar_cuff_dtls, 0);
			if ($flag == 1) {
				if ($rID6) $flag = 1; else $flag = 0;
			}
		}
		/*
		|--------------------------------------------------------------------------
		| subcon_planning_camdesign_dtls
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1 && $came_udpdateId != '' && !empty($data_array_came_design_update))
		{
			$rsltUpdateCamDesignDtls = execute_query(bulk_update_sql_statement("subcon_planning_camdesign_dtls", "id", $field_array_cam_design_dtls_update, $data_array_came_design_update, $cam_upd_id_arr));
			if ($rsltUpdateCamDesignDtls)
				$flag = 1;
			else
				$flag = 0;
		}
		
		/*
		|--------------------------------------------------------------------------
		| subcon_planning_camdesign_dtls
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1 && $data_array_cam_design_dtls != '')
		{
			$rsltCamDesignDtls = sql_insert("subcon_planning_camdesign_dtls", $field_array_cam_design_dtls, $data_array_cam_design_dtls, 0);
			if ($rsltCamDesignDtls)
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
		$update_dtls_id = str_replace("'", "", $update_dtls_id);
		
		/*
		|--------------------------------------------------------------------------
		| subcon_planning_dtls
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		
		$field_array_update = "status_active*is_deleted*updated_by*update_date";
		$data_array_update = "0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rsltUpdateDtls = sql_update("subcon_planning_dtls", $field_array_update, $data_array_update, "id", $update_dtls_id, 1);
		
		if ($rsltUpdateDtls)
			$flag = 1;
		else
			$flag = 0;

		/*
		|--------------------------------------------------------------------------
		| subcon_planning_plan_dtls
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1)
		{
			$rsltPlanningPlanDtls = sql_update("subcon_planning_plan_dtls", $field_array_update, $data_array_update, "dtls_id", $update_dtls_id, 1);
			if ($rsltPlanningPlanDtls)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_planning_machine_dtls
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1)
		{
			$rsltMachineDtls = sql_update("subcon_planning_machine_dtls", $field_array_update, $data_array_update, "dtls_id", $update_dtls_id, 1);
			if ($rsltMachineDtls)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| subcon_planning_machine_dtwise
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag == 1)
		{
			$rsltMachineDatewise = sql_update("subcon_planning_machine_dtwise", $field_array_update, $data_array_update, "dtls_id", $update_dtls_id, 1);
			if ($rsltMachineDatewise)
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
		else if ($db_type == 2 || $db_type == 1)
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
| actn_color
|--------------------------------------------------------------------------
|
*/
if ($action == "actn_color")
{
	echo load_html_head_contents("Color Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		$(document).ready(function (e){
			setFilterGrid('tbl_list_search', -1);
            //set_all();
        });

		var selected_id = new Array();
		var selected_name = new Array();
		//var selected_qnty = new Array();

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

		function js_set_value(str)
		{
			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1)
			{
				selected_id.push($('#txt_individual_id' + str).val());
				selected_name.push($('#txt_individual' + str).val());
				//selected_qnty.push($('#txt_individual_qnty' + str).val());
			}
			else
			{
				for (var i = 0; i < selected_id.length; i++)
				{
					if (selected_id[i] == $('#txt_individual_id' + str).val())
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
				//selected_qnty.splice(i, 1);
			}
			
			var id = '';
			var name = '';
			//var qnty = '';
			for (var i = 0; i < selected_id.length; i++)
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				//qnty += selected_qnty[i] + ',';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			qnty = qnty.substr(0, qnty.length - 1);
			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
			//$('#txt_selected_qnty').val(qnty);
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

				//alert(coloProgUpdateId+'='+txtColorId+'='+txtColorName+'='+txtColorProgQty+'='+hidden_color_allowed_qty+'='+hidden_color_prev_prog_qty+'='+txt_individual_color_blqty);
				//78=2=WHITE=5000=NaN=0=NaN
				
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
							<!-- not used -->
                            <input type="hidden" name="txt_selected_qnty" id="txt_selected_qnty" value=""/>
						</thead>
					</table>
					<div style="width:600px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="582" class="rpt_table" id="tbl_list_search">
						<tbody>
							<?php
							$color_mixing_in_knittingplan = return_field_value("color_mixing_in_knitting_plan", "variable_settings_production", "company_name = ".$companyId." and variable_list = 53"); 
							if($color_mixing_in_knittingplan==1)
							{
								$color_mixing_in_knittingplan_yes = 1;
							}
							else
							{
								$color_mixing_in_knittingplan_yes = 0;
							}
							
							//$hidden_color_id = explode(",", $hidden_color_id);
							$hidden_color_id = explode(",", $colorId);
							if($planId != '')
							{
								$sqlPlan = "
									SELECT
										a.subcon_order_id,
										c.id, c.mst_id, c.dtls_id, c.determination_id, c.gsm_weight, c.dia, c.color_id, c.status_active, SUM(c.program_qnty) AS program_qnty 
									FROM 
										subcon_planning_mst a
										INNER JOIN subcon_planning_dtls b ON a.id = b.mst_id
										INNER JOIN subcon_planning_plan_dtls c ON b.id = c.dtls_id
									WHERE
										a.id IN(".$planId.") 
										AND b.status_active = 1 
										AND b.is_deleted = 0
										AND c.is_revised=0
									GROUP BY 
										a.subcon_order_id,
										c.id, c.mst_id, c.dtls_id, c.determination_id, c.gsm_weight, c.dia, c.color_id, c.status_active
								";
								//echo $sqlPlan;
								$resultPlan = sql_select($sqlPlan);
								$programDataArr = array();	
								$planIdArr = array();
								$progNoArr = array();
								$color_prog_data = array();
								foreach ($resultPlan as $row)
								{
									$color_plan_data[$planId][$row[csf('color_id')]]['color_prog_qty_total'] += $row[csf('program_qnty')];
									//not used
									$color_plan_data[$planId][$row[csf('color_id')]]['colo_prog_update_id'] = $row[csf('id')];
									$color_plan_data[$planId][$row[csf('color_id')]]['program_no'] = $row[csf('dtls_id')];
									$color_prog_data[$planId][$row[csf('dtls_id')]][$row[csf('color_id')]]['color_prog_qty'] = $row[csf('program_qnty')];
									$color_prog_data[$planId][$row[csf('dtls_id')]][$row[csf('color_id')]]['colo_prog_update_id'] = $row[csf('id')];
								}
								unset($resultPlan);
								//echo "<pre>";
								//print_r($color_plan_data); die;
							}

							$color_library = get_color_array();
							$sql = "
								SELECT 
									color_id AS fabric_color_id, SUM(qnty) AS qnty 
								FROM 
									subcon_ord_breakdown 
								WHERE 
									id IN( ".$orderBrkDownId.") 
									AND mst_id IN(".$orderMstId.") 
									AND order_id IN(".$orderDtlsId.") 
								GROUP BY color_id";
								
							//echo $sql;
							$i = 1;
							$tot_qnty = 0;
							$result = sql_select($sql);							
							foreach ($result as $row) 
							{
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
	
								$tot_qnty += $row[csf('qnty')];
	
								if (in_array($row[csf('fabric_color_id')], $hidden_color_id))
								{
									if ($color_row_id == "")
										$color_row_id = $i;
									else
										$color_row_id .= "," . $i;
								}

								//$colo_prog_update_id = $color_plan_data[$planId][$row[csf('fabric_color_id')]]['colo_prog_update_id']; 
								$colo_prog_update_id = $color_prog_data[$planId][$prog_no][$row[csf('fabric_color_id')]]['colo_prog_update_id']; 
								$color_prog_qty = $color_prog_data[$planId][$prog_no][$row[csf('fabric_color_id')]]['color_prog_qty'];
								$color_total_prog_qty = $color_plan_data[$planId][$row[csf('fabric_color_id')]]['color_prog_qty_total']; 
								$blance = ($row[csf('qnty')]-($color_total_prog_qty));
								$previous_color_prog_qty = ($color_total_prog_qty-$color_prog_qty);
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)"> 
									<td width="40" align="center"><? echo $i; ?>								
										<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" 
                                        value="<? echo $row[csf('fabric_color_id')]; ?>"/>
										<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>"
										value="<? echo $color_library[$row[csf('fabric_color_id')]]; ?>"/>
										<input type="hidden" name="colo_prog_update_id[]"
										id="colo_prog_update_id_<?php echo $i; ?>"
										value="<? echo  $update_id= ($colo_prog_update_id!="")?$colo_prog_update_id:"0"; ?>"/>
									</td>
									<td width="160">
										<p><? echo $color_library[$row[csf('fabric_color_id')]]; ?></p>
										<input type="hidden" name="text_colorid_[]" id="text_colorid_<? echo $i;?>" 
                                        value="<? echo $row[csf('fabric_color_id')]; ?>"/>
										<input type="hidden" name="text_color_name_[]" id="text_color_name_<? echo $i;?>" 
                                        value="<? echo $color_library[$row[csf('fabric_color_id')]]; ?>"/>
									</td>
									<td width="80" align="right">
										<? echo number_format($row[csf('qnty')], 2); ?> 
										<input type="hidden" name="hidden_color_allowed_qty[]" id="hidden_color_allowed_qty<? echo $i;?>" 
                                        value="<? echo number_format($row[csf('qnty')], 2,'.',''); ?>"/>
									</td>
									<td width="80" align="right">
										<input type="text" name="text_color_prog_qty[]" id="text_color_prog_qty_<? echo $i;?>" 
                                        value="<? echo  $text_color_prog_qty= ($color_prog_qty>0)?$color_prog_qty:""; ?>" 
                                        style="max-width: 80px; text-align: center;" placeholder="Write"/>
									</td>
									<td width="80" align="right">
										<p><? echo $text_prev_qty = ($previous_color_prog_qty>0)?number_format(($previous_color_prog_qty),2):"0"; ?></p>
										<input type="hidden" name="hidden_color_prev_prog_qty[]" id="hidden_color_prev_prog_qty_<? echo $i;?>" 
                                        value="<? echo $text_prev_qty = ($previous_color_prog_qty>0)?number_format(($previous_color_prog_qty),2,'.',''):"0"; ?>"/>
									</td>
									<td align="right"><p><? echo $balanceQty = ($blance>0)?number_format($blance ,2):"0" ; ?></p> 
									<input type="hidden" name="txt_individual_color_blqty[]"
									id="txt_individual_color_blqty<?php echo $i; ?>"
									value="<? echo $balanceQty = ($blance>0)?number_format($blance ,2,'.',''):"0" ; ?>"/>
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
| actn_machine
|--------------------------------------------------------------------------
|
*/

if ($action == "actn_machine")
{
	echo load_html_head_contents("Machine Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<style type="text/css">
		.highlight {
			background: #2e9500;
		}

		.highlight a {
			background-color: #42B373 !important;
			background-image :none !important;
			color: #ffffff !important;
			opacity: 0.7;
		}

		.program_calendar {
			height: 18px;
			font-size: 11px;
			line-height: 16px;
			padding: 0 5px;
			text-align:left;
			border: 1px solid #676767;
			border-radius: 3px;
			border-radius: .5em;
		}
	</style>
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
			var min_date = '';
			var max_date = '';
			var hidden_prog_qnty = $('#hidden_prog_qnty').val();
			var tot_row = $("#tbl_list_search tbody tr").length - 1;

			for (var i = 1; i <= tot_row; i++)
			{
				var machineId = $('#txt_individual_id' + i).val();
				var machineNo = $('#txt_individual' + i).val();
				var capacity = $('#txt_capacity_' + i).val();
				var distributionQnty = $('#txt_distribution_qnty_' + i).val();
				var noOfDays = $('#txt_noOfDays_' + i).val();
				var startDate = $('#txt_startDate_' + i).val();
				var endDate = $('#txt_endDate_' + i).val();
				var dtls_id = $('#dtls_id_' + i).val();
				var hidd_dia_id = $('#hidd_dia_id_' + i).val();
				var hidd_gg_id = $('#hidd_gg_id_' + i).val();
				

				if (distributionQnty * 1 > 0)
				{
					if (save_string == "")
					{
						save_string = machineId + "_" + machineNo + "_" + capacity + "_" + distributionQnty + "_" + noOfDays + "_" + startDate + "_" + endDate + "_" + dtls_id;
						allMachineId = machineId;
						allMachineDiaId = hidd_dia_id;
						allMachineGgId = hidd_gg_id;
						allMachineNo = machineNo;
					}
					else
					{
						save_string += "," + machineId + "_" + machineNo + "_" + capacity + "_" + distributionQnty + "_" + noOfDays + "_" + startDate + "_" + endDate + "_" + dtls_id;
						allMachineId += "," + machineId;
						allMachineNo += "," + machineNo;
						allMachineDiaId += "," + hidd_dia_id;
						allMachineGgId += "," + hidd_gg_id;
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

			if(tot_distribution_qnty > hidden_prog_qnty)
			{
				alert("Distribution quantity can not be greater than Program quantity");
				return;
			}
			else
			{
				$('#hidden_machine_id').val(allMachineId);
				$('#hidden_machine_no').val(allMachineNo);
				$('#save_string').val(save_string);
				$('#hidden_machine_capacity').val(tot_capacity);
				$('#hidden_distribute_qnty').val(tot_distribution_qnty);
				$('#hidden_min_date').val(min_date);
				$('#hidden_max_date').val(max_date);

				$('#hidden_machine_dia').val(allMachineDiaId);
				$('#hidden_machine_gg').val(allMachineGgId);
			}

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
				var days_req = $('#txt_noOfDays_' + row_no).val();

				days_req = Math.ceil(days_req);
				if (days_req > 0)
				{
					days_req = days_req - 1;
					$("#txt_endDate_" + row_no).val(add_days($('#txt_startDate_' + row_no).val(), days_req));
				}

				var txt_startDate = $('#txt_startDate_' + row_no).val();
				var txt_endDate = $('#txt_endDate_' + row_no).val();
				var machine_id = $('#txt_individual_id' + row_no).val();

				var data = machine_id + "**" + txt_startDate + "**" + txt_endDate + "**" + '<? echo $update_dtls_id; ?>';
				var response = return_global_ajax_value(data, 'actn_date_duplication_check', '', 'inbound_subcontract_program_controller');
				var response = response.split("_");
                //alert(response);return;
                if (response[0] != 0)
				{
                	alert("Date Overlaping for this machine. Dates Are (" + response[1] + ").");
                	$('#txt_startDate_' + row_no).val('');
                	$('#txt_endDate_' + row_no).val('');
                	return;
                }
            }
        }

        function calculate_noOfDays(row_no)
		{
        	var distribute_qnty = $('#txt_distribution_qnty_' + row_no).val();
        	var machine_capacity = $('#txt_capacity_' + row_no).val();

        	var days_req = distribute_qnty * 1 / machine_capacity * 1;
        	$('#txt_noOfDays_' + row_no).val(days_req.toFixed(2));

        	if (distribute_qnty * 1 > 0)
			{
        		fn_add_date_field(row_no);
        	}
        	else
			{
        		$('#txt_noOfDays_' + row_no).val('');
        		$('#txt_startDate_' + row_no).val('');
        		$('#txt_endDate_' + row_no).val('');
        	}
        }

        // declare bookedDays global
        var bookedDays = [];
		// perform initial json request for free days
		fn_machine_book_dates();

		$(document).ready(function(){
			// fairly standard configuration, importantly containing beforeShowDay and onChangeMonthYear custom methods
			$('.program_calendar').datepicker({
				dateFormat: 'dd-mm-yy',
				changeMonth: true,
				changeYear: true,
				beforeShowDay:highlightDays,
				onChangeMonthYear: fn_machine_book_dates
			});
		});


		function fn_machine_book_dates(row_no)
		{
			var machine_id = $('#txt_individual_id' + row_no).val();

			if(machine_id!="" && machine_id!="undefined")
			{
				var update_dtls_id = '<? echo $update_dtls_id; ?>';
				var data ={"machine_id":machine_id,"update_dtls_id":update_dtls_id}

				$.ajax({
					type: "POST",
					url: "inbound_subcontract_program_controller.php?action=machine_allready_book_dates",
					data: data,
					cache: false,
					dataType: "json",
					success: function(response_data){
						$.each(response_data, function(index, value) {
							if (value!= "")
							{
	  							bookedDays.push(value); // add this date to the bookedDays array
	  						}
	  					});
					}
				})
			}
		}

		function highlightDays(date)
		{
			for (var i = 0; i < bookedDays.length; i++)
			{
				if (bookedDays[i] == $.datepicker.formatDate('dd-mm-yy', date))
				{
					return [true, 'highlight', 'All ready book this date'];
				}
			}
			return [true,''];
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
				<input type="hidden" name="hidden_machine_capacity" id="hidden_machine_capacity" class="text_boxes" value="">
				<input type="hidden" name="hidden_distribute_qnty" id="hidden_distribute_qnty" class="text_boxes" value="">
				<input type="hidden" name="hidden_min_date" id="hidden_min_date" class="text_boxes" value="">
				<input type="hidden" name="hidden_max_date" id="hidden_max_date" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_dia" id="hidden_machine_dia" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_gg" id="hidden_machine_gg" class="text_boxes" value="">
				<input type="hidden" name="hidden_prog_qnty" id="hidden_prog_qnty" class="text_boxes" value="<? echo $txt_program_qnty;?>">
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
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search">
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
                        $dtls_id = $machine_wise_data[7];

                        $qnty_array[$machine_id]['capacity'] = $capacity;
                        $qnty_array[$machine_id]['distribution'] = $distribution_qnty;
                        $qnty_array[$machine_id]['noOfDays'] = $noOfDays;
                        $qnty_array[$machine_id]['startDate'] = $startDate;
                        $qnty_array[$machine_id]['endDate'] = $endDate;
                        $qnty_array[$machine_id]['dtls_id'] = $dtls_id;
                    }

                    $floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

                    if($txt_machine_gg!="")
                    {
                        $machinCond = "and gauge='$txt_machine_gg'";
                    }


                    $sql = "select id, machine_no, dia_width, gauge, machine_group, prod_capacity, floor_id from lib_machine_name where company_id=$companyId and category_id=1 and status_active=1 and is_deleted=0 $machinCond order by seq_no";// and dia_width='$txt_machine_dia'
                    //echo $sql;
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

                        if ($distribution_qnty > 0) $bgcolor = "yellow"; else $bgcolor = $bgcolor;

                        $noOfDays = $qnty_array[$row[csf('id')]]['noOfDays'];
                        $startDate = $qnty_array[$row[csf('id')]]['startDate'];
                        $endDate = $qnty_array[$row[csf('id')]]['endDate'];
                        $dtls_id = $qnty_array[$row[csf('id')]]['dtls_id'];

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
                        <td width="60" align="center"><p><? echo $row[csf('dia_width')]; ?>
						<input type="hidden" name="hidd_dia_id[]" id="hidd_dia_id_<? echo $i; ?>"
                            value="<? echo $row[csf('dia_width')]; ?>" disabled="disabled"/>
							
					</p></td>
                        <td width="60" align="center"><p><? echo $row[csf('gauge')]; ?>
						<input type="hidden" name="hidd_gg_id[]" id="hidd_gg_id_<? echo $i; ?>"
                            value="<? echo $row[csf('gauge')]; ?>" disabled="disabled"/>
					</p></td>
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
                            class="program_calendar" style="width:67px" value="<? echo $startDate; ?>"
                            onChange="fn_add_date_field(<? echo $i; ?>);" onClick="fn_machine_book_dates(<? echo $i; ?>)"/>
                            <!-- onChange="fn_add_date_field(<? //echo $i; ?>);" -->
                        </td>
                        <td align="center">
                            <input type="text" name="txt_endDate[]" id="txt_endDate_<? echo $i; ?>"
                            class="datepicker" style="width:67px" value="<? echo $endDate; ?>"
                            disabled="disabled"/>
                            <input type="hidden" name="dtls_id[]" id="dtls_id_<? echo $i; ?>"
                            value="<? echo $dtls_id; ?>" disabled="disabled"/>
                        </td>
                    </tr>
                    <?
                    $i++;
                }
					?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th width="380" colspan="6" align="right"><b>Total</b></th>
                            <th width="90" align="center"><input type="text" name="txt_total_capacity" id="txt_total_capacity" class="text_boxes_numeric" style="width:75px" readonly disabled="disabled" value="<? echo $tot_capacity; ?>"/></th>
                            <th width="90" align="center"><input type="text" name="txt_total_distribution_qnty" id="txt_total_distribution_qnty" class="text_boxes_numeric" style="width:75px" readonly disabled="disabled" value="<? echo $tot_distribution_qnty; ?>"/></th>
                            <th width="60">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <table width="800" id="tbl_close">
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| actn_date_duplication_check
|--------------------------------------------------------------------------
|
*/
if ($action == "actn_date_duplication_check")
{
	$data = explode("**", $data);
	$machine_id = $data[0];
	if ($db_type == 0)
	{
		$startDate = change_date_format(trim($data[1]), "yyyy-mm-dd", "");
		$endDate = change_date_format(trim($data[2]), "yyyy-mm-dd", "");
	}
	else
	{
		$startDate = change_date_format(trim($data[1]), '', '', 1);
		$endDate = change_date_format(trim($data[2]), '', '', 1);
	}
	$update_dtls_id = $data[3];

	if ($update_dtls_id == "")
	{
		$sql = "SELECT distribution_date, SUM(days_complete) AS days_complete FROM subcon_planning_machine_dtwise WHERE machine_id='".$machine_id."' AND distribution_date BETWEEN '".$startDate."' AND '".$endDate."' GROUP BY distribution_date ORDER BY distribution_date";
	}
	else
	{
		$sql = "SELECT distribution_date, SUM(days_complete) AS days_complete FROM subcon_planning_machine_dtwise WHERE machine_id='".$machine_id."' AND distribution_date BETWEEN '".$startDate."' AND '".$endDate."' AND dtls_id<>".$update_dtls_id." GROUP BY distribution_date ORDER BY distribution_date";
	}
	//echo $sql;die;
	$data_array = sql_select($sql);
	$data = '';
	if (count($data_array) > 0)
	{
		foreach ($data_array as $row)
		{
			if ($row[csf('days_complete')] >= 1)
			{
				if ($data == '')
					$data = change_date_format($row[csf('distribution_date')]);
				else
					$data .= "," . change_date_format($row[csf('distribution_date')]);
			}
		}

		if ($data == '')
			echo "0_";
		else
			echo "1" . "_" . $data;
	}
	else
	{
		echo "0_";
	}

	exit();
}

/*
|--------------------------------------------------------------------------
| print
|--------------------------------------------------------------------------
|
*/
if ($action == "print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_id = $data[0];
	$program_id = $data[1];
	$path = $data[2];
	//echo $program_id; die;
	echo load_html_head_contents("Program Qnty Info", $path, 1, 1, '', '', '');

	//$company_details = return_library_array("select id,company_name from lib_company where id=".$company_id."", "id", "company_name");
	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$country_arr = return_library_array("select id, country_name from lib_country where status_active=1 and is_deleted=0", 'id', 'country_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 order by yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

	if($program_id!="")
	{
		$sqlPlan = "
			SELECT 
				spm.buyer_id AS party_id, spm.gsm_weight,
				spd.knitting_source, spd.knitting_party, spd.color_id, spd.color_range, spd.machine_dia, spd.width_dia_type, spd.machine_gg, spd.fabric_dia, spd.program_qnty, spd.stitch_length, spd.spandex_stitch_length, spd.draft_ratio, spd.machine_id, spd.machine_capacity, spd.distribution_qnty, spd.status, spd.start_date, spd.end_date, spd.program_date, spd.feeder, spd.remarks, spd.save_data, spd.location_id, spd.advice,
				sppd.id, sppd.mst_id, sppd.dtls_id, sppd.determination_id, sppd.gsm_weight, sppd.dia, sppd.buyer_id, sppd.fabric_desc,spd.yarn_details_breakdown,
				sod.order_no,sod.cust_buyer,sod.cust_style_ref 
			FROM 
				subcon_planning_mst spm
				INNER JOIN subcon_planning_dtls spd ON spm.id = spd.mst_id 
				INNER JOIN subcon_planning_plan_dtls sppd ON spd.id = sppd.dtls_id
				INNER JOIN subcon_ord_dtls sod ON sppd.po_id = sod.id
				
			WHERE
				spd.id=".$program_id."
		";
		//echo $sqlPlan;
		$planData=sql_select($sqlPlan);
	}
	//echo "<pre>";
	//print_r($planData); die;
	$location_id = $planData[0][csf('location_id')];
	$com_dtls = fnc_company_location_address($company_id, $location_id, 2);
	//echo $location_id;die;
	//echo '<pre>';print_r($com_dtls);

	$product_details_array = array();
	//$sql = "SELECT id, supplier_id, lot, current_stoclocation_idk, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0";
	$sql = "SELECT id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand FROM product_details_master WHERE item_category_id=1 AND company_id=".$company_id." AND status_active=1 AND is_deleted=0 AND id IN(SELECT prod_id FROM ppl_yarn_requisition_entry WHERE knit_id='".$program_id."' AND status_active=1 AND is_deleted=0)";
	$result = sql_select($sql);
	foreach ($result as $row)
	{
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
	//echo "<pre>";
	//print_r($product_details_array);

	$sql_machin = "SELECT dtls_id, machine_id, SUM(distribution_qnty) AS distribution_qnty FROM subcon_planning_machine_dtls WHERE status_active=1 AND is_deleted=0 AND dtls_id IN($program_id) GROUP BY dtls_id, machine_id ORDER BY machine_id";
	$machine_datas = sql_select($sql_machin);
	$machineData = array();
	foreach ($machine_datas as $mcrow)
	{
		$machineData[$mcrow[csf('dtls_id')]][$mcrow[csf('machine_id')]] = $mcrow[csf('distribution_qnty')];
	}

	?>
	<div style="width:1000px;">
		<style>
			table, th, td {
				/*border-bottom:1px solid black;*/
				border-collapse: collapse;
			}
		</style>
		<div style="width:100%;">
			<table class="rpt_table"  style="border-bottom:1px solid black;" align="center">
				<tr>
					<td width="65%" align="center" style="font-size: 16px; font-family: arial; font-weight: bolder;"><? echo $com_dtls[0]; ?></td>
					<td width="12%">&nbsp;</td>
					<td width="23%">&nbsp;</td>
				</tr>
				<tr>
					<td align="center"><b><?php	echo $com_dtls[1]; ?></b></td>
					<td>&nbsp;</td>
					<td align="right"  id="barcode_img_id_1<?php //echo $x; ?>" height="50"></td>

 
				</tr>
				<tr>
					<td align="center" style="font-size: 20px; font-weight: bold;">Knitting Program Slip</td>
                    <td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="border-top: 1px solid black; border-left: 1px solid black; padding-left: 5px;"><b>Program No</b></td>
					<td style="border-top: 1px solid black; border-right: 1px solid black;"><b>: <? echo $program_id;?></b></td>
				</tr>
                <tr>
					<td align="center">&nbsp;</td>
					<td style="border-left: 1px solid black; padding-left: 5px;"><b>Order No</b></td>
					<td style="border-right: 1px solid black;"><b>: <? echo $planData[0][csf('order_no')];?></b></td>
				</tr>
				 <tr>
					<td align="center">&nbsp;</td>
					<td style="border-left: 1px solid black; padding-left: 5px;"><b>Cust Buyer</b></td>
					<td style="border-right: 1px solid black;"><b>: <? echo $planData[0][csf('cust_buyer')];?></b></td>
				</tr>
				 <tr>
					<td align="center">&nbsp;</td>
					<td style="border-left: 1px solid black; padding-left: 5px;"><b>Cust Style Ref</b></td>
					<td style="border-right: 1px solid black;"><b>: <? echo $planData[0][csf('cust_style_ref')];?></b></td>
				</tr>
			</table>
			<br><br>
			<table class="rpt_table" style="float: left;font-weight: bold; margin-bottom:25px;">
				<tr>
					<td colspan="3"><b>Attention- Knitting Manager</b></td>
					<td width="200" style="border:1px solid black;text-align:center;" align="right">Program Date</td>
                </tr>
				<tr>
					<td width="40" style="padding:0px 10px 0px 20px;">Factory</td>
					<td style="border-bottom: 1px solid black; font-size: 20px;">: <?php echo $company_details[$planData[0][csf('knitting_party')]]; ?></td>
					<td width="10">&nbsp;</td>
					<td style="border:1px solid black;text-align:center;"><b><?php echo change_date_format($planData[0][csf('program_date')]);?></b></td>
				</tr>
				<tr>
					<td style="padding:0px 10px 0px 20px;">Address</td>
					<td style="border-bottom: 1px solid black;"><? echo ": ".$com_dtls[1]; ?></td>
					<td>&nbsp;</td>
					<td style="border:1px solid black;text-align:center;">Target Date of Completion</td>
				</tr>
				<tr>
					<td style="padding:0px 10px 0px 20px;">Party</td>
					<td>: <? echo $buyer_arr[$planData[0][csf('party_id')]]; ?>
					</td>
					<td>&nbsp;</td>
					<td style="border:1px solid black;text-align:center;"><? echo change_date_format($planData[0][csf('end_date')]);?></td>
				</tr>
			</table>
		
			<br>
			<table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="50">MC. No/SL</th>
						<th width="100">Buyer</th>
						<th width="150">Fabric Description</th>
						<th width="150">Yarn Details</th>
						<th width="100">Yarn Qnty</th>
						<th width="100">Garments Color</th>
						<th width="50">MC Dia & Gauge</th>
						<th width="50">Fin Dia</th>
						<th width="50">Fin GSM</th>
						<th width="50">SL</th>
						<th width="50">Colour Range</th>
						<th width="80">Program Qty.</th>
						<th width="50">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$total_distribution_qty = 0;
					//$fabric_arr = explode(",",$planData[0][csf('fabric_desc')]);
					$machine_idarr = explode(",", $planData[0][csf("machine_id")]);
					

					$prog_distriqty = 0;
					foreach ($machine_idarr as $machineid)
					{
						$distributionQnty = $machineData[$planData[0][csf("dtls_id")]][$machineid];
						if($distributionQnty>0)
						{
							$prog_distriqty = $distributionQnty;
						}
						else
						{
							$prog_distriqty = $planData[0][csf("program_qnty")];
						}

						if($machineid != "")
						{
							$machineSl = $machine_arr[$machineid];
						}
						else
						{
							$machineSl = 1;
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $machineSl; ?></td>
							<td align="center"><? echo $buyer_arr[$planData[0][csf("buyer_id")]]; ?></td>
							<td align="center"><? echo $planData[0][csf('fabric_desc')]; ?></td>
							<td align="center"><? 
								$yarnDetails=explode("__",$planData[0][csf('yarn_details_breakdown')]);
								foreach ($yarnDetails as $dataRow)
								{
									$y_Data=explode("_",$dataRow);
									$y_Datas=$count_arr[$y_Data[0]].",".$y_Data[1].",".$y_Data[2];
									$dataRows=str_replace("_",",",$dataRow);
									echo $y_Datas."<br/>-------------------------<br/>"; 
								}
								?></td>
							<td align="center"><? 
								$yarnDetails=explode("__",$planData[0][csf('yarn_details_breakdown')]);
								
								foreach ($yarnDetails as $dataRow){
									$y_Data=explode("_",$dataRow);
									echo $y_Data[3]."<br/>-------------------------<br/>"; 
									}
								?>
							</td>

							<td align="center">
								<?
								$color_id_arr = array_unique(explode(",", $planData[0][csf('color_id')]));
								$all_color = "";
								foreach ($color_id_arr as $color_id)
								{
									$all_color .= $color_library[$color_id] . ",";
								}
								$all_color = chop($all_color, ",");
								echo $all_color;
								?>
							</td>
							<td align="center"><? echo $planData[0][csf('machine_dia')] . "X" . $planData[0][csf('machine_gg')]; ?></td>
							<td align="center"><? echo $planData[0][csf('fabric_dia')];?></td>
							<td align="center"><? echo $planData[0][csf('gsm_weight')];?></td>
							<td align="center"><? echo $planData[0][csf('stitch_length')];?></td>
							<td><? echo $color_range[$planData[0][csf('color_range')]]; ?></td>
							<td align="right"><? echo number_format($prog_distriqty, 2); ?></td>
							<td><? echo $planData[0][csf('remarks')]; ?></td>
						</tr>
						<?
						//$total_distribution_qty += $row[csf('distribution_qnty')];
						$total_distribution_qty += $prog_distriqty;

						if($machineid != "")
						{
							$machineSl++;
						}
					}
					?>
					<tfoot>
						<th colspan="11" align="right"><b>Total</b></th>
						<th style="text-align: right;"><? echo number_format($total_distribution_qty, 2); ?></th>
					</tfoot>
				</tbody>
			</table>
			<br>
			<span> Advice:  <? echo $planData[0][csf('advice')]; ?> </span>
        	<div style="width:100%; float:left;padding-top:10px;">
            <?
            $sql_stripe_feeder = sql_select("select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and a.dtls_id=".$program_id." and a.no_of_feeder>0 group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder");
            if (count($sql_stripe_feeder) > 0)
            {
                ?>
                <table style="width:48%; float:left;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th colspan="6" align="center">Stripe Measurement Information:</th>
                        </tr>
                        <tr>
                            <th width="50">SL</th>
                            <th width="100">Combo Color</th>
                            <th width="100">Stripe Color</th>
                            <th width="100">Measurement </th>
                            <th width="50">Uom</th>
                            <th width="100">No Of Feeder</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?
                        $i = 1;
                        $total_feeder = 0;
                        foreach ($sql_stripe_feeder as $row)
                        {
                            if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td width="50" align="center"><? echo $i; ?></td>
                                <td width="100"><? echo $color_library[$row[csf('color_id')]]; ?></td>
                                <td width="100"><? echo $color_library[$row[csf('stripe_color_id')]]; ?></td>
                                <td width="100" align="center">
                                    <?
                                    echo number_format($row[csf('measurement')], 2);
                                    $total_measurement += $row[csf('measurement')];
                                    ?>
                                </td>
                                <td width="50" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                                <td width="100" align="center">
                                    <? echo number_format($row[csf('no_of_feeder')], 0);
                                    $total_feeder += $row[csf('no_of_feeder')]; ?>
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
                        ?>
                    </tbody>

                    <tfoot>
                        <th colspan="3" align="right"><b>Total</b></th>
                        <th style="text-align: center;"><? echo number_format($total_measurement, 0); ?></th>
                        <th>&nbsp;</th>
                        <th style="text-align: center;"><? echo number_format($total_feeder, 0); ?></th>
                    </tfoot>
                </table>
                <?
            }

            //$sql_collar_cuff_dtls = sql_select("select body_part_id, grey_size, finish_size, qty_pcs from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id=$program_id");

             $sql_collar_cuff_dtls = sql_select("select  body_part, grey_size,finish_size, qty_pcs FROM subcon_planning_collar_cuff_dtls WHERE dtls_id=$program_id AND status_active=1");



            if (count($sql_collar_cuff_dtls) > 0)
            {
                ?>
                <table style="width:48%; float:left; margin-left:40px;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th colspan="6" align="center">Collar & Cuff Measurement Information:</th>
                        </tr>
                        <tr>
                            <th width="50">SL</th>
                            <th width="100">Body Part</th>
                            <th width="100">Grey Size</th>
                            <th width="100">Finish Size</th>
                            <th width="50">Qty. Pcs</th>
                            <th width="100">GMT Size</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?
                        $k = 1;
                        $total_cuff_qty = 0;
                        foreach ($sql_collar_cuff_dtls as $cuff_row)
                        {
                            if ($k % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor ; ?>">
                                <td width="50" align="center"><? echo $k; ?></td>
                                <td width="100" align="center"><? echo $body_part[$cuff_row[csf('body_part')]]; ?></td>
                                <td width="100" align="center"><? echo $cuff_row[csf('grey_size')]; ?></td>
                                <td width="100" align="center"><? echo $cuff_row[csf('finish_size')]; ?></td>
                                <td width="50" align="right"><? echo number_format($cuff_row[csf('qty_pcs')], 0);?></td>
                                <td width="100">&nbsp;</td>
                            </tr>
                            <?
                            $total_qty_pcs += $cuff_row[csf('qty_pcs')];
                            $k++;
                        }
                        ?>
                    </tbody>

                    <tfoot>
                        <th colspan="4" align="right"><b>Total</b></th>
                        <th align="right"><? echo number_format($total_qty_pcs,0);?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
                <?
            }
            
			
			 $data_collar_cuff = sql_select("SELECT id, mst_id, dtls_id, body_part, grey_size,finish_size, qty_pcs FROM subcon_planning_collar_cuff_dtls WHERE dtls_id='".$program_id."' AND status_active=1 AND is_deleted=0");
            if (count($data_collar_cuff) > 0)
            {
                ?>
                <table style="width:48%; float:left; margin-left:40px;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th colspan="6" align="center">Collar & Cuff Details:</th>
                        </tr>
                        <tr>
                            <th width="50">SL</th>
                            <th width="100">Body Part</th>
                            <th width="80">Grey Size</th>
                            <th width="80">Finish Size</th>
                            <th width="70">Qty. Pcs</th>                        
                        </tr>
                    </thead>

                    <tbody>
                        <?
                        $k = 1;
                        $total_cuff_qty = 0; $total_qty_pcs=0;
                        foreach ($data_collar_cuff as $cuff_row)
                        {
                            if ($k % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor ; ?>">
                                <td width="50" align="center"><? echo $k; ?></td>
                                <td width="100" align="left"><? echo $cuff_row[csf('body_part')]; ?></td>
                                <td width="80" align="center"><? echo $cuff_row[csf('grey_size')]; ?></td>
                                <td width="80" align="center"><? echo $cuff_row[csf('finish_size')]; ?></td>
                                <td width="70" align="right"><? echo number_format($cuff_row[csf('qty_pcs')], 0);?></td>
                               
                            </tr>
                            <?
                            $total_qty_pcs += $cuff_row[csf('qty_pcs')];
                            $k++;
                        }
                        ?>
                    </tbody>

                    <tfoot>
                        <th colspan="4" align="right"><b>Total</b></th>
                        <th align="right"><? echo number_format($total_qty_pcs,0);?></th>
                       
                    </tfoot>
                </table>
                <?
            }
            ?>
            </div>
            <div style="width:100%; float:left; padding-top:20px;">
                <?
                $sql_stripe_colorwise = sql_select("select a.stripe_color_id, a.no_of_feeder,sum(b.fabreqtotkg) as fabreqtotkg , max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and a.dtls_id=$program_id and a.no_of_feeder>0 group by a.stripe_color_id, a.no_of_feeder");
                if (count($sql_stripe_colorwise) > 0)
                {
                    ?>
                    <table style="width:48%; float:left;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
                        <thead>
                            <tr>
                                <th colspan="5" align="center">Colour Wise Quantity</th>
                            </tr>
                            <tr>
                                <th width="100">Stripe Color</th>
                                <th width="100">Measurement</th>
                                <th width="100">UOM</th>
                                <th width="100">Total Feeder</th>
                                <th width="100">Quantity(Kg)</th>
                            </tr>
                        </thead>
                        <tbody>
						<?
                        $y = 1;
                        foreach ($sql_stripe_colorwise as $colorwise_row)
                        {
                            if ($y % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td width="100"><? echo $color_library[$colorwise_row[csf('stripe_color_id')]]; ?></td>
                                <td width="100" align="center"><? echo number_format($colorwise_row[csf('measurement')], 2);?></td>
                                <td width="100" align="center"><? echo $unit_of_measurement[$colorwise_row[csf('uom')]]; ?></td>
                                <td width="100" align="center"><? echo number_format($colorwise_row[csf('no_of_feeder')], 0);?></td>
                                <td width="100" align="right"><? echo number_format($colorwise_row[csf('fabreqtotkg')], 0);?></td>
                            </tr>
                            <?
                            $y++;
                        }
                        ?>
                        </tbody>
                    </table>
                    <?
                }
                ?>
            </div>
			<?
			/*
			|--------------------------------------------------------------------------
			| count feeding information
			|--------------------------------------------------------------------------
			|
			*/
			$sql_count_feed = "SELECT seq_no, count_id, feeding_id FROM subcon_planning_feeding_dtls WHERE dtls_id=".$program_id." AND status_active=1 AND is_deleted=0 ORDER BY seq_no";
            $data_array_count_feed = sql_select($sql_count_feed);
            if(count($data_array_count_feed)>0)
            {
                ?>
                <div style="width:100%; float:left; padding-top:20px;">
                    <table style="width:48%; float:left; margin-left:40px;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
                        <thead>
                            <tr>
                                <th colspan="4" align="center">Count Feeding</th>
                            </tr>
                            <tr>
                                <th width="50">Seq. No</th>
                                <th width="100">Count</th>
                                <th width="100">Feeding</th>
                                <th width="100">Percentage</th>
                            </tr>
                        </thead>
    
                        <tbody>
                            <?
                            $feeding_arr = array(1 => 'Knit', 2 => 'Binding', 3 => 'Loop');
                            $j=1;
                            foreach ($data_array_count_feed as $count_feed_row)
                            {
                                if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                                ?>
                                <tr>
                                    <td width="50" align="center"><? echo $count_feed_row[csf('seq_no')]; ?></td>
                                    <td width="100" align="center"><? echo $count_arr[$count_feed_row[csf('count_id')]];?></td>
                                    <td width="100"><? echo $feeding_arr[$count_feed_row[csf('feeding_id')]];?></td>
                                    <td width="100">&nbsp;</td>
                                </tr>
                                <?
                                $j++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?
            }
			
			/*
			|--------------------------------------------------------------------------
			| cam design information
			|--------------------------------------------------------------------------
			|
			*/
            $sql_cam_design = "SELECT id,cmd1, cmd2, cmd3, cmd4, cmd5, cmd6, cmd7, cmd8, cmd9, cmd10, cmd11, cmd12, cmd13, cmd14, cmd15, cmd16, cmd17, cmd18, cmd19, cmd20, cmd21, cmd22, cmd23, cmd24 FROM subcon_planning_camdesign_dtls WHERE dtls_id=".$program_id." AND status_active=1 AND is_deleted=0 ORDER BY id";
            $data_cam_design = sql_select($sql_cam_design);
            if (!empty($data_cam_design))
            {
                ?>
            	<div style="width:100%; float:left;padding-top:10px;">
                    <table width="100%" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
                        <thead>
                            <tr>
                                <th colspan="25" align="center">Cam Design Information</th>
                            </tr>
                            <tr>
                                <th width="4%">SL</th>
                                <?
                                for ($i=1; $i<=24; $i++)
                                {
                                    ?>
                                    <th width="4%"><? echo $i; ?></th>
                                    <?
                                }
                                ?>
                            </tr>
                        </thead>
                    </table>
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_came_design">
                        <tbody>
                        <?
                        $sl=1;
                        foreach ($data_cam_design as $row)
                        {
                            if ($sl % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td width="4%" align="center"><? echo $sl; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd1')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd2')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd3')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd4')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd5')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd6')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd7')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd8')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd9')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd10')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd11')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd12')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd13')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd14')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd15')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd16')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd17')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd18')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd19')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd20')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd21')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd22')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd23')]; ?></td>
                                <td width="4%" align="center"><? echo $row[csf('cmd24')]; ?></td>
                            </tr>
                            <?
                            $sl++;
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
				<?
            }
            ?>
            <div style="width:100%; float:left; padding-top:20px;">
                <table style="width:100%; float:left;" class="rpt_table">
                    <tr>
                        <td rowspan="3" valign="top" width="130">Special Instruction :</td>
                        <td>Any type of fabric faults is not acceptable.(Patta,Sinker/Needle Mark,Loop/Hole,Tara,Fly,Oil Sport )</td>
                    </tr>
                    <tr>
                        <td>Factory must mention the Program Number on the Delivery Challan and Bill/ Invoice.</td>
                    </tr>
                    <tr>
                        <td>Roll marking must be done with Parmanent marker</td>
                    </tr>
                </table>
            </div>


            <div style="width:100%; float:left; padding-top:100px;">
                <table style="width:100%; float:left;">
                    <tr>
                        <td>Received & Accepted by: </td>
                        <td>&nbsp;</td>
                        <td>Prepared By: </td>
                        <td>&nbsp;</td>
                        <td>Authorized Signature: </td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </table>
        </div>
    </div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
			//for gate pass barcode
			function generateBarcode(valuess)
			{
				//var zs = '<?php// echo $x; ?>';
				var value = valuess;
				var btype = 'code39';
				var renderer = 'bmp';
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 2,
					barHeight: 50,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#barcode_img_id_1").html('11');
				value = {code: value, rect: false};
				$("#barcode_img_id_1").show().barcode(value, btype, settings);
			}
			var value = '<? echo $program_id; ?>';
			
			if( value != '')
			{
				generateBarcode('<? echo strtoupper($program_id); ?>');
			}
		</script>
        <div style="page-break-after:always;"></div>
        <?php

}
?>

    
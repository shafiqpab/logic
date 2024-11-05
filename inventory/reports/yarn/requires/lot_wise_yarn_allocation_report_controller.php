<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "")
{
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_buyer")
{
	echo create_drop_down("cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}

if ($action == "generate_report")
{
	$process = array(&$_POST);
	//var_dump($process);die;
	extract(check_magic_quote_gpc($process));

	$search_cond = "";
	$company_id = str_replace("'", "", $cbo_company_name);
	$buyer_id = str_replace("'", "", $cbo_buyer_name);
	$cbo_is_sales = str_replace("'", "", $cbo_is_sales);
	$internal_ref = str_replace("'", "", $txt_internal_ref);
	$booking_no = str_replace("'", "", $txt_booking_no);
	$sales_ord_no = str_replace("'", "", $txt_sales_no);
	$lot = str_replace("'", "", trim($txt_lot_no));
	$cbo_year = str_replace("'", "", $cbo_year_selection);

	if ($buyer_id==0) $buyer_id_cond =""; else $buyer_id_cond =" and c.buyer_name=$buyer_id ";

	if ($cbo_is_sales!=1)
	{
		$is_sales_cond ="  and b.is_sales<>1";
	}
	else
	{
		if ($sales_ord_no=="") $sales_ord_no_cond =""; else $sales_ord_no_cond =" and d.job_no_prefix_num like '%$sales_ord_no%' ";
		$is_sales_cond =" and b.is_sales=1";
	}

	if ($internal_ref=="") $internal_ref_cond =""; else $internal_ref_cond =" and d.grouping like '%$internal_ref%' ";
	if ($internal_ref=="") $internal_ref_cond2 =""; else $internal_ref_cond2 =" and c.grouping like '%$internal_ref%' ";
	if ($booking_no=="") $booking_no_cond =""; else $booking_no_cond =" and b.booking_no like '%$booking_no%' ";

	if ($lot=="") $lot_cond =""; else $lot_cond =" and a.lot='$lot' ";


	if(trim($cbo_year)!=0)
	{
		if($sales_ord_no=="")
		{
			if($db_type==0) $year_cond=" AND YEAR(c.insert_date) = ".$cbo_year;
			else if($db_type==2) $year_cond=" AND TO_CHAR(C.INSERT_DATE,'YYYY') = ".$cbo_year;
			else $year_cond="";
		}
		else
		{
			if($db_type==0) $year_cond=" AND YEAR(d.insert_date) = ".$cbo_year;
			else if($db_type==2) $year_cond=" AND TO_CHAR(d.INSERT_DATE,'YYYY') = ".$cbo_year;
			else $year_cond="";
		}
	}
	else
	{
		$year_cond="";
	}


	if ($db_type == 0) {
		if ($from_date != "" && $to_date != ""){
			$allocation_date_cond = "and b.allocation_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
		}

	}
	else {
		if ($from_date != "" && $to_date != ""){
			$allocation_date_cond .= " and b.allocation_date  between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
		}
	}

    //library array
	$company_name = return_field_value("company_name", "lib_company", "id=$company_id and status_active=1 and is_deleted=0","company_name");
	$company_short_name = return_field_value("company_short_name", "lib_company", "id=$company_id and status_active=1 and is_deleted=0","company_short_name");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
	$yarn_count_library=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$composition_library=return_library_array( "select id,composition_name from lib_composition_array", "id", "composition_name"  );

    if($cbo_is_sales!=1)
    {
    	$allocation_sql = "SELECT a.id as prod_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_type, a.supplier_id, a.lot, b.booking_no, sum(b.qnty) allocated_qnty, b.allocation_date, b.job_no, c.buyer_name, d.id po_id, d.po_number, d.pub_shipment_date,d.grouping from inv_mat_allocation_dtls_log b,wo_po_break_down d,wo_po_details_master c,product_details_master a where c.company_name=$company_id $buyer_id_cond $internal_ref_cond $booking_no_cond $lot_cond $allocation_date_cond and b.po_break_down_id=d.id and b.job_no=c.job_no and a.id=b.item_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in (1,3) and d.is_deleted=0 group by a.id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_type, a.supplier_id, a.lot, b.booking_no, d.id, d.po_number, d.pub_shipment_date,d.grouping, b.allocation_date, b.job_no, c.buyer_name order by a.id,a.lot, a.yarn_count_id, a.yarn_comp_type1st,a.yarn_type,a.supplier_id,b.booking_no,b.job_no,d.po_number,d.id,d.grouping,b.allocation_date,c.buyer_name asc";
    }
    else
    {
		$allocation_sql =  "SELECT a.id  AS prod_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_type, a.supplier_id, a.lot, b.mst_id, b.booking_no, b.qnty allocated_qnty, b.allocation_date, b.job_no,b.is_sales,d.buyer_id as buyer_name, d.id AS po_id,(case when d.within_group=1 then listagg(c.po_number,',') within group(order by c.id) else CAST( d.job_no_prefix_num AS VARCHAR(200) ) end) AS po_number,(CASE WHEN d.within_group = 1 THEN LISTAGG (c.pub_shipment_date, ',') WITHIN GROUP (ORDER BY c.id) ELSE NULL END)
             AS PUB_SHIPMENT_DATE,(case when d.within_group=1 then listagg(c.grouping,',') within group(order by c.id) else null end) AS GROUPING,d.within_group,d.po_buyer FROM inv_mat_allocation_dtls_log b,fabric_sales_order_mst d left join wo_po_break_down c on d.po_job_no= c.job_no_mst $internal_ref_cond2, product_details_master a WHERE d.company_id = $company_id $booking_no_cond $lot_cond $allocation_date_cond $sales_ord_no_cond $year_cond $is_sales_cond AND b.po_break_down_id = d.id AND b.job_no = d.job_no AND a.id = b.item_id AND a.status_active = 1 AND a.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 GROUP BY a.id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_type, a.supplier_id, a.lot, b.mst_id, b.booking_no,b.qnty, d.id,d.job_no_prefix_num, b.allocation_date, b.job_no,b.is_sales,d.buyer_id,d.within_group,d.po_buyer order by a.id,a.lot, a.yarn_count_id, a.yarn_comp_type1st,a.yarn_type,a.supplier_id, b.mst_id,b.booking_no,b.job_no,d.id,b.allocation_date,d.buyer_id asc";
    }

	//and b.status_active=1 and b.is_deleted=0
	//echo $allocation_sql;die;
	$allocation_result = sql_select($allocation_sql);
	$po_arr=$prod_arr=$rowspan_arr=$allcate_qty=$checked_allocation= array();
	foreach ($allocation_result as $row)
	{
		$po_arr[$row[csf("po_id")]]=$row[csf("po_id")];
		$prod_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];

		if($row[csf("is_sales")]!=1)
		{
			if (isset($rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]))
			{
				$allcate_qty[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]['allocated_qnty']+=$row[csf('allocated_qnty')];

				$rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]+=1;
			}
			else
			{
				$allcate_qty[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]['allocated_qnty']+=$row[csf('allocated_qnty')];

				$rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]=1;
			}
		}
		else
		{

			if( $checked_allocation[$row[csf('mst_id')]]=="" )
			{
				$checked_allocation[$row[csf('mst_id')]] = $row[csf('mst_id')];
				$allcate_qty[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]['allocated_qnty']+=$row[csf('allocated_qnty')];
			}

			$allocation_po_prod[$row[csf('po_id')]][$row[csf('prod_id')]] +=1;

		}

	}

	// Issue qty sql start
	if(!empty($po_arr))
	{
		$array_prod_arr=array_chunk($prod_arr,999);
		foreach($array_prod_arr as $prod_val)
		{
			$prod_var=implode(",",$prod_val);
			$prod_cond=" and c.prod_id in($prod_var)";
		}

		$array_po_arr=array_chunk($po_arr,999);
		foreach($array_po_arr as $po_val)
		{
			$po_var=implode(",",$po_val);
			$order_cond=" and c.po_breakdown_id in($po_var)";
		}

		$sql_issue = "SELECT a.id,a.issue_basis,b.requisition_no,c.po_breakdown_id,c.prod_id, sum(c.quantity) as issue_qty, max(a.issue_date) as max_issue_date from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.issue_purpose in(1,2) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 $order_cond $prod_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by a.id,a.issue_basis,b.requisition_no,c.po_breakdown_id,c.prod_id";
		//echo $sql_issue;

		$result_issue = sql_select($sql_issue);
		$issue_id_arr=array();
		foreach ($result_issue as $row)
		{
			$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_qty")];
			$issue_id_arr[$row[csf("id")]] = $row[csf("id")];
			$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_id"][$row[csf("id")]] = $row[csf("id")];
			$issue_date_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]['max_issue_date'] = $row[csf("max_issue_date")];
		}
	}

	// echo "<pre>";
	// print_r($issue_arr);
	// echo "</pre>";
	// Issue qty sql end

	// Return qty sql start
	$issue_return_req_array = array();
	if(!empty($issue_id_arr))
	{
		$array_issue_id=array_chunk($issue_id_arr,999);
		$issue_cond=" and (";
		foreach($array_issue_id as $issue_id_val)
		{
			$issue_id_var=implode(",",$issue_id_val);
			$issue_cond.=" b.issue_id in($issue_id_var) or ";
		}
		$issue_cond=chop($issue_cond,'or ');
		$issue_cond.=")";

		$sql_return = "SELECT B.ISSUE_ID, C.PO_BREAKDOWN_ID, C.ID, C.PROD_ID, C.QUANTITY FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, ORDER_WISE_PRO_DETAILS C WHERE A.ID=B.MST_ID AND C.TRANS_TYPE=4 AND A.ENTRY_FORM=9 AND B.ID=C.TRANS_ID AND B.ITEM_CATEGORY=1 AND C.ISSUE_PURPOSE IN(1,2) AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND C.STATUS_ACTIVE=1".$prod_cond.$issue_cond;
		$result_return = sql_select($sql_return);
		$duplicate_check = array();
		foreach ($result_return as $row)
		{
			if($duplicate_check[$row['ID']] != $row['ID'])
			{
				$duplicate_check[$row['ID']] = $row['ID'];
				$issue_return_req_array[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']][$row['ISSUE_ID']] += $row['QUANTITY'];
			}
		}
	}
	// Return qty sql end

	?>
	<style type="text/css">
		table tbody tr td{ text-align: center; }
		.right-align{ text-align: right; }
	</style>
	<?
	ob_start();
	?>
	<div id="scroll_body">

		<table width="1943" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
			<thead>
				<tr class="form_caption" style="border:none;">
					<td colspan="21" align="center" style="border:none;font-size:16px; font-weight:bold" >Lot Wise Yarn Allocation Report</td>
				</tr>
				<tr style="border:none;">
					<td colspan="21" align="center" style="border:none; font-size:14px;">Company Name : <? echo $company_name; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="21" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? if ($from_date != "" || $to_date != "") echo "From " . change_date_format($from_date) . " To " . change_date_format($to_date) . ""; ?>
					</td>
				</tr>
				<tr>
					<th width="20">SL</th>
					<th width="120">Product ID</th>
					<th width="120">Lot</th>
					<th width="110">Count</th>
					<th width="130">Composition</th>
					<th width="120">Yarn Type</th>
					<th width="100">Supplier</th>
					<th width="80">Booking No.</th>
					<th width="80">Job NO.</th>
					<th width="80">Internal Ref.</th>
					<th width="100">Shipment date</th>
					<th width="100">Allocation Date</th>
					<th width="80">Allocated Qty</th>
					<th width="80">Issue Qty</th>
					<th width="80">Issue Rtn Qty</th>
					<th width="80">Issue Balance</th>
					<th width="100">Last Issue Date</th>
					<th width="100">Days Passed From Last Issue Date</th>
					<th width="100">Yarn Allocated Age</th>
					<th width="80">Buyer</th>
					<th width="80">Company</th>
				</tr>
			</thead>
		</table>

		<div style="width:1960px; overflow-y:scroll; max-height:250px" id="scroll_body">
			<table width="1940" border="1" cellpadding="2" style="font:'Arial Narrow';"  cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<tbody>
					<?php

					$i = 1;
					$r=1;
					$item_arr = array();
					$issue_print = 0;
					if(!empty($allocation_result))
					{
						foreach ($allocation_result as $allocation_key=>$row)
						{
							$allocation_qnty = $allcate_qty[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]['allocated_qnty'];

							$max_issue_date = $issue_date_array[$row[csf("po_id")]][$row[csf("prod_id")]]['max_issue_date'];
							$ageOfDays = datediff("d", $issue_date_array[$row[csf("po_id")]][$row[csf("prod_id")]]['max_issue_date'], date("Y-m-d"));
							$dpfOfDays = datediff("d", $row[csf("allocation_date")], date("Y-m-d"));


							if($row[csf("is_sales")]==1)
	                        {
	                        	if ( (!empty($checked_arr)) && in_array($row[csf("po_id")]."**".$row[csf("prod_id")], $checked_arr))
	                        	{
	                        		$issue_qnty = 0;
	                        		$issue_ids = array();
	                        		$return_qnty=0;
	                        		$balance = 0;

	                        	}
	                        	else
	                        	{
									//echo $row[csf("po_id")]."==".$row[csf("prod_id")];
	                        		$issue_qnty = $issue_arr[$row[csf("po_id")]][$row[csf("prod_id")]];
	                        		$checked_arr[] = $row[csf("po_id")]."**".$row[csf("prod_id")];
	                        		$issue_ids = $issue_array_req[$row[csf("po_id")]][$row[csf("prod_id")]]["issue_id"];
	                        		$return_qnty=0;
	                        		foreach ($issue_ids as $issue_id) {
	                        			$return_qnty += $issue_return_req_array[$row[csf('po_id')]][$row[csf('prod_id')]][$issue_id];
	                        		}

	                        		$balance = $allocation_qnty - $issue_qnty + $return_qnty;
	                        	}
							}
							else
							{

								$issue_qnty = $issue_arr[$row[csf("po_id")]][$row[csf("prod_id")]];
								$issue_ids = $issue_array_req[$row[csf("po_id")]][$row[csf("prod_id")]]["issue_id"];
								$return_qnty=0;
								foreach ($issue_ids as $issue_id) {
									$return_qnty += $issue_return_req_array[$row[csf('po_id')]][$row[csf('prod_id')]][$issue_id];
								}

								$balance = $allocation_qnty - $issue_qnty + $return_qnty;
							}


	                        if($row[csf("is_sales")]==1)
	                        {
	                        	if($row[csf("within_group")]==1)
	                        	{
	                        		$buyer_id = $row[csf("po_buyer")];
	                        	}else{
	                        		$buyer_id = $row[csf("buyer_name")];
	                        	}
	                        }
	                        else
	                        {
	                        	$buyer_id = $row[csf("buyer_name")];
	                        }


							$bgcolor = ($i % 2 == 0)?"#E9F3FF":"#FFFFFF";
							$stylecolor = ($row["transaction_type"] == 2)? "style='color:#A61000'" : "style='color:#000000'";

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="20"><? echo $i;?></td>
								<td width="120"><? echo $row[csf("Prod_id")];?></td>
								<td width="120"><? echo $row[csf("lot")];?></td>
								<td width="110"><? echo $yarn_count_library[$row[csf("yarn_count_id")]];?></td>
								<td width="130"><? echo $composition_library[$row[csf("yarn_comp_type1st")]];?></td>
								<td width="120"><? echo $yarn_type[$row[csf("yarn_type")]];?></td>
								<td width="100"><? echo $supplier_library[$row[csf("supplier_id")]];?></td>
								<td width="80"><? echo $row[csf("booking_no")];;?></td>
								<td width="80" class="right-align"><? echo $row[csf("job_no")];?></td>
								<td width="80" class="center-align">
									<?
									echo implode(",",array_unique(explode(",",$row[csf("grouping")])));
									?>
								</td>
								<td width="100">
									<?
									echo implode(",",array_unique(explode(",",change_date_format($row[csf("pub_shipment_date")]))));
									?>
								</td>
								<td width="100"><? echo change_date_format($row[csf("allocation_date")]);?></td>
								<td width="80" class="right-align" title="<? echo $row[csf("mst_id")];?>"><? echo number_format($row[csf("allocated_qnty")],2,".",""); $qty +=$row[csf("allocated_qnty")]; ?></td>
								<? if ($r==1)
								{
									?>
									<td width="80" class="right-align" style="vertical-align:middle;" rowspan="<? echo $rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]; ?>"><? echo number_format($issue_qnty,2,".","");?></td>

									<td width="80" class="right-align" style="vertical-align:middle;" rowspan="<? echo $rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]; ?>"><? echo number_format($return_qnty,2,".","");?></td>
									<td width="80" class="right-align" style="vertical-align:middle;" rowspan="<? echo $rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]; ?>"><? echo number_format($balance, 2);?></td>

									<td width="100" style="vertical-align:middle;" rowspan="<? echo $rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]; ?>"><? echo change_date_format($max_issue_date);?></td>
									<td width="100" style="vertical-align:middle;" rowspan="<? echo $rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]; ?>">
									<? if($ageOfDays-1 > 0) echo $ageOfDays-1; ?>
									</td>
									<td width="100" style="vertical-align:middle;" rowspan="<? echo $rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]; ?>">
									<? if($dpfOfDays-1 > 0) echo $dpfOfDays-1; ?>
									</td>
									<?
								}

								if($row[csf("is_sales")]!=1)
	                        	{
									if($rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]==$r) {
										$r=0;
									}

								}else{
									$r=0;
								}


								?>


								<td width="80"><? echo $buyer_library[$buyer_id];?></td>
								<td width="80"><? echo $company_short_name; ?></td>
							</tr>
							<?

							$total_allocation_qnty +=$row[csf("allocated_qnty")];
							$total_issue_qnty += $issue_qnty;
							$total_issue_rtn_qnty += $return_qnty;
							$total_balance += $balance;
							$r++;
							$i++;

						}
					}
					else
					{
						echo "<tr><th colspan='19' style='color:red;'>No Data Found</th></tr>";
					}
					?>
				</tbody>
			</table>
		</div>

		<table width="1943" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
			<tr class="tbl_bottom">
				<td width="20">&nbsp;</td>
				<td width="120">&nbsp;</td>
				<td width="120">&nbsp;</td>
				<td width="110">&nbsp;</td>
				<td width="130">&nbsp;</td>
				<td width="120">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="80">&nbsp;</td>
				<td width="80">&nbsp;</td>
				<td width="80">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100" class="right-align" style="font-weight: bold; font-size:16px;">Total =</td>
				<td width="80" class="right-align" id="value_total_allocation_qty"></td>
				<td width="80" class="right-align" id="value_total_issue_qty"></td>
				<td width="80" class="right-align" id="value_total_issue_return_qty"></td>
				<td width="80" class="right-align" id="value_total_balance"></td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="80">&nbsp;</td>
				<td width="80">&nbsp;</td>
			</tr>
		</table>

	</div>

	<br />
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**1";
	exit();
}

if ($action == "generate_report_2")
{
	$process = array(&$_POST);
	//var_dump($process);die;
	extract(check_magic_quote_gpc($process));

	$search_cond = "";
	$company_id = str_replace("'", "", $cbo_company_name);
	$buyer_id = str_replace("'", "", $cbo_buyer_name);
	$internal_ref = str_replace("'", "", $txt_internal_ref);
	$booking_no = str_replace("'", "", $txt_booking_no);
	$lot = str_replace("'", "", trim($txt_lot_no));
	$cbo_date_category = str_replace("'", "", $cbo_date_category);

	if ($buyer_id==0) $buyer_id_cond ="";
	else
		$buyer_id_cond =" AND C.BUYER_NAME = ".$buyer_id;

	if ($internal_ref=="") $internal_ref_cond ="";
	else $internal_ref_cond =" AND D.GROUPING = '".$internal_ref."'";

	if ($booking_no=="") $booking_no_cond ="";
	else $booking_no_cond =" AND B.BOOKING_NO = '".$booking_no."'";

	if ($lot=="") $lot_cond ="";
	else $lot_cond =" AND A.LOT = '".$lot."'";

	$allocation_date_cond = '';
	if ($from_date != "" && $to_date != "")
	{
		if($cbo_date_category == 2)
		{
			$allocation_date_cond = " AND D.PUB_SHIPMENT_DATE BETWEEN '" . date("j-M-Y", strtotime($from_date)) . "' AND '" . date("j-M-Y", strtotime($to_date)) . "'";
		}
		else
		{
			$allocation_date_cond = " AND B.ALLOCATION_DATE BETWEEN '" . date("j-M-Y", strtotime($from_date)) . "' AND '" . date("j-M-Y", strtotime($to_date)) . "'";
		}
	}

    //library array
	$company_name = return_field_value("company_name", "lib_company", "id=$company_id and status_active=1 and is_deleted=0","company_name");
	$company_short_name = return_field_value("company_short_name", "lib_company", "id=$company_id and status_active=1 and is_deleted=0","company_short_name");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
	$yarn_count_library=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$composition_library=return_library_array( "select id,composition_name from lib_composition_array", "id", "composition_name"  );

	$allocation_sql = "SELECT A.ID AS PROD_ID, A.YARN_COUNT_ID, A.YARN_COMP_TYPE1ST, A.YARN_TYPE, A.SUPPLIER_ID, A.LOT, B.ID AS DTLS_ID, B.BOOKING_NO, B.QNTY, B.ALLOCATION_DATE, B.JOB_NO, C.BUYER_NAME, D.ID PO_ID, D.PO_NUMBER, D.PUB_SHIPMENT_DATE, D.GROUPING, E.ID AS BKN_ID FROM INV_MATERIAL_ALLOCATION_DTLS B, WO_PO_BREAK_DOWN D, WO_PO_DETAILS_MASTER C, PRODUCT_DETAILS_MASTER A, WO_BOOKING_MST E WHERE B.PO_BREAK_DOWN_ID = D.ID AND B.JOB_NO = C.JOB_NO AND A.ID = B.ITEM_ID AND B.BOOKING_NO = E.BOOKING_NO AND B.QNTY > 0 AND B.IS_DYIED_YARN <> 1 AND E.IS_SHORT <> 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND D.STATUS_ACTIVE in(1,3) AND D.IS_DELETED = 0 AND C.COMPANY_NAME = ".$company_id.$buyer_id_cond.$internal_ref_cond.$booking_no_cond.$lot_cond.$allocation_date_cond." ORDER BY A.ID, B.BOOKING_NO, B.JOB_NO, B.ALLOCATION_DATE ASC";
	//echo $allocation_sql;

	$allocation_result = sql_select($allocation_sql);
	$po_arr =  array();
	$prod_arr =  array();
	$bkn_id_arr =  array();
	$data_arr = array();
	$duplicate_check = array();
	foreach ($allocation_result as $row)
	{
		if($duplicate_check[$row['DTLS_ID']] != $row['DTLS_ID'])
		{
			$duplicate_check[$row['DTLS_ID']] = $row['DTLS_ID'];
			$po_arr[$row['PO_ID']]=$row['PO_ID'];
			$prod_arr[$row['PROD_ID']]=$row['PROD_ID'];
			$bkn_id_arr[$row['BKN_ID']]=$row['BKN_ID'];

			$data_arr[$row['PROD_ID']][$row['BOOKING_NO']][$row['JOB_NO']]['LOT'] = $row['LOT'];
			$data_arr[$row['PROD_ID']][$row['BOOKING_NO']][$row['JOB_NO']]['YARN_COUNT_ID'] = $row['YARN_COUNT_ID'];
			$data_arr[$row['PROD_ID']][$row['BOOKING_NO']][$row['JOB_NO']]['YARN_COMP_TYPE1ST'] = $row['YARN_COMP_TYPE1ST'];
			$data_arr[$row['PROD_ID']][$row['BOOKING_NO']][$row['JOB_NO']]['YARN_TYPE'] = $row['YARN_TYPE'];
			$data_arr[$row['PROD_ID']][$row['BOOKING_NO']][$row['JOB_NO']]['SUPPLIER_ID'] = $row['SUPPLIER_ID'];
			$data_arr[$row['PROD_ID']][$row['BOOKING_NO']][$row['JOB_NO']]['PUB_SHIPMENT_DATE'] = change_date_format($row['PUB_SHIPMENT_DATE']);
			$data_arr[$row['PROD_ID']][$row['BOOKING_NO']][$row['JOB_NO']]['ALLOCATION_DATE'] = change_date_format($row['ALLOCATION_DATE']);
			$data_arr[$row['PROD_ID']][$row['BOOKING_NO']][$row['JOB_NO']]['GROUPING'] = $row['GROUPING'];
			$data_arr[$row['PROD_ID']][$row['BOOKING_NO']][$row['JOB_NO']]['BUYER_NAME'] = $row['BUYER_NAME'];
			$data_arr[$row['PROD_ID']][$row['BOOKING_NO']][$row['JOB_NO']]['GROUPING'] = $row['GROUPING'];
			$data_arr[$row['PROD_ID']][$row['BOOKING_NO']][$row['JOB_NO']]['PO_ID'][$row['PO_ID']] = $row['PO_ID'];
			//$data_arr[$row['PROD_ID']][$row['BOOKING_NO']][$row['JOB_NO']]['ALLOCATED_QNTY'] += decimal_format($row['QNTY'],1,'');
			$data_arr[$row['PROD_ID']][$row['BOOKING_NO']][$row['JOB_NO']]['ALLOCATED_QNTY'] += $row['QNTY'];
		}
	}
	//end for allocation

	$con = connect();
	execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id);
	execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = ".$user_id);
	execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID = ".$user_id);
	execute_query("DELETE FROM TMP_ISSUE_ID WHERE USER_ID = ".$user_id);
	oci_commit($con);

	//for po id
	$con = connect();
	foreach($po_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_PO_ID(PO_ID, USER_ID) VALUES('".$val."', '".$user_id."')");
	}

	//for product id
	foreach($prod_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_PROD_ID(PROD_ID, USERID) VALUES('".$val."', '".$user_id."')");
	}

	//for booking id
	foreach($bkn_id_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_BOOKING_ID(BOOKING_ID, USERID) VALUES('".$val."', '".$user_id."')");
	}
	oci_commit($con);

	//for requisition
	$sql_req = "SELECT L.BOOKING_NO, M.REQUISITION_NO AS REQUISITION_ID FROM PPL_PLANNING_ENTRY_PLAN_DTLS L, PPL_YARN_REQUISITION_ENTRY M, WO_BOOKING_MST N, TMP_BOOKING_ID O WHERE L.DTLS_ID = M.KNIT_ID AND L.BOOKING_NO = N.BOOKING_NO AND N.ID = O.BOOKING_ID AND O.USERID = ".$user_id;
	//$sql_req = "SELECT M.REQUISITION_ID FROM PPL_YARN_REQUISITION_BREAKDOWN M, WO_BOOKING_MST N, TMP_BOOKING_ID O WHERE M.BOOKING_NO = N.BOOKING_NO AND N.ID = O.BOOKING_ID AND O.USERID = ".$user_id;
	//echo $sql_req;
	$sql_req_rslt = sql_select($sql_req);
	$req_data_arr = array();
	foreach($sql_req_rslt as $row)
	{
		$req_data_arr[$row['REQUISITION_ID']]['REQUISITION_NO'] = $row['REQUISITION_ID'];
		$req_data_arr[$row['REQUISITION_ID']]['BOOKING_NO'] = $row['BOOKING_NO'];
	}

	//for yarn dyeing work order
	$sql_ydw = "SELECT L.YDW_NO, M.FAB_BOOKING_NO FROM WO_YARN_DYEING_MST L, WO_YARN_DYEING_DTLS M, WO_BOOKING_MST N, TMP_BOOKING_ID O WHERE L.ID = M.MST_ID AND M.FAB_BOOKING_NO = N.BOOKING_NO AND N.ID = O.BOOKING_ID AND O.USERID = ".$user_id;
	//echo $sql_req;
	$sql_ydw_rslt = sql_select($sql_ydw);
	$ydw_data_arr = array();
	foreach($sql_ydw_rslt as $row)
	{
		$ydw_data_arr[$row['YDW_NO']]['YDW_NO'] = $row['YDW_NO'];
		$ydw_data_arr[$row['YDW_NO']]['FAB_BOOKING_NO'] = $row['FAB_BOOKING_NO'];
	}

	//for issue
	$sql_issue = "SELECT A.ID, A.ISSUE_BASIS, A.ISSUE_PURPOSE, A.BOOKING_NO, B.REQUISITION_NO, C.ID AS PRO_DTLS_ID, C.PO_BREAKDOWN_ID, C.PROD_ID, C.QUANTITY FROM INV_ISSUE_MASTER A, INV_TRANSACTION B, ORDER_WISE_PRO_DETAILS C,WO_PO_BREAK_DOWN D WHERE A.ID = B.MST_ID AND B.ID = C.TRANS_ID AND C.PO_BREAKDOWN_ID=D.ID AND A.ENTRY_FORM = 3 AND A.ISSUE_PURPOSE IN(1,2) AND A.ISSUE_BASIS IN(1, 3) AND B.RECEIVE_BASIS  IN(1, 3) AND C.TRANS_TYPE = 2 AND B.ITEM_CATEGORY = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE in (1,3) AND D.IS_DELETED=0 AND C.PROD_ID IN(SELECT PROD_ID FROM TMP_PROD_ID WHERE USERID = ".$user_id.") AND C.PO_BREAKDOWN_ID IN(SELECT PO_ID FROM TMP_PO_ID WHERE USER_ID = ".$user_id.")";
	//echo $sql_issue;

	$result_issue = sql_select($sql_issue);
	$issue_id_arr=array();
	$issue_booking_arr=array();
	$duplicate_check = array();
	foreach ($result_issue as $row)
	{
		if($duplicate_check[$row['PRO_DTLS_ID']] != $row['PRO_DTLS_ID'])
		{
			$duplicate_check[$row['PRO_DTLS_ID']] = $row['PRO_DTLS_ID'];
			//for requisition basis
			if($row['ISSUE_BASIS'] == 3 && $req_data_arr[$row['REQUISITION_NO']]['REQUISITION_NO'] == $row['REQUISITION_NO'])
			{
				$BKN_NO = $req_data_arr[$row['REQUISITION_NO']]['BOOKING_NO'];
				$issue_arr[$row['PROD_ID']][$BKN_NO][$row['PO_BREAKDOWN_ID']]['ISSUE_QTY'] += $row['QUANTITY'];
				$issue_array_req[$row['PROD_ID']][$BKN_NO][$row['PO_BREAKDOWN_ID']]['ISSUE_ID'][$row['ID']] = $row['ID'];

				$issue_id_arr[$row['ID']] = $row['ID'];
				$issue_booking_arr[$row['ID']][$BKN_NO]['BOOKING_NO'] = $BKN_NO;
			}

			//for wo basis
			if($row['ISSUE_BASIS'] == 1 && $ydw_data_arr[$row['BOOKING_NO']]['YDW_NO'] == $row['BOOKING_NO'])
			//if($row['ISSUE_BASIS'] == 1 && $row['ISSUE_PURPOSE'] == 2)
			{
				$BKN_NO = $ydw_data_arr[$row['BOOKING_NO']]['FAB_BOOKING_NO'];
				$issue_arr[$row['PROD_ID']][$BKN_NO][$row['PO_BREAKDOWN_ID']]['ISSUE_QTY'] += $row['QUANTITY'];
				$issue_array_req[$row['PROD_ID']][$BKN_NO][$row['PO_BREAKDOWN_ID']]['ISSUE_ID'][$row['ID']] = $row['ID'];

				$issue_id_arr[$row['ID']] = $row['ID'];
				$issue_booking_arr[$row['ID']][$BKN_NO]['BOOKING_NO'] = $BKN_NO;
			}
		}
	}
	/*echo "<pre>";
	print_r($zs[31290]);
	echo "</pre>";
	die;*/

	foreach($issue_id_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_ISSUE_ID(ISSUE_ID, USER_ID) VALUES('".$val."', '".$user_id."')");
	}
	oci_commit($con);
	//end for issue

	//for issue return
	$issue_return_req_array = array();
	if(!empty($issue_id_arr))
	{
		$sql_return = "SELECT B.ISSUE_ID, C.PO_BREAKDOWN_ID, C.ID, C.PROD_ID, C.QUANTITY FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, ORDER_WISE_PRO_DETAILS C WHERE A.ID=B.MST_ID AND C.TRANS_TYPE=4 AND A.ENTRY_FORM=9 AND B.ID=C.TRANS_ID AND B.ITEM_CATEGORY=1 AND C.ISSUE_PURPOSE IN(1,2) AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND C.STATUS_ACTIVE=1 AND C.PROD_ID IN(SELECT PROD_ID FROM TMP_PROD_ID WHERE USERID = ".$user_id.") AND B.ISSUE_ID IN(SELECT ISSUE_ID FROM TMP_ISSUE_ID WHERE USER_ID = ".$user_id.")";
		//echo $sql_return;
		$result_return = sql_select($sql_return);
		$duplicate_check = array();
		foreach ($result_return as $row)
		{
			if($duplicate_check[$row['ID']] != $row['ID'])
			{
				$duplicate_check[$row['ID']] = $row['ID'];
				foreach($issue_booking_arr[$row['ISSUE_ID']] as $kbkn=>$vbkn)
				{
					$BKN_NO = $vbkn['BOOKING_NO'];
					$issue_return_req_array[$row['PROD_ID']][$BKN_NO][$row['PO_BREAKDOWN_ID']][$row['ISSUE_ID']] += $row['QUANTITY'];
				}
			}
		}
	}

	execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_id);
	execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = ".$user_id);
	execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID = ".$user_id);
	execute_query("DELETE FROM TMP_ISSUE_ID WHERE USER_ID = ".$user_id);
	oci_commit($con);
	//end for issue return
	/*echo "<pre>";
	print_r($issue_return_req_array[14341]);
	echo "</pre>";*/
	?>
	<style type="text/css">
		table tbody tr td{ text-align: center; }
		.right-align{ text-align: right; }
	</style>
	<?
	ob_start();
	?>
	<div id="scroll_body">
		<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
			<thead>
				<tr class="form_caption" style="border:none;">
					<td colspan="18" align="center" style="border:none;font-size:16px; font-weight:bold" >Lot Wise Yarn Allocation Report</td>
				</tr>
				<tr style="border:none;">
					<td colspan="18" align="center" style="border:none; font-size:14px;"><? echo $company_name; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="18" align="center" style="border:none;font-size:12px; font-weight:bold"><? if ($from_date != "" || $to_date != "") echo "From " . change_date_format($from_date) . " To " . change_date_format($to_date) . ""; ?></td>
				</tr>
				<tr>
					<th width="20">SL</th>
					<th width="120">Product ID</th>
					<th width="120">Lot</th>
					<th width="110">Count</th>
					<th width="130">Composition</th>
					<th width="120">Yarn Type</th>
					<th width="100">Supplier</th>
					<th width="80">Booking No.</th>
					<th width="80">Job NO.</th>
					<th width="80">Internal Ref.</th>
					<th width="100">Shipment date</th>
					<th width="100">Allocation Date</th>
					<th width="80">Allocated Qty</th>
					<th width="80">Issue Qty</th>
					<th width="80">Issue Rtn Qty</th>
					<th width="80">Issue Balance</th>
					<th width="80">Buyer</th>
					<th width="80">Company</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$i = 0;
				if(!empty($data_arr))
				{
					foreach($data_arr as $product_id=>$product_id_arr)
					{
						foreach($product_id_arr as $booking_no=>$booking_no_arr)
						{
							foreach($booking_no_arr as $job_no=>$row)
							{
								$i++;
								$bgcolor = ($i % 2 == 0)?"#E9F3FF":"#FFFFFF";

								//for issue qty
								$issue_qnty = 0;
								foreach($row['PO_ID'] as $pKey=>$pVal)
								{
									//$issue_qnty += $issue_arr[$product_id][$pKey]['ISSUE_QTY'];
									$issue_qnty += $issue_arr[$product_id][$booking_no][$pKey]['ISSUE_QTY'];
								}

								//for issue return
								$return_qnty=0;
								foreach($row['PO_ID'] as $pKey=>$pVal)
								{
									foreach($issue_array_req[$product_id][$booking_no][$pKey]['ISSUE_ID'] as $key=>$val)
									{
										$return_qnty += $issue_return_req_array[$product_id][$booking_no][$pKey][$key];
									}
								}

								//for balance
								//$balance = $row['ALLOCATED_QNTY'] - $issue_qnty + $return_qnty;
								$balance = number_format($row['ALLOCATED_QNTY'],6,'.','') - number_format($issue_qnty,6,'.','') + number_format($return_qnty,6,'.','');
								//echo $balance.'='.$row['ALLOCATED_QNTY'].'='.$issue_qnty.'='.$return_qnty;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="color:#000000" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td><? echo $i;?></td>
									<td><? echo $product_id; ?></td>
									<td>&nbsp;<? echo $row['LOT']; ?></td>
									<td>&nbsp;<? echo $yarn_count_library[$row['YARN_COUNT_ID']]; ?></td>
									<td><? echo $composition_library[$row['YARN_COMP_TYPE1ST']]; ?></td>
									<td><? echo $yarn_type[$row['YARN_TYPE']]; ?></td>
									<td><? echo $supplier_library[$row['SUPPLIER_ID']]; ?></td>
									<td><? echo $booking_no; ?></td>
									<td class="right-align"><? echo $job_no; ?></td>
									<td class="center-align"><? echo $row['GROUPING']; ?></td>
									<td>&nbsp;<? echo $row['PUB_SHIPMENT_DATE']; ?></td>
									<td>&nbsp;<? echo $row['ALLOCATION_DATE']; ?></td>
									<td class="right-align"><? echo decimal_format($row['ALLOCATED_QNTY'],1,','); ?></td>
                                    <td class="right-align"><? echo decimal_format($issue_qnty,1,','); ?></td>
                                    <td class="right-align"><? echo decimal_format($return_qnty,1,','); ?></td>
                                    <td class="right-align"><? echo decimal_format($balance,1,','); ?></td>
									<td><? echo $buyer_library[$row['BUYER_NAME']];?></td>
									<td><? echo $company_short_name; ?></td>
								</tr>
								<?
								$total_allocation_qnty += $row['ALLOCATED_QNTY'];
								$total_issue_qnty += $issue_qnty;
								$total_issue_rtn_qnty += $return_qnty;

								$total_balance += $balance;
							}
						}
					}
					?>
					<tr>
						<th style="border-bottom: 1px solid #8DAFDA;padding: 1px;" colspan="18"></th>
					</tr>
					<tr>
						<th class="right-align" colspan="12">Total = </th>
						<th class="right-align"><? echo decimal_format($total_allocation_qnty,1,','); ?></th>
						<th class="right-align"><? echo decimal_format($total_issue_qnty,1,','); ?></th>
						<th class="right-align"><? echo decimal_format($total_issue_rtn_qnty,1,','); ?></th>
						<th class="right-align"><? echo decimal_format($total_balance,1,','); ?></th>
					</tr>
					<?
				}
				else
				{
					echo "<tr><th colspan='18' style='color:red;'>No Data Found</th></tr>";
				}
				?>
			</tbody>
		</table>
	</div><br />
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**2";
	disconnect($con);
	exit();
}

if ($action == "generate_report_3")
{
	$process = array(&$_POST);
	//var_dump($process);die;
	extract(check_magic_quote_gpc($process));

	$search_cond = "";
	$company_id = str_replace("'", "", $cbo_company_name);
	$buyer_id = str_replace("'", "", $cbo_buyer_name);
	$cbo_is_sales = str_replace("'", "", $cbo_is_sales);
	$internal_ref = str_replace("'", "", $txt_internal_ref);
	$booking_no = str_replace("'", "", $txt_booking_no);
	$sales_ord_no = str_replace("'", "", $txt_sales_no);
	$lot = str_replace("'", "", trim($txt_lot_no));
	$cbo_year = str_replace("'", "", $cbo_year_selection);

	if ($buyer_id==0) $buyer_id_cond =""; else $buyer_id_cond =" and c.buyer_name=$buyer_id ";

	if ($cbo_is_sales!=1)
	{
		$is_sales_cond ="  and b.is_sales<>1";
	}
	else
	{
		if ($sales_ord_no=="") $sales_ord_no_cond =""; else $sales_ord_no_cond =" and d.job_no_prefix_num like '%$sales_ord_no%' ";
		$is_sales_cond =" and b.is_sales=1";
	}

	if ($internal_ref=="") $internal_ref_cond =""; else $internal_ref_cond =" and d.grouping like '%$internal_ref%' ";
	if ($internal_ref=="") $internal_ref_cond2 =""; else $internal_ref_cond2 =" and c.grouping like '%$internal_ref%' ";
	if ($booking_no=="") $booking_no_cond =""; else $booking_no_cond =" and b.booking_no like '%$booking_no%' ";

	if ($lot==""){
		$lot_cond ="";
		$lot_cond2 ="";
	}else {
		$lot_cond =" and a.lot='$lot' ";
		$lot_cond2 =" and c.lot='$lot' ";
	}


	if(trim($cbo_year)!=0)
	{
		if($sales_ord_no=="")
		{
			if($db_type==0) $year_cond=" AND YEAR(c.insert_date) = ".$cbo_year;
			else if($db_type==2) $year_cond=" AND TO_CHAR(C.INSERT_DATE,'YYYY') = ".$cbo_year;
			else $year_cond="";
		}
		else
		{
			if($db_type==0) $year_cond=" AND YEAR(d.insert_date) = ".$cbo_year;
			else if($db_type==2) $year_cond=" AND TO_CHAR(d.INSERT_DATE,'YYYY') = ".$cbo_year;
			else $year_cond="";
		}
	}
	else
	{
		$year_cond="";
	}


	if ($db_type == 0) {
		if ($from_date != "" && $to_date != ""){
			$allocation_date_cond = "and b.allocation_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
		}

	}
	else {
		if ($from_date != "" && $to_date != ""){
			$allocation_date_cond .= " and b.allocation_date  between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
		}
	}

	//library array
	$company_name = return_field_value("company_name", "lib_company", "id=$company_id and status_active=1 and is_deleted=0","company_name");
	$company_short_name = return_field_value("company_short_name", "lib_company", "id=$company_id and status_active=1 and is_deleted=0","company_short_name");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
	$yarn_count_library=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$composition_library=return_library_array( "select id,composition_name from lib_composition_array", "id", "composition_name"  );

	if($cbo_is_sales!=1)
    {
    	$lot_sql = "SELECT distinct a.id as product_id, a.lot from inv_mat_allocation_dtls_log b,wo_po_break_down d,wo_po_details_master c,product_details_master a where c.company_name=$company_id $buyer_id_cond $internal_ref_cond $booking_no_cond $lot_cond $allocation_date_cond and b.po_break_down_id=d.id and b.job_no=c.job_no and a.id=b.item_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in (1,3) and d.is_deleted=0 group by a.id, a.lot";
    }
    else
    {
		$lot_sql =  "SELECT distinct a.id  AS product_id, a.lot FROM inv_mat_allocation_dtls_log b,fabric_sales_order_mst d left join wo_po_break_down c on d.po_job_no= c.job_no_mst $internal_ref_cond2, product_details_master a WHERE d.company_id = $company_id $booking_no_cond $lot_cond $allocation_date_cond $sales_ord_no_cond $year_cond $is_sales_cond AND b.po_break_down_id = d.id AND b.job_no = d.job_no AND a.id = b.item_id AND a.status_active = 1 AND a.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 GROUP BY a.id, a.lot";
    }
	$result = array();
	$lot_arr = array();	
	//  echo $lot_sql; die;
	$lot_arr = sql_select($lot_sql);
	foreach($lot_arr as $row)
	{
		if($row['LOT'] != '')
		{
			$lot_array[$row['LOT']]['LOT'] = $row['LOT'];
			$lot_array[$row['LOT']]['PROD_ID'] = $row['PRODUCT_ID'];
		}
		
	}

	$allocation_sql = "SELECT c.lot, b.allocation_date, b.insert_date, b.qnty  allocation_qnty, c.unit_of_measure, c.store_id, c.id as PROD_ID, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.supplier_id,a.BOOKING_WITHOUT_ORDER,b.BOOKING_NO,b.IS_SALES,B.PO_BREAK_DOWN_ID FROM inv_material_allocation_mst a, inv_material_allocation_dtls b, product_details_master c WHERE  a.id = b.mst_id AND b.item_id = c.id  AND a.item_category = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 $lot_cond2 ORDER BY a.item_id, b.insert_date DESC";
	// echo $allocation_sql;
    $allocation_result = sql_select($allocation_sql);
	foreach ($allocation_result as $row)
	{
		$first_allocation_flag[$row['LOT']] = 0;
		$first_allocation_date[$row['LOT']] = '';
		if(($lot_array[$row['LOT']]['LOT'] == $row['LOT']) && ($lot_array[$row['LOT']]['PROD_ID'] == $row['PROD_ID']))
		{	
			
			$result[$row['PROD_ID']]['LOT'] = $row['LOT'];
			$result[$row['PROD_ID']]['PROD_ID'] = $row['PROD_ID'];
			$result[$row['PROD_ID']]['YARN_COUNT_ID'] = $row['YARN_COUNT_ID'];
			$result[$row['PROD_ID']]['YARN_TYPE'] = $row['YARN_TYPE'];
			$result[$row['PROD_ID']]['YARN_COMP_TYPE1ST'] = $row['YARN_COMP_TYPE1ST'];
			$result[$row['PROD_ID']]['ALLOCATION_DATE'] = $row['ALLOCATION_DATE'];
			$result[$row['PROD_ID']]['ALLOCATED_QNTY'] += $row['ALLOCATION_QNTY'];
			$result[$row['PROD_ID']]['SUPPLIER_ID'] = $row['SUPPLIER_ID'];
			$result[$row['PROD_ID']]['SUPPLIER_ID'] = $row['SUPPLIER_ID'];
			$first_alloc_date[$row['PROD_ID']]['FIRST_ALLOCATION_DATE'] = $row['ALLOCATION_DATE'];
			

			if($row['BOOKING_WITHOUT_ORDER']==1)
			{
				$result[$row['PROD_ID']]['SMN_BOOKING_NO'] .= $row['BOOKING_NO']."__";
			}
			else
			{
				if($row['IS_SALES']==1)
				{
					$result[$row['PROD_ID']]['FSO_ID'] .= $row['PO_BREAK_DOWN_ID']."__";
				}
				else
				{
					$result[$row['PROD_ID']]['PO_ID'] .= $row['PO_BREAK_DOWN_ID']."__";
				}

				$result[$row['PROD_ID']]['FAB_BOOKING_NO'] .= $row['BOOKING_NO']."__";
			}
			
		}	
	}

	//  echo "<pre>";
	// print_r($First_allow_Date); die; 

	$issue_sql = "SELECT a.transaction_date, a.transaction_type, CASE WHEN a.transaction_type = 2 THEN a.cons_quantity ELSE 0 END issue_qnty, CASE WHEN a.transaction_type = 4 THEN a.cons_quantity ELSE 0 END issue_return_qnty, a.prod_id, c.lot FROM inv_transaction a, product_details_master c WHERE  a.prod_id = c.id  AND a.transaction_type IN (2,4) and a.receive_basis in (1,8) AND a.item_category = 1 AND a.status_active = 1 AND a.is_deleted = 0 $lot_cond2 ORDER BY a.transaction_date ASC";
	// echo $issue_sql; die;
	$issue_result = sql_select($issue_sql);
	foreach ($issue_result as $row)
	{
		if($lot_array[$row['LOT']]['PROD_ID'] == $row['PROD_ID'])
		{
			$result[$row['PROD_ID']]['LAST_ISSUE_DATE'] = $last_issue_date;
			$result[$row['PROD_ID']]['ISSUE_QNTY'] += $row['ISSUE_QNTY'];
			$result[$row['PROD_ID']]['ISSUE_RETURN_QNTY'] += $row['ISSUE_RETURN_QNTY'];
			if($row['TRANSACTION_TYPE'] == 2)
			{
				$last_issue_date_arr[$row['PROD_ID']]['LAST_ISSUE_DATE'] = $row['TRANSACTION_DATE'];
			}	
		}		
	}

	// echo "<pre>";
	// print_r($last_issue_date);die; 
	//echo "Today is " . date("Y/m/d") . "<br>";

	?>
	<style type="text/css">
		table tbody tr td{ text-align: center; }
		.right-align{ text-align: right; }
	</style>
	<?

	ob_start();
	?>
	<div id="scroll_body">

		<table width="1520" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
			<thead>
				<tr class="form_caption" style="border:none;">
					<td colspan="21" align="center" style="border:none;font-size:16px; font-weight:bold" >Lot Wise Yarn Allocation Report</td>
				</tr>
				<tr style="border:none;">
					<td colspan="21" align="center" style="border:none; font-size:14px;">Company Name : <? echo $company_name; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="21" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? if ($from_date != "" || $to_date != "") echo "From " . change_date_format($from_date) . " To " . change_date_format($to_date) . ""; ?>
					</td>
				</tr>
				<tr>
					<th width="20">SL</th>
					<th width="100">Product ID</th>
					<th width="100">Lot</th>
					<th width="100">Count</th>
					<th width="100">Composition</th>
					<th width="100">Yarn Type</th>
					<th width="100">Supplier</th>
					<th width="100">First Allocation Date</th>
					<th width="100">Total Allocated Qty</th>
					<th width="100">Total Issue Qty</th>
					<th width="100">Total Issue Rtn Qty</th>
					<th width="100">Total Issue Balance</th>
					<th width="100">Last Issue Date</th>
					<th width="100">Days Passed From Last Issue Date</th>
					<th width="100">Yarn Allocated Age</th>
					<th width="100">Company</th>
				</tr>
			</thead>
		</table>

		<div style="width:1520; overflow-y:scroll; max-height:250px" id="scroll_body">
			<table width="1520" border="1" cellpadding="2" style="font:'Arial Narrow';"  cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<tbody>
					<?php

					$i = 1;
					$r=1;
					$item_arr = array();
					$issue_print = 0;
					$ref_data ="";

					if(!empty($result))
					{
						foreach ($result as $key=>$row)
						{
							$prod_id = $row[csf("Prod_id")];
							$first_allocation_date = $first_alloc_date[$prod_id]['FIRST_ALLOCATION_DATE'];
							$last_issue_date = $last_issue_date_arr[$prod_id]['LAST_ISSUE_DATE'];
							$dpfOfDays_allocation = datediff("d", $first_allocation_date, date("Y-m-d"));
							$dpfOfDays_issue = datediff("d", $last_issue_date, date("Y-m-d"));

							$bgcolor = ($i % 2 == 0)?"#E9F3FF":"#FFFFFF";
							$stylecolor = ($row["transaction_type"] == 2)? "style='color:#A61000'" : "style='color:#000000'";

							$fso_id = chop($row['FSO_ID'],'__');
							$po_id = chop($row['PO_ID'],'__');							
							$smn_booking_no = chop($row['SMN_BOOKING_NO'],'__');
							$fab_booking_no = chop($row['FAB_BOOKING_NO'],'__');

							$ref_data = $prod_id.'___'.$fso_id.'___'.$po_id.'___'.$smn_booking_no.'___'.$fab_booking_no;
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="20"><? echo $i;?></td>
								<td width="100"><? echo $prod_id;?></td>
								<td width="100"><? echo $row[csf("lot")];?></td>
								<td width="100"><? echo $yarn_count_library[$row[csf("yarn_count_id")]];?></td>
								<td width="100"><? echo $composition_library[$row[csf("yarn_comp_type1st")]];?></td>
								<td width="100"><? echo $yarn_type[$row[csf("yarn_type")]];?></td>
								<td width="100"><? echo $supplier_library[$row[csf("supplier_id")]];?></td>
								<td width="100"><? echo change_date_format($first_allocation_date);?></td>
								<td width="100" class="right-align" ><a onclick="allocation_popup('<?= $ref_data;?>');" href="##"><? echo number_format($row[csf("allocated_qnty")],2,".",""); $qty +=$row[csf("allocated_qnty")]; ?></a></td>
								<td width="100" class="right-align"><a onclick ="issue_popup('<?= $ref_data;?>');" href="##"><? echo number_format($row[csf("issue_qnty")],2,".","");?></a></td>
								<td width="100" class="right-align"><a onclick ="issue_rtn_popup('<?= $ref_data;?>');" href="##"><? echo number_format($row[csf("issue_return_qnty")],2,".","");  ?></a></td>
								<td width="100" class="right-align"><? echo number_format(($row[csf("allocated_qnty")] + $row[csf("issue_return_qnty")] - $row[csf("issue_qnty")]),2,".","");  ?></td>
								<td width="100"><? echo change_date_format($last_issue_date);?></td>
								<td width="100"><?= $dpfOfDays_issue?></td>
								<td width="100"><?= $dpfOfDays_allocation?></td>
								
								<td width="100"><? echo $company_short_name; ?></td>
							</tr>
							<?
							$total_issue_qnty +=$row[csf("issue_qnty")];
							$total_issue_rtn_qnty +=$row[csf("issue_return_qnty")];
							$issue_balance_qty += ($row[csf("allocated_qnty")] - $row[csf("issue_qnty")] + $row[csf("issue_return_qnty")]);
							$total_allocation_qnty +=$row[csf("allocated_qnty")];
							$r++;
							$i++;
						}
					}
					else
					{
						echo "<tr><th colspan='19' style='color:red;'>No Data Found</th></tr>";
					}
					?>
				</tbody>
			</table>
		</div>

		<table width="1520" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
			<tr class="tbl_bottom">
				<td width="20">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100" class="right-align" style="font-weight: bold; font-size:16px;">Total</td>
				<td width="100" class="right-align" name="value_total_allocation_qty"><?= number_format($total_allocation_qnty,2,".",""); ?></td>
				<td width="100" class="right-align" name="value_total_issue_qty"><?= number_format($total_issue_qnty,2,".",""); ?></td>
				<td width="100" class="right-align" name="value_total_issue_return_qty"><?= number_format($total_issue_rtn_qnty,2,".",""); ?></td>
				<td width="100" class="right-align" name="value_total_balance"><?= number_format($issue_balance_qty,2,".","");?></td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				
			</tr>
		</table>

	</div>

	<br />
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**1";
	exit();
}

if ($action == "yarn_allocation_popup") 
{
	echo load_html_head_contents("Popup Info", "../../../../", 1, 1, $unicode);
	//$started = microtime(true);
	extract($_REQUEST);
	list($prod_id,$fso_id,$po_id,$sample_booking_no,$fab_booking_no) = explode("___",$ref_data);
	$buyer_library = return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

	$fab_booking_no_arr =  explode("__",$fab_booking_no);
	$sample_booking_no_arr =  explode("__",$sample_booking_no);
	$booking_no_arr = array_merge($fab_booking_no_arr,$sample_booking_no_arr);
	$booking_nos = "'".implode("','",$booking_no_arr)."'";
	
	$requisition_demand_booking_array = return_library_array( "select a.requisition_no,b.booking_no from PPL_YARN_REQUISITION_ENTRY a,PPL_PLANNING_ENTRY_PLAN_DTLS b where a.prod_id = $prod_id and a.knit_id=b.dtls_id and b.booking_no in ($booking_nos)  ", "requisition_no", "booking_no"  );

	$wo_booking_array = return_library_array( "select b.mst_id as booking_id, (b.booking_no || b.fab_booking_no) as booking_no from wo_yarn_dyeing_dtls b where b.product_id = $prod_id and (b.booking_no in ($booking_nos) or b.fab_booking_no in ($booking_nos)) ", "booking_id", "booking_no");

	// for sample booking
	$sample_booking_nos = "'".implode("','",$sample_booking_no_arr)."'";
	$sample_booking_sql = "select b.BOOKING_NO, a.BUYER_NAME as buyer, a.STYLE_REF_NO, a.INTERNAL_REF from SAMPLE_DEVELOPMENT_MST a, SAMPLE_DEVELOPMENT_YARN_DTLS b where b.mst_id = a.id and b.booking_no in ($sample_booking_nos)";
	$booking_info_smpl = sql_select($sample_booking_sql);

	foreach($booking_info_smpl as $row)
	{
		$booking_data_arr[$row['BOOKING_NO']]['BUYER'] =  $row['BUYER'];
		$booking_data_arr[$row['BOOKING_NO']]['STYLE_REF_NO'] =  $row['STYLE_REF_NO'];
		$booking_data_arr[$row['BOOKING_NO']]['INTERNAL_REF'] =  $row['INTERNAL_REF'];
	}
	unset($booking_info_smpl);

	// for fabric booking 
	$fabric_booking_nos = "'".implode("','",$fab_booking_no_arr)."'";
	$booking_info_sql = "select a.booking_no as BOOKING_NO, a.buyer_id as BUYER,d.style_ref_no as STYLE_REF_NO, c.pub_shipment_date as PUB_SHIPMENT_DATE, c.grouping as GROUPING from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_id=d.id and b.booking_no in ($fabric_booking_nos) and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
	//echo $booking_info_sql; die;
	$booking_info = sql_select($booking_info_sql);
	foreach($booking_info as $row)
	{
		$booking_data_arr[$row['BOOKING_NO']]['BUYER'] =  $row['BUYER'];
		$booking_data_arr[$row['BOOKING_NO']]['STYLE_REF_NO'] =  $row['STYLE_REF_NO'];
		$booking_data_arr[$row['BOOKING_NO']]['PUB_SHIPMENT_DATE'] = $row['PUB_SHIPMENT_DATE'];
		$booking_data_arr[$row['BOOKING_NO']]['INTERNAL_REF'] = $row['GROUPING'];
	}
	unset($booking_info);
	/* echo "<pre>";
	print_r($booking_data_arr); */

	$issue_sql = "SELECT a.id as ISSUE_ID,a.issue_basis as ISSUE_BASIS,a.booking_id as BOOKING_ID,b.requisition_no as REQUISITION_NO,b.booking_no as BOOKING_NO,b.prod_id as PROD_ID,b.CONS_QUANTITY as ISSUE_QNTY FROM inv_issue_master a,inv_transaction b WHERE a.id=b.mst_id and b.receive_basis in (1,8) and b.item_category=1 and b.transaction_type=2 and b.prod_id = $prod_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";
	$issue_result = sql_select($issue_sql);				
	foreach($issue_result as $row)
	{
		$booking_no = ($row['ISSUE_BASIS']==1)?$wo_booking_array[$row['BOOKING_ID']]:$requisition_demand_booking_array[$row['REQUISITION_NO']];
		$issue_data_arr[$booking_no]['ISSUE_QNTY'] += $row['ISSUE_QNTY']; 
		$issue_data_arr[$booking_no]['ISSUE_IDS'] .= $row['ISSUE_ID'].","; 
	}
	unset($issue_result);

	$sql_rtn = "select a.receive_basis as RECEIVE_BASIS,a.booking_id as BOOKING_ID,b.requisition_no as REQUISITION_NO, b.cons_quantity as RETURN_QNTY from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis in (1,8) and b.item_category=1 and b.transaction_type = 4 and b.prod_id = $prod_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";
	//echo $sql_rtn;echo "<br>";
	$issue_rtn_rslt = sql_select($sql_rtn);
	foreach($issue_rtn_rslt as $row)
	{
		$booking_no = ($row['RECEIVE_BASIS']==1)?$wo_booking_array[$row['BOOKING_ID']]:$requisition_demand_booking_array[$row['REQUISITION_NO']];
		$issue_return_data_arr[$booking_no]['ISSUE_RETURN'] += $row['RETURN_QNTY'];
	}
	unset($issue_rtn_rslt);

	/* echo "<pre>";
	print_r($issue_return_data_arr); */
	?>
	<body>
		<div align="center" style="width:98%;" >
			<table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th>SL</th>
						<th>Date</th>
						<th>Job/FSO No.</th>
						<th>Buyer</th>
						<th>Style Ref</th>
						<th>Shipment Date</th>
						<th>Internal Ref No.</th>
						<th>Booking No.</th>
						<th>Allocated Qty</th>
						<th>Issue Qty</th>
						<th>Issue Rtn Qty</th>
						<th>Balance</th>
						<th>Allocation Age</th>
					</tr>
				</thead>
				<tbody style="background-color: white;">
				<?
					$i=1;
					$total_allocated_qty = 0;
					$total_issue_qty = 0;
					$total_issue_rtn_qty = 0;
					$total_balance = 0;
					
					$allocation_sql = "SELECT a.job_no as JOB_NO, a.booking_no as BOOKING_NO, a.po_break_down_id as PO_BREAK_DOWN_ID, a.item_id as ITEM_ID, c.product_name_details as PRODUCT_NAME_DETAILS, c.lot as LOT, b.allocation_date as ALLOCATION_DATE , b.insert_date as INSERT_DATE, sum(b.qnty) as QNTY, c.unit_of_measure as UNIT_OF_MEASURE, c.store_id as STORE_ID FROM inv_material_allocation_mst a, inv_material_allocation_dtls b, product_details_master  c WHERE  a.id = b.mst_id AND b.item_id = c.id AND a.item_id IN ($prod_id) AND a.item_category = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 group by a.job_no, a.booking_no, a.po_break_down_id, a.item_id, c.product_name_details, c.lot, b.allocation_date, b.insert_date,c.unit_of_measure, c.store_id ORDER BY a.item_id, b.insert_date ASC";
					//echo $allocation_sql; die;

					$allocation_result = sql_select($allocation_sql);
				
					foreach($allocation_result as $row)
					{
						$bgcolor = ($i % 2 == 0)?"#E9F3FF":"#FFFFFF";
						$dpfOfDays_allocation = datediff("d", $row['ALLOCATION_DATE'], date("Y-m-d"));
						$stylecolor = ($row["transaction_type"] == 2)? "style='color:#A61000'" : "style='color:#000000'";

						$buyer_id = $booking_data_arr[$row['BOOKING_NO']]['BUYER'];
						$style_ref_no = $booking_data_arr[$row['BOOKING_NO']]['STYLE_REF_NO'];
						$pub_shipment_date = $booking_data_arr[$row['BOOKING_NO']]['PUB_SHIPMENT_DATE'];
						$internal_ref = $booking_data_arr[$row['BOOKING_NO']]['INTERNAL_REF'];

						$issue_qnty = $issue_data_arr[$row['BOOKING_NO']]['ISSUE_QNTY'];
						$issue_return = $issue_return_data_arr[$row['BOOKING_NO']]['ISSUE_RETURN'];	
			
						$balance = $row['QNTY'] + $issue_return - $issue_qnty;
						?>
							<tr style="text-align: center;" bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td><?= $i?></td>
								<td><?= change_date_format($row['ALLOCATION_DATE']);?></td>
								<td><?= $row['JOB_NO'];?></td>
								<td><?= $buyer_library[$buyer_id];?></td>
								<td><?= $style_ref_no;?></td>								
								<td><?= change_date_format($pub_shipment_date); ?></td>
								<td><?= $internal_ref; ?></td>
								<td><?= $row['BOOKING_NO']; ?></td>
								<td align="center"><?= $row['QNTY']; ?></td>
								<td align="center"><?if($issue_qnty != '') echo $issue_qnty; else echo 0;?></td>
								<td align="center"><?if($issue_return != '') echo $issue_return; else echo 0;?></td>
								<td align="center"><?= $balance; ?></td>
								<td align="center"><?= $dpfOfDays_allocation; ?></td>
							</tr>
						<?
						$total_issue_qty += $issue_qnty;
						$total_issue_rtn_qty += $issue_return;
						$total_balance += $balance;
						$total_allocated_qty += $row['QNTY'];
						$i++;
					}
					?>
					<tfoot>
						<tr  class="tbl_bottom">
							<td colspan="8" align="right">Total</td>
							<td style="text-align:center"><?=$total_allocated_qty ?></td>
							<td style="text-align:center"><?=$total_issue_qty ?></td>
							<td style="text-align:center"><?=$total_issue_rtn_qty ?></td>
							<td style="text-align:center"><?= $total_balance ?></td>
							<td style="text-align:center"><??></td>
						
						</tr>
					</tfoot>
					
				</tbody>
			</table>    
		</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script> 
	</html>
	<?
	//echo "<br />Execution Time: " . (microtime(true) - $started).'S'; die;
	exit();
}

if ($action == "yarn_issue_popup") 
{
	echo load_html_head_contents("Popup Info", "../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($prod_id,$fso_id,$po_id,$sample_booking_no,$fab_booking_no) = explode("___",$ref_data);
	$fab_booking_no_arr =  explode("__",$fab_booking_no);
	$sample_booking_no_arr =  explode("__",$sample_booking_no);
	$booking_no_arr = array_merge($fab_booking_no_arr,$sample_booking_no_arr);
	$booking_nos = "'".implode("','",$booking_no_arr)."'";
	$fso_ids = str_replace("__",",",$fso_id);
	$po_id = str_replace("__",",",$po_id);

	$requisition_sql = "select a.requisition_no as REQUISITION_NO,b.booking_no as BOOKING_NO,b.po_id as PO_ID,b.is_sales AS IS_SALES,c.job_no AS FSO_NO,c.po_job_no AS PO_JOB_NO  from PPL_YARN_REQUISITION_ENTRY a,PPL_PLANNING_ENTRY_PLAN_DTLS b left join FABRIC_SALES_ORDER_MST c on b.po_id=c.id and c.id in ($fso_ids)  where a.prod_id = $prod_id and a.knit_id=b.dtls_id and b.booking_no in ($booking_nos)";

	//echo $requisition_sql; die;
	$requisition_result = sql_select($requisition_sql); 
	foreach($requisition_result as $row)
	{
		$requisition_demand_booking_array[$row['REQUISITION_NO']]['booking_no']= $row['BOOKING_NO'];
		if($row['IS_SALES']==1)
		{		
			$requisition_demand_booking_array[$row['REQUISITION_NO']]['fso_no']= $row['FSO_NO'];
		}	
	}

	$wo_sql = "select b.mst_id as BOOKING_ID, b.job_no as JOB_NO, (b.booking_no || b.fab_booking_no) as BOOKING_NO,a.is_sales as IS_SALES from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.product_id = $prod_id and (b.booking_no in ($booking_nos) or b.fab_booking_no in ($booking_nos))";
	$wo_result = sql_select($wo_sql); 
	$wo_booking_array= array();
	foreach($wo_result as $row)
	{
		$wo_booking_array[$row['BOOKING_ID']]['booking_no']= $row['BOOKING_NO'];
		if($row['IS_SALES']==1)
		{
			$wo_booking_array[$row['BOOKING_ID']]['fso_no']= $row['JOB_NO'];
		}
	}

	//for sample booking
	$sample_booking_nos = "'".implode("','",$sample_booking_no_arr)."'";
	$sample_booking_sql = "select b.BOOKING_NO, a.BUYER_NAME as buyer, a.STYLE_REF_NO, a.INTERNAL_REF from SAMPLE_DEVELOPMENT_MST a, SAMPLE_DEVELOPMENT_YARN_DTLS b where b.mst_id = a.id and b.booking_no in ($sample_booking_nos)";
	$booking_info_smpl = sql_select($sample_booking_sql);

	foreach($booking_info_smpl as $row)
	{
		$booking_data_arr[$row['BOOKING_NO']]['INTERNAL_REF'] =  $row['INTERNAL_REF'];
	}
	unset($booking_info_smpl);

	//for fabric booking 
	$fabric_booking_nos = "'".implode("','",$fab_booking_no_arr)."'";
	$booking_info_sql = "select a.booking_no as BOOKING_NO,c.grouping as GROUPING from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_id=d.id and b.booking_no in ($fabric_booking_nos) and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
	//echo $booking_info_sql; die;
	$booking_info = sql_select($booking_info_sql);
	foreach($booking_info as $row)
	{
		$booking_data_arr[$row['BOOKING_NO']]['INTERNAL_REF'] = $row['GROUPING'];
	}
	unset($booking_info);

	$issue_sql = " SELECT a.issue_number AS ISSUE_NUMBER,a.issue_date AS ISSUE_DATE,a.issue_basis AS ISSUE_BASIS,a.booking_id AS BOOKING_ID, (b.demand_no || a.booking_no) as WO_DEMAND_NO,b.cons_quantity as ISSUE_QNTY, b.requisition_no AS REQUISITION_NO, c.po_breakdown_id AS PO_ID,c.is_sales AS IS_SALES FROM  inv_issue_master a , inv_transaction b left join order_wise_pro_details c on b.id=c.trans_id and b.prod_id=c.prod_id and c.trans_type=2 and c.status_active=1 and c.is_deleted=0 WHERE b.mst_id = a.id AND b.prod_id=$prod_id AND b.item_category = 1 AND b.transaction_type = 2 AND b.receive_basis in (1,8) AND b.status_active = 1 AND b.is_deleted = 0 ORDER BY b.transaction_date DESC";
	//echo $issue_sql; die;
	$issue_result = sql_select($issue_sql);

	/* echo "<pre>";
	print_r($po_data_array); */
	?>
	<body>
		<div align="center" style="width:98%;" >
			<table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th>SL</th>
						<th>Issue Date</th>
						<th>Issue ID</th>
						<th>Demand/Wo NO</th>
						<th>FSO</th>
						<th>Booking NO</th>
						<th>IR/IB</th>
						<th>Issue Qty</th>
					</tr>
				</thead>
				<tbody style="background-color: white;">
					<?
					$i=1;
					$total_issued_qty = 0;
					foreach($issue_result as $row)
					{
						$bgcolor = ($i % 2 == 0)?"#E9F3FF":"#FFFFFF";
						$stylecolor = ($row["transaction_type"] == 2)? "style='color:#A61000'" : "style='color:#000000'";
						$issued_qty = $row["ISSUE_QNTY"];
						$total_issued_qty += $issued_qty;
						$fso_no = ($row["ISSUE_BASIS"]==8)? $requisition_demand_booking_array[$row["REQUISITION_NO"]]['fso_no']:$wo_booking_array[$row["BOOKING_ID"]]['fso_no'];
						$booking_no = ($row["ISSUE_BASIS"]==8)? $requisition_demand_booking_array[$row["REQUISITION_NO"]]['booking_no']:$wo_booking_array[$row["BOOKING_ID"]]['booking_no'];
						$int_ref = $booking_data_arr[$booking_no]['INTERNAL_REF'];
						?>
							<tr style="text-align: center;" bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td><?= $i?></td>
								<td><?= change_date_format($row["ISSUE_DATE"]);?></td>
								<td><?= $row["ISSUE_NUMBER"];?></td>
								<td><?= $row["WO_DEMAND_NO"];?></td>
								<td><?= $fso_no;?></td>
								<td><?= $booking_no;?></td>
								<td><?= $int_ref; ?></td>
								<td align="center"><?= $issued_qty ?></td>
							
							</tr>
						<?
						$i++;
					}
					?>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="7" align="right">Total</td>
							<td style="text-align:center;"><?=$total_issued_qty ?></td>
						</tr>
					</tfoot>
					
				</tbody>
			</table>    
		</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script> 
	</html>
	<?
	exit();
}

if ($action == "yarn_issue_rtn_popup") 
{
	echo load_html_head_contents("Popup Info", "../../../../", 1, 1, $unicode);
	extract($_REQUEST);

	list($prod_id,$fso_id,$po_id,$sample_booking_no,$fab_booking_no) = explode("___",$ref_data);
	$fab_booking_no_arr =  explode("__",$fab_booking_no);
	$sample_booking_no_arr =  explode("__",$sample_booking_no);
	$booking_no_arr = array_merge($fab_booking_no_arr,$sample_booking_no_arr);
	$booking_nos = "'".implode("','",$booking_no_arr)."'";
	$fso_ids = str_replace("__",",",$fso_id);
	$po_id = str_replace("__",",",$po_id);

	$requisition_sql = "select a.requisition_no as REQUISITION_NO,b.booking_no as BOOKING_NO,b.po_id as PO_ID,b.is_sales AS IS_SALES,c.job_no AS FSO_NO,c.po_job_no AS PO_JOB_NO  from PPL_YARN_REQUISITION_ENTRY a,PPL_PLANNING_ENTRY_PLAN_DTLS b left join FABRIC_SALES_ORDER_MST c on b.po_id=c.id and c.id in ($fso_ids)  where a.prod_id = $prod_id and a.knit_id=b.dtls_id and b.booking_no in ($booking_nos)";

	//echo $requisition_sql; die;
	$requisition_result = sql_select($requisition_sql); 
	foreach($requisition_result as $row)
	{
		$requisition_demand_booking_array[$row['REQUISITION_NO']]['booking_no']= $row['BOOKING_NO'];
		if($row['IS_SALES']==1)
		{		
			$requisition_demand_booking_array[$row['REQUISITION_NO']]['fso_no']= $row['FSO_NO'];
		}	
	}

	$wo_sql = "select b.mst_id as BOOKING_ID, b.job_no as JOB_NO, (b.booking_no || b.fab_booking_no) as BOOKING_NO,a.is_sales as IS_SALES from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.product_id = $prod_id and (b.booking_no in ($booking_nos) or b.fab_booking_no in ($booking_nos))";
	$wo_result = sql_select($wo_sql); 
	$wo_booking_array= array();
	foreach($wo_result as $row)
	{
		$wo_booking_array[$row['BOOKING_ID']]['booking_no']= $row['BOOKING_NO'];
		if($row['IS_SALES']==1)
		{
			$wo_booking_array[$row['BOOKING_ID']]['fso_no']= $row['JOB_NO'];
		}
	}

	//for sample booking
	$sample_booking_nos = "'".implode("','",$sample_booking_no_arr)."'";
	$sample_booking_sql = "select b.BOOKING_NO, a.BUYER_NAME as buyer, a.STYLE_REF_NO, a.INTERNAL_REF from SAMPLE_DEVELOPMENT_MST a, SAMPLE_DEVELOPMENT_YARN_DTLS b where b.mst_id = a.id and b.booking_no in ($sample_booking_nos)";
	$booking_info_smpl = sql_select($sample_booking_sql);

	foreach($booking_info_smpl as $row)
	{
		$booking_data_arr[$row['BOOKING_NO']]['INTERNAL_REF'] =  $row['INTERNAL_REF'];
	}
	unset($booking_info_smpl);

	//for fabric booking 
	$fabric_booking_nos = "'".implode("','",$fab_booking_no_arr)."'";
	$booking_info_sql = "select a.booking_no as BOOKING_NO,c.grouping as GROUPING from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_id=d.id and b.booking_no in ($fabric_booking_nos) and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
	//echo $booking_info_sql; die;
	$booking_info = sql_select($booking_info_sql);
	foreach($booking_info as $row)
	{
		$booking_data_arr[$row['BOOKING_NO']]['INTERNAL_REF'] = $row['GROUPING'];
	}
	unset($booking_info);

	
	$issue_rtn_sql = "SELECT a.transaction_date, a.cons_quantity     AS issue_return_qnty, a.prod_id, c.lot, e.RECV_NUMBER       AS issue_return_no, a.REQUISITION_NO, a.job_no, b.ISSUE_NUMBER, b.ISSUE_DATE,e.booking_id AS BOOKING_ID, e.booking_no AS WO_DEMAND_NO, a.RECEIVE_BASIS FROM inv_transaction a, INV_ISSUE_MASTER  b, product_details_master c, INV_RECEIVE_MASTER   e WHERE   a.prod_id = c.id AND a.mst_id = e.id   AND e.ISSUE_ID = b.id AND a.prod_id =$prod_id AND a.transaction_type IN (4) AND a.item_category = 1 AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.transaction_date DESC";
	//echo $issue_rtn_sql; die;
	$issue_rtn_result = sql_select($issue_rtn_sql);
	?>
	<body>
		<div align="center" style="width:98%;" >
			<table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th>SL</th>
						<th>Issue Return ID</th>
						<th>Issue Return Date</th>
						<th>Issue Date</th>
						<th>Issue ID</th>
						<th>Demand NO/ Wo No</th>
						<th>Booking NO</th>
						<th>FSO No</th>
						<th>IR/IB</th>
						<th>Issue Return Qty</th>
					</tr>
				</thead>
		
				<tbody style="background-color: white;">
					<?
					$i=1;
					$total_issued_rtn_qty = 0;
					foreach($issue_rtn_result as $row)
					{
						$bgcolor = ($i % 2 == 0)?"#E9F3FF":"#FFFFFF";
						$issued_rtn_qty = $row["ISSUE_RETURN_QNTY"];
						$total_issued_rtn_qty += $issued_rtn_qty;

						$fso_no = ($row["RECEIVE_BASIS"]==8)? $requisition_demand_booking_array[$row["REQUISITION_NO"]]['fso_no']:$wo_booking_array[$row["BOOKING_ID"]]['fso_no'];
						$booking_no = ($row["RECEIVE_BASIS"]==8)? $requisition_demand_booking_array[$row["REQUISITION_NO"]]['booking_no']:$wo_booking_array[$row["BOOKING_ID"]]['booking_no'];
						$int_ref = $booking_data_arr[$booking_no]['INTERNAL_REF'];
						
						?>
							<tr style="text-align: center;" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td><?= $i?></td>
								<td><?= $row["ISSUE_RETURN_NO"];?></td>
								<td><?= change_date_format($row["TRANSACTION_DATE"]);?></td>
								<td><?= change_date_format($row["ISSUE_DATE"]);?></td>
								<td><?= $row["ISSUE_NUMBER"];?></td>
								<td><?= $row["WO_DEMAND_NO"];?></td>
								<td><?= $booking_no;?></td>
								<td><?= $fso_no; ?></td>
								<td><?= $int_ref; ?></td>
								<td><?= $issued_rtn_qty; ?></td>
							
							</tr>
						<?
						$i++;
					}
					?>
					<tfoot>
						<tr style="background-color: white;" class="tbl_bottom">
							<td colspan="9" align="right">Total</td>
							<td align="center"><?=$total_issued_rtn_qty ?></td>
						</tr>
					</tfoot>
					
				</tbody>
			</table>    
		</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script> 
	</html>
	<?
	exit();
}

if ($action == "generate_report_allocation_date_wise")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$search_cond 		= "";
	$company_id 		= str_replace("'", "", $cbo_company_name);
	$buyer_id 			= str_replace("'", "", $cbo_buyer_name);
	$booking_no 		= str_replace("'", "", $txt_booking_no);
	$lot 				= str_replace("'", "", trim($txt_lot_no));
	$cbo_date_category  = str_replace("'", "", $cbo_date_category);
	$cbo_year  			= str_replace("'", "", $cbo_year_selection);

	if ($buyer_id==0) $buyer_id_cond ="";
	else $buyer_id_cond =" AND C.BUYER_NAME = ".$buyer_id;

	if ($booking_no=="") $booking_no_cond ="";
	else $booking_no_cond ="AND B.BOOKING_NO LIKE '%".$booking_no."%' ";

	if ($lot=="") $lot_cond ="";
	else $lot_cond =" AND A.LOT = '".$lot."'";

	if(trim($cbo_year)!=0)
	{
		if($db_type==0) $year_cond=" AND YEAR(c.insert_date) = ".$cbo_year;
		else if($db_type==2) $year_cond=" AND TO_CHAR(C.INSERT_DATE,'YYYY') = ".$cbo_year;
		else $year_cond="";
	}
	else
	{
		$year_cond="";
	}

	$allocation_date_cond="";
	if ($from_date != "" && $to_date != "")
	{
		if($cbo_date_category==1)
		{
			$allocation_date_cond .= " AND E.BOOKING_DATE BETWEEN '".date("d-M-Y", strtotime($from_date))."' AND '".date("d-M-Y", strtotime($to_date))."'";
		}
		if($cbo_date_category==2)
		{
			$allocation_date_cond .= " AND D.PUB_SHIPMENT_DATE BETWEEN '".date("d-M-Y", strtotime($from_date))."' AND '".date("d-M-Y", strtotime($to_date))."'";
		}
		if($cbo_date_category==3)
		{
			$allocation_date_cond = " AND TRUNC(B.INSERT_DATE) BETWEEN '" . date("d-M-Y", strtotime($from_date)) . "' AND '" . date("d-M-Y", strtotime($to_date)) . "'";
		}
	}

    //library array
	$company_name 		= return_field_value("company_name", "lib_company", "id=$company_id and status_active=1 and is_deleted=0","company_name");
	$company_short_name = return_field_value("company_short_name", "lib_company", "id=$company_id and status_active=1 and is_deleted=0","company_short_name");
	$buyer_library 		= return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_library 	= return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
	$yarn_count_library = return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$composition_library= return_library_array( "select id,composition_name from lib_composition_array", "id", "composition_name"  );

	$allocation_sql = "SELECT A.ID AS PROD_ID, E.BOOKING_DATE, A.YARN_COUNT_ID, A.YARN_COMP_TYPE1ST, A.YARN_TYPE, A.SUPPLIER_ID, A.LOT, A.AVG_RATE_PER_UNIT, B.BOOKING_NO, SUM(B.QNTY) ALLOCATED_QNTY, B.INSERT_DATE AS ALLOCATION_DATE, B.JOB_NO, B.ALLOCATION_DATE AS ALLO_DATE, C.BUYER_NAME,D.ID PO_ID, D.PO_NUMBER, D.PUB_SHIPMENT_DATE FROM INV_MATERIAL_ALLOCAT_HYSTORY B, WO_BOOKING_MST E, WO_PO_BREAK_DOWN D, WO_PO_DETAILS_MASTER C, PRODUCT_DETAILS_MASTER A WHERE C.COMPANY_NAME = ".$company_id.$buyer_id_cond.$booking_no_cond.$lot_cond.$allocation_date_cond.$year_cond." AND B.BOOKING_NO = E.BOOKING_NO AND B.PO_BREAK_DOWN_ID = D.ID AND B.JOB_NO = C.JOB_NO AND A.ID = B.ITEM_ID AND B.QNTY !=0 AND A.ITEM_CATEGORY_ID = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND D.STATUS_ACTIVE = 1 AND D.IS_DELETED = 0 AND E.STATUS_ACTIVE !=0 AND E.IS_DELETED != 1 GROUP BY A.ID, E.BOOKING_DATE, B.BOOKING_NO, D.ID, D.PO_NUMBER, D.PUB_SHIPMENT_DATE, A.YARN_COUNT_ID, A.YARN_COMP_TYPE1ST, A.YARN_TYPE, A.SUPPLIER_ID, A.LOT, A.AVG_RATE_PER_UNIT, B.INSERT_DATE, B.JOB_NO, B.ALLOCATION_DATE, C.BUYER_NAME ORDER BY A.ID, B.JOB_NO, B.BOOKING_NO, D.ID, D.PO_NUMBER, A.YARN_COUNT_ID, A.YARN_COMP_TYPE1ST, A.YARN_TYPE, A.LOT, B.INSERT_DATE, A.SUPPLIER_ID, C.BUYER_NAME ASC";
	//echo $allocation_sql;
	$allocation_result = sql_select($allocation_sql);
	$po_arr=$prod_arr=$rowspan_arr=$allcate_qty= array();
	foreach ($allocation_result as $row)
	{
		$po_arr[$row['PO_ID']]=$row['PO_ID'];
		$prod_arr[$row['PROD_ID']]=$row['PROD_ID'];

		if (isset($rowspan_arr[$row['PROD_ID']][$row['LOT']][$row['YARN_COUNT_ID']][$row['YARN_COMP_TYPE1ST']][$row['YARN_TYPE']][$row['SUPPLIER_ID']][$row['BOOKING_NO']][$row['JOB_NO']][$row['PO_NUMBER']]))
		{
			$allcate_qty[$row['PROD_ID']][$row['LOT']][$row['YARN_COUNT_ID']][$row['YARN_COMP_TYPE1ST']][$row['YARN_TYPE']][$row['SUPPLIER_ID']][$row['BOOKING_NO']][$row['JOB_NO']][$row['PO_NUMBER']]['allocated_qnty'] += $row['ALLOCATED_QNTY'];

			$rowspan_arr[$row['PROD_ID']][$row['LOT']][$row['YARN_COUNT_ID']][$row['YARN_COMP_TYPE1ST']][$row['YARN_TYPE']][$row['SUPPLIER_ID']][$row['BOOKING_NO']][$row['JOB_NO']][$row['PO_NUMBER']] += 1;
		}
		else
		{
			$allcate_qty[$row['PROD_ID']][$row['LOT']][$row['YARN_COUNT_ID']][$row['YARN_COMP_TYPE1ST']][$row['YARN_TYPE']][$row['SUPPLIER_ID']][$row['BOOKING_NO']][$row['JOB_NO']][$row['PO_NUMBER']]['allocated_qnty'] += $row[csf('ALLOCATED_QNTY')];

			$rowspan_arr[$row['PROD_ID']][$row['LOT']][$row['YARN_COUNT_ID']][$row['YARN_COMP_TYPE1ST']][$row['YARN_TYPE']][$row['SUPPLIER_ID']][$row['BOOKING_NO']][$row['JOB_NO']][$row['PO_NUMBER']] = 1;
		}
	}

	// Issue qty sql start
	if(!empty($po_arr))
	{
		/*
		$array_prod_arr=array_chunk($prod_arr,999);
		foreach($array_prod_arr as $prod_val)
		{
			$prod_var=implode(",",$prod_val);
			$prod_cond=" AND C.PROD_ID IN(".$prod_var.")";
		}

		$array_po_arr=array_chunk($po_arr,999);
		foreach($array_po_arr as $po_val)
		{
			$po_var=implode(",",$po_val);
			$order_cond=" AND C.PO_BREAKDOWN_ID IN(".$po_var.")";
		}
		$sql_issue = "SELECT A.ID, A.ISSUE_BASIS, B.REQUISITION_NO, C.PO_BREAKDOWN_ID, C.PROD_ID, SUM(C.QUANTITY) AS ISSUE_QTY FROM INV_ISSUE_MASTER A, INV_TRANSACTION B, ORDER_WISE_PRO_DETAILS C WHERE A.ID = B.MST_ID AND B.ID = C.TRANS_ID AND A.ISSUE_PURPOSE IN(1,2) AND C.TRANS_TYPE = 2 AND A.ENTRY_FORM = 3 AND B.ITEM_CATEGORY = 1 ".$order_cond.$prod_cond." AND A.STATUS_ACTIVE = 1 AND B.STATUS_ACTIVE = 1 AND C.STATUS_ACTIVE = 1 GROUP BY A.ID, A.ISSUE_BASIS, B.REQUISITION_NO, C.PO_BREAKDOWN_ID, C.PROD_ID";
		*/

		$sql_issue = "SELECT A.ID, A.ISSUE_BASIS, B.REQUISITION_NO, C.PO_BREAKDOWN_ID, C.PROD_ID, SUM(C.QUANTITY) AS ISSUE_QTY FROM INV_ISSUE_MASTER A, INV_TRANSACTION B, ORDER_WISE_PRO_DETAILS C WHERE A.ID = B.MST_ID AND B.ID = C.TRANS_ID AND A.COMPANY_ID = ".$company_id." AND A.ISSUE_PURPOSE IN(1,2) AND C.TRANS_TYPE = 2 AND A.ENTRY_FORM = 3 AND B.ITEM_CATEGORY = 1 AND A.STATUS_ACTIVE = 1 AND B.STATUS_ACTIVE = 1 AND C.STATUS_ACTIVE = 1 GROUP BY A.ID, A.ISSUE_BASIS, B.REQUISITION_NO, C.PO_BREAKDOWN_ID, C.PROD_ID";
		//echo $sql_issue;
		$result_issue = sql_select($sql_issue);
		$issue_id_arr=array();
		foreach ($result_issue as $row)
		{
			if (in_array($row['PO_BREAKDOWN_ID'], $po_arr) && in_array($row['PROD_ID'], $prod_arr))
			{
				$issue_arr[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']] += $row['ISSUE_QTY'];
				$issue_id_arr[$row['ID']] = $row['ID'];
				$issue_array_req[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]['issue_id'][$row['ID']] = $row['ID'];
			}
		}
		unset($result_issue);
	}
	// Issue qty sql end

	// Return qty sql start
	$issue_return_req_array = array();
	if(!empty($issue_id_arr))
	{
		/*
		$array_issue_id=array_chunk($issue_id_arr,999);
		$issue_cond=" AND (";
		foreach($array_issue_id as $issue_id_val)
		{
			$issue_id_var=implode(",",$issue_id_val);
			$issue_cond.=" B.ISSUE_ID IN(".$issue_id_var.") OR ";
		}
		$issue_cond=chop($issue_cond,'OR ');
		$issue_cond.=")";

		$sql_return = "SELECT B.ISSUE_ID, C.PO_BREAKDOWN_ID, C.PROD_ID, SUM(C.QUANTITY) AS ISSUE_RETURN_QTY FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, ORDER_WISE_PRO_DETAILS C WHERE A.ID = B.MST_ID AND C.TRANS_TYPE = 4 AND A.ENTRY_FORM = 9 AND B.ID = C.TRANS_ID AND B.ITEM_CATEGORY = 1 AND C.ISSUE_PURPOSE IN(1,2)".$prod_cond.$issue_cond." AND A.STATUS_ACTIVE = 1 AND B.STATUS_ACTIVE = 1 AND C.STATUS_ACTIVE = 1 GROUP BY B.ISSUE_ID, C.PO_BREAKDOWN_ID, C.PROD_ID";
		*/

		$sql_return = "SELECT B.ISSUE_ID, C.PO_BREAKDOWN_ID, C.PROD_ID, SUM(C.QUANTITY) AS ISSUE_RETURN_QTY FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, ORDER_WISE_PRO_DETAILS C WHERE A.ID = B.MST_ID AND A.COMPANY_ID = ".$company_id." AND C.TRANS_TYPE = 4 AND A.ENTRY_FORM = 9 AND B.ID = C.TRANS_ID AND B.ITEM_CATEGORY = 1 AND C.ISSUE_PURPOSE IN(1,2) AND A.STATUS_ACTIVE = 1 AND B.STATUS_ACTIVE = 1 AND C.STATUS_ACTIVE = 1 GROUP BY B.ISSUE_ID, C.PO_BREAKDOWN_ID, C.PROD_ID";
		$result_return = sql_select($sql_return);
		foreach ($result_return as $row)
		{
			if (in_array($row['PO_BREAKDOWN_ID'], $po_arr) && in_array($row['ISSUE_ID'], $issue_id_arr))
			{
				$issue_return_req_array[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']][$row['ISSUE_ID']] += $row['ISSUE_RETURN_QTY'];
			}
		}
		unset($result_return);
	}
	//Return qty sql end
	?>
	<style type="text/css">
		table tbody tr td{ text-align: center; }
		.right-align{ text-align: right; }
	</style>
	<?
	ob_start();
	?>

	<div align="center">
		<table width="2500" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px"  rules="all" id="table_header_1">
		<thead>
			<tr class="form_caption" style="border:none;">
				<td colspan="21" align="center" style="border:none;font-size:16px; font-weight:bold" >Lot Wise Yarn Allocation Report</td>
			</tr>
			<tr style="border:none;">
				<td colspan="21" align="center" style="border:none; font-size:14px;">Company Name : <? echo $company_name; ?></td>
			</tr>
			<tr style="border:none;">
				<td colspan="21" align="center" style="border:none;font-size:12px; font-weight:bold"><? if ($from_date != "" || $to_date != "") echo "From " . change_date_format($from_date) . " To " . change_date_format($to_date) . ""; ?></td>
			</tr>
			<tr>
				<th width="25">SL</th>
				<th width="100">Product ID</th>
				<th width="100">Lot</th>
				<th width="100">Count</th>
				<th width="100">Composition</th>
				<th width="100">Type</th>
				<th width="100">Supplier</th>
				<th width="130">Booking No.</th>
				<th width="100">Booking Date</th>
				<th width="130">Job NO.</th>
				<th width="130">Order No.</th>
				<th width="100">Ship date</th>
				<th width="100">Allocation Date</th>
				<th width="80">Rate</th>
				<th width="70">Allocated</th>
				<th width="70">Issue</th>
				<th width="70">Issue Rtn.</th>
				<th width="70">Balance</th>
				<th width="120">Buyer</th>
				<th width="120">Company</th>
				<th width="100">Age Up To All/Date</th>
			</tr>
		</thead>
		</table>
		<div style="width:2520px; overflow-y:scroll; max-height:250px" id="scroll_body">
	        <table width="2502" border="1" cellpadding="2" style="font:'Arial Narrow';"  cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?php
					$i = 1;
					$r=1;
					$item_arr = array();
					if(!empty($allocation_result))
					{
						foreach ($allocation_result as $allocation_key=>$row)
						{
							$allocation_qnty = $allcate_qty[$row['PROD_ID']][$row['LOT']][$row['YARN_COUNT_ID']][$row['YARN_COMP_TYPE1ST']][$row['YARN_TYPE']][$row['SUPPLIER_ID']][$row['BOOKING_NO']][$row['JOB_NO']][$row['PO_NUMBER']]['allocated_qnty'];
							$issue_qnty = $issue_arr[$row['PO_ID']][$row['PROD_ID']];
							$issue_ids = $issue_array_req[$row['PO_ID']][$row['PROD_ID']]['issue_id'];
							$return_qnty=0;
							foreach ($issue_ids as $issue_id)
							{
								if($issue_return_req_array[$row['PO_ID']][$row['PROD_ID']][$issue_id]>0)
								{
									$return_qnty = $issue_return_req_array[$row['PO_ID']][$row['PROD_ID']][$issue_id];
								}
							}

							$bgcolor = ($i % 2 == 0)?"#E9F3FF":"#FFFFFF";
							$stylecolor = ($row['transaction_type'] == 2)? "style='color:#A61000'" : "style='color:#000000'";
							$ageOfDays = datediff("d", $row['ALLOCATION_DATE'], date("Y-m-d"));
							$allocated_qnty_title = ($row['ALLOCATED_QNTY'] < 0)?"Quantity Decreased":"Quantity Increased";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="25"><? echo $i;?></td>
								<td width="100"><? echo $row['PROD_ID'];?></td>
								<td width="100"><? echo $row['LOT'];?></td>
								<td width="100"><? echo $yarn_count_library[$row['YARN_COUNT_ID']];?></td>
								<td width="100"><? echo $composition_library[$row['YARN_COMP_TYPE1ST']];?></td>
								<td width="100"><? echo $yarn_type[$row['YARN_TYPE']];?></td>
								<td width="100"><? echo $supplier_library[$row['SUPPLIER_ID']];?></td>
								<td width="130"><? echo $row['BOOKING_NO'];?></td>
								<td width="100"><? echo date("d-M-Y", strtotime($row['BOOKING_DATE']));?></td>
								<td width="130"><? echo $row['JOB_NO'];?></td>
								<td width="130"><? echo $row['PO_NUMBER'];?></td>
								<td width="100"><? echo date("d-M-Y",strtotime($row['PUB_SHIPMENT_DATE']));?></td>
								<td width="100"><? echo date("d-M-Y",strtotime($row['ALLOCATION_DATE']));?></td>
								<td width="80"><? echo $row['AVG_RATE_PER_UNIT'];?></td>
								<td width="70" class="right-align" title="<? echo $allocated_qnty_title; ?>">
								<? echo number_format($row['ALLOCATED_QNTY'],2,".",""); $qty +=$row['ALLOCATED_QNTY']; ?>
								</td>
								<? if ($r==1)
								{
									$rspan = $rowspan_arr[$row['PROD_ID']][$row['LOT']][$row['YARN_COUNT_ID']][$row['YARN_COMP_TYPE1ST']][$row['YARN_TYPE']][$row['SUPPLIER_ID']][$row['BOOKING_NO']][$row['JOB_NO']][$row['PO_NUMBER']];
									?>
									<td width="70" class="right-align" style="vertical-align:middle;" rowspan="<? echo $rspan; ?>"><? echo number_format($issue_qnty,2,".","");?></td>

									<td width="70" class="right-align" style="vertical-align:middle;" rowspan="<? echo $rspan; ?>"><? echo number_format($return_qnty,2,".","");?></td>
									<td width="70" class="right-align" style="vertical-align:middle;" rowspan="<? echo $rspan; ?>"><? $balance = $allocation_qnty - $issue_qnty + $return_qnty;
									echo number_format($balance, 2);?></td>
									<?
									$total_issue_qnty += $issue_qnty;
									$total_issue_rtn_qnty += $return_qnty;
									$total_balance += $balance;
								}

								if($rspan==$r)
								{
									$r=0;
								}
								?>
								<td width="120"><? echo $buyer_library[$row['BUYER_NAME']];?></td>
								<td width="120"><? echo $company_short_name; ?></td>
								<td width="100"><? echo $ageOfDays; ?></td>
							</tr>
							<?
							$total_allocation_qnty +=$row['ALLOCATED_QNTY'];
							$r++;
							$i++;
						}
						?>
						<tr>
							<th style="border-bottom: 1px solid #8DAFDA;padding: 1px;" colspan="21"></th>
						</tr>
						<?
					}
					else
					{
						echo "<tr><th colspan='21' style='color:red;'>No Data Found</th></tr>";
					}
					?>
			</table>
		</div>
		<table width="2502" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
			<tr class="tbl_bottom">
				<td width="25">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="120">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="130">&nbsp;</td>
				<td width="130">&nbsp;</td>
				<td width="110">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="80" class="right-align" style="font-weight: bold; font-size:16px;">Grand Total</td>
				<td width="70" class="right-align" id="value_total_allocation_qty"></td>
				<td width="70" class="right-align" id="value_total_issue_qty"></td>
				<td width="70" class="right-align" id="value_total_issue_return_qty"></td>
				<td width="70" class="right-align" id="value_total_balance"></td>
				<td width="120">&nbsp;</td>
				<td width="120">&nbsp;</td>
				<td width="100">&nbsp;</td>
			</tr>
		</table>
	</div>
		<br />
		<?
		$html = ob_get_contents();
		ob_clean();
		foreach (glob("*.xls") as $filename) {
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$html**$filename**3";
		exit();
}

if ($action == "generate_report_4")
{
	$process = array(&$_POST);
	//var_dump($process);die;
	extract(check_magic_quote_gpc($process));

	$search_cond = "";
	$company_id = str_replace("'", "", $cbo_company_name);
	$buyer_id = str_replace("'", "", $cbo_buyer_name);
	$cbo_is_sales = str_replace("'", "", $cbo_is_sales);
	$internal_ref = str_replace("'", "", $txt_internal_ref);
	$booking_no = str_replace("'", "", $txt_booking_no);
	$sales_ord_no = str_replace("'", "", $txt_sales_no);
	$lot = str_replace("'", "", trim($txt_lot_no));
	$cbo_year = str_replace("'", "", $cbo_year_selection);

	if ($buyer_id==0) $buyer_id_cond =""; else $buyer_id_cond =" and c.buyer_name=$buyer_id ";

	if ($cbo_is_sales!=1)
	{
		$is_sales_cond ="  and b.is_sales<>1";
	}
	else
	{
		if ($sales_ord_no=="") $sales_ord_no_cond =""; else $sales_ord_no_cond =" and d.job_no_prefix_num like '%$sales_ord_no%' ";
		$is_sales_cond =" and b.is_sales=1";
	}

	if ($internal_ref=="") $internal_ref_cond =""; else $internal_ref_cond =" and d.grouping like '%$internal_ref%' ";
	if ($internal_ref=="") $internal_ref_cond2 =""; else $internal_ref_cond2 =" and c.grouping like '%$internal_ref%' ";
	if ($booking_no=="") $booking_no_cond =""; else $booking_no_cond =" and b.booking_no like '%$booking_no%' ";

	if ($lot=="") $lot_cond =""; else $lot_cond =" and a.lot='$lot' ";


	if(trim($cbo_year)!=0)
	{
		if($sales_ord_no=="")
		{
			if($db_type==0) $year_cond=" AND YEAR(c.insert_date) = ".$cbo_year;
			else if($db_type==2) $year_cond=" AND TO_CHAR(C.INSERT_DATE,'YYYY') = ".$cbo_year;
			else $year_cond="";
		}
		else
		{
			if($db_type==0) $year_cond=" AND YEAR(d.insert_date) = ".$cbo_year;
			else if($db_type==2) $year_cond=" AND TO_CHAR(d.INSERT_DATE,'YYYY') = ".$cbo_year;
			else $year_cond="";
		}
	}
	else
	{
		$year_cond="";
	}


	if ($db_type == 0) {
		if ($from_date != "" && $to_date != ""){
			$allocation_date_cond = "and b.allocation_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
		}

	}
	else {
		if ($from_date != "" && $to_date != ""){
			$allocation_date_cond .= " and b.allocation_date  between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
		}
	}

    //library array
	$company_name = return_field_value("company_name", "lib_company", "id=$company_id and status_active=1 and is_deleted=0","company_name");
	$company_short_name = return_field_value("company_short_name", "lib_company", "id=$company_id and status_active=1 and is_deleted=0","company_short_name");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
	$yarn_count_library=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$composition_library=return_library_array( "select id,composition_name from lib_composition_array", "id", "composition_name"  );

    if($cbo_is_sales!=1)
    {
    	$allocation_sql = "SELECT a.id as prod_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_type, a.supplier_id, a.lot, b.booking_no, sum(b.qnty) allocated_qnty, b.allocation_date, b.job_no, c.buyer_name, d.id po_id, d.po_number, d.pub_shipment_date,d.grouping, e.remarks from INV_MATERIAL_ALLOCATION_DTLS b,wo_po_break_down d,wo_po_details_master c,product_details_master a, INV_MATERIAL_ALLOCATION_MST e where e.id = b.mst_id and c.company_name=$company_id $buyer_id_cond $internal_ref_cond $booking_no_cond $lot_cond $allocation_date_cond and b.po_break_down_id=d.id and b.job_no=c.job_no and a.id=b.item_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in (1,3) and d.is_deleted=0 group by a.id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_type, a.supplier_id, a.lot, b.booking_no, d.id, d.po_number, d.pub_shipment_date,d.grouping, b.allocation_date, b.job_no, c.buyer_name, e.remarks order by a.id,a.lot, a.yarn_count_id, a.yarn_comp_type1st,a.yarn_type,a.supplier_id,b.booking_no,b.job_no,d.po_number,d.id,d.grouping,b.allocation_date,c.buyer_name asc";
    }
    else
    {
		$allocation_sql =  "SELECT a.id  AS prod_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_type, a.supplier_id, a.lot, b.mst_id, b.booking_no, b.qnty allocated_qnty, b.allocation_date, b.job_no,b.is_sales,d.buyer_id as buyer_name, d.id AS po_id,(case when d.within_group=1 then listagg(c.po_number,',') within group(order by c.id) else CAST( d.job_no_prefix_num AS VARCHAR(200) ) end) AS po_number,(CASE WHEN d.within_group = 1 THEN LISTAGG (c.pub_shipment_date, ',') WITHIN GROUP (ORDER BY c.id) ELSE NULL END)
             AS PUB_SHIPMENT_DATE,(case when d.within_group=1 then listagg(c.grouping,',') within group(order by c.id) else null end) AS GROUPING,d.within_group,d.po_buyer FROM inv_mat_allocation_dtls_log b,fabric_sales_order_mst d left join wo_po_break_down c on d.po_job_no= c.job_no_mst $internal_ref_cond2, product_details_master a WHERE d.company_id = $company_id $booking_no_cond $lot_cond $allocation_date_cond $sales_ord_no_cond $year_cond $is_sales_cond AND b.po_break_down_id = d.id AND b.job_no = d.job_no AND a.id = b.item_id AND a.status_active = 1 AND a.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 GROUP BY a.id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_type, a.supplier_id, a.lot, b.mst_id, b.booking_no,b.qnty, d.id,d.job_no_prefix_num, b.allocation_date, b.job_no,b.is_sales,d.buyer_id,d.within_group,d.po_buyer order by a.id,a.lot, a.yarn_count_id, a.yarn_comp_type1st,a.yarn_type,a.supplier_id, b.mst_id,b.booking_no,b.job_no,d.id,b.allocation_date,d.buyer_id asc";
    }

	//and b.status_active=1 and b.is_deleted=0
	// echo $allocation_sql;die;
	$allocation_result = sql_select($allocation_sql);
	$po_arr=$prod_arr=$rowspan_arr=$allcate_qty=$checked_allocation= array();
	foreach ($allocation_result as $row)
	{
		$po_arr[$row[csf("po_id")]]=$row[csf("po_id")];
		$prod_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];

		if($row[csf("is_sales")]!=1)
		{
			if (isset($rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]))
			{
				$allcate_qty[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]['allocated_qnty']+=$row[csf('allocated_qnty')];

				$rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]+=1;
			}
			else
			{
				$allcate_qty[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]['allocated_qnty']+=$row[csf('allocated_qnty')];

				$rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]=1;
			}
		}
		else
		{

			if( $checked_allocation[$row[csf('mst_id')]]=="" )
			{
				$checked_allocation[$row[csf('mst_id')]] = $row[csf('mst_id')];
				$allcate_qty[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]['allocated_qnty']+=$row[csf('allocated_qnty')];
			}

			$allocation_po_prod[$row[csf('po_id')]][$row[csf('prod_id')]] +=1;

		}

	}

	// Issue qty sql start
	if(!empty($po_arr))
	{
		$array_prod_arr=array_chunk($prod_arr,999);
		foreach($array_prod_arr as $prod_val)
		{
			$prod_var=implode(",",$prod_val);
			$prod_cond=" and c.prod_id in($prod_var)";
		}

		$array_po_arr=array_chunk($po_arr,999);
		foreach($array_po_arr as $po_val)
		{
			$po_var=implode(",",$po_val);
			$order_cond=" and c.po_breakdown_id in($po_var)";
		}

		$sql_issue = "SELECT a.id,a.issue_basis,b.requisition_no,c.po_breakdown_id,c.prod_id, sum(c.quantity) as issue_qty, max(a.issue_date) as max_issue_date from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.issue_purpose in(1,2) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 $order_cond $prod_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by a.id,a.issue_basis,b.requisition_no,c.po_breakdown_id,c.prod_id";
		//echo $sql_issue;

		$result_issue = sql_select($sql_issue);
		$issue_id_arr=array();
		foreach ($result_issue as $row)
		{
			$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_qty")];
			$issue_id_arr[$row[csf("id")]] = $row[csf("id")];
			$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_id"][$row[csf("id")]] = $row[csf("id")];
			$issue_date_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]['max_issue_date'] = $row[csf("max_issue_date")];
		}
	}

	// echo "<pre>";
	// print_r($issue_arr);
	// echo "</pre>";
	// Issue qty sql end

	// Return qty sql start
	$issue_return_req_array = array();
	if(!empty($issue_id_arr))
	{
		$array_issue_id=array_chunk($issue_id_arr,999);
		$issue_cond=" and (";
		foreach($array_issue_id as $issue_id_val)
		{
			$issue_id_var=implode(",",$issue_id_val);
			$issue_cond.=" b.issue_id in($issue_id_var) or ";
		}
		$issue_cond=chop($issue_cond,'or ');
		$issue_cond.=")";

		$sql_return = "SELECT B.ISSUE_ID, C.PO_BREAKDOWN_ID, C.ID, C.PROD_ID, C.QUANTITY FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, ORDER_WISE_PRO_DETAILS C WHERE A.ID=B.MST_ID AND C.TRANS_TYPE=4 AND A.ENTRY_FORM=9 AND B.ID=C.TRANS_ID AND B.ITEM_CATEGORY=1 AND C.ISSUE_PURPOSE IN(1,2) AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND C.STATUS_ACTIVE=1".$prod_cond.$issue_cond;
		$result_return = sql_select($sql_return);
		$duplicate_check = array();
		foreach ($result_return as $row)
		{
			if($duplicate_check[$row['ID']] != $row['ID'])
			{
				$duplicate_check[$row['ID']] = $row['ID'];
				$issue_return_req_array[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']][$row['ISSUE_ID']] += $row['QUANTITY'];
			}
		}
	}
	// Return qty sql end


	// booking requisition start

	$sql_req = "select a.booking_no, b.prod_id, sum(b.YARN_QNTY) as req_qty from PPL_PLANNING_ENTRY_PLAN_DTLS a, PPL_YARN_REQUISITION_ENTRY b where company_id = $company_id and a.dtls_id = b.knit_id group by a.booking_no, b.prod_id"; 
	$req = sql_select($sql_req);
		
	$requisition_arr = array();
	foreach($req as $row)
	{
		$requisition_arr[$row['BOOKING_NO']][$row['PROD_ID']]['REQUISITION_QTY'] = $row['REQ_QTY'];
	}

	// echo "<pre>";
	// print_r($requisition_arr);




	// booking requisition end

	?>
	<style type="text/css">
		table tbody tr td{ text-align: center; }
		.right-align{ text-align: right; }
	</style>
	<?
	ob_start();
	?>
	<div id="scroll_body">

		<table width="1800" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
			<thead>
				<tr class="form_caption" style="border:none;">
					<td colspan="21" align="center" style="border:none;font-size:16px; font-weight:bold" >Lot Wise Yarn Allocation Report</td>
				</tr>
				<tr style="border:none;">
					<td colspan="21" align="center" style="border:none; font-size:14px;">Company Name : <? echo $company_name; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="21" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? if ($from_date != "" || $to_date != "") echo "From " . change_date_format($from_date) . " To " . change_date_format($to_date) . ""; ?>
					</td>
				</tr>
				<tr>
					<th width="20">SL</th>
					<th width="80">Product ID</th>
					<th width="80">Lot</th>
					<th width="100">Count</th>
					<th width="100">Composition</th>
					<th width="100">Yarn Type</th>
					<th width="80">Supplier</th>
					<th width="80">Booking No.</th>
					<th width="80">Job NO.</th>
					<th width="80">Internal Ref.</th>
					<th width="80">Order No.</th>
					<th width="100">Shipment date</th>
					<th width="100">Allocation Date</th>
					<th width="80">Booking Req Qty</th>
					<th width="80">Allocated Qty</th>
					<th width="80">Booking Allocation Balance</th>
					<th width="80">Issue Qty</th>
					<th width="80">Issue Rtn Qty</th>
					<th width="80">Issue Balance</th>
					<!-- <th width="100">Last Issue Date</th> -->
					<!-- <th width="100">Days Passed From Last Issue Date</th> -->
					<!-- <th width="100">Yarn Allocated Age</th> -->
					<th width="80">Buyer</th>
					<th width="80">Company</th>
					<th width="80">Remarks</th>
				</tr>
			</thead>
		</table>

		<!-- <div style="width:1820; overflow-y:scroll; max-height:250px" id="scroll_body">
			
		</div> -->
		<table  width="1800" border="1" cellpadding="2" style="font:'Arial Narrow'; overflow-y:scroll; max-height:250px;"  cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<tbody>
					<?php

					$i = 1;
					$r=1;
					$item_arr = array();
					$issue_print = 0;
					if(!empty($allocation_result))
					{
						$total_req_qty = 0;
						$total_qty = 0;
						$total_qty_balance = 0;
						$total_issue = 0;
						$total_issue_return = 0;
						$total_issue_balance = 0;
						foreach ($allocation_result as $allocation_key=>$row)
						{
							$allocation_qnty = $allcate_qty[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]['allocated_qnty'];

							$max_issue_date = $issue_date_array[$row[csf("po_id")]][$row[csf("prod_id")]]['max_issue_date'];
							$ageOfDays = datediff("d", $issue_date_array[$row[csf("po_id")]][$row[csf("prod_id")]]['max_issue_date'], date("Y-m-d"));
							$dpfOfDays = datediff("d", $row[csf("allocation_date")], date("Y-m-d"));
							// $qty = 0;
							// $req_qty = 0;
							// $balance = 0;

							if($row[csf("is_sales")]==1)
	                        {
	                        	if ( (!empty($checked_arr)) && in_array($row[csf("po_id")]."**".$row[csf("prod_id")], $checked_arr))
	                        	{
	                        		$issue_qnty = 0;
	                        		$issue_ids = array();
	                        		$return_qnty=0;
	                        		$balance = 0;

	                        	}
	                        	else
	                        	{
									//echo $row[csf("po_id")]."==".$row[csf("prod_id")];
	                        		$issue_qnty = $issue_arr[$row[csf("po_id")]][$row[csf("prod_id")]];
	                        		$checked_arr[] = $row[csf("po_id")]."**".$row[csf("prod_id")];
	                        		$issue_ids = $issue_array_req[$row[csf("po_id")]][$row[csf("prod_id")]]["issue_id"];
	                        		$return_qnty=0;
	                        		foreach ($issue_ids as $issue_id) {
	                        			$return_qnty += $issue_return_req_array[$row[csf('po_id')]][$row[csf('prod_id')]][$issue_id];
	                        		}

	                        		$balance = $allocation_qnty - $issue_qnty + $return_qnty;
	                        	}
							}
							else
							{

								$issue_qnty = $issue_arr[$row[csf("po_id")]][$row[csf("prod_id")]];
								$issue_ids = $issue_array_req[$row[csf("po_id")]][$row[csf("prod_id")]]["issue_id"];
								$return_qnty=0;
								foreach ($issue_ids as $issue_id) {
									$return_qnty += $issue_return_req_array[$row[csf('po_id')]][$row[csf('prod_id')]][$issue_id];
								}

								$balance = $allocation_qnty - $issue_qnty + $return_qnty;
							}


	                        if($row[csf("is_sales")]==1)
	                        {
	                        	if($row[csf("within_group")]==1)
	                        	{
	                        		$buyer_id = $row[csf("po_buyer")];
	                        	}else{
	                        		$buyer_id = $row[csf("buyer_name")];
	                        	}
	                        }
	                        else
	                        {
	                        	$buyer_id = $row[csf("buyer_name")];
	                        }


							$bgcolor = ($i % 2 == 0)?"#E9F3FF":"#FFFFFF";
							$stylecolor = ($row["transaction_type"] == 2)? "style='color:#A61000'" : "style='color:#000000'";

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="20"><? echo $i;?></td>
								<td width="80"><? echo $row[csf("Prod_id")];?></td>
								<td width="80"><? echo $row[csf("lot")];?></td>
								<td width="100"><? echo $yarn_count_library[$row[csf("yarn_count_id")]];?></td>
								<td width="100"><? echo $composition_library[$row[csf("yarn_comp_type1st")]];?></td>
								<td width="100"><? echo $yarn_type[$row[csf("yarn_type")]];?></td>
								<td width="80"><? echo $supplier_library[$row[csf("supplier_id")]];?></td>
								<td width="80"><? echo $row[csf("booking_no")];;?></td>
								<td width="80"><? echo $row[csf("job_no")];?></td>
								<td width="80" class="center-align">
									<?
									echo implode(",",array_unique(explode(",",$row[csf("grouping")])));
									?>
								</td>
								<td width="80" class="center-align"><?echo $row[csf("po_number")];?></td>
								<td width="100">
									<?
									echo implode(",",array_unique(explode(",",change_date_format($row[csf("pub_shipment_date")]))));
									?>
								</td>
								<td width="100"><? echo change_date_format($row[csf("allocation_date")]);?></td>
								<td width="80" class="right-align">
									<?
									if(!$requisition_arr[$row['BOOKING_NO']][$row['PROD_ID']]['REQUISITION_QTY'])
									{ $req_qty = 0; } 
									else { $req_qty = $requisition_arr[$row['BOOKING_NO']][$row['PROD_ID']]['REQUISITION_QTY']; }
									echo $req_qty;
									$total_req_qty += $req_qty;
									?>
								</td>
								<td width="80" class="right-align" title="<? echo $row[csf("mst_id")];?>"><? echo number_format($row[csf("allocated_qnty")],2,".",""); $total_qty +=$row[csf("allocated_qnty")]; ?></td>
								<td width="80" class="right-align" ><? $balance_booking = $req_qty - $row[csf("allocated_qnty")]; echo $balance_booking; $total_qty_balance += $balance_booking; ?></td>
								<? if ($r==1)
								{
									?>
									<td width="80" class="right-align" style="vertical-align:middle;" rowspan="<? echo $rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]; ?>"><? echo number_format($issue_qnty,2,".",""); $total_issue += $issue_qnty;?> </td>

									<td width="80" class="right-align" style="vertical-align:middle;" rowspan="<? echo $rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]; ?>"><? echo number_format($return_qnty,2,".",""); $total_issue_return += $return_qnty; ?></td>
									<td width="80" class="right-align" style="vertical-align:middle;" rowspan="<? echo $rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]; ?>"><? echo number_format($balance, 2); $total_issue_balance += $balance;?></td>

									<?
								}

								if($row[csf("is_sales")]!=1)
	                        	{
									if($rowspan_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]][$row[csf('supplier_id')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('po_number')]]==$r) {
										$r=0;
									}

								}else{
									$r=0;
								}


								?>


								<td width="80"><? echo $buyer_library[$buyer_id];?></td>
								<td width="80"><? echo $company_short_name; ?></td>
								<td width="80"><? echo $row[csf('remarks')]; ?></td>
							</tr>
							<?

							$total_allocation_qnty +=$row[csf("allocated_qnty")];
							$total_issue_qnty += $issue_qnty;
							$total_issue_rtn_qnty += $return_qnty;
							$total_balance += $balance;
							$r++;
							$i++;

						}
					}
					else
					{
						echo "<tr><th colspan='19' style='color:red;'>No Data Found</th></tr>";
					}
					?>
				</tbody>
			</table>

		
		<table width="1800" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
				<tr class="tbl_bottom">
					<td width="20" >&nbsp;</td>
					<td width="80" >&nbsp;</td>
					<td width="80" >&nbsp;</td>
					<td width="100" >&nbsp;</td>
					<td width="100" >&nbsp;</td>		
					<td width="100" >&nbsp;</td>
					<td width="80" >&nbsp;</td>
					<td width="80" >&nbsp;</td>
					<td width="80" >&nbsp;</td>
					<td width="80" >&nbsp;</td>
					<td width="80" >&nbsp;</td>
					<td width="100" >&nbsp;</td>
					<td width="100" >&nbsp;</td>
					<td width="80" ><? echo $total_req_qty; ?></td>
					<td width="80" ><? echo $total_qty; ?></td>
					<td width="80" ><? echo $total_qty_balance; ?></td>
					<td width="80" ><? echo $total_issue; ?></td>
					<td width="80" ><? echo $total_issue_return; ?></td>
					<td width="80" ><? echo $total_issue_balance; ?></td>
					<td width="80" >&nbsp;</td>
					<td width="80" >&nbsp;</td>
					<td width="80" >&nbsp;</td>
				</tr>
			</table>
		
	</div>

	<br />
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**1";
	exit();
}
?>
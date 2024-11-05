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

//for load_drop_down_buyer
if ($action == "load_drop_down_buyer")
{
	echo create_drop_down("cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}

//for generate_report
if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_id = str_replace("'", "", $cbo_company_name);
	$buyer_id = str_replace("'", "", $cbo_buyer_name);
	$year = str_replace("'", "", $cbo_year);
	$job_no = str_replace("'", "", trim($txt_job_no));
	$booking_no = str_replace("'", "", trim($txt_booking_no));
	$lot = str_replace("'", "", trim($txt_lot_no));
	$value_with_zero = str_replace("'", "", $cbo_value_with);
	$qty_kg = str_replace("'", "", trim($txt_kg));
	$year_selection = str_replace("'", "", $cbo_year_selection);

	//for year
	$year_cond="";
	if($year !=0)
	{
		$year_cond=" AND TO_CHAR(C.INSERT_DATE, 'YYYY') = ".$cbo_year;
	}

	//for buyer
	if ($buyer_id==0) $buyer_id_cond ="";
	else $buyer_id_cond =" AND C.BUYER_NAME = ".$buyer_id;

	//for job_no
	if ($job_no=="") $job_no_cond ="";
	else $job_no_cond =" AND B.JOB_NO LIKE '%".$job_no."%'";
	//else $job_no_cond =" AND B.JOB_NO ='".$job_no."'";

	//for booking_no
	if ($booking_no=="") $booking_no_cond ="";
	else $booking_no_cond =" AND B.BOOKING_NO LIKE '%".$booking_no."%'";
	//else $booking_no_cond =" AND B.BOOKING_NO = '".$booking_no."'";

	//for lot
	if ($lot=="") $lot_cond ="";
	else $lot_cond =" AND A.LOT = '".$lot."'";

	//for date
	if ($from_date != "")
	{
		$allocation_date_cond = " AND B.ALLOCATION_DATE <= '".date("j-M-Y", strtotime($from_date))."'";
	}

    //library array
	$company_name = return_field_value("company_name", "lib_company", "id=$company_id and status_active=1 and is_deleted=0","company_name");
	$company_short_name = return_field_value("company_short_name", "lib_company", "id=".$company_id." and status_active=1 and is_deleted=0","company_short_name");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
	$yarn_count_library=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$composition_library=return_library_array( "select id,composition_name from lib_composition_array", "id", "composition_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$shipmentStatusArr = array(0 => "Full Pending", 1 => "Full Pending", 2 => "Partial Delivery", 3 => "Full Delivery/Closed");

	//for allocation qty
	$allocation_sql = "SELECT A.ID AS PROD_ID, A.YARN_COUNT_ID, A.YARN_COMP_TYPE1ST, A.YARN_TYPE, A.SUPPLIER_ID, A.LOT, A.COLOR, A.CURRENT_STOCK, A.ALLOCATED_QNTY, B.ID, B.BOOKING_NO, B.QNTY, B.ALLOCATION_DATE, B.JOB_NO, C.ID AS JOB_ID, C.BUYER_NAME FROM PRODUCT_DETAILS_MASTER A, INV_MATERIAL_ALLOCATION_DTLS B, WO_PO_DETAILS_MASTER C WHERE C.COMPANY_NAME=".$company_id.$year_cond.$buyer_id_cond.$job_no_cond.$booking_no_cond.$lot_cond.$allocation_date_cond." AND B.JOB_NO=C.JOB_NO AND A.ID=B.ITEM_ID AND A.CURRENT_STOCK >= 1 AND A.ALLOCATED_QNTY > 0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 ORDER BY A.ID, A.YARN_COUNT_ID, A.YARN_COMP_TYPE1ST, A.YARN_TYPE, A.LOT, B.JOB_NO, B.BOOKING_NO, B.ALLOCATION_DATE, A.SUPPLIER_ID, C.BUYER_NAME ASC";
	//echo $allocation_sql; die;
	//CURRENT_STOCK, ALLOCATED_QNTY
	$allocation_result = sql_select($allocation_sql);
	$prod_arr=array();
	$booking_no_arr=array();
	$job_no_arr=array();
	$allcate_qty= array();
	$duplicate_check = array();
	foreach ($allocation_result as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])
		{
			$duplicate_check[$row['ID']] = $row['ID'];
			$prod_arr[$row['PROD_ID']] = $row['PROD_ID'];
			$booking_no_arr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
			$job_no_arr[$row['JOB_ID']] = $row['JOB_ID'];

			$allcate_qty[$row['PROD_ID']][$row['JOB_NO']]['QNTY'] += $row['QNTY'];
			$allcate_qty[$row['PROD_ID']][$row['JOB_NO']]['LOT'] = $row['LOT'];
			$allcate_qty[$row['PROD_ID']][$row['JOB_NO']]['YARN_COUNT_ID'] = $row['YARN_COUNT_ID'];
			$allcate_qty[$row['PROD_ID']][$row['JOB_NO']]['YARN_COMP_TYPE1ST'] = $row['YARN_COMP_TYPE1ST'];
			$allcate_qty[$row['PROD_ID']][$row['JOB_NO']]['YARN_TYPE'] = $row['YARN_TYPE'];
			$allcate_qty[$row['PROD_ID']][$row['JOB_NO']]['SUPPLIER_ID'] = $row['SUPPLIER_ID'];
			$allcate_qty[$row['PROD_ID']][$row['JOB_NO']]['COLOR'] = $row['COLOR'];
			$allcate_qty[$row['PROD_ID']][$row['JOB_NO']]['BOOKING_NO'][$row['BOOKING_NO']] = $row['BOOKING_NO'];
			$allcate_qty[$row['PROD_ID']][$row['JOB_NO']]['BUYER_NAME'] = $row['BUYER_NAME'];
			$allcate_qty[$row['PROD_ID']][$row['JOB_NO']]['ALLOCATION_DATE'][] = $row['ALLOCATION_DATE'];
			$allcate_qty[$row['PROD_ID']][$row['JOB_NO']]['CURRENT_STOCK'] = $row['CURRENT_STOCK'];
		}
	}
	unset($allocation_result);
	/*echo "<pre>";
	print_r($allcate_qty);
	echo "</pre>";*/

	$con = connect();
	execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = ".$user_id);
	execute_query("DELETE FROM TMP_JOB_NO WHERE USERID = ".$user_id);
	oci_commit($con);

	//for product id
	$con = connect();
	foreach($prod_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_PROD_ID(PROD_ID, USERID) VALUES('".$val."', '".$user_id."')");
	}
  
	//for job no
	foreach($job_no_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_JOB_NO(JOB_NO, USERID) VALUES('".$val."', '".$user_id."')");
	}
	oci_commit($con);


	//for requisition qty
	$requisition_data = array();
	$requisition_no_arr = array();
	$requisition_booking_no_arr = array();
	if(!empty($booking_no_arr))
	{
		$sql_req = "SELECT A.BOOKING_NO, B.ID, B.PROD_ID, B.YARN_QNTY, B.REQUISITION_NO FROM PPL_PLANNING_ENTRY_PLAN_DTLS A, PPL_YARN_REQUISITION_ENTRY B, TMP_PROD_ID C WHERE A.DTLS_ID = B.KNIT_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.PROD_ID = C.PROD_ID AND C.USERID = ".$user_id;
		//echo $sql_req; die; //AND B.PROD_ID = C.PROD_ID AND C.USERID = ".$user_id
		$sql_req_rslt = sql_select($sql_req);
		$duplicate_check = array();
		foreach($sql_req_rslt as $row)
		{
			if($duplicate_check[$row['ID']] != $row['ID'])
			{
				$duplicate_check[$row['ID']] = $row['ID'];
				if(!empty($booking_no_arr[$row['BOOKING_NO']]))
				{
					$requisition_no_arr[$row['REQUISITION_NO']] = $row['REQUISITION_NO'];
					$requisition_booking_no_arr[$row['REQUISITION_NO']] = $row['BOOKING_NO'];
					$requisition_data[$row['PROD_ID']][$row['BOOKING_NO']]['qty'] += $row['YARN_QNTY'];
				}
			}
		}
		unset($sql_req_rslt);
	}

	/* echo "<pre>";
	print_r($requisition_booking_no_arr);
	die; */

	/**
	 * Date:08-07-2023  Work order consider
	 */
	$ydw_sql = "select a.id  wo_id, a.ydw_no, b.job_no, (b.fab_booking_no || b.booking_no) as booking_no, b.yarn_wo_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, tmp_prod_id c where  a.id = b.mst_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and b.product_id = c.prod_id and c.userid = $user_id $job_no_cond";
	//echo $ydw_sql; die;
	$ydw_result = sql_select($ydw_sql);

	$yarn_ysdw_array = array();
	foreach ($ydw_result as $row)
	{
		$yarn_ysdw_array[$row[csf('ydw_no')]][$row[csf('job_no')]]['qnty'] = $row[csf('yarn_wo_qty')];
		$yarn_ysdw_array[$row[csf('ydw_no')]][$row[csf('job_no')]]['booking_no'] = $row[csf('booking_no')];
		$yarn_ysdw_array[$row[csf('ydw_no')]][$row[csf('job_no')]]['job_no'] = $row[csf('job_no')];
		$all_job_arr[$row[csf('ydw_no')]][$row[csf('job_no')]] = $row[csf('job_no')];
	}

	//echo "<pre>";
	//print_r($yarn_ysdw_array);
	//die;
 
	//for issue qty
	$issue_data = array();
	$issue_id_arr = array();
	
	//$sql_issue = "SELECT A.ISSUE_PURPOSE,A.BOOKING_NO, B.ID, B.MST_ID, B.PROD_ID, B.REQUISITION_NO,B.JOB_NO, B.CONS_QUANTITY FROM INV_ISSUE_MASTER A, INV_TRANSACTION B, TMP_PROD_ID C WHERE A.ID = MST_ID AND A.ENTRY_FORM = 3  and (A.issue_purpose in(1,2) or A.issue_purpose in(1,2,7,12,15,38,46) and B.job_no is not null) AND A.ITEM_CATEGORY = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND A.COMPANY_ID = ".$company_id." AND B.ITEM_CATEGORY = 1 AND B.TRANSACTION_TYPE = 2 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND B.PROD_ID = C.PROD_ID AND C.USERID = ".$user_id;
	//echo $sql_issue; die;

	$sql_issue = "SELECT A.ISSUE_PURPOSE,A.BOOKING_NO, B.ID, B.MST_ID, B.PROD_ID, B.REQUISITION_NO,B.JOB_NO, D.QUANTITY FROM INV_ISSUE_MASTER A, INV_TRANSACTION B, TMP_PROD_ID C, ORDER_WISE_PRO_DETAILS D WHERE A.ID = MST_ID AND B.ID=D.TRANS_ID AND B.PROD_ID=D.PROD_ID AND A.ENTRY_FORM = 3  and (A.issue_purpose in(1,2) or A.issue_purpose in(1,2,7,12,15,38,46) and B.job_no is not null) AND A.ITEM_CATEGORY = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND A.COMPANY_ID = ".$company_id." AND B.ITEM_CATEGORY = 1 AND B.TRANSACTION_TYPE = 2 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND B.PROD_ID = C.PROD_ID AND C.USERID = ".$user_id;

	//echo $sql_issue; die;

	$sql_issue_rslt = sql_select($sql_issue);
	foreach($sql_issue_rslt as $row)
	{
		if( (!empty($requisition_no_arr[$row['REQUISITION_NO']]))  && ($row['ISSUE_PURPOSE']==1) ) 
		{
			$requsn_booking_no = $requisition_booking_no_arr[$row['REQUISITION_NO']];
			$issue_data[$row['PROD_ID']][$requsn_booking_no]['rqsn_issue_qty'] += $row['QUANTITY'];
		}

		if( $row['ISSUE_PURPOSE']!=1 ) 
		{
			$job_no = $yarn_ysdw_array[$row['BOOKING_NO']][$row['JOB_NO']]['job_no'];
			$issue_data[$row['PROD_ID']][$job_no]['wo_issue_qty'] += $row['QUANTITY'];
		}

		$issue_id_arr[$row['MST_ID']] = $row['MST_ID'];
		
	}
	unset($sql_issue_rslt);

	//echo "<pre>";
	//print_r($issue_data); die;
	
	//for issue return
	$issue_return_data = array();
	if(!empty($issue_id_arr))
	{
		$sql_issue_return = "SELECT A.RECEIVE_BASIS,A.BOOKING_NO, B.ISSUE_ID, B.ID, B.PROD_ID, CONS_QUANTITY FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, TMP_PROD_ID C WHERE A.ID = B.MST_ID AND A.ENTRY_FORM = 9 AND A.RECEIVE_BASIS in(1,3) AND A.ITEM_CATEGORY = 1 AND B.RECEIVE_BASIS in(1,3) AND B.ITEM_CATEGORY = 1 AND B.TRANSACTION_TYPE = 4 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.PROD_ID = C.PROD_ID AND C.USERID = ".$user_id;
		//echo $sql_issue_return;die;
		$sql_issue_return_rslt = sql_select($sql_issue_return);
		$duplicate_check = array();
		foreach($sql_issue_return_rslt as $row)
		{
			if($duplicate_check[$row['ID']] != $row['ID'])
			{
				$duplicate_check[$row['ID']] = $row['ID'];

				if(!empty($issue_id_arr[$row['ISSUE_ID']]))
				{
					if($row['RECEIVE_BASIS']==3)
					{
						$booking_no = $requisition_booking_no_arr[$row['BOOKING_NO']];
						$issue_return_data[$row['PROD_ID']][$booking_no]['rqsn_rtn_qty'] += $row['CONS_QUANTITY'];
						$issue_return_data[$row['PROD_ID']][$booking_no]['issue_id'] .= $row['ISSUE_ID'].",";
					}
					else
					{
						//echo $row['RECEIVE_BASIS']; die;
						$job_no_arr = $all_job_arr[$row[csf('BOOKING_NO')]];
						foreach($job_no_arr as $job_no)
						{
							$issue_return_data[$row['PROD_ID']][$job_no]['wo_rtn_qty'] += $row['CONS_QUANTITY'];
							$issue_return_data[$row['PROD_ID']][$job_no]['issue_id'] .= $row['ISSUE_ID'].",";
						}
					}	
				}
			}
		}
		unset($sql_issue_return_rslt);
		//echo "<pre>";print_r($issue_return_data);die;
	}

	//for status
	$sql_ship_status = "SELECT A.JOB_NO_MST, A.SHIPING_STATUS FROM WO_PO_BREAK_DOWN A, TMP_JOB_NO B WHERE A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND A.JOB_ID = B.jOB_NO AND B.USERID = ".$user_id;
	//echo $sql_ship_status;
	$sql_ship_status_rslt = sql_select($sql_ship_status);
	$ship_status_data = array();
	foreach($sql_ship_status_rslt as $row)
	{
		$ship_status_data[$row['JOB_NO_MST']][$row['SHIPING_STATUS']] = $row['SHIPING_STATUS'];
	}
	unset($sql_ship_status_rslt);

	$con = connect();
	$r_id3=execute_query("DELETE FROM TMP_PROD_ID where USERID=$user_id");
	$r_id4=execute_query("DELETE FROM TMP_JOB_NO where USERID=$user_id");

	if($r_id3 && $r_id4)
	{
		oci_commit($con);
	}

	?>
	<style type="text/css">
		table tbody tr td{ text-align: center;}
		.right-align{text-align: right;}
	</style>
	<?
	ob_start();
	$rpt_arr = array();
	$rpt_arr['sl']['caption'] = 'Sl No';
	$rpt_arr['sl']['width'] = '30';
	$rpt_arr['sl']['align'] = 'center';

	$rpt_arr['yarn_lot']['caption'] = 'Lot';
	$rpt_arr['yarn_lot']['width'] = '100';
	$rpt_arr['yarn_lot']['align'] = 'center';

	$rpt_arr['frist_allocation_date']['caption'] = 'First Allocation Date';
	$rpt_arr['frist_allocation_date']['width'] = '70';
	$rpt_arr['frist_allocation_date']['align'] = 'center';

	$rpt_arr['last_allocation_date']['caption'] = 'Last Allocation Date';
	$rpt_arr['last_allocation_date']['width'] = '70';
	$rpt_arr['last_allocation_date']['align'] = 'center';

	$rpt_arr['buyer_name']['caption'] = 'Buyer Name';
	$rpt_arr['buyer_name']['width'] = '120';
	$rpt_arr['buyer_name']['align'] = 'left';

	$rpt_arr['job_no']['caption'] = 'Job No';
	$rpt_arr['job_no']['width'] = '100';
	$rpt_arr['job_no']['align'] = 'center';

	$rpt_arr['booking_no']['caption'] = 'Booking No';
	$rpt_arr['booking_no']['width'] = '100';
	$rpt_arr['booking_no']['align'] = 'center';

	$rpt_arr['yarn_count']['caption'] = 'Count';
	$rpt_arr['yarn_count']['width'] = '60';
	$rpt_arr['yarn_count']['align'] = 'center';

	$rpt_arr['yarn_composition']['caption'] = 'Composition';
	$rpt_arr['yarn_composition']['width'] = '120';
	$rpt_arr['yarn_composition']['align'] = 'left';

	$rpt_arr['yarn_type']['caption'] = 'Yarn Type';
	$rpt_arr['yarn_type']['width'] = '100';
	$rpt_arr['yarn_type']['align'] = 'center';

	$rpt_arr['yarn_color']['caption'] = 'Color';
	$rpt_arr['yarn_color']['width'] = '120';
	$rpt_arr['yarn_color']['align'] = 'left';

	$rpt_arr['yarn_supplier']['caption'] = 'Supplier';
	$rpt_arr['yarn_supplier']['width'] = '120';
	$rpt_arr['yarn_supplier']['align'] = 'left';

	$rpt_arr['stock_qty']['caption'] = 'Stock Qty';
	$rpt_arr['stock_qty']['width'] = '100';
	$rpt_arr['stock_qty']['align'] = 'right';

	$rpt_arr['allocation_qty']['caption'] = 'Allocated Qty';
	$rpt_arr['allocation_qty']['width'] = '100';
	$rpt_arr['allocation_qty']['align'] = 'right';

	$rpt_arr['requisition_qty']['caption'] = 'Requisition Qty(Kg';
	$rpt_arr['requisition_qty']['width'] = '100';
	$rpt_arr['requisition_qty']['align'] = 'right';

	// $rpt_arr['requisition_balance_qty']['caption'] = 'Requisition Balance Qty(Kg';
	// $rpt_arr['requisition_balance_qty']['width'] = '100';
	// $rpt_arr['requisition_balance_qty']['align'] = 'right';

	$rpt_arr['issue_qty']['caption'] = 'Issue Qty(Kg';
	$rpt_arr['issue_qty']['width'] = '100';
	$rpt_arr['issue_qty']['align'] = 'right';

	$rpt_arr['issue_return_qty']['caption'] = 'Issue Return Qty(Kg)';
	$rpt_arr['issue_return_qty']['width'] = '100';
	$rpt_arr['issue_return_qty']['align'] = 'right';

	$rpt_arr['net_issued_qty']['caption'] = 'Net Issed Qty(Kg)';
	$rpt_arr['net_issued_qty']['width'] = '100';
	$rpt_arr['net_issued_qty']['align'] = 'right';

	$rpt_arr['possible_to_reduce_in_requsition']['caption'] = 'Possible to Reduce in Requsition';
	$rpt_arr['possible_to_reduce_in_requsition']['width'] = '100';
	$rpt_arr['possible_to_reduce_in_requsition']['align'] = 'right';

	$rpt_arr['possible_to_red_allocation']['caption'] = 'Possible to Red. Allocation';
	$rpt_arr['possible_to_red_allocation']['width'] = '100';
	$rpt_arr['possible_to_red_allocation']['align'] = 'right';

	$rpt_arr['need_unallocated_qty']['caption'] = 'Need Unallocation Qty(Kg)';
	$rpt_arr['need_unallocated_qty']['width'] = '100';
	$rpt_arr['need_unallocated_qty']['align'] = 'right';

	$rpt_arr['age_of_days']['caption'] = 'Age(days)';
	$rpt_arr['age_of_days']['width'] = '50';
	$rpt_arr['age_of_days']['align'] = 'center';


	$rpt_arr['status']['caption'] = 'Status';
	$rpt_arr['status']['width'] = '100';
	$rpt_arr['status']['align'] = 'left';

	$tbl_width = 0;
	$no_of_column = 0;
	foreach($rpt_arr as $key=>$val)
	{
		$tbl_width += $val['width'];
		$no_of_column += 1;
	}
	$dv_width = $tbl_width+20;
	?>
	<div>
		<div style="width:<? echo $dv_width; ?>px;">
			<table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="<? echo $no_of_column; ?>" align="center" style="border:none;font-size:16px; font-weight:bold" >Job Wise Yarn Allocation Report</td>
					</tr>
					<tr style="border:none;">
						<td colspan="<? echo $no_of_column; ?>" align="center" style="border:none; font-size:14px;">Company Name : <? echo $company_name; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="<? echo $no_of_column; ?>" align="center" style="border:none;font-size:12px; font-weight:bold"><? if ($from_date != "" || $to_date != "") echo "Date : " . change_date_format($from_date); ?></td>
					</tr>
					<tr>
						<?
						foreach($rpt_arr as $key=>$val)
						{
							if($key == 'status')
							{
								$val['width'] = '';
							}
							?>
							<th width="<? echo $val['width']; ?>"><? echo $val['caption']; ?></th>
							<?
						}
						?>
					</tr>
				</thead>
			</table>
		<div>
        <div style="width:<? echo $dv_width; ?>px; id="scroll_body" >
            <table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px" rules="all" id="table_body">
                <tbody>
                    <?php
                    $print_data = array();
                    if(!empty($allcate_qty))
                    {
                        $row_span_arr = array();
                        foreach ($allcate_qty as $prod=>$prd_arr)
                        {
                            foreach($prd_arr as $job=>$job_row)
                            {
								//for allocation qty
                                $allocation_qty = $job_row['QNTY'];
                                if($allocation_qty > 0)
                                {
                                    //for frist and last allocation date
                                    $frist_allocation_date = '';
                                    $last_allocation_date = '';
                                    foreach($job_row['ALLOCATION_DATE'] as $key=>$val)
                                    {
                                        if($key == 0)
                                        {
                                            $frist_allocation_date = date('d-m-Y', strtotime($val));
                                        }
                                        else
                                        {
                                            $last_allocation_date = date('d-m-Y', strtotime($val));
                                        }
                                    }

                                    //for requisition, issue and issue return qty
                                    $requisition_qty = 0;
                                    $rqsn_issue_qty = 0;
                                    $rqsn_issue_return_qty = 0;
									$issue_id = "";
                                    foreach($job_row['BOOKING_NO'] as $ke=>$va)
                                    {
                                        $requisition_qty += $requisition_data[$prod][$va]['qty'];
                                        $rqsn_issue_qty += $issue_data[$prod][$va]['rqsn_issue_qty'];
                                        $rqsn_issue_return_qty += $issue_return_data[$prod][$va]['rqsn_rtn_qty'];
										$issue_id = $issue_return_data[$prod][$va]['issue_id'];
                                    }
                                    //for net issued
                                    $rqsn_net_issued_qty = $rqsn_issue_qty - $rqsn_issue_return_qty;

                                    //for requisition balance qty
                                    $requisition_balance_qty = $requisition_qty - $rqsn_net_issued_qty;
									$requisition_balance_title = "Rqsn qty ". $requisition_qty ."-(Issue qty ". $rqsn_issue_qty ."- Issue Return qty ". $rqsn_issue_return_qty.")";
									
									if($issue_id!="")
									{
										$issue_id .= $issue_return_data[$prod][$job]['issue_id'].",";
									}

									$wo_issue_qty = $issue_data[$prod][$job]['wo_issue_qty'];
									$wo_issue_rtn_qty = $issue_return_data[$prod][$job]['wo_rtn_qty'];

									$issue_qty = ($rqsn_issue_qty+$wo_issue_qty);
									$issue_return_qty = ($rqsn_issue_return_qty+$wo_issue_rtn_qty);

									$net_issued_qty = ($issue_qty-$issue_return_qty);
                                    //for need unallocated qty
                                    $need_unallocated_qty = ($allocation_qty -$net_issued_qty);

                                    //for age of days
                                    $age_of_days = datediff("d", $frist_allocation_date, date("Y-m-d"));

                                    //for ship status
                                    $is_partial = 0;
                                    $is_pending = 0;
                                    $is_delivery = 0;

									//reduce in requsition and allocation
									$possible_to_reduce_in_requsition = $requisition_qty - $net_issued_qty;
									$possible_to_red_allocation = $allocation_qty - $requisition_qty;

                                    foreach($ship_status_data[$job] as $key=>$val)
                                    {
                                        if($val == 2)
                                        {
                                            $is_partial = 1;
                                        }
                                        else if($val == 3)
                                        {
                                            $is_delivery = 1;
                                        }
                                        else
                                        {
                                            $is_pending = 1;
                                        }
                                    }

                                    $ship_status = '';
                                    if($is_partial == 1)
                                    {
                                        $ship_status = $shipmentStatusArr[2];
                                    }
                                    else
                                    {
                                        if($is_pending == 1)
                                        {
                                            $ship_status = $shipmentStatusArr[1];
                                        }
                                        else
                                        {
                                            $ship_status = $shipmentStatusArr[3];
                                        }
                                    }

                                    //for value with zero
                                    if($value_with_zero == 0)
                                    {
                                        $row_span_arr[$prod]++;
                                        $print_data[$prod][$job]['frist_allocation_date'] = $frist_allocation_date;
                                        $print_data[$prod][$job]['last_allocation_date'] = $last_allocation_date;
                                        $print_data[$prod][$job]['job_no'] = $job;
                                        $print_data[$prod][$job]['buyer_name'] = $buyer_library[$job_row['BUYER_NAME']];
                                        $print_data[$prod][$job]['booking_no'] = implode(', ',$job_row['BOOKING_NO']);
                                        $print_data[$prod][$job]['yarn_count'] = $yarn_count_library[$job_row['YARN_COUNT_ID']];
                                        $print_data[$prod][$job]['yarn_composition'] = $composition_library[$job_row['YARN_COMP_TYPE1ST']];
                                        $print_data[$prod][$job]['yarn_type'] = $yarn_type[$job_row['YARN_TYPE']];
                                        $print_data[$prod][$job]['yarn_color'] = $color_library[$job_row['COLOR']];
                                        $print_data[$prod][$job]['yarn_lot'] = $job_row['LOT'];
                                        $print_data[$prod][$job]['yarn_supplier'] = $supplier_library[$job_row['SUPPLIER_ID']];
                                        $print_data[$prod][$job]['stock_qty'] = $job_row['CURRENT_STOCK'];
                                        $print_data[$prod][$job]['allocation_qty'] = $allocation_qty;
                                        $print_data[$prod][$job]['requisition_qty'] = $requisition_qty;
                                        $print_data[$prod][$job]['requisition_balance_qty'] = $requisition_balance_qty;
                                        $print_data[$prod][$job]['issue_qty'] = $issue_qty;
                                        $print_data[$prod][$job]['issue_return_qty'] = $issue_return_qty;
                                        $print_data[$prod][$job]['net_issued_qty'] = $net_issued_qty;
                                        $print_data[$prod][$job]['possible_to_reduce_in_requsition'] = $possible_to_reduce_in_requsition;
                                        $print_data[$prod][$job]['possible_to_red_allocation'] = $possible_to_red_allocation;
                                        $print_data[$prod][$job]['need_unallocated_qty'] = $need_unallocated_qty;
                                        $print_data[$prod][$job]['age_days'] = $age_of_days;
                                        $print_data[$prod][$job]['ship_status'] = $ship_status;
										$print_data[$prod][$job]['issue_id'] = $issue_id;
                                    }
                                    else
                                    {
                                        if($qty_kg == '')
                                        {
                                            if($need_unallocated_qty != 0)
                                            {
                                                $row_span_arr[$prod]++;
                                                $print_data[$prod][$job]['frist_allocation_date'] = $frist_allocation_date;
                                                $print_data[$prod][$job]['last_allocation_date'] = $last_allocation_date;
                                                $print_data[$prod][$job]['job_no'] = $job;
                                                $print_data[$prod][$job]['buyer_name'] = $buyer_library[$job_row['BUYER_NAME']];
                                                $print_data[$prod][$job]['booking_no'] = implode(', ',$job_row['BOOKING_NO']);
                                                $print_data[$prod][$job]['yarn_count'] = $yarn_count_library[$job_row['YARN_COUNT_ID']];
                                                $print_data[$prod][$job]['yarn_composition'] = $composition_library[$job_row['YARN_COMP_TYPE1ST']];
                                                $print_data[$prod][$job]['yarn_type'] = $yarn_type[$job_row['YARN_TYPE']];
                                                $print_data[$prod][$job]['yarn_color'] = $color_library[$job_row['COLOR']];
                                                $print_data[$prod][$job]['yarn_lot'] = $job_row['LOT'];
                                                $print_data[$prod][$job]['yarn_supplier'] = $supplier_library[$job_row['SUPPLIER_ID']];
												$print_data[$prod][$job]['stock_qty'] = $job_row['CURRENT_STOCK'];
                                                $print_data[$prod][$job]['allocation_qty'] = $allocation_qty;
                                                $print_data[$prod][$job]['requisition_qty'] = $requisition_qty;
                                                $print_data[$prod][$job]['requisition_balance_qty'] = $requisition_balance_qty;
                                                $print_data[$prod][$job]['issue_qty'] = $issue_qty;
                                                $print_data[$prod][$job]['issue_return_qty'] = $issue_return_qty;
                                                $print_data[$prod][$job]['net_issued_qty'] = $net_issued_qty;
                                                $print_data[$prod][$job]['possible_to_reduce_in_requsition'] = $possible_to_reduce_in_requsition;
                                                $print_data[$prod][$job]['possible_to_red_allocation'] = $possible_to_red_allocation;
                                                $print_data[$prod][$job]['need_unallocated_qty'] = $need_unallocated_qty;
                                                $print_data[$prod][$job]['age_of_days'] = $age_of_days;
                                                $print_data[$prod][$job]['ship_status'] = $ship_status;
												$print_data[$prod][$job]['issue_id'] = $issue_id;
                                            }
                                        }
                                        else
                                        {
                                            if($need_unallocated_qty != 0 && $need_unallocated_qty > $qty_kg)
                                            {
                                                $row_span_arr[$prod]++;
                                                $print_data[$prod][$job]['frist_allocation_date'] = $frist_allocation_date;
                                                $print_data[$prod][$job]['last_allocation_date'] = $last_allocation_date;
                                                $print_data[$prod][$job]['job_no'] = $job;
                                                $print_data[$prod][$job]['buyer_name'] = $buyer_library[$job_row['BUYER_NAME']];
                                                $print_data[$prod][$job]['booking_no'] = implode(', ',$job_row['BOOKING_NO']);
                                                $print_data[$prod][$job]['yarn_count'] = $yarn_count_library[$job_row['YARN_COUNT_ID']];
                                                $print_data[$prod][$job]['yarn_composition'] = $composition_library[$job_row['YARN_COMP_TYPE1ST']];
                                                $print_data[$prod][$job]['yarn_type'] = $yarn_type[$job_row['YARN_TYPE']];
                                                $print_data[$prod][$job]['yarn_color'] = $color_library[$job_row['COLOR']];
                                                $print_data[$prod][$job]['yarn_lot'] = $job_row['LOT'];
                                                $print_data[$prod][$job]['yarn_supplier'] = $supplier_library[$job_row['SUPPLIER_ID']];
												$print_data[$prod][$job]['stock_qty'] = $job_row['CURRENT_STOCK'];
                                                $print_data[$prod][$job]['allocation_qty'] = $allocation_qty;
                                                $print_data[$prod][$job]['requisition_qty'] = $requisition_qty;
                                                $print_data[$prod][$job]['requisition_balance_qty'] = $requisition_balance_qty;
                                                $print_data[$prod][$job]['issue_qty'] = $issue_qty;
                                                $print_data[$prod][$job]['issue_return_qty'] =$issue_return_qty;
                                                $print_data[$prod][$job]['net_issued_qty'] = $net_issued_qty;
                                                $print_data[$prod][$job]['possible_to_reduce_in_requsition'] = $possible_to_reduce_in_requsition;
                                                $print_data[$prod][$job]['possible_to_red_allocation'] = $possible_to_red_allocation;
                                                $print_data[$prod][$job]['need_unallocated_qty'] = $need_unallocated_qty;
                                                $print_data[$prod][$job]['age_of_days'] = $age_of_days;
                                                $print_data[$prod][$job]['ship_status'] = $ship_status;
                                                $print_data[$prod][$job]['issue_id'] = $issue_id;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    /*echo "<pre>";
					print_r($print_data);
					echo "</pre>";*/

                    if(!empty($print_data))
                    {
                        $i = 1;
						$total_stock_qty = 0;
						$total_allocation_qty = 0;
						$total_requisition_qty = 0;
						$total_requisition_balance_qty = 0;
						$total_issue_qty =  0;
						$total_issue_return_qty =  0;
						$total_net_issued_qty =  0;
						$total_need_unallocated_qty =  0;
                        foreach ($print_data as $prod_id=>$prd_id_arr)
                        {
                            $z = 1;
                            foreach($prd_id_arr as $job_no=>$row)
                            {
                                //for bgcolor
                                $bgcolor = ($i % 2 == 0)?"#E9F3FF":"#FFFFFF";
                                $row_span = $row_span_arr[$prod_id];
                                if($row_span == 1)
                                {
                                    ?>
                                    <tr valign="middle" bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                        <td width="<? echo $rpt_arr['sl']['width']; ?>"><p><? echo $i;?></p></td>
                                        <td  title="<? echo $prod_id; ?>" width="<? echo $rpt_arr['yarn_lot']['width']; ?>"><p><? echo $row['yarn_lot']; ?></p></td>
                                        <td width="<? echo $rpt_arr['frist_allocation_date']['width']; ?>"><p><? echo $row['frist_allocation_date']; ?></p></td>
                                        <td width="<? echo $rpt_arr['last_allocation_date']['width']; ?>"><p><? echo $row['last_allocation_date']; ?></p></td>
                                        <td width="<? echo $rpt_arr['buyer_name']['width']; ?>"><p><? echo $row['buyer_name']; ?></p></td>
                                        <td width="<? echo $rpt_arr['job_no']['width']; ?>"><p><? echo $row['job_no']; ?></p></td>
                                        <td width="<? echo $rpt_arr['booking_no']['width']; ?>"><p><? echo $row['booking_no']; ?></p></td>
                                        <td width="<? echo $rpt_arr['yarn_count']['width']; ?>"><p><? echo $row['yarn_count']; ?></p></td>
                                        <td width="<? echo $rpt_arr['yarn_composition']['width']; ?>"><p><? echo $row['yarn_composition']; ?></p></td>
                                        <td width="<? echo $rpt_arr['yarn_type']['width']; ?>"><p><? echo $row['yarn_type']; ?></p></td>
                                        <td width="<? echo $rpt_arr['yarn_color']['width']; ?>"><p><? echo $row['yarn_color']; ?></p></td>
                                        <td width="<? echo $rpt_arr['yarn_supplier']['width']; ?>"><p><? echo $row['yarn_supplier']; ?></p></td>
                                        <td width="<? echo $rpt_arr['stock_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['stock_qty'],2,".",""); ?></p></td>
                                        <td width="<? echo $rpt_arr['allocation_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['allocation_qty'],2,".",""); ?></p></td>
                                        <td width="<? echo $rpt_arr['requisition_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['requisition_qty'],2,".","");?></p></td>
                                        
                                        <td width="<? echo $rpt_arr['issue_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['issue_qty'],2,".","");?></p></td>

                                        <td width="<? echo $rpt_arr['issue_return_qty']['width']; ?>" class="right-align" title="<? echo "Issue Id:".implode(",",array_unique(explode(",",chop($row['issue_id'],",")))); ?>">
											<p>
												<a href="#report_details" onClick="openmypage('<? echo implode(",",array_unique(explode(",",chop($row['issue_id'],",")))); ?>','<? echo $prod_id; ?>','yarn_issue_return_popup','Yarn Issue Return Details')"><? echo number_format($row['issue_return_qty'], 2); ?></a>
												<? //echo number_format($row['issue_return_qty'], 2);?>
											</p>
										</td>

                                        <td width="<? echo $rpt_arr['net_issued_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['net_issued_qty'],2,".","");?></p></td>
                                        <td width="<? echo $rpt_arr['possible_to_reduce_in_requsition']['width']; ?>" class="right-align"><p><? echo number_format($row['possible_to_reduce_in_requsition'],2,".","");?></p></td>
                                        <td width="<? echo $rpt_arr['possible_to_red_allocation']['width']; ?>" class="right-align"><p><? echo number_format($row['possible_to_red_allocation'],2,".","");?></p></td>
                                        <td width="<? echo $rpt_arr['need_unallocated_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['need_unallocated_qty'], 2);?></p></td>
                                        <td width="<? echo $rpt_arr['age_of_days']['width']; ?>" align="center"><p><? echo $row['age_of_days']; ?></p></td>
                                        <td><p><? echo $row['ship_status']; ?></p></td>
                                    </tr>
                                    <?
                                }
                                else
                                {
                                    if($z == 1)
                                    {
                                        $z++;
                                        ?>
                                        <tr valign="middle" bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                            <td width="<? echo $rpt_arr['sl']['width']; ?>"><p><? echo $i;?></p></td>
                                        	<td title="<? echo $prod_id; ?>" width="<? echo $rpt_arr['yarn_lot']['width']; ?>"><p><? echo $row['yarn_lot']; ?></p></td>
                                            <td width="<? echo $rpt_arr['frist_allocation_date']['width']; ?>"><p><? echo $row['frist_allocation_date']; ?></p></td>
                                            <td width="<? echo $rpt_arr['last_allocation_date']['width']; ?>"><p><? echo $row['last_allocation_date']; ?></p></td>
                                            <td width="<? echo $rpt_arr['buyer_name']['width']; ?>" style="vertical-align:middle;"><p><? echo $row['buyer_name']; ?></p></td>
                                            <td width="<? echo $rpt_arr['job_no']['width']; ?>" style="vertical-align:middle;"><p><? echo $row['job_no']; ?></p></td>
                                            <td width="<? echo $rpt_arr['booking_no']['width']; ?>" style="vertical-align:middle;"><p><? echo $row['booking_no']; ?></p></td>
                                            <td width="<? echo $rpt_arr['yarn_count']['width']; ?>"><p><? echo $row['yarn_count']; ?></p></td>
                                            <td width="<? echo $rpt_arr['yarn_composition']['width']; ?>"><p><? echo $row['yarn_composition']; ?></p></td>
                                            <td width="<? echo $rpt_arr['yarn_type']['width']; ?>"><p><? echo $row['yarn_type']; ?></p></td>
                                            <td width="<? echo $rpt_arr['yarn_color']['width']; ?>"><p><? echo $row['yarn_color']; ?></p></td>
                                            <td width="<? echo $rpt_arr['yarn_supplier']['width']; ?>"><p><? echo $row['yarn_supplier']; ?></p></td>
                                            <td width="<? echo $rpt_arr['stock_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['stock_qty'],2,".",""); ?></p></td>
                                            <td width="<? echo $rpt_arr['allocation_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['allocation_qty'],2,".",""); ?></p></td>
                                            <td width="<? echo $rpt_arr['requisition_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['requisition_qty'],2,".","");?></p></td>
                                            
                                            <td width="<? echo $rpt_arr['issue_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['issue_qty'],2,".","");?></p></td>
                                            <td width="<? echo $rpt_arr['issue_return_qty']['width']; ?>"class="right-align"><p><a href="#report_details" onClick="openmypage('<? echo implode(",",array_unique(explode(",",chop($row['issue_id'],",")))); ?>','<? echo $prod_id; ?>','yarn_issue_return_popup','Yarn Issue Return Details')"><? echo number_format($row['issue_return_qty'], 2); ?></a></p></td>
                                            <td width="<? echo $rpt_arr['net_issued_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['net_issued_qty'],2,".","");?></p></td>
											<td width="<? echo $rpt_arr['possible_to_reduce_in_requsition']['width']; ?>" class="right-align"><p><? echo number_format($row['possible_to_reduce_in_requsition'],2,".","");?></p></td>
                                        	<td width="<? echo $rpt_arr['possible_to_red_allocation']['width']; ?>" class="right-align"><p><? echo number_format($row['possible_to_red_allocation'],2,".","");?></p></td>
                                            <td width="<? echo $rpt_arr['need_unallocated_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['need_unallocated_qty'], 2);?></p></td>
                                            <td width="<? echo $rpt_arr['age_of_days']['width']; ?>" align="center"><p><? echo $row['age_of_days']; ?></p></td>
                                            <td style="vertical-align:middle;"><p><? echo $row['ship_status']; ?></p></td>
                                        </tr>
                                        <?
                                    }
                                    else
                                    {
                                        ?>
                                        <tr valign="middle" bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                            <td title="<? echo $prod_id; ?>" width="<? echo $rpt_arr['sl']['width']; ?>"><p><? echo $i;?></p></td>
                                        	<td width="<? echo $rpt_arr['yarn_lot']['width']; ?>"><p><? echo $row['yarn_lot']; ?></p></td>
                                            <td width="<? echo $rpt_arr['frist_allocation_date']['width']; ?>"><p><? echo $row['frist_allocation_date']; ?></p></td>
                                            <td width="<? echo $rpt_arr['last_allocation_date']['width']; ?>"><p><? echo $row['last_allocation_date']; ?></p></td>
                                            <td width="<? echo $rpt_arr['buyer_name']['width']; ?>" style="vertical-align:middle;"><p><? echo $row['buyer_name']; ?></p></td>
                                            <td width="<? echo $rpt_arr['job_no']['width']; ?>" style="vertical-align:middle;"><p><? echo $row['job_no']; ?></p></td>
                                            <td width="<? echo $rpt_arr['booking_no']['width']; ?>" style="vertical-align:middle;"><p><? echo $row['booking_no']; ?></p></td>
                                            <td width="<? echo $rpt_arr['yarn_count']['width']; ?>"><p><? echo $row['yarn_count']; ?></p></td>
                                            <td width="<? echo $rpt_arr['yarn_composition']['width']; ?>"><p><? echo $row['yarn_composition']; ?></p></td>
                                            <td width="<? echo $rpt_arr['yarn_type']['width']; ?>"><p><? echo $row['yarn_type']; ?></p></td>
                                            <td width="<? echo $rpt_arr['yarn_color']['width']; ?>"><p><? echo $row['yarn_color']; ?></p></td>
                                            <td width="<? echo $rpt_arr['yarn_supplier']['width']; ?>"><p><? echo $row['yarn_supplier']; ?></p></td>
                                            <td width="<? echo $rpt_arr['stock_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['stock_qty'],2,".",""); ?></p></td>
                                            <td width="<? echo $rpt_arr['allocation_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['allocation_qty'],2,".",""); ?></p></td>
                                            <td width="<? echo $rpt_arr['requisition_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['requisition_qty'],2,".","");?></p></td>
                                            
                                            <td width="<? echo $rpt_arr['issue_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['issue_qty'],2,".","");?></p></td>
                                            <td width="<? echo $rpt_arr['issue_return_qty']['width']; ?>"class="right-align"><p><a href="#report_details" onClick="openmypage('<? echo implode(",",array_unique(explode(",",chop($row['issue_id'],",")))); ?>','<? echo $prod_id; ?>','yarn_issue_return_popup','Yarn Issue Return Details')"><? echo number_format($row['issue_return_qty'], 2); ?></a></p></td>
                                            <td width="<? echo $rpt_arr['net_issued_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['net_issued_qty'],2,".","");?></p></td>
											<td width="<? echo $rpt_arr['possible_to_reduce_in_requsition']['width']; ?>" class="right-align"><p><? echo number_format($row['possible_to_reduce_in_requsition'],2,".","");?></p></td>
                                      		<td width="<? echo $rpt_arr['possible_to_red_allocation']['width']; ?>" class="right-align"><p><? echo number_format($row['possible_to_red_allocation'],2,".","");?></p></td>
                                            <td width="<? echo $rpt_arr['need_unallocated_qty']['width']; ?>" class="right-align"><p><? echo number_format($row['need_unallocated_qty'], 2);?></p></td>
                                            <td width="<? echo $rpt_arr['age_of_days']['width']; ?>" align="center"><p><? echo $row['age_of_days']; ?></p></td>
                                            <td style="vertical-align:middle;"><p><? echo $row['ship_status']; ?></p></td>
                                        </tr>
                                        <?
                                    }
                                }

                                //$total_stock_qty += $row['stock_qty'];
                                $total_allocation_qty += $row['allocation_qty'];
                                $total_requisition_qty += $row['requisition_qty'];
                                $total_requisition_balance_qty += $row['requisition_balance_qty'];
                                $total_issue_qty += $row['issue_qty'];
                                $total_issue_return_qty += $row['issue_return_qty'];
                                $total_net_issued_qty += $row['net_issued_qty'];
                                $total_possible_to_reduce_in_requsition += $row['possible_to_reduce_in_requsition'];
                                $total_possible_to_red_allocation += $row['possible_to_red_allocation'];

                                $total_need_unallocated_qty += $row['need_unallocated_qty'];
                                $i++;
                            }
                        }
                    }
                    else
                    {
                        echo "<tr><th colspan=$no_of_column style='color:red;'>No Data Found</th></tr>";
                    }
                    ?>
                </tbody>
            </table>
    	</div>
        <table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px" rules="all">
            <tfoot>
                <tr>
                    <th style="border-bottom: 1px solid #8DAFDA;padding: 1px;" colspan="<? echo $no_of_column; ?>"></th>
                </tr>
                <tr valign="middle">
                    <th width="<? echo $rpt_arr['sl']['width']; ?>"></th>
                    <th width="<? echo $rpt_arr['yarn_lot']['width']; ?>"></th>
                    <th width="<? echo $rpt_arr['frist_allocation_date']['width']; ?>"></th>
                    <th width="<? echo $rpt_arr['last_allocation_date']['width']; ?>"></th>
                    <th width="<? echo $rpt_arr['buyer_name']['width']; ?>"></th>
                    <th width="<? echo $rpt_arr['job_no']['width']; ?>"></th>
                    <th width="<? echo $rpt_arr['booking_no']['width']; ?>"></th>
                    <th width="<? echo $rpt_arr['yarn_count']['width']; ?>"></th>
                    <th width="<? echo $rpt_arr['yarn_composition']['width']; ?>"></th>
                    <th width="<? echo $rpt_arr['yarn_type']['width']; ?>"></th>
                    <th width="<? echo $rpt_arr['yarn_color']['width']; ?>"></th>
                    <th width="<? echo $rpt_arr['yarn_supplier']['width']; ?>"></th>
                    <th width="<? echo $rpt_arr['stock_qty']['width']; ?>" class="right-align">Total = </th>
                    <th width="<? echo $rpt_arr['allocation_qty']['width']; ?>" class="right-align" id="value_total_allocation_qty"><? echo number_format($total_allocation_qty,2,".",""); ?></th>
                    <th width="<? echo $rpt_arr['requisition_qty']['width']; ?>" class="right-align" id="value_total_requisition_qty"><? echo number_format($total_requisition_qty,2,".",""); ?></th>
                   
                    <th width="<? echo $rpt_arr['issue_qty']['width']; ?>" class="right-align" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,".",""); ?></th>
                    <th width="<? echo $rpt_arr['issue_return_qty']['width']; ?>" class="right-align" id="value_total_issue_return_qty"><? echo number_format($total_issue_return_qty,2,".",""); ?></th>
                    <th width="<? echo $rpt_arr['net_issued_qty']['width']; ?>" class="right-align" id="value_total_net_issued_qty"><? echo number_format($total_net_issued_qty,2,".",""); ?></th>
                    <th width="<? echo $rpt_arr['possible_to_reduce_in_requsition']['width']; ?>" class="right-align" id="value_possible_to_reduce_in_requsition"><? echo number_format($total_possible_to_reduce_in_requsition,2,".",""); ?></th>
                    <th width="<? echo $rpt_arr['possible_to_red_allocation']['width']; ?>" class="right-align" id="value_total_possible_to_red_allocation"><? echo number_format($total_possible_to_red_allocation,2,".",""); ?></th>
                    <th width="<? echo $rpt_arr['need_unallocated_qty']['width']; ?>" class="right-align" id="value_total_need_unallocated_qty"><? echo number_format($total_need_unallocated_qty,2,".",""); ?></th>
                    <th width="<? echo $rpt_arr['age_of_days']['width']; ?>"></th>
                    <th></th>
                </tr>
            </tfoot>
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
	echo "$html**$filename**$report_type";
	exit();
}

if($action=="yarn_issue_return_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	$sql_issue_return = "SELECT A.RECV_NUMBER, B.RECEIVE_BASIS, A.BOOKING_NO, B.ISSUE_ID, A.REQUISITION_NO, B.ID, B.PROD_ID, B.CONS_QUANTITY AS ISSUE_RTN_QNTY FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B WHERE A.ID = B.MST_ID AND B.ISSUE_ID in($issue_id) AND B.PROD_ID=$prod_id AND A.ENTRY_FORM = 9 AND A.RECEIVE_BASIS in(1,3) AND A.ITEM_CATEGORY = 1 AND B.RECEIVE_BASIS in(1,3) AND B.ITEM_CATEGORY = 1 AND B.TRANSACTION_TYPE = 4 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0";

	//echo $sql_issue_return;

	$result=sql_select($sql_issue_return);

	$all_req_arr = array();
	$IssueRtnArr = array();
	foreach($result as $row)
	{
		$IssueRtnArr[$row['BOOKING_NO']]['recv_number'] .= $row['RECV_NUMBER'].",";
		$IssueRtnArr[$row['BOOKING_NO']]['issue_rtn_qnty'] += $row['ISSUE_RTN_QNTY'];	

		if($row['REQUISITION_NO']>0){
			$all_req_arr[$row['REQUISITION_NO']] = $row['REQUISITION_NO'];
		}
	}
	//echo "<pre>";
	//print_r($IssueRtnArr);

	$program_result = sql_select("select a.knit_id,a.requisition_no
	from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b
	where a.knit_id = b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($all_req_arr,0,'a.requisition_no')." ");
	$programInfoArr = array();
	foreach($program_result as $row)
	{
		$programInfoArr[$row[csf('requisition_no')]]['program_no'] = $row[csf('knit_id')];
	}

	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
		}
	</script>
	<div style="width:360px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:350px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="340" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="4"><b>Yarn Issue Return</b></th>
				</thead>
				<thead>
                    <th width="70">Program No</th>
                    <th width="100">Requision / Wo No</th>
                    <th width="70">Issue Return Qty</th>
                    <th >Issue Return Qty ID</th>
				</thead>
                <?
                $i=1; $total_issue_rtn_qnty=0;

				foreach($IssueRtnArr as $k_req_no => $row)
				{
					//var_dump($row['recv_number']);

					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";

					$recv_numbers = implode(",<br>",array_unique(explode(",",chop($row['recv_number'],","))));

					$issue_rtn_qnty = $row['issue_rtn_qnty'];
					$program_no = $programInfoArr[$k_req_no]['program_no'];
					$prog_wo_no = ($row['RECEIVE_BASIS']==1)? $row['BOOKING_NO']:$program_no; 
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="70" align="center"><p><? echo $prog_wo_no; ?></p></td>
						<td width="100" align="center"><p><? echo $k_req_no;?>&nbsp;</p></td>
						<td width="70" align="right"><p><? echo number_format($issue_rtn_qnty); ?>&nbsp;</p></td>
						<td align="center"><? echo $recv_numbers; ?></td>

					</tr>
					<?
					$i++;
					$total_issue_rtn_qnty +=$issue_rtn_qnty;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td align="right"><b>Total:</b></td>
                    <td align="right"><? echo number_format($total_issue_rtn_qnty,2);?></td>
					<td>&nbsp;</td>
                </tr>
                <?
                ?>
            </table>
		</div>
	</fieldset>
	<?
	exit();
}

?>
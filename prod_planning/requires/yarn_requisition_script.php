<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
extract($_REQUEST);
$permission = $_SESSION['page_permission'];
include('../../includes/common.php');

$fieldRequisition = 'id, requisition_id, program_id, order_id, item_id, order_requisition_qty, requisition_qty, distribution_method, booking_no, inserted_by, insert_date';
$idReq = return_next_id('id', 'ppl_yarn_requisition_breakdown', 1);
$dataRequisition = '';
//for partial issue
$sqlProg = "SELECT a.dtls_id, a.po_id, a.program_qnty, a.booking_no, b.requisition_no, b.prod_id, b.yarn_qnty FROM ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b WHERE a.dtls_id = b.knit_id AND a.is_sales = 0 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0";
echo $sqlProg; die;
$sqlProgResultSet = sql_select($sqlProg);
$dataArrProg = array();
$progQtyArr = array();
$reqQtyArr = array();
foreach($sqlProgResultSet as $row)
{
	$progQtyArr[$row[csf('dtls_id')]] += $row[csf('program_qnty')];
	$reqQtyArr[$row[csf('dtls_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
	$dataArrProg[$row[csf('dtls_id')]][$row[csf('prod_id')]][$row[csf('po_id')]]['program_qnty'] = $row[csf('program_qnty')];
	$dataArrProg[$row[csf('dtls_id')]][$row[csf('prod_id')]][$row[csf('po_id')]]['requisition_no'] = $row[csf('requisition_no')];
	$dataArrProg[$row[csf('dtls_id')]][$row[csf('prod_id')]][$row[csf('po_id')]]['booking_no'] = $row[csf('booking_no')];
}
//echo "<pre>";
//print_r($reqQtyArr); die;

$sqlInc = "SELECT c.requisition_no, c.prod_id, c.cons_quantity FROM inv_transaction c WHERE c.receive_basis = 3 AND c.item_category = 1 AND c.transaction_type=2 AND c.status_active = 1 AND c.is_deleted = 0 AND c.requisition_no IS NOT NULL";
//echo $sqlProg; die;
$sqlInvResultSet = sql_select($sqlInc);
$dataArrInv = array();
foreach($sqlInvResultSet as $row)
{
	$dataArrInv[$row[csf('requisition_no')]][$row[csf('prod_id')]]['requisition_no'] = $row[csf('requisition_no')];
	$dataArrInv[$row[csf('requisition_no')]][$row[csf('prod_id')]]['cons_quantity'] += $row[csf('cons_quantity')];
}
//echo "<pre>";
//print_r($dataArrInv); die;

foreach($dataArrProg as $progNo=>$progArr)
{
	foreach($progArr as $prodId=>$prodArr)
	{
		foreach($prodArr as $poId=>$row)
		{
			if(isset($dataArrInv[$row['requisition_no']]))
			{
				if(($row['requisition_no'] == $dataArrInv[$row['requisition_no']][$prodId]['requisition_no']) && ($reqQtyArr[$progNo][$row['requisition_no']][$prodId] > $dataArrInv[$row['requisition_no']][$prodId]['cons_quantity']))
				{
					$qty = 0;
					$programQty = $progQtyArr[$progNo];
					$orderQty = $row['program_qnty'];
					$requisitionQty = $reqQtyArr[$progNo][$row['requisition_no']][$prodId];
					$qty = number_format((($orderQty * $requisitionQty) / $programQty), 2, '.', '');
		
					if ($dataRequisition != '')
						$dataRequisition .= ',';
		
					$dataRequisition .= "(" . $idReq . "," . $row['requisition_no'] . "," . $progNo . "," . $poId . "," . $prodId . "," . $qty . "," . $requisitionQty . ",1,'" . $row['booking_no'] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$idReq = $idReq+1;
				}
			}
		}
	}
}
//echo $dataRequisition; die;

$sqlProgram = "SELECT a.dtls_id, a.po_id, a.program_qnty, a.booking_no, b.requisition_no, b.prod_id, b.yarn_qnty FROM ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b WHERE a.dtls_id = b.knit_id AND a.is_sales = 0 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND b.requisition_no NOT IN(SELECT c.requisition_no FROM inv_transaction c WHERE c.receive_basis = 3 AND c.item_category = 1 AND c.transaction_type=2 AND c.status_active = 1 AND c.is_deleted = 0 AND c.requisition_no IS NOT NULL GROUP BY c.requisition_no)";
//echo $sqlProgram; die;
$sqlProgramResultSet = sql_select($sqlProgram);
$dataArrProgram = array();
$poIdArr = array();
$programQtyArr = array();
$requisitionQtyArr = array();
foreach($sqlProgramResultSet as $row)
{
	$poIdArr[$row[csf('po_id')]] = $row[csf('po_id')];
	$programQtyArr[$row[csf('dtls_id')]] += $row[csf('program_qnty')];
	$requisitionQtyArr[$row[csf('dtls_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
	$dataArrProgram[$row[csf('dtls_id')]][$row[csf('prod_id')]][$row[csf('po_id')]]['program_qnty'] = $row[csf('program_qnty')];
	$dataArrProgram[$row[csf('dtls_id')]][$row[csf('prod_id')]][$row[csf('po_id')]]['requisition_no'] = $row[csf('requisition_no')];
	$dataArrProgram[$row[csf('dtls_id')]][$row[csf('prod_id')]][$row[csf('po_id')]]['booking_no'] = $row[csf('booking_no')];
}
//echo "<pre>";
//print_r($dataArrProgram); die;

//for will not issue
foreach($dataArrProgram as $progNo=>$progArr)
{
	foreach($progArr as $prodId=>$prodArr)
	{
		foreach($prodArr as $poId=>$row)
		{
			$qty = 0;
			$programQty = $programQtyArr[$progNo];
			$orderQty = $row['program_qnty'];
			$requisitionQty = $requisitionQtyArr[$progNo][$row['requisition_no']][$prodId];
			$qty = number_format((($orderQty * $requisitionQty) / $programQty), 2, '.', '');

			if ($dataRequisition != '')
				$dataRequisition .= ',';

			$dataRequisition .= "(" . $idReq . "," . $row['requisition_no'] . "," . $progNo . "," . $poId . "," . $prodId . "," . $qty . "," . $requisitionQty . ",1,'" . $row['booking_no'] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			$idReq = $idReq+1;
		}
	}
}


$con = connect();
$rID = sql_insert("ppl_yarn_requisition_breakdown", $fieldRequisition, $dataRequisition, 1);
echo "10**INSERT INTO ppl_yarn_requisition_breakdown (".$fieldRequisition.") VALUES ".$dataRequisition.""; die;

if ($db_type == 0)
{
	if ($rID)
	{
		mysql_query("COMMIT");
		echo "Success";
	}
	else
	{
		mysql_query("ROLLBACK");
		echo "Fail";
	}
}
else if ($db_type == 2 || $db_type == 1)
{
	if ($rID)
	{
		oci_commit($con);
		echo "Success";
	}
	else
	{
		oci_rollback($con);
		echo "Fail";
	}
}
disconnect($con);
die;
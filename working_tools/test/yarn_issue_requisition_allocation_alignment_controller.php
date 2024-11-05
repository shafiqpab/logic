<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
if ($db_type == 0) {
	mysql_query("BEGIN");
}

$user_id=$_SESSION['logic_erp']['user_id'];

die("You are not authenticated");

if($user_id!=1)
{
	die('You are not authenticated');
	disconnect($con);
}

$requisition_issue_sql = "SELECT  a.id, a.knit_id, c.po_id, a.requisition_no, a.prod_id,c.booking_no,a.yarn_qnty, b.cons_quantity AS issue_qnty FROM ppl_yarn_requisition_entry a, inv_transaction b, ppl_planning_entry_plan_dtls c WHERE a.requisition_no = b.requisition_no AND a.prod_id=b.prod_id AND a.knit_id=c.dtls_id and b.transaction_type = 2 AND b.item_category = 1 AND a.status_active = 1 AND b.status_active = 1 AND c.status_active = 1 AND a.yarn_qnty = 0 AND b.cons_quantity > 0 ORDER BY a.requisition_no"; 

//echo $requisition_issue_sql; die();

$issue_data = sql_select($requisition_issue_sql);
if(!empty($issue_data))
{
	$requisition_up_data = array();
	foreach ($issue_data as $row) 
	{
	    $requisition_up_data[$row[csf("id")]][$row[csf("requisition_no")]][$row[csf("prod_id")]] +=  $row[csf("issue_qnty")];
	    $allocation_data[$row[csf("booking_no")]][$row[csf("po_id")]][$row[csf("prod_id")]] +=  $row[csf("issue_qnty")];

	    $bookingNo .= "'".$row[csf("booking_no")]."',";
	    $poIds.=$row[csf("po_id")].",";
	    $prodIds.=$row[csf("prod_id")].",";
	}
}

if($prodIds!="")
{
	$prodIds = implode(",",array_unique(explode(",",chop($prodIds,',')))); 
	$prod_id_cond=""; 
	if($db_type==2 && $tot_rows>1000)
	{
		$prod_id_cond=" and (";
		
		$prodIdArr=array_chunk(explode(",",$prodIds),999);
		foreach($prodIdArr as $ids)
		{
			$ids=implode(",",$ids);
			$prod_id_cond.=" b.item_id in($ids) or ";				
		}
		$prod_id_cond=chop($po_id_cond,'or ');
		$prod_id_cond.=")";
	}
	else
	{
		$prod_id_cond=" and b.item_id in ($prodIds)";
	}
}

if($poIds!="")
{
	$poIds = implode(",",array_unique(explode(",",chop($poIds,',')))); 
	$po_id_cond=""; 
	$po_id_bokking_cond=""; 

	if($db_type==2 && $tot_rows>1000)
	{
		$po_id_cond=" and (";
		
		$poIdArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdArr as $ids)
		{
			$ids=implode(",",$ids);
			$po_id_cond.=" b.po_break_down_id in($ids) or ";				
		}
		$po_id_cond=chop($po_id_cond,'or ');
		$po_id_cond.=")";
	}
	else
	{
		$po_id_cond=" and b.po_break_down_id in ($poIds)";
	}
}

if($bookingNo!="")
{
	$bookingNo = implode(",",array_unique(explode(",",chop($bookingNo,',')))); 
	$booking_no_cond=""; 

	if($db_type==2 && $tot_rows>1000)
	{
		$booking_no_cond=" and (";		
		$bookingNoArr=array_chunk(explode(",",$bookingNo),999);
		foreach($bookingNoArr as $ids)
		{
			$ids=implode(",",$ids);
			$booking_no_cond.=" b.booking_no in($ids) or ";	
		}

		$booking_no_cond=chop($booking_no_cond,'or ');
		$booking_no_cond.=")";
	}
	else
	{
		$booking_no_cond=" and b.booking_no in ($bookingNo)";
	}
}

$sql_allcation = "SELECT a.*,a.id as mst_id,b.*,b.id dtls_id from INV_MATERIAL_ALLOCATION_MST a, INV_MATERIAL_ALLOCATION_DTLS b WHERE a.item_id=b.item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_no_cond $po_id_cond $prod_id_cond";
//echo $sql_allcation; die;

$result_alc = sql_select($sql_allcation); 

$allocation_up_data = array();
foreach ($result_alc as $row) 
{
	$allocation_qty = $allocation_data[$row[csf("booking_no")]][$row[csf("po_break_down_id")]][$row[csf("item_id")]];
	
	$allocation_up_data[$row[csf("job_no")]][$row[csf("booking_no")]][$row[csf("po_break_down_id")]][$row[csf("item_id")]]['mst_id']=$row[csf("mst_id")];
	$allocation_up_data[$row[csf("job_no")]][$row[csf("booking_no")]][$row[csf("po_break_down_id")]][$row[csf("item_id")]]['dtls_id']=$row[csf("dtls_id")];
	$allocation_up_data[$row[csf("job_no")]][$row[csf("booking_no")]][$row[csf("po_break_down_id")]][$row[csf("item_id")]]['new_qty']=$allocation_qty;
	$allocation_up_data[$row[csf("job_no")]][$row[csf("booking_no")]][$row[csf("po_break_down_id")]][$row[csf("item_id")]]['previous_qnty']=$row[csf("qnty")];
	$allocation_up_data[$row[csf("job_no")]][$row[csf("booking_no")]][$row[csf("po_break_down_id")]][$row[csf("item_id")]]['qnty_break_down']="'".($row[csf("qnty")]+$allocation_qty)."_".$row[csf("po_break_down_id")]."_".$row[csf("job_no")]."'";
}

// requsition update 
foreach ($requisition_up_data as $requisition_id=>$requisition_arr) 
{
	foreach ($requisition_arr as $requisition_no => $prod_id_arr) 
	{
		foreach ($prod_id_arr as $prod_id=>$issue_qnty) 
		{
			
			//$requisition_data[$requisition_id] = $issue_qnty;	
			$update_requisition_sql =execute_query("update ppl_yarn_requisition_entry set yarn_qnty=$issue_qnty,updated_by=888 where id=$requisition_id");

           	if($update_requisition_sql) $update_requisition_sql=1; else {"update ppl_yarn_requisition_entry set yarn_qnty=$issue_qnty,updated_by=888 where id=$requisition_id";oci_rollback($con);die;}
		}
	}
}

// allocaton update
foreach ($allocation_up_data as $job=>$booking_arr) 
{
	foreach ($booking_arr as $po_id => $prod_id_arr) 
	{
		foreach ($prod_id_arr as $prod_id=>$dataArr) 
		{
			foreach ($dataArr as $row) 
			{
				//echo "<pre>";
				//print_r($val);
				$details_id = $row['dtls_id'];
				$mst_id = $row['mst_id'];
				$new_qty = $row['new_qty'];
				$previous_qty = $row['previous_qnty'];
				$qnty_break_down = $row['qnty_break_down'];

				$total_allocation_qty = ($new_qty+$previous_qty);

				$update_mst_table_sql =execute_query("update inv_material_allocation_mst set qnty_break_down=$qnty_break_down,qnty=$total_allocation_qty,updated_by=888 where id=$mst_id");

           		if($update_mst_table_sql) $update_mst_table_sql=1; else {"update inv_material_allocation_mst set qnty_break_down=$qnty_break_down,qnty=$total_allocation_qty,updated_by=888 where id=$details_id";oci_rollback($con);die;}

            	$update_dtls_table_sql =execute_query("update inv_material_allocation_dtls set qnty=$total_allocation_qty,updated_by=888 where id=$details_id");

            	if($update_dtls_table_sql) $update_dtls_table_sql=1; else {"update inv_material_allocation_dtls set qnty=$total_allocation_qty,updated_by=888 where id=$details_id";oci_rollback($con);die;}		
			}
		}
	}
}

//echo "<pre>";
//print_r($allocation_up_data);
//die('test');

//echo "10**".$update_mst_table_sql. "&&". $update_dtls_table_sql. "&&".$update_requisition_sql;
//oci_rollback($con); die();

if( $update_mst_table_sql && $update_dtls_table_sql && $update_requisition_sql ) 
{
	oci_commit($con); 
    echo "Success";
    die; 
}
else
{
	oci_rollback($con);
	echo "10**failed";
	die();
}
?>
<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');

$con=connect();
//$issue_mrr_cond = " and a.issue_number = 'CCKL-YIS-20-05710'";
//$requisition_cond = "and b.requisition_no in (5530,5530,5530,5530,5530,6952,6594,6760,6789,6789,6789)";
$issue_sql = "select a.id as issue_id,a.issue_number,a.issue_basis,a.issue_purpose,b.id as trans_id,b.prod_id,b.requisition_no,b.cons_quantity as issue_qty from inv_issue_master a , inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and c.dyed_type!=1 and a.entry_form = 3 and a.issue_basis=3 and a.issue_purpose=1 and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=1 $issue_mrr_cond $requisition_cond group by a.id,b.id,a.issue_number,a.issue_basis,a.issue_purpose,b.prod_id, b.requisition_no,b.cons_quantity order by a.id desc";//and

//echo $issue_sql; die();
$issue_data = sql_select($issue_sql);

foreach ($issue_data as $row)
{
	$requ_id.=$row[csf('requisition_no')].",";
	$prod_id.=$row[csf('prod_id')].",";
}

$req_id_cond="";
if($requ_id!="")
{
	$requ_id=substr($requ_id,0,-1);
	$requ_id=implode(",",array_filter(array_unique(explode(",",$requ_id))));

	if($db_type==0) $req_id_cond="and requisition_no in(".$requ_id.")";
	else
	{
		$req_ids=explode(",",$requ_id);
		if(count($req_ids)>990)
		{
			$req_id_cond="and (";
			$req_ids=array_chunk($req_ids,990);
			$z=0;
			foreach($req_ids as $id)
			{
				$id=implode(",",$id);
				if($z==0) $req_id_cond.=" requisition_no in(".$id.")";
				else $req_id_cond.=" or requisition_no in(".$id.")";
				$z++;
			}
			$req_id_cond.=")";
		}
		else $req_id_cond="and requisition_no in(".$requ_id.")";
	}
}

	// product cond
$prod_id_cond="";
$prod_id_cond2="";
if($prod_id!="")
{
	$prod_id=substr($prod_id,0,-1);
	$prod_id=implode(",",array_filter(array_unique(explode(",",$prod_id))));

	if($db_type==0) {
		$prod_id_cond="and b.prod_id in(".$prod_id.")";
		$prod_id_cond2="and b.item_id in(".$prod_id.")";
	}
	else
	{
		$prod_ids=explode(",",$prod_id);
		if(count($prod_ids)>990)
		{
			$prod_id_cond="and (";
			$prod_id_cond2="and (";
			$prod_ids=array_chunk($prod_ids,990);
			$z=0;
			foreach($prod_ids as $id)
			{
				$id=implode(",",$id);
				if($z==0) {
					$prod_id_cond.=" b.prod_id in(".$id.")";
					$prod_id_cond2.=" b.item_id in(".$id.")";
				}
				else {
					$prod_id_cond.=" or b.prod_id in(".$id.")";
					$prod_id_cond2.=" or b.item_id in(".$id.")";
				}
				$z++;
			}
			$prod_id_cond.=")";
			$prod_id_cond2.=")";
		}
		else {
			$prod_id_cond="and b.prod_id in(".$prod_id.")";
			$prod_id_cond2="and b.item_id in(".$prod_id.")";
		}
	}
}

$requsition_sql = "select b.requisition_no id,b.prod_id,sum(b.yarn_qnty) qnty
from ppl_yarn_requisition_entry b where b.status_active=1 and b.is_deleted=0 $req_id_cond $prod_id_cond group by b.requisition_no,b.prod_id"; 
$requsition_data = sql_select($requsition_sql);

$req_qty_array= array();
foreach ($requsition_data as $row) {
	$req_qty_array[$row[csf('id')]][$row[csf('prod_id')]] = $row[csf('qnty')];
}

$sql_plan_po = "select c.requisition_no,booking_no,po_id,b.is_sales from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id and a.is_sales!=2 $req_id_cond group by c.requisition_no,booking_no,po_id,b.is_sales order by po_id"; 

$plan_po_data = sql_select($sql_plan_po);
$booking_no_arr = array();
foreach ($plan_po_data as $row) {
	$po_id.=$row[csf('po_id')].",";
	$booking_no_arr[$row[csf('po_id')]]['po_id'] = $row[csf('booking_no')];
	$booking_no_arr[$row[csf('booking_no')]]['is_sales'] = $row[csf('is_sales')];
	$requisition_po_arr[$row[csf('requisition_no')]][] = $row[csf('po_id')];
}

$po_id_cond="";
$po_id_cond2="";
$po_id_cond3="";

if($po_id!="")
{
	$po_id=substr($po_id,0,-1);
	$po_id=implode(",",array_filter(array_unique(explode(",",$po_id))));

	if($db_type==0) {
		$po_id_cond="and a.id in(".$po_id.")";
		$po_id_cond2="and a.po_break_down_id in(".$po_id.")";
		$po_id_cond3="and a.po_breakdown_id in(".$po_id.")";
	}
	else
	{
		$po_ids=array_unique(explode(",",$po_id));
		if(count($po_ids)>990)
		{
			$po_id_cond="and (";
			$po_id_cond2="and (";
			$po_ids=array_chunk($po_ids,990);
			$z=0;
			foreach($po_ids as $id)
			{
				$id=implode(",",$id);
				if($z==0) {
					$po_id_cond.=" a.id in(".$id.")";
					$po_id_cond2.=" a.po_break_down_id in(".$id.")";
					$po_id_cond3.=" a.po_breakdown_id in(".$id.")";
				}
				else {
					$po_id_cond.=" or a.id in(".$id.")";
					$po_id_cond2.=" or a.po_break_down_id in(".$id.")";
					$po_id_cond3.=" or a.po_breakdown_id in(".$id.")";
				}
				$z++;
			}
			$po_id_cond.=")";
			$po_id_cond2.=")";
			$po_id_cond3.=")";
		}
		else 
		{
			$po_id_cond="and a.id in(".$po_id.")";
			$po_id_cond2="and a.po_break_down_id in(".$po_id.")";
			$po_id_cond3="and a.po_breakdown_id in(".$po_id.")";
		}
	}
}

//die($po_id_cond2);

$allocation_sql = "select a.po_break_down_id,a.job_no,a.booking_no,a.item_id,a.qnty as order_wise_allocation,b.booking_no from inv_material_allocation_mst b,inv_material_allocation_dtls a where b.id=a.mst_id and b.is_dyied_yarn!=1 $prod_id_cond2 $po_id_cond2 and b.status_active=1 and a.status_active=1"; // and a.booking_no='AOPL-Fb-19-00497' 

$allocation_result = sql_select($allocation_sql);
$order_wise_allocation_arr = array();
foreach ($allocation_result as $allocation_row) {

	$order_wise_allocation_arr[$allocation_row[csf("item_id")]][$allocation_row[csf("job_no")]][$allocation_row[csf("booking_no")]][$allocation_row[csf("po_break_down_id")]] = $allocation_row[csf("order_wise_allocation")];


	$total_allocation_qnty[$allocation_row[csf("item_id")]][$allocation_row[csf("job_no")]][$allocation_row[csf("booking_no")]] += $allocation_row[csf("order_wise_allocation")];
}

/*
echo "<pre>";
print_r($total_allocation_qnty);
die();*/

$is_salesOrder == 0;
if ($is_salesOrder == 1) {
	$po_sql = "select a.id,a.job_no,a.job_no as po_number, sum(b.grey_qty) as po_quantity, 1 as total_set_qnty
	from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id $po_id_cond group by a.id,a.job_no";
} else {
	if ($po_id != "") {
		$po_sql = "select a.id, a.job_no_mst as job_no , a.po_number,a.file_no,a.grouping, a.po_quantity, b.total_set_qnty
		from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst $po_id_cond group by a.id, a.job_no_mst, a.po_number,a.file_no,a.grouping, a.po_quantity, b.total_set_qnty";
	}
}

$order_wise_data = sql_select($po_sql);
foreach ($order_wise_data as $row)
{
	$booking_no = $booking_no_arr[$row[csf('id')]]['po_id'];
	$po_info[$row[csf('id')]]["order_id"] = $row[csf('id')];
	$po_info[$row[csf('id')]]["booking_no"] = $booking_no;
	$po_info[$row[csf('id')]]["job_no"] = $row[csf('job_no')];
}

//echo "<pre>";
//print_r($po_info);
//die();

$field_array_proportionate = "id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,issue_purpose,returnable_qnty,is_sales,inserted_by, insert_date";
/*echo "<pre>";
print_r($requisition_po_arr);
echo "</pre>";*/
$data_array_prop="";
foreach ($issue_data as $issue_row) {

	$transactionID 	= $issue_row[csf('trans_id')];
	$prodId 		= $issue_row[csf('prod_id')];
	$issue_purpose 	= $issue_row[csf('issue_purpose')];
	$requisition_no = $issue_row[csf('requisition_no')];
	$issue_basis 	= $issue_row[csf('issue_basis')];
	$issue_qty 	= $issue_row[csf('issue_qty')];

	$i = 0;
	$req_po_data = $requisition_po_arr[$requisition_no];
	$returnable_qnty = 0;
	foreach ($req_po_data as $po_id)
	{
		$order_id 	= $po_info[$po_id]["order_id"];
		$booking_no = $po_info[$po_id]["booking_no"];
		$job_no 	= $po_info[$po_id]["job_no"];
		$is_salesOrder = $booking_no_arr[$booking_no]['is_sales'];

		$check_order_data = trim(return_field_value("id"," order_wise_pro_details","po_breakdown_id=$order_id and trans_id=$transactionID and prod_id=$prodId"));
		
		if($check_order_data=="")
		{
			$totalAllocationqty = $total_allocation_qnty[$prodId][$job_no][$booking_no];
			$order_wise_allocation_qty = $order_wise_allocation_arr[$prodId][$job_no][$booking_no][$order_id];
			$req_qnty = ($req_qty_array[$requisition_no][$prodId]/$totalAllocationqty)*$order_wise_allocation_qty;

			//echo $req_qty_array[$requisition_no][$prodId]."/".$totalAllocationqty."*".$order_wise_allocation_qty; 
			if($issue_basis == 3)
			{
				$perc = ( ($req_qnty / $req_qty_array[$requisition_no][$prodId]) * 100 );
			}

			$order_qnty = number_format(( ($perc * $issue_qty) / 100 ),2,".","");

			if($order_qnty>0)
			{
				if($data_array_prop!=""){
					$data_array_prop .= ",";
				}
				$id_proport = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				//$id_proport = 0;
				$data_array_prop .= "(" . $id_proport . "," . $transactionID . ",2,3," . $order_id . "," . $prodId . ",'" . $order_qnty . "'," . $issue_purpose . ",'" . $returnable_qnty . "','" . $is_salesOrder . "',8888,'" . $pc_date_time . "')";
			}
		}

	}

}

//die();

//echo $data_array_prop; die();

if ($data_array_prop != "") {
	echo "10**INSERT INTO order_wise_pro_details (".$field_array_proportionate.") VALUES ".$data_array_prop."";
	echo "<br>";
	die();
	//$proportQ = sql_insert("order_wise_pro_details", $field_array_proportionate, $data_array_prop, 0);
}


//echo $db_type;die;
if ($db_type == 0) {
	if ($proportQ) {
		mysql_query("COMMIT");
		echo "0**" . "success";
	} else {
		mysql_query("ROLLBACK");
		echo "10**"."fail";
	}
} else if ($db_type == 2 || $db_type == 1) {

	if ($proportQ) {
		oci_commit($con);
		echo "0**"."success";
	} else {
		oci_rollback($con);
		echo "10**fail";
	}
}
disconnect($con);
die();

?>

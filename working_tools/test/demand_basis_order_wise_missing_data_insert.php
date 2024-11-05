<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$company_id = 3;

$issue_sql = "select a.id as issue_id,a.issue_number,a.issue_basis,a.issue_purpose,b.id as trans_id,b.prod_id,b.requisition_no,b.demand_id,b.cons_quantity as issue_qty from inv_issue_master a , inv_transaction b where a.id=b.mst_id and a.entry_form = 3 AND a.company_id=$company_id and a.issue_basis=8 and a.issue_purpose=1 and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=1 and not exists ( select trans_id from order_wise_pro_details c where b.id = c.trans_id and c.status_active=1 and c.is_deleted=0) group by a.id,b.id,a.issue_number,a.issue_basis,a.issue_purpose,b.prod_id, b.requisition_no,b.demand_id,b.cons_quantity order by a.id desc";//

echo $issue_sql; die();//

$issue_data = sql_select($issue_sql);

$issue_qty_arr = array();
foreach ($issue_data as $row)
{
	$requ_id.=$row[csf('requisition_no')].",";
	$demand_id.=$row[csf('demand_id')].",";
	$prod_id.=$row[csf('prod_id')].",";

	$issue_qty_arr[$row[csf('issue_id')]][$row[csf('requisition_no')]][$row[csf('demand_id')]][$row[csf('prod_id')]] = $row[csf('issue_qty')];
}

//echo "<pre>";
//print_r($issue_qty_arr);
//die();

$req_id_cond="";
if($requ_id!="")
{
	$requ_id=substr($requ_id,0,-1);
	$requ_id=implode(",",array_filter(array_unique(explode(",",$requ_id))));

	if($db_type==0) $req_id_cond="and c.requisition_no in(".$requ_id.")";
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
				if($z==0) $req_id_cond.=" c.requisition_no in(".$id.")";
				else $req_id_cond.=" or c.requisition_no in(".$id.")";
				$z++;
			}
			$req_id_cond.=")";
		}
		else $req_id_cond="and c.requisition_no in(".$requ_id.")";
	}
}

$demand_id_cond="";
if($demand_id!="")
{
	$demand_id=substr($demand_id,0,-1);
	$demand_id=implode(",",array_filter(array_unique(explode(",",$demand_id))));

	if($db_type==0) $demand_id_cond="and e.mst_id in(".$demand_id.")";
	else
	{
		$demand_ids=explode(",",$demand_id);
		if(count($demand_ids)>990)
		{
			$demand_id_cond="and (";
			$demand_ids=array_chunk($demand_ids,990);
			$z=0;
			foreach($demand_ids as $id)
			{
				$id=implode(",",$id);
				if($z==0) $demand_id_cond.=" e.mst_id in(".$id.")";
				else $demand_id_cond.=" or e.mst_id in(".$id.")";
				$z++;
			}
			$demand_id_cond.=")";
		}
		else $demand_id_cond="and e.mst_id in(".$demand_id.")";
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
		$prod_id_cond="and c.prod_id in(".$prod_id.")";
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
					$prod_id_cond.=" c.prod_id in(".$id.")";
					$prod_id_cond2.=" b.item_id in(".$id.")";
				}
				else {
					$prod_id_cond.=" or c.prod_id in(".$id.")";
					$prod_id_cond2.=" or b.item_id in(".$id.")";
				}
				$z++;
			}
			$prod_id_cond.=")";
			$prod_id_cond2.=")";
		}
		else {
			$prod_id_cond="and c.prod_id in(".$prod_id.")";
			$prod_id_cond2="and b.item_id in(".$prod_id.")";
		}
	}
}

$sql_plan_po = "select c.requisition_no, e.mst_id as demand_id,a.po_id,c.prod_id,listagg ( g.id , ',') within group (order by g.id) as po_breakdown_id from  ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c, ppl_yarn_requisition_breakdown d, ppl_yarn_demand_reqsn_dtls e, wo_po_details_master f, wo_po_break_down g where b.id=a.dtls_id and b.id=c.knit_id and c.knit_id=d.program_id and c.requisition_no=d.requisition_id and c.prod_id=d.item_id and d.requisition_id=e.requisition_no and d.item_id=e.prod_id AND f.job_no=g.job_no_mst and d.order_id=g.id $req_id_cond  $demand_id_cond group by c.requisition_no,e.mst_id,a.po_id,c.prod_id order by e.mst_id";
//echo $sql_plan_po; die();

$plan_po_data = sql_select($sql_plan_po);
$booking_no_arr = array();
$po_qty_array = array();
$total_po_qty_array = array();
foreach ($plan_po_data as $row) 
{
	$po_id.=$row[csf('po_id')].",";
	$demand_wise_po_string_arr[$row[csf('requisition_no')]][$row[csf('demand_id')]][$row[csf('prod_id')]] = $row[csf('po_breakdown_id')];
	$demand_po_arr[$row[csf('requisition_no')]][$row[csf('demand_id')]][$row[csf('prod_id')]][] = $row[csf('po_id')];
}


//echo "<pre>";
//print_r($demand_po_arr);
//echo "</pre>";
//die();

$po_id_cond="";

if($po_id!="")
{
	$po_id=substr($po_id,0,-1);
	$po_id=implode(",",array_filter(array_unique(explode(",",$po_id))));

	if($db_type==0) 
	{
		$po_id_cond="and a.id in(".$po_id.")";
	}
	else
	{
		$po_ids=explode(",",$po_id);
		if(count($po_ids)>990)
		{
			$po_id_cond="and (";
			$po_ids=array_chunk($po_ids,990);
			$z=0;
			foreach($po_ids as $id)
			{
				$id=implode(",",$id);
				if($z==0) {
					$po_id_cond.=" a.id in(".$id.")";
				}
				else {
					$po_id_cond.=" or a.id in(".$id.")";
				}
				$z++;
			}
			$po_id_cond.=")";
		}
		else {
			$po_id_cond="and a.id in(".$po_id.")";
		}
	}
}


if ($po_id != "") 
{
	$po_sql = "select a.id, a.job_no_mst as job_no , a.po_number,a.file_no,a.grouping, a.po_quantity, b.total_set_qnty
	from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst $po_id_cond group by a.id, a.job_no_mst, a.po_number,a.file_no,a.grouping, a.po_quantity, b.total_set_qnty";

	$order_wise_data = sql_select($po_sql);

	foreach($order_wise_data as $row)
	{
		$po_qty_array[$row[csf('id')]] =  ($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
	}
}

$requsition_sql = "select c.requisition_no,e.mst_id as demand_id, c.prod_id,sum(e.yarn_demand_qnty) demand_qnty
from ppl_yarn_requisition_entry c,ppl_yarn_demand_reqsn_dtls e where c.requisition_no=e.requisition_no and c.prod_id=e.prod_id and c.is_deleted=0 and c.is_deleted=0 and e.is_deleted=0 and e.is_deleted=0 $req_id_cond $prod_id_cond $demand_id_cond group by c.requisition_no,e.mst_id, c.prod_id";  

$requsition_data = sql_select($requsition_sql);

$req_qty_array= array();
$po_wise_demand_required_qty = array();

$total_po_qnty_in_pcs = 0;

foreach ($requsition_data as $row) 
{
	$demand_po_string  = $demand_wise_po_string_arr[$row[csf('requisition_no')]][$row[csf('demand_id')]][$row[csf('prod_id')]];
	$demand_po_data  = $demand_po_arr[$row[csf('requisition_no')]][$row[csf('demand_id')]][$row[csf('prod_id')]];

	$total_po_qnty_in_pcs = return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_qnty_in_pcs","wo_po_details_master a, wo_po_break_down b","a.job_no=b.job_no_mst and b.id in ($demand_po_string)","po_qnty_in_pcs");

	foreach ($demand_po_data as $po_id) // prepare po wise demand required qty
	{
		//echo $po_id;
		$po_quantity_pcs = $po_qty_array[$po_id];

		
		$percentage = ($po_quantity_pcs / $total_po_qnty_in_pcs) * 100;
		$distribiute_demand_qnty = ($percentage * $row[csf('demand_qnty')]) / 100;

		//echo $po_quantity_pcs ."/". $total_po_qnty_in_pcs."=$distribiute_demand_qnty<br>";

		$po_wise_demand_required_qty[$row[csf('requisition_no')]][$row[csf('demand_id')]][$row[csf('prod_id')]][$po_id] = $distribiute_demand_qnty;
		$total_demand_required_qty[$row[csf('requisition_no')]][$row[csf('demand_id')]][$row[csf('prod_id')]] += $distribiute_demand_qnty;
	}
}

//echo "<pre>";
//print_r($po_qty_array);
//die();

$field_array_proportionate = "id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,issue_purpose,returnable_qnty,is_sales,inserted_by, insert_date";

$data_array_prop="";
foreach ($issue_data as $issue_row) 
{

	$transactionID 	= $issue_row[csf('trans_id')];
	$prodId 		= $issue_row[csf('prod_id')];
	$issue_purpose 	= $issue_row[csf('issue_purpose')];
	$requisition_no = $issue_row[csf('requisition_no')];
	$demand_id 		= $issue_row[csf('demand_id')];
	$issue_basis 	= $issue_row[csf('issue_basis')];
	$issue_qty 		= $issue_qty_arr[$issue_row[csf('issue_id')]][$requisition_no][$demand_id][$prodId];
	$is_salesOrder  = 0;
	$i = 0;
	$demand_po_data = $demand_po_arr[$requisition_no][$demand_id][$prodId];


	foreach ($demand_po_data as $po_id)
	{
		$check_order_data = trim(return_field_value("id"," ORDER_WISE_PRO_DETAILS","PO_BREAKDOWN_ID=$po_id and trans_id=$transactionID and prod_id=$prodId and status_active=1 and is_deleted=0"));

		if($check_order_data=="")
		{
			
			$req_qnty = $po_wise_demand_required_qty[$requisition_no][$demand_id][$prodId][$po_id];
			$totReqQty = $total_demand_required_qty[$requisition_no][$demand_id][$prodId];

			$order_qnty = number_format(($req_qnty*$issue_qty)/$totReqQty,2,".","");
			//$order_qnty = number_format(( ($perc * $issue_qty) / 100 ),2,".","");

			if($order_qnty>0)
			{
				if($data_array_prop!=""){
					$data_array_prop .= ",";
				}
				$id_proport = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				//$id_proport = 0;
				$data_array_prop .= "(" . $id_proport . "," . $transactionID . ",2,3," . $po_id . "," . $prodId . ",'" . $order_qnty . "'," . $issue_purpose . ",'" . $returnable_qnty . "','" . $is_salesOrder . "',9999,'" . $pc_date_time . "')";

				$order_req[$po_id] = $req_qnty;
			}
		}
	}

}

//print_r($order_req) ."<br>"; 
//$data_array_prop; die();

if ($data_array_prop != "") {
	//echo "10**INSERT INTO order_wise_pro_details (".$field_array_proportionate.") VALUES ".$data_array_prop."";
	$proportQ = sql_insert("order_wise_pro_details", $field_array_proportionate, $data_array_prop, 0);
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

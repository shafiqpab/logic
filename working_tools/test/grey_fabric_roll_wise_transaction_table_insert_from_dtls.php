<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$issue_dtls_sql = sql_select("select a.id,a.mst_id,a.trans_id,a.basis,a.program_no,a.prod_id,a.issue_qnty,a.rate, a.amount,a.color_id,a.location_id,a.machine_id,a.stitch_length,a.yarn_lot,a.yarn_count,a.brand_id,a.floor_id,a.room,a.rack,a.self,a.store_name,a.inserted_by,a.insert_date,a.qty_in_pcs
,b.id, d.company_id, d.issue_date
from inv_grey_fabric_issue_dtls a left join inv_transaction b on a.trans_id=b.id and b.item_category=13 and b.transaction_type=2 and b.status_active=1, order_wise_pro_details c, inv_issue_master d
where a.status_active=1 and a.id=c.dtls_id and c.entry_form=61 and a.mst_id=d.id
group by a.id,a.mst_id,a.trans_id,a.basis,a.program_no,a.prod_id,a.issue_qnty,a.rate, a.amount,a.color_id,a.location_id,a.machine_id,a.stitch_length,a.yarn_lot,a.yarn_count,a.brand_id,a.floor_id,a.room,a.rack,a.self,a.store_name,a.inserted_by,a.insert_date,a.qty_in_pcs
, d.company_id, d.issue_date, b.id
having b.id is null
order by a.id");

if(empty($issue_dtls_sql))
{
	echo "Data Not Found";
	die;
}


$sql_ordWiseProf=sql_select("select a.trans_id,a.po_breakdown_id from order_wise_pro_details a,inv_transaction b where b.id=a.trans_id and b.transaction_type=4 and a.trans_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=52");

$orderids_arr=array();
foreach($sql_ordWiseProf  as $row)
{
	if($orderids_arr[$row[csf("trans_id")]]["po_breakdown_id"] == "")
	{
		$orderids_arr[$row[csf("trans_id")]]["po_breakdown_id"]= $row[csf("po_breakdown_id")];
	}else{
		$orderids_arr[$row[csf("trans_id")]]["po_breakdown_id"].= ",".$row[csf("po_breakdown_id")];
	}
}

$field_details_array = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,prod_id,item_category,transaction_type,transaction_date, cons_quantity,cons_rate,cons_amount,brand_id,location_id,machine_id,stitch_length,floor_id,room,rack,self,bin_box,store_id,inserted_by,insert_date";
foreach($issue_dtls_sql  as $val)
{
	$mst_id   = $val[csf("id")];
	$trans_id = $val[csf("trans_id")];
	$batch_id = $val[csf("batch_id")];
	$order_ids = $orderids_arr[$val[csf("trans_id")]]["po_breakdown_id"];
	$cons_quantity = ($val[csf("cons_quantity")]!="")?$val[csf("cons_quantity")]:0;
	
	$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);

	if($data_array_trans!="") $data_array_trans.=", ";

	$data_array_trans.="(".$transactionID.",".$val[csf("mst_id")].",'".$val[csf("basis")]."','".$val[csf("program_no")]."',".$val[csf("cbo_company_id")].",'".$val[csf("prod_id")]."',13,2,".$val[csf("issue_date")].",'".$val[csf("issue_qnty")]."','".$val[csf("rate")]."','".$val[csf("amount")]."','".$val[csf("brand_id")]."','".$val[csf("location_id")]."','".$val[csf("machine_id")]."','".$val[csf("stitch_length")]."','".$val[csf("floor_id")]."','".$val[csf("room")]."','".$val[csf("rack")]."','".$val[csf("self")]."','0','".$val[csf("store_name")]."',".$val[csf("inserted_by")] . ",'" . $pc_date_time ."')";

}
echo "10**insert into inv_transaction (".$field_details_array.") values ".$data_array_trans;die;

$rID = sql_insert2("inv_transaction",$field_details_array,$data_array_trans,1);
if($rID==1){
	oci_commit($con);
	echo "Success";
}else{
	oci_rollback($con);
	echo "failed";
}
die;



 ?>
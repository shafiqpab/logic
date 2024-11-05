<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

/*$recv_dtls_sql = sql_select("SELECT a.id, a.trans_id, a.prod_id, a.grey_receive_qnty, a.inserted_by, max(b.is_sales) as is_sales, b.po_breakdown_id, c.id as prop_table_id 
from pro_grey_prod_entry_dtls a, pro_roll_details b 
left join order_wise_pro_details c on b.dtls_id=c.dtls_id and b.ENTRY_FORM = c.ENTRY_FORM and c.entry_form=58
where a.id=b.dtls_id and b.is_sales=1 and b.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form=58
group by a.id, a.trans_id, a.prod_id, a.grey_receive_qnty, a.inserted_by, b.po_breakdown_id, b.qc_pass_qnty_pcs, c.id
having c.id is null
order by a.id asc");
if(empty($recv_dtls_sql))
{
	echo "Data Not Found";
	die;
}

foreach($recv_dtls_sql  as $val)
{
	$dtls_id   = $val[csf("id")];
	$trans_id = $val[csf("trans_id")];
	$po_breakdown_id = $val[csf("po_breakdown_id")];
	$prod_id = $val[csf("prod_id")];
	$recv_qnty = $val[csf("grey_receive_qnty")];
	$is_sales = $val[csf("is_sales")];
	$inserted_by = $val[csf("inserted_by")];
	
	$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
	if ($dtls_id*1>0 &&  $trans_id*1>0 && $po_breakdown_id*1>0 && $prod_id*1>0 && $recv_qnty!="") 
	{
		if($data_array_prop!="") $data_array_prop.=", ";
		$data_array_prop.="(".$id_prop.",".$trans_id.",1,58,".$dtls_id.",".$po_breakdown_id.",".$prod_id.",".$recv_qnty.",".$inserted_by.",'".$pc_date_time."',".$is_sales.")";
	}
	else
	{
		echo "10**data missmatch";die;
	}
}
$field_details_array = "id,trans_id,trans_type,entry_form,dtls_id,po_breakdown_id,prod_id,quantity,inserted_by,insert_date,is_sales";*/
//echo "10**insert into order_wise_pro_details (".$field_details_array.") values ".$data_array_prop;die;
/*$rID = sql_insert("order_wise_pro_details",$field_details_array,$data_array_prop,1);
if($rID==1){
	oci_commit($con);
	echo "Success";
}else{
	oci_rollback($con);
	echo "failed";
}
die;*/

$issue_dtls_sql = sql_select("SELECT a.id, a.trans_id,  b.po_breakdown_id, a.prod_id, a.issue_qnty, max(b.is_sales) as is_sales, a.qty_in_pcs, c.id as prop_table_id, a.inserted_by
from inv_grey_fabric_issue_dtls a, pro_roll_details b left join order_wise_pro_details c on b.dtls_id=c.dtls_id and c.entry_form=61 and c.status_active=1
where a.status_active=1 and a.id=b.dtls_id and b.entry_form=61  
and b.status_active=1 and a.id in (16535,16536,16537,16538)
group by a.id, a.trans_id, b.po_breakdown_id, a.prod_id, a.issue_qnty, a.qty_in_pcs, c.id, a.inserted_by  
having c.id is null
order by a.id");

if(empty($issue_dtls_sql))
{
	echo "Data Not Found";
	die;
}

$field_details_array = "id,trans_id,trans_type,entry_form,dtls_id,po_breakdown_id,prod_id,quantity,inserted_by,insert_date,is_sales,quantity_pcs";
foreach($issue_dtls_sql  as $val)
{
	$dtls_id   = $val[csf("id")];
	$trans_id = $val[csf("trans_id")];
	$po_breakdown_id = $val[csf("po_breakdown_id")];
	$prod_id = $val[csf("prod_id")];
	$issue_qnty = $val[csf("issue_qnty")];
	$is_sales = $val[csf("is_sales")];
	$quantity_pcs = $val[csf("quantity_pcs")];
	$inserted_by = $val[csf("inserted_by")];
	
	$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);

	if ($dtls_id*1>0 &&  $trans_id*1>0 && $po_breakdown_id*1>0 && $prod_id*1>0 && $issue_qnty!="") 
	{
		if($data_array_prop!="") $data_array_prop.=", ";
		$data_array_prop.="(".$id_prop.",".$trans_id.",2,61,".$dtls_id.",".$po_breakdown_id.",".$prod_id.",".$issue_qnty.",".$inserted_by.",'".$pc_date_time."',".$is_sales.",'".$quantity_pcs."')";
	}
}
echo "10**insert into order_wise_pro_details (".$field_details_array.") values ".$data_array_prop;die;

/*$rID = sql_insert("order_wise_pro_details",$field_details_array,$data_array_prop,1);
if($rID==1){
	oci_commit($con);
	echo "Success";
}else{
	oci_rollback($con);
	echo "failed";
}
die;*/



 ?>
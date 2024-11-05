<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$transfer_sql=sql_select("select a.transfer_system_id,a.transfer_date, a.transfer_criteria,b.id dtls_id, b.body_part_id,b.to_body_part,c.trans_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b,order_wise_pro_details c,inv_transaction d
	where a.id = b.mst_id and b.id=c.dtls_id and c.trans_id=d.id
	and a.entry_form in (14,15) and a.transfer_criteria=2 and c.entry_form in(14,15) and c.trans_type=6 and d.transaction_type=6");

if(empty($transfer_sql))
{
	echo "Data Not Found";
	die;
}
if(!empty($transfer_sql)){
	foreach($transfer_sql as $val)
	{
		$trans_id_arr[$val[csf("trans_id")]] = $val[csf("trans_id")];
	}

	if(!empty($trans_id_arr)){
		$all_trans_ids=array_unique($trans_id_arr);
		if($db_type==2 && count($all_trans_ids)>999)
		{
			$trans_cond=" and (";
			$transArr=array_chunk($all_trans_ids,999);
			foreach($transArr as $ids)
			{
				$ids=rtrim(implode(",",$ids),", ");
				$trans_cond.=" a.issue_trans_id in($ids) or";
			}
			$trans_cond=chop($trans_cond,'or ');
			$trans_cond.=")";
		}
		else
		{
			$transids=rtrim(implode(",",$all_trans_ids),", ");
			$trans_cond=" and a.issue_trans_id in($transids)";
		}

		$receive_body_part_sql = "select a.issue_trans_id, c.body_part_id
		from inv_mrr_wise_issue_details a,inv_transaction b,pro_finish_fabric_rcv_dtls c
		where a.recv_trans_id=b.id and b.id=c.trans_id and a.entry_form in(14,15) and a.status_active=1 and b.status_active=1 and b.transaction_type in(1,4) and c.status_active=1 $trans_cond";
		$receive_body_part_data = sql_select($receive_body_part_sql);
		foreach($receive_body_part_data as $row)
		{
			$body_part_id_arr[$row[csf("issue_trans_id")]] = $row[csf("body_part_id")];
		}
	}

	foreach($transfer_sql as $val)
	{
		$dtls_id  = $val[csf("dtls_id")];
		$trans_id  = $val[csf("trans_id")];
		$body_part_id = $body_part_id_arr[$val[csf("trans_id")]];
		if($body_part_id!=""){
			//echo "update inv_item_transfer_dtls set body_part_id=$body_part_id,to_body_part=$body_part_id, updated_by=999 where id = ".$dtls_id." <br />";
			//execute_query("update inv_item_transfer_dtls set body_part_id=$body_part_id,to_body_part=$body_part_id,updated_by=888 where id=$dtls_id",0);
			//echo "update inv_transaction set body_part_id=$body_part_id, updated_by=999 where id = ".$trans_id." <br />";
			//execute_query("update inv_transaction set body_part_id=$body_part_id, updated_by=888 where id=$trans_id",0);
		}
	}
}

//oci_commit($con);
//echo "Success";
die;


?>
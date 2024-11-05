<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');

$con=connect();
if ($db_type == 0) 
{
	mysql_query("BEGIN");
}

$sql_transfer="select b.mst_id,b.prod_id,b.company_id,b.transaction_type,b.transaction_date,b.order_rate,b.order_qnty,b.order_amount,b.cons_quantity,b.cons_rate,b.cons_amount,b.store_id,b.cons_uom,b.inserted_by,b.insert_date,c.lot,c.brand from inv_transaction b, product_details_master c where b.prod_id=c.id and not exists (select id from inv_item_transfer_mst a where a.id=b.mst_id) and b.transaction_type in (5,6) and b.status_active=1 and b.prod_id in (905372,919382,919467,928058,653096,917695,925269,925275,891075,449578,840250,644404,467903,647020,654648,677206,677211,677222,677620,767713,869950,869953,870557,870558,870560,870562,888706,891072,895978,899062,583643,370704,488442,633162,877548,888593,925710,932208,915613,916052,921897,844452,928120,923335,923568,876979,631772,913485,916821,921716,921762,915021,933635,895841,899389,910351,912737,914255,914708,918995,919085,919820,928237,906058,902606,902738,867897,900205,910866,921714,914719,916013,880767,904570,631557,887276,891928,872173,705329,726897,695093,695254,833120) and b.mst_id not in (133677,134854)";
echo $sql_transfer;die;

//echo $sql_issue;die;
$result_sql_transfer=sql_select($sql_transfer);
$master_data = $dtls_data = array();
foreach($result_sql_transfer as $transRow)
{
	if($transRow[csf("transaction_type")]==6)
	{
		$company_id = $transRow[csf("company_id")];
		$from_prod_id = $transRow[csf("prod_id")];
		$from_store = $transRow[csf("store_id")];
	}
	else
	{
		$to_company_id = $transRow[csf("company_id")];
		$to_prod_id = $transRow[csf("prod_id")];
		$to_store = $transRow[csf("store_id")];
	}

	if($company_id!=$to_company_id)
	{
		$transfer_criteria = 1;
	}
	else
	{
		$transfer_criteria = 2;
	}

	$pre_pare_data[$transRow[csf("mst_id")]]['mst_id'] = $transRow[csf("mst_id")];
	$pre_pare_data[$transRow[csf("mst_id")]]['company_id'] = $company_id;
	$pre_pare_data[$transRow[csf("mst_id")]]['to_company'] = $company_id;
	$pre_pare_data[$transRow[csf("mst_id")]]['transfer_date'] = $transRow[csf("transaction_date")];
	$pre_pare_data[$transRow[csf("mst_id")]]['transfer_criteria'] = $transfer_criteria;	
	$pre_pare_data[$transRow[csf("mst_id")]]['inserted_by'] = $transRow[csf("inserted_by")];
	$pre_pare_data[$transRow[csf("mst_id")]]['insert_date'] = $transRow[csf("insert_date")];

	$pre_pare_data[$transRow[csf("mst_id")]]['mst_id'] = $transRow[csf("mst_id")];
	$pre_pare_data[$transRow[csf("mst_id")]]['from_prod_id'] = $from_prod_id;
	$pre_pare_data[$transRow[csf("mst_id")]]['to_prod_id'] = $to_prod_id;
	$pre_pare_data[$transRow[csf("mst_id")]]['from_store'] = $from_store;
	$pre_pare_data[$transRow[csf("mst_id")]]['to_store'] = $to_store;
	$pre_pare_data[$transRow[csf("mst_id")]]['yarn_lot'] = $transRow[csf("lot")];
	$pre_pare_data[$transRow[csf("mst_id")]]['brand_id'] = $transRow[csf("brand")];
	$pre_pare_data[$transRow[csf("mst_id")]]['transfer_qnty'] = $transRow[csf("cons_quantity")];
	$pre_pare_data[$transRow[csf("mst_id")]]['rate'] = $transRow[csf("cons_rate")];
	$pre_pare_data[$transRow[csf("mst_id")]]['transfer_value'] = $transRow[csf("cons_amount")];
	$pre_pare_data[$transRow[csf("mst_id")]]['rate_in_usd'] = $transRow[csf("order_rate")];
	$pre_pare_data[$transRow[csf("mst_id")]]['transfer_value_in_usd'] = $transRow[csf("order_amount")];
	$pre_pare_data[$transRow[csf("mst_id")]]['uom'] = $transRow[csf("cons_uom")];
	$pre_pare_data[$transRow[csf("mst_id")]]['inserted_by'] = $transRow[csf("inserted_by")];
	$pre_pare_data[$transRow[csf("mst_id")]]['insert_date'] = $transRow[csf("insert_date")];
}

//echo "<pre>";
//print_r($pre_pare_data);
//die();

foreach($pre_pare_data as $data)
{
	$company_id = $data['company_id'];
	$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_item_transfer_mst",$con,1,$company_id,'YTE',10,date("Y",time()),1 ));

	$id = $data['mst_id'];

	$transfer_prefix = $new_transfer_system_id[1];
	$transfer_prefix_number = $new_transfer_system_id[2];
	$transfer_system_id = $new_transfer_system_id[0];
	$company_id = $data['company_id'];
	$transfer_date = $data['transfer_date'];
	$transfer_criteria = $data['transfer_criteria'];
	$item_category = 1;
	$inserted_by = $data['inserted_by'];
	$insert_date = $data['insert_date'];
	$updated_by = 777;
	$entry_form = 10;


	$mrrId=execute_query("insert into inv_item_transfer_mst (id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, transfer_date, transfer_criteria,item_category,inserted_by, insert_date,updated_by,entry_form) values(".$id.",'".$transfer_prefix."',".$transfer_prefix_number.",'".$transfer_system_id."',".$company_id.",'".$transfer_date."',".$transfer_criteria.",".$item_category.",".$inserted_by.",'".$insert_date."',".$updated_by.",".$entry_form.")");
	
	if($mrrId==false)
	{
		echo "insert into inv_item_transfer_mst (id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, transfer_date, transfer_criteria,item_category,inserted_by, insert_date,updated_by,entry_form) values(".$id.",'".$transfer_prefix."',".$transfer_prefix_number.",'".$transfer_system_id."',".$company_id.",'".$transfer_date."',".$transfer_criteria.",".$item_category.",".$inserted_by.",'".$insert_date."',".$updated_by.",".$entry_form.")";
		oci_rollback($con); disconnect($con);die;
	}

	$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
	$mst_id = $data['mst_id']; 
	$from_prod_id = $data['from_prod_id'];
	$to_prod_id = ($data['to_prod_id']=='')?0:$data['to_prod_id'];
	$yarn_lot = $data['yarn_lot'];
	$brand_id = $data['brand_id'];
	$from_store = $data['from_store'];
	$to_store = ($data['to_store']=='')?0:$data['to_store'];
	$item_category = 1;
	$transfer_qnty = $data['transfer_qnty'];
	$rate = $data['rate'];
	$transfer_value = $data['transfer_value'];
	$rate_in_usd = $data['rate_in_usd'];
	$transfer_value_in_usd = $data['transfer_value_in_usd'];
	$uom = $data['uom'];
	$inserted_by = $data['inserted_by'];
	$insert_date = $data['insert_date'];

	$mrrdtlsId=execute_query("insert into inv_item_transfer_dtls (id, mst_id, from_prod_id, to_prod_id, yarn_lot, brand_id, from_store,to_store,item_category,transfer_qnty, rate, transfer_value, rate_in_usd, transfer_value_in_usd, uom, inserted_by, insert_date) values(".$id.",".$mst_id.",".$from_prod_id.",".$to_prod_id.",'".$yarn_lot."',".$brand_id.",".$from_store.",".$to_store.",".$item_category.",".$transfer_qnty.",".$rate.",".$transfer_value.",".$rate_in_usd.",".$transfer_value_in_usd.",".$uom.",".$inserted_by.",'".$insert_date."')");
	
	if($mrrdtlsId==false)
	{
		echo "insert into inv_item_transfer_dtls (id, mst_id, from_prod_id, to_prod_id, yarn_lot, brand_id, from_store,to_store,item_category,transfer_qnty, rate, transfer_value, rate_in_usd, transfer_value_in_usd, uom, inserted_by, insert_date) values(".$id.",".$mst_id.",".$from_prod_id.",".$to_prod_id.",'".$yarn_lot."',".$brand_id.",".$from_store.",".$to_store.",".$item_category.",".$transfer_qnty.",".$rate.",".$transfer_value.",".$rate_in_usd.",".$transfer_value_in_usd.",".$uom.",".$inserted_by.",'".$insert_date."')";
		oci_rollback($con); disconnect($con);die;
	}


}


//echo $mrrId."##".$mrrdtlsId;oci_rollback($con); disconnect($con);die;

if($mrrId && $mrrdtlsId)
{
	oci_commit($con); 
	echo "Success";
}
else
{
	oci_rollback($con); 
	echo "Failed";
}
disconnect($con);
die;


?>
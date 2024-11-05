<?
include('includes/common.php');




$sql_rcv=sql_select("select sizeset_no ,job_no,color_id    from ppl_size_set_mst a,ppl_size_set_dtls b 
where a.id=b.mst_id group by sizeset_no,job_no,color_id  order by job_no,color_id ");


foreach($sql_rcv as $row)
{
	//if($update_data_arr_size_set[$row[csf('job_no')]][$row[csf('color_id')]]=="")
	//{

		$update_data_arr_size_set[$row[csf('job_no')]][$row[csf('color_id')]]=$row[csf('sizeset_no')];
		$job_arr[]=$row[csf('job_no')];
	//}
}


$sql=sql_select("select a.id,a.job_no,b.color_id from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and entry_form=253");


foreach($sql as $row)
{
	$update_id_arr[]=$row[csf('id')];
	$update_data_arr[$row[csf('id')]]=explode("*",("'".$update_data_arr_size_set[$row[csf('job_no')]][$row[csf('color_id')]]."'"));
}



//print_r($update_data_arr);die;


$update_field="size_set_no";
//echo implode(",",$update_id_arr);die;
$upsubDtlsID=bulk_update_sql_statement("ppl_cut_lay_mst","id",$update_field,$update_data_arr,$update_id_arr);
echo $upsubDtlsID."<br/><br/>";die;


//echo "dsf dsf dsf ds ";die;
$sql_rcv=sql_select("select  sum(a.cons_quantity) as qty, sum(a.cons_amount) as cons_amount,a.prod_id ,b.current_stock, b.stock_value, b.available_qnty,b.allocated_qnty
from  inv_transaction a,product_details_master b where a.prod_id=b.id and a.transaction_type=1 and a.job_no='SSL-19-00370' and a.status_active=1
group by a.prod_id,b.current_stock, b.stock_value, b.available_qnty,b.allocated_qnty");

foreach($sql_rcv as $row)
{
	$current_stock=$row[csf('current_stock')]-$row[csf('qty')];
	//echo $row[csf('current_stock')]."**".$row[csf('qty')];die;
	$cons_amount=$row[csf('stock_value')]-$row[csf('cons_amount')];
	//echo $current_stock;die;
	if($current_stock*1==0) {
		$rate=0;$cons_amount=0;
		
	}
	else {
		$rate=$cons_amount/$current_stock;
		
	}

	$update_id_arr[]=$row[csf('prod_id')];
	$update_data_arr[$row[csf('prod_id')]]=explode("*",("".$current_stock."*".$cons_amount."*".$rate.""));
	
}
$update_field="current_stock*stock_value*avg_rate_per_unit";
//echo implode(",",$update_id_arr);die;
$upsubDtlsID=bulk_update_sql_statement("product_details_master","id",$update_field,$update_data_arr,$update_id_arr);
echo $upsubDtlsID."<br/><br/>";die;



























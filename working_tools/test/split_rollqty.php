<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
 //die;
$barcode_sql="select a.id, a.grey_receive_qnty, b.quantity, c.qnty  from pro_grey_prod_entry_dtls a, order_wise_pro_details b, pro_roll_details c where a.id=b.dtls_id and b.dtls_id=c.dtls_id and a.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.entry_form=58 and c.entry_form=58 order by a.id desc"; 

foreach(sql_select($barcode_sql) as $v)
{
	/*if($v[csf("quantity")]!=$v[csf("qnty")])
	{*/
		$barcode_no_arr[$v[csf("id")]]['dqty']=$v[csf("grey_receive_qnty")];
		$barcode_no_arr[$v[csf("id")]]['oqty']=$v[csf("quantity")];
		$barcode_no_arr[$v[csf("id")]]['rqty']+=$v[csf("qnty")];
		
	//}
}
 

 
 $i=0;
foreach($barcode_no_arr as $id=>$q)
{
	if($q['oqty']!=$q['rqty'])
	{
		//echo $q['oqty'].'='.$q['rqty'].'<br>';
		$qty=0;
		$qty=$q['rqty'];
		//echo "UPDATE pro_grey_prod_entry_dtls set grey_receive_qnty='$qty' where id ='$id'".'<br>';
		$up=execute_query("UPDATE pro_grey_prod_entry_dtls set grey_receive_qnty='$qty' where id ='$id' and status_active=1 and is_deleted=0",1);
	
		$up_dtls=execute_query("UPDATE order_wise_pro_details set quantity='$qty' where dtls_id ='$id' and status_active=1 and is_deleted=0",1);
		$i++;
	}
	
}
 echo $i;
 
 
//die;

if($db_type==0)
{
	mysql_query("COMMIT");
	echo "success mysql";
}
else
{
	oci_commit($con);
	echo "success in oracle";
}

//$up=execute_query("UPDATE pro_qc_result_mst set status_active=0,is_deleted=11 where barcode_no in($all_barcodes) and ");
//echo "<pre>";print_r($barcode_wise_max_id);die;
?>
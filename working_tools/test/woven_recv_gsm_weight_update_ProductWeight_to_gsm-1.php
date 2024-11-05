<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$prod_sql=sql_select("select b.id,d.weight from inv_receive_master a,pro_finish_fabric_rcv_dtls b,inv_transaction c,  product_details_master d  where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and b.prod_id=d.id and a.entry_form = 17 and c.item_category=3 and c.transaction_type=1  group by b.id,d.weight");

////and c.id in(116147,116145) 

if(empty($prod_sql))
{
	echo "Data Not Found";
	die;
}

foreach($prod_sql as $val)
{
	$dtls_arr[$val[csf("id")]] = $val[csf("id")];
	$weightArr[$val[csf("id")]] = $val[csf("weight")];
}

$dtls_arr = array_filter($dtls_arr);

$i=0;
$weight = "";
foreach ($dtls_arr as  $dtls_id) 
{
	$weight =  $weightArr[$dtls_id];
	echo "update pro_finish_fabric_rcv_dtls set gsm = '".$weight. "', updated_by = 999 where id = ".$dtls_id." <br>";
	//execute_query("update pro_finish_fabric_rcv_dtls set gsm = '".$weight. "', updated_by = 999 where id = ".$dtls_id,0);


	$i++;
}
echo "<br/>total excuted row = " .$i."<br/>";


/*oci_commit($con);
echo "Success"; 
die;*/


?>
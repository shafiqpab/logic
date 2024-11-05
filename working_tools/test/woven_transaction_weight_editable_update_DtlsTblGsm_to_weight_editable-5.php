<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$rcv_dtls_sql=sql_select("select c.id,b.gsm as weight from inv_receive_master a,pro_finish_fabric_rcv_dtls b,inv_transaction c  where a.id=b.mst_id and b.trans_id=c.id and a.entry_form = 17 and c.item_category=3 and c.transaction_type=1 group by c.id,b.gsm");

//and c.id in(116147,116145) 

if(empty($rcv_dtls_sql))
{
	echo "Data Not Found";
	die;
}

foreach($rcv_dtls_sql as $val)
{
	$trans_arr[$val[csf("id")]] = $val[csf("id")];
	$weightArr[$val[csf("id")]] = $val[csf("weight")];
}

$trans_arr = array_filter($trans_arr);

$i=0;
$weight = "";
foreach ($trans_arr as  $trans_id) 
{
	$weight =  $weightArr[$trans_id];
	echo "update inv_transaction set weight_editable = '".$weight. "', updated_by = 999 where id = ".$trans_id." <br>";
	//execute_query("update inv_transaction set weight_editable = '".$weight. "', updated_by = 999 where id = ".$trans_id,0);


	$i++;
}
echo "<br/>total excuted row = " .$i."<br/>";


/*oci_commit($con);
echo "Success"; 
die;*/


?>
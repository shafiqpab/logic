<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$prod_sql=sql_select("select a.dtls_id as program_no,a.buyer_id from PPL_PLANNING_ENTRY_PLAN_DTLS a,INV_RECEIVE_MASTER b where a.dtls_id=b.booking_id and b.ENTRY_FORM = 2
 AND b.RECEIVE_BASIS = 2
 AND b.STATUS_ACTIVE = 1
AND b.ITEM_CATEGORY = 13
AND b.KNITTING_SOURCE = 1 and b.buyer_id=0  group by a.dtls_id,a.buyer_id");


////and c.id in(116147,116145) 

if(empty($prod_sql))
{
	echo "Data Not Found";
	die;
}

foreach($prod_sql as $val)
{
	$prog_arr[$val[csf("program_no")]] = $val[csf("program_no")];
	$buyerIdArr[$val[csf("program_no")]] = $val[csf("buyer_id")];
}

$prog_arr = array_filter($prog_arr);

$i=0;
$buyerId = "";
foreach ($prog_arr as  $prog_id) 
{
	$buyerId =  $buyerIdArr[$prog_id];
	echo "update INV_RECEIVE_MASTER set buyer_id = '".$buyerId. "', updated_by = 999 where booking_id = ".$prog_id." and buyer_id=0 <br>";
	//execute_query("update INV_RECEIVE_MASTER set buyer_id = '".$buyerId. "', updated_by = 999 where booking_id = ".$prog_id,0);


	$i++;
}
echo "<br/>total excuted row = " .$i."<br/>";


/*oci_commit($con);
echo "Success"; 
die;*/


?>
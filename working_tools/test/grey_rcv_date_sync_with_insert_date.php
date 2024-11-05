<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

if($db_type == 0){
	echo "Not For Mysql";
	die;
}

$mis_match_sql=sql_select("select id, recv_number,receive_date,to_char(insert_date,'mm/dd/yyyy') as insert_date
from inv_receive_master where entry_form = 58 and status_active =1 and receive_date > insert_date ");

if(empty($mis_match_sql))
{
	echo "Mismatch Data Not Found";
	die;
}

foreach($mis_match_sql as $val)
{
	$rcv_arr[$val[csf("id")]] = $val[csf("id")];
	$receive_ref[$val[csf("id")]]["insert_date"] = $val[csf("insert_date")];
}

$rcv_arr = array_filter($rcv_arr);

foreach ($rcv_arr as  $rcv_id) 
{
	$insert_date = date("d-M-Y",strtotime($receive_ref[$rcv_id]["insert_date"]));
	if($insert_date)
	{
		//echo "update inv_receive_master set receive_date = '".$insert_date."', updated_by = 999 where id = ".$rcv_id." <br>";
		//echo "update inv_transaction set transaction_date = '".$insert_date."', updated_by = 999 where mst_id = $rcv_id and item_category = 13 and transaction_type = 1 and status_active =1 "." <br>";

		execute_query("update inv_receive_master set receive_date = '".$insert_date."', updated_by = 999 where id = ".$rcv_id,0);
		execute_query("update inv_transaction set transaction_date = '".$insert_date."', updated_by = 999 where mst_id = $rcv_id and item_category = 13 and transaction_type = 1 and status_active =1",0);
	}
	
}

oci_commit($con);
echo "Success"; 
die;


?>
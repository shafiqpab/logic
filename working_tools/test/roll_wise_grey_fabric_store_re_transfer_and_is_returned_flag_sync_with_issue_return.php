<?
/*
	Re_transfer flag will update after all sorts of receives (rcv,transfer,issue return).
	Is_return flag will update after all transactions.

*/
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$splited_sql="select barcode_no from  pro_roll_details where roll_split_from > 0  and status_active = 1";
$splited_data = sql_select($splited_sql);
foreach ($splited_data as $row)
{
	$splited_data_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
}

$barcode_sql=sql_select("select barcode_no, max(case when entry_form in (2,22,56,58,82,83,110,183,133,61,84) then id else 0 end) as max_id, max(case when entry_form in (2,22,58,82,83,110,183,133,84) then id else 0 end) as max_rcv_id from pro_roll_details where status_active=1 and entry_form in (2,22,56,58,82,83,110,183,133,61,84) and barcode_no in (select barcode_no from pro_roll_details where entry_form =84 and status_active=1 group by barcode_no) group by barcode_no");

//and barcode_No =20020037121

foreach ($barcode_sql as $row)
{
	$barcode_data[$row[csf("barcode_no")]]["max_id"] = $row[csf("max_id")];
	$barcode_data[$row[csf("barcode_no")]]["max_rcv_id"] = $row[csf("max_rcv_id")];
}

foreach ($barcode_data as $barcode_no => $row)
{
	if($splited_data_arr[$barcode_no] == "")
	{
		echo "update pro_roll_details 
		set is_returned = (case when entry_form in (2,22,56,58,82,83,110,183,133,61,84) and id <>".$row['max_id']." then 1 else 0 end), 
		re_transfer = (case when entry_form in (2,22,58,82,83,110,183,133,84) and id <> ".$row['max_rcv_id']." then 1 else 0 end) 
		where barcode_No =$barcode_no and entry_form in (2,22,56,58,82,83,110,183,133,61,84)";
	}
}

/*oci_commit($con);
echo "Success";
die;*/
?>
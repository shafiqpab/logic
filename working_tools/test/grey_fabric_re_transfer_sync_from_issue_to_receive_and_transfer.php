<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$splited_sql="select barcode_no from  pro_roll_details where roll_split_from > 0  and status_active = 1";
//entry_form = 82  and
$splited_data = sql_select($splited_sql);
foreach ($splited_data as $row)
{
	$splited_data_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
}
//20021050122,20021054134,20021054336,20021042853,20021044552,,20021047213,20021088725,20021068862,20021054868,20021052021,20021054463,20021054146,20021053933,20021149371,20021160855,20021216028,20021282199,20021293900,20021286888,20021178164,20021205334,20020374624,
$barcode_data=sql_select("select id, entry_form, dtls_id, barcode_no from  pro_roll_details where entry_form in (22,58,82,83,84,110,180,183,61) and status_active =1 and status_active =1 and barcode_no >0 and barcode_no in(19020097792)  order by barcode_no, id desc");

foreach ($barcode_data as $row)
{
	if($splited_data_arr[$row[csf("barcode_no")]] == "")
	{
		if($barcode_no_chk[$row[csf("barcode_no")]] =="")
		{
			$barcode_no_chk[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
			$pre_store_id="";
		}

		if(($row[csf("entry_form")] == 58 || $row[csf("entry_form")] == 82 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 110 || $row[csf("entry_form")] == 180 || $row[csf("entry_form")] == 183) && $source_rcv_arr[$row[csf("barcode_no")]] =="")
		{
			$source_rcv_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
			//echo "update pro_roll_details set re_transfer = 0 where id = ".$row[csf("id")] ." Entry_form=".$row[csf("entry_form")]."<br>";

			$rID=execute_query("update pro_roll_details set re_transfer = 0 where id = ".$row[csf("id")], 0);
			if($rID == 0)
			{
				echo "Failed run script <br>";
				echo "update pro_roll_details set re_transfer = 0 where id = ".$row[csf("id")]."<br>";
				oci_rollback($con);
				disconnect($con);
				die;

			}
		}
		else if(($row[csf("entry_form")] == 58 || $row[csf("entry_form")] == 82 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 110 || $row[csf("entry_form")] == 180 || $row[csf("entry_form")] == 183) && $source_rcv_arr[$row[csf("barcode_no")]] !="")
		{
			//echo "update pro_roll_details set re_transfer = 1 where id = ".$row[csf("id")] ." Entry_form=".$row[csf("entry_form")]."<br>";

			$rID1=execute_query("update pro_roll_details set re_transfer = 1 where id = ".$row[csf("id")], 0);
			if($rID1 ==0)
			{
				echo "Failed run script <br>";
				echo "update pro_roll_details set re_transfer = 1 where id = ".$row[csf("id")] ."<br>";
				oci_rollback($con);
				disconnect($con);
				die;
			}
		}
	}
}



oci_commit($con);
echo "Success";
die;
?>
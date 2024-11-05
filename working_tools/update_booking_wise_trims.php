<?
include('../includes/common.php');
$con = connect();

$trim_group_arr =array();
$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group where item_category=4");
foreach($data_array as $row)
{
	$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
	$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
	$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
}

$pi_mis_sql="select a.BOOKING_NO, b.PO_BREAK_DOWN_ID, a.TRIM_GROUP, b.COLOR_NUMBER_ID, b.GMTS_SIZES, b.ITEM_COLOR, b.ITEM_SIZE, b.BRAND_SUPPLIER, b.DESCRIPTION, b.RATE
  from wo_booking_dtls a, wo_trim_book_con_dtls b
where a.id=b.WO_TRIM_BOOKING_DTLS_ID and a.BOOKING_TYPE=2 and a.status_active=1 and b.status_active=1 and b.cons>0 and b.BOOKING_NO in('CTL-TB-22-01192','CTL-TB-22-01704','CTL-TB-22-00680')
group by a.BOOKING_NO, a.TRIM_GROUP, b.PO_BREAK_DOWN_ID, b.COLOR_NUMBER_ID, b.GMTS_SIZES, b.ITEM_COLOR, b.ITEM_SIZE, b.BRAND_SUPPLIER, b.DESCRIPTION, b.RATE";

$pi_mis_result=sql_select($pi_mis_sql);
$booking_data=array();
foreach($pi_mis_result as $row)
{
	$booking_data[$row["BOOKING_NO"]][$row["PO_BREAK_DOWN_ID"]][$row["TRIM_GROUP"]][$row["COLOR_NUMBER_ID"]][$row["GMTS_SIZES"]][$row["ITEM_COLOR"]][$row["ITEM_SIZE"]][$row["BRAND_SUPPLIER"]][$row["DESCRIPTION"]]=$row[csf("RATE")];
}

$recv_sql="select a.ID, a.TRANS_ID, a.BOOKING_NO, b.PO_BREAKDOWN_ID, a.ITEM_GROUP_ID, a.GMTS_COLOR_ID, a.GMTS_SIZE_ID, a.ITEM_COLOR, a.ITEM_SIZE, a.BRAND_SUPPLIER, a.ITEM_DESCRIPTION, a.RATE, a.CONS_RATE, a.RECEIVE_QNTY, a.CONS_QNTY, p.EXCHANGE_RATE 
from INV_RECEIVE_MASTER p, INV_TRIMS_ENTRY_DTLS a, ORDER_WISE_PRO_DETAILS b 
where p.id=a.mst_id and a.trans_id=b.trans_id and a.id=b.dtls_id and b.entry_form=24 and a.status_active=1 and b.status_active=1 and a.BOOKING_NO in('CTL-TB-22-01192','CTL-TB-22-01704','CTL-TB-22-00680') ";

$recv_sql_result=sql_select($recv_sql);
$book_rid=$trans_rid=$propo_rid=true;
foreach($recv_sql_result as $row)
{
	$dtls_id=$row["ID"];
	$trans_id=$row["TRANS_ID"];
	$booking_rate=$booking_data[$row["BOOKING_NO"]][$row["PO_BREAKDOWN_ID"]][$row["ITEM_GROUP_ID"]][$row["GMTS_COLOR_ID"]][$row["GMTS_SIZE_ID"]][$row["ITEM_COLOR"]][$row["ITEM_SIZE"]][$row["BRAND_SUPPLIER"]][str_replace(", [BS]","",$row["ITEM_DESCRIPTION"])];
	if($booking_rate && $booking_rate!=$row["RATE"])
	{
		
		$ord_amount=$row["RECEIVE_QNTY"]*$booking_rate;
		$cons_rate=(($booking_rate/$trim_group_arr[$row['ITEM_GROUP_ID']]['conversion_factor'])*$row['EXCHANGE_RATE']);
		$cons_amount=$cons_rate*$row['CONS_QNTY'];
		
		echo $booking_rate."=".$row["RATE"]."=".$row["CONS_RATE"]."=".$cons_rate;die;
		
		$book_rid=execute_query("update INV_TRIMS_ENTRY_DTLS set RATE='$booking_rate', AMOUNT='$ord_amount', CONS_RATE='$cons_rate', BOOK_KEEPING_CURR='$cons_amount' where id=$dtls_id");
		if($book_rid==false)
		{
			echo "update INV_TRIMS_ENTRY_DTLS set RATE='$booking_rate', AMOUNT='$ord_amount', CONS_RATE='$cons_rate', BOOK_KEEPING_CURR='$cons_amount' where id=$dtls_id";oci_rollback($con);disconnec($con);die;
		}
		
		$trans_rid=execute_query("update INV_TRANSACTION set ORDER_RATE='$booking_rate', ORDER_AMOUNT='$ord_amount', CONS_RATE='$cons_rate', CONS_AMOUNT='$cons_amount' where id=$trans_id");
		if($trans_rid==false)
		{
			echo "update INV_TRANSACTION set ORDER_RATE='$booking_rate', ORDER_AMOUNT='$ord_amount', CONS_RATE='$cons_rate', CONS_AMOUNT='$cons_amount' where id=$trans_id";oci_rollback($con);disconnec($con);die;
		}
		
		$propo_rid=execute_query("update ORDER_WISE_PRO_DETAILS set ORDER_RATE='$booking_rate', ORDER_AMOUNT=QUANTITY*$booking_rate where TRANS_ID=$trans_id");
		if($propo_rid==false)
		{
			echo "update ORDER_WISE_PRO_DETAILS set ORDER_RATE='$booking_rate', ORDER_AMOUNT=QUANTITY*$booking_rate where TRANS_ID=$trans_id";oci_rollback($con);disconnec($con);die;
		}
		
	}
}

//echo $rID;die;
//echo $rID."<br>".$rID2;die;

if($book_rid && $trans_rid && $propo_rid)
{
	oci_commit($con); 
	echo "Success";
}
else
{
	oci_rollback($con); 
	echo "Failed";
}

?>
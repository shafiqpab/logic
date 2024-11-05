<?
include('../includes/common.php');
$con = connect();

$trim_group_arr =array();
$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group");
foreach($data_array as $row)
{
	$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
	$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
	$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
}

//$pi_mis_sql="select a.BOOKING_NO, b.PO_BREAK_DOWN_ID, a.TRIM_GROUP, b.COLOR_NUMBER_ID, b.GMTS_SIZES, b.ITEM_COLOR, b.ITEM_SIZE, b.BRAND_SUPPLIER, b.DESCRIPTION, b.RATE
//  from wo_booking_dtls a, wo_trim_book_con_dtls b
//where a.id=b.WO_TRIM_BOOKING_DTLS_ID and a.BOOKING_TYPE=2 and a.status_active=1 and b.status_active=1 and b.cons>0 and b.BOOKING_NO in('AST-TB-22-02037','AST-TB-22-01840')
//group by a.BOOKING_NO, a.TRIM_GROUP, b.PO_BREAK_DOWN_ID, b.COLOR_NUMBER_ID, b.GMTS_SIZES, b.ITEM_COLOR, b.ITEM_SIZE, b.BRAND_SUPPLIER, b.DESCRIPTION, b.RATE";

/*$pi_mis_sql="select a.BOOKING_NO, b.PO_BREAK_DOWN_ID, a.TRIM_GROUP, b.COLOR_NUMBER_ID, b.GMTS_SIZES, b.ITEM_COLOR, b.ITEM_SIZE, b.BRAND_SUPPLIER, b.DESCRIPTION, b.RATE, b.CONS, b.AMOUNT
  from wo_booking_dtls a, wo_trim_book_con_dtls b
where a.id=b.WO_TRIM_BOOKING_DTLS_ID and a.booking_no=b.booking_no and a.BOOKING_TYPE=2 and a.status_active=1 and b.status_active=1 and b.cons>0 and b.BOOKING_NO in('AST-TB-22-01952','AST-TB-22-02358','AST-TB-22-01953','AST-TB-22-02194','AST-TB-22-01625','AST-TB-22-01548','AST-TB-22-02221','AST-TB-22-02238','AST-TB-22-02444','AST-TB-22-01954','AST-TB-22-01551','AST-TB-22-01549','AST-TB-22-02297','AST-TB-22-02426','AST-TB-22-02166','AST-TB-22-02504','AST-TB-22-02359','AST-TB-22-02428','AST-TB-22-02578','AST-TB-22-02190','AST-TB-22-02200','AST-TB-22-01080','AST-TB-22-01546','AST-TB-22-02356','AST-TB-22-02298','AST-TB-22-02184','AST-TB-22-02388')";
*/
$pi_mis_sql="select b.WORK_ORDER_NO as BOOKING_NO, b.ITEM_GROUP as TRIM_GROUP, b.COLOR_ID as COLOR_NUMBER_ID, b.SIZE_ID as GMTS_SIZES, b.ITEM_COLOR, b.ITEM_SIZE, b.BRAND_SUPPLIER, b.ITEM_DESCRIPTION as DESCRIPTION, b.RATE, b.QUANTITY as CONS, b.NET_PI_AMOUNT as AMOUNT
  from COM_PI_ITEM_DETAILS b
where b.status_active=1 and b.pi_id=32172";
//echo $pi_mis_sql;
$pi_mis_result=sql_select($pi_mis_sql);
$booking_data=array();
foreach($pi_mis_result as $row)
{
	//$booking_data[$row["BOOKING_NO"]][$row["PO_BREAK_DOWN_ID"]][$row["TRIM_GROUP"]][$row["COLOR_NUMBER_ID"]][$row["GMTS_SIZES"]][$row["ITEM_COLOR"]][$row["ITEM_SIZE"]][$row["BRAND_SUPPLIER"]][$row["DESCRIPTION"]]=$row[csf("RATE")];
	$booking_data[$row["BOOKING_NO"]][$row["TRIM_GROUP"]][$row["COLOR_NUMBER_ID"]][$row["GMTS_SIZES"]][$row["ITEM_COLOR"]][$row["ITEM_SIZE"]][$row["BRAND_SUPPLIER"]][$row["DESCRIPTION"]]["CONS"]+=$row[csf("CONS")];
	$booking_data[$row["BOOKING_NO"]][$row["TRIM_GROUP"]][$row["COLOR_NUMBER_ID"]][$row["GMTS_SIZES"]][$row["ITEM_COLOR"]][$row["ITEM_SIZE"]][$row["BRAND_SUPPLIER"]][$row["DESCRIPTION"]]["AMOUNT"]+=$row[csf("AMOUNT")];
	
}

//echo "<pre>";print_r($booking_data);die;

//$recv_sql="select a.ID, a.TRANS_ID, a.BOOKING_NO, b.PO_BREAKDOWN_ID, a.ITEM_GROUP_ID, a.GMTS_COLOR_ID, a.GMTS_SIZE_ID, a.ITEM_COLOR, a.ITEM_SIZE, a.BRAND_SUPPLIER, a.ITEM_DESCRIPTION, a.RATE, a.CONS_RATE, a.RECEIVE_QNTY, a.CONS_QNTY, p.EXCHANGE_RATE 
//from INV_RECEIVE_MASTER p, INV_TRIMS_ENTRY_DTLS a, ORDER_WISE_PRO_DETAILS b 
//where p.id=a.mst_id and a.trans_id=b.trans_id and a.id=b.dtls_id and b.entry_form=24 and a.status_active=1 and b.status_active=1 and a.BOOKING_NO in('AST-TB-22-02037','AST-TB-22-01840') ";

/*$recv_sql="select a.ID, a.TRANS_ID, a.BOOKING_NO, a.ITEM_GROUP_ID, a.GMTS_COLOR_ID, a.GMTS_SIZE_ID, a.ITEM_COLOR, a.ITEM_SIZE, a.BRAND_SUPPLIER, a.ITEM_DESCRIPTION, a.RATE, a.CONS_RATE, a.RECEIVE_QNTY, a.CONS_QNTY, p.EXCHANGE_RATE 
from INV_RECEIVE_MASTER p, INV_TRIMS_ENTRY_DTLS a
where p.id=a.mst_id and p.entry_form=24 and a.status_active=1 and a.BOOKING_NO in('AST-TB-22-01952','AST-TB-22-02358','AST-TB-22-01953','AST-TB-22-02194','AST-TB-22-01625','AST-TB-22-01548','AST-TB-22-02221','AST-TB-22-02238','AST-TB-22-02444','AST-TB-22-01954','AST-TB-22-01551','AST-TB-22-01549','AST-TB-22-02297','AST-TB-22-02426','AST-TB-22-02166','AST-TB-22-02504','AST-TB-22-02359','AST-TB-22-02428','AST-TB-22-02578','AST-TB-22-02190','AST-TB-22-02200','AST-TB-22-01080','AST-TB-22-01546','AST-TB-22-02356','AST-TB-22-02298','AST-TB-22-02184','AST-TB-22-02388') ";*/

$recv_sql="select a.ID, a.TRANS_ID, b.WORK_ORDER_NO as BOOKING_NO, a.ITEM_GROUP_ID, a.GMTS_COLOR_ID, a.GMTS_SIZE_ID, a.ITEM_COLOR, a.ITEM_SIZE, a.BRAND_SUPPLIER, a.ITEM_DESCRIPTION, a.RATE, a.CONS_RATE, a.RECEIVE_QNTY, a.CONS_QNTY, p.EXCHANGE_RATE, a.PAYMENT_OVER_RECV, a.TRANS_ID 
from INV_RECEIVE_MASTER p, INV_TRIMS_ENTRY_DTLS a, COM_PI_ITEM_DETAILS b
where p.id=a.mst_id and a.booking_id=b.pi_id and a.ITEM_GROUP_ID=b.ITEM_GROUP and a.GMTS_COLOR_ID=b.COLOR_ID and a.ITEM_COLOR=b.ITEM_COLOR 
and a.GMTS_SIZE_ID=b.SIZE_ID and nvl(a.ITEM_SIZE,0) = nvl(b.ITEM_SIZE,0) 
and nvl(a.BRAND_SUPPLIER,0)=nvl(b.BRAND_SUPPLIER,0) and a.ITEM_DESCRIPTION=b.ITEM_DESCRIPTION 
and p.entry_form=24 and a.status_active=1 and b.status_active=1 and a.BOOKING_ID=32172 and b.pi_id=32172";

$recv_sql_result=sql_select($recv_sql);
$book_rid=$trans_rid=$propo_rid=true;
foreach($recv_sql_result as $row)
{
	$dtls_id=$row["ID"];
	$trans_id=$row["TRANS_ID"];
	//$booking_rate=$booking_data[$row["BOOKING_NO"]][$row["PO_BREAKDOWN_ID"]][$row["ITEM_GROUP_ID"]][$row["GMTS_COLOR_ID"]][$row["GMTS_SIZE_ID"]][$row["ITEM_COLOR"]][$row["ITEM_SIZE"]][$row["BRAND_SUPPLIER"]][str_replace(", [BS]","",$row["ITEM_DESCRIPTION"])];
	if($trans_check[$row["TRANS_ID"]]=="")
	{
	$trans_check[$row["TRANS_ID"]]=$row["TRANS_ID"];
	$booking_qnty=$booking_data[$row["BOOKING_NO"]][$row["ITEM_GROUP_ID"]][$row["GMTS_COLOR_ID"]][$row["GMTS_SIZE_ID"]][$row["ITEM_COLOR"]][$row["ITEM_SIZE"]][$row["BRAND_SUPPLIER"]][str_replace(", [BS]","",$row["ITEM_DESCRIPTION"])]["CONS"];
	
	$booking_amt=$booking_data[$row["BOOKING_NO"]][$row["ITEM_GROUP_ID"]][$row["GMTS_COLOR_ID"]][$row["GMTS_SIZE_ID"]][$row["ITEM_COLOR"]][$row["ITEM_SIZE"]][$row["BRAND_SUPPLIER"]][str_replace(", [BS]","",$row["ITEM_DESCRIPTION"])]["AMOUNT"];
	$booking_rate=$booking_amt/$booking_qnty;
	if($booking_rate && $booking_rate!=$row["RATE"])
	{
		
		$ord_amount=$row["RECEIVE_QNTY"]*$booking_rate;
		if($row["RATE"]==$row["CONS_RATE"])
		{
			$cons_rate=$row["CONS_RATE"];
			$cons_amount=$row["CONS_RATE"]*$row['CONS_QNTY'];
		}
		else
		{
			$cons_rate=(($booking_rate/$trim_group_arr[$row['ITEM_GROUP_ID']]['conversion_factor'])*$row['EXCHANGE_RATE']);
			$cons_amount=$cons_rate*$row['CONS_QNTY'];
		}
		
		//echo $row["BOOKING_NO"]."=".$row["ITEM_GROUP_ID"]."=".$row["GMTS_COLOR_ID"]."=".$row["GMTS_SIZE_ID"]."=".$row["ITEM_COLOR"]."=".$row["ITEM_SIZE"]."=".$row["BRAND_SUPPLIER"]."=".str_replace(", [BS]","",$row["ITEM_DESCRIPTION"])."=".$booking_rate."=".$row["RATE"]."=".$row["CONS_RATE"]."=".$cons_rate."=".$booking_amt."=".$booking_qnty;die;
		
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
		
		//$propo_rid=execute_query("update ORDER_WISE_PRO_DETAILS set ORDER_RATE='$booking_rate', ORDER_AMOUNT=QUANTITY*$booking_rate where TRANS_ID=$trans_id");
		if($propo_rid==false)
		{
			//echo "update ORDER_WISE_PRO_DETAILS set ORDER_RATE='$booking_rate', ORDER_AMOUNT=QUANTITY*$booking_rate where TRANS_ID=$trans_id";oci_rollback($con);disconnec($con);die;
		}
		
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
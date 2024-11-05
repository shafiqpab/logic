<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


/*$receive_id_sql =  sql_select("select a.location_id, c.knitting_location_id,c.recv_number, c.id from pro_grey_prod_delivery_mst a, pro_roll_details b , inv_receive_master c,  pro_roll_details d where a.id = b.mst_id and b.entry_form =56 and b.status_active =1 and c.id = d.mst_id and d.entry_form =58 and c.knitting_source=1 and d.status_active =1 and b.barcode_no = d.barcode_no and a.location_id != c.knitting_location_id group by a.location_id, c.knitting_location_id, c.recv_number, c.id");
*/

$receive_id_sql =  sql_select("select x.po_breakdown_id, x.dtls_id, x.roll_qnty,x.roll_reject , x.quantity, x.returnable_qnty
from (
    select a.po_breakdown_id,sum(a.qnty) as roll_qnty, sum(a.reject_qnty) as roll_reject, b.quantity, b.returnable_qnty, b.dtls_id
    from pro_roll_details a, order_wise_pro_details b
    where a.dtls_id = b.dtls_id and b.entry_form=2 and a.entry_form=2
    and b.trans_id=0
    group by a.po_breakdown_id, b.quantity, b.returnable_qnty, b.dtls_id
    having sum(a.reject_qnty) >0

) x
where (round (x.roll_qnty,2)  ) != round (x.quantity,2) "); 


if(empty($receive_id_sql))
{
	echo "Data Not Found";
	die;
}

foreach ($receive_id_sql as $val) 
{


	$dtls_po_id_arr[$val[csf("dtls_id")]][$val[csf("po_breakdown_id")]]['roll_qnty']+=$val[csf("roll_qnty")];
	$dtls_po_id_arr[$val[csf("dtls_id")]][$val[csf("po_breakdown_id")]]['roll_reject']+=$val[csf("roll_reject")];

	//execute_query("update inv_receive_master set knitting_location_id='".$val[csf("location_id")]."',updated_by=999 where id=".$val[csf("id")],0);
	//echo "update inv_receive_master set knitting_location_id='".$val[csf("location_id")]."',updated_by=999 where id=".$val[csf("id")]."<br>";
}

$dtlsIDChk=array();
foreach ($dtls_po_id_arr as $dtlsID => $dtlsData) 
{
	foreach ($dtlsData as $poid => $row) 
	{
		
		//execute_query("update order_wise_pro_details set quantity='".$row["roll_qnty"]."', returnable_qnty='".$row["roll_reject"]."' ,updated_by=999 where entry_form=2 and po_breakdown_id=$poid and dtls_id=".$dtlsID,0);
		echo "update order_wise_pro_details set quantity='".$row["roll_qnty"]."', returnable_qnty='".$row["roll_reject"]."' ,updated_by=999 where entry_form=2 and po_breakdown_id=$poid and dtls_id=".$dtlsID."<br>";	
		
	}

}

/*oci_commit($con); 
echo "Success";
disconnect($con);
die;*/
 
?>
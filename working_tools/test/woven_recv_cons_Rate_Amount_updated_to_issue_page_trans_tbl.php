<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
$con=connect();

//This script Only for Rate 


 $recv_sql=sql_select("select a.prod_id,a.batch_id,a.cons_rate from inv_receive_master x,pro_finish_fabric_rcv_dtls y, inv_transaction a where x.id=y.mst_id and y.trans_id=a.id and x.entry_form=17 and a.item_category=3 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id  IN (135475, 135476, 135477, 135477, 135478, 135479, 135480, 135856, 135857, 135857, 135858, 135859, 136580, 136580, 136914, 136915, 137916, 137916, 137917, 137917, 137918, 137918, 137919, 137919, 137920, 137920, 145058, 145058, 145058, 147507, 147645, 147645, 147645, 147645, 147645, 147645, 147645, 147645, 147645, 147645, 147645, 147648, 147648, 147648, 147648, 147650, 148402, 148402, 148402, 148402, 148403, 148403, 148403, 150496, 150496, 150496, 150500, 150500, 155089, 158395, 158396, 158396, 158396, 158396, 158397, 158397, 158398, 158398, 158399, 158399, 158399, 158403, 158404, 158417, 158417, 158418, 158419, 158419, 158419, 158458, 161079, 161079, 161082, 161082, 161083, 161083, 161084, 161084, 161493, 163783, 163783, 163783, 164259, 164259, 164259, 164260, 164260, 164301, 165251, 165254, 165282, 165286, 165287, 165287, 165287, 165826, 167689)
 group by a.prod_id,a.batch_id,a.cons_rate order by  a.prod_id,a.batch_id");


 $issue_sql=sql_select("select a.prod_id,a.batch_id,a.cons_quantity
 from inv_issue_master x,inv_wvn_finish_fab_iss_dtls y, inv_transaction a where x.id=y.mst_id and y.trans_id=a.id and x.entry_form=19 and a.item_category=3 and a.transaction_type=2 and a.status_active=1 and a.is_deleted=0  and a.cons_rate is null and a.prod_id  IN (135475, 135476, 135477, 135477, 135478, 135479, 135480, 135856, 135857, 135857, 135858, 135859, 136580, 136580, 136914, 136915, 137916, 137916, 137917, 137917, 137918, 137918, 137919, 137919, 137920, 137920, 145058, 145058, 145058, 147507, 147645, 147645, 147645, 147645, 147645, 147645, 147645, 147645, 147645, 147645, 147645, 147648, 147648, 147648, 147648, 147650, 148402, 148402, 148402, 148402, 148403, 148403, 148403, 150496, 150496, 150496, 150500, 150500, 155089, 158395, 158396, 158396, 158396, 158396, 158397, 158397, 158398, 158398, 158399, 158399, 158399, 158403, 158404, 158417, 158417, 158418, 158419, 158419, 158419, 158458, 161079, 161079, 161082, 161082, 161083, 161083, 161084, 161084, 161493, 163783, 163783, 163783, 164259, 164259, 164259, 164260, 164260, 164301, 165251, 165254, 165282, 165286, 165287, 165287, 165287, 165826, 167689)
 group by a.prod_id,a.batch_id,a.cons_quantity order by  a.prod_id,a.batch_id");




if(empty($issue_sql))
{
	echo "Issue Data Not Found";
	die;
}
foreach($issue_sql as $vals)
{
	$issue_arr[$vals[csf("prod_id")]][$vals[csf("batch_id")]]+= $vals[csf("cons_quantity")];

}

if(empty($recv_sql))
{
	echo "Recv Data Not Found";
	die;
}

foreach($recv_sql as $val)
{
	$recv_arr[$val[csf("prod_id")]][$val[csf("batch_id")]] = $val[csf("cons_rate")];

}
foreach ($recv_arr as $prod_id => $prod_data) 
{
	foreach ($prod_data as $batch_id => $row) 
	{

		// $issueAmount= $issue_arr[$prod_id][$batch_id]*$recv_arr[$prod_id][$batch_id]; // issue amount not valid


		echo "update inv_transaction set cons_rate = $row where prod_id=".$prod_id." and batch_id =".$batch_id." and  transaction_type=2 and status_active=1 and is_deleted=0 and item_category=3 <br />";
		//execute_query("update inv_transaction set cons_rate = $row where prod_id=".$prod_id." and batch_id =".$batch_id." and  transaction_type=2 and status_active=1 and is_deleted=0 and item_category=3");

		

	}
}
	
	//b.floor, b.room, b.rack_no, b.shelf_no,

	//echo "update pro_finish_fabric_rcv_dtls set floor = $floor_id, room = $room, rack_no = $rack, shelf_no = $self where id=".$val[csf("id")]." <br />";
	//execute_query("update pro_finish_fabric_rcv_dtls set floor = $floor_id, room = $room, rack_no = $rack, shelf_no = $self where id=".$val[csf("id")],0);
	

//135477	13949  183   185.13 = 33878.79
//135477	18717  200   122.85 = 24570 

//oci_commit($con);
//echo "Success"; 
die;




?>
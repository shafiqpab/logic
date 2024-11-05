<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
$con=connect();

//d.store_id = 2, floor_id = 1856, room = 1857, rack = 1858, self =1859

$sql_data = sql_select("SELECT c.id as mst_id, b.id as dtls_id, d.id as trans_id,  a.entry_form, a.barcode_no, 
d.store_id, d.floor_id, d.room, d.rack, d.self

from pro_roll_details a, pro_grey_prod_entry_dtls b, inv_receive_master c, inv_transaction d
where a.entry_form in (58,84,22) and a.barcode_no < 20020000001 and b.trans_id!=0 and a.dtls_id=b.id 
and c.entry_form in (58,84,22) and b.mst_id=c.id and d.item_category=13 and b.trans_id=d.id and d.transaction_type in (1,4)
and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 ");


foreach ($sql_data as $row)
{
	echo "UPDATE inv_receive_master set store_id = 2 where id = ".$row[csf("mst_id")]."<br>";
	//$rid3=execute_query("UPDATE inv_receive_master set store_id = 2 where id = ".$row[csf("mst_id")],0);


	echo "UPDATE inv_transaction set store_id = 2, floor_id = 1856, room = 1857, rack = 1858, self = 1859 where id = ".$row[csf("trans_id")]."<br>";
	//$rid4=execute_query("UPDATE inv_transaction set store_id = 2, floor_id = 1856, room = 1857, rack = 1858, self = 1859 where id = ".$row[csf("trans_id")],0);

	echo "UPDATE pro_grey_prod_entry_dtls set floor_id = 1856, room = 1857, rack = 1858, self = 1859 where id = ".$row[csf("dtls_id")]."<br>";
	//$rid44=execute_query("UPDATE pro_grey_prod_entry_dtls set floor_id = 1856, room = 1857, rack = 1858, self = 1859 where id = ".$row[csf("dtls_id")],0);
}


$issue_data=sql_select("SELECT  a.entry_form, a.barcode_no, b.id as dtls_id, d.id as trans_id
from pro_roll_details a, inv_grey_fabric_issue_dtls b, inv_issue_master c, inv_transaction d
where a.dtls_id=b.id and b.mst_id=c.id and b.trans_id=d.id and a.entry_form in (61) 
and c.entry_form in (61) and a.barcode_no < 20020000001 and d.transaction_type in (2)
and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.item_category=13");
foreach ($issue_data as $row)
{
	echo "UPDATE inv_transaction set store_id = 2, floor_id = 1856, room = 1857, rack = 1858, self = 1859 where id = ".$row[csf("trans_id")]."<br>";
	//$rid4=execute_query("UPDATE inv_transaction set store_id = 2, floor_id = 1856, room = 1857, rack = 1858, self = 1859 where id = ".$row[csf("trans_id")],0);

	echo "UPDATE inv_grey_fabric_issue_dtls set floor_id = 1856, room = 1857, rack = 1858, self = 1859 where id = ".$row[csf("dtls_id")]."<br>";
	//$rid44=execute_query("UPDATE inv_grey_fabric_issue_dtls set floor_id = 1856, room = 1857, rack = 1858, self = 1859 where id = ".$row[csf("dtls_id")],0);
}


$transfer_data=sql_select("SELECT b.id, a.barcode_no, c.id as trans_id, b.id as dtls_id
from pro_roll_details a, inv_item_transfer_dtls b, inv_transaction c
where a.barcode_no < 20020000001 and a.dtls_id=b.id and b.trans_id=c.id and a.entry_form in (83, 82, 110, 183, 180) and c.item_category=13 
and c.transaction_type in (5,6)
and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ");
foreach ($transfer_data as $row)
{
	echo "UPDATE inv_transaction set store_id = 2, floor_id = 1856, room = 1857, rack = 1858, self = 1859 where id = ".$row[csf("trans_id")]."<br>";
	//$rid4=execute_query("UPDATE inv_transaction set store_id = 2, floor_id = 1856, room = 1857, rack = 1858, self = 1859 where id = ".$row[csf("trans_id")],0);

	echo "UPDATE inv_item_transfer_dtls set from_store = 2, floor_id = 1856, room = 1857, rack = 1858, shelf = 1859, to_store = 2, to_floor_id = 1856, to_room = 1857, to_rack = 1858, to_shelf = 1859 where id = ".$row[csf("dtls_id")]." and trans_id = ".$row[csf("trans_id")]."<br>";
	//$rid44=execute_query("UPDATE inv_item_transfer_dtls set from_store = 2, floor_id = 1856, room = 1857, rack = 1858, shelf = 1859, to_store = 2, to_floor_id = 1856, to_room = 1857, to_rack = 1858, to_shelf = 1859 where id = ".$row[csf("dtls_id")]." and trans_id = ".$row[csf("trans_id")],0);

}


/* oci_commit($con);
echo "Success";
die; */
?>
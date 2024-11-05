<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$sql= "select B.ID, B.MST_ID, B.FROM_PROD_ID, B.TO_PROD_ID, B.FROM_STORE, B.FLOOR_ID, B.ROOM, B.RACK, B.SHELF, B.BIN_BOX, B.TO_STORE, B.TO_FLOOR_ID, B.TO_ROOM, B.TO_RACK, B.TO_SHELF, B.TO_BIN_BOX, B.ITEM_CATEGORY, B.ITEM_GROUP, B.TRANSFER_QNTY, B.RATE, B.TRANSFER_VALUE, B.UOM, B.REMARKS, B.TRANS_ID, B.TO_TRANS_ID, B.INSERTED_BY, B.INSERT_DATE
 from INV_ITEM_TRANSFER_MST a, INV_ITEM_TRANSFER_DTLS b
where a.id=b.mst_id and a.entry_form =78 and a.PURPOSE=3 and a.status_active=1 and b.status_active=1
and b.id not in(select b.DTLS_ID from INV_ITEM_TRANSFER_DTLS_AC b where b.status_active=1)";
$sql_result = sql_select($sql);
$field_array_dtls_ac="id, mst_id, dtls_id, is_acknowledge, from_prod_id, to_prod_id, from_store, floor_id, room, rack, shelf, bin_box, to_store, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, item_category, item_group, transfer_qnty, rate, transfer_value, uom, remarks, inserted_by, insert_date";
foreach ($sql_result as $val) 
{
	$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
	
	$data_array_dtls_ac="(".$id_dtls_ac.",".$val["MST_ID"].",".$val["ID"].",0,'".$val["FROM_PROD_ID"]."','".$val["TO_PROD_ID"]."','".$val["FROM_STORE"]."','".$val["FLOOR_ID"]."','".$val["ROOM"]."','".$val["RACK"]."','".$val["SHELF"]."','".$val["BIN_BOX"]."','".$val["TO_STORE"]."','".$val["TO_FLOOR_ID"]."','".$val["TO_ROOM"]."','".$val["TO_RACK"]."','".$val["TO_SHELF"]."','".$val["TO_BIN_BOX"]."','".$val["ITEM_CATEGORY"]."','".$val["ITEM_GROUP"]."','".$val["TRANSFER_QNTY"]."','".$val["RATE"]."','".$val["TRANSFER_VALUE"]."','".$val["UOM"]."','".$val["REMARKS"]."','".$val["INSERTED_BY"]."','".$val["INSERT_DATE"]."')";
	
	
	$rID=execute_query("insert into inv_item_transfer_dtls_ac (".$field_array_dtls_ac.") values ". $data_array_dtls_ac."");
	if($rID==false)
	{
		echo "insert into inv_item_transfer_dtls_ac (".$field_array_dtls_ac.") values ". $data_array_dtls_ac."";oci_rollback($con);disconnect($con);die;
	}
}

if ($rID) 
{
	oci_commit($con);
	echo "Success";
} else {
	oci_rollback($con);
	echo "Failed";
}
disconnect($con);
die;


?>
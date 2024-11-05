<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


function sql_insert2( $strTable, $arrNames, $arrValues, $commit, $contain_lob )
{
	global $con ;
	//echo $con;die;
	if($contain_lob=="") $contain_lob=0;
	if( $contain_lob==0)
	{
		$tmpv=explode(")",$arrValues);
		if(count($tmpv)>2)
			$strQuery= "INSERT ALL \n";
		else
			$strQuery= "INSERT  \n";

		for($i=0; $i<count($tmpv)-1; $i++)
		{
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1);
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
		}

		if(count($tmpv)>2) $strQuery .= "SELECT * FROM dual";

	}
	else
	{
		$tmpv=explode(")",$arrValues);

		for($i=0; $i<count($tmpv)-1; $i++)
		{
			$strQuery="";
			$strQuery= "INSERT  \n";
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1);
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
			$stid =  oci_parse($con, $strQuery);
			$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
			if (!$exestd) return "0";
		}
		return "1";

	}

	//echo $strQuery;die;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);

	if ($exestd)
		return "1";
	else
		return "0";

	die;

}

$acknowledged_transfer_sql = "select a.id,a.transfer_system_id,d.id dtls_id,d.from_prod_id,d.to_prod_id,d.color_id,d.from_store,d.to_store,d.transfer_qnty,d.batch_id,d.to_batch_id,d.trans_id,d.to_trans_id,d.from_order_id,d.to_order_id,
d.body_part_id,d.fabric_shade,d.to_body_part,d.floor_id,d.rack,d.room,d.shelf,d.to_floor_id,d.to_room,d.to_rack,d.to_shelf,c.item_category,d.remarks, d.no_of_roll, d.to_ord_book_id, d.to_ord_book_no
from inv_item_transfer_mst a,inv_transaction c, inv_item_transfer_dtls d
where a.entry_form=14 and a.id=c.mst_id and c.id=d.to_trans_id
and a.status_active=1 and c.status_active=1 and d.status_active=1 and c.transaction_type=5
and a.is_acknowledge=1
order by d.trans_id desc";
$acknowledged_transfer_data = sql_select($acknowledged_transfer_sql);
if(!empty($acknowledged_transfer_data))
{
	$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, batch_id, yarn_lot, brand_id, color_id, item_group, from_store, floor_id, room, rack, shelf, to_store, to_floor_id, to_room, to_rack, to_shelf, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id, trans_id, to_trans_id,remarks, fabric_shade, to_batch_id, body_part_id, to_body_part, no_of_roll, to_ord_book_id, to_ord_book_no";

	$field_array_dtls_ac="id, mst_id, dtls_id, is_acknowledge, from_prod_id, to_prod_id, batch_id, yarn_lot, brand_id, color_id, item_group, from_store, floor_id, room, rack, shelf, to_store, to_floor_id, to_room, to_rack, to_shelf, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id, fabric_shade, to_batch_id, body_part_id, to_body_part, no_of_roll, to_ord_book_id, to_ord_book_no, trans_id, to_trans_id";

	foreach($acknowledged_transfer_data  as $val)
	{
		$challan_id 				= $val[csf("id")];
		$dtls_id 					= $val[csf("dtls_id")];
		$color_id 					= $val[csf("color_id")];
		$item_category 				= $val[csf("item_category")];
		$body_part_id 				= $val[csf("body_part_id")];
		$fabric_shade 				= $val[csf("fabric_shade")];
		$from_order_id 				= $val[csf("from_order_id")];
		$to_order_id 				= $val[csf("to_order_id")];
		$from_prod_id 				= $val[csf("from_prod_id")];
		$to_prod_id 				= $val[csf("to_prod_id")];
		$from_store 				= $val[csf("from_store")];
		$to_store 	 				= $val[csf("to_store")];
		$rate_in_usd 				= $val[csf("rate_in_usd")];
		$remarks 					= $val[csf("remarks")];
		$from_floor_id 				= $val[csf("floor_id")];
		$from_room 					= $val[csf("room")];
		$from_rack 					= $val[csf("rack")];
		$from_shelf 				= $val[csf("shelf")];
		$to_floor_id 				= $val[csf("to_floor_id")];
		$to_room 					= $val[csf("to_room")];
		$to_rack 					= $val[csf("to_rack")];
		$to_shelf 					= $val[csf("to_shelf")];
		$trans_id 					= $val[csf("trans_id")];
		$to_trans_id 				= $val[csf("to_trans_id")];
		$transfer_qnty 				= $val[csf("transfer_qnty")];
		$transfer_value 			= $val[csf("transfer_value")];
		$transfer_value_in_usd 		= $val[csf("transfer_value_in_usd")];
		$uom 						= $val[csf("uom")];
		$to_body_part 				= $val[csf("to_body_part")];
		$roll 						= $val[csf("no_of_roll")];
		$to_ord_book_id 			= $val[csf("to_ord_book_id")];
		$to_ord_book_no 			= $val[csf("to_ord_book_no")];
		$batch_id 					= $val[csf("batch_id")];
		$to_batch_id 		 		= $val[csf("to_batch_id")];

		if ($data_array_dtls != "") $data_array_dtls .= ",";
		$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
		$data_array_dtls = "(" . $id_dtls.",'".$challan_id."','".$from_prod_id."','".$to_prod_id."','".$batch_id."',0,0,'".$color_id."',0,'".$from_store."','".$from_floor_id."','".$from_room."','".$from_rack."','".$from_shelf."','".$to_store."','".$to_floor_id."','".$to_room."','".$to_rack."','".$to_shelf."','".$item_category."','".$transfer_qnty."','".$rate_in_usd."','".$transfer_value."','".$uom."',999,'".$pc_date_time."','".$from_order_id."','".$to_order_id."','".$trans_id."','".$to_trans_id."','".$remarks."','".$fabric_shade."','".$to_batch_id."','".$body_part_id."','".$to_body_part."','".$roll."','".$to_ord_book_id."','".$to_ord_book_no."')";
		$rID = sql_insert2("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,1);
		if($rID){
			oci_commit($con);
			echo "$rID Success $id_dtls";
		}else{
			oci_rollback($con);
			echo "$rID failed $id_dtls";
		}

		$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
		if ($data_array_dtls_ac != "") $data_array_dtls_ac .= ",";
		$data_array_dtls_ac = "(".$id_dtls_ac.",".$challan_id.",".$id_dtls.",1,'".$from_prod_id."','".$to_prod_id."','".$batch_id."',0,0,'".$color_id."',0,'".$from_store."','".$from_floor_id."','".$from_room."','".$from_rack."','".$from_shelf."','".$to_store."','".$to_floor_id."','".$to_room."','".$to_rack."','".$to_shelf."','".$item_category."','".$transfer_qnty."','".$rate_in_usd."','".$transfer_value."','".$uom."',999,'".$pc_date_time."','".$from_order_id."','".$to_order_id."','".$fabric_shade."','".$to_batch_id."','".$body_part_id."','".$to_body_part."','".$roll."','".$to_ord_book_id."','".$to_ord_book_no."','".$trans_id."','".$to_trans_id."')";
		$rID2 = sql_insert2("inv_item_transfer_dtls_ac",$field_array_dtls_ac,$data_array_dtls_ac,1);
		if($rID2){
			//oci_commit($con);
			echo "$rID2 Success_ac $id_dtls_ac";
		}else{
			//oci_rollback($con);
			echo "$rID2 failed_ac $id_dtls_ac";
		}
		//echo "10**insert into inv_item_transfer_dtls_ac (".$field_array_dtls_ac.") values ".$data_array_dtls_ac . "br /";die;

		//execute_query("update inv_item_transfer_dtls set active_dtls_id_in_transfer=1,to_trans_id=0,to_store=0,to_floor_id=0,to_room=0,to_rack=0,to_shelf=0,updated_by=999 where id=$dtls_id",0);
		//execute_query("update inv_item_transfer_dtls_ac set is_acknowledge=1,trans_id=$trans_id,to_trans_id=$to_trans_id,to_store=0,to_floor_id=0,to_room=0,to_rack=0,to_shelf=0,updated_by=999 where dtls_id=$dtls_id",0);
		//echo "update inv_item_transfer_dtls set active_dtls_id_in_transfer=1,to_trans_id=0,to_store=0,to_floor_id=0,to_room=0,to_rack=0,to_shelf=0 where id=$dtls_id <br />";
		//echo "update inv_item_transfer_dtls_ac set is_acknowledge=1,trans_id=$trans_id,to_trans_id=$to_trans_id where dtls_id=$dtls_id <br />";
	}
}


//echo $rID = sql_insert2("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,1);
//echo "10**insert into inv_item_transfer_dtls_ac (".$field_array_dtls_ac.") values ".$data_array_dtls_ac . "<br />";die;
//$rID2 = sql_insert2("inv_item_transfer_dtls_ac",$field_array_dtls_ac,$data_array_dtls_ac,1);

//echo $rID ."&&". $rID2;
oci_commit($con);
/*if($rID && $rID2){
	//oci_commit($con);
	echo "Success";
}else{
	//oci_rollback($con);
	echo "failed";
}*/

die;
?>
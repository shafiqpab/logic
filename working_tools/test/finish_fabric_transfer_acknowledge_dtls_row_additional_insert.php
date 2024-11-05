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

$acknowledged_transfer_sql = "select  a.dtls_id,b.id as trans_id, b.mst_id, b.pi_wo_batch_no, b.company_id, b.prod_id, b.item_category, b.store_id,b.cons_uom, b.cons_quantity, b.cons_amount, b.no_of_roll, b.body_part_id,b.room,b.rack, b.self,b.floor_id, b.fabric_shade, a.po_breakdown_id, d.color, b.order_rate,b.order_amount, null as remarks
	from order_wise_pro_details a, inv_transaction b, inv_item_transfer_mst c, product_details_master d
	where a.trans_id = b.id and b.mst_id = c.id and b.prod_id = d.id and c.entry_form = 14 and a.entry_form = 14 and b.transaction_type = 5 and a.trans_type =5 and c.transfer_criteria=2 and b.item_category = 2 and b.status_active =1 and b.is_deleted =0 and c.transfer_system_id in ('CCL-FFTE-19-01954','CCL-FFTE-19-01959','CCL-FFTE-19-01948','CCL-FFTE-19-01951','CCL-FFTE-19-01962','CCL-FFTE-19-01966',
'CCL-FFTE-19-01964','CCL-FFTE-19-01958','CCL-FFTE-19-01953','CCL-FFTE-19-01950','CCL-FFTE-19-01961','CCL-FFTE-19-01955','CCL-FFTE-19-01957','CCL-FFTE-19-01952','CCL-FFTE-19-01960',
'CCL-FFTE-19-01949','CCL-FFTE-19-01963','CCL-FFTE-19-01976') and C.TRANSFER_SYSTEM_ID = 'CCL-FFTE-19-01976'";

$acknowledged_transfer_data = sql_select($acknowledged_transfer_sql);

foreach($acknowledged_transfer_data  as $val)
{
	$all_dtls_id .= $val[csf("dtls_id")].",";
}
$all_dtls_id = chop($all_dtls_id,",");

$chk_already_exists = sql_select("select id from inv_item_transfer_dtls where id in (".chop($all_dtls_id).")");
foreach ($chk_already_exists as $val) 
{
	$chk_already_exists_dtls_arr[$val[csf("id")]] = $val[csf("id")];
}

$chk_already_exists = sql_select("select dtls_id from inv_item_transfer_dtls_ac where dtls_id in (".chop($all_dtls_id).")");
foreach ($chk_already_exists as $val) 
{
	$chk_already_exists_acknow_dtls_arr[$val[csf("dtls_id")]] = $val[csf("dtls_id")];
}


if(!empty($acknowledged_transfer_data))
{
	$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, batch_id, yarn_lot, brand_id, color_id, item_group, from_store, floor_id, room, rack, shelf, to_store, to_floor_id, to_room, to_rack, to_shelf, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id, trans_id, to_trans_id,remarks, fabric_shade, to_batch_id, body_part_id, to_body_part, no_of_roll, to_ord_book_id, to_ord_book_no, active_dtls_id_in_transfer";

	$field_array_dtls_ac="id, mst_id, dtls_id, is_acknowledge, from_prod_id, to_prod_id, batch_id, yarn_lot, brand_id, color_id, item_group, from_store, floor_id, room, rack, shelf, to_store, to_floor_id, to_room, to_rack, to_shelf, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id, fabric_shade, to_batch_id, body_part_id, to_body_part, no_of_roll, to_ord_book_id, to_ord_book_no, trans_id, to_trans_id";

	foreach($acknowledged_transfer_data  as $val)
	{
		$challan_id 				= $val[csf("mst_id")];
		$dtls_id 					= $val[csf("dtls_id")];
		$color_id 					= $val[csf("color")];
		$item_category 				= $val[csf("item_category")];
		$body_part_id 				= $val[csf("body_part_id")];
		$fabric_shade 				= $val[csf("fabric_shade")];
		$from_order_id 				= $val[csf("po_breakdown_id")];
		$to_order_id 				= $val[csf("po_breakdown_id")];
		$from_prod_id 				= $val[csf("prod_id")];
		$to_prod_id 				= $val[csf("prod_id")];
		$from_store 				= 4;
		$to_store 	 				= $val[csf("store_id")];
		$rate_in_usd 				= $val[csf("order_rate")];
		$remarks 					= $val[csf("remarks")];
		$from_floor_id 				= 0;
		$from_room 					= 0;
		$from_rack 					= 0;
		$from_shelf 				= 0;
		$to_floor_id 				= $val[csf("floor_id")];
		$to_room 					= $val[csf("room")];
		$to_rack 					= $val[csf("rack")];
		$to_shelf 					= $val[csf("self")];
		$trans_id 					= 0;
		$to_trans_id 				= $val[csf("trans_id")];
		$transfer_qnty 				= $val[csf("cons_quantity")];
		$transfer_value 			= $val[csf("cons_amount")];
		$transfer_value_in_usd 		= $val[csf("order_amount")];
		$uom 						= $val[csf("cons_uom")];
		$to_body_part 				= $val[csf("body_part_id")];
		$roll 						= $val[csf("no_of_roll")];
		$to_ord_book_id 			= 0;
		$to_ord_book_no 			= "";
		$batch_id 					= $val[csf("pi_wo_batch_no")];
		$to_batch_id 		 		= $val[csf("pi_wo_batch_no")];

		if ($data_array_dtls != "") $data_array_dtls .= ",";

		if($chk_already_exists_dtls_arr[$dtls_id] == "")
		{
			$data_array_dtls = "(" . $dtls_id.",'".$challan_id."','".$from_prod_id."','".$to_prod_id."','".$batch_id."',0,0,'".$color_id."',0,'".$from_store."','".$from_floor_id."','".$from_room."','".$from_rack."','".$from_shelf."','".$to_store."','".$to_floor_id."','".$to_room."','".$to_rack."','".$to_shelf."','".$item_category."','".$transfer_qnty."','".$rate_in_usd."','".$transfer_value."','".$uom."',666,'".$pc_date_time."','".$from_order_id."','".$to_order_id."','".$trans_id."','".$to_trans_id."','".$remarks."','".$fabric_shade."','".$to_batch_id."','".$body_part_id."','".$to_body_part."','".$roll."','".$to_ord_book_id."','".$to_ord_book_no."',0)";

			echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls . ";<br />";
			//$rID = sql_insert2("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,1);
		}
		/*if($rID){
			oci_commit($con);
			echo "$rID Success $dtls_id";
		}else{
			oci_rollback($con);
			echo "$rID failed $dtls_id";
		}*/

		if($chk_already_exists_acknow_dtls_arr[$dtls_id] == "")
		{
			$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
			if ($data_array_dtls_ac != "") $data_array_dtls_ac .= ",";
			$data_array_dtls_ac = "(".$id_dtls_ac.",".$challan_id.",".$dtls_id.",1,'".$from_prod_id."','".$to_prod_id."','".$batch_id."',0,0,'".$color_id."',0,'".$from_store."','".$from_floor_id."','".$from_room."','".$from_rack."','".$from_shelf."','".$to_store."','".$to_floor_id."','".$to_room."','".$to_rack."','".$to_shelf."','".$item_category."','".$transfer_qnty."','".$rate_in_usd."','".$transfer_value."','".$uom."',666,'".$pc_date_time."','".$from_order_id."','".$to_order_id."','".$fabric_shade."','".$to_batch_id."','".$body_part_id."','".$to_body_part."','".$roll."','".$to_ord_book_id."','".$to_ord_book_no."','".$trans_id."','".$to_trans_id."')";

			echo "insert into inv_item_transfer_dtls_ac (".$field_array_dtls_ac.") values ".$data_array_dtls_ac . ";<br />";

			//$rID2 = sql_insert2("inv_item_transfer_dtls_ac",$field_array_dtls_ac,$data_array_dtls_ac,1);
		}



		/*if($rID2){
			oci_commit($con);
			echo "$rID2 Success_ac $id_dtls_ac";
		}else{
			oci_rollback($con);
			echo "$rID2 failed_ac $id_dtls_ac";
		}*/
		//echo "10**insert into inv_item_transfer_dtls_ac (".$field_array_dtls_ac.") values ".$data_array_dtls_ac . "br /";die;
		//echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls . "<br />";

		//execute_query("update inv_item_transfer_dtls set active_dtls_id_in_transfer=1,to_trans_id=0,to_store=0,to_floor_id=0,to_room=0,to_rack=0,to_shelf=0,updated_by=999 where id=$dtls_id",0);
		//execute_query("update inv_item_transfer_dtls_ac set is_acknowledge=1,trans_id=$trans_id,to_trans_id=$to_trans_id,to_store=0,to_floor_id=0,to_room=0,to_rack=0,to_shelf=0,updated_by=999 where dtls_id=$dtls_id",0);
		//echo "update inv_item_transfer_dtls set active_dtls_id_in_transfer=1,to_trans_id=0,to_store=0,to_floor_id=0,to_room=0,to_rack=0,to_shelf=0 where id=$dtls_id <br />";
		//echo "update inv_item_transfer_dtls_ac set is_acknowledge=1,trans_id=$trans_id,to_trans_id=$to_trans_id where dtls_id=$dtls_id <br />";
	}
}


/*oci_commit($con);
die;*/
?>
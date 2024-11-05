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

$production_sql="select b.id,b.recv_number,b.receive_basis,b.receive_date,b.booking_id,b.entry_form,b.booking_without_order, b.company_id,b.item_category,b.store_id,
c.id dtls_id,c.prod_id,c.brand_id,c.uom,c.yarn_prod_id,c.reject_fabric_receive,c.floor_id,c.room,c.rack,c.self,
listagg(cast(d.po_breakdown_id as varchar(4000)),',')  within group (order by d.po_breakdown_id) as po_breakdown_id, sum(d.quantity) receive_qnty
from inv_receive_master b, pro_grey_prod_entry_dtls c, order_wise_pro_details d
where b.id=c.mst_id and c.id=d.dtls_id
and b.entry_form in(2) and d.entry_form in(2) and b.receive_basis=2 and b.status_active=1 and c.updated_by=999 and d.updated_by=999
and c.status_active=1 and d.status_active=1
group by b.id,b.recv_number,b.receive_basis,b.receive_date,b.booking_id,b.entry_form,b.booking_without_order, b.company_id,b.item_category,b.store_id,
c.id,c.prod_id,c.brand_id,c.uom,c.yarn_prod_id,c.reject_fabric_receive,c.floor_id,c.room,c.rack,c.self,c.reject_fabric_receive
order by b.id desc";//

$production_data=sql_select($production_sql);

$yarn_prod_data = sql_select("select id,avg_rate_per_unit from product_details_master where item_category_id=1 and status_active=1");
foreach ($yarn_prod_data as $yarn_row)
{
	$yarn_prod_id_arr[$yarn_row[csf("id")]] = $yarn_row[csf("avg_rate_per_unit")];
}

$field_array_trans = "id, mst_id, receive_basis, pi_wo_batch_no, booking_without_order, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, brand_id, order_uom, order_qnty, cons_uom, cons_quantity, floor_id, room, rack, self, inserted_by, insert_date";

foreach ($production_data as $row)
{
	$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);

	$grey_update_id 		= $row[csf("id")];
	$dtls_id 				= $row[csf("dtls_id")];
	$cbo_receive_basis 		= $row[csf("receive_basis")];
	$txt_booking_no_id 		= $row[csf("booking_id")];
	$booking_without_order 	= $row[csf("booking_without_order")];
	$cbo_company_id 		= $row[csf("company_id")];
	$prod_id 				= $row[csf("prod_id")];
	$txt_receive_date 		= $row[csf("receive_date")];
	$cbo_store_name 		= $row[csf("store_id")];
	$brand_id 				= $row[csf("brand_id")];
	$cbo_uom 				= $row[csf("uom")];
	$receive_qnty 			= $row[csf("receive_qnty")];
	$reject_qnty 			= $row[csf("reject_fabric_receive")];
	$cbo_floor_id 			= $row[csf("floor_id")];
	$txt_room 				= $row[csf("room")];
	$txt_rack 				= $row[csf("rack")];
	$txt_self 				= $row[csf("self")];

	/*$yarn_prod_id 			= explode(",",$row[csf("yarn_prod_id")]);
	$yarn_rate = 0;
	foreach ($yarn_prod_id as $yarn_info) {
		$yarn_rate += $yarn_prod_id_arr[$yarn_info];
	}

	$row_cond = "";
	$row_limit = "";
	if ($db_type == 0) {
		$txt_receive_date = change_date_format($txt_receive_date, 'yyyy-mm-dd', '-');
		$row_limit = " limit 1";
	} else {
		$txt_receive_date = change_date_format($txt_receive_date, 'yyyy-mm-dd', '-', 1);
		$row_cond = " and rownum=1";
	}

	$exchange_rate = sql_select("select conversion_rate,max(con_date) con_date from currency_conversion_rate where con_date<='$txt_receive_date' and currency=2  $row_cond group by conversion_rate,con_date order by con_date desc $row_limit");

	$exchange_rate  = $exchange_rate[0][csf('conversion_rate')];
	$cons_rate 		= $yarn_rate / $receive_qnty;
	$cons_amount 	= $cons_rate*$receive_qnty;*/

	//if ($data_array_trans != "") $data_array_trans .= ",";
	/*$data_array_trans = "(" . $id_trans . "," . $grey_update_id . ",'" . $cbo_receive_basis . "','" . $txt_booking_no_id . "','" . $booking_without_order . "','" . $cbo_company_id . "','" . $prod_id . "',13,1,'" . $txt_receive_date . "','" . $cbo_store_name . "','" . $brand_id . "','" . $cbo_uom . "','" . $receive_qnty . "','" . $cbo_uom . "','" . $receive_qnty . "','" . $cbo_floor_id . "','" . $txt_room . "','" . $txt_rack . "','" . $txt_self . "',999,'" . $pc_date_time . "')";

	//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans."<br />";
	$rID = sql_insert2("inv_transaction", $field_array_trans, $data_array_trans, 1);
	//echo "<br />update pro_grey_prod_entry_dtls set trans_id=$id_trans,updated_by=999 where id=$dtls_id";
	//echo "<br />update order_wise_pro_details set trans_id=$id_trans,updated_by=999 where dtls_id=$dtls_id";
	execute_query("update pro_grey_prod_entry_dtls set trans_id=$id_trans,updated_by=999 where id=$dtls_id",0);
	execute_query("update order_wise_pro_details set trans_id=$id_trans,updated_by=999 where dtls_id=$dtls_id",0);*/

	/*if($rID==1){
		oci_commit($con);
		echo "$id_trans Success <br />";
	}else{
		oci_rollback($con);
		echo "$id_trans failed <br />";
	}*/

	$product_arr[$prod_id] += $receive_qnty;

}

foreach ($product_arr as $prod_id => $prod_row) {
	//echo $prod_id ."==". $prod_row . "<br />";
	//echo "update product_details_master set current_stock=(current_stock+$prod_row),updated_by=999 where id=$prod_id; <br />";
	//execute_query("update product_details_master set current_stock=(current_stock+$prod_row),updated_by=999 where id=$prod_id",0);
}
//oci_commit($con);
echo "success";
?>
<?

header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$field_array_trans = "id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, cons_quantity, brand_id, store_id, floor_id, room, rack, self, inserted_by, insert_date";

$field_array_prop="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";

$trans_data = sql_select("select a.id, a.transfer_date, b.id as dtls_id, b.knit_program_id, a.company_id, b.from_order_id,  b.from_prod_id, b.item_category, a.transfer_date, b.transfer_qnty, b.brand_id, b.from_store, b.floor_id, b.room, b.rack, b.shelf as self, b.to_order_id, b.to_store, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and a.entry_form=82 and a.status_active=1 and b.status_active=1 and a.transfer_criteria=2 and b.trans_id =0 and b.to_trans_id=0 ");

if(empty($trans_data))
{
	echo "Mismatch Not Found";
	die;
}
else
{
	$i=1;
	foreach ($trans_data as $row) 
	{
		$id 				= $row[csf("id")];
		$dtls_id 			= $row[csf("dtls_id")];
		$recvBasis 			= "9";
		$progBookPiId 		= $row[csf("knit_program_id")];
		$company_id 		= $row[csf("company_id")];
		$productId 			= $row[csf("from_prod_id")];

		//$txt_transfer_date 	= change_date_format($row[csf("transfer_date")]);

		if($db_type==0) 
		{
			$txt_transfer_date = change_date_format($row[csf("transfer_date")],"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$txt_transfer_date = change_date_format($row[csf("transfer_date")],"","",1);
		}


		$rollWgt 			= $row[csf("transfer_qnty")];
		$brandId 			= $row[csf("brand_id")];
		$fromStoreId 		= $row[csf("from_store")];
		$fromFloor 			= $row[csf("floor_id")];
		$fromRoom 			= $row[csf("room")];
		$fromRack 			= $row[csf("rack")];
		$fromShelf 			= $row[csf("self")];
		$from_order_id 		= $row[csf("from_order_id")];

		$toStoreId 			= $row[csf("to_store")];
		$toFloor 			= $row[csf("to_floor_id")];
		$toRoom 			= $row[csf("to_room")];
		$toRack 			= $row[csf("to_rack")];
		$toShelf 			= $row[csf("to_shelf")];

		$cbo_company_id 	= $row[csf("company_id")];


		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$data_array_trans_out = "(".$transactionID.",".$id.",'".$recvBasis."','".$progBookPiId."',".$cbo_company_id.",'".$productId."',13,6,'".$txt_transfer_date."','".$rollWgt."','".$brandId."','".$fromStoreId."','".$fromFloor."','".$fromRoom."','".$fromRack."','".$fromShelf."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$data_array_prop_out ="(".$id_prop.",".$transactionID.",6,82,'".$dtls_id."','".$from_order_id."','".$productId."','".$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$from_trans_id=$transactionID;


		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$to_trans_id=$transactionID;

		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$data_array_trans_in = "(".$transactionID.",".$id.",'".$recvBasis."','".$progBookPiId."',".$cbo_company_id.",".$productId.",13,5,'".$txt_transfer_date."','".$rollWgt."','".$brandId."',".$toStoreId.",'".$toFloor."','".$toRoom."','".$toRack."','".$toShelf."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$data_array_prop_in ="(".$id_prop.",".$transactionID.",5,82,'".$dtls_id."','".$from_order_id."',".$productId.",'".$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		/*$dtlsId_arr[]=$dtls_id;
		$field_array_update_dtls="trans_id*to_trans_id*updated_by*update_date";
        $data_array_update_dtls[$dtls_id]=explode("*",("'".$from_trans_id."'*'".$to_trans_id."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));*/


        $rID = sql_insert("inv_transaction",$field_array_trans,$data_array_trans_out,0);
        $rID2 = sql_insert("inv_transaction",$field_array_trans,$data_array_trans_in,0);

        $rID3 = sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop_out,0);
        $rID4 = sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop_in,0);

        $rID5=execute_query("update inv_item_transfer_dtls set trans_id=".$from_trans_id.", to_trans_id=".$to_trans_id.", to_order_id=".$from_order_id."  where id = ".$dtls_id);


        if($rID && $rID2 && $rID3 && $rID4 && $rID5)
        {

        }else{
        	echo "$rID && $rID2 && $rID3 && $rID4 && $rID5\n dtls_id= $dtls_id";
        	oci_rollback($con);
        	die;
        }

        //$insertQry.="$i insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans_out."##<br>";
        //$i++;
	}
}

//echo $insertQry;
//die;

oci_commit($con);
echo "Success";
die;

?>
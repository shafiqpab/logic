<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($action=="load_drop_down_store")
{
	$data_ref=explode("_",$data);
	if($data_ref[1])
	{
		$location_cond = " and a.location_id=$data_ref[1]";
	}
	echo create_drop_down( "cbo_to_store", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id  and b.category_type=2 and a.status_active=1 and a.is_deleted=0 and company_id=$data_ref[0] $location_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"reset_room_rack_shelf(1,'cbo_to_store');fn_load_floor(this.value);");
}

if ($action=="load_drop_down_room")
{
	$ex_data = explode("_",$data);

	echo create_drop_down( "cboRoom_$ex_data[2]", 50, "select b.room_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.floor_id in($ex_data[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "-- Select --", "", "change_room(this.value,this.id)","","","","","","","","cboRoom[]","onchange_void");
	exit();
}

if ($action=="load_drop_down_rack")
{
	$ex_data = explode("_",$data);
	echo create_drop_down( "txtRack_$ex_data[2]", 50, "select b.rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and b.room_id in($ex_data[0]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "-- Select --", "", "change_rack(this.value,this.id)","","","","","","","","txtRack[]","onchange_void");
	exit();
}

if ($action=="load_drop_down_shelf")
{
	$ex_data = explode("_",$data);
	echo create_drop_down( "txtShelf_$ex_data[2]", 50, "select b.shelf_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and b.rack_id in($ex_data[0]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "-- Select --", "", "change_shelf(this.value,this.id)","","","","","","","","txtShelf[]","onchange_void");
	exit();
}

if ($action=="load_drop_down_bin")
{
	$ex_data = explode("_",$data);
	echo create_drop_down( "txtBin_$ex_data[2]", 50, "select b.bin_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and b.shelf_id in($ex_data[0]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "-- Select --", "", "","","","","","","","","txtBin[]","onchange_void");
	exit();
}

if($action=="floor_list")
{
	$data_ref=explode("__",$data);
	$floor_arr=array();
	$location_cond = "";
	if(!empty($data_ref[2])) $location_cond = "and b.location_id='$data_ref[2]'";
	$floor_data=sql_select("select b.floor_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($floor_data as $row)
	{
		$floor_arr[$row[csf('floor_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsFloor_arr= json_encode($floor_arr);
	echo $jsFloor_arr;
	die();
}

if($action=="room_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$room_arr=array();
	$location_cond = '';
	if(!empty($data_ref[1])) $location_cond = "and b.location_id='$data_ref[1]'";
	$room_data=sql_select("select b.room_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.floor_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	
	foreach($room_data as $row)
	{
		$room_arr[$row[csf('room_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsRoom_arr= json_encode($room_arr);
	echo $jsRoom_arr;
	
	die();
}

if($action=="rack_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$room_arr=array();
	$location_cond = "";
	if(!empty($data_ref[1])) $location_cond = "and b.location_id='$data_ref[1]'";
	$room_data=sql_select("select b.rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.room_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($room_data as $row)
	{
		$room_arr[$row[csf('rack_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsRoom_arr= json_encode($room_arr);
	echo $jsRoom_arr;
	die();
}

if($action=="shelf_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$room_arr=array();
	$location_cond = "";
	if(!empty($data_ref[1])) $location_cond = "and b.location_id='$data_ref[1]'";
	$room_data=sql_select("select b.shelf_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.rack_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($room_data as $row)
	{
		$room_arr[$row[csf('shelf_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsRoom_arr= json_encode($room_arr);
	echo $jsRoom_arr;
	die();
}

if($action=="bin_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$bin_arr=array();
	$location_cond = "";
	if(!empty($data_ref[1])) $location_cond = "and b.location_id='$data_ref[1]'";
	$bin_data=sql_select("select b.bin_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.shelf_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($bin_data as $row)
	{
		$bin_arr[$row[csf('bin_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsBin_arr= json_encode($bin_arr);
	echo $jsBin_arr;
	die();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
		
		//$new_receive_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'FRIR', date("Y",time()), 5, "select recv_number_prefix, recv_number_prefix_num from  inv_receive_master where company_id=$cbo_company_id and entry_form=126 and $year_cond=".date('Y',time())." order by id desc ", "recv_number_prefix", "recv_number_prefix_num" ));
		
		//$id=return_next_id( "id", "inv_receive_master", 1 ) ;
		
		$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
        //echo "10**";print_r($id); die;
        $new_receive_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'FRIR',126,date("Y",time()),2 ));

        //echo "10**";print_r($new_receive_system_id);die;

		$field_array="id, entry_form, item_category, recv_number_prefix, recv_number_prefix_num, recv_number, company_id, receive_date, issue_id, challan_no, store_id, inserted_by, insert_date";
		
		$data_array="(".$id.",126,2,'".$new_receive_system_id[1]."',".$new_receive_system_id[2].",'".$new_receive_system_id[0]."',".$cbo_company_id.",".$txt_issue_rtn_date.",".$txt_issue_id.",".$txt_challan_no.",".$cbo_to_store.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		
		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, brand_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, machine_id, floor_id, room, rack, self,  bin_box, inserted_by, insert_date";
		
		//, yarn_lot, yarn_count, brand_id , color_range_id, stitch_length
		//$dtls_id=return_next_id( "id", "pro_finish_fabric_rcv_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, trans_id, prod_id, fabric_description_id, gsm, width, order_id, receive_qnty, rate, amount, uom, machine_no_id, floor, room, rack_no, shelf_no, bin, color_id, body_part_id, batch_id, remarks, inserted_by, insert_date";
		
		
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, booking_without_order, entry_form, barcode_no, roll_id, roll_no, qnty, rate, amount,qc_pass_qnty, inserted_by, insert_date";
		$field_array_roll_update="roll_used*is_returned*updated_by*update_date";
		
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 ); 
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date,color_id";
		

		for($j=1;$j<=$tot_row;$j++)
		{
			/*$productId="productId_".$j;
			$rollWgt="rollWgt_".$j;
			$all_prod_id.=$$productId.",";
			$prodData_array[$$productId]+=$$rollWgt;*/
			
			
			$recvBasis="recvBasis_".$j;
			$barcodeNo="barcodeNo_".$j;
			$progBookPiId="progBookPiId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$BookWithoutOrd="BookWithoutOrd_".$j;
			$rollId="rollId_".$j;
			$rollWgt="rollWgt_".$j;
			$yarnLot="yarnLot_".$j;
			$yarnCount="yarnCount_".$j;
			$colorId="colorId_".$j;
			$stichLn="stichLn_".$j;
			$brandId="brandId_".$j;
			$batchId="batchId_".$j;
			

			$floorId="floorId_".$j;
			$roomId="roomId_".$j;
			$rack="rack_".$j;
			$shelf="shelf_".$j;
			$bin="bin_".$j;
			
			$dtlsId="dtlsId_".$j;
			$transId="transId_".$j;
			$rolltableId="rolltableId_".$j;
			
			$rollNo="rollNo_".$j;
			$febDescripId="febDescripId_".$j;
			$machineNoId="machineNoId_".$j;
			$gsm="gsm_".$j;
			$diaWidth="diaWidth_".$j;
			$rollRate="rollRate_".$j;
			$rollAmt="rollAmt_".$j;
			$colorRange="colorRange_".$j;
			$body_part="bodyPartData_".$j;
			$txtRemark="txtRemark_".$j;
			//echo "10** ".$$batchId;die;
			$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$dtls_id = return_next_id_by_sequence("PRO_FIN_FAB_RCV_DTLS_PK_SEQ", "pro_finish_fabric_rcv_dtls", $con);
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			
			
			if($data_array_trans!="") $data_array_trans.=",";
			$data_array_trans.="(".$transactionID.",".$id.",'".$$recvBasis."','".$$batchId."',".$cbo_company_id.",'".$$productId."',2,4,".$txt_issue_rtn_date.",".$cbo_to_store.",'".$$brandId."',12,'".$$rollWgt."','".$$rollRate."','".$$rollAmt."',12,'".$$rollWgt."','".$$rollRate."','".$$rollAmt."','".$$rollWgt."','".$$rollAmt."','".$$machineNoId."','".$$floorId."','".$$roomId."','".$$rack."','".$$shelf."','".$$bin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			
			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_prop.="(".$id_prop.",".$transactionID.",4,126,'".$dtls_id."','".$$orderId."','".$$productId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$colorId."')";

			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$id.",".$transactionID.",'".$$productId."','".$$febDescripId."','".$$gsm."','".$$diaWidth."','".$$orderId."','".$$rollWgt."','".$$rollRate."','".$$rollAmt."',12,'".$$machineNoId."','".$$floorId."','".$$roomId."','".$$rack."','".$$shelf."','".$$bin."','".$$colorId."','".$$body_part."','".$$batchId."','".$$txtRemark."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			
			
			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",".$id.",".$dtls_id.",'".$$orderId."','".$$BookWithoutOrd."',126,'".$$barcodeNo."','".$$rollId."','".$$rollNo."','".$$rollWgt."','".$$rollRate."','".$$rollAmt."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			

			$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$id_roll."__".$transactionID.",";
			$prodData_array[$$productId]+=$$rollWgt;
			$all_prod_id.=$$productId.",";
			$all_roll_id.=$$rolltableId.",";

			$inserted_roll_id_arr[$id_roll]=$id_roll;
			$new_inserted[$$barcodeNo]=$$barcodeNo;
		}


		$unReturnedBarcode = return_library_array("select barcode_no from pro_roll_details where entry_form =71 and is_returned=0 and barcode_no in (".implode(',', $new_inserted).") and status_active=1","barcode_no","barcode_no");
		foreach ($new_inserted as $val) 
		{
			if($unReturnedBarcode[$val]=="")
			{
				echo "20**Barcode not found in issue for return";
				disconnect($con);
				die;
			}
		}
		
		$prod_id_array=array();
		$all_prod_id=implode(",",array_unique(explode(",",chop($all_prod_id,','))));

		$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($all_prod_id) and store_id=$cbo_to_store  and status_active = 1", "max_date");      
		if($max_transaction_date != "")
		{
			$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_rtn_date)));
			if ($receive_date < $max_transaction_date) 
			{
				echo "20**Issue Return Date Can not Be Less Than Last Transaction Date Of This Lot";
				disconnect($con);
				die;
			}
		} 

		$prodIssueResult=sql_select("select id, current_stock,avg_rate_per_unit, stock_value from product_details_master where id in($all_prod_id) and company_id=$cbo_company_id");
		$field_array_prod_update = "last_purchased_qnty*current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
		
		foreach($prodIssueResult as $row)
		{
			$issue_rtn_qty=$prodData_array[$row[csf('id')]];
			$issue_rtn_value=$issue_rtn_qty*$row[csf('avg_rate_per_unit')];
			$current_stock=$row[csf('current_stock')]+$issue_rtn_qty;
			$current_stock_value=$row[csf('stock_value')]+$issue_rtn_value;
			$avg_rate_per_unit=$current_stock_value/$current_stock;
			if($current_stock <=0){
				$current_stock_value=0;
				$avg_rate_per_unit=0;
			}
			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",($issue_rtn_qty."*'".$current_stock."'*'".$avg_rate_per_unit."'*'".$current_stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
		$all_roll_id_arr=array_unique(explode(",",chop($all_roll_id,',')));
		foreach($all_roll_id_arr as $roll_id)
		{
			$roll_id_array[]=$roll_id;
			$data_array_roll_update[$roll_id]=explode("*",("1*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
		}

		//echo "10**insert into pro_finish_fabric_rcv_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		
		$rID=$rID2=$rID3=$rID4=$rID5=$prodUpdate=$rollUpdate=true;
		$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		$rID3=sql_insert("pro_finish_fabric_rcv_dtls",$field_array_dtls,$data_array_dtls,0);
		$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
		$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $roll_id_array ));

		$rID6 = execute_query("update pro_roll_details set re_transfer=1 where entry_form in (7,37,68,134,126) and barcode_no in (".implode(',', $new_inserted).") and status_active=1 and id not in (".implode(',', $inserted_roll_id_arr).")");
		//echo "10** $rollUpdate";die;
		//echo "10** insert into pro_roll_details ($field_array_roll) values $data_array_roll";die;
		// oci_rollback($con);
	  	// echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$prodUpdate."&&".$rollUpdate;oci_rollback($con);die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate && $rollUpdate)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_receive_system_id[0]."**".substr($barcodeNos,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $prodUpdate && $rollUpdate)
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_receive_system_id[0]."**".substr($barcodeNos,0,-1);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$all_prod_id="";
		
		$field_array="transfer_date*challan_no*updated_by*update_date";
		$data_array=$txt_issue_rtn_date."*".$txt_challan_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array_inv_rec_mst="store_id*updated_by*update_date";
		$data_array_inv_rec_mst=$cbo_to_store."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		
		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, brand_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, machine_id, floor_id, room, rack, self,  bin_box, inserted_by, insert_date";
		$field_array_trans_update = "store_id*floor_id*room*rack*self*bin_box*updated_by*update_date";
		
		//$dtls_id=return_next_id( "id", "pro_finish_fabric_rcv_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, trans_id, prod_id, fabric_description_id, gsm, width, order_id, receive_qnty, rate, amount, uom, machine_no_id, floor, room, rack_no, shelf_no, bin, color_id, body_part_id, batch_id, remarks, inserted_by, insert_date";
		$field_array_dtls_up="floor*room*rack_no*shelf_no*bin*remarks*updated_by*update_date";
		
		
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, booking_without_order, entry_form, barcode_no, roll_id, roll_no, qnty, rate, amount,qc_pass_qnty, inserted_by, insert_date";
		$field_array_roll_update="roll_used*is_returned*updated_by*update_date";
		
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 ); 
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date,color_id";

		for($j=1;$j<=$tot_row;$j++)
		{
			$recvBasis="recvBasis_".$j;
			$barcodeNo="barcodeNo_".$j;
			$progBookPiId="progBookPiId_".$j;
			$batchId="batchId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$BookWithoutOrd="BookWithoutOrd_".$j;
			$rollId="rollId_".$j;
			$rollWgt="rollWgt_".$j;
			$yarnLot="yarnLot_".$j;
			$yarnCount="yarnCount_".$j;
			$colorId="colorId_".$j;
			$stichLn="stichLn_".$j;
			$brandId="brandId_".$j;
			
			$floorId="floorId_".$j;
			$roomId="roomId_".$j;
			$rack="rack_".$j;
			$shelf="shelf_".$j;
			$bin="bin_".$j;
			
			$dtlsId="dtlsId_".$j;
			$transId="transId_".$j;
			$rolltableId="rolltableId_".$j;
			$updateRetRollTableId="updateRetRollTableId_".$j;
			
			$rollNo="rollNo_".$j;
			$febDescripId="febDescripId_".$j;
			$machineNoId="machineNoId_".$j;
			$gsm="gsm_".$j;
			$diaWidth="diaWidth_".$j;
			$rollRate="rollRate_".$j;
			$rollAmt="rollAmt_".$j;
			$colorRange="colorRange_".$j;
			$body_part="bodyPartData_".$j;
			$txtRemark="txtRemark_".$j;
			//echo "10**". $$body_part;die;
			if(str_replace("'","",$$dtlsId)>0) // Update
			{
				if(str_replace("'", "", $$transId))
				{
					$prop_id_array_up[]=$$transId;
					$data_array_transaction_up[$$transId]=explode("*",($cbo_to_store."*'".$$floorId."'*'".$$roomId."'*'".$$rack."'*'".$$shelf."'*'".$$bin."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				$dtls_id_array_up[]=$$dtlsId;
				$data_array_dtls_up[$$dtlsId]=explode("*",("'".$$floor."'*'".$$room."'*'".$$rack."'*'".$$shelf."'*'".$$bin."'*'".$$txtRemark."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				$barcode_dtls_arr[$$barcodeNo]=$$updateRetRollTableId;
				$saved_barcode_arr[$$barcodeNo]=$$barcodeNo;
			}
			else //if(str_replace("'","",$$dtlsId)=="")
			{
				$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$dtls_id = return_next_id_by_sequence("PRO_FIN_FAB_RCV_DTLS_PK_SEQ", "pro_finish_fabric_rcv_dtls", $con);
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				
				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$transactionID.",".$update_id.",'".$$recvBasis."','".$$batchId."',".$cbo_company_id.",'".$$productId."',2,4,".$txt_issue_rtn_date.",".$cbo_to_store.",'".$$brandId."',12,'".$$rollWgt."','".$$rollRate."','".$$rollAmt."',12,'".$$rollWgt."','".$$rollRate."','".$$rollAmt."','".$$rollWgt."','".$$rollAmt."','".$$machineNoId."','".$$floorId."','".$$roomId."','".$$rack."','".$$shelf."','".$$bin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$transactionID.",4,126,'".$dtls_id."','".$$orderId."','".$$productId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$colorId."')";
				
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$update_id.",".$transactionID.",'".$$productId."','".$$febDescripId."','".$$gsm."','".$$diaWidth."','".$$orderId."','".$$rollWgt."','".$$rollRate."','".$$rollAmt."',12,'".$$machineNoId."','".$$floorId."','".$$roomId."','".$$rack."','".$$shelf."','".$$bin."','".$$colorId."','".$$body_part."','".$$batchId."','".$$txtRemark."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				
				
				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",".$update_id.",".$dtls_id.",'".$$orderId."','".$$BookWithoutOrd."',126,'".$$barcodeNo."','".$$rollId."','".$$rollNo."','".$$rollWgt."','".$$rollRate."','".$$rollAmt."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				

				$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$id_roll."__".$transactionID.",";
				$prodData_array[$$productId]+=$$rollWgt;
				$all_prod_id.=$$productId.",";
				$all_roll_id.=$$rolltableId.",";

				$inserted_roll_id_arr[$id_roll]=$id_roll;
				$new_inserted[$$barcodeNo]=$$barcodeNo;
				
			}
			
		}

		if(!empty($new_inserted))
		{
			$unReturnedBarcode = return_library_array("select barcode_no from pro_roll_details where entry_form =71 and is_returned=0 and barcode_no in (".implode(',', $new_inserted).") and status_active=1","barcode_no","barcode_no");
			foreach ($new_inserted as $val) 
			{
				if($unReturnedBarcode[$val]=="")
				{
					echo "20**Barcode not found in issue for return";
					disconnect($con);
					die;
				}
			}
		}

		if(!empty($saved_barcode_arr))
		{
			$barcode_max_id_arr = return_library_array("select max(id) as max_id, barcode_no from pro_roll_details where barcode_no in (".implode(',', $saved_barcode_arr).") and status_active=1 group by barcode_no","barcode_no","max_id");
			foreach ($barcode_dtls_arr as $barcodeNO=>$return_table_id) 
			{
				if($barcode_max_id_arr[$barcodeNO] != $return_table_id)
				{
					echo "20**Sorry barcode $barcodeNO found in next process.\nUpdate not allowed.".$barcode_max_id_arr[$barcodeNO] .'!='. $return_table_id;
					disconnect($con);
					die;
				}
			}
		}
		
		$prod_id_array=array();
		$all_prod_id=implode(",",array_unique(explode(",",chop($all_prod_id,','))));
		if($all_prod_id=="") $all_prod_id=0;

		$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($all_prod_id) and store_id=$cbo_to_store  and status_active = 1", "max_date");      
		if($max_transaction_date != "")
		{
			$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_rtn_date)));
			if ($receive_date < $max_transaction_date) 
			{
				echo "20**Issue Return Date Can not Be Less Than Last Transaction Date Of This Lot";
				disconnect($con);
				die;
			}
		}
		
		//echo "10**"."select id, current_stock,avg_rate_per_unit, stock_value from product_details_master where id in($all_prod_id) and company_id=$cbo_company_id";die;
		$prodIssueResult=sql_select("select id, current_stock,avg_rate_per_unit, stock_value from product_details_master where id in($all_prod_id) and company_id=$cbo_company_id");
		$field_array_prod_update = "last_purchased_qnty*current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
		
		foreach($prodIssueResult as $row)
		{
			$issue_rtn_qty=$prodData_array[$row[csf('id')]];
			$issue_rtn_value=$issue_rtn_qty*$row[csf('avg_rate_per_unit')];
			$current_stock=$row[csf('current_stock')]+$issue_rtn_qty;
			$current_stock_value=$row[csf('stock_value')]+$issue_rtn_value;
			$avg_rate_per_unit=$current_stock_value/$current_stock;
			if($current_stock <=0){
				$current_stock_value=0;
				$avg_rate_per_unit=0;
			}
			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",($issue_rtn_qty."*'".$current_stock."'*'".$avg_rate_per_unit."'*'".$current_stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		$all_roll_id=chop($all_roll_id,',');
		if(!empty($all_roll_id))
		{
			$all_roll_id_arr=array_unique(explode(",",$all_roll_id));
			foreach($all_roll_id_arr as $roll_id)
			{
				$roll_id_array[]=$roll_id;
				$data_array_roll_update[$roll_id]=explode("*",("1*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}
		
		
		
		
		$rID=$rID2=$rID3=$rID4=$rID5=$prodUpdate=$rollUpdate=$rID_6=$rID7=true;
		
		$rID_6=sql_update("inv_receive_master",$field_array_inv_rec_mst,$data_array_inv_rec_mst,"id",$update_id,0);
		
		$rID=sql_update("inv_item_transfer_mst",$field_array,$data_array,"id",$update_id,0);
		if($data_array_trans!="")
		{
			$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		}
		if($data_array_dtls!="")
		{
			$rID3=sql_insert("pro_finish_fabric_rcv_dtls",$field_array_dtls,$data_array_dtls,0);
		}
		if($data_array_roll!="")
		{
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		}
		if($data_array_prop!="")
		{
			$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		}
		
		if(count($prod_id_array)>0)
		{
			$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
		}
		
		if(count($roll_id_array)>0)
		{
			$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $roll_id_array ));
		}

		if(!empty($inserted_roll_id_arr))
		{
			$rID7 = execute_query("update pro_roll_details set re_transfer=1 where entry_form in (7,37,68,134,126) and barcode_no in (".implode(',', $new_inserted).") and status_active=1 and id not in (".implode(',', $inserted_roll_id_arr).")");
		}


		if(!empty($data_array_transaction_up))
		{
			$trans_data_upd=execute_query(bulk_update_sql_statement( "inv_transaction", "id", $field_array_trans_update, $data_array_transaction_up, $prop_id_array_up ));
		}
		if(!empty($data_array_dtls_up))
		{
			// echo "10**".bulk_update_sql_statement( "pro_grey_prod_entry_dtls", "id", $field_array_dtls_up, $data_array_dtls_up, $dtls_id_array_up );die;
			$dtls_data_upd=execute_query(bulk_update_sql_statement( "pro_finish_fabric_rcv_dtls", "id", $field_array_dtls_up, $data_array_dtls_up, $dtls_id_array_up ));
		}
		
		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$prodUpdate."&&".$rollUpdate."&&".$data_array_roll;die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $prodUpdate && $rollUpdate && $rID_6 && $rID7)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_no)."**".substr($barcodeNos,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $prodUpdate && $rollUpdate && $rID_6 && $rID7)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_no)."**".substr($barcodeNos,0,-1);
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
}


if($action=="populate_issue_data")
{
	$data = explode("_",$data);
	
	$issue_no=$data[0];
	$issue_id=$data[1];

	$company_name_array=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_data_array=sql_select($sql_deter);
	foreach( $deter_data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_data_array);
	
	if($issue_id) $issue_cond = " and c.id = $issue_id ";
	$issue_data=sql_select("select a.id, a.barcode_no, a.po_breakdown_id, a.booking_without_order from pro_roll_details a, inv_finish_fabric_issue_dtls b, inv_issue_master c where a.dtls_id=b.id and b.mst_id=c.id and a.entry_form in(71) and c.entry_form in(71) and a.status_active=1 and a.is_deleted=0 and a.roll_used=0 and a.is_returned=0 and c.issue_number='$issue_no' $issue_cond");
	$issue_barcode="";
	foreach($issue_data as $row)
	{
		if($row[csf('barcode_no')]!="") $issue_barcode.=$row[csf('barcode_no')].",";
		$issue_data_array[$row[csf('barcode_no')]]['barcode_no']=$row[csf('barcode_no')];
		$issue_data_array[$row[csf('barcode_no')]]['id']=$row[csf('id')];
		$issue_data_array[$row[csf('barcode_no')]]['po_breakdown_id']=$row[csf('po_breakdown_id')];
		$issue_data_array[$row[csf('barcode_no')]]['booking_without_order']=$row[csf('booking_without_order')];
		if($row[csf('booking_without_order')] == 1){
			$non_order_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}else{
			$po_no_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}
		
	}
	$issue_barcode=chop($issue_barcode,",");

    $po_no_arr = array_filter($po_no_arr);
    $all_po_ids = implode(",", $po_no_arr);
    $all_po_no_arr_cond=""; $poCond="";
    if($db_type==2 && count($po_no_arr)>999)
    {
    	$all_po_no_arr_chunk=array_chunk($po_no_arr,999) ;
    	foreach($all_po_no_arr_chunk as $chunk_arr)
    	{
    		$chunk_arr_value=implode(",",$chunk_arr);
    		$poCond.=" b.id in($chunk_arr_value) or ";
    	}

    	$all_po_no_arr_cond.=" and (".chop($poCond,'or ').")";
    }
    else
    {
    	$all_po_no_arr_cond=" and b.id in($all_po_ids)";
    }

	$po_data_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.file_no FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst $all_po_no_arr_cond");
	$po_details_array=array();
	foreach($po_data_sql as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['file_no']=$row[csf("file_no")];
	}
	unset($po_data_sql);

	$non_order_arr = array_filter($non_order_arr);
    $non_order_ids = implode(",", $non_order_arr);
    $all_non_order_cond=""; $nonOrderCond="";
    if($db_type==2 && count($non_order_arr)>999)
    {
    	$all_non_order_arr_chunk=array_chunk($non_order_arr,999) ;
    	foreach($all_non_order_arr_chunk as $chunk_arr)
    	{
    		$chunk_arr_value=implode(",",$chunk_arr);
    		$nonOrderCond.=" id in($chunk_arr_value) or ";
    	}

    	$all_non_order_cond.=" and (".chop($nonOrderCond,'or ').")";
    }
    else
    {
    	$all_non_order_cond=" and id in($non_order_ids)";
    }

    $non_order_sql = sql_select("select buyer_id, id, booking_no from wo_non_ord_samp_booking_mst where status_active=1 $all_non_order_cond");
	foreach ($non_order_sql as  $val) 
	{
		$non_order_ref[$val[csf("id")]]["buyer_name"] = $val[csf("buyer_id")];
		$non_order_ref[$val[csf("id")]]["booking_no"] = $val[csf("booking_no")];
	}

	
	$batch_rcv_barcode=return_library_array("select barcode_no from pro_roll_details  where entry_form=72 and roll_split_from=0 and status_active=1 and is_deleted=0 and barcode_no in($issue_barcode)","barcode_no","barcode_no");

	$data_array=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.machine_no_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qc_pass_qnty, c.roll_id as roll_id_prev,c.rate, c.booking_no, c.booking_without_order, max(c.reprocess) as  reprocess FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($issue_barcode) group by a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.machine_no_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id, c.roll_no, c.po_breakdown_id, c.qc_pass_qnty, c.roll_id,c.rate, c.booking_no, c.booking_without_order");
	
	$roll_details_array=array(); $barcode_array=array(); 
	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			if($batch_rcv_barcode[$row[csf('barcode_no')]]=="")
			{
				$receive_basis=$receive_basis_arr[$row[csf("receive_basis")]];
				$receive_basis_id=$row[csf("receive_basis")];
				
				if($row[csf("roll_id")]==0) 
				{
					$roll_id=$row[csf("roll_tbl_id")];
				}
				else
				{
					$roll_id=$row[csf("roll_id")];
				}
				
				$color='';
				$color_id=explode(",",$row[csf('color_id')]);
				foreach($color_id as $val)
				{
					if($val>0) $color.=$color_arr[$val].",";
				}
				$color=chop($color,',');
				if($row[csf("knitting_source")]==1)
				{
					$knitting_company_name=$company_name_array[$row[csf("knitting_company")]];
				}
				else if($row[csf("knitting_source")]==3)
				{
					$knitting_company_name=$supplier_arr[$row[csf("knitting_company")]];
				}
				
				$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];//entry_form $machine_arr

				$po_breakdown_id= $issue_data_array[$row[csf('barcode_no')]]['po_breakdown_id'];
				$booking_without_order = $issue_data_array[$row[csf('barcode_no')]]['booking_without_order'];

				if($booking_without_order==1)
				{
					$job_no ="";
					$buyer_id =$non_order_ref[$po_breakdown_id]["buyer_name"];
					$po_number = "";
					$file_no = "";
				}else{
					$job_no = $po_details_array[$po_breakdown_id]['job_no'];
					$buyer_id = $po_details_array[$po_breakdown_id]['buyer_name'];
					$po_number = $po_details_array[$po_breakdown_id]['po_number'];
					$file_no = $po_details_array[$po_breakdown_id]['file_no'];
				}
				
				$compsition_description=$constructtion_arr[$row[csf("fabric_description_id")]]." ".$composition_arr[$row[csf("fabric_description_id")]];

				$barcodeData.=$row[csf('id')]."**".$row[csf("company_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("store_id")]."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$row[csf("prod_id")]."**".$row[csf("fabric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$po_breakdown_id."**".$row[csf("qc_pass_qnty")]."**".$row[csf("barcode_no")]."**".$job_no."**".$buyer_id."**".$buyer_arr[$buyer_id]."**".$po_number."**".$file_no."**".$row[csf("color_id")]."**".$store_arr[$row[csf("store_id")]]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$machine_arr[$row[csf("machine_no_id")]]."**".$row[csf("machine_no_id")]."**".$row[csf("entry_form")]."**".$issue_data_array[$row[csf('barcode_no')]]['id']."**".$row[csf('rate')]."**".$row[csf('amount')]."**".$row[csf('color_range_id')]."**".$booking_without_order."**".$row[csf('batch_id')]."__";
			}
			else
			{
				$barcodeData.=-1 ."__";
			}
		}
		$barcodeData=implode("__",array_unique(explode("__",chop($barcodeData,"__"))));
		echo trim($barcodeData);
	}
	else
	{
		echo "0";
	}
	exit();
}

if($action=="populate_barcode_data")
{
	//echo "dfdf";die;
	$data = explode("_",$data);
	
	$barcodeNos=$data[0];
	$issue_no=$data[1];

	$company_name_array=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	if($issue_no) $issue_cond = " and a.mst_id = $issue_no ";
	$issue_data=sql_select("select a.id, a.mst_id, a.barcode_no, b.prod_id, a.qc_pass_qnty, a.booking_without_order, a.po_breakdown_id  from pro_roll_details a,inv_finish_fabric_issue_dtls b where a.dtls_id=b.id and a.entry_form=71 and a.status_active=1 and a.is_deleted=0 and a.roll_used=0 and a.is_returned=0 and a.barcode_no in($barcodeNos) $issue_cond");
	$issue_barcode="";
	foreach($issue_data as $row)
	{
		if($row[csf('barcode_no')]!="") $issue_barcode.=$row[csf('barcode_no')].",";
		$issue_data_array[$row[csf('barcode_no')]]['barcode_no']=$row[csf('barcode_no')];
		$issue_data_array[$row[csf('barcode_no')]]['id']=$row[csf('id')];
		$issue_data_array[$row[csf('barcode_no')]]['issue_id']=$row[csf('mst_id')];
		$issue_data_array[$row[csf("barcode_no")]]['prod_id']=$row[csf("prod_id")];
		$issue_data_array[$row[csf("barcode_no")]]['qnty']=$row[csf("qc_pass_qnty")];
		$issue_data_array[$row[csf('barcode_no')]]['po_breakdown_id']=$row[csf('po_breakdown_id')];
		$issue_data_array[$row[csf('barcode_no')]]['booking_without_order']=$row[csf('booking_without_order')];
		if($row[csf('booking_without_order')] == 1){
			$non_order_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}else{
			$po_no_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}
	}
	$issue_barcode=chop($issue_barcode,",");
	
	if($issue_barcode=="") {echo "0"; die;}


	$po_no_arr = array_filter($po_no_arr);
    $all_po_ids = implode(",", $po_no_arr);
    $all_po_no_arr_cond=""; $poCond="";
    if($db_type==2 && count($po_no_arr)>999)
    {
    	$all_po_no_arr_chunk=array_chunk($po_no_arr,999) ;
    	foreach($all_po_no_arr_chunk as $chunk_arr)
    	{
    		$chunk_arr_value=implode(",",$chunk_arr);
    		$poCond.=" b.id in($chunk_arr_value) or ";
    	}

    	$all_po_no_arr_cond.=" and (".chop($poCond,'or ').")";
    }
    else
    {
    	$all_po_no_arr_cond=" and b.id in($all_po_ids)";
    }

	$po_data_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.file_no FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst $all_po_no_arr_cond");
	$po_details_array=array();
	foreach($po_data_sql as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['file_no']=$row[csf("file_no")];
	}
	unset($po_data_sql);

	$non_order_arr = array_filter($non_order_arr);
    $non_order_ids = implode(",", $non_order_arr);
    $all_non_order_cond=""; $nonOrderCond="";
    if($db_type==2 && count($non_order_arr)>999)
    {
    	$all_non_order_arr_chunk=array_chunk($non_order_arr,999) ;
    	foreach($all_non_order_arr_chunk as $chunk_arr)
    	{
    		$chunk_arr_value=implode(",",$chunk_arr);
    		$nonOrderCond.=" id in($chunk_arr_value) or ";
    	}

    	$all_non_order_cond.=" and (".chop($nonOrderCond,'or ').")";
    }
    else
    {
    	$all_non_order_cond=" and id in($non_order_ids)";
    }

    $non_order_sql = sql_select("select buyer_id, id, booking_no from wo_non_ord_samp_booking_mst where status_active=1 $all_non_order_cond");
	foreach ($non_order_sql as  $val) 
	{
		$non_order_ref[$val[csf("id")]]["buyer_name"] = $val[csf("buyer_id")];
		$non_order_ref[$val[csf("id")]]["booking_no"] = $val[csf("booking_no")];
	}

	$batch_rcv_barcode=return_library_array("select barcode_no from pro_roll_details  where entry_form=72 and roll_split_from=0 and status_active=1 and is_deleted=0 and barcode_no in($issue_barcode)","barcode_no","barcode_no");

	if($issue_barcode!="")
	{		
		$data_array=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.machine_no_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qc_pass_qnty, c.roll_id as roll_id_prev,c.rate, c.booking_no, c.booking_without_order, max(c.reprocess) as  reprocess FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($issue_barcode) group by a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.machine_no_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id, c.roll_no, c.po_breakdown_id, c.qc_pass_qnty, c.roll_id,c.rate, c.booking_no, c.booking_without_order");
	}
	
	$roll_details_array=array(); $barcode_array=array(); 
	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			if($batch_rcv_barcode[$row[csf('barcode_no')]]=="")
			{
				$receive_basis=$receive_basis_arr[$row[csf("receive_basis")]];
				$receive_basis_id=$row[csf("receive_basis")];
				
				if($row[csf("roll_id")]==0) 
				{
					$roll_id=$row[csf("roll_tbl_id")];
				}
				else
				{
					$roll_id=$row[csf("roll_id")];
				}
				
				$color='';
				$color_id=explode(",",$row[csf('color_id')]);
				foreach($color_id as $val)
				{
					if($val>0) $color.=$color_arr[$val].",";
				}
				$color=chop($color,',');
				if($row[csf("knitting_source")]==1)
				{
					$knitting_company_name=$company_name_array[$row[csf("knitting_company")]];
				}
				else if($row[csf("knitting_source")]==3)
				{
					$knitting_company_name=$supplier_arr[$row[csf("knitting_company")]];;
				}
				
				$po_breakdown_id= $issue_data_array[$row[csf('barcode_no')]]['po_breakdown_id'];
				$booking_without_order = $issue_data_array[$row[csf('barcode_no')]]['booking_without_order'];

				if($booking_without_order==1)
				{
					$job_no ="";
					$buyer_id =$non_order_ref[$po_breakdown_id]["buyer_name"];
					$po_number = "";
					$file_no = "";
				}else{
					$job_no = $po_details_array[$po_breakdown_id]['job_no'];
					$buyer_id = $po_details_array[$po_breakdown_id]['buyer_name'];
					$po_number = $po_details_array[$po_breakdown_id]['po_number'];
					$file_no = $po_details_array[$po_breakdown_id]['file_no'];
				}
			
				$compsition_description=$constructtion_arr[$row[csf("fabric_description_id")]]." ".$composition_arr[$row[csf("fabric_description_id")]];
				
				$barcodeData.=$row[csf('id')]."**".$row[csf("company_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("store_id")]."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$issue_data_array[$row[csf("barcode_no")]]['prod_id']."**".$row[csf("fabric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$po_breakdown_id."**".$issue_data_array[$row[csf("barcode_no")]]['qnty']."**".$row[csf("barcode_no")]."**".$job_no."**".$buyer_id."**".$buyer_arr[$buyer_id]."**".$po_number."**".$file_no."**".$row[csf("color_id")]."**".$store_arr[$row[csf("store_id")]]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$machine_arr[$row[csf("machine_no_id")]]."**".$row[csf("machine_no_id")]."**".$row[csf("entry_form")]."**".$issue_data_array[$row[csf('barcode_no')]]['id']."**".$row[csf('rate')]."**".$row[csf('amount')]."**".$row[csf('color_range_id')]."**".$booking_without_order."**".$row[csf('batch_id')]."__";
			
			}
			else
			{
				$barcodeData.=-1 ."__";
			}
		}
		$barcodeData=implode("__",array_unique(explode("__",chop($barcodeData,"__"))));
		echo trim($barcodeData);
	}
	else
	{
		echo "0";
	}
	exit();
}

if($action=="populate_barcode_data_update")
{
	$company_name_array=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	$scanned_barcode_update_data=sql_select("select a.id as roll_upid, a.po_breakdown_id, a.booking_without_order, a.barcode_no, a.roll_id, a.dtls_id, b.floor, b.room, b.rack_no, b.shelf_no, b.bin, b.remarks, b.trans_id from pro_roll_details a, pro_finish_fabric_rcv_dtls b where a.dtls_id=b.id and a.entry_form in(126) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$data");
	foreach($scanned_barcode_update_data as $row)
	{
		if($row[csf('barcode_no')]!="") $issue_barcode.=$row[csf('barcode_no')].",";
		$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']=$row[csf('roll_upid')];
		$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']=$row[csf('dtls_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['trans_id']=$row[csf('trans_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']=$row[csf('po_breakdown_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['booking_without_order']=$row[csf('booking_without_order')];

		$barcode_update_data[$row[csf("barcode_no")]]['floor']=$row[csf("floor")];
		$barcode_update_data[$row[csf("barcode_no")]]['room']=$row[csf("room")];
		$barcode_update_data[$row[csf("barcode_no")]]['rack_no']=$row[csf("rack_no")];
		$barcode_update_data[$row[csf("barcode_no")]]['shelf_no']=$row[csf("shelf_no")];
		$barcode_update_data[$row[csf("barcode_no")]]['bin_box']=$row[csf("bin")];
		$barcode_update_data[$row[csf("barcode_no")]]['remarks']=$row[csf("remarks")];

		if($row[csf('booking_without_order')] == 1){
			$non_order_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}else{
			$po_no_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}
	}
	$issue_barcode=chop($issue_barcode,",");

	$po_no_arr = array_filter($po_no_arr);
	if(!empty($po_no_arr))
	{
	    $all_po_ids = implode(",", $po_no_arr);
	    $all_po_no_arr_cond=""; $poCond="";
	    if($db_type==2 && count($po_no_arr)>999)
	    {
	    	$all_po_no_arr_chunk=array_chunk($po_no_arr,999) ;
	    	foreach($all_po_no_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$poCond.=" b.id in($chunk_arr_value) or ";
	    	}
	    	$all_po_no_arr_cond.=" and (".chop($poCond,'or ').")";
	    }
	    else
	    {
	    	$all_po_no_arr_cond=" and b.id in($all_po_ids)";
	    }

		$po_data_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.file_no FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst $all_po_no_arr_cond");
		$po_details_array=array();
		foreach($po_data_sql as $row)
		{
			$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
			$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
			$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
			$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
			$po_details_array[$row[csf("po_id")]]['file_no']=$row[csf("file_no")];
		}
		unset($po_data_sql);
	}

	$non_order_arr = array_filter($non_order_arr);
	if(!empty($non_order_arr))
	{
	    $non_order_ids = implode(",", $non_order_arr);
	    $all_non_order_cond=""; $nonOrderCond="";
	    if($db_type==2 && count($non_order_arr)>999)
	    {
	    	$all_non_order_arr_chunk=array_chunk($non_order_arr,999) ;
	    	foreach($all_non_order_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$nonOrderCond.=" id in($chunk_arr_value) or ";
	    	}

	    	$all_non_order_cond.=" and (".chop($nonOrderCond,'or ').")";
	    }
	    else
	    {
	    	$all_non_order_cond=" and id in($non_order_ids)";
	    }

	    $non_order_sql = sql_select("select buyer_id, id, booking_no from wo_non_ord_samp_booking_mst where status_active=1 $all_non_order_cond");
		foreach ($non_order_sql as  $val) 
		{
			$non_order_ref[$val[csf("id")]]["buyer_name"] = $val[csf("buyer_id")];
			$non_order_ref[$val[csf("id")]]["booking_no"] = $val[csf("booking_no")];
		}
	}
	
	$issue_data=sql_select("select a.id,a.barcode_no, b.prod_id,a.qc_pass_qnty, b.floor, b.room, b.rack_no, b.shelf_no, b.bin_box  from pro_roll_details a,inv_finish_fabric_issue_dtls b, inv_receive_master c where a.dtls_id=b.id and a.entry_form=71 and a.status_active=1 and a.is_deleted=0 and a.roll_used=1 and a.barcode_no in($issue_barcode) and b.mst_id=c.issue_id and c.entry_form=126
		and c.id=$data");

	
	foreach($issue_data as $row)
	{
		
		$issue_data_array[$row[csf('barcode_no')]]['barcode_no']=$row[csf('barcode_no')];
		$issue_data_array[$row[csf('barcode_no')]]['id']=$row[csf('id')];
		$issue_data_array[$row[csf("barcode_no")]]['prod_id']=$row[csf("prod_id")];
		$issue_data_array[$row[csf("barcode_no")]]['qnty']=$row[csf("qc_pass_qnty")];

	}
	
	$data_array=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.machine_no_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qc_pass_qnty, c.roll_id as roll_id_prev,c.rate, c.booking_no, c.booking_without_order, max(c.reprocess) as  reprocess FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($issue_barcode) group by a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.machine_no_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id, c.roll_no, c.po_breakdown_id, c.qc_pass_qnty, c.roll_id,c.rate, c.booking_no, c.booking_without_order");
	
	$roll_details_array=array(); $barcode_array=array(); 
	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			$receive_basis=$receive_basis_arr[$row[csf("receive_basis")]];
			$receive_basis_id=$row[csf("receive_basis")];
			
			if($row[csf("roll_id")]==0) 
			{
				$roll_id=$row[csf("roll_tbl_id")];
			}
			else
			{
				$roll_id=$row[csf("roll_id")];
			}
			
			$color='';
			$color_id=explode(",",$row[csf('color_id')]);
			foreach($color_id as $val)
			{
				if($val>0) $color.=$color_arr[$val].",";
			}
			$color=chop($color,',');
			if($row[csf("knitting_source")]==1)
			{
				$knitting_company_name=$company_name_array[$row[csf("knitting_company")]];
			}
			else if($row[csf("knitting_source")]==3)
			{
				$knitting_company_name=$supplier_arr[$row[csf("knitting_company")]];
			}
			
			$booking_without_order = $barcode_update_data[$row[csf('barcode_no')]]['booking_without_order'];
			$po_id = $barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id'];

			if($booking_without_order==1)
			{
				$job_no ="";
				$buyer_id =$non_order_ref[$po_id]["buyer_name"];
				$po_number = "";
				$file_no = "";
			}else{
				$job_no = $po_details_array[$po_id]['job_no'];
				$buyer_id = $po_details_array[$po_id]['buyer_name'];
				$po_number = $po_details_array[$po_id]['po_number'];
				$file_no = $po_details_array[$po_id]['file_no'];
			}

			$floor_id = $barcode_update_data[$row[csf("barcode_no")]]['floor'];
			$room_id =$barcode_update_data[$row[csf("barcode_no")]]['room'];
			$rack_no = $barcode_update_data[$row[csf("barcode_no")]]['rack_no'];
			$shelf_no = $barcode_update_data[$row[csf("barcode_no")]]['shelf_no'];
			$bin_box = $barcode_update_data[$row[csf("barcode_no")]]['bin_box'];
			$remarks = $barcode_update_data[$row[csf("barcode_no")]]['remarks'];
			$roll_upid = $barcode_update_data[$row[csf('barcode_no')]]['roll_upid'];
			$up_trans_id = $barcode_update_data[$row[csf('barcode_no')]]['trans_id'];



			$compsition_description=$constructtion_arr[$row[csf("fabric_description_id")]]." ".$composition_arr[$row[csf("fabric_description_id")]];
			
			$barcodeData.=$row[csf('id')]."**".$row[csf("company_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("store_id")]."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$rack_no."**".$shelf_no."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$issue_data_array[$row[csf("barcode_no")]]['prod_id']."**".$row[csf("fabric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$po_id."**".$issue_data_array[$row[csf("barcode_no")]]['qnty']."**".$row[csf("barcode_no")]."**".$job_no."**".$buyer_id."**".$buyer_arr[$buyer_id]."**".$po_number."**".$file_no."**".$row[csf("color_id")]."**".$store_arr[$row[csf("store_id")]]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$machine_arr[$row[csf("machine_no_id")]]."**".$row[csf("machine_no_id")]."**".$row[csf("entry_form")]."**".$issue_data_array[$row[csf('barcode_no')]]['id']."**".$row[csf('rate')]."**".$row[csf('amount')]."**".$row[csf('color_range_id')]."**".$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']."**".$booking_without_order."**".$floor_id."**".$room_id."**".$bin_box."**".$batch_id."**".$remarks."**".$roll_upid."**".$up_trans_id."__";
			
		}
		$barcodeData=implode("__",array_unique(explode("__",chop($barcodeData,"__"))));
		echo trim($barcodeData);
	}
	else
	{
		echo "0";
	}
	exit();
}



if($action=="issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 

	<script>

		function js_set_value(id)
		{
			$('#hidden_system_id').val(id);
			parent.emailwindow.hide();
		}

	</script>

</head>

<body>
	<div align="center" style="width:760px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:760px; margin-left:2px">
				<table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Company</th>
						<th>Date Range</th>
						<th>Issue Return No</th>
						<th>Barcode No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="hidden_system_id" id="hidden_system_id">  
						</th> 
					</thead>
					<tr class="general">

						<td align="center">	
							<? 
							echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
							?>
						</td> 
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
						</td>    
						<td align="center">				
							<input type="text" style="width:120px" class="text_boxes"  name="txt_issue_no" id="txt_issue_no" />	
						</td>
						<td align="center">				
							<input type="text" style="width:110px" class="text_boxes"  name="txt_barcode_no" id="txt_barcode_no" />	
						</td> 						
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_issue_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_barcode_no').value, 'create_challan_search_list_view', 'search_div', 'finish_roll_issue_return_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);
	
	$company_id=$data[0];
	$issue_no=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$barcode_no =$data[4];
	$str_cond="";
	if($company_id>0){ $str_cond=" and a.company_id=$company_id";} else { echo "Please Select Company"; die;}
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$str_cond.=" and a.receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd","-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$str_cond.=" and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	
	
	$search_field_cond="";
	if(trim($issue_no)!="")
	{
		$str_cond.=" and a.recv_number like '%$issue_no'";
	}
	if(trim($barcode_no)!="")
	{
		$str_cond.=" and c.barcode_no=$barcode_no";
	}
	
	
	if($db_type==0) 
	{
		$year_field="YEAR(a.insert_date) as year,";
	}
	else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year,";
	}
	else $year_field="";//defined Later
	
	$sql = "select a.id, $year_field a.recv_number_prefix_num, a.recv_number, a.company_id, a.store_id, a.challan_no, a.receive_date
	from   inv_receive_master a, pro_roll_details c
	where a.id=c.mst_id and a.entry_form=126 and c.entry_form=126 and a.status_active=1 and a.is_deleted=0 $str_cond
	group by a.id, a.insert_date, a.recv_number_prefix_num, a.recv_number, a.company_id, a.store_id, a.challan_no, a.receive_date
	order by a.id desc"; 
	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table">
		<thead>
			<th width="50">SL</th>
			<th width="150">Company</th>
			<th width="140">Issue No</th>
			<th width="50">Year</th>
			<th width="150">To Store</th>
			<th width="100">Challan</th>
			<th>Transfer date</th>
		</thead>
	</table>
	<div style="width:760px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>');"> 
					<td width="50" align="center"><? echo $i; ?></td>
					<td width="150" align="center"><p>&nbsp;<? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
					<td width="140" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
					<td width="50"><p><? echo $row[csf('year')]; ?>&nbsp;</p></td>
					<td width="150"><p><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
					<td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?	
	exit();
}

if($action=="populate_data_from_data")
{
	//$sql = "select id, company_id, issue_number, knit_dye_source, knit_dye_company, issue_date, batch_no, issue_purpose from inv_issue_master where id=$data and entry_form=61";
	/*$sql = "select a.id, a.recv_number, a.company_id, a.receive_date, a.challan_no, a.store_id,issue_id 
	from   inv_receive_master a
	where a.entry_form=126 and a.id=$data"; */
	$sql ="select a.id, a.recv_number, a.company_id, a.receive_date, a.challan_no, a.store_id, d.location_id, a.issue_id , b.issue_number
    from   inv_receive_master a, inv_issue_master b, lib_store_location d
    where  a.issue_id = B.ID and a.entry_form=126 and a.store_id=d.id and a.id = $data and a.status_active=1 and a.is_deleted = 0";
	//echo $sql;die;

	$variable_inventory_arr=return_library_array("select company_name, store_method from variable_settings_inventory  where item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 ","company_name","store_method");
	$res = sql_select($sql);	
	foreach($res as $row)
	{		
		echo "$('#txt_system_no').val('".$row[csf("recv_number")]."');\n";
		echo "$('#update_id').val(".$row[csf("id")].");\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";

		echo "$('#cbo_location_name').val(".$row[csf("location_id")].");\n";
		echo "load_drop_down( 'requires/finish_roll_issue_return_controller', '".$row[csf("company_id")].'_'.$row[csf("location_id")]."', 'load_drop_down_store', 'store_td' );\n";

		echo "fn_load_floor(".$row[csf("store_id")].");\n";

		
		echo "$('#cbo_to_store').val(".$row[csf("store_id")].");\n";

		echo "$('#cbo_to_store').attr('disabled','disabled');\n";

		echo "$('#txt_issue_rtn_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_issue_id').val('".$row[csf("issue_id")]."');\n";
		echo "$('#txt_issue_challan_no').val('".$row[csf("issue_number")]."');\n";
		echo "$('#store_update_upto').val(".$variable_inventory_arr[$row[csf('company_id')]].");\n";
		
	}
	exit();	
}


if($action=="populate_master_from_data")
{
	$data_ref=explode("__",$data);
	if($data_ref[1]==1) $sql_cond=" and c.barcode_no in(".$data_ref[0].")"; else  $sql_cond=" and a.issue_number="."'".$data_ref[0]."'";
	$sql = "select a.company_id,a.store_id, d.location_id, a.id,a.issue_number 
	from  inv_issue_master a,  inv_finish_fabric_issue_dtls b, pro_roll_details c, lib_store_location d
	where a.id=b.mst_id and b.id=c.dtls_id and a.store_id=d.id and a.entry_form=71 and c.entry_form=71 $sql_cond
	group by a.company_id ,a.store_id, d.location_id, a.id,a.issue_number"; 
	//echo $sql;die;

	$variable_inventory_arr=return_library_array("select company_name, store_method from variable_settings_inventory  where item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 ","company_name","store_method");
	$res = sql_select($sql);	
	foreach($res as $row)
	{		
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#txt_issue_id').val(".$row[csf("id")].");\n";
		echo "$('#cbo_location_name').val(".$row[csf("location_id")].");\n";
		echo "$('#txt_issue_challan_no').val('".$row[csf("issue_number")]."');\n";
		echo "$('#store_update_upto').val(".$variable_inventory_arr[$row[csf('company_id')]].");\n";
		echo "load_drop_down('requires/finish_roll_issue_return_controller', '".$row[csf("company_id")].'_'.$row[csf("location_id")]."', 'load_drop_down_store', 'store_td' );\n";
	}
	exit();	
}


if($action=="barcode_nos")
{
	if($db_type==0) 
	{
		$barcode_nos=return_field_value("group_concat(barcode_no order by id desc) as barcode_nos","pro_roll_details","entry_form=126 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
	}
	else if($db_type==2) 
	{
		$barcode_nos=return_field_value("LISTAGG(barcode_no, ',') WITHIN GROUP (ORDER BY id desc) as barcode_nos","pro_roll_details","entry_form=126 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
	}
	echo $barcode_nos;
	exit();	
}

if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($company_id>0) $disable=1; else $disable=0;  
	?> 

	<script>

		var selected_id = new Array();
		var issue_id_arr = new Array;
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{

			var pre_issue_id = $('#hidden_issue_no').val();
			if(pre_issue_id=="")
			{
				issue_id_arr = [];
			}

			var issue_id = $('#txt_issue_id_' + str).val();

			if(issue_id_arr.length==0)
			{
				issue_id_arr.push( issue_id );
			}
			else if( jQuery.inArray( issue_id, issue_id_arr )==-1 &&  issue_id_arr.length>0)
			{
				alert("Issue No Mixed is Not Allowed");
				return;
			}

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}

			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_barcode_nos').val( id );

			if(id=="")
			{
				$('#hidden_issue_no').val('');
			}
			else
			{
				$('#hidden_issue_no').val(issue_id);
			}
		}
		
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			$('#hidden_barcode_nos').val( '' );
			selected_id = new Array();
		}

		function fnc_show()
		{
			if(form_validation('cbo_company_id','Company')==false)
			{
				return; 
			}

			if($('#txt_order_no').val().trim() =="" && $('#txt_file_no').val().trim() =="" && $('#txt_ref_no').val().trim() =="" && $('#barcode_no').val().trim() =="" && $('#txt_batch_no').val().trim() =="")
			{
				alert("Please select any criteria");
				$('#txt_order_no').focus();
				return;
			}

			show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('barcode_no').value+'_'+'<? echo $issue_id;?>'+'_'+document.getElementById('txt_batch_no').value, 'create_barcode_search_list_view', 'search_div', 'finish_roll_issue_return_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:1030px;">
			<form name="searchwofrm"  id="searchwofrm">
				<fieldset style="width:1030px; margin-left:2px;">
					<legend>Enter search words</legend>           
					<table cellpadding="0" cellspacing="0" width="850" border="1" rules="all" class="rpt_table">
						<thead>
							<th class="must_entry_caption">Company</th>
							<th>Order No</th>
							<th>File No</th>
							<th>Internal Ref No</th>
							<th>Batch No</th>
							<th>Barcode No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
								<input type="hidden" name="hidden_issue_no" id="hidden_issue_no" value="<? echo $issue_id;?>">  
							</th> 
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $company_id, "",$disable );
								?>
							</td>
							<td align="center">				
								<input type="text" style="width:120px" class="text_boxes"  name="txt_order_no" id="txt_order_no" />	
							</td> 
							<td align="center">				
								<input type="text" style="width:120px" class="text_boxes"  name="txt_file_no" id="txt_file_no" />	
							</td>
							<td align="center">				
								<input type="text" style="width:120px" class="text_boxes"  name="txt_ref_no" id="txt_ref_no" />	
							</td>	
							<td align="center">
								<input type="text" name="txt_batch_no" id="txt_batch_no" style="width:120px" class="text_boxes" />
							</td>		
							<td align="center">
								<input type="text" name="barcode_no" id="barcode_no" style="width:120px" class="text_boxes" />
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="fnc_show()" style="width:100px;" />
							</td>
						</tr>
					</table>
					<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_barcode_search_list_view")
{
	$data = explode("_",$data);
	$company_id=trim($data[0]);
	$order_no=$data[1];
	$file_no =trim($data[2]);
	$ref_no =trim($data[3]);
	$barcode_no =trim($data[4]);
	$issue_id =trim($data[5]);
	$batch_no =trim($data[6]);
	
	//echo $store_id.jahid;die;
	

	$search_field_cond="";
	if($order_no!="") $search_field_cond=" and d.po_number like '%$order_no%'";
	if($file_no!="") $search_field_cond.=" and d.file_no like '%$file_no%'";
	if($ref_no!="") $search_field_cond.=" and d.grouping like '%$ref_no%'";
	if($batch_no!="") $search_field_cond.=" and f.batch_no like '%$batch_no%'";
	if($company_id!=0) $search_field_cond.=" and a.company_id=$company_id";
	if($issue_id) $search_field_cond.=" and a.id=$issue_id";
	
	
	if($barcode_no!="")
	{
		$barcode_cond=" and c.barcode_no='$barcode_no'";
	}

	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2",'id','product_name_details');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$cutting_rcv_barcode=return_library_array("select barcode_no from pro_roll_details  where entry_form=72 and roll_split_from=0 and status_active=1 and is_deleted=0 ","barcode_no","barcode_no");
	/*
	$sql="SELECT a.id, a.issue_number, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping
	FROM inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_roll_details c, wo_po_break_down d 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.roll_used=0 and a.entry_form in(71) and c.entry_form in(71) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond";*/


	$sql="SELECT a.id, a.issue_number, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, f.batch_no 
	from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_roll_details c, wo_po_break_down d, inv_transaction e, pro_batch_create_mst f
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.id=e.mst_id and b.trans_id=e.id and e.pi_wo_batch_no=f.id and e.item_category=2 and c.roll_used=0 and a.entry_form in(71) and c.entry_form in(71) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond
	group by a.id, a.issue_number, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, f.batch_no";

	//echo $sql;//die;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="250">Fabric Description</th>
			<th width="100">Job No</th>
			<th width="110">Order No</th>
			<th width="70">File NO</th>
			<th width="70">Ref No</th>
			<th width="70">Shipment Date</th>
			<th width="70">Batch No</th>
			<th width="90">Barcode No</th>
			<th width="50">Roll No</th>
			<th>Roll Qty.</th>
		</thead>
	</table>
	<div style="width:1030px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;
			foreach ($result as $row)
			{  
				if($cutting_rcv_barcode[$row[csf('barcode_no')]] == "")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td width="30" align="center">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
							<input type="hidden" name="txt_issue_id" id="txt_issue_id_<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>"/>
						</td>
						<td width="250"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						<td width="100"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td width="70" align="center"><p><? echo $row[csf('file_no')]; ?></p></td>
						<td width="70" align="center"><p><? echo $row[csf('grouping')]; ?></p></td>
						<td width="70" align="center"><p><? echo change_date_format($row[csf('pub_shipment_date')]); ?></p></td>
						<td width="70"><p><? echo $row[csf('batch_no')]; ?></p></td>
						<td width="90"><p><? echo $row[csf('barcode_no')]; ?></p></td>
						<td width="50" align="center"><p><? echo $row[csf('roll_no')]; ?></p></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
					<?
					$i++;
				}
			}
			?>
		</table>
	</div>
	<table width="1010">
		<tr>
			<td align="center" >
				<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
			</td>
		</tr>
	</table>
	<?	
	exit();
}


if($action=="challan_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($company_id>0) $disable=1; else $disable=0;
	?> 
	<script>
		function js_set_value(str) 
		{
			$('#hidden_issue_id').val(str);
			parent.emailwindow.hide();
		}
	</script>

</head>

<body>
	<div align="center" style="width:860px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:860px; margin-left:2px;">
				<legend>Enter search words</legend>           
				<table cellpadding="0" cellspacing="0" width="820" border="1" rules="all" class="rpt_table">
					<thead>
						<th width="220" class="must_entry_caption">Company</th>
						<th width="160">Issue No</th>
						<th width="300">Issue Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="hidden_issue_id" id="hidden_issue_id">  
						</th> 
					</thead>
					<tr class="general">
						<td>
							<?
							echo create_drop_down( "cbo_company_id", 200, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $company_id, "", $disable );
							?>
						</td>
						<td align="center">				
							<input type="text" style="width:140px" class="text_boxes"  name="txt_issue_no" id="txt_issue_no" />	
						</td> 
						<td align="center">				
							<input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:80px" placeholder="From Date" readonly>&nbsp;To&nbsp;
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"placeholder="To Date" readonly>	
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_issue_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $issue_id;?>', 'create_issue_search_list_view', 'search_div', 'finish_roll_issue_return_controller', 'setFilterGrid(\'tbl_issue_list\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="4" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_issue_search_list_view")
{
	$data = explode("_",$data);
	
	$company_id=trim($data[0]);
	$issue_no=$data[1];
	$from_date =trim($data[2]);
	$to_date =trim($data[3]);
	$issue_id =trim($data[4]);
	
	if($db_type==0)
	{
		$from_date=change_date_format($from_date,'YYYY-MM-DD');
		$to_date=change_date_format($to_date,'YYYY-MM-DD');
	}
	else
	{
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
	}
	
	//echo $store_id.jahid;die;
	

	$search_field_cond="";
	if($company_id!=0)
	{
		$search_field_cond.=" and a.company_id=$company_id";
	} 
	else
	{
		echo "Select Company First .";die;
	}
	
	if($issue_no!="") $search_field_cond.=" and a.issue_number like '%$issue_no%'";
	if($from_date!="" && $to_date!="") $search_field_cond.=" and a.issue_date between '$from_date' and '$to_date'";
	if($issue_id) $search_field_cond.=" and a.id = '$issue_id'";
	//echo $search_field_cond;die;
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$sql="SELECT a.id, a.issue_number, a.knit_dye_source, a.knit_dye_company, a.issue_purpose, a.batch_no, a.issue_date, a.company_id, b.location_id
	FROM inv_issue_master a, lib_store_location b
	WHERE a.store_id=b.id and a.entry_form in(71) and a.status_active=1 and a.is_deleted=0  $search_field_cond order by a.id desc";

	//echo $sql;//die;
	$result = sql_select($sql);

	if(!empty($result)){
		$variable_inventory_arr=return_library_array("select company_name, store_method from variable_settings_inventory  where item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 ","company_name","store_method");
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table">
		<thead>
			<th width="50">SL</th>
			<th width="140">Issue Number</th>
			<th width="120">Dyeing Source</th>
			<th width="150">Dyeing Company</th>
			<th width="150">Issue Purpose</th>
			<th width="100">Batch No</th>
			<th >Issue Date</th>
		</thead>
	</table>
	<div style="width:860px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="tbl_issue_list">  
			<?
			$i=1;
			foreach ($result as $row)
			{  
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$store_update_upto = $variable_inventory_arr[$row[csf('company_id')]];	
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $row[csf('issue_number')]; ?>,<? echo $row[csf('company_id')]; ?>,<? echo $row[csf('id')]; ?>,<? echo $row[csf('location_id')]; ?>,<? echo $store_update_upto;?>')"> 
					<td width="50" align="center"><? echo $i; ?></td>
					<td width="140"><p><? echo $row[csf('issue_number')]; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $knitting_source[$row[csf('knit_dye_source')]]; ?>&nbsp;</p></td>
					<td width="150"><p><? if($row[csf('knit_dye_source')]==1) echo $company_arr[$row[csf('knit_dye_company')]]; else echo $supplier_arr[$row[csf('knit_dye_company')]]; ?>&nbsp;</p></td>
					<td width="150"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?>&nbsp;</p></td>
					<td width="100" align="center"><p><? echo $row[csf('batch_no')]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo change_date_format($row[csf('issue_date')]); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?	
	exit();
}


if($action=="grey_issue_return_print")
{
	echo load_html_head_contents("Finish Roll Issue Return","../../../", 1, 1, $unicode,'',''); 
	extract($_REQUEST);
	$data=explode('*',$data);
	
	$company=$data[0];
	$txt_issue_no=$data[1];
	$update_id=$data[2];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}
	
	
	$store_library=return_library_array("select id, store_name from  lib_store_location","id","store_name");
	$supplier_library=return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_library=return_library_array("select id, short_name from lib_buyer","id","short_name");
	$brand_library=return_library_array("select id, brand_name from lib_brand","id","brand_name");
	$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
	$lib_mc_arr=array();
	$mc_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach($mc_sql as $row)
	{
		$lib_mc_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$issue_return_sql = sql_select("SELECT c.company_id, c.recv_number, c.store_id, c.receive_date, c.challan_no, a.po_breakdown_id, a.booking_without_order, a.barcode_no, b.remarks, d.issue_number, d.issue_purpose, d.knit_dye_source,d.knit_dye_company, a.qnty, e.batch_no 
from pro_roll_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c, inv_issue_master d, pro_batch_create_mst e
where a.dtls_id=b.id and b.mst_id=c.id and c.issue_id=d.id and b.batch_id=e.id and a.entry_form in(126) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$update_id");

	foreach($issue_return_sql as $row)
	{
		if($row[csf('booking_without_order')]==0){
			$all_po_arr[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
		$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']=$row[csf('po_breakdown_id')];
		$issue_rtn_barcode_no.=$row[csf("barcode_no")].",";

		$issue_data_array[$row[csf("barcode_no")]]['qnty']= $row[csf('qnty')];
		$issue_data_array[$row[csf("barcode_no")]]['issue_number']= $row[csf('issue_number')];
		$issue_data_array[$row[csf("barcode_no")]]['issue_purpose']= $row[csf('issue_purpose')];
		$issue_data_array[$row[csf("barcode_no")]]['knit_dye_source']= $row[csf('knit_dye_source')];
		$issue_data_array[$row[csf("barcode_no")]]['knit_dye_company']= $row[csf('knit_dye_company')];
		$issue_data_array[$row[csf("barcode_no")]]['batch_no']= $row[csf('batch_no')];
		$issue_data_array[$row[csf("barcode_no")]]['remarks']= $row[csf('remarks')];
		$issue_data_array[$row[csf("barcode_no")]]['po_breakdown_id']= $row[csf('po_breakdown_id')];
	}

	$issue_rtn_barcode_no=implode(",",array_unique(explode(",",chop($issue_rtn_barcode_no,","))));
	
	if(!empty($all_po_arr))
	{
		$data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.file_no, b.grouping as int_ref_no FROM wo_po_details_master a, wo_po_break_down b WHERE a.id=b.job_id and b.id in(".implode(',',$all_po_arr).")");
		$po_details_array=array();
		foreach($data_array as $row)
		{
			$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
			$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
			$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
			$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
			$po_details_array[$row[csf("po_id")]]['file_no']=$row[csf("file_no")];
			$po_details_array[$row[csf("po_id")]]['int_ref_no']=$row[csf("int_ref_no")];
		}
		
	}

	$dtls_data=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company,a.store_id, b.id as dtls_id, b.prod_id, b.body_part_id, b.fabric_description_id  as febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id,c.barcode_no,b.dia_width_type, c.id as roll_id, c.roll_no, c.po_breakdown_id as po_id, c.qnty, c.roll_id as roll_id_prev,c.rate, c.booking_no, c.booking_without_order, max(c.reprocess) as reprocess FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($issue_rtn_barcode_no) group by a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company,a.store_id, b.id, b.prod_id, b.body_part_id, b.fabric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id,c.barcode_no,b.dia_width_type, c.id, c.roll_no, c.po_breakdown_id, c.qnty, c.roll_id,c.rate,c.amount, c.booking_no, c.booking_without_order");
	?>
	<div>
		<table cellspacing="0" border="0">
			<tr>
				<td colspan="10" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
		</tr>

		<tr>
			<td colspan="10" align="center" style="font-size:12px">
				<?
				$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company"); 
				foreach ($nameArray as $result)
				{ 
					
					echo $result[csf('city')];
				}
				?></td>
			</tr>
			<tr>
				<td colspan="10" align="center" style="font-size:16px; font-weight:bold">Finish Roll Issue Return Challan</td>
			</tr>
			<tr>
				<td colspan="10">&nbsp;</td>
			</tr>
			<tr>
				<td width="70"><strong>Company:</strong></td>
				<td width="120"><? echo $company_array[$issue_return_sql[0][csf('company_id')]]['shortname']; ?></td>
				<td width="125"><strong>Issue Return ID:</strong></td>
				<td width="160"><? echo $issue_return_sql[0][csf('recv_number')]; ?></td>
				<td width="100"><strong>Store Name:</strong></td>
				<td width="160" ><? echo $store_library[$issue_return_sql[0][csf('store_id')]]; ?></td>
				<td width="100"></td>
				<td width="120"></td>
			</tr>
			<tr>
				<td >Source : </td>
				<td ><? echo $knitting_source[$issue_return_sql[0][csf('knit_dye_source')]]; ?></td>
				<td >Service Company : </td>
				<td >
					<?
					echo $company_array[$issue_return_sql[0][csf('knit_dye_company')]]['name'];
					if($issue_return_sql[0][csf('knit_dye_source')]==1){echo $company_array[$issue_return_sql[0][csf('knit_dye_company')]]['shortname'];}
					else{ echo $supplier_library[$issue_return_sql[0][csf('knit_dye_company')]];} ?>
				</td>
				<td ><strong> Issue Purpose:</strong></td>
				<td ><? echo $yarn_issue_purpose[$issue_return_sql[0][csf('issue_purpose')]]; ?></td>
				<td ><strong>Receive Date:</strong></td>
				<td ><? echo change_date_format($issue_return_sql[0][csf('receive_date')]); ?></td>
				<td><strong>Challan No:</strong></td>
				<td ><? echo $issue_return_sql[0][csf('challan_no')]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1350"  border="1" rules="all" class="rpt_table" >
			<thead>
				<th width="20">SL</th>
				<th width="70">Barcode No</th>
				<th width="70">Product Id</th>
				<th width="70">Batch No</th>
				<th width="100">Order <br />File/ Reference</th>
				<th width="75">Buyer <br />Job</th>
				<th width="80">Basis</th>
				<th width="100">Issue No.</th>
				<th width="60">Dyeing Company</th>
				<th width="100">Body Part</th>
				<th width="100">Construction/ Composition</th>
				<th width="60">Width Type</th>
				<th width="40">Fin GSM</th>
				<th width="40">Fab. Dia</th>
				<th width="100">Fab Color</th>
				<th width="50">Roll No</th>
				<th>Issue Return Qty</th>
				<th width="100">Remarks</th>
			</thead>
			<tbody>
				<?
				$i=0;
				$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
				foreach($dtls_data as $row)
				{


					//$issue_purpose = $receive_basis_arr[$issue_data_array[$row[csf("barcode_no")]]['issue_purpose']];
					$issue_number = $issue_data_array[$row[csf("barcode_no")]]['issue_number'];
					$knit_dye_source = $issue_data_array[$row[csf("barcode_no")]]['knit_dye_source'];
					$knit_dye_company = $issue_data_array[$row[csf("barcode_no")]]['knit_dye_company'];
					$batch_no = $issue_data_array[$row[csf("barcode_no")]]['batch_no'];
					$remarks = $issue_data_array[$row[csf("barcode_no")]]['remarks'];
					$po_breakdown_id = $issue_data_array[$row[csf("barcode_no")]]['po_breakdown_id'];
					$receive_basis = $receive_basis_arr[$row[csf("receive_basis")]];

					if($row[csf("knitting_source")]==1 )
					{
						$knitting_company = $company_array[$row[csf('knitting_company')]]['shortname'];
					}
					else if($row[csf("knitting_source")]==3)
					{
						$knitting_company =  $supplier_library[$row[csf("knitting_company")]];
					}
					$i++;
					?>
					<tr>
						<td align="center"><? echo $i; ?></td>
						<td><p><? echo $row[csf("barcode_no")]; ?></p></td>
						<td><p><? echo $row[csf("prod_id")]; ?></p></td>
						<td><p><? echo $batch_no; ?></p></td>
						<td><p><? echo $po_details_array[$po_breakdown_id]['po_number']."<br> F: ".$po_details_array[$po_breakdown_id]['file_no']." R: ".$po_details_array[$po_breakdown_id]['int_ref_no']; ?></p></td>
						<td><p><? echo $buyer_library[$po_details_array[$po_breakdown_id]['buyer_name']]. "<br>".$po_details_array[$po_breakdown_id]['job_no']; ?></p></td>
						<td><p><? echo $receive_basis; ?></p></td>
						<td><p><? echo $issue_number; ?></p></td>
						<td><p>
							<? 
							echo $knitting_company; 
							?></p>
						</td>
						<td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						<td><p><? echo $constructtion_arr[$row[csf('febric_description_id')]].', '.$composition_arr[$row[csf('febric_description_id')]]; ?></p></td>
							<td><p><? echo $fabric_typee[$row[csf("dia_width_type")]]; ?></p></td>
							<td><p><? echo $row[csf('gsm')]; ?></p></td>
							<td><p><? echo $row[csf('width')]; ?></p></td>                    

							<td>
								<p>
								<?
								$color_library[$row[csf("color_id")]];

								$all_color_arr=array_unique(explode(",",$row[csf('color_id')]));
								$all_color="";
								foreach($all_color_arr as $color_id)
								{
									$all_color.=$color_library[$color_id].",";
								}
								$all_color=chop($all_color,",");

								echo $all_color;
								?>
								</p>
								</td>
								<td align="center"><? echo $row[csf("roll_no")];?></td>
								<td align="right"><? 
								//echo number_format($row[csf('qnty')],2,'.','');
								echo number_format($issue_data_array[$row[csf("barcode_no")]]['qnty'],2,'.','');
								?></td>
								<td align="right"><? echo $remarks;	?></td>
							</tr>
							<?
							$tot_qty+=$issue_data_array[$row[csf("barcode_no")]]['qnty'];

						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th align="right" colspan="16"><strong>Total</strong></th>
							<th align="right"><? echo number_format($tot_qty,2,'.',''); ?></th>
							<th align="right" ><strong></strong></th>
						</tr>
					</tfoot>
				</table>
			</div>


			<? echo signature_table(112, $company, "1250px"); ?>


			<?
			exit();
}

if($action=="grey_issue_return_print_________________")
{
	echo load_html_head_contents("Finish Roll Issue Return","../../../", 1, 1, $unicode,'',''); 
	extract($_REQUEST);
	$data=explode('*',$data);
	
	$company=$data[0];
	$txt_issue_no=$data[1];
	$update_id=$data[2];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}
	
	
	$store_library=return_library_array("select id, store_name from  lib_store_location","id","store_name");
	$supplier_library=return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_library=return_library_array("select id, short_name from lib_buyer","id","short_name");
	$brand_library=return_library_array("select id, brand_name from lib_brand","id","brand_name");
	$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
	$lib_mc_arr=array();
	$mc_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach($mc_sql as $row)
	{
		$lib_mc_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}
	
	$data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.file_no, b.grouping as int_ref_no FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['file_no']=$row[csf("file_no")];
		$po_details_array[$row[csf("po_id")]]['int_ref_no']=$row[csf("int_ref_no")];
	}
	

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$scanned_barcode_update_data=sql_select("select a.id as roll_upid, a.po_breakdown_id, a.booking_without_order, a.barcode_no, a.roll_id, a.dtls_id, b.floor, b.room, b.rack_no, b.shelf_no, b.bin from pro_roll_details a, pro_finish_fabric_rcv_dtls b where a.dtls_id=b.id and a.entry_form in(126) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$update_id");
	foreach($scanned_barcode_update_data as $row)
	{
		$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']=$row[csf('po_breakdown_id')];
	}
	
	$issue_return_data=sql_select("select a.id, a.recv_number, a.company_id, a.store_id, a.receive_date, a.challan_no,c.barcode_no from inv_receive_master a,  pro_roll_details c where a.id=c.mst_id  and a.id=$update_id");
	$issue_rtn_barcode_no="";
	foreach($issue_return_data as $row)
	{
		$issue_rtn_barcode_no.=$row[csf("barcode_no")].",";
	}
	
	$issue_rtn_barcode_no=implode(",",array_unique(explode(",",chop($issue_rtn_barcode_no,","))));
	
	
	
	$dtls_data=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, b.id as dtls_id, b.prod_id as prod_id, b.body_part_id as body_part_id, b.febric_description_id as febric_description_id, b.machine_no_id as machine_no_id, b.gsm as gsm, b.width as width, b.color_id as color_id, b.color_range_id as color_range_id, b.yarn_lot as yarn_lot, b.yarn_count as yarn_count, b.stitch_length as stitch_length, b.brand_id as brand_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id as po_id, c.qnty, c.rate, c.amount
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_id=0 and c.barcode_no in($issue_rtn_barcode_no)");
	


	//$dataArray=sql_select("select id, recv_number, company_id, store_id, receive_date, challan_no from inv_receive_master where id=$update_id");
	

	//---------------------------------- issue info emergency ana hoycay, kono bul thaklay ok korun.

	
	$batch_name_array=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	
	$sql = "select a.issue_basis,a.batch_no,a.issue_number_prefix_num, a.issue_number, a.knit_dye_source, a.knit_dye_company, a.issue_purpose,a.item_color,c.barcode_no from inv_issue_master a,inv_finish_fabric_issue_dtls b,pro_roll_details c where a.id=b.mst_id and  a.id=c.mst_id and  b.mst_id=c.mst_id and c.barcode_no in ($issue_rtn_barcode_no)  and a.entry_form=71 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company"; 
	
	//echo $sql;
	$issue_data_arr=sql_select($sql);
	foreach($issue_data_arr as $row){
		$issue_data[$row[csf('barcode_no')]]['issue_number']=$row[csf('issue_number')];
	}
	
	// echo "select a.id,a.barcode_no, b.prod_id,a.qc_pass_qnty, b.floor, b.room, b.rack_no, b.shelf_no, b.bin_box  from pro_roll_details a,inv_finish_fabric_issue_dtls b, inv_receive_master c where a.dtls_id=b.id and a.entry_form=71 and a.status_active=1 and a.is_deleted=0 and a.roll_used=1 and a.barcode_no in($issue_rtn_barcode_no) and b.mst_id=c.issue_id and c.entry_form=126
	// and c.company_id=$company ";
	$issue_result_data=sql_select("select a.barcode_no, a.qc_pass_qnty from pro_roll_details a,inv_finish_fabric_issue_dtls b, inv_receive_master c where a.dtls_id=b.id and a.entry_form=71 and a.status_active=1 and a.is_deleted=0 and a.roll_used=1 and a.barcode_no in($issue_rtn_barcode_no) and b.mst_id=c.issue_id and c.entry_form=126
		and c.company_id=$company ");

	$issue_data_array = array();
	foreach($issue_result_data as $row)
	{
		$issue_data_array[$row[csf("barcode_no")]]['qnty'] =$row[csf("qc_pass_qnty")];
	}
	unset($issue_result_data);
	

	$sql="SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id,
	a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id,
	b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id as roll_id, c.roll_no, c.po_breakdown_id,c.qnty
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0  and c.barcode_no in ($issue_rtn_barcode_no) and a.company_id=$company
	and a.entry_form in(37,7,68,66) and c.entry_form in(37,7,68,66) and c.status_active=1 and c.is_deleted=0";

		  //echo $sql;die;
	$data_array=sql_select($sql);
	$roll_details_array=array();
	foreach($data_array as $row)
	{
		if(str_replace("'","",$row[csf('entry_form')])!=66){

			$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$body_part[$row[csf("body_part_id")]];
			$roll_details_array[$row[csf("barcode_no")]]['receive_basis']=$receive_basis_arr[$row[csf("receive_basis")]];
			$roll_details_array[$row[csf("barcode_no")]]['receive_date']=change_date_format($row[csf("receive_date")]);
			if(str_replace("'","",$row[csf('entry_form')])==68)
			{
				$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("recv_number")];
			}
			else
			{
				$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];
			}
			$roll_details_array[$row[csf("barcode_no")]]['color']=$color_library[$row[csf("color_id")]];
			$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
			$roll_details_array[$row[csf("barcode_no")]]['dia_width_type']=$fabric_typee[$row[csf("dia_width_type")]];
			$roll_details_array[$row[csf("barcode_no")]]['body_part']=$body_part[$row[csf("body_part_id")]];
			$roll_details_array[$row[csf("barcode_no")]]['batch_id']=$row[csf("batch_id")];
			$roll_details_array[$row[csf("barcode_no")]]['batch_name']=$batch_name_array[$row[csf("batch_id")]];

			$roll_details_array[$row[csf("barcode_no")]]['trans_id']=$row[csf("trans_id")];
			$roll_details_array[$row[csf("barcode_no")]]['dtls_id']=$row[csf("dtls_id")];
			$roll_details_array[$row[csf("barcode_no")]]['prod_id']=$row[csf("prod_id")];
			$roll_details_array[$row[csf("barcode_no")]]['deter_d']=$row[csf("fabric_description_id")];
			$roll_details_array[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
			$roll_details_array[$row[csf("barcode_no")]]['width']=$row[csf("width")];
			$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
			$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
			$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
			$roll_details_array[$row[csf("barcode_no")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
			$barcode_array[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		}



		if($row[csf("knitting_source")]==1 && str_replace("'","",$row[csf('entry_form')])==66)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_array[$row[csf('knitting_company')]]['shortname'];
		}
		else if($row[csf("knitting_source")]==3 && str_replace("'","",$row[csf('entry_form')])==66)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$supplier_library[$row[csf("knitting_company")]];
		}


	}


 //var_dump($company_array);


	?>
	<div>
		<table cellspacing="0" border="0">
			<tr>
				<td colspan="10" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
		</tr>

		<tr>
			<td colspan="10" align="center" style="font-size:12px">
				<?
				$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company"); 
				foreach ($nameArray as $result)
				{ 
					
					echo $result[csf('city')];
				}
				?></td>
			</tr>
			<tr>
				<td colspan="10" align="center" style="font-size:16px; font-weight:bold">Finish Roll Issue Return Challan</td>
			</tr>
			<tr>
				<td colspan="10">&nbsp;</td>
			</tr>
			<tr>
				<td width="70"><strong>Company:</strong></td>
				<td width="120"><? echo $company_array[$issue_return_data[0][csf('company_id')]]['shortname']; ?></td>
				<td width="125"><strong>Issue Return ID:</strong></td>
				<td width="160"><? echo $issue_return_data[0][csf('recv_number')]; ?></td>
				<td width="100"><strong>Store Name:</strong></td>
				<td width="160" ><? echo $store_library[$issue_return_data[0][csf('store_id')]]; ?></td>
				<td width="100"></td>
				<td width="120"></td>
			</tr>
			<tr>
				<td >Source : </td>
				<td ><? echo $knitting_source[$issue_data_arr[0][csf('knit_dye_source')]]; ?></td>
				<td >Service Company : </td>
				<td >
					<? 

					echo $company_array[$issue_data_arr[0][csf('knit_dye_company')]]['name'];
					if($issue_data_arr[0][csf('knit_dye_source')]==1){echo $company_array[$issue_data_arr[0][csf('knit_dye_company')]]['shortname'];}
					else{ echo $supplier_library[$issue_data_arr[0][csf('knit_dye_company')]];} ?>
				</td>
				<td ><strong> Issue Purpose:</strong></td>
				<td ><? echo $yarn_issue_purpose[$issue_data_arr[0][csf('issue_purpose')]]; ?></td>
				<td ><strong>Receive Date:</strong></td>
				<td ><? echo change_date_format($issue_return_data[0][csf('receive_date')]); ?></td>
				<td><strong>Challan No:</strong></td>
				<td ><? echo $issue_return_data[0][csf('challan_no')]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1250"  border="1" rules="all" class="rpt_table" >
			<thead>
				<th width="20">SL</th>
				<th width="70">Barcode No</th>
				<th width="70">Product Id</th>
				<th width="70">Batch No</th>
				<th width="100">Order <br />File/ Reference</th>
				<th width="75">Buyer <br />Job</th>
				<th width="80">Basis</th>
				<th width="100">Issue No.</th>
				<th width="60">Dyeing Company</th>
				<th width="100">Body Part</th>
				<th width="100">Construction/ Composition</th>
				<th width="60">Width Type</th>
				<th width="40">Fin GSM</th>
				<th width="40">Fab. Dia</th>
				<th width="100">Fab Color</th>
				<th width="50">Roll No</th>
				<th>Issue Return Qty</th>
			</thead>
			<tbody>
				<?
				$i=0;
				$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
				foreach($dtls_data as $row)
				{
					$i++;
					?>
					<tr>
						<td align="center"><? echo $i; ?></td>
						<td><p><? echo $row[csf("barcode_no")]; ?></p></td>
						<td><p><? echo $roll_details_array[$row[csf("barcode_no")]]['prod_id']; ?></p></td>
						<td><p><? echo $roll_details_array[$row[csf("barcode_no")]]['batch_name']; ?></p></td>
						<td><p><? echo $po_details_array[$row[csf("po_id")]]['po_number']."<br> F: ".$po_details_array[$row[csf("po_id")]]['file_no']." R: ".$po_details_array[$row[csf("po_id")]]['int_ref_no']; ?></p></td>
						<td><p><? echo $buyer_library[$po_details_array[$row[csf("po_id")]]['buyer_name']]. "<br>".$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['job_no']; ?></p></td>
						<td><p><? echo $roll_details_array[$row[csf("barcode_no")]]['receive_basis']; ?></p></td>
						<td><p><? echo $roll_details_array[$row[csf("barcode_no")]]['booking_no']; ?></p></td>
						<td><p>
							<? 
							echo $roll_details_array[$row[csf("barcode_no")]]['knitting_company']; 
							?></p></td>
							<td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
							<td><p><? echo $constructtion_arr[$row[csf('febric_description_id')]].', '.$composition_arr[$row[csf('febric_description_id')]]; ?></p></td>
							<td><p><? echo $roll_details_array[$row[csf("barcode_no")]]['dia_width_type']; ?></p></td>
							<td><p><? echo $row[csf('gsm')]; ?></p></td>
							<td><p><? echo $roll_details_array[$row[csf("barcode_no")]]['width'];//$lib_mc_arr[$row[csf("machine_no_id")]]['dia']; ?></p></td>                    

							<td><p>
								<? 
								$all_color_arr=array_unique(explode(",",$row[csf('color_id')]));
								$all_color="";
								foreach($all_color_arr as $color_id)
								{
									$all_color.=$color_library[$color_id].",";
								}
								$all_color=chop($all_color,",");
					//echo $all_color;
								echo $roll_details_array[$row[csf("barcode_no")]]['color'];
								?></p></td>
								<td align="center"><? echo $row[csf("roll_no")];?></td>
								<td align="right"><? 
								//echo number_format($row[csf('qnty')],2,'.','');
								echo number_format($issue_data_array[$row[csf("barcode_no")]]['qnty'],2,'.','');
								?></td>
							</tr>
							<?
							$tot_qty+=$issue_data_array[$row[csf("barcode_no")]]['qnty'];

						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th align="right" colspan="16"><strong>Total</strong></th>
							<th align="right"><? echo number_format($tot_qty,2,'.',''); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>


			<? echo signature_table(112, $company, "1250px"); ?>


			<?
			exit();
}



		if($action=="grey_issue_return_print_grouping")
		{
			echo load_html_head_contents("Grey Fabric Transfer Entry Report","../../../", 1, 1, $unicode,'',''); 
			extract($_REQUEST);
			$data=explode('*',$data);

			$company=$data[0];
			$txt_issue_no=$data[1];
			$update_id=$data[2];

			$company_array=array();
			$company_data=sql_select("select id, company_name, company_short_name from lib_company where id=$company");
			foreach($company_data as $row)
			{
				$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
				$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
			}
			$store_library=return_library_array("select id, store_name from  lib_store_location","id","store_name");
			$supplier_library=return_library_array("select id, short_name from lib_supplier","id","short_name");
			$buyer_library=return_library_array("select id, short_name from lib_buyer","id","short_name");
			$brand_library=return_library_array("select id, brand_name from lib_brand","id","brand_name");
			$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
			$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
			$lib_mc_arr=array();
			$mc_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
			foreach($mc_sql as $row)
			{
				$lib_mc_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
				$lib_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
				$lib_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
			}

			$data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.file_no, b.grouping as int_ref_no FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
			$po_details_array=array();
			foreach($data_array as $row)
			{
				$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
				$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
				$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
				$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
				$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
				$po_details_array[$row[csf("po_id")]]['file_no']=$row[csf("file_no")];
				$po_details_array[$row[csf("po_id")]]['int_ref_no']=$row[csf("int_ref_no")];
			}

	/*$product_array=array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id']=$row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];
	}*/
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	// Do save data dtls in pro_grey_prod_entry_dtls table
	/*$issue_return_barcode_sql=sql_select("select c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.id=$update_id");*/
	$issue_return_barcode_sql=sql_select("select c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=c.mst_id  and a.id=$update_id");
	$issue_rtn_barcode_no="";
	foreach($issue_return_barcode_sql as $row)
	{
		$issue_rtn_barcode_no.=$row[csf("barcode_no")].",";
	}
	
	$issue_rtn_barcode_no=implode(",",array_unique(explode(",",chop($issue_rtn_barcode_no,","))));
	if($db_type==0)
	{
		$dtls_data=sql_select("SELECT  a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, b.febric_description_id as febric_description_id, b.machine_no_id as machine_no_id, b.gsm as gsm, b.width as width, b.color_id as color_id, b.color_range_id as color_range_id, b.yarn_lot as yarn_lot, b.yarn_count as yarn_count, b.stitch_length as stitch_length, b.brand_id as brand_id, count(c.id) as total_roll, group_concat(c.id) as roll_id, group_concat(c.roll_no) as roll_no, c.po_breakdown_id as po_id, sum(c.qnty) as qnty, sum(c.amount) as amount
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
			WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($issue_rtn_barcode_no)
			group by a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, c.po_breakdown_id");
	}
	else
	{
		$dtls_data=sql_select("SELECT  a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, b.febric_description_id as febric_description_id, b.machine_no_id as machine_no_id, b.gsm as gsm, b.width as width, b.color_id as color_id, b.color_range_id as color_range_id, b.yarn_lot as yarn_lot, b.yarn_count as yarn_count, b.stitch_length as stitch_length, b.brand_id as brand_id, count(c.id) as total_roll, listagg(cast(c.id as varchar(4000)),',') within group (order by c.id) as roll_id, listagg(cast(c.roll_no as varchar(4000)),',') within group (order by c.roll_no) as roll_no, c.po_breakdown_id as po_id, sum(c.qnty) as qnty, sum(c.amount) as amount
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
			WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($issue_rtn_barcode_no)
			group by a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, c.po_breakdown_id");
	}
	
	
	
	$dataArray=sql_select("select id, recv_number, company_id, store_id, receive_date, challan_no from inv_receive_master where id=$update_id");
	?>
	<div>
		<table width="1000" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>

			<tr>
				<td colspan="6" align="center" style="font-size:12px">
					<? 
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company"); 
					foreach ($nameArray as $result)
					{ 
						echo $result[csf('city')];
					}
					?></td>
				</tr>            
				<tr>
					<td colspan="6" align="center" style="font-size:16px; font-weight:bold">Finish Roll Issue Return Challan</td>
				</tr>
				<tr>
					<td colspan="6">&nbsp;</td>
				</tr>
				<tr>
					<td width="130"><strong>System No:</strong></td>
					<td width="180"><? echo $dataArray[0][csf('recv_number')]; ?></td>
					<td width="130"><strong>Company:</strong></td>
					<td width="180"><? echo $company_array[$dataArray[0][csf('company_id')]]['shortname']; ?></td>
					<td width="130"><strong>Store Name:</strong></td>
					<td ><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
				</tr>
				<tr>
					<td ><strong>Receive Date:</strong></td>
					<td ><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
					<td><strong>Challan No:</strong></td>
					<td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
				</tr>
			</table>
			<br>
			<table cellspacing="0" width="1380"  border="1" rules="all" class="rpt_table" >
				<thead>
					<th width="20">SL</th>
					<th width="100">Order <br />File/ Reference</th>
					<th width="75">Buyer <br />Job</th>
					<th width="80">Program/ Booking No</th>
					<th width="90">Production Basis</th>
					<th width="60">Knitting Company</th>
					<th width="50">Yarn Count</th>
					<th width="90">Yarn Brand</th>
					<th width="50">Lot No</th>
					<th width="100">Fab Color</th>
					<th width="80">Color Range</th>
					<th width="260">Fabric Type</th>
					<th width="40">Stich Lenth</th>
					<th width="40">Fin GSM</th>
					<th width="40">Fab. Dia</th>
					<th width="60">MC No <br />Dia</th>
					<th width="50">Total Roll</th>
					<th>Issue Return Qty</th>
				</thead>
				<tbody>
					<?
					$i=0;
					$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
					foreach($dtls_data as $row)
					{
						$i++;
						?>
						<tr>
							<td align="center"><? echo $i; ?></td>
							<td style="word-break:break-all;"><? echo $po_details_array[$row[csf("po_id")]]['po_number']."<br> F: ".$po_details_array[$row[csf("po_id")]]['file_no']." R: ".$po_details_array[$row[csf("po_id")]]['int_ref_no']; ?></td>
							<td style="word-break:break-all;"><? echo $buyer_library[$po_details_array[$row[csf("po_id")]]['buyer_name']]. "<br>".$po_details_array[$row[csf("po_id")]]['job_no']; ?></td>
							<td style="word-break:break-all;" align="center"><? echo $row[csf('booking_no')]; ?></td>

							<td style="word-break:break-all;" align="center"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
							<td style="word-break:break-all;" align="center">
								<? 
								if($row[csf('knitting_source')]==1) echo $company_array[$row[csf('knitting_company')]]['shortname']; 
								else echo  $supplier_library[$row[csf('knitting_company')]]; 
								?></td>
								<td style="word-break:break-all;" align="center">
									<? 
					//echo $yarn_count_arr[$row[csf('y_count')]]; 
									$yarn_count_array=array_unique(explode(",",$row[csf('yarn_count')]));
									$all_count="";
									foreach($yarn_count_array as $count_id)
									{
										$all_count.=$yarn_count_arr[$count_id].",";
									}
									$all_count=chop($all_count,",");
									echo $all_count;
									?></td>
									<td style="word-break:break-all;"><?  echo $brand_library[$row[csf("brand_id")]]; ?></td>
									<td style="word-break:break-all;" align="center"><? echo $row[csf('yarn_lot')]; ?></td>
									<td style="word-break:break-all;">
										<? 
										$all_color_arr=array_unique(explode(",",$row[csf('color_id')]));
										$all_color="";
										foreach($all_color_arr as $color_id)
										{
											$all_color.=$color_library[$color_id].",";
										}
										$all_color=chop($all_color,",");
										echo $all_color;
										?></td>
										<td style="word-break:break-all;" align="center"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
										<td style="word-break:break-all;"><? echo $constructtion_arr[$row[csf('febric_description_id')]]." ".$composition_arr[$row[csf('febric_description_id')]]; ?></td>
										<td style="word-break:break-all;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
										<td style="word-break:break-all;"  align="center"><? echo $row[csf('gsm')]; ?></td>
										<td style="word-break:break-all;" align="center"><? echo $row[csf("width")]; ?></td>
										<td style="word-break:break-all;"><? echo "N :".$lib_mc_arr[$row[csf("machine_no_id")]]['no'].'<br />D :'.$lib_mc_arr[$row[csf("machine_no_id")]]['dia']; ?></td>
										<td align="center"><? echo $row[csf("total_roll")];?></td>
										<td align="right"><? echo number_format($row[csf('qnty')],2,'.','');?></td>
									</tr>
									<?
									$tot_qty+=$row[csf('qnty')];

								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th align="right" colspan="17"><strong>Total</strong></th>
									<th align="right"><? echo number_format($tot_qty,2,'.',''); ?></th>
								</tr>
							</tfoot>
						</table>
					</div>
					<?
					exit();
				}
				?>
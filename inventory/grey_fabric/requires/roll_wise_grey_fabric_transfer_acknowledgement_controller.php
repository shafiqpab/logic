<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$action_from = $_REQUEST['action_from'];

if ($action=="load_room_rack_self_bin")
{
	//print_r($_REQUEST);
	load_room_rack_self_bin($action_from,$data);
	die;
}

if($action=="load_drop_store_to")
{
	$data= explode("_", $data);
	echo create_drop_down( "cbo_store_name", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13 and a.company_id=$data[1] and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "fnc_details_row_blank();fn_load_floor(this.value);" );
}

if($action=="load_drop_store_balnk")
{
	echo create_drop_down( "cbo_store_name", 152, $blank_array,"", 1, "--Select store--", 0, "" );
}

if($action=="floor_list")
{
	$data_ref=explode("__",$data);
	$floor_arr=array();

	if($data_ref[0] && $data_ref[1])
	{
		$floor_data=sql_select("select b.floor_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
		group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name");
		foreach($floor_data as $row)
		{
			$floor_arr[$row[csf('floor_id')]]=$row[csf('floor_room_rack_name')];
		}
	}
	$jsFloor_arr= json_encode($floor_arr);
	echo $jsFloor_arr;
	die();
}

if($action=="room_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$room_arr=array();
	$location_cond = "";
	if($data_ref[1]) $location_cond = "and b.location_id='$data_ref[1]'";
	$room_data=sql_select("select b.room_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.floor_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($room_data as $row)
	{
		$room_arr[$row[csf('floor_room_rack_name')]]=$row[csf('room_id')];
	}
	$jsRoom_arr= json_encode($room_arr);
	echo $jsRoom_arr;
	die();
}

if($action=="rack_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$rack_arr=array();
	$location_cond = "";
	if($data_ref[1]) $location_cond = "and b.location_id='$data_ref[1]'";
	$rack_data=sql_select("select b.rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.room_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($rack_data as $row)
	{
		$rack_arr[$row[csf('floor_room_rack_name')]]=$row[csf('rack_id')];
	}
	$jsRack_arr= json_encode($rack_arr);
	echo $jsRack_arr;
	die();
}

if($action=="shelf_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$shelf_arr=array();
	$location_cond = "";
	if($data_ref[1]) $location_cond = "and b.location_id='$data_ref[1]'";
	$shelf_data=sql_select("select b.shelf_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.rack_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($shelf_data as $row)
	{
		$shelf_arr[$row[csf('floor_room_rack_name')]]=$row[csf('shelf_id')];
	}
	$jsShelf_arr= json_encode($shelf_arr);
	echo $jsShelf_arr;
	die();
}

if($action=="bin_list")
{
	$data_ref=explode("__",$data);
	$bin_arr=array();
	$location_cond = "";
	if($data_ref[1]) $location_cond = "and b.location_id='$data_ref[1]'";
	$bin_data=sql_select("select b.bin_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.shelf_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($bin_data as $row)
	{
		$bin_arr[$row[csf('floor_room_rack_name')]]=$row[csf('bin_id')];
	}
	$jsBin_arr= json_encode($bin_arr);
	echo $jsBin_arr;
	die();
}

if ($action=="load_drop_down_room")
{
	$ex_data = explode("_",$data);

	echo create_drop_down( "cboRoomTo_$ex_data[2]", 50, "select b.room_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.floor_id in($ex_data[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "-- Select --", "", "change_room(this.value,this.id)","","","","","","","","cboRoomTo[]","onchange_void");
	exit();
}

if ($action=="load_drop_down_rack")
{
	$ex_data = explode("_",$data);
	echo create_drop_down( "txtRackTo_$ex_data[2]", 50, "select b.rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and b.room_id in($ex_data[0]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "-- Select --", "", "change_rack(this.value,this.id)","","","","","","","","txtRackTo[]","onchange_void");
	exit();
}

if ($action=="load_drop_down_shelf")
{
	$ex_data = explode("_",$data);
	echo create_drop_down( "txtShelfTo_$ex_data[2]", 50, "select b.shelf_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and b.rack_id in($ex_data[0]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "-- Select --", "", "change_shelf(this.value,this.id)","","","","","","","","txtShelfTo[]","onchange_void");
	exit();
}

if ($action=="load_drop_down_bin")
{
	$ex_data = explode("_",$data);
	echo create_drop_down( "txtBinTo_$ex_data[2]", 50, "select b.bin_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and b.shelf_id in($ex_data[0]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "-- Select --", "", "","","","","","","","","txtBinTo[]","onchange_void");
	exit();
}

if($action=="load_drop_store_2")
{
	echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13  and a.status_active=1 and a.is_deleted=0 and  a.location_id=$data group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
}

if ($action=="requ_variable_settings")
{
	extract($_REQUEST);
	$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=13 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	echo '0**'.$variable_inventory;
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
    for($k=1;$k<=$tot_row;$k++)
    {
        $productId="productId_".$k;
        $prod_ids.=$$productId.",";
    }
    $prod_ids=implode(",",array_unique(explode(",",chop($prod_ids,','))));

    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($prod_ids) and transaction_type in (1,4,5)", "max_date");
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$trans_date = date("Y-m-d", strtotime(str_replace("'","",$txt_transfer_date)));
	if ($trans_date < $max_recv_date)
    {
        echo "20**Transfer Date Can not Be Less Than Last Receive Date Of These Lot";
        die;
	}

	if(str_replace("'","",$cbo_transfer_criteria)==2 || str_replace("'","",$cbo_transfer_criteria)==4)
	{
		$cbo_to_company_id = $cbo_company_id;
	}

	$store_arr_chk =sql_select("select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13 and a.status_active=1 and a.company_id=$cbo_to_company_id and a.id=$cbo_store_name");
	if(empty($store_arr_chk))
	{
		echo "20**To store not found in to company";
        die;
	}

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		for($x=1;$x<=$tot_row;$x++)
		{
			$barcodeNo="barcodeNo_".$x;
			$all_barcodeNo.=$$barcodeNo.",";
			$tot_rollWgt="rollWgt_".$x;
			$tot_rollWgt2+=$$tot_rollWgt;
		}
		$all_barcodeNo=chop($all_barcodeNo,',');

		if($db_type==0) $year_cond="YEAR(insert_date)";
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later

		if (str_replace("'","",$fso_yes_no_type)==1) 
		{
			$ack_entry_form=133;
		}
		else
		{
			$ack_entry_form=82;
		}
		$transfer_recv_num=''; $transfer_update_id='';
		if(str_replace("'","",$update_id)=="")
		{
			$id = return_next_id_by_sequence("INV_ITEM_TRANS_MST_AC_PK_SEQ", "inv_item_trans_acknowledgement", $con);
			$field_array="id, entry_form, challan_id, company_id, transfer_criteria, item_category, acknowledg_date, store_id, remarks, inserted_by, insert_date";

			$data_array="(".$id.",".$ack_entry_form.",".$txt_transfer_mst_id.",".$cbo_to_company_id.",".$cbo_transfer_criteria.",13,".$txt_transfer_date.",".$cbo_store_name.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			// echo "10**insert into inv_item_trans_acknowledgement (".$field_array.") values ".$data_array;die;

			$transfer_recv_num=$txt_transfer_mst_id;
			$transfer_ack_update_id=$id;
		}
		else
		{
			$field_array="acknowledg_date*store_id*remarks*updated_by*update_date";
			$data_array="".$txt_transfer_date."*".$cbo_store_name."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$transfer_recv_num=str_replace("'","",$txt_transfer_mst_id);
			$transfer_ack_update_id=str_replace("'","",$update_id);
		}

		$field_array_trans = "id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, cons_uom, transaction_date, cons_quantity, brand_id, store_id, floor_id, room, rack, self, bin_box, inserted_by, insert_date, body_part_id, order_id, cons_rate, cons_amount, program_no, stitch_length";

		$field_array_dtls_update="to_trans_id*to_store*to_floor_id*to_room*to_rack*to_shelf*to_bin_box*knit_program_id*prod_detls_id*updated_by*update_date";

		$field_array_prop="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";

		$field_array_prod_update = "current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";

		$totalRollId="";
		if(str_replace("'","",$cbo_transfer_criteria)==1) // Company to Company
		{
			for($j=1;$j<=$tot_row;$j++)
			{
				$recvBasis="recvBasis_".$j;
				$barcodeNo="barcodeNo_".$j;
				$progBookPiId="progBookPiId_".$j;
				$productId="productId_".$j;
				$orderId="orderId_".$j;
				$rollId="rollId_".$j;
				$rollWgt="rollWgt_".$j;
				$yarnLot="yarnLot_".$j;
				$yarnCount="yarnCount_".$j;
				$colorId="colorId_".$j;
				$stichLn="stichLn_".$j;
				$brandId="brandId_".$j;

				$fromStoreId="fromStoreId_".$j;
				$fromFloor="fromFloor_".$j;
				$fromRoom="fromRoom_".$j;
				$fromRack="fromRack_".$j;
				$fromShelf="fromShelf_".$j;
				$fromBin="fromBin_".$j;

				$toFloor="toFloor_".$j;
				$toRoom="toRoomId_".$j;
				$toRack="toRack_".$j;
				$toShelf="toShelf_".$j;
				$toBin="toBin_".$j;

				$rollNo="rollNo_".$j;				

				$febDescripId="febDescripId_".$j;
				$machineNoId="machineNoId_".$j;
				$gsm="gsm_".$j;
				$diaWidth="diaWidth_".$j;
				$knitDetailsId="knitDetailsId_".$j;
				$transferEntryForm="transferEntryForm_".$j;
				$bookWithoutOrder="bookWithoutOrder_".$j;
				$rollMstId="rollMstId_".$j;
				$totalRollId.=$$rollMstId.",";
				$toOrderId="toOrderId_".$j;
				$constructCompo="constructCompo_".$j;
				$rollAmount="rollAmount_".$j;
				$fromBookingWithoutOrder="fromBookingWithoutOrder_".$j;
				$requiDtlsId="requiDtlsId_".$j;
				$rolltableId="rolltableId_".$j;
				$rollIds.=$$rolltableId.",";
				$dtlsId="dtlsId_".$j;

				$fromBodyPart="fromBodyPart_".$j;
				$toBodyPart="toBodyPart_".$j;
				$consRate="consRate_".$j;
				$consAmount="consAmount_".$j;
				$programNo="programNo_".$j;

				if($$toOrderId=="") $toOrderIdRef=$$orderId; else $toOrderIdRef=$$toOrderId;

				$form_trans_id=$transactionID;

				$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$to_trans_id=$transactionID;
				
				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$transactionID.",".$txt_transfer_mst_id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_to_company_id.",".$$productId.",13,5,12,".$txt_transfer_date.",'".$$rollWgt."','".$$brandId."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$toBodyPart."','".$toOrderIdRef."','".$$consRate."','".$$consAmount."','".$$programNo."','".$$stichLn."')";

				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$transactionID.",5,".$ack_entry_form.",'".$$dtlsId."','".$toOrderIdRef."',".$$productId.",'".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				$dtls_id_array[]=$$dtlsId;
				$data_array_dtls_update[$$dtlsId]=explode("*",($to_trans_id."*".$cbo_store_name."*'".$$toFloor."'*'".$$toRoom."'*'".$$toRack."'*'".$$toShelf."'*'".$$toBin."'*'".$$progBookPiId."'*'".$$knitDetailsId."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				$product_qnty_update[str_replace("'", "",$$productId)]['qnty']+= str_replace("'", "", $$rollWgt);

				$all_to_prod_id.=$$productId.",";
				$all_roll_id.=$$rollId.",";
				// $roll_id_array_up[]=$$rolltableId;
			}
			// echo "10**".print_r($roll_id_array_up);die;
			$all_to_prod_id = chop($all_to_prod_id,",");
			$all_to_prod_id= array_filter(array_unique(explode(",", $all_to_prod_id)));
			if(!empty($all_to_prod_id))
			{
				$prod_id_array=array();
				$up_to_prod_ids=implode(",",$all_to_prod_id);
				
				//echo "10**select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($up_to_prod_ids) ";die;
				$toProdResult=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($up_to_prod_ids) ");
				foreach($toProdResult as $row)
				{
					$stock_qnty = $product_qnty_update[$row[csf("id")]]['qnty'] + $row[csf("current_stock")];
					$stock_value = $row[csf("avg_rate_per_unit")]*$stock_qnty;
					// if Qty is zero then rate & value will be zero
					if ($stock_qnty<=0) 
					{
						$stock_value=0;
						$row[csf("avg_rate_per_unit")]=0;
					}
					$prod_id_array[]=$row[csf('id')];
					$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$stock_qnty."'*'".$row[csf("avg_rate_per_unit")]."'*'".$stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
			}
		}
		else // Store to store and order to order 
		{
			for($j=1;$j<=$tot_row;$j++)
			{
				$recvBasis="recvBasis_".$j;
				$barcodeNo="barcodeNo_".$j;
				$progBookPiId="progBookPiId_".$j;
				$productId="productId_".$j;
				$orderId="orderId_".$j;
				$rollId="rollId_".$j;
				$rollWgt="rollWgt_".$j;
				$yarnLot="yarnLot_".$j;
				$yarnCount="yarnCount_".$j;
				$colorId="colorId_".$j;
				$stichLn="stichLn_".$j;
				$brandId="brandId_".$j;


				$toFloor="toFloor_".$j;
				$toRoom="toRoomId_".$j;
				$toRack="toRack_".$j;
				$toShelf="toShelf_".$j;
				$toBin="toBin_".$j;

				$rollNo="rollNo_".$j;
				$fromStoreId="fromStoreId_".$j;
				$fromFloor="fromFloor_".$j;
				$fromRoom="fromRoom_".$j;
				$fromRack="fromRack_".$j;
				$fromShelf="fromShelf_".$j;
				$fromBin="fromBin_".$j;

				$febDescripId="febDescripId_".$j;
				$machineNoId="machineNoId_".$j;
				$gsm="gsm_".$j;
				$diaWidth="diaWidth_".$j;
				$knitDetailsId="knitDetailsId_".$j;
				$transferEntryForm="transferEntryForm_".$j;
				$bookWithoutOrder="bookWithoutOrder_".$j;
				$rollMstId="rollMstId_".$j;
				$totalRollId.=$$rollMstId.",";
				$toOrderId="toOrderId_".$j;
				$fromBookingWithoutOrder="fromBookingWithoutOrder_".$j;
				$rolltableId="rolltableId_".$j;
				$rollIds.=$$rolltableId.",";
				$dtlsId="dtlsId_".$j;

				$fromBodyPart="fromBodyPart_".$j;
				$toBodyPart="toBodyPart_".$j;
				$consRate="consRate_".$j;
				$consAmount="consAmount_".$j;
				$programNo="programNo_".$j;

				if($$toOrderId=="") $toOrderIdRef=$$orderId; else $toOrderIdRef=$$toOrderId;

				//---------------------------------------------------------------------------		
				$form_trans_id=$transactionID;

				$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$to_trans_id=$transactionID;
				
				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$transactionID.",".$txt_transfer_mst_id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_to_company_id.",".$$productId.",13,5,12,".$txt_transfer_date.",'".$$rollWgt."','".$$brandId."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$toBodyPart."','".$toOrderIdRef."','".$$consRate."','".$$consAmount."','".$$programNo."','".$$stichLn."')";

				if(str_replace("'", "", $$bookWithoutOrder) !=1)
				{
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_prop!="") $data_array_prop.= ",";
					$data_array_prop.="(".$id_prop.",".$transactionID.",5,".$ack_entry_form.",'".$$dtlsId."','".$toOrderIdRef."',".$$productId.",'".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				//---------------------------------------------------------------------------------------
				$dtls_id_array[]=$$dtlsId;
				$data_array_dtls_update[$$dtlsId]=explode("*",($to_trans_id."*".$cbo_store_name."*'".$$toFloor."'*'".$$toRoom."'*'".$$toRack."'*'".$$toShelf."'*'".$$toBin."'*'".$$progBookPiId."'*'".$$knitDetailsId."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				$all_roll_id.=$$rollId.",";
			}
		}
		// echo "10** insert into order_wise_pro_details ($field_array_prop) values $data_array_prop";die;

		$rID=$rID2=$rID3=$rID4=$rID5=$rID6=$prodUpdate=$rollUpdate=$rID7_roll_re_transfer=$rID7=true;

		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_item_trans_acknowledgement",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_item_trans_acknowledgement",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}
		//echo "10**insert into inv_item_trans_acknowledgement (".$field_array.") values ".$data_array;die;

		//echo "10** insert into inv_transaction ($field_array_trans) values $data_array_trans";die;
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);

		if($data_array_prop != "")
		{
			$rID3=sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop,0);
			if($rID3) $flag=1; else $flag=0; 
		}

		$field_array_mst_update = "is_acknowledge";
		$data_array_mst_update  = "1";
		$rID4=sql_update("inv_item_transfer_mst",$field_array_mst_update,$data_array_mst_update,"id",$txt_transfer_mst_id,0);
		if($rID4) $flag=1; else $flag=0; 

		// echo "10**".bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $dtls_id_array );die;
		$rID5=execute_query(bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $dtls_id_array ));
		if($flag==1)
		{
			if($rID5) $flag=1; else $flag=0; 
		}

		$rollIds=chop($rollIds,',');
		$rID6=sql_multirow_update("pro_roll_details","re_transfer","0","id",$rollIds,0);
		if($flag==1) 
		{
			if($rID6) $flag=1; else $flag=0; 
		}

		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			// echo "10**".bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
			$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
		}

		//echo "10** insert into inv_item_transfer_dtls ($field_array_dtls) values $data_array_dtls";die;
		//echo "10**".bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
	  	// echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$rID6."&&".$prodUpdate; oci_rollback($con); die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate)
			{
				mysql_query("COMMIT");
				echo "0**".$txt_transfer_mst_id."**".$transfer_ack_update_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate)
			{
				oci_commit($con);
				echo "0**".$txt_transfer_mst_id."**".$transfer_ack_update_id;
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

		for($j=1;$j<=$tot_row;$j++)
		{
			$barcodeNo="barcodeNo_".$j;
			$all_barcodeNo.=$$barcodeNo.",";
			$rolltableId="rolltableId_".$j;

			$saved_roll_arr[str_replace("'", "", $$barcodeNo)]=str_replace("'", "", $$rolltableId);
		}
		$all_barcodeNo=chop($all_barcodeNo,',');
		
		// For > First Acknowledgment of First Transfer Again Second Acknowledgment of Second Transfer Now First Acknowledgment of Location Change
		if($all_barcodeNo!="")
		{
			$next_transfer_sql = sql_select("SELECT max(a.id) as max_id, a.barcode_no from pro_roll_details a
			where  a.barcode_no in ($all_barcodeNo) and a.status_active =1 and a.is_deleted=0 and entry_form in(22,58,84,83,82,110,180,183,133) group by a.barcode_no");
			// echo "10**".$next_transfer_sql;die;
			foreach ($next_transfer_sql as $next_trans)
			{
				$next_transfer_arr[$next_trans[csf('barcode_no')]]=$next_trans[csf('max_id')];
			}
		}
		// echo "10**<pre>";print_r($next_transfer_arr);die;

		if (!empty($saved_roll_arr)) // barcode to next transaction found
		{
			foreach ($saved_roll_arr as $barcode => $saved_roll_id) 
			{
				// echo $saved_roll_id .'!='. $next_transfer_arr[$barcode].'<br>';
				if ($saved_roll_id != $next_transfer_arr[$barcode]) 
				{
					echo "20**Sorry Next Transaction Found Update/Delete Not allowed, \nBarcode No :  ".$barcode;
					disconnect($con);die;
				}
			}
		}
		// echo "10**string";die;

		$field_array_update="acknowledg_date*store_id*remarks*updated_by*update_date";
		$data_array_update="".$txt_transfer_date."*".$cbo_store_name."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("inv_item_trans_acknowledgement",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;

		//echo "10**".$rID."**".$update_id."**".$txt_transfer_mst_id;die;

		$field_array_trans_update = "store_id*floor_id*room*rack*self*bin_box*updated_by*update_date";

		$field_array_dtls_update="to_store*to_floor_id*to_room*to_rack*to_shelf*to_bin_box*updated_by*update_date";

		for($j=1;$j<=$tot_row;$j++)
		{
			$dtlsId="dtlsId_".$j;
			$all_dtlsId.=$$dtlsId.",";
			$transIdTo="transIdTo_".$j;

			$toFloor="toFloor_".$j;
			$toRoom="toRoomId_".$j;
			$toRack="toRack_".$j;
			$toShelf="toShelf_".$j;
			$toBin="toBin_".$j;

			if(str_replace("'", "", $$transIdTo))
			{
				$to_transId_array_up[]=$$transIdTo;
				$data_array_trans_update[$$transIdTo]=explode("*",($cbo_store_name."*'".$$toFloor."'*'".$$toRoom."'*'".$$toRack."'*'".$$toShelf."'*'".$$toBin."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			$dtls_id_array[]=$$dtlsId;
			$data_array_dtls_update[$$dtlsId]=explode("*",($cbo_store_name."*'".$$toFloor."'*'".$$toRoom."'*'".$$toRack."'*'".$$toShelf."'*'".$$toBin."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}

		$rID=$dtls_data_upd=$trans_data_upd=true;

		$rID=sql_update("inv_item_trans_acknowledgement",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0; 

		if(!empty($data_array_trans_update))
		{
			// echo "10**".bulk_update_sql_statement( "inv_transaction", "id", $field_array_trans_update, $data_array_trans_update, $to_transId_array_up );die;
			$trans_data_upd=execute_query(bulk_update_sql_statement( "inv_transaction", "id", $field_array_trans_update, $data_array_trans_update, $to_transId_array_up ));
		}

		if(!empty($data_array_dtls_update))
		{
			// echo "10**".bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $dtls_id_array );die;
			$dtls_data_upd=execute_query(bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $dtls_id_array ));
		}
		
		// echo "10**$rID && $dtls_data_upd && $trans_data_upd";oci_rollback($con); die;

		if($db_type==0)
		{
			if($rID && $dtls_data_upd && $trans_data_upd)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'", '', $txt_transfer_mst_id)."**".str_replace("'", '', $update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**".str_replace("'", '', $txt_transfer_mst_id)."**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtls_data_upd && $trans_data_upd)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $txt_transfer_mst_id)."**".str_replace("'", '', $update_id);
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $txt_transfer_mst_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="populate_barcode_data_from_transfer")
{
	$data=explode("**",$data);
	//$bar_code=$data[0];
	$sys_id=$data[0];
	$company_name_array=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');


	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array);

	$scanned_barcode_update_data=sql_select("SELECT a.is_sales, c.company_id, a.id as roll_upid, a.po_breakdown_id, a.barcode_no, a.roll_id, a.dtls_id, a.from_roll_id, a.re_transfer, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.from_store, b.to_store, b.to_prod_id, b.from_prod_id, b.from_order_id, c.from_order_id  as fso_from_order_id, b.from_booking_without_order, b.transfer_requ_dtls_id, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf as to_shelf, b.to_bin_box, b.body_part_id, b.to_body_part, d.cons_rate, d.cons_amount, d.program_no, e.machine_dia, e.machine_gg, c.to_company
	from pro_roll_details a, inv_item_transfer_dtls b , inv_item_transfer_mst c, inv_transaction d, PPL_PLANNING_INFO_ENTRY_DTLS e
	where a.dtls_id=b.id and b.mst_id=c.id and c.id=d.mst_id and b.trans_id=d.id and d.PROGRAM_NO=e.id and b.FROM_PROGRAM=e.id and d.transaction_type in(6) and d.item_category=13  and c.entry_form in(82,133) and a.entry_form in(82,133) and a.mst_id=$sys_id and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.is_sales in(0,1)");
	foreach($scanned_barcode_update_data as $row)
	{
		$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']=$row[csf('roll_upid')];
		$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']=$row[csf('dtls_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['transfer_requ_dtls_id']=$row[csf('transfer_requ_dtls_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['trans_id']=$row[csf('trans_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_trans_id']=$row[csf('to_trans_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']=$row[csf('po_breakdown_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form']=$row[csf('from_trans_entry_form')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_store']=$row[csf('to_store')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_store']=$row[csf('from_store')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_prod_id']=$row[csf('to_prod_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']=$row[csf('from_prod_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_order_id']=$row[csf('from_order_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['fso_from_order_id']=$row[csf('fso_from_order_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_roll_id']=$row[csf('from_roll_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['re_transfer']=$row[csf('re_transfer')];
		$barcode_update_data[$row[csf('barcode_no')]]['company_id']=$row[csf('company_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_booking_without_order']=$row[csf('from_booking_without_order')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_floor']=$row[csf('to_floor_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_room']=$row[csf('to_room')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_rack']=$row[csf('to_rack')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_self']=$row[csf('to_shelf')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_bin_box']=$row[csf('to_bin_box')];
		$barcode_update_data[$row[csf('barcode_no')]]['body_part_id']=$row[csf('body_part_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_body_part']=$row[csf('to_body_part')];
		$barcode_update_data[$row[csf('barcode_no')]]['cons_rate']=$row[csf('cons_rate')];
		$barcode_update_data[$row[csf('barcode_no')]]['cons_amount']=$row[csf('cons_amount')];
		$barcode_update_data[$row[csf('barcode_no')]]['program_no']=$row[csf('program_no')];
		$barcode_update_data[$row[csf('barcode_no')]]['machine_dia']=$row[csf('machine_dia')];
		$barcode_update_data[$row[csf('barcode_no')]]['machine_gg']=$row[csf('machine_gg')];

		if ($row[csf('is_sales')]==1) 
		{
			$fso_order_id_arr[$row[csf('fso_from_order_id')]] = $row[csf('fso_from_order_id')];
			$fso_order_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}
		else
		{
			if($row[csf('from_booking_without_order')] == 1)
			{
				$non_order_booking_buyer_po_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
			}
			else
			{
				$po_arr_book_booking_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
			}

			$po_arr_book_booking_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}	

		$bar_code .=$row[csf('barcode_no')].",";

		$cbo_store_id = $row[csf('to_store')];
		$cbo_to_company = $row[csf('to_company')];
	}

	$bar_code = chop($bar_code,",");

	$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.febric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id, max(b.yarn_lot) as yarn_lot, max(b.yarn_count) as yarn_count, max(b.stitch_length) as stitch_length, max(b.brand_id) as brand_id, max(b.floor_id) as floor, max(b.room) as room, max(b.rack) as rack, max(b.self) as self, max(b.bin_box) as bin_box, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.amount, c.booking_without_order,c.id as roll_mst_id, c.is_sales
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($bar_code) and c.is_sales in(0,1)
	group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty, c.amount, c.booking_without_order,c.id, c.is_sales");

	if(count($data_array)>0)
	{
		foreach($data_array as $val)
		{
			$splitted_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];

			if ($row[csf('is_sales')]==1) 
			{
				$fso_order_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
			}
			else
			{
				if($val[csf("booking_without_order")] == 1 )
				{
					$non_order_booking_buyer_po_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
				}
				else
				{
					$po_arr_book_booking_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
				}
			}
			/*if($val[csf("receive_basis")] == 2){
				$program_id_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
			}*/
		}

		$splited_barcode = implode(',',array_filter($splitted_barcode_arr));
		$nxProcessedBarcode = array();
		if($splited_barcode)
		{
			$nxtProcessSql = sql_select("select a.id,a.barcode_no,a.roll_no from  pro_roll_details a where a.barcode_no in (".$splited_barcode.") and a.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and a.is_returned!=1");
			foreach ($nxtProcessSql as $val2)
			{
				$nxProcessedBarcode[$val2[csf("barcode_no")]] = $val2[csf("barcode_no")];
			}
			//print_r($nxProcessedBarcode);


			$splited_roll_sql=sql_select("select barcode_no,split_from_id from pro_roll_split where status_active =1 and barcode_no in ($splited_barcode)");

			foreach($splited_roll_sql as $bar)
			{
				$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
			}

			$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 and barcode_no in ($splited_barcode) and entry_form = 82 order by barcode_no");
			foreach($child_split_sql as $bar)
			{
				$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
			}

			//print_r($splited_roll_ref);die;

		}

		$production_basis_sql = sql_select("select a.barcode_no, b.receive_basis, a.booking_without_order, b.booking_id  from pro_roll_details a, inv_receive_master b where a.mst_id = b.id and b.entry_form = 2 and a.entry_form = 2 and a.status_active =1 and a.barcode_no in ($bar_code)");
		foreach ($production_basis_sql as $val)
		{
			$production_basis_arr[$val[csf("barcode_no")]]["receive_basis"] = $val[csf("receive_basis")];
			$production_basis_arr[$val[csf("barcode_no")]]["booking_without_order"] = $val[csf("booking_without_order")];
			$production_basis_arr[$val[csf("barcode_no")]]["booking_id"] = $val[csf("booking_id")];

			if($val[csf("receive_basis")] ==2 && $val[csf("booking_without_order")] ==0)
			{
				$program_with_order_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
			}

		}
	}
	// echo "<pre>";print_r($fso_order_id_arr);die;

	//$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1",'id','buyer_id');

	$non_order_booking_buyer_po_arr = array_filter($non_order_booking_buyer_po_arr);
	//$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $non_order_booking_buyer_po_arr).")",'id','buyer_id');

	if(count($non_order_booking_buyer_po_arr)>0)
	{
		$non_order_sql = sql_select("select id, buyer_id, booking_no from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $non_order_booking_buyer_po_arr).")");
		foreach ($non_order_sql as  $val)
		{
			$book_buyer_arr[$val[csf("id")]] = $val[csf("buyer_id")];
			$non_booking_arr[$val[csf("id")]] = $val[csf("booking_no")];
		}
	}

	$po_arr_book_booking_arr = array_filter($po_arr_book_booking_arr);
	if(count($po_arr_book_booking_arr)>0)
	{
		if(!empty($program_with_order_arr))
		{
			$book_booking_sql=sql_select("SELECT a.po_break_down_id, a.booking_no , c.id as plan_id from wo_booking_dtls a left join ppl_planning_info_entry_mst b on a.booking_no = b.booking_no left join  ppl_planning_info_entry_dtls c on b.id = c.mst_id and c.id in (".implode(",", $program_with_order_arr).") where  a.is_deleted=0 and a.status_active=1 and a.booking_type in (1,4) and a.po_break_down_id in (".implode(',', $po_arr_book_booking_arr).") group by a.po_break_down_id, a.booking_no ,c.id");

			foreach ($book_booking_sql as $val)
			{
				$book_booking_arr_plan_wise[$val[csf("po_break_down_id")]][$val[csf("plan_id")]] = $val[csf("booking_no")];
			}
		}
		else
		{
			$book_booking_arr=return_library_array("SELECT po_break_down_id, booking_no from wo_booking_dtls where is_deleted=0 and status_active=1 and booking_type=1 and po_break_down_id in (". implode(',', $po_arr_book_booking_arr) .")",'po_break_down_id','booking_no');
		}

		$po_data_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.grouping FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id in (".implode(',', $po_arr_book_booking_arr).")");
		$po_details_array=array();
		foreach($po_data_sql as $row)
		{
			$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
			$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
			$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
			$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
			$po_details_array[$row[csf("po_id")]]['grouping']=$row[csf("grouping")];
		}
	}

	$fso_order_id_arr = array_filter($fso_order_id_arr);
	if(count($fso_order_id_arr)>0) // 
	{
		//$sales_sql="SELECT a.id, a.job_no, a.sales_booking_no, a.within_group, a.buyer_id, a.style_ref_no from fabric_sales_order_mst a where a.id in (".implode(',', $fso_order_id_arr).") and a.status_active=1 and a.is_deleted=0";

		$sales_sql="SELECT c.id, c.job_no, c.sales_booking_no, c.within_group, c.buyer_id, c.style_ref_no, a.po_number, a.grouping, b.booking_no, b.booking_mst_id
		from fabric_sales_order_mst c
		left join wo_booking_dtls b on c.BOOKING_ID=b.BOOKING_MST_ID and b.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0 
		left join wo_po_break_down a on b.po_break_down_id=a.id and a.status_active=1 and a.is_deleted=0
		where c.id in (".implode(',', $fso_order_id_arr).") and c.status_active=1 and c.is_deleted=0";
		//echo $sales_sql;
		$sales_sql_result=sql_select($sales_sql);
		foreach($sales_sql_result as $row)
		{
			$sales_array[$row[csf('id')]]['job']			= $row[csf('job_no')];
			$sales_array[$row[csf('id')]]['booking_no']		= $row[csf('sales_booking_no')];
			$sales_array[$row[csf('id')]]['within_group']	= $row[csf('within_group')];
			$sales_array[$row[csf('id')]]['buyer_id']		= $row[csf('buyer_id')];
			$sales_array[$row[csf('id')]]['style_ref_no']	= $row[csf('style_ref_no')];
			$sales_array[$row[csf('id')]]['grouping']	= $row[csf('grouping')];
		}
	}
	/*
	|--------------------------------------------------------------------------
	| for floor, room, rack and shelf disable
	|--------------------------------------------------------------------------
	|
	*/
	$sql_floorRoomRackShelf = sql_select("SELECT c.barcode_no FROM pro_roll_details c WHERE c.entry_form IN(61,82,83,133) AND c.status_active=1 AND c.is_deleted=0 AND c.barcode_no IN(".$bar_code.")");
	$floorRoomRackShelf_disable_arr=array();
	foreach($sql_floorRoomRackShelf as $row)
	{
		$floorRoomRackShelf_disable_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
	}
	//end

	/*
	|--------------------------------------------------------------------------
	| for floor, room, rack and shelf Load drop down all
	|--------------------------------------------------------------------------
	|
	*/
	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id, b.store_id,b.floor_id,b.room_id, b.rack_id,b.shelf_id,b.bin_id, a.floor_room_rack_name floor_name, c.floor_room_rack_name room_name, d.floor_room_rack_name rack_name, e.floor_room_rack_name shelf_name , f.floor_room_rack_name bin_name 
		from lib_floor_room_rack_dtls b
		left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
		left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
		left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
		left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
		left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0
		where b.status_active=1 and b.is_deleted=0 and b.store_id=$cbo_store_id and b.company_id =$cbo_to_company";
	$lib_floor_room_data_arr=sql_select($lib_room_rack_shelf_sql);
	foreach ($lib_floor_room_data_arr as $room_rack_shelf_row) 
	{
		$company  = $room_rack_shelf_row[csf("company_id")];
		$store_id   = $room_rack_shelf_row[csf("store_id")];
		$floor_id = $room_rack_shelf_row[csf("floor_id")];
		$room_id  = $room_rack_shelf_row[csf("room_id")];
		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_floor_arr[$store_id] .= $floor_id.",";
		}

		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_room_arr[$store_id][$floor_id] .= $room_id.",";
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
			$lib_rack_arr[$store_id][$floor_id][$room_id] .= $rack_id.",";
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
			$lib_shelf_arr[$store_id][$floor_id][$room_id][$rack_id] .= $shelf_id.",";
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
			$lib_bin_arr[$store_id][$floor_id][$room_id][$rack_id][$shelf_id].= $bin_id.",";
		}
	}
	unset($lib_floor_room_data_arr);

	//end
	
	$roll_details_array=array();
	$barcode_array=array();
	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==0) || ($row[csf("entry_form")]==22 && ($row[csf("receive_basis")]==4 || $row[csf("receive_basis")]==6)))
			{
				$receive_basis="Independent";
				$receive_basis_id=0;
			}
			else if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==1) || ($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==2))
			{
				$receive_basis="Booking";
				$receive_basis_id=2;
			}
			else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2)
			{
				$receive_basis="Knitting Plan";
				$receive_basis_id=3;
			}
			else if($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==1)
			{
				$receive_basis="PI";
				$receive_basis_id=1;
			}
			else if($row[csf("entry_form")]==58)
			{
				$receive_basis="Delivery";
				$receive_basis_id=9;
			}

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

			/*if($order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=="")
			{
				$po_id=$row[csf("po_breakdown_id")];
			}
			else
			{
				$po_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"];
			}*/

			$roll_mst_id = $barcode_update_data[$row[csf('barcode_no')]]['from_roll_id'];
			//echo $roll_mst_id;die;

			if ($row[csf('is_sales')]==1) 
			{
				$from_order_id = $barcode_update_data[$row[csf('barcode_no')]]['fso_from_order_id'];
				$from_job_no=$sales_array[$from_order_id]['job'];
				$booking_no_fab=$sales_array[$from_order_id]['booking_no'];
				$buyer_name=$buyer_arr[$sales_array[$from_order_id]['buyer_id']];
				$buyer_id=$sales_array[$from_order_id]['buyer_id'];
				$from_po_number='';
				$to_po_number='';
				$from_int_ref=$sales_array[$from_order_id]['grouping'];
				$to_job_no=$sales_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['job'];
				// echo $from_order_id.'='.$row[csf("po_breakdown_id")].'<br><br>';
				$program_no=$barcode_update_data[$row[csf('barcode_no')]]['program_no'];
			}
			else
			{
				if($row[csf("booking_without_order")]==1)
				{
					$booking_no_fab=$non_booking_arr[$row[csf("po_breakdown_id")]];
				}
				else
				{
					if($production_basis_arr[$row[csf('barcode_no')]]["receive_basis"] == 2 && $production_basis_arr[$row[csf('barcode_no')]]["booking_without_order"] == 0 )
					{
						$plan_id = $production_basis_arr[$row[csf('barcode_no')]]["booking_id"];
						$booking_no_fab = $book_booking_arr_plan_wise[$row[csf("po_breakdown_id")]][$plan_id];
					}
					else
					{
						$booking_no_fab=$book_booking_arr[$row[csf("po_breakdown_id")]];
					}
				}

				$from_order_id =  $barcode_update_data[$row[csf('barcode_no')]]['from_order_id'];
				$from_booking_without_order = $barcode_update_data[$row[csf('barcode_no')]]['from_booking_without_order'];
				// echo $from_order_id.'='.$row[csf("po_breakdown_id")].'<br><br>';
				if($from_booking_without_order == 1)
				{
					$buyer_name=$buyer_arr[$book_buyer_arr[$from_order_id]];
				}
				else
				{
					$buyer_name=$buyer_arr[$po_details_array[$from_order_id]['buyer_name']];
				}
				$buyer_id=$po_details_array[$from_order_id]['buyer_name'];

				if($barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 82 || $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 83 || $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 110 || $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 180)
				{
					$booking_no_fab = $booking_no_fab . " (T)";
				}

				$from_job_no=$po_details_array[$from_order_id]['job_no'];
				$from_po_number=$po_details_array[$from_order_id]['po_number'];
				$from_int_ref=$po_details_array[$from_order_id]['grouping'];
				$to_po_number=$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['po_number'];
				$to_job_no=$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['job_no'];
				$program_no=0;
			}
			$cons_rate=$barcode_update_data[$row[csf('barcode_no')]]['cons_rate'];
			$cons_amount=$barcode_update_data[$row[csf('barcode_no')]]['cons_amount'];
			/*
			|--------------------------------------------------------------------------
			| for floor, room, rack and shelf disable
			|--------------------------------------------------------------------------
			|
			*/
			$isFloorRoomRackShelfDisable=0;
			if(!empty($floorRoomRackShelf_disable_arr[$val[csf("barcode_no")]]))
			{
				$isFloorRoomRackShelfDisable=1;
			}
			//end
			$body_part_id = $barcode_update_data[$row[csf('barcode_no')]]['body_part_id'];
			$to_body_part = $barcode_update_data[$row[csf('barcode_no')]]['to_body_part'];
			$store_id = $barcode_update_data[$row[csf('barcode_no')]]['from_store'];
			$entry_form = $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'];
			$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];
			$compsition_description=$constructtion_arr[$row[csf("febric_description_id")]]." ".$composition_arr[$row[csf("febric_description_id")]];

			/*
			|--------------------------------------------------------------------------
			| for floor, room, rack and shelf Load drop down
			|--------------------------------------------------------------------------
			|
			*/
			$to_floor_id= $barcode_update_data[$row[csf('barcode_no')]]['to_floor'];
			$to_room =$barcode_update_data[$row[csf('barcode_no')]]['to_room'];
			$to_rack =$barcode_update_data[$row[csf('barcode_no')]]['to_rack'];
			$to_self =$barcode_update_data[$row[csf('barcode_no')]]['to_self'];
			
			$multi_floor = chop($lib_floor_arr[$cbo_store_id],",");
			$multi_room = chop($lib_room_arr[$cbo_store_id][$to_floor_id],",");
			$multi_rack = chop($lib_rack_arr[$cbo_store_id][$to_floor_id][$to_room],",");
			$multi_self = chop($lib_shelf_arr[$cbo_store_id][$to_floor_id][$to_room][$to_rack],",");
			$multi_bin = chop($lib_bin_arr[$cbo_store_id][$to_floor_id][$to_room][$to_rack][$to_self],",");
			if($multi_floor==""){
				$multi_floor = "0";
			}
			if($multi_room==""){
				$multi_room = "0";
			}
			if($multi_rack==""){
				$multi_rack = "0";
			}
			if($multi_self==""){
				$multi_self = "0";
			}
			if($multi_bin==""){
				$multi_bin = "0";
			}
			//end

			$barcodeData.=$row[csf('id')]."**".$barcode_update_data[$row[csf('barcode_no')]]['company_id']."**".$body_part[$body_part_id]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$store_id."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_prod_id']."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$from_order_id."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$from_job_no."**".$buyer_id."**".$buyer_name."**".$from_po_number."**".$row[csf("color_id")]."**".$store_arr[$store_id]."**".$body_part_id."**".$row[csf("brand_id")]."**".$row[csf("machine_no_id")]."**".$entry_form."**".$row[csf("booking_without_order")]."**".$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']."**".$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['trans_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_trans_id']."**".$to_po_number."**".$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']."**".$nxProcessedBarcode[$row[csf("barcode_no")]]."**".$roll_mst_id."**".$booking_no_fab."**".$row[csf("amount")]."**".$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']."**".$from_booking_without_order."**".$splited_roll_ref[$row[csf('barcode_no')]][$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_floor']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_room']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_rack']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_self']."**".$row[csf("floor")]."**".$row[csf("room")]."**".$isFloorRoomRackShelfDisable."**".$from_int_ref."**".$row[csf("bin_box")]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_bin_box']."**".$to_job_no."**".$to_body_part."**".$cons_rate."**".$cons_amount."**".$program_no."**".$barcode_update_data[$row[csf('barcode_no')]]['machine_dia']."**".$barcode_update_data[$row[csf('barcode_no')]]['machine_gg']."**".$multi_floor."**".$multi_room."**".$multi_rack."**".$multi_self."**".$multi_bin."__";
		}
		//echo $test_str;die;
		echo chop($barcodeData,"__");
	}
	else
	{
		echo "0";
	}
	exit();
}

if($action=="populate_barcode_data_ack")
{
	$data=explode("**",$data);
	//$bar_code=$data[0];
	$sys_id=$data[0];
	$company_name_array=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');


	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array);

	$scanned_barcode_update_data=sql_select("SELECT a.is_sales, c.company_id, a.id as roll_upid, a.po_breakdown_id, a.barcode_no, a.roll_id, a.dtls_id, a.from_roll_id, a.re_transfer, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.from_store, b.to_store, b.to_prod_id, b.from_prod_id, b.from_order_id, c.from_order_id  as fso_from_order_id, c.to_company, b.from_booking_without_order, b.transfer_requ_dtls_id, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf as to_shelf, b.to_bin_box, b.body_part_id, b.to_body_part, e.MACHINE_DIA, e.MACHINE_GG
	from pro_roll_details a, inv_item_transfer_dtls b, inv_item_transfer_mst c, inv_item_trans_acknowledgement d, PPL_PLANNING_INFO_ENTRY_DTLS e
	where a.dtls_id=b.id and b.mst_id=c.id and c.id=d.challan_id and e.id = b.TO_PROGRAM and c.entry_form in(82,133) and a.entry_form in(82,133) and a.status_active=1 and a.is_deleted=0 and d.id=$sys_id and a.is_sales in(0,1)");
	// echo ("SELECT a.is_sales, c.company_id, a.id as roll_upid, a.po_breakdown_id, a.barcode_no, a.roll_id, a.dtls_id, a.from_roll_id, a.re_transfer, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.from_store, b.to_store, b.to_prod_id, b.from_prod_id, b.from_order_id, c.from_order_id  as fso_from_order_id, b.from_booking_without_order, b.transfer_requ_dtls_id, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf as to_shelf, b.to_bin_box, b.body_part_id, b.to_body_part, e.MACHINE_DIA, e.MACHINE_GG
	// from pro_roll_details a, inv_item_transfer_dtls b, inv_item_transfer_mst c, inv_item_trans_acknowledgement d, PPL_PLANNING_INFO_ENTRY_DTLS e
	// where a.dtls_id=b.id and b.mst_id=c.id and c.id=d.challan_id and e.id = b.TO_PROGRAM and c.entry_form in(82,133) and a.entry_form in(82,133) and a.status_active=1 and a.is_deleted=0 and d.id=$sys_id and a.is_sales in(0,1)");
	foreach($scanned_barcode_update_data as $row)
	{
		$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']=$row[csf('roll_upid')];
		$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']=$row[csf('dtls_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['transfer_requ_dtls_id']=$row[csf('transfer_requ_dtls_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['trans_id']=$row[csf('trans_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_trans_id']=$row[csf('to_trans_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']=$row[csf('po_breakdown_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form']=$row[csf('from_trans_entry_form')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_store']=$row[csf('to_store')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_store']=$row[csf('from_store')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_prod_id']=$row[csf('to_prod_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']=$row[csf('from_prod_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_order_id']=$row[csf('from_order_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['fso_from_order_id']=$row[csf('fso_from_order_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_roll_id']=$row[csf('from_roll_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['re_transfer']=$row[csf('re_transfer')];
		$barcode_update_data[$row[csf('barcode_no')]]['company_id']=$row[csf('company_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_booking_without_order']=$row[csf('from_booking_without_order')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_floor']=$row[csf('to_floor_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_room']=$row[csf('to_room')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_rack']=$row[csf('to_rack')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_self']=$row[csf('to_shelf')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_bin_box']=$row[csf('to_bin_box')];
		$barcode_update_data[$row[csf('barcode_no')]]['body_part_id']=$row[csf('body_part_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_body_part']=$row[csf('to_body_part')];
		$barcode_update_data[$row[csf('barcode_no')]]['machine_dia']=$row[csf('machine_dia')];
		$barcode_update_data[$row[csf('barcode_no')]]['machine_gg']=$row[csf('machine_gg')];

		if ($row[csf('is_sales')]==1) 
		{
			$fso_order_id_arr[$row[csf('fso_from_order_id')]] = $row[csf('fso_from_order_id')];
			$fso_order_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}
		else
		{
			if($row[csf('from_booking_without_order')] == 1)
			{
				$non_order_booking_buyer_po_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
			}
			else
			{
				$po_arr_book_booking_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
			}
			$po_arr_book_booking_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}


		$bar_code .=$row[csf('barcode_no')].",";

		$cbo_store_id = $row[csf('to_store')];
		$cbo_to_company = $row[csf('to_company')];
	}

	$bar_code = chop($bar_code,",");

	$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.febric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id, max(b.yarn_lot) as yarn_lot, max(b.yarn_count) as yarn_count, max(b.stitch_length) as stitch_length, max(b.brand_id) as brand_id, max(b.floor_id) as floor, max(b.room) as room, max(b.rack) as rack, max(b.self) as self, max(b.bin_box) as bin_box, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.amount, c.booking_without_order,c.id as roll_mst_id, c.is_sales
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($bar_code)
	group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty, c.amount, c.booking_without_order,c.id, c.is_sales");

	if(count($data_array)>0)
	{
		foreach($data_array as $val)
		{
			$splitted_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];

			if ($row[csf('is_sales')]==1) 
			{
				$fso_order_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
			}
			else
			{
				if($val[csf("booking_without_order")] == 1 )
				{
					$non_order_booking_buyer_po_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
				}
				else
				{
					$po_arr_book_booking_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
				}
			}

			/*if($val[csf("receive_basis")] == 2){
				$program_id_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
			}*/
		}

		$splited_barcode = implode(',',array_filter($splitted_barcode_arr));
		$nxProcessedBarcode = array();
		if($splited_barcode)
		{
			$nxtProcessSql = sql_select("select a.id,a.barcode_no,a.roll_no from  pro_roll_details a where a.barcode_no in (".$splited_barcode.") and a.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and a.is_returned!=1");
			foreach ($nxtProcessSql as $val2)
			{
				$nxProcessedBarcode[$val2[csf("barcode_no")]] = $val2[csf("barcode_no")];
			}
			//print_r($nxProcessedBarcode);


			$splited_roll_sql=sql_select("select barcode_no,split_from_id from pro_roll_split where status_active =1 and barcode_no in ($splited_barcode)");

			foreach($splited_roll_sql as $bar)
			{
				$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
			}

			$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 and barcode_no in ($splited_barcode) and entry_form = 82 order by barcode_no");
			foreach($child_split_sql as $bar)
			{
				$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
			}

			//print_r($splited_roll_ref);die;

		}

		$production_basis_sql = sql_select("select a.barcode_no, b.receive_basis, a.booking_without_order, b.booking_id  from pro_roll_details a, inv_receive_master b where a.mst_id = b.id and b.entry_form = 2 and a.entry_form = 2 and a.status_active =1 and a.barcode_no in ($bar_code)");
		foreach ($production_basis_sql as $val)
		{
			$production_basis_arr[$val[csf("barcode_no")]]["receive_basis"] = $val[csf("receive_basis")];
			$production_basis_arr[$val[csf("barcode_no")]]["booking_without_order"] = $val[csf("booking_without_order")];
			$production_basis_arr[$val[csf("barcode_no")]]["booking_id"] = $val[csf("booking_id")];

			if($val[csf("receive_basis")] ==2 && $val[csf("booking_without_order")] ==0)
			{
				$program_with_order_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
			}

		}
	}

	//$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1",'id','buyer_id');

	$non_order_booking_buyer_po_arr = array_filter($non_order_booking_buyer_po_arr);
	//$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $non_order_booking_buyer_po_arr).")",'id','buyer_id');

	if(count($non_order_booking_buyer_po_arr)>0)
	{
		$non_order_sql = sql_select("select id, buyer_id, booking_no from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $non_order_booking_buyer_po_arr).")");
		foreach ($non_order_sql as  $val)
		{
			$book_buyer_arr[$val[csf("id")]] = $val[csf("buyer_id")];
			$non_booking_arr[$val[csf("id")]] = $val[csf("booking_no")];
		}
	}

	$po_arr_book_booking_arr = array_filter($po_arr_book_booking_arr);
	if(count($po_arr_book_booking_arr)>0)
	{
		if(!empty($program_with_order_arr))
		{
			$book_booking_sql=sql_select("SELECT a.po_break_down_id, a.booking_no , c.id as plan_id from wo_booking_dtls a left join ppl_planning_info_entry_mst b on a.booking_no = b.booking_no left join  ppl_planning_info_entry_dtls c on b.id = c.mst_id and c.id in (".implode(",", $program_with_order_arr).") where  a.is_deleted=0 and a.status_active=1 and a.booking_type in (1,4) and a.po_break_down_id in (".implode(',', $po_arr_book_booking_arr).") group by a.po_break_down_id, a.booking_no ,c.id");

			foreach ($book_booking_sql as $val)
			{
				$book_booking_arr_plan_wise[$val[csf("po_break_down_id")]][$val[csf("plan_id")]] = $val[csf("booking_no")];
			}
		}
		else
		{
			$book_booking_arr=return_library_array("SELECT po_break_down_id, booking_no from wo_booking_dtls where is_deleted=0 and status_active=1 and booking_type=1 and po_break_down_id in (". implode(',', $po_arr_book_booking_arr) .")",'po_break_down_id','booking_no');
		}

		$po_data_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.grouping FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id in (".implode(',', $po_arr_book_booking_arr).")");
		$po_details_array=array();
		foreach($po_data_sql as $row)
		{
			$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
			$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
			$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
			$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
			$po_details_array[$row[csf("po_id")]]['grouping']=$row[csf("grouping")];
		}
	}

	$fso_order_id_arr = array_filter($fso_order_id_arr);
	if(count($fso_order_id_arr)>0) // 
	{
		//$sales_sql="SELECT a.id, a.job_no, a.sales_booking_no, a.within_group, a.buyer_id, a.style_ref_no from fabric_sales_order_mst a where a.id in (".implode(',', $fso_order_id_arr).") and a.status_active=1 and a.is_deleted=0";

		$sales_sql="SELECT c.id, c.job_no, c.sales_booking_no, c.within_group, c.buyer_id, c.style_ref_no, a.po_number, a.grouping, b.booking_no, b.booking_mst_id
		from fabric_sales_order_mst c
		left join wo_booking_dtls b on c.BOOKING_ID=b.BOOKING_MST_ID and b.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0 
		left join wo_po_break_down a on b.po_break_down_id=a.id and a.status_active=1 and a.is_deleted=0
		where c.id in (".implode(',', $fso_order_id_arr).") and c.status_active=1 and c.is_deleted=0";
		//echo $sales_sql;
		$sales_sql_result=sql_select($sales_sql);
		foreach($sales_sql_result as $row)
		{
			$sales_array[$row[csf('id')]]['job']			= $row[csf('job_no')];
			$sales_array[$row[csf('id')]]['booking_no']		= $row[csf('sales_booking_no')];
			$sales_array[$row[csf('id')]]['within_group']	= $row[csf('within_group')];
			$sales_array[$row[csf('id')]]['buyer_id']		= $row[csf('buyer_id')];
			$sales_array[$row[csf('id')]]['style_ref_no']	= $row[csf('style_ref_no')];
			$sales_array[$row[csf('id')]]['grouping']	= $row[csf('grouping')];
		}
	}

	/*
	|--------------------------------------------------------------------------
	| for floor, room, rack and shelf disable
	|--------------------------------------------------------------------------
	|
	*/
	$sql_floorRoomRackShelf = sql_select("SELECT c.barcode_no FROM pro_roll_details c WHERE c.entry_form IN(61,82,83,133) AND c.status_active=1 AND c.is_deleted=0 AND c.barcode_no IN(".$bar_code.")");
	$floorRoomRackShelf_disable_arr=array();
	foreach($sql_floorRoomRackShelf as $row)
	{
		$floorRoomRackShelf_disable_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
	}
	//end

	/*
	|--------------------------------------------------------------------------
	| for floor, room, rack and shelf Load drop down all
	|--------------------------------------------------------------------------
	|
	*/
	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id, b.store_id,b.floor_id,b.room_id, b.rack_id,b.shelf_id,b.bin_id, a.floor_room_rack_name floor_name, c.floor_room_rack_name room_name, d.floor_room_rack_name rack_name, e.floor_room_rack_name shelf_name , f.floor_room_rack_name bin_name 
		from lib_floor_room_rack_dtls b
		left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
		left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
		left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
		left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
		left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0
		where b.status_active=1 and b.is_deleted=0 and b.store_id=$cbo_store_id and b.company_id =$cbo_to_company";
	$lib_floor_room_data_arr=sql_select($lib_room_rack_shelf_sql);
	foreach ($lib_floor_room_data_arr as $room_rack_shelf_row) 
	{
		$company  = $room_rack_shelf_row[csf("company_id")];
		$store_id   = $room_rack_shelf_row[csf("store_id")];
		$floor_id = $room_rack_shelf_row[csf("floor_id")];
		$room_id  = $room_rack_shelf_row[csf("room_id")];
		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_floor_arr[$store_id] .= $floor_id.",";
		}

		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_room_arr[$store_id][$floor_id] .= $room_id.",";
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
			$lib_rack_arr[$store_id][$floor_id][$room_id] .= $rack_id.",";
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
			$lib_shelf_arr[$store_id][$floor_id][$room_id][$rack_id] .= $shelf_id.",";
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
			$lib_bin_arr[$store_id][$floor_id][$room_id][$rack_id][$shelf_id].= $bin_id.",";
		}
	}
	unset($lib_floor_room_data_arr);

	//end
	
	$roll_details_array=array();
	$barcode_array=array();
	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==0) || ($row[csf("entry_form")]==22 && ($row[csf("receive_basis")]==4 || $row[csf("receive_basis")]==6)))
			{
				$receive_basis="Independent";
				$receive_basis_id=0;
			}
			else if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==1) || ($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==2))
			{
				$receive_basis="Booking";
				$receive_basis_id=2;
			}
			else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2)
			{
				$receive_basis="Knitting Plan";
				$receive_basis_id=3;
			}
			else if($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==1)
			{
				$receive_basis="PI";
				$receive_basis_id=1;
			}
			else if($row[csf("entry_form")]==58)
			{
				$receive_basis="Delivery";
				$receive_basis_id=9;
			}

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

			if($order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=="")
			{
				$po_id=$row[csf("po_breakdown_id")];
				//$roll_mst_id= $row[csf("roll_mst_id")];
			}
			else
			{
				$po_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"];
				//$roll_mst_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["roll_table_id"];
			}

			$roll_mst_id = $barcode_update_data[$row[csf('barcode_no')]]['from_roll_id'];
			//echo $po_id;die;

			if ($row[csf('is_sales')]==1) 
			{
				$from_order_id = $barcode_update_data[$row[csf('barcode_no')]]['fso_from_order_id'];
				$from_job_no=$sales_array[$from_order_id]['job'];
				$booking_no_fab=$sales_array[$from_order_id]['booking_no'];
				$buyer_name=$buyer_arr[$sales_array[$from_order_id]['buyer_id']];
				$buyer_id=$sales_array[$from_order_id]['buyer_id'];
				$from_po_number='';
				$to_po_number='';
				$from_int_ref=$sales_array[$from_order_id]['grouping'];
				$to_job_no=$sales_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['job'];
				// echo $from_order_id.'='.$row[csf("po_breakdown_id")].'<br><br>';
				$program_no=0;//update a data update kora hoyni
			}
			else
			{
				if($row[csf("booking_without_order")]==1)
				{
					$booking_no_fab=$non_booking_arr[$row[csf("po_breakdown_id")]];
				}
				else
				{
					if($production_basis_arr[$row[csf('barcode_no')]]["receive_basis"] == 2 && $production_basis_arr[$row[csf('barcode_no')]]["booking_without_order"] == 0 )
					{
						$plan_id = $production_basis_arr[$row[csf('barcode_no')]]["booking_id"];
						$booking_no_fab = $book_booking_arr_plan_wise[$row[csf("po_breakdown_id")]][$plan_id];
					}
					else
					{
						$booking_no_fab=$book_booking_arr[$row[csf("po_breakdown_id")]];
					}
				}

				$from_order_id =  $barcode_update_data[$row[csf('barcode_no')]]['from_order_id'];
				$from_booking_without_order = $barcode_update_data[$row[csf('barcode_no')]]['from_booking_without_order'];

				if($from_booking_without_order == 1)
				{
					$buyer_name=$buyer_arr[$book_buyer_arr[$from_order_id]];
				}
				else
				{
					$buyer_name=$buyer_arr[$po_details_array[$from_order_id]['buyer_name']];
				}

				if($barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 82 || $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 83 || $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 110 || $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 180)
				{
					$booking_no_fab = $booking_no_fab . " (T)";
				}

				$from_job_no=$po_details_array[$from_order_id]['job_no'];
				$from_po_number=$po_details_array[$from_order_id]['po_number'];
				$from_int_ref=$po_details_array[$from_order_id]['grouping'];
				$to_po_number=$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['po_number'];
				$to_job_no=$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['job_no'];
			}
			$cons_rate=0;//update a data update kora hoyni
			$cons_amount=0;//update a data update kora hoyni
			/*
			|--------------------------------------------------------------------------
			| for floor, room, rack and shelf disable
			|--------------------------------------------------------------------------
			|
			*/
			$isFloorRoomRackShelfDisable=0;
			if(!empty($floorRoomRackShelf_disable_arr[$val[csf("barcode_no")]]))
			{
				$isFloorRoomRackShelfDisable=1;
			}
			//end
			$body_part_id = $barcode_update_data[$row[csf('barcode_no')]]['body_part_id'];
			$to_body_part = $barcode_update_data[$row[csf('barcode_no')]]['to_body_part'];
			$store_id = $barcode_update_data[$row[csf('barcode_no')]]['from_store'];
			$entry_form = $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'];
			$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];
			$compsition_description=$constructtion_arr[$row[csf("febric_description_id")]]." ".$composition_arr[$row[csf("febric_description_id")]];

			/*
			|--------------------------------------------------------------------------
			| for floor, room, rack and shelf Load drop down
			|--------------------------------------------------------------------------
			|
			*/
			$to_floor_id= $barcode_update_data[$row[csf('barcode_no')]]['to_floor'];
			$to_room =$barcode_update_data[$row[csf('barcode_no')]]['to_room'];
			$to_rack =$barcode_update_data[$row[csf('barcode_no')]]['to_rack'];
			$to_self =$barcode_update_data[$row[csf('barcode_no')]]['to_self'];
			
			$multi_floor = chop($lib_floor_arr[$cbo_store_id],",");
			$multi_room = chop($lib_room_arr[$cbo_store_id][$to_floor_id],",");
			$multi_rack = chop($lib_rack_arr[$cbo_store_id][$to_floor_id][$to_room],",");
			$multi_self = chop($lib_shelf_arr[$cbo_store_id][$to_floor_id][$to_room][$to_rack],",");
			$multi_bin = chop($lib_bin_arr[$cbo_store_id][$to_floor_id][$to_room][$to_rack][$to_self],",");
			if($multi_floor==""){
				$multi_floor = "0";
			}
			if($multi_room==""){
				$multi_room = "0";
			}
			if($multi_rack==""){
				$multi_rack = "0";
			}
			if($multi_self==""){
				$multi_self = "0";
			}
			if($multi_bin==""){
				$multi_bin = "0";
			}
			//end

			$barcodeData.=$row[csf('id')]."**".$barcode_update_data[$row[csf('barcode_no')]]['company_id']."**".$body_part[$body_part_id]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$store_id."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_prod_id']."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$from_order_id."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$from_job_no."**".$buyer_id."**".$buyer_name."**".$from_po_number."**".$row[csf("color_id")]."**".$store_arr[$store_id]."**".$body_part_id."**".$row[csf("brand_id")]."**".$row[csf("machine_no_id")]."**".$entry_form."**".$row[csf("booking_without_order")]."**".$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']."**".$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['trans_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_trans_id']."**".$to_po_number."**".$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']."**".$nxProcessedBarcode[$row[csf("barcode_no")]]."**".$roll_mst_id."**".$booking_no_fab."**".$row[csf("amount")]."**".$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']."**".$from_booking_without_order."**".$splited_roll_ref[$row[csf('barcode_no')]][$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_floor']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_room']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_rack']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_self']."**".$row[csf("floor")]."**".$row[csf("room")]."**".$isFloorRoomRackShelfDisable."**".$from_int_ref."**".$row[csf("bin_box")]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_bin_box']."**".$to_job_no."**".$to_body_part."**".$cons_rate."**".$cons_amount."**".$program_no."**".$barcode_update_data[$row[csf('barcode_no')]]['machine_dia']."**".$barcode_update_data[$row[csf('barcode_no')]]['machine_gg']."**".$multi_floor."**".$multi_room."**".$multi_rack."**".$multi_self."**".$multi_bin."__";
		}
		//echo $test_str;die;
		echo chop($barcodeData,"__");
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
			<fieldset style="width:860px; margin-left:2px">
	            <table cellpadding="0" cellspacing="0" width="850" border="1" rules="all" class="rpt_table">
	                <thead>
	                    <th class="must_entry_caption">Is sales</th>
	                    <th>Acknowledge Date Range</th>
	                    <th>IR/IB</th>
	                    <th>Barcode No.</th>
	                    <th width="200">Please Enter Acknowledge ID</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                    	<input type="hidden" name="hidden_system_id" id="hidden_system_id">
	                    </th>
	                </thead>
	                <tr class="general">
						<td align="center">
							<?
							echo create_drop_down("is_sales", 80, $yes_no, "", 1, "--Select--", "", $dd, 0);
							?>
						</td>
	                    <td align="center">
	                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:90px" readonly>To
						  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:90px" readonly>
						</td>
						<td align="center">
	                        <input type="text" style="width:100px" class="text_boxes"  name="internal_ref" id="internal_ref" />
	                    </td>
						<td align="center">
	                        <input type="text" style="width:100px" class="text_boxes"  name="txt_barcode_no" id="txt_barcode_no" />
	                    </td>
	                    <td align="center" id="search_by_td">
	                        <input type="text" style="width:150px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('internal_ref').value+'_'+document.getElementById('txt_barcode_no').value+'_'+document.getElementById('is_sales').value, 'create_challan_search_list_view', 'search_div', 'roll_wise_grey_fabric_transfer_acknowledgement_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

	$search_string="%".trim($data[0]);
	$start_date =$data[1];
	$end_date =$data[2];
	$company_id =$data[3];
	$internal_ref =$data[4];
	$barcode_no =$data[5];
	$is_sales =$data[6];
	// echo $start_date.'=='.$end_date; die;
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and d.acknowledg_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and d.acknowledg_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}

	$search_field_cond="";
	if(trim($data[0])!="")
	{
		$search_field_cond="and d.id like '$search_string'";
	}
	if(!empty($barcode_no)){
		$search_field_cond .= " and c.barcode_no='$barcode_no'";
	}
	//echo $search_field_cond;die;
	if($db_type==0)
	{
		$year_field="YEAR(a.insert_date) as year,";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') as year,";
	}
	else $year_field="";//defined Later


	if($company_id) $company_cond = " and d.company_id=$company_id";

	$salesCond = "";
	$salesCondition = "";
	if(!empty($internal_ref)){
		$salesCond .= " and c.grouping='$internal_ref'";
		$salesCondition .= " and e.grouping='$internal_ref'";
	}
	
	$isSale = "";
	if($is_sales == 1){
		$isSale .= " and c.is_sales=1";
		
		$saleSql = "SELECT a.id, c.grouping
					FROM fabric_sales_order_mst a, wo_booking_dtls b, wo_po_break_down c
					WHERE a.booking_id=b.booking_mst_id and b.po_break_down_id=c.id $salesCond 
					and a.status_active=1 and a.is_deleted=0
					and b.status_active=1 and b.is_deleted=0
					and c.status_active=1 and c.is_deleted=0";
	}else{
		$saleSql = "SELECT c.id, c.grouping
					FROM wo_po_break_down c
					WHERE c.status_active=1 and c.is_deleted=0 $salesCond";
		$isSale .= " and c.is_sales!=1";
	}
	//echo $saleSql; exit();
	$saleSqlResult = sql_select($saleSql);
	$poIdArray = array();
	$poIdIRIDArray = array();
	foreach($saleSqlResult as $saleResult ){
		$poIdArray[$saleResult['ID']] = $saleResult['ID'];
		$poIdIRIDArray[$saleResult['ID']] = $saleResult['GROUPING'];
	}
	//echo "<pre>"; print_r($saleSqlResult); exit();
	$sql = "SELECT d.id as ack_id, a.id, $year_field a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, max(b.to_store) as to_store, c.po_breakdown_id
	FROM inv_item_trans_acknowledgement d, inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	where d.challan_id=a.id and a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id  and a.entry_form in(82,133) ".where_con_using_array($poIdArray, 0, 'c.po_breakdown_id')." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $isSale $company_cond $search_field_cond $date_cond  
	group by d.id, a.id, c.po_breakdown_id, a.insert_date, a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no
	order by d.id";
	//echo $sql; die;
	$result = sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="40">Ack. ID</th>
            <th width="40">Year</th>
            <th width="100">Ack. Criteria</th>
            <th width="100">From Company</th>
            <th width="100">To Company</th>
            <th width="100">To Store</th>
            <th width="50">Challan</th>
            <th width="80">Ack. date</th>
            <th width="80">IR/IB</th>
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
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="40" align="center"><p>&nbsp;<? echo $row[csf('ack_id')]; ?></p></td>
                    <td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="100"><p><? echo $item_transfer_criteria[$row[csf('transfer_criteria')]]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $company_arr[$row[csf('company_id')]]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $company_arr[$row[csf('to_company')]]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $store_arr[$row[csf('to_store')]]; ?>&nbsp;</p></td>
                    <td width="50"><p><? echo $row[csf('id')]; ?>&nbsp;</p></td>
                    <td width="80"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                    <td width="80"><p><? echo $poIdIRIDArray[$row[csf('po_breakdown_id')]] ; ?></p></td>
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

if($action=="itemTransfer_acknowledgement_popup")
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

		function fsoOnChange_event(id)
		{
			if (id==1) 
			{
				$('#txt_order_no').removeAttr('disabled','disabled');
			}
			else
			{
				$('#txt_order_no').attr('disabled','fales');
			}
		}

    </script>

	</head>

	<body>
	<div align="center" style="width:920px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:920px; margin-left:2px">
	            <table cellpadding="0" cellspacing="0" width="910" border="1" rules="all" class="rpt_table">
	                <thead>
	                	<th>FSO</th>
	                    <th>Transfer Date Range</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="100">Please Enter Transfer No</th>
	                    <th>IR/IB</th>
						<th>Sales Order No</th>
	                    <th id="booking_td_up" width="120">Booking No</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                    	<input type="hidden" name="hidden_system_id" id="hidden_system_id">
	                    </th>
	                </thead>
	                <tr class="general">
	                	<td>
							<?
							echo create_drop_down("cbo_fso_yes_no", 70, $yes_no, "", 0, "--Select--", 2, "fsoOnChange_event(this.value)");
							?>
						</td>
	                    <td align="center">
	                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
						  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
						</td>
	                    <td align="center">
	                    	<?
	                       		$search_by_arr=array(1=>"Transfer No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
							?>
	                    </td>
	                    <td align="center" id="search_by_td">
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	                    <td>
							<input type="text" style="width:80px;" class="text_boxes" name="txt_ref_no" id="txt_ref_no" placeholder="Int Ref." />
						</td>
						<td>
							<input type="text" style="width:80px;" class="text_boxes" name="txt_order_no" id="txt_order_no" placeholder="Enter Order No" disabled="" />
						</td>
	                     <td align="center" id="booking_td">
	                        <input type="text" style="width:90px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />
	                    </td>
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_to_company_id; ?>+'_'+document.getElementById('txt_booking_no').value+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+<? echo $cbo_store_name; ?>+'_'+document.getElementById('cbo_fso_yes_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_order_no').value, 'create_acknowledgement_search_list_view', 'search_div', 'roll_wise_grey_fabric_transfer_acknowledgement_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                     </td>
	                </tr>
	                <tr>
	                	<td colspan="7" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_acknowledgement_search_list_view")
{
	$data = explode("_",$data);
	// echo "<pre>";print_r($data);die;
	$search_string="%".trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$bookingNo =$data[5];
	$transfer_criteria =$data[6];
	$cbo_to_store_name =$data[7];
	$fso_yes_no=$data[8];
	$int_ref=$data[9];
	$fso_no=$data[10];

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.transfer_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.transfer_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}

	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and a.transfer_system_id like '$search_string'";
	}
	/*if(trim($data[0])!="")
	{
		if($search_by==2) $search_field_cond="and c.barcode_no like '$search_string'";
	}
	if(trim($data[0])!="")
	{
		if($search_by==3) $search_field_cond="and d.grouping like '$search_string'";
	}*/
	//echo $search_field_cond;die;
	if($db_type==0)
	{
		$year_field="YEAR(a.insert_date) as year,";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') as year,";
	}
	else $year_field="";//defined Later
	if($company_id) $company_cond = " and a.to_company=$company_id";
	if($fso_no!="") $fso_no_cond = " and d.job_no='$fso_no'";

	if ($fso_yes_no==1) // FSO Yes
	{
		if ($int_ref!="") 
		{
			$po_sql="SELECT a.id, a.po_number, a.grouping, b.booking_no, b.booking_mst_id from wo_po_break_down a, wo_booking_dtls b 
			where a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and a.grouping='$int_ref' and b.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0";
			// echo $po_sql;
			$po_sql_result=sql_select($po_sql);
			$refBookingId_cond="";
			foreach ($po_sql_result as $key => $row) 
			{
				$bookingId_arr[$row[csf('booking_mst_id')]] = $row[csf('booking_mst_id')];
			}
			$refBookingId_cond=" and d.booking_id in(".implode(",",$bookingId_arr).") ";
			//echo $refBookingId_cond;die;
		}

		$sql = "SELECT a.id, $year_field a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, a.entry_form, a.to_order_id, max(b.to_store) as to_store
		FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, fabric_sales_order_mst d
		where a.id=b.mst_id and a.to_order_id=d.id and a.entry_form in(133) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transfer_criteria=$transfer_criteria $company_cond $search_field_cond $date_cond $fso_no_cond $refBookingId_cond
		group by a.id, a.insert_date, a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, a.entry_form, a.to_order_id
		order by a.id desc";
	}
	else
	{
		if ($int_ref!="") $int_ref_cond=" and d.grouping ='$int_ref'";
		if($bookingNo !="")
		{
			//"query from plan to get program_no";
			$program_arr=array();$programIds="";
			$qry_plan=sql_select( "SELECT a.booking_no,b.id as progran_no from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.booking_no like '%$bookingNo%' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

			foreach ($qry_plan as $row)
			{
				$program_arr[$row[csf('progran_no')]]["progran_no"]=$row[csf('progran_no')];
				$programIds.="'".$row[csf('progran_no')]."'".",";
			}
			$programIds=chop($programIds,",");
		}

		if(!empty($program_arr)) $cond_for_booking=" and b.knit_program_id in($programIds)";

		$sql = "SELECT a.id, $year_field a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, a.entry_form, max(b.to_store) as to_store, d.grouping
		FROM inv_item_transfer_mst a, inv_item_transfer_dtls b 
		left join wo_po_break_down d on b.from_order_id=d.id, pro_roll_details c 
		where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.entry_form in(82) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transfer_criteria=$transfer_criteria $company_cond $search_field_cond $date_cond $cond_for_booking $int_ref_cond 
		group by a.id, a.insert_date, a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no,d.grouping, a.entry_form
		order by a.id desc";
	}
	
	//echo $sql;//die;
	$result = sql_select($sql);
	foreach ($result as $key => $row) 
	{
		$data_arr[$row[csf('id')]]['id']=$row[csf('id')];
		$data_arr[$row[csf('id')]]['transfer_prefix_number']=$row[csf('transfer_prefix_number')];
		$data_arr[$row[csf('id')]]['year']=$row[csf('year')];
		$data_arr[$row[csf('id')]]['transfer_criteria']=$row[csf('transfer_criteria')];
		$data_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
		$data_arr[$row[csf('id')]]['to_company']=$row[csf('to_company')];
		$data_arr[$row[csf('id')]]['to_store']=$row[csf('to_store')];
		$data_arr[$row[csf('id')]]['grouping'].=$row[csf('grouping')].',';
		$data_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
		$data_arr[$row[csf('id')]]['transfer_date']=$row[csf('transfer_date')];
		$data_arr[$row[csf('id')]]['entry_form']=$row[csf('entry_form')];
		$data_arr[$row[csf('id')]]['to_order_id']=$row[csf('to_order_id')];
		if ($row[csf('entry_form')]) 
		{
			$all_fso_id_arr[$row[csf('to_order_id')]] = $row[csf('to_order_id')];
		}
	}
	// echo "<pre>";print_r($data_arr);
	if(count($all_fso_id_arr)>0)
	{
		$all_fso_id = implode(",", $all_fso_id_arr);
		$fso_idCond = $all_fso_id_cond = "";

		if($db_type==2 && count($all_fso_id_arr)>999)
		{
			$barcode_arr_chunk=array_chunk($all_fso_id_arr,999) ;
			foreach($barcode_arr_chunk as $chunk_arr)
			{
				$fso_idCond.=" c.id in(".implode(",",$chunk_arr).") or ";
			}

			$all_fso_id_cond.=" and (".chop($fso_idCond,'or ').")";

		}
		else
		{
			$all_fso_id_cond=" and c.id in($all_fso_id)";
		}

		$int_ref_sql="SELECT c.id, a.po_number, a.grouping, b.booking_no, b.booking_mst_id 
		from fabric_sales_order_mst c, wo_booking_dtls b, wo_po_break_down a 
		where c.BOOKING_ID=b.BOOKING_MST_ID and b.po_break_down_id=a.id and c.within_group=1 and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $all_fso_id_cond";
		// echo $int_ref_sql;die;
		$int_ref_sql_result=sql_select($int_ref_sql);
		$int_ref_arr=array();
		foreach ($int_ref_sql_result as $key => $row) 
		{
			$int_ref_arr[$row[csf('id')]] = $row[csf('grouping')];
		}
	}



	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="60">Trans. No</th>
            <th width="40">Year</th>
            <th width="125">Transfer Criteria</th>
            <th width="130">From Company</th>
            <th width="130">To Company</th>
            <th width="120">To Store</th>
            <th width="100">IR/IB</th>
            <th width="80">Challan</th>
            <th>Transfer date</th>
        </thead>
	</table>
	<div style="width:920px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="tbl_list_search">
        	<?
            $i=1;
            foreach ($data_arr as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                $ir_ib='';
                if ($row['entry_form']==133) 
                {
                	$ir_ib=$int_ref_arr[$row['to_order_id']];
                }
                else
                {
                	$ir_ib=implode(",", array_unique(explode(",", chop($row['grouping'],","))));
                }
                
        		?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row['id']; ?>_<? echo $fso_yes_no; ?>');">
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="60" align="center"><p>&nbsp;<? echo $row['transfer_prefix_number']; ?></p></td>
                    <td width="40" align="center"><p><? echo $row['year']; ?></p></td>
                    <td width="125"><p><? echo $item_transfer_criteria[$row['transfer_criteria']]; ?>&nbsp;</p></td>
                    <td width="130"><p><? echo $company_arr[$row['company_id']]; ?>&nbsp;</p></td>
                    <td width="130"><p><? echo $company_arr[$row['to_company']]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $store_arr[$row['to_store']]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $ir_ib; ?>&nbsp;</p></td>
                    <td width="80"><p><? echo $row['challan_no']; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row['transfer_date']); ?></td>
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

if($action=="populate_requisition_data_from_data")
{
	//$sql = "select id, company_id, issue_number, knit_dye_source, knit_dye_company, issue_date, batch_no, issue_purpose from inv_issue_master where id=$data and entry_form=61";
	$sql = "SELECT a.id, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, max(b.to_store) as to_store, a.remarks, sum(b.transfer_qnty) as transfer_qnty
	from  inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
	where a.id=b.mst_id and a.entry_form=339 and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.id, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, a.remarks ";
	// echo $sql;
	$previous_transfer_qnty = return_field_value("sum(b.transfer_qnty) as transfer_qnty", "inv_item_transfer_mst a, inv_item_transfer_dtls b", "a.id=b.mst_id and a.entry_form=82 and a.transfer_requ_id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ", "transfer_qnty");
	$res = sql_select($sql);
	foreach($res as $row)
	{
		$balance=$row[csf("transfer_qnty")]-$previous_transfer_qnty;
		if ($row[csf("transfer_criteria")]==2) 
		{
			$to_company_id=$row[csf("company_id")];
			echo "$('#cbo_to_company_id').val(".$row[csf("company_id")].");\n";
		}
		else{
			$to_company_id=$row[csf("to_company")];
			echo "$('#cbo_to_company_id').val(".$row[csf("to_company")].");\n";
		}
		echo "load_drop_down( 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller','".$row[csf("transfer_criteria")].'_'.$to_company_id."', 'load_drop_store_to', 'store_td' );\n";

		echo "$('#txt_requisition_no').val('".$row[csf("transfer_system_id")]."');\n";
		echo "$('#txt_requisition_id').val(".$row[csf("id")].");\n";
		echo "$('#cbo_transfer_criteria').val(".$row[csf("transfer_criteria")].");\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','true')".";\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "$('#cbo_to_company_id').val(".$row[csf("to_company")].");\n";
		echo "$('#cbo_to_company_id').attr('disabled','true')".";\n";
		// echo "$('#txt_bar_code_num').attr('disabled','true')".";\n";
		echo "$('#txt_transfer_date').val('".change_date_format($row[csf("transfer_date")])."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#cbo_store_name').val(".$row[csf("to_store")].");\n";
		echo "$('#hidd_requi_qty').val(".$balance.");\n";
  	}
	exit();
}

if($action=="populate_data_from_acknowledgement")
{
	$sql = "SELECT c.id as ack_id, a.id, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, max(b.to_store) as to_store, a.remarks, a.transfer_requ_id, a.transfer_requ_no,a.delivery_company_name, sum(b.transfer_qnty) as transfer_qnty, c.challan_id, c.acknowledg_date
	from  inv_item_trans_acknowledgement c, inv_item_transfer_mst a, inv_item_transfer_dtls b
	where c.challan_id=a.id and a.id=b.mst_id and a.entry_form in(82,133) and a.id=$data
	group by c.id, a.id, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.remarks, a.transfer_requ_id, a.transfer_requ_no,a.delivery_company_name, c.challan_id, c.acknowledg_date ";
	//echo $sql;
	$res = sql_select($sql);
	
	foreach($res as $row)
	{
		if ($row[csf("transfer_criteria")]==2) 
		{
			$to_company_id=$row[csf("company_id")];
			echo "$('#cbo_to_company_id').val(".$row[csf("company_id")].");\n";
		}
		else{
			$to_company_id=$row[csf("to_company")];
			echo "$('#cbo_to_company_id').val(".$row[csf("to_company")].");\n";
		}
		echo "load_drop_down( 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller','".$row[csf("transfer_criteria")].'_'.$to_company_id."', 'load_drop_store_to', 'store_td' );\n";

		echo "$('#cbo_transfer_criteria').val(".$row[csf("transfer_criteria")].");\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','true')".";\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_to_company_id').attr('disabled','true')".";\n";
		echo "$('#txt_transfer_date').val('".change_date_format($row[csf("acknowledg_date")])."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#cbo_store_name').val(".$row[csf("to_store")].");\n";
		if($row[csf("transfer_criteria")] != 4)
		{
			echo "$('#cbo_store_name').attr('disabled','true')".";\n";
		}
		echo "$('#txt_transfer_no').val('".$row[csf("transfer_system_id")]."');\n";
		echo "$('#txt_transfer_no').attr('disabled','true')".";\n";
		echo "$('#txt_transfer_mst_id').val(".$row[csf("id")].");\n";
		echo "$('#txt_transfer_acknowledge_no').val(".$row[csf("ack_id")].");\n";
		echo "$('#update_id').val(".$row[csf("ack_id")].");\n";
  	}
	exit();
}

if($action=="populate_data_from_data")
{
	$sql = "SELECT a.id, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, max(b.to_store) as to_store, a.remarks, a.transfer_requ_id, a.transfer_requ_no,a.delivery_company_name, sum(b.transfer_qnty) as transfer_qnty
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b
	where a.id=b.mst_id and a.entry_form in(82,133) and a.id=$data
	group by a.id, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, a.remarks, a.transfer_requ_id, a.transfer_requ_no,a.delivery_company_name ";
	//echo $sql;
	$res = sql_select($sql);
	
	foreach($res as $row)
	{
		if ($row[csf("transfer_criteria")]==2) 
		{
			$to_company_id=$row[csf("company_id")];
			echo "$('#cbo_to_company_id').val(".$row[csf("company_id")].");\n";
		}
		else{
			$to_company_id=$row[csf("to_company")];
			echo "$('#cbo_to_company_id').val(".$row[csf("to_company")].");\n";
		}
		echo "load_drop_down( 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller','".$row[csf("transfer_criteria")].'_'.$to_company_id."', 'load_drop_store_to', 'store_td' );\n";

		echo "$('#cbo_transfer_criteria').val(".$row[csf("transfer_criteria")].");\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','true')".";\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_to_company_id').attr('disabled','true')".";\n";
		// echo "$('#txt_transfer_date').val('".change_date_format($row[csf("transfer_date")])."');\n";
		// echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#cbo_store_name').val(".$row[csf("to_store")].");\n";
		echo "$('#cbo_store_name').attr('disabled','true')".";\n";
		echo "$('#txt_transfer_no').val('".$row[csf("transfer_system_id")]."');\n";
		echo "$('#txt_transfer_mst_id').val(".$row[csf("id")].");\n";
  	}
	exit();
}

if($action=="barcode_nos_ack")
{
	$barcode_sql="SELECT barcode_no as barcode_nos from pro_roll_details where entry_form in(82,133) and status_active=1 and is_deleted=0 and mst_id=$data and re_transfer=1";
	$sql_barcode_data_arr=sql_select($barcode_sql);
	foreach ($sql_barcode_data_arr as $value) 
	{
		$barcode_nos.=$value[csf("barcode_nos")].',';
	}
	echo chop($barcode_nos,',');
	exit();
}

if($action=="barcode_nos_ack_saved")
{
	$barcode_sql="SELECT b.barcode_no as barcode_nos from inv_item_trans_acknowledgement a, pro_roll_details b where a.challan_id=b.mst_id and b.entry_form in(82,133) and b.status_active=1 and b.is_deleted=0 and b.mst_id=$data";
	$sql_barcode_data_arr=sql_select($barcode_sql);
	foreach ($sql_barcode_data_arr as $value) 
	{
		$barcode_nos.=$value[csf("barcode_nos")].',';
	}
	echo chop($barcode_nos,',');
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

			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}

			function js_set_value( str)
			{
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

				var total_selected_val = $("#hidden_selected_row_total").val()*1;

				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_individual_id' + str).val() );
					total_selected_val=total_selected_val+$('#txt_individual_qty' + str).val()*1;
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					total_selected_val=total_selected_val-$('#txt_individual_qty' + str).val()*1;
				}
				var id = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );

				$('#hidden_barcode_nos').val( id );
				$('#hidden_selected_row_total').val( total_selected_val.toFixed(2));
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

			function check_all_data()
			{
				var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

				tbl_row_count = tbl_row_count-1;
				for( var i = 1; i <= tbl_row_count; i++ )
				{
					if($("#search"+i).css("display") != "none")
					{
						js_set_value( i );
					}
				}
			}

	    </script>

	</head>

	<body>
	<div align="center" style="width:1360px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:1360px; margin-left:2px;">
			<legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="1360" border="1" rules="all" class="rpt_table">
	                <thead>
	                    <th>Type</th>
	                    <th>Year</th>
	                    <th>Location</th>
	                    <th>Buyer</th>
	                    <th>Job</th>
	                    <th>Order No</th>
	                    <th>File No</th>
	                    <th>Internal Ref No</th>
	                    <th>Barcode No</th>
	                    <th>Booking No</th>
	                    <th>Store Name</th>
	                    <th>Shipment Date</th>
	                    <th>Status</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                        <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">
	                    </th>
	                </thead>
	                <tr class="general">
	                	<td>
	                    <?
							echo create_drop_down("cbo_order_status", 80, array(1=>"With Order",2=>"Sample Booking"), "", 0, "", 1, "");
						?>
	                    </td>
                    	<td>
						<?php
						echo create_drop_down( "cbo_year_selection", 65, create_year_array(),"", 0,"-- --", date("Y",time()), "",0,"" );
						?>
						</td>
	                    <td>
	                    <?
							echo create_drop_down( "cbo_location_name", 100, "select id,location_name from lib_location where company_id=$company_id and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down( 'roll_wise_grey_fabric_transfer_acknowledgement_controller',this.value, 'load_drop_store_2', 'store_td_id' );" );
						?>
	                    </td>
	                    <td>
						<?
							echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "","" );
						?>
                    	</td>
                    	<td align="center">
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_job_no" id="txt_job_no" />
	                    </td>
	                    <td align="center">
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_order_no" id="txt_order_no" />
	                    </td>
	                    <td align="center">
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_file_no" id="txt_file_no" />
	                    </td>
	                    <td align="center">
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_ref_no" id="txt_ref_no" />
	                    </td>
	                    <td align="center"><input type="text" name="barcode_no" id="barcode_no" style="width:80px" class="text_boxes" /></td>
	                    <td align="center">
	                        <input type="text" style="width:100px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />
	                    </td>
	                    <td id="store_td_id">
						<?
							echo create_drop_down("cbo_store_name", 100, $blank_array, "", 1, "-- Select Store--", 0, "");
						?>
						</td>
						<td>
                    		<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" readonly>
                        	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" readonly>
                    	</td>
						<td>
                        <?
                        	echo create_drop_down( "cbo_status", 90, $row_status,"", 1, "- Select -", $selected, "","","1,3" );
                        ?>
                    	</td>

	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('barcode_no').value+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+<? echo $cbo_store_name; ?>+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_store_name').value+'_'+document.getElementById('cbo_order_status').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_status').value, 'create_barcode_search_list_view', 'search_div', 'roll_wise_grey_fabric_transfer_acknowledgement_controller', 'setFilterGrid(\'tbl_list_search\',-1,tableFilters);reset_hide_field();')" style="width:100px;" />
	                     </td>
	                </tr>
	                <tr>
                    	<td colspan="14" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                	</tr>
	           </table>
	           <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script>
		var tableFilters =
		{
			col_operation: {
				id: ["value_total_selected_value_td"],
				col: [16],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
		setFilterGrid("tbl_list_search",-1,tableFilters);
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action=="to_order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$item_desc_ids="'$item_desc_ids'";
	$item_gsm="'$item_gsm'";
	$item_dia="'$item_dia'";
	$txt_requisition_basis="'$txt_requisition_basis'";
	?>
	<script>
        function js_set_value(data)
        {
            $('#order_id').val(data);
            parent.emailwindow.hide();
        }
    </script>
	</head>
	<body>
	<div align="center" style="width:980px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:970px;margin-left:10px">
			<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="900" class="rpt_table">
					<thead>
						<th>Buyer Name</th>
						<th>Order No</th>
						<th width="180">Shipment Date Range</th>
						<th width="100">Booking No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="order_id" id="order_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<?
								echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_to_company_id' order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",'' );
								//echo "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_to_company_id' order by buy.buyer_name";
							?>
						</td>
						<td>
							<input type="text" style="width:150px;" class="text_boxes" name="txt_order_no" id="txt_order_no" />
						</td>
						<td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" readonly>
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" readonly>
						</td>
						<td>
							<input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:80px" placeholder="Booking No">
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_to_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $item_desc_ids; ?>+'_'+<? echo $item_gsm; ?>+'_'+<? echo $item_dia; ?>+'_'+<? echo $txt_requisition_basis; ?>, 'create_po_search_list_view', 'search_div', 'roll_wise_grey_fabric_transfer_acknowledgement_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="margin-top:10px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=='create_po_search_list_view')
{
	$data=explode('_',$data);

	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string=trim($data[1]);
	$company_id=$data[2];

	if ($data[3]!="" &&  $data[4]!="")
	{
		if($db_type==0)
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else
		$shipment_date ="";

	$type=$data[5];

	$item_desc_ids=$data[7];
	$item_gsm=$data[8];
	$item_dia=$data[9];
	$txt_requisition_basis=$data[10];

	if ($txt_requisition_basis==1) 
	{
		$composition_arr=array(); $constructtion_arr=array();
		$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array=sql_select($sql_deter);
		foreach( $data_array as $row )
		{
			$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
			$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		$item_desc_id_arr=array_filter(array_unique(explode(',', $item_desc_ids)));// 9,10
		$item_gsm=implode(",", array_filter(array_unique(explode(',', $item_gsm))));
		$item_dia_arr=array_filter(array_unique(explode(',', $item_dia)));
		// print_r($item_desc_id_arr);
		// echo $item_gsm_arr;
		$item_dia="";
	    foreach ($item_dia_arr as $key => $value) 
	    {
	        if ($item_dia=="") 
	        {
	            $item_dia.= $value;
	        }
	        else 
	        {
	            $item_dia.= "','".$value;
	        }
	    }
	    // echo $item_dia;

	    $fabric_construction="";$fabric_composition="";
	    foreach ($item_desc_id_arr as $key => $value) 
	    {
	        if ($fabric_construction=="") 
	        {
	            $fabric_construction.= $constructtion_arr[$value];
	        }
	        else 
	        {
	            $fabric_construction.= "','".$constructtion_arr[$value];
	        }
	        if ($fabric_composition=="") 
	        {
	            $fabric_composition.= $composition_arr[$value];
	        }
	        else 
	        {
	            $fabric_composition.= "','".$composition_arr[$value];
	        }
	    }
	}
	if($fabric_construction!="") $fabric_construction_cond=" and c.construction in ('$fabric_construction')";
	if($fabric_composition!="") $fabric_composition_cond=" and c.copmposition in ('$fabric_composition')";
	if($item_dia!="") $item_dia_cond=" and c.dia_width in ('$item_dia')";
	if($item_gsm!="") $item_gsm_cond=" and c.gsm_weight in ($item_gsm)";
	

	

	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	$status_cond=" and b.status_active=1";

	$bookingNo= trim($data[5]);
	if($bookingNo!="") $booking_cond=" and c.booking_no like '%$bookingNo%'";
	$po_cond="";
	if($search_string!="")
	$po_cond=" and b.po_number ='$search_string'";
	
	

	/*if($db_type==0) $year_field_cond="and YEAR(a.insert_date)='$data[6]'";
	else if($db_type==2) $year_field_cond="and to_char(a.insert_date,'YYYY')='$data[6]'";
	else $year_field_cond="";*/
	//defined Later
	//$sql= "select a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $shipment_date order by b.id, b.pub_shipment_date";
	$sql= "SELECT a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date,c.booking_no
	from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c  
	where a.job_no=b.job_no_mst  and b.id=c.po_break_down_id and a.company_name=$company_id and a.buyer_name like '$buyer' $po_cond and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.booking_type in (1,4) $status_cond $shipment_date $booking_cond $year_field_cond $fabric_construction_cond $fabric_composition_cond $item_dia_cond $item_gsm_cond
	group by a.job_no,to_char(a.insert_date,'YYYY'), a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date,c.booking_no order by b.id, b.pub_shipment_date";
	// echo $sql;die;
	$arr=array(2=>$company_arr,3=>$buyer_arr);
	echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date,Booking No", "70,60,70,80,120,90,110,90,80,80","950","200",0, $sql , "js_set_value", "id,po_number,job_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,booking_no", "",'','0,0,0,0,0,1,0,1,3,0');

	exit();
}

if($action=="grey_issue_print")
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
	$buyer_library=return_library_array("select id, short_name from lib_buyer","id","short_name");
	$brand_library=return_library_array("select id, brand_name from lib_brand","id","brand_name");

	/*$location_arr=return_library_array("select id, location_name from  lib_location",'id','location_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand",'id','brand_name');
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$booking_arr=return_library_array( "select id, booking_no from wo_booking_mst where item_category in(2,13)", "id", "booking_no");
	$pi_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");*/

	$lib_mc_arr=array();
	$mc_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach($mc_sql as $row)
	{
		$lib_mc_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}

	/*$ppl_mc_arr=array();
	if($db_type==0) $machine_cond=" machine_id<>''"; else $machine_cond=" machine_id is not null";
	$ppl_mc_sql=sql_select("select id, machine_id, machine_dia as dia_width, machine_gg as gauge from  ppl_planning_info_entry_dtls where $machine_cond");
	foreach($ppl_mc_sql as $row)
	{
		$mc_id_arr=array_unique(explode(",",$row[csf('machine_id')]));
		foreach($mc_id_arr as $mc_id)
		{
			$ppl_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
			$ppl_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
		}

		$ppl_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$ppl_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];

	}*/
	//echo "";

	$dataArray=sql_select("select id, transfer_system_id, transfer_criteria, transfer_date, item_category, challan_no, remarks from inv_item_transfer_mst where id=$update_id");

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

	?>
    <div>
        <table width="1080" cellspacing="0">
            <tr>
                <td colspan="9" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
            </tr>
            <!--<tr>
                <td colspan="9">&nbsp;</td>
            </tr>-->
            <tr>
                <td colspan="9" align="center" style="font-size:16px; font-weight:bold">Grey Fabric Transfer Entry Report</td>
            </tr>
            <tr>
                <td colspan="9">&nbsp;</td>
            </tr>
            <tr>
            	<td width="150"><strong>Transfer ID</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="200"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
                
                <td width="150"><strong>Transfer Criteria</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="200"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
                
                <td width="150"><strong>Item Category</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="200"><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
            </tr>
            <tr>
            	<td><strong>Transfer Date</strong></td>
                <td><strong>:</strong></td>
                <td><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
                
                <td><strong>Challan No</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $dataArray[0][csf('challan_no')]; ?></td>
                
                <td><strong>Remarks</strong></td>
                <td><strong>:</strong></td>
                <td><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="2180"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th width="20">SL</th>
                <th width="100">From Store</th>
                <th width="100">To Store</th>
                <th width="50">File No</th>
                <th width="50">Reff No</th>
                <th width="100">Buyer <br> Job</th>
                <th width="80">Production Basis</th>
                <th width="100">Delivery Challan No</th>
                <th width="90">Prog./ Book. No</th>
                <th width="80">Fab Color</th>
                <th width="80">Color Range</th>
                <th width="100">Fab. Constraction</th>
                <th width="100">Fab. Compositon</th>
                <th width="50">Fin GSM</th>
                <th width="50">Fab. Dia</th>
                <th width="100">Count</th>
                <th width="100">Yarn Brand</th>
                <th width="80">Yarn Lot</th>
                <th width="50">Stich Lenth</th>
                <th width="80">Machine No</th>
                <th width="60">MC Dia X Gauge</th>
                <th width="100">Barcode No</th>
                <th width="40">UOM</th>
                <th width="50">Roll No</th>
                <th width="50">Transfered Qnty</th>
                <th width="70">Floor</th>
                <th width="70">Room</th>
                <th width="70">Rack</th>
                <th width="70">Shelf</th>
                <th>Bin/Box</th>
            </thead>
            <?
				$delivery_challan_arr=return_library_array( "select id,sys_number from pro_grey_prod_delivery_mst where entry_form=56",'id','sys_number');
				$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
				$prod_basis_arr=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
				$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
				$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
				$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1",'id','buyer_id');
				$i=1; $tot_qty=0;
				$transfer_criteria=$dataArray[0][csf('transfer_criteria')];
				$sql = "SELECT a.id as mst_id, b.id as dtls_id, b.from_store, b.to_store, b.from_order_id, b.to_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.knit_program_id, b.prod_detls_id, b.from_trans_entry_form, b.gsm, b.dia_width, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, c.qnty, c.roll_no, c.roll_id, c.barcode_no, c.booking_without_order, c.po_breakdown_id
				from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
				where a.id=b.mst_id and b.id=c.dtls_id and a.id=$update_id and c.entry_form=82 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  order by c.roll_no";
            	//echo $sql;
				$result=sql_select($sql);
				$all_roll_id=$all_po_id=$all_details_id="";
				foreach($result as $row)
				{
					if($row[csf("roll_id")]!="") $all_roll_id.=$row[csf("roll_id")].",";
					if($row[csf("from_order_id")]!="") $all_po_id.=$row[csf("from_order_id")].",";
					if($row[csf("prod_detls_id")]!="") $all_details_id.=$row[csf("prod_detls_id")].",";
				}
				$all_roll_id=implode(",",array_unique(explode(",",chop($all_roll_id,","))));
				$all_po_id=implode(",",array_unique(explode(",",chop($all_po_id,","))));
				$all_details_id=implode(",",array_unique(explode(",",chop($all_details_id,","))));
				if($all_roll_id!="")
				{
					$production_sql=sql_select("select b.receive_basis, b.booking_id, b.booking_no, c.id as prod_detls_id, c.color_id, c.color_range_id, c.machine_dia, c.machine_gg, c.machine_no_id, c.yarn_lot, d.id as roll_id, d.roll_no, d.barcode_no
					from inv_receive_master b, pro_grey_prod_entry_dtls c , pro_roll_details d
					where b.id=c.mst_id and c.id=d.dtls_id and d.roll_id=0 and d.id in($all_roll_id)");

					$production_delivery_data=array();
					foreach($production_sql as $row)
					{
						$production_delivery_data[$row[csf("roll_id")]]["receive_basis"]=$row[csf("receive_basis")];
						$production_delivery_data[$row[csf("roll_id")]]["booking_id"]=$row[csf("booking_id")];
						$production_delivery_data[$row[csf("roll_id")]]["booking_no"]=$row[csf("booking_no")];
						$production_delivery_data[$row[csf("roll_id")]]["color_id"]=$row[csf("color_id")];
						$production_delivery_data[$row[csf("roll_id")]]["color_range_id"]=$row[csf("color_range_id")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_dia"]=$row[csf("machine_dia")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_gg"]=$row[csf("machine_gg")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_no_id"]=$row[csf("machine_no_id")];
						$production_delivery_data[$row[csf("roll_id")]]["yarn_lot"]=$row[csf("yarn_lot")];

						if($production_delivery_data[$row[csf("roll_id")]]["receive_basis"] == 2)
						{
							$program_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
						}
					}
				}

				if(!empty($program_id_arr))
				{
					$booking_from_program = return_library_array("select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id  = b.mst_id and b.status_active = 1 and b.is_deleted = 0 and b.id in (".implode(",", $program_id_arr).") ","id","booking_no");
				}

				if($all_po_id!="")
				{
					$job_array=array();
					$job_sql="select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($all_po_id)";
					//echo $job_sql;
					$job_sql_result=sql_select($job_sql);
					foreach($job_sql_result as $row)
					{
						$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
						$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
						$job_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
						$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
						$job_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
						$job_array[$row[csf('id')]]['grouping']=$row[csf('grouping')];
					}
				}
				
				/*
				|--------------------------------------------------------------------------
				| for floor, room, rack and shelf disable
				|--------------------------------------------------------------------------
				|
				*/
				$floorRoomRackShelf_array=return_library_array( "SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");

				$i=0;
				foreach($result as $row)
				{
					$i++;
					?>
                    <tr>
                        <td align="center"><? echo $i; ?></td>
                        <td style="word-break:break-all;"><? echo $store_arr[$row[csf('from_store')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $store_arr[$row[csf('to_store')]]; ?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						if($row[csf('booking_without_order')]==1) echo "&nbsp;"; else echo $job_array[$row[csf('from_order_id')]]['file_no'] ;
						?></td>

                        <td style="word-break:break-all;" align="center"><? if($row[csf('booking_without_order')]==1) echo "&nbsp;"; else echo $job_array[$row[csf('from_order_id')]]['grouping']; ?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						if($row[csf('booking_without_order')]==1)
						{
							echo $buyer_library[$book_buyer_arr[$row[csf('po_breakdown_id')]]];
							//echo $row[csf('po_breakdown_id')];
						}
						else
						{
							echo $buyer_library[$job_array[$row[csf('from_order_id')]]['buyer']]. "<br>".$job_array[$row[csf('from_order_id')]]['job'];
						}
						?></td>
                        <td style="word-break:break-all;"><? echo $prod_basis_arr[$production_delivery_data[$row[csf("roll_id")]]["receive_basis"]]; ?></td>
                        <td style="word-break:break-all;" align="center"><? if($row[csf("from_trans_entry_form")]==58) echo $delivery_challan_arr[$row[csf("knit_program_id")]]; else echo "";?></td>
                        <td style="word-break:break-all;" align="left">
                        	<?
                        	if($production_delivery_data[$row[csf("roll_id")]]["receive_basis"] == 2)
                        	{
                        		$program_no = $production_delivery_data[$row[csf("roll_id")]]["booking_no"];
                        		$booking_number = $booking_from_program[$program_no];
                        	}
                        	else
                        	{
                        		$booking_number = $production_delivery_data[$row[csf("roll_id")]]["booking_no"];
                        	}

                        	echo  "P: ".$program_no."<br />B: ".$booking_number;//$production_delivery_data[$row[csf("roll_id")]]["booking_no"];


                        	?>
                        </td>
                        <td style="word-break:break-all;" align="center">
						<?
						$all_color_arr=array_unique(explode(",",$production_delivery_data[$row[csf("roll_id")]]["color_id"]));
						$all_color="";
						foreach($all_color_arr as $color_id)
						{
							$all_color.=$color_library[$color_id].",";
						}
						$all_color=chop($all_color,",");
						echo $all_color;
						?></td>
                        <td style="word-break:break-all;" align="center"><? echo $color_range[$production_delivery_data[$row[csf("roll_id")]]["color_range_id"]]; ?></td>
                        <td style="word-break:break-all;"><? echo $constructtion_arr[$row[csf('feb_description_id')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $composition_arr[$row[csf('feb_description_id')]]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf("gsm")]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf("dia_width")]; ?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						//echo $yarn_count_arr[$row[csf('y_count')]];
						$yarn_count_array=array_unique(explode(",",$row[csf('y_count')]));
						$all_count="";
						foreach($yarn_count_array as $count_id)
						{
							$all_count.=$yarn_count_arr[$count_id].",";
						}
						$all_count=chop($all_count,",");
						echo $all_count;
						?></td>
                        <td style="word-break:break-all;"><?  echo $brand_library[$row[csf("brand_id")]]; ?></td>
                        <td style="word-break:break-all;"><? echo $production_delivery_data[$row[csf("roll_id")]]["yarn_lot"]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $lib_mc_arr[$production_delivery_data[$row[csf("roll_id")]]["machine_no_id"]]['no']; ?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						echo $production_delivery_data[$row[csf("roll_id")]]["machine_dia"].'X'.$production_delivery_data[$row[csf("roll_id")]]["machine_gg"];
						?></td>
                        <td style="word-break:break-all;"><?  echo $row[csf("barcode_no")]; ?></td>
                        <td align="center">Kg</td>
                        <td align="center"><? echo $row[csf("roll_no")];?></td>
                        <td align="right"><? echo number_format($row[csf('qnty')],2,'.','');?></td>
                        <td style="word-break:break-all;"><? echo $floorRoomRackShelf_array[$row[csf('to_floor_id')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $floorRoomRackShelf_array[$row[csf('to_room')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $floorRoomRackShelf_array[$row[csf('to_rack')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $floorRoomRackShelf_array[$row[csf('to_self')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $floorRoomRackShelf_array[$row[csf('to_bin_box')]]; ?></td>
                    </tr>
                	<?
					$tot_qty+=$row[csf('qnty')];
				}
			?>
            <tfoot>
            	<tr>
                	<th align="right" colspan="23"><strong>Total</strong></th>
                    <th><? echo $i; ?></th>
                    <th align="right"><? echo number_format($tot_qty,2,'.',''); ?></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
		</table>
        <br>
        <?
        	echo signature_table(94, $company, "2180px");
        ?>
	</div>
	<?
	exit();
}

if($action=="grey_issue_print_2")
{
	echo load_html_head_contents("Grey Fabric Transfer Entry Report","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode('*',$data);

	$company=$data[0];
	$txt_issue_no=$data[1];
	$update_id=$data[2];
	$operation_btn=$data[4];
	$delv_company=$data[5];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}
	$buyer_library=return_library_array("select id, short_name from lib_buyer","id","short_name");
	$brand_library=return_library_array("select id, brand_name from lib_brand","id","brand_name");


	$lib_mc_arr=array();
	$mc_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach($mc_sql as $row)
	{
		$lib_mc_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}


	$dataArray=sql_select("select id, transfer_system_id, transfer_criteria, transfer_date, item_category, challan_no, remarks from inv_item_transfer_mst where id=$update_id");

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array);

	?>
    <div>
        <table width="1000" cellspacing="0">
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong>
                	<?
                	    if($operation_btn==7) {echo $company_array[$company]['name'];}
                		else { echo "Delivery Company: ".  $delv_company."<br>";
                				echo "LC Company: ". $company_array[$company]['name'];}
                	?></strong></td>
            </tr>
             <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px; font-weight:bold">Grey Fabric Transfer Entry Report</td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
            	<td width="130"><strong>Transfer ID :</strong></td>
                <td width="180"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
                <td width="130"><strong>Transfer Criteria:</strong></td>
                <td width="180"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
                <td width="130"><strong>Item Category:</strong></td>
                <td ><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
            </tr>
            <tr>
            	<td ><strong>Transfer Date:</strong></td>
                <td ><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
                <td ><strong>Challan No:</strong></td>
                <td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td ><strong>Remarks:</strong></td>
                <td ><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="1900"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th width="20">SL</th>
                <th width="100">Delivery Challan No</th>
                <th width="100">Buyer <br> Job</th>
                <th width="90">Prog./ Book. No</th>
                <th width="100">Fab. Constraction</th>
                <th width="100">Fab. Compositon</th>
                <th width="50">Fin GSM</th>
                <th width="80">Fab Color</th>
                <th width="60">MC Dia X Gauge</th>
                <th width="50">Fab. Dia</th>
                <th width="50">Stich Length</th>
                <th width="100">Count</th>
                <th width="100">Yarn Brand</th>
                <th width="80">Yarn Lot</th>
                <th width="80">Machine No</th>
                <th width="50">Total Roll</th>
                <th width="50">Transfered Qnty</th>
                <th width="100">From Store</th>
                <th width="100">To Store</th>


            </thead>
            <?
				$delivery_challan_arr=return_library_array( "select id,sys_number from pro_grey_prod_delivery_mst where entry_form=56",'id','sys_number');
				$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
				$prod_basis_arr=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
				$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
				$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
				$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1",'id','buyer_id');
				$i=1; $tot_qty=0;
				$transfer_criteria=$dataArray[0][csf('transfer_criteria')];
				$sql = "select a.id as mst_id, b.id as dtls_id, b.from_store, b.to_store, b.from_order_id, b.to_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.knit_program_id, b.prod_detls_id, b.from_trans_entry_form, b.gsm, b.dia_width, c.qnty, c.roll_no, c.roll_id, c.barcode_no, c.booking_without_order, c.po_breakdown_id
				from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
				where a.id=b.mst_id and b.id=c.dtls_id and a.id=$update_id and c.entry_form=82 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  order by c.roll_no";
            	//echo $sql;
				$result=sql_select($sql);
				$all_roll_id=$all_po_id=$all_details_id="";
				foreach($result as $row)
				{
					if($row[csf("roll_id")]!="") $all_roll_id.=$row[csf("roll_id")].",";
					if($row[csf("barcode_no")]!="") $all_barcode_no.=$row[csf("barcode_no")].",";
					if($row[csf("from_order_id")]!="") $all_po_id.=$row[csf("from_order_id")].",";
					if($row[csf("prod_detls_id")]!="") $all_details_id.=$row[csf("prod_detls_id")].",";
				}
				$all_roll_id=implode(",",array_unique(explode(",",chop($all_roll_id,","))));
				$all_barcode_no=implode(",",array_unique(explode(",",chop($all_barcode_no,","))));
				$all_po_id=implode(",",array_unique(explode(",",chop($all_po_id,","))));
				$all_details_id=implode(",",array_unique(explode(",",chop($all_details_id,","))));
				if($all_roll_id!="")
				{
					$production_sql=sql_select("select b.receive_basis, b.booking_id, b.booking_no, c.id as prod_detls_id, c.color_id, c.color_range_id, c.machine_dia, c.machine_gg, c.machine_no_id, c.yarn_lot, d.id as roll_id, d.roll_no, d.barcode_no
					from inv_receive_master b, pro_grey_prod_entry_dtls c, pro_roll_details d
					where b.id=c.mst_id and c.id=d.dtls_id and d.roll_id=0 and d.barcode_no in($all_barcode_no) and d.entry_form in (2,22)");  //and d.id in($all_roll_id)

					$production_delivery_data=array();
					foreach($production_sql as $row)
					{
						$production_delivery_data[$row[csf("barcode_no")]]["receive_basis"]=$row[csf("receive_basis")];
						$production_delivery_data[$row[csf("barcode_no")]]["booking_id"]=$row[csf("booking_id")];
						$production_delivery_data[$row[csf("barcode_no")]]["booking_no"]=$row[csf("booking_no")];
						$production_delivery_data[$row[csf("barcode_no")]]["color_id"]=$row[csf("color_id")];
						$production_delivery_data[$row[csf("barcode_no")]]["color_range_id"]=$row[csf("color_range_id")];
						$production_delivery_data[$row[csf("barcode_no")]]["machine_dia"]=$row[csf("machine_dia")];
						$production_delivery_data[$row[csf("barcode_no")]]["machine_gg"]=$row[csf("machine_gg")];
						$production_delivery_data[$row[csf("barcode_no")]]["machine_no_id"]=$row[csf("machine_no_id")];
						$production_delivery_data[$row[csf("barcode_no")]]["yarn_lot"]=$row[csf("yarn_lot")];

						if($production_delivery_data[$row[csf("barcode_no")]]["receive_basis"] == 2)
						{
							$program_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
						}
					}
				}



				if(!empty($program_id_arr))
				{
					$booking_from_program = return_library_array("select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id  = b.mst_id and b.status_active = 1 and b.is_deleted = 0 and b.id in (".implode(",", $program_id_arr).") ","id","booking_no");
				}

				if($all_po_id!="")
				{
					$job_array=array();
					$job_sql="select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($all_po_id)";
					//echo $job_sql;
					$job_sql_result=sql_select($job_sql);
					foreach($job_sql_result as $row)
					{
						$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
						$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
						$job_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
						$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
						$job_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
						$job_array[$row[csf('id')]]['grouping']=$row[csf('grouping')];
					}
				}

				foreach($result as $row)
				{
					$all_color_arr=array_unique(explode(",",$production_delivery_data[$row[csf("barcode_no")]]["color_id"]));
					$all_color="";
					foreach($all_color_arr as $color_id)
					{
						$all_color.=$color_library[$color_id].",";
					}

					$all_color=chop($all_color,",");

					if($row[csf("from_trans_entry_form")]==58)
						$delivery_challan_no =  $delivery_challan_arr[$row[csf("knit_program_id")]];
					else $delivery_challan_no = "";

					if($row[csf('booking_without_order')]==1)
					{
						$job_buyer_no =  $buyer_library[$book_buyer_arr[$row[csf('po_breakdown_id')]]];
					}
					else
					{
						$job_buyer_no = $buyer_library[$job_array[$row[csf('from_order_id')]]['buyer']]. "<br>".$job_array[$row[csf('from_order_id')]]['job'];
					}

					if($production_delivery_data[$row[csf("barcode_no")]]["receive_basis"] == 2)
                	{
                		$program_no = $production_delivery_data[$row[csf("barcode_no")]]["booking_no"];
                		$booking_number = $booking_from_program[$program_no];
                	}
                	else
                	{
                		$booking_number = $production_delivery_data[$row[csf("barcode_no")]]["booking_no"];
                	}

                	$program_booking_no =   "P: ".$program_no."<br />B: ".$booking_number;

					$machine_dia_gg = $production_delivery_data[$row[csf("barcode_no")]]["machine_dia"].'X'.$production_delivery_data[$row[csf("barcode_no")]]["machine_gg"];

					$yarn_count_array=array_unique(explode(",",$row[csf('y_count')]));
					$all_count="";
					foreach($yarn_count_array as $count_id)
					{
						$all_count.=$yarn_count_arr[$count_id].",";
					}
					$all_count=chop($all_count,",");

					$brand_no =  $brand_library[$row[csf("brand_id")]];
                    $lot_no =  $production_delivery_data[$row[csf("barcode_no")]]["yarn_lot"];
                    $machine_no =  $lib_mc_arr[$production_delivery_data[$row[csf("barcode_no")]]["machine_no_id"]]['no'];
                    $from_store_no =  $store_arr[$row[csf('from_store')]];
                    $to_store_no = $store_arr[$row[csf('to_store')]];


					$data_array[$all_color][$program_booking_no][$delivery_challan_no."**".$job_buyer_no."**".$program_booking_no."**".$row[csf('feb_description_id')]."**".$row[csf("gsm")]."**".$machine_dia_gg."**".$row[csf("dia_width")]."**".$row[csf("stitch_length")]."**".$all_count."**".$brand_no."**".$lot_no."**".$machine_no."**".$from_store_no."**".$to_store_no]["qnty"] += $row[csf('qnty')];

					$data_array[$all_color][$program_booking_no][$delivery_challan_no."**".$job_buyer_no."**".$program_booking_no."**".$row[csf('feb_description_id')]."**".$row[csf("gsm")]."**".$machine_dia_gg."**".$row[csf("dia_width")]."**".$row[csf("stitch_length")]."**".$all_count."**".$brand_no."**".$lot_no."**".$machine_no."**".$from_store_no."**".$to_store_no]["roll_count"]++;
				}

				$i=1;
				foreach ($data_array as $color_no => $color_data)
				{
					foreach ($color_data as $prog_book_no => $prog_book_arr)
					{
						foreach ($prog_book_arr as $data_key_string => $value)
						{
							$key_arr = explode("**", $data_key_string);

							?>
							<tr>
		                        <td align="center"><? echo $i; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $key_arr[0];?></td>
		                        <td style="word-break:break-all;" align="center"><?	echo $key_arr[1];?></td>
								<td style="word-break:break-all;" align="left"><? echo $key_arr[2];?></td>

		                        <td style="word-break:break-all;" align="center"><? echo $constructtion_arr[$key_arr[3]]; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $composition_arr[$key_arr[3]]; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $key_arr[4]; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $color_no;?>	</td>
								<td style="word-break:break-all;" align="center"><? echo $key_arr[5];?>	</td>

								<td style="word-break:break-all;" align="center"><? echo $key_arr[6]; ?></td>
								<td style="word-break:break-all;" align="center"><? echo $key_arr[7]; ?></td>
								<td style="word-break:break-all;" align="center"><? echo $key_arr[8];?></td>

		                        <td style="word-break:break-all;" align="center"><?  echo $key_arr[9]; ?></td>

		                        <td style="word-break:break-all;" align="center"><? echo $key_arr[10]; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $key_arr[11]; ?>  </td>

		                        <td align="right"><? echo $value['roll_count'];?></td>
		                        <td align="right"><? echo number_format($value['qnty'],2,'.','');?></td>
		                        <td style="word-break:break-all;" align="center"><p><? echo $key_arr[12]; ?></p></td>
		                        <td style="word-break:break-all;" align="center"><p><? echo $key_arr[13]; ?></p></td>
	                    	</tr>
							<?
							$i++;
							$sub_color_tot_roll += $value['roll_count'];
							$sub_booking_tot_roll += $value['roll_count'];
							$grand_tot_roll += $value['roll_count'];

							$sub_color_tot_qnty += $value['qnty'];
							$sub_booking_tot_qnty += $value['qnty'];
							$grand_tot_qnty += $value['qnty'];
						}
						?>
						<tr bgcolor="#eeeded" style="font-weight: bold;">
	                        <td colspan="15" align="right">Booking Color wise Total</td>
	                        <td align="right"><? echo $sub_color_tot_roll;?></td>
	                        <td align="right"><? echo number_format($sub_color_tot_qnty,2,'.','');?></td>
	                        <td colspan="2">&nbsp;</td>
	                	</tr>
						<?
						$sub_color_tot_roll =0;$sub_color_tot_qnty=0;
					}
					?>
					<tr bgcolor="#eeeded" style="font-weight: bold;">
                        <td colspan="15" align="right">Booking wise Total</td>
                        <td align="right"><? echo $sub_booking_tot_roll;?></td>
                        <td align="right"><? echo number_format($sub_booking_tot_qnty,2,'.','');?></td>
                        <td colspan="2">&nbsp;</td>
                	</tr>
					<?
					$sub_booking_tot_roll =0;$sub_booking_tot_qnty=0;

				}
			?>
            <tfoot>
            	<tr>
                	<th align="right" colspan="15"><strong>Total</strong></th>
                    <th align="center"><? echo $grand_tot_roll; ?></th>
                    <th align="right"><? echo number_format($grand_tot_qnty,2,'.',''); ?></th>
                    <th colspan="2">&nbsp;</th>
                </tr>
            </tfoot>
		</table>
        <br>
        <?
        	echo signature_table(94, $company, "1900px");
        ?>
	</div>
	<?
	exit();
}

if($action=="grey_issue_print_gropping_2")
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
	$buyer_library=return_library_array("select id, short_name from lib_buyer","id","short_name");
	$brand_library=return_library_array("select id, brand_name from lib_brand","id","brand_name");
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');


	/*$location_arr=return_library_array("select id, location_name from  lib_location",'id','location_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand",'id','brand_name');
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$booking_arr=return_library_array( "select id, booking_no from wo_booking_mst where item_category in(2,13)", "id", "booking_no");
	$pi_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");*/

	$lib_mc_arr=array();
	$mc_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach($mc_sql as $row)
	{
		$lib_mc_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}

	$ppl_mc_arr=array();
	if($db_type==0) $machine_cond=" machine_id<>''"; else $machine_cond=" machine_id is not null";

	/*$ppl_mc_sql=sql_select("select id, machine_id, machine_dia as dia_width, machine_gg as gauge from  ppl_planning_info_entry_dtls where $machine_cond");
	foreach($ppl_mc_sql as $row)
	{
		$mc_id_arr=array_unique(explode(",",$row[csf('machine_id')]));
		foreach($mc_id_arr as $mc_id)
		{
			$ppl_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
			$ppl_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
		}

		$ppl_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$ppl_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}*/
	//echo "";

	$dataArray=sql_select("select id, transfer_system_id, transfer_criteria, transfer_date, item_category, challan_no from inv_item_transfer_mst where id=$update_id");


	$dataArray_dtls=sql_select("select mst_id,from_store,to_store from inv_item_transfer_dtls where mst_id=$update_id group by mst_id,from_store,to_store");
	$toFrom_store=array();
	foreach ($dataArray_dtls as $row) {
		$toFrom_store[$row[csf('mst_id')]]["from_store"]=$row[csf('from_store')];
		$toFrom_store[$row[csf('mst_id')]]["to_store"]=$row[csf('to_store')];
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

	?>
    <div>
        <table width="1000" cellspacing="0">
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
            </tr>
             <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px; font-weight:bold">Grey Fabric Transfer Entry Report</td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
            	<td width="130"><strong>Transfer ID :</strong></td>
                <td width="180"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
                <td width="130"><strong>Transfer Criteria:</strong></td>
                <td width="180"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
                <td width="130"><strong>Item Category:</strong></td>
                <td ><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
            </tr>
            <tr>
            	<td ><strong>Transfer Date:</strong></td>
                <td ><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
                <td ><strong>Challan No:</strong></td>
                <td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
            </tr>
			<tr>
            	<td ><strong>From Store</strong></td>
                <td ><? echo $store_arr[$toFrom_store[$dataArray[0][csf('id')]]["from_store"]] ; ?></td>
                <td ><strong>To Store</strong></td>
                <td ><? echo $store_arr[$toFrom_store[$dataArray[0][csf('id')]]["to_store"]]; ?></td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="1500"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th width="20">SL</th>
                <th width="100">Buyer <br> Job</th>
                <th width="80">Production Basis</th>
                <th width="100">Delivery Challan No</th>
                <th width="90">Prog./ Book. No</th>
                <th width="80">Fab Color</th>
                <th width="80">Color Range</th>
                <th width="100">Fab. Constraction</th>
                <th width="100">Fab. Compositon</th>
                <th width="50">Fin GSM</th>
                <th width="50">Fab. Dia</th>
                <th width="100">Count</th>
                <th width="100">Yarn Brand</th>
                <th width="80">Yarn Lot</th>
                <th width="50">Stich Lenth</th>
                <th width="80">Machine No</th>
                <th width="60">MC Dia X Gauge</th>
                <th width="40">UOM</th>
                <th width="50">Total Roll</th>
                <th>Transfered Qnty</th>
            </thead>
            <?
				$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
				$prod_basis_arr=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
				$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
				$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
				$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1",'id','buyer_id');
				$delivery_challan_arr=return_library_array( "select id,sys_number from pro_grey_prod_delivery_mst where entry_form=56",'id','sys_number');

				$i=1; $tot_qty=0;
				$transfer_criteria=$dataArray[0][csf('transfer_criteria')];
				if($db_type==0)
				{
					$sql = "select a.id as mst_id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, sum(c.qnty) as transfer_qnty, count(c.roll_id) as tot_roll, group_concat(c.roll_id) as roll_id, c.booking_without_order, c.po_breakdown_id,b.from_trans_entry_form,b.knit_program_id
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.id=$update_id and c.entry_form=82 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					group by a.id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, c.booking_without_order, c.po_breakdown_id,b.from_trans_entry_form,b.knit_program_id";//b.knit_program_id,
				}
				else
				{
					//LISTAGG(CAST(brand AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY brand) as brand
					$sql = "select a.id as mst_id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, sum(c.qnty) as transfer_qnty, count(c.roll_id) as tot_roll, LISTAGG(CAST(c.roll_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY c.roll_id) as roll_id, c.booking_without_order, c.po_breakdown_id,b.from_trans_entry_form,b.knit_program_id
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.id=$update_id and c.entry_form=82 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					group by a.id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, c.booking_without_order, c.po_breakdown_id,b.from_trans_entry_form,b.knit_program_id";//b.knit_program_id,
				}
            	//echo $sql;
				$result=sql_select($sql);
				$all_roll_id=$all_po_id=$all_details_id=$all_entry_form="";
				foreach($result as $row)
				{
					if($row[csf("roll_id")]!="") $all_roll_id.=$row[csf("roll_id")].",";
					if($row[csf("from_order_id")]!="") $all_po_id.=$row[csf("from_order_id")].",";
					if($row[csf("prod_detls_id")]!="") $all_details_id.=$row[csf("prod_detls_id")].",";
				}
				$all_roll_id=implode(",",array_unique(explode(",",chop($all_roll_id,","))));
				$all_po_id=implode(",",array_unique(explode(",",chop($all_po_id,","))));
				$all_details_id=implode(",",array_unique(explode(",",chop($all_details_id,","))));
				$all_entry_form=array_unique(explode(',',chop($all_entry_form,",")));
				if($all_roll_id!="")
				{
					/*$prod_data_sql=sql_select("SELECT a.id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_count, b.stitch_length, b.febric_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, c.barcode_no, c.po_breakdown_id
					FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
					WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2) and c.entry_form in(2) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($all_po_id)");
					$prod_data_array=array();
					foreach($prod_data_sql as $row)
					{
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["entry_form"]=$row[csf("entry_form")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["receive_basis"]=$row[csf("receive_basis")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["booking_no"]=$row[csf("booking_no")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["booking_id"]=$row[csf("booking_id")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["knitting_source"]=$row[csf("knitting_source")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["knitting_company"]=$row[csf("knitting_company")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["dtls_id"]=$row[csf("dtls_id")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["gsm"]=$row[csf("gsm")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["width"]=$row[csf("width")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["color_id"]=$row[csf("color_id")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["color_range_id"]=$row[csf("color_range_id")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["barcode_no"]=$row[csf("barcode_no")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["roll_no"]=$row[csf("roll_no")];
					}*/

					/*$production_sql=sql_select("select b.receive_basis, b.booking_id, b.booking_no, c.id as prod_detls_id, c.color_id, c.color_range_id
					from  inv_receive_master b, pro_grey_prod_entry_dtls c
					where b.id=c.mst_id and b.entry_form in(2,22,58) and c.id in($all_details_id)");
					$production_delivery_data=array();
					foreach($production_sql as $row)
					{
						$production_delivery_data[$row[csf("prod_detls_id")]]["receive_basis"]=$row[csf("receive_basis")];
						$production_delivery_data[$row[csf("prod_detls_id")]]["booking_id"]=$row[csf("booking_id")];
						$production_delivery_data[$row[csf("prod_detls_id")]]["booking_no"]=$row[csf("booking_no")];
						$production_delivery_data[$row[csf("prod_detls_id")]]["color_id"]=$row[csf("color_id")];
						$production_delivery_data[$row[csf("prod_detls_id")]]["color_range_id"]=$row[csf("color_range_id")];
					}*/

					$production_sql=sql_select("select b.receive_basis, b.booking_id, b.booking_no, c.id as prod_detls_id, c.color_id, c.color_range_id, c.machine_dia, c.machine_gg, c.machine_no_id, c.yarn_lot, d.id as roll_id, d.roll_no, d.barcode_no
					from inv_receive_master b, pro_grey_prod_entry_dtls c , pro_roll_details d
					where b.id=c.mst_id and c.id=d.dtls_id and d.id in($all_roll_id)"); //and d.roll_id=0
					$production_delivery_data=array();
					foreach($production_sql as $row)
					{
						$production_delivery_data[$row[csf("roll_id")]]["receive_basis"]=$row[csf("receive_basis")];
						$production_delivery_data[$row[csf("roll_id")]]["booking_id"]=$row[csf("booking_id")];
						$production_delivery_data[$row[csf("roll_id")]]["booking_no"]=$row[csf("booking_no")];
						$production_delivery_data[$row[csf("roll_id")]]["color_id"]=$row[csf("color_id")];
						$production_delivery_data[$row[csf("roll_id")]]["color_range_id"]=$row[csf("color_range_id")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_dia"]=$row[csf("machine_dia")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_gg"]=$row[csf("machine_gg")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_no_id"]=$row[csf("machine_no_id")];
						$production_delivery_data[$row[csf("roll_id")]]["yarn_lot"]=$row[csf("yarn_lot")];
					}
				}

				if($all_po_id!="")
				{
					$job_array=array();
					$job_sql="select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($all_po_id)";
					//echo $job_sql;
					$job_sql_result=sql_select($job_sql);
					foreach($job_sql_result as $row)
					{
						$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
						$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
						$job_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
						$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
						$job_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
						$job_array[$row[csf('id')]]['grouping']=$row[csf('grouping')];
					}
				}


				/*if($all_roll_id!="")
				{
					$book_no_arr=return_library_array("select a.booking_no, c.id from  inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.id in($all_roll_id) and c.roll_id=0", "id","booking_no");

				}*/

				$i=0;//$delivery_challan_arr
				foreach($result as $row)
				{
					$i++;
					?>
                    <tr>
                        <td align="center"><? echo $i; ?></td>

                        <td style="word-break:break-all;" align="center">
						<?
						if($row[csf('booking_without_order')]==1)
						{
							echo $buyer_library[$book_buyer_arr[$row[csf('po_breakdown_id')]]];
						}
						else
						{
							echo $buyer_library[$job_array[$row[csf('from_order_id')]]['buyer']]. "<br>".$job_array[$row[csf('from_order_id')]]['job'];
						}

						?></td>
                        <td style="word-break:break-all;">
						<?
						$roll_id_arr=array_unique(explode(",",$row[csf("roll_id")]));
						$all_recv_basis=$rcv_basis_id=$machine_dia=$machine_gg=$machine_no="";
						foreach($roll_id_arr as $rol_id)
						{
							$all_recv_basis.=$prod_basis_arr[$production_delivery_data[$rol_id]["receive_basis"]].",";
							$rcv_basis_id.=$production_delivery_data[$rol_id]["receive_basis"].",";
							$machine_dia.=$production_delivery_data[$rol_id]["machine_dia"].",";
							$machine_gg.=$production_delivery_data[$rol_id]["machine_gg"].",";
							$machine_no.=$lib_mc_arr[$production_delivery_data[$rol_id]["machine_no_id"]]['no'].",";
							$yarn_lot.=$production_delivery_data[$rol_id]["yarn_lot"].",";
						}
						$all_recv_basis=implode(",",array_unique(explode(",",chop($all_recv_basis,","))));
						$rcv_basis_id=implode(",",array_unique(explode(",",chop($rcv_basis_id,","))));
						$machine_dia=implode(",",array_unique(explode(",",chop($machine_dia,","))));
						$machine_gg=implode(",",array_unique(explode(",",chop($machine_gg,","))));
						$machine_no=implode(",",array_unique(explode(",",chop($machine_no,","))));
						$yarn_lot=implode(",",array_unique(explode(",",chop($yarn_lot,","))));
						echo $all_recv_basis;

						?></td>
                       <td style="word-break:break-all;" align="center"><? if($row[csf('from_trans_entry_form')]==58) echo $delivery_challan_arr[$row[csf("knit_program_id")]]; else echo ""; ?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						$all_booking="";
						foreach($roll_id_arr as $rol_id)
						{
							$all_booking.=$production_delivery_data[$rol_id]["booking_no"].",";
						}
						$all_booking=implode(",",array_unique(explode(",",chop($all_booking,","))));
						echo $all_booking;
						?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						$all_color="";
						foreach($roll_id_arr as $rol_id)
						{
							$all_color_arr=array_unique(explode(",",$production_delivery_data[$rol_id]["color_id"]));
							foreach($all_color_arr as $color_id)
							{
								$all_color.=$color_library[$color_id].",";
							}
						}
						$all_color=implode(",",array_unique(explode(",",chop($all_color,","))));
						echo $all_color;
						?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						$all_color_range="";
						foreach($roll_id_arr as $rol_id)
						{
							$all_color_range.=$color_range[$production_delivery_data[$rol_id]["color_range_id"]].",";
						}
						$all_color_range=implode(",",array_unique(explode(",",chop($all_color_range,","))));
						echo $all_color_range;
						//echo $color_range[$production_delivery_data[$row[csf("prod_detls_id")]]["color_range_id"]];
						?>
                        </td>
                        <td style="word-break:break-all;"><? echo $constructtion_arr[$row[csf('feb_description_id')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $composition_arr[$row[csf('feb_description_id')]]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf("gsm")]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf("dia_width")]; ?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						//echo $row[csf('y_count')];
						$yarn_count_array=array_unique(explode(",",$row[csf('y_count')]));
						$all_count="";
						foreach($yarn_count_array as $y_count_id)
						{
							$all_count.=$yarn_count_arr[$y_count_id].",";
						}
						$all_count=chop($all_count,",");
						echo $all_count;
						?>
                        </td>
                        <td style="word-break:break-all;"><?  echo $brand_library[$row[csf("brand_id")]]; ?></td>
                        <td style="word-break:break-all;" title="<? echo implode(",",array_unique(explode(",",$row[csf("roll_id")])));?>"><? echo $yarn_lot;  $yarn_lot=""; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $machine_no; $machine_no="";//$lib_mc_arr[$row[csf("machine_no_id")]]['no']; ?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						/*if($rcv_basis_id==2)
						{
							echo $ppl_mc_arr[$all_booking]['dia'].'X'.$ppl_mc_arr[$all_booking]['gauge'];
						}
						else
						{
							echo $lib_mc_arr[$row[csf("machine_no_id")]]['dia'].'X'.$lib_mc_arr[$row[csf("machine_no_id")]]['gauge'];
						}*/
						echo $machine_dia.'X'.$machine_gg;
						$machine_dia=$machine_gg="";
						?>
                        </td>
                        <td align="center">Kg</td>
                        <td align="center"><? echo $row[csf("tot_roll")]; ?></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2,'.','');?></td>
                    </tr>
                	<?
					$tot_qty+=$row[csf('transfer_qnty')];
					$gt_total_roll+=$row[csf("tot_roll")];

				}
			?>
            <tfoot>
            	<tr>
                	<th align="right" colspan="18"><strong>Total</strong></th>
                    <th align="center"><? echo $gt_total_roll; ?></th>
                    <th align="right"><? echo number_format($tot_qty,2,'.',''); ?></th>
                </tr>
            </tfoot>
		</table>
        <br>
		<?
        	echo signature_table(94, $company, "1800px");
        ?>
	</div>
	<?
	exit();
}

if($action=="grey_issue_print_gropping")
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
	$buyer_library=return_library_array("select id, short_name from lib_buyer","id","short_name");
	$brand_library=return_library_array("select id, brand_name from lib_brand","id","brand_name");

	/*$location_arr=return_library_array("select id, location_name from  lib_location",'id','location_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand",'id','brand_name');
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$booking_arr=return_library_array( "select id, booking_no from wo_booking_mst where item_category in(2,13)", "id", "booking_no");
	$pi_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");*/

	$lib_mc_arr=array();
	$mc_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach($mc_sql as $row)
	{
		$lib_mc_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}

	$ppl_mc_arr=array();
	if($db_type==0) $machine_cond=" machine_id<>''"; else $machine_cond=" machine_id is not null";

	/*$ppl_mc_sql=sql_select("select id, machine_id, machine_dia as dia_width, machine_gg as gauge from  ppl_planning_info_entry_dtls where $machine_cond");
	foreach($ppl_mc_sql as $row)
	{
		$mc_id_arr=array_unique(explode(",",$row[csf('machine_id')]));
		foreach($mc_id_arr as $mc_id)
		{
			$ppl_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
			$ppl_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
		}

		$ppl_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$ppl_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}*/
	//echo "";

	$dataArray=sql_select("select id, transfer_system_id, transfer_criteria, transfer_date, item_category, challan_no, remarks from inv_item_transfer_mst where id=$update_id");


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

	?>
    <div>
        <table width="1000" cellspacing="0">
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
            </tr>
             <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px; font-weight:bold">Grey Fabric Transfer Entry Report</td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
            	<td width="130"><strong>Transfer ID :</strong></td>
                <td width="180"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
                <td width="130"><strong>Transfer Criteria:</strong></td>
                <td width="180"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
                <td width="130"><strong>Item Category:</strong></td>
                <td ><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
            </tr>
            <tr>
            	<td ><strong>Transfer Date:</strong></td>
                <td ><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
                <td ><strong>Challan No:</strong></td>
                <td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td ><strong>Remarks:</strong></td>
                <td ><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="1800"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th width="20">SL</th>
                <th width="100">From Store</th>
                <th width="100">To Store</th>
                <th width="50">File No</th>
                <th width="50">Reff No</th>
                <th width="100">Buyer <br> Job</th>
                <th width="80">Production Basis</th>
                <!--<th width="100">Delivery Challan No</th>-->
                <th width="90">Prog./ Book. No</th>
                <th width="80">Fab Color</th>
                <th width="80">Color Range</th>
                <th width="100">Fab. Constraction</th>
                <th width="100">Fab. Compositon</th>
                <th width="50">Fin GSM</th>
                <th width="50">Fab. Dia</th>
                <th width="100">Count</th>
                <th width="100">Yarn Brand</th>
                <th width="80">Yarn Lot</th>
                <th width="50">Stich Lenth</th>
                <th width="80">Machine No</th>
                <th width="60">MC Dia X Gauge</th>
                <th width="40">UOM</th>
                <th width="50">Total Roll</th>
                <th>Transfered Qnty</th>
            </thead>
            <?
				$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
				$prod_basis_arr=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
				$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
				$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
				$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1",'id','buyer_id');
				//$delivery_challan_arr=return_library_array( "select id,sys_number from pro_grey_prod_delivery_mst where entry_form=56",'id','sys_number');

				$i=1; $tot_qty=0;
				$transfer_criteria=$dataArray[0][csf('transfer_criteria')];
				if($db_type==0)
				{
					$sql = "select a.id as mst_id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, sum(c.qnty) as transfer_qnty, count(c.roll_id) as tot_roll, group_concat(c.roll_id) as roll_id, c.booking_without_order, c.po_breakdown_id
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.id=$update_id and c.entry_form=82 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					group by a.id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, c.booking_without_order, c.po_breakdown_id";//b.knit_program_id,
				}
				else
				{
					//LISTAGG(CAST(brand AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY brand) as brand
					$sql = "select a.id as mst_id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, sum(c.qnty) as transfer_qnty, count(c.roll_id) as tot_roll, LISTAGG(CAST(c.roll_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY c.roll_id) as roll_id, c.booking_without_order, c.po_breakdown_id
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.id=$update_id and c.entry_form=82 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					group by a.id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, c.booking_without_order, c.po_breakdown_id";//b.knit_program_id,
				}
            	//echo $sql;
				$result=sql_select($sql);
				$all_roll_id=$all_po_id=$all_details_id=$all_entry_form="";
				foreach($result as $row)
				{
					if($row[csf("roll_id")]!="") $all_roll_id.=$row[csf("roll_id")].",";
					if($row[csf("from_order_id")]!="") $all_po_id.=$row[csf("from_order_id")].",";
					if($row[csf("prod_detls_id")]!="") $all_details_id.=$row[csf("prod_detls_id")].",";
				}
				$all_roll_id=implode(",",array_unique(explode(",",chop($all_roll_id,","))));
				$all_po_id=implode(",",array_unique(explode(",",chop($all_po_id,","))));
				$all_details_id=implode(",",array_unique(explode(",",chop($all_details_id,","))));
				$all_entry_form=array_unique(explode(',',chop($all_entry_form,",")));
				if($all_roll_id!="")
				{
					/*$prod_data_sql=sql_select("SELECT a.id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_count, b.stitch_length, b.febric_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, c.barcode_no, c.po_breakdown_id
					FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
					WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2) and c.entry_form in(2) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($all_po_id)");
					$prod_data_array=array();
					foreach($prod_data_sql as $row)
					{
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["entry_form"]=$row[csf("entry_form")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["receive_basis"]=$row[csf("receive_basis")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["booking_no"]=$row[csf("booking_no")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["booking_id"]=$row[csf("booking_id")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["knitting_source"]=$row[csf("knitting_source")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["knitting_company"]=$row[csf("knitting_company")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["dtls_id"]=$row[csf("dtls_id")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["gsm"]=$row[csf("gsm")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["width"]=$row[csf("width")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["color_id"]=$row[csf("color_id")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["color_range_id"]=$row[csf("color_range_id")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["barcode_no"]=$row[csf("barcode_no")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["roll_no"]=$row[csf("roll_no")];
					}*/

					/*$production_sql=sql_select("select b.receive_basis, b.booking_id, b.booking_no, c.id as prod_detls_id, c.color_id, c.color_range_id
					from  inv_receive_master b, pro_grey_prod_entry_dtls c
					where b.id=c.mst_id and b.entry_form in(2,22,58) and c.id in($all_details_id)");
					$production_delivery_data=array();
					foreach($production_sql as $row)
					{
						$production_delivery_data[$row[csf("prod_detls_id")]]["receive_basis"]=$row[csf("receive_basis")];
						$production_delivery_data[$row[csf("prod_detls_id")]]["booking_id"]=$row[csf("booking_id")];
						$production_delivery_data[$row[csf("prod_detls_id")]]["booking_no"]=$row[csf("booking_no")];
						$production_delivery_data[$row[csf("prod_detls_id")]]["color_id"]=$row[csf("color_id")];
						$production_delivery_data[$row[csf("prod_detls_id")]]["color_range_id"]=$row[csf("color_range_id")];
					}*/

					$production_sql=sql_select("select b.receive_basis, b.booking_id, b.booking_no, c.id as prod_detls_id, c.color_id, c.color_range_id, c.machine_dia, c.machine_gg, c.machine_no_id, c.yarn_lot, d.id as roll_id, d.roll_no, d.barcode_no
					from inv_receive_master b, pro_grey_prod_entry_dtls c , pro_roll_details d
					where b.id=c.mst_id and c.id=d.dtls_id and d.id in($all_roll_id)"); //and d.roll_id=0
					$production_delivery_data=array();
					foreach($production_sql as $row)
					{
						$production_delivery_data[$row[csf("roll_id")]]["receive_basis"]=$row[csf("receive_basis")];
						$production_delivery_data[$row[csf("roll_id")]]["booking_id"]=$row[csf("booking_id")];
						$production_delivery_data[$row[csf("roll_id")]]["booking_no"]=$row[csf("booking_no")];
						$production_delivery_data[$row[csf("roll_id")]]["color_id"]=$row[csf("color_id")];
						$production_delivery_data[$row[csf("roll_id")]]["color_range_id"]=$row[csf("color_range_id")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_dia"]=$row[csf("machine_dia")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_gg"]=$row[csf("machine_gg")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_no_id"]=$row[csf("machine_no_id")];
						$production_delivery_data[$row[csf("roll_id")]]["yarn_lot"]=$row[csf("yarn_lot")];

						if($row[csf("receive_basis")] == 2)
						{
							$program_id_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
						}
					}
				}

				if(!empty($program_id_arr))
				{
					$booking_from_program = return_library_array("select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id  = b.mst_id and b.status_active = 1 and b.is_deleted = 0 and b.id in (".implode(",", $program_id_arr).") ","id","booking_no");
				}

				if($all_po_id!="")
				{
					$job_array=array();
					$job_sql="select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($all_po_id)";
					//echo $job_sql;
					$job_sql_result=sql_select($job_sql);
					foreach($job_sql_result as $row)
					{
						$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
						$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
						$job_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
						$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
						$job_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
						$job_array[$row[csf('id')]]['grouping']=$row[csf('grouping')];
					}
				}


				/*if($all_roll_id!="")
				{
					$book_no_arr=return_library_array("select a.booking_no, c.id from  inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.id in($all_roll_id) and c.roll_id=0", "id","booking_no");

				}*/

				$i=0;//$delivery_challan_arr
				foreach($result as $row)
				{
					$i++;
					?>
                    <tr>
                        <td align="center"><? echo $i; ?></td>
                        <td style="word-break:break-all;"><? echo $store_arr[$row[csf('from_store')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $store_arr[$row[csf('to_store')]]; ?></td>
                        <td style="word-break:break-all;" align="center"><? if($row[csf('booking_without_order')]==1) echo "&nbsp;"; else echo $job_array[$row[csf('from_order_id')]]['file_no'] ; ?></td>

                        <td style="word-break:break-all;" align="center"><? if($row[csf('booking_without_order')]==1) echo "&nbsp;"; else echo $job_array[$row[csf('from_order_id')]]['grouping'];//$row[csf('grouping')]; ?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						if($row[csf('booking_without_order')]==1)
						{
							echo $buyer_library[$book_buyer_arr[$row[csf('po_breakdown_id')]]];
						}
						else
						{
							echo $buyer_library[$job_array[$row[csf('from_order_id')]]['buyer']]. "<br>".$job_array[$row[csf('from_order_id')]]['job'];
						}

						?></td>
                        <td style="word-break:break-all;">
						<?
						$roll_id_arr=array_unique(explode(",",$row[csf("roll_id")]));
						$all_recv_basis=$rcv_basis_id=$machine_dia=$machine_gg=$machine_no=$program_nos="";$all_booking="";
						foreach($roll_id_arr as $rol_id)
						{
							$all_recv_basis.=$prod_basis_arr[$production_delivery_data[$rol_id]["receive_basis"]].",";
							$rcv_basis_id.=$production_delivery_data[$rol_id]["receive_basis"].",";
							$machine_dia.=$production_delivery_data[$rol_id]["machine_dia"].",";
							$machine_gg.=$production_delivery_data[$rol_id]["machine_gg"].",";
							$machine_no.=$lib_mc_arr[$production_delivery_data[$rol_id]["machine_no_id"]]['no'].",";
							$yarn_lot.=$production_delivery_data[$rol_id]["yarn_lot"].",";

							if($production_delivery_data[$rol_id]["receive_basis"] == 2)
							{
								$all_booking .= $booking_from_program[$production_delivery_data[$rol_id]["booking_id"]].",";

								$program_nos .= $production_delivery_data[$rol_id]["booking_no"].",";
							}else{
								$all_booking.=$production_delivery_data[$rol_id]["booking_no"].",";
							}

						}

						$program_nos=implode(",",array_unique(explode(",",chop($program_nos,","))));
						$all_booking=implode(",",array_unique(explode(",",chop($all_booking,","))));
						$all_recv_basis=implode(",",array_unique(explode(",",chop($all_recv_basis,","))));
						$rcv_basis_id=implode(",",array_unique(explode(",",chop($rcv_basis_id,","))));
						$machine_dia=implode(",",array_unique(explode(",",chop($machine_dia,","))));
						$machine_gg=implode(",",array_unique(explode(",",chop($machine_gg,","))));
						$machine_no=implode(",",array_unique(explode(",",chop($machine_no,","))));
						$yarn_lot=implode(",",array_unique(explode(",",chop($yarn_lot,","))));
						echo $all_recv_basis;

						?></td>
                        <!--<td style="word-break:break-all;" align="center"><? //if($row[csf('from_trans_entry_form')]==58) echo $delivery_challan_arr[$row[csf("knit_program_id")]]; else echo ""; ?></td>-->
                        <td style="word-break:break-all;" align="center">
						<?

						/*foreach($roll_id_arr as $rol_id)
						{
							$all_booking.=$production_delivery_data[$rol_id]["booking_no"].",";
						}
						$all_booking=implode(",",array_unique(explode(",",chop($all_booking,","))));
						echo $all_booking;*/
						echo "P: ".$program_nos."<br >B: ".$all_booking;
						?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						$all_color="";
						foreach($roll_id_arr as $rol_id)
						{
							$all_color_arr=array_unique(explode(",",$production_delivery_data[$rol_id]["color_id"]));
							foreach($all_color_arr as $color_id)
							{
								$all_color.=$color_library[$color_id].",";
							}
						}
						$all_color=implode(",",array_unique(explode(",",chop($all_color,","))));
						echo $all_color;
						?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						$all_color_range="";
						foreach($roll_id_arr as $rol_id)
						{
							$all_color_range.=$color_range[$production_delivery_data[$rol_id]["color_range_id"]].",";
						}
						$all_color_range=implode(",",array_unique(explode(",",chop($all_color_range,","))));
						echo $all_color_range;
						//echo $color_range[$production_delivery_data[$row[csf("prod_detls_id")]]["color_range_id"]];
						?>
                        </td>
                        <td style="word-break:break-all;"><? echo $constructtion_arr[$row[csf('feb_description_id')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $composition_arr[$row[csf('feb_description_id')]]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf("gsm")]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf("dia_width")]; ?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						//echo $row[csf('y_count')];
						$yarn_count_array=array_unique(explode(",",$row[csf('y_count')]));
						$all_count="";
						foreach($yarn_count_array as $y_count_id)
						{
							$all_count.=$yarn_count_arr[$y_count_id].",";
						}
						$all_count=chop($all_count,",");
						echo $all_count;
						?>
                        </td>
                        <td style="word-break:break-all;"><?  echo $brand_library[$row[csf("brand_id")]]; ?></td>
                        <td style="word-break:break-all;" title="<? echo implode(",",array_unique(explode(",",$row[csf("roll_id")])));?>"><? echo $yarn_lot;  $yarn_lot=""; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $machine_no; $machine_no="";//$lib_mc_arr[$row[csf("machine_no_id")]]['no']; ?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						/*if($rcv_basis_id==2)
						{
							echo $ppl_mc_arr[$all_booking]['dia'].'X'.$ppl_mc_arr[$all_booking]['gauge'];
						}
						else
						{
							echo $lib_mc_arr[$row[csf("machine_no_id")]]['dia'].'X'.$lib_mc_arr[$row[csf("machine_no_id")]]['gauge'];
						}*/
						echo $machine_dia.'X'.$machine_gg;
						$machine_dia=$machine_gg="";
						?>
                        </td>
                        <td align="center">Kg</td>
                        <td align="center"><? echo $row[csf("tot_roll")]; ?></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2,'.','');?></td>
                    </tr>
                	<?
					$tot_qty+=$row[csf('transfer_qnty')];
					$gt_total_roll+=$row[csf("tot_roll")];

				}
			?>
            <tfoot>
            	<tr>
                	<th align="right" colspan="21"><strong>Total</strong></th>
                    <th align="center"><? echo $gt_total_roll; ?></th>
                    <th align="right"><? echo number_format($tot_qty,2,'.',''); ?></th>
                </tr>
            </tfoot>
		</table>
        <br>
		<?
        	echo signature_table(94, $company, "1800px");
        ?>
	</div>
	<?
	exit();
}
?>

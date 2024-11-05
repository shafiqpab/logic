<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$action_from = $_REQUEST['action_from'];

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}

if ($action=="load_room_rack_self_bin")
{
	//print_r($_REQUEST);
	load_room_rack_self_bin($action_from,$data);
	die;
}

if($action=="load_drop_store_to")
{
	$data= explode("_", $data);
	echo create_drop_down( "cbo_store_name", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13 and a.company_id=$data[1] $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "fnc_details_row_blank();fn_load_floor(this.value);" );
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
		$room_arr[$row[csf('room_id')]]=$row[csf('floor_room_rack_name')];
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
		$rack_arr[$row[csf('rack_id')]]=$row[csf('floor_room_rack_name')];
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
		$shelf_arr[$row[csf('shelf_id')]]=$row[csf('floor_room_rack_name')];
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
		$bin_arr[$row[csf('bin_id')]]=$row[csf('floor_room_rack_name')];
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
	echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13  and a.status_active=1 and a.is_deleted=0 and  a.location_id=$data $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
}

if ($action=="requ_variable_settings")
{
	extract($_REQUEST);
	$requisition_type=return_field_value("user_given_code_status","variable_settings_inventory","company_name='$cbo_company_id' and variable_list=30 and item_category_id='$item_category' and status_active=1 and is_deleted=0");
	$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=13 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	echo $requisition_type.'**'.$variable_inventory;
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	// echo "20**$cbo_complete_status".'====='; die;

	// Lib -> Variable Settings -> Inventory -> Variable List -> Auto Transfer Receive
	// if Auto Transfer Receive yes, then no need to acknowledgement
	$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and item_category_id=13 and status_active=1 and variable_list= 27", "auto_transfer_rcv");
	if($variable_auto_rcv == "")
	{
		$variable_auto_rcv = 1; // if auto receive 1 No, then no need to acknowledgement
	}
	// echo "20**$variable_auto_rcv".'====='; die;
	
    for($k=1;$k<=$tot_row;$k++)
    {
        $productId="productId_".$k;
        $barcodeNO="barcodeNo_".$k;
        $prod_ids.=$$productId.",";
        $barcodeNOS.=$$barcodeNO.",";

        $rollMstIds="rollMstId_".$k;
		$all_rollMstIds.=$$rollMstIds.",";
    }
    $prod_ids=implode(",",array_unique(explode(",",chop($prod_ids,','))));
    $barcodeNOS=implode(",",array_unique(explode(",",chop($barcodeNOS,','))));

	$all_rollMstIds=chop($all_rollMstIds,',');
    if($all_rollMstIds!="") 
	{
		$in_active_data_refer = sql_select("SELECT a.id, a.barcode_no, a.status_active, a.is_deleted, a.re_transfer from pro_roll_details a where a.id in ($all_rollMstIds)");
		foreach ($in_active_data_refer as $row) 
		{
			if ($row[csf("status_active")]==1 && $row[csf("is_deleted")]==0 && $row[csf("re_transfer")]==0)
			{
				$active_barcode_source_arr[$row[csf("barcode_no")]]=$row[csf("id")];
			}
			if ($row[csf("status_active")]==0 && $row[csf("is_deleted")]==1) 
			{
				$inActive_barcode_source_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
			}
		}
		if(!empty($inActive_barcode_source_arr))
		{
			echo "20**Please Reload Barcode No : ". implode(",", $inActive_barcode_source_arr);
			disconnect($con);
			die;
		}
	}

	// echo "10**Fail";die;

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

	if(str_replace("'","",$update_id)!="")
	{
		$is_acknowledge = return_field_value("b.id id", "inv_item_transfer_mst a,inv_item_trans_acknowledgement b", "a.id=b.challan_id and  a.id=$update_id and a.status_active=1 and a.is_acknowledge=1 and b.entry_form=82", "id");
		if($is_acknowledge != "" )
		{
			echo "20**Update not allowed. This Transfer Challan is already Acknowledged.\nAcknowledge System ID = $is_acknowledge";
			die;
		}
	}

	$trans_check_sql = sql_select("SELECT barcode_no,entry_form,po_breakdown_id,qnty from pro_roll_details where barcode_no in($barcodeNOS) and entry_form in ( 22,58,83,133,82,180,110,183,84) and re_transfer =0 and status_active = 1 and is_deleted = 0 union all select a.barcode_no,a.entry_form,a.po_breakdown_id, a.qnty from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.entry_form in (2) and b.trans_id<>0 and a.re_transfer =0 and a.barcode_no in($barcodeNOS) and a.status_active = 1 and a.is_deleted = 0");

	//$trans_check_sql = sql_select("select barcode_no,entry_form,po_breakdown_id, qnty from pro_roll_details where barcode_no in($barcodeNOS) and entry_form in (80,81,83,84,82,110,183,82) and re_transfer =0 and status_active = 1 and is_deleted = 0");
	if($trans_check_sql[0][csf("barcode_no")] !="")
	{
		foreach ($trans_check_sql as $val)
		{
			$trans_po_barcode_check_arr[$val[csf("barcode_no")]][$val[csf("po_breakdown_id")]] = $val[csf("barcode_no")]."__".$val[csf("po_breakdown_id")];
			$actual_wgt_arr[$val[csf("barcode_no")]] = $val[csf("qnty")];
		}
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

		if(str_replace("'","",$txt_requisition_id) !="")
		{
			$prev_transf_res = sql_select("SELECT sum(b.transfer_qnty) as prev_transfer_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b
			where a.id=b.mst_id and a.entry_form=82 and a.transfer_requ_id=$txt_requisition_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			$prev_transfer_qnty=$prev_transf_res[0][csf("prev_transfer_qnty")]*1;

			$requ_qnty_res = sql_select("SELECT sum(b.transfer_qnty) as transfer_requ_qnty from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b where a.id=b.mst_id and a.entry_form=339 and a.id=$txt_requisition_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			$requ_qnty=$requ_qnty_res[0][csf("transfer_requ_qnty")]*1;
			// echo "20**".$requ_qnty.'<'.$prev_transfer_qnty.'+'.$tot_rollWgt2; disconnect($con); die;
			if($requ_qnty < ($prev_transfer_qnty+$tot_rollWgt2))
			{
				echo "20**Over roll Wgt not allowed."; disconnect($con); die;
			}
			if ($requ_qnty == ($prev_transfer_qnty+$tot_rollWgt2)) 
			{
				$cbo_complete_status=2;
			}
			else
			{
				$cbo_complete_status=1;
			}
		}
		// echo "20**Success".$cbo_complete_status; disconnect($con); die;

		if($all_barcodeNo!="")
		{
			$issue_data_refer = sql_select("select a.id, a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 61 and a.barcode_no in ($all_barcodeNo) and a.status_active = 1 and a.is_deleted = 0 and a.is_returned=0");
			if($issue_data_refer[0][csf("barcode_no")] != "")
			{
				echo "20**Sorry Barcode No : ". $issue_data_refer[0][csf("barcode_no")] ."\nFound in Issue No ".$issue_data_refer[0][csf("issue_number")];
				disconnect($con);
				die;
			}
		}

		// echo "10**Fail";die;

		if($db_type==0) $year_cond="YEAR(insert_date)";
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later

		$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
		$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,'GFTE',82,date("Y",time()),13 ));

		$field_array="id, entry_form, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company, from_order_id, to_order_id, item_category, remarks, transfer_requ_no, transfer_requ_id,delivery_company_name, inserted_by, insert_date";

		$data_array="(".$id.",82,'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$cbo_transfer_criteria.",".$cbo_to_company_id.",0,0,".$cbo_item_category.",".$txt_remarks.",".$txt_requisition_no.",".$txt_requisition_id.",".$txt_delv_company_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$field_array_prod_insert = "id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, gsm, dia_width, inserted_by, insert_date";

		$field_array_prod_update = "current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";

		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$field_array_trans = "id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, cons_quantity, brand_id, store_id, floor_id, room, rack, self, bin_box, inserted_by, insert_date, body_part_id, cons_rate, cons_amount";
		//$dtls_id=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		//, color_id
		//,from_store,floor_id,room,rack,shelf, to_store,to_floor_id,to_room,to_rack,to_shelf
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, from_order_id, to_order_id, feb_description_id, machine_no_id, stitch_length, yarn_lot, y_count, brand_id, from_store, floor_id, room, rack, shelf, bin_box, to_store,to_floor_id,to_room,to_rack,to_shelf, to_bin_box, item_category, transfer_qnty, knit_program_id, prod_detls_id, from_trans_entry_form, from_booking_without_order, gsm, dia_width, transfer_requ_dtls_id, inserted_by, insert_date, body_part_id, to_body_part, to_color_id, rate, yarn_rate, kniting_charge";

		$field_array_roll="id, barcode_no, mst_id, dtls_id, company_id, is_transfer, po_breakdown_id, entry_form, qnty, roll_no, roll_id, booking_without_order, from_roll_id, re_transfer, inserted_by, insert_date, rate, amount";
		$field_array_roll_update="is_transfer*updated_by*update_date";

		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$field_array_prop="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";


		$totalRollId="";
		if(str_replace("'","",$cbo_transfer_criteria)==1) // Company to Company
		{
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
				$rollId="rollId_".$j;
				$rollWgt="rollWgt_".$j;
				$yarnLot="yarnLot_".$j;
				$yarnCount="yarnCount_".$j;
				$colorId="colorId_".$j;
				$cboToColor="cboToColor_".$j;
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
				$cboToBodyPart="cboToBodyPart_".$j;
				$fromBodyPart="fromBodyPart_".$j;

				$rollRate="rollRate_".$j;
				$yarnRate="yarnRate_".$j;
				$knittingCharge="knittingCharge_".$j;

				$cons_rate = str_replace("'", "", $$rollRate);
				$cons_amount = str_replace("'", "", $$rollWgt) * $cons_rate;
				//if($$bookWithoutOrder==1) $toOrderId="orderId_".$j; else $toOrderId="toOrderId_".$j;	

				//----------------------VALIDATION FOR DUPLICATE---------------------------------------
				if($trans_po_barcode_check_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$orderId)] != str_replace("'", "", $$barcodeNo)."__".str_replace("'", "", $$orderId))
				{
					if($$fromBookingWithoutOrder == 1)
					{
						echo "20**Sorry! This barcode =". $$barcodeNo ." doesn't belong to this from booking no";
					}
					else{
						echo "20**Sorry! This barcode =". $$barcodeNo ." doesn't belong to this from order no";
					}
					disconnect($con);
					die;
				}

				if( number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","")  != number_format($$rollWgt,2,".",""))
				{
					echo "20**Sorry! This barcode (". str_replace("'","", $$barcodeNo) .") is split. actual weight ". number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","") ." doesn't match with current ".number_format($$rollWgt,2,".","") ."";
					disconnect($con);
					die;
				}

				if ($active_barcode_source_arr[str_replace("'","", $$barcodeNo)]!=str_replace("'","", $$rollMstId)) 
				{
					echo "20**Sorry! This barcode's source has changed, Please reload this barcode ".$$barcodeNo."";
					disconnect($con);
					die;
				}
				// ---------------------------------------------------------------------------

				//$new_prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$dtls_id = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);

				$row_prod = sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_to_company_id and item_category_id=13 and detarmination_id=".$$febDescripId." and gsm=".$$gsm." and upper(dia_width)='".str_replace("'", "", strtoupper($$diaWidth))."' and status_active=1 and is_deleted=0");
				if (count($row_prod) > 0 || $new_prod_ref_arr[$cbo_to_company_id."**".$$febDescripId."**".$$gsm."**".strtoupper($$diaWidth)."**13"] != "")
				{
					if(count($row_prod) > 0)
					{
						// $test='String1';
           				$new_prod_id = $row_prod[0][csf('id')];
           				$product_id_update_parameter[$new_prod_id]['qnty']+=$$rollWgt;
           				$product_id_update_parameter[$new_prod_id]['amount']+=$cons_amount;
           				$update_to_prod_id[$new_prod_id]=$new_prod_id;
					}
					else
					{
						// $test='String2';
						$new_prod_id = $new_prod_ref_arr[$cbo_to_company_id."**".$$febDescripId."**".$$gsm."**".strtoupper($$diaWidth)."**13"];
						$product_id_insert_parameter[$new_prod_id."**".$$febDescripId."**".$$gsm."**".strtoupper($$diaWidth)."**".$$constructCompo."**13"]+=$$rollWgt;
						$product_id_insert_amount[$new_prod_id."**".$$febDescripId."**".$$gsm."**".strtoupper($$diaWidth)."**".$$constructCompo."**13"]+=$cons_amount;
					}
               	}
               	else
               	{
               		// $test='String3';
               		$new_prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
               		$new_prod_ref_arr[$cbo_to_company_id."**".$$febDescripId."**".$$gsm."**".strtoupper($$diaWidth)."**13"] = $new_prod_id;
               		$product_id_insert_parameter[$new_prod_id."**".$$febDescripId."**".$$gsm."**".strtoupper($$diaWidth)."**".$$constructCompo."**13"]+=$$rollWgt;
               		$product_id_insert_amount[$new_prod_id."**".$$febDescripId."**".$$gsm."**".strtoupper($$diaWidth)."**".$$constructCompo."**13"]+=$cons_amount;
               	}


				if($$toOrderId=="") $toOrderIdRef=$$orderId; else $toOrderIdRef=$$toOrderId;

				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$transactionID.",".$id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_company_id.",".$$productId.",13,6,".$txt_transfer_date.",'".$$rollWgt."','".$$brandId."','".$$fromStoreId."','".$$fromFloor."','".$$fromRoom."','".$$fromRack."','".$$fromShelf."','".$$fromBin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$fromBodyPart."','".$cons_rate."','".$cons_amount."')";


				if(str_replace("'", "", $$fromBookingWithoutOrder) != 1 )
				{
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_prop!="") $data_array_prop.= ",";
					$data_array_prop.="(".$id_prop.",".$transactionID.",6,82,'".$dtls_id."','".$$orderId."',".$$productId.",'".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				

				$form_trans_id=$transactionID;
				//$transactionID = $transactionID+1;

				$to_trans_id=0;
				if($variable_auto_rcv==1) // if auto receive 1 No, then no need to acknowledgement
				{
					$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$to_trans_id=$transactionID;
					
					if($data_array_trans!="") $data_array_trans.=",";
					$data_array_trans.="(".$transactionID.",".$id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_to_company_id.",".$new_prod_id.",13,5,".$txt_transfer_date.",'".$$rollWgt."','".$$brandId."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$cboToBodyPart."','".$cons_rate."','".$cons_amount."')";

					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_prop!="") $data_array_prop.= ",";
					$data_array_prop.="(".$id_prop.",".$transactionID.",5,82,'".$dtls_id."','".$toOrderIdRef."',".$new_prod_id.",'".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}

				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$id.",".$form_trans_id.",".$to_trans_id.",'".$$productId."',".$new_prod_id.",'".$$orderId."','".$toOrderIdRef."','".$$febDescripId."','".$$machineNoId."','".$$stichLn."','".$$yarnLot."','".$$yarnCount."','".$$brandId."','".$$fromStoreId."','".$$fromFloor."','".$$fromRoom."','".$$fromRack."','".$$fromShelf."','".$$fromBin."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."',".$cbo_item_category.",'".$$rollWgt."','".$$progBookPiId."','".$$knitDetailsId."','".$$transferEntryForm."','".$$fromBookingWithoutOrder."','".$$gsm."','".strtoupper($$diaWidth)."','".$$requiDtlsId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$fromBodyPart."','".$$cboToBodyPart."','".$$cboToColor."','".$$rollRate."','".$$yarnRate."','".$$knittingCharge."')";
				
				if($variable_auto_rcv==1) // if Auto recv No 1
				{
					$re_transfer=0;
				}
				else{
					$re_transfer=1;
				}

				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$id.",".$dtls_id.",".$cbo_to_company_id.",6,'".$toOrderIdRef."',82,'".$$rollWgt."','".$$rollNo."','".$$rollId."','".$$bookWithoutOrder."','".$$rollMstId."',".$re_transfer.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cons_rate."','".$cons_amount."')";


				$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$form_trans_id."__".$to_trans_id."__".$id_roll.",";
				$prodData_array[$$productId]+=$$rollWgt;
				$prodData_amount_array[$$productId]+=$$rollAmount;
				$all_prod_id.=$$productId.",";
				$all_roll_id.=$$rollId.",";

				$inserted_roll_id_arr[$id_roll] =  $id_roll;
				$barcode_id[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);
			}

			if(!empty($product_id_insert_parameter))
			{
				foreach ($product_id_insert_parameter as $key => $val)
				{
					$prod_description_arr = explode("**", $key);
					$prod_id = $prod_description_arr[0];
					$fabric_desc_id = $prod_description_arr[1];
					$txt_gsm = $prod_description_arr[2];
					$txt_width = $prod_description_arr[3];
					$cons_compo = $prod_description_arr[4];

					$roll_amount = $product_id_insert_amount[$key];

					$avg_rate_per_unit = $roll_amount/$val;

					$prod_name_dtls = trim($cons_compo) . ", " . trim($txt_gsm) . ", " . trim($txt_width);

					if($variable_auto_rcv==2) // if Auto recv Yes 2 need to ack
					{
						$avg_rate_per_unit=0;
						$val=0;
						$roll_amount=0;
					}		

					// if Qty is zero then rate & value will be zero
					if ($val<=0) 
					{
						$roll_amount=0;
						$avg_rate_per_unit=0;
					}			

					if($data_array_prod_insert!="") $data_array_prod_insert.=",";
                   	$data_array_prod_insert .= "(" . $prod_id . "," . $cbo_to_company_id . "," . $cbo_store_name . ",13," . $fabric_desc_id . ",'" . $cons_compo . "','" . $prod_name_dtls . "'," . "12" . "," . $avg_rate_per_unit . "," . $val . "," . $val . "," . $roll_amount . "," . $txt_gsm . ",'" . $txt_width . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				}
			}

			if(!empty($update_to_prod_id))
			{
				$prod_id_array=array();
				$up_to_prod_ids=implode(",",array_unique($update_to_prod_id));
				//echo "10**select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($up_to_prod_ids) ";die;
				$toProdIssueResult=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($up_to_prod_ids) ");
				foreach($toProdIssueResult as $row)
				{
					if($variable_auto_rcv==1) // if auto receive 1 No, then no need to acknowledgement
					{
						$stock_qnty = $product_id_update_parameter[$row[csf("id")]]['qnty'] + $row[csf("current_stock")];
						$stock_value = $product_id_update_parameter[$row[csf("id")]]['amount'] + $row[csf("stock_value")];
					}
					else // if auto receive 2 No
					{
						$stock_qnty =  $row[csf("current_stock")];
						$stock_value =  $row[csf("stock_value")];
					}
					if ($stock_qnty>0) 
					{
						$avg_rate_per_unit = $stock_value/$stock_qnty;
						$stock_value = $avg_rate_per_unit*$stock_qnty;
					}
					else
					{
						$avg_rate_per_unit = 0;
						$stock_value = 0;
					}
					// if Qty is zero then rate & value will be zero
					if ($stock_qnty<=0) 
					{
						$stock_value=0;
						$avg_rate_per_unit=0;
					}
					
					// echo "10**".$avg_rate_per_unit.'==='.$stock_value.'############';
					$prod_id_array[]=$row[csf('id')];
					$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$stock_qnty."'*'".$avg_rate_per_unit."'*'".$stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				unset($toProdIssueResult);
			}


			$all_prod_id_arr=implode(",",array_unique(explode(",",chop($all_prod_id,','))));
			$fromProdIssueResult=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($all_prod_id_arr) and company_id=$cbo_company_id");
			foreach($fromProdIssueResult as $row)
			{
				$issue_qty=$prodData_array[$row[csf('id')]];
				$issue_amount=$prodData_amount_array[$row[csf('id')]];

				$current_stock=$row[csf('current_stock')]-$issue_qty;
				$current_amount=$row[csf('stock_value')]-$issue_amount;
				//$current_avg_rate=$row[csf('stock_value')]-$issue_amount;
				$current_avg_rate=$current_amount/$current_stock;

				// if Qty is zero then rate & value will be zero
				if ($current_stock<=0) 
				{
					$current_amount=0;
					$current_avg_rate=0;
				}

				$prod_id_array[]=$row[csf('id')];
				$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$current_stock."'*'".$current_avg_rate."'*'".$current_amount."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}

			$all_roll_id_arr=array_unique(explode(",",chop($all_roll_id,',')));
			foreach($all_roll_id_arr as $roll_id)
			{
				$roll_id_array[]=$roll_id;
				$data_array_roll_update[$roll_id]=explode("*",("5*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
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
				$cboToColor="cboToColor_".$j;
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
				$cboToBodyPart="cboToBodyPart_".$j;
				$fromBodyPart="fromBodyPart_".$j;

				$rollRate="rollRate_".$j;
				$yarnRate="yarnRate_".$j;
				$knittingCharge="knittingCharge_".$j;

				$cons_rate = str_replace("'", "", $$rollRate);
				$cons_amount = str_replace("'", "", $$rollWgt) * $cons_rate;

				//if($$bookWithoutOrder==1) $toOrderId="orderId_".$j; else $toOrderId="toOrderId_".$j;

				//if($$toOrderId=="") $toOrderIdRef=0; else $toOrderIdRef=$$toOrderId;

				if($$toOrderId=="") $toOrderIdRef=$$orderId; else $toOrderIdRef=$$toOrderId;


				//------------------------------------VALIDATION FOR DUPLICATE---------------------------------------

				if($trans_po_barcode_check_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$orderId)] != str_replace("'", "", $$barcodeNo)."__".str_replace("'", "", $$orderId))
				{
					if($$fromBookingWithoutOrder == 1)
					{
						echo "20**Sorry! This barcode =". $$barcodeNo ." doesn't belong to this from booking no";
					}
					else{
						echo "20**Sorry! This barcode =". $$barcodeNo ." doesn't belong to this from order no";
					}
					disconnect($con);
					die;
				}

				if( number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","")  != number_format($$rollWgt,2,".",""))
				{
					echo "20**Sorry! This barcode (". str_replace("'","", $$barcodeNo) .") is split. actual weight ". number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","") ." doesn't match with current ".number_format($$rollWgt,2,".","") ."";
					disconnect($con);
					die;
				}

				//echo "10**".$active_barcode_source_arr[str_replace("'","", $$barcodeNo)].'!='.str_replace("'","", $$rollMstId);
				if ($active_barcode_source_arr[str_replace("'","", $$barcodeNo)]!=str_replace("'","", $$rollMstId)) 
				{
					echo "20**Sorry! This barcode's source has changed, Please reload this barcode ".$$barcodeNo."";
					disconnect($con);
					die;
				}

				//---------------------------------------------------------------------------
				$dtls_id = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
				$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con); 

				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$transactionID.",".$id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_company_id.",'".$$productId."',13,6,".$txt_transfer_date.",'".$$rollWgt."','".$$brandId."','".$$fromStoreId."','".$$fromFloor."','".$$fromRoom."','".$$fromRack."','".$$fromShelf."','".$$fromBin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$fromBodyPart."','".$cons_rate."','".$cons_amount."')";

				if(str_replace("'", "", $$fromBookingWithoutOrder) != 1)
				{
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_prop!="") $data_array_prop.= ",";
					$data_array_prop.="(".$id_prop.",".$transactionID.",6,82,'".$dtls_id."','".$$orderId."','".$$productId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				

				$form_trans_id=$transactionID;

				$to_trans_id=0;
				if($variable_auto_rcv==1) // if auto receive 1 No, then no need to acknowledgement
				{
					$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$to_trans_id=$transactionID;
					
					if($data_array_trans!="") $data_array_trans.=",";
					$data_array_trans.="(".$transactionID.",".$id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_to_company_id.",".$$productId.",13,5,".$txt_transfer_date.",'".$$rollWgt."','".$$brandId."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$cboToBodyPart."','".$cons_rate."','".$cons_amount."')";

					if(str_replace("'", "", $$bookWithoutOrder) !=1)
					{
						$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						if($data_array_prop!="") $data_array_prop.= ",";
						$data_array_prop.="(".$id_prop.",".$transactionID.",5,82,'".$dtls_id."','".$toOrderIdRef."',".$$productId.",'".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					}
				}
				//---------------------------------------------------------------------------------------

				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$id.",".$form_trans_id.",".$to_trans_id.",'".$$productId."','".$$productId."','".$$orderId."','".$toOrderIdRef."','".$$febDescripId."','".$$machineNoId."','".$$stichLn."','".$$yarnLot."','".$$yarnCount."','".$$brandId."','".$$fromStoreId."','".$$fromFloor."','".$$fromRoom."','".$$fromRack."','".$$fromShelf."','".$$fromBin."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."',".$cbo_item_category.",'".$$rollWgt."','".$$progBookPiId."','".$$knitDetailsId."','".$$transferEntryForm."','".$$fromBookingWithoutOrder."','".$$gsm."','".strtoupper($$diaWidth)."','".$$requiDtlsId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$fromBodyPart."','".$$cboToBodyPart."','".$$cboToColor."','".$$rollRate."','".$$yarnRate."','".$$knittingCharge."')";

				if($variable_auto_rcv==1) // if Auto recv No 1
				{
					$re_transfer=0;
				}
				else{
					$re_transfer=1;
				}

				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$id.",".$dtls_id.",".$cbo_to_company_id.",6,'".$toOrderIdRef."',82,'".$$rollWgt."','".$$rollNo."','".$$rollId."','".$$bookWithoutOrder."','".$$rollMstId."',".$re_transfer.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cons_rate."','".$cons_amount."')";
				$barcodeNos.=$$barcodeNo."__".$dtls_id."__0__0__".$id_roll.",";

				$all_roll_id.=$$rollId.",";

				$inserted_roll_id_arr[$id_roll] =  $id_roll;

				$barcode_id[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);

			}

			$all_roll_id_arr=array_unique(explode(",",chop($all_roll_id,',')));
			foreach($all_roll_id_arr as $roll_id)
			{
				$roll_id_array[]=$roll_id;
				$data_array_roll_update[$roll_id]=explode("*",("5*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}
		// echo '10**string';die;

		// echo "10** insert into order_wise_pro_details ($field_array_prop) values $data_array_prop";die;
		//echo "10**insert into product_details_master (".$field_array_prod_insert.") values ".$data_array_prod_insert;die;
		// echo "10** insert into pro_roll_details ($field_array_roll) values $data_array_roll";oci_rollback($con); die;

		$rID=$rID2=$rID3=$rID4=$rID5=$rID6=$prodUpdate=$rollUpdate=$rID7_roll_re_transfer=$rID7=true;
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			//echo "10** insert into inv_transaction ($field_array_trans) values $data_array_trans";die;

			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);

			if($data_array_prop != "")
			{
				$rID5=sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop,0);
			}
			$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $roll_id_array ));

			if ($data_array_prod_insert != "")
			{
				$rID6=sql_insert("product_details_master",$field_array_prod_insert,$data_array_prod_insert,0);
			}
			// echo "10**".bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
			$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));

		}
		else // Store to Store and Order to Order
		{
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);

			if($data_array_prop != "")
			{
				$rID5=sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop,0);
			}
			
			$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $roll_id_array ));
		}

		$totalRollId=chop($totalRollId,',');

		$rID7_roll_re_transfer=sql_multirow_update("pro_roll_details","re_transfer","1","id",$totalRollId,0);


		$rID7=execute_query("update pro_roll_details set is_returned=1 where barcode_no in (". implode(',', $barcode_id).") and id not in (".implode(',', $inserted_roll_id_arr).")");
		if ($flag == 1)
		{
			if ($rID7_roll_re_transfer)
				$flag = 1;
			else
				$flag = 0;
		}

		if (str_replace("'","",$txt_requisition_id)!="") 
		{
			$requi_field_array_update="requisition_status*updated_by*update_date";
			$requi_data_array_update=$cbo_complete_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID8=sql_update("inv_item_transfer_requ_mst",$requi_field_array_update,$requi_data_array_update,"id",$txt_requisition_id,1);
			if($rID8) $flag=1; else $flag=0;
		}


		//echo "10** insert into inv_item_transfer_dtls ($field_array_dtls) values $data_array_dtls";die;
		//echo "10**".bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
	  	// echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$rID6."&&".$prodUpdate."&&".$rollUpdate. "&&" . $rID7_roll_re_transfer ."&&".$rID7 ."&&".$rID8 ."##".$test; oci_rollback($con); die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate && $rollUpdate && $rID7_roll_re_transfer && $rID7)
			{
				mysql_query("COMMIT");
				echo "0**".$id."**".$new_transfer_system_id[0]."**".substr($barcodeNos,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate && $rollUpdate && $rID7_roll_re_transfer && $rID7)
			{
				oci_commit($con);
				echo "0**".$id."**".$new_transfer_system_id[0]."**".substr($barcodeNos,0,-1);
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
			$dtlsId="dtlsId_".$j;
			$all_dtlsId.=$$dtlsId.",";
			$rolltableId="rolltableId_".$j;
			$all_rolltableId.=$$rolltableId.",";
			$tot_rollWgt="rollWgt_".$j;
			$tot_rollWgt2+=$$tot_rollWgt;
			//$diaWidth="diaWidth_".$j;
			//$diaWidth2=strtoupper($$diaWidth);
			$rollMstId="rollMstId_".$j; // source_roll_id

			if ($$rolltableId!="") 
			{
				$saved_roll_arr[str_replace("'", "", $$barcodeNo)]=str_replace("'", "", $$rolltableId);
			}
			else
			{
				$new_roll_arr[str_replace("'", "", $$barcodeNo)]=str_replace("'", "", $$rollMstId);
			}
			$barcode_wgt_arr[str_replace("'", "", $$barcodeNo)]= str_replace("'", "", $$tot_rollWgt);
		}
		//echo "20**".$diaWidth2;disconnect($con); die;
		if(str_replace("'","",$txt_requisition_id) !="")
		{
			$transfer_update_id=str_replace("'","",$update_id);

			$prev_transf_res = sql_select("SELECT sum(b.transfer_qnty) as prev_transfer_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b
			where a.id=b.mst_id and a.entry_form=82 and a.id!=$transfer_update_id and a.transfer_requ_id=$txt_requisition_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			$prev_transfer_qnty=$prev_transf_res[0][csf("prev_transfer_qnty")]*1;

			$requ_qnty_res = sql_select("SELECT sum(b.transfer_qnty) as transfer_requ_qnty from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b where a.id=b.mst_id and a.entry_form=339 and a.id=$txt_requisition_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			$requ_qnty=$requ_qnty_res[0][csf("transfer_requ_qnty")]*1;
			$transfer_qty=$prev_transfer_qnty*1+$tot_rollWgt2*1;
			// echo "20**".$requ_qnty.'<'.$prev_transfer_qnty.'+'.$tot_rollWgt2; disconnect($con); die;			
			$req_qnty=number_format($requ_qnty,2,'.','');
			$trans_qty=number_format($transfer_qty,2,'.','');
			// echo "20**".$req_qnty.'<'.$trans_qty; disconnect($con); die;
			if($req_qnty < $trans_qty)
			{
				echo "20**Over Roll Wgt Not Allowed.";die; //disconnect($con); die;
			}
			if ($req_qnty == $trans_qty) 
			{
				$cbo_complete_status=2;
			}
			else
			{
				$cbo_complete_status=1;
			}
			/*$requ_qnty=$requ_qnty_res[0][csf("transfer_requ_qnty")]*1;
			$transfer_qty=$prev_transfer_qnty*1+$tot_rollWgt2*1;
			// echo "20**".$requ_qnty.'<'.$prev_transfer_qnty.'+'.$tot_rollWgt2; disconnect($con); die;			
			$req_qnty=number_format($requ_qnty,2,'.','');
			$trans_qty=number_format($transfer_qty,2,'.','');
			// echo "20**".$req_qnty.'<'.$trans_qty; disconnect($con); die;
			if($req_qnty < $trans_qty)
			{
				echo "20**Over Roll Wgt Not Allowed.";die; //disconnect($con); die;
			}
			if ($req_qnty == $trans_qty) 
			{
				$cbo_complete_status=2;
			}
			else
			{
				$cbo_complete_status=1;
			}*/
		}
		// echo "20**Success".$cbo_complete_status2; disconnect($con); die;

		$all_barcodeNo=chop($all_barcodeNo,',');
		$all_barcodeNo_arr=explode(",", $all_barcodeNo);

		$all_rolltableId=chop($all_rolltableId,',');
		$all_roll_id_arr=explode(",", $all_rolltableId);

		// echo "10**<pre>";print_r($all_roll_id_arr);die;
		if($all_barcodeNo!="")
		{
			$issue_data_refer = sql_select("select a.id, a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 61 and a.barcode_no in ($all_barcodeNo) and a.status_active = 1 and a.is_deleted = 0 and a.is_returned=0");
			if($issue_data_refer[0][csf("barcode_no")] != "")
			{
				echo "20**Sorry Barcode No : ". $issue_data_refer[0][csf("barcode_no")] ."\nFound in Issue No ".$issue_data_refer[0][csf("issue_number")];
				disconnect($con);
				die;
			}

			/*$next_transfer_sql = sql_select("select a.mst_id, a.entry_form, a.barcode_no from pro_roll_details a
			where  a.entry_form in (82) and a.barcode_no in ($all_barcodeNo) and a.status_active =1 and a.is_deleted=0 and a.re_transfer=1 order by a.id asc");
			foreach ($next_transfer_sql as $next_trans)
			{
				$next_transfer_arr[$next_trans[csf('barcode_no')]][$next_trans[csf('mst_id')]]["mst_id"]=$next_trans[csf('mst_id')];
			}*/

			/*$next_transfer_sql = sql_select("select max(a.id) as max_id,  a.barcode_no from pro_roll_details a
			where  a.barcode_no in ($all_barcodeNo) and a.status_active =1 and a.is_deleted=0 group by  a.barcode_no");*/

			$re_transfer_cond=' and a.re_transfer=0';
			if($variable_auto_rcv==1) // if auto receive 1 No, then no need to acknowledgement
			{
				// $re_transfer=0;
				$re_transfer_cond=' and a.re_transfer=0';
			}
			else{
				// $re_transfer=1;
				$re_transfer_cond=' and a.re_transfer in(1,0)';
			}

			// Split, Mother barcode transfer after, child barcode new insert current transfer id
			$next_transfer_sql = sql_select("SELECT max(a.id) as max_id,  a.barcode_no from pro_roll_details a, PRO_GREY_PROD_ENTRY_DTLS b
			where a.DTLS_ID=b.id and a.barcode_no in ($all_barcodeNo) and a.status_active =1 and a.is_deleted=0 and entry_form in(2) $re_transfer_cond and b.trans_id>0 group by  a.barcode_no
			union all
			SELECT max(a.id) as max_id,  a.barcode_no from pro_roll_details a
			where  a.barcode_no in ($all_barcodeNo) and a.status_active =1 and a.is_deleted=0 and entry_form in(22,58,84,83,82,110,180,183) $re_transfer_cond group by  a.barcode_no");
			// echo "10**".$next_transfer_sql;die;
			foreach ($next_transfer_sql as $next_trans)
			{
				$next_transfer_arr[$next_trans[csf('barcode_no')]]=$next_trans[csf('max_id')];
			}
			// when acknowledge found re_transfer=1, new barcode scan in update event re_transfer=0, then acknowledge saved barcode this $next_transfer_arr empty, so below empty check. 
			// After acknowledge > re_transfer=0 in acknowledge page

			$current_transfer_sql = sql_select("SELECT a.barcode_no, b.transfer_system_id as system_id, a.roll_split_from, a.qnty from pro_roll_details a, inv_item_transfer_mst b where a.mst_id=b.id and a.entry_form in (83,82,110,180,183) and a.barcode_no in ($all_barcodeNo) and a.status_active=1 and a.is_deleted=0 $re_transfer_cond
			union all 
			select a.barcode_no, b.recv_number as system_id, a.roll_split_from, a.qnty from pro_roll_details a, inv_receive_master b where a.mst_id=b.id and a.entry_form in (84) and a.barcode_no in ($all_barcodeNo) and a.status_active=1 and a.is_deleted=0 $re_transfer_cond");

			foreach ($current_transfer_sql as $current_trans)
			{
				$next_transfer_ref[$current_trans[csf('barcode_no')]]["transfer_no"]=$current_trans[csf('system_id')];
				$current_barcode_split[$current_trans[csf('barcode_no')]]=$current_trans[csf('roll_split_from')];
				$current_barcode_qnty[$current_trans[csf('barcode_no')]]=$current_trans[csf('qnty')];
			}

			if (!empty($saved_roll_arr)) // Saved barcode to next transaction found
			{
				foreach ($saved_roll_arr as $barcode => $saved_roll_id) 
				{
					// echo $saved_roll_id .'!='. $next_transfer_arr[$barcode].'<br>';
					if ($saved_roll_id != $next_transfer_arr[$barcode]) 
					{
						if ($current_barcode_split[$barcode]) 
						{
							echo "20**Sorry Split Found Update/Delete Not allowed, \nBarcode No :  ".$barcode;
							disconnect($con);
							die;
						}
						else
						{
							echo "20**Sorry Barcode No : ". $barcode ." \nFound in Transfer/Return No : ".$next_transfer_ref[$barcode]["transfer_no"];
							disconnect($con);
							die;
						}
					}
					// echo "20**=$current_barcode_qnty[$barcode] != $barcode_wgt_arr[$barcode]<br>";
					if ($current_barcode_qnty[$barcode] != $barcode_wgt_arr[$barcode]) 
					{
						echo "20**Sorry Split Found Update/Delete Not allowed, \nBarcode No. :  ".$barcode;
						disconnect($con);
						die;
					}
				}
			}
			if (!empty($new_roll_arr)) // new barcode show in current transfer but this barcode saved to another tab 
			{
				foreach ($new_roll_arr as $barcode => $new_roll_id) 
				{
					//echo $new_roll_id .'!='. $next_transfer_arr[$barcode].'<br>';
					if ($new_roll_id != $next_transfer_arr[$barcode]) 
					{
						echo "20**Sorry Barcode No : ". $barcode ." \nFound in Transfer/Return No : ".$next_transfer_ref[$barcode]["transfer_no"];
						disconnect($con);
						die;
					}
				}
			}
			// echo "10**string";die;

			/*foreach ($all_barcodeNo_arr as $check_barcode)
			{
				if($next_transfer_arr[$check_barcode][$key]["mst_id"])
				{
					echo "20**Sorry Barcode No : ". $check_barcode ." \nFound in Transfer/Return No : ".$next_transfer_ref[$check_barcode]["transfer_no"];
					disconnect($con);
					die;
				}
			}*/

			$split_roll_sql=sql_select("select barcode_no,split_from_id from pro_roll_split where status_active =1 and barcode_no in ($all_barcodeNo)");

			foreach($split_roll_sql as $bar)
			{
				$split_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
			}

			$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 and barcode_no in ($all_barcodeNo) and entry_form = 82 order by barcode_no");
			foreach($child_split_sql as $bar)
			{
				$child_splited_arr[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
			}

			$all_dtlsId=chop($all_dtlsId,',');
			$all_dtlsId_arr=explode(",", $all_dtlsId);

			if($all_dtlsId !="")
			{
				$deleted_dtls=sql_select("select b.barcode_no, a.id from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=82 and a.id in ($all_dtlsId) and a.status_active=0 and a.is_deleted=1");

				foreach($deleted_dtls as $row)
				{
					if($row[csf('id')])
					{
						echo "20**Barcode ".$row[csf('barcode_no')]." is already deleted by another user. Please reload the System ID again.";
						disconnect($con);
						die;
					}
				}
			}

			for($inc=1; $inc <= count($all_dtlsId_arr); $inc++)
			{
				$rollDtlsId=trim($all_roll_id_arr[$inc-1],"'");
				$BarcodeNO=trim($all_barcodeNo_arr[$inc-1],"'");
				//echo $rollDtlsId.'='.$BarcodeNO;
				if($split_roll_ref[$BarcodeNO][$rollDtlsId] !="" || $child_splited_arr[$BarcodeNO][$rollDtlsId] != "")
				{
					echo "20**"."Split Found. barcode no: ".$BarcodeNO;
					disconnect($con);
					die;
				}
			}
		}
		//echo "20**".$all_barcodeNo;die;

		$all_prod_id="";
		$field_array="transfer_date*challan_no*remarks*delivery_company_name*updated_by*update_date";
		$data_array=$txt_transfer_date."*".$txt_challan_no."*".$txt_remarks."*".$txt_delv_company_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_prod_insert = "id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, gsm, dia_width, inserted_by, insert_date";
		$field_array_prod_update = "current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";

		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$field_array_trans = "id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, cons_quantity, brand_id, store_id, floor_id, room, rack, self, bin_box, inserted_by, insert_date, body_part_id, cons_rate, cons_amount";
		$field_array_trans_deleted = "status_active*is_deleted*updated_by*update_date";
		$field_array_trans_update = "floor_id*room*rack*self*bin_box*updated_by*update_date*body_part_id";

		//$dtls_id=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, from_order_id, to_order_id, feb_description_id, machine_no_id, stitch_length, yarn_lot, y_count, brand_id, from_store, floor_id, room, rack, shelf, bin_box, to_store, to_floor_id,to_room, to_rack,to_shelf, to_bin_box, item_category, transfer_qnty, knit_program_id, prod_detls_id, from_trans_entry_form, from_booking_without_order, gsm, dia_width, inserted_by, insert_date, body_part_id, to_body_part, to_color_id, rate, yarn_rate, kniting_charge";

		$field_array_dtls_up="from_order_id*to_order_id*to_store*to_floor_id*to_room*to_rack*to_shelf*to_bin_box*knit_program_id*prod_detls_id*from_trans_entry_form*gsm*dia_width*updated_by*update_date*to_body_part*to_color_id";

		$field_array_dtls_deleted="status_active*is_deleted*updated_by*update_date";

		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll="id, barcode_no, mst_id, dtls_id, company_id, is_transfer, po_breakdown_id, entry_form, qnty, roll_no, roll_id, booking_without_order, from_roll_id, re_transfer, inserted_by, insert_date, rate, amount";
		$field_array_roll_up="po_breakdown_id*roll_id*roll_no*booking_without_order*updated_by*update_date";
		$field_array_roll_deleted="status_active*is_deleted*updated_by*update_date";

		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$field_array_prop="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		$field_array_prop_up="po_breakdown_id*updated_by*update_date";
		$field_array_prop_up_deleted="status_active*is_deleted*updated_by*update_date";
		$field_array_roll_update="is_transfer*updated_by*update_date";

		$totalRollId=""; $update_to_prod_id = array(); $deleted_prod_id_arr = array(); $update_from_prod_id_arr = array();
		if(str_replace("'","",$cbo_transfer_criteria)==1)
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
				$cboToColor="cboToColor_".$j;
				$stichLn="stichLn_".$j;
				$brandId="brandId_".$j;

				$toFloor="toFloor_".$j;
				$toRoom="toRoomId_".$j;
				$toRack="toRack_".$j;
				$toShelf="toShelf_".$j;
				$toBin="toBin_".$j;

				$fromStoreId="fromStoreId_".$j;
				$fromFloor="fromFloor_".$j;
				$fromRoom="fromRoom_".$j;
				$fromRack="fromRack_".$j;
				$fromShelf="fromShelf_".$j;
				$fromBin="fromBin_".$j;

				$rollNo="rollNo_".$j;
				$dtlsId="dtlsId_".$j;
				$transId="transId_".$j;
				$transIdTo="transIdTo_".$j;
				$rolltableId="rolltableId_".$j;
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
				$cboToBodyPart="cboToBodyPart_".$j;
				$fromBodyPart="fromBodyPart_".$j;

				$rollRate="rollRate_".$j;
				$yarnRate="yarnRate_".$j;
				$knittingCharge="knittingCharge_".$j;

				$cons_rate = str_replace("'", "", $$rollRate);
				$cons_amount = str_replace("'", "", $$rollWgt) * $cons_rate;


				if($$toOrderId=="") $toOrderIdRef=$$orderId; else $toOrderIdRef=$$toOrderId;

				//------------------------------------VALIDATION FOR DUPLICATE---------------------------------------
				if($$dtlsId=="")
				{
					if($trans_po_barcode_check_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$orderId)] != str_replace("'", "", $$barcodeNo)."__".str_replace("'", "", $$orderId))
					{
						if($$fromBookingWithoutOrder == 1)
						{
							echo "20**Sorry! This barcode =". $$barcodeNo ." doesn't belong to this from booking no";
						}
						else{
							echo "20**Sorry! This barcode =". $$barcodeNo ." doesn't belong to this from order no";
						}
						disconnect($con);
						die;
					}
				}


				if( number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","")  != number_format($$rollWgt,2,".",""))
				{
					echo "20**Sorry! This barcode (". str_replace("'","", $$barcodeNo) .") is split. actual weight ". number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","") ." doesn't match with currentA ".number_format($$rollWgt,2,".","") ."";
					disconnect($con);
					die;
				}
					
				if($$dtlsId>0) // Update
				{
					if($variable_auto_rcv == 1 )
					{
						if(str_replace("'", "", $$transIdTo))
						{
							$prop_id_array_up[]=$$transIdTo;
							$data_array_prop_up[$$transIdTo]=explode("*",($toOrderIdRef."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

							$data_array_transaction_up[$$transIdTo]=explode("*",("'".$$toFloor."'*'".$$toRoom."'*'".$$toRack."'*'".$$toShelf."'*'".$$toBin."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$$cboToBodyPart."'"));
						}
					}
					$dtls_id_array_up[]=$$dtlsId;
					$data_array_dtls_up[$$dtlsId]=explode("*",($$orderId."*".$toOrderIdRef."*".$cbo_store_name."*'".$$toFloor."'*'".$$toRoom."'*'".$$toRack."'*'".$$toShelf."'*'".$$toBin."'*'".$$progBookPiId."'*'".$$knitDetailsId."'*'".$$transferEntryForm."'*'".$$gsm."'*'".strtoupper($$diaWidth)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$$cboToBodyPart."'*'".$$cboToColor."'"));

					$roll_id_array_up[]=$$rolltableId;
					$data_array_roll_up[$$rolltableId]=explode("*",($toOrderIdRef."*".$$rollId."*".$$rollNo."*'".$$bookWithoutOrder."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				else // New Insert
				{
					//$new_prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
					$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
					$dtls_id = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);

					$row_prod = sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_to_company_id and item_category_id=13 and detarmination_id='".$$febDescripId."' and gsm='".$$gsm."' and upper(dia_width)='".strtoupper($$diaWidth)."' and status_active=1 and is_deleted=0");
					if (count($row_prod) > 0 || $new_prod_ref_arr[$cbo_to_company_id."**".$$febDescripId."**".$$gsm."**".strtoupper($$diaWidth)."**13"] != "")
					{
						if(count($row_prod) > 0)
						{
							// $test='String1';
	           				$new_prod_id = $row_prod[0][csf('id')];
	           				$product_id_update_parameter[$new_prod_id]['qnty']+=$$rollWgt;
	           				$product_id_update_parameter[$new_prod_id]['amount']+=$cons_amount;
	           				$update_to_prod_id[$new_prod_id]=$new_prod_id;
						}
						else
						{
							// $test='String2';
							$new_prod_id = $new_prod_ref_arr[$cbo_to_company_id."**".$$febDescripId."**".$$gsm."**".strtoupper($$diaWidth)."**13"];
							$product_id_insert_parameter[$new_prod_id."**".$$febDescripId."**".$$gsm."**".strtoupper($$diaWidth)."**".$$constructCompo."**13"]+=$$rollWgt;
							$product_id_insert_amount[$new_prod_id."**".$$febDescripId."**".$$gsm."**".strtoupper($$diaWidth)."**".$$constructCompo."**13"]+=$cons_amount;
						}
	               	}
	               	else
	               	{
	               		// $test='String3';
	               		$new_prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
	               		$new_prod_ref_arr[$cbo_to_company_id."**".$$febDescripId."**".$$gsm."**".strtoupper($$diaWidth)."**13"] = $new_prod_id;
	               		$product_id_insert_parameter[$new_prod_id."**".$$febDescripId."**".$$gsm."**".strtoupper($$diaWidth)."**".$$constructCompo."**13"]+=$$rollWgt;
	               		$product_id_insert_amount[$new_prod_id."**".$$febDescripId."**".$$gsm."**".strtoupper($$diaWidth)."**".$$constructCompo."**13"]+=$cons_amount;
	               	}

					if($data_array_trans!="") $data_array_trans.=",";
					$data_array_trans.="(".$transactionID.",".$update_id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_company_id.",'".$$productId."',13,6,".$txt_transfer_date.",'".$$rollWgt."','".$$brandId."','".$$fromStoreId."','".$$fromFloor."','".$$fromRoom."','".$$fromRack."','".$$fromShelf."','".$$fromBin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$fromBodyPart."','".$cons_rate."','".$cons_amount."')";

					if(str_replace("'", "", $fromBookingWithoutOrder) !=1)
					{
						$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						if($data_array_prop!="") $data_array_prop.= ",";
						$data_array_prop.="(".$id_prop.",".$transactionID.",6,82,'".$dtls_id."','".$$orderId."','".$$productId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					}

					$form_trans_id=$transactionID;
					
					$to_trans_id=0;
					if($variable_auto_rcv==1) // if auto receive 1 No, then no need to acknowledgement
					{
						$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
						$to_trans_id=$transactionID;

						if($data_array_trans!="") $data_array_trans.=",";

						$data_array_trans.="(".$transactionID.",".$update_id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_to_company_id.",".$new_prod_id.",13,5,".$txt_transfer_date.",'".$$rollWgt."','".$$brandId."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$cboToBodyPart."','".$cons_rate."','".$cons_amount."')";

						$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						if($data_array_prop!="") $data_array_prop.= ",";
						$data_array_prop.="(".$id_prop.",".$transactionID.",5,82,'".$dtls_id."','".$toOrderIdRef."',".$new_prod_id.",'".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					}

					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$dtls_id.",".$update_id.",".$form_trans_id.",".$to_trans_id.",'".$$productId."',".$new_prod_id.",'".$$orderId."','".$toOrderIdRef."','".$$febDescripId."','".$$machineNoId."','".$$stichLn."','".$$yarnLot."','".$$yarnCount."','".$$brandId."','".$$fromStoreId."','".$$fromFloor."','".$$fromRoom."','".$$fromRack."','".$$fromShelf."','".$$fromBin."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."',".$cbo_item_category.",'".$$rollWgt."','".$$progBookPiId."','".$$knitDetailsId."','".$$transferEntryForm."','".$$fromBookingWithoutOrder."','".$$gsm."','".strtoupper($$diaWidth)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$fromBodyPart."','".$$cboToBodyPart."','".$$cboToColor."','".$$rollRate."','".$$yarnRate."','".$$knittingCharge."')";

					if($variable_auto_rcv==1) // if Auto recv No 1
					{
						$re_transfer=0;
					}
					else{
						$re_transfer=1;
					}

					if($data_array_roll!="") $data_array_roll.= ",";
					$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$update_id.",".$dtls_id.",".$cbo_to_company_id.",6,'".$toOrderIdRef."',82,'".$$rollWgt."','".$$rollNo."','".$$rollId."','".$$bookWithoutOrder."','".$$rollMstId."',".$re_transfer.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cons_rate."','".$cons_amount."')";

					$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$form_trans_id."__".$to_trans_id."__".$id_roll.",";
					$prodData_array[$$productId]+=$$rollWgt;
					$prodData_array_amount[$$productId]+=$$rollAmount;
					$all_prod_id.=$$productId.","; // if new insert $$productId is from product id
					$all_roll_id.=$$rollId.",";

					$inserted_roll_id_arr[$id_roll] = $id_roll;
					$new_inserted[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);
				}
			}
			// echo "10**$txt_deleted_trans_id====<br>";
			if (str_replace("'", "", $txt_deleted_trans_id)!="")
			{
				$deleted_trans_id= array_filter(explode(",", $txt_deleted_trans_id));

				for($incr=1;$incr <= count($deleted_trans_id);$incr++)
				{
					$transactionIds=trim($deleted_trans_id[$incr-1],"'");
					
					if(str_replace("'", "", $transactionIds))
					{
						$transactionIds_arr_deleted[]=$transactionIds;
						$data_array_trans_deleted[$transactionIds]=explode("*",("0"."*"."1"."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

						$prop_id_array_up_deleted[]=$transactionIds;
						$data_array_prop_up_deleted[$transactionIds]=explode("*",("0"."*"."1"."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					}
				}

				$txt_deleted_prod_qty=trim($txt_deleted_prod_qty,"'");
				$txt_deleted_prod_qty=explode(",", $txt_deleted_prod_qty);
				$qty_production_arr=array();
				foreach($txt_deleted_prod_qty as $val)
				{
					$qty_production=explode("=", $val);

					$up_del_prod_id_data[$qty_production[0]]['qnty'] += $qty_production[1];
					$up_del_prod_id_data[$qty_production[0]]['amount'] += $qty_production[2];

					$up_del_from_prod_id_data[$qty_production[3]]['qnty'] += $qty_production[1];
					$up_del_from_prod_id_data[$qty_production[3]]['amount'] += $qty_production[2];

					$update_from_prod_id_arr[] = $qty_production[3];

				}

				$txt_deleted_prod_id=trim($txt_deleted_prod_id,"'");
				$deleted_prod_id_arr=array_unique(explode(",",chop($txt_deleted_prod_id,',')));

				$update_from_prod_id_arr= array_filter(array_unique($update_from_prod_id_arr));
			}

			if(!empty($product_id_insert_parameter))
			{
				foreach ($product_id_insert_parameter as $key => $val)
				{
					$prod_description_arr = explode("**", $key);
					$prod_id = $prod_description_arr[0];
					$fabric_desc_id = $prod_description_arr[1];
					$txt_gsm = $prod_description_arr[2];
					$txt_width = $prod_description_arr[3];
					$cons_compo = $prod_description_arr[4];

					$roll_amount = $product_id_insert_amount[$key];

					$avg_rate_per_unit = $roll_amount/$val;
					

					$prod_name_dtls = trim($cons_compo) . ", " . trim($txt_gsm) . ", " . trim($txt_width);
					if($variable_auto_rcv==2) // if Auto recv Yes 2 need to ack
					{
						$avg_rate_per_unit=0;
						$val=0;
						$roll_amount=0;
					}
					// if Qty is zero then rate & value will be zero
					if ($val<=0) 
					{
						$roll_amount=0;
						$avg_rate_per_unit=0;
					}
					if($data_array_prod_insert!="") $data_array_prod_insert.=",";
                   	$data_array_prod_insert .= "(" . $prod_id . "," . $cbo_to_company_id . "," . $cbo_store_name . ",13," . $fabric_desc_id . ",'" . $cons_compo . "','" . $prod_name_dtls . "'," . "12" . "," . $avg_rate_per_unit . "," . $val . "," . $val . "," . $roll_amount . "," . $txt_gsm . ",'" . $txt_width . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				}
			}
			// echo "10** insert into product_details_master ($field_array_prod_insert) values $data_array_prod_insert".'===='.$test;die;

			$all_prod_id_arr=array_unique(explode(",",chop($all_prod_id,','))); // if new insert from product id

			$all_up_del_prod_id = array_merge($update_to_prod_id,$deleted_prod_id_arr,$update_from_prod_id_arr,$all_prod_id_arr); // New Roll, Deleted Roll, Deleted From roll product id Mearged to update
			// echo "10**";print_r($all_up_del_prod_id);die;
			if(!empty($all_up_del_prod_id))
			{

				$prod_id_array=array();
				$all_up_del_prod_id=chop(implode(",",array_unique($all_up_del_prod_id)),',') ;
				$toProdIssueResult=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($all_up_del_prod_id) ");

				// echo "10**"."select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($all_up_del_prod_id) ";die;
				
				if ($variable_auto_rcv==2) // need to ack 
				{
					foreach($toProdIssueResult as $row)
					{
						//New Roll (+) and Deleted roll (-) and Deleted from roll (+)
						// $product_id_update_parameter > new roll already found to product

						$new_added_from_prod_qnty = $prodData_array[$row[csf("id")]];
						$new_added_from_prod_amount = $prodData_array_amount[$row[csf("id")]];

						$stock_qnty = //$product_id_update_parameter[$row[csf("id")]]['qnty'] + 
						$row[csf("current_stock")] 
						/*- 
						$up_del_prod_id_data[$row[csf("id")]]['qnty'] */
						+ 
						$up_del_from_prod_id_data[$row[csf("id")]]['qnty'] 
						- 
						$new_added_from_prod_qnty;

						$stock_value = //$product_id_update_parameter[$row[csf("id")]]['amount'] + 
						$row[csf("stock_value")] 
						//- $up_del_prod_id_data[$row[csf("id")]]['amount'] 
						+ 
						$up_del_from_prod_id_data[$row[csf("id")]]['amount'] 
						- 
						$new_added_from_prod_amount;

						// $avg_rate_per_unit = $stock_value/$stock_qnty;
						if ($stock_qnty>0) 
						{
							$avg_rate_per_unit = $stock_value/$stock_qnty;
						}
						else
						{
							$avg_rate_per_unit = 0;
						}
						// if Qty is zero then rate & value will be zero
						if ($stock_qnty<=0) 
						{
							$stock_value=0;
							$avg_rate_per_unit=0;
						}

						$prod_id_array[]=$row[csf('id')];
						$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$stock_qnty."'*'".$avg_rate_per_unit."'*'".$stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					}
					unset($toProdIssueResult);
				}
				else
				{
					foreach($toProdIssueResult as $row)
					{
						//New Roll (+) and Deleted roll (-) and Deleted from roll (+)
						// $product_id_update_parameter > new roll already found to product

						$new_added_from_prod_qnty = $prodData_array[$row[csf("id")]];
						$new_added_from_prod_amount = $prodData_array_amount[$row[csf("id")]];

						$stock_qnty = $product_id_update_parameter[$row[csf("id")]]['qnty'] + $row[csf("current_stock")] - $up_del_prod_id_data[$row[csf("id")]]['qnty'] + $up_del_from_prod_id_data[$row[csf("id")]]['qnty'] - $new_added_from_prod_qnty;

						$stock_value = $product_id_update_parameter[$row[csf("id")]]['amount'] + $row[csf("stock_value")] - $up_del_prod_id_data[$row[csf("id")]]['amount'] + $up_del_from_prod_id_data[$row[csf("id")]]['amount'] - $new_added_from_prod_amount;

						$avg_rate_per_unit = $stock_value/$stock_qnty;
						// if Qty is zero then rate & value will be zero
						if ($stock_qnty<=0) 
						{
							$stock_value=0;
							$avg_rate_per_unit=0;
						}

						$prod_id_array[]=$row[csf('id')];
						$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$stock_qnty."'*'".$avg_rate_per_unit."'*'".$stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					}
					unset($toProdIssueResult);
				}
			}

			//echo "10**fail";die;
			$all_roll_id=chop($all_roll_id,',');
			if($all_roll_id!="")
			{
				$all_roll_id_arr=array_unique(explode(",",chop($all_roll_id,',')));
				if(count($all_roll_id_arr)>0)
				{
					foreach($all_roll_id_arr as $roll_id)
					{
						$roll_id_array[]=$roll_id;
						$data_array_roll_update[$roll_id]=explode("*",("5*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					}
				}
			}
		}
		else // Store to Store
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
				$cboToColor="cboToColor_".$j;
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

				$toOrderId="toOrderId_".$j;
				$dtlsId="dtlsId_".$j;
				$transId="transId_".$j;
				$transIdTo="transIdTo_".$j;
				$rolltableId="rolltableId_".$j;
				$febDescripId="febDescripId_".$j;
				$machineNoId="machineNoId_".$j;
				$gsm="gsm_".$j;
				$diaWidth="diaWidth_".$j;
				$knitDetailsId="knitDetailsId_".$j;
				$transferEntryForm="transferEntryForm_".$j;
				$bookWithoutOrder="bookWithoutOrder_".$j;
				$fromBookingWithoutOrder="fromBookingWithoutOrder_".$j;
				$rollMstId="rollMstId_".$j;
				$totalRollId.=$$rollMstId.",";
				$cboToBodyPart="cboToBodyPart_".$j;
				$fromBodyPart="fromBodyPart_".$j;

				$rollRate="rollRate_".$j;
				$yarnRate="yarnRate_".$j;
				$knittingCharge="knittingCharge_".$j;

				$cons_rate = str_replace("'", "", $$rollRate);
				$cons_amount = str_replace("'", "", $$rollWgt) * $cons_rate;

				//if($$bookWithoutOrder==1) $toOrderId="orderId_".$j; else $toOrderId="toOrderId_".$j;

				//------------------------------------VALIDATION FOR DUPLICATE---------------------------------------

				if($$dtlsId=="")
				{
					if($trans_po_barcode_check_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$orderId)] != str_replace("'", "", $$barcodeNo)."__".str_replace("'", "", $$orderId))
					{
						if($$fromBookingWithoutOrder == 1)
						{
							echo "20**Sorry! This barcode =". $$barcodeNo ." doesn't belong to this from booking no";
						}
						else{
							echo "20**Sorry! This barcode =". $$barcodeNo ." doesn't belong to this from order no";
						}
						disconnect($con);
						die;
					}
				}

				if( number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","")  != number_format($$rollWgt,2,".",""))
				{
					echo "20**Sorry! This barcode (". str_replace("'","", $$barcodeNo) .") is split. actual weight ". number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","") ." doesn't match with currentB ".number_format($$rollWgt,2,".","") ."";
					disconnect($con);
					die;
				}
				
				// echo "10**string";die;

				if($$toOrderId=="") $toOrderIdRef=$$orderId; else $toOrderIdRef=$$toOrderId;

				if($$dtlsId>0)
				{
					if($variable_auto_rcv==1) // if auto receive 1 No, then no need to acknowledgement
					{
						if(str_replace("'", "", $$transIdTo)  !="" && str_replace("'", "", $$transIdTo) !=0)
						{
							$prop_id_array_up[]=$$transIdTo;
							$data_array_prop_up[$$transIdTo]=explode("*",($toOrderIdRef."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
							$data_array_transaction_up[$$transIdTo]=explode("*",("'".$$toFloor."'*'".$$toRoom."'*'".$$toRack."'*'".$$toShelf."'*'".$$toBin."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$$cboToBodyPart."'"));
						}
					}					

					$dtls_id_array_up[]=$$dtlsId;
					$data_array_dtls_up[$$dtlsId]=explode("*",($$orderId."*".$toOrderIdRef."*".$cbo_store_name."*'".$$toFloor."'*'".$$toRoom."'*'".$$toRack."'*'".$$toShelf."'*'".$$toBin."'*'".$$progBookPiId."'*'".$$knitDetailsId."'*'".$$transferEntryForm."'*'".$$gsm."'*'".strtoupper($$diaWidth)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$$cboToBodyPart."'*'".$$cboToColor."'"));

					$roll_id_array_up[]=$$rolltableId;
					//$data_array_roll_up[$$rolltableId]=explode("*",($$toOrderId."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					$data_array_roll_up[$$rolltableId]=explode("*",($toOrderIdRef."*".$$rollId."*".$$rollNo."*'".$$bookWithoutOrder."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				else if($$barcodeNo != "")
				{
					//--------------------------------------------------------------------------
					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
					$dtls_id = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
					$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);

					if($data_array_trans!="") $data_array_trans.=",";
					$data_array_trans.="(".$transactionID.",".$update_id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_company_id.",'".$$productId."',13,6,".$txt_transfer_date.",'".$$rollWgt."','".$$brandId."','".$$fromStoreId."','".$$fromFloor."','".$$fromRoom."','".$$fromRack."','".$$fromShelf."','".$$fromBin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$fromBodyPart."','".$cons_rate."','".$cons_amount."')";

					if(str_replace("'", "", $$fromBookingWithoutOrder) !=1)
					{
						$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						if($data_array_prop!="") $data_array_prop.= ",";
						$data_array_prop.="(".$id_prop.",".$transactionID.",6,82,'".$dtls_id."','".$$orderId."','".$$productId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					}
					

					$form_trans_id=$transactionID;
					$to_trans_id=0;
					if($variable_auto_rcv==1) // if auto receive 1 No, then no need to acknowledgement
					{
						$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
						$to_trans_id=$transactionID;
						
						if($data_array_trans!="") $data_array_trans.=",";
						$data_array_trans.="(".$transactionID.",".$update_id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_to_company_id.",".$$productId.",13,5,".$txt_transfer_date.",'".$$rollWgt."','".$$brandId."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$cboToBodyPart."','".$cons_rate."','".$cons_amount."')";

						if(str_replace("'", "", $$bookWithoutOrder) !=1)
						{
							$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
							if($data_array_prop!="") $data_array_prop.= ",";
							$data_array_prop.="(".$id_prop.",".$transactionID.",5,82,'".$dtls_id."','".$toOrderIdRef."',".$$productId.",'".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						}
					}
					//-------------------------------------------------------------------------------
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$dtls_id.",".$update_id.",".$form_trans_id.",".$to_trans_id.",'".$$productId."','".$$productId."','".$$orderId."','".$toOrderIdRef."','".$$febDescripId."','".$$machineNoId."','".$$stichLn."','".$$yarnLot."','".$$yarnCount."','".$$brandId."','".$$fromStoreId."','".$$fromFloor."','".$$fromRoom."','".$$fromRack."','".$$fromShelf."','".$$fromBin."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."',".$cbo_item_category.",'".$$rollWgt."','".$$progBookPiId."','".$$knitDetailsId."','".$$transferEntryForm."','".$$fromBookingWithoutOrder."','".$$gsm."','".strtoupper($$diaWidth)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$fromBodyPart."','".$$cboToBodyPart."','".$$cboToColor."','".$$rollRate."','".$$yarnRate."','".$$knittingCharge."')";

					if($variable_auto_rcv==1) // if Auto recv No 1
					{
						$re_transfer=0;
					}
					else{
						$re_transfer=1;
					}

					if($data_array_roll!="") $data_array_roll.= ",";
					$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$update_id.",".$dtls_id.",".$cbo_to_company_id.",6,'".$toOrderIdRef."',82,'".$$rollWgt."','".$$rollNo."','".$$rollId."','".$$bookWithoutOrder."','".$$rollMstId."',".$re_transfer.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cons_rate."','".$cons_amount."')";
					$barcodeNos.=$$barcodeNo."__".$dtls_id."__0__0__".$id_roll.",";
					
					$all_roll_id.=$$rollId.",";

					$inserted_roll_id_arr[$id_roll] = $id_roll;
					$new_inserted[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);
				}
			}

			$all_roll_id=chop($all_roll_id,',');
			if($all_roll_id!="")
			{
				$all_roll_id_arr=array_unique(explode(",",$all_roll_id));
				//$trans_roll_id=sql_select("select id from pro_roll_details where roll_id in(".implode(",",$all_roll_id_arr).") and entry_form=82");
				foreach($all_roll_id_arr as $roll_id)
				{
					$roll_id_array[]=$roll_id;
					$data_array_roll_update[$roll_id]=explode("*",("5*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
			}

			if ($txt_deleted_trans_id!="")
			{
				$deleted_trans_id= array_filter(explode(",", str_replace("'", "", $txt_deleted_trans_id)));

				for($incr=1;$incr <= count($deleted_trans_id);$incr++)
				{
					$transactionIds=trim($deleted_trans_id[$incr-1],"'");
					
					if(str_replace("'", "", $transactionIds))
					{
						$transactionIds_arr_deleted[]=$transactionIds;
						$data_array_trans_deleted[$transactionIds]=explode("*",("0"."*"."1"."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

						$prop_id_array_up_deleted[]=$transactionIds;
						$data_array_prop_up_deleted[$transactionIds]=explode("*",("0"."*"."1"."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					}
				}
			}
		}


		if(str_replace("'", "", $txt_deleted_trnsf_dtls_id) !="" )
		{
			$deleted_trnsf_dtls_id=explode(",", $txt_deleted_trnsf_dtls_id);
			$deleted_roll_id=explode(",", $txt_deleted_id);


			$txt_deleted_barcode = str_replace("'", "", $txt_deleted_barcode);
			$deleted_barcode_no_arr=explode(",", $txt_deleted_barcode);

			$issue_data_ref = sql_select("select a.id, a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 61 and a.barcode_no  in ($txt_deleted_barcode) and a.status_active = 1 and a.is_deleted = 0 and is_returned <> 1");
			if($issue_data_ref[0][csf("barcode_no")] != ""){
				echo "20**Sorry Barcode No ". $issue_data_ref[0][csf("barcode_no")] ." Found in Issue No ".$issue_data_ref[0][csf("issue_number")];
				disconnect($con);
				die;
			}

			/*$nxt_transfer_sql = sql_select("select a.mst_id, a.barcode_no, b.transfer_system_id, a.re_transfer, a.entry_form from pro_roll_details a, inv_item_transfer_mst b where a.mst_id=b.id and a.entry_form in (83,82,110,180,183) and a.barcode_no in ($txt_deleted_barcode) and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0");

			foreach ($nxt_transfer_sql as $nxt_trans)
			{
				$nxt_transfer_arr[$nxt_trans[csf('barcode_no')]][$nxt_trans[csf('mst_id')]]["barcode_no"]=$nxt_trans[csf('barcode_no')];
			}*/

			$nxt_transfer_sql = sql_select("select max(a.id) as max_id,  a.barcode_no from pro_roll_details a
			where  a.barcode_no in ($txt_deleted_barcode) and a.status_active =1 and a.is_deleted=0 group by  a.barcode_no");
			foreach ($nxt_transfer_sql as $nxt_trans)
			{
				$nxt_transfer_arr[$nxt_trans[csf('barcode_no')]]=$nxt_trans[csf('max_id')];
			}

			$deleted_saved_transfer_sql = sql_select("select a.id, a.barcode_no from pro_roll_details a
			where  a.barcode_no in ($txt_deleted_barcode) and a.mst_id=$update_id and a.entry_form in (82) and a.status_active =1 and a.is_deleted=0");
			foreach ($deleted_saved_transfer_sql as $row)
			{
				$deleted_saved_transfer_arr[$row[csf('barcode_no')]]=$row[csf('id')];
			}

			$current_transfer_sql = sql_select("select a.barcode_no, b.transfer_system_id as system_id from pro_roll_details a, inv_item_transfer_mst b where a.mst_id=b.id and a.entry_form in (83,82,110,180,183) and a.barcode_no in ($txt_deleted_barcode) and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0
			union all 
			select a.barcode_no, b.recv_number as system_id from pro_roll_details a, inv_receive_master b where a.mst_id=b.id and a.entry_form in (84) and a.barcode_no in ($txt_deleted_barcode) and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0");

			foreach ($current_transfer_sql as $current_trans)
			{
				$nxt_transfer_ref[$current_trans[csf('barcode_no')]]["transfer_no"]=$current_trans[csf('system_id')];
			}

			foreach ($deleted_saved_transfer_arr as $del_barcode => $del_id)
			{
				if($nxt_transfer_arr[$del_barcode] != $del_id )
				{
					echo "20**Sorry Barcode No ". $del_barcode ." \nFound in Deleted/Return No ".$nxt_transfer_ref[$del_barcode]["transfer_no"];
					disconnect($con);
					die;
				}
			}
			// echo "10**string";die;
			/*foreach ($deleted_barcode_no_arr as $del_barcode)
			{
				if($nxt_transfer_arr[$del_barcode][str_replace("'", "", $update_id)]["barcode_no"] == "")
				{
					echo "20**Sorry Barcode No ". $del_barcode ." \nFound in Transfer/Return No ".$nxt_transfer_ref[$del_barcode]["transfer_no"];
					disconnect($con);
					die;
				}
			}*/

			$splited_roll_sql=sql_select("select barcode_no,split_from_id from pro_roll_split where status_active =1 and barcode_no in ($txt_deleted_barcode)");

			foreach($splited_roll_sql as $bar)
			{
				$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
			}

			$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 and barcode_no in ($txt_deleted_barcode) and entry_form = 82 order by barcode_no");
			foreach($child_split_sql as $bar)
			{
				$child_split_arr[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
			}


			for($inc=1;$inc <= count($deleted_trnsf_dtls_id);$inc++)
			{
				$trnsfDtlsId=trim($deleted_trnsf_dtls_id[$inc-1],"'");
				$rollDtlsId=trim($deleted_roll_id[$inc-1],"'");
				$BarcodeNO=trim($deleted_barcode_no_arr[$inc-1],"'");

				if($splited_roll_ref[$BarcodeNO][$rollDtlsId] !="" || $child_split_arr[$BarcodeNO][$rollDtlsId] != "")
				{
					echo "20**"."Split Found. barcode no: ".$BarcodeNO;
					disconnect($con);
					die;
				}

				$dtls_id_array_deleted[]=$trnsfDtlsId;
				$data_array_dtls_deleted[$trnsfDtlsId]=explode("*",("0"."*"."1"."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				$roll_id_array_deleted[]=$rollDtlsId;
				$data_array_roll_deleted[$rollDtlsId]=explode("*",("0"."*"."1"."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}

		if(!empty($new_inserted))
		{
			$issue_data_ref = sql_select("select a.id, a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 61 and a.barcode_no  in (".implode(',', $new_inserted).") and a.status_active = 1 and a.is_deleted = 0 and is_returned <> 1");
			if($issue_data_ref[0][csf("barcode_no")] != ""){
				echo "20**Sorry Barcode No ". $issue_data_ref[0][csf("barcode_no")] ." Found in Issue No ".$issue_data_ref[0][csf("issue_number")];
				disconnect($con);
				die;
			}

			$duplicate_with_same_system = sql_select("select a.mst_id, a.barcode_no, b.transfer_system_id, a.re_transfer, a.entry_form from pro_roll_details a, inv_item_transfer_mst b where a.mst_id=b.id and a.entry_form in (82) and a.barcode_no in (".implode(',', $new_inserted).") and a.mst_id=$update_id and a.status_active=1 and a.is_deleted=0");

			if($duplicate_with_same_system[0][csf("barcode_no")] != ""){
				echo "20**Sorry duplicate barcode not allowed in same system id.\nbarcode no: ". $duplicate_with_same_system[0][csf("barcode_no")];
				disconnect($con);
				die;
			}

		}


		$all_dtls_id=chop($all_dtls_id,",");

		//$rollUpdate=bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $roll_id_array );
		//echo "10**$data_array_dtls";die;
		//echo "10**".bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_up, $data_array_dtls_up, $dtls_id_array_up );
		//oci_rollback($con);
		//die;
		//echo "10**insert into inv_item_transfer_dtls ($field_array_dtls) values $data_array_dtls";die;
		$rID=$rID2=$rID3=$rID4=$rID5=$rID6=$prodUpdate=$propo_data_upd=$dtls_data_upd=$roll_data_upd=$trans_data_upd=$deleted_dtls_data_upd=$deleted_roll_data_upd=$rollUpdate=$deleted_transaction_data_upd=$deleted_propo_data_upd=$rID7_roll_re_transfer=$source_roll_re_transfer=true;
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array,$data_array,"id",$update_id,0);

			if(!empty($data_array_prop_up) )
			{
				$propo_data_upd=execute_query(bulk_update_sql_statement( "order_wise_pro_details", "trans_id", $field_array_prop_up, $data_array_prop_up, $prop_id_array_up ));
			}
			if(!empty($data_array_dtls_up))
			{
				$dtls_data_upd=execute_query(bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_up, $data_array_dtls_up, $dtls_id_array_up ));
			}
			if(!empty($data_array_roll_up))
			{
				$roll_data_upd=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_up, $data_array_roll_up, $roll_id_array_up ));
			}

			if(!empty($data_array_transaction_up))
			{
				$trans_data_upd=execute_query(bulk_update_sql_statement( "inv_transaction", "id", $field_array_trans_update, $data_array_transaction_up, $prop_id_array_up ));
			}

			if(trim($txt_deleted_trnsf_dtls_id,"'")!="")
			{
				$deleted_dtls_data_upd=execute_query(bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_deleted, $data_array_dtls_deleted, $dtls_id_array_deleted ));
				$deleted_roll_data_upd=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_deleted, $data_array_roll_deleted, $roll_id_array_deleted ));

				$data_array_prop_up_deleted = array_filter($data_array_prop_up_deleted);

				if(!empty($data_array_prop_up_deleted))
				{
					$deleted_propo_data_upd=execute_query(bulk_update_sql_statement( "order_wise_pro_details", "trans_id", $field_array_prop_up_deleted, $data_array_prop_up_deleted, $prop_id_array_up_deleted ));
				}

				$deleted_transaction_data_upd=execute_query(bulk_update_sql_statement( "inv_transaction", "id", $field_array_trans_deleted, $data_array_trans_deleted, $transactionIds_arr_deleted ));
			}

			if($data_array_trans!="")
			{
				$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			}

			if($data_array_dtls!="")
			{
				$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
			}

			if($data_array_roll!="")
			{
				$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			}

			if(!empty($data_array_roll_update))
			{
				$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $roll_id_array ));
			}
			if($data_array_prop!="")
			{
				$rID5=sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop,0);
			}
			if($data_array_prod_insert!="")
			{
				// echo "10** insert into product_details_master ($field_array_prod_insert) values $data_array_prod_insert";die;
				$rID6=sql_insert("product_details_master",$field_array_prod_insert,$data_array_prod_insert,0);
			}
			if(!empty($data_array_prod_update) )
			{
				// echo bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
				$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
			}
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array,$data_array,"id",$update_id,0);

			if(!empty($data_array_prop_up))
			{
				$propo_data_upd=execute_query(bulk_update_sql_statement( "order_wise_pro_details", "trans_id", $field_array_prop_up, $data_array_prop_up, $prop_id_array_up ));
				//echo "10**".bulk_update_sql_statement( "order_wise_pro_details", "trans_id", $field_array_prop_up, $data_array_prop_up, $prop_id_array_up );die;
			}

			if(!empty($data_array_dtls_up))
			{
				$dtls_data_upd=execute_query(bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_up, $data_array_dtls_up, $dtls_id_array_up ));
			}

			if(!empty($data_array_roll_up))
			{
				$roll_data_upd=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_up, $data_array_roll_up, $roll_id_array_up ));
			}

			if(!empty($data_array_transaction_up))
			{
				$trans_data_upd=execute_query(bulk_update_sql_statement( "inv_transaction", "id", $field_array_trans_update, $data_array_transaction_up, $prop_id_array_up ));
			}

			//echo "10**".$dtls_data_upd; die;
			if(trim($txt_deleted_trnsf_dtls_id,"'")!="")
			{
				$deleted_dtls_data_upd=execute_query(bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_deleted, $data_array_dtls_deleted, $dtls_id_array_deleted ));
				$deleted_roll_data_upd=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_deleted, $data_array_roll_deleted, $roll_id_array_deleted ));

				//echo "10**".bulk_update_sql_statement( "order_wise_pro_details", "trans_id", $field_array_prop_up_deleted, $data_array_prop_up_deleted, $prop_id_array_up_deleted );die;

				$data_array_prop_up_deleted = array_filter($data_array_prop_up_deleted);
				if(!empty($data_array_prop_up_deleted))
				{
					$deleted_propo_data_upd=execute_query(bulk_update_sql_statement( "order_wise_pro_details", "trans_id", $field_array_prop_up_deleted, $data_array_prop_up_deleted, $prop_id_array_up_deleted ));
				}

				//echo "10**".bulk_update_sql_statement( "inv_transaction", "id", $field_array_trans_deleted, $data_array_trans_deleted, $transactionIds_arr_deleted );die;
				$deleted_transaction_data_upd=execute_query(bulk_update_sql_statement( "inv_transaction", "id", $field_array_trans_deleted, $data_array_trans_deleted, $transactionIds_arr_deleted ));
			}

			if($data_array_trans!="")
			{
				//echo "10**insert into inv_transaction ($field_array_trans) values $data_array_trans";die;
				$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			}

			if($data_array_dtls!="")
			{
				$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
			}
			if($data_array_roll!="")
			{
				//echo "10**insert into pro_roll_details ($field_array_roll) values $data_array_roll"; oci_rollback($con);die;
				$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			}
			if($data_array_prop!="")
			{
				$rID5=sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop,0);
			}

			if(!empty($data_array_roll_update))
			{
				$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $roll_id_array ));
			}
		}

		$totalRollId=chop($totalRollId,',');
		//echo "10**$totalRollId"; die;
		if($totalRollId !="")
		{
			$rID7_roll_re_transfer=sql_multirow_update("pro_roll_details","re_transfer","1","id",$totalRollId,0);
		}

		//previous source re_transfer flag update
		$txt_deleted_source_roll_id = str_replace("'", "", $txt_deleted_source_roll_id);
		if($txt_deleted_source_roll_id != ""){
			$source_roll_re_transfer=sql_multirow_update("pro_roll_details","re_transfer","0","id",$txt_deleted_source_roll_id,0);
		}

		if(!empty($new_inserted))
		{
			$rID7=execute_query("update pro_roll_details set is_returned=1 where barcode_no in (". implode(',', $new_inserted).") and id  not in (".implode(',', $inserted_roll_id_arr).")");
			if ($flag == 1)
			{
				if ($rID7)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if (str_replace("'","",$txt_requisition_id)!="") 
		{
			$requi_field_array_update="requisition_status*updated_by*update_date";
			$requi_data_array_update=$cbo_complete_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID8=sql_update("inv_item_transfer_requ_mst",$requi_field_array_update,$requi_data_array_update,"id",$txt_requisition_id,1);
			if($rID8) $flag=1; else $flag=0;
		}

		//echo "10**".bulk_update_sql_statement( "inv_transaction", "id", $field_array_trans_update, $data_array_transaction_up, $prop_id_array_up );
		//oci_rollback($con); die;

		// echo "10**$rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate && $propo_data_upd && $dtls_data_upd && $roll_data_upd && $trans_data_upd && $deleted_dtls_data_upd && $deleted_roll_data_upd && $deleted_propo_data_upd && $deleted_transaction_data_upd && $rollUpdate && $rID7_roll_re_transfer && $source_roll_re_transfer##$txt_deleted_source_roll_id";oci_rollback($con); die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate && $propo_data_upd && $dtls_data_upd && $roll_data_upd && $trans_data_upd && $deleted_dtls_data_upd && $deleted_roll_data_upd && $deleted_propo_data_upd && $deleted_transaction_data_upd && $rollUpdate && $rID7_roll_re_transfer && $source_roll_re_transfer)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_transfer_no)."**".substr($barcodeNos,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**".str_replace("'", '', $update_id)."**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate && $propo_data_upd && $dtls_data_upd && $roll_data_upd && $deleted_dtls_data_upd && $deleted_roll_data_upd && $deleted_propo_data_upd && $deleted_transaction_data_upd && $rollUpdate && $rID7_roll_re_transfer && $source_roll_re_transfer)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_transfer_no)."**".substr($barcodeNos,0,-1);
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
	else if ($operation==2)   // Delete Here\
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$deleted_dtls=sql_select("SELECT a.transfer_criteria, a.company_id, a.to_company, c.barcode_no, b.id, c.id as roll_id, c.from_roll_id, b.trans_id, b.to_trans_id, b.from_prod_id, b.to_prod_id, c.qnty, c.amount  from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=82 and c.entry_form=82 and a.id=$update_id order by b.id desc");
		//$deleted_dtls=sql_select("select b.barcode_no, a.id, b.id as roll_id, b.from_roll_id, a.trans_id, a.to_trans_id, a.from_prod_id, a.to_prod_id, b.qnty from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=82 and a.mst_id = $update_id order by b.id desc");

		$all_barcodeNo="";
		$from_roll_id="";

		foreach($deleted_dtls as $row)
		{
			$barcodeNo=$row[csf("barcode_no")];
			$all_barcodeNo.=$barcodeNo.",";
			$from_roll_id.=$row[csf("from_roll_id")].",";
			$rolltableId=$row[csf("roll_id")];

			if($row[csf("trans_id")])
			{
				$dtls_id_array_deleted[]=$row[csf("trans_id")];
				$data_array_dtls_deleted[$row[csf("trans_id")]]=explode("*",("0"."*"."1"."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$del_trans_id.=$row[csf("trans_id")].",";
			}
			
			if($row[csf("to_trans_id")])
			{
				$dtls_id_array_deleted[]=$row[csf("to_trans_id")];
				$data_array_dtls_deleted[$row[csf("to_trans_id")]]=explode("*",("0"."*"."1"."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$del_trans_id.=$row[csf("to_trans_id")].",";
			}


			if($row[csf("transfer_criteria")] ==1)
			{
				if($row[csf("company_id")] != $row[csf("to_company")])
				{
					$from_product[$row[csf("from_prod_id")]]['qnty'] +=$row[csf("qnty")];
					$to_product[$row[csf("to_prod_id")]]['qnty'] +=$row[csf("qnty")];
					$from_product[$row[csf("from_prod_id")]]['amount'] +=$row[csf("amount")];
					$to_product[$row[csf("to_prod_id")]]['amount'] +=$row[csf("amount")];

					$all_product_arr[$row[csf("from_prod_id")]] = $row[csf("from_prod_id")];
					$all_product_arr[$row[csf("to_prod_id")]] = $row[csf("to_prod_id")];
				}
			}
		}
		
		$all_barcodeNo=chop($all_barcodeNo,',');
		$del_trans_id=chop($del_trans_id,',');



		$all_barcodeNo_array =  explode(",", $all_barcodeNo);

		$all_barcodeNo_cond=""; $barCond="";
		if($db_type==2 && count($all_barcodeNo_array)>999)
		{
			$all_barcodeNo_array_chunk=array_chunk($all_barcodeNo_array,999) ;
			foreach($all_barcodeNo_array_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$barCond.=" a.barcode_no in($chunk_arr_value) or ";
			}
			$all_barcodeNo_cond.=" and (".chop($barCond,'or ').")";
		}
		else
		{
			$all_barcodeNo_cond=" and a.barcode_no in($all_barcodeNo)";
		}


		$issue_data_refer = sql_select("select a.id, a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 61 $all_barcodeNo_cond and a.status_active = 1 and a.is_deleted = 0 and a.is_returned=0");
		if($issue_data_refer[0][csf("barcode_no")] != "")
		{
			echo "20**Sorry Barcode No : ". $issue_data_refer[0][csf("barcode_no")] ."\nFound in Issue No ".$issue_data_refer[0][csf("issue_number")];
			disconnect($con);
			die;
		}

		
		$next_transfer_sql = sql_select("select max(a.id) as max_id,  a.barcode_no from pro_roll_details a
			where a.status_active =1 and a.is_deleted=0 $all_barcodeNo_cond group by  a.barcode_no");
		foreach ($next_transfer_sql as $next_trans)
		{
			$next_transfer_arr[$next_trans[csf('barcode_no')]]=$next_trans[csf('max_id')];
		}
		
		$current_transfer_sql = sql_select("select a.barcode_no, b.transfer_system_id as system_id from pro_roll_details a, inv_item_transfer_mst b where a.mst_id=b.id and a.entry_form in (83,82,110,180,183) $all_barcodeNo_cond and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0
			union all 
			select a.barcode_no, b.recv_number as system_id from pro_roll_details a, inv_receive_master b where a.mst_id=b.id and a.entry_form in (84) $all_barcodeNo_cond and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0");

		foreach ($current_transfer_sql as $current_trans)
		{
			$nxt_transfer_ref[$current_trans[csf('barcode_no')]]["transfer_no"]=$current_trans[csf('system_id')];
		}

		foreach($deleted_dtls as $row)
		{
			if($next_transfer_arr[$row[csf('barcode_no')]] != $row[csf('roll_id')])
			{
				echo "20**next transection found.\nRef No : ".$nxt_transfer_ref[$row[csf('barcode_no')]]["transfer_no"];;
				disconnect($con);
				die;
			}
		}

		if(!empty($all_product_arr))
		{
			$product_sql =  sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in (".implode(',', $all_product_arr).")");

			foreach ($product_sql as $value) 
			{
				$product_data[$value[csf('id')]]['current_stock'] = $value[csf('current_stock')];
				$avg_rate_per_unit = $value[csf('avg_rate_per_unit')];

				$stock_qnty = $value[csf('current_stock')] + $from_product[$value[csf('id')]]['qnty'] - $to_product[$value[csf('id')]]['qnty'];
				
				$stock_value = $value[csf('stock_value')] + $from_product[$value[csf('id')]]['amount'] - $to_product[$value[csf('id')]]['amount'];
				//$stock_value = $stock_qnty*$avg_rate_per_unit;

				if ($stock_qnty<=0) 
				{
					$stock_value=0;
					$avg_rate_per_unit=0;
				}else{
					$avg_rate_per_unit=$stock_value/$stock_qnty;
				}

				$prod_id_array[]=$value[csf('id')];
				$data_array_prod_update[$value[csf('id')]]=explode("*",("'".$stock_qnty."'*'".$avg_rate_per_unit."'*'".$stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}


		$from_roll_id=chop($from_roll_id,',');
		
		$source_roll_re_transfer=sql_multirow_update("pro_roll_details","re_transfer","0","id",$from_roll_id,0);
		

		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_dtls_deleted="status_active*is_deleted*updated_by*update_date";
		$field_array_prod_update = "current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";

		// $deleted_propo_data_upd=execute_query(bulk_update_sql_statement( "order_wise_pro_details", "trans_id", $field_array_dtls_deleted, $data_array_dtls_deleted, $dtls_id_array_deleted ));

		// $rID8=sql_update("order_wise_pro_details",$field_array,$data_array,"trans_id",$update_id,1);
		$rID1=sql_update("inv_item_transfer_mst",$field_array,$data_array,"id",$update_id,1);
		$rID2=sql_update("inv_item_transfer_dtls",$field_array,$data_array,"mst_id",$update_id,1);
		$rID3=sql_update("pro_roll_details",$field_array,$data_array,"mst_id*entry_form",$update_id."*82",1);
		$rID4=execute_query("update order_wise_pro_details set status_active=0, is_deleted=1 where entry_form=82 and trans_id in($del_trans_id)");
		$rID5=execute_query(bulk_update_sql_statement( "inv_transaction", "id", $field_array_dtls_deleted, $data_array_dtls_deleted, $dtls_id_array_deleted ));

		$prodUpdate=true;
		if(!empty($prod_id_array))
		{
			$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
		}
	
		
		//echo "10**".$rID1.'='.$rID2.'='.$rID3.'='.$rID4.'='.$rID5.'='.$source_roll_re_transfer . '=' . $prodUpdate;oci_rollback($con); die;

		if($db_type==0)
		{
			if($rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $source_roll_re_transfer && $prodUpdate)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'", '', $update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $source_roll_re_transfer && $prodUpdate)
			{
				oci_commit($con);  
				echo "2**".str_replace("'", '', $update_id);
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

if($action=="populate_barcode_data")
{
	$data=explode("**",$data);
	$bar_code=$data[0];
	$sys_id=$data[1];
	$transfer_criteria=$data[2];
	$cbo_store_id=$data[3];

	$issue_roll_mst_arr=return_library_array( "select a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b  where a.mst_id=b.id and a.entry_form=61 and a.barcode_no in($bar_code)",'barcode_no','issue_number');

	$scanned_barcode_issue_data=sql_select("select a.id, a.barcode_no,a.entry_form, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and b.entry_form = 61 and a.entry_form =61 and a.is_returned=0 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in ($bar_code)");

	foreach($scanned_barcode_issue_data as $row)
	{
		$scanned_barcode_issue_array[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		$scanned_barcode_entry_form_array[$row[csf('barcode_no')]]=$row[csf('entry_form')];
		$issue_roll_mst_arr[$row[csf('barcode_no')]] = $row[csf('issue_number')];
	}

	$scanned_barcode_update_data=sql_select("SELECT a.id as roll_upid, a.po_breakdown_id, a.barcode_no, a.roll_id, a.dtls_id, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.to_store, b.to_prod_id, b.from_prod_id from pro_roll_details a, inv_item_transfer_dtls b  where a.dtls_id=b.id and a.entry_form in(82) and a.status_active=1 and a.is_deleted=0 and a.mst_id='$sys_id'");

	if($sys_id != "")
	{
		$scanned_barcode_update_data=sql_select("SELECT  a.barcode_no, a.roll_id, c.transfer_system_id, a.entry_form from pro_roll_details a, inv_item_transfer_dtls b, inv_item_transfer_mst c where a.dtls_id=b.id and b.mst_id = c.id and a.mst_id = c.id and c.entry_form = 82 and a.entry_form in(82) and a.status_active=1 and a.is_deleted=0 and a.mst_id=$sys_id");
		foreach($scanned_barcode_update_data as $row)
		{
			$scanned_barcode_issue_array[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
			$scanned_barcode_entry_form_array[$row[csf('barcode_no')]]=$row[csf('entry_form')];
			$transfer_roll_mst_arr[$row[csf('barcode_no')]] = $row[csf('transfer_system_id')];
		}
	}

	$order_to_order_trans_sql=sql_select("SELECT a.id, a.barcode_no, a.po_breakdown_id,a.entry_form,a.booking_without_order from pro_roll_details a where a.entry_form in(183,180,110,83,82,58) and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0 and a.barcode_no in($bar_code)");
	$order_to_order_trans_data=array();
	foreach($order_to_order_trans_sql as $row)
	{
		$order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=$row[csf("barcode_no")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"]=$row[csf("po_breakdown_id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["roll_table_id"]=$row[csf("id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["entry_form"]=$row[csf("entry_form")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["booking_without_order"]=$row[csf("booking_without_order")];
	}
	unset($order_to_order_trans_sql);

	$trans_store_sql=sql_select("select a.company_id,a.to_company, a.transfer_criteria, to_prod_id as prod_id, c.barcode_no, c.entry_form, b.to_store, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.to_body_part as body_part_id
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	where a.id = b.mst_id and a.entry_form in (183,180,110,83,82) and b.id=c.dtls_id and c.entry_form in(183,180,110,83,82)
	and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.barcode_no in($bar_code)
	order by c.barcode_no desc");

	foreach($trans_store_sql as $row)
	{
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_store"]=$row[csf("to_store")];

		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_floor_id"]=$row[csf("to_floor_id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_room"]=$row[csf("to_room")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_rack"]=$row[csf("to_rack")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_self"]=$row[csf("to_shelf")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_bin_box"]=$row[csf("to_bin_box")];

		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_prod_id"]=$row[csf("prod_id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_company"] = $row[csf("to_company")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["body_part_id"] = $row[csf("body_part_id")];
		$store_ids .=$row[csf("to_store")].",";
	}
	unset($trans_store_sql);

	/*$trans_store_sql=sql_select("select a.company_id,a.to_company, a.transfer_criteria, to_prod_id as prod_id, c.barcode_no, c.entry_form, b.to_store
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	where a.id = b.mst_id and a.entry_form=82 and b.id=c.dtls_id and c.entry_form in(82)
	and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.barcode_no in($bar_code)
	order by c.barcode_no desc");

	foreach($trans_store_sql as $row)
	{
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_store"]=$row[csf("to_store")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_prod_id"]=$row[csf("prod_id")];
		$store_ids .=$row[csf("to_store")].",";

		if($row[csf("transfer_criteria")] == 1)
		{
			$order_to_order_trans_data[$row[csf("barcode_no")]]["to_company"] = $row[csf("to_company")];
		}
		else
		{
			$order_to_order_trans_data[$row[csf("barcode_no")]]["to_company"] = $row[csf("company_id")];
		}
	}
	unset($trans_store_sql);*/

	$issue_return_sql=sql_select("select b.company_id, a.id, a.barcode_no, a.po_breakdown_id,a.entry_form,a.booking_without_order, b.store_id, c.floor_id, c.room, c.rack, c.self, c.bin_box, c.prod_id, c.body_part_id from pro_roll_details a, inv_receive_master b, pro_grey_prod_entry_dtls c where a.mst_id=b.id and a.dtls_id=c.id and b.id=c.mst_id and a.entry_form in(84) and b.entry_form in(84) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.re_transfer=0 and a.barcode_no in ($bar_code)");
	foreach($issue_return_sql as $row)
	{
		$order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=$row[csf("barcode_no")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["entry_form"]=$row[csf("entry_form")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_store"]=$row[csf("store_id")];

		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_floor_id"]=$row[csf("floor_id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_room"]=$row[csf("room")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_rack"]=$row[csf("rack")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_self"]=$row[csf("self")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_bin_box"]=$row[csf("bin_box")];


		$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"]=$row[csf("po_breakdown_id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["booking_without_order"]=$row[csf("booking_without_order")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["roll_table_id"]=$row[csf("id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_company"] = $row[csf("company_id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_prod_id"]=$row[csf("prod_id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["body_part_id"]=$row[csf("body_part_id")];
		$store_ids .=$row[csf("store_id")].",";
	}
	unset($issue_return_sql);

	//============================================store check 22-02-2020 start==========================
	if($bar_code!="")
	{
		$barcode_cond=" and c.barcode_no in ($bar_code)";
	}

	if($transfer_criteria == 1) //  || $transfer_criteria == 2
	{
		if($cbo_store_id) {
			$without_store_cond_1=" and a.store_id!=$cbo_store_id";
			$without_store_cond_2=" and b.to_store!=$cbo_store_id";
		}
	}

	$sql= "SELECT a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,a.store_id as to_store, b.floor_id, b.room,  cast(b.rack as varchar2(100)) as rack, b.self, b.body_part_id
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d
		WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.booking_without_order=0 and c.re_transfer=0 $barcode_cond $without_store_cond_1 $store_cond_1
		union all
		select a.transfer_system_id as recv_number, null as location_id, from_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,b.to_store, b.to_floor_id as floor_id, b.to_room as room, cast(b.to_rack as varchar2(100)) as rack, b.to_shelf as self, b.to_body_part as body_part_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_po_break_down d
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.entry_form in(83,183) and c.entry_form in(83,183) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.booking_without_order=0 and c.re_transfer=0 $barcode_cond $without_store_cond_2
		union all
		select a.transfer_system_id as recv_number, null as location_id, to_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,b.to_store, b.to_floor_id as floor_id, b.to_room as room, cast(b.to_rack as varchar2(100)) as rack, b.to_shelf as self, b.to_body_part as body_part_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_po_break_down d
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.entry_form in(82) and c.entry_form in(82) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.booking_without_order=0 and c.re_transfer=0 and a.transfer_criteria in (1,2,4) $barcode_cond $without_store_cond_2
		union all
		 select a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,a.store_id as to_store, b.floor_id, b.room,  cast(b.rack as varchar2(100)) as rack, b.self, b.body_part_id 
		 from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d 
		 where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and b.trans_id<>0 and a.entry_form in(84) and c.entry_form in(84) 
		 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.booking_without_order=0 and c.re_transfer=0 
		 $barcode_cond $without_store_cond_1 ";
	
	if($transfer_criteria == 1 || $transfer_criteria == 2)
	{
		$sql .= " union all

		SELECT a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, null as po_number, null as pub_shipment_date, null as job_no_mst, null as file_no, d.grouping, a.entry_form,a.store_id as to_store, b.floor_id, b.room,  cast(b.rack as varchar2(100)) as rack, b.self, b.body_part_id
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_non_ord_samp_booking_mst d
		WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.booking_without_order=1 and c.re_transfer=0 $barcode_cond $without_store_cond_1 $store_cond_1
		union all
		select a.transfer_system_id as recv_number, null as location_id, to_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, null as po_number, null as pub_shipment_date, null as job_no_mst, null as file_no, d.grouping, a.entry_form, b.to_store, b.to_floor_id as floor_id, b.to_room as room, cast(b.to_rack as varchar2(100)) as rack, b.to_shelf as self, b.to_body_part as body_part_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_non_ord_samp_booking_mst d
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.entry_form in(110,180) and c.entry_form in(110,180) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.booking_without_order=1 and c.re_transfer=0 $barcode_cond $without_store_cond_2
		union all
		select a.transfer_system_id as recv_number, null as location_id, to_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, null as po_number, null as pub_shipment_date, null as job_no_mst, null as file_no, d.grouping, a.entry_form, b.to_store, b.to_floor_id as floor_id, b.to_room as room, cast(b.to_rack as varchar2(100)) as rack, b.to_shelf as self, b.to_body_part as body_part_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_non_ord_samp_booking_mst d
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.entry_form in(82) and c.entry_form in(82) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.booking_without_order=1 and c.re_transfer=0 and a.transfer_criteria in (1,2) $barcode_cond $without_store_cond_2
		union all
		 select a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, null as po_number, null as pub_shipment_date, null as job_no_mst, null as file_no, d.grouping, a.entry_form,a.store_id as to_store, b.floor_id, b.room,  cast(b.rack as varchar2(100)) as rack, b.self, b.body_part_id 
		 from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_non_ord_samp_booking_mst d 
		 where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and b.trans_id<>0 and a.entry_form in(84) and c.entry_form in(84) 
		 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.booking_without_order=1 and c.re_transfer=0 
		 $barcode_cond $without_store_cond_1	";
	}
	//echo '30**'.$sql;die;
	$result = sql_select($sql);
	if(empty($result))
	{
		echo "30**barcode not found";
		die;
	}
	//=================================================end================================================

	$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.febric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id, max(b.yarn_lot) as yarn_lot, max(b.yarn_count) as yarn_count, max(b.stitch_length) as stitch_length, max(b.brand_id) as brand_id, max(b.floor_id) as floor, max(b.room) as room, max(b.rack) as rack, max(b.self) as self, max(b.bin_box) as bin_box, b.yarn_rate, b.kniting_charge, c.rate, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id as roll_mst_id
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($bar_code)
	group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, b.yarn_rate, b.kniting_charge, c.qnty, c.rate, c.amount, c.booking_without_order,c.id");  //b.yarn_rate, b.kniting_charge, c.qnty, c.rate, c.amount,

	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			if($order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=="")
			{
				$po_id=$row[csf("po_breakdown_id")];
			}
			else
			{
				$po_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"];
			}


			if($row[csf("booking_without_order")]==1)
			{
				$non_order_booking_buyer_po_arr[$po_id] = $po_id;

			}
			else
			{
				$po_arr_book_booking_arr[$po_id] = $po_id;
			}


			if($order_to_order_trans_data[$row[csf("barcode_no")]]["booking_without_order"] == 1)
			{
				$non_order_booking_buyer_po_arr[$po_id] = $po_id;
			}
			else
			{
				$po_arr_book_booking_arr[$po_id] = $po_id;
			}


			if($row[csf("booking_without_order")]==1)
			{
				$non_order_booking_buyer_po_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			}
			else
			{
				$po_arr_book_booking_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			}


			$color_id_ref_arr[$row[csf("color_id")]] = $row[csf("color_id")];

			$company_ids .= $row[csf("company_id")].",";
			$store_ids .= $row[csf("store_id")].",";
			$febric_description_ids .= $row[csf("febric_description_id")].",";
		}

		$company_ids = chop($company_ids,",");
		$store_ids = chop($store_ids,",");
		$febric_description_ids = chop($febric_description_ids,",");

		$production_basis_sql = sql_select("SELECT a.barcode_no, b.receive_basis, a.booking_without_order, b.booking_id
		from pro_roll_details a, inv_receive_master b
		where a.mst_id = b.id and b.entry_form = 2 and a.entry_form = 2 and a.status_active =1 and a.barcode_no in ($bar_code)");
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

		$company_name_array=return_library_array( "select id, company_name from  lib_company where status_active=1 and is_deleted=0 and id in($company_ids)",'id','company_name');
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0",'id','short_name');
		$store_arr=return_library_array( "select id, store_name from lib_store_location where status_active=1 and is_deleted=0 and id in($store_ids)",'id','store_name');
		$machine_library = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");

		$composition_arr=array();
		$constructtion_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and a.id in ($febric_description_ids)";

		$deter_data_array=sql_select($sql_deter);
		foreach( $deter_data_array as $row )
		{
			$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
			$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
		}
		unset($deter_data_array);

	}
	$po_id="";

	$non_order_booking_buyer_po_arr = array_filter($non_order_booking_buyer_po_arr);

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
	if(count($po_arr_book_booking_arr) >0 )
	{
		if(!empty($program_with_order_arr))
		{
			$book_booking_sql=sql_select("select a.po_break_down_id, a.booking_no , c.id as plan_id from wo_booking_dtls a left join ppl_planning_info_entry_mst b on a.booking_no = b.booking_no left join  ppl_planning_info_entry_dtls c on b.id = c.mst_id and c.id in (".implode(",", $program_with_order_arr).") where  a.is_deleted=0 and a.status_active=1 and a.booking_type in (1,4) and a.po_break_down_id in (".implode(',', $po_arr_book_booking_arr).") group by a.po_break_down_id, a.booking_no ,c.id");

			foreach ($book_booking_sql as $val)
			{
				$book_booking_arr_plan_wise[$val[csf("po_break_down_id")]][$val[csf("plan_id")]] = $val[csf("booking_no")];
			}
		}
		else
		{
			$book_booking_arr=return_library_array("select po_break_down_id, booking_no from wo_booking_dtls where is_deleted=0 and status_active=1 and booking_type=1 and po_break_down_id in (".implode(",", $po_arr_book_booking_arr).") ",'po_break_down_id','booking_no');
		}

		$po_ref_data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.grouping FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and a.status_active =1 and b.status_active=1 and b.id in (".implode(",", $po_arr_book_booking_arr).") ");

		$po_details_array=array();
		foreach($po_ref_data_array as $row)
		{
			$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
			$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
			$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
			$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
			$po_details_array[$row[csf("po_id")]]['grouping']=$row[csf("grouping")];
		}
		unset($po_ref_data_array);
	}

	$color_id_ref_arr = array_filter(array_unique($color_id_ref_arr));
	if(count($color_id_ref_arr)>0)
	{
		$all_color_ids = implode(",", $color_id_ref_arr);
		$all_color_id_cond=""; $colorCond="";
		if($db_type==2 && count($color_id_ref_arr)>999)
		{
			$color_id_ref_chunk=array_chunk($color_id_ref_arr,999) ;
			foreach($color_id_ref_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$colorCond.=" id in($chunk_arr_value) or ";
			}

			$all_color_id_cond.=" and (".chop($colorCond,'or ').")";
		}
		else
		{
			$all_color_id_cond=" and id in($all_color_ids)";
		}

		$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0 $all_color_id_cond","id","color_name");
	}

	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id, b.store_id,b.floor_id,b.room_id, b.rack_id,b.shelf_id,b.bin_id, a.floor_room_rack_name floor_name, c.floor_room_rack_name room_name, d.floor_room_rack_name rack_name, e.floor_room_rack_name shelf_name , f.floor_room_rack_name bin_name 
		from lib_floor_room_rack_dtls b
		left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
		left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
		left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
		left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
		left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0
		where b.status_active=1 and b.is_deleted=0 and b.store_id=$cbo_store_id";
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
			$lib_floor_arr[$company][$store_id] .= $floor_id.",";
		}

		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_room_arr[$company][$store_id][$floor_id] .= $room_id.",";
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
			$lib_rack_arr[$company][$store_id][$floor_id][$room_id] .= $rack_id.",";
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
			$lib_shelf_arr[$company][$store_id][$floor_id][$room_id][$rack_id] .= $shelf_id.",";
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
			$lib_bin_arr[$company][$store_id][$floor_id][$room_id][$rack_id][$shelf_id].= $bin_id.",";
		}
	}
	unset($lib_floor_room_data_arr);
			
	$roll_details_array=array();
	$barcode_array=array();
	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			if($scanned_barcode_issue_array[$row[csf('barcode_no')]]=="")
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
					$roll_mst_id=$row[csf("roll_mst_id")];
					$entry_form=$row[csf("entry_form")];
					$booking_without_order="";
				}
				else
				{
					$po_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"];
					$roll_mst_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["roll_table_id"];
					$entry_form=$order_to_order_trans_data[$row[csf("barcode_no")]]["entry_form"];
					$booking_without_order=$order_to_order_trans_data[$row[csf("barcode_no")]]["booking_without_order"];
				}

				/*if($entry_form == 82 || $entry_form == 84)
				{
					$to_store = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_store"];
				}
				else
				{
					$to_store = $row[csf("store_id")];
				}*/

				if($order_to_order_trans_data[$row[csf("barcode_no")]]["to_store"])
				{
					$to_store = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_store"];


					$to_floor_id = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_floor_id"];
					$to_room = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_room"];
					$to_rack = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_rack"];
					$to_self = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_self"];
					$to_bin_box = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_bin_box"];

				}
				else
				{
					$to_store = $row[csf("store_id")];

					$to_floor_id = $row[csf("floor")];
					$to_room = $row[csf("room")];
					$to_rack = $row[csf("rack")];
					$to_self = $row[csf("self")];
					$to_bin_box = $row[csf("bin_box")];
				}

				if($order_to_order_trans_data[$row[csf("barcode_no")]]["body_part_id"])
				{
					$body_part_id = $order_to_order_trans_data[$row[csf("barcode_no")]]["body_part_id"];
				}
				else
				{
					$body_part_id = $row[csf("body_part_id")];
				}
				

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

				if($entry_form == 82 || $entry_form == 83 || $entry_form == 110 || $entry_form == 180)
				{
					$booking_no_fab = $booking_no_fab ." (T)";
					if($booking_without_order == 1)
					{
						$buyer_name=$buyer_arr[$book_buyer_arr[$po_id]];
					}
					else
					{
						$buyer_name=$buyer_arr[$po_details_array[$po_id]['buyer_name']];
					}
				}
				else
				{
					if($row[csf("booking_without_order")]==1)
					{
						$buyer_name=$buyer_arr[$book_buyer_arr[$row[csf("po_breakdown_id")]]];
					}
					else
					{
						$buyer_name=$buyer_arr[$po_details_array[$row[csf("po_breakdown_id")]]['buyer_name']];
					}

				}

				//echo $entry_form;die;
				$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];//entry_form

				$compsition_description=$constructtion_arr[$row[csf("febric_description_id")]]." ".$composition_arr[$row[csf("febric_description_id")]];

				if($order_to_order_trans_data[$row[csf("barcode_no")]]["to_company"])
				{
					$barcode_company_id = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_company"];
					$to_prod_id = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_prod_id"];
				}
				else
				{
					$barcode_company_id = $row[csf("company_id")];
					$to_prod_id = $row[csf("prod_id")];
				}

				if($transfer_criteria==4)
				{
					$multi_floor = chop($lib_floor_arr[$barcode_company_id][$to_store],",");
					$multi_room = chop($lib_room_arr[$barcode_company_id][$to_store][$to_floor_id],",");
					$multi_rack = chop($lib_rack_arr[$barcode_company_id][$to_store][$to_floor_id][$to_room],",");
					$multi_self = chop($lib_shelf_arr[$barcode_company_id][$to_store][$to_floor_id][$to_room][$to_rack],",");
					$multi_bin = chop($lib_bin_arr[$barcode_company_id][$to_store][$to_floor_id][$to_room][$to_rack][$to_self],",");
				}else{
					$multi_floor = "0";
					$multi_room = "0";
					$multi_rack = "0";
					$multi_self = "0";
					$multi_bin = "0";
				}

				$yarn_rate = $row[csf("yarn_rate")];
				$kniting_charge = $row[csf("kniting_charge")];
				$roll_rate = $row[csf("rate")];

				$barcodeData .=$row[csf('id')]."**".$barcode_company_id."**".$body_part[$body_part_id]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$to_store."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$to_rack."**".$to_self."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$to_prod_id."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$po_id."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$po_id]['job_no']."**".$po_details_array[$po_id]['buyer_name']."**".$buyer_name."**".$po_details_array[$po_id]['po_number']."**".$row[csf("color_id")]."**".$store_arr[$to_store]."**".$body_part_id."**".$row[csf("brand_id")]."**".$row[csf("machine_no_id")]."**".$entry_form."**".$row[csf("booking_without_order")]."**".$roll_mst_id."**".$booking_no_fab."**".$row[csf("amount")]."**".$booking_without_order."**".$to_floor_id."**".$to_room."**".$po_details_array[$po_id]['grouping']."**".$to_bin_box."**".$multi_floor."**".$multi_room."**".$multi_rack."**".$multi_self."**".$multi_bin."**".$roll_rate."**".$yarn_rate."**".$kniting_charge."**".$plan_id."**".$machine_library[$row[csf("machine_no_id")]]."__";//$row[csf("roll_mst_id")];

			}
			else
			{
				if($scanned_barcode_entry_form_array[$row[csf('barcode_no')]]==82)
				{
					$barcodeData="-1**".$transfer_roll_mst_arr[$row[csf('barcode_no')]];
				}
				else
				{
					$barcodeData="-1**".$issue_roll_mst_arr[$row[csf('barcode_no')]];
				}

				echo chop($barcodeData,"__");
				exit();
			}
		}
		echo chop($barcodeData,"__");
	}
	else
	{
		echo "0";
	}
	exit();
}

if($action=="populate_barcode_data_update")
{
	$data=explode("**",$data);
	//$bar_code=$data[0];
	$sys_id=$data[0];
	$company_name_array=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$machine_library = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");


	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array);

	$scanned_barcode_update_data=sql_select("SELECT c.company_id, a.id as roll_upid, a.po_breakdown_id, a.barcode_no, a.roll_id, a.dtls_id, a.from_roll_id, a.re_transfer, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.from_store, b.to_store, b.to_prod_id, b.from_prod_id, b.from_order_id, b.from_booking_without_order,a.booking_without_order, b.transfer_requ_dtls_id, b.floor_id, b.room, b.rack, b.shelf, b.bin_box, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf as to_shelf, b.to_bin_box, b.body_part_id, b.to_body_part, b.to_color_id, c.to_company
	from pro_roll_details a, inv_item_transfer_dtls b , inv_item_transfer_mst c
	where a.dtls_id=b.id and b.mst_id=c.id and c.entry_form=82 and a.entry_form in(82) and a.status_active=1 and a.is_deleted=0 and a.mst_id=$sys_id");
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

		$barcode_update_data[$row[csf('barcode_no')]]['from_floor_id']=$row[csf('floor_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_room']=$row[csf('room')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_rack']=$row[csf('rack')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_shelf']=$row[csf('shelf')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_bin_box']=$row[csf('bin_box')];

		$barcode_update_data[$row[csf('barcode_no')]]['to_prod_id']=$row[csf('to_prod_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']=$row[csf('from_prod_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_order_id']=$row[csf('from_order_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_roll_id']=$row[csf('from_roll_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['re_transfer']=$row[csf('re_transfer')];
		$barcode_update_data[$row[csf('barcode_no')]]['company_id']=$row[csf('company_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_booking_without_order']=$row[csf('from_booking_without_order')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_floor']=$row[csf('to_floor_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_room']=$row[csf('to_room')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_rack']=$row[csf('to_rack')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_self']=$row[csf('to_shelf')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_bin_box']=$row[csf('to_bin_box')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_body_part']=$row[csf('body_part_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_body_part']=$row[csf('to_body_part')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_color_id']=$row[csf('to_color_id')];

		if($row[csf('from_booking_without_order')] == 1)
		{
			$non_order_booking_buyer_po_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
		}
		else
		{
			$po_arr_book_booking_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
		}
		if($row[csf('booking_without_order')] == 0)
		{
			$po_arr_book_booking_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}
		else
		{
			$non_order_booking_buyer_po_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}


		$bar_code .=$row[csf('barcode_no')].",";

		$cbo_store_id = $row[csf('to_store')];
		$cbo_to_company = $row[csf('to_company')];
	}

	$bar_code = chop($bar_code,",");

	$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.febric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id, max(b.yarn_lot) as yarn_lot, max(b.yarn_count) as yarn_count, max(b.stitch_length) as stitch_length, max(b.brand_id) as brand_id, max(b.floor_id) as floor, max(b.room) as room, max(b.rack) as rack, max(b.self) as self, max(b.bin_box) as bin_box, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.amount, c.booking_without_order,c.id as roll_mst_id
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($bar_code)
	group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty, c.amount, c.booking_without_order,c.id");

	if(count($data_array)>0)
	{
		foreach($data_array as $val)
		{
			$splitted_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];

			if($val[csf("booking_without_order")] == 1 )
			{
				$non_order_booking_buyer_po_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			}else{
				$po_arr_book_booking_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
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




			$from_body_part = $barcode_update_data[$row[csf('barcode_no')]]['from_body_part'];
			$to_body_part = $barcode_update_data[$row[csf('barcode_no')]]['to_body_part'];
			$to_color_id = $barcode_update_data[$row[csf('barcode_no')]]['to_color_id'];

			$store_id = $barcode_update_data[$row[csf('barcode_no')]]['from_store'];
			$from_floor_id = $barcode_update_data[$row[csf('barcode_no')]]['from_floor_id'];
			$from_room_id = $barcode_update_data[$row[csf('barcode_no')]]['from_room'];
			$from_rack_id = $barcode_update_data[$row[csf('barcode_no')]]['from_rack'];
			$from_shelf_id = $barcode_update_data[$row[csf('barcode_no')]]['from_shelf'];
			$from_bin_id = $barcode_update_data[$row[csf('barcode_no')]]['from_bin_box'];
			
			$entry_form = $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'];
			$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];
			$compsition_description=$constructtion_arr[$row[csf("febric_description_id")]]." ".$composition_arr[$row[csf("febric_description_id")]];

			$barcodeData.=$row[csf('id')]."**".$barcode_update_data[$row[csf('barcode_no')]]['company_id']."**".$body_part[$from_body_part]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$store_id."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$from_rack_id."**".$from_shelf_id."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_prod_id']."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$from_order_id."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$from_order_id]['job_no']."**".$po_details_array[$from_order_id]['buyer_name']."**".$buyer_name."**".$po_details_array[$from_order_id]['po_number']."**".$row[csf("color_id")]."**".$store_arr[$store_id]."**".$from_body_part."**".$row[csf("brand_id")]."**".$row[csf("machine_no_id")]."**".$entry_form."**".$row[csf("booking_without_order")]."**".$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']."**".$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['trans_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_trans_id']."**".$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['po_number']."**".$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']."**".$nxProcessedBarcode[$row[csf("barcode_no")]]."**".$roll_mst_id."**".$booking_no_fab."**".$row[csf("amount")]."**".$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']."**".$from_booking_without_order."**".$splited_roll_ref[$row[csf('barcode_no')]][$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_floor']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_room']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_rack']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_self']."**".$from_floor_id."**".$from_room_id."**".$isFloorRoomRackShelfDisable."**".$po_details_array[$from_order_id]['grouping']."**".$from_bin_id."**".$barcode_update_data[$row[csf('barcode_no')]]['to_bin_box']."**".$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['job_no']."**".$to_body_part."**".$multi_floor."**".$multi_room."**".$multi_rack."**".$multi_self."**".$multi_bin."**".$plan_id."**".$machine_library[$row[csf("machine_no_id")]]."**".$to_color_id."__";//$row[csf("roll_mst_id")]."__";
			//$test_str .= $splited_roll_ref[$row[csf('barcode_no')]][$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']]."__";
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

if($action=="populate_barcode_data_from_requisition")
{
	$data=explode("**",$data);
	$bar_code=$data[0];
	$sys_id=$data[1];
	$cbo_store_id=$data[2];
	$company_name_array=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$machine_library = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");


	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array);

	/*$scanned_barcode_update_data=sql_select("select c.company_id, a.id as roll_upid, a.po_breakdown_id, a.barcode_no, a.roll_id, a.dtls_id, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.from_store, b.to_store, b.to_prod_id, b.from_prod_id, b.from_order_id, b.from_booking_without_order from pro_roll_details a, inv_item_transfer_dtls b , inv_item_transfer_mst c  where a.dtls_id=b.id and b.mst_id = c.id and c.entry_form = 82 and a.entry_form in(82) and a.status_active=1 and a.is_deleted=0 and a.mst_id=$sys_id");*/

	$scanned_barcode_update_data=sql_select("SELECT a.transfer_criteria, a.company_id, a.to_company, b.id as roll_upid, b.to_order_id, b.barcode_no, b.id as dtls_id, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.from_store, b.to_store, b.to_prod_id, b.from_prod_id, b.from_order_id, b.from_booking_without_order, b.roll_id
	from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
	where a.id=b.mst_id and b.entry_form=339 and a.entry_form=339 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.mst_id=$sys_id");
	foreach($scanned_barcode_update_data as $row)
	{
		$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']=$row[csf('roll_upid')];
		$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']=$row[csf('dtls_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['trans_id']=$row[csf('trans_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_trans_id']=$row[csf('to_trans_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']=$row[csf('to_order_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form']=$row[csf('from_trans_entry_form')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_store']=$row[csf('to_store')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_store']=$row[csf('from_store')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_prod_id']=$row[csf('to_prod_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']=$row[csf('from_prod_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_order_id']=$row[csf('from_order_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['company_id']=$row[csf('company_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_booking_without_order']=$row[csf('from_booking_without_order')];
		$barcode_update_data[$row[csf('barcode_no')]]['transfer_criteria']=$row[csf('transfer_criteria')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_company']=$row[csf('to_company')];

		if($row[csf('from_booking_without_order')] == 1)
		{
			$non_order_booking_buyer_po_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
		}
		else
		{
			$po_arr_book_booking_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];


			$all_order_id_from_n_to_arr[$row[csf('from_order_id')]] =$row[csf('from_order_id')];
			$all_order_id_from_n_to_arr[$row[csf('to_order_id')]] =$row[csf('to_order_id')];
		}
	}

	$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.febric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id, max(b.yarn_lot) as yarn_lot, max(b.yarn_count) as yarn_count, max(b.stitch_length) as stitch_length, max(b.brand_id) as brand_id, max(b.floor_id) as floor, max(b.room) as room, max(b.rack) as rack, max(b.self) as self, max(b.bin_box) as bin_box, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.amount, c.booking_without_order,c.id as roll_mst_id, b.yarn_rate, b.kniting_charge, c.rate
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($bar_code)
	group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty, c.amount, c.booking_without_order, c.id, b.yarn_rate, b.kniting_charge, c.rate");

	if(count($data_array)>0)
	{
		foreach($data_array as $val)
		{
			$splitted_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];

			if($val[csf("booking_without_order")] == 1 )
			{
				$receive_non_order_booking_buyer_po_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			}else{
				$receive_po_arr_book_booking_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			}

			/*if($val[csf("receive_basis")] == 2){
				$program_id_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
			}*/
		}

		$production_basis_sql = sql_select("SELECT a.barcode_no, b.receive_basis, a.booking_without_order, b.booking_id
			from pro_roll_details a, inv_receive_master b
			where a.mst_id = b.id and b.entry_form = 2 and a.entry_form = 2 and a.status_active =1 and a.barcode_no in ($bar_code)");
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

	$po_data_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.grouping FROM wo_po_details_master a, wo_po_break_down b WHERE a.id=b.job_id and b.id in (".implode(',', $all_order_id_from_n_to_arr).") ");
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

	$non_order_booking_buyer_po_arr = array_filter($non_order_booking_buyer_po_arr);
	//$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $non_order_booking_buyer_po_arr).")",'id','buyer_id');

	if(count($non_order_booking_buyer_po_arr)>0)
	{
		$non_order_sql = sql_select("SELECT id, buyer_id, booking_no from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $non_order_booking_buyer_po_arr).")");
		foreach ($non_order_sql as  $val)
		{
			$book_buyer_arr[$val[csf("id")]] = $val[csf("buyer_id")];
			$non_booking_arr[$val[csf("id")]] = $val[csf("booking_no")];
		}
	}

	$receive_po_arr_book_booking_arr = array_filter($receive_po_arr_book_booking_arr);
	if(count($receive_po_arr_book_booking_arr)>0)
	{
		if(!empty($program_with_order_arr))
		{
			$book_booking_sql=sql_select("SELECT a.po_break_down_id, a.booking_no , c.id as plan_id from wo_booking_dtls a left join ppl_planning_info_entry_mst b on a.booking_no = b.booking_no left join  ppl_planning_info_entry_dtls c on b.id = c.mst_id and c.id in (".implode(",", $program_with_order_arr).") where  a.is_deleted=0 and a.status_active=1 and a.booking_type in (1,4) and a.po_break_down_id in (".implode(',', $receive_po_arr_book_booking_arr).") group by a.po_break_down_id, a.booking_no ,c.id");

			foreach ($book_booking_sql as $val)
			{
				$book_booking_arr_plan_wise[$val[csf("po_break_down_id")]][$val[csf("plan_id")]] = $val[csf("booking_no")];
			}
		}
		else
		{
			$book_booking_arr=return_library_array("SELECT po_break_down_id, booking_no from wo_booking_dtls where is_deleted=0 and status_active=1 and booking_type=1 and po_break_down_id in (". implode(',', $receive_po_arr_book_booking_arr) .")",'po_break_down_id','booking_no');
		}
	}

	$issue_barcode_data=sql_select("SELECT a.id, a.barcode_no,a.entry_form, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and b.entry_form = 61 and a.entry_form =61 and a.is_returned !=1 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in ($bar_code)");

	foreach($issue_barcode_data as $row)
	{
		$issue_barcode_array[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	// order and non-order mixed not allowed
	// echo $data_array[0][csf("booking_without_order")];die;
	//if ($data_array[0][csf("booking_without_order")]==0) 
	if (!empty($po_arr_book_booking_arr)) 
	{
		$sql= "SELECT a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,a.store_id as to_store, d.status_active, e.buyer_name as buyer_id, c.po_breakdown_id, b.floor_id as to_floor_id, b.room as to_room,  cast(b.rack as varchar2(100)) as to_rack, b.self as to_shelf, b.bin_box as to_bin_box, b.body_part_id, c.id
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e
		WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 
		and c.re_transfer=0 and c.booking_without_order=0 and c.po_breakdown_id in(". implode(',', $po_arr_book_booking_arr) .")
		union all
		select a.transfer_system_id as recv_number, null as location_id, from_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,b.to_store, d.status_active, e.buyer_name as buyer_id, c.po_breakdown_id, b.to_floor_id, b.to_room, cast(b.to_rack as varchar2(100)) as to_rack, b.to_shelf, b.to_bin_box, b.to_body_part as body_part_id, c.id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and d.job_id=e.id and a.entry_form in(83,183) and c.entry_form in(83,183) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.re_transfer=0 
		and c.booking_without_order=0 and c.po_breakdown_id in(". implode(',', $po_arr_book_booking_arr) .")
		union all
		select a.transfer_system_id as recv_number, null as location_id, to_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,b.to_store, d.status_active, e.buyer_name as buyer_id, c.po_breakdown_id, b.to_floor_id, b.to_room, cast(b.to_rack as varchar2(100)) as to_rack, b.to_shelf, b.to_bin_box, b.to_body_part as body_part_id, c.id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_po_break_down d, wo_po_details_master e
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and d.job_id=e.id and a.entry_form in(82) and c.entry_form in(82) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.re_transfer=0 
		and a.transfer_criteria in (1,2,4) and c.booking_without_order=0 and c.po_breakdown_id in(". implode(',', $po_arr_book_booking_arr) .")
		union all
		select a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form, a.store_id as to_store, d.status_active, e.buyer_name as buyer_id, c.po_breakdown_id,	b.floor_id as to_floor_id, b.room as to_room, cast(b.rack as varchar2(100)) as to_rack, b.self as to_shelf, b.bin_box as to_bin_box, b.body_part_id, c.id 
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and b.trans_id<>0 and a.entry_form in(84) and c.entry_form in(84) 
		and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.re_transfer=0 and c.booking_without_order=0 and c.po_breakdown_id in(". implode(',', $po_arr_book_booking_arr) .")";

	}
	// echo $sql;die;
	$result = sql_select($sql);
	foreach ($result as $row) 
	{
		$avalable_barcode_arr[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]] = $row[csf('barcode_no')];

		$current_barcode_store_room_rack[$row[csf('barcode_no')]]["to_store"] = $row[csf('to_store')];
		$current_barcode_store_room_rack[$row[csf('barcode_no')]]["to_floor_id"] = $row[csf('to_floor_id')];
		$current_barcode_store_room_rack[$row[csf('barcode_no')]]["to_room"] = $row[csf('to_room')];
		$current_barcode_store_room_rack[$row[csf('barcode_no')]]["to_rack"] = $row[csf('to_rack')];
		$current_barcode_store_room_rack[$row[csf('barcode_no')]]["to_shelf"] = $row[csf('to_shelf')];
		$current_barcode_store_room_rack[$row[csf('barcode_no')]]["to_bin_box"] = $row[csf('to_bin_box')];
		$current_barcode_store_room_rack[$row[csf('barcode_no')]]["body_part_id"] = $row[csf('body_part_id')];
		$current_barcode_store_room_rack[$row[csf('barcode_no')]]["entry_form"] = $row[csf('entry_form')];
		$current_barcode_store_room_rack[$row[csf('barcode_no')]]["roll_table_id"] = $row[csf('id')];
	}


	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id, b.store_id,b.floor_id,b.room_id, b.rack_id,b.shelf_id,b.bin_id, a.floor_room_rack_name floor_name, c.floor_room_rack_name room_name, d.floor_room_rack_name rack_name, e.floor_room_rack_name shelf_name , f.floor_room_rack_name bin_name 
		from lib_floor_room_rack_dtls b
		left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
		left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
		left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
		left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
		left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0
		where b.status_active=1 and b.is_deleted=0 and b.store_id=$cbo_store_id";
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
			$lib_floor_arr[$company][$store_id] .= $floor_id.",";
		}

		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_room_arr[$company][$store_id][$floor_id] .= $room_id.",";
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
			$lib_rack_arr[$company][$store_id][$floor_id][$room_id] .= $rack_id.",";
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
			$lib_shelf_arr[$company][$store_id][$floor_id][$room_id][$rack_id] .= $shelf_id.",";
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
			$lib_bin_arr[$company][$store_id][$floor_id][$room_id][$rack_id][$shelf_id].= $bin_id.",";
		}
	}
	unset($lib_floor_room_data_arr);

	//echo "10";
	//print_r($lib_floor_arr);die;

	$roll_details_array=array(); $barcode_array=array();
	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			if($issue_barcode_array[$row[csf('barcode_no')]]=="")
			{
				$from_order_id =  $barcode_update_data[$row[csf('barcode_no')]]['from_order_id'];
				//if ($avalable_barcode_arr[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]] !="") 
				if ($avalable_barcode_arr[$row[csf('barcode_no')]][$from_order_id] !="") 
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

					//echo $po_id;die;

					if($row[csf("booking_without_order")]==1)
					{
						//$buyer_name=$buyer_arr[$book_buyer_arr[$po_id]];
						//$booking_no_fab="";
						$booking_no_fab=$non_booking_arr[$row[csf("po_breakdown_id")]];
					}
					else
					{
						//$buyer_name=$buyer_arr[$po_details_array[$po_id]['buyer_name']];
						//$booking_no_fab=$book_booking_arr[$po_id];
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
					$transfer_criteria = $barcode_update_data[$row[csf('barcode_no')]]['transfer_criteria'];
					$to_company = $barcode_update_data[$row[csf('barcode_no')]]['to_company'];

					if($from_booking_without_order == 1)
					{
						$buyer_name=$buyer_arr[$book_buyer_arr[$from_order_id]];
					}
					else
					{
						$buyer_name=$buyer_arr[$po_details_array[$from_order_id]['buyer_name']];
					}

					if($barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 82 || $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 83)
					{
						$booking_no_fab = $booking_no_fab . " (T)";
					}

					$to_store = $current_barcode_store_room_rack[$row[csf('barcode_no')]]["to_store"];
					$to_floor_id = $current_barcode_store_room_rack[$row[csf('barcode_no')]]["to_floor_id"];
					$to_room = $current_barcode_store_room_rack[$row[csf('barcode_no')]]["to_room"];
					$to_rack = $current_barcode_store_room_rack[$row[csf('barcode_no')]]["to_rack"];
					$to_shelf = $current_barcode_store_room_rack[$row[csf('barcode_no')]]["to_shelf"];
					$to_bin_box = $current_barcode_store_room_rack[$row[csf('barcode_no')]]["to_bin_box"];
					$body_part_id = $current_barcode_store_room_rack[$row[csf('barcode_no')]]["body_part_id"];
					$entry_form = $current_barcode_store_room_rack[$row[csf('barcode_no')]]["entry_form"];
					$roll_mst_id = $current_barcode_store_room_rack[$row[csf('barcode_no')]]["roll_table_id"];


					if($transfer_criteria==4)
					{
						$multi_floor = chop($lib_floor_arr[$to_company][$to_store],",");
						$multi_room = chop($lib_room_arr[$to_company][$to_store][$to_floor_id],",");
						$multi_rack = chop($lib_rack_arr[$to_company][$to_store][$to_floor_id][$to_room],",");
						$multi_self = chop($lib_shelf_arr[$to_company][$to_store][$to_floor_id][$to_room][$to_rack],",");
						$multi_bin = chop($lib_bin_arr[$to_company][$to_store][$to_floor_id][$to_room][$to_rack][$to_shelf],",");
					}else{
						$multi_floor = "0";
						$multi_room = "0";
						$multi_rack = "0";
						$multi_self = "0";
						$multi_bin = "0";
					}

					$yarn_rate = $row[csf("yarn_rate")];
					$kniting_charge = $row[csf("kniting_charge")];
					$roll_rate = $row[csf("rate")];

					//echo $multi_floor."====="."[".$to_company."][".$to_store."][".$to_floor_id."][".$to_room."][".$to_rack."][".$to_shelf."]";die;
					//$store_id = $barcode_update_data[$row[csf('barcode_no')]]['from_store'];
					//$entry_form = $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'];
					$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];
					$compsition_description=$constructtion_arr[$row[csf("febric_description_id")]]." ".$composition_arr[$row[csf("febric_description_id")]];

					$barcodeData.=$row[csf('id')]."**".$barcode_update_data[$row[csf('barcode_no')]]['company_id']."**".$body_part[$body_part_id]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$to_store."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$to_rack."**".$to_shelf."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$from_order_id."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$from_order_id]['job_no']."**".$po_details_array[$from_order_id]['buyer_name']."**".$buyer_name."**".$po_details_array[$from_order_id]['po_number']."**".$row[csf("color_id")]."**".$store_arr[$to_store]."**".$body_part_id."**".$row[csf("brand_id")]."**".$row[csf("machine_no_id")]."**".$entry_form."**".$row[csf("booking_without_order")]."**".$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']."**".$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['trans_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_trans_id']."**".$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['po_number']."**".$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']."**".$nxProcessedBarcode[$row[csf("barcode_no")]]."**".$roll_mst_id."**".$booking_no_fab."**".$row[csf("amount")]."**".$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']."**".$from_booking_without_order."**".$splited_roll_ref[$row[csf('barcode_no')]][$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']]."**".$to_floor_id."**".$to_room."**".$po_details_array[$from_order_id]['grouping']."**".$to_bin_box."**".$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['job_no']."**".$multi_floor."**".$multi_room."**".$multi_rack."**".$multi_self."**".$multi_bin."**".$roll_rate."**".$yarn_rate."**".$kniting_charge."**".$plan_id."**".$machine_library[$row[csf("machine_no_id")]]."__";


					//$row[csf("roll_mst_id")]."__";
					//$test_str .= $splited_roll_ref[$row[csf('barcode_no')]][$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']]."__";
					
				}
				else
				{
					$not_available_barcode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
				}
			}
		}
		if ($barcodeData=="") 
		{
			$barcode_nos=implode(",",$not_available_barcode);
			$trans_check_sql = sql_select("SELECT a.transfer_system_id, b.barcode_no from inv_item_transfer_mst a, pro_roll_details b where a.id=b.mst_id and a.entry_form in (83,133,82,180,110,183) and b.barcode_no in($barcode_nos) and b.entry_form in (83,133,82,180,110,183) and b.re_transfer =0 and b.status_active = 1 and b.is_deleted = 0");

			/*foreach ($trans_check_sql as $key => $row) 
			{
				echo $transfer_number='82####'.$row[csf("transfer_system_id")];
			}*/
			echo $transfer_number='82####'.$trans_check_sql[0][csf("transfer_system_id")];
			
		}
		else
		{
			echo chop($barcodeData,"__");			
		}
		//echo $test_str;die;
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
	                    <th>Transfer Date Range</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="100">Please Enter Transfer No</th>
	                    <th id="booking_td_up" width="120">Booking No</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                    	<input type="hidden" name="hidden_system_id" id="hidden_system_id">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td align="center">
	                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
						  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
						</td>
	                    <td align="center">
	                    	<?
	                       		$search_by_arr=array(1=>"Transfer No", 2=>"Barcode Number", 3=>"Internal Ref. No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
							?>
	                    </td>
	                    <td align="center" id="search_by_td">
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	                     <td align="center" id="booking_td">
	                        <input type="text" style="width:90px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />
	                    </td>
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_booking_no').value, 'create_challan_search_list_view', 'search_div', 'grey_fabric_transfer_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$bookingNo =$data[5];

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
	if(trim($data[0])!="")
	{
		if($search_by==2) $search_field_cond="and c.barcode_no like '$search_string'";
	}
	if(trim($data[0])!="")
	{
		if($search_by==3) $search_field_cond="and d.grouping like '$search_string'";
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



	if($bookingNo !=""){
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


	/*$sql = "SELECT a.id, $year_field a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, max(b.to_store) as to_store
	FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, wo_po_break_down d
	where a.id=b.mst_id and a.id=c.mst_id and b.from_order_id=d.id and b.id=c.dtls_id and a.entry_form=82 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond $cond_for_booking
	group by a.id, a.insert_date, a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no
	order by id";*/

	if($company_id) $company_cond = " and a.company_id=$company_id";

	$sql = "SELECT a.id, $year_field a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, max(b.to_store) as to_store
	FROM inv_item_transfer_mst a, inv_item_transfer_dtls b left join wo_po_break_down d on b.from_order_id=d.id, pro_roll_details c 
	where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.entry_form=82 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $search_field_cond $date_cond $cond_for_booking
	group by a.id, a.insert_date, a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no
	order by a.id";
	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="40">Trans. No</th>
            <th width="40">Year</th>
            <th width="125">Transfer Criteria</th>
            <th width="120">From Company</th>
            <th width="120">To Company</th>
            <th width="120">To Store</th>
            <th width="70">Challan</th>
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
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="40" align="center"><p>&nbsp;<? echo $row[csf('transfer_prefix_number')]; ?></p></td>
                    <td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="125"><p><? echo $item_transfer_criteria[$row[csf('transfer_criteria')]]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $company_arr[$row[csf('to_company')]]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $store_arr[$row[csf('to_store')]]; ?>&nbsp;</p></td>
                    <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
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

if($action=="itemTransfer_requisition_popup")
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
	                    <th>Requisition Date Range</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="100">Please Enter Requisition No</th>
	                    <th id="booking_td_up" width="120">Booking No</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                    	<input type="hidden" name="hidden_system_id" id="hidden_system_id">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td align="center">
	                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
						  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
						</td>
	                    <td align="center">
	                    	<?
	                       		$search_by_arr=array(1=>"Requisition No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
							?>
	                    </td>
	                    <td align="center" id="search_by_td">
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	                     <td align="center" id="booking_td">
	                        <input type="text" style="width:90px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />
	                    </td>
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_booking_no').value+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+<? echo $cbo_store_name; ?>, 'create_requisition_search_list_view', 'search_div', 'grey_fabric_transfer_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

if($action=="create_requisition_search_list_view")
{
	$data = explode("_",$data);

	$search_string="%".trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$bookingNo =$data[5];
	$transfer_criteria =$data[6];
	$cbo_store_name =$data[7];

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

	if($db_type==0)
	{
		$year_field="YEAR(a.insert_date) as year,";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') as year,";
	}
	else $year_field="";//defined Later



	if($bookingNo !=""){
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

	$sql_trans_requ=sql_select("SELECT a.transfer_requ_id from inv_item_transfer_mst a, inv_item_transfer_dtls b
	where a.id=b.mst_id and a.item_category=13 and a.company_id=$company_id and a.transfer_criteria=$transfer_criteria  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond and a.entry_form=82 and a.transfer_requ_id IS NOT NULL
	group by a.transfer_requ_id");

	$requ_id="";
	foreach ($sql_trans_requ as $row)
	{
		if ($requ_id=="")
		{
			$requ_id.=$row[csf('transfer_requ_id')];
		}
		else
		{
			$requ_id.=', '.$row[csf('transfer_requ_id')];
		}
	}
	//echo $requ_id;
	/*if ($requ_id!="")
	{
		$requ_id_cond= "and a.id not in($requ_id)";
	}*/

	$sql = "SELECT a.id, $year_field a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, max(b.to_store) as to_store
	from  inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
	where a.id=b.mst_id and a.entry_form=339 and a.requisition_status=1 and a.status_active=1 and a.is_deleted=0 and a.is_approved=1 and a.transfer_criteria=$transfer_criteria and a.company_id=$company_id $search_field_cond $date_cond $cond_for_booking $requ_id_cond and b.to_store=$cbo_store_name
	group by a.id, a.insert_date, a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no order by id";
	// echo $sql;//die;
	$result = sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="40">Trans. No</th>
            <th width="40">Year</th>
            <th width="125">Transfer Criteria</th>
            <th width="120">From Company</th>
            <th width="120">To Company</th>
            <th width="120">To Store</th>
            <th width="70">Challan</th>
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
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="40" align="center"><p>&nbsp;<? echo $row[csf('transfer_prefix_number')]; ?></p></td>
                    <td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="125"><p><? echo $item_transfer_criteria[$row[csf('transfer_criteria')]]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $company_arr[$row[csf('to_company')]]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $store_arr[$row[csf('to_store')]]; ?>&nbsp;</p></td>
                    <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
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
	$data_arr = explode("**", $data);
	$requisition=$data_arr[0];
	$type=$data_arr[1];
	if ($type==0) 
	{
		$requ_cond=" and a.id=$requisition";
	}
	else
	{
		$requ_cond=" and a.transfer_system_id='$requisition'";
	}
	//$sql = "select id, company_id, issue_number, knit_dye_source, knit_dye_company, issue_date, batch_no, issue_purpose from inv_issue_master where id=$data and entry_form=61";
	$sql = "SELECT a.id, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, max(b.to_store) as to_store, a.remarks, sum(b.transfer_qnty) as transfer_qnty
	from  inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
	where a.id=b.mst_id and a.entry_form=339 $requ_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.id, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, a.remarks ";
	// echo $sql;die;
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
		echo "load_drop_down( 'requires/grey_fabric_transfer_roll_wise_controller','".$row[csf("transfer_criteria")].'_'.$to_company_id."', 'load_drop_store_to', 'store_td' );\n";

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
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#cbo_store_name').val(".$row[csf("to_store")].");\n";
		echo "$('#hidd_requi_qty').val(".$balance.");\n";
  	}
	exit();
}

if($action=="requisition_barcode_nos")
{
	/*if($db_type==0)
	{
		$barcode_nos=return_field_value("group_concat(barcode_no order by id desc) as barcode_nos","inv_item_transfer_requ_dtls","entry_form=339 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
	}
	else if($db_type==2)
	{
		$barcode_nos=return_field_value("LISTAGG(barcode_no, ',') WITHIN GROUP (ORDER BY id desc) as barcode_nos","inv_item_transfer_requ_dtls","entry_form=339 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
	}
	echo $barcode_nos;*/
	$barcode_nos_sql=sql_select("SELECT barcode_no as barcode_nos FROM inv_item_transfer_requ_dtls WHERE entry_form=339 and status_active=1 and is_deleted=0 and mst_id=$data");
	foreach ($barcode_nos_sql as $key => $row) 
	{
		if($row[csf("barcode_nos")]!="") $all_barcode.=$row[csf("barcode_nos")].",";
	}
	$barcode_nos=implode(",",array_unique(explode(",",chop($all_barcode,","))));
	echo $barcode_nos;
	exit();
}

if($action=="populate_data_from_data")
{
	//$sql = "select id, company_id, issue_number, knit_dye_source, knit_dye_company, issue_date, batch_no, issue_purpose from inv_issue_master where id=$data and entry_form=61";
	$sql = "SELECT a.id, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, max(b.to_store) as to_store, a.remarks, a.transfer_requ_id, a.transfer_requ_no,a.delivery_company_name, sum(b.transfer_qnty) as transfer_qnty
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b
	where a.id=b.mst_id and a.entry_form=82 and a.id=$data
	group by a.id, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, a.remarks, a.transfer_requ_id, a.transfer_requ_no,a.delivery_company_name ";
	//echo $sql;
	$res = sql_select($sql);
	$requ_id=$res[0][csf("transfer_requ_id")];
	if ($requ_id!="") 
	{
		/*$requ_qnty = return_field_value("sum(b.transfer_qnty) as transfer_requ_qnty", "inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b", "a.id=b.mst_id and a.entry_form=339 and a.id=$requ_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ", "transfer_requ_qnty");*/
		$requ_qnty = "SELECT a.id,b.from_prod_id, b.from_order_id, b.feb_description_id, b.gsm, sum(b.transfer_qnty) as transfer_requ_qnty from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b where a.id=b.mst_id and a.entry_form=339 and a.id=$requ_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.from_prod_id, b.from_order_id, b.feb_description_id, b.gsm";
		$requ_result = sql_select($requ_qnty);
		foreach ($requ_result as $key => $row) 
		{
			$requ_array[$row[csf("id")]]['from_order_id']=$row[csf("from_order_id")];
			$requ_array[$row[csf("id")]]['from_prod_id']=$row[csf("feb_description_id")];
			$requ_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
			$requ_array[$row[csf("id")]]['transfer_requ_qnty']+=$row[csf("transfer_requ_qnty")];
		}

		$prev_transf_sql = "SELECT sum(b.transfer_qnty) as prev_transfer_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b
		where a.id=b.mst_id and a.entry_form=82 and a.id!=$data and a.transfer_requ_id=$requ_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$prev_transf_res = sql_select($prev_transf_sql);
		$prev_transfer_qnty=$prev_transf_res[0][csf("prev_transfer_qnty")];
	}
	// echo "<pre>"; print_r($requ_array);die;
	
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
		//echo "load_drop_down( 'requires/grey_fabric_transfer_roll_wise_controller','".$row[csf("transfer_criteria")].'_'.$row[csf("company_id")]."', 'load_drop_store', 'store_td' );\n";
		echo "load_drop_down( 'requires/grey_fabric_transfer_roll_wise_controller','".$row[csf("transfer_criteria")].'_'.$to_company_id."', 'load_drop_store_to', 'store_td' );\n";

		echo "$('#txt_transfer_no').val('".$row[csf("transfer_system_id")]."');\n";
		echo "$('#cbo_transfer_criteria').val(".$row[csf("transfer_criteria")].");\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','true')".";\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		//echo "$('#cbo_to_company_id').val(".$row[csf("to_company")].");\n";
		echo "$('#cbo_to_company_id').attr('disabled','true')".";\n";
		echo "$('#txt_transfer_date').val('".change_date_format($row[csf("transfer_date")])."');\n";
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#cbo_store_name').val(".$row[csf("to_store")].");\n";
		if($row[csf("transfer_criteria")] != 4)
		{
			echo "$('#cbo_store_name').attr('disabled','true')".";\n";
		}

		echo "$('#txt_requisition_no').attr('disabled','true')".";\n";
		echo "$('#txt_delv_company_id').val('".$row[csf("delivery_company_name")]."');\n";

		if ($row[csf("transfer_requ_id")]!="")
		{
			// $requ_array[$row[csf("transfer_requ_id")]]['transfer_qnty'];
			$balance=$requ_array[$row[csf("transfer_requ_id")]]['transfer_requ_qnty'];
			$from_product=$requ_array[$row[csf("transfer_requ_id")]]['from_prod_id'];
			$from_gsm=$requ_array[$row[csf("transfer_requ_id")]]['gsm'];
			$from_order=$requ_array[$row[csf("transfer_requ_id")]]['from_order_id'];

			echo "$('#txt_requisition_no').val('".$row[csf("transfer_requ_no")]."');\n";
			echo "$('#txt_requisition_id').val('".$row[csf("transfer_requ_id")]."');\n";
			//echo "$('#txt_bar_code_num').attr('disabled','true')".";\n";
			echo "$('#hidd_requi_qty').val(".$balance.");\n";
			echo "$('#txt_from_detar_id').val(".$from_product.");\n";
			echo "$('#txt_from_gsm').val(".$from_gsm.");\n";
			echo "$('#txt_from_order_id').val(".$from_order.");\n";
			echo "$('#txt_prev_transfer_qnty').val(".$prev_transfer_qnty.");\n";
		}
		else
		{
			echo "$('#txt_requisition_no').val('');\n";
			echo "$('#txt_requisition_id').val('');\n";
			echo "$('#hidd_requi_qty').val('');\n";
			echo "$('#txt_from_detar_id').val('');\n";
			echo "$('#txt_from_gsm').val('');\n";
			echo "$('#txt_from_order_id').val('');\n";
			echo "$('#txt_prev_transfer_qnty').val('');\n";
			echo "$('#txt_bar_code_num').removeAttr('disabled','')".";\n";
		}


		echo "$('#update_id').val(".$row[csf("id")].");\n";
  	}
	exit();
}

if($action=="barcode_nos")
{
	$barcode_sql="SELECT barcode_no as barcode_nos from pro_roll_details where entry_form=82 and status_active=1 and is_deleted=0 and mst_id=$data";
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
	if($company_id>0) $disable=1; else $disable=0;$requisition_id="'$requisition_id'";
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

				if(id!="")
				{
					var no_of_roll = id.split(',').length;
				}
				else
				{
					var no_of_roll = "0";
				}
				$('#hidden_selected_row_count').val(no_of_roll);
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
							echo create_drop_down( "cbo_location_name", 100, "select id,location_name from lib_location where company_id=$company_id and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down( 'grey_fabric_transfer_roll_wise_controller',this.value, 'load_drop_store_2', 'store_td_id' );" );
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
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('barcode_no').value+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+<? echo $cbo_store_name; ?>+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_store_name').value+'_'+document.getElementById('cbo_order_status').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_status').value+'_'+<? echo $requisition_id; ?>, 'create_barcode_search_list_view', 'search_div', 'grey_fabric_transfer_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1,tableFilters);reset_hide_field();')" style="width:100px;" />
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
				col: [17],
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

if($action=="create_barcode_search_list_view")
{
	$data = explode("_",$data);

	$location_id=trim($data[0]);
	$order_no=$data[1];
	$company_id =$data[2];
	$file_no =trim($data[3]);
	$ref_no =trim($data[4]);
	$barcode_no =trim($data[5]);
	$transfer_cateria =trim($data[6]);
	$store_id=trim($data[7]);
	$bookingNo=trim($data[8]);
	$storeName=trim($data[9]);
	$order_status=trim($data[10]);
	$cbo_year=trim($data[11]);

	$cbo_buyer_name=trim($data[12]);
	$txt_job_no=trim($data[13]);
	$txt_date_from=trim($data[14]);
	$txt_date_to=trim($data[15]);
	$cbo_status=trim($data[16]);
	$requisition_id=trim($data[17]);

	if($order_status == 1)
	{
		$booking_without_order =0;
	}
	else
	{
		$booking_without_order =1;
		if($ref_no!="")
		{
			$sample_ref_cond = " and d.grouping like '%$ref_no%'";
		}

		if($cbo_buyer_name > 0) 
		{
			$sample_ref_cond .=" and d.buyer_id = '$cbo_buyer_name'";
		}
	}

	//echo $store_id.jahid;die;

	$search_field_cond="";
	if($order_no!="") $search_field_cond=" and d.po_number like '%$order_no%'";
	if($file_no!="") $search_field_cond.=" and d.file_no like '%$file_no%'";
	if($ref_no!="") $search_field_cond.=" and d.grouping like '%$ref_no%'";
	if($txt_job_no!="") $search_field_cond.=" and d.job_no_mst like '%$txt_job_no%'";

	if ($txt_date_from!="" &&  $txt_date_to!="")
	{
		if($db_type==0)
		{
			$search_field_cond .= " and d.pub_shipment_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$search_field_cond .= " and d.pub_shipment_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}

	if($cbo_status !=0)
	{
		$search_field_cond .=" and d.status_active =$cbo_status ";
	}

	
	if($storeName>0) $store_cond_1=" and a.store_id =$storeName";
	if($storeName>0) $store_cond_2=" and b.to_store =$storeName";

	$location_cond="";
	if($location_id>0) $location_cond=" and a.location_id=$location_id";

	if($barcode_no!="")
	{
		$barcode_cond=" and c.barcode_no='$barcode_no'";
	}

	if($bookingNo !="")
	{
		//"query from plan to get program_no";
		/*
			$program_arr=array();$programIds="";
			$qry_plan=sql_select( "select a.booking_no,b.id as progran_no from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.booking_no like '%$bookingNo%' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

			foreach ($qry_plan as $row) {
				$program_arr[$row[csf('progran_no')]]["progran_no"]=$row[csf('progran_no')];
				$programIds.="'".$row[csf('progran_no')]."'".",";

			}
			$programIds=chop($programIds,",");
		*/


		if($db_type==0)
		{
			$booking_year_cond = " and year(d.insert_date) =$cbo_year";
		}
		else{
			$booking_year_cond = " and to_char(d.insert_date,'YYYY') =$cbo_year";
		}

		$sample_booking_cond = " and d.booking_no like '%$bookingNo%' $booking_year_cond";
	}


	if (($order_no!="" || $file_no!="" || $ref_no!="" || $txt_job_no!="" || $bookingNo !="" || $cbo_buyer_name > 0 || $cbo_status !=0 || ($txt_date_from!="" &&  $txt_date_to!="")) && $booking_without_order==0)
	{

		if($db_type==0)
		{
			$job_year_cond = " and year(b.insert_date) =$cbo_year";
		}
		else{
			$job_year_cond = " and to_char(b.insert_date,'YYYY') =$cbo_year";
		}

		if($cbo_buyer_name > 0) 
		{
			$buyer_name_cond =" and b.buyer_name = '$cbo_buyer_name'";
			$buyer_id_cond =" and a.buyer_id = '$cbo_buyer_name'";
		}

		if($bookingNo != "")
		{
			$po_sql = sql_select("select d.id as po_id, b.booking_no from wo_po_break_down d, wo_booking_dtls b, wo_booking_mst a where d.id = b.po_break_down_id and b.booking_no=a.booking_no and a.company_id=$company_id and a.booking_type in (1,4) and b.status_active =1 and b.booking_no like '%$bookingNo%' and a.booking_year=$cbo_year $search_field_cond $buyer_id_cond group by d.id, b.booking_no");
		}
		else
		{
			$po_sql = sql_select("select d.id as po_id from wo_po_break_down d, wo_po_details_master b where d.job_id=b.id and b.company_name=$company_id and b.status_active =1  $search_field_cond $job_year_cond $buyer_name_cond");
		}

		if(empty($po_sql))
		{
			echo "Reference not found";
			die;
		}

		foreach ($po_sql as $val)
		{
			$trans_po_arr[$val[csf("po_id")]] = $val[csf("po_id")];
		}

		$trans_po_arr = array_filter(array_unique($trans_po_arr));
		if(count($trans_po_arr)>0)
		{
			$all_po_nos = implode(",", $trans_po_arr);
			$all_po_cond=""; $poCond="";
			if($db_type==2 && count($trans_po_arr)>999)
			{
				$trans_po_arr_chunk=array_chunk($trans_po_arr,999) ;
				foreach($trans_po_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$poCond.=" c.po_breakdown_id in($chunk_arr_value) or ";
				}

				$all_po_cond.=" and (".chop($poCond,'or ').")";
			}
			else
			{
				$all_po_cond=" and c.po_breakdown_id in($all_po_nos)";
			}
		}
	}

	/*
		if(!empty($program_arr)){
			//"query from knitting production with entry form 2 to get the barcodes";
			$barcode_arr=array();$barcodeAllNo="";
			$qry_roll_dtls=sql_select( "select b.barcode_no,b.booking_no from inv_receive_master a,pro_roll_details b where a.id=b.mst_id and a.receive_basis=2 and  a.booking_id in($programIds) and b.entry_form in(2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
			foreach ($qry_roll_dtls as $row) {

				$barcode_arr[$row[csf('booking_no')]]["barcode_no"]=$row[csf('barcode_no')];
				$barcodeAllNo.="'".$row[csf('barcode_no')]."'".",";

			}
			$barcodeAllNo=chop($barcodeAllNo,",");
		}

		if(!empty($barcode_arr)) $barcode_cond_for_booking=" and c.barcode_no in($barcodeAllNo)";
	*/

	
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$buyer_arr=return_library_array( "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name",'id','buyer_name');
	$color_name_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');


	if($transfer_cateria == 1) // || $transfer_cateria == 2
	{
		if($store_id) {
			$without_store_cond_1=" and a.store_id!=$store_id";
			$without_store_cond_2=" and b.to_store!=$store_id";
		} 
		else 
		{
			$without_store_cond_1="";
			$without_store_cond_2="";
		}
	}

	// for requisition ==========================
	$job_no_cond='';$non_order_booking_cond='';
	if ($requisition_id!="") 
	{
		if($db_type==0)
		{
			$job_year_cond = " and year(b.insert_date) =$cbo_year";
		}
		else{
			$job_year_cond = " and to_char(b.insert_date,'YYYY') =$cbo_year";
		}
		
		$requisition_data=sql_select("SELECT b.barcode_no, b.from_prod_id, b.from_order_id, b.from_booking_without_order
		from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b 
		where a.id=b.mst_id and b.entry_form=339 and a.entry_form=339 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$requisition_id");
		foreach ($requisition_data as $row) 
		{
			$requisition_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
			$requisition_from_prod_arr[$row[csf('from_prod_id')]] = $row[csf('from_prod_id')];
			if ($row[csf('from_booking_without_order')]==0) 
			{
				$requisition_from_order_id_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
			}
			else
			{
				$requisition_from_non_order_id_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
			}
		}
		$requisition_barcode_arr = array_filter(array_unique($requisition_barcode_arr));
		$requisition_from_order_id_arr = array_filter(array_unique($requisition_from_order_id_arr));
		$requisition_from_non_order_id_arr = array_filter(array_unique($requisition_from_non_order_id_arr));

		if(count($requisition_barcode_arr)>0)
		{
			$requisition_all_barcode_nos = implode(",", $requisition_barcode_arr);
			$BarCond = $requisition_all_barcode_cond = "";

			if($db_type==2 && count($requisition_barcode_arr)>999)
			{
				$barcode_arr_chunk=array_chunk($requisition_barcode_arr,999) ;
				foreach($barcode_arr_chunk as $chunk_arr)
				{
					$BarCond.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";
				}

				$requisition_all_barcode_cond.=" and (".chop($BarCond,'or ').")";
			}
			else
			{
				$requisition_all_barcode_cond=" and a.barcode_no in($requisition_all_barcode_nos)";
			}
		}

		$from_requisition_sql = sql_select("SELECT a.barcode_no, a.receive_basis, b.gsm, b.width as dia, b.color_id, b.color_range_id, b.febric_description_id from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id = b.id and a.entry_form = 2 and a.status_active=1 and b.status_active =1 and b.is_deleted =0 and a.is_deleted =0 $requisition_all_barcode_cond");
		$requisition_gsm_arr=$requisition_dia_arr=$requisition_color_id_arr=$requisition_color_range_id_arr=$requisition_feb_descr_arr=array();
		foreach ($from_requisition_sql as $row)
		{
			$requisition_gsm_arr[$row[csf("gsm")]] = $row[csf("gsm")];
			$requisition_dia_arr[$row[csf("dia")]] = $row[csf("dia")];
			$requisition_color_id_arr[$row[csf("color_id")]] = $row[csf("color_id")];
			$requisition_color_range_id_arr[$row[csf("color_range_id")]] = $row[csf("color_range_id")];
			$requisition_feb_descr_arr[$row[csf("febric_description_id")]] = $row[csf("febric_description_id")];
		}
		$gsm_cond= "and b.gsm in ("."'" . implode("','", $requisition_gsm_arr) . "'".")";
		$dia_cond= "and b.width in ("."'" . implode("','", $requisition_dia_arr) . "'".")";
		$color_id_cond= "and b.color_id in ("."'" . implode("','", $requisition_color_id_arr) . "'".")";
		$color_range_id_cond= "and b.color_range_id in ("."'" . implode("','", $requisition_color_range_id_arr) . "'".")";
		$febric_description_id_cond= "and b.febric_description_id in ("."'" . implode("','", $requisition_feb_descr_arr) . "'".")";
		// echo $febric_description_id_cond.'='.$gsm_cond;die;

		$job_ref_data_array=sql_select("SELECT a.job_no, b.id
		FROM wo_po_details_master a, wo_po_break_down b 
		WHERE a.job_no=b.job_no_mst and a.status_active =1 and b.status_active=1  and b.id in (".implode(",", $requisition_from_order_id_arr).") $job_year_cond group by a.job_no, b.id");
		$job_no_array=array(); $job_no_cond='';
		foreach($job_ref_data_array as $row)
		{
			$job_no_array[$row[csf("job_no")]]=$row[csf("job_no")];
			$po_id_array[$row[csf("id")]]=$row[csf("id")];
		}
		$job_no_cond= "and e.job_no in ("."'" . implode("','", $job_no_array) . "'".")";
		$po_id_cond= "and d.id in ("."'" . implode("','", $po_id_array) . "'".")";

		$non_order_booking_sql = sql_select("SELECT id, buyer_id, booking_no from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $requisition_from_non_order_id_arr).")");
		$non_order_booking_arr=array(); $non_order_booking_cond='';
		foreach ($non_order_booking_sql as  $val) 
		{
			$non_order_booking_arr[$val[csf("booking_no")]] = $val[csf("booking_no")];
		}
		$non_order_booking_cond= "and d.booking_no in ("."'" . implode("','", $non_order_booking_arr) . "'".")";
	}
	// =======================================

	if($booking_without_order==0)
	{
		$sql= "SELECT a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,a.store_id as to_store, d.status_active, e.buyer_name as buyer_id,cast(b.rack as varchar2(100)) as rack, b.self, b.floor_id, b.room
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e
		WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.re_transfer=0 $search_field_cond $barcode_cond $location_cond $without_store_cond_1 $store_cond_1 $barcode_cond_for_booking $all_po_cond and c.booking_without_order= $booking_without_order $job_no_cond $po_id_cond
		union all
		select a.transfer_system_id as recv_number, null as location_id, from_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,b.to_store, d.status_active, e.buyer_name as buyer_id, cast(b.to_rack as varchar2(100)) as rack,b.to_shelf as self , b.to_floor_id as floor_id, b.to_room as room
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and d.job_id=e.id and a.to_company=$company_id and a.entry_form in(83,183) and c.entry_form in(83,183) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.re_transfer=0 $barcode_cond $without_store_cond_2 $store_cond_2 $all_po_cond and c.booking_without_order= $booking_without_order $job_no_cond $po_id_cond
		union all
		select a.transfer_system_id as recv_number, null as location_id, to_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,b.to_store, d.status_active, e.buyer_name as buyer_id, cast(b.to_rack as varchar2(100)) as rack, b.to_shelf as self , b.to_floor_id as floor_id, b.to_room as room
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_po_break_down d, wo_po_details_master e
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and d.job_id=e.id and a.entry_form in(82) and c.entry_form in(82) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.re_transfer=0 and a.transfer_criteria in (1,2,4) and a.to_company = $company_id $barcode_cond $without_store_cond_2 $store_cond_2 $all_po_cond and c.booking_without_order= $booking_without_order $job_no_cond $po_id_cond
		union all
		select a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form, a.store_id as to_store, d.status_active, e.buyer_name as buyer_id,cast(b.rack as varchar2(100)) as rack,b.self, b.floor_id, b.room 
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(84) and c.entry_form in(84) 
		and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.re_transfer=0 $search_field_cond $barcode_cond $location_cond $without_store_cond_1 $store_cond_1 $barcode_cond_for_booking $all_po_cond and c.booking_without_order= $booking_without_order $job_no_cond $po_id_cond";
	}
	else
	{
		$sql = "SELECT a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.booking_no as po_number, null pub_shipment_date, null job_no_mst, null file_no, d.grouping, a.entry_form,a.store_id as to_store , d.status_active, d.buyer_id,cast(b.rack as varchar2(100)) as rack, b.self, b.floor_id, b.room
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_non_ord_samp_booking_mst d 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.re_transfer=0 and c.booking_without_order=$booking_without_order $barcode_cond $store_cond_1 $sample_booking_cond $sample_ref_cond $non_order_booking_cond
		union all 
		select a.transfer_system_id as recv_number, null as location_id, from_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs,  d.booking_no as po_number, null as pub_shipment_date,  null as job_no_mst,  null as file_no, d.grouping, a.entry_form,b.to_store , d.status_active, d.buyer_id, cast(b.to_rack as varchar2(100)) as rack,b.to_shelf as self, b.to_floor_id as floor_id, b.to_room as room 
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_non_ord_samp_booking_mst d 
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.to_company=$company_id and a.entry_form in(110,180)  and c.entry_form in(110,180) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.booking_without_order=$booking_without_order $barcode_cond $without_store_cond_2 $store_cond_2 $sample_booking_cond $sample_ref_cond $non_order_booking_cond
		union all 
		select a.transfer_system_id as recv_number, null as location_id, to_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs,  d.booking_no as po_number,  null as  pub_shipment_date, null as job_no_mst,  null as file_no, d.grouping, a.entry_form,b.to_store , d.status_active, d.buyer_id, cast(b.to_rack as varchar2(100)) as rack,b.to_shelf as self, b.to_floor_id as floor_id, b.to_room as room 
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, wo_non_ord_samp_booking_mst d 
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.entry_form in(82) and c.entry_form in(82) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.re_transfer=0 and a.transfer_criteria in (1,2) 
				and a.to_company =$company_id  and c.booking_without_order=$booking_without_order $barcode_cond $without_store_cond_2 $store_cond_2 $sample_booking_cond $sample_ref_cond $non_order_booking_cond
		union all 
		select a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs,  d.booking_no as po_number, null as pub_shipment_date,  null as job_no_mst,  null as file_no, d.grouping, a.entry_form, a.store_id as to_store , d.status_active, d.buyer_id,cast(b.rack as varchar2(100)) as rack,b.self, b.floor_id, b.room 
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_non_ord_samp_booking_mst d 
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(84) and c.entry_form in(84) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.re_transfer=0 and c.booking_without_order=$booking_without_order $barcode_cond $without_store_cond_1 $store_cond_1 $sample_booking_cond $sample_ref_cond $non_order_booking_cond";
	}

	// echo $sql;die;

	$result = sql_select($sql);

	foreach ($result as $row) 
	{
		$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		$all_prod_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
	}
	$barcode_arr = array_filter(array_unique($barcode_arr));

	if(count($barcode_arr)>0)
	{
		$all_barcode_nos = implode(",", $barcode_arr);
		$BarCond = $BarCond2 = $all_barcode_cond = $all_barcode_cond2 = "";

		if($db_type==2 && count($barcode_arr)>999)
		{
			$barcode_arr_chunk=array_chunk($barcode_arr,999) ;
			foreach($barcode_arr_chunk as $chunk_arr)
			{
				$BarCond.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";
				$BarCond2.=" b.barcode_no in(".implode(",",$chunk_arr).") or ";
			}

			$all_barcode_cond.=" and (".chop($BarCond,'or ').")";
			$all_barcode_cond2.=" and (".chop($BarCond,'or ').")";

		}
		else
		{
			$all_barcode_cond=" and a.barcode_no in($all_barcode_nos)";
			$all_barcode_cond2=" and b.barcode_no in($all_barcode_nos)";
		}



		$all_prod_ids = implode(",", $all_prod_arr);
		$prodCond = $all_prod_id_cond = "";

		if($db_type==2 && count($all_prod_arr)>999)
		{
			$all_prod_arr_chunk=array_chunk($all_prod_arr,999) ;
			foreach($all_prod_arr_chunk as $chunk_arr)
			{
				$prodCond.=" id in(".implode(",",$chunk_arr).") or ";
			}
			$all_prod_id_cond.=" and (".chop($prodCond,'or ').")";
		}
		else
		{
			$all_prod_id_cond=" and id in($all_prod_ids)";
		}
	}

	if(!empty($barcode_arr))
	{
		$scanned_barcode_arr=array();
		$barcodeData=sql_select( "select a.barcode_no from pro_roll_details a where a.entry_form in(61) and a.is_returned=0 and a.status_active=1 and a.is_deleted=0 $all_barcode_cond");
		foreach ($barcodeData as $row)
		{
			$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		}

		$stitch_lot_sql = sql_select("SELECT a.barcode_no, a.receive_basis, a.booking_no,  b.stitch_length, b.yarn_lot, b.yarn_count, b.width, b.body_part_id, b.color_id, b.color_range_id, b.gsm, b.machine_dia from pro_roll_details a,pro_grey_prod_entry_dtls b where a.dtls_id = b.id and a.entry_form = 2 and a.status_active=1 and b.status_active =1 and b.is_deleted =0 and a.is_deleted =0 $all_barcode_cond");
		foreach ($stitch_lot_sql as $row)
		{
			$production_ref_arr[$row[csf("barcode_no")]]['machine_dia'] = $row[csf("machine_dia")];
			$production_ref_arr[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];
			$production_ref_arr[$row[csf("barcode_no")]]['yarn_lot'] = $row[csf("yarn_lot")];
			$production_ref_arr[$row[csf("barcode_no")]]['yarn_count'] = $row[csf("yarn_count")];
			$production_ref_arr[$row[csf("barcode_no")]]['width'] = $row[csf("width")];
			$production_ref_arr[$row[csf("barcode_no")]]['gsm'] = $row[csf("gsm")];
			$production_ref_arr[$row[csf("barcode_no")]]['body_part_id'] = $row[csf("body_part_id")];
			$production_ref_arr[$row[csf("barcode_no")]]['receive_basis'] = $row[csf("receive_basis")];
			$production_ref_arr[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
			$production_ref_arr[$row[csf("barcode_no")]]['color_id'] = $color_name_arr[$row[csf("color_id")]];
			$production_ref_arr[$row[csf("barcode_no")]]['color_range_id'] = $color_range[$row[csf("color_range_id")]];
		}

		$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13 $all_prod_id_cond",'id','product_name_details');
	}

	$requisition_barcode_arr=array();
	if ($requisition_id!="") 
	{
		$sqlRecvT="SELECT d.id, d.entry_form, d.receive_basis, d.knitting_source, b.febric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, b.color_range_id, a.barcode_no 
		FROM inv_receive_master d, pro_grey_prod_entry_dtls b, pro_roll_details a WHERE d.id=b.mst_id and b.id=a.dtls_id and d.entry_form in(2,58) and a.entry_form in(2,58) and d.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $all_barcode_cond $gsm_cond $febric_description_id_cond order by d.entry_form desc";
		// echo $sqlRecvT;die; //  $dia_cond $color_id_cond $color_range_id_cond
		$recvDataT=sql_select($sqlRecvT);

		foreach($recvDataT as $row)
		{
			$requisition_barcode_arr[$row[csf('barcode_no')]] =$row[csf('barcode_no')];
		}
	}
	// echo "<pre>";print_r($requisition_barcode_arr);die;
	// echo $requisition_id.'=====';die;
	
	$floorRoomRackShelf_array=return_library_array( "SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");
	$sql_recv = sql_select("SELECT  b.barcode_no,c.self_old,c.rack1 from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_transaction c where a.id=b.dtls_id and a.trans_id=c.id $all_barcode_cond2 and b.entry_form=58  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

	foreach($sql_recv as $row)
	{
		$recv_rack_self_infoArr[$row[csf("barcode_no")]]['rack1']=$row[csf("rack1")];
		$recv_rack_self_infoArr[$row[csf("barcode_no")]]['self_old']=$row[csf("self_old")];
	}

	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1970" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="150">Fabric Description</th>
            <th width="100">Body Part</th>
            <th width="30">Dia</th>
            <th width="50">MC/Dia</th>
            <th width="30">Gsm</th>
            <th width="60">Color</th>
            <th width="60">Color Range</th>
            <th width="90">Stitch Ln.</th>
            <th width="50">Program No</th>
            <th width="100">Lot</th>
            <th width="100">Yarn Count</th>
            <th width="100">Buyer</th>
            <th width="75">Job No</th>
            <th width="110">Order No/Sample Booking</th>
            <th width="70">Barcode No</th>
            <th width="50">Roll No</th>
            <th width="50">Roll Qty.</th>
            <th width="50">Qty. In Pcs</th>
            <th width="70">File NO</th>
            <th width="70">Ref No</th>
            <th width="70">Shipment Date</th>
            <th width="110">Location</th>
            <th width="100">Store</th>
            <th width="50">Rack</th>
			<th width="50">Shelf</th>
			<th width="50">Rack Old</th>
            <th width="50">Shelf Old</th>
            <th width="50">Status</th>
        </thead>
	</table>
	<div style="width:1990px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1970" class="rpt_table" id="tbl_list_search">
        <?
            $i=1;
        if ($requisition_id!="") // for Requisition Basis
		{
            foreach ($result as $row)
            {
            	if($scanned_barcode_arr[$row[csf('barcode_no')]]=="" && $requisition_barcode_arr[$row[csf("barcode_no")]]!="")
            	{
					$trans_flag = "";
					if($row[csf('entry_form')] == 82 || $row[csf('entry_form')] == 83 || $row[csf('entry_form')] == 110 || $row[csf('entry_form')] == 180)
					{
						$trans_flag = " (T)";
					}

					$machine_dia = $production_ref_arr[$row[csf("barcode_no")]]['machine_dia'];
					$stitch_length = $production_ref_arr[$row[csf("barcode_no")]]['stitch_length'];
					$yarn_lot = $production_ref_arr[$row[csf("barcode_no")]]['yarn_lot'];
					$yarnCount = $production_ref_arr[$row[csf("barcode_no")]]['yarn_count'];
					$dia_width = $production_ref_arr[$row[csf("barcode_no")]]['width'];
					$gsm = $production_ref_arr[$row[csf("barcode_no")]]['gsm'];
					$body_part_id = $production_ref_arr[$row[csf("barcode_no")]]['body_part_id'];
					$receive_basis = $production_ref_arr[$row[csf("barcode_no")]]['receive_basis'];
					$colorName = $production_ref_arr[$row[csf("barcode_no")]]['color_id'];
					$colorRange = $production_ref_arr[$row[csf("barcode_no")]]['color_range_id'];
					if($receive_basis == 2)
					{
						$program_no = $production_ref_arr[$row[csf("barcode_no")]]['booking_no'];
					}else{
						$program_no = "";
					}

					$yarn_count_array=array_unique(explode(",",$yarnCount));
					$all_count="";
					foreach($yarn_count_array as $count_id)
					{
						$all_count.=$yarn_count_arr[$count_id].",";
					}
					$all_count=chop($all_count,",");

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="30" align="center"><? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
							<input type="hidden" name="txt_individual_qty" id="txt_individual_qty<?php echo $i; ?>" value="<?php echo $row[csf('qnty')]; ?>"/>
						</td>
						<td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						<td width="100"><p><? echo $body_part[$body_part_id]; ?></p></td>
						<td width="30"><p><? echo $dia_width; ?></p></td>
						<td width="50"><p><? echo $machine_dia; ?></p></td>
						<td width="30"><p><? echo $gsm; ?></p></td>
						<td width="60"><p><? echo $colorName; ?></p></td>
						<td width="60"><p><? echo $colorRange; ?></p></td>
						<td width="90"><p><? echo $stitch_length; ?></p></td>
						<td width="50"><p><? echo $program_no; ?></p></td>
						<td width="100"><p><? echo $yarn_lot; ?></p></td>
						<td width="100"><p><? echo $all_count; ?></p></td>
						<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
						<td width="75"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td width="70"><p><? echo $row[csf('barcode_no')].$trans_flag; ?>&nbsp;</p></td>
						<td width="50" align="center"><? echo $row[csf('roll_no')]; ?></td>
						<td width="50" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
						<td width="50" align="right"><? echo $row[csf('qc_pass_qnty_pcs')]*1; ?></td>
                        <td width="70" align="center"><? echo $row[csf('file_no')]; ?></td>
                        <td width="70" align="center"><? echo $row[csf('grouping')]; ?></td>
						<td width="70" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
						<td width="110" align="center"><? echo $location_arr[$row[csf('location_id')]]; ?></td>
						<td width="100" align="center"><? echo $store_arr[$row[csf('to_store')]]; ?></td>
						<td width="50" align="left"><? echo $floorRoomRackShelf_array[$row[csf('rack')]]; ?></td>
						<td width="50" align="left"><? echo $floorRoomRackShelf_array[$row[csf('self')]]; ?></td>
						<td width="50" align="left"><? echo $recv_rack_self_infoArr[$row[csf("barcode_no")]]['rack1']; ?></td>
						<td width="50" align="left"><? echo $recv_rack_self_infoArr[$row[csf("barcode_no")]]['self_old']; ?></td>
						<td align="center" width="50"><? echo $row_status[$row[csf('status_active')]]; ?></td>
					</tr>
					<?
					$i++;
					$total_grey_qnty +=$row[csf('qnty')];					
            	}	
			}
		}
		else
		{
			foreach ($result as $row)
            {
            	if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
            	{
					$trans_flag = "";
					if($row[csf('entry_form')] == 82 || $row[csf('entry_form')] == 83 || $row[csf('entry_form')] == 110 || $row[csf('entry_form')] == 180)
					{
						$trans_flag = " (T)";
					}

					$machine_dia = $production_ref_arr[$row[csf("barcode_no")]]['machine_dia'];
					$stitch_length = $production_ref_arr[$row[csf("barcode_no")]]['stitch_length'];
					$yarn_lot = $production_ref_arr[$row[csf("barcode_no")]]['yarn_lot'];
					$yarnCount = $production_ref_arr[$row[csf("barcode_no")]]['yarn_count'];
					$dia_width = $production_ref_arr[$row[csf("barcode_no")]]['width'];
					$gsm = $production_ref_arr[$row[csf("barcode_no")]]['gsm'];
					$body_part_id = $production_ref_arr[$row[csf("barcode_no")]]['body_part_id'];
					$receive_basis = $production_ref_arr[$row[csf("barcode_no")]]['receive_basis'];
					$colorName = $production_ref_arr[$row[csf("barcode_no")]]['color_id'];
					$colorRange = $production_ref_arr[$row[csf("barcode_no")]]['color_range_id'];
					if($receive_basis == 2)
					{
						$program_no = $production_ref_arr[$row[csf("barcode_no")]]['booking_no'];
					}else{
						$program_no = "";
					}

					$yarn_count_array=array_unique(explode(",",$yarnCount));
					$all_count="";
					foreach($yarn_count_array as $count_id)
					{
						$all_count.=$yarn_count_arr[$count_id].",";
					}
					$all_count=chop($all_count,",");

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="30" align="center"><? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
							<input type="hidden" name="txt_individual_qty" id="txt_individual_qty<?php echo $i; ?>" value="<?php echo $row[csf('qnty')]; ?>"/>
						</td>
						<td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						<td width="100"><p><? echo $body_part[$body_part_id]; ?></p></td>
						<td width="30"><p><? echo $dia_width; ?></p></td>
						<td width="50"><p><? echo $machine_dia; ?></p></td>
						<td width="30"><p><? echo $gsm; ?></p></td>
						<td width="60"><p><? echo $colorName; ?></p></td>
						<td width="60"><p><? echo $colorRange; ?></p></td>
						<td width="90"><p><? echo $stitch_length; ?></p></td>
						<td width="50"><p><? echo $program_no; ?></p></td>
						<td width="100"><p><? echo $yarn_lot; ?></p></td>
						<td width="100"><p><? echo $all_count; ?></p></td>
						<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
						<td width="75"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td width="70"><p><? echo $row[csf('barcode_no')].$trans_flag; ?>&nbsp;</p></td>
						<td width="50" align="center"><? echo $row[csf('roll_no')]; ?></td>
						<td width="50" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
						<td width="50" align="right"><? echo $row[csf('qc_pass_qnty_pcs')]*1; ?></td>
                        <td width="70" align="center"><? echo $row[csf('file_no')]; ?></td>
                        <td width="70" align="center"><? echo $row[csf('grouping')]; ?></td>
						<td width="70" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
						<td width="110" align="center"><? echo $location_arr[$row[csf('location_id')]]; ?></td>
						<td width="100" align="center"><? echo $store_arr[$row[csf('to_store')]]; ?></td>
						<td width="50" align="left"><? echo $floorRoomRackShelf_array[$row[csf('rack')]]; ?></td>
						<td width="50" align="left"><? echo $floorRoomRackShelf_array[$row[csf('self')]]; ?></td>
						<td width="50" align="left"><? echo $recv_rack_self_infoArr[$row[csf("barcode_no")]]['rack1']; ?></td>
						<td width="50" align="left"><? echo $recv_rack_self_infoArr[$row[csf("barcode_no")]]['self_old']; ?></td>
						<td align="center" width="50"><? echo $row_status[$row[csf('status_active')]]; ?></td>
					</tr>
					<?
					$i++;
					$total_grey_qnty +=$row[csf('qnty')];					
            	}	
			}
		}
        ?>
        </table>
    </div>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1970" class="rpt_table">
        <tr class="tbl_bottom">
            <td width="30"></td>
            <td width="150"></td>
            <td width="100"></td>
            <td width="30"></td>
            <td width="50"></td>
            <td width="30"></td>
            <td width="60"></td>
            <td width="60"></td>
            <td width="90"></td>
            <td width="50"></td>
            <td width="100"></td>
            <td width="100"></td>
            <td width="100"></td>
            <td width="75"></td>
            <td width="110"></td>
            <td width="70"></td>
            <td width="50">Total :</td>
            <td width="50" id="value_total_selected_value_td"><? echo number_format($total_grey_qnty,2); ?></td>
            <td width="50"></td>
            <td width="70"></td>
            <td width="70"></td>
            <td width="70"></td>
            <td width="110"></td>
            <td width="100"></td>
            <td width="50"></td>
            <td width="50"></td>
            <td width="50"></td>
            <td width="50"></td>
            <td width="50"></td>
        </tr>
        <tr class="tbl_bottom">
            <td width="30"></td>
            <td width="150"></td>
            <td width="100"></td>
            <td width="30"></td>
            <td width="50"></td>
            <td width="30"></td>
            <td width="60"></td>
            <td width="60"></td>
            <td width="90"></td>
            <td width="50"></td>
            <td width="100"></td>
            <td width="100"></td>
            <td width="100"></td>
            <td width="75"></td>
            <td width="110">Count of<br>Selected Row:</td>
            <td width="70"><input type="text"  style="width:50px" class="text_boxes_numeric" name="hidden_selected_row_count" id="hidden_selected_row_count" readonly value="0"></td>
            <td width="50">Select Total:</td>            
            <td width="50"><input type="text" name="hidden_selected_row_total" id="hidden_selected_row_total" class="text_boxes_numeric" style="width: 35px;" readonly disabled></td>
            <td width="50"></td>
            <td width="70"></td>
            <td width="70"></td>
            <td width="70"></td>
            <td width="110"></td>
            <td width="100"></td>
            <td width="50"></td>
            <td width="50"></td>
            <td width="50"></td>
            <td width="50"></td>
            <td width="50"></td>
        </tr>
	</table>
    <table width="1380">
        <tr>
        	<td align="left" colspan="2">
				<input type="checkbox" name="close" class="formbutton" onClick="check_all_data()"/> Check all
			</td>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
	<?
	exit();
}

if ($action=="to_order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$item_desc_ids="'$item_desc_ids'";
	$item_gsm="'$item_gsm'";
	$item_dia="'$item_dia'";
	$txt_requisition_basis="'$txt_requisition_basis'";$requisition_id="'$requisition_id'";
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
	<div align="center" style="width:1080px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:1070px;margin-left:10px">
			<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="975" class="rpt_table">
					<thead>
						<th>Buyer Name</th>
						<th>Job Year</th>
						<th>Job No</th>
						<th>Order No</th>
						<th>Internal Ref. No</th>
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
							<?
							echo create_drop_down( "cbo_job_year", 65, create_year_array(),"", 0,"-- --", date("Y",time()), "",0,"" );
							?>
						</td>
						<td>
							<input type="text" style="width:100px;" class="text_boxes" name="txt_job_no" id="txt_job_no" />
						</td>
						<td>
							<input type="text" style="width:150px;" class="text_boxes" name="txt_order_no" id="txt_order_no" />
						</td>
						<td>
							<input type="text" style="width:100px;" class="text_boxes" name="txt_internal_ref" id="txt_internal_ref" />
						</td>
						<td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" readonly>
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" readonly>
						</td>
						<td>
							<input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:80px" placeholder="Booking No">
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_to_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $item_desc_ids; ?>+'_'+<? echo $item_gsm; ?>+'_'+<? echo $item_dia; ?>+'_'+<? echo $txt_requisition_basis; ?>+'_'+<? echo $requisition_id; ?>+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_job_year').value, 'create_po_search_list_view', 'search_div', 'grey_fabric_transfer_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="6" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	$requisition_id=$data[11];
	$txt_internal_ref=$data[12];
	// $txt_job_no=$data[13];
	// echo $txt_job_no;die;
	$cbo_year=trim($data[14]);
	if($db_type==0)
	{
		$job_year_cond = " and year(a.insert_date) =$cbo_year";
	}
	else{
		$job_year_cond = " and to_char(a.insert_date,'YYYY') =$cbo_year";
	}

	if ($txt_requisition_basis==1) 
	{
		$composition_arr=array(); $constructtion_arr=array();
		$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0   and b.status_active=1 and b.is_deleted=0";
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
	/*if($fabric_construction!="") $fabric_construction_cond=" and c.construction in ('$fabric_construction')";
	if($fabric_composition!="") $fabric_composition_cond=" and c.copmposition like '%$fabric_composition'";
	if($item_gsm!="") $item_gsm_cond=" and c.gsm_weight in ($item_gsm)";*/
	if($fabric_construction!="") $fabric_construction_cond=" and d.construction in ('$fabric_construction')";
	if($fabric_composition!="") $fabric_composition_cond=" and d.composition like '%$fabric_composition'";
	if($item_gsm!="") $item_gsm_cond=" and d.gsm_weight in ($item_gsm)";
	if($item_dia!="") $item_dia_cond=" and c.dia_width in ('$item_dia')";
	
	

	// for requisition ==========================
	$job_no_cond=$gsm_cond=$dia_cond=$color_id_cond='';
	// echo $requisition_id;die;
	if ($requisition_id!="") 
	{
		$requisition_data=sql_select("SELECT b.barcode_no, b.from_prod_id, b.to_prod_id, b.from_order_id, b.to_order_id, b.from_booking_without_order 
		from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b 
		where a.id=b.mst_id and b.entry_form=339 and a.entry_form=339 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$requisition_id");
		foreach ($requisition_data as $row) 
		{
			$requisition_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
			$requisition_from_prod_arr[$row[csf('from_prod_id')]] = $row[csf('from_prod_id')];
			if ($row[csf('from_booking_without_order')]==0) 
			{
				$requisition_from_order_id_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
				$requisition_to_order_id_arr[$row[csf('to_order_id')]] = $row[csf('to_order_id')];
			}
		}
		$requisition_barcode_arr = array_filter(array_unique($requisition_barcode_arr));
		$requisition_from_order_id_arr = array_filter(array_unique($requisition_from_order_id_arr));
		$requisition_to_order_id_arr = array_filter(array_unique($requisition_to_order_id_arr));

		if(count($requisition_barcode_arr)>0)
		{
			$requisition_all_barcode_nos = implode(",", $requisition_barcode_arr);
			$BarCond = $requisition_all_barcode_cond = "";

			if($db_type==2 && count($requisition_barcode_arr)>999)
			{
				$barcode_arr_chunk=array_chunk($requisition_barcode_arr,999) ;
				foreach($barcode_arr_chunk as $chunk_arr)
				{
					$BarCond.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";
				}

				$requisition_all_barcode_cond.=" and (".chop($BarCond,'or ').")";
			}
			else
			{
				$requisition_all_barcode_cond=" and a.barcode_no in($requisition_all_barcode_nos)";
			}
		}

		$from_requisition_sql = sql_select("SELECT a.barcode_no, a.receive_basis, b.gsm, b.width as dia, b.color_id, b.color_range_id, b.febric_description_id from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id = b.id and a.entry_form = 2 and a.status_active=1 and b.status_active =1 and b.is_deleted =0 and a.is_deleted =0 $requisition_all_barcode_cond");
		$requisition_gsm_arr=$requisition_dia_arr=$requisition_color_id_arr=$requisition_color_range_id_arr=$requisition_feb_descr_arr=array();
		foreach ($from_requisition_sql as $row)
		{
			$requisition_gsm_arr[$row[csf("gsm")]] = $row[csf("gsm")];
			$requisition_dia_arr[$row[csf("dia")]] = $row[csf("dia")];
			$requisition_color_id_arr[$row[csf("color_id")]] = $row[csf("color_id")];
			//$requisition_color_range_id_arr[$row[csf("color_range_id")]] = $row[csf("color_range_id")];
			$requisition_feb_descr_arr[$row[csf("febric_description_id")]] = $row[csf("febric_description_id")];
		}

		$gsm_cond= "and d.gsm_weight in ("."'" . implode("','", $requisition_gsm_arr) . "'".")";
		$dia_cond= "and c.dia_width in ("."'" . implode("','", $requisition_dia_arr) . "'".")";
		$color_id_cond= "and c.fabric_color_id in ("."'" . implode("','", $requisition_color_id_arr) . "'".")";
		//$color_range_id_cond= "and b.color_range_id in ("."'" . implode("','", $requisition_color_range_id_arr) . "'".")";
		$febric_description_id_cond= "and b.febric_description_id in ("."'" . implode("','", $requisition_feb_descr_arr) . "'".")";


		$job_ref_data_array=sql_select("SELECT a.job_no, b.id FROM wo_po_details_master a, wo_po_break_down b 
		WHERE a.job_no=b.job_no_mst and a.status_active =1 and b.status_active=1 and b.id in (".implode(",", $requisition_to_order_id_arr).") group by a.job_no, b.id");
		$job_no_array=array(); $job_no_cond='';
		foreach($job_ref_data_array as $row)
		{
			$job_no_array[$row[csf("job_no")]]=$row[csf("job_no")];
			// $po_id_array[$row[csf("id")]]=$row[csf("id")];
		}
		$job_no_cond= "and a.job_no in ("."'" . implode("','", $job_no_array) . "'".")";
		// $po_id_cond= "and b.id not in ("."'" . implode("','", $po_id_array) . "'".")";
		$without_from_order_req_cond=" and b.id not in (".implode(",", $requisition_from_order_id_arr).")";
		$requisition_to_order_id_cond=" and b.id in (".implode(",", $requisition_to_order_id_arr).")";
	}
	// =======================================
	// echo $job_no_cond;die;

	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	$status_cond=" and b.status_active=1";

	$bookingNo= trim($data[5]);
	if($bookingNo!="") $booking_cond=" and c.booking_no like '%$bookingNo%'";
	$po_cond="";
	if($search_string!="")
	$po_cond=" and b.po_number ='$search_string'";

	$internal_ref= trim($data[12]);
	if($internal_ref!="") $internal_ref_cond=" and b.grouping like '%$internal_ref%'";
	$txt_job_no= trim($data[13]);
	if($txt_job_no!="") $txt_search_job_no_cond=" and a.job_no_prefix_num='$txt_job_no'";
	
	$sql= "SELECT a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date,c.booking_no,b.grouping 
	from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c,  wo_pre_cost_fabric_cost_dtls d  
	where a.job_no=b.job_no_mst  and b.id=c.po_break_down_id and c.pre_cost_fabric_cost_dtls_id=d.id and a.company_name=$company_id and a.buyer_name like '$buyer' $po_cond and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and d.status_active=1 and c.booking_type in (1,4) $status_cond $shipment_date $booking_cond $internal_ref_cond $job_year_cond $fabric_construction_cond $fabric_composition_cond $gsm_cond $without_from_order_req_cond $requisition_to_order_id_cond $txt_search_job_no_cond
	group by a.job_no,to_char(a.insert_date,'YYYY'), a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date,c.booking_no,b.grouping  order by b.id, b.pub_shipment_date";// $color_id_cond  $dia_cond  $job_no_cond  $without_from_order_req_cond 
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
                <th width="100">From Order Buyer/Job</th>
                <th width="80">Style Ref.</th>
                <th width="80">From Order No.</th>
                <th width="80">To Order Buyer/Job</th>
                <th width="80">Style Ref.</th>
                <th width="80">To Order No.</th>
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
                <th width="50">Bin/Box</th>
                <th width="50">Rack Old</th>
                <th>Shelf Old</th>
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
            	// echo $sql; die;
				$result=sql_select($sql);
				$all_roll_id=$all_po_id=$all_details_id="";$all_baracodes="";
				foreach($result as $row)
				{
					if($row[csf("roll_id")]!="") $all_roll_id.=$row[csf("roll_id")].",";
					if($row[csf("from_order_id")]!="") $all_po_id.=$row[csf("from_order_id")].",";
					if($row[csf("to_order_id")]!="") $all_po_id.=$row[csf("to_order_id")].",";
					if($row[csf("prod_detls_id")]!="") $all_details_id.=$row[csf("prod_detls_id")].",";
					$all_baracodes.=$row[csf("barcode_no")].",";
				}
				$all_roll_id=implode(",",array_unique(explode(",",chop($all_roll_id,","))));
				$all_po_id=implode(",",array_unique(explode(",",chop($all_po_id,","))));
				$all_details_id=implode(",",array_unique(explode(",",chop($all_details_id,","))));
				$all_baracodes=implode(",",array_unique(explode(",",chop($all_baracodes,","))));
				// echo $all_po_id;die;
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
				$sql_recv = sql_select("SELECT  b.barcode_no,c.self_old,c.rack1 from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_transaction c where a.id=b.dtls_id and a.trans_id=c.id and b.barcode_no in($all_baracodes) and b.entry_form=58  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

				foreach($sql_recv as $row)
				{
					$recv_rack_self_infoArr[$row[csf("barcode_no")]]['rack1']=$row[csf("rack1")];
					$recv_rack_self_infoArr[$row[csf("barcode_no")]]['self_old']=$row[csf("self_old")];
				}

				$i=0;$k=1;$j=1;$tot_color_qty=0;$tot_booking_qty=0;$roll_no=0;
				foreach($result as $row)
				{
					$i++;
					//$roll_no=count($row[csf("barcode_no")]);
					if($production_delivery_data[$row[csf("roll_id")]]["receive_basis"] == 2)
                	{
                		$program_no = $production_delivery_data[$row[csf("roll_id")]]["booking_no"];
                		$booking_number = $booking_from_program[$program_no];
                	}
                	else
                	{
                		$booking_number = $production_delivery_data[$row[csf("roll_id")]]["booking_no"];
                	}
                	$program_booking_no="P: ".$program_no."<br />B: ".$booking_number;

                	$all_color_arr=array_unique(explode(",",$production_delivery_data[$row[csf("roll_id")]]["color_id"]));
					$all_color="";
					foreach($all_color_arr as $color_id)
					{
						$all_color.=$color_library[$color_id].",";
					}
					$all_color=chop($all_color,",");

					
                    if (!in_array($all_color,$group_by_color_arr) )
                    {
                        if($j!=1)
                        {
                            ?>  
                            <tr>
			                	<td align="right" colspan="28"><strong>Color Wise Total</strong></td>
			                    <td align="right"><? echo $color_roll_no;?></td>
			                    <td align="right"><? echo number_format($tot_color_qty,2,'.',''); ?></td>
			                    <td>&nbsp;</td>
			                    <td>&nbsp;</td>
			                    <td>&nbsp;</td>
			                    <td>&nbsp;</td>
			                    <td>&nbsp;</td>
			                    <td>&nbsp;</td>
			                    <td>&nbsp;</td>
			                </tr>
                            <?
                            unset($color_roll_no);unset($tot_color_qty);
                        }
                        $group_by_color_arr[]=$all_color; 
                        $j++; 
                    }

                    if (!in_array($program_booking_no,$group_by_arr) )
                    {
                        if($k!=1)
                        {
                            ?>  
                            <tr>
			                	<td align="right" colspan="28"><strong>Booking Wise Total</strong></td>
			                    <td align="right"><? echo $booking_roll_no;?></td>
			                    <td align="right"><? echo number_format($tot_booking_qty,2,'.',''); ?></td>
			                    <td>&nbsp;</td>
			                    <td>&nbsp;</td>
			                    <td>&nbsp;</td>
			                    <td>&nbsp;</td>
			                    <td>&nbsp;</td>
			                    <td>&nbsp;</td>
			                    <td>&nbsp;</td>
			                </tr>
                            <?
                            unset($booking_roll_no);unset($tot_booking_qty);
                        }
                        $group_by_arr[]=$program_booking_no; 
                        $k++; 
                    }
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
						<td><? echo $job_array[$row[csf('from_order_id')]]['style_ref']; ?></td>
						<td title="From order id: <?echo $row[csf('from_order_id')];?>" style="word-break:break-all;"><? echo $job_array[$row[csf('from_order_id')]]['po']; ?></td>
						
						<td style="word-break:break-all;" align="center">
						<?
						if($row[csf('booking_without_order')]==1)
						{
							echo $buyer_library[$book_buyer_arr[$row[csf('po_breakdown_id')]]];
							//echo $row[csf('po_breakdown_id')];
						}
						else
						{
							echo $buyer_library[$job_array[$row[csf('to_order_id')]]['buyer']]. "<br>".$job_array[$row[csf('to_order_id')]]['job'];
						}
						?></td>
						<td><? echo $job_array[$row[csf('to_order_id')]]['style_ref']; ?></td>
						<td title="To order id: <?echo $row[csf('to_order_id')];?>" style="word-break:break-all;"><? echo $job_array[$row[csf('to_order_id')]]['po']; ?></td>

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
                        <td style="word-break:break-all;"><? echo $floorRoomRackShelf_array[$row[csf('to_shelf')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $floorRoomRackShelf_array[$row[csf('to_bin_box')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $recv_rack_self_infoArr[$row[csf("barcode_no")]]['rack1']; ?></td>
                        <td style="word-break:break-all;"><? echo $recv_rack_self_infoArr[$row[csf("barcode_no")]]['self_old']; ?></td>
                    </tr>
                	<?
					$tot_booking_qty+=$row[csf('qnty')];
					$tot_color_qty+=$row[csf('qnty')];
					$grand_tot_qty+=$row[csf('qnty')];
					$booking_roll_no+=count($row[csf("barcode_no")]);
					$color_roll_no+=count($row[csf("barcode_no")]);
				}
			?>
			
	        <tr class="tbl_bottom">
	            <td align="right" colspan="28"><strong>Color Wise Total</strong></td>
                <td align="right"><? echo $color_roll_no; ?></td>
                <td align="right"><? echo number_format($tot_color_qty,2,'.',''); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
	        </tr>
	        <tr class="tbl_bottom">
	            <td align="right" colspan="28"><strong>Booking Wise Total</strong></td>
                <td align="right"><? echo $booking_roll_no; ?></td>
                <td align="right"><? echo number_format($tot_booking_qty,2,'.',''); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
	        </tr>
            <tfoot>
            	<tr>
                	<th align="right" colspan="28"><strong>Grand Total</strong></th>
                    <th><? echo $i; ?></th>
                    <th align="right"><? echo number_format($grand_tot_qty,2,'.',''); ?></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
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

if($action=="grey_issue_print_4")
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
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
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
        <table cellspacing="0" width="1880"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th width="20">SL</th>
                <th width="100">From Store</th>
                <th width="100">To Store</th>
                <th width="50">File No</th>
                <th width="50">Reff No</th>
                <th width="100">From Order Buyer/Job</th>
                <th width="80">From Style Ref.</th>
                <th width="80">From Order No.</th>
                <th width="80">To Order Buyer/Job</th>
                <th width="80">To Style Ref.</th>
                <th width="80">To Order No.</th>
                <th width="80">Production Basis</th>
                <th width="100">Delivery Challan No</th>
                <th width="90">Prog./ Book. No</th>
                <th width="80">Fab Color</th>
                <th width="80">Color Range</th>
                <th width="80">Body Part</th>
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
                <th width="">Transfered Qnty</th>
				<th width="70">Qty In Pcs-Size</th>
				<th width="70">Floor</th>
				<th width="70">Room</th>
                <th width="70">Rack</th>
                <th width="70">Shelf</th>
                <th width="50">Bin/Box</th>
                <th width="50">Rack Old</th>
                <th>Shelf Old</th>
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
				$sql = "SELECT a.id as mst_id, b.id as dtls_id, b.from_store, b.to_store, b.from_order_id, b.to_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.knit_program_id, b.prod_detls_id, b.from_trans_entry_form, b.gsm, b.dia_width, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.to_body_part, c.qnty, c.roll_no, c.roll_id, c.barcode_no, c.booking_without_order, c.po_breakdown_id
				from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
				where a.id=b.mst_id and b.id=c.dtls_id and a.id=$update_id and c.entry_form=82 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  order by c.roll_no";
            	// echo $sql; die;
				$result=sql_select($sql);
				$all_roll_id=$all_po_id=$all_details_id="";$all_baracodes="";
				foreach($result as $row)
				{
					if($row[csf("roll_id")]!="") $all_roll_id.=$row[csf("roll_id")].",";
					if($row[csf("from_order_id")]!="") $all_po_id.=$row[csf("from_order_id")].",";
					if($row[csf("to_order_id")]!="") $all_po_id.=$row[csf("to_order_id")].",";
					if($row[csf("prod_detls_id")]!="") $all_details_id.=$row[csf("prod_detls_id")].",";
					$all_baracodes.=$row[csf("barcode_no")].",";
				}
				$all_roll_id=implode(",",array_unique(explode(",",chop($all_roll_id,","))));
				$all_po_id=implode(",",array_unique(explode(",",chop($all_po_id,","))));
				$all_details_id=implode(",",array_unique(explode(",",chop($all_details_id,","))));
				$all_baracodes=implode(",",array_unique(explode(",",chop($all_baracodes,","))));
				// echo $all_po_id;die;
				if($all_roll_id!="")
				{
					$production_sql=sql_select("SELECT b.receive_basis, b.booking_id, b.booking_no, c.id as prod_detls_id, c.color_id, c.color_range_id, c.machine_dia, c.machine_gg, c.machine_no_id, c.yarn_lot, c.body_part_id, d.id as roll_id, d.roll_no, d.barcode_no, d.qc_pass_qnty_pcs, d.coller_cuff_size
					from inv_receive_master b, pro_grey_prod_entry_dtls c, pro_roll_details d
					where b.id=c.mst_id and c.id=d.dtls_id and d.roll_id=0 and d.barcode_no in($all_baracodes) and d.entry_form in (2,22)");

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
						$production_delivery_data[$row[csf("barcode_no")]]["size_qty_pcs"]=$row[csf("qc_pass_qnty_pcs")].'-'.$row[csf("coller_cuff_size")];
						//$production_delivery_data[$row[csf("barcode_no")]]["body_part_id"]=$row[csf("body_part_id")];

						if($production_delivery_data[$row[csf("barcode_no")]]["receive_basis"] == 2)
						{
							$program_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
						}
					}
				}
				// echo "<pre>";
				// print_r($production_delivery_data); die;

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
				$sql_recv = sql_select("SELECT  b.barcode_no,c.self_old,c.rack1 from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_transaction c where a.id=b.dtls_id and a.trans_id=c.id and b.barcode_no in($all_baracodes) and b.entry_form=58  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

				foreach($sql_recv as $row)
				{
					$recv_rack_self_infoArr[$row[csf("barcode_no")]]['rack1']=$row[csf("rack1")];
					$recv_rack_self_infoArr[$row[csf("barcode_no")]]['self_old']=$row[csf("self_old")];
				}

				$data_array=array();
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
						$from_order_no='';
					}
					else
					{
						$job_buyer_no = $buyer_library[$job_array[$row[csf('from_order_id')]]['buyer']]. "<br>".$job_array[$row[csf('from_order_id')]]['job'];
						$from_order_no=$job_array[$row[csf('from_order_id')]]['po'];
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
                    $size_qty_pcs =  $production_delivery_data[$row[csf("barcode_no")]]["size_qty_pcs"];
                    $machine_no =  $lib_mc_arr[$production_delivery_data[$row[csf("barcode_no")]]["machine_no_id"]]['no'];
                    $all_color_range=$color_range[$production_delivery_data[$row[csf("barcode_no")]]["color_range_id"]];
                    $prod_basis=$prod_basis_arr[$production_delivery_data[$row[csf("barcode_no")]]["receive_basis"]];
                    //$body_part_id=$production_delivery_data[$row[csf("barcode_no")]]["body_part_id"];
                    $from_store_no =  $store_arr[$row[csf('from_store')]];
                    $to_store_no = $store_arr[$row[csf('to_store')]];


					$data_array[$all_color][$program_booking_no][$delivery_challan_no."**".$job_buyer_no."**".$row[csf('feb_description_id')]."**".$row[csf("gsm")]."**".$machine_dia_gg."**".$row[csf("dia_width")]."**".$row[csf("stitch_length")]."**".$all_count."**".$brand_no."**".$lot_no."**".$machine_no."**".$from_store_no."**".$to_store_no."**".$from_order_no."**".$prod_basis."**".$size_qty_pcs."**".$row[csf("booking_without_order")]."**".$row[csf("from_order_id")]."**".$all_color_range."**".$row[csf("to_body_part")]."**".$row[csf("to_floor_id")]."**".$row[csf("to_room")]."**".$row[csf("to_rack")]."**".$row[csf("to_shelf")]."**".$row[csf("to_bin_box")]]["qnty"] += $row[csf('qnty')];
					
					$data_array[$all_color][$program_booking_no][$delivery_challan_no."**".$job_buyer_no."**".$row[csf('feb_description_id')]."**".$row[csf("gsm")]."**".$machine_dia_gg."**".$row[csf("dia_width")]."**".$row[csf("stitch_length")]."**".$all_count."**".$brand_no."**".$lot_no."**".$machine_no."**".$from_store_no."**".$to_store_no."**".$from_order_no."**".$prod_basis."**".$size_qty_pcs."**".$row[csf("booking_without_order")]."**".$row[csf("from_order_id")]."**".$all_color_range."**".$row[csf("to_body_part")]."**".$row[csf("to_floor_id")]."**".$row[csf("to_room")]."**".$row[csf("to_rack")]."**".$row[csf("to_shelf")]."**".$row[csf("to_bin_box")]]["toOrderId"] = $row[csf('po_breakdown_id')];

					$data_array[$all_color][$program_booking_no][$delivery_challan_no."**".$job_buyer_no."**".$row[csf('feb_description_id')]."**".$row[csf("gsm")]."**".$machine_dia_gg."**".$row[csf("dia_width")]."**".$row[csf("stitch_length")]."**".$all_count."**".$brand_no."**".$lot_no."**".$machine_no."**".$from_store_no."**".$to_store_no."**".$from_order_no."**".$prod_basis."**".$size_qty_pcs."**".$row[csf("booking_without_order")]."**".$row[csf("from_order_id")]."**".$all_color_range."**".$row[csf("to_body_part")]."**".$row[csf("to_floor_id")]."**".$row[csf("to_room")]."**".$row[csf("to_rack")]."**".$row[csf("to_shelf")]."**".$row[csf("to_bin_box")]]["roll_count"]++;
				}

				// echo "<pre>";print_r($data_array);die;

				$i=1;$grand_tot_qty=$grand_roll_count=0;
				foreach ($data_array as $color_no => $color_data)
				{
					$booking_roll_no =0;$tot_booking_qty=0;
					foreach ($color_data as $prog_book_no => $prog_book_arr)
					{
						$color_roll_no=0;$tot_color_qty=0;
						foreach ($prog_book_arr as $data_key_string => $row)
						{
							$key_arr = explode("**", $data_key_string);
							// echo "<pre>"; print_r($key_arr);
							$chalan=$key_arr[0];
							$from_job_buyer=$key_arr[1];
							$feb_descri=$key_arr[2];
							$gsm=$key_arr[3];
							$machine_dia_gg=$key_arr[4];
							$dia_width=$key_arr[5];
							$stitch_length=$key_arr[6];
							$all_count=$key_arr[7];
							$brand_no=$key_arr[8];
							$lot_no=$key_arr[9];
							$machine_no=$key_arr[10];
							$from_store_no=$key_arr[11];
							$to_store_no=$key_arr[12];
							$from_order_no=$key_arr[13];
							$prod_basis=$key_arr[14];
							$size_qty_pcs=$key_arr[15];
							$booking_without_order=$key_arr[16];
							$from_order_id=$key_arr[17];
							$allColor_range=$key_arr[18];
							$body_part_id=$key_arr[19];
							$to_floor_id = $key_arr[20];
							$to_room = $key_arr[21];
							$to_rack = $key_arr[22];
							$to_shelf = $key_arr[23];
							$to_bin_box = $key_arr[24];
							?>
		                    <tr>
		                        <td align="center"><? echo $i; ?></td>
		                        <td style="word-break:break-all;"><? echo $from_store_no; ?></td>
		                        <td style="word-break:break-all;"><? echo $to_store_no; ?></td>
		                        <td style="word-break:break-all;" align="center">
								<?
								if($booking_without_order==1) echo "&nbsp;"; else echo $job_array[$from_order_id]['file_no'] ;
								?></td>

		                        <td style="word-break:break-all;" align="center"><? if($booking_without_order==1) echo "&nbsp;"; else echo $job_array[$from_order_id]['grouping']; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $from_job_buyer; ?></td>
								<td><? echo $job_array[$from_order_id]['style_ref']; ?></td>
								<td title="From order id: <?echo $from_order_id;?>" style="word-break:break-all;"><? echo $job_array[$from_order_id]['po']; ?></td>
								
								<td style="word-break:break-all;" align="center">
								<?
								if($booking_without_order==1)
								{
									echo $buyer_library[$book_buyer_arr[$row[csf('po_breakdown_id')]]];
									//echo $row[csf('po_breakdown_id')];
								}
								else
								{
									echo $buyer_library[$job_array[$row['toOrderId']]['buyer']]. "<br>".$job_array[$row['toOrderId']]['job'];
								}
								?></td>
								<td><? echo $job_array[$row['toOrderId']]['style_ref']; ?></td>
								<td title="To order id: <?echo $row['toOrderId'];?>" style="word-break:break-all;"><? echo $job_array[$row['toOrderId']]['po']; ?></td>

		                        <td style="word-break:break-all;"><? echo $prod_basis; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $chalan;;?></td>
		                        <td style="word-break:break-all;" align="left"><? echo $prog_book_no; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $color_no; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $allColor_range; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $body_part[$body_part_id]; ?></td>
		                        <td style="word-break:break-all;"><? echo $constructtion_arr[$feb_descri]; ?></td>
		                        <td style="word-break:break-all;"><? echo $composition_arr[$feb_descri]; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $gsm; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $dia_width; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $all_count; ?></td>
		                        <td style="word-break:break-all;"><?  echo $brand_no; ?></td>
		                        <td style="word-break:break-all;"><? echo $lot_no; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $stitch_length; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $machine_no; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $machine_dia_gg; ?></td>
		                        <td align="center">Kg</td>
		                        <td align="center"><? echo $row["roll_count"];?></td>
		                        <td align="right"><? echo number_format($row['qnty'],2,'.','');?></td>
		                        <td align="right"><? echo $size_qty_pcs;?></td>
		                        <td align="right"><? echo $floorRoomRackShelf_array[$to_floor_id];?></td>
		                        <td align="right"><? echo $floorRoomRackShelf_array[$to_room];?></td>
		                        <td align="right"><? echo $floorRoomRackShelf_array[$to_rack];?></td>
		                        <td align="right"><? echo $floorRoomRackShelf_array[$to_shelf];?></td>
		                        <td align="right"><? echo $floorRoomRackShelf_array[$to_bin_box];?></td>
								<td style="word-break:break-all;"><? echo $recv_rack_self_infoArr[$row[csf("barcode_no")]]['rack1']; ?></td>
                        		<td style="word-break:break-all;"><? echo $recv_rack_self_infoArr[$row[csf("barcode_no")]]['self_old']; ?></td>
		                    </tr>
		                	<?	
		                	$i++;						
							$color_roll_no+=$row["roll_count"];
							$tot_color_qty+=$row['qnty'];
							
							$booking_roll_no+=$row["roll_count"];
							$tot_booking_qty+=$row['qnty'];

							$grand_roll_count+=$row['roll_count'];
							$grand_tot_qty+=$row['qnty'];
						}
						?>
						<tr bgcolor="#eeeded" style="font-weight: bold;">
	                        <td colspan="28" align="right">Booking Color wise Total</td>
	                        <td align="right"><? echo $color_roll_no;?></td>
	                        <td align="right"><? echo number_format($tot_color_qty,2,'.','');?></td>
	                        <td align="right"></td>
	                        <td align="right"></td>
	                        <td align="right"></td>
	                        <td align="right"></td>
	                        <td align="right"></td>
	                        <td align="right"></td>
	                        <td align="right"></td>
	                        <td align="right"></td>
	                	</tr>
						<?
					}
					?>
					<tr bgcolor="#eeeded" style="font-weight: bold;">
                        <td colspan="28" align="right">Booking wise Total</td>
                        <td align="right"><? echo $booking_roll_no;?></td>
                        <td align="right"><? echo number_format($tot_booking_qty,2,'.','');?></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
                	</tr>
					<?
				}
			?>
            <tfoot>
            	<tr bgcolor="#eeeded">
                	<th align="right" colspan="28"><strong>Grand Total</strong></th>
                    <th align="right"><? echo number_format($grand_roll_count,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($grand_tot_qty,2,'.',''); ?></th>
					<td align="right"></td>
					<td align="right"></td>
					<td align="right"></td>
					<td align="right"></td>
					<td align="right"></td>
					<td align="right"></td>
					<td align="right"></td>
					<td align="right"></td>
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
                <td colspan="6" align="center" style="font-size:16px; font-weight:bold">Grey Fabric Transfer Entry Challan</td>
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
                <th width="90">Order No</th>
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
				$sql = "SELECT a.id as mst_id, b.id as dtls_id, b.from_store, b.to_store, b.from_order_id, b.to_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.knit_program_id, b.prod_detls_id, b.from_trans_entry_form, b.gsm, b.dia_width, c.qnty, c.roll_no, c.roll_id, c.barcode_no, c.booking_without_order, c.po_breakdown_id
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
						$from_order_no='';
					}
					else
					{
						$job_buyer_no = $buyer_library[$job_array[$row[csf('from_order_id')]]['buyer']]. "<br>".$job_array[$row[csf('from_order_id')]]['job'];
						$from_order_no=$job_array[$row[csf('from_order_id')]]['po'];
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


					$data_array[$all_color][$program_booking_no][$delivery_challan_no."**".$job_buyer_no."**".$program_booking_no."**".$row[csf('feb_description_id')]."**".$row[csf("gsm")]."**".$machine_dia_gg."**".$row[csf("dia_width")]."**".$row[csf("stitch_length")]."**".$all_count."**".$brand_no."**".$lot_no."**".$machine_no."**".$from_store_no."**".$to_store_no."**".$from_order_no]["qnty"] += $row[csf('qnty')];

					$data_array[$all_color][$program_booking_no][$delivery_challan_no."**".$job_buyer_no."**".$program_booking_no."**".$row[csf('feb_description_id')]."**".$row[csf("gsm")]."**".$machine_dia_gg."**".$row[csf("dia_width")]."**".$row[csf("stitch_length")]."**".$all_count."**".$brand_no."**".$lot_no."**".$machine_no."**".$from_store_no."**".$to_store_no."**".$from_order_no]["roll_count"]++;
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
								<td style="word-break:break-all;" align="left"><? echo $key_arr[14];?></td>
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
	                        <td colspan="16" align="right">Booking Color wise Total</td>
	                        <td align="right"><? echo $sub_color_tot_roll;?></td>
	                        <td align="right"><? echo number_format($sub_color_tot_qnty,2,'.','');?></td>
	                        <td colspan="2">&nbsp;</td>
	                	</tr>
						<?
						$sub_color_tot_roll =0;$sub_color_tot_qnty=0;
					}
					?>
					<tr bgcolor="#eeeded" style="font-weight: bold;">
                        <td colspan="16" align="right">Booking wise Total</td>
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
                	<th align="right" colspan="16"><strong>Total</strong></th>
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
                <th width="70">Qty In Pcs-Size</th>
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
					$sql = "SELECT a.id as mst_id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, sum(c.qnty) as transfer_qnty, count(c.roll_id) as tot_roll, LISTAGG(CAST(c.roll_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY c.roll_id) as roll_id, c.booking_without_order, c.po_breakdown_id
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

					$production_sql=sql_select("SELECT b.receive_basis, b.booking_id, b.booking_no, c.id as prod_detls_id, c.color_id, c.color_range_id, c.machine_dia, c.machine_gg, c.machine_no_id, c.yarn_lot, d.id as roll_id, d.roll_no, d.barcode_no, d.qc_pass_qnty_pcs, d.coller_cuff_size
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
						$production_delivery_data[$row[csf("roll_id")]]["size_qty_pcs"]=$row[csf("qc_pass_qnty_pcs")].'-'.$row[csf("coller_cuff_size")];

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
						$all_recv_basis=$rcv_basis_id=$machine_dia=$machine_gg=$machine_no=$program_nos=$size_qty_pcs="";$all_booking="";
						foreach($roll_id_arr as $rol_id)
						{
							$all_recv_basis.=$prod_basis_arr[$production_delivery_data[$rol_id]["receive_basis"]].",";
							$rcv_basis_id.=$production_delivery_data[$rol_id]["receive_basis"].",";
							$machine_dia.=$production_delivery_data[$rol_id]["machine_dia"].",";
							$machine_gg.=$production_delivery_data[$rol_id]["machine_gg"].",";
							$machine_no.=$lib_mc_arr[$production_delivery_data[$rol_id]["machine_no_id"]]['no'].",";
							$yarn_lot.=$production_delivery_data[$rol_id]["yarn_lot"].",";
							$size_qty_pcs.=$production_delivery_data[$rol_id]["size_qty_pcs"].",";

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
						$size_qty_pcs_arr=array_unique(explode(",",chop($size_qty_pcs,",")));
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
                        <td align="center"><? 
                        foreach ($size_qty_pcs_arr as $key => $value) 
                        {
                        	echo $value.'<br>';
                        }
                         ?></td>
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
                    <th align="center"></th>
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
if($action=="bodypart_list")
{
	$bodyPart_arr=array();
	if($data)
	{
		$body_part_sql = sql_select("SELECT x.body_part_id from ( SELECT a.body_part_id from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted = 0 and b.pre_cost_fabric_cost_dtls_id=a.id and b.po_break_down_id =$data and b.booking_type =1 union all select b.body_part_id from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c where b.job_no=c.job_no and a.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.pre_cost_fabric_cost_dtls_id=a.id and c.po_break_down_id =$data and a.fabric_description = b.id and c.booking_type=4 ) x group by x.body_part_id");


		foreach($body_part_sql as $row)
		{
			$bodyPart_arr[$row[csf('body_part_id')]]=$body_part[$row[csf('body_part_id')]];
		}
	}
	$jsBodyPart_arr= json_encode($bodyPart_arr);
	echo $jsBodyPart_arr;
	die();
}

if($action=="toColor_list")
{
	$ToColor_arr=array();
	if($data)
	{
		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$color_sql=sql_select("SELECT color_number_id from WO_PO_COLOR_SIZE_BREAKDOWN where PO_BREAK_DOWN_ID=$data and STATUS_ACTIVE=1 and is_deleted=0 group by color_number_id");

		foreach($color_sql as $row)
		{
			$ToColor_arr[$row[csf('color_number_id')]]=$color_arr[$row[csf('color_number_id')]];
		}
	}
	$jsToColor_arr= json_encode($ToColor_arr);
	echo $jsToColor_arr;
	die();
}

if($action=="bodypart_list_order_wise")
{
	$bodyPart_arr=array();

	$data = chop($data,",");

	if($data)
	{
		$body_part_sql = sql_select("SELECT x.po_break_down_id, x.body_part_id from ( SELECT b.po_break_down_id, a.body_part_id from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted = 0 and b.pre_cost_fabric_cost_dtls_id=a.id and b.po_break_down_id in ($data) and b.booking_type =1 union all select c.po_break_down_id, b.body_part_id from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c where b.job_no=c.job_no and a.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.pre_cost_fabric_cost_dtls_id=a.id and c.po_break_down_id in ($data) and a.fabric_description = b.id and c.booking_type=4 ) x group by x.po_break_down_id, x.body_part_id");


		foreach($body_part_sql as $row)
		{
			$bodyPart_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]]=$body_part[$row[csf('body_part_id')]];
		}
	}
	$jsBodyPart_arr= json_encode($bodyPart_arr);
	echo $jsBodyPart_arr;
	die();
}

if($action=="toColor_list_order_wise")
{
	$toColor_arr=array();

	$data = chop($data,",");

	if($data)
	{
		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		
		$color_sql=sql_select("SELECT color_number_id, po_break_down_id 
			from WO_PO_COLOR_SIZE_BREAKDOWN where PO_BREAK_DOWN_ID in($data) and STATUS_ACTIVE=1 and is_deleted=0 group by color_number_id, po_break_down_id");

		foreach($color_sql as $row)
		{
			$toColor_arr[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]=$color_arr[$row[csf('color_number_id')]];
		}
	}
	$jsToColor_arr= json_encode($toColor_arr);
	echo $jsToColor_arr;
	die();
}

if($action=="bodypart_list_sample_wise")
{
	$bodyPart_arr=array();

	$data = chop($data,",");

	if($data)
	{
		$body_part_sql = sql_select("SELECT b.body_part as body_part_id, a.id as po_break_down_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and  a.id in ($data)");

		foreach($body_part_sql as $row)
		{
			$bodyPart_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]]=$body_part[$row[csf('body_part_id')]];
		}
	}
	$jsBodyPart_arr= json_encode($bodyPart_arr);
	echo $jsBodyPart_arr;
	die();
}
?>

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

if($action=="load_drop_to_com")
{
	$data_ref = explode("_", $data);
	
	if($data_ref[0]==1)
	{
		echo create_drop_down( "cbo_to_company_id", 160,  "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "to_company_on_change(this.value)",0 );
	}
	else
	{
		echo create_drop_down( "cbo_to_company_id", 160,  "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "to_company_on_change(this.value)",1 );
	}

}

if($action=="load_drop_store_from")
{
	$data_ref= explode("_", $data);
	
	echo create_drop_down( "cbo_store_name_from", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=2 and a.company_id=$data_ref[1] and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "fnc_details_row_blank();store_on_change(this.value)" );
}

// if($action=="load_drop_store_from_pro")
// {
// 	$data_ref= explode("_", $data);
// 	var_dump($data_ref);
// 	echo create_drop_down( "cbo_store_name_from", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=2 and a.company_id=$data_ref[1] and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "fnc_details_row_blank();store_on_change(this.value)" );
// }

if($action=="load_drop_store_to")
{
	$data_ref= explode("_", $data);
	echo create_drop_down( "cbo_store_name", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=2 and a.company_id=$data_ref[1] and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "fn_load_floor(this.value);store_on_change(this.value);" );
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
	echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=2  and a.status_active=1 and a.is_deleted=0 and  a.location_id=$data group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
}

if ($action=="requ_variable_settings")
{
	extract($_REQUEST);
	
	$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$requisition_type=return_field_value("user_given_code_status","variable_settings_inventory","company_name='$cbo_company_id' and variable_list=30 and item_category_id='$item_category' and status_active=1 and is_deleted=0");
	echo $requisition_type.'**'.$variable_inventory;
	exit();
}

if($action=="populate_requisition_data_from_data")
{
	//$sql = "select id, company_id, issue_number, knit_dye_source, knit_dye_company, issue_date, batch_no, issue_purpose from inv_issue_master where id=$data and entry_form=61";
	$sql = "SELECT a.id, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, max(b.to_store) as to_store, a.remarks, sum(b.transfer_qnty) as transfer_qnty
	from  inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
	where a.id=b.mst_id and a.entry_form=506 and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.id, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, a.remarks ";
	// echo $sql;
	$previous_transfer_qnty = return_field_value("sum(b.transfer_qnty) as transfer_qnty", "inv_item_transfer_mst a, inv_item_transfer_dtls b", "a.id=b.mst_id and a.entry_form=134 and a.transfer_requ_id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ", "transfer_qnty");
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
		echo "load_drop_down( 'requires/roll_wise_finish_fabric_transfer_entry_controller','".$row[csf("transfer_criteria")].'_'.$to_company_id."', 'load_drop_store_to', 'store_td' );\n";

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
	if($db_type==0)
	{
		$barcode_nos=return_field_value("group_concat(barcode_no order by id desc) as barcode_nos","inv_item_transfer_requ_dtls","entry_form=506 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
	}
	else if($db_type==2)
	{
		$barcode_nos=return_field_value("LISTAGG(barcode_no, ',') WITHIN GROUP (ORDER BY id desc) as barcode_nos","inv_item_transfer_requ_dtls","entry_form=506 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
	}
	echo $barcode_nos;
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	// echo "20**$cbo_complete_status".'====='; die;

	$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and item_category_id=2 and status_active=1 and variable_list= 27", "auto_transfer_rcv");
	if($variable_auto_rcv == "")
	{
		$variable_auto_rcv = 1; // if auto receive 1 No, then no need to acknowledgement
	}
	 //echo "20**$variable_auto_rcv".'====='; die;
	
    for($k=1;$k<=$tot_row;$k++)
    {
        $productId="productId_".$k;
        $barcodeNO="barcodeNo_".$k;
        $prod_ids.=$$productId.",";
        $barcodeNOS.=$$barcodeNO.",";
    }
	$barcodeNOS=chop($barcodeNOS,',');
	$barcodeNOS_array =  array_unique(explode(",", $barcodeNOS));

	$prod_ids=chop($prod_ids,',');
    //$prod_ids=implode(",",array_unique(explode(",",chop($prod_ids,','))));
	$prod_ids_array =  array_unique(explode(",", $prod_ids));
	

	$barcodeNOS_cond=""; $barcodeNOSCond="";
	if($db_type==2 && count($barcodeNOS_array)>999)
	{
		$barcodeNOS_array_chunk=array_chunk($barcodeNOS_array,999) ;
		foreach($barcodeNOS_array_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$barcodeNOSCond.=" barcode_no in($chunk_arr_value) or ";
		}
		$barcodeNOS_cond.=" and (".chop($barcodeNOSCond,'or ').")";
	}
	else
	{
		$barcodeNOS_cond=" and barcode_no in($barcodeNOS)";
	}

	$prod_ids_cond=""; $prod_idsCond="";
	if($db_type==2 && count($prod_ids_array)>999)
	{
		$prod_ids_array_chunk=array_chunk($prod_ids_array,999) ;
		foreach($prod_ids_array_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$prod_idsCond.=" prod_id in($chunk_arr_value) or ";
		}
		$prod_ids_cond.=" and (".chop($prod_idsCond,'or ').")";
	}
	else
	{
		$prod_ids_cond=" and prod_id in($prod_ids)";
	}
	
	
    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "transaction_type in (1,4,5) $prod_ids_cond", "max_date");
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

	
	$store_arr_chk =sql_select("select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=2 and a.status_active=1 and a.company_id=$cbo_to_company_id and a.id=$cbo_store_name");
	if(empty($store_arr_chk))
	{
		echo "20**To store not found in to company";
        die;
	}

	if(str_replace("'","",$update_id)!="")
	{
		$is_acknowledge = return_field_value("b.id id", "inv_item_transfer_mst a,inv_item_trans_acknowledgement b", "a.id=b.challan_id and  a.id=$update_id and a.status_active=1 and a.is_acknowledge=1 and b.entry_form=134", "id");
		//505
		if($is_acknowledge != "" )
		{
			echo "20**Update not allowed. This Transfer Challan is already Acknowledged.\nAcknowledge System ID = $is_acknowledge";
			die;
		}
	}
	//echo "select barcode_no,entry_form,po_breakdown_id,qnty from pro_roll_details where entry_form in ( 37,68,126,134,214 ) $barcodeNOS_cond and re_transfer =0 and status_active = 1 and is_deleted = 0";die;
	 
	
	$trans_check_sql = sql_select("select barcode_no,entry_form,po_breakdown_id,qnty from pro_roll_details where entry_form in ( 37,68,126,134,214 ) $barcodeNOS_cond and re_transfer =0 and status_active = 1 and is_deleted = 0");
	// union all select a.barcode_no,a.entry_form,a.po_breakdown_id, a.qnty from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.entry_form in (7) and b.trans_id<>0 and a.re_transfer =0 and a.barcode_no in($barcodeNOS) and a.status_active = 1 and a.is_deleted = 0

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
			$batch_no="batchNo_".$x;
			$all_batch_no.="'".$$batch_no."',";
			
			
		}
		$all_barcodeNo=chop($all_barcodeNo,',');
		
		$all_batch_no=chop($all_batch_no,',');

		$all_batch_no_array =  array_unique(explode(",", $all_batch_no));
		$all_barcodeNo_array =  array_unique(explode(",", $all_barcodeNo));

		$all_batch_no_cond=""; $batchCond="";
		if($db_type==2 && count($all_batch_no_array)>999)
		{
			$all_batch_no_array_chunk=array_chunk($all_batch_no_array,999) ;
			foreach($all_batch_no_array_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$batchCond.=" a.batch_no in($chunk_arr_value) or ";
			}
			$all_batch_no_cond.=" and (".chop($batchCond,'or ').")";
		}
		else
		{
			$all_batch_no_cond=" and a.batch_no in($all_batch_no)";
		}

		$all_barcodeNo_cond=""; $barcodeNoCond="";
		if($db_type==2 && count($all_barcodeNo_array)>999)
		{
			$all_barcodeNo_array_chunk=array_chunk($all_barcodeNo_array,999) ;
			foreach($all_barcodeNo_array_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$barcodeNoCond.=" a.barcode_no in($chunk_arr_value) or ";
			}
			$all_barcodeNo_cond.=" and (".chop($barcodeNoCond,'or ').")";
		}
		else
		{
			$all_barcodeNo_cond=" and a.barcode_no in($all_barcodeNo)";
		}
		
		// echo "SELECT a.id,a.batch_no,a.color_id, a.batch_weight, a.booking_no from pro_batch_create_mst a where   a.status_active=1 and a.is_deleted=0 and a.entry_form in(14,68,134) and a.company_id=$cbo_to_company_id $all_batch_no_cond  group by a.id,a.batch_no,a.color_id,a.batch_weight, a.booking_no";die;
		
		$batchData=sql_select("SELECT a.id,a.batch_no,a.color_id, a.batch_weight, a.booking_no from pro_batch_create_mst a where   a.status_active=1 and a.is_deleted=0 and a.entry_form in(14,68,134) and a.company_id=$cbo_to_company_id $all_batch_no_cond  group by a.id,a.batch_no,a.color_id,a.batch_weight, a.booking_no");

		$batch_data_arr=array();
		foreach ($batchData as $rows)
		{
			$batch_data_arr[$rows[csf("batch_no")]][$rows[csf("color_id")]][$rows[csf("booking_no")]]['id']=$rows[csf("id")];
			$batch_data_arr[$rows[csf("batch_no")]][$rows[csf("color_id")]][$rows[csf("booking_no")]]['batch_weight']=$rows[csf("batch_weight")];
		}

		if($all_barcodeNo!="")
		{
			
			$issue_data_refer = sql_select("select a.id, a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 71 $all_barcodeNo_cond and a.status_active = 1 and a.is_deleted = 0 and a.is_returned=0");
			if($issue_data_refer[0][csf("barcode_no")] != "")
			{
				echo "20**Sorry Barcode No : ". $issue_data_refer[0][csf("barcode_no")] ."\nFound in Issue No ".$issue_data_refer[0][csf("issue_number")];
				disconnect($con);
				die;
			}
		}

		//echo "10**Fail";die;

		if($db_type==0) $year_cond="YEAR(insert_date)";
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later

		$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
		$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,'FFRTE',134,date("Y",time()),2 )); //505

		$field_array="id, entry_form, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company, from_order_id, to_order_id, item_category, remarks, delivery_company_name, inserted_by, insert_date";

		$data_array="(".$id.",134,'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$cbo_transfer_criteria.",".$cbo_to_company_id.",0,0,".$cbo_item_category.",".$txt_remarks.",".$txt_delv_company_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//505
		$field_array_prod_insert = "id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, gsm, dia_width, inserted_by, insert_date";

		$field_array_prod_update = "current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";

		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$field_array_trans = "id, mst_id, receive_basis, batch_id, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type,entry_form, transaction_date, cons_quantity, store_id, floor_id, room, rack, self, bin_box,body_part_id, inserted_by, insert_date";
		//$dtls_id=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		//, color_id
		//,from_store,floor_id,room,rack,shelf, to_store,to_floor_id,to_room,to_rack,to_shelf
		
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, from_order_id, to_order_id, feb_description_id, machine_no_id, batch_id, from_store, floor_id, room, rack, shelf, bin_box, to_store,to_floor_id,to_room,to_rack,to_shelf, to_bin_box, item_category, transfer_qnty, knit_program_id, prod_detls_id, from_trans_entry_form, from_booking_without_order, gsm, dia_width, transfer_requ_dtls_id,to_batch_id, body_part_id, to_body_part,  to_ord_book_id, to_ord_book_no, color_id, inserted_by, insert_date";

		$field_array_roll="id, barcode_no, mst_id, dtls_id, company_id, is_transfer, po_breakdown_id, entry_form, qnty, roll_no, roll_id, booking_without_order, from_roll_id, re_transfer, inserted_by, insert_date";
		$field_array_roll_update="is_transfer*updated_by*update_date";

		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$field_array_prop="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, color_id, inserted_by, insert_date";

		$field_array_batch_dtls="id, mst_id, po_id, prod_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id,width_dia_type, inserted_by, insert_date";


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
				$batch_id="batchId_".$j;
				$batch_no="batchNo_".$j;
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
				$fromBodyPart="fromBodyPart_".$j;

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
				$cboToBodyPart="cboToBodyPart_".$j;
				$tobookingNo="tobookingNo_".$j;
				$toBookingMstId="toBookingMstId_".$j;
				$constructCompo="constructCompo_".$j;
				$rollAmount="rollAmount_".$j;
				$fromBookingWithoutOrder="fromBookingWithoutOrder_".$j;
				$requiDtlsId="requiDtlsId_".$j;
				
				//if($$bookWithoutOrder==1) $toOrderId="orderId_".$j; else $toOrderId="toOrderId_".$j;	

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
			
				//$new_prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$dtls_id = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);

				if (str_replace("'", "", $$diaWidth)=="")
				{
					if($db_type == 0){
						$dia_cond = " and dia_width = '' ";
					}else{
						$dia_cond = " and dia_width is null ";
					}
				}
				else
				{
					$dia_cond = " and dia_width = '".str_replace("'", "", $$diaWidth)."'";
				}
				// echo "select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_to_company_id and item_category_id=2 and detarmination_id=".$$febDescripId." and gsm=".$$gsm." $dia_cond and status_active=1 and is_deleted=0";die;
				$row_prod = sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_to_company_id and item_category_id=2 and detarmination_id=".$$febDescripId." and gsm=".$$gsm." and color=".$$colorId." $dia_cond and status_active=1 and is_deleted=0");
				if (count($row_prod) > 0 || $new_prod_ref_arr[$cbo_to_company_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**2"] != "")
				{
					if(count($row_prod) > 0)
					{
						// $test='String1';
           				$new_prod_id = $row_prod[0][csf('id')];
           				$product_id_update_parameter[$new_prod_id]['qnty']+=$$rollWgt;
           				$product_id_update_parameter[$new_prod_id]['amount']+=$$rollAmount;
           				$update_to_prod_id[$new_prod_id]=$new_prod_id;
					}
					else
					{
						// $test='String2';
						$new_prod_id = $new_prod_ref_arr[$cbo_to_company_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**2"];
						$product_id_insert_parameter[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**2"]+=$$rollWgt;
						$product_id_insert_amount[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**2"]+=$$rollAmount;
					}
               	}
               	else
               	{
               		// $test='String3';
					   
               		$new_prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
               		$new_prod_ref_arr[$cbo_to_company_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**2"] = $new_prod_id;
               		$product_id_insert_parameter[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**2"]+=$$rollWgt;
               		$product_id_insert_amount[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**2"]+=$$rollAmount;
               	}


				if($$toOrderId=="") $toOrderIdRef=$$orderId; else $toOrderIdRef=$$toOrderId;

				//================Dynamic Batch Creation==============================
		
				
				// $batchData=sql_select("SELECT a.id, a.batch_weight, a.booking_no from pro_batch_create_mst a where a.batch_no = ".$$batch_no." and a.color_id = ".$$colorId." and a.status_active=1 and a.is_deleted=0 and a.entry_form in(14,68,134) and a.company_id=$cbo_to_company_id  group by a.id, a.batch_weight, a.booking_no");

				$batch_no          = str_replace("'", "", $$batch_no);
				$colorId           = str_replace("'", "", $$colorId);
				$booking_no        = str_replace("'", "", $$tobookingNo);
				$booking_id        = str_replace("'", "", $$toBookingMstId);
				$txt_transfer_qnty = str_replace("'", "", $$rollWgt);
				//echo "10**This rollWgt =". $rollWgt;die;

				$batchData = $batch_data_arr[$batch_no][$colorId][$booking_no]['id'];

				
				if($batchData)
				{
					$batch_id_to=$batchData;
					//$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty);
					$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$booking_no]['batch_weight']+$txt_transfer_qnty;
					$field_array_batch_update="batch_weight*updated_by*update_date";
					$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
				else
				{
					// $booking_id = str_replace("'", "", $hdn_to_booking_id);
					// $booking_no = str_replace("'", "", $hdn_to_booking_no);

					if($new_created_batch[$batch_no][$colorId][$booking_no]['id'] == ""){

						$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
						$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";
						if($data_array_batch!="") $data_array_batch.=",";
						$data_array_batch.="(".$batch_id_to.",'".$batch_no."',134,".$txt_transfer_date.",".$cbo_to_company_id.",".$booking_id.",'".$booking_no."',0,".$colorId.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

						$new_created_batch[$batch_no][$colorId][$booking_no]['id'] =$batch_id_to;
					}
					
				}

				//===========================================
			
				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$transactionID.",".$id.",'".$$recvBasis."',".$$batch_id.",".$$batch_id.",".$cbo_company_id.",".$$productId.",2,6,134,".$txt_transfer_date.",'".$$rollWgt."','".$$fromStoreId."','".$$fromFloor."','".$$fromRoom."','".$$fromRack."','".$$fromShelf."','".$$fromBin."','".$$fromBodyPart."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


				if(str_replace("'", "", $$fromBookingWithoutOrder) != 1 )
				{
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_prop!="") $data_array_prop.= ",";
					$data_array_prop.="(".$id_prop.",".$transactionID.",6,134,'".$dtls_id."','".$$orderId."',".$$productId.",'".$$rollWgt."','".$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					//505
				}
				

				$form_trans_id=$transactionID;
				//$transactionID = $transactionID+1;

				$to_trans_id=0;
				if($variable_auto_rcv==1) // if auto receive 1 No, then no need to acknowledgement
				{
					$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$to_trans_id=$transactionID;

				
					if($data_array_trans!="") $data_array_trans.=",";
					$data_array_trans.="(".$transactionID.",".$id.",'".$$recvBasis."',".$batch_id_to.",".$batch_id_to.",".$cbo_to_company_id.",".$new_prod_id.",2,5,134,".$txt_transfer_date.",'".$$rollWgt."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."','".$$cboToBodyPart."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_prop!="") $data_array_prop.= ",";
					$data_array_prop.="(".$id_prop.",".$transactionID.",5,134,'".$dtls_id."','".$toOrderIdRef."',".$new_prod_id.",'".$$rollWgt."','".$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					//505
				}

				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$id.",".$form_trans_id.",".$to_trans_id.",'".$$productId."',".$new_prod_id.",'".$$orderId."','".$toOrderIdRef."','".$$febDescripId."','".$$machineNoId."','".$$batch_id."','".$$fromStoreId."','".$$fromFloor."','".$$fromRoom."','".$$fromRack."','".$$fromShelf."','".$$fromBin."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."',".$cbo_item_category.",'".$$rollWgt."','".$$progBookPiId."','".$$knitDetailsId."','".$$transferEntryForm."','".$$fromBookingWithoutOrder."','".$$gsm."','".$$diaWidth."','".$$requiDtlsId."',".$batch_id_to.",'".$$fromBodyPart."','".$$cboToBodyPart."','".$$toBookingMstId."','".$$tobookingNo."','".$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			
				if($variable_auto_rcv==1) // if Auto recv No 1
				{
					$re_transfer=0;
				}
				else{
					$re_transfer=1;
				}
				
				// 		$field_array_roll="id, barcode_no, mst_id, dtls_id, company_id, is_transfer, po_breakdown_id, entry_form, qnty, roll_no, roll_id, booking_without_order, from_roll_id, re_transfer, inserted_by, insert_date";
				// $field_array_roll_update="is_transfer*updated_by*update_date";
				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$id.",".$dtls_id.",".$cbo_to_company_id.",6,'".$toOrderIdRef."',134,'".$$rollWgt."','".$$rollNo."','".$$rollId."','".$$bookWithoutOrder."','".$$rollMstId."',".$re_transfer.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				//505


				$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$form_trans_id."__".$to_trans_id."__".$id_roll.",";
				$prodData_array[$$productId]+=$$rollWgt;
				$prodData_amount_array[$$productId]+=$$rollAmount;
				$all_prod_id.=$$productId.",";
				$all_roll_id.=$$rollId.",";

				$inserted_roll_id_arr[$id_roll] =  $id_roll;
				$barcode_id[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);

				$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
				if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
				$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$toOrderIdRef.",".$new_prod_id.",0,0,0,0,".$txt_transfer_qnty.",".$dtls_id.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
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
                   	$data_array_prod_insert .= "(" . $prod_id . "," . $cbo_to_company_id . "," . $cbo_store_name . ",2," . $fabric_desc_id . ",'" . $cons_compo . "','" . $prod_name_dtls . "'," . "12" . "," . $avg_rate_per_unit . "," . $val . "," . $val . "," . $roll_amount . "," . $txt_gsm . ",'" . $txt_width . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
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


			//$all_prod_id_arr=implode(",",array_unique(explode(",",chop($all_prod_id,','))));
			$all_prod_id=chop($all_prod_id,',');
			$all_prod_id_arr =  array_unique(explode(",", $all_prod_id));

			$all_prod_id_cond=""; $all_prod_id_Cond="";
			if($db_type==2 && count($all_prod_id_array)>999)
			{
				$all_prod_id_array_chunk=array_chunk($all_prod_id_array,999) ;
				foreach($all_prod_id_array_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$all_prod_id_Cond.=" id in($chunk_arr_value) or ";
				}
				$all_prod_id_cond.=" and (".chop($all_prod_id_Cond,'or ').")";
			}
			else
			{
				$all_prod_id_cond=" and id in($all_prod_id)";
			}
			
			$fromProdIssueResult=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id $all_prod_id_cond");
			foreach($fromProdIssueResult as $row)
			{
				$issue_qty=$prodData_array[$row[csf('id')]];
				$issue_amount=$prodData_amount_array[$row[csf('id')]];

				$current_stock=$row[csf('current_stock')]-$issue_qty;
				$current_amount=$row[csf('stock_value')]-$issue_amount;
				$current_avg_rate=$row[csf('stock_value')]-$issue_amount;

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
				$batch_id="batchId_".$j;
				$batch_no="batchNo_".$j;
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
				$fromBodyPart="fromBodyPart_".$j;

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
				$cboToBodyPart="cboToBodyPart_".$j;
				$tobookingNo="tobookingNo_".$j;
				$toBookingMstId="toBookingMstId_".$j;
				$fromBookingWithoutOrder="fromBookingWithoutOrder_".$j;
				//echo "10**".$$rollNo;die;


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

				
				
				//---------------------------------------------------------------------------
				$dtls_id = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
				$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);

				if(str_replace("'", "", $cbo_transfer_criteria) == 4)
				{
					$batch_no          = str_replace("'", "", $$batch_no);
					$colorId           = str_replace("'", "", $$colorId);
					$booking_no        = str_replace("'", "", $$tobookingNo);
					$booking_id        = str_replace("'", "", $$toBookingMstId);
					$txt_transfer_qnty = str_replace("'", "", $$rollWgt);
					//echo "10**This tobookingNo =". $$tobookingNo;die;

					$batchData = $batch_data_arr[$batch_no][$colorId][$booking_no]['id'];
					//var_dump($batchData);die;
					
					if($batchData)
					{
						$batch_id_to=$batchData;
						//$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty);
						$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$booking_no]['batch_weight']+$txt_transfer_qnty;
						$field_array_batch_update="batch_weight*updated_by*update_date";
						$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					}
					else
					{
						// $booking_id = str_replace("'", "", $hdn_to_booking_id);
						// $booking_no = str_replace("'", "", $hdn_to_booking_no);
						if($new_created_batch[$batch_no][$colorId][$booking_no]['id'] == ""){

							$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
							$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";
							if($data_array_batch!="") $data_array_batch.=",";
							$data_array_batch.="(".$batch_id_to.",'".$batch_no."',134,".$txt_transfer_date.",".$cbo_to_company_id.",".$booking_id.",'".$booking_no."',0,".$colorId.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

							$new_created_batch[$batch_no][$colorId][$booking_no]['id'] =$batch_id_to;
						}
						
					}
				}
				else
				{
					$batch_id_to = $$batch_id; // for store to store transfer
					$colorId = $$colorId; // for store to store transfer
				}
				
				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$transactionID.",".$id.",'".$$recvBasis."',".$$batch_id.",".$$batch_id.",".$cbo_company_id.",'".$$productId."',2,6,134,".$txt_transfer_date.",'".$$rollWgt."','".$$fromStoreId."','".$$fromFloor."','".$$fromRoom."','".$$fromRack."','".$$fromShelf."','".$$fromBin."','".$$fromBodyPart."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				if(str_replace("'", "", $$fromBookingWithoutOrder) != 1)
				{
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_prop!="") $data_array_prop.= ",";
					$data_array_prop.="(".$id_prop.",".$transactionID.",6,134,'".$dtls_id."','".$$orderId."','".$$productId."','".$$rollWgt."','".$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					//505
				}
				

				$form_trans_id=$transactionID;

				$to_trans_id=0;
				if($variable_auto_rcv==1) // if auto receive 1 No, then no need to acknowledgement
				{
					$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$to_trans_id=$transactionID;
					
					if($data_array_trans!="") $data_array_trans.=",";
					$data_array_trans.="(".$transactionID.",".$id.",'".$$recvBasis."',".$batch_id_to.",".$batch_id_to.",".$cbo_to_company_id.",".$$productId.",2,5,134,".$txt_transfer_date.",'".$$rollWgt."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."','".$$cboToBodyPart."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

					
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_prop!="") $data_array_prop.= ",";
					$data_array_prop.="(".$id_prop.",".$transactionID.",5,134,'".$dtls_id."','".$toOrderIdRef."',".$$productId.",'".$$rollWgt."','".$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					//505
					
				}
				//---------------------------------------------------------------------------------------
				
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$id.",".$form_trans_id.",".$to_trans_id.",'".$$productId."','".$$productId."','".$$orderId."','".$toOrderIdRef."','".$$febDescripId."','".$$machineNoId."','".$$batch_id."','".$$fromStoreId."','".$$fromFloor."','".$$fromRoom."','".$$fromRack."','".$$fromShelf."','".$$fromBin."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."',".$cbo_item_category.",'".$$rollWgt."','".$$progBookPiId."','".$$knitDetailsId."','".$$transferEntryForm."','".$$fromBookingWithoutOrder."','".$$gsm."','".$$diaWidth."','".$$requiDtlsId."','".$batch_id_to."','".$$fromBodyPart."','".$$cboToBodyPart."','".$$toBookingMstId."','".$$tobookingNo."','".$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				if($variable_auto_rcv==1) // if Auto recv No 1
				{
					$re_transfer=0;
				}
				else{
					$re_transfer=1;
				}

				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$id.",".$dtls_id.",".$cbo_to_company_id.",6,'".$toOrderIdRef."',134,'".$$rollWgt."','".$$rollNo."','".$$rollId."','".$$bookWithoutOrder."','".$$rollMstId."',".$re_transfer.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$barcodeNos.=$$barcodeNo."__".$dtls_id."__0__0__".$id_roll.",";
				//505
				$all_roll_id.=$$rollId.",";

				$inserted_roll_id_arr[$id_roll] =  $id_roll;

				$barcode_id[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);

				if(str_replace("'", "", $cbo_transfer_criteria) == 4)
				{
					$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
					if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
					$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$toOrderIdRef.",'".$$productId."',0,0,0,0,".$txt_transfer_qnty.",".$dtls_id.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}

			}

			$all_roll_id_arr=array_unique(explode(",",chop($all_roll_id,',')));
			foreach($all_roll_id_arr as $roll_id)
			{
				$roll_id_array[]=$roll_id;
				$data_array_roll_update[$roll_id]=explode("*",("5*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}

			
		}
		// echo "10** insert into order_wise_pro_details ($field_array_prop) values $data_array_prop";die;
		//echo "10**insert into product_details_master (".$field_array_prod_insert.") values ".$data_array_prod_insert;die;

		$rID=$rID2=$rID3=$rID4=$rID5=$rID6=$prodUpdate=$rollUpdate=$rID7_roll_re_transfer=$rID7=true;
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			//echo "10** insert into inv_item_transfer_dtls ($field_array_dtls) values $data_array_dtls";die;

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
		}

		$totalRollId=chop($totalRollId,',');

		$rID7_roll_re_transfer=sql_multirow_update("pro_roll_details","re_transfer","1","id",$totalRollId,0);


		// $rID7=execute_query("update pro_roll_details set is_returned=1 where barcode_no in (". implode(',', $barcode_id).") and id not in (".implode(',', $inserted_roll_id_arr).")");
		// if ($flag == 1)
		// {
		// 	if ($rID7)
		// 		$flag = 1;
		// 	else
		// 		$flag = 0;
		// }

		$rID8=$rID9=true;

		if(str_replace("'","",$cbo_transfer_criteria)==1 || str_replace("'","",$cbo_transfer_criteria)==4)
		{
			if($data_array_batch_dtls!="")
			{
				//echo "10**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;oci_rollback($con);die;
				$rID8=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,0);
				if($flag==1)
				{
					if($rID8) $flag=1; else $flag=0;
				}
			}
			
			if($batchData)
			{
				//echo "10**";echo $data_array_batch_update."==".$batch_id_to;die;
				$rID9=sql_update("pro_batch_create_mst",$field_array_batch_update,$data_array_batch_update,"id",$batch_id_to,0);
			}
			else
			{
				//echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;oci_rollback($con);die;
				$rID9=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
			}

			if($flag==1)
			{
				if($rID9) $flag=1; else $flag=0;
			}
		}

		
		//echo "10** insert into order_wise_pro_details ($field_array_prop) values $data_array_prop";die;
		//echo "10**".bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
	  	// echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$rID6."&&".$prodUpdate."&&".$rollUpdate. "&&" . $rID7_roll_re_transfer ."&&".$rID7 ."&&".$rID8 ."&&".$rID9 ."##".$test; oci_rollback($con); die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate && $rollUpdate && $rID7_roll_re_transfer && $rID7 && $rID8 && $rID9)
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
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate && $rollUpdate && $rID7_roll_re_transfer && $rID7 && $rID8 && $rID9)
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
			$previousToBbatchId="previousToBbatchId_".$j;
			$all_previousToBbatchId.=$$previousToBbatchId.",";
			$rollMstId="rollMstId_".$j;

			$batch_no="batchNo_".$j;
			$all_batch_no.="'".$$batch_no."',";

			if ($$rolltableId!="") 
			{
				$saved_roll_arr[$$barcodeNo]=$$rolltableId;
			}
			else
			{
				$new_roll_arr[$$barcodeNo]=$$rollMstId;
			}

			//$update_dtls_id.=str_replace("'","",$$dtlsId).",";
		}

		
		// echo "20**all_dtlsId".$all_dtlsId;disconnect($con); die;
	
		$all_previousToBbatchId=chop($all_previousToBbatchId,',');
		
		$all_barcodeNo=chop($all_barcodeNo,',');
		$all_barcodeNo_arr=explode(",", $all_barcodeNo);

		$all_rolltableId=chop($all_rolltableId,',');
		$all_roll_id_arr=explode(",", $all_rolltableId);

		$all_batch_no=chop($all_batch_no,',');

		$all_batch_no_array =  explode(",", $all_batch_no);

		$all_batch_no_cond=""; $batchCond="";
		if($db_type==2 && count($all_batch_no_array)>999)
		{
			$all_batch_no_array_chunk=array_chunk($all_batch_no_array,999) ;
			foreach($all_batch_no_array_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$batchCond.=" a.batch_no in($chunk_arr_value) or ";
			}
			$all_batch_no_cond.=" and (".chop($batchCond,'or ').")";
		}
		else
		{
			$all_batch_no_cond=" and a.batch_no in($all_batch_no)";
		}
		

		//    echo "SELECT a.id,a.batch_no,a.color_id, a.batch_weight, a.booking_no from pro_batch_create_mst a where   a.status_active=1 and a.is_deleted=0 and a.entry_form in(14,68,134) and a.company_id=$cbo_to_company_id $all_batch_no_cond  group by a.id,a.batch_no,a.color_id,a.batch_weight, a.booking_no";die;

		$batchData=sql_select("SELECT a.id,a.batch_no,a.color_id, a.batch_weight, a.booking_no from pro_batch_create_mst a where   a.status_active=1 and a.is_deleted=0 and a.entry_form in(14,68,134) and a.company_id=$cbo_to_company_id $all_batch_no_cond  group by a.id,a.batch_no,a.color_id,a.batch_weight, a.booking_no");

		$batch_data_arr=array();
		foreach ($batchData as $rows)
		{
			$batch_data_arr[$rows[csf("batch_no")]][$rows[csf("color_id")]][$rows[csf("booking_no")]]['id']=$rows[csf("id")];
			$batch_data_arr[$rows[csf("batch_no")]][$rows[csf("color_id")]][$rows[csf("booking_no")]]['batch_weight']=$rows[csf("batch_weight")];
		}
		//var_dump($batch_data_arr);die;

		 //echo "10**<pre>";print_r($all_barcodeNo);die;
		if($all_barcodeNo!="")
		{
			$all_barcodeNo_array =  array_unique(explode(",", $all_barcodeNo));
			$all_barcodeNo_cond="";$all_barcodeNo_cond_noAlias=""; $barcodeNoCond="";$barcodeNoCond_noalias="";
			if($db_type==2 && count($all_barcodeNo_array)>999)
			{
				$all_barcodeNo_array_chunk=array_chunk($all_barcodeNo_array,999) ;
				foreach($all_barcodeNo_array_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barcodeNoCond.=" a.barcode_no in($chunk_arr_value) or ";
					$barcodeNoCond_noalias.=" barcode_no in($chunk_arr_value) or ";
				}
				$all_barcodeNo_cond.=" and (".chop($barcodeNoCond,'or ').")";
				$all_barcodeNo_cond_noAlias.=" and (".chop($barcodeNoCond_noalias,'or ').")";
			}
			else
			{
				$all_barcodeNo_cond=" and a.barcode_no in($all_barcodeNo)";
				$all_barcodeNo_cond_noAlias=" and barcode_no in($all_barcodeNo)";
			}
			
			$issue_data_refer = sql_select("select a.id, a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 71 $all_barcodeNo_cond and a.status_active = 1 and a.is_deleted = 0 and a.is_returned=0");
			if($issue_data_refer[0][csf("barcode_no")] != "")
			{
				echo "20**Sorry Barcode No : ". $issue_data_refer[0][csf("barcode_no")] ."\nFound in Issue No ".$issue_data_refer[0][csf("issue_number")];
				disconnect($con);
				die;
			}

			$next_transfer_sql = sql_select("select max(a.id) as max_id,  a.barcode_no from pro_roll_details a
			where  a.status_active =1 $all_barcodeNo_cond and a.is_deleted=0 group by  a.barcode_no");
			foreach ($next_transfer_sql as $next_trans)
			{
				$next_transfer_arr[$next_trans[csf('barcode_no')]]=$next_trans[csf('max_id')];
			}

			$current_transfer_sql = sql_select("select a.barcode_no, b.transfer_system_id as system_id from pro_roll_details a, inv_item_transfer_mst b where a.mst_id=b.id and a.entry_form in (134,214,216,219) $all_barcodeNo_cond and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0
			union all 
			select a.barcode_no, b.recv_number as system_id from pro_roll_details a, inv_receive_master b where a.mst_id=b.id and a.entry_form in (126) $all_barcodeNo_cond and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0");

			foreach ($current_transfer_sql as $current_trans)
			{
				$next_transfer_ref[$current_trans[csf('barcode_no')]]["transfer_no"]=$current_trans[csf('system_id')];
			}

			if (!empty($saved_roll_arr)) // Saved barcode to next transaction found
			{
				foreach ($saved_roll_arr as $barcode => $saved_roll_id) 
				{
					if ($saved_roll_id != $next_transfer_arr[$barcode]) 
					{
						echo "20**Sorry Barcode No : ". $barcode ." \nFound in Transfer/Return No : ".$next_transfer_ref[$barcode]["transfer_no"];
						disconnect($con);
						die;
					}
				}
			}
			if (!empty($new_roll_arr)) // new barcode show in current transfer but this barcode saved to another tab 
			{
				foreach ($new_roll_arr as $barcode => $new_roll_id) 
				{
					if ($new_roll_id != $next_transfer_arr[$barcode]) 
					{
						echo "20**Sorry Barcode No : ". $barcode ." \nFound in Transfer/Return No : ".$next_transfer_ref[$barcode]["transfer_no"];
						disconnect($con);
						die;
					}
				}
			}

			
			$split_roll_sql=sql_select("select barcode_no,split_from_id from pro_roll_split where status_active =1 $all_barcodeNo_cond_noAlias");

			foreach($split_roll_sql as $bar)
			{
				$split_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
			}

			$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 $all_barcodeNo_cond_noAlias and entry_form = 134 order by barcode_no");
			foreach($child_split_sql as $bar)
			{
				$child_splited_arr[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
			}

			$all_dtlsId=chop($all_dtlsId,',');
			$all_dtlsId_arr=explode(",", $all_dtlsId);
			//echo "20**all_dtlsId ".$all_dtlsId;die;
			if($all_dtlsId !="")
			{
				$all_dtlsId_array =  array_unique(explode(",", $all_dtlsId));
				$all_dtlsId_cond=""; $all_dtlsIdCond="";
				if($db_type==2 && count($all_dtlsId_array)>999)
				{
					$all_dtlsId_array_chunk=array_chunk($all_dtlsId_array,999) ;
					foreach($all_dtlsId_array_chunk as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$all_dtlsIdCond.=" a.id in($chunk_arr_value) or ";
					}
					$all_dtlsId_cond.=" and (".chop($all_dtlsIdCond,'or ').")";
				}
				else
				{
					$all_dtlsId_cond=" and a.id in($all_dtlsId)";
				}
				
				$deleted_dtls=sql_select("select b.barcode_no, a.id from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=134 $all_dtlsId_cond and a.status_active=0 and a.is_deleted=1");

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

		$field_array_trans = "id, mst_id, receive_basis, batch_id, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type,entry_form, transaction_date, cons_quantity, store_id, floor_id, room, rack, self, bin_box, inserted_by, insert_date";

		$field_array_trans_deleted = "status_active*is_deleted*updated_by*update_date";
		$field_array_trans_update = "floor_id*room*rack*self*bin_box*batch_id*pi_wo_batch_no*body_part_id*updated_by*update_date";

		//$dtls_id=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;

		

		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, from_order_id, to_order_id, feb_description_id, machine_no_id, batch_id, from_store, floor_id, room, rack, shelf, bin_box, to_store,to_floor_id,to_room,to_rack,to_shelf,to_bin_box, item_category, transfer_qnty, knit_program_id, prod_detls_id, from_trans_entry_form, from_booking_without_order, gsm, dia_width,to_batch_id, body_part_id, to_body_part,  to_ord_book_id, to_ord_book_no, color_id, inserted_by, insert_date";

		$field_array_dtls_up="from_order_id*to_order_id*to_store*to_floor_id*to_room*to_rack*to_shelf*to_bin_box*knit_program_id*prod_detls_id*from_trans_entry_form*gsm*dia_width*body_part_id*to_body_part*updated_by*update_date";

		$field_array_dtls_deleted="status_active*is_deleted*updated_by*update_date";

		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll="id, barcode_no, mst_id, dtls_id, company_id, is_transfer, po_breakdown_id, entry_form, qnty, roll_no, roll_id, booking_without_order, from_roll_id, re_transfer, inserted_by, insert_date";
		$field_array_roll_up="po_breakdown_id*roll_id*roll_no*booking_without_order*updated_by*update_date";
		$field_array_roll_deleted="status_active*is_deleted*updated_by*update_date";

		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$field_array_prop="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, color_id, inserted_by, insert_date";
		$field_array_prop_up="po_breakdown_id*updated_by*update_date";
		$field_array_prop_up_deleted="status_active*is_deleted*updated_by*update_date";
		$field_array_roll_update="is_transfer*updated_by*update_date";

		$field_array_batch_dtls="id, mst_id, po_id, prod_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id,width_dia_type, inserted_by, insert_date";

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
				$batch_id="batchId_".$j;
				$batch_no="batchNo_".$j;
				$colorId="colorId_".$j;

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
				$fromBodyPart="fromBodyPart_".$j;

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
				$cboToBodyPart="cboToBodyPart_".$j;
				$tobookingNo="tobookingNo_".$j;
				$toBookingMstId="toBookingMstId_".$j;
				$hiddenTransferqnty="hiddenTransferqnty_".$j;
				$constructCompo="constructCompo_".$j;
				$rollAmount="rollAmount_".$j;
				$previousToBbatchId="previousToBbatchId_".$j;

				$fromBookingWithoutOrder="fromBookingWithoutOrder_".$j;
				$requiDtlsId="requiDtlsId_".$j;
				
				if($$toOrderId=="") $toOrderIdRef=$$orderId; else $toOrderIdRef=$$toOrderId;
				
				//------------------------------------VALIDATION FOR DUPLICATE---------------------------------------
				if($$dtlsId=="")
				{
					if($trans_po_barcode_check_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$orderId)] != str_replace("'", "", $$barcodeNo)."__".str_replace("'", "", $$orderId))
					{
						if($$fromBookingWithoutOrder == 1)
						{
							echo "20**Sorry! This barcode  =". $$barcodeNo ." doesn't belong to this from booking no";
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
					echo "20**Sorry! This barcode (". str_replace("'","", $$barcodeNo) .") is split. actual weight ". number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","") ." doesn't match with current ".number_format($$rollWgt,2,".","") ."";
					disconnect($con);
					die;
				}
				
				if($$dtlsId>0) // Update
				{
					$batch_no          = str_replace("'", "", $$batch_no);
					$colorId           = str_replace("'", "", $$colorId);
					$booking_no        = str_replace("'", "", $$tobookingNo);
					$booking_id        = str_replace("'", "", $$toBookingMstId);
					$txt_transfer_qnty = str_replace("'", "", $$rollWgt);
					
					$batchData = $batch_data_arr[$batch_no][$colorId][$booking_no]['id'];
					
					$field_array_batch_update="batch_weight*updated_by*update_date";

					if($batchData)
					{
						$batch_id_to=$batchData;
						//$batch_id_to=$batchData[0][csf('id')];
						
						
						if($batch_id_to==str_replace("'","",$$previousToBbatchId))
						{
							$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$booking_no]['batch_weight']+str_replace("'", '',$txt_transfer_qnty)-str_replace("'", '',$$hiddenTransferqnty);
						
		
							$update_batch_id[]=str_replace("'","",$$previousToBbatchId);
							$data_array_batch_update[str_replace("'","",$$previousToBbatchId)]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
						}
						else
						{
							//previous batch adjusted
							$previousToBbatchId = str_replace("'","",$$previousToBbatchId);
							$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$previousToBbatchId");
							$adjust_batch_weight=$batch_weight-str_replace("'", '',$$hiddenTransferqnty);
							$data_array_batch_update[$previousToBbatchId]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		
							//new batch adjusted $tobookingNo
							$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$booking_no]['batch_weight']+str_replace("'", '',$txt_transfer_qnty);
							$update_batch_id[]=$batchData;
							$data_array_batch_update[]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
							
						}
					}
					else
					{
		
						//    $booking_id = str_replace("'", "", $hdn_to_booking_id);
						//    $booking_no = str_replace("'", "", $hdn_to_booking_no);

						if( $new_created_batch[$batch_no][$colorId][$booking_no]['id'] == "" )
						{
							$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
							$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";
							
						}
		
						$data_array_batch.="(".$batch_id_to.",'".$batch_no."',134,".$txt_transfer_date.",".$cbo_to_company_id.",".$booking_id.",'".$booking_no."',0,".$colorId.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

						$new_created_batch[$batch_no][$colorId][$booking_no]['id'] =$batch_id_to;
		
						//previous batch adjusted
						$previousToBbatchId = str_replace("'","",$$previousToBbatchId);
						$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$previousToBbatchId");
						$adjust_batch_weight=$batch_weight-str_replace("'", '',$$hiddenTransferqnty);
						$update_batch_id[]=$previousToBbatchId;
						$data_array_batch_update[$previousToBbatchId]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
						
					} 
					if($variable_auto_rcv == 1 )
					{
						if(str_replace("'", "", $$transIdTo))
						{
							$prop_id_array_up[]=$$transIdTo;
							$data_array_prop_up[$$transIdTo]=explode("*",($toOrderIdRef."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

							$data_array_transaction_up[$$transIdTo]=explode("*",("'".$$toFloor."'*'".$$toRoom."'*'".$$toRack."'*'".$$toShelf."'*'".$$toBin."'*'".$batch_id_to."'*'".$batch_id_to."'*'".$$cboToBodyPart."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
							
						}
					}
					$dtls_id_array_up[]=$$dtlsId;
					$data_array_dtls_up[$$dtlsId]=explode("*",($$orderId."*".$toOrderIdRef."*".$cbo_store_name."*'".$$toFloor."'*'".$$toRoom."'*'".$$toRack."'*'".$$toShelf."'*'".$$toBin."'*'".$$progBookPiId."'*'".$$knitDetailsId."'*'".$$transferEntryForm."'*'".$$gsm."'*'".$$diaWidth."'*'".$$fromBodyPart."'*'".$$cboToBodyPart."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

					$roll_id_array_up[]=$$rolltableId;
					$data_array_roll_up[$$rolltableId]=explode("*",($toOrderIdRef."*".$$rollId."*".$$rollNo."*'".$$bookWithoutOrder."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

						 
					$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
					if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
					$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$toOrderIdRef.",".$$productId.",0,0,0,0,".$txt_transfer_qnty.",".$$dtlsId.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					   
				}
				else // New Insert
				{
					if (str_replace("'", "", $$diaWidth)=="")
					{
						if($db_type == 0){
							$dia_cond = " and dia_width = '' ";
						}else{
							$dia_cond = " and dia_width is null ";
						}
					}
					else
					{
						$dia_cond = " and dia_width = '".str_replace("'", "", $$diaWidth)."'";
					}
					//$new_prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
					$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
					$dtls_id = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);

					$row_prod = sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_to_company_id and item_category_id=2 and detarmination_id='".$$febDescripId."' and gsm='".$$gsm."' and color=".$colorId." $dia_cond and status_active=1 and is_deleted=0");
					if (count($row_prod) > 0 || $new_prod_ref_arr[$cbo_to_company_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**2"] != "")
					{
						if(count($row_prod) > 0)
						{
							// $test='String1';
	           				$new_prod_id = $row_prod[0][csf('id')];
	           				$product_id_update_parameter[$new_prod_id]['qnty']+=$$rollWgt;
	           				$product_id_update_parameter[$new_prod_id]['amount']+=$$rollAmount;
	           				$update_to_prod_id[$new_prod_id]=$new_prod_id;
						}
						else
						{
							// $test='String2';
							$new_prod_id = $new_prod_ref_arr[$cbo_to_company_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**2"];
							$product_id_insert_parameter[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**2"]+=$$rollWgt;
							$product_id_insert_amount[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**2"]+=$$rollAmount;
						}
	               	}
	               	else
	               	{
	               		// $test='String3';
	               		$new_prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
	               		$new_prod_ref_arr[$cbo_to_company_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**2"] = $new_prod_id;
	               		$product_id_insert_parameter[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**2"]+=$$rollWgt;
	               		$product_id_insert_amount[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**2"]+=$$rollAmount;
						   
					
	               	}
					   
					   $batch_no          = str_replace("'", "", $$batch_no);
					   $colorId           = str_replace("'", "", $$colorId);
					   $booking_no        = str_replace("'", "", $$tobookingNo);
					   $booking_id        = str_replace("'", "", $$toBookingMstId);
					   $txt_transfer_qnty = str_replace("'", "", $$rollWgt);
					   //echo "10**This rollWgt =". $rollWgt;die;
	   
					   $batchData = $batch_data_arr[$batch_no][$colorId][$booking_no]['id'];
	   
					   
					   if($batchData)
					   {
						   $batch_id_to=$batchData;
						   //$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty);
						   $curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$booking_no]['batch_weight']+$txt_transfer_qnty;
						   $field_array_batch_update="batch_weight*updated_by*update_date";
						   $data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					   }
					   else
					   {
						   // $booking_id = str_replace("'", "", $hdn_to_booking_id);
						   // $booking_no = str_replace("'", "", $hdn_to_booking_no);
						   if($new_created_batch[$batch_no][$colorId][$booking_no]['id'] == ""){

								$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
								$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";
								
						   }
		   
						   $data_array_batch.="(".$batch_id_to.",'".$batch_no."',134,".$txt_transfer_date.",".$cbo_to_company_id.",".$booking_id.",'".$booking_no."',0,".$colorId.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

						   $new_created_batch[$batch_no][$colorId][$booking_no]['id'] =$batch_id_to;
					   } 

					$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
					if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
					$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$toOrderIdRef.",".$new_prod_id.",0,0,0,0,".$txt_transfer_qnty.",".$dtls_id.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";   
					  

					if($data_array_trans!="") $data_array_trans.=",";
					$data_array_trans.="(".$transactionID.",".$update_id.",'".$$recvBasis."',".$$batch_id.",".$$batch_id.",".$cbo_company_id.",'".$$productId."',2,6,134,".$txt_transfer_date.",'".$$rollWgt."','".$$fromStoreId."','".$$fromFloor."','".$$fromRoom."','".$$fromRack."','".$$fromShelf."','".$$fromBin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

					if(str_replace("'", "", $fromBookingWithoutOrder) !=1)
					{
						$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						if($data_array_prop!="") $data_array_prop.= ",";
						$data_array_prop.="(".$id_prop.",".$transactionID.",6,134,'".$dtls_id."','".$$orderId."','".$$productId."','".$$rollWgt."','".$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					}

					$form_trans_id=$transactionID;
					
					$to_trans_id=0;
					if($variable_auto_rcv==1) // if auto receive 1 No, then no need to acknowledgement
					{
						$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
						$to_trans_id=$transactionID;

						if($data_array_trans!="") $data_array_trans.=",";

						$data_array_trans.="(".$transactionID.",".$update_id.",'".$$recvBasis."',".$batch_id_to.",".$batch_id_to.",".$cbo_to_company_id.",".$new_prod_id.",2,5,134,".$txt_transfer_date.",'".$$rollWgt."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

						$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						if($data_array_prop!="") $data_array_prop.= ",";
						$data_array_prop.="(".$id_prop.",".$transactionID.",5,134,'".$dtls_id."','".$toOrderIdRef."',".$new_prod_id.",'".$$rollWgt."','".$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					}
					
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$dtls_id.",".$update_id.",".$form_trans_id.",".$to_trans_id.",'".$$productId."',".$new_prod_id.",'".$$orderId."','".$toOrderIdRef."','".$$febDescripId."','".$$machineNoId."','".$$batch_id."','".$$fromStoreId."','".$$fromFloor."','".$$fromRoom."','".$$fromRack."','".$$fromShelf."','".$$fromBin."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."',".$cbo_item_category.",'".$$rollWgt."','".$$progBookPiId."','".$$knitDetailsId."','".$$transferEntryForm."','".$$fromBookingWithoutOrder."','".$$gsm."','".$$diaWidth."','".$$requiDtlsId."','".$batch_id_to."','".$$fromBodyPart."','".$$cboToBodyPart."','".$$toBookingMstId."','".$$tobookingNo."','".$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

					if($variable_auto_rcv==1) // if Auto recv No 1
					{
						$re_transfer=0;
					}
					else{
						$re_transfer=1;
					}

					if($data_array_roll!="") $data_array_roll.= ",";
					$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$update_id.",".$dtls_id.",".$cbo_to_company_id.",6,'".$toOrderIdRef."',82,'".$$rollWgt."','".$$rollNo."','".$$rollId."','".$$bookWithoutOrder."','".$$rollMstId."',".$re_transfer.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

					$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$form_trans_id."__".$to_trans_id."__".$id_roll.",";
					$prodData_array[$$productId]+=$$rollWgt;
					$prodData_array_amount[$$productId]+=$$rollAmount;
					$all_prod_id.=$$productId.","; // if new insert $$productId is from product id
					$all_roll_id.=$$rollId.",";

					$inserted_roll_id_arr[$id_roll] = $id_roll;
					$new_inserted[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);

					$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
			
					if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
					$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$toOrderIdRef.",".$new_prod_id.",0,0,0,0,".$txt_transfer_qnty.",".$dtls_id.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
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
                   	$data_array_prod_insert .= "(" . $prod_id . "," . $cbo_to_company_id . "," . $cbo_store_name . ",2," . $fabric_desc_id . ",'" . $cons_compo . "','" . $prod_name_dtls . "'," . "12" . "," . $avg_rate_per_unit . "," . $val . "," . $val . "," . $roll_amount . "," . $txt_gsm . ",'" . $txt_width . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

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
				$batch_id="batchId_".$j;
				$batch_no="batchNo_".$j;
				$colorId="colorId_".$j;
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
				$fromBodyPart="fromBodyPart_".$j;

				$toOrderId="toOrderId_".$j;
				$cboToBodyPart="cboToBodyPart_".$j;
				$tobookingNo="tobookingNo_".$j;
				$toBookingMstId="toBookingMstId_".$j;
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
				$previousToBbatchId="previousToBbatchId_".$j;
				$rollMstId="rollMstId_".$j;
				$totalRollId.=$$rollMstId.",";

				
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
					echo "20**Sorry! This barcode (". str_replace("'","", $$barcodeNo) .") is split. actual weight ". number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","") ." doesn't match with current ".number_format($$rollWgt,2,".","") ."";
					disconnect($con);
					die;
				}

				if($$toOrderId=="") $toOrderIdRef=$$orderId; else $toOrderIdRef=$$toOrderId;
				
				if($$dtlsId>0)
				{
					if(str_replace("'","",$cbo_transfer_criteria)==4)
					{
						// $batchData=sql_select("SELECT a.id, a.booking_no, a.batch_weight from pro_batch_create_mst a where a.batch_no=$txt_batch_no and a.color_id=$hide_color_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=14 and a.company_id=$company_id_to and a.booking_no = $hdn_to_booking_no group by a.id, a.batch_weight, a.booking_no");

						$batch_no          = str_replace("'", "", $$batch_no);
						$colorId           = str_replace("'", "", $$colorId);
						$booking_no        = str_replace("'", "", $$tobookingNo);
						$booking_id        = str_replace("'", "", $$toBookingMstId);
						$txt_transfer_qnty = str_replace("'", "", $$rollWgt);
						//echo "10**This rollWgt =". $txt_transfer_qnty;die;

						$batchData = $batch_data_arr[$batch_no][$colorId][$booking_no]['id'];
						
						$field_array_batch_update="batch_weight*updated_by*update_date";
						if($batchData)
						{
							
							$batch_id_to=$batchData;
							if($batch_id_to==str_replace("'","",$$previousToBbatchId))
							{
								
								$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$booking_no]['batch_weight']+str_replace("'", '',$txt_transfer_qnty)-str_replace("'", '',$$hiddenTransferqnty);

								$update_batch_id[]=str_replace("'","",$$previousToBbatchId);
								$data_array_batch_update[str_replace("'","",$$previousToBbatchId)]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
								
							}
							else
							{
								
								//previous batch adjusted
								$previousToBbatchId = str_replace("'","",$$previousToBbatchId);
								$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$previousToBbatchId");
								$adjust_batch_weight=$batch_weight-str_replace("'", '',$$hiddenTransferqnty);
								$data_array_batch_update[$previousToBbatchId]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

								//new batch adjusted
								$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$booking_no]['batch_weight']+str_replace("'", '',$txt_transfer_qnty);
								$update_batch_id[]=$batchData;
								$data_array_batch_update[$batchData]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
							}
						}
						else
						{
							// $booking_id = str_replace("'", "", $hdn_to_booking_id);
							// $booking_no = str_replace("'", "", $hdn_to_booking_no);
							if($new_created_batch[$batch_no][$colorId][$booking_no]['id'] == ""){

								$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
								$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";

							}

							$data_array_batch.="(".$batch_id_to.",'".$batch_no."',134,".$txt_transfer_date.",".$cbo_to_company_id.",".$booking_id.",'".$booking_no."',0,".$colorId.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

							$new_created_batch[$batch_no][$colorId][$booking_no]['id'] =$batch_id_to;

							//previous batch adjusted
							$previousToBbatchId = str_replace("'","",$$previousToBbatchId);
							
							$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$previousToBbatchId");
							$adjust_batch_weight=$batch_weight-str_replace("'", '',$$hiddenTransferqnty);
							$update_batch_id[]=$previousToBbatchId;
							//var_dump($update_batch_id);die;
							$data_array_batch_update[$previousToBbatchId]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
						}
					}
					else
					{
						$batch_id_to = $$batch_id; // for store to store transfer
						$colorId = $$colorId; // for store to store transfer
					}
					$batch_id = str_replace("'", "", $$batch_id);
					$batch_id_to = str_replace("'", "", $batch_id_to);

					if($variable_auto_rcv==1) // if auto receive 1 No, then no need to acknowledgement
					{
						if(str_replace("'", "", $$transIdTo)  !="" && str_replace("'", "", $$transIdTo) !=0)
						{
							$prop_id_array_up[]=$$transIdTo;
							$data_array_prop_up[$$transIdTo]=explode("*",($toOrderIdRef."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
							$data_array_transaction_up[$$transIdTo]=explode("*",("'".$$toFloor."'*'".$$toRoom."'*'".$$toRack."'*'".$$toShelf."'*'".$$toBin."'*'".$batch_id_to."'*'".$batch_id_to."'*'".$$cboToBodyPart."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
						}
					}					

				

					$dtls_id_array_up[]=$$dtlsId;
					$data_array_dtls_up[$$dtlsId]=explode("*",($$orderId."*".$toOrderIdRef."*".$cbo_store_name."*'".$$toFloor."'*'".$$toRoom."'*'".$$toRack."'*'".$$toShelf."'*'".$$toBin."'*'".$$progBookPiId."'*'".$$knitDetailsId."'*'".$$transferEntryForm."'*'".$$gsm."'*'".$$diaWidth."'*'".$$fromBodyPart."'*'".$$cboToBodyPart."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

					$roll_id_array_up[]=$$rolltableId;
					//$data_array_roll_up[$$rolltableId]=explode("*",($$toOrderId."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					$data_array_roll_up[$$rolltableId]=explode("*",($toOrderIdRef."*".$$rollId."*".$$rollNo."*'".$$bookWithoutOrder."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

					
					if(str_replace("'", "", $cbo_transfer_criteria) == 4)
					{
						$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
						if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
						$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$toOrderIdRef.",'".$$productId."',0,0,0,0,".$txt_transfer_qnty.",".$$dtlsId.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					}
					
				}
				
				else if($$barcodeNo != "")
				{
					//--------------------------------------------------------------------------
					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
					$dtls_id = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
					$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);

					if(str_replace("'", "", $cbo_transfer_criteria) == 4)
					{
						$batch_no          = str_replace("'", "", $$batch_no);
						$colorId           = str_replace("'", "", $$colorId);
						$booking_no        = str_replace("'", "", $$tobookingNo);
						$booking_id        = str_replace("'", "", $$toBookingMstId);
						$txt_transfer_qnty = str_replace("'", "", $$rollWgt);
						//echo "10**This rollWgt =". $rollWgt;die;

						$batchData = $batch_data_arr[$batch_no][$colorId][$booking_no]['id'];
						//var_dump($batchData);die;
						
						if($batchData)
						{
							$batch_id_to=$batchData;
							//$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty);
							$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$booking_no]['batch_weight']+$txt_transfer_qnty;
							$field_array_batch_update="batch_weight*updated_by*update_date";
							$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
						}
						else
						{
							// $booking_id = str_replace("'", "", $hdn_to_booking_id);
							// $booking_no = str_replace("'", "", $hdn_to_booking_no);
							if($new_created_batch[$batch_no][$colorId][$booking_no]['id'] == ""){

								$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
								$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";
							}

							

							$data_array_batch.="(".$batch_id_to.",'".$batch_no."',134,".$txt_transfer_date.",".$cbo_to_company_id.",".$booking_id.",'".$booking_no."',0,".$colorId.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

							$new_created_batch[$batch_no][$colorId][$booking_no]['id'] =$batch_id_to;
						}
					}
					else
					{
						$batch_id_to = $$batch_id; // for store to store transfer
						$colorId = $$colorId; // for store to store transfer
					}

					if(str_replace("'", "", $cbo_transfer_criteria) == 4)
					{
						$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
						if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
						$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$toOrderIdRef.",'".$$productId."',0,0,0,0,".$txt_transfer_qnty.",".$dtls_id.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					}
					
					
					if($data_array_trans!="") $data_array_trans.=",";
					$data_array_trans.="(".$transactionID.",".$update_id.",'".$$recvBasis."',".$$batch_id.",".$$batch_id.",".$cbo_company_id.",'".$$productId."',2,6,134,".$txt_transfer_date.",'".$$rollWgt."','".$$fromStoreId."','".$$fromFloor."','".$$fromRoom."','".$$fromRack."','".$$fromShelf."','".$$fromBin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

					if(str_replace("'", "", $$fromBookingWithoutOrder) !=1)
					{
						$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						if($data_array_prop!="") $data_array_prop.= ",";
						$data_array_prop.="(".$id_prop.",".$transactionID.",6,134,'".$dtls_id."','".$$orderId."','".$$productId."','".$$rollWgt."','".$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						//505
					}
					

					$form_trans_id=$transactionID;
					$to_trans_id=0;
					if($variable_auto_rcv==1) // if auto receive 1 No, then no need to acknowledgement
					{
						$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
						$to_trans_id=$transactionID;
	
					
						if($data_array_trans!="") $data_array_trans.=",";
						$data_array_trans.="(".$transactionID.",".$update_id.",'".$$recvBasis."',".$batch_id_to.",".$batch_id_to.",".$cbo_to_company_id.",'".$$productId."',2,5,134,".$txt_transfer_date.",'".$$rollWgt."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

						
						$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						if($data_array_prop!="") $data_array_prop.= ",";
						$data_array_prop.="(".$id_prop.",".$transactionID.",5,134,'".$dtls_id."','".$toOrderIdRef."','".$$productId."','".$$rollWgt."','".$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						//505
						
					}
					//-------------------------------------------------------------------------------
					

					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$dtls_id.",".$update_id.",".$form_trans_id.",".$to_trans_id.",'".$$productId."','".$$productId."','".$$orderId."','".$toOrderIdRef."','".$$febDescripId."','".$$machineNoId."','".$$batch_id."','".$$fromStoreId."','".$$fromFloor."','".$$fromRoom."','".$$fromRack."','".$$fromShelf."','".$$fromBin."',".$cbo_store_name.",'".$$toFloor."','".$$toRoom."','".$$toRack."','".$$toShelf."','".$$toBin."',".$cbo_item_category.",'".$$rollWgt."','".$$progBookPiId."','".$$knitDetailsId."','".$$transferEntryForm."','".$$fromBookingWithoutOrder."','".$$gsm."','".$$diaWidth."',".$batch_id_to.",'".$$fromBodyPart."','".$$cboToBodyPart."','".$$toBookingMstId."','".$$tobookingNo."','".$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

					if($variable_auto_rcv==1) // if Auto recv No 1
					{
						$re_transfer=0;
					}
					else{
						$re_transfer=1;
					}

					if($data_array_roll!="") $data_array_roll.= ",";
					$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$update_id.",".$dtls_id.",".$cbo_to_company_id.",6,'".$toOrderIdRef."',134,'".$$rollWgt."','".$$rollNo."','".$$rollId."','".$$bookWithoutOrder."','".$$rollMstId."',".$re_transfer.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$barcodeNos.=$$barcodeNo."__".$dtls_id."__0__0__".$id_roll.",";
					//505
					
					$all_roll_id.=$$rollId.",";

					$inserted_roll_id_arr[$id_roll] = $id_roll;
					$new_inserted[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);

					if(str_replace("'", "", $cbo_transfer_criteria) == 4)
					{
						$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
						if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
						$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$toOrderIdRef.",'".$$productId."',0,0,0,0,".$txt_transfer_qnty.",".$dtls_id.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					}
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

			$issue_data_ref = sql_select("select a.id, a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 71 and a.barcode_no  in ($txt_deleted_barcode) and a.status_active = 1 and a.is_deleted = 0 and is_returned <> 1");
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

			$nxt_transfer_sql = sql_select("SELECT max(a.id) as max_id,  a.barcode_no from pro_roll_details a
			where  a.barcode_no in ($txt_deleted_barcode) and a.status_active =1 and a.is_deleted=0 group by  a.barcode_no");
			foreach ($nxt_transfer_sql as $nxt_trans)
			{
				$nxt_transfer_arr[$nxt_trans[csf('barcode_no')]]=$nxt_trans[csf('max_id')];
			}

			$deleted_saved_transfer_sql = sql_select("SELECT a.id, a.barcode_no from pro_roll_details a
			where  a.barcode_no in ($txt_deleted_barcode) and a.mst_id=$update_id and a.entry_form = 134 and a.status_active =1 and a.is_deleted=0");
			//505
			foreach ($deleted_saved_transfer_sql as $row)
			{
				$deleted_saved_transfer_arr[$row[csf('barcode_no')]]=$row[csf('id')];
			}

			$current_transfer_sql = sql_select("SELECT a.barcode_no, b.transfer_system_id as system_id from pro_roll_details a, inv_item_transfer_mst b where a.mst_id=b.id and a.entry_form in (134,214,216,219) and a.barcode_no in ($txt_deleted_barcode) and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0
			union all 
			select a.barcode_no, b.recv_number as system_id from pro_roll_details a, inv_receive_master b where a.mst_id=b.id and a.entry_form =126 and a.barcode_no in ($txt_deleted_barcode) and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0"); //,505
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

			$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 and barcode_no in ($txt_deleted_barcode) and entry_form = 134 order by barcode_no"); //505
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
				//$all_dtls_ids_deleted.=$trnsfDtlsId.",";
				$data_array_dtls_deleted[$trnsfDtlsId]=explode("*",("0"."*"."1"."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				$roll_id_array_deleted[]=$rollDtlsId;
				$data_array_roll_deleted[$rollDtlsId]=explode("*",("0"."*"."1"."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}

		if(!empty($new_inserted))
		{
			$issue_data_ref = sql_select("select a.id, a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 71 and a.barcode_no  in (".implode(',', $new_inserted).") and a.status_active = 1 and a.is_deleted = 0 and is_returned <> 1");
			if($issue_data_ref[0][csf("barcode_no")] != ""){
				echo "20**Sorry Barcode No ". $issue_data_ref[0][csf("barcode_no")] ." Found in Issue No ".$issue_data_ref[0][csf("issue_number")];
				disconnect($con);
				die;
			}

			$duplicate_with_same_system = sql_select("select a.mst_id, a.barcode_no, b.transfer_system_id, a.re_transfer, a.entry_form from pro_roll_details a, inv_item_transfer_mst b where a.mst_id=b.id and a.entry_form = 134 and a.barcode_no in (".implode(',', $new_inserted).") and a.mst_id=$update_id and a.status_active=1 and a.is_deleted=0");
			//505

			if($duplicate_with_same_system[0][csf("barcode_no")] != ""){
				echo "20**Sorry duplicate barcode not allowed in same system id.\nbarcode no: ". $duplicate_with_same_system[0][csf("barcode_no")];
				disconnect($con);
				die;
			}

		}

		// if($all_dtls_ids_deleted=="")
		// {
		// 	$update_all_dtls_id_deleted=chop($all_dtls_id.',');
		// }
		// else
		// {
		// 	if($all_dtls_id !="")
		// 	{
		// 		$update_all_dtls_id_deleted=$all_dtls_id.','.$all_dtls_ids_deleted;
		// 	}
		// 	else
		// 	{
		// 		$update_all_dtls_id_deleted=$all_dtls_id.$all_dtls_ids_deleted;
		// 	}
			
		// }
		$all_dtls_id=chop($all_dtls_id,",");

		//$rollUpdate=bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $roll_id_array );
		//echo "10**$data_array_dtls";die;
		//echo "10**insert into inv_item_transfer_dtls ($field_array_dtls) values $data_array_dtls";die;
		$rID=$rID2=$rID3=$rID4=$rID5=$rID6=$prodUpdate=$propo_data_upd=$dtls_data_upd=$roll_data_upd=$trans_data_upd=$deleted_dtls_data_upd=$deleted_roll_data_upd=$rollUpdate=$deleted_transaction_data_upd=$deleted_propo_data_upd=$rID7_roll_re_transfer=$source_roll_re_transfer= $batchMstUpdate = $rID8 = $delete_batch_dtls = $batchDtls=$rID7=true;
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
				// echo "10** insert into inv_item_transfer_dtls ($field_array_dtls) values $data_array_dtls";die;
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
				//echo "10**".bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_up, $data_array_dtls_up, $dtls_id_array_up );die;
				$dtls_data_upd=execute_query(bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_up, $data_array_dtls_up, $dtls_id_array_up ));
			}

			if(!empty($data_array_roll_up))
			{
				$roll_data_upd=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_up, $data_array_roll_up, $roll_id_array_up ));
			}

			if(!empty($data_array_transaction_up))
			{
				//echo "10**".bulk_update_sql_statement( "inv_transaction", "id", $field_array_trans_update, $data_array_transaction_up, $prop_id_array_up );die;
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
				//echo "10**insert into inv_item_transfer_dtls ($field_array_dtls) values $data_array_dtls";die;
				$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
			}
			if($data_array_roll!="")
			{
				//echo "10**insert into pro_roll_details ($field_array_roll) values $data_array_roll"; oci_rollback($con);die;
				$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			}
			if($data_array_prop!="")
			{
				//echo "10**insert into order_wise_pro_details ($field_array_prop) values $data_array_prop"; oci_rollback($con);die;
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

		// if(!empty($new_inserted))
		// {
		// 	$rID7=execute_query("update pro_roll_details set is_returned=1 where barcode_no in (". implode(',', $new_inserted).") and id  not in (".implode(',', $inserted_roll_id_arr).")");
		// 	if ($flag == 1)
		// 	{
		// 		if ($rID7)
		// 			$flag = 1;
		// 		else
		// 			$flag = 0;
		// 	}
		// }

		if(str_replace("'","",$cbo_transfer_criteria)==1 || str_replace("'","",$cbo_transfer_criteria)==4)
		{
			
			if(count($data_array_batch_update)>0)
			{
				//echo "10**"; echo bulk_update_sql_statement("pro_batch_create_mst","id",$field_array_batch_update,$data_array_batch_update,$update_batch_id);oci_rollback($con);die;
				$batchMstUpdate=execute_query(bulk_update_sql_statement("pro_batch_create_mst","id",$field_array_batch_update,$data_array_batch_update,$update_batch_id));
				if($flag==1)
				{
					if($batchMstUpdate) $flag=1; else $flag=0;
				}
			}

			//echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;oci_rollback($con);die;

			if(count($data_array_batch)>0)
			{
				//echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;oci_rollback($con);die;
				$rID8=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
				if($flag==1)
				{
					if($rID8) $flag=1; else $flag=0;
				}
			}
			
			//echo "20**all_dtlsId:: ".$all_dtlsId;die;
			
			if(!empty($all_dtlsId))
			{
				//echo "20**delete from pro_batch_create_dtls where mst_id in($all_previousToBbatchId) and dtls_id in($all_dtlsId)";die;
				$delete_batch_dtls=execute_query( "delete from pro_batch_create_dtls where mst_id in($all_previousToBbatchId) and dtls_id in($all_dtlsId)",0);
				if($flag==1)
				{
					if($delete_batch_dtls) $flag=1; else $flag=0;
				}
			}

			//echo "20**".$data_array_batch_dtls;die;

			if($data_array_batch_dtls!="")
			{
				//echo "6**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;
				$batchDtls=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
				if($flag==1)
				{
					if($batchDtls) $flag=1; else $flag=0;
				}
			}

		}

		

		//echo "10**".bulk_update_sql_statement( "inv_transaction", "id", $field_array_trans_update, $data_array_transaction_up, $prop_id_array_up );
		//oci_rollback($con); die;

		//10**1 && 1 && 1 && 1 && 1 && 1 && 1 && 1 && 1 && 1 && 1 && 1 && 1 && 1 && 1 && 1 && 1 && 1 && && && 0 && 0##

		// echo "10**$rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate && $propo_data_upd && $dtls_data_upd && $roll_data_upd && $trans_data_upd && $deleted_dtls_data_upd && $deleted_roll_data_upd && $deleted_propo_data_upd && $deleted_transaction_data_upd && $rollUpdate && $rID7_roll_re_transfer && $source_roll_re_transfer && $batchMstUpdate && $rID8 && $delete_batch_dtls && $batchDtls##$txt_deleted_source_roll_id";oci_rollback($con); die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate && $propo_data_upd && $dtls_data_upd && $roll_data_upd && $trans_data_upd && $deleted_dtls_data_upd && $deleted_roll_data_upd && $deleted_propo_data_upd && $deleted_transaction_data_upd && $rollUpdate && $rID7_roll_re_transfer && $source_roll_re_transfer && $batchMstUpdate && $rID8 && $delete_batch_dtls && $batchDtls && $rID7)
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
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate && $propo_data_upd && $dtls_data_upd && $roll_data_upd && $deleted_dtls_data_upd && $deleted_roll_data_upd && $deleted_propo_data_upd && $deleted_transaction_data_upd && $rollUpdate && $rID7_roll_re_transfer && $source_roll_re_transfer && $batchMstUpdate && $rID8 && $delete_batch_dtls && $batchDtls && $rID7)
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
	

}
if($action=="populate_barcode_data_from_requisition")
{
	$data=explode("**",$data);
	//var_dump($data);die;
	$bar_code=$data[0];
	$sys_id=$data[1];
	$transfer_criteria=$data[2];
	$cbo_store_id=$data[3];
	$from_store_id=$data[4];
	$requisition_sys_id=$data[5];

	$requisiton_data = sql_select("SELECT a.id,a.entry_form, a.company_id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.to_company, a.transfer_date, a.challan_no, b.id as roll_upid, b.id as dtls_id, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.to_prod_id, b.from_prod_id,b.barcode_no, max(b.to_store) as to_store, max(b.from_store) as from_store,b.from_order_id,b.to_order_id,b.body_part_id,b.to_body_part,b.batch_id,b.from_booking_without_order, b.roll_id,b.to_booking_no,b.to_booking_id from  inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
	where a.id=b.mst_id and a.entry_form=506 and a.requisition_status=1 and a.status_active=1 and a.is_deleted=0 and a.is_approved=1 and a.transfer_criteria=$transfer_criteria and a.id=$requisition_sys_id and b.barcode_no in($bar_code) and b.to_store=$cbo_store_id and b.from_store=$from_store_id 
	group by a.id,a.entry_form, a.company_id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.to_company, a.transfer_date, a.challan_no, b.id, b.id, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.to_prod_id, b.from_prod_id,b.barcode_no,b.from_order_id,b.to_order_id,b.body_part_id,b.to_body_part,b.batch_id,b.from_booking_without_order, b.roll_id,b.to_booking_no,b.to_booking_id  order by id");
	foreach($requisiton_data as $row)
	{
		$requisition_barcode.=$row[csf('barcode_no')].",";
		//$requisiton_data_array[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		$requisiton_data_array[$row[csf('barcode_no')]]['roll_upid']=$row[csf('roll_upid')];
		$requisiton_data_array[$row[csf('barcode_no')]]['dtls_id']=$row[csf('dtls_id')];
		$requisiton_data_array[$row[csf('barcode_no')]]['trans_id']=$row[csf('trans_id')];
		$requisiton_data_array[$row[csf('barcode_no')]]['to_trans_id']=$row[csf('to_trans_id')];
		$requisiton_data_array[$row[csf('barcode_no')]]['from_trans_entry_form']=$row[csf('from_trans_entry_form')];
		$requisiton_data_array[$row[csf('barcode_no')]]['to_prod_id']=$row[csf('to_prod_id')];
		$requisiton_data_array[$row[csf('barcode_no')]]['from_prod_id']=$row[csf('from_prod_id')];
		$requisiton_data_array[$row[csf('barcode_no')]]['company_id']=$row[csf('company_id')];
		$requisiton_data_array[$row[csf('barcode_no')]]['from_booking_without_order']=$row[csf('from_booking_without_order')];
		if($row[csf('from_booking_without_order')] == 1)
		{
			$non_order_booking_buyer_po_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
		}
		else
		{
			$po_arr_book_booking_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
			$po_arr_book_booking_arr[$row[csf('to_order_id')]] = $row[csf('to_order_id')];
		}
		$requisiton_data_array[$row[csf('barcode_no')]]['from_store']=$row[csf('from_store')];
		$requisiton_data_array[$row[csf('barcode_no')]]['to_store'] = $row[csf('to_store')];
		$requisiton_data_array[$row[csf('barcode_no')]]['from_order_id'] = $row[csf('from_order_id')];
		$requisiton_data_array[$row[csf('barcode_no')]]['to_order_id'] = $row[csf('to_order_id')];
		$requisiton_data_array[$row[csf('barcode_no')]]['body_part_id'] = $row[csf('body_part_id')];
		$requisiton_data_array[$row[csf('barcode_no')]]['to_body_part'] = $row[csf('to_body_part')];
		$requisiton_data_array[$row[csf('barcode_no')]]['batch_id'] = $row[csf('batch_id')];
		$requisiton_data_array[$row[csf('barcode_no')]]['to_booking_no'] = $row[csf('to_booking_no')];
		$requisiton_data_array[$row[csf('barcode_no')]]['to_booking_id'] = $row[csf('to_booking_id')];
	}
	$requisition_barcode=chop($requisition_barcode,",");

	$batch_arr=return_library_array( "select id,batch_no from  pro_batch_create_mst",'id','batch_no');
	
	$issue_roll_mst_arr=return_library_array( "select a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b  where a.mst_id=b.id and a.entry_form=71 and a.barcode_no in($requisition_barcode)",'barcode_no','issue_number');

	$scanned_barcode_issue_data=sql_select("select a.id, a.barcode_no,a.entry_form, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and b.entry_form = 71 and a.entry_form =71 and a.is_returned=0 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in ($requisition_barcode)");

	foreach($scanned_barcode_issue_data as $row)
	{
		$scanned_barcode_issue_array[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		$scanned_barcode_entry_form_array[$row[csf('barcode_no')]]=$row[csf('entry_form')];
		$issue_roll_mst_arr[$row[csf('barcode_no')]] = $row[csf('issue_number')];
	}

	$scanned_barcode_update_data=sql_select("SELECT a.id as roll_upid, a.po_breakdown_id, a.barcode_no, a.roll_id, a.dtls_id, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.to_store, b.to_prod_id, b.from_prod_id from pro_roll_details a, inv_item_transfer_dtls b  where a.dtls_id=b.id and a.entry_form =134 and a.status_active=1 and a.is_deleted=0 and a.mst_id='$sys_id'"); //505

	if($sys_id != "")
	{
		$scanned_barcode_update_data=sql_select("SELECT  a.barcode_no, a.roll_id, c.transfer_system_id, a.entry_form from pro_roll_details a, inv_item_transfer_dtls b, inv_item_transfer_mst c where a.dtls_id=b.id and b.mst_id = c.id and a.mst_id = c.id and c.entry_form = 134 and a.entry_form =134 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$sys_id"); //505
		foreach($scanned_barcode_update_data as $row)
		{
			$scanned_barcode_issue_array[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
			$scanned_barcode_entry_form_array[$row[csf('barcode_no')]]=$row[csf('entry_form')];
			$transfer_roll_mst_arr[$row[csf('barcode_no')]] = $row[csf('transfer_system_id')];
		}
	}

	$order_to_order_trans_sql=sql_select("SELECT a.id, a.barcode_no, a.po_breakdown_id,a.entry_form,a.booking_without_order from pro_roll_details a where a.entry_form =68 and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0 and a.booking_without_order=0 and a.barcode_no in($requisition_barcode)");

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
	// echo "select a.company_id,a.to_company, a.transfer_criteria, to_prod_id as prod_id, c.barcode_no, c.entry_form, b.to_store, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.to_body_part as body_part_id,c.id,b.to_ord_book_no,b.to_ord_book_id,b.to_order_id
	// from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	// where a.id = b.mst_id and a.entry_form in (134,214,216,219) and b.id=c.dtls_id and c.entry_form in(134,214,216,219)
	// and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.barcode_no in($requisition_barcode)
	// order by c.barcode_no desc";die;
	$trans_store_sql=sql_select("select a.company_id,a.to_company, a.transfer_criteria, to_prod_id as prod_id, c.barcode_no, c.entry_form, b.to_store, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.to_body_part as body_part_id,c.id,b.to_ord_book_no,b.to_ord_book_id,b.to_order_id
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	where a.id = b.mst_id and a.entry_form in (134,214,216,219) and b.id=c.dtls_id and c.entry_form in(134,214,216,219)
	and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.barcode_no in($requisition_barcode)
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
		$order_to_order_trans_data[$row[csf("barcode_no")]]["roll_table_id"]=$row[csf("id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=$row[csf("barcode_no")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["entry_form"]=$row[csf("entry_form")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_ord_book_no"]=$row[csf("to_ord_book_no")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_ord_book_id"]=$row[csf("to_ord_book_id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"]=$row[csf("to_order_id")];



		$store_ids .=$row[csf("to_store")].",";
	}
	unset($trans_store_sql);

	//$issue_return_sql=sql_select("select b.company_id, a.id, a.barcode_no, a.po_breakdown_id,a.entry_form,a.booking_without_order, b.store_id, c.floor_id, c.room, c.rack, c.self, c.bin_box, c.prod_id, c.body_part_id from pro_roll_details a, inv_receive_master b, pro_grey_prod_entry_dtls c where a.mst_id=b.id and a.dtls_id=c.id and b.id=c.mst_id and a.entry_form in(126) and b.entry_form in(126) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.re_transfer=0 and a.barcode_no in ($requisition_barcode)");

	$issue_return_sql=sql_select("select b.company_id, a.id, a.barcode_no, a.po_breakdown_id,a.entry_form,a.booking_without_order, b.store_id, c.floor as floor_id, c.room, c.rack_no as rack, c.shelf_no as self, c.bin as bin_box, c.prod_id, c.body_part_id from pro_roll_details a, inv_receive_master b, pro_finish_fabric_rcv_dtls c where a.mst_id=b.id and a.dtls_id=c.id and b.id=c.mst_id and a.entry_form in(126) and b.entry_form in(126) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.re_transfer=0 and a.barcode_no in ($requisition_barcode)");

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

	//============================================store check 01-11-2021 start==========================
	if($requisition_barcode!="")
	{
		$barcode_cond=" and c.barcode_no in ($requisition_barcode)";
	}

	if($transfer_criteria == 1 || $transfer_criteria == 2)
	{
		if($cbo_store_id) {
			$without_store_cond_1=" and a.store_id!=$cbo_store_id";
			$without_store_cond_2=" and b.to_store!=$cbo_store_id";
		}
	}

	$sql="SELECT a.recv_number, a.location_id, b.prod_id,b.fabric_description_id as fabric_description_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, 
	d.file_no, d.grouping, a.entry_form,a.store_id as to_store, b.body_part_id 
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and a.entry_form in(68,126) and c.entry_form in(68,126) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.re_transfer=0 and a.store_id=$from_store_id $barcode_cond $without_store_cond_1 $store_cond_1  and c.booking_without_order=0
	union all
	select a.transfer_system_id as recv_number, null as location_id, from_prod_id as prod_id,b.feb_description_id as fabric_description_id, c.barcode_no, c.roll_no, c.qnty,c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,b.to_store, b.to_body_part as body_part_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_po_break_down d
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.entry_form in(134,219) and c.entry_form in(134,219) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.booking_without_order=0 and b.to_store=$from_store_id $barcode_cond $without_store_cond_2";
	
	//echo $sql;
	$result = sql_select($sql);
	if(empty($result))
	{
		echo "30**barcode not found";
		die;
	}
	//=================================================end================================================

	$sql_data="SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.fabric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id,max(b.batch_id) as batch_id, max(b.floor) as floor, max(b.room) as room, max(b.rack_no) as rack, max(b.shelf_no) as self, max(b.bin) as bin_box, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id as roll_mst_id
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form = 68 and c.entry_form = 68 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($requisition_barcode)
	group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id";

	//echo $sql_data;die;
	$data_array = sql_select($sql_data);

	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			if($transfer_criteria==1)
			{
				$po_id=$requisiton_data_array[$row[csf('barcode_no')]]['from_order_id'];
				$po_idTo=$requisiton_data_array[$row[csf('barcode_no')]]['to_order_id'];
			}
			else
			{
				if($order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=="")
				{
					$po_id=$row[csf("po_breakdown_id")];
				}
				else
				{
					$po_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"];
				}
			}


			if($row[csf("booking_without_order")]==0)
			{
				$po_arr_book_booking_arr[$po_id] = $po_id;
				$po_arr_book_booking_arr_to[$po_idTo] = $po_idTo;

			}

			if($order_to_order_trans_data[$row[csf("barcode_no")]]["booking_without_order"] == 0)
			{
				
				$po_arr_book_booking_arr[$po_id] = $po_id;
				$po_arr_book_booking_arr_to[$po_idTo] = $po_idTo;
			}

			$color_id_ref_arr[$row[csf("color_id")]] = $row[csf("color_id")];

			$company_ids .= $row[csf("company_id")].",";
			//$store_ids .= $row[csf("store_id")].",";
			$store_ids .= $requisiton_data_array[$row[csf("barcode_no")]]['from_store'].",";
			$febric_description_ids .= $row[csf("febric_description_id")].",";
		}

		$company_ids = chop($company_ids,",");
		$store_ids = chop($store_ids,",");
		$febric_description_ids = chop($febric_description_ids,",");
		//var_dump($company_ids);

		
		$production_basis_sql = sql_select("select a.barcode_no, b.receive_basis, a.booking_without_order, b.booking_id  from pro_roll_details a, inv_receive_master b where a.mst_id = b.id and b.entry_form = 2 and a.entry_form = 2 and a.status_active =1 and a.barcode_no in ($requisition_barcode)");
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

		$composition_arr=array();
		$constructtion_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and a.id in ($febric_description_ids)";
		//echo $sql_deter;
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
	$po_arr_book_booking_arr_to = array_filter($po_arr_book_booking_arr_to);
	if(count($po_arr_book_booking_arr_to) >0 )
	{
		$po_arr_book_booking_arr=$po_arr_book_booking_arr+$po_arr_book_booking_arr_to;
		//$po_to_cond="and b.id in (".implode(",", $po_arr_book_booking_arr_to).")";
	}
	$po_arr_book_booking_arr = array_filter($po_arr_book_booking_arr);
	if(count($po_arr_book_booking_arr) >0 )
	{
		$book_booking_arr=return_library_array("select po_break_down_id, booking_no from wo_booking_dtls where booking_type=1 and po_break_down_id in (".implode(",", $po_arr_book_booking_arr).") ",'po_break_down_id','booking_no');
		$book_booking_id_arr=return_library_array("select po_break_down_id, booking_mst_id from wo_booking_dtls where booking_type=1 and po_break_down_id in (".implode(",", $po_arr_book_booking_arr).") ",'po_break_down_id','booking_mst_id');

	
		$sql_del_arr = "SELECT a.id, a.sys_number_prefix_num, a.sys_number,b.barcode_num, c.batch_no, c.id as batch_id
		from pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b, pro_batch_create_mst c
		where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=67 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.order_id in (".implode(",", $po_arr_book_booking_arr).") group by a.id, a.sys_number_prefix_num, a.sys_number,b.barcode_num,c.id, c.batch_no order by a.id";

		//echo $sql_del_arr;die;

		$sql_del_data=sql_select($sql_del_arr);
		
		$roll_delivery_challan=array();
		foreach($sql_del_data as $row)
		{
			$roll_delivery_challan[$row[csf("barcode_num")]][$row[csf("batch_id")]]['roll_delivery_challan']=$row[csf("sys_number")];	
			$roll_delivery_challan[$row[csf("barcode_num")]][$row[csf("batch_id")]]['roll_delivery_challan_id']=$row[csf("id")];	
		}
		unset($sql_del_data);
	
		$po_arr_book_booking_arr_to = array_filter($po_arr_book_booking_arr_to);
		if(count($po_arr_book_booking_arr_to) >0 )
		{
			$po_arr_book_booking_arr=$po_arr_book_booking_arr+$po_arr_book_booking_arr_to;
			//$po_to_cond="and b.id in (".implode(",", $po_arr_book_booking_arr_to).")";
		}
		$po_ref_data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.grouping FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and a.status_active =1 and b.status_active=1 and b.id in (".implode(",", $po_arr_book_booking_arr).")");

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
	//echo $lib_room_rack_shelf_sql;die;
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

				// if($order_to_order_trans_data[$row[csf("barcode_no")]]["to_store"])
				// {
				// 	$to_store = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_store"];
				// }
				// else
				// {
				// 	$to_store = $row[csf("store_id")];
				// }

				if($order_to_order_trans_data[$row[csf("barcode_no")]]["body_part_id"])
				{
					$body_part_id = $order_to_order_trans_data[$row[csf("barcode_no")]]["body_part_id"];
				}
				else
				{
					$body_part_id = $row[csf("body_part_id")];
				}

				
				if($row[csf("booking_without_order")]==0)
				{
					
					if($order_to_order_trans_data[$row[csf("barcode_no")]]["to_ord_book_no"] !="")
					{
						
						$booking_no_fab=$order_to_order_trans_data[$row[csf("barcode_no")]]["to_ord_book_no"];
						
					}
					else
					{
						$booking_no_fab=$book_booking_arr[$row[csf("po_breakdown_id")]];
					
					}
					$roll_delivery_challan_no = $roll_delivery_challan[$row[csf("barcode_no")]][$row[csf("batch_id")]]['roll_delivery_challan'];
					$roll_delivery_challan_id = $roll_delivery_challan[$row[csf("barcode_no")]][$row[csf("batch_id")]]['roll_delivery_challan_id'];
					
				}

				if($entry_form == 135 || $entry_form == 214 || $entry_form == 216 || $entry_form == 134)
				{
					$booking_no_fab = $booking_no_fab;
					if($booking_without_order == 0)
					{
						$buyer_name=$buyer_arr[$po_details_array[$po_id]['buyer_name']];
						
					}
					
				}
				else
				{
					if($row[csf("booking_without_order")]==0)
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
				$body_part_id=$requisiton_data_array[$row[csf('barcode_no')]]['body_part_id'];
				$batch_id=$requisiton_data_array[$row[csf('barcode_no')]]['batch_id'];
				$po_id=$requisiton_data_array[$row[csf('barcode_no')]]['from_order_id'];
				$po_id_to=$requisiton_data_array[$row[csf('barcode_no')]]['to_order_id'];
				$po_id_to_number=$po_details_array[$po_id_to]['po_number'];
				$to_store=$requisiton_data_array[$row[csf('barcode_no')]]['to_store'];
				$from_store=$requisiton_data_array[$row[csf('barcode_no')]]['from_store'];
				$buyer_name=$buyer_arr[$po_details_array[$po_id]['buyer_name']];
				$booking_no_fab=$book_booking_arr[$po_id];
				$booking_id_fab=$book_booking_id_arr[$po_id];
				$to_body_part=$requisiton_data_array[$row[csf('barcode_no')]]['to_body_part'];
				$to_job=$po_details_array[$po_id_to]['job_no'];
				if($transfer_criteria==1 || $transfer_criteria==4)
				{
					$to_booking_no_fab=$requisiton_data_array[$row[csf('barcode_no')]]['to_booking_no'];
					$to_booking_id_fab=$requisiton_data_array[$row[csf('barcode_no')]]['to_booking_id'];
				}
				else
				{
					$to_booking_no_fab=$booking_no_fab;
					$to_booking_id_fab=$booking_id_fab;
				}
				//echo "$to_store string";

				$barcodeData .=$row[csf('id')]."**".$barcode_company_id."**".$body_part[$body_part_id ]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$roll_delivery_challan_no."**".$roll_delivery_challan_id."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$to_store."**".$row[csf("knitting_company")]."**".$batch_arr[$batch_id]."**".$batch_id."**".$row[csf("stitch_length")]."**".$to_rack."**".$to_self."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$to_prod_id."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$po_id."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$po_id]['job_no']."**".$po_details_array[$po_id]['buyer_name']."**".$buyer_name."**".$po_details_array[$po_id]['po_number']."**".$row[csf("color_id")]."**".$store_arr[$from_store]."**".$body_part_id ."**".$row[csf("brand_id")]."**".$row[csf("machine_no_id")]."**".$entry_form."**".$row[csf("booking_without_order")]."**".$roll_mst_id."**".$booking_no_fab."**".$row[csf("amount")]."**".$booking_without_order."**".$to_floor_id."**".$to_room."**".$po_details_array[$po_id]['grouping']."**".$to_bin_box."**".$multi_floor."**".$multi_room."**".$multi_rack."**".$multi_self."**".$multi_bin."**".$po_id_to."**".$po_id_to_number."**".$to_body_part."**".$to_job."**".$to_booking_no_fab."**".$to_booking_id_fab."__";

			}
			else
			{
				if($scanned_barcode_entry_form_array[$row[csf('barcode_no')]]==134) //505
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
if($action=="populate_barcode_data")
{
	$data=explode("**",$data);
	//var_dump($data);die;
	$bar_code=$data[0];
	$sys_id=$data[1];
	$transfer_criteria=$data[2];
	$cbo_store_id=$data[3];
	$from_store_id=$data[4];

	$batch_arr=return_library_array( "select id,batch_no from  pro_batch_create_mst",'id','batch_no');
	
	$issue_roll_mst_arr=return_library_array( "select a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b  where a.mst_id=b.id and a.entry_form=71 and a.barcode_no in($bar_code)",'barcode_no','issue_number');

	$scanned_barcode_issue_data=sql_select("select a.id, a.barcode_no,a.entry_form, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and b.entry_form = 71 and a.entry_form =71 and a.is_returned=0 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in ($bar_code)");

	foreach($scanned_barcode_issue_data as $row)
	{
		$scanned_barcode_issue_array[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		$scanned_barcode_entry_form_array[$row[csf('barcode_no')]]=$row[csf('entry_form')];
		$issue_roll_mst_arr[$row[csf('barcode_no')]] = $row[csf('issue_number')];
	}

	$scanned_barcode_update_data=sql_select("SELECT a.id as roll_upid, a.po_breakdown_id, a.barcode_no, a.roll_id, a.dtls_id, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.to_store, b.to_prod_id, b.from_prod_id from pro_roll_details a, inv_item_transfer_dtls b  where a.dtls_id=b.id and a.entry_form =134 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$sys_id"); //505

	if($sys_id != "")
	{
		$scanned_barcode_update_data=sql_select("SELECT  a.barcode_no, a.roll_id, c.transfer_system_id, a.entry_form from pro_roll_details a, inv_item_transfer_dtls b, inv_item_transfer_mst c where a.dtls_id=b.id and b.mst_id = c.id and a.mst_id = c.id and c.entry_form = 134 and a.entry_form =134 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$sys_id"); //505
		foreach($scanned_barcode_update_data as $row)
		{
			$scanned_barcode_issue_array[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
			$scanned_barcode_entry_form_array[$row[csf('barcode_no')]]=$row[csf('entry_form')];
			$transfer_roll_mst_arr[$row[csf('barcode_no')]] = $row[csf('transfer_system_id')];
		}
	}

	$order_to_order_trans_sql=sql_select("SELECT a.id, a.barcode_no, a.po_breakdown_id,a.entry_form,a.booking_without_order from pro_roll_details a where a.entry_form =68 and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0 and a.booking_without_order=0 and a.barcode_no in($bar_code)");

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
	// echo "select a.company_id,a.to_company, a.transfer_criteria, to_prod_id as prod_id, c.barcode_no, c.entry_form, b.to_store, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.to_body_part as body_part_id,c.id,b.to_ord_book_no,b.to_ord_book_id,b.to_order_id
	// from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	// where a.id = b.mst_id and a.entry_form in (134,214,216,219) and b.id=c.dtls_id and c.entry_form in(134,214,216,219)
	// and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.barcode_no in($bar_code)
	// order by c.barcode_no desc";die;
	$trans_store_sql=sql_select("select a.company_id,a.to_company, a.transfer_criteria, to_prod_id as prod_id, c.barcode_no, c.entry_form, b.to_store, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.to_body_part as body_part_id,c.id,b.to_ord_book_no,b.to_ord_book_id,b.to_order_id
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	where a.id = b.mst_id and a.entry_form in (134,214,216,219) and b.id=c.dtls_id and c.entry_form in(134,214,216,219)
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
		$order_to_order_trans_data[$row[csf("barcode_no")]]["roll_table_id"]=$row[csf("id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=$row[csf("barcode_no")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["entry_form"]=$row[csf("entry_form")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_ord_book_no"]=$row[csf("to_ord_book_no")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_ord_book_id"]=$row[csf("to_ord_book_id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"]=$row[csf("to_order_id")];



		$store_ids .=$row[csf("to_store")].",";
	}
	unset($trans_store_sql);

	//$issue_return_sql=sql_select("select b.company_id, a.id, a.barcode_no, a.po_breakdown_id,a.entry_form,a.booking_without_order, b.store_id, c.floor_id, c.room, c.rack, c.self, c.bin_box, c.prod_id, c.body_part_id from pro_roll_details a, inv_receive_master b, pro_grey_prod_entry_dtls c where a.mst_id=b.id and a.dtls_id=c.id and b.id=c.mst_id and a.entry_form in(126) and b.entry_form in(126) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.re_transfer=0 and a.barcode_no in ($bar_code)");

	$issue_return_sql=sql_select("SELECT b.company_id, a.id, a.barcode_no, a.po_breakdown_id,a.entry_form,a.booking_without_order, 
	b.store_id, c.floor as floor_id, c.room, c.rack_no as rack, c.shelf_no as self, c.bin as bin_box,c.prod_id, c.body_part_id 
	from pro_roll_details a, inv_receive_master b, pro_finish_fabric_rcv_dtls c where a.mst_id=b.id and a.dtls_id=c.id and b.id=c.mst_id 
	and a.entry_form in(126) and b.entry_form in(126) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.re_transfer=0 and a.barcode_no in($bar_code)");
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

	//============================================store check 01-11-2021 start==========================
	if($bar_code!="")
	{
		$barcode_cond=" and c.barcode_no in ($bar_code)";
	}

	if($transfer_criteria == 1 || $transfer_criteria == 2)
	{
		if($cbo_store_id) {
			$without_store_cond_1=" and a.store_id!=$cbo_store_id";
			$without_store_cond_2=" and b.to_store!=$cbo_store_id";
		}
	}

	$sql="SELECT a.recv_number, a.location_id, b.prod_id,b.fabric_description_id as fabric_description_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, 
	d.file_no, d.grouping, a.entry_form,a.store_id as to_store, b.body_part_id 
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and a.entry_form in(68,126) and c.entry_form in(68,126) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.re_transfer=0 and a.store_id=$from_store_id $barcode_cond $without_store_cond_1 $store_cond_1  and c.booking_without_order=0
	union all
	select a.transfer_system_id as recv_number, null as location_id, from_prod_id as prod_id,b.feb_description_id as fabric_description_id, c.barcode_no, c.roll_no, c.qnty,c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,b.to_store, b.to_body_part as body_part_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_po_break_down d
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.entry_form in(134,219) and c.entry_form in(134,219) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.booking_without_order=0 and b.to_store=$from_store_id $barcode_cond $without_store_cond_2";
	
	//echo $sql;
	$result = sql_select($sql);
	if(empty($result))
	{
		echo "30**barcode not found";
		die;
	}
	//=================================================end================================================


	$sql_data="SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.fabric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id,max(b.batch_id) as batch_id, max(b.floor) as floor, max(b.room) as room, max(b.rack_no) as rack, max(b.shelf_no) as self, max(b.bin) as bin_box, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id as roll_mst_id
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form = 68 and c.entry_form = 68 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($bar_code)
	group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id";


	//echo $sql_data;die;
	$data_array = sql_select($sql_data);

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


			if($row[csf("booking_without_order")]==0)
			{
				$po_arr_book_booking_arr[$po_id] = $po_id;

			}

			if($order_to_order_trans_data[$row[csf("barcode_no")]]["booking_without_order"] == 0)
			{
				
				$po_arr_book_booking_arr[$po_id] = $po_id;
			}

			$color_id_ref_arr[$row[csf("color_id")]] = $row[csf("color_id")];

			$company_ids .= $row[csf("company_id")].",";
			$store_ids .= $row[csf("store_id")].",";
			$febric_description_ids .= $row[csf("febric_description_id")].",";
		}

		$company_ids = chop($company_ids,",");
		$store_ids = chop($store_ids,",");
		$febric_description_ids = chop($febric_description_ids,",");
		//var_dump($company_ids);

		
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

		$company_name_array=return_library_array( "select id, company_name from  lib_company where status_active=1 and is_deleted=0 and id in($company_ids)",'id','company_name');
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0",'id','short_name');
		$store_arr=return_library_array( "select id, store_name from lib_store_location where status_active=1 and is_deleted=0 and id in($store_ids)",'id','store_name');

		$composition_arr=array();
		$constructtion_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and a.id in ($febric_description_ids)";
		//echo $sql_deter;
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
		$book_booking_arr=return_library_array("select po_break_down_id, booking_no from wo_booking_dtls where booking_type=1 and po_break_down_id in (".implode(",", $po_arr_book_booking_arr).") ",'po_break_down_id','booking_no');

	
		$sql_del_arr = "SELECT a.id, a.sys_number_prefix_num, a.sys_number,b.barcode_num, c.batch_no, c.id as batch_id
		from pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b, pro_batch_create_mst c
		where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=67 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.order_id in (".implode(",", $po_arr_book_booking_arr).") group by a.id, a.sys_number_prefix_num, a.sys_number,b.barcode_num,c.id, c.batch_no order by a.id";

		//echo $sql_del_arr;die;

		$sql_del_data=sql_select($sql_del_arr);
		
		$roll_delivery_challan=array();
		foreach($sql_del_data as $row)
		{
			$roll_delivery_challan[$row[csf("barcode_num")]][$row[csf("batch_id")]]['roll_delivery_challan']=$row[csf("sys_number")];	
			$roll_delivery_challan[$row[csf("barcode_num")]][$row[csf("batch_id")]]['roll_delivery_challan_id']=$row[csf("id")];	
		}
		unset($sql_del_data);
	
		
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
	//echo $lib_room_rack_shelf_sql;die;
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

				// if($order_to_order_trans_data[$row[csf("barcode_no")]]["to_store"])
				// {
				// 	$to_store = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_store"];
				// }
				// else
				// {
				// 	$to_store = $row[csf("store_id")];
				// }

				if($order_to_order_trans_data[$row[csf("barcode_no")]]["body_part_id"])
				{
					$body_part_id = $order_to_order_trans_data[$row[csf("barcode_no")]]["body_part_id"];
				}
				else
				{
					$body_part_id = $row[csf("body_part_id")];
				}

				
				if($row[csf("booking_without_order")]==0)
				{
					
					if($order_to_order_trans_data[$row[csf("barcode_no")]]["to_ord_book_no"] !="")
					{
						
						$booking_no_fab=$order_to_order_trans_data[$row[csf("barcode_no")]]["to_ord_book_no"];
						
					}
					else
					{
						$booking_no_fab=$book_booking_arr[$row[csf("po_breakdown_id")]];
					
					}
					$roll_delivery_challan_no = $roll_delivery_challan[$row[csf("barcode_no")]][$row[csf("batch_id")]]['roll_delivery_challan'];
					$roll_delivery_challan_id = $roll_delivery_challan[$row[csf("barcode_no")]][$row[csf("batch_id")]]['roll_delivery_challan_id'];
					
				}

				if($entry_form == 135 || $entry_form == 214 || $entry_form == 216 || $entry_form == 134)
				{
					$booking_no_fab = $booking_no_fab;
					if($booking_without_order == 0)
					{
						$buyer_name=$buyer_arr[$po_details_array[$po_id]['buyer_name']];
						
					}
					
				}
				else
				{
					if($row[csf("booking_without_order")]==0)
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
				

				$barcodeData .=$row[csf('id')]."**".$barcode_company_id."**".$body_part[$body_part_id ]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$roll_delivery_challan_no."**".$roll_delivery_challan_id."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$to_store."**".$row[csf("knitting_company")]."**".$batch_arr[$row[csf("batch_id")]]."**".$row[csf("batch_id")]."**".$row[csf("stitch_length")]."**".$to_rack."**".$to_self."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$to_prod_id."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$po_id."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$po_id]['job_no']."**".$po_details_array[$po_id]['buyer_name']."**".$buyer_name."**".$po_details_array[$po_id]['po_number']."**".$row[csf("color_id")]."**".$store_arr[$to_store]."**".$body_part_id ."**".$row[csf("brand_id")]."**".$row[csf("machine_no_id")]."**".$entry_form."**".$row[csf("booking_without_order")]."**".$roll_mst_id."**".$booking_no_fab."**".$row[csf("amount")]."**".$booking_without_order."**".$to_floor_id."**".$to_room."**".$po_details_array[$po_id]['grouping']."**".$to_bin_box."**".$multi_floor."**".$multi_room."**".$multi_rack."**".$multi_self."**".$multi_bin."__";

			}
			else
			{
				if($scanned_barcode_entry_form_array[$row[csf('barcode_no')]]==134) //505
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
	$batch_arr=return_library_array( "select id,batch_no from  pro_batch_create_mst",'id','batch_no');


	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array);

	
	$scanned_barcode_update_data=sql_select("SELECT c.company_id, a.id as roll_upid, a.po_breakdown_id, a.barcode_no, a.roll_id, a.dtls_id, a.from_roll_id, a.re_transfer, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.from_store, b.to_store, b.to_prod_id, b.from_prod_id, b.from_order_id, b.from_booking_without_order, b.transfer_requ_dtls_id, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf as to_shelf, b.to_bin_box, b.batch_id,b.to_batch_id,b.body_part_id,b.to_body_part,b.transfer_qnty,b.to_ord_book_id,b.to_ord_book_no,c.to_company
	from pro_roll_details a, inv_item_transfer_dtls b , inv_item_transfer_mst c
	where a.dtls_id=b.id and b.mst_id=c.id and c.entry_form=134 and a.entry_form =134 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$sys_id"); //505
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
		$barcode_update_data[$row[csf('barcode_no')]]['from_roll_id']=$row[csf('from_roll_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['re_transfer']=$row[csf('re_transfer')];
		$barcode_update_data[$row[csf('barcode_no')]]['company_id']=$row[csf('company_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_booking_without_order']=$row[csf('from_booking_without_order')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_floor']=$row[csf('to_floor_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_room']=$row[csf('to_room')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_rack']=$row[csf('to_rack')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_self']=$row[csf('to_shelf')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_bin_box']=$row[csf('to_bin_box')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_batch_id']=$row[csf('to_batch_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_body_part']=$row[csf('body_part_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_body_part']=$row[csf('to_body_part')];
		$barcode_update_data[$row[csf('barcode_no')]]['transfer_qnty']=$row[csf('transfer_qnty')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_ord_book_id']=$row[csf('to_ord_book_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_ord_book_no']=$row[csf('to_ord_book_no')];

		if($row[csf('from_booking_without_order')] == 1)
		{
			$non_order_booking_buyer_po_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
		}
		else
		{
			$po_arr_book_booking_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
		}

		$po_arr_book_booking_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];

		$bar_code .=$row[csf('barcode_no')].",";

		$cbo_store_id = $row[csf('to_store')];
		$cbo_to_company = $row[csf('to_company')];
	}

	$bar_code = chop($bar_code,",");

	$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.fabric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id,max(b.batch_id) as batch_id, max(b.floor) as floor, max(b.room) as room, max(b.rack_no) as rack, max(b.shelf_no) as self, max(b.bin) as bin_box, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id as roll_mst_id
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(37,68) and c.entry_form in(37,68) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($bar_code)
	group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id");

	
	if(count($data_array)>0)
	{
		foreach($data_array as $val)
		{
			$splitted_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];

			if($val[csf("booking_without_order")] == 1 )
			{
				$non_order_booking_buyer_po_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			}
			else{
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
			$nxtProcessSql = sql_select("select a.id,a.barcode_no,a.roll_no from  pro_roll_details a where a.barcode_no in (".$splited_barcode.") and a.entry_form = 71 and a.status_active=1 and a.is_deleted=0 and a.is_returned!=1");
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

			$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 and barcode_no in ($splited_barcode) and entry_form = 134 order by barcode_no"); //505
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
				
		$book_booking_arr=return_library_array("SELECT po_break_down_id, booking_no from wo_booking_dtls where booking_type=1 and po_break_down_id in (". implode(',', $po_arr_book_booking_arr) .")",'po_break_down_id','booking_no');

		$sql_del_arr = "SELECT a.id, a.sys_number_prefix_num, a.sys_number,b.barcode_num, c.batch_no, c.id as batch_id
		from pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b, pro_batch_create_mst c
		where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=67 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.order_id in (".implode(",", $po_arr_book_booking_arr).") group by a.id, a.sys_number_prefix_num, a.sys_number,b.barcode_num,c.id, c.batch_no order by a.id";

		//echo $sql_del_arr;die;

		$sql_del_data=sql_select($sql_del_arr);
		
		$roll_delivery_challan=array();
		foreach($sql_del_data as $row)
		{
			$roll_delivery_challan[$row[csf("barcode_num")]][$row[csf("batch_id")]]['roll_delivery_challan']=$row[csf("sys_number")];	
			$roll_delivery_challan[$row[csf("barcode_num")]][$row[csf("batch_id")]]['roll_delivery_challan_id']=$row[csf("id")];	
		}
		unset($sql_del_data);
		
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
	$sql_floorRoomRackShelf = sql_select("SELECT c.barcode_no FROM pro_roll_details c WHERE c.entry_form IN(71,134) AND c.status_active=1 AND c.is_deleted=0 AND c.barcode_no IN(".$bar_code.")");//,505
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
		//echo $lib_room_rack_shelf_sql;die;
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

			if($row[csf("booking_without_order")]==0)
			{				
				$booking_no_fab=$book_booking_arr[$row[csf("po_breakdown_id")]];
			
				$roll_delivery_challan_no = $roll_delivery_challan[$row[csf("barcode_no")]][$row[csf("batch_id")]]['roll_delivery_challan'];
				$roll_delivery_challan_id = $roll_delivery_challan[$row[csf("barcode_no")]][$row[csf("batch_id")]]['roll_delivery_challan_id'];
				
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

			if( $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] ==134 || $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 216 || $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 214)
			{
				$booking_no_fab = $booking_no_fab;
			}
			//$barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 505 ||
			
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

			$store_id = $barcode_update_data[$row[csf('barcode_no')]]['from_store'];
			$entry_form = $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'];
			$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];
			$compsition_description=$constructtion_arr[$row[csf("febric_description_id")]]." ".$composition_arr[$row[csf("febric_description_id")]];

			$barcodeData.=$row[csf('id')]."**".$barcode_update_data[$row[csf('barcode_no')]]['company_id']."**".$body_part[$from_body_part]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$roll_delivery_challan_no."**".$roll_delivery_challan_id."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$store_id."**".$row[csf("knitting_company")]."**".$batch_arr[$row[csf("batch_id")]]."**".$row[csf("batch_id")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_prod_id']."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$from_order_id."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$from_order_id]['job_no']."**".$po_details_array[$from_order_id]['buyer_name']."**".$buyer_name."**".$po_details_array[$from_order_id]['po_number']."**".$row[csf("color_id")]."**".$store_arr[$store_id]."**".$from_body_part."**".$row[csf("brand_id")]."**".$row[csf("machine_no_id")]."**".$entry_form."**".$row[csf("booking_without_order")]."**".$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']."**".$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['trans_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_trans_id']."**".$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['po_number']."**".$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']."**".$nxProcessedBarcode[$row[csf("barcode_no")]]."**".$roll_mst_id."**".$booking_no_fab."**".$row[csf("amount")]."**".$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']."**".$from_booking_without_order."**".$splited_roll_ref[$row[csf('barcode_no')]][$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_floor']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_room']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_rack']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_self']."**".$row[csf("floor")]."**".$row[csf("room")]."**".$isFloorRoomRackShelfDisable."**".$po_details_array[$from_order_id]['grouping']."**".$row[csf("bin_box")]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_bin_box']."**".$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['job_no']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_batch_id']."**".$to_body_part."**".$barcode_update_data[$row[csf('barcode_no')]]['transfer_qnty']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_ord_book_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_ord_book_no']."**".$multi_floor."**".$multi_room."**".$multi_rack."**".$multi_self."**".$multi_bin."__";

			// $barcodeData.=$row[csf('id')]."**".$barcode_update_data[$row[csf('barcode_no')]]['company_id']."**".$body_part[$from_body_part]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$store_id."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_prod_id']."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$from_order_id."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$from_order_id]['job_no']."**".$po_details_array[$from_order_id]['buyer_name']."**".$buyer_name."**".$po_details_array[$from_order_id]['po_number']."**".$row[csf("color_id")]."**".$store_arr[$store_id]."**".$from_body_part."**".$row[csf("brand_id")]."**".$row[csf("machine_no_id")]."**".$entry_form."**".$row[csf("booking_without_order")]."**".$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']."**".$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['trans_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_trans_id']."**".$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['po_number']."**".$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']."**".$nxProcessedBarcode[$row[csf("barcode_no")]]."**".$roll_mst_id."**".$booking_no_fab."**".$row[csf("amount")]."**".$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']."**".$from_booking_without_order."**".$splited_roll_ref[$row[csf('barcode_no')]][$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_floor']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_room']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_rack']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_self']."**".$row[csf("floor")]."**".$row[csf("room")]."**".$isFloorRoomRackShelfDisable."**".$po_details_array[$from_order_id]['grouping']."**".$row[csf("bin_box")]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_bin_box']."**".$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['job_no']."**".$to_body_part."**".$multi_floor."**".$multi_room."**".$multi_rack."**".$multi_self."**".$multi_bin."__";//$row[csf("roll_mst_id")]."__";

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
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_booking_no').value, 'create_challan_search_list_view', 'search_div', 'roll_wise_finish_fabric_transfer_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.entry_form=134 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $search_field_cond $date_cond $cond_for_booking
	group by a.id, a.insert_date, a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no
	order by a.id"; //505
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

		function js_set_value(data)
		{
			var data=data.split("_");
			var id=data[0];
			var from_store=data[1];
			$('#hidden_system_id').val(id);
			$('#hidden_from_store').val(from_store);
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
	                    	<input type="hidden" name="hidden_from_store" id="hidden_from_store">
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
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_booking_no').value+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+<? echo $cbo_store_name; ?>+'_'+<? echo $cbo_store_name_from; ?>, 'create_requisition_search_list_view', 'search_div', 'roll_wise_finish_fabric_transfer_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$cbo_store_name_from =$data[8];

	if($cbo_store_name_from>0)
	{
		$storeFromCond="and b.from_store=$cbo_store_name_from";
	}

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
	where a.id=b.mst_id and a.item_category=13 and a.company_id=$company_id and a.transfer_criteria=$transfer_criteria  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond and a.entry_form=505 and a.transfer_requ_id IS NOT NULL
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

	$sql = "SELECT a.id, $year_field a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, max(b.to_store) as to_store, max(b.from_store) as from_store
	from  inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
	where a.id=b.mst_id and a.entry_form=506 and a.requisition_status=1 and a.status_active=1 and a.is_deleted=0 and a.is_approved=1 and a.transfer_criteria=$transfer_criteria and a.company_id=$company_id $search_field_cond $date_cond $cond_for_booking $requ_id_cond and b.to_store=$cbo_store_name $storeFromCond 
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
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')].'_'.$row[csf('from_store')]; ?>');">
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
if($action=="populate_data_from_data")
{
	//$sql = "select id, company_id, issue_number, knit_dye_source, knit_dye_company, issue_date, batch_no, issue_purpose from inv_issue_master where id=$data and entry_form=61";
	$sql = "SELECT a.id, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no,max(b.from_store) as from_store, max(b.to_store) as to_store, a.remarks, a.transfer_requ_id, a.transfer_requ_no,a.delivery_company_name, sum(b.transfer_qnty) as transfer_qnty
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b
	where a.id=b.mst_id and a.entry_form=134 and a.id=$data
	group by a.id, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, a.remarks, a.transfer_requ_id, a.transfer_requ_no,a.delivery_company_name "; //505
	//echo $sql;
	$res = sql_select($sql);

	
	foreach($res as $row)
	{

		echo "load_drop_down( 'requires/roll_wise_finish_fabric_transfer_entry_controller','".$row[csf("transfer_criteria")].'_'.$row[csf("company_id")]."', 'load_drop_to_com', 'cra_124' );\n";

		if ($row[csf("transfer_criteria")]==2) 
		{
			$to_company_id=$row[csf("company_id")];
			echo "$('#cbo_to_company_id').val(".$row[csf("company_id")].");\n";
		}
		else{
			$to_company_id=$row[csf("to_company")];
			echo "$('#cbo_to_company_id').val(".$row[csf("to_company")].");\n";
		}
		//echo "load_drop_down( 'requires/roll_wise_finish_fabric_transfer_entry_controller','".$row[csf("transfer_criteria")].'_'.$row[csf("company_id")]."', 'load_drop_store', 'store_td' );\n";

		echo "load_drop_down( 'requires/roll_wise_finish_fabric_transfer_entry_controller','".$row[csf("transfer_criteria")].'_'.$row[csf("company_id")]."', 'load_drop_store_from', 'from_store_td' );\n";

		echo "load_drop_down( 'requires/roll_wise_finish_fabric_transfer_entry_controller','".$row[csf("transfer_criteria")].'_'.$to_company_id."', 'load_drop_store_to', 'store_td' );\n";

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
		echo "$('#cbo_store_name_from').val(".$row[csf("from_store")].");\n";
		echo "$('#cbo_store_name').val(".$row[csf("to_store")].");\n";
		echo "$('#cbo_store_name_from').attr('disabled','true')".";\n";
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
			$from_order=$requ_array[$row[csf("transfer_requ_id")]]['from_order_id'];

			// echo "$('#txt_requisition_no').val('".$row[csf("transfer_requ_no")]."');\n";
			// echo "$('#txt_requisition_id').val('".$row[csf("transfer_requ_id")]."');\n";
			//echo "$('#txt_bar_code_num').attr('disabled','true')".";\n";
			echo "$('#hidd_requi_qty').val(".$balance.");\n";
			echo "$('#txt_from_product').val(".$from_product.");\n";
			echo "$('#txt_from_order_id').val(".$from_order.");\n";
			echo "$('#txt_prev_transfer_qnty').val(".$prev_transfer_qnty.");\n";
		}
		else
		{
			// echo "$('#txt_requisition_no').val('');\n";
			// echo "$('#txt_requisition_id').val('');\n";
			echo "$('#hidd_requi_qty').val('');\n";
			echo "$('#txt_from_product').val('');\n";
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
	$barcode_sql="SELECT barcode_no as barcode_nos from pro_roll_details where entry_form=134 and status_active=1 and is_deleted=0 and mst_id=$data"; //505
	//echo $barcode_sql;
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
	<div align="center" style="width:1560px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:1560px; margin-left:2px;">
			<legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="1560" border="1" rules="all" class="rpt_table">
	                <thead>
	                   
	                    <th>Year</th>
	                    <th>Location</th>
	                    <th>Buyer</th>
	                    <th>Job</th>
	                    <th>Order No</th>
	                    <th>File No</th>
	                    <th>Internal Ref No</th>
	                    <th>Style Ref</th>
	                    <th>Barcode No</th>
	                    <th>Booking No</th>
						<th>Batch No</th>
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
						<?php
						echo create_drop_down( "cbo_year_selection", 65, create_year_array(),"", 0,"-- --", date("Y",time()), "",0,"" );
						?>
						</td>
	                    <td>
	                    <?
							echo create_drop_down( "cbo_location_name", 100, "select id,location_name from lib_location where company_id=$company_id and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down( 'roll_wise_finish_fabric_transfer_entry_controller',this.value, 'load_drop_store_2', 'store_td_id' );" );
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
						<td align="center">
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_style" id="txt_style" />
	                    </td>
	                    <td align="center"><input type="text" name="barcode_no" id="barcode_no" style="width:80px" class="text_boxes" /></td>
	                    <td align="center">
	                        <input type="text" style="width:100px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />
	                    </td>
						<td align="center">
	                        <input type="text" style="width:100px" class="text_boxes"  name="txt_batch_no" id="txt_batch_no" />
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
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('barcode_no').value+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+<? echo $cbo_store_name; ?>+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_store_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_status').value+'_'+<? echo $requisition_id; ?>+'_'+<? echo $cbo_store_name_from; ?>+'_'+document.getElementById('txt_batch_no').value+'_'+<? echo $requisition_id; ?>, 'create_barcode_search_list_view', 'search_div', 'roll_wise_finish_fabric_transfer_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1,tableFilters);reset_hide_field();')" style="width:100px;" />
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
				col: [14],
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
    //var_dump($data);
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
	//$order_status=trim($data[10]);
	$cbo_year=trim($data[11]);

	$cbo_buyer_name=trim($data[12]);
	$txt_job_no=trim($data[13]);
	$txt_date_from=trim($data[14]);
	$txt_date_to=trim($data[15]);
	$cbo_status=trim($data[16]);
	$requisition_id=trim($data[17]);
	$from_store_id=trim($data[18]);
	$batchNo=trim($data[19]);
	$requisition_id=trim($data[20]);
	//var_dump($storeName);die;


	$search_field_cond="";
	if($order_no!="") $search_field_cond=" and d.po_number like '%$order_no%'";
	if($file_no!="") $search_field_cond.=" and d.file_no like '%$file_no%'";
	if($ref_no!="") $search_field_cond.=" and d.grouping like '%$ref_no%'";
	if($txt_job_no!="") $search_field_cond.=" and d.job_no_mst like '%$txt_job_no%'";
	if($batchNo!="") $batch_no_cond.=" and f.batch_no like '%$batchNo%'";

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
			$po_sql = sql_select("select d.id as po_id, b.booking_no from wo_po_break_down d, wo_booking_dtls b, wo_booking_mst a where d.id = b.po_break_down_id and b.booking_no=a.booking_no and a.company_id=$company_id and a.booking_type in (1) and b.status_active =1 and b.booking_no like '%$bookingNo%' and a.booking_year=$cbo_year $search_field_cond $buyer_id_cond group by d.id, b.booking_no");
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

	
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$buyer_arr=return_library_array( "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name",'id','buyer_name');
	$color_name_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$batch_arr=return_library_array( "select id,batch_no from  pro_batch_create_mst",'id','batch_no');


	if($transfer_cateria == 1 || $transfer_cateria == 2)
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
		$requisition_data=sql_select("SELECT b.barcode_no, b.from_prod_id, b.from_order_id, b.from_booking_without_order
		from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b 
		where a.id=b.mst_id and b.entry_form=506 and a.entry_form=506 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$requisition_id");
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

		$from_requisition_sql = sql_select("SELECT a.barcode_no, a.receive_basis, b.gsm, b.width as dia, b.color_id, b.fabric_description_id from pro_roll_details a, pro_finish_fabric_rcv_dtls b where a.dtls_id = b.id and a.entry_form = 68 and a.status_active=1 and b.status_active =1 and b.is_deleted =0 and a.is_deleted =0 $requisition_all_barcode_cond");
		$requisition_gsm_arr=$requisition_dia_arr=$requisition_color_id_arr=$requisition_color_range_id_arr=$requisition_feb_descr_arr=array();
		foreach ($from_requisition_sql as $row)
		{
			$requisition_gsm_arr[$row[csf("gsm")]] = $row[csf("gsm")];
			$requisition_dia_arr[$row[csf("dia")]] = $row[csf("dia")];
			$requisition_color_id_arr[$row[csf("color_id")]] = $row[csf("color_id")];
			$requisition_feb_descr_arr[$row[csf("fabric_description_id")]] = $row[csf("fabric_description_id")];
		}
		$gsm_cond= "and b.gsm in ("."'" . implode("','", $requisition_gsm_arr) . "'".")";
		$dia_cond= "and b.width in ("."'" . implode("','", $requisition_dia_arr) . "'".")";
		$color_id_cond= "and b.color_id in ("."'" . implode("','", $requisition_color_id_arr) . "'".")";
		$febric_description_id_cond= "and b.fabric_description_id in ("."'" . implode("','", $requisition_feb_descr_arr) . "'".")";


		$job_ref_data_array=sql_select("SELECT a.job_no, b.id
		FROM wo_po_details_master a, wo_po_break_down b 
		WHERE a.job_no=b.job_no_mst and a.status_active =1 and b.status_active=1  and b.id in (".implode(",", $requisition_from_order_id_arr).") group by a.job_no, b.id");
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
	
	// =======================================
	//cast(b.rack_no as VARCHAR2(4000)) as rack_no, for fakir fashion ltd.
	$data_arr="SELECT a.recv_number, a.location_id, b.prod_id,b.fabric_description_id as fabric_description_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, 
	d.file_no, d.grouping, a.entry_form,a.store_id as to_store, d.status_active, e.buyer_name as buyer_id,b.floor,b.room,b.rack_no,b.shelf_no 
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e,pro_batch_create_mst f
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and b.batch_id=f.id and a.company_id=$company_id and a.entry_form in(68) and c.entry_form in(68) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.re_transfer=0 and a.store_id=$from_store_id  $search_field_cond $barcode_cond $location_cond $without_store_cond_1 $store_cond_1 $all_po_cond $batch_no_cond and c.booking_without_order=0
	union all
	SELECT a.recv_number, a.location_id, b.prod_id,b.fabric_description_id as fabric_description_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, 
	d.file_no, d.grouping, a.entry_form,a.store_id as to_store, d.status_active, e.buyer_name as buyer_id,b.floor,b.room,b.rack_no,b.shelf_no 
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e,pro_batch_create_mst f
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and b.batch_id=f.id and a.company_id=$company_id and a.entry_form in(126) and c.entry_form in(126) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.re_transfer=0 and a.store_id=$from_store_id  $search_field_cond $barcode_cond $without_store_cond_1 $store_cond_1 $all_po_cond $batch_no_cond and c.booking_without_order=0
	union all
	select a.transfer_system_id as recv_number, null as location_id, from_prod_id as prod_id,b.feb_description_id as fabric_description_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,b.to_store, d.status_active, e.buyer_name as buyer_id,b.to_floor_id as floor,b.to_room as room,b.to_rack as rack_no,b.to_shelf as shelf_no
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e,pro_batch_create_mst f
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and d.job_id=e.id and b.to_batch_id=f.id and a.to_company=$company_id and a.entry_form =134 and c.entry_form =134 and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1  and b.to_store=$from_store_id $barcode_cond $without_store_cond_2 $store_cond_2 $all_po_cond $batch_no_cond and c.booking_without_order= 0 $job_no_cond";

	// union all select a.transfer_system_id as recv_number, null as location_id, from_prod_id as prod_id,b.feb_description_id as fabric_description_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,b.to_store, d.status_active, e.buyer_name as buyer_id
	// from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e
	// where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and d.job_id=e.id and a.to_company=$company_id and a.entry_form in(216,219) and c.entry_form in(216,219) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.re_transfer=0 $barcode_cond $without_store_cond_2 $store_cond_2 $all_po_cond and c.booking_without_order= 0 $job_no_cond

	//echo $data_arr;

	$result = sql_select($data_arr);

	foreach ($result as $row) 
	{
		$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		$all_prod_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
		$fabric_description_ids .= $row[csf("fabric_description_id")].",";
	}
	
	//$fabric_description_ids = chop($fabric_description_ids,",");
	$fabric_description_ids=implode(",",array_filter(array_unique(explode(",",chop($fabric_description_ids,',')))));
	
	$barcode_arr = array_filter(array_unique($barcode_arr));

	if(count($barcode_arr)>0)
	{
		$all_barcode_nos = implode(",", $barcode_arr);
		$BarCond = $all_barcode_cond = "";

		if($db_type==2 && count($barcode_arr)>999)
		{
			$barcode_arr_chunk=array_chunk($barcode_arr,999) ;
			foreach($barcode_arr_chunk as $chunk_arr)
			{
				$BarCond.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";
			}

			$all_barcode_cond.=" and (".chop($BarCond,'or ').")";

		}
		else
		{
			$all_barcode_cond=" and a.barcode_no in($all_barcode_nos)";
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
		//echo "select a.barcode_no from pro_roll_details a where a.entry_form in(68) and a.is_returned=0 and a.status_active=1 and a.is_deleted=0 $all_barcode_cond";die;
		$barcodeData=sql_select( "select a.barcode_no from pro_roll_details a where a.entry_form =71 and a.is_returned=0 and a.status_active=1 and a.is_deleted=0 $all_barcode_cond");
		foreach ($barcodeData as $row)
		{
			$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		}

		$stitch_lot_sql = sql_select("SELECT a.barcode_no, a.receive_basis, a.booking_no, b.width,b.dia_width_type,b.batch_id, b.body_part_id, b.color_id,b.gsm from pro_roll_details a,pro_finish_fabric_rcv_dtls b where a.dtls_id = b.id and a.entry_form = 68 and a.status_active=1 and b.status_active =1 and b.is_deleted =0 and a.is_deleted =0 $all_barcode_cond");
		foreach ($stitch_lot_sql as $row)
		{
			$production_ref_arr[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];
			$production_ref_arr[$row[csf("barcode_no")]]['width'] = $row[csf("width")];
			$production_ref_arr[$row[csf("barcode_no")]]['dia_width_type'] = $row[csf("dia_width_type")];
			$production_ref_arr[$row[csf("barcode_no")]]['batch_id'] = $row[csf("batch_id")];
			$production_ref_arr[$row[csf("barcode_no")]]['gsm'] = $row[csf("gsm")];
			$production_ref_arr[$row[csf("barcode_no")]]['body_part_id'] = $row[csf("body_part_id")];
			$production_ref_arr[$row[csf("barcode_no")]]['receive_basis'] = $row[csf("receive_basis")];
			$production_ref_arr[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
			$production_ref_arr[$row[csf("barcode_no")]]['color_id'] = $color_name_arr[$row[csf("color_id")]];
		}

		$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2 $all_prod_id_cond",'id','product_name_details');

		$composition_arr=array(); $constructtion_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in ($fabric_description_ids)";
		$deter_array=sql_select($sql_deter);
		foreach( $deter_array as $row )
		{
			$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
			$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
		}

		// $composition_arr=array();
		// $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in ($fabric_description_ids)";
		// //echo $sql_deter;
		// $data_array=sql_select($sql_deter);
		// foreach( $data_array as $row )
		// {
		// 	if(array_key_exists($row[csf('id')],$composition_arr))
		// 	{
		// 		$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		// 	}
		// 	else
		// 	{
		// 		$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		// 	}
		// }
		//var_dump($composition_arr);

		$requisition_barcode_arr=array();
		if ($requisition_id!="") 
		{
			$sqlRecvT="SELECT d.id, d.entry_form, d.receive_basis, d.knitting_source, b.fabric_description_id, b.body_part_id, b.gsm, b.width, b.color_id, a.barcode_no 
			FROM inv_receive_master d, pro_finish_fabric_rcv_dtls b, pro_roll_details a WHERE d.id=b.mst_id and b.id=a.dtls_id and d.entry_form in(68,126) and a.entry_form in(68,126) and d.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $all_barcode_cond $gsm_cond $dia_cond $color_id_cond $febric_description_id_cond order by d.entry_form desc"; 

			// echo $sqlRecvT;die;
			$recvDataT=sql_select($sqlRecvT);

			foreach($recvDataT as $row)
			{
				$requisition_barcode_arr[$row[csf('barcode_no')]] =$row[csf('barcode_no')];
			}
		}
		//print_r($requisition_barcode_arr);

		$floorRoomRackShelf_array=return_library_array( "SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");
		
	}

	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2110" class="rpt_table">
        <thead>
            <th width="30" style="word-break: break-all;">SL</th>
            <th width="150" style="word-break: break-all;">Fabric Description</th>
            <th width="100" style="word-break: break-all;">Body Part</th>
            <th width="30" style="word-break: break-all;">Dia</th>
            <th width="30" style="word-break: break-all;">Gsm</th>
            <th width="60" style="word-break: break-all;">Color</th>
            <th width="90" style="word-break: break-all;">Batch No</th>
            <th width="100" style="word-break: break-all;">Construction</th>
            <th width="100" style="word-break: break-all;">Composition</th>
            <th width="100" style="word-break: break-all;">Buyer</th>
            <th width="75" style="word-break: break-all;">Job No</th>
            <th width="110" style="word-break: break-all;">Order No/Sample Booking</th>
            <th width="70" style="word-break: break-all;">Barcode No</th>
            <th width="50" style="word-break: break-all;">Roll No</th>
            <th width="50" style="word-break: break-all;">Roll Qty.</th>
            <th width="50" style="word-break: break-all;">Qty. In Pcs</th>
            <th width="70" style="word-break: break-all;">File NO</th>
            <th width="70" style="word-break: break-all;">Ref No</th>
            <th width="70" style="word-break: break-all;">Shipment Date</th>
            <th width="110" style="word-break: break-all;">Location</th>
            <th width="100" style="word-break: break-all;">Store</th>
            <th width="100" style="word-break: break-all;">Floor</th>
            <th width="100" style="word-break: break-all;">Room</th>
            <th width="100" style="word-break: break-all;">Rack</th>
            <th width="100" style="word-break: break-all;">Shelf</th>
            <th style="word-break: break-all;">Status</th>
        </thead>
	</table>
	<div style="width:2130px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2110" class="rpt_table" id="tbl_list_search">
        <?
            $i=1;
       
			if ($requisition_id!="") // for Requisition Basis
			{
				foreach ($result as $row)
	            {
	            	if($scanned_barcode_arr[$row[csf('barcode_no')]]=="" && $requisition_barcode_arr[$row[csf("barcode_no")]]!="")
            		{
						//var_dump($row[csf('barcode_no')]);
						$batch_no = $batch_arr[$production_ref_arr[$row[csf("barcode_no")]]['batch_id']];
						$yarn_lot = $production_ref_arr[$row[csf("barcode_no")]]['yarn_lot'];
						$dia_width = $production_ref_arr[$row[csf("barcode_no")]]['width'];
						$gsm = $production_ref_arr[$row[csf("barcode_no")]]['gsm'];
						$body_part_id = $production_ref_arr[$row[csf("barcode_no")]]['body_part_id'];
						$receive_basis = $production_ref_arr[$row[csf("barcode_no")]]['receive_basis'];
						$colorName = $production_ref_arr[$row[csf("barcode_no")]]['color_id'];

						// $composition_data=$composition_arr[$row[csf('fabric_description_id')]];
						// $composition_Construction = split ("\,", $composition_data);
						
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
							<td width="30" align="center"><? echo $i; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
								<input type="hidden" name="txt_individual_qty" id="txt_individual_qty<?php echo $i; ?>" value="<?php echo $row[csf('qnty')]; ?>"/>
							</td>
							<td width="150" title="<? echo $row[csf('prod_id')];?>"><p><? echo $composition_arr[$row[csf('fabric_description_id')]]; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $body_part[$body_part_id]; ?></p></td>
							<td width="30" style="word-break: break-all;"><p><? echo $dia_width; ?></p></td>
							<td width="30" style="word-break: break-all;"><p><? echo $gsm; ?></p></td>
							<td width="60" style="word-break: break-all;"><p><? echo $colorName; ?></p></td>
							<td width="90" style="word-break: break-all;"><p><? echo $batch_no; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $constructtion_arr[$row[csf('fabric_description_id')]]; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $composition_arr[$row[csf('fabric_description_id')]];; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
							<td width="75" style="word-break: break-all;"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
							<td width="110" style="word-break: break-all;"><p><? echo $row[csf('po_number')]; ?></p></td>
							<td width="70" style="word-break: break-all;"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
							<td width="50" align="center" style="word-break: break-all;"><? echo $row[csf('roll_no')]; ?></td>
							<td width="50" align="right" style="word-break: break-all;"><? echo number_format($row[csf('qnty')],2); ?></td>
							<td width="50" align="right" style="word-break: break-all;"><? echo $row[csf('qc_pass_qnty_pcs')]*1; ?></td>
	                        <td width="70" align="center" style="word-break: break-all;"><? echo $row[csf('file_no')]; ?></td>
	                        <td width="70" align="center" style="word-break: break-all;"><? echo $row[csf('grouping')]; ?></td>
							<td width="70" align="center" style="word-break: break-all;"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
							<td width="110" align="center" style="word-break: break-all;"><? echo $location_arr[$row[csf('location_id')]]; ?></td>
							<td width="100" align="center" style="word-break: break-all;"><? echo $store_arr[$row[csf('to_store')]]; ?></td>
							<td width="100" align="center" style="word-break: break-all;"><? echo $floorRoomRackShelf_array[$row[csf('floor')]]; ?> </td>
							<td width="100" align="center" style="word-break: break-all;"><? echo $floorRoomRackShelf_array[$row[csf('room')]]; ?></td>
							<td width="100" align="center" style="word-break: break-all;"><? echo $floorRoomRackShelf_array[$row[csf('rack_no')]]; ?></td>
							<td width="100" align="center" style="word-break: break-all;"><? echo $floorRoomRackShelf_array[$row[csf('shelf_no')]]; ?></td>
							<td align="center" style="word-break: break-all;"><? echo $row_status[$row[csf('status_active')]]; ?></td>
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
					//var_dump($row[csf('barcode_no')]);
					
	            	if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
	            	{
						$batch_no = $batch_arr[$production_ref_arr[$row[csf("barcode_no")]]['batch_id']];
						$yarn_lot = $production_ref_arr[$row[csf("barcode_no")]]['yarn_lot'];
						$dia_width = $production_ref_arr[$row[csf("barcode_no")]]['width'];
						$gsm = $production_ref_arr[$row[csf("barcode_no")]]['gsm'];
						$body_part_id = $production_ref_arr[$row[csf("barcode_no")]]['body_part_id'];
						$receive_basis = $production_ref_arr[$row[csf("barcode_no")]]['receive_basis'];
						$colorName = $production_ref_arr[$row[csf("barcode_no")]]['color_id'];

						// $composition_data=$composition_arr[$row[csf('fabric_description_id')]];
						// $composition_Construction = split ("\,", $composition_data);
						
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
							<td width="30" align="center"><? echo $i; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
								<input type="hidden" name="txt_individual_qty" id="txt_individual_qty<?php echo $i; ?>" value="<?php echo $row[csf('qnty')]; ?>"/>
							</td>
							<td width="150" title="<? echo $row[csf('prod_id')];?>"><p><? echo $composition_arr[$row[csf('fabric_description_id')]]; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $body_part[$body_part_id]; ?></p></td>
							<td width="30" style="word-break: break-all;"><p><? echo $dia_width; ?></p></td>
							<td width="30" style="word-break: break-all;"><p><? echo $gsm; ?></p></td>
							<td width="60" style="word-break: break-all;"><p><? echo $colorName; ?></p></td>
							<td width="90" style="word-break: break-all;"><p><? echo $batch_no; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $constructtion_arr[$row[csf('fabric_description_id')]]; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $composition_arr[$row[csf('fabric_description_id')]];; ?></p></td>
							<td width="100" style="word-break: break-all;"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
							<td width="75" style="word-break: break-all;"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
							<td width="110" style="word-break: break-all;"><p><? echo $row[csf('po_number')]; ?></p></td>
							<td width="70" style="word-break: break-all;"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
							<td width="50" align="center" style="word-break: break-all;"><? echo $row[csf('roll_no')]; ?></td>
							<td width="50" align="right" style="word-break: break-all;"><? echo number_format($row[csf('qnty')],2); ?></td>
							<td width="50" align="right" style="word-break: break-all;"><? echo $row[csf('qc_pass_qnty_pcs')]*1; ?></td>
	                        <td width="70" align="center" style="word-break: break-all;"><? echo $row[csf('file_no')]; ?></td>
	                        <td width="70" align="center" style="word-break: break-all;"><? echo $row[csf('grouping')]; ?></td>
							<td width="70" align="center" style="word-break: break-all;"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
							<td width="110" align="center" style="word-break: break-all;"><? echo $location_arr[$row[csf('location_id')]]; ?></td>
							<td width="100" align="center" style="word-break: break-all;"><? echo $store_arr[$row[csf('to_store')]]; ?></td>
							<td width="100" align="center" style="word-break: break-all;"><? echo $floorRoomRackShelf_array[$row[csf('floor')]]; ?> </td>
							<td width="100" align="center" style="word-break: break-all;"><? echo $floorRoomRackShelf_array[$row[csf('room')]]; ?></td>
							<td width="100" align="center" style="word-break: break-all;"><? echo $floorRoomRackShelf_array[$row[csf('rack_no')]]; ?></td>
							<td width="100" align="center" style="word-break: break-all;"><? echo $floorRoomRackShelf_array[$row[csf('shelf_no')]]; ?></td>
							<td align="center" style="word-break: break-all;"><? echo $row_status[$row[csf('status_active')]]; ?></td>
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
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2110" class="rpt_table">
        <tr class="tbl_bottom">
            <td width="30"></td>
            <td width="150"></td>
            <td width="100"></td>
            <td width="30"></td>
            <td width="30"></td>
            <td width="60"></td>
            <td width="90"></td>
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
            <td width="100"></td>
            <td width="100"></td>
            <td width="100"></td>
            <td width="100"></td>
            <td></td>
        </tr>
        <tr class="tbl_bottom">
            <td width="30"></td>
            <td width="150"></td>
            <td width="100"></td>
            <td width="30"></td>
            <td width="30"></td>
            <td width="60"></td>
            <td width="90"></td>
            <td width="50"></td>
            <td width="100"></td>
            <td width="100"></td>
            <td width="75"></td>
            <td width="110"></td>
            <td width="70"></td>
            <td width="50">Select Total:</td>            
            <td width="50"><input type="text" name="hidden_selected_row_total" id="hidden_selected_row_total" class="text_boxes_numeric" style="width: 35px;" readonly disabled></td>
            <td width="50"></td>
            <td width="70"></td>
            <td width="70"></td>
            <td width="70"></td>
            <td width="110"></td>
            <td width="100"></td>
            <td width="100"></td>
            <td width="100"></td>
            <td width="100"></td>
            <td width="100"></td>
            <td></td>
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
	$color_id="'$color_id'";
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
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_to_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $item_desc_ids; ?>+'_'+<? echo $item_gsm; ?>+'_'+<? echo $item_dia; ?>+'_'+<? echo $color_id; ?>+'_'+<? echo $txt_requisition_basis; ?>+'_'+<? echo $requisition_id; ?>, 'create_po_search_list_view', 'search_div', 'roll_wise_finish_fabric_transfer_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$color_id=$data[10];
	$txt_requisition_basis=$data[11];
	$requisition_id=$data[12];

	// =======================================

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
		$color_id=implode(",", array_filter(array_unique(explode(',', $color_id))));
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
	if($fabric_composition!="") $fabric_composition_cond=" and c.copmposition like '%$fabric_composition'";
	if($item_dia!="") $item_dia_cond=" and c.dia_width in ('$item_dia')";
	if($item_gsm!="") $item_gsm_cond=" and c.gsm_weight in ($item_gsm)";
	if($color_id!="") $color_id_cond=" and c.gsm_weight in ($color_id)";
	

	// for requisition ==========================
	$job_no_cond=$gsm_cond=$dia_cond=$color_id_cond='';
	// echo $requisition_id;die;
	if ($requisition_id!="") 
	{
		$requisition_data=sql_select("SELECT b.barcode_no, b.from_prod_id, b.to_prod_id, b.from_order_id, b.to_order_id, b.from_booking_without_order 
		from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b 
		where a.id=b.mst_id and b.entry_form=506 and a.entry_form=506 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$requisition_id");
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

		$from_requisition_sql = sql_select("SELECT a.barcode_no, a.receive_basis, b.gsm, b.width as dia, b.color_id, b.fabric_description_id from pro_roll_details a, pro_finish_fabric_rcv_dtls b where a.dtls_id = b.id and a.entry_form in(68,126) and a.status_active=1 and b.status_active =1 and b.is_deleted =0 and a.is_deleted =0 $requisition_all_barcode_cond");
		$requisition_gsm_arr=$requisition_dia_arr=$requisition_color_id_arr=$requisition_color_range_id_arr=$requisition_feb_descr_arr=array();
		foreach ($from_requisition_sql as $row)
		{
			$requisition_gsm_arr[$row[csf("gsm")]] = $row[csf("gsm")];
			$requisition_dia_arr[$row[csf("dia")]] = $row[csf("dia")];
			$requisition_color_id_arr[$row[csf("color_id")]] = $row[csf("color_id")];
			//$requisition_color_range_id_arr[$row[csf("color_range_id")]] = $row[csf("color_range_id")];
			$requisition_feb_descr_arr[$row[csf("fabric_description_id")]] = $row[csf("fabric_description_id")];
		}

		$gsm_cond= "and c.gsm_weight in ("."'" . implode("','", $requisition_gsm_arr) . "'".")";
		$dia_cond= "and c.dia_width in ("."'" . implode("','", $requisition_dia_arr) . "'".")";
		$color_id_cond= "and c.fabric_color_id in ("."'" . implode("','", $requisition_color_id_arr) . "'".")";
		//$color_range_id_cond= "and b.color_range_id in ("."'" . implode("','", $requisition_color_range_id_arr) . "'".")";
		$febric_description_id_cond= "and b.fabric_description_id in ("."'" . implode("','", $requisition_feb_descr_arr) . "'".")";


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
	
	$sql= "SELECT a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date,c.booking_no,c.booking_mst_id
	from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c  
	where a.job_no=b.job_no_mst  and b.id=c.po_break_down_id and a.company_name=$company_id and a.buyer_name like '$buyer' $po_cond and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.booking_type in (1) $status_cond $shipment_date $booking_cond $year_field_cond $fabric_construction_cond $fabric_composition_cond $job_no_cond $color_id_cond $gsm_cond $dia_cond $without_from_order_req_cond $requisition_to_order_id_cond
	group by a.job_no,to_char(a.insert_date,'YYYY'), a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date,c.booking_no,c.booking_mst_id order by b.id, b.pub_shipment_date";
	//echo $sql;die;
	$arr=array(2=>$company_arr,3=>$buyer_arr);
	echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date,Booking No", "70,60,70,80,120,90,110,90,80,80","950","200",0, $sql , "js_set_value", "id,po_number,job_no,booking_no,booking_mst_id", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,booking_no", "",'','0,0,0,0,0,1,0,1,3,0');

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

if ($action=="finish_fabric_transfer_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="SELECT id, transfer_system_id, transfer_date, transfer_criteria, challan_no, transfer_requ_no, remarks, from_order_id, to_order_id, item_category from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	// echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	$store_library=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

	$sql_dtls="SELECT a.company_id, a.to_company, b.feb_description_id, b.batch_id, b.to_batch_id, b.dia_width, b.gsm, b.color_id, b.y_count, b.transfer_qnty , b.from_store, b.to_store, b.from_order_id, b.to_order_id, b.from_prod_id, b.to_prod_id, c.barcode_no
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=134 and c.entry_form=134 and b.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	//echo $sql_dtls;
	$sql_result= sql_select($sql_dtls);
	foreach($sql_result as $row)
	{
		$str_ref=$row[csf('batch_id')].'*'.$row[csf('dia_width')].'*'.$row[csf('gsm')].'*'.$row[csf('color_id')];
		$dtls_data_arr[$row[csf('feb_description_id')]][$str_ref]['transfer_qnty']+=$row[csf('transfer_qnty')];
		$dtls_data_arr[$row[csf('feb_description_id')]][$str_ref]['y_count']=$row[csf('y_count')];
		$dtls_data_arr[$row[csf('feb_description_id')]][$str_ref]['company_id']=$row[csf('company_id')];
		$dtls_data_arr[$row[csf('feb_description_id')]][$str_ref]['to_company']=$row[csf('to_company')];
		$dtls_data_arr[$row[csf('feb_description_id')]][$str_ref]['from_store']=$row[csf('from_store')];
		$dtls_data_arr[$row[csf('feb_description_id')]][$str_ref]['to_store']=$row[csf('to_store')];
		$dtls_data_arr[$row[csf('feb_description_id')]][$str_ref]['from_order_id']=$row[csf('from_order_id')];
		$dtls_data_arr[$row[csf('feb_description_id')]][$str_ref]['to_order_id']=$row[csf('to_order_id')];
		$dtls_data_arr[$row[csf('feb_description_id')]][$str_ref]['no_of_roll']++;

		$order_id_arr[$row[csf('from_order_id')]]=$row[csf('from_order_id')];
		$order_id_arr[$row[csf('to_order_id')]]=$row[csf('to_order_id')];
		$batch_id_arr[$row[csf('batch_id')]]=$row[csf('batch_id')];
	}
	// echo "<pre>";print_r($order_id_arr);die;
	$all_po_ids=implode(",", array_unique($order_id_arr));
	$all_batch_ids=implode(",", array_unique($batch_id_arr));
	// echo $all_po_ids;die;

	$batch_result=sql_select( "SELECT a.id, a.batch_no, a.color_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id in($all_batch_ids)");
	$batch_arr=array();
	foreach($batch_result as $row)
	{
		$batch_arr[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
	}
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	$poDataArray=sql_select("SELECT b.id,a.buyer_name,a.style_ref_no,a.job_no,b.po_number,b.pub_shipment_date, b.file_no, b.grouping as ref_no, c.booking_no 
	from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c 
	where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.id in($all_po_ids) and c.status_active=1 and c.is_deleted=0 and c.booking_type in(1,4) order by c.booking_no desc");
	$job_array=array(); //$all_job_id='';
	foreach($poDataArray as $row)
	{
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['po_no']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['date']=$row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['qty']=$row[csf('qty')];
		$job_array[$row[csf('id')]]['file']=$row[csf('file_no')];
		$job_array[$row[csf('id')]]['ref']=$row[csf('ref_no')];
		$job_array[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
	} 
	?>
	<div style="width:1010px;">
		<table width="1010" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">  
					<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
						if($result[csf('plot_no')]!="")  echo "Plot No: ".$result[csf('plot_no')]." ";
						if($result[csf('level_no')]!="")  echo "Level No: ".$result[csf('level_no')]." ";
						if($result[csf('road_no')]!="")  echo "Road No: ".$result[csf('road_no')]." ";
						if($result[csf('block_no')]!="")  echo "Block No: ".$result[csf('block_no')]." ";
						if($result[csf('city')]!="")  echo "City No: ".$result[csf('city')]." ";
						if($result[csf('zip_code')]!="")  echo "Zip Code: ".$result[csf('zip_code')]." "; 
						if($result[csf('email')]!="")  echo "Email Address: ".$result[csf('email')]." "; 
						if($result[csf('website')]!="")  echo "Website No: ".$result[csf('website')];
					}
					?> 
				</td>  
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
			</tr>
		</table>
		<br>
		<table width="1010" cellspacing="0" align="center" border="0">
			<tr>
				<td width="100">Transfer No</td>
				<td width="150">:&nbsp;<? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
				<td width="125">Transfer Criteria</td>
				<td width="125">:&nbsp;<? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
				<td width="125">Requisition Number</td>
				<td width="125">:&nbsp;<? echo $dataArray[0][csf('transfer_requ_no')]; ?></td>
			</tr>
			<tr>
				<td>Transfer Date</td>
				<td>:&nbsp;<? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
				<td>Challan No</td>
				<td>:&nbsp;<? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td>Requisition Date</td>
				<td>:&nbsp;</td>
			</tr>
			<tr>
				<td>Remarks</td>
				<td>:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2025" class="rpt_table" >
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<th colspan="9">Finish Fabric Information</th>
					<th colspan="7">Transfer From Job [Finish Fabric Out]</th>
					<th colspan="7">Transfer To Job [Finish Fabric In]</th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="55">Fab. Construction</th>
					<th width="100">Fab. Composition</th>
					<th width="60">Batch No</th>
					<th width="60">Fab. Dia</th>
					<th width="60">Fin GSM</th>
					<th width="100">Fab Color</th>
					<th width="80">Transfer Qnty</th>
					<th width="80">No of Roll</th>

					<th width="100">From Company</th>
					<th width="100">From Store</th>
					<th width="100">Buyer</th>
					<th width="100">Style</th>
					<th width="100">Job No</th>
					<th width="100">Order</th>
					<th width="100">Fabric Booking No</th>

					<th width="100">To Company</th>
					<th width="100">To Store</th>
					<th width="100">Buyer</th>
					<th width="100">Style</th>
					<th width="100">Job No</th>
					<th width="100">To Order</th>
					<th width="">Fabric Booking No</th>
				</tr>
			</thead>
			<tbody> 
				<?
				
				$i=1;
				foreach ($dtls_data_arr as $fab_dtr_idk => $fab_dtr_idv) 
				{
					foreach ($fab_dtr_idv as $str_refk => $row) 
					{
						$data=explode("*", $str_refk);
						$batch_id=$data[0];
						$dia_width=$data[1];
						$gsm=$data[2];
						$color_id=$data[3];

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="50" align="center"><? echo $constructtion_arr[$fab_dtr_idk]; ?></td>
							<td width="100" align="center"><? echo $composition_arr[$fab_dtr_idk]; ?></td>
							<td width="60" align="center"><? echo $batch_arr[$batch_id]["batch_no"]; ?></td>
							<td width="60"><? echo $dia_width; ?></td>
							<td width="60"><? echo $gsm; ?></td>
							<td width="100"><? echo $color_library[$color_id]; ?></td>
							<td width="80" align="right"><? echo number_format($row["transfer_qnty"],2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($row["no_of_roll"],2,'.',''); ?></td>

							<td width="100"><? echo $company_library[$row["company_id"]]; ?></td>
							<td width="100"><? echo $store_library[$row["from_store"]]; ?></td>
							<td width="100"><? echo $buyer_library[$job_array[$row["from_order_id"]]['buyer']]; ?></td>
							<td width="100"><? echo $job_array[$row["from_order_id"]]['style']; ?></td>
							<td width="100"><? echo $job_array[$row["from_order_id"]]['job']; ?></td>
							<td width="100"><? echo $job_array[$row["from_order_id"]]['po_no']; ?></td>
							<td width="100"><? echo $job_array[$row["from_order_id"]]['booking_no']; ?></td>

							<td width="100"><? echo $company_library[$row["to_company"]]; ?></td>
							<td width="100"><? echo $store_library[$row["to_store"]]; ?></td>
							<td width="100"><? echo $buyer_library[$job_array[$row["to_order_id"]]['buyer']]; ?></td>
							<td width="100"><? echo $job_array[$row["to_order_id"]]['style']; ?></td>
							<td width="100"><? echo $job_array[$row["to_order_id"]]['job']; ?></td>
							<td width="100"><? echo $job_array[$row["to_order_id"]]['po_no']; ?></td>
							<td width=""><? echo $job_array[$row["to_order_id"]]['booking_no']; ?></td>
						</tr>
						<? 
						$i++; 
						$transfer_qnty_sum += $row["transfer_qnty"];
					}
				}
				?>
			</tbody> 
			<tfoot>
				<tr>
					<td colspan="7" align="right"><strong>Total </strong></td>
					<td align="right"><?php echo number_format($transfer_qnty_sum,2,'.',''); ?></td>
				</tr>                           
			</tfoot>
		</table>
	</div>   
	<?	
	exit();
}

?>

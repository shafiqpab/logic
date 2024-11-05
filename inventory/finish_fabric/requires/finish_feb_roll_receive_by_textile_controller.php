<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

/*if ($action=="load_drop_down_store")
	{
		//$data=explode("***",$data);
		echo create_drop_down( "cbo_store_name", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=2 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"");
		exit();
	}*/


if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	//echo create_drop_down( "cbo_location", 152, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/finish_feb_roll_receive_by_textile_controller*2', 'store','store_td', $('#cbo_company_id').val(), this.value);");
	$sql_location = "select id, location_name from lib_location where company_id='$data[0]' and status_active=1 and is_deleted=0 group by id, location_name order by location_name";
	$location_id = sql_select($sql_location);
	$selected_location = "";

	if (count($location_id) == 1){
		$selected_location = $location_id[0][csf('id')];
	}

	echo create_drop_down("cbo_location", 151, $sql_location, "id,location_name", 1, "--Select Location--", 0,"load_drop_down( 'requires/finish_feb_roll_receive_by_textile_controller', $data[0]+'_'+this.value, 'load_drop_down_store', 'store_td');");
	die;
	exit();
}

if ($action == "load_drop_down_store") 
{
	$data = explode("_", $data);
	$category_id = 2;
	$sql_store = "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and
	a.company_id='$data[0]' and b.category_type=$category_id and a.location_id='$data[1]' and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name";
	//echo $sql_store;die;
	$store_id = sql_select($sql_store);
	$selected_store = "";
	if (count($store_id) == 1) $selected_store = $store_id[0][csf('id')];
	echo create_drop_down("cbo_store_name", 152, $sql_store, "id,store_name", 1, "--Select store--", 0, "fn_load_floor(this.value);reset_room_rack_shelf('','cbo_store_name');storeUpdateUptoDisable();");
	////storeUpdateUptoDisable();
	exit();
}

/*if($action=="floor_list")
{
	$data_ref=explode("__",$data);
	$floor_arr=array();
	$location_cond = "";
	if($data_ref[2]) $location_cond = "and b.location_id='$data_ref[2]'";
	$floor_data=sql_select("select b.floor_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name");
	foreach($floor_data as $row)
	{
		$floor_arr[$row[csf('floor_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsFloor_arr= json_encode($floor_arr);
	echo $jsFloor_arr;
	die();

}*/

if($action=="floor_list")
{
	$data_ref=explode("__",$data);
	$floor_arr=array();
	$location_cond = "";
	if($data_ref[2]) $location_cond = "and b.location_id='$data_ref[2]'";
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
	$location_cond = "";
	if($data_ref[2]) $location_cond = "and b.location_id='$data_ref[1]'";
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
	if($data_ref[2]) $location_cond = "and b.location_id='$data_ref[1]'";
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
	if($data_ref[2]) $location_cond = "and b.location_id='$data_ref[1]'";
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

if ($action=="load_room_rack_self_bin")
{
	$explodeData = explode('*', $data);
	$explodeData[11] = 'storeUpdateUptoDisable()';
	$data=implode('*', $explodeData);
	load_room_rack_self_bin("requires/finish_feb_roll_receive_by_textile_controller",$data);
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	 //---------------Check Receive date with Last Transaction date-------------//
	for($k=1;$k<=$tot_row;$k++)
	{ 	
	   $active_id="activeId_".$k;
	   $barcode_No="barcodeNo_".$k;
	   if($$active_id==1)
	   {
	   	$transId="transId_".$k;
		$productId="productId_".$k;
		$all_prod_ids.=$$productId.",";
		$all_transIds.=$$transId.",";
		$all_barcode_nos.= $$barcode_No.",";
	   }
	   else
	   {
	   		$all_barcode_issue_chk.= $$barcode_No.",";
	   }
	}

	$all_prod_ids=implode(",",array_unique(explode(",",substr($all_prod_ids,0,-1))));
	$all_transIds=implode(",",array_unique(explode(",",substr($all_transIds,0,-1))));
	$all_barcode_arr=array_filter(array_unique(explode(",",chop($all_barcode_nos,","))));

	if(count($all_barcode_arr)>0)
    {
    	$all_barcode_nos = implode(",", $all_barcode_arr);
	    $all_barcode_no_cond=""; $barCond="";
	    if($db_type==2 && count($all_barcode_arr)>999)
	    {
	    	$all_barcode_arr_chunk=array_chunk($all_barcode_arr,999);
	    	foreach($all_barcode_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$barCond.="  a.barcode_no in($chunk_arr_value) or ";
	    	}

	    	$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
	    }
	    else
	    {
	    	$all_barcode_no_cond=" and a.barcode_no in($all_barcode_nos)";
	    }

	    $inserted_roll=sql_select("select b.recv_number, a.id, a.barcode_no, a.reprocess from pro_roll_details a, inv_receive_master b where a.mst_id = b.id and a.entry_form=317 and b.entry_form=317 and a.status_active=1 and a.is_deleted=0 $all_barcode_no_cond");
		$inserted_roll_arr=array();
		foreach($inserted_roll as $inf)
		{
			$inserted_roll_arr[$inf[csf('barcode_no')]][$inf[csf('reprocess')]]["barcode_no"]=$inf[csf('barcode_no')];
			$inserted_roll_arr[$inf[csf('barcode_no')]][$inf[csf('reprocess')]]["id"]=$inf[csf('id')];
			$inserted_roll_arr[$inf[csf('barcode_no')]][$inf[csf('reprocess')]]["recv_number"]=$inf[csf('recv_number')];
		}
    }
    $all_barcode_issue_chk = array_filter(explode(",",chop($all_barcode_issue_chk,",")));
    if(count($all_barcode_issue_chk)>0)
    {
    	$all_barcode_issue_chk_nos = implode(",", $all_barcode_issue_chk);
	    $all_barcode_issue_chk_cond=""; $barCond="";
	    if($db_type==2 && count($all_barcode_issue_chk)>999)
	    {
	    	$all_barcode_issue_chk_chunk=array_chunk($all_barcode_issue_chk,999);
	    	foreach($all_barcode_issue_chk_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$barCond.="  b.barcode_no in($chunk_arr_value) or ";
	    	}

	    	$all_barcode_issue_chk_cond.=" and (".chop($barCond,'or ').")";
	    }
	    else
	    {
	    	$all_barcode_issue_chk_cond=" and b.barcode_no in($all_barcode_issue_chk_nos)";
	    }

	    $issued_roll=sql_select("select a.issue_number, b.barcode_no, b.reprocess from inv_issue_master a, pro_roll_details b where a.id = b.mst_id and a.status_active =1 and b.status_active = 1 and a.entry_form = 318 and b.entry_form = 318 $all_barcode_issue_chk_cond");
		$issued_roll_arr=array();
		foreach($issued_roll as $inf)
		{
			$issued_roll_arr[$inf[csf('barcode_no')]][$inf[csf('reprocess')]]["barcode_no"]=$inf[csf('barcode_no')];
			$issued_roll_arr[$inf[csf('barcode_no')]][$inf[csf('reprocess')]]["issue_number"]=$inf[csf('issue_number')];
		}
    }


	if($operation==1)
	{
		$is_update_cond = " and a.id not in ($all_transIds)";
	}else{
		$is_update_cond = "";
	}

	$max_transaction_date = return_field_value("max(a.transaction_date) as max_date", "inv_transaction a, inv_receive_master b", "a.prod_id in ($all_prod_ids) and b.store_id=$cbo_store_name  and a.status_active = 1 $is_update_cond ", "max_date");      
	if($max_transaction_date != "")
	{
		$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
		$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_delivery_date)));
		if ($receive_date < $max_transaction_date) 
		{
			echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot";
			die;
		}
	}
	

	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
		mysql_query("BEGIN");
		}
		 $category_id=2; $entry_form=317; $prefix='FFRRT';


		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";
		
		
		$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
        //print_r($id); die;
        $new_grey_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,$prefix,$entry_form,date("Y",time()),$category_id ));
		
		
		$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form,receive_basis, item_category, company_id, receive_date, challan_no, knitting_source, knitting_company, fabric_nature,location_id,store_id, knitting_location_id, inserted_by, insert_date";

		$data_array="(".$id.",'".$new_grey_recv_system_id[1]."',".$new_grey_recv_system_id[2].",'".$new_grey_recv_system_id[0]."',$entry_form,9, $category_id,". $cbo_company_id.",".$txt_delivery_date.",".$txt_challan_no.",".$cbo_knitting_source.",".$knit_company_id.",2,".$cbo_location.",".$cbo_store_name.",".$knit_location_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$grey_recv_num=$new_grey_recv_system_id[0];
		$grey_update_id=$id;
		
		if($color_id=="") $color_id=0;
		$field_array_trans="id, mst_id, batch_id, company_id, prod_id, item_category, transaction_type, transaction_date,store_id, cons_quantity,cons_rate, cons_amount, balance_qnty, balance_amount, cons_reject_qnty,  inserted_by, insert_date";
		
		$field_array_dtls="id, mst_id, trans_id, prod_id,body_part_id, fabric_description_id,gsm,width, order_id, receive_qnty, reject_qty, batch_id, dia_width_type,barcode_no, color_id, floor, room,rack_no,shelf_no,rate, amount, dyeing_charge, grey_fabric_rate, inserted_by, insert_date";

		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,roll_id, roll_no,barcode_no,reject_qnty,qc_pass_qnty,rate, amount,prev_reprocess,reprocess,inserted_by, insert_date, is_sales, booking_without_order, booking_no";
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date,is_sales";

		$i=0;
		$cur_st_qnty=0;
		$barcodeNos="";
		for($j=1;$j<=$tot_row;$j++)
		{ 	
		   $activeId="activeId_".$j;
		   if($$activeId==1)
		   {
		    $rollId="rollId_".$j;
			$rollId="rollId_".$j;
			$batchId="batchId_".$j;
			$bodyPart="bodyPart_".$j;
			$colorId="colorId_".$j;
			$deterId="deterId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$rollGsm="rollGsm_".$j;
			$rollDia="rolldia_".$j;
			$job_no="job_no_".$j;
			$rollwgt="rollQty_".$j;
			$rolldia="rolldia_".$j;
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			$currentWgt="currentWgt_".$j;
			$rejectQty="rejectQty_".$j;
			$wideTypeId="wideTypeId_".$j;
			$floor="floor_".$j;
			$room="room_".$j;
			$rack="rack_".$j;
			$self="self_".$j;
			$grey_rate="greyRate_".$j;
			$dyeing_charge="dyeingCharge_".$j;
			$preReprocess = "preReprocess_" . $j;
			$reProcess = "reprocess_" . $j;
			$IsSalesId = "IsSalesId_".$j;
			$bookingWithoutOrder = "bookingWithoutOrder_".$j;
			$bookingNumber = "bookingNumber_".$j;
			
			$cons_rate=$$grey_rate+$$dyeing_charge;
			$amount=$$currentWgt*$cons_rate;

			if($inserted_roll_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$reProcess)]["barcode_no"] != "")
			{
				echo "20**Sorry! Barcode already Scanned. Challan No: ".$inserted_roll_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$reProcess)]["recv_number"]." \nBarcode No ".$$barcodeNo;
				disconnect($con);die;
			}
			
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$id_dtls = return_next_id_by_sequence("PRO_FIN_FAB_RCV_DTLS_PK_SEQ", "pro_finish_fabric_rcv_dtls", $con);
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
	
			if($data_array_roll!="") $data_array_roll.= ",";
			if($data_array_trans!="") $data_array_trans.= ",";
			if($data_array_dtls!="") $data_array_dtls.= ",";
			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_trans.="(".$id_trans.",".$grey_update_id.",'".$$batchId."',".$cbo_company_id.",".$$productId.",".$category_id.",1,".$txt_delivery_date.",".$cbo_store_name.",".$$currentWgt.",'".$cons_rate."','".$amount."','".$$currentWgt."','".$amount."','".$$rejectQty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$data_array_dtls.="(".$id_dtls.",".$grey_update_id.",".$id_trans.",".$$productId.",'".$$bodyPart."','".$$deterId."','".$$rollGsm."','".$$rollDia."','".$$orderId."','".$$currentWgt."','".$$rejectQty."','".$$batchId."','".$$wideTypeId."','".$$barcodeNo."','".$$colorId."','".$$floor."','".$$room."','".$$rack."','".$$self."','".$cons_rate."','".$amount."','".$$dyeing_charge."','".$$grey_rate."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$data_array_roll.="(".$id_roll.",".$grey_update_id.",".$id_dtls.",'".$$orderId."',$entry_form,'".$$rollwgt."','".$$rollId."','".$$rollNo."','".$$barcodeNo."','".$$rejectQty."','".$$currentWgt."','".$cons_rate."','".$amount."','" . $$preReprocess . "','" . $$reProcess . "',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','" . $$IsSalesId . "','".$$bookingWithoutOrder. "','".$$bookingNumber. "')";
			
			$data_array_prop.="(".$id_prop.",".$id_trans.",1,$entry_form,".$id_dtls.",'".$$orderId."',".$$productId.",'".$$colorId."','".$$currentWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','" . $$IsSalesId . "')";
			$prodData_array[$$productId]+=$$currentWgt;
			$prodData_amount_array[$prod_id[$i]]+=$cons_amount;
			$barcodeNos.=$j."__".$id_dtls."__".$id_trans."__".$id_roll."__".$$currentWgt.",";
			$all_prod_id.=$$productId.",";

			$i++;
		   }
		}

		$prod_id_array=array();
		$all_prod_id=implode(",",array_unique(explode(",",substr($all_prod_id,0,-1))));
		$field_array_prod_update = "last_purchased_qnty*current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
		$prodResult=sql_select("select id, current_stock,avg_rate_per_unit,stock_value from product_details_master where id in($all_prod_id)");
		foreach($prodResult as $row)
		{
			$issue_qty=$prodData_array[$row[csf('id')]];
			$product_amount=$prodData_amount_array[$row[csf('id')]];
			$current_stock=$row[csf('current_stock')]+$issue_qty;
			$stockValue=$row[csf('stock_value')]+$product_amount;
			if($current_stock > 0){
				$avg_rate_per_unit=number_format($stockValue/$current_stock,$dec_place[3],'.','');
			}else{
				$avg_rate_per_unit=0;
				$stockValue=0;
			}

			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",($prodData_array[$row[csf('id')]]."*'".$current_stock."'*'".$avg_rate_per_unit."'*'".$stockValue."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
		$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);

		if($rID) $flag=1; else $flag=0;
		$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));

		if($flag==1) 
		{
			if($prodUpdate) $flag=1; else $flag=0; 
		}
		 
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);

		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		 
		$rID4=sql_insert("pro_finish_fabric_rcv_dtls",$field_array_dtls,$data_array_dtls,0);

		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		}
		//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
		$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);

		if($flag==1) 
		{
			if($rID5) $flag=1; else $flag=0; 
		} 
		
		$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		if($flag==1) 
		{
			if($rID6) $flag=1; else $flag=0; 
		} 
		
		//echo "10**".$flag."**".$rID."**".$prodUpdate."**".$rID3."**".$rID4."**".$rID5;die;
		if($db_type==0)
		{
			if($flag==1)
			{
			mysql_query("COMMIT");  
			echo "0**".$grey_update_id."**".$new_grey_recv_system_id[0]."**".substr($barcodeNos,0,-1);
			}
			else
			{
			mysql_query("ROLLBACK"); 
			echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
			oci_commit($con);  
			echo "0**".$grey_update_id."**".$new_grey_recv_system_id[0]."**".substr($barcodeNos,0,-1);
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
		
	    $category_id=2; $entry_form=317;
		$field_array_update="location_id*store_id*receive_date*updated_by*update_date";
		$data_array_update="".$cbo_location."*".$cbo_store_name."*".$txt_delivery_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		if($color_id=="") $color_id=0;
		$field_array_trans="id, mst_id, batch_id, company_id, prod_id, item_category, transaction_type, transaction_date,store_id, cons_quantity,cons_rate, cons_amount, balance_qnty, balance_amount, cons_reject_qnty,  inserted_by, insert_date";
		$field_array_dtls="id, mst_id, trans_id, prod_id,body_part_id,fabric_description_id,gsm,width,order_id,receive_qnty,reject_qty,batch_id, dia_width_type, barcode_no, color_id,floor,room,rack_no,shelf_no,rate, amount,dyeing_charge,grey_fabric_rate, inserted_by, insert_date";
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,roll_id, roll_no,barcode_no,reject_qnty,qc_pass_qnty,rate, amount,prev_reprocess,reprocess, inserted_by, insert_date,is_sales, booking_without_order, booking_no";
		$field_array_proportionate="id,trans_id,trans_type,entry_form,dtls_id,po_breakdown_id,prod_id,color_id,quantity,inserted_by,insert_date,is_sales";
		
		$field_array_trans_update="transaction_date*store_id*cons_quantity*cons_rate*cons_amount*balance_qnty*balance_amount*floor_id*room*rack*self*updated_by*update_date";
		$field_array_dtls_update="receive_qnty*rate*amount*dyeing_charge*grey_fabric_rate*floor*room*rack_no*shelf_no*updated_by*update_date";
		$field_array_roll_update="qc_pass_qnty*rate*amount* updated_by* update_date";
		$field_array_propo_update="quantity*updated_by*update_date";
		$field_array_trans_remove="updated_by*update_date*status_active*is_deleted";
		$field_array_dtls_remove="updated_by*update_date*status_active*is_deleted";
		$field_array_roll_remove="updated_by* update_date*status_active*is_deleted";
		$field_array_propor_remove="updated_by*update_date*status_active*is_deleted";
		
		$i=0;
		$cur_st_qnty=0;
		$barcodeNos="";
		for($j=1;$j<=$tot_row;$j++)
		{ 	
		    $activeId="activeId_".$j;
		    $updateDetailsId="updateDetailsId_".$j;
			$transId="transId_".$j;
			$rollTableId="rollTableId_".$j;
		    $rollId="rollId_".$j;
			$batchId="batchId_".$j;
			$bodyPart="bodyPart_".$j;
			$colorId="colorId_".$j;
			$deterId="deterId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$rollGsm="rollGsm_".$j;
			$rollDia="rolldia_".$j;
			$job_no="job_no_".$j;
			$rollwgt="rollQty_".$j;
			$rolldia="rolldia_".$j;
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			$currentWgt="currentWgt_".$j;
			$rejectQty="rejectQty_".$j;
			$wideTypeId="wideTypeId_".$j;
			$floor="floor_".$j;
			$room="room_".$j;
			$rack="rack_".$j;
			$self="self_".$j;
			$grey_rate="greyRate_".$j;
			$dyeing_charge="dyeingCharge_".$j;
			$preReprocess = "preReprocess_" . $j;
			$reProcess = "reprocess_" . $j;
			$IsSalesId = "IsSalesId_".$j;
			$bookingWithoutOrder = "bookingWithoutOrder_".$j;
			$bookingNumber = "bookingNumber_".$j;
			
			$cons_rate=$$grey_rate+$$dyeing_charge;
			$amount=$$currentWgt*$cons_rate;
			
		    if(str_replace("'","",$$updateDetailsId)!=0)
		    {
				  if($$activeId==1)
				  {
					$update_roll_id[]=$$rollTableId;
					$update_array_roll[$$rollTableId]=explode("*",("".$$currentWgt."*'".$cons_rate."'*'".$amount."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					$prodData_array[$$productId]+=$$currentWgt-$$rollwgt;
					$prodData_issarray[$$productId]+=$$currentWgt;
					$prodData_amount_array[$$productId]+=($$currentWgt-$$rollwgt)*$cons_rate;
					
					$update_trans_id[]=$$transId;
					$update_trans_arr[$$transId]=explode("*",("".$txt_delivery_date."*".$cbo_store_name."*".$$currentWgt."*'".$cons_rate."'*'".$amount."'*'".$$currentWgt."'*'".$amount."'*'".$$floor."'*'".$$room."'*'".$$rack."'*'".$$self."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
					$update_detl_id[]=$$updateDetailsId;
					$update_array_dtls[$$updateDetailsId]=explode("*",("".$$currentWgt."*'".$cons_rate."'*'".$amount."'*'".$$dyeing_charge."'*'".$$grey_rate."'*'".$$floor."'*'".$$room."'*'".$$rack."'*'".$$self."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
					$all_prod_id.=$$productId.",";
					
					if(str_replace("'", "", $$transId))
					{
						$update_prop_id[]=$$transId;
						$update_array_prop[$$transId]=explode("*",("".$$currentWgt."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					}
					$barcodeNos.=$j."__".$$updateDetailsId."__".$$transId."__".$$rollTableId."__".$$currentWgt.",";
				}	
				else if(str_replace("'","",$$activeId)==0)
				{
					if($issued_roll_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$reProcess)]["barcode_no"] != "")
					{
						echo "20**Sorry! Barcode already Issued. Issue Challan No: ".$issued_roll_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$reProcess)]["issue_number"]." \nBarcode No ".$$barcodeNo;
						disconnect($con);die;
					}
					//$stock=return_field_value("current_stock","product_details_master","id=$prod_id[$i]");
					$remove_roll_id[]=$$rollTableId;
					$remove_array_roll[$$rollTableId]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
					$prodData_array[$$productId]+=-$$rollwgt;
					$prodData_amount_array[$$productId]+=(-$$rollwgt)*$cons_rate;
					$prodData_issarray[$$productId]+=0;
					$all_prod_id.=$$productId.",";
					$remove_trans_id[]=$$transId;
					$remove_trans_arr[$$transId]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
					$remove_detl_id[]=$$updateDetailsId;
					$remove_array_dtls[$$updateDetailsId]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
					
					if(str_replace("'", "", $$transId))
					{
						$remove_prop_id[]=$$transId;
						$remove_array_prop[$$transId]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
					}

					$barcodeNos.=$j."__0__0__0__".$$currentWgt.",";
						
				}
			}
			else
			{
				if(str_replace("'","",$$activeId)==1)
				{

					if($inserted_roll_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$reProcess)]["barcode_no"] != "")
					{
						echo "20**Sorry! Barcode already Scanned. Challan No: ".$inserted_roll_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$reProcess)]["recv_number"]." \nBarcode No ".$$barcodeNo;
						disconnect($con);die;
					}



					
					$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
					$id_dtls = return_next_id_by_sequence("PRO_FIN_FAB_RCV_DTLS_PK_SEQ", "pro_finish_fabric_rcv_dtls", $con);
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			
				if($data_array_roll!="") $data_array_roll.= ",";
				if($data_array_trans!="") $data_array_trans.= ",";
				if($data_array_dtls!="") $data_array_dtls.= ",";
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_trans.="(".$id_trans.",".$update_id.",".$$batchId.",".$cbo_company_id.",".$$productId.",".$category_id.",1,
				".$txt_delivery_date.",".$cbo_store_name.",".$$currentWgt.",'".$cons_rate."','".$amount."',".$$currentWgt.",'".$amount."','".$$rejectQty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$data_array_dtls.="(".$id_dtls.",".$update_id.",".$id_trans.",".$$productId.",'".$$bodyPart."','".$$deterId."','".$$rollGsm."','".$$rollDia."','".$$orderId."','".$$currentWgt."',".$$rejectQty.",'".$$batchId."','".$$wideTypeId."','".$$barcodeNo."','".$$colorId."','".$$floor."','".$$room."','".$$rack."','".$$self."','".$cons_rate."','".$amount."','".$$dyeing_charge."','".$$grey_rate."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$data_array_roll.="(".$id_roll.",".$update_id.",".$id_dtls.",'".$$orderId."',$entry_form,'".$$rollwgt."','".$$rollId."','".$$rollNo."','".$$barcodeNo."','".$$rejectQty."','".$$currentWgt."','".$cons_rate."','".$amount."','" . $$preReprocess . "','" . $$reProcess . "',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','" . $$IsSalesId . "','".$$bookingWithoutOrder. "','".$$bookingNumber. "')";
				
				$data_array_prop.="(".$id_prop.",".$id_trans.",1,$entry_form,".$id_dtls.",'".$$orderId."',".$$productId.",'".$$colorId."','".$$currentWgt."',
				".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','" . $$IsSalesId . "')";
				$prodData_array[$$productId]+=$$currentWgt;
				$prodData_amount_array[$$productId]+=($$currentWgt)*$cons_rate;
				$prodData_issarray[$$productId]+=$$currentWgt;
				$barcodeNos.=$j."__".$id_dtls."__".$id_trans."__".$id_roll."__".$$currentWgt.",";
				$all_prod_id.=$$productId.",";

				$i++;	
				}
			}
		}
		//print_r($prodData_array);die;
		$prod_id_array=array();
		$all_prod_id=implode(",",array_unique(explode(",",substr($all_prod_id,0,-1))));
		$field_array_prod_update = "last_purchased_qnty*current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
		$prodResult=sql_select("select id, current_stock,avg_rate_per_unit,stock_value from product_details_master where id in($all_prod_id)");
		foreach($prodResult as $row)
		{
			$issue_qty=$prodData_array[$row[csf('id')]];
			$product_amount=$prodData_amount_array[$row[csf('id')]];
			$current_stock=$row[csf('current_stock')]+$issue_qty;
			$stockValue=$row[csf('stock_value')]+$product_amount;
			if($current_stock>0){
				$avg_rate_per_unit=number_format($stockValue/$current_stock,$dec_place[3],'.','');
			}else{
				$avg_rate_per_unit=0;
				$stockValue=0;
			}

			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",($prodData_issarray[$row[csf('id')]]."*'".$current_stock."'*'".$avg_rate_per_unit."'*'".$stockValue."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}

		$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,0);
		if($rID) $flag=1; else $flag=0;
		
        if(count($data_array_prod_update)>0)
	 	{
			//echo bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$data_array_prod_update,$prod_id_array);die;
			$update_product=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$data_array_prod_update,$prod_id_array),1);
			if($flag==1) 
			{
			if($update_product) $flag=1; else $flag=0; 
			} 
	 	}

		if($remove_trans_arr!="")
	 	{
			$remove_tran=execute_query(bulk_update_sql_statement(" inv_transaction","id",$field_array_trans_remove,$remove_trans_arr,$remove_trans_id),1);
			if($flag==1) 
			{
			if($remove_tran) $flag=1; else $flag=0; 
			} 
	 	}
		 
		 
		if($remove_array_dtls!="")
	 	{
			$remove_grey=execute_query(bulk_update_sql_statement("pro_finish_fabric_rcv_dtls","id",$field_array_dtls_remove,$remove_array_dtls,$remove_detl_id),1);
			if($flag==1) 
			{
			if($remove_grey) $flag=1; else $flag=0; 
			} 
	    }
		 
		 
		if($remove_array_roll!="")
	 	{
			$remove_roll=execute_query(bulk_update_sql_statement(" pro_roll_details","id",$field_array_roll_remove,$remove_array_roll,$remove_roll_id),1);
			if($flag==1) 
			{
			if($remove_roll) $flag=1; else $flag=0; 
			} 
	 	}
		
	    if(!empty($remove_array_prop))
	 	{
			$remove_order=execute_query(bulk_update_sql_statement(" order_wise_pro_details","trans_id",$field_array_propor_remove,$remove_array_prop,$remove_prop_id),1);
			if($flag==1) 
			{
			if($remove_order) $flag=1; else $flag=0; 
			} 
	 	}
			
	    if(count($update_array_roll)>0)
	    {
			$update_roll=execute_query(bulk_update_sql_statement("pro_roll_details","id",$field_array_roll_update,$update_array_roll,$update_roll_id),1);
			if($flag==1) 
			{
			if($update_roll) $flag=1; else $flag=0; 
			} 
	 	}
		
		if(count($update_array_dtls)>0)
	 	{
			$update_grey_prod=execute_query(bulk_update_sql_statement("pro_finish_fabric_rcv_dtls","id",$field_array_dtls_update,$update_array_dtls,$update_detl_id),1);
			if($flag==1) 
			{
			if($update_grey_prod) $flag=1; else $flag=0; 
			} 
	 	}
		
		if(count($update_trans_arr)>0)
	 	{
			$update_trans=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$update_trans_arr,$update_trans_id),1);
			if($flag==1) 
			{
			if($update_trans) $flag=1; else $flag=0; 
			} 
	 	}
		
		if(count($update_array_prop)>0)
	 	{
			$update_order=execute_query(bulk_update_sql_statement("order_wise_pro_details","trans_id",$field_array_propo_update,$update_array_prop,$update_prop_id),1);
			if($flag==1) 
			{
			if($update_order) $flag=1; else $flag=0; 
			} 
	 	}

		if(count($roll_data_array_update)>0)
		{
			$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $roll_data_array_update, $roll_id_arr ));
			if($flag==1)
			{
			if($rollUpdate) $flag=1; else $flag=0;
			}
		}
			
		//echo bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$data_array_prod_update,$prod_id_array);die;

		$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
		if($flag==1) 
		{
			if($prodUpdate) $flag=1; else $flag=0; 
		} 
		
		if(count($data_array_trans)>0)
		{
			$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} 
		}
		
		if(count($data_array_dtls)>0)
		{
			$rID4=sql_insert("pro_finish_fabric_rcv_dtls",$field_array_dtls,$data_array_dtls,0);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			} 
		}
		if(count($data_array_roll)>0)
		{
			//echo "10**insert into pro_roll_details ($field_array_roll) values $data_array_roll";die;
			$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}
		
		if(count($data_array_prop)>0)
		{
			$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
			} 
		}
		
		//echo "10**".$update_grey_prod."=".$rollUpdate."=".$prodUpdate."=".$rID3."=".$rID4."=".$rID5."=".$rID6."=".$flag;die;
		//echo "10**".$flag;die;
		if($db_type==0)
		{
			if($flag==1)
			{
			mysql_query("COMMIT");  
			echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_no)."**".substr($barcodeNos,0,-1);;
			}
			else
			{
			mysql_query("ROLLBACK"); 
			echo "6**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
			oci_commit($con);  
			echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_no)."**".substr($barcodeNos,0,-1);;
			}
			else
			{
			oci_rollback($con);
			echo "6**0**0";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="finish_item_details_update")
{
	$ext_data=explode("_",$data);
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0", "id", "short_name"); 
	$company_name_array=return_library_array( "select id,company_name from lib_company where status_active=1 and is_deleted=0 and id=$ext_data[2]", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	

	$insert_roll_condall="";
	if(count($receive_barcode)>0){ $insert_roll_condall=" and c.barcode_no not in (".implode(",",$receive_barcode).")"; }

	$data_array=sql_select("SELECT b.id as dtls_id,b.product_id,b.color_id,b.job_no,b.order_id,b.bodypart_id,b.construction,b.composition,b.batch_id ,b.grey_sys_id, b.sys_dtls_id,b.grey_sys_number,b.determination_id, b.gsm,b.dia, c.qnty,b.width_type,c.barcode_no,c.po_breakdown_id,c.roll_id, c.roll_no, c.reject_qnty, c.prev_reprocess, c.reprocess, c.is_sales, c.booking_without_order, c.booking_no FROM pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b,pro_roll_details c WHERE a.company_id=$ext_data[2]  and a.sys_number='$ext_data[0]' and a.entry_form=67 and a.id=b.mst_id and b.id=c.dtls_id and  c.entry_form=67 and c.is_sales=1 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $insert_roll_condall");


	$poIDs=""; $deterIDs="";$barcode_NOs="";$job_NOs="";$batch_ids=$color_ids="";
	foreach($data_array as $row)
	{
		$poIDs.=$row[csf("po_breakdown_id")].",";
		$deterIDs.=$row[csf("determination_id")].",";
		$barcode_NOs.=$row[csf("barcode_no")].",";
		$job_NOs.=$row[csf("job_no")].",";
		$batch_ids.=$row[csf("batch_id")].",";
		$color_ids.=$row[csf("color_id")].",";

	}
	
	$poIDs_all=rtrim($poIDs,","); 
	$poIDs_alls=explode(",",$poIDs_all);
	$poIDs_alls=array_chunk($poIDs_alls,999); 
	$po_id_cond=" and";
	$po_id_cond_2=" and";
	$sales_order_cond=" and";
	foreach($poIDs_alls as $dtls_id)
	{
		if($po_id_cond==" and")  $po_id_cond.="(b.id in(".implode(',',$dtls_id).")"; else $po_id_cond.=" or b.id in(".implode(',',$dtls_id).")";
		if($po_id_cond_2==" and")  $po_id_cond_2.="(c.id in(".implode(',',$dtls_id).")"; else $po_id_cond_2.=" or c.id in(".implode(',',$dtls_id).")";

		if($sales_order_cond==" and")  $sales_order_cond.="(id in(".implode(',',$dtls_id).")"; else $sales_order_cond.=" or id in(".implode(',',$dtls_id).")";
	}
	$po_id_cond.=")";
	$po_id_cond_2.=")";
	$sales_order_cond.=")";

	$roll_po_id_cond=" and";
	foreach($poIDs_alls as $dtls_id)
	{
		if($roll_po_id_cond==" and")  $roll_po_id_cond.="(po_breakdown_id in(".implode(',',$dtls_id).")"; else $roll_po_id_cond.=" or po_breakdown_id in(".implode(',',$dtls_id).")";
	}
	$roll_po_id_cond.=")";

	$sales_arr=array();
	$sql_sales=sql_select("select id,job_no,within_group,sales_booking_no,po_job_no,po_buyer,buyer_id,customer_buyer,insert_date from fabric_sales_order_mst where status_active=1 and is_deleted=0 $sales_order_cond");
	foreach ($sql_sales as $sales_row) {
		$sales_arr[$sales_row[csf('id')]]["sales_order_no"] 	= $sales_row[csf('job_no')];
		$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
		$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
		$sales_arr[$sales_row[csf('id')]]["po_job_no"] 			= $sales_row[csf('po_job_no')];
		$sales_arr[$sales_row[csf('id')]]["po_buyer"] 			= $sales_row[csf('po_buyer')];
		$sales_arr[$sales_row[csf('id')]]["buyer_id"] 			= $sales_row[csf('buyer_id')];
		$sales_arr[$sales_row[csf('id')]]["cust_buyer_id"] 		= $sales_row[csf('customer_buyer')];
		$sales_arr[$sales_row[csf('id')]]["year"] 				= date("Y", strtotime($sales_row[csf("insert_date")]));
	}

	$deterIDs_all=rtrim($deterIDs,","); 
	$deterIDs_alls=explode(",",$deterIDs_all);
	$deterIDs_alls=array_chunk($deterIDs_alls,999); 
	$deter_id_cond=" and";
	$deter_id_cond_2=" and";
	foreach($deterIDs_alls as $dtls_id)
	{
		if($deter_id_cond==" and")  $deter_id_cond.="(a.id in(".implode(',',$dtls_id).")"; else $deter_id_cond.=" or a.id in(".implode(',',$dtls_id).")";
		if($deter_id_cond_2==" and")  $deter_id_cond_2.="(b.lib_yarn_count_deter_id in(".implode(',',$dtls_id).")"; else $deter_id_cond_2.=" or b.lib_yarn_count_deter_id in(".implode(',',$dtls_id).")";
	}
	$deter_id_cond.=")";
	$deter_id_cond_2.=")";
	//echo $deter_id_cond;die;

	$barcode_Nos_all=rtrim($barcode_NOs,","); 
	$barcode_Nos_alls=explode(",",$barcode_Nos_all);
	$barcode_Nos_alls=array_chunk($barcode_Nos_alls,999); 
	$barcode_no_conds=" and";
	$barcode_no_conds_2=" and";
	foreach($barcode_Nos_alls as $dtls_id)
	{
		if($barcode_no_conds==" and")  $barcode_no_conds.="(c.barcode_no in(".implode(',',$dtls_id).")"; else $barcode_no_conds.=" or c.barcode_no in(".implode(',',$dtls_id).")";
		if($barcode_no_conds_2==" and")  $barcode_no_conds_2.="(barcode_no in(".implode(',',$dtls_id).")"; else $barcode_no_conds_2.=" or barcode_no in(".implode(',',$dtls_id).")";
	}
	$barcode_no_conds.=")";
	$barcode_no_conds_2.=")";

	$job_NOs_all=rtrim($job_NOs,","); 
	$job_NOs_alls="'".implode("','",explode(",",$job_NOs_all))."'";
	$job_NOs_alls=explode(",",$job_NOs_alls);
	$job_NOs_alls=array_chunk($job_NOs_alls,999); 
	$job_NOs_conds=" and";
	foreach($job_NOs_alls as $dtls_id)
	{
		if($job_NOs_conds==" and")  $job_NOs_conds.="(b.job_no_prefix_num in(".implode(',',$dtls_id).")"; 
		else $job_NOs_conds.=" or b.job_no_prefix_num in(".implode(',',$dtls_id).")";
	}
	$job_NOs_conds.=")";

	$color_arr=array();
	if($batch_ids != ""){
		//$batch_name_array=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".rtrim($batch_ids,", ").")", "id", "batch_no");	
		$batch_sql = sql_select("select id, batch_no, booking_no from  pro_batch_create_mst where id in(".rtrim($batch_ids,", ").")");
		foreach($batch_sql as $row)
		{
			$batch_info_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
			$batch_info_array[$row[csf("id")]]['booking_no'] = $row[csf("booking_no")];
		}
	}
	if($color_ids != ""){
		$color_arr=return_library_array( "select id, color_name from lib_color where id in(".rtrim($color_ids,", ").")",'id','color_name');
	}
	$inserted_roll=sql_select("select c.id as roll_table_id,b.id as update_dtls_id,b.trans_id,c.barcode_no,b.floor,b.room,b.rack_no,b.shelf_no,c.qc_pass_qnty,c.reprocess from  inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_roll_details c where  a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=317 and c.entry_form=317 and a.id=$ext_data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $barcode_no_conds");
	$inserted_roll_arr=array();
	$inserted_barcode=array();
	foreach($inserted_roll as $inf)
	{
		$inserted_roll_check_arr[$inf[csf('barcode_no')]][$inf[csf('reprocess')]]=$inf[csf('barcode_no')];
		$inserted_barcode[]=$inf[csf('barcode_no')];
		$inserted_roll_arr[$inf[csf('barcode_no')]][$inf[csf('reprocess')]]['barcode']=$inf[csf('barcode_no')];
		$inserted_roll_arr[$inf[csf('barcode_no')]][$inf[csf('reprocess')]]['floor']=$inf[csf('floor')];
		$inserted_roll_arr[$inf[csf('barcode_no')]][$inf[csf('reprocess')]]['rack']=$inf[csf('rack_no')];
		$inserted_roll_arr[$inf[csf('barcode_no')]][$inf[csf('reprocess')]]['room']=$inf[csf('room')];
		$inserted_roll_arr[$inf[csf('barcode_no')]][$inf[csf('reprocess')]]['self']=$inf[csf('shelf_no')];
		$inserted_roll_arr[$inf[csf('barcode_no')]][$inf[csf('reprocess')]]['qc_pass_qnty']=$inf[csf('qc_pass_qnty')];
		$inserted_roll_arr[$inf[csf('barcode_no')]][$inf[csf('reprocess')]]['roll_table_id']=$inf[csf('roll_table_id')];
		$inserted_roll_arr[$inf[csf('barcode_no')]][$inf[csf('reprocess')]]['update_dtls_id']=$inf[csf('update_dtls_id')];
		$inserted_roll_arr[$inf[csf('barcode_no')]][$inf[csf('reprocess')]]['trans_id']=$inf[csf('trans_id')];
	}

	$insert_roll_cond="";
	if(count($inserted_barcode)>0){ $insert_roll_cond=" and barcode_no not in (".implode(",",$inserted_barcode).")"; }
	$all_barcode=sql_select("select barcode_no $barcode_no_conds_2 from pro_roll_details where entry_form=317 $insert_roll_cond and status_active=1 and is_deleted=0");
	$receive_barcode=array();
	foreach($all_barcode as $val)
	{
		$receive_barcode[]=$val[csf("barcode_no")];
	}

	$fabricnyarn_dyeing=sql_select("select a.job_no,b.lib_yarn_count_deter_id, b.id,a.color_break_down from  wo_pre_cost_fab_conv_cost_dtls a,wo_pre_cost_fabric_cost_dtls b where a.fabric_description=b.id and a.job_no=b.job_no  and a.cons_process in (30,31) $deter_id_cond_2 ");
	$color_dyeing_cost=array();
	foreach($fabricnyarn_dyeing as $inf)
	{
		$color_breakdown=explode("__",$inf[csf('color_break_down')]);
		foreach($color_breakdown as $color_data)
		{
			$color_cost=explode("_",$color_data);
			$color_dyeing_cost[$inf[csf('job_no')]][$inf[csf('lib_yarn_count_deter_id')]][$color_cost[0]]=$color_cost[1];
		}
	}
	if ($job_NOs_all!="") {

		$precost_exchange_rate_arr = return_library_array( "select a.job_no,a.exchange_rate  from  wo_pre_cost_mst a, wo_po_details_master b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 $job_NOs_conds ",'job_no','exchange_rate');		
	}
	$conversion_cost=sql_select("select a.job_no,b.lib_yarn_count_deter_id,b.id,sum(a.charge_unit) as charge_unit from  wo_pre_cost_fab_conv_cost_dtls a,wo_pre_cost_fabric_cost_dtls b where  a.fabric_description=b.id and a.job_no=b.job_no  and a.cons_process in(25,26,32,33,34,35,36,37,38,39, 40,60,61,62, 64,67,68, 69,70,71,72, 73,74,75,77,78,79,80,81,82,83,84,85,86,87,88,89,92,93,94,100,125,127,128,129,132,133,134,135,136,137,138,63,65,66,76,90,91) and b.status_active=1 and b.is_deleted=0 $deter_id_cond_2 group by a.job_no,b.lib_yarn_count_deter_id,b.id");
	$conversion_cost_arr=array();
	foreach($conversion_cost as $c_cost)
	{
		$conversion_cost_arr[$c_cost[csf('job_no')]][$c_cost[csf('lib_yarn_count_deter_id')]]=$c_cost[csf('charge_unit')];	
	}


	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $deter_id_cond";

	$data_array_deter=sql_select($sql_deter);
	foreach( $data_array_deter as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$issue_roll_arr=array();
	$sql_issue=sql_select("select barcode_no,reprocess from pro_roll_details where entry_form=71 and status_active=1 and is_deleted=0 $roll_po_id_cond");
	foreach($sql_issue as $inv)
	{
		$issue_roll_arr[$inv[csf('barcode_no')]][$inv[csf('reprocess')]]=$inv[csf('barcode_no')];   
	}


	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name
	from lib_floor_room_rack_dtls b
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	where b.status_active=1 and b.is_deleted=0 and b.company_id=$ext_data[2] and b.store_id=$ext_data[3] and b.location_id=$ext_data[4]";
	// echo $lib_room_rack_shelf_sql;die; 
	$lib_rrsb_arr=sql_select($lib_room_rack_shelf_sql);
	if(!empty($lib_rrsb_arr))
	{
		foreach ($lib_rrsb_arr as $room_rack_shelf_row) {
			$company  = $room_rack_shelf_row[csf("company_id")];
			$floor_id = $room_rack_shelf_row[csf("floor_id")];
			$room_id  = $room_rack_shelf_row[csf("room_id")];
			$rack_id  = $room_rack_shelf_row[csf("rack_id")];
			$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
			$bin_id   = $room_rack_shelf_row[csf("bin_id")];

			if($floor_id!=""){
				$lib_floor_arr[$floor_id] = $room_rack_shelf_row[csf("floor_name")];
			}

			if($floor_id!="" && $room_id!=""){
				$lib_room_arr[$room_id] = $room_rack_shelf_row[csf("room_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!=""){
				$lib_rack_arr[$rack_id] = $room_rack_shelf_row[csf("rack_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!=""){
				$lib_shelf_arr[$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
			}
		}
	}
	else
	{
		$lib_floor_arr[0]="";
		$lib_room_arr[0]="";
		$lib_rack_arr[0]="";
		$lib_shelf_arr[0]="";
	}


	$roll_details_array=array(); 
	foreach($data_array as $row)
	{
		$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
		$roll_details_array[$row[csf("barcode_no")]]['batch_id']=$row[csf("batch_id")];
		$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$row[csf("bodypart_id")];
		$roll_details_array[$row[csf("barcode_no")]]['construction']=$row[csf("construction")];
		$roll_details_array[$row[csf("barcode_no")]]['composition']=$row[csf("composition")];
		$roll_details_array[$row[csf("barcode_no")]]['color_id']=$row[csf("color_id")];
		$roll_details_array[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
		$roll_details_array[$row[csf("barcode_no")]]['dia']=$row[csf("dia")];
		$roll_details_array[$row[csf("barcode_no")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
		$roll_details_array[$row[csf("barcode_no")]]['reject_qnty']=$row[csf("reject_qnty")];
		$roll_details_array[$row[csf("barcode_no")]]['width_type']=$row[csf("width_type")];
		$roll_details_array[$row[csf("barcode_no")]]['grey_sys_id']=$row[csf("grey_sys_id")];
		$roll_details_array[$row[csf("barcode_no")]]['sys_dtls_id']=$row[csf("sys_dtls_id")];
		$roll_details_array[$row[csf("barcode_no")]]['grey_sys_number']=$row[csf("grey_sys_number")];
		$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
		$roll_details_array[$row[csf("barcode_no")]]['prod_id']=$row[csf("product_id")];
		$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
		$roll_details_array[$row[csf("barcode_no")]]['deter_id']=$row[csf("determination_id")];
		$roll_details_array[$row[csf("barcode_no")]]['reprocess']=$row[csf("reprocess")];
		$roll_details_array[$row[csf("barcode_no")]]['prev_reprocess']=$row[csf("prev_reprocess")];
		$roll_details_array[$row[csf("barcode_no")]]['is_sales']=$row[csf("is_sales")];
		$roll_details_array[$row[csf("barcode_no")]]['booking_without_order']=$row[csf("booking_without_order")];
		$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];
	}
	$grey_rate=sql_select("SELECT c.barcode_no,c.rate FROM pro_roll_details c WHERE  c.entry_form=61  and c.status_active=1 and c.is_deleted=0 $barcode_no_conds");
	$roll_details_rate=array();
	foreach($grey_rate as $value)
	{
		$roll_details_rate[$value[csf("barcode_no")]]['rate']=$value[csf("rate")];	
	}

	$production_sql_data=sql_select("SELECT c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty as prod_qty, c.qc_pass_qnty, c.reject_qnty 
	FROM pro_roll_details c 
	WHERE c.entry_form=66 and c.status_active=1 and c.is_deleted=0 $barcode_no_conds");
	$production_data_arr=array();
	foreach($production_sql_data as $value)
	{
		$production_data_arr[$value[csf("barcode_no")]]['prod_qty']=$value[csf("prod_qty")];	
		$production_data_arr[$value[csf("barcode_no")]]['qc_pass_qnty']=$value[csf("qc_pass_qnty")];	
		$production_data_arr[$value[csf("barcode_no")]]['reject_qnty']=$value[csf("reject_qnty")];	
	}
	// echo "<pre>";print_r($production_data_arr);

	$j=1;
	foreach($roll_details_array as $key=>$b_code)
	{
		$IsSalesId = $b_code['is_sales'];
		$job_no=$po_details_array[$b_code['po_breakdown_id']]['job_no_full'];
		$fabric_dyeing_charge=$color_dyeing_cost[$job_no][$b_code['deter_id']][$b_code['color_id']];
		$other_charge=$conversion_cost_arr[$job_no][$b_code['deter_id']];
		$dyeing_charge=($fabric_dyeing_charge+$other_charge)*$precost_exchange_rate_arr[$job_no];

		$prod_qty=$production_data_arr[$key]['prod_qty'];
		$qc_pass_qnty=$production_data_arr[$key]['qc_pass_qnty'];
		$reject_qnty=$production_data_arr[$key]['reject_qnty'];
		$processLoss=($prod_qty-($qc_pass_qnty+$reject_qnty));

		// $grey_qty=$b_code['reject_qnty']+$b_code['qnty'];	
		
		$grey_qty=$prod_qty;	
		if($dyeing_charge_basis==1)
		{
			$grey_rate=($grey_qty/$b_code['qnty'])*$roll_details_rate[$key]['rate'];
		}
		else
		{
			$grey_rate=$roll_details_rate[$key]['rate'];
		}

		$sales_booking_no 	= $sales_arr[$b_code['po_breakdown_id']]["sales_booking_no"];
		$within_group 		= $sales_arr[$b_code['po_breakdown_id']]["within_group"];
		$year 				= $sales_arr[$b_code['po_breakdown_id']]["year"];
		$order_no 			= $sales_arr[$b_code['po_breakdown_id']]["sales_order_no"];

		if ($IsSalesId == 1) 
		{
			if ($within_group == 1) 
			{
				$job_no_mst = $sales_arr[$b_code['po_breakdown_id']]["po_job_no"];
				$buyer_id = $sales_arr[$b_code['po_breakdown_id']]["po_buyer"];
				$cust_buyer_id = "";
			}
			else
			{
				$job_no_mst = "";
				$buyer_id = $sales_arr[$b_code['po_breakdown_id']]["buyer_id"];
				$cust_buyer_id = $sales_arr[$b_code['po_breakdown_id']]["cust_buyer_id"];
			}

			$buyer_name = $buyer_name_array[$buyer_id];
			$cust_buyer_name = $buyer_name_array[$cust_buyer_id];
		}

		?>
		<tr id="tr__<? echo $j;?>" align="center" valign="middle">
			<td width="40" id="sl_<? echo $j;?>" ><? echo $j;?> &nbsp;&nbsp;
				<?
				$issue_cond='';
				if($issue_roll_arr[$key][$b_code['reprocess']]!="") $issue_cond="disabled";
				if($inserted_roll_check_arr[$key][$b_code['reprocess']]!="")
				{
					?>	
					<input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" checked="checked" <? echo $issue_cond; ?> > 
					<?	
				}
				else
				{
					?>
					<input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]">
					<?
				}
				?>
			</td>
			<td width="80" id="barcode_<? echo $j;?>"><? echo $key;?></td>
			<td width="45" id="rollNo_<? echo $j;?>"><? echo $b_code['roll_no'];?></td>
			<td width="60" id="batchNo_<? echo $j;?>" style="word-break:break-all;"><? echo $batch_info_array[$b_code['batch_id']]['batch_no'];?></td>
			<td width="80" id="bodyPart_<? echo $j;?>" style="word-break:break-all;" align="center"><? echo $body_part[$b_code['body_part_id']];?></td>
			<td width="80" id="cons_<? echo $j;?>" style="word-break:break-all;" align="left"><? echo $constructtion_arr[$b_code['deter_id']];?></td>
			<td width="80" id="comps_<? echo $j;?>" style="word-break:break-all;" align="left"><? echo $composition_arr[$b_code['deter_id']];?> </td>
			<td width="70" id="color_<? echo $j;?>"><? echo $color_arr[$b_code['color_id']];?></td>
			<td width="40" id="gsm_<? echo $j;?>"><? echo $b_code['gsm'];?></td>
			<td width="40" id="dia_<? echo $j;?>" style="word-break:break-all;"><? echo $b_code['dia'];?></td>
			<td width="50" id="rollWgt_<? echo $j;?>">
				<input type="text" id="currentQty_1" class="text_boxes_numeric"  value="<? if($inserted_roll_check_arr[$key][$b_code['reprocess']]!="") echo $inserted_roll_arr[$key][$b_code['reprocess']]['qc_pass_qnty'];  else   echo $b_code['qnty'];?>" style="width:35px" name="currentQty[]" <? echo $issue_cond; ?> onChange="fnc_rollQntyChange()" disabled/>
			</td>
			<td width="50" align="right" id="rejectQty_<? echo $j;?>"><? echo $b_code['reject_qnty'];?></td>
			<td width="50" align="right" id="processLoss_<? echo $j;?>"><? echo $processLoss;?></td>
			<td width="50" align="right" id="usedQty_<? echo $j;?>"><? echo number_format(($grey_qty),2,'.','');?></td>
			

			<td width="50" align="center" id="floor_td_to" title="<? echo $inserted_roll_arr[$key][$b_code['reprocess']]['floor']; ?>"><p><?
			
			//echo create_drop_down( "cbo_floor_to_$j", 50,$lib_floor_arr,"", 1, "--Select--", $inserted_roll_arr[$key][$b_code['reprocess']]['floor'], "load_room_rack_self_bin('requires/finish_feb_roll_receive_by_textile_controller*13*cbo_room_to_$j*$j', 'room',room_td_to, document.getElementById('cbo_company_id').value,document.getElementById('cbo_location').value,document.getElementById('cbo_store_name').value,this.value,'','','','','','50','H');storeUpdateUptoDisable();","","","","","","","","cbo_floor_to[]" ,"onchange_void");

			$argument = "'".$j.'_0'."'";	
			echo create_drop_down( "cbo_floor_to_$j", 50,$lib_floor_arr,"", 1, "--Select--", $inserted_roll_arr[$key][$b_code['reprocess']]['floor'], "fn_load_room(this.value, $j); copy_all($argument); reset_room_rack_shelf($j,'cbo_floor_to');storeUpdateUptoDisable();","","","","","","","","cbo_floor_to[]" ,"onchange_void"); 
			?>
            </p></td>
            <td width="50" align="center" id="room_td_to" title="<? echo $inserted_roll_arr[$key][$b_code['reprocess']]['room']; ?>"><p><? 
			//echo create_drop_down( "cbo_room_to_$j", 50,$lib_room_arr,"", 1, "--Select--", $inserted_roll_arr[$key][$b_code['reprocess']]['room'], "load_room_rack_self_bin('requires/finish_feb_roll_receive_by_textile_controller*13*cbo_room_to_$j*$j', 'room',room_td_to, document.getElementById('cbo_company_id').value,document.getElementById('cbo_location').value,document.getElementById('cbo_store_name').value,getElementById('cbo_floor_to_".$j."').value,this.value,'','','','','50','H');storeUpdateUptoDisable();","","","","","","","","cbo_room_to[]","onchange_void" );
			$argument = "'".$j.'_1'."'";
			echo create_drop_down( "cbo_room_to_$j", 50,$lib_room_arr,"", 1, "--Select--", $inserted_roll_arr[$key][$b_code['reprocess']]['room'], "fn_load_rack(this.value, $j); copy_all($argument); reset_room_rack_shelf($j,'cbo_room_to');storeUpdateUptoDisable();","","","","","","","","cbo_room_to[]","onchange_void" );
			


			?>
            </p></td>
            <td width="50" align="center" id="rack_td_to" title="<? echo $inserted_roll_arr[$key][$b_code['reprocess']]['rack']; ?>"><p><?
			//echo create_drop_down( "txt_rack_to_$j", 50,$lib_rack_arr,"", 1, "--Select--", $inserted_roll_arr[$key][$b_code['reprocess']]['rack'], "load_room_rack_self_bin('requires/finish_feb_roll_receive_by_textile_controller*13*cbo_room_to_$j*$j', 'room',room_td_to, document.getElementById('cbo_company_id').value,document.getElementById('cbo_location').value,document.getElementById('cbo_store_name').value,getElementById('cbo_floor_to_".$j."').value,getElementById('cbo_room_to_".$j."').value,this.value,'','','','50','H');storeUpdateUptoDisable();","","","","","","","","txt_rack_to[]","onchange_void" );
			$argument = "'".$j.'_2'."'";				
			echo create_drop_down( "txt_rack_to_$j", 50,$lib_rack_arr,"", 1, "--Select--",$inserted_roll_arr[$key][$b_code['reprocess']]['rack'], "fn_load_shelf(this.value, $j); copy_all($argument); reset_room_rack_shelf($j,'txt_rack_to');storeUpdateUptoDisable();","","","","","","","","txt_rack_to[]","onchange_void" ); 
		
			?>
            </p></td>
            <td width="50" align="center" id="shelf_td_to" title="<? echo $inserted_roll_arr[$key][$b_code['reprocess']]['self']; ?>"><p>
            <? 

            //echo create_drop_down( "txt_shelf_to_$j", 50,$lib_shelf_arr,"", 1, "--Select--", $inserted_roll_arr[$key][$b_code['reprocess']]['self'], "storeUpdateUptoDisable();","","","","","","","","txt_shelf_to[]","onchange_void" ); 
            $argument = "'".$j.'_3'."'";
            echo create_drop_down( "txt_shelf_to_$j", 50,$lib_shelf_arr,"", 1, "--Select--", $inserted_roll_arr[$key][$b_code['reprocess']]['self'], " copy_all($argument); reset_room_rack_shelf($j,'txt_shelf_to');storeUpdateUptoDisable();","","","","","","","","txt_shelf_to[]","onchange_void" );

            ?>
            </p></td>

			<td width="60" id="wideType_<? echo $j;?>"><? echo $fabric_typee[$b_code['width_type']];?></td>
			<td width="45" id="year_<? echo $j;?>" align="center"><? echo $year; ?></td>
			<td width="90" id="job_<? echo $j;?>"><? echo $job_no_mst; ?></td>
			<td width="100" id="bookingNo_<? echo $j;?>"><? echo $batch_info_array[$b_code['batch_id']]['booking_no']; ?></td>
			<td width="60" id="custBuyer_<? echo $j;?>"><? echo $cust_buyer_name; ?></td>
			<td width="50" id="buyer_<? echo $j;?>"><? echo $buyer_name; ?></td>
			<td width="80" id="order_<? echo $j;?>" style="word-break:break-all;" align="center"><? echo $order_no; ?></td>
			<td width="60" id="prodId_<? echo $j;?>"><? echo $b_code['prod_id'];?></td>
			<td width="" id="systemId_<? echo $j;?>" style="word-break:break-all;"><? echo $b_code['grey_sys_number'];?>
				<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $j;?>" value="<? echo $key; ?>"/>

				<input type="hidden" name="productionId[]" id="productionId_<? echo $j;?>" value="<? echo $b_code['grey_sys_id']; ?>"/>
				<input type="hidden" name="productionDtlsId[]" id="productionDtlsId_<? echo $j;?>" value="<? echo $b_code['sys_dtls_id']; ?>"/>
				<input type="hidden" name="deterId[]" id="deterId_1" value="<? echo $b_code['deter_id']; ?>"/>
				<input type="hidden" name="productId[]" id="productId_<? echo $j;?>" value="<? echo $b_code['prod_id']; ?>"/>
				<input type="hidden" name="orderId[]" id="orderId_<? echo $j;?>" value="<? echo $b_code['po_breakdown_id']; ?>"/>
				<input type="hidden" name="rollId[]" id="rollId_<? echo $j;?>" value="<? echo $b_code['roll_id']; ?>"/>
				<input type="hidden" name="rollQty[]" id="rollQty_<? echo $j;?>"  value="<?   if($inserted_roll_check_arr[$key][$b_code['reprocess']]!="") 
				echo $inserted_roll_arr[$key][$b_code['reprocess']]['qc_pass_qnty']; else   echo $b_code['qnty'];?>" />
				<input type="hidden" name="batchID[]" id="batchID_<? echo $j;?>"  value="<? echo $b_code['batch_id']; ?>" />
				<input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value="<? echo $b_code['body_part_id']; ?>"/> 
				<input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value="<? echo $b_code['color_id']; ?>"/> 
				<input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>" 
				value="<? if($inserted_roll_check_arr[$key][$b_code['reprocess']]) echo  $inserted_roll_arr[$key][$b_code['reprocess']]['update_dtls_id']; else echo 0; ?>" /> 
				<input type="hidden" name="transId[]" id="transId_<? echo $j; ?>" 
				value="<? if($inserted_roll_check_arr[$key][$b_code['reprocess']]!="") echo  $inserted_roll_arr[$key][$b_code['reprocess']]['trans_id']; else echo 0; ?>" /> 
				<input type="hidden" name="rollTableId[]" id="rollTableId_<? echo $j; ?>" 
				value="<? if($inserted_roll_check_arr[$key][$b_code['reprocess']]!="") echo  $inserted_roll_arr[$key][$b_code['reprocess']]['roll_table_id']; else echo 0; ?>"/> 
				<input type="hidden" name="wideTypeId[]" id="wideTypeId_<? echo $j; ?>"  value="<? echo $b_code['width_type']; ?>"/> 
				<input type="hidden" name="JobNumber[]" id="JobNumber_<? echo $j; ?>"  value="<? echo $job_no_mst; ?>"/> 
				<input type="hidden" name="buyerId[]" id="buyerId_<? echo $j; ?>"  value="<? echo $buyer_id; ?>"/>
				<input type="hidden" name="dyeingCharge[]" id="dyeingCharge_<? echo $j; ?>"  value="<?php echo $dyeing_charge; ?>"/> 
				<input type="hidden" value="<?php echo $grey_rate; ?>" id="greyRate_<?php echo $j; ?>" name="greyRate[]" />
				<input type="hidden" name="reProcess[]" id="reProcess_<? echo $j; ?>" value="<?php echo $b_code['reprocess']; ?>"/>
				<input type="hidden" name="prereProcess[]" id="prereProcess_<? echo $j; ?>" value="<?php echo $b_code['prev_reprocess']; ?>"/>
				<input type="hidden" name="IsSalesId[]" id="IsSalesId_<? echo $j; ?>" value="<?php echo $b_code['is_sales']; ?>"/>
				<input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_<? echo $j; ?>" value="<?php echo $b_code['booking_without_order']; ?>"/>
				<input type="hidden" name="bookingNumber[]" id="bookingNumber_<? echo $j; ?>" value="<?php echo $b_code['booking_no']; ?>"/>

				<input type="hidden" name="rejectQnty[]" id="rejectQnty_<? echo $j; ?>" value="<?php echo $b_code['reject_qnty']; ?>"/>
				<input type="hidden" name="usedQnty[]" id="usedQnty_<? echo $j; ?>" value="<?php echo $grey_qty; ?>"/>

			</td>  
		</tr>
		<?
		$j++;
	}
	?>
	<input type="hidden" value="<? echo $j - 1; ?>" id="txt_tr_length" name="txt_tr_length"/>
 	<?
}
if($action=="finish_item_details")
{
    $data=explode("_",$data);
	$dyeing_charge_basis=$data[1];
	$is_sales=$data[2];
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");

	$sales_arr=array();
	$sql_sales=sql_select("select id,job_no,within_group, sales_booking_no, po_job_no,po_buyer,buyer_id,customer_buyer,insert_date from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	foreach ($sql_sales as $sales_row) {
		$sales_arr[$sales_row[csf('id')]]["sales_order_no"] 		= $sales_row[csf('job_no')];
		$sales_arr[$sales_row[csf('id')]]["within_group"] 			= $sales_row[csf('within_group')];
		$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 		= $sales_row[csf('sales_booking_no')];
		$sales_arr[$sales_row[csf('id')]]["po_job_no"] 				= $sales_row[csf('po_job_no')];
		$sales_arr[$sales_row[csf('id')]]["po_buyer"] 				= $sales_row[csf('po_buyer')];
		$sales_arr[$sales_row[csf('id')]]["buyer_id"] 				= $sales_row[csf('buyer_id')];
		$sales_arr[$sales_row[csf('id')]]["cust_buyer_id"] 			= $sales_row[csf('customer_buyer')];
		$sales_arr[$sales_row[csf('id')]]["year"] 					= date("Y", strtotime($sales_row[csf("insert_date")]));
	}

	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id where b.status_active=1 and b.is_deleted=0 order by b.id asc";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	//********************************* For Process costing Maintain******************************************************************************

	$precost_exchange_rate_arr = return_library_array( "select job_no,exchange_rate  from  wo_pre_cost_mst",'job_no','exchange_rate');
	$conversion_cost=sql_select("select a.job_no,b.lib_yarn_count_deter_id,b.id,sum(a.charge_unit) as charge_unit from  wo_pre_cost_fab_conv_cost_dtls a,wo_pre_cost_fabric_cost_dtls b where  a.fabric_description=b.id and a.job_no=b.job_no  and a.cons_process in(25,26,32,33,34,35,36,37,38,39, 40,60,61,62, 64,67,68, 69,70,71,72, 73,74,75,77,78,79,80,81,82,83,84,85,86,87,88,89,92,93,94,100,125,127,128,129,132,133,134,135,136,137,138,63,65,66,76,90,91) group by a.job_no,b.lib_yarn_count_deter_id,b.id");
	$conversion_cost_arr=array();
	foreach($conversion_cost as $c_cost)
	{
		$conversion_cost_arr[$c_cost[csf('job_no')]][$c_cost[csf('lib_yarn_count_deter_id')]]=$c_cost[csf('charge_unit')];	
	}
	
	$fabricnyarn_dyeing=sql_select("select a.job_no,b.lib_yarn_count_deter_id, b.id,a.color_break_down from  wo_pre_cost_fab_conv_cost_dtls a,wo_pre_cost_fabric_cost_dtls b where a.fabric_description=b.id and a.job_no=b.job_no  and a.cons_process in (30,31) ");
	$color_dyeing_cost=array();
	foreach($fabricnyarn_dyeing as $inf)
	{
		$color_breakdown=explode("__",$inf[csf('color_break_down')]);
		foreach($color_breakdown as $color_data)
		{
			$color_cost=explode("_",$color_data);
			$color_dyeing_cost[$inf[csf('job_no')]][$inf[csf('lib_yarn_count_deter_id')]][$color_cost[0]]=$color_cost[1];
		}
	}
	
	//******************************************finish************************************************************************************************
	
	$inserted_roll=sql_select("select barcode_no,reprocess from pro_roll_details c where entry_form=317  and status_active=1 and is_deleted=0");
	$inserted_roll_arr=array();
	foreach($inserted_roll as $inf)
	{
		$inserted_roll_arr[$inf[csf('barcode_no')]][$inf[csf('reprocess')]]=$inf[csf('barcode_no')];
	}
	
    $data_array=sql_select("SELECT b.id as dtls_id,b.product_id,b.color_id,b.job_no,b.order_id,b.bodypart_id,b.construction,b.composition,b.batch_id ,b.grey_sys_id,b.sys_dtls_id,b.grey_sys_number,b.determination_id, b.gsm,b.dia,c.qnty,b.width_type,c.barcode_no,c.po_breakdown_id,c.roll_id,c.is_sales, c.roll_no, c.reject_qnty,max(c.reprocess) as reprocess, c.prev_reprocess, c.booking_without_order, c.booking_no 
    FROM pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b,pro_roll_details c 
    WHERE  a.id=b.mst_id and b.id=c.dtls_id and  c.entry_form=67 and a.entry_form=67 and a.sys_number='$data[0]'  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	GROUP BY b.id,b.product_id,b.color_id,b.job_no,b.order_id,b.bodypart_id,b.construction,b.composition,b.batch_id,b.grey_sys_id,b.sys_dtls_id,b.grey_sys_number,b.determination_id, b.gsm,b.dia,c.qnty,b.width_type,c.barcode_no,c.po_breakdown_id,c.roll_id, c.roll_no,c.is_sales, c.reject_qnty,c.prev_reprocess, c.booking_without_order, c.booking_no");

	$batch_ids=$color_ids="";
	$roll_details_array=array(); 
	foreach($data_array as $row)
	{
		$batch_ids.=$row[csf("batch_id")].",";
		$color_ids.= $row[csf("color_id")].",";
		$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
		$roll_details_array[$row[csf("barcode_no")]]['batch_id']=$row[csf("batch_id")];
		$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$row[csf("bodypart_id")];
		$roll_details_array[$row[csf("barcode_no")]]['construction']=$row[csf("construction")];
		$roll_details_array[$row[csf("barcode_no")]]['composition']=$row[csf("composition")];
		$roll_details_array[$row[csf("barcode_no")]]['color_id']=$row[csf("color_id")];
		$roll_details_array[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
		$roll_details_array[$row[csf("barcode_no")]]['dia']=$row[csf("dia")];
		$roll_details_array[$row[csf("barcode_no")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
		$roll_details_array[$row[csf("barcode_no")]]['reject_qnty']=$row[csf("reject_qnty")];
		$roll_details_array[$row[csf("barcode_no")]]['width_type']=$row[csf("width_type")];
		$roll_details_array[$row[csf("barcode_no")]]['grey_sys_id']=$row[csf("grey_sys_id")];
		$roll_details_array[$row[csf("barcode_no")]]['sys_dtls_id']=$row[csf("sys_dtls_id")];
		$roll_details_array[$row[csf("barcode_no")]]['grey_sys_number']=$row[csf("grey_sys_number")];
		$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
		$roll_details_array[$row[csf("barcode_no")]]['prod_id']=$row[csf("product_id")];
		$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
		$roll_details_array[$row[csf("barcode_no")]]['deter_id']=$row[csf("determination_id")];
		$roll_details_array[$row[csf("barcode_no")]]['reprocess']=$row[csf("reprocess")];
		$roll_details_array[$row[csf("barcode_no")]]['prev_reprocess']=$row[csf("prev_reprocess")];
		$roll_details_array[$row[csf("barcode_no")]]['is_sales']=$row[csf("is_sales")];
		$roll_details_array[$row[csf("barcode_no")]]['booking_without_order']=$row[csf("booking_without_order")];
		$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];

		$barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}

	$barcode_arr = array_filter(array_unique($barcode_arr));
	if(count($barcode_arr)>0)
	{
		$all_barcode = implode(",", $barcode_arr);
		$all_barcode_cond=""; $barcodeCond=""; 
		if($db_type==2 && count($barcode_arr)>999)
		{
			$barcode_ref_chunk=array_chunk($barcode_arr,999) ;
			foreach($barcode_ref_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);	
				$barcodeCond.=" barcode_no in($chunk_arr_value) or ";	
			}
			
			$all_barcode_cond.=" and (".chop($barcodeCond,'or ').")";	
		}
		else
		{
			$all_barcode_cond=" and barcode_no in($all_barcode)";	 
		}
	}
	//echo $all_barcode_cond;die;


	$color_arr=array();
	if($batch_ids != "")
	{
		$batch_sql = sql_select("select id, batch_no, booking_no from  pro_batch_create_mst where id in(".rtrim($batch_ids,", ").")");
		foreach($batch_sql as $row)
		{
			$batch_info_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
			$batch_info_array[$row[csf("id")]]['booking_no'] = $row[csf("booking_no")];
		}
	}
	if($color_ids != ""){
		$color_arr=return_library_array( "select id, color_name from lib_color where id in(".rtrim($color_ids,", ").")",'id','color_name');
	}

	
	$grey_rate=sql_select("SELECT c.barcode_no,c.rate FROM pro_roll_details c WHERE  c.entry_form=61  and c.status_active=1 and c.is_deleted=0 $all_barcode_cond");
	$roll_details_rate=array();
	foreach($grey_rate as $value)
	{
		$roll_details_rate[$value[csf("barcode_no")]]['rate']=$value[csf("rate")];	
	}

	$production_sql_data=sql_select("SELECT c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty as prod_qty, c.qc_pass_qnty, c.reject_qnty 
	FROM pro_roll_details c 
	WHERE c.entry_form=66 and c.status_active=1 and c.is_deleted=0 $all_barcode_cond");
	$production_data_arr=array();
	foreach($production_sql_data as $value)
	{
		$production_data_arr[$value[csf("barcode_no")]]['prod_qty']=$value[csf("prod_qty")];	
		$production_data_arr[$value[csf("barcode_no")]]['qc_pass_qnty']=$value[csf("qc_pass_qnty")];	
		$production_data_arr[$value[csf("barcode_no")]]['reject_qnty']=$value[csf("reject_qnty")];	
	}
	// echo "<pre>";print_r($production_data_arr);
	
	$j=1;
 	foreach($roll_details_array as $key=>$b_code)
	{
		if($inserted_roll_arr[$key][$b_code['reprocess']]=="")
		{
			$sales_booking_no 	= $sales_arr[$b_code['po_breakdown_id']]["sales_booking_no"];
			$within_group 		= $sales_arr[$b_code['po_breakdown_id']]["within_group"];
			$year 				= $sales_arr[$b_code['po_breakdown_id']]["year"];
			$order_no 	= $sales_arr[$b_code['po_breakdown_id']]["sales_order_no"];
			if ($is_sales == 1) 
			{
				if ($within_group == 1) {
					$job_no_mst = $sales_arr[$b_code['po_breakdown_id']]["po_job_no"];
					$buyer_id = $sales_arr[$b_code['po_breakdown_id']]["po_buyer"];
					$cust_buyer_id = "";
				}
				else
				{
					$job_no_mst = "";
					$buyer_id = $sales_arr[$b_code['po_breakdown_id']]["buyer_id"];
					$cust_buyer_id = $sales_arr[$b_code['po_breakdown_id']]["cust_buyer_id"];
				}
			}

			$buyer_name = $buyer_name_array[$buyer_id];
			$cust_buyer_name = $buyer_name_array[$cust_buyer_id];

			$job_no=$po_details_array[$b_code['po_breakdown_id']]['job_no_full'];
			$fabric_dyeing_charge=$color_dyeing_cost[$job_no][$b_code['deter_id']][$b_code['color_id']];
			$other_charge=$conversion_cost_arr[$job_no][$b_code['deter_id']];
			$dyeing_charge=($fabric_dyeing_charge+$other_charge)*$precost_exchange_rate_arr[$job_no];

			$prod_qty=$production_data_arr[$key]['prod_qty'];
			$qc_pass_qnty=$production_data_arr[$key]['qc_pass_qnty'];
			$reject_qnty=$production_data_arr[$key]['reject_qnty'];
			$processLoss=($prod_qty-($qc_pass_qnty+$reject_qnty));

			// $grey_qty=$b_code['reject_qnty']+$b_code['qnty'];
			$grey_qty=$prod_qty;

			if($dyeing_charge_basis==1)
			{
				$grey_rate=($grey_qty/$b_code['qnty'])*$roll_details_rate[$key]['rate'];
			}
			else
			{
				$grey_rate=$roll_details_rate[$key]['rate'];
			}
	   ?>
		 <tr id="tr__<? echo $j;?>" align="center" valign="middle">
			<td width="40" id="sl_<? echo $j;?>" ><? echo $j;?> &nbsp;&nbsp;
				<input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" checked="checked">
			</td>
			<td width="80" id="barcode_<? echo $j;?>"><? echo $key;?></td>
			<td width="45" id="rollNo_<? echo $j;?>"><? echo $b_code['roll_no'];?></td>
			<td width="60" id="batchNo_<? echo $j;?>"><? echo $batch_info_array[$b_code['batch_id']]['batch_no'];?></td>
			<td width="80" id="bodyPart_<? echo $j;?>" style="word-break:break-all;" align="center"><? echo $body_part[$b_code['body_part_id']];?></td>
			<td width="80" id="cons_<? echo $j;?>" style="word-break:break-all;" align="left"><? echo $constructtion_arr[$b_code['deter_id']];?></td>
			<td width="80" id="comps_<? echo $j;?>" style="word-break:break-all;" align="left"><? echo $composition_arr[$b_code['deter_id']];?></td>
			<td width="70" id="color_<? echo $j;?>"><? echo $color_arr[$b_code['color_id']];?></td>
			<td width="40" id="gsm_<? echo $j;?>"><? echo $b_code['gsm'];?></td>
			<td width="40" id="dia_<? echo $j;?>"><? echo $b_code['dia'];?></td>
			<td width="50" id="rollWgt_<? echo $j;?>">
            <input type="text" id="currentQty_1" class="text_boxes_numeric" value="<? echo $b_code['qnty'];?>" style="width:35px" name="currentQty[]" onChange="fnc_rollQntyChange()" disabled/>
			</td>
			<td width="50" align="right" id="rejectQty_<? echo $j;?>"><? echo $b_code['reject_qnty'];?></td>
			<td width="50" align="right" id="processLoss_<? echo $j;?>"><? echo $processLoss;?></td>
			<td width="50" align="right" id="usedQty_<? echo $j;?>"><? echo number_format(($grey_qty),2,'.','');?></td>
			


			<td width="50" align="center" id="floor_td_to" class="floor_td_to"><p>
               <? 

               //echo create_drop_down( "cbo_floor_to_$j", 50,$blank_array,"", 1, "--Select--", 0, "load_room_rack_self_bin('requires/finish_feb_roll_receive_by_textile_controller*2*cbo_room_to_$j*$j', 'room',room_td_to, document.getElementById('cbo_company_id').value,document.getElementById('cbo_location').value,document.getElementById('cbo_store_name').value,this.value,'','','','','','50','H');storeUpdateUptoDisable();",0,"","","","","","","cbo_floor_to[]" ,"onchange_void");

                $argument = "'".$j.'_0'."'";
                echo create_drop_down( "cbo_floor_to_$j", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, $j); copy_all($argument); reset_room_rack_shelf($j,'cbo_floor_to');storeUpdateUptoDisable();",0,"","","","","","","cbo_floor_to[]" ,"onchange_void");

                 ?>
            </p></td>

            <td width="50" align="center" id="room_td_to"><p>
            <? 
            	//echo create_drop_down( "cbo_room_to_$j", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","cbo_room_to[]","onchange_void" ); 
            	$argument = "'".$j.'_1'."'";
                echo create_drop_down( "cbo_room_to_$j", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_rack(this.value, $j); copy_all($argument); reset_room_rack_shelf($j,'cbo_room_to');storeUpdateUptoDisable();",0,"","","","","","","cbo_room_to[]","onchange_void" );

            ?>
            </p></td>                   
            <td width="50" align="center" id="rack_td_to"><p>
            <? 
            	//echo create_drop_down( "txt_rack_to_$j", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txt_rack_to[]","onchange_void" ); 
            	$argument = "'".$j.'_2'."'";
                echo create_drop_down( "txt_rack_to_$j", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, $j); copy_all($argument); reset_room_rack_shelf($j,'txt_rack_to');storeUpdateUptoDisable();",0,"","","","","","","txt_rack_to[]","onchange_void" ); 
            ?>
            </p></td>

            <td width="50" align="center" id="shelf_td_to"><p>
            <? 
            	//echo create_drop_down( "txt_shelf_to_$j", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txt_shelf_to[]","onchange_void" ); 
            	$argument = "'".$j.'_3'."'";
                echo create_drop_down( "txt_shelf_to_$j", 50,$blank_array,"", 1, "--Select--", 0, "copy_all($argument); reset_room_rack_shelf($j,'txt_shelf_to');storeUpdateUptoDisable();",0,"","","","","","","txt_shelf_to[]","onchange_void" );
            ?>
            </p></td>

			<!-- <td width="50" id="room_1"><input type="text" id="roomName_<? //echo $j;?>" class="text_boxes_numeric"  style="width:35px" name="roomName[]" onBlur="copy_all('<?  //echo $j."_0"; ?>')"/></td>
			<td width="50" id="rack_1"><input type="text" id="rackName_<? //echo $j;?>" class="text_boxes"  style="width:35px" name="rackName[]" onBlur="copy_all('<?  //echo $j."_1"; ?>')"/></td>
			<td width="50" id="self_1"><input type="text" id="selfName_<? //echo $j;?>" class="text_boxes_numeric"  style="width:35px" name="selfName[]" onBlur="copy_all('<?  //echo $j."_2"; ?>')"/></td> -->

			<td width="60" id="wideType_<? echo $j;?>"><? echo $fabric_typee[$b_code['width_type']];?></td>
			<td width="45" id="year_<? echo $j;?>" align="center"><? echo $year; ?></td>
			<td width="90" id="job_<? echo $j;?>"><? echo $job_no_mst; ?></td>
			<td width="100" id="bookingNo_<? echo $j;?>"><? echo $batch_info_array[$b_code['batch_id']]['booking_no'];?></td>
			<td width="60" id="custBuyer_<? echo $j;?>"><? echo $cust_buyer_name; ?></td>
			<td width="50" id="buyer_<? echo $j;?>"><? echo $buyer_name; ?></td>
			<td width="80" id="order_<? echo $j;?>" style="word-break:break-all;" align="center"><? echo $order_no; ?></td>

			<td width="60" id="prodId_<? echo $j;?>"><? echo $b_code['prod_id'];?></td>
			<td width="" id="systemId_<? echo $j;?>" style="word-break:break-all;"><? echo $b_code['grey_sys_number'];?>
                <input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $j;?>" value="<? echo $key; ?>"/>
                <input type="hidden" name="productionId[]" id="productionId_<? echo $j;?>" value="<? echo $b_code['grey_sys_id']; ?>"/>
                <input type="hidden" name="productionDtlsId[]" id="productionDtlsId_<? echo $j;?>" value="<? echo $b_code['sys_dtls_id']; ?>"/>
                <input type="hidden" name="deterId[]" id="deterId_1" value="<? echo $b_code['deter_id']; ?>"/>
                <input type="hidden" name="productId[]" id="productId_<? echo $j;?>" value="<? echo $b_code['prod_id']; ?>"/>
                <input type="hidden" name="orderId[]" id="orderId_<? echo $j;?>" value="<? echo $b_code['po_breakdown_id']; ?>"/>
                <input type="hidden" name="rollId[]" id="rollId_<? echo $j;?>" value="<? echo $b_code['roll_id']; ?>"/>
                <input type="hidden" name="rollQty[]" id="rollQty_<? echo $j;?>"  value="<? echo $b_code['qnty'];?>" />
                <input type="hidden" name="batchID[]" id="batchID_<? echo $j;?>"  value="<? echo $b_code['batch_id']; ?>" />
                <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value="<? echo $b_code['body_part_id']; ?>"/> 
                <input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value="<? echo $b_code['color_id']; ?>"/> 
                
                <input type="hidden" name="wideTypeId[]" id="wideTypeId_<? echo $j; ?>"  value="<? echo $b_code['width_type']; ?>"/> 
                <input type="hidden" name="JobNumber[]" id="JobNumber_<? echo $j; ?>"  value="<? echo $job_no_mst; ?>"/> 
                <input type="hidden" name="buyerId[]" id="buyerId_<? echo $j; ?>"   value="<? echo $buyer_id; ?>"/>
                <input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>"  value="0" /> 
                <input type="hidden" name="transId[]" id="transId_<? echo $j; ?>" value="0" /> 
                <input type="hidden" name="rollTableId[]" id="rollTableId_<? echo $j; ?>"  value="0"/> 
                <!-- <input type="hidden" value="<? echo $j; ?>" id="txt_tr_length" name="txt_tr_length" /> -->
                <input type="hidden" name="dyeingCharge[]" id="dyeingCharge_<? echo $j; ?>"  value="<?php echo $dyeing_charge; ?>"/> 
                <input type="hidden" value="<?php echo $grey_rate; ?>" id="greyRate_<?php echo $j; ?>" name="greyRate[]" />
                <input type="hidden" name="reProcess[]" id="reProcess_<? echo $j; ?>" value="<?php echo $b_code['reprocess']; ?>"/>
                <input type="hidden" name="prereProcess[]" id="prereProcess_<? echo $j; ?>" value="<?php echo $b_code['prev_reprocess']; ?>"/>
                <input type="hidden" name="IsSalesId[]" id="IsSalesId_<? echo $j; ?>" value="<?php echo $b_code['is_sales']; ?>"/>
                <input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_<? echo $j; ?>" value="<?php echo $b_code['booking_without_order']; ?>"/>
                <input type="hidden" name="bookingNumber[]" id="bookingNumber_<? echo $j; ?>" value="<?php echo $b_code['booking_no']; ?>"/>

                <input type="hidden" name="rejectQnty[]" id="rejectQnty_<? echo $j; ?>" value="<?php echo $b_code['reject_qnty']; ?>"/>
                <input type="hidden" name="usedQnty[]" id="usedQnty_<? echo $j; ?>" value="<?php echo $grey_qty; ?>"/>

			</td>  
		</tr>
		<?
		$j++;
		}
 	}
 	?>
	<input type="hidden" value="<? echo $j - 1; ?>" id="txt_tr_length" name="txt_tr_length"/>
 	<?
 	
}

if($action=="load_php_form_update")
{

	$sql=sql_select("select  a.id,a.recv_number_prefix_num,a.recv_number,a.receive_date,a.challan_no,a.recv_number,a.company_id, a.receive_basis,a.knitting_source,a.knitting_company,a.location_id,a.knitting_location_id,a.store_id from  inv_receive_master a where  a.id=$data ");
	//echo "select  a.id,a.recv_number_prefix_num,a.recv_number,a.receive_date,a.challan_no,a.recv_number,a.company_id, a.receive_basis,a.knitting_source,a.knitting_company,a.location_id,a.store_id from  inv_receive_master a where  a.id=$data ";
	//echo $sql;die;

	$variable_inventory_arr=return_library_array("select company_name, store_method from variable_settings_inventory  where item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1","company_name","store_method");

	foreach($sql as $val)
	{
		//echo "load_drop_down( 'requires/finish_feb_roll_receive_by_textile_controller', '".$val[csf("company_id")]."', 'load_drop_down_location', 'location_td' )\n";

		echo "load_drop_down( 'requires/finish_feb_roll_receive_by_textile_controller', '".$val[csf("company_id")]."', 'load_drop_down_location', 'location_td');\n";
		echo "document.getElementById('cbo_location').value  = '" . ($val[csf("location_id")]) . "';\n";
		
		//echo "load_room_rack_self_bin('requires/finish_feb_roll_receive_by_textile_controller*2', 'store','store_td', '".$val[csf('company_id')]."','".$val[csf('location_id')]."',this.value);\n";

		echo "load_drop_down( 'requires/finish_feb_roll_receive_by_textile_controller', " . $val[csf("company_id")] . "+'_'+" . $val[csf("location_id")] . ", 'load_drop_down_store', 'store_td');\n";
		echo "document.getElementById('cbo_store_name').value  = '" . ($val[csf("store_id")]) . "';\n";

		//echo "load_room_rack_self_bin('requires/finish_feb_roll_receive_by_textile_controller*2', 'store','store_td', '".$val[csf('company_id')]."','".$val[csf('location_id')]."',this.value,'','','','','','','50','H');\n";

		if($val[csf('knitting_source')]==1) $knit_comp=$company_arr[$val[csf('knitting_company')]]; 
		else $knit_comp=$supllier_arr[$val[csf('knitting_company')]];
		echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n"; 
		// echo "document.getElementById('cbo_location').value  = '".($val[csf("location_id")])."';\n"; 
		// echo "document.getElementById('cbo_store_name').value  = '".($val[csf("store_id")])."';\n"; 
		echo "document.getElementById('cbo_knitting_source').value  = '".($val[csf("knitting_source")])."';\n"; 
		echo "document.getElementById('knit_company_id').value  = '".($val[csf("knitting_company")])."';\n";  
		echo "document.getElementById('txt_knitting_company').value  = '".$knit_comp."';\n"; 
		echo "document.getElementById('update_id').value  = '".($val[csf("id")])."';\n";
		echo "document.getElementById('txt_delivery_date').value  = '".change_date_format(($val[csf("receive_date")]))."';\n";
		$location_name=return_field_value("location_name","lib_location"," id=".$val[csf('knitting_location_id')]);
		echo "document.getElementById('txt_knitting_location').value  = '".$location_name."';\n";
		echo "document.getElementById('knit_location_id').value  = '".$val[csf('knitting_location_id')]."';\n";
		echo "document.getElementById('store_update_upto').value  = '".$variable_inventory_arr[$val[csf('company_id')]]."';\n";
		//echo "storeUpdateUptoDisable();\n";
	}
}

if($action=="load_php_form")
{
	$sql=sql_select("select  a.id,a.sys_number,a.company_id,a.knitting_source,a.knitting_company,location_id,a.sys_number_prefix_num,a.delevery_date
	from pro_grey_prod_delivery_mst a
    where  a.entry_form=67   and a.sys_number='$data'  order by sys_number");
	foreach($sql as $val)
	{
		echo "load_drop_down( 'requires/finish_feb_roll_receive_by_textile_controller', '".$val[csf("company_id")]."', 'load_drop_down_location', 'location_td' )\n";

		if($val[csf('knitting_source')]==1) $knit_comp=$company_arr[$val[csf('knitting_company')]]; 
		else $knit_comp=$supllier_arr[$val[csf('knitting_company')]];
		echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n"; 
		echo "document.getElementById('txt_knitting_company').value  = '".$knit_comp."';\n"; 
		echo "document.getElementById('cbo_knitting_source').value  = '".$val[csf("knitting_source")]."';\n"; 
		echo "document.getElementById('knit_company_id').value  = '".$val[csf("knitting_company")]."';\n";
		$location_name=return_field_value("location_name","lib_location"," id=".$val[csf('location_id')]);
		echo "document.getElementById('txt_knitting_location').value  = '".$location_name."';\n";
		echo "document.getElementById('knit_location_id').value  = '".$val[csf('location_id')]."';\n";
		$company_id=$val[csf("company_id")];  
	}
	$dyeing_charge_basis=return_field_value("dyeing_fin_bill","variable_settings_subcon","variable_list=1 and company_id=$company_id");
	echo "document.getElementById('dyeing_charge_basis').value  = '".$dyeing_charge_basis."';\n";
}

if($action=="challan_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

<script>

	function js_set_value(data,id)
	{
	$('#hidden_challan_no').val(data);
	$('#hidden_challan_id').val(id);
	parent.emailwindow.hide();
	}

</script>

</head>
<body>
<div align="center" style="width:760px;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <th>Company</th>
                    <th>Delivery Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Please Enter Challan No</th>
                    <th>
                    <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    <input type="hidden" name="hidden_challan_no" id="hidden_challan_no">  
                    <input type="hidden" name="hidden_challan_id" id="hidden_challan_id">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    <? echo create_drop_down( "cbo_company_id", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',$company_id,"",0); ?>        
                    </td>
                    <td align="center">
                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
					<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
					</td>
                    <td align="center">	
					<?
						$search_by_arr=array(1=>"System No",2=>"Batch No",3=>"Sales Order No");
						$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
						echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
                    ?>
                    </td>     
                    <td align="center" id="search_by_td">				
                    <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value, 'create_challan_search_list_view', 'search_div', 'finish_feb_roll_receive_by_textile_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	if($company_id==0) { echo "Please Select Company First."; die; }
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and delevery_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and delevery_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and a.sys_number like '$search_string'";
		if($search_by==2) $search_field_cond="and d.batch_no like '$search_string'";
		if($search_by==3) $search_field_cond="and e.job_no like '$search_string'";
	}
	
	if($db_type==0) 
	{
		$year_field=" YEAR(a.insert_date) as year";
	}
	else if($db_type==2) 
	{
		$year_field=" to_char(a.insert_date,'YYYY') as year";
	}
	else $year_field="";
	
	$data_array=sql_select("SELECT c.barcode_no,c.is_sales,a.sys_number FROM pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b,pro_roll_details c 
	WHERE  a.id=b.mst_id and b.id=c.dtls_id and  c.entry_form=67 and a.entry_form=67  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	
	$challan_barcode=array();
	$inserted_barcode=array();
	foreach($data_array as $val)
	{
		$challan_barcode[$val[csf('sys_number')]][]=$val[csf('barcode_no')];
		$challan_is_sales[$val[csf('sys_number')]]['is_sales']=$val[csf('is_sales')];
	}	
	$inserted_roll=sql_select("select b.challan_no,a.barcode_no from pro_roll_details a,inv_receive_master b where a.mst_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.entry_form=317 and b.entry_form=317");
	foreach($inserted_roll as $b_id)
	{
		$inserted_barcode[$b_id[csf('challan_no')]][]=$b_id[csf('barcode_no')];	
	}
	
	//$sql="select  a.id,a.sys_number,a.company_id,a.knitting_source,a.knitting_company, a.location_id, a.sys_number_prefix_num,a.delevery_date,$year_field from pro_grey_prod_delivery_mst a where  a.entry_form=67  and  company_id=$company_id $search_field_cond $date_cond order by sys_number";

	$sql="select a.id,a.sys_number,a.company_id,a.knitting_source,a.knitting_company, a.location_id, a.sys_number_prefix_num, a.delevery_date, $year_field
	from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, pro_roll_details c, pro_batch_create_mst d, fabric_sales_order_mst e
	where a.id = b.mst_id and b.barcode_num = c.barcode_no and c.entry_form  = 67 and c.is_sales =1 and c.po_breakdown_id = e.id and b.batch_id = d.id and a.status_active = 1 and b.status_active = 1 and c.status_active = 1  and a.entry_form=67 and a.company_id=$company_id $search_field_cond $date_cond
	group by  a.id,a.sys_number,a.company_id,a.knitting_source,a.knitting_company, a.location_id, a.sys_number_prefix_num,a.delevery_date, a.insert_date
	order by sys_number";



	$result = sql_select($sql);

	$variable_inventory_arr=return_library_array("select company_name, store_method from variable_settings_inventory  where item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1","company_name","store_method");
	//echo "<pre>"; print_r($variable_inventory_arr);die;
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="140">Company</th>
            <th width="80">System No</th>
            <th width="70">Year</th>
            <th width="120">Prod. Source</th>
            <th width="140">Dye/Finishing Company</th>
            <th>Delivery date</th>
        </thead>
	</table>
	<div style="width:740px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">  
        <?
        $i=1;
        foreach ($result as $row)
		{ 	
			if(count($challan_barcode[$row[csf('sys_number')]])-count($inserted_barcode[$row[csf('sys_number')]])>0)
		{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		$knit_comp="&nbsp;";
		$is_sales=$challan_is_sales[$row[csf('sys_number')]]['is_sales'];
		if($row[csf('knitting_source')]==1) $knit_comp=$company_arr[$row[csf('knitting_company')]]; 
		else $knit_comp=$supllier_arr[$row[csf('knitting_company')]];

		$location_name=return_field_value("location_name","lib_location"," id=".$row[csf('location_id')]);

		$data_all=$row[csf('sys_number')]."_".$row[csf('company_id')]."_".$row[csf('knitting_source')]."_".$row[csf('knitting_company')]."_".$knit_comp."_".$dyeing_charge_basis."_".$is_sales."_".$row[csf('location_id')]."_".$location_name."_".$variable_inventory_arr[$row[csf('company_id')]];
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data_all; ?>','<? echo $row[csf('id')]; ?>');"> 
			<td width="40"><? echo $i; ?></td>
			<td width="140"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
			<td width="80" align="center"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
			<td width="70" align="center"><p><? echo $row[csf('year')]; ?></p></td>
			<td width="120"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?>&nbsp;</p></td>
			<td width="140"><p><? echo $knit_comp; ?>&nbsp;</p></td>
			<td align="center"><? echo change_date_format($row[csf('delevery_date')]); ?></td>
		</tr>
		<?
		$i++;
		}
	}
    ?>
    </table>
</div>
<?	
exit();
}

if($action=="check_challan_no")
{
    $data_array=sql_select("SELECT c.barcode_no FROM pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b,pro_roll_details c 
	WHERE  a.id=b.mst_id and b.id=c.dtls_id and  c.entry_form=67 and a.entry_form=67  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.sys_number='$data'");
	$inserted_roll=sql_select("select a.barcode_no from pro_roll_details a,inv_receive_master b where a.mst_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.entry_form=317 and b.entry_form=317 and b.challan_no='$data' ");
	if(count($data_array)-count($inserted_roll)>0){ echo 1;}
	else{ echo 0; }
	exit();	
}

if($action=="update_system_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    ?> 
<script>
	
	function js_set_value(data,id,challan)
	{
	$('#hidden_receive_no').val(data);
	$('#hidden_update_id').val(id);
	$('#hidden_challan_no').val(challan);
	parent.emailwindow.hide();
	}

	function fnc_show()
	{
		if(form_validation('cbo_company_id','Company')==false)
		{
			return; 
		}
		show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_update_search_list_view', 'search_div', 'finish_feb_roll_receive_by_textile_controller', 'setFilterGrid(\'tbl_list_search\',-1);')
	}
</script>

</head>
<body>
<div align="center" style="width:860px;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:860px; margin-left:2px">
		<legend>Receive Number Popup</legend>           
            <table cellpadding="0" cellspacing="0" width="850" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <th>Company</th>
					<th>Search By</th>
                    <th id="search_by_td_up" >Please Enter Receive No</th>
                    <th id="" width="250">Receive Date</th>
                    <th>
                    <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    <input type="hidden" name="hidden_receive_no" id="hidden_receive_no">  
                    <input type="hidden" name="hidden_update_id" id="hidden_update_id">
                    <input type="hidden" name="hidden_challan_no" id="hidden_challan_no">    
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	 <? echo create_drop_down( "cbo_company_id", 170,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',$company_id,"",0); ?>        
                    </td>
					<td align="center">
					<?
						$search_by_arr=array(1=>"Receive No",2=>"Batch No",3=>"Fab. Booking No");
						$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
						echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
					?>
					</td>
                    <td align="center">
                    <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
					</td>
                    <td align="center">	
                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:90px" readonly>To
					<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:90px" readonly>
                    </td>     
            		<td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="fnc_show()" style="width:100px;" />
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



if($action=="create_update_search_list_view")
{
	
	$data = explode("_",$data);

	$company_id =$data[0];
	$cbo_search_by=$data[1];
	$txt_search_common= trim($data[2]);
	$start_date =$data[3];
	$end_date =$data[4];
	$year_id =$data[5];
	
	if($company_id==0) { echo "Please Select Company First."; die; }
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	
	if($db_type==0) 
	{
		$year_field=" YEAR(a.insert_date) as year" ; 
		$year=" and YEAR(insert_date)=$year_id ";
	}
	else if($db_type==2) 
	{
		$year_field=" to_char(a.insert_date,'YYYY') as year";  
		$year=" and to_char(a.insert_date,'YYYY')=$year_id ";
	}
	else $year_field="";


	if($txt_search_common && $cbo_search_by==1)
	{
		$search_cond="and a.recv_number_prefix_num='$txt_search_common' $year ";
	}
	else if($txt_search_common && $cbo_search_by==2)
	{
		$search_cond="and c.batch_no='$txt_search_common' ";
	}
	else if($txt_search_common && $cbo_search_by==3)
	{
		$search_cond="and c.booking_no='$txt_search_common' ";
	}

	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and YEAR(a.insert_date)=$year_id"; else $year_cond="";
	}
	else if ($db_type==2)
	{
		if($year_id!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
	}
	
	/* $sql="select  a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no, a.recv_number,a.company_id, a.knitting_source,a.knitting_company,
	a.receive_date,$year_field
	from  inv_receive_master a
	where a.entry_form=317  and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $receiv_cond $date_cond $year_cond order by  a.recv_number_prefix_num, a.receive_date"; */

	$sql="SELECT a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no, a.recv_number,a.company_id, a.knitting_source,a.knitting_company,
	a.receive_date, b.batch_id, $year_field
	from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c
	where a.entry_form=317 and a.id=b.mst_id and b.batch_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_cond $date_cond $year_cond
	group by a.id,a.recv_number_prefix_num, a.recv_number, a.challan_no, a.recv_number,a.company_id, a.knitting_source,a.knitting_company,
	a.receive_date, b.batch_id, a.insert_date
	order by  a.recv_number_prefix_num, a.receive_date";


	//echo $sql;
	$result = sql_select($sql);
	$recvId="";
	foreach ($result as $row)
    { 
    	$recvId.=$row[csf("id")].","; 
    }
    $recvId=chop($recvId,",");
	$company_arr=return_library_array( "select id, company_name from lib_company where id=$company_id",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0",'id','supplier_name');
	$cons_qnty_sql=sql_select("select mst_id,cons_quantity from inv_transaction where status_active=1 and is_deleted=0 and company_id=$company_id and mst_id in($recvId)");
	foreach($cons_qnty_sql as $vals)
	{
		$cons_qnty_arr[$vals[csf("mst_id")]] +=$vals[csf("cons_quantity")];
	}
	/*echo "<pre>";
	print_r($cons_qnty_arr);die;*/
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="140">Company</th>
            <th width="80">Receive No</th>
            <th width="70">Year</th>
            <th width="120">Dyeing Source</th>
            <th width="140">Dyeing Company</th>
            <th width="120">Receive date</th>
            <th width="120">Receive Qnty</th>
        </thead>
	</table>
	<div style="width:840px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" id="tbl_list_search">  
        <? 
            $i=1;
            foreach ($result as $row)
            {  
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			$knit_comp="&nbsp;";
			if($row[csf('knitting_source')]==1)
			$knit_comp=$company_arr[$row[csf('knitting_company')]]; 
			else
			$knit_comp=$supllier_arr[$row[csf('knitting_company')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('recv_number')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $row[csf('challan_no')]; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="140"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                    <td width="80" align="center"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                    <td width="70" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="120"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?>&nbsp;</p></td>
                    <td width="140"><p><? echo $knit_comp; ?>&nbsp;</p></td>
                    <td width="120"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="120" align="right"><? echo $cons_qnty_arr[$row[csf("id")]]; ?></td>
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

if($action=="finish_delivery_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$txt_challan_no=$data[1];
	$update_id=$data[2];
	$storeId=$data[4];
	$location=$data[5];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}

	$location_and_store=sql_select("select location_id, store_id from inv_receive_master where id = $update_id and is_deleted=0 and status_active=1");
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$batch_arr=return_library_array( "select id,batch_no from  pro_batch_create_mst",'id','batch_no');
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$job_array=array();
	$job_sql="select a.job_no_prefix_num, a.job_no, b.id, b.po_number,a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
	}
	
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}

	/*
	|--------------------------------------------------------------------------
	| for floor, room, rack and shelf disable
	|--------------------------------------------------------------------------
	|
	*/
	$lib_floor_arr=array(); $lib_room_arr=array(); $lib_rack_arr=array(); $lib_shelf_arr=array();
	 $lib_bin_arr=array();
	$lib_room_rack_shelf_sql = "SELECT b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name, f.floor_room_rack_name bin_name
	FROM lib_floor_room_rack_dtls b
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0 
	WHERE b.status_active=1 and b.is_deleted=0 and b.company_id=".$company." and b.store_id=".$storeId." and b.location_id=".$location."";
	//echo $lib_room_rack_shelf_sql;die;
	$lib_rrsb_arr=sql_select($lib_room_rack_shelf_sql);
	if(!empty($lib_rrsb_arr))
	{
		foreach ($lib_rrsb_arr as $room_rack_shelf_row)
		{
			//$companyId  = $room_rack_shelf_row[csf("company_id")];
			$floorId = $room_rack_shelf_row[csf("floor_id")];
			$roomId  = $room_rack_shelf_row[csf("room_id")];
			$rackId  = $room_rack_shelf_row[csf("rack_id")];
			$shelfId = $room_rack_shelf_row[csf("shelf_id")];
			$binId   = $room_rack_shelf_row[csf("bin_id")];
	
			if($floorId!="")
			{
				$lib_floor_arr[$floorId] = $room_rack_shelf_row[csf("floor_name")];
			}
	
			if($floorId!="" && $roomId!="")
			{
				$lib_room_arr[$roomId] = $room_rack_shelf_row[csf("room_name")];
			}
	
			if($floorId!="" && $roomId!="" && $rackId!="")
			{
				$lib_rack_arr[$rackId] = $room_rack_shelf_row[csf("rack_name")];
			}
	
			if($floorId!="" && $roomId!="" && $rackId!="" && $shelfId!="")
			{
				$lib_shelf_arr[$shelfId] = $room_rack_shelf_row[csf("shelf_name")];
			}

			if($floorId!="" && $roomId!="" && $rackId!="" && $shelfId!="" && $binId!="")
			{
				$lib_bin_arr[$binId] = $room_rack_shelf_row[csf("bin_name")];
			}
		}
	}
	else
	{
		$lib_floor_arr[0]="";
		$lib_room_arr[0]="";
		$lib_rack_arr[0]="";
		$lib_shelf_arr[0]="";
		$lib_bin_arr[0]="";
	}
	//end
	
	?>
    <div style="width:1010px;">
    	<table width="1010" cellspacing="0" align="center" border="0">
			<tr>
				<td align="center" style="font-size:x-large"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr>
				<td align="center">
					<?
 					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company"); 
					foreach ($nameArray as $result)
					{ 
											 
						 echo $result[csf('plot_no')].', '.$result[csf('level_no')].', '.$result[csf('road_no')].', '.$result[csf('block_no')].', '.$result[csf('city')].', '.$result[csf('zip_code')].', '.$result[csf('province')].', '.$country_arr[$result[csf('country_id')]]; ?><br> 
						 <? echo $result[csf('email')];?> 
						 <? echo $result[csf('website')];
					}
					?>
				</td>
			</tr>
			
			<tr>
				<td align="center" style="font-size:16px"><strong><u>Finish Fabric Roll Receive</u></strong></td>
			</tr>
            <tr>
				<td align="center" style="font-size:18px"><strong><u>Receive No <? echo $txt_challan_no; ?></u></strong></td>
			</tr>
        </table> 
        <br>
        <?
			$sql_data= sql_select("select a.challan_no,a.recv_number,a.company_id,a.knitting_source,a.knitting_company,a.receive_date,a.store_id,a.location_id
	        from  inv_receive_master a
	        where a.entry_form=317 and a.company_id=$company and a.recv_number='$txt_challan_no'");
		?>
        
		<table width="1110" cellspacing="0" align="center" border="0">
			<tr>
				<!-- <td style="font-size:16px; font-weight:bold;" width="100">Challan No</td> -->
				<td style="font-size:16px; font-weight:bold;" width="100">Receive ID</td>
                <td width="200">:&nbsp;<? echo $sql_data[0][csf('recv_number')]; ?></td>
                <td style="font-size:16px; font-weight:bold;" width="150">Receive Date</td>
                <td width="200"  align=""><? echo $data[3]//echo change_date_format($sql_data[0][csf('receive_date')]); ?></td>
                <td style="font-size:16px; font-weight:bold;" width="100"> Delivery Challan</td>
                <td width="200">:&nbsp;<? echo $sql_data[0][csf('challan_no')]; ?></td>
                <!--<td style="font-size:16px; font-weight:bold;" width="100">Company</td>
                <td width="200">:&nbsp;<? echo $company_array[$company]['name']; ?></td>-->
			</tr>
            <tr>
				<td style="font-size:16px; font-weight:bold;" width="100">Prod. Source</td>
                <td width="200">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
                <td style="font-size:16px; font-weight:bold;" width="150">Dye/Finishing Company</td>
                <td width="200">:&nbsp;
                 <?
				 if($sql_data[0][csf('knitting_source')]==1) echo  $company_array[$sql_data[0][csf('knitting_company')]]['name'];
				 else  echo $supplier_arr[$sql_data[0][csf('knitting_company')]];
				 ?>
				</td>
				<td style="font-size:16px; font-weight:bold;" width="100">Location</td>
                <td width="200">:&nbsp;<? echo $location_arr[$location_and_store[0][csf('location_id')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="100">Store Name</td>
                <td width="200">:&nbsp;<? echo $store_arr[$location_and_store[0][csf('store_id')]]; ?></td>
			</tr>
            <tr>
				<td width="" id="barcode_img_id"  colspan="2"></td>	
             	
		    </tr>
		</table>
        <br>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="90">Barcode No</th>
                    <th width="60">Batch No</th>
                    <th width="70">Order No</th>
                    <th width="70">Buyer <br> Job</th>
                    <!--<th width="70">Knitting Source</th>-->
                    <th width="70">Prod. Source</th>
                    <th width="90">Dye/Finishing Company</th>
                    <th width="50">Product Id</th>
                    <th width="70">Body Part</th>
                    <th width="130">Fabric Type</th>
                    <th width="70"> Color</th>
                   
                    <th width="50">GSM</th>
                    <th width="40">Dia</th>
                    <th width="60">Dia/Width Type</th>
                    <th width="40">Room</th>
                    <th width="60">Rack<br> Self</th>
                    <th width="40">Roll No</th>
                    <th width="40">Grey Wgt.</th>
                    <th width="40">Process Loss</th>
                    <th width="40">Reject Qty</th>   	
                    <th>QC Pass Qty</th>
                </tr>
            </thead>
            <?		
			
		
			$i=1; $tot_qty=0;$tot_reject_qty=0; $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
			$sql_update=sql_select("SELECT b.id,b.order_id,b.body_part_id,b.batch_id,b.width,b.dia_width_type,b.fabric_description_id,b.gsm,
			b.prod_id,b.color_id,b.room,b.rack_no,b.shelf_no,c.roll_no,c.barcode_no,c.qc_pass_qnty,c.reject_qnty, c.is_sales, c.po_breakdown_id
			from pro_finish_fabric_rcv_dtls b,pro_roll_details c
			where b.id=c.dtls_id and b.mst_id=$update_id and  c.entry_form=317 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by c.roll_no");

			$barcode_NOs="";
			foreach($sql_update as $row)
			{
				$barcode_NOs.=$row[csf("barcode_no")].",";
			}
			$barcode_Nos_all=rtrim($barcode_NOs,","); 
			$barcode_Nos_alls=explode(",",$barcode_Nos_all);
			$barcode_Nos_alls=array_chunk($barcode_Nos_alls,999); 
			$barcode_no_conds=" and";
			foreach($barcode_Nos_alls as $dtls_id)
			{
				if($barcode_no_conds==" and")  $barcode_no_conds.="(c.barcode_no in(".implode(',',$dtls_id).")"; else $barcode_no_conds.=" or c.barcode_no in(".implode(',',$dtls_id).")";
			}
			$barcode_no_conds.=")";

			$data_array=sql_select("SELECT a.id, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id,b.color_id ,b.batch_id, b.fabric_description_id,b.body_part_id,b.dia_width_type, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.reject_qnty, c.qc_pass_qnty FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=66 and c.entry_form=66 and c.status_active=1 and c.is_deleted=0 $barcode_no_conds");
			$roll_details_array=array(); $barcode_array=array(); 
			foreach($data_array as $row)
			{				
				$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
				$roll_details_array[$row[csf("barcode_no")]]['knitting_source']=$knitting_source[$row[csf("knitting_source")]];
				$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id']=$row[csf("knitting_company")];
				
				if($row[csf("knitting_source")]==1)
				{
					$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_array[$row[csf("knitting_company")]]['name'];
				}
				else if($row[csf("knitting_source")]==3)
				{
					$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
				}

				$roll_details_array[$row[csf("barcode_no")]]['prod_qty']=$row[csf("qnty")];	
				$roll_details_array[$row[csf("barcode_no")]]['qc_pass_qnty']=$row[csf("qc_pass_qnty")];	
				$roll_details_array[$row[csf("barcode_no")]]['reject_qnty']=$row[csf("reject_qnty")];	
			}
			// echo "<pre>";print_r($roll_details_array);

			$poIDs="";
			foreach($data_array as $row)
			{
				$poIDs.=$row[csf("po_breakdown_id")].",";
			}
			$poIDs_all=rtrim($poIDs,","); 
			$poIDs_alls=explode(",",$poIDs_all);
			$poIDs_alls=array_chunk($poIDs_alls,999); 
			$sales_order_cond=" and";
			foreach($poIDs_alls as $dtls_id)
			{
				if($sales_order_cond==" and")  $sales_order_cond.="(id in(".implode(',',$dtls_id).")"; else $sales_order_cond.=" or id in(".implode(',',$dtls_id).")";
			}
			$sales_order_cond.=")";

			$sales_arr=array();
			$sql_sales=sql_select("SELECT id,job_no,within_group,sales_booking_no,po_job_no,po_buyer,buyer_id,insert_date from fabric_sales_order_mst where status_active=1 and is_deleted=0 $sales_order_cond");
			foreach ($sql_sales as $sales_row) 
			{
				$sales_arr[$sales_row[csf('id')]]["sales_order_no"] 	= $sales_row[csf('job_no')];
				$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
				$sales_arr[$sales_row[csf('id')]]["po_job_no"] 			= $sales_row[csf('po_job_no')];
				$sales_arr[$sales_row[csf('id')]]["po_buyer"] 			= $sales_row[csf('po_buyer')];
				$sales_arr[$sales_row[csf('id')]]["buyer_id"] 			= $sales_row[csf('buyer_id')];
			}
			 
			foreach($sql_update as $row)
			{
				$buyer_job="";$order_no="";
				$IsSalesId = $row[csf('is_sales')];
				if ($IsSalesId == 1) 
				{
					$within_group = $sales_arr[$row[csf('po_breakdown_id')]]["within_group"];
					if ($within_group == 1) 
					{
						$job_no_mst = $sales_arr[$row[csf('po_breakdown_id')]]["po_job_no"];
						$buyer_id = $sales_arr[$row[csf('po_breakdown_id')]]["po_buyer"];
					}
					else
					{
						$job_no_mst = "";
						$buyer_id = $sales_arr[$row[csf('po_breakdown_id')]]["buyer_id"];
					}

					$order_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["sales_order_no"];
					$buyer_job = $buyer_array[$buyer_id]."<br>".$job_no_mst;
				}
				else
				{
					$order_no=$job_array[$row[csf('order_id')]]['po'];
					$buyer_job=$buyer_array[$job_array[$row[csf('order_id')]]['buyer']]."<br>".$job_array[$row[csf('order_id')]]['job'];
				}
				$prod_qty=$roll_details_array[$row[csf('barcode_no')]]['prod_qty'];
				$qc_pass_qnty=$roll_details_array[$row[csf('barcode_no')]]['qc_pass_qnty'];
				$reject_qnty=$roll_details_array[$row[csf('barcode_no')]]['reject_qnty'];
				$processLoss=($prod_qty-($qc_pass_qnty+$reject_qnty));
				?>
            	<tr>
                    <td width="30"><? echo $i; ?></td>
                    <td width="90" style="word-break:break-all;"><? echo $row[csf('barcode_no')]; ?></td>
                    <td width="60" style="word-break:break-all;" align="center"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
                    <td width="90" style="word-break:break-all;" align="center"><? echo $order_no; ?></td>
                    <td width="60" style="word-break:break-all;" align="center" title="order id: <? echo $row[csf('order_id')]; ?>"><? echo $buyer_job; ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['knitting_source']; ?></td>
             
                    <td width="100" style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['knitting_company']; ?></td>
                   <!-- <td width="70" style="word-break:break-all;"><?echo $knitting_source[$row[csf("knitting_source")]]; ?></td>-->
                    <td width="70" align="center"><? echo $row[csf("prod_id")]; ?></td>
                    <td width="50" style="word-break:break-all;" align="center"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
                    <td width="70" style="word-break:break-all;" align="center"><? echo $composition_arr[$row[csf('fabric_description_id')]]; ?></td>
                    <td width="60" style="word-break:break-all;" align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                    <td width="50" align="center" ><? echo  $row[csf('gsm')];  ?></td>
                    <td width="40" align="center"><? echo $row[csf('width')]; ?></td>
                    
                    <td width="50" style="word-break:break-all;" align="center"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
                    <td width="40" align="center"><? echo $lib_room_arr[$row[csf("room")]]; ?></td>
                    <td width="40" style="word-break:break-all;" align="right"><? echo $lib_rack_arr[$row[csf('rack_no')]]."<br>".$lib_shelf_arr[$row[csf('shelf_no')]]; ?></td>
                    <td width="40" align="center"><? echo $row[csf("roll_no")]; ?></td>
                    <td width="40" align="right"><? echo $prod_qty; ?></td>
                    <td width="40" align="right"><? echo $processLoss; ?></td>
                    <td width="40" style="word-break:break-all;" align="right"><? echo $row[csf('reject_qnty')]; ?></td>
                    <td align="right"><? echo number_format($row[csf('qc_pass_qnty')],2); ?></td>
                </tr>
            	<?
				$tot_grey_wgt+=$prod_qty;
				$tot_processLoss+=$processLoss;
				$tot_qty+=$row[csf('qc_pass_qnty')];
				$tot_reject_qty+=$row[csf('reject_qnty')];
				$i++;
			}
			?>
            <tr> 
                <td align="right" colspan="17"><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_grey_wgt,2,'.',''); ?></td>
                <td align="right"><? //echo number_format($tot_processLoss,2,'.',''); ?></td>
                <td align="right"><? echo number_format($tot_reject_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>
		</table>
	</div>
    <? echo signature_table(246, $company, "1210px"); ?>
   	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 40,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
	<?
	exit();
}



if($action=="fabric_details_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$txt_challan_no=$data[1];
	$update_id=$data[2];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}

	$location_and_store=sql_select("select location_id, store_id from inv_receive_master where id = $update_id and is_deleted=0 and status_active=1");
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$batch_arr=return_library_array( "select id,batch_no from  pro_batch_create_mst",'id','batch_no');
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$job_array=array();
	$job_sql="select a.job_no_prefix_num, a.job_no, b.id, b.po_number,a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
	}
	//print_r($job_array);
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}
	
?>
    <div style="width:1010px;">
    	<table width="1010" cellspacing="0" align="center" border="0">
			<tr>
				<td align="center" style="font-size:x-large"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			
			<tr>
				<td align="center">
					<?
 					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company"); 
					foreach ($nameArray as $result)
					{ 
											 
						 echo $result[csf('plot_no')].', '.$result[csf('level_no')].', '.$result[csf('road_no')].', '.$result[csf('block_no')].', '.$result[csf('city')].', '.$result[csf('zip_code')].', '.$result[csf('province')].', '.$country_arr[$result[csf('country_id')]]; ?><br> 
						 <? echo $result[csf('email')];?> 
						 <? echo $result[csf('website')];
					}
					?>
				</td>
			</tr>

			<tr>
				<td align="center" style="font-size:16px"><strong><u>Finish Fabric Roll Receive</u></strong></td>
			</tr>
            <tr>
				<td align="center" style="font-size:18px"><strong><u>Receive No <? echo $txt_challan_no; ?></u></strong></td>
			</tr>
        </table> 
        <br>
        <?
			$sql_data= sql_select("select a.challan_no,a.recv_number,a.company_id,a.knitting_source,a.knitting_company,a.receive_date
	        from  inv_receive_master a
	        where a.entry_form=317 and a.company_id=$company");
		
		?>
        
        
		<table width="1110" cellspacing="0" align="center" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="100">Challan No</td>
                <td width="200">:&nbsp;<? echo $sql_data[0][csf('challan_no')]; ?></td>
                <td style="font-size:16px; font-weight:bold;" width="150">Receive Date</td>
                <td width="200"  align="">:&nbsp;<? echo change_date_format($data[3])//echo change_date_format($sql_data[0][csf('receive_date')]); ?></td>
                <td style="font-size:16px; font-weight:bold;" width="100">Company</td>
                <td width="200">:&nbsp;<? echo $company_array[$company]['name']; ?></td>
			</tr>
            <tr>
				<td style="font-size:16px; font-weight:bold;" width="100">Prod. Source</td>
                <td width="200">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
                <td style="font-size:16px; font-weight:bold;" width="150">Dye/Finishing Company</td>
                <td width="200">:&nbsp;
                 <?
				 if($sql_data[0][csf('knitting_source')]==1) echo  $company_array[$sql_data[0][csf('knitting_company')]]['name'];
				 else  echo $supplier_arr[$sql_data[0][csf('knitting_company')]];
				 ?>
				</td>
                <td style="font-size:16px; font-weight:bold;" width="100">Location</td>
				<td width="200">:&nbsp;<? echo $location_arr[$location_and_store[0][csf('location_id')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="100">Store Name</td>
				<td width="200">:&nbsp;<? echo $store_arr[$location_and_store[0][csf('store_id')]]; ?></td>
			</tr>
            <tr>
			<td width="" id="barcode_img_id"  colspan="2"></td>	 
		    </tr>
		</table>
        <br>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="60">Batch No</th>
                    <th width="80">Order No</th>
                    <th width="100">Buyer <br> Job</th>
                    <th width="50">Product Id</th>
                    <th width="80">Body Part</th>
                    <th width="150">Fabric Type</th>
                    <th width="70"> Color</th>
                    <th width="50">GSM</th>
                    <th width="40">Dia</th>
                    <th width="70">Dia/Width Type</th>
                    <th width="40">Room</th>
                    <th width="60">Rack</th>
                     <th width="40">Self</th>
                    <th width="40">Roll No</th>
                    <th width="40">Reject Qty</th>   	
                    <th>QC Pass Qty</th>
                </tr>
            </thead>
            <?
			
	$data_array=sql_select("SELECT a.id, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id,b.color_id ,b.batch_id, b.fabric_description_id,b.body_part_id,b.dia_width_type, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.reject_qnty FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=66 and c.entry_form=66 and c.status_active=1 and c.is_deleted=0");
	$roll_details_array=array(); $barcode_array=array(); 
	foreach($data_array as $row)
	{
		
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source']=$knitting_source[$row[csf("knitting_source")]];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id']=$row[csf("knitting_company")];
		
		if($row[csf("knitting_source")]==1)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_array[$row[csf("knitting_company")]]['name'];
		}
		else if($row[csf("knitting_source")]==3)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
		}
	
	}
		
			 $i=1; $tot_qty=0;$tot_reject_qty=0; $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
			
			 $sql_update=sql_select("select  b.order_id,b.body_part_id,b.batch_id,b.width,b.dia_width_type,b.fabric_description_id,b.gsm,
			 b.prod_id,b.color_id,b.room,b.rack_no,b.shelf_no,count(c.id) as no_of_roll,sum(c.qc_pass_qnty) as qc_pass_qnty,
			 sum(c.reject_qnty) as reject_qnty
			 from pro_finish_fabric_rcv_dtls b,pro_roll_details c
			 where  b.id=c.dtls_id and b.mst_id=$update_id and  c.entry_form=317 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 
			 and c.is_deleted=0 
			 group by b.order_id,b.body_part_id,b.batch_id,b.width,b.dia_width_type,b.fabric_description_id,b.gsm,
			 b.prod_id,b.color_id,b.room,b.rack_no,b.shelf_no");
			 
				foreach($sql_update as $row)
				{
				
				?>
                	<tr>
                        <td width="30"><? echo $i; ?></td>
                        <td width="60" style="word-break:break-all;" align="center"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
                        <td width="80" style="word-break:break-all;" align="center"><? echo $job_array[$row[csf('order_id')]]['po']; ?></td>
                        <td width="100" style="word-break:break-all;" align="center"><? echo $buyer_array[$job_array[$row[csf('order_id')]]['buyer']]."<br>".$job_array[$row[csf('order_id')]]['job']; ?></td>
                        <td width="50" align="center"><? echo $row[csf("prod_id")]; ?></td>
                        <td width="80" style="word-break:break-all;" align="center"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
                        <td width="150" style="word-break:break-all;" align="center"><? echo $composition_arr[$row[csf('fabric_description_id')]]; ?></td>
                        <td width="70" style="word-break:break-all;" align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                        <td width="50" align="center" ><? echo  $row[csf('gsm')];  ?></td>
                        <td width="40" align="center"><? echo $row[csf('width')]; ?></td>
                        <td width="70" style="word-break:break-all;" align="center"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
                        <td width="40" align="center"><? echo $row[csf("room")]; ?></td>
                        <td width="40" style="word-break:break-all;" align="right"><? echo $row[csf('rack_no')]; ?></td>
                        <td width="40" align="center"><? echo $row[csf("shelf_no")]; ?></td>
                        <td width="40" align="center"><? echo $row[csf("roll_no")]; ?></td>
                        <td width="40" style="word-break:break-all;" align="right"><? echo $row[csf('reject_qnty')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('qc_pass_qnty')],2); ?></td>
                    </tr>
                <?
					$tot_qty+=$row[csf('qc_pass_qnty')];
					$tot_reject_qty+=$row[csf('reject_qnty')];
					$i++;
				}
			?>
            <tr> 
                <td align="right" colspan="15"><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_reject_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>
          
		</table>
	</div>
    <? echo signature_table(16, $company, "1210px"); ?>
     	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 40,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
    <?
	
exit();
}



?>

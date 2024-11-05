<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_store_to")
{
	$data=explode("_",$data);
	$company=$data[0];

	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
	$store_location_id = $userCredential[0][csf('store_location_id')];
	if ($store_location_id != '') {$store_location_credential_cond = " and a.id in($store_location_id)";} else { $store_location_credential_cond = "";}

	echo create_drop_down( "cbo_store_name_to", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.company_id=$company and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );
	exit();
}
if ($action=="load_drop_down_floor_to")
{
	$data=explode("_",$data);
	$store=$data[0];
	$company=$data[1];
	echo create_drop_down( "cbo_floor_to", 150, "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store' and a.company_id='$company' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name", "floor_id,floor_room_rack_name", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/grey_fabric_transfer_v2_acknowledgement_controller',this.value+'_'+$company+'_'+$store, 'load_drop_down_room_to', 'room_td' );",0,"","","","","","","cboFloorTo[]" );
	exit();
}
if ($action=="load_drop_down_room_to")
{
	$data=explode("_",$data);
	$floorId=$data[0];
	$company=$data[1];
	$store=$data[2];
	echo create_drop_down( "cbo_room_to", 150, "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.store_id=$store and a.company_id='$company' and b.floor_id=$floorId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "-- Select Room --", 0, "load_drop_down( 'requires/grey_fabric_transfer_v2_acknowledgement_controller',this.value+'_'+$company+'_'+$store+'_'+$floorId, 'load_drop_down_rack_to', 'rack_td' );",0,"","","","","","","cboRoomTo[]" );
	exit();
}
if ($action=="load_drop_down_rack_to")
{
	$data=explode("_",$data);
	$roomId=$data[0];
	$company=$data[1];
	$store=$data[2];
	$floorId=$data[3];
	echo create_drop_down( "txt_rack_to", 150, "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and b.store_id=$store and a.company_id='$company' and b.floor_id=$floorId and room_id=$roomId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "-- Select Rack --", 0, "load_drop_down( 'requires/grey_fabric_transfer_v2_acknowledgement_controller',this.value+'_'+$company+'_'+$store+'_'+$floorId+'_'+$roomId, 'load_drop_down_shelf_to', 'shelf_td' );",0,"","","","","","","txtRackTo[]" );
	exit();
}
if ($action=="load_drop_down_shelf_to")
{
	$data=explode("_",$data);
	$rackId=$data[0];
	$company=$data[1];
	$store=$data[2];
	$floorId=$data[3];
	$roomId=$data[4];
	echo create_drop_down( "txt_shelf_to", 150, "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and b.store_id=$store and a.company_id='$company' and b.floor_id=$floorId and room_id=$roomId and rack_id=$rackId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "-- Select Shelf --", 0, "",0,"","","","","","","txtShelfTo[]" );
	exit();
}

if($action=='itemTransfer_popup')
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,"1");
	//echo load_html_head_contents("Composition Info","../../../../", 1, 1, '','1','');
	?>
	<script>
		function js_set_value(data)
		{
			$('#transfer_id').val(data);
			parent.emailwindow.hide();

		}
	</script>
	<?
	$company=str_replace("'","",$company);

	$location=str_replace("'","",$location);
	$store=str_replace("'","",$store);
	if($location!=0) $location_cond	=" and a.to_location_id = '$location'";
	if($store!=0) $store_cond	=" and b.to_store = '$store'";
	if($db_type==0) $year_field="YEAR(a.insert_date)";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY')";
	else $year_field="";
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

	$sql="SELECT a.id, $year_field as year, a.transfer_prefix_number, a.transfer_system_id, a.challan_no, a.to_company, a.transfer_date, a.transfer_criteria, a.item_category,a.to_location_id, b.to_store
	from inv_item_transfer_mst a ,inv_item_transfer_dtls b
	where a.item_category=13 and a.to_company=$company $location_cond $store_cond and a.id=b.mst_id and a.entry_form in(13) and a.transfer_criteria in(1,2,4) and a.is_acknowledge=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by a.id,a.insert_date, a.transfer_prefix_number, a.transfer_system_id, a.challan_no, a.to_company, a.transfer_date, a.transfer_criteria, a.item_category,a.to_location_id, b.to_store order by a.transfer_system_id desc";
	//echo $sql;die;
	$arr=array(2=>$company_arr,3=>$store_arr,5=>$item_transfer_criteria,6=>$item_category);
	echo  create_list_view("tbl_list_search", "Challan No,Year,Company,Store,Transfer Date,Transfer Criteria,Item Category", "120,40,120,120,70,120","760","250",0, $sql, "js_set_value", "id", "", 1, "0,0,to_company,to_store,0,transfer_criteria,item_category", $arr, "transfer_system_id,year,to_company,to_store,transfer_date,transfer_criteria,item_category", '',"setFilterGrid('tbl_list_search',-1)",'0,0,0,0,3,0,0','');

	?>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?

	echo "<input type='hidden' id='transfer_id' value='' />";
	exit();
}


if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("SELECT a.transfer_system_id, a.challan_no, a.company_id, a.location_id, a.transfer_date, a.transfer_criteria, a.item_category, a.to_company, a.to_location_id,b.to_store 
	from inv_item_transfer_mst a,inv_item_transfer_dtls b 
	where a.id=b.mst_id and a.id='$data' 
	group by a.transfer_system_id, a.challan_no, a.company_id, a.location_id, a.transfer_date, a.transfer_criteria, a.item_category, a.to_company, a.to_location_id,b.to_store");
	foreach ($data_array as $row)
	{
		if($row[csf("to_store")]>0){
			$loc_com=$row[csf("to_company")];
			echo "load_drop_down('requires/grey_fabric_transfer_v2_acknowledgement_controller','".$loc_com."', 'load_drop_down_store_to', 'to_store_td' );\n";
			echo "document.getElementById('cbo_store_name_to').value 		= '".$row[csf("to_store")]."';\n";
		}

		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";

		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";

		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_transfer_acknowledgement',1,1);\n";
		exit();

	}
}


if($action=="show_dtls_list_view")
{
	$data_ref=explode("**",$data);
	$challan_id=$data_ref[0];
	$data_array=sql_select("SELECT a.transfer_criteria, a.company_id, a.to_company, a.location_id, a.to_location_id, b.from_order_id, b.to_order_id, b.id, b.mst_id, b.id,  b.from_store, b.to_store, b.floor_id, b.room, b.rack, b.shelf, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.color_id, b.uom, b.batch_id , b.fabric_shade, b.to_batch_id,b.trans_id, b.to_trans_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b
	where a.id=$data_ref[0] and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.active_dtls_id_in_transfer=1");
	$toBatchId="";$toOrderId="";$fabricShade="";$fromOrderId="";$toProdId="";$check_prev_recv_arr=array();
	foreach($data_array as $row)
	{
		$order_ids.=$row[csf('to_order_id')].",";
		$order_id=chop($order_ids,",");

		$toBatchId.=$row[csf('to_batch_id')].",";
		$toOrderId.=$row[csf('to_order_id')].",";
		$fabricShade.=$row[csf('fabric_shade')].",";
		$fromOrderId.=$row[csf('from_order_id')].",";
		$toProdId.=$row[csf('to_prod_id')].",";

	}

	$toBatchId=chop($toBatchId,",");
	$toOrderId=chop($toOrderId,",");
	$fabricShade=chop($fabricShade,",");
	$fromOrderId=chop($fromOrderId,",");
	$toProdId=chop($toProdId,",");

	$order_name_arr=return_library_array( "select id ,po_number from wo_po_break_down where status_active=1 and is_deleted =0 and id in(".$order_id.")",'id','po_number');
	$store=$data_array[0][csf('to_store')];
	$company=$data_array[0][csf('to_company')];
	$location=$data_array[0][csf('to_location_id')];

	$prev_rcv=sql_select("SELECT a.pi_wo_batch_no,a.prod_id,b.po_breakdown_id,a.fabric_shade,c.from_order_id, sum(a.cons_quantity) as cons_quantity, c.to_trans_id
	from inv_transaction a, order_wise_pro_details b,inv_item_transfer_dtls_ac c 
	where a.status_active=1 and a.is_deleted=0 and a.transaction_type=5 and a.mst_id=$challan_id and a.prod_id in($toProdId) and a.pi_wo_batch_no in($toBatchId) and b.po_breakdown_id in($toOrderId) and a.fabric_shade in($fabricShade) and a.id = b.trans_id and a.mst_id=c.mst_id and c.dtls_id=b.dtls_id and c.from_order_id in($fromOrderId) and b.entry_form=13
	group by a.pi_wo_batch_no,a.prod_id,b.po_breakdown_id,a.fabric_shade,c.from_order_id, c.to_trans_id");
	foreach($prev_rcv as $row)
	{
		$check_prev_recv_arr[$row[csf('po_breakdown_id')]][$row[csf('from_order_id')]][$row[csf('prod_id')]]+=$row[csf('cons_quantity')];
	}
	/*echo "<pre>";
	print_r($check_prev_recv_arr);die;*/

	?>
	<fieldset style="width:400px;">
		<table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<th width="30">SL</th>
				<th width="50">Product Id</th>
				<th width="180">Item Description</th>
				<th width="80">Trans Qnty</th>
				<!-- <th>Balance</th> -->
			</thead>
			<tbody>
				<?

				$i=1;
				foreach($data_array as $row)
				{
					$prod_id=$row[csf('to_prod_id')];
					$item_desc=return_field_value("product_name_details","product_details_master","id=$prod_id");

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($row[csf("rate")]=="") $rate=0; else $rate=$row[csf("rate")];
					//$balance_qnty=$row[csf('transfer_qnty')]-$prev_rcv;
					
					//echo $row[csf('transfer_qnty')].'-'.$check_prev_recv_arr[$row[csf('to_trans_id')]][$row[csf('to_order_id')]][$row[csf('from_order_id')]][$row[csf('to_prod_id')]];
					//echo $row[csf('to_trans_id')].'='.$row[csf('to_order_id')].'='.$row[csf('from_order_id')].'='.$row[csf('to_prod_id')];
					$balance_qnty=$row[csf('transfer_qnty')]-$check_prev_recv_arr[$row[csf('to_order_id')]][$row[csf('from_order_id')]][$row[csf('to_prod_id')]];
					//echo $balance_qnty.'<br>';
					if($balance_qnty>0)
					{
						?>

						<tr bgcolor="<? echo $bgcolor;?>" onClick="set_form_data('<? echo $prod_id."__".$item_desc."__".$row[csf("uom")]."__".$order_name_arr[$row[csf('to_order_id')]]."__".$row[csf('to_order_id')]."__".$row[csf("fabric_shade")]."________".$row[csf("to_batch_id")]."__".$row[csf("color_id")]."__".$rate."______".$row[csf('transfer_qnty')]."__".$row[csf('trans_id')]; ?>',0);" style="cursor:pointer">
							<td align="center"><? echo $i; ?></td>
							<td align="center"><? echo $prod_id; ?></td>
							<td><p><? echo $item_desc; ?></p></td>
							<td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?></td>
							<!-- <td align="right"><? //echo number_format($balance_qnty,2); ?></td> -->
						</tr>
						<?
						$i++;
					}
				}
				?>
			</tbody>
		</table>
	</fieldset>
	<?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$prod_data 		= sql_select("SELECT id, avg_rate_per_unit, current_stock, stock_value 
		from product_details_master where id=$productID");
	$item_id 			= $prod_data[0][csf("id")];
	$avg_rate_per_unit  = $prod_data[0][csf("avg_rate_per_unit")];
	$current_stock 		= $prod_data[0][csf("current_stock")];
	$stock_value 		= $prod_data[0][csf("stock_value")];

	// ======================================= Already acknowledgement Validation
	$transfer_issue_dtls = sql_select("SELECT from_prod_id,sum(transfer_qnty) transfer_qnty 
	from inv_item_transfer_dtls 
	where mst_id=$challan_id and to_prod_id=$productID and to_batch_id='".str_replace("'","",$batch_id)."' and active_dtls_id_in_transfer=1 group by from_prod_id");	

	$from_prod_id 		= $transfer_issue_dtls[0][csf("from_prod_id")];
	$transfer_issue_qnty= $transfer_issue_dtls[0][csf("transfer_qnty")];
	$up_trans_id 		= str_replace("'","",$update_trans_id);
	
	$prev_rcv = sql_select("SELECT sum(a.transfer_qnty) as transfer_qnty, a.to_prod_id
	from inv_item_transfer_dtls a, inv_item_transfer_dtls_ac b
	where a.id=B.dtls_id and a.mst_id=$challan_id and a.to_prod_id=$productID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.active_dtls_id_in_transfer=0 group by a.to_prod_id");
	$prev_rcv_qnty = $prev_rcv[0][csf("transfer_qnty")]*1;

	//$cu_rcv = $acknowledge_qnty + str_replace("'","",$txtTransQnty);

	if(str_replace("'","",$update_id)!=""){
		$cu_rcv 	   = ($prev_rcv_qnty!=str_replace("'","",$txtTransQnty))?$prev_rcv_qnty*1:0;
	}
	else
	{
		$cu_rcv 	   = ($prev_rcv_qnty>0)?$prev_rcv_qnty:0;
	}

	//echo "20**update_id".$update_id.'='.$cu_rcv.'>'.$transfer_issue_qnty;die; // 6>6
	if($cu_rcv>0)
	{
		echo "20**Acknowledgement against this challan is already done.\nTransfer Quantity = ".$transfer_issue_qnty."\nAcknowledge Quantity = ".$prev_rcv_qnty;die;
	}
	// =======================================  Already acknowledgement Validation end
	
	$con = connect();
	if ($operation==0)  // Insert Here
	{
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		

		if(str_replace("'","",$productID)=="")
		{
			echo "10**";die;
		}
		if (str_replace("'", "", $txt_system_id) != "")
		{
			$field_array="acknowledg_date*remarks*updated_by*update_date";
			$data_array="".$txt_transfer_date."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$id=str_replace("'","",$update_id);
		}
		else
		{
			$id = return_next_id_by_sequence("INV_ITEM_TRANS_MST_AC_PK_SEQ", "inv_item_trans_acknowledgement", $con);
			$field_array="id, entry_form, challan_id, company_id, store_id, transfer_criteria, item_category, acknowledg_date, remarks, inserted_by, insert_date";

			$data_array="(".$id.",359,".$challan_id.",".$cbo_company_id_to.",".$cbo_store_name_to.",".$cbo_transfer_criteria.",".$cbo_item_category.",".$txt_transfer_date.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		// echo "20**$txtOrderID";die;
		$existing_transfer_dtls_row = sql_select("SELECT b.batch_id,b.body_part_id,b.fabric_shade,b.feb_description_id, b.floor_id,b.from_booking_without_order,b.from_order_id,b.from_prod_id,b.from_store,
		b.from_trans_entry_form,b.rack,b.rate,b.remarks,b.room,b.shelf,b.trans_id,b.transfer_qnty,b.transfer_value,b.transfer_value_in_usd,b.uom,b.to_body_part,b.roll,b.to_ord_book_id,b.to_ord_book_no,b.to_batch_id, b.yarn_lot, b.brand_id, b.stitch_length, b.y_count, b.from_program, b.to_program, b.transfer_requ_dtls_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b
		where a.id=b.mst_id and a.id=$challan_id and b.trans_id=$from_trans_id and a.status_active=1 and b.status_active=1 and b.active_dtls_id_in_transfer=1");

		$body_part_id 				= $existing_transfer_dtls_row[0][csf("body_part_id")];
		$from_batch_id 				= $existing_transfer_dtls_row[0][csf("batch_id")];
		$to_batch_id 				= $existing_transfer_dtls_row[0][csf("to_batch_id")];
		$fabric_shade 				= $existing_transfer_dtls_row[0][csf("fabric_shade")];
		$from_order_id 				= $existing_transfer_dtls_row[0][csf("from_order_id")];
		$from_prod_id 				= $existing_transfer_dtls_row[0][csf("from_prod_id")];
		$from_store 				= $existing_transfer_dtls_row[0][csf("from_store")];
		$rate 						= $existing_transfer_dtls_row[0][csf("rate")];
		$remarks 					= $existing_transfer_dtls_row[0][csf("remarks")];
		$from_floor_id 				= $existing_transfer_dtls_row[0][csf("floor_id")];
		$from_room 					= $existing_transfer_dtls_row[0][csf("room")];
		$from_rack 					= $existing_transfer_dtls_row[0][csf("rack")];
		$from_shelf 				= $existing_transfer_dtls_row[0][csf("shelf")];
		$trans_id 					= $existing_transfer_dtls_row[0][csf("trans_id")];
		$transfer_qnty 				= $existing_transfer_dtls_row[0][csf("transfer_qnty")];
		$transfer_value 			= $existing_transfer_dtls_row[0][csf("transfer_value")];
		$transfer_value_in_usd 		= $existing_transfer_dtls_row[0][csf("transfer_value_in_usd")];
		$uom 						= $existing_transfer_dtls_row[0][csf("uom")];
		$to_body_part 				= $existing_transfer_dtls_row[0][csf("to_body_part")];
		$roll 						= $existing_transfer_dtls_row[0][csf("roll")];
		$to_ord_book_id 			= $existing_transfer_dtls_row[0][csf("to_ord_book_id")];
		$to_ord_book_no 			= $existing_transfer_dtls_row[0][csf("to_ord_book_no")];
		$yarn_lot 					= $existing_transfer_dtls_row[0][csf("yarn_lot")];
		$brand_id 					= $existing_transfer_dtls_row[0][csf("brand_id")];
		$stitch_length 				= $existing_transfer_dtls_row[0][csf("stitch_length")];
		$y_count 					= $existing_transfer_dtls_row[0][csf("y_count")];
		$from_program 				= $existing_transfer_dtls_row[0][csf("from_program")];
		$to_program 				= $existing_transfer_dtls_row[0][csf("to_program")];
		$transfer_requ_dtls_id 		= $existing_transfer_dtls_row[0][csf("transfer_requ_dtls_id")];

		$field_array_trans="id, mst_id, company_id, prod_id, pi_wo_batch_no, item_category, transaction_type, transaction_date, store_id, floor_id, room, rack, self, order_id, body_part_id, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, dyeing_color_id, stitch_length, program_no, inserted_by, insert_date";

		$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, batch_id, yarn_lot, color_id, item_group, from_store, floor_id, room, rack, shelf, to_store, to_floor_id, to_room, to_rack, to_shelf, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id, trans_id, to_trans_id,remarks, fabric_shade, to_batch_id, body_part_id, to_body_part, stitch_length, y_count, from_program, to_program, transfer_requ_dtls_id, roll, to_ord_book_id, to_ord_book_no";

		$field_array_dtls_ac="id, mst_id, dtls_id, is_acknowledge, from_prod_id, to_prod_id, batch_id, yarn_lot, color_id, item_group, from_store, floor_id, room, rack, shelf, to_store, to_floor_id, to_room, to_rack, to_shelf, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id, fabric_shade, to_batch_id, body_part_id, to_body_part, roll, to_ord_book_id, to_ord_book_no, trans_id, to_trans_id, from_program, to_program, stitch_length, y_count";

		$field_array_proportionate="id,trans_id,trans_type,entry_form,dtls_id,po_breakdown_id,prod_id,color_id,quantity,inserted_by,insert_date";

		$save_string = explode("**", rtrim(str_replace("'","",$save_string),"** "));
		foreach ($save_string as $save_value) 
		{
			$data  = explode("_", $save_value);
			$floor = $data[0];
			$room  = $data[1];
			$rack  = $data[2];
			$shelf = $data[3];
			$qnty  = $data[4];

			$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$trans_value   = str_replace("'","",$qnty)*str_replace("'","",$item_rate);

			if ($data_array_trans != "") $data_array_trans .= ",";
			$data_array_trans .= "(" . $recv_trans_id.",".$challan_id.",".$cbo_company_id_to.",".$productID.",".$to_batch_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$floor.",".$room.",".$rack.",".$shelf.",".$txtOrderID.",".$to_body_part.",".$cbo_uom.",".$qnty.",".$item_rate.",'".$trans_value."',".$qnty.",".$trans_value.",".$color_id.",'".$stitch_length."',".$to_program.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
			$data_array_dtls .= "(" . $id_dtls.",".$challan_id.",".$from_prod_id.",".$productID.",".$from_batch_id.",'".$yarn_lot."',".$color_id.",0,".$from_store.",".$from_floor_id.",".$from_room.",".$from_rack.",".$from_shelf.",".$cbo_store_name_to.",".$floor.",".$room.",".$rack.",".$shelf.",".$cbo_item_category.",".$qnty.",".$rate.",".$trans_value.",".$uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$from_order_id.",".$txtOrderID.",".$trans_id.",".$recv_trans_id.",'".$remarks."',".$fabric_shade.",".$to_batch_id.",".$body_part_id.",".$to_body_part.",'".$stitch_length."','".$y_count."',".$from_program.",".$to_program.",'".$transfer_requ_dtls_id."','".$roll."','".$to_ord_book_id."','".$to_ord_book_no."')";

			$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
			if ($data_array_dtls_ac != "") $data_array_dtls_ac .= ",";
			$data_array_dtls_ac .= "(".$id_dtls_ac.",".$challan_id.",".$id_dtls.",0,".$from_prod_id.",".$productID.",".$from_batch_id.",'".$yarn_lot."',".$color_id.",0,".$from_store.",".$from_floor_id.",".$from_room.",".$from_rack.",".$from_shelf.",".$cbo_store_name_to.",".$floor.",".$room.",".$rack.",".$shelf.",".$cbo_item_category.",".$qnty.",".$rate.",".$trans_value.",".$uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$from_order_id.",".$txtOrderID.",".$fabric_shade.",".$to_batch_id.",".$body_part_id.",".$to_body_part.",'".$roll."','".$to_ord_book_id."','".$to_ord_book_no."',".$trans_id.",".$recv_trans_id.",".$from_program.",".$to_program.",'".$stitch_length."','".$y_count."')";

			if ($data_array_prop != "") $data_array_prop .= ",";
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop .= "(" . $id_prop.",".$recv_trans_id.",5,13,".$id_dtls.",".$txtOrderID.",".$productID.",".$color_id.",".$qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}

		$field_array_prod_update = "avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
		$curr_stock_qnty 		 = $current_stock+str_replace("'", '',$txtTransQnty);
		$curr_stock_value 		 = $trans_value+$stock_value;

		if($curr_stock_value>0 && $curr_stock_qnty)  $present_agv_rate=$curr_stock_value/$curr_stock_qnty; else $present_agv_rate=0;
		// if Qty is zero then rate & value will be zero
		if ($curr_stock_qnty<=0) 
		{
			$curr_stock_value=0;
			$present_agv_rate=0;
		}
		$data_array_prod_update="'".$present_agv_rate."'*".$txtTransQnty."*".$curr_stock_qnty."*'".$curr_stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_mst_update = "is_acknowledge";
		$data_array_mst_update  = "1";

		$field_array_dtls_update = "active_dtls_id_in_transfer";
		$data_array_dtls_update  = "1";

		$rID=$rID2=$rID3=$rID4=$rID5=$prod=true;
		if (str_replace("'", "", $txt_system_id) != "")
		{
			$rID=sql_update("inv_item_trans_acknowledgement",$field_array,$data_array,"id",$update_id,0);
		}
		else
		{
			$rID=sql_insert("inv_item_trans_acknowledgement",$field_array,$data_array,0);
		}

		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		//echo "10**insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		//echo "10**insert into inv_item_transfer_dtls_ac ($field_array_dtls_ac) values $data_array_dtls_ac";oci_rollback($con);die;

		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		$rID8=sql_update("inv_item_transfer_mst",$field_array_mst_update,$data_array_mst_update,"id",$challan_id,0);
		$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$update_dtls_id,0);
		//$rID4=sql_update("inv_item_transfer_dtls_ac",$field_array_status,$data_array_status,"id",$update_dtls_ac_id,0);
		$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		$rID6=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		$rID7=sql_insert("inv_item_transfer_dtls_ac",$field_array_dtls_ac,$data_array_dtls_ac,0);
		$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$productID,0);

		//echo "10**".$rID."**".$rID2."**".$rID3."**".$rID5."**".$rID6."**".$rID7."**".$rID8."**".$prod;oci_rollback($con);die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID5 && $rID6 && $rID7 && $rID8 && $prod)
			{
				mysql_query("COMMIT");
				echo "0**".$id."**".str_replace("'","",$challan_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID5 && $rID6 && $rID7 && $rID8 && $prod)
			{
				oci_commit($con);
				echo "0**".$id."**".str_replace("'","",$challan_id);
			}
			else
			{
				oci_rollback($con);
				echo "5**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if(str_replace("'","",$productID)=="")
		{
			echo "10**Update Id Null";die;
		}

		$existing_transfer_dtls_row = sql_select("select b.batch_id,b.body_part_id,b.fabric_shade,b.feb_description_id, b.floor_id,b.from_booking_without_order,b.from_order_id,b.from_prod_id,b.from_store,
			b.from_trans_entry_form,b.rack,b.rate_in_usd,b.remarks,b.room,b.shelf,b.trans_id,b.transfer_qnty,b.transfer_value,b.transfer_value_in_usd,b.uom,b.to_body_part,b.roll,b.to_ord_book_id,b.to_ord_book_no,b.to_batch_id
			from inv_item_transfer_mst a,inv_item_transfer_dtls b
			where a.id=b.mst_id and a.id=$challan_id and b.trans_id=$from_trans_id and a.status_active=1 and b.status_active=1 and a.is_acknowledge=1 and b.active_dtls_id_in_transfer=1");

		$batch_id 					= $existing_transfer_dtls_row[0][csf("batch_id")];
		$to_batch_id				= $existing_transfer_dtls_row[0][csf("to_batch_id")];
		$body_part_id 				= $existing_transfer_dtls_row[0][csf("body_part_id")];
		$fabric_shade 				= $existing_transfer_dtls_row[0][csf("fabric_shade")];
		$from_order_id 				= $existing_transfer_dtls_row[0][csf("from_order_id")];
		$from_prod_id 				= $existing_transfer_dtls_row[0][csf("from_prod_id")];
		$from_store 				= $existing_transfer_dtls_row[0][csf("from_store")];
		$rate_in_usd 				= $existing_transfer_dtls_row[0][csf("rate_in_usd")];
		$remarks 					= $existing_transfer_dtls_row[0][csf("remarks")];
		$from_floor_id 				= $existing_transfer_dtls_row[0][csf("floor_id")];
		$from_room 					= $existing_transfer_dtls_row[0][csf("room")];
		$from_rack 					= $existing_transfer_dtls_row[0][csf("rack")];
		$from_shelf 				= $existing_transfer_dtls_row[0][csf("shelf")];
		$from_trans_id 				= $existing_transfer_dtls_row[0][csf("trans_id")];
		$transfer_qnty 				= $existing_transfer_dtls_row[0][csf("transfer_qnty")];
		$transfer_value 			= $existing_transfer_dtls_row[0][csf("transfer_value")];
		$transfer_value_in_usd 		= $existing_transfer_dtls_row[0][csf("transfer_value_in_usd")];
		$uom 						= $existing_transfer_dtls_row[0][csf("uom")];
		$to_body_part 				= $existing_transfer_dtls_row[0][csf("to_body_part")];
		$roll 						= $existing_transfer_dtls_row[0][csf("roll")];
		$to_ord_book_id 			= $existing_transfer_dtls_row[0][csf("to_ord_book_id")];
		$to_ord_book_no 			= $existing_transfer_dtls_row[0][csf("to_ord_book_no")];

		$field_array_update="acknowledg_date*remarks*updated_by*update_date";
		$data_array_update="".$txt_transfer_date."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo "10**".$data_array_update;die;
		$field_array_trans="id, mst_id, company_id, prod_id, pi_wo_batch_no, item_category, transaction_type, transaction_date, store_id, floor_id, room, rack, self, order_id, fabric_shade, body_part_id, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, dyeing_color_id, inserted_by, insert_date";

		$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, batch_id, yarn_lot, color_id, item_group, from_store, floor_id, room, rack, shelf, to_store, to_floor_id, to_room, to_rack, to_shelf, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id, trans_id, to_trans_id,remarks, fabric_shade, to_batch_id, body_part_id, to_body_part, no_of_roll, to_ord_book_id, to_ord_book_no";

		$field_array_dtls_ac="id, mst_id, dtls_id, is_acknowledge, from_prod_id, to_prod_id, batch_id, yarn_lot, color_id, item_group, from_store, floor_id, room, rack, shelf, to_store, to_floor_id, to_room, to_rack, to_shelf, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id, fabric_shade, to_batch_id, body_part_id, to_body_part, no_of_roll, to_ord_book_id, to_ord_book_no, trans_id, to_trans_id";
		$field_array_proportionate="id,trans_id,trans_type,entry_form,dtls_id,po_breakdown_id,prod_id,color_id,quantity,inserted_by,insert_date";

		$field_array_trans_update="transaction_date*pi_wo_batch_no*floor_id*room*rack*self*cons_uom*cons_quantity*cons_rate*cons_amount*updated_by*update_date";
		$field_array_dtls_update="to_floor_id*to_room*to_rack*to_shelf*transfer_qnty*updated_by*update_date";
		$field_array_proportionate_update="po_breakdown_id*prod_id*color_id*quantity*updated_by*update_date";

		$save_string = explode("**", rtrim(str_replace("'","",$save_string),"** "));
		$trans_ids="";
		foreach ($save_string as $save_value) {
			$data  = explode("_", $save_value);
			$floor = $data[0];
			$room  = $data[1];
			$rack  = $data[2];
			$shelf = $data[3];
			$qnty  = $data[4];
			$trans_id = $data[5];
			$trans_ids .= $data[5].",";

			if($trans_id > 0){
				$trans_value=str_replace("'","",$qnty)*str_replace("'","",$item_rate);

				$update_transid[]=$trans_id;
				$update_trans_arr[$trans_id]=explode("*",("".$txt_transfer_date."*".$to_batch_id."*".$floor."*".$room."*".$rack."*".$shelf."*".$uom."*".$qnty."*".$item_rate."*".$trans_value."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));

				$update_dtlsid[]=$trans_id;
				$update_dtls_arr[$trans_id]=explode("*",("".$floor."*".$room."*".$rack."*".$shelf."*".$qnty."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));

				$update_propid[]=$trans_id;
				$data_array_prop_update[$trans_id]=explode("*",("".$txtOrderID."*".$productID."*".$color_id."*".$qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			}
			else
			{

				$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$trans_value   = str_replace("'","",$qnty)*str_replace("'","",$item_rate);

				if ($data_array_trans != "") $data_array_trans .= ",";
				$data_array_trans .= "(" . $recv_trans_id.",".$challan_id.",".$cbo_company_id_to.",".$productID.",".$to_batch_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$floor.",".$room.",".$rack.",".$shelf.",".$txtOrderID.",".$to_body_part.",".$cbo_uom.",".$qnty.",".$item_rate.",'".$trans_value."',".$qnty.",".$trans_value.",".$color_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				if ($data_array_dtls != "") $data_array_dtls .= ",";
				$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
				$data_array_dtls .= "(" . $id_dtls.",".$challan_id.",".$from_prod_id.",".$productID.",".$batch_id.",0,".$color_id.",0,".$from_store.",".$from_floor_id.",".$from_room.",".$from_rack.",".$from_shelf.",".$cbo_store_name_to.",".$floor.",".$room.",".$rack.",".$shelf.",".$cbo_item_category.",".$qnty.",".$rate_in_usd.",".$trans_value.",".$uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$from_order_id.",".$txtOrderID.",".$from_trans_id.",".$recv_trans_id.",'".$remarks."',".$fabric_shade.",".$to_batch_id.",".$body_part_id.",".$to_body_part.",'".$roll."','".$to_ord_book_id."','".$to_ord_book_no."')";

				$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
				if ($data_array_dtls_ac != "") $data_array_dtls_ac .= ",";
				$data_array_dtls_ac .= "(".$id_dtls_ac.",".$challan_id.",".$id_dtls.",0,".$from_prod_id.",".$productID.",".$batch_id.",0,".$color_id.",0,".$from_store.",".$from_floor_id.",".$from_room.",".$from_rack.",".$from_shelf.",".$cbo_store_name_to.",".$floor.",".$room.",".$rack.",".$shelf.",".$cbo_item_category.",".$qnty.",".$rate_in_usd.",".$trans_value.",".$uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$from_order_id.",".$txtOrderID.",".$fabric_shade.",".$to_batch_id.",".$body_part_id.",".$to_body_part.",'".$roll."','".$to_ord_book_id."','".$to_ord_book_no."',".$from_trans_id.",".$recv_trans_id.")";

				if ($data_array_prop != "") $data_array_prop .= ",";
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop .= "(" . $id_prop.",".$recv_trans_id.",5,13,".$id_dtls.",".$txtOrderID.",".$productID.",".$color_id.",".$qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}
		$trans_ids = rtrim($trans_ids,", ");
		$prev_cons_data=sql_select("select id, prod_id, cons_quantity, cons_amount from inv_transaction where id in($trans_ids)");
		$prev_prod=$prev_cons_data[0][csf("prod_id")];
		$prev_quantity=$prev_cons_data[0][csf("cons_quantity")];
		$prev_amount=$prev_cons_data[0][csf("cons_amount")];
		$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";

		if($prev_prod==str_replace("'","",$productID))
		{
			$curr_stock_qnty=(($current_stock-$prev_quantity)+str_replace("'", '',$txtTransQnty));
			$curr_stock_value=($trans_value+($stock_value-$prev_amount));
			if($curr_stock_value>0 && $curr_stock_qnty)  
			{
				$present_agv_rate=$curr_stock_value/$curr_stock_qnty;
			}
			else
			{
				$present_agv_rate=0;
			}
			// if Qty is zero then rate & value will be zero
			if ($curr_stock_qnty<=0) 
			{
				$curr_stock_value=0;
				$present_agv_rate=0;
			}

			$update_prod_id[]=str_replace("'","",$productID);
			$data_array_prod_update[str_replace("'","",$productID)]=explode("*",("'".$present_agv_rate."'*".$txtTransQnty."*".$curr_stock_qnty."*'".$curr_stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		else
		{
			$prev_prod_data=sql_select("select id, avg_rate_per_unit, current_stock, stock_value from product_details_master where id=$prev_prod");
			$prev_item_id=$prod_data[0][csf("id")];
			$prev_avg_rate_per_unit=$prod_data[0][csf("avg_rate_per_unit")];
			$prev_current_stock=$prod_data[0][csf("current_stock")];
			$prev_stock_value=$prod_data[0][csf("stock_value")];
			$adj_stcok_qnty=($prev_current_stock-$prev_quantity);
			$adj_stcok_value=($prev_stock_value-$prev_amount);
			if($adj_stcok_value>0 && $adj_stcok_qnty>0) 
			{
				$adj_avg_rate=$adj_stcok_value/$adj_stcok_qnty; 
			}
			else
			{
				$adj_avg_rate=0;
			}
			// if Qty is zero then rate & value will be zero
			if ($adj_stcok_qnty<=0) 
			{
				$adj_stcok_value=0;
				$adj_avg_rate=0;
			}

			$update_prod_id[]=$prev_prod;
			$data_array_prod_update[$prev_prod]=explode("*",("'".$adj_avg_rate."'*".$prev_quantity."*".$adj_stcok_qnty."*'".$adj_stcok_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));


			$curr_stock_qnty=($current_stock+str_replace("'", '',$txtTransQnty));
			$curr_stock_value=($trans_value+$stock_value);
			if($curr_stock_value>0 && $curr_stock_qnty)  $present_agv_rate=$curr_stock_value/$curr_stock_qnty; else $present_agv_rate=0;
			// if Qty is zero then rate & value will be zero
			if ($curr_stock_qnty<=0) 
			{
				$curr_stock_value=0;
				$present_agv_rate=0;
			}
			$update_prod_id[]=str_replace("'","",$productID);
			$data_array_prod_update[str_replace("'","",$productID)]=explode("*",("'".$present_agv_rate."'*".$txtTransQnty."*".$curr_stock_qnty."*'".$curr_stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

		}

		$rID=$rID2=$rID3=$rID4=$rID5=$rID6=$rID7=$rID8=$rID9=$prod=$order_wise_pro_details=$delete_trans=$delete_dtls=$delete_prop=$delete_dtls_ac=true;

		$rID=sql_update("inv_item_trans_acknowledgement",$field_array_update,$data_array_update,"id",$update_id,1);

		//echo "10**".$field_array_update."<br>".$data_array_update."<br>".$update_id;die;
		//echo "10**".bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$update_trans_arr,$update_transid);oci_rollback($con); die;


		$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$update_trans_arr,$update_transid));
		$rID3=execute_query(bulk_update_sql_statement("inv_item_transfer_dtls","trans_id",$field_array_dtls_update,$update_dtls_arr,$update_dtlsid));
		$rID4=execute_query(bulk_update_sql_statement("inv_item_transfer_dtls_ac","trans_id",$field_array_dtls_update,$update_dtls_arr,$update_dtlsid));
		$rID5=execute_query(bulk_update_sql_statement("order_wise_pro_details","trans_id",$field_array_proportionate_update,$data_array_prop_update,$update_propid));

		//echo "10**insert into inv_item_transfer_dtls (".$field_array_dtls_ac.") values ".$data_array_dtls_ac;die;
		if($data_array_trans!="")
			$rID6=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);

		if($data_array_prop!="")
			$rID7=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);

		if($data_array_dtls!="")
			$rID8=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);

		if($data_array_dtls_ac!="")
			$rID9=sql_insert("inv_item_transfer_dtls_ac",$field_array_dtls_ac,$data_array_dtls_ac,0);

		$prod=execute_query(bulk_update_sql_statement( "product_details_master","id",$field_array_prod_update,$data_array_prod_update,$update_prod_id ));
		$txt_deleted_trans_ids = str_replace("'", '',$txt_deleted_trans_ids);

		if($txt_deleted_trans_ids !=""){
			$delete_trans= execute_query("delete from inv_transaction where id in($txt_deleted_trans_ids)",0);
			$delete_dtls = execute_query("delete from inv_item_transfer_dtls where to_trans_id in($txt_deleted_trans_ids)",0);
			$delete_prop = execute_query("delete from order_wise_pro_details where trans_id in($txt_deleted_trans_ids)",0);
			$delete_dtls_ac = execute_query("delete from inv_item_transfer_dtls_ac where to_trans_id in($txt_deleted_trans_ids)",0);
		}
		//echo "10**".$rID."**".$rID2."**".$rID3."**".$rID4."**".$rID5."**".$rID6."**".$rID7."**".$rID8."**".$rID9."**".$delete_trans."**".$delete_dtls."**".$delete_prop."**".$delete_dtls_ac."**".$prod;oci_rollback($con);die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $rID8 && $prod && $delete_trans && $delete_dtls && $delete_prop && $delete_dtls_ac)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$challan_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $rID8 && $prod && $delete_trans && $delete_dtls && $delete_prop && $delete_dtls_ac)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$challan_id);
			}
			else
			{
				oci_rollback($con);
				echo "5**0";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="show_dtls_list_view_update")
{
	$data_array=sql_select("SELECT a.id, a.company_id, a.location_id, a.challan_id, a.acknowledg_date, a.remarks,b.mst_id, b.order_id, b.prod_id, b.pi_wo_batch_no, sum(b.cons_quantity) cons_quantity, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as trans_id, b.cons_rate, b.cons_uom, b.fabric_shade,c.trans_id from_trans_id
		from inv_item_trans_acknowledgement a, inv_transaction b,inv_item_transfer_dtls c
		where a.id='$data' and a.challan_id=b.mst_id and b.id=c.to_trans_id and a.entry_form=359 and b.transaction_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1
		group by a.id, a.company_id, a.location_id, a.challan_id, a.acknowledg_date, a.remarks, b.mst_id, b.order_id, b.prod_id, b.pi_wo_batch_no, b.dyeing_color_id, b.cons_rate, b.cons_uom, b.fabric_shade,c.trans_id");
	$trans_ids = "";
	foreach($data_array as $row)
	{
		$order_ids.=$row[csf('order_id')].",";
		$trans_ids.=$row[csf('trans_id')].",";
	}

	$trans_ids = rtrim($trans_ids, ", ");
	$trans_data_array=sql_select("select b.id,b.floor_id, b.room, b.rack, b.self, b.cons_quantity from inv_transaction b where b.id in(".$trans_ids.") and b.status_active=1 and b.is_deleted=0");
	foreach($trans_data_array as $row)
	{
		$save_string_arr[$row[csf("id")]]= $row[csf("floor_id")] . "_" . $row[csf("room")] . "_" . $row[csf("rack")] . "_" . $row[csf("self")] . "_" . $row[csf("cons_quantity")] . "_" . $row[csf("id")];
	}

	$order_id=implode(",",array_unique(explode(",",chop($order_ids,","))));

	$order_name_arr=return_library_array( "select id ,po_number from wo_po_break_down where status_active=1 and is_deleted =0 and id in(".$order_id.")",'id','po_number');
	$store=$data_array[0][csf('store_id')];
	$company=$data_array[0][csf('company_id')];
	$location=$data_array[0][csf('location_id')];

	?>
	<fieldset style="width:850px;">
		<table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<th width="30">SL</th>
				<th width="100">Product Id</th>
				<th width="400">Item Description</th>
				<th width="125">Order</th>
				<th width="100">Batch No</th>
				<th>Qnty</th>
			</thead>
			<tbody>
				<?
				$i=1;
				foreach($data_array as $row)
				{
					$prod_id=$row[csf('prod_id')];
					$item_desc=return_field_value("product_name_details","product_details_master","id=$prod_id");
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($row[csf("cons_rate")]=="") $rate=0; else $rate=$row[csf("cons_rate")];

					$trans_ids = array_unique(explode(",",$row[csf('trans_id')]));
					$save_string="";
					foreach ($trans_ids as $trans_row) {
						$save_string .= $save_string_arr[$trans_row] . "**";
					}
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="set_form_data('<? echo $prod_id."__".$item_desc."__".$row[csf("cons_uom")]."__".$order_name_arr[$row[csf('order_id')]]."__".$row[csf('order_id')]."__".$row[csf("fabric_shade")]."__".$company."__".$location."__".$store."__".$row[csf("pi_wo_batch_no")]."__".$row[csf("dyeing_color_id")]."__".$rate."__".$save_string."__".$row[csf('trans_id')]."__".$row[csf('cons_quantity')]."__".$row[csf('from_trans_id')]; ?>',1);" style="cursor:pointer">
						<td align="center"><? echo $i; ?></td>
						<td align="center"><? echo $prod_id; ?></td>
						<td><p><? echo $item_desc; ?></p></td>
						<td><p><? echo $order_name_arr[$row[csf('order_id')]]; ?></p></td>
						<td><p></p></td>
						<td align="right"><? echo number_format($row[csf('cons_quantity')],2); ?></td>
					</tr>
					<?
					$i++;
				}
				?>
			</tbody>
		</table>
	</fieldset>
	<?
	exit();
}

if ($action=="itemAcknowle_popup")
{
	echo load_html_head_contents("Item Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>
		function js_set_value(data)
		{
			$('#transfer_id').val(data);
			parent.emailwindow.hide();
		}

		function load_store()
		{
			var cbo_company_id_to='<? echo $cbo_company_id_to; ?>';
			load_drop_down('grey_fabric_transfer_v2_acknowledgement_controller',cbo_company_id_to, 'load_drop_down_store_to', 'cbo_store_name_to' );
		}
	</script>
</head>
<body onLoad="load_store();">
	<div align="center" style="width:890px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:880px;margin-left:10px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="870" class="rpt_table">
					<thead>
						<th>Transfer Criteria</th>
						<th>Store</th>
						<th>System ID</th>
						<th>Challan No</th>
						<th>Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<?
							echo create_drop_down("cbo_transfer_criteria", 120,$item_transfer_criteria,"", 1,"-- Select --",'0',"",'','1,2,4');
							?>
						</td>
						<td id="to_store_td">
							<?
							echo create_drop_down( "cbo_store_name_to", 160, $blank_array,"", 1, "--Select store--", 0, "");
							?>
						</td>
						<td>
							<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes_numeric" style="width:70px;" />
						</td>
						<td>
							<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:70px;" />
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px">To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_transfer_criteria').value+'_'+document.getElementById('cbo_store_name_to').value+'_'+document.getElementById('txt_system_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id_to; ?>+'_'+document.getElementById('txt_challan_no').value, 'create_transfer_search_list_view', 'search_div', 'grey_fabric_transfer_v2_acknowledgement_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="7" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=='create_transfer_search_list_view')
{
	$data = explode("_",$data);
	$transfer_criteria=$data[0];
	$store=$data[1];
	$system_id=$data[2];
	$txt_date_from=$data[3];
	$txt_date_to=$data[4];
	$company=$data[5];
	$challan_no=trim($data[6]);

	if($company==0)$company_cond=""; else $company_cond=" and a.company_id=$company";
	if($transfer_criteria==0)$criteria_cond=""; else $criteria_cond=" and a.transfer_criteria=$transfer_criteria";
	if($store==0)$store_cond=""; else $store_cond=" and a.store_id=$store";
	if($system_id=="")$system_id_cond=""; else $system_id_cond=" and a.id=$system_id";
	if($challan_no=="")$tran_challan_cond=""; else $tran_challan_cond=" and b.transfer_system_id like '%$challan_no%'";
	if ($txt_date_from!="" &&  $txt_date_to!="")
	{
		if($db_type==0)
		{
			$acknowledg_date_cond = "and a.acknowledg_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$acknowledg_date_cond = "and a.acknowledg_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}
	else
	{
		$acknowledg_date_cond ="";
	}
	if($db_type==0) $year_field="YEAR(a.insert_date)";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY')";
	else $year_field="";

	$sql="SELECT a.id, $year_field as year, a.entry_form, a.challan_id, a.company_id, a.store_id, a.location_id, a.transfer_criteria, a.item_category, a.acknowledg_date, a.remarks, b.transfer_system_id 
	from inv_item_trans_acknowledgement a, inv_item_transfer_mst b 
	where a.challan_id=b.id and a.entry_form=359 and a.transfer_criteria in(1,2,4) $company_cond $transfer_criteria_cond $store_cond $system_id_cond $acknowledg_date_cond $tran_challan_cond and a.status_active=1 and a.is_deleted=0";

	$store_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$location_arr=return_library_array( "select id, location_name from  lib_location", "id", "location_name"  );
	//print_r($location_arr);
	$arr=array(2=>$location_arr,3=>$store_arr,6=>$item_transfer_criteria);

	echo  create_list_view("tbl_list_search", "System ID, Year, Location, Store, Challan No, Transfer Date, Transfer Criteria", "70,60,130,150,150,80","860","250",0, $sql, "js_set_value", "id,challan_id", "", 1, "0,0,location_id,store_id,0,0,transfer_criteria", $arr, "id,year,location_id,store_id,transfer_system_id,acknowledg_date,transfer_criteria", '','','0,0,0,0,0,3,0');

	exit();
}

if($action=='populate_data_from_transfer_master_update')
{
	$data_array=sql_select("SELECT a.id, a.entry_form, a.challan_id, a.company_id, a.store_id, a.location_id, a.transfer_criteria, a.item_category, a.acknowledg_date, a.remarks, b.transfer_system_id 
		from inv_item_trans_acknowledgement a, inv_item_transfer_mst b 
		where a.id='$data' and a.challan_id=b.id and a.entry_form=359 and a.transfer_criteria in(1,2,4) and a.status_active=1 and a.is_deleted=0");
	foreach ($data_array as $row)
	{		
		if($row[csf("store_id")]>0){
			$loc_com=$row[csf("company_id")];
			echo "load_drop_down('requires/grey_fabric_transfer_v2_acknowledgement_controller','".$loc_com."', 'load_drop_down_store_to', 'to_store_td' );\n";
			echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("store_id")]."';\n";
			echo "$('#cbo_store_name_to').attr('disabled','disabled');\n";
		}
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "$('#txt_challan_no').attr('disabled','disabled');\n";
		echo "document.getElementById('challan_id').value 					= '".$row[csf("challan_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("acknowledg_date")])."';\n";
		//echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";

		//echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_transfer_acknowledgement',1,1);\n";

		exit();

	}
}

if($action=='quantity_popup')
{
	extract($_REQUEST);
	echo load_html_head_contents("Quantity Popup Info","../../../", 1, 1, $unicode);

	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name from lib_floor_room_rack_dtls b
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	where b.status_active=1 and b.is_deleted=0 and b.company_id=$cbo_company_id_to and b.store_id=$store";
	$lib_rrsb_arr=sql_select($lib_room_rack_shelf_sql);
	foreach ($lib_rrsb_arr as $room_rack_shelf_row) {
		$company  = $room_rack_shelf_row[csf("company_id")];
		$floor_id = $room_rack_shelf_row[csf("floor_id")];
		$room_id  = $room_rack_shelf_row[csf("room_id")];
		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

		if($store !="" && $floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_floor_arr[$floor_id] = $room_rack_shelf_row[csf("floor_name")];
		}

		if($store !="" && $floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_room_arr[$room_id] = $room_rack_shelf_row[csf("room_name")];
		}

		if($store !="" && $floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
			$lib_rack_arr[$rack_id] = $room_rack_shelf_row[csf("rack_name")];
		}

		if($store !="" && $floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
			$lib_shelf_arr[$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
		}
	}

	?>
	<script type="text/javascript">

		function fn_addRow(i) {

			var row_num = $('#txt_tot_row').val();
			row_num++;

			var clone = $("#tr__" + i).clone();
			clone.attr({
				id: "tr__" + row_num,
			});

			clone.find("input,select").each(function () {

				$(this).attr({
					'id': function (_, id) {
						var id = id.split("_");
						return id[0] + "_" + id[1] + "_" + id[2] + "_" + row_num
					},
					'name': function (_, name) {
						return name
					},
					'value': function (_, value) {
						return value
					}
				});

			}).end();

			$("#tr__" + i).after(clone);

			$('#cbo_floor_to_' + row_num).attr("onchange", 'load_room_rack_self_bin("grey_fabric_transfer_v2_acknowledgement_controller*2*cbo_room_to_' + row_num + '*' + row_num + '","room","room_td_to","<? echo $cbo_company_id_to;?>","<? echo $location;?>","<? echo $store;?>",this.value,0,"","","","","","H");');
			$('#hidden_trans_id_' + row_num).val("");

			$('#txt_increase_button_' + row_num).attr("value", "+").attr("onclick", "fn_addRow(" + row_num + ");");
			$('#txt_decrease_button_' + row_num).attr("value", "-").attr("onclick", "fn_deleteRow(" + row_num + ");");

			$('#txt_tot_row').val(row_num);

			$("#txt_trans_qnty_"+ row_num).val("");
			var hidden_rcv_qnty =$("#hidden_rcv_qnty").val();
			var total_ac_qnty=0;
			$("#qnty_breakdown_table").find('tbody tr').each(function()
			{
				var txt_trans_qnty = $( this ).find( "td:eq(4) input" ).val()*1;
				total_ac_qnty 		+= txt_trans_qnty;
			});
			if( hidden_rcv_qnty - total_ac_qnty >= 0)
			{
				$("#txt_trans_qnty_"+ row_num).val(hidden_rcv_qnty - total_ac_qnty);
			}
		}

		function fn_deleteRow(rowNo) {
			var trans_id = $('#hidden_trans_id_' + rowNo).val();
			$("#tr__" + rowNo).remove();
			$('#txt_tot_row').val(rowNo-1);
			var txt_deleted_trans_ids = $("#txt_deleted_trans_ids").val();
			if(txt_deleted_trans_ids!=""){
				$("#txt_deleted_trans_ids").val(txt_deleted_trans_ids+","+trans_id);
			}else{
				$("#txt_deleted_trans_ids").val(trans_id);
			}

		}

		function fnc_close()
		{
			var i = 1;
			var save_string = ""; var total_ac_qnty = 0;
			var hidden_rcv_qnty = $('#hidden_rcv_qnty').val()*1;
			$("#qnty_breakdown_table").find('tbody tr').each(function()
			{
				var cbo_floor_to 	= $("#cbo_floor_to_"+i).val();
				var cbo_room_to 	= $("#cbo_room_to_"+i).val();
				var txt_rack_to 	= $("#txt_rack_to_"+i).val();
				var txt_shelf_to 	= $("#txt_shelf_to_"+i).val();
				var txt_trans_qnty  = $("#txt_trans_qnty_"+i).val()*1;
				var hidden_trans_id = $("#hidden_trans_id_"+i).val()*1;
				save_string 		+= cbo_floor_to + "_" + cbo_room_to + "_" + txt_rack_to + "_" + txt_shelf_to + "_" + txt_trans_qnty + "_" + hidden_trans_id + "**";
				total_ac_qnty 		+= txt_trans_qnty;
				i++;
			});

			if(total_ac_qnty != hidden_rcv_qnty){
				alert("Acknowledgement quantity must be equal to Transfer quantity.\nTransfer quantity = " + hidden_rcv_qnty);
				return;
			}else{
				$("#save_string").val(save_string);
				parent.emailwindow.hide();
			}

		}
	</script>
	<input type="hidden" id="cbo_company_id" value="<? echo $cbo_company_id_to;?>" />
	<input type="hidden" id="cbo_location" value="<? echo $location;?>" />
	<input type="hidden" id="cbo_store_name" value="<? echo $store;?>" />
	<input type="hidden" id="hidden_rcv_qnty" value="<? echo $hidden_rcv_qnty;?>" />
	<input type="hidden" id="save_string" value="" />
	<input type="hidden" id="txt_deleted_trans_ids" value="" />
	<table cellpadding="0" width="750" cellspacing="0" border="1" id="qnty_breakdown_table" class="rpt_table" rules="all" style='margin:10px auto;'>
		<thead>
			<th>Floor</th>
			<th>Room</th>
			<th>Rack</th>
			<th>Shelf</th>
			<th>Quantity</th>
			<th width="70"></th>
		</thead>
		<tbody>
			<?
			$i=1;
			if($save_string=="") { ?>
				<tr id="tr__1">
					<td id="floor_td_to">
						<? echo create_drop_down( "cbo_floor_to_$i", 150,$lib_floor_arr,"", 1, "--Select--", 0, "load_room_rack_self_bin('grey_fabric_transfer_v2_acknowledgement_controller*2*cbo_room_to_$i*$i', 'room',this.value, '".$cbo_company_id_to."','".$location."','".$store."',this.value,'','','','','','','H');",0,"","","","","","","cbo_floor_to[]" ); ?>
					</td>
					<td id="room_td_to">
						<? echo create_drop_down( "cbo_room_to_$i", 150,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","cbo_room_to[]" ); ?>
					</td>
					<td id="rack_td_to">
						<? echo create_drop_down( "txt_rack_to_$i", 150,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txt_rack_to[]" ); ?>
					</td>
					<td id="shelf_td_to">
						<? echo create_drop_down( "txt_shelf_to_$i", 150,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txt_shelf_to[]" ); ?>
					</td>
					<td>
						<input type="text" id="txt_trans_qnty_<? echo $i;?>" name="txt_trans_qnty[]" class="text_boxes_numeric" style="width:60px;" placeholder="Write" value="<? echo $hidden_rcv_qnty;?>"/>
					</td>
					<td align="center">
						<input type="button" id="txt_increase_button_<? echo $i;?>" name="txt_increase_button[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow(<? echo $i;?>);" />
						<input type="button" id="txt_decrease_button_<? echo $i;?>" name="txt_decrease_button[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i;?>);" />
						<input type="hidden" id="hidden_trans_id_<? echo $i;?>" value="" />
					</td>
				</tr>
				<?
			}else{
				$save_string = explode("**", rtrim($save_string,"** "));
				$i = 0;
				foreach ($save_string as $row) {
					$i++;
					$data = explode("_",$row);
					?>
					<tr id="tr__<? echo $i;?>">
						<td id="floor_td_to">
							<? echo create_drop_down( "cbo_floor_to_$i", 150,$lib_floor_arr,"", 1, "--Select--", $data[0], "load_room_rack_self_bin('grey_fabric_transfer_v2_acknowledgement_controller*2*cbo_room_to_1*1', 'room',this.value, '".$cbo_company_id_to."','".$location."','".$store."',this.value);",0,"","","","","","","cbo_floor_to[]" ); ?>
						</td>
						<td id="room_td_to">
							<? echo create_drop_down( "cbo_room_to_$i", 150,$lib_room_arr,"", 1, "--Select--", $data[1], "load_room_rack_self_bin('grey_fabric_transfer_v2_acknowledgement_controller*2*txt_rack_to_$i*1','rack','rack_td_to','".$cbo_company_id_to."','".$location."','".$store."','".$data[0]."',this.value);",0,"","","","","","","cbo_room_to[]" ); ?>
						</td>
						<td id="rack_td_to">
							<? echo create_drop_down( "txt_rack_to_$i", 150,$lib_rack_arr,"", 1, "--Select--", $data[2], "load_room_rack_self_bin('grey_fabric_transfer_v2_acknowledgement_controller*2*txt_shelf_to_$i*2','shelf','shelf_td_to','".$cbo_company_id_to."','".$location."','".$store."','".$data[0]."','".$data[1]."',this.value);",0,"","","","","","","txt_rack_to[]" ); ?>
						</td>
						<td id="shelf_td_to">
							<? echo create_drop_down( "txt_shelf_to_$i", 150,$lib_shelf_arr,"", 1, "--Select--", $data[3], "load_room_rack_self_bin('grey_fabric_transfer_v2_acknowledgement_controller*2*cbo_bin_to*2','bin','bin_td_to','".$cbo_company_id_to."','".$location."','".$store."','".$data[0]."','".$data[1]."','".$data[2]."',this.value);",0,"","","","","","","txt_shelf_to[]" ); ?>
						</td>
						<td>
							<input type="text" id="txt_trans_qnty_<? echo $i;?>" name="txt_trans_qnty[]" class="text_boxes_numeric" style="width:60px;" placeholder="Write" value="<? echo $data[4];?>" />
						</td>
						<td align="center">
							<input type="button" id="txt_increase_button_<? echo $i;?>" name="txt_increase_button[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow(<? echo $i;?>);" />
							<input type="button" id="txt_decrease_button_<? echo $i;?>" name="txt_decrease_button[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i;?>);" />
							<input type="hidden" id="hidden_trans_id_<? echo $i;?>" value="<? echo $data[5];?>" />
						</td>
					</tr>
					<?
				}
			}
			?>
			<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<? echo $i;?>">
			<!-- <input type="hidden" name="txt_deleted_trans_ids" id="txt_deleted_trans_ids" class="text_boxes" value="<? //echo $i;?>"> -->
		</tbody>
	</table>
	<table width="750">
		<tr>
			<td  colspan="7" align="center">
				<div style="width:100%;" align="center">
					<input type="button" name="close" id="close" onClick="fnc_close();" class="formbutton" value="Close" style="width:100px">
				</div>
			</td>
		</tr>
	</table>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
}

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("grey_fabric_transfer_v2_acknowledgement_controller",$data);
}

?>
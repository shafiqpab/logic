<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}

if ($action=='load_drop_down_buyer')
{
	echo create_drop_down( 'cbo_buyer_name', 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name",'id,buyer_name', 1, '-- All Buyer --', $selected, '' );
	exit();
}

if ($action=="load_drop_down_store")
{
    echo create_drop_down( "cbo_store_name", 120, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "loadFloor();" );
	exit();
}

if($action=="load_drop_down_floors")
{
    extract($_REQUEST);

    $datas=explode("_", $data);

    $company_ids = str_replace("'","",$datas[0]); 
    if($datas[1] != ""){$store_id_cond="and b.store_id in ($datas[1])";}

	echo create_drop_down( "cbo_floor_name", 120, "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN($company_ids) $store_id_cond
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	ORDER BY a.floor_room_rack_name","floor_room_rack_id,floor_room_rack_name", 1, "--Select Floor--", 0, "loadRoom()");
	exit();
}

if($action=="load_drop_down_rooms")
{
    extract($_REQUEST);

    $datas=explode("_", $data);

    $company_ids = str_replace("'","",$datas[0]); 
    if($datas[1] != ""){$store_id_cond="and b.store_id in ($datas[1])";}
    if($datas[2] != ""){$floor_id_cond="and b.floor_id in ($datas[2])";}

	echo create_drop_down( "cbo_room_name", 120, "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN($company_ids) $store_id_cond $floor_id_cond
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	ORDER BY a.floor_room_rack_name","floor_room_rack_id,floor_room_rack_name", 1, "--Select Room--", 0, "loadRack();");
	exit();
}

if($action=="load_drop_down_racks")
{
    extract($_REQUEST);

    $datas=explode("_", $data);

    $company_ids = str_replace("'","",$datas[0]); 
    if($datas[1] != ""){$store_id_cond="and b.store_id in ($datas[1])";}
    if($datas[2] != ""){$floor_id_cond="and b.floor_id in ($datas[2])";}
    if($datas[3] != ""){$room_id_cond="and b.room_id in ($datas[3])";}

	echo create_drop_down( "cbo_rack_name", 120, "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN($company_ids) $store_id_cond $floor_id_cond $room_id_cond
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	ORDER BY a.floor_room_rack_name","floor_room_rack_id,floor_room_rack_name", 1, "--Select Rack--", 0, "loadShelf();");
	exit();
}

if($action=="load_drop_down_shelfs")
{
    extract($_REQUEST);

    $datas=explode("_", $data);

    $company_ids = str_replace("'","",$datas[0]); 
    if($datas[1] != ""){$store_id_cond="and b.store_id in ($datas[1])";}
    if($datas[2] != ""){$floor_id_cond="and b.floor_id in ($datas[2])";}
    if($datas[3] != ""){$room_id_cond="and b.room_id in ($datas[3])";}
    if($datas[4] != ""){$rack_id_cond="and b.rack_id in ($datas[4])";}

	echo create_drop_down( "cbo_shelf_name", 120, "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.shelf_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN($company_ids) $store_id_cond $floor_id_cond $room_id_cond $rack_id_cond
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	ORDER BY a.floor_room_rack_name","floor_room_rack_id,floor_room_rack_name", 1, "--Select Shelf--", 0, "loadBinbox();" );
	exit();
}

if($action=="load_drop_down_binboxs")
{
    extract($_REQUEST);

    $datas=explode("_", $data);

    $company_ids = str_replace("'","",$datas[0]); 
    if($datas[1] != ""){$store_id_cond="and b.store_id in ($datas[1])";}
    if($datas[2] != ""){$floor_id_cond="and b.floor_id in ($datas[2])";}
    if($datas[3] != ""){$room_id_cond="and b.room_id in ($datas[3])";}
    if($datas[4] != ""){$rack_id_cond="and b.rack_id in ($datas[4])";}
    if($datas[5] != ""){$shelf_id_cond="and b.shelf_id in ($datas[5])";}

	echo create_drop_down( "cbo_binbox_name", 120, "SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.bin_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN($company_ids) $store_id_cond $floor_id_cond $room_id_cond $rack_id_cond $shelf_id_cond
    	GROUP BY a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	ORDER BY a.floor_room_rack_name","floor_room_rack_id,floor_room_rack_name", 1, "--Select Binbox--", 0, "");
	exit();
}

//$sql_main="";


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
    $cbo_company_name 	 = str_replace("'","",$cbo_company_name);
	$cbo_buyer_name 	 = str_replace("'","",$cbo_buyer_name);
	$txt_job_no 		 = trim(str_replace("'","",$txt_job_no));
	$txt_style_no 		 = trim(str_replace("'","",$txt_style_no));
	$txt_order_no 		 = trim(str_replace("'","",$txt_order_no));
	$txt_internal_ref_no = trim(str_replace("'","",$txt_internal_ref_no));
	$cbo_item_group 	 = str_replace("'","",$cbo_item_group);
	$cbo_store_name 	 = str_replace("'","",$cbo_store_name);
	$cbo_floor_name 	 = str_replace("'","",$cbo_floor_name);
	$cbo_room_name 		 = str_replace("'","",$cbo_room_name);
	$cbo_rack_name 		 = str_replace("'","",$cbo_rack_name);
	$cbo_shelf_name 	 = str_replace("'","",$cbo_shelf_name);
	$cbo_binbox_name 	 = str_replace("'","",$cbo_binbox_name);
	$txt_date_from 	     = trim(str_replace("'","",$txt_date_from));
	$txt_date_to 	     = trim(str_replace("'","",$txt_date_to));
	
    $company_cond=$buyer_cond="";
    if($cbo_company_name > 0) $company_cond = " and e.company_name=$cbo_company_name";
    //if($cbo_buyer_name > 0) $buyer_cond = " and f.buyer_name=$cbo_buyer_name";
    if($cbo_buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and e.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
		}		
	}
	else $buyer_cond=" and e.buyer_name=$cbo_buyer_name";


    $job_no_cond=$order_no_cond=$internal_ref_no_cond="";
    if($txt_job_no != "" ) $job_no_cond = " and f.job_no like '%$txt_job_no'";
    if($txt_order_no != "" ) $order_no_cond = " and d.po_number='$txt_order_no'";
    if($txt_internal_ref_no != "" ) $internal_ref_no_cond = " and d.grouping='$txt_internal_ref_no'";    

    $item_group_cond=$store_cond=$floor_cond=$room_cond=$rack_cond=$shelf_cond=$binbox_cond="";
    if($cbo_item_group > 0 ) $item_group_cond = " and a.item_group_id=$cbo_item_group";	
	if($cbo_store_name > 0) $store_cond = " and b.store_id=$cbo_store_name";
    if($cbo_floor_name > 0) $floor_cond = " and b.floor_id=$cbo_floor_name";
    if($cbo_room_name > 0) $room_cond = " and b.room=$cbo_room_name";
    if($cbo_rack_name > 0) $rack_cond = " and b.rack=$cbo_rack_name";
    if($cbo_shelf_name > 0) $shelf_cond = " and b.self=$cbo_shelf_name";
    if($cbo_binbox_name > 0) $binbox_cond = " and b.bin_box=$cbo_binbox_name";

    

    $date_cond="";
	if($txt_date_from != "" && $txt_date_to != "")
	{
		$date_from=change_date_format($txt_date_from,"","",1);
        $date_to=change_date_format($txt_date_to,"","",1);
		$date_cond = " and b.transaction_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
	}
    $date_from_time=strtotime($date_from);
    $date_to_time=strtotime($date_to);
   
    $buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
    $item_group_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name");
    $store_library=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
    $color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
    $rack_shalf_bin_library=return_library_array( "select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name" );
    $floor_library = return_library_array("select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name", "floor_id", "floor_room_rack_name");

    $sql_booking_qty=sql_select("select po_break_down_id as ORDER_ID, wo_qnty as WO_QNTY from wo_booking_dtls where status_active=1 and is_deleted=0 and wo_qnty is not null");
    foreach( $sql_booking_qty as $row){
        $booking_qty_arr[$row['ORDER_ID']]+=$row['WO_QNTY'];
    }
    //echo '<pre>';print_r($booking_qty_arr);

    $sql_main="SELECT a.id as PROD_ID, a.item_group_id as ITEM_GROUP_ID, a.item_description as ITEM_DESCRIPTION, a.item_color as ITEM_COLOR, a.item_size as ITEM_SIZE, a.unit_of_measure as UOM, a.stock_value as STOCK_VALUE, b.transaction_type as TRANSACTION_TYPE, b.transaction_date as TRANSACTION_DATE, b.cons_quantity as QUANTITY, b.store_id as STORE_ID, b.floor_id as FLOOR_ID, b.room as ROOM, b.rack as RACK, b.self as SHELF, b.bin_box as BIN_BOX, c.trans_type as TRANS_TYPE, c.quantity as QUANTITY, d.id as ORDER_ID, d.po_number as PO_NUMBER, d.grouping as INTERNAL_REF_NO, e.buyer_name as BUYER_ID, e.job_no as JOB_NO, e.style_ref_no as STYLE_REF_NO 
    from product_details_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
    WHERE a.id=b.prod_id and b.id=c.trans_id and b.prod_id=c.prod_id and c.po_breakdown_id=d.id and d.job_id=e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $company_cond $buyer_cond $job_no_cond $order_no_cond $internal_ref_no_cond $item_group_cond $store_cond $floor_cond $room_cond $rack_cond $shelf_cond $binbox_cond order by a.id";

    $sql_main_res=sql_select($sql_main);

    $main_data_arr=array();
    $opening_stock_arr=array();

    foreach($sql_main_res as $row)
    {
        $transaction_date_time=strtotime($row['TRANSACTION_DATE']);
        
        if ($date_from_time<=$transaction_date_time && $date_to_time>=$transaction_date_time)
        {
            $keys=$row['BUYER_ID']."##".$row['STYLE_REF_NO']."##".$row['PO_NUMBER']."##".$row['INTERNAL_REF_NO']."##".$row['ITEM_GROUP_ID']."##".$row['ITEM_DESCRIPTION']."##".$row['ITEM_COLOR']."##".$row['ITEM_SIZE']."##".$row['UOM']."##".$row['STORE_ID']."##".$row['FLOOR_ID']."##".$row['ROOM']."##".$row['RACK']."##".$row['SHELF']."##".$row['BIN_BOX'];

            $main_data_arr[$keys]['buyer_id']=$row['BUYER_ID'];
            $main_data_arr[$keys]['job_no']=$row['JOB_NO'];
            $main_data_arr[$keys]['style_ref_no']=$row['STYLE_REF_NO'];
            $main_data_arr[$keys]['order_id']=$row['ORDER_ID'];
            $main_data_arr[$keys]['po_number']=$row['PO_NUMBER'];
            $main_data_arr[$keys]['internal_ref_no']=$row['INTERNAL_REF_NO'];
            $main_data_arr[$keys]['item_group_id']=$row['ITEM_GROUP_ID'];
            $main_data_arr[$keys]['item_description']=$row['ITEM_DESCRIPTION'];
            $main_data_arr[$keys]['item_color']=$row['ITEM_COLOR'];
            $main_data_arr[$keys]['item_size']=$row['ITEM_SIZE'];
            $main_data_arr[$keys]['uom']=$row['UOM'];
            $main_data_arr[$keys]['store_id']=$row['STORE_ID'];
            $main_data_arr[$keys]['floor_id']=$row['FLOOR_ID'];
            $main_data_arr[$keys]['room']=$row['ROOM'];
            $main_data_arr[$keys]['rack']=$row['RACK'];
            $main_data_arr[$keys]['shelf']=$row['SHELF'];
            $main_data_arr[$keys]['bin_box']=$row['BIN_BOX'];

            if ($check_order_item_arr[$keys]==""){
                $check_order_item_arr[$keys]=$keys;
                if ($row['TRANSACTION_TYPE']==1) $main_data_arr[$keys]['rec_qty']+=$row['QUANTITY'];
                else if ($row['TRANSACTION_TYPE']==2) $main_data_arr[$keys]['iss_qty']+=$row['QUANTITY'];
                else if ($row['TRANSACTION_TYPE']==3) $main_data_arr[$keys]['rec_rtn_qty']+=$row['QUANTITY'];
                else if ($row['TRANSACTION_TYPE']==4) $main_data_arr[$keys]['iss_rtn_qty']+=$row['QUANTITY'];
                else if ($row['TRANSACTION_TYPE']==5) $main_data_arr[$keys]['transfer_in_qty']+=$row['QUANTITY'];
                else if ($row['TRANSACTION_TYPE']==6) $main_data_arr[$keys]['transfer_out_qty']+=$row['QUANTITY'];
                $main_data_arr[$keys]['stock_value']+=$row['STOCK_VALUE'];
            }
        }

        if (($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4 || $row['TRANSACTION_TYPE']==5) && ($row['TRANSACTION_DATE']<$date_from)) 
        {
            $opening_stock_arr[$keys]['rcv_total_opening']+=$row['QUANTITY'];
        }
        if (($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6) && ($row['TRANSACTION_DATE']<$date_from)) 
        {
            $opening_stock_arr[$keys]['iss_total_opening']+=$row['QUANTITY'];
        }
    }
    //echo '<pre>';print_r($main_data_arr);
    $table_width="3000";
    ob_start();	
    ?>
    <style>
        .wrd_brk{word-break: break-all;}
    </style>
    <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <thead>
            <tr>
                <th rowspan="2" width="40">SL</th>
                <th rowspan="2" width="120">Buyer</th>
                <th rowspan="2" width="120">Job No</th>
                <th rowspan="2" width="100">Style No</th>
                <th rowspan="2" width="100">Order No.</th>
                <th rowspan="2" width="100">Internal Ref.</th>
                <th colspan="4" width="500"><strong>Description</strong></th>
                <th rowspan="2" width="80">WO. Qty</th>
                <th rowspan="2" width="50">UOM</th>
                <th rowspan="2" width="100">Store Name</th>
                <th rowspan="2" width="100"><strong>Opening Stock</strong></th>
                <th colspan="5" width="400"><strong>Receive</strong></th>
                <th colspan="5" width="400"><strong>Issue</strong></th>
                <th rowspan="2" width="100"><strong>Closing Stock</strong></th>
                <th colspan="5" width="400"><strong>Inventory</strong></th>
                <th rowspan="2" width="80">Avg. Rate (TK)</th>
                <th rowspan="2">Amount</th>                
            </tr> 
            <tr>                         
                <th width="100">Item Group</th>
                <th width="200">Item Description</th>
                <th width="100">Item Color</th>
                <th width="100">Item Size</th>
                <th width="80">Receive</th>
                <th width="80">Sample Receive</th>
                <th width="80">Issue Return</th>
                <th width="80">Transfer In</th>
                <th width="80">Total Receive</th>
                <th width="80">Issue</th>
                <th width="80">Sample Issue</th>
                <th width="80">Received Return</th>
                <th width="80">Transfer Out</th>
                <th width="80">Total Issue</th>
                <th width="80">Floor</th>
                <th width="80">Room</th>
                <th width="80">Rack</th>
                <th width="80">Self</th>
                <th width="80">Bin/Box</th>
            </tr> 
        </thead>
    </table>
    <div style="width:<? echo $table_width+20; ?>px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
        <table width="<? echo $table_width; ?>px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
        <?
        $i=1;
        foreach($main_data_arr as $key => $row)
        {
            $total_rec_qty=$total_iss_qty=0;
            $total_rec_qty=$row['rec_qty']+$row['iss_rtn_qty']+$row['transfer_in_qty'];
            $total_iss_qty=$row['iss_qty']+$row['rec_rtn_qty']+$row['transfer_out_qty'];
            $opening_bal=$opening_stock_arr[$key]['rcv_total_opening']-$opening_stock_arr[$key]['iss_total_opening'];
            $closing_stock=$opening_bal+$total_rec_qty-$total_iss_qty;
            $avg_rate=$row['stock_value']/$closing_stock;
            ?>    
            <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                <td width="40" class="wrd_brk"><? echo $i; ?></td>	
                <td width="120" class="wrd_brk"><p><? echo $buyer_library[$row['buyer_id']]; ?></p></td>
                <td width="120" class="wrd_brk"><p><? echo $row['job_no']; ?></p></td>
                <td width="100" class="wrd_brk"><p><? echo $row['style_ref_no']; ?></p></td>
                <td width="100" class="wrd_brk"><p><? echo $row['po_number']; ?></p></td>
                <td width="100" class="wrd_brk"><p><? echo $row['internal_ref_no']; ?></p></td>       
                <td width="100" class="wrd_brk"><p><? echo $item_group_library[$row['item_group_id']]; ?></p></td>
                <td width="200" class="wrd_brk"><p><? echo $row['item_description']; ?></p></td>
                <td width="100" class="wrd_brk"><p><? echo $color_library[$row['item_color']]; ?></p></td>
                <td width="100" class="wrd_brk"><p><? echo $row['item_size']; ?></p></td>
                <td width="80" align="right" class="wrd_brk"><p><? echo number_format($booking_qty_arr[$row['order_id']],2); ?></p></td>
                <td width="50" align="center" class="wrd_brk"><p><? echo $unit_of_measurement[$row['uom']]; ?></p></td>
                <td width="100" class="wrd_brk"><p><? echo $store_library[$row['store_id']]; ?></p></td>
                <td width="100" align="right" class="wrd_brk"><p><? echo number_format($opening_bal,2); ?></p></td>
                <td width="80" align="right" class="wrd_brk" ><p><? echo number_format($row['rec_qty'],2); ?></p></td>
                <td width="80" align="right" class="wrd_brk"><p><? //echo $row['sample_rec_qty']; ?></p></td>
                <td width="80" align="right" class="wrd_brk"><p><? echo number_format($row['iss_rtn_qty'],2); ?></p></td>
                <td width="80" align="right" class="wrd_brk"><p><? echo number_format($row['transfer_in_qty'],2); ?></p></td>
                <td width="80" align="right" class="wrd_brk"><p><? echo number_format($total_rec_qty,2); ?></p></td>
                <td width="80" align="right" class="wrd_brk"><p><? echo number_format($row['iss_qty'],2); ?></p></td>
                <td width="80" align="right" class="wrd_brk"><p><? //echo number_format($closing_stock,2); ?></p></td>
                <td width="80" align="right" class="wrd_brk"><p><? echo number_format($row['rec_rtn_qty'],2); ?></p></td>
                <td width="80" align="right" class="wrd_brk"><p><? echo number_format($row['transfer_out_qty'],2); ?></p></td>
                <td width="80" align="right" class="wrd_brk"><p><? echo number_format($total_iss_qty,2); ?></p></td>
                <td width="100" align="right" class="wrd_brk"><p><? echo number_format($closing_stock,2); ?></p></td>
                <td width="80" class="wrd_brk"><p><? echo $floor_library[$row['floor_id']]; ?></p></td>
                <td width="80" class="wrd_brk"><p><? echo $rack_shalf_bin_library[$row['room']]; ?></p></td>
                <td width="80" class="wrd_brk"><p><? echo $rack_shalf_bin_library[$row['rack']]; ?></p></td>
                <td width="80" class="wrd_brk"><p><? echo $rack_shalf_bin_library[$row['shelf']]; ?></p></td>

                <td width="80" align="right" class="wrd_brk"><p><? echo $rack_shalf_bin_library[$row['binbox']]; ?></p></td>
                <td width="80" align="right" class="wrd_brk"><p><? echo number_format($avg_rate,2); ?></p></td>
                <td align="right" class="wrd_brk"><p><? echo number_format($row['stock_value'],2); ?></p></td>                
            </tr> 
            <?            
            $i++;
        }  
        ?>                   
        </table>
    </div>     
    <?
	$html = ob_get_contents();
    ob_clean();    
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../" ); 
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type"; 
    exit();
}
?>
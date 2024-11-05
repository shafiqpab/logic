<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

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
if ($supplier_id !='') {
    $supplier_credential_cond = "and c.id in($supplier_id)";
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

if ($action=="item_description_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);  
	?>	
    <script>
	 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
	
	function check_all_data()
	{
		var onclickString =""; var paramArr = ""; var functionParam = "";
		var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
		tbl_row_count = tbl_row_count - 1; 
		for( var i = 1; i <= tbl_row_count; i++ )
		{
			onclickString = $('#tr_' + i).attr('onclick');
			paramArr = onclickString.split("'");
			functionParam = paramArr[1];
			js_set_value( functionParam );

		}
	}
		
	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#item_desc_id').val( id );
		$('#item_desc_val').val( ddd );
	} 
		  
	</script>
     <input type="hidden" id="item_desc_id" />
     <input type="hidden" id="item_desc_val" />
 	<?

	$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	//if ($data[1]==0) $item_name =""; else $item_name =" and item_group_id in($data[1])";
	if ($data[1]==0) $item_name =""; else $item_name =" and a.item_group_id in($data[1])";

	$sql= "select a.id,a.item_group_id, a.item_category_id,b.item_group_code,a.item_description from product_details_master a, lib_item_group b where a.item_group_id = b.id and b.is_deleted=0 and b.status_active=1 and  a.company_id=$data[0] and a.item_category_id =4 $item_name";

	// $sql="SELECT id, item_group_id,item_category_id, item_description from product_details_master where company_id=$data[0] and item_category_id=4 and status_active=1 and is_deleted=0 $item_name"; 
	// echo $sql;
	$arr=array(0=>$trim_group,3=>$item_category);
	echo  create_list_view("list_view", "Item Group,Group Code,Description,Product ID", "130,130,200,100","600","300",0, $sql , "js_set_value", "id,item_description,item_group_id", "", 1, "item_group_id,0,0", $arr , "item_group_id,item_group_code,item_description,id", "",'setFilterGrid("list_view",-1);','0,0,0,0','',1) ;
	exit();
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));	
	//echo $cbo_company_name;die;
	
	$cbo_company_name 	 = str_replace("'","",$cbo_company_name);	
	$cbo_store_name 	 = str_replace("'","",$cbo_store_name);	
	$cbo_floor_name 	 = str_replace("'","",$cbo_floor_name);
	$cbo_room_name 		 = str_replace("'","",$cbo_room_name);
	$cbo_rack_name 		 = str_replace("'","",$cbo_rack_name);
	$cbo_shelf_name 	 = str_replace("'","",$cbo_shelf_name);
	$cbo_binbox_name 	 = str_replace("'","",$cbo_binbox_name);
	$value_with 	 = str_replace("'","",$value_with);

	$company_cond=$store_cond=$floor_cond="";
	$room_cond=$rack_cond=$shelf_cond=$binbox_cond="";
	if ($cbo_company_name>0) $company_cond =" and a.company_id='$cbo_company_name'";
	$items_group=$item="";		;
	if ($cbo_item_group>0) {
		$items_group=" and b.prod_id in ($cbo_item_group)";
		$item=" and b.item_group_id in ($cbo_item_group)";
	}
	$item_description=$prod_cond=""; 
	if ($item_description_id>0) {
		$item_description=" and b.prod_id in ($item_description_id)";
		$prod_cond=" and b.id in ($item_description_id)";
	}
	if ($cbo_store_name>0) $store_cond =" and a.store_id='$cbo_store_name'";
	if ($cbo_floor_name>0) $floor_cond =" and a.floor_id='$cbo_floor_name'";
	if ($cbo_room_name>0) $room_cond =" and a.room='$cbo_room_name'";
	if ($cbo_rack_name>0) $rack_cond =" and a.rack='$cbo_rack_name'";
	if ($cbo_shelf_name>0) $shelf_cond =" and a.self='$cbo_shelf_name'";
	if ($cbo_binbox_name>0) $binbox_cond =" and a.bin_box='$cbo_binbox_name'";
	
	
    $pre_from_date = date('d-M-Y',strtotime('first day of last month',strtotime($from_date)));
    $pre_to_date = date('d-M-Y',strtotime('last day of previous month',strtotime($from_date)));
    
	
	if($db_type==0) 
	{       
		$from_date=change_date_format($from_date,'yyyy-mm-dd');
		$to_date=change_date_format($to_date,'yyyy-mm-dd');
        $pre_from_date=change_date_format($pre_from_date,'yyyy-mm-dd');
		$pre_to_date=change_date_format($pre_to_date,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{               
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
        $pre_from_date=change_date_format($pre_from_date,'','',1);
		$pre_to_date=change_date_format($pre_to_date,'','',1);
	}
	else $from_date=$to_date="";

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$colorArr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name"); 
	$store_arr = return_library_array("select id,store_name from lib_store_location","id","store_name");
	//$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	
	$item_group_sql="select ID, ITEM_NAME, ORDER_UOM from lib_item_group where item_category=4";
	$item_group_sql_result=sql_select($item_group_sql);
	//echo "<pre>";print_r($item_group_sql_result);die;
	foreach($item_group_sql_result as $row)
	{
		$trim_group[$row["ID"]]["ITEM_NAME"]=$row["ITEM_NAME"];
		$trim_group[$row["ID"]]["ORDER_UOM"]=$row["ORDER_UOM"];
	}
	//echo "<pre>";print_r($trim_group);die;
	$rack_shalf_bin_library=return_library_array( "select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name" );
    $floor_library = return_library_array("select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name", "floor_id", "floor_room_rack_name");
	
	
	$date_array=array();
	//$returnRes_date="select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=4 group by prod_id";
	$returnRes_date="SELECT a.prod_id as PROD_ID, MIN(a.transaction_date) AS MIN_DATE, MAX(a.transaction_date) AS MAX_DATE FROM inv_transaction a WHERE a.is_deleted=0 AND a.status_active=1 AND a.item_category=4 and a.company_id=$cbo_company_name GROUP BY a.prod_id";
	//echo $returnRes_date;die;
	$result_returnRes_date = sql_select($returnRes_date);
	foreach($result_returnRes_date as $row)	
	{
		$date_array[$row['PROD_ID']]['MIN_DATE']=$row['MIN_DATE'];
		$date_array[$row['PROD_ID']]['MAX_DATE']=$row['MAX_DATE'];
	}
	$table_width="2520";
	ob_start();	
	?>
	<style type="text/css">
		.wrap_break_word {
			word-break: break-all;
			word-wrap: break-word;
		}
	</style>
	<div>
		<table style="width:<? echo $table_width; ?>px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
			<thead>
				<tr class="form_caption" style="border:none;">
					<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="14" align="center" style="border:none; font-size:14px;">
						<b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                             
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? if($from_date!="" || $to_date!="") echo "From : ".change_date_format($from_date)." To : ".change_date_format($to_date)."" ;?>
					</td>
				</tr>
			</thead>
		</table>
		<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th rowspan="2" width="40">SL</th>
					<th rowspan="2" width="60">Prod.ID</th>
					<th colspan="5">Description</th>					
					<th rowspan="2" width="110">Opening Stock</th>
					<th colspan="4">Receive</th>
					<th colspan="4">Issue</th>
					<th rowspan="2" width="100">Store</th>
					<th colspan="5">Inventory</th>
					<th rowspan="2" width="100">Closing Stock</th>
					<th rowspan="2" width="80">Avg. Rate (TK.)</th>
					<th rowspan="2" width="100">Amount</th>
					<th rowspan="2" width="80">Age(Days)</th>
					<th rowspan="2">DOH</th>
				</tr> 
				<tr>                         
					<th width="120">Item Group</th>
					<th width="180">Item Description</th>					
					<th width="100">Item Color</th>
					<th width="100">Item Size</th>
					<th width="100">UOM</th>
					<th width="80">Receive</th>
					<th width="80">Issue Return</th>
					<th width="80">Transfer In</th>
					<th width="100">Total Receive</th>
					<th width="80">Issue</th>
					<th width="80">Received Return</th>
					<th width="80">Transfer Out</th>
					<th width="100">Total Issue</th>
					<th width="100">Floor</th>
					<th width="100">Room</th>
					<th width="100">Rack</th>
					<th width="100">Shelf</th>
					<th width="100">Bin/Box</th>
				</tr> 
			</thead>
		</table>
		<div style="width:<? echo $table_width+20; ?>px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
			<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
				//$sql="select b.id, b.item_description,b.item_group_id, b.current_stock, b.avg_rate_per_unit from product_details_master b where b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' and b.item_category_id='4' $item $prod_cond $search_cond order by b.id";
				$sql="SELECT b.id as PROD_ID, b.order_uom as ORDER_UOM, b.item_description as ITEM_DESCRIPTION, b.item_size as ITEM_SIZE, b.item_color as ITEM_COLOR, b.item_group_id as ITEM_GROUP_ID, b.current_stock as CURRENT_STOCK, b.avg_rate_per_unit as AVG_RATE_PER_UNIT, a.store_id as STORE_ID, a.floor_id as FLOOR_ID, a.room as ROOM, a.rack as RACK, a.self as SHELF, a.bin_box as BIN_BOX,
				SUM(CASE WHEN a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' THEN a.cons_quantity ELSE 0 END) AS RCV_TOTAL_OPENING,
				SUM(CASE WHEN a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' THEN a.cons_quantity ELSE 0 END) AS ISS_TOTAL_OPENING,
				SUM(CASE WHEN a.transaction_type in(1) and a.transaction_date BETWEEN '".$from_date."' and '".$to_date."' THEN a.cons_quantity ELSE 0 END) AS RECEIVE,
				SUM(CASE WHEN a.transaction_type in(4) and a.transaction_date BETWEEN '".$from_date."' and '".$to_date."' THEN a.cons_quantity ELSE 0 END) AS ISSUE_RETURN,
				SUM(CASE WHEN a.transaction_type in(5) and a.transaction_date BETWEEN '".$from_date."' and '".$to_date."' THEN a.cons_quantity ELSE 0 END) AS TRANSFER_IN,
				SUM(CASE WHEN a.transaction_type in(2) and a.transaction_date BETWEEN '".$from_date."' and '".$to_date."' THEN a.cons_quantity ELSE 0 END) AS ISSUE,
				SUM(CASE WHEN a.transaction_type in(3) and a.transaction_date BETWEEN '".$from_date."' and '".$to_date."' THEN a.cons_quantity ELSE 0 END) AS RECEIVE_RETURN,
				SUM(CASE WHEN a.transaction_type in(6) and a.transaction_date BETWEEN '".$from_date."' and '".$to_date."' THEN a.cons_quantity ELSE 0 END) AS TRANSFER_OUT
				from inv_transaction a, product_details_master b
				where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=4 and a.item_category=4 $company_cond $item $prod_cond $store_cond $floor_cond $room_cond $rack_cond $shelf_cond $binbox_cond
				group by b.id, b.order_uom, b.item_description, b.item_color, b.item_size, b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id, a.floor_id, a.room, a.rack, a.self, a.bin_box
				order by b.item_group_id, b.order_uom ASC"; 
				//echo $sql; die;	
				$result = sql_select($sql);
				//echo $sql; die;
				// echo "<pre>";
				// 	print_r($result);
				// echo "</pre>";
				$i=1; $total_amount=0;
				$k=1; $check_date_array=array();
				foreach($result as $row)
				{
					if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
					//$ageOfDays = datediff("d",$date_array[$row['ID']]['MIN_DATE'],date("Y-m-d"));
					//$daysOnHand = datediff("d",$date_array[$row['ID']]['MAX_DATE'],date("Y-m-d"));
					$ageOfDays = datediff("d",$date_array[$row['PROD_ID']]['MIN_DATE'],date("Y-m-d"));
					$daysOnHand = datediff("d",$date_array[$row['PROD_ID']]['MAX_DATE'],date("Y-m-d")); 
					$opening_bal=$row['RCV_TOTAL_OPENING']-$row['ISS_TOTAL_OPENING'];
					$receive = $row['RECEIVE'];
					$issue = $row['ISSUE'];
					$issue_return=$row['ISSUE_RETURN'];
					$receive_return=$row['RECEIVE_RETURN'];
					$transfer_in=$row['TRANSFER_IN'];
					$transfer_out=$row['TRANSFER_OUT'];
					
					$tot_receive=$receive+$issue_return+$transfer_in;
					$tot_issue=$issue+$receive_return+$transfer_out;
									
					$closingStock=$opening_bal+$tot_receive-$tot_issue;
					$amount=$closingStock*$row['AVG_RATE_PER_UNIT'];
					if($closingStock==0 || number_format($closingStock,2)==0.00)
					{
						$row['AVG_RATE_PER_UNIT']=$amount=0;
					}
					
					if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
					{
						if($value_with==1)
						{
							if(number_format($opening_bal,2)>0 || number_format($tot_receive,2)>0 || number_format($tot_issue,2)>0 || number_format($closingStock,2)>0)
							{
								$data=$row['ITEM_GROUP_ID'].'__'.$row['ORDER_UOM'];
								if ($check_date_array[$data]=="")
								{
									if ($k!=1)
									{
										?>
										<tr class="tbl_bottom">
											<td width="40">&nbsp;</td>
											<td width="60">&nbsp;</td> 
											<td width="120">&nbsp;</td> 
											<td width="180">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td> 
											<td width="100" align="right" title="<? //echo $data; ?>">Total</td> 
											<td width="110" align="right" ><? echo number_format($total_opening,2); ?></td>
											<td width="80" align="right" ><? echo number_format($total_receive,2); ?></td>
											<td width="80" align="right" ><? echo number_format($total_issue_return,2); ?></td>
											<td width="80" align="right" ><? echo number_format($total_transfer_in,2); ?></td>
											<td width="100" align="right" ><? echo number_format($total_receive_balance,2); ?></td>
											<td width="80" align="right" ><? echo number_format($total_issue,2); ?></td>
											<td width="80" align="right" ><? echo number_format($total_receive_return,2); ?></td>
											<td width="80" align="right" ><? echo number_format($total_transfer_out,2); ?></td>
											<td width="100" align="right" ><? echo number_format($total_issue_balance,2); ?></td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100" align="right" ><? echo number_format($total_closing_stock,2); ?></td>
											<td width="80">&nbsp;</td>
											<td width="100" align="right" ><? echo number_format($total_amount,2); ?></td>
											<td width="80">&nbsp;</td>
											<td >&nbsp;</td>
										</tr>
										<?								
										unset($total_opening);
										unset($total_receive);
										unset($total_issue_return);
										unset($total_transfer_in);
										unset($total_receive_balance);
										unset($total_issue);
										unset($total_receive_return);
										unset($total_transfer_out);
										unset($total_issue_balance);
										unset($total_closing_stock);
										unset($total_amount);
										//unset($total_used);								
									}
									$k++;						
								}						
								$check_date_array[$data]=$data;
								$datas=$cbo_company_name.'__'.$row['PROD_ID'].'__'.$from_date.'__'.$to_date.'__'.$row['FLOOR_ID'].'__'.$row['ROOM'].'__'.$row['RACK'].'__'.$row['SHELF'].'__'.$row['BIN_BOX'];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40"><? echo $i; ?></td>
									<td width="60" align="center"><p><? echo $row['PROD_ID']; ?></p></td>
									<td width="120" class="wrap_break_word"><p><? echo $trim_group[$row['ITEM_GROUP_ID']]["ITEM_NAME"]; ?></p></td>
									<td width="180" class="wrap_break_word"><p><? echo $row['ITEM_DESCRIPTION']; ?></p></td>
									<td width="100" class="wrap_break_word"><p><? echo $colorArr[$row['ITEM_COLOR']]; ?></p></td>
									<td width="100" class="wrap_break_word"><p><? echo $row['ITEM_SIZE']; ?></p></td>
									<td width="100" class="wrap_break_word" align="center" title="<?= $trim_group[$row['ITEM_GROUP_ID']]["ORDER_UOM"];?>"><p><? echo $unit_of_measurement[$trim_group[$row['ITEM_GROUP_ID']]["ORDER_UOM"]]; ?></p></td>		       
									<td width="110" align="right"><p><? echo number_format($opening_bal,2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($receive,2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_return,2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_in,2); ?></p></td>
									<td width="100" align="right"><p><? echo number_format($tot_receive,2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue,2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($receive_return,2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_out,2); ?></p></td>
									<td width="100" align="right"><p><? echo number_format($tot_issue,2); ?></p></td>
									<td width="100" class="wrap_break_word"><p><? echo $store_arr[$row['STORE_ID']]; ?></p></td>
									<td width="100" class="wrap_break_word"><p><? echo $floor_library[$row['FLOOR_ID']]; ?></p></td>
									<td width="100" class="wrap_break_word"><p><? echo $rack_shalf_bin_library[$row['ROOM']]; ?></p></td>
									<td width="100" class="wrap_break_word"><p><? echo $rack_shalf_bin_library[$row['RACK']]; ?></p></td>
									<td width="100" class="wrap_break_word"><p><? echo $rack_shalf_bin_library[$row['SHELF']]; ?></p></td>
									<td width="100" class="wrap_break_word"><p><? echo $rack_shalf_bin_library[$row['BIN_BOX']]; ?></p></td>
									<td width="100" align="right"><p><a href='#details' onClick="openmypage_popup('<? echo $datas; ?>','closing_stock_popup','750px');"><? echo number_format($closingStock,2); ?></a></p></td>
									<td width="80" align="right"><? if($closingStock>0)  echo number_format($row['AVG_RATE_PER_UNIT'],2); else echo "0.00"; ?></td>
									<td width="100" align="right"><? if($closingStock>0) echo number_format($amount,2); else echo "0.00"; ?></td>
									<td width="80" align="center"><? echo $ageOfDays; ?></td>
									<td align="center"><? echo $daysOnHand; ?></td>
								</tr>
								<? 
								$total_opening+=number_format($opening_bal,2);
								$total_receive+=$receive;
								$total_issue_return+=$issue_return;
								$total_transfer_in+=$transfer_in;
								$total_receive_balance+=$tot_receive;
								$total_issue+=$issue;
								$total_receive_return+=$receive_return;
								$total_transfer_out+=$transfer_out;
								$total_issue_balance+=$tot_issue;
								$total_closing_stock+=number_format($closingStock,2);
								$total_amount+=$amount;
								
								$grand_total_opening+=number_format($opening_bal,2);
								$grand_total_receive+=$receive;
								$grand_total_issue_return+=$issue_return;
								$grand_total_transfer_in+=$transfer_in;
								$grand_total_receive_balance+=$tot_receive;
								$grand_total_issue+=$issue;
								$grand_total_receive_return+=$receive_return;
								$grand_total_transfer_out+=$transfer_out;
								$grand_total_issue_balance+=$tot_issue;
								$grand_total_closing_stock+=number_format($closingStock,2);
								$grand_total_amount+=$amount;
								$i++;
							}
							
						}
						else
						{
							$data=$row['ITEM_GROUP_ID'].'__'.$row['ORDER_UOM'];
							if ($check_date_array[$data]=="")
							{
								if ($k!=1)
								{
									?>
									<tr class="tbl_bottom">
										<td width="40">&nbsp;</td>
										<td width="60">&nbsp;</td> 
										<td width="120">&nbsp;</td> 
										<td width="180">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td> 
										<td width="100" align="right" title="<? //echo $data; ?>">Total</td> 
										<td width="110" align="right" ><? echo number_format($total_opening,2); ?></td>
										<td width="80" align="right" ><? echo number_format($total_receive,2); ?></td>
										<td width="80" align="right" ><? echo number_format($total_issue_return,2); ?></td>
										<td width="80" align="right" ><? echo number_format($total_transfer_in,2); ?></td>
										<td width="100" align="right" ><? echo number_format($total_receive_balance,2); ?></td>
										<td width="80" align="right" ><? echo number_format($total_issue,2); ?></td>
										<td width="80" align="right" ><? echo number_format($total_receive_return,2); ?></td>
										<td width="80" align="right" ><? echo number_format($total_transfer_out,2); ?></td>
										<td width="100" align="right" ><? echo number_format($total_issue_balance,2); ?></td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100" align="right" ><? echo number_format($total_closing_stock,2); ?></td>
										<td width="80">&nbsp;</td>
										<td width="100" align="right" ><? echo number_format($total_amount,2); ?></td>
										<td width="80">&nbsp;</td>
										<td >&nbsp;</td>
									</tr>
									<?								
									unset($total_opening);
									unset($total_receive);
									unset($total_issue_return);
									unset($total_transfer_in);
									unset($total_receive_balance);
									unset($total_issue);
									unset($total_receive_return);
									unset($total_transfer_out);
									unset($total_issue_balance);
									unset($total_closing_stock);
									unset($total_amount);
									//unset($total_used);								
								}
								$k++;						
							}						
							$check_date_array[$data]=$data;
							$datas=$cbo_company_name.'__'.$row['PROD_ID'].'__'.$from_date.'__'.$to_date.'__'.$row['FLOOR_ID'].'__'.$row['ROOM'].'__'.$row['RACK'].'__'.$row['SHELF'].'__'.$row['BIN_BOX'];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
								<td width="60" align="center"><p><? echo $row['PROD_ID']; ?></p></td>
								<td width="120" class="wrap_break_word"><p><? echo $trim_group[$row['ITEM_GROUP_ID']]["ITEM_NAME"]; ?></p></td>
								<td width="180" class="wrap_break_word"><p><? echo $row['ITEM_DESCRIPTION']; ?></p></td>
								<td width="100" class="wrap_break_word"><p><? echo $colorArr[$row['ITEM_COLOR']]; ?></p></td>
								<td width="100" class="wrap_break_word"><p><? echo $row['ITEM_SIZE']; ?></p></td>
								<td width="100" class="wrap_break_word" align="center" title="<?= $trim_group[$row['ITEM_GROUP_ID']]["ORDER_UOM"];?>"><p><? echo $unit_of_measurement[$trim_group[$row['ITEM_GROUP_ID']]["ORDER_UOM"]]; ?></p></td>			       
								<td width="110" align="right"><p><? echo number_format($opening_bal,2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($receive,2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($issue_return,2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($transfer_in,2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($tot_receive,2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($issue,2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($receive_return,2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($transfer_out,2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($tot_issue,2); ?></p></td>
								<td width="100" class="wrap_break_word"><p><? echo $store_arr[$row['STORE_ID']]; ?></p></td>
								<td width="100" class="wrap_break_word"><p><? echo $floor_library[$row['FLOOR_ID']]; ?></p></td>
								<td width="100" class="wrap_break_word"><p><? echo $rack_shalf_bin_library[$row['ROOM']]; ?></p></td>
								<td width="100" class="wrap_break_word"><p><? echo $rack_shalf_bin_library[$row['RACK']]; ?></p></td>
								<td width="100" class="wrap_break_word"><p><? echo $rack_shalf_bin_library[$row['SHELF']]; ?></p></td>
								<td width="100" class="wrap_break_word"><p><? echo $rack_shalf_bin_library[$row['BIN_BOX']]; ?></p></td>
								<td width="100" align="right"><p><a href='#details' onClick="openmypage_popup('<? echo $datas; ?>','closing_stock_popup','750px');"><? echo number_format($closingStock,2); ?></a></p></td>
								<td width="80" align="right"><? if($closingStock>0)  echo number_format($row['AVG_RATE_PER_UNIT'],2); else echo "0.00"; ?></td>
								<td width="100" align="right"><? if($closingStock>0) echo number_format($amount,2); else echo "0.00"; ?></td>
								<td width="80" align="center"><? echo $ageOfDays; ?></td>
								<td align="center"><? echo $daysOnHand; ?></td>
							</tr>
							<? 
							$total_opening+=number_format($opening_bal,2);
							$total_receive+=$receive;
							$total_issue_return+=$issue_return;
							$total_transfer_in+=$transfer_in;
							$total_receive_balance+=$tot_receive;
							$total_issue+=$issue;
							$total_receive_return+=$receive_return;
							$total_transfer_out+=$transfer_out;
							$total_issue_balance+=$tot_issue;
							$total_closing_stock+=number_format($closingStock,2);
							$total_amount+=$amount;
							
							$grand_total_opening+=number_format($opening_bal,2);
							$grand_total_receive+=$receive;
							$grand_total_issue_return+=$issue_return;
							$grand_total_transfer_in+=$transfer_in;
							$grand_total_receive_balance+=$tot_receive;
							$grand_total_issue+=$issue;
							$grand_total_receive_return+=$receive_return;
							$grand_total_transfer_out+=$transfer_out;
							$grand_total_issue_balance+=$tot_issue;
							$grand_total_closing_stock+=number_format($closingStock,2);
							$grand_total_amount+=$amount;
							$i++;
						}
						 				
					}
				}
				?>
			</table>
		</div> 
		<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
			<tr class="tbl_bottom">
				<td width="40">&nbsp;</td>
				<td width="60">&nbsp;</td> 
				<td width="120">&nbsp;</td> 
				<td width="180">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td> 
				<td width="100" align="right">Total</td> 
				<td width="110" align="right" ><? echo number_format($total_opening,2); ?></td>
				<td width="80" align="right" ><? echo number_format($total_receive,2); ?></td>
				<td width="80" align="right" ><? echo number_format($total_issue_return,2); ?></td>
				<td width="80" align="right" ><? echo number_format($total_transfer_in,2); ?></td>
				<td width="100" align="right" ><? echo number_format($total_receive_balance,2); ?></td>
				<td width="80" align="right" ><? echo number_format($total_issue,2); ?></td>
				<td width="80" align="right" ><? echo number_format($total_receive_return,2); ?></td>
				<td width="80" align="right" ><? echo number_format($total_transfer_out,2); ?></td>
				<td width="100" align="right" ><? echo number_format($total_issue_balance,2); ?></td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100" align="right" ><? echo number_format($total_closing_stock,2); ?></td>
				<td width="80">&nbsp;</td>
				<td width="100" align="right" ><? echo number_format($total_amount,2); ?></td>
				<td width="80">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>

			<tr class="tbl_bottom">
				<td width="40">&nbsp;</td>
				<td width="60">&nbsp;</td> 
				<td width="120">&nbsp;</td> 
				<td width="180">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td> 
				<td width="100" align="right">Grand Total</td> 
				<td width="110" align="right" ><? echo number_format($grand_total_opening,2); ?></td>
				<td width="80" align="right" ><? echo number_format($grand_total_receive,2); ?></td>
				<td width="80" align="right" ><? echo number_format($grand_total_issue_return,2); ?></td>
				<td width="80" align="right" ><? echo number_format($grand_total_transfer_in,2); ?></td>
				<td width="100" align="right" ><? echo number_format($grand_total_receive_balance,2); ?></td>
				<td width="80" align="right" ><? echo number_format($grand_total_issue,2); ?></td>
				<td width="80" align="right" ><? echo number_format($grand_total_receive_return,2); ?></td>
				<td width="80" align="right" ><? echo number_format($grand_total_transfer_out,2); ?></td>
				<td width="100" align="right" ><? echo number_format($grand_total_issue_balance,2); ?></td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100" align="right" ><? echo number_format($grand_total_closing_stock,2); ?></td>
				<td width="80">&nbsp;</td>
				<td width="100" align="right" ><? echo number_format($grand_total_amount,2); ?></td>
				<td width="80">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</div>
	<?

	$html = ob_get_contents();
    ob_clean();
    
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" ); 
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

if ($action=='closing_stock_popup')
{
	echo load_html_head_contents('Report Info', '../../../../', 1, 1,'','','');
	extract($_REQUEST);
	$data=explode("__",$data);
	$company_id=$data[0];
	$prod_id=$data[1];
	$from_date=$data[2];
	$to_date=$data[3];
	$floor_id=$data[4];
	$room=$data[5];
	$rack=$data[6];
	$self=$data[7];
	$bin_box=$data[8];

	$companyArr = return_library_array("select id, company_name from lib_company",'id','company_name');
	$buyerArr = return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier",'id','supplier_name');
	$user_arr = return_library_array( "select id, user_name from user_passwd", 'id', 'user_name');
	$table_width = 700;
	?>
	
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">		
		<div style="width:100%" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr>
	                    <th width="120">Buyer</th>
	                    <th width="120">Job</th>
	                    <th width="120">Style</th>
						<th width="120">PO Number</th>
	                    <th width="120">Internal Ref</th>
	                    <th>Qty</th>       
                    </tr>
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:auto; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="tbl_list_view">
	                <tbody>
	               	<?
					
					$floor_room_rack_cond="";
					if($floor_id>0) $floor_room_rack_cond.=" and a.floor_id=$floor_id"; 
					if($room>0) $floor_room_rack_cond.=" and a.room=$room"; 
					if($rack>0) $floor_room_rack_cond.=" and a.rack=$rack"; 
					if($self>0) $floor_room_rack_cond.=" and a.self=$self"; 
					if($bin_box>0) $floor_room_rack_cond.=" and a.bin_box=$bin_box"; 
					$sql="SELECT b.po_breakdown_id as PO_BREAKDOWN_ID, c.po_number as PO_NUMBER, c.grouping as INTERNAL_REF_NO, d.buyer_name as BUYER_NAME, d.job_no as JOB_NO, d.style_ref_no as STYLE_REF_NO, 
					SUM(CASE WHEN a.TRANSACTION_TYPE IN(1,4,5) AND a.transaction_date<'$from_date' THEN b.quantity ELSE 0 END) AS RCV_TOTAL_OPENING, 
					SUM(CASE WHEN a.TRANSACTION_TYPE IN(2,3,6) AND a.transaction_date<'$from_date' THEN b.quantity ELSE 0 END) AS ISS_TOTAL_OPENING, 
					SUM(CASE WHEN a.TRANSACTION_TYPE IN(1,4,5) AND a.transaction_date BETWEEN '$from_date' and '$to_date' THEN b.quantity ELSE 0 END) AS TOTAL_RECEIVE, 
					SUM(CASE WHEN a.TRANSACTION_TYPE IN(2,3,6) AND a.transaction_date BETWEEN '$from_date' and '$to_date' THEN b.quantity ELSE 0 END) AS TOTAL_ISSUE
					from inv_transaction a, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
					where a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_id=d.id and b.prod_id=$prod_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
					and a.item_category=4 $floor_room_rack_cond
					group by b.po_breakdown_id, c.po_number, c.grouping, d.buyer_name, d.job_no, d.style_ref_no";
	               	$sql_res = sql_select($sql);	               			
	               	$i=1; $total_quantity=$opening_bal=0;
               		foreach ($sql_res as $row) 
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$opening_bal=$row['RCV_TOTAL_OPENING']-$row['ISS_TOTAL_OPENING'];
						$quantity=$opening_bal+$row['TOTAL_RECEIVE']- $row['TOTAL_ISSUE'];
						if ($quantity != 0)
						{   		          		 
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="120"><p><? echo $buyerArr[$row['BUYER_NAME']]; ?></p></td>
								<td width="120"><p><? echo $row['JOB_NO']; ?></p></td>
								<td width="120"><p><? echo $row['STYLE_REF_NO']; ?></p></td>
								<td width="120"><p><? echo $row['PO_NUMBER']; ?></p></td>
								<td width="120"><p><? echo $row['INTERNAL_REF_NO']; ?></p></td>
								<td align="right"><p><? echo number_format($quantity,2); ?></p></td>                       
							</tr>
							<?
						}	
	                    $i++;
	                    $total_quantity += $quantity;
                    }
                    ?>
					<tr class="tbl_bottom">
						<td width="120">&nbsp;</td>
						<td width="120">&nbsp;</td>
						<td width="120">&nbsp;</td>
						<td width="120">&nbsp;</td>
						<td width="120">Total</td>
						<td align="right"><? echo number_format($total_quantity,2); ?></td>
					</tr>					
	                </tbody>
	            </table>
	        </div>
	    </div>
	</fieldset>
	<script>//setFilterGrid("tbl_list_view",-1);</script>
    <?
	exit();
}
?>
<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

if ($action=="load_room_rack_self_bin")
{
	$explodeData = explode('*', $data);
	$customFnc = array( 'store_update_upto_disable()' ); // not necessarily an array, see manual quote
	array_splice( $explodeData, 11, 0, $customFnc ); // splice in at position 3
	$data=implode('*', $explodeData);
	//echo $data;
	load_room_rack_self_bin("requires/finish_fabric_fso_to_fso_transfer_controller",$data);
}

if ($action=="upto_variable_settings")
{
	extract($_REQUEST);
	echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	exit();
}

if($action == "load_drop_floor")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	$sl = $data[2];
	echo create_drop_down( "to_floor_".$sl, "80", "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_id,floor_room_rack_name", 1, "--Select Floor--", 0, "load_drop_down( 'requires/finish_fabric_fso_to_fso_transfer_controller',$store_id+'_'+$company_id+'_'+$sl+'_'+this.value, 'load_drop_room', 'room_td_$sl');load_drop_down( 'requires/finish_fabric_fso_to_fso_transfer_controller',0, 'load_drop_rack', 'rack_td_$sl');load_drop_down( 'requires/finish_fabric_fso_to_fso_transfer_controller',0, 'load_drop_shelf', 'shelf_td_$sl');store_update_upto_disable();" );
}

if($action == "load_drop_room")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	$sl = $data[2];
	$floor_id = $data[3];
	$floor_cond = ($floor_id != "") ? " and b.floor_id='$data[3]'" : "";
	echo create_drop_down( "to_room_".$sl, "70", "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.store_id='$store_id' and a.company_id='$company_id' $floor_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select Room--", 0, "load_drop_down( 'requires/finish_fabric_fso_to_fso_transfer_controller',$store_id+'_'+$company_id+'_'+$sl+'_'+$floor_id+'_'+this.value, 'load_drop_rack', 'rack_td_$sl');load_drop_down( 'requires/finish_fabric_fso_to_fso_transfer_controller',0, 'load_drop_shelf', 'shelf_td_$sl');" );
}

if($action == "load_drop_rack")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	$sl = $data[2];
	$floor_id = $data[3];
	$room_id = $data[4];

	$floor_cond = ($floor_id != "") ? " and b.floor_id='$floor_id'" : "";
	$room_cond = ($room_id != "") ? " and b.room_id='$room_id'" : "";

	echo create_drop_down( "to_rack_".$sl, '100', "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.store_id='$store_id' and a.company_id='$company_id' $floor_cond $room_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select Rack--", 0, "load_drop_down( 'requires/finish_fabric_fso_to_fso_transfer_controller',$store_id+'_'+$company_id+'_'+$sl+'_'+this.value, 'load_drop_shelf', 'shelf_td_$sl');" );
}

if($action == "load_drop_shelf")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	$sl = $data[2];
	$rack_no = $data[3];

	echo create_drop_down( "to_shelf_".$sl, '80', "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.store_id='$store_id' and a.company_id='$company_id' and b.rack_id='$rack_no' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select Shelf--", 0, "" );
}

if ($action == "load_drop_down_buyer")
{
	$data = explode("_", $data);
	$with_in_group=$data[0];
	$company_id = $data[1];

	if ($company_id == 0)
	{
		echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "--Select Buyer--", 0, "");
	}
	else
	{
		if ($with_in_group== 1)
		{
			echo create_drop_down("cbo_buyer_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Buyer--", "0", "", "");
		}
		else if ($with_in_group== 2)
		{
			echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "--Select Buyer--", $selected, "", 0);
		}
		else
		{
			echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "--Select Buyer--", 0, "");
		}
	}

	exit();
}

if ($action=="order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
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
		<div align="center" style="width:850px;">
			<form name="searchdescfrm"  id="searchdescfrm">
				<fieldset style="width:850px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="840" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Within Group</th>
							<th>Buyer Name</th>
							<th>Sales Order No</th>
							<th width="170">Delivery Date Range</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:80px;" class="formbutton" />
								<input type="hidden" name="order_id" id="order_id" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down("cbo_within_group", 70, $yes_no, "", 1, "--Select--", 0, "load_drop_down( 'finish_fabric_fso_to_fso_transfer_controller', this.value+'_'+".$cbo_company_id.", 'load_drop_down_buyer', 'buyer_td' );");
								?>
							</td>
							<td id="buyer_td"><? echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "--Select Buyer--", 0, ""); ?></td>
							<td>
								<input type="text" style="width:80px;" class="text_boxes" name="txt_order_no" id="txt_order_no" placeholder="Enter Order No" />
							</td>
							<td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" readonly>
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" readonly>
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+document.getElementById('cbo_within_group').value, 'create_po_search_list_view', 'search_div', 'finish_fabric_fso_to_fso_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;" />
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

if($action=='create_po_search_list_view')
{
	$data=explode('_',$data);
	$company_id=$data[2];

	//$po_buyer_arr=return_library_array( "select id, buyer_id from wo_booking_mst",'id','buyer_id');
	//$sales_buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if ($data[3]!="" &&  $data[4]!="")
	{
		if($db_type==0)
		{
			$delivery_date_cond = "and a.delivery_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$delivery_date_cond = "and a.delivery_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else
		$delivery_date_cond ="";

	$type=$data[5];
	$arr=array(2=>$company_arr);//2=>$company_arr,

	$with_in_group=$data[6];

	if($data[0]==0) $buyer_cond=""; else $buyer_cond="and a.buyer_id='$data[0]'";
	if($data[1]!="") $po_cond="and a.job_no_prefix_num='$data[1]'"; else $po_cond="";
	if($with_in_group!=0) $within_group_cond="and a.within_group='$with_in_group'"; else $within_group_cond="";

	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	?>
	<div style="width:100%;">
		<table cellspacing="0" border="1" cellpadding="0" rules="all" width="800" class="rpt_table" align="left">
			<thead>
				<th width="30">SL</th>
				<th width="50">Sales Order No</th>
				<th width="50">Year</th>
				<th width="60">With in Group</th>
				<th width="110">Sales Order Buyer</th>
				<th width="110">Booking No</th>
				<th width="100">PO Buyer</th>
				<th width="100">Style Ref.</th>
				<th width="70">PO Qty</th>
				<th>Delivery Date</th>
			</thead>
		</table>
	</div>
	<div style="width:820px;max-height:180px; overflow-y:scroll;float: left;" id="sewing_production_list_view" >
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search" align="left">
			<?
			if($db_type==0)
			{
				$sql= "select a.id, a.job_no, a.job_no_prefix_num, YEAR(a.insert_date) as year, a.delivery_date, a.style_ref_no, a.buyer_id, a.booking_id, a.sales_booking_no, a.within_group,a.po_buyer, group_concat(b.item_number_id) as item_number_id, sum(b.grey_qty) as order_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $buyer_cond $po_cond $delivery_date_cond $within_group_cond group by a.id, a.job_no, a.job_no_prefix_num, a.insert_date, a.delivery_date, a.style_ref_no, a.buyer_id, a.booking_id, a.sales_booking_no,a.po_buyer, a.within_group order by a.id DESC ";
			}
			else if($db_type==2)
			{
				$sql= "select a.id, a.job_no, a.job_no_prefix_num, to_char(a.insert_date,'YYYY') as year, a.delivery_date, a.style_ref_no, a.buyer_id, a.booking_id, a.sales_booking_no, a.within_group,a.po_buyer, listagg(b.item_number_id,',') within group (order by b.item_number_id) as item_number_id, sum(b.grey_qty) as order_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $buyer_cond $po_cond $delivery_date_cond $within_group_cond group by a.id, a.job_no, a.job_no_prefix_num, a.insert_date, a.delivery_date, a.style_ref_no,a.po_buyer, a.buyer_id, a.booking_id, a.sales_booking_no, a.within_group order by a.id DESC ";
			}
        // echo  $sql; die;
			$i=1; $sql_result=sql_select($sql);
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($row[csf('within_group')]==1) $buyer_name=$company_arr[$row[csf('buyer_id')]];
				else if($row[csf('within_group')]==2) $buyer_name=$buyer_arr[$row[csf('buyer_id')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>');" >
					<td width="30"><? echo $i; ?></td>
					<td width="50"><? echo $row[csf('job_no_prefix_num')]; ?></td>
					<td width="50"><? echo $row[csf('year')]; ?></td>
					<td width="60"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
					<td width="110"><? echo $buyer_name; ?></td>
					<td width="110"><? echo $row[csf('sales_booking_no')]; ?></td>
					<td width="100"><? echo $buyer_arr[$row[csf('po_buyer')]]; ?></td>
					<td width="100"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="70" align="right"><? echo number_format($row[csf('order_qty')],2); ?></td>
					<td><? echo change_date_format($row[csf('delivery_date')]); ?></td>
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

if($action=='populate_data_from_order')
{

	$data=explode("**",$data);
	$po_id=$data[0];
	$which_order=$data[1];
	//$po_buyer_arr=return_library_array( "select id, buyer_id from wo_booking_mst",'id','buyer_id');
	//$po_comp_arr=return_library_array( "select id, company_id from wo_booking_mst",'id','company_id');

	$data_array= sql_select("select a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.sales_booking_no,a.po_company_id,a.po_buyer, a.buyer_id, a.booking_id, listagg(b.item_number_id,',') within group (order by b.item_number_id) as item_number_id from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id='$po_id' group by a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.sales_booking_no,a.po_company_id,a.po_buyer, a.buyer_id, a.booking_id");


	foreach ($data_array as $row)
	{
		$gmts_item_id=array_unique(explode(",",$row[csf('item_number_id')]));
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
		}

		echo "document.getElementById('txt_".$which_order."_order_id').value 			= '".$po_id."';\n";
		echo "document.getElementById('txt_".$which_order."_order_no').value 			= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_booking_no').value 			= '".$row[csf("sales_booking_no")]."';\n";
		echo "document.getElementById('cbo_".$which_order."_company').value 			= '".$row[csf("po_company_id")]."';\n";
		echo "document.getElementById('cbo_".$which_order."_buyer_name').value 			= '".$row[csf("po_buyer")]."';\n";
		echo "document.getElementById('txt_".$which_order."_style_ref').value 			= '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_gmts_item').value 			= '".$gmts_item."';\n";

		exit();
	}


}

if($action=="show_dtls_list_view")
{
	$data=explode("_",$data);
	$po_id=$data[0];
	$company_id=$data[1];

	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');

	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	$store_library=return_library_array( "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.category_type in(2) group by a.id, a.store_name order by a.store_name",'id','store_name');

	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name from lib_floor_room_rack_dtls b
 	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
 	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
 	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
 	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
 	where b.status_active=1 and b.is_deleted=0 and b.company_id=$company_id";

 	$lib_floor_arr=sql_select($lib_room_rack_shelf_sql);
 	foreach ($lib_floor_arr as $room_rack_shelf_row) {
 		$company  = $room_rack_shelf_row[csf("company_id")];
 		$floor_id = $room_rack_shelf_row[csf("floor_id")];
 		$room_id  = $room_rack_shelf_row[csf("room_id")];
 		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
 		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
 		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

 		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
 			$lib_floor_arr[$company][$floor_id] = $room_rack_shelf_row[csf("floor_name")];
 		}

 		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
 			$lib_room_arr[$company][$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
 		}

 		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
 			$lib_rack_arr[$company][$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
 		}

 		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
 			$lib_shelf_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
 		}
 	}


	$prod_sql=sql_select("select id, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id=2");
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
		$product_arr[$row[csf("id")]]["detarmination_id"]=$row[csf("detarmination_id")];
		$product_arr[$row[csf("id")]]["gsm"]=$row[csf("gsm")];
		$product_arr[$row[csf("id")]]["dia_width"]=$row[csf("dia_width")];
	}

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array_deter=sql_select($sql_deter);
	foreach( $data_array_deter as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array_deter);

	$sql = "SELECT x.po_breakdown_id,x.batch_id, x.prod_id, x.body_part_id,x.fabric_description_id,x.store_id,x.uom,  x.floor, x.room, x.rack_no, x.shelf_no, x.color_id,x.fabric_shade,x.dia_width_type,  x.width,  x.machine_no_id, x.gsm, x.order_rate, x.cons_rate, x.aop_rate, sum(quantity) as quantity
	from (
	select c.po_breakdown_id, b.batch_id, b.prod_id,  b.body_part_id, b.fabric_description_id, a.store_id, b.uom, b.floor, b.room, b.rack_no,b.shelf_no, b.color_id,b.fabric_shade,b.dia_width_type, b.width, null as machine_no_id, b.gsm, sum(c.quantity) as quantity , max(d.order_rate) as order_rate, max(d.cons_rate) as cons_rate, max(b.aop_rate) as aop_rate
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c , inv_transaction d
	where a.id = b.mst_id and b.id = c.dtls_id and a.id = d.mst_id and c.trans_id = d.id and d.transaction_type = 1 and d.item_category = 2 and a.entry_form in (7,225) and c.entry_form in (7,225) and b.is_sales=1 and c.is_sales=1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.po_breakdown_id =$po_id and a.receive_basis in(5,10,14)
	group by c.po_breakdown_id,b.batch_id, b.prod_id,  b.body_part_id, b.fabric_description_id, a.store_id, b.uom, b.floor, b.room, b.rack_no, b.shelf_no, b.color_id,b.fabric_shade,b.dia_width_type, b.width, a.item_category, b.gsm
	union all
	select a.to_order_id as po_breakdown_id, b.to_batch_id as batch_id, b.from_prod_id as prod_id, b.body_part_id,b.feb_description_id as fabric_description_id,b.to_store as store_id,b.uom, b.to_floor_id as floor,b.to_room as room, b.to_rack as rack_no,b.to_shelf as shelf_no,
	b.color_id,b.fabric_shade,b.dia_width_type, b.dia_width as width, null machine_no_id, b.gsm, sum(b.transfer_qnty) as quantity  , max(d.order_rate) as  order_rate,max(d.cons_rate) as cons_rate, max(b.aop_rate) as aop_rate
	from inv_item_transfer_mst a, inv_item_transfer_dtls b , inv_transaction d
	where a.id=b.mst_id and a.entry_form in(230) and a.to_order_id =$po_id and a.id = d.mst_id and d.transaction_type = 5 and b.to_trans_id = d.id and d.item_category = 2 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
	group by a.to_order_id, b.to_batch_id, b.from_prod_id, b.body_part_id, b.feb_description_id, b.to_store, b.uom, b.to_floor_id,b.to_room, b.to_rack, b.to_shelf, b.color_id,b.fabric_shade,b.dia_width_type, b.dia_width,  b.gsm

	    ) x
    group by  po_breakdown_id,batch_id, prod_id, body_part_id,fabric_description_id,store_id,uom,  floor, room, rack_no, shelf_no, color_id,fabric_shade,dia_width_type, width,  machine_no_id, gsm,  quantity, order_rate, cons_rate, x.aop_rate ";

	//echo $sql."<br><br>";//die;

	$transfered_fabric_sql = sql_select("select a.from_order_id as po_breakdown_id,b.batch_id, b.from_prod_id as prod_id, b.body_part_id,b.feb_description_id as fabric_description_id,b.from_store as store_id,b.uom, b.floor_id, b.room, b.rack as rack_no,b.shelf as shelf_no, b.color_id,b.fabric_shade,b.dia_width_type, b.dia_width as width, b.gsm, sum(b.transfer_qnty) as quantity
	from inv_item_transfer_mst a, inv_item_transfer_dtls b
	where a.id=b.mst_id and a.entry_form in(230) and a.from_order_id =$po_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
	group by a.from_order_id, b.batch_id, b.from_prod_id, b.body_part_id, b.feb_description_id, b.from_store, b.uom, b.floor_id, b.room, b.rack, b.shelf, b.color_id,b.fabric_shade,b.dia_width_type, b.dia_width, b.gsm");

	$floor_id = $room_id =$rack_id = $shelf_id = 0;
	foreach ($transfered_fabric_sql as $val)
	{
		if($val[csf("floor_id")]=="") $floor_id = 0; else $floor_id = $val[csf("floor_id")];
		if($val[csf("room")]=="") $room_id = 0; else $room_id = $val[csf("room")];
		if($val[csf("rack_no")]=="") $rack_id = 0; else $rack_id = $val[csf("rack_no")];
		if($val[csf("shelf_no")]=="") $shelf_id = 0; else $shelf_id = $val[csf("shelf_no")];

		$transfered_arr[$val[csf("po_breakdown_id")]][$val[csf("body_part_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$floor_id][$room_id][$rack_id][$shelf_id][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("quantity")];

		//$transfered_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$floor_id][$room_id][$rack_id][$shelf_id][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("quantity")];

		//$transfered_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$val[csf("floor_id")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("quantity")];
	}

	//$delivery_qnty_sql = sql_select("select c.po_breakdown_id, b.batch_id,b.store_id, b.prod_id,b.room,b.floor, b.rack_no,b.shelf_no, b.fabric_shade, b.width_type, sum(c.quantity) as  quantity from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c where a.id = b.mst_id and b.id = c.dtls_id and c.po_breakdown_id  = $po_id and c.status_active = 1 and a.entry_form in (224,287) and c.entry_form in (224,287) group by c.po_breakdown_id, b.batch_id,b.store_id, b.prod_id,b.room,b.floor, b.rack_no,b.shelf_no, b.fabric_shade, b.width_type");

	$delivery_qnty_sql = sql_select("SELECT c.po_breakdown_id, b.body_part_id, b.batch_id,b.store_id, b.prod_id,b.room,b.floor, b.rack_no,b.shelf_no, b.fabric_shade, b.width_type, sum(c.quantity) as  quantity from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c where a.id = b.mst_id and b.id = c.dtls_id and c.po_breakdown_id = $po_id  and c.status_active = 1 and a.entry_form in (224,287) and c.entry_form in (224,287) group by c.po_breakdown_id, b.body_part_id, b.batch_id,b.store_id, b.prod_id,b.room,b.floor, b.rack_no,b.shelf_no, b.fabric_shade, b.width_type");

	$floor_id = $room_id =$rack_id = $shelf_id = 0;
	foreach ($delivery_qnty_sql as $val)
	{
		if($val[csf("floor")]=="") $floor_id = 0; else $floor_id = $val[csf("floor")];
		if($val[csf("room")]=="") $room_id = 0; else $room_id = $val[csf("room")];
		if($val[csf("rack_no")]=="") $rack_id = 0; else $rack_id = $val[csf("rack_no")];
		if($val[csf("shelf_no")]=="") $shelf_id = 0; else $shelf_id = $val[csf("shelf_no")];

		$delivery_qnty_arr[$val[csf("po_breakdown_id")]][$val[csf("body_part_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$floor_id][$room_id][$rack_id][$shelf_id][$val[csf("fabric_shade")]][$val[csf("width_type")]] += $val[csf("quantity")];

		//$delivery_qnty_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$floor_id][$room_id][$rack_id][$shelf_id][$val[csf("fabric_shade")]][$val[csf("width_type")]] += $val[csf("quantity")];

		//$delivery_qnty_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("width_type")]] += $val[csf("quantity")];
	}


	$issue_return_sql = sql_select("SELECT e.po_breakdown_id,b.batch_id,d.store_id,b.prod_id, b.body_part_id, b.room,b.floor,b.rack_no, b.shelf_no,b.fabric_shade, b.dia_width_type, sum(e.quantity) as qnty 
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction d , order_wise_pro_details e 
	where a.id=b.mst_id  and b.mst_id=d.mst_id and b.trans_id = d.id and d.id = e.trans_id and b.id = e.dtls_id  and d.item_category=2 and d.transaction_type in (4) and a.entry_form in (233) and e.entry_form = 233 and e.po_breakdown_id = $po_id and e.is_sales=1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0
	group by  e.po_breakdown_id,b.batch_id,d.store_id,b.prod_id, b.body_part_id, b.room,b.floor,b.rack_no, b.shelf_no,b.fabric_shade,b.dia_width_type");

	$floor_id = $room_id =$rack_id = $shelf_id = 0;
	foreach ($issue_return_sql as $val)
	{
		if($val[csf("floor")]=="") $floor_id = 0; else $floor_id = $val[csf("floor")];
		if($val[csf("room")]=="") $room_id = 0; else $room_id = $val[csf("room")];
		if($val[csf("rack_no")]=="") $rack_id = 0; else $rack_id = $val[csf("rack_no")];
		if($val[csf("shelf_no")]=="") $shelf_id = 0; else $shelf_id = $val[csf("shelf_no")];

		$issue_return_qnty_arr[$val[csf("po_breakdown_id")]][$val[csf("body_part_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$floor_id][$room_id][$rack_id][$shelf_id][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("qnty")];

		//$issue_return_qnty_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("qnty")];
	}

	//echo $sql;//die;
	$data_array=sql_select($sql);

	foreach($data_array as $row)
	{
		$batch_ref[$row[csf('batch_id')]] = $row[csf('batch_id')];
	}

	$batch_ref_id = implode(",",array_filter(array_unique($batch_ref)));

	$batch_arr=return_library_array( "select a.id, a.batch_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id in ($batch_ref_id)",'id','batch_no');

	?>
		<table cellspacing="0" width="1360px" class="rpt_table" id="" rules="all" align="center">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="50">Product Id</th>
				    <th width="110">Batch No</th>
				    <th width="80">Color</th>
				    <th width="80">Fabric Shade</th>
				    <th width="100">Body Part</th>
				    <th width="80">Dia/ W. Type</th>
				    <th width="220">Fabric Description</th>
				    <th width="80">Store Name</th>
				    <th width="80">Floor Name</th>
				    <th width="70">Room Name</th>
				    <th width="70">From Rack</th>
				    <th width="80">From Shelf</th>
				    <th width="80">Available Qnty</th>
				    <th width="50">UOM</th>
				    <th width="50">Rate</th>
				    <th width="50">AOP Rate</th>
				</tr>
			</thead>
		<tbody>
	<?
	$i=1;
	$floor_id = $room_id =$rack_id = $shelf_id = 0;
	foreach($data_array as $row)
	{
		if($row[csf("floor")]=="") $floor_id = 0; else $floor_id = $row[csf("floor")];
		if($row[csf("room")]=="") $room_id = 0; else $room_id = $row[csf("room")];
		if($row[csf("rack_no")]=="") $rack_id = 0; else $rack_id = $row[csf("rack_no")];
		if($row[csf("shelf_no")]=="") $shelf_id = 0; else $shelf_id = $row[csf("shelf_no")];

		$transfered_qnty = $transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]][$row[csf('batch_id')]][$row[csf("prod_id")]][$row[csf("store_id")]][$floor_id][$room_id][$rack_id][$shelf_id][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]];

		$delivery_qnty = $delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]][$row[csf('batch_id')]][$row[csf("prod_id")]][$row[csf("store_id")]][$floor_id][$room_id][$rack_id][$shelf_id][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]];

		$issue_return_qnty_qnty = $issue_return_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]][$row[csf('batch_id')]][$row[csf("prod_id")]][$row[csf("store_id")]][$floor_id][$room_id][$rack_id][$shelf_id][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]];

		$available_qnty = $row[csf('quantity')] - $transfered_qnty - $delivery_qnty + $issue_return_qnty_qnty;
		$available_qnty = number_format($available_qnty,2,'.','');

		//echo "\nqnty = (". $row[csf('quantity')] .") - (tr= $transfered_qnty) - (deli = $delivery_qnty) + (iss ret = $issue_return_qnty_qnty)<br>";

		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$prod_name_dtls=$constructtion_arr[$row[csf('fabric_description_id')]]." ".$composition_arr[$row[csf('fabric_description_id')]]." ".$product_arr[$row[csf('prod_id')]]["gsm"]." ".$product_arr[$row[csf('prod_id')]]["dia_width"];

		$floor 		= $lib_floor_arr[$company_id][$floor_id];
		$room 		= $lib_room_arr[$company_id][$floor_id][$room_id];
		$rack_no	= $lib_rack_arr[$company_id][$floor_id][$room_id][$rack_id];
		$shelf_no 	= $lib_shelf_arr[$company_id][$floor_id][$room_id][$rack_id][$shelf_id];

		if($available_qnty>0)
		{
			$ref_data = $row[csf('prod_id')]."_".$prod_name_dtls."_".$row[csf('fabric_description_id')]."_".$row[csf('batch_id')]."_".$batch_arr[$row[csf('batch_id')]]."_".$row[csf('gsm')]."_".$row[csf('width')]."_".$row[csf('dia_width_type')]."_".$row[csf('machine_no_id')]."_".$row[csf('color_id')]."_".$color_arr[$row[csf('color_id')]]."_".$row[csf('fabric_shade')]."_".$row[csf('uom')]."_".$row[csf('body_part_id')]."_".$body_part[$row[csf('body_part_id')]]."_".$row[csf('order_rate')]."_".$row[csf('cons_rate')]."_".$row[csf('store_id')]."_".$store_library[$row[csf('store_id')]]."_".$floor_id."_".$floor."_".$room_id."_".$room."_".$rack_id."_".$rack_no."_".$shelf_id."_".$shelf_no."_".$available_qnty."_".$row[csf('aop_rate')];
			?>

			<tr bgcolor="<? echo $bgcolor;?>" style="text-decoration:none; cursor:pointer" onClick="set_form_data('<? echo $ref_data?>')" id="tr<? echo $i;?>">
				<td width="30" align="center"><? echo $i; ?></td>
				<td width="50" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
				<td width="110"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
				<td width="80"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
				<td width="80"><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></td>
				<td width="100"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
				<td width="80"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
				<td width="220"><p><? echo $prod_name_dtls; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $store_library[$row[csf('store_id')]]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $floor; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $room; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $rack_no; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $shelf_no; ?>&nbsp;</p></td>
				<td width="80"><p align="right" title='<? echo "\nqnty = (". $row[csf('quantity')] .") - (tr= $transfered_qnty) - (deli = $delivery_qnty) + (iss ret = $issue_return_qnty_qnty)";?>'>
					<? echo number_format($available_qnty, 2, '.', ''); ?>&nbsp;</p></td>
				<td width="50"><p align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
				<td width="50"><p align="right"><? echo $row[csf('order_rate')]; ?>&nbsp;</p></td>
				<td width="50"><p align="right"><? echo $row[csf('aop_rate')]; ?>&nbsp;</p></td>
			</tr>
			<?
			$i++;
		}
	}
	?>
		</tbody>
	</table>
	<?
	exit();
}



if($action=="show_transfer_listview")
{
	$data=explode("**",$data);
	$mst_id=$data[0];
	$order_id=$data[1];
	$company_id=$data[2];

	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=2","id","product_name_details");
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	$store_arr=return_library_array( "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.category_type in(2) group by a.id, a.store_name order by a.store_name",'id','store_name');

	$sql="select id, from_store, to_store, from_prod_id, transfer_qnty, color_id from inv_item_transfer_dtls where mst_id='$mst_id' and status_active = '1' and is_deleted = '0'";

	$arr=array(0=>$store_arr,1=>$store_arr,2=>$product_arr,4=>$color_arr);

	echo  create_list_view("list_view", "From Store,To Store,Item Description,Transfered Qnty,Color", "130,130,280,130,110","800","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "from_store,to_store,from_prod_id,0,color_id", $arr, "from_store,to_store,from_prod_id,transfer_qnty,color_id", "requires/finish_fabric_fso_to_fso_transfer_controller",'','0,0,0,2,0');

	exit();
}


if($action=='populate_transfer_details_form_data')
{
	$sql=" select a.company_id, a.from_order_id as po_breakdown_id,b.batch_id, b.from_prod_id as prod_id, b.body_part_id,b.feb_description_id,b.from_store,b.to_store as store_id,b.uom, b.rack,b.shelf, b.to_rack, b.to_shelf,b.room,b.floor_id as floor,b.to_room,b.to_floor_id, b.color_id,b.fabric_shade, b.dia_width, b.dia_width_type, b.gsm, b.machine_no_id, b.transfer_qnty, b.id as dtls_id, b.trans_id, b.to_trans_id, b.rate, b.rate_in_usd, b.to_batch_id, b.aop_rate from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and a.entry_form in(230) and b.id = $data and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";

	//echo $sql;die;

	$data_array=sql_select($sql);


	foreach($data_array as $row)
	{
		$sales_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		$batch_ref[$row[csf('batch_id')]] = $row[csf('batch_id')];
		$company_id = $row[csf('company_id')];
	}
	$po_id = implode(",", array_filter(array_unique($sales_arr)));
	$batch_ref_id = implode(",", array_filter(array_unique($batch_ref)));


	$batch_arr=return_library_array( "select a.id, a.batch_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id in ($batch_ref_id) ",'id','batch_no');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	$store_library=return_library_array( "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company_id	and a.status_active=1 and a.is_deleted=0 and b.category_type in(2) group by a.id, a.store_name order by a.store_name",'id','store_name');


	$prod_sql=sql_select("select id, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id=2");
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
		$product_arr[$row[csf("id")]]["detarmination_id"]=$row[csf("detarmination_id")];
		$product_arr[$row[csf("id")]]["gsm"]=$row[csf("gsm")];
		$product_arr[$row[csf("id")]]["dia_width"]=$row[csf("dia_width")];
	}

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($deter_array);


	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name from lib_floor_room_rack_dtls b
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	where b.status_active=1 and b.is_deleted=0 and b.company_id= $company_id";
	$lib_floor_arr=sql_select($lib_room_rack_shelf_sql);
	foreach ($lib_floor_arr as $room_rack_shelf_row)
	{
		$company  = $room_rack_shelf_row[csf("company_id")];
		$floor_id = $room_rack_shelf_row[csf("floor_id")];
		$room_id  = $room_rack_shelf_row[csf("room_id")];
		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_floor_arr[$company][$floor_id] = $room_rack_shelf_row[csf("floor_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_room_arr[$company][$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
			$lib_rack_arr[$company][$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
			$lib_shelf_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
		}
	}

	$rcv_trnsin_sql = sql_select("
	select x.po_breakdown_id,x.batch_id, x.prod_id, x.body_part_id,x.fabric_description_id, x.store_id,x.uom, x.room, x.floor, x.rack_no, x.shelf_no, x.color_id,x.fabric_shade, x.width, x.dia_width_type, x.gsm, sum(quantity) as quantity
	from (
		select c.po_breakdown_id, b.batch_id, b.prod_id,  b.body_part_id, b.fabric_description_id, a.store_id, b.uom, b.room, b.floor, b.rack_no, b.shelf_no, b.color_id,b.fabric_shade, b.width, b.dia_width_type, b.gsm, sum(c.quantity) as quantity
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
		where a.id = b.mst_id and b.id = c.dtls_id and a.entry_form in (7,225) and c.entry_form in (7,225) and b.is_sales=1 and c.is_sales=1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
		and c.po_breakdown_id =$po_id and a.receive_basis in(5,10,14) and c.trans_id >0
		group by c.po_breakdown_id,b.batch_id, b.prod_id,  b.body_part_id, b.fabric_description_id, a.store_id, b.uom, b.room, b.floor, b.rack_no, b.shelf_no, b.color_id,b.fabric_shade,b.dia_width_type, b.width, a.item_category, b.gsm
		union all
		select a.to_order_id as po_breakdown_id,b.to_batch_id as batch_id, b.from_prod_id as prod_id, b.body_part_id,b.feb_description_id as fabric_description_id, b.to_store as store_id,b.uom, b.to_room as room, b.to_floor_id as floor, b.to_rack as rack_no,b.to_shelf as shelf_no,
		b.color_id,b.fabric_shade, b.dia_width as width, b.dia_width_type, b.gsm, sum(b.transfer_qnty) as quantity
		from inv_item_transfer_mst a, inv_item_transfer_dtls b
		where a.id=b.mst_id and a.entry_form in(230) and a.to_order_id =$po_id
		and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
		group by a.to_order_id, b.to_batch_id, b.from_prod_id, b.body_part_id, b.feb_description_id, b.to_store, b.uom, b.to_room, b.to_floor_id, b.to_rack, b.to_shelf, b.color_id,b.fabric_shade, b.dia_width, b.dia_width_type, b.gsm
	) x
	group by  po_breakdown_id,batch_id, prod_id, body_part_id,fabric_description_id, store_id,uom, room, floor, rack_no, shelf_no, color_id,fabric_shade, width, dia_width_type, gsm ");


	$floor_id = $room_id =$rack_id = $shelf_id = 0;
	foreach ($rcv_trnsin_sql as $val)
	{
		if($val[csf("floor")]=="") $floor_id = 0; else $floor_id = $val[csf("floor")];
		if($val[csf("room")]=="") $room_id = 0; else $room_id = $val[csf("room")];
		if($val[csf("rack_no")]=="") $rack_id = 0; else $rack_id = $val[csf("rack_no")];
		if($val[csf("shelf_no")]=="") $shelf_id = 0; else $shelf_id = $val[csf("shelf_no")];
		$rcv_transin_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$floor_id][$room_id][$rack_id][$shelf_id][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("quantity")];

		//$rcv_transin_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("quantity")];

	}

	/*echo "<pre>";
	print_r($rcv_transin_arr[975][6203][18317][23]);
	die;*/

	$transfered_fabric_sql = sql_select("select a.from_order_id as po_breakdown_id,b.batch_id, b.from_prod_id as prod_id, b.body_part_id,b.feb_description_id as fabric_description_id,b.from_store as store_id,b.uom, b.floor_id,b.room, b.rack as rack_no,b.shelf as shelf_no, b.color_id,b.fabric_shade, b.dia_width as width, b.dia_width_type, b.gsm, sum(b.transfer_qnty) as quantity
		from inv_item_transfer_mst a, inv_item_transfer_dtls b
		where a.id=b.mst_id and a.entry_form in(230) and a.from_order_id in ($po_id)
		and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
		group by a.from_order_id, b.batch_id, b.from_prod_id, b.body_part_id, b.feb_description_id, b.from_store, b.uom, b.floor_id,b.room, b.rack, b.shelf, b.color_id,b.fabric_shade, b.dia_width, b.dia_width_type, b.gsm");
	$floor_id = $room_id =$rack_id = $shelf_id = 0;
	foreach ($transfered_fabric_sql as $val)
	{
		if($val[csf("floor_id")]=="") $floor_id = 0; else $floor_id = $val[csf("floor_id")];
		if($val[csf("room")]=="") $room_id = 0; else $room_id = $val[csf("room")];
		if($val[csf("rack_no")]=="") $rack_id = 0; else $rack_id = $val[csf("rack_no")];
		if($val[csf("shelf_no")]=="") $shelf_id = 0; else $shelf_id = $val[csf("shelf_no")];

		$transfered_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$floor_id][$floor_id][$rack_id][$shelf_id][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("quantity")];

		//$transfered_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$val[csf("floor_id")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("quantity")];
	}

	$delivery_qnty_sql = sql_select("select c.po_breakdown_id, b.batch_id,b.store_id, b.prod_id,b.room,b.floor, b.rack_no,b.shelf_no, b.fabric_shade, b.width_type, sum(c.quantity) as  quantity from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c where a.id = b.mst_id and b.id = c.dtls_id and c.po_breakdown_id  = $po_id and c.status_active = 1 and a.entry_form in (224,287) and c.entry_form in (224,287) group by c.po_breakdown_id, b.batch_id,b.store_id, b.prod_id,b.room,b.floor, b.rack_no,b.shelf_no, b.fabric_shade, b.width_type");

	$floor_id = $room_id =$rack_id = $shelf_id = 0;
	foreach ($delivery_qnty_sql as $val)
	{
		if($val[csf("floor")]=="") $floor_id = 0; else $floor_id = $val[csf("floor_id")];
		if($val[csf("room")]=="") $room_id = 0; else $room_id = $val[csf("room")];
		if($val[csf("rack_no")]=="") $rack_id = 0; else $rack_id = $val[csf("rack_no")];
		if($val[csf("shelf_no")]=="") $shelf_id = 0; else $shelf_id = $val[csf("shelf_no")];

		$delivery_qnty_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$floor_id][$room_id][$rack_id][$shelf_id][$val[csf("fabric_shade")]][$val[csf("width_type")]] += $val[csf("quantity")];

		//$delivery_qnty_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("width_type")]] += $val[csf("quantity")];
	}

	$issue_return_sql = sql_select("SELECT e.po_breakdown_id,b.batch_id,d.store_id,b.prod_id,b.room,b.floor,b.rack_no, b.shelf_no, b.fabric_shade, b.dia_width_type, sum(e.quantity) as qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction d , order_wise_pro_details e where a.id=b.mst_id  and b.mst_id=d.mst_id and b.trans_id = d.id and d.id = e.trans_id and b.id = e.dtls_id  and d.item_category=2 and d.transaction_type in (4) and a.entry_form in (233) and e.entry_form = 233 and e.po_breakdown_id = $po_id and e.is_sales=1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 group by e.po_breakdown_id,b.batch_id,d.store_id,b.prod_id,b.room,b.floor, b.rack_no, b.shelf_no,b.fabric_shade,b.dia_width_type");

	$floor_id = $room_id =$rack_id = $shelf_id = 0;
	foreach ($issue_return_sql as $val)
	{
		if($val[csf("floor")]=="") $floor_id = 0; else $floor_id = $val[csf("floor_id")];
		if($val[csf("room")]=="") $room_id = 0; else $room_id = $val[csf("room")];
		if($val[csf("rack_no")]=="") $rack_id = 0; else $rack_id = $val[csf("rack_no")];
		if($val[csf("shelf_no")]=="") $shelf_id = 0; else $shelf_id = $val[csf("shelf_no")];

		$issue_return_qnty_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$floor_id][$room_id][$rack_id][$shelf_id][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("qnty")];
		//$issue_return_qnty_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("qnty")];
	}


	$floor_id = $room_id =$rack_id = $shelf_id = 0;
	foreach ($data_array as $row)
	{
		if($val[csf("floor")]=="") $floor_id = 0; else $floor_id = $val[csf("floor")];
		if($val[csf("room")]=="") $room_id = 0; else $room_id = $val[csf("room")];
		if($val[csf("rack")]=="") $rack_id = 0; else $rack_id = $val[csf("rack")];
		if($val[csf("shelf")]=="") $shelf_id = 0; else $shelf_id = $val[csf("shelf")];

		$rcv_transfer_in_qnty = $rcv_transin_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("from_store")]][$floor_id][$room_id][$rack_id][$shelf_id][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]];

		$transfered_qnty = $transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("from_store")]][$floor_id][$room_id][$rack_id][$shelf_id][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]];
		$delivery_qnty = $delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("from_store")]][$floor_id][$room_id][$rack_id][$shelf_id][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]];

		$issue_return_qnty = $issue_return_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("from_store")]][$floor_id][$room_id][$rack_id][$shelf_id][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]];

		$available_qnty = ($rcv_transfer_in_qnty ) - $transfered_qnty - $delivery_qnty + $row[csf('transfer_qnty')] + $issue_return_qnty;

		$available_qnty = number_format($available_qnty,2,'.','');
		echo "document.getElementById('txt_current_stock').value 		= '".$available_qnty."';\n";

		$prod_name_dtls=$constructtion_arr[$row[csf('feb_description_id')]]." ".$composition_arr[$row[csf('feb_description_id')]]." ".$product_arr[$row[csf('prod_id')]]["gsm"]." ".$product_arr[$row[csf('prod_id')]]["dia_width"];

		echo "document.getElementById('txt_product_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('txt_item_desc').value 			= '".$prod_name_dtls."';\n";
		echo "document.getElementById('fabric_desc_id').value 			= '".$row[csf("feb_description_id")]."';\n";
		echo "document.getElementById('txt_batch_id').value 			= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_batch_no').value 			= '".$batch_arr[$row[csf('batch_id')]]."';\n";
		echo "document.getElementById('previous_from_batch_id').value 	= '".$row[csf('batch_id')]."';\n";
		echo "document.getElementById('previous_to_batch_id').value 	= '".$row[csf('to_batch_id')]."';\n";
		echo "document.getElementById('txt_gsm').value 					= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_width').value 				= '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('txt_dia_width_type').value 		= '".$row[csf("dia_width_type")]."';\n";
		echo "document.getElementById('txt_machine_id').value 			= '".$row[csf("machine_no_id")]."';\n";
		echo "document.getElementById('txt_color_id').value 			= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_color').value 				= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('cbo_fabric_shade').value 		= '".$row[csf("fabric_shade")]."';\n";
		echo "document.getElementById('cbo_uom').value 					= '".$row[csf("uom")]."';\n";

		echo "document.getElementById('txt_body_part_id').value 		= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('txt_body_part').value 			= '".$body_part[$row[csf("body_part_id")]]."';\n";
		echo "document.getElementById('txt_rate').value 				= '".$row[csf("rate_in_usd")]."';\n";
		echo "document.getElementById('txt_cons_rate').value 			= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_aop_rate').value 			= '".$row[csf("aop_rate")]."';\n";
		echo "document.getElementById('from_store_id').value 			= '".$row[csf("from_store")]."';\n";
		echo "document.getElementById('from_store_name').value 			= '".$store_library[$row[csf("from_store")]]."';\n";

		if($row[csf("floor")]>0){
			$floor 		= $lib_floor_arr[$company_id][$row[csf('floor')]];
			echo "document.getElementById('from_floor_id').value 		= '".$row[csf("floor")]."';\n";
			echo "document.getElementById('from_floor').value 			= '".$floor."';\n";
		}
		if($row[csf("room")]>0){
			$room 		= $lib_room_arr[$company_id][$row[csf('floor')]][$row[csf('room')]];
			echo "document.getElementById('from_room_id').value 		= '".$row[csf("room")]."';\n";
			echo "document.getElementById('from_room').value 			= '".$room."';\n";
		}
		if($row[csf("rack")]>0){
			$rack_no	= $lib_rack_arr[$company_id][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack')]];
			echo "document.getElementById('from_rack_id').value 		= '".$row[csf("rack")]."';\n";
			echo "document.getElementById('from_rack').value 			= '".$rack_no."';\n";
		}
		if($row[csf("shelf")]>0){
			$shelf_no 	= $lib_shelf_arr[$company_id][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('shelf')]];
			echo "document.getElementById('from_shelf_id').value 		= '".$row[csf("shelf")]."';\n";
			echo "document.getElementById('from_shelf').value 			= '".$shelf_no."';\n";
		}

		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		echo "load_room_rack_self_bin('requires/finish_fabric_fso_to_fso_transfer_controller', 'floor','floor_td', $('#cbo_company_id').val(),'',$('#cbo_store_name').val(),this.value);\n";
		echo "document.getElementById('cbo_floor').value 			= '".$row[csf("to_floor_id")]."';\n";

		echo "load_room_rack_self_bin('requires/finish_fabric_fso_to_fso_transfer_controller', 'room','room_td', $('#cbo_company_id').val(),'',$('#cbo_store_name').val(),'".$row[csf('to_floor_id')]."',this.value);\n";
		echo "document.getElementById('cbo_room').value 			= '".$row[csf("to_room")]."';\n";

		echo "load_room_rack_self_bin('requires/finish_fabric_fso_to_fso_transfer_controller', 'rack','rack_td', $('#cbo_company_id').val(),'',$('#cbo_store_name').val(),'".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."',this.value);\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("to_rack")]."';\n";
		echo "load_room_rack_self_bin('requires/finish_fabric_fso_to_fso_transfer_controller', 'shelf','shelf_td', $('#cbo_company_id').val(),'',$('#cbo_store_name').val(),'".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."',this.value);\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("to_shelf")]."';\n";


		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hidden_transfer_qnty').value 		= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("dtls_id")]."';\n";
		echo "document.getElementById('update_trans_to').value 				= '".$row[csf("to_trans_id")]."';\n";
		echo "document.getElementById('update_trans_from').value 			= '".$row[csf("trans_id")]."';\n";
		echo "store_update_upto_disable();\n";
	}

	echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_finish_transfer_entry',1,1);\n";
	exit();

}

if($action=="populate_data_about_order")
{
	$data=explode("**",$data);
	$order_id=$data[0];
	$prod_id=$data[1];

	$sql=sql_select("select
		sum(case when entry_form in(2,22) then quantity end) as grey_fabric_recv,
		sum(case when entry_form in(16) then quantity end) as grey_fabric_issued,
		sum(case when entry_form=45 then quantity end) as grey_fabric_recv_return,
		sum(case when entry_form=51 then quantity end) as grey_fabric_issue_return,
		sum(case when entry_form in(13,81) and trans_type=5 then quantity end) as grey_fabric_trans_recv,
		sum(case when entry_form in(13,80) and trans_type=6 then quantity end) as grey_fabric_trans_issued
		from order_wise_pro_details where trans_id<>0 and prod_id=$prod_id and po_breakdown_id=$order_id and is_deleted=0 and status_active=1");

	$grey_fabric_recv=$sql[0][csf('grey_fabric_recv')]+$sql[0][csf('grey_fabric_trans_recv')]+$sql[0][csf('grey_fabric_issue_return')];
	$grey_fabric_issued=$sql[0][csf('grey_fabric_issued')]+$sql[0][csf('grey_fabric_trans_issued')]+$sql[0][csf('grey_fabric_recv_return')];
	$yet_issue=$grey_fabric_recv-$grey_fabric_issued;

	echo "$('#txt_stock').val('".$yet_issue."');\n";

	exit();
}

if ($action=="orderToorderTransfer_popup")
{
	echo load_html_head_contents("Order To Order Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			$('#transfer_id').val(data);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:780px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:760px;margin-left:10px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
					<thead>
						<th>Search By</th>
						<th id="search_by_td_up">Please Enter Transfer ID</th>
						<th>Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:80px;" class="formbutton" />
							<input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<?
							$search_by_arr=array(1=>"Transfer ID",2=>"Challan No.");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td id="search_by_td">
							<input type="text" style="width:170px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td >
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly>
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" readonly>
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_transfer_search_list_view', 'search_div', 'finish_fabric_fso_to_fso_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;" />
						</td>
					</tr>
					<tr>
						<td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$date_form=$data[3];
	$date_to =$data[4];

	if ($data[3]!="" &&  $data[4]!="")
	{
		if($db_type==0)
		{
			$transfer_date_cond = " and transfer_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$transfer_date_cond = " and transfer_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else
		$transfer_date_cond ="";

	if($search_by==1)
		$search_field="transfer_prefix_number";
	else
		$search_field="challan_no";

	if($db_type==0) $year_field="YEAR(insert_date) as year,";
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later

	$sql="select id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_mst where item_category=2 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=4 and entry_form=230 and status_active=1 and is_deleted=0 $transfer_date_cond order by id";

	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,80,90,130","750","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');

	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("select transfer_system_id,challan_no, company_id, transfer_date, item_category, from_order_id, to_order_id from inv_item_transfer_mst where id='$data'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "get_php_form_data('".$row[csf("from_order_id")]."**from'".",'populate_data_from_order','requires/finish_fabric_fso_to_fso_transfer_controller');\n";

		echo "show_list_view(".$row[csf('from_order_id')]."+'_'+".$row[csf('company_id')].",'show_dtls_list_view', 'list_fabric_desc_container','requires/finish_fabric_fso_to_fso_transfer_controller','');\n";

		echo "get_php_form_data('".$row[csf("to_order_id")]."**to'".",'populate_data_from_order','requires/finish_fabric_fso_to_fso_transfer_controller');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "store_update_upto_disable();\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_finish_transfer_entry',1,1);\n";
		exit();
	}
}



if ($action=="orderInfo_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	</head>

	<body>
		<div align="center" style="width:770px;">
			<form name="searchdescfrm"  id="searchdescfrm">
				<fieldset style="width:760px;margin-left:15px">
					<legend><? echo ucfirst($type); ?> Order Info</legend>
					<br>
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr bgcolor="#FFFFFF">
							<td align="center"><? echo ucfirst($type); ?> Order No: <b><? echo $txt_order_no; ?></b></td>
						</tr>
					</table>
					<br>
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="750" align="center">
						<thead>
							<th width="40">SL</th>
							<th width="100">Required</th>
							<?
							if($type=="from")
							{
								?>
								<th width="100">Knitted</th>
								<th width="100">Issue to dye</th>
								<th width="100">Issue Return</th>
								<th width="100">Transfer Out</th>
								<th width="100">Transfer In</th>
								<th>Remaining</th>
								<?
							}
							else
							{
								?>
								<th width="80">Yrn. Issued</th>
								<th width="80">Yrn. Issue Rtn</th>
								<th width="80">Knitted</th>
								<th width="90">Issue Rtn.</th>
								<th width="100">Transf. Out</th>
								<th width="100">Transf. In</th>
								<th>Shortage</th>
								<?
							}
							?>

						</thead>
						<?
						$req_qty=return_field_value("sum(b.grey_fab_qnty) as grey_req_qnty","wo_booking_mst a, wo_booking_dtls b","a.booking_no=b.booking_no and a.item_category in(2,13) and b.po_break_down_id=$txt_order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1","grey_req_qnty");

						$sql="select
						sum(CASE WHEN entry_form ='3' THEN quantity ELSE 0 END) AS issue_qnty,
						sum(CASE WHEN entry_form ='5' THEN quantity ELSE 0 END) AS dye_issue_qnty,
						sum(CASE WHEN entry_form ='9' THEN quantity ELSE 0 END) AS return_qnty,
						sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_out_qnty,
						sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_in_qnty,
						sum(CASE WHEN trans_id<>0 and entry_form in(2,22) THEN quantity ELSE 0 END) AS knit_qnty
						from order_wise_pro_details where po_breakdown_id=$txt_order_id and status_active=1 and is_deleted=0";
						$dataArray=sql_select($sql);
						$remaining=0; $shoratge=0;
						?>
						<tr bgcolor="#EFEFEF">
							<td>1</td>
							<td align="right"><? echo number_format($req_qty,2); ?>&nbsp;</td>
							<?
							if($type=="from")
							{
								$remaining=$dataArray[0][csf('issue_qnty')]-$dataArray[0][csf('return_qnty')]-$dataArray[0][csf('transfer_out_qnty')]+$dataArray[0][csf('transfer_in_qnty')]-$dataArray[0][csf('knit_qnty')];
								?>
								<td align="right"><? echo number_format($dataArray[0][csf('knit_qnty')],2); ?>&nbsp;</td>
								<td align="right"><? echo number_format($dataArray[0][csf('dye_issue_qnty')],2); ?></td>
								<td align="right"><? echo number_format($dataArray[0][csf('return_qnty')],2); ?>&nbsp;</td>
								<td align="right"><? echo number_format($dataArray[0][csf('transfer_in_qnty')],2); ?></td>
								<td align="right"><? echo number_format($dataArray[0][csf('transfer_out_qnty')],2); ?>&nbsp;</td>
								<td align="right"><? echo number_format($remaining,2); ?>&nbsp;</td>
								<?
							}
							else
							{
								$shoratge=$req_qty-$dataArray[0][csf('issue_qnty')]-$dataArray[0][csf('return_qnty')]+$dataArray[0][csf('transfer_out_qnty')]-$dataArray[0][csf('transfer_in_qnty')];
								?>
								<td align="right"><? echo number_format($dataArray[0][csf('issue_qnty')],2); ?>&nbsp;</td>
								<td align="right"><? echo number_format($dataArray[0][csf('return_qnty')],2); ?></td>
								<td align="right"><? echo number_format($dataArray[0][csf('knit_qnty')],2); ?>&nbsp;</td>
								<td align="right"><? echo number_format($dataArray[0][csf('return_qnty')],2); ?></td>
								<td align="right"><? echo number_format($dataArray[0][csf('transfer_in_qnty')],2); ?>&nbsp;</td>
								<td align="right"><? echo number_format($dataArray[0][csf('transfer_out_qnty')],2); ?>&nbsp;</td>
								<td align="right"><? echo number_format($shoratge,2); ?>&nbsp;</td>
								<?
							}

							?>
						</tr>
					</table>
					<table>
						<tr>
							<td align="center" >
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	</html>
	<?
	exit();
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	for($j=1;$j<=$total_row;$j++)
	{
		$productId="productId_".$j;
		$prod_ids .= $$productId.",";
	}
	$prod_ids=implode(",",array_unique(explode(",",chop($prod_ids,","))));
	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($prod_ids) and transaction_type in (1,4,5)", "max_date");
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$transfer_date = date("Y-m-d", strtotime(str_replace("'","",$txt_transfer_date)));
	if ($transfer_date < $max_recv_date)
	{
		echo "20**Transfer Date Can not Be Less Than Last Receive Date Of This Lot";
		die;
	}

	$from_floor_id = (str_replace("'", "", $from_floor_id) =="")? 0 :str_replace("'", "", $from_floor_id);
	$from_room_id = (str_replace("'", "", $from_room_id)=="")? 0 :str_replace("'", "", $from_room_id);
	$from_rack_id = (str_replace("'", "", $from_rack_id)=="")? 0 :str_replace("'", "", $from_rack_id);
	$from_shelf_id = (str_replace("'", "", $from_shelf_id)=="")? 0 :str_replace("'", "", $from_shelf_id);

	$cbo_floor = (str_replace("'", "", $cbo_floor) =="")? 0 :str_replace("'", "", $cbo_floor);
	$cbo_room = (str_replace("'", "", $cbo_room)=="")? 0 :str_replace("'", "", $cbo_room);
	$txt_rack = (str_replace("'", "", $txt_rack)=="")? 0 :str_replace("'", "", $txt_rack);
	$txt_shelf = (str_replace("'", "", $txt_shelf)=="")? 0 :str_replace("'", "", $txt_shelf);


	$rcvDeli_Con="";	
	$store_update_upto=str_replace("'","",$store_update_upto);
	if($store_update_upto > 1)
	{
		if($store_update_upto==5)
		{
			if(str_replace("'","",$from_floor_id)!=0){$rcvDeli_Con= " and b.floor=$from_floor_id" ;}
			if(str_replace("'","",$from_room_id)!=0){$rcvDeli_Con.= " and b.room=$from_room_id" ;}
			if(str_replace("'","",$from_rack_id)!=0){$rcvDeli_Con.= " and b.rack_no=$from_rack_id" ;}
			if(str_replace("'","",$txt_shelf)!=0){$rcvDeli_Con.= " and b.shelf_no=$from_shelf_id" ;}
		}
		else if($store_update_upto==4)
		{
			if(str_replace("'","",$from_floor_id)!=0){$rcvDeli_Con= " and b.floor=$from_floor_id" ;}
			if(str_replace("'","",$from_room_id)!=0){$rcvDeli_Con.= " and b.room=$from_room_id" ;}
			if(str_replace("'","",$from_rack_id)!=0){$rcvDeli_Con.= " and b.rack_no=$from_rack_id" ;}
		}
		else if($store_update_upto==3)
		{
			if(str_replace("'","",$from_floor_id)!=0){$rcvDeli_Con= " and b.floor=$from_floor_id" ;}
			if(str_replace("'","",$from_room_id)!=0){$rcvDeli_Con.= " and b.room=$from_room_id" ;}
		}
		else if($store_update_upto==2)
		{
			if(str_replace("'","",$from_floor_id)!=0){$rcvDeli_Con= " and a.floor=$from_floor_id" ;}
		}
	}

	$transtoCon="";	
	$store_update_upto=str_replace("'","",$store_update_upto);
	if($store_update_upto > 1)
	{
		if($store_update_upto==5)
		{
			if(str_replace("'","",$from_floor_id)!=0){$transtoCon= " and b.to_floor_id=$from_floor_id" ;}
			if(str_replace("'","",$from_room_id)!=0){$transtoCon.= " and b.to_room=$from_room_id" ;}
			if(str_replace("'","",$from_rack_id)!=0){$transtoCon.= " and b.to_rack=$from_rack_id" ;}
			if(str_replace("'","",$txt_shelf)!=0){$transtoCon.= " and b.to_shelf=$from_shelf_id" ;}
		}
		else if($store_update_upto==4)
		{
			if(str_replace("'","",$from_floor_id)!=0){$transtoCon= " and b.to_floor_id=$from_floor_id" ;}
			if(str_replace("'","",$from_room_id)!=0){$transtoCon.= " and b.to_room=$from_room_id" ;}
			if(str_replace("'","",$from_rack_id)!=0){$transtoCon.= " and b.to_rack=$from_rack_id" ;}
		}
		else if($store_update_upto==3)
		{
			if(str_replace("'","",$from_floor_id)!=0){$transtoCon= " and b.to_floor_id=$from_floor_id" ;}
			if(str_replace("'","",$from_room_id)!=0){$transtoCon.= " and b.to_room=$from_room_id" ;}
		}
		else if($store_update_upto==2)
		{
			if(str_replace("'","",$from_floor_id)!=0){$transtoCon= " and b.to_floor_id=$from_floor_id" ;}
		}
	}

	$transferedCond="";	
	$store_update_upto=str_replace("'","",$store_update_upto);
	if($store_update_upto > 1)
	{
		if($store_update_upto==5)
		{
			if(str_replace("'","",$from_floor_id)!=0){$transferedCond= " and b.floor_id=$from_floor_id" ;}
			if(str_replace("'","",$from_room_id)!=0){$transferedCond.= " and b.room=$from_room_id" ;}
			if(str_replace("'","",$from_rack_id)!=0){$transferedCond.= " and b.rack=$from_rack_id" ;}
			if(str_replace("'","",$txt_shelf)!=0){$transferedCond.= " and b.shelf=$from_shelf_id" ;}
		}
		else if($store_update_upto==4)
		{
			if(str_replace("'","",$from_floor_id)!=0){$transferedCond= " and b.floor_id=$from_floor_id" ;}
			if(str_replace("'","",$from_room_id)!=0){$transferedCond.= " and b.room=$from_room_id" ;}
			if(str_replace("'","",$from_rack_id)!=0){$transferedCond.= " and b.rack=$from_rack_id" ;}
		}
		else if($store_update_upto==3)
		{
			if(str_replace("'","",$from_floor_id)!=0){$transferedCond= " and b.floor_id=$from_floor_id" ;}
			if(str_replace("'","",$from_room_id)!=0){$transferedCond.= " and b.room=$from_room_id" ;}
		}
		else if($store_update_upto==2)
		{
			if(str_replace("'","",$from_floor_id)!=0){$transferedCond= " and b.floor_id=$from_floor_id" ;}
		}
	}

	//--------------------Server side from stock quantity check start----------------------------------------
	$rcv_trnsin_sql = sql_select("
	select x.po_breakdown_id, x.batch_id, x.prod_id, x.body_part_id, x.store_id, x.room, x.floor, x.rack_no, x.shelf_no, x.fabric_shade, x.dia_width_type, sum(quantity) as quantity
	from (
		select c.po_breakdown_id, b.batch_id, b.prod_id,  b.body_part_id, a.store_id, b.room, b.floor, b.rack_no, b.shelf_no, b.fabric_shade, b.dia_width_type, sum(c.quantity) as quantity
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
		where a.id = b.mst_id and b.id = c.dtls_id and a.entry_form in (7,225) and c.entry_form in (7,225) and b.is_sales=1 and c.is_sales=1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
		and c.po_breakdown_id =$txt_from_order_id and a.store_id=$from_store_id $rcvDeli_Con and b.prod_id =$txt_product_id and a.receive_basis in(5,10,14) and c.trans_id >0
		group by c.po_breakdown_id, b.batch_id, b.prod_id,  b.body_part_id, a.store_id, b.room, b.floor, b.rack_no, b.shelf_no, b.fabric_shade, b.dia_width_type
		union all
		select a.to_order_id as po_breakdown_id,b.to_batch_id as batch_id, b.from_prod_id as prod_id, b.body_part_id, b.to_store as store_id, b.to_room as room, b.to_floor_id as floor, b.to_rack as rack_no, b.to_shelf as shelf_no, b.fabric_shade,  b.dia_width_type, sum(b.transfer_qnty) as quantity
		from inv_item_transfer_mst a, inv_item_transfer_dtls b
		where a.id=b.mst_id and a.entry_form in(230) and a.to_order_id =$txt_from_order_id and b.to_store=$from_store_id $transtoCon and b.from_prod_id =$txt_product_id
		and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
		group by a.to_order_id, b.to_batch_id, b.from_prod_id, b.body_part_id, b.to_store, b.to_room, b.to_floor_id, b.to_rack, b.to_shelf, b.fabric_shade, b.dia_width_type
	) x
	group by  po_breakdown_id, batch_id, prod_id, body_part_id, store_id, room, floor, rack_no, shelf_no, fabric_shade, dia_width_type");

	$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
	foreach ($rcv_trnsin_sql as $val)
	{
		if($val[csf("floor")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor")];
		if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
		if($val[csf("rack_no")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack_no")];
		if($val[csf("shelf_no")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("shelf_no")];
		$rcv_transin_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("quantity")];
		//echo $val[csf("po_breakdown_id")].']['.$val[csf("batch_id")].']['.$val[csf("prod_id")].']['.$val[csf("store_id")].']['.$sql_floor_id.']['.$sql_room_id.']['.$sql_rack_id.']['.$sql_shelf_id.']['.$val[csf("fabric_shade")].']['.$val[csf("dia_width_type")].'==<br>';
	}

	$delivery_qnty_sql = sql_select("select c.po_breakdown_id, b.batch_id,b.store_id, b.prod_id,b.room,b.floor, b.rack_no,b.shelf_no, b.fabric_shade, b.width_type, sum(c.quantity) as  quantity from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c where a.id = b.mst_id and b.id=c.dtls_id and c.po_breakdown_id= $txt_from_order_id and b.prod_id=$txt_product_id and b.store_id=$from_store_id $rcvDeli_Con and c.status_active =1 and a.entry_form in (224,287) and c.entry_form in (224,287) group by c.po_breakdown_id, b.batch_id,b.store_id, b.prod_id,b.room,b.floor, b.rack_no,b.shelf_no, b.fabric_shade, b.width_type");

	$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
	foreach ($delivery_qnty_sql as $val)
	{
		if($val[csf("floor")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor")];
		if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
		if($val[csf("rack_no")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack_no")];
		if($val[csf("shelf_no")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("shelf_no")];

		$delivery_qnty_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id][$val[csf("fabric_shade")]][$val[csf("width_type")]] += $val[csf("quantity")];
	}

	$issue_return_sql = sql_select("SELECT e.po_breakdown_id,b.batch_id,d.store_id,b.prod_id,b.room,b.floor,b.rack_no, b.shelf_no,b.fabric_shade, b.dia_width_type, sum(e.quantity) as qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction d , order_wise_pro_details e where a.id=b.mst_id  and b.mst_id=d.mst_id and b.trans_id = d.id and d.id = e.trans_id and b.id = e.dtls_id  and d.item_category=2 and d.transaction_type in (4) and a.entry_form in (233) and e.entry_form = 233 and e.po_breakdown_id = $txt_from_order_id $rcvDeli_Con and b.prod_id=$txt_product_id and d.store_id=$from_store_id and e.is_sales=1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 group by  e.po_breakdown_id,b.batch_id,d.store_id,b.prod_id,b.room,b.floor,b.rack_no, b.shelf_no,b.fabric_shade,b.dia_width_type");

	$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
	foreach ($issue_return_sql as $val)
	{
		if($val[csf("floor")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor")];
		if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
		if($val[csf("rack_no")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack_no")];
		if($val[csf("shelf_no")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("shelf_no")];

		$issue_return_qnty_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("qnty")];
	}

	$up_trans_cond="";
	if(str_replace("'","",$update_trans_from)!="") {$up_trans_cond=" and b.trans_id !=$update_trans_from";}

	$transfered_fabric_sql = sql_select("select a.from_order_id as po_breakdown_id,b.batch_id, b.from_prod_id as prod_id, b.body_part_id,b.feb_description_id as fabric_description_id,b.from_store as store_id,b.uom, b.floor_id, b.room, b.rack as rack_no,b.shelf as shelf_no, b.color_id,b.fabric_shade,b.dia_width_type, b.dia_width as width, b.gsm, sum(b.transfer_qnty) as quantity
	from inv_item_transfer_mst a, inv_item_transfer_dtls b
	where a.id=b.mst_id and a.entry_form in(230) and a.from_order_id =$txt_from_order_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and b.from_prod_id =$txt_product_id and b.from_store= $from_store_id $transferedCond $up_trans_cond
	group by a.from_order_id, b.batch_id, b.from_prod_id, b.body_part_id, b.feb_description_id, b.from_store, b.uom, b.floor_id, b.room, b.rack, b.shelf, b.color_id,b.fabric_shade,b.dia_width_type, b.dia_width, b.gsm");

	$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
	foreach ($transfered_fabric_sql as $val)
	{
		if($val[csf("floor_id")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor_id")];
		if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
		if($val[csf("rack_no")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack_no")];
		if($val[csf("shelf_no")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("shelf_no")];

		$transfered_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("quantity")];
	}
	//echo str_replace("'", "", $txt_from_order_id).']['.str_replace("'", "", $txt_batch_id).']['.str_replace("'", "", $txt_product_id).']['.str_replace("'", "", $from_store_id).']['.str_replace("'", "", $from_floor_id).']['.str_replace("'", "", $from_room_id).']['.str_replace("'", "", $from_rack_id).']['.str_replace("'", "", $from_shelf_id).']['.str_replace("'", "", $cbo_fabric_shade).']['.str_replace("'", "", $txt_dia_width_type).'*<br>';

	$recv_transin_qnty = $rcv_transin_arr[str_replace("'", "", $txt_from_order_id)][str_replace("'", "", $txt_batch_id)][str_replace("'", "", $txt_product_id)][str_replace("'", "", $from_store_id)][str_replace("'", "", $from_floor_id)][str_replace("'", "", $from_room_id)][str_replace("'", "", $from_rack_id)][str_replace("'", "", $from_shelf_id)][str_replace("'", "", $cbo_fabric_shade)][str_replace("'", "", $txt_dia_width_type)];
	$delivery_qnty = $delivery_qnty_arr[str_replace("'", "", $txt_from_order_id)][str_replace("'", "", $txt_batch_id)][str_replace("'", "", $txt_product_id)][str_replace("'", "", $from_store_id)][str_replace("'", "", $from_floor_id)][str_replace("'", "", $from_room_id)][str_replace("'", "", $from_rack_id)][str_replace("'", "", $from_shelf_id)][str_replace("'", "", $cbo_fabric_shade)][str_replace("'", "", $txt_dia_width_type)];
	$issue_return_qnty = $issue_return_qnty_arr[str_replace("'", "", $txt_from_order_id)][str_replace("'", "", $txt_batch_id)][str_replace("'", "", $txt_product_id)][str_replace("'", "", $from_store_id)][str_replace("'", "", $from_floor_id)][str_replace("'", "", $from_room_id)][str_replace("'", "", $from_rack_id)][str_replace("'", "", $from_shelf_id)][str_replace("'", "", $cbo_fabric_shade)][str_replace("'", "", $txt_dia_width_type)];
	$transfer_out_qnty = $transfered_arr[str_replace("'", "", $txt_from_order_id)][str_replace("'", "", $txt_batch_id)][str_replace("'", "", $txt_product_id)][str_replace("'", "", $from_store_id)][str_replace("'", "", $from_floor_id)][str_replace("'", "", $from_room_id)][str_replace("'", "", $from_rack_id)][str_replace("'", "", $from_shelf_id)][str_replace("'", "", $cbo_fabric_shade)][str_replace("'", "", $txt_dia_width_type)];

	$trans_qnty=str_replace("'","",$txt_transfer_qnty)*1;
	$stock_qnty=($recv_transin_qnty + $issue_return_qnty) - ($delivery_qnty + $transfer_out_qnty );

	// echo "10**".$recv_transin_qnty."+=iss ret".$issue_return_qnty.",-iss".$delivery_qnty.",+trans out=".$transfer_out_qnty;die;
	if($trans_qnty>$stock_qnty)
	{
		echo "20**Transfer quantity is not available in this Store.\nAvailable=$stock_qnty";
		die;
	}

	//echo "10**".$recv_transin_qnty."=iss".$delivery_qnty.",=iss ret".$issue_return_qnty.",trans out=".$transfer_out_qnty;die;

	//--------------------------Server side check end------------------------------------------------------

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$transfer_recv_num=''; $transfer_update_id='';

		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";

			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,'FSTST',230,date("Y",time()),3 ));

			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);

			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company, from_order_id, to_order_id, item_category, inserted_by, insert_date";

			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",230,4,0,".$txt_from_order_id.",".$txt_to_order_id.",2,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_id."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}

		//$rate=0; $amount=0;
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, order_qnty, order_rate, order_amount, cons_rate, cons_amount,floor_id, room, rack, self, machine_id, pi_wo_batch_no, store_id, inserted_by, insert_date";


		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, item_category, transfer_qnty, rate, rate_in_usd, transfer_value, transfer_value_in_usd, uom, rack, shelf, to_rack, to_shelf, gsm, dia_width, machine_no_id,feb_description_id,body_part_id,from_store, to_store, floor_id, room, to_floor_id,  to_room, batch_id, to_batch_id, inserted_by, insert_date,color_id,fabric_shade,dia_width_type,aop_rate,aop_amount";


		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date,color_id,is_sales";

		$field_array_batch_dtls="id, mst_id, po_id, prod_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id, width_dia_type, body_part_id, is_sales, inserted_by, insert_date";


		//echo "10**";die;

		//txt_system_id*cbo_company_id*txt_transfer_date*txt_challan_no*txt_from_order_id*txt_to_order_id*txt_product_id*fabric_desc_id*txt_batch_id*txt_gsm*txt_width*txt_dia_width_type*txt_machine_id*txt_color_id*cbo_fabric_shade*cbo_uom*txt_body_part_id*txt_rate*txt_cons_rate*from_store_id*from_floor_id*from_room_id*from_rack_id*from_shelf_id*txt_transfer_qnty*update_dtls_id*update_trans_from*update_trans_to*update_id  cbo_floor*cbo_room*txt_rack*txt_shelf

		//echo "10**select a.id, a.batch_weight, a.booking_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.batch_no=$txt_batch_no and a.color_id=$txt_color_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=230 and a.company_id=$cbo_company_id and a.sales_order_id= $txt_to_order_id group by a.id, a.batch_weight, a.booking_no";

		$batchData=sql_select("select a.id, a.batch_weight, a.booking_no from pro_batch_create_mst a where a.batch_no=$txt_batch_no and a.color_id=$txt_color_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=230 and a.company_id=$cbo_company_id and a.sales_order_id= $txt_to_order_id group by a.id, a.batch_weight, a.booking_no");

		if(count($batchData)>0)
		{
			$batch_id_to=$batchData[0][csf('id')];
			$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty);
			$field_array_batch_update="batch_weight*updated_by*update_date";
			$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		else
		{

			$sales_book_data = sql_select("select sales_booking_no, booking_id, booking_without_order from fabric_sales_order_mst where id=$txt_to_order_id");
			$sales_booking_no =$sales_book_data[0][csf('sales_booking_no')];
			$sales_booking_id =$sales_book_data[0][csf('booking_id')];
			$booking_without_order =$sales_book_data[0][csf('booking_without_order')];

			//$booking_id = str_replace("'", "", $hdn_to_booking_id);
			$booking_no = str_replace("'", "", $txt_to_booking_no);  

			$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
			$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,sales_order_no,sales_order_id,is_sales,inserted_by,insert_date";

			$data_array_batch="(".$batch_id_to.",".$txt_batch_no.",230,".$txt_transfer_date.",".$cbo_company_id.",'".$sales_booking_id."','".$booking_no."','".$booking_without_order."',".$txt_color_id.",".$txt_transfer_qnty.",".$txt_to_order_no.",".$txt_to_order_id.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}

		//echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;
		//oci_rollback($con);
		//die;

		$cons_amount = str_replace("'","",$txt_cons_rate)*str_replace("'","",$txt_transfer_qnty);
		$order_amount = str_replace("'","",$txt_rate)*str_replace("'","",$txt_transfer_qnty);
		$aop_amount = str_replace("'","",$txt_aop_rate)*str_replace("'","",$txt_transfer_qnty);

		if($data_array_trans!="") $data_array_trans.=",";
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$data_array_trans.="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$txt_product_id.",2,6,".$txt_transfer_date.",".$txt_from_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_transfer_qnty.",".$txt_rate.",".$order_amount.",".$txt_cons_rate.",".$cons_amount.",".$from_floor_id.",".$from_room_id.",".$from_rack_id.",".$from_shelf_id.",".$txt_machine_id.",".$txt_batch_id.",".$from_store_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$from_trans_id=$id_trans;
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$data_array_trans.=",(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$txt_product_id.",2,5,".$txt_transfer_date.",".$txt_to_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_transfer_qnty.",".$txt_rate.",".$order_amount.",".$txt_cons_rate.",".$cons_amount.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$txt_machine_id.",".$batch_id_to.",".$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
		if($data_array_dtls!="") $data_array_dtls.=",";
		$data_array_dtls.="(".$id_dtls.",".$transfer_update_id.",".$from_trans_id.",".$id_trans.",".$txt_product_id.",2,".$txt_transfer_qnty.",".$txt_cons_rate.",".$txt_rate.",".$cons_amount.",".$order_amount.",".$cbo_uom.",".$from_rack_id.",".$from_shelf_id.",".$txt_rack.",".$txt_shelf.",".$txt_gsm.",".$txt_width.",".$txt_machine_id.",".$fabric_desc_id.",".$txt_body_part_id.",".$from_store_id.",".$cbo_store_name.",".$from_floor_id.",".$from_room_id.",".$cbo_floor.",".$cbo_room.",".$txt_batch_id.",".$batch_id_to.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_color_id.",".$cbo_fabric_shade.",".$txt_dia_width_type.",".$txt_aop_rate.",'".$aop_amount."')";


		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		if($data_array_prop!="") $data_array_prop.= ",";
		$data_array_prop.="(".$id_prop.",".$from_trans_id.",6,230,".$id_dtls.",".$txt_from_order_id.",".$txt_product_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_color_id.",1)";

		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$data_array_prop.=",(".$id_prop.",".$id_trans.",5,230,".$id_dtls.",".$txt_to_order_id.",".$txt_product_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_color_id.",1)";


		//$field_array_batch_dtls="id, mst_id, po_id, prod_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id,width_dia_type, inserted_by, insert_date";

		$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
		if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
		$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$txt_to_order_id.",".$txt_product_id.",".$txt_item_desc.",0,0,0,".$txt_transfer_qnty.",".$id_dtls.",".$txt_dia_width_type.",".$txt_body_part_id.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		//echo "10**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;
		//oci_rollback($con);
		//die;

		$rID=$rID2=$rID3=$rID4=$rID5=$rID6=1;
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		}

		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);


		if(count($batchData)>0)
		{
			//echo "10**";echo $data_array_batch_update."==".$batch_id_to;die;
			$rID5=sql_update("pro_batch_create_mst",$field_array_batch_update,$data_array_batch_update,"id",$batch_id_to,0);
		}
		else
		{
			//echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;oci_rollback($con);die;
			$rID5=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
		}

		$rID6=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,0);
		
		//echo "10**insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con); die;
		//echo "10**$rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6";oci_rollback($con); die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6)
			{
				mysql_query("COMMIT");
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6)
			{
				oci_commit($con);
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//--------------------Server side to stock quantity check start----------------------------------------

		$table_qnty_sql = sql_select("select transfer_qnty from inv_item_transfer_dtls where id = $update_dtls_id");
		$table_qnty = $table_qnty_sql[0][csf("transfer_qnty")];

		$rcv_trnsin_sql = sql_select("
		select x.po_breakdown_id, x.batch_id, x.prod_id, x.body_part_id, x.store_id, x.room, x.floor, x.rack_no, x.shelf_no, x.fabric_shade, x.dia_width_type, sum(quantity) as quantity
		from (
			select c.po_breakdown_id, b.batch_id, b.prod_id,  b.body_part_id, a.store_id, b.room, b.floor, b.rack_no, b.shelf_no, b.fabric_shade, b.dia_width_type, sum(c.quantity) as quantity
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
			where a.id = b.mst_id and b.id = c.dtls_id and a.entry_form in (7,225) and c.entry_form in (7,225) and b.is_sales=1 and c.is_sales=1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
			and c.po_breakdown_id =$txt_to_order_id and a.store_id=$cbo_store_name and b.prod_id =$txt_product_id and a.receive_basis in(5,10,14) and c.trans_id >0
			group by c.po_breakdown_id, b.batch_id, b.prod_id,  b.body_part_id, a.store_id, b.room, b.floor, b.rack_no, b.shelf_no, b.fabric_shade, b.dia_width_type
			union all
			select a.to_order_id as po_breakdown_id,b.to_batch_id as batch_id, b.from_prod_id as prod_id, b.body_part_id, b.to_store as store_id, b.to_room as room, b.to_floor_id as floor, b.to_rack as rack_no, b.to_shelf as shelf_no, b.fabric_shade,  b.dia_width_type, sum(b.transfer_qnty) as quantity
			from inv_item_transfer_mst a, inv_item_transfer_dtls b
			where a.id=b.mst_id and a.entry_form in(230) and a.to_order_id =$txt_to_order_id and b.to_store=$cbo_store_name and b.from_prod_id =$txt_product_id
			and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
			group by a.to_order_id, b.to_batch_id, b.from_prod_id, b.body_part_id, b.to_store, b.to_room, b.to_floor_id, b.to_rack, b.to_shelf, b.fabric_shade, b.dia_width_type
		) x
		group by  po_breakdown_id, batch_id, prod_id, body_part_id, store_id, room, floor, rack_no, shelf_no, fabric_shade, dia_width_type ");


		$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
		foreach ($rcv_trnsin_sql as $val)
		{
			if($val[csf("floor")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor")];
			if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
			if($val[csf("rack_no")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack_no")];
			if($val[csf("shelf_no")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("shelf_no")];
			$rcv_transin_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("quantity")];
		}
		unset($rcv_trnsin_sql);

		$delivery_qnty_sql = sql_select("select c.po_breakdown_id, b.batch_id,b.store_id, b.prod_id,b.room,b.floor, b.rack_no,b.shelf_no, b.fabric_shade, b.width_type, sum(c.quantity) as  quantity from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c where a.id = b.mst_id and b.id=c.dtls_id and c.po_breakdown_id= $txt_to_order_id and b.prod_id=$txt_product_id and b.store_id=$cbo_store_name and c.status_active =1 and a.entry_form in (224,287) and c.entry_form in (224,287) group by c.po_breakdown_id, b.batch_id,b.store_id, b.prod_id,b.room,b.floor, b.rack_no,b.shelf_no, b.fabric_shade, b.width_type");

		$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
		foreach ($delivery_qnty_sql as $val)
		{
			if($val[csf("floor")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor")];
			if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
			if($val[csf("rack_no")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack_no")];
			if($val[csf("shelf_no")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("shelf_no")];

			$delivery_qnty_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id][$val[csf("fabric_shade")]][$val[csf("width_type")]] += $val[csf("quantity")];
		}
		unset($delivery_qnty_sql);

		$issue_return_sql = sql_select("SELECT e.po_breakdown_id,b.batch_id,d.store_id,b.prod_id,b.room,b.floor,b.rack_no, b.shelf_no,b.fabric_shade, b.dia_width_type, sum(e.quantity) as qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction d , order_wise_pro_details e where a.id=b.mst_id  and b.mst_id=d.mst_id and b.trans_id = d.id and d.id = e.trans_id and b.id = e.dtls_id  and d.item_category=2 and d.transaction_type in (4) and a.entry_form in (233) and e.entry_form = 233 and e.po_breakdown_id = $txt_to_order_id and b.prod_id=$txt_product_id and d.store_id=$cbo_store_name and e.is_sales=1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 group by  e.po_breakdown_id,b.batch_id,d.store_id,b.prod_id,b.room,b.floor,b.rack_no, b.shelf_no,b.fabric_shade,b.dia_width_type");

		$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
		foreach ($issue_return_sql as $val)
		{
			if($val[csf("floor")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor")];
			if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
			if($val[csf("rack_no")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack_no")];
			if($val[csf("shelf_no")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("shelf_no")];

			$issue_return_qnty_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("qnty")];
		}
		unset($issue_return_sql);

		$transfered_fabric_sql = sql_select("select a.from_order_id as po_breakdown_id,b.batch_id, b.from_prod_id as prod_id, b.body_part_id,b.feb_description_id as fabric_description_id,b.from_store as store_id,b.uom, b.floor_id, b.room, b.rack as rack_no,b.shelf as shelf_no, b.color_id,b.fabric_shade,b.dia_width_type, b.dia_width as width, b.gsm, sum(b.transfer_qnty) as quantity
		from inv_item_transfer_mst a, inv_item_transfer_dtls b
		where a.id=b.mst_id and a.entry_form in(230) and a.from_order_id =$txt_to_order_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and b.from_prod_id =$txt_product_id and b.from_store= $cbo_store_name $up_trans_cond
		group by a.from_order_id, b.batch_id, b.from_prod_id, b.body_part_id, b.feb_description_id, b.from_store, b.uom, b.floor_id, b.room, b.rack, b.shelf, b.color_id,b.fabric_shade,b.dia_width_type, b.dia_width, b.gsm");

		$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
		foreach ($transfered_fabric_sql as $val)
		{
			if($val[csf("floor_id")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor_id")];
			if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
			if($val[csf("rack_no")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack_no")];
			if($val[csf("shelf_no")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("shelf_no")];

			$transfered_arr[$val[csf("po_breakdown_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]][$val[csf("store_id")]][$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] += $val[csf("quantity")];
		}
		unset($transfered_fabric_sql);

		$to_recv_transin_qnty = $rcv_transin_arr[str_replace("'", "", $txt_to_order_id)][str_replace("'", "", $previous_to_batch_id)][str_replace("'", "", $txt_product_id)][str_replace("'", "", $cbo_store_name)][str_replace("'", "", $cbo_floor)][str_replace("'", "", $cbo_room)][str_replace("'", "", $txt_rack)][str_replace("'", "", $txt_shelf)][str_replace("'", "", $cbo_fabric_shade)][str_replace("'", "", $txt_dia_width_type)];
		$to_delivery_qnty = $delivery_qnty_arr[str_replace("'", "", $txt_to_order_id)][str_replace("'", "", $previous_to_batch_id)][str_replace("'", "", $txt_product_id)][str_replace("'", "", $cbo_store_name)][str_replace("'", "", $cbo_floor)][str_replace("'", "", $cbo_room)][str_replace("'", "", $txt_rack)][str_replace("'", "", $txt_shelf)][str_replace("'", "", $cbo_fabric_shade)][str_replace("'", "", $txt_dia_width_type)];
		$to_issue_return_qnty = $issue_return_qnty_arr[str_replace("'", "", $txt_to_order_id)][str_replace("'", "", $previous_to_batch_id)][str_replace("'", "", $txt_product_id)][str_replace("'", "", $cbo_store_name)][str_replace("'", "", $cbo_floor)][str_replace("'", "", $cbo_room)][str_replace("'", "", $txt_rack)][str_replace("'", "", $txt_shelf)][str_replace("'", "", $cbo_fabric_shade)][str_replace("'", "", $txt_dia_width_type)];
		$to_transfer_out_qnty = $transfered_arr[str_replace("'", "", $txt_to_order_id)][str_replace("'", "", $previous_to_batch_id)][str_replace("'", "", $txt_product_id)][str_replace("'", "", $cbo_store_name)][str_replace("'", "", $cbo_floor)][str_replace("'", "", $cbo_room)][str_replace("'", "", $txt_rack)][str_replace("'", "", $txt_shelf)][str_replace("'", "", $cbo_fabric_shade)][str_replace("'", "", $txt_dia_width_type)];

		$trans_qnty=str_replace("'","",$txt_transfer_qnty)*1;
		$hidden_transfer_qnty=str_replace("'","",$hidden_transfer_qnty)*1;
		$to_stock_qnty=($to_recv_transin_qnty + $to_issue_return_qnty) - ($to_delivery_qnty + $to_transfer_out_qnty );

		//echo "10**if(($table_qnty - $trans_qnty) > $to_stock_qnty)";die;
		if(($table_qnty - $trans_qnty) > $to_stock_qnty)
		{
			echo "20**Transfer quantity is not available in to Store.\nAvailable=$to_stock_qnty";
			disconnect($con);die;
		}

		//echo "10**(rcv tran = $to_recv_transin_qnty + issue ret= $to_issue_return_qnty) - (issue= $to_delivery_qnty + trans out=$to_transfer_out_qnty ) == $to_stock_qnty, table_qnty = $table_qnty";
		//die;

		//--------------------------Server side check end------------------------------------------------------
		

		$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_id."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";


		//$rate=0; $amount=0;
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, order_qnty, order_rate, order_amount, cons_rate, cons_amount, floor_id, room, rack, self, machine_id, pi_wo_batch_no, store_id, inserted_by, insert_date";
		$field_array_trans_update="prod_id*transaction_date*order_id*cons_quantity*order_qnty*order_rate*order_amount*cons_rate*cons_amount*floor_id*room*rack*self*store_id*updated_by*update_date";

		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, item_category, transfer_qnty, rate,rate_in_usd, transfer_value, transfer_value_in_usd, uom, rack, shelf, to_rack, to_shelf, gsm, dia_width, machine_no_id,feb_description_id,body_part_id,from_store, to_store, floor_id, room, to_floor_id,  to_room, batch_id, to_batch_id, inserted_by, insert_date,color_id,fabric_shade,dia_width_type";

		$field_array_dtls_update="from_prod_id*transfer_qnty*rate*rate_in_usd*transfer_value*transfer_value_in_usd*to_store*to_floor_id*to_room*to_rack*to_shelf*updated_by*update_date*aop_rate*aop_amount";


		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date,color_id,is_sales";

		$field_array_batch_dtls="id, mst_id, po_id, prod_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id,width_dia_type, body_part_id , is_sales, inserted_by, insert_date";

		//$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount,floor_id, room, rack, self, machine_id, inserted_by, insert_date";

		//txt_system_id*cbo_company_id*txt_transfer_date*txt_challan_no*txt_from_order_id*txt_to_order_id*txt_product_id*fabric_desc_id*txt_batch_id*txt_gsm*txt_width*txt_dia_width_type*txt_machine_id*txt_color_id*cbo_fabric_shade*cbo_uom*txt_body_part_id*txt_rate*txt_cons_rate*from_store_id*from_floor_id*from_room_id*from_rack_id*from_shelf_id*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*txt_transfer_qnty*update_dtls_id*update_trans_from*update_trans_to*update_id



		$batchData=sql_select("select a.id, a.batch_weight, a.booking_no from pro_batch_create_mst a where a.batch_no=$txt_batch_no and a.color_id=$txt_color_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=230 and a.company_id=$cbo_company_id and a.sales_order_id= $txt_to_order_id group by a.id, a.batch_weight, a.booking_no");

		$field_array_batch_update="batch_weight*updated_by*update_date";
		if(count($batchData)>0)
		{
			$batch_id_to=$batchData[0][csf('id')];
			if($batch_id_to==str_replace("'","",$previous_to_batch_id))
			{
				$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty)-str_replace("'", '',$hidden_transfer_qnty);


				$update_batch_id[]=str_replace("'","",$previous_to_batch_id);
				$data_array_batch_update[str_replace("'","",$previous_to_batch_id)]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			else
			{
				//previous batch adjusted
				$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$previous_to_batch_id");
				$adjust_batch_weight=$batch_weight-str_replace("'", '',$hidden_transfer_qnty);
				$data_array_batch_update[str_replace("'","",$previous_to_batch_id)]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				//new batch adjusted
				$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty);
				$update_batch_id[]=$batchData[0][csf('id')];
				$data_array_batch_update[$batchData[0][csf('id')]]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}
		else
		{
			$booking_no = str_replace("'", "", $txt_to_booking_no);  

			$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
			$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,sales_order_no,sales_order_id,is_sales,inserted_by,insert_date";

			$data_array_batch="(".$batch_id_to.",".$txt_batch_no.",230,".$txt_transfer_date.",".$cbo_company_id.",0,'".$booking_no."',0,".$txt_color_id.",".$txt_transfer_qnty.",".$txt_to_order_no.",".$txt_to_order_id.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//previous batch adjusted
			$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$previous_to_batch_id");
			$adjust_batch_weight=$batch_weight-str_replace("'", '',$hidden_transfer_qnty);
			$update_batch_id[]=str_replace("'","",$previous_to_batch_id);
			$data_array_batch_update[str_replace("'","",$previous_to_batch_id)]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}

		$cons_amount = str_replace("'","",$txt_cons_rate)*str_replace("'","",$txt_transfer_qnty);
		$order_amount = str_replace("'","",$txt_rate)*str_replace("'","",$txt_transfer_qnty);
		$aop_amount = str_replace("'","",$txt_aop_rate)*str_replace("'","",$txt_transfer_qnty);

		$update_trans_from = str_replace("'","",$update_trans_from);
		$update_trans_to = str_replace("'","",$update_trans_to);
		$update_dtls_id = str_replace("'","",$update_dtls_id);

		$transId_arr[]=$update_trans_from;
		$data_array_update_trans[$update_trans_from]=explode("*",($txt_product_id."*".$txt_transfer_date."*".$txt_from_order_id."*".$txt_transfer_qnty."*".$txt_transfer_qnty."*".$txt_rate."*".$order_amount."*".$$consRate."*".$cons_amount."*".$from_floor_id."*".$from_room_id."*".$from_rack_id."*".$from_shelf_id."*".$from_store_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

		$transId_arr[]=$update_trans_to;
		$data_array_update_trans[$update_trans_to]=explode("*",($txt_product_id."*".$txt_transfer_date."*".$txt_to_order_id."*".$txt_transfer_qnty."*".$txt_transfer_qnty."*".$txt_rate."*".$order_amount."*".$txt_cons_rate."*".$cons_amount."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

		$dtlsId_arr[]=$update_dtls_id;
		$data_array_update_dtls[$update_dtls_id]=explode("*",($txt_product_id."*".$txt_transfer_qnty."*".$txt_cons_rate."*".$txt_rate."*".$cons_amount."*".$order_amount."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_aop_rate."*'".$aop_amount."'"));

		$data_array_prop = "";

		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);

		if($data_array_prop!="") $data_array_prop.= ",";
		$data_array_prop.="(".$id_prop.",".$update_trans_from.",6,230,".$update_dtls_id.",".$txt_from_order_id.",".$txt_product_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_color_id.",1)";

		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$data_array_prop.=",(".$id_prop.",".$update_trans_to.",5,230,".$update_dtls_id.",".$txt_to_order_id.",".$txt_product_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_color_id.",1)";

		$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
		if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
		$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$txt_to_order_id.",".$txt_product_id.",".$txt_item_desc.",0,0,0,".$txt_transfer_qnty.",".$update_dtls_id.",".$txt_dia_width_type.",".$txt_body_part_id.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;

		$rID2=$rID3=true;
		if(count($data_array_update_dtls)>0)
		{
			$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_update_trans,$transId_arr));
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}

			$rID3=execute_query(bulk_update_sql_statement("inv_item_transfer_dtls","id",$field_array_dtls_update,$data_array_update_dtls,$dtlsId_arr));
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}
		}

		$update_dtls_id=chop($update_dtls_id,',');
		if($update_dtls_id!="")
		{
			$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id in(".$update_dtls_id.") and entry_form=230");
			if($flag==1)
			{
				if($query) $flag=1; else $flag=0;
			}
		}
		//echo "10**$update_dtls_id  insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;

		if($data_array_prop!="")
		{
			$rIDProp=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
			if($flag==1)
			{
				if($rIDProp) $flag=1; else $flag=0;
			}
		}

		if(count($data_array_batch_update)>0)
		{
			//echo "10**"; echo bulk_update_sql_statement("pro_batch_create_mst","id",$field_array_batch_update,$data_array_batch_update,$update_batch_id);oci_rollback($con);die;
			$batchMstUpdate=execute_query(bulk_update_sql_statement("pro_batch_create_mst","id",$field_array_batch_update,$data_array_batch_update,$update_batch_id));
			if($flag==1)
			{
				if($batchMstUpdate) $flag=1; else $flag=0;
			}
		}

		if(count($data_array_batch)>0)
		{
			//echo "10**";die;
			$rID5=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
			if($flag==1)
			{
				if($rID5) $flag=1; else $flag=0;
			}
		}

		/*echo "10**$flag";
		oci_rollback($con);
		die;*/

		$delete_batch_dtls=execute_query( "delete from pro_batch_create_dtls where mst_id=$previous_to_batch_id and dtls_id=$update_dtls_id",0);
		if($flag==1)
		{
			if($delete_batch_dtls) $flag=1; else $flag=0;
		}

		$rID6=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,0);

		//echo "10**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;oci_rollback($con);die;

		if($flag==1)
		{
			if($rID6) $flag=1; else $flag=0;
		}

		//echo "10**$rID=$rID2=$rID3=$query=$rIDProp=$rID6=$flag";die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**"."&nbsp;"."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**"."&nbsp;"."**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$update_dtls_id = str_replace("'", "", $update_dtls_id);
		$update_trans_from = str_replace("'", "", $update_trans_from);
		$update_trans_to = str_replace("'", "", $update_trans_to);
		$previous_from_batch_id = str_replace("'", "", $previous_from_batch_id);
		$previous_to_batch_id = str_replace("'", "", $previous_to_batch_id);

		if($update_dtls_id == "" || $update_trans_to == "")
		{
			echo "20**Delete Failed";
			disconnect($con);die;
		}		

		$sql = sql_select("SELECT a.prod_id, a.store_id, a.pi_wo_batch_no as batch_id, a.cons_quantity from inv_transaction a, product_details_master b where a.status_active=1 and a.id=$update_trans_to and a.prod_id=b.id");
		if (!empty($sql)) 
		{
			echo "20**Delete Failed";
			disconnect($con);die;
		}
		
		foreach( $sql as $row)
		{
			$before_prod_id 		= $row[csf("prod_id")];
			$before_store_id		= $row[csf("store_id")];
			$before_batch_id		= $row[csf("batch_id")];
			$before_cons_quantity	= $row[csf("cons_quantity")];
		}

		$max_trans_query = sql_select("SELECT  max(id) as max_id from inv_transaction where prod_id =$before_prod_id and store_id=$before_store_id and pi_wo_batch_no=$before_batch_id and item_category=2 and status_active=1");

		$max_trans_id = $max_trans_query[0][csf('max_id')];
		if($max_trans_id > $update_trans_to)
		{
			echo "20**Delete not allowed. Next transaction found.";
			disconnect($con);die;
		}

		$batchData=sql_select("SELECT a.id, a.batch_weight, a.booking_no from pro_batch_create_mst a where a.status_active=1 and a.is_deleted=0 and a.entry_form=230 and a.company_id=$cbo_company_id and a.id=$before_batch_id");
			
		$checkTransaction = sql_select("SELECT id from inv_item_transfer_dtls where status_active=1 and is_deleted=0 and mst_id = ".$update_id." and id !=".$update_dtls_id."");

		$flag=1;
		$rID=execute_query("update inv_item_transfer_dtls set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id =$update_dtls_id");
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}

		$rID2=execute_query("update inv_transaction set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in ($update_trans_from, $update_trans_to)");
		if($flag==1) { if($rID2) $flag=1; else $flag=0; }

		$rID3=execute_query("update order_wise_pro_details set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where trans_id in ($update_trans_from, $update_trans_to)");
		if($flag==1) { if($rID3) $flag=1; else $flag=0; }

		$curr_batch_weight=$batchData[0][csf('batch_weight')]-$before_cons_quantity;
		$field_array_batch_update="batch_weight*updated_by*update_date";
		$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rIDBatch=sql_update("pro_batch_create_mst",$field_array_batch_update,$data_array_batch_update,"id*entry_form",$before_batch_id.'*230',0);
		if($rIDBatch) $flag=1; else $flag=0;

		$field_array_batch_dtls="updated_by*update_date*status_active*is_deleted";
		$data_array_batch_dtls="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$batchDtls=sql_update("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,"mst_id*dtls_id","$before_batch_id*$update_dtls_id",1);
		if($batchDtls) $flag=1; else $flag=0;

		if(count($checkTransaction) == 0)
		{
			$field_array_update="updated_by*update_date*status_active*is_deleted";
			$data_array_update="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$is_mst_del = sql_update("inv_item_transfer_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
			if($is_mst_del) $flag=1; else $flag=0;
		}

		// echo "10**$flag##$rID##$rID2##$rID3##$rIDBatch##$batchDtls##$is_mst_del";die;
		// oci_rollback($con);disconnect($con);die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0"."**".$is_mst_del;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**"."&nbsp;"."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0"."**".$is_mst_del;
			}
			else
			{
				oci_rollback($con);
				echo "6**0**"."&nbsp;"."**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="finish_fabric_order_to_order_transfer_print")
{
	echo "Print Not Available";
	die;
	extract($_REQUEST);
	$data=explode('*',$data);
	$all_order_id=$data[3].",".$data[4];
	//print_r ($data);

	$sql="select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_library=return_library_array( "select id, color_name from  lib_color where status_active=1", "id", "color_name"  );
	$batch_result=sql_select( "select a.id, a.batch_no, a.color_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id=$data[3]");
	$batch_arr=array();
	foreach($batch_result as $row)
	{
		$batch_arr[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_arr[$row[csf("id")]]["color_id"]=$row[csf("color_id")];
	}

	$prod_sql=sql_select("select id, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id=2");
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
		$product_arr[$row[csf("id")]]["detarmination_id"]=$row[csf("detarmination_id")];
		$product_arr[$row[csf("id")]]["gsm"]=$row[csf("gsm")];
		$product_arr[$row[csf("id")]]["dia_width"]=$row[csf("dia_width")];
	}

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$poDataArray=sql_select("select b.id,a.buyer_name,a.style_ref_no,a.job_no,b.po_number,b.pub_shipment_date, b.file_no, b.grouping as ref_no, (a.total_set_qnty*b.po_quantity) as qty from wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$data[0] and b.id in($all_order_id) and b.status_active=1 and b.is_deleted=0 ");
	$job_array=array(); //$all_job_id='';
	foreach($poDataArray as $row)
	{
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['no']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['date']=$row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['qty']=$row[csf('qty')];
		$job_array[$row[csf('id')]]['file']=$row[csf('file_no')];
		$job_array[$row[csf('id')]]['ref']=$row[csf('ref_no')];
	}
	?>
	<div style="width:920px;">
		<table width="900" cellspacing="0" align="right">
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
			<tr>
				<td width="125"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
				<td width="125"><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
				<td width="125"><strong>Challan No.:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
			</tr>
		</table>
		<table width="900" cellspacing="0" align="right" style="margin-top:5px;">
			<tr>
				<td width="450">
					<table width="100%" cellspacing="0" align="right">
						<tr>
							<td colspan="4" align="center" style="font-weight:bold; font-size:18px;"><u>From Order</u></td>
						</tr>
						<tr>
							<td width="100">Order No:</td>
							<td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['no']; ?></td>
							<td width="100">Quantity:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['qty']; ?></td>
						</tr>
						<tr>
							<td>Buyer:</td>
							<td>&nbsp;<? echo $buyer_library[$job_array[$dataArray[0][csf('from_order_id')]]['buyer']]; ?></td>
							<td>Job No:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['job']; ?></td>
						</tr>
						<tr>
							<td>File No:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['file']; ?></td>
							<td>Ref. No:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['ref']; ?></td>
						</tr>
						<tr>
							<td>Style Ref:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['style']; ?></td>
							<td>Ship. Date:</td>
							<td>&nbsp;<? echo change_date_format($job_array[$dataArray[0][csf('from_order_id')]]['date']); ?></td>
						</tr>
					</table>
				</td>
				<td>
					<table width="100%" cellspacing="0" align="right">
						<tr>
							<td colspan="4" align="center" style="font-weight:bold; font-size:18px;"><u>To Order</u></td>
						</tr>
						<tr>
							<td width="100">Order No:</td>
							<td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['no']; ?></td>
							<td width="100">Quantity:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['qty']; ?></td>
						</tr>
						<tr>
							<td>Buyer:</td>
							<td>&nbsp;<? echo $buyer_library[$job_array[$dataArray[0][csf('to_order_id')]]['buyer']]; ?></td>
							<td>Job No:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['job']; ?></td>
						</tr>
						<tr>
							<td>File No:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['file']; ?></td>
							<td>Ref. No:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['ref']; ?></td>
						</tr>
						<tr>
							<td>Style Ref:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['style']; ?></td>
							<td>Ship. Date:</td>
							<td>&nbsp;<? echo change_date_format($job_array[$dataArray[0][csf('to_order_id')]]['date']); ?></td>
						</tr>
						<tr>
							<td colspan="4">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="50">SL</th>
					<th width="55">Product Id</th>
					<th width="100">Barcode No</th>
					<th width="60">Roll No</th>
					<th width="100">Batch No</th>
					<th width="110">Color</th>
					<th width="230">Fabric Description</th>
					<th width="70">GSM</th>
					<th>Transfered Qnty</th>
				</thead>
				<tbody>
					<?
					$sql_dtls="select a.from_prod_id, a.transfer_qnty, a.uom, a.y_count, a.brand_id, a.yarn_lot, a.to_rack, a.to_shelf, a.stitch_length, a.batch_id, a.gsm, b.barcode_no, b.roll_no
					from inv_item_transfer_dtls a, pro_roll_details b
					where a.id=b.dtls_id and b.entry_form=230 and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.barcode_no";

					$sql_result= sql_select($sql_dtls);
					$i=1;
					foreach($sql_result as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$transfer_qnty=$row[csf('transfer_qnty')];
						$transfer_qnty_sum += $transfer_qnty;
						$prod_name_dtls=$constructtion_arr[$product_arr[$row[csf('from_prod_id')]]["detarmination_id"]]." ".$composition_arr[$product_arr[$row[csf('from_prod_id')]]["detarmination_id"]]." ".$product_arr[$row[csf('from_prod_id')]]["gsm"]." ".$product_arr[$row[csf('from_prod_id')]]["dia_width"];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td align="center"><? echo $row[csf("from_prod_id")]; ?></td>
							<td align="center"><? echo $row[csf("barcode_no")]; ?></td>
							<td align="center"><? echo $row[csf("roll_no")]; ?></td>
							<td><? echo $batch_arr[$row[csf("batch_id")]]["batch_no"]; ?></td>
							<td><? echo $color_library[$batch_arr[$row[csf("batch_id")]]["color_id"]]; ?></td>
							<td><? echo $prod_name_dtls; ?></td>
							<td align="center"><? echo $row[csf("gsm")]; ?></td>
							<td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="8" align="right"><strong>Total </strong></td>
						<td align="right"><?php echo $transfer_qnty_sum; ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<?
	exit();
}
?>

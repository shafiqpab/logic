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

	echo create_drop_down( "cbo_store_name_to", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.company_id=$company and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "load_drop_down( 'requires/grey_fabric_transfer_v2_controller',this.value+'_'+$data[0], 'load_drop_down_floor_to', 'floor_td_to' );" );
	exit();
}

if ($action=="load_drop_down_floor_to")
{
	//print_r($data);die;
	$data=explode("_",$data);
	$store=$data[0];
	$company=$data[1];
	echo create_drop_down( "cbo_floor_to", 150, "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store' and a.company_id='$company' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name", "floor_id,floor_room_rack_name", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/grey_fabric_transfer_v2_controller',this.value+'_'+$company+'_'+$store, 'load_drop_down_room_to', 'room_td_to' );");
	exit();
}

if ($action=="load_drop_down_room_to")
{
	$data=explode("_",$data);
	$floorId=$data[0];
	$company=$data[1];
	$store=$data[2];
	echo create_drop_down( "cbo_room_to", 150, "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.store_id=$store and a.company_id='$company' and b.floor_id=$floorId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "-- Select Room --", 0, "load_drop_down( 'requires/grey_fabric_transfer_v2_controller',this.value+'_'+$company+'_'+$store+'_'+$floorId, 'load_drop_down_rack_to', 'rack_td_to' );");
	exit();
}

if ($action=="load_drop_down_rack_to")
{
	$data=explode("_",$data);
	$roomId=$data[0];
	$company=$data[1];
	$store=$data[2];
	$floorId=$data[3];
	echo create_drop_down( "txt_rack_to", 150, "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and b.store_id=$store and a.company_id='$company' and b.floor_id=$floorId and room_id=$roomId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "-- Select Rack --", 0, "load_drop_down( 'requires/grey_fabric_transfer_v2_controller',this.value+'_'+$company+'_'+$store+'_'+$floorId+'_'+$roomId, 'load_drop_down_shelf_to', 'shelf_td_to' );");
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
	echo create_drop_down( "txt_shelf_to", 150, "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and b.store_id=$store and a.company_id='$company' and b.floor_id=$floorId and room_id=$roomId and rack_id=$rackId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "-- Select Shelf --", 0, "");
	exit();
}

if ($action=="requ_variable_settings")
{
	extract($_REQUEST);
	$requisition_type=return_field_value("user_given_code_status","variable_settings_inventory","company_name='$cbo_company_id' and variable_list=30 and item_category_id='$item_category' and status_active=1 and is_deleted=0");
	$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=13 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	echo $requisition_type.'**'.$variable_inventory;
	exit();
}
$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

if ($action=="order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo "10***".$cbo_company_id_to;die;
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
	<div align="center" style="width:880px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:870px;margin-left:10px">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
	                <thead>
	                    <th>Buyer Name</th>
	                    <th>Order No</th>
	                    <th>Internal Ref. No</th>
	                    <th width="230">Shipment Date Range</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="order_id" id="order_id" class="text_boxes" value="">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
								echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
							?>
	                    </td>
	                    <td>
	                        <input type="text" style="width:130px;" class="text_boxes" name="txt_order_no" id="txt_order_no" />
	                    </td>
	                     <td>
	                        <input type="text" style="width:130px;" class="text_boxes" name="txt_int_ref_no" id="txt_int_ref_no" />
	                    </td>
	                    <td>
	                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
	                    </td>
	                    <td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+<? echo $cbo_company_id_to;?>+'_'+document.getElementById('txt_int_ref_no').value, 'create_po_search_list_view', 'search_div', 'grey_fabric_transfer_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$search_string="%".trim($data[1])."%";

	$company_id=$data[2];
	$cbo_company_id_to=$data[6];
	$txt_int_ref_no=$data[7];

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
	$arr=array(2=>$company_arr,3=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";	
	if($type=="from") $company_cond=" and a.company_name=$company_id "; else $company_cond=" and a.company_name=$cbo_company_id_to";	
	if($txt_int_ref_no!="") $int_ref_no_cond=" and b.grouping like" ."'%".trim($txt_int_ref_no)."%'"; else $int_ref_no_cond="";	

	$sql= "select a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $company_cond $shipment_date $int_ref_no_cond order by b.id, b.pub_shipment_date";  
	 
	echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date", "70,60,70,80,120,90,110,90,80","850","200",0, $sql , "js_set_value", "id", "", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,1,3');
	
	exit();
}

if($action=='populate_data_from_order')
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$which_order=$data[1];
	$trans_criteria=$data[2];

	$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
	foreach ($data_array as $row)
	{ 
		$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
		}
		
		echo "document.getElementById('txt_".$which_order."_order_id').value 			= '".$po_id."';\n";
		echo "document.getElementById('txt_".$which_order."_order_no').value 			= '".$row[csf("po_number")]."';\n";
		echo "document.getElementById('txt_".$which_order."_po_qnty').value 			= '".$row[csf("po_quantity")]."';\n";
		echo "document.getElementById('cbo_".$which_order."_buyer_name').value 			= '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_".$which_order."_style_ref').value 			= '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_job_no').value 				= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_gmts_item').value 			= '".$gmts_item."';\n";
		echo "document.getElementById('txt_".$which_order."_shipment_date').value 		= '".change_date_format($row[csf("shipment_date")])."';\n";

		if($trans_criteria==2) // Store to Store
		{
			$which_order="to";
			echo "document.getElementById('txt_".$which_order."_order_id').value 			= '".$po_id."';\n";
			echo "document.getElementById('txt_".$which_order."_order_no').value 			= '".$row[csf("po_number")]."';\n";
			echo "document.getElementById('txt_".$which_order."_po_qnty').value 			= '".$row[csf("po_quantity")]."';\n";
			echo "document.getElementById('cbo_".$which_order."_buyer_name').value 			= '".$row[csf("buyer_name")]."';\n";
			echo "document.getElementById('txt_".$which_order."_style_ref').value 			= '".$row[csf("style_ref_no")]."';\n";
			echo "document.getElementById('txt_".$which_order."_job_no').value 				= '".$row[csf("job_no")]."';\n";
			echo "document.getElementById('txt_".$which_order."_gmts_item').value 			= '".$gmts_item."';\n";
			echo "document.getElementById('txt_".$which_order."_shipment_date').value 		= '".change_date_format($row[csf("shipment_date")])."';\n";
		}
		exit();
	}
}

if($action=="load_drop_down_item_desc")
{
	$item_description=array();
	$sql="select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id=$data and b.entry_form in(2,22,13) and b.trans_type in(1,5)  and b.status_active=1 and b.is_deleted=0";
	$dataArray=sql_select($sql);	
	foreach($dataArray as $row)
	{
		$item_description[$row[csf('id')]]=$row[csf('product_name_details')];
	}
	echo create_drop_down( "cbo_item_desc", 368, $item_description,'', 1, "--Select Item Description--",'0','','1');  
	exit();
}

if($action=="show_dtls_list_view")
{
	// $data = explode("_",$data);
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');	

	$sql="SELECT a.id as prod_id, a.product_name_details, a.lot,  a.avg_rate_per_unit, d.store_id as to_store, (case when d.entry_form =2 and d.receive_basis=2  then c.store_floor when d.entry_form =22  then c.floor_id else 0 end) as to_floor, c.room as to_room, c.rack as to_rack, c.self as to_shelf, b.quantity as qnty, a.brand as brand, c.yarn_count as yarn_count, c.brand_id as brand_id, c.yarn_lot as yarn_lot, c.stitch_length as stitch_length, 
	(case when d.entry_form =2 and d.receive_basis=2  then d.booking_id else 0 end) as booking_id 
	from product_details_master a, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_receive_master d 
	where a.id=b.prod_id and b.dtls_id=c.id and c.mst_id=d.id and a.item_category_id=13 and b.entry_form in(2,22) and d.entry_form in(2,22) and b.po_breakdown_id=$data and a.status_active=1 
	and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.receive_basis<>9 and c.trans_id>0 
	UNION ALL 
	SELECT a.id as prod_id, a.product_name_details, a.lot,  a.avg_rate_per_unit, d.store_id as to_store, c.floor_id as to_floor, c.room as to_room, c.rack as to_rack, c.self as to_shelf, b.quantity as qnty,  
	a.brand as brand, c.yarn_count as yarn_count, c.brand_id as brand_id, c.yarn_lot as yarn_lot, c.stitch_length as stitch_length, 
	(case when  e.receive_basis=2  then e.booking_id else 0 end) as booking_id
	from product_details_master a, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_receive_master d, inv_receive_master e 
	where a.id=b.prod_id and b.dtls_id=c.id and c.mst_id=d.id and d.booking_id=e.id and e.entry_form=2 and a.item_category_id=13 and b.entry_form in(22) and d.entry_form in(22) and b.po_breakdown_id=$data 
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.receive_basis=9 and c.trans_id>0 
	UNION ALL 
	SELECT a.id as prod_id, a.product_name_details, a.lot, a.avg_rate_per_unit, c.to_store as to_store, c.to_floor_id as to_floor_id, c.to_room as to_room, c.to_rack as to_rack, c.to_shelf as to_shelf, b.quantity as qnty, a.brand as brand, c.y_count as yarn_count, c.brand_id as brand_id, c.yarn_lot  as yarn_lot, 
	c.stitch_length as stitch_length, 
	(case when  c.from_program !=0  then c.from_program else 0 end) as booking_id
	from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c, inv_item_transfer_mst d 
	where a.id=b.prod_id and b.dtls_id=c.id and c.mst_id=d.id and a.item_category_id=13 and b.entry_form in(13) and d.entry_form in(13) and b.po_breakdown_id=$data and b.trans_type=5 and a.status_active=1 
	and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	
	
	//echo $sql;
	$data_array=sql_select($sql);
	$prodidArr=array();
	foreach($data_array as $row )
	{
		$to_floor 		= ($row[csf('to_floor')]=="")?0:$row[csf('to_floor')];
		$to_room 		= ($row[csf('to_room')]=="")?0:$row[csf('to_room')];
		$to_rack 		= ($row[csf('to_rack')]=="")?0:$row[csf('to_rack')];
		$to_shelf 		= ($row[csf('to_shelf')]=="")?0:$row[csf('to_shelf')];

		$array_group=$row[csf('prod_id')].'**'.$row[csf('to_store')].'**'.$to_floor.'**'.$to_room.'**'.$to_rack.'**'.$to_shelf.'**'.$row[csf('booking_id')];

		$all_data_arr[$array_group]['qnty'] += $row[csf('qnty')];
		$all_data_arr[$array_group]['product_name_details'] = $row[csf('product_name_details')];
		$all_data_arr[$array_group]['lot'] = $row[csf('lot')];
		$all_data_arr[$array_group]['avg_rate_per_unit'] = $row[csf('avg_rate_per_unit')];
		$all_data_arr[$array_group]['brand_id'] = $row[csf('brand_id')];
		$all_data_arr[$array_group]['yarn_count'] = $row[csf('yarn_count')];
		$all_data_arr[$array_group]['yarn_lot'] = $row[csf('yarn_lot')];
		$all_data_arr[$array_group]['stitch_length'] = $row[csf('stitch_length')];
		$all_data_arr[$array_group]['prod_id'] = $row[csf('prod_id')];
		$all_data_arr[$array_group]['to_store'] = $row[csf('to_store')];
		$all_data_arr[$array_group]['to_floor'] = $row[csf('to_floor')];
		$all_data_arr[$array_group]['to_room'] = $row[csf('to_room')];
		$all_data_arr[$array_group]['to_rack'] = $row[csf('to_rack')];
		$all_data_arr[$array_group]['to_shelf'] = $row[csf('to_shelf')];
		$all_data_arr[$array_group]['booking_id'] = $row[csf('booking_id')];

		//x.prod_id, x.product_name_details, x.lot, x.avg_rate_per_unit, x.brand, x.yarn_count,x.brand_id, x.yarn_lot, x.to_store, x.to_floor, x.to_room, x.to_rack, x.to_shelf, x.stitch_length,  x.booking_id
	}

	
	$issData=" SELECT a.prod_id, a.store_id, a.floor_id, a.rack, a.room,  a.self, d.po_breakdown_id, sum(d.quantity) as issue_qnty, c.program_no  
	from inv_transaction a, inv_grey_fabric_issue_dtls c, order_wise_pro_details d 
	where c.trans_id=a.id and a.id=d.trans_id and c.id=d.dtls_id and c.trans_id>0 and d.trans_id>0 and d.entry_form=16 and d.po_breakdown_id in($data) and c.status_active=1 and d.status_active=1  and a.transaction_type=2
	GROUP BY a.prod_id, a.store_id, a.floor_id, a.rack, a.room, a.self, d.po_breakdown_id, c.program_no";
	// echo $issData;die; // and c.program_no in ($program_no)
	$issue_result=sql_select($issData);
	$floor_id=$room=$rack=$self=$program_no="";$issue_qty_array=array();
	foreach($issue_result as $row)
	{
		$store_id 	= ($row[csf('store_id')]=="")?0:$row[csf('store_id')];
		$floor_id 	= ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room 		= ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack 		= ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self 		= ($row[csf('self')]=="")?0:$row[csf('self')];
		$program_no = ($row[csf('program_no')]=="")?0:$row[csf('program_no')];
		$issue_qty_array[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$program_no][$store_id][$floor_id][$room][$rack][$self]+=$row[csf('issue_qnty')];
	}
	/*echo "<pre>";
	print_r($issue_qty_array);die;*/

	$rcv_rtn_sql=" SELECT c.prod_id, c.store_id, c.floor_id,c.room, c.rack, c.self, d.po_breakdown_id, sum(d.quantity) as return_qnty 
	from inv_issue_master b, inv_transaction c, order_wise_pro_details d
	where b.id=c.mst_id and c.id=d.trans_id and c.transaction_type=3 and b.entry_form=45 and d.entry_form=45 and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.po_breakdown_id in($data) 
	group by c.prod_id, c.store_id, c.floor_id,c.room, c.rack, c.self, d.po_breakdown_id";
	// echo $rcv_rtn_sql;die;
	$rcv_rtn_result=sql_select($rcv_rtn_sql);
	$floor_id=$room=$rack=$self="";$recvRt_qty_array=array();
	foreach($rcv_rtn_result as $row)
	{
		$store_id 	= ($row[csf('store_id')]=="")?0:$row[csf('store_id')];
		$floor_id 	= ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room 		= ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack 		= ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self 		= ($row[csf('self')]=="")?0:$row[csf('self')];
		$recvRt_qty_array[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$store_id][$floor_id][$room][$rack][$self]+=$row[csf('return_qnty')];
	}
	/*echo "<pre>";
	print_r($recvRt_qty_array);die;*/

	$issue_rtn_sql=" SELECT c.prod_id, c.store_id, c.floor_id,c.room, c.rack, c.self, (d.quantity) as issrtnqnty, d.po_breakdown_id, (case when b.receive_basis=3 then b.booking_id when b.receive_basis=1 then 0 else 0 end) as program_no 
	from inv_receive_master b, inv_transaction c, order_wise_pro_details d 
	where b.id=c.mst_id and c.id=d.trans_id and c.transaction_type=4 and b.entry_form=51 and d.entry_form=51 and d.po_breakdown_id in($data) and b.status_active=1 and c.status_active=1 and d.status_active=1";
	// echo $issue_rtn_sql;
	$issue_rtn_result=sql_select($issue_rtn_sql);
	$floor_id=$room=$rack=$self=$program_no="";$issRt_qty_array=array();
	foreach($issue_rtn_result as $row)
	{
		$store_id 	= ($row[csf('store_id')]=="")?0:$row[csf('store_id')];
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$program_no = ($row[csf('program_no')]=="")?0:$row[csf('program_no')];
		$issRt_qty_array[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$program_no][$store_id][$floor_id][$room][$rack][$self] +=$row[csf('issrtnqnty')];
	}
	/*echo "<pre>";
	print_r($issRt_qty_array);die;*/

	$trans_out_sql="SELECT a.prod_id, a.store_id, a.floor_id,a.room, a.rack, a.self, d.trans_type, d.po_breakdown_id, sum(d.quantity) as trans_out_qnty, c.from_program 
	from inv_transaction a, inv_item_transfer_mst b, inv_item_transfer_dtls c, order_wise_pro_details d
	where b.id=c.mst_id and c.id=d.dtls_id and a.id=d.trans_id and a.id=c.trans_id and d.trans_id=c.trans_id and b.entry_form in(13,81) and d.entry_form in(13,81) and b.status_active=1 and c.status_active=1 and d.status_active=1  and d.po_breakdown_id in($data) and d.trans_type=6
	group by a.prod_id, a.store_id, a.floor_id,a.room, a.rack, a.self, d.trans_type, d.po_breakdown_id, c.from_program";
	//echo $trans_out_sql; and c.from_program in($program_no)
	$transfer_result=sql_select($trans_out_sql);
	$floor_id=$room=$rack=$self="";$trans_out_qnty_array=array();
	foreach($transfer_result as $row)
	{
		$store_id 	= ($row[csf('store_id')]=="")?0:$row[csf('store_id')];
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$program_no = ($row[csf('from_program')]=="")?0:$row[csf('from_program')];
		$trans_out_qnty_array[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$program_no][$store_id][$floor_id][$room][$rack][$self] +=$row[csf('trans_out_qnty')];
	}
	/*echo "<pre>";
	print_r($trans_out_qnty_array);*///die;
	$floorRoomRackShelf_array=return_library_array( "SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");
	$store_lib_array=return_library_array( "SELECT a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type=13 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name", "id", "store_name");

	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="550">
        <thead>
            <th>Fabric Description</th>
            <th width="70">Book./ Prog. No</th>
            <th width="40">Y/count</th>
            <th width="40">Y/Brand</th>
            <th width="40">Y/Lot</th>
            <th width="45">Stitch Length</th>
            <th width="40">Store</th>
            <th width="40">Floor</th>
            <th width="75">Balance Qty</th>
        </thead>
        <tbody>
            <? 
            $i=1;
            foreach($all_data_arr as $data_str=>$row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
				$ycount='';
				$count_id=explode(',',$row['yarn_count']);
				foreach($count_id as $count)
				{
					if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
				}
				$knit_palan_no=$row['booking_id'];
				
				if($row['to_store'] =="") $to_store_id = "0"; else $to_store_id = $row['to_store'];
				if($row['to_floor'] =="") $to_floor_id = "0"; else $to_floor_id = $row['to_floor'];
				if($row['to_room'] =="") $to_room_id = "0"; else $to_room_id = $row['to_room'];
				if($row['to_rack'] =="") $to_rack_id = "0"; else $to_rack_id = $row['to_rack'];
				if($row['to_shelf'] =="") $to_shelf_id = "0"; else $to_shelf_id = $row['to_shelf'];
				
				$issRt_qty = $issRt_qty_array[$row['prod_id']][$data][$row['booking_id']][$to_store_id][$to_floor_id][$to_room_id][$to_rack_id][$to_shelf_id];

				$recvRt_qty = $recvRt_qty_array[$row['prod_id']][$data][$to_store_id][$to_floor_id][$to_room_id][$to_rack_id][$to_shelf_id];

				$issue_qty = $issue_qty_array[$row['prod_id']][$data][$row['booking_id']][$to_store_id][$to_floor_id][$to_room_id][$to_rack_id][$to_shelf_id];

				$trans_out_qnty = $trans_out_qnty_array[$row['prod_id']][$data][$row['booking_id']][$to_store_id][$to_floor_id][$to_room_id][$to_rack_id][$to_shelf_id];


				$stock = $row["qnty"] + $issRt_qty - ($recvRt_qty + $issue_qty + $trans_out_qnty);

				$title = "rcv+trans_in= ".$row["qnty"]." , iss_return= ".$issRt_qty." , rcv_return=". $recvRt_qty." , trans_out = ".$trans_out_qnty." , issue = ".$issue_qty;
				$test = "Product ID= ".$row['prod_id'].", PO= ".$data.", Program= ".$row['booking_id'].", Store = ".$to_store_id.", Floor = ".$to_floor_id.", Room = ".$to_room_id.", Rack = ".$to_rack_id.", Self = ".$to_shelf_id;
				$stock = number_format($stock,2,'.','');
				if($stock>0)
				{	
					$brand_id = $row['brand_id'];
					$brand_idArray = explode(',', $brand_id);
					$brand_name="";
					foreach($brand_idArray as $value)
					{
					    if($brand_name=="") $brand_name=$brand_arr[$value]; 
					    else $brand_name.=",".$brand_arr[$value];
					}
					//echo $brand_name;
             	?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row['prod_id']."**".$ycount."**".$row['yarn_count']."**".$brand_name."**".$row['brand_id']."**".$row['yarn_lot']."**".$row['to_rack']."**".$row['to_shelf']."**".$knit_palan_no."**".$row['stitch_length']."**".$row['avg_rate_per_unit']."**".$row['to_store']."**".$row['to_floor']."**".$row['to_room']."**".$stock;?>")' style="cursor:pointer">
                    <td><p><? echo $row['product_name_details']; ?></p></td> 
                    <td><p><? echo $row['booking_id']; ?>&nbsp;</p></td>
                    <td><p><? echo $ycount; ?>&nbsp;</p></td>
                    <td><p><? echo $brand_name; ?>&nbsp;</p></td>
                    <td><p><? echo $row['yarn_lot']; ?>&nbsp;</p></td>
                    <td><p><? echo $row['stitch_length']; ?>&nbsp;</p></td>
                    <td><p><? echo $store_lib_array[$row['to_store']]; ?>&nbsp;</p></td>
                    <td title="<? echo $test;?>"><p><? echo $floorRoomRackShelf_array[$row['to_floor']]; ?>&nbsp;</p></td>
                    <td title="<? echo $title;?>"><p><? echo $stock; ?>&nbsp;</p></td>
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

if($action=="populate_data_about_order")
{
	$data=explode("**",$data);
	$order_id=$data[0];
	$prod_id=$data[1];
	$program_no=$data[2];
	$company_id=$data[3];
	$store_id=$data[4];
	$yet_issue=0;
	//echo $program_no."==".jahid;die;
	if($program_no!="")
	{
		$fabric_store_auto_update=return_field_value("auto_update","variable_settings_production","company_name =$company_id and variable_list=15 and item_category_id=13 and is_deleted=0 and status_active=1");
		
		//echo $fabric_store_auto_update."===jahid";
		if($fabric_store_auto_update==1)
		{
			$receive_sql="SELECT b.id, d.po_breakdown_id, d.quantity 
			from inv_receive_master b, pro_grey_prod_entry_dtls c, order_wise_pro_details d
			where b.id=c.mst_id and c.id=d.dtls_id and c.trans_id>0 and d.trans_id>0 and b.entry_form=2 and d.entry_form=2 and b.receive_basis=2 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.booking_id=$program_no and d.po_breakdown_id in($order_id)";
		}
		else
		{
			$receive_sql="SELECT b.id, d.po_breakdown_id, d.quantity 
			from inv_receive_master a, inv_receive_master b, pro_grey_prod_entry_dtls c, order_wise_pro_details d
			where a.id=b.booking_id and b.id=c.mst_id and c.id=d.dtls_id and c.trans_id>0 and d.trans_id>0 and a.entry_form=2 and b.entry_form=22 and d.entry_form=22 and b.receive_basis=9 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.booking_id=$program_no and d.po_breakdown_id in($order_id)";
		}
		//echo $receive_sql;die;
		$receive_result=sql_select($receive_sql);
		$all_rcv_id="";
		foreach($receive_result as $row)
		{
			if($rcv_chaeck[$row[csf('id')]]=="")
			{
				$rcv_chaeck[$row[csf('id')]]=$row[csf('id')];
				$all_rcv_id.=$row[csf('id')].",";
			}
			$yet_issue+=$row[csf('quantity')];
		}
		$all_rcv_id=chop($all_rcv_id,",");
		if($all_rcv_id!="")
		{
			$rcv_rtn_sql=" SELECT d.po_breakdown_id, d.quantity 
			from inv_issue_master b, inv_transaction c, order_wise_pro_details d
			where b.id=c.mst_id and c.id=d.trans_id and b.transaction_type=3 and b.entry_form=45 and d.entry_form=45 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.received_id in($all_rcv_id) and d.po_breakdown_id in($order_id) ";
			//echo $rcv_rtn_sql;die;
			$rcv_rtn_result=sql_select($rcv_rtn_sql);
			foreach($rcv_rtn_result as $row)
			{
				$yet_issue-=$row[csf('quantity')];
			}
		}
		
		$issue_sql=" SELECT d.po_breakdown_id, d.quantity  from inv_grey_fabric_issue_dtls c, order_wise_pro_details d 
		where c.id=d.dtls_id and c.trans_id>0 and d.trans_id>0 and d.entry_form=16 and c.program_no=$program_no and d.po_breakdown_id in($order_id) and c.status_active=1 and d.status_active=1 ";
		// echo $issue_sql;die;
		$issue_result=sql_select($issue_sql);
		foreach($issue_result as $row)
		{
			$yet_issue-=$row[csf('quantity')];
		}
		
		$issue_rtn_sql=" SELECT d.po_breakdown_id, d.quantity  
		from inv_receive_master b, inv_transaction c, order_wise_pro_details d 
		where b.id=c.mst_id and c.id=d.trans_id and b.transaction_type=4 and b.entry_form=51 and d.entry_form=51 and b.booking_id=$program_no and d.po_breakdown_id in($order_id) and b.status_active=1 and c.status_active=1 and d.status_active=1 ";
		// echo $issue_rtn_sql;die;
		$issue_rtn_result=sql_select($issue_rtn_sql);
		foreach($issue_rtn_result as $row)
		{
			$yet_issue+=$row[csf('quantity')];
		}
		
		$transfer_sql="SELECT d.trans_type, d.po_breakdown_id, d.quantity 
		from inv_item_transfer_mst b, inv_item_transfer_dtls c, order_wise_pro_details d
		where b.id=c.mst_id and c.id=d.dtls_id  and b.entry_form in(13,81) and d.entry_form in(13,81) and b.status_active=1 and c.status_active=1 and d.status_active=1 and c.from_program=$program_no and d.po_breakdown_id in($order_id)";
		
		// echo $transfer_sql;die;
		
		$transfer_result=sql_select($transfer_sql);
		foreach($transfer_result as $row)
		{
			if($row[csf('trans_type')]==5)
			{
				$yet_issue+=$row[csf('quantity')];
			}
			else
			{
				$yet_issue-=$row[csf('quantity')];
			}
			
		}
	}
	else
	{ 
		$sql=sql_select("SELECT 
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
	}
	

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
		    var receive_data = data.split("_");
		    //alert(receive_data[0]+"***"+receive_data[1]);
			$('#transfer_id').val(receive_data[0]);
			$('#to_company_id').val();
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:900px; margin: 0 auto;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:760px;margin-left:10px">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
	                <thead>
	                    <th>Search By</th>
	                    <th width="150" id="search_by_td_up">Please Enter Transfer ID</th>
	                    <th width="190">Date Range</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
	                        <input type="hidden" name="to_company_id" id="to_company_id" class="text_boxes" value="">
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
	                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	                    <td>
	                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
	                    </td>
	                    <td>
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_transfer_search_list_view', 'search_div', 'grey_fabric_transfer_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
	                </tr>
	            </table>	        	
				<div style="margin-top: 10px">
					<div style="margin-top:10px" id="search_div"></div> 
				</div>
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
	$transfer_criteria_id =$data[3];
	
	if($search_by==1)
		$search_field="transfer_system_id";	
	else
		$search_field="challan_no";

	if ($data[4]!="" &&  $data[5]!="") 
	{
		if($db_type==0)
		{
			$transfer_date = "and transfer_date between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$transfer_date = "and transfer_date between '".change_date_format($data[4],'','',1)."' and '".change_date_format($data[5],'','',1)."'";
		}
	}
	else 
		$transfer_date ="";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	if($db_type==0)
	{
		$sql="SELECT id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category, to_company 
		from inv_item_transfer_mst where item_category=13 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=$transfer_criteria_id $transfer_date and entry_form in(13) and status_active=1 and is_deleted=0 order by id";
	}
	else
	{
		$sql="SELECT id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category, to_company 
		from inv_item_transfer_mst where item_category=13 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=$transfer_criteria_id $transfer_date and entry_form in(13) and status_active=1 and is_deleted=0 order by id";
	}

	//echo $sql;die;
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category,7=>$company_arr);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category,To Company", "80,70,100,110,90,130,120","880","250",0, $sql, "js_set_value", "id,to_company", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category,to_company", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category,to_company", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("SELECT transfer_system_id,challan_no,transfer_criteria, company_id, transfer_date, item_category, from_order_id, to_order_id, to_company, transfer_requ_no, transfer_requ_id from inv_item_transfer_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		$transfer_criteria = $row[csf("transfer_criteria")];
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_requisition_no').value 			= '".$row[csf("transfer_requ_no")]."';\n";
		echo "document.getElementById('txt_requisition_id').value 			= '".$row[csf("transfer_requ_id")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "get_php_form_data('".$row[csf("from_order_id")]."**from**$transfer_criteria'".",'populate_data_from_order','requires/grey_fabric_transfer_v2_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."**to**$transfer_criteria'".",'populate_data_from_order','requires/grey_fabric_transfer_v2_controller');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		echo "$('#txt_requisition_no').attr('disabled','disabled');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		exit();
	}
}

if($action=="show_transfer_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13","id","product_name_details");
	
	$sql="SELECT id, from_prod_id, transfer_qnty, item_category, uom, to_rack as rack, to_shelf as shelf from inv_item_transfer_dtls where mst_id='$data' and status_active='1' and is_deleted='0' and active_dtls_id_in_transfer=1";
	$floorRoomRackShelf_array=return_library_array( "SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");

	
	$arr=array(0=>$item_category,1=>$product_arr,3=>$unit_of_measurement,4=>$floorRoomRackShelf_array,5=>$floorRoomRackShelf_array);
	 
	echo create_list_view("list_view", "Item Category,Item Description,Transfered Qnty,UOM, Rack, Shelf", "120,250,100,70,80","730","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "item_category,from_prod_id,0,uom,rack,shelf", $arr, "item_category,from_prod_id,transfer_qnty,uom,rack,shelf", "requires/grey_fabric_transfer_v2_controller",'','0,0,2,0,0,0');
	
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');	

	$data_array=sql_select("SELECT a.transfer_criteria,a.company_id,a.to_company, b.id, b.mst_id, b.from_store, b.to_store,b.floor_id,b.room,b.rack,b.shelf,b.to_floor_id,b.to_room,b.to_rack,b.to_shelf, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.roll, b.item_category, b.uom, b.y_count, b.yarn_lot, b.brand_id,b.from_program,b.to_program,b.stitch_length, a.transfer_requ_id, b.transfer_requ_dtls_id, b.from_order_id, b.trans_id, b.to_trans_id 
		from inv_item_transfer_mst a, inv_item_transfer_dtls b where b.id='$data' and a.id=b.mst_id");

	$sql="SELECT a.id as prod_id, a.product_name_details, a.lot,  a.avg_rate_per_unit, d.store_id as to_store, c.store_floor as to_floor, c.room as to_room, c.rack as to_rack, c.self as to_shelf, b.quantity as qnty, a.brand as brand, c.yarn_count as yarn_count, c.brand_id as brand_id, c.yarn_lot as yarn_lot, c.stitch_length as stitch_length, 
	(case when d.entry_form =2 and d.receive_basis=2  then d.booking_id else 0 end) as booking_id 
	from product_details_master a, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_receive_master d 
	where a.id=b.prod_id and b.dtls_id=c.id and c.mst_id=d.id and a.item_category_id=13 and b.entry_form in(2,22) and d.entry_form in(2,22) and b.po_breakdown_id=".$data_array[0][csf('from_order_id')]." and a.status_active=1 
	and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.receive_basis<>9 and c.trans_id>0 
	UNION ALL 
	SELECT a.id as prod_id, a.product_name_details, a.lot,  a.avg_rate_per_unit, d.store_id as to_store, c.floor_id as to_floor, c.room as to_room, c.rack as to_rack, c.self as to_shelf, b.quantity as qnty,  
	a.brand as brand, c.yarn_count as yarn_count, c.brand_id as brand_id, c.yarn_lot as yarn_lot, c.stitch_length as stitch_length, 
	(case when  e.receive_basis=2  then e.booking_id else 0 end) as booking_id
	from product_details_master a, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_receive_master d, inv_receive_master e 
	where a.id=b.prod_id and b.dtls_id=c.id and c.mst_id=d.id and d.booking_id=e.id and e.entry_form=2 and a.item_category_id=13 and b.entry_form in(22) and d.entry_form in(22) and b.po_breakdown_id=".$data_array[0][csf('from_order_id')]." 
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.receive_basis=9 and c.trans_id>0 
	UNION ALL 
	SELECT a.id as prod_id, a.product_name_details, a.lot, a.avg_rate_per_unit, c.to_store as to_store, c.to_floor_id as to_floor_id, c.to_room as to_room, c.to_rack as to_rack, c.to_shelf as to_shelf, b.quantity as qnty, a.brand as brand, c.y_count as yarn_count, c.brand_id as brand_id, c.yarn_lot  as yarn_lot, 
	c.stitch_length as stitch_length, 
	(case when  c.from_program !=0  then c.from_program else 0 end) as booking_id
	from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c, inv_item_transfer_mst d 
	where a.id=b.prod_id and b.dtls_id=c.id and c.mst_id=d.id and a.item_category_id=13 and b.entry_form in(13) and d.entry_form in(13) and b.po_breakdown_id=".$data_array[0][csf('from_order_id')]." and b.trans_type=5 and a.status_active=1 
	and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	
	// echo $sql;die;
	$result = sql_select($sql);
	$receive_qty_array=array();
	$to_store_id=$to_floor_id=$to_room_id=$to_rack_id=$seto_shelf_idlf="";
	foreach($result as $row )
	{
		if($row[csf('to_store')] =="") $to_store_id = "0"; else $to_store_id = $row[csf('to_store')];
		if($row[csf('to_floor')] =="") $to_floor_id = "0"; else $to_floor_id = $row[csf('to_floor')];
		if($row[csf('to_room')] =="") $to_room_id = "0"; else $to_room_id = $row[csf('to_room')];
		if($row[csf('to_rack')] =="") $to_rack_id = "0"; else $to_rack_id = $row[csf('to_rack')];
		if($row[csf('to_shelf')] =="") $to_shelf_id = "0"; else $to_shelf_id = $row[csf('to_shelf')];

		$receive_qty_array[$row[csf('prod_id')]][$data_array[0][csf('from_order_id')]][$row[csf('booking_id')]][$to_store_id][$to_floor_id][$to_room_id][$to_rack_id][$to_shelf_id]['qnty']+=$row[csf('qnty')];

		$receive_qty_array[$row[csf('prod_id')]][$data_array[0][csf('from_order_id')]][$row[csf('booking_id')]][$to_store_id][$to_floor_id][$to_room_id][$to_rack_id][$to_shelf_id]['brand_id'].=$row[csf('brand_id')].",";

	}
	/*echo "<pre>";
	print_r($receive_qty_array);die;*/

	$issData=" SELECT a.prod_id, a.store_id, a.floor_id, a.rack, a.room,  a.self, d.po_breakdown_id, sum(d.quantity) as issue_qnty, c.program_no  
	from inv_transaction a, inv_grey_fabric_issue_dtls c, order_wise_pro_details d 
	where c.trans_id=a.id and a.id=d.trans_id and c.id=d.dtls_id and c.trans_id>0 and d.trans_id>0 and d.entry_form=16  and d.po_breakdown_id =".$data_array[0][csf('from_order_id')]." and c.status_active=1 and d.status_active=1  and a.transaction_type=2
	GROUP BY a.prod_id, a.store_id, a.floor_id, a.rack, a.room, a.self, d.po_breakdown_id, c.program_no";
	// echo $issData;die;
	$issue_result=sql_select($issData);
	$floor_id=$room=$rack=$self=$program_no="";$issue_qty_array=array();
	foreach($issue_result as $row)
	{
		$store_id 	= ($row[csf('store_id')]=="")?0:$row[csf('store_id')];
		$floor_id 	= ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room 		= ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack 		= ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self 		= ($row[csf('self')]=="")?0:$row[csf('self')];
		$program_no = ($row[csf('program_no')]=="")?0:$row[csf('program_no')];
		$issue_qty_array[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$program_no][$store_id][$floor_id][$room][$rack][$self]+=$row[csf('issue_qnty')];
	}
	/*echo "<pre>";
	print_r($issue_qty_array);die;*/

	$rcv_rtn_sql=" SELECT c.prod_id, c.store_id, c.floor_id,c.room, c.rack, c.self, d.po_breakdown_id, sum(d.quantity) as return_qnty 
	from inv_issue_master b, inv_transaction c, order_wise_pro_details d
	where b.id=c.mst_id and c.id=d.trans_id and c.transaction_type=3 and b.entry_form=45 and d.entry_form=45 and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.po_breakdown_id =".$data_array[0][csf('from_order_id')]." 
	group by c.prod_id, c.store_id, c.floor_id,c.room, c.rack, c.self, d.po_breakdown_id";
	// echo $rcv_rtn_sql;die;
	$rcv_rtn_result=sql_select($rcv_rtn_sql);
	$floor_id=$room=$rack=$self="";$recvRt_qty_array=array();
	foreach($rcv_rtn_result as $row)
	{
		$store_id 	= ($row[csf('store_id')]=="")?0:$row[csf('store_id')];
		$floor_id 	= ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room 		= ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack 		= ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self 		= ($row[csf('self')]=="")?0:$row[csf('self')];
		$recvRt_qty_array[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$store_id][$floor_id][$room][$rack][$self]+=$row[csf('return_qnty')];
	}
	/*echo "<pre>";
	print_r($recvRt_qty_array);die;*/

	$issue_rtn_sql=" SELECT c.prod_id, c.store_id, c.floor_id,c.room, c.rack, c.self, (d.quantity) as issrtnqnty, d.po_breakdown_id, (case when b.receive_basis=3 then b.booking_id when b.receive_basis=1 then 0 else 0 end) as program_no
	from inv_receive_master b, inv_transaction c, order_wise_pro_details d 
	where b.id=c.mst_id and c.id=d.trans_id and c.transaction_type=4 and b.entry_form=51 and d.entry_form=51 and d.po_breakdown_id =".$data_array[0][csf('from_order_id')]." and b.status_active=1 and c.status_active=1 and d.status_active=1";
	// echo $issue_rtn_sql;
	$issue_rtn_result=sql_select($issue_rtn_sql);
	$floor_id=$room=$rack=$self=$program_no="";$issRt_qty_array=array();
	foreach($issue_rtn_result as $row)
	{
		$store_id 	= ($row[csf('store_id')]=="")?0:$row[csf('store_id')];
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$program_no = ($row[csf('program_no')]=="")?0:$row[csf('program_no')];
		$issRt_qty_array[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$program_no][$store_id][$floor_id][$room][$rack][$self] +=$row[csf('issrtnqnty')];
	}
	/*echo "<pre>";
	print_r($issRt_qty_array);die;*/

	$trans_out_sql="SELECT a.prod_id, a.store_id, a.floor_id,a.room, a.rack, a.self, d.trans_type, d.po_breakdown_id, sum(d.quantity) as trans_out_qnty, c.from_program 
	from inv_transaction a, inv_item_transfer_mst b, inv_item_transfer_dtls c, order_wise_pro_details d
	where b.id=c.mst_id and c.id=d.dtls_id and a.id=d.trans_id and a.id=c.trans_id and d.trans_id=c.trans_id and b.entry_form in(13,81) and d.entry_form in(13,81) and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.po_breakdown_id =".$data_array[0][csf('from_order_id')]." and d.trans_type=6
	group by a.prod_id, a.store_id, a.floor_id,a.room, a.rack, a.self, d.trans_type, d.po_breakdown_id, c.from_program";
	// echo $trans_out_sql;die;
	$transfer_result=sql_select($trans_out_sql);
	$floor_id=$room=$rack=$self="";$trans_out_qnty_array=array();
	foreach($transfer_result as $row)
	{
		$store_id 	= ($row[csf('store_id')]=="")?0:$row[csf('store_id')];
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$program_no = ($row[csf('from_program')]=="")?0:$row[csf('from_program')];
		$trans_out_qnty_array[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$program_no][$store_id][$floor_id][$room][$rack][$self] +=$row[csf('trans_out_qnty')];
	}
	/*echo "<pre>";
	print_r($trans_out_qnty_array);*///die;

	foreach ($data_array as $row) 
	{ 
		$ycount='';
		$count_id=explode(',',$row[csf('y_count')]);
		foreach($count_id as $count)
		{
			if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
		}
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store")]."';\n";	
		echo "document.getElementById('cbo_floor').value 					= '".$row[csf("floor_id")]."';\n";
		echo "document.getElementById('cbo_room').value 					= '".$row[csf("room")]."';\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack")]."';\n";		
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf")]."';\n";
		echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "$('#cbo_floor').attr('disabled','disabled');\n";
		echo "$('#cbo_room').attr('disabled','disabled');\n";
		echo "$('#txt_rack').attr('disabled','disabled');\n";
		echo "$('#txt_shelf').attr('disabled','disabled');\n";


		$to_company_id=$row[csf("to_company")];
		if($row[csf("to_store")]>0){			
			echo "load_drop_down('requires/grey_fabric_transfer_v2_controller','".$to_company_id."', 'load_drop_down_store_to', 'to_store_td' );\n";
			echo "document.getElementById('cbo_store_name_to').value 		= '".$row[csf("to_store")]."';\n";
		}
		if($row[csf("to_floor_id")]>0){		
			$store_com=$row[csf("to_store")]."_".$row[csf("to_company")];	
			echo "load_drop_down('requires/grey_fabric_transfer_v2_controller','".$store_com."', 'load_drop_down_floor_to', 'floor_td_to' );\n";
			echo "document.getElementById('cbo_floor_to').value 		= '".$row[csf("to_floor_id")]."';\n";
		}
		if($row[csf("to_room")]>0){		
			$floor_com_store=$row[csf("to_floor_id")]."_".$row[csf("to_company")]."_".$row[csf("to_store")];	
			echo "load_drop_down('requires/grey_fabric_transfer_v2_controller','".$floor_com_store."', 'load_drop_down_room_to', 'room_td_to' );\n";
			echo "document.getElementById('cbo_room_to').value 		= '".$row[csf("to_room")]."';\n";
		}
		if($row[csf("to_rack")]>0){		
			$room_floor_com_store=$row[csf("to_room")]."_".$row[csf("to_company")]."_".$row[csf("to_store")]."_".$row[csf("to_floor_id")];
			echo "load_drop_down('requires/grey_fabric_transfer_v2_controller','".$room_floor_com_store."', 'load_drop_down_rack_to', 'rack_td_to' );\n";
			echo "document.getElementById('txt_rack_to').value 		= '".$row[csf("to_rack")]."';\n";
		}
		if($row[csf("to_shelf")]>0){		
			$rack_room_floor_com_store=$row[csf("to_rack")]."_".$row[csf("to_company")]."_".$row[csf("to_store")]."_".$row[csf("to_floor_id")]."_".$row[csf("to_room")];
			echo "load_drop_down('requires/grey_fabric_transfer_v2_controller','".$rack_room_floor_com_store."', 'load_drop_down_shelf_to', 'shelf_td_to' );\n";
			echo "document.getElementById('txt_shelf_to').value 		= '".$row[csf("to_shelf")]."';\n";
		}

		if ($row[csf("transfer_requ_id")]!="") 
		{			
			echo "$('#cbo_store_name_to').attr('disabled','disabled');\n";
			echo "$('#cbo_floor_to').attr('disabled','disabled');\n";
			echo "$('#cbo_room_to').attr('disabled','disabled');\n";
			echo "$('#txt_rack_to').attr('disabled','disabled');\n";
			echo "$('#txt_shelf_to').attr('disabled','disabled');\n";
			echo "$('#txt_transfer_qnty').prop('readonly', true);\n";
			echo "$('#txt_roll').prop('readonly', true);\n";
			echo "$('#txt_to_prog').prop('readonly', true);\n";
		}
	
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('hidden_requ_dtls_id').value 			= '".$row[csf("transfer_requ_dtls_id")]."';\n";
		echo "document.getElementById('cbo_item_desc').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_roll').value 					= '".$row[csf("roll")]."';\n";
		echo "document.getElementById('txt_ycount').value 					= '".$ycount."';\n";
		echo "document.getElementById('hid_ycount').value 					= '".$row[csf("y_count")]."';\n";
		
		echo "document.getElementById('hid_ybrand').value 					= '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('txt_ylot').value 					= '".$row[csf("yarn_lot")]."';\n";
		echo "document.getElementById('txt_rack_to').value 					= '".$row[csf("to_rack")]."';\n";
		echo "document.getElementById('txt_shelf_to').value 				= '".$row[csf("to_shelf")]."';\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack")]."';\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf")]."';\n";
		echo "document.getElementById('txt_form_prog').value 				= '".$row[csf("from_program")]."';\n";
		echo "document.getElementById('txt_to_prog').value 					= '".$row[csf("to_program")]."';\n";
		echo "document.getElementById('stitch_length').value 				= '".$row[csf("stitch_length")]."';\n";
		echo "document.getElementById('hide_trans_qty').value 				= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_transfer_value').value 			= '".$row[csf("transfer_value")]."';\n";
		//echo "populate_stock();\n";

		//===================================================
		if($row[csf('from_store')] =="") $store_id = "0"; else $store_id = $row[csf('from_store')];
		if($row[csf('floor_id')] =="") $floor_id = "0"; else $floor_id = $row[csf('floor_id')];
		if($row[csf('room')] =="") $room_id = "0"; else $room_id = $row[csf('room')];
		if($row[csf('rack')] =="") $rack_id = "0"; else $rack_id = $row[csf('rack')];
		if($row[csf('shelf')] =="") $shelf_id = "0"; else $shelf_id = $row[csf('shelf')];

		//echo $row[csf('prod_id')].'='.$row[csf("order_id")].'='.$row[csf('program')].'='.$store_id.'='.$floor_id.'='.$room_id.'='.$rack_id.'='.$shelf_id;die;

		$receive_qty = $receive_qty_array[$row[csf('from_prod_id')]][$row[csf("from_order_id")]][$row[csf('from_program')]][$store_id][$floor_id][$room_id][$rack_id][$shelf_id]['qnty'];
		$brand_id = $receive_qty_array[$row[csf('from_prod_id')]][$row[csf("from_order_id")]][$row[csf('from_program')]][$store_id][$floor_id][$room_id][$rack_id][$shelf_id]['brand_id'];

		$brand_idArray = array_unique( explode(',', chop($brand_id,',')));
		$brand_name="";
		foreach($brand_idArray as $value)
		{
		    if($brand_name=="") $brand_name=$brand_arr[$value]; 
		    else $brand_name.=",".$brand_arr[$value];
		}

		$issRt_qty = $issRt_qty_array[$row[csf('from_prod_id')]][$row[csf("from_order_id")]][$row[csf('from_program')]][$store_id][$floor_id][$room_id][$rack_id][$shelf_id];

		$recvRt_qty = $recvRt_qty_array[$row[csf('from_prod_id')]][$row[csf("from_order_id")]][$store_id][$floor_id][$room_id][$rack_id][$shelf_id];

		$issue_qty = $issue_qty_array[$row[csf('from_prod_id')]][$row[csf("from_order_id")]][$row[csf('from_program')]][$store_id][$floor_id][$room_id][$rack_id][$shelf_id];

		$trans_out_qnty = $trans_out_qnty_array[$row[csf('from_prod_id')]][$row[csf("from_order_id")]][$row[csf('from_program')]][$store_id][$floor_id][$room_id][$rack_id][$shelf_id];
		//echo $receive_qty.'+'.$issRt_qty.'-'.($recvRt_qty.'+'.$issue_qty.'+'.$trans_out_qnty).'+'.$row[csf("transfer_qnty")];
		$stockQty = $receive_qty + $issRt_qty - ($recvRt_qty + $issue_qty + $trans_out_qnty) + $row[csf("transfer_qnty")];
		echo "document.getElementById('txt_stock').value 					= '".$stockQty."';\n";
		echo "document.getElementById('txt_ybrand').value 					= '".$brand_name."';\n";
		//===================================================

		/*
			|
			| N.B. These bunch of code are one of the worst bugs of this decade. Totally disappointing and frustrating...
			|

			//$sql_trans=sql_select("select id as trans_id, transaction_type from inv_transaction where mst_id=".$row[csf('mst_id')]." and item_category=13 and transaction_type in(5,6) order by id asc");
			//echo "document.getElementById('update_trans_issue_id').value 	= '".$sql_trans[0][csf("trans_id")]."';\n";
			//echo "document.getElementById('update_trans_recv_id').value 	= '".$sql_trans[1][csf("trans_id")]."';\n";

		*/

		
		echo "document.getElementById('update_trans_issue_id').value 	= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 	= '".$row[csf("to_trans_id")]."';\n";


		echo "document.getElementById('previous_from_prod_id').value 	= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_to_prod_id').value 		= '".$row[csf("to_prod_id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		exit();
	}
}

// ====================================Requisition Info Start=================================
if ($action=="itemTransfer_requisition_popup")
{
	echo load_html_head_contents("Order To Order Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data)
		{
		    var receive_data = data.split("_");
		    //alert(receive_data[0]+"***"+receive_data[1]);return;
			$('#transfer_id').val(receive_data[0]);
			$('#to_company_id').val();
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:900px; margin: 0 auto;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:760px;margin-left:10px">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
	                <thead>
	                    <th>Search By</th>
	                    <th width="150" id="search_by_td_up">Please Enter Requisition ID</th>
	                    <th width="190">Date Range</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
	                        <input type="hidden" name="to_company_id" id="to_company_id" class="text_boxes" value="">
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
	                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	                    <td>
	                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
	                    </td>
	                    <td>
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_transfer_requisition_search_list_view', 'search_div', 'grey_fabric_transfer_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
	                </tr>
	            </table>
			</fieldset>				        	
			<fieldset>
				 <legend>List View</legend>
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

if($action=='create_transfer_requisition_search_list_view')
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$transfer_criteria_id =$data[3];
	
	if($search_by==1)
		$search_field="transfer_system_id";	
	else
		$search_field="challan_no";

	if ($data[4]!="" &&  $data[5]!="") 
	{
		if($db_type==0)
		{
			$transfer_date = "and transfer_date between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$transfer_date = "and transfer_date between '".change_date_format($data[4],'','',1)."' and '".change_date_format($data[5],'','',1)."'";
		}
	}
	else 
		$transfer_date ="";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later

	$sql_trans_requ=sql_select("SELECT a.transfer_requ_id from inv_item_transfer_mst a, inv_item_transfer_dtls b
	where a.id=b.mst_id and a.item_category=13 and a.company_id=$company_id and a.transfer_criteria=$transfer_criteria_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $transfer_date and a.entry_form=13 and a.transfer_requ_id IS NOT NULL
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
	if ($requ_id!="")
	{
		$requ_id_cond= "and id not in($requ_id)";
	}
	
	if($db_type==0)
	{
		$sql="SELECT id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category, to_company 
		from inv_item_transfer_requ_mst where item_category=13 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=$transfer_criteria_id $transfer_date $requ_id_cond and entry_form in(353) and status_active=1 and is_deleted=0 order by id";
	}
	else
	{
		$sql="SELECT id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category, to_company 
		from inv_item_transfer_requ_mst where item_category=13 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=$transfer_criteria_id $transfer_date $requ_id_cond and entry_form in(353) and status_active=1 and is_deleted=0 order by id";
	}

	//echo $sql;die;
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category,7=>$company_arr);

	echo  create_list_view("tbl_list_search", "Requisition ID,Year,Challan No,Company,Requisition Date,Transfer Criteria,Item Category,To Company", "80,70,100,110,90,130,120","880","250",0, $sql, "js_set_value", "id,to_company", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category,to_company", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category,to_company", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_requisition_master')
{
	$data_array=sql_select("SELECT transfer_system_id,challan_no,transfer_criteria, company_id, transfer_date, item_category, from_order_id, to_order_id, to_company, ready_to_approve, is_approved from inv_item_transfer_requ_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		$transfer_criteria = $row[csf("transfer_criteria")];
		echo "document.getElementById('txt_requisition_id').value 			= '".$data."';\n";
		echo "document.getElementById('txt_requisition_no').value 			= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "get_php_form_data('".$row[csf("from_order_id")]."**from**$transfer_criteria'".",'populate_data_from_order','requires/grey_fabric_transfer_v2_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."**to**$transfer_criteria'".",'populate_data_from_order','requires/grey_fabric_transfer_v2_controller');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		exit();
	}
}

if($action=="show_dtls_list_requisition_view")
{
	$product_arr = return_library_array("SELECT id, product_name_details from product_details_master where item_category_id=13","id","product_name_details");
	
	$sql="SELECT id, from_prod_id, transfer_qnty, item_category, uom, to_rack as rack, to_shelf as shelf 
	from inv_item_transfer_requ_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	
	$arr=array(0=>$item_category,1=>$product_arr,3=>$unit_of_measurement);
	 
	echo create_list_view("list_view", "Item Category,Item Description,Requisition Qnty,UOM, Rack, Shelf", "120,250,100,70,80","730","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_requisition_details_form_data'", 0, "item_category,from_prod_id,0,uom,0,0", $arr, "item_category,from_prod_id,transfer_qnty,uom,rack,shelf", "requires/grey_fabric_transfer_v2_controller",'','0,0,2,0,0,0');
	
	exit();
}

if($action=='populate_transfer_requisition_details_form_data')
{
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');	

	$data_array=sql_select("SELECT a.id, a.transfer_criteria,a.company_id,a.to_company, b.id as req_dtls_id, b.mst_id, b.from_store, b.to_store,b.floor_id,b.room,b.rack,b.shelf,b.to_floor_id,b.to_room,b.to_rack,b.to_shelf, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.roll, b.item_category, b.uom, b.y_count, b.yarn_lot, b.brand_id,b.from_program,b.to_program, b.stitch_length, b.from_order_id
		from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b where b.id='$data' and a.id=b.mst_id");
	
	$requ_dtls_id=trim($data_array[0][csf('req_dtls_id')]);
	$trans_requ_dtls_id = return_field_value("transfer_requ_dtls_id", "inv_item_transfer_dtls", "transfer_requ_dtls_id=$requ_dtls_id", "transfer_requ_dtls_id");
	//echo $requ_dtls_id.'=='.$trans_requ_dtls_id;die;
	if ($requ_dtls_id==$trans_requ_dtls_id)
	{
		echo "alert('Already Transfered');";die;
	}

	$sql="SELECT a.id as prod_id, a.product_name_details, a.lot,  a.avg_rate_per_unit, d.store_id as to_store, c.store_floor as to_floor, c.room as to_room, c.rack as to_rack, c.self as to_shelf, b.quantity as qnty, a.brand as brand, c.yarn_count as yarn_count, c.brand_id as brand_id, c.yarn_lot as yarn_lot, c.stitch_length as stitch_length, 
	(case when d.entry_form =2 and d.receive_basis=2  then d.booking_id else 0 end) as booking_id 
	from product_details_master a, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_receive_master d 
	where a.id=b.prod_id and b.dtls_id=c.id and c.mst_id=d.id and a.item_category_id=13 and b.entry_form in(2,22) and d.entry_form in(2,22) and b.po_breakdown_id=".$data_array[0][csf('from_order_id')]." and a.status_active=1 
	and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.receive_basis<>9 and c.trans_id>0 
	UNION ALL 
	SELECT a.id as prod_id, a.product_name_details, a.lot,  a.avg_rate_per_unit, d.store_id as to_store, c.floor_id as to_floor, c.room as to_room, c.rack as to_rack, c.self as to_shelf, b.quantity as qnty,  
	a.brand as brand, c.yarn_count as yarn_count, c.brand_id as brand_id, c.yarn_lot as yarn_lot, c.stitch_length as stitch_length, 
	(case when  e.receive_basis=2  then e.booking_id else 0 end) as booking_id
	from product_details_master a, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_receive_master d, inv_receive_master e 
	where a.id=b.prod_id and b.dtls_id=c.id and c.mst_id=d.id and d.booking_id=e.id and e.entry_form=2 and a.item_category_id=13 and b.entry_form in(22) and d.entry_form in(22) and b.po_breakdown_id=".$data_array[0][csf('from_order_id')]." 
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.receive_basis=9 and c.trans_id>0 
	UNION ALL 
	SELECT a.id as prod_id, a.product_name_details, a.lot, a.avg_rate_per_unit, c.to_store as to_store, c.to_floor_id as to_floor_id, c.to_room as to_room, c.to_rack as to_rack, c.to_shelf as to_shelf, b.quantity as qnty, a.brand as brand, c.y_count as yarn_count, c.brand_id as brand_id, c.yarn_lot  as yarn_lot, 
	c.stitch_length as stitch_length, 
	(case when  c.from_program !=0  then c.from_program else 0 end) as booking_id
	from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c, inv_item_transfer_mst d 
	where a.id=b.prod_id and b.dtls_id=c.id and c.mst_id=d.id and a.item_category_id=13 and b.entry_form in(13) and d.entry_form in(13) and b.po_breakdown_id=".$data_array[0][csf('from_order_id')]." and b.trans_type=5 and a.status_active=1 
	and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	// echo $sql;die;
	$result = sql_select($sql);
	$receive_qty_array=array();
	$to_store_id=$to_floor_id=$to_room_id=$to_rack_id=$seto_shelf_idlf="";
	foreach($result as $row )
	{
		$prodidArr[$row[csf('prod_id')]]=$row[csf('prod_id')];

		if($row[csf('to_store')] =="") $to_store_id = "0"; else $to_store_id = $row[csf('to_store')];
		if($row[csf('to_floor')] =="") $to_floor_id = "0"; else $to_floor_id = $row[csf('to_floor')];
		if($row[csf('to_room')] =="") $to_room_id = "0"; else $to_room_id = $row[csf('to_room')];
		if($row[csf('to_rack')] =="") $to_rack_id = "0"; else $to_rack_id = $row[csf('to_rack')];
		if($row[csf('to_shelf')] =="") $to_shelf_id = "0"; else $to_shelf_id = $row[csf('to_shelf')];

		$receive_qty_array[$row[csf('prod_id')]][$data_array[0][csf('from_order_id')]][$row[csf('booking_id')]][$to_store_id][$to_floor_id][$to_room_id][$to_rack_id][$to_shelf_id]['qnty']+=$row[csf('qnty')];

		$receive_qty_array[$row[csf('prod_id')]][$data_array[0][csf('from_order_id')]][$row[csf('booking_id')]][$to_store_id][$to_floor_id][$to_room_id][$to_rack_id][$to_shelf_id]['brand_id'].=$row[csf('brand_id')].",";

	}
	/*echo "<pre>";
	print_r($receive_qty_array);die;*/

	$issData=" SELECT a.prod_id, a.store_id, a.floor_id, a.rack, a.room,  a.self, d.po_breakdown_id, sum(d.quantity) as issue_qnty, c.program_no  
	from inv_transaction a, inv_grey_fabric_issue_dtls c, order_wise_pro_details d 
	where c.trans_id=a.id and a.id=d.trans_id and c.id=d.dtls_id and c.trans_id>0 and d.trans_id>0 and d.entry_form=16  and d.po_breakdown_id =".$data_array[0][csf('from_order_id')]." and c.status_active=1 and d.status_active=1  and a.transaction_type=2
	GROUP BY a.prod_id, a.store_id, a.floor_id, a.rack, a.room, a.self, d.po_breakdown_id, c.program_no";
	// echo $issData;die;
	$issue_result=sql_select($issData);
	$floor_id=$room=$rack=$self=$program_no="";$issue_qty_array=array();
	foreach($issue_result as $row)
	{
		$store_id 	= ($row[csf('store_id')]=="")?0:$row[csf('store_id')];
		$floor_id 	= ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room 		= ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack 		= ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self 		= ($row[csf('self')]=="")?0:$row[csf('self')];
		$program_no = ($row[csf('program_no')]=="")?0:$row[csf('program_no')];
		$issue_qty_array[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$program_no][$store_id][$floor_id][$room][$rack][$self]+=$row[csf('issue_qnty')];
	}
	/*echo "<pre>";
	print_r($issue_qty_array);die;*/

	$rcv_rtn_sql=" SELECT c.prod_id, c.store_id, c.floor_id,c.room, c.rack, c.self, d.po_breakdown_id, sum(d.quantity) as return_qnty 
	from inv_issue_master b, inv_transaction c, order_wise_pro_details d
	where b.id=c.mst_id and c.id=d.trans_id and c.transaction_type=3 and b.entry_form=45 and d.entry_form=45 and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.po_breakdown_id =".$data_array[0][csf('from_order_id')]." 
	group by c.prod_id, c.store_id, c.floor_id,c.room, c.rack, c.self, d.po_breakdown_id";
	// echo $rcv_rtn_sql;die;
	$rcv_rtn_result=sql_select($rcv_rtn_sql);
	$floor_id=$room=$rack=$self="";$recvRt_qty_array=array();
	foreach($rcv_rtn_result as $row)
	{
		$store_id 	= ($row[csf('store_id')]=="")?0:$row[csf('store_id')];
		$floor_id 	= ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room 		= ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack 		= ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self 		= ($row[csf('self')]=="")?0:$row[csf('self')];
		$recvRt_qty_array[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$store_id][$floor_id][$room][$rack][$self]+=$row[csf('return_qnty')];
	}
	/*echo "<pre>";
	print_r($recvRt_qty_array);die;*/

	$issue_rtn_sql=" SELECT c.prod_id, c.store_id, c.floor_id,c.room, c.rack, c.self, (d.quantity) as issrtnqnty, d.po_breakdown_id, (case when b.receive_basis=3 then b.booking_id when b.receive_basis=1 then 0 else 0 end) as program_no
	from inv_receive_master b, inv_transaction c, order_wise_pro_details d 
	where b.id=c.mst_id and c.id=d.trans_id and c.transaction_type=4 and b.entry_form=51 and d.entry_form=51 and d.po_breakdown_id =".$data_array[0][csf('from_order_id')]." and b.status_active=1 and c.status_active=1 and d.status_active=1";
	// echo $issue_rtn_sql;
	$issue_rtn_result=sql_select($issue_rtn_sql);
	$floor_id=$room=$rack=$self=$program_no="";$issRt_qty_array=array();
	foreach($issue_rtn_result as $row)
	{
		$store_id 	= ($row[csf('store_id')]=="")?0:$row[csf('store_id')];
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$program_no = ($row[csf('program_no')]=="")?0:$row[csf('program_no')];
		$issRt_qty_array[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$program_no][$store_id][$floor_id][$room][$rack][$self] +=$row[csf('issrtnqnty')];
	}
	/*echo "<pre>";
	print_r($issRt_qty_array);die;*/

	$trans_out_sql="SELECT a.prod_id, a.store_id, a.floor_id,a.room, a.rack, a.self, d.trans_type, d.po_breakdown_id, sum(d.quantity) as trans_out_qnty, c.from_program 
	from inv_transaction a, inv_item_transfer_mst b, inv_item_transfer_dtls c, order_wise_pro_details d
	where b.id=c.mst_id and c.id=d.dtls_id and a.id=d.trans_id and a.id=c.trans_id and d.trans_id=c.trans_id and b.entry_form in(13,81) and d.entry_form in(13,81) and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.po_breakdown_id =".$data_array[0][csf('from_order_id')]." and d.trans_type=6
	group by a.prod_id, a.store_id, a.floor_id,a.room, a.rack, a.self, d.trans_type, d.po_breakdown_id, c.from_program";
	// echo $trans_out_sql;die;
	$transfer_result=sql_select($trans_out_sql);
	$floor_id=$room=$rack=$self="";$trans_out_qnty_array=array();
	foreach($transfer_result as $row)
	{
		$store_id 	= ($row[csf('store_id')]=="")?0:$row[csf('store_id')];
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$program_no = ($row[csf('from_program')]=="")?0:$row[csf('from_program')];
		$trans_out_qnty_array[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$program_no][$store_id][$floor_id][$room][$rack][$self] +=$row[csf('trans_out_qnty')];
	}
	/*echo "<pre>";
	print_r($trans_out_qnty_array);*///die;

	foreach ($data_array as $row) 
	{
		$ycount='';
		$count_id=explode(',',$row[csf('y_count')]);
		foreach($count_id as $count)
		{
			if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
		}

		$company_id=$row[csf("to_company")];
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store")]."';\n";	
		echo "document.getElementById('cbo_floor').value 					= '".$row[csf("floor_id")]."';\n";
		echo "document.getElementById('cbo_room').value 					= '".$row[csf("room")]."';\n";		
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack")]."';\n";		
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf")]."';\n";
		echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "$('#cbo_floor').attr('disabled','disabled');\n";
		echo "$('#cbo_room').attr('disabled','disabled');\n";
		echo "$('#txt_rack').attr('disabled','disabled');\n";
		echo "$('#txt_shelf').attr('disabled','disabled');\n";

		$to_company_id=$row[csf("to_company")];
		if($row[csf("to_store")]>0){			
			echo "load_drop_down('requires/grey_fabric_transfer_v2_controller','".$to_company_id."', 'load_drop_down_store_to', 'to_store_td' );\n";
			echo "document.getElementById('cbo_store_name_to').value 		= '".$row[csf("to_store")]."';\n";
		}
		if($row[csf("to_floor_id")]>0){		
			$store_com=$row[csf("to_store")]."_".$row[csf("to_company")];	
			echo "load_drop_down('requires/grey_fabric_transfer_v2_controller','".$store_com."', 'load_drop_down_floor_to', 'floor_td_to' );\n";
			echo "document.getElementById('cbo_floor_to').value 		= '".$row[csf("to_floor_id")]."';\n";
		}
		if($row[csf("to_room")]>0){		
			$floor_com_store=$row[csf("to_floor_id")]."_".$row[csf("to_company")]."_".$row[csf("to_store")];	
			echo "load_drop_down('requires/grey_fabric_transfer_v2_controller','".$floor_com_store."', 'load_drop_down_room_to', 'room_td_to' );\n";
			echo "document.getElementById('cbo_room_to').value 		= '".$row[csf("to_room")]."';\n";
		}
		if($row[csf("to_rack")]>0){		
			$room_floor_com_store=$row[csf("to_room")]."_".$row[csf("to_company")]."_".$row[csf("to_store")]."_".$row[csf("to_floor_id")];
			echo "load_drop_down('requires/grey_fabric_transfer_v2_controller','".$room_floor_com_store."', 'load_drop_down_rack_to', 'rack_td_to' );\n";
			echo "document.getElementById('txt_rack_to').value 		= '".$row[csf("to_rack")]."';\n";
		}
		if($row[csf("to_shelf")]>0){		
			$rack_room_floor_com_store=$row[csf("to_rack")]."_".$row[csf("to_company")]."_".$row[csf("to_store")]."_".$row[csf("to_floor_id")]."_".$row[csf("to_room")];
			echo "load_drop_down('requires/grey_fabric_transfer_v2_controller','".$rack_room_floor_com_store."', 'load_drop_down_shelf_to', 'shelf_td_to' );\n";
			echo "document.getElementById('txt_shelf_to').value 		= '".$row[csf("to_shelf")]."';\n";
		}
		
		echo "$('#cbo_store_name_to').attr('disabled','disabled');\n";
		echo "$('#cbo_floor_to').attr('disabled','disabled');\n";
		echo "$('#cbo_room_to').attr('disabled','disabled');\n";
		echo "$('#txt_rack_to').attr('disabled','disabled');\n";
		echo "$('#txt_shelf_to').attr('disabled','disabled');\n";
		echo "$('#txt_transfer_qnty').prop('readonly', true);\n";
		echo "$('#txt_roll').prop('readonly', true);\n";
		echo "$('#txt_to_prog').prop('readonly', true);\n";

		echo "document.getElementById('hidden_requ_dtls_id').value 			= '".$row[csf("req_dtls_id")]."';\n";
		echo "document.getElementById('cbo_item_desc').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_roll').value 					= '".$row[csf("roll")]."';\n";
		echo "document.getElementById('txt_ycount').value 					= '".$ycount."';\n";
		echo "document.getElementById('hid_ycount').value 					= '".$row[csf("y_count")]."';\n";
		
		echo "document.getElementById('hid_ybrand').value 					= '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('txt_ylot').value 					= '".$row[csf("yarn_lot")]."';\n";
		echo "document.getElementById('txt_rack_to').value 					= '".$row[csf("to_rack")]."';\n";
		echo "document.getElementById('txt_shelf_to').value 				= '".$row[csf("to_shelf")]."';\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack")]."';\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf")]."';\n";
		echo "document.getElementById('txt_form_prog').value 				= '".$row[csf("from_program")]."';\n";
		echo "document.getElementById('txt_to_prog').value 					= '".$row[csf("to_program")]."';\n";
		echo "document.getElementById('stitch_length').value 				= '".$row[csf("stitch_length")]."';\n";
		//echo "document.getElementById('hide_trans_qty').value 				= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_transfer_value').value 			= '".$row[csf("transfer_value")]."';\n";
		//echo "populate_stock();\n";

		//===================================================
		if($row[csf('from_store')] =="") $store_id = "0"; else $store_id = $row[csf('from_store')];
		if($row[csf('floor_id')] =="") $floor_id = "0"; else $floor_id = $row[csf('floor_id')];
		if($row[csf('room')] =="") $room_id = "0"; else $room_id = $row[csf('room')];
		if($row[csf('rack')] =="") $rack_id = "0"; else $rack_id = $row[csf('rack')];
		if($row[csf('shelf')] =="") $shelf_id = "0"; else $shelf_id = $row[csf('shelf')];

		//echo $row[csf('prod_id')].'='.$row[csf("order_id")].'='.$row[csf('program')].'='.$store_id.'='.$floor_id.'='.$room_id.'='.$rack_id.'='.$shelf_id;die;

		$receive_qty = $receive_qty_array[$row[csf('from_prod_id')]][$row[csf("from_order_id")]][$row[csf('from_program')]][$store_id][$floor_id][$room_id][$rack_id][$shelf_id]['qnty'];
		$brand_id = $receive_qty_array[$row[csf('from_prod_id')]][$row[csf("from_order_id")]][$row[csf('from_program')]][$store_id][$floor_id][$room_id][$rack_id][$shelf_id]['brand_id'];
		$brand_idArray = array_unique( explode(',', chop($brand_id,',')));
		$brand_name="";
		foreach($brand_idArray as $value)
		{
		    if($brand_name=="") $brand_name=$brand_arr[$value]; 
		    else $brand_name.=",".$brand_arr[$value];
		}

		$issRt_qty = $issRt_qty_array[$row[csf('from_prod_id')]][$row[csf("from_order_id")]][$row[csf('from_program')]][$store_id][$floor_id][$room_id][$rack_id][$shelf_id];

		$recvRt_qty = $recvRt_qty_array[$row[csf('from_prod_id')]][$row[csf("from_order_id")]][$store_id][$floor_id][$room_id][$rack_id][$shelf_id];

		$issue_qty = $issue_qty_array[$row[csf('from_prod_id')]][$row[csf("from_order_id")]][$row[csf('from_program')]][$store_id][$floor_id][$room_id][$rack_id][$shelf_id];

		$trans_out_qnty = $trans_out_qnty_array[$row[csf('from_prod_id')]][$row[csf("from_order_id")]][$row[csf('from_program')]][$store_id][$floor_id][$room_id][$rack_id][$shelf_id];
		//echo $receive_qty.'+'.$issRt_qty.'-'.($recvRt_qty.'+'.$issue_qty.'+'.$trans_out_qnty).'+'.$row[csf("transfer_qnty")];
		$stockQty = $receive_qty + $issRt_qty - ($recvRt_qty + $issue_qty + $trans_out_qnty);
		//  + $row[csf("transfer_qnty")]
		echo "document.getElementById('txt_stock').value 				= '".$stockQty."';\n";
		echo "document.getElementById('txt_ybrand').value 				= '".$brand_name."';\n";
		//===================================================
		
		echo "document.getElementById('update_trans_issue_id').value 	= '".$sql_trans[0][csf("trans_id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 	= '".$sql_trans[1][csf("trans_id")]."';\n";
		//echo "document.getElementById('previous_from_prod_id').value 	= '".$row[csf("from_prod_id")]."';\n";
		//echo "document.getElementById('previous_to_prod_id').value 		= '".$row[csf("to_prod_id")]."';\n";
		//echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		exit();
	}
}
// ====================================Requisition Info End============================

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
					
					$sql="SELECT 
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
    //echo "20**".$txt_from_order_id;die;
    
    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and transaction_type in (1,4,5,6)", "max_date");      
	if($max_recv_date != "")
    {
        $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
        $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
        //echo "20**".$transfer_date .'<'. $max_recv_date;die;
        if ($transfer_date < $max_recv_date) 
        {
            echo "20**Transfer Date Can not Be Less Than Last Receive Date Of This Lot";
            die;
        }
    }
    $hide_trans_qty = str_replace("'","",$hide_trans_qty);
    $transfer_qty = str_replace("'","",$txt_transfer_qnty);
    $from_order_id = str_replace("'","",$txt_from_order_id);
    $store_cond = str_replace("'","",$cbo_store_name);
    $floor_cond = str_replace("'","",$cbo_floor);
    $room_cond = str_replace("'","",$cbo_room);
    $rack_cond = str_replace("'","",$txt_rack);
    $shelf_cond = str_replace("'","",$txt_shelf);
    $form_prog_cond = str_replace("'","",$txt_form_prog);
    $item_desc_cond = str_replace("'","",$cbo_item_desc);
    $ylot_cond = str_replace("'","",$txt_ylot);
    if ($form_prog_cond!=0 && $form_prog_cond!="")
    {
    	$form_prog_conda = " and d.booking_id=$form_prog_cond";
    	$form_prog_condb = " and e.booking_id=$form_prog_cond";
    	$form_prog_condc = " and c.from_program=$form_prog_cond";
    }
    $floor_con=" and (( d.entry_form=2 and c.store_floor=$floor_cond ) or ( d.entry_form=22 and c.floor_id=$floor_cond))"; // according to discuss with reza vai and tofael vai

	$sql="SELECT x.prod_id, x.product_name_details, x.lot, x.to_store, x.to_floor, x.to_room, x.to_rack, x.to_shelf, x.booking_id, sum(x.qnty) as qnty
	from(
	SELECT a.id as prod_id, a.product_name_details, a.lot, d.store_id as to_store, (case when d.entry_form =2 and d.receive_basis=2  then c.store_floor when d.entry_form =22  then c.floor_id else 0 end) as to_floor, c.room as to_room, c.rack as to_rack, c.self as to_shelf, sum(b.quantity) as qnty,
	(case when d.entry_form =2 and d.receive_basis=2  then d.booking_id else 0 end) as booking_id 
	from product_details_master a, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_receive_master d 
	where a.id=b.prod_id and b.dtls_id=c.id and c.mst_id=d.id and a.item_category_id=13 and b.entry_form in(2,22) and d.entry_form in(2,22) and b.po_breakdown_id=$from_order_id and d.store_id=$store_cond and a.id=$item_desc_cond $form_prog_conda $floor_con and c.room=$room_cond and c.rack=$rack_cond and c.self=$shelf_cond and a.status_active=1 
	and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.receive_basis<>9 and c.trans_id>0 
	group by a.id, a.product_name_details, a.lot, d.receive_basis, d.booking_id, d.entry_form, d.store_id, c.store_floor,c.floor_id, c.room, c.rack, c.self
	UNION ALL 
	SELECT a.id as prod_id, a.product_name_details, a.lot, d.store_id as to_store, c.floor_id as to_floor, c.room as to_room, c.rack as to_rack, c.self as to_shelf, sum(b.quantity) as qnty, 
	(case when  e.receive_basis=2  then e.booking_id else 0 end) as booking_id
	from product_details_master a, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_receive_master d, inv_receive_master e 
	where a.id=b.prod_id and b.dtls_id=c.id and c.mst_id=d.id and d.booking_id=e.id and e.entry_form=2 and a.item_category_id=13 and b.entry_form in(22) and d.entry_form in(22) and b.po_breakdown_id=$from_order_id  and d.store_id=$store_cond and a.id=$item_desc_cond $form_prog_condb and c.floor_id=$floor_cond and c.room=$room_cond and c.rack=$rack_cond and c.self=$shelf_cond
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.receive_basis=9 and c.trans_id>0 
	group by a.id, a.product_name_details, a.lot, a.brand, e.receive_basis, e.booking_id, d.store_id, c.floor_id, c.room, c.rack, c.self
	UNION ALL 
	SELECT a.id as prod_id, a.product_name_details, a.lot, c.to_store as to_store, c.to_floor_id as to_floor_id, c.to_room as to_room, c.to_rack as to_rack, c.to_shelf as to_shelf, sum(b.quantity) as qnty, 
	(case when  c.from_program !=0  then c.from_program else 0 end) as booking_id
	from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c, inv_item_transfer_mst d 
	where a.id=b.prod_id and b.dtls_id=c.id and c.mst_id=d.id and a.item_category_id=13 and b.entry_form in(13) and d.entry_form in(13) and b.po_breakdown_id=$from_order_id and c.to_store=$store_cond and a.id=$item_desc_cond $form_prog_condc and c.to_floor_id=$floor_cond and c.to_room=$room_cond and c.to_rack=$rack_cond and c.to_shelf=$shelf_cond and b.trans_type=5 and a.status_active=1 
	and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.id, a.product_name_details, a.lot, c.to_store, c.to_floor_id, c.to_room, c.to_rack, c.to_shelf, c.from_program
	) x
	group by x.prod_id, x.product_name_details, x.lot, x.to_store, x.to_floor, x.to_room, x.to_rack, x.to_shelf, x.booking_id";
	//echo "10**".$sql;die;
	$data_array=sql_select($sql);$order_data_array=array();

	$issData=" SELECT a.prod_id, a.store_id, a.floor_id, a.rack, a.room,  a.self, d.po_breakdown_id, sum(d.quantity) as issue_qnty, c.program_no  
	from inv_transaction a, inv_grey_fabric_issue_dtls c, order_wise_pro_details d 
	where c.trans_id=a.id and a.id=d.trans_id and c.id=d.dtls_id and c.trans_id>0 and d.trans_id>0 and d.entry_form=16 and d.po_breakdown_id in($from_order_id) and c.status_active=1 and d.status_active=1  and a.transaction_type=2
	GROUP BY a.prod_id, a.store_id, a.floor_id, a.rack, a.room, a.self, d.po_breakdown_id, c.program_no";
	// echo $issData;die; // and c.program_no in ($program_no)
	$issue_result=sql_select($issData);
	$floor_id=$room=$rack=$self=$program_no="";$issue_qty_array=array();
	foreach($issue_result as $row)
	{
		$store_id 	= ($row[csf('store_id')]=="")?0:$row[csf('store_id')];
		$floor_id 	= ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room 		= ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack 		= ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self 		= ($row[csf('self')]=="")?0:$row[csf('self')];
		$program_no = ($row[csf('program_no')]=="")?0:$row[csf('program_no')];
		$issue_qty_array[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$program_no][$store_id][$floor_id][$room][$rack][$self]+=$row[csf('issue_qnty')];
	}
	/*echo "<pre>";
	print_r($issue_qty_array);die;*/

	$rcv_rtn_sql=" SELECT c.prod_id, c.store_id, c.floor_id,c.room, c.rack, c.self, d.po_breakdown_id, sum(d.quantity) as return_qnty 
	from inv_issue_master b, inv_transaction c, order_wise_pro_details d
	where b.id=c.mst_id and c.id=d.trans_id and c.transaction_type=3 and b.entry_form=45 and d.entry_form=45 and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.po_breakdown_id in($from_order_id)
	group by c.prod_id, c.store_id, c.floor_id,c.room, c.rack, c.self, d.po_breakdown_id";
	// echo $rcv_rtn_sql;die;
	$rcv_rtn_result=sql_select($rcv_rtn_sql);
	$floor_id=$room=$rack=$self="";$recvRt_qty_array=array();
	foreach($rcv_rtn_result as $row)
	{
		$store_id 	= ($row[csf('store_id')]=="")?0:$row[csf('store_id')];
		$floor_id 	= ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room 		= ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack 		= ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self 		= ($row[csf('self')]=="")?0:$row[csf('self')];
		$recvRt_qty_array[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$store_id][$floor_id][$room][$rack][$self]+=$row[csf('return_qnty')];
	}
	/*echo "<pre>";
	print_r($recvRt_qty_array);die;*/

	$issue_rtn_sql=" SELECT c.prod_id, c.store_id, c.floor_id,c.room, c.rack, c.self, (d.quantity) as issrtnqnty, d.po_breakdown_id, (case when b.receive_basis=3 then b.booking_id when b.receive_basis=1 then 0 else 0 end) as program_no 
	from inv_receive_master b, inv_transaction c, order_wise_pro_details d 
	where b.id=c.mst_id and c.id=d.trans_id and c.transaction_type=4 and b.entry_form=51 and d.entry_form=51 and d.po_breakdown_id in($from_order_id) and b.status_active=1 and c.status_active=1 and d.status_active=1";
	// echo $issue_rtn_sql;
	$issue_rtn_result=sql_select($issue_rtn_sql);
	$floor_id=$room=$rack=$self=$program_no="";$issRt_qty_array=array();
	foreach($issue_rtn_result as $row)
	{
		$store_id 	= ($row[csf('store_id')]=="")?0:$row[csf('store_id')];
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$program_no = ($row[csf('program_no')]=="")?0:$row[csf('program_no')];
		$issRt_qty_array[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$program_no][$store_id][$floor_id][$room][$rack][$self] +=$row[csf('issrtnqnty')];
	}
	/*echo "<pre>";
	print_r($issRt_qty_array);die;*/

	$trans_out_sql="SELECT a.prod_id, a.store_id, a.floor_id,a.room, a.rack, a.self, d.trans_type, d.po_breakdown_id, sum(d.quantity) as trans_out_qnty, c.from_program 
	from inv_transaction a, inv_item_transfer_mst b, inv_item_transfer_dtls c, order_wise_pro_details d
	where b.id=c.mst_id and c.id=d.dtls_id and a.id=d.trans_id and a.id=c.trans_id and d.trans_id=c.trans_id and b.entry_form in(13,81) and d.entry_form in(13,81) and b.status_active=1 and c.status_active=1 and d.status_active=1  and d.po_breakdown_id in($from_order_id) and d.trans_type=6
	group by a.prod_id, a.store_id, a.floor_id,a.room, a.rack, a.self, d.trans_type, d.po_breakdown_id, c.from_program";
	//echo $trans_out_sql; and c.from_program in($program_no)
	$transfer_result=sql_select($trans_out_sql);
	$floor_id=$room=$rack=$self="";$trans_out_qnty_array=array();
	foreach($transfer_result as $row)
	{
		$store_id 	= ($row[csf('store_id')]=="")?0:$row[csf('store_id')];
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$program_no = ($row[csf('from_program')]=="")?0:$row[csf('from_program')];
		$trans_out_qnty_array[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$program_no][$store_id][$floor_id][$room][$rack][$self] +=$row[csf('trans_out_qnty')];
	}
	/*echo "<pre>";
	print_r($trans_out_qnty_array);*///die;
	foreach($data_array as $row)
    {
    	if($row[csf('to_store')] =="") $to_store_id = "0"; else $to_store_id = $row[csf('to_store')];
		if($row[csf('to_floor')] =="") $to_floor_id = "0"; else $to_floor_id = $row[csf('to_floor')];
		if($row[csf('to_room')] =="") $to_room_id = "0"; else $to_room_id = $row[csf('to_room')];
		if($row[csf('to_rack')] =="") $to_rack_id = "0"; else $to_rack_id = $row[csf('to_rack')];
		if($row[csf('to_shelf')] =="") $to_shelf_id = "0"; else $to_shelf_id = $row[csf('to_shelf')];
		
		$issRt_qty = $issRt_qty_array[$row[csf('prod_id')]][$from_order_id][$row[csf('booking_id')]][$to_store_id][$to_floor_id][$to_room_id][$to_rack_id][$to_shelf_id];

		$recvRt_qty = $recvRt_qty_array[$row[csf('prod_id')]][$from_order_id][$to_store_id][$to_floor_id][$to_room_id][$to_rack_id][$to_shelf_id];

		$issue_qty = $issue_qty_array[$row[csf('prod_id')]][$from_order_id][$row[csf('booking_id')]][$to_store_id][$to_floor_id][$to_room_id][$to_rack_id][$to_shelf_id];

		$trans_out_qnty = $trans_out_qnty_array[$row[csf('prod_id')]][$from_order_id][$row[csf('booking_id')]][$to_store_id][$to_floor_id][$to_room_id][$to_rack_id][$to_shelf_id];


		$stock = $row[csf("qnty")] + $issRt_qty - ($recvRt_qty + $issue_qty + $trans_out_qnty);

		if ( $transfer_qty > ($stock+$hide_trans_qty) ) 
		{
			echo "20**Stock Qty Not Available";
			disconnect($con);die;
		}
    }
    //echo "10**Check";die;
    if(str_replace("'","",$update_id)!="")
	{
		$is_acknowledge = return_field_value("b.id id", "inv_item_transfer_mst a,inv_item_trans_acknowledgement b", "a.id=b.challan_id and  a.id=$update_id and a.status_active=1 and a.is_acknowledge=1", "id");
		if($is_acknowledge != "" )
		{
			echo "20**Update not allowed. This Transfer Challan is already Acknowledged.\nAcknowledge System ID = $is_acknowledge";
			disconnect($con);die;
		}
		
		$up_cond="";
		if(str_replace("'","",$update_trans_issue_id)!="")
		{
			$all_trans_id=str_replace("'","",$update_trans_issue_id);
			$up_cond=" and a.id not in($all_trans_id)";
		}

		$variable_auto_transfer_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and variable_list= 27", "auto_transfer_rcv");

		if($variable_auto_transfer_rcv == 2)
		{
			$duplicate_product_check=return_field_value("a.id", "inv_transaction a,order_wise_pro_details b", "a.status_active=1 and a.transaction_type in(6) and a.item_category=$cbo_item_category and a.prod_id=$cbo_item_desc and a.mst_id=$update_id and a.id=b.trans_id and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id=$txt_from_order_id $up_cond", "id");
			if($duplicate_product_check)
			{
				echo "20**Duplicate Item With Same From Order Is Not Allow Within Same MRR";disconnect($con);die;
			}
		}
	}
        
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$transfer_recv_num=''; $transfer_update_id='';
		$entry_form_no=13;
		$short_prefix_name="GFTE";
		$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and variable_list= 27 and item_category_id =13", "auto_transfer_rcv");
		
		if($variable_auto_rcv == "")
		{
			$variable_auto_rcv = 1; // if auto receive yes(1), then no need to acknowledgement
		}
		// echo "10**".$variable_auto_rcv;die;

		if(str_replace("'","",$update_id)=="") 
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
		 	
			//$id=return_next_id( "id", "inv_item_transfer_mst", 1 ) ;
			
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,$short_prefix_name,$entry_form_no,date("Y",time()),13 ));
					 
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company, from_order_id, to_order_id, item_category, transfer_requ_no, transfer_requ_id, is_acknowledge, inserted_by, insert_date";
			$is_acknowledge=0;
			if($variable_auto_rcv!=2) // if auto receive yes(1), then no need to acknowledgement
			{
				$is_acknowledge=1;
			}
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$entry_form_no.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$txt_from_order_id.",".$txt_to_order_id.",".$cbo_item_category.",".$txt_requisition_no.",".$txt_requisition_id.",".$is_acknowledge.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			/*$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;*/
			
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_id."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; */
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
    
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, store_id, floor_id, room, rack, self, program_no, stitch_length, balance_qnty, balance_amount, inserted_by, insert_date";

		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, from_order_id, to_order_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, y_count, yarn_lot,from_store,floor_id,room,rack,shelf, to_store,to_floor_id,to_room,to_rack,to_shelf, from_program, to_program, stitch_length, transfer_requ_dtls_id, inserted_by, insert_date, active_dtls_id_in_transfer";
		//echo "10**".$cbo_item_desc;die;
		$rate=str_replace("'","",$txt_rate); $amount=str_replace("'","",$txt_transfer_value); //$rate=0; $amount=0;
		// if (Company to Company) Check existing product or Create new product	
		if(str_replace("'","",$cbo_transfer_criteria)==1) // Company to Company
		{
			//$cbo_item_desc.",".$product_id; // Dtls table
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$cbo_item_desc");
			
			$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=$presentStock*$presentAvgRate;

			// if Qty is zero then rate & value will be zero
			if ($presentStock<=0) 
			{
				$presentStockValue=0;
				$presentAvgRate=0;
			}
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate=$presentAvgRate."*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$cbo_item_desc,1);
			
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			}
			*/ 

			//echo "10**".$prodUpdate;die;
			//$supplier_id=$data_prod[0][csf('supplier_id')];//lot=$txt_yarn_lot and supplier_id='$supplier_id' and	
			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$cbo_company_id_to and item_category_id=$cbo_item_category and id=$cbo_item_desc and status_active=1 and is_deleted=0");
		
			if(count($row_prod)>0) // Check existing product
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
	
				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;

				// if Qty is zero then rate & value will be zero
				if ($curr_stock_qnty<=0) 
				{
					$stock_value=0;
					$avg_rate_per_unit=0;
				}

				if($variable_auto_rcv!=2) // if auto receive yes(1), then no need to acknowledgement
				{
					$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
					$data_array_prod_update=$avg_rate_per_unit."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
					/*$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
					if($flag==1) 
					{
						if($prod) $flag=1; else $flag=0; 
					}*/
				}
			}
			else // Create new product
			{
				//$product_id=return_next_id( "id", "product_details_master", 1 ) ;
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);
				$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;

				// if Qty is zero then rate & value will be zero
				if ($curr_stock_qnty<=0) 
				{
					$stock_value=0;
					$avg_rate_per_unit=0;
				}
				
				if($variable_auto_rcv!=2) // if auto receive yes(1), then no need to acknowledgement
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, upper(dia_width) as dia_width, brand, inserted_by, insert_date from product_details_master where id=$cbo_item_desc";
				}
				else
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date)
					select
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, upper(dia_width) as dia_width, brand, inserted_by, insert_date from product_details_master where id=$cbo_item_desc";
				}			
				
				/*$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} */
			}

			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and transaction_type in (1,4,5)", "max_date");      
            if($max_recv_date != "")
            {
                $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
                $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
                if ($transfer_date < $max_recv_date) 
                {
                    echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
                    if($db_type == 0)
                    {
                        mysql_query("ROLLBACK"); 
                    }else{
                        oci_rollback($con);
                    }
                   // check_table_status( $_SESSION['menu_id'],0);
                    disconnect($con);
                    die;
                }
            }
            //-----------------------------Check Transfer date with Last Issue Date  for Trasfer In-----------------
            $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and transaction_type in (2,3,6)", "max_date");

			if($max_issue_date != "")
		    {
		        $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
		        $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
		        if ($transfer_date < $max_issue_date) 
		        {
		            echo "20**Transfer Date Can not Be Less Than Last Issue Date Of This Lot";
		            if($db_type == 0)
                    {
                        mysql_query("ROLLBACK"); 
                    }else{
                        oci_rollback($con);
                    }
                    //check_table_status( $_SESSION['menu_id'],0);
                    disconnect($con);
                    die;
		        }
		    }
			//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);		
			//$cbo_item_desc=$hidden_product_id
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$cbo_item_desc.",".$cbo_item_category.",6,".$txt_transfer_date.",".$txt_from_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$txt_form_prog.",".$stitch_length.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//$id_trans_recv=$id_trans+1;
			// 5=Replace $cbo_item_desc to $product_id
			$id_trans_recv=0;
			if($variable_auto_rcv!=2) // if auto receive yes(1), then no need to acknowledgement
			{
				$id_trans_recv=return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$id_trans_recv.",".$transfer_update_id.",".$cbo_company_id_to.",".$product_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$txt_to_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$txt_to_prog.",".$stitch_length.",".$txt_transfer_qnty.",".$amount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
			// check existing product or Create new product for company to company $field_array_dtls="to_prod_id"; $product_id;	
			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$id_trans.",".$id_trans_recv.",".$cbo_item_desc.",".$product_id.",".$txt_from_order_id.",".$txt_to_order_id.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_roll.",'".$rate."','".$amount."',".$cbo_uom.",".$hid_ycount.",".$txt_ylot.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$txt_form_prog.",".$txt_to_prog.",".$stitch_length.",".$hidden_requ_dtls_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
		}
		else // Store to Store or order to order
		{
			//$cbo_item_desc.",".$cbo_item_desc; // Dtls table			
			//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and transaction_type in (1,4,5)", "max_date");      
            if($max_recv_date != "")
            {
                $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
                $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
                if ($transfer_date < $max_recv_date) 
                {
                    echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
                    if($db_type == 0)
                    {
                        mysql_query("ROLLBACK"); 
                    }else{
                        oci_rollback($con);
                    }
                    //check_table_status( $_SESSION['menu_id'],0);
                    disconnect($con);
                    die;
                }
            }
            //-----------------------------Check Transfer date with Last Issue Date  for Trasfer In-----------------
            $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and transaction_type in (2,3,6)", "max_date");      
            if($max_issue_date != "")
            {
                $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
                $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
                if ($transfer_date < $max_issue_date) 
                {
                    echo "20**Transfer In Date Can not Be Less Than Last Issue Date Of This Lot";
                    if($db_type == 0)
                    {
                        mysql_query("ROLLBACK"); 
                    }else{
                        oci_rollback($con);
                    }
                    //check_table_status( $_SESSION['menu_id'],0);
                    disconnect($con);
                    die;
                }
            }
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);		
			//$cbo_item_desc=$hidden_product_id
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$cbo_item_desc.",".$cbo_item_category.",6,".$txt_transfer_date.",".$txt_from_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$txt_form_prog.",".$stitch_length.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			
			//changing transfer in product id variable for order wise production table insert
			$product_id = $cbo_item_desc;


			$id_trans_recv=0;
			if($variable_auto_rcv!=2) // if auto receive yes(1), then no need to acknowledgement
			{ // For store to store and order to order -> To Company is a From Company
				$id_trans_recv=return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$id_trans_recv.",".$transfer_update_id.",".$cbo_company_id.",".$cbo_item_desc.",".$cbo_item_category.",5,".$txt_transfer_date.",".$txt_to_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$txt_to_prog.",".$stitch_length.",".$txt_transfer_qnty.",".$amount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}			
			
			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);	
			// check existing product or Create new product for company to company $field_array_dtls="to_prod_id";	
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$id_trans.",".$id_trans_recv.",".$cbo_item_desc.",".$product_id.",".$txt_from_order_id.",".$txt_to_order_id.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_roll.",'".$rate."','".$amount."',".$cbo_uom.",".$hid_ycount.",".$txt_ylot.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$txt_form_prog.",".$txt_to_prog.",".$stitch_length.",".$hidden_requ_dtls_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
		}

		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		
		$data_array_prop="(".$id_prop.",".$id_trans.",6,13,".$id_dtls.",".$txt_from_order_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//$id_prop=$id_prop+1;
		if($variable_auto_rcv!=2)
	 	{
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop.=",(".$id_prop.",".$id_trans_recv.",5,13,".$id_dtls.",".$txt_to_order_id.",".$product_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}



		if(str_replace("'","",$cbo_transfer_criteria)==1) 
		{
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$cbo_item_desc,1);
			
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			}

			if($data_array_prod_update !="")
			{
				$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				}
			}


			if($sql_prod_insert != "")
			{
				$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} 
			}
		}
				
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}
		//echo "10**insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die; //ok
		//echo "10**".$rID2;die;
		//echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die; // ok
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		//echo $flag;die;
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;

		$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		// if auto receive yes(1), then no need to acknowledgement
		//echo $flag;die;
		//echo "10**".$rID.'##'.$rID2.'##'.$rID3.'##'.$rID4.'##'.$variable_auto_rcv;die;

		if($db_type==0)
		{
			if($flag==1)
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
			if($flag==1)
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
		$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and variable_list= 27 and item_category_id =13", "auto_transfer_rcv");

		if($variable_auto_rcv == "")
		{
			$variable_auto_rcv = 1; // if auto receive Yes(1), then no need to acknowledgement
		}

		$entry_form_no=13;
		$short_prefix_name="GFTE";

        /**
         * List of fields that will not change/update on update button event
         * fields=> from_order_id*to_order_id*
         * data=> $txt_from_order_id."*".$txt_to_order_id."*".
         */
		$field_array_update="challan_no*transfer_date*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;*/
		
		$field_array_trans="prod_id*transaction_date*order_id*cons_uom*cons_quantity*cons_rate*cons_amount*store_id*floor_id*room*rack*self*program_no*stitch_length*balance_qnty*balance_amount*updated_by*update_date";

		$field_array_dtls="trans_id*to_trans_id*from_prod_id*to_prod_id*from_order_id*to_order_id*transfer_qnty*roll*rate*transfer_value*uom*y_count*yarn_lot*from_store*floor_id*room*rack*shelf*to_store*to_floor_id*to_room*to_rack*to_shelf*from_program*to_program*stitch_length*transfer_requ_dtls_id*updated_by*update_date";

		//'store_id*floor_id*room*brand_id*balance_qnty*balance_amount '
		$updateTransID_array=array();
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
		
		$rate=str_replace("'","",$txt_rate); $amount=str_replace("'","",$txt_transfer_value);// $rate=0; $amount=0;
		if(str_replace("'","",$cbo_transfer_criteria)==1) // Company to company
		{
			$updateProdID_array=array();
			$field_array_adjust="current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
			
			$stock_from=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_from_prod_id");
			$adjust_curr_stock_from=$stock_from[0][csf('current_stock')]+str_replace("'", '',$hide_trans_qty);
			$cur_st_rate_from=$stock_from[0][csf('avg_rate_per_unit')];
			$cur_st_value_from=$adjust_curr_stock_from*$cur_st_rate_from;
			// if Qty is zero then rate & value will be zero
			if ($adjust_curr_stock_from<=0) 
			{
				$cur_st_value_from=0;
				$cur_st_rate_from=0;
			}
			$previous_from_prod_id=str_replace("'","",$previous_from_prod_id);
			$updateProdID_array[]=$previous_from_prod_id; 
			
			$data_array_adjust[$previous_from_prod_id]=explode("*",("".$adjust_curr_stock_from."*".$cur_st_rate_from."*".$cur_st_value_from."*".$user_id."*'".$pc_date_time."'"));
			
			$stock_to=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_to_prod_id");
			$adjust_curr_stock_to=$stock_to[0][csf('current_stock')]-str_replace("'", '',$hide_trans_qty);
			
			if($variable_auto_rcv != 2 ) // if auto receive Yes(1), then no need to acknowledgement
			{
				$cur_st_rate_to=$stock_to[0][csf('avg_rate_per_unit')];
				$cur_st_value_to=$adjust_curr_stock_to*$cur_st_rate_to;			
				$previous_to_prod_id=str_replace("'","",$previous_to_prod_id);
				
				// if Qty is zero then rate & value will be zero
				if ($adjust_curr_stock_to<=0) 
				{
					$cur_st_value_to=0;
					$cur_st_rate_to=0;
				}

				$updateProdID_array[]=$previous_to_prod_id; 			
				$data_array_adjust[$previous_to_prod_id]=explode("*",("".$adjust_curr_stock_to."*".$cur_st_rate_to."*".$cur_st_value_to."*".$user_id."*'".$pc_date_time."'"));
			}
			/*$prodUpdate_adjust=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array));
			if($flag==1) 
			{
				if($prodUpdate_adjust) $flag=1; else $flag=0; 
			}*/

			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$hidden_product_id");
			
			$presentStock=$data_prod[0][csf('current_stock')] - str_replace("'","",$txt_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=$presentStock*$presentAvgRate;

			// if Qty is zero then rate & value will be zero
			if ($presentStock<=0) 
			{
				$presentStockValue=0;
				$presentAvgRate=0;
			}
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate=$presentAvgRate."*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			/*$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			
			if($flag==1)
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			}*/ 
			//================
			$supplier_id=$data_prod[0][csf('supplier_id')];//and lot=$txt_yarn_lot and supplier_id='$supplier_id'
				
			$row_prod=sql_select("SELECT id, current_stock, avg_rate_per_unit from product_details_master where company_id=$cbo_company_id_to and item_category_id=$cbo_item_category and id=$cbo_item_desc and status_active=1 and is_deleted=0");

			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];

				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				// if Qty is zero then rate & value will be zero
				if ($curr_stock_qnty<=0) 
				{
					$stock_value=0;
					$avg_rate_per_unit=0;
				}

				if($variable_auto_rcv != 2 ) // if auto receive Yes(1), then no need to acknowledgement
				{
					$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";				
					$data_array_prod_update=$avg_rate_per_unit."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					/*$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
					if($flag==1) 
					{
						if($prod) $flag=1; else $flag=0; 
					} */
				}
			}
			else // Create new product
			{
				//$product_id=return_next_id( "id", "product_details_master", 1 ) ;
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);
				$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;

				// if Qty is zero then rate & value will be zero
				if ($curr_stock_qnty<=0) 
				{
					$stock_value=0;
					$avg_rate_per_unit=0;
				}
				
				if($variable_auto_rcv != 2 ) // if auto receive Yes(1), then no need to acknowledgement
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, upper(dia_width) as dia_width, brand, inserted_by, insert_date from product_details_master where id=$cbo_item_desc";
				}
				else
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date)
					select
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, upper(dia_width) as dia_width, brand, inserted_by, insert_date from product_details_master where id=$cbo_item_desc";
				}
				
				
				/*$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				}*/ 
			}			
            //-----------------------------Check Transfer date with Last Receive Date for Trasfer Out------------
            $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and transaction_type in (1,4,5)", "max_date");      
            if($max_recv_date != "") 
            {    
                $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
                $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
                if ($transfer_date < $max_recv_date) 
                {
                    echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
                    if($db_type == 0)
                    {
                        mysql_query("ROLLBACK"); 
                    }else{
                        oci_rollback($con);
                    }
                    //check_table_status( $_SESSION['menu_id'],0);
                    disconnect($con);
                    die;
                }
            }
            //-----------------------------Check Transfer date with Last Issue Date  for Trasfer In-----------------
            $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and transaction_type in (2,3,6)", "max_date");      
            if($max_issue_date != "")
            {    
                $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
                $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
                if ($transfer_date < $max_issue_date) 
                {
                    echo "20**Transfer In Date Can not Be Less Than Last Issue Date Of This Lot";
                    if($db_type == 0)
                    {
                        mysql_query("ROLLBACK"); 
                    }else{
                        oci_rollback($con);
                    }
                    //check_table_status( $_SESSION['menu_id'],0);
                    disconnect($con);
                    die;
                }
            }

			$updateTransID_array[]=$update_trans_issue_id; 
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$cbo_item_desc."*".$txt_transfer_date."*".$txt_from_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$txt_form_prog."*".$stitch_length."*'0'*'0'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
			if ($update_trans_issue_id!="" && $update_trans_recv_id=="") 
			{
				$update_trans_recv_id=0; // if auto receive No(2) then $update_trans_recv_id==""
			}
			if($variable_auto_rcv != 2 ) // if auto receive Yes(1), then no need to acknowledgement
			{
				$updateTransID_array[]=$update_trans_recv_id; 
				$updateTransID_data[$update_trans_recv_id]=explode("*",("'".$product_id."'*".$txt_transfer_date."*".$txt_to_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_to_prog."*".$stitch_length."*".$txt_transfer_qnty."*'".$amount."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			

			$data_array_dtls=$update_trans_issue_id."*".$update_trans_recv_id."*".$cbo_item_desc."*".$product_id."*".$txt_from_order_id."*".$txt_to_order_id."*".$txt_transfer_qnty."*".$txt_roll."*'".$rate."'*'".$amount."'*".$cbo_uom."*".$hid_ycount."*".$txt_ylot."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_form_prog."*".$txt_to_prog."*".$stitch_length."*".$hidden_requ_dtls_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		else // order to order and store to store
		{
			//$field_array_trans="prod_id*transaction_date*order_id*cons_uom*cons_quantity*cons_rate*cons_amount*store_id*floor_id*room*rack*self*brand_id*program_no*stitch_length*balance_qnty*balance_amount*updated_by*update_date";
			$updateTransID_array[]=$update_trans_issue_id; 
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$cbo_item_desc."*".$txt_transfer_date."*".$txt_from_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$txt_form_prog."*".$stitch_length."*0*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
			if ($update_trans_issue_id!="" && $update_trans_recv_id=="") 
			{
				$update_trans_recv_id=0; // if auto receive No(2) then $update_trans_recv_id==""
			}
			
			$product_id = $cbo_item_desc;

			if($variable_auto_rcv != 2 ) // if auto receive Yes(1), then no need to acknowledgement
			{
				$updateTransID_array[]=$update_trans_recv_id; 
				$updateTransID_data[$update_trans_recv_id]=explode("*",("".$product_id."*".$txt_transfer_date."*".$txt_to_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_to_prog."*".$stitch_length."*0*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}			

			$data_array_dtls=$update_trans_issue_id."*".$update_trans_recv_id."*".$cbo_item_desc."*".$product_id."*".$txt_from_order_id."*".$txt_to_order_id."*".$txt_transfer_qnty."*".$txt_roll."*'".$rate."'*'".$amount."'*".$cbo_uom."*".$hid_ycount."*".$txt_ylot."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_form_prog."*".$txt_to_prog."*".$stitch_length."*".$hidden_requ_dtls_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		
		$data_array_prop="(".$id_prop.",".$update_trans_issue_id.",6,13,".$update_dtls_id.",".$txt_from_order_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		//$id_prop=$id_prop+1;
		if($variable_auto_rcv != 2 ) // if auto receive Yes(1), then no need to acknowledgement
		{
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop.=",(".$id_prop.",".$update_trans_recv_id.",5,13,".$update_dtls_id.",".$txt_to_order_id.",".$product_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}		
		



		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$prodUpdate_adjust=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array));
			if($flag==1) 
			{
				if($prodUpdate_adjust) $flag=1; else $flag=0; 
			}

			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			
			if($flag==1)
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			}

			if($data_array_prod_update !="")
			{
				$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} 
			}

			if($sql_prod_insert !="")
			{
				$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				}
			}

		}



		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;

		/*echo "10**".$field_array_trans.'value'.$updateTransID_data;
		print_r($updateTransID_data); die;*/
		//echo "10**".bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array);oci_rollback($con);die;

		$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array));
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}
		$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}

		$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id=$update_dtls_id and entry_form=13");
		{
			if($query) $flag=1; else $flag=0; 
		} 
		// echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		}

		// if auto receive Yes(1), then no need to acknowledgement
		//echo "10**".$rID.'**'.$rID2.'**'.$rID3.'**'.$query.'**'.$rID4.'##'.$variable_auto_rcv.'**'.$update_trans_recv_id;oci_rollback($con);die;
		//echo "10**".$flag;die;

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
}

if ($action=="grey_fabric_order_to_order_transfer_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	//$job_arr = return_library_array("select b.id, a.job_no from wo_po_details_master a,","id","job_no");
	$po_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
	$qnty_arr = return_library_array("select id, po_quantity from wo_po_break_down","id","po_quantity");
	$buyer_arr = return_library_array("select id, buyer_name from wo_po_details_master","id","buyer_name");
	//$style_arr = return_library_array("select id, style_ref_no from wo_po_details_master","id","style_ref_no");
	$ship_date_arr = return_library_array("select id, pub_shipment_date from wo_po_break_down","id","pub_shipment_date");
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13","id","product_name_details");
	
	$poDataArray=sql_select("select b.id,a.buyer_name,a.style_ref_no,a.job_no,b.po_number, b.grouping from  wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$data[0] and b.status_active=1 and b.is_deleted=0 ");// and a.season like '$txt_season'
		$job_array=array(); //$all_job_id='';
		foreach($poDataArray as $row)
		{
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['grouping']=$row[csf('grouping')];
		} 

	
	$sqlFromOrderArr="SELECT po_id,LISTAGG(dtls_id, ',') WITHIN GROUP (ORDER BY dtls_id) as prog_no, sum(program_qnty) as program_qnty from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 and is_sales!=1 and po_id =".$dataArray[0][csf('from_order_id')]." group by  po_id ";
	//echo $sqlFromOrderArr;die;
	$fromOrderArr=sql_select($sqlFromOrderArr);
	$fromProgInfoArray=array();
	foreach($fromOrderArr as $row)
	{
		$fromProgInfoArray[$row[csf('po_id')]]['prog_no'] = $row[csf('prog_no')];
		$fromProgInfoArray[$row[csf('po_id')]]['program_qnty'] = $row[csf('program_qnty')];
	} 
	unset($fromOrderArr);
	//var_dump($fromProgInfoArray);
	$sqlToOrderArr="SELECT po_id,LISTAGG(dtls_id, ',') WITHIN GROUP (ORDER BY dtls_id) as prog_no, sum(program_qnty) as program_qnty from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 and is_sales!=1 and po_id =".$dataArray[0][csf('to_order_id')]." group by  po_id ";
	//echo $sqlToOrderArr;die;
	$toOrderArr=sql_select($sqlToOrderArr);
	$toProgInfoArray=array();
	foreach($toOrderArr as $row)
	{
		$toProgInfoArray[$row[csf('po_id')]]['prog_no'] = $row[csf('prog_no')];
		$toProgInfoArray[$row[csf('po_id')]]['program_qnty'] = $row[csf('program_qnty')];
	} 
	unset($fromOrderArr);
	//var_dump($toProgInfoArray);
	?>
	<style type="text/css">
        .word_wrap_break {
            word-break: break-all;
            word-wrap: break-word;
        }
    </style>
	<div style="width:950px;">
	    <table width="940" cellspacing="0" align="right">
	        <tr>
	            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="6" align="center" style="font-size:14px">  
					<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
						foreach ($nameArray as $result)
						{ 
						?>
							Plot No: <? echo $result['plot_no']; ?> 
							Level No: <? echo $result['level_no']?>
							Road No: <? echo $result['road_no']; ?> 
							Block No: <? echo $result['block_no'];?> 
							City No: <? echo $result['city'];?> 
							Zip Code: <? echo $result['zip_code']; ?> 
							Province No: <?php echo $result['province'];?> 
							Country: <? echo $country_arr[$result['country_id']]; ?><br> 
							Email Address: <? echo $result['email'];?> 
							Website No: <? echo $result['website'];
						}
	                ?> 
	            </td>  
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
	        </tr>
	        <tr>
	        	<td width="150"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
	            <td width="140"><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
	            <td width="135"><strong>Challan No.:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>From order No:</strong></td> <td width="175px"><? echo $po_arr[$dataArray[0][csf('from_order_id')]]; ?></td>
	            <td><strong>From ord Qnty:</strong></td> <td width="175px"><? echo $qnty_arr[$dataArray[0][csf('from_order_id')]]; ?></td>
	            <td><strong>From ord Buyer:</strong></td> <td width="175px"><? echo $buyer_library[$job_array[$dataArray[0][csf('from_order_id')]]['buyer']]; //$buyer_library[$buyer_arr[$dataArray[0][csf('from_order_id')]]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>From Style Ref.:</strong></td> <td width="175px"><? echo $job_array[$dataArray[0][csf('from_order_id')]]['style']; //$style_arr ?></td>
	            <td><strong>From Job No:</strong></td> <td width="175px"><? echo $job_array[$dataArray[0][csf('from_order_id')]]['job'];
				//$job_array[$row[csf('id')]]['job'];
				 ?></td>
	            <td><strong>From Ship. Date:</strong></td> <td width="175px"><? echo change_date_format($ship_date_arr[$dataArray[0][csf('from_order_id')]]); ?></td>
	        </tr>
	        <tr>
				<td valign='top'><strong>From Interenl Ref.:</strong></td> <td width="175px" valign='top'><? echo $job_array[$dataArray[0][csf('from_order_id')]]['grouping'];  ?></td>
				<td><strong>To order No:</strong></td> <td width="175px"><? echo $po_arr[$dataArray[0][csf('to_order_id')]]; ?></td>
	            <td><strong>To ord Qnty:</strong></td> <td width="175px"><? echo $qnty_arr[$dataArray[0][csf('to_order_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>To ord Buyer:</strong></td> <td width="175px"><? echo $buyer_library[$job_array[$dataArray[0][csf('to_order_id')]]['buyer']];//$buyer_library[$buyer_arr[$dataArray[0][csf('to_order_id')]]]; ?></td>
	            <td><strong>To Style Ref.:</strong></td> <td width="175px"><? echo $job_array[$dataArray[0][csf('to_order_id')]]['style'];//$style_arr[$dataArray[0][csf('to_order_id')]]; ?></td>
	            <td><strong>To Job No:</strong></td> <td width="175px"><? echo $job_array[$dataArray[0][csf('to_order_id')]]['job']//$job_arr[$dataArray[0][csf('to_order_id')]]; ?></td>
	        </tr>
	        <tr>
				<td valign='top'><strong>To Interenl Ref.:</strong></td> <td width="175px" valign='top'><? echo $job_array[$dataArray[0][csf('to_order_id')]]['grouping'];  ?></td>
				<td><strong>To Ship. Date:</strong></td> <td width="175px"><? echo change_date_format($ship_date_arr[$dataArray[0][csf('to_order_id')]]); ?></td>
			</tr>
			<tr>
				<td valign='top'><strong>From Program QTY:</strong></td> <td width="175px" valign='top' colspan="2"><? echo $fromProgInfoArray[$dataArray[0][csf('from_order_id')]]['program_qnty'];  ?></td>
				<td valign='top'><strong>To Program QTY:</strong></td> <td width="175px" valign='top' colspan="2"><? echo $toProgInfoArray[$dataArray[0][csf('to_order_id')]]['program_qnty'];  ?></td>
			</tr>
			<tr>
				<td valign='top'><strong>From Program No:</strong></td>
				<td width="175px" valign='top' colspan="2" class="word_wrap_break"><? echo $fromProgInfoArray[$dataArray[0][csf('from_order_id')]]['prog_no'];  ?></td>
				<td valign='top'><strong>To Program No:</strong></td>
				<td width="175px" valign='top' colspan="2" class="word_wrap_break"><? echo $toProgInfoArray[$dataArray[0][csf('to_order_id')]]['prog_no'];  ?></td>
			</tr>
	    </table>
	    <br>
	    <div style="width:100%;">
		    <table align="right" cellspacing="0" width="940"  border="1" rules="all" class="rpt_table" >
		        <thead bgcolor="#dddddd" align="center">
		            <th width="30">SL</th>
		            <th width="120" >Item Category</th>
		            <th width="250" >Item Description</th>
		            <th width="70" >UOM</th>
		            <th width="100" >Transfered Qnty</th>
		        </thead>
		        <tbody> 
		   
					<?
					$sql_dtls="select id, item_category, item_group, from_prod_id, transfer_qnty, uom from inv_item_transfer_dtls where mst_id='$data[1]' and active_dtls_id_in_transfer =1 and status_active=1 and is_deleted=0";
					
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
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
				            <td align="center"><? echo $i; ?></td>
				            <td><? echo $item_category[$row[csf("item_category")]]; ?></td>
				            <td><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
				            <td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
				            <td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
						</tr>
						<? 
						$i++; 
					} ?>
		        </tbody>
		        <tfoot>
		            <tr>
		                <td colspan="4" align="right"><strong>Total :</strong></td>
		                <td align="right"><?php echo $transfer_qnty_sum; ?></td>
		            </tr>                           
		        </tfoot>
		    </table>
	        <br>
			 <?
	            echo signature_table(202, $data[0], "900px");
	         ?>
	    </div>
   	</div>   
 <?	
 exit();
}
?>

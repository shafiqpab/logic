<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");


if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/grey_transfer_requ_entry_controller",$data);
}

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/grey_transfer_requ_entry_controller*2', 'store','from_store_td', $('#cbo_company_id').val(),this.value),'','','','','','',fnc_item_blank(1); " );
	exit();
}

if ($action=="load_drop_down_location_to")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/grey_transfer_requ_entry_controller*2*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id_to').val(),this.value);" );
	exit();
}


if ($action=="itemTransfer_popup")
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
	</script>
</head>
<body>
	<div align="center" style="width:880px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:760px;margin-left:10px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
					<thead>
						<th width="200">Search By</th>
						<th width="200" id="search_by_td_up">Please Enter Requisition ID</th>
						<th width="220">Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<?
							$search_by_arr=array(1=>"Requisition ID",2=>"Challan No.",3=>"Batch Number");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td id="search_by_td">
							<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />&nbsp;To&nbsp;
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_transfer_search_list_view', 'search_div', 'grey_transfer_requ_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" height="40" valign="middle" colspan="4"><? echo load_month_buttons(1);  ?></td>
					</tr>
				</table>

			</fieldset>
		</form>
		<fieldset style="width:860px;margin-left:10px">
			<legend>Search Result</legend>
			<div style="margin-top:10px" id="search_div"></div>
		</fieldset>
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
	$date_form=trim($data[3]);
	$date_to =trim($data[4]);

	$batch_id_arr = return_library_array("select id, batch_no from pro_batch_create_mst where (extention_no is null or extention_no =0)  and company_id=$company_id and is_deleted=0 and status_active=1","id","batch_no");
	$batch_number_arr = array_flip($batch_id_arr);

	//echo "<pre>";
	//print_r($batch_number_arr);die;

	if($date_form !="" && $date_to !="")
	{
		if($db_type==0) $date_cond=" and a.transfer_date between '".change_date_format(trim($date_form),"yyyy-mm-dd")."' and '".change_date_format($date_to,"yyyy-mm-dd")."'";
		else $date_cond=" and a.transfer_date between '".change_date_format(trim($date_form),"","",1)."' and '".change_date_format(trim($date_to),"","",1)."'";
	}

	if($search_by==1){
		$search_field=" a.transfer_system_id";
	}else if($search_by==2){
		$search_field=" a.challan_no";
	}else{
		$search_string="%".$batch_number_arr[trim($data[0])]."%";
		$search_field=" b.to_batch_id";
	}

	if($db_type==0) $year_field="YEAR(a.insert_date)";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY')";
	else $year_field="";//defined Later

	$sql="select a.id, $year_field as year, a.transfer_prefix_number, a.transfer_system_id, a.challan_no, a.company_id, a.transfer_date, a.transfer_criteria, a.item_category,a.location_id,a.to_location_id, b.to_batch_id from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
	where a.id=b.mst_id and a.item_category=2 and a.company_id=$company_id and $search_field like '$search_string' and a.transfer_criteria in(1,2,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond and a.entry_form=14
	group by a.id, $year_field, a.transfer_prefix_number, a.transfer_system_id, a.challan_no, a.company_id, a.transfer_date, a.transfer_criteria, a.item_category,a.location_id,a.to_location_id,b.to_batch_id
	order by a.id desc";
	//echo $sql;//die;

	$arr=array(3=>$batch_id_arr,4=>$company_arr,6=>$item_transfer_criteria,7=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Batch No,Company,Transfer Date,Transfer Criteria,Item Category", "70,60,100,100,120,90,140","860","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,to_batch_id,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,to_batch_id,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,0,3,0,0');

	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("select transfer_system_id, challan_no, company_id, location_id, transfer_date, transfer_criteria, item_category, to_company, to_location_id, ready_to_approve from inv_item_transfer_requ_mst where id='$data'");
	foreach ($data_array as $row)
	{
		echo "active_inactive(".$row[csf("transfer_criteria")].");\n";

		echo "load_drop_down('requires/grey_transfer_requ_entry_controller','".$row[csf("company_id")]."', 'load_drop_down_location', 'from_location_td' );\n";
		echo "load_drop_down('requires/grey_transfer_requ_entry_controller','".$row[csf("to_company")]."', 'load_drop_down_location_to', 'to_location_td' );\n";

		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";

		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('previous_to_company_id').value 		= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('cbo_location_to').value 				= '".$row[csf("to_location_id")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value 			= '".$row[csf("ready_to_approve")]."';\n";

		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#cbo_location').attr('disabled','disabled');\n";

		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n";
		exit();

	}
}



if ($action=="itemDescription_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			$('#product_id').val(data);
			parent.emailwindow.hide();
		}
	</script>

</head>

<body>
	<div align="center">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:910px;margin-left:10px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="900" class="rpt_table" border="1" rules="all" align="center">
					<thead>
						<th width="150">Order</th>
						<th width="150">Booking</th>
						<th width="150">Batch No.</th>
						<th width="150">Buyer</th>
						<th width="180" id="search_by_td_up">Item Details</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="product_id" id="product_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<input type="text" style="width:120px;" class="text_boxes"  name="txt_order_no" id="txt_order_no" value="<? if($transfer_criteria==4) echo $txt_from_order_no; ?>" <? if($transfer_criteria==4) echo "disabled"; else echo ""; ?> />
							<input type="hidden"  name="txt_order_id" id="txt_order_id" value="<? if($transfer_criteria==4) echo $txt_from_order_id; ?>" />
						</td>
						<td>
							<input type="text" style="width:120px;" class="text_boxes"  name="txt_batch_no" id="txt_batch_no" />
						</td>
						<td>
							<input type="text" style="width:120px;" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />
						</td>

						<td>
							<?
							echo create_drop_down( "cbo_buyer_name", 140, "select a.id, a.buyer_name from lib_buyer a, lib_buyer_party_type b, lib_buyer_tag_company c where a.id=b.buyer_id and a.id=c.buyer_id and b.party_type=1 and c.tag_company='$cbo_company_id' and a.status_active=1 and a.is_deleted =0 order by a.buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
							?>
						</td>

						<td>
							<input type="text" style="width:150px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_batch_no').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_store_name; ?>+'_'+<? echo $cbo_floor; ?>+'_'+<? echo $cbo_room; ?>+'_'+<? echo $txt_rack; ?>+'_'+<? echo $txt_shelf; ?>+'_'+document.getElementById('txt_order_id').value, 'create_product_search_list_view', 'search_div', 'grey_transfer_requ_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
				</table>
				<div style="margin-top:10px;" id="search_div"></div>
			</fieldset>
		</form>
	</div>

</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=='create_product_search_list_view')
{
	$data = explode("_",$data);
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	$txt_order_no 	= str_replace("'","",$data[0]);
	$txt_batch_no 	= str_replace("'","",$data[1]);
	$txt_booking_no = str_replace("'","",$data[2]);
	$cbo_buyer_name = str_replace("'","",$data[3]);
	$search_string	= trim(str_replace("'","",$data[4]));
	$company_id 	= str_replace("'","",$data[5]);
	$cbo_store_name	= str_replace("'","",$data[6]);
	$cbo_floor		= str_replace("'","",$data[7]);
	$cbo_room		= str_replace("'","",$data[8]);
	$cbo_rack		= str_replace("'","",$data[9]);
	$txt_shelf		= str_replace("'","",$data[10]);
	$order_id		= trim(str_replace("'","",$data[11]));


	$product_ids="";
	if($db_type==0) $select_prod_field=" group_concat(id) as id"; else $select_prod_field="listagg(cast(id as varchar(4000)),',') within group(order by id) as id";
	if($search_string!="") $product_ids=return_field_value("$select_prod_field","product_details_master","product_name_details ='$search_string'","id");

	$sql_cond="";
	if($order_id!="") 			$sql_cond =" and d.po_breakdown_id =$order_id";
	if($product_ids!="") 		$sql_cond .=" and c.prod_id in($product_ids)";
	if($txt_order_no != "") 	$sql_cond .=" and f.po_number = '$txt_order_no'";
	if($txt_booking_no != "") 	$sql_cond .=" and e.booking_no = '$txt_booking_no'";
	if($txt_batch_no != "") 	$sql_cond .=" and e.batch_no = '$txt_batch_no'";
	if($cbo_buyer_name > 0) 	$sql_cond .=" and g.buyer_name ='$cbo_buyer_name'";

	if($cbo_floor  > 0) 		$sql_cond .=" and c.floor_id ='$cbo_floor'";
	if($cbo_room  > 0) 			$sql_cond .=" and c.room ='$cbo_room'";
	if($cbo_rack  > 0) 			$sql_cond .=" and c.rack ='$cbo_rack'";
	if($txt_shelf  > 0) 		$sql_cond .=" and c.self ='$txt_shelf'";

	//echo $sql_cond.jahid;die;

	$sql = "select id, product_name_details, color, unit_of_measure, current_stock, avg_rate_per_unit, detarmination_id, company_id, store_id, floor_id, fabric_shade, body_part_id, room, rack_no, shelf_no, sum(qnty) as qnty,prod_id, po_number, order_id,buyer_id, booking_no, batch_no, batch_id
	from
	(
	select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock, a.avg_rate_per_unit, a.detarmination_id, c.company_id,c.store_id, c.floor_id, b.fabric_shade, b.body_part_id, (case when c.room is null or c.room=0 then 0 else c.room end) room,(case when c.rack is null or c.rack=0 then 0 else c.rack end) rack_no,(case when c.self is null or c.self=0 then 0 else c.self end) shelf_no,sum(d.quantity) as qnty,c.prod_id, f.po_number, d.po_breakdown_id as order_id, g.buyer_name as buyer_id,e.booking_no,e.batch_no,  c.pi_wo_batch_no as batch_id
	from product_details_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details d, inv_transaction c, pro_batch_create_mst e, wo_po_break_down f, wo_po_details_master g
	where d.entry_form in (7,37) and a.id=b.prod_id and b.trans_id=c.id  and c.company_id=$company_id and c.store_id = '$cbo_store_name' $sql_cond and a.item_category_id=2 and c.item_category=2 and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.trans_id = d.trans_id  and d.trans_id = c.id and d.po_breakdown_id = f.id and f.job_no_mst = g.job_no and c.pi_wo_batch_no = e.id and b.body_part_id > 0
	group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock, a.avg_rate_per_unit, a.detarmination_id, c.company_id, c.store_id, c.floor_id, b.fabric_shade, b.body_part_id, c.room, c.rack, c.self, c.prod_id, f.po_number, d.po_breakdown_id,g.buyer_name,e.booking_no, e.batch_no, c.pi_wo_batch_no
	union all
	select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock, a.avg_rate_per_unit, a.detarmination_id, c.company_id,c.store_id, c.floor_id, b.fabric_shade, c.body_part_id, (case when c.room is null or c.room=0 then 0 else c.room end) room,(case when c.rack is null or c.rack=0 then 0 else c.rack end) rack_no,(case when c.self is null or c.self=0 then 0 else c.self end) shelf_no, sum(d.quantity) as qnty,  c.prod_id, f.po_number , d.po_breakdown_id as order_id, g.buyer_name as buyer_id, e.booking_no, e.batch_no, c.pi_wo_batch_no as batch_id
	from product_details_master a, inv_item_transfer_requ_dtls b, inv_transaction c, order_wise_pro_details d, pro_batch_create_mst e, wo_po_break_down f, wo_po_details_master g
	where a.id = b.to_prod_id and b.to_trans_id = c.id   and c.transaction_type = 5 and c.item_category = 2  and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and a.status_active=1 and a.is_deleted=0 and c.company_id = $company_id and c.store_id = '$cbo_store_name' $sql_cond and d.entry_form in (14,15,306) and d.trans_type = 5 and c.id = d.trans_id and c.pi_wo_batch_no = e.id and d.po_breakdown_id = f.id and f.job_no_mst = g.job_no and d.status_active =1 and f.status_active =1 and c.body_part_id > 0
	group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock, a.avg_rate_per_unit, a.detarmination_id, a.company_id,c.store_id, c.company_id, c.floor_id, b.fabric_shade, c.body_part_id, c.room,c.rack,c.self,c.prod_id, f.po_number, d.po_breakdown_id,g.buyer_name,e.booking_no, e.batch_no, c.pi_wo_batch_no
	)
	group by id, product_name_details, color, unit_of_measure, current_stock, avg_rate_per_unit, detarmination_id, company_id,store_id, company_id, floor_id,fabric_shade, body_part_id, room, rack_no, shelf_no, prod_id, po_number, order_id, buyer_id, booking_no, batch_no, batch_id";
	//echo $sql;//die;
	$result = sql_select($sql);
	$order_data_array=array();
	foreach($result as $row )
	{
		$prodidArr[$row[csf('prod_id')]]=$row[csf('prod_id')];
	}
	$prodidArr = array_filter($prodidArr);
	if(!empty($prodidArr))
	{
		$all_prod_id_cond_1 = $prodCond_1="";
		$all_prod_id_cond_2 = $prodCond_2="";
		$all_prod_ids = implode(",", $prodidArr);
		if($db_type==2 && count($prodidArr)>999)
		{
			$prodidArr_chunk=array_chunk($prodidArr,999) ;
			foreach($prodidArr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$prodCond_1.="  a.prod_id in($chunk_arr_value) or ";
				$prodCond_2.="  id in($chunk_arr_value) or ";
			}

			$all_prod_id_cond_1.=" and (".chop($prodCond_1,'or ').")";
			$all_prod_id_cond_2.=" and (".chop($prodCond_2,'or ').")";
		}
		else
		{
			$all_prod_id_cond_1=" and a.prod_id in($all_prod_ids)";
			$all_prod_id_cond_2=" and id in($all_prod_ids)";
		}
	}

	$issue_qty_array=array();
	$issData = sql_select("SELECT a.prod_id, a.pi_wo_batch_no as batch_id,a.fabric_shade, a.floor_id, d.po_breakdown_id, b.body_part_id, a.rack, a.room,  a.self, sum(d.quantity) as issue_qnty FROM inv_finish_fabric_issue_dtls b, inv_transaction a, order_wise_pro_details d WHERE d.entry_form=18 and b.trans_id=a.id and a.id=d.trans_id and a.item_category=2 and a.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$company_id and a.store_id ='$cbo_store_name' and b.trans_id = d.trans_id $all_prod_id_cond_1  GROUP BY a.prod_id, a.pi_wo_batch_no,a.fabric_shade, a.floor_id, d.po_breakdown_id, b.body_part_id, a.rack, a.room, a.self ");


	$floor_id=$room=$rack=$self="";
	foreach($issData as $row)
	{
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$issue_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$floor_id][$room][$rack][$self][$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]]+=$row[csf('issue_qnty')];
	}

	$recvRt_qty_array=array(); $issRt_qty_array=array();
	$receiveReturnData=sql_select("SELECT a.prod_id, a.pi_wo_batch_no, a.fabric_shade, a.floor_id,a.room, a.rack, a.self, sum( b.quantity ) as return_qnty , b.po_breakdown_id, a.body_part_id FROM inv_transaction a, order_wise_pro_details b WHERE a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type =3 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and a.store_id ='$cbo_store_name' and b.entry_form =46 $all_prod_id_cond_1 GROUP BY a.prod_id, a.fabric_shade,a.pi_wo_batch_no, a.floor_id,a.room, a.rack, a.self, b.po_breakdown_id, a.body_part_id");


	$floor_id=$room=$rack=$self="";
	foreach($receiveReturnData as $row)
	{
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$recvRt_qty_array[$row[csf('prod_id')]][$row[csf('pi_wo_batch_no')]][$row[csf('fabric_shade')]][$floor_id][$room][$rack][$self][$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]] +=$row[csf('return_qnty')];
	}

	$issueReturnData=sql_select("SELECT a.prod_id, a.pi_wo_batch_no,c.body_part_id, b.po_breakdown_id, a.fabric_shade, a.floor_id,a.room, a.rack, a.self, sum(b.quantity) as issrqnty FROM inv_transaction a, order_wise_pro_details b, pro_finish_fabric_rcv_dtls c WHERE a.id=b.trans_id and a.id = c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type =4 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$company_id and a.store_id ='$cbo_store_name' $all_prod_id_cond_1 GROUP BY a.prod_id, a.fabric_shade,c.body_part_id,a.pi_wo_batch_no, b.po_breakdown_id,a.floor_id,a.room, a.rack, a.self");


	$floor_id=$room=$rack=$self="";
	foreach($issueReturnData as $row)
	{
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$issRt_qty_array[$row[csf('prod_id')]][$row[csf('pi_wo_batch_no')]][$row[csf('fabric_shade')]][$floor_id][$room][$rack][$self][$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]] +=$row[csf('issrqnty')];
	}

	/*echo "SELECT a.pi_wo_batch_no, b.po_breakdown_id,a.prod_id, c.body_part_id, a.fabric_shade, a.floor_id ,  a.room, a.rack , a.self, sum(b.quantity) as trans_out_qnty from  inv_transaction a, order_wise_pro_details b, inv_item_transfer_dtls c where  a.id = b.trans_id and a.id = c.trans_id and b.trans_id=c.trans_id and b.trans_type=6 and a.company_id = $company_id and a.store_id ='$cbo_store_name' and a.transaction_type = 6 and a.item_category = 2 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and a.pi_wo_batch_no> 0 $all_prod_id_cond_1 group by a.pi_wo_batch_no,b.po_breakdown_id, a.prod_id, c.body_part_id, a.fabric_shade, a.floor_id,  a.room,a.rack,a.self";*/

	$transOutData = sql_select("SELECT a.pi_wo_batch_no, b.po_breakdown_id,a.prod_id, c.body_part_id, a.fabric_shade, a.floor_id ,  a.room, a.rack , a.self, sum(b.quantity) as trans_out_qnty from  inv_transaction a, order_wise_pro_details b, inv_item_transfer_dtls c where  a.id = b.trans_id and a.id = c.trans_id and b.trans_id=c.trans_id and b.trans_type=6 and a.company_id = $company_id and a.store_id ='$cbo_store_name' and a.transaction_type = 6 and a.item_category = 2 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and a.pi_wo_batch_no> 0 $all_prod_id_cond_1 group by a.pi_wo_batch_no,b.po_breakdown_id, a.prod_id, c.body_part_id, a.fabric_shade, a.floor_id,  a.room,a.rack,a.self");

	$floor_id=$room=$rack=$self="";
	foreach($transOutData as $row)
	{
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$trans_out_qnty_array[$row[csf('prod_id')]][$row[csf('pi_wo_batch_no')]][$row[csf('fabric_shade')]][$floor_id][$room][$rack][$self][$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]] +=$row[csf('trans_out_qnty')];
	}

	$prod_sql=sql_select("select id, product_name_details, color from product_details_master where status_active=1 $all_prod_id_cond_2 ");
	$prod_data=array();
	foreach($prod_sql as $row)
	{
		$prod_data[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
		$prod_data[$row[csf("id")]]["color"]=$row[csf("color")];
	}


	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name from lib_floor_room_rack_dtls b
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	where b.status_active=1 and b.is_deleted=0";
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
	?>
	<div style="width:1560px;" align="center">
		<table cellspacing="0" width="100%" class="rpt_table" id="" rules="all" align="center">
			<thead>
				<tr>
					<th width="35">SL</th>
					<th width="80">Order</th>
					<th width="80">Item ID</th>
					<th width="120">Company</th>
					<th width="120">Buyer</th>
					<th width="100">Body Part</th>
					<th width="200">Item Details</th>
					<th width="90">Color</th>
					<th width="90">F. Shade</th>
					<th width="90">Booking</th>
					<th width="90">Batch</th>
					<th width="90">Floor</th>
					<th width="90">Room</th>
					<th width="90">Rack</th>
					<th width="90">Shelf</th>
					<th>Stock</th>
				</tr>
			</thead>
		</table>
	</div>
	<div style="width:1560px; overflow-y:scroll; max-height:250px;" align="center">
		<table cellspacing="0" width="1540" class="rpt_table" id="tbl_list_search" rules="all" align="center"  style="margin-bottom: 5px; word-break:break-all">
			<tbody>
				<?
				$i=1;
				foreach($result as $row)
				{
					if($row[csf('floor_id')] =="") $floor_id = "0"; else $floor_id = $row[csf('floor_id')];
					if($row[csf('room')] =="") $room_id = "0"; else $room_id = $row[csf('room')];
					if($row[csf('rack_no')] =="") $rack_id = "0"; else $rack_id = $row[csf('rack_no')];
					if($row[csf('shelf_no')] =="") $self_id = "0"; else $self_id = $row[csf('shelf_no')];

					$floor 		= $lib_floor_arr[$row[csf('company_id')]][$floor_id];
					$room 		= $lib_room_arr[$row[csf('company_id')]][$floor_id][$room_id];
					$rack_no	= $lib_rack_arr[$row[csf('company_id')]][$floor_id][$room_id][$rack_id];
					$shelf_no 	= $lib_shelf_arr[$row[csf('company_id')]][$floor_id][$room_id][$rack_id][$self_id];


					$issRt_qty = $issRt_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$floor_id][$room_id][$rack_id][$self_id][$row[csf("order_id")]][$row[csf('body_part_id')]];

					$recvRt_qty = $recvRt_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$floor_id][$room_id][$rack_id][$self_id][$row[csf("order_id")]][$row[csf('body_part_id')]];

					$issue_qty = $issue_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$floor_id][$room_id][$rack_id][$self_id][$row[csf("order_id")]][$row[csf('body_part_id')]];

					$trans_out_qnty = $trans_out_qnty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$floor_id][$room_id][$rack_id][$self_id][$row[csf("order_id")]][$row[csf('body_part_id')]];
					$ref_title = "prod id=".$row[csf('prod_id')].", batch=".$row[csf('batch_id')].", f.shade=".$row[csf('fabric_shade')].", floor=".$floor_id.", room=".$room_id.", rack=".$rack_id.", self=".$self_id.",order=".$row[csf("order_id")].", body=".$row[csf('body_part_id')];

					$stock = $row[csf("qnty")] + $issRt_qty - ($recvRt_qty + $issue_qty + $trans_out_qnty);
					$stock_title = $row[csf("qnty")]." + ".$issRt_qty." + ".$recvRt_qty." + ".$issue_qty." + ".$trans_out_qnty;
					$title = "rcv+trans_in= ".$row[csf("qnty")]." , iss_return= ".$issRt_qty." , rcv_return=". $recvRt_qty." , trans_out = ".$trans_out_qnty." , issue = ".$issue_qty;
					if($stock>0)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr  bgcolor="<? echo $bgcolor; ?>" valign="middle"  style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf("order_id")]."_".$row[csf('prod_id')]."_".$row[csf('company_id')]."_".$row[csf('store_id')]."_".$floor_id."_".$room_id."_".$rack_id."_".$self_id."_".$row[csf('batch_id')]."_".$row[csf('batch_no')]."_".$row[csf('avg_rate_per_unit')]."_".$prod_data[$row[csf('prod_id')]]['product_name_details']."_".$prod_data[$row[csf('prod_id')]]['color']."_".$row[csf('po_number')]."_".$row[csf('fabric_shade')]."_".$row[csf('unit_of_measure')]."_".$row[csf('body_part_id')]."_".$row[csf('detarmination_id')]."_".$stock;?>')" >
							<td width="35" align="center"><? echo $i; ?></td>
							<td width="80" title="<? echo $order_id; ?>"><? echo $row[csf('po_number')]; ?></td>
							<td width="80" title="<? echo $stock_title; ?>"><? echo $row[csf('prod_id')]; ?></td>
							<td width="120"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
							<td width="120"><? echo $buyer_library[$row[csf('buyer_id')]]; ?></td>
							<td width="100"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
							<td width="200" title="<? echo $ref_title;?>"><? echo $prod_data[$row[csf('prod_id')]]["product_name_details"];?></td>
							<td width="90"><? echo $color_arr[$prod_data[$row[csf('prod_id')]]["color"]]; ?></td>
							<td width="90"><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></td>
							<td width="90"><? echo $row[csf('booking_no')]; ?></td>
							<td width="90"><? echo $row[csf('batch_no')]; ?></td>
							<td width="90"><? echo $floor; ?></td>
							<td width="90"><? echo $room; ?></td>
							<td width="90"><? echo $rack_no; ?></td>
							<td width="90"><? echo $shelf_no; ?></td>
							<td title="<? echo $title;?>"><? echo $stock; ?></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<?
	exit();
}

if ($action=="load_body_part")
{
	$data=explode("_",$data);
	$order_id = $data[1];
	$product_id = $data[2];

	$transfer_criteria = $data[3];

	//if from body part drop down
	if($data[4] == 1)
	{
		$id = "cbo_from_body_part";
	}
	else
	{
		$id = "cbo_to_body_part";
	}

	// if body part found
	if($data[0] )
	{
		echo create_drop_down( $id, 160,$body_part,"", 1, "--Select--", $data[0], "change_body_part(this.id)",0,$data[0] );
	}
	else
	{
		$fabric_cond_1=$fabric_cond_2="";
		/*if($transfer_criteria !=2 && $data[4] != 1)
		{
			//for to body part dropdown but not store to store tranfer criteria then fabrication check needed .
			$detarFromProduct =return_library_array("select id, detarmination_id from  product_details_master where id =$product_id","id","detarmination_id");
			$fabric_cond_1 = " and a.lib_yarn_count_deter_id = $detarFromProduct[$product_id] ";
			$fabric_cond_2 = " and b.lib_yarn_count_deter_id = $detarFromProduct[$product_id] ";
		}*/

		$body_part_sql = sql_select("SELECT a.body_part_id from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0  and b.status_active = 1 and b.is_deleted = 0 and b.pre_cost_fabric_cost_dtls_id = a.id and b.po_break_down_id =$order_id $fabric_cond_1 and b.booking_type =1 union all select b.body_part_id from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c where b.job_no=c.job_no and a.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.pre_cost_fabric_cost_dtls_id = a.id and c.po_break_down_id =$order_id $fabric_cond_2 and a.fabric_description = b.id and c.booking_type = 4");

		foreach ($body_part_sql as $row)
		{
			$body_part_arr[$row[csf("body_part_id")]] = $row[csf("body_part_id")];
		}
		$body_part_ids = implode(",",array_filter($body_part_arr));
		if($body_part_ids != "")
		{
			echo create_drop_down( $id, 160,$body_part,"", 1, "--Select--", 0, "change_body_part(this.id)",0,$body_part_ids );
		}else{
			echo create_drop_down( $id, 160,$blank_array,"", 1, "--Select--", 0, "",0,"" );
		}

	}

	exit();
}

if($action=='populate_data_from_product_master')
{
	$data = explode("_",$data);
	$transfer_criteria=$data[19];

	$floor_id = $data[4];
	$room = $data[5];
	$rack = $data[6];
	$self = $data[7];
	$txt_rate = number_format($data[10],2);
	//$data_array=sql_select($sql);

	echo "document.getElementById('txt_from_order_id').value 			= '".$data[0]."';\n";
	echo "document.getElementById('txt_from_order_no').value 			= '".$data[13]."';\n";
	if($transfer_criteria==2)
	{
		echo "document.getElementById('txt_to_order_id').value 			= '".$data[0]."';\n";
		echo "document.getElementById('txt_to_order_no').value 			= '".$data[13]."';\n";

		echo "load_drop_down('requires/grey_transfer_requ_entry_controller',".$data[16]."+'_'+".$data[0]."+'_'+".$data[1]."+'_'+".$transfer_criteria."+'_'+2, 'load_body_part', 'to_body_part' );\n";
	}

	echo "document.getElementById('from_product_id').value 				= '".$data[1]."';\n";

	echo "load_drop_down('requires/grey_transfer_requ_entry_controller',".$data[16]."+'_'+".$data[0]."+'_'+".$data[1]."+'_'+".$transfer_criteria."+'_'+1, 'load_body_part', 'from_body_td' );\n";

	echo "document.getElementById('txt_item_desc').value 				= '".$data[11]."';\n";
	echo "document.getElementById('txt_current_stock').value 			= '".$data[18]."';\n";
	echo "document.getElementById('hidden_current_stock').value 		= '".$data[18]."';\n";
	echo "document.getElementById('batch_id').value 					= '".$data[8]."';\n";
	echo "document.getElementById('txt_batch_no').value 				= '".$data[9]."';\n";


	echo "document.getElementById('txt_rate').value 					= '".$txt_rate."';\n";
	echo "document.getElementById('hide_color_id').value 				= '".$data[12]."';\n";
	echo "document.getElementById('txt_color').value 					= '".$color_arr[$data[12]]."';\n";
	echo "document.getElementById('cbo_uom').value 						= '".$data[15]."';\n";
	echo "document.getElementById('cbo_fabric_shade').value 			= '".$data[14]."';\n";

	if($floor_id !=0)
	{
		echo "load_room_rack_self_bin('requires/grey_transfer_requ_entry_controller*2', 'floor','floor_td', '".$data[2]."','"."','".$data[3]."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 				= '".$floor_id."';\n";
	}
	if($room !=0)
	{
		echo "load_room_rack_self_bin('requires/grey_transfer_requ_entry_controller*2', 'room','room_td', '".$data[2]."','"."','".$data[3]."','".$floor_id."',this.value);\n";
		echo "document.getElementById('cbo_room').value 				= '".$room."';\n";
	}
	if($rack !=0)
	{
		echo "load_room_rack_self_bin('requires/grey_transfer_requ_entry_controller*2', 'rack','rack_td', '".$data[2]."','"."','".$data[3]."','".$floor_id."','".$room."',this.value);\n";
		echo "document.getElementById('txt_rack').value 				= '".$rack."';\n";
	}
	if($self !=0)
	{
		echo "load_room_rack_self_bin('requires/grey_transfer_requ_entry_controller*2', 'shelf','shelf_td', '".$data[2]."','"."','".$data[3]."','".$floor_id."','".$room."','".$rack."',this.value);\n";
		echo "document.getElementById('txt_shelf').value 				= '".$self."';\n";
	}
	exit();
}


if ($action=="po_search_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function fn_show_check()
		{
			if( $('#txt_po_no').val()=='' && $('#txt_job_no').val()=='' && $('#cbo_buyer_name').val()*1==0 && $('#txt_internal_ref').val()==''){
				alert("Please Enter at Least One Search Criteria");return;
			}
			show_list_view ( $('#txt_po_no').val()+'_'+$('#txt_job_no').val()+'_'+<? echo $cbo_company_id_to; ?>+'_'+$('#cbo_buyer_name').val()+'_'+$('#txt_internal_ref').val(), 'create_po_search_list_view', 'search_div', 'grey_transfer_requ_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
		}

		function js_set_value( id,name,booking_no,booking_id)
		{
			$('#hidden_order_id').val(id);
			$('#hidden_order_no').val(name);
			$('#hidden_booking_no').val(booking_no);
			$('#hidden_booking_id').val(booking_id);
			parent.emailwindow.hide();
		}

		function hidden_field_reset()
		{
			$('#hidden_order_id').val('');
			$('#hidden_order_no').val( '' );
			$('#hidden_booking_no').val( '' );
			$('#hidden_booking_id').val( '' );
		}

	</script>

</head>
<body>
	<div align="center">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:620px;margin-left:5px">
				<input type="hidden" name="hidden_order_id" id="hidden_order_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_order_no" id="hidden_order_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">
				<table cellpadding="0" cellspacing="0" width="620" class="rpt_table" border="1" rules="all">
					<thead>
						<th>Buyer</th>
						<th>PO No</th>
						<th>Job No</th>
						<th>Internal Ref. No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="po_id" id="po_id" value="">
						</th>
					</thead>
					<tr class="general">
						<td align="center">
							<?
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id_to' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $buyer_name, "","" );
							?>
						</td>
						<td align="center">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_po_no" id="txt_po_no" placeholder="Write" />
						</td>
						<td align="center">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_job_no" id="txt_job_no"  placeholder="Write"/>
						</td>
						<td align="center">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_internal_ref" id="txt_internal_ref"  placeholder="Write"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check()" style="width:100px;" />
						</td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"  align="center"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}


if($action=="create_po_search_list_view")
{
	$data = explode("_",$data);
	$txt_po_no = trim($data[0]);
	$txt_job_no = trim($data[1]);
	$company_id =$data[2];
	$buyer_id =$data[3];
	$txt_internal_ref =$data[4];

	$search_con="";
	if($buyer_id!=0)
		$search_con = " and a.buyer_name=$buyer_id";

	if($txt_po_no!="")
		$search_con .= " and b.po_number like '%$txt_po_no%'";
	if($txt_job_no!="")
		$search_con .=" and a.job_no like '%$txt_job_no%'";
	if($txt_internal_ref!="")
		$search_con .=" and b.grouping like '%$txt_internal_ref%'";

	$sql = "select a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, b.id,b.grouping as ref_no, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date, c.booking_no , d.id as booking_id
	from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d
	where a.job_no=b.job_no_mst and a.company_name=$company_id $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id = c.po_break_down_id and d.booking_no = c.booking_no and c.status_active =1 and c.is_deleted=0
	group by a.id, a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, b.id,b.grouping, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date,c.booking_no ,d.id
	order by a.id desc";
	?>

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" >
		<thead>
			<th width="40">SL</th>
			<th width="100">Job No</th>
			<th width="140">Style No</th>
			<th width="160">PO No</th>
			<th width="100">Ref. No</th>
			<th width="100">Booking No</th>
			<th width="">UOM</th>
		</thead>
	</table>
	<div style="width:750px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="tbl_list_search" >
			<?
			$i=1; $po_row_id='';
			$nameArray=sql_select( $sql );
			foreach ($nameArray as $selectResult)
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				if(in_array($selectResult[csf('id')],$hidden_po_id))
				{
					if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $selectResult[csf('id')];?>','<? echo $selectResult[csf('po_number')];?>','<? echo $selectResult[csf('booking_no')]?>','<? echo $selectResult[csf('booking_id')];?>')">
					<td width="40" align="center"><?php echo "$i"; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
					<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $selectResult[csf('po_number')]; ?>"/>
					<input type="hidden" name="txt_buyer" id="txt_buyer<?php echo $i ?>" value="<? echo $selectResult[csf('buyer_name')]; ?>"/>
					<input type="hidden" name="txt_styleRef" id="txt_styleRef<?php echo $i ?>" value="<? echo $selectResult[csf('style_ref_no')]; ?>"/>
				</td>
				<td width="100"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
				<td width="140"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
				<td width="160"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
				<td width="100"><p><? echo $selectResult[csf('ref_no')]; ?></p></td>
				<td width="100"><p><? echo $selectResult[csf('booking_no')]; ?></p></td>
				<td width="" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>
		<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<?php echo $po_row_id; ?>"/>
	</table>
</div>
<?
exit();
}





if($action=="show_transfer_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=2","id","product_name_details");

	$sql="select id, from_store, to_store, from_prod_id, color_id,sum(transfer_qnty) transfer_qnty from inv_item_transfer_requ_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0' and active_dtls_id_in_transfer=1 group by id, from_store, to_store, from_prod_id, color_id";

	$arr=array(0=>$store_arr,1=>$store_arr,2=>$product_arr,4=>$color_arr);

	echo  create_list_view("list_view", "From Store,To Store,Item Description,Transfered Qnty,Color", "130,130,280,130,110","880","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "from_store,to_store,from_prod_id,0,color_id", $arr, "from_store,to_store,from_prod_id,transfer_qnty,color_id", "requires/grey_transfer_requ_entry_controller",'','0,0,0,2,0');

	exit();
}

if($action=='populate_transfer_details_form_data')
{
	$data_array=sql_select("select a.transfer_criteria, a.company_id, a.to_company, a.location_id, a.to_location_id, b.from_order_id, b.to_order_id, b.id, b.mst_id, b.from_store, b.to_store, b.floor_id, b.room, b.rack, b.shelf, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.color_id, b.batch_id,b.remarks,b.fabric_shade, b.uom, b.to_batch_id, b.body_part_id, b.to_body_part, b.no_of_roll, b.to_ord_book_id, b.to_ord_book_no, b.trans_id, b.to_trans_id from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b where b.id='$data' and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");

	$order_no=explode(",",trim($data_array[0][csf('from_order_id')].",".$data_array[0][csf('to_order_id')],"  , "));

	$order_name_arr=return_library_array( "select id ,po_number from wo_po_break_down where status_active=1 and is_deleted =0 and id in(".implode(",",$order_no).")",'id','po_number');
	//=====================================================================================

	$sql = " select floor_id,order_id, fabric_shade, body_part_id, room, rack_no, shelf_no, prod_id, batch_id, sum(qnty) as qnty
	from
	(
		select  c.floor_id, b.fabric_shade, b.body_part_id, (case when c.room is null or c.room=0 then 0 else c.room end) room,(case when c.rack is null or c.rack=0 then 0 else c.rack end) rack_no,(case when c.self is null or c.self=0 then 0 else c.self end) shelf_no,sum(d.quantity) as qnty,c.prod_id, d.po_breakdown_id as order_id, c.pi_wo_batch_no as batch_id
		from product_details_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details d, inv_transaction c, pro_batch_create_mst e, wo_po_break_down f, wo_po_details_master g
		where d.entry_form in (7,37) and a.id=b.prod_id and b.trans_id=c.id  and c.company_id=".$data_array[0][csf('company_id')]." and c.store_id = ".$data_array[0][csf('from_store')]." and c.prod_id = ".$data_array[0][csf('from_prod_id')]."  and a.item_category_id=2 and c.item_category=2 and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.trans_id = d.trans_id  and d.trans_id = c.id and d.po_breakdown_id = f.id and f.job_no_mst = g.job_no and c.pi_wo_batch_no = e.id and b.body_part_id > 0 and c.pi_wo_batch_no = " .$data_array[0][csf('batch_id')]."
		group by c.floor_id, b.fabric_shade, b.body_part_id, c.room, c.rack, c.self, c.prod_id, d.po_breakdown_id, c.pi_wo_batch_no
		union all
		select c.floor_id, b.fabric_shade, c.body_part_id, (case when c.room is null or c.room=0 then 0 else c.room end) room,(case when c.rack is null or c.rack=0 then 0 else c.rack end) rack_no,(case when c.self is null or c.self=0 then 0 else c.self end) shelf_no, sum(d.quantity) as qnty, c.prod_id, d.po_breakdown_id as order_id, c.pi_wo_batch_no as batch_id
		from product_details_master a, inv_item_transfer_requ_dtls b, inv_transaction c, order_wise_pro_details d, pro_batch_create_mst e, wo_po_break_down f, wo_po_details_master g
		where a.id = b.to_prod_id and b.to_trans_id = c.id   and c.transaction_type = 5 and c.item_category = 2  and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and a.status_active=1 and a.is_deleted=0 and c.company_id = ".$data_array[0][csf('company_id')]." and c.store_id = ".$data_array[0][csf('from_store')]." and c.prod_id = ".$data_array[0][csf('from_prod_id')]." and d.entry_form in (14,15,306) and d.trans_type = 5 and c.id = d.trans_id and c.pi_wo_batch_no = e.id and d.po_breakdown_id = f.id and f.job_no_mst = g.job_no and d.status_active =1 and f.status_active =1 and c.body_part_id > 0 and c.pi_wo_batch_no = " .$data_array[0][csf('batch_id')]."
		group by c.floor_id, b.fabric_shade, c.body_part_id, c.room,c.rack,c.self,c.prod_id, d.po_breakdown_id, c.pi_wo_batch_no
	)
	group by floor_id,order_id, fabric_shade, body_part_id, room, rack_no, shelf_no, prod_id, batch_id";


	$result = sql_select($sql);
	$order_data_array=array();
	$floor_id=$room=$rack=$self="";
	foreach($result as $row )
	{
		$prodidArr[$row[csf('prod_id')]]=$row[csf('prod_id')];


		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack_no')]=="")?0:$row[csf('rack_no')];
		$self = ($row[csf('shelf_no')]=="")?0:$row[csf('shelf_no')];

		$receive_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$floor_id][$room][$rack][$self][$row[csf('order_id')]][$row[csf('body_part_id')]]+=$row[csf('qnty')];
	}


	$prodidArr = array_filter($prodidArr);
	if(!empty($prodidArr))
	{
		$all_prod_id_cond_1 = $prodCond_1="";
		$all_prod_id_cond_2 = $prodCond_2="";
		$all_prod_ids = implode(",", $prodidArr);
		if($db_type==2 && count($prodidArr)>999)
		{
			$prodidArr_chunk=array_chunk($prodidArr,999) ;
			foreach($prodidArr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$prodCond_1.="  a.prod_id in($chunk_arr_value) or ";
				$prodCond_2.="  id in($chunk_arr_value) or ";
			}

			$all_prod_id_cond_1.=" and (".chop($prodCond_1,'or ').")";
			$all_prod_id_cond_2.=" and (".chop($prodCond_2,'or ').")";
		}
		else
		{
			$all_prod_id_cond_1=" and a.prod_id in($all_prod_ids)";
			$all_prod_id_cond_2=" and id in($all_prod_ids)";
		}
	}

	$issue_qty_array=array();
	$issData = sql_select("SELECT a.prod_id, a.pi_wo_batch_no as batch_id,a.fabric_shade, a.floor_id, d.po_breakdown_id, b.body_part_id, a.rack, a.room,  a.self, sum(d.quantity) as issue_qnty FROM inv_finish_fabric_issue_dtls b, inv_transaction a, order_wise_pro_details d WHERE d.entry_form=18 and b.trans_id=a.id and a.id=d.trans_id and a.item_category=2 and a.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=".$data_array[0][csf('company_id')]." and a.store_id =".$data_array[0][csf('from_store')]." and b.trans_id = d.trans_id $all_prod_id_cond_1  GROUP BY a.prod_id, a.pi_wo_batch_no,a.fabric_shade, a.floor_id, d.po_breakdown_id, b.body_part_id, a.rack, a.room, a.self ");


	$floor_id=$room=$rack=$self="";
	foreach($issData as $row)
	{
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$issue_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$floor_id][$room][$rack][$self][$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]]+=$row[csf('issue_qnty')];
	}

	$recvRt_qty_array=array(); $issRt_qty_array=array();
	$receiveReturnData=sql_select("SELECT a.prod_id, a.pi_wo_batch_no, a.fabric_shade, a.floor_id,a.room, a.rack, a.self, sum( b.quantity ) as return_qnty , b.po_breakdown_id, a.body_part_id FROM inv_transaction a, order_wise_pro_details b WHERE a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type =3 and b.status_active=1 and b.is_deleted=0 and a.company_id=".$data_array[0][csf('company_id')]." and a.store_id =".$data_array[0][csf('from_store')]." and b.entry_form =46 $all_prod_id_cond_1 GROUP BY a.prod_id, a.fabric_shade,a.pi_wo_batch_no, a.floor_id,a.room, a.rack, a.self, b.po_breakdown_id, a.body_part_id");


	$floor_id=$room=$rack=$self="";
	foreach($receiveReturnData as $row)
	{
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$recvRt_qty_array[$row[csf('prod_id')]][$row[csf('pi_wo_batch_no')]][$row[csf('fabric_shade')]][$floor_id][$room][$rack][$self][$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]] +=$row[csf('return_qnty')];
	}

	$issueReturnData=sql_select("SELECT a.prod_id, a.pi_wo_batch_no,c.body_part_id, b.po_breakdown_id, a.fabric_shade, a.floor_id,a.room, a.rack, a.self, sum(b.quantity) as issrqnty FROM inv_transaction a, order_wise_pro_details b, pro_finish_fabric_rcv_dtls c WHERE a.id=b.trans_id and a.id = c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type =4 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=".$data_array[0][csf('company_id')]." and a.store_id =".$data_array[0][csf('from_store')]." $all_prod_id_cond_1 GROUP BY a.prod_id, a.fabric_shade,c.body_part_id,a.pi_wo_batch_no, b.po_breakdown_id,a.floor_id,a.room, a.rack, a.self");


	$floor_id=$room=$rack=$self="";
	foreach($issueReturnData as $row)
	{
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$issRt_qty_array[$row[csf('prod_id')]][$row[csf('pi_wo_batch_no')]][$row[csf('fabric_shade')]][$floor_id][$room][$rack][$self][$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]] +=$row[csf('issrqnty')];
	}


	$transOutData = sql_select("select a.pi_wo_batch_no, b.po_breakdown_id,a.prod_id, c.body_part_id, a.fabric_shade, a.floor_id ,  a.room, a.rack , a.self, sum(b.quantity) as trans_out_qnty from  inv_transaction a, order_wise_pro_details b, inv_item_transfer_dtls c where  a.id = b.trans_id and a.id = c.trans_id and b.trans_id=c.trans_id and b.trans_type=6 and a.company_id = ".$data_array[0][csf('company_id')]." and a.store_id =".$data_array[0][csf('from_store')]." and a.transaction_type = 6 and a.item_category = 2 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and a.pi_wo_batch_no> 0 $all_prod_id_cond_1 group by a.pi_wo_batch_no,b.po_breakdown_id, a.prod_id, c.body_part_id, a.fabric_shade, a.floor_id,  a.room,a.rack,a.self");


	$floor_id=$room=$rack=$self="";
	foreach($transOutData as $row)
	{
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$trans_out_qnty_array[$row[csf('prod_id')]][$row[csf('pi_wo_batch_no')]][$row[csf('fabric_shade')]][$floor_id][$room][$rack][$self][$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]] +=$row[csf('trans_out_qnty')];
	}



	//=============================================================================================

	//echo "<pe>";
	//print_r($stock_data_arrray); die;

	foreach ($data_array as $row)
	{
		if ($row[csf("transfer_criteria")]==1) {
			$company_id=$row[csf("to_company")];
		}
		else
		{
			$company_id=$row[csf("company_id")];
		}
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";



		if($row[csf("from_store")]>0){
			echo "load_room_rack_self_bin('requires/grey_transfer_requ_entry_controller*2', 'store','from_store_td', $('#cbo_company_id').val(),'"."',this.value);\n";
			echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store")]."';\n";
		}
		if($row[csf("floor_id")]>0){
			echo "load_room_rack_self_bin('requires/grey_transfer_requ_entry_controller*2', 'floor','floor_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."',this.value);\n";
			echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";
		}
		if($row[csf("room")]>0){
			echo "load_room_rack_self_bin('requires/grey_transfer_requ_entry_controller*2', 'room','room_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."',this.value);\n";
			echo "document.getElementById('cbo_room').value 				= '".$row[csf("room")]."';\n";
		}
		if($row[csf("rack")]>0){
			echo "load_room_rack_self_bin('requires/grey_transfer_requ_entry_controller*2', 'rack','rack_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
			echo "document.getElementById('txt_rack').value 				= '".$row[csf("rack")]."';\n";
		}
		if($row[csf("shelf")]>0){
			echo "load_room_rack_self_bin('requires/grey_transfer_requ_entry_controller*2', 'shelf','shelf_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
			echo "document.getElementById('txt_shelf').value 				= '".$row[csf("shelf")]."';\n";
		}

		if($row[csf("to_store")]>0){
			echo "load_room_rack_self_bin('requires/grey_transfer_requ_entry_controller*2*cbo_store_name_to', 'store','to_store_td', '".$company_id."','".$row[csf("to_location_id")]."',this.value);\n";
			echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store")]."';\n";
			echo "document.getElementById('previous_to_store').value 				= '".$row[csf("to_store")]."';\n";
		}
		if($row[csf("to_floor_id")]>0){
			echo "load_room_rack_self_bin('requires/grey_transfer_requ_entry_controller*2*cbo_floor_to', 'floor','floor_td_to', '".$company_id."','"."','".$row[csf('to_store')]."',this.value);\n";
			echo "document.getElementById('cbo_floor_to').value 			= '".$row[csf("to_floor_id")]."';\n";
		}
		if($row[csf("to_room")]>0){
			echo "load_room_rack_self_bin('requires/grey_transfer_requ_entry_controller*2*cbo_room_to', 'room','room_td_to', '".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."',this.value);\n";
			echo "document.getElementById('cbo_room_to').value 				= '".$row[csf("to_room")]."';\n";
		}
		if($row[csf("to_rack")]>0){
			echo "load_room_rack_self_bin('requires/grey_transfer_requ_entry_controller*2*txt_rack_to', 'rack','rack_td_to','".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."',this.value);\n";
			echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		}
		if($row[csf("to_shelf")]>0){
			echo "load_room_rack_self_bin('requires/grey_transfer_requ_entry_controller*2*txt_shelf_to', 'shelf','shelf_td_to','".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."',this.value);\n";
			echo "document.getElementById('txt_shelf_to').value 			= '".$row[csf("to_shelf")]."';\n";
		}

		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hidden_transfer_qnty').value 		= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hide_color_id').value 				= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('cbo_fabric_shade').value 			= '".$row[csf("fabric_shade")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";

		echo "document.getElementById('batch_id').value 					= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('previous_from_batch_id').value 		= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('previous_to_batch_id').value 		= '".$row[csf("to_batch_id")]."';\n";

		$batch_no = return_field_value("batch_no", "pro_batch_create_mst", "id=".$row[csf("batch_id")]);

		echo "document.getElementById('txt_batch_no').value 				= '".$batch_no."';\n";
		echo "document.getElementById('txt_from_order_no').value 			= '".$order_name_arr[$row[csf("from_order_id")]]."';\n";
		echo "document.getElementById('txt_from_order_id').value 			= '".$row[csf("from_order_id")]."';\n";
		echo "document.getElementById('from_product_id').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_from_prod_id').value 		= '".$row[csf("from_prod_id")]."';\n";

		echo "document.getElementById('txt_to_order_no').value 				= '".$order_name_arr[$row[csf("to_order_id")]]."';\n";
		echo "document.getElementById('txt_to_order_id').value 				= '".$row[csf("to_order_id")]."';\n";
		echo "document.getElementById('previous_to_order_id').value 		= '".$row[csf("to_order_id")]."';\n";
		echo "document.getElementById('previous_to_prod_id').value 			= '".$row[csf("to_prod_id")]."';\n";

		echo "document.getElementById('hdn_to_booking_id').value 			= '".$row[csf("to_ord_book_id")]."';\n";
		echo "document.getElementById('hdn_to_booking_no').value 			= '".$row[csf("to_ord_book_no")]."';\n";

		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";

		echo "document.getElementById('txt_rate').value 					= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_transfer_value').value 			= '".$row[csf("transfer_value")]."';\n";

		$sql=sql_select("select product_name_details,current_stock,avg_rate_per_unit from product_details_master where id=".$row[csf('from_prod_id')]);


		echo "load_drop_down('requires/grey_transfer_requ_entry_controller', 0+'_'+".$row[csf('to_order_id')]."+'_'+".$row[csf("from_prod_id")]."+'_' +".$row[csf("transfer_criteria")]." +'_'+2, 'load_body_part', 'to_body_part' );";

		echo "document.getElementById('cbo_to_body_part').value 			= '".$row[csf("to_body_part")]."';\n";

		echo "load_drop_down('requires/grey_transfer_requ_entry_controller',".$row[csf('body_part_id')]."+'_'+".$row[csf("from_order_id")]."+'_'+".$row[csf("from_prod_id")]."+'_'+".$row[csf("transfer_criteria")]."+'_'+1, 'load_body_part', 'from_body_td' );\n";

		echo "document.getElementById('txt_no_of_roll').value 			= '".$row[csf("no_of_roll")]."';\n";



		//===========================================================================================
		if($row[csf('floor_id')] =="") $floor_id = "0"; else $floor_id = $row[csf('floor_id')];
		if($row[csf('room')] =="") $room_id = "0"; else $room_id = $row[csf('room')];
		if($row[csf('rack')] =="") $rack_id = "0"; else $rack_id = $row[csf('rack')];
		if($row[csf('shelf')] =="") $self_id = "0"; else $self_id = $row[csf('shelf')];

		$receive_qty = $receive_qty_array[$row[csf('from_prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$floor_id][$room_id][$rack_id][$self_id][$row[csf("from_order_id")]][$row[csf('body_part_id')]];

		$issRt_qty = $issRt_qty_array[$row[csf('from_prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$floor_id][$room_id][$rack_id][$self_id][$row[csf("from_order_id")]][$row[csf('body_part_id')]];

		$recvRt_qty = $recvRt_qty_array[$row[csf('from_prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$floor_id][$room_id][$rack_id][$self_id][$row[csf("from_order_id")]][$row[csf('body_part_id')]];

		$issue_qty = $issue_qty_array[$row[csf('from_prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$floor_id][$room_id][$rack_id][$self_id][$row[csf("from_order_id")]][$row[csf('body_part_id')]];

		$trans_out_qnty = $trans_out_qnty_array[$row[csf('from_prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$floor_id][$room_id][$rack_id][$self_id][$row[csf("from_order_id")]][$row[csf('body_part_id')]];

		$stockQty = $receive_qty + $issRt_qty - ($recvRt_qty + $issue_qty + $trans_out_qnty);


		/*echo $row[csf('from_prod_id')]." ,batch= ".$row[csf('batch_id')].", shade=".$row[csf('fabric_shade')].", floor=".$floor_id.", room=".$room_id .",rack =".$rack_id.", shelf".$self_id .",order=".$row[csf("from_order_id")].", bodypart=".$row[csf('body_part_id')];die;*/


		//echo "rcv = $receive_qty , iss return= $issRt_qty , rcv return = $recvRt_qty ,issue = $issue_qty ,tr out= $trans_out_qnty";die;

		//=============================================================================


		//$stockQty = ($stock_data_arrray[$row[csf("from_order_id")]][$row[csf("from_prod_id")]][$row[csf("batch_id")]]['rcv_qty'] - $stock_data_arrray[$row[csf("from_order_id")]][$row[csf("from_prod_id")]][$row[csf("batch_id")]]['issue_qty'])+$row[csf("transfer_qnty")];


		echo "document.getElementById('txt_item_desc').value 				= '".$sql[0][csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$stockQty."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$stockQty."';\n";

		//$sql_trans=sql_select("select id, transaction_type from inv_transaction where mst_id=".$row[csf('mst_id')]." and prod_id='".$row[csf("from_prod_id")]."' and item_category=2 and transaction_type in(5,6) order by id asc");

		echo "document.getElementById('update_trans_issue_id').value 		= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$row[csf("to_trans_id")]."';\n";
		//echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "disable_enable_fields('cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf',1);\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n";

		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($db_type==0)	{ mysql_query("BEGIN"); }

	$company_id_to=str_replace("'","",$cbo_company_id_to);
	$cbo_location_to=str_replace("'","",$cbo_location_to);

	if(str_replace("'","",$update_id)!="")
	{

		$is_acknowledge = return_field_value("b.id id", "inv_item_transfer_requ_mst a,inv_item_trans_acknowledgement b", "a.id=b.challan_id and  a.id=$update_id and a.status_active=1 and a.is_acknowledge=1", "id");
		if($is_acknowledge != "" )
		{
			echo "20**Update not allowed. This Transfer Challan is already Acknowledged.\nAcknowledge System ID = $is_acknowledge";
			die;
		}

		$up_cond="";
		if(str_replace("'","",$update_trans_issue_id)!="")
		{
			$all_trans_id=str_replace("'","",$update_trans_issue_id);
			$up_cond=" and id not in($all_trans_id)";
		}

		$duplicate_product_check=return_field_value("id", "inv_transaction", " status_active=1 and transaction_type in(6) and item_category =$cbo_item_category and prod_id=$from_product_id and pi_wo_batch_no=$batch_id and mst_id=$update_id $up_cond", "id");
		if($duplicate_product_check)
		{
			echo "20**Duplicate Item Not Allow Within Same MRR";die;
		}
	}


	$up_trans_id=str_replace("'","",$update_trans_issue_id);
	if(str_replace("'","",$update_trans_recv_id)!="") $up_trans_id.=",".str_replace("'","",$update_trans_recv_id);
	$up_cond="";$up_cond_2="";
	if(str_replace("'","",$update_trans_issue_id)!="") $up_cond=" and a.id not in($up_trans_id)";
	if(str_replace("'","",$update_trans_issue_id)!="") $up_cond_2=" and c.id not in($up_trans_id)";


	$cbo_floor = (str_replace("'", "", $cbo_floor) =="")? 0 :str_replace("'", "", $cbo_floor);
	$cbo_room = (str_replace("'", "", $cbo_room)=="")? 0 :str_replace("'", "", $cbo_room);
	$txt_rack = (str_replace("'", "", $txt_rack)=="")? 0 :str_replace("'", "", $txt_rack);
	$txt_shelf = (str_replace("'", "", $txt_shelf)=="")? 0 :str_replace("'", "", $txt_shelf);

	$cbo_floor_to = (str_replace("'", "", $cbo_floor_to) =="")? 0 :str_replace("'", "", $cbo_floor_to);
	$cbo_room_to = (str_replace("'", "", $cbo_room_to)=="")? 0 :str_replace("'", "", $cbo_room_to);
	$txt_rack_to = (str_replace("'", "", $txt_rack_to)=="")? 0 :str_replace("'", "", $txt_rack_to);
	$txt_shelf_to = (str_replace("'", "", $txt_shelf_to)=="")? 0 :str_replace("'", "", $txt_shelf_to);

	//==============================================================================================

	$rcv_trans_in = sql_select("select sum(qnty) as qnty
		from
		(
		select sum(d.quantity) as qnty
		from product_details_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details d, inv_transaction c, pro_batch_create_mst e
		where d.entry_form in (7,37) and a.id=b.prod_id and b.trans_id=c.id  and c.company_id=$cbo_company_id and c.store_id = $cbo_store_name $sql_cond and a.item_category_id=2 and c.item_category=2 and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.trans_id = d.trans_id  and d.trans_id = c.id and c.pi_wo_batch_no = e.id and c.floor_id = $cbo_floor and c.rack = $txt_rack and c.self = $txt_shelf and c.room = $cbo_room and b.body_part_id = $cbo_from_body_part and d.po_breakdown_id=$txt_from_order_id and c.pi_wo_batch_no = $batch_id and a.id = $from_product_id and b.fabric_shade = $cbo_fabric_shade $up_cond_2
		union all
		select sum(d.quantity) as qnty
		from product_details_master a, inv_item_transfer_requ_dtls b, inv_transaction c, order_wise_pro_details d, pro_batch_create_mst e
		where a.id = b.to_prod_id and b.to_trans_id = c.id   and c.transaction_type = 5 and c.item_category = 2  and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and a.status_active=1 and a.is_deleted=0 and c.company_id = $cbo_company_id and c.store_id = $cbo_store_name $sql_cond and d.entry_form in (14,15,306) and d.trans_type = 5 and c.id = d.trans_id and c.pi_wo_batch_no = e.id and d.status_active =1  and c.floor_id = $cbo_floor and c.rack = $txt_rack and c.self = $txt_shelf and c.room = $cbo_room and b.body_part_id = $cbo_from_body_part and d.po_breakdown_id=$txt_from_order_id and c.pi_wo_batch_no = $batch_id and a.id = $from_product_id and b.fabric_shade = $cbo_fabric_shade $up_cond_2
	)");
	$rcv_trans_in_qnty = $rcv_trans_in[0][csf("qnty")];


	$issData = sql_select("SELECT sum(d.quantity) as issue_qnty FROM inv_finish_fabric_issue_dtls b, inv_transaction a, order_wise_pro_details d WHERE d.entry_form=18 and b.trans_id=a.id and a.id=d.trans_id and a.item_category=2 and a.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$cbo_company_id and a.store_id =$cbo_store_name and a.prod_id=$from_product_id and d.po_breakdown_id=$txt_from_order_id and a.pi_wo_batch_no=$batch_id and b.trans_id = d.trans_id and a.floor_id =$cbo_floor and a.rack=$txt_rack and a.self = $txt_shelf and a.room=$cbo_room and b.body_part_id=$cbo_from_body_part and a.fabric_shade = $cbo_fabric_shade	$up_cond");
	$issue_qnty = $issData[0][csf("issue_qnty")];

	$recvRt_qty_array=array(); $issRt_qty_array=array();
	$receiveReturnData=sql_select("SELECT sum( b.quantity) as return_qnty FROM inv_transaction a, order_wise_pro_details b WHERE a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type =3 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_id and a.store_id =$cbo_store_name and b.entry_form =46 and a.prod_id=$from_product_id and b.po_breakdown_id=$txt_from_order_id and a.pi_wo_batch_no=$batch_id and a.floor_id =$cbo_floor and a.rack=$txt_rack and a.self = $txt_shelf and a.room=$cbo_room and a.body_part_id = $cbo_from_body_part and a.fabric_shade = $cbo_fabric_shade $up_cond");
	$recvRt_qty = $receiveReturnData[0][csf("return_qnty")];

	$issueReturnData=sql_select("SELECT sum(b.quantity) as issrqnty FROM inv_transaction a, order_wise_pro_details b, pro_finish_fabric_rcv_dtls c WHERE a.id=b.trans_id and a.id = c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type =4 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_id and a.store_id =$cbo_store_name and a.prod_id=$from_product_id and b.po_breakdown_id=$txt_from_order_id and a.pi_wo_batch_no=$batch_id and a.floor_id =$cbo_floor and a.rack=$txt_rack and a.self = $txt_shelf and a.room=$cbo_room and c.body_part_id=$cbo_from_body_part and a.fabric_shade = $cbo_fabric_shade $up_cond");
	$issRt_qty = $issueReturnData[0][csf("issrqnty")];

	$transOutData = sql_select("select sum(b.quantity) as trans_out_qnty from  inv_transaction a, order_wise_pro_details b, inv_item_transfer_requ_dtls c where  a.id = b.trans_id and a.id = c.trans_id and b.trans_id=c.trans_id and b.trans_type=6 and a.company_id = $cbo_company_id and a.store_id =$cbo_store_name and a.prod_id=$from_product_id and b.po_breakdown_id=$txt_from_order_id and a.pi_wo_batch_no=$batch_id and a.floor_id =$cbo_floor and a.rack=$txt_rack and a.self = $txt_shelf and a.room=$cbo_room and c.to_body_part = $cbo_from_body_part and a.fabric_shade = $cbo_fabric_shade and a.transaction_type = 6 and a.item_category = 2 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and a.pi_wo_batch_no> 0 $up_cond");
	$trans_out_qnty = $transOutData[0][csf("trans_out_qnty")];

	$trans_qnty=str_replace("'","",$txt_transfer_qnty)*1;
	$stock_qnty=($rcv_trans_in_qnty + $issRt_qty) - ($issue_qnty + $trans_out_qnty + $recvRt_qty);
	//echo "10**($rcv_trans_in_qnty + $issRt_qty) - ($issue_qnty + $trans_out_qnty + $recvRt_qty)";die;
	if($trans_qnty>$stock_qnty)
	{
		echo "20**Transfer quantity is not available in this Store.\nAvailable=$stock_qnty";
		die;
	}

	//==========================================================================================================

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		$transfer_recv_num=''; $transfer_update_id='';
		$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and variable_list= 27", "auto_transfer_rcv");

		//echo "10**".$variable_auto_rcv; die;
		if($variable_auto_rcv == "")
		{
			$variable_auto_rcv = 1;
		}

		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later

			$id = return_next_id_by_sequence("ITEM_TRAN_REQU_MST_PK_SEQ", "inv_item_transfer_requ_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("ITEM_TRAN_REQU_MST_PK_SEQ", "inv_item_transfer_requ_mst",$con,1,$cbo_company_id,"FFRE",14,date("Y",time()),$cbo_item_category ));

			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, location_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company,to_location_id, from_order_id, to_order_id, item_category, ready_to_approve, inserted_by, insert_date";

			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$cbo_location.",".$txt_challan_no.",".$txt_transfer_date.",14,".$cbo_transfer_criteria.",".$company_id_to.",".$cbo_location_to.",".$txt_from_order_id.",".$txt_to_order_id.",".$cbo_item_category.",".$cbo_ready_to_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


			//echo "10**insert into inv_item_transfer_requ_mst (".$field_array.") values ".$data_array;die;
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*to_company*to_location_id*ready_to_approve*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$company_id_to."*".$cbo_location_to."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			/*$rID=sql_update("inv_item_transfer_requ_mst",$field_array_update,$data_array_update,"id",$update_id,1);*/

			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}


		//$id_dtls=return_next_id( "id", "inv_item_transfer_requ_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, from_prod_id, batch_id, yarn_lot, brand_id, color_id, item_group, from_store, floor_id, room, rack, shelf, to_store, to_floor_id, to_room, to_rack, to_shelf, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id,remarks, fabric_shade, body_part_id, to_body_part, no_of_roll, to_ord_book_id, to_ord_book_no,active_dtls_id_in_transfer";


		if(str_replace("'","",$cbo_transfer_criteria)==1) //Company to Company Transfer
		{
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value,detarmination_id,dia_width,gsm from product_details_master where id=$from_product_id");

			$supplier_id=$data_prod[0][csf('supplier_id')];// and supplier_id='$supplier_id'
			$product_id=0;


			if ($data_prod[0][csf('dia_width')]=="")
			{
				if($db_type == 0){
					$dia_cond = " and dia_width = '' ";
				}else{
					$dia_cond = " and dia_width is null ";
				}
			}
			else
			{
				$dia_cond = " and dia_width = '".$data_prod[0][csf('dia_width')]."'";
			}


			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$company_id_to and item_category_id=$cbo_item_category and detarmination_id=".$data_prod[0][csf('detarmination_id')]." $dia_cond and gsm = ".$data_prod[0][csf('gsm')]." and color=$hide_color_id and status_active=1 and is_deleted=0");



			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
			}

			//----------------Check Last Receive Date for Transfer Out----------------
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$from_product_id and transaction_type in (1,4,5) and store_id = $cbo_store_name  and status_active = 1", "max_date");
			if($max_recv_date !="")
			{
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_recv_date)
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Item";
					disconnect($con);
					die;
				}
			}

			//----------------Check Last Issue Date for Transfer In----------------
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id = $cbo_store_name_to  and status_active = 1", "max_date");
			if($max_issue_date !="")
			{
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date)
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
					disconnect($con);
					die;
				}
			}

			//.",".$txt_from_order_id.",".$txt_to_order_id
			$id_dtls = return_next_id_by_sequence("ITEM_TRAN_REQU_DTLS_PK_SEQ", "inv_item_transfer_requ_dtls", $con);
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_from_order_id.",".$txt_to_order_id.",".$txt_remarks.",".$cbo_fabric_shade.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$txt_no_of_roll.",".$hdn_to_booking_id.",".$hdn_to_booking_no.",1)";


		}
		else //Store To Store and order to order Transfer
		{
			//----------------Check Last Receive Date for Transfer Out----------------
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$from_product_id and transaction_type in (1,4,5) and store_id = $cbo_store_name  and status_active = 1", "max_date");
			if($max_recv_date !="")
			{
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_recv_date)
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Item";
					disconnect($con);
					die;
				}
			}

			 //----------------Check Last Issue Date for Transfer In----------------
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$from_product_id and store_id = $cbo_store_name_to  and status_active = 1", "max_date");
			if($max_issue_date !="")
			{
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date)
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Item";
					disconnect($con);
					die;
				}
			}

	

			$id_dtls = return_next_id_by_sequence("ITEM_TRAN_REQU_DTLS_PK_SEQ", "inv_item_transfer_requ_dtls", $con);
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_from_order_id.",".$txt_to_order_id.",".$txt_remarks.",".$cbo_fabric_shade.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$txt_no_of_roll.",".$hdn_to_booking_id.",".$hdn_to_booking_no.",1)";
			
		}


		if(str_replace("'","",$update_id)=="")
		{
			//echo "10**insert into inv_item_transfer_requ_mst (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("inv_item_transfer_requ_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_item_transfer_requ_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0;
		}


		$rID=$rID3=true;


		// echo "10**insert into inv_item_transfer_requ_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;

		$rID3=sql_insert("inv_item_transfer_requ_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}

		// echo "10**".$flag."**".$rID."**".$rID3."**".$variable_auto_rcv; die;

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
		$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and variable_list= 27", "auto_transfer_rcv");

		if($variable_auto_rcv == "")
		{
			$variable_auto_rcv = 1;
		}

		$is_rcv_exist = return_field_value("to_trans_id", "inv_item_transfer_requ_dtls", " id =$update_dtls_id and status_active=1", "to_trans_id");

		if($variable_auto_rcv != 2 )
		{
			// Check to order/store to validate their balance
			$trans_qnty=str_replace("'","",$txt_transfer_qnty)*1;


			$issue_check = sql_select("select sum(issue_qnty) as issue_qnty from inv_mrr_wise_issue_details  where recv_trans_id = $update_trans_recv_id and status_active = 1 ");

			//if($issue_check[0][csf("issue_qnty")] > $trans_qnty)
			if($issue_check[0][csf("issue_qnty")] > 0)
			{
				$check_to_ord_stock = ($hidden_transfer_qnty - $issue_check[0][csf("issue_qnty")]);

				echo "20**Next transaction found.\nTransfered Fabric already used = ".$issue_check[0][csf("issue_qnty")]." \nUpdate Not Allowed";
				disconnect($con);die;
			}
		}


		$field_array_update="challan_no*transfer_date*to_company*to_location_id*to_order_id*ready_to_approve*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$company_id_to."*".$cbo_location_to."*".$txt_to_order_id."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";



		$updateTransID_array=array();
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id);

		$field_array_dtls="from_prod_id*batch_id*color_id*from_store*floor_id*room*rack*shelf*to_store*to_floor_id*to_room*to_rack*to_shelf*transfer_qnty*rate*transfer_value*uom*updated_by*update_date*from_order_id*to_order_id*remarks*fabric_shade*body_part_id*to_body_part*no_of_roll*to_ord_book_id*to_ord_book_no";



		$prod=true;
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			//----------------Check Last Receive Date for Transfer Out----------------
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$from_product_id and store_id = $cbo_store_name and id <> $update_trans_recv_id  and status_active = 1", "max_date");
			if($max_recv_date !="")
			{
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
				if ($transfer_date < $max_recv_date)
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
					disconnect($con);
					die;
				}
			}

			//----------------Check Last Issue Date for Transfer In----------------
			$product_id = str_replace("'", "", $previous_to_prod_id);
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id = $cbo_store_name_to and id not in ($update_trans_recv_id , $update_trans_issue_id )  and status_active = 1", "max_date");
			if($max_issue_date !="")
			{
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date)
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
					disconnect($con);
					die;
				}
			}


			$data_array_dtls=$from_product_id."*".$batch_id."*".$hide_color_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_from_order_id."*".$txt_to_order_id."*".$txt_remarks."*".$cbo_fabric_shade."*".$cbo_from_body_part."*".$cbo_to_body_part."*".$txt_no_of_roll."*".$hdn_to_booking_id."*".$hdn_to_booking_no."";
			//echo "10**string";
		}
		else
		{
			$data_array_dtls=$from_product_id."*".$batch_id."*".$hide_color_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_from_order_id."*".$txt_to_order_id."*".$txt_remarks."*".$cbo_fabric_shade."*".$cbo_from_body_part."*".$cbo_to_body_part."*".$txt_no_of_roll."*".$hdn_to_booking_id."*".$hdn_to_booking_no."";

		}

		//=============================================================================

		//echo "10**fail";die;

		$rID=$rID3=true;

		$rID=sql_update("inv_item_transfer_requ_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;


		$rID3=sql_update("inv_item_transfer_requ_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}


		// echo "10**".$flag."**".$rID."**".$rID3;oci_rollback($con);die;

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
	else if ($operation==2)  // Delete
	{
		if($update_dtls_id == "")
		{
			echo "20**Delete Failed";
			die;
		}
		$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and variable_list= 27", "auto_transfer_rcv");

		if($variable_auto_rcv == "")
		{
			$variable_auto_rcv = 1;
		}

		$trans_qnty=str_replace("'","",$txt_transfer_qnty)*1;
		$update_dtls_id = str_replace("'", "", $update_dtls_id);
		$update_trans_recv_id = str_replace("'", "", $update_trans_recv_id);
		$update_trans_issue_id = str_replace("'", "", $update_trans_issue_id);
		$hidden_transfer_qnty = str_replace("'", "", $hidden_transfer_qnty);
		$previous_from_prod_id = str_replace("'", "", $previous_from_prod_id);
		$previous_to_prod_id = str_replace("'", "", $previous_to_prod_id);

		if($variable_auto_rcv == 2 )
		{

			$is_rcv_exist = return_field_value("to_trans_id", "inv_item_transfer_requ_dtls", " id =$update_dtls_id and status_active=1", "to_trans_id");
			if($is_rcv_exist > 0)
			{
				echo "20**Fabric transfer already acknowledged by user.Delete not allowed.";
				die;
			}
		}

		$issue_check = sql_select("select sum(issue_qnty) as issue_qnty from inv_mrr_wise_issue_details  where recv_trans_id = $update_trans_recv_id and status_active = 1 ");

		if($issue_check[0][csf("issue_qnty")] > 0)
		{
			$check_to_ord_stock = ($hidden_transfer_qnty - $issue_check[0][csf("issue_qnty")]);

			echo "20**Next transaction found.\nTransfered Fabric already used = ".$issue_check[0][csf("issue_qnty")]." \nDelete not allowed";
			die;
		}


		$field_array_trans_pre_mrr = "balance_qnty*balance_amount*updated_by*update_date";
		$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=14 and a.item_category=2");

		$updateID_array_pre_trans = array();
		foreach($sql as $result)
		{
			$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
		}

		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$product_sql =  sql_select("select id, current_stock ,avg_rate_per_unit ,stock_value from product_details_master where id in ($previous_from_prod_id , $previous_to_prod_id )");

			foreach ($product_sql as  $val)
			{
				$product_arr[$val[csf("id")]]["current_stock"] = $val[csf("current_stock")];
				$product_arr[$val[csf("id")]]["avg_rate_per_unit"] = $val[csf("avg_rate_per_unit")];
				$product_arr[$val[csf("id")]]["stock_value"] = $val[csf("stock_value")];
			}


			$field_product_arr_up = "current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";

			$from_product_qnty =  $product_arr[$previous_from_prod_id]["current_stock"]+ $hidden_transfer_qnty;
			$from_product_rate =  $product_arr[$previous_from_prod_id]["avg_rate_per_unit"]*1;
			$from_product_amount =  $product_arr[$previous_from_prod_id]["stock_value"] + ($hidden_transfer_qnty*$from_product_rate);

			$updateID_array[]=$previous_from_prod_id;
			$data_product_arr_up[$previous_from_prod_id]=explode("*",("".$from_product_qnty."*".$from_product_rate."*".$from_product_amount."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));


			if($variable_auto_rcv!=2)
			{
				$to_product_qnty =  $product_arr[$previous_to_prod_id]["current_stock"]- $hidden_transfer_qnty;
				$to_product_rate =  $product_arr[$previous_to_prod_id]["avg_rate_per_unit"]*1;
				$to_product_amount =  $product_arr[$previous_to_prod_id]["stock_value"] - ($hidden_transfer_qnty*$to_product_rate);

				$updateID_array[]=$previous_to_prod_id;
				$data_product_arr_up[$previous_to_prod_id]=explode("*",("".$to_product_qnty."*".$to_product_rate."*".$to_product_amount."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			}
		}

		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$flag=1;
		$rID=execute_query("update inv_item_transfer_requ_dtls set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id =$update_dtls_id");
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}

		/*if($variable_auto_rcv==2)
		{
			$rID2=execute_query("update inv_transaction set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in ($update_trans_issue_id)");
			if($flag==1) { if($rID2) $flag=1; else $flag=0; }

			$rID6=execute_query("update inv_item_transfer_requ_dtls_ac set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where dtls_id =$update_dtls_id");
			if($flag==1)
			{
				if($rID6) $flag=1; else $flag=0;
			}

		}
		else*/
			if($variable_auto_rcv!=2){
				$rID2=execute_query("update inv_transaction set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in ($update_trans_issue_id, $update_trans_recv_id)");
				if($flag==1) { if($rID2) $flag=1; else $flag=0; }
			}

			if(count($update_data_trans_pre_mrr)>0)
			{
				$rID3=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans_pre_mrr,$update_data_trans_pre_mrr,$updateID_array_pre_trans));
				$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_trans_issue_id and entry_form=14");
				if($flag==1)
				{
					if($rID3) $flag=1; else $flag=0;
				}
				if($flag==1)
				{
					if($query3) $flag=1; else $flag=0;
				}
			}
			/*echo "10**";
			print_r($data_product_arr_up);
			die;*/
			if(count($data_product_arr_up) > 0)
			{
				$rID4=execute_query(bulk_update_sql_statement("product_details_master","id",$field_product_arr_up,$data_product_arr_up,$updateID_array));
				if($flag==1)
				{
					if($rID4) $flag=1; else $flag=0;
				}
			}

			if($variable_auto_rcv!=2)
			{
				$rID5=execute_query("update order_wise_pro_details set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where trans_id in ($update_trans_issue_id, $update_trans_recv_id)");
				if($flag==1) { if($rID5) $flag=1; else $flag=0; }
			}

			//echo "10**$flag##$rID##$rID2##$rID3##$rID4##$rID5##$query3";
			//oci_rollback($con);disconnect($con);die;

			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");
					echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "7**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id);
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id);
				}
				else
				{
					oci_rollback($con);
					echo "7**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id);
				}
			}
			disconnect($con);
			die;
	}
}


if($action=="finish_fabric_transfer_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);

	$sql="select id, transfer_system_id, challan_no, transfer_date, transfer_criteria, item_category, to_company, from_order_id, to_order_id, item_category,location_id,to_location_id from inv_item_transfer_requ_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$prod_sql=sql_select("select id, product_name_details, unit_of_measure from product_details_master where item_category_id=2");
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]=$row[csf("product_name_details")];
		$uom_arr[$row[csf("id")]]=$row[csf("unit_of_measure")];
	}

	?>
	<div style="width:930px;">
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
				<td width="120"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
				<td width="130"><strong>Transfer Criteria:</strong></td> <td width="175px"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
				<td width="125"><strong>To Company</strong></td><td width="175px"><? echo $company_library[$dataArray[0][csf('to_company')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
				<td><strong>Challan No.:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Item Category:</strong></td> <td width="175px"><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="40">SL</th>
					<th width="100" >From Store</th>
					<th width="100" >To Store</th>
					<th width="250" >Item Description</th>
					<th width="50" >UOM</th>
					<th width="100" >Transfered Qnty</th>
					<th width="110" >Color</th>
				</thead>
				<tbody>
					<?
					$sql_dtls="select id, from_store, to_store, from_prod_id, transfer_qnty, color_id from inv_item_transfer_requ_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
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
							<td><? echo $store_library[$row[csf("from_store")]]; ?></td>
							<td><? echo $store_library[$row[csf("to_store")]]; ?></td>
							<td><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
							<td align="center"><? echo $unit_of_measurement[$uom_arr[$row[csf("from_prod_id")]]]; ?></td>
							<td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
							<td><? echo $color_arr[$row[csf("color_id")]]; ?></td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="5" align="right"><strong>Total :</strong></td>
						<td align="right"><?php echo $transfer_qnty_sum; ?></td>
						<td align="right"><?php //echo $req_qny_edit_sum; ?></td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(23, $data[0], "900px");
			?>
		</div>
	</div>
	<?
	exit();
}

if($action=="finish_fabric_transfer_print_2")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	//$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where is_deleted=0 and status_active=1","id","batch_no");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	$prod_sql=sql_select("select id, product_name_details, unit_of_measure from product_details_master where item_category_id=2");
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]=$row[csf("product_name_details")];
		$uom_arr[$row[csf("id")]]=$row[csf("unit_of_measure")];
	}

	$sql="select a.id, a.transfer_system_id, a.challan_no, a.transfer_date, a.transfer_criteria, b.batch_id,c.batch_no, a.item_category, a.location_id, a.to_company, a.from_store_id, a.to_store_id, b.from_order_id, b.to_order_id, a.item_category, a.location_id, b.from_store, b.to_store, b.from_prod_id, sum(b.transfer_qnty) as transfer_qnty, b.uom,b.remarks, b.no_of_roll
	from inv_item_transfer_requ_mst a,inv_item_transfer_requ_dtls b , pro_batch_create_mst c
	where a.id=b.mst_id and b.batch_id = c.id and a.id='$data[1]' and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by a.id, a.transfer_system_id, a.challan_no, a.transfer_date, a.transfer_criteria,b.batch_id, c.batch_no, a.item_category, a.location_id, a.to_company, a.from_store_id, a.to_store_id, b.from_order_id, b.to_order_id, a.item_category, a.location_id, b.from_store, b.to_store, b.from_prod_id, b.uom,b.remarks ,b.no_of_roll
	order by c.batch_no";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$orderID="";$prodID="";
	foreach ($dataArray as $row) {
		$orderidArr[$row[csf('from_order_id')]]=$row[csf('from_order_id')];
		$orderID.=$row[csf('from_order_id')].',';
		$prodID.=$row[csf('from_prod_id')].',';
		$batchID.=$row[csf('batch_id')].',';

	}
	$orderID=chop($orderID,",");
	$prodID=chop($prodID,",");
	$batchID=chop($batchID,",");
	//echo "select a.id ,a.po_number,b.style_ref_no from wo_po_break_down a,wo_po_details_master b  where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted =0 and a.id in(".implode(",",$orderidArr).")";die;
	$sql_order= sql_select("select a.id ,a.po_number, b.style_ref_no from wo_po_break_down a,wo_po_details_master b  where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted =0 and a.id in(".implode(",",$orderidArr).")");
	foreach ($sql_order as  $row) {
		$orderArr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$orderArr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
	}

	$grey_used_sql = sql_select("select sum(grey_used_qty) as grey_used_qty, sum(receive_qnty) as receive_qnty, batch_id, prod_id from pro_finish_fabric_rcv_dtls where status_active =1 and is_deleted =0 and prod_id in ($prodID) and batch_id in ($batchID) group by  batch_id, prod_id ");

	foreach ($grey_used_sql as $val)
	{
		$grey_used_unit_arr[$val[csf("batch_id")]][$val[csf("prod_id")]] += $val[csf("grey_used_qty")]/$val[csf("receive_qnty")];
	}

	$sql_dtls ="select   a.company_id, a.supplier_id,  b.buyer_id, c.po_breakdown_id as order_id, d.id as prod_id, d.product_name_details, d.color , f.booking_no as booking_no_batch
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, product_details_master d , inv_transaction e, pro_batch_create_mst f
	where a.id=b.mst_id and b.id = c.dtls_id and b.prod_id=d.id  and c.trans_id=e.id and b.batch_id=f.id and a.item_category=2  and a.entry_form=37 and c.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id is not null and c.po_breakdown_id in($orderID) and d.id in($prodID) and f.id in($batchID) group by a.company_id, a.supplier_id, b.buyer_id, c.po_breakdown_id, d.id, d.product_name_details, d.color , f.booking_no order by d.id";
	//echo $sql_dtls;
	$sql_result= sql_select($sql_dtls);

	$tarnsferArry=array();
	foreach($sql_result as $row)
	{
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['booking_no_batch']=$row[csf('booking_no_batch')];
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['color']=$row[csf('color')];
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['product_name_details']=$row[csf('product_name_details')];
		//$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['batch_id']=$batch_arr[$row[csf('batch_no')]];
	}
	$tableWidth = 1300;
	?>
	<div style="width:<? echo $tableWidth; ?>px;">
		<table width="<? echo $tableWidth; ?>" cellspacing="0" align="right">
			<tr>
				<td align="left"><img src="../../<? echo $image_location; ?>" height="70" width="180"></td>
				<td  align="left" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
				<td></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?> <br>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Province No: <?php echo $result[csf('province')];?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}
					?>

				</td>
			</tr>
			<tr><td></td></tr>
			<tr><td></td></tr>
			<tr><td></td></tr>
			<tr>
				<td colspan="6" align="center" style="font-size:x-large;"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
			</tr>
		</table>
		<table width="<? echo $tableWidth; ?>" cellspacing="0" align="right" style="margin-top: 30px;">
			<tr>
				<td width="130"><strong>Transfer Criteria</strong></td> <td width="175px"><strong>:<? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></strong></td>
				<td><strong>Print Date and Time</strong></td><td width="175px"><strong>:<? echo date("d/m/Y") ." : ". date("h:i:sa"); ?></strong></td>
			</tr>
			<tr style="height: 20px;">
			</tr>

			<tr>
				<td width="125"><strong>From</strong></td><td width="175px">: <? echo $store_library[$dataArray[0][csf('from_store')]]; ?></td>
				<td width="125"><strong>To Company</strong></td><td width="175px">: <? echo $company_library[$dataArray[0][csf('to_company')]]; ?></td>
			</tr>
			<tr>
				<td width="120"><strong>Transfer ID</strong></td><td width="175px"><strong>: <? echo $dataArray[0][csf('transfer_system_id')]; ?></strong></td>
				<td width="125"><strong>To Store</strong></td><td width="175px">: <? echo $store_library[$dataArray[0][csf('to_store')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Transfer Date</strong></td><td width="175px">: <? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Challan No</strong></td><td width="175px">: <? echo $dataArray[0][csf('challan_no')]; ?></td>
			</tr>
			<tr>
				<td><strong>Item Category</strong></td> <td width="175px">: <? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
			</tr>
		</table>
		<br>
		<div  style="width:<? echo $tableWidth; ?>px;">
			<table align="right" cellspacing="0" width="1300"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="100">Buyer</th>
					<th width="180">Order</th>
					<th width="100">Style</th>
					<th width="160">Fab. Booking</th>
					<th width="100">Batch</th>
					<th width="100">Fab. Color</th>
					<th width="350">Item Description</th>
					<th width="100">No of Roll</th>
					<th width="100">Grey Used Quantity</th>
					<th width="100">Fab. WT</th>
					<th width="50">UOM</th>
					<th>Remarks</th>
				</thead>
				<tbody>

					<?

	//$sql_dtls="select id, from_store, to_store, from_prod_id, transfer_qnty, color_id from inv_item_transfer_requ_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";


					$i=1;
					foreach($dataArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$grey_used_unit = $grey_used_unit_arr[$row[csf('batch_id')]][$row[csf("from_prod_id")]];
						$grey_used_qnty = $grey_used_unit * $row[csf('transfer_qnty')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td align="center" width="100"><? echo $buyer_library[$tarnsferArry[$row[csf('from_order_id')]][$row[csf('from_prod_id')]]['buyer_id']]; ?></td>
							<td align="center" width="100" title="<? echo $order_id; ?>"><? echo $orderArr[$row[csf('from_order_id')]]['po_number'] ; ?></td>
							<td align="center" width="100"><? echo $orderArr[$row[csf('from_order_id')]]['style_ref_no']; ?></td>
							<td align="center" width="180"><? echo $tarnsferArry[$row[csf('from_order_id')]][$row[csf('from_prod_id')]]['booking_no_batch']; ?></td>
							<td align="center" width="100"><? echo $row[csf('batch_no')]; ?></td>
							<td align="center" width="100"><? echo $color_arr[$tarnsferArry[$row[csf('from_order_id')]][$row[csf('from_prod_id')]]['color']]; ?></td>
							<td align="center" width="250"><? echo $tarnsferArry[$row[csf('from_order_id')]][$row[csf('from_prod_id')]]['product_name_details']; ?></td>
							<td align="right" width="100"><? echo $row[csf('no_of_roll')]; ?></td>
							<td align="right" width="100"><? echo number_format($grey_used_qnty,2,".",""); ?></td>
							<td align="right" width="100"><? echo $total_trnf=$row[csf('transfer_qnty')];  ?></td>
							<td align="center" width="50"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
							<td align="center"><? echo $row[csf('remarks')]; ?></td>
						</tr>
						<?
						$total_transfer_qty+=$total_trnf;
						$no_of_roll += $row[csf('no_of_roll')];
						$tot_grey_used_qnty += $grey_used_qnty;
						$i++; } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="8" align="right"><strong>Total :</strong></td>
							<td align="right"><?php echo $no_of_roll; ?></td>
							<td align="right"><?php echo number_format($tot_grey_used_qnty,2,".",""); ?></td>
							<td align="right"><?php echo $total_transfer_qty; ?></td>
							<td colspan="2" align="right"></td>
						</tr>
					</tfoot>
				</table>
				<br>
				<?
				echo signature_table(23, $data[0], "1100px");
				?>
			</div>
		</div>
		<?
		exit();
	}



	function sql_update_a($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
	{

		$strQuery = "UPDATE ".$strTable." SET ";
		$arrUpdateFields=explode("*",$arrUpdateFields);
		$arrUpdateValues=explode("*",$arrUpdateValues);

		if(count($arrUpdateFields)!=count($arrUpdateValues)){
			return "0";
		}

		if(is_array($arrUpdateFields))
		{
			$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
			$Arraysize = count($arrayUpdate);
			$i = 1;
			foreach($arrayUpdate as $key=>$value):
				$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
				$i++;
			endforeach;
		}
		else
		{
			$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
		}
		$strQuery .=" WHERE ";

		$arrRefFields=explode("*",$arrRefFields);
		$arrRefValues=explode("*",$arrRefValues);
		if(is_array($arrRefFields))
		{
			$arrayRef = array_combine($arrRefFields,$arrRefValues);
			$Arraysize = count($arrayRef);
			$i = 1;
			foreach($arrayRef as $key=>$value):
				$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
				$i++;
			endforeach;
		}
		else
		{
			$strQuery .= $arrRefFields."=".$arrRefValues."";
		}
		echo $strQuery;die;
		global $con;
		if( strpos($strQuery, "WHERE")==false)  return "0";
		$stid =  oci_parse($con, $strQuery);
		$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
		if ($exestd)
			return "1";
		else
			return "0";

		die;
		if ( $commit==1 )
		{
			if (!oci_error($stid))
			{
				oci_commit($con);
				return "1";
			}
			else
			{
				oci_rollback($con);
				return "10";
			}
		}
		else
			return 1;
		die;
	}
	?>
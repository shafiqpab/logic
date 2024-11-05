<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

$variable_settings = "1";

if ($action == "load_drop_down_company") {
	$data = explode("_",$data);
	$company_cond = ($data[0] != "" && $data[0] == 1)?" and id=$data[0]":"";
	if($data[1] == 1){
		echo create_drop_down("cbo_party", 152, "select id,company_name from lib_company where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "--Select--", $data[0], "");
	}else{
		echo create_drop_down("cbo_party", 162, "select a.id,a.buyer_name from lib_buyer a,lib_buyer_party_type b where a.id=b.buyer_id and b.party_type=3 and a.status_active=1", "id,buyer_name", 1, "-- Select Party --", $selected, "", 0);
	}
	
	exit();
}

if ($action == "load_drop_down_buyer") {
	$data = explode("_",$data);
	echo create_drop_down("cbo_party", 152, "select id, buyer_name from lib_buyer where id=$data[0] and status_active=1", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
	exit();
}

if ($action=="upto_variable_settings")
{
	extract($_REQUEST);
	echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	exit();
}

if($action=="load_variable_qnty_popup")
{
	$style_wise_popup=0;
	$style_wise_popup=return_field_value("production_entry","variable_settings_production","company_name='$data' and variable_list=72 and is_deleted=0 and status_active=1");
	if($style_wise_popup==1) $style_wise_popup=$style_wise_popup; else $style_wise_popup=0;

	echo "document.getElementById('style_wise_popup').value 				= '".$style_wise_popup."';\n";
	
	exit();
}
/*if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location_name", 162, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/finish_feb_delivery_to_garments_controller*2', 'store','store_td', $('#cbo_company_id').val(), this.value);" );
	exit();
}

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/finish_feb_delivery_to_garments_controller",$data);
}
*/

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 152, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "reset_on_change(this.id);load_drop_down('requires/finish_feb_delivery_to_garments_controller', this.value+'_'+$data[0], 'load_drop_down_store','store_td');" );
	exit();
}
if($action=="print_button_variable_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."' and module_id=6 and report_id=213 and is_deleted=0 and status_active=1");	
	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);

	foreach($print_report_format_arr as $id) {

		if($id==115) $buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:80px;" value="Print" onClick="fnc_report_generated(1)" />';
		else if($id==66) $buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:80px;" value="Print 2" onClick="fn_report_generated(2)" />';
		else if($id==111) $buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:80px;" value="Print3" onClick="fn_report_generated3(3)" />';
		else if($id==137) $buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:80px;" value="Print 4" onClick="fn_report_generated(4)" />';
	    }
		echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
		exit();
}

if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);

	echo create_drop_down( "cbo_store_name", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[1]' and a.location_id = $data[0] and b.category_type=2 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"reset_on_change(this.id);load_drop_down('requires/finish_feb_delivery_to_garments_controller', this.value+'_'+$data[1], 'load_drop_floor','floor_td');load_drop_down('requires/finish_feb_delivery_to_garments_controller', this.value+'_'+$data[1], 'load_drop_room','room_td');load_drop_down('requires/finish_feb_delivery_to_garments_controller', this.value+'_'+$data[1], 'load_drop_rack','rack_td');load_drop_down('requires/finish_feb_delivery_to_garments_controller', this.value+'_'+$data[1], 'load_drop_shelf','shelf_td');");
	exit();
}


if($action == "load_drop_floor")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "cbo_floor", "182", "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_id,floor_room_rack_name", 1, "--Select Floor--", 0, "" );
}

if($action == "load_drop_room")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];

	echo create_drop_down( "cbo_room", "182", "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select Room--", 0, "" );
}

if($action == "load_drop_rack")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "txt_rack", '182', "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select Rack--", 0, "" );
}

if($action == "load_drop_shelf")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "txt_shelf", '182', "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select Shelf--", 0, "" );
}



if($action=="fabric_sales_order_popup")
{
	echo load_html_head_contents("Fabric Sales Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(booking_data) {
			document.getElementById('hidden_booking_data').value = booking_data;
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:1100px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:1000px; margin-left:3px">
				<legend>Enter search words</legend> 

				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="1000" class="rpt_table">
					<thead>
						<th>Within Group</th>
						<th>Sales Order No</th>
						<th>Booking No</th>
						<th>Style Ref. No</th>                    
						<th>Batch No</th>                    
						<th>Sales Date Range</th>                    
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
							<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
						</th> 
					</thead>
					<tr>
						<td align="center">
							<? echo create_drop_down("cbo_within_group", 150, $yes_no, "", 1, "--Select--", "", $dd, 0); ?>
						</td>
						<td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_sale_order_no" id="txt_sale_order_no" />	
						</td>
						<td align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />	
						</td>
						<td align="center">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_style_no" id="txt_style_no" />
						</td>
						<td align="center">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_batch_no" id="txt_batch_no" />
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"
							style="width:70px" readonly>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_sale_order_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $store_id;?>'+'_'+document.getElementById('txt_batch_no').value, 'create_fso_search_list_view', 'search_div', 'finish_feb_delivery_to_garments_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:70px;" />
						</td>
					</tr>
					<tr>
						<td colspan="7" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="margin-top:10px;" id="search_div" align="center"></div> 
			</fieldset>
		</form>
	</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_fso_search_list_view")
{
	$data 				= explode("_",$data);
	$company_id 		= $data[0];
	$fso_no		 		= trim($data[1]);
	$txt_booking_no		= trim($data[2]);
	$txt_style_no		= trim($data[3]);
	$within_group 		= trim($data[4]);	
	$date_from 			= trim($data[5]);
	$date_to 			= trim($data[6]);
	$store_id 			= trim($data[7]);
	$batch_no 			= trim($data[8]);
	//Static Variable Settings for floor, room, rack, shelf wise qnty balancing or not
	$variable_settings = "1";

	$company_arr 	= return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$supplier_arr 	= return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
	$buyer_arr 		= return_library_array("select id,short_name from lib_buyer", 'id', 'short_name');
	$location_arr 	= return_library_array("select id,location_name from lib_location", 'id', 'location_name');
	
	$search_field_cond="";$batch_no_cond="";
	$search_field_cond .= ($txt_booking_no != "")?" and d.sales_booking_no like '%" . $txt_booking_no . "'":"";
	$search_field_cond .= ($fso_no!= "")?" and d.job_no_prefix_num=$fso_no":"";
	$search_field_cond .= ($txt_style_no != "")?" and d.style_ref_no like '%" . $txt_style_no . "%'":"";
	$batch_no_cond 		= ($batch_no != "")?" and c.batch_no like '%" . $batch_no . "%'":"";

	if ($batch_no!="") 
	{
		$batch_sql="SELECT id from pro_batch_create_mst where status_active=1 and is_deleted=0 and batch_no='$batch_no'";
		//echo $batch_sql;die;
		$batch_sql_result = sql_select($batch_sql);
		$batch_id_arr=array();
		foreach ($batch_sql_result as $key => $row) 
		{
			$batch_id_arr[] = $row[csf("id")];
		}
		$batch_id_arr = array_filter(array_unique($batch_id_arr));
		$batch_ids =  implode(",", $batch_id_arr); 
		$batch_id_cond=" and b.batch_id in (".$batch_ids.") ";
	}
	// echo $batch_id_cond;die;

	$date_cond = '';
	if ($date_from != "" && $date_to != "") {
		if ($db_type == 0) {
			$date_cond = "and d.insert_date between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and d.insert_date between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and d.within_group=$within_group";
	
	$sql = "SELECT x.company_id, x.buyer_id,x.within_group,x.location_id,x.sales_booking_no,x.booking_id, x.style_ref_no,x.job_no_prefix_num, x.job_no, x.booking_date,x.id, x.po_job_no,x.po_company_id,x.insert_date,x.po_buyer, sum(x.receive_qnty) as receive_qnty, x.booking_without_order  from (select a.company_id, d.buyer_id, d.within_group,d.location_id, d.sales_booking_no, d.booking_id, d.style_ref_no,d.job_no_prefix_num,d.job_no,d.booking_date,d.id,d.po_job_no,d.po_company_id,d.insert_date,d.po_buyer,sum(b.receive_qnty) as receive_qnty, d.booking_without_order 
	from inv_receive_master a,pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c,fabric_sales_order_mst d 
	where a.company_id=$company_id and a.entry_form in (225,7) and a.id=b.mst_id and b.batch_id=c.id and c.sales_order_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_sales=1 and a.store_id = '$store_id' $search_field_cond $within_group_cond $date_cond $batch_no_cond 
	group by a.company_id,a.knitting_source, a.knitting_company,d.buyer_id,d.within_group, d.location_id, d.sales_booking_no, d.booking_id, d.style_ref_no, d.job_no_prefix_num, d.job_no,d.booking_date,d.id, d.po_job_no,d.po_company_id, d.insert_date, d.po_buyer, d.booking_without_order	
	union all
	select a.company_id, d.buyer_id,d.within_group, d.location_id, d.sales_booking_no, d.booking_id, d.style_ref_no,d.job_no_prefix_num, d.job_no, d.booking_date,d.id,d.po_job_no,d.po_company_id, d.insert_date,d.po_buyer,sum(b.transfer_qnty) as receive_qnty, d.booking_without_order 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, fabric_sales_order_mst d 
	where a.id = b.mst_id and a.to_order_id = d.id and a.entry_form = 230 and b.to_store ='$store_id' $search_field_cond $within_group_cond $date_cond $batch_id_cond
	group by a.company_id, d.buyer_id, d.within_group, d.location_id, d.sales_booking_no, d.booking_id, d.style_ref_no,d.job_no_prefix_num, d.job_no, d.booking_date,d.id,d.po_job_no, d.po_company_id,d.insert_date, d.po_buyer, d.booking_without_order
	) x
	group by  x.company_id, x.buyer_id,x.within_group,x.location_id,x.sales_booking_no,x.booking_id, x.style_ref_no,x.job_no_prefix_num, x.job_no, x.booking_date,x.id, x.po_job_no,x.po_company_id,x.insert_date,x.po_buyer, x.booking_without_order order by x.job_no";
	//echo $sql;
	$result = sql_select($sql);	
	
	$buyer_id_arr=array();$order_id_arr=array();
	foreach ($result as $row) {
		$buyer_id_arr[] = $row[csf("buyer_id")];
		$order_id_arr[] = $row[csf("id")];
	}
	$order_id_arr = array_filter(array_unique($order_id_arr));
	$order_ids =  "'".implode("','", $order_id_arr)."'"; 

	$party_type_arr=array();
	if(!empty($buyer_id_arr)){
		$buyer_party_info=sql_select("select buyer_id,party_type from lib_buyer_party_type where buyer_id in(".implode(",",$buyer_id_arr).")");		
		foreach ($buyer_party_info as $buyer_party) {
			$party_type_arr[$buyer_party[csf("buyer_id")]][]=$buyer_party[csf("party_type")];
		}
	}


	$delivery_sql = "select b.store_id,b.batch_id,b.body_part_id bodypart_id,b.prod_id product_id,b.uom,b.fabric_shade,sum(b.issue_qnty) delivery_qnty, b.width_type,b.order_id,$select_fields_delivery c.color_id,d.detarmination_id determination_id,d.gsm,d.dia_width dia from inv_issue_master a,inv_finish_fabric_issue_dtls b,pro_batch_create_mst c,product_details_master d where a.entry_form in (224,287) and a.id=b.mst_id and b.batch_id=c.id and b.prod_id=d.id and a.status_active='1' and a.is_deleted='0' and b.order_id in (".$order_ids.") and b.store_id=".$store_id." and b.status_active=1 and c.status_active=1 and d.status_active=1 group by b.store_id,b.batch_id,b.body_part_id,b.prod_id, b.uom,b.fabric_shade,b.width_type,b.order_id $group_field_delivery,c.color_id,d.detarmination_id,d.gsm,d.dia_width";

		$deliveryData = sql_select($delivery_sql);
		$deliveryQnty=0;
		foreach ($deliveryData as $row) {
			$deliveryQnty[$row[csf("order_id")]] += $row[csf("delivery_qnty")];
		}


		$trans_out_sql = "SELECT a.from_order_id as po_breakdown_id,b.batch_id, b.from_prod_id as prod_id, b.body_part_id,b.feb_description_id as fabric_description_id, b.from_store as store_id,b.uom, b.floor_id, b.room, b.rack as rack_no,b.shelf as shelf_no,b.color_id, b.fabric_shade,b.dia_width_type, b.dia_width as width, b.gsm, sum(b.transfer_qnty) as quantity 
		FROM inv_item_transfer_mst a, inv_item_transfer_dtls b 
		WHERE a.id=b.mst_id and a.entry_form =230 and a.from_order_id in (".$order_ids.") and b.from_store = ".$store_id." and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
		group by a.from_order_id, b.batch_id, b.from_prod_id, b.body_part_id, b.feb_description_id, b.from_store, b.uom, b.floor_id, b.room, b.rack, b.shelf, b.color_id,b.fabric_shade,b.dia_width_type, b.dia_width, b.gsm";

		$trans_out_Data = sql_select($trans_out_sql);
		$trans_outQnty=0;
		foreach ($trans_out_Data as $row) 
		{
			$trans_outQnty[$row[csf("po_breakdown_id")]] += $row[csf("quantity")];		
		}

		$issue_return_sql = sql_select("select e.po_breakdown_id,b.batch_id,d.store_id,b.prod_id,b.room,b.floor,b.rack_no, b.shelf_no,b.fabric_shade, b.dia_width_type, sum(e.quantity) as qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction d , order_wise_pro_details e where a.id=b.mst_id  and b.mst_id=d.mst_id and b.trans_id = d.id and d.id = e.trans_id and b.id = e.dtls_id  and d.item_category=2 and d.transaction_type in (4) and a.entry_form in (233) and e.entry_form = 233 and e.po_breakdown_id in ($order_ids) and d.store_id=".$store_id." and e.is_sales=1 group by  e.po_breakdown_id,b.batch_id,d.store_id,b.prod_id,b.room,b.floor,b.rack_no, b.shelf_no,b.fabric_shade,b.dia_width_type");

		$issue_returnQnty=0;
		foreach ($issue_return_sql as $row) 
		{
			$issue_returnQnty[$row[csf("po_breakdown_id")]] += $row[csf("qnty")];
		}


	?>
	<style type="text/css">
	.rpt_table tr{ text-decoration:none; cursor:pointer; }
	.rpt_table tr td{ text-align: center; }
</style>
<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
	<thead>
		<th width="40">SL</th>
		<th width="90">Sales Order No</th>
		<th width="60">Year</th>
		<th width="80">Within Group</th>
		<th width="70">Buyer</th>
		<th width="120">Booking No</th>
		<th width="80">Booking date</th>
		<th width="110">Style Ref.</th>
		<th>Location</th>
	</thead>
</table>
<div style="width:800px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table"
	id="tbl_list_search">
	<?
	$i = 1;
	if(!empty($result)){
		foreach ($result as $row) {	
				$cumulative_delivery_qnty = $deliveryQnty[$row[csf('id')]] - $issue_returnQnty[$row[csf('id')]];
				$total_delivery_out = $cumulative_delivery_qnty + $trans_outQnty[$row[csf('id')]];
				$balance_qnty = $row[csf('receive_qnty')] - $total_delivery_out;
				//echo $row[csf('receive_qnty')].'-'. $deliveryQnty[$row[csf('id')]] .'-'. $issue_returnQnty[$row[csf('id')]] .'+'. $trans_outQnty[$row[csf('id')]];
			if($balance_qnty>0)
			{
				$within_group = $row[csf('within_group')];
				if($within_group==1 || ($within_group==2 && in_array(3,$party_type_arr[$row[csf("buyer_id")]]))){
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

					$buyer = $buyer_arr[$row[csf('po_buyer')]];
					$booking_data = $row[csf('id')]. "**" . $row[csf('sales_booking_no')]."**".$row[csf('company_id')]."**".$row[csf('within_group')]."**".$row[csf('buyer_id')]."**".$row[csf('job_no')]."**".$row[csf('po_job_no')]."**".$row[csf('po_company_id')]."**".$company_arr[$row[csf('po_company_id')]]."**".$row[csf('booking_id')]."**".$row[csf('booking_without_order')];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $booking_data; ?>');">
						<td width="40"><? echo $i; ?></td>
						<td width="90"><? echo $row[csf('job_no_prefix_num')]; ?></td>
						<td width="60"><p><? echo date("Y",strtotime($row[csf('insert_date')])); ?></p></td>
						<td width="80"><p><? echo$yes_no[$within_group]; ?></p></td>
						<td width="70"><p><? echo $buyer; ?></p></td>
						<td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
						<td width="80"><? echo change_date_format($row[csf('booking_date')]); ?></td>
						<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
						<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
					</tr>
					<?
					$i++;
				}
			}
		}
	}else{
		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<th colspan="9">No data found</th>
		</tr>
		<?
	}
	?>
</table>
</div>
<?
exit();
}

if($action=='show_fabric_desc_listview')
{
	// print_r($data);die;
	$data=explode("**",$data);

	//Static Variable Settings for floor, room, rack, shelf wise qnty balancing or not
	$variable_settings = "1";

	$booking_without_order = $data[4];
	$store_update_upto = $data[5];

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id asc";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
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
	}
	unset($data_array);

	$lib_floor_arr=return_library_array( "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and a.company_id='$data[3]' and b.store_id='$data[2]' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name", "floor_id","floor_room_rack_name" ); 

	$lib_room_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[3] and b.store_id='$data[2]' order by b.room_id", "room_id","floor_room_rack_name" ); 

	$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[3] and b.store_id='$data[2]' order by b.floor_id", "rack_id","floor_room_rack_name" );

	$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[3] and b.store_id='$data[2]' order by b.floor_id", "shelf_id","floor_room_rack_name" ); 

	if($variable_settings == 1)
	{
		$select_fields_rcv  = " b.floor, b.room,b.rack_no,b.shelf_no, ";
		$select_fields_trans  = " b.to_floor_id as floor,b.to_room as room, b.to_rack as rack_no,b.to_shelf as shelf_no, ";
		$group_field_rcv = " ,b.floor, b.room,b.rack_no,b.shelf_no ";
		$group_field_trans = " ,b.to_floor_id,b.to_room, b.to_rack,b.to_shelf ";

		$select_fields_all = " x.floor, x.room, x.rack_no, x.shelf_no, ";
		$group_fields_all = " ,x.floor, x.room, x.rack_no, x.shelf_no ";
	}

	if($db_type==0){
		$castingCond_order_id="cast(c.po_breakdown_id as CHAR(4000)) as order_id";
		$castingCond_to_order_id="cast(a.to_order_id as CHAR(4000)) as order_id";
		$booking_without_order_cond = " and (a.booking_without_order=0 or a.booking_without_order ='') ";
	}
	else{
		$castingCond_order_id="cast(c.po_breakdown_id as varchar2(4000)) as order_id";
		$castingCond_to_order_id="cast(a.to_order_id as varchar2(4000)) as order_id";
		$booking_without_order_cond = " and (a.booking_without_order=0 or a.booking_without_order is null) ";
	}
	if($booking_without_order == 1 )
	{
		$sql_rcv = "select a.company_id as knitting_company, 1 as knitting_source, min(a.receive_date) as receive_date,a.store_id, b.prod_id,b.batch_id,b.order_id,b.body_part_id, b.fabric_description_id,
		 b.gsm,b.width,b.color_id,b.dia_width_type,b.is_sales,b.uom,b.fabric_shade, b.floor, b.room,b.rack_no,b.shelf_no, sum(b.receive_qnty) as receive_qnty, sum(d.order_qnty) as order_qnty, 
		 sum(d.order_amount) as order_amount, sum(d.cons_quantity) as cons_quantity, sum(d.cons_amount) as cons_amount, sum(b.aop_amount) as aop_amount 
		 from inv_receive_master a,inv_transaction d,pro_finish_fabric_rcv_dtls b
		 where a.id=b.mst_id and d.id=b.trans_id and a.company_id='$data[3]' and b.order_id = '".$data[0]."' and a.store_id = ".$data[2]." and b.trans_id >0 and a.item_category=2 and a.entry_form in (7,225) and b.is_sales=1 and a.status_active=1 and b.status_active=1 
		 group by a.company_id, a.store_id, b.prod_id,b.batch_id,b.order_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.dia_width_type,b.is_sales, b.uom,b.fabric_shade $group_field_rcv ";
		 //and a.booking_without_order=1
	}
	else
	{

		$sql_rcv = "select a.company_id as knitting_company, 1 as knitting_source,min(a.receive_date) as receive_date,a.store_id, b.prod_id,b.batch_id, $castingCond_order_id,b.body_part_id, b.fabric_description_id, b.gsm,b.width,b.color_id, b.dia_width_type,b.is_sales,b.uom,b.fabric_shade, b.floor, b.room,b.rack_no,b.shelf_no, sum(c.quantity) as receive_qnty, sum(d.order_qnty) as order_qnty, sum(d.order_amount) as order_amount, sum(d.cons_quantity) as cons_quantity, sum(d.cons_amount) as cons_amount, sum(b.aop_amount) as aop_amount 
		from inv_receive_master a,inv_transaction d,pro_finish_fabric_rcv_dtls b,order_wise_pro_details c where a.id=b.mst_id and d.id=b.trans_id and b.id = c.dtls_id and a.company_id='$data[3]' and c.po_breakdown_id = ".$data[0]." and a.store_id = ".$data[2]." and a.item_category=2 and a.entry_form in (7,225) and c.entry_form in (7,225) and b.is_sales=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 group by a.company_id, a.store_id, b.prod_id,b.batch_id,c.po_breakdown_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.dia_width_type,b.is_sales,b.uom,b.fabric_shade $group_field_rcv ";
		//$booking_without_order_cond
	}
	

	$sql .= "select x.knitting_company, x.knitting_source, x.receive_date, x.store_id, x.prod_id, x.batch_id, x.order_id, x.body_part_id, x.fabric_description_id, x.gsm, x.width, x.color_id, x.dia_width_type, x.is_sales, x.uom, x.fabric_shade, $select_fields_all sum(receive_qnty) as receive_qnty, sum(x.order_qnty) as order_qnty, sum(x.order_amount) as order_amount, sum(x.cons_quantity) cons_quantity, sum(x.cons_amount) as cons_amount, sum(x.aop_amount) as aop_amount  from (";

	$sql .= $sql_rcv ." union all
	select a.company_id as knitting_company, 1 as knitting_source, min(a.transfer_date) as receive_date,b.to_store as store_id, b.from_prod_id as prod_id, b.to_batch_id as batch_id, $castingCond_to_order_id , b.body_part_id, b.feb_description_id as fabric_description_id, b.gsm, b.dia_width as width,b.color_id, b.dia_width_type, 1 as is_sales, b.uom, b.fabric_shade, $select_fields_trans  
	sum(b.transfer_qnty) as receive_qnty,
	sum(d.order_qnty) as order_qnty,
	sum(d.order_amount) as order_amount,
	sum(d.cons_quantity) as cons_quantity,
	sum(d.cons_amount) as cons_amount, sum(b.aop_amount) as aop_amount 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction d 
	where a.id=b.mst_id and b.to_trans_id=d.id and a.entry_form in(230) and a.to_order_id =".$data[0]." and b.to_store = ".$data[2]." and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
	group by a.company_id, b.to_store, b.from_prod_id, b.to_batch_id, a.to_order_id, b.body_part_id, b.feb_description_id, b.gsm, b.dia_width,b.color_id, b.dia_width_type, b.uom, b.fabric_shade $group_field_trans";

	$sql .= " ) x group by x.knitting_company, x.knitting_source, x.receive_date, x.store_id, x.prod_id, x.batch_id, x.order_id, x.body_part_id, x.fabric_description_id, x.gsm, x.width, x.color_id, x.dia_width_type, x.is_sales, x.uom, x.fabric_shade $group_fields_all 
	order by x.batch_id, x.fabric_description_id, x.color_id ";
	//echo $sql;

	//knitting_company, knitting_source are changed to company and inhouse with consultasion with jahid vai, barkat (URMI), rasel vai 04-Oct-2021


	$data_array=sql_select($sql);
	$batch_id_arr = $color_id_arr = $order_id_arr = $sales_id_arr = array();
	foreach($data_array as $row)
	{
		$batch_id_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];
		$color_id_arr[$row[csf("color_id")]] = $row[csf("color_id")];
		$order_id_arr[$row[csf("order_id")]] = $row[csf("order_id")];
	}

	$order_id_arr = array_filter(array_unique($order_id_arr));
	$order_ids =  "'".implode("','", $order_id_arr)."'"; 

	$delivery_arr = array();
	if(!empty($order_id_arr))
	{
		if($variable_settings == 1)
		{
			$select_fields_delivery  = "b.floor floor_id,b.room,b.rack_no rack,b.shelf_no shelf, ";
			$group_field_delivery = " ,b.floor,b.room,b.rack_no,b.shelf_no ";
		}

		$delivery_sql = "SELECT 1 as knit_dye_source, a.company_id as knit_dye_company, b.store_id,b.batch_id,b.body_part_id bodypart_id,b.prod_id product_id,b.uom,b.fabric_shade, 
		sum(b.issue_qnty) delivery_qnty, 
		sum(e.order_qnty) as order_qnty, 
		sum(e.order_amount) as order_amount,
		sum(e.cons_quantity) as cons_quantity, 
		sum(e.cons_amount) as cons_amount,
		sum(b.aop_amount) as aop_amount,
		b.width_type,b.order_id,$select_fields_delivery c.color_id,d.detarmination_id determination_id,d.gsm,d.dia_width dia from inv_issue_master a,inv_finish_fabric_issue_dtls b,pro_batch_create_mst c,product_details_master d, inv_transaction e where a.entry_form in (224,287) and a.id=b.mst_id and b.batch_id=c.id and b.prod_id=d.id and b.trans_id=e.id and a.status_active='1' and a.is_deleted='0' and b.order_id in (".$order_ids.") and b.store_id=".$data[2]." and b.status_active=1 and c.status_active=1 and d.status_active=1 
		group by a.company_id, b.store_id,b.batch_id,b.body_part_id,b.prod_id, b.uom,b.fabric_shade,b.width_type,b.order_id $group_field_delivery,c.color_id,d.detarmination_id,d.gsm,d.dia_width";

		$deliveryData = sql_select($delivery_sql);
		foreach ($deliveryData as $row) {
			if($variable_settings == 1)
			{
				$delivery_arr[$row[csf("knit_dye_source")]][$row[csf("knit_dye_company")]][$row[csf("bodypart_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]][$row[csf("floor_id")]][$row[csf("rack")]][$row[csf("shelf")]][$row[csf("room")]] += $row[csf("delivery_qnty")];
				$delivery_trans_arr[$row[csf("knit_dye_source")]][$row[csf("knit_dye_company")]][$row[csf("bodypart_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]][$row[csf("floor_id")]][$row[csf("rack")]][$row[csf("shelf")]][$row[csf("room")]]['order_qnty'] += $row[csf("order_qnty")];
				$delivery_trans_arr[$row[csf("knit_dye_source")]][$row[csf("knit_dye_company")]][$row[csf("bodypart_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]][$row[csf("floor_id")]][$row[csf("rack")]][$row[csf("shelf")]][$row[csf("room")]]['order_amount'] += $row[csf("order_amount")];
				$delivery_trans_arr[$row[csf("knit_dye_source")]][$row[csf("knit_dye_company")]][$row[csf("bodypart_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]][$row[csf("floor_id")]][$row[csf("rack")]][$row[csf("shelf")]][$row[csf("room")]]['cons_quantity'] += $row[csf("cons_quantity")];
				$delivery_trans_arr[$row[csf("knit_dye_source")]][$row[csf("knit_dye_company")]][$row[csf("bodypart_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]][$row[csf("floor_id")]][$row[csf("rack")]][$row[csf("shelf")]][$row[csf("room")]]['cons_amount'] += $row[csf("cons_amount")];
				$delivery_trans_arr[$row[csf("knit_dye_source")]][$row[csf("knit_dye_company")]][$row[csf("bodypart_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]][$row[csf("floor_id")]][$row[csf("rack")]][$row[csf("shelf")]][$row[csf("room")]]['aop_amount'] += $row[csf("aop_amount")];
			}
			else
			{
				$delivery_arr[$row[csf("knit_dye_source")]][$row[csf("knit_dye_company")]][$row[csf("bodypart_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]] += $row[csf("delivery_qnty")];
				$delivery_trans_arr[$row[csf("knit_dye_source")]][$row[csf("knit_dye_company")]][$row[csf("bodypart_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]]['order_qnty'] += $row[csf("order_qnty")];
				$delivery_trans_arr[$row[csf("knit_dye_source")]][$row[csf("knit_dye_company")]][$row[csf("bodypart_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]]['order_amount'] += $row[csf("order_amount")];
				$delivery_trans_arr[$row[csf("knit_dye_source")]][$row[csf("knit_dye_company")]][$row[csf("bodypart_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]]['cons_quantity'] += $row[csf("cons_quantity")];
				$delivery_trans_arr[$row[csf("knit_dye_source")]][$row[csf("knit_dye_company")]][$row[csf("bodypart_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]]['cons_amount'] += $row[csf("cons_amount")];
				$delivery_trans_arr[$row[csf("knit_dye_source")]][$row[csf("knit_dye_company")]][$row[csf("bodypart_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]]['aop_amount'] += $row[csf("aop_amount")];
			}			
		}

	 	$trans_out_sql = "SELECT a.from_order_id as po_breakdown_id,b.batch_id, b.from_prod_id as prod_id, b.body_part_id,b.feb_description_id as fabric_description_id, b.from_store as store_id,b.uom, b.floor_id, b.room, b.rack as rack_no,b.shelf as shelf_no,b.color_id, b.fabric_shade,b.dia_width_type, b.dia_width as width, b.gsm, sum(b.transfer_qnty) as quantity, sum(c.order_qnty) as order_qnty, sum(c.order_amount) as order_amount, sum(c.cons_quantity) as cons_quantity, sum(c.cons_amount) as cons_amount, sum(b.aop_amount) as aop_amount
		FROM inv_item_transfer_mst a, inv_item_transfer_dtls b ,inv_transaction c 
		WHERE a.id=b.mst_id  and b.to_trans_id=c.id and a.entry_form =230 and a.from_order_id =".$data[0]." and b.from_store = ".$data[2]." and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
		group by a.from_order_id, b.batch_id, b.from_prod_id, b.body_part_id, b.feb_description_id, b.from_store, b.uom, b.floor_id, b.room, b.rack, b.shelf, b.color_id,b.fabric_shade,b.dia_width_type, b.dia_width, b.gsm";

		$trans_out_Data = sql_select($trans_out_sql);
		foreach ($trans_out_Data as $row) 
		{
			if($variable_settings == 1)
			{
				$trans_out_arr[$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor_id")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]] += $row[csf("quantity")];
				
				$trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor_id")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['order_qnty'] += $row[csf("order_qnty")];
				$trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor_id")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['order_amount'] += $row[csf("order_amount")];
				$trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor_id")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['cons_quantity'] += $row[csf("cons_quantity")];
				$trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor_id")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['cons_amount'] += $row[csf("cons_amount")];
				$trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor_id")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['aop_amount'] += $row[csf("aop_amount")];
			}
			else
			{
				$trans_out_arr[$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]] += $row[csf("quantity")];
			
				$trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['order_qnty'] += $row[csf("order_qnty")];
				$trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['order_amount'] += $row[csf("order_amount")];
				$trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['cons_quantity'] += $row[csf("cons_quantity")];
				$trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['cons_amount'] += $row[csf("cons_amount")];
				$trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['aop_amount'] += $row[csf("aop_amount")];
			}			
		}

		$issue_return_sql = sql_select("SELECT a.company_id as knitting_company,1 as knitting_source, e.po_breakdown_id, b.batch_id,d.store_id,b.prod_id,b.room, b.floor,b.rack_no, b.shelf_no,b.fabric_shade, b.dia_width_type, b.body_part_id,
		sum(e.quantity) as qnty, sum(d.order_qnty) as order_qnty, sum(d.order_amount) as order_amount,
		sum(d.cons_quantity) as cons_quantity, sum(d.cons_amount) as cons_amount, sum(b.aop_amount) as aop_amount
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction d , order_wise_pro_details e where a.id=b.mst_id  and b.mst_id=d.mst_id and b.trans_id = d.id and d.id = e.trans_id and b.id = e.dtls_id  and d.item_category=2 and d.transaction_type in (4) and a.entry_form in (233) and e.entry_form = 233 and e.po_breakdown_id in ($order_ids) and d.store_id=".$data[2]." and e.is_sales=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
		group by  a.company_id, e.po_breakdown_id,b.batch_id,d.store_id,b.prod_id,b.room,b.floor,b.rack_no, b.shelf_no,b.fabric_shade,b.dia_width_type, b.body_part_id");
		
		foreach ($issue_return_sql as $row) 
		{
			if($variable_settings == 1)
			{
				$issue_return_qnty_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]] += $row[csf("qnty")];

				$issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['order_qnty'] += $row[csf("order_qnty")];
				$issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['order_amount'] += $row[csf("order_amount")];
				$issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['cons_quantity'] += $row[csf("cons_quantity")];
				$issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['cons_amount'] += $row[csf("cons_amount")];
				$issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['aop_amount'] += $row[csf("aop_amount")];
			}
			else
			{
				$issue_return_qnty_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]] += $row[csf("qnty")];

				$issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['order_qnty'] += $row[csf("order_qnty")];
				$issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['order_amount'] += $row[csf("order_amount")];
				$issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['cons_quantity'] += $row[csf("cons_quantity")];
				$issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['cons_amount'] += $row[csf("cons_amount")];
				$issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['aop_amount'] += $row[csf("aop_amount")];
			}
		}
	}

	if(!empty($batch_id_arr)){

		$all_batch_ids = implode(",", $batch_id_arr);
		$all_batch_id_cond = "";
		$batchCond = "";
		if ($db_type == 2 && count($batch_id_arr) > 999) {
			$batch_id_arr_chunk = array_chunk($batch_id_arr, 999);
			foreach ($batch_id_arr_chunk as $chunk_arr) {
				$chunk_arr_value = implode(",", $chunk_arr);
				$batchCond .= " id in($chunk_arr_value) or ";
			}

			$all_batch_id_cond .= " and (" . chop($batchCond, 'or ') . ")";
		} else {
			$all_batch_id_cond = " and id in($all_batch_ids)";
		}
			
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where status_active=1 $all_batch_id_cond","id","batch_no");
	}
	
	$color_arr=array();
	if(!empty($color_id_arr)){
		$all_color_ids = implode(",", $color_id_arr);
		$all_color_id_cond = "";
		$colorCond = "";
		if ($db_type == 2 && count($color_id_arr) > 999) {
			$color_id_arr_chunk = array_chunk($color_id_arr, 999);
			foreach ($color_id_arr_chunk as $chunk_arr) {
				$chunk_arr_value = implode(",", $chunk_arr);
				$colorCond .= " id in($chunk_arr_value) or ";
			}

			$all_color_id_cond .= " and (" . chop($colorCond, 'or ') . ")";
		} else {
			$all_color_id_cond = " and id in($all_color_ids)";
		}

		$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 $all_color_id_cond",'id','color_name');
	}
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="690">
		<thead>
			<th width="20">SL</th>
			<th width="70">Batch</th>
			<th width="150">Fabric Description</th>
			<th width="30">UOM</th>
			<th width="70">Dia/ W. Type</th>
			<th width="60">Color</th>
			<th width="40">Shade</th>
			<?
			if($variable_settings == 1)
			{
				?>
				<th width="50">Floor</th>
				<th width="50">Room</th>
				<th width="50">Rack</th>
				<th width="50">Shelf</th>
				<?
			}
			?>
			<th width="40">Qnty</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($data_array as $row)
			{  
				if($variable_settings == 1)   
				{
					$delivery_qnty = $delivery_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]];
					$trans_out_qnty = $trans_out_arr[$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]];
					$issue_return_qnty = $issue_return_qnty_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]];

					$delivery_order_qnty = $delivery_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['order_qnty'];
					$delivery_order_amount = $delivery_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['order_amount'];
					$delivery_cons_qnty = $delivery_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['cons_quantity'];
					$delivery_cons_amount = $delivery_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['cons_amount'];
					$delivery_aop_amount = $delivery_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['aop_amount'];

					$trans_out_order_qnty = $trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['order_qnty'];
					$trans_out_order_amount = $trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['order_amount'];
					$trans_out_cons_qnty = $trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['cons_quantity'];
					$trans_out_cons_amount = $trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['cons_amount'];
					$trans_out_aop_amount = $trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['aop_amount'];

					$issue_return_order_qnty = $issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['order_qnty'];
					$issue_return_order_amount = $issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['order_amount'];
					$issue_return_cons_qnty = $issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['cons_quantity'];
					$issue_return_cons_amount = $issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['cons_amount'];
					$issue_return_aop_amount = $issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]]['aop_amount'];


					$addition_data = "**1"."**".$row[csf("floor")]."**".$row[csf("rack_no")]."**".$row[csf("shelf_no")]."**".$row[csf("room")];
					$title = "Fl=".$row[csf("floor")].",Rc=".$row[csf("rack_no")].",Sh=".$row[csf("shelf_no")].",Ro=".$row[csf("room")];
				}
				else
				{
					$delivery_qnty = $delivery_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]];
					$trans_out_qnty = $trans_out_arr[$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]];
					$issue_return_qnty = $issue_return_qnty_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]];

					$delivery_order_qnty = $delivery_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['order_qnty'];
					$delivery_order_amount = $delivery_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['order_amount'];
					$delivery_cons_qnty = $delivery_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['cons_quantity'];
					$delivery_cons_amount = $delivery_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['cons_amount'];
					$delivery_aop_amount = $delivery_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['aop_amount'];
					
					$trans_out_order_qnty = $trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['order_qnty'];
					$trans_out_cons_amount = $trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['order_amount'];
					$trans_out_cons_qnty = $trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['cons_quantity'];
					$trans_out_cons_amount = $trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['cons_amount'];
					$trans_out_aop_amount = $trans_out_trns_arr[$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['aop_amount'];
					
					$issue_return_order_qnty = $issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['order_qnty'];
					$issue_return_order_amount = $issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['order_amount'];
					$issue_return_cons_qnty = $issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['cons_quantity'];
					$issue_return_cons_amount = $issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['cons_amount'];
					$issue_return_aop_amount = $issue_return_qnty_trans_arr[$row[csf("knitting_source")]][$row[csf("knitting_company")]][$row[csf("body_part_id")]][$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]]['aop_amount'];


					$addition_data = "**0";
					$title = "";
				}

				$cumulative_delivery_qnty = $delivery_qnty - $issue_return_qnty;
				$total_delivery_out = $cumulative_delivery_qnty + $trans_out_qnty;

				$cumulative_delivery_order_qnty = $delivery_order_qnty- $issue_return_order_qnty;
				$total_delivery_order_out = $cumulative_delivery_order_qnty + $trans_out_order_qnty;

				$cumulative_delivery_order_amount = $delivery_order_amount- $issue_return_order_amount;
				$total_delivery_order_amount_out = $cumulative_delivery_order_amount + $trans_out_order_amount;

				$cumulative_delivery_cons_qnty = $delivery_cons_qnty- $issue_return_cons_qnty;
				$total_delivery_cons_qnty_out = $cumulative_delivery_cons_qnty + $trans_out_cons_qnty;
				$cumulative_delivery_cons_amount = $delivery_cons_amount- $issue_return_cons_amount;
				$total_delivery_cons_amount_out = $cumulative_delivery_cons_amount + $trans_out_cons_amount;

				//___AOP
				$cumulative_delivery_aop_amount = $delivery_aop_amount- $issue_return_aop_amount;
				$total_delivery_aop_amount_out = $cumulative_delivery_aop_amount + $trans_out_aop_amount;

				$balance_qnty = $row[csf('receive_qnty')] - $total_delivery_out;
				$balance_qnty = number_format($balance_qnty,2,".","");
				//echo "rcv = ".$row[csf('receive_qnty')]." , (deli= $delivery_qnty) - (iss ret = $issue_return_qnty) + (tr out =$trans_out_qnty)<br>";
				if($balance_qnty>0)
				{
					$order_stock_qnty = $row[csf('order_qnty')]-$total_delivery_order_out;
					$order_stock_amount = $row[csf('order_amount')]-$total_delivery_order_amount_out;
					$cons_stock_qnty = $row[csf('cons_quantity')]-$total_delivery_cons_qnty_out;
					$cons_stock_amount = $row[csf('cons_amount')]-$total_delivery_cons_amount_out;
					$aop_stock_amount = $row[csf('aop_amount')]-$total_delivery_aop_amount_out;

					$cons_rate = $cons_stock_amount/$cons_stock_qnty;
					$order_rate = $order_stock_amount/$order_stock_qnty;
					$cons_rate = number_format($cons_rate,2,".","");
					$order_rate = number_format($order_rate,2,".","");

					$aop_rate = $aop_stock_amount/$order_stock_qnty;
					$aop_rate = number_format($aop_rate,2,".","");

					$data=$row[csf('body_part_id')]."**".$composition_arr[$row[csf('fabric_description_id')]]."**".$row[csf('gsm')]."**".$row[csf('width')]."**".$row[csf('fabric_description_id')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('body_part_id')]."****".$row[csf('dia_width_type')]."**".$batch_arr[$row[csf('batch_id')]]."**".$row[csf('batch_id')]."**".$row[csf('is_sales')]."**".$row[csf('color_id')]."**".$row[csf('receive_qnty')]."**".$cumulative_delivery_qnty."___".$trans_out_qnty."**".$row[csf("prod_id")]."**".$row[csf('knitting_company')]."**".$row[csf("knitting_source")]."**".$row[csf('uom')]."**".$row[csf('fabric_shade')]."**".$row[csf('receive_date')].$addition_data."**".$cons_rate."**".$order_rate."**".$aop_rate;

					$fab_desc = $body_part[$row[csf('body_part_id')]].", ".$composition_arr[$row[csf('fabric_description_id')]].", ".$row[csf('gsm')].", ".$row[csf('width')];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $data; ?>")' style="cursor:pointer" >
						<td align="center"><? echo $i; ?></td>
						<td <? echo $batch_dispaly; ?>><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
						<td><? echo $fab_desc; ?></td>
						<td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
						<td <? echo $dia_w_type_dispaly; ?> align="center"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
						<td align="center"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
						<td align="center"><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></td>
						<? 
						if($variable_settings == 1)
						{
							?>
							<td align="center"><? echo $lib_floor_arr[$row[csf('floor')]]; ?></td>
							<td align="center"><? echo $lib_room_arr[$row[csf('room')]]; ?></td>
							<td align="center"><? echo $lib_rack_arr[$row[csf('rack_no')]]; ?></td>
							<td align="center"><? echo $lib_shelf_arr[$row[csf('shelf_no')]]; ?></td>
							<?
						}
						?>
						<td align="right" title="<? echo $title;?>"><? echo number_format($balance_qnty,2); ?></td>
					</tr>
					<?  
					$i++;
				}
			}
			?>
		</tbody>
	</table>
	<?
	exit;
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	// Data insert block start here

	//--------------------------------server side validation (ssv) start----------------------------------------
	if( str_replace("'","",$update_mst_id) != "" ) 
	{
		$gmts_rcv_no = sql_select("SELECT  recv_number from inv_receive_master where booking_id=$update_mst_id and entry_form=37 and receive_basis=10 and status_active=1");
		
		if($gmts_rcv_no[0][csf("recv_number")] != ""){
			echo "20**Garments receive found.\nSave/update not allowed.\nReceive no: ".$gmts_rcv_no[0][csf("recv_number")];die;
		}
	}

	if($operation==1 || $operation==2)
	{
		$trans_sql = sql_select("SELECT a.prod_id,a.cons_quantity,a.cons_rate,a.cons_amount,b.avg_rate_per_unit,b.current_stock,b.stock_value, a.store_id, a.pi_wo_batch_no as batch_id from inv_transaction a, product_details_master b where a.status_active=1 and a.id=$update_trans_id and a.mst_id=$update_mst_id and a.prod_id=b.id");
		if (empty($trans_sql)) 
		{
			echo "20**Update/Delete not allowed";disconnect($con);die;
		}
		$before_prod_id=$before_delivery_qnty=$before_rate=$beforeAmount=$before_store_id=$before_batch_id="";
		$before_curr_stock_qnty=0;$before_curr_stock_value=0;
		foreach( $trans_sql as $row)
		{
			$before_prod_id 		= $row[csf("prod_id")];
			$before_delivery_qnty 	= $row[csf("cons_quantity")]; //stock qnty
			$before_rate 			= $row[csf("cons_rate")];
			$beforeAmount			= $row[csf("cons_amount")]; //stock value
			$before_curr_stock_qnty	= $row[csf("current_stock")];
			$before_curr_stock_value = $row[csf("stock_value")];
			$before_store_id		= $row[csf("store_id")];
			$before_batch_id		= $row[csf("batch_id")];
		}

		$max_trans_query = sql_select("SELECT max(case when transaction_type in (1,4,5) then transaction_date else null end) as max_date, max(id) as max_id from inv_transaction where prod_id =$before_prod_id and store_id=$before_store_id and item_category=2 and status_active=1");// and pi_wo_batch_no=$before_batch_id
		$max_recv_date = $max_trans_query[0][csf('max_date')];
		$max_trans_id = $max_trans_query[0][csf('max_id')];

		if($max_trans_id > str_replace("'", "", $update_trans_id))
		{
			//echo "20**Next transaction found of this store and product. update/delete not allowed.";
			//die;
		}
		$update_cond = " and e.id <> $update_trans_id";

		if($max_recv_date != "")
		{
			$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
			$issue_date = date("Y-m-d", strtotime(str_replace("'","",$txt_delivery_date)));
			if ($issue_date < $max_recv_date)
			{
				echo "20**Issue Date Can not Be Less Than Last Receive Date Of This Lot";
				die;
			}
		}
	}
	//echo "10**failed".str_replace("'","",$update_mst_id);die;

	$variable_settings = 1;
	$booking_without_order = str_replace("'","",$hdn_booking_without_order);
	if($variable_settings == 1)
	{
		$select_fields_rcv  = " b.floor, b.room,b.rack_no,b.shelf_no, ";
		$select_fields_trans  = " b.to_floor_id as floor,b.to_room as room, b.to_rack as rack_no,b.to_shelf as shelf_no, ";
		$group_field_rcv = " ,b.floor, b.room,b.rack_no,b.shelf_no ";
		$group_field_trans = " ,b.to_floor_id,b.to_room, b.to_rack,b.to_shelf ";

		$select_fields_all = " x.floor, x.room, x.rack_no, x.shelf_no, ";
		$group_fields_all = " ,x.floor, x.room, x.rack_no, x.shelf_no ";

		$rcvIssDeli_Con="";	
		$store_update_upto=str_replace("'","",$store_update_upto);
		if($store_update_upto > 1)
		{
			if($store_update_upto==5)
			{
				if(str_replace("'","",$cbo_floor)!=0){$rcvIssDeli_Con= " and b.floor=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$rcvIssDeli_Con.= " and b.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$rcvIssDeli_Con.= " and b.rack_no=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$rcvIssDeli_Con.= " and b.shelf_no=$txt_shelf" ;}
			}
			else if($store_update_upto==4)
			{
				if(str_replace("'","",$cbo_floor)!=0){$rcvIssDeli_Con= " and b.floor=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$rcvIssDeli_Con.= " and b.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$rcvIssDeli_Con.= " and b.rack_no=$txt_rack" ;}
			}
			else if($store_update_upto==3)
			{
				if(str_replace("'","",$cbo_floor)!=0){$rcvIssDeli_Con= " and b.floor=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$rcvIssDeli_Con.= " and b.room=$cbo_room" ;}
			}
			else if($store_update_upto==2)
			{
				if(str_replace("'","",$cbo_floor)!=0){$rcvIssDeli_Con= " and b.floor=$cbo_floor" ;}
			}
		}

		$transtoCon="";	
		$store_update_upto=str_replace("'","",$store_update_upto);
		if($store_update_upto > 1)
		{
			if($store_update_upto==5)
			{
				if(str_replace("'","",$cbo_floor)!=0){$transtoCon= " and b.to_floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$transtoCon.= " and b.to_room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$transtoCon.= " and b.to_rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$transtoCon.= " and b.to_shelf=$txt_shelf" ;}
			}
			else if($store_update_upto==4)
			{
				if(str_replace("'","",$cbo_floor)!=0){$transtoCon= " and b.to_floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$transtoCon.= " and b.to_room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$transtoCon.= " and b.to_rack=$txt_rack" ;}
			}
			else if($store_update_upto==3)
			{
				if(str_replace("'","",$cbo_floor)!=0){$transtoCon= " and b.to_floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$transtoCon.= " and b.to_room=$cbo_room" ;}
			}
			else if($store_update_upto==2)
			{
				if(str_replace("'","",$cbo_floor)!=0){$trans_outCon= " and b.to_floor_id=$cbo_floor" ;}
			}
		}

		$transCon="";	
		$store_update_upto=str_replace("'","",$store_update_upto);
		if($store_update_upto > 1)
		{
			if($store_update_upto==5)
			{
				if(str_replace("'","",$cbo_floor)!=0){$transCon= " and b.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$transCon.= " and b.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$transCon.= " and b.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$transCon.= " and b.shelf=$txt_shelf" ;}
			}
			else if($store_update_upto==4)
			{
				if(str_replace("'","",$cbo_floor)!=0){$transCon= " and b.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$transCon.= " and b.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$transCon.= " and b.rack=$txt_rack" ;}
			}
			else if($store_update_upto==3)
			{
				if(str_replace("'","",$cbo_floor)!=0){$transCon= " and b.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$transCon.= " and b.room=$cbo_room" ;}
			}
			else if($store_update_upto==2)
			{
				if(str_replace("'","",$cbo_floor)!=0){$trans_outCon= " and b.floor_id=$cbo_floor" ;}
			}
		}
	}

	if($db_type==0){
		$castingCond_order_id="cast(c.po_breakdown_id as CHAR(4000)) as order_id";
		$castingCond_to_order_id="cast(a.to_order_id as CHAR(4000)) as order_id";
		//$booking_without_order_cond = " and (a.booking_without_order=0 or a.booking_without_order ='') ";
	}
	else{
		$castingCond_order_id="cast(c.po_breakdown_id as varchar2(4000)) as order_id";
		$castingCond_to_order_id="cast(a.to_order_id as varchar2(4000)) as order_id";
		//$booking_without_order_cond = " and (a.booking_without_order=0 or a.booking_without_order is null) ";
	}
	if($booking_without_order == 1 )
	{
		$sql_rcv = "select a.knitting_company,a.knitting_source, a.store_id, b.prod_id,b.batch_id,b.order_id,b.body_part_id, b.fabric_description_id, b.gsm, b.width,b.color_id, b.dia_width_type,b.is_sales,b.uom, b.fabric_shade, b.floor, b.room,b.rack_no,b.shelf_no, sum(b.receive_qnty) as receive_qnty, sum(d.cons_quantity) as cons_quantity
		 from inv_receive_master a,inv_transaction d,pro_finish_fabric_rcv_dtls b
		 where a.id=b.mst_id and d.id=b.trans_id  and b.order_id = ".$hdn_fso_id." and a.store_id = ".$cbo_store_name." $rcvIssDeli_Con and b.trans_id >0 and a.item_category=2 and a.entry_form in (7,225) and b.is_sales=1 and a.status_active=1 and b.status_active=1 and b.prod_id= $hidden_product_id and b.body_part_id=$cbo_body_part and b.dia_width_type = $txt_dia_width_type and b.fabric_shade =$cbo_fabric_shade and b.batch_id =$hdn_batch_id and a.company_id=$cbo_company_id
		 group by a.knitting_company,a.knitting_source, a.store_id, b.prod_id,b.batch_id,b.order_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.dia_width_type,b.is_sales,
		 b.uom,b.fabric_shade $group_field_rcv ";

		 //and a.knitting_company = $hdn_knitting_company and a.knitting_source = $hdn_knitting_source and a.booking_without_order=1
	}
	else
	{
		$sql_rcv = "select a.knitting_company,a.knitting_source, a.store_id, b.prod_id,b.batch_id, $castingCond_order_id,b.body_part_id, b.fabric_description_id, b.gsm,b.width,b.color_id, b.dia_width_type,b.is_sales,b.uom,b.fabric_shade, b.floor, b.room,b.rack_no,b.shelf_no, sum(c.quantity) as receive_qnty, sum(d.cons_quantity) as cons_quantity
		from inv_receive_master a,inv_transaction d,pro_finish_fabric_rcv_dtls b,order_wise_pro_details c where a.id=b.mst_id and d.id=b.trans_id and b.id = c.dtls_id and c.po_breakdown_id = ".$hdn_fso_id." and a.store_id = ".$cbo_store_name." $rcvIssDeli_Con  $booking_without_order_cond and b.prod_id= $hidden_product_id and b.body_part_id=$cbo_body_part and b.dia_width_type = $txt_dia_width_type and b.fabric_shade =$cbo_fabric_shade and b.batch_id =$hdn_batch_id and a.company_id=$cbo_company_id and a.item_category=2 and a.entry_form in (7,225) and c.entry_form in (7,225) and b.is_sales=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 group by a.knitting_company,a.knitting_source, a.store_id, b.prod_id,b.batch_id,c.po_breakdown_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.dia_width_type,b.is_sales,b.uom,b.fabric_shade $group_field_rcv ";

		//and a.knitting_company = $hdn_knitting_company and a.knitting_source = $hdn_knitting_source
	}

	$sql .= "select x.knitting_company, x.knitting_source, x.store_id, x.prod_id, x.batch_id, x.order_id, x.body_part_id, x.fabric_description_id, x.gsm, x.width, x.color_id, x.dia_width_type, x.is_sales, x.uom, x.fabric_shade, $select_fields_all sum(receive_qnty) as receive_qnty, sum(x.cons_quantity) cons_quantity from (";

	$sql .= $sql_rcv ." union all
	select a.company_id as knitting_company, 1 as knitting_source, b.to_store as store_id, b.from_prod_id as prod_id, b.to_batch_id as batch_id, $castingCond_to_order_id , b.body_part_id, b.feb_description_id as fabric_description_id, b.gsm, b.dia_width as width,b.color_id, b.dia_width_type, 1 as is_sales, b.uom, b.fabric_shade, $select_fields_trans  
	sum(b.transfer_qnty) as receive_qnty, sum(d.cons_quantity) as cons_quantity
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction d 
	where a.id=b.mst_id and b.to_trans_id=d.id and a.entry_form in(230) and a.to_order_id =".$hdn_fso_id." and b.to_store = ".$cbo_store_name." $transtoCon and b.from_prod_id= $hidden_product_id and b.body_part_id=$cbo_body_part and b.dia_width_type = $txt_dia_width_type and b.fabric_shade =$cbo_fabric_shade and b.to_batch_id =$hdn_batch_id and a.company_id = $cbo_company_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
	group by a.company_id, b.to_store, b.from_prod_id, b.to_batch_id, a.to_order_id, b.body_part_id, b.feb_description_id, b.gsm, b.dia_width,b.color_id, b.dia_width_type, b.uom, b.fabric_shade $group_field_trans";

	$sql .= " ) x group by x.knitting_company, x.knitting_source, x.store_id, x.prod_id, x.batch_id, x.order_id, x.body_part_id, x.fabric_description_id, x.gsm, x.width, x.color_id, x.dia_width_type, x.is_sales, x.uom, x.fabric_shade $group_fields_all ";
	//echo "10**";
	//echo $sql;die;

	$data_array=sql_select($sql);

	$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
	foreach ($data_array as $val) 
	{
		if($val[csf("floor")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor")];
		if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
		if($val[csf("rack_no")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack_no")];
		if($val[csf("shelf_no")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("shelf_no")];
		$receive_qnty_arr[$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id] += $val[csf('receive_qnty')];
	}
	
	
	$delivery_sql = sql_select("select a.knit_dye_source,a.knit_dye_company, b.uom, sum(b.issue_qnty) delivery_qnty, sum(e.cons_quantity) as cons_quantity, b.floor,b.room,b.rack_no,b.shelf_no, c.color_id,d.detarmination_id determination_id,d.gsm,d.dia_width dia from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c,product_details_master d, inv_transaction e where a.entry_form in (224,287) and a.id=b.mst_id and b.batch_id=c.id and b.prod_id=d.id and b.trans_id=e.id and a.status_active='1' and a.is_deleted='0' and b.order_id =$hdn_fso_id and b.store_id=".$cbo_store_name." $rcvIssDeli_Con and b.batch_id =$hdn_batch_id and b.body_part_id=$cbo_body_part and b.prod_id= $hidden_product_id and b.width_type = $txt_dia_width_type and b.fabric_shade =$cbo_fabric_shade and b.status_active=1 and c.status_active=1 and d.status_active=1 $update_cond group by a.knit_dye_source,a.knit_dye_company, b.uom, b.floor, b.room, b.rack_no, b.shelf_no, c.color_id, d.detarmination_id, d.gsm, d.dia_width");
	//and a.knit_dye_company = $hdn_knitting_company and a.knit_dye_source = $hdn_knitting_source

	$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
	foreach ($delivery_sql as  $val)
	{
		if($val[csf("floor")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor")];
		if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
		if($val[csf("rack_no")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack_no")];
		if($val[csf("shelf_no")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("shelf_no")];
		$delivery_arr[$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id] += $val[csf("delivery_qnty")];
	}

	$trans_out_sql = "SELECT b.uom, b.floor_id, b.room, b.rack, b.shelf, b.dia_width as width, b.gsm, sum(b.transfer_qnty) as quantity, sum(c.cons_quantity) as cons_quantity
	FROM inv_item_transfer_mst a, inv_item_transfer_dtls b ,inv_transaction c 
	WHERE a.id=b.mst_id  and b.to_trans_id=c.id and a.entry_form =230 and a.from_order_id =".$hdn_fso_id." and b.from_store = ".$cbo_store_name." $transCon and b.body_part_id=$cbo_body_part and b.from_prod_id= $hidden_product_id and b.dia_width_type = $txt_dia_width_type and b.fabric_shade =$cbo_fabric_shade and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and b.batch_id =$hdn_batch_id
	group by b.uom, b.floor_id, b.room, b.rack, b.shelf, b.dia_width, b.gsm";
	$trans_out_Data = sql_select($trans_out_sql);
	$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
	foreach ($trans_out_Data as $val) 
	{
		if($val[csf("floor_id")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor_id")];
		if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
		if($val[csf("rack")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack")];
		if($val[csf("shelf")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("shelf")];
		$trans_out_arr[$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id] += $val[csf("quantity")];
		
	}

	$issue_return_sql = sql_select("select b.room, b.floor,b.rack_no, b.shelf_no, sum(e.quantity) as qnty, sum(d.cons_quantity) as cons_quantity
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction d , order_wise_pro_details e where a.id=b.mst_id  and b.mst_id=d.mst_id and b.trans_id = d.id and d.id = e.trans_id and b.id = e.dtls_id  and d.item_category=2 and d.transaction_type in (4) and a.entry_form in (233) and e.entry_form = 233 and e.po_breakdown_id =$hdn_fso_id and d.store_id=".$cbo_store_name." $rcvIssDeli_Con and b.body_part_id=$cbo_body_part and b.prod_id= $hidden_product_id and b.dia_width_type = $txt_dia_width_type and b.fabric_shade =$cbo_fabric_shade and b.batch_id =$hdn_batch_id and e.is_sales=1 group by b.room,b.floor,b.rack_no, b.shelf_no");
	//and a.knitting_company = $hdn_knitting_company and a.knitting_source = $hdn_knitting_source

	$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
	foreach ($issue_return_sql as $val) 
	{
		if($val[csf("floor")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor")];
		if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
		if($val[csf("rack_no")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack_no")];
		if($val[csf("shelf_no")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("shelf_no")];
		$issue_return_qnty_arr[$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id]+= $val[csf("qnty")];
	}

	$cbo_floor = (str_replace("'", "", $cbo_floor) =="")? 0 :str_replace("'", "", $cbo_floor);
	$cbo_room = (str_replace("'", "", $cbo_room)=="")? 0 :str_replace("'", "", $cbo_room);
	$txt_rack = (str_replace("'", "", $txt_rack)=="")? 0 :str_replace("'", "", $txt_rack);
	$txt_shelf = (str_replace("'", "", $txt_shelf)=="")? 0 :str_replace("'", "", $txt_shelf);

	$receive_qnty = $receive_qnty_arr[$cbo_floor][$cbo_room][$txt_rack][$txt_shelf];
	$delivery_qnty = $delivery_arr[$cbo_floor][$cbo_room][$txt_rack][$txt_shelf];
	$trans_out_qnty = $trans_out_arr[$cbo_floor][$cbo_room][$txt_rack][$txt_shelf];
	$issue_return_qnty = $issue_return_qnty_arr[$cbo_floor][$cbo_room][$txt_rack][$txt_shelf];

	$stock_qnty  = ($receive_qnty + $issue_return_qnty) - ($delivery_qnty + $trans_out_qnty);
	$stock_qnty = number_format($stock_qnty,2,'.','');

	//echo "10**";
	//echo "rcv=$receive_qnty,issue & rcv return=$delivery_qnty,trans out=$trans_out_qnty, iss ret=$issue_return_qnty";
	//die;
	if(str_replace("'","",$txt_Delivery_qnty)>$stock_qnty)
	{
		echo "17**Issue Quantity Exceeds The Current Stock Quantity.\nStock : $stock_qnty";
		die;
	}
	
	//----------------------------(ssv) ends-------------------------------------------------------


	if($operation==0) // Insert Here---------------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$delivery_date = date("Y-m-d", strtotime(str_replace("'","",$txt_delivery_date)));
		$hdn_receive_date = date("Y-m-d", strtotime(str_replace("'","",$hdn_receive_date)));
		if ($delivery_date < $hdn_receive_date) 
		{
			echo "20**Delivery Date can not be less that Receive Date";
			disconnect($con);die;
		}
		$finish_fabric_issue_num=''; $finish_update_id=''; $product_id=$hidden_product_id;
		$stock_sql=sql_select("select current_stock, stock_value, color from product_details_master where id=$product_id");
		$curr_stock_qnty=$stock_sql[0][csf('current_stock')];
		$curr_stock_qnty = number_format($curr_stock_qnty,2,'.','');

		$curr_stock_value=$stock_sql[0][csf('stock_value')];
		$curr_stock_value = number_format($curr_stock_value,2,'.','');

		$color_id=$stock_sql[0][csf('color')];

		if(str_replace("'","",$txt_Delivery_qnty)>$curr_stock_qnty)
		{
			echo "17**Issue Quantity Exceeds The Current Stock Quantity";
			disconnect($con);die;
		}

		if(str_replace("'","",$update_mst_id) == ""){
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			$new_system_id = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,str_replace("'","",$cbo_company_id),'FDG',224,date("Y",time())));

			$field_array="id, issue_number_prefix, issue_number_prefix_num, issue_number, issue_purpose, entry_form, item_category, company_id, issue_date, knit_dye_company,knit_dye_source, location_id, buyer_id, inserted_by, insert_date,store_id, supplier_id,fso_no,fso_id, booking_no, booking_id,vehicle_no,driver_name";

			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',10,224,2,".$cbo_company_id.",". $txt_delivery_date.",".$hdn_knitting_company.",".$hdn_knitting_source.",".$cbo_location.",".$hdn_buyer_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_store_name.",".$cbo_party.",".$txt_fso_no.",".$hdn_fso_id.",".$txt_booking_no.",".$hdn_booking_id.",".$txt_vehicle_no.",".$txt_driver_name.")";
			$finish_fabric_issue_num=$new_system_id[0];
			$finish_update_id=$id;
		}else{
			$field_array_update="issue_date*VEHICLE_NO*DRIVER_NAME*updated_by*update_date";			
			$data_array_update=$txt_delivery_date."*".$txt_vehicle_no."*".$txt_driver_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$finish_fabric_issue_num=str_replace("'","",$txt_system_id);
			$finish_update_id=str_replace("'","",$update_mst_id);
		}

		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);  
		$hidden_fabric_rate=str_replace("'","",$hidden_fabric_rate);
		$hidden_fabric_order_rate=str_replace("'","",$hidden_fabric_order_rate);
		$cons_amount=$hidden_fabric_rate*str_replace("'","",$txt_Delivery_qnty);
		$order_amount=$hidden_fabric_order_rate*str_replace("'","",$txt_Delivery_qnty);
		$aop_amount=str_replace("'","",$hidden_fabric_aop_rate)*str_replace("'","",$txt_Delivery_qnty);

		$field_array_trans="id, mst_id, company_id, pi_wo_batch_no, prod_id, item_category,transaction_type, transaction_date,cons_uom, cons_quantity, cons_rate, cons_amount,order_qnty, order_rate,order_amount,no_of_roll,fabric_shade, store_id, rack, self,floor_id,room, inserted_by, insert_date";
		
		$data_array_trans="(".$id_trans.",".$finish_update_id.",".$cbo_company_id.",".$hdn_batch_id.",".$hidden_product_id.",2,2,".$txt_delivery_date.",".$hdn_uom.",".$txt_Delivery_qnty.",'".$hidden_fabric_rate."','".$cons_amount."',".$txt_Delivery_qnty.",'".$hidden_fabric_order_rate."','".$order_amount."',".$txt_no_of_roll.",".$cbo_fabric_shade.",".$cbo_store_name.",".$txt_rack.",".$txt_shelf.",".$cbo_floor.",".$cbo_room.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$id_dtls=return_next_id_by_sequence( "INV_FIN_FAB_ISSUE_DTLS_PK_SEQ", "inv_finish_fabric_issue_dtls", $con) ;
		$field_array_dtls="id, mst_id, trans_id, batch_id, prod_id, uom, issue_qnty,rate,rate_in_usd,fabric_shade,store_id,no_of_roll, body_part_id,rack_no,shelf_no,floor,room, order_id,inserted_by,insert_date,width_type, aop_rate, aop_amount, remarks";
		
		$data_array_dtls="(".$id_dtls.",".$finish_update_id.",".$id_trans.",".$hdn_batch_id.",".$hidden_product_id.",".$hdn_uom.",".$txt_Delivery_qnty.",".$hidden_fabric_rate.",".$hidden_fabric_order_rate.",".$cbo_fabric_shade.",".$cbo_store_name.",".$txt_no_of_roll.",".$cbo_body_part.",".$txt_rack.",".$txt_shelf.",".$cbo_floor.",".$cbo_room.",".$hdn_fso_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_dia_width_type.",".$hidden_fabric_aop_rate.",'".$aop_amount."',".$txt_remarks.")";

		$field_array_prod_update="last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";		
		$curr_stock_qnty=$curr_stock_qnty-str_replace("'","",$txt_Delivery_qnty);	
		$curr_stock_value=$curr_stock_value-str_replace("'","",$cons_amount);	

		if($curr_stock_qnty > 0)
		{
			$curr_rate = $curr_stock_value/$curr_stock_qnty;
		}else{
			$curr_rate =0;
			$curr_stock_value=0;
		}
			
		$data_array_prod_update=$txt_Delivery_qnty."*".$curr_stock_qnty."*'".$curr_stock_value."'*'".$curr_rate."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date, is_sales";
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		if($i==0) $add_comma=""; else $add_comma=",";
		$data_array_prop.="$add_comma(".$id_prop.",".$id_trans.",2,224,".$id_dtls.",".$hdn_fso_id.",".$product_id.",'".$color_id."',".$txt_Delivery_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";

		$save_string= array_filter(explode(",", str_replace("'","",trim($save_data))));
		$field_array_job_proportionate="id, trans_id, trans_type, entry_form, dtls_id, job_id, prod_id, color_id, pub_shipment_date, job_wise_qnty, inserted_by, insert_date, is_sales";
		for($i=0;$i<count($save_string);$i++)
		{
			$job_dtls=explode("**",$save_string[$i]);
			$job_id=$job_dtls[0];
			$job_wise_qnty=$job_dtls[1];
			$pub_ship_date=$job_dtls[2];

			if ($db_type == 0) {
				$pub_ship_date =  change_date_format($pub_ship_date, "yyyy-mm-dd", "-");
			} else {
				$pub_ship_date =  change_date_format($pub_ship_date, '', '', 1);
			}


			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			if($data_array_job_prop!="" ) $data_array_job_prop.=",";
			$data_array_job_prop.="(".$id_prop.",".$id_trans.",2,224,".$id_dtls.",".$job_id.",".$product_id.",'".$color_id."','".$pub_ship_date."',".$job_wise_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0)";
		}
		




		if(str_replace("'","",$update_mst_id) == "")
		{
			$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0; 
		}
		else
		{
			$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_mst_id,1);
			if($rID) $flag=1; else $flag=0; 
		}

		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		$rID3=sql_insert("inv_finish_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 

		$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
		if($flag==1) 
		{
			if($prod) $flag=1; else $flag=0; 
		} 

		if($data_array_prop!="")
		{
			//echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
			$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			} 
		}

		if($data_array_job_prop!="")
		{
			//echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
			$rID5=sql_insert("order_wise_pro_details",$field_array_job_proportionate,$data_array_job_prop,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}
		//echo "10**".$field_array_update.'<br>'.$data_array_update.'<br>'.$update_mst_id;oci_rollback($con);die;
		//echo "10**".$rID."##".$rID2."##".$rID3."##".$prod."##".$rID4."##".$rID5."<br>".$data_array_job_prop;oci_rollback($con); die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$finish_update_id."**".$finish_fabric_issue_num;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "0**".$finish_update_id."**".$finish_fabric_issue_num;
			}
			else
			{
				oci_rollback($con);
				echo "5**0**";
			}
		}

		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$product_id=$hidden_product_id; $color_id=''; $curr_stock_qnty=''; $latest_current_stock='';

		$field_array_update="issue_date*fso_no*fso_id*booking_no*booking_id*vehicle_no*driver_name*updated_by*update_date";		
		$data_array_update=$txt_delivery_date."*".$txt_fso_no."*".$hdn_fso_id."*".$txt_booking_no."*".$hdn_booking_id."*".$txt_vehicle_no."*".$txt_driver_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$field_array_trans="prod_id*pi_wo_batch_no*transaction_date*fabric_shade*store_id*cons_uom*cons_quantity* cons_rate* cons_amount*order_qnty*order_rate*order_amount*rack*self*floor_id*room*updated_by* update_date";


		$hidden_fabric_rate=str_replace("'","",$hidden_fabric_rate);
		$hidden_fabric_order_rate=str_replace("'","",$hidden_fabric_order_rate );
		$cons_amount=$hidden_fabric_rate*str_replace("'","",$txt_Delivery_qnty);
		$order_amount=$hidden_fabric_order_rate*str_replace("'","",$txt_Delivery_qnty);
		$aop_amount=str_replace("'","",$hidden_fabric_aop_rate)*str_replace("'","",$txt_Delivery_qnty);
		
		$stock_sql=sql_select("select current_stock, stock_value, color from product_details_master where id=$product_id");
		$curr_stock_qnty=$stock_sql[0][csf('current_stock')];
		$curr_stock_qnty = number_format($curr_stock_qnty,2,'.','');

		$curr_stock_value=$stock_sql[0][csf('stock_value')];
		$curr_stock_value = number_format($curr_stock_value,2,'.','');

		$color_id=$stock_sql[0][csf('color')];
		$field_array_prod_update="last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";

		if(str_replace("'","",$product_id)==str_replace("'","",$hidden_pre_product_id))
		{
			$latest_current_stock=$curr_stock_qnty+str_replace("'", '',$hdn_Delivery_qnty);
			$curr_stock_qnty=$curr_stock_qnty-str_replace("'","",$txt_Delivery_qnty)+str_replace("'", '',$hdn_Delivery_qnty);
			$curr_stock_value=$curr_stock_value-str_replace("'","",$txt_Delivery_qnty)+str_replace("'", '',$hdn_Delivery_qnty);

			if($curr_stock_qnty > 0)
			{
				$curr_rate = $curr_stock_value/$curr_stock_qnty;
			}else{
				$curr_rate =0;
				$curr_stock_value=0;
			}

			$data_array_prod_update=$txt_Delivery_qnty."*".$curr_stock_qnty."*'".$curr_stock_value."'*'".$curr_rate."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		else
		{

			$adjust_stock_sql=sql_select("select current_stock, stock_value, color from product_details_master where id=$hidden_pre_product_id");
			$adjust_stock_qnty=$adjust_stock_sql[0][csf('current_stock')];
			$adjust_stock_qnty = number_format($adjust_stock_qnty,2,'.','');

			$adjust_stock_value=$adjust_stock_sql[0][csf('stock_value')];
			$adjust_stock_value = number_format($adjust_stock_value,2,'.','');

			//$stock=return_field_value("current_stock","product_details_master","id=$hidden_pre_product_id");
			$adjust_curr_stock=$adjust_stock_qnty+str_replace("'", '',$hdn_Delivery_qnty);
			$adjust_stock_value=$adjust_stock_value+str_replace("'", '',$hdn_Delivery_amount);

			if($adjust_curr_stock > 0)
			{
				$adjust_stock_rate = $adjust_stock_value/$adjust_curr_stock;
			}else{
				$adjust_stock_rate =0;
				$adjust_stock_value=0;
			}


			$latest_current_stock=$curr_stock_qnty;
			
			$curr_stock_qnty=$curr_stock_qnty-str_replace("'","",$txt_Delivery_qnty);	
			if($curr_stock_qnty > 0)
			{
				$curr_rate = $curr_stock_value/$curr_stock_qnty;
			}else{
				$curr_rate =0;
				$curr_stock_value=0;
			}

			$data_array_prod_update=$txt_Delivery_qnty."*".$curr_stock_qnty."*'".$curr_stock_value."'*'".$curr_rate."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}

		if(str_replace("'","",$txt_Delivery_qnty)>$latest_current_stock)
		{
			echo "17**Issue Quantity Exceeds The Current Stock Quantity"; 
			disconnect($con);die;			
		}

		$issue_return_arr = sql_select("select a.recv_number,a.booking_id, a.booking_no, b.issue_dtls_id, b.receive_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id = b.mst_id and a.entry_form = 233 and b.issue_dtls_id = $update_dtls_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0");
		if(!empty($issue_return_arr))
		{
			//echo "10**f1=".$issue_return_arr[0][csf("receive_qnty")];die;
			if($issue_return_arr[0][csf("receive_qnty")] > str_replace("'","",$txt_Delivery_qnty))
			{
				echo "17**Issue quantity can not be less than Issue return quantity.\nIssue return challan no: ".$issue_return_arr[0][csf("recv_number")]."\nReturn quantity: ".$issue_return_arr[0][csf("receive_qnty")]; 
				disconnect($con);die;
			}
		}
		
		$data_array_trans=$product_id."*".$hdn_batch_id."*".$txt_delivery_date."*".$cbo_fabric_shade."*".$cbo_store_name."*".$hdn_uom."*".$txt_Delivery_qnty."*'".$hidden_fabric_rate."'*'".$cons_amount."'*".$txt_Delivery_qnty."*'".$hidden_fabric_order_rate."'*'".$order_amount."'*".$txt_rack."*".$txt_shelf."*".$cbo_floor."*".$cbo_room."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_dtls="batch_id*prod_id*uom*issue_qnty*rate*rate_in_usd*fabric_shade*store_id*no_of_roll*body_part_id*rack_no*shelf_no*floor*room*updated_by*update_date*width_type*aop_rate*aop_amount*remarks";
		$hidden_fabric_rate=str_replace("'","",$hidden_fabric_rate);
		$data_array_dtls=$hdn_batch_id."*".$product_id."*".$hdn_uom."*".$txt_Delivery_qnty."*".$hidden_fabric_rate."*".$hidden_fabric_order_rate."*".$cbo_fabric_shade."*".$cbo_store_name."*".$txt_no_of_roll."*".$cbo_body_part."*".$txt_rack."*".$txt_shelf."*".$cbo_floor."*".$cbo_room."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_dia_width_type."*".$hidden_fabric_aop_rate."*'".$aop_amount."'*".$txt_remarks."";

		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date, is_sales";
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		if($i==0) $add_comma=""; else $add_comma=",";
		$data_array_prop.="$add_comma(".$id_prop.",".$update_trans_id.",2,224,".$update_dtls_id.",".$hdn_fso_id.",".$product_id.",'".$color_id."',".$txt_Delivery_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";

		$save_string= array_filter(explode(",", str_replace("'","",trim($save_data))));
		$field_array_job_proportionate="id, trans_id, trans_type, entry_form, dtls_id, job_id, prod_id, color_id, pub_shipment_date, job_wise_qnty, inserted_by, insert_date, is_sales";
		for($i=0;$i<count($save_string);$i++)
		{
			$job_dtls=explode("**",$save_string[$i]);
			$job_id=$job_dtls[0];
			$job_wise_qnty=$job_dtls[1];
			$pub_ship_date=$job_dtls[2];

			if ($db_type == 0) {
				$pub_ship_date =  change_date_format($pub_ship_date, "yyyy-mm-dd", "-");
			} else {
				$pub_ship_date =  change_date_format($pub_ship_date, '', '', 1);
			}


			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			if($data_array_job_prop!="" ) $data_array_job_prop.=",";
			$data_array_job_prop.="(".$id_prop.",".$update_trans_id.",2,224,".$update_dtls_id.",".$job_id.",".$product_id.",'".$color_id."','".$pub_ship_date."',".$job_wise_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0)";
		}

		//$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and trans_id=$update_trans_id and entry_form=18",0);

		$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_mst_id,1);
		if($rID) $flag=1; else $flag=0; 
		
		if(str_replace("'","",$product_id)==str_replace("'","",$hidden_pre_product_id))
		{
			$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			if($flag==1) 
			{
				if($prod) $flag=1; else $flag=0; 
			}
		}
		else
		{
			$adjust_prod=sql_update("product_details_master","current_stock*stock_value*avg_rate_per_unit",$adjust_curr_stock."*'".$adjust_stock_value."'*'".$adjust_stock_rate."'","id",$hidden_pre_product_id,0);
			if($flag==1) 
			{
				if($adjust_prod) $flag=1; else $flag=0; 
			}
			
			$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			if($flag==1) 
			{
				if($prod) $flag=1; else $flag=0; 
			} 
		}

		$rID2=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_trans_id);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}
		 
		$rID3=sql_update("inv_finish_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		
		$delete_roll=execute_query( "delete from pro_roll_details where dtls_id=$update_dtls_id and entry_form=18",0);
		if($flag==1) 
		{
			if($delete_roll) $flag=1; else $flag=0; 
		} 
		
		$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and trans_id=$update_trans_id and entry_form=224",0);
		if($flag==1) 
		{
			if($delete_prop) $flag=1; else $flag=0; 
		}
		
		if($data_array_prop!="")
		{
			$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			} 
		}

		if($data_array_job_prop!="")
		{
			$rID5=sql_insert("order_wise_pro_details",$field_array_job_proportionate,$data_array_job_prop,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}

		//echo "6**".$rID."##".$rID2."##".$rID3."##".$prod."##".$rID4."##".$prod."##".$rID5;oci_rollback($con);die;
		// echo "6**".$flag;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_mst_id)."**".str_replace("'","",$txt_system_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**1";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".str_replace("'","",$update_mst_id)."**".str_replace("'","",$txt_system_id);
			}
			else
			{
				oci_rollback($con);
				echo "6**1";
			}
		}	
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------
		
		$text_issue_rtn = sql_select("SELECT  recv_number from inv_receive_master where booking_id=$update_mst_id and entry_form=233 and status_active=1");
		
		if($text_issue_rtn[0][csf("recv_number")] != "")
		{
			echo "20**Issue return found delete not allowed.\nReturn no: ".$text_issue_rtn[0][csf("recv_number")];
			disconnect($con);die;
		}

		$update_trans_id = str_replace("'","",$update_trans_id);
		$product_id = str_replace("'","",$hidden_product_id);
		if( str_replace("'","",$update_trans_id) == "" )
		{
			echo "20**Delete not allowed.";disconnect($con); die;
		}
		else
		{
			/*$sql = sql_select("SELECT a.prod_id,a.cons_quantity,a.cons_rate,a.cons_amount,b.avg_rate_per_unit,b.current_stock,b.stock_value, a.store_id, a.pi_wo_batch_no from inv_transaction a, product_details_master b where a.status_active=1 and a.id=$update_trans_id and a.mst_id=$update_mst_id and a.prod_id=b.id");
			if (empty($sql)) 
			{
				echo "20**Delete not allowed";disconnect($con);die;
			}
			$before_prod_id=$delivery_qnty=$before_rate=$beforeAmount="";
			$curr_stock_qnty=0;
			foreach( $sql as $row)
			{
				$before_prod_id 		= $row[csf("prod_id")];
				$delivery_qnty 			= $row[csf("cons_quantity")]; //stock qnty
				$before_rate 			= $row[csf("cons_rate")];
				$beforeAmount			= $row[csf("cons_amount")]; //stock value
				$curr_stock_qnty		= $row[csf("current_stock")];
			}*/

			/*$stock_sql=sql_select("select current_stock, color from product_details_master where id=$before_prod_id");
			$curr_stock_qnty=$stock_sql[0][csf('current_stock')];*/
			$curr_stock_qnty = number_format($before_curr_stock_qnty,2,'.','');				
			$curr_stock_qnty=$curr_stock_qnty+$before_delivery_qnty;

			$curr_stock_value = number_format($before_curr_stock_value,2,'.','');				
			$curr_stock_value=$curr_stock_value+$beforeAmount;

			if($curr_stock_qnty > 0){
				$curr_rate = $curr_stock_value/$curr_stock_qnty;
			}else{
				$curr_rate =0;
			}

						
			
			$field_array = "updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

			$checkTransaction = sql_select("SELECT id from inv_finish_fabric_issue_dtls where status_active=1 and is_deleted=0 and mst_id = ".$update_mst_id." and id !=".$update_dtls_id."");
			//$issID = 1;
			if(count($checkTransaction) == 0)
			{
				$is_mst_del = sql_update("inv_issue_master", $field_array, $data_array, "id", $update_mst_id, 1);
				if($is_mst_del) $flag=1; else $flag=0;
			}

			$rID=sql_update("inv_transaction",$field_array,$data_array,"id",$update_trans_id,1);
			if($rID) $flag=1; else $flag=0;				

			$rID2=sql_update("inv_finish_fabric_issue_dtls",$field_array,$data_array,"id",$update_dtls_id,1);
			if($rID2) $flag=1; else $flag=0;

			$rID3=sql_update("order_wise_pro_details",$field_array,$data_array,"dtls_id*trans_id*entry_form","$update_dtls_id*$update_trans_id*224",1);
			if($rID3) $flag=1; else $flag=0;

			$field_array_prod_update="last_purchased_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
			$data_array_prod_update=$hdn_Delivery_qnty."*".$curr_stock_qnty."*'".$curr_stock_value."'*'".$curr_rate."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID4=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$before_prod_id,1);
			if($rID4) $flag=1; else $flag=0;
		}

		// echo "10**$rID##$rID2##$rID3##$rID4##$is_mst_del**$flag";
		// oci_rollback($con);disconnect($con);die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$update_mst_id)."**".str_replace("'","",$txt_system_id)."**".$is_mst_del;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**1";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "2**".str_replace("'","",$update_mst_id)."**".str_replace("'","",$txt_system_id)."**".$is_mst_del;
			}
			else
			{
				oci_rollback($con);
				echo "6**1";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="save_update_delete1"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	// Data insert block start here
	if($operation==0)
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$delivery_date = date("Y-m-d", strtotime(str_replace("'","",$txt_delivery_date)));
		$hdn_receive_date = date("Y-m-d", strtotime(str_replace("'","",$hdn_receive_date)));
		if ($delivery_date < $hdn_receive_date) 
		{
			echo "20**Delivery Date can not be less that Receive Date";
			disconnect($con);die;
		}

		//echo "10**$txt_delivery_date==$hdn_receive_date";die;

		if( str_replace("'","",$update_mst_id) == "" ) 
		{			
			$id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_MST_PK_SEQ", "pro_grey_prod_delivery_mst", $con);
			$new_mrr_number = explode("*", return_next_id_by_sequence("PRO_GREY_PROD_DELI_MST_PK_SEQ", "pro_grey_prod_delivery_mst",$con,1,$cbo_company_id,'FDG',224,date("Y",time())));

			$field_array="id,sys_number_prefix,sys_number_prefix_num,sys_number,entry_form,fin_pord_type,delevery_date,company_id,location_id,buyer_id,knitting_company,knitting_source,inserted_by,insert_date,party_name,store_id";

			$data_array="(".$id.",'".$new_mrr_number[1]."','".$new_mrr_number[2]."','".$new_mrr_number[0]."',224,0,".$txt_delivery_date.",".$cbo_company_id.",".$cbo_location.",".$hdn_buyer_id.",".$hdn_knitting_company.",".$hdn_knitting_source.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',".$cbo_party.",".$cbo_store_name.")";
		}

		$field_array_dtls="id,mst_id,entry_form,program_no,product_id,job_no,order_id,determination_id,gsm,dia,current_delivery,batch_id,inserted_by,insert_date,is_sales,bodypart_id,color_id,width_type,within_group,floor_id,room,rack,shelf,uom,fabric_shade,roll_no";
		$dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_delivery_dtls", $con);
		$data_array_dtls="(".$dtls_id.",".$id.",224,".$hdn_batch_id.",".$hidden_product_id.",".$txt_po_job.",".$hdn_fso_id.",".$txt_fabric_description_id.",".$txt_gsm.",".$txt_dia.",".$txt_Delivery_qnty.",".$hdn_batch_id.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',1,".$cbo_body_part.",".$txt_color_id.",".$txt_dia_width_type.",".$hdn_within_group.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$hdn_uom.",".$cbo_fabric_shade.",".$txt_no_of_roll.")";

		$rID=$rID2=true;
		if( str_replace("'","",$update_mst_id) == "" )
		{
			$rID=sql_insert("pro_grey_prod_delivery_mst",$field_array,$data_array,1); 
		}
		$rID2=sql_insert("pro_grey_prod_delivery_dtls",$field_array_dtls,$data_array_dtls,1);
		//echo "10**insert into pro_grey_prod_delivery_mst (".$field_array.") values ".$data_array;die;
		//echo "10**".$rID."##".$rID2;die;
		if($db_type==0)//
		{
			if($rID && $rID2 )
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$new_mrr_number[0])."**".str_replace("'",'',$ref_dtls_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id);
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 )
			{
				oci_commit($con); 
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$new_mrr_number[0])."**".str_replace("'",'',$ref_dtls_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'",'',$id);
			}
		}
		disconnect($con);
	}

	if($operation==1) // update 
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		if( str_replace("'","",$update_mst_id) !="" ) 
		{
			$field_array_up = "delevery_date*company_id*location_id*store_id*updated_by*update_date";
			$data_array_up = $txt_delivery_date . "*" . $cbo_company_id . "*" . $cbo_location . "*" . $cbo_store_name . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			$field_array_dtls_up = "roll_no*current_delivery*floor_id*room*rack*shelf*updated_by*update_date";
			$data_array_dtls_up = $txt_no_of_roll."*".$txt_Delivery_qnty . "*" . $cbo_floor . "*" . $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
			$up_rID = sql_update("pro_grey_prod_delivery_mst", $field_array_up, $data_array_up, "id", $update_mst_id, 0);
			$up_rID2 = sql_update("pro_grey_prod_delivery_dtls", $field_array_dtls_up, $data_array_dtls_up, "mst_id", $update_mst_id, 0);

			//echo "6**".$up_rID."**".$up_rID2; die();

			if ($db_type == 0) {
				if ($up_rID && $up_rID2 ) {
					mysql_query("COMMIT");
					echo "1**" . str_replace("'", '', $update_mst_id) . "**" . str_replace("'", '', $txt_system_id);
				} else {
					mysql_query("ROLLBACK");
					echo "6**" . str_replace("'", '', $update_mst_id) . "**";
				}
			} else if ($db_type == 2 || $db_type == 1) {
				if ($up_rID && $up_rID2) {
					oci_commit($con);
					echo "1**" . str_replace("'", '', $update_mst_id) . "**" . str_replace("'", '', $txt_system_id);
				} else {
					oci_rollback($con);
					echo "6**" . str_replace("'", '', $update_mst_id) . "**1";
				}
			}
			disconnect($con);
			die;
		}
	}
	exit;
}

if($action == "show_delivery_listview"){

	//$delivery_sql = "select a.id,a.sys_number,b.id dtls_id,b.batch_id,b.bodypart_id,b.color_id,b.determination_id,b.gsm,b.dia,b.order_id,b.product_id,b.width_type,sum(b.current_delivery) delivery_qnty from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b where a.entry_form=224 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.is_sales=1 and a.id=$data group by a.id,a.sys_number,b.id,b.batch_id,b.bodypart_id,b.color_id,b.determination_id,b.gsm,b.dia,b.order_id, b.product_id,b.width_type order by a.id desc";
	$delivery_sql = "select a.id,a.issue_number,b.id dtls_id, b.batch_id,b.body_part_id,b.prod_id,sum(b.issue_qnty) issue_qnty,b.store_id,b.no_of_roll,b.order_id, c.id product_id,c.detarmination_id,c.gsm,c.dia_width,c.color from inv_issue_master a,inv_finish_fabric_issue_dtls b,product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.id=$data and a.status_active='1' and a.is_deleted='0' and b.status_active='1' and b.is_deleted='0' group by a.id,a.issue_number,b.id, b.batch_id,b.body_part_id,b.prod_id,b.store_id,b.no_of_roll,b.order_id,c.id,c.detarmination_id,c.gsm,c.dia_width,c.color";
	$deliveryData = sql_select($delivery_sql);
	$batch_id_arr = $determination_id_arr = $product_id_arr = $color_id_arr=array();
	foreach($deliveryData as $row)
	{
		$batch_id_arr[] 		= $row[csf("batch_id")];
		$color_id_arr[] 		= $row[csf("color")];
		$determination_id_arr[] = $row[csf("detarmination_id")];
		$product_id_arr[] 		= $row[csf("product_id")];
	}

	$composition_arr=array();
	if(!empty($determination_id_arr)){
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in(".implode(",",$determination_id_arr).") and b.status_active=1 and b.is_deleted=0 order by b.id asc";
		$data_array=sql_select($sql_deter);
		if(count($data_array)>0)
		{
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
		}
	}
	if(!empty($product_id_arr)){
		$fabric_desc_arr = return_library_array("select id, item_description from product_details_master where item_category_id=2 and id in(".implode(",",$product_id_arr).")","id","item_description");
	}
	if(!empty($batch_id_arr)){
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")","id","batch_no");
	}
	if(!empty($color_id_arr)){
		$color_arr = return_library_array( "select id, color_name from lib_color where id in(".implode(",",$color_id_arr).")",'id','color_name');
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="620" class="rpt_table">
		<thead>
			<th width="80">Batch</th>
			<th width="100">Body Part</th>
			<th width="150">Fabric Description</th>
			<th width="60">GSM</th>
			<th width="70">Dia / Width</th>
			<th width="80">Color</th>
			<th width="80">Delivery Qty</th>
		</thead>
	</table>
	<div style="width:620px; max-height:200px;" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="620" class="rpt_table" id="list_view">  
			<?
			$i=1;
			foreach($deliveryData as $row)
			{  
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	 

				if($row[csf('detarmination_id')]==0 || $row[csf('detarmination_id')]=="")
					$fabric_desc=$fabric_desc_arr[$row[csf('product_id')]]; 
				else
					$fabric_desc=$composition_arr[$row[csf('detarmination_id')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_data_dtls_part(<? echo $row[csf('dtls_id')]; ?>,<? echo $row[csf('product_id')]; ?>)"> 
					<td width="80"><p><? echo $batch_arr[$row[csf('batch_id')]]; ?></p></td>
					<td width="100"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
					<td width="150"><p><? echo $fabric_desc; ?></p></td>
					<td width="60"><p><? echo $row[csf('gsm')]; ?></p></td>
					<td width="70"><p><? echo $row[csf('dia_width')]; ?></p></td>
					<td width="80"><p><? echo $color_arr[$row[csf('color')]]; ?></p></td>
					<td width="80" align="right"><? echo number_format($row[csf('issue_qnty')],2); ?></td>
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

if($action == "populate_delivery_dtls_data")
{
	$data=explode("**",$data);
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 order by b.id asc";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
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
	}

	$variable_settings ="1";
	if($variable_settings == 1)
	{
		$select_fields_rcv  = " b.floor, b.room,b.rack_no,b.shelf_no, ";
		$select_fields_trans  = " b.to_floor_id as floor,b.to_room as room, b.to_rack as rack_no,b.to_shelf as shelf_no, ";
		$group_field_rcv = " ,b.floor, b.room,b.rack_no,b.shelf_no ";
		$group_field_trans = " ,b.to_floor_id,b.to_room, b.to_rack,b.to_shelf ";
	}
	if($db_type==0){
		$castingCond_order_id="cast(c.po_breakdown_id as CHAR(4000)) as order_id";
		$castingCond_to_order_id="cast(a.to_order_id as CHAR(4000)) as order_id";
	}
	else{
		$castingCond_order_id=" cast(c.po_breakdown_id as varchar2(4000)) as order_id";
		$castingCond_to_order_id="cast(a.to_order_id as varchar2(4000)) as order_id";
	}
	$receive_sql= "select a.knitting_company,a.knitting_source,min(a.receive_date) as receive_date,a.store_id, b.prod_id,b.batch_id,  $castingCond_order_id,b.body_part_id, b.fabric_description_id,b.gsm,b.width,b.color_id,b.dia_width_type,b.is_sales,b.uom,b.fabric_shade, $select_fields_rcv sum(c.quantity) as  receive_qnty 
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = ".$data[2]." and b.prod_id = $data[1] and b.trans_id!=0 and a.item_category=2 and a.entry_form in (7,225) and c.entry_form in (7,225) and b.is_sales=1 and a.status_active=1 and b.status_active=1  and c.status_active=1
	group by a.knitting_company,a.knitting_source, a.store_id, b.prod_id,b.batch_id,c.po_breakdown_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.dia_width_type,b.is_sales,b.uom,b.fabric_shade $group_field_rcv
	union all

	select a.knitting_company,a.knitting_source,min(a.receive_date) as receive_date,a.store_id, b.prod_id,b.batch_id,b.order_id,b.body_part_id, b.fabric_description_id,b.gsm,b.width,b.color_id,
	b.dia_width_type,b.is_sales,b.uom,b.fabric_shade, b.floor, b.room,b.rack_no,b.shelf_no, sum(b.receive_qnty) as receive_qnty 
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b
	where a.id=b.mst_id and b.order_id = '".$data[2]."' and b.prod_id = $data[1] and b.trans_id!=0 and a.item_category=2 and a.entry_form in (7,225) and b.is_sales=1 and a.status_active=1 
	and b.status_active=1  and a.booking_without_order = 1
	group by a.knitting_company,a.knitting_source, a.store_id, b.prod_id,b.batch_id,b.order_id, b.body_part_id, b.fabric_description_id, b.gsm, 
	b.width, b.color_id, b.dia_width_type,b.is_sales,b.uom,b.fabric_shade $group_field_rcv

	union all
	select null as knitting_company, null as knitting_source, min(a.transfer_date) as receive_date,b.to_store as store_id, b.from_prod_id as prod_id, b.to_batch_id as batch_id,  $castingCond_to_order_id , b.body_part_id, b.feb_description_id as fabric_description_id, b.gsm, b.dia_width as width,b.color_id, b.dia_width_type, 1 as is_sales, b.uom, b.fabric_shade , $select_fields_trans sum(b.transfer_qnty) as receive_qnty
	from inv_item_transfer_mst a, inv_item_transfer_dtls b 
	where a.id=b.mst_id and a.entry_form in(230) and a.to_order_id =".$data[2]." and b.from_prod_id = $data[1] and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
	group by b.to_store, b.from_prod_id, b.to_batch_id, a.to_order_id, b.body_part_id, b.feb_description_id, b.gsm, b.dia_width,b.color_id, b.dia_width_type, b.uom, b.fabric_shade $group_field_trans";

	$receive_array=sql_select($receive_sql);
	foreach($receive_array as $row)
	{
		if($variable_settings == 1)
		{
			$receive_arr[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]] += $row[csf("receive_qnty")];
		}
		else
		{
			$receive_arr[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]] += $row[csf("receive_qnty")];
		}
	}

	$trans_out_sql = "SELECT a.from_order_id as po_breakdown_id,b.batch_id, b.from_prod_id as prod_id, b.body_part_id,b.feb_description_id as fabric_description_id, b.from_store as store_id,b.uom, b.floor_id, b.room, b.rack as rack_no,b.shelf as shelf_no,b.color_id, b.fabric_shade,b.dia_width_type, b.dia_width as width, b.gsm, sum(b.transfer_qnty) as quantity 
	FROM inv_item_transfer_mst a, inv_item_transfer_dtls b 
	WHERE a.id=b.mst_id and a.entry_form =230 and a.from_order_id =".$data[2]." and b.from_prod_id = ".$data[1]." and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 
	group by a.from_order_id, b.batch_id, b.from_prod_id, b.body_part_id, b.feb_description_id, b.from_store, b.uom, b.floor_id, b.room, b.rack, b.shelf, b.color_id,b.fabric_shade,b.dia_width_type, b.dia_width, b.gsm";

	$trans_out_Data = sql_select($trans_out_sql);
	foreach ($trans_out_Data as $row) 
	{
		if($variable_settings == 1)
		{
			$trans_out_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor_id")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]] += $row[csf("quantity")];
		}
		else
		{
			$trans_out_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]] += $row[csf("quantity")];
		}			
	}



	//===========================================
	if($variable_settings == 1)
	{
		$select_fields_delivery  = " b.floor_id, b.rack, b.shelf, b.room, ";
		$group_field_delivery = " ,b.floor_id, b.rack, b.shelf, b.room ";
	}

	/*$cumm_delivery_sql = "select a.id,a.company_id,a.issue_number,b.id dtls_id, b.batch_id,b.body_part_id,b.prod_id product_id,a.location_id,b.uom,b.fabric_shade,b.width_type, sum(b.issue_qnty) delivery_qnty,b.store_id,b.no_of_roll roll_no,b.order_id,b.floor floor_id,b.room,b.rack_no rack,b.shelf_no shelf from inv_issue_master a,inv_finish_fabric_issue_dtls b where a.id=b.mst_id and a.status_active='1' and a.is_deleted='0' and b.status_active=1 and b.is_deleted=0 and b.order_id='".$data[2]."' and b.prod_id=".$data[1]." group by a.id,a.company_id,a.issue_number,b.id, b.batch_id,b.body_part_id,b.prod_id,a.location_id,b.uom, b.fabric_shade,b.width_type,b.store_id, b.no_of_roll,b.order_id, b.floor,b.room,b.rack_no,b.shelf_no";*/

	$cumm_delivery_sql = "select b.batch_id,b.body_part_id,b.prod_id product_id,b.uom,b.fabric_shade,b.width_type, sum(b.issue_qnty) delivery_qnty,b.store_id, b.order_id,b.floor floor_id,b.room,b.rack_no rack,b.shelf_no shelf from inv_issue_master a,inv_finish_fabric_issue_dtls b where a.id=b.mst_id and a.status_active='1' and a.is_deleted='0' and b.status_active=1 and b.is_deleted=0 and b.order_id='".$data[2]."' and b.prod_id=".$data[1]." and entry_form in (224,287) group by b.batch_id,b.body_part_id,b.prod_id,b.uom, b.fabric_shade,b.width_type,b.store_id, b.order_id, b.floor,b.room,b.rack_no,b.shelf_no";
	
	$cummDeliveryData = sql_select($cumm_delivery_sql);
	foreach ($cummDeliveryData as $row) 
	{

		if($variable_settings == 1)
		{
			$cumm_delivery_arr[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]][$row[csf("floor_id")]][$row[csf("rack")]][$row[csf("shelf")]][$row[csf("room")]] += $row[csf("delivery_qnty")];
		}
		else
		{
			$cumm_delivery_arr[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]] += $row[csf("delivery_qnty")];
		}
	}

	$issue_return_sql = sql_select("select e.po_breakdown_id,b.batch_id,d.store_id,b.prod_id,b.room,b.floor,b.rack_no, b.shelf_no,b.fabric_shade, b.dia_width_type, sum(e.quantity) as qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction d , order_wise_pro_details e 
	  where a.id=b.mst_id  and b.mst_id=d.mst_id and b.trans_id = d.id and d.id = e.trans_id and b.id = e.dtls_id  and d.item_category=2 and d.transaction_type in (4) and a.entry_form in (233) and e.entry_form = 233 and e.po_breakdown_id in ('".$data[2]."') and e.is_sales=1 and b.prod_id=".$data[1]." and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by  e.po_breakdown_id,b.batch_id,d.store_id,b.prod_id,b.room,b.floor,b.rack_no, b.shelf_no,b.fabric_shade,b.dia_width_type");

	foreach ($issue_return_sql as $row) 
	{

		if($variable_settings == 1)
		{
			$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("floor")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("room")]] += $row[csf("qnty")];
		}
		else
		{
			$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]] += $row[csf("qnty")];
		}
	}

	//=================================================

	$job_wise_sql= sql_select("SELECT job_id, pub_shipment_date, job_wise_qnty from order_wise_pro_details where dtls_id =$data[0] and entry_form=224 and job_id is not null and is_sales=0");

	// main query
	$delivery_sql = "select a.id,a.company_id,a.issue_number,b.id dtls_id, a.knit_dye_company, a.knit_dye_source, b.batch_id, b.body_part_id, b.prod_id, a.location_id, b.uom, b.fabric_shade, sum(b.issue_qnty) delivery_qnty, b.store_id, b.no_of_roll roll_no, b.width_type, b.order_id, b.floor floor_id, b.room, b.rack_no rack, b.shelf_no shelf, b.trans_id, c.id product_id,c.detarmination_id, c.gsm, c.dia_width, c.color color_id,d.batch_no, e.color_name,b.rate,b.rate_in_usd, b.aop_rate, f.cons_amount, b.remarks from inv_issue_master a,inv_finish_fabric_issue_dtls b,product_details_master c,pro_batch_create_mst d,lib_color e, inv_transaction f where a.id=b.mst_id and b.prod_id=c.id and b.batch_id=d.id and c.color=e.id and b.trans_id=f.id and b.id=$data[0] and a.status_active='1' and a.is_deleted='0' and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 group by a.id,a.company_id,a.issue_number,b.id, b.batch_id, b.body_part_id, b.prod_id, a.location_id,b.uom,b.fabric_shade, b.store_id,b.no_of_roll,b.width_type,b.order_id, b.floor,b.room, b.rack_no,b.shelf_no, b.trans_id,c.id, a.knit_dye_company, a.knit_dye_source, c.detarmination_id, c.gsm,c.dia_width, c.color,d.batch_no, e.color_name,b.rate,b.rate_in_usd, b.aop_rate, f.cons_amount, b.remarks";

	$deliveryData = sql_select($delivery_sql);
	foreach ($deliveryData as $row)
	{
		$fab_desc = $body_part[$row[csf('body_part_id')]].", ".$composition_arr[$row[csf('detarmination_id')]].", ".$row[csf('gsm')].", ".$row[csf('dia_width')];
		if($variable_settings == 1)
		{
			$total_receive = $receive_arr[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]][$row[csf("floor_id")]][$row[csf("rack")]][$row[csf("shelf")]][$row[csf("room")]] ;
			$cumm_delivery = $cumm_delivery_arr[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]][$row[csf("floor_id")]][$row[csf("rack")]][$row[csf("shelf")]][$row[csf("room")]] ;

			$issue_return_qnty = $issue_return_arr[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]][$row[csf("floor_id")]][$row[csf("rack")]][$row[csf("shelf")]][$row[csf("room")]];

			$trans_out = $trans_out_arr[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]][$row[csf("floor_id")]][$row[csf("rack")]][$row[csf("shelf")]][$row[csf("room")]];
		}
		else
		{
			$total_receive = $receive_arr[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]] ;
			$cumm_delivery = $cumm_delivery_arr[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]] ;

			$issue_return_qnty = $issue_return_arr[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]] ;

			$trans_out = $trans_out_arr[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("store_id")]][$row[csf("fabric_shade")]][$row[csf("width_type")]];
		}

		$total_receive = number_format($total_receive,2,".","");

		$cumm_delivery = $cumm_delivery - $issue_return_qnty;

		//N.B>>>  Balance =  Total Receive - Total Delivery(with Current) - Current Delivery
		$balance = number_format($total_receive - $cumm_delivery - $trans_out,2,".","");		
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_body_part').value 				= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('txt_batch_no').value 				= '".$row[csf("batch_no")]."';\n";
		echo "document.getElementById('hdn_batch_id').value 				= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_fabric_description').value 		= '".$fab_desc."';\n";
		echo "document.getElementById('txt_fabric_description_id').value 	= '".$row[csf("detarmination_id")]."';\n";
		echo "document.getElementById('txt_gsm').value 						= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_dia').value 						= '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('hdn_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$row[csf("color_name")]."';\n";
		echo "document.getElementById('txt_color_id').value 				= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('cbo_fabric_shade').value 			= '".$row[csf("fabric_shade")]."';\n";
		echo "document.getElementById('txt_no_of_roll').value 				= '".$row[csf("roll_no")]."';\n";
		echo "document.getElementById('txt_dia_width_type').value 			= '".$row[csf("width_type")]."';\n";
		echo "document.getElementById('hidden_product_id').value 			= '".$row[csf("product_id")]."';\n";
		echo "document.getElementById('hidden_pre_product_id').value 		= '".$row[csf("product_id")]."';\n";
		echo "document.getElementById('hdn_knitting_company').value 		= '".$row[csf("knit_dye_company")]."';\n";
		echo "document.getElementById('hdn_knitting_source').value 			= '".$row[csf("knit_dye_source")]."';\n";
		echo "document.getElementById('txt_Delivery_qnty').value 			= '".number_format($row[csf("delivery_qnty")],2,".","")."';\n";
		echo "document.getElementById('hdn_Delivery_qnty').value 			= '".number_format($row[csf("delivery_qnty")],2,".","")."';\n";
		echo "document.getElementById('hdn_Delivery_amount').value 			= '".$row[csf("cons_amount")]."';\n";

		echo "load_drop_down('requires/finish_feb_delivery_to_garments_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_floor','floor_td');\n";
		echo "document.getElementById('cbo_floor').value 					= '".$row[csf("floor_id")]."';\n";

		echo "load_drop_down('requires/finish_feb_delivery_to_garments_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_room','room_td');\n";
		echo "document.getElementById('cbo_room').value 					= '".$row[csf("room")]."';\n";

		echo "load_drop_down('requires/finish_feb_delivery_to_garments_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_rack','rack_td');\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack")]."';\n";

		echo "load_drop_down('requires/finish_feb_delivery_to_garments_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_shelf','shelf_td');\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf")]."';\n";

		echo "document.getElementById('txt_fabric_receive').value 			= '".$total_receive."';\n";
		echo "document.getElementById('txt_fabric_transout').value 			= '".number_format($trans_out,2,".","")."';\n";
		echo "document.getElementById('txt_cumulative_delivery').value 		= '".number_format($cumm_delivery,2,".","")."';\n";
		echo "document.getElementById('txt_yet_delivery').value 			= '".$balance."';\n";
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("dtls_id")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('hidden_fabric_order_rate').value 	= '".$row[csf("rate_in_usd")]."';\n";
		echo "document.getElementById('hidden_fabric_rate').value 			= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('hidden_fabric_aop_rate').value 		= '".$row[csf("aop_rate")]."';\n";
		echo "$('#cbo_body_part').attr('disabled','disabled');\n";		

		$save_string="";
		if(!empty($job_wise_sql))
		{
			foreach ($job_wise_sql as $value) 
			{
				if($save_string=="")
				{
					$save_string=$value[csf("job_id")]."**".$value[csf("job_wise_qnty")]."**".change_date_format($value[csf("pub_shipment_date")]);
				}
				else
				{
					$save_string.=",".$value[csf("job_id")]."**".$value[csf("job_wise_qnty")]."**".change_date_format($value[csf("pub_shipment_date")]);
				}
			}
			echo "document.getElementById('save_data').value 		= '".$save_string."';\n";
		}
	}
	exit();
}
if($action=='populate_data_from_to_garments')
{
	$data_array=sql_select("select id, store_id,company_id,location_id,issue_date, vehicle_no, driver_name from inv_issue_master where id='$data'");
	
	foreach ($data_array as $row)
	{ 
		echo "load_drop_down('requires/finish_feb_delivery_to_garments_controller', '".$row[csf('location_id')]."_".$row[csf('company_id')]."', 'load_drop_down_store','store_td');\n";

		echo "reset_on_change(\"cbo_store_name\");\n";
		echo "load_drop_down('requires/finish_feb_delivery_to_garments_controller', '".$row[csf('store_id')]."_".$row[csf('company_id')]."', 'load_drop_floor','floor_td');\n";
		echo "load_drop_down('requires/finish_feb_delivery_to_garments_controller', '".$row[csf('store_id')]."_".$row[csf('company_id')]."', 'load_drop_room','room_td');\n";
		echo "load_drop_down('requires/finish_feb_delivery_to_garments_controller', '".$row[csf('store_id')]."_".$row[csf('company_id')]."', 'load_drop_rack','rack_td');\n";
		echo "load_drop_down('requires/finish_feb_delivery_to_garments_controller', '".$row[csf('store_id')]."_".$row[csf('company_id')]."', 'load_drop_shelf','shelf_td');\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		echo "document.getElementById('txt_delivery_date').value 			= '".change_date_format($row[csf("issue_date")])."';\n";
		echo "document.getElementById('txt_vehicle_no').value 				= '".$row[csf("vehicle_no")]."';\n";
		echo "document.getElementById('txt_driver_name').value 				= '".$row[csf("driver_name")]."';\n";

		exit();
	}
}

if ($action=="systemId_popup")
{
	echo load_html_head_contents("System ID Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			var dataValue = data.split("**");
			$('#hidden_sys_id').val(dataValue[0]);
			$('#hidden_batch_id').val(dataValue[1]);
			$('#hidden_sales_id').val(dataValue[2]);
			$('#hidden_booking_no').val(dataValue[3]);
			$('#hidden_buery_id').val(dataValue[4]);
			$('#hidden_po_company_id').val(dataValue[5]);
			$('#hidden_fso_no').val(dataValue[6]);
			$('#hidden_batch_no').val(dataValue[7]);
			$('#hidden_po_job_no').val(dataValue[8]);
			$('#hidden_po_company_name').val(dataValue[9]);
			$('#hidden_location').val(dataValue[10]);
			$('#hidden_sys_number').val(dataValue[11]);
			$('#hidden_within_group').val(dataValue[12]);
			$('#hidden_store_id').val(dataValue[13]);
			$('#hidden_booking_id').val(dataValue[14]);
			$('#hidden_booking_without_order').val(dataValue[15]);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:980px;">
		<form name="searchsystemidfrm"  id="searchsystemidfrm">
			<fieldset style="width:970px;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="800" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Delivery Date Range</th>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up">Please Enter System Id</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_sys_id" id="hidden_sys_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_sys_number" id="hidden_sys_number" class="text_boxes" value="">
							<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
							<input type="hidden" name="hidden_sales_id" id="hidden_sales_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_fso_no" id="hidden_fso_no" class="text_boxes" value="">
							<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_batch_no" id="hidden_batch_no" class="text_boxes" value="">
							<input type="hidden" name="hidden_buery_id" id="hidden_buery_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_po_company_id" id="hidden_po_company_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_po_company_name" id="hidden_po_company_name" class="text_boxes" value="">
							<input type="hidden" name="hidden_po_job_no" id="hidden_po_job_no" class="text_boxes" value="">
							<input type="hidden" name="hidden_location" id="hidden_location" class="text_boxes" value="">
							<input type="hidden" name="hidden_grey_sys_id" id="hidden_grey_sys_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_grey_sys_number" id="hidden_grey_sys_number" class="text_boxes" value="">
							<input type="hidden" name="hidden_sys_dtls_id" id="hidden_sys_dtls_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_within_group" id="hidden_within_group" class="text_boxes" value="">
							<input type="hidden" name="hidden_store_id" id="hidden_store_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_party_id" id="hidden_party_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_booking_without_order" id="hidden_booking_without_order" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;">
						</td>
						<td id="">
							<?
							echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   
							?>
						</td>
						<td>
							<?
							$search_by_arr=array(1=>"System ID",2=>"Sales Order No",3=>"Booking No",4=>"Batch No");
							$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td id="search_by_td">
							<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value, 'create_finish_search_list_view', 'search_div', 'finish_feb_delivery_to_garments_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit;
}

if($action=="create_finish_search_list_view")
{
	$data = explode("_",$data);
	$search_string=trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$buyer_id =$data[5];

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	if($buyer_id!=0) $buyer_cond="and a.buyer_id=$buyer_id";else $buyer_cond="";
	if($buyer_id!=0) $buyer_cond2="and d.buyer_id=$buyer_id";else $buyer_cond2="";

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.issue_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.issue_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==1)
			$search_field_cond="and a.issue_number_prefix_num='$search_string'";
		else if($search_by==2)
			$search_field_cond="and d.job_no_prefix_num='$search_string'";
		else if($search_by==3)
			$search_field_cond="and d.sales_booking_no like '%$search_string'";
		else 
			$search_field_cond="and c.batch_no like '%$search_string'";
	}
	else
	{
		$search_field_cond="";
	}
	if($db_type==0){
		$batchNoCond="group_concat(c.batch_no) as batch_no";		
		$year_cond=" YEAR(a.insert_date) as year";
	}
	else{
		$batchNoCond="listagg(cast(c.batch_no as varchar2(4000)), ',') within group (order by c.id) as batch_no";
		$year_cond="to_char(a.insert_date,'YYYY') as year";
	}
	$delivery_sql="SELECT a.id, issue_number_prefix_num, $year_cond, a.issue_number, a.challan_no, a.company_id, a.issue_date,a.issue_purpose,a.supplier_id party_name, a.buyer_id,a.location_id,a.store_id, b.sample_type, sum(b.issue_qnty) as issue_qnty, $batchNoCond,d.id order_id,d.job_no,d.sales_booking_no, d.booking_id, d.buyer_id,d.within_group,d.po_buyer,d.po_company_id, d.po_job_no, d.booking_without_order 
	from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c,fabric_sales_order_mst d 
	where a.entry_form=224 and a.id=b.mst_id and b.batch_id=c.id and b.order_id=cast( d.id as varchar2(4000))  and a.item_category=2 and a.company_id=$company_id $search_field_cond and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $buyer_cond $buyer_cond2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0  
	group by a.id, issue_number_prefix_num, a.issue_number, a.challan_no, a.company_id, a.issue_date, a.issue_purpose,a.supplier_id, a.buyer_id,a.location_id,a.store_id, b.sample_type, a.insert_date,d.id,d.job_no,d.sales_booking_no,d.booking_id, d.buyer_id,d.within_group,d.po_buyer,d.po_company_id, d.po_job_no,d.booking_without_order order by a.id";

	$deliveryData = sql_select($delivery_sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="100">Company</th>
			<th width="70">Challan NO</th>
			<th width="80">Within Group</th>
			<th width="100">Buyer Name</th>
			<th width="120">FSO No</th>
			<th width="100">Booking No</th>
			<th width="100">Batch No</th>
			<th width="80">Delivery date</th>
			<th width="80">Delivery Qnty</th>
		</thead>
	</table>
	<div style="width:890px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;
			foreach ($deliveryData as $row)
			{  
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";	 
				$buyer_id = ($row[csf('within_group')]==1)?$row[csf('po_buyer')]:$row[csf('buyer_id')];
				$data = $row[csf('id')] . "**" . $row[csf('batch_id')] . "**" . $row[csf('order_id')] . "**" . $row[csf('sales_booking_no')] . "**" . $buyer_id . "**" . $row[csf('party_name')] . "**" . $row[csf('job_no')] . "**" . $row[csf('batch_no')] . "**" . $row[csf('po_job_no')] . "**" . $company_arr[$row[csf('party_name')]] . "**" . $row[csf('location_id')] . "**" . $row[csf('issue_number')] . "**" . $row[csf('within_group')] . "**" . $row[csf('store_id')]."**".$row[csf('booking_id')]."**".$row[csf('booking_without_order')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>');"> 
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="100" align="center"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
					<td width="70" align="center"><p><? echo $row[csf('issue_number_prefix_num')]; ?></p></td>
					<td width="80" align="center"><p><? echo $yes_no[$row[csf('within_group')]]; ?></p></td>
					<td width="100" align="center"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
					<td width="120" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
					<td width="100" align="center"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="100" align="center"><p><? echo $row[csf('batch_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
					<td width="80" align="right"><? echo number_format($row[csf('issue_qnty')],2); ?></td>
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

if($action == "chk_dtls_id_with_same_criteria")
{
	$data = explode("**", $data);
	$variable_settings_yes = $data[0];
	$mst_id = $data[1];
	$order_id = $data[2];
	$batch_id = $data[3];
	$product_id = $data[4];
	$store_id = $data[5];
	$fabric_shade = $data[6];
	$width_type = $data[7];
	$body_part_id = $data[8];

	$additional_cond = "";
	if($variable_settings_yes == 1)
	{
		$floor_id = $data[9];
		$rack = $data[10];
		$shelf = $data[11];
		$room = $data[12];
		//$additional_cond = " and b.floor_id= $floor_id and b.rack= $rack and b.shelf= $shelf and b.room = $room " ;
		$additional_cond = " and b.floor= $floor_id and b.rack_no= $rack and b.shelf_no= $shelf and b.room = $room " ;
	}

	//$delivery_sql = " select a.id from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b where a.entry_form=224 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.is_sales=1 and a.id=$mst_id and order_id = '$order_id' and b.product_id = $product_id and b.batch_id= $batch_id and a.store_id = $store_id $additional_cond";

	$delivery_sql = "select a.id from inv_issue_master a, inv_finish_fabric_issue_dtls b where a.entry_form=224 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.id=$mst_id and b.order_id='$order_id' and b.prod_id=$product_id and b.batch_id=$batch_id and b.store_id=$store_id and b.fabric_shade=$fabric_shade and b.width_type=$width_type and b.body_part_id=$body_part_id $additional_cond" ;
	
	$DeliveryData = sql_select($delivery_sql);

	if($DeliveryData[0][csf("id")] !="")
	{
		echo trim($DeliveryData[0][csf("id")]);
	}
	exit();	
}

if ($action=="finish_fabric_receive_print_2")
{
	extract($_REQUEST);
	$data=explode('*',$data);

	$sql ="select a.id ,a.company_id, a.supplier_id, a.issue_number, a.issue_date, c.job_no, c.sales_booking_no, c.style_ref_no, c.within_group, c.po_buyer,c.buyer_id, d.batch_no, b.issue_qnty, e.detarmination_id, e.gsm,e.dia_width,e.color, b.uom, b.fabric_shade, b.no_of_roll, b.body_part_id, b.remarks from inv_issue_master a, inv_finish_fabric_issue_dtls b , fabric_sales_order_mst c, pro_batch_create_mst d, product_details_master e where a.entry_form=224 and a.id=b.mst_id and b.order_id=c.id and a.fso_id = c.id and b.batch_id = d.id and b.prod_id = e.id and a.status_active=1 and b.status_active=1 and a.id=$data[1] and a.company_id='$data[0]'";

	$dataArray=sql_select($sql);
	$rec_basis=$dataArray[0][csf("receive_basis")];
	$country_arr=return_library_array( "SELECT id, country_name from  lib_country", "id", "country_name");
	$location_library=return_library_array( "SELECT id, location_name from lib_location", "id", "location_name");
	$company_arr = return_library_array("SELECT id, company_name from lib_company","id","company_name");
	$buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer","id","buyer_name");
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active=1",'id','color_name');

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 order by b.id asc";
	$deter_array=sql_select($sql_deter);
	if(count($deter_array)>0)
	{
		foreach( $deter_array as $row )
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
	}
	unset($deter_array);

	?>
	<div style="width:1370px;">
		<table width="1340" cellspacing="0" align="right">
			<tr>
				<td colspan="5" align="center" style="font-size:22px"><strong><? echo $company_arr[$data[0]]; ?></strong></td>
				<td rowspan="3" id="barcode_img_id"></td>
			</tr>
			<tr class="form_caption">
				<td colspan="5" align="center" style="font-size:14px">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
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
				<td colspan="5" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Party</strong></td>
				<td width="175px"><? echo $company_arr[$dataArray[0][csf('company_id')]]; ?></td>
				<td width="120"><strong>Challan :</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
				<td width="125"><strong>Receive Date:</strong></td>
				<td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			 <tr>
				<td><strong>Delivery to:</strong></td>
				<td>
					<? 
					if($dataArray[0][csf('within_group')]==2){
						echo $buyer_arr[$dataArray[0][csf('supplier_id')]]; 
					}else{
						echo $company_arr[$dataArray[0][csf('supplier_id')]];
					}
					?>
				</td>
				<td><strong>Buyer:</strong></td>
				<td>
					<? 
					if($dataArray[0][csf('within_group')]==2){
						echo $buyer_arr[$dataArray[0][csf('buyer_id')]]; 
					}else{
						echo $buyer_arr[$dataArray[0][csf('po_buyer')]];
					}
					?>
				</td>
				<td><strong>FSO No:</strong></td>
				<td><? echo $dataArray[0][csf('job_no')]; ?></td>
			</tr>
			<tr>
				<td><strong>Booking No:</strong></td>
				<td><? echo $dataArray[0][csf('sales_booking_no')]; ?></td>
				<td><strong>Style Ref:</strong></td>
				<td><p><? echo $dataArray[0][csf('style_ref_no')]; ?></p></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<div style="width:100%; margin-top:20px;">
			<table align="right" cellspacing="0" cellpadding="0" width="1340"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="25">SL</th>
						<th width="100">Body Part</th>
						<th width="140">Fabric Description</th>
						<th width="30">F/ GSM</th>
						<th width="60">F/DIA</th>
						<th width="70">Batch No</th>
						<th width="50">Fab Color</th>
						<th width="60">No.of Roll</th>
						<th width="60">Delivery Qty</th>
						<th width="30">UOM</th>
						<th width="60">Fabric Shade</th>
						<th width="100">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($dataArray as $row)
					{
						if ($i%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";
						?>
						<tr>
							<td width="25" align="center"><? echo $i; ?></td>
							<td align="center"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
							<td align="center"><? echo $composition_arr[$row[csf("detarmination_id")]]; ?></td>
							<td align="center"><? echo $row[csf("gsm")]; ?></td>
							<td align="center"><? echo $row[csf("dia_width")]; ?></td>
							<td align="center"><? echo $row[csf("batch_no")]; ?></td>
							<td align="center"><? echo $color_arr[$row[csf("color")]]; ?></td>
							<td align="right"><? echo number_format($row[csf("no_of_roll")]); ?></td>
							<td align="right"><? echo number_format($row[csf("issue_qnty")],2); ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
							<td align="center"><? echo $fabric_shade[$row[csf("fabric_shade")]];?></td>
							<td align="center"><? echo $row[csf("remarks")];?></td>
						</tr>
						<? 
						$i++;
						$totalRoll +=$row[csf("no_of_roll")];
						$totalIssueQnty +=$row[csf("issue_qnty")];
						
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="7" align="right"><strong>Total :</strong></td>
						<td align="right"><?php echo number_format($totalRoll); ?></td>
						<td align="right"><?php echo number_format($totalIssueQnty); ?></td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(205, $data[0], "1140px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;
			var btype = 'code39';
			var renderer ='bmp';
			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[3]; ?>');
	</script>
	<?
	exit();
}
if ($action=="finish_fabric_receive_print_3")
{
	extract($_REQUEST);
	$data=explode('*',$data);

	$sql ="select a.id ,a.company_id, a.supplier_id, a.issue_number, a.issue_date, c.job_no, c.sales_booking_no, c.style_ref_no, c.within_group, c.po_buyer,c.buyer_id, d.batch_no, b.issue_qnty, e.detarmination_id, e.gsm,e.dia_width,e.color, b.uom, b.fabric_shade, b.no_of_roll, b.body_part_id, b.remarks from inv_issue_master a, inv_finish_fabric_issue_dtls b , fabric_sales_order_mst c, pro_batch_create_mst d, product_details_master e where a.entry_form=224 and a.id=b.mst_id and b.order_id=c.id and a.fso_id = c.id and b.batch_id = d.id and b.prod_id = e.id and a.status_active=1 and b.status_active=1 and a.id=$data[1] and a.company_id='$data[0]'";

	$dataArray=sql_select($sql);
	$rec_basis=$dataArray[0][csf("receive_basis")];
	$country_arr=return_library_array( "SELECT id, country_name from  lib_country", "id", "country_name");
	$location_library=return_library_array( "SELECT id, location_name from lib_location", "id", "location_name");
	$company_arr = return_library_array("SELECT id, company_name from lib_company","id","company_name");
	$buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer","id","buyer_name");
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active=1",'id','color_name');

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 order by b.id asc";
	$deter_array=sql_select($sql_deter);
	if(count($deter_array)>0)
	{
		foreach( $deter_array as $row )
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
	}
	unset($deter_array);

	?>
	<div style="width:1370px;">
		<table width="1340" cellspacing="0" align="right">
			<tr>
				<td colspan="5" align="center" style="font-size:22px"><strong><? echo $company_arr[$data[0]]; ?></strong></td>
				<td rowspan="3" id="barcode_img_id"></td>
			</tr>
			<tr class="form_caption">
				<td colspan="5" align="center" style="font-size:14px">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
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
				<td colspan="5" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Party</strong></td>
				<td width="175px"><? echo $company_arr[$dataArray[0][csf('company_id')]]; ?></td>
				<td width="120"><strong>Challan :</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
				<td width="125"><strong>Receive Date:</strong></td>
				<td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			 <tr>
				<td><strong>Delivery to:</strong></td>
				<td>
					<? 
					if($dataArray[0][csf('within_group')]==2){
						echo $buyer_arr[$dataArray[0][csf('supplier_id')]]; 
					}else{
						echo $company_arr[$dataArray[0][csf('supplier_id')]];
					}
					?>
				</td>
				<td><strong>Buyer:</strong></td>
				<td>
					<? 
					if($dataArray[0][csf('within_group')]==2){
						echo $buyer_arr[$dataArray[0][csf('buyer_id')]]; 
					}else{
						echo $buyer_arr[$dataArray[0][csf('po_buyer')]];
					}
					?>
				</td>
				<td><strong>FSO No:</strong></td>
				<td><? echo $dataArray[0][csf('job_no')]; ?></td>
			</tr>
			<tr>
				<td><strong>Booking No:</strong></td>
				<td><? echo $dataArray[0][csf('sales_booking_no')]; ?></td>
				<td><strong>Style Ref:</strong></td>
				<td><p><? echo $dataArray[0][csf('style_ref_no')]; ?></p></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<div style="width:100%; margin-top:20px;">
			<table align="right" cellspacing="0" cellpadding="0" width="1340"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="25">SL</th>
						<th width="100">Body Part</th>
						<th width="140">Fabric Description</th>
						<th width="30">F/ GSM</th>
						<th width="60">F/DIA</th>
						<th width="70">Batch No</th>
						<th width="50">Fab Color</th>
						<th width="60">No.of Roll</th>
						<th width="60">Delivery Qty</th>
						<th width="30">UOM</th>
						<th width="60">Fabric Shade</th>
						<th width="100">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($dataArray as $row)
					{
						if ($i%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";
						?>
						<tr>
							<td width="25" align="center"><? echo $i; ?></td>
							<td align="center"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
							<td align="center"><? echo $composition_arr[$row[csf("detarmination_id")]]; ?></td>
							<td align="center"><? echo $row[csf("gsm")]; ?></td>
							<td align="center"><? echo $row[csf("dia_width")]; ?></td>
							<td align="center"><? echo $row[csf("batch_no")]; ?></td>
							<td align="center"><? echo $color_arr[$row[csf("color")]]; ?></td>
							<td align="right"><? echo number_format($row[csf("no_of_roll")]); ?></td>
							<td align="right"><? echo number_format($row[csf("issue_qnty")],2); ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
							<td align="center"><? echo $fabric_shade[$row[csf("fabric_shade")]];?></td>
							<td align="center"><? echo $row[csf("remarks")];?></td>
						</tr>
						<? 
						$i++;
						$totalRoll +=$row[csf("no_of_roll")];
						$totalIssueQnty +=$row[csf("issue_qnty")];
						
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="7" align="right"><strong>Total :</strong></td>
						<td align="right"><?php echo number_format($totalRoll); ?></td>
						<td align="right"><?php echo number_format($totalIssueQnty); ?></td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(205, $data[0], "1140px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;
			var btype = 'code39';
			var renderer ='bmp';
			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[3]; ?>');
	</script>
	<?
	exit();
}

if ($action=="finish_fabric_receive_print_4")
{
	extract($_REQUEST);
	$data=explode('*',$data);

	$sql ="select a.id ,a.company_id, a.supplier_id, a.issue_number, a.issue_date, c.job_no, c.sales_booking_no, c.style_ref_no, c.within_group, c.po_buyer,c.buyer_id, d.batch_no,d.id as batch_id, b.issue_qnty, e.detarmination_id, e.gsm,e.dia_width,e.color, b.uom, b.fabric_shade, b.no_of_roll, b.body_part_id, b.remarks from inv_issue_master a, inv_finish_fabric_issue_dtls b, fabric_sales_order_mst c, pro_batch_create_mst d, product_details_master e where a.entry_form=224 and a.id=b.mst_id and b.order_id=c.id and a.fso_id = c.id and b.batch_id = d.id and b.prod_id = e.id and a.status_active=1 and b.status_active=1 and a.id=$data[1] and a.company_id='$data[0]'";

	$dataArray=sql_select($sql);
	$batchIDs="";
	foreach($dataArray as $row)
	{
		$batchIDArr[$row[csf('batch_id')]]=$row[csf('batch_id')];
	}
	$batchIDs = implode(",",$batchIDArr);
	$batchIDs=chop($batchIDs,",");
	/* $sql_batch ="select a.id ,a.company_id, a.supplier_id, a.issue_number, a.issue_date, c.job_no, c.sales_booking_no, c.style_ref_no,c.within_group, c.po_buyer,c.buyer_id, d.batch_no,d.id as batch_id,f.batch_qnty, e.detarmination_id, e.gsm,e.dia_width,e.color,  b.body_part_id from inv_issue_master a, inv_finish_fabric_issue_dtls b , fabric_sales_order_mst c, pro_batch_create_mst d,pro_batch_create_dtls f,product_details_master e where a.entry_form=224 and a.id=b.mst_id and b.order_id=c.id and a.fso_id = c.id and b.batch_id = d.id and d.id=f.mst_id and b.prod_id = e.id and a.status_active=1 and b.status_active=1 and a.id=$data[1] and d.id in($batchIDs) and a.company_id='$data[0]' "; */

	$sql_batch ="SELECT  e.detarmination_id, e.gsm,e.dia_width,d.color_id as color, f.body_part_id ,f.batch_qnty from pro_batch_create_mst d,pro_batch_create_dtls f,product_details_master e where d.id=f.mst_id and f.prod_id = e.id and f.status_active=1 and d.status_active=1 and d.id in($batchIDs) and d.company_id='$data[0]'";

	$sql_batch_infoArr=sql_select($sql_batch);
	$batch_info_arr= array();
	foreach ($sql_batch_infoArr as $rows)
	{
		$batch_info_arr[$rows[csf('body_part_id')]][$rows[csf('color')]][$rows[csf('gsm')]][$rows[csf('dia_width')]][$rows[csf('detarmination_id')]]['batch_qnty']+=$rows[csf('batch_qnty')];
	}

	$sql_job_info ="select a.job_no,b.grouping,d.body_part_id,c.fabric_color_id,d.gsm_weight,c.dia_width,d.lib_yarn_count_deter_id,c.fin_fab_qnty from wo_booking_mst a, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d, wo_po_break_down b 
	where a.booking_no=c.booking_no and c.pre_cost_fabric_cost_dtls_id=d.id and c.po_break_down_id=b.id and a.status_active=1 and c.status_active=1 and d.status_active=1 and b.status_active=1 and a.company_id='$data[0]' and a.booking_no='".$dataArray[0][csf('sales_booking_no')]."' group by a.job_no,b.grouping ,d.body_part_id,c.fabric_color_id,d.gsm_weight,c.dia_width,d.lib_yarn_count_deter_id,c.fin_fab_qnty";

	$sql_job_infoArr=sql_select($sql_job_info);
	$booking_info_arr= array();
	foreach ($sql_job_infoArr as $rows)
	{
		$booking_info_arr[$rows[csf('body_part_id')]][$rows[csf('fabric_color_id')]][$rows[csf('gsm_weight')]][$rows[csf('dia_width')]][$rows[csf('lib_yarn_count_deter_id')]]['job_no']=$rows[csf('job_no')];
		$booking_info_arr[$rows[csf('body_part_id')]][$rows[csf('fabric_color_id')]][$rows[csf('gsm_weight')]][$rows[csf('dia_width')]][$rows[csf('lib_yarn_count_deter_id')]]['grouping']=$rows[csf('grouping')];
		$booking_info_arr[$rows[csf('body_part_id')]][$rows[csf('fabric_color_id')]][$rows[csf('gsm_weight')]][$rows[csf('dia_width')]][$rows[csf('lib_yarn_count_deter_id')]]['fin_fab_qnty']+=$rows[csf('fin_fab_qnty')];
	}

	$sql_total_del_qnty ="select a.id ,a.company_id, a.supplier_id, a.issue_number, a.issue_date, c.job_no, c.sales_booking_no, c.style_ref_no, c.within_group, c.po_buyer,c.buyer_id, d.batch_no,d.id as batch_id, b.issue_qnty, e.detarmination_id, e.gsm,e.dia_width,e.color, b.uom, b.fabric_shade, b.no_of_roll, b.body_part_id, b.remarks from inv_issue_master a, inv_finish_fabric_issue_dtls b , fabric_sales_order_mst c, pro_batch_create_mst d, product_details_master e where a.entry_form=224 and a.id=b.mst_id and b.order_id=c.id and a.fso_id = c.id and b.batch_id = d.id and b.prod_id = e.id and a.status_active=1 and b.status_active=1 and d.id in($batchIDs) and a.company_id='$data[0]'";

	$dataArrayDeli=sql_select($sql_total_del_qnty);
	$deli_info_arr= array();
	foreach($dataArrayDeli as $rows)
	{
		$deli_info_arr[$rows[csf('body_part_id')]][$rows[csf('color')]][$rows[csf('gsm')]][$rows[csf('dia_width')]][$rows[csf('detarmination_id')]]['total_issue_qnty']+=$rows[csf('issue_qnty')];
	}

	$sql_process_los_qnty ="select  b.body_part_id,b.color_id,b.fabric_description_id,b.original_gsm,b.original_width, c.process_loss_perc from INV_RECEIVE_MASTER a,PRO_FINISH_FABRIC_RCV_DTLS b,ORDER_WISE_PRO_DETAILS c where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=2 and a.entry_form=7 and a.company_id='$data[0]' and b.batch_id in($batchIDs)";

	$dataArrayprocess_loss=sql_select($sql_process_los_qnty);
	$process_loss_info_arr= array();
	foreach($dataArrayprocess_loss as $rows)
	{
		$process_loss_info_arr[$rows[csf('body_part_id')]][$rows[csf('color_id')]][$rows[csf('original_gsm')]][$rows[csf('original_width')]][$rows[csf('fabric_description_id')]]['process_loss_perc']+=$rows[csf('process_loss_perc')];
	}

	$rec_basis=$dataArray[0][csf("receive_basis")];
	$country_arr=return_library_array( "SELECT id, country_name from  lib_country", "id", "country_name");
	$location_library=return_library_array( "SELECT id, location_name from lib_location", "id", "location_name");
	$company_arr = return_library_array("SELECT id, company_name from lib_company","id","company_name");
	$buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer","id","buyer_name");
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active=1",'id','color_name');


	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 order by b.id asc";
	$deter_array=sql_select($sql_deter);
	if(count($deter_array)>0)
	{
		foreach( $deter_array as $row )
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
	}
	unset($deter_array);

	?>
	<div style="width:1370px;">
		<table width="1340" cellspacing="0" align="right">
			<tr>
				<td colspan="5" align="center" style="font-size:22px"><strong><? echo $company_arr[$data[0]]; ?></strong></td>
				<td rowspan="3" id="barcode_img_id"></td>
			</tr>
			<tr class="form_caption">
				<td colspan="5" align="center" style="font-size:14px">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
						?>
						<? echo $result[csf('plot_no')]; ?>
							<? if($result[csf('level_no')]!="") echo $result[csf('level_no')]?>
							<? if($result[csf('road_no')]!="") echo $result[csf('road_no')]; ?>
							<? if($result[csf('block_no')]!="") echo $result[csf('block_no')];?>
							<? if($result[csf('city')]!="") echo $result[csf('city')];?>
							<? if($result[csf('zip_code')]!="") echo $result[csf('zip_code')]; ?>
							<? if($result[csf('province')]!="") echo $result[csf('province')];?>
							<? if($result[csf('country_id')]!="") echo $country_arr[$result[csf('country_id')]]; ?><br>
							Email:<? if($result[csf('email')]!="") echo $result[csf('email')].",";?>
							Website:<? if($result[csf('website')]!="") echo $result[csf('website')];
					}
					$location='';
						if($dataArray[0][csf('knit_dye_source')]==1)
						{
							$caption="Location";
							$issueTo=$company_arr[$dataArray[0][csf('knit_dye_company')]];
							$location=return_field_value("location_name","lib_location","id='".$dataArray[0][csf('location_id')]."'");
						}
						else
						{
							$caption="Address";
							$supplierData=sql_select("select address_1, address_2, supplier_name from lib_supplier where id='".$dataArray[0][csf('knit_dye_company')]."'");
							$issueTo=$supplierData[0][csf('supplier_name')];
							$location=$supplierData[0][csf('address_1')];
							if($location=="") $location=$supplierData[0][csf('address_2')]; else $location.=", ".$supplierData[0][csf('address_2')];
						}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="5" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Party</strong></td>
				<td width="175px"><? echo $company_arr[$dataArray[0][csf('company_id')]]; ?></td>
				<td width="120"><strong>Challan :</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
				<td width="125"><strong>Receive Date:</strong></td>
				<td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			 <tr>
				<td><strong>Delivery to:</strong></td>
				<td>
					<? 
					if($dataArray[0][csf('within_group')]==2){
						echo $buyer_arr[$dataArray[0][csf('supplier_id')]]; 
					}else{
						echo $company_arr[$dataArray[0][csf('supplier_id')]];
					}
					?>
				</td>
				<td><strong>Buyer:</strong></td>
				<td>
					<? 
					if($dataArray[0][csf('within_group')]==2){
						echo $buyer_arr[$dataArray[0][csf('buyer_id')]]; 
					}else{
						echo $buyer_arr[$dataArray[0][csf('po_buyer')]];
					}
					?>
				</td>
				<td><strong>FSO No:</strong></td>
				<td><? echo $dataArray[0][csf('job_no')]; ?></td>
			</tr>
			<tr>
				<td><strong>Booking No:</strong></td>
				<td><? echo $dataArray[0][csf('sales_booking_no')]; ?></td>
				<td><strong>Style Ref:</strong></td>
				<td><p><? echo $dataArray[0][csf('style_ref_no')]; ?></p></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<div style="width:100%; margin-top:20px;">
			<table align="right" cellspacing="0" cellpadding="0" width="1340"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="25">SL</th>
						<th width="100">Job No</th>


						<th width="100">Int. Ref.</th>
						<th width="100">Body Part</th>
						<th width="140">Fabric Description</th>
						<th width="30">F/ GSM</th>
						<th width="60">F/DIA</th>

						<th width="60">Booking Qty.</th>
						<th width="50">Fab Color</th>

						<th width="70">Batch No</th>
						<th width="70">Batch Qty.</th>
					
						
						<th width="60">No.of Roll</th>
						<th width="30">UOM</th>
						<th width="60">Current Delivery Qty</th>
						<th width="60">Total Delivery Qty</th>
						<th width="60">Delivery Balance</th>
						
						<th width="60">Process Loss %</th>
						<th width="100">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					
					$i=1;
					foreach($dataArray as $row)
					{
						if ($i%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";
						?>
						<tr>
							<td width="25" align="center"><? echo $i; ?></td>

							<td align="center"><? echo $sql_job_infoArr[0][csf('job_no')];//$booking_info_arr[$row[csf('body_part_id')]][$row[csf('color')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('detarmination_id')]]['job_no']; ?></td>
							<td align="center"><? echo $sql_job_infoArr[0][csf('grouping')];//$booking_info_arr[$row[csf('body_part_id')]][$row[csf('color')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('detarmination_id')]]['grouping']; ?></td>


							<td align="center"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
							<td align="center"><? echo $composition_arr[$row[csf("detarmination_id")]]; ?></td>
							<td align="center"><? echo $row[csf("gsm")]; ?></td>
							<td align="center"><? echo $row[csf("dia_width")]; ?></td>
							

							<td align="right"><? echo number_format($booking_info_arr[$row[csf('body_part_id')]][$row[csf('color')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('detarmination_id')]]['fin_fab_qnty'],2); ?></td>
							<td align="center"><? echo $color_arr[$row[csf("color")]]; ?></td>

							<td align="center"><? echo $row[csf("batch_no")]; ?></td>
							<td align="right"><? echo number_format($batch_info_arr[$row[csf('body_part_id')]][$row[csf('color')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('detarmination_id')]]['batch_qnty'],2); ?></td>
							

							<td align="right"><? echo number_format($row[csf("no_of_roll")]); ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
							<td align="right"><? echo number_format($row[csf("issue_qnty")],2); ?></td>
							<td align="right"><? echo number_format($deli_info_arr[$row[csf('body_part_id')]][$row[csf('color')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('detarmination_id')]]['total_issue_qnty'],2); ?></td>
							<td align="right"><? echo number_format($batch_info_arr[$row[csf('body_part_id')]][$row[csf('color')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('detarmination_id')]]['batch_qnty']-$deli_info_arr[$row[csf('body_part_id')]][$row[csf('color')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('detarmination_id')]]['total_issue_qnty'],2); ?></td>
							
							<td align="right"><? echo number_format($process_loss_info_arr[$row[csf('body_part_id')]][$row[csf('color')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('detarmination_id')]]['process_loss_perc'],2); ?> %</td>
							<td align="center"><? echo $row[csf("remarks")];?></td>
						</tr>
						<? 
						$i++;
						$totalBookingQty +=$booking_info_arr[$row[csf('body_part_id')]][$row[csf('color')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('detarmination_id')]]['fin_fab_qnty'];
						$totalBatchQty +=$batch_info_arr[$row[csf('body_part_id')]][$row[csf('color')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('detarmination_id')]]['batch_qnty'];
						$totalDeliQty +=$deli_info_arr[$row[csf('body_part_id')]][$row[csf('color')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('detarmination_id')]]['total_issue_qnty'];
						$totalDeliBalanceQty +=$batch_info_arr[$row[csf('body_part_id')]][$row[csf('color')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('detarmination_id')]]['batch_qnty']-$deli_info_arr[$row[csf('body_part_id')]][$row[csf('color')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('detarmination_id')]]['total_issue_qnty'];

						$totalProcssQty +=$process_loss_info_arr[$row[csf('body_part_id')]][$row[csf('color')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('detarmination_id')]]['process_loss_perc'];	
						$totalRoll +=$row[csf("no_of_roll")];
						$totalIssueQnty +=$row[csf("issue_qnty")];
						
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="7" align="right"><strong>Total :</strong></td>
						<td align="right"><?php echo number_format($totalBookingQty,2); ?></td>
						<td align="right"></td>
						<td align="right">&nbsp;</td>
						<td align="right"><?php echo number_format($totalBatchQty,2); ?></td>
						<td align="right"><?php echo number_format($totalRoll); ?></td>
						<td align="right"></td>
						<td align="right"><?php echo number_format($totalIssueQnty,2); ?></td>
						<td align="right"><?php echo number_format($totalDeliQty,2); ?></td>
						<td align="right"><?php echo number_format($totalDeliBalanceQty,2); ?></td>
						<td align="right"><?php echo number_format(($totalDeliBalanceQty/$totalBatchQty)*100,2); ?> %</td>
						<td align="right"></td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(205, $data[0], "1340px",'',"30px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;
			var btype = 'code39';
			var renderer ='bmp';
			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[3]; ?>');
	</script>
	<?
	exit();
}


if ($action=="finish_fabric_receive_print_1")
{
	extract($_REQUEST);
	$data=explode('*',$data);

	$sql ="SELECT a.company_id, a.supplier_id, a.issue_number, a.issue_date, c.job_no, c.sales_booking_no, c.style_ref_no, c.within_group, c.po_buyer,c.buyer_id, d.batch_no, b.issue_qnty, e.detarmination_id, e.gsm,e.dia_width,e.color, b.uom, b.fabric_shade, b.no_of_roll, b.body_part_id, b.remarks , f.job_id, f.job_wise_qnty, g.job_no as po_job, g.style_ref_no as job_style, b.id as dtls_id, a.vehicle_no, a.driver_name
	from inv_issue_master a, inv_finish_fabric_issue_dtls b 
	left join order_wise_pro_details f on b.id=f.dtls_id and f.entry_form=224 and f.job_id is not null and f.job_wise_qnty <>0
	left join wo_po_details_master g on f.job_id=g.id, fabric_sales_order_mst c, pro_batch_create_mst d, product_details_master e
	where a.entry_form=224 and a.id=b.mst_id and b.order_id=c.id and a.fso_id = c.id and b.batch_id = d.id and b.prod_id = e.id and a.status_active=1 and b.status_active=1 and a.id=$data[1] and a.company_id='$data[0]' order by b.id";
	//echo $sql;//die();

	$dataArray=sql_select($sql);
	$dtls_id_dupli_chk =array();
	foreach ($dataArray as  $val) {
		$dtls_wise_roll_count[$val[csf("dtls_id")]]++;
		if($dtls_id_dupli_chk[$val[csf("dtls_id")]] ==""){
			$dtls_id_dupli_chk[$val[csf("dtls_id")]]=$val[csf("dtls_id")];
			$dtls_wise_roll_no[$val[csf("dtls_id")]] +=$val[csf("no_of_roll")];
		}
	}

	$rec_basis=$dataArray[0][csf("receive_basis")];
	$sales_booking_no=$dataArray[0][csf("sales_booking_no")];

	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active=1",'id','color_name');

	if(!empty($dataArray))
	{
		$booking_sql = sql_select("SELECT b.body_part_id, b.lib_yarn_count_deter_id, a.gsm_weight, a.dia_width, a.fabric_color_id, a.gmts_color_id
	from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b
	where a.booking_no='$sales_booking_no' and a.pre_cost_fabric_cost_dtls_id=b.id and a.status_active=1 and a.BOOKING_TYPE in (1,4)
	group by b.body_part_id, a.gsm_weight, a.dia_width, a.fabric_color_id, a.gmts_color_id, b.lib_yarn_count_deter_id
	union all
	select c.body_part_id, c.lib_yarn_count_deter_id, a.gsm_weight, a.dia_width, a.fabric_color_id, a.gmts_color_id
	from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls b, wo_pre_cost_fabric_cost_dtls c
	where a.booking_no='$sales_booking_no' and a.pre_cost_fabric_cost_dtls_id=b.id and b.fabric_description=c.id and a.status_active=1 and a.booking_type in (3)
	group by c.body_part_id, a.gsm_weight, a.dia_width, a.fabric_color_id, a.gmts_color_id, c.lib_yarn_count_deter_id");

		foreach ($booking_sql as  $val) {
			$gmst_color_arr[$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("gsm_weight")]][$val[csf("dia_width")]][$val[csf("fabric_color_id")]].=$color_arr[$val[csf("gmts_color_id")]].',';
		}
	}

	$country_arr=return_library_array( "SELECT id, country_name from  lib_country", "id", "country_name");
	$location_library=return_library_array( "SELECT id, location_name from lib_location", "id", "location_name");
	$company_arr = return_library_array("SELECT id, company_name from lib_company","id","company_name");
	$buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer","id","buyer_name");
	

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 order by b.id asc";
	$deter_array=sql_select($sql_deter);
	if(count($deter_array)>0)
	{
		foreach( $deter_array as $row )
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
	}
	unset($deter_array);

	?>
	<div style="width:1370px;">
		<table width="1340" cellspacing="0" align="right">
			<tr>
				<td colspan="5" align="center" style="font-size:22px"><strong><? echo $company_arr[$data[0]]; ?></strong></td>
				<td rowspan="3" id="barcode_img_id"></td>
			</tr>
			<tr class="form_caption">
				<td colspan="5" align="center" style="font-size:14px">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					//print_r("$nameArray");
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
				<td colspan="5" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Party</strong></td>
				<td width="175px"><? echo $company_arr[$dataArray[0][csf('company_id')]]; ?></td>
				<td width="120"><strong>Challan :</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
				<td width="125"><strong>Receive Date:</strong></td>
				<td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			 <tr>
				<td><strong>Delivery to:</strong></td>
				<td>
					<? 
					if($dataArray[0][csf('within_group')]==2){
						echo $buyer_arr[$dataArray[0][csf('supplier_id')]]; 
					}else{
						echo $company_arr[$dataArray[0][csf('supplier_id')]];
					}
					?>
				</td>
				<td><strong>Buyer:</strong></td>
				<td>
					<? 
					if($dataArray[0][csf('within_group')]==2){
						echo $buyer_arr[$dataArray[0][csf('buyer_id')]]; 
					}else{
						echo $buyer_arr[$dataArray[0][csf('po_buyer')]];
					}
					?>
				</td>
				<td><strong>FSO No:</strong></td>
				<td><? echo $dataArray[0][csf('job_no')]; ?></td>
			</tr>
			<tr>
				<td><strong>Vehicle No. :</strong></td>
				<td>
					<? 
						echo $dataArray[0][csf('vehicle_no')]; 
					?>
				</td>
				<td><strong>Driver Name:</strong></td>
				<td>
					<? 
					echo $dataArray[0][csf('driver_name')]; 
					?>
				</td>
			</tr>
			<tr>
				<td conspan="4">&nbsp;</td>
			</tr>
		</table>
		
		<div style="width:100%; margin-top:20px;">
			<table align="right" cellspacing="0" cellpadding="0" width="1340"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="25">SL</th>
						<th width="100">Job No</th>
						<th width="100">Booking No</th>
						<th width="100">Style Ref</th>
						<th width="100">Body Part</th>
						<th width="140">Fabric Description</th>
						<th width="30">F/ GSM</th>
						<th width="60">F/DIA</th>
						<th width="70">Batch No</th>
						<th width="70">Gmts Color</th>
						<th width="50">Fab Color</th>
						<th width="60">No.of Roll</th>
						<th width="60">Delivery Qty</th>
						<th width="30">UOM</th>
						<th width="60">Fabric Shade</th>
						<th width="100">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;$dtls_id_row_chk = array();
					foreach($dataArray as $row)
					{  $job=$row[csf("po_job_no")];
						$po_job=explode(",", $job);
						//    foreach($po_job as $po_job_no){
						// if ($i%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";

						$garments_color = $gmst_color_arr[$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("color")]];
						$garments_color_names = implode(",",array_unique(explode(",",chop($garments_color,','))));
						?>
						<tr>
							<td width="25" align="center"><? echo $i; ?></td>
							<td width="25" align="center"><? echo $row[csf("po_job")]; ?></td>
							<td width="25" align="center"><? echo $row[csf("sales_booking_no")]; ?></td>
							<td width="25" align="center"><? echo $row[csf("job_style")]; ?></td>
							<td align="center"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
							<td align="center"><? echo $composition_arr[$row[csf("detarmination_id")]]; ?></td>
							<td align="center"><? echo $row[csf("gsm")]; ?></td>
							<td align="center"><? echo $row[csf("dia_width")]; ?></td>
							<td align="center"><? echo $row[csf("batch_no")]; ?></td>
							<td align="center"><? echo $garments_color_names; ?></td>
							<td align="center"><? echo $color_arr[$row[csf("color")]]; ?></td>
							<? 
							if($dtls_id_row_chk[$row[csf("dtls_id")]] =="")
							{
								
								?>
								<td align="center" rowspan="<? echo $dtls_wise_roll_count[$row[csf("dtls_id")]];?>"><? echo $dtls_wise_roll_no[$row[csf("dtls_id")]];//number_format($row[csf("no_of_roll")]); ?></td>
								<?
								$totalRoll +=$dtls_wise_roll_no[$row[csf("dtls_id")]];
							}
							?>

							<td align="right">
								
							<? 
								if($row[csf("job_wise_qnty")]==""){
									$quantity = number_format($row[csf("issue_qnty")], 2, '.', ''); 
								}else{
									$quantity =  number_format($row[csf("job_wise_qnty")], 2, '.', ''); 
								}
								echo $quantity;
							?>
							</td>
							<td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
							<td align="center"><? echo $fabric_shade[$row[csf("fabric_shade")]];?></td>
							<? 
							if($dtls_id_row_chk[$row[csf("dtls_id")]] =="")
							{
								$dtls_id_row_chk[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
								?>
								<td align="center" rowspan="<? echo $dtls_wise_roll_count[$row[csf("dtls_id")]];?>"><? echo $row[csf("remarks")];?></td>
						
							<?}
						?>
						</tr>
						<? 
						$i++;
						
						$totalIssueQnty +=$quantity;
						
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="11" align="right"><strong>Total :</strong></td>
						<td align="center"><?php echo number_format($totalRoll); ?></td>
						<td align="right"><?php echo number_format($totalIssueQnty, 2, '.', ''); ?></td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(205, $data[0], "1140px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;
			var btype = 'code39';
			var renderer ='bmp';
			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[3]; ?>');
	</script>
	<?
	exit();
}

if ($action=="style_wise_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$prev_distribution_method=1;
	$disabled="";
	$disable_drop_down=0;
	$width="1070";
	$receive_basis=10;

	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$variable_style_popup = sql_select("select production_entry from variable_settings_production where company_name=$cbo_company_id and variable_list=72 and status_active =1 and is_deleted=0 order by id");
	$variable_style_popup = ($variable_style_popup[0][csf('over_rcv_percent')]==1) ? $variable_style_popup[0][csf('over_rcv_percent')] : 0;
	
	//$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category = 2 and status_active =1 and is_deleted=0 order by id");
	//$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
	//echo $over_receive_limit;die;

	$variable_set_production=sql_select("select distribute_qnty, auto_update from variable_settings_production where variable_list =51 and company_name=$cbo_company_id and item_category_id=2 "); //and auto_update=1
	$over_receive_limit = !empty($variable_set_production) ? $variable_set_production[0][csf('distribute_qnty')] : 0;
	$is_over_receive_unlimited = ($variable_set_production[0][csf('auto_update')] == 3) ? 1 : 0;


	?>
	<script>
		var receive_basis="<? echo $receive_basis; ?>";
		var roll_maintained="<? echo $roll_maintained; ?>";
		function distribute_qnty(str)
		{
			if(str==1)
			{
				var tot_po_qnty=$('#tot_po_qnty').val()*1;
				var tot_req_qnty=$('#tot_req_qnty').val()*1;
				var txt_prop_finish_qnty=$('#txt_prop_finish_qnty').val()*1;
				var tblRow = $("#tbl_list_search tr").length;
				var len=totalFinish=0;

				$("#tbl_list_search").find('tr').each(function()
				{
					var txtreqqty=$(this).find('input[name="txtreqqty[]"]').val()*1;
					if(txtreqqty>0)
					{
						len=len+1;
						var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
						var txtbalanceqnty=$(this).find('input[name="txtbalanceqnty[]"]').val()*1;
						var hidden_cummulative_rcv_qnty=$(this).find('input[name="hidden_cummulative_rcv_qnty[]"]').val()*1;

						var perc=(txtreqqty/tot_req_qnty)*100;

						var finish_qnty=((perc*txt_prop_finish_qnty)/100);
						totalFinish = (totalFinish*1+finish_qnty*1).toFixed(2);
						//totalFinish = totalFinish;
						var balance_qty= txtreqqty-(hidden_cummulative_rcv_qnty + finish_qnty);

						if(tblRow==len)
						{
							var balance = (txt_prop_finish_qnty-totalFinish);
							if(balance > 0){
								finish_qnty = (finish_qnty*1 + balance*1);
							}else{
								finish_qnty = (finish_qnty*1 - balance*1);
							}
							if(balance!=0) totalFinish=totalFinish*1+(balance*1);
						}

						$(this).find('input[name="txtfinishQnty[]"]').val(finish_qnty.toFixed(2));
						$(this).find('input[name="txtbalanceqnty[]"]').val(balance_qty.toFixed(2));
					}
				});
			}
			else
			{
				$('#txt_prop_finish_qnty').val('');
				$("#tbl_list_search").find('tr').each(function()
				{
					$(this).find('input[name="txtfinishQnty[]"]').val('');
					$(this).find('input[name="txtbalanceqnty[]"]').val('');
				});
			}
		}

		function fnc_close()
		{
			var save_string='';	 var tot_finish_qnty=0;  var tot_required_qnty = 0;
			var tot_balance="";
			var hdn_delivery_qnty=$('#hdn_delivery_qnty').val()*1;
			var hidden_cummu_deli_rcv_qnty=$('#hidden_cummu_deli_rcv_qnty').val()*1;
			var hdn_dtls_id=$('#hdn_dtls_id').val();
			var hdnRequiredQnty=""; var hiddenCummulativeRcvQnty="";
			var overRecLim="<? echo $over_receive_limit; ?>";
			var is_over_receive_unlimited="<? echo $is_over_receive_unlimited; ?>";
			var overValue = 0; var total_txt_finish_and_reject_qnty= 0; var shipment_date ="";
			$("#tbl_list_search").find('tr').each(function()
			{
				var txtJobId=$(this).find('input[name="txtJobId[]"]').val();
				var txtfinishQnty=$(this).find('input[name="txtfinishQnty[]"]').val();
				var txtreqqty=$(this).find('input[name="txtreqqty[]"]').val();
				shipment_date=$(this).find('input[name="txtPubShipDate[]"]').val();

				var hdn_cumm_this_challan_delivery=$(this).find('input[name="hdn_cumm_this_challan_delivery[]"]').val();
				hdnRequiredQnty=$(this).find('input[name="txtreqqty[]"]').val();
				hiddenCummulativeRcvQnty=$(this).find('input[name="hidden_cummulative_delivery_qnty[]"]').val()*1;

				tot_finish_qnty=tot_finish_qnty*1+txtfinishQnty*1;
				tot_required_qnty+=txtreqqty*1;

				total_txt_finish_and_reject_qnty += txtfinishQnty*1;// + txtrejectQnty*1;

				if(overValue==0)
				{
					if(txtfinishQnty*1 > 0)
					{
						if(( (overRecLim*hdnRequiredQnty*1)/100 +hdnRequiredQnty*1) < (hiddenCummulativeRcvQnty*1 + txtfinishQnty*1))
						{
							overValue =1;

							//alert( "(" + ( (overRecLim*hdnRequiredQnty*1)/100 +hdnRequiredQnty*1) +") < (" + hiddenCummulativeRcvQnty*1 + " + " + txtfinishQnty*1 + ")");
						}
					}
				}

				if(save_string=="")
				{
					save_string=txtJobId+"**"+txtfinishQnty*1+"**"+shipment_date;
				}
				else
				{
					save_string+=","+txtJobId+"**"+txtfinishQnty*1+"**"+shipment_date;
				}

			});

			tot_required_qnty=(overRecLim*tot_required_qnty)/100 + tot_required_qnty;
			var balance = tot_required_qnty-hidden_cummu_deli_rcv_qnty;

			if(is_over_receive_unlimited ==0)
			{
				if((tot_required_qnty-hidden_cummu_deli_rcv_qnty) < (total_txt_finish_and_reject_qnty))
				{
					alert("Delivery quantity can not be greater than Required quantity.\nDelivery quantity balance= " + balance);
					return;
				}

				if(overValue > 0)
				{
					alert("Delivery quantity can not be greater than Required quantity.");
					return;
				}
			}

			$('#save_string').val( save_string );
			$('#tot_finish_qnty').val(tot_finish_qnty.toFixed(2));


			$('#distribution_method').val( $('#cbo_distribiution_method').val());

			parent.emailwindow.hide();
		}

	</script>
	</head>
	<body>
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:<? echo $width; ?>px;margin-left:5px">
				<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
				<input type="hidden" name="tot_finish_qnty" id="tot_finish_qnty" class="text_boxes" value="">

				<input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">

				<input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
				<input type="hidden" name="hide_deleted_id" id="hide_deleted_id" class="text_boxes" value="<? echo $txt_deleted_id; ?>">
				<input type="hidden" name="hdn_delivery_qnty" id="hdn_delivery_qnty" value="<? echo number_format($hdn_delivery_qnty,2,'.',''); ?>">
				<input type="hidden" name="hdn_dtls_id" id="hdn_dtls_id" value="<? echo $update_dtls_id; ?>">
				<?

				$cumu_delivery_qty=array();
				$sql_cuml_delivery="SELECT job_id, pub_shipment_date, job_wise_qnty, dtls_id,  b.BODY_PART_ID,c.DETARMINATION_ID
					from order_wise_pro_details a, inv_finish_fabric_issue_dtls b, product_details_master c
					where a.dtls_id=b.id and b.prod_id=c.id and a.entry_form=224 and a.job_id is not null and a.is_sales=0
					and b.order_id='$hdn_fso_id' and b.BODY_PART_ID=$cbo_body_part and b.uom=$uom  and c.DETARMINATION_ID=$fabric_desc_id and c.COLOR=$txt_color_id and b.status_active=1
					and a.status_active=1";
					//and c.GSM = $txt_gsm and c.DIA_WIDTH='$txt_dia'

				$sql_result_cuml=sql_select($sql_cuml_delivery);
				foreach($sql_result_cuml as $row)
				{
					if($update_dtls_id!="" && $update_dtls_id==$row[csf('dtls_id')]){
						$this_challan_rec_qty[$row[csf('job_id')]]+=$row[csf('job_wise_qnty')];
					}else{
						$cumu_delivery_qty[$row[csf('job_id')]]+=$row[csf('job_wise_qnty')];
						$delivery_wise_rcv_qnty +=$row[csf('job_wise_qnty')];
					}
				}

				//print_r($this_challan_rec_qty);
					
				$prev_distribution_method=2; $disable_drop_down=1; //N. B. According to Mr. Mamun

				?>
				<div id="search_div" style="margin-top:10px">
					<div style="width:<? echo $width; ?>px; margin-top:10px" align="center">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
							<thead>
								<th>Total Receive Qnty</th>
								<th>Distribution Method</th>
							</thead>
							<tr class="general">
								<td><input type="text" name="txt_prop_finish_qnty" id="txt_prop_finish_qnty" class="text_boxes_numeric" value="<? if($prev_distribution_method==1) echo $txt_production_qty; ?>" style="width:120px"  onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?>/></td>
								<td>
									<?
									$distribiution_method=array(1=>"Proportionately",2=>"Manually");
									echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0,"--Select--",$prev_distribution_method,"distribute_qnty(this.value);",$disable_drop_down );

									?>
								</td>
							</tr>
						</table>
						<? if($over_receive_limit > 0) { ?>
						<span><b>Over percentage :  <? echo $over_receive_limit;?>%</b></span>
						<?}?>
					</div>
					<div style="margin-left:10px; margin-top:10px">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width; ?>">
							<thead>
								<th width="80">Style ref. No </th>
								<th width="80">Job No</th>
								<th width="100">Booking No</th>
								<th width="80">PO Qty.</th>
								<th width="80">Req. Qty.</th>
								<th width="80">Cumu. Delivery Qty.</th>
								<th width="80">Finish Qty.</th>
							</thead>
							<tbody id="tbl_list_search">
								<?
								$i=1; $tot_po_qnty=0; $po_array=array(); $po_data_array=array();

								if($db_type==0)
								{
									$select_req = " (sum(ifnull(b.fin_fab_qnty,0)) + sum(ifnull(b.adjust_qty,0))) ";
								}else{
									$select_req = " (sum(nvl(b.fin_fab_qnty,0)) + sum(nvl(b.adjust_qty,0))) ";
								}
								
								$jobStyle_sql="SELECT a.id, d.id as job_id, b.booking_no, c.job_no_mst, d.style_ref_no, sum(d.total_set_qnty*c.po_quantity) as po_qnty, $select_req as requ_qnty
								from fabric_sales_order_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d, wo_pre_cost_fabric_cost_dtls e
								where a.id=$hdn_fso_id and a.booking_id=b.booking_mst_id and b.po_break_down_id=c.id and c.job_id= d.id and b.pre_cost_fabric_cost_dtls_id=e.id  and b.fabric_color_id=$txt_color_id and e.lib_yarn_count_deter_id=$fabric_desc_id and e.uom=$uom and b.status_active=1 and b.is_deleted=0
								group by a.id, d.id, b.booking_no, c.job_no_mst, d.style_ref_no
								order by d.style_ref_no asc";

								//, c.pub_shipment_date
								//and b.gsm_weight = $txt_gsm and b.dia_width='$txt_dia' and e.body_part_id=$cbo_body_part
								//echo $jobStyle_sql;die();

								$nameArray=sql_select($jobStyle_sql);
								if(empty($nameArray))
								{
									//N.B fabrication condition ommited here if job no. not available in query
									$jobStyle_sql="SELECT a.id, d.id as job_id, b.booking_no, c.job_no_mst, d.style_ref_no, sum(d.total_set_qnty*c.po_quantity) as po_qnty, $select_req as requ_qnty
									from fabric_sales_order_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d, wo_pre_cost_fabric_cost_dtls e
									where a.id=$hdn_fso_id and a.booking_id=b.booking_mst_id and b.po_break_down_id=c.id and c.job_id= d.id and b.pre_cost_fabric_cost_dtls_id=e.id  and b.fabric_color_id=$txt_color_id and e.uom=$uom and b.status_active=1 and b.is_deleted=0
									group by a.id, d.id, b.booking_no, c.job_no_mst, d.style_ref_no
									order by d.style_ref_no asc";
									//and e.lib_yarn_count_deter_id=$fabric_desc_id
									//, c.pub_shipment_date
									
								}
								//echo $jobStyle_sql;die();
								$nameArray=sql_select($jobStyle_sql);
								
								$po_data_array=array();
								$explSaveData = explode(",",$save_data);
								//print_r($explSaveData);
								foreach($explSaveData as $val)
								{
									$finQnty = explode("**",$val);
									$job_data_array[$finQnty[0]]['qnty']+=$finQnty[1];
								}

								foreach($nameArray as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$tot_po_qnty+=$row[csf('po_qnty')];
									$qnty = $job_data_array[$row[csf('job_id')]]['qnty'];

									//echo $row[csf('job_id')]."][".change_date_format($row[csf('pub_shipment_date')])."]<br>";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
										<td width="80" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
										<td width="80" align="center">
											<? echo $row[csf('job_no_mst')]; ?>
											<input type="hidden" name="txtJobId[]" id="txtJobId_<? echo $i; ?>" value="<? echo $row[csf('job_id')]; ?>">

										</td>
										<td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>

										<td width="80" align="right">
											<? echo $row[csf('po_qnty')]; ?>
											<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty')]; ?>">
										</td>

										<td align="right" width="80" >
											<? echo number_format($row[csf('requ_qnty')],2,'.',''); ?>
											<input type="hidden" name="txtreqqty[]" id="txtreqqty_<? echo $i; ?>" value="<? echo number_format($row[csf('requ_qnty')],2,'.',''); ?>"/>
										</td>
											<?
											//$hidden_cummulative_delivery_qnty = number_format(($cumu_delivery_qty[$row[csf('job_id')]][change_date_format($row[csf('pub_shipment_date')])]),2,'.','');
											$hidden_cummulative_delivery_qnty = number_format(($cumu_delivery_qty[$row[csf('job_id')]]),2,'.','');

											//$this_challan_rec = number_format($this_challan_rec_qty[$row[csf('job_id')]][change_date_format($row[csf('pub_shipment_date')])],2,".","");
											$this_challan_rec = number_format($this_challan_rec_qty[$row[csf('job_id')]],2,".","");
											?>
											<td width="80" align="right" id="cumul_balance_td">
												<?
												$cumul_balance=$row[csf('requ_qnty')]-$hidden_cummulative_delivery_qnty;
												echo number_format($hidden_cummulative_delivery_qnty,2,'.','');
												?>
												<input type="hidden" name="hdn_cumm_this_challan_delivery[]" id="hdn_cumm_this_challan_delivery_<? echo $i; ?>" value="<? echo $this_challan_rec; ?>"/>

												<input type="hidden" name="hidden_cummulative_delivery_qnty[]" id="hidden_cummulative_delivery_qnty_<? echo $i; ?>" value="<? echo $hidden_cummulative_delivery_qnty; ?>" />
											</td>

											<td align="center" width="80">
												<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" placeholder="<? echo number_format($cumul_balance,2,'.',''); ?>" value="<? echo $qnty; ?>"/>
											</td>
									</tr>
										<?
										$i++;
										$tot_req_qnty+=$row[csf('requ_qnty')];
								}
							
								?>
								<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
								<input type="hidden" name="tot_req_qnty" id="tot_req_qnty" class="text_boxes" value="<? echo $tot_req_qnty; ?>">
								<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<? echo $i-1; ?>">
								<input type="hidden" name="hidden_cummu_deli_rcv_qnty" id="hidden_cummu_deli_rcv_qnty" value="<? echo $delivery_wise_rcv_qnty; ?>" />
							</tbody>
						</table>
					</div>
					<table width="<? echo $width; ?>" id="table_id">
						<tr>
							<td align="center" >
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
							</td>
						</tr>
					</table>
				</div>
				</div>
				</fieldset>
			</form>
		</body>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
		<?
		exit();
}
?>

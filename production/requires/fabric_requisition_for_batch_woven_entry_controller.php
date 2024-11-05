<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down( 'requires/fabric_requisition_for_batch_woven_entry_controller', $data+'_'+this.value, 'load_drop_down_store', 'store_td' );" );
	exit();
}
if($action == "load_drop_down_store")
{
	$data = explode("_",$data);
	if ($data[1] != "" && $data[1] > 0) {$location_cond = " and a.location_id='$data[1]'";} else { $location_cond = "";}
	$sql =  "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' $location_cond $store_location_credential_cond  and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name";
	//echo $sql;
	echo create_drop_down( "cbo_store_name", 152,$sql,"id,store_name", 1, "--Select store--", 0, "" );
}

if ($action == "batch_popup")
{
	echo load_html_head_contents("Batch Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(batch_id,load_unload,batch_no,unloaded_batch,ext_from) {
			document.getElementById('hidden_batch_id').value = batch_id;
			document.getElementById('hidden_batch_no').value = batch_no;
			document.getElementById('hidden_load_unload').value = load_unload;
			document.getElementById('hidden_unloaded_batch').value = unloaded_batch;
			document.getElementById('hidden_ext_from').value = ext_from;
			parent.emailwindow.hide();
		}

	</script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:1030px;margin-left:4px;">
				<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
					<table cellpadding="0" cellspacing="0" width="500" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Search By</th>
							<th>Search</th>
							<th colspan="2">Batch Date</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
								class="formbutton"/>
								<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" value="">
								<input type="hidden" name="hidden_batch_no" id="hidden_batch_no" value="">
								<input type="hidden" name="hidden_load_unload" id="hidden_load_unload" value="">
								<input type="hidden" name="hidden_unloaded_batch" id="hidden_unloaded_batch" value="">
								<input type="hidden" name="hidden_ext_from" id="hidden_ext_from" value="">
							</th>
						</thead>
						<tr class="general">
							<td align="center">
								<?
								$search_by_arr = array(1 => "Batch No", 2 => "Booking No");
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center">
								<input type="text" style="width:140px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
	                        <td>
	                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:160px;" tabindex="6" value="" />
	                        </td>
	                        <td>
	                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:160px;" tabindex="6" value="" />
	                        </td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+'2_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_batch_search_list_view', 'search_div', 'fabric_requisition_for_batch_woven_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
								style="width:100px;"/>
							</td>
						</tr>
	                    <tr>
	                    	<td colspan="5"><? echo load_month_buttons(1);  ?></td>
	                    </tr>
					</table>
					<div id="search_div" style="margin-top:10px"></div>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action == "create_batch_search_list_view")
{
	$data = explode('_', $data);

	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$batch_against_id = $data[3];
	
	$date_from 	= $data[4];
	$date_to	= $data[5];
	
	
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_from=change_date_format(str_replace("'","",$date_from),"yyyy-mm-dd","");
			$date_to=change_date_format(str_replace("'","",$date_to),"yyyy-mm-dd","");
		}
		else
		{
			$date_from=date("j-M-Y",strtotime(str_replace("'","",$date_from)));
			$date_to=date("j-M-Y",strtotime(str_replace("'","",$date_to)));
		}
		$date_con=" and a.batch_date between '$date_from' and '$date_to'";		
		
	}



	if ($search_by == 1)
		$search_field = 'a.batch_no';
	else
		$search_field = 'a.booking_no';

	$batch_cond = "";
	if ($batch_against_id != 2) $batch_cond = " and a.batch_against=$batch_against_id";
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$po_name_arr = array();
	if ($db_type == 2) $group_concat = "  listagg(cast(b.po_number AS VARCHAR2(4000)),',') within group (order by b.id) as order_no";
	else if ($db_type == 0) $group_concat = " group_concat(b.po_number) as order_no";

	if ($db_type == 2) $group_concat2 = "  listagg(cast(b.po_id AS VARCHAR2(4000)),',') within group (order by b.id) as po_id";
	else if ($db_type == 0) $group_concat2 = " group_concat(b.po_id) as po_id";

	$sql ="SELECT a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id, $group_concat2, a.is_sales, a.re_dyeing_from from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id and $search_field like '$search_string' and a.page_without_roll=0 and a.status_active=1 and a.entry_form=563 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_cond $date_con group by a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id,a.is_sales,a.re_dyeing_from order by a.batch_date desc";
	//echo $sql;
	$result = sql_select($sql);

	if(count($result)<1)
	{
		echo "<span>Data Not Found</span>";die;
	}
	$batch_id=array();
	foreach ($result as $row) {
		$ids = implode(",", array_unique(explode(",", $row[csf("po_id")])));
		$po_ids .= $ids . ",";
		$is_sales[] = $row[csf("is_sales")];
		$batch_id[] .= $row[csf("id")];
	}
	$po_ids = rtrim($po_ids, ",");
	if($po_ids!="") $po_ids=$po_ids;else $po_ids=0;
	$sql_po = sql_select("select b.id,b.po_number from wo_po_break_down b where b.status_active=1 and b.is_deleted=0 and b.id in($po_ids)");
	$po_name_arr = array();
	foreach ($sql_po as $p_name) {
		$po_name_arr[$p_name[csf('id')]] = $p_name[csf('po_number')];
	}

	$sql_load_unload="select id, batch_id,load_unload_id,result from pro_fab_subprocess where batch_id in (".implode(",",$batch_id).") and load_unload_id in (1,2) and entry_form=35 and is_deleted=0 and status_active=1";
	$load_unload_data=sql_select($sql_load_unload);
	foreach ($load_unload_data as $row)
	{
		if($row[csf('load_unload_id')]==1)
		{
			$load_unload_arr[$row[csf('batch_id')]] = $row[csf('load_unload_id')];
		}
		else if($row[csf('load_unload_id')]==2 )
		{
			$unloaded_batch[$row[csf('batch_id')]] = $row[csf('batch_id')];
		}
	}

	$re_dyeing_from = return_library_array("select re_dyeing_from from pro_batch_create_mst where re_dyeing_from <>0 and status_active = 1 and is_deleted = 0","re_dyeing_from","re_dyeing_from");
	//print_r($re_dyeing_from);
	?>
	<style>
		table tbody tr td {
			text-align: center;
		}
	</style>
	<table class="rpt_table" id="rpt_tabletbl_list_search" rules="all" width="1020" cellspacing="0" cellpadding="0"
	border="0">
	<thead>
		<tr>
			<th width="50">SL No</th>
			<th width="100">Batch No</th>
			<th width="70">Ext. No</th>
			<th width="150">PO No./FSO No</th>
			<th width="105">Booking No</th>
			<th width="80">Batch Weight</th>
			<th width="80">Total Trims Weight</th>
			<th width="80">Batch Date</th>
			<th width="80">Batch Against</th>
			<th width="85">Batch For</th>
			<th>Color</th>
		</tr>
	</thead>
	<tbody>
		<?
		$i = 1;
		foreach ($result as $row)
		{
			if ($row[csf("is_sales")] != 1) {
				$order_id = array_unique(explode(",", $row[csf("po_id")]));
				$order_ids = "";
				foreach ($order_id as $order) {
					$order_ids .= $po_name_arr[$order] . ",";
				}
			} else {
				$order_ids = $row[csf("sales_order_no")];
			}
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			if($re_dyeing_from[$row[csf('id')]])
			{
				$ext_from = $re_dyeing_from[$row[csf('id')]];
			}else{
				$ext_from = "0";
			}
			?>
			<tr onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $load_unload_arr[$row[csf('id')]]; ?>','<? echo $row[csf('batch_no')]; ?>','<? echo $unloaded_batch[$row[csf('id')]]; ?>','<? echo $ext_from;?>')" bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
				<td width="50"><? echo $i; ?></td>
				<td width="100"><? echo $row[csf("batch_no")]; ?></td>
				<td width="70"><? echo $row[csf("extention_no")]; ?></td>
				<td width="150"><p><? echo trim($order_ids, ","); ?></p></td>
				<td width="105"><? echo $row[csf("booking_no")]; ?></td>
				<td width="80"><? echo $row[csf("batch_weight")]; ?></td>
				<td width="80"><? echo $row[csf("total_trims_weight")]; ?></td>
				<td width="80"><? echo $row[csf("batch_date")]; ?></td>
				<td width="80"><? echo $batch_against[$row[csf("batch_against")]]; ?></td>
				<td width="85"><? echo $batch_for[$row[csf("batch_for")]]; ?></td>
				<td><? echo $color_arr[$row[csf("color_id")]]; ?></td>
			</tr>
			<?
			$i++;
			
		}
		?>
	</tbody>
	</table>
	<?
	exit();
}
if ($action == "show_color_listview")
{ 
	$data = explode("**", $data);
	$batch_id = $data[0];
	$batch_no = $data[2];
	$search_type = '';
	$cbo_company_id = $data[1];
	$batch_qnty_array = array();
	$booking_no_column = ($search_type == 7) ? "a.sales_order_no" : "a.booking_no";
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$batch_sql = "SELECT a.color_id,a.color_range_id, a.booking_no booking_no, SUM (b.batch_qnty) AS qnty, b.item_description, a.sales_order_no, b.width_dia_type, b.gsm, b.grey_dia, b.fin_dia, b.po_id,b.body_part_id,b.fullwidth,b.cutablewidth,a.id FROM pro_batch_create_mst a, pro_batch_create_dtls b WHERE a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=563 and a.id =$batch_id  group by a.color_id,a.color_range_id, a.booking_no,  b.item_description, a.sales_order_no, b.width_dia_type, b.gsm, b.grey_dia, b.fin_dia, b.po_id,b.body_part_id,b.fullwidth,b.cutablewidth,a.id";
	//echo $batch_sql;
	$batch_data_array = sql_select($batch_sql);
	$po_id_arr = array();
	foreach ($batch_data_array as $row)
	{
		$batch_group =$row[csf('id')] ."*".$row[csf('po_id')] ."*". $row[csf('color_id')] ."*". $row[csf('color_range_id')] ."*". trim($row[csf('item_description')]);
		$batch_data =$row[csf('po_id')] ."*". $row[csf('color_id')] ."*". $row[csf('color_range_id')] ."*". trim($row[csf('item_description')]) ."*". $row[csf('booking_no')] ."*". $row[csf('sales_order_no')] ."*". $batch_id ."*". $row[csf('width_dia_type')] ."*". $row[csf('gsm')] ."*". $row[csf('grey_dia')] ."*". $row[csf('fin_dia')] ."*". $row[csf('body_part_id')] ."*". $row[csf('fullwidth')] ."*". $row[csf('cutablewidth')] ."*". $color_arr[$row[csf('color_id')]];
		$batch_qnty_array[$batch_group]['qnty']+= $row[csf('qnty')];
		$batch_qnty_array[$batch_group]['batch_data'] = $batch_data ;
		$po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
	}

	$po_cond = where_con_using_array($po_id_arr,1,"b.po_id");
	$sql_req = "SELECT b.color_range_id,b.color_id,b.reqn_qty,b.po_id,b.item_description,a.batch_id FROM pro_fab_reqn_for_batch_woven_mst a ,pro_fab_reqn_for_batch_woven_dtls b  where a.id = b.mst_id and  a.is_deleted =0 and a.status_active =1 and  b.is_deleted =0 and b.status_active =1 $po_cond";
	//echo $sql_req;die;
	$req_res = sql_select($sql_req);
	$requisition_arr = array();
	foreach($req_res as $row)
	{
		$batch_group =$row[csf('batch_id')] ."*".$row[csf('po_id')] ."*". $row[csf('color_id')] ."*". $row[csf('color_range_id')] ."*". trim($row[csf('item_description')]);
		$requisition_arr[$batch_group]+= $row[csf('reqn_qty')];
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="480" class="rpt_table">
		<caption>Batch List View</caption>
		<thead>
			<th width="25">SL</th>
			<th width="80">Color</th>
			<th width="150">Description</th>
			<th width="75">Batch Qty.</th>
			<th width="75">Requistion Qty.</th>
			<th>Balance</th>
		</thead>
		<?
		$i = 1;
		

		
		//echo $sql;
		$sql = sql_select($sql);

		foreach ($batch_qnty_array as $batch_group => $row)
		{
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			//echo "<pre>$batch_group</pre>";
			$requisition_qnty = $requisition_arr[$batch_group];
			$balance = $row['qnty'] - $requisition_qnty;
			$data = $row['qnty'] ."*". $row['batch_data'] ."*". $balance;
			$batch_group_data = explode("*",$batch_group);
			$color_id = $batch_group_data[2];
			$item_description = $batch_group_data[4];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="put_batch_data('<? echo $data; ?>')">
				<td width="25"><? echo $i; ?></td>
				<td width="80"><p><? echo $color_arr[$color_id]; ?></p></td>
				<td width="150"><p><? echo $item_description; ?></p></td>
				<td width="75" align="right"><p><? echo number_format($row['qnty'], 2); ?>&nbsp;</p></td>
				<td width="75" align="right"><? echo number_format($requisition_qnty, 2); ?>&nbsp;</td>
				<td align="right"><? echo number_format($balance, 2); ?></td>
			</tr>
			<?
			$i++;
		}
		?>
		</table>
	<?
	exit();
}
if ($action == "show_details_listview")
{ 
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$sql_req = "SELECT b.color_range_id,b.color_id,b.reqn_qty,b.po_id,b.item_description,a.batch_id,b.id as dtls_id,b.uom,b.roll_no FROM pro_fab_reqn_for_batch_woven_mst a ,pro_fab_reqn_for_batch_woven_dtls b  where a.id = b.mst_id and  a.is_deleted =0 and a.status_active =1 and  b.is_deleted =0 and b.status_active =1 and a.id = $data ";
	//echo $sql_req;die;
	$req_res = sql_select($sql_req);
	$requisition_arr = array();
	foreach($req_res as $row)
	{
		$batch_id = $row[csf('batch_id')];
		$batch_group =$row[csf('batch_id')] ."*".$row[csf('po_id')] ."*". $row[csf('color_id')] ."*". $row[csf('color_range_id')] ."*". trim($row[csf('item_description')]);
		$requisition_arr[$batch_group]+= $row[csf('reqn_qty')];
	}

	$batch_sql = "SELECT a.color_id,a.color_range_id, a.booking_no booking_no, SUM (b.batch_qnty) AS qnty, b.item_description, a.sales_order_no, b.width_dia_type, b.gsm, b.grey_dia, b.fin_dia, b.po_id,b.body_part_id,b.fullwidth,b.cutablewidth,a.id FROM pro_batch_create_mst a, pro_batch_create_dtls b WHERE a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=563 and a.id =$batch_id  group by a.color_id,a.color_range_id, a.booking_no,  b.item_description, a.sales_order_no, b.width_dia_type, b.gsm, b.grey_dia, b.fin_dia, b.po_id,b.body_part_id,b.fullwidth,b.cutablewidth,a.id";
	//echo $batch_sql;
	$batch_data_array = sql_select($batch_sql);
	$po_id_arr = array();
	$batch_qnty_array = array();
	foreach ($batch_data_array as $row)
	{
		$batch_group =$row[csf('id')] ."*".$row[csf('po_id')] ."*". $row[csf('color_id')] ."*". $row[csf('color_range_id')] ."*". trim($row[csf('item_description')]);
		$batch_data =$row[csf('po_id')] ."*". $row[csf('color_id')] ."*". $row[csf('color_range_id')] ."*". trim($row[csf('item_description')]) ."*". $row[csf('booking_no')] ."*". $row[csf('sales_order_no')] ."*". $batch_id ."*". $row[csf('width_dia_type')] ."*". $row[csf('gsm')] ."*". $row[csf('grey_dia')] ."*". $row[csf('fin_dia')] ."*". $row[csf('body_part_id')] ."*". $row[csf('fullwidth')] ."*". $row[csf('cutablewidth')] ."*". $color_arr[$row[csf('color_id')]];
		$batch_qnty_array[$batch_group]['qnty']+= $row[csf('qnty')];
		$batch_qnty_array[$batch_group]['batch_data'] = $batch_data ;
		$po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
	}
	//print_r($batch_qnty_array);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="480" class="rpt_table">
		<caption>Requistion List View</caption>
		<thead>
			<th width="25">SL</th>
			<th width="80">Color</th>
			<th width="150">Description</th>
			<th width="75">Requistion Qty.</th>
			<th width="75">Batch Qty.</th>
			<th>Balance</th>
		</thead>
		<?
		$i = 1;
		
		foreach ($req_res as $row)
		{
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			$batch_group =$row[csf('batch_id')] ."*".$row[csf('po_id')] ."*". $row[csf('color_id')] ."*". $row[csf('color_range_id')] ."*". trim($row[csf('item_description')]);
			$batch_qnty = $batch_qnty_array[$batch_group]['qnty'];
			$batch_data = $batch_qnty_array[$batch_group]['batch_data'] ;
			// echo "<pre>";
			// echo $batch_data;
			// echo "</pre>";
			
			$data = $row[csf('reqn_qty')] ."*". $batch_data ."*". ($batch_qnty - $requisition_arr[$batch_group] + $row[csf('reqn_qty')]) ."*". $row[csf('uom')] ."*". $row[csf('roll_no')];
			$dtls_id = $row[csf('dtls_id')];

			$balance = $batch_qnty - $requisition_arr[$batch_group];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="put_batch_data('<? echo $data; ?>','<?=$dtls_id;?>')">
				<td width="25"><? echo $i; ?></td>
				<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
				<td width="150"><p><? echo trim($row[csf('item_description')]); ?></p></td>
				<td width="75" align="right"><p><? echo number_format($row[csf('reqn_qty')], 2); ?>&nbsp;</p></td>
				<td width="75" align="right"><? echo number_format($batch_qnty, 2); ?>&nbsp;</td>
				<td align="right"><? echo number_format($balance, 2); ?></td>
			</tr>
			<?
			$i++;
		}
		?>
		</table>
	<?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$hidden_booking_no = str_replace("'","",$hidden_booking_no);
	$hidden_po_id = str_replace("'","",$hidden_po_id);
	$hidden_job_no = str_replace("'","",$hidden_job_no);
	$txt_fabric_description = str_replace("'","",$txt_fabric_description);
	$txt_gsm = str_replace("'","",$txt_gsm);
	$txt_width = str_replace("'","",$txt_width);
	$txt_machine_dia = str_replace("'","",$txt_machine_dia);
	$txt_roll_no = str_replace("'","",$txt_roll_no);
	$cbo_body_part = str_replace("'","",$cbo_body_part);
	$cbo_uom = str_replace("'","",$cbo_uom);
	$txt_rate = str_replace("'","",$txt_rate);
	$txt_amount = str_replace("'","",$txt_amount);
	$txt_batch_no = str_replace("'","",$txt_batch_no);
	$hidden_dtls_id = str_replace("'","",$hidden_dtls_id);
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
		
		$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'FRBW', date("Y",time()), 5, "select reqn_number_prefix, reqn_number_prefix_num from pro_fab_reqn_for_batch_woven_mst where company_id=$cbo_company_id and $year_cond=".date('Y',time())." order by id desc ", "reqn_number_prefix","reqn_number_prefix_num"));
		$id=return_next_id( "id", "pro_fab_reqn_for_batch_woven_mst", 1 ) ;

				 
		$field_array="id,reqn_number_prefix,reqn_number_prefix_num,reqn_number,company_id,location_id,store_id,batch_id,batch_no,reqn_date,inserted_by,insert_date";
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',".$cbo_company_id.",".$cbo_location_name.",".$cbo_store_name.",".$hidden_batch_id.",'".$txt_batch_no."',".$txt_requisition_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$field_array_dtls="id, mst_id, receive_basis, program_booking_pi_no, po_id, job_no, gsm_weight, dia_width, mc_dia, roll_no, color_id, reqn_qty,rate,amount,uom,color_range_id, item_description,body_part_id, inserted_by, insert_date";
		$dtls_id = return_next_id( "id", "pro_fab_reqn_for_batch_woven_dtls", 1 );
		$data_array_dtls.="(".$dtls_id.",".$id.",4,'".$hidden_booking_no."','".$hidden_po_id."','".$hidden_job_no."','".$txt_gsm."','".$txt_width."','".$txt_machine_dia."','".$txt_roll_no."',".$color_id.",".$txt_req_qnty.",'".$txt_rate."','".$txt_amount."','".$cbo_uom."',".$cbo_color_range.",'".$txt_fabric_description."','".$cbo_body_part."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		
		//echo "10**insert into pro_fab_reqn_for_batch_woven_mst (".$field_array.") values ".$data_array;oci_rollback($con);die;
		$rID=sql_insert("pro_fab_reqn_for_batch_woven_mst",$field_array,$data_array,0);
		$rID2=sql_insert("pro_fab_reqn_for_batch_woven_dtls",$field_array_dtls,$data_array_dtls,1);
		//oci_rollback($con);
		//echo "10**".$rID."&&".$rID2;die;

		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_mrr_number[0];
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_mrr_number[0];
			}
			else
			{
				oci_rollback($con);
				echo "5**$rID && $rID2 ** insert into pro_fab_reqn_for_batch_woven_dtls (".$field_array_dtls.") values ".$data_array_dtls;
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
		
		$field_array="company_id*location_id*store_id*batch_id*batch_no*reqn_date*updated_by*update_date";
		$data_array=$cbo_company_id."*".$cbo_location_name."*".$cbo_store_name."*".$hidden_batch_id."*'".$txt_batch_no."'*".$txt_requisition_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		
		
		$rID=true;
		$rID=sql_update("pro_fab_reqn_for_batch_woven_mst",$field_array,$data_array,"id",$update_id,0);
		$rID2=true; 
		if(empty($hidden_dtls_id))
		{
			$field_array_dtls="id, mst_id, receive_basis, program_booking_pi_no, po_id, job_no, gsm_weight, dia_width, roll_no, color_id, reqn_qty,uom,color_range_id, item_description,body_part_id, inserted_by, insert_date";
			$dtls_id = return_next_id( "id", "pro_fab_reqn_for_batch_woven_dtls", 1 );
			$data_array_dtls.="(".$dtls_id.",".$update_id.",4,'".$hidden_booking_no."','".$hidden_po_id."','".$hidden_job_no."','".$txt_gsm."','".$txt_width."','".$txt_roll_no."',".$color_id.",".$txt_req_qnty.",'".$cbo_uom."',".$cbo_color_range.",'".$txt_fabric_description."','".$cbo_body_part."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$rID2=sql_insert("pro_fab_reqn_for_batch_woven_dtls",$field_array_dtls,$data_array_dtls,1);
		}
		else
		{
			$field_array_dtls="program_booking_pi_no*po_id*job_no*gsm_weight*dia_width*roll_no*color_id*reqn_qty*uom*color_range_id*item_description*body_part_id*updated_by*update_date";
			$data_array_dtls="'".$hidden_booking_no."'*'".$hidden_po_id."'*'".$hidden_job_no."'*'".$txt_gsm."'*'".$txt_width."'*'".$txt_roll_no."'*".$color_id."*".$txt_req_qnty."*'".$cbo_uom."'*".$cbo_color_range."*'".$txt_fabric_description."'*'".$cbo_body_part."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
			$rID2=sql_update("pro_fab_reqn_for_batch_woven_dtls",$field_array_dtls,$data_array_dtls,"id",$hidden_dtls_id,0);
		}
		
		
		
		//oci_rollback($con);
		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$statusChange;die;
		
		if($db_type==0)
		{
			if($rID && $rID2  )
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_requisition_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**$rID && $rID2";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2  )
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_requisition_no);
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**$rID && $rID2";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="requisition_popup")
{
	echo load_html_head_contents("Requisition Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 

	<script>
	
		function js_set_value(data)
		{
			$('#hidden_reqn_id').val(data);
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
	                    <th>Location</th>
	                    <th>Requisition Date Range</th>
	                    <th id="search_by_td_up" width="180">Requisition No</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                    	<input type="hidden" name="hidden_reqn_id" id="hidden_reqn_id">  
	                    </th> 
	                </thead>
	                <tr class="general">
	                    <td align="center">
	                    	 <? echo create_drop_down( "cbo_location_id", 150,"select id,location_name from lib_location where company_id='$company_id' and status_active =1 and is_deleted=0 order by location_name",'id,location_name', 1, '-- Select Location --',0,"",0); ?>        
	                    </td>
	                    <td align="center">
	                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" readonly>To
						  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" readonly>
						</td>
	                    <td align="center" id="search_by_td">				
	                        <input type="text" style="width:130px" class="text_boxes"  name="txt_reqn_no" id="txt_reqn_no" />	
	                    </td> 						
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_reqn_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_location_id').value+'_'+<? echo $company_id; ?>, 'create_reqn_search_list_view', 'search_div', 'fabric_requisition_for_batch_woven_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                     </td>
	                </tr>
	                <tr>
	                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
	                </tr>
	           </table>
	           <div style="width:100%; margin-top:5px;" id="search_div" align="center"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		$('#cbo_location_id').val(0);
	</script>
	</html>
	<?
}

if($action=="create_reqn_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0]);
	$start_date =$data[1];
	$end_date =$data[2];
	$location_id =$data[3];
	$company_id =$data[4];

	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.reqn_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.reqn_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		$search_field_cond="and a.reqn_number like '$search_string'";
	}
	
	$location_cond="";
	if($location_id>0)
	{
		$location_cond="and a.location_id=$location_id";
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
	
	//$sql = "select id, $year_field reqn_number_prefix_num, reqn_number, location_id, reqn_date from pro_fab_reqn_for_batch_woven_mst where status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $location_cond $date_cond order by id"; 
	$sql ="SELECT a.id, to_char(a.insert_date,'YYYY') as year, a.reqn_number_prefix_num, a.reqn_number, a.location_id, a.reqn_date ,b.entry_form from pro_fab_reqn_for_batch_woven_mst a, pro_fab_reqn_for_batch_woven_dtls b
	where a.id=b.mst_id and b.entry_form is null and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $location_cond $date_cond group by a.id, a.insert_date, a.reqn_number_prefix_num, a.reqn_number, a.location_id, a.reqn_date ,b.entry_form
	order by a.id";
	$arr=array(0=>$location_arr);
	
	echo create_list_view("tbl_list_search", "Location, Year, Requisition No, Requisition Date", "250,70,130","700","200",0, $sql, "js_set_value", "id", "", 1, "location_id,0,0,0", $arr, "location_id,year,reqn_number_prefix_num,reqn_date","","",'0,0,0,3','');
	
	exit();
}

if($action=='populate_data_from_requisition')
{
	$data_array=sql_select("SELECT a.id, a.reqn_number, a.company_id, a.location_id, a.reqn_date,a.store_id,a.batch_id,a.batch_no from pro_fab_reqn_for_batch_woven_mst a  where  a.id='$data' and a.is_deleted =0 and a.status_active =1 ");
	
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_requisition_no').value 			= '".$row[csf("reqn_number")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_location_name').value 			= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down( 'requires/fabric_requisition_for_batch_woven_entry_controller', ".$row[csf("company_id")]."+'_'+".$row[csf("location_id")].", 'load_drop_down_store', 'store_td' );\n";
		echo "document.getElementById('txt_requisition_date').value 		= '".change_date_format($row[csf("reqn_date")])."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_store_name').value 					= '".$row[csf("store_id")]."';\n";
		echo "document.getElementById('hidden_batch_id').value 					= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_batch_no').value 					= '".$row[csf("batch_no")]."';\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_fabric_requisition_for_batch',1);\n";  
		exit();
	}
}



if($action=="print_fab_req_for_batch")
{
	extract($_REQUEST);
	//echo $data;
	$ex_data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	//$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$yarncount=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	
	$sql_mst="Select id, reqn_number,location_id,reqn_date from pro_fab_reqn_for_batch_woven_mst where company_id=$ex_data[0] and id='$ex_data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	
	?>
    <div style="width:1060px;">
    <table width="100%" cellpadding="0" cellspacing="0" >
        <tr>
            <td width="70" align="right"> 
            	<img  src='../../<? echo $imge_arr[$ex_data[0]]; ?>' height='100%' width='100%' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr class="form_caption">
                    	<td align="center" style="font-size:18px"><strong ><? echo $company_library[$ex_data[0]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="center" style="font-size:14px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="center" style="font-size:14px"><? echo show_company($ex_data[0],'',''); ?> </td>  
                    </tr>
                    <tr class="form_caption">
                    	<td align="center" style="font-size:16px"><u><strong><? echo $ex_data[3]; ?></strong></u></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table width="930" cellspacing="0" align="" border="0">
        <tr>
            <td width="130"><strong>Requisition No :</strong></td> <td width="175"><? echo $dataArray[0][csf('reqn_number')]; ?></td>
            <td width="130"><strong>Requisition Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('reqn_date')]); ?></td>
            <td width="130">&nbsp;</td> <td width="175">&nbsp;</td>
        </tr>
    </table>
    <br>
	<div style="width:100%;">
		<table align="left" cellspacing="0" width="1060"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" style="font-size:13px">
                <th width="20">SL</th>
                <th width="110">File/Ref. No</th>
                <th width="100">Buyer/ Job /Order</th>
                <th width="120">Construction, Composition</th>
                <th width="30">GSM</th> 
                <th width="30">Dia</th>
                <th width="70">Color/ Code</th>
                <th width="60">Prog. /Book Qty.</th>
                <th width="60">Total Req. Qty.</th>
                <th width="60">Balance</th>
                <th width="60">Reqsn. Qty.</th>
                <th width="90">Remarks</th>
                <th width="45">Prog. No</th>
                <th width="100">Booking No</th>
                <!--<th>Yarn Lot/  Count</th>-->
            </thead>
            <tbody>
    		<?
			if($db_type==0) $year_val="year(a.insert_date)"; else if( $db_type==2) $year_val="TO_CHAR(a.insert_date,'YYYY')";
			$po_arr=array();
			$po_sql="select a.style_ref_no, $year_val as year, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$ex_data[0]'";
			$po_sql_result=sql_select($po_sql);
			foreach( $po_sql_result as $row )
			{
				$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
				$po_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
				$po_arr[$row[csf('id')]]['file']=$row[csf('file_no')];
				$po_arr[$row[csf('id')]]['ref']=$row[csf('grouping')];
				$po_arr[$row[csf('id')]]['year']=$row[csf('year')];
			}
			
			$composition_arr=array(); $constructtion_arr=array();
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
			$data_array=sql_select($sql_deter);
			foreach( $data_array as $row )
			{
				$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
				$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
			
			
			$product_arr=array();
			$sql_product="select id, gsm, dia_width from product_details_master where item_category_id=13";
			$data_array=sql_select($sql_product);
			foreach( $data_array as $row )
			{
				$product_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
				$product_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
			}
			
			$program_qnty_array=array(); $program_bookingNo_array=array();
			$programData=sql_select("select po_id, booking_no, dtls_id, determination_id, gsm_weight, dia, sum(program_qnty) as qnty from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 group by po_id, dtls_id, determination_id, gsm_weight, dia, booking_no");
			foreach($programData as $row)
			{
				$program_qnty_array[$row[csf('po_id')]][$row[csf('dtls_id')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]]=$row[csf('qnty')];
				$program_bookingNo_array[$row[csf('dtls_id')]]=$row[csf('booking_no')];
			}
			
			$booking_qnty_array=array(); $samp_booking_qnty_array=array();
			$bookingData=sql_select("select a.po_break_down_id, a.booking_no, b.lib_yarn_count_deter_id as deter_id, b.gsm_weight, a.dia_width, sum(a.grey_fab_qnty) as qnty from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by a.po_break_down_id, a.booking_no, b.lib_yarn_count_deter_id, b.gsm_weight, a.dia_width");
			foreach($bookingData as $row)
			{
				$booking_qnty_array[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]=$row[csf('qnty')];
			}
			
			$sampBookingData=sql_select("select a.booking_no, a.lib_yarn_count_deter_id as deter_id, a.gsm_weight, a.dia_width, sum(a.grey_fabric) as qnty from wo_non_ord_samp_booking_dtls a, wo_non_ord_samp_booking_mst b where a.booking_no=b.booking_no and b.item_category=2 and a.status_active=1 and a.is_deleted=0 group by a.booking_no, a.lib_yarn_count_deter_id, a.gsm_weight, a.dia_width");
			foreach($sampBookingData as $row)
			{
				$samp_booking_qnty_array[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]=$row[csf('qnty')];
			}
			
			$reqn_qnty_array=array();
			$reqnData=sql_select("select receive_basis, program_booking_pi_id, po_id, prod_id, determination_id, color_id, sum(reqn_qty) as qnty from pro_fab_reqn_for_batch_woven_dtls where status_active=1 and is_deleted=0 group by receive_basis, program_booking_pi_id, po_id, prod_id, determination_id, color_id");
			foreach($reqnData as $row)
			{
				$reqn_qnty_array[$row[csf('receive_basis')]][$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]=$row[csf('qnty')];
			}
			
			$sql="select id, receive_basis, program_booking_pi_no, program_booking_pi_id, po_id, buyer_id, job_no, prod_id, determination_id, color_id, reqn_qty, remarks from pro_fab_reqn_for_batch_woven_dtls where mst_id='$ex_data[1]' and status_active=1 and is_deleted=0";
			$result=sql_select($sql);
			$i=1;
			foreach($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$file_ref_no="";
				$file_ref_no="F: K/".$po_arr[$row[csf('po_id')]]['year'].'/'.$po_arr[$row[csf('po_id')]]['file'].'<br> R: '.$po_arr[$row[csf('po_id')]]['ref'];
				$buyer_job_ord="";
				$buyer_job_ord="B: ".$buyer_arr[$row[csf('buyer_id')]].'<br> J: '.$row[csf('job_no')].'<br> O: '.$po_arr[$row[csf('po_id')]]['po'];
				$const_comp="";
				$const_comp=$constructtion_arr[$row[csf('determination_id')]].', '.$composition_arr[$row[csf('determination_id')]];
				
				$gsm=$product_arr[$row[csf('prod_id')]]['gsm'];
				$dia=$product_arr[$row[csf('prod_id')]]['dia'];
				
				$programNo=''; $reqQty=0; $bookingNo='';
				if($row[csf('receive_basis')]==1) 
				{
					$programNo=$row[csf('program_booking_pi_no')];
					$bookingNo=$program_bookingNo_array[$row[csf('program_booking_pi_id')]];
					$reqQty=$program_qnty_array[$row[csf('po_id')]][$row[csf('program_booking_pi_id')]][$row[csf('determination_id')]][$gsm][$dia];
				}
				else if($row[csf('receive_basis')]==2) 
				{
					$bookingNo=$row[csf('program_booking_pi_no')];	
					if($row[csf('po_id')]>0)
						$reqQty=$booking_qnty_array[$row[csf('po_id')]][$row[csf('program_booking_pi_no')]][$row[csf('determination_id')]][$gsm][$dia];
					else 
						$reqQty=$samp_booking_qnty_array[$row[csf('program_booking_pi_no')]][$row[csf('determination_id')]][$gsm][$dia];
				}
				
				$totReqnQty=$reqn_qnty_array[$row[csf('receive_basis')]][$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('determination_id')]][$row[csf('color_id')]];
				$reqQty=number_format($reqQty,2,'.','');
				$totReqnQty=number_format($totReqnQty,2,'.','');
				$balance=number_format($reqQty-$totReqnQty,2,'.','');
				
				$yarn_count='';
				/*$yarn_count_id=array_unique(explode(',',$row[csf('count')]));
				foreach($yarn_count_id as $id)
				{
					if($id>0)
					{
						if($yarn_count=='') $yarn_count=$yarncount[$id]; else $yarn_count.=", ".$yarncount[$id];
					}
				}*/
				
				$color='';
				$color_id=array_unique(explode(',',$row[csf('color_id')]));
				foreach($color_id as $id)
				{
					if($id>0)
					{
						if($color=='') $color=$color_arr[$id]; else $color.=", ".$color_arr[$id];
					}
				}	
				
				$lot_count="";
				//$lot_count="L :".$row[csf('lot')].'<br> C :'.$yarn_count;
				
				?>
				 <tr bgcolor="<? echo $bgcolor; ?>" style="font-size:13px">
					<td><? echo $i; ?></td>
					<td><div style="word-wrap:break-word; width:110px"><? echo $file_ref_no; ?></div></td>
					<td><div style="word-wrap:break-word; width:100px"><? echo $buyer_job_ord; ?></div></td>
					<td><div style="word-wrap:break-word; width:120px"><? echo $const_comp; ?></div></td>
					<td><? echo $gsm; ?></td> 
					<td><? echo $dia; ?></td>
					<td><div style="word-wrap:break-word; width:70px"><? echo $color; ?></div></td>
					<td align="right"><? echo number_format($reqQty,2); ?></td>
					<td align="right"><? echo number_format($totReqnQty,2); ?></td>
					<td align="right"><? echo number_format($balance,2); ?></td>
					<td align="right"><? echo number_format($row[csf('reqn_qty')],2); ?></td>
					<td><div style="word-wrap:break-word; width:110px"><? echo $row[csf('remarks')]; ?></div></td>
					<td align="center"><? echo $programNo; ?></td>
					<td><div style="word-wrap:break-word; width:100px"><? echo $bookingNo; ?></div></td>
					<!--<td><div style="word-wrap:break-word; width:95px"><?echo $lot_count; ?></div></td>-->
				</tr>
				<?
				$grnd_prog_book_qty+=$reqQty;
				$grnd_tot_req_qty+=$totReqnQty;
				$grnd_balance+=$balance;
				$grnd_reqn_qty+=$row[csf('reqn_qty')];
				$i++;
			}
			?>
            </tbody>
            <tfoot bgcolor="#dddddd" style="font-size:13px">
            	<tr>
                	<td colspan="7" align="right"><strong>Total :</strong></td>
                    <td align="right"><? echo number_format($grnd_prog_book_qty,2); ?></td>
                    <td align="right"><? echo number_format($grnd_tot_req_qty,2); ?></td>
                    <td align="right"><? echo number_format($grnd_balance,2); ?></td>
                    <td align="right"><? echo number_format($grnd_reqn_qty,2); ?></td>
                    <td colspan="4">&nbsp;</td>
                </tr>
            </tfoot>
        </table>
        </div>
        <br>
		 <?
            echo signature_table(93, $ex_data[0], "1060px");
         ?>
    </div>
    <?
	exit();
}
?>

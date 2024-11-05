<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:../../../login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


//--------------------------------------------------------------------------------------------------------------------
if($action=="batchnumbershow")
{
	echo load_html_head_contents("batch Info", "../../../", 1, 1,'','','',1);
	extract($_REQUEST);
	//echo $type;
	?>
	<script type="text/javascript">
        function js_set_value(id)
        { 
            document.getElementById('selected_id').value=id;
            parent.emailwindow.hide();
        }
    </script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	if ($company_name==0) $com_con=""; else $com_con="and a.company_id='$company_name'";
	if ($batch_against==0) $batch_against_cond=""; else $batch_against_cond="and a.batch_against='$batch_against'";

	 $sql="SELECT a.batch_no,c.file_no,c.grouping,c.job_no_mst,a.company_id from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c where a.id=b.mst_id $com_con $batch_against_cond and b.po_id=c.id and  a.is_deleted=0 and b.is_deleted=0 group by a.batch_no,c.file_no,c.grouping,c.job_no_mst,a.company_id";	

	 echo create_list_view("list_view", "Job No,Batch No.", "100,100","420","350",0, $sql, "js_set_value", "job_no_mst,batch_no", "", 1, "0,0,0,0,0", $arr , "job_no_mst,batch_no", "",'setFilterGrid("list_view",-1);','0') ;
	exit();
}

if ($action == "batch_popup") 
{
	echo load_html_head_contents("Batch Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(batch_id,batch_no) 
		{
			document.getElementById('hidden_batch_id').value = batch_id;
			document.getElementById('hidden_batch_no').value = batch_no;
			parent.emailwindow.hide();
		}

	</script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:630px;margin-left:4px;">
				<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
					<table cellpadding="0" cellspacing="0" width="500" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Search By</th>
							<th>Search</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
								class="formbutton"/>
								<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" value="">
								<input type="hidden" name="hidden_batch_no" id="hidden_batch_no" value="">
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
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $company_name; ?>+'_'+<? echo $batch_against; ?>, 'create_batch_search_list_view', 'search_div', 'batch_and_fabric_wise_roll_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
								style="width:100px;"/>
							</td>
						</tr>
					</table>
					<div id="search_div" style="margin-top:10px"></div>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
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

	if ($search_by == 1)
		$search_field = 'a.batch_no';
	else
		$search_field = 'a.booking_no';

	$batch_cond = "";
	if ($batch_against_id != 2) $batch_cond = " and a.batch_against=$batch_against_id";
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	$po_name_arr = array();
	if ($db_type == 2) $group_concat = "  listagg(cast(b.po_number AS VARCHAR2(4000)),',') within group (order by b.id) as order_no";
	else if ($db_type == 0) $group_concat = " group_concat(b.po_number) as order_no";

	if ($db_type == 2) $group_concat2 = "  listagg(cast(b.po_id AS VARCHAR2(4000)),',') within group (order by b.id) as po_id";
	else if ($db_type == 0) $group_concat2 = " group_concat(b.po_id) as po_id";

	$sql ="select a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id,$group_concat2,b.is_sales,a.re_dyeing_from from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id and $search_field like '$search_string' and a.page_without_roll=0 and a.status_active=1 and a.entry_form=0 and a.is_deleted=0 $batch_cond group by a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id,b.is_sales,a.re_dyeing_from order by a.batch_date desc";
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
			if( ($batch_against_id !=2  && $row[csf("batch_against")] !=2) || ($batch_against_id ==2 && ($row[csf("batch_against")] ==2 || $unloaded_batch[$row[csf('id')]])) )
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
				<tr onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('batch_no')]; ?>')" bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
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
		}
		?>
	</tbody>
	</table>
	<?
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name=str_replace("'","",$cbo_company_name);
	$batch_against_id=str_replace("'","",$cbo_batch_against);
	$batch_no=str_replace("'","",$txt_batch_no);
	$type=str_replace("'","",$type);

	$sql_batch=sql_select("select id from pro_batch_create_mst where batch_no='$batch_no' ");
	$batch_update_id = $sql_batch[0][csf('id')];

	// ========================= fabric grade ==========================
	// $sql_fab_grade = "SELECT a.barcode_no,b.fabric_grade,b.roll_length from pro_finish_fabric_rcv_dtls a, pro_qc_result_mst b where a.id=b.pro_dtls_id and  a.batch_id=$batch_update_id and a.status_active=1 and b.status_active=1";

	//$sql_fab_grade = "SELECT b.barcode_no,b.fabric_grade,b.roll_length,b.entry_form from pro_qc_result_mst b where b.status_active=1 and b.entry_form in(267,283)";
	$sql_fab_grade = "SELECT b.barcode_no,b.fabric_grade,b.roll_length,b.entry_form from pro_qc_result_mst b, pro_batch_create_dtls c where b.barcode_no = c.barcode_no and c.mst_id=$batch_update_id and b.status_active=1 and b.entry_form in(267,283)";
	$sql_fab_grade_res = sql_select($sql_fab_grade);
	$fab_grade_array = array();
	foreach ($sql_fab_grade_res as $val) 
	{
		$fab_grade_array[$val[csf('barcode_no')]][$val[csf('entry_form')]]['fabric_grade'] = $val[csf('fabric_grade')];
		$fab_grade_array[$val[csf('barcode_no')]][$val[csf('entry_form')]]['roll_length'] = $val[csf('roll_length')];
	}

	// ========================= fabric fin. qty ==========================
	
	$sql_fin_fab = "SELECT sum(a.receive_qnty) as fin_qnty, b.barcode_no FROM pro_finish_fabric_rcv_dtls a, pro_roll_details b WHERE a.id = b.dtls_id AND b.entry_form = 66 AND b.status_active = 1 AND b.is_deleted = 0 and a.batch_id=$batch_update_id group by b.barcode_no"; 
	//$sql_fin_fab = "SELECT sum(b.qnty) as fin_qnty, b.barcode_no FROM pro_finish_fabric_rcv_dtls a, pro_roll_details b WHERE a.id = b.dtls_id AND b.entry_form = 66 AND b.status_active = 1 AND b.is_deleted = 0 and a.batch_id=$batch_update_id group by b.barcode_no";
	
	// $sql_fin_fab = "SELECT b.barcode_no,sum(b.receive_qnty) as fin_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c where a.id=b.mst_id and a.id=c.mst_id and c.id=b.trans_id and b.batch_id='$batch_update_id' and a.status_active=1 and b.status_active=1 group by b.barcode_no"; 
	// echo $sql_fin_fab;die();
	$sql_fin_fab_res = sql_select($sql_fin_fab);
	$fin_qty_array = array();
	foreach ($sql_fin_fab_res as $val) 
	{
		$fin_qty_array[$val[csf('barcode_no')]] = $val[csf('fin_qnty')];
	}

	if($type==1) // ROLL WISE
	{
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		//$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
		$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
		$machine_no_arr = return_library_array("select id,machine_no from lib_machine_name", 'id', 'machine_no');
		$sample_type_arr = return_library_array("select id,sample_name from lib_sample where is_deleted=0 and status_active=1 order by sample_name","id","sample_name");

		
		$trims_item='';
		$sql_trims=sql_select("select item_description from pro_batch_trims_dtls where mst_id=$batch_update_id ");
		if(count($sql_trims)>0)
		{
			foreach ($sql_trims as $row) {
				if( $trims_item=='') $trims_item=$row[csf('item_description')];else $trims_item.=', '.$row[csf('item_description')];
			}
		}
		if ($db_type == 0) $year_field = "DATE_FORMAT(a.insert_date,'%y')";
		else $year_field = "to_char(a.insert_date,'YY')";
		if ($db_type == 0) 
		{
			$sql = "SELECT a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic,a.dyeing_machine, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.remarks, a.collar_qty, a.cuff_qty, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id,b.is_sales,$year_field as year from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic,a.dyeing_machine, a.extention_no, a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight,a.remarks,a.collar_qty, a.cuff_qty,b.is_sales,a.insert_date";
		} 
		else 
		{
			$sql = "SELECT a.id, a.batch_no, a.booking_no_id,a.booking_no,a.booking_without_order, a.color_id, a.batch_against, a.color_range_id, a.organic,a.dyeing_machine, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.remarks, a.collar_qty, a.cuff_qty, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id,b.is_sales,$year_field as year from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.color_id, a.batch_against, a.color_range_id, a.organic ,a.dyeing_machine,a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight,a.remarks,a.collar_qty,a.cuff_qty,b.is_sales,a.insert_date";
		}
		// echo $sql;
		$dataArray = sql_select($sql);
		$batch_color_id=$dataArray[0][csf('color_id')];
		$color_sql="select id, color_name from lib_color where id in($batch_color_id)";
		$color_data_arr=sql_select($color_sql);
		$color_arr=array();
		foreach ($color_data_arr as $key => $value) 
		{
			$color_arr[$value[csf('id')]]=$value[csf('color_name')];
		}
		$po_id = array_filter(array_unique(explode(",", $dataArray[0][csf('po_id')])));

		$booking_no = $dataArray[0][csf('booking_no')];
		$non_order_arr=array();
		$sql_non_order=sql_select("select b.body_part_id,d.booking_no,d.style_des,d.sample_type from wo_non_ord_samp_booking_dtls d,pro_batch_create_mst a, pro_batch_create_dtls b where  a.id=b.mst_id and d.booking_no=a.booking_no and d.booking_no='$booking_no' and a.id=$batch_update_id and  d.body_part=b.body_part_id and d.status_active=1 and d.is_deleted=0 group by b.body_part_id,d.booking_no,d.style_des,d.sample_type");

		foreach ($sql_non_order as $nonOrder_row) 
		{
			$non_order_arr[$nonOrder_row[csf('booking_no')]]["sample_type"] 	= $nonOrder_row[csf('sample_type')];
			$non_order_arr[$nonOrder_row[csf('booking_no')]]["style_des"] 		= $nonOrder_row[csf('style_des')];
		}
			
		$job_array = array();
		if($dataArray[0][csf('is_sales')] != 1)
		{
			$job_sql = "select distinct(a.buyer_name) as buyer_name,a.style_ref_no, a.job_no_prefix_num, a.job_no, b.pub_shipment_date, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in (" .implode(",",$po_id).")";
			$job_sql_result = sql_select($job_sql);
			foreach ($job_sql_result as $row) {
				$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
				$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
				$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
				$job_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];
				$job_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
				$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
				$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
			}
		}

		$job_arr=array(); $sales_arr=array();
		if($dataArray[0][csf('is_sales')] == 1)
		{
			$sql_job=sql_select("SELECT a.buyer_id, b.job_no as job_no_mst,b.booking_no,b.po_break_down_id,d.style_ref_no from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and d.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_type in(1,4) and c.id in (".implode(",",$po_id).") group by b.job_no,b.booking_no,a.buyer_id,b.po_break_down_id,d.style_ref_no");

			foreach ($sql_job as $job_row) 
			{
				$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] 		= $job_row[csf('job_no_mst')];
				$job_arr[$job_row[csf('booking_no')]]["buyer_id"] 			= $job_row[csf('buyer_id')];
				$job_arr[$job_row[csf('booking_no')]]["style_ref"] 			= $job_row[csf('style_ref_no')];
			}

			$sql_sales=sql_select("select id,job_no,within_group,buyer_id,sales_booking_no,delivery_date from fabric_sales_order_mst where status_active=1 and is_deleted=0 and id in (".implode(",",$po_id).")");
			foreach ($sql_sales as $sales_row) 
			{
				$sales_arr[$sales_row[csf('id')]]["po_number"] 			= $sales_row[csf('job_no')];
				$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
				$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
				$sales_arr[$sales_row[csf('id')]]["style_ref_no"] 		= $sales_row[csf('style_ref_no')];
				$sales_arr[$sales_row[csf('id')]]["delivery_date"] 		= $sales_row[csf('delivery_date')];
			}
		}

		$po_number = "";
		$job_number = "";
		$job_style = "";
		$buyer_id = "";
		$ship_date = "";
		$internal_ref = "";
		$file_nos = "";
		
		$batch_against_id = $dataArray[0][csf('batch_against')];
		$batch_booking_id = $dataArray[0][csf('booking_no_id')];
		$batch_product_id = $dataArray[0][csf('prod_id')];
		$batch_booking_without = $dataArray[0][csf('booking_without_order')];
		$is_sales = $dataArray[0][csf('is_sales')];
		foreach ($po_id as $val) 
		{
			if ($is_sales == 1) 
			{
				$within_group = $sales_arr[$val]["within_group"];
				$po_number 	  = $sales_arr[$val]["po_number"];
				$ship_date 	  = $sales_arr[$val]["delivery_date"];
				if ($within_group == 1) 
				{
					$sales_booking_no = $sales_arr[$val]["sales_booking_no"];
					$job_number = $job_arr[$sales_booking_no]["job_no_mst"];
					$buyer_id 	= $job_arr[$sales_booking_no]["buyer_id"];
					$job_style 	= $job_arr[$sales_booking_no]["style_ref"];
				}
				else
				{
					$job_number = "";
					$buyer_id 	= "";
					$job_style 	= "";
				}
			}
			else
			{
				if ($po_number == "") $po_number = $job_array[$val]['po']; else $po_number .= ',' . $job_array[$val]['po'];
				if ($job_number == "") $job_number = $job_array[$val]['job']; else $job_number .= ',' . $job_array[$val]['job'];
				if ($job_style == "") $job_style = $job_array[$val]['style_ref']; else $job_style .= ',' . $job_array[$val]['style_ref'];
				if ($buyer_id == "") $buyer_id = $job_array[$val]['buyer']; else $buyer_id .= ',' . $job_array[$val]['buyer'];
				if ($ship_date == "") $ship_date = change_date_format($job_array[$val]['ship_date']); else $ship_date .= ',' . change_date_format($job_array[$val]['ship_date']);

				if ($internal_ref == "") $internal_ref = $job_array[$val]['ref']; else $internal_ref .= ',' . $job_array[$val]['ref'];
				if ($job_array[$val]['file_no'] > 0) {
					if ($file_nos == "") $file_nos = $job_array[$val]['file_no']; else $file_nos .= ',' . $job_array[$val]['file_no'];
				}
			}
		}

		$job_no = implode(",", array_unique(explode(",", $job_number)));
		$jobstyle = implode(",", array_unique(explode(",", $job_style)));
		$buyer = implode(",", array_unique(explode(",", $buyer_id)));
		$internal_ref = implode(",", array_unique(array_filter(explode(",", $internal_ref))));
		$file_nos = implode(",", array_unique(explode(",", $file_nos)));

		if ($dataArray[0][csf('booking_without_order')] == 1) 
		{
			$booking_without_order = sql_select("select booking_no_prefix_num, buyer_id from wo_non_ord_samp_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");

			$booking_id = $booking_without_order[0][csf('booking_no_prefix_num')];
			$buyer_id_booking = $booking_without_order[0][csf('buyer_id')];
		} 
		else 
		{
			$booking_with_order = sql_select("select booking_no_prefix_num, buyer_id from wo_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
			$booking_id = $booking_with_order[0][csf('booking_no_prefix_num')];
			$buyer_id_booking = $booking_with_order[0][csf('buyer_id')];
		}
		$batch_sl_no = $dataArray[0][csf("year")]."-".$dataArray[0][csf("id")] ;
		//============================== po process loss ==================================
		$sql_pro_loss = "SELECT a.body_part_id,b.po_break_down_id as po_id,b.color_number_id as color_id,b.process_loss_percent from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.status_active=1 and b.status_active=1 and a.id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no'";
		$sql_pro_loss_res = sql_select($sql_pro_loss);
		$po_process_loss_array = array();
		foreach ($sql_pro_loss_res as $val) 
		{
			if($val !=0)
			{
				$po_process_loss_array[$val[csf('po_id')]][$val[csf('color_id')]][$val[csf('body_part_id')]] = $val[csf('process_loss_percent')];
			}
			
		}
		
		ob_start();
		?>
		<style type="text/css">
			table tr td{word-break: break-all;word-wrap: break-word;}
		</style>
		<div style="width: 1450px;">
	    	<table cellpadding="0" align="center" cellspacing="0" width="1430" align="center">
				<tr>
				   <td  width="100%" align="center" class="form_caption"><? echo $report_title; ?></td>
				</tr>
				<tr>
					<td align="center" width="100%" style="font-size:18px">
						<strong><? echo $company_library[$company_name]; ?></strong></td>
				</tr>
				<tr>
					<td align="center"  width="100%">Print Time:<? echo $date = date("F j, Y, g:i a"); ?></td>
				</tr>
			</table>
			<div style="margin-bottom: 15px;">
				<table width="1430" cellspacing="0" align="center" border="0">					
					<tr>
						<td colspan="20" align="center">
							<? 
							if ($dataArray[0][csf('dyeing_machine')] != 0) 
								{ ?>
									<strong>M/C	No:</strong>&nbsp;  <strong><? echo $machine_no_arr[$dataArray[0][csf('dyeing_machine')]];
								} 
								else echo '&nbsp; '; 
								?></strong>
						</td>
					</tr>
				</table>
					<table width="1430" cellspacing="0" align="center" border="0">		
					<tr>
						<td width="690" colspan="10" align="left" style="font-size:16px">
							<strong><u>Batch Information</u></strong>
						</td>
						<td  width="690" colspan="10" align="left" style="font-size:16px">
							<strong><u>Order Information</u></strong>
						</td>
					</tr>
				</table>
				<br>
				<table width="1430" cellspacing="0" align="center" border="0" class="header_info">		
					<tr>
						<td width="145"><strong>Batch No</strong></td>
						<td width="200">:&nbsp;<strong><? echo $dataArray[0][csf('batch_no')]; ?></strong></td>
						<td width="145"><strong>Batch SL</strong></td>
						<td width="200">:&nbsp;<strong><? echo $batch_sl_no; ?></strong></td>

						<?
						if ($dataArray[0][csf('batch_against')] == 3) {
							?>
						<td width="145"><strong>Booking no</strong></td>
						<td width="200">:&nbsp;<strong><? echo $booking_id; ?></strong></td>

						<? } else { ?>
						<td width="145"><strong>Job</strong></td>
						<td width="200">:&nbsp;<strong><? echo $job_no; ?></strong></td>
						<? }
						?>

						<td width="145"><strong>Order No</strong></td>
						<td width="200">:&nbsp;<strong><? echo $po_number; ?></strong></td>
					</tr>
					<tr>
						<td width="145"><strong>B. Color</strong></td>
						<td width="200">:&nbsp;<strong><? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></strong></td>
						<td width="145"><strong>Color Ran.</strong></td>
						<td width="200">:&nbsp;<strong><? echo $color_range[$dataArray[0][csf('color_range_id')]]; ?></strong></td>

						<td><strong>Buyer</strong></td>
						<td>
							:&nbsp;<strong>
								<?
								if ($dataArray[0][csf('batch_against')] == 3)
								{
									echo $buyer_arr[$buyer_id_booking];
								}
								else if ($dataArray[0][csf('booking_without_order')] == 1) 
								{
									echo $buyer_arr[$buyer_id_booking];
								}
								else
								{
									$buyer_name_show="";
									foreach (explode(",", $buyer) as $val) {
										$buyer_name_show .= $buyer_arr[$val].",";
									}
									echo chop($buyer_name_show,",");
								}
								?>
							</strong>
						</td>
						<td><strong>Style</strong></td>
						<td>:&nbsp;<strong><? echo $jobstyle; ?></strong></td>
					</tr>
					<tr>
						<td><strong>Batch Against</strong></td>
						<td>:&nbsp;<strong><? echo $batch_against[$dataArray[0][csf('batch_against')]]; ?></strong></td>
						<td><strong>Batch Ext.</strong></td>
						<td>:&nbsp;<strong><? echo $dataArray[0][csf('extention_no')]; ?></strong></td>

						<td><strong>Style Des</strong></td>
						<td>:&nbsp;<strong><? echo $non_order_arr[$dataArray[0][csf('booking_no')]]["style_des"]; ?></strong></td>
						<td><strong>Ship Date</strong></td>
						<td>
							:&nbsp;<strong><? if (trim($ship_date) != "0000-00-00" && trim($ship_date) != "") echo $ship_date; else echo "&nbsp;"; ?></strong></td>
					</tr>
					<tr>
						<td><strong>Batch For</strong></td>
						<td>:&nbsp;<strong><? echo $batch_for[$dataArray[0][csf('batch_for')]]; ?></strong></td>
						<td><strong>B. Weight</strong></td>
						<td>:&nbsp;<strong><? echo $dataArray[0][csf('batch_weight')]; ?></strong></td>

						<td><strong>Int. Ref.</strong></td>
						<td>:&nbsp;<strong><? echo $internal_ref; ?></strong></td>
						<td><strong>File No</strong></td>
						<td>:&nbsp;<strong><? echo $file_nos; ?></strong></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>

						<td><strong>Sample Type</strong></td>
						<td>:&nbsp;<strong><? echo $sample_type_arr[$non_order_arr[$dataArray[0][csf('booking_no')]]["sample_type"]]; ?></strong></td>
						<td><strong>Remarks</strong></td>
						<td>:&nbsp;<strong><? echo $dataArray[0][csf('remarks')]; ?></strong></td>
					</tr>
				</table>
			</div>
			
			<div style="float:left; font-size:16px;"><strong><u>Fabrication Details</u></strong></div>
			<br clear="all">
			<table align="center" cellspacing="0" style="font-size:21px" width="1430" border="1" rules="all" class="rpt_table" id="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30">SL</th>
						<th width="60">Prog. No</th>
						<th width="100">Roll ID</th>
						<th width="100">Body part</th>
						<th width="150">Const. & Comp.</th>
						<th width="50">Fin. GSM</th>
						<th width="50">Fin. Dia</th>
						<th width="70">M/Dia X <br>Gauge</th>
						<th width="70">D/W Type</th>
						<th width="60">S. Length</th>
						<th width="80">Yarn Lot</th>
						<th width="100">Yarn Supp.</th>
						<th width="100">Yarn Count</th>
						<th width="50">Grey Fabric Grade</th>
						<th width="50">Grey Qty</th>
						<th width="50">Finish Fabric Grade</th>
						<th width="50">Finish Qty</th>
						<th width="50">Qty in Pcs</th>
						<th width="50">Length</th>
						<th width="50">Actual Process Loss %</th>
						<th width="50">PO Process Loss %</th>
						<th width="60">Status</th>
					</tr>
				</thead>	
				<?
				$i = 1;
				$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
				$supplier_array_lib = return_library_array("select id,short_name from  lib_supplier", "id", "short_name");
				$machine_array_lib_dia = return_library_array("select id,dia_width from  lib_machine_name", "id", "dia_width");
				$machine_array_lib_gauge = return_library_array("select id,gauge from  lib_machine_name", "id", "gauge");

				$supplier_brand = return_library_array("select id,brand_name from lib_brand", "id", "brand_name");

				$machine_lib_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
				foreach ($machine_lib_sql as $row) {
					$dya_gauge_arr[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
					$dya_gauge_arr[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
					$dya_gauge_arr[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
				}

				$yarn_lot_arr = array();
				$sample_arr = array();
				$yarn_count = array();
				$s_length = array();
				if ($batch_against_id == 3 && $batch_booking_without == 1) 
				{
					$yarn_lot_data = sql_select("select  p.booking_id, a.prod_id, a.yarn_lot, a.yarn_count, a.stitch_length,a.machine_no_id from inv_receive_master p, pro_grey_prod_entry_dtls a where  p.id=a.mst_id and p.booking_id='$batch_booking_id' and p.booking_without_order=1 and a.prod_id in($batch_product_id) and p.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0");
					foreach ($yarn_lot_data as $rows) 
					{
						$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['yarncount'] = $rows[csf('yarn_count')];
						$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['stitch_length'] = $rows[csf('stitch_length')];
						$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['samplelot'] = $rows[csf('yarn_lot')];
						$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['machine_no_id'] = $rows[csf('machine_no_id')];
					}
				} 
				else 
				{
					$from_order_sql = sql_select("select a.from_order_id,a.to_order_id from inv_item_transfer_mst a where a.entry_form in(13,83,133) and a.to_order_id in (".implode(",",$po_id).") and a.status_active=1 and a.is_deleted =0");
					foreach ($from_order_sql as $val) 
					{
						$from_order_arr[$val[csf("to_order_id")]][] = $val[csf("from_order_id")];
					}	


				/*$yarn_lot_data = sql_select("select  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot as yarn_lot, a.yarn_count, a.stitch_length as stitch_length,a.machine_no_id as machine_no_id
				from pro_grey_prod_entry_dtls a, order_wise_pro_details b
				where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.prod_id in ($batch_product_id)");*/

				$yarn_lot_data = sql_select("SELECT  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot as yarn_lot, a.yarn_count, a.stitch_length as stitch_length,a.machine_no_id as machine_no_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.prod_id in ($batch_product_id) group by  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot, a.yarn_count, a.stitch_length,a.machine_no_id union all select  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot as yarn_lot, a.yarn_count, a.stitch_length as stitch_length,a.machine_no_id as machine_no_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b, inv_item_transfer_mst c where a.id=b.dtls_id and b.po_breakdown_id = c.from_order_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.prod_id in ($batch_product_id) group by  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot, a.yarn_count, a.stitch_length,a.machine_no_id");
				foreach ($yarn_lot_data as $rows) 
				{
					$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['lot'] .= $rows[csf('yarn_lot')] . ",";
					$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['stitch_length'] .= $rows[csf('stitch_length')] . ",";
					$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['brand_id'] .= $rows[csf('brand_id')] . ",";
					$yarn_count[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['yarn_count'] .= $rows[csf('yarn_count')] . ",";
					$machine_no_id[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['machine_no_id'] .= $rows[csf('machine_no_id')] . ",";
				}
			}
			$sql_dtls_knit = "select a.id as batch_id,a.booking_no_id,e.receive_basis,e.booking_id,a.booking_without_order,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg,e.knitting_source, e.knitting_company
			from pro_batch_create_mst a,pro_batch_create_dtls b, pro_grey_prod_entry_dtls d,  inv_receive_master e
			where a.id=b.mst_id and a.booking_no_id = e.booking_id and d.mst_id = e.id and a.company_id=$data[0] and a.id=$batch_update_id  and e.booking_id=$batch_booking_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)";
			$result = sql_select($sql_dtls_knit);
			$machine_dia_guage_arr = array();
			foreach ($result as $row) {
				$machine_dia_guage_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['basis'] = $row[csf('receive_basis')];
				$machine_dia_guage_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
				$machine_dia_guage_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
			}

			$sql_dtls = "SELECT b.id, a.batch_no, a.total_trims_weight, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.booking_without_order, a.process_id, a.extention_no, b.batch_qnty AS batch_qnty, b.roll_no, b.item_description, b.program_no, b.po_id, b.prod_id, b.body_part_id, b.width_dia_type,b.barcode_no, b.batch_qty_pcs from pro_batch_create_mst a,pro_batch_create_dtls b where a.company_id=$company_name and a.id=b.mst_id and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";
			//echo $sql_dtls;
			$sql_result = sql_select($sql_dtls);
			foreach ($sql_result as $ro)
			{
				$program_nos[$ro[csf("program_no")]] = $ro[csf("program_no")];
			}

			$program_ids = implode(",",array_filter($program_nos));
			$program_sql = sql_select("select id,width_dia_type, machine_dia,machine_gg,machine_id,id from ppl_planning_info_entry_dtls where id in ($program_ids)");
			foreach ($program_sql as $val)
			{
				$program_data[$val[csf("id")]]["machine_dia"] =$val[csf("machine_dia")];
				$program_data[$val[csf("id")]]["machine_gg"] =$val[csf("machine_gg")];
			}

			foreach ($sql_result as $row) {

				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				//echo $row[csf('prod_id')].'='.$row[csf('po_id')];
				$desc = explode(",", $row[csf('item_description')]);
				if ($row[csf('booking_without_order')] == 0) {
					$recv_basis = $machine_dia_guage_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['basis'];
				}

				if ($batch_against_id == 3 && $row[csf('booking_without_order')] == 1) {
					$yarn_lot = $sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['samplelot'];
					$y_count = $sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['yarncount'];
					$stitch = $sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['stitch_length'];
					$yarn_count_value = $yarncount[$y_count];
				} 
				else 
				{
					$y_count = chop($yarn_count[$row[csf('prod_id')]][$row[csf('po_id')]]['yarn_count'], ",");
					$y_count_id = array_unique(explode(',', $y_count));
					$yarn_count_value = '';
					foreach ($y_count_id as $val) 
					{
						if ($val > 0) {
							if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
						}
					}

					/*$stitch = implode(", ", array_unique(explode(",", chop($yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['stitch_length'], ","))));
					$yarn_lot = implode(", ", array_unique(explode(",", chop($yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['lot'], ","))));
					$yarn_brand = array_unique(explode(",", chop($yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['brand_id'], ",")));*/

					$stitch = $yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['stitch_length'];
					$yarn_lot = $yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['lot'];
					$yarn_brand = $yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['brand_id'];

					if(!empty($from_order_arr))
					{
						foreach ($from_order_arr as $val) 
						{
							$from_po =  array_unique($val);

							$stitch .= $yarn_lot_arr[$from_po][$row[csf('po_id')]]['stitch_length'].",";
							$yarn_lot .= $yarn_lot_arr[$from_po][$row[csf('po_id')]]['lot'].",";
							$yarn_brand .= $yarn_lot_arr[$from_po][$row[csf('po_id')]]['brand_id'].",";
						}
					}

					$stitch = implode(", ", array_unique(explode(",", chop($stitch, ","))));
					$yarn_lot = implode(", ", array_unique(explode(",", chop($yarn_lot, ","))));
					$yarn_brand = array_unique(explode(",", chop($yarn_brand, ",")));


				}

				$brand_suplier = "";
				foreach ($yarn_brand as $brand_id) {
					if ($brand_suplier == "") $brand_suplier = $supplier_brand[$brand_id]; else $brand_suplier .= "," . $supplier_brand[$brand_id];
				}

				$machine_dia_width = $machine_dia_guage_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['dia'];
				$machine_gauge = $machine_dia_guage_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['gg'];
				if($machine_dia_width =="")
				{
					$machine_dia_width =$machine_dia_width = $program_data[$row[csf('program_no')]]["machine_dia"];
				}
				if($machine_gauge =="")
				{
					$machine_gauge =$program_data[$row[csf('program_no')]]["machine_gg"];
				}
				$dya_gage = $machine_dia_width . '<br>' . $machine_gauge;

				$grey_qty = $row[csf('batch_qnty')];
				$finish_qty=$fin_qty_array[$row[csf('barcode_no')]];
				$actual_process_loss = ($finish_qty*100)/$grey_qty;
				if($actual_process_loss) $actual_process_loss = 100-$actual_process_loss;
				$po_process_loss = $po_process_loss_array[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('body_part_id')]];
				$status = $actual_process_loss - $po_process_loss;
				?>
					<tr style="font-size:21px" bgcolor="<? echo $bgcolor; ?>">
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="60" align="center" style="word-break:break-all;"><? echo $row[csf('program_no')]; ?></td>						
						<td width="100" style="word-break:break-all;" align="center"><? echo $row[csf('barcode_no')]; ?></td>
						<td width="100"	style="word-break:break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
						<td width="150" style="word-break:break-all;"><? echo $desc[0] . "," . $desc[1]; ?></td>
						<td width="50" align="center" style="word-break:break-all;"><? echo $desc[2]; ?></td>
						<td width="50" align="center" style="word-break:break-all;"><? echo $desc[3]; ?></td>
						<td width="70" align="center" style="word-break:break-all;"><? echo $dya_gage; ?></td>
						<td width="70" style="word-break:break-all;"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
						<td width="60" align="center" style="word-break:break-all;"><? echo $stitch; ?></td>
						<td width="80" align="center" style="word-break:break-all;"><? echo implode(',', array_unique(explode(",", $yarn_lot))); ?></td>
						<td align="center" width="100" style="word-break:break-all;"><? echo rtrim($brand_suplier, ","); ?></td>
						<td width="100" align="center" style="word-break:break-all;"><?  echo $yarn_count_value; ?></td>
						<td width="50" align="center"><? echo $fab_grade_array[$row[csf('barcode_no')]][283]['fabric_grade'];?></td>
						<td width="50" style="word-break:break-all;" align="right"><? echo number_format($grey_qty, 2); ?></td>
						<td width="50" align="center"><? echo $fab_grade_array[$row[csf('barcode_no')]][267]['fabric_grade'];?></td>
						<td width="50" align="right"><? echo number_format($finish_qty,2);?></td>
						<td width="50" align="right"><? echo number_format($row[csf('batch_qty_pcs')],2);?></td>
						<td width="50" align="right"><? echo number_format($fab_grade_array[$row[csf('barcode_no')]][267]['roll_length'],2);?></td>
						<td width="50" align="center"><? echo number_format($actual_process_loss,2); ?></td>
						<td width="50" align="center"><? echo $po_process_loss; ?></td>
						<td width="60" align="center"><? echo ($status > 0) ? number_format($status,2)."%" : "";?></td>
					</tr>
					<?php
					$total_roll_number += $row[csf('num_of_rows')];
					$total_batch_qty += $row[csf('batch_qnty')];
					$total_finish_qty += $finish_qty;
					$total_batch_qty_pcs += $row[csf('batch_qty_pcs')];
					$i++;
				}
				$all_barcode = implode(", ", array_unique(explode(",", chop($all_barcode, ","))));
				$total_actual_process_loss = ($total_finish_qty*100)/$total_batch_qty;
				if($total_actual_process_loss) $total_actual_process_loss = 100-$total_actual_process_loss;
				?>
				<tfoot>
					<tr>
						<th colspan="14">Total</th>
						<th align="right"><?  echo number_format($total_batch_qty,2);?></th>
						<th></th>
						<th align="right"><?  echo number_format($total_finish_qty,2);?></th>
						<th align="right"><?  echo number_format($total_batch_qty_pcs,2);?></th>
						<th></th>
						<th><?  echo number_format($total_actual_process_loss,2);?></th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
		</div>	
		<?
	}
	else // button 2
	{
	
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$supplier_library = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
		$machine_library = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
		//$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
		$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');

		$job_array = array();
		$job_sql = "SELECT distinct(a.buyer_name) as buyer_name,a.style_ref_no, a.job_no_prefix_num, a.job_no, b.pub_shipment_date, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$job_sql_result = sql_select($job_sql);
		foreach ($job_sql_result as $row) 
		{
			$job_array[$row[csf('id')]]['job'] 		= $row[csf('job_no')];
			$job_array[$row[csf('id')]]['po'] 		= $row[csf('po_number')];
			$job_array[$row[csf('id')]]['buyer'] 	= $row[csf('buyer_name')];
			$job_array[$row[csf('id')]]['ship_date']= $row[csf('pub_shipment_date')];
			$job_array[$row[csf('id')]]['ref'] 		= $row[csf('grouping')];
			$job_array[$row[csf('id')]]['file_no'] 	= $row[csf('file_no')];
			$job_array[$row[csf('id')]]['style'] 	= $row[csf('style_ref_no')];
		}

		if ($db_type == 0) 
		{
			$sql = "SELECT a.id, a.batch_no,a.batch_date, a.booking_no_id, a.booking_no,a.booking_without_order,a.sales_order_no,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, group_concat(b.barcode_no) as barcode_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			group by a.id, a.batch_no,a.batch_date, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id, a.sales_order_no,a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty, a.cuff_qty, a.SAVE_STRING";
		} 
		else 
		{
			$sql = "SELECT a.id, a.batch_no,a.batch_date, a.booking_no_id,a.booking_no,a.booking_without_order,a.sales_order_no, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id,LISTAGG(CAST(b.barcode_no AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.barcode_no) AS barcode_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			group by a.id, a.batch_no,a.batch_date, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for,a.sales_order_no, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty,a.cuff_qty, a.SAVE_STRING";
		}
		//echo $sql;
		$dataArray = sql_select($sql);
		$batch_color_id=$dataArray[0][csf('color_id')];
		$color_sql="select id, color_name from lib_color where id in($batch_color_id)";
		$color_data_arr=sql_select($color_sql);
		$color_arr=array();
		foreach ($color_data_arr as $key => $value) 
		{
			$color_arr[$value[csf('id')]]=$value[csf('color_name')];
		}

		$po_number 		= "";
		$job_number 	= "";
		$job_style 		= "";
		$buyer_id 		= "";
		$ship_date 		= "";
		$internal_ref 	= "";
		$file_nos 		= "";
		$po_id 			= array_unique(explode(",", $dataArray[0][csf('po_id')]));
		$barcode_no 	= implode(",",array_unique(explode(",", $dataArray[0][csf('barcode_no')])));
		$booking_no 	= $dataArray[0][csf('booking_no')];
		$batch_against_id = $dataArray[0][csf('batch_against')];
		$batch_booking_id = $dataArray[0][csf('booking_no_id')];
		$batch_product_id = $dataArray[0][csf('prod_id')];
		$sales_order_no   = $dataArray[0][csf('sales_order_no')];
		$batch_booking_without = $dataArray[0][csf('booking_without_order')];
		foreach ($po_id as $val) 
		{
			if ($po_number 	== "") $po_number = $job_array[$val]['po']; else $po_number 	.= ',' . $job_array[$val]['po'];
			if ($job_number == "") $job_number = $job_array[$val]['job']; else $job_number 	.= ',' . $job_array[$val]['job'];
			if ($job_style 	== "") $job_style = $job_array[$val]['style']; else $job_style 	.= ',' . $job_array[$val]['style'];
			if ($buyer_id 	== "") $buyer_id = $job_array[$val]['buyer']; else $buyer_id 	.= ',' . $job_array[$val]['buyer'];
			if ($ship_date 	== "") $ship_date = change_date_format($job_array[$val]['ship_date']); else $ship_date .= ',' . change_date_format($job_array[$val]['ship_date']);

			if ($internal_ref == "") $internal_ref = $job_array[$val]['ref']; else $internal_ref .= ',' . $job_array[$val]['ref'];
			if ($job_array[$val]['file_no'] > 0) 
			{
				if ($file_nos == "") $file_nos = $job_array[$val]['file_no']; else $file_nos .= ',' . $job_array[$val]['file_no'];
			}
		}

		$job_no 		= implode(",", array_unique(explode(",", $job_number)));
		$jobstyle 		= implode(",", array_unique(explode(",", $job_style)));
		$buyer 			= implode(",", array_unique(explode(",", $buyer_id)));
		$internal_ref 	= implode(",", array_unique(array_filter(explode(",", $internal_ref))));
		$file_nos 		= implode(",", array_unique(explode(",", $file_nos)));

		if ($dataArray[0][csf('booking_without_order')] == 1) 
		{
			$booking_without_order = sql_select("select a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id,b.fabric_color from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$company_name and a.booking_no='$booking_no' and a.booking_type=4");
			foreach ($booking_without_order as $row) 
			{
				$color_type_array2[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('fabric_color')]]['color_type_id'] = $row[csf('color_type_id')];
				$booking_id = $row[csf('booking_no_prefix_num')];
				$buyer_id_booking = $row[csf('buyer_id')];
			}
		} 
		else 
		{
			$booking_with_order = sql_select("select booking_no_prefix_num, buyer_id from wo_booking_mst where company_id=$company_name and booking_no='$booking_no' and booking_type=4");
			$booking_id = $booking_with_order[0][csf('booking_no_prefix_num')];
			$buyer_id_booking = $booking_with_order[0][csf('buyer_id')];

			$color_sql = "SELECT  b.booking_no,c.color_type_id,c.body_part_id,c.lib_yarn_count_deter_id,b.fabric_color_id as gmts_color_id, c.gsm_weight from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company_name and a.booking_no='$booking_no' and a.booking_type=1 and b.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.booking_no,c.color_type_id,b.fabric_color_id,c.body_part_id,c.lib_yarn_count_deter_id, c.gsm_weight";
			$color_sql_result = sql_select($color_sql);
			foreach ($color_sql_result as $row) 
			{
				$color_type_array[$row[csf('booking_no')]][$row[csf('body_part_id')]]['color_type_id'] = $row[csf('color_type_id')];
				$color_type_array_precost[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('gsm_weight')]]['color_type_id'] = $row[csf('color_type_id')];
			}
		}

		$sales_sql = "SELECT b.job_no_mst as booking_no,b.color_type_id,b.body_part_id from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company_name and a.job_no='$sales_order_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.job_no_mst ,b.color_type_id,b.body_part_id";
		$sales_result = sql_select($sales_sql);
		foreach ($sales_result as $row) 
		{
			$sales_color_type_array[$row[csf('booking_no')]][$row[csf('body_part_id')]]['color_type_id'] = $row[csf('color_type_id')];
		}

		//============================== po process loss ==================================
		$sql_pro_loss = "SELECT a.body_part_id,b.po_break_down_id as po_id,b.color_number_id as color_id,b.process_loss_percent from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.status_active=1 and b.status_active=1 and a.id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no'";
		$sql_pro_loss_res = sql_select($sql_pro_loss);
		$po_process_loss_array = array();
		foreach ($sql_pro_loss_res as $val) 
		{
			$po_process_loss_array[$val[csf('po_id')]][$val[csf('color_id')]][$val[csf('body_part_id')]] = $val[csf('process_loss_percent')];
		}
		ob_start();

		?>
		<div style="width:1700px;">
			<table width="1700" cellspacing="0" align="center" border="0">
				<tr>
				   <td align="center" width="100%"  class="form_caption"><? echo $report_title; ?></td>
				</tr>
				<tr>
					<td width="100%" align="center" style="font-size:22px">
						<strong><? echo $company_library[$company_name]; ?></strong>
					</td>
				</tr>
				<tr>
					<td width="100%" style="font-size:14px" align="center">Print Time:<? echo $date = date("F j, Y, g:i a"); ?></td>
				</tr>
				</table>
				<table width="1700" cellspacing="0" align="center" border="0">		
				<tr>
					<td width="810" colspan="11" align="left" style="font-size:16px">
						<strong><u>Batch Information</u></strong>
					</td>
					<td  width="810" colspan="11" align="left" style="font-size:16px">
						<strong><u>Order Information</u></strong>
					</td>
				</tr>
			</table>
			<br>
			<table width="1700" cellspacing="0" align="center" border="0">
				<tr style="font-size:21px">
					<td width="150"><strong>Batch No</strong></td>
					<td width="255">:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
					<td width="150"><strong>Batch Date</strong></td>
					<td width="255">:&nbsp;<? echo change_date_format($dataArray[0][csf('batch_date')]); ?></td>

					<?
					if ($dataArray[0][csf('batch_against')] == 3) {
						?>
					<td width="150"><strong>Booking no</strong></td>
					<td width="255">:&nbsp;<? echo $booking_id; ?></td>

					<? } else { ?>
					<td width="150"><strong>Job</strong></td>
					<td width="255">:&nbsp;<? echo $job_no; ?></td>
					<? }
					?>
					<td width="150"><strong>Order No</strong></td>
					<td width="255">:&nbsp;<? echo $po_number; ?></td>
				</tr>
				<tr style="font-size:21px">
					<td><strong>B. Color</strong></td>
					<td>:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
					<td><strong>Color Ran.</strong></td>
					<td>:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range_id')]]; ?></td>

					<td><strong>Buyer</strong></td>
					<td>
					:&nbsp;<? if ($dataArray[0][csf('batch_against')] == 3) echo $buyer_arr[$buyer_id_booking]; else echo $buyer_arr[$buyer]; ?></td>				
					<td><strong>Style Ref.</strong></td>
					<td>:&nbsp;<? echo $jobstyle; ?></td>
				</tr>
				<tr style="font-size:21px">
					<td><strong>Batch Against</strong></td>
					<td>:&nbsp;<? echo $batch_against[$dataArray[0][csf('batch_against')]]; ?></td>
					<td width="120"><strong>Batch SL</strong></td>
					<td width="135">:&nbsp;<? echo $batch_sl_no; ?></td>

					<td><strong>Int. Ref.</strong></td>
					<td>:&nbsp;<? echo $internal_ref; ?></td>
					<td><strong>File No</strong></td>
					<td>:&nbsp;<? echo $file_nos; ?></td>
				</tr>
				<tr style="font-size:21px">
					<td><strong>Batch For</strong></td>
					<td>:&nbsp;<? echo $batch_for[$dataArray[0][csf('batch_for')]]; ?></td>
					<td><strong>B. Weight</strong></td>
					<td>:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?></td>

					<td><strong>Ship Date</strong></td>
					<td>
						:&nbsp;<? if (trim($ship_date) != "0000-00-00" && trim($ship_date) != "") echo implode(",", array_unique(explode(",", $ship_date))); else echo "&nbsp;"; ?>
							
					</td>
					<td><strong>Remarks </strong></td>
					<td>:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
				</tr>

			</table>
			<div style="float:left; font-size:18px;"><strong><u>Fabrication Details</u></strong></div>			
				<?
				$i = 1;
				$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
				$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');
				$supplier_array_lib = return_library_array("select id,short_name from  lib_supplier", "id", "short_name");
				$machine_array_lib_dia = return_library_array("select id,dia_width from  lib_machine_name", "id", "dia_width");
				$machine_array_lib_gauge = return_library_array("select id,gauge from  lib_machine_name", "id", "gauge");
				$supplier_from_prod = return_library_array("select lot,supplier_id from  product_details_master where item_category_id=1 ", "lot", "supplier_id");

				$machine_lib_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
				foreach ($machine_lib_sql as $row) {
					$dya_gauge_arr[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
					$dya_gauge_arr[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
					$dya_gauge_arr[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
				}			

				$receive_basis = sql_select("select a.receive_basis from inv_receive_master a,pro_roll_details b where a.id=b.mst_id  and (a.booking_id='" . $booking_no_id . "' OR b.barcode_no in($barcode_no)) and a.entry_form in(2,22) and b.entry_form in(2,22) group by a.receive_basis");

				foreach ($receive_basis as $val)
				{
					$receive_basis_arr[$val[csf("receive_basis")]];
				}

				$receivebasis = array_filter($receive_basis_arr);

				foreach ($receivebasis as $rcvid) {
					if ($rcvid == 0 || $rcvid == 1 || $rcvid == 11) {
						$machine_info = "d.machine_dia,d.machine_gg,";
					} else {
						$machine_info = "";
					}
				}

				if ($db_type == 0) 
				{
					$sql_dtls = "SELECT a.booking_no_id,a.color_id,e.receive_basis,e.booking_id, a.booking_without_order,a.is_sales, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no,d.febric_description_id,d.gsm, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows,$machine_info d.machine_no_id, group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count) as yarn_count, d.stitch_length as stitch_length, group_concat(d.brand_id) as brand_id, e.knitting_source, e.knitting_company, group_concat(c.barcode_no) as barcode_no,b.po_id
					from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
					where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$company_name and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
					group by a.booking_no_id,a.color_id,e.booking_id,$machine_info a.booking_without_order,a.is_sales,e.receive_basis,b.prod_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, d.stitch_length,d.febric_description_id,d.gsm, e.knitting_source, e.knitting_company,b.po_id order by b.program_no";

				} 
				else 
				{
					$sql_dtls = "SELECT a.booking_no_id,a.color_id,e.receive_basis,e.booking_id,a.booking_without_order,a.is_sales, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no,d.febric_description_id,d.gsm, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows, d.machine_no_id,$machine_info LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count, d.stitch_length as stitch_length, LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id, e.knitting_source, e.knitting_company, LISTAGG(CAST(c.barcode_no AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY c.barcode_no) as barcode_no,b.po_id
					from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
					where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$company_name and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
					group by a.booking_no_id,a.color_id,e.receive_basis,$machine_info e.booking_id,a.booking_without_order,a.is_sales,b.prod_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, d.stitch_length,d.febric_description_id,d.gsm, e.knitting_source, e.knitting_company,b.po_id order by b.program_no";
				}
				?>

					<table align="center" cellspacing="0" style="font-size:21px" width="1700" border="1" rules="all" class="rpt_table" id="rpt_table">
						<thead bgcolor="#dddddd" align="center">
							<tr>
								<th width="30">SL</th>
								<th width="60">Prog. No</th>
								<th width="50">Total Roll</th>
								<th width="80">Machine / Knitting Com</th>
								<th width="80">Body part</th>
								<th width="80">Color Type</th>
								<th width="150">Const. & Comp.</th>
								<th width="50">Fin. GSM</th>
								<th width="50">Fin. Dia</th>
								<th width="70">M/Dia X Gauge</th>
								<th width="70">D/W Type</th>
								<th width="60">S. Length</th>
								<th width="80">Yarn Lot</th>
								<th width="80">Brand</th>
								<th width="80">Yarn Count</th>
								<th width="80">Grey Fab. Grade</th>
								<th width="70">Grey Qty.</th>
								<th width="80">Fin. Fab. Grade</th>
								<th width="80">Fin. Qty.</th>
								<th width="80">Length</th>
								<th width="80">Actual Process Loss %</th>
								<th width="80">PO Process Loss%</th>
								<th width="80">Status</th>
							</tr>
						</thead>
						<tbody>
				<?
				// echo $sql_dtls;die;
				$sql_result = sql_select($sql_dtls);
				$all_barcode = "";
				$total_grey_qty = 0;
				$total_fin_qty  = 0;
				foreach ($sql_result as $row) 
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$desc = explode(",", $row[csf('item_description')]);
					$all_barcode .= $row[csf("barcode_no")] . ",";

					$yarn_lot = implode(",", array_unique(explode(",", $row[csf('yarn_lot')])));
					$y_count = array_unique(explode(",", $row[csf('yarn_count')]));
					$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
					$yarn_count_value = "";
					foreach ($y_count as $val) 
					{
						if ($val > 0) 
						{
							if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
						}
					}
					$brand_value = "";
					foreach ($brand_id as $bid) 
					{
						if ($bid > 0) 
						{
							if ($brand_value == '') $brand_value = $brand_name_arr[$bid]; else $brand_value .= ", " . $brand_name_arr[$bid];
						}
					}
					if ($row[csf('receive_basis')] == 0 || $row[csf('receive_basis')] == 1 || $row[csf('receive_basis')] == 11) //from Entry page
					{
						$machine_dia_width = $row[csf('machine_dia')];
						$machine_gauge = $row[csf('machine_gg')];
					} 
					else if ($row[csf('receive_basis')] == 2) //Knitting Plan
					{
						$program_data = sql_select("select width_dia_type, machine_dia, machine_gg, machine_id from ppl_planning_info_entry_dtls where id='" . $row[csf('booking_id')] . "'");

						$machine_dia_width = $program_data[0][csf('machine_dia')];
						$machine_gauge = $program_data[0][csf('machine_gg')];
					}

					$stitch = implode(",", array_unique(explode(",", $row[csf('stitch_length')])));
					$dya_gage = $machine_dia_width . "<br>" . $machine_gauge;
					$is_sales=$row[csf('is_sales')];
					if($is_sales==1) //Sales
					{
						$color_type_id=$sales_color_type_array[$sales_order_no][$row[csf('body_part_id')]]['color_type_id'];
					}
					else
					{
						if($row[csf('booking_without_order')]==1)
						{
							$color_type_id=$color_type_array[$booking_no][$row[csf('body_part_id')]]['color_type_id'];
						}
						else
						{
							$color_id=$dataArray[0][csf('color_id')];
							$color_type_id=$color_type_array_precost[$booking_no][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$color_id][$row[csf('gsm')]]['color_type_id'];
						}
					}
					$grey_qty = $row[csf('batch_qnty')];
					$barcode_no = $row[csf('barcode_no')];
					$finish_qty = 0;
					$finish_grade = "";
					$grey_grade = "";
					$roll_length = 0;
					$barcode_arr = explode(",", $barcode_no);
					foreach ($barcode_arr as $val) 
					{
						$finish_qty+=$fin_qty_array[$val];
						if($fab_grade_array[$val][283]['fabric_grade'] !="")
						{
							$grey_grade .= ($grey_grade == "") ? $fab_grade_array[$val][283]['fabric_grade'] : ", ".$fab_grade_array[$val][283]['fabric_grade'];
						}

						if($fab_grade_array[$val][267]['fabric_grade'] !="")
						{
							$finish_grade .= ($finish_grade == "") ? $fab_grade_array[$val][267]['fabric_grade'] : ", ".$fab_grade_array[$val][267]['fabric_grade'];
						}
						$roll_length += $fab_grade_array[$val][267]['roll_length'];
					}
					// $finish_qty=$fin_qty_array[$row[csf('barcode_no')]];
					$actual_process_loss = ($finish_qty*100)/$grey_qty;
					if($actual_process_loss) $actual_process_loss=100-$actual_process_loss;
					$po_process_loss = $po_process_loss_array[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('body_part_id')]];
					$status = $actual_process_loss - $po_process_loss;
					?>
							<tr style="font-size:21px" bgcolor="<? echo $bgcolor; ?>">
								<td width="30" align="center"><? echo $i; ?></td>
								<td width="60" align="center" style="word-break:break-all;"><? echo $row[csf('program_no')]; ?></td>
									<?
									if ($row[csf('knitting_source')] == 1) {
										$machin_knit_com = $machine_library[$row[csf('machine_no_id')]];
									} else {
										$machin_knit_com = $supplier_library[$row[csf('knitting_company')]];
									}
									?>
								<td align="center" width="50" style="word-break:break-all;"><? echo $row[csf('num_of_rows')]; ?></td>
								<td width="80" style="word-break:break-all;" align="center"><? echo $machin_knit_com; ?></td>
								<td width="80" style="word-break:break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
								<td width="80" style="word-break:break-all;"><? echo $color_type[$color_type_id]; ?></td>
								<td width="150" style="word-break:break-all;"><? echo $desc[0] . "," . $desc[1]; ?></td>
								<td width="50" align="center" style="word-break:break-all;"><? echo $desc[2]; ?></td>
								<td width="50" align="center" style="word-break:break-all;"><? echo $desc[3]; ?></td>
								<td width="70" align="center" style="word-break:break-all;"><? echo $dya_gage; ?></td>
								<td width="70" style="word-break:break-all;"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
								<td width="60" align="center" style="word-break:break-all;"><? echo $stitch; ?></td>
								<td width="80" style="word-break:break-all;"><? echo implode(',', array_unique(explode(",", $yarn_lot))); ?></td>
								<td width="80" style="word-break:break-all;"><? echo $brand_value;?></td>
								<td width="80" style="word-break:break-all;" align="center"><? echo $yarn_count_value; ?></td>
								<td width="70" align="center"><? echo $grey_grade;?></td>
								<td width="80" style="word-break:break-all;" align="right"><? echo number_format($grey_qty, 2); ?></td>
								<td width="80" align="center"><? echo $finish_grade;?></td>
								<td width="80" align="right"><? echo number_format($finish_qty,2);?></td>
								<td width="80" align="right"><? echo $roll_length;?></td>
								<td width="80" align="center" title="<? echo "100-(Finish qty*100/Grey qty)";  ?>"><? echo number_format($actual_process_loss,2); ?></td>
								<td width="80" align="center"><? echo $po_process_loss; ?></td>
								<td width="80" align="center"><? echo ($status > 0) ? number_format($status,2)."%" : "";?></td>
							</tr>
						<?
						$total_grey_qty += $grey_qty;
						$total_fin_qty  += $finish_qty;
					}
					$total_actual_process_loss = ($total_fin_qty*100)/$total_grey_qty;
					if($total_actual_process_loss) $total_actual_process_loss=100-$total_actual_process_loss;
					?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="16" align="right">Total </th>
							<th align="right"><? echo number_format($total_grey_qty,2); ?></th>
							<th></th>
							<th align="right"><? echo number_format($total_fin_qty,2); ?></th>
							<th></th>
							<th align="right"><? echo number_format($total_actual_process_loss,2); ?></th>
							<th></th>
							<th></th>
						</tr>
					</tfoot>
				</table>		
			</div>
		<?
	}				
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	
	disconnect($con);
	exit();

}
?>

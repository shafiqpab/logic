<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$subprocessForWashIn=implode(",",$subprocessForWashArr);


if ($action == "load_drop_machine")
{
	if($db_type==2)
	{
		echo create_drop_down( "cbo_machine_name", 144, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where category_id=2 and company_id=$data and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no","id,machine_name", 1,"-- Select Machine --", $selected, "","" );

	}
	else if($db_type==0)
	{
		echo create_drop_down( "cbo_machine_name", 144, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=2 and company_id=$data and status_active=1 and is_deleted=0 and is_locked=0 order by  seq_no","id,machine_name", 1, "-- Select Machine --", $selected, "","" );
	}
}

if ($action == "load_drop_down_location") {
	$data = explode("_", $data);
	echo create_drop_down("cbo_location", 152, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "");
	exit();
}

if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,2,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 1);
	exit();
}

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/chemical_dyes_receive_controller",$data);
}
if ($action == "incharge_name_popup") {
	echo load_html_head_contents("In Charge Name Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(id) {
			$('#incharge_hdn').val(id);
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" name="incharge_hdn" id="incharge_hdn" value=""/>
	<?
		if($cbo_company_id>0) $com_cond=" and company_id=$cbo_company_id";else  $com_cond="";
	 $sql = "select id, first_name from lib_employee where  in_charge like '%5%' and status_active=1 and is_deleted=0 $com_cond order by first_name";

	echo create_list_view("tbl_list_search", "In Charge Name", "320", "280", "150", 0, $sql, "js_set_value", "id,first_name", "", 1, "0", $arr, "first_name", "", 'setFilterGrid("tbl_list_search",-1);', '0', '', 0);
	exit();
}


if ($action == "systemid_popup") {
	echo load_html_head_contents("Labdip No Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id) {
			$('#hidden_update_id').val(id);
			parent.emailwindow.hide();
		}
	</script>
</head>

<body>
	<div align="center" style="width:100%;">
		<form name="searchlabdipfrm" id="searchlabdipfrm">
			<fieldset style="width:1085px;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="1100" class="rpt_table">
					<thead>
						<tr>
							<th colspan="8"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
						</tr>
						<tr>
							<th>Recipe Date Range</th>
							<th>System ID</th>
							<th width="130">Labdip No</th>
							<th width="100">Batch No</th>
							<th width="100">IR/IB</th>
							<th width="100">Booking No</th>
							<th width="150">Recipe Description</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:80px;"
								class="formbutton"/>
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
								<input type="hidden" name="working_company_id" id="working_company_id" class="text_boxes" value="<? echo $cbo_working_company_id; ?>">
								<input type="hidden" name="hidden_update_id" id="hidden_update_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_pickup" id="hidden_pickup" class="text_boxes" value="">
								<input type="hidden" name="hidden_surplus_solution" id="hidden_surplus_solution" class="text_boxes" value="">
							</th>
						</tr>
					</thead>
					<tr class="general">
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:60px;">To<input type="text" name="txt_date_to" id="txt_date_to"
							class="datepicker" style="width:60px;">
						</td>
						<td>
							<input type="text" style="width:130px;" class="text_boxes" name="txt_search_sysId"
							id="txt_search_sysId" placeholder="Search"/>
						</td>
						<td>
							<input type="text" style="width:130px;" class="text_boxes" name="txt_search_labdip"
							id="txt_search_labdip" placeholder="Search"/>
						</td>
						<td>
							<input type="text" style="width:100px;" class="text_boxes" name="txt_search_batch" id="txt_search_batch" placeholder="Search"/>
						</td>
						<td>
							<input type="text" style="width:100px;" class="text_boxes" name="txt_search_intref" id="txt_search_intref" placeholder="Search"/>
						</td>
						<td>
							<input type="text" style="width:100px;" class="text_boxes" name="txt_search_booking" id="txt_search_booking" placeholder="Search"/>
						</td>
						<td>
							<input type="text" style="width:130px;" class="text_boxes" name="txt_search_recDes"
							id="txt_search_recDes" placeholder="Search"/>
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_labdip').value+'_'+document.getElementById('txt_search_sysId').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_search_recDes').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_search_batch').value+'_'+document.getElementById('working_company_id').value+'_'+document.getElementById('txt_search_intref').value+'_'+document.getElementById('txt_search_booking').value, 'create_recipe_search_list_view', 'search_div', 'recipe_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:80px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="7" align="center" height="40"
						valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action == "create_recipe_search_list_view") {
	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	//	$job_buyer=return_library_array( "select subcon_job, party_id from subcon_ord_mst", "subcon_job", "party_id");
	$data = explode("_", $data);
	$labdip = $data[0];
	$sysid = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];
	$rec_des = trim($data[5]);
	$search_type = $data[6];
	$batch_no = $data[7];
	$working_company_id = $data[8];
	$internal_ref = $data[9];
	$booking_no = $data[10];

	if($start_date=='' && $end_date=='' && $batch_no=='' && $sysid==''  && $labdip=='' && $booking_no=='' && $internal_ref=='')
	{
		echo "<b style='font-size:20px;color:red;'>Please select any one from search panel.</b>";die;
	}


	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and recipe_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-", 1) . "'";
		} else if ($db_type == 2) {
			$date_cond = "and recipe_date between '" . change_date_format(trim($start_date), "mm-dd-yyyy", "/", 1) . "' and '" . change_date_format(trim($end_date), "mm-dd-yyyy", "/", 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	if ($search_type == 1) {
		if ($labdip != '') $labdip_cond = " and labdip_no='$labdip'"; else $labdip_cond = "";
		if ($sysid != '') $sysid_cond = " and id=$sysid"; else $sysid_cond = "";
		if ($rec_des != '') $rec_des_cond = " and recipe_description='$rec_des'"; else $rec_des_cond = "";
		if ($booking_no != '') $booking_no_cond = " and style_or_order='$booking_no'"; else $booking_no_cond = "";
	} else if ($search_type == 4 || $search_type == 0) {
		if ($labdip != '') $labdip_cond = " and labdip_no like '%$labdip%'"; else $labdip_cond = "";
		if ($sysid != '') $sysid_cond = " and id like '%$sysid%' "; else $sysid_cond = "";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '%$rec_des%'"; else $rec_des_cond = "";
		if ($booking_no != '') $booking_no_cond = " and style_or_order like '%$booking_no%'"; else $booking_no_cond = "";
	} else if ($search_type == 2) {
		if ($labdip != '') $labdip_cond = " and labdip_no like '$labdip%'"; else $labdip_cond = "";
		if ($sysid != '') $sysid_cond = " and id like '$sysid%' "; else $sysid_cond = "";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '$rec_des%'"; else $rec_des_cond = "";
		if ($booking_no != '') $booking_no_cond = " and style_or_order like '$booking_no%'"; else $booking_no_cond = "";
	} else if ($search_type == 3) {
		if ($labdip != '') $labdip_cond = " and labdip_no like '%$labdip'"; else $labdip_cond = "";
		if ($sysid != '') $sysid_cond = " and id like '%$sysid' "; else $sysid_cond = "";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '%$rec_des'"; else $rec_des_cond = "";
		if ($booking_no != '') $booking_no_cond = " and style_or_order like '%$booking_no'"; else $booking_no_cond = "";
	}
	if ($batch_no != "") {
		//$batch_ids = return_field_value("id", "pro_batch_create_mst", "batch_no='$batch_no' and status_active=1 and is_deleted=0", "id");
		//$batch_cond = "and batch_id=$batch_ids";

		$sql_batch=sql_select(" select id from pro_batch_create_mst where (working_company_id=$working_company_id OR company_id=$company_id) and batch_no='$batch_no' and status_active=1 and is_deleted=0");
		foreach ($sql_batch as  $value) {
			$batch_ids_arr[$value[csf('id')]]=$value[csf('id')];
		}

		if(!empty($batch_ids_arr))
		{
			$batch_cond = "and batch_id in(".implode(",", $batch_ids_arr).")";

		}

	} else {
		$batch_cond = "";
	}
	$lc_working_company_cond="";
	if($working_company_id!=0 && $company_id!=0)
	{
		$lc_working_company_cond="and (working_company_id=$working_company_id OR company_id=$company_id)";

		if($company_id!=0) $po_company_cond="and a.company_name=$company_id";else $po_company_cond="";
		if($company_id!=0) $sub_company_cond="and a.company_id=$company_id";else $sub_company_cond="";

	}
	else
	{
		//echo $working_company_id."SS";
		if($working_company_id!=0) $lc_working_company_cond="and working_company_id=$working_company_id";
		if($company_id!=0) $lc_working_company_cond.="and company_id=$company_id";
		if($company_id!=0) $po_company_cond="and a.company_name=$company_id";else $po_company_cond="";
		if($company_id!=0) $sub_company_cond="and a.company_id=$company_id";else $sub_company_cond="";
	}

	if ($internal_ref!="")
	{
		/*$po_sql="SELECT a.grouping, b.booking_no from wo_po_break_down a, wo_booking_dtls b
		where a.id=b.po_break_down_id and a.grouping='$internal_ref' and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";*/

		$po_sql="SELECT a.id as batch_id, b.booking_no, c.grouping 
		from pro_batch_create_mst a, wo_booking_dtls b, wo_po_break_down c 
		where b.booking_no=a.booking_no and b.po_break_down_id=c.id  and c.grouping='$internal_ref' and b.booking_type in(1,4)
		and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by a.id, b.booking_no, c.grouping";
		// echo $po_sql;die;
		$po_sql_result=sql_select($po_sql);
		if (empty($po_sql_result)) 
		{
			echo "Data not found";die;
		}
		$batchIdCond="";
		foreach ($po_sql_result as $key => $row) 
		{
			$batchId_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
		}
		$batchIdCond=" and batch_id in(".implode(",",$batchId_arr).") ";
	}
	// echo $batchIdCond;die;

	$sql = "SELECT id, labdip_no,batch_id, recipe_description, recipe_date, order_source, style_or_order, buyer_id, color_id, color_range, pickup, surplus_solution, batch_qty from pro_recipe_entry_mst where entry_form=59 and status_active=1 and is_deleted=0 $lc_working_company_cond $labdip_cond $sysid_cond $rec_des_cond $date_cond $batch_cond $booking_no_cond $batchIdCond order by id DESC";
	// echo $sql;die;

	$sql_result=sql_select($sql);
	foreach($sql_result as $row)
	{
		$all_batch_id.=$row[csf("batch_id")].",";
		$batch_idArr[$row[csf("batch_id")]]=$row[csf("batch_id")];
	}
	//$batrch_idcond=where_con_using_array($batch_idArr,0,'id');
	 $con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=28");
	oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 28, 1, $batch_idArr, $empty_arr);//Batch ID Ref from=1
	disconnect($con);
	//$batch_no_arr = return_library_array("select id, batch_no from pro_batch_create_mst where  status_active=1 and is_deleted=0 $batrch_idcond", 'id', 'batch_no');
	//$sql_batch="select id, batch_no,extention_no from pro_batch_create_mst where  status_active=1 and is_deleted=0 $batrch_idcond";
	$sql_batch="select b.id, b.batch_no,b.extention_no from pro_batch_create_mst b,gbl_temp_engine g  where   b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=28 and b.status_active=1 and b.is_deleted=0 ";
	$sql_result_batch=sql_select($sql_batch);
	foreach($sql_result_batch as $row)
	{

		$batch_no_arr[$row[csf("id")]]=$row[csf("batch_no")];
		$batch_no_ex_no_arr[$row[csf("id")]]=$row[csf("extention_no")];
	}

	$all_batch_id=chop($all_batch_id,",");
	if($all_batch_id!="")
	{
		$baIds=chop($all_batch_id,','); $ba_cond_in="";
			$ba_ids=count(array_unique(explode(",",$all_batch_id)));
			if($db_type==2 && $ba_ids>1000)
			{
			$ba_cond_in=" and (";
			$baIdsArr=array_chunk(explode(",",$baIds),999);
			foreach($baIdsArr as $ids)
			{
			$ids=implode(",",$ids);
			$ba_cond_in.=" c.mst_id in($ids) or";
			}
			$ba_cond_in=chop($ba_cond_in,'or ');
			$ba_cond_in.=")";
			}
			else
			{
			$ba_cond_in=" and c.mst_id in($baIds)";
			}
			// echo "select distinct(a.buyer_name) as buyer_name, b.id,c.mst_id as batch_id,b.file_no,b.grouping from  wo_po_break_down b,wo_po_details_master a,pro_batch_create_dtls c where  a.job_no=b.job_no_mst and b.id=c.po_id and b.status_active=1 and b.is_deleted=0  $ba_cond_in $po_company_cond ";
		//$po_arr = sql_select("select distinct(a.buyer_name) as buyer_name, b.id,c.mst_id as batch_id,c.po_id,c.is_sales,b.file_no,b.grouping,b.po_number from  wo_po_break_down b,wo_po_details_master a,pro_batch_create_dtls c where  a.job_no=b.job_no_mst and b.id=c.po_id and b.status_active=1 and b.is_deleted=0  $ba_cond_in $po_company_cond ");
		$po_arr = sql_select("select distinct(a.buyer_name) as buyer_name, b.id,c.mst_id as batch_id,c.po_id,c.is_sales,b.file_no,b.grouping,b.po_number from  wo_po_break_down b,wo_po_details_master a,pro_batch_create_dtls c,gbl_temp_engine g  where  a.id=b.job_id and b.id=c.po_id and c.mst_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=28 and b.status_active=1 and b.is_deleted=0   $po_company_cond ");
		$po_ref_arr = array();
		$po_file_arr = array();
		$po_buyer_arr = array();
		$po_number_arr = array();
		foreach ($po_arr as $row) {

			if($row[csf('is_sales')]==0)
			{
				$po_file_arr[$row[csf('batch_id')]] = $row[csf('file_no')];
				$po_ref_arr[$row[csf('batch_id')]] = $row[csf('grouping')];
				$po_buyer_arr[$row[csf('batch_id')]] = $row[csf('buyer_name')];
				$po_number_arr[$row[csf('batch_id')]] = $row[csf('po_number')];
			}
			else
			{
				$sales_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
				$batch_wise_sales_id_arr[$row[csf('batch_id')]] = $row[csf('po_id')];
			}
		}
	}

	if($all_batch_id!="")
	{
		$baIds=chop($all_batch_id,','); $ba_cond_in1="";
			$ba_ids=count(array_unique(explode(",",$all_batch_id)));
			if($db_type==2 && $ba_ids>1000)
			{
			$ba_cond_in1=" and (";
			$baIdsArr=array_chunk(explode(",",$baIds),999);
			foreach($baIdsArr as $ids)
			{
			$ids=implode(",",$ids);
			$ba_cond_in1.=" b.mst_id in($ids) or";
			}
			$ba_cond_in1=chop($ba_cond_in1,'or ');
			$ba_cond_in1.=")";
			}
			else
			{
			$ba_cond_in1=" and b.mst_id in($baIds)";
			}


		// $sub_con_sql=" select a.batch_no, a.batch_date, a.color_id, a.floor_id, a.machine_no, a.color_range_id, a.extention_no, a.batch_weight, a.process_id, listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) as po_number, a.total_trims_weight, listagg(cast(c.job_no_mst as varchar2(4000)),',') within group (order by c.job_no_mst) as job_no_mst, listagg(cast(c.delivery_date as varchar2(4000)),',') within group (order by c.delivery_date) as delivery_date,a.remarks ,c.cust_buyer,c.cust_style_ref from pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_dtls c where a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $ba_cond_in1 group by a.batch_no, a.batch_date, a.color_id, a.floor_id, a.machine_no, a.color_range_id, a.extention_no, a.batch_weight, a.process_id, a.total_trims_weight, a.remarks, c.cust_buyer, c.cust_style_ref ";

		//$sub_con_sql=" select b.mst_id as batch_id,c.job_no_mst,c.order_no,d.subcon_job,d.party_id from pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_dtls c,subcon_ord_mst d where a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id  and d.subcon_job=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $ba_cond_in1 $sub_company_cond";
		$sub_con_sql=" select b.mst_id as batch_id,c.job_no_mst,c.order_no,d.subcon_job,d.party_id from pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_dtls c,subcon_ord_mst d,gbl_temp_engine g where a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.mst_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=28 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $sub_company_cond";
		//echo $sub_con_sql;

		$sub_con_arr =sql_select($sub_con_sql);

		$sub_buyer_arr = array();
		$sub_order_arr = array();
		foreach ($sub_con_arr as $row)
		{
			$sub_buyer_arr[$row[csf('batch_id')]] = $row[csf('job_no_mst')];
			$sub_order_arr[$row[csf('batch_id')]] = $row[csf('order_no')];
			$job_buyer[$row[csf('subcon_job')]] = $row[csf('party_id')];
		}
	}
	//echo $sql;

	//	$arr = array(2 => $batch_no_arr, 3 => $po_file_arr, 4 => $po_ref_arr, 7 => $knitting_source, 9 => $buyer_arr, 10 => $color_arr, 11 => $color_range , 11 => $po_buyer_arr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 18, 2, $sales_id_arr, $empty_arr);//Sales ID Ref from=2
	$sales_arr = $po_arr = $sub_po_arr = array();
	if(!empty($sales_id_arr)){
		$sales_order_result = sql_select("select b.id,b.job_no,b.within_group,b.sales_booking_no,b.buyer_id from fabric_sales_order_mst b,gbl_temp_engine g where  b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=2 and g.entry_form=28 and b.status_active=1 and b.is_deleted=0");
		foreach ($sales_order_result as $sales_row) {
			//$batch_wise_sales_id_arr[$row[csf('batch_id')]] = $row[csf('po_id')];
			$within_group=$sales_row[csf("within_group")];

			$sales_arr[$sales_row[csf("id")]]["buyer_id"] = $sales_row[csf("buyer_id")];
			$sales_arr[$sales_row[csf("id")]]["within_group"] = $sales_row[csf("within_group")];
			$sales_arr[$sales_row[csf("id")]]["sales_no"] = $sales_row[csf("job_no")];
		}
	}
	//$batchID_cond=where_con_using_array($batch_idArr,0,'a.id');

	 $sql_book="select a.id as batch_id,a.booking_no_id,a.booking_without_order,b.buyer_id from wo_booking_mst b,pro_batch_create_mst a,gbl_temp_engine g where b.booking_no=a.booking_no and a.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=28  and a.status_active=1 ";
	$sql_bookArr =sql_select($sql_book);
		foreach ($sql_bookArr as $row)
		{
			$book_buyer_arr[$row[csf("batch_id")]]["buyer_id"] = $row[csf("buyer_id")];
			$book_buyer_arr[$row[csf("batch_id")]]["booking_without_order"] = $row[csf("booking_without_order")];
		}
		unset($sql_bookArr);
		$sql_book_non="select a.id as batch_id,a.booking_no_id,a.booking_without_order,b.buyer_id from wo_non_ord_samp_booking_mst b,pro_batch_create_mst a,gbl_temp_engine g  where b.booking_no=a.booking_no and a.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=28 and a.status_active=1 $batchID_cond";
	$sql_non_bookArr =sql_select($sql_book_non);
		foreach ($sql_non_bookArr as $row)
		{
			$book_non_buyer_arr[$row[csf("batch_id")]]["buyer_id"] = $row[csf("buyer_id")];
			$book_non_buyer_arr[$row[csf("batch_id")]]["booking_without_order"] = $row[csf("booking_without_order")];
		}
		unset($sql_non_bookArr);

	$sql_irib="SELECT a.id as batch_id, b.booking_no, c.grouping 
	from gbl_temp_engine g, pro_batch_create_mst a, wo_booking_dtls b, wo_po_break_down c
	where a.id=g.ref_val  and g.user_id = $user_id and g.ref_from=1 and g.entry_form=28 and b.booking_no=a.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	// echo $sql_irib;die;
	$sql_iribArr =sql_select($sql_irib);
	foreach ($sql_iribArr as $row)
	{
		$irib_arr[$row[csf("batch_id")]]["irib"] = $row[csf("grouping")];// internal Ref
	}
	unset($sql_iribArr);
	// echo "<pre>";print_r($irib_arr);die;

	// echo create_list_view("tbl_list_search", "ID,Labdip No,Batch No,File No,Ref. No,Recipe Description,Recipe Date,Order Source,Booking,Buyer,Color,Color Range,Pick Up,Surplus Solution", "50,80,80,70,80,130,70,80,110,100,70,90,90,90", "1250", "200", 0, $sql, "js_set_value", "id", "", 1, "0,0,batch_id,batch_id,batch_id,0,0,order_source,0,buyer_id,color_id,color_range,0,0", $arr, "id,labdip_no,batch_id,batch_id,batch_id,recipe_description,recipe_date,order_source,style_or_order,buyer_id,color_id,color_range,pickup,surplus_solution", "", "", '0,0,0,0,0,0,3,0,0,0,0,0,0,0', '');
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=28");
	oci_commit($con);
	disconnect($con);
	?>

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300" class="rpt_table">
		<thead>
			<th width="30">SL No</th>
			<th width="50">ID</th>
			<th width="80">Labdip No</th>
			<th width="80">Batch No</th>
			<th width="80">Batch Weight</th>
            <th width="50">Ext. No</th>
			<th width="70">File No</th>
			<th width="80">Ref. No</th>
			<th width="130">Recipe Description</th>
			<th width="70">Recipe Date</th>
			<th width="80">Order Source</th>
			<th width="110">Booking</th>
			<th width="100">IR/IB</th>
			<th width="100">Buyer</th>
			<th width="70">Color</th>
			<th width="70">Po Order No</th>
			<th width="90">Color Range</th>
			<th width="90">Pick Up</th>
			<th width="90">Surplus Solution</th>
			</thead>
		</table>
		<div style="width:1320px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300" class="rpt_table"
			id="tbl_list_search">
			<?
			$i = 1;
			foreach ($sql_result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				$sales_id=$batch_wise_sales_id_arr[$row[csf('id')]];
				$sales_buyer_id=$sales_arr[$sales_id]["buyer_id"];
				$sales_no=$sales_arr[$sales_id]["sales_no"];
				$within_group=$sales_arr[$sales_id]["within_group"];
				$booking_without_order=$book_buyer_arr[$row[csf("batch_id")]]["booking_without_order"];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    onClick="js_set_value(<? echo $row[csf('id')]; ?>);">
					<td width="30"><? echo $i; ?></td>
					<td width="50"><p><? echo $row[csf('id')]; ?></p></td>
					<td width="80"><p><? echo $row[csf('labdip_no')]; ?></p></td>
					<td width="80"><p><? echo  $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
					<td width="80" align="right"><p><? echo number_format($row[csf('batch_qty')],2,'.',''); ?></p></td>
                    <td width="50"><p><? echo  $batch_no_ex_no_arr[$row[csf('batch_id')]]; ?></p></td>
					<td width="70"><p><? echo  $po_file_arr[$row[csf('batch_id')]]; ?></p></td>
					<td width="80"><p><? echo  $po_ref_arr[$row[csf('batch_id')]]; ?></p></td>
					<td width="130"><p><? echo $row[csf('recipe_description')]; ?></p></td>
					<td width="70"><p><? echo change_date_format($row[csf('recipe_date')]); ?></p></td>
					<td width="80" title="<? echo 'knitting_source : '.$row[csf('order_source')]; ?>"><p><? echo $knitting_source[$row[csf('order_source')]]; ?></p></td>
					<td width="110"><p><? echo $row[csf('style_or_order')]; ?></p></td>
					<td width="100"><p><? echo $irib_arr[$row[csf('batch_id')]]['irib']; ?></p></td>
					<td width="100" title="<?=$sales_no;?>"><p>
						<?
						if($row[csf('buyer_id')])
						{
							echo $buyer_arr[$row[csf('buyer_id')]];
						}
						else
						{
							if($row[csf('order_source')] == 1)
							{
								if($booking_without_order==1)
								{
									$buyer=$buyer_arr[$book_non_buyer_arr[$row[csf("batch_id")]]["buyer_id"]];
								}
								else if($booking_without_order==0)
								{
									$buyer=$buyer_arr[$book_buyer_arr[$row[csf("batch_id")]]["buyer_id"]];
								}
								else
								{
								$buyer= $buyer_arr[$po_buyer_arr[$row[csf('batch_id')]]];
								}
							}
							else
							{
								echo $buyer_arr[$job_buyer[$sub_buyer_arr[$row[csf('batch_id')]]]];

							}

						}
						 ?>
					</p></td>
					<td width="70"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
					<td width="70"><p><?
					if($row[csf('order_source')] == 1)
					{
						echo $po_number_arr[$row[csf('batch_id')]];
					}else
					{
						echo $sub_order_arr[$row[csf('batch_id')]];

					}

					 ?></p></td>
					<td width="90"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
					<td width="90"><p><? echo $row[csf('pickup')]; ?></p></td>
					<td width="90"><p><? echo $row[csf('surplus_solution')]; ?></p></td>

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

if ($action == "create_recipe_search_list_view_old") {
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$batch_no_arr = return_library_array("select id, batch_no from pro_batch_create_mst where  status_active=1 and is_deleted=0", 'id', 'batch_no');
	$data = explode("_", $data);
	$labdip = $data[0];
	$sysid = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];
	$rec_des = trim($data[5]);
	$search_type = $data[6];
	$batch_no = $data[7];
	$working_company_id = $data[8];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and recipe_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-", 1) . "'";
		} else if ($db_type == 2) {
			$date_cond = "and recipe_date between '" . change_date_format(trim($start_date), "mm-dd-yyyy", "/", 1) . "' and '" . change_date_format(trim($end_date), "mm-dd-yyyy", "/", 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	if ($search_type == 1) {
		if ($labdip != '') $labdip_cond = " and labdip_no='$labdip'"; else $labdip_cond = "";
		if ($sysid != '') $sysid_cond = " and id=$sysid"; else $sysid_cond = "";
		if ($rec_des != '') $rec_des_cond = " and recipe_description='$rec_des'"; else $rec_des_cond = "";
	} else if ($search_type == 4 || $search_type == 0) {
		if ($labdip != '') $labdip_cond = " and labdip_no like '%$labdip%'"; else $labdip_cond = "";
		if ($sysid != '') $sysid_cond = " and id like '%$sysid%' "; else $sysid_cond = "";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '%$rec_des%'"; else $rec_des_cond = "";
	} else if ($search_type == 2) {
		if ($labdip != '') $labdip_cond = " and labdip_no like '$labdip%'"; else $labdip_cond = "";
		if ($sysid != '') $sysid_cond = " and id like '$sysid%' "; else $sysid_cond = "";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '$rec_des%'"; else $rec_des_cond = "";
	} else if ($search_type == 3) {
		if ($labdip != '') $labdip_cond = " and labdip_no like '%$labdip'"; else $labdip_cond = "";
		if ($sysid != '') $sysid_cond = " and id like '%$sysid' "; else $sysid_cond = "";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '%$rec_des'"; else $rec_des_cond = "";
	}
	if ($batch_no != "") {
		//$batch_ids = return_field_value("id", "pro_batch_create_mst", "batch_no='$batch_no' and status_active=1 and is_deleted=0", "id");
		//$batch_cond = "and batch_id=$batch_ids";

		$sql_batch=sql_select(" select id from pro_batch_create_mst where (working_company_id=$working_company_id OR company_id=$company_id) and batch_no='$batch_no' and status_active=1 and is_deleted=0");
		foreach ($sql_batch as  $value) {
			$batch_ids_arr[$value[csf('id')]]=$value[csf('id')];
		}

		if(!empty($batch_ids_arr))
		{
			$batch_cond = "and batch_id in(".implode(",", $batch_ids_arr).")";

		}

	} else {
		$batch_cond = "";
	}
	$lc_working_company_cond="";
	if($working_company_id!=0 && $company_id!=0)
	{
		$lc_working_company_cond="and (working_company_id=$working_company_id OR company_id=$company_id)";

		if($company_id!=0) $po_company_cond="and a.company_name=$company_id";else $po_company_cond="";

	}
	else
	{
		//echo $working_company_id."SS";
		if($working_company_id!=0) $lc_working_company_cond="and working_company_id=$working_company_id";
		if($company_id!=0) $lc_working_company_cond.="and company_id=$company_id";
		if($company_id!=0) $po_company_cond="and a.company_name=$company_id";else $po_company_cond="";
	}

	$sql = "select id, labdip_no,batch_id, recipe_description, recipe_date, order_source, style_or_order, buyer_id, color_id, color_range, pickup, surplus_solution from pro_recipe_entry_mst where entry_form=59 and status_active=1 and is_deleted=0 $lc_working_company_cond $labdip_cond $sysid_cond $rec_des_cond $date_cond $batch_cond order by id DESC";

	$sql_result=sql_select($sql);
	foreach($sql_result as $row)
	{
		$all_batch_id.=$row[csf("batch_id")].",";
	}
	$all_batch_id=chop($all_batch_id,",");
	if($all_batch_id!="")
	{
		$baIds=chop($all_batch_id,','); $ba_cond_in="";
			$ba_ids=count(array_unique(explode(",",$all_batch_id)));
			if($db_type==2 && $ba_ids>1000)
			{
			$ba_cond_in=" and (";
			$baIdsArr=array_chunk(explode(",",$baIds),999);
			foreach($baIdsArr as $ids)
			{
			$ids=implode(",",$ids);
			$ba_cond_in.=" c.mst_id in($ids) or";
			}
			$ba_cond_in=chop($ba_cond_in,'or ');
			$ba_cond_in.=")";
			}
			else
			{
			$ba_cond_in=" and c.mst_id in($baIds)";
			}
		$po_arr = sql_select("select b.id,c.mst_id as batch_id,b.file_no,b.grouping from  wo_po_break_down b,wo_po_details_master a,pro_batch_create_dtls c where  a.job_no=b.job_no_mst and b.id=c.po_id and b.status_active=1 and b.is_deleted=0  $ba_cond_in $po_company_cond ");
		$po_ref_arr = array();
		$po_file_arr = array();
		foreach ($po_arr as $row) {

			$po_file_arr[$row[csf('batch_id')]] = $row[csf('file_no')];
			$po_ref_arr[$row[csf('batch_id')]] = $row[csf('grouping')];
		}
	}
	//echo $sql;

	$arr = array(2 => $batch_no_arr, 3 => $po_file_arr, 4 => $po_ref_arr, 7 => $knitting_source, 9 => $buyer_arr, 10 => $color_arr, 11 => $color_range);

	echo create_list_view("tbl_list_search", "ID,Labdip No,Batch No,File No,Ref. No,Recipe Description,Recipe Date,Order Source,Booking,Buyer,Color,Color Range,Pick Up,Surplus Solution", "50,80,80,70,80,130,70,80,110,100,70,90,90,90", "1250", "200", 0, $sql, "js_set_value", "id", "", 1, "0,0,batch_id,batch_id,batch_id,0,0,order_source,0,buyer_id,color_id,color_range,0,0", $arr, "id,labdip_no,batch_id,batch_id,batch_id,recipe_description,recipe_date,order_source,style_or_order,buyer_id,color_id,color_range,pickup,surplus_solution", "", "", '0,0,0,0,0,0,3,0,0,0,0,0,0,0', '');

	exit();
}

if ($action == 'populate_data_from_search_popup') {
	//echo "select id, labdip_no, company_id, location_id, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range from pro_recipe_entry_mst where id='$data'";
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');

	$data_array = sql_select("select id, labdip_no, labdip_id, order_source, company_id, working_company_id, location_id, recipe_description, batch_id,batch_qty, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, machine_id, copy_from, recipe_type, batch_ratio, liquor_ratio, remarks, sub_tank, buyer_id, color_id, color_range, pickup, surplus_solution, recipe_serial_no, pump, cycle_time, in_charge_id from pro_recipe_entry_mst where id='$data'");
	$sql_rec_dtls = sql_select("select id,mst_id,process_remark,liquor_ratio,total_liquor from pro_recipe_entry_dtls where mst_id=" . $data . " and status_active=1");
	foreach ($sql_rec_dtls as $row)
	{
		$sub_process_Arr[$row[csf("mst_id")]]['total_liquor']=$row[csf("total_liquor")];
		$sub_process_Arr[$row[csf("mst_id")]]['liquor_ratio']=$row[csf("liquor_ratio")];
	}
	$batch_id=$data_array[0][csf('batch_id')];
	$in_charge_arr = return_library_array("select b.id, b.first_name from lib_employee b,pro_recipe_entry_mst f where f.in_charge_id=b.id  and f.batch_id=$batch_id and f.status_active=1 and f.is_deleted=0 and b.status_active=1 and b.is_deleted=0", 'id', 'first_name');

	$iss_req = return_library_array("SELECT b.id, b.requ_no from dyes_chem_requ_recipe_att a, dyes_chem_issue_requ_mst b where a.mst_id=b.id and b.entry_form=156 and a.recipe_id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", 'id', 'requ_no');
	// echo "<pre>";print_r($iss_req);die;

	foreach ($data_array as $row) 
	{
		echo "document.getElementById('txt_sys_id').value 					= '" . $row[csf("id")] . "';\n";

		echo "document.getElementById('update_id_check').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('txt_labdip_no').value 				= '" . $row[csf("labdip_no")] . "';\n";
		echo "document.getElementById('hidden_labdip_id').value 			= '" . $row[csf("labdip_id")] . "';\n";
		echo "document.getElementById('cbo_company_id').value 				= '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_working_company_id').value 		= '" . $row[csf("working_company_id")] . "';\n";
		echo "$('#cbo_company_id').attr('disabled','true')" . ";\n";
		echo "$('#cbo_working_company_id').attr('disabled','true')" . ";\n";

		//echo "load_drop_down('requires/recipe_entry_controller', ".$row[csf('company_id')].", 'load_drop_down_location', 'location_td' );\n";
		//echo "load_drop_down('requires/recipe_entry_controller', ".$row[csf('company_id')].", 'load_drop_down_buyer', 'buyer_td_id' );\n";
		echo "load_drop_down('requires/recipe_entry_controller', '" . $row[csf("working_company_id")] . "', 'load_drop_down_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location').value 				= '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('txt_recipe_date').value 				= '" . change_date_format($row[csf("recipe_date")]) . "';\n";
		echo "document.getElementById('cbo_order_source').value 			= '" . $row[csf("order_source")] . "';\n";

		echo "load_drop_down('requires/recipe_entry_controller', ".$row[csf('working_company_id')].", 'load_drop_machine', 'td_dyeing_machine' );\n";

		echo "document.getElementById('txt_recipe_des').value 				= '" . $row[csf("recipe_description")] . "';\n";
		echo "document.getElementById('txt_batch_id').value 				= '" . $row[csf("batch_id")] . "';\n";
		echo "document.getElementById('cbo_machine_name').value 				= '" . $row[csf("machine_id")] . "';\n";
		if (!empty($iss_req)) 
		{
			echo "$('#cbo_machine_name').attr('disabled','true')" . ";\n";
		}
		
		// echo "document.getElementById('txt_machine_no').value 				= '" . $machine_arr[$row[csf("machine_id")]] . "';\n";
		echo "document.getElementById('cbo_method').value 					= '" . $row[csf("method")] . "';\n";

		echo "document.getElementById('txt_liquor').value 					= '" . $row[csf("total_liquor")] . "';\n";
		echo "document.getElementById('txt_batch_ratio').value 				= '" . $row[csf("batch_ratio")] . "';\n";
		echo "document.getElementById('txt_liquor_ratio').value 			= '" . $row[csf("liquor_ratio")] . "';\n";
		echo "document.getElementById('cbo_recipe_type').value 			= '" . $row[csf("recipe_type")] . "';\n";
		echo "document.getElementById('txt_pick_up').value 			= '" . $row[csf("pickup")] . "';\n";
		echo "document.getElementById('surpls_solution').value 			= '" . $row[csf("surplus_solution")] . "';\n";

		echo "document.getElementById('txt_recipe_serial_no').value 			= '" . $row[csf("recipe_serial_no")] . "';\n";
		echo "document.getElementById('txt_remarks').value 					= '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('txt_sub_tank').value 					= '" . $row[csf("sub_tank")] . "';\n";
		echo "document.getElementById('txt_pump').value 					= '" . $row[csf("pump")] . "';\n";
		echo "document.getElementById('txt_cycle_time').value 					= '" . $row[csf("cycle_time")] . "';\n";
		echo "document.getElementById('txt_in_charge_id').value	= '" . $row[csf("in_charge_id")] . "';\n";
		echo "document.getElementById('txt_in_charge').value	= '" . $in_charge_arr[$row[csf("in_charge_id")]] . "';\n";

		echo "document.getElementById('txt_copy_from').value 				= '" . $row[csf("copy_from")] . "';\n";
		echo "document.getElementById('update_id').value 					= '" . $row[csf("id")] . "';\n";
		$order_source=$row[csf("order_source")];
		echo "get_php_form_data(" . $row[csf("working_company_id")] . "+'**'+" . $row[csf("batch_id")] . "+'**'+" . $order_source . ", 'load_data_from_batch', 'requires/recipe_entry_controller');\n";
		if ($row[csf("batch_qty")] != 0) {
			echo "document.getElementById('txt_batch_weight').value 				= '" . $row[csf("batch_qty")] . "';\n";
		}

		//echo "select id,sub_process_id,process_remark from pro_recipe_entry_dtls where mst_id='".$row[csf("id")]."' ";
		// $sql_rec_dtls = sql_select("select id,sub_process_id,process_remark,liquor_ratio,total_liquor from pro_recipe_entry_dtls where mst_id=" . $row[csf("id")] . " ");

		$liquor_ratio =$sub_process_Arr[$row[csf("id")]]['liquor_ratio'];// $sql_rec_dtls[0][csf('liquor_ratio')];
		$total_liquor =$sub_process_Arr[$row[csf("id")]]['total_liquor'];//$sql_rec_dtls[0][csf('total_liquor')];

		//echo "document.getElementById('txt_liquor_ratio_dtls').value 			= '".$liquor_ratio."';\n";
		//echo "document.getElementById('txt_total_liquor_ratio').value 			= '".$total_liquor."';\n";

		/*$sub_process=$sql_rec_dtls[0][csf('sub_process_id')];
		echo "subprocess_change($sub_process);\n";
		if($sub_process==93 || $sub_process==94 || $sub_process==95 || $sub_process==96 || $sub_process==97 || $sub_process==98)
		{
			echo "document.getElementById('cbo_sub_process').value 				= ".$sub_process.";\n";

			echo "document.getElementById('txt_subprocess_remarks').value 				= '".$sql_rec_dtls[0][csf('process_remark')]."';\n";
			//echo "document.getElementById('update_dtls_id').value 				= '".$sql_rec_dtls[0][csf('id')]."';\n";
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_recipe_entry',1);\n";

		}
		else
		{
			echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_recipe_entry',1);\n";
		}*/
		echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_recipe_entry',1);\n";
		echo "$('#btn_recipe_calc').removeAttr('disabled','disabled');\n";
		echo "$('#btn_recipe_calc').removeClass('formbutton_disabled').addClass('formbutton');\n";
		echo "$('#btn_recipe_calc_2').removeAttr('disabled','disabled');\n";
		echo "$('#btn_recipe_calc_2').removeClass('formbutton_disabled').addClass('formbutton');\n";
		echo "$('#btn_recipe_calc_3').removeAttr('disabled','disabled');\n";
		echo "$('#btn_recipe_calc_3').removeClass('formbutton_disabled').addClass('formbutton');\n";
		echo "$('#btn_recipe_calc_8').removeAttr('disabled','disabled');\n";
		echo "$('#btn_recipe_calc_8').removeClass('formbutton_disabled').addClass('formbutton');\n";

		exit();
	}
}

if ($action == "booking_popup") {
	echo load_html_head_contents("WO Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(booking_id, booking_no, color, color_id, job_no, type) {
			$('#hidden_booking_id').val(booking_id);
			$('#hidden_booking_no').val(booking_no);
			$('#hidden_color').val(color);
			$('#hidden_color_id').val(color_id);
			$('#hidden_job_no').val(job_no);
			$('#booking_without_order').val(type);
			parent.emailwindow.hide();
		}

	</script>

</head>

<body>
	<div align="center" style="width:775px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:100%;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="750" class="rpt_table" border="1" rules="all">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="200">Enter Booking No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
							class="formbutton"/>
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
							value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="txt_buyer_id" id="txt_buyer_id" class="text_boxes"
							value="<? echo $cbo_buyer_name; ?>">
							<input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes"
							value="">
							<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes"
							value="">
							<input type="hidden" name="hidden_color" id="hidden_color" class="text_boxes" value="">
							<input type="hidden" name="hidden_color_id" id="hidden_color_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_job_no" id="hidden_job_no" class="text_boxes" value="">
							<input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes"
							value="">
						</th>
					</thead>
					<tr>
						<td align="center">
							<?
							echo create_drop_down("cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", $data[0]);
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Booking No", 2 => "Buyer Order", 3 => "Job No", 4 => "Booking Date");
							$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*2', '../../') ";
							echo create_drop_down("cbo_search_by", 170, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo $batch_against; ?>', 'create_booking_search_list_view', 'search_div', 'recipe_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:100px;"/>
						</td>
					</tr>
				</table>
				<table width="100%" style="margin-top:5px">
					<tr>
						<td colspan="5">
							<div style="width:100%; margin-top:10px; margin-left:3px" id="search_div"
							align="left"></div>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action == "create_booking_search_list_view") {
	$data = explode("_", $data);

	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$buyer_id = $data[3];
	$batch_against = $data[4];

	if ($db_type == 0) {
		$groupby_field = "group by a.id, b.fabric_color_id";
		$groupby_u_field = "group by a.id, b.fabric_color_id";
		$groupby_d_field = "group by s.id, f.fabric_color";
	} else if ($db_type == 2) {
		$groupby_field = "group by a.id, b.fabric_color_id,a.booking_no, a.booking_date, a.buyer_id,c.job_no, c.style_ref_no ";
		$groupby_u_field = "group by a.id, b.fabric_color_id,a.booking_no, a.booking_date, a.buyer_id,c.job_no, c.style_ref_no ";
		$groupby_d_field = "group by s.id, f.fabric_color,s.booking_no, s.booking_date, s.buyer_id";
	}

	if ($buyer_id == 0) {
		echo "Please Select Buyer First.";
		die;
	}

	//$po_number_array = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');;
	if (trim($data[0]) != "") {
		if ($search_by == 1)
			$search_field_cond = "and a.booking_no like '$search_string'";
		else if ($search_by == 2)
			$search_field_cond = "and d.po_number like '$search_string'";
		else if ($search_by == 3)
			$search_field_cond = "and c.job_no like '$search_string'";
		else
			$search_field_cond = "and a.booking_date like '" . change_date_format(trim($data[0]), "yyyy-mm-dd", "-") . "'";
	} else {
		$search_field_cond = "";
	}

	if ($batch_against == 1) {
		if ($db_type == 0) {
			$sql = "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id, c.job_no, c.style_ref_no,group_concat(distinct(d.id)) as po_id, 0 as type FROM wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d WHERE a.booking_no=b.booking_no and a.booking_type<>4 and b.po_break_down_id=d.id and a.company_id=$company_id and a.buyer_id=$buyer_id and c.job_no=d.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0  $search_field_cond $groupby_field";
		} else if ($db_type == 2) {
			$sql = "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id, c.job_no, c.style_ref_no, listagg(d.id,',') within group (order by d.id) as po_id, 0 as type FROM wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d WHERE a.booking_no=b.booking_no and a.booking_type<>4 and b.po_break_down_id=d.id and a.company_id=$company_id and a.buyer_id=$buyer_id and c.job_no=d.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0  $search_field_cond $groupby_field";
		}
	} else {
		if ($search_by == 1)
			$search_field_cond_sample = "and s.booking_no like '$search_string'";
		else if ($search_by == 4)
			$search_field_cond_sample = "and s.booking_date like '" . change_date_format(trim($data[0]), "yyyy-mm-dd", "-", 1) . "'";
		else
			$search_field_cond_sample = "";
		if ($db_type == 0) {
			$sql = "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id, c.job_no, c.style_ref_no, group_concat(distinct(d.id)) as po_id, 0 as type FROM wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d WHERE a.booking_no=b.booking_no and b.po_break_down_id=d.id and a.company_id=$company_id and a.buyer_id=$buyer_id and c.job_no=d.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and a.item_category=2 $search_field_cond $groupby_u_field
			union all
			SELECT s.id, s.booking_no, s.booking_date, s.buyer_id, f.fabric_color as fabric_color_id, NULL as job_no, NULL as style_ref_no, NULL as po_id, 1 as type FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls f WHERE s.booking_no=f.booking_no and s.company_id=$company_id and s.buyer_id=$buyer_id and s.status_active =1 and s.is_deleted =0 and f.status_active =1 and f.is_deleted =0  $search_field_cond_sample $groupby_d_field
			";
		} else if ($db_type == 2) {
			$sql = "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id, c.job_no, c.style_ref_no,listagg(d.id,',') within group (order by d.id) as po_id, 0 as type FROM wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d WHERE a.booking_no=b.booking_no and b.po_break_down_id=d.id and a.company_id=$company_id and a.buyer_id=$buyer_id and c.job_no=d.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and a.item_category=2 $search_field_cond $groupby_u_field
			union all
			SELECT s.id, s.booking_no, s.booking_date, s.buyer_id, f.fabric_color as fabric_color_id, NULL as job_no, NULL as style_ref_no, NULL as po_id, 1 as type FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls f WHERE s.booking_no=f.booking_no and s.company_id=$company_id and s.buyer_id=$buyer_id and s.status_active =1 and s.is_deleted =0 and f.status_active =1 and f.is_deleted =0  $search_field_cond_sample $groupby_d_field
			";
		}
	}

	//echo $sql;
	$result = sql_select($sql);
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$color_arr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="115">Booking No</th>
			<th width="75">Booking Date</th>
			<th width="100">Buyer</th>
			<th width="85">Job No</th>
			<th width="100">Style Ref.</th>
			<th width="70">Color</th>
			<? if ($batch_against == 3) { ?>
				<th width="60">Without Order</th><? } ?>
				<th>Buyer Order</th>
			</thead>
		</table>
		<div style="width:770px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table"
			id="tbl_list_search">
			<?
			foreach ($result as $row)
			{
				$po_idArr[$row[csf('po_id')]]=$row[csf('po_id')];
			}
			 $con = connect();
			execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (3) and ENTRY_FORM=28");
			oci_commit($con);
			disconnect($con);
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 28, 3, $po_idArr, $empty_arr);//PO ID Ref from=3
			$po_number_array = return_library_array("select b.id,b.po_number from wo_po_break_down b,gbl_temp_engine g  where b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=2 and g.entry_form=28 and b.status_active=1", 'id', 'po_number');

			 $con = connect();
			execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (3) and ENTRY_FORM=28");
			oci_commit($con);

			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				/* if($row[csf('po_id')]!="")
				{
                	if($db_type==0)
					{
					$po_no=return_field_value(" group_concat(po_number)","wo_po_break_down","id in (".$row[csf('po_id')].")");
					}
					else if($db_type==2)
					{
					$po_no=return_field_value("listagg(po_number,',') within group (order by po_number) as po_number","wo_po_break_down","id in (".$row[csf('po_id')].")",'po_number');

					}
				}
				else $po_no="";*/

				$po_no = "";
				$po_id = array_unique(explode(",", $row[csf('po_id')]));

				foreach ($po_id as $val) {
					if ($po_no == '') $po_no = $po_number_array[$val]; else $po_no .= "," . $po_number_array[$val];
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $color_arr[$row[csf('fabric_color_id')]]; ?>','<? echo $row[csf('fabric_color_id')]; ?>','<? echo $po_no;//$row[csf('job_no')];
                    ?>','<? echo $row[csf('type')]; ?>');">
                    <td width="30"><? echo $i; ?></td>
                    <td width="115"><p><? echo $row[csf('booking_no')]; ?></p></td>
                    <td width="75" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                    <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
                    <td width="85" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
                    <td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                    <td width="70"><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></td>
                    <? if ($batch_against == 3) { ?>
                    	<td width="60"
                    	align="center"><? if ($row[csf('type')] == 0) echo "No"; else echo "Yes"; ?></td><? } ?>
                    	<td><p><? echo $po_no; ?></p></td>
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


    if ($action == "recipe_item_details") {
    	$process_array = array();
    	$process_array_remark = array();
    	$sql = "select id,sub_seq, sub_process_id as sub_process_id,process_remark, store_id from pro_recipe_entry_dtls where mst_id='$data'   and status_active=1 and is_deleted=0 order by sub_seq";
    	$nameArray = sql_select($sql);
    	foreach ($nameArray as $row) {
    		if (!in_array($row[csf("sub_process_id")], $process_array)) {
    			$process_array[] = $row[csf("sub_process_id")];
    			$process_array_remark[$row[csf("sub_process_id")]] = $row[csf("process_remark")]."**".$row[csf("store_id")];
    		}
    	}
		//print_r($process_array);
    	foreach ($process_array  as $sub_provcess_id) {
    		$process_ref = explode("**",$process_array_remark[$sub_provcess_id]);
			$process_remark=$process_ref[0];
			$store_id=$process_ref[1];
    		?>
    		<h3 align="left" id="accordion_h<? echo $sub_provcess_id; ?>" style="width:910px" class="accordion_h"
    			onClick="fnc_item_details(<? echo $sub_provcess_id; ?>,'<? echo $process_remark; ?>','<? echo $store_id; ?>')"><span
    			id="accordion_h<? echo $sub_provcess_id; ?>span">+</span><? echo $dyeing_sub_process[$sub_provcess_id]; ?>
    		</h3>
    		<?
    	}
    }

    if ($action == "batch_popup") {
    	echo load_html_head_contents("Batch Info", "../../", 1, 1, '', '1', '');
    	extract($_REQUEST);
    	?>
    	<script>
    		function js_set_value(batch_data) {
          //  alert (batch_data);
          document.getElementById('hidden_batch_id').value = batch_data;
			//document.getElementById('hidden_batch_type').value = batch_type;
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="left">
		<fieldset style="width:800px;margin-left:4px;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="700" class="rpt_table">
					<thead>
						<tr>
							<th colspan="4">
								<?
								echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --");
								?>
							</th>
						</tr>
						<tr>
							<th>Batch Type</th>
							<th>Batch</th>
							<th>Date Range</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
								class="formbutton"/>
								<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" value="">

							</th>
						</tr>
					</thead>
					<tr class="general">
						<td align="center">
							<?
							echo create_drop_down("cbo_search_by", 150, $order_source, "", 1, "--Select--", 0, 0, 0);
							?>
						</td>
						<td align="center">
							<input type="text" style="width:140px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:60px;">To<input type="text" name="txt_date_to" id="txt_date_to"
							class="datepicker" style="width:60px;">
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+'<? echo $cbo_company_id; ?>'+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_batch_search_list_view', 'search_div', 'recipe_entry_controller', 'setFilterGrid(\'list_view\',-1);')"
							style="width:100px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="6" align="center" height="40"
						valign="middle"><? echo load_month_buttons(1); ?></td>
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
exit();
}

if ($action == "create_batch_search_list_view") {
	$data = explode('_', $data);
	$search_common = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$search_type = $data[3];
	$start_date = $data[4];
	$end_date = $data[5];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and a.batch_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-", 1) . "'";
		} else if ($db_type == 2) {
			$date_cond = "and a.batch_date between '" . change_date_format(trim($start_date), "mm-dd-yyyy", "/", 1) . "' and '" . change_date_format(trim($end_date), "mm-dd-yyyy", "/", 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	if ($search_common == "") {
		//echo "<p style='color:firebrick; text-align: center; font-weight: bold;'>Batch No is required</p>";
		//exit;

	}

	if ($search_type == 1) {
		if ($search_common != '') $batch_cond = " and a.batch_no='$search_common'"; else $batch_cond = "";
	} else if ($search_type == 4 || $search_type == 0) {
		if ($search_common != '') $batch_cond = " and a.batch_no like '%$search_common%'"; else $batch_cond = "";
	} else if ($search_type == 2) {
		if ($search_common != '') $batch_cond = " and a.batch_no like '$search_common%'"; else $batch_cond = "";
	} else if ($search_type == 3) {
		if ($search_common != '') $batch_cond = " and a.batch_no like '%$search_common'"; else $batch_cond = "";
	}

	if ($search_by == 1) {
		$batch_type_cond = " and a.entry_form in(0,74)";
	} else if ($search_by == 2) {
		$batch_type_cond = " and a.entry_form=36";
	} else  if ($search_by == 3) {
		$batch_type_cond = " and a.entry_form in (0,36,74)";
	}
	else  if ($search_by == 4) {
		$batch_type_cond = " and a.entry_form in (136)";
	}

	if($search_by==2)
	{
		$company_batch_cond="and a.company_id=$company_id";
	}
	else
	{
		$company_batch_cond="and a.working_company_id=$company_id";
	}

	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$company_name_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');

	if($db_type==0) $select_recipe="group_concat(b.id) as recipe_id";
	else if($db_type==2) $select_recipe="listagg(b.id,',') within group (order by b.id) as recipe_id";
	$batch_result = sql_select("select a.id as batch_id,a.double_dyeing,$select_recipe ,count(b.id) total_receipe from pro_batch_create_mst a,pro_recipe_entry_mst b where a.id=b.batch_id and b.entry_form=59 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $batch_type_cond $batch_cond $company_batch_cond group by a.id,a.double_dyeing");

	$batch_check_arr = array();
	foreach ($batch_result as $row) {
		/*if($row[csf("double_dyeing")]==1 && $row[csf("total_receipe")] >= 2){
			$batch_check_arr[$row[csf("batch_id")]]= $row[csf("recipe_id")];
		}else */
		if($row[csf("double_dyeing")]!=1){
			$batch_check_arr[$row[csf("batch_id")]]= $row[csf("recipe_id")];
		}
	}

	if ($search_by!= 4)
	{
		if ($db_type == 0) 
		{
			$sql = "SELECT a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.entry_form, group_concat(b.po_id) as po_id, b.is_sales from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_type_cond $batch_cond $date_cond group by a.id, a.batch_no, a.extention_no, b.is_sales order by a.id DESC";
		} 
		else if ($db_type == 2) 
		{
			$sql = "SELECT a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.entry_form, listagg(b.po_id,',') within group (order by b.po_id) as po_id, b.is_sales from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_type_cond $batch_cond $company_batch_cond $date_cond group by a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.entry_form, b.is_sales order by a.id DESC";
		}
	}
	else
	{
		$sql = "SELECT a.id, a.batch_no,a.job_no, a.extention_no, a.batch_weight, a.batch_date, a.color_id, a.entry_form ,sum(b.trims_wgt_qnty) as trims_wgt_qnty from pro_batch_create_mst a, pro_batch_trims_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=136 $batch_type_cond $batch_cond $company_batch_cond $date_cond group by a.id, a.batch_no, a.extention_no, a.batch_weight, a.job_no, a.batch_date, a.color_id, a.entry_form order by a.id DESC";
	}
	//echo $sql;
	$nameArray = sql_select($sql);
	$po_id_arr=$subcon_po_id_arr=$sales_order_arr=array();
	foreach ($nameArray as $selectResult) {
		if($selectResult[csf("is_sales")]==1){
			$sales_order_arr[$selectResult[csf("po_id")]]=$selectResult[csf("po_id")];
		}else{
			if ($selectResult[csf("entry_form")] == 36) {
				$subcon_po_id_arr[$selectResult[csf("po_id")]]=$selectResult[csf("po_id")];
			}else{
				$po_id_arr[$selectResult[csf("po_id")]]=$selectResult[csf("po_id")];
			}
		}
	}


	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=28");
	oci_commit($con);
	disconnect($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 28, 1, $po_id_arr, $empty_arr);//PO ID Ref from=1
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 28, 2, $subcon_po_id_arr, $empty_arr);//Subcon PO ID Ref from=2
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 28, 3, $sales_order_arr, $empty_arr);//Sales ID Ref from=3


	/*$sales_arr = $po_arr = $sub_po_arr = array();
	if(!empty($sales_order_arr)){
		$sales_order_result = sql_select("select id,job_no,within_group,sales_booking_no from fabric_sales_order_mst where status_active=1 and is_deleted=0 and id in(".implode(",",$sales_order_arr).")");
		foreach ($sales_order_result as $sales_row) {
			$sales_arr[$sales_row[csf("id")]]["sales_order_no"] = $sales_row[csf("job_no")];
		}
	}

	if(!empty($po_id_arr)){
		$po_arr = return_library_array("select id,po_number from wo_po_break_down where id in(".implode(",",$po_id_arr).")", 'id', 'po_number');
	}
	if(!empty($subcon_po_id_arr)){
		$sub_po_arr = return_library_array("select id,order_no from  subcon_ord_dtls where id in(".implode(",",$subcon_po_id_arr).")", 'id', 'order_no');
	}*/
	$sales_arr = $po_arr = $sub_po_arr = array();
	if(!empty($sales_order_arr)){
		$sales_order_result = sql_select("select b.id,b.job_no,b.within_group,b.sales_booking_no from fabric_sales_order_mst b,gbl_temp_engine g where b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=3 and g.entry_form=28 and b.status_active=1 and b.is_deleted=0 ");
		foreach ($sales_order_result as $sales_row) {
			$sales_arr[$sales_row[csf("id")]]["sales_order_no"] = $sales_row[csf("job_no")];
		}
	}

	if(!empty($po_id_arr)){
		$po_arr = return_library_array("select b.id,b.po_number from wo_po_break_down b,gbl_temp_engine g where b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=28 ", 'id', 'po_number');
	}
	if(!empty($subcon_po_id_arr)){
		$sub_po_arr = return_library_array("select b.id,b.order_no from  subcon_ord_dtls b,gbl_temp_engine g where b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=2 and g.entry_form=28 ", 'id', 'order_no');
	}

	if ($search_by!=4)
	{
		$caption_head="PO/FSO No.";
	}
	else
	{
		$caption_head="Job No.";
	}?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="618" class="rpt_table">
			<caption> <strong><? echo $company_name_arr[$company_id];?></strong></caption>
			<thead>
				<th width="30">SL</th>
				<th width="70">Batch No</th>
				<th width="40">Ex.</th>
				<th width="90">Color</th>
				<th width="80">Batch Weight</th>
				<th width="80">Total Trims Weight</th>
				<th width="70">Batch Date</th>
				<th><? echo $caption_head;?></th>
			</thead>
		</table>
		<div style="width:618px; overflow-y:scroll; max-height:240px;" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="list_view">
				<?
				$con = connect();
				execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=28");
				oci_commit($con);
				$i = 1;
				foreach ($nameArray as $selectResult) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$order_no = '';
					$order_id = array_unique(explode(",", $selectResult[csf("po_id")]));
					$is_sales = $selectResult[csf("is_sales")];
					foreach ($order_id as $val) {
						if ($selectResult[csf("entry_form")] == 36) {
							if($is_sales == 1){
								$order_no = $sales_arr[$val]["sales_order_no"];
							}else{
								if ($order_no == "") $order_no = $sub_po_arr[$val]; else $order_no .= ", " . $sub_po_arr[$val];
							}
						} else {
							if($is_sales == 1){
								$order_no = $sales_arr[$val]["sales_order_no"];
							}else{
								if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
							}
						}
					}
					if ($search_by!=4) $order_nos=$order_no;else $order_nos=$selectResult[csf("job_no")];
					if ($search_by!=4) $batch_weight_qty=$selectResult[csf("total_trims_weight")];else $batch_weight_qty=$selectResult[csf("trims_wgt_qnty")];

					if($search_by>0)
					{
						$search_by=$search_by;
					}
					else
					{
						if($selectResult[csf('entry_form')]==0 || $selectResult[csf('entry_form')]==74)
						{
							$search_by=1;
						}
						else if($selectResult[csf('entry_form')]==36)
						{
							$search_by=2;
						}
						else if($selectResult[csf('entry_form')]==136)
						{
							$search_by=3;
						}
					}

					$batch_data=$selectResult[csf('id')].'_'.$search_by;
					if($batch_check_arr[$selectResult[csf("id")]]=="")
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
							id="search<? echo $i; ?>" onClick="js_set_value('<? echo $batch_data; ?>')">
							<td width="30"><? echo $i; ?></td>
							<td width="70"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
							<td width="40"><? echo $selectResult[csf('extention_no')]; ?></td>
							<td width="90"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo $selectResult[csf('batch_weight')]; ?></p></td>
							<td width="80" align="right"><p><? echo $batch_weight_qty; ?></p>
							</td>
							<td width="70" align="center">
								<p><? echo change_date_format($selectResult[csf('batch_date')]); ?></p></td>
								<td><p><? echo $order_nos; ?></p></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
				</table>
			</div>
		</div>
		<?
		exit();
	}

	if ($action == "ratio_data_from_dtls") {
		$ex_data = explode('**', $data);
		$company = $ex_data[0];
		$sub_process = $ex_data[1];
		$update_id = $ex_data[2];
		//|| sub_process==140 || sub_process==141 || sub_process==142 || sub_process==143
	//	if ($sub_process == 93 || $sub_process == 94 || $sub_process == 95 || $sub_process == 96 || $sub_process == 97 || $sub_process == 98 || $sub_process == 140 || $sub_process == 141 || $sub_process == 142 || $sub_process == 143) {
		if(in_array($sub_process,$subprocessForWashArr))
		{
			$sql_rec_dtls = "select id,sub_process_id,process_remark,liquor_ratio,total_liquor,check_id from pro_recipe_entry_dtls where mst_id=" . $update_id . " and sub_process_id=$sub_process ";
		} else {
			$sql_rec_dtls = "select id,sub_process_id,process_remark,liquor_ratio,total_liquor,check_id from pro_recipe_entry_dtls where mst_id=" . $update_id . " and sub_process_id=$sub_process and status_active=1 and ratio>0";//
		}
	 //echo $sql_rec_dtls;
		$result_dtl = sql_select($sql_rec_dtls);
		foreach ($result_dtl as $row) {
		//txt_total_liquor_ratio*txt_liquor_ratio_dtls
			if($row[csf("check_id")]==0 || $row[csf("check_id")]==2)
			{
				$check_id=2;
				echo "document.getElementById('check_id').value= '" . $check_id . "';\n";
				echo "$('#check_id').attr('checked', false);\n";

			}
			else
			{
				$check_id=$row[csf("check_id")];
				 echo "document.getElementById('check_id').value= '" . $check_id . "';\n";
				echo "$('#check_id').attr('checked', true);\n";
			}

			echo "document.getElementById('txt_liquor_ratio_dtls').value 		= '" . $row[csf("liquor_ratio")] . "';\n";
			echo "document.getElementById('txt_total_liquor_ratio').value 			= '" . $row[csf("total_liquor")] . "';\n";
			echo "caculate_tot_liquor(1);\n";
		}
		if($update_id!='')
		{
		$sub_seq_no =return_field_value("max(a.sub_seq) as sub_seq","pro_recipe_entry_dtls a"," a.mst_id=" . $update_id . "  and a.status_active=1 and a.is_deleted=0  and a.ratio>0 ", "sub_seq");
		}
		$sub_seq=$sub_seq_no+1;
		echo "document.getElementById('txt_subprocess_seq').value 			= '" . $sub_seq . "';\n";

		$sub_seq_no =return_field_value("a.sub_seq as sub_seq","pro_recipe_entry_dtls a"," a.mst_id=" . $update_id . "   and a.sub_process_id=$sub_process and a.status_active=1 and a.is_deleted=0  and a.ratio>0 ", "sub_seq");
		if($sub_seq_no)
		{
		echo "document.getElementById('txt_subprocess_seq').value 			= '" . $sub_seq_no . "';\n";
		}


		exit();
	}
if ($action == "recipe_previ_copy_check")
{
$ex_data = explode('**', $data);
$company =$ex_data[0];
if($db_type==0)
{
 $from_date="2019-03-07";
}
else
{
 $from_date="07-Mar-2019";
}
//$recipe_date =$ex_data[1];

$sql_recipe="select recipe_date from pro_recipe_entry_mst where  entry_form=59 and  recipe_date>='$from_date' and status_active=1";
	 echo $sql_recipe;die;
	$data_array=sql_select($sql_recipe);
	if(count($data_array)>0)
	{
		echo date("Y-m-d",strtotime($data_array[0][csf('recipe_date')]));
		//echo date("Y-m-d",strtotime($data_array[0][csf('process_end_date')]));
	}
	else
	{
		echo "";
	}
	exit();

}

	if ($action == "load_data_from_batch") {
		$ex_data = explode('**', $data);
		$po_arr = return_library_array("select b.id,b.po_number from wo_po_break_down b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$ex_data[1]'", 'id', 'po_number');
		//$sub_po_arr = return_library_array("select id,order_no from  subcon_ord_dtls", 'id', 'order_no');
		//$buyer_arr = return_library_array("select booking_no,buyer_id from wo_booking_mst", 'booking_no', 'buyer_id');
		$buyer_arr = return_library_array("select a.booking_no,a.buyer_id from wo_booking_mst a,pro_batch_create_mst b where a.booking_no=b.booking_no and a.status_active=1 and b.id='$ex_data[1]' ", 'booking_no', 'buyer_id');
		//$sample_buyer_arr = return_library_array("select booking_no,buyer_id from wo_non_ord_samp_booking_mst", 'booking_no', 'buyer_id');		
		// FSO non order booking id empty in batch mst table field BOOKING_NO_ID so use this relation a.booking_no=b.booking_no
		$sample_buyer_arr = return_library_array("select a.booking_no,a.buyer_id from wo_non_ord_samp_booking_mst a,pro_batch_create_mst b where a.booking_no=b.booking_no and b.id='$ex_data[1]'", 'booking_no', 'buyer_id');
		//$sub_buyer_arr = return_library_array("select b.id, a.party_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst", 'id', 'party_id');
		$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
		$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');

		$sub_po=sql_select("select b.id,b.order_no, a.party_id from subcon_ord_mst a, subcon_ord_dtls b,pro_batch_create_dtls c where a.subcon_job=b.job_no_mst and c.po_id=b.id and c.mst_id='$ex_data[1]'");
		foreach ($sub_po as $row) {
			$sub_po_arr[$row[csf("id")]] = $row[csf("order_no")];
			$sub_buyer_arr[$row[csf("id")]] = $row[csf("party_id")];
		}

		//$sales_order_result = sql_select("select id,job_no,within_group,sales_booking_no from fabric_sales_order_mst where status_active=1 and is_deleted=0");
		$sales_order_result = sql_select("select b.id,b.job_no,b.within_group,a.sales_booking_no from fabric_sales_order_mst a,pro_batch_create_mst b where a.id=b.sales_order_id  and a.id='$ex_data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$sales_arr = array();
		foreach ($sales_order_result as $sales_row) {
			$sales_arr[$sales_row[csf("id")]]["sales_order_no"] 	= $sales_row[csf("job_no")];
		}
		$batch_cat=$ex_data[2];
		//and a.working_company_id='$ex_data[0]'
		if($batch_cat!=4)
		{
			if ($db_type == 0) {
				$sql = "select a.id, a.batch_no,a.company_id, a.extention_no,a.batch_against, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id,a.double_dyeing, a.booking_no,a.booking_without_order, a.booking_no_id,a.dyeing_machine, a.entry_form, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id,b.is_sales,a.re_dyeing_from from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id  and a.id='$ex_data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no,a.dyeing_machine,a.company_id, a.extention_no,b.is_sales,a.re_dyeing_from,a.booking_without_order order by a.id DESC";
			} else if ($db_type == 2) {
				$sql = "select a.id, a.batch_no,a.company_id,a.batch_against, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id,a.double_dyeing, a.booking_no,a.booking_without_order, a.booking_no_id,a.dyeing_machine, a.entry_form, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,b.is_sales,a.re_dyeing_from from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id  and a.id='$ex_data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no,a.dyeing_machine,a.company_id,a.batch_against, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id,a.double_dyeing, a.booking_no,a.booking_without_order, a.booking_no_id, a.entry_form,b.is_sales,a.re_dyeing_from order by a.id DESC";
			}
		}
		else
		{
			$sql = "select a.id, a.batch_no,a.company_id,a.job_no,a.batch_against, a.extention_no, a.batch_weight, sum(b.trims_wgt_qnty) as total_trims_weight, a.batch_date, a.color_id, a.color_range_id,a.double_dyeing,a.booking_without_order,a.dyeing_machine,a.entry_form,a.re_dyeing_from from pro_batch_create_mst a, pro_batch_trims_dtls b where a.id=b.mst_id  and a.id='$ex_data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no,a.company_id,a.job_no,a.batch_against, a.extention_no, a.batch_weight,a.booking_without_order, a.dyeing_machine,a.batch_date, a.color_id, a.color_range_id,a.double_dyeing, a.entry_form,a.re_dyeing_from order by a.id DESC";
		}
		// echo $sql;die;
		if ($db_type == 2) {
			$group_po="listagg((cast(b.po_number as varchar2(4000))),',') within group (order by b.po_number) as po_number ";
		}
		else
		{
			$group_po="group_concat(b.po_number) as po_number ";
		}
		$result_sql = sql_select($sql);
		foreach ($result_sql as $row) {
			$order_no = "";
			$buyer_id = "";
			$order_id = array_unique(explode(",", $row[csf("po_id")]));
			$is_sales = $row[csf("is_sales")];
			foreach ($order_id as $val) {
				if ($row[csf("entry_form")] == 36) {
					if($is_sales == 1){
						$order_no = $sales_arr[$val]["sales_order_no"];
					}else{
						if ($order_no == "") $order_no = $sub_po_arr[$val]; else $order_no .= ", " . $sub_po_arr[$val];
					}
					if ($buyer_id == "") $buyer_id = $sub_buyer_arr[$val]; else $buyer_id .= "," . $sub_buyer_arr[$val];
				} else {
					if($is_sales == 1){
						$order_no = $sales_arr[$val]["sales_order_no"];
					}else{
						if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
					}
				}
				$booking_no=$row[csf("booking_no")];$booking_without_order=$row[csf("booking_without_order")];
			}
			$po_id = implode(",", array_unique(explode(",", $row[csf("po_id")])));
			$prod_id = implode(",", array_unique(explode(",", $row[csf("prod_id")])));
			$batch_id = implode(",", array_unique(explode(",", $row[csf("id")])));

			if ($row[csf("entry_form")] == 36) {
				$batch_type = "<b> SUBCONTRACT ORDER </b>";
				//$ord_source = 2;
				$buyer_name = implode(',', array_unique(explode(",", $buyer_id)));
			}
			else 
			{
				$batch_type = "<b> SELF ORDER </b>";
				//$ord_source = 1;
				if ($row[csf("entry_form")] == 74) {
					$result = sql_select("select c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.mst_id='$ex_data[1]' and a.status_active=1 and a.is_deleted=0");
					$buyer_name = $result[0][csf('buyer_name')];
				}
				else if ($row[csf("entry_form")] == 136) {
					$result = sql_select("select c.buyer_name,$group_po from pro_batch_create_mst a, wo_po_break_down b, wo_po_details_master c where  a.job_no=c.job_no  and b.job_no_mst=c.job_no and a.id='$ex_data[1]' and a.entry_form=136 and a.status_active=1 and a.is_deleted=0 group by c.buyer_name");
					$buyer_name = $result[0][csf('buyer_name')];
					$order_no = $result[0][csf('po_number')];
				}
				else 
				{
					if ($row[csf("batch_against")] == 3) 
					{
						if ($booking_without_order == 1)
						{
							$buyer_name = $sample_buyer_arr[$row[csf("booking_no")]];
						}
						else
						{
							//echo $row[csf("booking_no")];die;//FAL-SMN-23-00215
							$bookingNoArr=explode("-", $row[csf("booking_no")]);
							// echo "<pre>";print_r($bookingNoArr);die;
							if($bookingNoArr[1]=='SMN')
							{
								//echo $row[csf("booking_no")];die;
								$buyer_name = $sample_buyer_arr[$row[csf("booking_no")]];
							}
							else
							{
								$buyer_name = $buyer_arr[$row[csf("booking_no")]];
							}							
						}
					} 
					else 
					{
						if($row[csf("re_dyeing_from")] > 0)
						{
							$batch_against= return_field_value("batch_against","pro_batch_create_mst","id=".$row[csf("re_dyeing_from")]);
						}
						if($batch_against==3 && $booking_without_order==1 )
						{
							$buyer_name = $sample_buyer_arr[$row[csf("booking_no")]];
						}
						else
						{
							//echo $row[csf("booking_no")];die;//FAL-SMN-23-00215
							$bookingNoArr=explode("-", $row[csf("booking_no")]);
							// echo "<pre>";print_r($bookingNoArr);die;
							if($bookingNoArr[1]=='SMN')
							{
								//echo $row[csf("booking_no")];die;
								$buyer_name = $sample_buyer_arr[$row[csf("booking_no")]];
							}
							else
							{
								$buyer_name = $buyer_arr[$row[csf("booking_no")]];
							}							
						}
					}
				}
			}
			$order_no = implode(",", array_unique(explode(",", $order_no)));
			$tot_batch_wgtTrim=$row[csf("batch_weight")];//+$row[csf("total_trims_weight")]; comments by kausar
			/*if($row[csf("color_range_id")]==0)
			{
				echo "$('#cbo_color_range').removeAttr('disabled',true);\n";
			}
			else
			{
				echo "$('#cbo_color_range').attr('disabled',true);\n";
			}*/
			echo "$('#cbo_color_range').removeAttr('disabled',true);\n";
			echo "$('#cbo_double_dyeing').removeAttr('disabled',true);\n";

			echo "document.getElementById('cbo_order_source').value 		= '" . $batch_cat . "';\n";
			echo "document.getElementById('txt_batch_no').value 			= '" . $row[csf("batch_no")] . "';\n";
			// echo "document.getElementById('txt_machine_no').value 			= '" . $machine_arr[$row[csf("dyeing_machine")]] . "';\n";
			// echo "document.getElementById('txt_machine_id').value 		= '" . $row[csf("dyeing_machine")] . "';\n";
			echo "document.getElementById('cbo_machine_name').value 		= '" . $row[csf("dyeing_machine")] . "';\n";
			echo "document.getElementById('txt_batch_weight').value 		= '" . $tot_batch_wgtTrim . "';\n";
			echo "document.getElementById('txt_hidden_batch_weight').value 		= '" . $tot_batch_wgtTrim . "';\n";
			echo "document.getElementById('txt_booking_order').value 		= '" . $row[csf("booking_no")] . "';\n";
			echo "document.getElementById('txt_booking_id').value 			= '" . $row[csf("booking_no_id")] . "';\n";

			echo "document.getElementById('cbo_company_id').value 	= '" . $row[csf("company_id")] . "';\n";
			echo "$('#cbo_company_id').attr('disabled','disabled');\n";
			//echo "load_drop_down( 'requires/recipe_entry_controller', '" . $row[csf("company_id")] . "', 'load_drop_down_location', 'location_td' );\n";
			echo "load_drop_down( 'requires/recipe_entry_controller', '" . $row[csf("company_id")] . "', 'load_drop_down_buyer', 'buyer_td_id' );\n";

			echo "document.getElementById('cbo_buyer_name').value 			= '" . $buyer_name . "';\n";
			echo "document.getElementById('txt_color').value 				= '" . $color_arr[$row[csf("color_id")]] . "';\n";
			echo "document.getElementById('txt_color_id').value 			= '" . $row[csf("color_id")] . "';\n";
			echo "document.getElementById('cbo_color_range').value 			= '" . $row[csf("color_range_id")] . "';\n";
			echo "document.getElementById('cbo_double_dyeing').value 		= '" . $row[csf("double_dyeing")] . "';\n";
			echo "document.getElementById('txt_trims_weight').value 		= '" . $row[csf("total_trims_weight")] . "';\n";
			echo "document.getElementById('txt_order').value 				= '" . $order_no . "';\n";
			echo "document.getElementById('batch_type').innerHTML 			= '" . $batch_type . "';\n";
			if($batch_cat!=4)
			{
				echo "get_php_form_data('" . $po_id . "'+'**'+'" . $prod_id . "'+'**'+'" . $booking_no . "'+'**'+'" . $booking_without_order . "'+'**'+'" . $ex_data[1] . "'+'**'+'" . $row[csf("company_id")] . "', 'lode_data_from_grey_production', 'requires/recipe_entry_controller');\n";
			}
			echo "caculate_tot_liquor(1);\n";
		}
		exit();
	}

if ($action == "lode_data_from_grey_production")
{
	$ex_data = explode('**', $data);
	$po_id = str_replace("'", "", $ex_data[0]);
	$prod_id = str_replace("'", "", $ex_data[1]);
	$booking_no = str_replace("'", "", $ex_data[2]);
	$booking_without_order = str_replace("'", "", $ex_data[3]);
	$batch_id = str_replace("'", "", $ex_data[4]);
	$company_id = str_replace("'", "", $ex_data[5]);
	//echo $booking_no.'DDD';
	/*
	if($booking_without_order==1)
	{
		if ($db_type == 2)
		{
				$ordr_with="union
		Select listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(cast(a.brand_id as varchar2(4000)),',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from inv_receive_master  c,pro_grey_prod_entry_dtls a where  c.id=a.mst_id and c.booking_no='$booking_no'  and a.prod_id in ($prod_id) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(2,22)";
		}
		else
		{
				$ordr_with="union
		Select group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from inv_receive_master  c,pro_grey_prod_entry_dtls a where  c.id=a.mst_id and c.booking_no='$booking_no'  and a.prod_id in ($prod_id) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(2,22)";
		}
	}
	else
	{
		$ordr_with="";
	}
	*/

	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

	$roll_maintained = return_field_value("fabric_roll_level", "variable_settings_production", "company_name=$company_id and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");

	/*if ($db_type == 0) {
		$sql_prod = "Select group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($po_id) and b.prod_id in ($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22)
		$ordr_with
		";
	} else if ($db_type == 2) {
		$sql_prod = "Select listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(cast(a.brand_id as varchar2(4000)),',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($po_id) and b.prod_id in ($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22)
		$ordr_with ";
		//echo $sql_prod;
	}*/

	if ($roll_maintained==1)
	{
		if ($db_type==0)
		{
			$sql_prod="SELECT group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count) as yarn_count, group_concat(d.brand_id) as brand_id
			from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d
			where a.id=b.mst_id and b.barcode_no=c.barcode_no and c.dtls_id=d.id and a.company_id=$company_id and a.id in($batch_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and
			a.is_deleted=0 and c.entry_form in(2,22)";
		}
		else
		{
			// $sql_prod="SELECT LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot,
			// LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count,
			// LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id
			// from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d
			// where a.id=b.mst_id and b.barcode_no=c.barcode_no and c.dtls_id=d.id and a.company_id=$company_id and a.id in($batch_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and
			// a.is_deleted=0 and c.entry_form in(2,22)";
			$sql_prod="SELECT  d.yarn_lot as yarn_lot,
			 d.yarn_count as yarn_count,
			  d.brand_id as brand_id
			from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d
			where a.id=b.mst_id and b.barcode_no=c.barcode_no and c.dtls_id=d.id and a.company_id=$company_id and a.id in($batch_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and
			a.is_deleted=0 and c.entry_form in(2,22)";
		}
	}
	else
	{
		$sql_prod = "SELECT  d.yarn_lot as yarn_lot,d.yarn_count as yarn_count,d.brand_id as brand_id
		from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
		where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id
		and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22) group by  d.yarn_lot,d.yarn_count,d.brand_id";
	}

	$result_sql_prod = sql_select($sql_prod);
	$yarn_lot="";$all_brand_name="";$all_count_name="";
	foreach ($result_sql_prod as $row)
	{
		if($row[csf('yarn_lot')]!='' || $row[csf('brand_id')]!='' || $row[csf('yarn_count')]!='')
		{
			//$yarn_lot = implode(",", array_unique(explode(",", $row[csf('yarn_lot')])));
			//$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
			$yarn_lot =  $row[csf('yarn_lot')];
			$brand_id =  $row[csf('brand_id')];
			$yarn_count = $row[csf('yarn_count')];
			// $brand_name = "";
			// foreach ($brand_id as $val) {
			// 	if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= "," . $brand_arr[$val];
			// }

			//$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
			// $count_name = "";
			// foreach ($yarn_count as $val) {
			// 	if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= "," . $count_arr[$val];
			// }
			if($yarn_lot=="") $yarn_lot=$row[csf('yarn_lot')];else $yarn_lot.=",".$row[csf('yarn_lot')];
			if($all_brand_name=="") $all_brand_name=$brand_arr[$brand_id];else $all_brand_name.=",".$brand_arr[$brand_id];
			if($all_count_name=="") $all_count_name=$count_arr[$yarn_count];else $all_count_name.=",".$count_arr[$yarn_count];
		}
	}
	echo "document.getElementById('txt_yarn_lot').value 			= '" . implode(",", array_unique(explode(",", $yarn_lot))) . "';\n";
	echo "document.getElementById('txt_brand').value 				= '" . implode(",", array_unique(explode(",", $all_brand_name))) . "';\n";
	echo "document.getElementById('txt_count').value 				= '" . implode(",", array_unique(explode(",", $all_count_name))) . "';\n";
	exit();
}


if($action=="item_details")
{
	$data=explode("**",$data);
	$company_id=$data[0];
	$sub_process_id=$data[1];
	$update_id=$data[2];
	$store_id=$data[4];
	$variable_lot=$data[5];
	$from_lib_check_id=$data[6];
	$lab_mst_id=$data[7];
	$lab_check_id=$data[8];
	// echo $lab_check_id.'dsd';
	$sql_lot_variable = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $company_id and variable_list = 29 and is_deleted = 0 and status_active = 1");
	$lot_variable=$sql[0][csf("auto_transfer_rcv")];

	 
	//$sql_chk = sql_select("select is_control, id from variable_settings_production where company_name = $company_id and variable_list = 54 and is_deleted = 0 and status_active = 1");
	$sql_chk = sql_select("select id, variable_list, is_control, apply_for from variable_settings_production where company_name = $company_id and variable_list  in (54,78) and is_deleted = 0 and status_active = 1");
	
	 $labdipnofrom=0;
	foreach ($sql_chk as $row)
	{
		if($row[csf('variable_list')]==54) $is_control=$row[csf('is_control')];
		if($row[csf('variable_list')]==78) $labdipnofrom=$row[csf('apply_for')];
	}
	if($labdipnofrom==0 || $labdipnofrom=="") $variable_labdipnofrom=1; else $variable_labdipnofrom=$labdipnofrom;
	$stock_check=$is_control;
	if($stock_check==0 || $stock_check==2) $stock_check=0;else $stock_check=$stock_check;
	if($variable_labdipnofrom==2)
	{
		$chk_disable="";
	}
	else {  $chk_disable=" disabled='disabled'";}
	//print_r($colorref_prod_id_arr);
	if($lab_mst_id)
	{
		$lab_hideshow="type='checkbox'";
		$lab_msg="Remove lab data";
	}
	else{
		$lab_hideshow="type='hidden'";
		$lab_msg="";
	}


	$sql_prev_issue=sql_select("select b.RECIPE_ID from DYES_CHEM_ISSUE_DTLS b,INV_ISSUE_MASTER a where a.id=b.mst_id and a.entry_form=5 and a.company_id=$company_id and a.store_id=$store_id and b.sub_process=$sub_process_id and b.status_active=1 and b.RECIPE_ID is not null");
	$sql_prev_issue_ids=array();
	foreach($sql_prev_issue as $val)
	{
		$receipe_id_arr=explode(",",$val["RECIPE_ID"]);
		foreach($receipe_id_arr as $reci_id)
		{
			$sql_prev_issue_ids[$reci_id]=$reci_id;
		}
	}
	unset($sql_prev_issue);

	$recipe_sql="select a.ID, a.BATCH_QTY, b.TOTAL_LIQUOR, b.PROD_ID, b.DOSE_BASE, b.RATIO
	from PRO_RECIPE_ENTRY_MST a, PRO_RECIPE_ENTRY_DTLS b
	where a.id=b.MST_ID and a.status_active=1 and b.status_active=1 and a.COMPANY_ID=$company_id and b.STORE_ID=$store_id";
	$recipe_sql_result=sql_select($recipe_sql);
	$recipe_pipieline_data=array();
	foreach($recipe_sql_result as $val)
	{
		if($sql_prev_issue_ids[$val["ID"]]=="")
		{
			if($val["DOSE_BASE"]==1)
			{
				$recipe_qnty=(($val["TOTAL_LIQUOR"]*$val["RATIO"])/1000);
			}
			else
			{
				$recipe_qnty=(($val["BATCH_QTY"]*$val["RATIO"])/100);
			}
			$recipe_pipieline_data[$val["PROD_ID"]]["qnty"]+=$recipe_qnty;
			$recipe_pipieline_data[$val["PROD_ID"]]["rcp_id"].=$val["ID"].",";
		}
	}
	unset($recipe_sql_result);

	$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');

	$recipe_data_arr=array(); $recipe_prod_id_arr=array(); $product_data_arr=array();
	//echo $update_id.test;die;
	if($update_id!="")
	{	//sum(b.req_qny_edit) as qnty
		$iss_arr=return_library_array("select b.product_id, sum(b.req_qny_edit) as qnty from inv_issue_master a, dyes_chem_issue_dtls b, dyes_chem_requ_recipe_att c where a.req_no=c.mst_id and a.id=b.mst_id and a.entry_form=5 and a.issue_basis=7 and c.recipe_id=$update_id and b.sub_process=$sub_process_id group by b.product_id",'product_id','qnty');
		$sql_req_res=sql_select("select c.product_id, c.store_id, c.item_lot, sum(c.recipe_qnty) as qnty from dyes_chem_issue_requ_mst a,dyes_chem_requ_recipe_att b, dyes_chem_issue_requ_dtls c where  a.id=c.mst_id and c.mst_id=b.mst_id  and a.id=b.mst_id  and a.entry_form=156 and a.requisition_basis=8 and b.recipe_id=$update_id and c.sub_process=$sub_process_id group by c.product_id, c.store_id, c.item_lot");

		foreach($sql_req_res as $row)
		{
			if($variable_lot==1) $dyes_lot=$row[csf('item_lot')]; else $dyes_lot="";
			$prod_key=$row[csf('product_id')]."_".$row[csf('store_id')]."_".$dyes_lot;
			$iss_req_arr[$prod_key]=$row[csf('qnty')];
		}

		//	if($sub_process_id==93 || $sub_process_id==94 || $sub_process_id==95 || $sub_process_id==96 || $sub_process_id==97 || $sub_process_id==98 || $sub_process_id == 140 || $sub_process_id == 141 || $sub_process_id == 142 || $sub_process_id == 143 )
		if(in_array($sub_process_id,$subprocessForWashArr))
		{
			$ration_cond="";
		}
		else
		{
			$ration_cond=" and ratio>0 ";
		}
		$recipeData=sql_select("select prod_id, id, item_lot, comments, dose_base, ratio, seq_no, store_id, item_lot from pro_recipe_entry_dtls where mst_id=$update_id and sub_process_id=$sub_process_id and status_active=1 and is_deleted=0 $ration_cond order by seq_no");
		 
		foreach($recipeData as $row)
		{
			$recepi_prod_id[$row[csf('prod_id')]]=$row[csf('prod_id')];
			//$prod_key=$row[csf('prod_id')]."_".$row[csf('store_id')]."_".$row[csf('item_lot')];
			$prod_key=$row[csf('prod_id')]."_".$row[csf('store_id')];
			if(trim($row[csf('item_lot')])!="" && $variable_lot==1) $prod_key .="_".$row[csf('item_lot')];

			$recipe_data_arr[$prod_key][1]=$row[csf('item_lot')];
			$recipe_data_arr[$prod_key][2]=$row[csf('dose_base')];
			$recipe_data_arr[$prod_key][3]=$row[csf('ratio')];
			$recipe_data_arr[$prod_key][4]=$row[csf('seq_no')];
			$recipe_data_arr[$prod_key][5]=$row[csf('id')];
			$recipe_data_arr[$prod_key][6]=$row[csf('comments')];
			$recipe_prod_id_arr[]=$prod_key;
			$stock_check_data_arr[$prod_key]=$row[csf('prod_id')];
		}
		unset($recipeData);
	}
	//echo $variable_labdipnofrom.'='.$lab_check_id.'A';
	if($variable_labdipnofrom==2 && $lab_check_id==2) //From Lib ---
	{
		 
		$colorDataSql=sql_select("SELECT a.item_category_id,a.item_group_id,a.sub_group_name,a.item_description,a.item_size,a.unit_of_measure,c.cons_qty as store_stock, a.current_stock,a.lot,b.id, b.mst_id, b.prod_id, b.dose_base, b.ratio, b.item_lot, b.seq_no, b.remarks, b.store_id from lab_color_ingredients_dtls b,product_details_master a,inv_store_wise_qty_dtls c where a.id=b.prod_id and a.id=c.prod_id and b.prod_id=c.prod_id and c.STORE_ID=b.STORE_ID and b.mst_id=$lab_mst_id and a.COMPANY_ID=$company_id and c.STORE_ID=$store_id and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 order by b.seq_no asc");
	
			foreach($colorDataSql as $row)
			{
				
				$prod_key=$row[csf('prod_id')]."_".$row[csf('store_id')];
				if(trim($row[csf('item_lot')]) && $variable_lot==1) $prod_key .="_".$row[csf('item_lot')];
				$colorref_data_arr[$prod_key]['id']=$row[csf('id')];
				$colorref_data_arr[$prod_key]['item_lot']=$row[csf('item_lot')];
				$colorref_data_arr[$prod_key]['dose_base']=$row[csf('dose_base')];
				$colorref_data_arr[$prod_key]['ratio']=$row[csf('ratio')];
				$colorref_data_arr[$prod_key]['seq_no']=$row[csf('seq_no')];
				$colorref_data_arr[$prod_key]['remarks']=$row[csf('remarks')];
			//	echo $row[csf('prod_id')].'='.$row[csf('ratio')].'<br>';
				$colorref_prod_id_arr[$prod_key]=$prod_key;
				$color_prod_id_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];
				$product_data_arr[$prod_key]=$row[csf('item_category_id')]."**".$row[csf('item_group_id')]."**".$row[csf('sub_group_name')]."**".$row[csf('item_description')]."**".$row[csf('item_size')]."**".$row[csf('unit_of_measure')]."**".$row[csf('current_stock')]."**".$row[csf('store_stock')]."**".$row[csf('lot')]."**".$row[csf('first_receive_date')]."**".$row[csf('last_receive_date')]."**".$row[csf('ratio')]."**".$row[csf('seq_no')];
			}
			//echo "<pre>";
			//print_r($product_data_arr);
			$color_prod_id_not=implode(",",$color_prod_id_arr);
	}
	if($color_prod_id_not) $color_prod_id_not_cond="and a.id not in($color_prod_id_not)";else $color_prod_id_not_cond="";

	//echo "<pre>";print_r($recipe_data_arr);
	if($stock_check==1) $stock_check=" and a.current_stock>0 and b.cons_qty>0";else $stock_check="";
	$sql="select a.id,a.subprocess_id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.cons_qty as store_stock, b.lot, b.store_id,b.first_receive_date,b.last_receive_date
	from product_details_master a, inv_store_wise_qty_dtls b
	where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$store_id and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $stock_check";
	$sql.=" order by a.id,b.lot";
	//echo $sql;
	$nameArray=sql_select( $sql );
	$first_date_array=array();
	foreach($nameArray as $row)
	{
		// $first_date_array[$row[csf('lot')]];
		//$from_lib_check_id;
		//sub_process_id
		//echo $from_lib_check_id.'X';
		$prod_key=$row[csf('id')]."_".$row[csf('store_id')];
		if(trim($row[csf('lot')]) && $variable_lot==1) $prod_key .="_".$row[csf('lot')];
		$subprocess_Arr=array_unique(explode(",",$row[csf('subprocess_id')]));
		if($from_lib_check_id==1 && in_array($sub_process_id,$subprocess_Arr)) //CheckBox Checked //Lib-> Item Account Creation -Sub Process:: Issue Id=8876
		{
			//echo $from_lib_check_id.'d';
			if($colorref_prod_id_arr[$prod_key]=='')
			{
				$product_data_arr[$prod_key]=$row[csf('item_category_id')]."**".$row[csf('item_group_id')]."**".$row[csf('sub_group_name')]."**".$row[csf('item_description')]."**".$row[csf('item_size')]."**".$row[csf('unit_of_measure')]."**".$row[csf('current_stock')]."**".$row[csf('store_stock')]."**".$row[csf('lot')]."**".$row[csf('first_receive_date')]."**".$row[csf('last_receive_date')];
			}
			
		}
		if($from_lib_check_id==2) //When uncheck the box then data come as usual business
		{
			//echo "K";
		if($colorref_prod_id_arr[$prod_key]=='')
		{
			$product_data_arr[$prod_key]=$row[csf('item_category_id')]."**".$row[csf('item_group_id')]."**".$row[csf('sub_group_name')]."**".$row[csf('item_description')]."**".$row[csf('item_size')]."**".$row[csf('unit_of_measure')]."**".$row[csf('current_stock')]."**".$row[csf('store_stock')]."**".$row[csf('lot')]."**".$row[csf('first_receive_date')]."**".$row[csf('last_receive_date')];
		}
	  }

	}
	

//	echo "<pre>";print_r($Cproduct_data_arr);
	//echo "<pre>";print_r($product_data_arr); 
//echo $variable_lot.'SSSSSS';

	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" >
			<thead>
                <tr>
                    <th colspan="16"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?=$lab_msg;?> <input  <?=$lab_hideshow?> name="lab_check_id" id="lab_check_id" onClick="lib_check_labdip(1)" value="2" <?=$chk_disable?> ></th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="80">Item Category</th>
                    <th width="100">Item Group</th>
                    <th width="70">Sub Group</th>
                    <th width="130">Item Description</th>
                    <th width="80">Item Lot</th>
                    <th width="40">UOM</th>
                    <th width="70" class="must_entry_caption">Dose Base</th>
                    <th width="55" class="must_entry_caption">Ratio</th>
                    <th width="40" class="must_entry_caption">Seq. No</th>
                    <th width="100">Sub Process</th>
                    <th width="100">First Receive Date</th>
                    <th width="100">Last Receive Date</th>
                    <th width="50">Prod. ID</th>
                    <th width="70">Stock Qty</th>
                    <th width="">Comments</th>
                </tr>
			</thead>
		</table>
		<div style="width:1250px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1232" class="rpt_table" id="tbl_list_search" align="left">
				<tbody>
				<?
				//echo $variable_lot.'xxxxddd';
				//if($sub_process_id==93 || $sub_process_id==94 || $sub_process_id==95 || $sub_process_id==96 || $sub_process_id==97 || $sub_process_id==98 || $sub_process_id == 140 || $sub_process_id == 141 || $sub_process_id == 142 || $sub_process_id == 143 ) //Wash start...
				if(in_array($sub_process_id,$subprocessForWashArr))
				{
					// echo 'string';
					$i=1; //$max_seq_no='';
					//if(count($recipe_prod_id_arr)>0)
					//{
					//echo $sub_process_id.'dsdsdsdd';
					if($variable_lot==1)
					{
						$lot_popup='';
						$place_holder='';
					}
					else
					{
						$lot_popup='onDblClick="openmypage_itemLot('.$i.')"';
						$place_holder='Browse';
					}
					foreach($recipe_prod_id_arr as $prodId)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$prodData=explode("**",$product_data_arr[$prodId]);
						$item_category_id=$prodData[0];
						$item_group_id=$prodData[1];
						$sub_group_name=$prodData[2];
						$item_description=$prodData[3];
						$item_size=$prodData[4];
						$unit_of_measure=$prodData[5];
						$current_stock=$prodData[6];
						$store_stock=$prodData[7];
						$lot_no=$prodData[6];
						$first_date=$prodData[9];
						$last_date=$prodData[10];

						$item_lot=$recipe_data_arr[$prodId][1];
						$dtls_id=$recipe_data_arr[$prodId][5];
						//echo $dtls_id.'saa';
						$ratio=$recipe_data_arr[$prodId][3];
						$seq_no=$recipe_data_arr[$prodId][4];
						$comments=$recipe_data_arr[$prodId][6];
						$bgcolor="yellow";

						$selected_dose=$recipe_data_arr[$prodId][2];
						$disbled="";
						$prodIdArr=explode("_",$prodId);
						$prod_Id=$prodIdArr[0];
							 
						$iss_qty=$iss_arr[$prod_Id];
						$iss_req_qty=$iss_req_arr[$prod_Id];
						if($iss_req_qty=='' || $iss_req_qty==0) $iss_req_qty=0;else $iss_req_qty=$iss_req_qty;
						/*if(($update_id!="" && $ratio>0) && ($iss_qty>0 || $iss_req_qty>0) )
						{
							$disbled="disabled='disabled'";
						}*/

						if($iss_qty>0)  //Issue-Id=4748 as per cto/Saeed
						{
							$disbled="disabled='disabled'";
						}
						$prodId_ref=explode("_",$prodId);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
							<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
							<td width="80" id="item_category_<? echo $i; ?>"><p><? echo $item_category[$item_category_id]; ?></p></td>
							<td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
							<td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
							<td width="130" id="item_description_<? echo $i; ?>"><p><? echo $item_description." ".$item_size; ?></p></td>
							<td width="80" title="<? echo $item_lot; ?>" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]" id="txt_item_lot_<? echo $i; ?>" class="text_boxes" style="width:68px" <? echo $lot_popup; ?> placeholder="<? echo $place_holder; ?>" value="<? echo $item_lot; ?>" readonly>
							</td>
							<td width="40" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
							<td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-",$selected_dose); ?></td>
							<td width="50" align="center" id="ratio_<? echo $i; ?>" title="<? echo 'Issue Qty Found='.$iss_qty;?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;"  value="<? echo $ratio; ?>" onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); "  <? echo $disbled; ?>></td>
							<td width="40" align="center" id="seqno_<? echo $i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);"></td>
							<td  width="100" id="sub_process_<? echo $i; ?>"><p><? echo $dyeing_sub_process[$sub_process_id]; ?></p><input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $dtls_id; ?>"></td>
							<td  width="100" id="txt_first_date_<? echo $i; ?>"><p><? echo $first_date; ?></p><input type="hidden" name="txt_first_date" id="txt_first_date_<? echo $i; ?>" value="<? echo $first_date; ?>"></td>
							<td  width="100" id="txt_last_date_<? echo $i; ?>"><p><? echo $last_date; ?></p><input type="hidden" name="txt_last_date" id="txt_last_date_<? echo $i; ?>" value="<? echo $last_date; ?>"></td>
							<td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId_ref[0]; ?><input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" value="<? echo $prodId_ref[0]; ?>"></td>
							<td align="right" width="70" id="stock_qty_<? echo $i; ?>" title="<? echo "current stock=".$prodData[6]."store stock=".$store_stock  ?>"><? echo number_format($store_stock,2,'.',''); ?>
                            <input type="hidden" name="txt_rcp_pipeline_qnty[]" id="txt_rcp_pipeline_qnty_<? echo $i; ?>" title="<? echo chop($recipe_pipieline_data[$prodId_ref[0]]["rcp_id"],","); ?>" value="<? echo $recipe_pipieline_data[$prodId_ref[0]]["qnty"]; ?>"></td>
							<td width="" align="center" id="comments_<? echo $i; ?>"><input type="text" name="txt_comments[]" id="txt_comments_<? echo $i; ?>" class="text_boxes" style="width:80px" value="<? echo $comments; ?>"></td>
						</tr>
						<?
						//$max_seq_no[]=$selectResult[csf('seq_no')];
						$i++;
					}
					//}
				}
				else 				//Wash End....
				{
					// echo 'Wash End';
					$i=1; //$max_seq_no='';

					if(count($recipe_prod_id_arr)>0)
					{
						// echo $sub_process_id.'dsdsdsdd';

						foreach($recipe_prod_id_arr as $prodId)
						{
							if($variable_lot==1)
							{
								$lot_popup='';
								$place_holder='';
							}
							else
							{
								$lot_popup='onDblClick="openmypage_itemLot('.$i.')"';
								$place_holder='Browse';
							}
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							//echo $prodId."<br>";
							$prodData=explode("**",$product_data_arr[$prodId]);
							$item_category_id=$prodData[0];
							$item_group_id=$prodData[1];
							$sub_group_name=$prodData[2];
							$item_description=$prodData[3];
							$item_size=$prodData[4];
							$unit_of_measure=$prodData[5];
							$current_stock=$prodData[6];
							$store_stock=$prodData[7];
							$lot_no=$prodData[8];
							$first_date=$prodData[9];
							$last_date=$prodData[10];


							$item_lot=$recipe_data_arr[$prodId][1];
							$dtls_id=$recipe_data_arr[$prodId][5];
							$ratio=$recipe_data_arr[$prodId][3];
							$seq_no=$recipe_data_arr[$prodId][4];
							$comments=$recipe_data_arr[$prodId][6];
							$bgcolor="yellow";

							$selected_dose=$recipe_data_arr[$prodId][2];
							$disbled="";
							$prodIdArr=explode("_",$prodId);
							$prod_Id=$prodIdArr[0];
							$iss_qty=$iss_arr[$prod_Id];
						//	echo $iss_qty.'='.$prodId.'=A<br>';
							if($iss_qty=='' || $iss_qty==0) $iss_qty=0;else $iss_qty=$iss_qty;
							$iss_req_qty=$iss_req_arr[$prod_Id];
							if($iss_req_qty=='' || $iss_req_qty==0) $iss_req_qty=0;else $iss_req_qty=$iss_req_qty;
							//echo $update_id.'='.$ratio.'='.$iss_qty.'='.$iss_req_qty.'<br/>';
							/*if(($update_id!="" && $ratio>0) && ($iss_qty>0 || $iss_req_qty>0) )
							{

								$disbled="disabled='disabled'";
							}*/
							 
							if($iss_qty>0)  //Issue-Id=4748 as per cto/Saeed
							{

								$disbled="disabled='disabled'";
							}

							$current_stock_check=number_format($store_stock,7,'.','');
							$prodId_ref=explode("_",$prodId);
							//if($current_stock_check>0)
							//{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
									<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
									<td width="80" id="item_category_<? echo $i; ?>"><p><? echo $item_category[$item_category_id]; ?></p></td>
									<td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
									<td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
									<td width="130" title="<?=$prodId."=".$item_description."=".$item_size;?>" id="item_description_<? echo $i; ?>"><p><? echo $item_description." ".$item_size; ?></p></td>
									<td width="80" title="<? echo $item_lot; ?>" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]" id="txt_item_lot_<? echo $i; ?>" class="text_boxes" style="width:68px" <? echo $lot_popup; ?> placeholder="<? echo $place_holder; ?>" value="<? echo $item_lot; ?>" readonly>
									</td>
									<td width="40" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
									<td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-",$selected_dose); ?></td>
									<td width="50" align="center" title="<? echo 'Issue Qty Found='.$iss_qty;?>" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;"  value="<? echo $ratio; ?>" onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); "  <? echo $disbled; ?>></td>
									<td width="40" align="center" id="seqno_<? echo $i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);"></td>
									<td  width="100" id="sub_process_<? echo $i; ?>"><p><? echo $dyeing_sub_process[$sub_process_id]; ?></p><input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $dtls_id; ?>"></td>
									<td  width="100" id="txt_first_date_<? echo $i; ?>"><p><? echo $first_date; ?></p><input type="hidden" name="txt_first_date" id="txt_first_date_<? echo $i; ?>" value="<? echo $first_date; ?>"></td>
									<td  width="100" id="txt_last_date_<? echo $i; ?>"><p><? echo $last_date; ?></p><input type="hidden" name="txt_last_date" id="txt_last_date_<? echo $i; ?>" value="<? echo $last_date; ?>"></td>
									<td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId_ref[0]; ?><input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" value="<? echo $prodId_ref[0]; ?>"></td>
									<td align="right" width="70" title="<? echo 'Stock Qty Allowed 12 Digit after decimal='.$store_stock; ?>" id="stock_qty_<? echo $i; ?>"><? echo number_format($store_stock,2,'.',''); ?>
                                    <input type="hidden" name="txt_rcp_pipeline_qnty[]" id="txt_rcp_pipeline_qnty_<? echo $i; ?>" title="<? echo chop($recipe_pipieline_data[$prodId_ref[0]]["rcp_id"],","); ?>" value="<? echo $recipe_pipieline_data[$prodId_ref[0]]["qnty"]; ?>"></td>
									<td width="" align="center" id="comments_<? echo $i; ?>"><input type="text" name="txt_comments[]" id="txt_comments_<? echo $i; ?>" class="text_boxes" style="width:80px" value="<? echo $comments; ?>"></td>
								</tr>
								<?
								//$max_seq_no[]=$selectResult[csf('seq_no')];
								$i++;
							//}
						}
					}
					// echo 'checking';die;
					foreach($product_data_arr as $prodId=>$data)  // Save here
					{
						// echo 'Save here';
						if(!in_array($prodId,$recipe_prod_id_arr))
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							if($variable_lot==1)
							{
								$lot_popup='';
								$place_holder='';
							}
							else
							{
								$lot_popup='onDblClick="openmypage_itemLot('.$i.')"';
								$place_holder='Browse';
							}

							$prodData=explode("**",$data);
							$item_category_id=$prodData[0];
							$item_group_id=$prodData[1];
							$sub_group_name=$prodData[2];
							$item_description=$prodData[3];
							$item_size=$prodData[4];
							$unit_of_measure=$prodData[5];
							$current_stock=$prodData[6];
							$store_stock=$prodData[7];
							$lot_no=$prodData[8];
							$first_date=$prodData[9];
							$last_date=$prodData[10];
							$ratio=$prodData[11];
							$seq_no=$prodData[12];
							

							/*if($selectResult[csf('ratio')]>0)
							{
								$ratio=$selectResult[csf('ratio')];
								$seq_no=$selectResult[csf('seq_no')];
								$bgcolor="yellow";
							}
							else
							{
								$ratio='';
								$seq_no='';
								$bgcolor=$bgcolor;
							}

							if($selectResult[csf('dtls_id')]=="")
							{
								if($item_category_id==6)
								{
									$selected_dose=2;
								}
								else
								{
									$selected_dose=1;
								}
							}
							else
							{
								$selected_dose=$selectResult[csf('dose_base')];
							}

							$disbled="";
							$iss_qty=$iss_arr[$selectResult[csf('id')]];
							if($update_id!="" && $ratio>0 && $iss_qty>0)
							{
								$disbled="disabled='disabled'";
							}*/

							//$ratio=''; $seq_no='';
							 $disbled="";$comments='';
							if($item_category_id==6)
							{
								$selected_dose=2;
							}
							else
							{
								$selected_dose=1;
							}
							if($ratio)  {$lab_from_color="#FF3";$lab_ttl="From Lab Dip";}else { $lab_from_color="";$lab_ttl="";}
						
							$td_color="";
							if($store_stock<=0) $td_color="#FF0000"; else $td_color="";
							$current_stock_check=number_format($store_stock,2,'.','');
							$prodId_ref=explode("_",$prodId);
						 	//if($prodId==4060) echo "A=".$ratio.'='.$seq_no;
							if($current_stock_check>0)
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
									<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
									<td width="80" id="item_category_<? echo $i; ?>"><p><? echo $item_category[$item_category_id]; ?></p></td>
									<td width="100" bgcolor="<? echo $td_color;?>" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
									<td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
									<td width="130" title="<?=$item_description."=".$item_size;?>" id="item_description_<? echo $i; ?>"><p><? echo $item_description." ".$item_size; ?></p></td>
									<td width="80" title="<? echo $lot_no; ?>" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]" id="txt_item_lot_<? echo $i; ?>" class="text_boxes" style="width:68px" <? echo $lot_popup; ?> placeholder="<? echo $place_holder; ?>" value="<? echo $lot_no; ?>" readonly>
									</td>
									<td width="40" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
									<td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-",$selected_dose); ?></td>
									<td width="50" align="center" id="ratio_<? echo $i; ?>" title="<?=$lab_ttl;?>">
                                    <input type="text"  name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px; background-color:<?=$lab_from_color;?>"  value="<? echo $ratio; ?>" onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); "  <? echo $disbled; ?>></td>
									<td width="40" align="center" id="seqno_<? echo $i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px; background-color:<?=$lab_from_color;?>" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);"></td>
									<td  width="100" id="sub_process_<? echo $i; ?>"><p><? echo $dyeing_sub_process[$sub_process_id]; ?></p><input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? //echo $dtls_id; ?>"></td>
									<td  width="100" id="txt_first_date_<? echo $i; ?>"><p><? echo $first_date; ?></p><input type="hidden" name="txt_first_date" id="txt_first_date_<? echo $i; ?>" value="<? echo $first_date; ?>"></td>
									<td  width="100" id="txt_last_date_<? echo $i; ?>"><p><? echo $last_date; ?></p><input type="hidden" name="txt_last_date" id="txt_last_date_<? echo $i; ?>" value="<? echo $last_date; ?>"></td>
									<td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId_ref[0]; ?><input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" value="<? echo $prodId_ref[0]; ?>"></td>
									<td align="right" width="70" title="<? echo "After decimal 2 digit allowed,current stock=".$prodData[6]."store stock=".$store_stock  ?>" id="stock_qty_<? echo $i; ?>"><? echo number_format($store_stock,2,'.',''); ?>
                                    <input type="hidden" name="txt_rcp_pipeline_qnty[]" id="txt_rcp_pipeline_qnty_<? echo $i; ?>" title="<? echo chop($recipe_pipieline_data[$prodId_ref[0]]["rcp_id"],","); ?>" value="<? echo $recipe_pipieline_data[$prodId_ref[0]]["qnty"]; ?>"></td>
									<td width="" align="center" id="comments_<? echo $i; ?>"><input type="text" name="txt_comments[]" id="txt_comments_<? echo $i; ?>" class="text_boxes" style="width:80px" value="<? echo $comments; ?>"></td>
								</tr>
								<?
								//$max_seq_no[]=$selectResult[csf('seq_no')];
								$i++;
							}
						}
					}
				}
				?>
			</tbody>
		</table>
	</div>
</div>
<?
exit();
}


if($action=="populate_data_lib_data")
{
	// lots_variable
	$sql = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $data and variable_list = 29 and is_deleted = 0 and status_active = 1");

	// Report setting
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=7 and report_id=243 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(",",$print_report_format);
	echo $sql[0][csf("auto_transfer_rcv")].'*'.$print_report_format;
	exit();
}

if($action=="populate_stock_data")
{
	$sql_chk = sql_select("select id, variable_list, is_control, apply_for from variable_settings_production where company_name = $data and variable_list  in (54,78) and is_deleted = 0 and status_active = 1");
	
	$is_control=2; $labdipnofrom=0;
	foreach ($sql_chk as $row)
	{
		if($row[csf('variable_list')]==54) $is_control=$row[csf('is_control')];
		if($row[csf('variable_list')]==78) $labdipnofrom=$row[csf('apply_for')];
	}
	
	if($is_control==0 || $is_control==2) $chk_stock=2;else  $chk_stock=$is_control;
	echo "document.getElementById('variable_stock').value 			= '" . $chk_stock . "';\n";
	
		if($labdipnofrom==0 || $labdipnofrom=="") $variable_labdipnofrom=1; else $variable_labdipnofrom=$labdipnofrom;
	echo "document.getElementById('variable_labdipno').value 		= '" . $variable_labdipnofrom . "';\n";

	exit();
}

if($action=="populate_recipe_data")
{
	$sql_chk = sql_select("select id, variable_list, production_entry, apply_for from variable_settings_production where company_name = $data and variable_list=59 and is_deleted = 0 and status_active = 1");
	//echo $sql[0][csf("is_control")];
	
	if($sql_chk[0][csf("production_entry")]==0 || $sql_chk[0][csf("production_entry")]==2) $chk_recipe=2; else $chk_recipe=$sql_chk[0][csf("production_entry")];
	echo "document.getElementById('variable_recipe').value 			= '" . $chk_recipe . "';\n";
	
	exit();
}

if($action=="labdipno_popup")
{
	echo load_html_head_contents("Lab Dip No Tag popup","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( strval )
		{
			document.getElementById('selected_id').value=strval;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <?
	$ingrediants_arr=array();
	$data_ingradients = sql_select("select color_ref_id, panton from lab_color_ingredients_mst where status_active=1 and is_deleted=0");
	foreach($data_ingradients as $row)
	{
		$ingrediants_arr[$row[csf("color_ref_id")]]=$row[csf("panton")];
	}
	unset($data_ingradients);
	
	$colorRef_arr=array();
	$dataColorRef=sql_select("select id, shade_brightness, dye_type, color_ref from lab_color_reference where status_active=1 and is_deleted=0");
	foreach($dataColorRef as $row)
	{
		$colorRef_arr[$row[csf("id")]]=$row[csf("color_ref")];
	}
	unset($dataColorRef);
	
//	$sqlLabData="select id, buyer_id, request_no, colorref_id, po_id, color_id, labdip_id, lab_company_id from lab_labdip_request where company_id='$cbo_company_name' and status_active=1 and is_deleted=0 order by id desc";
	 $sqlLabData="select id, company_id,section_id, color_ref_id,client_id,panton,sys_no,sample_no,correction from lab_color_ingredients_mst where company_id='$cbo_company_name' and status_active=1 and is_deleted=0 order by id desc";
	//echo $sqlSaveData; die;
	$sqlLabData_res=sql_select($sqlLabData);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	?>
	
	<table width="650" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table " >
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="120">Buyer</th>
                <th width="100">Lab Dip No.</th>
                <th width="150">Color Ref.</th>
				<th width="60">Correction</th>
				<th width="40">S.N:</th>
                <th>Pantone No<input type="hidden" id="selected_id"></th>
            </tr>
        </thead>
        <tbody id="list_view">
        <?
        $i=1;
		foreach($sqlLabData_res as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')].'_'.$row[csf('sys_no')]; ?>')">
				<td width="30"><? echo $i; ?></td>
				<td width="120" style="word-break:break-all"><? echo $buyer_arr[$row[csf('client_id')]]; ?></td>
				<td width="100" style="word-break:break-all"><? echo $row[csf('sys_no')]; ?></td>
				
				<td width="150" style="word-break:break-all"><? echo $colorRef_arr[$row[csf("color_ref_id")]]; ?></td>
				<td width="60" style="word-break:break-all"><? echo $row[csf('correction')]; ?></td>
				<td width="40" style="word-break:break-all"><? echo $dyeinglab_dyecode_arr[$row[csf('sample_no')]]; ?></td>
				<td><? echo $row[csf("panton")]; ?></td>
			</tr>
			<?
			$i++;
		}
        ?>
        </tbody>
	</table>
    <script>setFilterGrid('list_view',-1);</script>
	<?
	exit();
}

if ($action == "item_details_old") {
	$data = explode("**", $data);
	$company_id = $data[0];
	$sub_process_id = $data[1];
	$update_id = $data[2];
	$copy_val = $data[3];
	$product_ids = "";

	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');

	$recipe_data_arr = array();
	$recipe_prod_id_arr = array();
	$product_data_arr = array();
	if ($sub_process_id == 93 || $sub_process_id == 94 || $sub_process_id == 95 || $sub_process_id == 96 || $sub_process_id == 97 || $sub_process_id == 98 || $sub_process_id == 140 || $sub_process_id == 141 || $sub_process_id == 142 || $sub_process_id == 143 ) {
		$ration_cond = "";
	} else {
		$ration_cond = " and ratio>0 ";
	}
	if ($update_id != "") {    //sum(b.req_qny_edit) as qnty
		$iss_arr = return_library_array("select b.product_id, sum(b.required_qnty) as qnty from inv_issue_master a, dyes_chem_issue_dtls b, dyes_chem_requ_recipe_att c where a.req_no=c.mst_id and a.id=b.mst_id and a.entry_form=5 and a.issue_basis=7 and c.recipe_id=$update_id and b.sub_process=$sub_process_id group by b.product_id", 'product_id', 'qnty');

		//echo "select prod_id, id, item_lot,comments,dose_base, ratio, seq_no from pro_recipe_entry_dtls where mst_id=$update_id and sub_process_id=$sub_process_id and status_active=1 and is_deleted=0 and ratio>0 order by seq_no";


		//echo "select prod_id, id, item_lot,comments,dose_base, ratio, seq_no from pro_recipe_entry_dtls where mst_id=$update_id and sub_process_id=$sub_process_id and status_active=1 and is_deleted=0 $ration_cond order by seq_no";
		$recipeData = sql_select("select prod_id, id, item_lot,comments,dose_base, ratio, seq_no from pro_recipe_entry_dtls where mst_id=$update_id and sub_process_id=$sub_process_id and status_active=1 and is_deleted=0 $ration_cond order by seq_no");

		foreach ($recipeData as $row) {
			$recipe_data_arr[$row[csf('prod_id')]][1] = $row[csf('item_lot')];
			$recipe_data_arr[$row[csf('prod_id')]][2] = $row[csf('dose_base')];
			$recipe_data_arr[$row[csf('prod_id')]][3] = $row[csf('ratio')];
			$recipe_data_arr[$row[csf('prod_id')]][4] = $row[csf('seq_no')];
			$recipe_data_arr[$row[csf('prod_id')]][5] = $row[csf('id')];
			$recipe_data_arr[$row[csf('prod_id')]][6] = $row[csf('comments')];

			$recipe_prod_id_arr[] = $row[csf('prod_id')];
			//$product_ids .= $row[csf('prod_id')] . ",";
		}
	}
	$product_ids = implode(",", $recipe_prod_id_arr);
	//var_dump($recipe_prod_id_arr);
	/*if($db_type==2)
	{
		if($update_id=="")
		{
			$sql="select id, item_category_id, item_group_id, sub_group_name, item_description, item_size, unit_of_measure, current_stock from product_details_master where company_id='$company_id' and item_category_id in(5,6,7) and status_active=1 and is_deleted=0 order by upper(item_description)";
		}
		else
		{
			$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.id as dtls_id, b.mst_id, b.item_lot, b.dose_base, b.ratio, b.seq_no from product_details_master a left join pro_recipe_entry_dtls b on a.id=b.prod_id and b.mst_id=$update_id and b.sub_process_id=$sub_process_id and b.status_active=1 and b.is_deleted=0 and b.ratio>0 where a.company_id='$company_id' and a.item_category_id in (5,6,7) and a.status_active=1 and a.is_deleted=0 order by b.seq_no, b.id";
		}
	}
	else if($db_type==0)
	{
		if($update_id=="")
		{
			$sql="select id, item_category_id, item_group_id, sub_group_name, item_description, item_size, unit_of_measure, current_stock from product_details_master where company_id='$company_id' and item_category_id in(5,6,7) and status_active=1 and is_deleted=0 order by item_description";
		}
		else
		{
			$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.id as dtls_id, b.mst_id, b.item_lot, b.dose_base, b.ratio, b.seq_no from product_details_master a left join pro_recipe_entry_dtls b on a.id=b.prod_id and b.mst_id=$update_id and b.sub_process_id=$sub_process_id and b.status_active=1 and b.is_deleted=0 and b.ratio>0 where a.company_id='$company_id' and a.item_category_id in (5,6,7) and a.status_active=1 and a.is_deleted=0 order by b.seq_no DESC";
		}
	}*/

	//$sql = "select id, item_category_id, item_group_id, sub_group_name, item_description, item_size, unit_of_measure, current_stock from product_details_master where company_id='$company_id' and item_category_id in(5,6,7) and status_active=1 and is_deleted=0";

	$sql = "select id, item_category_id, item_group_id, sub_group_name, item_description, item_size, unit_of_measure, current_stock
	from product_details_master
	where company_id='$company_id'
	and item_category_id in(5,6,7,23) and status_active=1 and is_deleted=0 and id not in($product_ids)";
	//echo $sql;

	$nameArray = sql_select($sql);
	foreach ($nameArray as $row) {
		$product_data_arr[$row[csf('id')]] = $row[csf('item_category_id')] . "**" . $row[csf('item_group_id')] . "**" . $row[csf('sub_group_name')] . "**" . $row[csf('item_description')] . "**" . $row[csf('item_size')] . "**" . $row[csf('unit_of_measure')] . "**" . $row[csf('current_stock')];
	}

	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="80">Item Category</th>
				<th width="100">Item Group</th>
				<th width="70">Sub Group</th>
				<th width="130">Item Description</th>
				<th width="80">Item Lot</th>
				<th width="40">UOM</th>
				<th width="70" class="must_entry_caption">Dose Base</th>
				<th width="55" class="must_entry_caption">Ratio</th>
				<th width="40" class="must_entry_caption">Seq. No</th>
				<th width="100">Sub Process</th>
				<th width="50">Prod. ID</th>
				<th width="70">Stock Qty</th>
				<th width="">Comments</th>
			</thead>
		</table>
		<div style="width:1050px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1032" class="rpt_table"
			id="tbl_list_search">
			<tbody>
				<?
				if ($sub_process_id == 93 || $sub_process_id == 94 || $sub_process_id == 95 || $sub_process_id == 96 || $sub_process_id == 97 || $sub_process_id == 98 || $sub_process_id == 140 || $sub_process_id == 141 || $sub_process_id == 142 || $sub_process_id == 143 ) //Wash start...
				{
					$i = 1; //$max_seq_no='';
					//if(count($recipe_prod_id_arr)>0)
					//{
					//echo $sub_process_id.'dsdsdsdd';

					foreach ($recipe_prod_id_arr as $prodId) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						$prodData = explode("**", $product_data_arr[$prodId]);
						$item_category_id = $prodData[0];
						$item_group_id = $prodData[1];
						$sub_group_name = $prodData[2];
						$item_description = $prodData[3];
						$item_size = $prodData[4];
						$unit_of_measure = $prodData[5];
						$current_stock = $prodData[6];

						$item_lot = $recipe_data_arr[$prodId][1];
						$dtls_id = $recipe_data_arr[$prodId][5];
						//echo $dtls_id.'saa';
						$ratio = $recipe_data_arr[$prodId][3];
						$seq_no = $recipe_data_arr[$prodId][4];
						$comments = $recipe_data_arr[$prodId][6];
						$bgcolor = "yellow";

						$selected_dose = $recipe_data_arr[$prodId][2];
						$disbled = "";
						$iss_qty = $iss_arr[$prodId];
						if ($update_id != "" && $ratio > 0 && $iss_qty > 0) {
							$disbled = "disabled='disabled'";
						}
						if ($copy_val == 1 && $current_stock <= 0) {
							$fontcolor = 'style="color:#FF0000"';
						} else {
							$fontcolor = '';
						}
						?>
						<tr <? echo $fontcolor; ?> bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
							<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
							<td width="80" id="item_category_<? echo $i; ?>">
								<p><? echo $item_category[$item_category_id]; ?></p></td>
								<td width="100" id="item_group_id_<? echo $i; ?>">
									<p><? echo $item_group_arr[$item_group_id]; ?></p></td>
									<td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p>
									</td>
									<td width="130" id="item_description_<? echo $i; ?>">
										<p><? echo $item_description . " " . $item_size; ?></p></td>
										<td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]"
											id="txt_item_lot_<? echo $i; ?>"
											class="text_boxes" style="width:68px"
											onDblClick="openmypage_itemLot(<? echo $i; ?>)"
											placeholder="Browse"
											value="<? echo $item_lot; ?>">
										</td>
										<td width="40" align="center"
										id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
										<td width="70" align="center"
										id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-", $selected_dose); ?></td>
										<td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text"
											name="txt_ratio[]"
											id="txt_ratio_<? echo $i; ?>"
											class="text_boxes_numeric"
											style="width:40px;"
											value="<? echo $ratio; ?>"
											onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); " <? echo $disbled; ?>>
										</td>
										<td width="40" align="center" id="seqno_<? echo $i; ?>"><input type="text"
											name="txt_seqno[]"
											id="txt_seqno_<? echo $i; ?>"
											class="text_boxes_numeric"
											style="width:30px"
											value="<? echo $seq_no; ?>"
											onBlur="row_sequence(<? echo $i; ?>);">
										</td>
										<td width="100" id="sub_process_<? echo $i; ?>">
											<p><? echo $dyeing_sub_process[$sub_process_id]; ?></p><input type="hidden"
											name="updateIdDtls[]"
											id="updateIdDtls_<? echo $i; ?>"
											value="<? echo $dtls_id; ?>">
										</td>
										<td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId; ?><input
											type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>"
											value="<? echo $prodId; ?>"></td>
											<td align="right" width="70" title="<? echo $copy_val; ?>"
												id="stock_qty_<? echo $i; ?>"><? echo number_format($current_stock, 2, '.', ''); ?></td>
												<td width="" align="center" id="comments_<? echo $i; ?>"><input type="text"
													name="txt_comments[]"
													id="txt_comments_<? echo $i; ?>"
													class="text_boxes"
													style="width:80px"
													value="<? echo $comments; ?>">
												</td>
											</tr>
											<?
						//$max_seq_no[]=$selectResult[csf('seq_no')];
											$i++;
										}
					//}

				} else                //Wash End....
				{
					$i = 1; //$max_seq_no='';
					if (count($recipe_prod_id_arr) > 0) {
						//echo $sub_process_id.'dsdsdsdd';
						//echo "<pre>";
						//print_r($recipe_prod_id_arr); die;
						foreach ($recipe_prod_id_arr as $prodId) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

							$prodData = explode("**", $product_data_arr[$prodId]);
							$item_category_id = $prodData[0];
							$item_group_id = $prodData[1];
							$sub_group_name = $prodData[2];
							$item_description = $prodData[3];
							$item_size = $prodData[4];
							$unit_of_measure = $prodData[5];
							$current_stock = $prodData[6];

							$item_lot = $recipe_data_arr[$prodId][1];
							$dtls_id = $recipe_data_arr[$prodId][5];
							$ratio = $recipe_data_arr[$prodId][3];
							$seq_no = $recipe_data_arr[$prodId][4];
							$comments = $recipe_data_arr[$prodId][6];
							$bgcolor = "yellow";

							$selected_dose = $recipe_data_arr[$prodId][2];
							$disbled = "";
							$iss_qty = $iss_arr[$prodId];
							if ($update_id != "" && $ratio > 0 && $iss_qty > 0) {
								$disbled = "disabled='disabled'";
							}
							if ($copy_val == 1 && $current_stock <= 0) {
								$fontcolor = 'style="color:#FF0000"';
							} else {
								$fontcolor = "";
							}
							?>
							<tr <? echo $fontcolor; ?> bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
								<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
								<td width="80" id="item_category_<? echo $i; ?>">
									<p><? echo $item_category[$item_category_id]; ?></p></td>
									<td width="100" id="item_group_id_<? echo $i; ?>">
										<p><? echo $item_group_arr[$item_group_id]; ?></p></td>
										<td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>
									&nbsp;</p></td>
									<td width="130" id="item_description_<? echo $i; ?>">
										<p><? echo $item_description . " " . $item_size; ?></p></td>
										<td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]"
											id="txt_item_lot_<? echo $i; ?>"
											class="text_boxes" style="width:68px"
											onDblClick="openmypage_itemLot(<? echo $i; ?>)"
											placeholder="Browse"
											value="<? echo $item_lot; ?>">
										</td>
										<td width="40" align="center"
										id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>
									&nbsp;</td>
									<td width="70" align="center"
									id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-", $selected_dose); ?></td>
									<td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text"
										name="txt_ratio[]"
										id="txt_ratio_<? echo $i; ?>"
										class="text_boxes_numeric"
										style="width:40px;"
										value="<? echo $ratio; ?>"
										onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); " <? echo $disbled; ?>>
									</td>
									<td width="40" align="center" id="seqno_<? echo $i; ?>"><input type="text"
										name="txt_seqno[]"
										id="txt_seqno_<? echo $i; ?>"
										class="text_boxes_numeric"
										style="width:30px"
										value="<? echo $seq_no; ?>"
										onBlur="row_sequence(<? echo $i; ?>);">
									</td>
									<td width="100" id="sub_process_<? echo $i; ?>">
										<p><? echo $dyeing_sub_process[$sub_process_id]; ?></p><input type="hidden"
										name="updateIdDtls[]"
										id="updateIdDtls_<? echo $i; ?>"
										value="<? echo $dtls_id; ?>">
									</td>
									<td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId; ?><input
										type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>"
										value="<? echo $prodId; ?>"></td>
										<td align="right" width="70" title="<? echo $copy_val; ?>"
											id="stock_qty_<? echo $i; ?>"><? echo number_format($current_stock, 2, '.', ''); ?></td>
											<td width="" align="center" id="comments_<? echo $i; ?>"><input type="text"
												name="txt_comments[]"
												id="txt_comments_<? echo $i; ?>"
												class="text_boxes"
												style="width:80px"
												value="<? echo $comments; ?>">
											</td>
										</tr>
										<?
							//$max_seq_no[]=$selectResult[csf('seq_no')];
										$i++;
									}
								}

								foreach ($product_data_arr as $prodId => $data) {
									if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

									$prodData = explode("**", $data);
									$item_category_id = $prodData[0];
									$item_group_id = $prodData[1];
									$sub_group_name = $prodData[2];
									$item_description = $prodData[3];
									$item_size = $prodData[4];
									$unit_of_measure = $prodData[5];
									$current_stock = $prodData[6];

						/*if($selectResult[csf('ratio')]>0)
						{
							$ratio=$selectResult[csf('ratio')];
							$seq_no=$selectResult[csf('seq_no')];
							$bgcolor="yellow";
						}
						else
						{
							$ratio='';
							$seq_no='';
							$bgcolor=$bgcolor;
						}

						if($selectResult[csf('dtls_id')]=="")
						{
							if($item_category_id==6)
							{
								$selected_dose=2;
							}
							else
							{
								$selected_dose=1;
							}
						}
						else
						{
							$selected_dose=$selectResult[csf('dose_base')];
						}

						$disbled="";
						$iss_qty=$iss_arr[$selectResult[csf('id')]];
						if($update_id!="" && $ratio>0 && $iss_qty>0)
						{
							$disbled="disabled='disabled'";
						}*/

						$ratio = '';
						$seq_no = '';
						$disbled = "";
						$comments = '';
						if ($item_category_id == 6) {
							$selected_dose = 2;
						} else {
							$selected_dose = 1;
						}

						//$td_color="";
						//if($selectResult[csf('current_stock')]<=0) $td_color="#FF0000"; else $td_color="";
						if ($copy_val == 1 && $current_stock <= 0) {
							$fontcolor = 'style="color:#FF0000"';
						} else {
							$fontcolor = "";
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
							<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
							<td width="80" id="item_category_<? echo $i; ?>">
								<p><? echo $item_category[$item_category_id]; ?></p></td>
								<td width="100" id="item_group_id_<? echo $i; ?>">
									<p><? echo $item_group_arr[$item_group_id]; ?></p></td>
									<td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>
								&nbsp;</p></td>
								<td width="130" id="item_description_<? echo $i; ?>">
									<p><? echo $item_description . " " . $item_size; ?></p></td>
									<td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]"
										id="txt_item_lot_<? echo $i; ?>"
										class="text_boxes" style="width:68px"
										onDblClick="openmypage_itemLot(<? echo $i; ?>)"
										placeholder="Browse"
										value="<? echo $selectResult[csf('item_lot')]; ?>">
									</td>
									<td width="40" align="center"
									id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>
								&nbsp;</td>
								<td width="70" align="center"
								id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-", $selected_dose); ?></td>
								<td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text"
									name="txt_ratio[]"
									id="txt_ratio_<? echo $i; ?>"
									class="text_boxes_numeric"
									style="width:40px;"
									value="<? echo $ratio; ?>"
									onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); " <? echo $disbled; ?>>
								</td>
								<td width="40" align="center" id="seqno_<? echo $i; ?>"><input type="text"
									name="txt_seqno[]"
									id="txt_seqno_<? echo $i; ?>"
									class="text_boxes_numeric"
									style="width:30px"
									value="<? echo $seq_no; ?>"
									onBlur="row_sequence(<? echo $i; ?>);">
								</td>
								<td width="100" id="sub_process_<? echo $i; ?>">
									<p><? echo $dyeing_sub_process[$sub_process_id]; ?></p><input type="hidden"
									name="updateIdDtls[]"
									id="updateIdDtls_<? echo $i; ?>"
                                                                                              value="<? //echo $dtls_id;
                                                                                              ?>"></td>
                                                                                              <td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId; ?><input
                                                                                              	type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>"
                                                                                              	value="<? echo $prodId; ?>"></td>
                                                                                              	<td align="right" width="70" title="<? echo $copy_val; ?>"
                                                                                              		id="stock_qty_<? echo $i; ?>"><? echo number_format($current_stock, 2, '.', ''); ?></td>
                                                                                              		<td width="" align="center" id="comments_<? echo $i; ?>"><input type="text"
                                                                                              			name="txt_comments[]"
                                                                                              			id="txt_comments_<? echo $i; ?>"
                                                                                              			class="text_boxes"
                                                                                              			style="width:80px"
                                                                                              			value="<? echo $comments; ?>">
                                                                                              		</td>
                                                                                              	</tr>
                                                                                              	<?
						//$max_seq_no[]=$selectResult[csf('seq_no')];
                                                                                              	$i++;
                                                                                              }
                                                                                          }
                                                                                          ?>
                                                                                      </tbody>
                                                                                  </table>
                                                                              </div>
                                                                          </div>
                                                                          <?
                                                                          exit();
                                                                      }

if ($action == "itemLot_popup")
{
	echo load_html_head_contents("Item Lot Info", "../../", 1, 1, '', 1, '');
	extract($_REQUEST);
	?>
	<script>
	//var selected_id = new Array, selected_name = new Array();
//	selected_attach_id = new Array();
//
//	function toggle(x, origColor) {
//	var newColor = 'yellow';
//	if (x.style) {
//	x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
//	}
//	}
//
//	function js_set_value(id) {
//	var str = id.split("_");
//	toggle(document.getElementById('tr_' + str[0]), '#FFFFFF');
//	var strdt = str[2];
//	str = str[1];
//
//	if (jQuery.inArray(str, selected_id) == -1) {
//	selected_id.push(str);
//	selected_name.push(strdt);
//	}
//	else {
//	for (var i = 0; i < selected_id.length; i++) {
//	if (selected_id[i] == str) break;
//	}
//	selected_id.splice(i, 1);
//	selected_name.splice(i, 1);
//	}
//	var id = '';
//	var ddd = '';
//	for (var i = 0; i < selected_id.length; i++) {
//	id += selected_id[i] + ',';
//	ddd += selected_name[i] + ',';
//	}
//	id = id.substr(0, id.length - 1);
//	ddd = ddd.substr(0, ddd.length - 1);
//	$('#item_lot').val(id);
//	//$('#prod_id').val( ddd );
//	}

	function js_set_value(str)
	{
		$("#item_lot").val(str);
		parent.emailwindow.hide();
	}
	</script>
	<input type="hidden" id="prod_id"/><input type="hidden" id="item_lot"/>
	<?
	if ($db_type == 0) {
	$sql = "SELECT distinct batch_lot from inv_transaction where prod_id='$txt_prod_id' and status_active=1 and is_deleted=0 and batch_lot!=' '  order by batch_lot desc";
	} elseif ($db_type == 2) {
	$sql = "SELECT distinct batch_lot from inv_transaction where prod_id='$txt_prod_id' and status_active=1 and is_deleted=0 and batch_lot is not null order by batch_lot desc";
	}

	//echo $sql;

	echo create_list_view("list_view", "Item Lot", "200", "330", "250", 0, $sql, "js_set_value", "batch_lot", "", 1, "", 0, "batch_lot", "recipe_entry_controller", 'setFilterGrid("list_view",-1);', '0', '');

	//echo create_list_view("list_view", "Item Lot", "50,80,80,70,80,130,70,80,110,100,70,90,90,90", "1250", "200", 0, $sql, "js_set_value", "id", "", 1, "0,0,batch_id,batch_id,batch_id,0,0,order_source,0,buyer_id,color_id,color_range,0,0", $arr, "id,labdip_no,batch_id,batch_id,batch_id,recipe_description,recipe_date,order_source,style_or_order,buyer_id,color_id,color_range,pickup,surplus_solution", "", "", '0,0,0,0,0,0,3,0,0,0,0,0,0,0', '');
	die;
}

if ($action == "save_update_delete")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	if (str_replace("'", "", $copy_id) == 11) //Yes- Copy
		{
			 $sql_previ_vali = "select c.current_stock,c.item_category_id,c.id,c.current_stock,b.ratio
					from product_details_master c,pro_recipe_entry_dtls b
					where c.id=b.prod_id and c.company_id=$cbo_working_company_id
					  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.mst_id=$update_id_check    and b.ratio>0";
					$sql_chk_prev_vali=sql_select($sql_previ_vali);
					foreach($sql_chk_prev_vali as $row)
					{

						$tot_previ_ratio_forArr[$row[csf('id')]]+=$row[csf('ratio')];
						$curr_stock_forArr[$row[csf('id')]]=$row[csf('current_stock')];

					}
					unset($sql_chk_prev_vali);

				for ($i = 1; $i <= $total_row; $i++)
				{
					$product_id = "product_id_" . $i;
					$txt_ratio = "txt_ratio_" . $i;
					$cbo_dose_base = "cbo_dose_base_" . $i;
					$current_ratio=str_replace("'","",$$txt_ratio);
					$txt_total_liquor_ratio=str_replace("'","",$txt_total_liquor_ratio);
					$txt_batch_weight=str_replace("'","",$txt_batch_weight);
					$prod_id=str_replace("'","",$$product_id);
					$dose_baseId=str_replace("'","",$$cbo_dose_base);
					$curr_stock=$curr_stock_forArr[$prod_id];
					$tot_curr_ratio_forArr[$prod_id]=$current_ratio;
					$previ_ratio=$tot_previ_ratio_forArr[$prod_id];
					$total_ratio=$tot_curr_ratio_forArr[$prod_id]+$previ_ratio;
					if($dose_baseId==1)
						{
							$recipe_qnty = ($txt_total_liquor*$total_ratio)/1000;
						}
						else {
							$recipe_qnty = ($txt_batch_weight*$total_ratio)/100;
						}
						//echo "20**Copy not allowed,Ratio Qty is Over than Stock Qty,Prod Id=".$recipe_qnty.'='.$curr_stock;
						//die;
					if($recipe_qnty>$curr_stock)
					{
						echo "20**Copy not allowed,Ratio Qty is Over than Stock Qty,Prod Id=".$prod_id;
						disconnect($con);
						die;
					}
				}
		}
		//=================Validation End===================
		//	echo '10**=='.$copy_id.'='.$recipe_qnty.'='.$curr_stock;die;
		if (str_replace("'", "", $copy_id) == 2) //No Copy
		{
			$recipe_date=str_replace("'","",$txt_recipe_date);
			$cbo_sub_process_chk=str_replace("'","",$cbo_sub_process);
			$recipe_date_cond="and app_date_upto<='$recipe_date' ";
			$lib_sql_recipe=" select id,app_date_upto,color_range_id,lower_limit_qty, upper_limit_qty from lib_recipe_base_color_range where  status_active=1 $recipe_date_cond order by app_date_upto desc";
			$sql_recipe_lib_res=sql_select($lib_sql_recipe);
			//=======Recipe Base Color Range===============
			if(count($sql_recipe_lib_res)>0)
			{
				$tot_ratio_for_color_range=0;
				 $sql_previ = "select c.item_category_id,c.id,c.current_stock,b.ratio
					from product_details_master c,pro_recipe_entry_dtls b
					where c.id=b.prod_id and c.company_id=$cbo_working_company_id
					and c.item_category_id in(6) and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.mst_id=$update_id and b.sub_process_id!=$cbo_sub_process_chk  and b.ratio>0";
					$sql_chk_prev=sql_select($sql_previ);
					foreach($sql_chk_prev as $row)
					{
						if($row[csf('item_category_id')]==6) //Dyes
						{
						$tot_ratio_for_color_range+=$row[csf('ratio')];
						}
					}
					unset($sql_chk_prev);
					//echo "10**=".$sql_previ;die;

				$prod_idArr=array();
				for ($i = 1; $i <= $total_row; $i++)
				{
					$product_id = "product_id_" . $i;
					$txt_ratio = "txt_ratio_" . $i;
					if(str_replace("'","",$$txt_ratio)!="")
					{
					$prod_idArr[$$product_id]=str_replace("'","",$$product_id);
					}
				}
				$sql_prod_cate = "select c.id as prod_id,c.item_category_id,c.id,c.current_stock
				from product_details_master c
				where c.company_id=$cbo_working_company_id
				and c.item_category_id in(6) and c.status_active=1 and c.is_deleted=0 and c.id in(".implode(",",$prod_idArr).")";
				$sql_prod_cate_res=sql_select($sql_prod_cate);
				foreach($sql_prod_cate_res as $row)
				{
					$prod_catIdArr[$row[csf('prod_id')]]=$row[csf('item_category_id')];
				}

				for ($i = 1; $i <= $total_row; $i++)
				{
					$txt_ratio = "txt_ratio_" . $i;
					$product_id = "product_id_" . $i;
					$updateIdDtls = "updateIdDtls_" . $i;
					$prod_id=str_replace("'","",$$product_id);
					$prod_catId=$prod_catIdArr[$prod_id];

					if(str_replace("'","",$$txt_ratio)!="" && $prod_catId==6)
					{
						$ratio_chk=str_replace("'","",$$txt_ratio);
						$tot_ratio_for_color_range+=$ratio_chk;
					}
				}
				//$tot_ratio_for_color_range
				//$tot_ratio_for_color_range=$tot_ratio_for_color_range*1;
					$tot_ratio_for_color_range_cond="and '$tot_ratio_for_color_range' between lower_limit_qty and upper_limit_qty";
					$sql_recipe_base=" select app_date_upto,color_range_id from lib_recipe_base_color_range where  status_active=1 $recipe_date_cond $tot_ratio_for_color_range_cond  order by app_date_upto desc";
					$sql_recipe_base_res=sql_select($sql_recipe_base,1);
					//$color_range_id=$sql_recipe_base_res[0][csf('color_range_id')];
					foreach($sql_recipe_base_res as $row)
					{
					$color_range_id=$row[csf('color_range_id')];
					}
					//echo "10**=".$color_range_id.'='.$tot_ratio_for_color_range.'='.$sql_recipe_base;die;
					if($color_range_id>0 && $tot_ratio_for_color_range>0)
					{
					$cbo_color_range='';
					$cbo_color_range=$color_range_id;
					}
			}
		}
//=======Recipe Base Color Range====== END===============
$hidden_labdip_id=str_replace("'", "", $hidden_labdip_id);
	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$recipe_update_id = '';
		//$color_id = return_id($txt_color, $color_arr, "lib_color", "id,color_name");
		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,color_name","59");
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_color[$color_id]=str_replace("'","",$txt_color);

			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
		}
		else
		{
			$color_id=0;
		}

		$subprocess = str_replace("'", "", $cbo_sub_process);

		$batch_id = str_replace("'", "", $txt_batch_id);
		$txt_recipe_serial_no = str_replace("'", "", $txt_recipe_serial_no);


		$sql_chk = sql_select("select is_control, id from variable_settings_production where company_name = $cbo_working_company_id and variable_list = 54 and is_deleted = 0 and status_active = 1");
		$stock_check=$sql_chk[0][csf("is_control")];
		if($stock_check==0 || $stock_check==2) $stock_check=0;else $stock_check=$stock_check;

		if (str_replace("'", "", $update_id) == "" && str_replace("'", "", $copy_id) == 2)
		{
			$recipe_serial_no =return_field_value("max(a.recipe_serial_no) as recipe_serial_no","pro_recipe_entry_mst a"," a.batch_id=" . $batch_id . " and a.entry_form=59 and a.status_active=1 and a.is_deleted=0", "recipe_serial_no");
			$recipe_serial_no=$recipe_serial_no+1;
		}
		else
		{
			$recipe_serial_no=$txt_recipe_serial_no;
		}

		if (str_replace("'", "", $copy_id) == 1 || str_replace("'", "", $copy_id) == 2)
		{

			$batch_process_ids = return_field_value("process_id","pro_batch_create_mst","id = $batch_id");
			if($batch_process_ids!= "")
			{
				$batch_process_id_arr = explode(",", $batch_process_ids);
				if(!in_array("137", $batch_process_id_arr))
				{

					$double_dyeing = return_field_value("double_dyeing", "pro_batch_create_mst", "id=" . $batch_id . " and status_active=1 and is_deleted=0 and status_active=1 and is_deleted=0", "double_dyeing");

					$batch_id_count =return_field_value("count(a.batch_id) as batch_id_count","pro_recipe_entry_mst a"," a.batch_id=" . $batch_id . " and a.entry_form=59 and a.status_active=1 and a.is_deleted=0", "batch_id_count");

					/*$recipe_no = return_field_value("a.id as id", "pro_recipe_entry_mst a,pro_recipe_entry_dtls b", "a.id=b.mst_id and b.sub_process_id=$subprocess and a.batch_id=" . $batch_id . " and a.entry_form=59 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "id");*/
					$recipe_nos=sql_select("select a.id as id from pro_recipe_entry_mst a,pro_recipe_entry_dtls b where a.id=b.mst_id and b.sub_process_id=$subprocess and a.batch_id=" . $batch_id . " and a.entry_form=59 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  a.id");
					$recipe_no="";
					foreach ($recipe_nos as $row) {
						$recipe_no.=$row[csf('id')].",";
					}
					$recipe_no =chop($recipe_no,",");
					if ($recipe_no != '') {
						/*if ($double_dyeing==1 && $batch_id_count==2) {
							echo "14**0**$recipe_no"; die;
						}
						else*/
						if($double_dyeing==2 || $double_dyeing==0){
 							echo "14**0**$recipe_no";
 							disconnect($con);
							die;
						}

					}
				}
			}
		}


		if (is_duplicate_field("batch_no", "pro_fab_subprocess", "batch_id=" . $batch_id . " and load_unload_id=2 and result=1 and status_active=1 and is_deleted=0") == 1) //Issue Id-14023
		{
			disconnect($con);
			echo "13**0**$batch_id";
			die;
		}


		if (str_replace("'", "", $update_id) == "")
		{
			/*if(is_duplicate_field( "labdip_no", "pro_recipe_entry_mst", "labdip_no=$txt_labdip_no" )==1)
			{
				echo "11**0";
				die;
			}*/

			$id = return_next_id("id", "pro_recipe_entry_mst", 1);

			$field_array = "id, entry_form, labdip_no, labdip_id, company_id,working_company_id, location_id, recipe_description, batch_id,machine_id, method, recipe_date, order_source, style_or_order, booking_id, color_id, buyer_id, color_range,double_dyeing, booking_type,recipe_type, total_liquor, batch_ratio, liquor_ratio,batch_qty, remarks,pump,cycle_time,in_charge_id, inserted_by, insert_date, pickup, surplus_solution, sub_tank, copy_from, recipe_serial_no";
			//echo $txt_liquor;
			$data_array = "(" . $id . ",59," . $txt_labdip_no . ",'" . $hidden_labdip_id . "'," . $cbo_company_id . "," . $cbo_working_company_id . "," . $cbo_location . "," . $txt_recipe_des . "," . $txt_batch_id . "," . $cbo_machine_name . "," . $cbo_method . "," . $txt_recipe_date . "," . $cbo_order_source . "," . $txt_booking_order . "," . $txt_booking_id . ",'" . $color_id . "'," . $cbo_buyer_name . "," . $cbo_color_range . "," . $cbo_double_dyeing . "," . $txt_booking_type . "," . $cbo_recipe_type . "," . $txt_liquor . "," . $txt_batch_ratio . "," . $txt_liquor_ratio . "," . $txt_batch_weight . "," . $txt_remarks . "," . $txt_pump . "," . $txt_cycle_time . "," . $txt_in_charge_id . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $txt_pick_up . "," .$surpls_solution . "," .$txt_sub_tank . "," .$txt_copy_from . ",'" .$recipe_serial_no . "')";

			//$rID=sql_insert("pro_recipe_entry_mst",$field_array,$data_array,0);
			//if($rID) $flag=1; else $flag=0;
			$recipe_update_id = $id;
		}
		else
		{
			/*$requisition_no="";
			$sql_reqs="select requ_no from dyes_chem_issue_requ_mst where recipe_id=$update_id and status_active=1 and is_deleted=0 order by id";
			$data=sql_select($sql_reqs);
			if(count($data)>0)
			{
				foreach($data as $row)
				{
					if($requisition_no=="") $requisition_no=$row[csf('requ_no')]; else $requisition_no.=",\n".$row[csf('requ_no')];
				}

				echo "14**".$requisition_no."**1";
				die;
			}*/

			/*if(is_duplicate_field( "labdip_no", "pro_recipe_entry_mst", "labdip_no=$txt_labdip_no and id<>$update_id" )==1)
			{
				echo "11**0";
				die;
			}*/
			if ($db_type == 0)
			{
				$reqsn_update = execute_query("update dyes_chem_issue_requ_mst a, dyes_chem_requ_recipe_att b set a.is_apply_last_update=2, a.updated_by=" . $_SESSION['logic_erp']['user_id'] . ", a.update_date='" . $pc_date_time . "' where a.id=b.mst_id and b.recipe_id=" . $update_id);
			} else {
				$reqsn_update = execute_query("update dyes_chem_issue_requ_mst a set a.is_apply_last_update=2, a.updated_by=" . $_SESSION['logic_erp']['user_id'] . ", a.update_date='" . $pc_date_time . "' where exists( select b.mst_id from dyes_chem_requ_recipe_att b where a.id=b.mst_id and b.recipe_id=" . $update_id . ")");
			}

			$reqsn_update_att = execute_query("update dyes_chem_requ_recipe_att set is_apply_last_update=2 where recipe_id=" . $update_id);


			if ($reqsn_update && $reqsn_update_att) {
				$flag = 1;
			} else {
				$flag = 0;
			}



			if (is_duplicate_field("sub_process_id", "pro_recipe_entry_dtls", "mst_id=$update_id and sub_process_id=$cbo_sub_process and status_active=1 and is_deleted=0") == 1) {
				echo "11**0";
				disconnect($con);
				die;
			}
			$field_array_update = "labdip_no*labdip_id*company_id*working_company_id*location_id*recipe_description*batch_id*machine_id*method*recipe_date*order_source*style_or_order*color_id*buyer_id*color_range*double_dyeing*booking_id*booking_type*total_liquor*batch_ratio*liquor_ratio*batch_qty*recipe_type*remarks*pump*cycle_time*in_charge_id*updated_by*update_date*pickup*surplus_solution*sub_tank";

			$data_array_update = $txt_labdip_no . "*'" . $hidden_labdip_id. "'*" . $cbo_company_id . "*" . $cbo_working_company_id . "*" . $cbo_location . "*" . $txt_recipe_des . "*" . $txt_batch_id . "*" . $cbo_machine_name . "*" . $cbo_method . "*" . $txt_recipe_date . "*" . $cbo_order_source . "*" . $txt_booking_order . "*" . $color_id . "*" . $cbo_buyer_name . "*" . $cbo_color_range . "*" . $cbo_double_dyeing . "*" . $txt_booking_id . "*" . $txt_booking_type . "*" . $txt_liquor . "*" . $txt_batch_ratio . "*" . $txt_liquor_ratio . "*" . $txt_batch_weight . "*" . $cbo_recipe_type . "*" . $txt_remarks . "*" . $txt_pump . "*" . $txt_cycle_time . "*" . $txt_in_charge_id . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'" . "*" . $txt_pick_up . "*" . $surpls_solution . "*" . $txt_sub_tank;

			//$rID=sql_update("pro_recipe_entry_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			//if($rID) $flag=1; else $flag=0;
			$recipe_update_id = str_replace("'", "", $update_id);
		}
		/*if (str_replace("'", "", $copy_id) == 2)
		{
			$sub_seq_no =return_field_value("max(a.sub_seq) as sub_seq","pro_recipe_entry_dtls a"," a.mst_id=" . $update_id . " and a.status_active=1 and a.is_deleted=0", "sub_seq");
			$sub_seq=$sub_seq_no+1;
		}*/
		//echo "10**".$sub_seq;die;

		if (str_replace("'", "", $copy_id) == 2)
		{
			//if ($subprocess == 93 || $subprocess == 94 || $subprocess == 95 || $subprocess == 96 || $subprocess == 97 || $subprocess == 98  || $subprocess == 140 || $subprocess == 141 || $subprocess == 142 || $subprocess == 143)
			if(in_array($subprocess,$subprocessForWashArr))
			{
				$txt_comments_1 = str_replace("'", "", $txt_comments_1);
				$txt_ratio_1 = str_replace("'", "", $txt_ratio_1);
				$txt_seqno_1 =1;
				$cbo_dose_base_1 = str_replace("'", "", $cbo_dose_base_1);

				$field_array_dtls = "id,mst_id,sub_process_id,store_id,process_remark,comments,liquor_ratio,total_liquor,ratio,seq_no,sub_seq,dose_base,check_id,inserted_by,insert_date";
				$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);
				$data_array_dtls = "(" . $dtls_id . "," . $recipe_update_id . "," . $cbo_sub_process . "," . $cbo_store_name . "," . $txt_subprocess_remarks . ",'" . $txt_comments_1 . "'," . $txt_liquor_ratio_dtls . "," . $txt_total_liquor_ratio . ",'" . $txt_ratio_1 . "','" . $txt_seqno_1 . "'," . $txt_subprocess_seq . ",'" . $cbo_dose_base_1 . "','" . str_replace("'", "", $check_id) . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				//echo "10**insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
			}
			else
			{

				$field_array_dtls = "id,mst_id,sub_process_id,store_id,process_remark,prod_id,item_lot,comments,liquor_ratio,total_liquor,dose_base,check_id,ratio,seq_no,sub_seq,inserted_by,insert_date";
				$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);

				for ($i = 1; $i <= $total_row; $i++)
				{
					$product_id = "product_id_" . $i;
					$txt_item_lot = "txt_item_lot_" . $i;
					$cbo_dose_base = "cbo_dose_base_" . $i;
					$txt_ratio = "txt_ratio_" . $i;
					$txt_comments = "txt_comments_" . $i;
					$txt_seqno = "txt_seqno_" . $i;
					if ($i != 1) $data_array_dtls .= ",";
					$data_array_dtls .= "(" . $dtls_id . "," . $recipe_update_id . "," . $cbo_sub_process . "," . $cbo_store_name . "," . $txt_subprocess_remarks . ",'" . str_replace("'", "", $$product_id) . "','" . str_replace("'", "", $$txt_item_lot) . "','" . str_replace("'", "", $$txt_comments) . "'," . $txt_liquor_ratio_dtls . "," . $txt_total_liquor_ratio . ",'" . str_replace("'", "", $$cbo_dose_base) . "','" . str_replace("'", "", $check_id) . "','" . str_replace("'", "", $$txt_ratio) . "','" . str_replace("'", "", $$txt_seqno) . "','" . str_replace("'", "", $txt_subprocess_seq) . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$dtls_id = $dtls_id + 1;
				}
				//echo "insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
			}
		}
		else if (str_replace("'", "", $copy_id) == 1)
		{
			/*if($subprocess==93 || $subprocess==94 || $subprocess==95 || $subprocess==96 || $subprocess==97 || $subprocess==98)
		  	{
				$field_array_dtls="id,mst_id,sub_process_id,process_remark,comments,ratio,seq_no,dose_base,inserted_by,insert_date";
				$dtls_id=return_next_id( "id","pro_recipe_entry_dtls", 1 ) ;
				$sql="select id, sub_process_id, prod_id, item_lot,comments,process_remark, dose_base, ratio, seq_no from pro_recipe_entry_dtls where mst_id=$update_id_check order by id";

				$nameArray=sql_select( $sql );
				$data_array_dtls="(".$dtls_id.",".$recipe_update_id.",'".$nameArray[0][csf('sub_process_id')]."','".$nameArray[0][csf('process_remark')]."','".$nameArray[0][csf('comments')]."','".$nameArray[0][csf('ratio')]."','".$nameArray[0][csf('seq_no')]."','".$nameArray[0][csf('dose_base')]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			}*/
			// else
			//{
			if($stock_check==1)//Stock Qty Check Here
			{
				$sql_stock = "select c.id,c.current_stock
				from product_details_master c,pro_recipe_entry_dtls b
				where c.id=b.prod_id and c.company_id=$cbo_working_company_id
				and c.item_category_id in(5,6,7,23) and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.mst_id=$update_id_check  ";
				$result_stock = sql_select($sql_stock);
				foreach ($result_stock as $row)
				{
					if($row[csf('current_stock')]<=0)
					{
						echo "20**Copy not allowed,Stock Zero/Minus Found,Prod Id=".$row[csf('id')];
						disconnect($con);
						die;
					}

				}

			}

			$field_array_dtls = "id,mst_id,sub_process_id,store_id,process_remark,prod_id,item_lot,comments,liquor_ratio,total_liquor,dose_base,check_id,ratio,seq_no,sub_seq,inserted_by,insert_date";
			$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);
			$sql = "select b.id, b.sub_process_id, b.prod_id, b.item_lot,b.comments,b.process_remark, b.dose_base,b.check_id,b.liquor_ratio,b.total_liquor,b.ratio,b.seq_no,b.sub_seq, b.store_id,a.current_stock from pro_recipe_entry_dtls b,product_details_master a where a.id=b.prod_id and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.mst_id=$update_id_check  and b.status_active=1  order by b.sub_process_id,b.seq_no";
			$nameArray = sql_select($sql);
			foreach ($nameArray as $row)
			{
				$prodIdArr[$row[csf('prod_id')]]=$row[csf('prod_id')];
			}
			$sql_store = "select a.id as prod_id,a.current_stock,b.store_id,b.lot,b.cons_qty as store_stock from inv_store_wise_qty_dtls b,product_details_master a where a.id=b.prod_id and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_working_company_id  and b.status_active=1 and a.id in(".implode(",",$prodIdArr).") and a.current_stock>0 and b.cons_qty>0 order by  a.id,b.lot ";
			$nameArray_store = sql_select($sql_store);
			
			//  echo "10**=".$sql .'DDD';die;
			foreach ($nameArray_store as $row)
			{
				$prod_key=$row[csf('prod_id')]."_".$row[csf('store_id')];
				if($storeIdCheck_prod_id_arr[$prod_key]=='') 
				{
				$storeWiseStockArr[$prod_key]=$row[csf('store_stock')];
				}
			}
			unset($nameArray_store);

			$tot_row = count($nameArray);
			$i = 1;

			foreach ($nameArray as $row)
			{
			 
				$withZeroStock=str_replace("'", "", $withZeroStock);
				$batch_weight=str_replace("'", "", $txt_batch_weight);
				$batch_ratio=str_replace("'", "", $txt_batch_ratio);
				$total_liquor=$batch_ratio*1*$row[csf('liquor_ratio')]*$batch_weight;
				$prod_key=$row[csf('prod_id')]."_".$row[csf('store_id')];
				$store_stock=$storeWiseStockArr[$prod_key];
				if($row[csf('dose_base')]==1)
				{
					  $recipe_qnty = ($total_liquor*$row[csf('ratio')])/1000;
				}
				else{
					$recipe_qnty = ($batch_weight*$row[csf('ratio')])/100;
				}
				 
			//	echo "10**".'='.$row[csf('prod_id')].'='.$recipe_qnty.'='.$store_stock.'<br>';
			 
				
				if($withZeroStock==0 && $recipe_qnty<=$store_stock) //Issue id=27998,Yr-2023
				{
					//echo "99**Recipe qty is over found than the stock=ProdId-".$row[csf('prod_id')];die;
				//issue id=27965

				$process_remark=str_replace("'", "", $row[csf('process_remark')]);
				if ($i != 1) $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $dtls_id . "," . $recipe_update_id . ",'" . $row[csf('sub_process_id')] . "','" . $row[csf('store_id')] . "','" . $process_remark . "','" . $row[csf('prod_id')] . "','" . $row[csf('item_lot')] . "','" . $row[csf('comments')] . "','" . $row[csf('liquor_ratio')] . "','" . $total_liquor . "','" . $row[csf('dose_base')] . "','" . $row[csf('check_id')] . "','" . $row[csf('ratio')] . "','" . $row[csf('seq_no')] . "','" . $row[csf('sub_seq')] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$dtls_id = $dtls_id + 1; 
			  
				$i++;
			  }
			  if($withZeroStock==1) //Issue id=27998,Yr-2023
				{
					//echo "99**Recipe qty is over found than the stock=ProdId-".$row[csf('prod_id')];die;
				//issue id=27965

				$process_remark=str_replace("'", "", $row[csf('process_remark')]);
				if ($i != 1) $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $dtls_id . "," . $recipe_update_id . ",'" . $row[csf('sub_process_id')] . "','" . $row[csf('store_id')] . "','" . $process_remark . "','" . $row[csf('prod_id')] . "','" . $row[csf('item_lot')] . "','" . $row[csf('comments')] . "','" . $row[csf('liquor_ratio')] . "','" . $total_liquor . "','" . $row[csf('dose_base')] . "','" . $row[csf('check_id')] . "','" . $row[csf('ratio')] . "','" . $row[csf('seq_no')] . "','" . $row[csf('sub_seq')] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$dtls_id = $dtls_id + 1; 
			  
				$i++;
			  }
			}
			//}
		}
		//echo "10**=B";die;
		
		 	// echo "10**insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
		/*$rID2=sql_insert("pro_recipe_entry_dtls",$field_array_dtls,$data_array_dtls,1);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		} */

		//test all insert
		if (str_replace("'", "", $update_id) == "")
		{
			$rID = sql_insert("pro_recipe_entry_mst", $field_array, $data_array, 0);
			if ($rID) $flag = 1; else $flag = 0;
		}
		else
		{
			$rID = sql_update("pro_recipe_entry_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
			if ($rID) $flag = 1; else $flag = 0;
		}
		$rID2 = sql_insert("pro_recipe_entry_dtls", $field_array_dtls, $data_array_dtls, 1);
		if ($flag == 1)
		{
			if ($rID2) $flag = 1; else $flag = 0;
		}
		/*$color_range_id =return_field_value("color_range_id","pro_batch_create_mst","id=".$batch_id." and entry_form=0 and status_active=1 and is_deleted=0", "color_range_id");
		echo "10**SELECT color_range_id from pro_batch_create_mst where id=".$batch_id." and entry_form=0 and status_active=1 and is_deleted=0";die;*/
		$field_array_update_color_range = "color_range_id*double_dyeing*updated_by*update_date";
		$data_array_update_color_range = $cbo_color_range."*".$cbo_double_dyeing."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID3 = sql_update("pro_batch_create_mst", $field_array_update_color_range, $data_array_update_color_range, "id", $batch_id, 1);
		if ($flag == 1)
		{
		if ($rID3) $flag = 1; else $flag = 0;
		}

		$field_array_update_dyeing_machine = "dyeing_machine*updated_by*update_date";
		$data_array_update_dyeing_machine = $cbo_machine_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID4 = sql_update("pro_batch_create_mst", $field_array_update_dyeing_machine, $data_array_update_dyeing_machine, "id", $batch_id, 1);
		if ($flag == 1)
		{
			if ($rID4) $flag = 1; else $flag = 0;
		}
		

		//echo "10**".$rID.'=='.$rID2.'=='.$rID3.'=='.$rID4.'=='.$flag;die;
		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "0**" . $recipe_update_id . "**" . $subprocess. "**" . $recipe_serial_no. "**".str_replace("'", '', $cbo_color_range). "**".str_replace("'", '', $cbo_double_dyeing);
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "0**" . $recipe_update_id . "**" . $subprocess. "**" . $recipe_serial_no. "**" .str_replace("'", '', $cbo_color_range). "**".str_replace("'", '', $cbo_double_dyeing);
			} else {
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation == 1)   // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		// =============== mc wise production floor update into batch creation page ==================
		$mc_wise_production_floor_id=0;
		if (str_replace("'", "", $cbo_working_company_id)>0)
		{
			$mc_wise_production_floor_id = return_field_value("floor_id","lib_machine_name","company_id=$cbo_working_company_id and category_id=2 and id = $cbo_machine_name and status_active=1 and is_deleted=0 and is_locked=0");
		}
		// echo $mc_wise_production_floor_id.'==';die;
		// =============== mc wise production floor update into batch creation page ==================

		/*$requisition_no="";
		$sql_reqs="select requ_no from dyes_chem_issue_requ_mst where recipe_id=$update_id and status_active=1 and is_deleted=0 order by id";
		$data=sql_select($sql_reqs);
		if(count($data)>0)
		{
			foreach($data as $row)
			{
				if($requisition_no=="") $requisition_no=$row[csf('requ_no')]; else $requisition_no.=",\n".$row[csf('requ_no')];
			}

			echo "14**".$requisition_no."**1";
			die;
		}*/

		/*if(is_duplicate_field( "labdip_no", "pro_recipe_entry_mst", "labdip_no=$txt_labdip_no and id<>$update_id" )==1)
		{
			echo "11**0";
			die;
		}*/
		//	txt_subprocess_seq
		$batch_id = str_replace("'", "", $txt_batch_id);
		$subprocess = str_replace("'", "", $cbo_sub_process);
		if (str_replace("'", "", $copy_id) == 1)
		{
			//$recipe_no = return_field_value("id", "pro_recipe_entry_mst", "batch_id=" . $batch_id . " and entry_form=59", "id");

			$batch_process_ids = return_field_value("process_id","pro_batch_create_mst","id = $batch_id");
			if($batch_process_ids!= "")
			{
				$batch_process_id_arr = explode(",", $batch_process_ids);
				if(!in_array("137", $batch_process_id_arr))
				{
					$recipe_no = return_field_value("a.id", "pro_recipe_entry_mst a,pro_recipe_entry_dtls b", "a.id=b.mst_id and b.sub_process_id=$subprocess and a.batch_id=" . $batch_id . " and a.entry_form=59 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "id");
					if ($recipe_no != '') {
						echo "14**0**$recipe_no";
						disconnect($con);
						die;
					}
				}
			}
		}

		if (is_duplicate_field("batch_no", "pro_fab_subprocess", "batch_id=" . $batch_id . " and load_unload_id=2 and result=1 and status_active=1 and is_deleted=0") == 1)
		{
			//disconnect($con);
			echo "13**0**$batch_id";
			disconnect($con);
			die;
		}



		//$color_id = return_id($txt_color, $color_arr, "lib_color", "id,color_name");//booking_id 	booking_type 	total_liquor
		//txt_batch_weight
		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,color_name","59");
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_color[$color_id]=str_replace("'","",$txt_color);

			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
		}
		else
		{
			$color_id=0;
		}

		$field_array_update = "labdip_no*labdip_id*company_id*working_company_id*location_id*recipe_description*batch_id*machine_id*method*recipe_date*order_source*style_or_order*color_id*buyer_id*color_range*double_dyeing*booking_id*booking_type*total_liquor*batch_ratio*liquor_ratio*batch_qty*recipe_type*remarks*pump*cycle_time*in_charge_id*updated_by*update_date*pickup*surplus_solution*sub_tank";

		$data_array_update = $txt_labdip_no . "*'" . $hidden_labdip_id. "'*" . $cbo_company_id . "*" . $cbo_working_company_id . "*" . $cbo_location . "*" . $txt_recipe_des . "*" . $txt_batch_id . "*" . $cbo_machine_name . "*" . $cbo_method . "*" . $txt_recipe_date . "*" . $cbo_order_source . "*" . $txt_booking_order . "*'" . $color_id . "'*" . $cbo_buyer_name . "*" . $cbo_color_range . "*" . $cbo_double_dyeing . "*" . $txt_booking_id . "*" . $txt_booking_type . "*" . $txt_liquor . "*" . $txt_batch_ratio . "*" . $txt_liquor_ratio . "*" . $txt_batch_weight . "*" . $cbo_recipe_type . "*" . $txt_remarks . "*" . $txt_pump . "*" . $txt_cycle_time . "*" . $txt_in_charge_id . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'" . "*" . $txt_pick_up . "*" . $surpls_solution . "*" . $txt_sub_tank;

		//echo $data_array_update;die;
		//$rID=sql_update("pro_recipe_entry_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		//if($rID) $flag=1; else $flag=0;
		//if ($subprocess == 93 || $subprocess == 94 || $subprocess == 95 || $subprocess == 96 || $subprocess == 97 || $subprocess == 98 || $subprocess == 140 || $subprocess == 141 || $subprocess == 142 || $subprocess == 143 )
		if(in_array($subprocess,$subprocessForWashArr))
		{
			//$update_dtls_id=str_replace("'","",$updateIdDtls_1);
			$field_array_dtls_update2 = "sub_process_id*process_remark*comments*liquor_ratio*total_liquor*ratio*seq_no*sub_seq*dose_base*check_id*updated_by*update_date";
			//$dtls_id=return_next_id( "id","pro_recipe_entry_dtls", 1 ) ;
			//$data_array_dtls_update="$txt_item_lot)."'*";
			$data_array_dtls_update2 = $subprocess . "*" . $txt_subprocess_remarks . "*" . $txt_comments_1 . "*" . $txt_liquor_ratio_dtls . "*" . $txt_total_liquor_ratio . "*" . $txt_ratio_1 . "*" . $txt_seqno_1 . "*" . $txt_subprocess_seq . "*" . $cbo_dose_base_1 . "*" . $check_id . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
			$update_dtls_id = str_replace("'", "", $updateIdDtls_1);


		}
		else
		{
			$field_array_dtls = "id,mst_id,sub_process_id,store_id,process_remark,prod_id,item_lot,comments,liquor_ratio,total_liquor,dose_base,check_id,ratio,seq_no,sub_seq,inserted_by,insert_date";
			$field_array_dtls_update = "prod_id*item_lot*comments*liquor_ratio*total_liquor*dose_base*check_id*ratio*seq_no*sub_seq*sub_process_id*process_remark*updated_by*update_date";
			$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);

			for ($i = 1; $i <= $total_row; $i++) {
				$product_id = "product_id_" . $i;
				$txt_item_lot = "txt_item_lot_" . $i;
				$txt_comments = "txt_comments_" . $i;
				$cbo_dose_base = "cbo_dose_base_" . $i;
				$txt_ratio = "txt_ratio_" . $i;
				$updateIdDtls = "updateIdDtls_" . $i;
				$txt_seqno = "txt_seqno_" . $i;

				if (str_replace("'", "", $$updateIdDtls) != "") {
					$id_arr[] = str_replace("'", '', $$updateIdDtls);
					$data_array_dtls_update[str_replace("'", '', $$updateIdDtls)] = explode("*", (str_replace("'", "", $$product_id) . "*'" . str_replace("'", "", $$txt_item_lot) . "'*'" . str_replace("'", "", $$txt_comments) . "'*" . $txt_liquor_ratio_dtls . "*" . $txt_total_liquor_ratio . "*'" . str_replace("'", "", $$cbo_dose_base) . "'*'" . str_replace("'", "", $check_id) . "'*'" . str_replace("'", "", $$txt_ratio) . "'*'" . str_replace("'", "", $$txt_seqno) . "'*" . $txt_subprocess_seq . "*" . $cbo_sub_process . "*" . $txt_subprocess_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
				} else {
					if ($data_array_dtls != "") $data_array_dtls .= ",";

					$data_array_dtls .= "(" . $dtls_id . "," . $update_id . "," . $cbo_sub_process . "," . $cbo_store_name . "," . $txt_subprocess_remarks . ",'" . str_replace("'", "", $$product_id) . "','" . str_replace("'", "", $$txt_item_lot) . "','" . str_replace("'", "", $$txt_comments) . "'," . $txt_liquor_ratio_dtls . "," . $txt_total_liquor_ratio . ",'" . str_replace("'", "", $$cbo_dose_base) . "','" . str_replace("'", "", $check_id) . "','" . str_replace("'", "", $$txt_ratio) . "','" . str_replace("'", "", $$txt_seqno) . "'," . $txt_subprocess_seq . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$dtls_id = $dtls_id + 1;
				}
			}
		}
		//print_r ($data_array_dtls_update);die;
		/*if($data_array_dtls_update!="")
		{
			$rID2=execute_query(bulk_update_sql_statement( "pro_recipe_entry_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ),1);
			//echo bulk_update_sql_statement( "pro_recipe_entry_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );die;
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}

		if($data_array_dtls!="")
		{
			//echo "insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
			$rID2=sql_insert("pro_recipe_entry_dtls",$field_array_dtls,$data_array_dtls,1);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}*/

		// Update test all
		$rID = sql_update("pro_recipe_entry_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
		if ($rID) $flag = 1; else $flag = 0;
		if ($data_array_dtls_update2 != "") {
			$rID = sql_update("pro_recipe_entry_dtls", $field_array_dtls_update2, $data_array_dtls_update2, "id", $update_dtls_id, 1);
			if ($rID) $flag = 1; else $flag = 0;
		}
		if ($data_array_dtls_update != "") {
			$rID2 = execute_query(bulk_update_sql_statement("pro_recipe_entry_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr), 1);
			if ($rID2) $flag = 1; else $flag = 0;
		}

		if ($data_array_dtls != "") {
			//echo "insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
			$rID2 = sql_insert("pro_recipe_entry_dtls", $field_array_dtls, $data_array_dtls, 1);
			if ($rID2) $flag = 1; else $flag = 0;
		}

		if ($db_type == 0) {
			$reqsn_update = execute_query("update dyes_chem_issue_requ_mst a, dyes_chem_requ_recipe_att b set a.is_apply_last_update=2, a.updated_by=" . $_SESSION['logic_erp']['user_id'] . ", a.update_date='" . $pc_date_time . "' where a.id=b.mst_id and b.recipe_id=" . $update_id);
		} else {
			$reqsn_update = execute_query("update dyes_chem_issue_requ_mst a set a.is_apply_last_update=2, a.updated_by=" . $_SESSION['logic_erp']['user_id'] . ", a.update_date='" . $pc_date_time . "' where exists( select b.mst_id from dyes_chem_requ_recipe_att b where a.id=b.mst_id and b.recipe_id=" . $update_id . ")");
		}

		$reqsn_update_att = execute_query("update dyes_chem_requ_recipe_att set is_apply_last_update=2 where recipe_id=" . $update_id);

		if ($flag == 1) {
			if ($reqsn_update && $reqsn_update_att) {
				$flag = 1;
			} else {
				$flag = 0;
			}
		}

		$field_array_update_color_range = "color_range_id*double_dyeing*updated_by*update_date";
		$data_array_update_color_range = $cbo_color_range."*".$cbo_double_dyeing."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID3 = sql_update("pro_batch_create_mst", $field_array_update_color_range, $data_array_update_color_range, "id", $batch_id, 1);
		if ($rID3) $flag = 1; else $flag = 0;

		//$rID4 = sql_update("pro_batch_create_mst", $field_array_update_dyeing_machine, $data_array_update_dyeing_machine, "id", $batch_id, 1);
		//if ($rID4) $flag = 1; else $flag = 0;

		$field_array_update_dyeing_machine = "dyeing_machine*floor_id*updated_by*update_date";
		$data_array_update_dyeing_machine = $cbo_machine_name."*'".$mc_wise_production_floor_id."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID4 = sql_update("pro_batch_create_mst", $field_array_update_dyeing_machine, $data_array_update_dyeing_machine, "id", $batch_id, 1);
		if ($rID4) $flag = 1; else $flag = 0;

		// echo "10**".$rID.'=='.$rID2.'=='.$rID3.'=='.$rID4.'=='.$flag;die;
		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $update_id) . "**" . $subprocess. "****".str_replace("'", '', $cbo_color_range). "**".str_replace("'", '', $cbo_double_dyeing);
			} else {
				mysql_query("ROLLBACK");
				echo "6**0**1";
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "1**" . str_replace("'", '', $update_id) . "**" . $subprocess. "****".str_replace("'", '', $cbo_color_range). "**".str_replace("'", '', $cbo_double_dyeing);
			} else {
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$batch_id = str_replace("'", "", $txt_batch_id);

		$recipe_no = return_field_value("a.id as id", "pro_recipe_entry_mst a,pro_recipe_entry_dtls b", "a.id=b.mst_id and a.id=" . $update_id . " and a.entry_form!=59 and  b.status_active=1 and b.is_deleted=0", "id");
		if ($recipe_no != '')
		{
			echo "133**0**$recipe_no";
			disconnect($con);
			die;
		}

		if($recipe_no=='')
		{
			$req_no = return_field_value("b.requ_no as id", "dyes_chem_issue_requ_mst b,dyes_chem_requ_recipe_att a", " b.id=a.mst_id and a.recipe_id=" .$update_id." and  b.status_active=1 and b.is_deleted=0 ", "id");
		}
		if ($req_no != '')
		{
			echo "11**0**$req_no";
			disconnect($con);
			die;
		}
		if (is_duplicate_field("batch_no", "pro_fab_subprocess", "batch_id=" . $batch_id . " and load_unload_id=2 and result=1 and status_active=1 and is_deleted=0") == 1)
		{
			disconnect($con);
			echo "13**0**$batch_id";
			disconnect($con);
			die;
		}

		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);



		//echo "10**".$delete_cause;die;

		/*$rID1=execute_query( "update pro_recipe_entry_mst set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  id in (".str_replace("'","",$update_id).")",0);
		if($rID1) $flag = 1; else $flag = 0;
		$rID2=execute_query( "update pro_recipe_entry_dtls set status_active=0,is_deleted=1 where  mst_id in(".str_replace("'","",$update_id).") ",0);
		if( $flag==1)
		{
			if($rID2) $flag = 1; else $flag = 0;
		}*/
		$dtls_mst=sql_select("select mst_id from pro_recipe_entry_dtls where mst_id in(".str_replace("'","",$update_id).") and status_active=1 and is_deleted=0 ");
		if(count($dtls_mst)==1)
		{
			$rID3=execute_query( "update pro_recipe_entry_mst set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where  id in(".str_replace("'","",$update_id).") ",0);
			//echo "10**=update pro_recipe_entry_mst set status_active=0,is_deleted=1 where  id in(".str_replace("'","",$update_id).") ";die;
			if($rID3) $flag = 1; else $flag = 0;
		}
		//echo "10**=".count($dtls_mst).'='.$rID3.'='.$flag;die;
		$rID2=execute_query( "update pro_recipe_entry_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause' where  mst_id in(".str_replace("'","",$update_id).") and sub_process_id=".str_replace("'","",$cbo_sub_process)." ",0);
		if($rID2) $flag = 1; else $flag = 0;




		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'","",$update_id);
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "2**" . str_replace("'","",$update_id);
			} else {
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
}
function sql_update2($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit="",$return_query='')
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
	if($return_query==1){return $strQuery ;}
// echo $strQuery;die;
		//return $strQuery;die;
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);

	if ($exestd){user_activities($exestd);}
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

	if ($action == "item_req_check_data") {
		$data = explode("**", $data);

		$company=$data[0];
		$update_id=$data[1];
		$cbo_sub_process=$data[2];
		$batch_id=$data[3];

		$recipe_no = return_field_value("a.id as id", "pro_recipe_entry_mst a,pro_recipe_entry_dtls b", "a.id=b.mst_id and a.id=" . $update_id . " and a.entry_form!=59 and  b.status_active=1 and b.is_deleted=0", "id");
		if ($recipe_no != '')
		{
			echo "133**0**$recipe_no";
			die;
		}

		if($recipe_no=='')
		{
			$req_no = return_field_value("b.requ_no as id", "dyes_chem_issue_requ_mst b,dyes_chem_requ_recipe_att a", " b.id=a.mst_id and a.recipe_id=" .$update_id." and  b.status_active=1 and b.is_deleted=0", "id");
		}
		if ($req_no != '')
		{
			echo "11**0**$req_no";
			die;
		}
		$batch_no = return_field_value("batch_no as id", "pro_fab_subprocess", "batch_id=" .$batch_id." and load_unload_id=2 and result=1 and status_active=1 and is_deleted=0", "id");

		if ($batch_no!= '')
		{
			echo "13**0**$batch_no";
		}

		exit();
	}

	if ($action == "item_stock_details") {
		$data = explode("**", $data);
		$update_id_check=$data[1];
		$batch_wgt=$data[2];
		$txt_total_liquor=$data[3];
		$txt_batch_ratio=$data[4];

		$sql = "SELECT b.id, b.sub_process_id, b.prod_id, b.item_lot,b.comments,b.process_remark, b.dose_base,b.check_id,b.liquor_ratio,b.total_liquor,b.ratio,b.seq_no,b.sub_seq, b.store_id,a.current_stock, a.item_description
		from pro_recipe_entry_dtls b,product_details_master a 
		where a.id=b.prod_id and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.mst_id=$update_id_check  and b.status_active=1  order by b.sub_process_id,b.seq_no";
		$nameArray = sql_select($sql);
		foreach ($nameArray as $row)
		{
			$prodIdArr[$row[csf('prod_id')]]=$row[csf('prod_id')];
		}
			
		$sql_store = "SELECT a.id as prod_id,a.current_stock,b.store_id,b.lot,b.cons_qty as store_stock 
		from inv_store_wise_qty_dtls b,product_details_master a 
		where a.id=b.prod_id and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0]  and b.status_active=1 and a.id in(".implode(",",$prodIdArr).") and a.current_stock>0 and b.cons_qty>0 order by  a.id,b.lot ";
		$nameArray_store = sql_select($sql_store);
			
		//echo "10**=".$sql_store .'DDD';die;
		foreach ($nameArray_store as $row)
		{
			$prod_key=$row[csf('prod_id')]."_".$row[csf('store_id')];
			if($storeIdCheck_prod_id_arr[$prod_key]=='') 
			{
				$storeWiseStockArr[$prod_key]=$row[csf('store_stock')];
			}
		}
		unset($nameArray_store);
		foreach ($nameArray as $row)
		{			
			$batch_weight=$batch_wgt;
			$prod_key=$row[csf('prod_id')]."_".$row[csf('store_id')];
			// echo $prod_key.'<br>';
			$store_stock=$storeWiseStockArr[$prod_key]; 
			$batch_ratio=str_replace("'", "", $txt_batch_ratio);
			$total_liquor=$batch_ratio*1*$row[csf('liquor_ratio')]*$batch_weight;
			if($row[csf('dose_base')]==1)
			{
					$recipe_qnty = ($total_liquor*$row[csf('ratio')])/1000;
					//echo $txt_total_liquor.'*'.$row[csf('ratio')].'#'.$row[csf('prod_id')]."_".$row[csf('store_id')].'='.$recipe_qnty.'>'.$store_stock.'<br>';
			}
			else{
				$recipe_qnty = ($batch_weight*$row[csf('ratio')])/100;
			}
				
		 	//echo "10**".'='.$row[csf('prod_id')].'='.$txt_total_liquor.'*'.$row[csf('ratio')].'/1000='.$store_stock.'<br>';
			
			//echo $recipe_qnty.'>'.$store_stock.'<br>';//die;
			if($recipe_qnty>$store_stock) //Issue id=27998,Yr-2023a
			{
				echo "99**Recipe qty is over found than the stock=ProdId-".$row[csf('prod_id')].'**'.$row[csf('item_description')];die;
			}
		}


		// $sql_stock = "select id,current_stock from product_details_master where company_id='$data[0]' and item_category_id in(5,6,7,23) and status_active=1 and is_deleted=0 and id in($prod_id) and current_stock<=0";
		//echo $sql;
		// $item_stock = sql_select($sql_stock);
		// $current_stock = $item_stock[0][csf('current_stock')];

		// if ($current_stock <= 0) {
		// 	echo "1" . "_" . $item_stock[0][csf('id')];
		// } else {
		// 	echo "0_";
		// }
		exit();
	}

if ($action == "recipe_entry_print")
{
		extract($_REQUEST);
		$data = explode('*', $data);
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		//$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
	//	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
		$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
		$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
		$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
		$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');

		$sql_recipe_mst = "select id, labdip_no, company_id,working_company_id as w_com_id,location_id, recipe_description, batch_id,machine_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, remarks from pro_recipe_entry_mst where id='$data[1]'";
		$dataArray = sql_select($sql_recipe_mst);
		$batch_id=$dataArray[0][csf('batch_id')];
		$w_com_id=$dataArray[0][csf('w_com_id')];
		$company_id=$dataArray[0][csf('company_id')];
		$order_source=$dataArray[0][csf('order_source')];

		$order_array = return_library_array("select id, order_no from subcon_ord_dtls b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id' ", "id", "order_no");
		$po_arr = return_library_array("select b.id,b.po_number from wo_po_break_down b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id'", 'id', 'po_number');
		$sales_order_result = sql_select("select a.id,a.job_no,a.within_group,a.sales_booking_no from fabric_sales_order_mst a,pro_batch_create_mst b where a.id=b.sales_order_id and a.status_active=1 and a.is_deleted=0 and b.id='$batch_id'");
		$sales_arr = array();
		foreach ($sales_order_result as $sales_row) {
			$sales_arr[$sales_row[csf("id")]]["sales_order_no"] 	= $sales_row[csf("job_no")];
		}
		$batch_array = array();
		if ($db_type == 0) {
			$sql = "select a.id, a.batch_no,a.batch_against, a.entry_form, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty,b.is_sales from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
		} else if ($db_type == 2) {
				$sql = "select a.id, a.batch_no,a.batch_against,a.entry_form, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,  sum(b.batch_qnty) as batch_qnty,a.booking_without_order,a.booking_no  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.id='$data[4]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.booking_without_order,a.batch_against, a.batch_no,a.entry_form,a.booking_no,a.total_trims_weight order by a.id DESC";
		}
		// echo $sql;
		$result_sql = sql_select($sql);
		foreach ($result_sql as $row) {
			$order_no = '';
			$order_id = array_unique(explode(",", $row[csf("po_id")]));
			if ($row[csf("entry_form")] == 36) {
				$batch_type = "<b> SUBCONTRACT ORDER </b>";
				foreach ($order_id as $val) {
					if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];
				}
			} else {
				$batch_type = "<b> SELF ORDER </b>";
				foreach ($order_id as $val) {
					if($row[csf("is_sales")] == 1){
						$order_no = $sales_arr[$val]["sales_order_no"];
					}
					else
					{
						if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
					}
				}
			}
			$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
			$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
			$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
			$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")];
			$batch_array[$row[csf("id")]]['order'] = $order_no;
			$batch_array[$row[csf("id")]]['batch_type'] = $batch_type;
			$booking_without_order=$row[csf("booking_without_order")];
			$booking_no=$row[csf("booking_no")];
		}
		//echo $booking_without_order.'D';
		if($booking_without_order==1)
		{
			$booking_without_order = sql_select("select a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id,b.process_loss from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$data[0] and a.booking_no='$booking_no' and a.booking_type=4");
				foreach ($booking_without_order as $row) {
					$color_type_array[$row[csf('booking_no')]][$row[csf('body_part')]]['color_type_id'] = $row[csf('color_type_id')];
					$process_loss_array[$row[csf('booking_no')]][$row[csf('body_part')]]['color_type_id'] = $row[csf('process_loss')];
					//$booking_id = $row[csf('booking_no_prefix_num')];
					$buyer = $row[csf('buyer_id')];
				}
		}
		else
		{
			$booking_with_order = sql_select("select booking_no_prefix_num, buyer_id from wo_booking_mst where company_id=$data[0] and booking_no='$booking_no'");
				//$booking_id = $booking_with_order[0][csf('booking_no_prefix_num')];
				$buyer = $booking_with_order[0][csf('buyer_id')];
		}
		if($order_source==2)
		{
			 $sub_con_sql=" select b.mst_id as batch_id,c.job_no_mst,c.order_no,d.subcon_job,d.party_id from pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_dtls c,subcon_ord_mst d where a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id  and d.subcon_job=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id='$data[4]' ";
			//echo $sub_con_sql;

			$sub_con_arr =sql_select($sub_con_sql);

			$sub_buyer_arr = array();
			$sub_order_arr = array();
			foreach ($sub_con_arr as $row)
			{
				$sub_buyer_arr[$row[csf('batch_id')]] = $row[csf('job_no_mst')];
				$sub_order_arr[$row[csf('batch_id')]] = $row[csf('order_no')];
				$buyer = $row[csf('party_id')];
			}
		}
	//echo $sql;

		$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$w_com_id");
	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
		$group_id=$nameArray[0][csf('group_id')];
		?>
		<div style="width:930px; font-size:6px">
			<table width="930" cellspacing="0" align="right" border="0">
				<tr>
					<td colspan="6" align="center" style="font-size:x-large">
						<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_library[$w_com_id]; ?>
					</strong></td>
				</tr>
				<tr>
					<td colspan="6" align="center">
						<?

						foreach ($nameArray as $result) {
							?>
							Plot No: <? echo $result[csf('plot_no')]; ?>
							Level No: <? echo $result[csf('level_no')]; ?>
							Road No: <? echo $result[csf('road_no')]; ?>
							Block No: <? echo $result[csf('block_no')]; ?>
							Zip Code: <? echo $result[csf('zip_code')];
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:20px"><u><strong><? echo 'Owner Company : '.$company_library[$data[0]].'<br>'.$data[3]; ?></strong></u></td>
				</tr>
				<tr>
					<td width="130"><strong>System ID:</strong></td>
					<td width="175"><? echo $dataArray[0][csf('id')]; ?></td>
					<td width="130"><strong>Labdip No: </strong></td>
					<td width="175px"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
					<td width="130"><strong>Recipe Des.:</strong></td>
					<td width="175"><? echo $dataArray[0][csf('recipe_description')]; ?></td>
				</tr>
				<tr>
					<td><strong>Batch No:</strong></td>
					<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_no']; ?></td>
					<td><strong>Recipe Date:</strong></td>
					<td> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
					<td><strong>Order Source:</strong></td>
					<td><? echo $order_source[$dataArray[0][csf('order_source')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Buyer Name:</strong></td>
					<td><? if($dataArray[0][csf('buyer_id')]>0) echo $buyer_library[$dataArray[0][csf('buyer_id')]];else  echo $buyer_library[$buyer];?></td>
					<td><strong>Booking:</strong></td>
					<td><? echo $dataArray[0][csf('style_or_order')]; ?></td>
					<td><strong>Color:</strong></td>
					<td> <? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Color Range:</strong></td>
					<td><? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
                <!-- <td><strong>B/L Ratio:</strong></td> <td><? echo $dataArray[0][csf('batch_ratio')] . ':' . $dataArray[0][csf('liquor_ratio')]; ?></td>
                	<td><strong>Total Liq.:</strong></td><td> <? echo $dataArray[0][csf('total_liquor')]; ?></td>-->
                	<td><strong>Batch Weight:</strong></td>
                	<td><? $batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];echo $batch_weight; ?></td>
                	<td><strong>Trims Weight:</strong></td>
                	<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight']; ?></td>
                </tr>
                <tr>

                	<td><strong>Order No.:</strong></td>
                	<td>
                		<? if ($batch_array[$dataArray[0][csf('batch_id')]]['batch_against'] == 3) echo "Sample Without Order";
                		else echo $batch_array[$dataArray[0][csf('batch_id')]]['order']; ?></td>
                		<td><strong>Method:</strong></td>
                		<td><? echo $dyeing_method[$dataArray[0][csf('method')]]; ?></td>
                        <td><strong>Machine no:</strong></td>
                		<td><? echo $machine_arr[$dataArray[0][csf('machine_id')]]; ?></td>
                	</tr>
                	<tr>
                		<td><strong>Remarks:</strong></td>
                		<td colspan="5"><? echo $dataArray[0][csf('remarks')]; ?></td>
                	</tr>
                </table>
                <br>
                <?
				$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
				$j = 1;
				$entryForm = $entry_form_arr[$batch_id_qry[0]];
			?>
                <table width="930" cellspacing="0" align="right" class="rpt_table" border="1" style="font-size:16px; margin:5px;" rules="all">
				<thead bgcolor="#dddddd" align="center">

						<tr bgcolor="#CCCCFF">
							<th colspan="5" align="center"><strong>Fabrication</strong></th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="80">Dia/ W. Type</th>
							<th width="200">Constrution & Composition</th>
							<th width="70">Gsm</th>
							<th width="70">Dia</th>
						</tr>
				</thead>
				<tbody>
					<?
						foreach ($batch_id_qry as $b_id) {
							 $batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
							$result_batch_query = sql_select($batch_query);
							foreach ($result_batch_query as $rows) {
								if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								$fabrication_full = $rows[csf("item_description")];
								$fabrication = explode(',', $fabrication_full);
					?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td align="center"><? echo $j; ?></td>
									<td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>
									<td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
									<td align="center"><? echo $fabrication[2]; ?></td>
									<td align="center"><? echo $fabrication[3]; ?></td>
								</tr>
					<?
								$j++;
							}
						}
					?>
				</tbody>
			</table>
            <br> <br> <br>
                <div style="width:100%;">
                	<table align="right" style="margin:5px;" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
                		<thead bgcolor="#dddddd" align="center">
                			<th width="30">SL</th>
                			<th width="110">Item Cat.</th>
                			<th width="110">Product ID</th>
                			<th width="200">Item Group</th>
                			<th width="220">Item Description</th>
                			<th width="80">Item Lot</th>
                			<th width="50">UOM</th>
                			<th width="100">Dose Base</th>
                			<th width="100">Ratio</th>
                			<th width="">Comments</th>
                		</thead>
                		<?
                		$i = 1;
                		$j = 1;
                		$mst_id = $data[1];
                		$com_id = $data[0];


                		$process_array = array();
                		$sub_process_data_array = array();
                		$sub_process_remark_array = array();
                		$sql_ratio = "select id, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by id";
                		$nameArray = sql_select($sql_ratio);
                		foreach ($nameArray as $row) {
                			if (!in_array($row[csf("sub_process_id")], $process_array)) {
                				$process_array[] = $row[csf("sub_process_id")];
                				$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
                			}
                			$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
                			$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];


                		}

                		if ($db_type == 2) {
                			/*$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio from product_details_master a, pro_recipe_entry_dtls b where a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$com_id' and a.item_category_id in(5,6,7) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 order by b.sub_process_id, b.seq_no";*/

                			$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0  )
                			union
                			(
                			select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
                		)  order by seq_no,sub_process_id";
                	} else if ($db_type == 0) {
                		$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio, b.seq_no as seq_no from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 )
                		union
                		(
                		select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio,b.seq_no as seq_no from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
                	)  ";
                }
				// echo $sql;
                $sql_result = sql_select($sql);

                foreach ($sql_result as $row) {
                	$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")]. "**" . $row[csf("current_stock")] . "**" . $row[csf("prod_id")] . "$$$";

                }
				//var_dump($sub_process_data_array);
                foreach ($process_array as $process_id) {
                	$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];

                	$liquor_ratio_process = $sub_process_remark_array[$process_id]['liquor_ratio'];
                	$remark = $sub_process_remark_array[$process_id]['remark'];
                	if ($remark == '') $remark = ''; else $remark .= $remark . ', ';
                	$tot_ratio=1.5;
                	$leveling_water=$batch_weight*($liquor_ratio_process-$tot_ratio) ;

                	?>
                	<tr bgcolor="#EEEFF0">
                		<td colspan="9" align="left"><b>Sub Process
                			Name:- <? echo $dyeing_sub_process[$process_id] . ', ' . $remark .' Total Liquor(ltr): '.$liquor_ratio.', '. 'Levelling  Water(Ltr): '.number_format($leveling_water,2,'.',''); ?></b>
                		</td>
                	</tr>
                	<?
                	$tot_ratio = 0;
                	//$sub_process_data = array_filter(explode("@@@",$sub_process_data_array2[$process_id]));
					//$sub_process_data = explode("$$$", substr($sub_process_data_array[$process_id], 0, -1));
					$sub_process_dataArr=rtrim($sub_process_data_array[$process_id],'$$$');
					$sub_process_data = explode("$$$", $sub_process_dataArr);
					//echo count($sub_process_data).'='.$process_id.'<br>';
                	foreach ($sub_process_data as $data) {
                		$data = explode("**", $data);
                		$current_stock = $data[13];
                		$current_stock_check=number_format($current_stock,7,'.','');
                		//if($process_id==93 || $process_id==94 || $process_id==95 || $process_id==96 || $process_id==97 || $process_id==98 || $process_id == 140 || $process_id == 141 || $process_id == 142 || $process_id == 143)
						if(in_array($process_id,$subprocessForWashArr))
						{
							$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];

	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
	                			<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td><? echo $i; ?></td>
	                				<td><p><? echo $item_category[$item_category_id]; ?></p></td>
	                				<td><p><? echo $prod_id; ?></p></td>
	                				<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
	                				<td><p><? echo $item_description; ?></p></td>
	                				<td><p><? echo $item_lot; ?></p></td>
	                				<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
	                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
	                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
	                				<td align="right"><? echo $comments; ?>&nbsp;</td>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
	                			$i++;
						}
						else
						{


	                		if($current_stock_check>0)
	                		{
	                			$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];

	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
	                			<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td><? echo $i; ?></td>
	                				<td><p><? echo $item_category[$item_category_id]; ?></p></td>
	                				<td><p><? echo $prod_id; ?></p></td>
	                				<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
	                				<td><p><? echo $item_description; ?></p></td>
	                				<td><p><? echo $item_lot; ?></p></td>
	                				<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
	                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
	                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
	                				<td align="right"><? echo $comments; ?>&nbsp;</td>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
	                			$i++;
	                		}
                		}

                	}
                	?>
                	<tr class="tbl_bottom">
                		<td align="right" colspan="8"><strong>Sub Process Total</strong></td>
                		<td align="right"><? echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                	</tr>
                	<?
                }
                ?>

                <tr class="tbl_bottom">
                	<td align="right" colspan="8"><strong>Grand Total</strong></td>
                	<td align="right"><? echo number_format($grand_tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                    <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                </tr>
            </table>
            <br>
            <?
            echo signature_table(62, $com_id, "1030px");
            ?>
        </div>
    </div>
    <?
}

if($action == "recipe_entry_print_2")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_id = $data[0];
	$update_id = $data[1];
	$txt_labdip_no = $data[2];
	$txt_yarn_lot = $data[3];
	$txt_brand= $data[4];
	$txt_count= $data[5];
	$txt_pick_up= $data[6];
	$surpls_solution= $data[7];
	$batch_id= $data[8];
	$sub_process_id = $data[9];
	$report_title = $data[10];

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	//$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
	//$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');


	$batch_array = array();
	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no,a.batch_against, a.entry_form, a.batch_weight, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$company_id'  and a.id=$batch_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
	} else if ($db_type == 2) {
		 $sql = "select a.id, a.batch_no,a.batch_against,a.entry_form, a.batch_weight, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,  sum(b.batch_qnty) as batch_qnty  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$company_id' and a.id=$batch_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.batch_against, a.batch_no,a.entry_form, a.batch_weight, a.total_trims_weight order by a.id DESC";
	}
	//echo $sql;

	$result_sql = sql_select($sql);
	foreach ($result_sql as $row) {
		$order_no = '';
		$order_id = array_unique(explode(",", $row[csf("po_id")]));
		if ($row[csf("entry_form")] == 36) {
			$batch_type = "<b> SUBCONTRACT ORDER </b>";
			foreach ($order_id as $val) {
				if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];
			}
		} else {
			$batch_type = "<b> SELF ORDER </b>";
			foreach ($order_id as $val) {
				if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
			}
		}
		$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
		$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
		$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
		$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")];
		$batch_array[$row[csf("id")]]['batch_weight'] = $row[csf("batch_weight")];
		$batch_array[$row[csf("id")]]['order'] = $order_no;
		$batch_array[$row[csf("id")]]['batch_type'] = $batch_type;
		$batch_nos=$row[csf("batch_no")];
	}

	$sql_recipe_mst = "select id, labdip_no, recipe_type,company_id,working_company_id as w_com_id,location_id, recipe_description, batch_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, remarks from pro_recipe_entry_mst where id='$update_id'";

	$dataArray = sql_select($sql_recipe_mst);
	//var_dump( $dataArray);
	$w_com_id=$dataArray[0][csf('w_com_id')];
	$company_id=$dataArray[0][csf('company_id')];

	$recipe_type=$dataArray[0][csf('recipe_type')];
	$order_source=$dataArray[0][csf('order_source')];
	$batch_id=$dataArray[0][csf('batch_id')];
	 $order_array = return_library_array("select id, order_no from subcon_ord_dtls b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id' ", "id", "order_no");
	$po_arr = return_library_array("select b.id,b.po_number from wo_po_break_down b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id'", 'id', 'po_number');

	if($booking_without_order==1)
		{
			$booking_without_order = sql_select("select a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id,b.process_loss from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$company_id and a.booking_no='$booking_no' and a.booking_type=4");
				foreach ($booking_without_order as $row) {
					//$color_type_array[$row[csf('booking_no')]][$row[csf('body_part')]]['color_type_id'] = $row[csf('color_type_id')];
					//$process_loss_array[$row[csf('booking_no')]][$row[csf('body_part')]]['color_type_id'] = $row[csf('process_loss')];
					//$booking_id = $row[csf('booking_no_prefix_num')];
					$buyer = $row[csf('buyer_id')];
				}
		}
		else
		{
			$booking_with_order = sql_select("select booking_no_prefix_num, buyer_id from wo_booking_mst where company_id=$company_id and booking_no='$booking_no'");
				//$booking_id = $booking_with_order[0][csf('booking_no_prefix_num')];
				$buyer = $booking_with_order[0][csf('buyer_id')];
		}

		//echo $order_source.'DDDDDDD';
	if($order_source==2)
	{
		 $sub_con_sql=" select b.mst_id as batch_id,c.job_no_mst,c.order_no,d.subcon_job,d.party_id from pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_dtls c,subcon_ord_mst d where a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id  and d.subcon_job=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id='$batch_id' ";
		//echo $sub_con_sql;

		$sub_con_arr =sql_select($sub_con_sql);

		$sub_buyer_arr = array();
		$sub_order_arr = array();
		foreach ($sub_con_arr as $row)
		{
			$sub_buyer_arr[$row[csf('batch_id')]] = $row[csf('job_no_mst')];
			$sub_order_arr[$row[csf('batch_id')]] = $row[csf('order_no')];
			$buyer = $row[csf('party_id')];
		}
	}


	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$w_com_id");

	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	$group_id=$nameArray[0][csf('group_id')];

	//total_solution = batch_weight*pickup/100+surplus
	//total_solution = batch_weight+surplus ==> modified by shehab
	//$total_solution = ($batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']+$surpls_solution);
	 $total_solution = ((($batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']*$txt_pick_up)/100)+$surpls_solution);

	//solution_amount = (total_solution/5)*4;
	$solution_amount = ($total_solution/5)*4;

	//Alkali_Solution = total_solution/5;
	$alkali_solution_amount = $total_solution/5;

	$construction_sql = "
	select a.id,
	a.mst_id,
	a.prod_id,
	a.item_description,
	a.gsm,
	b.detarmination_id,
	b.item_category_id,
	b.unit_of_measure,
	c.construction
	from pro_batch_create_dtls a,
	product_details_master b,
	lib_yarn_count_determina_mst c
	where   a.prod_id = b.id
	and b.detarmination_id = c.id
	and b.item_category_id = 13
	and a.is_deleted = 0
	and a.status_active = 1
	and b.is_deleted = 0
	and b.status_active = 1
	and c.is_deleted = 0
	and c.status_active = 1
	and a.mst_id = $batch_id

	order by a.id";
	  //echo $construction_sql;
	$const_result = sql_select($construction_sql);
	  //var_dump ($const_result);
	foreach ($const_result as $row) {
		$construction_data["mst_id"] = $row[csf("mst_id")];
		$construction_data["prod_id"] = $row[csf("prod_id")];
		$construction_data["item_description"] = $row[csf("item_description")];
		$construction_data["uom"] = $row[csf("unit_of_measure")];
		$construction_data["fabric_type"] = $row[csf("construction")];
		$construction_data["gsm"] = $row[csf("gsm")];

	}

	$composition_arra = explode(",",$construction_data["item_description"]);

		//Total Length = (Batch Weight/GSM/width)*1000;
	//$total_length = ((($batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']/$composition_arra[2])/$composition_arra[3])*1000);
	$width_new=$composition_arra[3]/39.37;
	$total_length = ($batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']*1000)/($width_new*$composition_arra[2]);
	//echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_weight'].'='.$width_new.'='.$composition_arra[2];
	?>
	<div style="width:1000px; font-size:6px">
		<table width="1000" cellspacing="0" align="center" border="0" role="all">
			<tr>
				<td colspan="6" align="center" style="font-size:x-large">
					<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_library[$w_com_id]; ?>
				</strong></td>
			</tr>
			<tr>
				<td colspan="6" style="font-size:x-large; text-align:center;"><u><strong><? echo $company_library[$data[0]]; //.data[3]; ?></strong></u></td>
			</tr>
			<tr>
				<td colspan="6" align="center">
					<?

					foreach ($nameArray as $result) {
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]; ?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')]; ?>
						Zip Code: <? echo $result[csf('zip_code')];
					}
					?>
				</td>
			</tr>
		</table>
		<table cellspacing="0" align="center" border="1" width="1000" style="margin-top:20px;" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<th colspan="8" align="center" style="font-size:20px"><u><strong>Recipe Calculation for CPB Bulk</strong></u></th>
				</tr>
			</thead>
			<tr>
				<td width="180" align="left"><strong>Labdip No </strong></td>
				<td width="220px" align="center"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
				<td width="150" align="left"><strong>Color Name</strong></td>
				<td colspan="2" align="center"> <? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
				<td width="130" align="left"><strong>Date</strong></td>
				<td colspan="2" align="center"> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
			</tr>
			<tr bgcolor="#dddddd" >
				<td width="180" align="left"><strong>Buyer</strong></td>
				<td width="220" align="center"><? if($dataArray[0][csf('buyer_id')]>0) echo $buyer_library[$dataArray[0][csf('buyer_id')]];else  echo $buyer_library[$buyer];?></td>
				<td width="150" align="left"><strong>Shade Type</strong></td>
				<td colspan="2" align="center"><? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
				<td width="130" rowspan="3" align="left"><strong>PH</strong></td>
				<td width="150" align="left"><strong>Dyes Solution</strong></td>
				<td width="60" align="center"></td>
			</tr>
			<tr>
				<td width="180" align="left"><strong>Order No</strong></td>
				<td width="220" align="center">
					<? if ($batch_array[$dataArray[0][csf('batch_id')]]['batch_against'] == 3) echo "Sample Without Order";
					else echo $batch_array[$dataArray[0][csf('batch_id')]]['order']; ?></td>
					<td colspan="3" align="center"><strong>Fabric Specifications</strong></td>
					<td width="150" align="left"><strong>Alkali Solution</strong></td>
					<td width="60" align="center"></td>
				</tr>
				<tr bgcolor="#dddddd" >
					<td width="180" align="left"><strong>Fabric Type</strong></td>
					<td width="220" align="center"> <? echo $construction_data["fabric_type"];//need fabric type data ?></td>
					<td width="150" align="left"><strong>Width</strong></td>
					<td width="90" align="center" title="Width/39.37"><? echo number_format($composition_arra[3]/39.37,4);?></td>
					<td width="90" align="center">M<? //echo $unit_of_measurement[$construction_data["uom"]]?></td>
					<td width="150" align="left"><strong>Dye Lqour</strong></td>
					<td width="60" align="center"></td>

				</tr>
				<tr>
					<td width="180" align="left"><strong>Composition</strong></td>
					<td width="220" align="center"> <? echo $composition_arra[1]; ?></td>
					<td width="150" align="left"><strong>GSM</strong></td>
					<td width="90" align="center"><? echo $composition_arra[2];?></td>
					<td width="90" align="center">G<? //echo $unit_of_measurement[$construction_data["uom"]];?></td>
					<td width="150" align="left"><strong>Yarn Lot</strong></td>
					<td colspan="2" align="center"><? echo $txt_yarn_lot;?></td>
				</tr>
				<tr bgcolor="#dddddd" >
					<td width="180" align="left"><strong>Padder Pressure:</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left" title="Batch Weight*1000/(Width*GSM)"><strong>Total Length</strong></td>
					<td width="90" align="center"><? echo number_format($total_length,4); ?></td>
					<td width="90" align="center">M<? //echo $unit_of_measurement[$construction_data["uom"]];?></td>
					<td width="150" align="left"><strong>Yarn Brand</strong></td>
					<td colspan="2" align="center"><? echo $txt_brand;?></td>
				</tr>
				<tr>
					<td width="180" align="left"><strong>M/M For Body Fabric</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Batch weight</strong></td>
					<td width="90" align="center"><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']; ?></td>
					<td width="90" align="center"><? echo $unit_of_measurement[$construction_data["uom"]]; ?></td>
					<td width="150" align="left"><strong>Dye Lot No</strong></td>
					<td colspan="2" align="center"></td>

				</tr>
				<tr bgcolor="#dddddd" >
					<td width="180" align="left"><strong>M/M For Rib Fabric</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Pick Up</strong></td>
					<td width="90" align="center"><? echo $txt_pick_up;?></td>
					<td width="90" align="center">%</td>
                    <td width="150" align="left"><strong>Batch No</strong></td>
					<td colspan="2" align="center"><? echo $batch_nos;?></td>
				</tr>
				<tr>
					<td width="180" align="left"><strong>Rotation Hours</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Surplus Solution</strong></td>
					<td width="90" align="center"><? echo $surpls_solution;?></td>
					<td width="90" align="center">L<? //echo $unit_of_measurement[$construction_data["uom"]];?></td>
				</tr>
				<tr bgcolor="#dddddd" >
					<td width="180" align="left"><strong>Padding Complition Time</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Total Solution</strong></td>
					<td width="90" align="center" title="(Batch Wgt*PickUp/100)+Surplus  Solution"><? echo number_format($total_solution,4); ?></td>
					<td width="90" align="center">L<? //echo $unit_of_measurement[$construction_data["uom"]];?></td>
				</tr>
				<tr>
					<td width="180" align="left"><strong>Washing Time</strong></td>
					<td width="220" align="center"> </td>

				</tr>
			</table>
			<br/><br/>
			<div style="width: 600px; float: left; margin-top:15px; margin-right:10px;">
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="600" class="rpt_table">
					<thead bgcolor="#dddddd" align="center">
						<tr>
							<th colspan="5">Dyes Solution</th>
						</tr>
					</thead>
					<tr>
						<td width="130" align="center">Solution Amount</td>
						<td colspan="3" align="center"><? echo number_format($solution_amount,0);?></td>
						<td align="center"><? echo $unit_of_measurement[$construction_data["uom"]]; ?></td>
					</tr>
					<tr bgcolor="#dddddd" >
						<td width="130" align="center">Particulars</td>
						<td width="170" align="center">Brand Name</td>
						<td width="50" align="center">GPL</td>
						<td width="90" align="center">Amount</td>
						<td width="60" align="center"></td>
					</tr>
					<?

					//if($sub_process_id==93 || $sub_process_id==94 || $sub_process_id==95 || $sub_process_id==96 || $sub_process_id==97 || $sub_process_id==98 || $sub_process_id == 140 || $sub_process_id == 141 || $sub_process_id == 142 || $sub_process_id == 143)
					if(in_array($sub_process_id, $subprocessForWashArr))
					{
						$ratio_cond="";
					}
					else
					{
						$ratio_cond=" and ratio>0 ";
					}
					//AND a.sub_process_id = $sub_process_id //Remove it Faisal-30-01-2020

					$recipeData=sql_select("SELECT
						a.id,
						a.prod_id,
						a.ratio,
						b.item_category_id,
						b.item_description,
						b.unit_of_measure
						FROM pro_recipe_entry_dtls a, product_details_master b
						WHERE     a.prod_id = b.id
						AND a.mst_id = $update_id

						AND a.status_active = 1
						AND a.is_deleted = 0
						AND b.status_active = 1
						AND b.is_deleted = 0
						$ratio_cond
						ORDER BY seq_no");

					$tot_dyes_soluton_item_amount=0;
					$recipe_prod_id_arr=array();
					$prod_id_chk_arr=array(9714,89815,80530,9716,9704,100889,16274,16863);
					foreach($recipeData as $row)
					{
						if((in_array($row[csf('prod_id')],$prod_id_chk_arr)) ||  $row[csf('item_category_id')] == 6) {
							$recipe_data_arr[$row[csf('prod_id')]]['item_category_id'] = $row[csf('item_category_id')];
							$recipe_data_arr[$row[csf('prod_id')]]['item_description']=$row[csf('item_description')];
							$recipe_data_arr[$row[csf('prod_id')]]['uom']=$row[csf('unit_of_measure')];
							$recipe_data_arr[$row[csf('prod_id')]]['ratio']=$row[csf('ratio')];
							//$recipe_data_arr[$row[csf('prod_id')]]['recipe_type']=$row[csf('recipe_type')];
						}
						if((!in_array($row[csf('prod_id')],$prod_id_chk_arr)) && $row[csf('item_category_id')] == 5 || $row[csf('item_category_id')] == 7 || $row[csf('item_category_id')] == 23 ){
							$recipe_data_arr[$row[csf('prod_id')]]['item_category_id']=$row[csf('item_category_id')];
							$recipe_data_arr[$row[csf('prod_id')]]['item_description']=$row[csf('item_description')];
							$recipe_data_arr[$row[csf('prod_id')]]['uom']=$row[csf('unit_of_measure')];
							$recipe_data_arr[$row[csf('prod_id')]]['ratio']=$row[csf('ratio')];
							//$recipe_data_arr[$row[csf('prod_id')]]['recipe_type']=$row[csf('recipe_type')];

						}
						$recipe_prod_id_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];

						//Dyes Solution total amount = (Total Solution * GPL)/1000 {if uom is kg else if uom is % then divided by 100}
						if($recipe_data_arr[$row[csf('prod_id')]]['uom'] == 12){
							$dyes_soluton_item_amount = ($total_solution * $recipe_data_arr[$row[csf('prod_id')]]['ratio'])/1000;
							$dyes_soluton_item_total += $dyes_soluton_item_amount;
						}else{
							$dyes_soluton_item_amount = (($total_solution * $recipe_data_arr[$row[csf('prod_id')]]['ratio'])/100);
							$dyes_soluton_item_total += $dyes_soluton_item_amount;
						}



						if((in_array($row[csf('prod_id')],$prod_id_chk_arr)) || $recipe_data_arr[$row[csf('prod_id')]]['item_category_id'] == 6){
							//Required_water = solution_amount - (sum_of_particulars_amount)
							//$required_water = $solution_amount - $dyes_soluton_item_total;

							$tot_dyes_soluton_item_amount+=$dyes_soluton_item_amount;
							?>
							<tr>
								<td align="center"  width="130"><? echo $item_category[$recipe_data_arr[$row[csf('prod_id')]]['item_category_id']];?></td>
								<td align="center"  width="170"><? echo $recipe_data_arr[$row[csf('prod_id')]]['item_description'];?></td>
								<td  align="center" title="ProdID=<? echo $row[csf('prod_id')]; ?>" width="50"><? echo $recipe_data_arr[$row[csf('prod_id')]]['ratio'];?></td>
								<td align="center"  width="90"><? echo $dyes_soluton_item_amount; ?></td>
								<td align="center"  width="60"><? echo $unit_of_measurement[$recipe_data_arr[$row[csf('prod_id')]]['uom']]; ?></td>
							</tr>
							<?
						}
					}
					?>
					<tr bgcolor="#dddddd">
						<td  align="center" colspan="2">Required Water</td>
						<td align="center"  colspan="2" title="Dyes Solution/Amount"><? $required_water=$solution_amount-$tot_dyes_soluton_item_amount; echo number_format($required_water,4);?></td>
						<td align="center" >L<? //echo $unit_of_measurement[$recipe_data_arr[$row[csf('prod_id')]]['uom']]; ?></td>
					</tr>

				</table>
			</div>
			<div style="width: 370px; float: left; margin-top:15px; margin-left:3px;">
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="360" class="rpt_table">
					<thead>
						<tr bgcolor="#dddddd" align="center">
							<th colspan="4">Alkali Solution</th>
						</tr>
					</thead>
					<tr>
						<td width="110"></td>
						<td width="110" title="<? echo $alkali_solution_amount;?>"  align="center"><? echo number_format($alkali_solution_amount,0); ?></td>
						<td width="80" align="center"><? echo $unit_of_measurement[$recipe_data_arr[$row[csf('prod_id')]]['uom']]; ?></td>
						<td width="60"></td>
					</tr>
					<tr bgcolor="#dddddd" >
						<td align="center"  width="110"><strong>Chemicals</strong></td>
						<td align="center"  width="110"><strong>Recipe GPL</strong></td>
						<td align="center"  width="110"><strong>Amount</strong></td>
						<td align="center"  width="70"></td>
					</tr>
					<?
			//var_dump($recipe_data_arr);
			$tot_alkali_item_soluton_amount=0;
					foreach ($recipe_data_arr as $key => $value) {
						$recipe_alkali_data_arr[$key]['item_category_id'] = $value['item_category_id'];
						$recipe_alkali_data_arr[$key]['item_description'] = $value['item_description'];
						$recipe_alkali_data_arr[$key]['uom'] = $value['unit_of_measure'];
						$recipe_alkali_data_arr[$key]['ratio'] = $value['ratio'];
						//echo $recipe_type.'DDDDDDDD';

						if($recipe_alkali_data_arr[$key]['uom'] == 12){
							$alkali_item_soluton_amount = ($total_solution * $recipe_alkali_data_arr[$key]['ratio'])/1000;
							$alkali_item_soluton_total += $alkali_item_soluton_amount;
						}else{
							if($recipe_type==2)//Recipe Type- CBP
							{
								//echo $total_solution.'DDDDDDDDD';
								$alkali_item_soluton_amount = ($total_solution * $recipe_alkali_data_arr[$key]['ratio'])/1000;
							}
							else
							{
								$alkali_item_soluton_amount = ($total_solution * $recipe_alkali_data_arr[$key]['ratio'])/100;
							}
							$alkali_item_soluton_total += $alkali_item_soluton_amount;
						}
					//alkali_water = alkali_solution_amount - (sum_of_chemicals_amount)
						//$water = $alkali_solution_amount - $alkali_item_soluton_total;


				if((!in_array($key,$prod_id_chk_arr)) && ($recipe_alkali_data_arr[$key]['item_category_id'] == 5 || $recipe_alkali_data_arr[$key]['item_category_id'] == 7 || $recipe_alkali_data_arr[$key]['item_category_id'] == 23))
						{
							$tot_alkali_item_soluton_amount+=$alkali_item_soluton_amount;
							?>
							<tr>
								<td width="110"><? echo $recipe_alkali_data_arr[$key]['item_description'];?></td>
								<td align="center" title="ProdID=<? echo $key; ?>" width="110"><? echo $recipe_alkali_data_arr[$key]['ratio'];?></td>
								<td align="right"  width="110"><? echo $alkali_item_soluton_amount; ?></td>
								<td align="center"  width="70"><? echo $unit_of_measurement[$recipe_alkali_data_arr[$key]['uom']]; ?></td>
							</tr>
							<?
						}
					}
					?>
					<tr bgcolor="#dddddd">
						<td align="center">Water</td>
						<td align="center" colspan="2" title="Alkali Solution/Amount"><? $water = $alkali_solution_amount - $tot_alkali_item_soluton_amount;echo number_format($water,4);?></td>
						<td align="center">L<? //echo $unit_of_measurement[$recipe_data_arr[$row[csf('prod_id')]]['uom']]; ?></td>
					</tr>
				</table>
			</div>
		</div>
		<?
}


if ($action == "recipe_entry_print_3")
{
		extract($_REQUEST);
		$data = explode('*', $data);
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		//$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
		//$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
		$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
		$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
		$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
		$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");

		$batch_array = array();
		if ($db_type == 0) {
			$sql = "select a.id, a.batch_no,a.batch_against, a.entry_form, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
		} else if ($db_type == 2) {
			$sql = "select a.id, a.batch_no,a.batch_against,a.entry_form, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,  sum(b.batch_qnty) as batch_qnty  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.batch_against, a.batch_no,a.entry_form,a.total_trims_weight order by a.id DESC";
		}
		// echo $sql;
		$result_sql = sql_select($sql);
		foreach ($result_sql as $row) {
			$order_no = '';
			$order_id = array_unique(explode(",", $row[csf("po_id")]));
			if ($row[csf("entry_form")] == 36) {
				$batch_type = "<b> SUBCONTRACT ORDER </b>";
				foreach ($order_id as $val) {
					if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];
				}
			} else {
				$batch_type = "<b> SELF ORDER </b>";
				foreach ($order_id as $val) {
					if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
				}
			}
			$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
			$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
			$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
			$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")];
			$batch_array[$row[csf("id")]]['order'] = $order_no;
			$batch_array[$row[csf("id")]]['batch_type'] = $batch_type;
		}


		$sql_recipe_mst = "select id, labdip_no, company_id,working_company_id as w_com_id,location_id, recipe_description, machine_id,batch_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, remarks from pro_recipe_entry_mst where id='$data[1]'";
		$dataArray = sql_select($sql_recipe_mst);
		$w_com_id=$dataArray[0][csf('w_com_id')];
		$company_id=$dataArray[0][csf('company_id')];
		$batch_id=$dataArray[0][csf('batch_id')];
	    $order_array = return_library_array("select id, order_no from subcon_ord_dtls b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id' ", "id", "order_no");
	   $po_arr = return_library_array("select b.id,b.po_number from wo_po_break_down b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id'", 'id', 'po_number');

		$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$w_com_id");
	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
		$group_id=$nameArray[0][csf('group_id')];
		$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
		$fabrication = array();
		foreach ($batch_id_qry as $b_id) {
			 $batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
			$result_batch_query = sql_select($batch_query);
			foreach ($result_batch_query as $rows) {
				$fabrication_full = $rows[csf("item_description")];
				$fabrication = explode(',', $fabrication_full);
			}
		}
		$machine_id = $dataArray[0][csf('machine_id')];
		$machine_details_arr = sql_select("select no_of_feeder, cycle_time from lib_machine_name where id=$machine_id");
		$cycle_time = $machine_details_arr[0][csf('cycle_time')];
		$no_of_tube = $machine_details_arr[0][csf('no_of_feeder')];
		?>
		<div style="width:930px; font-size:6px">
			<table width="930" cellspacing="0" align="right" border="0">
				<tr>
					<td colspan="6" align="center" style="font-size:x-large">
						<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_library[$w_com_id]; ?>
					</strong></td>
				</tr>
				<tr>
					<td colspan="6" align="center">
						<?

						foreach ($nameArray as $result) {
							?>
							Plot No: <? echo $result[csf('plot_no')]; ?>
							Level No: <? echo $result[csf('level_no')]; ?>
							Road No: <? echo $result[csf('road_no')]; ?>
							Block No: <? echo $result[csf('block_no')]; ?>
							Zip Code: <? echo $result[csf('zip_code')];
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:20px"><u><strong><? echo 'Owner Company : '.$company_library[$data[0]].'<br>'.$data[3]; ?></strong></u></td>
				</tr>
				<tr>
					<td width="130"><strong>System ID:</strong></td>
					<td width="175"><? echo $dataArray[0][csf('id')]; ?></td>
					<td width="130"><strong>Labdip No: </strong></td>
					<td width="175px"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
					<td width="130"><strong>Recipe Des.:</strong></td>
					<td width="175"><? echo $dataArray[0][csf('recipe_description')]; ?></td>
				</tr>
				<tr>
					<td><strong>Batch No:</strong></td>
					<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_no']; ?></td>
					<td><strong>Recipe Date:</strong></td>
					<td> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
					<td><strong>Order Source:</strong></td>
					<td><? echo $order_source[$dataArray[0][csf('order_source')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Buyer Name:</strong></td>
					<td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
					<td><strong>Booking:</strong></td>
					<td><? echo $dataArray[0][csf('style_or_order')]; ?></td>
					<td><strong>Color:</strong></td>
					<td> <? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Color Range:</strong></td>
					<td><? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
                <!-- <td><strong>B/L Ratio:</strong></td> <td><? echo $dataArray[0][csf('batch_ratio')] . ':' . $dataArray[0][csf('liquor_ratio')]; ?></td>
                	<td><strong>Total Liq.:</strong></td><td> <? echo $dataArray[0][csf('total_liquor')]; ?></td>-->
                	<td><strong>Batch Weight:</strong></td>
                	<td><? $batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];echo $batch_weight; ?></td>
                	<td><strong>Trims Weight:</strong></td>
                	<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight']; ?></td>
                </tr>
                <tr>

	                	<td><strong>Order No.:</strong></td>
	                	<td>
	                		<? if ($batch_array[$dataArray[0][csf('batch_id')]]['batch_against'] == 3) echo "Sample Without Order";
	                		else echo $batch_array[$dataArray[0][csf('batch_id')]]['order']; ?>

                		</td>
                		<td><strong>Method:</strong></td>
                		<td><? echo $dyeing_method[$dataArray[0][csf('method')]]; ?></td>
                		<td><strong>Rope Length:</strong></td>
                		<td><? $rope_length = ($batch_weight*1000)/($fabrication[2]*$fabrication[3]*2.54/100); echo number_format($rope_length,6); ?></td>
                	</tr>
                	<tr>
                		<td><strong>Machine no:</strong></td>
                		<td><? echo $machine_arr[$dataArray[0][csf('machine_id')]]; ?></td>
                		<td><strong>Cycle Time:</strong></td>
                		<td><? echo $cycle_time; ?></td>
                		<td><strong>No.Tube:</strong></td>
                		<td><? echo $no_of_tube; ?></td>
                	</tr>
                	<tr>
                		<td><strong>Elongation:</strong></td>
                		<td><? $elongation = $rope_length + ($rope_length*20)/100; echo number_format($elongation,6); ?></td>
                		<td><strong>Winch Speed:</strong></td>
                		<td><? if($cycle_time=='' || $no_of_tube==''){ $winch_speed = 0;}else{ $winch_speed = ($elongation/$cycle_time)/$no_of_tube; } echo number_format($winch_speed,6); ?></td>
                	</tr>
                	<tr>
                		<td><strong>Remarks:</strong></td>
                		<td colspan="5"><? echo $dataArray[0][csf('remarks')]; ?></td>
                	</tr>
                </table>
                <br>
                <?
				$j = 1;
				$entryForm = $entry_form_arr[$batch_id_qry[0]];
			?>
                <table width="930" cellspacing="0" align="right" class="rpt_table" border="1" style="font-size:16px; margin:5px;" rules="all">
				<thead bgcolor="#dddddd" align="center">

						<tr bgcolor="#CCCCFF">
							<th colspan="5" align="center"><strong>Fabrication</strong></th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="80">Dia/ W. Type</th>
							<th width="200">Constrution & Composition</th>
							<th width="70">Gsm</th>
							<th width="70">Dia</th>
						</tr>
				</thead>
				<tbody>
					<?
						foreach ($batch_id_qry as $b_id) {
							 $batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
							$result_batch_query = sql_select($batch_query);
							foreach ($result_batch_query as $rows) {
								if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								$fabrication_full = $rows[csf("item_description")];
								$fabrication = explode(',', $fabrication_full);
					?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td align="center"><? echo $j; ?></td>
									<td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>
									<td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
									<td align="center"><? echo $fabrication[2]; ?></td>
									<td align="center"><? echo $fabrication[3]; ?></td>
								</tr>
					<?
								$j++;
							}
						}
					?>
				</tbody>
			</table>
            <br> <br> <br>
            <div style="width:100%;">
        	<table align="right" style="margin:5px;" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
        		<thead bgcolor="#dddddd" align="center">
        			<th width="30">SL</th>
        			<th width="110">Item Cat.</th>
        			<th width="110">Product ID</th>
        			<th width="200">Item Group</th>
        			<th width="220">Item Description</th>
        			<th width="80">Item Lot</th>
        			<th width="50">UOM</th>
        			<th width="100">Dose Base</th>
        			<th width="100">Ratio</th>
        			<th width="">Comments</th>
        		</thead>
        		<?
        		$i = 1;
        		$j = 1;
        		$mst_id = $data[1];
        		$com_id = $data[0];


        		$process_array = array();
        		$sub_process_data_array = array();
        		$sub_process_remark_array = array();
        		$sql_ratio = "select id,sub_seq, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by sub_seq";
        		$nameArray = sql_select($sql_ratio);
        		foreach ($nameArray as $row) 
        		{
        			if (!in_array($row[csf("sub_process_id")], $process_array)) {

        				$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
						$process_array[] = $row[csf("sub_process_id")];
        			}
        			if ($processChkArr[$row[csf("sub_process_id")]]=='') {
						$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
						$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
						$processChkArr[$row[csf("sub_process_id")]]=$row[csf("sub_process_id")];
						}
        		}
				//print_r($sub_process_remark_array);

        		if ($db_type == 2) 
        		{
					/*$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio from product_details_master a, pro_recipe_entry_dtls b where a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$com_id' and a.item_category_id in(5,6,7) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 order by b.sub_process_id, b.seq_no";*/

					$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0  )
					union
					(
					select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
					)  order by seq_no,sub_process_id";
        		} 
            	else if ($db_type == 0) 
            	{
					$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio, b.seq_no as seq_no from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 )
					union
					(
					select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio,b.seq_no as seq_no from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
					)  ";
            	}
				// echo $sql;
            	$sql_result = sql_select($sql);

                foreach ($sql_result as $row) 
                {
                	$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")]. "**" . $row[csf("current_stock")] . "**" . $row[csf("prod_id")] . "$$$";
                }
				//var_dump($sub_process_data_array);
                foreach ($process_array as $process_id) 
                {
                	$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];

                	$liquor_ratio_process = $sub_process_remark_array[$process_id]['liquor_ratio'];
                	$remark = $sub_process_remark_array[$process_id]['remark'];
					//echo $remark.', ';
                	if ($remark == '') $remark = ''; else $remark = $remark . ', ';
                	$tot_ratio=1.5;
                	$leveling_water=$batch_weight*($liquor_ratio_process-$tot_ratio) ;

                	?>
                	<tr bgcolor="#EEEFF0">
                		<td colspan="9" align="left"><b>Sub Process
                			Name:- <? echo $dyeing_sub_process[$process_id] . ', ' . $remark .' Total Liquor(ltr): '.$liquor_ratio.', '. 'Levelling  Water(Ltr): '.number_format($leveling_water,2,'.',''); ?></b>
                		</td>
                	</tr>
                	<?
                	$tot_ratio = 0;
                	//$sub_process_data = array_filter(explode("@@@",$sub_process_data_array2[$process_id]));
					//$sub_process_data = explode("$$$", substr($sub_process_data_array[$process_id], 0, -1));
					$sub_process_dataArr=rtrim($sub_process_data_array[$process_id],'$$$');
					$sub_process_data = explode("$$$", $sub_process_dataArr);
					//echo count($sub_process_data).'='.$process_id.'<br>';
                	foreach ($sub_process_data as $data) 
                	{
                		$data = explode("**", $data);
                		$current_stock = $data[13];
                		$current_stock_check=number_format($current_stock,7,'.','');
                		//if($process_id==93 || $process_id==94 || $process_id==95 || $process_id==96 || $process_id==97 || $process_id==98)
						if(in_array($process_id, $subprocessForWashArr))
						{
							$id = $data[0];
                			$item_category_id = $data[1];
                			$item_group_id = $data[2];
                			$sub_group_name = $data[3];
                			$item_description = $data[4];
                			$item_size = $data[5];
                			$unit_of_measure = $data[6];
                			$dtls_id = $data[7];
                			$sub_process_id = $data[8];
                			$item_lot = $data[9];
                			$dose_base_id = $data[10];
                			$ratio = $data[11];
                			$comments = $data[12];
                			$prod_id = $data[14];

                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                			?>
                			<tr bgcolor="<? echo $bgcolor; ?>">
                				<td><? echo $i; ?></td>
                				<td><p><? echo $item_category[$item_category_id]; ?></p></td>
                				<td><p><? echo $prod_id; ?></p></td>
                				<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
                				<td><p><? echo $item_description; ?></p></td>
                				<td><p><? echo $item_lot; ?></p></td>
                				<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
                				<td align="right"><? echo $comments; ?>&nbsp;</td>
                			</tr>
                			<?
                			$tot_ratio += $ratio;
                			$grand_tot_ratio += $ratio;
                			$i++;
						}
						else
						{
	                		//if($current_stock_check>0)
	                		//{
	                			$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];

	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
	                			<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td><? echo $i; ?></td>
	                				<td><p><? echo $item_category[$item_category_id]; ?></p></td>
	                				<td><p><? echo $prod_id; ?></p></td>
	                				<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
	                				<td><p><? echo $item_description; ?></p></td>
	                				<td><p><? echo $item_lot; ?></p></td>
	                				<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
	                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
	                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
	                				<td align="right"><? echo $comments; ?>&nbsp;</td>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
	                			$i++;
	                		//}
                		}

                	}
                	?>
                	<tr class="tbl_bottom">
                		<td align="right" colspan="8"><strong>Sub Process Total</strong></td>
                		<td align="right"><? echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                	</tr>
                	<?
                }
                ?>

                <tr class="tbl_bottom">
                	<td align="right" colspan="8"><strong>Grand Total</strong></td>
                	<td align="right"><? echo number_format($grand_tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                    <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                </tr>
            </table>
            <br>
            <?
            echo signature_table(62, $com_id, "1030px");
            ?>
        </div>
    </div>
    <?
	exit();
}

if ($action == "recipe_entry_print_4")
{
		extract($_REQUEST);
		$data = explode('*', $data);
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		//$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
		//$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
		$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
		$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
		$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
		$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');

		$batch_array = array();
		if ($db_type == 0) {
			$sql = "select a.id, a.batch_no,a.batch_against, a.entry_form, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
		} else if ($db_type == 2) {
			$sql = "select a.id, a.batch_no,a.batch_against,a.entry_form, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,  sum(b.batch_qnty) as batch_qnty  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.batch_against, a.batch_no,a.entry_form,a.total_trims_weight order by a.id DESC";
		}
		// echo $sql;
		$result_sql = sql_select($sql);
		foreach ($result_sql as $row) {
			$order_no = '';
			$order_id = array_unique(explode(",", $row[csf("po_id")]));
			if ($row[csf("entry_form")] == 36) {
				$batch_type = "<b> SUBCONTRACT ORDER </b>";
				foreach ($order_id as $val) {
					if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];
				}
			} else {
				$batch_type = "<b> SELF ORDER </b>";
				foreach ($order_id as $val) {
					if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
				}
			}
			$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
			$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
			$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
			$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")];
			$batch_array[$row[csf("id")]]['order'] = $order_no;
			$batch_array[$row[csf("id")]]['batch_type'] = $batch_type;
		}




		$sql_recipe_mst = "select id, labdip_no, company_id,working_company_id as w_com_id,location_id, recipe_description, batch_id,machine_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, remarks from pro_recipe_entry_mst where id='$data[1]'";
		$dataArray = sql_select($sql_recipe_mst);
		$batch_id=$dataArray[0][csf('batch_id')];
		$w_com_id=$dataArray[0][csf('w_com_id')];
		$company_id=$dataArray[0][csf('company_id')];
		$order_source=$dataArray[0][csf('order_source')];

		$order_array = return_library_array("select id, order_no from subcon_ord_dtls b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id' ", "id", "order_no");
		//$po_arr = return_library_array("select b.id,b.po_number from wo_po_break_down b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id'", 'id', 'po_number');


		$po_arr = sql_select("select b.id,b.po_number,c.mst_id as batch_id, b.grouping from wo_po_break_down b,wo_po_details_master a,pro_batch_create_dtls c where  a.job_no=b.job_no_mst and b.id=c.po_id and b.status_active=1 and b.is_deleted=0 and c.mst_id='$batch_id'");
		$internal_ref_arr = array();

		foreach ($po_arr as $row) {

			$internal_ref_arr[$row[csf('batch_id')]] = $row[csf('grouping')];
			$po_arr[$row[csf('id')]] = $row[csf('po_number')];

		}
		unset($po_arr);

		$total_batch_weight =  $dataArray[0][csf('batch_qty')];

		$mst_id = $dataArray[0][csf('id')];
		$total_liquor_ratio_sql = sql_select("select liquor_ratio, total_liquor, check_id from pro_recipe_entry_dtls where mst_id=" . $mst_id . " and ratio>0 group by liquor_ratio, total_liquor, check_id");
		$total_liquor_ratio = 0;
		foreach($total_liquor_ratio_sql as $value){

			$total_liquor_ratio += $value[csf('liquor_ratio')];
		}
		$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$w_com_id");
	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
		$group_id=$nameArray[0][csf('group_id')];
		?>
		<div style="width:930px; font-size:6px">
			<table width="930" cellspacing="0" align="right" border="0">
				<tr>
					<td colspan="6" align="center" style="font-size:x-large">
						<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_library[$w_com_id]; ?>
					</strong></td>
				</tr>
				<tr>
					<td colspan="6" align="center">
						<?

						foreach ($nameArray as $result) {
							?>
							Plot No: <? echo $result[csf('plot_no')]; ?>
							Level No: <? echo $result[csf('level_no')]; ?>
							Road No: <? echo $result[csf('road_no')]; ?>
							Block No: <? echo $result[csf('block_no')]; ?>
							Zip Code: <? echo $result[csf('zip_code')];
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:20px"><u><strong><? echo 'Owner Company : '.$company_library[$data[0]].'<br>'.$data[3]; ?></strong></u></td>
				</tr>
				<tr>
					<td width="130"><strong>System ID:</strong></td>
					<td width="175"><? echo $dataArray[0][csf('id')]; ?></td>
					<td width="130"><strong>Labdip No: </strong></td>
					<td width="175px"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
					<td width="130"><strong>Recipe Des.:</strong></td>
					<td width="175"><? echo $dataArray[0][csf('recipe_description')]; ?></td>
				</tr>
				<tr>
					<td><strong>Batch No:</strong></td>
					<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_no']; ?></td>
					<td><strong>Recipe Date:</strong></td>
					<td> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
					<td><strong>Order Source:</strong></td>
					<td><? echo $order_source[$dataArray[0][csf('order_source')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Buyer Name:</strong></td>
					<td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
					<td><strong>Booking:</strong></td>
					<td><? echo $dataArray[0][csf('style_or_order')]; ?></td>
					<td><strong>Color:</strong></td>
					<td> <? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Color Range:</strong></td>
					<td><? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
                <!-- <td><strong>B/L Ratio:</strong></td> <td><? echo $dataArray[0][csf('batch_ratio')] . ':' . $dataArray[0][csf('liquor_ratio')]; ?></td>
                	<td><strong>Total Liq.:</strong></td><td> <? echo $dataArray[0][csf('total_liquor')]; ?></td>-->
                	<td><strong>Batch Weight:</strong></td>
                	<td><? $batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];echo $batch_weight; ?></td>
                	<td><strong>Trims Weight:</strong></td>
                	<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight']; ?></td>
                </tr>
                <tr>
                	<td><strong>Order No.:</strong></td>
                	<td>
                		<? if ($batch_array[$dataArray[0][csf('batch_id')]]['batch_against'] == 3) echo "Sample Without Order";
                		else echo $batch_array[$dataArray[0][csf('batch_id')]]['order']; ?></td>
                		<td><strong>Method:</strong></td>
                		<td><? echo $dyeing_method[$dataArray[0][csf('method')]]; ?></td>
                        <td><strong>Machine no:</strong></td>
                		<td><? echo $machine_arr[$dataArray[0][csf('machine_id')]]; ?></td>
                	</tr>
                	<tr>
                		<td><strong>Remarks:</strong></td>
                		<td ><? echo $dataArray[0][csf('remarks')]; ?></td>
                		<td><strong>Liquor Ratio:</strong></td>
                		<td ><? echo $total_liquor_ratio; ?></td>
                		<td><strong>Internal Ref:</strong></td>
                		<td ><? echo $internal_ref_arr[$dataArray[0][csf('batch_id')]]; ?></td>
                	</tr>
                </table>
                <br>
                <?
				$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
				$j = 1;
				$entryForm = $entry_form_arr[$batch_id_qry[0]];
			?>
                <table width="930" cellspacing="0" align="right" class="rpt_table" border="1" style="font-size:16px; margin:5px;" rules="all">
				<thead bgcolor="#dddddd" align="center">

						<tr bgcolor="#CCCCFF">
							<th colspan="5" align="center"><strong>Fabrication</strong></th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="80">Dia/ W. Type</th>
							<th width="200">Constrution & Composition</th>
							<th width="70">Gsm</th>
							<th width="70">Dia</th>
						</tr>
				</thead>
				<tbody>
					<?
						foreach ($batch_id_qry as $b_id) {
							 $batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
							$result_batch_query = sql_select($batch_query);
							foreach ($result_batch_query as $rows) {
								if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								$fabrication_full = $rows[csf("item_description")];
								$fabrication = explode(',', $fabrication_full);
					?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td align="center"><? echo $j; ?></td>
									<td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>
									<td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
									<td align="center"><? echo $fabrication[2]; ?></td>
									<td align="center"><? echo $fabrication[3]; ?></td>
								</tr>
					<?
								$j++;
							}
						}
					?>
				</tbody>
			</table>
            <br> <br> <br>
                <div style="width:100%;">
                	<table align="right" style="margin:5px;" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
                		<thead bgcolor="#dddddd" align="center">
                			<tr>
                				<th rowspan="2" width="30">SL</th>
	                			<th rowspan="2" width="110">Item Cat.</th>
	                			<th rowspan="2" width="110">Product ID</th>
	                			<th rowspan="2" width="220">Function Name</th>
	                			<th rowspan="2" width="200">Item Group</th>
	                			<th rowspan="2" width="80">Item Lot</th>
	                			<th rowspan="2" width="50">UOM</th>
	                			<th rowspan="2" width="100">Dose Base</th>
	                			<th rowspan="2" width="100">Ratio/Dose</th>
	                			<th colspan="3" width="200">Amount/KG</th>
	                			<th rowspan="2" width="">Comments</th>
                			</tr>
                			<tr>
                				<th>KG</th>
                				<th>Gram</th>
                				<th>Miligram</th>
                			</tr>

                		</thead>
                		<?
                		$i = 1;
                		$j = 1;
                		$mst_id = $data[1];
                		$com_id = $data[0];


                		$process_array = array();
                		$sub_process_data_array = array();
                		$sub_process_remark_array = array();
                		$sql_ratio = "select id, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by id";
                		$nameArray = sql_select($sql_ratio);
                		foreach ($nameArray as $row) {
                			if (!in_array($row[csf("sub_process_id")], $process_array)) {
                				$process_array[] = $row[csf("sub_process_id")];
                				$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
                			}
                			$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
                			$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];


                		}

                		if ($db_type == 2) {
                			/*$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio from product_details_master a, pro_recipe_entry_dtls b where a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$com_id' and a.item_category_id in(5,6,7) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 order by b.sub_process_id, b.seq_no";*/

                			$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0  )
                			union
                			(
                			select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
                		)  order by seq_no,sub_process_id";
                	} else if ($db_type == 0) {
                		$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio, b.seq_no as seq_no from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 )
                		union
                		(
                		select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio,b.seq_no as seq_no from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
                	)  ";
                }
				// echo $sql;
                $sql_result = sql_select($sql);

                foreach ($sql_result as $row) {
                	$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")]. "**" . $row[csf("current_stock")] . "**" . $row[csf("prod_id")] . "$$$";

                }
				//var_dump($sub_process_data_array);
                foreach ($process_array as $process_id) {
                	$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];
                	$l_ratio = $sub_process_remark_array[$process_id]['liquor_ratio'];

                	$liquor_ratio_process = $sub_process_remark_array[$process_id]['liquor_ratio'];
                	$remark = $sub_process_remark_array[$process_id]['remark'];
                	if ($remark == '') $remark = ''; else $remark .= $remark . ', ';
                	$tot_ratio=1.5;
                	$leveling_water=$batch_weight*($liquor_ratio_process-$tot_ratio) ;


                	?>
                	<tr bgcolor="#EEEFF0">
                		<td colspan="13" align="left"><b>Sub Process
                			Name:- <? echo $dyeing_sub_process[$process_id] . ', ' . $remark; ?>
							<!--  .' Total Liquor(ltr): '.number_format($liquor_ratio,2,'.','');
							, Liquor Ratio: <?php echo number_format($l_ratio,2,'.','');
							?> -->
						</b>
                		</td>
                	</tr>
                	<?
                	$tot_ratio = 0;
                	$tot_kg = 0;
                	$tot_gm = 0;
                	$tot_mgm = 0;
                	//$sub_process_data = array_filter(explode("@@@",$sub_process_data_array2[$process_id]));
					//$sub_process_data = explode("$$$", substr($sub_process_data_array[$process_id], 0, -1));
					$sub_process_dataArr=rtrim($sub_process_data_array[$process_id],'$$$');
					$sub_process_data = explode("$$$", $sub_process_dataArr);
					//echo count($sub_process_data).'='.$process_id.'<br>';
                	foreach ($sub_process_data as $data) {
                		$data = explode("**", $data);
                		$current_stock = $data[13];
                		$current_stock_check=number_format($current_stock,7,'.','');

                	//	if($process_id==93 || $process_id==94 || $process_id==95 || $process_id==96 || $process_id==97 || $process_id==98 || $process_id == 140 || $process_id == 141 || $process_id == 142 || $process_id == 143)
						if(in_array($process_id, $subprocessForWashArr))
						{
							$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];

	                			if($dose_base_id==1 && $item_category_id==5){

	                				$amount = number_format($ratio*$total_batch_weight*$l_ratio/1000,6, '.', '');

	                				$amount = explode('.',$amount);
	                			}
	                			else{

	                				$amount = number_format($ratio*$total_batch_weight/100,6, '.', '');

	                				$amount = explode('.',$amount);
	                			}

	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
	                			<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td><? echo $i; ?></td>
	                				<td><p><? echo $item_category[$item_category_id]; ?></p></td>
	                				<td><p><? echo $prod_id; ?></p></td>
	                				<td><p><? echo $sub_group_name; ?></p></td>
	                				<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
	                				<td><p><? echo $item_lot; ?></p></td>
	                				<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
	                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
	                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
	                				<td align="right"><? echo $amount[0]; ?>&nbsp;</td>
	                				<td align="right"><? echo number_format(substr($amount[1], 0, 3)); ?>&nbsp;</td>
	                				<td align="right"><? echo number_format(substr($amount[1], 3, 6)); ?>&nbsp;</td>
	                				<td align="right"><? echo $comments; ?>&nbsp;</td>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
	                			$grand_tot_kg += $amount[0];
	                			$grand_tot_gm += substr($amount[1], 0, 3);
	                			$grand_tot_mgm += substr($amount[1], 3, 6);
	                			$tot_kg += $amount[0];
			                	$tot_gm += substr($amount[1], 0, 3);
			                	$tot_mgm += substr($amount[1], 0, 3);
	                			$i++;
						}
						else
						{


	                		if($current_stock_check>0)
	                		{
	                			$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];

	                			if($dose_base_id==1 && $item_category_id==5){

	                				$amount = number_format($ratio*$total_batch_weight*$l_ratio/1000,6, '.', '');

	                				$amount = explode('.',$amount);
	                			}
	                			else{

	                				$amount = number_format($ratio*$total_batch_weight/100,6, '.', '');

	                				$amount = explode('.',$amount);
	                			}

	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
	                			<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td><? echo $i; ?></td>
	                				<td><p><? echo $item_category[$item_category_id]; ?></p></td>
	                				<td><p><? echo $prod_id; ?></p></td>
	                				<td><p><? echo $sub_group_name; ?></p></td>
	                				<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
	                				<td><p><? echo $item_lot; ?></p></td>
	                				<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
	                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
	                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
	                				<td align="right"><? echo $amount[0]; ?>&nbsp;</td>
	                				<td align="right"><? echo number_format(substr($amount[1], 0, 3)); ?>&nbsp;</td>
	                				<td align="right"><? echo number_format(substr($amount[1], 3, 6)); ?>&nbsp;</td>
	                				<td align="right"><? echo $comments; ?>&nbsp;</td>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
	                			$grand_tot_kg += $amount[0];
	                			$grand_tot_gm += substr($amount[1], 0, 3);
	                			$grand_tot_mgm += substr($amount[1], 3, 6);
	                			$tot_kg += $amount[0];
			                	$tot_gm += substr($amount[1], 0, 3);
			                	$tot_mgm += substr($amount[1], 3, 6);
	                			$i++;
	                		}
                		}
                	}

                	if($tot_mgm>=1000){
                		$tot_gm += intdiv($tot_mgm, 1000);
                		$tot_mgm = $tot_mgm%1000;
                	}

                	if($tot_gm>=1000){
                		$tot_kg += intdiv($tot_gm, 1000);
                		$tot_gm = $tot_gm%1000;
                	}
                	?>
                	<tr class="tbl_bottom">
                		<td align="right" colspan="8"><strong>Sub Process Total</strong></td>
                		<td align="right"><? echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo $tot_kg; ?>&nbsp;</td>
                        <td align="right"><? echo $tot_gm; ?>&nbsp;</td>
                        <td align="right"><? echo $tot_mgm ?>&nbsp;</td>
                        <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                	</tr>
                	<?
                }
                	if($grand_tot_mgm>=1000){
                		$grand_tot_gm += intdiv($grand_tot_mgm, 1000);
                		$grand_tot_mgm = $grand_tot_mgm%1000;
                	}

                	if($grand_tot_gm>=1000){
                		$grand_tot_kg += intdiv($grand_tot_gm, 1000);
                		$grand_tot_gm = $grand_tot_gm%1000;
                	}
                ?>

                <tr class="tbl_bottom">
                	<td align="right" colspan="8"><strong>Grand Total</strong></td>
                	<td align="right"><? echo number_format($grand_tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                    <td align="right"><? echo $grand_tot_kg; ?>&nbsp;</td>
                    <td align="right"><? echo $grand_tot_gm; ?>&nbsp;</td>
                    <td align="right"><? echo $grand_tot_mgm; ?>&nbsp;</td>
                    <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                </tr>
            </table>
            <br>
            <?
            echo signature_table(62, $com_id, "1030px");
            ?>
        </div>
    </div>
    <?
}

if ($action == "recipe_entry_print_5")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	//$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
	//$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');

	$sales_order_result = sql_select("select id,job_no,within_group,sales_booking_no from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	$sales_arr = array();
	foreach ($sales_order_result as $sales_row) {
		$sales_arr[$sales_row[csf("id")]]["sales_order_no"] 	= $sales_row[csf("job_no")];
	}

	$batch_array = array();
	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no,a.batch_against, a.entry_form, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty,b.is_sales from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
	} else if ($db_type == 2) {
		$sql = "select a.id, a.batch_no,a.batch_against,a.entry_form, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,  sum(b.batch_qnty) as batch_qnty,b.is_sales  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.batch_against, a.batch_no,a.entry_form,a.total_trims_weight,b.is_sales order by a.id DESC";
	}
	// echo $sql;
	$result_sql = sql_select($sql);
	foreach ($result_sql as $row) {
		$order_no = '';
		$order_id = array_unique(explode(",", $row[csf("po_id")]));
		if ($row[csf("entry_form")] == 36) {
			$batch_type = "<b> SUBCONTRACT ORDER </b>";
			foreach ($order_id as $val) {
				if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];
			}
		} else {
			$batch_type = "<b> SELF ORDER </b>";
			foreach ($order_id as $val) {
				if($row[csf("is_sales")] == 1){
					$order_no = $sales_arr[$val]["sales_order_no"];
				}
				else
				{
					if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
				}
			}
		}
		$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
		$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
		$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
		$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")];
		$batch_array[$row[csf("id")]]['order'] = $order_no;
		$batch_array[$row[csf("id")]]['batch_type'] = $batch_type;
	}


	$sql_recipe_mst = "select id, labdip_no, company_id,working_company_id as w_com_id,location_id, recipe_description, batch_id,machine_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, remarks from pro_recipe_entry_mst where id='$data[1]'";
	$dataArray = sql_select($sql_recipe_mst);
	$w_com_id=$dataArray[0][csf('w_com_id')];
	$company_id=$dataArray[0][csf('company_id')];
	$batch_id=$dataArray[0][csf('batch_id')];
	$order_array = return_library_array("select id, order_no from subcon_ord_dtls b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id' ", "id", "order_no");
	$po_arr = return_library_array("select b.id,b.po_number from wo_po_break_down b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id'", 'id', 'po_number');

	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$w_com_id");
	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	$group_id=$nameArray[0][csf('group_id')];
	?>
	<div style="width:930px; font-size:6px">
		<table width="930" cellspacing="0" align="right" border="0">
			<tr>
				<td colspan="6" align="center" style="font-size:x-large">
					<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_library[$w_com_id]; ?>
				</strong></td>
			</tr>
			<tr>
				<td colspan="6" align="center">
					<?

					foreach ($nameArray as $result) {
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]; ?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')]; ?>
						Zip Code: <? echo $result[csf('zip_code')];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:20px"><u><strong><? echo 'Owner Company : '.$company_library[$data[0]].'<br>'.$data[3]; ?></strong></u></td>
			</tr>
			<tr>
				<td width="130"><strong>System ID:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('id')]; ?></td>
				<td width="130"><strong>Labdip No: </strong></td>
				<td width="175px"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
				<td width="130"><strong>Recipe Des.:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('recipe_description')]; ?></td>
			</tr>
			<tr>
				<td><strong>Batch No:</strong></td>
				<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_no']; ?></td>
				<td><strong>Recipe Date:</strong></td>
				<td> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
				<td><strong>Order Source:</strong></td>
				<td><? echo $order_source[$dataArray[0][csf('order_source')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Buyer Name:</strong></td>
				<td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
				<td><strong>Booking:</strong></td>
				<td><? echo $dataArray[0][csf('style_or_order')]; ?></td>
				<td><strong>Color:</strong></td>
				<td> <? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Color Range:</strong></td>
				<td><? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
            	<!-- <td><strong>B/L Ratio:</strong></td> <td><? echo $dataArray[0][csf('batch_ratio')] . ':' . $dataArray[0][csf('liquor_ratio')]; ?></td>
            	<td><strong>Total Liq.:</strong></td><td> <? echo $dataArray[0][csf('total_liquor')]; ?></td>-->
            	<td><strong>Batch Weight:</strong></td>
            	<td><? $batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];echo $batch_weight; ?></td>
            	<td><strong>Trims Weight:</strong></td>
            	<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight']; ?></td>
            </tr>
            <tr>
            	<td><strong>Order No.:</strong></td>
            	<td><? if ($batch_array[$dataArray[0][csf('batch_id')]]['batch_against'] == 3) echo "Sample Without Order"; else echo $batch_array[$dataArray[0][csf('batch_id')]]['order']; ?></td>
        		<td><strong>Method:</strong></td>
        		<td><? echo $dyeing_method[$dataArray[0][csf('method')]]; ?></td>
                <td><strong>Machine no:</strong></td>
        		<td><? echo $machine_arr[$dataArray[0][csf('machine_id')]]; ?></td>
            </tr>
        	<tr>
        		<td><strong>Remarks:</strong></td>
        		<td colspan="5"><? echo $dataArray[0][csf('remarks')]; ?></td>
        	</tr>
        </table>
        <br>
        <?
		$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
		$j = 1;
		$entryForm = $entry_form_arr[$batch_id_qry[0]];
		?>
        <table width="930" cellspacing="0" align="right" class="rpt_table" border="1" style="font-size:16px; margin:5px;" rules="all">
			<thead bgcolor="#dddddd" align="center">

					<tr bgcolor="#CCCCFF">
						<th colspan="5" align="center"><strong>Fabrication</strong></th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="80">Dia/ W. Type</th>
						<th width="200">Constrution & Composition</th>
						<th width="70">Gsm</th>
						<th width="70">Dia</th>
					</tr>
			</thead>
			<tbody>
				<?
				foreach ($batch_id_qry as $b_id)
				{
					$batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
					$result_batch_query = sql_select($batch_query);
					foreach ($result_batch_query as $rows)
					{
						if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$fabrication_full = $rows[csf("item_description")];
						$fabrication = explode(',', $fabrication_full);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $j; ?></td>
							<td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>
							<td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
							<td align="center"><? echo $fabrication[2]; ?></td>
							<td align="center"><? echo $fabrication[3]; ?></td>
						</tr>
						<?
						$j++;
					}
				}
				?>
			</tbody>
		</table>
        <br> <br> <br>
        <div style="width:100%;">
        	<table align="right" style="margin:5px;" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
        		<thead bgcolor="#dddddd" align="center">
        			<th width="30">SL</th>
        			<th width="110">Item Cat.</th>
        			<th width="110">Product ID</th>
        			<th width="200">Item Group</th>
        			<th width="220">Item Description</th>
        			<th width="80">Item Lot</th>
        			<th width="50">UOM</th>
        			<th width="100">Dose Base</th>
        			<th width="100">Ratio</th>
        			<th width="80">Recipe Qty</th>
        			<th width="">Comments</th>
        		</thead>
        		<?
        		$i = 1;
        		$j = 1;
        		$mst_id = $data[1];
        		$com_id = $data[0];


        		$process_array = array();
        		$sub_process_data_array = array();
        		$sub_process_remark_array = array();
        		$sql_ratio = "select id, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by id";
        		$nameArray = sql_select($sql_ratio);
        		foreach ($nameArray as $row) {
        			if (!in_array($row[csf("sub_process_id")], $process_array)) {
        				$process_array[] = $row[csf("sub_process_id")];
        				$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
        			}
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
        		}

        		if ($db_type == 2)
        		{
					/*$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio from product_details_master a, pro_recipe_entry_dtls b where a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$com_id' and a.item_category_id in(5,6,7) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 order by b.sub_process_id, b.seq_no";*/

					$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no, c.batch_qty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0  )
					union
					(
					select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no, null as batch_qty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
					)  order by seq_no,sub_process_id";
        		}
        		else if ($db_type == 0)
        		{
					$sql = "(SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio, b.seq_no as seq_no, c.batch_qty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 )
					union
					(
					select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio,b.seq_no as seq_no, null as batch_qty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
					)  ";
        		}
				//echo $sql;
        		$sql_result = sql_select($sql);
	            foreach ($sql_result as $row)
	            {
	            	$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")]. "**" . $row[csf("current_stock")] . "**" . $row[csf("prod_id")] . "**" . $row[csf("batch_qty")] . "$$$";

	            }
				//var_dump($sub_process_data_array);
	            foreach ($process_array as $process_id)
	            {
	            	$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];

	            	$liquor_ratio_process = $sub_process_remark_array[$process_id]['liquor_ratio'];
	            	$remark = $sub_process_remark_array[$process_id]['remark'];
	            	if ($remark == '') $remark = ''; else $remark .= $remark . ', ';
	            	$tot_ratio=1.5;
	            	$leveling_water=$batch_weight*($liquor_ratio_process-$tot_ratio) ;

	            	?>
	            	<tr bgcolor="#EEEFF0">
	            		<td colspan="11" align="left"><b>Sub Process
	            			Name:- <? echo $dyeing_sub_process[$process_id] . ', ' . $remark .' Total Liquor(ltr): '.$liquor_ratio.', '. 'Levelling  Water(Ltr): '.number_format($leveling_water,2,'.',''); ?></b>
	            		</td>
	            	</tr>
	            	<?
	            	$tot_ratio = 0;$tot_recipe_qty = 0;
	            	//$sub_process_data = array_filter(explode("@@@",$sub_process_data_array2[$process_id]));
					//$sub_process_data = explode("$$$", substr($sub_process_data_array[$process_id], 0, -1));
					$sub_process_dataArr=rtrim($sub_process_data_array[$process_id],'$$$');
					$sub_process_data = explode("$$$", $sub_process_dataArr);
					//echo count($sub_process_data).'='.$process_id.'<br>';
	            	foreach ($sub_process_data as $data) {
	            		$data = explode("**", $data);
	            		$current_stock = $data[13];
	            		$current_stock_check=number_format($current_stock,7,'.','');
	            		//if($process_id==93 || $process_id==94 || $process_id==95 || $process_id==96 || $process_id==97 || $process_id==98 || $process_id == 140 || $process_id == 141 || $process_id == 142 || $process_id == 143)
						if (in_array($process_id, $subprocessForWashArr))
						{
							$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];
								$batch_qty = $data[15];

								if ($dose_base_id == 1) {

									$recipe_qty = number_format(($liquor_ratio/1000)*$ratio, 4, '.', '');

								} else {
									$recipe_qty = number_format(($batch_qty*$ratio)/100, 4, '.', '');

								}
	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
	                			<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td><? echo $i; ?></td>
	                				<td><p><? echo $item_category[$item_category_id]; ?></p></td>
	                				<td><p><? echo $prod_id; ?></p></td>
	                				<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
	                				<td><p><? echo $item_description; ?></p></td>
	                				<td><p><? echo $item_lot; ?></p></td>
	                				<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
	                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
	                				<td align="right"><strong><? echo number_format($ratio, 6, '.', ''); ?></strong>&nbsp;</td>
	                				<td align="right" title="Recipe Qty=(Total Liquor(ltr)/1000)*Ratio"><strong><? echo $recipe_qty; ?></strong>&nbsp;</td>
	                				<td align="right"><? echo $comments; ?>&nbsp;</td>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
	                			$tot_recipe_qty +=$recipe_qty;
	                			$grand_tot_recipe_qty +=$recipe_qty;
	                			$i++;
						}
						else
						{


	                		if($current_stock_check>0)
	                		{
	                			$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];
								$batch_qty = $data[15];

								if ($dose_base_id == 1) {

									$recipe_qty = number_format(($liquor_ratio/1000)*$ratio, 4, '.', '');

								} else {
									$recipe_qty = number_format(($batch_qty*$ratio)/100, 4, '.', '');

								}
	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
	                			<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td><? echo $i; ?></td>
	                				<td><p><? echo $item_category[$item_category_id]; ?></p></td>
	                				<td><p><? echo $prod_id; ?></p></td>
	                				<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
	                				<td><p><? echo $item_description; ?></p></td>
	                				<td><p><? echo $item_lot; ?></p></td>
	                				<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
	                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
	                				<td align="right"><strong><? echo number_format($ratio, 6, '.', ''); ?></strong>&nbsp;</td>
	                				<td align="right" title="Recipe Qty=(Total Liquor(ltr)/1000)*Ratio"><strong><? echo $recipe_qty; ?></strong>&nbsp;</td>
	                				<td align="right"><? echo $comments; ?>&nbsp;</td>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
	                			$tot_recipe_qty +=$recipe_qty;
	                			$grand_tot_recipe_qty +=$recipe_qty;
	                			$i++;
	                		}
	            		}
	            	}
	            	?>
	            	<!-- <tr class="tbl_bottom">
	            		<td align="right" colspan="8"><strong>Sub Process Total</strong></td>
	            		<td align="right"><?// echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
	            		<td align="right"><? //echo number_format($tot_recipe_qty, 2, '.', ''); ?>&nbsp;</td>
	                    <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
	            	</tr> -->
	            	<?
	            }
	            ?>
	            <tr class="tbl_bottom">
	            	<td align="right" colspan="8"><strong>Grand Total</strong></td>
	            	<td align="right"><strong><? echo number_format($grand_tot_ratio, 6, '.', ''); ?></strong>&nbsp;</td>
	            	<td align="right"><strong><? echo number_format($grand_tot_recipe_qty, 4, '.', ''); ?></strong>&nbsp;</td>
	                <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
	            </tr>
    		</table>
	        <br>
	        <?
	        echo signature_table(62, $com_id, "1030px");
	        ?>
		</div>
	</div>
	<?
}
if ($action == "recipe_entry_print_6")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	//$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");

	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');

	 $sql_recipe_mst = "select id, labdip_no, company_id,working_company_id as w_com_id,copy_from,surplus_solution,pump,pickup,cycle_time,location_id, recipe_description, batch_id,machine_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, remarks from pro_recipe_entry_mst where id='$data[1]' and status_active=1";
	$dataArray = sql_select($sql_recipe_mst);
	$w_com_id=$dataArray[0][csf('w_com_id')];
	$company_id=$dataArray[0][csf('company_id')];
	$pump=$dataArray[0][csf('pump')];$cycle_time=$dataArray[0][csf('cycle_time')];
	$batch_id=$dataArray[0][csf('batch_id')];
	$mst_id=$data[1];
	$order_array = return_library_array("select id, order_no from subcon_ord_dtls b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id' ", "id", "order_no");



	//	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
   	$sql_po="select b.id,b.po_number from wo_po_break_down b,wo_booking_dtls a,pro_batch_create_mst c where c.booking_no=a.booking_no and a.po_break_down_id=b.id and b.status_active=1 and c.id=$batch_id";
	$result_sql_po = sql_select($sql_po);
	foreach ($result_sql_po as $row)
	{
		$po_arr[$row[csf("po_number")]] = $row[csf("po_number")];
	}


	$batch_array = array();
	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no,a.batch_against, a.entry_form, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty,b.is_sales from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
	} else if ($db_type == 2) {
		$sql = "select a.id, a.batch_no,a.batch_against,a.entry_form, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,  sum(b.batch_qnty) as batch_qnty,b.is_sales  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.id=$batch_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.batch_against, a.batch_no,a.entry_form,a.total_trims_weight,b.is_sales order by a.id DESC";
	}
	// echo $sql;
	$result_sql = sql_select($sql);
	foreach ($result_sql as $row) {
		$order_no = '';
		$order_id = array_unique(explode(",", $row[csf("po_id")]));
		if ($row[csf("entry_form")] == 36) {
			$batch_type = "<b> SUBCONTRACT ORDER </b>";
			foreach ($order_id as $val) {
				if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];
			}
		} else {
			$batch_type = "<b> SELF ORDER </b>";
			foreach ($order_id as $val) {
				if($row[csf("is_sales")] == 1){
					$order_no =implode(", ",$po_arr);// $sales_arr[$val]["sales_order_no"];
				}
				else
				{
					if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
				}
			}
		}
		$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
		$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
		$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
		$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")];
		$batch_array[$row[csf("id")]]['order'] = $order_no;
		$batch_array[$row[csf("id")]]['batch_type'] = $batch_type;
	}




	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$w_com_id");
	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	$group_id=$nameArray[0][csf('group_id')];
	$sales_order_result = sql_select("select b.id,b.job_no,b.within_group,b.sales_booking_no,b.style_ref_no from fabric_sales_order_mst b,pro_batch_create_mst c where b.id=c.sales_order_id and c.status_active=1 and c.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.id='$batch_id' and c.company_id='$data[0]'");


	$sales_arr = array();
	foreach ($sales_order_result as $sales_row) {
		$sales_arr[$sales_row[csf("id")]]["sales_order_no"] 	= $sales_row[csf("job_no")];
		$sales_order_no	= $sales_row[csf("job_no")];
		$style_ref	= $sales_row[csf("style_ref_no")];
	}
	 $sql_recipe_dtl = "select mst_id, liquor_ratio,total_liquor from pro_recipe_entry_dtls where mst_id='$data[1]' and status_active=1 and liquor_ratio>0  and  RATIO>0";
	$ratio_dataArray = sql_select($sql_recipe_dtl);
	$liquor_ratio=$ratio_dataArray[0][csf('liquor_ratio')];
	$total_liquor=$ratio_dataArray[0][csf('total_liquor')];
	// echo $liquor_ratio.'DDDDDD';


	$total_batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];
	?>
	<div style="width:930px; font-size:6px">
		<table width="930" cellspacing="0" align="right" border="1"  class="rpt_table" border="1"  rules="all">
			<tr>
				<td colspan="6" align="center" style="font-size:x-large">
					<strong><? echo $company_library[$w_com_id]; ?>
				</strong></td>
			</tr>

			<tr>
				<td colspan="6" align="center" style="font-size:20px"><u><strong><? echo 'Dyeing Recipe'; ?></strong></u></td>
			</tr>
			<tr>
				<td width="130"><strong>System ID</strong></td>
				<td width="175"><? echo $dataArray[0][csf('id')]; ?></td>
				<td width="130"><strong>Labdip No </strong></td>
				<td width="175px"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
				<td><strong>Trims Weight</strong></td>
            	<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight']; ?></td>
			</tr>
			<tr>
				<td><strong>Batch No</strong></td>
				<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_no']; ?></td>
				<td><strong>Recipe Date</strong></td>
				<td> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
				<td><strong>Reel Speed</strong></td>
				<td><? echo $dataArray[0][csf('surplus_solution')];//$order_source[$dataArray[0][csf('order_source')]] ?></td>
			</tr>

			<tr>
				<td><strong>Buyer Name</strong></td>
				<td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
				<td><strong>Booking</strong></td>
				<td><? echo $dataArray[0][csf('style_or_order')]; ?></td>
				<td><strong>Pump Pressure</strong></td>
				<td> <? echo $pump;//$color_arr[$dataArray[0][csf('color_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Color Range</strong></td>
				  <td><? echo $color_range[$dataArray[0][csf('color_range')]];; ?></td>
            	<td><strong>Copy Recipe</strong></td>
            	<td><?=$dataArray[0][csf('copy_from')];//,pump,cycle_time//$batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];echo $batch_weight; ?></td>
                <td><strong>JT/Chamber</strong></td>
            	<td><?=$dataArray[0][csf('pickup')].' %';//$batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];echo $batch_weight; ?></td>

            </tr>
            <tr>
				<td><strong>Color</strong></td>
				 <td><? echo $color_arr[$dataArray[0][csf('color_id')]];; ?></td>
            	<td><strong>Order Source</strong></td>
            	<td><? echo $order_source[$dataArray[0][csf('order_source')]];//,pump,cycle_time//$batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];echo $batch_weight; ?></td>
                <td><strong>Cycle Time</strong></td>
            	<td><?=$cycle_time;//$batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];echo $batch_weight; ?></td>

            </tr>
            <tr>
            	<td><strong>Sales Order No.</strong></td>
            	<td><? echo $sales_order_no; ?></td>
        		<td><strong>Batch Weight</strong></td>
        		<td><?
				   $batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];
				   $trims_weight=$batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight'];
				   $total_batch_weight= $batch_weight+$trims_weight;
				   echo $total_batch_weight;
				   ?></td>
                <td><strong>Liquor Ratio</strong></td>
        		<td><? echo number_format($liquor_ratio,2); ?></td>
            </tr>
            <tr>
            	<td><strong>Style Ref</strong></td>
        		<td><? echo $style_ref; ?></td>
                 <td><strong>Machine no</strong></td>
        		<td><? echo $machine_arr[$dataArray[0][csf('machine_id')]]; ?></td>
        		<td><strong>Water</strong></td>
        		<td><? echo $total_liquor; ?></td>

            </tr>
        	<tr>
				<td><strong>Remarks</strong></td>
            	<td colspan="5"><?  echo $dataArray[0][csf('remarks')]; ?></td>
        	</tr>
        </table>
        <br>
        <?
		$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
		$j = 1;
		$entryForm = $entry_form_arr[$batch_id_qry[0]];
		?>
		<!-- Fabrication -->
        <table width="930" cellspacing="0" align="right" class="rpt_table" border="1" style="font-size:16px; margin:5px;" rules="all">
			<thead bgcolor="#dddddd" align="center">

					<tr bgcolor="#CCCCFF">
						<th colspan="6" align="center"><strong>Fabrication</strong></th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="80">Dia/ W. Type</th>
						<th width="200">Constrution & Composition</th>
						<th width="70">Gsm</th>
						<th width="70">Dia</th>
                        <th width="70">Yarn Lot</th>
					</tr>
			</thead>
			<tbody>
				<?
				 $sql_dtls_knit = "select a.booking_no_id,e.receive_basis,e.booking_id,a.booking_without_order,a.is_sales, b.batch_qnty AS batch_qnty, b.roll_no as roll_no, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, b.width_dia_type as num_of_rows, d.machine_no_id,d.machine_dia,d.machine_gg, d.yarn_lot,d.id as dtls_id,d.yarn_count,d.brand_id,c.barcode_no, d.stitch_length as stitch_length,  e.knitting_source, e.knitting_company,c.qc_pass_qnty_pcs ,c.coller_cuff_size
				from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d, inv_receive_master e
				where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id='$data[0]' and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
				order by b.program_no";
				$sql_result_knit = sql_select($sql_dtls_knit);
				foreach ($sql_result_knit as $row)
				{


					$knittin_data_arr2[$row[csf('item_description')]]["yarn_lot"]= $row[csf('yarn_lot')];

				}

				foreach ($batch_id_qry as $b_id)
				{
					$batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
					$result_batch_query = sql_select($batch_query);
					foreach ($result_batch_query as $rows)
					{
						if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$yarn_lot=$knittin_data_arr2[$rows[csf('item_description')]]["yarn_lot"];

						$fabrication_full = $rows[csf("item_description")];
						$fabrication = explode(',', $fabrication_full);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $j; ?></td>
							<td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>
							<td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
							<td align="center"><? echo $fabrication[2]; ?></td>
							<td align="center"><? echo $fabrication[3]; ?></td>
                            <td align="center"><? echo $yarn_lot; ?></td>
						</tr>
						<?
						$j++;
					}
				}
				?>
			</tbody>
		</table>
        <br> <br> <br>
        <div style="width:100%;">
        	<table align="right" style="margin:5px;" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
        		<thead bgcolor="#dddddd" align="center">
        			<tr>
        				<th rowspan="2" width="30">SL</th>
            			<th rowspan="2" width="220">Function Name</th>
            			<th rowspan="2" width="200">Item Desc</th>
            			<th rowspan="2" width="80">Item Lot</th>
            			<th rowspan="2" width="50">Dosage Option</th>
            			<th rowspan="2" width="100">Dosage</th>
            			<th colspan="3" width="200">Amount</th>

        			</tr>
        			<tr>
        				<th>KG</th>
        				<th>Gram</th>
        				<th>Miligram</th>
        			</tr>
        		</thead>
        		<?
        		$i = 1;
        		$j = 1;
        		$mst_id = $data[1];
        		$com_id = $data[0];

        		$process_array = array();
        		$sub_process_data_array = array();
        		$sub_process_remark_array = array();
        		$sql_ratio = "select id, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 and ratio>0 order by sub_seq";
        		$nameArray = sql_select($sql_ratio);
        		foreach ($nameArray as $row) 
        		{
        			if (!in_array($row[csf("sub_process_id")], $process_array)) {
        				$process_array[] = $row[csf("sub_process_id")];
        				$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
        			}
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
        		}

				$sql = "(SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$company_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0  )
				union
				(
				SELECT null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
				)  order by seq_no,sub_process_id";

				// echo $sql;
                $sql_result = sql_select($sql);

                foreach ($sql_result as $row) 
                {
                	$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")]. "**" . $row[csf("current_stock")] . "**" . $row[csf("prod_id")] . "$$$";

                }
				//var_dump($sub_process_data_array);
                foreach ($process_array as $process_id) 
                {
                	$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];
                	$l_ratio = $sub_process_remark_array[$process_id]['liquor_ratio'];

                	$liquor_ratio_process = $sub_process_remark_array[$process_id]['liquor_ratio'];
                	$remark = $sub_process_remark_array[$process_id]['remark'];
                	//if ($remark == '') $remark = ''; else $remark .= $remark . ', ';
                	$tot_ratio=1.5;
                	$leveling_water=$batch_weight*($liquor_ratio_process-$tot_ratio) ;


                	?>
                	<tr bgcolor="#EEEFF0">
                		<td colspan="9" align="left"><b>Sub Process
                			Name:- <? echo $dyeing_sub_process[$process_id] . ', ' . $remark. ', Total Liquor(ltr):' . $liquor_ratio; ?></b>
                		</td>
                	</tr>
                	<?
                	$tot_ratio = 0;
                	$tot_kg = 0;
                	$tot_gm = 0;
                	$tot_mgm = 0;
                	//$sub_process_data = array_filter(explode("@@@",$sub_process_data_array2[$process_id]));
					//$sub_process_data = explode("$$$", substr($sub_process_data_array[$process_id], 0, -1));
					$sub_process_dataArr=rtrim($sub_process_data_array[$process_id],'$$$');
					$sub_process_data = explode("$$$", $sub_process_dataArr);
					//echo count($sub_process_data).'='.$process_id.'<br>';
                	foreach ($sub_process_data as $data) 
                	{
                		$data = explode("**", $data);
                		$current_stock = $data[13];
                		$current_stock_check=number_format($current_stock,7,'.','');

						if(in_array($process_id, $subprocessForWashArr))
						{
							$id = $data[0];
                			$item_category_id = $data[1];
                			$item_group_id = $data[2];
                			$sub_group_name = $data[3];
                			$item_description = $data[4];
                			$item_size = $data[5];
                			$unit_of_measure = $data[6];
                			$dtls_id = $data[7];
                			$sub_process_id = $data[8];
                			$item_lot = $data[9];
                			$dose_base_id = $data[10];
                			$ratio = $data[11];
                			$comments = $data[12];
                			$prod_id = $data[14];

                			if($dose_base_id==1 && $item_category_id==5)
                			{
                				$amount = number_format($ratio*$total_batch_weight*$l_ratio/1000,6, '.', '');
                				$amount = explode('.',$amount);
                			}
                			else
                			{
                				$amount = number_format($ratio*$total_batch_weight/100,6, '.', '');
                				$amount = explode('.',$amount);
                			}

                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                			?>
							<? if($item_category_id==6){?>
                			<tr bgcolor="<? echo $bgcolor; ?>">
                				<td><b><? echo $i; ?></b></td>
								<td><p><b><? echo $sub_group_name; ?></b></p></td>
                				<td><p><b><? echo $item_description; ?></b></p></td>
                				<td><p><b><? echo $item_lot; ?></b></p></td>

								<td><p><b><? echo $dose_base[$dose_base_id]; ?></b></p></td>
                				<td align="right"><b><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</b></td>
                				<td align="right"><b><? echo $amount[0]; ?>&nbsp;</b></td>
                				<td align="right"><b><? echo number_format(substr($amount[1], 0, 3)); ?>&nbsp;</b></td>
                				<td align="right"><b><? echo number_format(substr($amount[1], 3, 6)); ?>&nbsp;</b></td>
                			</tr>
							<?}else{?>
							<tr bgcolor="<? echo $bgcolor; ?>">
                				<td><? echo $i; ?></td>
								<td><p><? echo $sub_group_name; ?></p></td>
                				<td><p><? echo $item_description; ?></p></td>
                				<td><p><? echo $item_lot; ?></p></td>

								<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
                				<td align="right"><? echo $amount[0]; ?>&nbsp;</td>
                				<td align="right"><? echo number_format(substr($amount[1], 0, 3)); ?>&nbsp;</td>
                				<td align="right"><? echo number_format(substr($amount[1], 3, 6)); ?>&nbsp;</td>
                			</tr>
							<?}?>
                			<?
                			$tot_ratio += $ratio;
                			$grand_tot_ratio += $ratio;
                			$grand_tot_kg += $amount[0];
                			$grand_tot_gm += substr($amount[1], 0, 3);
                			$grand_tot_mgm += substr($amount[1], 3, 6);
                			$tot_kg += $amount[0];
		                	$tot_gm += substr($amount[1], 0, 3);
		                	$tot_mgm += substr($amount[1], 0, 3);
                			$i++;
						}
						else
						{
	                		if($current_stock_check>0)
	                		{
	                			$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
								//	echo $sub_group_name.'==DD';
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];

	                			if($dose_base_id==1)
	                			{
	                				$amount = number_format($ratio*$total_batch_weight*$l_ratio/1000,6, '.', '');
	                				$amount = explode('.',$amount);
	                			}
	                			else
	                			{
	                				$amount = number_format($ratio*$total_batch_weight/100,6, '.', '');
	                				$amount = explode('.',$amount);
	                			}

	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
								<? if($item_category_id==6){?>
	                			<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td><? echo $i; ?></td>

	                				<td><p><b><? echo $sub_group_name; ?></b></p></td>
	                				<td><p><b><? echo $item_description; ?></b></p></td>
	                				<td><p><b><? echo $item_lot; ?></b></p></td>

	                				<td><p><b><? echo $dose_base[$dose_base_id]; ?></b></p></td>
	                				<td align="right"><b><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</b></td>
	                				<td align="right"><b><? echo $amount[0]; ?>&nbsp;</b></td>
	                				<td align="right"><b><? echo number_format(substr($amount[1], 0, 3)); ?>&nbsp;</b></td>
	                				<td align="right"><b><? echo number_format(substr($amount[1], 3, 6)); ?>&nbsp;</b></td>

	                			</tr>
								<? }else{?>
								<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td><? echo $i; ?></td>

	                				<td><p><? echo $sub_group_name; ?></p></td>
	                				<td><p><? echo $item_description; ?></p></td>
	                				<td><p><? echo $item_lot; ?></p></td>

	                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
	                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
	                				<td align="right"><? echo $amount[0]; ?>&nbsp;</td>
	                				<td align="right"><? echo number_format(substr($amount[1], 0, 3)); ?>&nbsp;</td>
	                				<td align="right"><? echo number_format(substr($amount[1], 3, 6)); ?>&nbsp;</td>
	                			</tr>
								<?}?>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
	                			$grand_tot_kg += $amount[0];
	                			$grand_tot_gm += substr($amount[1], 0, 3);
	                			$grand_tot_mgm += substr($amount[1], 3, 6);
	                			$tot_kg += $amount[0];
			                	$tot_gm += substr($amount[1], 0, 3);
			                	$tot_mgm += substr($amount[1], 3, 6);
	                			$i++;
	                		}
                		}
                	}

                	if($tot_mgm>=1000){
                		$tot_gm += intdiv($tot_mgm, 1000);
                		$tot_mgm = $tot_mgm%1000;
                	}

                	if($tot_gm>=1000){
                		$tot_kg += intdiv($tot_gm, 1000);
                		$tot_gm = $tot_gm%1000;
                	}
                	?>
                	<tr class="tbl_bottom">
                		<td align="right" colspan="5"><strong>Sub Process Total</strong></td>
                		<td align="right"><? echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo $tot_kg; ?>&nbsp;</td>
                        <td align="right"><? echo $tot_gm; ?>&nbsp;</td>
                        <td align="right"><? echo $tot_mgm ?>&nbsp;</td>
                        <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                	</tr>
                	<?
                }

            	if($grand_tot_mgm>=1000)
            	{
            		$grand_tot_gm += intdiv($grand_tot_mgm, 1000);
            		$grand_tot_mgm = $grand_tot_mgm%1000;
            	}

            	if($grand_tot_gm>=1000)
            	{
            		$grand_tot_kg += intdiv($grand_tot_gm, 1000);
            		$grand_tot_gm = $grand_tot_gm%1000;
            	}
                ?>

                <tr class="tbl_bottom">
                	<td align="right" colspan="5"><strong>Grand Total</strong></td>
                	<td align="right"><? echo number_format($grand_tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                    <td align="right"><? echo $grand_tot_kg; ?>&nbsp;</td>
                    <td align="right"><? echo $grand_tot_gm; ?>&nbsp;</td>
                    <td align="right"><? echo $grand_tot_mgm; ?>&nbsp;</td>
                    <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                </tr>
            </table>
            <br>
            <table width="930" cellspacing="0" align="right" class="rpt_table" border="1" style="font-size:16px; margin:5px;" rules="all">
	            <tr>
		            <td>Whiteness  </td>
		            <td width="100">&nbsp;   </td>
		            <td>Neutral pH  </td>
		            <td width="100">&nbsp;   </td>
		            <td>Alkali pH  </td>
		            <td width="100">&nbsp;   </td>
		            </tr>
		            <tr>
		            <td>Absorbency  </td>
		            <td width="100">&nbsp;   </td>
		            <td>Leveling  </td>
		            <td width="100">&nbsp;   </td>
		            <td>Soaping pH  </td>
		            <td width="100">&nbsp;   </td>
		            </tr>
		            <tr>
		            <td>Residual Peroxide  </td>
		            <td width="100">&nbsp;   </td>
		            <td>Specific Gravity   </td>
		            <td width="100">&nbsp;   </td>
		            <td>Final pH </td>
		            <td width="100">&nbsp;   </td>
	            </tr>
            </table>
            <?
            echo signature_table(62, $com_id, "1030px");
            ?>
        </div>
	</div>
	<?
}

if ($action == "recipe_entry_print_7")
{
	extract($_REQUEST);
	$data = explode('*', $data);

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id,location_name from lib_location","id", "location_name"); 
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	//$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
	//$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');

	$sales_order_result = sql_select("select id,job_no,within_group,sales_booking_no from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	$sales_arr = array();
	foreach ($sales_order_result as $sales_row) {
		$sales_arr[$sales_row[csf("id")]]["sales_order_no"] 	= $sales_row[csf("job_no")];
	}

	$batch_array = array();
	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no,a.batch_against, a.entry_form, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty,b.is_sales from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
	} else if ($db_type == 2) {
		$sql = "select a.id, a.batch_no,a.batch_against,a.entry_form, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,  sum(b.batch_qnty) as batch_qnty,b.is_sales  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.batch_against, a.batch_no,a.entry_form,a.total_trims_weight,b.is_sales order by a.id DESC";
	}
	// echo $sql;
	$result_sql = sql_select($sql);
	foreach ($result_sql as $row) {
		$order_no = '';
		$order_id = array_unique(explode(",", $row[csf("po_id")]));
		if ($row[csf("entry_form")] == 36) {
			$batch_type = "<b> SUBCONTRACT ORDER </b>";
			foreach ($order_id as $val) {
				if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];
			}
		} else {
			$batch_type = "<b> SELF ORDER </b>";
			foreach ($order_id as $val) {
				if($row[csf("is_sales")] == 1){
					$order_no = $sales_arr[$val]["sales_order_no"];
				}
				else
				{
					if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
				}
			}
		}
		$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
		$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
		$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
		$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")];
		$batch_array[$row[csf("id")]]['order'] = $order_no;
		$batch_array[$row[csf("id")]]['batch_type'] = $batch_type;
	}


	$sql_recipe_mst = "SELECT id, labdip_no, company_id,working_company_id as w_com_id,location_id, recipe_description, batch_id,machine_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, remarks, batch_qty from pro_recipe_entry_mst where id='$data[1]'";
	// echo $sql_recipe_mst;
	$dataArray = sql_select($sql_recipe_mst);
	$w_com_id=$dataArray[0][csf('w_com_id')];
	$company_id=$dataArray[0][csf('company_id')];
	$batch_id=$dataArray[0][csf('batch_id')];
	$location_id=$dataArray[0][csf('location_id')];
	$booking_no=$dataArray[0][csf('style_or_order')];
	$order_array = return_library_array("select id, order_no from subcon_ord_dtls b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id' ", "id", "order_no");
	$po_arr = return_library_array("select b.id,b.po_number from wo_po_break_down b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id'", 'id', 'po_number');

	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$w_com_id");
	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	$group_id=$nameArray[0][csf('group_id')];

	$groupingArray = sql_select("SELECT grouping from wo_booking_mst a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='$booking_no'");
	$internal_ref=$groupingArray[0][csf('grouping')];

	
	//lot start
	$roll_maintained = return_field_value("fabric_roll_level", "variable_settings_production", "company_name=$company_id and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	if ($roll_maintained==1)
	{
		$sql_prod= "SELECT distinct d.yarn_lot as yarn_lot from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d where a.id=b.mst_id and b.barcode_no=c.barcode_no and c.dtls_id=d.id and a.company_id=$company_id and a.id in($batch_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form in(2,22)";
	}
	else
	{
		$sql_prod = "SELECT distinct d.yarn_lot as yarn_lot from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d, inv_receive_master e where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22) group by  d.yarn_lot,d.yarn_count d.brand_id";
	}

	$result_sql_prod = sql_select($sql_prod);
	// echo "<pre>";
	// print_r($result_sql_prod);die;
	$yarn_lot="";
	foreach ($result_sql_prod as $row)
	{
		if($row[csf('yarn_lot')]!='')
		{
			$yarn_lot =  $row[csf('yarn_lot')];
			
			// if($yarn_lot=="") $yarn_lot=$row[csf('yarn_lot')];else $yarn_lot.=",".$row[csf('yarn_lot')];
		}
	}
	//lot end
	
	
	$sales_order_result = sql_select("select b.id,b.job_no,b.within_group,b.sales_booking_no,b.style_ref_no from fabric_sales_order_mst b,pro_batch_create_mst c where b.id=c.sales_order_id and c.status_active=1 and c.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.id='$batch_id' and c.company_id='$data[0]'");

	$sales_arr = array();
	foreach ($sales_order_result as $sales_row) {
		$style_ref	= $sales_row[csf("style_ref_no")];
	}
	?>
	<div style="width:930px; font-size:9px">
		<table width="930" cellspacing="0" align="right" border="0">
			<tr>
				<td colspan="6" align="center" style="font-size:x-large">
					<strong><? echo 'Working Company : '.$company_library[$w_com_id]; ?></strong>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center">
					<?

						echo "Location Name: ".$location_arr[$location_id];
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:20px"><u><strong><? echo 'Owner Company : '.$company_library[$data[0]].'<br>'.$data[3]; ?></strong></u></td>
			</tr>
			<tr>
				<td width="130"><strong>System ID:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('id')]; ?></td>
				<td width="130"><strong>Labdip No: </strong></td>
				<td width="175px"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
				<td width="130"><strong>Recipe Des.:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('recipe_description')]; ?></td>
			</tr>
			<tr>
				<td><strong>Batch No:</strong></td>
				<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_no']; ?></td>
				<td><strong>Recipe Date:</strong></td>
				<td> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
				<td><strong>Order Source:</strong></td>
				<td><? echo $order_source[$dataArray[0][csf('order_source')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Buyer Name:</strong></td>
				<td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
				<td><strong>Booking:</strong></td>
				<td><? echo $dataArray[0][csf('style_or_order')]; ?></td>
				<td><strong>Color:</strong></td>
				<td> <? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Color Range:</strong></td>
				<td><? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
            	<!-- <td><strong>B/L Ratio:</strong></td> <td><? echo $dataArray[0][csf('batch_ratio')] . ':' . $dataArray[0][csf('liquor_ratio')]; ?></td>
            	<td><strong>Total Liq.:</strong></td><td> <? echo $dataArray[0][csf('total_liquor')]; ?></td>-->
            	<td><strong>Batch Weight:</strong></td>
            	<td><? $batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];echo $batch_weight; ?></td>
            	<td><strong>Trims Weight:</strong></td>
            	<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight']; ?></td>
            </tr>
            <tr>
            	<td><strong>Order No.:</strong></td>
            	<td><? if ($batch_array[$dataArray[0][csf('batch_id')]]['batch_against'] == 3) echo "Sample Without Order"; else echo $batch_array[$dataArray[0][csf('batch_id')]]['order']; ?></td>
        		<td><strong>Method:</strong></td>
        		<td><? echo $dyeing_method[$dataArray[0][csf('method')]]; ?></td>
                <td><strong>Machine No:</strong></td>
        		<td><? echo $machine_arr[$dataArray[0][csf('machine_id')]]; ?></td>
            </tr>
        	<tr>
				<td><strong>Style Reff:</strong></td>
        		<td colspan="1"><? echo $style_ref; ?></td>
				<td><strong>Internal Ref No:</strong></td>
        		<td><? echo $internal_ref; ?></td>
        		<?
        		if ($batch_weight!=$dataArray[0][csf('batch_qty')]) 
        		{
        			?>
        			<td><strong>Batch Wgt. R:</strong></td>
        			<td title="<? echo $batch_weight.'!='.$dataArray[0][csf('batch_qty')]; ?>">
        			<?
        			echo $dataArray[0][csf('batch_qty')];
        			?>
        			</td>
        			<?
        		}
        		?>
        	</tr>
			<tr>
        		<td><strong>Remarks:</strong></td>
        		<td colspan="3"><? echo $dataArray[0][csf('remarks')]; ?></td>
        		<td><strong>Yarn Lot:</strong></td>
        		<td ><? echo $yarn_lot; ?></td>
        	</tr>
        </table>
        <br>
        <?
		$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
		$j = 1;
		$entryForm = $entry_form_arr[$batch_id_qry[0]];
		?>
        <table width="930" cellspacing="0"  class="rpt_table" border="1" style="font-size:20px;" rules="all">
			<thead bgcolor="#dddddd" align="center">

					<tr bgcolor="#CCCCFF">
						<th colspan="5" align="center"><strong>Fabrication</strong></th>
					</tr>
					<tr>
						<th width="30" height="40">SL</th>
						<th width="80" height="40">Dia/ W. Type</th>
						<th width="200" height="40">Constrution & Composition</th>
						<th width="70" height="40">Gsm</th>
						<th width="70" height="40">Dia</th>
					</tr>
			</thead>
			<tbody>
				<?
				foreach ($batch_id_qry as $b_id)
				{
					$batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
					$result_batch_query = sql_select($batch_query);
					foreach ($result_batch_query as $rows)
					{
						if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$fabrication_full = $rows[csf("item_description")];
						$fabrication = explode(',', $fabrication_full);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center" height="40"><? echo $j; ?></td>
							<td align="center" height="40"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>
							<td align="left" height="40"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
							<td align="center" height="40"><? echo $fabrication[2]; ?></td>
							<td align="center" height="40"><? echo $fabrication[3]; ?></td>
						</tr>
						<?
						$j++;
					}
				}
				?>
			</tbody>
		</table>
        <br> <br>
        <div style="width:100%;">
        	<table  cellspacing="0" width="1300" border="1" rules="all" class="rpt_table" style="font-size:20px;">
        		<thead bgcolor="#dddddd" align="center">
					<tr>
						<th rowspan="2" width="30" height="40" >SL</th>
						<th rowspan="2" width="110" height="40">Item Cat.</th>
						<th rowspan="2" width="110" height="40">Product ID</th>
						<th rowspan="2" width="200" height="40">Item Group</th>
						<th rowspan="2" width="220" height="40">Item Description</th>
						<th rowspan="2" width="80" height="40">Item Lot</th>
						<th rowspan="2" width="50" height="40">UOM</th>
						<th rowspan="2" width="100" height="40">Dose Base</th>
						<th rowspan="2" width="100" height="40">Ratio</th>
						<th rowspan="2" width="80" height="40">Recipe Qty</th>
						<th colspan="3" width="200">Amount/KG</th>
						<th rowspan="2" width="" height="40">Comments</th>
					</tr>
					<tr>
						<th>KG</th>
						<th>Gram</th>
						<th>Miligram</th>
                	</tr>
        		</thead>
        		<?
        		$i = 1;
        		$j = 1;
        		$mst_id = $data[1];
        		$com_id = $data[0];


        		$process_array = array();
        		$sub_process_data_array = array();
        		$sub_process_remark_array = array();
        		$sql_ratio = "select id, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by id";
        		$nameArray = sql_select($sql_ratio);
        		foreach ($nameArray as $row) {
        			if (!in_array($row[csf("sub_process_id")], $process_array)) {
        				$process_array[] = $row[csf("sub_process_id")];
        				$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
        			}
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
        		}

        		if ($db_type == 2)
        		{
					/*$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio from product_details_master a, pro_recipe_entry_dtls b where a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$com_id' and a.item_category_id in(5,6,7) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 order by b.sub_process_id, b.seq_no";*/

					$sql = "(SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no, c.batch_qty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0  )
					union
					(
					SELECT null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no, null as batch_qty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
					)  order by seq_no,sub_process_id";
        		}
        		else if ($db_type == 0)
        		{
					$sql = "(SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio, b.seq_no as seq_no, c.batch_qty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 )
					union
					(
						SELECT null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio,b.seq_no as seq_no, null as batch_qty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
					)  ";
        		}
				//echo $sql;
        		$sql_result = sql_select($sql);
				$prodIdChk = array();
				$prodIdArr = array();
	            foreach ($sql_result as $row)
	            {
	            	$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")]. "**" . $row[csf("current_stock")] . "**" . $row[csf("prod_id")] . "**" . $row[csf("batch_qty")] . "$$$";

					if($prodIdChk[$row[csf('prod_id')]] == "")
					{
						$prodIdChk[$row[csf('prod_id')]] = $row[csf('prod_id')];
						array_push($prodIdArr,$row[csf('prod_id')]);
					}

	            }

				//var_dump($prodIdArr);
				$sql_prod_info = "SELECT id, avg_rate_per_unit from product_details_master where status_active=1 and is_deleted=0 ".where_con_using_array($prodIdArr,0,'id')."";
				//echo $sql_prod_info;
        		$prodInfoArray = sql_select($sql_prod_info);
				$prod_data_array = array();
        		foreach ($prodInfoArray as $row)
				{
        			$prod_data_array[$row[csf("id")]]['avg_rate_per_unit'] = $row[csf("avg_rate_per_unit")];
        		}
				//var_dump($prod_data_array);

				$grand_tot_ratio = 0;
				$grand_tot_recipe_qty = 0;
				$grand_tot_avg_rate = 0;
	            foreach ($process_array as $process_id)
	            {
	            	$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];

	            	$liquor_ratio_process = $sub_process_remark_array[$process_id]['liquor_ratio'];
	            	$remark = $sub_process_remark_array[$process_id]['remark'];
	            	if ($remark == '') $remark = ''; else $remark .= $remark . ', ';
	            	$tot_ratio=1.5;
	            	$leveling_water=$batch_weight*($liquor_ratio_process-$tot_ratio) ;

	            	?>
	            	<tr bgcolor="#EEEFF0">
	            		<td colspan="14" align="left" height="25"><b>Sub Process
	            			Name:- <? echo $dyeing_sub_process[$process_id] . ', ' . $remark .' Total Liquor(ltr): '.$liquor_ratio.', '. 'Levelling  Water(Ltr): '.number_format($leveling_water,2,'.',''); ?></b>
	            		</td>
	            	</tr>
	            	<?
	            	$tot_ratio = 0;$tot_recipe_qty = 0;
	            	//$sub_process_data = array_filter(explode("@@@",$sub_process_data_array2[$process_id]));
					//$sub_process_data = explode("$$$", substr($sub_process_data_array[$process_id], 0, -1));
					$sub_process_dataArr=rtrim($sub_process_data_array[$process_id],'$$$');
					$sub_process_data = explode("$$$", $sub_process_dataArr);
					//echo count($sub_process_data).'='.$process_id.'<br>';

	            	foreach ($sub_process_data as $data) 
	            	{
	            		$data = explode("**", $data);
	            		$current_stock = $data[13];
	            		$current_stock_check=number_format($current_stock,7,'.','');
	            		//if($process_id==93 || $process_id==94 || $process_id==95 || $process_id==96 || $process_id==97 || $process_id==98 || $process_id == 140 || $process_id == 141 || $process_id == 142 || $process_id == 143)
						if (in_array($process_id, $subprocessForWashArr))
						{
							$id = $data[0];
                			$item_category_id = $data[1];
                			$item_group_id = $data[2];
                			$sub_group_name = $data[3];
                			$item_description = $data[4];
                			$item_size = $data[5];
                			$unit_of_measure = $data[6];
                			$dtls_id = $data[7];
                			$sub_process_id = $data[8];
                			$item_lot = $data[9];
                			$dose_base_id = $data[10];
                			$ratio = $data[11];
                			$comments = $data[12];
                			$prod_id = $data[14];
							$batch_qty = $data[15];
							// echo $dose_base_id.'='.$ratio.'='.$batch_weight.'='.$liquor_ratio_process.'<br>';
							if($dose_base_id==1){

								$amount = number_format($ratio*$batch_weight*$liquor_ratio_process/1000,6, '.', '');

								$amount = explode('.',$amount);
							}
							else{

								$amount = number_format($ratio*$batch_weight/100,6, '.', '');

								$amount = explode('.',$amount);
							}

							if ($dose_base_id == 1) {

								$recipe_qty = number_format(($liquor_ratio/1000)*$ratio, 4, '.', '');

							} else {
								$recipe_qty = number_format(($batch_qty*$ratio)/100, 4, '.', '');

							}
                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                			?>
                			<tr bgcolor="<? echo $bgcolor; ?>">
                				<td height="40"><? echo $i; ?></td>
                				<td height="40"><p><? echo $item_category[$item_category_id]; ?></p></td>
                				<td height="40"><p><? echo $prod_id; ?></p></td>
                				<td height="40"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
                				<td height="40"><p><? echo $item_description; ?></p></td>
                				<td height="40"><p><? echo $item_lot; ?></p></td>
                				<td height="40" align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                				<td height="40"><p><? echo $dose_base[$dose_base_id]; ?></p></td>
                				<td height="40" align="right"><strong><? echo number_format($ratio, 6, '.', ''); ?></strong>&nbsp;</td>
                				<td height="40" align="right" title="Recipe Qty=(Total Liquor(ltr)/1000)*Ratio"><strong><? echo $recipe_qty; ?></strong>&nbsp;</td>
								<td align="right"><? echo $amount[0]; ?>&nbsp;</td>
								<td align="right"><? echo number_format(substr($amount[1], 0, 3)); ?>&nbsp;</td>
								<td align="right"><? echo number_format(substr($amount[1], 3, 6)); ?>&nbsp;</td>
                				<td height="40" align="right"><? echo $comments; ?>&nbsp;</td>
                			</tr>
                			<?
                			$tot_ratio += $ratio;
                			$grand_tot_ratio += $ratio;
                			$tot_recipe_qty +=$recipe_qty;
                			$grand_tot_recipe_qty +=$recipe_qty;
							$grand_tot_avg_rate +=$prod_data_array[$prod_id]['avg_rate_per_unit']*$recipe_qty;
							$grand_tot_kg += $amount[0];
							$grand_tot_gm += substr($amount[1], 0, 3);
							$grand_tot_mgm += substr($amount[1], 3, 6);
							$tot_kg += $amount[0];
							$tot_gm += substr($amount[1], 0, 3);
							$tot_mgm += substr($amount[1], 3, 6);
                			$i++;
						}
						else
						{
	                		//if($current_stock_check>0)
	                		//{
	                			$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];
								$batch_qty = $data[15];
								// echo $dose_base_id.'='.$ratio.'='.$batch_weight.'='.$liquor_ratio_process.'<br>';
								if($dose_base_id==1){

									$amount = number_format($ratio*$batch_weight*$liquor_ratio_process/1000,6, '.', '');
	
									$amount = explode('.',$amount);
								}
								else{
	
									$amount = number_format($ratio*$batch_weight/100,6, '.', '');
	
									$amount = explode('.',$amount);
								}

								if ($dose_base_id == 1) {

									$recipe_qty = number_format(($liquor_ratio/1000)*$ratio, 4, '.', '');

								} else {
									$recipe_qty = number_format(($batch_qty*$ratio)/100, 4, '.', '');

								}
	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
	                			<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td height="40"><?echo $i; ?></td>
	                				<td height="40"><p><? echo $item_category[$item_category_id]; ?></p></td>
	                				<td height="40"><p><? echo $prod_id; ?></p></td>
	                				<td height="40"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
	                				<td height="40"><p><? echo $item_description; ?></p></td>
	                				<td height="40"><p><? echo $item_lot; ?></p></td>
	                				<td height="40" align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
	                				<td height="40"><p><? echo $dose_base[$dose_base_id]; ?></p></td>
	                				<td height="40" align="right"><strong><? echo number_format($ratio, 6, '.', ''); ?></strong>&nbsp;</td>
	                				<td height="40" align="right" title="Recipe Qty=(Total Liquor(ltr)/1000)*Ratio"><strong><? echo $recipe_qty; ?></strong>&nbsp;</td>
									<td align="right"><? echo $amount[0]; ?>&nbsp;</td>
	                				<td align="right"><? echo number_format(substr($amount[1], 0, 3)); ?>&nbsp;</td>
	                				<td align="right"><? echo number_format(substr($amount[1], 3, 6)); ?>&nbsp;</td>
	                				<td height="40" align="right"><? echo $comments; ?>&nbsp;</td>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
	                			$tot_recipe_qty +=$recipe_qty;
	                			$grand_tot_recipe_qty +=$recipe_qty;
								$grand_tot_avg_rate +=$prod_data_array[$prod_id]['avg_rate_per_unit']*$recipe_qty;
								$grand_tot_kg += $amount[0];
								$grand_tot_gm += substr($amount[1], 0, 3);
								$grand_tot_mgm += substr($amount[1], 3, 6);
								$tot_kg += $amount[0];
								$tot_gm += substr($amount[1], 0, 3);
								$tot_mgm += substr($amount[1], 3, 6);
	                			$i++;
	                		//}
	            		}
	            	}

	            }
				if($tot_mgm>=1000){
					$tot_gm += intdiv($tot_mgm, 1000);
					$tot_mgm = $tot_mgm%1000;
				}

				if($tot_gm>=1000){
					$tot_kg += intdiv($tot_gm, 1000);
					$tot_gm = $tot_gm%1000;
				}
	            ?>
	            <tr class="tbl_bottom">
	            	<td align="right" colspan="8" height="20"><strong>Grand Total :</strong></td>
	            	<td align="right" height="20" ><strong><? echo number_format($grand_tot_ratio, 6, '.', ''); ?></strong>&nbsp;</td>
	            	<td align="right"  height="20"><strong><? echo number_format($grand_tot_recipe_qty, 4, '.', ''); ?></strong>&nbsp;</td>
					<td align="right"><? echo $tot_kg; ?>&nbsp;</td>
                    <td align="right"><? echo $tot_gm; ?>&nbsp;</td>
                    <td align="right"><? echo $tot_mgm ?>&nbsp;</td>
	                <td align="right"  height="20">&nbsp;</td>
	            </tr>
				<tr class="tbl_bottom">
	            	<td align="right"  height="20" colspan="8"><strong>Total Cost[TK] : </strong></td>
	            	<td align="center"  height="20" colspan="2" ><strong><? echo number_format($grand_tot_avg_rate, 4, '.', ''); ?></strong>&nbsp;</td>
					<td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
	                <td align="right"  height="20">&nbsp;</td>
	            </tr>
				<tr class="tbl_bottom">
	            	<td align="right"  height="20"  colspan="8"><strong>Cost Per KG[TK] : </strong></td>
	            	<td align="center"  height="20" colspan="2" ><strong><? $per_kg = ($grand_tot_avg_rate /$batch_weight); echo number_format($per_kg, 4, '.', ''); ?></strong>&nbsp;</td>
					<td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
	                <td align="right"  height="20">&nbsp;</td>
	            </tr>
    		</table>
	        <br>
	        <?
	        echo signature_table(62, $com_id, "1030px","","50px");
	        ?>
		</div>
	</div>
	<?
}

if ($action == "recipe_entry_print_8")
{
		extract($_REQUEST);
		$data = explode('*', $data);
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		//$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
		//$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
		$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
		$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
		$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
		$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');

		$batch_array = array();
		if ($db_type == 0) {
			$sql = "select a.id, a.batch_no,a.batch_against, a.entry_form, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
		} else if ($db_type == 2) {
			$sql = "select a.id, a.batch_no,a.batch_against,a.entry_form, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,  sum(b.batch_qnty) as batch_qnty  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.batch_against, a.batch_no,a.entry_form,a.total_trims_weight order by a.id DESC";
		}
		// echo $sql;
		$result_sql = sql_select($sql);
		foreach ($result_sql as $row) {
			$order_no = '';
			$order_id = array_unique(explode(",", $row[csf("po_id")]));
			if ($row[csf("entry_form")] == 36) {
				$batch_type = "<b> SUBCONTRACT ORDER </b>";
				foreach ($order_id as $val) {
					if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];
				}
			} else {
				$batch_type = "<b> SELF ORDER </b>";
				foreach ($order_id as $val) {
					if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
				}
			}
			$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
			$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
			$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
			$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")];
			$batch_array[$row[csf("id")]]['order'] = $order_no;
			$batch_array[$row[csf("id")]]['batch_type'] = $batch_type;
		}




		$sql_recipe_mst = "select id, labdip_no, company_id,working_company_id as w_com_id,location_id, recipe_description, batch_id,machine_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, remarks from pro_recipe_entry_mst where id='$data[1]'";
		$dataArray = sql_select($sql_recipe_mst);
		$batch_id=$dataArray[0][csf('batch_id')];
		$w_com_id=$dataArray[0][csf('w_com_id')];
		$company_id=$dataArray[0][csf('company_id')];
		$order_source=$dataArray[0][csf('order_source')];
		$style_or_order_booking=$dataArray[0][csf('style_or_order')];

		$order_array = return_library_array("select id, order_no from subcon_ord_dtls b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id' ", "id", "order_no");
		//$po_arr = return_library_array("select b.id,b.po_number from wo_po_break_down b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id'", 'id', 'po_number');


		$po_arr = sql_select("select b.id,b.po_number,c.mst_id as batch_id, b.grouping from wo_po_break_down b,wo_po_details_master a,pro_batch_create_dtls c where  a.job_no=b.job_no_mst and b.id=c.po_id and b.status_active=1 and b.is_deleted=0 and c.mst_id='$batch_id'");
		$internal_ref_arr = array();

		foreach ($po_arr as $row) {

			$internal_ref_arr[$row[csf('batch_id')]] = $row[csf('grouping')];
			$po_arr[$row[csf('id')]] = $row[csf('po_number')];

		}
		unset($po_arr);

		$total_batch_weight =  $dataArray[0][csf('batch_qty')];

		$mst_id = $dataArray[0][csf('id')];
		$total_liquor_ratio_sql = sql_select("select liquor_ratio, total_liquor, check_id from pro_recipe_entry_dtls where mst_id=" . $mst_id . " and ratio>0 group by liquor_ratio, total_liquor, check_id");
		$total_liquor_ratio = 0;
		foreach($total_liquor_ratio_sql as $value){

			$total_liquor_ratio += $value[csf('liquor_ratio')];
		}
		$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$w_com_id");
	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
		$group_id=$nameArray[0][csf('group_id')];

			$internal_ref_data = sql_select("select grouping from wo_po_break_down a, wo_booking_dtls b where b.po_break_down_id=a.id and b.booking_no='$style_or_order_booking' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
		?>
		<div style="width:930px; font-size:6px">
			<table width="930" cellspacing="0" align="right" border="0">
				<tr>
					<td colspan="6" align="center" style="font-size:x-large">
						<strong><? echo 'Working Company : '.$company_library[$w_com_id]; ?>
					</strong></td>
				</tr>
				<tr>
					<td colspan="6" align="center">
						<?

						foreach ($nameArray as $result) {
							?>
							Plot No: <? echo $result[csf('plot_no')]; ?>
							Level No: <? echo $result[csf('level_no')]; ?>
							Road No: <? echo $result[csf('road_no')]; ?>
							Block No: <? echo $result[csf('block_no')]; ?>
							Zip Code: <? echo $result[csf('zip_code')];
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:20px"><u><strong><? echo 'Owner Company : '.$company_library[$data[0]].'<br>'.$data[3]; ?></strong></u></td>
				</tr>
				<tr>
					<td width="130"><strong>System ID:</strong></td>
					<td width="175"><? echo $dataArray[0][csf('id')]; ?></td>
					<td width="130"><strong>Labdip No: </strong></td>
					<td width="175px"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
					<td width="130"><strong>Recipe Des.:</strong></td>
					<td width="175"><? echo $dataArray[0][csf('recipe_description')]; ?></td>
				</tr>
				<tr>
					<td><strong>Batch No:</strong></td>
					<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_no']; ?></td>
					<td><strong>Recipe Date:</strong></td>
					<td> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
					<td><strong>Order Source:</strong></td>
					<td><? echo $order_source[$dataArray[0][csf('order_source')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Buyer Name:</strong></td>
					<td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
					<td><strong>Booking:</strong></td>
					<td><? echo $dataArray[0][csf('style_or_order')]; ?></td>
					<td><strong>Color:</strong></td>
					<td> <? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Color Range:</strong></td>
					<td><? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
                <!-- <td><strong>B/L Ratio:</strong></td> <td><? echo $dataArray[0][csf('batch_ratio')] . ':' . $dataArray[0][csf('liquor_ratio')]; ?></td>
                	<td><strong>Total Liq.:</strong></td><td> <? echo $dataArray[0][csf('total_liquor')]; ?></td>-->
                	<td><strong>Batch Weight:</strong></td>
                	<td><? $batch_weight=($batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'] + $batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight']); echo $batch_weight; ?></td>
                	<!-- <td><strong>Trims Weight:</strong></td>
                	<td><? //echo $batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight']; ?></td> -->
                </tr>
                <tr>
                	<td><strong>Order No.:</strong></td>
                	<td>
                		<? if ($batch_array[$dataArray[0][csf('batch_id')]]['batch_against'] == 3) echo "Sample Without Order";
                		else echo $batch_array[$dataArray[0][csf('batch_id')]]['order']; ?></td>
                		<td><strong>Method:</strong></td>
                		<td><? echo $dyeing_method[$dataArray[0][csf('method')]]; ?></td>
                        <td><strong>Machine no:</strong></td>
                		<td><? echo $machine_arr[$dataArray[0][csf('machine_id')]]; ?></td>
                	</tr>
                	<tr>
                		<td><strong>Remarks:</strong></td>
                		<td ><? echo $dataArray[0][csf('remarks')]; ?></td>
                		<!-- <td><strong>Liquor Ratio:</strong></td>
                		<td ><? //echo $total_liquor_ratio ?></td> -->
                		<td><strong>Internal Ref:</strong></td>
                		<td ><? 

                			//echo $internal_ref_arr[$dataArray[0][csf('batch_id')]]; 
                		 	echo ($internal_ref_data[0][GROUPING]);

                		?></td>
                	</tr>
                </table>
                <br>
                <?
				$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
				$j = 1;
				$entryForm = $entry_form_arr[$batch_id_qry[0]];
			?>
                <table width="930" cellspacing="0" align="right" class="rpt_table" border="1" style="font-size:16px; margin:5px;" rules="all">
				<thead bgcolor="#dddddd" align="center">

						<tr bgcolor="#CCCCFF">
							<th colspan="5" align="center"><strong>Fabrication</strong></th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="80">Dia/ W. Type</th>
							<th width="200">Constrution & Composition</th>
							<th width="70">Gsm</th>
							<th width="70">Dia</th>
						</tr>
				</thead>
				<tbody>
					<?
						foreach ($batch_id_qry as $b_id) {
							 $batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
							$result_batch_query = sql_select($batch_query);
							foreach ($result_batch_query as $rows) {
								if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								$fabrication_full = $rows[csf("item_description")];
								$fabrication = explode(',', $fabrication_full);
					?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td align="center"><? echo $j; ?></td>
									<td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>
									<td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
									<td align="center"><? echo $fabrication[2]; ?></td>
									<td align="center"><? echo $fabrication[3]; ?></td>
								</tr>
					<?
								$j++;
							}
						}
					?>
				</tbody>
			</table>
            <br> <br> <br>
                <div style="width:100%;">
                	<table align="right" style="margin:5px;" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
                		<thead bgcolor="#dddddd" align="center">
                			<tr>
                				<th rowspan="2" width="30">SL</th>
	                			<th rowspan="2" width="110">Item Cat.</th>
	                			<th rowspan="2" width="110">Product ID</th>
	                			<th rowspan="2" width="220">Item Description</th>
	                			<th rowspan="2" width="200">Item Group</th>
	                			<th rowspan="2" width="80">Item Lot</th>
	                			<th rowspan="2" width="50">UOM</th>
	                			<th rowspan="2" width="100">Dose Base</th>
	                			<th rowspan="2" width="100">Ratio/Dose</th>
	                			<th colspan="3" width="200">Amount/KG</th>
	                			<th rowspan="2" width="">Comments</th>
                			</tr>
                			<tr>
                				<th>KG</th>
                				<th>Gram</th>
                				<th>Miligram</th>
                			</tr>

                		</thead>
                		<?
                		$i = 1;
                		$j = 1;
                		$mst_id = $data[1];
                		$com_id = $data[0];


                		$process_array = array();
                		$sub_process_data_array = array();
                		$sub_process_remark_array = array();
                		$sql_ratio = "select id, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by id";
						// echo $sql_ratio; die;
                		$nameArray = sql_select($sql_ratio);
                		foreach ($nameArray as $row) {
                			if (!in_array($row[csf("sub_process_id")], $process_array)) {
                				$process_array[] = $row[csf("sub_process_id")];
                				$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
                			}
                			$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
                			$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];


                		}

                		if ($db_type == 2) {
                			/*$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio from product_details_master a, pro_recipe_entry_dtls b where a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$com_id' and a.item_category_id in(5,6,7) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 order by b.sub_process_id, b.seq_no";*/

                			$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0  )
                			union
                			(
                			select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
                		)  order by seq_no,sub_process_id";
                	} else if ($db_type == 0) {
                		$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio, b.seq_no as seq_no from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 )
                		union
                		(
                		select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio,b.seq_no as seq_no from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
                	)  ";
                }
				// echo $sql;
                $sql_result = sql_select($sql);

                foreach ($sql_result as $row) {
                	$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")]. "**" . $row[csf("current_stock")] . "**" . $row[csf("prod_id")] . "$$$";

                }
				//var_dump($sub_process_data_array);
                foreach ($process_array as $process_id) {
                	$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];
                	$l_ratio = $sub_process_remark_array[$process_id]['liquor_ratio'];

                	$liquor_ratio_process = $sub_process_remark_array[$process_id]['liquor_ratio'];
                	$remark = $sub_process_remark_array[$process_id]['remark'];
                	// if ($remark == '') $remark = ''; else $remark .= $remark . ', ';
                	$tot_ratio=1.5;
                	$leveling_water=$batch_weight*($liquor_ratio_process-$tot_ratio) ;


                	?>
                	<tr bgcolor="#EEEFF0">
                		<td colspan="13" align="left"><b>Sub Process
                			Name:- <? echo $dyeing_sub_process[$process_id]. ' ';?>
							<? echo "(Liquor Ratio: 1:". number_format($liquor_ratio_process,0,'.',''). ") Water ".number_format($liquor_ratio,0,'.',''). " LTR ". $remark?>
							
						</b>
                		</td>
                	</tr>
                	<?
                	$tot_ratio = 0;
                	$tot_kg = 0;
                	$tot_gm = 0;
                	$tot_mgm = 0;
                	//$sub_process_data = array_filter(explode("@@@",$sub_process_data_array2[$process_id]));
					//$sub_process_data = explode("$$$", substr($sub_process_data_array[$process_id], 0, -1));
					$sub_process_dataArr=rtrim($sub_process_data_array[$process_id],'$$$');
					$sub_process_data = explode("$$$", $sub_process_dataArr);
					//echo count($sub_process_data).'='.$process_id.'<br>';
                	foreach ($sub_process_data as $data) {
                		$data = explode("**", $data);
                		$current_stock = $data[13];
                		$current_stock_check=number_format($current_stock,7,'.','');

                	//	if($process_id==93 || $process_id==94 || $process_id==95 || $process_id==96 || $process_id==97 || $process_id==98 || $process_id == 140 || $process_id == 141 || $process_id == 142 || $process_id == 143)
						if(in_array($process_id, $subprocessForWashArr))
						{
							$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];

	                			

	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
	                			<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td><? echo $i; ?></td>
	                				<td><p><? echo $item_category[$item_category_id]; ?></p></td>
	                				<td><p><? echo $prod_id; ?></p></td>
	                				<td><p><? echo $item_description; ?></p></td>
	                				<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
	                				<td><p><? echo $item_lot; ?></p></td>
	                				<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
	                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
	                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
	                				<td align="right"><?
									// if($dose_base_id==1 && $item_category_id==5)
									if($dose_base_id==1)
									{

										$amount = number_format($ratio*$batch_weight*$liquor_ratio_process/1000,6, '.', '');
	
										$amount = explode('.',$amount);
									}
									else{
	
										$amount = number_format($ratio*$total_batch_weight/100,6, '.', '');
	
										$amount = explode('.',$amount);
									}
									echo $amount[0]; ?>&nbsp;</td>
	                				<td align="right"><? echo number_format(substr($amount[1], 0, 3)); ?>&nbsp;</td>
	                				<td align="right"><? echo number_format(substr($amount[1], 3, 6)); ?>&nbsp;</td>
	                				<td align="right"><? echo $comments; ?>&nbsp;</td>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
	                			$grand_tot_kg += $amount[0];
	                			$grand_tot_gm += substr($amount[1], 0, 3);
	                			$grand_tot_mgm += substr($amount[1], 3, 6);
	                			$tot_kg += $amount[0];
			                	$tot_gm += substr($amount[1], 0, 3);
			                	$tot_mgm += substr($amount[1], 0, 3);
	                			$i++;
						}
						else
						{


	                		if($current_stock_check>0)
	                		{
	                			$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];


	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
	                			<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td><? echo $i;?></td>
	                				<td><p><? echo $item_category[$item_category_id]; ?></p></td>
	                				<td><p><? echo $prod_id; ?></p></td>
	                				<td><p><? echo $item_description; ?></p></td>
	                				<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
	                				<td><p><? echo $item_lot; ?></p></td>
	                				<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
	                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
	                				<td align="right"><? echo number_format($ratio, 6, '.', '');?>&nbsp;</td>
	                				<td align="right"><?
									// if($dose_base_id==1 && $item_category_id==5)
									if($dose_base_id==1)
									{

										$amount = number_format($ratio*$batch_weight*$liquor_ratio_process/1000,6, '.', '');
										// echo "ratio: ".$ratio."<br>";
										// echo "batch_weight: ".$batch_weight."<br>";
										// echo "liquor_ratio_process: ".$liquor_ratio_process."<br>";
										$amount = explode('.',$amount);
									}
									else{
	
										$amount = number_format($ratio*$batch_weight/100,6, '.', '');
	
										$amount = explode('.',$amount);
									}
									echo $amount[0]; ?>&nbsp;</td>
	                				<td align="right"><? echo number_format(substr($amount[1], 0, 3)); ?>&nbsp;</td>
	                				<td align="right"><? echo number_format(substr($amount[1], 3, 6)); ?>&nbsp;</td>
	                				<td align="right"><? echo $comments; ?>&nbsp;</td>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
	                			$grand_tot_kg += $amount[0];
	                			$grand_tot_gm += substr($amount[1], 0, 3);
	                			$grand_tot_mgm += substr($amount[1], 3, 6);
	                			$tot_kg += $amount[0];
			                	$tot_gm += substr($amount[1], 0, 3);
			                	$tot_mgm += substr($amount[1], 3, 6);
	                			$i++;
	                		}
                		}
                	}

                	if($tot_mgm>=1000){
                		$tot_gm += intdiv($tot_mgm, 1000);
                		$tot_mgm = $tot_mgm%1000;
                	}

                	if($tot_gm>=1000){
                		$tot_kg += intdiv($tot_gm, 1000);
                		$tot_gm = $tot_gm%1000;
                	}
                	?>
                	<tr class="tbl_bottom">
                		<td align="right" colspan="8"><strong>Sub Process Total</strong></td>
                		<td align="right"><? echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo $tot_kg; ?>&nbsp;</td>
                        <td align="right"><? echo $tot_gm; ?>&nbsp;</td>
                        <td align="right"><? echo $tot_mgm ?>&nbsp;</td>
                        <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                	</tr>
                	<?
                }
                	if($grand_tot_mgm>=1000){
                		$grand_tot_gm += intdiv($grand_tot_mgm, 1000);
                		$grand_tot_mgm = $grand_tot_mgm%1000;
                	}

                	if($grand_tot_gm>=1000){
                		$grand_tot_kg += intdiv($grand_tot_gm, 1000);
                		$grand_tot_gm = $grand_tot_gm%1000;
                	}
                ?>

                <tr class="tbl_bottom">
                	<td align="right" colspan="8"><strong>Grand Total</strong></td>
                	<td align="right"><? echo number_format($grand_tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                    <td align="right"><? echo $grand_tot_kg; ?>&nbsp;</td>
                    <td align="right"><? echo $grand_tot_gm; ?>&nbsp;</td>
                    <td align="right"><? echo $grand_tot_mgm; ?>&nbsp;</td>
                    <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                </tr>
            </table>
            <br>
            <?
            echo signature_table(62, $com_id, "1030px");
            ?>
        </div>
    </div>
    <?
}

if ($action == "recipe_entry_print_9") // for ccl
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$recipe_id=$data[1];
	$working_company=$data[4];
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');
	$user_name_library = return_library_array("select id, user_name from user_passwd", "id", "user_name");
	$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
	$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');

	$sql = "SELECT c.id as recipe_id, c.labdip_no, c.company_id,c.working_company_id as w_com_id,c.location_id, recipe_description, c.batch_id, c.machine_id, c.method, c.recipe_date, c.order_source, c.style_or_order, c.booking_id, c.total_liquor, c.batch_ratio, c.liquor_ratio, c.remarks, c.buyer_id, c.color_id, c.color_range, c.remarks as recipe_remarks, c.insert_date, c.inserted_by, c.batch_qty as qnty, c.copy_from, a.id, a.batch_no, a.batch_date, a.booking_no_id, a.booking_no, a.booking_without_order, a.sales_order_no, a.batch_type_id, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight as batch_weight, a.dyeing_machine, a.remarks as batch_remarks, a.collar_qty, a.cuff_qty, a.shift_id, a.double_dyeing, a.sales_order_id,a.is_sales, a.booking_entry_form , a.save_string
	from pro_recipe_entry_mst c, pro_batch_create_mst a
	where c.batch_id=a.id and c.id=$data[1] and a.is_sales=1 and a.status_active=1 and a.is_deleted=0";
	//echo $sql;die;
	$dataArray = sql_select($sql);
	$w_com_id=$dataArray[0][csf('w_com_id')];
	$company_id=$dataArray[0][csf('company_id')];
	$batch_id=$dataArray[0][csf('batch_id')];
	$location_id=$dataArray[0][csf('location_id')];
	$booking_no=$dataArray[0][csf('style_or_order')];
	$sales_order_id=$dataArray[0][csf('sales_order_id')];
	$color_id=$dataArray[0][csf('color_id')];
	$save_string_data = $dataArray[0][csf('save_string')];
	$sales_order_no = $dataArray[0][csf('sales_order_no')];
	$rbatch_weight = $dataArray[0][csf('qnty')];
	$copy_from_recipe_id = $dataArray[0][csf('copy_from')];
	$batch_weight = $dataArray[0][csf('batch_weight')];

	// Process Loss
	$sales_sql = "SELECT b.job_no_mst as booking_no,b.color_type_id,b.body_part_id,b.gsm_weight, b.dia, b.width_dia_type,b.process_loss,sum(b.grey_qty) qnty,b.color_id from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.job_no='$sales_order_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.job_no_mst ,b.color_type_id,b.body_part_id,b.gsm_weight, b.dia, b.width_dia_type,b.process_loss,b.color_id ";
	$sales_result = sql_select($sales_sql);
	foreach ($sales_result as $row)
	{
		$sales_process_loss_array[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]]['process_loss'] = $row[csf('process_loss')];
	}

	if ($dataArray[0][csf('is_sales')] == 1)
	{
		$sales_data = sql_select("SELECT id,job_no,sales_booking_no,within_group,buyer_id,style_ref_no, po_buyer from fabric_sales_order_mst where id=$sales_order_id");

		if ($sales_data[0][csf("within_group")] == 1)
		{
			$booking_data = sql_select("SELECT a.booking_no,a.buyer_id, a.fabric_source, a.booking_type, a.is_short, b.job_no, c.pub_shipment_date, c.grouping, d.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d 
			where a.booking_no=b.booking_no and b.po_break_down_id = c.id and c.job_id=d.id and  a.booking_no='$booking_no' and b.fabric_color_id='$color_id' and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 
			group by a.booking_no,a.buyer_id, a.fabric_source, a.booking_type, a.is_short, b.job_no, c.pub_shipment_date, c.grouping, d.style_ref_no");

			foreach ($booking_data as $row)
			{
				$job_number .= $row[csf('job_no')].",";
				$job_style .= $row[csf('style_ref_no')].",";
				$internal_ref .= $row[csf('grouping')].",";
				$book_fabric_source[$row[csf('booking_no')]]=$row[csf('fabric_source')];
				// $booking_type_id[$row[csf('booking_no')]]=$row[csf('booking_type')];

				if($row[csf("booking_type")]==1 && $row[csf("is_short")]==2)
	            {
	                $booking_type_arr[$row[csf("booking_no")]]="Main";
	            }
	            else if($row[csf("booking_type")]==1 && $row[csf("is_short")]==1)
	            {
	                $booking_type_arr[$row[csf("booking_no")]]="Short";
	            }
	            else if($row[csf("booking_type")]==4)
	            {
	                $booking_type_arr[$row[csf("booking_no")]]="Sample";
	            }

	            if($min_shipment_date == ""){
					$min_shipment_date = $row[csf('pub_shipment_date')];
				}

				if($max_shipment_date == ""){
					$max_shipment_date = $row[csf('pub_shipment_date')];
				}

				if(strtotime($row[csf('pub_shipment_date')]) > strtotime($min_shipment_date))
				{
					$min_shipment_date = $min_shipment_date;
				}else{
					$min_shipment_date = $row[csf('pub_shipment_date')];
				}


				if(strtotime($row[csf('pub_shipment_date')]) > strtotime($max_shipment_date))
				{
					$max_shipment_date = $row[csf('pub_shipment_date')];
				}
				else
				{
					$max_shipment_date = $max_shipment_date;
				}
			}
			$job_number = chop($job_number,",");
			$job_style = chop($job_style,",");
			$internal_ref = chop($internal_ref,",");
			//$job_number = $booking_data[0][csf("job_no")];
			$buyer_id = $booking_data[0][csf("buyer_id")];
			$po_number = $sales_data[0][csf("job_no")];
			//$job_style = $job_array[$job_number]['style_ref_no'];
			//$internal_ref = $job_array[$job_number]['int_ref'];
			$ship_date = "";
			$sales_buyer_id = $sales_data[0][csf("po_buyer")];
		}
		else
		{
			$po_number = $sales_data[0][csf("job_no")];
			$job_number = "";
			$buyer_id = $sales_data[0][csf("buyer_id")];
			$job_style = $sales_data[0][csf("style_ref_no")];
			$ship_date = "";
			$sales_buyer_id = $sales_data[0][csf("buyer_id")];
		}
	}
	$job_no = implode(",", array_unique(explode(",", $job_number)));
	$jobstyle = implode(",", array_unique(explode(",", $job_style)));
	$buyer = implode(",", array_unique(explode(",", $buyer_id)));
	$internal_ref = implode(",", array_unique(array_filter(explode(",", $internal_ref))));
	$file_nos = implode(",", array_unique(explode(",", $file_nos)));

	if ($job_no!="") {$job_cond="and job_no_mst in('$job_no')";}else{$job_cond="";}
	$lapdip_no=sql_select("SELECT job_no_mst,color_name_id,po_break_down_id,lapdip_no, pantone_no from wo_po_lapdip_approval_info where status_active=1 and is_deleted=0 $job_cond");
	foreach ($lapdip_no as $row)
	{
		$lapdip_no_arr[$row[csf('job_no_mst')]][$row[csf('color_name_id')]]['lapdip_no'] = $row[csf('lapdip_no')];
		$lapdip_no_arr[$row[csf('job_no_mst')]][$row[csf('color_name_id')]]['pantone_no'] = $row[csf('pantone_no')];
	}

	$chem_issue_requ=sql_select("SELECT a.REQU_PREFIX_NUM, c.RECIPE_ID from DYES_CHEM_ISSUE_REQU_MST a, DYES_CHEM_ISSUE_REQU_DTLS_CHILD c
	where a.id=c.mst_id and c.RECIPE_ID=$recipe_id and a.ENTRY_FORM=156 and a.status_active=1 and a.is_deleted=0");
	// echo $chem_issue_requ;
	foreach ($chem_issue_requ as $row) 
	{
		$recipe_requ_no_arr[$row['RECIPE_ID']] .= $row['REQU_PREFIX_NUM'].',';
	}
	$recipe_requ_no= implode(",", array_unique(explode(",", implode(",", $recipe_requ_no_arr)))) ;

	$groupingArray = sql_select("SELECT grouping from wo_booking_mst a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='$booking_no'");
	$internal_ref=$groupingArray[0][csf('grouping')];

	$path = '../';
	?>
	<div style="width:1200px; font-size:9px; margin: 0 auto;">
		<table width="1200" cellspacing="0" align="center" border="0" style="font-size: 20px; display: inline; ">
			<tr width="1200">
				<td width="20%" rowspan="2"  colspan="2" align="center" style="font-size:50px"><img align="left" src='<? echo $path.$imge_arr[$working_company]; ?>' height='60'  /></td>
				
				<td width="45%" colspan="8" align="center" style="font-size:30px;padding-left: 80px; ">
					<strong><? echo $company_library[$w_com_id]; ?></strong>
					<p style="margin-top: 0px; font-size:18px;"><i>
						<?
						$nameArray=sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$working_company");
						foreach ($nameArray as $result)
						{
						    ?>
						    <? echo $result[csf('plot_no')]; ?>
						    <? echo $result[csf('level_no')]?>
						    <? echo $result[csf('road_no')]; ?>
						    <? echo $result[csf('block_no')];?>
						    <? echo $result[csf('city')];?>
						    <? echo $result[csf('zip_code')]; ?>
						    <?php echo $result[csf('province')]; ?>
						    <? echo $country_arr[$result[csf('country_id')]];
						}
						?>
					</i></p>
				</td>
				<td width="10%"  colspan="1" align="center" style="font-size:30px"></td>
				<td width="25%" colspan="2" style="padding-left: 1px;font-size:1rem; float: center" align="right"></td>
			</tr>
			<tr width="1200">
				<td width="50%" colspan="6" align="center" style="font-size:17px; padding-left: 200px;"> <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Order Owned By: <? echo $company_library[$data[0]]; ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
				<td width="10%"  colspan="1" align="center" style="font-size:30px">&nbsp;</td>
				<td colspan="1" width="30%" id="barcode_img_id" align="right" style="font-size:24px padding-left: 10px; float: right;"></td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="font-size:30px"></td>
				<td colspan="8" align="center" style="font-size:18px; padding-left: 100px;"><strong><u>Dyeing Main Recipe</u></strong></td>
			</tr>
			<tr>
				<td colspan="9"></td>
			</tr>
		</table>
		<br><br>

		<table width="1200" cellspacing="0" align="center"  border="1" rules="all" class="rpt_table" style="font-size: 17px;">
			<tr>
				<td width="160"><strong>Buyer</strong></td>
				<td width="220px">:&nbsp;<?
					if($dataArray[0][csf('is_sales')] ==1)
					{

						echo $buyer_library[$sales_buyer_id];
					}
					else if ($dataArray[0][csf('batch_against')] == 3)
					{
						echo $buyer_library[$buyer_id_booking];
					}
					else
					{
						echo $buyer_library[$buyer];
					}
					?>
				</td>
				<td width="150"><strong>Batch No</strong></td>
				<td width="200px">:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
				<td width="150"><strong>Recipe Creation Date</strong></td>
				<td width="200px">:&nbsp;<? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
			</tr>	
			<tr>
				<td><strong>System Recipe ID</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('recipe_id')]; ?></td>
				<td><strong>Colour</strong></td>
				<td>:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
				<td><strong>Recipe Creation Time</strong></td>
				<td>:&nbsp;<? 
					$dateString = $dataArray[0][csf('insert_date')];
					// Create a DateTime object from the string
					$dateTime = DateTime::createFromFormat('d-M-y h.i.s A', $dateString);
					// Get the time part
					$timeOnly = $dateTime->format('h:i:s A');
					// Output the result
					echo $timeOnly; 
					?>
				</td>
			</tr>
			<tr>
				<td><strong>System Rquisition ID.</strong></td>
				<td>:&nbsp;<? echo chop($recipe_requ_no,","); ?></td>
				<td><strong>Batch Qty (KG)</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('qnty')]; ?></td>
				<td><strong>Recipe Creator</strong></td>
				<td>:&nbsp;<? echo $user_name_library[$dataArray[0][csf('inserted_by')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Internal No</strong></td>
				<td>:&nbsp;<? echo $internal_ref; ?></td>
				<td><strong>Pantone No</strong></td>
				<td>:&nbsp;<? echo $lapdip_no_arr[$job_no][$dataArray[0][csf('color_id')]]['pantone_no']; ?></td>
				<td><strong>Batch Against</strong></td>
				<td>:&nbsp;<? echo $batch_against[$dataArray[0][csf('batch_against')]];; ?></td>
			</tr>
			<tr>
				<td><strong>Sys. Booking No.</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('style_or_order')]; ?></td>				
				<td><strong>Labdip No</strong></td>
				<td>:&nbsp;<? echo $lapdip_no_arr[$job_no][$dataArray[0][csf('color_id')]]['lapdip_no']; ?></td>
				<td><strong>Batch Type</strong></td>
				<td>:&nbsp;<? echo $batch_type_arr[$dataArray[0][csf('batch_type_id')]]; ?></td>
			</tr>			
			<tr>
				<td><strong>FSO No</strong></td>
				<td>:&nbsp;<? echo $po_number; ?></td>
				<td><strong>Colour Range</strong></td>
				<td>:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
				<td><strong>Fabric Source</strong></td>
				<td>:&nbsp;<? echo $fabric_source[$book_fabric_source[$dataArray[0][csf('booking_no')]]]; ?></td>
			</tr>			
			<tr>
				<td><strong>Job No.</strong></td>
				<td>:&nbsp;<? echo $job_no; ?></td>
				<td width="150"><strong>Dyeing Part</strong></td>
				<td width="200px">:&nbsp;<?
					if ($dataArray[0][csf('double_dyeing')]==1)
					{
						echo "Duble Part";
					}
					if ($dataArray[0][csf('double_dyeing')]==2)
					{
						echo "Single Part";
					}
					?></td>
				<td><strong>Shipment Date</strong></td>
				<td>:&nbsp;<?
					if (trim($ship_date) != "0000-00-00" && trim($ship_date) != "") echo implode(",", array_unique(explode(",", $ship_date))); else echo "&nbsp;";
					if($min_shipment_date != "")
					{
						echo " ".change_date_format($min_shipment_date)." To ".change_date_format($max_shipment_date);
					}
					?>
				</td>
			</tr>
			<tr>
				<td><strong>Style Refernce No.</strong></td>
				<td>:&nbsp;<? echo $jobstyle; ?></td>
				<td><strong>Match With</strong></td>
				<td>:&nbsp;</td>
				<td><strong>M/C No.</strong></td>
				<td>:&nbsp;<?
					if ($db_type == 2) {
						$dyeing_machine = return_field_value("(machine_no || ': ' || prod_capacity || '(kg)') as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
					} else if ($db_type == 0) {
						$dyeing_machine = return_field_value("concat(machine_no,': ',prod_capacity,'(kg)') as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
					}
					echo $dyeing_machine;
					?>
				</td>
			</tr>
			<tr>
				<td><strong>Booking Type</strong></td>
				<td>:&nbsp;<? echo $booking_type_arr[$dataArray[0][csf('booking_no')]]; ?></td>
				<td><strong>Recipe Description</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('recipe_description')]; ?></td>
				<td><strong>Trolly No. </strong></td>
				<td>:&nbsp;<? ?></td>
			</tr>
			<tr>
				<td><strong>Remarks</strong></td>
				<td colspan="5">:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
        <br>
        <?
		$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
		$j = 1;
		$entryForm = $entry_form_arr[$batch_id_qry[0]];

		// Fabric Description
		$sql_dtls = "SELECT e.receive_basis,e.booking_id,a.booking_without_order,a.is_sales, b.batch_qnty AS batch_qnty, b.roll_no as roll_no, b.item_description,  b.prod_id, b.body_part_id, b.width_dia_type, b.remarks, b.item_size, b.batch_qty_pcs, b.color_type, d.machine_dia,d.machine_gg, d.stitch_length as stitch_length,d.febric_description_id, d.yarn_lot,d.yarn_count, d.brand_id, c.barcode_no, e.knitting_source,a.color_id, f.dia_width, f.gsm
		from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e, product_details_master f 
		where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and b.prod_id=f.id and a.company_id=$data[0] and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)";

		//echo $sql_dtls;die;
		$sql_result = sql_select($sql_dtls);
		foreach ($sql_result as $key => $row)
		{
			$str_ref=$row[csf('width_dia_type')]."*".$row[csf('dia_width')]."*".$row[csf('gsm')]."*".$row[csf('color_type')];

			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['receive_basis']=$row[csf('receive_basis')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['booking_without_order']=$row[csf('booking_without_order')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['is_sales']=$row[csf('is_sales')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['batch_qnty']+=$row[csf('batch_qnty')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['batch_qty_pcs']+=$row[csf('batch_qty_pcs')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['roll_no']+=$row[csf('roll_no')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['item_description']=$row[csf('item_description')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['remarks'].=$row[csf('remarks')].',';
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['febric_description_id']=$row[csf('febric_description_id')];
			//$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['gsm']=$row[csf('gsm')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['knitting_source']=$row[csf('knitting_source')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['color_id']=$row[csf('color_id')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['gsm']=$row[csf('gsm')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['yarn_lot'].=$row[csf('yarn_lot')].',';
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['brand_id'].=$row[csf('brand_id')].',';
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['num_of_rows']++;

			// $barcode_data_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];

			if ($row[csf('item_size')]!='')
			{
				$item_size_arr[$row[csf('body_part_id')]][$row[csf('item_size')]]+=$row[csf('batch_qty_pcs')];
			}
	        $descr_id_arr[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
		}
		// echo "<pre>";print_r($data_arr);

		if (!empty($descr_id_arr))
		{
			$lib_fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where status_active=1", "id", "fabric_composition_name");

			$determination_sql = sql_select("select a.id, a.construction, b.copmposition_id,b.type_id, b.percent, a.fabric_composition_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ".where_con_using_array($descr_id_arr,1,'a.id'));
			$f_comp_arr=array();
			foreach ($determination_sql as $d_row) {
				// $comp = $lib_fabric_composition[$d_row[csf("fabric_composition_id")]];
				$f_comp_arr[$d_row[csf("id")]]=$lib_fabric_composition[$d_row[csf("fabric_composition_id")]];
			}
		}
		// echo "<pre>";print_r($f_comp_arr);
		?>
		<div style="float:left; font-size:18px;"><strong><u>Fabric Description</u></strong></div>
		<table align="center" cellspacing="0" width="1200" border="1" rules="all" class="rpt_table" style="border-top:none;font-size: 18px;">
			<thead bgcolor="#dddddd" align="center" style="font-size: 18px;">
				<tr>
					<th width="30">SL</th>
					<th width="80">Body part</th>
					<th width="50">Color Type</th>
					<th width="200">Const. X Comp.</th>
					<th width="60">D/W Type</th>
					<th width="50">Fin. Dia</th>
					<th width="50">Fin. GSM</th>
					<th width="80">Brand</th>
					<th width="80">Yarn Lot</th>
					<th width="70">Grey Qty.</th>
					<th width="50">Roll No.</th>
					<th width="80">TTL WT</th>
					<th width="40">PL%</th>
					<th width="50">GM/Line.M</th>
					<th>Remarks</th>
				</tr>
			</thead>
			<?
			$row_count = 0;$tot_batch_qty=0;
			foreach ($data_arr as $body_part_id => $body_partIdv)
			{
				foreach ($body_partIdv as $prod_id => $prod_idv)
				{
					foreach ($prod_idv as $strRef => $row)
					{
						$row_count++;
						$tot_batch_qty += $row['batch_qnty'];
					}
				}
			}
			// echo $row_count.'=='.$tot_batch_qty;

			$i = 1;
			foreach ($data_arr as $body_part_id => $body_partIdv)
			{
				$sub_total_roll_number=0; $sub_total_batch_qty=0;$sub_total_finish_qty=0;
				foreach ($body_partIdv as $prod_id => $prod_idv)
				{
					foreach ($prod_idv as $strRef => $row)
					{
						$dataStr = explode("*", $strRef);
						$width_dia_type=$dataStr[0];
						$dia=$dataStr[1];
						$gsm=$dataStr[2];
						$color_type_id=$dataStr[3];

						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$desc = explode(",", $row['item_description']);
						$remarks = implode(",", array_filter(array_unique(explode(",", $row['remarks'])))) ;

						$yarn_lot = implode(",", array_unique(explode(",", chop($row['yarn_lot'],","))));
						$brand_id_arr = array_unique(explode(",", chop($row['brand_id'],",")));

						$brand_value = "";
						foreach ($brand_id_arr as $bid)
						{
							if ($bid > 0) {
								if ($brand_value == '') $brand_value = $brand_name_arr[$bid]; else $brand_value .= ", " . $brand_name_arr[$bid];
							}
						}

						$is_sales=$row['is_sales'];
						if($is_sales==1) //Sales
						{
							$process_loss=$sales_process_loss_array[$sales_order_no][$body_part_id][$color_type_id][$gsm][$dia][$width_dia_type]['process_loss'];
						}
						else
						{
							if($row['booking_without_order']==1)
							{
								//$color_type_id=$color_type_array[$booking_no][$body_part_id]['color_type_id'];
								$process_loss=$process_loss_array[$booking_no][$body_part_id]['color_type_id'];
							}
							else
							{
								$color_id=$dataArray[0][csf('color_id')];
								//$color_type_id=$color_type_array_precost[$booking_no][$body_part_id][$row['febric_description_id']][$row['gsm']]['color_type_id'];
								$process_loss= $process_loss_array[$booking_no][$body_part_id][$row['febric_description_id']][$gsm][$row['color_id']];
							}
						}

						?>
						<tr>
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="80" title="<? echo $body_part_id; ?>" style="word-break:break-all;"><? echo $body_part[$body_part_id]; ?></td>
							<td width="50" style="word-break:break-all;"><? echo $color_type[$color_type_id]; ?></td>
							<td width="150" style="word-break:break-all;" title="<?=$row["febric_description_id"];?>"><? echo $desc[0] . "," . $f_comp_arr[$row["febric_description_id"]]; ?></td>
							<td width="60" style="word-break:break-all;" title="<?=$width_dia_type;?>"><? 
							if ($fabric_typee[$width_dia_type]=='Open Width') 
							{
								echo "Open";
							}
							else
							{
								echo $fabric_typee[$width_dia_type];
							}
							?></td>
							<td width="50" align="center" style="word-break:break-all;"><? echo $dia; ?></td>
							<td width="50" align="center" style="word-break:break-all;"><? echo $gsm; ?></td>
							<td width="80" style="word-break:break-all;"><? echo $brand_value; ?></td>
							<td width="80" style="word-break:break-all;"><? echo $yarn_lot;
							//echo implode(',', array_unique(explode(",", $yarn_lot))); ?></td>
							<?
							if(number_format($batch_weight, 2) == number_format($rbatch_weight,2))
							{
								?>
								<td width="70" align="right" style="word-break:break-all;" ><? echo number_format($row['batch_qnty'], 2);?></td>
								<?
							}
							else
							{
							?>
							<td width="70" align="center" style="word-break:break-all;" valign="bottom"><? echo "...............";?></td>
								<?
							}?>
							<td align="center" width="50" style="word-break:break-all;"><? echo $row['num_of_rows']; ?></td>
							<?
							if ($test==0) 
							{
								?>
								<td width="80" rowspan="<?=$row_count;?>" title="<? echo $row_count; ?>" style="word-break:break-all;" align="right"><b>
									<? 
									//echo number_format($ttl_wt=$tot_batch_qty+$dataArray[0][csf('total_trims_weight')], 2); 
									echo number_format($ttl_wt=$rbatch_weight, 2); 
									?>
								</b></td>
								<?
								$test++;
							}
							?>
							

							<td width="40" align="right"><? if ($process_loss>0) 
							{
								echo $process_loss;
							}
							else{
								echo '0';
							} ?></td>
							<td width="50" align="right">
								<? 
								if ($width_dia_type==1) 
								{
									echo $gMLineM=number_format($dia*$gsm/39.37,2,'.','');
								}
								else
								{
									echo '0';
								} ?>
							</td>
							<td><? echo $remarks;?></td>
						</tr>
						<?php
						$total_roll_number += $row['num_of_rows'];
						$total_batch_qty += $row['batch_qnty'];
						$total_ttl_wt += $ttl_wt;

						$sub_total_roll_number += $row['num_of_rows'];
						$sub_total_batch_qty += $row['batch_qnty'];
						$sub_total_ttl_wt += $ttl_wt;
						$i++;
					}
				}
			}
			if($save_string_data!=""){
			?>
			<?php
				$save_string_data_arr= explode('!!',$save_string_data);
				foreach($save_string_data_arr as $key=>$row){
					$col=explode('_',$row);
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="left"><? echo $key+1; ?></td>
						<td align="left"><? echo $col[2];?></td>
						<td colspan="7" align="center"><? echo $col[0];?></td>
						<td align="right"><? echo $col[1]; ?></td>
						<td colspan="5" align="center" style="border:none;">&nbsp;<? ?></td>

					</tr>
					<?
				}
			}
			?>
	    </table>
        <br> <br>

        <div style="width:100%;">
        	<table  cellspacing="0" width="1200" border="1" rules="all" class="rpt_table" style="font-size:20px;">
        		<thead bgcolor="#dddddd" align="center">
					<tr>
						<th rowspan="2" width="30" height="40" >SL</th>
						<th rowspan="2" width="150" height="40">Group Name</th>
						<th rowspan="2" width="200" height="40">Product Name</th>
						<th rowspan="2" width="80" height="40">Product Lot</th>						
						<th rowspan="2" width="50" height="40" title="Ratio">Conc.</th>
						<th rowspan="2" width="50" height="40">Dose Base</th>
						<th colspan="3" width="150">Weight (KG)</th>
						<th rowspan="2" width="50" height="40">Rate (TK)</th>
						<th rowspan="2" width="80" height="40">TTL Cost (TK)</th>
						<th rowspan="2" width="" height="40">Process</th>
					</tr>
					<tr>
						<th>KG</th>
						<th>GM</th>
						<th>MG</th>
                	</tr>
        		</thead>
        		<?
        		$i = 1;
        		$j = 1;
        		$mst_id = $data[1];
        		$com_id = $data[0];

        		$process_array = array();
        		$sub_process_data_array = array();
        		$sub_process_remark_array = array();
				/* 	$sql_ratio = "SELECT id, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio from pro_recipe_entry_dtls where mst_id=$mst_id and ratio is not null and status_active=1 and is_deleted=0 order by sub_seq"; */
				$sql_ratio = "SELECT b.id, b.sub_process_id as sub_process_id, b.process_remark, b.total_liquor, b.liquor_ratio, a.batch_qty,a.batch_ratio from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id and b.mst_id=$mst_id and b.ratio is not null and b.status_active=1 and b.is_deleted=0 order by b.sub_seq";
        		//echo $sql_ratio;
        		$nameArray = sql_select($sql_ratio);
        		foreach ($nameArray as $row) {
        			if (!in_array($row[csf("sub_process_id")], $process_array)) {
        				$process_array[] = $row[csf("sub_process_id")];
        				$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
        			}
        			//$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("batch_qty")]*$row[csf("batch_ratio")]*$row[csf("liquor_ratio")];
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
        		}

        		if ($db_type == 2)
        		{
					$sql = "(SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no, c.batch_qty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0  )
					union
					(
					SELECT null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no, null as batch_qty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
					)  order by seq_no,sub_process_id";
        		}
        		else if ($db_type == 0)
        		{
					$sql = "(SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio, b.seq_no as seq_no, c.batch_qty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 )
					union
					(
						SELECT null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio,b.seq_no as seq_no, null as batch_qty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
					)  ";
        		}
				//echo $sql;
        		$sql_result = sql_select($sql);
				$prodIdChk = array();
				$prodIdArr = array();
				$process_count = array();
	            foreach ($sql_result as $row)
	            {
	            	$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")]. "**" . $row[csf("current_stock")] . "**" . $row[csf("prod_id")] . "**" . $row[csf("batch_qty")] . "$$$";

					if($prodIdChk[$row[csf('prod_id')]] == "")
					{
						$prodIdChk[$row[csf('prod_id')]] = $row[csf('prod_id')];
						array_push($prodIdArr,$row[csf('prod_id')]);
					}
					$process_count[$row[csf("sub_process_id")]]++;

					/*$key_1 = $row[csf("batch_id")] . $row[csf("order_id")] . $row[csf("gsm")] . $row[csf("fabric_description_id")];
					$gData_1[$key_1] += 1;*/
	            }
	            // echo '<pre>';print_r($process_count);
				// var_dump($process_count);
				$sql_prod_info = "SELECT id, avg_rate_per_unit from product_details_master where status_active=1 and is_deleted=0 ".where_con_using_array($prodIdArr,0,'id')."";
				//echo $sql_prod_info;
        		$prodInfoArray = sql_select($sql_prod_info);
				$prod_data_array = array();
        		foreach ($prodInfoArray as $row)
				{
        			$prod_data_array[$row[csf("id")]]['avg_rate_per_unit'] = $row[csf("avg_rate_per_unit")];
        		}
				//var_dump($prod_data_array);
        		$avg_rage_sql="SELECT a.prod_id,
				sum(case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end) as rcv_qty,
				sum(case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end) as iss_qty,
				sum(case when a.transaction_type in (1,4,5) then a.CONS_AMOUNT else 0 end) as rcv_amount,
				sum(case when a.transaction_type in (2,3,6) then a.CONS_AMOUNT else 0 end) as iss_amount,
				round(sum(case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end)-sum(case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end),2) as bal_qty,
				round(sum(case when a.transaction_type in (1,4,5) then a.CONS_AMOUNT else 0 end)-sum(case when a.transaction_type in (2,3,6) then a.CONS_AMOUNT else 0 end),2) as bal_amount
				from inv_transaction a
				where a.item_category in(7,5,6,23) and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($prodIdArr,0,'prod_id')." group by a.prod_id order by a.prod_id";

				/*$avg_rage_sql=" SELECT a.prod_id,a.cons_quantity, a.store_rate as cons_rate, a.store_amount as cons_amount from inv_transaction a where a.status_active=1 and a.is_deleted=0 and a.item_category in(7,5,6,23) and a.company_id=$data[0] and a.transaction_type in (1,4,5) and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($prodIdArr,0,'prod_id')."";*/
				// echo $avg_rage_sql;// and a.prod_id in (77301)
				$avg_rage_sql_result = sql_select($avg_rage_sql);
				foreach ($avg_rage_sql_result as $row) 
				{
					$avg_rate_arr[$row[csf("prod_id")]]['cons_amount'] += $row[csf("bal_amount")];
					$avg_rate_arr[$row[csf("prod_id")]]['cons_quantity'] += $row[csf("bal_qty")];
				}
				// echo '<pre>';print_r($avg_rate_arr);

				$grand_tot_ratio = 0;
				$grand_tot_recipe_qty = 0;
				$grand_tot_avg_rate = 0;$tot_process_count=0;
	            foreach ($process_array as $process_id)
	            {
	            	$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];

	            	$liquor_ratio_process = $sub_process_remark_array[$process_id]['liquor_ratio'];
	            	$remark = $sub_process_remark_array[$process_id]['remark'];
	            	// if ($remark == '') $remark = ''; else $remark .= $remark . ', ';
	            	$tot_ratio=1.5;
	            	$leveling_water=$batch_weight*($liquor_ratio_process-$tot_ratio) ;

	            	?>
	            	<tr bgcolor="#EEEFF0" style="font-size: 22px;">
	            		<td align="center" colspan="12" align="left" height="30" title="<?=$process_id;?>"><b><? echo $dyeing_sub_process[$process_id] .'  (Liquor Ratio: 1:'.number_format($liquor_ratio_process,1,'.','').'), '. 'Water: '.number_format($liquor_ratio,2,'.',''); ?> LTR</b>
	            		</td>
	            	</tr>
	            	<?
	            	$tot_ratio = 0;$tot_recipe_qty = 0;
	            	//$sub_process_data = array_filter(explode("@@@",$sub_process_data_array2[$process_id]));
					//$sub_process_data = explode("$$$", substr($sub_process_data_array[$process_id], 0, -1));
					$sub_process_dataArr=rtrim($sub_process_data_array[$process_id],'$$$');
					$sub_process_data = explode("$$$", $sub_process_dataArr);
					//echo count($sub_process_data).'='.$process_id.'<br>';

	            	foreach ($sub_process_data as $data) 
	            	{
	            		$data = explode("**", $data);
	            		$current_stock = $data[13];
	            		$current_stock_check=number_format($current_stock,7,'.','');
	            		//if($process_id==93 || $process_id==94 || $process_id==95 || $process_id==96 || $process_id==97 || $process_id==98 || $process_id == 140 || $process_id == 141 || $process_id == 142 || $process_id == 143)
						if (in_array($process_id, $subprocessForWashArr))
						{
							$id = $data[0];
                			$item_category_id = $data[1];
                			$item_group_id = $data[2];
                			$sub_group_name = $data[3];
                			$item_description = $data[4];
                			$item_size = $data[5];
                			$unit_of_measure = $data[6];
                			$dtls_id = $data[7];
                			$sub_process_id = $data[8];
                			$item_lot = $data[9];
                			$dose_base_id = $data[10];
                			$ratio = $data[11];
                			$comments = $data[12];
                			$prod_id = $data[14];
							$batch_qty = $data[15];

							//echo $dose_base_id.'*'.$ratio.'*'.$batch_weight.'*'.$liquor_ratio_process.'<br>';
							if($dose_base_id==1){
								$amount = number_format($ratio*$batch_weight*$liquor_ratio_process/1000,6, '.', '');
								$amount = explode('.',$amount);
							}
							else{
								$amount = number_format($ratio*$batch_weight/100,6, '.', '');
								$amount = explode('.',$amount);
							}

							/*if ($dose_base_id == 1) {
								$recipe_qty = number_format(($liquor_ratio/1000)*$ratio, 4, '.', '');
							} else {
								$recipe_qty = number_format(($batch_qty*$ratio)/100, 4, '.', '');
							}*/
                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                			if ($dose_base[$dose_base_id]=='% on BW') {$bgcolor2 = "#E2EFDA";} else {$bgcolor2 = "#FFFFFF";}
	                		?>
	                		<tr >
                				<td bgcolor="<? echo $bgcolor2 ?>" height="30"><? echo $i; ?></td>
                				<td bgcolor="<? echo $bgcolor2 ?>" height="30" align="center"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
                				<td bgcolor="<? echo $bgcolor2 ?>" height="30" align="center" style="font-size: 18px;"><p><? echo $item_description; ?></p></td>
                				<td bgcolor="<? echo $bgcolor2 ?>" height="30" align="center"><p><? echo $item_lot; ?></p></td>
                				<td bgcolor="<? echo $bgcolor2 ?>" height="30" align="right"><strong><? echo $ratio; ?></strong>&nbsp;</td>                				
                				<td bgcolor="<? echo $bgcolor2 ?>" height="30"><p><? 
	                				if ($dose_base[$dose_base_id]=='% on BW') 
	                				{
	                					echo '%';
	                				}
	                				elseif ($dose_base[$dose_base_id]=='GPLL') 
	                				{
	                					echo 'GPL';
	                				}
	                				else
	                				{
	                					echo $dose_base[$dose_base_id];
	                				} ?></p>
	                			</td>
								<td bgcolor="<? echo $bgcolor2 ?>" width="40" align="right"><strong><? echo $amount[0]; ?></strong>&nbsp;</td>
								<td bgcolor="<? echo $bgcolor2 ?>" width="40" align="right"><strong><? echo number_format(substr($amount[1], 0, 3)); ?></strong>&nbsp;</td>
								<td bgcolor="<? echo $bgcolor2 ?>" width="40" align="right"><strong><? echo number_format(substr($amount[1], 3, 6)); ?></strong>&nbsp;</td>
								<td bgcolor="<? echo $bgcolor2 ?>" height="30" align="right"><?=number_format($avg_rate,2);?>&nbsp;</td>
	                			<td bgcolor="<? echo $bgcolor2 ?>" height="30" align="right" title="<?=$kg_gm_mg_amount;?>"><?=number_format($kg_gm_mg_amount*$avg_rate,2);?>&nbsp;</td>
                				<?
								if(!in_array($sub_process_id,$sub_process_chk))
								{
									$sub_process_chk[]=$sub_process_id;
									?>	
									<td width="150" rowspan="<? echo $tot_process_count ;?>" valign="middle"><strong><? echo $remark; ?></strong></td>
									<?
								}
								?>
                			</tr>
                			<?
                			$tot_ratio += $ratio;
                			$grand_tot_ratio += $ratio;
							$grand_tot_avg_rate +=$prod_data_array[$prod_id]['avg_rate_per_unit']*$recipe_qty;
							$grand_tot_kg += $amount[0];
							$grand_tot_gm += substr($amount[1], 0, 3);
							$grand_tot_mgm += substr($amount[1], 3, 6);
							$tot_kg += $amount[0];
							$tot_gm += substr($amount[1], 0, 3);
							$tot_mgm += substr($amount[1], 3, 6);
                			$i++;
						}
						else
						{
	                		//if($current_stock_check>0)
	                		//{
	                			$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];
								$batch_qty = $data[15];
								$tot_process_count=$process_count[$sub_process_id];

								$cons_amount=$avg_rate_arr[$prod_id]['cons_amount'];
								$cons_quantity=$avg_rate_arr[$prod_id]['cons_quantity'];
								if ($cons_amount>0) {
									$avg_rate=$cons_amount/$cons_quantity;
								}
								else {
									$avg_rate=0;
								}
								// echo $avg_rate.'<br>';

								//echo $dose_base_id.'='.$ratio.'='.$batch_weight.'='.$liquor_ratio_process.'<br>';
								if($dose_base_id==1){
									$amount = number_format($ratio*$rbatch_weight*$liquor_ratio_process/1000,6, '.', '');
									//echo $amount.'<br>';
									$kg_gm_mg_amount=$amount;
									$amount = explode('.',$amount);
								}
								else{
									$amount = number_format($ratio*$rbatch_weight/100,6, '.', '');
									//echo $amount.'<br>';
									$kg_gm_mg_amount=$amount;
									$amount = explode('.',$amount);
								}

								/*if ($dose_base_id == 1) {

									$recipe_qty = number_format(($liquor_ratio/1000)*$ratio, 4, '.', '');
								} else {
									$recipe_qty = number_format(($batch_qty*$ratio)/100, 4, '.', '');
								}*/
	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								if ($dose_base[$dose_base_id]=='% on BW') {$bgcolor2 = "#E2EFDA";} else {$bgcolor2 = "#FFFFFF";}
	                			?>
	                			<tr >
	                				<td bgcolor="<? echo $bgcolor2 ?>" height="30" ><?echo $i; ?></td>
	                				<td bgcolor="<? echo $bgcolor2 ?>" height="30" align="center"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
	                				<td bgcolor="<? echo $bgcolor2 ?>" height="30" align="center" title="<?=$prod_id;?>" style="font-size: 18px;"><p><strong><? echo $item_description; ?></strong></p></td>
	                				<td bgcolor="<? echo $bgcolor2 ?>" height="30" align="center"><p><? echo $item_lot; ?></p></td>
	                				<td bgcolor="<? echo $bgcolor2 ?>" height="30" align="right"><strong><? echo $ratio; ?></strong>&nbsp;</td>
	                				<td bgcolor="<? echo $bgcolor2 ?>" height="30" title="<?=$dose_base_id;?>"><p><? 
		                				if ($dose_base[$dose_base_id]=='% on BW') 
		                				{
		                					echo '%';
		                				}
		                				elseif ($dose_base[$dose_base_id]=='GPLL') 
		                				{
		                					echo 'GPL';
		                				}
		                				else
		                				{
		                					echo $dose_base[$dose_base_id];
		                				}
		                				?></p>
		                			</td>
									<td bgcolor="<? echo $bgcolor2 ?>" width="40" align="right"><strong><? echo $amount[0]; ?></strong>&nbsp;</td>
	                				<td bgcolor="<? echo $bgcolor2 ?>" width="40" align="right"><strong><? echo number_format(substr($amount[1], 0, 3)); ?></strong>&nbsp;</td>
	                				<td bgcolor="<? echo $bgcolor2 ?>" width="40" align="right"><strong><? echo number_format(substr($amount[1], 3, 6)); ?></strong>&nbsp;</td>
	                				<td bgcolor="<? echo $bgcolor2 ?>" height="30" align="right" title="avg_rate=balance amount/balance qnty. <? echo $cons_amount.'/'.$cons_quantity.'='.$avg_rate; ?>"><?=number_format($avg_rate,2);?>&nbsp;</td>
	                				<td bgcolor="<? echo $bgcolor2 ?>"  height="30" align="right" title="<?=$kg_gm_mg_amount;?>"><?=number_format($ttl_cost=$kg_gm_mg_amount*$avg_rate,2);
	                				if ($dose_base_id==1) 
	                				{
	                					$chem_cost+=$ttl_cost;
	                				}
	                				else
	                				{
	                					$dyes_cost+=$ttl_cost;
	                				}
	                				?>&nbsp;</td>

	                				<?
									if(!in_array($sub_process_id,$sub_process_chk))
									{
										$sub_process_chk[]=$sub_process_id;
										?>	
										<td width="150" rowspan="<? echo $tot_process_count ;?>" valign="middle"><strong><? echo $remark; ?></strong></td>
										<?
									}
									?>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
								$grand_tot_avg_rate +=$prod_data_array[$prod_id]['avg_rate_per_unit']*$recipe_qty;
								$grand_tot_kg += $amount[0];
								$grand_tot_gm += substr($amount[1], 0, 3);
								$grand_tot_mgm += substr($amount[1], 3, 6);
								$tot_kg += $amount[0];
								$tot_gm += substr($amount[1], 0, 3);
								$tot_mgm += substr($amount[1], 3, 6);
	                			$i++;
	                		//}
	            		}
	            	}

	            }
				if($tot_mgm>=1000){
					$tot_gm += intdiv($tot_mgm, 1000);
					$tot_mgm = $tot_mgm%1000;
				}

				if($tot_gm>=1000){
					$tot_kg += intdiv($tot_gm, 1000);
					$tot_gm = $tot_gm%1000;
				}
	            ?>
				<tr class="tbl_bottom">
					<td align="center" colspan="4" rowspan="2"><strong>Total Cost</strong></td>
	            	<td align="right"  height="20" colspan="2"><strong>Chem Cost</strong></td>
	            	<td align="center" height="20" colspan="3"><strong><? echo number_format($chem_cost, 2, '.', ''); ?></strong>&nbsp;</td>
					<td align="center" colspan="2" rowspan="2"><strong>Total Cost (Chem.+Dyes)</strong></td>
                    <td align="center" rowspan="2"><strong><? echo number_format($chem_cost+$dyes_cost, 2, '.', ''); ?></strong></td>
	            </tr>
				<tr class="tbl_bottom">
	            	<td align="right"  height="20" colspan="2"><strong>Dyes Cost</strong></td>
	            	<td align="center" height="20" colspan="3"><strong><? echo number_format($dyes_cost, 2, '.', ''); ?></strong>&nbsp;</td>
	            </tr>
    		</table>
	        <br>
	        <?
			$path = '../';
			$image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$recipe_id' and form_name='recipe_entry' and is_deleted=0");
			if (count($image_location) > 0) 
			{
				?>
				<div style="width:850px">
					<div style="width:850px;margin-top:10px">
						<img style="padding-left: 190px;" src="<? echo $path . $image_location; ?>"/>
					</div>
				</div>
				<? 
			}
			else 
			{
				$image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$copy_from_recipe_id' and form_name='recipe_entry' and is_deleted=0");
				if (count($image_location) > 0) 
				{
					?>
					<div style="width:850px">
						<div style="width:850px;margin-top:10px">
							<img style="padding-left: 190px;" src="<? echo $path . $image_location; ?>"/>
						</div>
					</div>
					<? 
				}
			}

	        echo signature_table(62, $com_id, "1030px","","50px");
	        ?>
		</div>
	</div>
	<?
}

if ($action == "recipe_entry_print_10") // for NZ Group
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$recipe_id=$data[1];
	$working_company=$data[4];
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');
	$user_name_library = return_library_array("select id, user_name from user_passwd", "id", "user_name");
	$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
	$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');

	$sql = "SELECT c.id as recipe_id, c.labdip_no, c.company_id,c.working_company_id as w_com_id,c.location_id, recipe_description, c.batch_id, c.machine_id, c.method, c.recipe_date, c.order_source, c.style_or_order, c.booking_id, c.total_liquor, c.batch_ratio, c.liquor_ratio, c.remarks, c.buyer_id, c.color_id, c.color_range, c.remarks as recipe_remarks, c.insert_date, c.inserted_by, c.batch_qty as qnty, c.cycle_time, c.pump, c.surplus_solution, c.sub_tank,c.pickup, a.id, a.batch_no, a.batch_date, a.booking_no_id, a.booking_no, a.booking_without_order, a.sales_order_no, a.batch_type_id, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks as batch_remarks, a.collar_qty, a.cuff_qty, a.shift_id, a.double_dyeing, a.sales_order_id,a.is_sales, a.booking_entry_form , a.save_string
	from pro_recipe_entry_mst c, pro_batch_create_mst a
	where c.batch_id=a.id and c.id=$data[1] and a.is_sales=1 and a.status_active=1 and a.is_deleted=0";
	// echo $sql;die;
	$dataArray = sql_select($sql);
	$w_com_id=$dataArray[0][csf('w_com_id')];
	$company_id=$dataArray[0][csf('company_id')];
	$batch_id=$dataArray[0][csf('batch_id')];
	$location_id=$dataArray[0][csf('location_id')];
	$booking_no=$dataArray[0][csf('style_or_order')];
	$sales_order_id=$dataArray[0][csf('sales_order_id')];
	$color_id=$dataArray[0][csf('color_id')];
	$save_string_data = $dataArray[0][csf('save_string')];
	$sales_order_no = $dataArray[0][csf('sales_order_no')];
	$batch_weight = $dataArray[0][csf('batch_weight')];
	// $batch_weight = $dataArray[0][csf('batch_qnty')];

	if ($dataArray[0][csf('is_sales')] == 1)
	{
		$sales_data = sql_select("SELECT id,job_no,sales_booking_no,within_group,buyer_id,style_ref_no, po_buyer from fabric_sales_order_mst where id=$sales_order_id");

		if ($sales_data[0][csf("within_group")] == 1)
		{
			$booking_data = sql_select("SELECT a.booking_no,a.buyer_id, a.fabric_source, a.booking_type, a.is_short, b.job_no, c.pub_shipment_date, c.grouping, d.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d 
			where a.booking_no=b.booking_no and b.po_break_down_id = c.id and c.job_id=d.id and  a.booking_no='$booking_no' and b.fabric_color_id='$color_id' and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 
			group by a.booking_no,a.buyer_id, a.fabric_source, a.booking_type, a.is_short, b.job_no, c.pub_shipment_date, c.grouping, d.style_ref_no");

			foreach ($booking_data as $row)
			{
				$job_number .= $row[csf('job_no')].",";
				$job_style .= $row[csf('style_ref_no')].",";
				$internal_ref .= $row[csf('grouping')].",";
				$book_fabric_source[$row[csf('booking_no')]]=$row[csf('fabric_source')];
				// $booking_type_id[$row[csf('booking_no')]]=$row[csf('booking_type')];

				if($row[csf("booking_type")]==1 && $row[csf("is_short")]==2)
	            {
	                $booking_type_arr[$row[csf("booking_no")]]="Main";
	            }
	            else if($row[csf("booking_type")]==1 && $row[csf("is_short")]==1)
	            {
	                $booking_type_arr[$row[csf("booking_no")]]="Short";
	            }
	            else if($row[csf("booking_type")]==4)
	            {
	                $booking_type_arr[$row[csf("booking_no")]]="Sample";
	            }

	            if($min_shipment_date == ""){
					$min_shipment_date = $row[csf('pub_shipment_date')];
				}

				if($max_shipment_date == ""){
					$max_shipment_date = $row[csf('pub_shipment_date')];
				}

				if(strtotime($row[csf('pub_shipment_date')]) > strtotime($min_shipment_date))
				{
					$min_shipment_date = $min_shipment_date;
				}else{
					$min_shipment_date = $row[csf('pub_shipment_date')];
				}


				if(strtotime($row[csf('pub_shipment_date')]) > strtotime($max_shipment_date))
				{
					$max_shipment_date = $row[csf('pub_shipment_date')];
				}
				else
				{
					$max_shipment_date = $max_shipment_date;
				}
			}
			$job_number = chop($job_number,",");
			$job_style = chop($job_style,",");
			$internal_ref = chop($internal_ref,",");
			$buyer_id = $booking_data[0][csf("buyer_id")];
			$po_number = $sales_data[0][csf("job_no")];
			$ship_date = "";
			$sales_buyer_id = $sales_data[0][csf("po_buyer")];
			$style_ref_no = $sales_data[0][csf("style_ref_no")];
		}
		else
		{
			$po_number = $sales_data[0][csf("job_no")];
			$job_number = "";
			$buyer_id = $sales_data[0][csf("buyer_id")];
			$job_style = $sales_data[0][csf("style_ref_no")];
			$ship_date = "";
			$sales_buyer_id = $sales_data[0][csf("buyer_id")];
		}
	}
	$job_no = implode(",", array_unique(explode(",", $job_number)));
	$jobstyle = implode(",", array_unique(explode(",", $job_style)));
	$buyer = implode(",", array_unique(explode(",", $buyer_id)));
	$internal_ref = implode(",", array_unique(array_filter(explode(",", $internal_ref))));
	$file_nos = implode(",", array_unique(explode(",", $file_nos)));

	$sql_recipe_dtl = "SELECT mst_id, liquor_ratio,total_liquor from pro_recipe_entry_dtls where mst_id='$data[1]' and status_active=1 and liquor_ratio>0  and  RATIO>0";
	//echo $sql_recipe_dtl;
	$ratio_dataArray = sql_select($sql_recipe_dtl);
	$liquor_ratio=$ratio_dataArray[0][csf('liquor_ratio')];
	//$total_liquor=$ratio_dataArray[0][csf('total_liquor')];
	$tot_liquor=array();
	foreach ($ratio_dataArray as $row) 
	{
		$tot_liquor[$row[csf('total_liquor')]]=$row[csf('total_liquor')];
	}
	$total_liquor = max($tot_liquor);

	/*$total_liquor = 0;
	foreach ($tot_liquor as $key=>$val) {
	    if ($val > $total_liquor) {
	        $total_liquor = $val;
	    }
	}
	echo $total_liquor;*/

	$path = '../';
	?>
	<div style="width:930px; font-size:9px">
		<table width="930" cellspacing="0" align="center" border="0" style="font-size: 20px; display: inline; ">
			<tr width="930">
				<td width="20%" rowspan="2"  colspan="2" align="center" style="font-size:50px"><img align="left" src='<? echo $path.$imge_arr[$working_company]; ?>' height='60'  /></td>
				
				<td width="45%" colspan="8" align="center" style="font-size:30px;padding-left: 80px; ">
					<strong><? echo $company_library[$w_com_id]; ?></strong>
					<p style="margin-top: 0px; font-size:18px;"><i>
						<?
						$nameArray=sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$working_company");
						foreach ($nameArray as $result)
						{
						    ?>
						    <? echo $result[csf('plot_no')]; ?>
						    <? echo $result[csf('level_no')]?>
						    <? echo $result[csf('road_no')]; ?>
						    <? echo $result[csf('block_no')];?>
						    <? echo $result[csf('city')];?>
						    <? echo $result[csf('zip_code')]; ?>
						    <?php echo $result[csf('province')]; ?>
						    <? echo $country_arr[$result[csf('country_id')]];
						}
						?>
					</i></p>
				</td>
				<td width="10%"  colspan="1" align="center" style="font-size:30px"></td>
				<td width="25%" colspan="2" style="padding-left: 1px;font-size:1rem; float: center" align="right"></td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="font-size:18px; padding-left: 140px;""><strong><u>Dyes & Chemical Requisition</u></strong></td>
				<td colspan="8" align="center" style="font-size:18px; padding-left: 100px;"></td>
			</tr>
			<tr>
				<td colspan="9"></td>
			</tr>
		</table>
		<br><br>

		<table width="930" cellspacing="0" align="center"  border="1" rules="all" class="rpt_table" style="font-size: 17px;">
			<tr>
				<td width="160"><strong>Shade Type</strong></td>
				<td width="220px">:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
				<td width="150"><strong>Serial No.</strong></td>
				<td width="200px">:&nbsp;<? echo $dataArray[0][csf('recipe_id')]; ?></td>
				<td width="150"><strong>LD</strong></td>
				<td width="200px">:&nbsp;<? echo $dataArray[0][csf('labdip_no')]; ?></td>
			</tr>	
			<tr>
				<td><strong>Recipe Date</strong></td>
				<td>:&nbsp;<? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
				<td><strong>Buyer</strong></td>
				<td>:&nbsp;<?
					if($dataArray[0][csf('is_sales')] ==1)
					{

						echo $buyer_library[$sales_buyer_id];
					}
					else if ($dataArray[0][csf('batch_against')] == 3)
					{
						echo $buyer_library[$buyer_id_booking];
					}
					else
					{
						echo $buyer_library[$buyer];
					}
					?>
				</td>
				<td><strong>MC NO</strong></td>
				<td>:&nbsp;<? echo $machine_arr[$dataArray[0][csf('machine_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Order</strong></td>
				<td>:&nbsp;<? echo $style_ref_no; ?></td>
				<td><strong>Batch No</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
				<td><strong>Color</strong></td>
				<td>:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Reel SP</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('surplus_solution')]; ?></td>				
				<td><strong>P/Press</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('pump')]; ?></td>
				<td><strong>Cycle Time</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('cycle_time')]; ?></td>
			</tr>			
			<tr>
				<td><strong>Total QTY (Kg)</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?></td>
				<td><strong>Fabric Weight</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('batch_weight')]-$dataArray[0][csf('total_trims_weight')]; ?></td>
				<td><strong>Trims Weight</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('total_trims_weight')]; ?></td>
			</tr>			
			<tr>
				<td><strong>Water</strong></td>
				<td>:&nbsp;<? echo number_format($total_liquor,2,'.',''); ?> LT</td>
				<td><strong>Sub Tank</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('sub_tank')]; ?> LT</td>
			</tr>
		</table>
        <br>
        <?
		$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
		$j = 1;
		$entryForm = $entry_form_arr[$batch_id_qry[0]];
		?>

		<!-- Fabrication -->
        <table width="930" cellspacing="0" align="right" class="rpt_table" border="1" style="font-size:16px; margin-bottom:5px;" rules="all">
			<thead bgcolor="#dddddd" align="center">
				<tr bgcolor="#CCCCFF">
					<th colspan="6" align="center"><strong>Fabrication</strong></th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="80">Dia/ W. Type</th>
					<th width="200">Constrution & Composition</th>
					<th width="70">Gsm</th>
					<th width="70">Dia</th>
                    <th width="70">Yarn Lot</th>
				</tr>
			</thead>
			<tbody>
				<?
				 $sql_dtls_knit = "SELECT a.booking_no_id,e.receive_basis,e.booking_id,a.booking_without_order,a.is_sales, b.batch_qnty AS batch_qnty, b.roll_no as roll_no, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, b.width_dia_type as num_of_rows, d.machine_no_id,d.machine_dia,d.machine_gg, d.yarn_lot,d.id as dtls_id,d.yarn_count,d.brand_id,c.barcode_no, d.stitch_length as stitch_length,  e.knitting_source, e.knitting_company,c.qc_pass_qnty_pcs ,c.coller_cuff_size
				from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d, inv_receive_master e
				where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id='$data[0]' and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
				order by b.program_no";
				$sql_result_knit = sql_select($sql_dtls_knit);
				foreach ($sql_result_knit as $row)
				{
					$knittin_data_arr2[$row[csf('item_description')]]["yarn_lot"]= $row[csf('yarn_lot')];
				}

				foreach ($batch_id_qry as $b_id)
				{
					$batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
					$result_batch_query = sql_select($batch_query);
					foreach ($result_batch_query as $rows)
					{
						if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$yarn_lot=$knittin_data_arr2[$rows[csf('item_description')]]["yarn_lot"];

						$fabrication_full = $rows[csf("item_description")];
						$fabrication = explode(',', $fabrication_full);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $j; ?></td>
							<td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>
							<td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
							<td align="center"><? echo $fabrication[2]; ?></td>
							<td align="center"><? echo $fabrication[3]; ?></td>
                            <td align="center"><? echo $yarn_lot; ?></td>
						</tr>
						<?
						$j++;
					}
				}
				?>
			</tbody>
		</table>
        <br> <br> <br>
        <!-- Fabrication End -->

        <div style="width:100%;">
        	<table  cellspacing="0" width="930" border="1" rules="all" class="rpt_table" style="font-size:20px;">
        		<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30" height="40" >SL</th>
						<th width="200" height="40" title="Item Description">Chemical Name</th>
						<th width="80" height="40" title="Ratio">g/l</th>
						<th width="50" height="40" title="Ratio">%</th>
						<th width="50" height="40">+</th>
						<th width="50">Total qty (gm)</th>
						<th width="" height="40">Comments</th>
					</tr>
        		</thead>
        		<?
        		$i = 1;
        		$j = 1;
        		$mst_id = $data[1];
        		$com_id = $data[0];

        		$process_array = array();
        		$sub_process_data_array = array();
        		$sub_process_remark_array = array();
        		$sql_ratio = "SELECT id, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio from pro_recipe_entry_dtls where mst_id=$mst_id and ratio is not null and status_active=1 and is_deleted=0 order by sub_seq";
        		// echo $sql_ratio;
        		$nameArray = sql_select($sql_ratio);
        		foreach ($nameArray as $row) 
        		{
        			if (!in_array($row[csf("sub_process_id")], $process_array)) 
        			{
        				$process_array[] = $row[csf("sub_process_id")];
        				$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
        			}
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
        		}

        		if ($db_type == 2)
        		{
					$sql = "(SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no, c.batch_qty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0  )
					union
					(
					SELECT null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no, null as batch_qty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
					)  order by seq_no,sub_process_id";
        		}
        		else if ($db_type == 0)
        		{
					$sql = "(SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio, b.seq_no as seq_no, c.batch_qty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 )
					union
					(
						SELECT null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio,b.seq_no as seq_no, null as batch_qty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
					)  ";
        		}
				//echo $sql;
        		$sql_result = sql_select($sql);
				$prodIdChk = array();
				$prodIdArr = array();
				$process_count = array();
	            foreach ($sql_result as $row)
	            {
	            	$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")]. "**" . $row[csf("current_stock")] . "**" . $row[csf("prod_id")] . "**" . $row[csf("batch_qty")] . "$$$";

					if($prodIdChk[$row[csf('prod_id')]] == "")
					{
						$prodIdChk[$row[csf('prod_id')]] = $row[csf('prod_id')];
						array_push($prodIdArr,$row[csf('prod_id')]);
					}
					$process_count[$row[csf("sub_process_id")]]++;

					/*$key_1 = $row[csf("batch_id")] . $row[csf("order_id")] . $row[csf("gsm")] . $row[csf("fabric_description_id")];
					$gData_1[$key_1] += 1;*/
	            }

				$grand_tot_ratio = 0;
				$grand_tot_recipe_qty = 0;
				$grand_tot_avg_rate = 0;$tot_process_count=0;$gpll_ratio=0;$bw_ratio=0;
	            foreach ($process_array as $process_id)
	            {
	            	$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];

	            	$liquor_ratio_process = $sub_process_remark_array[$process_id]['liquor_ratio'];
	            	$remark = $sub_process_remark_array[$process_id]['remark'];
	            	// if ($remark == '') $remark = ''; else $remark .= $remark . ', ';
	            	$tot_ratio=1.5;
	            	$leveling_water=$batch_weight*($liquor_ratio_process-$tot_ratio) ;

	            	?>
	            	<tr bgcolor="#EEEFF0" style="font-size: 22px;">
	            		<td align="center" colspan="9" align="left" height="30" title="<?=$process_id;?>"><b><? echo $dyeing_sub_process[$process_id] .'  (Liquor Ratio:'.number_format($liquor_ratio_process,1,'.','').'), '. 'Water: '.number_format($liquor_ratio,2,'.',''); ?> LTR</b>
	            		</td>
	            	</tr>
	            	<?
	            	$tot_ratio = 0;$tot_recipe_qty = 0;
	            	//$sub_process_data = array_filter(explode("@@@",$sub_process_data_array2[$process_id]));
					//$sub_process_data = explode("$$$", substr($sub_process_data_array[$process_id], 0, -1));
					$sub_process_dataArr=rtrim($sub_process_data_array[$process_id],'$$$');
					$sub_process_data = explode("$$$", $sub_process_dataArr);
					//echo count($sub_process_data).'='.$process_id.'<br>';

	            	foreach ($sub_process_data as $data) 
	            	{
	            		$data = explode("**", $data);
	            		$current_stock = $data[13];
	            		$current_stock_check=number_format($current_stock,7,'.','');
	            		//if($process_id==93 || $process_id==94 || $process_id==95 || $process_id==96 || $process_id==97 || $process_id==98 || $process_id == 140 || $process_id == 141 || $process_id == 142 || $process_id == 143)
						if (in_array($process_id, $subprocessForWashArr))
						{
							$id = $data[0];
                			$item_category_id = $data[1];
                			$item_group_id = $data[2];
                			$sub_group_name = $data[3];
                			$item_description = $data[4];
                			$item_size = $data[5];
                			$unit_of_measure = $data[6];
                			$dtls_id = $data[7];
                			$sub_process_id = $data[8];
                			$item_lot = $data[9];
                			$dose_base_id = $data[10];
                			$ratio = $data[11];
                			$comments = $data[12];
                			$prod_id = $data[14];
							$batch_qty = $data[15];

							//echo $dose_base_id.'*'.$ratio.'*'.$batch_weight.'*'.$liquor_ratio_process.'<br>';
							if($dose_base_id==1){
								$amount = number_format($ratio*$batch_weight*$liquor_ratio_process/1000,3, '.', '');
								$amount = explode('.',$amount);
							}
							else{
								$amount = number_format($ratio*$batch_weight/100,3, '.', '');
								$amount = explode('.',$amount);
							}

							if ($dose_base_id == 1) {
								$recipe_qty = number_format(($liquor_ratio/1000)*$ratio, 4, '.', '');
							} else {
								$recipe_qty = number_format(($batch_qty*$ratio)/100, 4, '.', '');
							}
							
                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                			?>
                			<tr>
                				<td height="30"><? echo $i; ?></td>
                				<td height="30" align="center" style="font-size: 18px;"><p><? echo $item_description; ?></p></td>
                				<td height="30" align="right" title="<?=$dose_base_id;?>"><p><? echo ($dose_base_id == 1) ? number_format($ratio, 3, '.', '') : '0.000' ?></p>
		                			</td>
		                		<td height="30" align="right"><strong><? echo ($dose_base_id == 2) ? number_format($ratio, 3, '.', '') : '0.000' ?></strong>&nbsp;</td>
		                		<td height="30"></td>
								<td width="50" align="right"><strong><? echo number_format($recipe_qty, 3, '.', ''); //$amount; ?></strong>&nbsp;</td>
                				<?
								if(!in_array($sub_process_id,$sub_process_chk))
								{
									$sub_process_chk[]=$sub_process_id;
									?>	
									<td width="150" rowspan="<? echo $tot_process_count ;?>" valign="middle"><strong><? echo $remark; ?></strong></td>
									<?
								}
								?>
                			</tr>
                			<?
                			$tot_ratio += $ratio;
                			$grand_tot_ratio += $ratio;
							$grand_tot_kg += $amount;
                			$i++;
						}
						else
						{
	                		//if($current_stock_check>0)
	                		//{
	                			$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];
								$batch_qty = $data[15];
								$tot_process_count=$process_count[$sub_process_id];

								$cons_amount=$avg_rate_arr[$prod_id]['cons_amount'];
								$cons_quantity=$avg_rate_arr[$prod_id]['cons_quantity'];
								if ($cons_amount>0) {
									$avg_rate=$cons_amount/$cons_quantity;
								}
								else {
									$avg_rate=0;
								}
								// echo $avg_rate.'<br>';

								//echo $dose_base_id.'='.$ratio.'='.$batch_weight.'='.$liquor_ratio_process.'<br>';
								if($dose_base_id==1)
								{
									$amount = number_format($ratio*$batch_weight*$liquor_ratio_process/1000,3, '.', '');
									// $amount = explode('.',$amount);
								}
								else{
									$amount = number_format($ratio*$batch_weight/100,3, '.', '');
									// $amount = explode('.',$amount);
								}

								if ($dose_base_id == 1) {
									$recipe_qty = number_format(($liquor_ratio/1000)*$ratio, 4, '.', '');
								} else {
									$recipe_qty = number_format(($batch_qty*$ratio)/100, 4, '.', '');
								}

	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
	                			<tr>
	                				<td height="30"><?echo $i; ?></td>
	                				<td height="30" align="center" title="<?=$prod_id;?>" style="font-size: 18px;"><p><strong><? echo $item_description; ?></strong></p></td>
	                				<td height="30" title="<?=$dose_base_id;?>"><strong><?
	                				echo ($dose_base_id == 1) ? number_format($ratio, 3, '.', '') : '0.000' ?></strong></td>
		                			<td height="30" align="right" title="<?=$dose_base_id;?>" align="right"><strong><? 
									echo ($dose_base_id == 2) ? number_format($ratio, 3, '.', '') : '0.000'; ?></strong>&nbsp;</td>
		                			<td height="30"></td>
									<td width="50" align="right"><strong><? echo number_format($recipe_qty, 3, '.', ''); //$amount; ?></strong>&nbsp;</td>
	                				<?
									if(!in_array($sub_process_id,$sub_process_chk))
									{
										$sub_process_chk[]=$sub_process_id;
										?>	
										<td width="150" rowspan="<? echo $tot_process_count ;?>" valign="middle"><strong><? echo $remark; ?></strong></td>
										<?
									}
									?>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
								$grand_tot_kg += $amount;
	                			$i++;
	                		//}
	            		}
	            	}

	            }
				if($tot_mgm>=1000){
					$tot_gm += intdiv($tot_mgm, 1000);
					$tot_mgm = $tot_mgm%1000;
				}

				if($tot_gm>=1000){
					$tot_kg += intdiv($tot_gm, 1000);
					$tot_gm = $tot_gm%1000;
				}
	            ?>
				<tr class="tbl_bottom">
	            	<td></td>
	            	<td align="right" height="20"><strong>ALL OVER</strong></td>
	            	<td align="right" height="20"><strong><? echo number_format($dataArray[0][csf('pickup')], 2, '.', ''); ?>%</strong>&nbsp;</td>
	            	<td></td>
	            	<td></td>
	            	<td></td>
	            	<td></td>
	            </tr>
				<tr class="tbl_bottom">
	            	<td></td>
	            	<td align="right" height="20"><strong>SALT=</strong></td>
	            	<td align="right" height="20"><strong><? echo number_format($salt=$dataArray[0][csf('batch_weight')]*0.4, 2, '.', ''); ?></strong>&nbsp;</td>
	            	<td align="right" height="20"><strong><?=number_format($salt/$dataArray[0][csf('sub_tank')]*100, 2, '.', '');?>%</strong></td>
	            	<td align="right" height="20"><strong>Levelling Water</strong>&nbsp;</td>
	            	<td align="right" height="20"><strong><?
	            	$levelling_water=$total_liquor-($dataArray[0][csf('batch_weight')]*0.4+$dataArray[0][csf('batch_weight')]*0.4+$dataArray[0][csf('batch_weight')]*0.4+$dataArray[0][csf('batch_weight')]*0.4);
	            	echo number_format($levelling_water, 2, '.', '');?></strong></td>
	            	<td><strong>LT</strong></td>
	            </tr>
	            <tr class="tbl_bottom">
	            	<td></td>
	            	<td align="right"  height="20"><strong>COLOUR=</strong></td>
	            	<td align="right" height="20"><strong><? echo number_format($colour=$dataArray[0][csf('batch_weight')]*0.4, 2, '.', ''); ?></strong>&nbsp;</td>
	            	<td align="right" height="20"><strong><?=number_format($colour/$dataArray[0][csf('sub_tank')]*100, 2, '.', '');?>%</strong></td>
	            	<td></td>
	            	<td></td>
	            	<td></td>
	            </tr>
	            <tr class="tbl_bottom">
	            	<td></td>
	            	<td align="right"  height="20"><strong>SODA ASH=</strong></td>
	            	<td align="right" height="20"><strong><? echo number_format($soda_ash=$dataArray[0][csf('batch_weight')]*0.4, 2, '.', ''); ?></strong>&nbsp;</td>
	            	<td align="right" height="20"><strong><?=number_format($soda_ash/$dataArray[0][csf('sub_tank')]*100, 2, '.', '');?>%</strong></td>
	            	<td></td>
	            	<td></td>
	            	<td></td>
	            </tr>
	            <tr class="tbl_bottom">
	            	<td></td>
	            	<td align="right"  height="20"><strong>SODA ASH=</strong></td>
	            	<td align="right" height="20"><strong><? echo number_format($soda_ash2=$dataArray[0][csf('batch_weight')]*0.4, 2, '.', ''); ?></strong>&nbsp;</td>
	            	<td align="right" height="20"><strong><?=number_format($soda_ash2/$dataArray[0][csf('sub_tank')]*100, 2, '.', '');?>%</strong></td>
	            	<td></td>
	            	<td></td>
	            	<td></td>
	            </tr>
    		</table>
	        <br>
	        <?
	        echo signature_table(62, $com_id, "1030px","","50px");
	        ?>
		</div>
	</div>
	<?
}

if ($action == "recipe_entry_print_11")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	//$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");

	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');

	 $sql_recipe_mst = "select id, labdip_no, company_id,working_company_id as w_com_id,copy_from,surplus_solution,pump,pickup,cycle_time,location_id, recipe_description, batch_id,machine_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, remarks from pro_recipe_entry_mst where id='$data[1]' and status_active=1";
	$dataArray = sql_select($sql_recipe_mst);
	$w_com_id=$dataArray[0][csf('w_com_id')];
	$company_id=$dataArray[0][csf('company_id')];
	$pump=$dataArray[0][csf('pump')];$cycle_time=$dataArray[0][csf('cycle_time')];
	$batch_id=$dataArray[0][csf('batch_id')];
	$mst_id=$data[1];
	$order_array = return_library_array("select id, order_no from subcon_ord_dtls b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id' ", "id", "order_no");



	//	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
   	$sql_po="select b.id,b.po_number from wo_po_break_down b,wo_booking_dtls a,pro_batch_create_mst c where c.booking_no=a.booking_no and a.po_break_down_id=b.id and b.status_active=1 and c.id=$batch_id";
	$result_sql_po = sql_select($sql_po);
	foreach ($result_sql_po as $row)
	{
		$po_arr[$row[csf("po_number")]] = $row[csf("po_number")];
	}


	$batch_array = array();
	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no,a.batch_against, a.entry_form, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty,b.is_sales from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
	} else if ($db_type == 2) {
		$sql = "select a.id, a.batch_no,a.batch_against,a.entry_form, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,  sum(b.batch_qnty) as batch_qnty,b.is_sales  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.id=$batch_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.batch_against, a.batch_no,a.entry_form,a.total_trims_weight,b.is_sales order by a.id DESC";
	}
	// echo $sql;
	$result_sql = sql_select($sql);
	foreach ($result_sql as $row) {
		$order_no = '';
		$order_id = array_unique(explode(",", $row[csf("po_id")]));
		if ($row[csf("entry_form")] == 36) {
			$batch_type = "<b> SUBCONTRACT ORDER </b>";
			foreach ($order_id as $val) {
				if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];
			}
		} else {
			$batch_type = "<b> SELF ORDER </b>";
			foreach ($order_id as $val) {
				if($row[csf("is_sales")] == 1){
					$order_no =implode(", ",$po_arr);// $sales_arr[$val]["sales_order_no"];
				}
				else
				{
					if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
				}
			}
		}
		$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
		$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
		$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
		$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")];
		$batch_array[$row[csf("id")]]['order'] = $order_no;
		$batch_array[$row[csf("id")]]['batch_type'] = $batch_type;
	}




	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$w_com_id");
	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	$group_id=$nameArray[0][csf('group_id')];
	$sales_order_result = sql_select("select b.id,b.job_no,b.within_group,b.sales_booking_no,b.style_ref_no from fabric_sales_order_mst b,pro_batch_create_mst c where b.id=c.sales_order_id and c.status_active=1 and c.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.id='$batch_id' and c.company_id='$data[0]'");


	$sales_arr = array();
	foreach ($sales_order_result as $sales_row) {
		$sales_arr[$sales_row[csf("id")]]["sales_order_no"] 	= $sales_row[csf("job_no")];
		$sales_order_no	= $sales_row[csf("job_no")];
		$style_ref	= $sales_row[csf("style_ref_no")];
	}
	 $sql_recipe_dtl = "select mst_id, liquor_ratio,total_liquor from pro_recipe_entry_dtls where mst_id='$data[1]' and status_active=1 and liquor_ratio>0  and  RATIO>0";
	$ratio_dataArray = sql_select($sql_recipe_dtl);
	$liquor_ratio=$ratio_dataArray[0][csf('liquor_ratio')];
	$total_liquor=$ratio_dataArray[0][csf('total_liquor')];
	// echo $liquor_ratio.'DDDDDD';


	$total_batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];
	?>
	<div style="width:930px; font-size:6px">
		<table width="930" cellspacing="0" align="right" border="1"  class="rpt_table" border="1"  rules="all">
			<tr>
				<td colspan="6" align="center" style="font-size:x-large">
					<strong><? echo $company_library[$w_com_id]; ?>
				</strong></td>
			</tr>

			<tr>
				<td colspan="6" align="center" style="font-size:20px"><u><strong><? echo 'Dyeing Recipe'; ?></strong></u></td>
			</tr>
			<tr>
				<td width="130"><strong>System ID</strong></td>
				<td width="175"><? echo $dataArray[0][csf('id')]; ?></td>
				<td width="130"><strong>Labdip No </strong></td>
				<td width="175px"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
				<td><strong>Trims Weight</strong></td>
            	<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight']; ?></td>
			</tr>
			<tr>
				<td><strong>Batch No</strong></td>
				<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_no']; ?></td>
				<td><strong>Recipe Date</strong></td>
				<td> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
				<td><strong>Reel Speed</strong></td>
				<td><? echo $dataArray[0][csf('surplus_solution')];//$order_source[$dataArray[0][csf('order_source')]] ?></td>
			</tr>

			<tr>
				<td><strong>Buyer Name</strong></td>
				<td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
				<td><strong>Booking</strong></td>
				<td><? echo $dataArray[0][csf('style_or_order')]; ?></td>
				<td><strong>Pump Pressure</strong></td>
				<td> <? echo $pump;//$color_arr[$dataArray[0][csf('color_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Color Range</strong></td>
				  <td><? echo $color_range[$dataArray[0][csf('color_range')]];; ?></td>
            	<td><strong>Copy Recipe</strong></td>
            	<td><?=$dataArray[0][csf('copy_from')];//,pump,cycle_time//$batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];echo $batch_weight; ?></td>
                <td><strong>JT/Chamber</strong></td>
            	<td><?=$dataArray[0][csf('pickup')].' %';//$batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];echo $batch_weight; ?></td>

            </tr>
            <tr>
				<td><strong>Color</strong></td>
				 <td><? echo $color_arr[$dataArray[0][csf('color_id')]];; ?></td>
            	<td><strong>Order Source</strong></td>
            	<td><? echo $order_source[$dataArray[0][csf('order_source')]];//,pump,cycle_time//$batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];echo $batch_weight; ?></td>
                <td><strong>Cycle Time</strong></td>
            	<td><?=$cycle_time;//$batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];echo $batch_weight; ?></td>

            </tr>
            <tr>
            	<td><strong>Sales Order No.</strong></td>
            	<td><? echo $sales_order_no; ?></td>
        		<td><strong>Batch Weight</strong></td>
        		<td><?
				   $batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];
				   $trims_weight=$batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight'];
				   $total_batch_weight= $batch_weight+$trims_weight;
				   echo $total_batch_weight;
				   ?></td>
                <td><strong>Liquor Ratio</strong></td>
        		<td><? echo number_format($liquor_ratio,2); ?></td>
            </tr>
            <tr>
            	<td><strong>Style Ref</strong></td>
        		<td><? echo $style_ref; ?></td>
                 <td><strong>Machine no</strong></td>
        		<td><? echo $machine_arr[$dataArray[0][csf('machine_id')]]; ?></td>
        		<td><strong>Water</strong></td>
        		<td><? echo $total_liquor; ?></td>

            </tr>
        	<tr>
				<td><strong>Remarks</strong></td>
            	<td colspan="5"><?  echo $dataArray[0][csf('remarks')]; ?></td>
        	</tr>
        </table>
        <br>
        <?
		$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
		$j = 1;
		$entryForm = $entry_form_arr[$batch_id_qry[0]];
		?>
		<!-- Fabrication -->
        <table width="930" cellspacing="0" align="right" class="rpt_table" border="1" style="font-size:16px; margin:5px;" rules="all">
			<thead bgcolor="#dddddd" align="center">

					<tr bgcolor="#CCCCFF">
						<th colspan="6" align="center"><strong>Fabrication</strong></th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="80">Dia/ W. Type</th>
						<th width="200">Constrution & Composition</th>
						<th width="70">Gsm</th>
						<th width="70">Dia</th>
                        <th width="70">Yarn Lot</th>
					</tr>
			</thead>
			<tbody>
				<?
				 $sql_dtls_knit = "select a.booking_no_id,e.receive_basis,e.booking_id,a.booking_without_order,a.is_sales, b.batch_qnty AS batch_qnty, b.roll_no as roll_no, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, b.width_dia_type as num_of_rows, d.machine_no_id,d.machine_dia,d.machine_gg, d.yarn_lot,d.id as dtls_id,d.yarn_count,d.brand_id,c.barcode_no, d.stitch_length as stitch_length,  e.knitting_source, e.knitting_company,c.qc_pass_qnty_pcs ,c.coller_cuff_size
				from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d, inv_receive_master e
				where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id='$data[0]' and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
				order by b.program_no";
				$sql_result_knit = sql_select($sql_dtls_knit);
				foreach ($sql_result_knit as $row)
				{


					$knittin_data_arr2[$row[csf('item_description')]]["yarn_lot"]= $row[csf('yarn_lot')];

				}

				foreach ($batch_id_qry as $b_id)
				{
					$batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
					$result_batch_query = sql_select($batch_query);
					foreach ($result_batch_query as $rows)
					{
						if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$yarn_lot=$knittin_data_arr2[$rows[csf('item_description')]]["yarn_lot"];

						$fabrication_full = $rows[csf("item_description")];
						$fabrication = explode(',', $fabrication_full);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $j; ?></td>
							<td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>
							<td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
							<td align="center"><? echo $fabrication[2]; ?></td>
							<td align="center"><? echo $fabrication[3]; ?></td>
                            <td align="center"><? echo $yarn_lot; ?></td>
						</tr>
						<?
						$j++;
					}
				}
				?>
			</tbody>
		</table>
        <br> <br> <br>
        <div style="width:100%;">
        	<table align="right" style="margin:5px;" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
        		<thead bgcolor="#dddddd" align="center">
        			<tr>
        				<th rowspan="2" width="30">SL</th>
            			<th rowspan="2" width="220">Function Name</th>
            			<th rowspan="2" width="200">Item Desc</th>
            			<th rowspan="2" width="80">Item Lot</th>
            			<th rowspan="2" width="50">Dosage Option</th>
            			<th rowspan="2" width="100">Dosage</th>
            			<th colspan="3" width="200">Amount</th>
						<th >Comments</th>

        			</tr>
        			<tr>
        				<th>KG</th>
        				<th>Gram</th>
        				<th>Miligram</th>
        			</tr>
        		</thead>
        		<?
        		$i = 1;
        		$j = 1;
        		$mst_id = $data[1];
        		$com_id = $data[0];

        		$process_array = array();
        		$sub_process_data_array = array();
        		$sub_process_remark_array = array();
        		$sql_ratio = "select id, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by sub_seq";
        		$nameArray = sql_select($sql_ratio);
        		foreach ($nameArray as $row) 
        		{
        			if (!in_array($row[csf("sub_process_id")], $process_array)) {
        				$process_array[] = $row[csf("sub_process_id")];
        				$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
        			}
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
        		}

				$sql = "(SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$company_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0  )
				union
				(
				SELECT null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
				)  order by seq_no,sub_process_id";

				// echo $sql;
                $sql_result = sql_select($sql);

                foreach ($sql_result as $row) 
                {
                	$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")]. "**" . $row[csf("current_stock")] . "**" . $row[csf("prod_id")] . "$$$";

                }
				//var_dump($sub_process_data_array);
                foreach ($process_array as $process_id) 
                {
                	$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];
                	$l_ratio = $sub_process_remark_array[$process_id]['liquor_ratio'];

                	$liquor_ratio_process = $sub_process_remark_array[$process_id]['liquor_ratio'];
                	$remark = $sub_process_remark_array[$process_id]['remark'];
                	//if ($remark == '') $remark = ''; else $remark .= $remark . ', ';
                	$tot_ratio=1.5;
                	$leveling_water=$batch_weight*($liquor_ratio_process-$tot_ratio) ;


                	?>
                	<tr bgcolor="#EEEFF0">
                		<td colspan="9" align="left"><b>Sub Process
                			Name:- <? echo $dyeing_sub_process[$process_id] . ', ' . $remark. ', Total Liquor(ltr):' . $liquor_ratio; ?></b>
                		</td>
                	</tr>
                	<?
                	$tot_ratio = 0;
                	$tot_kg = 0;
                	$tot_gm = 0;
                	$tot_mgm = 0;
                	//$sub_process_data = array_filter(explode("@@@",$sub_process_data_array2[$process_id]));
					//$sub_process_data = explode("$$$", substr($sub_process_data_array[$process_id], 0, -1));
					$sub_process_dataArr=rtrim($sub_process_data_array[$process_id],'$$$');
					$sub_process_data = explode("$$$", $sub_process_dataArr);
					//echo count($sub_process_data).'='.$process_id.'<br>';
                	foreach ($sub_process_data as $data) 
                	{
                		$data = explode("**", $data);
                		$current_stock = $data[13];
                		$current_stock_check=number_format($current_stock,7,'.','');

						if(in_array($process_id, $subprocessForWashArr))
						{
							$id = $data[0];
                			$item_category_id = $data[1];
                			$item_group_id = $data[2];
                			$sub_group_name = $data[3];
                			$item_description = $data[4];
                			$item_size = $data[5];
                			$unit_of_measure = $data[6];
                			$dtls_id = $data[7];
                			$sub_process_id = $data[8];
                			$item_lot = $data[9];
                			$dose_base_id = $data[10];
                			$ratio = $data[11];
                			$comments = $data[12];
                			$prod_id = $data[14];

                			if($dose_base_id==1 && $item_category_id==5)
                			{
                				$amount = number_format($ratio*$total_batch_weight*$l_ratio/1000,6, '.', '');
                				$amount = explode('.',$amount);
                			}
                			else
                			{
                				$amount = number_format($ratio*$total_batch_weight/100,6, '.', '');
                				$amount = explode('.',$amount);
                			}

                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                			?>
							<? if($item_category_id==6){?>
                			<tr bgcolor="<? echo $bgcolor; ?>">
                				<td><b><? echo $i; ?></b></td>
								<td><p><b><? echo $sub_group_name; ?></b></p></td>
                				<td><p><b><? echo $item_description; ?></b></p></td>
                				<td><p><b><? echo $item_lot; ?></b></p></td>

								<td><p><b><? echo $dose_base[$dose_base_id]; ?></b></p></td>
                				<td align="right"><b><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</b></td>
                				<td align="right"><b><? echo $amount[0]; ?>&nbsp;</b></td>
                				<td align="right"><b><? echo number_format(substr($amount[1], 0, 3)); ?>&nbsp;</b></td>
                				<td align="right"><b><? echo number_format(substr($amount[1], 3, 6)); ?>&nbsp;</b></td>
                			</tr>
							<?}else{?>
							<tr bgcolor="<? echo $bgcolor; ?>">
                				<td><? echo $i; ?></td>
								<td><p><? echo $sub_group_name; ?></p></td>
                				<td><p><? echo $item_description; ?></p></td>
                				<td><p><? echo $item_lot; ?></p></td>

								<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
                				<td align="right"><? echo $amount[0]; ?>&nbsp;</td>
                				<td align="right"><? echo number_format(substr($amount[1], 0, 3)); ?>&nbsp;</td>
                				<td align="right"><? echo number_format(substr($amount[1], 3, 6)); ?>&nbsp;</td>
								<td><?echo $comments;?></td>
                			</tr>
							<?}?>
                			<?
                			$tot_ratio += $ratio;
                			$grand_tot_ratio += $ratio;
                			$grand_tot_kg += $amount[0];
                			$grand_tot_gm += substr($amount[1], 0, 3);
                			$grand_tot_mgm += substr($amount[1], 3, 6);
                			$tot_kg += $amount[0];
		                	$tot_gm += substr($amount[1], 0, 3);
		                	$tot_mgm += substr($amount[1], 0, 3);
                			$i++;
						}
						else
						{
	                		if($current_stock_check>0)
	                		{
	                			$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
								//	echo $sub_group_name.'==DD';
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];

	                			if($dose_base_id==1)
	                			{
	                				$amount = number_format($ratio*$total_batch_weight*$l_ratio/1000,6, '.', '');
	                				$amount = explode('.',$amount);
	                			}
	                			else
	                			{
	                				$amount = number_format($ratio*$total_batch_weight/100,6, '.', '');
	                				$amount = explode('.',$amount);
	                			}

	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
								<? if($item_category_id==6){?>
	                			<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td><? echo $i; ?></td>

	                				<td><p><b><? echo $sub_group_name; ?></b></p></td>
	                				<td><p><b><? echo $item_description; ?></b></p></td>
	                				<td><p><b><? echo $item_lot; ?></b></p></td>

	                				<td><p><b><? echo $dose_base[$dose_base_id]; ?></b></p></td>
	                				<td align="right"><b><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</b></td>
	                				<td align="right"><b><? echo $amount[0]; ?>&nbsp;</b></td>
	                				<td align="right"><b><? echo number_format(substr($amount[1], 0, 3)); ?>&nbsp;</b></td>
	                				<td align="right"><b><? echo number_format(substr($amount[1], 3, 6)); ?>&nbsp;</b></td>

	                			</tr>
								<? }else{?>
								<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td><? echo $i; ?></td>

	                				<td><p><? echo $sub_group_name; ?></p></td>
	                				<td><p><? echo $item_description; ?></p></td>
	                				<td><p><? echo $item_lot; ?></p></td>

	                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
	                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
	                				<td align="right"><? echo $amount[0]; ?>&nbsp;</td>
	                				<td align="right"><? echo number_format(substr($amount[1], 0, 3)); ?>&nbsp;</td>
	                				<td align="right"><? echo number_format(substr($amount[1], 3, 6)); ?>&nbsp;</td>
									<td><?echo $comments;?></td>
	                			</tr>
								<?}?>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
	                			$grand_tot_kg += $amount[0];
	                			$grand_tot_gm += substr($amount[1], 0, 3);
	                			$grand_tot_mgm += substr($amount[1], 3, 6);
	                			$tot_kg += $amount[0];
			                	$tot_gm += substr($amount[1], 0, 3);
			                	$tot_mgm += substr($amount[1], 3, 6);
	                			$i++;
	                		}
                		}
                	}

                	if($tot_mgm>=1000){
                		$tot_gm += intdiv($tot_mgm, 1000);
                		$tot_mgm = $tot_mgm%1000;
                	}

                	if($tot_gm>=1000){
                		$tot_kg += intdiv($tot_gm, 1000);
                		$tot_gm = $tot_gm%1000;
                	}
                	?>
                	<tr class="tbl_bottom">
                		<td align="right" colspan="5"><strong>Sub Process Total</strong></td>
                		<td align="right"><? echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo $tot_kg; ?>&nbsp;</td>
                        <td align="right"><? echo $tot_gm; ?>&nbsp;</td>
                        <td align="right"><? echo $tot_mgm ?>&nbsp;</td>
                        <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                	</tr>
                	<?
                }

            	if($grand_tot_mgm>=1000)
            	{
            		$grand_tot_gm += intdiv($grand_tot_mgm, 1000);
            		$grand_tot_mgm = $grand_tot_mgm%1000;
            	}

            	if($grand_tot_gm>=1000)
            	{
            		$grand_tot_kg += intdiv($grand_tot_gm, 1000);
            		$grand_tot_gm = $grand_tot_gm%1000;
            	}
                ?>

                <tr class="tbl_bottom">
                	<td align="right" colspan="5"><strong>Grand Total</strong></td>
                	<td align="right"><? echo number_format($grand_tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                    <td align="right"><? echo $grand_tot_kg; ?>&nbsp;</td>
                    <td align="right"><? echo $grand_tot_gm; ?>&nbsp;</td>
                    <td align="right"><? echo $grand_tot_mgm; ?>&nbsp;</td>
                    <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                </tr>
            </table>
            <br>
            <table width="930" cellspacing="0" align="right" class="rpt_table" border="1" style="font-size:16px; margin:5px;" rules="all">
	            <tr>
		            <td>Whiteness  </td>
		            <td width="100">&nbsp;   </td>
		            <td>Neutral pH  </td>
		            <td width="100">&nbsp;   </td>
		            <td>Alkali pH  </td>
		            <td width="100">&nbsp;   </td>
		            </tr>
		            <tr>
		            <td>Absorbency  </td>
		            <td width="100">&nbsp;   </td>
		            <td>Leveling  </td>
		            <td width="100">&nbsp;   </td>
		            <td>Soaping pH  </td>
		            <td width="100">&nbsp;   </td>
		            </tr>
		            <tr>
		            <td>Residual Peroxide  </td>
		            <td width="100">&nbsp;   </td>
		            <td>Specific Gravity   </td>
		            <td width="100">&nbsp;   </td>
		            <td>Final pH </td>
		            <td width="100">&nbsp;   </td>
	            </tr>
            </table>
            <?
            echo signature_table(62, $com_id, "1030px");
            ?>
        </div>
	</div>
	<?
}

?>

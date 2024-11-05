<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../../../includes/common.php');

	$user_name=$_SESSION['logic_erp']['user_id'];
	$data=$_REQUEST['data'];
	$permission=$_SESSION['page_permission'];
	$action=$_REQUEST['action'];


	if($action=="batch_popup")
	{
		echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
		extract($_REQUEST);
		?>
		<script type="text/javascript">
			function js_set_value(id)
			{
				var item_id = id.split("_");
				document.getElementById('selected_batch_id').value = item_id[0];
				document.getElementById('selected_batch_no').value = item_id[1];
				parent.emailwindow.hide();
			}
		</script>

	</head>
	<body>
		<div align="center">
			<fieldset style="width:1000px;margin-left:4px;">
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
						<thead>
							<tr>
								<th>Search By</th>
								<th>Search</th>
								<th>Batch Create Date Range</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
									<input type="hidden" id="selected_batch_id" name="selected_batch_id" />
									<input type="hidden" id="selected_batch_no" name="selected_batch_no" />
								</th>
							</tr>
						</thead>
						<tr class="general">
							<td align="center">
								<?
								$search_by_arr=array(1=>"Batch No",2=>"Booking No");
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td align="center">
								<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_batch_search_list_view', 'search_div', 'grey_fabric_barcode_sticker_export_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	exit();
}

if($action=="create_batch_search_list_view")
{
	$data=explode('_',$data);
	$search_by 			= $data[1];
	$company_name 		= $data[2];
	$start_date =$data[3];
	$end_date =$data[4];

	if($search_by==1)
		$search_field='batch_no';
	else
		$search_field='booking_no';

	$search_condition 	= ($data[0] != "") ? " and $search_field like '%".trim($data[0])."%'" : "";
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.batch_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}else{
			$date_cond="and a.insert_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}else{
		$date_cond="";
	}

	$po_name_arr=array();


	if($db_type==2) $group_concat="  listagg(cast(b.po_number AS VARCHAR2(4000)),',') within group (order by b.id) as order_no" ;
	else if($db_type==0) $group_concat=" group_concat(b.po_number) as order_no" ;

	$sql_po=sql_select("select a.mst_id,$group_concat from pro_batch_create_dtls a, wo_po_break_down b where a.po_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.mst_id");
	$po_name_arr=array();
	foreach($sql_po as $p_name)
	{
		$po_name_arr[$p_name[csf('mst_id')]]=implode(",",array_unique(explode(",",$p_name[csf('order_no')])));
	}
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$arr=array(2=>$po_name_arr,9=>$color_arr);

	$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id from pro_batch_create_mst a
	inner join pro_batch_create_dtls b on a.id = b.mst_id
	where a.company_id=$company_name $search_condition $date_cond and a.page_without_roll=0 and a.status_active=1 and a.entry_form=0 and a.is_deleted=0
	group by a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id";

	echo create_list_view("tbl_list_search", "Batch No,Ext. No,Order No,Booking No,Batch Weight,Total Trims Weight, Batch Date,Batch Against,Batch For, Color", "100,70,150,105,80,80,80,80,85,80","1000","320",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,0,id,0,0,0,0,batch_against,batch_for,color_id", $arr, "batch_no,extention_no,id,booking_no,batch_weight,total_trims_weight,batch_date,batch_against,batch_for,color_id", "",'','0,0,0,0,2,2,3,0,0');
	exit();
}

if($action == "get_batch_barcodes"){
	$barcode_nos = '';
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$batch_id_condition = ($batch_id != "") ? " and a.id=$batch_id" : "";
	if($batch_id_condition == ""){
		$batch_no_condition = ($batch_number != "") ? " and a.batch_no='$batch_number'" : "";
		$barcode_condition  = ($barcode_number != "") ? " and b.barcode_no='$barcode_number'" : "";
	}
	$barcodeData = sql_select("select a.id, a.batch_no,b.barcode_no, b.roll_id, c.dtls_id from pro_batch_create_mst a
		inner join pro_batch_create_dtls b on a.id = b.mst_id inner join pro_roll_details c on b.roll_id = c.id where a.company_id=1 and a.status_active=1 and a.is_deleted=0 $batch_id_condition $batch_no_condition $barcode_condition");


	if(!empty($barcodeData)){
		foreach ($barcodeData as $value) {
			$barcode_nos .= $value[csf('roll_id')] . ',';
			$dtls_id = $value[csf('dtls_id')];
			$batch_no = $value[csf('batch_no')];
		}
		echo rtrim($barcode_nos,',') . '***' . $dtls_id;
	}else{
		echo "Not Found";
	}

	exit;
}

if ($action=="barcode_hmtl_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_id=$data[0];
	$batch_no=$data[1];
	$batch_id=$data[2];
	$rpt_title=$data[4];

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");

	$batch_cond="";
	if($batch_id!="") $batch_cond=" and a.id=$batch_id";
	if($barcode_no!="") $batch_cond.=" and b.barcode_no=$barcode_no";

	if($db_type==0)
	{
		$batch_sql="select a.color_id, a.batch_no, a.batch_date,b.item_description,  group_concat(c.po_number) as po_number, c.job_no_mst,c.grouping,c.file_no  from  pro_batch_create_mst a, pro_batch_create_dtls b, wo_po_break_down c where a.id=b.mst_id and b.po_id=c.id and a.company_id=$company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_cond group by a.batch_no, a.batch_date, c.job_no_mst,c.grouping,c.file_no,b.item_description,a.color_id";
	}
	else
	{
		$batch_sql="select a.color_id, a.batch_no, a.batch_date,b.item_description, listagg(cast(c.po_number as varchar(4000)),',') within group (order by c.po_number) as po_number, c.job_no_mst,c.grouping,c.file_no  from  pro_batch_create_mst a, pro_batch_create_dtls b, wo_po_break_down c where a.id=b.mst_id and b.po_id=c.id and a.company_id=$company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $batch_cond group by a.batch_no, a.batch_date, c.job_no_mst,c.grouping,c.file_no,b.item_description,a.color_id";
	}

	$batch_result=sql_select($batch_sql);
	$item_des="";

	foreach($batch_result as $row)
	{
		$batch_no=$row[csf("batch_no")];
		$batch_date=$row[csf("batch_date")];
		$po_number=implode(",",array_unique(explode(",",$row[csf("po_number")])));
		$job_no_mst=$row[csf("job_no_mst")];
		$item_des.=$row[csf("item_description")].",";
		$color_id=$color_arr[$row[csf("color_id")]];
		$grouping=$row[csf("grouping")];
		$file_no=$row[csf("file_no")];
	}
	?>
	<style type="text/css">
		#html_print_id > tbody > tr > td{
			padding-bottom: 30px;
		}
	</style>
	<div style="width:1650px;">
		<table width="1650" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u>Roll to Roll entry page(Dyeing Finishing Dept.)</u></strong></td>
			</tr>
			<tr>
				<td width="125"><strong>Job:</strong></td><td width="" align="left"><? echo $job_no_mst; ?></td>
			</tr>
			<tr>
				<td width="125"><strong>Order:</strong></td><td width=""><? echo $po_number; ?></td>
			</tr>

			<tr>
				<td width="125"><strong>File:</strong></td><td width=""><? echo $file_no; ?></td>
			</tr>
			<tr>
				<td width="125"><strong>Ref No:</strong></td><td width=""><? echo $grouping; ?></td>
			</tr>


			<tr>
				<td width="125"><strong>Batch:</strong></td><td width=""><? echo $batch_no; ?></td>
			</tr>
			<tr>
				<td width="125"><strong>Color:</strong></td><td width=""><? echo $color_id; ?></td>
			</tr>
			<tr>
				<td width="125"><strong>Cons/Composition:</strong></td> <td width=""><? echo chop($item_des,","); ?></td>
			</tr>
			<tr>
				<td width="125"><strong>Date:</strong></td> <td width=""><? if($batch_date!="" && $batch_date!="0000-00-00") echo change_date_format($batch_date); ?></td>
			</tr>
		</table>
		<br>
		<table align="right" cellspacing="0" width="1650"  border="1" rules="all" class="rpt_table" id="html_print_id" >
			<thead bgcolor="#dddddd" align="center">
				<th width="50">SL</th>
				<th width="100">Barcode</th>
				<th width="100">Construction</th>
				<th width="50" >Grey Qty</th>
				<th width="50" >Reject</th>
				<th width="75" >QC<br>Pass Qty</th>
				<th width="50" >GSM</th>
				<th width="50" >UOM</th>
				<th width="50" >F.Dia</th>
				<th width="50" >Shade</th>

				<th width="50" >Hole</th>
				<th width="50" >Color Spot</th>
				<th width="50" >Softener Spot</th>
				<th width="50" >Insect Spot</th>
				<th width="50" >Oil Spot</th>
				<th width="50" >Yellow Spot</th>
				<th width="50" >Yarn Conta</th>
				<th width="50" >Fly Conta</th>
				<th width="50" >Poly Conta</th>
				<th width="50" >Slub</th>
				<th width="50" >Slub Hole</th>
				<th width="50" >Dirty Stain</th>
				<th width="50" >Cut/Joint</th>
				<th width="50" >Defect Name</th>
				<th width="50" >Chem: Stain</th>
				<th width="50" >Dust</th>
				<th width="50" >Print Mis</th>

				<th width="50" >Length</th>
				<th width="130" >Remarks</th>
			</thead>
			<tbody >
				<?
				$sql_dtls="select a.id, a.batch_no,b.barcode_no,b.batch_qnty,b.item_description from pro_batch_create_mst a inner join pro_batch_create_dtls b on a.id = b.mst_id and b.status_active=1 and b.is_deleted=0 where a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and a.id = $batch_id";
				$sql_result= sql_select($sql_dtls);
				$i = 1;
				$grey_receive_qnty_sum = 0;
				if(!empty($sql_result)){
					foreach($sql_result as $row)
					{
						$construction_arr=explode(",", $row[csf("item_description")]);
						$construction=$construction_arr[0];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i++; ?></td>
							<td align="center"><? echo $row[csf("barcode_no")]; ?></td>
							<td align="center"><? echo $construction; ?></td>
							<td align="center"><? echo $row[csf("batch_qnty")]; ?></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>

							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>

							<td></td>
							<td></td>
						</tr>
						<?
						$grey_receive_qnty_sum += $row[csf("batch_qnty")];
					}
				}else{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td colspan="11" align="center">No Data Found</td>
					</tr>
					<? } ?>
				</tbody>
				<tfoot>
					<tr>
						<td style="padding-bottom: 40px;"></td>
						<td></td>
						<td  align="right"><strong>Total = </strong></td>
						<td align="center"><strong><?php echo $grey_receive_qnty_sum; ?></strong></td>
						<td></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>

						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>

						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(106, $data[0], "900px");
			?>
		</div>
		<?
		exit();
	}

	if ($action == "report_barcode_text_file") {
		$data = explode("***", $data);
		// print_r($data);
		/*$dtls_id="";
		// For "Grey Fabric Bar-code Striker Export Report" report page
		if ($data[2] != '' || $data[3] != '') {
			$batch_no_condition = ($data[2] != "") ? " and a.batch_no='" . $data[2] . "'" : "";
			$barcode_condition = ($data[3] != "") ? " and b.barcode_no='" . $data[3] . "'" : "";

			$barcodeDataSql = "select a.id, a.batch_no,b.barcode_no, b.roll_id, c.dtls_id from pro_batch_create_mst a
			inner join pro_batch_create_dtls b on a.id = b.mst_id inner join pro_roll_details c on b.roll_id = c.id where a.company_id=$data[0] and a.status_active=1 and a.is_deleted=0 $batch_id_condition $batch_no_condition $barcode_condition";
			$barcodeData = sql_select($barcodeDataSql);
			if (!empty($barcodeData)) {
				foreach ($barcodeData as $value) {
					$barcode_nos .= $value[csf('roll_id')] . ',';
					$dtls_id .= $value[csf('dtls_id')].",";
					$batch_no = $value[csf('batch_no')];
				}
				$data[0] = rtrim($barcode_nos, ',');
				$data[1] = rtrim($dtls_id,",");
			} else {
				echo "Not Found";
			}
		}*/
		// For "Grey Fabric Bar-code Striker Export Report" report page (end)

		$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
		$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
		$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
		$machine_no_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
		$machine_brand_arr = return_library_array("select id, brand from lib_machine_name", 'id', 'brand');
		$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
		$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

		$id_info=sql_select("SELECT id, mst_id, dtls_id from pro_roll_details where barcode_no=$data[1] and entry_form=2
			and status_active=1 and is_deleted=0");

		$mst_id = $id_info[0][csf('mst_id')];
		$dtl_id = $id_info[0][csf('id')];
		$dtlss_id = $id_info[0][csf('dtls_id')];
		// print_r($id_info);

		$sql_yarn_info=sql_select("SELECT a.prod_id,b.brand,b.yarn_type,b.yarn_comp_type1st,b.yarn_comp_type2nd,b.yarn_comp_percent1st,b.yarn_comp_percent2nd,b.lot,b.yarn_count_id from pro_material_used_dtls a,product_details_master b  where a.prod_id=b.id and  a.mst_id=$mst_id and a.dtls_id in($dtlss_id) and a.entry_form=2
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$yarn_information_string=""; $all_yarn_type='';
		foreach($sql_yarn_info as $p_val)
		{
			$costing_yarn_composition='';
			$costing_yarn_band=$brand_arr[$p_val[csf('brand')]];
			$costing_yarn_lot=trim($p_val[csf('lot')]);
			$costing_yarn_count=$count_arr[$p_val[csf('yarn_count_id')]];
			$costing_yarn_composition=$composition[$p_val[csf('yarn_comp_type1st')]] . " " . $p_val[csf('yarn_comp_percent1st')] . "%";
			if ($p_val[csf('yarn_comp_type2nd')] != 0) $costing_yarn_composition .= " " . $composition[$p_val[csf('yarn_comp_type2nd')]] . " " . $p_val[csf('yarn_comp_percent2nd')] . "%";
			$yarn_information_string.=$costing_yarn_band." ".$costing_yarn_lot." ".$costing_yarn_count." ".$costing_yarn_composition. "\r\n";
			$all_yarn_type.=",".$yarn_type[$p_val[csf('yarn_type')]];
		}

		$sql = "SELECT a.company_id, a.recv_number, a.location_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date, a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm, b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.shift_name, b.insert_date,b.operator_name, b.color_range_id, b.floor_id,b.body_part_id  from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.id in($dtlss_id)";
		$result = sql_select($sql);
		$party_name = '';
		$prod_date = '';
		$order_id = '';
		$buyer_name = '';
		$grey_dia = '';
		$tube_type = '';
		$program_no = '';
		$booking_no = '';
		$booking_without_order = '';
		$yarn_lot = '';
		$yarn_count = '';
		$brand = '';
		$gsm = '';
		$finish_dia = '';
		$shiftName = '';
		$colorRange = '';
		$productionId = '';
		$comp=$construction=array();
		foreach ($result as $row) {
			if ($row[csf('knitting_source')] == 1) {
				$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
			} else if ($row[csf('knitting_source')] == 3) {
				$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
			}
			$yarn_type_data = return_field_value("yarn_type", "product_details_master", "id=" . $row[csf('prod_id')]);

			$booking_no = $row[csf('booking_no')];
			$booking_id = $row[csf('booking_id')];
			$operator_name = $operator_name_arr[$row[csf('operator_name')]];
			$floor_name = $floor_name_arr[$row[csf('floor_id')]];

			$booking_without_order = $row[csf('booking_without_order')];
			$productionId = $row[csf('recv_number')];
			$prod_date = date("d-m-Y", strtotime($row[csf('receive_date')]));
			$location_name=return_field_value("location_name","lib_location", "id=".$row[csf('location_id')]);
			$order_id = $row[csf('order_id')];
			$gsm = $row[csf('gsm')];
			$finish_dia = $row[csf('width')];
			$shiftName = $shift_name[$row[csf('shift_name')]];
			$colorRange = $color_range[$row[csf('color_range_id')]];
			$color = '';
			$color_id = explode(",", $row[csf('color_id')]);
			foreach ($color_id as $val) {
				if ($val > 0) $color .= $color_arr[$val] . ",";
			}
			$color = chop($color, ',');
			if (trim($color) != "") {
			}

			$stitch_length = $row[csf('stitch_length')];
			$yarn_lot = $row[csf('yarn_lot')];
			$brand = $brand_arr[$row[csf('brand_id')]];
			$yarn_count = '';
			$count_id = explode(",", $row[csf('yarn_count')]);
			foreach ($count_id as $val) {
				if ($val > 0) {
					if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
				}
			}

			if ($row[csf('receive_basis')] == 0 || $row[csf('receive_basis')] == 1 || $row[csf('receive_basis')] == 4) {
				$machine_data = sql_select("select machine_no, dia_width, gauge,brand from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
				$machine_name = $machine_data[0][csf('machine_no')];
				$machine_dia_width = $row[csf('machine_dia')];
				$machine_gauge = $row[csf('machine_gg')];
				$machine_brand = $row[csf('brand')];
				if($row[csf('receive_basis')]==1)
				{
					$sql_precost_tube=sql_select("select  b.width_dia_type,b.color_type_id from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no='$booking_no' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.body_part_id=".$row[csf('body_part_id')]." and b.lib_yarn_count_deter_id=".$row[csf('febric_description_id')]."");
					foreach($sql_precost_tube as $t_val)
					{
						$tube_type = $fabric_typee[$t_val[csf('width_dia_type')]];
						$color_type_name = $color_type[$t_val[csf('color_type_id')]];
					}
				}

			} else if ($row[csf('receive_basis')] == 2) {
				$program_data = sql_select("select a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg, b.machine_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
				$program_no = $row[csf('booking_id')];
				$grey_dia = $program_data[0][csf('machine_dia')];
				$tube_type = $fabric_typee[$program_data[0][csf('width_dia_type')]];
				$machine_dia_width = $program_data[0][csf('machine_dia')];
				$machine_gauge = $program_data[0][csf('machine_gg')];
				$machine_brand = $machine_brand_arr[$row[csf('machine_no_id')]];
				$machine_name = $machine_no_arr[$row[csf('machine_no_id')]];
				$row[csf("within_group")] = $program_data[0][csf('within_group')];
			}

			if ($row[csf("within_group")] == 1)
				$buyer_name = return_field_value("company_short_name", "lib_company", "id='" . $row[csf('buyer_id')] . "'");
			else
				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);


			if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "") {
				$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
			} else {
				$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);

				if ($determination_sql[0][csf('construction')] != "") {
					$comp[$row[csf('febric_description_id')]] = $determination_sql[0][csf('construction')] . ", ";
					$construction[$row[csf('febric_description_id')]] = $determination_sql[0][csf('construction')];
				}

				foreach ($determination_sql as $d_row) {
					$comp[$row[csf('febric_description_id')]] .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
					$composi[$row[csf('febric_description_id')]] .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
				}
			}
		}

		$po_array = array();
		$booking_no_prefix = '';
		if ($booking_without_order == 1) {
			if ($row[csf("receive_basis")] == 4) {

				$fb_sales_sql = "select id,job_no_prefix_num,job_no,style_ref_no,within_group from fabric_sales_order_mst where id = " . $row[csf('booking_id')];
				$fb_salesResult = sql_select($fb_sales_sql);
				$booking_no_prefix = $fb_salesResult[0][csf('job_no_prefix_num')];
				$full_booking_no = $fb_salesResult[0][csf('job_no')];
				$style_ref_no = $fb_salesResult[0][csf('style_ref_no')];
				$sales_id = $fb_salesResult[0][csf('id')];

				$no_arr = explode("-", $full_booking_no);
				array_shift($no_arr); //remove 1st index
				$full_booking_no = implode("-", $no_arr);
				$po_array[$sales_id]['style_ref'] = $style_ref_no;

			} else {
				$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
				$full_booking_no = $booking_no;

				$sql_color_type=sql_select("select color_type_id from wo_non_ord_samp_booking_dtls where booking_no='".$booking_no."' and body_part=".$row[csf('body_part_id')]." and lib_yarn_count_deter_id =".$row[csf('febric_description_id')]." and status_active=1 and is_deleted=0  ");
				foreach($sql_color_type as $n_val)
				{
					$color_type_arr[]= $color_type[$n_val[csf('color_type_id')]];
				}
				$color_type_name=implode(",",array_unique($color_type_arr));
			}
		} else {
			$is_salesOrder = 0;
			if ($row[csf("receive_basis")] == 2) {
				$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id='" . $row[csf("booking_id")] . "'");
				$booking_no = return_field_value("b.booking_no as booking_no", "ppl_planning_info_entry_dtls a,ppl_planning_info_entry_mst b", " b.id=a.mst_id and a.id='" . $booking_id . "'", "booking_no");
			}
			if ($is_salesOrder == 1) {
				if ($row[csf("within_group")] == 1) {
					$po_sql = sql_select("select a.id, a.job_no as po_number, a.style_ref_no, a.job_no_prefix_num, a.sales_booking_no,b.buyer_id,a.within_group from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no=b.booking_no and a.id in($order_id)");
				} else {
					$po_sql = sql_select("select a.id, a.job_no as po_number, a.style_ref_no, a.job_no_prefix_num, a.sales_booking_no,a.buyer_id,a.within_group from fabric_sales_order_mst a where a.id in($order_id)");
				}
				foreach ($po_sql as $row) {
					$no_arr = explode("-", $row[csf('job_no')]);
				array_shift($no_arr); //remove 1st index
				$full_booking_no = implode("-", $no_arr);

				$po_no_arr = explode("-", $row[csf('po_number')]);
				array_shift($po_no_arr); //remove 1st index
				$po_no_arr = implode("-", $po_no_arr);
				$po_array[$row[csf('id')]]['no'] = $po_no_arr;
				$po_array[$row[csf('id')]]['job_no'] = $full_booking_no;
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_id')];
			}
		} else {
			$po_sql = sql_select("select a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)");
			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
				$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
			}
		}
	}
	$within_group = $row[csf("within_group")];
	foreach (glob("" . "*.zip") as $filename) {
		@unlink($filename);
	}
	$i = 1;
	$zip = new ZipArchive();
	$filename = str_replace(".sql", ".zip", 'norsel_bundle.sql');
	if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {
		$error .= "* Sorry ZIP creation failed at this time<br/>";
		echo $error;
	}

	$i = 1;
	$year = date("y");

	$query = "select a.id,a.roll_no,a.po_breakdown_id,a.barcode_no,a.qnty,a.reject_qnty,b.batch_wgt,b.febric_description_id from pro_roll_details a left join pro_grey_batch_dtls b on a.id = b.roll_id where a.id in($dtl_id) order by a.barcode_no asc";
	$res = sql_select($query);
	$split_data_arr = array();

	foreach ($res as $row) {
		$txt="";
		$split_roll_id = $row[csf('id')];
		$compo = $comp[$row[csf('febric_description_id')]];
		$roll_split_query = sql_select("select a.barcode_no, a.qnty, a.id, a.roll_split_from from pro_roll_details a where a.roll_id = $split_roll_id and a.roll_split_from != 0");
		$file_name = "NORSEL-IMPORT_" . $i;
		$myfile = fopen($file_name . ".txt", "w") or die("Unable to open file!");
		$txt .= "Norsel_imp\r\n1\r\n";
		if ($booking_without_order == 1) {
			$txt .= $party_name . "\r\n";
			$txt .= $booking_no_prefix . "\r\n";
			$txt .= $machine_name . "-" . $machine_dia_width . "X" . $machine_gauge . "\r\n";
			$full_job_no = $full_booking_no;
		} else {
			$txt .= $party_name . "\r\n";
			$txt .= $po_array[$row[csf('po_breakdown_id')]]['prefix'] . "\r\n";
			$txt .= $machine_name . "-" . $machine_dia_width . "X" . $machine_gauge . "\r\n";
			$full_job_no = $po_array[$row[csf('po_breakdown_id')]]['job_no'];
		}
		if (!empty($roll_split_query)) {
			$qnty = number_format($roll_split_query[0][csf('qnty')], 2, '.', '');
			$barcode = $roll_split_query[0][csf('barcode_no')];
		} else {
			$qnty = number_format($row[csf('QNTY')], 2, '.', '');
			$barcode = $row[csf('barcode_no')];
		}
		$txt .= $barcode . "\r\n";
		$txt .= $barcode . "\r\n";
		$txt .= "" . $prod_date . "\r\n";
		$txt .= $buyer_name . "\r\n";
		$txt .= "" . $po_array[$row[csf('po_breakdown_id')]]['no'] . "\r\n";
		$txt .= "" . $po_array[$row[csf('po_breakdown_id')]]['file_no'] . "\r\n";
		$txt .= "" . $po_array[$row[csf('po_breakdown_id')]]['grouping'] . "\r\n";
		$txt .= "" . $compo . "\r\n";
		$dia_length_tube=trim($grey_dia);
		if($dia_length_tube!="") $dia_length_tube.="/";
		$dia_length_tube.=trim($finish_dia) . " " . trim($stitch_length) . " " . trim($tube_type) . "\r\n";
		$txt.=$dia_length_tube;
		$txt .= "" . $gsm . "\r\n";
		$txt .= $yarn_count . "\r\n";//.$brand." Lot:".$yarn_lot."\r\n";
		$txt .= $brand . "\r\n";
		$txt .= $yarn_lot . "\r\n";
		$txt .= "" . $program_no . "\r\n";
		$txt .= $qnty . "Kg\r\n";
		$txt .= $shiftName . "\r\n";
		$txt .= "" . $row[csf('roll_no')] . "\r\n";
		$txt .= trim($color) . "\r\n";
		$txt .= "" . trim($colorRange) . "\r\n";
		$txt .= "" . $po_array[$row[csf('po_breakdown_id')]]['style_ref'] . "\r\n";
		$txt .= "" . $booking_no . "\r\n";
		$txt .= "" . $operator_name . "\r\n";
		$txt .= "" . $productionId . "\r\n";
		if ($within_group == 1) {
			$txt .= "" . return_field_value("short_name", "lib_buyer", "id=" . $po_array[$row[csf('po_breakdown_id')]]['buyer_name']) . "\r\n";
		} else {
			$txt .= $buyer_name . "\r\n";
		}
		$txt .= "" . $machine_brand . "\r\n";
		$txt .= "" . $yarn_type[$yarn_type_data] . "\r\n";

		$txt .= "" . $construction[$row[csf('febric_description_id')]] . "\r\n";
		$txt .= "" . $composi[$row[csf('febric_description_id')]] . "\r\n";
		$txt .= "" . $floor_name . "\r\n";
		$txt .= $machine_name . "\r\n";
		$txt .= $machine_dia_width . "X" . $machine_gauge . "\r\n";
		$txt .= $full_job_no . "\r\n";
		$dia_tube=trim($grey_dia);
		if($dia_tube!="") $dia_tube.="/";
		$dia_tube.=trim($finish_dia) ." ". trim($tube_type) . "\r\n";
		$txt.=$dia_tube;
		//$txt .= "" . trim($grey_dia) . "/" . trim($finish_dia) . " " . trim($tube_type) . "\r\n";
		$txt .= trim($stitch_length) . "\r\n";
		$txt .= "Rej. Qty.:" . $row[csf('reject_qnty')] . "\r\n";
		$txt.=$yarn_information_string;
		$txt.=ltrim($all_yarn_type,","). "\r\n";
		$txt .=$location_name. "\r\n";
		$txt .=$color_type_name. "\r\n";
		fwrite($myfile, $txt);
		fclose($myfile);
		//echo $myfile;
		//echo $comp[$row[csf('febric_description_id')]];
		$i++;
	}
	//print_r(glob("" . "*.txt"));
	//echo $txt;
	foreach (glob("" . "*.txt") as $filenames) {
		$zip->addFile($file_folder . $filenames);
	}
	$zip->close();

	foreach (glob("" . "*.txt") as $filename) {
		@unlink($filename);
	}
	echo "norsel_bundle";
	exit();
}



if ($action == "print_barcode_one_128_v2")
{
	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');


	$data = explode("***", $data);
	// var_dump($data);
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$user_arr = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
	$brand_id_arr = return_library_array("select lot, brand from product_details_master where item_category_id=1", 'lot', 'brand');
	///print_r($brand_id_arr['6112018']);die;
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');

	$query = "SELECT a.id,a.inserted_by, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty, b.fabric_grade,c.shift_name,d.recv_number_prefix_num,c.id as production_id, c.febric_description_id from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no left join pro_grey_prod_entry_dtls c on a.dtls_id=c.id left join inv_receive_master d on c.mst_id=d.id where a.barcode_no in($data[1]) and a.entry_form=2";

	//echo $query;
	$res = sql_select($query);
	$production_id_arr = array();
	foreach ($res as $row)
	{
		if($prodIdChk[$row[csf('production_id')]] == "")
        {
            $prodIdChk[$row[csf('production_id')]] = $row[csf('production_id')];
            array_push($production_id_arr,$row[csf('production_id')]);
        }
	}


	$sql = "SELECT a.company_id,a.receive_basis,a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id,b.id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id,b.operator_name from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id ".where_con_using_array($production_id_arr,0,'b.id')." ";
	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	$fab_booking_no = '';
	$constuction="";
	$constuction2="";
	foreach ($result as $row) 
	{
		if ($row[csf('knitting_source')] == 1) {
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}

		$receive_date=$row[csf('receive_date')];
		$booking_no = $row[csf('booking_no')];
		$booking_without_order = $row[csf('booking_without_order')];

		if ($row[csf("receive_basis")] == 1)
		{
			$fab_booking_no = $row[csf('booking_no')];
		}
		else if($row[csf("receive_basis")] == 2)
		{
			$fab_booking_no = return_field_value("a.booking_no as booking_no", "ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b", " a.id = b.mst_id and b.id ='".$row[csf('booking_id')]."'","booking_no");
		}

		$prod_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));

		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$operator_name = $row[csf('operator_name')];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_length')];
		$yarn_lot = $row[csf('yarn_lot')];

		$brand='';
		$lot_string = explode(",", $row[csf('yarn_lot')]);
		foreach ($lot_string as $val) {
			if ($val!="") $brand .= $brand_arr[$brand_id_arr[$val]] . ",";
		}
		$brand = chop($brand, ',');
		//$brand = $brand_arr[$row[csf('brand_id')]];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}

		if ($row[csf("receive_basis")] == 2) {
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$planning_data = sql_select("select a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $planning_data[0][csf('machine_dia')];
			$machine_gauge = $planning_data[0][csf('machine_gg')];

			$row[csf("within_group")] = $planning_data[0][csf('within_group')];

			$program_no = $row[csf('booking_id')];
			$grey_dia = $planning_data[0][csf('machine_dia')];
			$tube_type = $fabric_typee[$planning_data[0][csf('width_dia_type')]];
		} else {
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];
		}

		if ($row[csf("within_group")] == 1)
			$buyer_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('buyer_id')]);
		else
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);

		$comp = '';
		if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "") 
		{
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
		} 
		else 
		{
			$determination_sql = sql_select("select a.id,a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);

			if ($determination_sql[0][csf('construction')] != "") 
			{
				//$comp = $determination_sql[0][csf('construction')] . ", ";
				//$constuction = $determination_sql[0][csf('construction')];
				$constuction_arr[$row[csf('id')]]=$determination_sql[0][csf('construction')];
			}

			foreach ($determination_sql as $d_row) 
			{
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
				$comp_arr[$row[csf('id')]].= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
			}
		}
		$company_short_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('company_id')]);
	}
	// echo $constuction.'<br>';die;

	$po_array = array();
	$booking_no_prefix = '';
	if ($booking_without_order == 1) 
	{
		if ($row[csf("receive_basis")] == 4) 
		{
			$sales_info = sql_select("select a.job_no_prefix_num,a.job_no,a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id='" . $row[csf("booking_id")] . "'");
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $sales_info[0][csf('buyer_id')]);
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_booking_mst", "booking_no='" . $sales_info[0][csf('sales_booking_no')] . "'");
			$order_no = $sales_info[0][csf('job_no')];
		} 
		else 
		{
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
		}
	} 
	else 
	{
		$is_salesOrder = 0;
		if ($row[csf("receive_basis")] == 2) {
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=" . $row[csf("booking_id")]);
		}
		if ($is_salesOrder == 1) {
			$po_sql = sql_select("select a.job_no_prefix_num,a.job_no as po_number,a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id in($order_id)");
			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);
			}
		} else {

			if ($row[csf("receive_basis")] == 2)
			{
				$planning_booking_sql = sql_select("select a.booking_no_prefix_num from wo_booking_mst a,ppl_planning_entry_plan_dtls b where a.booking_no=b.booking_no and   b.dtls_id='" . $row[csf('booking_id')] . "'");
				$planning_booking_prefix=$planning_booking_sql[0][csf('booking_no_prefix_num')];

			}

			$po_sql = sql_select("select a.job_no,a.job_no_prefix_num,a.buyer_name,b.id,b.po_number,d.booking_no_prefix_num from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c,wo_booking_mst d where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no=d.booking_no and d.booking_type=1 and d.is_short=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.id in($order_id)");
			foreach ($po_sql as $row1) {
				$po_array[$row1[csf('id')]]['no'] = $row1[csf('po_number')];
				$po_array[$row1[csf('id')]]['job_no'] = $row1[csf('job_no')];
				$po_array[$row1[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				if ($row[csf("receive_basis")] == 2)
				{
					$po_array[$row1[csf('id')]]['booking_no'] = $planning_booking_prefix;
				}
				else
				{
					$po_array[$row1[csf('id')]]['booking_no'] = $row1[csf('booking_no_prefix_num')];
				}

				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row1[csf('buyer_name')]);
			}
		}
	}
	$i = 1;
	$barcode_array = array();

	// $query = "SELECT a.id,a.inserted_by, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty, b.fabric_grade,c.shift_name,d.recv_number_prefix_num from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no left join pro_grey_prod_entry_dtls c on a.dtls_id=c.id left join inv_receive_master d on c.mst_id=d.id where a.id in($data[0])";
	// $res = sql_select($query);


	$pdf=new PDF_Code128('P','mm',array(80,70));
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',10);


	$i=2; $j=1; $k=0; $br=0; $n=0;
	foreach ($res as $row)
	{
		$order_no = $po_array[$row[csf('po_breakdown_id')]]['no'];
		$constuction = $constuction_arr[$row[csf('production_id')]];
		$comp=$comp_arr[$row[csf('production_id')]];
		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=1; $k=0;
		}

		$pdf->Code128($i+1,$j,$row[csf("barcode_no")],50,8);
		$pdf->SetXY($i, $j+10);
		$pdf->Write(0, $row[csf("barcode_no")]. ",Dt:".change_date_format($receive_date). ",Pg:".$program_no. ",S:".$shift_name[$row[csf('shift_name')]]);

		$pdf->SetXY($i, $j+14);
		//." B: " . $po_array[$row[csf('po_breakdown_id')]]['booking_no']
		$pdf->Write(0, $company_short_name.":" . $po_array[$row[csf('po_breakdown_id')]]['booking_no'].",M/C:" . $machine_name . "," . $machine_dia_width . "X" . $machine_gauge. ",RW:" . number_format($row[csf('qnty')], 2, '.', ''));

		$pdf->SetXY($i, $j+18);
		$pdf->Write(0, $buyer_name . ",Po:" . substr($order_no, 0, 25));//24

		$pdf->SetXY($i, $j+22);
		$pdf->Write(0, "Clr:" .substr($color, 0, 35));

		$pdf->SetXY($i, $j+26);
		$pdf->Write(0, "Ct:".$yarn_count.",Lt:".$yarn_lot);

		$pdf->SetXY($i, $j+30);
		$pdf->Write(0, "Br:". $brand.",".$constuction);
		//
		$pdf->SetXY($i, $j+34);
		$pdf->Write(0, substr($comp, 0, 45));

		$pdf->SetXY($i, $j+38);
		$pdf->Write(0, "G/F Dia:" . $grey_dia. "," . trim($finish_dia).",GSM:". $gsm.",SL:" . trim($stitch_length));

		$pdf->SetXY($i, $j+42);
		$pdf->Write(0, "Prd:".$row[csf('recv_number_prefix_num')]. ",RL No:" . $row[csf('roll_no')] .",ID:" .$user_arr[$row[csf('inserted_by')]]);

		$pdf->SetXY($i, $j+46);
		$pdf->Write(0, "Fabric Booking:".$fab_booking_no);
		//"D/T: " .trim($tube_type).

		$k++;
		$br++;
	}

	foreach (glob("*".$userid.".pdf") as $filename) {
		@unlink($filename);
	}
	$name ='knitting_barcode_'.date('j-M-Y_h-iA').'_'.$userid.'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

if($action=="check_barcode_generate")
{
	$sql=sql_select("select id, smv_source from variable_settings_production where company_name='$data' and variable_list=27 and smv_source=1 and status_active=1 and is_deleted=0");

	if(count($sql)>0) echo $sql[0][csf('smv_source')];
	else{ echo 0; }
	exit();
}

?>

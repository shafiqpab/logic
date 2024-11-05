<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$permission=$_SESSION['page_permission'];



if ($action=="load_drop_down_floor")
{
	//echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id='$data'  order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
	
	echo create_drop_down( "cbo_floor_id", 130, "select a.id,a.floor_name from lib_prod_floor a,lib_machine_name b where a.id=b.floor_id and  a.status_active =1 and b.status_active=1 and a.company_id='$data'  group by a.id,a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
}

if($action=="machine_no_search_popup")
{
	

			
	echo load_html_head_contents("Machine No popup", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
		
		?>

		<script>

			var selected_id = new Array;
			var selected_name = new Array;

			function check_all_data() {
				var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
				tbl_row_count = tbl_row_count - 1;

				for (var i = 1; i <= tbl_row_count; i++) {
					$('#tr_' + i).trigger('click');
				}
			}

			function toggle(x, origColor) {
				var newColor = 'yellow';
				if (x.style) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
				}
			}

			function js_set_value(str) {

				if (str != "") str = str.split("_");

				toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

				if (jQuery.inArray(str[1], selected_id) == -1) {
					selected_id.push(str[1]);
					selected_name.push(str[2]);

				}
				else {
					for (var i = 0; i < selected_id.length; i++) {
						if (selected_id[i] == str[1]) break;
					}
					selected_id.splice(i, 1);
					selected_name.splice(i, 1);
				}
				var id = '';
				var name = '';
				for (var i = 0; i < selected_id.length; i++) {
					id += selected_id[i] + ',';
					name += selected_name[i] + '*';
				}

				id = id.substr(0, id.length - 1);
				name = name.substr(0, name.length - 1);

				$('#hide_order_id').val(id);
				$('#hide_order_no').val(name);
			}

		</script>

	</head>

	<body>
		

		<div align="center">
			
					<input type="hidden" name="hide_order_no" id="hide_order_no" value=""/>
					<input type="hidden" name="hide_order_id" id="hide_order_id" value=""/>

					<?
						$company_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
						$floor_lib=return_library_array("select id,floor_id from lib_machine_name", "id", "floor_id");
						$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );
						$arr = array( 0 => $floor_arr);
							$sql="select id,company_id,floor_id,machine_no,machine_group,dia_width,gauge,brand from lib_machine_name where status_active=1 and is_deleted=0 and company_id=$company_id and floor_id=$floor_id";
							//echo $sql;die;
							echo create_list_view("tbl_list_search", "Floor,Machine No,Brand Name,Machine Group,Dia Width, Gauge", "120,100,100,110,60", "660", "220", 0, $sql, "js_set_value", "id,machine_no", "", 1, "floor_id,0,0,0,0,0", $arr, "floor_id,machine_no,brand,machine_group,dia_width,gauge", "", '', '0,0,0,0,0,0', '', 1);
			?>
	</div>
	</body>
	<script type="text/javascript">
		setFilterGrid('tbl_list_search',-1);
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "style_ref_search_popup")
{
	echo load_html_head_contents("Style Reference / Job No. Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	//print_r($_SESSION['logic_erp']['data_arr'][478]); //die;
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][478] );
	
	?>
	<script>
		<?
		if (!empty($data_arr)) {
			echo "var field_level_data= ". $data_arr . ";\n";
		}
		else
		{
			echo "var field_level_data='';\n";
		}
		?>
		
		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str) {

			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_job_id' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_job_id' + str).val());
				selected_name.push($('#txt_job_no' + str).val());

			}
			else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_job_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
		}

	</script>

	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:600px;">
					<table width="590" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
					class="rpt_table" id="tbl_list">
					<thead>
						<th>Within Group</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Sales Order No</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
						<input type="hidden" name="hide_job_no" id="hide_job_no" value=""/>
						<input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
					</thead>
					<tbody>
						<tr>
							<td id="buyer_td" id="must_entry_form">
								<?
								echo create_drop_down("cbo_withing_group", 130, $yes_no,"", 1, "-- Select --", $selected, "");
								?>
							</td>
							<td align="center">
								<?
								$search_by_arr = array(1 => "FSO No", 2 => "Fabric Booking No",3=>"Style Ref");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 1, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show"
								onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $cbo_knitting_source; ?>'+'**'+document.getElementById('cbo_withing_group').value + '**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'program_wise_priority_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
								style="width:90px;"/>
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:05px" id="search_div"></div>
			</fieldset>
		</form>
		</div>
	</body>
	
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script type="text/javascript">
	setFieldLevelAccess('<? echo $companyID; ?>');
	</script>
  </html>
  <?
  exit();
}

if ($action == "create_job_search_list_view")
{
	$data = explode('**', $data);
	//print_r($data);die;
	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	$company_id = $data[0];
	$cbo_knitting_source = $data[1];
	$within_group = $data[2];

	$search_by = $data[3];
	$search_string = trim($data[4]);

	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and a.job_no like '%" . $search_string . "'";
		} else if($search_by == 2) {
			$search_field_cond = " and LOWER(a.sales_booking_no) like LOWER('%" . $search_string . "%')";
		}else{
			$search_field_cond = " and LOWER(a.style_ref_no) like LOWER('%" . $search_string . "%')";
		}
	}

	if ($within_group == 0 || $within_group=="") $within_group_cond = "and within_group in (1,2)"; else $within_group_cond = " and within_group=$within_group";
	
	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
	else $year_field = "";//defined Later
	if ($within_group == 1) {
		$sql = " select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no = b.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and within_group=$within_group $search_field_cond  and fabric_source in(1,2)
		union all
		select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num from fabric_sales_order_mst a,wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls c where a.sales_booking_no = b.booking_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and within_group=$within_group $search_field_cond   and  (b.fabric_source in(1,2) or c.fabric_source in(1,2)) group by a.id, a.insert_date, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id,b.booking_no_prefix_num,c.fabric_source";

	} else {
		$sql = " select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no booking_no_prefix_num, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  $within_group_cond $search_field_cond order by a.id";
	}
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="70">Sales Order No</th>
				<th width="60">Year</th>
				<th width="80">Within Group</th>
				<th width="70">PO Buyer</th>
				<th width="70">PO Company</th>
				<th width="120">Sales/ Booking No</th>
				<th>Style Ref.</th>
			</thead>
		</table>
	<div style="width:600px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="580" class="rpt_table"
		id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row)
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				if ($row[csf('within_group')] == 1)
					$buyer = $company_arr[$row[csf('buyer_id')]];
				else
					$buyer = $buyer_arr[$row[csf('buyer_id')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
					<td width="40"><? echo $i; ?>
					<input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>"
					value="<? echo $row[csf('id')]; ?>"/>
					<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>"
					value="<? echo $row[csf('job_no')]; ?>"/>
					</td>
					<td width="70"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80" align="center"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?>&nbsp;</p></td>
					<td width="70" align="center"><p><? echo $buyer; ?>&nbsp;</p></td>
					<td width="120" align="center"><p><? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
					<td><p><? echo $row[csf('style_ref_no')]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<table width="600" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/> Check /
						Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton"
						value="Close" style="width:100px"/>
					</div>
				</div>
			</td>
		</tr>
	</table>
<?
	exit();
}

if($action == "generate_report"){
	$process = array( &$_POST );
	//print_r($process);die;
	extract(check_magic_quote_gpc( $process ));
	//echo $hide_job_id;die;
	$hide_job_id=str_replace("'", "", $hide_job_id);
	$cbo_company_id = str_replace("'", "", $cbo_company_id);
	$cbo_floor_id = str_replace("'", "", $cbo_floor_id);
	$cbo_knitting_source = str_replace("'", "", $cbo_knitting_source);
	//echo $cbo_knitting_source;die;
	$cbo_mc_no_txt = str_replace("'", "", $cbo_mc_no_txt);
	$cbo_status = str_replace("'", "", $cbo_status);
	$hide_cbo_mc_id = str_replace("'", "", $hide_cbo_mc_id);
	//$cbo_fso_no_txt = str_replace("*", "'*'", $cbo_fso_no_txt);
	//$cbo_fso_arr=explode("*", $cbo_fso_no_txt);
	//$cbo_fso_no_txt=implode(",", $cbo_fso_arr);
	$status_cond="";
	if($cbo_status!="0"){
		$status_cond=" and b.status=$cbo_status "; 
	}
	$floor_con="";
	if($cbo_floor_id!="0" && $cbo_floor_id!=""){
		//$floor_con=" and b.floor_id=$cbo_floor_id";
	}

	$com_cond="";
	if($cbo_company_id!="0"){
		$com_cond=" and a.company_id=$cbo_company_id "; 
	}
	$source_con="";
	if($cbo_knitting_source!='0' && $cbo_knitting_source!=''){
		$source_con=" and b.knitting_source=$cbo_knitting_source";
	}
	$date_cond="";
	if(strlen($cbo_fso_no_txt) && $hide_job_id){

		$date_cond="";
		$fso_cond=" and c.id in($hide_job_id) ";

	}else{
		if (str_replace("'", "", $txt_date_from) != "" || str_replace("'", "", $txt_date_to) != "") {
		if ($db_type == 0) {
			$start_date = change_date_format(str_replace("'", "", $txt_date_from), "yyyy-mm-dd", "");
			$end_date = change_date_format(str_replace("'", "", $txt_date_to), "yyyy-mm-dd", "");
		} else if ($db_type == 2) {
			$start_date = change_date_format(str_replace("'", "", $txt_date_from), "", "", 1);
			$end_date = change_date_format(str_replace("'", "", $txt_date_to), "", "", 1);
		}
		$date_cond = " and b.program_date between '$start_date' and '$end_date'";
		
	}

	}

	$machine_con="";
	$machine_array=array();

	if(strlen($hide_cbo_mc_id) && $hide_cbo_mc_id)
	{
		$machine_con="  and b.machine_id in ($hide_cbo_mc_id) ";
		$machins=explode(",", $hide_cbo_mc_id);
		for($i=0;$i<count($machins);$i++)
		{

			array_push($machine_array, $machins[$i]);
		}
	}
	


	/*if($db_type==0)
	{
		$sql="select a.booking_no, a.company_id, b.machine_id, group_concat(b.id) as program_no
	 	from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,fabric_sales_order_mst c 
	 	where a.id=b.mst_id and a.booking_no=c.sales_booking_no and b.status_active=1 and b.is_deleted=0 and a.is_sales=1 and length(b.machine_id)>0  $com_cond  $fso_cond $date_cond $source_con $status_cond
	 	group by a.booking_no, a.company_id, b.machine_id";
	}
	else
	{
		$sql="select a.booking_no, a.company_id, b.machine_id, list_agg(cast(b.id as varchar(4000)),',') within_group(order by b.id) as program_no
	 	from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,fabric_sales_order_mst c 
	 	where a.id=b.mst_id and a.booking_no=c.sales_booking_no and b.status_active=1 and b.is_deleted=0 and a.is_sales=1 and length(b.machine_id)>0  $com_cond  $fso_cond $date_cond $source_con $status_cond
	 	group by a.booking_no, a.company_id, b.machine_id";
	}*/

	$sql="select a.booking_no, a.company_id, b.machine_id, b.id as program_no, b.program_date
	 	from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,fabric_sales_order_mst c 
	 	where a.id=b.mst_id and a.booking_no=c.sales_booking_no and b.status_active=1  and b.is_deleted=0 and a.is_sales=1 
	 	and length(b.machine_id)>0  $com_cond  $fso_cond $date_cond $source_con $status_cond  group by a.booking_no, a.company_id, b.machine_id, b.id, b.program_date 
	 	order by b.machine_id
	 	";
	
	//echo $sql;die;
	$res=sql_select($sql);
	
	//$result=array_values($result);
	$dtls_data=array();
	/*
	foreach ($res as $row)
	{
		$mc_id_arr=explode(",",$row[csf("machine_id")]);
		foreach($mc_id_arr as $m_id)
		{
			$dtls_data[$row[csf("booking_no")]][csf('program_date')][$m_id]["company_id"]=$row[csf("company_id")];
			$dtls_data[$row[csf("booking_no")]][csf('program_date')][$m_id]["booking_no"]=$row[csf("booking_no")];
			if($prog_dup_check[$row[csf("booking_no")]][csf('program_date')][$m_id][$row[csf("program_no")]]=="")
			{
				$prog_dup_check[$row[csf("booking_no")]][csf('program_date')][$m_id][$row[csf("program_no")]]=$row[csf("program_no")];
				$dtls_data[$row[csf("booking_no")]][csf('program_date')][$m_id]["program_date"]=$row[csf("program_date")];
				$dtls_data[$row[csf("booking_no")]][csf('program_date')][$m_id]["program_no"].=$row[csf("program_no")].",";
			}
		}
	}

	*/

$machine_lib = return_library_array("select id,machine_no from lib_machine_name", "id", "machine_no");
$floor_lib=return_library_array("select id,floor_id from lib_machine_name", "id", "floor_id");
$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );
$machine_sql="select dia_width,gauge,floor_id,machine_no,id from lib_machine_name where status_active=1 and is_deleted=0";
$machine_result=sql_select($machine_sql);
$machine_data_arr=array();
foreach ($machine_result as $row) {
	$machine_data_arr[$row[csf('id')]]['id']=$row[csf('id')];
	$machine_data_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
	$machine_data_arr[$row[csf('id')]]['gg']=$row[csf('gauge')];
	$machine_data_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
	$machine_data_arr[$row[csf('id')]]['machine_no']=$row[csf('machine_no')];
}

	foreach ($res as $row)
	{
		$mc_id_arr=explode(",",$row[csf("machine_id")]);
		foreach($mc_id_arr as $m_id)
		{
			$m_id=trim($m_id);

			if(!empty($cbo_floor_id))
			{
				if($cbo_floor_id==$floor_lib[$m_id])
				{
					if(count($machine_array))
					{
						if(in_array(trim($m_id),$machine_array))
						{
							$dtls_data[$m_id]["company_id"]=$row[csf("company_id")];
							$dtls_data[$m_id]["booking_no"]=$row[csf("booking_no")];
							$dtls_data[$m_id]["program_date"]=$row[csf("program_date")];
							$dtls_data[$m_id]["program_no"].=$row[csf("program_no")].",";
							$dtls_data[$m_id]["machine_id"]=$m_id;
							$dtls_data[$m_id]["machine_no"]=$machine_data_arr[$m_id]['machine_no'];
							$dtls_data[$m_id]["floor_id"]=$floor_arr[$floor_lib[$m_id]];
						}
					}else{
						$dtls_data[$m_id]["company_id"]=$row[csf("company_id")];
						$dtls_data[$m_id]["booking_no"]=$row[csf("booking_no")];
						$dtls_data[$m_id]["program_date"]=$row[csf("program_date")];
						$dtls_data[$m_id]["program_no"].=$row[csf("program_no")].",";
						$dtls_data[$m_id]["floor_id"]=$floor_arr[$floor_lib[$m_id]];
						$dtls_data[$m_id]["machine_id"]=$m_id;
						$dtls_data[$m_id]["machine_no"]=$machine_data_arr[$m_id]['machine_no'];
					}
				}
				

			}
			else
			{
				if(count($machine_array))
				{
					if(in_array(trim($m_id),$machine_array))
					{
						$dtls_data[$m_id]["company_id"]=$row[csf("company_id")];
						$dtls_data[$m_id]["booking_no"]=$row[csf("booking_no")];
						$dtls_data[$m_id]["program_date"]=$row[csf("program_date")];
						$dtls_data[$m_id]["program_no"].=$row[csf("program_no")].",";
						$dtls_data[$m_id]["floor_id"]=$floor_arr[$floor_lib[$m_id]];
						$dtls_data[$m_id]["machine_id"]=$m_id;
						$dtls_data[$m_id]["machine_no"]=$machine_data_arr[$m_id]['machine_no'];
					}
				}else{
					$dtls_data[$m_id]["company_id"]=$row[csf("company_id")];
					$dtls_data[$m_id]["booking_no"]=$row[csf("booking_no")];
					$dtls_data[$m_id]["program_date"]=$row[csf("program_date")];
					$dtls_data[$m_id]["program_no"].=$row[csf("program_no")].",";
					$dtls_data[$m_id]["floor_id"]=$floor_arr[$floor_lib[$m_id]];
					$dtls_data[$m_id]["machine_id"]=$m_id;
					$dtls_data[$m_id]["machine_no"]=$machine_data_arr[$m_id]['machine_no'];
				}

			}
			
			
			
		}
	}
	$floor_wise_data=array();
	function sortByMachineNo($a, $b) {
	    return $a['machine_no'] - $b['machine_no'];
	}

	usort($dtls_data, 'sortByMachineNo');
	foreach ($dtls_data as $machine_id => $data) {
		$floor_wise_data[$data['floor_id']][$data['machine_id']]['floor_name']=$data['floor_id'];
		$floor_wise_data[$data['floor_id']][$data['machine_id']]['company_id']=$data['company_id'];
		$floor_wise_data[$data['floor_id']][$data['machine_id']]['booking_no']=$data['booking_no'];
		$floor_wise_data[$data['floor_id']][$data['machine_id']]['program_date']=$data['program_date'];
		$floor_wise_data[$data['floor_id']][$data['machine_id']]['floor_id']=$data['floor_id'];
		$floor_wise_data[$data['floor_id']][$data['machine_id']]['machine_id']=$data['machine_id'];
		$floor_wise_data[$data['floor_id']][$data['machine_id']]['program_no']=$data['program_no'];
	}
	
	//echo count($floor_wise_data);

	//echo "<pre>";print_r($floor_wise_data); echo "</pre>";die;

	

//print_r($result);die;


$tbl_width=1100;
ob_start();
?>
<script type="text/javascript">
	 function program_wise_priority_save_update(operation){

		var total=$('#total_row').val();
		console.log(operation);
		var j = 0;
    	var dataString = '';
		for(var i=0;i<total;i++){
			 
			var priority = $('#priority_'+i).val();
			var machine_id = $('#machineId_'+i).val();
			var company_id = $('#companyId_'+i).val();
			var booking_no = $('#bookingNo_'+i).val();
			var program_id = $('#programId_'+i).val();
			var program_date = $('#programDate_'+i).val();

			 j++;

              dataString +='&companyId_' + j + '=' + company_id + '&bookingNo_' + j + '=' + booking_no + '&programDate_' + j + '=' + program_date + '&machineId_' + j + '=' + machine_id + '&programId_' + j + '=' + program_id + '&priority_' + j + '=' + priority ;
		}
        if (j < 1) {
            alert('No data');
            return;
        }

        
        var data = "action=save_update_delete&operation=" + operation + '&tot_row=' + j  + dataString;
		console.log(data);
		//return;
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/program_wise_priority_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = program_wise_priority_save_update_reponse;
		return;
		
	}

	function program_wise_priority_save_update_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			console.log(http.responseText);
			//release_freezing();
			//return;
			var reponse=trim(http.responseText).split('**');

			show_msg(reponse[0]);
			release_freezing();
			return;
		}
	}
</script>

<fieldset style="width:<? echo $tbl_width + 20; ?>px;">
	<table  border="1"  width="<? echo $tbl_width; ?>" class="rpt_table">
		<thead>
			<tr>
				<th  width="40" >Sl No</th>
				<th  width="100" >Date</th>
				<th  width="140" >Floor</th>
				<th  width="100" >M/C No</th>
				<th  width="70" >M/C Capacity</th>
				<th  width="70" >Program </th>
				<th  width="160" >Priority</th>
				<th  width="100" >Start Date</th>
				<th  width="70" >End Date</th>
				<th  width="70" >M/C Dia</th>
				<th   >M/C GG</th>
				
			</tr>
		</thead>
	</table>
		<?php  $sl_no=1; $c=0; $up_de=0;?>
	<table  border="1"  width="<? echo $tbl_width; ?>" id="scanning_tbl" class="rpt_table">
		<tbody>
			<?php
			foreach ($floor_wise_data as  $dtls_data) {
			
			foreach ($dtls_data  as $machine_id)
			{
				

				$key=$machine_id["machine_id"];
				 $programs=$machine_id["program_no"];
				 $company_id=$machine_id["company_id"];
				 $booking_no=$machine_id["booking_no"];
				//echo $programs;die;
				 $programs=chop($programs,',');
				$programs= explode(",", $programs);

				for($i=0;$i<count($programs);$i++)
				{
					$program_id=$programs[$i];
					$sql_program="select a.id ,b.start_date,b.end_date,b.capacity,c.within_group from ppl_planning_info_entry_dtls a,ppl_planning_info_entry_mst c ,ppl_planning_info_machine_dtls b where a.id=b.dtls_id and a.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and a.id=$program_id and b.machine_id=$key";
					//echo $sql_program;die;
					$res_program=sql_select($sql_program);
					//print_r($res_program);die;
					$within_group=$res_program[0][csf('within_group')];
					unset($sql_program);
					$program_date=$machine_id['program_date'];
					if ($db_type == 0) {
						$program_date = change_date_format($program_date, "yyyy-mm-dd", "");
						
					} else if ($db_type == 2) {
						$program_date = change_date_format($program_date, "", "", 1);
						
					}
					

					$priority_sql="select priority from program_wise_priority where booking_no='".$booking_no."' and company_id=$company_id and machine_id=$key and program_id=$program_id and program_date='".$program_date."'";
					//echo $priority_sql;die;
					
					$program_res=sql_select($priority_sql);
					$priority="";
					if(count($program_res)>0){
						 $priority=$program_res[0][csf('priority')];
						 $up_de++;
					}
					unset($priority_sql);
								


			 		if ($sl_no % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			 	?>
			 	
				
					<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl_no; ?>" style="text-align: center;">
						<td width="40"><? echo $sl_no++;?></td>
						<td  width="100"><?php echo change_date_format($machine_id["program_date"]) ?></td>
						<td width="140"><?php echo $floor_arr[$floor_lib[$key]]; ?></td>
						<td width="100"><? echo $machine_lib[$key];?> </td>
					
						
							<td width="70"><?php echo $res_program[0][csf('capacity')];?></td>
							<td width="70">
							<a href='##'
									onClick="generate_report2(<? echo $company_id . "," . $programs[$i].",".$within_group; ?>)"><?php echo $programs[$i];?> 
								</a>
							</td>
							<td width="160"><input type="text" name="priority[]" id="priority_<?php echo $c;?>" 
								 
									<?php if($priority==""){?>
										placeholder="Priority"
								<?php }else{?>
									value="<?php echo $priority;?>" 
								<?php } unset($program_res);	?>
								class="text_boxes"></td>

							<td width="100"><?php echo change_date_format($res_program[0][csf('start_date')]);?></td>
							<td width="70"><?php echo change_date_format($res_program[0][csf('end_date')]);?></td>
							
							<td width="70">
								<?php echo $machine_data_arr[$key]['dia']; ?>
								<input type="hidden" name="machine_id[]" value="<?php echo $key;?>" id="machineId_<?php echo $c;?>">
							<input type="hidden" name="program_date[]" value="<?php echo $machine_id['program_date'];?>" id="programDate_<?php echo $c;?>">
							<input type="hidden" name="booking_no[]" value="<?php echo $booking_no;?>" id="bookingNo_<?php echo $c;?>">
							<input type="hidden" name="company_id[]" value="<?php echo $company_id;?>" id="companyId_<?php echo $c;?>">
							<input type="hidden" name="program_id[]" value="<?php echo $programs[$i];?>" id="programId_<?php echo $c;?>">

							</td>
							<td>
								<?php echo $machine_data_arr[$key]['gg']; ?>
							</td>
							
							
							
						
						
					</tr>
					<?php 
							unset($res_program);
							$c++;

							
					}
				}

							
						
		       }

			?>
			<input type="hidden" name="total_row" value="<?php echo $c;?>" id="total_row">
			
		</tbody>


	</table>
	<?php 
			
			if($up_de>0)
			{
				$formbutton="formbutton";
			 
			}else{
				$formbutton="formbutton_disabled";
			}
			if($c>0){
				$save="formbutton";
			}else{
				$save="formbutton_disabled";
			}
			?>
			<table>
				<tr>
					<td><input type="button" class="<?php echo $save;?>" onClick="program_wise_priority_save_update(0)" style="width: 80px;" value="Save"></td>
					
					<td><input type="button" class="<?php echo $formbutton;?>"  onClick="program_wise_priority_save_update(1)" style="width: 80px;" value="Update" id="update_btn"></td>
					<td><input type="button" class="formbutton_disabled" onClick="program_wise_priority_save_update(2)" style="width: 80px;" value="Delete" id="delete_btn"
						></td>

				
				</tr>
			</table>
	

</fieldset>

	<script type="text/javascript">
		setFilterGrid('scanning_tbl',-1);
	</script>


<?php 

	exit();
}
if($action=="save_update_delete"){
	$process = array(&$_POST);
	//print_r($process);
	//echo "helal";die;
	//die;
	extract(check_magic_quote_gpc($process));
	if($operation==0)
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$field_array = "id, booking_no, priority, company_id,machine_id, program_id,program_date, inserted_by, insert_date";
		$data_array="";

		$id = return_next_id_by_sequence("program_wise_priority_seq", "program_wise_priority", $con);
		
		$rID = $rID2 = $ok=true;
		for ($j = 1; $j <= $tot_row; $j++) {
			
			$companyId="companyId_".$j;
			$bookingNo="bookingNo_".$j;
			$programDate="programDate_".$j;
			$machineId="machineId_".$j;
			$programId="programId_".$j;
			$priority="priority_".$j;
			if ($db_type == 0) {
				$programDate = change_date_format($$programDate, "yyyy-mm-dd", "");
				
			} else if ($db_type == 2) {
				$programDate = change_date_format($$programDate, "", "", 1);
				
			}
			$sql="select id,priority from program_wise_priority where booking_no='".$$bookingNo."' and company_id=".$$companyId." and machine_id=".$$machineId." and program_id=".$$programId." and program_date='".$programDate."'";
			//echo $sql;die;
			$result=sql_select($sql);
			unset($sql);
			if(count($result)==0)
			{	if($$priority!="undefined" && $$priority!=""){
					if ($data_array != "") $data_array .= ",";
					$data_array.= "(" . $id . ",'" . $$bookingNo . "','" . $$priority . "'," . $$companyId . "," . $$machineId . "," . $$programId .  ",'".$programDate. "',". $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$id = $id + 1;
				}
			}

			unset($result);
		}
		//echo $barcodeNos;die;

		//echo "10**insert into program_wise_priority (".$field_array.") values ".$data_array;die;
		//echo "10**insert into barcode_issue_to_finishing_mst (".$field_array_dtls.") values ".$data_array_dtls;die;
		//echo $rID2;
		//die;
		if($data_array!=""){
			$rID = sql_insert("program_wise_priority", $field_array, $data_array, 0);
		}
		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$statusUsed;die;

		if ($db_type == 0) {
			if ($rID ) {
				mysql_query("COMMIT");
				echo "0**" . $id;
			} else {
				mysql_query("ROLLBACK");
				echo "5**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID ) {
				oci_commit($con);
				echo "0**" . $id ;
			} else {
				oci_rollback($con);
				echo "5**0";
			}
		}
		//check_table_status($_SESSION['menu_id'], 0);
		disconnect($con);
		die;
		exit();
	}
	else if($operation==1)
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$rID2 =true;
		for ($j = 1; $j <= $tot_row; $j++) {
			
			$companyId="companyId_".$j;
			$bookingNo="bookingNo_".$j;
			$programDate="programDate_".$j;
			$machineId="machineId_".$j;
			$programId="programId_".$j;
			$priority="priority_".$j;
			if ($db_type == 0) {
				$programDate = change_date_format($$programDate, "yyyy-mm-dd", "");
				
			} else if ($db_type == 2) {
				$programDate = change_date_format($$programDate, "", "", 1);
				
			}
			$sql="select id,priority from program_wise_priority where booking_no='".$$bookingNo."' and company_id=".$$companyId." and machine_id=".$$machineId." and program_id=".$$programId." and program_date='".$programDate."'";
			//echo $sql;die;
			$result=sql_select($sql);
			unset($sql);
			if(count($result)>0)
			{	

				$data_array_status ="".$_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'".$$priority."'";
				$response = sql_update("program_wise_priority", 'updated_by*update_date*priority', $data_array_status, "id", $result[0][csf('id')], 0);
				if ($response==false) {
					$rID2=false;
					if ($db_type == 0) {
						
						mysql_query("ROLLBACK");
						echo "6**0";
						
					} else if ($db_type == 2 || $db_type == 1) {
						oci_rollback($con);
						echo "6**0";
						
					}
					eixt();
				} 
				
			}
			unset($result);
		}

		if ($db_type == 0) {
			if ($rID2 ) {
				mysql_query("COMMIT");
				echo "1**0";
			} else {
				mysql_query("ROLLBACK");
				echo "6**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID2 ) {
				oci_commit($con);
				echo "1**0" ;
			} else {
				oci_rollback($con);
				echo "6**0";
			}
		}
		//check_table_status($_SESSION['menu_id'], 0);
		disconnect($con);
		die;
		exit();
	}
	else{
		echo "5**1";
		die;
		exit();
	}
	
}
/*
if ($action == "print")
{
		extract($_REQUEST);
		$data = explode('*', $data);
		$company_id = $data[0];
		$program_id = $data[1];
		$path = $data[2];
		//echo $path;die;
		echo load_html_head_contents("Program Qnty Info", '../../../', 1, 1, '', '', '');
		//echo $company_id;die;

		$company_details = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
		$supllier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", 'id', 'supplier_name');
		$country_arr = return_library_array("select id, country_name from lib_country where status_active=1 and is_deleted=0", 'id', 'country_name');
		$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
		$brand_arr = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0", 'id', 'brand_name');
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
		$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", "id", "machine_no");
		$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

		$sales_info = sql_select("select a.job_no, a.style_ref_no,location_id  from fabric_sales_order_mst a, ppl_planning_entry_plan_dtls b where a.id=b.po_id and b.dtls_id = $program_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$product_details_array = array();
		$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0";
		$result = sql_select($sql);

		foreach ($result as $row) {
			$compos = '';
			if ($row[csf('yarn_comp_percent2nd')] != 0) {
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
			} else {
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
			}

			$product_details_array[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
			$product_details_array[$row[csf('id')]]['comp'] = $compos;
			$product_details_array[$row[csf('id')]]['type'] = $yarn_type[$row[csf('yarn_type')]];
			$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
			$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
			$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
		}

		?>
		<div style="width:860px">
			<div style="margin-left:20px; width:850px">
				<div style="width:100px;float:left;position:relative;margin-top:10px">
					<? $image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$company_id' and form_name='company_details' and is_deleted=0"); ?>
					<img src="<? echo $path . $image_location; ?>" height='100%' width='100%'/>
				</div>
				<div style="width:50px;float:left;position:relative;margin-top:10px"></div>
				<div style="float:left;position:relative;">
					<table width="100%" style="margin-top:10px; font-family: tahoma;">
						<tr>
							<td align="center" style="font-size:16px;">
								<? echo $company_details[$company_id]; ?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id and status_active=1 and is_deleted=0");
								foreach ($nameArray as $result) {
									?>
									Plot No: <? echo $result['plot_no']; ?>
									Level No: <? echo $result['level_no'] ?>
									Road No: <? echo $result['road_no']; ?>
									Block No: <? echo $result['block_no']; ?>
									City No: <? echo $result['city']; ?>
									Zip Code: <? echo $result['zip_code']; ?>
									Country: <? echo $country_arr[$result['country_id']]; ?><br>
									Email Address: <? echo $result['email']; ?>
									Website No: <? echo $result['website'];
								}
								?>
							</td>
						</tr>
						<tr>
							<td height="10"></td>
						</tr>
						<tr>
							<td width="100%" align="center" style="font-size:14px;"><b><u>Knitting Program</u></b></td>
						</tr>
					</table>
				</div>
			</div>
			<div style="margin-left:10px;float:left; width:850px;">
				<?
				$dataArray = sql_select("select id, mst_id, knitting_source, knitting_party, program_date, color_range, stitch_length, machine_dia, machine_gg, program_qnty, machine_id, remarks, location_id, advice, feeder, width_dia_type, color_id,fabric_dia from ppl_planning_info_entry_dtls where id=$program_id and status_active=1 and is_deleted=0");


				if ($dataArray[0][csf('knitting_source')] == 1) {

					$location = return_field_value("location_name", "lib_location", "id='" . $dataArray[0][csf('location_id')] . "'");
				}
				else if($dataArray[0][csf('knitting_source')] == 3)
				{
					$location = return_field_value("location_name", "lib_location", "id='" . $sales_info[0][csf('location_id')] . "'");
				}

				$advice = $dataArray[0][csf('advice')];

				$mst_dataArray = sql_select("select booking_no, buyer_id, fabric_desc, gsm_weight, dia, within_group from ppl_planning_info_entry_mst where status_active=1 and is_deleted=0 and id=" . $dataArray[0][csf('mst_id')]);
				$booking_no = $mst_dataArray[0][csf('booking_no')];
				$buyer_id = $mst_dataArray[0][csf('buyer_id')];
				$fabric_desc = $mst_dataArray[0][csf('fabric_desc')];
				$gsm_weight = $mst_dataArray[0][csf('gsm_weight')];
				$dia = $mst_dataArray[0][csf('dia')];
				$within_group = $mst_dataArray[0][csf('within_group')];

				?>
				&nbsp;&nbsp;<b>Attention- Knitting Manager</b>
				<table width="100%" style="margin-top:5px; font-family: tahoma;" cellspacing="7">
					<tr>
						<td width="140"><b>Program No:</b></td>
						<td width="170"><? echo $dataArray[0][csf('id')]; ?></td>
						<td width="170"><b>Program Date:</b></td>
						<td><? echo change_date_format($dataArray[0][csf('program_date')]); ?></td>
					</tr>
					<tr>
						<td><b>Factory:</b></td>
						<td>
							<?
							if ($dataArray[0][csf('knitting_source')] == 1) echo $company_details[$dataArray[0][csf('knitting_party')]];
							else if ($dataArray[0][csf('knitting_source')] == 3) echo $supllier_arr[$dataArray[0][csf('knitting_party')]];
							?>
						</td>
						<td><b>Fabrication & FGSM:</b></td>
						<td><? echo $fabric_desc . " & " . $gsm_weight; ?></td>
					</tr>
					<tr>
						<td><b>Address:</b></td>
						<td colspan="3">
							<?
							$address = '';
							if ($dataArray[0][csf('knitting_source')] == 1) {
								$addressArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,city from lib_company where id=$company_id and status_active=1 and is_deleted=0");
								foreach ($nameArray as $result) {
									?>
									Plot No: <? echo $result[csf('plot_no')]; ?>
									Road No: <? echo $result[csf('road_no')]; ?>
									Block No: <? echo $result[csf('block_no')]; ?>
									City No: <? echo $result[csf('city')];
								}
							} else if ($dataArray[0][csf('knitting_source')] == 3) {
								$address = return_field_value("address_1", "lib_supplier", "id=" . $dataArray[0][csf('knitting_party')]);
								echo $address;
							}

							$machine_no = '';
							$machine_id = explode(",", $dataArray[0][csf("machine_id")]);
							foreach ($machine_id as $val) {
								if ($machine_no == '') $machine_no = $machine_arr[$val]; else $machine_no .= "," . $machine_arr[$val];
							}

							if ($within_group == 1) {
								$buyer = $company_details[$buyer_id];
								$booking_buyer = return_field_value("buyer_id", "wo_booking_mst", "booking_no='" . $booking_no . "'");
								$customers_buyer = $buyer_arr[$booking_buyer];
							} else {
								$buyer = $buyer_arr[$buyer_id];
								$customers_buyer = $buyer_arr[$buyer_id];
							}
							?>
						</td>
					</tr>
					<tr>
						<td><b>PO Company:</b></td>
						<td><b><? echo $buyer; ?></b></td>
						<td><b>PO Buyer:</b></td>
						<td><b><? echo $customers_buyer; ?></b></td>
					</tr>
					<tr>
						<td><b>Sales Order No:</b></td>
						<td><b><? echo $sales_info[0]["JOB_NO"]; ?></b></td>
						<td><b>Fabric/Booking No:</b></td>
						<td><b><? echo $booking_no; ?></b></td>
					</tr>
					<tr>
						<td><b>Style Ref.:</b></td>
						<td><b><? echo $sales_info[0]["STYLE_REF_NO"]; ?></b></td>
						<td><b>Location:</b></td>
						<td><b><? echo $location; ?></b></td>
					</tr>
					<tr>
						<td><b>Machine No:</b></td>
						<td><b><? echo $machine_no; ?></b></td>
					</tr>
				</table>

				<table style="margin-top:10px; font-family: tahoma;" width="850" border="1" rules="all" cellpadding="0"
				cellspacing="0" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="70">Requisition No</th>
					<th width="70">Lot No</th>
					<th width="225">Yarn Description</th>
					<th width="110">Brand</th>
					<th width="80">Requisition Qnty</th>
					<th width="120">Yarn Color</th>
					<th>Remarks</th>
				</thead>
				<?
				$i = 1;
				$tot_reqsn_qnty = 0;
				$sql = "select requisition_no, prod_id, yarn_qnty from ppl_yarn_requisition_entry where knit_id='" . $dataArray[0][csf('id')] . "' and status_active=1 and is_deleted=0";
				$nameArray = sql_select($sql);
				foreach ($nameArray as $selectResult) {
					?>
					<tr>
						<td align="center"><? echo $i; ?></td>
						<td align="center"><p><? echo $selectResult[csf('requisition_no')]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?>
					&nbsp;</p></td>
					<td>
						<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?>
					&nbsp;</p></td>
					<td><p><? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?>&nbsp;</p></td>
					<td align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?></td>
					<td><p>
						&nbsp;&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['color']; ?></p>
					</td>
					<td>&nbsp;</td>
				</tr>
				<?
				$tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
				$i++;
			}
			?>
			<tfoot>
				<th colspan="5" align="right"><b>Total</b></th>
				<th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
		<table width="850" cellpadding="0" cellspacing="0" border="1" rules="all"
		style="margin-top:20px; font-family: tahoma;" class="rpt_table">
		<tr>
			<td width="120"><b>Colour Range:</b></td>
			<td width="150"><p><? echo $color_range[$dataArray[0][csf('color_range')]]; ?>&nbsp;</p></td>
			<td width="120"><b>GGSM OR S/L:</b></td>
			<td width="150"><p><? echo $dataArray[0][csf('stitch_length')]; ?>&nbsp;</p></td>
			<td width="120"><b>FGSM:</b></td>
			<td><p><? echo $gsm_weight; ?>&nbsp;</p></td>
		</tr>
		<tr>
			<td><b>Finish Dia</b></td>
			<td>
				<p><? echo $dataArray[0][csf('fabric_dia')] . "  (" . $fabric_typee[$dataArray[0][csf('width_dia_type')]] . ")"; ?>
			&nbsp;</p></td>
			<td><b>Machine Dia & Gauge:</b></td>
			<td><p><? echo $dataArray[0][csf('machine_dia')] . "X" . $dataArray[0][csf('machine_gg')]; ?>
		&nbsp;</p></td>
		<td><b>Program Qnty:</b></td>
		<td><p><? echo number_format($dataArray[0][csf('program_qnty')], 2); ?>&nbsp;</p></td>
		</tr>
		<tr>
		<td><b>Feeder:</b></td>
		<td><p>
			<?
			$feeder_array = array(1 => "Full Feeder", 2 => "Half Feeder");
			echo $feeder_array[$dataArray[0][csf('feeder')]];
			?>&nbsp;</p></td>
			<td><b>Garments Color</b></td>
			<td><p>
				<?
				$color_id_arr = array_unique(explode(",", $dataArray[0][csf('color_id')]));
				$all_color = "";
				foreach ($color_id_arr as $color_id) {
					$all_color .= $color_library[$color_id] . ",";
				}
				$all_color = chop($all_color, ",");
				echo $all_color;

				?>&nbsp;</p></td>
				<td><b>Remarks</b></td>
				<td><p><? echo $dataArray[0][csf('remarks')]; ?>&nbsp;</p></td>
			</tr>
		</table>
		<?
		$sql_fedder = sql_select("select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.dtls_id=$program_id and a.no_of_feeder>0 group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder order by a.id");
		if (count($sql_fedder) > 0) {
			?>
			<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
			class="rpt_table">
			<thead>
				<tr>
					<th width="50">SL</th>
					<th width="200">Color</th>
					<th width="200">Stripe Color</th>
					<th width="120">Measurement</th>
					<th width="120">UOM</th>
					<th>No Of Feeder</th>
				</tr>
			</thead>
			<tbody>
				<?
				$i = 1;
				$total_feeder = 0;
				foreach ($sql_fedder as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					?>
					<tr>
						<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
						<td><p><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</p></td>
						<td><p><? echo $color_library[$row[csf('stripe_color_id')]]; ?>&nbsp;</p></td>
						<td align="right"><p><? echo number_format($row[csf('measurement')], 2); ?>&nbsp;</p></td>
						<td align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
						<td align="right"><p><? echo number_format($row[csf('no_of_feeder')], 0);
						$total_feeder += $row[csf('no_of_feeder')]; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th align="right">Total:</th>
					<th align="right"><? echo number_format($total_feeder, 0); ?></th>
				</tr>
			</tfoot>
		</table>
		<?
		}
		?>

		<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
		class="rpt_table">
			<tr>
				<td colspan="4" style="word-wrap:break-word"><b>Advice:</b> <strong><? echo $advice; ?></strong>
				</td>
			</tr>
		</table>

	<table width="850" style="display:none">
		<tr>
			<td width="100%" height="90" colspan="4"></td>
		</tr>
		<tr>
			<td width="25%" align="center"><strong style="text-decoration:overline">Checked By</strong></td>
			<td width="25%" align="center"><strong style="text-decoration:overline">Receive By</strong></td>
			<td width="25%" align="center"><strong style="text-decoration:overline">Knitting Manager</strong>
			</td>
			<td width="25%" align="center"><strong style="text-decoration:overline">Authorised By</strong></td>
		</tr>
	</table>
	<br>
	<? echo signature_table(100, $company_id, "850px"); ?>
	</div>
	</div>
	<?
	exit();
}
*/


if ($action == "requisition_print_two") {
	extract($_REQUEST);
	//echo $data;die;
	$data = explode('**', $data);
	echo load_html_head_contents("Program Qnty Info", "../../../", 1, 1, '', '', '');

	$typeForAttention = $data[1];
	$program_ids = $data[0];
	$within_group = $data[3];
	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name'); 
    $yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by id, yarn_count","id","yarn_count");

	$po_dataArray = sql_select("select id,job_no,buyer_id,style_ref_no,within_group,sales_booking_no from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	//print_r($po_dataArray);
	foreach ($po_dataArray as $row) {
        $sales_array[$row[csf('id')]]['no'] = $row[csf('job_no')];
		$sales_array[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
		$sales_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
		$sales_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$sales_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
	}
	


	$book_dataArray = sql_select("select a.buyer_id,b.booking_no,b.po_break_down_id as po_id,b.job_no,c.po_number,d.style_ref_no from wo_booking_mst a,wo_booking_dtls b, wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	
	foreach ($book_dataArray as $row) {
		$booking_array[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
		$booking_array[$row[csf('booking_no')]]['po_id'] = $row[csf('po_id')];
		$booking_array[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
		$booking_array[$row[csf('booking_no')]]['po_no'] = $row[csf('po_number')];
		$booking_array[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
		$booking_array[$row[csf('booking_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
	}
	
	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row) {
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}
		$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}

	$knit_id_array = array();
	$prod_id_array = array();
	$rqsn_array = array();
	$reqsn_dataArray = sql_select("select knit_id, requisition_no,requisition_date,prod_id,sum(no_of_cone) as no_of_cone , sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where knit_id in($program_ids) and status_active=1 and is_deleted=0 group by knit_id, prod_id, requisition_no,requisition_date");

	foreach ($reqsn_dataArray as $row) {
		$prod_id_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
		$knit_id_array[$row[csf('knit_id')]] .= $row[csf('prod_id')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsn'] .= $row[csf('requisition_no')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsd'] .= $row[csf('requisition_date')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['qnty'] += $row[csf('yarn_qnty')];
		$rqsn_array[$row[csf('prod_id')]]['no_of_cone'] += $row[csf('no_of_cone')];
	}

	$sales_order_no = '';
	$buyer_name = '';
	$knitting_factory = '';
    $booking_no = '';
	$wg_yes_booking = '';
	$company = '';
	$order_buyer = '';
	$style_ref_no = '';
	if ($db_type == 0) {
		$dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, b.buyer_id,b.within_group, b.booking_no, b.company_id, group_concat(distinct(b.po_id)) as po_id,a.is_sales from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id,a.is_sales,b.within_group");
	} else {
		$dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id, LISTAGG(cast(b.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_id) as po_id,a.is_sales,b.within_group from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id,a.is_sales,b.within_group");
	}

	$k_source = "";
	$sup = $sales_ids = "";

	foreach ($dataArray as $row) {
		if ($duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] == "") {
			$duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] = $row[csf('knitting_party')];
		}
		if ($row[csf('knitting_source')] == 1) {
			$knitting_factory .= $company_details[$row[csf('knitting_party')]] . ",";
		} else if ($row[csf('knitting_source')] == 3) {
			$knitting_factory .= $supplier_details[$row[csf('knitting_party')]] . ",";
		}
        $knitting_factory=implode(",",array_unique(explode(",",$knitting_factory)));

		if ($buyer_name == "") {
			if($row[csf('within_group')]==1)
			{
				$buyer_name = $company_details[$row[csf('buyer_id')]];
			}else{

				$buyer_name = $buyer_arr[$row[csf('buyer_id')]];
			}
		}
		if ($booking_no != '') {
			$booking_no .= "," . $row[csf('booking_no')];
		} else {
			$booking_no = $row[csf('booking_no')];
		}

		if ($company == "") {
			$company = $company_details[$row[csf('company_id')]];
		}
		if ($company_id == "") {
			$company_id = $row[csf('company_id')];
		}
        $order_nos .= "," . $booking_array2[$row[csf('booking_no')]]['po_no'];
		$is_sales = $row[csf('is_sales')];
		$sales_ids .= $row[csf('po_id')] . ",";
		$k_source = $row[csf('knitting_source')];
		$sup = $row[csf('knitting_party')];
	}
    $sales_id = array_unique(explode(",", $sales_ids));
    $booking_nos = array_unique(explode(",", $booking_no));

    $order_buyer=$style_ref_no=$job_no=$order_nos="";
	foreach ($sales_id as $pid) {
		$sales_order_no .= $sales_array[$pid]['no'] . ","; 
		if($sales_array[$pid]['within_group'] == 1)
		{
			$order_buyer .= $company_details[$sales_array[$pid]['buyer_id']] . ",";
		}
        else if ($sales_array[$pid]['within_group'] == 2) {
            $order_buyer .= $buyer_arr[$sales_array[$pid]['buyer_id']] . ",";
            $style_ref_no .= "," . $sales_array[$pid]['style_ref_no'];
            $job_no .= "";
            $order_ids .= "";
        }else{
            $order_buyer .= $buyer_arr[$booking_array[$sales_array[$pid]['sales_booking_no']]['buyer_id']] . ",";
            $style_ref_no .= "," . $booking_array[$sales_array[$pid]['sales_booking_no']]['style_ref_no'];
            $job_no .= $booking_array[$sales_array[$pid]['sales_booking_no']]['job_no'] . ",";
            $order_ids .= $booking_array[$sales_array[$pid]['sales_booking_no']]['po_no'] . ",";
        }
	}
    $sales_nos = rtrim(implode(",", array_unique(explode(",", $sales_order_no))), ",");
	$order_buyers = rtrim(implode(",", array_unique(explode(",", $order_buyer))), ",");
	$style_ref_nos = ltrim(implode(",", array_unique(explode(",", $style_ref_no))), ",");
	$job_nos = implode(",", array_unique(explode(",", rtrim($job_no,","))));
	$booking_noss = implode(",", $booking_nos);

    if($program_ids!="")
    {
        $feedingResult =  sql_select("SELECT dtls_id, seq_no, count_id, feeding_id FROM ppl_planning_count_feed_dtls WHERE dtls_id in($program_ids) and status_active=1 and is_deleted=0");

        $feedingDataArr = array();
        foreach ($feedingResult as $row) {
            $feedingSequence[$row[csf('seq_no')]] =  $row[csf('seq_no')];
            $feedingDataArr[$row[csf('dtls_id')]][$row[csf('seq_no')]]['count_id'] = $row[csf('count_id')];
            $feedingDataArr[$row[csf('dtls_id')]][$row[csf('seq_no')]]['feeding_id'] = $row[csf('feeding_id')];  
        }
    }

	?>
    <div style="width:1200px; margin-left:5px;">
        <table width="100%" style="margin-top:10px">
            <tr>
                <td width="100%" align="center" style="font-size:20px;"><b><? echo $company; ?></b></td>
            </tr>
            <tr>
                <td align="center" style="font-size:14px">
                 <?
                 echo show_company($company_id, '', '');
                 ?>
             </td>
         </tr>
         <tr>
            <td width="100%" align="center" style="font-size:20px;"><b><u>Knitting Program</u></b></td>
        </tr>
    </table>
    <div style="margin-top:10px; width:950px">
        <table width="100%" cellpadding="2" cellspacing="5">
            <tr>
                <td width="140"><b style="font-size:18px">Knitting Factory </b></td>
                <td>:</td>
                <td style="font-size:18px"><b><? echo substr($knitting_factory, 0, -1); ?></b></td>
            </tr>
            <tr>
                <td width="140" style="font-size:18px"><b>Attention </b></td>
                <td>:</td>
                <?
                if ($typeForAttention == 1) {
                  echo "<td style=\"font-size:18px; font-weight:bold;\"> Knitting Manager </td>";
              } else {
                  ?>
                  <td style="font-size:18px; font-weight:bold;"><b><?
                    if ($k_source == 3) {
                     $ComArray = sql_select("select id,contact_person from lib_supplier where id=$sup");
                     foreach ($ComArray as $row) {
                      echo $row[csf('contact_person')];
                  }
              } else {

                 echo "";
             }
             ?></b></td>
             <? } ?>
         </tr>
         <tr>
            <td><b>Buyer Name </b></td>
            <td>:</td>
            <td><? echo $order_buyers; ?></td>
        </tr>
        <tr>
            <td><b>Style </b></td>
            <td>:</td>
            <td><? echo $style_ref_nos; ?></td>
        </tr>
        <tr>
            <td><b>Order No </b></td>
            <td>:</td>
            <td><? echo rtrim($order_ids,","); ?></td>
        </tr>
        <tr>
            <td><b>Job No </b></td>
            <td>:</td>
            <td><? echo $job_nos; ?></td>
        </tr>
        <tr>
            <td><b>Booking No </b></td>
            <td>:</td>
            <td><? echo $booking_noss; ?></td>
        </tr>
        <tr>
            <td><b>Sales Order No </b></td>
            <td>:</td>
            <td><? echo $sales_nos; ?></td>
        </tr>
    </table>
</div>
<table width="1050" style="margin-top:10px" border="1" rules="all" cellpadding="0" cellspacing="0"
class="rpt_table">
<thead>
    <th width="30">SL</th>
    <th width="100">Requisition No</th>
    <th width="100">Requisition Date</th>
    <th width="100">Brand</th>
    <th width="100">Lot No</th>
    <th width="200">Yarn Description</th>
    <th width="100">Color</th>
    <th width="100">Requisition Qty.</th>
    <th>No Of Cone</th>
</thead>
<?
$j = 1;
$tot_reqsn_qty = 0;
foreach ($rqsn_array as $prod_id => $data) {
    if ($j % 2 == 0)
     $bgcolor = "#E9F3FF";
 else
     $bgcolor = "#FFFFFF";
 ?>
 <tr bgcolor="<? echo $bgcolor; ?>">
    <td width="30"><? echo $j; ?></td>
    <td width="100"><? echo substr($data['reqsn'], 0, -1); ?></td>
    <td width="100"><? echo substr($data['reqsd'], 0, -1); ?></td>
    <td width="100"><p><? echo $product_details_array[$prod_id]['brand']; ?>&nbsp;</p></td>
    <td width="100"><p><? echo $product_details_array[$prod_id]['lot']; ?></p></td>
    <td width="200"><p><? echo $product_details_array[$prod_id]['desc']; ?></p>
    </th>
    <td width="100"><p><? echo $product_details_array[$prod_id]['color']; ?>&nbsp;</p></td>
    <td width="100" align="right"><p><? echo number_format($data['qnty'], 2, '.', ''); ?></p></td>
    <td align="right"><? echo number_format($data['no_of_cone']); ?></td>
</tr>
<?
$tot_reqsn_qty += $data['qnty'];
$tot_no_of_cone += $data['no_of_cone'];
$j++;
}
?>
<tfoot>
    <th colspan="7" align="right">Total</th>
    <th align="right"><? echo number_format($tot_reqsn_qty, 2, '.', ''); ?></th>
    <th><? echo number_format($tot_no_of_cone); ?></th>
</tfoot>
</table>

<table style="margin-top:10px;" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0"
class="rpt_table" align="center">
<thead align="center">
    <th width="25">SL</th>
    <th width="50">Program No</th>
    <th width="120">Fabrication</th>
    <th width="50">GSM</th>
    <th width="40">F. Dia</th>
    <th width="60">Dia Type</th>

    <th width="45">Floor</th>

    <th width="45">M/c. No</th>
    <th width="50">M/c. Dia & GG</th>
    <th width="100">Color</th>
    <th width="60">Color Range</th>
    <th width="50">S/L</th>
    <th width="50">Spandex S/L</th>
    <th width="50">Feeder</th>
    <th width="100">Count Feeding</th>
    <th width="70">Knit Start</th>
    <th width="70">Knit End</th>
    <th width="70">Prpgram Qty.</th>
    <th width="110">Yarn Description</th>
    <th width="50">Lot</th>
    <th width="70">Yarn Qty.(KG)</th>
    <th>Remarks</th>

</thead>
<?
$i = 1;
$s = 1;
$tot_program_qnty = 0;
$tot_yarn_reqsn_qnty = 0;
$company_id = '';
$sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks,b.advice from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

$nameArray = sql_select($sql);

$advice = "";
foreach ($nameArray as $row) {
    if ($i % 2 == 0)
     $bgcolor = "#E9F3FF";
    else
     $bgcolor = "#FFFFFF";

    $color = '';
    $color_id = explode(",", $row[csf('color_id')]);

    foreach ($color_id as $val) {
    if ($color == '')
      $color = $color_library[$val];
    else
      $color .= "," . $color_library[$val];
    }

    if ($company_id == '')
    $company_id = $row[csf('company_id')];

    $machine_no = '';
    $machine_id = explode(",", $row[csf('machine_id')]);

    foreach ($machine_id as $val) {
     if ($machine_no == '')
      $machine_no = $machine_arr[$val];
    else
      $machine_no .= "," . $machine_arr[$val];
    }
    if ($machine_id[0] != "") {
     $sql_floor = sql_select("select id, machine_no, floor_id from lib_machine_name where id=$machine_id[0] and status_active=1 and is_deleted=0  order by seq_no");
    }

    $count_feeding = "";
    foreach($feedingDataArr[$row[csf('program_id')]] as $feedingSequence=>$feedingData)
    {
        if($count_feeding =="")
        {   
            $count_feeding = $feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];                                                
        } 
        else 
        {
            $count_feeding .= ",".$feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
        }
    }

    if ($knit_id_array[$row[csf('program_id')]] != "") {
    $all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
    $row_span = count($all_prod_id);
    $z = 0;

 foreach ($all_prod_id as $prod_id) 
 {
    ?>
    <tr bgcolor="<? echo $bgcolor; ?>">
        <?
        if ($z == 0) 
        {
            ?>
            <td width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
            <td width="60" rowspan="<? echo $row_span; ?>" align="center" style="font-size:16px;">
            <b><? echo $row[csf('program_id')]; ?></b></td>
            <td width="120" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
            <td width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
            <td width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
            <td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>
            <td width="60" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>
            <td width="60" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $machine_no; ?></p></td>
            <td width="70" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
            <td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color; ?></p></td>
            <td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
            <td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('stitch_length')]; ?></p></td>
            <td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
            <td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
            <td width="100" rowspan="<? echo $row_span; ?>"><p><? echo $count_feeding; ?></p></td>
            <td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
            <td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
            <td width="70" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?></td>
            <?
            $tot_program_qnty += $row[csf('program_qnty')];
                $i++;
        }
        ?>
        <td width="110"><p><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</p></td>
        <td width="50" align="center"><p><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</p></td>
        <td width="70" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id], 2, '.', ''); ?></td>
        <?
        if ($z == 0) {
            ?>
            <td rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
            <?
        }
        ?>
    </tr>
        <?
        $tot_yarn_reqsn_qnty += $prod_id_array[$row[csf('program_id')]][$prod_id];
        $z++;
    }
} else {
 ?>
 <tr bgcolor="<? echo $bgcolor; ?>">
    <td width="25"><? echo $i; ?></td>
    <td width="60" align="center" style="font-size:16px;"><b><? echo $row[csf('program_id')]; ?></b></td>
    <td width="120"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
    <td width="50" align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
    <td width="50" align="center"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
    <td width="60"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>
    <td width="50" align="center"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>
    <td width="50" align="center"><p><? echo $machine_no; ?></p></td>
    <td width="50"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
    <td width="50"><p><? echo $color; ?></p></td>
    <td width="60"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
    <td width="60"><p><? echo $row_span; ?><p><? echo $row[csf('stitch_length')]; ?></p></td>
    <td width="60"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
    <td width="70"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
    <td width="100" rowspan="<? echo $row_span; ?>"><p><? echo $count_feeding; ?></p></td>
    <td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
    <td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
    <td width="70" align="right"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?></td>
    <td width="110"><p>&nbsp;</p></td>
    <td width="50"><p>&nbsp;</p></td>
    <td width="70" align="right">&nbsp;</td>
    <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
</tr>
<?
$tot_program_qnty += $row[csf('program_qnty')];
$i++;
}
$advice = $row[csf('advice')];
}
?>
<tfoot>
    <th colspan="17" align="right"><b>Total</b></th>
    <th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?>&nbsp;</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>

    <th align="right"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
    <th>&nbsp;</th>
    
</tfoot>

</table>
<br>
<?
$sql_collarCuff = sql_select("select id, body_part_id, grey_size, finish_size, qty_pcs from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id in($program_ids) order by id");
if (count($sql_collarCuff) > 0) {
   ?>
   <table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
   class="rpt_table">
   <thead>
    <tr>
        <th width="50">SL</th>
        <th width="200">Body Part</th>
        <th width="200">Grey Size</th>
        <th width="200">Finish Size</th>
        <th>Quantity Pcs</th>
    </tr>
</thead>
<tbody>
    <?
    $i = 1;
    $total_qty_pcs = 0;
    foreach ($sql_collarCuff as $row) {
     if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
     ?>
     <tr>
        <td align="center"><p><? echo $i; ?>&nbsp;</p></td>
        <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</p></td>
        <td style="padding-left:5px"><p><? echo $row[csf('grey_size')]; ?>&nbsp;</p></td>
        <td style="padding-left:5px"><p><? echo $row[csf('finish_size')]; ?>&nbsp;</p></td>
        <td align="right"><p><? echo number_format($row[csf('qty_pcs')], 0);
            $total_qty_pcs += $row[csf('qty_pcs')]; ?>&nbsp;&nbsp;</p></td>
        </tr>
        <?
        $i++;
    }
    ?>
</tbody>
<tfoot>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th align="right">Total</th>
        <th align="right"><? echo number_format($total_qty_pcs, 0); ?>&nbsp;</th>
    </tr>
</tfoot>
</table>
<?
}
?>
<br>

<?
$sql_strip = "select a.color_number_id, a.stripe_color, a.measurement, a.uom, b.dtls_id, b.no_of_feeder as no_of_feeder from wo_pre_stripe_color a, ppl_planning_feeder_dtls b where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_id and a.color_number_id=b.color_id and a.stripe_color=b.stripe_color_id and b.dtls_id in($program_ids) and b.no_of_feeder>0 and a.status_active=1 and a.is_deleted=0";
$result_stripe = sql_select($sql_strip);
if (count($result_stripe) > 0) {
   ?>
   <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
    <thead>
        <tr>
            <th colspan="7">Stripe Measurement</th>
        </tr>
        <tr>
            <th width="30">SL</th>
            <th width="60">Prog. no</th>
            <th width="140">Color</th>
            <th width="130">Stripe Color</th>
            <th width="70">Measurement</th>
            <th width="50">UOM</th>
            <th>No Of Feeder</th>
        </tr>
    </thead>
    <?
    $i = 1;
    $tot_feeder = 0;
    foreach ($result_stripe as $row) {
     if ($i % 2 == 0)
      $bgcolor = "#E9F3FF";
  else
      $bgcolor = "#FFFFFF";
  $tot_feeder += $row[csf('no_of_feeder')];
  ?>
  <tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
    <td width="30" align="center"><? echo $i; ?></td>
    <td width="50" align="center"><? echo $row[csf('dtls_id')]; ?></td>
    <td width="140"><p><? echo $color_library[$row[csf('color_number_id')]]; ?></p></td>
    <td width="130"><p><? echo $color_library[$row[csf('stripe_color')]]; ?></p></td>
    <td width="70" align="center"><? echo $row[csf('measurement')]; ?></td>
    <td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
    <td align="right" style="padding-right:10px"><? echo $row[csf('no_of_feeder')]; ?>&nbsp;</td>
</tr>
<?
$tot_masurement += $row[csf('measurement')];
$i++;
}
?>
</tbody>
<tfoot>
    <th colspan="4">Total</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
    <th style="padding-right:10px"><? echo $tot_feeder; ?>&nbsp;</th>
</tfoot>

</table>

<?
}
?>
<table border="1" rules="all" class="rpt_table">
    <tr>
        <td style="font-size:24px; font-weight:bold; width:20px;">ADVICE:</td>
        <td style="font-size:20px; width:100%;">     <? echo $advice; ?></td>
    </tr>
</table>
<div style="margin-top:60px; text-align: left;"><strong>Rate/Kg =</strong></div>
<br/>
<div style="float:left; border:1px solid #000;">
    <table border="1" rules="all" class="rpt_table" width="400" height="200">
        <thead>
            <th colspan="2" style="font-size:20px; font-weight:bold;">Please Strictly Avoid The Following Faults….
            </th>
            <thead>
                <tbody>
                    <tr>
                        <td style="width:190px; font-size:14px;"><b> 1.</b> Patta</td>
                        <td style="font-size:14px;"><b> 8.</b> Sinker mark</td>
                    </tr>
                    <tr>
                        <td style="font-size:14px;"><b> 2.</b> Loop</td>
                        <td style="font-size:14px;"><b> 9.</b> Needle mark</td>
                    </tr>
                    <tr>
                        <td style="font-size:14px;"><b> 3.</b> Hole</td>
                        <td style="font-size:14px;"><b> 10.</b> Oil mark</td>
                    </tr>
                    <tr>
                        <td><b> 4.</b> Star marks</td>
                        <td><b> 11.</b> Dia mark/Crease Mark</td>
                    </tr>
                    <tr>
                        <td style="font-size:14px;"><b> 5.</b> Barre</td>
                        <td style="font-size:14px;"><b> 12.</b> Wheel Free</td>
                    </tr>
                    <tr>
                        <td style="font-size:14px;"><b> 6.</b> Drop Stitch</td>
                        <td style="font-size:14px;"><b> 13.</b> Slub</td>
                    </tr>
                    <tr>
                        <td style="font-size:14px;"><b> 7.</b> Lot mixing</td>
                        <td style="font-size:14px;"><b> 14.</b> Other contamination</td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div style="float:right; border:1px solid #000;">
            <table border="1" rules="all" class="rpt_table" width="400" height="150">
                <thead>
                    <th colspan="2" style="font-size:18px; font-weight:bold;">Please Mark The Role The Each Role as
                        Follows
                    </th>
                    <thead>
                        <tr>
                            <td width="200" style="font-size:14px;"><b> 1.</b> Manufacturing Factory Name</td>
                            <td style="font-size:14px;"><b> 6.</b> Fabrics Type</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;"><b> 2.</b> Prog. Company Name</td>
                            <td style="font-size:14px;"><b> 7.</b> Finished Dia</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;"><b> 3.</b> Buyer, Style,Order no.</td>
                            <td style="font-size:14px;"><b> 8.</b> Finished Gsm & Color</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;"><b> 4.</b> Yarn Count, Lot & Brand</td>
                            <td style="font-size:14px;"><b> 9.</b> Yarn Composition</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;"><b> 5.</b> M/C No., Dia, Stitch Length</td>
                            <td style="font-size:14px;"><b> 10.</b> Knit Program No</td>

                        </table>
                    </div>
                    <?
		echo signature_table(100, $company_id, "1180px");//41
		?>
    </div>
    <?
    exit();
}
?>



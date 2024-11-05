<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']['user_id'];
require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------
if($action=='load_drop_down_location')
{
	echo create_drop_down( 'cbo_location_id', 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", 'id,location_name', 1, '-- Select Location --', $selected, "load_drop_down( 'requires/category_and_line_wise_total_npt_report_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_floor', 'floor_td' );" );
	exit();
}

if($action=='load_drop_down_floor')
{
	$data=explode('_',$data);
	$loca=$data[0];
	$com=$data[1];
	echo create_drop_down( 'cbo_floor_id', 100, "select id,floor_name from lib_prod_floor where production_process=5 and status_active =1 and is_deleted=0 and company_id='$com' and location_id='$loca' order by floor_name", 'id,floor_name', 1, '-- Select Floor --', $selected,  "load_drop_down( 'requires/category_and_line_wise_total_npt_report_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_id').value+'_'+this.value, 'load_drop_down_line', 'line_td' );", '', '', '', '', '', 4 );
	exit();
}

if ($action=='print_button_variable_setting')
{ 
	//echo $data;die;
	$buttonIdArr = ['108#show111111', '195#show222222', '242#show333333'];
	get_report_button_array($data, 7, 312, $user_id, $buttonIdArr);
	exit();
}

if ($action=='load_drop_down_line')
{
	$data=explode('_',$data);
	$company_id = $data[0];
	$location_id = $data[1];
	$floor_id = $data[2];

	echo create_drop_down( 'cbo_line_id', 120, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name", 'id,line_name', 1, '-- All --', $selected, '', 0, 0 );
	exit();
}

if ($action=='report_generate') {

	function get_date_range($first, $last, $step = '+1 day', $output_format = 'd-M-Y') {
	    $dates = array();
	    $current = strtotime($first);
	    $last = strtotime($last);

	    while( $current <= $last ) {
	        $dates[] = date($output_format, $current);
	        $current = strtotime($step, $current);
	    }

	    return $dates;
	}

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_location_id = str_replace("'", '', $cbo_location_id);
	$cbo_floor_id = str_replace("'", '', $cbo_floor_id);
	$cbo_line_id = str_replace("'", '', $cbo_line_id);
	$sql_cond = '';

	$line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", 'id', 'line_name');
	$company_library=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$floor_library=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');

	if( $cbo_location_id != 0 ) {
		$sql_cond .= " and a.location_id=$cbo_location_id";
	}

	if( $cbo_floor_id != 0 ) {
		$sql_cond .= " and a.floor_id=$cbo_floor_id";
	}

	if( $cbo_line_id != 0 ) {
		$sql_cond .= " and a.line_id=$cbo_line_id";
	}

	$line_idle_sql = "SELECT a.id, a.company_id, a.location_id, a.floor_id, a.line_ids,a.idle_date, a.prod_resource_id, b.category_id, b.cause_id, b.duration_hour, b.start_hour, b.start_minute, b.end_hour, b.end_minute, a.remarks, b.manpower
		from sewing_line_idle_mst a, sewing_line_idle_dtls b
		where a.is_deleted=0 and b.is_deleted=0 and b.mst_id=a.id and a.idle_date between  $txt_date_from  and $txt_date_to   and a.company_id=$cbo_company_id $sql_cond order by a.idle_date";

	//echo $line_idle_sql; die;
 	$line_idle_result = sql_select($line_idle_sql);
 	$line_idle_arr = array();
	 $line_idle_mst_arr=array();

 	foreach ($line_idle_result as $row) {
 		$idleMinute = $row[csf('duration_hour')]*$row[csf('manpower')]*60;

 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['line_ids'] = $row[csf('line_ids')];

 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['company_id'] = $row[csf('company_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['location_id'] = $row[csf('location_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]]['floor_id'] = $row[csf('floor_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['prod_resource_id'] = $row[csf('prod_resource_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['category_id'] = $row[csf('category_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['cause_id'] = $row[csf('cause_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['duration_hour'] = $row[csf('duration_hour')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['start_hour'] = $row[csf('start_hour')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['start_minute'] = $row[csf('start_minute')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['end_hour'] = $row[csf('end_hour')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['end_minute'] = $row[csf('end_minute')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['manpower'] = $row[csf('manpower')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['idle_minute'] = $idleMinute;
 		$line_idle_total_arr[$row[csf('category_id')]][$row[csf('cause_id')]]['total_minutes'] += $idleMinute;

 		$line_idle_mst_arr[$row[csf('line_ids')]]['remarks'] = $row[csf('remarks')];
 		$line_idle_mst_arr[$row[csf('idle_date')]][$row[csf('line_ids')]]['total_npt_minutes'] += $idleMinute;

 		$line_arr[] = $row[csf('line_ids')];
 	}
	//echo "<pre>";print_r($line_idle_arr);die;
	//  echo "<pre>";print_r($line_idle_mst_arr);die;
	// echo $idleMinute;



	$buyer_info_sql = "SELECT a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name as buyer_name, b.style_ref_no, b.job_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number
 		from wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d
 		where a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.serving_company=$cbo_company_id and a.production_date between  $txt_date_from  and $txt_date_to
 		group by b.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping ,d.color_type_id,a.remarks
		order by a.location,a.floor_id,a.sewing_line";

 	//echo $buyer_info_sql;
 	$buyer_info_result = sql_select($buyer_info_sql);
	$line_id_arr = array();
	foreach ($buyer_info_result as $row)
	{
		$line_id_arr[$row[csf('sewing_line')]]=$row[csf('sewing_line')];
	}
	// echo "<pre>";print_r($line_id_arr);die;
	$line_id = implode(",",$line_id_arr);
	$prod_reso_lib=return_library_array( "SELECT id, line_number from prod_resource_mst where is_deleted=0 and id in($line_id)",'id','line_number');
 	$buyer_info_arr = array();
 	foreach ($buyer_info_result as $row)
	{
		$prod_reso_id = $prod_reso_lib[$row[csf('sewing_line')]];
 		$buyer_info_arr[$prod_reso_id][$row[csf('production_date')]]['line_id'] = $prod_reso_id;
 		$buyer_info_arr[$prod_reso_id][$row[csf('production_date')]]['buyer_name'] = $row[csf('buyer_name')];
 		$buyer_info_arr[$prod_reso_id][$row[csf('production_date')]]['style_ref_no'] .= $row[csf('style_ref_no')] . ',';

 	}
	//echo "<pre>";print_r($buyer_info_arr);die;
 	$line_arr = array_unique( $line_arr );

	$sewingLines = implode(',', $line_arr);

	$resource_sql = "select a.company_id, a.location_id, a.floor_id, a.line_number, c.id, c.mst_id, c.from_date, c.to_date, c.man_power, c.operator, c.helper, c.line_chief, c.active_machine, c.target_per_hour, c.working_hour, c.po_id, b.smv_adjust, b.smv_adjust_type, c.capacity, c.target_efficiency
  		from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c
 		where a.id = b.mst_id and b.mast_dtl_id = c.id and a.id in ($sewingLines)";

 	//echo $resource_sql;
 	$po_ids = '';
 	foreach ($resource_result as $row) {
		$daterange = array();
		$begin = $row[csf('from_date')];
		$end = $row[csf('to_date')];
		$floor_id = $row[csf('floor_id')];
		if($begin != $end) {
			$daterange = get_date_range($begin, $end);
		} else {
			$daterange[] = date('d-M-y', strtotime($begin));
		}

		// make key for each date from Actual Production Resource Entry page
		for($i=0; $i<count($daterange); $i++) {
			$date = strtoupper( date( 'd-M-y', strtotime($daterange[$i]) ) );
			// echo $daterange[$i]."<br>";
		    $resource_arr[$row[csf('line')]][$date]['line'] = $row[csf('mst_id')];
			$resource_arr[$row[csf('line')]][$date]['operator'] = $row[csf('operator')];
			$resource_arr[$row[csf('line')]][$date]['helper'] = $row[csf('helper')];
			$resource_arr[$row[csf('line')]][$date]['man_power'] = $row[csf('man_power')];
			$resource_arr[$row[csf('line')]][$date]['working_hour'] = $row[csf('working_hour')];
			$resource_arr[$row[csf('line')]][$date]['prod_resource_mst_id'] = $row[csf('mst_id')];
		}

		$po_ids .= $row[csf('po_id')] . ',';
	}
	unset($resource_result);

	$po_ids = rtrim($po_ids, ',');

	$po_info_sql = "select id, style_description, buyer_name
					from wo_po_details_master
					where is_deleted=0 and status_active = 1 and id in($po_ids)";
	$po_result = sql_select($po_info_sql);
ob_start();
?> 	<div id="scroll_body">
		<div class="titles" style="text-align: center;" >
			<h2><?php echo $company_library[str_replace("'","",$cbo_company_id)];?></h2>
			<h3>Category And Line Wise Total NPT Report</h3>
		</div>
		<div width="6020px">
			<table class="rpt_table" width="6000px" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="30px" rowspan="2">Sl</th>
						<th width="60px" rowspan="2">Date</th>
						<th width="100px" rowspan="2">Floor</th>
						<th width="80px" rowspan="2">Line</th>

						<th width="90px" rowspan="2">Buyer Name</th>
						<th width="110px" rowspan="2">Style NO.</th>
						<th colspan="5">Cutting</th>
						<th colspan="8">Merchandising</th>
						<th colspan="5">Maintenance</th>
						<th colspan="4">Production Floor</th>
						<th colspan="6">Quality</th>
						<th colspan="3">Store</th>
						<th colspan="3">CAD</th>
						<th colspan="4">Commercial</th>
						<th colspan="8">HR And Admin</th>
						<th colspan="6">IE And Techincal</th>
						<th colspan="3">Electrical</th>
						<th colspan="3">Embroidery & Template</th>
						<th colspan="3">Down</th>
						<th colspan="7">Production & Technical</th>
						<th colspan="11">Expected-Opportunity-Loss-(EOL)</th>
						<th>Total</th>
						<th>Total</th>
						<th colspan="3"></th>
					</tr>
					<tr>
						<th width= "75" align="right" >Waiting for input</th>
						<th width= "75"  align="right">Wrong cut panel supply</th>
						<th  width= "75"  align="right">Fusing delay</th>
						<th  width= "75"  align="right">Cutting section capacity</th>
						<th  width= "75"  align="right">Others</th>
						<th  width= "75"  align="right">Fabric delay</th>
						<th  width= "75"  align="right">Accessories not in-house</th>
						<th  width= "75"  align="right">Accessories delay</th>
						<th width= "75"  align="right">Print/Emb.</th>
						<th width= "75"  align="right">Size set delay</th>
						<th  width= "75"  align="right">Order not ready</th>
						<th  width= "75"  align="right">PP activity delay</th>
						<th  width= "75"  align="right">Others</th>
						<th  width= "75"  align="right">M/C breakdown</th>
						<th  width= "75"  align="right">M/C adjustment</th>
						<th  width= "75"  align="right">M/C arrangement lacking</th>
						<th  width= "75"  align="right">Wrong attachment</th>
						<th  width= "75"  align="right">Others</th>
						<th  width= "75"  align="right">Bulk size set delay</th>
						<th  width= "75"  align="right">Technical problem</th>
						<th  width= "75"  align="right">Incapable manpower</th>
						<th  width= "75"  align="right">Others</th>
						<th  width= "75"  align="right">Alteration issue</th>
						<th  width= "75"  align="right">Quality not achieve</th>
						<th  width= "75"  align="right">Decision making delay</th>
						<th  width= "75"  align="right">Size set approval delay</th>
						<th  width= "75"  align="right">Wrong approval(Emb, Print, CWS, H/T & Quilting)</th>
						<th  width= "75" align="right">Others</th>
						<th  width= "75"  align="right">Accessories supply delay</th>
						<th  width= "75"  align="right">Delay fabric supply to cutting</th>
						<th  width= "75"  align="right">Others</th>
						<th  width= "75"  align="right">Wrong marker supply</th>
						<th  width= "75"  align="right">Marker supply delay</th>
						<th  width= "75"  align="right">Others</th>
						<th  width= "75"  align="right">BTB / TT delay</th>
						<th  width= "75"  align="right">Late forwarder selection</th>
						<th  width= "75"  align="right"> Late raw materils clearance</th>
						<th width= "75"   align="right">Theft of goods partils / full</th>
						<th  width= "75"  align="right">Power failure</th>
						<th  width= "75"  align="right">HR meeting (WPC) & others</th>
						<th  width= "75"  align="right">Worker transport delay</th>
						<th  width= "75"  align="right">Migration</th>
						<th  width= "75"  align="right">Natural call to evacuate floor</th>
						<th  width= "75"  align="right">Worker unrest</th>
						<th  width= "75"  align="right">Operator absentisiom</th>
						<th  width= "75"  align="right">Others</th>
						<th  width= "75"  align="right">Wrong method</th>
						<th  width= "75"  align="right">Style change over</th>
						<th  width= "75"  align="right">Wrong SMV calculation at pre-cost</th>
						<th  width= "75"  align="right">Line balance</th>
						<th  width= "75"  align="right">Quality problem</th>
						<th  width= "75"  align="right">Others</th>

						<th  width= "75"  align="right">Power failure</th>
						<th  width= "75"  align="right">Compressore problem(Air pressure)</th>
						<th  width= "75"  align="right">Others</th>

						<th  width= "75"  align="right">Waiting for Input</th>
						<th  width= "75"  align="right">Embroidery & Template  Capacity</th>
						<th  width= "75"  align="right">Others</th>

						<th  width= "75"  align="right">Waiting for Input</th>
						<th  width= "75"  align="right">Down Capacity</th>
						<th  width= "75"  align="right">Others</th>

						<th  width= "75"  align="right">Needle break</th>
						<th  width= "75"  align="right">Style Change over</th>
						<th  width= "75"  align="right">Wrong method </th>
						<th  width= "75"  align="right">Quality Prob</th>
						<th  width= "75"  align="right">Bulk Size set Delay</th>
						<th  width= "75"  align="right">Incapable Manpower</th>
						<th  width= "75"  align="right">Others & Zero(0) production hour for Technical issue</th>

						<th  width= "75"  align="right">HR & Admin</th>
						<th  width= "75"  align="right">Merchandising</th>
						<th  width= "75"  align="right">Maintenance</th>
						<th  width= "75"  align="right">Electrical</th>
						<th  width= "75"  align="right">Production & Techincal</th>
						<th  width= "75"  align="right">Quality</th>
						<th  width= "75"  align="right">Cutting</th>
						<th  width= "75"  align="right">Heat Transfer</th>
						<th  width= "75"  align="right">Storek</th>
						<th  width= "75"  align="right">CAD</th>
						<th  width= "75"  align="right">IE</th>


						<th  width= "75"  align="right">NPT Min</th>
						<th  width= "75"  align="right">NPT HRS</th>
						<th  width= "75"  align="right">CUMULATIVE MIN</th>
						<th  width= "75"  align="right">CUMULATIVE HRS</th>
						<th  width= "75"  align="right">REMARKS</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$i = 1;
						$cumulativeMin = 0;
						$totalCumulativeMin = 0;
						$totalCumulativeHour = 0;


						foreach ($line_idle_arr as $idel_date => $date_value)
						{
							foreach ($date_value as $line_id => $value)
							{


								$total_ppt_min = $line_idle_mst_arr[$idel_date][$line_id]['total_npt_minutes'];

								$rowNptMins = 0;
								$rowNptHours = 0;
								$rowCumMins += $total_ppt_min;
								$rowCumHours += number_format($rowCumMins / 60);

								$totalCumulativeMin += $rowCumMins;
								$totalCumulativeHour += $rowCumHours;

								$total_arr[1][2]['cumulative_minute'] += $value[1][2]['idle_minute'];
								$total_arr[1][3]['cumulative_minute'] += $value[1][3]['idle_minute'];
								$total_arr[1][4]['cumulative_minute'] += $value[1][4]['idle_minute'];
								$total_arr[1][5]['cumulative_minute'] += $value[1][5]['idle_minute'];
								$total_arr[1][1]['cumulative_minute'] += $value[1][1]['idle_minute'];

								$total_arr[2][6]['cumulative_minute'] += $value[2][6]['idle_minute'];
								$total_arr[2][7]['cumulative_minute'] += $value[2][7]['idle_minute'];
								$total_arr[2][8]['cumulative_minute'] += $value[2][8]['idle_minute'];
								$total_arr[2][9]['cumulative_minute'] += $value[2][9]['idle_minute'];
								$total_arr[2][10]['cumulative_minute'] += $value[2][10]['idle_minute'];
								$total_arr[2][11]['cumulative_minute'] += $value[2][11]['idle_minute'];
								$total_arr[2][12]['cumulative_minute'] += $value[2][12]['idle_minute'];
								$total_arr[2][1]['cumulative_minute'] += $value[2][1]['idle_minute'];

								$total_arr[3][13]['cumulative_minute'] += $value[3][13]['idle_minute'];
								$total_arr[3][14]['cumulative_minute'] += $value[3][14]['idle_minute'];
								$total_arr[3][15]['cumulative_minute'] += $value[3][15]['idle_minute'];
								$total_arr[3][16]['cumulative_minute'] += $value[3][16]['idle_minute'];
								$total_arr[3][1]['cumulative_minute'] += $value[3][1]['idle_minute'];

								$total_arr[4][17]['cumulative_minute'] += $value[4][17]['idle_minute'];
								$total_arr[4][18]['cumulative_minute'] += $value[4][18]['idle_minute'];
								$total_arr[4][19]['cumulative_minute'] += $value[4][19]['idle_minute'];
								$total_arr[4][1]['cumulative_minute'] += $value[4][1]['idle_minute'];

								$total_arr[5][20]['cumulative_minute'] += $value[5][20]['idle_minute'];
								$total_arr[5][21]['cumulative_minute'] += $value[5][21]['idle_minute'];
								$total_arr[5][1]['cumulative_minute'] += $value[5][1]['idle_minute'];
								$total_arr[5][110]['cumulative_minute'] += $value[5][110]['idle_minute'];
								$total_arr[5][111]['cumulative_minute'] += $value[5][111]['idle_minute'];
								$total_arr[5][112]['cumulative_minute'] += $value[5][112]['idle_minute'];

								$total_arr[6][22]['cumulative_minute'] += $value[6][22]['idle_minute'];
								$total_arr[6][23]['cumulative_minute'] += $value[6][23]['idle_minute'];
								$total_arr[6][1]['cumulative_minute'] += $value[6][1]['idle_minute'];

								$total_arr[7][24]['cumulative_minute'] += $value[7][24]['idle_minute'];
								$total_arr[7][25]['cumulative_minute'] += $value[7][25]['idle_minute'];
								$total_arr[7][1]['cumulative_minute'] += $value[7][1]['idle_minute'];

								$total_arr[8][26]['cumulative_minute'] += $value[8][26]['idle_minute'];
								$total_arr[8][27]['cumulative_minute'] += $value[8][27]['idle_minute'];
								$total_arr[8][28]['cumulative_minute'] += $value[8][28]['idle_minute'];
								$total_arr[8][29]['cumulative_minute'] += $value[8][29]['idle_minute'];

								$total_arr[9][30]['cumulative_minute'] += $value[9][30]['idle_minute'];
								$total_arr[9][31]['cumulative_minute'] += $value[9][31]['idle_minute'];
								$total_arr[9][32]['cumulative_minute'] += $value[9][32]['idle_minute'];
								$total_arr[9][33]['cumulative_minute'] += $value[9][33]['idle_minute'];
								$total_arr[9][34]['cumulative_minute'] += $value[9][34]['idle_minute'];
								$total_arr[9][35]['cumulative_minute'] += $value[9][35]['idle_minute'];
								$total_arr[9][36]['cumulative_minute'] += $value[9][36]['idle_minute'];
								$total_arr[9][1]['cumulative_minute'] += $value[9][1]['idle_minute'];

								$total_arr[10][37]['cumulative_minute'] += $value[10][37]['idle_minute'];
								$total_arr[10][38]['cumulative_minute'] += $value[10][38]['idle_minute'];
								$total_arr[10][39]['cumulative_minute'] += $value[10][39]['idle_minute'];
								$total_arr[10][40]['cumulative_minute'] += $value[10][40]['idle_minute'];
								$total_arr[10][41]['cumulative_minute'] += $value[10][41]['idle_minute'];
								$total_arr[10][1]['cumulative_minute'] += $value[10][1]['idle_minute'];

								$total_arr[11][42]['cumulative_minute'] += $value[11][42]['idle_minute'];
								$total_arr[11][43]['cumulative_minute'] += $value[11][43]['idle_minute'];
								$total_arr[11][44]['cumulative_minute'] += $value[11][44]['idle_minute'];

								$total_arr[12][45]['cumulative_minute'] += $value[12][45]['idle_minute'];
								$total_arr[12][46]['cumulative_minute'] += $value[12][46]['idle_minute'];
								$total_arr[12][44]['cumulative_minute'] += $value[12][47]['idle_minute'];

								$total_arr[13][48]['cumulative_minute'] += $value[13][48]['idle_minute'];
								$total_arr[13][49]['cumulative_minute'] += $value[13][49]['idle_minute'];
								$total_arr[13][50]['cumulative_minute'] += $value[13][50]['idle_minute'];

								$total_arr[14][53]['cumulative_minute'] += $value[14][53]['idle_minute'];
								$total_arr[14][54]['cumulative_minute'] += $value[14][54]['idle_minute'];
								$total_arr[14][55]['cumulative_minute'] += $value[14][55]['idle_minute'];
								$total_arr[14][56]['cumulative_minute'] += $value[14][56]['idle_minute'];
								$total_arr[14][113]['cumulative_minute'] += $value[14][113]['idle_minute'];
								$total_arr[14][114]['cumulative_minute'] += $value[14][114]['idle_minute'];
								$total_arr[14][115]['cumulative_minute'] += $value[14][115]['idle_minute'];

								$total_arr[27][116]['cumulative_minute'] += $value[27][116]['idle_minute'];
								$total_arr[27][117]['cumulative_minute'] += $value[27][117]['idle_minute'];
								$total_arr[27][118]['cumulative_minute'] += $value[27][118]['idle_minute'];
								$total_arr[27][119]['cumulative_minute'] += $value[27][119]['idle_minute'];
								$total_arr[27][120]['cumulative_minute'] += $value[27][120]['idle_minute'];
								$total_arr[27][121]['cumulative_minute'] += $value[27][121]['idle_minute'];
								$total_arr[27][122]['cumulative_minute'] += $value[27][122]['idle_minute'];
								$total_arr[27][123]['cumulative_minute'] += $value[27][123]['idle_minute'];
								$total_arr[27][124]['cumulative_minute'] += $value[27][124]['idle_minute'];
								$total_arr[27][125]['cumulative_minute'] += $value[27][125]['idle_minute'];
								$total_arr[27][126]['cumulative_minute'] += $value[27][126]['idle_minute'];



								// echo "<pre>"; print_r($total_arr);die;

								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer">
									<td><?=$i?></td>
									<td  align="left"><?=$idel_date?></td>
									<td  align="left"><?= $floor_library[$value['floor_id']]?></td>
									<td style="text-align: left;"><?php echo $line_library[$line_id]; ?></td>
									<td align="left"><?php echo $buyer_library[$buyer_info_arr[$line_id][$row[csf('production_date')]]['buyer_name']]; ?></td>
									<td align="left"><?php echo rtrim($buyer_info_arr[$line_id][$row[csf('production_date')]]['style_ref_no'],",") ;  ?></td>
									<td align="right"><?php echo $value[1][2]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[1][3]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[1][4]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[1][5]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[1][1]['idle_minute']; ?></td>

									<td align="right"><?php echo $value[2][6]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[2][7]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[2][8]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[2][9]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[2][10]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[2][11]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[2][12]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[2][1]['idle_minute']; ?></td>

									<td align="right"><?php echo $value[3][13]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[3][14]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[3][15]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[3][16]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[3][1]['idle_minute']; ?></td>

									<td align="right"><?php echo $value[4][17]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[4][18]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[4][19]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[4][1]['idle_minute']; ?></td>

									<td align="right"><?php echo $value[5][20]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[5][21]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[5][110]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[5][111]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[5][112]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[5][1]['idle_minute']; ?></td>


									<td align="right"><?php echo $value[6][22]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[6][23]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[6][1]['idle_minute']; ?></td>

									<td align="right"><?php echo $value[7][24]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[7][25]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[7][1]['idle_minute']; ?></td>

									<td align="right"><?php echo $value[8][26]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[8][27]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[8][28]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[8][29]['idle_minute']; ?></td>

									<td align="right"><?php echo $value[9][30]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[9][31]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[9][32]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[9][33]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[9][34]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[9][35]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[9][36]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[9][1]['idle_minute']; ?></td>

									<td align="right"><?php echo $value[10][37]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[10][38]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[10][39]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[10][40]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[10][41]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[10][1]['idle_minute']; ?></td>

									<td align="right"><?php echo $value[11][42]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[11][43]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[11][44]['idle_minute']; ?></td>

									<td align="right"><?php echo $value[12][45]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[12][46]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[12][47]['idle_minute']; ?></td>

									<td align="right"><?php echo $value[13][48]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[13][49]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[13][50]['idle_minute']; ?></td>

									<td align="right"><?php echo $value[14][53]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[14][54]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[14][55]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[14][56]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[14][113]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[14][114]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[14][115]['idle_minute']; ?></td>

									<td align="right"><?php echo $value[27][116]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[27][117]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[27][118]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[27][119]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[27][120]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[27][121]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[27][122]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[27][123]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[27][124]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[27][125]['idle_minute']; ?></td>
									<td align="right"><?php echo $value[27][126]['idle_minute']; ?></td>

									<td align="right"><?php echo $total_ppt_min; ?></td>
									<td align="right"><?php echo round($total_ppt_min / 60, 2); ?></td>
									<td align="right"><?php echo $rowCumMins; ?></td>
									<td align="right"><?php echo round($rowCumMins / 60, 2); ?></td>
									<td style="text-align: left;"><?php echo $line_idle_mst_arr[$idel_date][$line_id]['remarks']; ?></td>
								</tr>
								<?php

								$i++;
								$total_npt_min += $total_ppt_min;
								//$totalNptpHour += $total_ppt_min/60;
								// echo $totalCumulativeHou."<br>";
							}

						}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6">TOTAL MIN:</th>
						<th><?php echo $line_idle_total_arr[1][2]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[1][3]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[1][4]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[1][5]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[1][1]['total_minutes']; ?></th>

						<th><?php echo $line_idle_total_arr[2][6]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[2][7]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[2][8]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[2][9]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[2][10]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[2][11]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[2][12]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[2][1]['total_minutes']; ?></th>

						<th><?php echo $line_idle_total_arr[3][13]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[3][14]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[3][15]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[3][16]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[3][1]['total_minutes']; ?></th>

						<th><?php echo $line_idle_total_arr[4][17]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[4][18]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[4][19]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[4][1]['total_minutes']; ?></th>

						<th><?php echo $line_idle_total_arr[5][20]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[5][21]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[5][110]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[5][111]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[5][112]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[5][1]['total_minutes']; ?></th>


						<th><?php echo $line_idle_total_arr[6][22]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[6][23]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[6][1]['total_minutes']; ?></th>

						<th><?php echo $line_idle_total_arr[7][24]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[7][25]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[7][1]['total_minutes']; ?></th>

						<th><?php echo $line_idle_total_arr[8][26]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[8][27]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[8][28]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[8][29]['total_minutes']; ?></th>

						<th><?php echo $line_idle_total_arr[9][30]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[9][31]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[9][32]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[9][33]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[9][34]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[9][35]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[9][36]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[9][1]['total_minutes']; ?></th>

						<th><?php echo $line_idle_total_arr[10][37]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[10][38]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[10][39]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[10][40]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[10][41]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[10][1]['total_minutes']; ?></th>

						<th><?php echo $line_idle_total_arr[11][42]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[11][43]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[11][44]['total_minutes']; ?></th>

						<th><?php echo $line_idle_total_arr[12][45]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[12][46]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[12][47]['total_minutes']; ?></th>

						<th><?php echo $line_idle_total_arr[13][48]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[13][49]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[13][50]['total_minutes']; ?></th>

						<th><?php echo $line_idle_total_arr[14][53]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[14][54]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[14][55]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[14][56]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[14][113]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[14][114]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[14][115]['total_minutes']; ?></th>

						<th><?php echo $line_idle_total_arr[27][116]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[27][117]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[27][118]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[27][119]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[27][120]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[27][121]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[27][122]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[27][123]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[27][124]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[27][125]['total_minutes']; ?></th>
						<th><?php echo $line_idle_total_arr[27][126]['total_minutes']; ?></th>

						<th><?php echo number_format($total_npt_min,2) ;?></th>
						<th><?php echo number_format($total_npt_min / 60,2); ?></th>
						<th><?php echo number_format($totalCumulativeMin, 2); ?></th>
						<th><?php echo number_format($totalCumulativeMin / 60,2); ?></th>
						<th></th>

					</tr>
					<tr>
						<th colspan="6">TOTAL HRS:</th>
						<th><?php echo number_format($line_idle_total_arr[1][2]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[1][3]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[1][4]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[1][5]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[1][1]['total_minutes'] / 60,0); ?></th>

						<th><?php echo number_format($line_idle_total_arr[2][6]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[2][7]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[2][8]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[2][9]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[2][10]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[2][11]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[2][12]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[2][1]['total_minutes'] / 60,0); ?></th>

						<th><?php echo number_format($line_idle_total_arr[3][13]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[3][14]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[3][15]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[3][16]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[3][1]['total_minutes'] / 60,0); ?></th>

						<th><?php echo number_format($line_idle_total_arr[4][17]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[4][18]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[4][19]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[4][1]['total_minutes'] / 60,0); ?></th>

						<th><?php echo number_format($line_idle_total_arr[5][20]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[5][21]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[5][110]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[5][111]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[5][112]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[5][1]['total_minutes'] / 60,0);?></th>


						<th><?php echo number_format($line_idle_total_arr[6][22]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[6][23]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[6][1]['total_minutes'] / 60,0); ?></th>

						<th><?php echo number_format($line_idle_total_arr[7][24]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[7][25]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[7][1]['total_minutes'] / 60,0); ?></th>

						<th><?php echo number_format($line_idle_total_arr[8][26]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[8][27]['total_minutes'] / 60,0); ?></th>
						<th><?php echo  number_format($line_idle_total_arr[8][28]['total_minutes'] / 60,0); ?></th>
						<th><?php echo  number_format($line_idle_total_arr[8][29]['total_minutes'] / 60,0); ?></th>

						<th><?php echo number_format($line_idle_total_arr[9][30]['total_minutes']  / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[9][31]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[9][32]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[9][33]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[9][34]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[9][35]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[9][36]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[9][1]['total_minutes'] / 60,0); ?></th>

						<th><?php echo number_format($line_idle_total_arr[10][37]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[10][38]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[10][39]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[10][40]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format( $line_idle_total_arr[10][41]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[10][1]['total_minutes'] / 60,0); ?></th>

						<th><?php echo number_format($line_idle_total_arr[11][42]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[11][43]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[11][44]['total_minutes'] / 60,0); ?></th>

						<th><?php echo number_format($line_idle_total_arr[12][45]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[12][46]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[12][47]['total_minutes'] / 60,0); ?></th>

						<th><?php echo number_format($line_idle_total_arr[13][48]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[13][49]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[13][50]['total_minutes'] / 60,0); ?></th>

						<th><?php echo number_format($line_idle_total_arr[14][53]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[14][54]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[14][55]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[14][56]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[14][113]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[14][114]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[14][115]['total_minutes'] / 60,0); ?></th>

						<th><?php echo number_format($line_idle_total_arr[27][116]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[27][117]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[27][118]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[27][119]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[27][120]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[27][121]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[27][122]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[27][123]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[27][124]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[27][125]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($line_idle_total_arr[27][126]['total_minutes'] / 60,0); ?></th>
						<th><?php echo number_format($total_npt_min / 60,4); ?></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
					<tr>
						<th colspan="6">CUMULATIVE HOUR:</th>

						<th><?php echo number_format($total_arr[1][2]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[1][3]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[1][4]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[1][5]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[1][1]['cumulative_minute'] / 60,0); ?></th>

						<th><?php echo number_format($total_arr[2][6]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[2][7]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[2][8]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[2][9]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[2][10]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[2][11]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[2][12]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[2][1]['cumulative_minute'] / 60,0); ?></th>

						<th><?php echo number_format($total_arr[3][13]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[3][14]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[3][15]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[3][16]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[3][1]['cumulative_minute'] / 60,0); ?></th>

						<th><?php echo number_format($total_arr[4][17]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[4][18]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[4][19]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[4][1]['cumulative_minute'] / 60,0); ?></th>

						<th><?php echo number_format($total_arr[5][20]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[5][21]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[5][110]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[5][111]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[5][112]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[5][1]['cumulative_minute'] / 60,0);?></th>


						<th><?php echo number_format($total_arr[6][22]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[6][23]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[6][1]['cumulative_minute'] / 60,0); ?></th>

						<th><?php echo number_format($total_arr[7][24]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[7][25]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[7][1]['cumulative_minute'] / 60,0); ?></th>

						<th><?php echo number_format($total_arr[8][26]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[8][27]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo  number_format($total_arr[8][28]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo  number_format($total_arr[8][29]['cumulative_minute'] / 60,0); ?></th>

						<th><?php echo number_format($total_arr[9][30]['cumulative_minute']  / 60,0); ?></th>
						<th><?php echo number_format($total_arr[9][31]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[9][32]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[9][33]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[9][34]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[9][35]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[9][36]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[9][1]['cumulative_minute'] / 60,0); ?></th>

						<th><?php echo number_format($total_arr[10][37]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[10][38]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[10][39]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[10][40]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format( $total_arr[10][41]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[10][1]['cumulative_minute'] / 60,0); ?></th>

						<th><?php echo number_format($total_arr[11][42]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[11][43]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[11][44]['cumulative_minute'] / 60,0); ?></th>

						<th><?php echo number_format($total_arr[12][45]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[12][46]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[12][47]['cumulative_minute'] / 60,0); ?></th>

						<th><?php echo number_format($total_arr[13][48]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[13][49]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[13][50]['cumulative_minute'] / 60,0); ?></th>

						<th><?php echo number_format($total_arr[14][53]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[14][54]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[14][55]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[14][56]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[14][113]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[14][114]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[14][115]['cumulative_minute'] / 60,0); ?></th>

						<th><?php echo number_format($total_arr[27][116]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[27][117]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[27][118]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[27][119]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[27][120]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[27][121]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[27][122]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[27][123]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[27][124]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[27][125]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($total_arr[27][126]['cumulative_minute'] / 60,0); ?></th>
						<th><?php echo number_format($totalCumulativeMin / 60,2); ?></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
					<tr>
						<th colspan="6">TOTAL CUMULATIVE MIN:</th>
						<th><?php echo $total_arr[1][2]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[1][3]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[1][4]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[1][5]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[1][1]['cumulative_minute']; ?></th>

						<th><?php echo $total_arr[2][6]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[2][7]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[2][8]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[2][9]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[2][10]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[2][11]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[2][12]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[2][1]['cumulative_minute']; ?></th>

						<th><?php echo $total_arr[3][13]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[3][14]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[3][15]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[3][16]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[3][1]['cumulative_minute']; ?></th>

						<th><?php echo $total_arr[4][17]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[4][18]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[4][19]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[4][1]['cumulative_minute']; ?></th>

						<th><?php echo $total_arr[5][20]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[5][21]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[5][110]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[5][111]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[5][112]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[5][1]['cumulative_minute']; ?></th>


						<th><?php echo $total_arr[6][22]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[6][23]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[6][1]['cumulative_minute']; ?></th>

						<th><?php echo $total_arr[7][24]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[7][25]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[7][1]['cumulative_minute']; ?></th>

						<th><?php echo $total_arr[8][26]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[8][27]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[8][28]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[8][29]['cumulative_minute']; ?></th>

						<th><?php echo $total_arr[9][30]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[9][31]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[9][32]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[9][33]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[9][34]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[9][35]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[9][36]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[9][1]['cumulative_minute']; ?></th>

						<th><?php echo $total_arr[10][37]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[10][38]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[10][39]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[10][40]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[10][41]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[10][1]['cumulative_minute']; ?></th>

						<th><?php echo $total_arr[11][42]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[11][43]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[11][44]['cumulative_minute']; ?></th>

						<th><?php echo $total_arr[12][45]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[12][46]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[12][47]['cumulative_minute']; ?></th>

						<th><?php echo $total_arr[13][48]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[13][49]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[13][50]['cumulative_minute']; ?></th>

						<th><?php echo $total_arr[14][53]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[14][54]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[14][55]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[14][56]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[14][113]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[14][114]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[14][115]['cumulative_minute']; ?></th>

						<th><?php echo $total_arr[27][116]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[27][117]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[27][118]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[27][119]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[27][120]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[27][121]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[27][122]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[27][123]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[27][124]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[27][125]['cumulative_minute']; ?></th>
						<th><?php echo $total_arr[27][126]['cumulative_minute']; ?></th>
						<th><?php echo number_format($totalCumulativeMin,4); ?></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
		</div>
	 </div>
<?php
foreach (glob($user_id."_*.xls") as $filename)
{
	@unlink($filename);
}
$name=$user_id."_".time().".xls";
$create_new_excel = fopen($name, 'w');
$is_created = fwrite($create_new_excel,ob_get_contents());
//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
echo "####".$name;
exit();
}

/*
Development Date: 9 Sept 2023
*/
if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_location_id = str_replace("'", '', $cbo_location_id);
	$cbo_floor_id = str_replace("'", '', $cbo_floor_id);
	$cbo_line_id = str_replace("'", '', $cbo_line_id);
	$cbo_company_id = str_replace("'", '', $cbo_company_id);
	$txt_date_from = $txt_date_from ;
    $txt_date_to  =  $txt_date_to ;
	$sql_cond = '';
	//echo "location_id =$cbo_location_id <br> floor= $cbo_floor_id <br> Line = $cbo_line_id <br> cbo_company_id= $cbo_company_id <br> txt_date_from=$txt_date_from  <br> txt_date_to =$txt_date_to ";

	$line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", 'id', 'line_name');
	$company_library=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$floor_library=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');

	if( $cbo_location_id != 0 ) {
		$sql_cond .= " and a.location_id=$cbo_location_id";
	}

	if( $cbo_floor_id != 0 ) {
		$sql_cond .= " and a.floor_id=$cbo_floor_id";
	}

	if( $cbo_line_id != 0 ) {
		$sql_cond .= " and a.line_id=$cbo_line_id";
	}

	$line_idle_sql = "SELECT a.id, a.company_id, a.location_id, a.floor_id, a.line_ids,a.idle_date, a.prod_resource_id, b.category_id, b.CAUSE_ID, b.duration_hour, b.start_hour, b.start_minute, b.end_hour, b.end_minute, a.remarks, b.manpower
		from sewing_line_idle_mst a, sewing_line_idle_dtls b
		where a.status_active=1 and  a.is_deleted=0 and  b.status_active=1 and  b.is_deleted=0  and b.mst_id=a.id   and a.idle_date between  $txt_date_from  and $txt_date_to   and a.company_id=$cbo_company_id $sql_cond order by a.idle_date";
		// lib_category_wise_causes_entry c and b.category_id=c.category_id

	// echo $line_idle_sql; die;
 	$line_idle_result = sql_select($line_idle_sql);
 	$line_idle_arr = array();
	$category_id_arr=array();
 	foreach ($line_idle_result as $row) {
 		$idleMinute = $row[csf('duration_hour')]*$row[csf('manpower')]*60;
		$category_id_arr[$row['CATEGORY_ID']]=$npt_category[$row['CATEGORY_ID']];
		$cause_name_arr[$row['CATEGORY_ID']][$row['CAUSE_ID']]=$npt_cause[$row['CAUSE_ID']];

 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['line_ids'] = $row[csf('line_ids')];

 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['company_id'] = $row[csf('company_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['location_id'] = $row[csf('location_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]]['floor_id'] = $row[csf('floor_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['prod_resource_id'] = $row[csf('prod_resource_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['category_id'] = $row[csf('category_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['cause_id'] = $row[csf('cause_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['duration_hour'] = $row[csf('duration_hour')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['start_hour'] = $row[csf('start_hour')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['start_minute'] = $row[csf('start_minute')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['end_hour'] = $row[csf('end_hour')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['end_minute'] = $row[csf('end_minute')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['manpower'] = $row[csf('manpower')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['idle_minute'] = $idleMinute;
 		$line_idle_total_arr[$row[csf('category_id')]][$row[csf('cause_id')]]['total_minutes'] += $idleMinute;

 		$line_idle_mst_arr[$row[csf('line_ids')]]['remarks'] = $row[csf('remarks')];
 		$line_idle_mst_arr[$row[csf('line_ids')]]['total_npt_minutes'] += $idleMinute;
		//  $cause_name_arr[$row['CATEGORY_ID']][$row['CAUSE_ID']]=$npt_cause[$row['CAUSE_ID']];

 		$line_arr[] = $row[csf('line_ids')];
 	}
	// echo "<pre>";print_r($category_id_arr);die;
	//echo "<pre>";print_r($cause_name_arr);die;
	// echo "<pre>";print_r($line_idle_arr);die;


	$buyer_info_sql = "SELECT a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name as buyer_name, b.style_ref_no, b.job_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number
 		from wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d
 		where a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.serving_company=$cbo_company_id and a.production_date between  $txt_date_from  and $txt_date_to
 		group by b.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping ,d.color_type_id,a.remarks
		order by a.location,a.floor_id,a.sewing_line";

 	//echo $buyer_info_sql;
 	$buyer_info_result = sql_select($buyer_info_sql);
	$line_id_arr = array();
	foreach ($buyer_info_result as $row)
	{
		$line_id_arr[$row[csf('sewing_line')]]=$row[csf('sewing_line')];
	}
	// echo "<pre>";print_r($line_id_arr);die;
	$line_id = implode(",",$line_id_arr);
	$prod_reso_lib=return_library_array( "SELECT id, line_number from prod_resource_mst where is_deleted=0 and id in($line_id)",'id','line_number');
 	$buyer_info_arr = array();
 	foreach ($buyer_info_result as $row)
	{
		$prod_reso_id = $prod_reso_lib[$row[csf('sewing_line')]];
 		$buyer_info_arr[$prod_reso_id][$row[csf('production_date')]]['line_id'] = $prod_reso_id;
 		$buyer_info_arr[$prod_reso_id][$row[csf('production_date')]]['buyer_name'] = $row[csf('buyer_name')];
 		$buyer_info_arr[$prod_reso_id][$row[csf('production_date')]]['style_ref_no'] .= $row[csf('style_ref_no')] . ',';

 	}
	//echo "<pre>";print_r($buyer_info_arr);die;
 	$line_arr = array_unique( $line_arr );

	$sewingLines = implode(',', $line_arr);

	$resource_sql = "select a.company_id, a.location_id, a.floor_id, a.line_number, c.id, c.mst_id, c.from_date, c.to_date, c.man_power, c.operator, c.helper, c.line_chief, c.active_machine, c.target_per_hour, c.working_hour, c.po_id, b.smv_adjust, b.smv_adjust_type, c.capacity, c.target_efficiency
  		from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c
 		where a.id = b.mst_id and b.mast_dtl_id = c.id and a.id in ($sewingLines)";

 	//echo $resource_sql;
 	$po_ids = '';
 	foreach ($resource_result as $row) {
		$daterange = array();
		$begin = $row[csf('from_date')];
		$end = $row[csf('to_date')];
		$floor_id = $row[csf('floor_id')];
		if($begin != $end) {
			$daterange = get_date_range($begin, $end);
		} else {
			$daterange[] = date('d-M-y', strtotime($begin));
		}

		// make key for each date from Actual Production Resource Entry page
		for($i=0; $i<count($daterange); $i++) {
			$date = strtoupper( date( 'd-M-y', strtotime($daterange[$i]) ) );
			// echo $daterange[$i]."<br>";
		    $resource_arr[$row[csf('line')]][$date]['line'] = $row[csf('mst_id')];
			$resource_arr[$row[csf('line')]][$date]['operator'] = $row[csf('operator')];
			$resource_arr[$row[csf('line')]][$date]['helper'] = $row[csf('helper')];
			$resource_arr[$row[csf('line')]][$date]['man_power'] = $row[csf('man_power')];
			$resource_arr[$row[csf('line')]][$date]['working_hour'] = $row[csf('working_hour')];
			$resource_arr[$row[csf('line')]][$date]['prod_resource_mst_id'] = $row[csf('mst_id')];
		}

		$po_ids .= $row[csf('po_id')] . ',';
	}
	unset($resource_result);

	$po_ids = rtrim($po_ids, ',');

	$po_info_sql = "select id, style_description, buyer_name
					from wo_po_details_master
					where is_deleted=0 and status_active = 1 and id in($po_ids)";
	$po_result = sql_select($po_info_sql);
    ob_start();

	// $npt_cause1 = array(103=>'Fabric supply delay',132=>'Fabric Quality Issue',127 => "No Input Due To Cutting Production Delays", 128 => "Numbering And Bundling Mistake", 129 => "Cutting Quality Errors/Shade Problem", 130 => "Cutting Parts Not Available", 133 => "Accessories Quality Issues", 134 => "Lack Of Budgeted Manpower", 135 => "Approval Delay", 136 => "Accessory Consumption Issues", 137 => "Wrong Approval", 138 => "Printing Supply Delay/Quality Errors", 139 => "EMB Supply Delay/Quality Errors",102=>'Wrong accessories supply', 140 => "Accessory Delays",142 => "Power Falilure", 143 => "Air Compressor Failure",    144 => "Poor Monitoring", 145 => "Plan Not Follow Up", 146 => "Line Feeding Delay", 147 => "Piece Rate Workers Controlling Problem", 148 => "Allocated For Another Work", 149 => "Inefficient Production Output",  150 => "Rework", 151 => "Decision Delay", 152 => "Measurement Issues", 153 => "No Plan/Open Capacity", 154 => "Plan No Matching To The Line", 155 => "Sudden Planning Changes", 156 => "Machine Break Down", 157 => "Machine Setting Delay", 158 => "Machine Supply Delay", 159 => "Folders And Gauges Supply Delays", 160 => "Wrong Sample Issued", 161 => "Sample/Pattern Delays", 167 => "Lack Of Budgeted Manpower",162 => "Absenteeism And Late", 163 => "Meeting",141 => "Loader Man Allocated Delay",165=>"Layout Not Submit On Time",166=>"other");//57-109 added by kamrul
	// //$colspan=

    ?>
	<div id="scroll_body">
		<div class="titles" style="text-align: center;" >
			<h2><?php echo $company_library[str_replace("'","",$cbo_company_id)];?></h2>
			<h3>Report</h3>
       </div>
<table class="rpt_table" width="3000px" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <tr>
                <th width="30px" rowspan="2">Sl</th>
                <th width="80px" rowspan="2">Date</th>
                <th width="80px" rowspan="2">Floor</th>
                <th width="100px" rowspan="2">Line</th>
                <th width="120px" rowspan="2">Buyer Name</th>
                <th width="150px" rowspan="2">Style NO.</th>

				<?
				   foreach($category_id_arr as $category_key=>$cat_val)
				   {
					$colspan=count($cause_name_arr[$category_key]);
						?>
							<th align="right" colspan="<?=$colspan?>"><? echo $cat_val ?></th>
						<?  
				   }
				?>
               

                <th>Total</th>
                <th>Total</th>
                <th colspan="3"></th>
            </tr>
            <tr>
				<?
				   foreach($cause_name_arr as $cat_key=>$cat_id)
				   {
					foreach ($cat_id as $cause_id => $value) 
					{
						?>
					    <th  width= "75"  align="right"><? echo $value ; ?></th>
				    <? 
					}
					
				  }
				?>
				<th  width= "75"  align="right">NPT Min</th>
                <th  width= "75"  align="right">NPT HRS</th>
                <th  width= "75"  align="right">CUMULATIVE MIN</th>
                <th  width= "75"  align="right">CUMULATIVE HRS</th>
                <th  width= "75"  align="right">REMARKS</th>
            </tr>
        </thead>
         <tbody>
		 <?php
				$i = 1;
				$cumulativeMin = 0;
				$totalCumulativeMin = 0;
				$totalCumulativeHour = 0;


				foreach ($line_idle_arr as $idel_date => $date_value)
				{
					foreach ($date_value as $line_id => $value)
					{
						$total_ppt_min = $line_idle_mst_arr[$line_id]['total_npt_minutes'];

								$rowNptMins = 0;
								$rowNptHours = 0;
								$rowCumMins += $total_ppt_min;
								$rowCumHours += number_format($rowCumMins / 60);

								$totalCumulativeMin += $rowCumMins;
								$totalCumulativeHour += $rowCumHours;
							



						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>

				    	<tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer">
						<td><?=$i?></td>
						<td  align="left"><?=$idel_date?></td>
						<td width="80"  align="left"><p><?= $floor_library[$value['floor_id']]?></p></td>
						<td style="text-align: left;"><?php echo $line_library[$line_id]; ?></td>
						<td align="left"><?php echo $buyer_library[$buyer_info_arr[$line_id][$row[csf('production_date')]]['buyer_name']]; ?></td>
						<td align="left"><?php echo rtrim($buyer_info_arr[$line_id][$row[csf('production_date')]]['style_ref_no'],",") ;  ?>
					    </td>
						
						<?
							
							foreach($cause_name_arr as $cat_key=>$cat_id)
							{
								foreach ($cat_id as $cause_id => $val) 
								{
									?>
									<td  width= "75"  align="right"><? echo $value[$cat_key][$cause_id]['idle_minute']; ?></td>
								<? 
									$total_min_arr[$val] +=$value[$cat_key][$cause_id]['idle_minute'];
								}
								
							}
							//echo"<pre>";;print_r($cumulative_total_array);die;
							?>
						
						
						<td  align="right"><?php echo number_format($total_ppt_min,2) ;?></td>
						<td  align="right"><?php echo number_format($total_ppt_min / 60,2); ?></td>
						<td  align="right"><?php echo number_format($totalCumulativeMin,2); ?></td>
						<td  align="right"><?php echo number_format($totalCumulativeHour,2); ?></td>
						<td style="text-align: left;"><?php echo $line_idle_mst_arr[$line_id]['remarks']; ?></td>
					 </tr>

					 <?php

                      $i++;
					  $total_npt_min += $total_ppt_min;
					}
				}

			?>


		 </tbody>

		 <tfoot>
			<tr>
				<th colspan="6">TOTAL MIN:</th>
					<?
						foreach($cause_name_arr as $cat_key=>$cat_id)
						{
							foreach ($cat_id as $cause_id => $val) 
							{
								?>
								<th  width= "75"  align="right"><?=number_format($total_min_arr[$val],2); ?></th>
							<? 
							
							}
							
						}
					?>
				<th><?php echo number_format($total_npt_min,2) ;?></th>
				<th><?php echo number_format($total_npt_min / 60,2); ?></th>
				<th><?php echo number_format($totalCumulativeMin,2); ?></th>
				<th><?php echo number_format($totalCumulativeHour,2); ?></th>
				<th></th>

			 </tr>
            <tr>
                <th colspan="6">TOTAL HRS:</th>
				<?
						foreach($cause_name_arr as $cat_key=>$cat_id)
						{
							foreach ($cat_id as $cause_id => $val) 
							{
								?>
								<th  width= "75"  align="right"><?=number_format($total_min_arr[$val] / 60,2); ?></th>
							<? 
							
							}
							
						}
					?>
				<th><?php echo  number_format($total_npt_min / 60,2); ?></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>


            </tr>
            <tr>
                <th colspan="6">CUMULATIVE HOUR:</th>

				<?
						foreach($cause_name_arr as $cat_key=>$cat_id)
						{
							foreach ($cat_id as $cause_id => $val) 
							{
								?>
								<th  width= "75"  align="right"><?=number_format($total_min_arr[$val] / 60,2); ?></th>
							<? 
							
							}
							
						}
					?>
				<th><?php echo  number_format($totalCumulativeHour / 60,2); ?></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>




            </tr>
            <tr>
                <th colspan="6">TOTAL CUMULATIVE MIN:</th>
				<?
						foreach($cause_name_arr as $cat_key=>$cat_id)
						{
							foreach ($cat_id as $cause_id => $val) 
							{
								?>
								<th  width= "75"  align="right"><?=number_format($total_min_arr[$val],2); ?></th>
							<? 
							
							}
							
						}
					?>
				<th><?php echo  number_format($totalCumulativeMin,2); ?></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>

            </tr>

        </tfoot>
       
    </table>

</div>


    <?php

	foreach (glob($user_id."_*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');
	$is_created = fwrite($create_new_excel,ob_get_contents());
	echo "####".$name;
	exit();

}
if($action=="report_generate3"){

	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_location_id = str_replace("'", '', $cbo_location_id);
	$cbo_floor_id = str_replace("'", '', $cbo_floor_id);
	$cbo_line_id = str_replace("'", '', $cbo_line_id);
	$cbo_company_id = str_replace("'", '', $cbo_company_id);
	$txt_date_from = $txt_date_from ;
    $txt_date_to  =  $txt_date_to ;
	$sql_cond = '';
	//echo "location_id =$cbo_location_id <br> floor= $cbo_floor_id <br> Line = $cbo_line_id <br> cbo_company_id= $cbo_company_id <br> txt_date_from=$txt_date_from  <br> txt_date_to =$txt_date_to ";

	$line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", 'id', 'line_name');
	$company_library=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$floor_library=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');

	if( $cbo_location_id != 0 ) {
		$sql_cond .= " and a.location_id=$cbo_location_id";
	}

	if( $cbo_floor_id != 0 ) {
		$sql_cond .= " and a.floor_id=$cbo_floor_id";
	}

	if( $cbo_line_id != 0 ) {
		$sql_cond .= " and a.line_id=$cbo_line_id";
	}

	$line_idle_sql = "SELECT a.id, a.company_id, a.location_id, a.floor_id, a.line_ids,a.idle_date, a.prod_resource_id, b.category_id, b.cause_id, b.duration_hour, b.start_hour, b.start_minute, b.end_hour, b.end_minute, a.remarks, b.manpower
		from sewing_line_idle_mst a, sewing_line_idle_dtls b
		where a.is_deleted=0 and b.is_deleted=0 and b.mst_id=a.id and a.idle_date between  $txt_date_from  and $txt_date_to   and a.company_id=$cbo_company_id $sql_cond order by a.idle_date";

	//echo $line_idle_sql; die;
 	$line_idle_result = sql_select($line_idle_sql);
 	$line_idle_arr = array();
 	foreach ($line_idle_result as $row) {
 		$idleMinute = $row[csf('duration_hour')]*$row[csf('manpower')]*60;

 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['line_ids'] = $row[csf('line_ids')];

 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['company_id'] = $row[csf('company_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['location_id'] = $row[csf('location_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]]['floor_id'] = $row[csf('floor_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['prod_resource_id'] = $row[csf('prod_resource_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['category_id'] = $row[csf('category_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['cause_id'] = $row[csf('cause_id')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['duration_hour'] = $row[csf('duration_hour')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['start_hour'] = $row[csf('start_hour')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['start_minute'] = $row[csf('start_minute')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['end_hour'] = $row[csf('end_hour')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['end_minute'] = $row[csf('end_minute')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['manpower'] = $row[csf('manpower')];
 		$line_idle_arr[$row[csf('idle_date')]][$row[csf('line_ids')]][$row[csf('category_id')]][$row[csf('cause_id')]]['idle_minute'] = $idleMinute;
 		$line_idle_total_arr[$row[csf('category_id')]][$row[csf('cause_id')]]['total_minutes'] += $idleMinute;

 		$line_idle_mst_arr[$row[csf('line_ids')]]['remarks'] = $row[csf('remarks')];
 		$line_idle_mst_arr[$row[csf('line_ids')]]['total_npt_minutes'] += $idleMinute;

 		$line_arr[] = $row[csf('line_ids')];
 	}
	//echo "<pre>";print_r($line_idle_arr);die;


	$buyer_info_sql = "SELECT a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name as buyer_name, b.style_ref_no, b.job_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number
 		from wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d
 		where a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.serving_company=$cbo_company_id and a.production_date between  $txt_date_from  and $txt_date_to
 		group by b.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping ,d.color_type_id,a.remarks
		order by a.location,a.floor_id,a.sewing_line";

 	//echo $buyer_info_sql;
 	$buyer_info_result = sql_select($buyer_info_sql);
	$line_id_arr = array();
	foreach ($buyer_info_result as $row)
	{
		$line_id_arr[$row[csf('sewing_line')]]=$row[csf('sewing_line')];
	}
	// echo "<pre>";print_r($line_id_arr);die;
	$line_id = implode(",",$line_id_arr);
	$prod_reso_lib=return_library_array( "SELECT id, line_number from prod_resource_mst where is_deleted=0 and id in($line_id)",'id','line_number');
 	$buyer_info_arr = array();
 	foreach ($buyer_info_result as $row)
	{
		$prod_reso_id = $prod_reso_lib[$row[csf('sewing_line')]];
 		$buyer_info_arr[$prod_reso_id][$row[csf('production_date')]]['line_id'] = $prod_reso_id;
 		$buyer_info_arr[$prod_reso_id][$row[csf('production_date')]]['buyer_name'] = $row[csf('buyer_name')];
 		$buyer_info_arr[$prod_reso_id][$row[csf('production_date')]]['style_ref_no'] .= $row[csf('style_ref_no')] . ',';

 	}
	//echo "<pre>";print_r($buyer_info_arr);die;
 	$line_arr = array_unique( $line_arr );

	$sewingLines = implode(',', $line_arr);

	$resource_sql = "select a.company_id, a.location_id, a.floor_id, a.line_number, c.id, c.mst_id, c.from_date, c.to_date, c.man_power, c.operator, c.helper, c.line_chief, c.active_machine, c.target_per_hour, c.working_hour, c.po_id, b.smv_adjust, b.smv_adjust_type, c.capacity, c.target_efficiency
  		from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c
 		where a.id = b.mst_id and b.mast_dtl_id = c.id and a.id in ($sewingLines)";

 	//echo $resource_sql;
 	$po_ids = '';
 	foreach ($resource_result as $row) {
		$daterange = array();
		$begin = $row[csf('from_date')];
		$end = $row[csf('to_date')];
		$floor_id = $row[csf('floor_id')];
		if($begin != $end) {
			$daterange = get_date_range($begin, $end);
		} else {
			$daterange[] = date('d-M-y', strtotime($begin));
		}

		// make key for each date from Actual Production Resource Entry page
		for($i=0; $i<count($daterange); $i++) {
			$date = strtoupper( date( 'd-M-y', strtotime($daterange[$i]) ) );
			// echo $daterange[$i]."<br>";
		    $resource_arr[$row[csf('line')]][$date]['line'] = $row[csf('mst_id')];
			$resource_arr[$row[csf('line')]][$date]['operator'] = $row[csf('operator')];
			$resource_arr[$row[csf('line')]][$date]['helper'] = $row[csf('helper')];
			$resource_arr[$row[csf('line')]][$date]['man_power'] = $row[csf('man_power')];
			$resource_arr[$row[csf('line')]][$date]['working_hour'] = $row[csf('working_hour')];
			$resource_arr[$row[csf('line')]][$date]['prod_resource_mst_id'] = $row[csf('mst_id')];
		}

		$po_ids .= $row[csf('po_id')] . ',';
	}
	unset($resource_result);

	$po_ids = rtrim($po_ids, ',');

	$po_info_sql = "select id, style_description, buyer_name
					from wo_po_details_master
					where is_deleted=0 and status_active = 1 and id in($po_ids)";
	$po_result = sql_select($po_info_sql);
ob_start();

$npt_cause1 = array(103=>'Fabric supply delay',132=>'Fabric Quality Issue',127 => "No Input Due To Cutting Production Delays", 128 => "Numbering And Bundling Mistake", 129 => "Cutting Quality Errors/Shade Problem", 130 => "Cutting Parts Not Available", 133 => "Accessories Quality Issues", 134 => "Lack Of Budgeted Manpower", 135 => "Approval Delay", 136 => "Accessory Consumption Issues", 137 => "Wrong Approval", 138 => "Printing Supply Delay/Quality Errors", 139 => "EMB Supply Delay/Quality Errors",102=>'Wrong accessories supply', 140 => "Accessory Delays",142 => "Power Falilure", 143 => "Air Compressor Failure",    144 => "Poor Monitoring", 145 => "Plan Not Follow Up", 146 => "Line Feeding Delay", 147 => "Piece Rate Workers Controlling Problem", 148 => "Allocated For Another Work", 149 => "Inefficient Production Output",  150 => "Rework", 151 => "Decision Delay", 152 => "Measurement Issues", 153 => "No Plan/Open Capacity", 154 => "Plan No Matching To The Line", 155 => "Sudden Planning Changes", 156 => "Machine Break Down", 157 => "Machine Setting Delay", 158 => "Machine Supply Delay", 159 => "Folders And Gauges Supply Delays", 160 => "Wrong Sample Issued", 161 => "Sample/Pattern Delays", 167 => "Lack Of Budgeted Manpower",162 => "Absenteeism And Late", 163 => "Meeting",141 => "Loader Man Allocated Delay",165=>"Layout Not Submit On Time",166=>"other");//57-109 added by kamrul


    ?>
	<div id="scroll_body">
		<div class="titles" style="text-align: center;" >
			<h2><?php echo $company_library[str_replace("'","",$cbo_company_id)];?></h2>
			<h3>Report</h3>
       </div>
<table class="rpt_table" width="9000px" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
            <tr>
                <th width="30px" rowspan="2">Sl</th>
                <th width="60px" rowspan="2">Date</th>
                <th width="100px" rowspan="2">Floor</th>
                <th width="80px" rowspan="2">Line</th>
                <th width="90px" rowspan="2">Buyer Name</th>
                <th width="110px" rowspan="2">Style NO.</th>
                <th colspan="14">Production</th>
                <th colspan="21">Cutting</th>
                <th colspan="13">Dyeing</th>
                <th colspan="8">Printing</th>
                <th colspan="4">Embroidary</th>
                <th colspan="6">Maintenance</th>
                <th colspan="15">Merchandising</th>
                <th colspan="5">Quality</th>
                <th colspan="7">Management</th>
                <th colspan="3">Planning</th>
                <th >IE</th>
                <th colspan="8">Store</th>
                <th colspan="5">Technical</th>
                 <th colspan="6">Others</th>
                <th>Total</th>
                <th>Total</th>
                <th colspan="3"></th>
            </tr>
            <tr>
                <th width= "75" align="right" >Line capacity ok but production not achive</th>
                <th width= "75"  align="right">Specific Process Operator Skill Gap</th>
                <th  width= "75"  align="right">Layout Completing delay</th>
                <th  width= "75"  align="right">Previuse style closing delay</th>
                <th  width= "75"  align="right">Quality Assure poor</th>
                <th  width= "75"  align="right">Short cutting sewing</th>
                <th  width= "75"  align="right">Wash goods re-work</th>
                <th  width= "75"  align="right">MP set up delay </th>
                <th width= "75"  align="right">Alter rectify</th>
                <th width= "75"  align="right">Manpower shortage</th>
                <th  width= "75"  align="right">MP absent/Leave</th>
                <th  width= "75"  align="right">Input receive delay</th>
                <th  width= "75"  align="right">After Lunch MP absent</th>
                <th  width= "75"  align="right">Extra OT Used for prod.</th>


                <th  width= "75"  align="right">Input supply delay from cutting</th>
                <th  width= "75"  align="right">Shading problem</th>
                <th  width= "75"  align="right">Rib Shading problem</th>
                <th  width= "75"  align="right">Cuff/Btm shading Problem</th>
                <th  width= "75"  align="right">Rib supply delay</th>
                <th  width= "75"  align="right">Cuff & Bottom N/A</th>
                <th  width= "75"  align="right">Piping Supply Delay</th>
                <th  width= "75"  align="right">Waist Belt Supply delay</th>
                <th  width= "75"  align="right">Pocket supply delay</th>
                <th  width= "75"  align="right">Placket supply delay</th>
                <th  width= "75"  align="right">Lace Supply N/A</th>
                <th  width= "75"  align="right">Back & Front Part Up Down</th>
                <th  width= "75"  align="right">Stripe updrown</th>
                <th  width= "75" align="right">Cutting Measurment wrong (length/Width)</th>
                <th  width= "75"  align="right">Cut Pannel cutting wrong</th>
                <th  width= "75"  align="right">Sticker not match</th>
                <th  width= "75"  align="right">Cut pannel rejection</th>
                <th  width= "75"  align="right">GSM problem </th>
                <th  width= "75"  align="right">Decision Delay from cutting</th>
                <th  width= "75"  align="right">Cutting Wrong Input</th>
                <th  width= "75"  align="right">Short cutting Input</th>


                <th  width= "75"  align="right">Fabric supply delay</th>
                <th  width= "75"  align="right">Shading problem</th>
                <th width= "75"   align="right">Rib Shading problem</th>
                <th  width= "75"  align="right">Cuff/Btm shading Problem</th>
                <th  width= "75"  align="right">Cuff & Bottom N/A</th>
                <th  width= "75"  align="right">Piping Supply Delay</th>
                <th  width= "75"  align="right">Waist Belt  supply delay</th>
                <th  width= "75"  align="right"> Rib supply delay</th>
                <th  width= "75"  align="right"> Fabrics Dyeing wrong</th>
                <th  width= "75"  align="right"> Fabrics Hard</th>
                <th  width= "75"  align="right"> Twell Tape Supply N/A From Dyeing</th>
                <th  width= "75"  align="right"> Decision Delay from Dyeing</th>
                <th  width= "75"  align="right">Short Qty input due for fabrics</th>


                <th  width= "75"  align="right"> Print supply delay</th>
                <th  width= "75"  align="right">Print serial mistake </th>
                <th  width= "75"  align="right"> Decision Delay From Print</th>
                <th  width= "75"  align="right"> Sticker not match</th>
                <th  width= "75"  align="right"> Print position problem</th>
                <th  width= "75"  align="right"> Shaining mark</th>
                <th  width= "75"  align="right">Stream supply problem (Print Maint.) </th>
                <th  width= "75"  align="right"> Short cutting Printing</th>
                
                
                <th  width= "75"  align="right"> Emb. supply delay</th>
                <th  width= "75"  align="right">Emb. serial mistake </th>
                <th  width= "75"  align="right"> Decision Delay From Emb. </th>
                <th  width= "75"  align="right">Sticker not match</th>


                <th  width= "75"  align="right">Machine problem</th>
                <th  width= "75"  align="right">Machine shortage</th>
                <th  width= "75"  align="right">Machine setup delay </th>
                <th  width= "75"  align="right">Folder Supply Delay</th>
                <th  width= "75"  align="right">Short circuit</th>
                <th  width= "75"  align="right">Decision Delay</th>



                <th  width= "75"  align="right">Accessories supply delay</th>
                <th  width= "75"  align="right">Wrong Accessories Supply</th>
                <th  width= "75"  align="right">Sewing thread N/A</th>
                <th  width= "75"  align="right">Strap decission pending</th>
                <th  width= "75"  align="right">Elastic supply N/A</th>
                <th  width= "75"  align="right">Lace supply delay</th>
                <th  width= "75"  align="right">BTN Supply N/A</th>
                <th  width= "75"  align="right">Twill tape supply delay</th>
                <th  width= "75"  align="right">Mobilion tap supply delay</th>
                <th  width= "75"  align="right">Belt supply N/A</th>
                <th  width= "75"  align="right">Waist Belt Supply N/A</th>
                <th  width= "75"  align="right">Sewing thread shade</th>
                <th  width= "75"  align="right">Elastic measurement problem</th>
                <th  width= "75"  align="right">Decision Wrong</th>
                <th  width= "75"  align="right">Thread shading Problem</th>
                
                
                
                <th  width= "75"  align="right">Wrong Follow up & Decision</th>
                <th  width= "75"  align="right">Wrong Approval</th>
                <th  width= "75"  align="right">Approval arrange Delay</th>
                <th  width= "75"  align="right">Measurement Problem</th>
                <th  width= "75"  align="right">PP meeting & file supply delay.</th>

                <th  width= "75"  align="right">Decision pending</th>
                <th  width= "75"  align="right">Emergency Input Plan Chang</th>
                <th  width= "75"  align="right">Order confirm delay</th>
                <th  width= "75"  align="right">Decision Delay</th>
                <th  width= "75"  align="right">Input Decision Delay .</th>
                <th  width= "75"  align="right">Sudden Plan For Short Quantity</th>
                <th  width= "75"  align="right">Wrong Planning.</th>
             
                <th  width= "75"  align="right">Suddenly Plan Change .</th>
                <th  width= "75"  align="right">Wrong Plan</th>
                <th  width= "75"  align="right">Plan ok but Input Not Ready.</th>
             
                <th  width= "75"  align="right">IE Line Balancing Problem</th>
             
                <th  width= "75"  align="right">Wrong Accessories supply</th>
                <th  width= "75"  align="right">Accosorise Supply delay</th>
                <th  width= "75"  align="right">Label supply delay</th>
                <th  width= "75"  align="right">Decision Delay</th>
                <th  width= "75"  align="right">Lace supply N/A</th>
                <th  width= "75"  align="right">Elastic supply delay</th>
                <th  width= "75"  align="right">Filament thread arrangement delay.</th>
                <th  width= "75"  align="right">Thread Supply delay</th>
             

                <th  width= "75"  align="right">Cut mark mistake</th>
                <th  width= "75"  align="right">Pattern measurment wrong</th>
                <th  width= "75"  align="right">Panel Patern Measurement Wrong</th>
                <th  width= "75"  align="right">Neck Piping Fabrics booking Missing</th>
                <th  width= "75"  align="right">Fabrics booking Missing</th>
               
                <th  width= "75"  align="right">Earthquake</th>
                <th  width= "75"  align="right">Pocket supply delay from Finishing</th>
                <th  width= "75"  align="right">Electicity problem</th>
                <th  width= "75"  align="right">Strem Problem</th>
                <th  width= "75"  align="right">Air pressure supply N/A</th>
                <th  width= "75"  align="right">Fire Training</th>
              
                
                <th  width= "75"  align="right">NPT Min</th>
                <th  width= "75"  align="right">NPT HRS</th> 
                <th  width= "75"  align="right">CUMULATIVE MIN</th>
                <th  width= "75"  align="right">CUMULATIVE HRS</th>
                <th  width= "75"  align="right">REMARKS</th>
            </tr>
        </thead>
		<tbody>
		 <?php
				$i = 1;
				$cumulativeMin = 0;
				$totalCumulativeMin = 0;
				$totalCumulativeHour = 0;


				foreach ($line_idle_arr as $idel_date => $date_value)
				{
					foreach ($date_value as $line_id => $value)
					{
						$total_ppt_min = $line_idle_mst_arr[$line_id]['total_npt_minutes'];

								$rowNptMins = 0;
								$rowNptHours = 0;
								$rowCumMins += $total_ppt_min;
								$rowCumHours += number_format($rowCumMins / 60);

								$totalCumulativeMin += $rowCumMins;
								$totalCumulativeHour += $rowCumHours;
								$total_arr[14][266]['cumulative_minute'] +=$value[14][266]['idle_minute'];
								$total_arr[14][267]['cumulative_minute'] +=$value[14][267]['idle_minute'];
								$total_arr[14][268]['cumulative_minute'] +=$value[14][268]['idle_minute'];
								$total_arr[14][269]['cumulative_minute'] +=$value[14][269]['idle_minute'];
								$total_arr[14][270]['cumulative_minute'] +=$value[14][270]['idle_minute'];
								$total_arr[14][271]['cumulative_minute'] +=$value[14][271]['idle_minute'];
								$total_arr[14][272]['cumulative_minute'] +=$value[14][272]['idle_minute'];
								$total_arr[14][273]['cumulative_minute'] +=$value[14][273]['idle_minute'];
								$total_arr[14][274]['cumulative_minute'] +=$value[14][274]['idle_minute'];
								$total_arr[14][275]['cumulative_minute'] +=$value[14][275]['idle_minute'];							
								$total_arr[14][276]['cumulative_minute'] +=$value[14][276]['idle_minute'];
								$total_arr[14][277]['cumulative_minute'] +=$value[14][277]['idle_minute'];
								$total_arr[14][278]['cumulative_minute'] +=$value[14][278]['idle_minute'];
								
								
					            $total_arr[1][164]['cumulative_minute'] +=	$value[1][164]['idle_minute'];
								$total_arr[1][165]['cumulative_minute'] +=	$value[1][165]['idle_minute'];
								$total_arr[1][166]['cumulative_minute'] +=	$value[1][166]['idle_minute'];
								$total_arr[1][167]['cumulative_minute'] +=	$value[1][167]['idle_minute'];
								$total_arr[1][168]['cumulative_minute'] +=	$value[1][168]['idle_minute'];
								$total_arr[1][169]['cumulative_minute'] +=	$value[1][169]['idle_minute'];
								$total_arr[1][170]['cumulative_minute'] +=	$value[1][170]['idle_minute'];
								$total_arr[1][172]['cumulative_minute'] +=	$value[1][172]['idle_minute'];
								$total_arr[1][172]['cumulative_minute'] +=	$value[1][172]['idle_minute'];
								$total_arr[1][173]['cumulative_minute'] +=	$value[1][173]['idle_minute'];
								$total_arr[1][174]['cumulative_minute'] +=	$value[1][174]['idle_minute'];
								$total_arr[1][175]['cumulative_minute'] +=	$value[1][175]['idle_minute'];
								$total_arr[1][176]['cumulative_minute'] +=	$value[1][176]['idle_minute'];
								$total_arr[1][177]['cumulative_minute'] +=	$value[1][177]['idle_minute'];
								$total_arr[1][178]['cumulative_minute'] +=	$value[1][178]['idle_minute'];
								$total_arr[1][179]['cumulative_minute'] +=	$value[1][179]['idle_minute'];
								$total_arr[1][180]['cumulative_minute'] +=	$value[1][180]['idle_minute'];
								$total_arr[1][181]['cumulative_minute'] +=	$value[1][181]['idle_minute'];
								$total_arr[1][182]['cumulative_minute'] +=	$value[1][182]['idle_minute'];
								$total_arr[1][183]['cumulative_minute'] +=	$value[1][183]['idle_minute'];
								$total_arr[1][184]['cumulative_minute'] +=	$value[1][184]['idle_minute'];
								

								$total_arr[15][185]['cumulative_minute'] +=	$value[15][185]['idle_minute'];
								$total_arr[15][186]['cumulative_minute'] +=	$value[15][186]['idle_minute'];
								$total_arr[15][187]['cumulative_minute'] +=	$value[15][187]['idle_minute'];
								$total_arr[15][188]['cumulative_minute'] +=	$value[15][188]['idle_minute'];
								$total_arr[15][189]['cumulative_minute'] +=	$value[15][189]['idle_minute'];
								$total_arr[15][190]['cumulative_minute'] +=	$value[15][190]['idle_minute'];
								$total_arr[15][191]['cumulative_minute'] +=	$value[15][191]['idle_minute'];
								$total_arr[15][192]['cumulative_minute'] +=	$value[15][192]['idle_minute'];
								$total_arr[15][193]['cumulative_minute'] +=	$value[15][193]['idle_minute'];
								$total_arr[15][194]['cumulative_minute'] +=	$value[15][194]['idle_minute'];
								$total_arr[15][195]['cumulative_minute'] +=	$value[15][195]['idle_minute'];
								$total_arr[15][196]['cumulative_minute'] +=	$value[15][196]['idle_minute'];
								$total_arr[15][197]['cumulative_minute'] +=	$value[15][197]['idle_minute'];
								
								
								$total_arr[20][198]['cumulative_minute'] +=	$value[20][198]['idle_minute'];
								$total_arr[20][199]['cumulative_minute'] +=	$value[20][199]['idle_minute'];
								$total_arr[20][200]['cumulative_minute'] +=	$value[20][200]['idle_minute'];
								$total_arr[20][201]['cumulative_minute'] +=	$value[20][201]['idle_minute'];
								$total_arr[20][202]['cumulative_minute'] +=	$value[20][202]['idle_minute'];
								$total_arr[20][203]['cumulative_minute'] +=	$value[20][203]['idle_minute'];
								$total_arr[20][204]['cumulative_minute'] +=	$value[20][204]['idle_minute'];
								$total_arr[20][205]['cumulative_minute'] +=	$value[20][205]['idle_minute'];
								
								$total_arr[17][206]['cumulative_minute'] +=	$value[17][206]['idle_minute'];
								$total_arr[17][207]['cumulative_minute'] +=	$value[17][207]['idle_minute'];
								$total_arr[17][208]['cumulative_minute'] +=	$value[17][208]['idle_minute'];
								$total_arr[17][209]['cumulative_minute'] +=	$value[17][209]['idle_minute'];
								
								$total_arr[3][210]['cumulative_minute'] +=	$value[3][210]['idle_minute'];
								$total_arr[3][211]['cumulative_minute'] +=	$value[3][211]['idle_minute'];
								$total_arr[3][212]['cumulative_minute'] +=	$value[3][212]['idle_minute'];
								$total_arr[3][213]['cumulative_minute'] +=	$value[3][213]['idle_minute'];
								$total_arr[3][214]['cumulative_minute'] +=	$value[3][214]['idle_minute'];
								$total_arr[3][215]['cumulative_minute'] +=	$value[3][215]['idle_minute'];
								
								$total_arr[2][216]['cumulative_minute'] +=	$value[2][216]['idle_minute'];
								$total_arr[2][217]['cumulative_minute'] +=	$value[2][217]['idle_minute'];
								$total_arr[2][218]['cumulative_minute'] +=	$value[2][218]['idle_minute'];
								$total_arr[2][219]['cumulative_minute'] +=	$value[2][219]['idle_minute'];
								$total_arr[2][220]['cumulative_minute'] +=	$value[2][220]['idle_minute'];
								$total_arr[2][221]['cumulative_minute'] +=	$value[2][221]['idle_minute'];
								$total_arr[2][222]['cumulative_minute'] +=	$value[2][222]['idle_minute'];
								$total_arr[2][223]['cumulative_minute'] +=	$value[2][223]['idle_minute'];
								$total_arr[2][224]['cumulative_minute'] +=	$value[2][224]['idle_minute'];
								$total_arr[2][225]['cumulative_minute'] +=	$value[2][225]['idle_minute'];
								$total_arr[2][226]['cumulative_minute'] +=	$value[2][226]['idle_minute'];
								$total_arr[2][227]['cumulative_minute'] +=	$value[2][227]['idle_minute'];
								$total_arr[2][228]['cumulative_minute'] +=	$value[2][228]['idle_minute'];
								$total_arr[2][229]['cumulative_minute'] +=	$value[2][229]['idle_minute'];
								$total_arr[2][230]['cumulative_minute'] +=	$value[2][230]['idle_minute'];
								
								
								$total_arr[5][235]['cumulative_minute'] +=	$value[5][235]['idle_minute'];
								$total_arr[5][234]['cumulative_minute'] +=	$value[5][234]['idle_minute'];
								$total_arr[5][231]['cumulative_minute'] +=	$value[5][231]['idle_minute'];
								$total_arr[5][232]['cumulative_minute'] +=	$value[5][232]['idle_minute'];
								$total_arr[5][233]['cumulative_minute'] +=	$value[5][233]['idle_minute'];
								
								$total_arr[32][236]['cumulative_minute'] +=	$value[32][236]['idle_minute'];
								$total_arr[32][237]['cumulative_minute'] +=	$value[32][237]['idle_minute'];
								$total_arr[32][238]['cumulative_minute'] +=	$value[32][238]['idle_minute'];
								$total_arr[32][239]['cumulative_minute'] +=	$value[32][239]['idle_minute'];
								$total_arr[32][240]['cumulative_minute'] +=	$value[32][240]['idle_minute'];
								$total_arr[32][241]['cumulative_minute'] +=	$value[32][241]['idle_minute'];
								$total_arr[32][242]['cumulative_minute'] +=	$value[32][242]['idle_minute'];
								
								$total_arr[30][243]['cumulative_minute'] +=	$value[30][243]['idle_minute'];
								$total_arr[30][244]['cumulative_minute'] +=	$value[30][244]['idle_minute'];
								$total_arr[30][245]['cumulative_minute'] +=	$value[30][245]['idle_minute'];
								$total_arr[18][246]['cumulative_minute'] +=	$value[18][246]['idle_minute'];
								
								$total_arr[6][247]['cumulative_minute'] +=	$value[6][247]['idle_minute'];
								$total_arr[6][248]['cumulative_minute'] +=	$value[6][248]['idle_minute'];
								$total_arr[6][249]['cumulative_minute'] +=	$value[6][249]['idle_minute'];
								$total_arr[6][250]['cumulative_minute'] +=	$value[6][250]['idle_minute'];
								$total_arr[6][251]['cumulative_minute'] +=	$value[6][251]['idle_minute'];
								$total_arr[6][252]['cumulative_minute'] +=	$value[6][252]['idle_minute'];
								$total_arr[6][253]['cumulative_minute'] +=	$value[6][253]['idle_minute'];
								$total_arr[6][254]['cumulative_minute'] +=	$value[6][254]['idle_minute'];
								
								$total_arr[14][255]['cumulative_minute'] +=	$value[14][255]['idle_minute'];
								$total_arr[14][256]['cumulative_minute'] +=	$value[14][256]['idle_minute'];
								$total_arr[14][257]['cumulative_minute'] +=	$value[14][257]['idle_minute'];
								$total_arr[14][258]['cumulative_minute'] +=	$value[14][258]['idle_minute'];
								$total_arr[14][259]['cumulative_minute'] +=	$value[14][259]['idle_minute'];
								$total_arr[99][260]['cumulative_minute'] +=	$value[99][260]['idle_minute'];
								$total_arr[99][261]['cumulative_minute'] +=	$value[99][261]['idle_minute'];
								$total_arr[99][262]['cumulative_minute'] +=	$value[99][262]['idle_minute'];
								$total_arr[99][263]['cumulative_minute'] +=	$value[99][263]['idle_minute'];
								$total_arr[99][264]['cumulative_minute'] +=	$value[99][264]['idle_minute'];
								$total_arr[99][265]['cumulative_minute'] +=	$value[99][265]['idle_minute'];
								

								


						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>

				    	<tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer">
						<td><?=$i?></td>
						<td  align="left"><?=$idel_date?></td>
						<td width="40"  align="left"><p><?= $floor_library[$value['floor_id']]?></p></td>
						<td style="text-align: left;"><?php echo $line_library[$line_id]; ?></td>
						<td align="left"><?php echo $buyer_library[$buyer_info_arr[$line_id][$row[csf('production_date')]]['buyer_name']]; ?></td>
						<td align="left"><?php echo rtrim($buyer_info_arr[$line_id][$row[csf('production_date')]]['style_ref_no'],",") ;  ?></td>
						<td><?php echo  $value[14][266]['idle_minute']; ?></td>
						<td><?php echo  $value[14][267]['idle_minute'];  ?></td>
						<td><?  echo    $value[14][268]['idle_minute'];    ?></td>
						<td><?php echo  $value[14][269]['idle_minute'];    ?></td>
						<td><?php echo  $value[14][270]['idle_minute'];   ?></td>
						<td><?php echo  $value[14][271]['idle_minute'];   ?></td>
						<td><?php echo  $value[14][272]['idle_minute'];   ?></td>
						<td><?php echo  $value[14][273]['idle_minute'];   ?></td>
						<td><?php echo  $value[14][274]['idle_minute'];   ?></td>
						<td><?php echo  $value[14][275]['idle_minute'];   ?></td>
						<td><?php echo  $value[14][276]['idle_minute'];   ?></td>
						<td><?php echo  $value[14][277]['idle_minute'];   ?></td>
						<td><?php echo  $value[14][278]['idle_minute'];   ?></td>
						<td><?php echo  $value[14][279]['idle_minute'];   ?></td>

						<td><?php echo  $value[1][164]['idle_minute'];  ?></td>
						<td><?php echo  $value[1][165]['idle_minute']; ?></td>
						<td><?php echo  $value[1][166]['idle_minute']; ?></td>
						<td><?php echo  $value[1][167]['idle_minute']; ?></td>
						<td><?php echo  $value[1][168]['idle_minute']; ?></td>
						<td><?php echo  $value[1][169]['idle_minute']; ?></td>
						<td><?php echo  $value[1][170]['idle_minute']; ?></td>
						<td><?php echo  $value[1][172]['idle_minute']; ?></td>
						<td><?php echo  $value[1][172]['idle_minute']; ?></td>
						<td><?php echo  $value[1][173]['idle_minute']; ?></td>
						<td><?php echo  $value[1][174]['idle_minute']; ?></td>
						<td><?php echo  $value[1][175]['idle_minute']; ?></td>
						<td><?php echo  $value[1][176]['idle_minute']; ?></td>
						<td><?php echo  $value[1][177]['idle_minute']; ?></td>
						<td><?php echo  $value[1][178]['idle_minute']; ?></td>
						<td><?php echo  $value[1][179]['idle_minute']; ?></td>
						<td><?php echo  $value[1][180]['idle_minute']; ?></td>
						<td><?php echo  $value[1][181]['idle_minute']; ?></td>
						<td><?php echo  $value[1][182]['idle_minute']; ?></td>
						<td><?php echo  $value[1][183]['idle_minute'];  ?></td>
						<td><?php echo  $value[1][184]['idle_minute']; ?></td>

						<td><?php echo  $value[15][185]['idle_minute'];  ?></td>
						<td><?php echo  $value[15][186]['idle_minute'];   ?></td>
						<td><?php echo  $value[15][187]['idle_minute'];  ?></td>
						<td><?php echo  $value[15][188]['idle_minute'];  ?></td>
						<td><?php echo  $value[15][189]['idle_minute'];  ?></td>
						<td><?php echo  $value[15][190]['idle_minute'];   ?></td>
						<td><?php echo  $value[15][191]['idle_minute'];  ?></td>
						<td><?php echo  $value[15][192]['idle_minute'];  ?></td>
						<td><?php echo  $value[15][193]['idle_minute'];  ?></td>
						<td><?php echo  $value[15][194]['idle_minute'];  ?></td>
						<td><?php echo  $value[15][195]['idle_minute'];  ?></td>
						<td><?php echo  $value[15][196]['idle_minute'];  ?></td>
						<td><?php echo  $value[15][197]['idle_minute'];  ?></td>


						<td><?  echo   $value[20][198]['idle_minute'];  ?></td>
						<td><?  echo   $value[20][199]['idle_minute'];  ?></td>
						<td><?  echo   $value[20][200]['idle_minute'];  ?></td>
						<td><?  echo   $value[20][201]['idle_minute'];  ?></td>
						<td><?  echo   $value[20][202]['idle_minute'];  ?></td>
						<td><?  echo   $value[20][203]['idle_minute'];  ?></td>
						<td><?  echo   $value[20][204]['idle_minute'];  ?></td>
						<td><?  echo   $value[20][205]['idle_minute'];  ?></td>
						
						<td><? echo    $value[17][206]['idle_minute']   ;?></td>
						<td><? echo    $value[17][207]['idle_minute']   ;?></td>
						<td><? echo    $value[17][208]['idle_minute']   ;?></td>
						<td><? echo    $value[17][209]['idle_minute']   ;?></td>

						<td><? echo    $value[3][210]['idle_minute']   ;?></td>
						<td><? echo    $value[3][211]['idle_minute']   ;?></td>
						<td><? echo    $value[3][212]['idle_minute']   ;?></td>
						<td><? echo    $value[3][213]['idle_minute']   ;?></td>
						<td><? echo    $value[3][214]['idle_minute']   ;?></td>
						<td><? echo    $value[3][215]['idle_minute']   ;?></td>

						<td><? echo    $value[2][216]['idle_minute']   ;?></td>
						<td><? echo    $value[2][217]['idle_minute']   ;?></td>
						<td><? echo    $value[2][218]['idle_minute']   ;?></td>
						<td><? echo    $value[2][219]['idle_minute']   ;?></td>
						<td><? echo    $value[2][220]['idle_minute']   ;?></td>
						<td><? echo    $value[2][221]['idle_minute']   ;?></td>
						<td><? echo    $value[2][222]['idle_minute']   ;?></td>
						<td><? echo    $value[2][223]['idle_minute']   ;?></td>
						<td><? echo    $value[2][224]['idle_minute']   ;?></td>
						<td><? echo    $value[2][225]['idle_minute']   ;?></td>
						<td><? echo    $value[2][226]['idle_minute']   ;?></td>
						<td><? echo    $value[2][227]['idle_minute']   ;?></td>
						<td><? echo    $value[2][228]['idle_minute']   ;?></td>
						<td><? echo    $value[2][229]['idle_minute']   ;?></td>
						<td><? echo    $value[2][230]['idle_minute']   ;?></td>

						<td><? echo    $value[5][235]['idle_minute']   ;?></td>
						<td><? echo    $value[5][234]['idle_minute']   ;?></td>
						<td><? echo    $value[5][231]['idle_minute']   ;?></td>
						<td><? echo    $value[5][232]['idle_minute']   ;?></td>
						<td><? echo    $value[5][233]['idle_minute']   ;?></td>

						<td><? echo    $value[32][236]['idle_minute']   ;?></td>
						<td><? echo    $value[32][237]['idle_minute']   ;?></td>
						<td><? echo    $value[32][238]['idle_minute']   ;?></td>
						<td><? echo    $value[32][239]['idle_minute']   ;?></td>
						<td><? echo    $value[32][240]['idle_minute']   ;?></td>
						<td><? echo    $value[32][241]['idle_minute']   ;?></td>
						<td><? echo    $value[32][242]['idle_minute']   ;?></td>

						<td><? echo   $value[30][243]['idle_minute']  ;?></td>
						<td><? echo   $value[30][244]['idle_minute']  ;?></td>
						<td><? echo   $value[30][245]['idle_minute']  ;?></td>

						<td><? echo   $value[18][246]['idle_minute']  ;?></td>

						<td><? echo   $value[6][247]['idle_minute']  ;?></td>
						<td><? echo   $value[6][248]['idle_minute']  ;?></td>
						<td><? echo   $value[6][249]['idle_minute']  ;?></td>
						<td><? echo   $value[6][250]['idle_minute']  ;?></td>
						<td><? echo   $value[6][251]['idle_minute']  ;?></td>
						<td><? echo   $value[6][252]['idle_minute']  ;?></td>
						<td><? echo   $value[6][253]['idle_minute']  ;?></td>
						<td><? echo   $value[6][254]['idle_minute']  ;?></td>

						
						<td><? echo   $value[14][255]['idle_minute']  ;?></td>
						<td><? echo   $value[14][256]['idle_minute']  ;?></td>
						<td><? echo   $value[14][257]['idle_minute']  ;?></td>
						<td><? echo   $value[14][258]['idle_minute']  ;?></td>
						<td><? echo   $value[14][259]['idle_minute']  ;?></td>
					    <td><? echo   $value[99][260]['idle_minute']  ;?></td>
						<td><? echo   $value[99][261]['idle_minute']  ;?></td>
						<td><? echo   $value[99][262]['idle_minute']  ;?></td>
						<td><? echo   $value[99][263]['idle_minute']  ;?></td>
						<td><? echo   $value[99][264]['idle_minute']  ;?></td>
						<td><? echo   $value[99][265]['idle_minute']  ;?></td>
						
						
						<td><?php echo number_format($total_ppt_min,4) ;?></td>
						<td><?php echo number_format($total_ppt_min / 60,4); ?></td>
						<td><?php echo number_format($totalCumulativeMin,4); ?></td>
						<td><?php echo number_format($totalCumulativeHour,4); ?></td>
						<td style="text-align: left;"><?php echo $line_idle_mst_arr[$line_id]['remarks']; ?></td>
					 </tr>

					 <?php

                      $i++;
					  $total_npt_min += $total_ppt_min;
					}
				}

			?>


		 </tbody>
        <tfoot>
            <tr  bgcolor="" style="text-decoration:none; cursor:pointer">
                <th colspan="6">TOTAL MIN:</th>
                <th><?  echo $line_idle_total_arr[14][266]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][267]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][268]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][269]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][270]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][271]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][272]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][273]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][274]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][275]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][276]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][277]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][278]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][279]['total_minutes']   ?></th>

                <th><?  echo $line_idle_total_arr[1][164]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][165]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][166]['total_minutes']  ?></th>							
                <th><?  echo $line_idle_total_arr[1][167]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][168]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][169]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][170]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][172]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][172]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][173]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][174]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][175]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][176]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][177]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][178]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][179]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][180]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][181]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][182]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][183]['total_minutes']  ?></th>
                <th><?  echo $line_idle_total_arr[1][184]['total_minutes']  ?></th>

                <th><?  echo $line_idle_total_arr[15][185]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[15][186]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[15][187]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[15][188]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[15][189]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[15][190]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[15][191]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[15][192]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[15][193]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[15][194]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[15][195]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[15][196]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[15][197]['total_minutes']   ?></th>

                <th><?  echo $line_idle_total_arr[20][198]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[20][199]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[20][200]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[20][201]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[20][202]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[20][203]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[20][204]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[20][205]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[17][206]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[17][207]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[17][208]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[17][209]['total_minutes']   ?></th>

                <th><?  echo $line_idle_total_arr[3][210]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[3][211]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[3][212]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[3][213]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[3][214]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[3][215]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[2][216]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[2][217]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[2][218]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[2][219]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[2][220]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[2][221]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[2][222]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[2][223]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[2][224]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[2][225]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[2][226]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[2][227]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[2][228]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[2][229]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[2][230]['total_minutes']   ?></th>
  
                <th><?  echo $line_idle_total_arr[5][235]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[5][234]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[5][231]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[5][232]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[5][233]['total_minutes']   ?></th>
  
                <th><?  echo $line_idle_total_arr[32][236]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[32][237]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[32][238]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[32][239]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[32][240]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[32][241]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[32][242]['total_minutes']   ?></th>
  
                <th><?  echo $line_idle_total_arr[30][243]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[30][244]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[30][245]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[18][246]['total_minutes']   ?></th>
  
                <th><?  echo $line_idle_total_arr[6][247]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[6][248]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[6][249]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[6][250]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[6][251]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[6][252]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[6][253]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[6][254]['total_minutes']   ?></th>
  
                <th><?  echo $line_idle_total_arr[14][255]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][256]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][257]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][258]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[14][259]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[99][260]['total_minutes']   ?></th>
                 
                <th><?  echo $line_idle_total_arr[99][261]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[99][262]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[99][263]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[99][264]['total_minutes']   ?></th>
                <th><?  echo $line_idle_total_arr[99][265]['total_minutes']   ?></th>

                <th><?php echo number_format($total_npt_min,4) ;?></th>
                <th><?php echo number_format($total_npt_min / 60,4); ?></th>
                <th><?php echo number_format($totalCumulativeMin,4); ?></th>
                <th><?php echo number_format($totalCumulativeHour,4); ?></th> 
				<th></th>
            </tr>
            <tr>
                <th colspan="6">TOTAL HRS:</th>
				<th><?  echo number_format($line_idle_total_arr[14][266]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][267]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][268]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][269]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][270]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][271]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][272]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][273]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][274]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][275]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][276]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][277]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][278]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][279]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[1][164]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][165]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][166]['total_minutes'] /60,0)?></th>							
                <th><?  echo number_format($line_idle_total_arr[1][167]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][168]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][169]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][170]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][172]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][172]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][173]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][174]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][175]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][176]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][177]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][178]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][179]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][180]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][181]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][182]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][183]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[1][184]['total_minutes'] /60,0)?></th>
                <th><?  echo number_format($line_idle_total_arr[15][185]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[15][186]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[15][187]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[15][188]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[15][189]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[15][190]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[15][191]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[15][192]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[15][193]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[15][194]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[15][195]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[15][196]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[15][197]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[20][198]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[20][199]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[20][200]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[20][201]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[20][202]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[20][203]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[20][204]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[20][205]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[17][206]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[17][207]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[17][208]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[17][209]['total_minutes']/60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[3][210]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[3][211]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[3][212]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[3][213]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[3][214]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[3][215]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[2][216]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[2][217]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[2][218]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[2][219]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[2][220]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[2][221]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[2][222]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[2][223]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[2][224]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[2][225]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[2][226]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[2][227]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[2][228]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[2][229]['total_minutes'] /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[2][230]['total_minutes'] /60,0) ?></th>
  
                <th><?  echo number_format($line_idle_total_arr[5][235]['total_minutes']  /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[5][234]['total_minutes']  /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[5][231]['total_minutes']  /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[5][232]['total_minutes']  /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[5][233]['total_minutes']  /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[32][236]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[32][237]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[32][238]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[32][239]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[32][240]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[32][241]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[32][242]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[30][243]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[30][244]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[30][245]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[18][246]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[6][247]['total_minutes']  /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[6][248]['total_minutes']  /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[6][249]['total_minutes']  /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[6][250]['total_minutes']  /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[6][251]['total_minutes']  /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[6][252]['total_minutes']  /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[6][253]['total_minutes']  /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[6][254]['total_minutes']  /60,0) ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][255]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][256]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][257]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][258]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[14][259]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[99][260]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[99][261]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[99][262]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[99][263]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[99][264]['total_minutes'] /60,0)  ?></th>
                <th><?  echo number_format($line_idle_total_arr[99][265]['total_minutes'] /60,0)  ?></th>

                <th><?php echo  number_format($total_npt_min / 60,4); ?></th>
                <th></th>
                <th></th>
                <th></th>
				<th></th>
               
            </tr>
            <tr>
                <th colspan="6">CUMULATIVE HOUR:</th>

				<th><?  echo number_format($total_arr[14][266]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][267]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][268]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][269]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][270]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][271]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][272]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][273]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][274]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][275]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][276]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][277]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][278]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][279]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[1][164]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][165]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][166]['cumulative_minute'] /60,0)?></th>							
                <th><?  echo number_format($total_arr[1][167]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][168]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][169]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][170]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][172]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][172]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][173]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][174]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][175]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][176]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][177]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][178]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][179]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][180]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][181]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][182]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][183]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[1][184]['cumulative_minute'] /60,0)?></th>
                <th><?  echo number_format($total_arr[15][185]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[15][186]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[15][187]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[15][188]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[15][189]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[15][190]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[15][191]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[15][192]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[15][193]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[15][194]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[15][195]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[15][196]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[15][197]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[20][198]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[20][199]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[20][200]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[20][201]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[20][202]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[20][203]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[20][204]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[20][205]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[17][206]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[17][207]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[17][208]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[17][209]['cumulative_minute']/60,0)  ?></th>
                <th><?  echo number_format($total_arr[3][210]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[3][211]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[3][212]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[3][213]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[3][214]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[3][215]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[2][216]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[2][217]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[2][218]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[2][219]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[2][220]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[2][221]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[2][222]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[2][223]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[2][224]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[2][225]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[2][226]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[2][227]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[2][228]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[2][229]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[2][230]['cumulative_minute'] /60,0) ?></th>
                <th><?  echo number_format($total_arr[5][235]['cumulative_minute']  /60,0) ?></th>
                <th><?  echo number_format($total_arr[5][234]['cumulative_minute']  /60,0) ?></th>
                <th><?  echo number_format($total_arr[5][231]['cumulative_minute']  /60,0) ?></th>
                <th><?  echo number_format($total_arr[5][232]['cumulative_minute']  /60,0) ?></th>
                <th><?  echo number_format($total_arr[5][233]['cumulative_minute']  /60,0) ?></th>
                <th><?  echo number_format($total_arr[32][236]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[32][237]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[32][238]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[32][239]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[32][240]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[32][241]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[32][242]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[30][243]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[30][244]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[30][245]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[18][246]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[6][247]['cumulative_minute']  /60,0) ?></th>
                <th><?  echo number_format($total_arr[6][248]['cumulative_minute']  /60,0) ?></th>
                <th><?  echo number_format($total_arr[6][249]['cumulative_minute']  /60,0) ?></th>
                <th><?  echo number_format($total_arr[6][250]['cumulative_minute']  /60,0) ?></th>
                <th><?  echo number_format($total_arr[6][251]['cumulative_minute']  /60,0) ?></th>
                <th><?  echo number_format($total_arr[6][252]['cumulative_minute']  /60,0) ?></th>
                <th><?  echo number_format($total_arr[6][253]['cumulative_minute']  /60,0) ?></th>
                <th><?  echo number_format($total_arr[6][254]['cumulative_minute']  /60,0) ?></th>
                <th><?  echo number_format($total_arr[14][255]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][256]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][257]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][258]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[14][259]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[99][260]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[99][261]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[99][262]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[99][263]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[99][264]['cumulative_minute'] /60,0)  ?></th>
                <th><?  echo number_format($total_arr[99][265]['cumulative_minute'] /60,0)  ?></th>

                <th><?php echo number_format($totalCumulativeMin / 60,2); ?></th>
                <th></th>
                <th></th>
                <th></th>
				<th></th>
            </tr>
            <tr>
                <th colspan="6">TOTAL CUMULATIVE MIN:</th>
                
				<th><?  echo number_format($total_arr[14][266]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][267]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][268]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][269]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][270]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][271]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][272]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][273]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][274]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][275]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][276]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][277]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][278]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][279]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[1][164]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][165]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][166]['cumulative_minute'] )?></th>							
                <th><?  echo number_format($total_arr[1][167]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][168]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][169]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][170]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][172]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][172]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][173]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][174]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][175]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][176]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][177]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][178]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][179]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][180]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][181]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][182]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][183]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[1][184]['cumulative_minute'] )?></th>
                <th><?  echo number_format($total_arr[15][185]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[15][186]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[15][187]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[15][188]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[15][189]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[15][190]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[15][191]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[15][192]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[15][193]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[15][194]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[15][195]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[15][196]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[15][197]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[20][198]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[20][199]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[20][200]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[20][201]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[20][202]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[20][203]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[20][204]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[20][205]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[17][206]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[17][207]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[17][208]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[17][209]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[3][210]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[3][211]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[3][212]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[3][213]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[3][214]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[3][215]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[2][216]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[2][217]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[2][218]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[2][219]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[2][220]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[2][221]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[2][222]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[2][223]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[2][224]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[2][225]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[2][226]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[2][227]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[2][228]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[2][229]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[2][230]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[5][235]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[5][234]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[5][231]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[5][232]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[5][233]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[32][236]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[32][237]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[32][238]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[32][239]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[32][240]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[32][241]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[32][242]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[30][243]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[30][244]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[30][245]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[18][246]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[6][247]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[6][248]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[6][249]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[6][250]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[6][251]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[6][252]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[6][253]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[6][254]['cumulative_minute'] ) ?></th>
                <th><?  echo number_format($total_arr[14][255]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][256]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][257]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][258]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[14][259]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[99][260]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[99][261]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[99][262]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[99][263]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[99][264]['cumulative_minute'])  ?></th>
                <th><?  echo number_format($total_arr[99][265]['cumulative_minute'])  ?></th>

                <th><?php echo number_format($totalCumulativeMin,4);; ?></th>
                <th></th>
                <th></th>
                <th></th>
				<th></th>
            </tr>
            
        </tfoot>
    </table>

</div>


    <?php

	foreach (glob($user_id."_*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');
	$is_created = fwrite($create_new_excel,ob_get_contents());
	echo "####".$name;
	exit();






}




?>


<?
include('../../../../includes/common.php');
session_start();
extract($_REQUEST);
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$date = date('Y-m-d');

$user_id = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];


$userCredential = sql_select("SELECT brand_id, single_user_id FROM user_passwd where id=$user_id");
$userBuyerCredential = sql_select("SELECT buyer_id, single_user_id FROM user_passwd where id=$user_id");
$userbrand_id = $userCredential[0][csf('brand_id')];
$userbuyer_id = $userBuyerCredential[0][csf('buyer_id')];
$single_user_id = $userCredential[0][csf('single_user_id')];
$single_buyer_user_id = $userBuyerCredential[0][csf('single_user_id')];

$userbrand_idCond = "";
$filterBrandId = "";
if ($userbrand_id != '' && $single_user_id == 1) {
	$userbrand_idCond = "and id in ($userbrand_id)";
	$filterBrandId = $userbrand_id;
}

$userbuyer_idCond = "";
$filterBuyerId = "";
if ($userbuyer_id != '' && $single_buyer_user_id == 1) {
	$userbuyer_idCond = "and b.id in ($userbuyer_id)";
	$filterBuyerId = $userbuyer_id;
}

if ($action == "load_drop_down_buyer") {
	if ($data != 0) {
		echo create_drop_down("cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
		load_drop_down('requires/time_weight_details_controller', this . value, 'load_drop_down_brand', 'brand_td');
		exit();
	} else {
		echo create_drop_down("cbo_buyer_name", 130, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
		load_drop_down('requires/time_weight_details_controller', this . value, 'load_drop_down_brand', 'brand_td');
		exit();
	}
}
if ($action == "load_drop_down_brand") {
	echo create_drop_down("cbo_brand_name", 100, "select id, brand_name from lib_buyer_brand where buyer_id in($data) and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC", "id,brand_name", 1, "--Select--", "", "");
	exit();
}
if ($action == "buyer_popup") {
	echo load_html_head_contents("Buyer Info", "../../../../", 1, 1, '', '', '');
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
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(str) {

			if (str != "") str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);
			} else {
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
				name += selected_name[i] + ',';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_party_id').val(id);
			$('#hide_party_name').val(name);
		}
	</script>
	<input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
	<input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
	<?


	$permitted_buyer_id = return_field_value("buyer_id", "user_passwd", "id='" . $user_id . "'");
	if ($permitted_buyer_id) {
		$buyerCon = " and id in($permitted_buyer_id)";
	}
	$sql = "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyerCon order by buyer_name";


	echo create_list_view("tbl_list_search", "Buyer Name", "380", "380", "270", 0, $sql, "js_set_value", "id,buyer_name", "", 1, "0", $arr, "buyer_name", "", 'setFilterGrid("tbl_list_search",-1);', '0', '', 1);

	exit();
}


if ($action == "report_generate") {
	$company_name = str_replace("'", "", $cbo_company_name);
	$style_owner = str_replace("'", "", $cbo_style_owner);
	$buyer_name = str_replace("'", "", $cbo_buyer_name);
	$team_name = str_replace("'", "", $cbo_team_name);
	$team_member = str_replace("'", "", $cbo_team_member);
	$search_by = str_replace("'", "", $cbo_search_by);
	$search_string = str_replace("'", "", $txt_search_string);
	$txt_file = str_replace("'", "", $txt_file);
	$txt_ref = str_replace("'", "", $txt_ref);
	$date_from = str_replace("'", "", $txt_date_from);
	$date_to = str_replace("'", "", $txt_date_to);
	$cbo_year_selection=str_replace("'","",$cbo_year_selection);
	$cbo_year=str_replace("'","",$cbo_year);
	$rpt_type = str_replace("'", "", $rpt_type);
	$cbo_season = str_replace("'", "", $cbo_season);
	$ordstatus = str_replace("'", "", $cbo_ordstatus);
	$cbo_brand_name = str_replace("'", "", $cbo_brand_name);
	$cbo_season_year = str_replace("'", "", $cbo_season_year);
	$txt_style_ref=str_replace("'","",$txt_style_ref);

	if ($cbo_brand_name > 0) $brand_id_cond = "and a.brand_id in ($cbo_brand_name)";
	else $brand_id_cond = "";

	if ($txt_style_ref > 0) $style_ref_cond = "and  a.style_ref_no='$txt_style_ref'";
	else $style_ref_cond = "";

	$job_style_cond="";
		if(trim(str_replace("'","",$txt_style_ref))!="")
		{
			if(str_replace("'","",$style_ref_id)!="")
			{
				$job_style_cond=" and a.id in(".str_replace("'","",$style_ref_id).")";
			}
			else
			{
				$job_style_cond=" and a.style_ref_no = '".trim(str_replace("'","",$txt_style_ref))."'";
			}
		}

	if ($buyer_name == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				$buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				$buyer_id_cond2 = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else {
				$buyer_id_cond = "";
				$buyer_id_cond2 = "";
			}
		} else {
			$buyer_id_cond = "";
			$buyer_id_cond2 = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_name in ($buyer_name) "; //.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2 = " and a.buyer_id in ($buyer_name)";
	}

	if (trim($date_from) != "") $start_date = $date_from;
	if (trim($date_to) != "") $end_date = $date_to;

	/* $year_field=" and to_char(a.insert_date,'YYYY')";
	if($cbo_year_selection!=0) $year_cond=" $year_field=$cbo_year_selection"; else $year_cond=""; */
	$year_field=" and to_char(b.pub_shipment_date,'YYYY')";
	if($cbo_year!=0) $year_cond=" $year_field=$cbo_year"; else $year_cond="";
	$start_date = change_date_format($date_from, 'mm-dd-yyyy', '-', 1);
	$end_date = change_date_format($date_to, 'mm-dd-yyyy', '-', 1);


	if ($start_date != "" && $end_date != "") $date_cond = "and b.pub_shipment_date between '$start_date' and '$end_date'";
	else $date_cond = "";

	if ($rpt_type == 1) {
		$buyer_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
		$bank_name_arr = return_library_array("select id, bank_name from lib_bank", 'id', 'bank_name');
		$company_short_name_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
		$buyer_wise_season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", 'id', 'season_name');
		$company_team_name_arr = return_library_array("select id,team_name from lib_marketing_team", 'id', 'team_name');
		$company_team_member_name_arr = return_library_array("select id,team_member_name from  lib_mkt_team_member_info", 'id', 'team_member_name');
		$cbo_string_search_type = create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --","4","","","2,3,4" );

		ob_start();
	?>
		<div align="center">
			<div align="center">
				<h3 style="width:100%;" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel</h3>
				<div id="content_report_panel">
					<table width="1200" id="table_header_1" border="1" class="rpt_table" rules="all" align="left">
						<thead>
							<tr>
								<th colspan="15" ><?=$cbo_string_search_type;?></th>
							</tr>
							<tr>
								<th width="30">SL</th>
								<th width="100">Company</th>
								<th width="100">Buyer</th>
								<th width="100">Job No</th>
								<th width="100">Order Status</th>
								<th width="100">Style Ref</th>
								<th width="100">Item Name</th>
								<th width="100">Team Name</th>
								<th width="100">Team Member</th>
								<th width="100">First TOD</th>
								<th width="70">Order Qnty</th>
								<th width="50">Uom</th>
								<th width="50">Time And Weight</th>
								<th width="50">Bom Of Yarn</th>
								<th width="50">Yarn Purchase Reqs</th>
							</tr>
						</thead>
					</table>
					<div style="max-height:400px; overflow-y:scroll; float:left; width:1220px;" id="scroll_body">
						<table width="1200" border="1" class="rpt_table" rules="all" id="table_body" align="left">
							<?
							$fab_dec_cond = "listagg(cast(fabric_description as varchar2(4000)),',') within group (order by fabric_description)";
							$fabric_arr = array();
							$fab_sql = sql_select("select job_no, item_number_id, $fab_dec_cond as fabric_description from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 group by job_no, item_number_id");
							foreach ($fab_sql as $row) {
								$fabric_arr[$row[csf('job_no')]][$row[csf('item_number_id')]] = $row[csf('fabric_description')];
							}
							$i = 1;$order_qntytot = 0;$date = date('d-m-Y');

							$data_array = sql_select("SELECT a.id as job_id,a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, a.gmts_item_id, a.order_uom, a.team_leader, a.dealing_marchant, b.is_confirmed,  b.po_quantity, b.pub_shipment_date,c.style_ref_no as time_and_weight_chk, d.job_no as bom_of_yarn_chk,e.job_no as yarn_purchase_reqs_chk from  wo_po_break_down b,wo_po_details_master a LEFT JOIN sample_development_mst c on a.style_ref_no=c.style_ref_no and c.status_active=1 LEFT JOIN wo_pre_cost_fab_yarn_cost_dtls d on a.job_no=d.job_no and d.status_active=1 LEFT JOIN inv_purchase_requisition_dtls e on a.job_no=e.job_no and e.status_active=1 where  a.job_no=b.job_no_mst  and a.company_name in ($company_name)  $buyer_id_cond $job_style_cond $date_cond $brand_id_cond $year_cond and a.status_active=1 and b.status_active=1  group by a.id,a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.buyer_name,  a.style_ref_no, a.job_quantity,  a.job_no, a.gmts_item_id,  a.order_uom, a.team_leader, a.dealing_marchant, b.is_confirmed,  b.po_quantity,b.pub_shipment_date,c.style_ref_no,d.job_no,e.job_no order by a.job_no_prefix_num");
								foreach ($data_array as $row) {
									$main_data_arr[$row[csf('job_id')]]['company_name'] = $company_short_name_arr[$row[csf('company_name')]];
									$main_data_arr[$row[csf('job_id')]]['buyer_name'] = $buyer_short_name_arr[$row[csf('buyer_name')]];
									$main_data_arr[$row[csf('job_id')]]['job_no_prefix_num'] = $row[csf('job_no_prefix_num')];
									$main_data_arr[$row[csf('job_id')]]['is_confirmed'] = $order_status[$row[csf('is_confirmed')]];
									$main_data_arr[$row[csf('job_id')]]['style_ref_no'] = $row[csf('style_ref_no')];
									$main_data_arr[$row[csf('job_id')]]['gmts_item_id'] = $row[csf('gmts_item_id')];
									$main_data_arr[$row[csf('job_id')]]['dealing_marchant'] = $company_team_member_name_arr[$row[csf('dealing_marchant')]];
									$main_data_arr[$row[csf('job_id')]]['team_leader'] = $company_team_name_arr[$row[csf('team_leader')]];
									$main_data_arr[$row[csf('job_id')]]['estimated_shipdate'] = $row[csf('pub_shipment_date')];
									$main_data_arr[$row[csf('job_id')]]['po_quantity'] += $row[csf('po_quantity')];
									$main_data_arr[$row[csf('job_id')]]['order_uom'] = $unit_of_measurement[$row[csf('order_uom')]];
									$main_data_arr[$row[csf('job_id')]]['time_and_weight_chk'] = $row[csf('time_and_weight_chk')];
									$main_data_arr[$row[csf('job_id')]]['bom_of_yarn_chk'] = $row[csf('bom_of_yarn_chk')];
									$main_data_arr[$row[csf('job_id')]]['yarn_purchase_reqs_chk'] = $row[csf('yarn_purchase_reqs_chk')];
								}
								foreach ($main_data_arr as $job_id=>$value) { 
								if ($i % 2 == 0) $bgcolor = "#E9F3FF";else $bgcolor = "#FFFFFF";


								if($value['time_and_weight_chk']!=""){ $time_weight='YES';}
								else{ $time_weight='NO'; }

								if($value['bom_of_yarn_chk']!=""){ $bom_yarn='YES'; }
								else{ $bom_yarn='NO'; }

								if($value['yarn_purchase_reqs_chk']!=""){ $yarn_req='YES';}
								else{ $yarn_req='NO'; }
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle;text-align: center" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
									<td width="100" style="word-break:break-all"><?=$value['company_name']?></td>
									<td width="100" style="word-break:break-all"><?=$value['buyer_name']?></td>
									<td width="100" style="word-break:break-all"><?=$value['job_no_prefix_num']?></td>
									<td width="100" style="word-break:break-all"><?=$value['is_confirmed']?></td>
									<td width="100" style="word-break:break-all"><?=$value['style_ref_no']?></td>
									<td width="100" style="word-break:break-all">
										<? $gmts_item_id = explode(',', $value['gmts_item_id']);
										$fabric_description = "";
										for ($j = 0; $j <= count($gmts_item_id); $j++) {
											if ($fabric_description == "") $fabric_description = $fabric_arr[$job_id][$gmts_item_id[$j]];
											else $fabric_description .= ',' . $fabric_arr[$job_id][$gmts_item_id[$j]];
											echo $garments_item[$gmts_item_id[$j]];
										}
										?></td>
									<td width="100" style="word-break:break-all"><?=$value['dealing_marchant']?></td>
									<td width="100" style="word-break:break-all"><?=$value['team_leader']?></td>
									<td width="100" style="word-break:break-all"><? echo change_date_format($value['estimated_shipdate'], 'dd-mm-yyyy', '-');?></td>
									<td width="70" align="right" style="word-break:break-all">
										<?
										echo number_format($value['po_quantity'], 0);
										$order_qntytot = $order_qntytot + $value['po_quantity'];
										?></td>
									<td width="50" style="word-break:break-all"><?=$value['order_uom']?></td>
									<td width="50" style="word-break:break-all"><? echo $time_weight; ?></td>
									<td width="50" style="word-break:break-all"><? echo $bom_yarn; ?></td>
									<td width="50" style="word-break:break-all"><? echo $yarn_req ?></td>
							</tr>
							<?
								$i++;
							}
							?>
							
						
					</table>
				</div>
				<table width="1200" id="report_table_footer" border="1" class="rpt_table" rules="all" align="left">
					<tfoot>
					<tr>
							<th width="30">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">Total</th>
							<th width="70" align="right" id="tot_order_qty">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="50">&nbsp;</th>
						</tr>
					</tfoot>
				</table>
				<script>setFilterGrid('list_view',-1);</script>
				<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
				<?
				
							}
	foreach (glob("$user_id*.xls") as $filename) {
		if (@filemtime($filename) < (time() - $seconds_old))
			@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	$filename = $user_id . "_" . $name . ".xls";
	// echo "$total_data####$filename####$rpt_type";
	disconnect($con);
	exit();
}

?>
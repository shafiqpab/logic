<?
/*-------------------------------------------- Comments
Version          :  V1
Purpose			 : This form will create print Booking
Functionality	 :
JS Functions	 :
Created by		 : Aziz
Creation date 	 :
Requirment Client:
Requirment By    :
Requirment type  :
Requirment       :
Affected page    :
Affected Code    :
DB Script        :
Updated by 		 :
Update date		 :
QC Performed BY	 :
QC Date			 :
Comments		 : From this version oracle conversion is start
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
include('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$permission = $_SESSION['page_permission'];
//---------------------------------------------------- Start---------------------------------------------------------------------------
$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
$size_library = return_library_array("select id,size_name from lib_size", "id", "size_name");
$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", "id", "buyer_name");
$country_name_library = return_library_array("select id,country_name from lib_country", "id", "country_name");

if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
}
if ($action == "load_attention") {
	$supplier_name = return_field_value("contact_person", "lib_supplier", "id ='" . $data . "' and is_deleted=0 and status_active=1");
	echo $supplier_name;
	exit();
}

if ($action == "load_drop_down_supplier") {
	$data = explode("_", $data);
	$pay_mode_id = $data[0];
	$tag_buyer_id = $data[1];
	$tag_comp_id = $data[2];
	if ($pay_mode_id == 1 || $pay_mode_id == 2 || $pay_mode_id == 4) {
		$sql = "select c.id, c.supplier_name as supplier_name from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$tag_comp_id  and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";
	} else {
		$sql = "select c.id, c.company_name as supplier_name from lib_company c where c.status_active=1 and c.is_deleted=0 group by c.id, c.company_name order by company_name";
	}
	echo create_drop_down("cbo_supplier_name", 172, $sql, "id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/embellishment_wo_without_order_controller');", 0);
	/*$result = sql_select($sql);
	$supplierArr = array();
	foreach($result as $key => $val){
		$supplierArr[$key]["id"]=$val[csf("id")];
		$supplierArr[$key]["label"]=$val[csf("label")];
	}
	echo json_encode($supplierArr);*/
	exit();
}
if ($action == "load_drop_down_attention") {
	$data = explode('_', $data);
	$supp_id = $data[0];
	$paymode_id = $data[1];
	if ($paymode_id != 3 && $paymode_id != 5) {
		//echo "select contact_person from lib_supplier where id =".$supp_id." and is_deleted=0 and status_active=1";
		$supplier_com_att = return_field_value("contact_person", "lib_supplier", "id =" . $supp_id . " and is_deleted=0 and status_active=1");
	} else {
		$supplier_com_att = return_field_value("contract_person", "lib_company", "id =" . $supp_id . " and is_deleted=0 and status_active=1");
	}
	echo "document.getElementById('txt_attention').value = '" . $supplier_com_att . "';\n";
	exit();
}

if ($action == "fabric_emb_item_popup") {
	echo load_html_head_contents("Booking Search", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count;
			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function onlyUnique(value, index, self) {
			return self.indexOf(value) === index;
		}

		var selected_id = new Array();
		var selected_name = new Array();
		var selected_item = new Array();
		var selected_po = new Array();

		function js_set_value(str) {
			if ($("#search" + str).css("display") != 'none') {
				toggle(document.getElementById('search' + str), '#FFFFCC');
				if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
					selected_id.push($('#txt_individual_id' + str).val());
					selected_name.push($('#txt_job_no' + str).val());
					selected_item.push($('#precost_emb_id' + str).val());
					selected_po.push($('#txt_po_id' + str).val());
				} else {
					for (var i = 0; i < selected_id.length; i++) {
						if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
					}
					selected_id.splice(i, 1);
					selected_name.splice(i, 1);
					selected_item.splice(i, 1);
					selected_po.splice(i, 1);
				}
			}
			var id = '';
			var job = '';
			var precost_emb_id = '';
			var txt_po_id = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				job += selected_name[i] + ',';
				precost_emb_id += selected_item[i] + ',';
				txt_po_id += selected_po[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			job = job.substr(0, job.length - 1);
			precost_emb_id = precost_emb_id.substr(0, precost_emb_id.length - 1);
			txt_po_id = txt_po_id.substr(0, txt_po_id.length - 1);
			$('#txt_selected_id').val(id);
			$('#txt_job_id').val(job);
			$('#emb_id').val(precost_emb_id);
			$('#txt_selected_po').val(txt_po_id);
		}

		function check() {

			var cbo_company_name = document.getElementById('cbo_company_name').value;
			var cbo_buyer_name = document.getElementById('cbo_buyer_name').value;
			var cbo_supplier_name = document.getElementById('cbo_supplier_name').value;
			var cbo_year_selection = document.getElementById('cbo_year_selection').value;

			var cbo_currency = document.getElementById('cbo_currency').value;
			var txt_style = document.getElementById('txt_style').value;
			var txt_date_from = document.getElementById('txt_date_from').value;
			var txt_date_to = document.getElementById('txt_date_to').value;
			//var txt_order_search=document.getElementById('txt_order_search').value;


			var cbo_item = document.getElementById('cbo_item').value;
			var txt_req_no = document.getElementById('txt_req_no').value;
			show_list_view(cbo_company_name + '_' + cbo_buyer_name + '_' + cbo_supplier_name + '_' + cbo_year_selection + '_' + cbo_currency + '_' + txt_style + '_' + cbo_item + '_' + txt_req_no + '_' + txt_date_from + '_' + txt_date_to + '_' + '<? echo $txt_booking_no; ?>', 'create_fnc_process_data', 'search_div', 'embellishment_wo_without_order_controller', setFilterGrid('tbl_list_search', -1))
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="650" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>
						<tr>
							<th width="100">Style Ref </th>

							<th width="60">Year</th>
							<th width="100">Req. No </th>
							<th width="120">Item Name</th>
							<th width="150" colspan="2">Req. Date</th>
							<th>&nbsp;
								<input type="hidden" style="width:20px" name="txt_garments_nature" id="txt_garments_nature" value="<? echo $garments_nature; ?>" />
								<input type="hidden" name="cbo_company_name" id="cbo_company_name" value="<? echo $company_id; ?>" />
								<input type="hidden" style="width:20px" name="cbo_buyer_name" id="cbo_buyer_name" value="<? echo $cbo_buyer_name; ?>" />
								<input type="hidden" name="cbo_currency" id="cbo_currency" value="<? echo $cbo_currency; ?>" />
								<input type="hidden" style="width:20px" name="cbo_supplier_name" id="cbo_supplier_name" value="<? echo $cbo_supplier_name; ?>" />
							</th>
						</tr>
					</thead>
					<tr>
						<td><input name="txt_style" id="txt_style" class="text_boxes" style="width:90px"></td>

						<td><? echo create_drop_down("cbo_year_selection", 60, $year, "", 1, "-- Select --", date('Y'), "", 0);	?></td>
						<td><input name="txt_req_no" id="txt_req_no" class="text_boxes" style="width:100px"></td>
						<td><? echo create_drop_down("cbo_item", 120, $emblishment_name_array, "", 1, "-- Select Emb Name --", $selected, "", 0); ?></td>
						<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> &nbsp;
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="check();" style="width:60px;" />
						</td>
					</tr>
				</table>

			</form>
			<div id="search_div"></div>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_fnc_process_data") {
	$data = explode('_', $data);
	$company_id = $data[0];
	$cbo_buyer_name = $data[1];
	$cbo_supplier_name = $data[2];
	$cbo_year_selection = $data[3];
	$cbo_currency = $data[4];

	$txt_style = $data[5];
	$cbo_item = $data[6];
	$req_no = $data[7];
	$from_date = $data[8];
	$to_date = $data[9];
	$booking_no = $data[10];
	//echo $from_date.'='.$to_date;
	if ($db_type == 2) {
		$from_date = change_date_format($from_date, '', '', 1);
		$to_date = change_date_format($to_date, '', '', 1);
	}

	if ($from_date && $to_date) $data_cond = "and a.requisition_date between '$from_date' and '$to_date'";
	else  $data_cond = "";

	if ($txt_style != "") $style_cond = " and a.style_ref_no='$txt_style'";
	else $style_cond = $txt_style;
	if ($req_no != "") $req_cond = " and a.requisition_number_prefix_num='$req_no'";
	else $req_cond = $req_no;
	if ($cbo_item != 0) $itemgroup_cond = " and b.name_re=$cbo_item";
	else $itemgroup_cond = "";
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	//
?>
	</head>

	<body>
		<div style="width:1020px;">
			<?
			extract($_REQUEST);
			?>
			<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
			<input type="hidden" name="emb_id" id="emb_id" value="" />
			<input type="hidden" name="txt_job_id" id="txt_job_id" value="" />
			<input type="hidden" name="txt_selected_po" id="txt_selected_po" value="" />
			<table cellspacing="0" cellpadding="0" rules="all" width="1020" class="rpt_table">
				<thead>
					<th width="20">SL</th>
					<th width="100">Buyer</th>
					<th width="50">Year</th>
					<th width="100">Booking No</th>

					<th width="100">Req. No</th>
					<th width="100">Style. No</th>
					<th width="100">Garments Item</th>
					<th width="100">Embl. Name</th>
					<th width="100">Embl. Type</th>
					<th width="100">Body Part</th>
					<th width="80">Req. Qty</th>
					<th width="">UOM</th>

				</thead>
			</table>
			<div style="width:1020px; overflow-y:scroll; max-height:350px;" id="buyer_list_view">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="tbl_list_search">
					<?
					if ($db_type == 0) {

						$year_field = "YEAR(a.insert_date) as year";
					} else if ($db_type == 2) {
						$year_field = "to_char(a.insert_date,'YYYY') as year";
					}

					//print_r($req_qty_arr_wash);

					$req_no_arr = array();
					$sql_req_booking = sql_select("select  b.booking_no,b.style_id from wo_non_ord_samp_booking_dtls b  where b.status_active=1 and b.is_deleted=0  and b.style_id>0 ");
					foreach ($sql_req_booking as $row) {

						$req_no_arr[$row[csf('style_id')]]['booking_no'] = $row[csf('booking_no')];
					}
					unset($sql_req_booking);

					$sql = "select a.id,a.requisition_number_prefix_num,a.requisition_number, $year_field,a.company_id, a.buyer_name, a.style_ref_no,b.id as emb_dtls_id, b.name_re as emb_name, b.type_re as emb_type,b.body_part_id, b.gmts_item_id_re, (b.fin_fab_qnty) AS fin_fab_qnty
        from sample_development_mst a, sample_development_fabric_acc b where a.id=b.sample_mst_id and
		 b.form_type=3 and a.company_id=$company_id and a.entry_form_id=203 and b.fin_fab_qnty>0 and 
        a.buyer_name=$cbo_buyer_name and (b.supplier_id = $cbo_supplier_name or b.supplier_id= 0 )  
        " . $buyer_cond_test . " $itemgroup_cond $data_cond  $req_cond $style_cond order by a.id, b.id";

					$i = 1;
					$req_qty = 0;
					$req_amount = 0;
					$rate = 0;
					$total_req = 0;
					$total_amount = 0;
					$nameArray = sql_select($sql);

					foreach ($nameArray as $selectResult) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";


						//echo $selectResult[csf('fin_fab_qnty')]."==".$bal_woq."==".$cu_wo_qnty."<br/>" ;
						//if($bal_woq>0 && ($cu_wo_qnty=="" || $cu_wo_qnty==0))
						//{
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
							<td width="20"><? echo $i; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('emb_dtls_id')]; ?>" />
								<input type="hidden" name="precost_emb_id" id="precost_emb_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>" />
								<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('requisition_number')]; ?>" />
								<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('emb_dtls_id')]; ?>" />
								<input type="hidden" name="hiddemb_name" id="hiddemb_name<?php echo $i ?>" value="<? echo $selectResult[csf('emb_name')]; ?>" />
							</td>
							<td width="100">
								<p><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p>
							</td>
							<td width="50">
								<p><? echo $selectResult[csf('year')]; ?></p>
							</td>
							<td width="100">
								<div style="width:100px; word-wrap:break-word;"><? echo  $req_no_arr[$selectResult[csf('id')]]['booking_no']; //$selectResult[csf('style_ref_no')];
																				?></div>
							</td>

							<td width="100">
								<div style="width:100px; word-wrap:break-word;"><? echo $selectResult[csf('requisition_number')]; ?></div>
							</td>
							<td width="100">
								<div style="width:100px; word-wrap:break-word;"><? echo $selectResult[csf('style_ref_no')]; //$emblishment_name_array[$selectResult[csf('name_re')]];
																				?></div>
							</td>
							<td width="100" id="td_item_des<?php echo $i; ?>">
								<div style="width:100px; word-wrap:break-word;">
									<?
									echo $garments_item[$selectResult[csf('gmts_item_id_re')]];
									if ($selectResult[csf('emb_name')] == 1) $emb_type = $emblishment_print_type[$selectResult[csf('emb_type')]];
									if ($selectResult[csf('emb_name')] == 2) $emb_type = $emblishment_embroy_type[$selectResult[csf('emb_type')]];
									if ($selectResult[csf('emb_name')] == 3) $emb_type = $emblishment_wash_type[$selectResult[csf('emb_type')]];
									if ($selectResult[csf('emb_name')] == 4) $emb_type = $emblishment_spwork_type[$selectResult[csf('emb_type')]];
									if ($selectResult[csf('emb_name')] == 5) $emb_type = $emblishment_gmts_type[$selectResult[csf('emb_type')]];


									?>
								</div>
							</td>
							<td width="100">
								<div style="width:100px; word-wrap:break-word;"><? echo   $emblishment_name_array[$selectResult[csf('emb_name')]]; //$emb_type;//;
																				?></div>
							</td>
							<td width="100" align="right"><? echo $emb_type; //number_format($req_qty,4); 
															?></td>
							<td width="100">
								<div style="width:100px; word-wrap:break-word;"><? echo $body_part[$selectResult[csf('body_part_id')]]; ?></div>
							</td>

							<td width="80" align="right">
								<p><? echo number_format($selectResult[csf('fin_fab_qnty')], 4); ?></p>
							</td>
							<td align="right">Pcs <? //echo number_format($amount,2); 
													?></td>
						</tr>
					<?
						$i++;
						$total_amount += $amount;
						//}



					}
					?>
				</table>
			</div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" class="rpt_table">
				<tfoot>
					<th width="20">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th id="value_total_amount"><? echo number_format($total_amount, 2); ?></th>
				</tfoot>
			</table>

			<table width="790" cellspacing="0" cellpadding="0" style="border:none" align="center">
				<tr>
					<td align="center" height="30" valign="bottom">
						<div style="width:100%">
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
								<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
						</div>
					</td>
				</tr>
			</table>
			<script>
				var tableFilters = {
					col_operation: {
						id: ["value_total_req", "value_total_amount"],
						col: [11, 17],
						operation: ["sum", "sum"],
						write_method: ["innerHTML", "innerHTML"]
					}
				}
				setFilterGrid('tbl_list_search', -1, tableFilters)
			</script>
		</div>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "generate_fabric_booking") {
	extract($_REQUEST);
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	if ($garments_nature == 0) {
		$garment_nature_cond = "";
	} else {
		$garment_nature_cond = " and a.garments_nature=$garments_nature";
	}
	$param = implode(",", array_unique(explode(",", str_replace("'", "", $param))));
	$data = implode(",", array_unique(explode(",", str_replace("'", "", $data))));
	$req_id = implode(",", array_unique(explode(",", str_replace("'", "", $pre_cost_id))));


	/*$cu_booking_arr=array();
	$sql_cu_booking=sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.gmt_item, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and a.company_name=$cbo_company_name  and c.status_active=1 and c.is_deleted=0 and c.booking_type=6   group by c.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.gmt_item");
	foreach($sql_cu_booking as $row_cu_booking){
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('gmt_item')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('gmt_item')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_amount')];
	}*/
	$sql_cu_booking = sql_select("select a.id as booking_id, a.embl_cost_dtls_id, a.booking_no, a.booking_type,a.delivery_date,a.gmt_item_id,a.req_id, a.req_no,a.req_booking_no,a.emb_name,a.emb_type,a.body_part_id,a.uom_id, a.sensitivity,a.cons_break_down,a.wo_qnty, a.exchange_rate,a.rate, a.amount, (a.wo_qnty) AS cu_wo_qnty,a.rate,a.amount as cu_amount,b.fin_fab_qnty as req_qty,b.amount as req_amt
        from wo_non_ord_embl_booking_dtls a, sample_development_fabric_acc b where b.id=a.embl_cost_dtls_id and b.form_type=3  and a.entry_form_id=399 and a.wo_qnty>0 and b.id in($param) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  order by a.id");
	foreach ($sql_cu_booking as $row_cu_booking) {
		$cu_booking_arr[$row_cu_booking[csf('req_booking_no')]][$row_cu_booking[csf('embl_cost_dtls_id')]][$row_cu_booking[csf('gmt_item_id')]]['cu_woq'][$row_cu_booking[csf('req_id')]] = $row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('req_booking_no')]][$row_cu_booking[csf('embl_cost_dtls_id')]][$row_cu_booking[csf('gmt_item_id')]]['cu_amount'][$row_cu_booking[csf('req_id')]] = $row_cu_booking[csf('cu_amount')];
	}
	unset($sql_cu_booking);

	$req_no_arr = array();
	$sql_req_booking = "select  a.exchange_rate,a.currency_id,b.booking_no,b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b  where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.style_id>0 and b.style_id in($req_id)";
	$sql_req_booking_result = sql_select($sql_req_booking);
	foreach ($sql_req_booking_result as $row) {

		$req_no_arr[$row[csf('style_id')]]['booking_no'] = $row[csf('booking_no')];
		$req_no_arr[$row[csf('style_id')]]['exchange_rate'] = $row[csf('exchange_rate')];
		$req_no_arr[$row[csf('style_id')]]['currency_id'] = $row[csf('currency_id')];
	}
	unset($sql_req_booking_result);

	 $sql = "select a.id,a.requisition_number_prefix_num,a.requisition_number,a.company_id, a.buyer_name, a.style_ref_no,b.id as emb_dtls_id, b.name_re as emb_name, b.type_re as emb_type,b.body_part_id, b.gmts_item_id_re, (b.fin_fab_qnty) AS fin_fab_qnty,b.rate,b.amount,b.uom_id
        from sample_development_mst a, sample_development_fabric_acc b where a.id=b.sample_mst_id and b.form_type=3 and a.company_id=$cbo_company_name and a.entry_form_id=203 and b.fin_fab_qnty>0 and  b.id in($param) order by a.id, b.id";


	$i = 1;
	$nameArray = sql_select($sql);
?>

	<input type="hidden" id="strdata" value='<? //echo json_encode($job_and_trimgroup_level); 
												?>' style="background-color:#CCC" />
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table">
		<thead>
			<th width="20">SL</th>
			<th width="100">Booking No</th>
			<th width="100">Req. No</th>
			<th width="100">Gmt.Item</th>
			<th width="100">Emb. Name</th>
			<th width="150">Body Part</th>
			<th width="150">Emb. Type</th>
			<th width="70">Req. Qnty</th>
			<th width="50">UOM</th>
			<th width="80">CU WOQ</th>
			<th width="80">Bal WOQ</th>
			<th width="100">Sensitivity</th>
			<th width="80">WOQ PCS</th>
			<th width="55">Exchange rate</th>
			<th width="80">Rate</th>
			<th width="80">Amount</th>
			<th width="">Delv. Date</th>
		</thead>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table" id="tbl_list_search">
		<tbody>
			<?

			foreach ($nameArray as $selectResult) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";

				$cbo_currency_job = $req_no_arr[$selectResult[csf('id')]]['currency_id'];
				$exchange_rate = $req_no_arr[$selectResult[csf('id')]]['exchange_rate'];
				//$exchange_rate=$selectResult[csf('exchange_rate')];
				if ($cbo_currency == $cbo_currency_job) {
					$exchange_rate = 1;
				}

				$req_amount_cons_uom = $selectResult[csf('amount')];
				$req_qnty_cons_uom = $selectResult[csf('fin_fab_qnty')];
				$rate_cons_uom = $req_amount_cons_uom / $req_qnty_cons_uom;
				$req_booking_no = $req_no_arr[$selectResult[csf('id')]]['booking_no'];
				//fin_fab_qnty
				$cu_woq = $cu_booking_arr[$req_booking_no][$selectResult[csf('emb_dtls_id')]][$selectResult[csf('gmts_item_id_re')]]['cu_woq'][$selectResult[csf('id')]];
				$cu_amount = $cu_booking_arr[$req_booking_no][$selectResult[csf('emb_dtls_id')]][$selectResult[csf('gmts_item_id_re')]]['cu_amount'][$selectResult[csf('id')]];

				$bal_woq = def_number_format($req_qnty_cons_uom - $cu_woq, 5, "");
				$amount = def_number_format($rate_cons_uom * $bal_woq, 5, "");

				if ($selectResult[csf('emb_name')] == 1) $emb_type_name = $emblishment_print_type[$selectResult[csf('emb_type')]];
				if ($selectResult[csf('emb_name')] == 2) $emb_type_name = $emblishment_embroy_type[$selectResult[csf('emb_type')]];
				if ($selectResult[csf('emb_name')] == 3) $emb_type_name = $emblishment_wash_type[$selectResult[csf('emb_type')]];
				if ($selectResult[csf('emb_name')] == 4) $emb_type_name = $emblishment_spwork_type[$selectResult[csf('emb_type')]];
				if ($selectResult[csf('emb_name')] == 5) $emb_type_name = $emblishment_gmts_type[$selectResult[csf('emb_type')]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>">
					<td width="20"><? echo $i; ?></td>
					<td width="100">
						<? echo  $req_no_arr[$selectResult[csf('id')]]['booking_no']; ?>
						<input type="hidden" id="txtreqno_<? echo $i; ?>" value="<? echo $selectResult[csf('requisition_number')]; ?>" style="width:30px" class="text_boxes" readonly />
					</td>
					<td width="100"> <? echo $selectResult[csf('requisition_number')]; ?>
						<input type="hidden" id="txtbookingid_<? echo $i; ?>" value="" readonly />
						<input type="hidden" id="txtreqId_<? echo $i; ?>" value="<? echo $selectResult[csf('id')]; ?>" readonly />
						<input type="hidden" id="ReqbookingNo_<? echo $i; ?>" value="<? echo  $req_no_arr[$selectResult[csf('id')]]['booking_no']; ?>" readonly />

					</td>
					<td width="100">
						<? echo $garments_item[$selectResult[csf('gmts_item_id_re')]]; ?>
						<input type="hidden" id="txtgmtitemid_<? echo $i; ?>" value="<? echo $selectResult[csf('gmts_item_id_re')]; ?>" readonly />
					</td>
					<td width="100">
						<? echo $emblishment_name_array[$selectResult[csf('emb_name')]]; ?>
						<input type="hidden" id="txtembcostid_<? echo $i; ?>" value="<? echo $selectResult[csf('emb_dtls_id')]; ?>" readonly />
						<input type="hidden" id="emb_name_<? echo $i; ?>" value="<? echo $selectResult[csf('emb_name')]; ?>" readonly />
					</td>
					<td width="150">
						<? echo $body_part[$selectResult[csf('body_part_id')]]; ?>
						<input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="body_part_id_<? echo $i; ?>" value="<? echo $selectResult[csf('body_part_id')]; ?>" />
					</td>
					<td width="150">
						<? echo $emb_type_name; ?>
						<input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="emb_type_<? echo $i; ?>" value="<? echo $selectResult[csf('emb_type')]; ?>" />
					</td>
					<td width="70" align="right">
						<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i; ?>" value="<? echo number_format($req_qnty_cons_uom, 4, '.', ''); ?>" readonly />
						<input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i; ?>" value="<? echo number_format($req_amount_cons_uom, 4, '.', ''); ?>" readonly />

					</td>
					<td width="50">  
					Pcs 
						<?
						//echo $unit_of_measurement[$selectResult[csf('uom_id')]];
						//echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('gmt_item')]][cons_uom]];
						?>
						<input type="hidden" id="txtuom_<? echo $i; ?>" value="<? //echo $sql_lib_item_group_array[$selectResult[csf('gmt_item')]][cons_uom];
																				?>" readonly />
					</td>
					<td width="80" align="right">
						<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i; ?>" value="<? echo number_format($selectResult[csf('cu_woq')], 4, '.', ''); ?>" readonly />
						<input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i; ?>" value="<? echo number_format($selectResult[csf('cu_amount')], 4, '.', ''); ?>" readonly />
					</td>
					<td width="80" align="right">
						<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i; ?>" value="<? echo number_format($bal_woq, 4, '.', ''); ?>" readonly />
					</td>
					<td width="100" align="right">
						<? echo create_drop_down("cbocolorsizesensitive_" . $i, 100, $size_color_sensitive, "", 1, "--Select--", "4", "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)", 1, "1,2,3,4"); ?>
					</td>
					<td width="80" align="right">

						<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i; ?>" value="<? echo number_format($bal_woq, 4, '.', ''); ?>" onClick="open_consumption_popup('requires/embellishment_wo_without_order_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtreqId_<? echo $i; ?>',<? echo $i; ?>)" readonly />
					</td>
					<td width="55" align="right">
						<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i; ?>" value="<? echo $exchange_rate; ?>" readonly />

					</td>
					<td width="80" align="right">
						<?
						$ratetexcolor = "#000000";
						$decimal = explode(".", $rate_cons_uom);
						if (strlen($decimal[1] > 6)) {
							$ratetexcolor = "#F00";
						}
						?>
						<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;   text-align:right; color:<? echo $ratetexcolor; ?>; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i; ?>" value="<? echo $rate_cons_uom; ?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />

						<input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i; ?>" value="<? echo $rate_cons_uom; ?>" readonly />

					</td>
					<td width="80" align="right">
						<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i; ?>" value="<? echo number_format($amount, 4, '.', ''); ?>" readonly />
					</td>
					<td width="" align="right">
						<input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i; ?>" class="datepicker" value="<? echo $txt_delivery_date; ?>" readonly />
						<input type="hidden" id="consbreckdown_<? echo $i; ?>" value="" />

					</td>
				</tr>
			<?
				$i++;
			}


			?>
		</tbody>
	</table>
	<table width="1500" class="rpt_table" border="0" rules="all">
		<tfoot>
			<tr>
				<th width="20">&nbsp;</th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="150"></th>
				<th width="150"></th>
				<th width="70"><? echo $tot_req_qty; ?></th>
				<th width="50"></th>
				<th width="80"><? echo $tot_cu_woq; ?></th>
				<th width="80"><? echo $tot_bal_woq; ?></th>
				<th width="100"></th>
				<th width="80"></th>
				<th width="55"></th>
				<th width="80"></th>
				<th width="80"><input type="hidden" id="tot_amount" value="<? echo  $total_amount; ?>" style="width:80px" readonly /></th>
				<th width=""><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly /></th>
			</tr>
		</tfoot>
	</table>
	<table width="1100" colspan="14" cellspacing="0" class="" border="0">
		<tr>
			<td align="center" class="button_container">
				<?
				echo load_submit_buttons($permission, "fnc_trims_booking_dtls", 0, 0, "reset_form('','booking_list_view','','','')", 2);
				?>
			</td>
		</tr>
	</table>
<?
	exit();
}

if ($action == "consumption_popup") {
	echo load_html_head_contents("Consumption Entry", "../../../", 1, 1, $unicode, '', '');
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$size_library = return_library_array("select id, size_name from lib_size", "id", "size_name");
?>
	<script>
		var str_gmtssizes = [<? echo substr(return_library_autocomplete("select size_name from  lib_size", "size_name"), 0, -1); ?>];
		var str_diawidth = [<? echo substr(return_library_autocomplete("select color_name from lib_color", "color_name"), 0, -1); ?>];

		function poportionate_qty_old(qty) {
			var po_qty = document.getElementById('po_qty').value;
			var txtwoq_qty = document.getElementById('txtwoq_qty').value;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			for (var i = 1; i <= rowCount; i++) {
				var pcs = $('#pcsset_' + i).val();
				var txtwoq_cal = number_format_common((txtwoq_qty / po_qty) * (pcs), 5, 0);
				$('#qty_' + i).val(txtwoq_cal);
				calculate_requirement(i)
			}
			set_sum_value('qty_sum', 'qty_')
		}

		function poportionate_qty(qty) {
			var txtwoq = document.getElementById('txtwoq').value;
			var txtwoq_qty = document.getElementById('txtwoq_qty').value * 1;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			for (var i = 1; i <= rowCount; i++) {
				var poreqqty = $('#poreqqty_' + i).val();
				var txtwoq_cal = (txtwoq_qty / txtwoq) * (poreqqty);
				//alert(txtwoq_cal);
				if (i == 50) {
					//alert(txtwoq_cal+'='+txtwoq_qty+'='+txtwoq+'='+poreqqty);
				}
				$('#qty_' + i).val(number_format(txtwoq_cal, 4, '.', ''));
				calculate_requirement(i)
			}
			set_sum_value('qty_sum', 'qty_')
			var j = i - 1;
			var qty_sum = document.getElementById('qty_sum').value * 1;
			if (qty_sum > txtwoq_qty) {
				//alert(txtwoq_cal+'='+qty_sum+'-'+txtwoq_qty);
				$('#qty_' + j).val(number_format_common(txtwoq_cal * 1 - (qty_sum - txtwoq_qty), 5, 0))
			} else if (qty_sum < txtwoq_qty) {
				$('#qty_' + j).val(number_format_common((txtwoq_cal * 1) + (txtwoq_qty - qty_sum), 5, 0))
			} else {
				$('#qty_' + j).val(number_format_common(txtwoq_cal, 5, 0));
			}
			set_sum_value('qty_sum', 'qty_');
			calculate_requirement(j)
		}

		function calculate_requirement(i) {
			var process_loss_method_id = document.getElementById('process_loss_method_id').value;
			var cons = (document.getElementById('qty_' + i).value) * 1;
			var processloss = (document.getElementById('excess_' + i).value) * 1;
			var WastageQty = '';
			if (process_loss_method_id == 1) {
				WastageQty = cons + cons * (processloss / 100);
			} else if (process_loss_method_id == 2) {
				var devided_val = 1 - (processloss / 100);
				var WastageQty = parseFloat(cons / devided_val);
			} else {
				WastageQty = 0;
			}
			WastageQty = number_format_common(WastageQty, 5, 0);
			document.getElementById('woqny_' + i).value = WastageQty;
			set_sum_value('woqty_sum', 'woqny_')
			calculate_amount(i);
		}

		function set_sum_value(des_fil_id, field_id) {
			if (des_fil_id == 'qty_sum') var ddd = {
				dec_type: 5,
				comma: 0,
				currency: 0
			};
			if (des_fil_id == 'excess_sum') var ddd = {
				dec_type: 5,
				comma: 0,
				currency: 0
			};
			if (des_fil_id == 'woqty_sum') var ddd = {
				dec_type: 5,
				comma: 0,
				currency: 0
			};
			if (des_fil_id == 'amount_sum') var ddd = {
				dec_type: 5,
				comma: 0,
				currency: 0
			};
			if (des_fil_id == 'pcs_sum') var ddd = {
				dec_type: 6,
				comma: 0
			};
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			math_operation(des_fil_id, field_id, '+', rowCount, ddd);
		}

		function copy_value(value, field_id, i) {
			var gmtssizesid = document.getElementById('gmtssizesid_' + i).value;
			var pocolorid = document.getElementById('pocolorid_' + i).value;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			var copy_basis = $('input[name="copy_basis"]:checked').val()

			for (var j = i; j <= rowCount; j++) {
				if (field_id == 'des_') {
					if (copy_basis == 0) document.getElementById(field_id + j).value = value;
					if (copy_basis == 1) {
						if (gmtssizesid == document.getElementById('gmtssizesid_' + j).value) {
							document.getElementById(field_id + j).value = value;
						}
					}
					if (copy_basis == 2) {
						if (pocolorid == document.getElementById('pocolorid_' + j).value) {
							document.getElementById(field_id + j).value = value;
						}
					}
				}
				if (field_id == 'itemcolor_') {
					if (copy_basis == 0) {
						document.getElementById(field_id + j).value = value;
					}
					if (copy_basis == 1) {
						if (pocolorid == document.getElementById('pocolorid_' + j).value) {
							document.getElementById(field_id + j).value = value;
						}
					}
					if (copy_basis == 2) {
						if (pocolorid == document.getElementById('pocolorid_' + j).value) {
							document.getElementById(field_id + j).value = value;
						}
					}
				}

				if (field_id == 'itemsizes_') {
					if (copy_basis == 0) {
						document.getElementById(field_id + j).value = value;
					}
					if (copy_basis == 1) {
						if (gmtssizesid == document.getElementById('gmtssizesid_' + j).value) {
							document.getElementById(field_id + j).value = value;
						}
					}
					if (copy_basis == 2) {
						if (pocolorid == document.getElementById('pocolorid_' + j).value) {
							document.getElementById(field_id + j).value = value;
						}
					}
				}
				if (field_id == 'qty_') {
					if (copy_basis == 0) {
						document.getElementById(field_id + j).value = value;
						calculate_requirement(j)
						set_sum_value('qty_sum', 'qty_');
					}
					if (copy_basis == 1) {
						if (gmtssizesid == document.getElementById('gmtssizesid_' + j).value) {
							document.getElementById(field_id + j).value = value;
							calculate_requirement(j)
							set_sum_value('qty_sum', 'qty_');
						}
					}
					if (copy_basis == 2) {
						if (pocolorid == document.getElementById('pocolorid_' + j).value) {
							document.getElementById(field_id + j).value = value;
							calculate_requirement(j)
							set_sum_value('qty_sum', 'qty_');
						}
					}
				}
				if (field_id == 'excess_') {
					if (copy_basis == 0) {
						document.getElementById(field_id + j).value = value;
						calculate_requirement(j)
					}
					if (copy_basis == 1) {
						if (gmtssizesid == document.getElementById('gmtssizesid_' + j).value) {
							document.getElementById(field_id + j).value = value;
							calculate_requirement(j)
						}
					}
					if (copy_basis == 2) {
						if (pocolorid == document.getElementById('pocolorid_' + j).value) {
							document.getElementById(field_id + j).value = value;
							calculate_requirement(j)
						}
					}
				}
				if (field_id == 'rate_') {
					if (copy_basis == 0) {
						document.getElementById(field_id + j).value = value;
						calculate_amount(j)
					}
					if (copy_basis == 1) {
						if (gmtssizesid == document.getElementById('gmtssizesid_' + j).value) {
							document.getElementById(field_id + j).value = value;
							calculate_amount(j)
						}
					}
					if (copy_basis == 2) {
						if (pocolorid == document.getElementById('pocolorid_' + j).value) {
							document.getElementById(field_id + j).value = value;
							calculate_amount(j)
						}
					}
				}
			}
		}

		function calculate_amount(i) {
			var rate = (document.getElementById('rate_' + i).value) * 1;
			var woqny = (document.getElementById('woqny_' + i).value) * 1;
			var amount = number_format_common((rate * woqny), 5, 0);
			document.getElementById('amount_' + i).value = amount;
			set_sum_value('amount_sum', 'amount_');
			calculate_avg_rate()
		}

		function calculate_avg_rate() {
			var woqty_sum = document.getElementById('woqty_sum').value;
			var amount_sum = document.getElementById('amount_sum').value;
			var avg_rate = number_format_common((amount_sum / woqty_sum), 5, 0);
			document.getElementById('rate_sum').value = avg_rate;
		}

		function js_set_value() {
			var reg = /[^a-zA-Z0-9!@#$%^,;.:<>{}?\+|\[\]\- \/]/g;
			var row_num = $('#tbl_consmption_cost tbody tr').length;
			var cons_breck_down = "";
			for (var i = 1; i <= row_num; i++) {
				var txtdescription = $('#des_' + i).val();
				//alert(txtdescription.match(reg))
				if (txtdescription.match(reg)) {
					alert("Your Description Can not Have any thing other than a-zA-Z0-9!@#$%^,;.:<>{}?+|[]/- ");
					//release_freezing();
					$('#des_' + i).css('background-color', 'red');
					return;
				}
				var pocolorid = $('#pocolorid_' + i).val()
				if (pocolorid == '') pocolorid = 0;

				var gmtssizesid = $('#gmtssizesid_' + i).val()
				if (gmtssizesid == '') gmtssizesid = 0;

				var des = trim($('#des_' + i).val())
				if (des == '') des = 0;

				var itemcolor = $('#itemcolor_' + i).val()
				if (itemcolor == '') itemcolor = 0;

				var itemsizes = $('#itemsizes_' + i).val()
				if (itemsizes == '') itemsizes = 0;

				var qty = $('#qty_' + i).val()
				if (qty == '') qty = 0;

				var excess = $('#excess_' + i).val()
				if (excess == '') excess = 0;

				var woqny = $('#woqny_' + i).val()
				if (woqny == '') woqny = 0;

				var rate = $('#rate_' + i).val()
				if (rate == '') rate = 0;

				var amount = $('#amount_' + i).val()
				if (amount == '') amount = 0;

				var pcs = $('#pcs_' + i).val()
				if (pcs == '') pcs = 0;

				var colorsizetableid = $('#colorsizetableid_' + i).val()
				if (colorsizetableid == '') colorsizetableid = 0;

				var updateid = $('#updateid_' + i).val()
				if (updateid == '') updateid = 0;

				var reqqty = $('#reqqty_' + i).val()
				if (reqqty == '') reqqty = 0;

				//var poarticle=$('#poarticle_'+i).val()
				//if(poarticle=='') poarticle='no article';

				if (cons_breck_down == "") {
					cons_breck_down += pocolorid + '_' + gmtssizesid + '_' + des + '_' + itemcolor + '_' + itemsizes + '_' + qty + '_' + excess + '_' + woqny + '_' + rate + '_' + amount + '_' + pcs + '_' + colorsizetableid + '_' + reqqty;
				} else {
					cons_breck_down += "__" + pocolorid + '_' + gmtssizesid + '_' + des + '_' + itemcolor + '_' + itemsizes + '_' + qty + '_' + excess + '_' + woqny + '_' + rate + '_' + amount + '_' + pcs + '_' + colorsizetableid + '_' + reqqty;
				}
			}
			document.getElementById('cons_breck_down').value = cons_breck_down;
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<?
		extract($_REQUEST);
		//echo $txtembcostid.'dffff';
		if ($txt_req_id == "") {
			$txt_job_no_cond = "";
			$txt_job_no_cond1 = "";
		} else {
			$txt_job_no_cond = "and a.job_no='$txt_req_id'";
			$txt_job_no_cond1 = "and job_no='$txt_job_no'";
		}
		//echo $txt_req_quantity.'DSSS';

		$process_loss_method = return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
		//  echo $process_loss_method.'DXCC';
		/* $tot_po_qty=0;
        $sql_po_qty=sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id) and c.item_number_id=$txtgmtitemid  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
        foreach($sql_po_qty as$sql_po_qty_row){
			$po_qty_arr[$sql_po_qty_row[csf('id')]]=$sql_po_qty_row[csf('order_quantity_set')];
			$tot_po_qty+=$sql_po_qty_row[csf('order_quantity_set')];
        }*/
		?>
		<div align="center" style="width:1150px;">
			<fieldset>
				<form id="consumptionform_1" autocomplete="off">
					<table width="1150" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
						<thead>
							<tr>
								<th colspan="14" id="td_sync_msg" style="color:#FF0000"></th>
							</tr>
							<tr>
								<th colspan="14">
									<input type="hidden" id="cons_breck_down" name="cons_breck_down" value="" />
									<input type="hidden" id="txtwoq" value="<? echo $txt_req_quantity; ?>" />
									Wo Qty:<input type="text" id="txtwoq_qty" class="text_boxes_numeric" onBlur="poportionate_qty(this.value)" value="<? echo $txtwoq; ?>" />
									<input type="radio" name="copy_basis" value="0" <? if (!$txt_update_dtls_id) {
																						echo "checked";
																					} ?>>Copy to All
									<input type="radio" name="copy_basis" value="1">Gmts Size Wise
									<input type="radio" name="copy_basis" value="2">Gmts Color Wise
									<input type="radio" name="copy_basis" value="10" <? if ($txt_update_dtls_id) {
																							echo "checked";
																						} ?>>No Copy
									<input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<? echo $process_loss_method; ?>" />
									<input type="hidden" id="po_qty" name="po_qty" value="<? echo $txt_req_quantity; ?>" />
								</th>
							</tr>
							<tr>
								<th width="40">SL</th>
								<th width="100">Gmts. Color</th>
								<th width="70">Gmts. sizes</th>
								<th width="100">Description</th>
								<th width="100">Item Color</th>
								<th width="80">Item Sizes</th>
								<th width="70"> Wo Qty</th>
								<th width="40">Excess %</th>
								<th width="70">WO Qty.</th>
								<th width="120">Rate</th>
								<th width="100">Amount</th>
								<th width="">RMG Qnty</th>

							</tr>
						</thead>
						<tbody>
							<?

							$booking_data_arr = array();
							if ($txt_update_dtls_id == "") {
								$txt_update_dtls_id = 0;
							}
							$booking_data = sql_select("select id,wo_booking_dtls_id,description,item_color,item_size,cons,process_loss_percent,requirment,rate,amount,emb_color_size_id as color_size_table_id  from wo_non_ord_embl_book_cons_dtls where wo_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
							//echo "select id,wo_booking_dtls_id,description,item_color,item_size,cons,process_loss_percent,requirment,rate,amount,emb_color_size_id as color_size_table_id  from wo_non_ord_embl_book_cons_dtls where wo_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0";
							foreach ($booking_data as $row) {
								$booking_data_arr[$row[csf('color_size_table_id')]][id] = $row[csf('id')];
								$booking_data_arr[$row[csf('color_size_table_id')]][description] = $row[csf('description')];
								$booking_data_arr[$row[csf('color_size_table_id')]][item_color] = $row[csf('item_color')];
								$booking_data_arr[$row[csf('color_size_table_id')]][item_size] = $row[csf('item_size')];

								$booking_data_arr[$row[csf('color_size_table_id')]][cons] += $row[csf('cons')];
								$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent] = $row[csf('process_loss_percent')];
								$booking_data_arr[$row[csf('color_size_table_id')]][requirment] += $row[csf('requirment')];
								$booking_data_arr[$row[csf('color_size_table_id')]][rate] = $row[csf('rate')];
								$booking_data_arr[$row[csf('color_size_table_id')]][amount] += $row[csf('amount')];
							}

							if ($cbo_colorsizesensitive == 4) {
								$sql = "select a.id,a.style_ref_no,b.id as emb_dtls_id, (b.fin_fab_qnty) AS fin_fab_qnty,c.id as color_size_table_id,c.color_id as color_number_id,c.size_id as size_number_id,c.qnty
        from sample_development_mst a, sample_development_fabric_acc b,sample_develop_embl_color_size c where a.id=b.sample_mst_id and b.id=c.dtls_id and
		 b.form_type=3 and a.company_id=$cbo_company_name and b.id in($txtembcostid) and a.entry_form_id=203 and b.form_type=3 and a.status_active=1 and b.status_active=1 and c.status_active=1   and b.fin_fab_qnty>0 order by a.id, b.id";
							} else {

								$sql = "select a.id,a.style_ref_no,b.id as emb_dtls_id, (b.fin_fab_qnty) AS fin_fab_qnty,c.id as color_size_table_id,c.color_id as color_number_id,c.size_id as size_number_id,c.qnty
        from sample_development_mst a, sample_development_fabric_acc b,sample_develop_embl_color_size c where a.id=b.sample_mst_id and b.id=c.dtls_id and
		 b.form_type=3 and a.company_id=$cbo_company_name and b.id in($txtembcostid) and a.entry_form_id=203 and b.form_type=3 and a.status_active=1 and b.status_active=1 and c.status_active=1   and b.fin_fab_qnty>0 order by a.id, b.id";
							}
							$data_array = sql_select($sql);
							if (count($data_array) > 0) {
								$i = 0;
								foreach ($data_array as $row) {
									if ($cbo_colorsizesensitive == 4) {
										$txt_req_quantity = $row[csf('qnty')];
										$txtwoq_cal = def_number_format($txt_req_quantity, 5, "");
									} else if ($cbo_colorsizesensitive == 0) {
										$txt_req_quantity = $row[csf('qnty')];
										$txtwoq_cal = def_number_format($txt_req_quantity, 5, "");
									}
									//if($txtwoq_cal>0){
									//echo $txt_req_quantity.'XXXXXXXXXXXX';
									$i++;
									$description = $booking_data_arr[$row[csf('color_size_table_id')]][description];
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									if ($item_color == 0 || $item_color == "") $item_color = $row[csf('color_number_id')];
									$item_size = $booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									if ($item_size == 0 || $item_size == "") $item_size = $size_library[$row[csf('size_number_id')]];

									//$cons=$booking_data_arr[$row[csf('color_size_table_id')]][cons];

									$rate = $booking_data_arr[$row[csf('color_size_table_id')]][rate];
									if ($rate == 0 || $rate == "") $rate = $txt_avg_price;


							?>
									<tr id="break_1" align="center">
										<td><? echo $i; ?></td>

										<td>
											<input type="text" id="pocolor_<? echo $i; ?>" name="pocolor_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" <? //if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} 
																																																								?> readonly />
											<input type="hidden" id="pocolorid_<? echo $i; ?>" name="pocolorid_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $row[csf('color_number_id')]; ?>" readonly />
											<input type="hidden" id="reqid_<? echo $i; ?>" name="reqid_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $row[csf('id')]; ?>" />
											<input type="hidden" id="poqty_<? echo $i; ?>" name="poqty_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $txt_req_quantity; ?>" readonly />
											<input type="hidden" id="poreqqty_<? echo $i; ?>" name="poreqqty_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $txt_req_quantity; ?>" readonly />
										</td>
										<td>
											<input type="text" id="gmtssizes_<? echo $i; ?>" name="gmtssizes_<? echo $i; ?>" class="text_boxes" style="width:70px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>" <? //if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} 
																																																								?> readonly />
											<input type="hidden" id="gmtssizesid_<? echo $i; ?>" name="gmtssizesid_<? echo $i; ?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('size_number_id')]; ?>" readonly />
										</td>
										<td><input type="text" id="des_<? echo $i; ?>" name="des_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $description; ?>" onChange="copy_value(this.value,'des_',<? echo $i; ?>)" <? //if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} 
																																																												?> />
										</td>

										<td><input type="text" id="itemcolor_<? echo $i; ?>" value="<? echo $color_library[$item_color]; ?>" name="itemcolor_<? echo $i; ?>" class="text_boxes" style="width:100px" onChange="copy_value(this.value,'itemcolor_',<? echo $i; ?>)" <? //if($item_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} 
																																																																				?> />
										</td>
										<td><input type="text" id="itemsizes_<? echo $i; ?>" name="itemsizes_<? echo $i; ?>" class="text_boxes" style="width:80px" onChange="copy_value(this.value,'itemsizes_',<? echo $i; ?>)" value="<? echo $item_size; ?>" <? //if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} 
																																																																?> />
										</td>
										<td><input type="hidden" id="reqqty_<? echo $i; ?>" name="reqqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $txtwoq_cal ?>" readonly />
											<input type="text" id="qty_<? echo $i; ?>" onBlur="validate_sum( <? echo $i; ?> )" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i; ?>);copy_value(this.value,'qty_',<? echo $i; ?>)" name="qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" placeholder="<? echo $txtwoq_cal; ?>" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][cons]; ?>" />
										</td>
										<td>
											<input type="text" id="excess_<? echo $i; ?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) " name="excess_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_requirement(<? echo $i; ?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i; ?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" />
										</td>
										<td>
											<input type="text" id="woqny_<? echo $i; ?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_' )" onChange="set_sum_value( 'woqty_sum', 'woqny_' )" name="woqny_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][requirment]; ?>" readonly />
										</td>
										<td>
											<input type="text" id="rate_<? echo $i; ?>" name="rate_<? echo $i; ?>" class="text_boxes_numeric" style="width:120px" onChange="calculate_amount(<? echo $i; ?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i; ?>) " value="<? echo $rate; ?>" <? if ($piNumber || $recvNumber) {
																																																																																echo  "disabled";
																																																																															} else {
																																																																																echo "";
																																																																															} ?> />
										</td>
										<td>
											<input type="text" id="amount_<? echo $i; ?>" name="amount_<? echo $i; ?>" onBlur="set_sum_value( 'amount_sum', 'amount_' ) " class="text_boxes_numeric" style="width:100px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly>
										</td>
										<td>
											<input type="text" id="pcs_<? echo $i; ?>" name="pcs_<? echo $i; ?>" onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px" value="<? echo $row[csf('order_quantity')]; ?>" readonly>
											<input type="hidden" id="pcsset_<? echo $i; ?>" name="pcsset_<? echo $i; ?>" onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px" value="<? echo $row[csf('order_quantity_set')]; ?>" readonly>
											<input type="hidden" id="colorsizetableid_<? echo $i; ?>" name="colorsizetableid_<? echo $i; ?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_size_table_id')]; ?>" />
											<input type="hidden" id="updateid_<? echo $i; ?>" name="updateid_<? echo $i; ?>" class="text_boxes" style="width:85px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
										</td>
									</tr>
							<?
									//}
								}
							}


							?>
						</tbody>
						<tfoot>
							<tr>
								<th width="40">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="70"><input type="text" id="qty_sum" name="qty_sum" class="text_boxes_numeric" style="width:70px" readonly></th>
								<th width="40"><input type="text" id="excess_sum" name="excess_sum" class="text_boxes_numeric" style="width:40px" readonly></th>
								<th width="70"><input type="text" id="woqty_sum" name="woqty_sum" class="text_boxes_numeric" style="width:70px" readonly></th>
								<th width="40"><input type="text" id="rate_sum" name="rate_sum" class="text_boxes_numeric" style="width:120px" readonly></th>
								<th width="50"><input type="text" id="amount_sum" name="amount_sum" class="text_boxes_numeric" style="width:100px" readonly></th>
								<th><input type="hidden" id="json_data" name="json_data" class="text_boxes_numeric" style="width:50px" value='<? //echo json_encode($level_arr); 
																																				?>' readonly>
									<input type="text" id="pcs_sum" name="pcs_sum" class="text_boxes_numeric" style="width:50px" readonly>
								</th>
							</tr>
						</tfoot>
					</table>
					<table width="1150" cellspacing="0" class="" border="0" rules="all">
						<tr>
							<td align="center" width="100%"> <input type="button" class="formbutton" value="Close" onClick="js_set_value()" /> </td>
						</tr>
					</table>
				</form>
			</fieldset>
		</div>
	</body>
	<script>
		$("input[type=text]").focus(function() {
			$(this).select();
		});
		<?
		if ($txt_update_dtls_id == "") {
		?>
			poportionate_qty(<? echo $txtwoq; ?>);
		<?
		}
		?>
		set_sum_value('qty_sum', 'qty_');
		set_sum_value('woqty_sum', 'woqny_');
		set_sum_value('amount_sum', 'amount_');
		set_sum_value('pcs_sum', 'pcs_');
		calculate_avg_rate();
		var wo_qty = $('#txtwoq_qty').val() * 1;

		var wo_qty_sum = $('#qty_sum').val() * 1;

		if (wo_qty != wo_qty_sum) {
			//$('#td_sync_msg').html("Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.");
		}
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "set_cons_break_down") {
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$data = explode("_", $data);
	$garments_nature = $data[0];
	$cbo_company_name = $data[1];
	$txt_job_no = $data[2];
	$txt_po_id = $data[3];
	$txtembcostid = $data[4];
	$txtgmtitemid = $data[5];
	$txt_update_dtls_id = trim($data[6]);
	$cbo_colorsizesensitive = $data[7];
	$txt_req_quantity = $data[8];
	$txt_avg_price = $data[9];
	$txt_country = $data[10];
	$emb_name = $data[11];
	$emb_type = $data[12];
	$cbo_level = $data[13];

	if ($txt_job_no == "") {
		$txt_job_no_cond = "";
		$txt_job_no_cond1 = "";
	} else {
		$txt_job_no_cond = "and a.job_no='$txt_job_no'";
		$txt_job_no_cond1 = "and job_no='$txt_job_no'";
	}

	if ($txt_country == "") $txt_country_cond = "";
	else $txt_country_cond = "and c.country_id in ($txt_country)";

	$process_loss_method = return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");


	$booking_data_arr = array();
	if ($txt_update_dtls_id == "") {
		$txt_update_dtls_id = 0;
	}
	$booking_data = sql_select("select id,wo_booking_dtls_id,description,item_color,item_size,cons,process_loss_percent,requirment,rate,amount,emb_color_size_id as color_size_table_id  from wo_non_ord_embl_book_cons_dtls where wo_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
	foreach ($booking_data as $row) {
		$booking_data_arr[$row[csf('color_size_table_id')]][id] = $row[csf('id')];
		$booking_data_arr[$row[csf('color_size_table_id')]][description] = $row[csf('description')];
		$booking_data_arr[$row[csf('color_size_table_id')]][item_color] = $row[csf('item_color')];
		$booking_data_arr[$row[csf('color_size_table_id')]][item_size] = $row[csf('item_size')];

		$booking_data_arr[$row[csf('color_size_table_id')]][cons] += $row[csf('cons')];
		$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent] = $row[csf('process_loss_percent')];
		$booking_data_arr[$row[csf('color_size_table_id')]][requirment] += $row[csf('requirment')];
		$booking_data_arr[$row[csf('color_size_table_id')]][rate] = $row[csf('rate')];
		$booking_data_arr[$row[csf('color_size_table_id')]][amount] += $row[csf('amount')];
	}

	if ($cbo_colorsizesensitive == 4) {
		$sql = "select a.id,a.style_ref_no,b.id as emb_dtls_id, (b.fin_fab_qnty) AS fin_fab_qnty,c.id as color_size_table_id,c.color_id as color_number_id,c.size_id as size_number_id,c.qnty
        from sample_development_mst a, sample_development_fabric_acc b,sample_develop_embl_color_size c where a.id=b.sample_mst_id and b.id=c.dtls_id and
		 b.form_type=3 and a.company_id=$cbo_company_name and b.id in($txtembcostid) and a.entry_form_id=203 and b.form_type=3 and a.status_active=1 and b.status_active=1 and c.status_active=1   and b.fin_fab_qnty>0 order by a.id, b.id";
	} else {

		$sql = "select a.id,a.style_ref_no,b.id as emb_dtls_id, (b.fin_fab_qnty) AS fin_fab_qnty,c.id as color_size_table_id,c.color_id as color_number_id,c.size_id as size_number_id,c.qnty
        from sample_development_mst a, sample_development_fabric_acc b,sample_develop_embl_color_size c where a.id=b.sample_mst_id and b.id=c.dtls_id and
		 b.form_type=3 and a.company_id=$cbo_company_name and b.id in($txtembcostid) and a.entry_form_id=203 and b.form_type=3 and a.status_active=1 and b.status_active=1 and c.status_active=1   and b.fin_fab_qnty>0 order by a.id, b.id";
	}
	$data_array = sql_select($sql);

	/*$cu_booking_data_arr=array();
	$cu_booking_data=sql_select("select a.pre_cost_fabric_cost_dtls_id,b.id,b.wo_booking_dtls_id,b.po_break_down_id,b.color_number_id,b.gmts_sizes,b.requirment,b.article_number  from wo_booking_dtls a, wo_emb_book_con_dtls b where a.id=b.wo_booking_dtls_id and b.po_break_down_id in($txt_po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id not in($txt_update_dtls_id)");
	foreach($cu_booking_data as $cu_booking_data_row){
		if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('color_number_id')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==2 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('gmts_sizes')]][$cu_booking_data_row[csf('article_number')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==4 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('color_number_id')]][$cu_booking_data_row[csf('gmts_sizes')]][$cu_booking_data_row[csf('article_number')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==0 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]]+=$cu_booking_data_row[csf('requirment')];
		}
	}*/

	if (count($data_array) > 0) {
		$i = 0;
		foreach ($data_array as $row) {
			$color_number_id = $row[csf('color_number_id')];
			if ($color_number_id == "") $color_number_id = 0;

			$size_number_id = $row[csf('size_number_id')];
			if ($size_number_id == "") $size_number_id = 0;

			$description = $txt_pre_des;
			if ($description == "") $description = 0;



			$item_color = $color_library[$row[csf('color_number_id')]];
			if ($item_color == "") $item_color = 0;

			$item_size = $row[csf('item_size')];
			if ($item_size == "") $item_size = 0;
			$excess = 0;
			$pcs = $row[csf('order_quantity_set')];
			if ($pcs == "") $pcs = 0;

			$colorsizetableid = $row[csf('color_size_table_id')];
			if ($colorsizetableid == "") $colorsizetableid = 0;

			$articleNumber = $row[csf('article_number')];
			if ($articleNumber == "") $articleNumber = 'no article';

			if ($cbo_colorsizesensitive == 1 || $cbo_colorsizesensitive == 3) {
				if ($emb_name == 3) {
					$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
				} else {
					$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
				}

				$req_qnty_ordUom = def_number_format((($data[14] / $data[8]) * $txt_req_quantity), 5, "");
				$txtwoq_cal = def_number_format($req_qnty_ordUom, 5, "");
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");

				$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty'][$row[csf('id')]] = $txtwoq_cal;
				$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['po_qty'][$row[csf('id')]] = $po_qty;
				$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity_set'][$row[csf('id')]] = $row[csf('order_quantity_set')];
				$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['po_id'][$row[csf('id')]] = $row[csf('id')];
				$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity'][$row[csf('id')]] = $row[csf('order_quantity')];
				$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['color_size_table_id'][$row[csf('id')]] = $row[csf('color_size_table_id')];
				$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['amount'][$row[csf('id')]] = $amount;
			}
		}
	}

	$cons_breck_down = "";
	if (count($data_array) > 0 && $cbo_level == 1) {
		$i = 0;
		foreach ($data_array as $row) {
			$color_number_id = $row[csf('color_number_id')];
			if ($color_number_id == "") $color_number_id = 0;

			$size_number_id = $row[csf('size_number_id')];
			if ($size_number_id == "") $size_number_id = 0;

			$description = $txt_pre_des;
			if ($description == "") $description = 0;

			$brand_supplier = $txt_pre_brand_sup;
			if ($brand_supplier == "") $brand_supplier = 0;

			$item_color = $color_library[$row[csf('color_number_id')]];
			if ($item_color == "") $item_color = 0;

			//$item_size=$row[csf('item_size')];
			$item_size = $size_library[$row[csf('size_number_id')]];
			if ($item_size == "") $item_size = 0;
			$excess = 0;

			$pcs = $row[csf('order_quantity_set')];
			if ($pcs == "") $pcs = 0;

			$colorsizetableid = $row[csf('color_size_table_id')];
			if ($colorsizetableid == "") $colorsizetableid = 0;

			$articleNumber = $row[csf('article_number')];
			if ($articleNumber == "") $articleNumber = 'no article';

			if ($cbo_colorsizesensitive == 1 || $cbo_colorsizesensitive == 3) {
				if ($emb_name == 3) {
					$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
				} else {
					$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
				}
				$req_qnty_ordUom = def_number_format((($data[14] / $data[8]) * $txt_req_quantity), 5, "");
				$txtwoq_cal = def_number_format($req_qnty_ordUom, 5, "");
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");
			} else if ($cbo_colorsizesensitive == 2) {
				if ($emb_name == 3) {
					$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				} else {
					$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				}
				$req_qnty_ordUom = def_number_format((($data[14] / $data[8]) * $txt_req_quantity), 5, "");
				$txtwoq_cal = def_number_format($req_qnty_ordUom, 5, "");
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");
			} else if ($cbo_colorsizesensitive == 4) {
				if ($emb_name == 3) {
					$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				} else {
					$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				}
				$req_qnty_ordUom = def_number_format((($data[14] / $data[8]) * $txt_req_quantity), 5, "");
				$txtwoq_cal = def_number_format($req_qnty_ordUom, 5, "");
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");
			} else if ($cbo_colorsizesensitive == 0) {

				if ($emb_name == 3) {
					$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
				} else {
					$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
				}
				$req_qnty_ordUom = def_number_format((($data[14] / $data[8]) * $txt_req_quantity), 5, "");
				$txtwoq_cal = def_number_format($req_qnty_ordUom, 5, "");
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");
			}
			if ($txtwoq_cal > 0) {
				if ($cons_breck_down == "") {
					$cons_breck_down .= $color_number_id . '_' . $size_number_id . '_' . $description . '_' . $item_color . '_' . $item_size . '_' . $txtwoq_cal . '_' . $excess . '_' . $txtwoq_cal . '_' . $txt_avg_price . '_' . $amount . '_' . $pcs . '_' . $colorsizetableid . "_" . $txtwoq_cal . "_" . $articleNumber;
				} else {
					$cons_breck_down .= "__" . $color_number_id . '_' . $size_number_id . '_' . $description . '_' . $item_color . '_' . $item_size . '_' . $txtwoq_cal . '_' . $excess . '_' . $txtwoq_cal . '_' . $txt_avg_price . '_' . $amount . '_' . $pcs . '_' . $colorsizetableid . "_" . $txtwoq_cal . "_" . $articleNumber;
				}
			}
		}
		echo $cons_breck_down;
	}

	/*$level_arr=array();
	$gmt_color_edb="";
	$item_color_edb="";
	$gmt_size_edb="";
	$item_size_edb="";
	if($cbo_colorsizesensitive==1){
		$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
		$level_arr=$po_color_level_data_arr;
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==2){
		$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.size_number_id,c.article_number,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.size_number_id,c.article_number order by size_order";
		$level_arr=$po_size_level_data_arr;
		$gmt_color_edb="disabled";
		$item_color_edb="disabled";
	}
	else if($cbo_colorsizesensitive==3){
		$sql="select min(b.id) as id, min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
		$level_arr=$po_color_level_data_arr;
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==4){
		$sql="select min(b.id) as id ,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.item_size) as item_size,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id,c.size_number_id,c.article_number  order by  color_order,size_order";
		$level_arr=$po_color_size_level_data_arr;
	}
	else{
		  $sql="select b.job_no_mst,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.job_no_mst";
		$level_arr=$po_no_sen_level_data_arr;
	}*/
}

if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));


	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		if ($db_type == 0) {
			$new_booking_no = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_name), '', 'ENB', date("Y", time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_non_ord_embl_booking_mst where company_id=$cbo_company_name and booking_type=6 and YEAR(insert_date)=" . date('Y', time()) . " order by id desc ", "booking_no_prefix", "booking_no_prefix_num"));
		}
		if ($db_type == 2) {
			$new_booking_no = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_name), '', 'ENB', date("Y", time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_non_ord_embl_booking_mst where company_id=$cbo_company_name and booking_type=6 and to_char(insert_date,'YYYY')=" . date('Y', time()) . " order by id desc ", "booking_no_prefix", "booking_no_prefix_num"));
		}

		$id = return_next_id("id", "wo_non_ord_embl_booking_mst", 1); //
		$field_array = "id,booking_type,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,currency_id,category_id,pay_mode,source_id,booking_date,delivery_date,supplier_id,attention,tenor,ready_to_approved,entry_form_id,inserted_by,insert_date,remarks,delivery_to,status_active,is_deleted";
		$data_array = "(" . $id . ",6,'" . $new_booking_no[1] . "'," . $new_booking_no[2] . ",'" . $new_booking_no[0] . "'," . $cbo_company_name . "," . $cbo_buyer_name . "," . $cbo_currency . ",25," . $cbo_pay_mode . "," . $cbo_source . "," . $txt_booking_date . "," . $txt_delivery_date . "," . $cbo_supplier_name . "," . $txt_attention . "," . $txt_tenor . "," . $cbo_ready_to_approved . ",399," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $remarks . "," . $txt_delivery_to . ",1,0)";
		//echo "10** insert into wo_non_ord_embl_booking_mst (".$field_array.") values ".$data_array;die;
		$rID = sql_insert("wo_non_ord_embl_booking_mst", $field_array, $data_array, 0);
		if ($db_type == 0) {
			if ($rID) {
				mysql_query("COMMIT");
				echo "0**" . $new_booking_no[0] . "**" . $id;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $new_booking_no[0] . "**" . $id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID) {
				oci_commit($con);
				echo "0**" . $new_booking_no[0] . "**" . $id;
			} else {
				oci_rollback($con);
				echo "10**" . $new_booking_no[0] . "**" . $id;
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 1)   // Update Here
	{
		$con = connect();
		$is_approved = 0;
		$sql = sql_select("select is_approved from wo_non_ord_embl_booking_mst where booking_no=$txt_booking_no");
		foreach ($sql as $row) {
			$is_approved = $row[csf('is_approved')];
		}
		if ($is_approved == 1) {
			echo "approved**" . str_replace("'", "", $txt_booking_no);
			disconnect($con);
			die;
		}


		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$field_array = "buyer_id*currency_id*category_id*pay_mode*source_id*booking_date*delivery_date*supplier_id*attention*tenor*ready_to_approved*updated_by*update_date*remarks*delivery_to";
		$data_array = "" . $cbo_buyer_name . "*" . $cbo_currency . "*25*" . $cbo_pay_mode . "*" . $cbo_source . "*" . $txt_booking_date . "*" . $txt_delivery_date . "*" . $cbo_supplier_name . "*" . $txt_attention . "*" . $txt_tenor . "*" . $cbo_ready_to_approved . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $remarks . "*" . $txt_delivery_to . "";
		$rID = sql_update("wo_non_ord_embl_booking_mst", $field_array, $data_array, "booking_no", "" . $txt_booking_no . "", 0);

		if ($db_type == 0) {
			if ($rID) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $txt_booking_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID) {
				oci_commit($con);
				echo "1**" . str_replace("'", "", $txt_booking_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 2)   // Delete Here
	{
		$con = connect();
		$is_approved = 0;
		$sql = sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach ($sql as $row) {
			$is_approved = $row[csf('is_approved')];
		}
		if ($is_approved == 1) {
			echo "approved**" . str_replace("'", "", $txt_booking_no);
			disconnect($con);
			die;
		}
		//if(str_replace("'","",$cbo_pay_mode)==2){
		$pi_number = return_field_value("pi_number", "com_pi_master_details a,com_pi_item_details b", " a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_category_id=25  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if ($pi_number) {
			echo "piNo**" . str_replace("'", "", $txt_booking_no) . "**" . $pi_number;
			disconnect($con);
			die;
		}
		//}
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$delete_cause = str_replace("'", "", $delete_cause);
		$delete_cause = str_replace('"', '', $delete_cause);
		$delete_cause = str_replace('(', '', $delete_cause);
		$delete_cause = str_replace(')', '', $delete_cause);
		$rID = execute_query("update wo_booking_mst set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where  booking_no=$txt_booking_no", 0);
		$rID1 = execute_query("update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where  booking_no=$txt_booking_no", 0);
		if ($db_type == 0) {
			if ($rID && $rID1) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_booking_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID1) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $txt_booking_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}
if ($action == "save_update_delete_dtls") {
	$color_library = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$is_approved = return_field_value("is_approved", "wo_non_ord_embl_booking_mst", "booking_no=$txt_booking_no");
	if ($is_approved == 1) {
		echo "app1**" . str_replace("'", "", $txt_booking_no);
		disconnect($con);
		die;
	}

	if ($operation == 0) {
		$gmtArr = array();
		$ReqArr = array();
		$req_emb_id_arr = array();
		for ($i = 1; $i <= $total_row; $i++) {
			$txtembcostid = "txtembcostid_" . $i;
			$txtreqId = "txtreqId_" . $i;
			$txtgmtitemid = 'txtgmtitemid_' . $i;
			$txtembcostId = str_replace("'", "", $$txtembcostid);
			$Reqid = str_replace("'", "", $$txtreqId);
			$gmtItem = str_replace("'", "", $$txtgmtitemid);
			$req_emb_id_arr[$txtembcostId] = $txtembcostId;
			$ReqArr[$Reqid] = $Reqid;
			$gmtArr[$gmtItem] = $gmtItem;
		}
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		if (check_table_status($_SESSION['menu_id'], 1) == 0) {
			echo "15**0";
			disconnect($con);
			die;
		}

		if (is_duplicate_field("booking_no", "wo_non_ord_embl_booking_dtls", "gmt_item_id in(" . implode(",", $gmtArr) . ") and req_id in (" . implode(",", $ReqArr) . ") and embl_cost_dtls_id in(" . implode(",", $req_emb_id_arr) . ") and booking_type=6  and booking_no=$txt_booking_no and status_active=1 and is_deleted=0") == 1) {
			check_table_status($_SESSION['menu_id'], 0);
			echo "11**0";
			disconnect($con);
			die;
		}

		$id_dtls = return_next_id("id", "wo_non_ord_embl_booking_dtls", 1);
		$field_array = "id,booking_mst_id, embl_cost_dtls_id, booking_no, booking_type,delivery_date,gmt_item_id,req_id, req_no,req_booking_no,emb_name,emb_type,body_part_id,uom_id, sensitivity,cons_break_down,entry_form_id, wo_qnty, exchange_rate, rate, amount, inserted_by, insert_date, status_active, is_deleted";
		$field_array_cons = "id,wo_booking_dtls_id,booking_no,req_id,color_number_id,gmts_sizes,description,item_color,item_size,cons, process_loss_percent, requirment, rate, amount, emb_color_size_id";

		$add_comma = 0;
		$add_comma2 = 1;
		$data_array_cons = "";
		$id1 = return_next_id("id", "wo_non_ord_embl_book_cons_dtls", 1);
		$new_array_color = array();
		for ($i = 1; $i <= $total_row; $i++) {
			//===============
			$txtembcostid = "txtembcostid_" . $i;
			$txtgmtitemid = 'txtgmtitemid_' . $i;
			$txtreqId = "txtreqId_" . $i;
			$txtreqno = "txtreqno_" . $i;
			$ReqbookingNo = "ReqbookingNo_" . $i;
			$emb_name = "emb_name_" . $i;
			$emb_type = "emb_type_" . $i;
			$body_part_id = "body_part_id_" . $i;
			$txtuom = "txtuom_" . $i;
			$cbocolorsizesensitive = "cbocolorsizesensitive_" . $i;
			$txtwoq = "txtwoq_" . $i; //txtwoq_1
			$txtexchrate = "txtexchrate_" . $i;
			$txtrate = "txtrate_" . $i;
			$txtamount = "txtamount_" . $i;
			$txtddate = "txtddate_" . $i;
			$consbreckdown = "consbreckdown_" . $i;
			$txtbookingid = "txtbookingid_" . $i;
			$txtreqqnty = "txtreqqnty_" . $i;
			//$jsondata="jsondata_".$i;
			$txtreqamount = "txtreqamount_" . $i;
			$txtreqqnty = "txtreqqnty_" . $i;

			$uom_id = str_replace("'", "", $$txtuom);
			$job = str_replace("'", "", $$txtjob_id);
			$embcostid = str_replace("'", "", $$txtembcostid);
			$gmtitemid = str_replace("'", "", $$txtgmtitemid);
			$reqqnty = str_replace("'", "", $$txtreqqnty);
			$woq = str_replace("'", "", $$txtwoq);
			$rate = str_replace("'", "", $$txtrate);
			$amt = str_replace("'", "", $$txtamount);
			$Reqqnty = str_replace("'", "", $$txtreqqnty);
			$del_ddate = str_replace("'", "", $$txtddate);

			$colorsizesensitive_id = str_replace("'", "", $$cbocolorsizesensitive);
			$reqId = str_replace("'", "", $$txtreqId);
			$bookingid = str_replace("'", "", $$txtbookingid);
			$Reqwoq = str_replace("'", "", $$txtwoq);
			$rate_cal = $rate;

			if (str_replace("'", '', $$consbreckdown) != '') {
				$consbreckdown = $$consbreckdown;
			} else {
				$consbreckdown = "'" . create_consbreak_down($bookingid, $reqId, str_replace("'", '', $$ReqbookingNo), str_replace("'", '', $$txtembcostid), str_replace("'", '', $cbo_company_name), $colorsizesensitive_id, $Reqwoq, $Reqqnty, $rate_cal, $amt) . "'";
			}
			$cons_breckdown = substr(str_replace("'", "", $consbreckdown), 0, 3999);
			if ($db_type == 0) $del_date = date("Y-m-d", strtotime($del_ddate));
			else $del_date = date("d-M-Y", strtotime($del_ddate));
			//==============
			if ($add_comma2 != 1) $data_array .= ",";
			$data_array .= "(" . $id_dtls . "," . $update_id . "," . $$txtembcostid . ",'" . $txt_booking_no . "',6,'" . $del_date . "'," . $$txtgmtitemid . "," . $$txtreqId . "," . $$txtreqno . "," . $$ReqbookingNo . "," . $$emb_name . "," . $$emb_type . "," . $$body_part_id . ",12," . $$cbocolorsizesensitive . ",'" . $cons_breckdown . "',399," . $$txtwoq . "," . $$txtexchrate . "," . $$txtrate . "," . $$txtamount . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";

			//	CONS break down===============================================================================================
			if (str_replace("'", '', $consbreckdown) != '') {

				$rID_de1 = execute_query("delete from wo_non_ord_embl_book_cons_dtls where  wo_booking_dtls_id =" . $$txtbookingid . "", 0);
				$consbreckdown_array = explode('__', str_replace("'", '', $consbreckdown));
				for ($c = 0; $c < count($consbreckdown_array); $c++) {
					$consbreckdownarr = explode('_', $consbreckdown_array[$c]);

					if (str_replace("'", "", $consbreckdownarr[3]) != "") {
						if (!in_array(str_replace("'", "", $consbreckdownarr[3]), $new_array_color)) {
							$color_id = return_id(str_replace("'", "", $consbreckdownarr[3]), $size_library, "lib_size", "id,color_name", "399");
							$new_array_color[$color_id] = str_replace("'", "", $consbreckdownarr[3]);
						} else $color_id =  array_search(str_replace("'", "", $consbreckdownarr[3]), $new_array_color);
					} else $color_id = 0;

					/*if (!in_array(str_replace("'","",$consbreckdownarr[3]),$new_array_color)){
						$color_id = return_id( str_replace("'","",$consbreckdownarr[3]), $color_library, "lib_color", "id,color_name","399");
						$new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[3]);
					}
					else{
						$color_id =  array_search(str_replace("'","",$consbreckdownarr[3]), $new_array_color);
					}*/

					if ($add_comma != 0) $data_array_cons .= ",";
					$data_array_cons .= "(" . $id1 . "," . $id_dtls . ",'" . $txt_booking_no . "'," . $$txtreqId . ",'" . $consbreckdownarr[0] . "','" . $consbreckdownarr[1] . "','" . $consbreckdownarr[2] . "','" . $color_id . "','" . $consbreckdownarr[4] . "','" . $consbreckdownarr[5] . "','" . $consbreckdownarr[6] . "','" . $consbreckdownarr[7] . "','" . $consbreckdownarr[8] . "','" . $consbreckdownarr[9] . "','" . $consbreckdownarr[11] . "')";
					$id1 = $id1 + 1;
					$add_comma++;
					//echo "10** insert into wo_emb_book_con_dtls (".$field_array2.") values ".$data_array2;die;
				}
			}
			//CONS break down end===============================================================================================
			$id_dtls = $id_dtls + 1;
			$add_comma2++;
		}
		check_table_status($_SESSION['menu_id'], 0);
		//echo "10**insert into wo_non_ord_embl_book_cons_dtls (".$field_array_cons.") values ".$data_array_cons;die;
		$flag = 1;
		$rID1 = sql_insert("wo_non_ord_embl_booking_dtls", $field_array, $data_array, 0);
		if ($rID1) $flag = 1;
		else  $flag = 0;

		if ($data_array_cons != "") {
			if ($flag == 1) {
				$rID2 = sql_insert("wo_non_ord_embl_book_cons_dtls", $field_array_cons, $data_array_cons, 1);
				if ($rID2) $flag = 1;
				else  $flag = 0;
			}
		}

		check_table_status($_SESSION['menu_id'], 0);
		//	echo "10**".$rID1." ==". $rID2;die;
		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "0**" . $new_booking_no[0];
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $new_booking_no[0];
			}
		}
		if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "0**" . $new_booking_no[0];
			} else {
				oci_rollback($con);
				echo "10**" . $new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 1) {

		$gmtArr = array();
		$ReqArr = array();
		$req_emb_id_arr = array();
		$booking_dtls_id_arr = array();
		for ($i = 1; $i <= $total_row; $i++) {
			$txtembcostid = "txtembcostid_" . $i;
			$txtreqId = "txtreqId_" . $i;
			$txtgmtitemid = 'txtgmtitemid_' . $i;
			$txtbookingid = "txtbookingid_" . $i;
			$txtembcostId = str_replace("'", "", $$txtembcostid);
			$bookingdtlsId = str_replace("'", "", $$txtbookingid);
			$Reqid = str_replace("'", "", $$txtreqId);
			$gmtItem = str_replace("'", "", $$txtgmtitemid);
			$req_emb_id_arr[$txtembcostId] = $txtembcostId;
			$ReqArr[$Reqid] = $Reqid;
			$gmtArr[$gmtItem] = $gmtItem;
			$booking_dtls_id_arr[$bookingdtlsId] = $bookingdtlsId;
		}

		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		if (check_table_status($_SESSION['menu_id'], 1) == 0) {
			echo "15**1";
			disconnect($con);
			die;
		}

		if (is_duplicate_field("booking_no", "wo_non_ord_embl_booking_dtls", "gmt_item_id in(" . implode(",", $gmtArr) . ") and req_id in (" . implode(",", $ReqArr) . ") and pre_cost_fabric_cost_dtls_id in(" . implode(",", $req_emb_id_arr) . ") and id not in (" . implode(",", $booking_dtls_id_arr) . ") and booking_type=6  and booking_no=$txt_booking_no and status_active=1 and is_deleted=0") == 1) {
			check_table_status($_SESSION['menu_id'], 0);
			echo "11**0";
			disconnect($con);
			die;
		}


		//$field_array="id, embl_cost_dtls_id, booking_no, booking_type,delivery_date,gmt_item_id,req_id, req_no,req_booking_no,emb_name,emb_type,body_part_id,uom_id, sensitivity,cons_break_down,entry_form_id, wo_qnty, exchange_rate, rate, amount, inserted_by, insert_date,status_active,is_deleted";
		$field_array_cons = "id,wo_booking_dtls_id,booking_no,req_id,color_number_id,gmts_sizes,description,item_color,item_size,cons, process_loss_percent, requirment, rate, amount, emb_color_size_id";

		$field_array_up1 = "embl_cost_dtls_id*gmt_item_id*booking_no*req_id*req_booking_no*sensitivity*body_part_id*uom_id*emb_name*emb_type*wo_qnty*exchange_rate*rate*amount*delivery_date*cons_break_down*updated_by*update_date";
		//$field_array_up2="id,wo_booking_dtls_id,booking_no,req_id,color_number_id,gmts_sizes,description,item_color,item_size,process_loss_percent,cons, process_loss_percent,requirment,rate,amount,pcs,color_size_table_id,article_number";

		$add_comma = 0;
		$id1 = return_next_id("id", "wo_non_ord_embl_book_cons_dtls", 1);
		$new_array_color = array();
		for ($i = 1; $i <= $total_row; $i++) {
			$txtembcostid = "txtembcostid_" . $i;
			$txtgmtitemid = 'txtgmtitemid_' . $i;
			$txtreqId = "txtreqId_" . $i;
			$txtreqno = "txtreqno_" . $i;
			$ReqbookingNo = "ReqbookingNo_" . $i;
			$emb_name = "emb_name_" . $i;
			$emb_type = "emb_type_" . $i;
			$body_part_id = "body_part_id_" . $i;
			$txtuom = "txtuom_" . $i;
			$cbocolorsizesensitive = "cbocolorsizesensitive_" . $i;
			$txtwoq = "txtwoq_" . $i; //txtwoq_1
			$txtexchrate = "txtexchrate_" . $i;
			$txtrate = "txtrate_" . $i;
			$txtamount = "txtamount_" . $i;
			$txtddate = "txtddate_" . $i;
			$consbreckdown = "consbreckdown_" . $i;
			$txtbookingid = "txtbookingid_" . $i;
			$txtreqqnty = "txtreqqnty_" . $i;
			$txtreqamount = "txtreqamount_" . $i;

			$uom_id = str_replace("'", "", $$txtuom);
			$job = str_replace("'", "", $$txtjob_id);
			$embcostid = str_replace("'", "", $$txtembcostid);
			$gmtitemid = str_replace("'", "", $$txtgmtitemid);
			$reqqnty = str_replace("'", "", $$txtreqqnty);
			$woq = str_replace("'", "", $$txtwoq);
			$rate = str_replace("'", "", $$txtrate);
			$amt = str_replace("'", "", $$txtamount);
			$Reqqnty = str_replace("'", "", $$txtreqqnty);
			$rate_cal = str_replace("'", "", $$rate);
			$colorsizesensitive_id = str_replace("'", "", $$cbocolorsizesensitive);
			$reqId = str_replace("'", "", $$txtreqId);
			$bookingid = str_replace("'", "", $$txtbookingid);
			$Reqwoq = str_replace("'", "", $$txtwoq);
			$del_ddate = str_replace("'", "", $$txtddate);

			if ($db_type == 0) $del_date = date("Y-m-d", strtotime($del_ddate));
			else $del_date = date("d-M-Y", strtotime($del_ddate));

			if (str_replace("'", '', $$consbreckdown) != '') {
				$consbreckdown = $$consbreckdown;
			} else {
				$consbreckdown = "'" . create_consbreak_down($bookingid, $reqId, str_replace("'", '', $$ReqbookingNo), str_replace("'", '', $$txtembcostid), str_replace("'", '', $cbo_company_name), $colorsizesensitive_id, $Reqwoq, $Reqqnty, $rate_cal, $amt) . "'";
			}
			$cons_breckdown = substr(str_replace("'", "", $consbreckdown), 0, 3999);
			if ($db_type == 0) $del_date = date("Y-m-d", strtotime($del_ddate));
			else $del_date = date("d-M-Y", strtotime($del_ddate));


			if (str_replace("'", '', $$txtbookingid) != "") {
				$id_arr = array();
				$data_array_up1 = array();
				$id_arr[] = str_replace("'", '', $$txtbookingid);
				$data_array_up1[str_replace("'", '', $$txtbookingid)] = explode("*", ("" . $$txtembcostid . "*" . $$txtgmtitemid . "*'" . $txt_booking_no . "'*" . $$txtreqId . "*" . $$ReqbookingNo . "*" . $$cbocolorsizesensitive . "*" . $$body_part_id . "*" . $$txtuom . "*" . $$emb_name . "*" . $$emb_type . "*" . $$txtwoq . "*" . $$txtexchrate . "*" . $$txtrate . "*" . $$txtamount . "*'" . $del_date . "'*'" . $cons_breckdown . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

				//	CONS break down===============================================================================================
				if (str_replace("'", '', $consbreckdown) != '') {
					//$data_array_cons="";
					//check_table_status( $_SESSION['menu_id'],0);
					//echo "10**delete from wo_non_ord_embl_book_cons_dtls where  wo_booking_dtls_id =".$$txtbookingid."";die;
					$rID_de1 = execute_query("delete from wo_non_ord_embl_book_cons_dtls where  wo_booking_dtls_id =" . $$txtbookingid . "", 0);
					$consbreckdown_array = explode('__', str_replace("'", '', $consbreckdown));
					for ($c = 0; $c < count($consbreckdown_array); $c++) {
						$consbreckdownarr = explode('_', $consbreckdown_array[$c]);

						if (str_replace("'", "", $consbreckdownarr[3]) != "") {
							if (!in_array(str_replace("'", "", $consbreckdownarr[3]), $new_array_color)) {
								$color_id = return_id(str_replace("'", "", $consbreckdownarr[3]), $size_library, "lib_size", "id,color_name", "399");
								$new_array_color[$color_id] = str_replace("'", "", $consbreckdownarr[3]);
							} else $color_id =  array_search(str_replace("'", "", $consbreckdownarr[3]), $new_array_color);
						} else $color_id = 0;

						/*if (!in_array(str_replace("'","",$consbreckdownarr[3]),$new_array_color)){
						$color_id = return_id( str_replace("'","",$consbreckdownarr[3]), $color_library, "lib_color", "id,color_name","399");
						$new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[3]);
					}
					else{
						$color_id =  array_search(str_replace("'","",$consbreckdownarr[3]), $new_array_color);
					}*/

						if ($add_comma != 0) $data_array_cons .= ",";
						$data_array_cons .= "(" . $id1 . "," . $$txtbookingid . ",'" . $txt_booking_no . "'," . $$txtreqId . ",'" . $consbreckdownarr[0] . "','" . $consbreckdownarr[1] . "','" . $consbreckdownarr[2] . "','" . $color_id . "','" . $consbreckdownarr[4] . "','" . $consbreckdownarr[5] . "','" . $consbreckdownarr[6] . "','" . $consbreckdownarr[7] . "','" . $consbreckdownarr[8] . "','" . $consbreckdownarr[9] . "','" . $consbreckdownarr[11] . "')";
						$id1 = $id1 + 1;
						$add_comma++;
						//echo "10** insert into wo_emb_book_con_dtls (".$field_array2.") values ".$data_array2;die;
					}
				}
			}
		}
		//CONS break down end===============================================================================================
		$flag = 1;
		if ($data_array_up1 != "") {
			$rID1 = execute_query(bulk_update_sql_statement("wo_non_ord_embl_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr));
			//echo "10**".bulk_update_sql_statement( "wo_non_ord_embl_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr );die;
			if ($rID1) $flag = 1;
			else  $flag = 0;
		}
		//echo "10**".$flag; die;
		$rID2 = 1;
		if ($field_array_cons != "") {
			if ($flag == 1) {
				$rID2 = sql_insert("wo_non_ord_embl_book_cons_dtls", $field_array_cons, $data_array_cons, 1);
				if ($rID2) $flag = 1;
				else  $flag = 0;
			}
		}
		//echo "10** insert into wo_non_ord_embl_book_cons_dtls (".$field_array_cons.") values ".$data_array_cons;die;

		check_table_status($_SESSION['menu_id'], 0);
		//echo "10**".$rID1.'='.$rID2;die;
		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $txt_booking_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "1**" . str_replace("'", "", $txt_booking_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 2) {
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		for ($i = 1; $i <= $total_row; $i++) {
			$txtpoid = "txtpoid_" . $i;
			$txtbookingid = "txtbookingid_" . $i;
			$delete_cause = str_replace("'", "", $delete_cause);
			$delete_cause = str_replace('"', '', $delete_cause);
			$delete_cause = str_replace('(', '', $delete_cause);
			$delete_cause = str_replace(')', '', $delete_cause);

			//$rID1=execute_query( "delete from wo_booking_dtls where  id in (".str_replace("'","",$$txtbookingid).")",0);
			//$rID2=execute_query( "delete from wo_emb_book_con_dtls where  wo_booking_dtls_id in(".str_replace("'","",$$txtbookingid).")",0);

			$rID1 = execute_query("update wo_non_ord_embl_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "'   where  id in (" . str_replace("'", "", $$txtbookingid) . ") and booking_no=$txt_booking_no", 0);
			$rID2 = execute_query("update wo_non_ord_embl_book_cons_dtls set status_active=0,is_deleted=1 where  wo_booking_dtls_id in(" . str_replace("'", "", $$txtbookingid) . ") and booking_no=$txt_booking_no", 0);
		}
		if ($db_type == 0) {
			if ($rID1 &&  $rID2) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_booking_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($rID1 &&  $rID2) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $txt_booking_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

function create_consbreak_down($txt_update_dtls_id, $reqId, $reqNo, $txtembcostid, $cbo_company_name, $colorsizesensitive_id, $woQty, $reqQty, $txt_avg_price, $amount)
{
	$process_loss_method = return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
	$size_library = return_library_array("select id,size_name from lib_size", "id", "size_name");

	$booking_data_arr = array();
	if ($txt_update_dtls_id == "") {
		$txt_update_dtls_id = 0;
	}
	$booking_data = sql_select("select id,wo_booking_dtls_id,description,item_color,item_size,cons,process_loss_percent,requirment,rate,amount,emb_color_size_id as color_size_table_id  from wo_non_ord_embl_book_cons_dtls where wo_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
	//echo "10**".$txt_update_dtls_id."select id,wo_booking_dtls_id,description,item_color,item_size,cons,process_loss_percent,requirment,rate,amount,emb_color_size_id as color_size_table_id  from wo_non_ord_embl_book_cons_dtls where wo_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0";die;
	foreach ($booking_data as $row) {
		$booking_data_arr[$row[csf('color_size_table_id')]][id] = $row[csf('id')];
		$booking_data_arr[$row[csf('color_size_table_id')]][description] = $row[csf('description')];
		$booking_data_arr[$row[csf('color_size_table_id')]][item_color] = $row[csf('item_color')];
		$booking_data_arr[$row[csf('color_size_table_id')]][item_size] = $row[csf('item_size')];

		$booking_data_arr[$row[csf('color_size_table_id')]][cons] += $row[csf('cons')];
		$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent] = $row[csf('process_loss_percent')];
		$booking_data_arr[$row[csf('color_size_table_id')]][requirment] += $row[csf('requirment')];
		$booking_data_arr[$row[csf('color_size_table_id')]][rate] = $row[csf('rate')];
		$booking_data_arr[$row[csf('color_size_table_id')]][amount] += $row[csf('amount')];
	}

	if ($cbo_colorsizesensitive == 4) {
		$sql = "select a.id,a.style_ref_no,b.id as emb_dtls_id, (b.fin_fab_qnty) AS fin_fab_qnty,c.id as color_size_table_id,c.color_id as color_number_id,c.size_id as size_number_id,c.qnty
from sample_development_mst a, sample_development_fabric_acc b,sample_develop_embl_color_size c where a.id=b.sample_mst_id and b.id=c.dtls_id and
b.form_type=3 and a.company_id=$cbo_company_name and b.id in($txtembcostid) and a.entry_form_id=203 and b.form_type=3 and a.status_active=1 and b.status_active=1 and c.status_active=1   and b.fin_fab_qnty>0 order by a.id, b.id";
	} else {

		$sql = "select a.id,a.style_ref_no,b.id as emb_dtls_id, (b.fin_fab_qnty) AS fin_fab_qnty,c.id as color_size_table_id,c.color_id as color_number_id,c.size_id as size_number_id,c.qnty
from sample_development_mst a, sample_development_fabric_acc b,sample_develop_embl_color_size c where a.id=b.sample_mst_id and b.id=c.dtls_id and
b.form_type=3 and a.company_id=$cbo_company_name and b.id in($txtembcostid) and a.entry_form_id=203 and b.form_type=3 and a.status_active=1 and b.status_active=1 and c.status_active=1   and b.fin_fab_qnty>0 order by a.id, b.id";
	}
	//echo $sql;die;
	$data_array = sql_select($sql);

	/*$cu_booking_data_arr=array();
	$cu_booking_data=sql_select("select a.pre_cost_fabric_cost_dtls_id,b.id,b.wo_booking_dtls_id,b.po_break_down_id,b.color_number_id,b.gmts_sizes,b.requirment,b.article_number  from wo_booking_dtls a, wo_emb_book_con_dtls b where a.id=b.wo_booking_dtls_id and b.po_break_down_id in($txt_po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id not in($txt_update_dtls_id)");
	foreach($cu_booking_data as $cu_booking_data_row){
		if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('color_number_id')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==2 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('gmts_sizes')]][$cu_booking_data_row[csf('article_number')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==4 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('color_number_id')]][$cu_booking_data_row[csf('gmts_sizes')]][$cu_booking_data_row[csf('article_number')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==0 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]]+=$cu_booking_data_row[csf('requirment')];
		}
	}*/


	//	echo "10**".$txt_update_dtls_id.'FF';die;
	if (count($data_array) > 0) {
		$i = 0;
		$cons_breck_down = "";
		foreach ($data_array as $row) {
			if ($colorsizesensitive_id == 4) {
				$txt_req_quantity = $row[csf('qnty')];
				$txtwoq_cal = def_number_format($txt_req_quantity, 5, "");
			} else if ($cbo_colorsizesensitive == 0) {
				$txt_req_quantity = $row[csf('qnty')];
				$txtwoq_cal = def_number_format($txtwoq_cal, 5, "");
			}
			//if($txtwoq_cal>0){

			$i++;
			$color_number_id = $row[csf('color_number_id')];
			$size_number_id = $row[csf('size_number_id')];
			$colorsizetableid = $row[csf('color_size_table_id')];

			$description = $booking_data_arr[$colorsizetableid][description];
			if ($description == "") $description = 0;
			$item_color = $booking_data_arr[$colorsizetableid][item_color];
			//echo $item_color.'T=';
			if ($item_color == 0 || $item_color == "") {
				$item_color = $color_number_id;
			}
			$item_size = $booking_data_arr[$colorsizetableid][item_size];
			if ($item_size == 0 || $item_size == "") {
				$item_size = $size_library[$size_number_id];
			}

			// echo "10**".$row[csf('size_number_id')].'='.$item_size;die;

			//$cons=$booking_data_arr[$row[csf('color_size_table_id')]][cons];

			$rate = $booking_data_arr[$colorsizetableid][rate];
			if ($rate == 0 || $rate == "") $rate = $txt_avg_price;

			//$item_size=$size_library[$row[csf('size_number_id')]];



			//$item_color = $row[csf('color_number_id')];
			//$txt_req_quantity=$row[csf('qnty')];

			if ($colorsizetableid == "") $colorsizetableid = 0;
			$excess = 0;
			//$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
			//$excess=$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent];
			//$requirment=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
			//if($requirment==0 || $requirment=="") $txt_req_quantity=$txt_req_quantity;
			//else $txt_req_quantity=$requirment;

			//if($rate==0 || $rate=="") $rate=$txt_avg_price;
			$amount_cal = $txtwoq_cal * $txt_avg_price;
			$pcs = 0;

			//$txtwoq_cal =number_format_common(($txtwoq_qty/$txtwoq) * ($poreqqty),5,0);
			//echo "10**".$description.'='.$item_color.'='.$item_size.'='.$txtwoq_cal.'='.$excess.'='.$txtwoq_cal.'='.$txt_avg_price.'='.$amount_cal.'='.$pcs.'='.$colorsizetableid.'='.$reqQty.'XdX';die;
			//echo "10**".$color_number_id.'='.$item_color.'='.$colorsizetableid;die;

			if ($txtwoq_cal > 0) {
				if ($woQty) $txtwoq_cal = $txtwoq_cal;
				else $txtwoq_cal = 0;
				if ($cons_breck_down == "") {
					$cons_breck_down .= $color_number_id . '_' . $size_number_id . '_' . $description . '_' . $item_color . '_' . $item_size . '_' . $txtwoq_cal . '_' . $excess . '_' . $txtwoq_cal . '_' . $txt_avg_price . '_' . $amount_cal . '_' . $pcs . '_' . $colorsizetableid . "_" . $reqQty;
				} else {
					$cons_breck_down .= "__" . $color_number_id . '_' . $size_number_id . '_' . $description . '_' . $item_color . '_' . $item_size . '_' . $txtwoq_cal . '_' . $excess . '_' . $txtwoq_cal . '_' . $txt_avg_price . '_' . $amount_cal . '_' . $pcs . '_' . $colorsizetableid . "_" . $reqQty;
				}
				//check_table_status( $_SESSION['menu_id'],0);
				//echo "10**".$cons_breck_down;die;
				/*if(cons_breck_down==""){
					cons_breck_down+=pocolorid+'_'+gmtssizesid+'_'+des+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid+'_'+reqqty;
				}
				else{
					cons_breck_down+="__"+pocolorid+'_'+gmtssizesid+'_'+des+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid+'_'+reqqty;
				}*/
			}
		}
		return $cons_breck_down;
	}
	exit();
}

if ($action == "show_trim_booking") {
	extract($_REQUEST);
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	if ($garments_nature == 0) {
		$garment_nature_cond = "";
	} else {
		$garment_nature_cond = " and a.garments_nature=$garments_nature";
	}
	$cu_booking_arr = array();

	$sql_cu_booking = sql_select("select a.id as booking_id, a.embl_cost_dtls_id, a.booking_no, a.booking_type,a.delivery_date,a.gmt_item_id,a.req_id, a.req_no,a.req_booking_no,a.emb_name,a.emb_type,a.body_part_id,a.uom_id, a.sensitivity,a.cons_break_down,a.wo_qnty, a.exchange_rate,a.rate, a.amount, (a.wo_qnty) AS cu_wo_qnty,a.rate,a.amount as cu_amount,b.fin_fab_qnty as req_qty,b.amount as req_amt
        from wo_non_ord_embl_booking_dtls a, sample_development_fabric_acc b where b.id=a.embl_cost_dtls_id and b.form_type=3  and a.entry_form_id=399 and a.wo_qnty>0 and  a.id not in($wo_cost_emb_id) and b.id in($req_embl_dtls_id) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  order by a.id");
	foreach ($sql_cu_booking as $row_cu_booking) {
		$cu_booking_arr[$row_cu_booking[csf('req_booking_no')]][$row_cu_booking[csf('embl_cost_dtls_id')]][$row_cu_booking[csf('gmt_item_id')]]['cu_woq'][$row_cu_booking[csf('req_id')]] = $row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('req_booking_no')]][$row_cu_booking[csf('embl_cost_dtls_id')]][$row_cu_booking[csf('gmt_item_id')]]['cu_amount'][$row_cu_booking[csf('req_id')]] = $row_cu_booking[csf('cu_amount')];
	}
	unset($sql_cu_booking);

	$sql = "select a.id as booking_id, a.embl_cost_dtls_id, a.booking_no, a.booking_type,a.delivery_date,a.gmt_item_id,a.req_id,a.req_no,a.req_booking_no,a.emb_name,a.emb_type,a.body_part_id,a.uom_id, a.sensitivity,a.cons_break_down,a.wo_qnty, a.exchange_rate,a.rate, a.amount, (a.wo_qnty) AS wo_qnty,a.rate,a.amount,b.fin_fab_qnty as req_qty,b.amount as req_amt
        from wo_non_ord_embl_booking_dtls a, sample_development_fabric_acc b where b.id=a.embl_cost_dtls_id and b.form_type=3  and a.entry_form_id=399 and  a.id in($wo_cost_emb_id) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  order by a.id";
	//$job_and_trimgroup_level=array();
	$i = 1;
	$nameArray = sql_select($sql);
?>
	<input type="hidden" id="strdata" value='<? //echo json_encode($job_and_trimgroup_level); 
												?>' style="background-color:#CCC" />
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table">
		<thead>
			<th width="20">SL</th>
			<th width="100">Booking No</th>
			<th width="100">Req. No</th>
			<th width="100">Gmt.Item</th>
			<th width="100">Emb. Name</th>
			<th width="150">Body Part</th>
			<th width="150">Emb. Type</th>
			<th width="70">Req. Qnty</th>
			<th width="50">UOM</th>
			<th width="80">CU WOQ</th>
			<th width="80">Bal WOQ</th>
			<th width="100">Sensitivity</th>
			<th width="80">WOQ PCS</th>
			<th width="55">Exch.Rate</th>
			<th width="80">Rate</th>
			<th width="80">Amount</th>
			<th width="">Delv. Date</th>
		</thead>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table" id="tbl_list_search">
		<tbody>
			<?
			foreach ($nameArray as $selectResult) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";

				$pre_cost_emb_id = $selectResult[csf('embl_cost_dtls_id')];

				$cu_woq = $cu_booking_arr[$selectResult[csf('req_booking_no')]][$pre_cost_emb_id][$selectResult[csf('gmt_item_id')]]['cu_woq'][$selectResult[csf('req_id')]];
				$cu_amount = $cu_booking_arr[$selectResult[csf('req_booking_no')]][$pre_cost_emb_id][$selectResult[csf('gmt_item_id')]]['cu_amount'][$selectResult[csf('req_id')]];

				$woq = $job_and_trimgroup_level[$selectResult[csf('job_no')]][$pre_cost_emb_id][$selectResult[csf('gmt_item_id')]]['woq'][$selectResult[csf('req_id')]];
				$amount = $job_and_trimgroup_level[$selectResult[csf('job_no')]][$pre_cost_emb_id][$selectResult[csf('gmt_item_id')]]['amount'][$selectResult[csf('req_id')]];
				$rate = $amount / $woq;
				$total_amount += $amount;
				if ($selectResult[csf('emb_name')] == 1) {
					$emb_type_name = $emblishment_print_type[$selectResult[csf('emb_type')]];
				}
				if ($selectResult[csf('emb_name')] == 2) {
					$emb_type_name = $emblishment_embroy_type[$selectResult[csf('emb_type')]];
				}
				if ($selectResult[csf('emb_name')] == 3) {
					$emb_type_name = $emblishment_wash_type[$selectResult[csf('emb_type')]];
				}
				if ($selectResult[csf('emb_name')] == 4) {
					$emb_type_name = $emblishment_spwork_type[$selectResult[csf('emb_type')]];
				}
				if ($selectResult[csf('emb_name')] == 5) {
					$emb_type_name = $emblishment_gmts_type[$selectResult[csf('emb_type')]];
				}
				$req_qnty_cons_uom = $selectResult[csf('req_qty')];
				$req_amount_cons_uom = $selectResult[csf('req_amt')];
				$bal_woq = def_number_format($req_qnty_cons_uom - $cu_woq, 5, "");
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>">
					<td width="20"><? echo $i; ?></td>
					<td width="100">
						<? echo  $selectResult[csf('req_booking_no')]; ?>
						<input type="hidden" id="txtreqno_<? echo $i; ?>" value="<? echo $selectResult[csf('req_no')]; ?>" style="width:30px" class="text_boxes" readonly />
					</td>
					<td width="100"> <? echo $selectResult[csf('req_no')]; ?>
						<input type="hidden" id="txtbookingid_<? echo $i; ?>" value="<? echo $selectResult[csf('booking_id')]; ?>" readonly />
						<input type="hidden" id="txtreqId_<? echo $i; ?>" value="<? echo $selectResult[csf('req_id')]; ?>" readonly />
						<input type="hidden" id="ReqbookingNo_<? echo $i; ?>" value="<? echo  $selectResult[csf('req_booking_no')]; ?>" readonly />

					</td>
					<td width="100">
						<? echo $garments_item[$selectResult[csf('gmt_item_id')]]; ?>
						<input type="hidden" id="txtgmtitemid_<? echo $i; ?>" value="<? echo $selectResult[csf('gmt_item_id')]; ?>" readonly />
					</td>
					<td width="100">
						<? echo $emblishment_name_array[$selectResult[csf('emb_name')]]; ?>
						<input type="hidden" id="txtembcostid_<? echo $i; ?>" value="<? echo $selectResult[csf('embl_cost_dtls_id')]; ?>" readonly />
						<input type="hidden" id="emb_name_<? echo $i; ?>" value="<? echo $selectResult[csf('emb_name')]; ?>" readonly />
					</td>
					<td width="150">
						<? echo $body_part[$selectResult[csf('body_part_id')]]; ?>
						<input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="body_part_id_<? echo $i; ?>" value="<? echo $selectResult[csf('body_part_id')]; ?>" />
					</td>
					<td width="150">
						<? echo $emb_type_name; ?>
						<input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="emb_type_<? echo $i; ?>" value="<? echo $selectResult[csf('emb_type')]; ?>" />
					</td>
					<td width="70" align="right">
						<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i; ?>" value="<? echo number_format($req_qnty_cons_uom, 4, '.', ''); ?>" readonly />
						<input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i; ?>" value="<? echo number_format($req_amount_cons_uom, 4, '.', ''); ?>" readonly />

					</td>
					<td width="50">
						Pcs
						<? //echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('gmt_item')]][cons_uom]];
						?>
						<input type="hidden" id="txtuom_<? echo $i; ?>" value="<? //echo $sql_lib_item_group_array[$selectResult[csf('gmt_item')]][cons_uom];
																				?>" readonly />
					</td>
					<td width="80" align="right">
						<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i; ?>" value="<? echo number_format($selectResult[csf('cu_woq')], 4, '.', ''); ?>" readonly />
						<input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i; ?>" value="<? echo number_format($selectResult[csf('cu_amount')], 4, '.', ''); ?>" readonly />
					</td>
					<td width="80" align="right">
						<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i; ?>" value="<? echo number_format($bal_woq, 4, '.', ''); ?>" readonly />
					</td>
					<td width="100" align="right">
						<? echo create_drop_down("cbocolorsizesensitive_" . $i, 100, $size_color_sensitive, "", 1, "--Select--", "4", "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)", 1, "1,2,3,4"); ?>
					</td>
					<td width="80" align="right">

						<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i; ?>" value="<? echo number_format($selectResult[csf('wo_qnty')], 4, '.', ''); ?>" onClick="open_consumption_popup('requires/embellishment_wo_without_order_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtreqId_<? echo $i; ?>',<? echo $i; ?>)" readonly />
					</td>
					<td width="55" align="right">
						<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i; ?>" value="<? echo $selectResult[csf('exchange_rate')]; ?>" readonly />

					</td>
					<td width="80" align="right">
						<?
						$ratetexcolor = "#000000";
						$decimal = explode(".", $rate_cons_uom);
						if (strlen($decimal[1] > 6)) {
							$ratetexcolor = "#F00";
						}
						?>
						<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;   text-align:right; color:<? echo $ratetexcolor; ?>; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i; ?>" value="<? echo $selectResult[csf('rate')]; ?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />

						<input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i; ?>" value="<? echo $selectResult[csf('rate')]; ?>" readonly />

					</td>
					<td width="80" align="right">
						<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i; ?>" value="<? echo number_format($selectResult[csf('amount')], 4, '.', ''); ?>" readonly />
					</td>
					<td width="" align="right">
						<input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i; ?>" class="datepicker" value="<? echo change_date_format($selectResult[csf('delivery_date')]); ?>" readonly />
						<input type="hidden" id="consbreckdown_<? echo $i; ?>" value="<? //echo $selectResult[csf('cons_break_down')]; 
																						?>" />

					</td>
				</tr>
			<?
				$i++;
			}


			?>
		</tbody>
	</table>
	<table width="1500" class="rpt_table" border="0" rules="all">
		<tfoot>
			<tr>
				<th width="20">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="150">&nbsp;</th>
				<th width="150">&nbsp;</th>
				<th width="70"><? echo $tot_req_qty; ?></th>
				<th width="50">&nbsp;</th>
				<th width="80"><? echo $tot_cu_woq; ?></th>
				<th width="80"><? echo $tot_bal_woq; ?></th>
				<th width="100">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="55">&nbsp;</th>
				<th width="80"><input type="hidden" id="tot_amount" value="<? echo  number_format($total_amount, 4, '.', ''); ?>" class="text_boxes_numeric" style="width:140px" /></th>
				<th width="80"><input type="hidden" id="tot_amount" value="<? echo  number_format($total_amount, 4, '.', ''); ?>" class="text_boxes_numeric" style="width:140px" /></th>
				<th><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly /></th>
			</tr>
		</tfoot>
	</table>
	<table width="1100" colspan="14" cellspacing="0" class="" border="0">
		<tr>
			<td align="center" class="button_container">
				<? echo load_submit_buttons($permission, "fnc_trims_booking_dtls", 1, 0, "reset_form('','booking_list_view','','','')", 2); ?>
			</td>
		</tr>
	</table>
<?
	exit();
}

if ($action == "show_trim_booking_list") {
	extract($_REQUEST);

	$sql = "select a.id as booking_dtls_id,a.embl_cost_dtls_id,a.req_booking_no,a.req_id, a.req_no,a.gmt_item_id,a.req_id, a.req_no,a.req_booking_no,a.emb_name,a.emb_type,a.body_part_id,a.uom_id, a.sensitivity,a.wo_qnty, a.exchange_rate, a.rate, a.amount,a.delivery_date from wo_non_ord_embl_booking_dtls a where  a.booking_no=$txt_booking_no and a.is_deleted=0 and a.status_active=1 order by a.id";
	$i = 1;
	$nameArray = sql_select($sql);
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="100">Booking No</th>
			<th width="100">Req. No</th>
			<th width="100">Gmts. Item</th>
			<th width="100">Emb Name</th>
			<th width="150">Emb Type</th>
			<th width="150">Body Part</th>
			<th width="80">UOM</th>
			<th width="100">Sensitivity</th>
			<th width="80">WOQ PCS</th>
			<th width="80">Exch.Rate</th>
			<th width="80">Rate</th>
			<th width="80">Amount</th>
			<th width="">Delv. Date</th>
		</thead>
		<tbody id="save_list">
			<?

			foreach ($nameArray as $selectResult) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";

				/*$cbo_currency_job=$selectResult[csf('currency_id')];
	$exchange_rate=$selectResult[csf('exchange_rate')];
	if($cbo_currency==$cbo_currency_job){
		$exchange_rate=1;
	}*/
				if ($selectResult[csf('emb_name')] == 1) {
					$emb_type_name = $emblishment_print_type[$selectResult[csf('emb_type')]];
				}
				if ($selectResult[csf('emb_name')] == 2) {
					$emb_type_name = $emblishment_embroy_type[$selectResult[csf('emb_type')]];
				}
				if ($selectResult[csf('emb_name')] == 3) {
					$emb_type_name = $emblishment_wash_type[$selectResult[csf('emb_type')]];
				}
				if ($selectResult[csf('emb_name')] == 4) {
					$emb_type_name = $emblishment_spwork_type[$selectResult[csf('emb_type')]];
				}
				if ($selectResult[csf('emb_name')] == 5) {
					$emb_type_name = $emblishment_gmts_type[$selectResult[csf('emb_type')]];
				}


			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="fnc_show_booking(<? echo $selectResult[csf('booking_dtls_id')]; ?>,'<? echo $selectResult[csf('embl_cost_dtls_id')]; ?>','<? echo $selectResult[csf('req_booking_no')]; ?>','<? echo $selectResult[csf('req_id')]; ?>','<? echo $selectResult[csf('req_no')]; ?>')">
					<td width="40"><? echo $i; ?></td>
					<td width="100">
						<? echo $selectResult[csf('req_booking_no')]; ?>
					</td>
					<td width="100">
						<? echo $selectResult[csf('req_no')]; ?>
					</td>
					<td width="100">
						<? echo $garments_item[$selectResult[csf('gmt_item_id')]]; ?>
					</td>
					<td width="100">
						<? echo $emblishment_name_array[$selectResult[csf('emb_name')]]; ?>
					</td>
					<td width="150">
						<? echo $emb_type_name; ?>
					</td>
					<td width="150">
						<? echo $body_part[$selectResult[csf('body_part_id')]]; ?>
					</td>
					<td width="80">
					Pcs
						<? //echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('gmt_item')]][cons_uom]];
						?>
					</td>
					<td width="100" align="right">
						<? echo $size_color_sensitive[$selectResult[csf("sensitivity")]]; ?>
					</td>
					<td width="80" align="right">
						<? echo number_format($selectResult[csf("wo_qnty")], 4, '.', ''); ?>
					</td>
					<td width="80" align="right">
						<? echo $selectResult[csf("exchange_rate")]; ?>
					</td>
					<td width="80" align="right">
						<? echo number_format($selectResult[csf('rate')], 4, '.', ''); ?>
					</td>
					<td width="80" align="right">
						<? echo number_format($selectResult[csf('amount')], 4, '.', ''); ?>
					</td>
					<td width="" align="right">
						<? echo change_date_format($selectResult[csf('delivery_date')], "dd-mm-yyyy", "-"); ?>
					</td>
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

if ($action == "fabric_booking_popup") {
	echo load_html_head_contents("Booking Search", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

	<script>
		function js_set_value(booking_no) {
			document.getElementById('selected_booking').value = booking_no;
			parent.emailwindow.hide();
		}

		function check_orphan(str) {
			if ($("#chk_orphan").prop('checked') == true)

				$('#chk_orphan').val(1);

			else

				$('#chk_orphan').val(0);

		}
	</script>

	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="700" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>
						<th colspan="8">
							<?
							echo create_drop_down("cbo_search_category", 110, $string_search_type, '', 1, "-- Search Catagory --");
							?>
						</th>
					</thead>
					<thead>
						<th width="150">Company Name</th>
						<th width="150">Buyer Name</th>
						<th width="100">Booking No</th>
						<th width="130" colspan="2">Date Range</th>
						<th> <input type="checkbox" id="chk_orphan" onClick="check_orphan(this.value)" value="0"> Orphan WO</th>
					</thead>
					<tr class="general">
						<td> <input type="hidden" id="selected_booking">
							<?
							echo create_drop_down("cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $cbo_company_name, "load_drop_down( 'embellishment_wo_without_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
							?>
						</td>

						<td id="buyer_td">
							<?
							echo create_drop_down("cbo_buyer_name", 172, $blank_array, "", 1, "-- Select Buyer --");
							?>
						</td>
						<td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>


						<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
						<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('chk_orphan').value, 'create_booking_search_list_view', 'search_div', 'embellishment_wo_without_order_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="8" align="center" valign="middle"> <? echo load_month_buttons(1);  ?></td>
					</tr>
				</table>
				<div id="search_div"></div>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_booking_search_list_view") {
	$data = explode('_', $data);
	$without_check = $data[7];
	if ($data[0] != 0) $company = " and a.company_id='$data[0]'";
	else {
		echo "Please Select Company First.";
		die;
	}
	if ($data[1] != 0) $buyer = " and a.buyer_id='$data[1]'";
	else $buyer = ""; //{ echo "Please Select Buyer First."; die; }
	if ($db_type == 0) {
		$booking_year_cond = " and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
		if ($data[2] != "" &&  $data[3] != "") $booking_date  = "and a.booking_date  between '" . change_date_format($data[2], "yyyy-mm-dd", "-") . "' and '" . change_date_format($data[3], "yyyy-mm-dd", "-") . "'";
		else $booking_date = "";
	}
	if ($db_type == 2) {
		$booking_year_cond = " and to_char(a.insert_date,'YYYY')=$data[4]";
		if ($data[2] != "" &&  $data[3] != "") $booking_date  = "and a.booking_date  between '" . change_date_format($data[2], "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format($data[3], "yyyy-mm-dd", "-", 1) . "'";
		else $booking_date = "";
	}
	if ($data[6] == 4 || $data[6] == 0) {
		if (str_replace("'", "", $data[5]) != "") $booking_cond = " and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  ";
		else $booking_cond = "";
	}
	if ($data[6] == 1) {
		if (str_replace("'", "", $data[5]) != "") $booking_cond = " and a.booking_no_prefix_num ='$data[5]' ";
		else $booking_cond = "";
	}
	if ($data[6] == 2) {
		if (str_replace("'", "", $data[5]) != "") $booking_cond = " and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  ";
		else $booking_cond = "";
	}
	if ($data[6] == 3) {
		if (str_replace("'", "", $data[5]) != "") $booking_cond = " and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  ";
		else $booking_cond = "";
	}
	$approved = array(0 => "No", 1 => "Yes");
	$is_ready = array(0 => "No", 1 => "Yes", 2 => "No");
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$comp = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$suplier = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');
	//$po_num=return_library_array( "select job_no, job_no_prefix_num from wo_po_details_master",'job_no','job_no_prefix_num');
	//	$po_array=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');

	$arr = array(2 => $buyer_arr, 3 => $po_num, 5 => $po_array, 6 => $garments_item, 7 => $emblishment_name_array, 8 => $suplier, 9 => $approved, 10 => $is_ready);
	//echo $without_check.'DDDXCX';;
	if ($without_check == 1)
		$sql = "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.category_id,a.supplier_id, a.is_approved, a.ready_to_approved, a.pay_mode from wo_non_ord_embl_booking_mst a where  a.status_active=1 and a.is_deleted=0  $company " . set_user_lavel_filtering(' and a.buyer_id', 'buyer_id') . " $buyer $booking_date $booking_cond  and a.booking_type=6 and a.entry_form_id=399 and a.booking_no not in (select booking_no from  wo_non_ord_embl_booking_dtls where status_active=1) order by a.id DESC";
	else
		$sql = "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.category_id,a.supplier_id, a.is_approved, a.ready_to_approved, a.pay_mode from wo_non_ord_embl_booking_mst a where  a.status_active=1 and a.is_deleted=0  $company " . set_user_lavel_filtering(' and a.buyer_id', 'buyer_id') . " $buyer $booking_date $booking_cond  and a.booking_type=6 and a.entry_form_id=399 order by a.id DESC";
	$result = sql_select($sql);


?>
	<br>
	<table class="rpt_table" id="rpt_tablelist_view" rules="all" width="820" cellspacing="0" cellpadding="0" border="0">
		<thead>
			<tr>
				<th width="30">SL No</th>
				<th width="110">Booking No</th>
				<th width="100">Booking Date</th>
				<th width="120">Buyer</th>
				<th width="120">Supplier</th>
				<th width="70">Approved</th>
				<th width="">Is-Ready</th>
			</tr>
		</thead>
	</table>
	<div style="max-height:320px; width:820px; overflow-y:scroll" id="">
		<table class="rpt_table" id="list_view" rules="all" width="800" height="" cellspacing="0" cellpadding="0" border="0">
			<tbody>
				<?
				$i = 0;
				foreach ($result as $row) {
					$i++;
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";
					$suplier_name = "";
					if ($row[csf('pay_mode')] == 3 || $row[csf('pay_mode')] == 5) $suplier_name = $comp[$row[csf('supplier_id')]];
					else $suplier_name = $suplier[$row[csf('supplier_id')]];
				?>
					<tr onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">
						<td width="30"><? echo $i; ?></td>
						<td width="110">
							<p><? echo $row[csf('booking_no_prefix_num')]; ?></p>
						</td>
						<td width="100">
							<p><? echo change_date_format($row[csf('booking_date')]); ?></p>
						</td>

						<td width="120" style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
						<td width="120" style="word-break:break-all"><? echo $suplier_name; ?></td>
						<td width="70">
							<p><? echo $yes_no[$row[csf('is_approved')]]; ?></p>
						</td>
						<td width="">
							<p><? echo $is_ready[$row[csf('ready_to_approved')]]; ?></p>
						</td>
					</tr>
				<?
				}
				?>
			</tbody>
		</table>
	</div>
<?
	exit();
}

if ($action == "populate_data_from_search_popup") {
	// $sql= "select booking_no,booking_date,company_id,buyer_id,currency_id,exchange_rate,is_short,pay_mode,booking_month,supplier_id,attention,delivery_date,source,booking_year,is_approved,ready_to_approved,cbo_level,is_short,remarks from wo_booking_mst  where booking_no='$data' and  status_active=1 and is_deleted=0";
	$sql = "select a.id,a.booking_no_prefix_num, a.booking_no, a.currency_id,a.booking_date, a.delivery_date,a.company_id, a.buyer_id, a.category_id,a.supplier_id, a.is_approved, a.ready_to_approved, a.pay_mode,a.attention, a.tenor, a.source_id ,a.remarks,a.delivery_to from wo_non_ord_embl_booking_mst a where  a.booking_no='$data' order by a.id DESC";

	$data_array = sql_select($sql);
	foreach ($data_array as $row) {
		$supplier_library = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");

		echo "document.getElementById('cbo_company_name').value = '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_buyer_name').value = '" . $row[csf("buyer_id")] . "';\n";
		echo "document.getElementById('txt_booking_no').value = '" . $row[csf("booking_no")] . "';\n";
		echo "document.getElementById('cbo_currency').value = '" . $row[csf("currency_id")] . "';\n";

		echo "document.getElementById('cbo_pay_mode').value = '" . $row[csf("pay_mode")] . "';\n";
		echo "document.getElementById('txt_booking_date').value = '" . change_date_format($row[csf("booking_date")], 'dd-mm-yyyy', '-') . "';\n";
		echo "load_drop_down( 'requires/embellishment_wo_without_order_controller', '" . $row[csf("pay_mode")] . "'+'_'+'" . $row[csf("buyer_id")] . "'+'_'+'" . $row[csf("company_id")] . "', 'load_drop_down_supplier', 'supplier_td' );\n";
		echo "document.getElementById('cbo_supplier_name').value = '" . $row[csf("supplier_id")] . "';\n";
		/*if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5)
		{

			echo "document.getElementById('cbo_supplier_name').value = '".$company_library[$row[csf("supplier_id")]]."';\n";
			echo "document.getElementById('hidden_supplier_name').value = '".$company_library[$row[csf("supplier_id")]]."';\n";
		}
		else
		{
			echo "document.getElementById('cbo_supplier_name').value = '".$supplier_library[$row[csf("supplier_id")]]."';\n";
			echo "document.getElementById('hidden_supplier_name').value = '".$supplier_library[$row[csf("supplier_id")]]."';\n";
		}*/
		echo "document.getElementById('txt_attention').value = '" . $row[csf("attention")] . "';\n";
		echo "document.getElementById('txt_tenor').value = '" . $row[csf("tenor")] . "';\n";
		echo "document.getElementById('txt_delivery_date').value = '" . change_date_format($row[csf("delivery_date")], 'dd-mm-yyyy', '-') . "';\n";
		echo "document.getElementById('cbo_source').value = '" . $row[csf("source_id")] . "';\n";
		echo "document.getElementById('id_approved_id').value = '" . $row[csf("is_approved")] . "';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '" . $row[csf("ready_to_approved")] . "';\n";

		echo "document.getElementById('remarks').value = '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('update_id').value = '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('txt_delivery_to').value = '" . $row[csf("delivery_to")] . "';\n";


		echo " $('#cbo_company_name').attr('disabled',true);\n";
		echo " $('#cbo_supplier_name').attr('disabled',true);\n";
		echo " $('#cbo_currency').attr('disabled',true);\n";

		echo " $('#cbo_buyer_name').attr('disabled',true);\n";
		echo "fnc_show_booking_list();\n";
		if ($row[csf("is_approved")] == 1) {
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
		} else {
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
		}
	}
}


if ($action == "show_embellishment_booking_report") {
	extract($_REQUEST);
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$txt_booking_no = str_replace("'", "", $txt_booking_no);
	$cbo_company_name = str_replace("'", "", $cbo_company_name);
	$id_approved_id = str_replace("'", "", $id_approved_id);
	$report_type = str_replace("'", "", $report_type);
	$show_comment = str_replace("'", "", $show_comment);
	$cbo_template_id = str_replace("'", "", $cbo_template_id);

	$color_library = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library = return_library_array("select id, size_name from  lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$imge_arr = return_library_array("select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');
	$country_arr = return_library_array("select id,country_name from   lib_country", 'id', 'country_name');
	$supplier_name_arr = return_library_array("select id,supplier_name from   lib_supplier", 'id', 'supplier_name');
	$supplier_address_arr = return_library_array("select id,address_1 from   lib_supplier", 'id', 'address_1');
	$buyer_name_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$order_uom_arr = return_library_array("select id,order_uom  from lib_item_group", "id", "order_uom");
	$deling_marcent_arr = return_library_array("select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$season_arr = return_library_array("select id,season_name from lib_buyer_season", "id", "season_name");
	$sample_library = return_library_array("select id, sample_name from lib_sample", "id", "sample_name");

	$sql="select id from electronic_approval_setup where company_id=$cbo_company_name and page_id in(1910) and is_deleted=0";
	$res_result_arr = sql_select($sql);
	$approval_arr=array();
	foreach($res_result_arr as $row){
		$approval_arr[$row["ID"]]["ID"]=$row["ID"];
	}

	$header_info = sql_select("SELECT attention, buyer_id, delivery_date, currency_id, pay_mode, source_id, supplier_id,remarks,booking_date,is_approved from wo_non_ord_embl_booking_mst where BOOKING_NO ='$txt_booking_no' and status_active=1 and is_deleted=0");
	foreach ($header_info as $row) {
		$attribute = array('attention', 'buyer_id', 'delivery_date', 'currency_id', 'source_id', 'supplier_id', 'remarks', 'booking_date');
		foreach ($attribute as $attr) {
			${strtolower($attr)} = $row[csf($attr)];
		}
		$pay_mode_id = $row[csf('pay_mode')];
		$is_approved = $row[csf('is_approved')];
	}

	$req_sql_data = sql_select("SELECT c.dealing_marchant,c.season from wo_non_ord_embl_booking_mst a join wo_non_ord_embl_booking_dtls b on a.BOOKING_NO = b.booking_no join sample_development_mst c on b.req_id =c.id where a.booking_no='$txt_booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.dealing_marchant,c.season");
	foreach ($req_sql_data as $row) {
		$raq_data['dealing_marchant'][] = $deling_marcent_arr[$row[csf('dealing_marchant')]];
		$raq_data['season'][] = $season_arr[$row[csf('season')]];
	}
	$details_data = sql_select("SELECT b.req_no, b.sensitivity, b.req_booking_no,b.delivery_date,b.body_part_id,b.gmt_item_id, b.emb_type, b.emb_name, c.style_ref_no, d.sample_name_re , e.wo_booking_dtls_id, d.remarks_re as description,e.color_number_id, e.item_size, e.cons, e.process_loss_percent, e.requirment,e.rate, e.amount, e.emb_color_size_id,e.id as cons_dtls_id from wo_non_ord_embl_booking_mst a join wo_non_ord_embl_booking_dtls b on a.BOOKING_NO = b.booking_no join sample_development_mst c on b.req_id =c.id join sample_development_fabric_acc d on d.id=b.embl_cost_dtls_id join wo_non_ord_embl_book_cons_dtls e on b.id=e.wo_booking_dtls_id where a.booking_no='$txt_booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	foreach ($details_data as $row) {
		$booking_arr[$row[csf('req_no')]]['sensitivity'] = $row[csf('sensitivity')];
		$booking_arr[$row[csf('req_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$booking_arr[$row[csf('req_no')]]['req_booking_no'] = $row[csf('req_booking_no')];
		$attribute = array('req_no', 'sensitivity', 'req_booking_no', 'delivery_date', 'body_part_id', 'gmt_item_id', 'emb_type', 'emb_name', 'style_ref_no', 'sample_name_re', 'wo_booking_dtls_id', 'description', 'color_number_id', 'item_size', 'cons', 'process_loss_percent', 'requirment', 'rate', 'amount', 'emb_color_size_id');
		foreach ($attribute as $attr) {
			$booking_arr[$row[csf('req_no')]]['booking_dtls'][$row[csf('cons_dtls_id')]][$attr] = $row[csf($attr)];
		}
	}
?>
	<!DOCTYPE html>
	<html>

	<body>
		<div style="width:1333px" align="center">
			<table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
				<tr>
					<td align="center" colspan="7">
						<strong><? echo $company_library[$cbo_company_name]; ?> <br>
							Multiple Req. Wise Embellishment Work Order without order </strong>
					</td>
					<td colspan="2">
						Booking No. : <? echo $txt_booking_no; ?><br>
						Booking Date : <? echo change_date_format($booking_date); ?>
					</td>
				</tr>
				<tr>
					<td colspan="9"><strong>To,</strong></td>
				</tr>
				<tr>
					<td colspan="9"><? echo $supplier_name_arr[$supplier_id]; ?></td>
				</tr>
				<tr>
					<td colspan="9"><?
									if ($pay_mode_id == 5 || $pay_mode_id == 3) $address = $company_address[$supplier_id];
									else $address = $supplier_address_arr[$supplier_id];
									echo $address;
									?> </td>
				</tr>
				<tr>
					<td><strong>Attention</strong></td>
					<td>:</td>
					<td><? echo $attention; ?></td>
					<td><strong>Buyer Name</strong></td>
					<td>:</td>
					<td><? echo $buyer_name_arr[$buyer_id]; ?></td>
					<td><strong>Delivery Date</strong></td>
					<td>:</td>
					<td><? echo change_date_format($delivery_date); ?></td>
				</tr>
				<tr>
					<td><strong>Source</strong></td>
					<td>:</td>
					<td><? echo $source[$source_id]; ?></td>
					<td><strong>Currency</strong></td>
					<td>:</td>
					<td><? echo $currency[$currency_id]; ?></td>
					<td><strong>Pay mode</strong></td>
					<td>:</td>
					<td><? echo $pay_mode[$pay_mode_id]; ?></td>
				</tr>
				<tr>
					<td><strong>Dealing Merchant</strong></td>
					<td>:</td>
					<td><? echo implode(",", $raq_data['dealing_marchant']) ?></td>
					<td><strong>Season</strong></td>
					<td>:</td>
					<td><? echo implode(",", $raq_data['season']) ?></td>
					<td><strong>WO Date</strong></td>
					<td>:</td>
					<td><? echo change_date_format($booking_date); ?></td>
				</tr>
				<tr>
					<td><strong>Remarks</strong></td>
					<td>:</td>
					<td colspan="7"><? echo $remarks ?></td>
				</tr>
			</table>
			<table border="1" align="left" class="rpt_table" cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-top: 10px"><?
																																				$grand_amount_total = 0;
																																				foreach ($booking_arr as $req_no => $value) {
																																					$i = 1;
																																					$k = 1;
																																					$sub_requirment_total = 0;
																																					$sub_amount_total = 0;
																																					if ($i == 1) {
																																				?>
						<tr>
							<td colspan="14"><strong><? echo $size_color_sensitive[$value['sensitivity']] ?>, Style NO:<? echo $value['style_ref_no'] ?> , Req. No: <? echo $req_no ?>, Sample Booking No: <? echo $value['req_booking_no'] ?>
							</td>
						</tr>
						<tr>
							<td><strong>Sl No</strong></td>
							<td><strong>Sample Type</strong></td>
							<td><strong>Emb. name</strong></td>
							<td><strong>Emb. Type</strong></td>
							<td><strong>Gmts Item</strong></td>
							<td><strong>Body Part</strong></td>
							<td><strong>Delivery Date</strong></td>
							<td><strong>Gmts Color</strong></td>
							<td><strong>Item Size</strong></td>
							<td><strong>Gmts Size</strong></td>
							<td><strong>WO Qty(Pcs)</strong></td>
							<td><strong>Rate(Pcs)</strong></td>
							<td><strong>Amount</strong></td>
							<td><strong>Remarks</strong></td>
						</tr>
					<?
																																					}
																																					foreach ($value['booking_dtls'] as $data) {
																																						$i++;
																																						if ($data['emb_name'] == 1) {
																																							$emb_type_name = $emblishment_print_type[$data['emb_type']];
																																						}
																																						if ($data['emb_name'] == 2) {
																																							$emb_type_name = $emblishment_embroy_type[$data['emb_type']];
																																						}
																																						if ($data['emb_name'] == 3) {
																																							$emb_type_name = $emblishment_wash_type[$data['emb_type']];
																																						}
																																						if ($data['emb_name'] == 4) {
																																							$emb_type_name = $emblishment_spwork_type[$data['emb_type']];
																																						}
																																						if ($data['emb_name'] == 5) {
																																							$emb_type_name = $emblishment_gmts_type[$data['emb_type']];
																																						}
					?>
						<tr>
							<td><? echo $k; ?></td>
							<td><? echo $sample_library[$data['sample_name_re']] ?></td>
							<td><? echo $emblishment_name_array[$data['emb_name']] ?></td>
							<td><? echo $emb_type_name; ?></td>
							<td><? echo $garments_item[$data['gmt_item_id']] ?></td>
							<td><? echo $body_part[$data['body_part_id']] ?></td>
							<td><? echo change_date_format($data['delivery_date']) ?></td>
							<td><? echo $color_library[$data['color_number_id']] ?></td>
							<td><? echo $data['item_size'] ?></td>
							<td><? echo $data['item_size'] ?></td>
							<td><? echo $data['requirment'] ?></td>
							<td><? echo number_format($data['rate'], 2) ?></td>
							<td><? echo number_format($data['amount'], 2) ?></td>
							<td><? echo $data['description'] ?></td>
						</tr>
					<?
																																						$k++;
																																						$sub_requirment_total += $data['requirment'];
																																						$sub_amount_total += $data['amount'];
																																						$grand_requirment_total += $data['requirment'];
																																						$grand_amount_total += $data['amount'];
																																					}
					?>
					<tr>
						<td colspan="10" align="right"><strong>Item Total&nbsp;</strong></td>
						<td><strong><? echo number_format($sub_requirment_total, 2) ?></strong></td>
						<td><strong>Total Amount&nbsp;</strong></td>
						<td><strong><? echo number_format($sub_amount_total, 2) ?></strong></td>
						<td></td>
					</tr>
				<?

																																				}
				?>
				<tr>
					<td colspan="10" align="right"><strong>GR. Total&nbsp;</strong></td>
					<td><strong><? echo number_format($grand_requirment_total, 2) ?></strong></td>
					<td><strong>GR. Total&nbsp;</strong></td>
					<td><strong><? echo number_format($grand_amount_total, 2) ?></strong></td>
					<td></td>
				</tr>
				<?
				$mcurrency = "";
				$dcurrency = "";
				if ($currency_id == 1) {
					$mcurrency = 'Taka';
					$dcurrency = 'Paisa';
				}
				if ($currency_id == 2) {
					$mcurrency = 'USD';
					$dcurrency = 'CENTS';
				}
				if ($currency_id == 3) {
					$mcurrency = 'EURO';
					$dcurrency = 'CENTS';
				}
				?>
				<tr>
					<td colspan="4"><strong>Total Booking Amount (in word)</strong></td>
					<td colspan="10"><strong><? echo number_to_words(number_format($grand_amount_total, 2), $mcurrency, $dcurrency) ?></strong></td>
				</tr>
			</table>
			<table width="100%">
				<tr>
					<td width="49%"><? echo get_spacial_instruction($txt_booking_no); ?></td>
					<td width="2%">&nbsp;</td>
					<? $data_array = sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0"); ?>
					<td width="49%" valign="top">
						<table width="100%" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
							<tr style="border:1px solid black;">
								<td colspan="3" style="border:1px solid black;">Approval Status</td>
							</tr>
							<tr style="border:1px solid black;">
								<td width="3%" style="border:1px solid black;">Sl</td>
								<td width="50%" style="border:1px solid black;">Name</td>
								<td width="27%" style="border:1px solid black;">Approval Date</td>
								<td width="20%" style="border:1px solid black;">Approval No</td>
							</tr>
							<?
							$i;
							foreach ($data_array as $row) {
							?>
								<tr style="border:1px solid black;">
									<td width="3%" style="border:1px solid black;"><? echo $i; ?></td>
									<td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')]; ?></td>
									<td width="27%" style="border:1px solid black;"><? echo change_date_format($row[csf('approved_date')], "dd-mm-yyyy", "-"); ?></td>
									<td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')]; ?></td>
								</tr>
							<?
								$i++;
							}
							?>
						</table>
					</td>
				</tr>
			</table>
			<br>
				<table width="780" align="center">
						<tr>
							<div style="text-align:center;font-size:xx-large; font-style:italic; margin-top:20px; color:#FF0000;">
									<?
									if(count($approval_arr)>0)
									{				
										if($is_approved == 0){echo "Draft";}else{}
									}
									?>
							</div>
						</tr>
				</table>
			<br
			<div style="margin-top:-5px;"><? echo signature_table(133, $cbo_company_name, "1300px", $cbo_template_id,1); ?></div>
		</div>
	</body>

	</html>
<?


}

if ($action == "show_embellishment_booking_report2") //Shariar 9-7
{
	extract($_REQUEST);
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$txt_booking_no = str_replace("'", "", $txt_booking_no);
	$cbo_company_name = str_replace("'", "", $cbo_company_name);
	$id_approved_id = str_replace("'", "", $id_approved_id);
	$report_type = str_replace("'", "", $report_type);
	$show_comment = str_replace("'", "", $show_comment);
	$cbo_template_id = str_replace("'", "", $cbo_template_id);

	$color_library = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library = return_library_array("select id, size_name from  lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$imge_arr = return_library_array("select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');
	$country_arr = return_library_array("select id,country_name from   lib_country", 'id', 'country_name');
	$supplier_name_arr = return_library_array("select id,supplier_name from   lib_supplier", 'id', 'supplier_name');
	$supplier_address_arr = return_library_array("select id,address_1 from   lib_supplier", 'id', 'address_1');
	$buyer_name_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$order_uom_arr = return_library_array("select id,order_uom  from lib_item_group", "id", "order_uom");
	$deling_marcent_arr = return_library_array("select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$season_arr = return_library_array("select id,season_name from lib_buyer_season", "id", "season_name");
	$team_leader_arr = return_library_array("select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
	$brand_arr = return_library_array("select id, brand_name from lib_buyer_brand", "id", "brand_name");

	$header_info = sql_select("SELECT attention, buyer_id, delivery_date, currency_id, pay_mode, source_id, supplier_id,remarks,booking_date from wo_non_ord_embl_booking_mst where BOOKING_NO ='$txt_booking_no' and status_active=1 and is_deleted=0");
	foreach ($header_info as $row) {
		$attribute = array('attention', 'buyer_id', 'delivery_date', 'currency_id', 'source_id', 'supplier_id', 'remarks', 'booking_date');
		foreach ($attribute as $attr) {
			${strtolower($attr)} = $row[csf($attr)];
		}
		$pay_mode_id = $row[csf('pay_mode')];
	}
	$req_sql_data = sql_select("SELECT a.delivery_to,c.style_ref_no,c.team_leader,c.brand_id,c.requisition_number,c.dealing_marchant,c.season from wo_non_ord_embl_booking_mst a join wo_non_ord_embl_booking_dtls b on a.BOOKING_NO = b.booking_no join sample_development_mst c on b.req_id =c.id where a.booking_no='$txt_booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.delivery_to,c.style_ref_no,c.team_leader,c.brand_id,c.requisition_number,c.dealing_marchant,c.season");
	foreach ($req_sql_data as $row) {
		$raq_data['dealing_marchant'][] = $deling_marcent_arr[$row[csf('dealing_marchant')]];
		$raq_data['season'][] = $season_arr[$row[csf('season')]];
		$raq_data['brand'][] = $brand_arr[$row[csf('brand_id')]];
		$raq_data['team_leader'][] = $team_leader_arr[$row[csf('team_leader')]];
		$raq_data['style_ref_no'][] = $row[csf('style_ref_no')];
		$raq_data['delivery_to'][] = $row[csf('delivery_to')];
		$raq_data['requisition_number'][] = $row[csf('requisition_number')];
	}
	
	$nameArray_item = sql_select("select c.emb_name from  wo_non_ord_embl_booking_dtls c  where  c.booking_no='$txt_booking_no' and  c.status_active =1 and c.is_deleted=0 group by c.emb_name  order by c.emb_name ");

	
			$sub_embl="";
			$e=1;
			foreach ($nameArray_item as $result_item) {
				
				if($e==1){
				$sub_embl =$emblishment_name_array[$result_item[csf('emb_name')]];
				$e++;
				}else{
					$sub_embl .=",".$emblishment_name_array[$result_item[csf('emb_name')]];
				}


			}
	$details_data = sql_select("SELECT b.req_no, b.sensitivity, b.req_booking_no,b.uom_id,a.currency_id,b.body_part_id,b.gmt_item_id, b.emb_type, b.emb_name, c.style_ref_no, d.sample_name_re , e.wo_booking_dtls_id, d.remarks_re as description,e.color_number_id, sum(e.cons) as cons, e.process_loss_percent, sum(e.requirment) as requirment,avg(e.rate) as rate, sum(e.amount) as amount from wo_non_ord_embl_booking_mst a join wo_non_ord_embl_booking_dtls b on a.BOOKING_NO = b.booking_no join sample_development_mst c on b.req_id =c.id join sample_development_fabric_acc d on d.id=b.embl_cost_dtls_id join wo_non_ord_embl_book_cons_dtls e on b.id=e.wo_booking_dtls_id where a.booking_no='$txt_booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by  b.req_no, b.sensitivity, b.req_booking_no,b.uom_id,a.currency_id,b.body_part_id,b.gmt_item_id, b.emb_type, b.emb_name, c.style_ref_no, d.sample_name_re , e.wo_booking_dtls_id, d.remarks_re,e.color_number_id, e.process_loss_percent");
	foreach ($details_data as $row) {
		$booking_arr[$row[csf('req_no')]]['sensitivity'] = $row[csf('sensitivity')];
		$booking_arr[$row[csf('req_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$booking_arr[$row[csf('req_no')]]['req_booking_no'] = $row[csf('req_booking_no')];
		$attribute = array('req_no', 'sensitivity', 'req_booking_no', 'uom_id', 'currency_id', 'body_part_id', 'gmt_item_id', 'emb_type', 'emb_name', 'style_ref_no', 'sample_name_re', 'wo_booking_dtls_id', 'description', 'color_number_id', 'cons', 'process_loss_percent', 'requirment', 'rate', 'amount');
		foreach ($attribute as $attr) {
			$booking_arr[$row[csf('req_no')]]['booking_dtls'][$row[csf('color_number_id')]][$attr] = $row[csf($attr)];
		}
		/* echo '<pre>';
		print_r($booking_arr);	die; */
	}
?>
	<html>
	<div style="width:1333px" align="center">
		<table width="1333px" cellpadding="0" cellspacing="0" style="border:0px solid black;">


			<table border="1" align="left" class="rpt_table container" cellpadding="0" width="100%" cellspacing="0" rules="all" style="padding-bottom: 10px;">
				<tr>
					<td width="150px" style="border-right:0;font-size:22px;" align="left"><? if ($report_type == 1) {
																				if ($link == 1) {
																			?>
								<img src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='40%' width='50%' />

							<?
																				} else {
							?>
								<img src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='40%' width='50%' />
							<?	}
																			} else { ?>
							<img src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='40%' width='50%' />
						<? }
						?>
					</td>
					<td style="font-size:22px;" width="200px" colspan="2" align="center"><b><?=$company_library[$cbo_company_name];?></b></td>
					<td style="font-size:22px;" colspan="2" align="center"><b>M&M DEPARTMENT</b></td>
					<td style="font-size:22px;" colspan="2" align="center"><b>Embellishment Work Order [Sample]
							<hr>(CODE: MMD/M&M/DMF-09)
						</b></td>
					<td style="font-size:22px;" align="center"><b>BOOKING DATE :<? echo change_date_format($booking_date); ?></b> </td>
				</tr>
			</table>
			<table border="1" align="left" class="rpt_table container" cellpadding="0" width="100%" cellspacing="0" rules="all" style="padding-bottom: 10px">
				<tr>
					<td  style="font-size:20px;" colspan="2" align="left"><b>Factory Name: <?= $company_library[$cbo_company_name]; ?></b> </td>
					<td  style="font-size:20px;" colspan="2" align="left"><b>SUB:<? echo implode(",", array_unique(explode(",", $sub_embl))); ?> Work Order</b></td>
					<td  style="font-size:20px;" colspan="2" align="left"><b>Booking No:</b></td>
					<td  style="font-size:20px;" colspan="2" align="left"><b><?= $txt_booking_no; ?></b> </td>
				</tr>
				<tr>
				<?
				$nameArray=sql_select("select a.id,a.group_id,a.company_name,b.id,b.address from lib_company a,lib_group b  where a.id=$cbo_company_name and a.group_id=b.id");
				   
				foreach( $nameArray as $row)
				{
				$group_address=$row[csf('address')];
				}
				?>
					<td style="font-size:20px;" width="100" colspan="8" align="left"><b>Head Office: </b>
					<?=$group_address;?>

					</td>

				</tr>
				<tr>
					<td style="font-size:20px;" width="100" colspan="8" align="left"><b>Factory:</b> <?
																				$nameArray = sql_select("select id,plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
																				foreach ($nameArray as $result) {
																				?>
							<? if ($result[csf('plot_no')] != '') echo $result[csf('plot_no')];
																					else echo ''; ?> &nbsp;
							<? echo $result[csf('level_no')]; ?> &nbsp;
							<? echo $result[csf('road_no')]; ?> &nbsp;
							<? echo $result[csf('block_no')]; ?> &nbsp;
							<? echo $result[csf('city')]; ?> &nbsp;
							<? echo $result[csf('zip_code')]; ?> &nbsp;
							<?php echo $result[csf('province')]; ?> &nbsp;
							<? echo $country_arr[$result[csf('country_id')]]; ?>
							<? echo $result[csf('email')]; ?> &nbsp;
						<? echo $result[csf('website')];
																					if ($result[csf('plot_no')] != '') {
																						$plot_no = $result[csf('plot_no')];
																					}
																					if ($result[csf('level_no')] != '') {
																						$level_no = $result[csf('level_no')];
																					}
																					if ($result[csf('road_no')] != '') {
																						$road_no = $result[csf('road_no')];
																					}
																					if ($result[csf('block_no')] != '') {
																						$block_no = $result[csf('block_no')];
																					}
																					if ($result[csf('city')] != '') {
																						$city = $result[csf('city')];
																					}
																					$company_address[$result[csf('id')]] = $plot_no . '&nbsp' . $level_no . '&nbsp' . $road_no . '&nbsp' . $block_no . '&nbsp' . $city;
																				}
						?></td>
				</tr>
				<tr>
					<td style="font-size:20px;" width="100" align="left"> <b>To :</b> </td>

					<td style="font-size:20px;" width="350" colspan="3" align="left"> <b><?
																	if ($pay_mode_id == 5 || $pay_mode_id == 3) {
																		echo $company_library[$supplier_id];
																	} else {
																		echo $supplier_name_arr[$supplier_id];
																	}
																	?></b></td>
					<td style="font-size:20px;" width="100" colspan="2" align="left"><b>Req No. :</b> </td>
					<td style="font-size:20px;" width="200" colspan="2" align="left"><b><? echo implode(",", $raq_data['requisition_number']) ?></b></td>

				</tr>
				<tr>
					<td style="font-size:20px;" width="100" align="left"><b> Attn. :</b></td>
					<td style="font-size:20px;" width="150" colspan="3" align="left"><b><? echo $attention;     ?></b></td>
					<td style="font-size:20px;" width="150" colspan="2" align="left"> <b>Buyer’s Name:</b></td>
					<td style="font-size:20px;" width="200" colspan="2" align="left"><b><? echo $buyer_name_arr[$buyer_id]; ?></b></td>

				</tr>
				<tr>
					<td style="font-size:20px;" width="100" align="left"><b> Team Leader :</b></td>
					<td style="font-size:20px;" width="350" colspan="3" align="left"><b><? echo implode(",", $raq_data['team_leader']) ?></b></td>
					<td style="font-size:20px;" width="100" colspan="2" align="left"><b>Brand :</b></td>
					<td style="font-size:20px;" width="200" colspan="2" align="left"><b><? echo implode(",", $raq_data['brand']) ?> </b></td>
				</tr>
				<tr>
					<td style="font-size:20px;" width="100" align="left"><b>Dealing Merchant :</b></td>
					<td style="font-size:20px;" width="350" colspan="3" align="left"><b><? echo implode(",", $raq_data['dealing_marchant']) ?> </b></td>
					<td style="font-size:20px;" width="100" colspan="2" align="left"><b>Style Ref :</b></td>
					<td style="font-size:20px;" width="200" colspan="2" align="left"><b><? echo implode(",", $raq_data['style_ref_no']) ?> </b></td>
				</tr>
				<tr>
					<td style="font-size:20px;" width="100" align="left"><b>Delivery Date :</b></td>
					<td style="font-size:20px;" width="350" colspan="3" align="left"><b><?= change_date_format($delivery_date, 'dd-mm-yyyy', '-'); ?></b></td>
					<td style="font-size:20px;" width="100" colspan="2" align="left"><b>Delivery Place :</b></td>
					<td style="font-size:20px;" width="200" colspan="2" align="left"><b><? echo implode(",", $raq_data['delivery_to']) ?> </b></td>
				</tr>
			</table>

			<table border="1" align="left" class="rpt_table" cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-top: 10px;font-size:20px;"><?
																																				$grand_amount_total = 0;
																																				foreach ($booking_arr as $req_no => $value) {
																																					$i = 1;
																																					$k = 1;
																																					$sub_requirment_total = 0;
																																					$sub_amount_total = 0;
																																					if ($i == 1) {
																																				?>
						<tr>
							<td colspan="14"><strong><? echo $size_color_sensitive[$value['sensitivity']] ?>, Style NO:<? echo $value['style_ref_no'] ?> , Req. No: <? echo $req_no ?>, Sample Booking No: <? echo $value['req_booking_no'] ?>
							</td>
						</tr>
						<tr>
							<td><strong>Sl No</strong></td>
							<td><strong>Emb. name</strong></td>
							<td><strong>Emb. Type</strong></td>
							<td><strong>Gmts Item</strong></td>
							<td><strong>Body Part</strong></td>
							<td><strong>Description</strong></td>
							<td><strong>Gmts Color</strong></td>
							<td><strong>UOM</strong></td>
							<td><strong>WO Qty(Pcs)</strong></td>
							<td><strong>Rate(Pcs)</strong></td>
							<td><strong>Amount</strong></td>
							<td><strong>Currency</strong></td>
							<td><strong>Remarks</strong></td>
						</tr>
					<?
																																					}
																																					foreach ($value['booking_dtls'] as $data) {
																																						$i++;
																																						if ($data['emb_name'] == 1) {
																																							$emb_type_name = $emblishment_print_type[$data['emb_type']];
																																						}
																																						if ($data['emb_name'] == 2) {
																																							$emb_type_name = $emblishment_embroy_type[$data['emb_type']];
																																						}
																																						if ($data['emb_name'] == 3) {
																																							$emb_type_name = $emblishment_wash_type[$data['emb_type']];
																																						}
																																						if ($data['emb_name'] == 4) {
																																							$emb_type_name = $emblishment_spwork_type[$data['emb_type']];
																																						}
																																						if ($data['emb_name'] == 5) {
																																							$emb_type_name = $emblishment_gmts_type[$data['emb_type']];
																																						}
					?>
						<tr>
							<td><? echo $k; ?></td>
							<td><? echo $emblishment_name_array[$data['emb_name']] ?></td>
							<td><? echo $emb_type_name; ?></td>
							<td><? echo $garments_item[$data['gmt_item_id']] ?></td>
							<td><? echo $body_part[$data['body_part_id']] ?></td>
							<td><? echo $data['description'] ?></td>
							<td><? echo $color_library[$data['color_number_id']] ?></td>
							<td><? echo $unit_of_measurement[$data['uom_id']] ?></td>
							<td><? echo $data['requirment'] ?></td>
							<td><? echo number_format($data['rate'], 2) ?></td>
							<td><? echo number_format($data['amount'], 2) ?></td>
							<td><? echo $currency[$data['currency_id']] ?></td>
							<td><? echo $data['description'] ?></td>
						</tr>
					<?
																																						$k++;
																																						$sub_requirment_total += $data['requirment'];
																																						$sub_amount_total += $data['amount'];
																																						$grand_requirment_total += $data['requirment'];
																																						$grand_amount_total += $data['amount'];
																																					}
					?>
					<tr>
						<td colspan="8" align="right"><strong>Item Total&nbsp;</strong></td>
						<td><strong><? echo number_format($sub_requirment_total, 2) ?></strong></td>
						<td><strong>Total Amount&nbsp;</strong></td>
						<td><strong><? echo number_format($sub_amount_total, 2) ?></strong></td>
						<td></td>
					</tr>
				<?

																																				}
				?>
				<tr>
					<td colspan="8" align="right"><strong>GR. Total&nbsp;</strong></td>
					<td><strong><? echo number_format($grand_requirment_total, 2) ?></strong></td>
					<td><strong>GR. Total&nbsp;</strong></td>
					<td><strong><? echo number_format($grand_amount_total, 2) ?></strong></td>
					<td></td>
				</tr>
				<?
				$mcurrency = "";
				$dcurrency = "";
				if ($currency_id == 1) {
					$mcurrency = 'Taka';
					$dcurrency = 'Paisa';
				}
				if ($currency_id == 2) {
					$mcurrency = 'USD';
					$dcurrency = 'CENTS';
				}
				if ($currency_id == 3) {
					$mcurrency = 'EURO';
					$dcurrency = 'CENTS';
				}
				?>
				<tr>
					<td colspan="4"><strong>Total Booking Amount (in word)</strong></td>
					<td colspan="10"><strong><? echo number_to_words(number_format($grand_amount_total, 2), $mcurrency, $dcurrency) ?></strong></td>
				</tr>
			</table>





			<?
			//echo get_spacial_instruction($txt_booking_no,"97%",118);
			$mst_id = $txt_booking_no;
			$width = "100%";
			$entry_form = 399;

			if ($entry_form != '') {
				$entry_form_con = " and entry_form=$entry_form";
			}
			//echo "select id, terms from  wo_booking_terms_condition where   booking_no='" . str_replace("'", "", $mst_id) . "' $entry_form_con   order by id";
			$data_array = sql_select("select id, terms from  wo_booking_terms_condition where   booking_no='" . str_replace("'", "", $mst_id) . "' $entry_form_con   order by id asc");
			$tot_row = count($data_array) / 2;
			//echo $tot_row;
			$k = 1;
			foreach ($data_array as $row) {
				if ($k <= $tot_row) {
					$term_bookingArr[$row[csf('id')]]['terms'] = $row[csf('terms')];
				} else {
					$other_term_bookingArr[$row[csf('id')]]['terms'] = $row[csf('terms')];
				}
				$k++;
			}

			if (count($data_array) > 0) {
			?>
				<br>
				<table style="font-size:20px;" align="left" width="<?= $width; ?>" align="center" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td valign="top">

							<table width="650" class="rpt_table" align="center" border="1" cellpadding="0" cellspacing="0" rules="all">
								<thead>
									<tr style="border:1px solid black;">
										<th width="3%" style="border:1px solid black;">Sl</th>
										<th width="45%" style="border:1px solid black;">Special Instruction</th>
									</tr>
								</thead>
								<tbody>
									<?

									//print_r($term_bookingArr);
									$sl = 1;
									foreach ($term_bookingArr as $term => $row) {
									?>
										<tr id="settr_1" align="" style="border:1px solid black;">
											<td align="center" style="border:1px solid black;text-align:center"><?= $sl; ?></td>
											<td style="border:1px solid black; font-weight:bold"><?= $row['terms']; ?></td>
										<?
										$sl++;
									}

										?>
								</tbody>
							</table>
						</td>
						<!--1st part end-->
						<?
						$sl2 = $sl;
						if (count($other_term_bookingArr) > 0) {
						?>
							<td valign="top">
								<table width="650" class="rpt_table" align="center" border="1" cellpadding="0" cellspacing="0" rules="all">
									<thead>
										<tr style="border:1px solid black;">
											<th width="3%" style="border:1px solid black;">Sl</th>
											<th width="45%" style="border:1px solid black;">Special Instruction</th>
										</tr>
									</thead>
									<tbody>
										<?
										foreach ($other_term_bookingArr as $term2 => $row2) {
										?>
											<tr id="settr_2" align="" style="border:1px solid black;">
												<td align="center" style="border:1px solid black; text-align:center"><?= $sl2; ?></td>
												<td style="border:1px solid black; font-weight:bold"><?= $row2['terms']; ?></td>
											<?
											$sl2++;
										}

											?>
									</tbody>
								</table>

							</td>
						<?
						}
						?>
					</tr>
				</table>
			<?
			}
			?>


		</table>

		</table>
		<?
		// image show here  -------------------------------------------
		$sql_img = "select id, master_tble_id, image_location from common_photo_library where form_name='print_booking_multijob' and master_tble_id ='$txt_booking_no' ";
		$data_array = sql_select($sql_img);
		?>
		<div align="left" style="margin:5px 2px;float:left;width:100%">
			<? foreach ($data_array as $inf) { ?>
				<img src='../../<? echo $inf[csf("image_location")]; ?>' height='70' width='80' />
			<? } ?>
		</div>
	</div>
	<!--class="footer_signature"-->
	<div style="margin-top:-5px;"><? echo signature_table(133, $cbo_company_name, "1300px", $cbo_template_id); ?></div>
	<br>
	<div id="page_break_div"></div>
	<div><? echo "****" . custom_file_name($txt_booking_no, $style_sting, $job_no); ?></div>
	<?
	if ($link == 1) {
	?>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<?
	} else {
	?>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<?
	}
	?>
	<script>
		fnc_generate_Barcode('<? echo $varcode_booking_no; ?>', 'barcode_img_id');
	</script>

	</html>
<?
	exit();
}
?>
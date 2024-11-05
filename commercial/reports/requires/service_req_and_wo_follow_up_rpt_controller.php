<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

if ($action == "load_drop_down_location") {
	echo create_drop_down("cbo_location", 140, "SELECT id,location_name from lib_location where company_id=$data", "id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/service_req_and_wo_follow_up_rpt_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_store', 'store_td' )", 0, "");
}

if ($action == "load_drop_down_store") {
	$data = explode("_", $data);
	if ($data[1] != 0) $loc_cond = " and location_id=$data[1]";
	else $loc_cond = '';
	echo create_drop_down("cbo_store", 120, "SELECT id, store_name from lib_store_location where a.company_id='$data[0]' $loc_cond and a.status_active=1 order by store_name", "id,store_name", 1, "-- Select Store --", $selected, "", 0);
}

if ($action == "report_generate") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$cbo_company_name = str_replace("'", "", $cbo_company_name);
	$cbo_location = str_replace("'", "", $cbo_location);
	$txt_req_no = str_replace("'", "", $txt_req_no);
	$cbo_job_year = str_replace("'", "", $cbo_job_year);
	$txt_wo_no = str_replace("'", "", $txt_wo_no);
	$txt_date_from = str_replace("'", "", $txt_date_from);
	$txt_date_to = str_replace("'", "", $txt_date_to);
	$cbo_store = str_replace("'", "", $cbo_store);
	$value_with = str_replace("'", "", $cbo_value_with);
	$cbo_date_type = str_replace("'", "", $cbo_date_type);

	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$user_arr = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
	$location_arr = return_library_array("select id,location_name from  lib_location", "id", "location_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');

	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$item_group_arr = return_library_array("select id, item_name from lib_item_group", 'id', 'item_name');
	$division_arr = return_library_array("select id, division_name from lib_division", 'id', 'division_name');
	$department_arr = return_library_array("select id, department_name from lib_department", 'id', 'department_name');
	$section_arr = return_library_array("select id, section_name from lib_section", 'id', 'section_name');

	$str_cond = "";

	if($cbo_location > 0) $str_cond .= " and a.location_id=$cbo_location ";
	if($cbo_date_type==1){
		if($cbo_store > 0) $str_cond .= " and a.store_name=$cbo_store ";
		if($txt_req_no != "") $str_cond .= " and a.requ_prefix_num ='$txt_req_no' ";
		if($txt_wo_no != "") $str_cond .= " and d.wo_number_prefix_num like '%$txt_wo_no%' ";

		if($cbo_company_name > 0) $str_cond .= " and a.company_id=$cbo_company_name ";
		if($cbo_job_year > 0) $str_cond .= " and to_char(a.requisition_date,'YYYY')=$cbo_job_year ";
		if($txt_date_from != "" && $txt_date_to != "") $str_cond .= " and a.requisition_date between '$txt_date_from' and '$txt_date_to' ";
	}
	else{
		if($cbo_company_name > 0) $str_cond .= " and a.company_name=$cbo_company_name ";
		if($txt_wo_no != "") $str_cond .= " and a.wo_number_prefix_num like '%$txt_wo_no%' ";
		if($cbo_job_year > 0) $str_cond .= " and to_char(a.WO_DATE,'YYYY')=$cbo_job_year ";
		if($txt_date_from != "" && $txt_date_to != "") $str_cond .= " and a.WO_DATE between '$txt_date_from' and '$txt_date_to' ";
	}
	$sql_req_wo = "SELECT a.id as req_id, a.company_id, a.is_approved, a.requ_no, a.requisition_date, a.department_id, a.section_id, a.store_name, a.pay_mode, a.cbo_currency, a.delivery_date, a.inserted_by as REQUIRED_FOR,
	b.service_for,b.service_details,b.service_uom, b.quantity as req_quantity, b.tag_materials, b.remarks, 
	d.id as wo_id, d.wo_number, d.supplier_id, d.wo_date, d.is_approved as WO_IS_APPROVED,
	c.uom,c.supplier_order_quantity as wo_qnty, c.rate as WO_RATE, c.amount as wo_value, c.remarks as wo_remarks
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
	left join wo_non_order_info_dtls c on b.id=c.requisition_dtls_id and c.status_active=1
	LEFT join wo_non_order_info_mst d on d.id=c.mst_id and d.entry_form=484 and d.status_active=1 and d.company_name=$cbo_company_name 
	where a.id=b.mst_id and a.entry_form=526 and a.status_active=1 and b.status_active=1 $str_cond
	order by a.id";
	// echo $sql_req_wo;die;
	$req_wo_data=sql_select($sql_req_wo);
	$sql_wo = "SELECT a.id as wo_id, a.wo_number, a.supplier_id, a.wo_date, a.is_approved as WO_IS_APPROVED, b.uom,b.supplier_order_quantity as wo_qnty, b.rate as WO_RATE, b.amount as wo_value, b.remarks,b.service_details, a.WO_BASIS_ID 
	from wo_non_order_info_mst a ,wo_non_order_info_dtls b 
	where  a.id=b.mst_id and a.entry_form=484 and a.status_active=1 and b.status_active=1 and a.WO_BASIS_ID =2 $str_cond order by a.id";
	// echo $sql_wo;die;
	$wo_data=sql_select($sql_wo);
	
	$print_report_format = return_field_value("format_id", "lib_report_template", "template_name =" . $cbo_company_name . " and module_id=19 and report_id=206 and is_deleted=0 and status_active=1");
	$format_ids = explode(",", $print_report_format);
	ob_start();
	?>
	<style>
		td{word-break: break-all;}
	</style>
	<div style="width:2560px">
		<div>
			<table cellspacing="0" cellpadding="0" align="center" rules="all" width="250" border="0" class="rpt_table" style="float: left;">
				<caption><b>Summary</b></caption>
				<tr>
					<td width="80" bgcolor="#E9F3FF"><strong> Total Req. No Without WO</strong></td>
					<td width="50" id="totalReqWOutWO" bgcolor="#E9F3FF" align="center"><strong></strong></td>
				</tr>
				<tr>
					<td width="80" bgcolor="#FFFFFF"><strong>Total Req. No with WO</strong></td>
					<td width="50" id="totalReqWWO" bgcolor="#FFFFFF" align="center"><strong></strong></td>
				</tr>
				<tr>
					<td width="80" bgcolor="#FFFFFF"><strong>Total Independent with WO</strong></td>
					<td width="50" id="totalIndependent" bgcolor="#FFFFFF" align="center"><strong></strong></td>
				</tr>
			</table>
			<table width="2240" cellpadding="0" cellspacing="0" id="caption" align="left">
				<tr>
					<td align="center" width="100%" class="form_caption" colspan="26"><strong style="font-size:18px">Company Name:<? echo " " . $company_arr[str_replace("'", "", $cbo_company_name)]; ?></strong></td>
				</tr>
				<tr>
					<td align="center" width="100%" class="form_caption" colspan="26"><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
				</tr>
				<tr>
					<td align="center" width="100%" class="form_caption" colspan="26"><strong style="font-size:18px">From : <? echo $txt_date_from; ?> To : <? echo $txt_date_to; ?></strong></td>
				</tr>
			</table>
		</div>
		<br /> <br />
		<div style="width:1730px; float:left" align="left">
			<table width="1730" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
				<thead>
					<tr>
						<th colspan="17" title="Without WO">Requisition Details</th>
					</tr>				
					<tr>
						<th width="30">Sl</th>
						<th width="120">Req. No</th>
						<th width="80">Req. Date</th>
						<th width="120">Store Name</th>
						<th width="80">Pay Mode</th>
						<th width="80">Currency</th>
						<th width="80">Delivery Date</th>
						<th width="80">Service Type</th>
						<th width="140">Service Details</th>
						<th width="80">Service UOM</th>
						<th width="80">Reqsn Quantity</th>
						<th width="120">Req. By</th>
						<th width="120">For Department</th>
						<th width="100">For Section</th>
						<th width="80">Tag Materials</th>
						<th >Remarks</th>
						<th width="80">Approval Status</th>
					</tr>
				</thead>
			</table>
			<div style="width:1750px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
				<table width="1730" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
					<tbody>
						<?
						$i = 1;
						foreach ($req_wo_data as $row) {
							if ($row["WO_ID"] == '' || $row["WO_ID"] == 0) {
								if ($i % 2 == 0){$bgcolor = "#E9F3FF";}else{$bgcolor = "#FFFFFF";}
								//$all_req[$row["WO_ID"]]=$row["WO_ID"];
								$all_req[$row["REQ_ID"]]=$row["REQ_ID"];
								?>
								<tr bgcolor="<?=$bgcolor; ?>" onclick="change_color('trreq_<?=$i; ?>','<?=$bgcolor; ?>')" id="trreq_<? echo $i; ?>">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="120" align="center"><a href='##' onclick="fnc_req_report('<?=$row['COMPANY_ID']; ?>','<?=$row['REQ_ID']; ?>','Service Requisition','service_requisition_print')"><? echo $row['REQU_NO']; ?></a> <? echo $variable; ?></td>
									<td width="80" align="center">&nbsp;<? echo change_date_format($row["REQUISITION_DATE"]); ?></td>
									<td width="120"><? echo $store_arr[$row["STORE_NAME"]]; ?></td>
									<td width="80" align="center"><? echo $pay_mode[$row["PAY_MODE"]]; ?></td>
									<td width="80"><? echo $currency[$row["CBO_CURRENCY"]]; ?></td>
									<td width="80" align="center">&nbsp;<? echo change_date_format($row["DELIVERY_DATE"]); ?></td>
									<td width="80" align="center"><? echo $service_for_arr[$row["SERVICE_FOR"]]; ?></td>
									<td width="140"><? echo $row["SERVICE_DETAILS"]; ?></td>
									<td width="80" align="center"><? echo $service_uom_arr[$row["SERVICE_UOM"]]; ?></td>
									<td width="80" align="right"><? echo number_format($row["REQ_QUANTITY"], 2); ?></td>
									<td width="120"><? echo $user_arr[$row["REQUIRED_FOR"]]; ?></td>
									<td width="120"><? echo $department_arr[$row["DEPARTMENT_ID"]]; ?></td>
									<td width="100"><? echo $section_arr[$row["SECTION_ID"]]; ?></td>
									<td width="80" align="center"><a href="##" onclick="fnc_matrial_list('<?=$row['TAG_MATERIALS'];?>')">View</a></td>
									<td ><? echo $row["REMARKS"]; ?></td>
									<td width="80" align="center"><? echo $is_approved[$row["IS_APPROVED"]]; ?></td>
								</tr>
								<?
								$i++;
								$tot_req_qnty+=$row["REQ_QUANTITY"];
							}
						}

						if (count($all_req)>0) {
							$all_reqs_number = count($all_req);
						} else {
							$all_reqs_number = 0;
						}
						?>
					</tbody>

				</table>
			</div>
			<table width="1730" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_footer" align="left">
				<tfoot>
					<tr>
						<th width="30"></th>
						<th width="120"></th>
						<th width="80"></th>
						<th width="120"></th>
						<th width="80"></th>
						<th width="80"></th>
						<th width="80"></th>
						<th width="80"></th>
						<th width="140"></th>
						<th width="80"></th>
						<th width="80"><? echo number_format($tot_req_qnty, 2); ?></th>
						<th width="120"></th>
						<th width="120"></th>
						<th width="100"></th>
						<th width="80"></th>
						<th ></th>
						<th width="80"></th>
					</tr>
				</tfoot>
			</table>
		</div>
		<br /><br />
		<div style="width:2530px; float:left" align="left">
			<table width="2530" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
				<thead>
					<tr>
						<th colspan="16" title="With WO">Requisition Details</th>
						<th colspan="10" title="With WO">Work Order Details</th>
					</tr>				
					<tr>
					<th width="30">Sl</th>
						<th width="120">Req. No</th>
						<th width="80">Req. Date</th>
						<th width="120">Store Name</th>
						<th width="80">Pay Mode</th>
						<th width="80">Currency</th>
						<th width="80">Delivery Date</th>
						<th width="80">Service Type</th>
						<th width="140">Service Details</th>
						<th width="80">Service UOM</th>
						<th width="80">Reqsn Quantity</th>
						<th width="120">Req. By</th>
						<th width="120">For Department</th>
						<th width="100">For Section</th>
						<th width="80">Tag Materials</th>
						<th width="100">Remarks</th>
						<th width="120">WO No</th>
						<th width="80">WO Date</th>
						<th width="120">Supplier</th>
						<th width="80">UOM</th>
						<th width="80">WO Qnty</th>
						<th width="80">WO Rate</th>
						<th width="80">WO Value</th>
						<th width="80">WO Balance</th>
						<th width="80">Approval Status</th>
						<th >Remarks</th>
					</tr>
				</thead>
			</table>
			<div style="width:2550px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body2" align="left">
				<table width="2530" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2" align="left">
					<tbody>
						<?
						$i = 1;
						$tot_req_qnty=0;
						foreach ($req_wo_data as $row) {
							if ($row["WO_ID"] != '' && $row["WO_ID"] != 0) {
								if ($i % 2 == 0){$bgcolor = "#E9F3FF";}else{$bgcolor = "#FFFFFF";}
								//$all_req_wo[$row["WO_ID"]]=$row["WO_ID"];
								$all_req_wo[$row["REQ_ID"]]=$row["REQ_ID"];
								if ($format_ids[0] == 86) $type = 1; // Print
								else if ($format_ids[0] == 732) $type = 2; // PO Print
								else if ($format_ids[0] == 84) $type = 3; // Print 2
								else  $type = 0;

								?>
								<tr bgcolor="<?=$bgcolor; ?>" onclick="change_color('trwo_<?=$i; ?>','<?=$bgcolor; ?>')" id="trwo_<? echo $i; ?>">
								<td width="30" align="center"><? echo $i; ?></td>
									<td width="120" align="center"><a href='##' onclick="fnc_req_report('<?=$row['COMPANY_ID']; ?>','<?=$row['REQ_ID']; ?>','Service Requisition','service_requisition_print')"><? echo $row['REQU_NO']; ?></a> <? echo $variable; ?></td>
									<td width="80" align="center">&nbsp;<? echo change_date_format($row["REQUISITION_DATE"]); ?></td>
									<td width="120"><? echo $store_arr[$row["STORE_NAME"]]; ?></td>
									<td width="80" align="center"><? echo $pay_mode[$row["PAY_MODE"]]; ?></td>
									<td width="80" align="center"><? echo $currency[$row["CBO_CURRENCY"]]; ?></td>
									<td width="80" align="center">&nbsp;<? echo change_date_format($row["DELIVERY_DATE"]); ?></td>
									<td width="80" align="center"><? echo $service_for_arr[$row["SERVICE_FOR"]]; ?></td>
									<td width="140"><? echo $row["SERVICE_DETAILS"]; ?></td>
									<td width="80" align="center"><? echo $service_uom_arr[$row["SERVICE_UOM"]]; ?></td>
									<td width="80" align="right"><? echo number_format($row["REQ_QUANTITY"], 2); ?></td>
									<td width="120"><? echo $user_arr[$row["REQUIRED_FOR"]]; ?></td>
									<td width="120"><? echo $department_arr[$row["DEPARTMENT_ID"]]; ?></td>
									<td width="100"><? echo $section_arr[$row["SECTION_ID"]]; ?></td>
									<td width="80" align="center"><a href="##" onclick="fnc_matrial_list('<?=$row['TAG_MATERIALS'];?>')">View</a></td>
									<td width="100"><? echo $row["REMARKS"]; ?></td>
									<td width="120" align="center"><a href='##' onclick="fnc_wo_report(<?=$type;?>,'<?=$row['COMPANY_ID']; ?>','<?=$row['WO_ID']; ?>','Service Work Order')"><? echo $row['WO_NUMBER']; ?></a></td>
									<td width="80"><? echo change_date_format($row["WO_DATE"]); ?></td>
									<td width="120"><? echo $supplier_arr[$row["SUPPLIER_ID"]]; ?></td>									<td width="80" align="center"><? echo $service_uom_arr[$row["UOM"]]; ?></td>
									<td width="80" align="right"><? echo number_format($row["WO_QNTY"],2); ?></td>
									<td width="80" align="right"><? echo number_format($row["WO_RATE"],2); ?></td>
									<td width="80" align="right"><? echo number_format($row["WO_VALUE"],2); ?></td>
									<td width="80" align="right"><? $balance=$row["REQ_QUANTITY"]-$row["WO_QNTY"];  echo number_format($balance,2); ?></td>
									<td width="80" align="center"><? echo $is_approved[$row["WO_IS_APPROVED"]]; ?></td>
									<td ><? echo $row["REMARKS"]; ?></td>
								</tr>
								<?
								$i++;
								$tot_req_qnty+=$row["REQ_QUANTITY"];
								$tot_wo_qnty+=$row["WO_QNTY"];
								$tot_wo_value+=$row["WO_VALUE"];
								$tot_req_wo_bal+=$balance;
							}
						}

						if (count($all_req_wo)>0) {
							$all_reqs_with_wo = count($all_req_wo);
						} else {
							$all_reqs_with_wo = 0;
						}
						?>
					</tbody>

				</table>
			</div>
			<table width="2530" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_footer_2" align="left">
				<tfoot>
					<tr>
						<th width="30"></th>
						<th width="120"></th>
						<th width="80"></th>
						<th width="120"></th>
						<th width="80"></th>
						<th width="80"></th>
						<th width="80"></th>
						<th width="80"></th>
						<th width="140"></th>
						<th width="80"></th>
						<th width="80"><? echo number_format($tot_req_qnty, 2); ?></th>
						<th width="120"></th>
						<th width="120"></th>
						<th width="100"></th>
						<th width="80"></th>
						<th width="100"></th>
						<th width="120"></th>
						<th width="80"></th>
						<th width="120"></th>
						<th width="80"></th>
						<th width="80"><? echo number_format($tot_wo_qnty,2); ?></th>
						<th width="80"></th>
						<th width="80"><? echo number_format($tot_wo_value,2); ?></th>
						<th width="80"><? echo number_format($tot_req_wo_bal,2); ?></th>
						<th width="80"></th>
						<th ></th>
					</tr>
				</tfoot>
			</table>
		</div>
		<br>
		<div style="width:910px; float:left" align="left">
			<table width="910" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
				<thead>
					<tr>
						<th colspan="10" title="Independent Details">Independent Details</th>
					</tr>				
					<tr>
						<th width="120">WO No</th>
						<th width="80">WO Date</th>
						<th width="120">Supplier</th>
						<th width="90">Service Details</th>
						<th width="80">UOM</th>
						<th width="80">WO Qnty</th>
						<th width="80">WO Rate</th>
						<th width="80">WO Value</th>
						<th width="80">Approval Status</th>
						<th >Remarks</th>
					</tr>
				</thead>
			</table>
			<div style="width:910px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body2" align="left">
				<table width="910" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2" align="left">
					<tbody>
						<?
						$i = 1;
						$tot_ind_req_qnty=0;
						foreach ($wo_data as $row) {
								if ($i % 2 == 0){$bgcolor = "#E9F3FF";}else{$bgcolor = "#FFFFFF";}
								$all_ind_req_wo[$row["WO_ID"]]=$row["WO_ID"];
								if ($format_ids[0] == 86) $type = 1; // Print
								else if ($format_ids[0] == 732) $type = 2; // PO Print
								else if ($format_ids[0] == 84) $type = 3; // Print 2
								else  $type = 0;

								?>
								<tr bgcolor="<?=$bgcolor; ?>" onclick="change_color('trwo_<?=$i; ?>','<?=$bgcolor; ?>')" id="trwo_<? echo $i; ?>">
									<td width="120" align="center"><a href='##' onclick="fnc_wo_report(<?=$type;?>,'<?=$row['COMPANY_ID']; ?>','<?=$row['WO_ID']; ?>','Service Work Order')"><? echo $row['WO_NUMBER']; ?></a></td>
									<td width="80"><? echo change_date_format($row["WO_DATE"]); ?></td>
									<td width="120"><? echo $supplier_arr[$row["SUPPLIER_ID"]]; ?></td>	
									<td width="90" align="center"><? echo $row["SERVICE_DETAILS"]; ?></td>	
									<td width="80" align="center"><? echo $service_uom_arr[$row["UOM"]]; ?></td>
									<td width="80" align="right"><? echo number_format($row["WO_QNTY"],2); ?></td>
									<td width="80" align="right"><? echo number_format($row["WO_RATE"],2); ?></td>
									<td width="80" align="right"><? echo number_format($row["WO_VALUE"],2); ?></td>
									<td width="80" align="center"><? echo $is_approved[$row["WO_IS_APPROVED"]]; ?></td>
									<td ><? echo $row["REMARKS"]; ?></td>
								</tr>
								<?
								$i++;
								$tot_ind_req_qnty+=$row["REQ_QUANTITY"];
								$tot_ind_wo_qnty+=$row["WO_QNTY"];
								$tot_ind_wo_value+=$row["WO_VALUE"];
						}

						if (count($all_ind_req_wo)>0) {
							$all_ind_reqs_with_wo = count($all_ind_req_wo);
						} else {
							$all_ind_reqs_with_wo = 0;
						}
						?>
					</tbody>

				</table>
			</div>
			<table width="910" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_footer_3" align="left">
				<tfoot>
					<tr>
						<th width="120"></th>
						<th width="80"></th>
						<th width="120"></th>
						<th width="90"></th>
						<th width="80"></th>
						<th width="80"><? echo number_format($tot_ind_wo_qnty,2); ?></th>
						<th width="80"></th>
						<th width="80"><? echo number_format($tot_ind_wo_value,2); ?></th>
						<th width="80"></th>
						<th ></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<br />
	<script type="text/javascript">
		var all_reqs_number = '<?php echo $all_reqs_number; ?>';
		document.getElementById("totalReqWOutWO").textContent = all_reqs_number;
		var all_reqs_with_wo = '<?php echo $all_reqs_with_wo; ?>';
		document.getElementById("totalReqWWO").textContent = all_reqs_with_wo;
		var all_ind_reqs_with_wo = '<?php echo $all_ind_reqs_with_wo; ?>';
		document.getElementById("totalIndependent").textContent = all_ind_reqs_with_wo;
	</script>
	<?

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
	echo "$total_data####$filename";
	exit();
}

if ($action=="tag_materials_popup")
{
    echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>

    </head>
    <body>
        <div align="center" style="width:100%" >
            <fieldset style="width:900px">
            <?

            $arr=array (1=>$item_category,5=>$unit_of_measurement,9=>$row_status);

            $sql="SELECT a.id, a.item_account, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name, a.order_uom 
            from product_details_master a, lib_item_group b 
            where a.item_group_id=b.id and a.is_deleted=0 and a.id in ($tagMaterials) and a.item_category_id in (89,51,52,49,90,99,55,21,67,93,59,48,64,15,57,66,45,47,107,54,70,50,37,69,68,18,46,60,62,9,16,17,38,92,65,10,33,44,34,35,63,19,22,61,97,36,56,8,41,40,91,43,53,20,94,32,58,39) and a.entry_form<>24 ";
            // echo $sql;
            echo  create_list_view ( "list_view","Item Account,Item Category,Item Description,Item Size,Item Group,Order UOM,Stock,Re-Order Level,Product ID,Status", "120,100,140,80,100,80,80,80,80,50","950","250",0, $sql, "", "", "", '', "0,item_category_id,0,0,0,order_uom,0,0,0,status_active", $arr , "item_account,item_category_id,item_description,item_size,item_name,order_uom,current_stock,re_order_label,id,status_active", "", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,1,1,0,0','' );
            ?>
            </fieldset>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}
disconnect($con);
?>
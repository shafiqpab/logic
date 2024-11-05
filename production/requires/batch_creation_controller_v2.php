<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];


if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=56 and is_deleted=0 and status_active=1");
	echo trim($print_report_format);
	exit();

}

if ($action == "load_drop_machine") {

	if($db_type==2)
	{
		echo create_drop_down( "cbo_machine_name", 172, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where category_id=2 and floor_id=$data and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no","id,machine_name", 1,"-- Select Machine --", $selected, "","" );
	}
	else if($db_type==0)
	{
		echo create_drop_down( "cbo_machine_name", 172, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=2 and floor_id=$data and status_active=1 and is_deleted=0 and is_locked=0 order by  seq_no","id,machine_name", 1, "-- Select Machine --", $selected, "","" );
	}
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 172, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=3 and company_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/batch_creation_controller_v2',this.value, 'load_drop_machine', 'td_dyeing_machine' );",0 );

	exit();
}

if ($action == "load_fabric_source_from_variable_settings") {
	$sql_result = sql_select("select dyeing_fin_bill from variable_settings_production where company_name = $data and variable_list = 44 and is_deleted = 0 and status_active = 1");
	if ($sql_result) {
		foreach ($sql_result as $result) {
			echo "$('#fabric_source').val(" . $result[csf("dyeing_fin_bill")] . ");\n";
		}
	} else {
		echo "$('#fabric_source').val(2);\n";
	}
	exit();
}

if ($action == "fabricBooking_popup") {
	echo load_html_head_contents("WO Info", "../../", 1, 1, '', '', '', 1);
	extract($_REQUEST);
	?>
	<script>
		$(document).on("click", ".view_order", function (e) {
			e.preventDefault();
			var job_no = $(this).attr("data-job");
			var company_id = $(this).attr("data-company");
			dhtmlmodal.open('EmailBox', 'iframe', 'batch_creation_controller_v2.php?company_id=' + company_id + '&job_no=' + job_no + '&action=order_popup', 'Order Popup', 'width=640px,height=350px,center=1,resize=1,scrolling=0', '')
		});

		$(document).on("click", "#order_tbl table tbody tr td:not(:last-child)", function (e) {
			var thisAttrValues = $(this).parent("tr").attr("data-values");
			var dataValues = thisAttrValues.split("##");
			$('#hidden_booking_id').val(dataValues[0]);
			$('#hidden_booking_no').val(dataValues[1]);
			$('#hidden_color_id').val(dataValues[2]);
			$('#hidden_color').val(dataValues[3]);
			$('#hidden_job_no').val(dataValues[4]);
			$('#booking_without_order').val(dataValues[6]);
			$('#hidden_sales_booking_no').val(dataValues[5]);
			$('#hidden_search_type').val(dataValues[7]);
			$('#hidden_within_group').val(dataValues[8]);
			$('#hidden_sales_id').val(dataValues[9]);
			$('#hidden_sales_remarks').val(dataValues[10]);
			$('#hidden_is_sales').val(dataValues[11]);
			$('#hidden_po_id').val(dataValues[12]);
			parent.emailwindow.hide();
		});

		function js_set_value(booking_id, booking_no, color_id, color, job_no, sales_booking_no, type, search_by,color_type, entry_form,po_ids){

			$('#hidden_booking_id').val(booking_id);
			$('#hidden_booking_no').val(booking_no);
			$('#hidden_color_id').val(color_id);
			$('#hidden_color').val(color);
			$('#hidden_job_no').val(job_no);
			$('#hidden_po_id').val(po_ids);
			$('#booking_without_order').val(type);
			$('#hidden_sales_booking_no').val(sales_booking_no);
			$('#hidden_search_type').val(search_by);
			$('#hidden_color_type').val(color_type);
			$('#hidden_entry_form').val(entry_form);
			parent.emailwindow.hide();
		}
		function field_visible(thisValue) {
			$("#chkIsSales").prop("checked", false);
			if (thisValue == 1) {
				$("#is_sales_booking").css("display", "block");
			} else {
				$("#is_sales_booking").css("display", "none");
			}
		}
	</script>
</head>

<body>
	<div align="center" style="width:962px;">
		<form name="searchwofrm" id="searchwofrm" autocomplete=off>
			<fieldset style="width:100%;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="840" class="rpt_table" border="1" rules="all">
					<thead>
						<th>Booking Year</th>
						<th>Buyer</th>
						<th>Booking Type</th>
						<th>Search By</th>
						<th id="search_by_td_up"><?php echo ($batch_against == 7) ? "Enter Sales Order No" : "Enter Booking No"; ?></th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton"/>
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
							<input type="hidden" name="hidden_color_id" id="hidden_color_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_color" id="hidden_color" class="text_boxes" value="">
							<input type="hidden" name="hidden_job_no" id="hidden_job_no" class="text_boxes" value="">
                            <input type="hidden" name="hidden_po_id" id="hidden_po_id" class="text_boxes" value="">
							<input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes" value="">
							<input type="hidden" name="hidden_sales_booking_no" id="hidden_sales_booking_no" class="text_boxes" value="">
							<input type="hidden" name="hidden_search_type" id="hidden_search_type" class="text_boxes" value="">
							<input type="hidden" name="hidden_within_group" id="hidden_within_group" class="text_boxes" value="">
							<input type="hidden" name="hidden_sales_id" id="hidden_sales_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_color_type" id="hidden_color_type" class="text_boxes" value="">
							<input type="hidden" name="hidden_entry_form" id="hidden_entry_form" class="text_boxes" value="">
							<input type="hidden" name="hidden_sales_remarks" id="hidden_sales_remarks" class="text_boxes" value="">
							<input type="hidden" name="hidden_is_sales" id="hidden_is_sales" class="text_boxes" value="">
						</th>
					</thead>
					<tr>
						<td align="center">
							<?
							echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
						</td>
						<td align="center">
							<?
							echo create_drop_down("cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "", $data[0]);
							?>
						</td>
						<td align="center">
							<?
							if ($batch_against == 3) {
								$disabled = 0;
							} else {
								$disabled = 1;
							}


							$booking_type = array(1 => "With Order", 2 => "Without Order");
							echo create_drop_down("cbo_booking_type", 140, $booking_type, "", 0, "-- All --", '', '', $disabled);
							?>
						</td>
						<td align="center">
							<?
							if ($batch_against == 7) {
								$disabled = 1;
								$selected = 7;
							} else {
								$disabled = 0;
								$selected = 1;
							}
							$search_by_arr = array(1 => "Booking No", 2 => "Buyer Order", 3 => "Job No", 4 => "Booking Date", 5 => "Internal Ref.", 6 => "File No", 7 => "Sales Order");
							$dd = "change_search_event(this.value, '0*0*0*3*0*0*0', '0*0*0*3*0*0*0', '../../');field_visible(this.value);";
							echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", $selected, $dd, $disabled);
							?>
						</td>
						<td align="center">
							<div id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</div>
							<div id="is_sales_booking"><input type="checkbox" name="chkIsSales" id="chkIsSales"/> <label
								for="chkIsSales">For sales order </label></div>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo $batch_against; ?>'+'_'+document.getElementById('cbo_booking_type').value+'_'+document.getElementById('chkIsSales').checked+'_'+document.getElementById('cbo_year').value, 'create_booking_search_list_view', 'search_div', 'batch_creation_controller_v2', 'setFilterGrid(\'tbl_list_search\',-1);')"
								style="width:100px;"/>
							</td>
						</tr>
					</table>
					<div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_booking_search_list_view") {

	$data = explode("_", $data);

	$search_string 		= "%" . trim($data[0]) . "%";
	$search_by 			= $data[1];
	$company_id 		= $data[2];
	$buyer_id 			= $data[3];
	$batch_against 		= $data[4];
	$booking_type 		= $data[5];
	$is_sales_booking 	= $data[6];
	$booking_year 		= $data[7];

	if ($buyer_id == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] > 0) $buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")"; else $buyer_id_cond = "";
			if ($_SESSION['logic_erp']["buyer_id"] > 0) $buyer_id_samp_cond = " and s.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")"; else $buyer_id_samp_cond = "";
		} else {
			$buyer_id_cond = "";
			$buyer_id_samp_cond = "";
		}
	} else {
		$buyer_id_cond 		= " and a.buyer_id=$buyer_id";
		$buyer_id_samp_cond = " and s.buyer_id=$buyer_id";
	}

	if (trim($data[0]) != "") {
		if ($search_by == 1)
		{
			$search_field_cond = "and a.booking_no_prefix_num =". trim($data[0]) ."";
		}
		else if ($search_by == 2)
		{
			$search_field_cond = "and c.po_number like '$search_string'";
		}
		else if ($search_by == 3)
		{
			$search_field_cond = "and d.job_no_prefix_num = ". trim($data[0]) ."";
		}
		else if ($search_by == 5)
		{
			$search_field_cond = "and c.grouping like '$search_string'";
		}
		else if ($search_by == 6)
		{
			$search_field_cond = "and c.file_no like '$search_string'";
		}
		else {
			if ($db_type == 0) {
				$search_field_cond = "and a.booking_date like '" . change_date_format(trim($data[0]), "yyyy-mm-dd", "-") . "'";
			} else {
				$search_field_cond = "and a.booking_date like '" . change_date_format(trim($data[0]), '', '', 1) . "'";
			}
		}
	} else {
		$search_field_cond = "";
	}

	$color_arr = return_library_array("select id,color_name from lib_color", 'id', 'color_name');

	//Without Order
	//echo $search_by.'='.$batch_against.'='.$is_sales_booking.'='.$booking_type;
	if ($booking_type == 2 && $batch_against == 3 && $is_sales_booking == "false" && $search_by != 7)
	{
		$buyer_arr = return_library_array("select id,short_name from lib_buyer", 'id', 'short_name');

		if(trim($data[0]) != "")
		{
			if ($search_by == 1)
			{
				$search_field_cond_sample = "and s.booking_no_prefix_num = ". trim($data[0]) ."";
			}
			else if ($search_by == 4) {
				if ($db_type == 0) {
					$search_field_cond_sample = "and s.booking_date like '" . change_date_format(trim($data[0]), "yyyy-mm-dd", "-") . "'";
				} else {
					$search_field_cond_sample = "and s.booking_date like '" . change_date_format(trim($data[0]), '', '', 1) . "'";
				}
			} else
			$search_field_cond_sample = "";
		}

		$booking_year_cond="";
		if($booking_year>0)
		{
			if ($db_type == 0)
			{
				$booking_year_cond=" and YEAR(s.booking_date)=$booking_year";
			}
			else if ($db_type == 2)
			{
				$booking_year_cond=" and to_char(s.booking_date,'YYYY')=$booking_year";
			}
		}

		$sql = "SELECT s.id, s.booking_no, s.booking_date, s.buyer_id, f.style_des, f.body_part, f.fabric_description, f.color_type_id, null as job_no_mst, s.entry_form_id, 1 as types, (case when  c.fabric_color is null or c.fabric_color<=0 then c.color_id else c.fabric_color end )  as fabric_color_id, f.fabric_color
		FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls f left join  sample_development_rf_color c on c.mst_id=f.style_id and c.status_active =1
		WHERE s.booking_no=f.booking_no  and s.company_id=$company_id and s.status_active =1 and s.is_deleted =0 and f.status_active =1 and f.is_deleted =0 and s.item_category=2 $buyer_id_samp_cond $search_field_cond_sample $booking_year_cond
		group by s.id,  s.booking_no, s.booking_date, s.buyer_id, f.style_des,  f.fabric_color,f.color_type_id, s.entry_form_id ,c.color_id,  c.fabric_color , f.body_part, f.fabric_description";
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="115">Booking No</th>
				<th width="75">Booking Date</th>
				<th width="60">Buyer</th>
				<th width="90">Color</th>
				<th width="90">Color Type</th>
				<th width="50">Without Order</th>
				<th width="145">Style Desc.</th>
				<th width="85">Body Part</th>
				<th>Construction/ Composition</th>
			</thead>
		</table>
		<div style="width:990px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table"	id="tbl_list_search">
				<?
				$i = 1;
				foreach ($result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

					if($row[csf('entry_form_id')] == 140 )
					{
						$fabric_color_id = $row[csf('fabric_color_id')];
					}else{
						$fabric_color_id = $row[csf('fabric_color')];
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
						onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $fabric_color_id; ?>','<? echo $color_arr[$fabric_color_id]; ?>','<? echo $row[csf('job_no_mst')]; ?>','',<? echo 1; ?>,'<? echo $search_by; ?>','<? echo $row[csf('color_type_id')];?>','<? echo $row[csf('entry_form_id')]?>','0');">
						<td width="30"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td width="75" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
						<td width="60"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
						<td width="90"><p><? echo $color_arr[$fabric_color_id]; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $color_type[$row[csf('color_type_id')]]; ?>&nbsp;</p></td>
						<td width="50" align="center"><? echo "Yes"; ?></td>
						<td width="145"><p><? echo $row[csf('style_des')]; ?>&nbsp;</p></td>
						<td width="85"><p><? echo $body_part[$row[csf('body_part')]]; ?>&nbsp;</p></td>

						<td><p><? echo $row[csf('fabric_description')]; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
		<?
	}
	else if ($search_by == 7 || $is_sales_booking == "true") {
		// if search by sales order or booking agains sales order
		$search_by = 7;
		$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
		$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
		$po_details = sql_select("select a.id, a.booking_no, a.buyer_id, c.job_no,b.style_ref_no,c.po_break_down_id as po_id from wo_booking_mst a, wo_booking_dtls c,wo_po_details_master b
			where a.booking_no=c.booking_no and c.job_no=b.job_no and a.company_id=$company_id and a.status_active =1 and a.is_deleted=0 and b.status_active=1
			and b.is_deleted=0 and a.item_category=2 and a.entry_form in(86,118,88,119,89,90) group by a.id, a.booking_no, a.buyer_id,c.po_break_down_id, c.job_no,b.style_ref_no");
		foreach ($po_details as $po_row) {
			$po_arr[$po_row[csf("booking_no")]]["po_id"] 		= $po_row[csf("booking_no")];
			$po_arr[$po_row[csf("booking_no")]]["buyer_id"] 	= $po_row[csf("buyer_id")];
			$po_arr[$po_row[csf("booking_no")]]["job_no"] 		= $po_row[csf("job_no")];
			$po_arr[$po_row[csf("booking_no")]]["style_ref_no"] = $po_row[csf("style_ref_no")];
			$po_by_booking_arr[$po_row[csf('booking_no')]].=$po_row[csf('po_id')].',';
		}
		if ($is_sales_booking == "true") {
			$sales_job_cond = " and s.sales_booking_no LIKE trim('%$data[0]%')";
		} else {
			$sales_job_cond = " and s.job_no LIKE trim('%$data[0]%')";
		}
		$booking_year_cond="";
		if($booking_year>0)
		{
			if ($db_type == 0)
			{
				$booking_year_cond=" and YEAR(s.booking_date)=$booking_year";
			}
			else if ($db_type == 2)
			{
				$booking_year_cond=" and to_char(s.booking_date,'YYYY')=$booking_year";
			}
		}
		$sql = "select s.id, sd.mst_id,s.job_no, s.company_id, s.sales_booking_no, s.booking_date, s.within_group,s.buyer_id,s.style_ref_no,s.remarks, sd.color_id,sd.color_type_id from fabric_sales_order_mst s,fabric_sales_order_dtls sd where s.id = sd.mst_id and s.company_id = $company_id $sales_job_cond $booking_year_cond and s.status_active=1 and s.is_deleted=0 and sd.status_active=1 and sd.is_deleted=0 group by s.id, sd.mst_id,s.job_no, s.company_id, s.sales_booking_no,sd.color_type_id, s.booking_date, s.within_group,s.buyer_id,s.style_ref_no,s.remarks, sd.color_id order by s.booking_date desc";
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="920">
			<thead>
				<th width="30">SL</th>
				<th width="115">Booking No</th>
				<th width="75">Booking Date</th>
				<th width="60">Buyer</th>
				<th width="115">Sales Order No</th>
				<th width="70">Within Group</th>
				<th width="115">Job No</th>
				<th width="100">Style Ref.</th>
				<th width="70">Color</th>
				<th width="90">Color Type</th>
				<th>Buyer Order</th>
			</thead>
		</table>
		<div style="width:1040px; max-height:270px; overflow-y:scroll; text-align: center;" id="order_tbl">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table"
			id="tbl_list_search">
			<?
			$i = 1;

			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				$job = $po_arr[$row[csf('sales_booking_no')]]['job_no'];
				$within_group = $row[csf('within_group')];
				$buyer  = ($within_group == 1) ? $buyer_arr[$po_arr[$row[csf('sales_booking_no')]]["buyer_id"]] : $buyer_arr[$row[csf('buyer_id')]];
				$job_no = ($within_group == 1) ? $po_arr[$row[csf('sales_booking_no')]]["job_no"] : "";
				$style  = ($within_group == 1) ? $po_arr[$row[csf('sales_booking_no')]]["style_ref_no"] : $row[csf('style_ref_no')];
				$po_ids=rtrim($po_by_booking_arr[$row[csf('sales_booking_no')]],',');
				$po_ids=implode(",",array_unique(explode(",",$po_ids)));
			//	echo $po_ids.'dd';
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					data-values="<? echo $row[csf('dtls_id')]; ?>##<? echo $row[csf('job_no')]; ?>##<? echo $row[csf('color_id')]; ?>##<? echo $color_arr[$row[csf('color_id')]]; ?>##''##<? echo $row[csf('sales_booking_no')]; ?>##0##<? echo $search_by; ?>##<? echo $row[csf('within_group')]; ?>##<? echo $row[csf('id')]; ?>##<? echo $row[csf('remarks')];?>##1##<? echo $po_ids; ?>">
					<td width="30"><? echo $i; ?></td>
					<td width="115"><? echo $row[csf('sales_booking_no')]; ?></td>
					<td width="75" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="60"><? echo $buyer; ?></td>
					<td width="115" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="70" align="center"><? echo ($row[csf('within_group')] == 1) ? "Yes" : "No"; ?></td>
					<td width="115" align="center"><? echo $job_no; ?></td>
					<td width="100"><? echo $style; ?></td>
					<td width="70"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
					<td width="90"><? echo $color_type[$row[csf('color_type_id')]]; ?></td>
					<td>
						<?php if ($job != "") { ?>
							<a href='#' data-job="<? echo $job; ?>" data-company="<? echo $company_id; ?>"
								class="view_order">view</a>
							<?php } ?>
						</td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
		<?
	} else {

		$booking_year_cond="";
		if($booking_year>0)
		{
			if ($db_type == 0)
			{
				$booking_year_cond=" and YEAR(a.booking_date)=$booking_year";
			}
			else if ($db_type == 2)
			{
				$booking_year_cond=" and to_char(a.booking_date,'YYYY')=$booking_year";
			}
		}

		if($batch_against==3)
		{
			$booking_type_cond = " and a.booking_type=4";
		}
		else if($batch_against==1){
			$booking_type_cond = " and a.booking_type=1";
		}

		$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
		$sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id,b.color_type, c.job_no_mst,c.id po_id,c.po_number,c.file_no,c.grouping, 0 as type,d.style_ref_no, a.entry_form from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted =0 and a.item_category=2 and a.company_id=$company_id $buyer_id_cond $search_field_cond $booking_type_cond $booking_year_cond and d.status_active =1 and d.is_deleted=0 group by a.id, b.fabric_color_id,b.color_type, a.booking_no, a.booking_date, a.buyer_id, c.job_no_mst,c.id,c.po_number,c.file_no, c.grouping,d.style_ref_no, a.entry_form order by a.booking_date desc";
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="115">Booking No</th>
				<th width="75">Booking Date</th>
				<th width="100">Buyer</th>
				<th width="85">Job No</th>
				<th width="100">Style Ref.</th>
				<th width="70">Color</th>
				<th width="90">Color Type</th>
				<? if ($batch_against == 3) { ?>
					<th width="60">Without Order</th><? } ?>
					<th width="100">Internal Ref.</th>
					<th width="80">File No</th>
					<th>Buyer Order</th>
				</thead>
			</table>
			<div style="width:1040px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" class="rpt_table" id="tbl_list_search">
					<?php $i = 1;
					foreach ($result as $row) {
						$po_by_booking_arr[$row[csf('booking_no')]][$row[csf('job_no_mst')]].=$row[csf('po_id')].',';
					}
					//print_r($po_by_booking_arr);
					foreach ($result as $row) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$po_no = $row[csf('po_number')];
						$intl_ref = $row[csf('grouping')];
						$file_no = $row[csf('file_no')];
						$po_by_booking_ids=rtrim($po_by_booking_arr[$row[csf('booking_no')]][$row[csf('job_no_mst')]],',');
						$po_by_booking_ids=implode(",",array_unique(explode(",",$po_by_booking_ids)));
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
							onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('fabric_color_id')]; ?>','<? echo $color_arr[$row[csf('fabric_color_id')]]; ?>','<? echo $row[csf('job_no_mst')]; ?>','','<? echo $row[csf('type')]; ?>','<? echo $search_by; ?>','<? echo $row[csf('color_type')]; ?>','<? echo $row[csf('entry_form')]?>','<? echo $po_by_booking_ids;?>');">
							<td width="30"><? echo $i; ?></td>
							<td width="115"><p><? echo $row[csf('booking_no')]; ?></p></td>
							<td width="75" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
							<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
							<td width="85" align="center"><p><? echo $row[csf('job_no_mst')]; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $color_arr[$row[csf('fabric_color_id')]]; ?>&nbsp;</p>
							</td>
							<td width="90"><p><? echo $color_type[$row[csf('color_type')]]; ?>&nbsp;</p>
							</td>
							<? if ($batch_against == 3) { ?>
								<td width="60"
								align="center"><? echo "No";// if($row[csf('type')]==0) echo "No"; else echo "Yes"; ?></td><? } ?>
								<td width="100"><p><? echo $intl_ref; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $file_no; ?>&nbsp;</p></td>
								<td><p><? echo $po_no; ?>&nbsp;</p></td>
								</tr>                <? $i++;
							} ?>
						</table>
					</div>
					<?
				}
				exit();
			}

			if($action == "check_if_barcode_scanned"){
				$barcode = $data;
				$scanned_barcode_data=sql_select("select id, barcode_no as BARCODE_NO from pro_roll_details where barcode_no='$data' and entry_form=56 and status_active=1 and is_deleted=0");
				if(!empty($scanned_barcode_data)){
					echo "1";
				}else{
					echo "0";
				}
				exit();
			}

			if ($action == "order_popup") {
				echo load_html_head_contents("Order Info", "../../", 1, 1, '', '1', '');
				extract($_REQUEST);
				$po_info = sql_select("select a.job_no,a.style_ref_no,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				?>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
					<thead>
						<th width="30">SL</th>
						<th width="115">Job No</th>
						<th width="75">Style Reference No</th>
						<th width="60">PO Number</th>
					</thead>
					<tbody>
						<?php
						$i = 1;
						foreach ($po_info as $row) {
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="30" align="center"><? echo $i; ?></td>
								<td width="115" align="center"><? echo $row[csf('job_no')]; ?></td>
								<td width="75" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
								<td width="60" align="center"><? echo $row[csf('po_number')]; ?></td>
							</tr>
							<?php
							$i++;
						}
						?>
					</tbody>
				</table>
				<?php
				exit();
			}

			if ($action == "populate_color_id") {
				$data = explode("**", $data);
				$booking_no = $data[0];
				$color_name = $data[1];

				$color_id = return_field_value("distinct(a.id) as id", "lib_color a, wo_booking_dtls b ", "a.id=b.fabric_color_id and a.color_name='$color_name' and b.booking_no='$booking_no' and b.status_active=1 and b.is_deleted=0", "id");
				echo $color_id;
				exit();
			}

			if ($action == "dyeing_check_batch") {
				$data = explode("**", $data);
				$company_id = $data[0];
				$batch_id = $data[1];

				$batch_no= return_field_value("a.batch_no as batch_no", "pro_fab_subprocess a ", "a.batch_id=$batch_id and a.company_id=$company_id and a.entry_form=35 and a.load_unload_id in(1) and a.status_active=1 and a.is_deleted=0", "batch_no");
				echo trim($batch_no);
				exit();
			}

			if ($action == "populate_barcode_data") {
				$data = explode("**", $data);
				$bar_code = $data[0];
				$batch_against = $data[1];
				$booking_without_order = $data[2];
				$booking_no = $data[3];
				$search_type = $data[4];
				$sales_id = $data[5];

				$barcodeData = '';
				$roll_ids = '';
				$po_ids_arr = array();
				$barcodeDataArr = array();
				$rollDataArray = array();
				$dia_type_arr = array();

				$scanned_barcode_arr = array();
				$barcodeDataABatchCreated = sql_select("select barcode_no from pro_roll_details where entry_form=64 and status_active=1 and is_deleted=0 and barcode_no in ($bar_code)");
				foreach ($barcodeDataABatchCreated as $row) {
					$scanned_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
				}
				unset($barcodeDataABatchCreated);

				if ($booking_without_order == 1)
				{
					$sql_barcode = sql_select("select a.barcode_no, a.qc_pass_qnty, a.roll_id, a.roll_no, a.roll_split_from, d.booking_no, d.receive_basis from pro_roll_details a left join pro_roll_details c on a.roll_split_from = c.id and c.status_active =1 left join pro_roll_details d on d.barcode_no = c.barcode_no and d.entry_form = 2 and d.status_active =1 where a.entry_form=62 and a.barcode_no in($bar_code) and a.status_active=1 and a.is_deleted=0 and a.booking_no='$booking_no' order by a.barcode_no");
					foreach ($sql_barcode as $rowb)
					{
						$program_id="";
						if($scanned_barcode_arr[$rowb[csf('barcode_no')]] == "")
						{
							if($rowb[csf('receive_basis')] ==2){
								$program_id = $rowb[csf('booking_no')];
							}

							$barcodeDataArr[$rowb[csf('barcode_no')]] = $rowb[csf('qc_pass_qnty')] . "____" . $rowb[csf('roll_no')] . "__" . $rowb[csf('roll_id')] . "__". $rowb[csf('roll_split_from')] . "__". $program_id;
							$roll_ids .= $rowb[csf('roll_id')] . ",";
							$barcode_ids .= $rowb[csf('barcode_no')] . ",";
						}
					}
					unset($sql_barcode);

				}
				elseif ($search_type == 7)
				{
					$program_arr=array();

					$sql_barcode = sql_select("select a.barcode_no, a.qc_pass_qnty, a.roll_id, a.roll_no,a.po_breakdown_id,a.booking_no as program_id,b.within_group,b.job_no po_number,b.sales_booking_no, a.roll_split_from, d.booking_no as program_no, d.receive_basis from pro_roll_details a left join pro_roll_details c on a.roll_split_from = c.id and c.status_active =1
						left join pro_roll_details d on d.barcode_no = c.barcode_no and d.entry_form = 2 and d.status_active =1,fabric_sales_order_mst b where a.po_breakdown_id=b.id and a.entry_form=62 and a.status_active=1 and a.is_deleted=0 and a.po_breakdown_id = '$sales_id' and a.barcode_no in($bar_code) order by a.barcode_no");
					foreach ($sql_barcode as $rowb)
					{
						$split_source_program_id= "";
						if($scanned_barcode_arr[$rowb[csf('barcode_no')]] == "")
						{
							if($rowb[csf('receive_basis')] ==2)
							{
								$split_source_program_id = $rowb[csf('program_no')];
							}

							$barcodeDataArr[$rowb[csf('barcode_no')]] = $rowb[csf('qc_pass_qnty')] . "__" . $rowb[csf('po_number')] . "__" . $rowb[csf('roll_no')] . "__" . $rowb[csf('roll_id')] . "__" . $rowb[csf('po_breakdown_id')]. "__" . $rowb[csf('roll_split_from')]. "__" . $split_source_program_id;
							$roll_ids .= $rowb[csf('roll_id')] . ",";
							$barcode_ids .= $rowb[csf('barcode_no')] . ",";
							$po_ids_arr[$rowb[csf('po_breakdown_id')]] = $rowb[csf('po_breakdown_id')];
							$within_group = $rowb[csf('within_group')];
							$program_id_arr[] = $rowb[csf('program_id')];
							$program_arr[] = $rowb[csf('program_no')];
						}
					}
					unset($sql_barcode);
					$program_ids = implode(",", array_unique($program_id_arr));
			//echo "select id, width_dia_type from ppl_planning_info_entry_dtls where id in($program_ids)";
					$dia_type_arr = return_library_array("select id, width_dia_type from ppl_planning_info_entry_dtls where id in($program_ids)", 'id', 'width_dia_type');
				}
				else
				{
					if ($batch_against == 1 || $batch_against == 3)
					{

						$sql_result = sql_select("SELECT po_break_down_id FROM wo_booking_dtls WHERE booking_no='$booking_no' and status_active=1 group by po_break_down_id");
						$x = 1;
						foreach($sql_result as $row)
						{
							if($x>1)
							{
								$poIds .= ",".$row[csf('po_break_down_id')];
							} else {
								$poIds = $row[csf('po_break_down_id')];
							}
							$x++;
						}
						unset($sql_result);

						if ($poIds != "") {
					/*$bookingQuery = "SELECT c.barcode_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis in(1,2) and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.is_transfer!=6 and c.po_breakdown_id in($poIds)
					union all
					SELECT c.barcode_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=22 and c.entry_form=22 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($poIds) and a.receive_basis in(2,11,6) and c.is_transfer!=6
					union all
					SELECT c.barcode_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($poIds) and a.receive_basis in(10) and c.re_transfer = 0
					union all
					SELECT barcode_no FROM pro_roll_details WHERE entry_form=83 and status_active=1 and is_deleted=0 and po_breakdown_id in($poIds) and re_transfer = 0
					union all
					SELECT barcode_no FROM pro_roll_details WHERE entry_form in (82,183) and status_active=1 and is_deleted=0 and po_breakdown_id in($poIds) and re_transfer = 0
					union all
					SELECT barcode_no FROM pro_roll_details WHERE entry_form=62 and status_active=1 and is_deleted=0 and po_breakdown_id in($poIds) and roll_split_from>0";*/
					$bookingQuery = "SELECT barcode_no FROM pro_roll_details WHERE entry_form=62 and status_active=1 and is_deleted=0 and po_breakdown_id in($poIds) and barcode_no in($bar_code) ";
				}

				$booking_barcode_data = sql_select($bookingQuery);
				foreach ($booking_barcode_data as $row) {
					$booking_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
				}
				unset($booking_barcode_data);
			}
			$sql_barcode = sql_select("select a.barcode_no,a.qc_pass_qnty,a.roll_id,a.roll_no,b.po_number,a.po_breakdown_id, a.roll_split_from, d.booking_no as program_id, d.receive_basis from pro_roll_details a left join pro_roll_details c on a.roll_split_from = c.id and c.status_active =1 left join pro_roll_details d on d.barcode_no = c.barcode_no and d.entry_form = 2 and d.status_active =1, wo_po_break_down b where a.po_breakdown_id=b.id and a.entry_form=62 and a.barcode_no in($bar_code) and  a.status_active=1 and a.is_deleted=0 group by a.barcode_no,a.qc_pass_qnty,a.roll_id,a.roll_no,b.po_number,a.po_breakdown_id, a.roll_split_from, d.booking_no, d.receive_basis order by a.barcode_no");
			$barcode_ids = "";
			foreach ($sql_barcode as $rowb)
			{
				$program_id="";
				if ((($batch_against == 1 || $batch_against == 3) && in_array($rowb[csf('barcode_no')], $booking_barcode_arr)) || $batch_against == 5)
				{
					if($scanned_barcode_arr[$rowb[csf('barcode_no')]] == "")
					{
						if($rowb[csf('receive_basis')] ==2){
							$program_id = $rowb[csf('program_id')];
						}
						$barcodeDataArr[$rowb[csf('barcode_no')]] = $rowb[csf('qc_pass_qnty')] . "__" . $rowb[csf('po_number')] . "__" . $rowb[csf('roll_no')] . "__" . $rowb[csf('roll_id')] . "__" . $rowb[csf('po_breakdown_id')] . "__" . $rowb[csf('roll_split_from')] . "__" . $program_id;

						$roll_ids .= $rowb[csf('roll_id')] . ",";
						$barcode_ids .= $rowb[csf('barcode_no')] . ",";
						$po_ids_arr[$rowb[csf('po_breakdown_id')]] = $rowb[csf('po_breakdown_id')];
					}
				} else {

				}

			}
			unset($sql_barcode);

			$po_ids = implode(",", $po_ids_arr);
			if($po_ids!='') $po_ids=$po_ids;else $po_ids=0;
			$dia_type_arr = return_library_array("select a.id, a.width_dia_type from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and b.po_id in($po_ids)", 'id', 'width_dia_type');

			$sql_fab = sql_select("select b.id as po_id,a.body_part_id,a.width_dia_type,a.lib_yarn_count_deter_id as deter_id from wo_pre_cost_fabric_cost_dtls a,wo_po_break_down b where  b.job_id=a.job_id and b.id in($po_ids) and a.status_active=1 and a.is_deleted=0 order by a.job_no");
			foreach ($sql_fab as $row) {
				$dia_type_arr2[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('deter_id')]] = $row[csf('width_dia_type')];
			}
			unset($sql_fab);
		}


		$roll_ids = chop($roll_ids, ',');
		$barcode_ids = chop($barcode_ids, ',');

		if ($db_type == 0) {
			$data_array = sql_select("SELECT a.booking_no, a.booking_id, a.receive_basis, a.entry_form, b.prod_id, b.body_part_id, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.barcode_no,c.booking_no program_id, d.product_name_details,d.detarmination_id as deter_id
				FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d
				WHERE a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and c.barcode_no in($barcode_ids) and b.trans_id>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0
				union all
				SELECT '' as booking_no, 0 as booking_id, 0 as receive_basis, a.entry_form, b.prod_id, b.body_part_id, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.barcode_no,c.booking_no program_id, d.product_name_details,d.detarmination_id as deter_id
				FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c, product_details_master d
				WHERE a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and c.barcode_no in(" . $barcode_ids . ") and c.roll_split_from>0 and a.entry_form in(62) and c.entry_form in(62) and c.status_active=1 and c.is_deleted=0 ");
		} else {
			$data_array = sql_select("SELECT a.booking_no, a.booking_id, a.receive_basis, a.entry_form, b.prod_id, b.body_part_id, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.barcode_no, c.booking_no program_id,d.product_name_details,d.detarmination_id as deter_id
				FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d
				WHERE a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and c.barcode_no in($barcode_ids) and b.trans_id>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0
				union all
				SELECT null as booking_no, 0 as booking_id, 0 as receive_basis, a.entry_form, b.prod_id, b.body_part_id, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.barcode_no, c.booking_no program_id,d.product_name_details,d.detarmination_id as deter_id
				FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c, product_details_master d
				WHERE a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and c.barcode_no in(" . $barcode_ids . ") and c.roll_split_from>0 and a.entry_form in(62) and c.entry_form in(62) and c.status_active=1 and c.is_deleted=0");
		}

		foreach ($data_array as $row) {
			$barcode_no = $row[csf('barcode_no')];
			$qc_pass_qnty = $barcodeDataArr[$barcode_no]['qty'];
			$roll_no = $row[csf('roll_no')];
			//echo $row[csf("entry_form")].'='.$row[csf("receive_basis")].'='.$row[csf("program_id")];
			if ($row[csf("entry_form")] == 2 && $row[csf("receive_basis")] == 2) {
				$promram_id = $row[csf('program_id')];
				$widthDiaType = $dia_type_arr[$promram_id];
			}else if ($row[csf("entry_form")] == 58 && $row[csf("receive_basis")] == 10) {
				$promram_id = $row[csf('program_id')];
				$widthDiaType = $dia_type_arr[$promram_id];
			} else {
				$widthDiaType = $dia_type_arr2[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('deter_id')]];
				$promram_id = 0;
			}

			$data = $row[csf('booking_no')] . "**" . $promram_id . "**" . $row[csf('prod_id')] . "**" . $row[csf('product_name_details')] . "**" . $row[csf('body_part_id')] . "**" . $body_part[$row[csf('body_part_id')]] . "**" . $widthDiaType;
			$rollDataArray[$row[csf('barcode_no')]] = $data;
		}
		unset($data_array);
			//echo implode("```", $barcodeDataArr);die;
		if($po_ids =="") $po_id_condition = ""; else $po_id_condition = " and a.po_breakdown_id in($po_ids)";
		$basis_arr = return_library_array("select a.barcode_no, b.receive_basis from pro_roll_details a,inv_receive_master b where a.mst_id=b.id and a.barcode_no in(" . $barcode_ids . ") and b.entry_form=2 and a.entry_form=2 $po_id_condition", 'barcode_no', 'receive_basis');
		if (count($barcodeDataArr) > 0) {
			foreach ($barcodeDataArr as $barcode_no => $value) {
				$basis="";
				$barcodeDatas = explode("__", $value);
				$qc_pass_qnty = $barcodeDatas[0];
				$po_no = $barcodeDatas[1];
				$roll_no = $barcodeDatas[2];
				$roll_id = $barcodeDatas[3];
				$po_breakdown_id = $barcodeDatas[4];
				$roll_split_from = $barcodeDatas[5];
				if($roll_split_from==0){
					$basis=$basis_arr[$barcode_no];
					//$basis = return_field_value("b.receive_basis", "pro_roll_details a,inv_receive_master b", "a.mst_id=b.id and a.barcode_no='$barcode_no' and a.entry_form=2", "receive_basis");
				}

				$rollDatas = explode("**", $rollDataArray[$barcode_no]);
				$booking_no = $rollDatas[0];
				$promram_id = ($basis == 2)?$rollDatas[1]:$barcodeDatas[6];
				$prod_id = $rollDatas[2];
				$product_name_details = $rollDatas[3];
				$body_part_id = $rollDatas[4];
				$body_part_name = $rollDatas[5];
				$widthDiaType = $rollDatas[6];
				//echo $widthDiaType.'AZiz';
				if ($promram_id == "") $promram_id = 0;

				$barcodeData .= $booking_no . "**" . $promram_id . "**" . $prod_id . "**" . $product_name_details . "**" . $roll_id . "**" . $roll_no . "**" . $po_breakdown_id . "**" . $po_no . "**" . $qc_pass_qnty . "**" . $barcode_no . "**" . $body_part_id . "**" . $body_part_name . "**" . $widthDiaType . "#";
			}
			echo substr($barcodeData, 0, -1);
		} else {
			echo "0";
		}
		exit();
	}

	if ($action == "barcode_popup")
	{
		echo load_html_head_contents("Barcode Info", "../../", 1, 1, '', '', '');
		extract($_REQUEST);

		$disbled = "";
		$disbled_drop_down = 0;
		if ($booking_without_order == 1)
		{
			$disbled = "disabled='disabled'";
			$disbled_drop_down = 1;
		}
		?>

		<script>
			function check_all_data()
			{
				var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
				for( var i = 1; i < tbl_row_count; i++ ){
					js_set_value( i );
				}
			}

			var selected_id = new Array();

			function toggle(x, origColor)
			{
				var newColor = 'yellow';
				if (x.style) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
				}
			}

			function js_set_value(str)
			{
				var hdn_batch_color_id = $("#hdn_batch_color_id").val();
				var fabric_color_ids = $("#hdnColorId_"+str).val();
				var color_arr = fabric_color_ids.split(",");
				/*if(color_arr.indexOf(hdn_batch_color_id) < 0){
					alert("Fabric color does not match with Batch color");
					return;
				}*/
				var total_selected_val=$('#hidden_selected_row_total').val()*1;
				toggle(document.getElementById('search' + str), '#FFFFCC');

				if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1)
				{
					selected_id.push($('#txt_individual_id' + str).val());
					total_selected_val=total_selected_val+$('#txt_individual_qty' + str).val()*1;
				}
				else
				{
					for (var i = 0; i < selected_id.length; i++) {
						if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
					}
					selected_id.splice(i, 1);
					total_selected_val=total_selected_val-$('#txt_individual_qty' + str).val()*1;
				}
				var id = '';
				for (var i = 0; i < selected_id.length; i++) {
					id += selected_id[i] + ',';
				}
				id = id.substr(0, id.length - 1);

				$('#hidden_barcode_nos').val(id);
				$('#hidden_selected_row_total').val( total_selected_val.toFixed(2));
			}

			function fnc_close() {
				parent.emailwindow.hide();
			}

			function reset_hide_field() {
				$('#hidden_barcode_nos').val('');
				selected_id = new Array();
			}
		</script>
	</head>
	<body>
		<div align="center" style="width:1020px;">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:1020px; margin-left:2px">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Order No</th>
							<th>Internal Ref.</th>
							<th>File No</th>
							<th>Receive by Batch ID</th>
							<th>Barcode No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
								class="formbutton"/>
								<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">
								<input type="hidden" name="txt_sales_id" id="txt_sales_id"
								value="<?php echo $sales_id; ?>"/>
							</th>
						</thead>
						<tr class="general">
							<td align="center"><input type="text" name="txt_order" id="txt_order" value="" class="text_boxes"/></td>
							<td align="center"><input type="text" name="txt_internal_ref" id="txt_internal_ref" value="" class="text_boxes"/></td>
							<td align="center"><input type="text" name="txt_file" id="txt_file" value="" class="text_boxes"/></td>
							<td align="center"><input type="text" name="txt_receive_by_batch_id" id="txt_receive_by_batch_id" value="" class="text_boxes"/></td>

							<td><input type="text" name="barcode_no" id="barcode_no" style="width:120px"
								class="text_boxes"/></td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show"
									onClick="show_list_view ( document.getElementById('txt_order').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file').value+'_'+document.getElementById('txt_receive_by_batch_id').value+'_'+'<? echo $company_id; ?>'+'_'+document.getElementById('barcode_no').value+'_'+'<? echo $batch_against; ?>'+'_'+'<? echo $booking_without_order; ?>'+'_'+'<? echo $booking_no; ?>'+'_'+'<? echo $search_type; ?>'+'_'+document.getElementById('txt_sales_id').value+'_'+'<? echo $txt_batch_color_id; ?>'+'_'+'<? echo $color_type;?>'+'_'+'<? echo $booking_id;?>', 'create_barcode_search_list_view', 'search_div', 'batch_creation_controller_v2', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')"
									style="width:100px;"/>
								</td>
							</tr>
						</table>
						<div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
					</fieldset>
				</form>
			</div>
		</body>
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
		<?
	}

	if ($action == "create_barcode_search_list_view______________")
	{
		$data = explode("_", $data);
		$search_string = "%" . trim($data[0]) . "%";
		$search_by = $data[1];
		$company_id = $data[2];
		$barcode_no = trim($data[3]);
		$batch_against = trim($data[4]);
		$booking_without_order = trim($data[5]);
		$booking_no = trim($data[6]);
		$is_sales = trim($data[7]);
		$txt_sales_id = trim($data[8]);
		$batch_color_id = trim($data[9]);

		$search_field_cond = "";
		if (trim($data[0]) != "") {
			if ($search_by == 1) $search_field_cond = "and b.po_number like '$search_string'";
			else if ($search_by == 2) $search_field_cond = "and b.grouping like '$search_string'";
			else if ($search_by == 3) $search_field_cond = "and b.file_no like '$search_string'";
		}

		if ($barcode_no != "") {
			$barcode_cond = "and a.barcode_no='$barcode_no'";
		}

		$scanned_barcode_arr = array();
		$booking_barcode_arr = array();
		$barcodeData = sql_select("select barcode_no from pro_roll_details where entry_form=64 and status_active=1 and is_deleted=0");
		foreach ($barcodeData as $row) {
			$scanned_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		}

		//$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
		$product_sql = sql_select("select id,product_name_details,detarmination_id from product_details_master where item_category_id=13");
		foreach ($product_sql as $product_row) {
			$product_arr[$product_row[csf("id")]]["product_name_details"] = $product_row[csf("product_name_details")];
			$product_arr[$product_row[csf("id")]]["detarmination_id"] = $product_row[csf("detarmination_id")];
		}

		$batch_barcode_arr = array();
		$barcodeData_batch = sql_select("SELECT b.prod_id, c.id as roll_id FROM pro_grey_prod_entry_dtls b, pro_roll_details c WHERE b.id=c.dtls_id and c.entry_form in (2,22) and c.status_active=1 and c.is_deleted=0");
		foreach ($barcodeData_batch as $val) {
			$batch_barcode_arr[$val[csf('roll_id')]] = $val[csf('prod_id')];
		}

		$barcode_cond2 = '';
		if ($booking_without_order == 1) {
			$sql = "select a.barcode_no, a.qc_pass_qnty, a.roll_id, a.roll_no from pro_roll_details a where a.entry_form=62 and a.status_active=1 and a.is_deleted=0 and a.booking_without_order=1 and a.booking_no='$booking_no' $barcode_cond order by a.barcode_no";
		} else if ($is_sales == 7) {
			$sql = "select a.barcode_no, a.qc_pass_qnty, a.roll_id, a.roll_no,b.id po_id,b.job_no po_number,b.sales_booking_no from pro_roll_details a,fabric_sales_order_mst b  where a.po_breakdown_id=b.id and a.entry_form=62 and a.status_active=1 and a.is_deleted=0 and a.po_breakdown_id = '$txt_sales_id' $barcode_cond order by a.barcode_no";

			$fabric_color = sql_select("select b.id,c.determination_id, listagg(c.color_id,',') within group (order by c.color_id) as color_ids from fabric_sales_order_mst b, fabric_sales_order_dtls c where b.id=c.mst_id and b.id = $txt_sales_id and c.status_active=1 group by b.id,c.determination_id");
			$fab_color_arr=array();
			foreach ($fabric_color as $fab_row) {
				$fab_color_arr[$fab_row[csf("id")]][$fab_row[csf("determination_id")]]=$fab_row[csf("color_ids")];
			}
		} else {
			if ($batch_against == 1 || $batch_agaiNnst == 3) {
				if ($db_type == 2) $group_concat = "listagg(cast(po_break_down_id AS VARCHAR2(4000)),',') within group (order by po_break_down_id) as po_break_down_id";
				else if ($db_type == 0) $group_concat = " group_concat(po_break_down_id) as po_break_down_id";

				$poIds = return_field_value("$group_concat", "wo_booking_dtls", "booking_no='$booking_no' and status_active=1 group by booking_no", "po_break_down_id");
				/*$po_details = sql_select("select a.po_break_down_id,b.lib_yarn_count_deter_id,listagg(a.fabric_color_id,',') within group (order by a.fabric_color_id) as color_ids from wo_booking_dtls a,wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no='$booking_no' and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 group by a.po_break_down_id,b.lib_yarn_count_deter_id");
				$po_arr=array();
				foreach ($po_details as $po_row) {
					$fab_color_arr[$po_row[csf("po_break_down_id")]][$po_row[csf("lib_yarn_count_deter_id")]]=$po_row[csf("color_ids")];
				}*/
				if ($poIds != "") {
					/*$bookingQuery = "select c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis in(1,2) and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.is_transfer!=6 and c.po_breakdown_id in($poIds)
					union all
					select c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=22 and c.entry_form=22 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($poIds) and a.receive_basis in(2,11) and c.is_transfer!=6
					union all
					select c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($poIds) and a.receive_basis in(10) and c.is_transfer!=6
					union all
					select barcode_no from pro_roll_details where entry_form=83 and status_active=1 and is_deleted=0 and po_breakdown_id in($poIds) and is_transfer=5
					union all
					select barcode_no from pro_roll_details where entry_form=62 and status_active=1 and is_deleted=0 and po_breakdown_id in($poIds) and roll_split_from>0";*/
					$bookingQuery = "select c.barcode_no,listagg(b.color_id ,',') within group (order by c.barcode_no) as color_ids from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis in(1,2) and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.is_transfer!=6 and c.po_breakdown_id in($poIds) group by c.barcode_no
					union all select c.barcode_no,listagg(b.color_id ,',') within group (order by c.barcode_no) as color_ids from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=22 and c.entry_form=22 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($poIds) and a.receive_basis in(2,11) and c.is_transfer!=6 group by c.barcode_no
					union all select c.barcode_no,listagg(b.color_id ,',') within group (order by c.barcode_no) as color_ids from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($poIds) and a.receive_basis in(10) and c.is_transfer!=6 group by c.barcode_no
					union all select barcode_no,'' color_ids from pro_roll_details where entry_form=83 and status_active=1 and is_deleted=0 and po_breakdown_id in($poIds) and is_transfer=5 union all select barcode_no,'' color_ids from pro_roll_details where entry_form=62 and status_active=1 and is_deleted=0 and po_breakdown_id in($poIds) and roll_split_from>0";
				}

				$booking_barcode_data = sql_select($bookingQuery);
				foreach ($booking_barcode_data as $row) {
					$booking_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
					$booking_barcode_color_arr[$row[csf('barcode_no')]][] = $row[csf('color_ids')];
				}

				if ($db_type == 2 && count($booking_barcode_arr) > 1000) {
					$pre = " and (";
					$suff .= ")";
					$barcodesArr = array_chunk($booking_barcode_arr, 999);
					foreach ($barcodesArr as $barcodes) {
						$ids = implode(",", $barcodes);
						$barcodes_cond .= " a.barcode_no in($ids) or ";
					}
					$barcode_cond2 = $pre . chop($barcodes_cond, 'or ') . $suff;
				} else {
					if (empty($booking_barcode_arr)) {
						$empty_val = 0;
						$barcode_cond2 = "and a.barcode_no in(" . $empty_val . ")";
					} else {

						$barcode_cond2 = "and a.barcode_no in(" . implode(",", $booking_barcode_arr) . ")";
					}
				}
			}
			$sql = "select a.barcode_no, a.qc_pass_qnty, a.roll_id, a.roll_no,b.id po_id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, b.job_no_mst from pro_roll_details a, wo_po_break_down b where a.po_breakdown_id=b.id and a.entry_form=62 and a.status_active=1 and a.is_deleted=0 and a.booking_without_order!=1 $search_field_cond $barcode_cond $barcode_cond2 group by a.barcode_no, a.qc_pass_qnty, a.roll_id, a.roll_no,b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, b.job_no_mst order by a.barcode_no";
		}
		$result = sql_select($sql);
		?>
		<input type="hidden" id="hdn_batch_color_id" value="<? echo $batch_color_id; ?>" />
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="180">Fabric Description</th>
				<th width="80">Fabric Color</th>
				<th width="90">Job No</th>
				<th width="115"><?php echo ($is_sales == 7) ? "Sales " : ""; ?>Order No</th>
				<th width="90">Internal Ref.</th>
				<th width="70">File No</th>
				<th width="80">Shipment Date</th>
				<th width="80">Barcode No</th>
				<th width="60">Roll No</th>
				<th>Roll Qty.</th>
			</thead>
		</table>
		<div style="width:910px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
			<?
			if (count($result) == 0) {
				echo "<div style='width:890px; align='center'><b>No Data Found</b></div>";
				die;
			}
			?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table"
			id="tbl_list_search">
			<?
			$i = 1;
			$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
			foreach ($result as $row) {
				if ($scanned_barcode_arr[$row[csf('barcode_no')]] == "") {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
						id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="40" align="center">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>"
							value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="180"><? echo $product_arr[$batch_barcode_arr[$row[csf('roll_id')]]]["product_name_details"];?></td>
						<td width="80">
							<?

							$color_ids = array_unique(explode(",",implode(",",$booking_barcode_color_arr[$row[csf('barcode_no')]])));
							//$color_ids = array_unique(explode(",",$fab_color_arr[$row[csf("po_id")]][$product_arr[$batch_barcode_arr[$row[csf('roll_id')]]]["detarmination_id"]]));
							$colorArr = array();
							foreach ($color_ids as $color) {
								$colorArr[]=$color_arr[$color];
							}
							echo implode(",",$colorArr);
							?>
							<input type="hidden" id="hdnColorId_<? echo $i; ?>" value="<? echo implode(",",$color_ids);?>" />
						</td>
						<td width="90" align="center"><? echo $row[csf('job_no_mst')]; ?></td>
						<td width="115" align="center"><? echo $row[csf('po_number')]; ?></td>
						<td width="90"><? echo $row[csf('grouping')]; ?></td>
						<td width="70"><? echo $row[csf('file_no')]; ?></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
						<td width="80" align="center"><? echo $row[csf('barcode_no')]; ?></td>
						<td width="60" align="center"><? echo $row[csf('roll_no')]; ?></td>
						<td align="right"><? echo number_format($row[csf('qc_pass_qnty')], 2); ?></td>
					</tr>
					<?
					$i++;
				}
			}
			?>
		</table>
	</div>
	<table width="720">
		<tr>
			<td align="center">
				<input type="button" name="close" class="formbutton" value="Close" id="main_close"
				onClick="fnc_close();" style="width:100px"/>
			</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action == "create_barcode_search_list_view")
{
	list($order_no,$int_ref,$file_no,$received_by_batch_id,$company_id ,$barcode_no,$batch_against,$booking_without_order,$booking_no,$is_sales,$txt_sales_id,$batch_color_id,$colo_type_id,$booking_id) = explode("_", $data);

	$search_field_cond = "";
	if ($order_no != "") {
		$search_field_cond .= " and b.po_number like '$order_no'";
	}
	if ($int_ref != "") {
		$search_field_cond .= " and b.grouping like '$int_ref'";
	}
	if ($file_no != "") {
		$search_field_cond .= " and b.file_no like '$file_no'";
	}
	if ($received_by_batch_id != "") {
		$search_field_cond .= "and c.recv_number_prefix_num like '%$received_by_batch_id%' ";
	}
	if ($barcode_no != "") {
		$barcode_cond = "and a.barcode_no='$barcode_no'";
	}

	$body_part_result = sql_select("select id, body_part_full_name from lib_body_part");
	$body_partArr = array();
	foreach ($body_part_result as $row) {
		$body_part_Arr[$row[csf('id')]] = $row[csf('body_part_full_name')];
	}

	$booking_barcode_arr = array();

	$barcode_cond2 = '';
	if ($booking_without_order == 1)
	{
		$sql = "SELECT a.po_breakdown_id po_id,a.barcode_no, a.qc_pass_qnty, a.roll_id, a.roll_no from pro_roll_details a where a.entry_form=62 and a.status_active=1 and a.is_deleted=0 and a.booking_without_order=1 and a.po_breakdown_id='$booking_id' $barcode_cond order by a.barcode_no";

		$trans_out_barcode = sql_select("SELECT b.from_order_id,a.barcode_no from pro_roll_details a, inv_item_transfer_mst b where a.mst_id = b.id and b.entry_form = 180 and b.entry_form = 180 and b.from_order_id in ($booking_id) and a.re_transfer=0 and a.status_active = 1 and b.status_active = 1");

		foreach ($trans_out_barcode as $val)
		{
			$trans_out_barcode_arr[$val[csf('from_order_id')]][$val[csf('barcode_no')]] = $val[csf('barcode_no')];
		}
	}
	else if ($is_sales == 7)
	{
		$sql = "SELECT a.barcode_no, a.qc_pass_qnty, a.roll_id, a.roll_no, d.id as po_id, d.job_no as po_number, d.sales_booking_no from pro_roll_details a, wo_po_break_down b, inv_receive_mas_batchroll c,  fabric_sales_order_mst d where a.po_breakdown_id=d.id and d.id=b.id and c.id=a.mst_id and a.entry_form=62 and a.status_active=1 and a.is_deleted=0 and a.po_breakdown_id='$txt_sales_id' $barcode_cond $search_field_cond order by a.barcode_no";

		$fabric_color = sql_select("SELECT b.id,c.determination_id, listagg(c.color_id,',') within group (order by c.color_id) as color_ids from fabric_sales_order_mst b, fabric_sales_order_dtls c where b.id=c.mst_id and b.id = $txt_sales_id and c.status_active=1 group by b.id,c.determination_id");
		$fab_color_arr=array();
		foreach ($fabric_color as $fab_row) {
			$fab_color_arr[$fab_row[csf("id")]][$fab_row[csf("determination_id")]]=$fab_row[csf("color_ids")];
		}
	}
	else
	{
		if ($batch_against == 1 || $batch_against == 3)
		{
			if ($db_type == 2) $group_concat = "listagg(cast(po_break_down_id AS VARCHAR2(4000)),',') within group (order by po_break_down_id) as po_break_down_id";
			else if ($db_type == 0) $group_concat = " group_concat(po_break_down_id) as po_break_down_id";

			$sql_result =sql_select("select po_break_down_id from wo_booking_dtls where booking_no='$booking_no' and status_active=1 group by booking_no,po_break_down_id");

			$poId = 0;
			foreach($sql_result as $row)
			{
				if($poId==0)
				{
					$poIds = $row[csf('po_break_down_id')];
				} else {
					$poIds .= ",".$row[csf('po_break_down_id')];
				}
				$poId++;
			}

			if ($poIds != "")
			{

				$sql = "SELECT c.recv_number,a.barcode_no, a.qc_pass_qnty, a.roll_id, a.roll_no,b.id po_id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, b.job_no_mst, a.roll_split_from from pro_roll_details a, wo_po_break_down b,inv_receive_mas_batchroll c where c.id=a.mst_id and a.po_breakdown_id=b.id and a.entry_form=62 and c.entry_form=62 and a.status_active=1 and a.is_deleted=0 and a.booking_without_order!=1 and a.po_breakdown_id in($poIds) $search_field_cond $barcode_cond  group by c.recv_number,a.barcode_no, a.qc_pass_qnty, a.roll_id, a.roll_no,b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, b.job_no_mst,a.roll_split_from order by c.recv_number,a.barcode_no ";
			}
			else
			{
				echo "<div style='width:890px; align='center'><b>No Data Found .</b></div>";
				die;
			}

			$booking_barcode_data = sql_select($sql);
			foreach ($booking_barcode_data as $row)
			{
				$booking_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];

				if($row[csf('roll_split_from')]>0)
				{
					$split_barcode_arr[$row[csf("barcode_no")]] = $row[csf('barcode_no')];
				}
			}

			if(!empty($split_barcode_arr))
			{
				$split_barcode_nos = implode(",", $split_barcode_arr);
				$barCond = $split_barcode_cond = "";
				if($db_type==2 && count($split_barcode_arr)>999)
				{
					$split_barcode_arr_chunk=array_chunk($split_barcode_arr,999) ;
					foreach($split_barcode_arr_chunk as $chunk_arr)
					{
						$barCond.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";
					}
					$split_barcode_cond.=" and (".chop($barCond,'or ').")";
				}
				else
				{
					$split_barcode_cond=" and a.barcode_no in($split_barcode_nos)";
				}

				$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.entry_form = 62 $split_barcode_cond and a.roll_id = b.id and a.status_active =1 and b.status_active=1");
				if(!empty($split_ref_sql))
				{
					foreach ($split_ref_sql as $value)
					{
						$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
						$booking_barcode_arr[$value[csf("mother_barcode")]] = $value[csf("mother_barcode")];
					}
				}
			}

			$all_booking_barcode_arr = array_filter($booking_barcode_arr);

			if(!empty($all_booking_barcode_arr))
			{
				$all_barcode_no_cond=""; $barCond="";
				$all_barcode_nos = implode(",", $booking_barcode_arr);
				if($db_type==2 && count($all_booking_barcode_arr)>999)
				{
					$all_booking_barcode_arr_chunk=array_chunk($all_booking_barcode_arr,999) ;
					foreach($all_booking_barcode_arr_chunk as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$barCond.="  c.barcode_no in($chunk_arr_value) or ";
					}

					$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
				}
				else
				{
					$all_barcode_no_cond=" and c.barcode_no in($all_barcode_nos)";
				}

				if ($db_type == 2) $group_concat = "listagg(b.color_id ,',') within group (order by c.barcode_no) as color_ids";
				else if ($db_type == 0) $group_concat = " group_concat(b.color_id) as color_ids";

				$ref_barcode_sql = sql_select("SELECT c.barcode_no,$group_concat,b.body_part_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis in(1,2) and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and b.trans_id>0 $all_barcode_no_cond group by c.barcode_no,b.body_part_id
					union all select c.barcode_no,$group_concat,b.body_part_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=22 and c.entry_form=22 and c.status_active=1 and c.is_deleted=0 and a.receive_basis in(2,11) $all_barcode_no_cond group by c.barcode_no,b.body_part_id
					union all select c.barcode_no,$group_concat,b.body_part_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and c.status_active=1 and c.is_deleted=0 and a.receive_basis in(10) $all_barcode_no_cond group by c.barcode_no,b.body_part_id");

				foreach ($ref_barcode_sql as $row)
				{
					$booking_barcode_color_arr[$row[csf('barcode_no')]][] = $row[csf('color_ids')];
					$booking_barcode_body_part_arr[$row[csf('barcode_no')]] = $row[csf('body_part_id')];
				}

			}
		}
	}

	$result = sql_select($sql);
	$barcode_no_arr = $role_id_arr = array();
	foreach ($result as $row)
	{
		$barcode_no_arr[] = $row[csf("barcode_no")];
		$role_id_arr[] = $row[csf("roll_id")];
		$all_barcode_no_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		$all_role_id_arr[$row[csf("roll_id")]] = $row[csf("roll_id")];
	}

	$all_barcode_no_arr = array_filter(array_unique($all_barcode_no_arr));

	if(count($all_barcode_no_arr)>0)
	{
		$all_barcode_nos = implode(",", $all_barcode_no_arr);
		$BarCond = $rcv_by_batch_barcode_cond = "";

		if($db_type==2 && count($all_barcode_no_arr)>999)
		{
			$all_barcode_no_chunk=array_chunk($all_barcode_no_arr,999) ;
			foreach($all_barcode_no_chunk as $chunk_arr)
			{
				$BarCond.=" barcode_no in(".implode(",",$chunk_arr).") or ";
			}

			$rcv_by_batch_barcode_cond.=" and (".chop($BarCond,'or ').")";

		}
		else
		{
			$rcv_by_batch_barcode_cond=" and barcode_no in($all_barcode_nos)";
		}
	}

	$all_role_id_arr = array_filter(array_unique($all_role_id_arr));
	if(count($all_role_id_arr)>0)
	{
		$all_role_ids = implode(",", $all_role_id_arr);
		$rollCond = $all_roll_id_cond = "";

		if($db_type==2 && count($all_role_id_arr)>999)
		{
			$all_role_id_chunk=array_chunk($all_role_id_arr,999) ;
			foreach($all_role_id_chunk as $chunk_arr)
			{
				$rollCond.=" c.id in(".implode(",",$chunk_arr).") or ";
			}

			$all_roll_id_cond.=" and (".chop($rollCond,'or ').")";
		}
		else
		{
			$all_roll_id_cond=" and c.id in($all_role_ids)";
		}
	}

	$scanned_barcode_arr = array();
	if(!empty($all_barcode_no_arr))
	{
		$barcodeData = sql_select("select barcode_no from pro_roll_details where entry_form=64 and status_active=1 and is_deleted=0 $rcv_by_batch_barcode_cond ");
		foreach ($barcodeData as $row) {
			$scanned_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		}
	}

	$batch_barcode_arr = array();
	if(!empty($all_role_id_arr))
	{
		$barcodeData_batch = sql_select("SELECT b.prod_id, c.id as roll_id FROM pro_grey_prod_entry_dtls b, pro_roll_details c WHERE b.id=c.dtls_id and c.entry_form in (2,22) and c.status_active=1 and c.is_deleted=0 $all_roll_id_cond ");
		foreach ($barcodeData_batch as $val) {
			$batch_barcode_arr[$val[csf('roll_id')]] = $val[csf('prod_id')];
		}
	}
	$product_sql = sql_select("select id,product_name_details,detarmination_id from product_details_master where item_category_id=13");
	foreach ($product_sql as $product_row)
	{
		$product_arr[$product_row[csf("id")]]["product_name_details"] = $product_row[csf("product_name_details")];
		$product_arr[$product_row[csf("id")]]["detarmination_id"] = $product_row[csf("detarmination_id")];
	}
	?>
	<input type="hidden" id="hdn_batch_color_id" value="<? echo $batch_color_id; ?>"/>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1190" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="100">Body Part</th>
			<th width="180">Fabric Description</th>
			<th width="100">Color Type</th>
			<th width="80">Fabric Color</th>
			<th width="90">Job No</th>
			<th width="115"><?php echo ($is_sales == 7) ? "Sales " : ""; ?>Order No</th>
			<th width="90">Internal Ref.</th>
			<th width="70">File No</th>
			<th width="70">Shipment Date</th>
			<th width="80">Barcode No</th>
			<th width="35">Roll No</th>
			<th width="50">Roll Qty.</th>
			<th>Receive No</th>
		</thead>
	</table>
	<div style="width:1210px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
		<?
		if (count($result) == 0)
		{
			echo "<div style='width:890px; align='center'><b>No Data Found</b></div>";
			die;
		}
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1190" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;$total_roll_qty=0;
		$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
		foreach ($result as $row)
		{
			if ($scanned_barcode_arr[$row[csf('barcode_no')]] == "")
			{
				if ($trans_out_barcode_arr[$row[csf('po_id')]][$row[csf('barcode_no')]] == "")
				{

					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"	id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="30" align="center">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>"
							value="<?php echo $row[csf('barcode_no')]; ?>"/>
							<input type="hidden" name="txt_individual_qty[]" id="txt_individual_qty<?php echo $i; ?>" value="<?php echo $row[csf('qc_pass_qnty')]; ?>"/>
						</td>
						<td width="100" align="center">
							<?
							if($mother_barcode_arr[$row[csf("barcode_no")]]=="")
							{
								echo $body_part_Arr[$booking_barcode_body_part_arr[$row[csf('barcode_no')]]];
							}else{
								echo $body_part_Arr[$booking_barcode_body_part_arr[$mother_barcode_arr[$row[csf("barcode_no")]]]];
							}
							?>
						</td>
						<td width="180"><p><? echo $product_arr[$batch_barcode_arr[$row[csf('roll_id')]]]["product_name_details"];?></p></td>
						<td width="100" align="center"><? echo $color_type[$colo_type_id]; ?></td>
						<td width="80"><p>
							<?

							if($mother_barcode_arr[$row[csf("barcode_no")]]=="")
							{
								$color_ids = array_unique(explode(",",implode(",",$booking_barcode_color_arr[$row[csf('barcode_no')]])));
							}else{
								$color_ids = array_unique(explode(",",implode(",",$booking_barcode_color_arr[$mother_barcode_arr[$row[csf("barcode_no")]]])));
							}

							$colorArr = array();
							foreach ($color_ids as $color) {
								$colorArr[]=$color_arr[$color];
							}
							echo implode(",",$colorArr);
							?>
							<input type="hidden" id="hdnColorId_<? echo $i; ?>" value="<? echo implode(",",$color_ids);?>" />
						</p></td>
						<td width="90" align="center"><? echo $row[csf('job_no_mst')]; ?></td>
						<td width="115" align="center"><? echo $row[csf('po_number')]; ?></td>
						<td width="90"><? echo $row[csf('grouping')]; ?></td>
						<td width="70"><? echo $row[csf('file_no')]; ?></td>
						<td width="70" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
						<td width="80" align="center"><? echo $row[csf('barcode_no')]; ?></td>
						<td width="35" align="center"><? echo $row[csf('roll_no')]; ?></td>
						<td width="50" align="right"><? echo number_format($row[csf('qc_pass_qnty')], 2); ?></td>
						<td align="right"><? echo $row[csf('recv_number')]; ?></td>
					</tr>
					<?
					$total_roll_qty+=$row[csf('qc_pass_qnty')];
					$i++;
				}
			}
		}
		?>
		<tfoot>
			<tr>
				<th colspan="12">Total Qty </th>
				<th align="right"><? echo number_format($total_roll_qty,2);?> </th>
				<th> &nbsp;</td>
				</tr>
			</tfoot>
		</table>
	</div>
	<table width="1120">
		<tr>
			<td width="30" align="center"><input type="checkbox" id="chk_all" onClick="check_all_data()"></td>
			<td>Check/Uncheck All</td>
			<td align="center">
				<input type="button" name="close" class="formbutton" value="Close" id="main_close"
				onClick="fnc_close();" style="width:100px"/>
			</td>
			<td colspan="2" align="right">
				Selected Row Total<input type="text"  style="width:70px" class="text_boxes_numeric" name="hidden_selected_row_total" id="hidden_selected_row_total" readonly value="0">
			</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action == "load_drop_down_po")
{
	$data = explode("**", $data);
	$booking_no = $data[0];
	$color_id = $data[1];
	$roll_maintained = $data[2];
	echo create_drop_down("cboPoNo_1", 130, "SELECT a.id, a.po_number FROM wo_po_break_down a, wo_booking_dtls b WHERE a.id=b.po_break_down_id and b.booking_no='$booking_no' and b.fabric_color_id='$color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number", "id,po_number", 1, "-- Select Po Number --", '0', "load_item_desc(this.value,this.id );", '', "", "", "", "", "", "", "cboPoNo[]");
	exit();
}

if ($action == "load_drop_down_po_from_program")
{
	$data 		= explode("**", $data);
	$program_id = $data[0];
	$row_no 	= $data[1];
	$booking_no = $data[2];
	$color_id 	= trim($data[3]);
	$company_id = trim($data[4]);
	$is_sales 	= trim($data[5]);
	$sales_id 	= trim($data[6]);
	$roll_maintained = trim($data[7]);
	//echo $roll_maintained.'=='.$program_id;die;
	if ($program_id == 0) {
		echo create_drop_down("cboPoNo_" . $row_no, 130, "SELECT a.id, a.po_number FROM wo_po_break_down a, wo_booking_dtls b WHERE a.id=b.po_break_down_id and b.booking_no='$booking_no' and b.fabric_color_id='$color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number", "id,po_number", 1, "-- Select Po Number --", '0', "load_item_desc(this.value,this.id );", '', "", "", "", "", "", "", "cboPoNo[]");
	} else {


		$fabric_source = return_field_value("dyeing_fin_bill", "variable_settings_production", "company_name =$company_id and variable_list=44 and is_deleted=0 and status_active=1");
		if ($fabric_source == 3) {
			if($is_sales == 1)
			{
				$sql = "select d.po_breakdown_id as id,a.job_no po_number from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, fabric_sales_order_mst a where c.id=b.mst_id and b.id=d.dtls_id and d.po_breakdown_id = a.id and d.entry_form in(16,61) and d.trans_type=2 and b.program_no=$program_id and c.entry_form in(16,61) and b.status_active=1 and b.is_deleted=0 group by d.po_breakdown_id,a.job_no";
			}
			else
			{
				$sql = "select d.po_breakdown_id as id,a.po_number from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, wo_po_break_down a where c.id=b.mst_id and b.id=d.dtls_id and d.po_breakdown_id = a.id  and d.entry_form in(16,61) and d.trans_type=2 and b.program_no=$program_id and c.entry_form in(16,61) and b.status_active=1 and b.is_deleted=0 group by d.po_breakdown_id,a.po_number";
			}

		} else if ($fabric_source == 1) {
			if($is_sales == 1)
			{
				$sql = "select d.po_breakdown_id as id, a.job_no po_number from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, fabric_sales_order_mst a where c.id=b.mst_id and b.id=d.dtls_id and d.po_breakdown_id = a.id  and d.entry_form in(58,22) and d.trans_type=1 and b.program_no =$program_id and c.booking_without_order=0 and c.receive_basis=10 and c.entry_form in(58,22) and b.status_active=1 and b.is_deleted=0 group by d.po_breakdown_id, a.job_no";
			}
			else
			{
				$sql = "select d.po_breakdown_id as id, a.po_number from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, wo_po_break_down a where c.id=b.mst_id and b.id=d.dtls_id and d.po_breakdown_id = a.id  and d.entry_form in(58,22) and d.trans_type=1 and b.program_no =$program_id and c.booking_without_order=0 and c.receive_basis=10 and c.entry_form in(58,22) and b.status_active=1 and b.is_deleted=0 group by d.po_breakdown_id, a.po_number";
			}

		} else {
			if($is_sales == 1)
			{
				$sql = "select d.po_breakdown_id as id, a.job_no po_number from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, fabric_sales_order_mst a where c.id=b.mst_id and b.id=d.dtls_id and d.po_breakdown_id = a.id and d.entry_form=2 and d.trans_type=1 and c.receive_basis=2 and b.program_no = $program_id and c.booking_without_order=0 and c.receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 group by d.po_breakdown_id, a.job_no";
			}
			else
			{
				$sql = "select d.po_breakdown_id as id, a.po_number from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, wo_po_break_down a where c.id=b.mst_id and b.id=d.dtls_id and d.po_breakdown_id = a.id and d.entry_form=2 and d.trans_type=1 and c.receive_basis=2 and b.program_no = $program_id and c.booking_without_order=0 and c.receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 group by d.po_breakdown_id, a.po_number";
			}
		}


		echo create_drop_down("cboPoNo_" . $row_no, 130, $sql, "id,po_number", 1, "-- Select Po Number --", '0', 'load_item_desc(this.value,this.id );', '', "", "", "", "", "", "", "cboPoNo[]");
	}
	exit();
}


if ($action == "load_drop_down_program")
{
	$data = explode("**", $data);
	if($data[3] != 1){
		$po_sql = sql_select("select a.booking_no, a.po_break_down_id from wo_booking_dtls a where a.booking_no = '$data[0]' and a.status_active=1 and a.is_deleted = 0 group by a.booking_no, a.po_break_down_id");
		foreach ($po_sql as $val)
		{
			$po_id_arr[$val[csf("po_break_down_id")]] = $val[csf("po_break_down_id")];
		}

		$po_id = implode(",",array_filter($po_id_arr));
	}else{
		$po_id = trim($data[4]);
	}
	$sql ="";
	if($po_id)
	{
		$fabric_source = return_field_value("dyeing_fin_bill", "variable_settings_production", "company_name=$data[2] and variable_list=44 and is_deleted=0 and status_active=1");
		if ($fabric_source == 3) {
			$sql = "select b.program_no program_id,b.program_no from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form in(16,61) and d.trans_type=2 and d.po_breakdown_id in($po_id) and c.entry_form in(16,61) and b.status_active=1 and b.is_deleted=0  and b.program_no!=0 group by b.program_no order by b.program_no ";
		} else if ($fabric_source == 1) {
			$sql = "select b.program_no program_id,b.program_no from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form in(58,22) and d.trans_type=1 and d.po_breakdown_id in($po_id) and c.booking_without_order=0 and c.receive_basis=10 and c.entry_form in(58,22) and b.status_active=1 and b.is_deleted=0  group by b.program_no order by b.program_no";
		} else {
			$sql = "select b.program_no program_id,b.program_no from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=2 and d.trans_type=1 and c.receive_basis=2 and d.po_breakdown_id in($po_id) and c.booking_without_order=0 and c.receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0  group by b.program_no order by b.program_no";
		}
	}
	echo create_drop_down("cboProgramNo_1", 80, $sql, "program_id,program_no", 1, "-- Select --", '0', 'load_item_desc(this.value,this.id );add_self_attr(this);', '', "", "", "", "", "", "", "cboProgramNo[]");
	exit();
}

if ($action == "load_drop_down_program_against_po")
{
	$data = explode("**", $data);
	$po_id = $data[0];
	$row_no = $data[1];

	echo create_drop_down("cboProgramNo_" . $row_no, 80, "SELECT b.id as program_id, b.id as program_no FROM ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b WHERE a.dtls_id=b.id and a.po_id='$po_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "program_id,program_no", 1, "-- Select --", '0', 'load_item_desc(this.value,this.id );', '', "", "", "", "", "", "", "cboProgramNo[]");
	exit();
}

if ($action == "load_drop_down_item_desc") {
	$data = explode("**", $data);
	$po_id = $data[0];
	$row_no = $data[1];
	$booking_without_order = $data[2];
	$program_no = $data[3];
	$batch_maintained = $data[4];
	$fabric_source = $data[5];
//echo $batch_maintained.'='.$fabric_source;
	if ($batch_maintained == 0) {
		if ($booking_without_order == 1) {
			$sql = "select a.id, a.product_name_details from product_details_master a, inv_grey_fabric_issue_dtls b, inv_issue_master c where a.id=b.prod_id and b.mst_id=c.id and c.booking_no='$po_id' and c.issue_basis=1 and c.issue_purpose=8 and c.entry_form in(16) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";
		} else {
			if ($program_no > 0) {
				
				//$sql="select a.id, a.product_name_details from product_details_master a, inv_grey_fabric_issue_dtls b, inv_issue_master c, order_wise_pro_details d where a.id=b.prod_id and b.mst_id=c.id and a.id=d.prod_id and d.entry_form=16 and d.trans_type=2 and b.program_no=$program_no and d.po_breakdown_id=$po_id and c.issue_basis=3 and c.entry_form=16 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";
				//$sql = "select a.id, a.product_name_details from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=16 and d.trans_type=2 and b.program_no=$program_no and d.po_breakdown_id=$po_id and c.issue_basis=3 and c.entry_form=16 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";

				if ($fabric_source == 3) {
					 $sql = "select a.id, a.product_name_details from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form in(16,61) and d.trans_type=2 and b.program_no=$program_no and c.entry_form in(16,61) and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id=$po_id group by a.id, a.product_name_details";//
				} else if ($fabric_source == 1) {
					 $sql = "select a.id, a.product_name_details from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form in(58,22) and d.trans_type=1 and c.booking_id=$program_no and c.booking_without_order=0 and c.receive_basis=10 and c.entry_form in(58,22) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";// and d.po_breakdown_id=$po_id
				} else {
					 $sql = "select a.id, a.product_name_details from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=2 and d.trans_type=1 and c.booking_id=$program_no and c.receive_basis=2 and d.po_breakdown_id=$po_id and c.booking_without_order=0 and c.receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 group by  a.id, a.product_name_details";
				}
			} else {
				$sql = "select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.entry_form in(16) and b.trans_type in(2) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id=$po_id group by a.id, a.product_name_details";//
			}
		}
	} else {
		if ($booking_without_order == 1) {
			$sql = "select a.id, a.product_name_details from product_details_master a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and c.booking_no='$po_id' and c.booking_without_order=1 and c.entry_form in(2,22) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";
		} else {
			if ($program_no > 0) {
				if ($fabric_source == 3) {
					 $sql = "select a.id, a.product_name_details from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form in(16,61) and d.trans_type=2 and b.program_no=$program_no and c.entry_form in(16,61) and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id=$po_id group by a.id, a.product_name_details";//
				} else if ($fabric_source == 1) {
					$sql = "select a.id, a.product_name_details from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form in(58,22) and d.trans_type=1 and c.booking_id=$program_no and c.booking_without_order=0 and c.receive_basis=10 and c.entry_form in(58,22) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";// and d.po_breakdown_id=$po_id
				} else {
					$sql = "select a.id, a.product_name_details from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=2 and d.trans_type=1 and c.booking_id=$program_no and c.receive_basis=2 and c.booking_without_order=0 and c.receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 group by  a.id, a.product_name_details";// and d.po_breakdown_id=$po_id
				}
			} else {
				$sql = "select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.entry_form in(2,13,22) and b.trans_type in(1,5) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id=$po_id group by a.id, a.product_name_details";//
			}
		}
	}

	echo create_drop_down("cboItemDesc_" . $row_no, 180, $sql, 'id,product_name_details', 1, "-- Select Item Desc --", '0', 'load_body_part(this.value,this.id );', '', "", "", "", "", "", "", "cboItemDesc[]");
	exit();
}

if ($action == "load_drop_down_body_part") {
	$data = explode("**", $data);
	$booking_po_id = $data[0];
	$row_no = $data[1];
	$booking_without_order = $data[2];
	$prod_id = $data[3];

	$body_part_ids = '';
	if ($booking_without_order == 1) {
		$sql = "select b.body_part_id from pro_grey_prod_entry_dtls b, inv_receive_master c where b.mst_id=c.id and c.booking_no='$booking_po_id' and c.booking_without_order=1 and c.entry_form in(2,22) and b.status_active=1 and b.is_deleted=0 and b.prod_id=$prod_id group by b.body_part_id";
	} else {
		$sql = "select a.body_part_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and b.trans_type=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id=$prod_id group by a.body_part_id"; //  and b.po_breakdown_id='$booking_po_id'
	}

	$result = sql_select($sql);
	foreach ($result as $row) {
		$body_part_ids .= $row[csf('body_part_id')] . ",";
	}

	$body_part_ids = chop($body_part_ids, ',');
	if ($body_part_ids == "") {
		$body_part_ids = 0;
	}

	echo create_drop_down("cboBodyPart_" . $row_no, 120, $body_part, "", 1, "-- Select --", 0, '', 0, $body_part_ids, "", "", "", "", "", "cboBodyPart[]");
	exit();
}

if ($action == "roll_popup") {
	echo load_html_head_contents("Roll Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>
     function js_set_value(data) {
     	var data = data.split("_");
     	$('#hidden_roll_table_id').val(data[0]);
     	$('#hidden_roll_no').val(data[1]);
     	$('#hidden_roll_qnty').val(data[2]);
     	parent.emailwindow.hide();
     }
 </script>
</head>
<body>
	<div align="center" style="width:550px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:100%; margin-left:20px">
				<input type="hidden" name="hidden_roll_table_id" id="hidden_roll_table_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_roll_no" id="hidden_roll_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_roll_qnty" id="hidden_roll_qnty" class="text_boxes" value="">
				<?

				$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
				$po_arr = array();
				$po_buyer_arr = array();
				$sql_po = sql_select("select b.id, b.po_number, a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");

				foreach ($sql_po as $row) {
					$po_arr[$row[csf('id')]] = $row[csf('po_number')];
					$po_buyer_arr[$row[csf('id')]] = $buyer_arr[$row[csf('buyer_name')]];
				}

				$sql = "select a.id, a.po_breakdown_id, a.roll_no, a.qnty from pro_roll_details a, inv_receive_master b where a.mst_id=b.id and b.company_id=$cbo_company_id and a.entry_form=1 and a.roll_no>0 and a.status_active=1 and a.is_deleted=0";

				$po_arr = return_library_array("select id, po_number from wo_po_break_down", 'id', 'po_number');
				$arr = array(0 => $po_arr, 1 => $po_buyer_arr);

				echo create_list_view("tbl_list_search", "Order Number,Buyer Name,Roll No,Roll Qnty", "130,120,80", "510", "280", 0, $sql, "js_set_value", "id,roll_no,qnty", "", 1, "po_breakdown_id,po_breakdown_id,0,0", $arr, "po_breakdown_id,po_breakdown_id,roll_no,qnty", "", "setFilterGrid('tbl_list_search',-1)", '0,0,0,2', '');

				?>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
if ($action == "po_selection_popup") {
	echo load_html_head_contents("PO Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	$distribiution_method = array(1 => "Distribute Based On Lowest Shipment Date", 2 => "Manually");
	?>
	<script>
     function js_set_value(data) {
     	var data = data.split("_");
     	$('#hidden_roll_table_id').val(data[0]);
     	$('#hidden_roll_no').val(data[1]);
     	$('#hidden_roll_qnty').val(data[2]);
     	parent.emailwindow.hide();
     }
	 function distribute_qnty(str) {
				if (str == 1) {
					$('#txt_prop_batch_qnty').attr('disabled', false);
					var txt_prop_batch_qnty = $('#txt_prop_batch_qnty').val() * 1;
					var tot_po_qty = $('#tot_po_qty').val() * 1;
				//	alert(tot_po_qty);
					var tblRow = $("#tbl_list_search tr").length;
					var balance = txt_prop_batch_qnty;
					var len = totalGrey = 0;

					if (txt_prop_batch_qnty > 0) {
						var tot_yarn_bl_qty = 0;
						$("#tbl_list_search").find('tr').each(function () {
							//var balance_qty = $(this).find('input[name="balanceQty[]"]').val();
							//tot_yarn_bl_qty = tot_yarn_bl_qty * 1 + balance_qty * 1;
						});

						if (txt_prop_batch_qnty > tot_po_qty) {
							alert("Total Batch Qty Exceeds Total PO Qty");
							$('#txt_prop_batch_qnty').val('');

							$("#tbl_list_search").find('tr').each(function () {
								$(this).find('input[name="txtBatchQnty[]"]').val('');
							});
							calculate_tot_qnty();
							return;
						}
					}

					$("#tbl_list_search").find('tr').each(function () {
						len = len + 1;

						//var txtOrginal = $(this).find('input[name="txtOrginal[]"]').val() * 1;
						var isDisbled = $(this).find('input[name="txtBatchQnty[]"]').is(":disabled");
						var placeholder_value = $(this).find('input[name="txtBatchQnty[]"]').attr('placeholder') * 1;

						 if (isDisbled == false) {
							if (balance > 0) {
								if (placeholder_value < 0) placeholder_value = 0;
								if (balance > placeholder_value) {
									var grey_qnty = placeholder_value;
									balance = balance - placeholder_value;
								}
								else {
									var grey_qnty = balance;
									balance = 0;
								}

								if (tblRow == len) {
									var grey_qnty = txt_prop_batch_qnty - totalGrey;
								}

								totalGrey = totalGrey * 1 + grey_qnty * 1;

								$(this).find('input[name="txtBatchQnty[]"]').val(grey_qnty.toFixed(2));
							}
							else {
								$(this).find('input[name="txtBatchQnty[]"]').val('');
							}
						}
					});
				}
				else {
					$('#txt_prop_batch_qnty').val('');
					$('#txt_prop_batch_qnty').attr('disabled', true);
					//alert(1);
					$("#tbl_list_search").find('tr').each(function () {
						$('input[name="txtBatchQnty"]').prop('disabled', true);
					//	$(this).find('input[name="txtBatchQnty[]"]').is(":disabled");
						if ($(this).find('input[name="txtBatchQnty[]"]').is(":disabled") == false) {
							//$(this).find('input[name="txtBatchQnty[]"]').val('');
						}
					});
				}

				calculate_tot_qnty();
			}
	function calculate_tot_qnty() {
		var tot_batch_qnty = '';

		$("#tbl_list_search").find('tr').each(function () {
			var txtBatchQnty = $(this).find('input[name="txtBatchQnty[]"]').val() * 1;
			tot_batch_qnty = tot_batch_qnty * 1 + txtBatchQnty * 1;
     	});
            $('#txt_tot_batch_qnty').val(tot_batch_qnty.toFixed(2));
		}
	function fn_check_balance(rowNo)
	{
			var placeholder_value = $(this).find('input[name="txtBatchQnty[]"]').attr('placeholder') * 1;
			var txtPoQnty = '';
			var BatchQnty = '';
				//var txtPoQnty = $(this).find('input[name="txtPoQnty[]"]').val() * 1;
				//var BatchQnty = $(this).find('input[name="txtBatchQnty[]"]').val() * 1;
			BatchQnty = $('#txtBatchQnty_' + rowNo).val() * 1;
			txtPoQnty = $('#txtPoQnty_' + rowNo).val() * 1;
		//alert(txtPoQnty+'='+BatchQnty);
			if (BatchQnty > txtPoQnty) {
				alert("Batch Qty. Exceeds PO Qty.");
				$('#txtBatchQnty_' + rowNo).val('');
				return;
			}

	}
	function fnc_close() {
			var save_string = '';
			var tot_BatchQnty = '';
			var tot_grey_qnty_pcs = '';
			var tot_reject_qnty = '';
			var size_qnty_string = '';
			var no_of_roll = '';
			var po_id_array = new Array();
			var rfid_array = new Array();
			var breakOut = true;
			var diplicat_rfid = 0;

		$("#tbl_list_search").find('tr').each(function () {

				var txtPoId = $(this).find('input[name="txtPoId[]"]').val();
				var txtBatchQnty = $(this).find('input[name="txtBatchQnty[]"]').val();

				var txtPoQnty = $(this).find('input[name="txtPoQnty[]"]').val();
				var txtPoNo = $(this).find('input[name="txtPoNo[]"]').val();
				var porowid = $(this).find('input[name="porowid[]"]').val();

				tot_BatchQnty 		= txtBatchQnty * 1 + txtBatchQnty * 1;
				txtPoQnty 	= txtPoQnty * 1 + txtPoQnty * 1;



				if (txtBatchQnty * 1 > 0) {
					if (save_string == "") {
						save_string = txtPoId + "**" + txtBatchQnty + "**" + txtPoQnty+ "**" + txtPoNo+ "**" + porowid;
					}
					else {
						save_string += "!!" + txtPoId + "**" + txtBatchQnty + "**" + txtPoQnty+ "**" + txtPoNo+ "**" + porowid;
					}
					if (jQuery.inArray(txtPoId, po_id_array) == -1) {
						po_id_array.push(txtPoId);
					}
				}
				//alert(save_string);
			});
			$('#save_string').val(save_string);
			parent.emailwindow.hide();//36630**50000.00**100000**75872#36609**6666.00**100000**5451-992
	}


 </script>
</head>
<body>
<?

	//echo $posavestring.'ddd';;
	if($posavestring!="")
	{
		$posave_string=explode("!!",$posavestring);
		$po_qty_arr=array();
		foreach($posave_string as $po_wise)
		{
			$po_data=explode("**",$po_wise);
			//echo $po_data[0].'='.$po_data[1].',';
			$po_qty_arr[$po_data[0]]=$po_data[1];
			$total_disbute_qty+=$po_data[1];
		}
	}
//	$program_no;$booking_without_order;$row_id;$fabric_source;$batch_maintained; 
if ($batch_maintained == 0) {
		if ($booking_without_order == 1) {
			$sql = "select a.id, a.product_name_details,c.booking_no as po_id from product_details_master a, inv_grey_fabric_issue_dtls b, inv_issue_master c where a.id=b.prod_id and b.mst_id=c.id and c.booking_no in($po_no_id) and c.issue_basis=1 and c.issue_purpose=8 and c.entry_form in(16) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details,c.booking_no";
		} else {
			if ($program_no > 0) {
				//$sql="select a.id, a.product_name_details from product_details_master a, inv_grey_fabric_issue_dtls b, inv_issue_master c, order_wise_pro_details d where a.id=b.prod_id and b.mst_id=c.id and a.id=d.prod_id and d.entry_form=16 and d.trans_type=2 and b.program_no=$program_no and d.po_breakdown_id=$po_id and c.issue_basis=3 and c.entry_form=16 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";
				//$sql = "select a.id, a.product_name_details from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=16 and d.trans_type=2 and b.program_no=$program_no and d.po_breakdown_id=$po_id and c.issue_basis=3 and c.entry_form=16 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";

				if ($fabric_source == 3) {
					 $sql = "select a.id, a.product_name_details,b.program_no, d.po_breakdown_id as po_id from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form in(16,61) and d.trans_type=2 and b.program_no=$program_no and c.entry_form in(16,61) and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id in($po_no_id) group by a.id, a.product_name_details,b.program_no,d.po_breakdown_id";//
				} else if ($fabric_source == 1) {
					$sql = "select a.id, a.product_name_details from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form in(58,22) and d.trans_type=1 and c.booking_id=$program_no and c.booking_without_order=0 and c.receive_basis=10 and c.entry_form in(58,22) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";// and d.po_breakdown_id=$po_id
				} else {
					$sql = "select a.id, a.product_name_details from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=2 and d.trans_type=1 and c.booking_id=$program_no and c.receive_basis=2 and d.po_breakdown_id in($po_no_id) and c.booking_without_order=0 and c.receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 group by  a.id, a.product_name_details";
				}
			} else {
				$sql = "select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.entry_form in(16) and b.trans_type in(2) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($po_no_id) group by a.id, a.product_name_details";//
			}
		}
	} else {
		if ($booking_without_order == 1) {
			$sql = "select a.id, a.product_name_details from product_details_master a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and c.booking_no in($po_no_id) and c.booking_without_order=1 and c.entry_form in(2,22) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";
		} else {
			if ($program_no > 0) {
				if ($fabric_source == 3) {
				 $sql = "select a.id, a.product_name_details,b.program_no,d.po_breakdown_id as po_id from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form in(16,61) and d.trans_type=2 and b.program_no=$program_no and c.entry_form in(16,61) and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id in($po_no_id) group by a.id, a.product_name_details,b.program_no,d.po_breakdown_id";//
				} else if ($fabric_source == 1) {
					$sql = "select a.id, a.product_name_details,c.booking_id from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form in(58,22) and d.trans_type=1 and c.booking_id=$program_no and c.booking_without_order=0 and c.receive_basis=10 and c.entry_form in(58,22) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details,c.booking_id";// and d.po_breakdown_id=$po_id
				} else {
					$sql = "select a.id, a.product_name_details,c.booking_id from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=2 and d.trans_type=1 and c.booking_id=$program_no and c.receive_basis=2 and c.booking_without_order=0 and c.receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 group by  a.id, a.product_name_details,c.booking_id";// and d.po_breakdown_id=$po_id
				}
			} else {
				$sql = "select a.id, a.product_name_details,b.po_breakdown_id as po_id from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.entry_form in(2,13,22) and b.trans_type in(1,5) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($po_no_id) group by a.id, a.product_name_details,b.po_breakdown_id";//
			}
		}
	}
		//echo $sql;
		$fab_des = sql_select($sql);
		$fab_des_arr=array();
		foreach($fab_des as $row)
		{
			if ($program_no > 0) {
				if ($fabric_source == 3) {
				$fab_des_arr[$row[csf('program_no')]][$row[csf('po_id')]]=$row[csf('product_name_details')];
				}
				else if ($fabric_source == 1) {
					$fab_des_arr[$row[csf('program_no')]]=$row[csf('product_name_details')];
				}
			}
			else
			{
				$fab_des_arr[$row[csf('po_id')]]=$row[csf('product_name_details')];
			}
			$prod_id.=$row[csf('id')].',';
		}
		//print_r($fab_des_arr);
		//print '<pre>';
		//echo $prod_ids.'d';
		$prod_ids=rtrim($prod_id,',');
		if ($booking_without_order == 1) {
		$sql_body = "select b.body_part_id,b.prod_id,c.booking_no from pro_grey_prod_entry_dtls b, inv_receive_master c where b.mst_id=c.id and c.booking_no='$program_no' and c.booking_without_order=1 and c.entry_form in(2,22) and b.status_active=1 and b.is_deleted=0 and b.prod_id in($prod_ids) group by b.body_part_id,c.booking_no,b.prod_id";
	} else {
		  $sql_body = "select a.body_part_id,b.prod_id,b.po_breakdown_id as po_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and b.trans_type=1 and b.status_active=1 and b.is_deleted=0  and b.po_breakdown_id in($po_no_id)  group by a.body_part_id,b.prod_id,b.po_breakdown_id"; //  and b.po_breakdown_id='$booking_po_id'
	}
		$body_result = sql_select($sql_body);
		foreach($body_result as $row)
		{
			//echo $booking_without_order.'='.'DDD';
			if ($booking_without_order == 1) {
			$fab_body_arr[$row[csf('po_id')]]=$row[csf('body_part_id')];
			}
			else {
				$fab_body_arr[$row[csf('po_id')]]=$row[csf('body_part_id')];
				//echo $row[csf('body_part_id')].'dd'.$row[csf('po_id')];
			}
		}


	$sql_po = "select a.job_no, a.style_ref_no, a.order_uom, b.id as po_id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($po_no_id) ";
	//$disable="disabled";
	?>
	<div style="margin-left:5px; margin-top:10px">
    <div align="center">
	<table style="margin-bottom:2px" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
        <thead>
            <th>Total Batch Qty.</th>
            <th>Distribution Method</th>
        </thead>
        <tr class="general">
            <td><input type="text" name="txt_prop_batch_qnty" id="txt_prop_batch_qnty" class="text_boxes_numeric" value="<? echo $total_disbute_qty;?>"
                style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?> >
                <input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
            </td>
            <td>
                <?
                echo create_drop_down("cbo_distribiution_method", 250, $distribiution_method, "", 0, "", $prev_distribution_method, "distribute_qnty(this.value);", $disabled_dropdown);
                ?>
            </td>
        </tr>
		</table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">

			<thead>
				<th width="30">SL</th>
				<th width="100">PO No</th>
                <th width="100">PO Qty.</th>

				<th width="350">Item Desc.</th>
				<th width="120">Body Part</th>
				<th width="">Batch Qnty</th>
			</thead>
		</table>
		<div style="width:900px; overflow-y:scroll; max-height:480px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="tbl_list_search">
           <tbody>
			<?
			$i = 1;
			$nameArray = sql_select($sql_po);
			foreach ($nameArray as $selectResult) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				if ($program_no > 0) {
				if ($fabric_source == 3) {
					//echo $program_no.'='.$selectResult[csf('po_id')].'<br>';
					$fab_des=$fab_des_arr[$program_no][$selectResult[csf('po_id')]];
				}
				else if ($fabric_source == 1) {
					$fab_des=$fab_des_arr[$program_no];
				}
			}
			else
			{
				$fab_des=$fab_des_arr[$selectResult[csf('po_id')]];
			}

			if ($booking_without_order == 1) {
			$fab_body=$fab_body_arr[$selectResult[csf('po_id')]];
			}
			else {
				$fab_body=$fab_body_arr[$row[csf('po_id')]];
			}

				?>
				<tr bgcolor="<? echo $bgcolor; ?>"  id="tr_<? echo $i; ?>">
					<td width="30" align="center"><? echo $i; ?></td>
                    <td width="100"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                    <td width="100" align="right"><p><? echo $selectResult[csf('po_qnty_in_pcs')]; ?>
                    <input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $selectResult[csf('po_qnty_in_pcs')]; ?>">
                    <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $selectResult[csf('po_id')]; ?>">
                    <input type="hidden" name="txtPoNo[]" id="txtPoNo_<? echo $i; ?>" value="<? echo $selectResult[csf('po_number')]; ?>">
                     <input type="hidden" name="porowid[]" id="porowid_<? echo $i; ?>" value="<? echo $i; ?>">
                    </p></td>

                    <td width="350"><p><? echo $fab_des; ?></p></td>
                    <td width="120"><p><? echo $body_part[$fab_body]; ?></p></td>
					<td width="" align="center">
						<p>
                        <?
						if($posavestring!="")
						{
                       		if($po_qty_arr[$selectResult[csf('po_id')]]>0)
							{
								$po_batch_qty=$po_qty_arr[$selectResult[csf('po_id')]];
							}
							else $po_batch_qty="";

						}
						else
						{
							$po_batch_qty='';
						}
                        ?>
                        <input type="text" name="txtBatchQnty[]" id="txtBatchQnty_<? echo $i; ?>" class="text_boxes_numeric"
	                                   	placeholder="<?  echo number_format($selectResult[csf('po_qnty_in_pcs')], 2, '.', ''); ?>" value="<? echo $po_batch_qty;?>" style="width:100px" <? echo $disable; ?>
	                        onKeyUp="fn_check_balance(<? echo $i; ?>);calculate_tot_qnty();" >
                       </p></td>
					</tr>
					<?
					$i++;
					$tot_po_batch_qty+= $po_batch_qty;
					$tot_po_qty+= $selectResult[csf('po_qnty_in_pcs')];
				}
				?>
                </tbody>

			</table>
             <table width="880" border="1" cellpadding="0" cellspacing="0"   class="rpt_table" rules="all">
                    <tr bgcolor="#CCCCCC">
                    <td width="30">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100" align="">
                    <input type="text" name="tot_po_qty" id="tot_po_qty" class="text_boxes_numeric"  style="width:80px;" value="<? echo number_format($tot_po_qty, 2, '.', '');?>" disabled=""></td>
                    <td width="350">&nbsp;  </td>
                     <td width="120"> Total</td>
                    <td align="center"> <input type="text" name="txt_tot_batch_qnty" id="txt_tot_batch_qnty" class="text_boxes_numeric" style="width:100px; text-align:center"  value="<? echo number_format($tot_po_batch_qty, 2, '.', '');?>" disabled="">
                    </td>
                    </tr>

       		 </table>
             </div>
           <table width="880" border="1" cellpadding="0" cellspacing="0" rules="all">

                <td align="center" >
                    <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                </td>
            </tr>
        </table>
		</div>
	</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
	<?
	exit();
}


if ($action == "po_popup") {
	echo load_html_head_contents("Order Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);

	?>
	<script>
		var job_no = '';
		var hide_job_no = '<? echo $hide_job_no; ?>';
		var no_of_row =<? echo $no_of_row; ?>;

		function js_set_value(po_id, po_no, job_no) {
			if (no_of_row > 1 && hide_job_no != "") {
				if (job_no != hide_job_no) {
					alert("Job Mix Not Allowed");
					return;
				}
			}

			document.getElementById('po_id').value = po_id;
			document.getElementById('po_no').value = po_no;
			document.getElementById('job_no').value = job_no;
			parent.emailwindow.hide();
		}

	</script>

</head>

<body>
	<fieldset style="width:620px;margin-left:10px">
		<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
			<table cellpadding="0" cellspacing="0" width="620" class="rpt_table">
				<thead>
					<th>Buyer</th>
					<th>Search By</th>
					<th>Search</th>
					<th>
						<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton"/>
						<input type="hidden" name="po_id" id="po_id" value="">
						<input type="hidden" name="po_no" id="po_no" value="">
						<input type="hidden" name="job_no" id="job_no" value="">
					</th>
				</thead>
				<tr class="general">
					<td align="center">
						<?
						if ($batch_against == 5) {
							echo create_drop_down("cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", 0);
						} else {
							echo create_drop_down("cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", 0);
						}
						?>
					</td>
					<td align="center">
						<?
						$search_by_arr = array(1 => "PO No", 2 => "Job No");
						echo create_drop_down("cbo_search_by", 170, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
						?>
					</td>
					<td align="center">
						<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
						id="txt_search_common"/>
					</td>
					<td align="center">
						<input type="button" name="button2" class="formbutton" value="Show"
						onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+<? echo $batch_against; ?>, 'create_po_search_list_view', 'search_div', 'batch_creation_controller_v2', 'setFilterGrid(\'tbl_list_search\',-1);')"
						style="width:100px;"/>
					</td>
				</tr>
			</table>
			<div id="search_div" style="margin-top:10px"></div>
		</form>
	</fieldset>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action == "create_po_search_list_view") {
	$data = explode('_', $data);

	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$buyer_id = $data[3];
	$batch_against = $data[4];

	if ($batch_against == 5) {
		if ($search_by == 1)
			$search_field = 'b.po_number';
		else
			$search_field = 'a.job_no';
	} else if ($batch_against == 4) {
		if ($search_by == 1)
			$search_field = 'b.order_no';
		else
			$search_field = 'a.subcon_job';
	}

	if ($buyer_id == 0) {
		echo "Please Select Buyer First.";
		die;
	}

	if ($batch_against == 5) {
		$sql = "select a.job_no, a.style_ref_no, a.order_uom, b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name=$buyer_id and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	} else if ($batch_against == 4) {
		$sql = "select a.subcon_job as job_no, b.id, b.cust_style_ref as style_ref_no, b.order_uom, b.order_no as po_number, b.order_quantity as po_qnty_in_pcs, b.delivery_date as pub_shipment_date from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.company_id=$company_id and a.party_id=$buyer_id and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	}

	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="100">Job No</th>
				<th width="110">Style No</th>
				<th width="110">PO No</th>
				<th width="90">PO Quantity</th>
				<th width="50">UOM</th>
				<th><? if ($batch_against == 5) echo "Shipment"; else if ($batch_against == 4) echo "Delivery"; ?> Date</th>
			</thead>
		</table>
		<div style="width:618px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table"
			id="tbl_list_search">
			<?
			$i = 1;
			$nameArray = sql_select($sql);
			foreach ($nameArray as $selectResult) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					id="search<? echo $i; ?>"
					onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('po_number')]; ?>','<? echo $selectResult[csf('job_no')]; ?>')">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="100"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
					<td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
					<td width="110"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
					<td width="90" align="right"><? echo $selectResult[csf('po_qnty_in_pcs')]; ?></td>
					<td width="50" align="center">
						<p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
						<td align="center"><? echo change_date_format($selectResult[csf('pub_shipment_date')]); ?></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
	</div>
	<?
	exit();
}

if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$product_array 	= return_library_array("select id, product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
	$color_arr 		= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$po_batch_no_arr = array();
	$all_po_ids 	 = rtrim(str_replace("'", "", $all_po_ids),", ");
	//echo "10**select max(a.po_batch_no) as po_batch_no, a.po_id, b.color_id from pro_batch_create_dtls a, pro_batch_create_mst b where a.mst_id=b.id and a.po_id in($all_po_ids) group by b.color_id, a.po_id";die;
	$po_batch_data   = sql_select("select max(a.po_batch_no) as po_batch_no, a.po_id, b.color_id from pro_batch_create_dtls a, pro_batch_create_mst b where a.mst_id=b.id and a.po_id in($all_po_ids) group by b.color_id, a.po_id");
	foreach ($po_batch_data as $row) {
		$po_batch_no_arr[$row[csf('color_id')]][$row[csf('po_id')]] = $row[csf('po_batch_no')];
	}

	if (str_replace("'", "", $txt_ext_no) != "" || $db_type == 0) {
		$extention_no_cond  = "extention_no=$txt_ext_no";
		$extention_no_cond2 = "and batch_ext_no=$txt_ext_no";
	} else {
		$extention_no_cond  = "extention_no is null";
		$extention_no_cond2 = "and batch_ext_no is null";
	}

	if ($db_type == 0) {
		$extention_no_cond_valid = " and a.extention_no=0";

	} else {
		$extention_no_cond_valid = " and a.extention_no is null";
	}

	$fabric_source = return_field_value("dyeing_fin_bill", "variable_settings_production", "company_name =$cbo_company_id and variable_list=44 and is_deleted=0 and status_active=1");
	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$batch_update_id 	= '';
		$batch_no_creation 	= str_replace("'", "", $batch_no_creation);
		$roll_maintained 	= str_replace("'", "", $roll_maintained);
		$txt_search_type 	= str_replace("'", "", $txt_search_type);
		$ready_to_approved 	= str_replace("'", "", $cbo_ready_to_approved);

		$color_id = return_id($txt_batch_color, $color_arr, "lib_color", "id,color_name");

		if (str_replace("'", "", $update_id) == "") {
			$id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);

			$batch_update_id = $id;
			$serial_no = date("y", strtotime($pc_date_time)) . "-" . $id;

			if ($batch_no_creation == 1) {
				$txt_batch_number = "'" . $id . "'";
			} else {
				if (is_duplicate_field("batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_number and $extention_no_cond and status_active=1 and is_deleted=0") == 1) {
					disconnect($con);
					echo "11**0";
					die;
				}

				$txt_batch_number = $txt_batch_number;
			}
			//cbo_machine_name
			$field_array = "id, batch_no, batch_date, batch_against, batch_for, company_id,working_company_id, booking_no_id, booking_no, booking_without_order, extention_no, color_id, batch_weight, total_trims_weight,save_string, color_range_id, process_id, organic, dur_req_hr, dur_req_min, collar_qty, cuff_qty,floor_id, dyeing_machine, remarks,ready_to_approved, inserted_by, insert_date,sales_order_no,sales_order_id,is_sales,process_seq,booking_entry_form,service_booking_id,service_booking_no,double_dyeing";
			$sales_order_no = ($txt_search_type == 7) ? $txt_booking_no : "''";
			$txt_booking_no = ($txt_search_type != 7) ? $txt_booking_no : $txt_sales_booking_no;
			$txt_sales_id = ($txt_search_type == 7) ? str_replace("'", "", $txt_sales_id) : "''";
			$is_sales = ($txt_search_type == 7) ? 1 : 0;
			$data_array = "(" . $id . "," . $txt_batch_number . "," . $txt_batch_date . "," . $cbo_batch_against . "," . $cbo_batch_for . "," . $cbo_company_id . "," . $cbo_working_company_id . "," . $txt_booking_no_id . "," . $txt_booking_no . "," . $booking_without_order . "," . $txt_ext_no . "," . $color_id . "," . $txt_batch_weight . "," . $txt_tot_trims_weight . "," . $save_data . "," . $cbo_color_range . "," . $txt_process_id . "," . $txt_organic . "," . $txt_du_req_hr . "," . $txt_du_req_min . "," . $txt_color_qty . "," . $txt_cuff_qty . "," . $cbo_floor . "," . $cbo_machine_name . "," . $txt_remarks . "," . $ready_to_approved . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $sales_order_no . "," . $txt_sales_id . "," . $is_sales . "," . $txt_process_seq .",". $hidden_booking_entry_form .",". $service_booking_id .",". $txt_service_booking.",". $cbo_double_dyeing .")";

		} else {
			$batch_update_id = str_replace("'", "", $update_id);
			$serial_no = str_replace("'", "", $txt_batch_sl_no);

			if ($batch_no_creation != 1) {
				if (is_duplicate_field("batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_number and $extention_no_cond and id<>$update_id and status_active=1 and is_deleted=0") == 1) {
					disconnect($con);
					echo "11**0";
					die;
				}
			}

			$field_array_update = "batch_no*batch_date*batch_against*batch_for*company_id*working_company_id*booking_no_id*booking_no*booking_without_order*extention_no*color_id*batch_weight*total_trims_weight*save_string*color_range_id*process_id*organic*dur_req_hr*dur_req_min*collar_qty*cuff_qty*floor_id*dyeing_machine*remarks*ready_to_approved*process_seq*booking_entry_form*updated_by*update_date*service_booking_id*service_booking_no*double_dyeing";

			$data_array_update = $txt_batch_number . "*" . $txt_batch_date . "*" . $cbo_batch_against . "*" . $cbo_batch_for . "*" . $cbo_company_id . "*" . $cbo_working_company_id . "*" . $txt_booking_no_id . "*" . $txt_booking_no . "*" . $booking_without_order . "*" . $txt_ext_no . "*" . $color_id . "*" . $txt_batch_weight . "*" . $txt_tot_trims_weight . "*" . $save_data . "*" . $cbo_color_range . "*" . $txt_process_id . "*" . $txt_organic . "*" . $txt_du_req_hr . "*" . $txt_du_req_min . "*" . $txt_color_qty . "*" . $txt_cuff_qty . "*" . $cbo_floor . "*" . $cbo_machine_name . "*" . $txt_remarks ."*" . $ready_to_approved ."*" . $txt_process_seq. "*". $hidden_booking_entry_form ."*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*".$service_booking_id."*".$txt_service_booking."*".$cbo_double_dyeing;
		}

		$field_array_dtls = "id, mst_id, program_no, po_id, po_batch_no, prod_id, item_description, body_part_id, width_dia_type, roll_no, roll_id,barcode_no,batch_qnty, inserted_by, insert_date,is_sales";
		$field_array_roll = "id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, barcode_no, booking_no, booking_without_order, inserted_by, insert_date,is_sales";
		$roll_table_id = '';

		for ($i = 1; $i <= $total_row; $i++) {
			if (str_replace("'", "", $cbo_batch_against) == 5) {
				$po_id = "poId_" . $i;
			} else {
				$po_id = "cboPoNo_" . $i;
			}
			$id_dtls = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

			$program_no = "cboProgramNo_" . $i;
			$prod_id = "cboItemDesc_" . $i;
			$body_part_id = "cboBodyPart_" . $i;
			$txtRollNo = "txtRollNo_" . $i;
			$hideRollNo = "hideRollNo_" . $i;
			$txtBatchQnty = "txtBatchQnty_" . $i;
			$cboDiaWidthType = "cboDiaWidthType_" . $i;
			$barcodeNo = "barcodeNo_" . $i;
			$ItemDesc = $product_array[str_replace("'", "", $$prod_id)];

			$product_id = str_replace("'", "", $$prod_id);
			$po_batch_no = $po_batch_no_arr[$color_id][str_replace("'", "", $$po_id)] + 1;
			if($cbo_batch_against != 2)
			{
				if($roll_maintained != 1)
				{
					if (str_replace("'", "", $$program_no) > 0)
					{
						if ($fabric_source == 3)
						{
							$validation_qnty = sql_select("select sum(d.quantity) qnty from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form in(16,61) and d.trans_type=2 and b.program_no=".$$program_no." and d.po_breakdown_id=".$$po_id." and c.entry_form in(16,61) and b.status_active=1 and b.is_deleted=0 and d.prod_id = $product_id");

							//echo "10**"."select sum(b.issue_qnty) qnty from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form in(16,61) and d.trans_type=2 and b.program_no=".$$program_no." and d.po_breakdown_id=".$$po_id." and c.entry_form in(16,61) and b.status_active=1 and b.is_deleted=0 and d.prod_id = $product_id";die;
							$msg = "Batch quantity can not be greater than Issue quantity.\nIssue quantity=".$validation_qnty[0][csf("qnty")];

						}
						else if ($fabric_source == 1)
						{
							//production basis
							$validation_qnty = sql_select("select sum(x.quantity) qnty from (select c.booking_id,d.quantity from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form in(13,83,58,22) and d.trans_type in(1,5) and d.po_breakdown_id=".$$po_id." and c.booking_without_order=0 and c.receive_basis=9 and c.entry_form in(13,83,58,22) and b.status_active=1 and b.is_deleted=0 and d.prod_id = $product_id) x,inv_receive_master y where x.booking_id=y.id and y.booking_id=".$$program_no."");
							if(empty($validation_qnty)){
								$validation_qnty = sql_select("select sum(d.quantity ) qnty from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form in(13,83,58,22) and d.trans_type=1 and c.booking_id=".$$program_no." and d.po_breakdown_id=".$$po_id." and c.booking_without_order=0 and c.receive_basis=10 and c.entry_form in(13,83,58,22) and b.status_active=1 and b.is_deleted=0  and d.prod_id = $product_id");
							}
							$msg = "Batch quantity can not be greater than Grey Receive quantity.\nReceive quantity=".$validation_qnty[0][csf("qnty")];
						}
						else
						{
							$validation_qnty = sql_select("select sum(d.quantity) qnty from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form=2 and d.trans_type=1 and c.booking_id=".$$program_no." and c.receive_basis=2 and d.po_breakdown_id=".$$po_id." and c.booking_without_order=0 and c.receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0  and d.prod_id = $product_id");
							$msg = "Batch quantity can not be greater than Knitting Production quantity.\nProduction quantity=".$validation_qnty[0][csf("qnty")];
						}
					}
					else
					{
						if(str_replace("'", "", $booking_without_order) == 1)
						{
							$validation_qnty = sql_select("select sum(b.issue_qnty) qnty from product_details_master a, inv_grey_fabric_issue_dtls b, inv_issue_master c where a.id=b.prod_id and b.mst_id=c.id and c.booking_no=$txt_booking_no and c.issue_basis=1 and c.issue_purpose=8 and c.entry_form in(16) and b.status_active=1 and b.is_deleted=0 and a.id = $product_id group by a.id, a.product_name_details");
							$msg = "Batch quantity is not available.\nIssue quantity=".$validation_qnty[0][csf("qnty")];
						}
						else
						{
							if ($fabric_source == 3)
							{
								$validation_qnty = sql_select("select sum(d.quantity) qnty from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form in(16,61) and d.trans_type=2  and d.po_breakdown_id=".$$po_id." and c.entry_form in(16,61) and b.status_active=1 and b.is_deleted=0 and d.prod_id = $product_id");

								$msg = "Batch quantity can not be greater than Issue quantity.\nIssue quantity=".$validation_qnty[0][csf("qnty")]."";
							}
							else if ($fabric_source == 1)
							{
								$validation_qnty = sql_select("select sum(x.quantity) qnty from (select c.booking_id,d.quantity from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form in(13,83,58,22) and d.trans_type in(1,5) and d.po_breakdown_id=".$$po_id." and c.booking_without_order=0 and c.receive_basis=9 and c.entry_form in(13,83,58,22) and b.status_active=1 and b.is_deleted=0 and d.prod_id = $product_id) x,inv_receive_master y where x.booking_id=y.id ");
								if(empty($validation_qnty))
								{
									$validation_qnty = sql_select("select sum(d.quantity ) qnty from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form in(13,83,58,22)  and d.trans_type in(1,5) and d.po_breakdown_id=".$$po_id." and c.booking_without_order=0 and c.receive_basis=10 and c.entry_form in(13,83,58,22) and b.status_active=1 and b.is_deleted=0  and d.prod_id = $product_id");
								}
								$msg = "Batch quantity can not be greater than Grey Receive quantity.\nReceive quantity=".$validation_qnty[0][csf("qnty")];
							}
							else
							{
								$validation_qnty = sql_select("select sum(d.quantity) qnty from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form=2 and d.trans_type=1  and c.receive_basis=2 and d.po_breakdown_id=".$$po_id." and c.booking_without_order=0 and c.receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0  and d.prod_id = $product_id");
								$msg = "Batch quantity can not be greater than Knitting Production quantity.\nProduction quantity=".$validation_qnty[0][csf("qnty")];
							}
						}

					}
					$program_cond = (str_replace("'", "", $$program_no) > 0)?" and b.program_no=".$$program_no."":"";
					if($booking_without_order == 0){
						$po_cond = (str_replace("'", "", $$po_id) > 0)?" and b.po_id in(".str_replace("'", "", $$po_id).")":"";
					}else{
						$po_cond = "";
					}
					$total_batch_qnty = return_field_value("sum(b.batch_qnty) total_batch_qnty", "pro_batch_create_mst a,pro_batch_create_dtls b", "a.id=b.mst_id and a.company_id=$cbo_company_id and a.booking_no=$txt_booking_no $program_cond $po_cond $extention_no_cond_valid and b.prod_id = $product_id and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ","total_batch_qnty");

					$tot_batch_qty=str_replace("'", "", $$txtBatchQnty)+$total_batch_qnty;
					if((str_replace("'", "", $$txtBatchQnty)+$total_batch_qnty) > $validation_qnty[0][csf("qnty")])
					{
						disconnect($con);
						echo "17**".$msg;
						//echo "17**".$msg."=".$tot_batch_qty."=".$validation_qnty[0][csf("qnty")]."";
						die;
					}
				}
			}

			$is_sales = ($txt_search_type == 7) ? 1 : 0;
			if (str_replace("'", "", $$hideRollNo) != "") {
				if (str_replace("'", "", $booking_without_order) == 1 && $is_sales != 1) {
					$bookingNo = str_replace("'", "", $txt_booking_no);
					$poId = str_replace("'", "", $txt_booking_no_id);
				} else {
					$bookingNo = '';
					$poId = str_replace("'", "", $$po_id);
				}
				if ($data_array_roll != "") $data_array_roll .= ",";

				$data_array_roll .= "(" . $id_roll . "," . $batch_update_id . "," . $id_dtls . ",'" . $poId . "',64," . $$txtBatchQnty . "," . $$txtRollNo . "," . $$hideRollNo . "," . $$barcodeNo . ",'" . $bookingNo . "'," . $booking_without_order . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $is_sales . ")";
			}

			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$is_sales = ($txt_search_type == 7) ? 1 : 0;
			$data_array_dtls .= "(" . $id_dtls . "," . $batch_update_id . "," . $$program_no . "," . $$po_id . ",'" . $po_batch_no . "'," . $$prod_id . ",'" . $ItemDesc . "'," . $$body_part_id . "," . $$cboDiaWidthType . "," . $$txtRollNo . "," . $$hideRollNo . "," . $$barcodeNo . "," . $$txtBatchQnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $is_sales . ")";
		}

		//$id_dtls_trim = return_next_id("id", "pro_batch_trims_dtls", 1);
		$field_array_dtls_trims = "id,mst_id,item_description,trims_wgt_qnty,remarks,inserted_by, insert_date,status_active,is_deleted";
		$save_string = explode("!!", str_replace("'", "", $save_data));
		for ($i = 0; $i < count($save_string); $i++) {
			$id_dtls_trim = return_next_id_by_sequence("PRO_BATCH_TRIMS_DTLS_PK_SEQ", "pro_batch_trims_dtls", $con);
			$data = explode("_", $save_string[$i]);
			$item_des = $data[0];
			$trims_qty = $data[1];
			$remarks = $data[2];
			//if($trims_qty=='') $trims_qty=0;else $trims_qty=$trims_qty;
			if ($trims_qty > 0) {
				if ($data_array_dtls_trims != "") $data_array_dtls_trims .= ",";
				$data_array_dtls_trims .= "(" . $id_dtls_trim . "," . $batch_update_id . ",'" . $item_des . "'," . $trims_qty . ",'" . $remarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
				//$id_dtls_trim = $id_dtls_trim + 1;
			}
		}

		$rID = $rID1 = $rID2 = $rID3 = $rID4 =true;

		if (str_replace("'", "", $update_id) == "") {
			$rID = sql_insert("pro_batch_create_mst", $field_array, $data_array, 0);
			if ($rID) $flag = 1; else $flag = 0;
		} else {
			$rID = sql_update("pro_batch_create_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
			if ($rID) $flag = 1; else $flag = 0;
		}

		//echo "10**insert into pro_batch_create_mst (".$field_array.") values ".$data_array;die;

		$rID2 = sql_insert("pro_batch_create_dtls", $field_array_dtls, $data_array_dtls, 1);
		if ($flag == 1) {
			if ($rID2) $flag = 1; else $flag = 0;
		}


		if ($data_array_roll != "" && $roll_maintained == 1) {
			$rID3 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);
			if ($flag == 1) {
				if ($rID3) $flag = 1; else $flag = 0;
			}
		}
        //echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
		if ($data_array_dtls_trims != "") {
			//echo "insert into pro_batch_trims_dtls (".$field_array_dtls_trims.") values ".$data_array_dtls_trims;die;
			$rID4 = sql_insert("pro_batch_trims_dtls", $field_array_dtls_trims, $data_array_dtls_trims, 1);
			if ($flag == 1) {
				if ($rID4) $flag = 1; else $flag = 0;
			}
		}
		//echo "10**".$flag;die;
		//echo "10**".$rID . "**".$rID1 . "**".$rID2 . "**".$rID3 . "**".$rID4 . "**". $flag; oci_rollback($con);die;
		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "0**" . $batch_update_id . "**" . $serial_no . "**" . str_replace("'", "", $txt_batch_number);
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "0**" . $batch_update_id . "**" . $serial_no . "**" . str_replace("'", "", $txt_batch_number);
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

		$prev_batch_data_arr = array();
		$prev_batch_data = sql_select("select a.id as dtls_id, a.po_id, b.color_id,b.batch_weight from pro_batch_create_dtls a, pro_batch_create_mst b where a.mst_id=b.id and b.id=$update_id");
		foreach ($prev_batch_data as $row) {
			$prev_batch_data_arr[$row[csf('dtls_id')]]['po_id'] = $row[csf('po_id')];
			$prev_batch_data_arr[$row[csf('dtls_id')]]['color'] = $row[csf('color_id')];


		}
		$batchID=str_replace("'", "", $update_id);
		$batch_against_id=str_replace("'", "", $cbo_batch_against);
		$batch_weight 	= str_replace("'", "", $txt_batch_weight);
		if($batch_against_id!=2)
		{
			$issue_data_arr = array();
			$issue_batch_data = sql_select("select sum(b.req_qny_edit) as req_qny_edit,a.issue_number from inv_issue_master a,dyes_chem_issue_dtls b where a.id=b.mst_id and a.entry_form in(5) and  a.batch_no like '%$batchID%' and a.status_active=1  and b.status_active=1 group by a.issue_number");
			$issue_number="";
			$tot_issue_qty=0;
			foreach ($issue_batch_data as $row) {
				//$issue_data_arr[$row[csf('issue_number')]]['po_id'] = $row[csf('po_id')];
				if($issue_number=="") $issue_number=$row[csf('issue_number')];else $issue_number.=",".$row[csf('issue_number')];
				$tot_issue_qty+=$row[csf('req_qny_edit')];
			}

			//echo $msg_issue="23**Issue Found=".$issue_number."**".$tot_issue_qty."**".$batch_weight;
			if($issue_number!="" )
			{

				disconnect($con);
				$msg_issue="23**Issue Found,Update/Delete not allowed \n MRR No=".$issue_number."**".$tot_issue_qty."**".$batch_weight;
				echo $msg_issue;
				die;
			}
		}
		//echo "10**";die;
		$color_id = return_id($txt_batch_color, $color_arr, "lib_color", "id,color_name");
		$flag = 1;
		$batch_no_creation 	= str_replace("'", "", $batch_no_creation);
		$roll_maintained 	= str_replace("'", "", $roll_maintained);
		$txt_search_type 	= str_replace("'", "", $txt_search_type);
		$ready_to_approved 	= str_replace("'", "", $cbo_ready_to_approved);

			//echo "10**select batch_no from pro_fab_subprocess where batch_id=$update_id and entry_form=35 and load_unload_id in(2) and result=1 and status_active=1 and is_deleted=0";die;

			/*if (is_duplicate_field("batch_no", "pro_fab_subprocess", "batch_id=$update_id and entry_form=35 and load_unload_id in(2) and result=1 and status_active=1 and is_deleted=0") == 1)
			{
				disconnect($con);
				echo "14**0**Shade Matched";
				die;
			}*/

			if (str_replace("'", "", $cbo_batch_against) == 2 && str_replace("'", "", $unloaded_batch) != "" && str_replace("'", "", $ext_from) == 0) {


				if (is_duplicate_field("batch_no", "pro_fab_subprocess", "batch_id=$update_id and entry_form=35 and load_unload_id in(1) and status_active=1 and is_deleted=0 $extention_no_cond2 ") == 1)
				{
					disconnect($con);
						echo "14**0**Alreay Loaded/Unload,Update not allowed"; //Issue=5788
						die;
					}


					$id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);

					$batch_update_id = $id;
					$serial_no = date("y", strtotime($pc_date_time)) . "-" . $id;

					if (is_duplicate_field("batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_number and $extention_no_cond and status_active=1 and is_deleted=0") == 1) 					{
						disconnect($con);
						echo "11**0";
						die;
					}
			//cbo_machine_name
					$field_array = "id, batch_no, batch_date, batch_against, batch_for, company_id,working_company_id, booking_no_id, booking_no, booking_without_order, extention_no, color_id, batch_weight, total_trims_weight,save_string, color_range_id, process_id, organic, dur_req_hr, dur_req_min, re_dyeing_from, collar_qty, cuff_qty, floor_id,dyeing_machine, remarks,ready_to_approved, inserted_by, insert_date,sales_order_no,sales_order_id,is_sales,process_seq,booking_entry_form,service_booking_id,service_booking_no,double_dyeing";
					$sales_order_no = ($txt_search_type == 7) ? $txt_booking_no : "''";
					$txt_booking_no = ($txt_search_type != 7) ? $txt_booking_no : $txt_sales_booking_no;
					$txt_sales_id = ($txt_search_type == 7) ? str_replace("'", "", $txt_sales_id) : "''";
					$is_sales = ($txt_search_type == 7) ? 1 : 0;
					$data_array = "(" . $id . "," . $txt_batch_number . "," . $txt_batch_date . "," . $cbo_batch_against . "," . $cbo_batch_for . "," . $cbo_company_id . "," . $cbo_working_company_id . "," . $txt_booking_no_id . "," . $txt_booking_no . "," . $booking_without_order . "," . $txt_ext_no . "," . $color_id . "," . $txt_batch_weight . "," . $txt_tot_trims_weight . "," . $save_data . "," . $cbo_color_range . "," . $txt_process_id . "," . $txt_organic . "," . $txt_du_req_hr . "," . $txt_du_req_min . "," . $update_id . "," . $txt_color_qty . "," . $txt_cuff_qty . "," . $cbo_floor . "," . $cbo_machine_name . "," . $txt_remarks . "," . $ready_to_approved . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $sales_order_no . "," . $txt_sales_id . "," . $is_sales . ",". $txt_process_seq .",". $hidden_booking_entry_form .",". $service_booking_id .",". $txt_service_booking .",". $cbo_double_dyeing . ")";

					$field_array_dtls = "id, mst_id, program_no, po_id, po_batch_no, prod_id, item_description, body_part_id, width_dia_type,roll_no,roll_id,barcode_no,batch_qnty,inserted_by,insert_date,is_sales";
					$field_array_roll = "id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, barcode_no, booking_no, booking_without_order, inserted_by, insert_date,is_sales";

					$roll_table_id = '';

					for ($i = 1; $i <= $total_row; $i++) {
						if (str_replace("'", "", $hide_batch_against) == 5) {
							$po_id = "poId_" . $i;
						} else {
							$po_id = "cboPoNo_" . $i;
						}
						$id_dtls = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
						$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
						$program_no = "cboProgramNo_" . $i;
						$prod_id = "cboItemDesc_" . $i;
						$body_part_id = "cboBodyPart_" . $i;
						$txtRollNo = "txtRollNo_" . $i;
						$hideRollNo = "hideRollNo_" . $i;
						$txtBatchQnty = "txtBatchQnty_" . $i;
						$ItemDesc = $product_array[str_replace("'", "", $$prod_id)];
						$po_batch_no = "txtPoBatchNo_" . $i;
						$updateIdDtls = "updateIdDtls_" . $i;
						$cboDiaWidthType = "cboDiaWidthType_" . $i;
						$barcodeNo = "barcodeNo_" . $i;

						if (str_replace("'", "", $$hideRollNo) != "") {
							$is_sales = ($txt_search_type == 7) ? 1 : 0;

							if (str_replace("'", "", $booking_without_order) == 1 && $is_sales != 1) {
								$bookingNo = str_replace("'", "", $txt_booking_no);
								$poId = str_replace("'", "", $txt_booking_no_id);
							} else {
								$bookingNo = '';
								$poId = str_replace("'", "", $$po_id);
							}

							if ($data_array_roll != "") $data_array_roll .= ",";

							$data_array_roll .= "(" . $id_roll . "," . $batch_update_id . "," . $id_dtls . ",'" . $poId . "',64," . $$txtBatchQnty . "," . $$txtRollNo . "," . $$hideRollNo . "," . $$barcodeNo . ",'" . $bookingNo . "'," . $booking_without_order . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $is_sales . ")";
						}

						if ($data_array_dtls != "") $data_array_dtls .= ",";
						$is_sales = ($txt_search_type == 7) ? 1 : 0;
						$data_array_dtls .= "(" . $id_dtls . "," . $batch_update_id . "," . $$program_no . "," . $$po_id . "," . $$po_batch_no . "," . $$prod_id . ",'" . $ItemDesc . "'," . $$body_part_id . "," . $$cboDiaWidthType . "," . $$txtRollNo . "," . $$hideRollNo . "," . $$barcodeNo . "," . $$txtBatchQnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $is_sales . ")";
					}

					$field_array_dtls_trims = "id,mst_id,item_description,trims_wgt_qnty,remarks,inserted_by, insert_date,status_active,is_deleted";
					$save_string = explode("!!", str_replace("'", "", $save_data));
					for ($i = 0; $i < count($save_string); $i++) {
						$id_dtls_trim = return_next_id_by_sequence("PRO_BATCH_TRIMS_DTLS_PK_SEQ", "pro_batch_trims_dtls", $con);
						$data = explode("_", $save_string[$i]);
						$item_des = $data[0];
						$trims_qty = $data[1];
						$remarks = $data[2];
						if ($trims_qty > 0) {
							if ($i != 0) $data_array_dtls_trims .= ",";
							$data_array_dtls_trims .= "(" . $id_dtls_trim . "," . $batch_update_id . ",'" . $item_des . "'," . $trims_qty . ",'" . $remarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
						}
					}
					$rID = sql_insert("pro_batch_create_mst", $field_array, $data_array, 0);
					if ($rID) $flag = 1; else $flag = 0;

			//echo "insert into pro_batch_create_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
					$rID2 = sql_insert("pro_batch_create_dtls", $field_array_dtls, $data_array_dtls, 1);
					if ($flag == 1) {
						if ($rID2) $flag = 1; else $flag = 0;
					}

			//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die("with rid3");
					if ($data_array_roll != "" && $roll_maintained == 1) {
						$rID3 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);
						if ($flag == 1) {
							if ($rID3) $flag = 1; else $flag = 0;
						}
					}
					$delete_trims_dtls = execute_query("delete from pro_batch_trims_dtls where mst_id=$batch_update_id", 0);
					if ($flag == 1) {
						if ($delete_trims_dtls) $flag = 1; else $flag = 0;
					}
					if ($data_array_dtls_trims != "") {
						$rID6 = sql_insert("pro_batch_trims_dtls", $field_array_dtls_trims, $data_array_dtls_trims, 1);
						if ($flag == 1) {
							if ($rID6) $flag = 1; else $flag = 0;
						}
					}
				}
				else
				{
					$poBatchNoArr = array();
					$batch_update_id = str_replace("'", "", $update_id);
					$serial_no = str_replace("'", "", $txt_batch_sl_no);

					if ($batch_no_creation != 1) {
						if (is_duplicate_field("batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_number and $extention_no_cond and id<>$update_id and status_active=1 and is_deleted=0") == 1) {
							disconnect($con);
							echo "11**0";
							die;
						}
					}

					$field_array_update = "batch_no*batch_date*batch_against*batch_for*company_id*working_company_id*booking_no_id*booking_no*booking_without_order*extention_no*color_id*batch_weight*total_trims_weight*save_string*color_range_id*process_id*organic*dur_req_hr*dur_req_min*collar_qty*cuff_qty*floor_id*dyeing_machine*remarks*ready_to_approved*updated_by*update_date*sales_order_no*process_seq*booking_entry_form*service_booking_id*service_booking_no*double_dyeing";
					$sales_order_no = ($txt_search_type == 7) ? $txt_booking_no : "''";
					$txt_booking_no = ($txt_search_type != 7) ? $txt_booking_no : $txt_sales_booking_no;
					$data_array_update = $txt_batch_number . "*" . $txt_batch_date . "*" . $cbo_batch_against . "*" . $cbo_batch_for . "*" . $cbo_company_id . "*" . $cbo_working_company_id . "*" . $txt_booking_no_id . "*" . $txt_booking_no . "*" . $booking_without_order . "*" . $txt_ext_no . "*" . $color_id . "*" . $txt_batch_weight . "*" . $txt_tot_trims_weight . "*" . $save_data . "*" . $cbo_color_range . "*" . $txt_process_id . "*" . $txt_organic . "*" . $txt_du_req_hr . "*" . $txt_du_req_min . "*" . $txt_color_qty . "*" . $txt_cuff_qty . "*" . $cbo_floor . "*" . $cbo_machine_name . "*" . $txt_remarks . "*" . $ready_to_approved . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $sales_order_no."*".$txt_process_seq."*".$hidden_booking_entry_form."*".$service_booking_id."*".$txt_service_booking."*".$cbo_double_dyeing;

					$field_array_dtls = "id, mst_id, program_no, po_id, po_batch_no, prod_id,item_description, body_part_id, width_dia_type, roll_no,roll_id,barcode_no,batch_qnty,inserted_by,insert_date,is_sales";
					$field_array_dtls_update = "program_no*po_id*po_batch_no*prod_id*item_description*body_part_id*width_dia_type*roll_no*roll_id*barcode_no*batch_qnty*updated_by*update_date*is_sales";
					$field_array_roll = "id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, barcode_no, booking_no, booking_without_order, inserted_by, insert_date";
					$roll_table_id = '';

					for ($i = 1; $i <= $total_row; $i++) {
						if (str_replace("'", "", $cbo_batch_against) == 5) {
							$po_id = "poId_" . $i;
						} else {
							$po_id = "cboPoNo_" . $i;
						}
						$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
						$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
						$program_no = "cboProgramNo_" . $i;
						$prod_id = "cboItemDesc_" . $i;
						$body_part_id = "cboBodyPart_" . $i;
						$txtRollNo = "txtRollNo_" . $i;
						$hideRollNo = "hideRollNo_" . $i;
						$txtBatchQnty = "txtBatchQnty_" . $i;
						$ItemDesc = $product_array[str_replace("'", "", $$prod_id)];
						$txtPoBatchNo = "txtPoBatchNo_" . $i;
						$updateIdDtls = "updateIdDtls_" . $i;
						$cboDiaWidthType = "cboDiaWidthType_" . $i;
						$barcodeNo = "barcodeNo_" . $i;
						$product_id = str_replace("'", "", $$prod_id);
						if($cbo_batch_against != 2)
						{
							if($roll_maintained != 1)
							{
								if (str_replace("'", "", $$program_no) > 0)
								{
									if ($fabric_source == 3)
									{
										$validation_qnty = sql_select("select sum(d.quantity) qnty from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form in(16,61) and d.trans_type=2 and b.program_no=".$$program_no." and d.po_breakdown_id=".$$po_id." and c.entry_form in(16,61) and b.status_active=1 and b.is_deleted=0");
										$msg = "Batch quantity can not be greater than Issue quantity.\nIssue quantity=".$validation_qnty[0][csf("qnty")];
									}
									else if ($fabric_source == 1)
									{
										//production basis
										$validation_qnty = sql_select("select sum(x.quantity) qnty from (select c.booking_id,d.quantity from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form in(13,83,58,22) and d.trans_type in(1,5) and d.po_breakdown_id=".$$po_id." and c.booking_without_order=0 and c.receive_basis=9 and c.entry_form in(13,83,58,22) and b.status_active=1 and b.is_deleted=0) x,inv_receive_master y where x.booking_id=y.id and y.booking_id=".$$program_no."");
										if(empty($validation_qnty)){
											$validation_qnty = sql_select("select sum(d.quantity ) qnty from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form in(13,83,58,22) and d.trans_type in(1,5) and c.booking_id=".$$program_no." and d.po_breakdown_id=".$$po_id." and c.booking_without_order=0 and c.receive_basis=10 and c.entry_form in(13,83,58,22) and b.status_active=1 and b.is_deleted=0 ");
										}
										$msg = "Batch quantity can not be greater than Grey Receive quantity.\nReceive quantity=".$validation_qnty[0][csf("qnty")];
									}
									else
									{
										$validation_qnty = sql_select("select sum(d.quantity) qnty from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form=2 and d.trans_type=1 and c.booking_id=".$$program_no." and c.receive_basis=2 and d.po_breakdown_id=".$$po_id." and c.booking_without_order=0 and c.receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0");
										$msg = "Batch quantity can not be greater than Knitting Production quantity.\nProduction quantity=".$validation_qnty[0][csf("qnty")];
									}
								}
								else
								{
									if(str_replace("'", "", $booking_without_order) == 1)
									{
										$validation_qnty = sql_select("select sum(b.issue_qnty) qnty from product_details_master a, inv_grey_fabric_issue_dtls b, inv_issue_master c where a.id=b.prod_id and b.mst_id=c.id and c.booking_no=$txt_booking_no and c.issue_basis=1 and c.issue_purpose=8 and c.entry_form in(16) and b.status_active=1 and b.is_deleted=0 ");
										$msg = "Batch quantity can not be greater than Issue quantity.\nIssue quantity=".$validation_qnty[0][csf("qnty")]."";
									}
									else
									{
										if ($fabric_source == 3)
										{
											$validation_qnty = sql_select("select sum(d.quantity) qnty from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form in(16,61) and d.trans_type=2  and d.po_breakdown_id=".$$po_id." and c.entry_form in(16,61) and b.status_active=1 and b.is_deleted=0 and d.prod_id = $product_id");
											$msg = "Batch quantity can not be greater than Issue quantity.\nIssue quantity=".$validation_qnty[0][csf("qnty")]."";
										}
										else if ($fabric_source == 1)
										{
											$validation_qnty = sql_select("select sum(x.quantity) qnty from (select c.booking_id,d.quantity from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form in(13,83,58,22) and d.trans_type in(1,5) and d.po_breakdown_id=".$$po_id." and c.booking_without_order=0 and c.receive_basis=9 and c.entry_form in(13,83,58,22) and b.status_active=1 and b.is_deleted=0 and d.prod_id = $product_id) x,inv_receive_master y where x.booking_id=y.id ");
											if(empty($validation_qnty))
											{
												$validation_qnty = sql_select("select sum(d.quantity ) qnty from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form in(13,83,58,22)  and d.trans_type in(1,5) and d.po_breakdown_id=".$$po_id." and c.booking_without_order=0 and c.receive_basis=10 and c.entry_form in(13,83,58,22) and b.status_active=1 and b.is_deleted=0  and d.prod_id = $product_id");
											}
											$msg = "Batch quantity can not be greater than Grey Receive quantity.\nReceive quantity=".$validation_qnty[0][csf("qnty")];
										}
										else
										{
											$validation_qnty = sql_select("select sum(d.quantity) qnty from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d where c.id=b.mst_id and b.id=d.dtls_id and d.entry_form=2 and d.trans_type=1  and c.receive_basis=2 and d.po_breakdown_id=".$$po_id." and c.booking_without_order=0 and c.receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0  and d.prod_id = $product_id");
											$msg = "Batch quantity can not be greater than Knitting Production quantity.\nProduction quantity=".$validation_qnty[0][csf("qnty")];
										}
									}
								}
								$dtls_id=str_replace("'", "", $$updateIdDtls);
								if($dtls_id=="")  $dtls_id_cond=""; else $dtls_id_cond=" and b.id <> $dtls_id";

								$program_cond = (str_replace("'", "", $$program_no) > 0)?" and b.program_no=".$$program_no."":"";
								$po_cond = (str_replace("'", "", $$po_id) > 0)?" and b.po_id in(".str_replace("'", "", $$po_id).")":"";
								$total_batch_qnty=0;$tot_validation_qnty=0;
						//$total_batch_qnty = return_field_value("sum(b.batch_qnty) total_batch_qnty", "pro_batch_create_mst a,pro_batch_create_dtls b", "a.id=b.mst_id and a.company_id=$cbo_company_id and a.booking_no=$txt_booking_no $po_cond  $extention_no_cond_valid and b.prod_id = $product_id and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id <>".str_replace("'", "", $$updateIdDtls)." ","total_batch_qnty");
								$batch_preiv_qnty = sql_select("select sum(b.batch_qnty) as total_batch_qnty from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and a.booking_no=$txt_booking_no $po_cond $extention_no_cond_valid and b.prod_id = $product_id and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $dtls_id_cond $program_cond");
								$total_batch_qnty=$batch_preiv_qnty[0][csf("total_batch_qnty")];
								$tot_validation_qnty=$validation_qnty[0][csf("qnty")];

						//echo "10**"."select sum(b.batch_qnty) total_batch_qnty from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and a.booking_no=$txt_booking_no $po_cond $program_cond and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id <>".str_replace("'", "", $$updateIdDtls)." ";die;
						//echo "10**".$$txtBatchQnty."=".$validation_qnty[0][csf("qnty")];die;
								$tot_batch_qty=str_replace("'", "", $$txtBatchQnty)+$total_batch_qnty;
								if((str_replace("'", "", $$txtBatchQnty)+$total_batch_qnty) > $tot_validation_qnty){
									disconnect($con);
									echo "17**".$msg."**".$tot_batch_qty."**".$tot_validation_qnty;
									die;
								}
							}
						}
				//echo "10**".$msg; die;

						if (str_replace("'", "", $$updateIdDtls) != "") {
							$prev_po_id = $prev_batch_data_arr[str_replace("'", '', $$updateIdDtls)]['po_id'];
							$prev_color_id = $prev_batch_data_arr[str_replace("'", '', $$updateIdDtls)]['color'];

							if ($prev_po_id == str_replace("'", "", $$po_id) && $prev_color_id == $color_id) {
								$po_batch_no = str_replace("'", "", $$txtPoBatchNo);
								$poBatchNoArr[$prev_color_id][$prev_po_id] = $po_batch_no;
							} else {
								if ($poBatchNoArr[$color_id][str_replace("'", "", $$po_id)] == "") {
									$po_batch_no = $po_batch_no_arr[$color_id][str_replace("'", "", $$po_id)] + 1;
									$poBatchNoArr[$color_id][str_replace("'", "", $$po_id)] = $po_batch_no;
								} else {
									$po_batch_no = $poBatchNoArr[$color_id][str_replace("'", "", $$po_id)];
								}
							}

							$id_arr[] = str_replace("'", '', $$updateIdDtls);
							$is_sales = ($txt_search_type == 7) ? 1 : 0;
							$data_array_dtls_update[str_replace("'", '', $$updateIdDtls)] = explode("*", ($$program_no . "*" . $$po_id . "*'" . $po_batch_no . "'*" . $$prod_id . "*'" . $ItemDesc . "'*" . $$body_part_id . "*" . $$cboDiaWidthType . "*" . $$txtRollNo . "*" . $$hideRollNo . "*" . $$barcodeNo . "*" . $$txtBatchQnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $is_sales));
							$id_dtls = str_replace("'", '', $$updateIdDtls);
						} else {
							if ($poBatchNoArr[$color_id][str_replace("'", "", $$po_id)] == "") {
								$po_batch_no = $po_batch_no_arr[$color_id][str_replace("'", "", $$po_id)] + 1;
								$poBatchNoArr[$color_id][str_replace("'", "", $$po_id)] = $po_batch_no;
							} else {
								$po_batch_no = $poBatchNoArr[$color_id][str_replace("'", "", $$po_id)];
							}

							if ($data_array_dtls != "") $data_array_dtls .= ",";
							$is_sales = ($txt_search_type == 7) ? 1 : 0;
							$data_array_dtls .= "(" . $id_dtls_batch . "," . $batch_update_id . "," . $$program_no . "," . $$po_id . ",'" . $po_batch_no . "'," . $$prod_id . ",'" . $ItemDesc . "'," . $$body_part_id . "," . $$cboDiaWidthType . "," . $$txtRollNo . "," . $$hideRollNo . "," . $$barcodeNo . "," . $$txtBatchQnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $is_sales . ")";

					//$id_dtls_batch = $id_dtls_batch + 1;
							$id_dtls = $id_dtls_batch;
						}


						if (str_replace("'", "", $$hideRollNo) != "") {
							$is_sales = ($txt_search_type == 7) ? 1 : 0;
							if (str_replace("'", "", $booking_without_order) == 1 && $is_sales != 1) {
								$bookingNo = str_replace("'", "", $txt_booking_no);
								$poId = str_replace("'", "", $txt_booking_no_id);
							} else {
								$bookingNo = '';
								$poId = str_replace("'", "", $$po_id);
							}

							if ($data_array_roll != "") $data_array_roll .= ",";
							$data_array_roll .= "(" . $id_roll . "," . $batch_update_id . "," . $id_dtls . ",'" . $poId . "',64," . $$txtBatchQnty . "," . $$txtRollNo . "," . $$hideRollNo . "," . $$barcodeNo . ",'" . $bookingNo . "'," . $booking_without_order . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					//$id_roll = $id_roll + 1;
						}
					}

			//$id_dtls_trim = return_next_id("id", "pro_batch_trims_dtls", 1);
					$field_array_dtls_trims = "id,mst_id,item_description,trims_wgt_qnty,remarks,inserted_by, insert_date,status_active,is_deleted";
					$save_string = explode("!!", str_replace("'", "", $save_data));
					for ($i = 0; $i < count($save_string); $i++) {
						$id_dtls_trim = return_next_id_by_sequence("PRO_BATCH_TRIMS_DTLS_PK_SEQ", "pro_batch_trims_dtls", $con);
						$data = explode("_", $save_string[$i]);
						$item_des = $data[0];
						$trims_qty = $data[1];
						$remarks = $data[2];
				//if($trims_qty=='') $trims_qty=0;else $trims_qty=$trims_qty;
						if ($trims_qty > 0) {
							if ($data_array_dtls_trims != "") $data_array_dtls_trims .= ",";
							$data_array_dtls_trims .= "(" . $id_dtls_trim . "," . $batch_update_id . ",'" . $item_des . "'," . $trims_qty . ",'" . $remarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";

					//$id_dtls_trim = $id_dtls_trim + 1;
						}
					}
			//echo "insert into pro_batch_trims_dtls (".$field_array_dtls_trims.") values ".$data_array_dtls_trims;die;
			//echo "insert into pro_batch_create_mst (".$field_array_update.") values ".$data_array_update;die;
					$rID = sql_update("pro_batch_create_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
					if ($rID) $flag = 1; else $flag = 0;
			//echo $flag;
			//echo "6**$rID**$field_array_update**$data_array_update**$update_id";die;
			//echo "delete from pro_roll_details where mst_id=$update_id and entry_form=64";
					$delete_roll = execute_query("delete from pro_roll_details where mst_id=$update_id and entry_form=64", 1);

					if ($flag == 1) {
						if ($delete_roll) $flag = 1; else $flag = 0;
					}

			//echo bulk_update_sql_statement( "pro_batch_create_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );die;
					if ($data_array_dtls_update != "") {
						$rID2 = execute_query(bulk_update_sql_statement("pro_batch_create_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr));
						if ($flag == 1) {
							if ($rID2) $flag = 1; else $flag = 0;
						}
					}

			//echo "6**0**insert into pro_batch_create_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;

					if ($data_array_dtls != "") {

						$rID3 = sql_insert("pro_batch_create_dtls", $field_array_dtls, $data_array_dtls, 1);
						if ($flag == 1) {
							if ($rID3) $flag = 1; else $flag = 0;
						}
					}

					if ($txt_deleted_id != "") {
						$field_array_status = "updated_by*update_date*status_active*is_deleted";
						$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";

						$rID4 = sql_multirow_update("pro_batch_create_dtls", $field_array_status, $data_array_status, "id", $txt_deleted_id, 1);
						if ($flag == 1) {
							if ($rID4) $flag = 1; else $flag = 0;
						}
					}

			//echo "1**".str_replace("'", '', $batch_update_id)."**1**"."insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;]
			//echo "6**0**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
					if ($data_array_roll != "" && $roll_maintained == 1) {
						$rID5 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);
						if ($flag == 1) {
							if ($rID5) $flag = 1; else $flag = 0;
						}
					}

			//echo "delete from pro_batch_trims_dtls where mst_id=$batch_update_id";
					$delete_trims_dtls = execute_query("delete from pro_batch_trims_dtls where mst_id=$batch_update_id", 0);
					if ($flag == 1) {
						if ($delete_trims_dtls) $flag = 1; else $flag = 0;
					}
			//echo "10**insert into pro_batch_trims_dtls (".$field_array_dtls_trims.") values ".$data_array_dtls_trims;die;
					if ($data_array_dtls_trims != "") {
						$rID6 = sql_insert("pro_batch_trims_dtls", $field_array_dtls_trims, $data_array_dtls_trims, 1);
						if ($flag == 1) {
							if ($rID6) $flag = 1; else $flag = 0;
						}
					}
			/*if($roll_table_id!="")
			{
				$rID6=sql_multirow_update("pro_roll_details","roll_used",1,"id",$roll_table_id,1);
				if($flag==1)
				{
					if($rID6) $flag=1; else $flag=0;
				}
			}*/
		}
		//echo "10**".$rID . "**".$rID1 . "**" . $rID2 . "**" . $rID3 . "**" . $rID4 . "**" . $rID5 . "**" . $rID6.'=='.$flag ;die;
		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "1**" . $batch_update_id . "**" . $serial_no . "**" . str_replace("'", "", $txt_batch_number);
			} else {
				mysql_query("ROLLBACK");
				echo "6**0**1";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "1**" . $batch_update_id . "**" . $serial_no . "**" . str_replace("'", "", $txt_batch_number);
			} else {
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		//check_table_status($_SESSION['menu_id'], 0);
		disconnect($con);
		die;
	}
	else if ($operation == 2) // Not Used Delete Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$sql = "select id from pro_fab_subprocess where batch_id=$update_id and entry_form in(32,35) and status_active=1 and is_deleted=0";
		$data_array = sql_select($sql, 1);
		if (count($data_array) > 0) {
			echo "13**" . str_replace("'", "", $update_id);die;
		}
		$batchID=str_replace("'", "", $update_id);
		$batch_weight 	= str_replace("'", "", $txt_batch_weight);
		$batch_against_id=str_replace("'", "", $cbo_batch_against);
		if($batch_against_id!=2)
		{
			$issue_data_arr = array();
			$issue_batch_data = sql_select("select sum(b.req_qny_edit) as req_qny_edit,a.issue_number from inv_issue_master a,dyes_chem_issue_dtls b where a.id=b.mst_id and a.entry_form in(5) and  a.batch_no like '%$batchID%' and a.status_active=1  and b.status_active=1 group by a.issue_number");
			$issue_number="";
			$tot_issue_qty=0;
			foreach ($issue_batch_data as $row) {
				if($issue_number=="") $issue_number=$row[csf('issue_number')];else $issue_number.=",".$row[csf('issue_number')];
				$tot_issue_qty+=$row[csf('req_qny_edit')];
			}
		//echo $msg_issue="23**Issue Found=".$issue_number."**".$tot_issue_qty."**".$batch_weight;
			if($issue_number!="")
			{

				disconnect($con);
				$msg_issue="23**Issue Found,Update/Delete not allowed \n MRR No=".$issue_number."**".$tot_issue_qty."**".$batch_weight;
				echo $msg_issue;
				die;
			}
		}
		$field_array_status = "updated_by*update_date*status_active*is_deleted";
		$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
		$changeStatus = sql_update("pro_batch_create_mst", $field_array_status, $data_array_status, "id", $update_id, 1);
		$changeStatus2 = sql_update("pro_batch_create_dtls", $field_array_status, $data_array_status, "mst_id", $update_id, 1);
		$changeStatus3 = sql_update("pro_roll_details", $field_array_status, $data_array_status, "mst_id*entry_form",$update_id."*64", 1);
		$changeStatus4 = sql_update("pro_batch_trims_dtls", $field_array_status, $data_array_status, "mst_id", $update_id, 1);

		//echo $changeStatus."&&".$changeStatus2."&&".$changeStatus3;die;
		if ($db_type == 0) {
			if ($changeStatus && $changeStatus2 && $changeStatus3 && $changeStatus4) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $update_id);
			} else {
				mysql_query("ROLLBACK");
				echo "7**" . str_replace("'", "", $update_id);

			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($changeStatus && $changeStatus2 && $changeStatus3 && $changeStatus4) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $update_id);
			} else {
				oci_rollback($con);
				echo "7**" . str_replace("'", "", $update_id);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "batch_popup") {
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
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $batch_against; ?>, 'create_batch_search_list_view', 'search_div', 'batch_creation_controller_v2', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:100px;"/>
						</td>
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

if ($action == "create_batch_search_list_view") {
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
		}
		?>
	</tbody>
</table>
<?
exit();
}

if ($action == "populate_data_from_search_popup") {
	$data = explode("**", $data);
	$batch_against = $data[0];
	$batch_for = $data[1];
	$batch_id = $data[2];
	$batch_no = $data[3];
	$company_id = $data[4];
	$unloaded_batch = $data[5];
	$ext_from = $data[6];

	if ($db_type == 0) $year_field = "DATE_FORMAT(insert_date,'%y')";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YY')";
	else $year_cond = "";//defined Later

	$incrementExtentionNo="";
	if($batch_against==2) // Re-dyeing- Extention sequence maintain
	{
		if($unloaded_batch!="" && $ext_from ==0)
		{
			$exists_data_no = sql_select("select a.batch_no,max(a.extention_no) as max_extention_no from pro_batch_create_mst a  where a.batch_no = '".$batch_no."' and a.company_id= $company_id and a.status_active = 1 and a.is_deleted = 0 group by batch_no");
			$exists_extention_no = $exists_data_no[0][csf('max_extention_no')];
			if($exists_extention_no>0)
			{
				$incrementExtentionNo = $exists_extention_no+1;
			}else {
				$incrementExtentionNo = 1;
			}
		}
	}


	$color_arr = return_library_array("SELECT id, color_name from lib_color", 'id', 'color_name');
	$data_array = sql_select("SELECT id, company_id,working_company_id, batch_no, extention_no,floor_id, batch_weight, total_trims_weight,save_string, batch_date, batch_against, batch_for, booking_no, booking_no_id,booking_without_order, color_id, re_dyeing_from, color_range_id, organic, process_id, dur_req_hr, dur_req_min, collar_qty, cuff_qty, dyeing_machine, remarks,ready_to_approved,is_approved, $year_field as year,sales_order_no,sales_order_id,is_sales,process_seq,booking_entry_form,service_booking_id,service_booking_no,double_dyeing from pro_batch_create_mst where id='$batch_id'");
	foreach ($data_array as $row) {

		if($incrementExtentionNo=="")
		{
			if ($row[csf("extention_no")] == 0) $incrementExtentionNo = ''; else $incrementExtentionNo = $row[csf("extention_no")];
		}


		$serial_no = $row[csf("year")]."-".$row[csf("id")] ;
		$booking_no = ($row[csf("batch_for")] != "") ? $row[csf("sales_order_no")] : $row[csf("booking_no")];

		$process_name = '';
		$process_id_array = explode(",", $row[csf("process_id")]);
		foreach ($process_id_array as $val) {
			if ($process_name == "") $process_name = $conversion_cost_head_array[$val]; else $process_name .= "," . $conversion_cost_head_array[$val];
		}

		echo "document.getElementById('txt_batch_sl_no').value = '" . $serial_no . "';\n";
		echo "document.getElementById('cbo_batch_against').value = '" . $row[csf("batch_against")] . "';\n";
		echo "document.getElementById('cbo_batch_for').value = '" . $row[csf("batch_for")] . "';\n";
		echo "active_inactive();\n";
		echo "document.getElementById('txt_batch_date').value = '" . change_date_format($row[csf("batch_date")]) . "';\n";
		echo "document.getElementById('txt_batch_weight').value = '" . $row[csf("batch_weight")] . "';\n";
		echo "document.getElementById('cbo_company_id').value = '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_working_company_id').value = '" . $row[csf("working_company_id")] . "';\n";
		echo "document.getElementById('txt_tot_trims_weight').value = '" . $row[csf("total_trims_weight")] . "';\n";
		echo "document.getElementById('save_data').value = '" . $row[csf("save_string")] . "';\n";
		echo "document.getElementById('txt_batch_number').value = '" . $row[csf("batch_no")] . "';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '" . $row[csf("ready_to_approved")] . "';\n";
		echo "document.getElementById('is_approved_id').value = '" . $row[csf("is_approved")] . "';\n";
		if ($row[csf("is_sales")] != 1) {
			echo "document.getElementById('txt_booking_no').value = '" . $row[csf("booking_no")] . "';\n";
			$booking_no = $row[csf("booking_no")];
			$search_type = "****".$row[csf("booking_entry_form")];
		} else {
			echo "document.getElementById('txt_booking_no').value = '" . $row[csf("sales_order_no")] . "';\n";
			echo "document.getElementById('txt_sales_booking_no').value = '" . $row[csf("booking_no")] . "';\n";
			echo "document.getElementById('txt_sales_id').value = '" . $row[csf("sales_order_id")] . "';\n";
			echo "document.getElementById('txt_search_type').value = '7';\n";
			$booking_no = $row[csf("sales_order_no")];
			$search_type = "**7**".$row[csf("booking_entry_form")];
		}
		if($db_type==0) $group_po="group_concat(po_id) as po_id";
		if($db_type==2) $group_po="listagg(cast(po_id as varchar2(4000)),',') within group (order by po_id) as po_id";
		   
		
	
		$po_ids = return_field_value("$group_po", "pro_batch_create_dtls", "mst_id='" . $batch_id . "'","po_id");
		
		//load_drop_down('requires/batch_creation_controller_v2',this.value, 'load_drop_down_floor', 'td_floor' );
		echo "load_drop_down('requires/batch_creation_controller_v2','".$row[csf("working_company_id")]."', 'load_drop_down_floor', 'td_floor' );\n";
		echo "document.getElementById('txt_po_no_id').value = '" . $po_ids . "';\n";
		echo "document.getElementById('cbo_floor').value = '" . $row[csf("floor_id")] . "';\n";
		echo "document.getElementById('txt_booking_no_id').value = '" . $row[csf("booking_no_id")] . "';\n";
		echo "document.getElementById('booking_without_order').value = '" . $row[csf("booking_without_order")] . "';\n";
		echo "document.getElementById('txt_ext_no').value = '" . $incrementExtentionNo . "';\n";
		echo "document.getElementById('txt_batch_color_id').value = '" . $row[csf("color_id")] . "';\n";
		echo "document.getElementById('txt_batch_color').value = '" . $color_arr[$row[csf("color_id")]] . "';\n";
		echo "document.getElementById('cbo_color_range').value = '" . $row[csf("color_range_id")] . "';\n";
		echo "document.getElementById('txt_organic').value = '" . $row[csf("organic")] . "';\n";
		echo "document.getElementById('txt_process_id').value = '" . $row[csf("process_id")] . "';\n";
		echo "document.getElementById('txt_process_name').value = '" . $process_name . "';\n";
		echo "document.getElementById('txt_process_seq').value = '" . $row[csf("process_seq")] . "';\n";
		echo "document.getElementById('txt_du_req_hr').value = '" . $row[csf("dur_req_hr")] . "';\n";
		echo "document.getElementById('txt_du_req_min').value = '" . $row[csf("dur_req_min")] . "';\n";
		echo "document.getElementById('txt_du_req_min').value = '" . $row[csf("dur_req_min")] . "';\n";
		echo "document.getElementById('txt_color_qty').value = '" . $row[csf("collar_qty")] . "';\n";
		echo "document.getElementById('txt_cuff_qty').value = '" . $row[csf("cuff_qty")] . "';\n";
		echo "document.getElementById('txt_remarks').value = '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('update_id').value = '" . $row[csf("id")] . "';\n";
		echo "load_drop_down('requires/batch_creation_controller_v2','".$row[csf("floor_id")]."', 'load_drop_machine', 'td_dyeing_machine');\n";
		echo "document.getElementById('cbo_machine_name').value = '" . $row[csf("dyeing_machine")] . "';\n";
		echo "document.getElementById('txt_service_booking').value = '" . $row[csf("service_booking_no")] . "';\n";
		echo "document.getElementById('cbo_double_dyeing').value = '" . $row[csf("double_dyeing")] . "';\n";
		echo "document.getElementById('service_booking_id').value = '" . $row[csf("service_booking_id")] . "';\n";

		if ($row[csf("booking_no")] != "") {
			echo "show_list_view('" . $booking_no . "'+'**'+'" . $row[csf("booking_without_order")] . "'+'**'+'" . $search_type . "','show_color_listview','list_color','requires/batch_creation_controller_v2','');\n";
		}

		if ($batch_against == 2) {
			echo "document.getElementById('cbo_batch_against').value = '" . $batch_against . "';\n";
			//echo "$('#txt_ext_no').removeAttr('disabled','disabled');\n";
			echo "$('#txt_booking_no').attr('disabled','disabled');\n";
			echo "$('#txt_batch_color').attr('disabled','disabled');\n";
			echo "$('#txt_batch_number').attr('readOnly','readOnly');\n";
			echo "$('#cbo_color_range').attr('disabled','disabled');\n";
			echo "$('#txt_process_name').attr('disabled','disabled');\n";
		}

		if ($row[csf("batch_against")] == 2) {
			$prv_batch_against = return_field_value("batch_against", "pro_batch_create_mst", "id='" . $row[csf("re_dyeing_from")] . "'");
			echo "document.getElementById('hide_batch_against').value = '" . $prv_batch_against . "';\n";
			echo "document.getElementById('hide_update_id').value = '" . $row[csf("id")] . "';\n";
		} else {
			echo "document.getElementById('hide_batch_against').value = '" . $row[csf("batch_against")] . "';\n";
			echo "document.getElementById('hide_update_id').value = '';\n";
		}

		echo "document.getElementById('unloaded_batch').value = '".$unloaded_batch."';\n";
		echo "document.getElementById('ext_from').value = '".$ext_from."';\n";

		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_batch_creation',1);\n";

		if($row[csf("is_approved")]==1)
		{
			echo "$('#approved').text('Approved');\n";
		}
		elseif($row[csf("is_approved")]==3)
		{
			echo "$('#approved').text('Partial Approved');\n";
		}else{
			echo "$('#approved').text('');\n";
		}
	}

	exit();
}

if ($action == "serviceBooking_popup")
{
	echo load_html_head_contents("WO Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$width = 1055;
	?>
	<script>

		function js_set_value(id, booking_no) {
			$('#service_hidden_booking_id').val(id);
			$('#service_hidden_booking_no').val(booking_no);
			parent.emailwindow.hide();
		}

	</script>
</head>
<body>
	<div align="center" style="width:1030px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:1030px; margin-left:3px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="720" class="rpt_table">
					<thead>
						<th>Search By</th>
						<th width="240">Enter WO/PI/Production No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton"/>
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="service_hidden_booking_id" id="service_hidden_booking_id" class="text_boxes" value="">
							<input type="hidden" name="service_hidden_booking_no" id="service_hidden_booking_no" class="text_boxes" value="">
							<input type="hidden" name="service_booking_without_order" id="service_booking_without_order" class="text_boxes" value="">
							<input type="hidden" name="hidden_knitting_company" id="hidden_knitting_company" class="text_boxes" value="">
						</th>
					</thead>
					<tr>
						<td align="center">
							<?
							$receive_basis = array(0 => "Service Booking");
							echo create_drop_down("cbo_receive_basis", 152, $receive_basis, "", 1, "-- Select --", $recieve_basis, "", "1", "");
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_company_id').value+'_<? echo $cbo_knitting_source; ?>_'+<? echo $cbo_knitting_company; ?>, 'create_wo_no_production_search_list_view', 'search_div', 'batch_creation_controller_v2', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:100px;"/>
						</td>
					</tr>
				</table>
				<div style="margin-top:10px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action == "create_wo_no_production_search_list_view") {
	$data = explode("_", $data);
	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

	$search_string = "%" . trim($data[0]) . "%";
	$recieve_basis = $data[1];
	$company_id = $data[2];
	$knitting_source = $data[3];
	$knitting_company = $data[4];

	$paymodeCondition = "and a.pay_mode in(3,5)";
	$paymodeWoNonordCondition = "and s.pay_mode in(3,5)";

	$buyer_short_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$po_arr = array();
	$po_data = sql_select("select b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.grouping, b.file_no, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");
	foreach ($po_data as $row) {
		$po_arr[$row[csf('id')]] = $row[csf('po_number')] . "**" . $row[csf('pub_shipment_date')] . "**" . $row[csf('po_quantity')] . "**" . $row[csf('po_qnty_in_pcs')] . "**" . $row[csf('grouping')] . "**" . $row[csf('file_no')];
	}

	if (trim($data[0]) != "") {
		$search_field_cond = "and a.booking_no like '$search_string'";
		$search_field_cond_sample = "and s.booking_no like '$search_string'";
	} else {
		$search_field_cond = "";
	}

	//if ($knitting_company != 0) $supplier_con = "and a.supplier_id=$knitting_company"; else $supplier_con = "";

	$sql = "select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.buyer_id, a.supplier_id, a.po_break_down_id, a.item_category, a.delivery_date, a.job_no as job_no_mst, 0 as type_id from wo_booking_dtls c,wo_booking_mst a, wo_po_break_down b where c.booking_no=a.booking_no and a.job_no=b.job_no_mst and a.company_id=$company_id and a.item_category=12 and a.booking_type=3 and c.process=31 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $supplier_con $paymodeCondition group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.buyer_id, a.po_break_down_id, a.item_category, a.delivery_date, a.job_no, a.supplier_id
	union all
	select s.id, s.prefix_num as booking_no_prefix_num, s.booking_no, s.booking_date, s.buyer_id, s.supplier_id, null as po_break_down_id, s.item_category, null as delivery_date, null as job_no_mst, 1 as type_id from wo_non_ord_knitdye_booking_mst s where s.company_id=$company_id and s.item_category in(1,13) and s.status_active=1 and s.is_deleted=0 $paymodeWoNonordCondition $search_field_cond_sample
	order by type_id, id";

	//echo $sql;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="105">Booking No</th>
			<th width="75">Booking Date</th>
			<th width="60">Buyer</th>
			<th width="87">Item Category</th>
			<th width="75">Delivary date</th>
			<th width="80">Job No</th>
			<th width="80">Order Qnty</th>
			<th width="75">Shipment Date</th>
			<th width="100">Internal Ref.</th>
			<th width="90">File No</th>
			<th>Order No</th>
		</thead>
	</table>
	<div style="width:1028px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row) {
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			$po_qnty_in_pcs = '';
			$po_no = '';
			$min_shipment_date = '';
			$internal_ref = '';
			$file_no = '';

			$po_id = explode(",", $row[csf('po_break_down_id')]);
			foreach ($po_id as $id) {
				$po_data = explode("**", $po_arr[$id]);
				$po_number = $po_data[0];
				$pub_shipment_date = $po_data[1];
				$po_qnty = $po_data[2];
				$poQntyPcs = $po_data[3];
				$internalRef = $po_data[4];
				$fileNo = $po_data[5];

				if ($po_no == "") $po_no = $po_number; else $po_no .= "," . $po_number;
				if ($internal_ref == '') $internal_ref = $internalRef; else $internal_ref .= "," . $internalRef;
				if ($file_no == '') $file_no = $fileNo; else $file_no .= "," . $fileNo;

				if ($min_shipment_date == '') {
					$min_shipment_date = $pub_shipment_date;
				} else {
					if ($pub_shipment_date < $min_shipment_date) $min_shipment_date = $pub_shipment_date; else $min_shipment_date = $min_shipment_date;
				}

				$po_qnty_in_pcs += $poQntyPcs;
			}

			$internal_ref = implode(",", array_unique(explode(",", $internal_ref)));
			$file_no = implode(",", array_unique(explode(",", $file_no)));
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>');">
				<td width="30"><? echo $i; ?></td>
				<td width="105"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="75" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				<td width="60"><p><? echo $buyer_short_arr[$row[csf('buyer_id')]]; ?></p></td>
				<?
				if ($row[csf('type')] == 0) {
					$category_name = $item_category[$row[csf('item_category')]];
				} else {
					$category_name = $conversion_cost_head_array[$row[csf('item_category')]];
				}
				?>
				<td width="87"><p><? echo $category_name; ?></p></td>
				<td width="75" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
				<td width="80"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
				<td width="80" align="right"><? echo $po_qnty_in_pcs; ?></td>
				<td width="75" align="center"><? echo change_date_format($min_shipment_date); ?></td>
				<td width="100"><p><? echo $internal_ref; ?></p></td>
				<td width="90"><p><? echo $file_no; ?></p></td>
				<td><p><? echo $po_no; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>
	</table>
</div>
<?
}

if ($action == 'populate_data_from_service_booking') {
	$data = explode("**", $data);
	$booking_no = $data[0];
	$without_order = $data[1];
	$supplier_id = $data[2];
	$company_id = $data[3];
	$knitting_source = $data[4];

	if ($supplier_id != 0) $supplier_cond = "and a.knitting_company=$supplier_id"; else $supplier_cond = "";
	$booking_arr = array();
	if ($without_order == 0) {
		$sql_chk_prog_no = sql_select("select program_no from wo_booking_dtls where booking_no='$booking_no' group by program_no");
		$row_prog = "";
		foreach ($sql_chk_prog_no as $row_prog) {
			$row_prog = $row_prog[csf('program_no')];
		}
		if ($row_prog != "") {
			$sql_chk_prog_no_2 = sql_select("select id,program_qnty from ppl_planning_info_entry_dtls where id='$row_prog' group by id,program_qnty");
			$row_prog_2 = "";
			foreach ($sql_chk_prog_no_2 as $row_prog_2) {
				$row_prog_2 = $row_prog_2[csf('id')];
			}
		}

		if ($row_prog_2 != "") {
			$sql = "select a.program_no,a.booking_no, sum(a.wo_qnty) as qnty from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b,ppl_planning_info_entry_dtls d where a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id and a.booking_no='$booking_no' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and a.program_no=d.id  group by a.program_no,a.booking_no"; //new

		} else {
			$sql = "select a.booking_no,a.wo_qnty as qnty from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id and a.booking_no='$booking_no' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 "; //old
		}

	} else {
		$sql = "select a.booking_no, b.wo_qty as qnty
		from wo_non_ord_knitdye_booking_mst a, wo_non_ord_knitdye_booking_dtl b, wo_non_ord_samp_booking_dtls c
		where a.id=b.mst_id and b.fab_des_id=c.id and b.fabric_source=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='$booking_no'";

	}

	$book_result = sql_select($sql);
	foreach ($book_result as $row) {
		$booking_arr[$row[csf('booking_no')]] += $row[csf('qnty')];
		$sb_qnty += $row[csf('qnty')];
	}

	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company_id and variable_list=23 and category = 13 order by id");

	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;

	$receive_arr = array();
	$recv_sql = ("SELECT a.service_booking_no,a.knitting_company as supplier_id, sum(b.grey_receive_qnty) grey_receive_qnty
		FROM inv_receive_master a INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id WHERE a.service_booking_no ='$booking_no' and a.knitting_source=$knitting_source  $supplier_cond
		GROUP BY a.service_booking_no,a.knitting_company");

	$result = sql_select($recv_sql);

	$grey_receive_qnty = 0;
	if(!empty($result)){

		foreach ($result as $row) {

			$grey_receive_qnty = $row[csf('grey_receive_qnty')];
			$allowedQty = 0;
			$allowedQty = $booking_arr[$row[csf('service_booking_no')]];
			$result = ($over_receive_limit / 100) * $allowedQty;
			$allow_total_val = $allowedQty + $result;

			if ($over_receive_limit>0) {
				$over_receive_limit = $over_receive_limit;
				$totallow_qty = $allowedQty + $result;
				echo "$('#allowedQty').text('" . number_format($allowedQty, 2, '.', '') . " + " . $over_receive_limit . " %" . "=" . number_format($allow_total_val, 2, '.', '') . "');\n";
			} else {
				$over_receive_limit = 0;
				echo "$('#allowedQty').text('=" . number_format($allowedQty, 2, '.', '') . "');\n";
			}

			$tot_balance = $allow_total_val - $grey_receive_qnty;
			$txt_title = "Over receive is allowed up to" . ' ' . $over_receive_limit . ' %';
			echo "$('#td_title').attr('title','$txt_title');\n";
			echo "$('#totalProduction').text('" . number_format($grey_receive_qnty, 2, '.', '') . "');\n";
			echo "$('#balance').text('" . number_format($tot_balance, 2, '.', '') . "');\n";
			echo "document.getElementById('allowedQtyTotal').value 		= '" . number_format($allow_total_val, 2, '.', '') . "';\n";
			//echo "document.getElementById('service_booking_id').value 		= '" . number_format($allow_total_val, 2, '.', '') . "';\n";
		}
	}else{
		$allowedQty = $sb_qnty;
		$result = ($over_receive_limit / 100) * $allowedQty;
		$allow_total_val = $allowedQty + $result;

		if ($over_receive_limit != '') {
			$over_receive_limit = $over_receive_limit;
			$totallow_qty = $allowedQty + $result;
			echo "$('#allowedQty').text('" . number_format($allowedQty, 2, '.', '') . " + " . $over_receive_limit . "%" . " = " . number_format($allow_total_val, 2, '.', '') . "');\n";
			$tot_balance = $allow_total_val;
		} else {
			$over_receive_limit = 0;
			$allowedQty = $sb_qnty;
			$tot_balance = $sb_qnty;
			echo "$('#allowedQty').text('=" . number_format($sb_qnty, 2, '.', '') . "');\n";
		}
		echo "$('#balance').text('" . number_format($tot_balance, 2, '.', '') . "');\n";
		echo "document.getElementById('allowedQtyTotal').value 		= '" . number_format($tot_balance, 2, '.', '') . "';\n";
	}
}

if ($action == 'batch_details') {
	$data = explode('**', $data);
	$batch_against = $data[0];
	$batch_for = $data[1];
	$batch_id = $data[2];
	$roll_maintained = $data[3];
	$batch_maintained = $data[4];
	$tblRow = 0;

	if ($batch_against == 2) {
		$disbled = "";
		$disbled_drop_down = 1;
	} else {
		$disbled = "";
		$disbled_drop_down = 0;
	}

	$po_array = array();
	$program_no_array = array();
	$body_part_ids_array = array();
	$data_array = sql_select("select a.batch_against, a.batch_for, a.booking_no, a.re_dyeing_from, a.color_id, a.booking_without_order,a.is_sales,a.sales_order_id, b.id, b.program_no, b.po_id, b.prod_id, b.item_description, b.body_part_id, b.width_dia_type, b.roll_no, b.roll_id, b.barcode_no, b.batch_qnty, b.po_batch_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0");
	
	if ($data_array[0][csf('batch_against')] == 2)
	{
		foreach ($data_array as $row) {
			$tblRow++;

			$batch_array = sql_select("select batch_against, batch_for, booking_no from pro_batch_create_mst where id=" . $row[csf("re_dyeing_from")]);

			?>
			<tr class="general <? echo $row[csf('program_no')];?>" id="tr_<? echo $tblRow; ?>">
				<td id="slTd_<? echo $tblRow; ?>"><? echo $tblRow; ?></td>
				<?
				if ($batch_array[0][csf('batch_against')] == 1 || $batch_array[0][csf('batch_against')] == 2 || $batch_array[0][csf('batch_against')] == 3) {
					if ($roll_maintained == 1) {
						if ($tblRow == 1) {
							$po_array = return_library_array("SELECT a.id, a.po_number FROM wo_po_break_down a, pro_batch_create_dtls b WHERE a.id=b.po_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number", 'id', 'po_number');
							if (empty($po_array)) $po_array = array();

							$fab_description_array = return_library_array("select a.id, a.product_name_details from product_details_master a, pro_batch_create_dtls b where a.id=b.prod_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
						}

						if ($row[csf('program_no')] == 0) {
							$program_no_array = array();
						} else {
							$program_no_array[$row[csf('program_no')]] = $row[csf('program_no')];
						}
					} else {
						if ($tblRow == 1) {
							if ($row[csf('booking_without_order')] == 0) {
								$po_array = return_library_array("SELECT a.id, a.po_number FROM wo_po_break_down a, wo_booking_dtls b WHERE a.id=b.po_break_down_id and b.booking_no='" . $row[csf('booking_no')] . "' and b.fabric_color_id=" . $row[csf('color_id')] . " and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number", 'id', 'po_number');
								if (empty($po_array)) $po_array = array();

								$program_no_array = return_library_array("SELECT b.id as program_id, b.id as program_no FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b WHERE a.id=b.mst_id and a.booking_no='" . $row[csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", 'program_id', 'program_no');
								if (empty($program_no_array)) $program_no_array = array();
							} else if ($row[csf('booking_without_order')] == 1) {
								if ($batch_maintained == 0) {
									$fab_description_array = return_library_array("select a.id, a.product_name_details from product_details_master a, inv_grey_fabric_issue_dtls b, inv_issue_master c where a.id=b.prod_id and b.mst_id=c.id and c.booking_no='" . $row[csf('booking_no')] . "' and c.issue_basis=1 and c.issue_purpose=8 and c.entry_form in(16) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
								} else {
									$fab_description_array = return_library_array("select a.id, a.product_name_details from product_details_master a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and c.booking_no='" . $row[csf('booking_no')] . "' and c.booking_without_order=1 and c.entry_form in(2,22) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
								}
								if (empty($fab_description_array)) $fab_description_array = array();
							}
						}

						if ($row[csf('booking_without_order')] == 0) {
							if ($batch_maintained == 0) {
								if ($row[csf('program_no')] > 0) {
									//$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, inv_grey_fabric_issue_dtls b, inv_issue_master c, order_wise_pro_details d where a.id=b.prod_id and b.mst_id=c.id and a.id=d.prod_id and d.entry_form=16 and d.trans_type=2 and b.program_no='".$row[csf('program_no')]."' and d.po_breakdown_id='".$row[csf('po_id')]."' and c.issue_basis=3 and c.entry_form=16 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');

									$fab_description_array = return_library_array("select a.id, a.product_name_details from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=16 and d.trans_type=2 and b.program_no='" . $row[csf('program_no')] . "' and d.po_breakdown_id='" . $row[csf('po_id')] . "' and c.issue_basis=3 and c.entry_form=16 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
								} else {
									$fab_description_array = return_library_array("select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id='" . $row[csf('po_id')] . "' and b.entry_form in(16) and b.trans_type in(2) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
								}
							} else {
								if ($row[csf('program_no')] > 0) {
									//$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, pro_grey_prod_entry_dtls b, inv_receive_master c, order_wise_pro_details d where a.id=b.prod_id and b.mst_id=c.id and a.id=d.prod_id and d.entry_form=2 and d.trans_type=1 and c.booking_id='".$row[csf('program_no')]."' and d.po_breakdown_id='".$row[csf('po_id')]."' and c.booking_without_order=0 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
									$fab_description_array = return_library_array("select a.id, a.product_name_details from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=2 and d.trans_type=1 and c.booking_id='" . $row[csf('program_no')] . "' and c.receive_basis=2 and d.po_breakdown_id='" . $row[csf('po_id')] . "' and c.booking_without_order=0 and c.receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 group by  a.id, a.product_name_details", 'id', 'product_name_details');
								} else {
									$fab_description_array = return_library_array("select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id='" . $row[csf('po_id')] . "' and b.entry_form in(2,13,22) and b.trans_type in(1,5) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
								}

							}
							if (empty($fab_description_array)) $fab_description_array = array();
						}
					}

					if ($tblRow == 1) {
						$prodIds = implode(",", array_keys($fab_description_array));
						if ($row[csf('booking_without_order')] == 1) {
							$bodyPartData = sql_select("select b.prod_id, b.body_part_id from pro_grey_prod_entry_dtls b, inv_receive_master c where b.mst_id=c.id and c.booking_no='" . $row[csf('booking_no')] . "' and b.prod_id in($prodIds) and c.booking_without_order=1 and c.entry_form in(2,22) and b.status_active=1 and b.is_deleted=0 group by b.prod_id, b.body_part_id");
							foreach ($bodyPartData as $rowB) {
								$body_part_ids_array[$rowB[csf('prod_id')]] .= $rowB[csf('body_part_id')] . ",";
							}
						} else {
							$poIds = implode(",", array_keys($po_array));
							//$bodyPartData = sql_select("select b.prod_id, b.po_breakdown_id, a.body_part_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($poIds) and b.entry_form in(2,22) and b.trans_type=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id in($prodIds) group by b.prod_id, b.po_breakdown_id, a.body_part_id");
							if ($row[csf('program_no')] > 0) {
								$bodyPartData = sql_select("select b.prod_id, b.po_breakdown_id, a.body_part_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b,inv_receive_master c where a.id=b.dtls_id  and c.id=a.mst_id  and c.entry_form in(2,22) and b.entry_form in(2,22) and b.trans_type=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id in($prodIds) and c.booking_id='" . $row[csf('program_no')] . "' and c.booking_without_order=0  group by b.prod_id, b.po_breakdown_id, a.body_part_id");
							}
							else
							{
								$bodyPartData = sql_select("select b.prod_id, b.po_breakdown_id, a.body_part_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b,inv_receive_master c where a.id=b.dtls_id  and c.id=a.mst_id  and c.entry_form in(2,22) and b.entry_form in(2,22) and b.trans_type=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id in($prodIds) and c.booking_no='" . $row[csf('booking_no')] . "' and c.booking_without_order=0  group by b.prod_id, b.po_breakdown_id, a.body_part_id");
							}
						}
					}


					if ($row[csf('booking_without_order')] == 1) {
						$body_part_ids = chop($body_part_ids_array[$row[csf('prod_id')]], ',');
					} else {
						$body_part_ids = chop($body_part_ids_array[$row[csf('po_id')]][$row[csf('prod_id')]], ',');
					}

					echo "<td align='center' id='programNoTd_$tblRow'>";
					echo create_drop_down("cboProgramNo_".$tblRow, 80, $program_no_array, "", 1, "-- Select --", $row[csf('program_no')], "load_item_desc(this.value,this.id );", 1, "", "", "", "", "", "", "cboProgramNo[]");
					echo "</td>";
					echo "<td align='center' id='poNoTd_$tblRow'>";
					echo create_drop_down("cboPoNo_" . $tblRow, 130, $po_array, '', 1, "-- Select Po Number --", $row[csf('po_id')], "load_item_desc(this.value,this.id );", 1, "", "", "", "", "", "", "cboPoNo[]");
					echo "</td>";
					echo "<td align='center' id='itemDescTd_$tblRow' title='".$fab_description_array[$row[csf('prod_id')]]."'>";
					echo create_drop_down("cboItemDesc_" . $tblRow, 180, $fab_description_array, "", 1, "-- Select Item Desc --", $row[csf('prod_id')], "load_body_part(this.value,this.id);", 1, "", "", "", "", "", "", "cboItemDesc[]");
					echo "</td>";
					echo "<td align='center' id='bodyPartTd_$tblRow'>";
					echo create_drop_down("cboBodyPart_" . $tblRow, 120, $body_part, "", 1, "-- Select --", $row[csf('body_part_id')], "", 1, $body_part_ids, "", "", "", "", "", "cboBodyPart[]");
					echo "</td>";
				} else if ($batch_array[0][csf('batch_against')] == 5) {
					if ($tblRow == 1) {
						//$po_no_array=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
						$po_array = return_library_array("SELECT a.id, a.po_number FROM wo_po_break_down a, pro_batch_create_dtls b WHERE a.id=b.po_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number", 'id', 'po_number');
					}
					$po_no = $po_no_array[$row[csf('po_id')]];

					if ($roll_maintained == 1) {
						if ($tblRow == 1) {
							$fab_description_array = return_library_array("select a.id, a.product_name_details from product_details_master a, pro_batch_create_dtls b where a.id=b.prod_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
						}

						if ($row[csf('program_no')] == 0) {
							$program_no_array = array();
						} else {
							$program_no_array[$row[csf('program_no')]] = $row[csf('program_no')];
						}
					} else {
						$program_no_array = return_library_array("SELECT b.id as program_id, b.id as program_no FROM ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b WHERE a.dtls_id=b.id and a.po_id='" . $row[csf('po_id')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", 'program_id', 'program_no');
						if (empty($program_no_array)) $program_no_array = array();

						if ($batch_maintained == 0) {
							$fab_description_array = return_library_array("select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id='" . $row[csf('po_id')] . "' and b.entry_form in(16) and b.trans_type in(2) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
						} else {
							$fab_description_array = return_library_array("select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id='" . $row[csf('po_id')] . "' and b.entry_form in(2,13,22) and b.trans_type in(1,5) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
						}
						if (empty($fab_description_array)) $fab_description_array = array();
					}

					if ($tblRow == 1) {
						$prodIds = implode(",", array_keys($fab_description_array));
						$poIds = implode(",", array_keys($po_array));
						$bodyPartData = sql_select("select b.prod_id, b.po_breakdown_id, a.body_part_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($poIds) and b.entry_form in(2,22) and b.trans_type=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id in($prodIds) group by b.prod_id, b.po_breakdown_id, a.body_part_id");
						foreach ($bodyPartData as $rowB) {
							$body_part_ids_array[$rowB[csf('po_breakdown_id')]][$rowB[csf('prod_id')]] .= $rowB[csf('body_part_id')] . ",";
						}
					}

					$body_part_ids = chop($body_part_ids_array[$row[csf('po_id')]][$row[csf('prod_id')]], ',');

					echo "<td align='center' id='programNoTd_$tblRow'>";
					echo create_drop_down("cboProgramNo_".$tblRow, 80, $program_no_array, "", 1, "-- Select --", $row[csf('program_no')], "", 1, "", "", "", "", "", "", "cboProgramNo[]");
					echo "</td>";
					?>
					<td align='center' id='poNoTd_<? echo $tblRow; ?>'>
						<input type="text" name="cboPoNo[]" id="cboPoNo_<? echo $tblRow; ?>" class="text_boxes"
						style="width:120px;" placeholder="Double Click to Search"
						onDblClick="openmypage_po(<? echo $tblRow; ?>)" value="<? echo $po_no; ?>"
						disabled="disabled"/>
					</td>
					<td align='center' id='itemDescTd_<? echo $tblRow; ?>' title="<? echo $fab_description_array[$row[csf('prod_id')]];?>">
						<? echo create_drop_down("cboItemDesc_" . $tblRow, 180, $fab_description_array, "", 1, "-- Select Item Desc --", $row[csf('prod_id')], "", 1, "", "", "", "", "", "", "cboItemDesc[]"); ?>
					</td>
					<td align='center' id='bodyPartTd_<? echo $tblRow; ?>'>
						<? echo create_drop_down("cboBodyPart_" . $tblRow, 120, $body_part, "", 1, "-- Select --", $row[csf('body_part_id')], "", 1, $body_part_ids, "", "", "", "", "", "cboBodyPart[]"); ?>
					</td>
					<?
				}
				?>
				<td>
					<? echo create_drop_down("cboDiaWidthType_" . $tblRow, 90, $fabric_typee, "", 1, "-- Select --", $row[csf('width_dia_type')], "", 1, "", "", "", "", "", "", "cboDiaWidthType[]"); ?>
				</td>
				<td>
					<input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $tblRow; ?>" class="text_boxes"
					style="width:50px"
					value="<? if ($row[csf('roll_no')] != 0) echo $row[csf('roll_no')]; ?>"
					disabled="disabled"/>
					<input type="hidden" name="hideRollNo[]" id="hideRollNo_<? echo $tblRow; ?>"
					class="text_boxes"
					value="<? echo $row[csf('roll_id')]; ?>"/>
					<? if ($batch_array[0][csf('batch_against')] == 5) $po_id = $row[csf('po_id')]; else $po_id = ""; ?>
					<input type="hidden" name="poId[]" id="poId_<? echo $tblRow; ?>" value="<? echo $po_id; ?>"
					class="text_boxes" readonly/>
					<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $tblRow; ?>"
					class="text_boxes"
					value="<? echo $row[csf('id')]; ?>"/>
					<!--<input type="hidden" name="barcodeNo_<? echo $tblRow; ?>" id="barcodeNo_<? echo $tblRow; ?>" value="<? echo $row[csf('barcode_no')]; ?>" />-->
				</td>
				<td>
					<input type="text" name="barcodeNo[]" id="barcodeNo_<? echo $tblRow; ?>" class="text_boxes"
					value="<? echo $row[csf('barcode_no')]; ?>" style="width:70px" placeholder="Display"
					readonly/>
				</td>
				<td>
					<input type="text" name="txtBatchQnty[]" id="txtBatchQnty_<? echo $tblRow; ?>"
					class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:60px"
					value="<? echo $row[csf('batch_qnty')]; ?>"/>
				</td>
				<td>
					<input type="text" name="txtPoBatchNo[]" id="txtPoBatchNo_<? echo $tblRow; ?>"
					class="text_boxes_numeric" style="width:45px"
					value="<? echo $row[csf('po_batch_no')]; ?>"
					disabled/>
				</td>
				<td width="65">
                <?
                    if($roll_maintained==0)
					{?>
					
                        <input type="button" id="popopupid_<? echo $tblRow; ?>" name="popopupid[]" style="width:20px" class="formbuttonplasminus" value=":::" onClick="fnc_po_popup(<? echo $tblRow.','.$row[csf('program_no')]; ?>)" />
                    <?	
					}
					?>
                    
					<input type="button" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px"
					class="formbuttonplasminus" value="+"
					onClick="add_break_down_tr(<? echo $tblRow; ?>)"/>
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px"
					class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tblRow; ?>);"/>
				</td>
			</tr>
			<?
		}
	}
	else
	{
		foreach ($data_array as $row) {
			$tblRow++;

			?>
			<tr class="general <? echo $row[csf('program_no')];?>" id="tr_<? echo $tblRow; ?>">
				<td id="slTd_<? echo $tblRow; ?>"><? echo $tblRow; ?></td>
				<?
				if ($row[csf('batch_against')] == 1 || $row[csf('batch_against')] == 3) {
					if ($roll_maintained == 1) {
						//$disbled="disabled='disabled'";
						if ($batch_against == 2) {
							$disbled = "";
						} else {
							$disbled = "disabled='disabled'";
						}

						$disbled_drop_down = 1;

						if ($tblRow == 1) {

							if ($row[csf('is_sales')] == 1) {
								$po_array = return_library_array("SELECT a.id, a.job_no FROM fabric_sales_order_mst a, pro_batch_create_dtls b WHERE a.id=b.po_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.job_no", 'id', 'job_no');
							} else {
								
								$po_array = return_library_array("SELECT a.id, a.po_number FROM wo_po_break_down a, pro_batch_create_dtls b WHERE a.id=b.po_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number", 'id', 'po_number');
							}
							if (empty($po_array)) $po_array = array();

							$fab_description_array = return_library_array("select a.id, a.product_name_details from product_details_master a, pro_batch_create_dtls b where a.id=b.prod_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
						}

						if ($row[csf('program_no')] == 0 || $row[csf('program_no')] == "") {
							$program_no_array = array();
						} else {
							$program_no_array[$row[csf('program_no')]] = $row[csf('program_no')];
						}
					} else {
						if ($tblRow == 1) {
							if ($row[csf('booking_without_order')] == 0) {
								if ($row[csf('is_sales')] == 1) {
									$po_array = return_library_array("SELECT a.id, a.job_no po_number FROM fabric_sales_order_mst a WHERE a.id='" . $row[csf('sales_order_id')] . "' and a.status_active=1 and a.is_deleted=0", 'id', 'po_number');
								} else {
									
									$po_array = return_library_array("SELECT a.id, a.po_number FROM wo_po_break_down a, wo_booking_dtls b WHERE a.id=b.po_break_down_id and b.booking_no='" . $row[csf('booking_no')] . "' and b.fabric_color_id=" . $row[csf('color_id')] . " and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number", 'id', 'po_number');
								}

								//$po_array = return_library_array("SELECT a.id, a.po_number FROM wo_po_break_down a, wo_booking_dtls b WHERE a.id=b.po_break_down_id and b.booking_no='" . $row[csf('booking_no')] . "' and b.fabric_color_id=" . $row[csf('color_id')] . " and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number", 'id', 'po_number');
								if (empty($po_array)) $po_array = array();

								/*$program_no_array = return_library_array("SELECT b.id as program_id, b.id as program_no FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b WHERE a.id=b.mst_id and a.booking_no='" . $row[csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", 'program_id', 'program_no');
								if (empty($program_no_array)) $program_no_array = array();*/
							} else if ($row[csf('booking_without_order')] == 1) {
								if ($batch_maintained == 0) {
									$fab_description_array = return_library_array("select a.id, a.product_name_details from product_details_master a, inv_grey_fabric_issue_dtls b, inv_issue_master c where a.id=b.prod_id and b.mst_id=c.id and c.booking_no='" . $row[csf('booking_no')] . "' and c.issue_basis=1 and c.issue_purpose=8 and c.entry_form in(16) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
								} else {
									$fab_description_array = return_library_array("select a.id, a.product_name_details from product_details_master a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and c.booking_no='" . $row[csf('booking_no')] . "' and c.booking_without_order=1 and c.entry_form in(2,22) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
								}
								if (empty($fab_description_array)) $fab_description_array = array();
							}
						}

						if ($row[csf('program_no')] == 0 || $row[csf('program_no')] =="") {
							$program_no_array = array();
						} else {
							$program_no_array[$row[csf('program_no')]] = $row[csf('program_no')];
						}

						if ($row[csf('booking_without_order')] == 0) {
							if ($batch_maintained == 0) {
								if ($row[csf('program_no')] > 0) {
									$fab_description_array = return_library_array("select a.id, a.product_name_details from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=16 and d.trans_type=2 and b.program_no='" . $row[csf('program_no')] . "' and d.po_breakdown_id='" . $row[csf('po_id')] . "' and c.issue_basis=3 and c.entry_form=16 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
								} else {
									$fab_description_array = return_library_array("select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id='" . $row[csf('po_id')] . "' and b.entry_form in(16) and b.trans_type in(2) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
								}
							} else {
								if ($row[csf('program_no')] > 0) {
									$fab_description_array = return_library_array("select a.id, a.product_name_details from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=2 and d.trans_type=1 and c.booking_id='" . $row[csf('program_no')] . "' and c.receive_basis=2 and d.po_breakdown_id='" . $row[csf('po_id')] . "' and c.booking_without_order=0 and c.receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 group by  a.id, a.product_name_details", 'id', 'product_name_details');
								} else {
									$fab_description_array = return_library_array("select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id='" . $row[csf('po_id')] . "' and b.entry_form in(2,13,22) and b.trans_type in(1,5) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
								}
							}
							if (empty($fab_description_array)) $fab_description_array = array();
						}
					}
					$body_part_ids="";
					/*
						*** N.B.  Bobypart id restriction with order and product id is closed as saved bodypart comes from barcode production so in update mode remains so; as per concern with higher personnel.


						if ($tblRow == 1) {
							$prodIds = implode(",", array_keys($fab_description_array));
							if ($row[csf('booking_without_order')] == 1) {
								$bodyPartData = sql_select("select b.prod_id, b.body_part_id from pro_grey_prod_entry_dtls b, inv_receive_master c where b.mst_id=c.id and c.booking_no='" . $row[csf('booking_no')] . "' and b.prod_id in($prodIds) and c.booking_without_order=1 and c.entry_form in(2,22) and b.status_active=1 and b.is_deleted=0 group by b.prod_id, b.body_part_id");
								foreach ($bodyPartData as $rowB) {
									$body_part_ids_array[$rowB[csf('prod_id')]] .= $rowB[csf('body_part_id')] . ",";
								}
							} else {
								$poIds = implode(",", array_keys($po_array));
								$bodyPartData = sql_select("select b.prod_id, b.po_breakdown_id, a.body_part_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($poIds) and b.entry_form in(2,22) and b.trans_type=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id in($prodIds) group by b.prod_id, b.po_breakdown_id, a.body_part_id");
								foreach ($bodyPartData as $rowB) {
									$body_part_ids_array[$rowB[csf('po_breakdown_id')]][$rowB[csf('prod_id')]] .= $rowB[csf('body_part_id')] . ",";
								}
							}
						}
						if ($row[csf('booking_without_order')] == 1) {
							$body_part_ids = chop($body_part_ids_array[$row[csf('prod_id')]], ',');
						} else {
							$body_part_ids = chop($body_part_ids_array[$row[csf('po_id')]][$row[csf('prod_id')]], ',');
						}
					*/

						echo "<td align='center' id='programNoTd_$tblRow'>";
						echo create_drop_down("cboProgramNo_" . $tblRow, 80, $program_no_array, '', 1, "-- Select --", $row[csf('program_no')], "load_item_desc(this.value,this.id);", $disbled_drop_down, "", "", "", "", "", "", "cboProgramNo[]");
						echo "</td>";

						echo "<td align='center' id='poNoTd_$tblRow'>";
						echo create_drop_down("cboPoNo_" . $tblRow, 130, $po_array, '', 1, "-- Select Po Number --", $row[csf('po_id')], "load_item_desc(this.value,this.id);", $disbled_drop_down, "", "", "", "", "", "", "cboPoNo[]");
						echo "</td>";
						echo "<td align='center' id='itemDescTd_$tblRow' title='".$fab_description_array[$row[csf('prod_id')]]."'>";
						echo create_drop_down("cboItemDesc_" . $tblRow, 180, $fab_description_array, "", 1, "-- Select Item Desc --", $row[csf('prod_id')], "load_body_part(this.value,this.id);", $disbled_drop_down, "", "", "", "", "", "", "cboItemDesc[]");
						echo "</td>";
						echo "<td align='center' id='bodyPartTd_$tblRow'>";
						echo create_drop_down("cboBodyPart_" . $tblRow, 120, $body_part, "", 1, "-- Select --", $row[csf('body_part_id')], "", $disbled_drop_down, $body_part_ids, "", "", "", "", "", "cboBodyPart[]");
						echo "</td>";
					} else if ($row[csf('batch_against')] == 5) {
						if ($tblRow == 1) {
							$po_array = return_library_array("SELECT a.id, a.po_number FROM wo_po_break_down a, pro_batch_create_dtls b WHERE a.id=b.po_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number", 'id', 'po_number');
							if (empty($po_array)) $po_array = array();
						}
						$po_no = $po_array[$row[csf('po_id')]];

						if ($roll_maintained == 1) {

							if ($batch_against == 2) {
								$disbled = "";
							} else {
								$disbled = "disabled='disabled'";
							}
							$disbled_drop_down = 1;
							if ($tblRow == 1) {
								$fab_description_array = return_library_array("select a.id, a.product_name_details from product_details_master a, pro_batch_create_dtls b where a.id=b.prod_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
							}

							if ($row[csf('program_no')] == 0) {
								$program_no_array = array();
							} else {
								$program_no_array[$row[csf('program_no')]] = $row[csf('program_no')];
							}
						} else {
							if ($batch_against != 2) {
								$disbled = "";
								$disbled_drop_down = 0;
							}

							$program_no_array = return_library_array("SELECT b.id as program_id, b.id as program_no FROM ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b WHERE a.dtls_id=b.id and a.po_id='" . $row[csf('po_id')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", 'program_id', 'program_no');
							if (empty($program_no_array)) $program_no_array = array();

							if ($batch_maintained == 0) {
								$fab_description_array = return_library_array("select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id='" . $row[csf('po_id')] . "' and b.entry_form in(16) and b.trans_type in(2) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
							} else {
								$fab_description_array = return_library_array("select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id='" . $row[csf('po_id')] . "' and b.entry_form in(2,13,22) and b.trans_type in(1,5) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');
							}
							if (empty($fab_description_array)) $fab_description_array = array();
						}

						if ($tblRow == 1) {
							$prodIds = implode(",", array_keys($fab_description_array));
							$poIds = implode(",", array_keys($po_array));
							$bodyPartData = sql_select("select b.prod_id, b.po_breakdown_id, a.body_part_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($poIds) and b.entry_form in(2,22) and b.trans_type=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id in($prodIds) group by b.prod_id, b.po_breakdown_id, a.body_part_id");
							foreach ($bodyPartData as $rowB) {
								$body_part_ids_array[$rowB[csf('po_breakdown_id')]][$rowB[csf('prod_id')]] .= $rowB[csf('body_part_id')] . ",";
							}
						}

						$body_part_ids = chop($body_part_ids_array[$row[csf('po_id')]][$row[csf('prod_id')]], ',');

						echo "<td align='center' id='programNoTd_$tblRow'>";
						echo create_drop_down("cboProgramNo_".$tblRow, 80, $program_no_array, "", 1, "-- Select --", $row[csf('program_no')], "", $disbled_drop_down, "", "", "", "", "", "", "cboProgramNo[]");
						echo "</td>";
						?>
						<td id='poNoTd_<? echo $tblRow; ?>'>
							<input type="text" name="cboPoNo[]" id="cboPoNo_<? echo $tblRow; ?>" class="text_boxes"
							style="width:120px;" placeholder="Double Click to Search"
							onDblClick="openmypage_po(<? echo $tblRow; ?>)"
							value="<? echo $po_no; ?>" <? echo $disbled; ?> readonly/>
						</td>
						<td id='itemDescTd_<? echo $tblRow; ?>' title="<? echo $fab_description_array[$row[csf('prod_id')]];?>">
							<? echo create_drop_down("cboItemDesc_" . $tblRow, 180, $fab_description_array, "", 1, "-- Select Item Desc --", $row[csf('prod_id')], "load_body_part(this.value,this.id);", $disbled_drop_down, "", "", "", "", "", "", "cboItemDesc[]"); ?>
						</td>
						<td align='center' id='bodyPartTd_<? echo $tblRow; ?>'>
							<? echo create_drop_down("cboBodyPart_" . $tblRow, 120, $body_part, "", 1, "-- Select --", $row[csf('body_part_id')], "", $disbled_drop_down, $body_part_ids, "", "", "", "", "", "cboBodyPart[]"); ?>
						</td>
						<?
					}
					?>
					<td>
						<?
						if ($batch_against == 2) $disbled_drop = 1; else $disbled_drop = 0;
						echo create_drop_down("cboDiaWidthType_" . $tblRow, 90, $fabric_typee, "", 1, "-- Select --", $row[csf('width_dia_type')], "", $disbled_drop, "", "", "", "", "", "", "cboDiaWidthType[]");
						?>
					</td>
					<td>
						<input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $tblRow; ?>" class="text_boxes"
						style="width:50px"
						value="<? if ($row[csf('roll_no')] != 0) echo $row[csf('roll_no')]; ?>" <? echo $disbled; ?>/>
						<input type="hidden" name="hideRollNo[]" id="hideRollNo_<? echo $tblRow; ?>"
						class="text_boxes"
						value="<? echo $row[csf('roll_id')]; ?>"/>
						<? if ($row[csf('batch_against')] == 5) $po_id = $row[csf('po_id')]; else $po_id = ""; ?>
						<input type="hidden" name="poId[]" id="poId_<? echo $tblRow; ?>" value="<? echo $po_id; ?>"
						class="text_boxes" readonly/>
						<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $tblRow; ?>"
						class="text_boxes"
						value="<? echo $row[csf('id')]; ?>"/>
						<!--<input type="hidden" name="barcodeNo_<? echo $tblRow; ?>" id="barcodeNo_<? echo $tblRow; ?>" value="<? echo $row[csf('barcode_no')]; ?>" />-->
					</td>
					<td>
						<input type="text" name="barcodeNo[]" id="barcodeNo_<? echo $tblRow; ?>" class="text_boxes"
						value="<? echo $row[csf('barcode_no')]; ?>" style="width:70px" placeholder="Display"
						readonly/>
					</td>
					<td>
						<input type="text" name="txtBatchQnty[]" id="txtBatchQnty_<? echo $tblRow; ?>"
						class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:60px"
						value="<? echo $row[csf('batch_qnty')]; ?>" <? echo $disbled; ?>/>
					</td>
					<td>
						<input type="text" name="txtPoBatchNo[]" id="txtPoBatchNo_<? echo $tblRow; ?>"
						class="text_boxes_numeric" style="width:45px"
						value="<? echo $row[csf('po_batch_no')]; ?>"
						disabled/>
					</td>
					<td width="65">
                    <?
                    if($roll_maintained==0)
					{?>
					
                        <input type="button" id="popopupid_<? echo $tblRow; ?>" name="popopupid[]" style="width:20px" class="formbuttonplasminus" value=":::" onClick="fnc_po_popup(<? echo $tblRow.','.$row[csf('program_no')]; ?>)" />
                    <?	
					}
					?>
						<input type="button" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px"
						class="formbuttonplasminus" value="+"
						onClick="add_break_down_tr(<? echo $tblRow; ?>)"/>
						<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px"
						class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tblRow; ?>);"/>
					</td>
				</tr>
				<?
			}
		}

		exit();
	}

	if ($action == "process_name_popup_______________") {
		echo load_html_head_contents("Process Name Info", "../../", 1, 1, '', '1', '');
		extract($_REQUEST);
		?>
		<script>

			$(document).ready(function (e) {
				setFilterGrid('tbl_list_search', -1);
			});

			var selected_id = new Array();
			var selected_name = new Array();

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

			function set_all() {
				var old = document.getElementById('txt_process_row_id').value;
				if (old != "") {
					old = old.split(",");
					for (var k = 0; k < old.length; k++) {
						js_set_value(old[k])
					}
				}
			}

			function js_set_value(str) {
            /*var currentRowColor=document.getElementById( 'search' + str ).style.backgroundColor;
             if(currentRowColor=='yellow')
             {
             var mandatory=$('#txt_mandatory' + str).val();
             var process_name=$('#txt_individual' + str).val();
             if(mandatory==1)
             {
             alert(process_name+" Subprocess is Mandatory. So You can't De-select");
             return;
             }
         }*/

         toggle(document.getElementById('search' + str), '#FFFFCC');

         if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
         	selected_id.push($('#txt_individual_id' + str).val());
         	selected_name.push($('#txt_individual' + str).val());

         }
         else {
         	for (var i = 0; i < selected_id.length; i++) {
         		if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
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

         $('#hidden_process_id').val(id);
         $('#hidden_process_name').val(name);
     }
 </script>

</head>

<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
			<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
			<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes"
			value="">
			<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
					<thead>
						<th width="50">SL</th>
						<th>Process Name</th>
					</thead>
				</table>
				<div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view"
				align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table"
				id="tbl_list_search">
				<?
				$i = 1;
				$process_row_id = '';
						$not_process_id_print_array = array(1, 2, 3, 4, 101, 120, 121, 122, 123, 124); //$mandatory_subprocess_array=array(33,63,65,94);
						$hidden_process_id = explode(",", $txt_process_id);
						foreach ($conversion_cost_head_array as $id => $name) {
							if (!in_array($id, $not_process_id_print_array)) {
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

								if (in_array($id, $hidden_process_id)) {
									if ($process_row_id == "") $process_row_id = $i; else $process_row_id .= "," . $i;
								}
								/*$mandatory=0;
							if(in_array($id,$mandatory_subprocess_array))
							{
								$mandatory=1;
							}*/
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
								id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
								<td width="50" align="center"><?php echo "$i"; ?>
								<input type="hidden" name="txt_individual_id"
								id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
								<input type="hidden" name="txt_individual"
								id="txt_individual<?php echo $i ?>"
								value="<? echo $name; ?>"/>
								<input type="hidden" name="txt_mandatory"
								id="txt_mandatory<?php echo $i ?>"
								value="<? echo $mandatory; ?>"/>
							</td>
							<td><p><? echo $name; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
				<input type="hidden" name="txt_process_row_id" id="txt_process_row_id"
				value="<?php echo $process_row_id; ?>"/>
			</table>
		</div>
		<table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all"
							onClick="check_all_data()"/>
							Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();"
							class="formbutton" value="Close" style="width:100px"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();
}



if ($action == "process_name_popup") {
	echo load_html_head_contents("Process Name Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		var selected_id = new Array();
		var selected_name = new Array();

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

		function set_all() {
			var old_seq = document.getElementById('txt_process_seq').value;
			var old = document.getElementById('txt_process_row_id').value;
			if (old != "") {
				old = old.split(",");
				if(old_seq!=""){
					oldArr = old_seq.split(",");
				}

				for (var k = 0; k < old.length; k++) {
					if(old_seq!=""){
						idSeq = oldArr[k].split("_");
						$('#txt_sequence'+idSeq[0]).val(idSeq[1]);
						//$('#txt_sequence'+old[k]).val(oldArr[k]);
					}

					js_set_value(old[k]);
				}
			}
		}

		function js_set_value(str) {
            /*var currentRowColor=document.getElementById( 'search' + str ).style.backgroundColor;
             if(currentRowColor=='yellow')
             {
             var mandatory=$('#txt_mandatory' + str).val();
             var process_name=$('#txt_individual' + str).val();
             if(mandatory==1)
             {
             alert(process_name+" Subprocess is Mandatory. So You can't De-select");
             return;
             }
         }*/

         toggle(document.getElementById('search' + str), '#FFFFCC');

         if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
         	selected_id.push($('#txt_individual_id' + str).val());
         	selected_name.push($('#txt_individual' + str).val());
         }
         else {
         	for (var i = 0; i < selected_id.length; i++) {
         		if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
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

         $('#hidden_process_id').val(id);
         $('#hidden_process_name').val(name);
     }

     function window_close(){

     	var old = document.getElementById('hidden_process_id').value;
     	if (old != "") {
     		old = old.split(",");
     		var seq='';
     		for (var k = 0; k < old.length; k++) {
     			if(seq==''){seq=old[k]+'_'+$('#txt_sequence'+old[k]).val();}
     			else{seq+=','+old[k]+'_'+$('#txt_sequence'+old[k]).val();}
     		}
     	}
     	$('#hidden_process_seq').val(seq);
			//var oldArr = old_seq.split(",");


			parent.emailwindow.hide();
		}

	</script>

</head>

<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
			<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
			<input type="hidden" name="hidden_process_seq" id="hidden_process_seq" class="text_boxes" value="">
			<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes"
			value="">
			<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
					<thead>
						<th width="50">SL</th>
						<th>Process Name</th>
						<th width="82">Sequence</th>
					</thead>
				</table>
				<div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view"
				align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table"
				id="tbl_list_search">
				<?
				$i = 1;
				$process_row_id = '';
						$not_process_id_print_array = array(1, 2, 3, 4, 101, 120, 121, 122, 123, 124); //$mandatory_subprocess_array=array(33,63,65,94);
						$hidden_process_id = explode(",", $txt_process_id);
						foreach ($conversion_cost_head_array as $id => $name) {
							if (!in_array($id, $not_process_id_print_array)) {
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

								if (in_array($id, $hidden_process_id)) {
									if ($process_row_id == "") $process_row_id = $i; else $process_row_id .= "," . $i;
								}
								/*$mandatory=0;
							if(in_array($id,$mandatory_subprocess_array))
							{
								$mandatory=1;
							}*/
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
								id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
								<td width="50" align="center"><?php echo "$i"; ?>
								<input type="hidden" name="txt_individual_id"
								id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
								<input type="hidden" name="txt_individual"
								id="txt_individual<?php echo $i ?>"
								value="<? echo $name; ?>"/>
								<input type="hidden" name="txt_mandatory"
								id="txt_mandatory<?php echo $i ?>"
								value="<? echo $mandatory; ?>"/>
							</td>
							<td><p><? echo $name; ?></p></td>
							<td width="65" align="center"><input type="text" id="txt_sequence<? echo $id ?>" name="txt_sequence<? echo $id ?>" value="" class="text_boxes_numeric" style=" width:50px;"></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
				<input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>

				<input type="hidden" name="txt_process_seq" id="txt_process_seq" value="<?php echo $process_seq; ?>"/>



			</table>
		</div>
		<table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all"
							onClick="check_all_data()"/>
							Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="window_close()"
							class="formbutton" value="Close" style="width:100px"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();
}



if ($action == "batch_no_creation") {
	//$batch_no_creation=return_field_value("batch_no_creation","variable_settings_production","company_name ='$data' and variable_list=24 and is_deleted=0 and status_active=1");
	$batch_no_creation = '';
	$batch_maintained = '';
	$sql = sql_select("select variable_list, batch_no_creation, batch_maintained from variable_settings_production where company_name=$data and variable_list in (24,13) and status_active=1 and is_deleted=0");
	foreach ($sql as $row) {
		if ($row[csf('variable_list')] == 13) {
			$batch_maintained = $row[csf('batch_maintained')];
		} else {
			$batch_no_creation = $row[csf('batch_no_creation')];
		}
	}

	if ($batch_no_creation != 1) $batch_no_creation = 0;
	if ($batch_maintained != 1) $batch_maintained = 0;

	echo "document.getElementById('batch_no_creation').value 				= '" . $batch_no_creation . "';\n";
	echo "document.getElementById('batch_maintained').value 				= '" . $batch_maintained . "';\n";
	echo "$('#txt_batch_number').val('');\n";
	echo "$('#update_id').val('');\n";
	if ($batch_no_creation == 1) {
		echo "$('#txt_batch_number').attr('readonly','readonly');\n";
	} else {
		echo "$('#txt_batch_number').removeAttr('readonly','readonly');\n";
	}

	exit();
}

if ($action == "roll_maintained") {
	//Add New category id 50, old=category id was 3

	$roll_maintained = return_field_value("fabric_roll_level", "variable_settings_production", "company_name ='$data' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	if ($roll_maintained == "" || $roll_maintained == 2) $roll_maintained = 0; else $roll_maintained = $roll_maintained;

	echo "document.getElementById('roll_maintained').value 				= '" . $roll_maintained . "';\n";
	exit();
}

if ($action == "show_color_listview") {
	$data = explode("**", $data);
	$booking_no = $data[0];
	$booking_without_order = $data[1];
	$batch_against = $data[2];
	$search_type = $data[3];
	$booking_entry_form = $data[4];
	$batch_qnty_array = array();
	$booking_no_column = ($search_type == 7) ? "a.sales_order_no" : "a.booking_no";
	$batch_data_array = sql_select("SELECT a.color_id, $booking_no_column booking_no, sum(b.batch_qnty) as qnty FROM pro_batch_create_mst a, pro_batch_create_dtls b WHERE a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=0 group by a.color_id, a.booking_no,$booking_no_column");
	foreach ($batch_data_array as $row) {
		$batch_qnty_array[$row[csf('color_id')]][$row[csf('booking_no')]] = $row[csf('qnty')];
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="330" class="rpt_table">
		<thead>
			<th width="25">SL</th>
			<th width="80">Color</th>
			<th width="75">Booking Qty.</th>
			<th width="75">Batch Qty.</th>
			<th>Balance</th>
		</thead>
		<?
		$i = 1;
		if ($search_type != 7)
		{
			if ($booking_without_order == 1) {
				if($booking_entry_form == 140)
				{
					/*$main_sql=sql_select("SELECT b.sample_type ,a.color_id , (case when  a.fabric_color is null or a.fabric_color<=0 then a.color_id else a.fabric_color end )  as fabric_color ,sum(b.req_dzn) as req_dzn, b.style_id  from sample_development_rf_color a, wo_non_ord_samp_booking_dtls b
					where a.mst_id=b.style_id and b.booking_no = '$booking_no' group by b.sample_type , a.color_id,a.fabric_color, b.style_id");*/

					$main_sql = sql_select("SELECT sample_type,color_id,fabric_color,sum(req_dzn) req_dzn, style_id from
						(SELECT b.sample_type ,a.color_id , (case when  a.fabric_color is null or a.fabric_color<=0 then a.color_id else a.fabric_color end )  as fabric_color ,b.req_dzn, b.style_id
						from sample_development_rf_color a, wo_non_ord_samp_booking_dtls b
						where a.mst_id=b.style_id and b.booking_no = '$booking_no'
						group by b.sample_type , a.color_id,a.fabric_color, b.style_id,b.req_dzn
						)
						group by sample_type,color_id,fabric_color, style_id");

					foreach($main_sql as $val)
					{
						$style_id_arr[$val[csf("style_id")]] = $val[csf("style_id")];
						$color_ids_arr[$val[csf("fabric_color")]] = $val[csf("fabric_color")];
					}

					$dtls_sql="select sample_mst_id, sample_name,sample_color, sum(sample_prod_qty) as qnty from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=117
					and sample_mst_id in (". implode(",", $style_id_arr).")
					group by sample_mst_id, sample_name,sample_color ";
					foreach(sql_select($dtls_sql) as $v)
					{
						$dtls_arr[$v[csf("sample_mst_id")]][$v[csf("sample_name")]][$v[csf("sample_color")]]=$v[csf("qnty")];
					}

					$color_name_arr = return_library_array("select id, color_name from lib_color where status_active =1 and id in (".implode(",", $color_ids_arr).")","id","color_name");

					foreach ($main_sql as $row)
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$batch_qnty = $batch_qnty_array[$row[csf('color_id')]][$booking_no];

						$qnty=($row[csf("req_dzn")]/12)*$dtls_arr[$row[csf("style_id")]][$row[csf("sample_type")]][$row[csf("color_id")]];


						$balance = $qnty - $batch_qnty;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
							onClick="put_country_data(<? echo $row[csf('color_id')]; ?>,'<? echo $color_name_arr[$row[csf('color_id')]]; ?>')">
							<td width="25"><? echo $i; ?></td>
							<td width="80"><p><? echo $color_name_arr[$row[csf('color_id')]];//$row[csf('color_name')]; ?></p></td>
							<td width="75" align="right"><p><? echo number_format($qnty, 2); ?>&nbsp;</p></td>
							<td width="75" align="right"><? echo number_format($batch_qnty, 2); ?>&nbsp;</td>
							<td align="right"><? echo number_format($balance, 2); ?></td>
						</tr>
						<?
						$i++;
					}
				}
				else
				{
					$sql = sql_select("select b.id, b.color_name, sum(a.grey_fabric) as qnty from wo_non_ord_samp_booking_dtls a, lib_color b where a.fabric_color=b.id and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 group by b.id, b.color_name");
				}
			}
			else
			{
				/*if($booking_entry_form == 139)
				{
					$main_sql=sql_select("SELECT b.sample_type ,a.color_id , (case when  a.fabric_color is null or a.fabric_color<=0 then a.color_id else a.fabric_color end )  as fabric_color , sum(b.req_dzn) as req_dzn, b.style_id  from sample_development_rf_color a, wo_booking_dtls b
					where a.mst_id=b.style_id and b.booking_no = '$booking_no' group by b.sample_type , a.color_id,a.fabric_color, b.style_id");

					foreach($main_sql as $val)
					{
						$style_id_arr[$val[csf("style_id")]] = $val[csf("style_id")];
						$color_ids_arr[$val[csf("fabric_color")]] = $val[csf("fabric_color")];
					}

					$dtls_sql="select sample_mst_id, sample_name,sample_color, sum(sample_prod_qty) as qnty from sample_development_dtls where status_active=1 and is_deleted=0 and entry_form_id=117
					and sample_mst_id in (". implode(",", $style_id_arr).")
					group by sample_mst_id, sample_name,sample_color ";
					foreach(sql_select($dtls_sql) as $v)
					{
						$dtls_arr[$v[csf("sample_mst_id")]][$v[csf("sample_name")]][$v[csf("sample_color")]]=$v[csf("qnty")];
					}

					$color_name_arr = return_library_array("select id, color_name from lib_color where status_active =1 and id in (".implode(",", $color_ids_arr).")","id","color_name");

					foreach ($main_sql as $row)
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$batch_qnty = $batch_qnty_array[$row[csf('color_id')]][$booking_no];

						$qnty=($row[csf("req_dzn")]/12)*$dtls_arr[$row[csf("style_id")]][$row[csf("sample_type")]][$row[csf("color_id")]];

						$balance = $qnty - $batch_qnty;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
							onClick="put_country_data(<? echo $row[csf('color_id')]; ?>,'<? echo $color_name_arr[$row[csf('color_id')]]; ?>')">
							<td width="25"><? echo $i; ?></td>
							<td width="80"><p><? echo $color_name_arr[$row[csf('color_id')]];//$row[csf('color_name')]; ?></p></td>
							<td width="75" align="right"><p><? echo number_format($qnty, 2); ?>&nbsp;</p></td>
							<td width="75" align="right"><? echo number_format($batch_qnty, 2); ?>&nbsp;</td>
							<td align="right"><? echo number_format($balance, 2); ?></td>
						</tr>
						<?
						$i++;
					}
				}
				else
				{*/
					$sql = sql_select("select b.id, b.color_name, sum(a.grey_fab_qnty) as qnty from wo_booking_dtls a, lib_color b where a.fabric_color_id=b.id and a.booking_no='$booking_no' and  a.status_active=1 and a.is_deleted=0 group by b.id, b.color_name");
				//}
				}
			}
			else
			{
				$sql = sql_select("select a.color_id id, (select c.color_name from lib_color c where c.id = a.color_id) color_name, sum(a.grey_qty) qnty, sum(a.finish_qty) finish_qty from fabric_sales_order_dtls a where a.job_no_mst='$booking_no' group by a.color_id");
			}

			if( $booking_entry_form != 140 )
			{
				foreach ($sql as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$batch_qnty = $batch_qnty_array[$row[csf('id')]][$booking_no];

					$balance = $row[csf('qnty')] - $batch_qnty;
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
						onClick="put_country_data(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('color_name')]; ?>')">
						<td width="25"><? echo $i; ?></td>
						<td width="80"><p><? echo $row[csf('color_name')]; ?></p></td>
						<td width="75" align="right"><p><? echo number_format($row[csf('qnty')], 2); ?>&nbsp;</p></td>
						<td width="75" align="right"><? echo number_format($batch_qnty, 2); ?>&nbsp;</td>
						<td align="right"><? echo number_format($balance, 2); ?></td>
					</tr>
					<?
					$i++;
				}

			}


			?>
		</table>
		<?
		exit();
	}

	if ($action == "batch_card_print") {
		// var_dump($_REQUEST);
		extract($_REQUEST);
		$data = explode('*', $data);
		$company = $data[0];
		$batch_update_id = $data[1];
		$batch_mst_update_id = str_pad($batch_update_id, 10, '0', STR_PAD_LEFT);
	//echo $batch_mst_update_id;die;
		$batch_sl_no = $data[2];
		$working_company = $data[6];
	//echo $data[3].$data[4];die;
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
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

		if ($db_type == 0) {
			$sql = "select a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic,a.dyeing_machine, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.remarks, a.collar_qty, a.cuff_qty, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id,b.is_sales from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic,a.dyeing_machine, a.extention_no, a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight,a.remarks,a.collar_qty, a.cuff_qty,b.is_sales";
		} else {
			$sql = "select a.id, a.batch_no, a.booking_no_id,a.booking_no,a.booking_without_order, a.color_id, a.batch_against, a.color_range_id, a.organic,a.dyeing_machine, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.remarks, a.collar_qty, a.cuff_qty, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id,b.is_sales from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.color_id, a.batch_against, a.color_range_id, a.organic ,a.dyeing_machine,a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight,a.remarks,a.collar_qty,a.cuff_qty,b.is_sales";
		}

		$dataArray = sql_select($sql);
		$po_id = array_filter(array_unique(explode(",", $dataArray[0][csf('po_id')])));

		$booking_no = $dataArray[0][csf('booking_no')];
		$non_order_arr=array();
		$sql_non_order=sql_select("select b.body_part_id,d.booking_no,d.style_des,d.sample_type from wo_non_ord_samp_booking_dtls d,pro_batch_create_mst a, pro_batch_create_dtls b where  a.id=b.mst_id and d.booking_no=a.booking_no and d.booking_no='$booking_no' and a.id=$batch_update_id and  d.body_part=b.body_part_id and d.status_active=1 and d.is_deleted=0 group by b.body_part_id,d.booking_no,d.style_des,d.sample_type");

		foreach ($sql_non_order as $nonOrder_row) {
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
			$sql_job=sql_select("select a.buyer_id, b.job_no as job_no_mst,b.booking_no,b.po_break_down_id,d.style_ref_no from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and d.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_type in(1,4) and c.id in (".implode(",",$po_id).") group by b.job_no,b.booking_no,a.buyer_id,b.po_break_down_id,d.style_ref_no");

			foreach ($sql_job as $job_row) {
				$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] 		= $job_row[csf('job_no_mst')];
				$job_arr[$job_row[csf('booking_no')]]["buyer_id"] 			= $job_row[csf('buyer_id')];
				$job_arr[$job_row[csf('booking_no')]]["style_ref"] 			= $job_row[csf('style_ref_no')];
			}

			$sql_sales=sql_select("select id,job_no,within_group,buyer_id,sales_booking_no,delivery_date from fabric_sales_order_mst where status_active=1 and is_deleted=0 and id in (".implode(",",$po_id).")");
			foreach ($sql_sales as $sales_row) {
				$sales_arr[$sales_row[csf('id')]]["po_number"] 		= $sales_row[csf('job_no')];
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
			if ($is_sales == 1) {
				$within_group = $sales_arr[$val]["within_group"];
				$po_number 	  = $sales_arr[$val]["po_number"];
				$ship_date 	  = $sales_arr[$val]["delivery_date"];
				if ($within_group == 1) {
					$sales_booking_no = $sales_arr[$val]["sales_booking_no"];
					$job_number = $job_arr[$sales_booking_no]["job_no_mst"];
					$buyer_id 	= $job_arr[$sales_booking_no]["buyer_id"];
					$job_style 	= $job_arr[$sales_booking_no]["style_ref"];
				}else{
					$job_number = "";
					$buyer_id 	= "";
					$job_style 	= "";
				}
			}else{
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

		?>
		<div style="width:980px;">
			<table width="980" cellspacing="0" align="center" border="0">
				<tr>
					<td colspan="6" align="center" style="font-size:22px">
						<strong><? echo $company_library[$working_company]; ?></strong></td>
						<td colspan="2" align="left">Print Time:<? echo $date = date("F j, Y, g:i a"); ?></td>
					</tr>
					<tr>
						<td colspan="6" align="center" style="font-size:18px"><strong><u>Batch Card</u></strong></td>
						<td colspan="2" id="barcode_img_id" align="right" style="font-size:24px"></td>
					</tr>
					<tr>
						<td colspan="8">
							<?
							if ($dataArray[0][csf('dyeing_machine')] != 0)
								{ ?>
									<strong>M/C	No:</strong>&nbsp;  <strong><? echo $machine_no_arr[$dataArray[0][csf('dyeing_machine')]];
								}
								else echo '&nbsp; ';
								?></strong>
							</td>
							<td>&nbsp; </td>
						</tr>
						<tr>
							<td colspan="6" align="left" style="font-size:18px"><strong><u>Reference Details</u></strong>
							</td>
							<td style="font-size:24px; border: solid 2px;" align="center" colspan="2">&nbsp;<strong><? echo $dataArray[0][csf('organic')]; ?></strong>
							</td>
						</tr>
						<tr>
							<td width="110"><strong>Batch No</strong></td>
							<td width="135px">:&nbsp;<strong><? echo $dataArray[0][csf('batch_no')]; ?></strong></td>
							<td width="110"><strong>Batch SL</strong></td>
							<td width="135px">:&nbsp;<strong><? echo $batch_sl_no; ?></strong></td>
							<td width="110"><strong>B. Color</strong></td>
							<td width="155px">:&nbsp;<strong><? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></strong></td>
							<td width="110"><strong>Color Ran.</strong></td>
							<td width="135px">:&nbsp;<strong><? echo $color_range[$dataArray[0][csf('color_range_id')]]; ?></strong></td>
						</tr>
						<tr>
							<td><strong>Batch Against</strong></td>
							<td>:&nbsp;<strong><? echo $batch_against[$dataArray[0][csf('batch_against')]]; ?></strong></td>
							<td><strong>Batch Ext.</strong></td>
							<td>:&nbsp;<strong><? echo $dataArray[0][csf('extention_no')]; ?></strong></td>
							<td><strong>Batch For</strong></td>
							<td>:&nbsp;<strong><? echo $batch_for[$dataArray[0][csf('batch_for')]]; ?></strong></td>
							<td><strong>B. Weight</strong></td>
							<td>:&nbsp;<strong><? echo $dataArray[0][csf('batch_weight')]; ?></strong></td>
						</tr>
						<tr>
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
							<?
							if ($dataArray[0][csf('batch_against')] == 3) {
								?>
								<td><strong>Booking no</strong></td>
								<td>:&nbsp;<strong><? echo $booking_id; ?></strong></td>

							<? } else { ?>
								<td><strong>Job</strong></td>
								<td>:&nbsp;<strong><? echo $job_no; ?></strong></td>
							<? }
							?>

							<td><strong>Order No</strong></td>
							<td>:&nbsp;<strong><? echo $po_number; ?></strong></td>
							<td><strong>Ship Date</strong></td>
							<td>
								:&nbsp;<strong><? if (trim($ship_date) != "0000-00-00" && trim($ship_date) != "") echo $ship_date; else echo "&nbsp;"; ?></strong></td>
							</tr>
							<tr>
								<td><strong>Collar Qty (Pcs)</strong></td>
								<td>:&nbsp;<strong><? echo $dataArray[0][csf('collar_qty')]; ?></strong></td>
								<td><strong>Cuff Qty (Pcs)</strong></td>
								<td>:&nbsp;<strong><? echo $dataArray[0][csf('cuff_qty')]; ?></strong></td>
								<td><strong>Int. Ref.</strong></td>
								<td>:&nbsp;<strong><? echo $internal_ref; ?></strong></td>
								<td><strong>File No</strong></td>
								<td>:&nbsp;<strong><? echo $file_nos; ?></strong></td>
							</tr>
							<tr>
								<td><strong>Remarks</strong></td>
								<td>:&nbsp;<strong><? echo $dataArray[0][csf('remarks')]; ?></strong></td>
								<td><strong>Style</strong></td>
								<td>:&nbsp;<strong><? echo $jobstyle; ?></strong></td>

								<td><strong>Style Des</strong></td>
								<td>:&nbsp;<strong><? echo $non_order_arr[$dataArray[0][csf('booking_no')]]["style_des"]; ?></strong></td>
								<td><strong>Sample Type</strong></td>
								<td>:&nbsp;<strong><? echo $sample_type_arr[$non_order_arr[$dataArray[0][csf('booking_no')]]["sample_type"]]; ?></strong></td>
							</tr>
						</table>
						<div style="float:left; font-size:17px;"><strong><u>Fabrication Details</u></strong></div>
						<table align="center" cellspacing="0" width="980" border="1" rules="all" class="rpt_table"
						style="border-top:none">
						<thead bgcolor="#dddddd" align="center">
							<tr>
								<th width="30">SL</th>
								<th width="60">Prog. No</th>
								<th width="80">Body part</th>
								<th width="150">Const. & Comp.</th>
								<th width="50">Fin. GSM</th>
								<th width="50">Fin. Dia</th>
								<th width="70">M/Dia X Gauge</th>
								<th width="70">D/W Type</th>
								<th width="60">S. Length</th>
								<th width="70">Grey Qty.</th>
								<th width="50">Roll No.</th>
								<th width="80">Yarn Lot</th>
								<th width="80"><strong>Yarn Suplier</strong></th>
								<th width="80">Yarn Count</th>
								<th>ID Code</th>
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

				$yarn_lot_data = sql_select("select  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot as yarn_lot, a.yarn_count, a.stitch_length as stitch_length,a.machine_no_id as machine_no_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.prod_id in ($batch_product_id)
					group by  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot, a.yarn_count, a.stitch_length,a.machine_no_id
					union all
					select  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot as yarn_lot, a.yarn_count, a.stitch_length as stitch_length,a.machine_no_id as machine_no_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b, inv_item_transfer_mst c where a.id=b.dtls_id and b.po_breakdown_id = c.from_order_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.prod_id in ($batch_product_id) group by  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot, a.yarn_count, a.stitch_length,a.machine_no_id");

				foreach ($yarn_lot_data as $rows) {
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

			$sql_dtls = "select b.id, a.batch_no, a.total_trims_weight, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.booking_without_order, a.process_id, a.extention_no, b.batch_qnty AS batch_qnty, b.roll_no, b.item_description, b.program_no, b.po_id, b.prod_id, b.body_part_id, b.width_dia_type from pro_batch_create_mst a,pro_batch_create_dtls b where a.company_id=$data[0] and a.id=b.mst_id and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";
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


				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="30"><? echo $i; // echo ",".$row[csf('prod_id')]."=".$row[csf('po_id')];?> </td>
					<td width="60" align="center"><p><? echo $row[csf('program_no')]; ?></p></td>
					<td width="80"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
					<td width="150"><p><? echo $desc[0] . "," . $desc[1]; ?></p></td>
					<td width="50" align="center"><p><? echo $desc[2]; ?></p></td>
					<td width="50" align="center"><p><? echo $desc[3]; ?></p></td>
					<td width="70" align="center"><p><? echo $dya_gage; ?></p></td>
					<td width="70"><p><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></p></td>
					<td width="60" align="center"><p><? echo $stitch; ?></p></td>
					<td width="70" align="right"><p><? echo number_format($row[csf('batch_qnty')], 2); ?></p>
					</td>
					<td align="center" width="50"><p><? echo $row[csf('roll_no')]; ?></p></td>
					<td width="80">
                        <p><? echo $yarn_lot; //$yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['lot'];
                        ?></p></td>
                        <td width="80">
                        <p><? echo rtrim($brand_suplier, ",");//$supplier_array_lib[$supplier_from_prod[$yarn_lot]];
                        ?></p></td>
                        <td width="80"><p><? echo $yarn_count_value; ?></p></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php
                    $total_roll_number+= $row[csf('roll_no')];
                    $total_batch_qty += $row[csf('batch_qnty')];
                    $i++;
                }
                ?>
                <tr>
                <td style="border:none;" colspan="9" align="right"><b>Sum:</b> <? //echo $b_qty;
                ?> </td>
                <td align="right"><b><? echo number_format($total_batch_qty, 2); ?> </b></td>
                <td align="center"><b><? echo $total_roll_number; ?> </b></td>
                <td colspan="4" style="border:none;">&nbsp;</td>
            </tr>
            <tr>
                <td style="border:none;" colspan="9" align="right"><b>Trims Weight:</b> <? //echo $b_qty;
                ?> </td>
                <td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')], 2); ?> </b>
                </td>
                <td colspan="5" style="border:none;"><p>&nbsp;<? echo $trims_item;?></p></td>
            </tr>
            <tr>
            	<td style="border:none;" colspan="9" align="right"><b>Total:</b></td>
            	<td align="right">
            		<b><? echo number_format($total_batch_qty + $dataArray[0][csf('total_trims_weight')], 2); ?> </b>
            	</td>
            	<td colspan="5" style="border:none;">&nbsp;</td>
            </tr>
            <tr>
            	<td colspan="15" align="right">&nbsp; </td>
            </tr>
            <tr>
            	<td colspan="15" align="right">
            		<?
            		$process = $dataArray[0][csf('process_id')];
            		$process_id = explode(',', $process);
					//print_r($process_id);
            		$process_value = '';
            		$i = 1;
            		foreach ($process_id as $val) {
            			if ($process_value == '') $process_value = $i . '. ' . $conversion_cost_head_array[$val]; else $process_value .= ", " . $i . '. ' . $conversion_cost_head_array[$val];
            			$i++;
            		}
            		?>
            		<table align="left" rules="all" class="rpt_table" width="980">
            			<tr>
            				<th align="left" style="font-size:20px;"><strong>Process Required</strong></th>
            			</tr>
            			<tr>
            				<td style="font-size:20px;" title="<? echo $process_value; ?>">
            					<p><? echo $process_value; ?></p>
            				</td>
            			</tr>
            			<tr>
            				<td align="left" style="font-size:19px;">
            					Heat Setting:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;
            					&nbsp;&nbsp;&nbsp;&nbsp;
            					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            					&nbsp; Loading Date & Time: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            					&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            					&nbsp; UnLoading Date & Time:&nbsp;
            				</td>
            			</tr>
            		</table>
            	</td>
            </tr>
        </table>
        <div style="float:left; margin-left:10px;"><strong> Quality Instruction(Hand Written)</strong></div>
        <table width="980" cellspacing="0" align="center">
        	<tr>
        		<td valign="top" align="left" width="480">
        			<table cellspacing="0" width="475" align="left" border="1" rules="all" class="rpt_table">
        				<tr>
        					<th>SL</th>
        					<th>Roll No</th>
        					<th>Roll Mark</th>
        					<th>Actual Dia</th>
        					<th>Roll Wgt.</th>
        					<th>Remarks</th>
        				</tr>
        				<tr height="30">
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>

        				</tr>
        				<tr height="30">
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        				</tr>
        				<tr height="30">
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        				</tr>
        				<tr height="30">
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        				</tr>
        				<tr height="30">
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        				</tr>
        				<tr height="30">
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        				</tr>
        			</table>
        		</td>
        		<td width="10" align="justify" valign="top"></td>
        		<td width="480" valign="top" align="right">
        			<table width="475" cellspacing="0" border="1" rules="all" class="rpt_table">
        				<tr>
        					<th>SL</th>
        					<th>Roll No</th>
        					<th>Actual Dia</th>
        					<th>Roll Wgt.</th>
        					<th>Remarks</th>
        				</tr>
        				<tr height="30">
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        				</tr>
        				<tr height="30">
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        				</tr>
        				<tr height="30">
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        				</tr>
        				<tr height="30">
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        				</tr>
        			</table>
        		</td>
        	</tr>
        	<tr>
        		<td width="480" valign="top">
        			<table width="475" cellspacing="0" border="1" rules="all" class="rpt_table">
        				<tr>
        					<th align="left"><strong>Shade Result(<i>Hand Written</i>)</strong></th>
        				</tr>
        				<tr>
        					<td colspan="1" style="width:475px; height:80px">&nbsp;</td>
        				</tr>
        			</table>
        		</td>
        		<td width="10" align="justify" valign="top">&nbsp;</td>
        		<td width="480" valign="top" align="right">
        			<table cellspacing="0" border="1" rules="all" class="rpt_table" width="475">
        				<tr>
        					<th align="left" colspan="3"><strong>Shrinkage(<i>Hand Written</i>)</strong></th>
        				</tr>
        				<tr>
        					<th><b>Length % </b></th>
        					<th><b>Width % </b></th>
        					<th><b> Twist % </b></th>
        				</tr>
        				<tr height="30">
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        				</tr>
        				<tr height="30">
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        				</tr>
        				<tr height="30">
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        				</tr>
        				<tr height="30">
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        					<td>&nbsp; </td>
        				</tr>
        			</table>
        		</td>
        	</tr>
        	<tr>
        		<td width="980" colspan="3">
        			<table cellspacing="0" border="1" rules="all" class="rpt_table" width="980">
        				<tr>
        					<th align="center"><strong>Dyeing Information(<i>Hand Written</i>)</strong></th>
        				</tr>
        				<tr>
        					<td style="width:980px; height:120px">&nbsp;</td>
        				</tr>
        			</table>
        		</td>
        	</tr>
        </table>
        <br>
        <?
        echo signature_table(52, $company, "980px");
        ?>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    	function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 30,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $dataArray[0][csf('batch_no')]; ?>');
    </script>
    <?
    exit();
}

if ($action == "batch_card_print_2") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$batch_update_id = $data[1];
	$working_company = $data[7];
	$batch_mst_update_id = str_pad($batch_update_id, 10, '0', STR_PAD_LEFT);
	//echo $batch_mst_update_id;die;
	$batch_sl_no = $data[2];
	//echo $data[3].$data[4];die;
	$roll_maintained = $data[8];
	if($roll_maintained==0)
	{
		echo "<p width='250'><b>Sorry, This Print Report is in roll level</b></p>";
		die;
	}
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$machine_library = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	//$lapdip_no_arr = return_library_array("select job_no_mst,lapdip_no from wo_po_lapdip_approval_info where status_active=1 and is_deleted=0 and approval_status=3", 'job_no_mst', 'lapdip_no');

	$lapdip_no=sql_select("select job_no_mst,color_name_id,po_break_down_id,lapdip_no from wo_po_lapdip_approval_info where status_active=1 and is_deleted=0 and approval_status=3");
	foreach ($$lapdip_no as $row) {
		$lapdip_no_arr[$row[csf('job_no_mst')]][$row[csf('po_break_down_id')]][$row[csf('color_name_id')]]['lapdip_no'] = $row[csf('lapdip_no')];
	}


	$job_array = array();
	$job_sql = "select distinct(a.buyer_name) as buyer_name,a.style_ref_no, a.job_no_prefix_num, a.job_no, b.pub_shipment_date, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
		$job_array[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
		$job_array[$row[csf('job_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
	}

	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id ,a.is_sales
		from pro_batch_create_mst a, pro_batch_create_dtls b
		where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty, a.cuff_qty, a.SAVE_STRING ,a.is_sales ";
	} else {
		$sql = "select a.id, a.batch_no, a.booking_no_id,a.booking_no,a.booking_without_order, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id,a.is_sales
		from pro_batch_create_mst a, pro_batch_create_dtls b
		where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.batch_no, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty,a.cuff_qty, a.SAVE_STRING,a.is_sales";
	}
	//echo $sql;
	$dataArray = sql_select($sql);

	$po_number = "";
	$job_number = "";
	$job_style = "";
	$buyer_id = "";
	$ship_date = "";
	$internal_ref = "";
	$file_nos = "";
	$po_id = array_unique(explode(",", $dataArray[0][csf('po_id')]));
	$booking_no = $dataArray[0][csf('booking_no')];
	$batch_against_id = $dataArray[0][csf('batch_against')];
	$batch_booking_id = $dataArray[0][csf('booking_no_id')];
	$batch_product_id = $dataArray[0][csf('prod_id')];
	$batch_booking_without = $dataArray[0][csf('booking_without_order')];

	if ($dataArray[0][csf('is_sales')] == 1) {
		$sales_order_id = $po_id[0];
		$sales_data = sql_select("select id,job_no,sales_booking_no,within_group,buyer_id,style_ref_no, po_buyer from fabric_sales_order_mst where id=$sales_order_id");

		if ($sales_data[0][csf("within_group")] == 1) {
			$booking_data = sql_select("select b.job_no,a.buyer_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no='$booking_no' and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 group by b.job_no,a.buyer_id");
			$job_number = $booking_data[0][csf("job_no")];
			$buyer_id = $booking_data[0][csf("buyer_id")];
			$po_number = $sales_data[0][csf("job_no")];
			$job_style = $job_array[$job_number]['style_ref_no'];
			$ship_date = "";
			$sales_buyer_id = $sales_data[0][csf("po_buyer")];
		} else {
			$po_number = $sales_data[0][csf("job_no")];
			$job_number = "";
			$buyer_id = $sales_data[0][csf("buyer_id")];
			$job_style = $sales_data[0][csf("style_ref_no")];
			$ship_date = "";
			$sales_buyer_id = $sales_data[0][csf("buyer_id")];
		}

	} else {
		foreach ($po_id as $val) {
			if ($po_number == "") $po_number = $job_array[$val]['po']; else $po_number .= ',' . $job_array[$val]['po'];
			if ($job_number == "") $job_number = $job_array[$val]['job']; else $job_number .= ',' . $job_array[$val]['job'];
			if ($job_style == "") $job_style = $job_array[$val]['style']; else $job_style .= ',' . $job_array[$val]['style'];
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
	//$booking_without_order=return_field_value( "booking_no as booking_no", "wo_non_ord_samp_booking_mst","booking_no=$booking_no","booking_no");

	if ($dataArray[0][csf('booking_without_order')] == 1) {
		$booking_without_order = sql_select("select booking_no_prefix_num, buyer_id from wo_non_ord_samp_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
		$booking_id = $booking_without_order[0][csf('booking_no_prefix_num')];
		$buyer_id_booking = $booking_without_order[0][csf('buyer_id')];
	} else {
		$booking_with_order = sql_select("select booking_no_prefix_num, buyer_id from wo_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
		$booking_id = $booking_with_order[0][csf('booking_no_prefix_num')];
		$buyer_id_booking = $booking_with_order[0][csf('buyer_id')];
	}
	if ($job_no!="") {$job_cond="and job_no_mst in('$job_no')";}else{$job_cond="";}
	$lapdip_no=sql_select("select job_no_mst,color_name_id,po_break_down_id,lapdip_no from wo_po_lapdip_approval_info where status_active=1 and is_deleted=0 and approval_status=3 $job_cond");
	foreach ($lapdip_no as $row) {
		//$lapdip_no_arr[$row[csf('job_no_mst')]][$row[csf('po_break_down_id')]][$row[csf('color_name_id')]]['lapdip_no'] = $row[csf('lapdip_no')];
		$lapdip_no_arr[$row[csf('job_no_mst')]][$row[csf('color_name_id')]]['lapdip_no'] = $row[csf('lapdip_no')];
	}
	?>
	<div style="width:1060px;">
		<table width="1060" cellspacing="0" align="center" border="0" style="font-size: 17px;">
			<tr>
				<td colspan="6" align="center" style="font-size:22px">
					<strong><? echo $company_library[$working_company]; ?></strong></td>
					<td colspan="2" align="left">Print Time:<? echo $date = date("F j, Y, g:i a"); ?></td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:18px"><strong><u>Batch Card</u></strong></td>
					<td colspan="2" id="barcode_img_id" align="right" style="font-size:24px"></td>
				</tr>
				<tr>
					<td colspan="8">&nbsp; </td>
					<td>&nbsp; </td>
				</tr>
				<tr>
					<td colspan="6" align="left" style="font-size:18px"><strong><u>Reference Details</u></strong>
					</td>
					<td style="font-size:24px; border: solid 2px;" align="center" colspan="2">
						&nbsp;<? echo $dataArray[0][csf('organic')]; ?></td>
					</tr>
					<tr>
						<td width="120"><strong>Batch No</strong></td>
						<td width="135px">:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
						<td width="120"><strong>Batch SL</strong></td>
						<td width="135px">:&nbsp;<? echo $batch_sl_no; ?></td>
						<td width="120"><strong>B. Color</strong></td>
						<td width="135px">:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
						<td width="120"><strong>Color Ran.</strong></td>
						<td width="135px">:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range_id')]]; ?></td>
					</tr>
					<tr>
						<td><strong>Batch Against</strong></td>
						<td>:&nbsp;<? echo $batch_against[$dataArray[0][csf('batch_against')]]; ?></td>
						<td><strong>Batch Ext.</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('extention_no')]; ?></td>
						<td><strong>Lab dip No</strong></td>
						<td>:&nbsp;<?
						//echo $lapdip_no_arr[$job_no][$dataArray[0][csf('po_id')]][$dataArray[0][csf('color_id')]]['lapdip_no'];
						echo $lapdip_no_arr[$job_no][$dataArray[0][csf('color_id')]]['lapdip_no'];
						//echo $dataArray[0][csf('extention_no')]; ?></td>
						<td><strong>B. Weight</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?></td>
					</tr>
					<tr>
						<td style="font-size:14px;"><strong>Buyer</strong></td>
						<td style="font-size:14px;">
							:&nbsp;
							<?
							if($dataArray[0][csf('is_sales')] ==1)
							{

								echo $buyer_arr[$sales_buyer_id];
							}
							else if ($dataArray[0][csf('batch_against')] == 3)
							{
								echo $buyer_arr[$buyer_id_booking];
							}
							else
							{
								echo $buyer_arr[$buyer];
							}
							?>

						</td>
						<?
						if ($dataArray[0][csf('batch_against')] == 3) {
							?>
							<td  style="font-size:14px;"><strong>Booking no</strong></td>
							<td  style="font-size:14px;">:&nbsp;<? echo $booking_id; ?></td>

						<? } else { ?>
							<td  style="font-size:14px;"><strong>Job</strong></td>
							<td  style="font-size:14px;">:&nbsp;<? echo $job_no; ?></td>
						<? }
						?>
						<td><strong>Batch For</strong></td>
						<td>:&nbsp;<? echo $batch_for[$dataArray[0][csf('batch_for')]]; ?></td>
						<td><strong>Ship Date</strong></td>
						<td>
							:&nbsp;<? if (trim($ship_date) != "0000-00-00" && trim($ship_date) != "") echo implode(",", array_unique(explode(",", $ship_date))); else echo "&nbsp;"; ?></td>
						</tr>
						<tr>
							<td><strong>Collar Qty (Pcs)</strong></td>
							<td>:&nbsp;<? echo $dataArray[0][csf('collar_qty')]; ?></td>
							<td><strong>Cuff Qty (Pcs)</strong></td>
							<td>:&nbsp;<? echo $dataArray[0][csf('cuff_qty')]; ?></td>
							<td><strong>Order/FSO No</strong></td>
							<td>:&nbsp;<? echo $po_number; ?></td>
							<td><strong>File No</strong></td>
							<td>:&nbsp;<? echo $file_nos; ?></td>
						</tr>
						<tr>
							<td><strong>Dying Machine</strong></td>
							<td>:&nbsp;
								<?
								if ($db_type == 2) {
									$dyeing_machine = return_field_value("(machine_no || '-' || brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
								} else if ($db_type == 0) {
									$dyeing_machine = return_field_value("concat(machine_no,'-',brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
								}
								echo $dyeing_machine;
								?></td>
								<td><strong>Remarks</strong></td>
								<td>:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
								<td  style="font-size:14px;"><strong>Int. Ref.</strong></td>
								<td  style="font-size:14px;">:&nbsp;<? echo $internal_ref; ?></td>
								<td><strong>Style Ref.</strong></td>
								<td>:&nbsp;<? echo $jobstyle; ?></td>
							</tr>
							<tr>
								<td><strong>Booking No.</strong></td>
								<td>:&nbsp;<? echo $dataArray[0][csf('booking_no')]; ?></td>
							</tr>
						</table>
						<br/>
						<div style="float:left; font-size:17px;"><strong><u>Fabrication Details</u></strong></div>
						<table align="center" cellspacing="0" width="1060" border="1" rules="all" class="rpt_table"
						style="border-top:none;font-size: 17px;">
						<thead bgcolor="#dddddd" align="center">
							<tr>
								<th width="30">SL</th>
								<th width="60">Prog. No</th>
								<th width="80">Machine / Knitting Com</th>
								<th width="80">Body part</th>
								<th width="150">Const. & Comp.</th>
								<th width="50"  style="font-size:14px;">Fin. GSM</th>
								<th width="50"  style="font-size:14px;">Fin. Dia</th>
								<th width="70">M/Dia X Gauge</th>
								<th width="70"  style="font-size:14px;">D/W Type</th>
								<th width="60">S. Length</th>
								<th width="70">Grey Qty.</th>
								<th width="50">Roll No.</th>
								<th width="80">Yarn Lot</th>
								<th width="80"><strong>Brand</strong></th>
								<th width="80"  style="font-size:14px;">Yarn Count</th>
								<th>ID Code</th>
							</tr>
						</thead>
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

			/*$yarn_lot_arr=array();
			$sample_arr=array();
			$yarn_count=array();
			$s_length=array();
			if($batch_against_id==3 && $batch_booking_without==1)
			{
				$yarn_lot_data=sql_select("select  p.booking_id, a.prod_id, a.yarn_lot, a.yarn_count, a.stitch_length,a.machine_no_id from inv_receive_master p, pro_grey_prod_entry_dtls a where  p.id=a.mst_id and p.booking_id='$batch_booking_id' and p.booking_without_order=1 and a.prod_id in($batch_product_id) and p.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0");
				foreach($yarn_lot_data as $rows)
				{
					$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['yarncount']=$rows[csf('yarn_count')];
					$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['stitch_length']=$rows[csf('stitch_length')];
					$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['samplelot']=$rows[csf('yarn_lot')];
					$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['machine_no_id']=$rows[csf('machine_no_id')];
				}
			}
			else
			{
				/*if($db_type==0)
				{
					$yarn_lot_data=sql_select("select  a.brand_id, b.po_breakdown_id, a.prod_id, group_concat(distinct(a.yarn_lot)) as yarn_lot, a.yarn_count, group_concat(distinct(a.stitch_length)) as stitch_length, group_concat(distinct(a.machine_no_id)) as machine_no_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where  a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id");
				}
				else if($db_type==2)
				{
					$yarn_lot_data=sql_select("select  a.brand_id, b.po_breakdown_id, a.prod_id, LISTAGG(CAST(a.yarn_lot AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.yarn_lot) as yarn_lot, a.yarn_count, LISTAGG(CAST(a.stitch_length AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.stitch_length) as stitch_length, LISTAGG(CAST(a.machine_no_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.machine_no_id) as machine_no_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22)   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id,a.yarn_count,a.brand_id");
				}

				foreach($yarn_lot_data as $rows)
				{
					$yarn_lot=explode(",",$rows[csf('yarn_lot')]);
					$stitch_length_arr=explode(",",$rows[csf('stitch_length')]);
					$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['lot']=implode(", ",array_unique($yarn_lot));
					$yarn_count[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['yarn_count']=$rows[csf('yarn_count')];
					$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['stitch_length']=$rows[csf('stitch_length')];
					$yarn_count[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['brand_id']=$rows[csf('brand_id')];
					$machine_no_id[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['machine_no_id']=$rows[csf('machine_no_id')];

					$sample_arr[$rows[csf('prod_id')]]['yarncount']=$rows[csf('yarn_count')];
					$sample_arr[$rows[csf('prod_id')]]['stitch_length']=$rows[csf('stitch_length')];
					$sample_arr[$rows[csf('prod_id')]]['brand_id']=$rows[csf('brand_id')];
					$sample_arr[$rows[csf('prod_id')]]['samplelot']=implode(", ",array_unique($yarn_lot));
				}

				$yarn_lot_data=sql_select("select  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot as yarn_lot, a.yarn_count, a.stitch_length as stitch_length,a.machine_no_id as machine_no_id
				from pro_grey_prod_entry_dtls a, order_wise_pro_details b
				where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

				foreach($yarn_lot_data as $rows)
				{
					$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['lot'].=$rows[csf('yarn_lot')].",";
					$yarn_count[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['yarn_count'].=$rows[csf('yarn_count')].",";
					$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['stitch_length'].=$rows[csf('stitch_length')].",";
					$yarn_count[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['brand_id'].=$rows[csf('brand_id')].",";
					$machine_no_id[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['machine_no_id'].=$rows[csf('machine_no_id')].",";
				}

			}

			//var_dump($sample_arr);
			//echo $yarn_lot_arr[1939][3833]['lot'];
			$fin_feb_data=sql_select("select a.id,a.program_no,c.machine_gg,c.machine_dia,b.color_type_id,c.fabric_dia from pro_batch_create_dtls a, ppl_planning_info_entry_mst b,ppl_planning_info_entry_dtls c where a.program_no=c.id and b.id=c.mst_id");
			$fin_dia=array();
			$dia_type=array();
			$machine_dia=array();
			$color_type=array();
			foreach($fin_feb_data as $d_rows)
			{
				$fin_dia[$d_rows[csf('program_no')]]['f_dia']=$d_rows[csf('fabric_dia')];
				$machine_gg[$d_rows[csf('program_no')]]['m_gauge']=$d_rows[csf('machine_gg')];
				$machine_dia[$d_rows[csf('program_no')]]['m_dia']=$d_rows[csf('machine_dia')];
				$color_type[$d_rows[csf('program_no')]]['color_type']=$d_rows[csf('color_type_id')];
			}*/
			//var_dump($yarn_count);
			/*$sql_dtls="select a.booking_no_id, a.booking_without_order, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no, b.item_description, b.program_no, b.po_id, b.prod_id, b.body_part_id, b.width_dia_type,count(b.width_dia_type) as num_of_rows
	from pro_batch_create_mst a,pro_batch_create_dtls b
	where a.company_id=$data[0] and a.id=b.mst_id and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
	group by a.booking_no_id,a.booking_without_order,b.prod_id,b.po_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type order by b.program_no";

	$sql_dtls="select a.booking_no_id, a.booking_without_order, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no, b.item_description, b.program_no, b.po_id, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows, d.machine_no_id, e.knitting_source, e.knitting_company, e.id as rcv_id
	from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
	where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
	group by a.booking_no_id,a.booking_without_order,b.prod_id,b.po_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, e.knitting_source, e.knitting_company, e.id order by b.program_no";*/

	$booking_no_id = $data[6];
	/*$receive_basis=return_field_value("receive_basis","inv_receive_master","booking_id='".$booking_no_id."' and entry_form in(2,22)");*/
	if ($batch_booking_without == 1) {
		$receive_basis = return_field_value("receive_basis", "inv_receive_master", "booking_no='" . $booking_no . "' and entry_form in(2,22)");
	} else {

		$receive_basis = return_field_value("receive_basis", "inv_receive_master", "booking_id='" . $booking_no_id . "' and entry_form in(2,22)");

	}

	if ($receive_basis == 0 || $receive_basis == 1) {
		$machine_info = "d.machine_dia,d.machine_gg,";
	} else if ($receive_basis == 2) {
		$machine_info = "";
	}

	if ($db_type == 0) {
		$sql_dtls = "select a.booking_no_id,e.receive_basis,e.booking_id, a.booking_without_order, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows,d.machine_dia,d.machine_gg, d.machine_no_id, group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count) as yarn_count, d.stitch_length as stitch_length, group_concat(d.brand_id) as brand_id, e.knitting_source, e.knitting_company
		from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
		where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
		group by a.booking_no_id,e.booking_id,d.machine_dia,d.machine_gg, a.booking_without_order,e.receive_basis,b.prod_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, d.stitch_length, e.knitting_source, e.knitting_company order by b.program_no";

	} else {
		$sql_dtls = "select a.booking_no_id,e.receive_basis,e.booking_id,a.booking_without_order, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows, d.machine_no_id,d.machine_dia,d.machine_gg, LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count, d.stitch_length as stitch_length, LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id, e.knitting_source, e.knitting_company
		from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
		where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
		group by a.booking_no_id,e.receive_basis,d.machine_dia,d.machine_gg, e.booking_id,a.booking_without_order,b.prod_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, d.stitch_length, e.knitting_source, e.knitting_company order by b.program_no";
	}

	//echo $sql_dtls;//die;
	$sql_result = sql_select($sql_dtls);
	foreach ($sql_result as $row) {
		if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				//echo $row[csf('prod_id')].'='.$row[csf('po_id')];
		$desc = explode(",", $row[csf('item_description')]);

				/*if($batch_against_id==3 && $row[csf('booking_without_order')]==1)
		{
			$yarn_lot=$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['samplelot'];
			$y_count=$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['yarncount'];
			$stitch=$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['stitch_length'];
			$yarn_count_value=$yarncount[$y_count];
			$dya_gage=$dya_gauge_arr[$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['machine_no_id']]["dia_width"]."<br>".$dya_gauge_arr[$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['machine_no_id']]["gauge"];

		}
		else
		{
			$y_count=chop($yarn_count[$row[csf('prod_id')]][$row[csf('po_id')]]['yarn_count'],",");
			$y_count_id=array_unique(explode(',',$y_count));
			$yarn_count_value='';
			//$dya_gage=$dya_gauge_arr[$machine_no_id[$row[csf('prod_id')]][$row[csf('po_id')]]['machine_no_id']]["dia_width"]."<br>".$dya_gauge_arr[$machine_no_id[$row[csf('prod_id')]][$row[csf('po_id')]]['machine_no_id']]["gauge"];
			foreach($y_count_id as $val)
			{
				if($val>0)
				{
					if($yarn_count_value=='') $yarn_count_value=$yarncount[$val]; else $yarn_count_value.=", ".$yarncount[$val];
				}
			}

			$machine_no_id_arr=array_unique(explode(',',chop($machine_no_id[$row[csf('prod_id')]][$row[csf('po_id')]]['machine_no_id'],",")));
			$dya_gage="";
			foreach($machine_no_id_arr as $machine_id)
			{
				$dya_gage=$dya_gauge_arr[$machine_id]["dia_width"]."<br>".$dya_gauge_arr[$machine_id]["gauge"];
			}

		$stitch=implode(", ", array_unique(explode(",",chop($yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['stitch_length'],','))));
		$yarn_lot=implode(", ", array_unique(explode(",",chop($yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['lot'],','))));
	}*/

	$yarn_lot = implode(",", array_unique(explode(",", $row[csf('yarn_lot')])));
	$y_count = array_unique(explode(",", $row[csf('yarn_count')]));
	$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
	$yarn_count_value = "";
	foreach ($y_count as $val) {
		if ($val > 0) {
			if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
		}
	}
	$brand_value = "";
	foreach ($brand_id as $bid) {
		if ($bid > 0) {
			if ($brand_value == '') $brand_value = $brand_name_arr[$bid]; else $brand_value .= ", " . $brand_name_arr[$bid];
		}
	}
				//if($row[csf('receive_basis')]==0 || $row[csf('receive_basis')]==1) //from Entry page
				//{
	$machine_dia_width = $row[csf('machine_dia')];
	$machine_gauge = $row[csf('machine_gg')];
				//}
				/*else if($row[csf('receive_basis')]==2) //Knitting Plan
		{
			 $program_data=sql_select("select width_dia_type, machine_dia, machine_gg, machine_id from ppl_planning_info_entry_dtls where id='".$row[csf('booking_id')]."'");

			$machine_dia_width=$program_data[0][csf('machine_dia')];
			$machine_gauge=$program_data[0][csf('machine_gg')];
		}*/

		$stitch = implode(",", array_unique(explode(",", $row[csf('stitch_length')])));
				//
		$dya_gage = $machine_dia_width . "<br>" . $machine_gauge;
				//$yarn_lot_arr[$rows[csf('prod_id')]][$rows['po_breakdown_id']]['stitch_length']
				//$st_len=implode(", ", array_unique(explode(",",$stitch)));
				//$machine_dia_up=$machine_array_lib_dia[$machine_dia[$row[csf('program_no')]]['m_dia']];
				//$machine_gauge_up=$machine_array_lib_gauge[$machine_dia[$row[csf('program_no')]]['m_gauge']];
		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<td width="30" align="center"><? echo $i; ?></td>
			<td width="60" align="center" style="word-break:break-all;"><? echo $row[csf('program_no')]; ?></td>
			<?
			if ($row[csf('knitting_source')] == 1) {
				$machin_knit_com = $machine_library[$row[csf('machine_no_id')]];
			} else {
				$machin_knit_com = $supplier_library[$row[csf('knitting_company')]];
			}
			?>
			<td width="80" style="word-break:break-all;" align="center"><? echo $machin_knit_com; ?></td>
			<td width="80" style="word-break:break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
			<td width="150" style="word-break:break-all;"><? echo $desc[0] . "," . $desc[1]; ?></td>
			<td width="50" align="center" style="word-break:break-all; font-size:14px;"><? echo $desc[2]; ?></td>
			<td width="50" align="center" style="word-break:break-all; font-size:14px;"><? echo $desc[3]; ?></td>
			<td width="70" align="center" style="word-break:break-all;"><? echo $dya_gage; ?></td>
			<td width="70" style="word-break:break-all; font-size:14px;"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
			<td width="60" align="center" style="word-break:break-all;"><? echo $stitch; ?></td>
			<td width="70" align="right" style="word-break:break-all;"><? echo number_format($row[csf('batch_qnty')], 2); ?></td>
			<td align="center" width="50" style="word-break:break-all;"><? echo $row[csf('num_of_rows')]; ?></td>
			<td width="80" style="word-break:break-all;"><? echo implode(',', array_unique(explode(",", $yarn_lot))); ?></td>
			<td width="80" style="word-break:break-all;">
				<?
						/*$all_suplier="";
				$yarn_lot_arr=explode(",",$yarn_lot);
				foreach($yarn_lot_arr as $lot)
				{
					if($supplier_array_lib[$supplier_from_prod[$lot]]!="")
					{
						$all_suplier.=$supplier_array_lib[$supplier_from_prod[$lot]].",";
					}
				}
				$all_suplier=implode(',',array_unique(explode(",",chop($all_suplier,","))));
				echo $all_suplier;*/
				echo $brand_value;
				?></td>
				<td width="80" style="word-break:break-all; font-size:14px;"align="center" ><? echo $yarn_count_value; ?></td>
				<td>&nbsp;</td>
			</tr>
			<?php
			$total_roll_number += $row[csf('num_of_rows')];
			$total_batch_qty += $row[csf('batch_qnty')];
			$i++;
		}
		?>
		<tr>
                <td style="border:none;" colspan="10" align="right"><b>Sum:</b> <? //echo $b_qty;
                ?> </td>
                <td align="right"><b><? echo number_format($total_batch_qty, 2); ?> </b></td>
                <td align="center"><b><? echo $total_roll_number; ?> </b></td>
                <td colspan="4" style="border:none;">&nbsp;</td>
            </tr>
            <tr>
                <td style="border:none;" colspan="10" align="right"><b>Trims Weight:</b> <? //echo $b_qty;
                ?> </td>
                <td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')], 2); ?> </b>
                </td>
                <td colspan="5" style="border:none;">&nbsp;</td>
            </tr>
            <tr>
            	<td style="border:none;" colspan="10" align="right"><b>Total:</b></td>
            	<td align="right">
            		<b><? echo number_format($total_batch_qty + $dataArray[0][csf('total_trims_weight')], 2); ?> </b>
            	</td>
            	<td colspan="5" style="border:none;">&nbsp;</td>
            </tr>
            <tr>
            	<td colspan="16" align="right">&nbsp; </td>
            </tr>
            <tr>
            	<td colspan="16">
            		<?
            		$save_str = $dataArray[0][csf('SAVE_STRING')];
            		$save_ref = explode(",", $save_str);
            		if (!empty($dataArray[0][csf('SAVE_STRING')]) > 0) {
            			?>
            			<table align="left" rules="all" class="rpt_table" width="100%" border="1"
            			style="font-size: 12px;">
            			<thead>
            				<tr>
            					<th align="left" style="font-size:20px;" colspan="4"><strong>Trims
            					Details</strong></th>
            				</tr>
            				<tr>
            					<th width="50">SL</th>
            					<th width="250">Item Description</th>
            					<th width="150">Weight In Kg</th>
            					<th>Remarks</th>
            				</tr>
            			</thead>
            			<tbody>
            				<?
            				$i = 1;
            				foreach ($save_ref as $data_ref) {
            					$data_ref = explode("_", $data_ref);
            					?>
            					<tr>
            						<td align="center"><? echo $i; ?></td>
            						<td><? echo $data_ref[0]; ?> </td>
            						<td align="right"><? echo number_format($data_ref[1], 2); ?></td>
            						<td align="center"><? echo $data_ref[2]; ?></td>
            					</tr>
            					<?
            					$i++;
            				}
            				?>
            			</tbody>
            		</table>
            		<?
            	}
            	?>

            </td>
        </tr>
        <tr>
        	<td colspan="16" align="right">
        		<?
        		$process = $dataArray[0][csf('process_id')];
        		$process_id = explode(',', $process);
					//print_r($process_id);
        		$process_value = '';
        		$i = 1;
        		foreach ($process_id as $val) {
        			if ($process_value == '') $process_value = $i . '. ' . $conversion_cost_head_array[$val]; else $process_value .= ", " . $i . '. ' . $conversion_cost_head_array[$val];
        			$i++;
        		}
        		?>
        		<table align="left" rules="all" class="rpt_table" width="1060" style="font-size: 17px;">
        			<tr>
        				<th align="left" style="font-size:20px;"><strong>Process Required</strong></th>
        			</tr>
        			<tr>
        				<td style="font-size:20px;" title="<? echo $process_value; ?>">
        					<p><? echo $process_value; ?></p>
        				</td>
        			</tr>
        			<tr>
        				<td align="left" style="font-size:19px;">
        					Heat Setting:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;
        					&nbsp;&nbsp;&nbsp;&nbsp;
        					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        					&nbsp; Loading Date & Time: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        					&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        					&nbsp; UnLoading Date & Time: &nbsp;
        				</td>
        			</tr>
        		</table>
        	</td>
        </tr>
    </table>
    <div style="float:left; margin-left:10px;"><strong> Quality Instruction(Hand Written)</strong></div>
    <table width="1060" cellspacing="0" align="center" style="font-size: 17px;">
    	<tr>
    		<td valign="top" align="left" width="520">
    			<table cellspacing="0" width="515" align="left" border="1" rules="all" class="rpt_table"
    			style="font-size: 17px;">
    			<tr>
    				<th>SL</th>
    				<th>Roll No</th>
    				<th>Roll Mark</th>
    				<th>Actual Dia</th>
    				<th>Roll Wgt.</th>
    				<th>Remarks</th>
    			</tr>
    			<tr height="30">
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    			</tr>
    			<tr height="30">
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    			</tr>
    			<tr height="30">
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    			</tr>
    			<tr height="30">
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    			</tr>
    			<tr height="30">
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    			</tr>
    			<tr height="30">
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    			</tr>
    		</table>
    	</td>
    	<td width="10" align="justify" valign="top"></td>
    	<td width="520" valign="top" align="right">
    		<table width="515" cellspacing="0" border="1" rules="all" class="rpt_table"
    		style="font-size: 17px;">
    		<tr>
    			<th>SL</th>
    			<th>Roll No</th>
    			<th>Actual Dia</th>
    			<th>Roll Wgt.</th>
    			<th>Remarks</th>
    		</tr>
    		<tr height="30">
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    		</tr>
    		<tr height="30">
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    		</tr>
    		<tr height="30">
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    		</tr>
    		<tr height="30">
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    		</tr>
    	</table>
    </td>
</tr>
<tr>
	<td width="520" valign="top">
		<table width="515" cellspacing="0" border="1" rules="all" class="rpt_table"
		style="font-size: 17px;">
		<tr>
			<th align="left"><strong>Shade Result(<i>Hand Written</i>)</strong></th>
		</tr>
		<tr>
			<td colspan="1" style="width:475px; height:80px">&nbsp;</td>
		</tr>
	</table>
</td>
<td width="10" align="justify" valign="top">&nbsp;</td>
<td width="520" valign="top" align="right">
	<table cellspacing="0" border="1" rules="all" class="rpt_table" width="515"
	style="font-size: 17px;">
	<tr>
		<th align="left" colspan="3"><strong>Shrinkage(<i>Hand Written</i>)</strong></th>
	</tr>
	<tr>
		<th><b>Length % </b></th>
		<th><b>Width % </b></th>
		<th><b> Twist % </b></th>
	</tr>
	<tr height="30">
		<td>&nbsp; </td>
		<td>&nbsp; </td>
		<td>&nbsp; </td>
	</tr>
	<tr height="30">
		<td>&nbsp; </td>
		<td>&nbsp; </td>
		<td>&nbsp; </td>
	</tr>
	<tr height="30">
		<td>&nbsp; </td>
		<td>&nbsp; </td>
		<td>&nbsp; </td>
	</tr>
	<tr height="30">
		<td>&nbsp; </td>
		<td>&nbsp; </td>
		<td>&nbsp; </td>
	</tr>
</table>
</td>
</tr>
<tr>
	<td width="1060" colspan="3">
		<table cellspacing="0" border="1" rules="all" class="rpt_table" width="1060"
		style="font-size: 17px;">
		<tr>
			<th colspan="2" align="center"><strong>Dyeing & Finishing Information(<i>Hand
			Written</i>)</strong></th>
		</tr>
		<tr>
			<th> Dyeing</th>
			<th> Finishing</th>
		</tr>
		<tr>
			<td style="width:530px; height:120px">&nbsp;</td>
			<td style="width:530px; height:120px">&nbsp;</td>
		</tr>
	</table>
</td>
</tr>
</table>
<br>
<div style="margin-top: -80px;">
	<?
	echo signature_table(52, $company, "1060px");
	?>
</div>
</div>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 30,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $dataArray[0][csf('batch_no')]; ?>');
    </script>
    <?
    exit();
}

if ($action == "batch_card_print_3") {
	//echo load_html_head_contents("Batch Info","../../", 1, 1, '','','');
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$batch_update_id = $data[1];
	$working_company = $data[7];
	$batch_mst_update_id = str_pad($batch_update_id, 10, '0', STR_PAD_LEFT);
	//echo $batch_mst_update_id;die;
	$batch_sl_no = $data[2];
	$booking_no_id = $data[6];
	//echo $data[3].$data[4];die;
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$machine_library = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	//$booking_color_type_arr = return_library_array("select booking_no,color_type from wo_booking_dtls", 'booking_no', 'color_type');//fabric_sales_order_dtls



	$job_array = array();
	$job_sql = "select distinct(a.buyer_name) as buyer_name,a.style_ref_no, a.job_no_prefix_num, a.job_no, b.pub_shipment_date, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
		$job_array[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
	}

	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no,a.batch_date, a.booking_no_id, a.booking_no,a.booking_without_order,a.sales_order_no,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, group_concat(b.barcode_no) as barcode_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.batch_no,a.batch_date, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id, a.sales_order_no,a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty, a.cuff_qty, a.SAVE_STRING";
	} else {
		$sql = "select a.id, a.batch_no,a.batch_date, a.booking_no_id,a.booking_no,a.booking_without_order,a.sales_order_no, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id,LISTAGG(CAST(b.barcode_no AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.barcode_no) AS barcode_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.batch_no,a.batch_date, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for,a.sales_order_no, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty,a.cuff_qty, a.SAVE_STRING";
	}
	//echo $sql;
	$dataArray = sql_select($sql);

	$po_number = "";
	$job_number = "";
	$job_style = "";
	$buyer_id = "";
	$ship_date = "";
	$internal_ref = "";
	$file_nos = "";
	$po_id = array_unique(explode(",", $dataArray[0][csf('po_id')]));
	$barcode_no = implode(",",array_unique(explode(",", $dataArray[0][csf('barcode_no')])));
	$booking_no = $dataArray[0][csf('booking_no')];
	$batch_against_id = $dataArray[0][csf('batch_against')];
	$batch_booking_id = $dataArray[0][csf('booking_no_id')];
	$batch_product_id = $dataArray[0][csf('prod_id')];
	$sales_order_no = $dataArray[0][csf('sales_order_no')];
	$batch_booking_without = $dataArray[0][csf('booking_without_order')];
	foreach ($po_id as $val) {
		if ($po_number == "") $po_number = $job_array[$val]['po']; else $po_number .= ',' . $job_array[$val]['po'];
		if ($job_number == "") $job_number = $job_array[$val]['job']; else $job_number .= ',' . $job_array[$val]['job'];
		if ($job_style == "") $job_style = $job_array[$val]['style']; else $job_style .= ',' . $job_array[$val]['style'];
		if ($buyer_id == "") $buyer_id = $job_array[$val]['buyer']; else $buyer_id .= ',' . $job_array[$val]['buyer'];
		if ($ship_date == "") $ship_date = change_date_format($job_array[$val]['ship_date']); else $ship_date .= ',' . change_date_format($job_array[$val]['ship_date']);

		if ($internal_ref == "") $internal_ref = $job_array[$val]['ref']; else $internal_ref .= ',' . $job_array[$val]['ref'];
		if ($job_array[$val]['file_no'] > 0) {
			if ($file_nos == "") $file_nos = $job_array[$val]['file_no']; else $file_nos .= ',' . $job_array[$val]['file_no'];
		}
	}

	$job_no = implode(",", array_unique(explode(",", $job_number)));
	$jobstyle = implode(",", array_unique(explode(",", $job_style)));
	$buyer = implode(",", array_unique(explode(",", $buyer_id)));
	$internal_ref = implode(",", array_unique(array_filter(explode(",", $internal_ref))));
	$file_nos = implode(",", array_unique(explode(",", $file_nos)));
	//$booking_without_order=return_field_value( "booking_no as booking_no", "wo_non_ord_samp_booking_mst","booking_no=$booking_no","booking_no");

	if ($dataArray[0][csf('booking_without_order')] == 1) {
		$booking_without_order = sql_select("select a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id,b.fabric_color from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$company and a.booking_no='$booking_no' and a.booking_type=4");
		foreach ($booking_without_order as $row) {
			$color_type_array2[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('fabric_color')]]['color_type_id'] = $row[csf('color_type_id')];
			$booking_id = $row[csf('booking_no_prefix_num')];
			$buyer_id_booking = $row[csf('buyer_id')];
		}


	} else {
		$booking_with_order = sql_select("select booking_no_prefix_num, buyer_id from wo_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
		$booking_id = $booking_with_order[0][csf('booking_no_prefix_num')];
		$buyer_id_booking = $booking_with_order[0][csf('buyer_id')];

		$color_sql = "select  b.booking_no,c.color_type_id,c.body_part_id,c.lib_yarn_count_deter_id,b.fabric_color_id as gmts_color_id, c.gsm_weight from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company and a.booking_no='$booking_no' and a.booking_type=1 and b.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.booking_no,c.color_type_id,b.fabric_color_id,c.body_part_id,c.lib_yarn_count_deter_id, c.gsm_weight";
		$color_sql_result = sql_select($color_sql);
		foreach ($color_sql_result as $row) {
			$color_type_array[$row[csf('booking_no')]][$row[csf('body_part_id')]]['color_type_id'] = $row[csf('color_type_id')];
			$color_type_array_precost[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]]['color_type_id'] = $row[csf('color_type_id')];
		}
	}
	$sales_sql = "select b.job_no_mst as booking_no,b.color_type_id,b.body_part_id from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company and a.job_no='$sales_order_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.job_no_mst ,b.color_type_id,b.body_part_id";
	$sales_result = sql_select($sales_sql);
	foreach ($sales_result as $row) {
		$sales_color_type_array[$row[csf('booking_no')]][$row[csf('body_part_id')]]['color_type_id'] = $row[csf('color_type_id')];
	}


	?>
	<div style="width:1060px;">
		<table width="1060" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="6" align="center" style="font-size:22px">
					<strong><? echo $company_library[$working_company]; ?></strong></td>
					<td colspan="2" style="font-size:21px" align="left">Print Time:<? echo $date = date("F j, Y, g:i a"); ?></td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:18px"><strong><u>Batch Card</u></strong></td>
					<td colspan="2" id="barcode_img_id" align="right" style="font-size:24px"></td>
				</tr>
				<tr>
					<td colspan="8">&nbsp; </td>
					<td>&nbsp; </td>
				</tr>
				<tr>
					<td colspan="6" align="left" style="font-size:22px"><strong><u>Reference Details</u></strong>
					</td>
					<td style="font-size:24px; border: solid 2px;" align="center" colspan="2">
						&nbsp;<? echo $dataArray[0][csf('organic')]; ?></td>
					</tr>
					<tr style="font-size:21px">
						<td width="120"><strong>Batch No</strong></td>
						<td width="135px">:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
						<td width="120"><strong>Batch Date</strong></td>
						<td width="135px">:&nbsp;<? echo change_date_format($dataArray[0][csf('batch_date')]); ?></td>
						<td width="120"><strong>B. Color</strong></td>
						<td width="135px">:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
						<td width="120"><strong>Color Ran.</strong></td>
						<td width="135px">:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range_id')]]; ?></td>
					</tr>
					<tr style="font-size:21px">
						<td><strong>Batch Against</strong></td>
						<td>:&nbsp;<? echo $batch_against[$dataArray[0][csf('batch_against')]]; ?></td>
						<td width="120"><strong>Batch SL</strong></td>
						<td width="135px">:&nbsp;<? echo $batch_sl_no; ?></td>
						<td><strong>Batch For</strong></td>
						<td>:&nbsp;<? echo $batch_for[$dataArray[0][csf('batch_for')]]; ?></td>
						<td><strong>B. Weight</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?></td>
					</tr>
					<tr style="font-size:21px">
						<td><strong>Buyer</strong></td>
						<td>
							:&nbsp;<? if ($dataArray[0][csf('batch_against')] == 3) echo $buyer_arr[$buyer_id_booking]; else echo $buyer_arr[$buyer]; ?></td>
							<td><strong>Batch Ext.</strong></td>
							<td>:&nbsp;<? echo $dataArray[0][csf('extention_no')]; ?></td>
							<td><strong>Order No</strong></td>
							<td>:&nbsp;<? echo $po_number; ?></td>
							<td><strong>Ship Date</strong></td>
							<td>
								:&nbsp;<? if (trim($ship_date) != "0000-00-00" && trim($ship_date) != "") echo implode(",", array_unique(explode(",", $ship_date))); else echo "&nbsp;"; ?></td>
							</tr>
							<tr style="font-size:21px">
								<td><strong>Collar Qty (Pcs)</strong></td>
								<td>:&nbsp;<? echo $dataArray[0][csf('collar_qty')]; ?></td>
								<?
								if ($dataArray[0][csf('batch_against')] == 3) {
									?>
									<td><strong>Booking no</strong></td>
									<td>:&nbsp;<? echo $booking_id; ?></td>

								<? } else { ?>
									<td><strong>Job</strong></td>
									<td>:&nbsp;<? echo $job_no; ?></td>
								<? }
								?>
								<td><strong>Int. Ref.</strong></td>
								<td>:&nbsp;<? echo $internal_ref; ?></td>
								<td><strong>File No</strong></td>
								<td>:&nbsp;<? echo $file_nos; ?></td>
							</tr>
							<tr style="font-size:21px">
								<td><strong>Dying Machine</strong></td>
								<td>:&nbsp;
									<?
									if ($db_type == 2) {
										$dyeing_machine = return_field_value("(machine_no || '-' || brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
									} else if ($db_type == 0) {
										$dyeing_machine = return_field_value("concat(machine_no,'-',brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
									}
									echo $dyeing_machine;
									?></td>
									<td><strong>Cuff Qty (Pcs)</strong></td>
									<td>:&nbsp;<? echo $dataArray[0][csf('cuff_qty')]; ?></td>
									<td><strong>Booking No.</strong></td>
									<td>:&nbsp;<? echo $dataArray[0][csf('booking_no')]; ?></td>
									<td><strong>Style Ref.</strong></td>
									<td>:&nbsp;<? echo $jobstyle; ?></td>
								</tr>
								<tr>
									<td><strong>Remarks</strong></td>
									<td colspan="6">:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
								</tr>

							</table>
							<div style="float:left; font-size:22px;"><strong><u>Fabrication Details</u></strong></div>
							<table align="center" cellspacing="0" style="font-size:21px" width="1060" border="1" rules="all" class="rpt_table"
							style="border-top:none">
							<thead bgcolor="#dddddd" align="center">
								<tr>
									<th width="30">SL</th>
									<th width="60">Prog. No</th>
									<th width="80">Machine / Knitting Com</th>
									<th width="80">Body part</th>
									<th width="80">Color Type</th>
									<th width="150">Const. & Comp.</th>
									<th width="50">Fin. GSM</th>
									<th width="50">Fin. Dia</th>
									<th width="70">M/Dia X Gauge</th>
									<th width="70">D/W Type</th>
									<th width="60">S. Length</th>
									<th width="70">Grey Qty.</th>
									<th width="50">Total Roll</th>
									<th width="80">Yarn Lot</th>
									<th width="80"><strong>Brand</strong></th>
									<th width="80">Yarn Count</th>
									<th>ID Code</th>
								</tr>
							</thead>
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

			/*$yarn_lot_arr=array();
			$sample_arr=array();
			$yarn_count=array();
			$s_length=array();
			if($batch_against_id==3 && $batch_booking_without==1)
			{
				$yarn_lot_data=sql_select("select  p.booking_id, a.prod_id, a.yarn_lot, a.yarn_count, a.stitch_length,a.machine_no_id from inv_receive_master p, pro_grey_prod_entry_dtls a where  p.id=a.mst_id and p.booking_id='$batch_booking_id' and p.booking_without_order=1 and a.prod_id in($batch_product_id) and p.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0");
				foreach($yarn_lot_data as $rows)
				{
					$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['yarncount']=$rows[csf('yarn_count')];
					$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['stitch_length']=$rows[csf('stitch_length')];
					$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['samplelot']=$rows[csf('yarn_lot')];
					$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['machine_no_id']=$rows[csf('machine_no_id')];
				}
			}
			else
			{
				/*if($db_type==0)
				{
					$yarn_lot_data=sql_select("select  a.brand_id, b.po_breakdown_id, a.prod_id, group_concat(distinct(a.yarn_lot)) as yarn_lot, a.yarn_count, group_concat(distinct(a.stitch_length)) as stitch_length, group_concat(distinct(a.machine_no_id)) as machine_no_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where  a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id");
				}
				else if($db_type==2)
				{
					$yarn_lot_data=sql_select("select  a.brand_id, b.po_breakdown_id, a.prod_id, LISTAGG(CAST(a.yarn_lot AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.yarn_lot) as yarn_lot, a.yarn_count, LISTAGG(CAST(a.stitch_length AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.stitch_length) as stitch_length, LISTAGG(CAST(a.machine_no_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.machine_no_id) as machine_no_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22)   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id,a.yarn_count,a.brand_id");
				}

				foreach($yarn_lot_data as $rows)
				{
					$yarn_lot=explode(",",$rows[csf('yarn_lot')]);
					$stitch_length_arr=explode(",",$rows[csf('stitch_length')]);
					$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['lot']=implode(", ",array_unique($yarn_lot));
					$yarn_count[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['yarn_count']=$rows[csf('yarn_count')];
					$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['stitch_length']=$rows[csf('stitch_length')];
					$yarn_count[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['brand_id']=$rows[csf('brand_id')];
					$machine_no_id[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['machine_no_id']=$rows[csf('machine_no_id')];

					$sample_arr[$rows[csf('prod_id')]]['yarncount']=$rows[csf('yarn_count')];
					$sample_arr[$rows[csf('prod_id')]]['stitch_length']=$rows[csf('stitch_length')];
					$sample_arr[$rows[csf('prod_id')]]['brand_id']=$rows[csf('brand_id')];
					$sample_arr[$rows[csf('prod_id')]]['samplelot']=implode(", ",array_unique($yarn_lot));
				}

				$yarn_lot_data=sql_select("select  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot as yarn_lot, a.yarn_count, a.stitch_length as stitch_length,a.machine_no_id as machine_no_id
				from pro_grey_prod_entry_dtls a, order_wise_pro_details b
				where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

				foreach($yarn_lot_data as $rows)
				{
					$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['lot'].=$rows[csf('yarn_lot')].",";
					$yarn_count[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['yarn_count'].=$rows[csf('yarn_count')].",";
					$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['stitch_length'].=$rows[csf('stitch_length')].",";
					$yarn_count[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['brand_id'].=$rows[csf('brand_id')].",";
					$machine_no_id[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['machine_no_id'].=$rows[csf('machine_no_id')].",";
				}

			}

			//var_dump($sample_arr);
			//echo $yarn_lot_arr[1939][3833]['lot'];
			$fin_feb_data=sql_select("select a.id,a.program_no,c.machine_gg,c.machine_dia,b.color_type_id,c.fabric_dia from pro_batch_create_dtls a, ppl_planning_info_entry_mst b,ppl_planning_info_entry_dtls c where a.program_no=c.id and b.id=c.mst_id");
			$fin_dia=array();
			$dia_type=array();
			$machine_dia=array();
			$color_type=array();
			foreach($fin_feb_data as $d_rows)
			{
				$fin_dia[$d_rows[csf('program_no')]]['f_dia']=$d_rows[csf('fabric_dia')];
				$machine_gg[$d_rows[csf('program_no')]]['m_gauge']=$d_rows[csf('machine_gg')];
				$machine_dia[$d_rows[csf('program_no')]]['m_dia']=$d_rows[csf('machine_dia')];
				$color_type[$d_rows[csf('program_no')]]['color_type']=$d_rows[csf('color_type_id')];
			}*/
			//var_dump($yarn_count);
			/*$sql_dtls="select a.booking_no_id, a.booking_without_order, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no, b.item_description, b.program_no, b.po_id, b.prod_id, b.body_part_id, b.width_dia_type,count(b.width_dia_type) as num_of_rows
	from pro_batch_create_mst a,pro_batch_create_dtls b
	where a.company_id=$data[0] and a.id=b.mst_id and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
	group by a.booking_no_id,a.booking_without_order,b.prod_id,b.po_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type order by b.program_no";

	$sql_dtls="select a.booking_no_id, a.booking_without_order, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no, b.item_description, b.program_no, b.po_id, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows, d.machine_no_id, e.knitting_source, e.knitting_company, e.id as rcv_id
	from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
	where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
	group by a.booking_no_id,a.booking_without_order,b.prod_id,b.po_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, e.knitting_source, e.knitting_company, e.id order by b.program_no";*/


			/* if($dataArray[0][csf('booking_without_order')]==1 && $batch_against_id==3)
	 {
		 $receive_basis=return_field_value("receive_basis","inv_receive_master","booking_id='".$booking_no_id."' and booking_no='$booking_no' and entry_form in(2,22)");
	 }
	 else
	 {
		$receive_basis=return_field_value("receive_basis","inv_receive_master","booking_id='".$booking_no_id."' and entry_form in(2,22)");
	 }

	 if($receive_basis==0 || $receive_basis==1)
	 {
		$machine_info="d.machine_dia,d.machine_gg,";
	 }
	 else  if($receive_basis==2)
	 {
		$machine_info="";
	}*/



	/*if ($db_type == 0) {
		$recv_basis_grop = " group_concat(a.receive_basis) AS receive_basis";
	} else {
		$recv_basis_grop = " LISTAGG(a.receive_basis, ',') WITHIN GROUP (ORDER BY a.receive_basis) AS receive_basis";
	}

	if ($dataArray[0][csf('booking_without_order')] == 1 && $batch_against_id == 3) {
				//echo 'A';
				// $receive_basis=return_field_value("$recv_basis_grop","inv_receive_master","booking_id='".$booking_no_id."' and booking_no='$booking_no' and entry_form in(2,22)");
		$receive_basis = return_field_value("$recv_basis_grop", "inv_receive_master a,pro_roll_details b", "a.id=b.mst_id  and (a.booking_id='" . $booking_no_id . "' OR b.barcode_no in($barcode_no)) and a.entry_form in(2,22) and b.entry_form in(2,22)", "receive_basis");
	} else {
				 //echo $booking_no_id.'ddddd';

		$receive_basis = return_field_value("$recv_basis_grop", "inv_receive_master a,pro_roll_details b", "a.id=b.mst_id  and (a.booking_id='" . $booking_no_id . "' OR b.barcode_no in($barcode_no)) and a.entry_form in(2,22) and b.entry_form in(2,22)", "receive_basis");
				//echo "select a.receive_basis from inv_receive_master a,pro_roll_details b where a.id=b.mst_id and (a.booking_id='".$booking_no_id."' OR b.barcode_no in($barcode_no)) and a.entry_form in(2,22) and b.entry_form in(2,22)";
	}*/
			//echo $receive_basis.'fffg';

	$receive_basis = sql_select("select a.receive_basis from inv_receive_master a,pro_roll_details b where a.id=b.mst_id  and (a.booking_id='" . $booking_no_id . "' OR b.barcode_no in($barcode_no)) and a.entry_form in(2,22) and b.entry_form in(2,22) group by a.receive_basis");

	foreach ($receive_basis as $val)
	{
		$receive_basis_arr[$val[csf("receive_basis")]];
	}

	//$receivebasis = array_unique(explode(",", $receive_basis));

	$receivebasis = array_filter($receive_basis_arr);

	foreach ($receivebasis as $rcvid) {
		if ($rcvid == 0 || $rcvid == 1 || $rcvid == 11) {
			$machine_info = "d.machine_dia,d.machine_gg,";
		} else {
			$machine_info = "";
		}
	}

	if ($db_type == 0) {
		$sql_dtls = "select a.booking_no_id,a.color_id,e.receive_basis,e.booking_id, a.booking_without_order,a.is_sales, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no,d.febric_description_id,d.gsm, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows,$machine_info d.machine_no_id, group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count) as yarn_count, d.stitch_length as stitch_length, group_concat(d.brand_id) as brand_id, e.knitting_source, e.knitting_company, group_concat(c.barcode_no) as barcode_no
		from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
		where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
		group by a.booking_no_id,a.color_id,e.booking_id,$machine_info a.booking_without_order,a.is_sales,e.receive_basis,b.prod_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, d.stitch_length,d.febric_description_id,d.gsm, e.knitting_source, e.knitting_company order by b.program_no";

	} else {
		$sql_dtls = "select a.booking_no_id,a.color_id,e.receive_basis,e.booking_id,a.booking_without_order,a.is_sales, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no,d.febric_description_id,d.gsm, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows, d.machine_no_id,$machine_info LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count, d.stitch_length as stitch_length, LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id, e.knitting_source, e.knitting_company, LISTAGG(CAST(c.barcode_no AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY c.barcode_no) as barcode_no
		from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
		where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
		group by a.booking_no_id,a.color_id,e.receive_basis,$machine_info e.booking_id,a.booking_without_order,a.is_sales,b.prod_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, d.stitch_length,d.febric_description_id,d.gsm, e.knitting_source, e.knitting_company order by b.program_no";
	}

	//echo $sql_dtls;//die;
	$sql_result = sql_select($sql_dtls);
	$all_barcode = "";
	foreach ($sql_result as $row) {
		if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				//echo $row[csf('prod_id')].'='.$row[csf('po_id')];
		$desc = explode(",", $row[csf('item_description')]);

				/*if($batch_against_id==3 && $row[csf('booking_without_order')]==1)
		{
			$yarn_lot=$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['samplelot'];
			$y_count=$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['yarncount'];
			$stitch=$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['stitch_length'];
			$yarn_count_value=$yarncount[$y_count];
			$dya_gage=$dya_gauge_arr[$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['machine_no_id']]["dia_width"]."<br>".$dya_gauge_arr[$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['machine_no_id']]["gauge"];

		}
		else
		{
			$y_count=chop($yarn_count[$row[csf('prod_id')]][$row[csf('po_id')]]['yarn_count'],",");
			$y_count_id=array_unique(explode(',',$y_count));
			$yarn_count_value='';
			//$dya_gage=$dya_gauge_arr[$machine_no_id[$row[csf('prod_id')]][$row[csf('po_id')]]['machine_no_id']]["dia_width"]."<br>".$dya_gauge_arr[$machine_no_id[$row[csf('prod_id')]][$row[csf('po_id')]]['machine_no_id']]["gauge"];
			foreach($y_count_id as $val)
			{
				if($val>0)
				{
					if($yarn_count_value=='') $yarn_count_value=$yarncount[$val]; else $yarn_count_value.=", ".$yarncount[$val];
				}
			}

			$machine_no_id_arr=array_unique(explode(',',chop($machine_no_id[$row[csf('prod_id')]][$row[csf('po_id')]]['machine_no_id'],",")));
			$dya_gage="";
			foreach($machine_no_id_arr as $machine_id)
			{
				$dya_gage=$dya_gauge_arr[$machine_id]["dia_width"]."<br>".$dya_gauge_arr[$machine_id]["gauge"];
			}

		$stitch=implode(", ", array_unique(explode(",",chop($yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['stitch_length'],','))));
		$yarn_lot=implode(", ", array_unique(explode(",",chop($yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['lot'],','))));
	}*/

	$all_barcode .= $row[csf("barcode_no")] . ",";

	$yarn_lot = implode(",", array_unique(explode(",", $row[csf('yarn_lot')])));
	$y_count = array_unique(explode(",", $row[csf('yarn_count')]));
	$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
	$yarn_count_value = "";
	foreach ($y_count as $val) {
		if ($val > 0) {
			if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
		}
	}
	$brand_value = "";
	foreach ($brand_id as $bid) {
		if ($bid > 0) {
			if ($brand_value == '') $brand_value = $brand_name_arr[$bid]; else $brand_value .= ", " . $brand_name_arr[$bid];
		}
	}
				if ($row[csf('receive_basis')] == 0 || $row[csf('receive_basis')] == 1 || $row[csf('receive_basis')] == 11) //from Entry page
				{
					$machine_dia_width = $row[csf('machine_dia')];
					$machine_gauge = $row[csf('machine_gg')];
				} else if ($row[csf('receive_basis')] == 2) //Knitting Plan
				{
					$program_data = sql_select("select width_dia_type, machine_dia, machine_gg, machine_id from ppl_planning_info_entry_dtls where id='" . $row[csf('booking_id')] . "'");

					$machine_dia_width = $program_data[0][csf('machine_dia')];
					$machine_gauge = $program_data[0][csf('machine_gg')];
				}

				$stitch = implode(",", array_unique(explode(",", $row[csf('stitch_length')])));
				//
				$dya_gage = $machine_dia_width . "<br>" . $machine_gauge;
				//$yarn_lot_arr[$rows[csf('prod_id')]][$rows['po_breakdown_id']]['stitch_length']
				//$st_len=implode(", ", array_unique(explode(",",$stitch)));
				//$machine_dia_up=$machine_array_lib_dia[$machine_dia[$row[csf('program_no')]]['m_dia']];
				//$machine_gauge_up=$machine_array_lib_gauge[$machine_dia[$row[csf('program_no')]]['m_gauge']];
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
						$color_type_id=$color_type_array_precost[$booking_no][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]]['color_type_id'];
					}
				}


				//$color_type[$color_type_id];
				?>
				<tr style="font-size:21px" bgcolor="<? echo $bgcolor; ?>">
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="60" align="center"
					style="word-break:break-all;"><? echo $row[csf('program_no')]; ?></td>
					<?
					if ($row[csf('knitting_source')] == 1) {
						$machin_knit_com = $machine_library[$row[csf('machine_no_id')]];
					} else {
						$machin_knit_com = $supplier_library[$row[csf('knitting_company')]];
					}

					?>
					<td width="80" style="word-break:break-all;"
					align="center"><? echo $machin_knit_com; ?></td>
					<td width="80"
					style="word-break:break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
					<td width="80"
					style="word-break:break-all;"><? echo $color_type[$color_type_id]; ?></td>
					<td width="150" style="word-break:break-all;"><? echo $desc[0] . "," . $desc[1]; ?></td>
					<td width="50" align="center" style="word-break:break-all;"><? echo $desc[2]; ?></td>
					<td width="50" align="center" style="word-break:break-all;"><? echo $desc[3]; ?></td>
					<td width="70" align="center" style="word-break:break-all;"><? echo $dya_gage; ?></td>
					<td width="70"
					style="word-break:break-all;"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
					<td width="60" align="center" style="word-break:break-all;"><? echo $stitch; ?></td>
					<td width="70" align="right"
					style="word-break:break-all;"><? echo number_format($row[csf('batch_qnty')], 2); ?></td>
					<td align="center" width="50"
					style="word-break:break-all;"><? echo $row[csf('num_of_rows')]; ?></td>
					<td width="80"
					style="word-break:break-all;"><? echo implode(',', array_unique(explode(",", $yarn_lot))); ?></td>
					<td width="80" style="word-break:break-all;">
						<?
						/*$all_suplier="";
				$yarn_lot_arr=explode(",",$yarn_lot);
				foreach($yarn_lot_arr as $lot)
				{
					if($supplier_array_lib[$supplier_from_prod[$lot]]!="")
					{
						$all_suplier.=$supplier_array_lib[$supplier_from_prod[$lot]].",";
					}
				}
				$all_suplier=implode(',',array_unique(explode(",",chop($all_suplier,","))));
				echo $all_suplier;*/
				echo $brand_value;
				?></td>
				<td width="80" style="word-break:break-all;"
				align="center"><? echo $yarn_count_value; ?></td>
				<td>&nbsp;</td>
			</tr>
			<?php
			$total_roll_number += $row[csf('num_of_rows')];
			$total_batch_qty += $row[csf('batch_qnty')];
			$i++;
		}
		$all_barcode = implode(", ", array_unique(explode(",", chop($all_barcode, ","))));
		?>
		<tr style="font-size:21px">
			<td style="border:none; word-break:break-all;" colspan="10" rowspan="3" valign="top"><p><span
				style="font-weight:bold;">Roll Id : </span> <? echo $all_barcode; ?></p></td>
				<td style="border:none;" align="right"><b>Sum:</b></td>
				<td align="right"><b><? echo number_format($total_batch_qty, 2); ?> </b></td>
				<td align="center"><b><? echo $total_roll_number; ?> </b></td>
				<td colspan="5" style="border:none;">&nbsp;</td>
			</tr>
			<tr style="font-size:21px">
				<td style="border:none;" align="right"><b>Trims Weight:</b></td>
				<td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')], 2); ?> </b>
				</td>
				<td colspan="6" style="border:none;">&nbsp;</td>
			</tr>
			<tr style="font-size:21px">
				<td style="border:none;" align="right"><b>Total:</b></td>
				<td align="right">
					<b><? echo number_format($total_batch_qty + $dataArray[0][csf('total_trims_weight')], 2); ?> </b>
				</td>
				<td colspan="6" style="border:none;">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="17" align="right">&nbsp; </td>
			</tr>
			<tr style="font-size:21px">
				<td colspan="17">
					<?
					$save_str = $dataArray[0][csf('SAVE_STRING')];
					$save_ref = explode("!!", $save_str);
					if (!empty($dataArray[0][csf('SAVE_STRING')]) > 0) {
						?>
						<table align="left" rules="all" class="rpt_table" width="100%" border="1">
							<thead>
								<tr>
									<th align="left" style="font-size:20px;" colspan="4"><strong>Trims Details</strong></th>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="250">Item Description</th>
									<th width="150">Weight In Kg</th>
									<th>Remarks</th>
								</tr>
							</thead>
							<tbody>
								<?
								$i = 1;
								foreach ($save_ref as $data_ref) {
									$data_ref = explode("_", $data_ref);
									?>
									<tr>
										<td align="center"><? echo $i; ?></td>
										<td><? echo $data_ref[0]; ?> </td>
										<td align="right"><? echo number_format($data_ref[1], 2); ?></td>
										<td align="center"><? echo $data_ref[2]; ?></td>
									</tr>
									<?
									$i++;
								}
								?>
							</tbody>
						</table>
						<?
					}
					?>

				</td>
			</tr>
			<tr>
				<td colspan="17" align="right">
					<?
					$process = $dataArray[0][csf('process_id')];
					$process_id = explode(',', $process);
					//print_r($process_id);
					$process_value = '';
					$i = 1;
					foreach ($process_id as $val) {
						if ($process_value == '') $process_value = $i . '. ' . $conversion_cost_head_array[$val]; else $process_value .= ", " . $i . '. ' . $conversion_cost_head_array[$val];
						$i++;
					}
					?>
					<table style="font-size:21px" align="left" rules="all" class="rpt_table" width="1060">
						<tr>
							<th align="left" style="font-size:20px;"><strong>Process Required</strong></th>
						</tr>
						<tr>
							<td style="font-size:20px;" title="<? echo $process_value; ?>">
								<p><? echo $process_value; ?></p>
							</td>
						</tr>
						<tr>
							<td align="left" style="font-size:19px;">
								Heat Setting:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;
								&nbsp;&nbsp;&nbsp;&nbsp;
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								&nbsp; Loading Date & Time: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								&nbsp; UnLoading Date & Time:&nbsp;
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<div style="float:left; margin-left:10px;"><strong> Quality Instruction(Hand Written)</strong></div>
		<table width="1060" cellspacing="0" align="center" style="font-size:21px">
			<tr>
				<td valign="top" align="left" width="520">
					<table cellspacing="0" width="515" align="left" border="1" rules="all" class="rpt_table">
						<tr>
							<th>SL</th>
							<th>Roll No</th>
							<th>Roll Mark</th>
							<th>Actual Dia</th>
							<th>Roll Wgt.</th>
							<th>Remarks</th>
						</tr>
						<tr height="30">
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
						</tr>
						<tr height="30">
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
						</tr>
						<tr height="30">
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
						</tr>
						<tr height="30">
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
						</tr>
						<tr height="30">
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
						</tr>
						<tr height="30">
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
						</tr>
					</table>
				</td>
				<td width="10" align="justify" valign="top"></td>
				<td width="520" valign="top" align="right" style="font-size:21px">
					<table width="515" cellspacing="0" border="1" rules="all" class="rpt_table">
						<tr>
							<th>SL</th>
							<th>Roll No</th>
							<th>Actual Dia</th>
							<th>Roll Wgt.</th>
							<th>Remarks</th>
						</tr>
						<tr height="30">
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
						</tr>
						<tr height="30">
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
						</tr>
						<tr height="30">
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
						</tr>
						<tr height="30">
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
						</tr>
					</table>
				</td>
			</tr>
			<tr style="font-size:21px">
				<td width="520" valign="top">
					<table width="515" cellspacing="0" border="1" rules="all" class="rpt_table">
						<tr>
							<th align="left"><strong>Shade Result(<i>Hand Written</i>)</strong></th>
						</tr>
						<tr>
							<td colspan="1" style="width:475px; height:80px">&nbsp;</td>
						</tr>
					</table>
				</td>
				<td width="10" align="justify" valign="top">&nbsp;</td>
				<td width="520" valign="top" align="right">
					<table cellspacing="0" border="1" rules="all" class="rpt_table" width="515">
						<tr>
							<th align="left" colspan="3"><strong>Shrinkage(<i>Hand Written</i>)</strong></th>
						</tr>
						<tr>
							<th><b>Length % </b></th>
							<th><b>Width % </b></th>
							<th><b> Twist % </b></th>
						</tr>
						<tr height="30">
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
						</tr>
						<tr height="30">
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
						</tr>
						<tr height="30">
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
						</tr>
						<tr height="30">
							<td>&nbsp; </td>
							<td>&nbsp; </td>
							<td>&nbsp; </td>
						</tr>
					</table>
				</td>
			</tr>
			<tr style="font-size:21px">
				<td width="1060" colspan="3">
					<table cellspacing="0" border="1" rules="all" class="rpt_table" width="1060">
						<tr>
							<th colspan="2" align="center"><strong>Dyeing Finishing Information(<i>Hand
							Written</i>)</strong></th>
						</tr>
						<tr>
							<th> Dyeing</th>
							<th> Finishing</th>
						</tr>
						<tr>
							<td style="width:530px; height:120px">&nbsp;</td>
							<td style="width:530px; height:120px">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br>
		<?
		echo signature_table(52, $company, "1060px");
		?>
	</div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 30,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $dataArray[0][csf('batch_no')]; ?>');
    </script>
    <?
	//dataArray[0][csf('batch_no')]
    exit();
}


if ($action == "batch_card_print_4") {
	//echo load_html_head_contents("Batch Info","../../", 1, 1, '','','');
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$batch_update_id = $data[1];
	$working_company = $data[7];
	$batch_mst_update_id = str_pad($batch_update_id, 10, '0', STR_PAD_LEFT);
	//echo $batch_mst_update_id;die;
	$batch_sl_no = $data[2];
	$booking_no_id = $data[6];

	$roll_maintained = $data[8];
	if($roll_maintained==0)
	{
		echo "<p width='250'><b>Sorry, This Print Report is in roll level</b></p>";
		die;
	}
	//echo $data[3].$data[4];die;

	$company_sql = sql_select("select id, company_name,company_short_name from lib_company");
	foreach ($company_sql as $val)
	{
		$company_library[$val[csf("id")]] = $val[csf("company_name")];
		$company_library_short[$val[csf("id")]] = $val[csf("company_short_name")];
	}

	//$supplier_library = return_library_array("select id, short_name from lib_supplier", "id", "short_name");

	$machine_lib_sql = sql_select("select id, machine_no, dia_width, gauge,prod_capacity from lib_machine_name");

	foreach ($machine_lib_sql as $row) {
		$dya_gauge_arr[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$dya_gauge_arr[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$dya_gauge_arr[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
		$dya_gauge_arr[$row[csf("id")]]["prod_capacity"] = $row[csf("prod_capacity")];
	}

	$cons_comp_sql = sql_select("select a.id, a.construction,c.composition_name, b.percent
		from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b, lib_composition_array c
		where a.id = b.mst_id and b.copmposition_id = c.id  and a.status_active=1 and b.status_active=1 and c.status_active=1");
	foreach ($cons_comp_sql as  $val)
	{
		$cons_comp_arr[$val[csf("id")]]["const"] = $val[csf("construction")];
		$cons_comp_arr[$val[csf("id")]]["compo"] .= $val[csf("composition_name")] ." ". $val[csf("percent")] . "% ";

	}

	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$floor_arr = return_library_array("select id,floor_name from lib_prod_floor  where status_active =1 and is_deleted=0", 'id', 'floor_name');

	$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
	$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');

	$job_array = array();
	$job_sql = "select distinct(a.buyer_name) as buyer_name,a.style_ref_no, a.job_no_prefix_num, a.job_no, b.pub_shipment_date, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
		$job_array[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
	}

	if ($db_type == 0) {
		$sql = "select a.id, a.company_id,a.working_company_id,a.batch_no,a.is_sales, a.booking_no_id, a.booking_no,a.booking_without_order,a.sales_order_no,a.color_id,a.floor_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.batch_date,a.cuff_qty, a.SAVE_STRING, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, group_concat(b.barcode_no) as barcode_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id,a.company_id,a.working_company_id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id,a.floor_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id, a.sales_order_no,a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty,a.batch_date, a.cuff_qty, a.SAVE_STRING";
	} else {
		$sql = "select a.id, a.company_id,a.working_company_id,a.batch_no,a.is_sales, a.booking_no_id,a.booking_no,a.booking_without_order,a.sales_order_no, a.color_id,a.floor_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty,a.batch_date, a.cuff_qty, a.SAVE_STRING, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id,LISTAGG(CAST(b.barcode_no AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.barcode_no) AS barcode_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id,a.company_id,a.working_company_id, a.batch_no,a.is_sales, a.color_id,a.floor_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for,a.sales_order_no, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty,a.batch_date,a.cuff_qty, a.SAVE_STRING";
	}
	 //echo $sql;
	$dataArray = sql_select($sql);

	$po_number = "";
	$job_number = "";
	$job_style = "";
	$buyer_id = "";
	$ship_date = "";
	$internal_ref = "";
	$file_nos = "";
	$po_id = array_unique(explode(",", $dataArray[0][csf('po_id')]));
	$barcode_no = implode(",",array_unique(explode(",", $dataArray[0][csf('barcode_no')])));
	$booking_no = $dataArray[0][csf('booking_no')];
	$is_sales = $dataArray[0][csf('is_sales')];
	$batch_against_id = $dataArray[0][csf('batch_against')];
	$batch_booking_id = $dataArray[0][csf('booking_no_id')];
	$batch_product_id = $dataArray[0][csf('prod_id')];
	$sales_order_no = $dataArray[0][csf('sales_order_no')];
	$batch_booking_without = $dataArray[0][csf('booking_without_order')];
	foreach ($po_id as $val) {
		if ($po_number == "") $po_number = $job_array[$val]['po']; else $po_number .= ',' . $job_array[$val]['po'];
		if ($job_number == "") $job_number = $job_array[$val]['job']; else $job_number .= ',' . $job_array[$val]['job'];
		if ($job_style == "") $job_style = $job_array[$val]['style']; else $job_style .= ',' . $job_array[$val]['style'];
		if($is_sales!=1)
		{
			if ($buyer_id == "") $buyer_id = $job_array[$val]['buyer']; else $buyer_id .= ',' . $job_array[$val]['buyer'];
		}
		if ($ship_date == "") $ship_date = change_date_format($job_array[$val]['ship_date']); else $ship_date .= ',' . change_date_format($job_array[$val]['ship_date']);

		if ($internal_ref == "") $internal_ref = $job_array[$val]['ref']; else $internal_ref .= ',' . $job_array[$val]['ref'];
		if ($job_array[$val]['file_no'] > 0) {
			if ($file_nos == "") $file_nos = $job_array[$val]['file_no']; else $file_nos .= ',' . $job_array[$val]['file_no'];
		}
	}

	$job_no = implode(",", array_unique(explode(",", $job_number)));
	$jobstyle = implode(",", array_unique(explode(",", $job_style)));
	$po_numbers = implode(",", array_unique(explode(",", $po_number)));
	$buyer = implode(",", array_unique(explode(",", $buyer_id)));
	$internal_ref = implode(",", array_unique(array_filter(explode(",", $internal_ref))));
	$file_nos = implode(",", array_unique(explode(",", $file_nos)));
	//$booking_without_order=return_field_value( "booking_no as booking_no", "wo_non_ord_samp_booking_mst","booking_no=$booking_no","booking_no");

	if ($dataArray[0][csf('booking_without_order')] == 1) {
		$booking_without_order = sql_select("select a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id,b.process_loss from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$company and a.booking_no='$booking_no' and a.booking_type=4");
		foreach ($booking_without_order as $row) {
			$color_type_array[$row[csf('booking_no')]][$row[csf('body_part')]]['color_type_id'] = $row[csf('color_type_id')];
			$process_loss_array[$row[csf('booking_no')]][$row[csf('body_part')]]['color_type_id'] = $row[csf('process_loss')];
			//$booking_id = $row[csf('booking_no_prefix_num')];
			$buyer = $row[csf('buyer_id')];
		}


	} else {


		$booking_with_order = sql_select("select booking_no_prefix_num, buyer_id from wo_booking_mst where company_id=$company and booking_no='$booking_no'");
		//$booking_id = $booking_with_order[0][csf('booking_no_prefix_num')];
		$buyer = $booking_with_order[0][csf('buyer_id')];

		$color_sql = "select  b.booking_no,b.dia_width,b.fabric_color_id,c.color_type_id,c.body_part_id ,c.lib_yarn_count_deter_id, c.gsm_weight,c.avg_process_loss from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company and a.booking_no='$booking_no' and a.booking_type=1 and b.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.booking_no,b.dia_width,b.fabric_color_id,c.color_type_id,c.body_part_id, c.lib_yarn_count_deter_id, c.gsm_weight,c.avg_process_loss";
		$color_sql_result = sql_select($color_sql);
		foreach ($color_sql_result as $row) {
			//$color_type_array[$row[csf('booking_no')]][$row[csf('body_part_id')]]['color_type_id'] = $row[csf('color_type_id')];
			$color_type_array_precost[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['color_type_id'] = $row[csf('color_type_id')];
			//$process_loss_array[$row[csf('booking_no')]][$row[csf('body_part_id')]]['color_type_id'] = $row[csf('avg_process_loss')];
			$process_loss_array[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]] = $row[csf('avg_process_loss')];
		}
	}
	$sales_sql = "select a.buyer_id,a.po_buyer,a.within_group,b.job_no_mst as booking_no,b.color_type_id,b.body_part_id,b.process_loss from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company and a.job_no='$sales_order_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.job_no_mst ,b.color_type_id,b.body_part_id,b.process_loss,a.buyer_id,a.po_buyer,a.within_group";
	$sales_result = sql_select($sales_sql);
	foreach ($sales_result as $row) {
		$sales_color_type_array[$row[csf('booking_no')]][$row[csf('body_part_id')]]['color_type_id'] = $row[csf('color_type_id')];
		$sales_process_loss_array[$row[csf('booking_no')]][$row[csf('body_part_id')]]['color_type_id'] = $row[csf('process_loss')];
		if($row[csf('within_group')]==1)
		{
			$buyer = $row[csf('po_buyer')];
		}
		else {
			$buyer = $row[csf('buyer_id')];
		}
	}


	?>
	<div style="width:1060px;">
		<table width="1060" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="6" align="center" style="font-size:22px">
					<strong><? echo $company_library[$working_company]; ?></strong></td>
					<td colspan="2" align="left">Print Time:<? echo $date = date("F j, Y, g:i a"); ?></td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:18px"><strong><u>Batch Card</u></strong></td>
					<td colspan="2" id="barcode_img_id" align="right" style="font-size:24px"></td>
				</tr>
				<tr>
					<td colspan="8">&nbsp; </td>
				</tr>
				<tr>
					<td colspan="6" align="left" style="font-size:18px"><strong><u>Reference Details</u></strong></td>
					<td style="font-size:24px; border: solid 2px;" align="center" colspan="2">&nbsp;<? echo $dataArray[0][csf('organic')]; ?></td>
				</tr>
				<tr>
					<td width="113"><strong>Buyer</strong></td>
					<td width="146">:
						<?
						$buyer_ids = explode(",", $buyer);
						$buyerName = "";
						foreach ($buyer_ids as $val) {

							if ($buyerName == "")
							{
								$buyerName = $buyer_arr[$val];
							}
							else {

								$buyerName .= $buyer_arr[$val] . ",";
							}
						}

						$buyerName = chop($buyerName, ',');
						//echo "**".$buyer_id_booking."==".$dataArray[0][csf('batch_against')];die;
						if ($dataArray[0][csf('batch_against')] == 3)
						{
							echo $buyer_arr[$buyer];
						} else {
							echo $buyerName;
						}
						?>
					</td>
					<td width="105"><strong>Dyeing Comp</strong></td>
					<td width="168">:&nbsp;<? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
					<td width="83"><strong>B. Against</strong></td>
					<td width="153">:&nbsp;<? echo $batch_against[$dataArray[0][csf('batch_against')]]; ?></td>
					<td width="121"><strong>Batch Weight</strong></td>
					<td width="155">:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?></td>
				</tr>
				<tr>
					<td><strong>Booking No</strong></td>
					<td>:&nbsp;<? echo $booking_no; ?></td>
					<td><strong>Floor</strong></td>
					<td>:&nbsp;<? echo $floor_arr[$dataArray[0][csf('floor_id')]]; ?></td>
					<td><strong>Batch For</strong></td>
					<td>:&nbsp;<? echo $batch_for[$dataArray[0][csf('batch_for')]]; ?></td>
					<td><strong>Ship Date</strong></td>
					<td>:
						<? if (trim($ship_date) != "0000-00-00" && trim($ship_date) != "") echo implode(",", array_unique(explode(",", $ship_date))); else echo "&nbsp;"; ?></td>
					</tr>
					<tr>
						<td><strong>Color</strong></td>
						<td>  :&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
						<td><strong>Color Range</strong></td>
						<td>:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range_id')]]; ?></td>

						<td><strong>Style Ref.</strong></td>
						<td>:&nbsp;<? echo $jobstyle; ?></td>

						<td><strong>Collar Qty (Pcs)</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('collar_qty')]; ?></td>
					</tr>
					<tr>
						<td><strong>Batch No</strong></td>
						<td>:&nbsp;<? echo $company_library_short[$dataArray[0][csf('working_company_id')]].'-'.$dataArray[0][csf('batch_no')]; ?></td>
						<td><strong>Batch Ext.</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('extention_no')]; ?></td>
						<td><strong>Batch Date</strong></td>
						<td>:&nbsp;<? echo change_date_format($dataArray[0][csf('batch_date')]); ?></td>
						<td><strong>Cuff Qty (Pcs)</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('cuff_qty')]; ?></td>
					</tr>
					<tr>
						<td><strong>Dyeing Machine</strong></td>
						<td>:
							<?
							if ($db_type == 2) {
								$dyeing_machine = return_field_value("(machine_no || '-' || brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
							} else if ($db_type == 0) {
								$dyeing_machine = return_field_value("concat(machine_no,'-',brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
							}
							echo $dyeing_machine;
							?></td>
							<td><strong>M/C Capacity</strong></td>
							<td>:&nbsp;<? echo $dya_gauge_arr[$dataArray[0][csf('dyeing_machine')]]["prod_capacity"]; ?></td>
							<td><strong>Remarks</strong></td>
							<td colspan="3">:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
						</tr>
						<tr>
							<td><strong>Order No</strong></td>
							<td colspan="7">:&nbsp;<? echo $po_numbers; ?></td>
						</tr>

					</table>
					<div style="float:left; font-size:17px;"><strong><u>Fabrication Details</u></strong></div>
					<table align="left" cellspacing="0" width="1060" border="1" rules="all" class="rpt_table"	style="border-top:none">
						<thead bgcolor="#dddddd" align="center">
							<tr>
								<th width="30">Cut Mark</th>
								<th width="60">Prog. No</th>
								<th width="80">Machine / Knitting Floor</th>
								<th width="80">Body part</th>
								<th width="80">Color Type</th>
								<th width="150">Const. & Comp.</th>
								<th width="50">Fin. GSM</th>
								<th width="50">Fin. Dia</th>
								<th width="70">M/Dia X Gauge</th>
								<th width="70">D/W Type</th>
								<th width="60">S. Length</th>
								<th width="70">Grey Qty.</th>
								<th width="50">Total Roll</th>
								<th width="80"><strong>Brand</strong></th>
								<th width="80">Yarn Lot</th>
								<th width="80">Yarn Count</th>
								<th>P. Loss</th>
							</tr>
						</thead>
						<?
						$i = 1;



						if ($db_type == 0) {
							$recv_basis_grop = " group_concat(a.receive_basis) AS receive_basis";
						} else {
							$recv_basis_grop = " LISTAGG(a.receive_basis, ',') WITHIN GROUP (ORDER BY a.receive_basis) AS receive_basis";
						}

						if ($dataArray[0][csf('booking_without_order')] == 1 && $batch_against_id == 3) {

							$receive_basis = return_field_value("$recv_basis_grop", "inv_receive_master a,pro_roll_details b", "a.id=b.mst_id  and (a.booking_id='" . $booking_no_id . "' OR b.barcode_no in($barcode_no)) and a.entry_form in(2,22) and b.entry_form in(2,22) and a.receive_basis in (0,1,11)", "receive_basis");
						} else {

							$receive_basis = return_field_value("$recv_basis_grop", "inv_receive_master a,pro_roll_details b", "a.id=b.mst_id and (a.booking_id='" . $booking_no_id . "' OR b.barcode_no in($barcode_no)) and a.entry_form in(2,22) and b.entry_form in(2,22) and a.receive_basis in (0,1,11)", "receive_basis");
						}

						$receivebasis = array_unique(explode(",", $receive_basis));
						foreach ($receivebasis as $rcvid) {
							if ($rcvid == 0 || $rcvid == 1 || $rcvid == 11) {
								$machine_info = "d.machine_dia,d.machine_gg,";
							} else {
								$machine_info = "";
							}
						}

						if ($db_type == 0) {
							$sql_dtls = "select a.dyeing_machine,a.booking_no_id,e.receive_basis,e.booking_id, a.booking_without_order,a.is_sales, a.color_id, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no,d.febric_description_id,d.gsm,d.width, b.item_description, b.program_no, b.prod_id, b.body_part_id,d.febric_description_id,d.gsm,d.width, b.width_dia_type, count(b.width_dia_type) as num_of_rows,$machine_info d.machine_no_id, group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count) as yarn_count, d.stitch_length as stitch_length,d.floor_id, group_concat(d.brand_id) as brand_id, e.knitting_source, e.knitting_company, group_concat(c.barcode_no) as barcode_no
							from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
							where a.id=b.mst_id and b.roll_id = c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22) and c.entry_form in(2,22)
							group by a.dyeing_machine,a.booking_no_id,e.booking_id,$machine_info a.booking_without_order,a.is_sales,a.color_id, d.febric_description_id,d.gsm,d.width, e.receive_basis, b.prod_id, b.program_no, b.body_part_id, d.febric_description_id,d.gsm,d.width, b.item_description,b.width_dia_type, d.machine_no_id, d.stitch_length,d.floor_id, e.knitting_source, e.knitting_company order by b.program_no";

						} else {
							$sql_dtls = "select a.dyeing_machine,a.booking_no_id,e.receive_basis,e.booking_id,a.booking_without_order,a.is_sales, a.color_id, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no,d.febric_description_id,d.gsm,d.width, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows, d.machine_no_id,$machine_info LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count, d.stitch_length as stitch_length,d.floor_id, LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id, e.knitting_source, e.knitting_company, LISTAGG(CAST(c.barcode_no AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY c.barcode_no) as barcode_no
							from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
							where a.id=b.mst_id and b.roll_id = c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22) and c.entry_form in(2,22)
							group by a.dyeing_machine,a.booking_no_id,e.receive_basis,$machine_info e.booking_id,a.booking_without_order,a.is_sales, a.color_id, d.febric_description_id, d.gsm,d.width, b.prod_id, b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, d.stitch_length,d.floor_id, e.knitting_source, e.knitting_company order by b.program_no";
						}
	//echo $sql_dtls;die;dyeing_machine floor_id
 	//echo $sql_dtls;
						$sql_result = sql_select($sql_dtls);
						$all_barcode = "";
						foreach ($sql_result as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							$desc = explode(",", $row[csf('item_description')]);

							$all_barcode[$row[csf('machine_no_id')]][$row[csf("barcode_no")]]= $row[csf("barcode_no")];

							$yarn_lot = implode(",", array_unique(explode(",", $row[csf('yarn_lot')])));
							$y_count = array_unique(explode(",", $row[csf('yarn_count')]));
							$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
							$yarn_count_value = "";
							foreach ($y_count as $val) {
								if ($val > 0) {
									if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
								}
							}
							$brand_value = array();
							foreach ($brand_id as $bid) {
								if ($bid > 0) {
									$brand_value[$brand_name_arr[$bid]]=$brand_name_arr[$bid];
								}
							}
				if ($row[csf('receive_basis')] == 0 || $row[csf('receive_basis')] == 1 || $row[csf('receive_basis')] == 4 || $row[csf('receive_basis')] == 11) //from Entry page
				{
					$machine_dia_width = $row[csf('machine_dia')];
					$machine_gauge = $row[csf('machine_gg')];
				} else if ($row[csf('receive_basis')] == 2) //Knitting Plan
				{
					$program_data = sql_select("select width_dia_type, machine_dia, machine_gg, machine_id from ppl_planning_info_entry_dtls where id='" . $row[csf('booking_id')] . "'");

					$machine_dia_width = $program_data[0][csf('machine_dia')];
					$machine_gauge = $program_data[0][csf('machine_gg')];
				}

				$stitch = implode(",", array_unique(explode(",", $row[csf('stitch_length')])));
				//
				$dya_gage = $machine_dia_width . "<br>" . $machine_gauge;
				$is_sales=$row[csf('is_sales')];
				if($is_sales==1) //Sales
				{
					$color_type_id=$sales_color_type_array[$sales_order_no][$row[csf('body_part_id')]]['color_type_id'];

					$process_loss=$sales_process_loss_array[$sales_order_no][$row[csf('body_part_id')]]['color_type_id'];
				}
				else
				{
					if($row[csf('booking_without_order')] == 1)
					{
						$color_type_id=$color_type_array[$booking_no][$row[csf('body_part_id')]]['color_type_id'];
						$process_loss=$process_loss_array[$booking_no][$row[csf('body_part_id')]]['color_type_id'];
					}
					else
					{
						$color_type_id=$color_type_array_precost[$booking_no][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]]['color_type_id'];

						$process_loss= $process_loss_array[$booking_no][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('color_id')]];
					}

				}

				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="30"></td>
					<td width="60" align="center"
					style="word-break:break-all;"><? echo $row[csf('program_no')]; ?></td>
					<?

					$machin_knit_com = $dya_gauge_arr[$row[csf('machine_no_id')]]["machine_no"].'<br>'.$floor_arr[$row[csf('floor_id')]];

					?>
					<td width="80" style="word-break:break-all;" align="center"><? echo $machin_knit_com; ?></td>
					<td width="80" style="word-break:break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
					<td width="80" style="word-break:break-all;"><? echo $color_type[$color_type_id]; ?></td>
					<!-- <td width="150" style="word-break:break-all;"><? //echo $desc[0] . "," . $desc[1]; ?></td> -->
					<td width="150" style="word-break:break-all;"><? echo $cons_comp_arr[$row[csf("febric_description_id")]]["const"] . "," . $cons_comp_arr[$row[csf("febric_description_id")]]["compo"]; ?></td>
					<td width="50" align="center" style="word-break:break-all;"><? echo $row[csf('gsm')];//$desc[2]; ?></td>
					<td width="50" align="center" style="word-break:break-all;"><? echo $row[csf('width')];//$desc[3]; ?></td>
					<td width="70" align="center" style="word-break:break-all;"><? echo $dya_gage; ?></td>
					<td width="70"
					style="word-break:break-all;"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
					<td width="60" align="center" style="word-break:break-all;"><? echo $stitch; ?></td>
					<td width="70" align="right"
					style="word-break:break-all;"><? echo number_format($row[csf('batch_qnty')], 2); ?></td>
					<td align="center" width="50"
					style="word-break:break-all;"><? echo $row[csf('num_of_rows')]; ?></td>
					<td width="80" style="word-break:break-all;"><? echo implode(',',$brand_value); ?></td>
					<td width="80"
					style="word-break:break-all;"><? echo implode(',', array_unique(explode(",", $yarn_lot))); ?></td>
					<td width="80" style="word-break:break-all;" align="center"><? echo $yarn_count_value; ?></td>
					<td align="right"><? echo $process_loss; ?></td>
				</tr>
				<?php
				$total_roll_number += $row[csf('num_of_rows')];
				$total_batch_qty += $row[csf('batch_qnty')];
				$i++;
			}
		//$all_barcode = implode(",", array_unique(explode(",", chop($all_barcode, ","))));
			?>
			<tr>
				<td style="border:none; word-break:break-all;" colspan="10" rowspan="3" valign="top"><p>
					<?
					foreach($all_barcode as $machine_id=>$barcodeArr){
						echo '[ <b>'.$dya_gauge_arr[$machine_id]["machine_no"].':</b>'.implode(",", $barcodeArr).' ]';
					}

					?>
				</p></td>
				<td style="border:none;" align="right"><b>Sum:</b></td>
				<td align="right"><b><? echo number_format($total_batch_qty, 2); ?> </b></td>
				<td align="center"><b><? echo $total_roll_number; ?> </b></td>
				<td colspan="5" style="border:none;">&nbsp;</td>
			</tr>
			<tr>
				<td style="border:none;" align="right"><b>Trims Weight:</b></td>
				<td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')], 2); ?> </b>
				</td>
				<td colspan="6" style="border:none;">&nbsp;</td>
			</tr>
			<tr>
				<td style="border:none;" align="right"><b>Total:</b></td>
				<td align="right">
					<b><? echo number_format($total_batch_qty + $dataArray[0][csf('total_trims_weight')], 2); ?> </b>
				</td>
				<td colspan="6" style="border:none;">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="17" align="right">&nbsp; </td>
			</tr>
			<tr>
				<td colspan="17">
					<?
					$save_str = $dataArray[0][csf('SAVE_STRING')];
					$save_ref = explode(",", $save_str);
					if (!empty($dataArray[0][csf('SAVE_STRING')]) > 0) {
						?>
						<table align="left" rules="all" class="rpt_table" width="100%" border="1">
							<thead>
								<tr>
									<th align="left" style="font-size:20px;" colspan="4"><strong>Trims
									Details</strong></th>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="250">Item Description</th>
									<th width="150">Weight In Kg</th>
									<th>Remarks</th>
								</tr>
							</thead>
							<tbody>
								<?
								$i = 1;
								foreach ($save_ref as $data_ref) {
									$data_ref = explode("_", $data_ref);
									?>
									<tr>
										<td align="center"><? echo $i; ?></td>
										<td><? echo $data_ref[0]; ?> </td>
										<td align="right"><? echo number_format($data_ref[1], 2); ?></td>
										<td align="center"><? echo $data_ref[2]; ?></td>
									</tr>
									<?
									$i++;
								}
								?>
							</tbody>
						</table>
						<?
					}
					?>

				</td>
			</tr>
			<tr>
				<td colspan="17" align="right">
					<?
					$process = $dataArray[0][csf('process_id')];
					$process_id = explode(',', $process);
					//print_r($process_id);
					$process_value = '';
					$i = 1;
					foreach ($process_id as $val) {
						if ($process_value == '') $process_value = $i . '. ' . $conversion_cost_head_array[$val]; else $process_value .= ", " . $i . '. ' . $conversion_cost_head_array[$val];
						$i++;
					}
					?>

				</td>
			</tr>
		</table>
		<br>
		<table align="left" rules="all" class="rpt_table" width="1060">
			<tr>
				<th align="left" style="font-size:20px;"><strong>Process Required</strong></th>
			</tr>
			<tr>
				<td style="font-size:20px;" title="<? echo $process_value; ?>">
					<p><? echo $process_value; ?></p>
				</td>
			</tr>
		</table>
		<br>
		<table border="1" rules="all" width="1060">
			<tr>
				<td align="left" style="font-size:19px;">Loading Date & Time:</td>
				<td align="left" style="font-size:19px;">Un-loading Date & Time:</td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" border="1" rules="all" width="1060">
			<tr>
				<th colspan="2">Dyeing Parameter</th>
				<th colspan="2" align="center"><strong>Dyeing Finishing Information(<i>Hand	Written</i>)</strong>
				</th>
			</tr>
			<tr>
				<td width="12%">Rope Length</td>
				<td></td>
				<td width="33%">Dyeing</td>
				<td width="33%">Finishing</td>
			</tr>
			<tr>
				<td>Cycle Time</td>
				<td></th>
					<td rowspan="5">&nbsp;</td>
					<td rowspan="5">&nbsp;</td>
				</tr>
				<tr>
					<td>Reel Speed</td>
					<td></td>
				</tr>
				<tr>
					<td>Pump Speed</td>
					<td></td>
				</tr>
				<tr>
					<td>Gram/Meter</td>
					<td></td>
				</tr>
				<tr>
					<td>Operator</td>
					<td></td>
				</tr>
			</table>
			<br>
			<?
			echo get_spacial_instruction($batch_sl_no,"1060px",64);
			echo signature_table(52, $company, "1060px");
			?>
			<style>
				@media print {
					.break {page-break-after: always!important;}
				}
			</style>

			<p class="break"></p>

			<table id="batch_form" border="1" cellpadding="2" cellspacing="0" rules="all" width="100%">
				<tr>
					<td style="border-bottom:1px solid #fff;" align="center" colspan="12">
						<b style="font-size:20px;"><? echo $company_library[$working_company];?></b>
					</td>
				</tr>
				<tr>
					<td  colspan=12 align="center"><b>Dyeing Finishing Section</b></td>
				</tr>
				<tr>
					<td colspan=5 align="center"><b>Slitting / Squeezing</b></td>
					<td rowspan=49 width="5"></td>
					<td colspan=6 align="center"><b>After AOP Stenter /Santex Dryer (Curing) (3) if Needed</b></td>
				</tr>
				<tr >
					<td width=129 >M/C Speed(m/m)</td>
					<td width=70 >&nbsp;</td>
					<td width=50 colspan=2>Post P.Pressure</td>
					<td width=70>&nbsp;</td>
					<td width=170 colspan=2>Temperature (&#176;C)</td>
					<td width=70 >&nbsp;</td>
					<td width=50 colspan=2>Set Width</td>
					<td width=70>&nbsp;</td>
				</tr>
				<tr>
					<td>Over Feed%</td>
					<td>&nbsp;</td>
					<td  colspan=2>Before Width</td>
					<td></td>
					<td  colspan=2>M/C Speed (m/m)</td>
					<td>&nbsp;</td>
					<td  colspan=2>After Width</td>
					<td></td>
				</tr>
				<tr>
					<td>Pre. P. Pressure</td>
					<td></td>
					<td colspan=2>After Width</td>
					<td></td>
					<td colspan=2>Over Feed %</td>
					<td></td>
					<td colspan=2>After GSM</td>
					<td></td>
				</tr>
				<tr>
					<td height="30"><b>Signature &amp; Date</b></td>
					<td colspan=4></td>
					<td colspan=2><b>Signature &amp; Date</b></td>
					<td colspan=4>&nbsp;</td>
				</tr>
				<tr>
					<td colspan=5 align="center"><b>Stenter Santex Dryer (1)</b></td>
					<td colspan=6 align="center"><b>After Wash Stenter Santex/ Dryer (Softener Use) if (4)</b></td>
				</tr>
				<tr>
					<td>Temperature (&#176;C)</td>
					<td>&nbsp;</td>
					<td colspan=2>2nd P.Pressure</td>
					<td>&nbsp;</td>
					<td colspan=2>Temperature (&#176;C)</td>
					<td>&nbsp;</td>
					<td colspan=2>2nd P.Pressure</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>M/C Speed(m/m)</td>
					<td>&nbsp;</td>
					<td  colspan=2>Set Width</td>
					<td>&nbsp;</td>
					<td colspan=2>M/C Speed (m/m)</td>
					<td>&nbsp;</td>
					<td  colspan=2>Set Width</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>Over Feed %</td>
					<td>&nbsp;</td>
					<td colspan=2>After Width</td>
					<td></td>
					<td  colspan=2>
						Over Feed %
					</td>
					<td>
					</td>
					<td  colspan=2>
						After Width
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<td>
						1st P. Pressure
					</td>
					<td>
					</td>
					<td  colspan=2>
						After GSM
					</td>
					<td>&nbsp;

					</td>
					<td  colspan=2>
						1st P. Pressure
					</td>
					<td>&nbsp;

					</td>
					<td  colspan=2>
						After GSM
					</td>
					<td>&nbsp;

					</td>
				</tr>
				<tr>
					<td>
						Shrinkage %
					</td>
					<td>
						L%=
					</td>
					<td  colspan=2>
						W%=
					</td>
					<td><span style="font-size:12px;">Spi%=</span></td>
					<td  colspan=2>
						Shrinkage %
					</td>
					<td>
						L%=
					</td>
					<td  colspan=2>
						W%=
					</td>
					<td>
						<span style="font-size:12px;">Spi%=</span>
					</td>
				</tr>
				<tr>
					<td>
						Softener Use(G/L)
					</td>
					<td>&nbsp;

					</td>
					<td  colspan=2>&nbsp;

					</td>
					<td>&nbsp;

					</td>
					<td  colspan=2>
						Softener Use(G/L)
					</td>
					<td  colspan=4>&nbsp;

					</td>
				</tr>
				<tr>
					<td height="30"><b>Signature &amp; Date</b></td>
					<td  colspan=4>&nbsp;

					</td>
					<td  colspan=2><b>Signature &amp; Date</b></td>
					<td  colspan=4>&nbsp;

					</td>
				</tr>
				<tr>
					<td  colspan=5 align="center"><b>Compacting Finish (1)</b></td>
					<td  colspan=6 align="center"><b>Suedding/Peach M/C </b></td>
				</tr>
				<tr>
					<td  colspan=5 align="center"> Normal Compacting /Sanforizing/ Compacting </td>
					<td>  M/C Speed (m/m) </td>
					<td>&nbsp;

					</td>
					<td>&nbsp;

					</td>
					<td  colspan=2>
						Drum Tension
					</td>
					<td>&nbsp;

					</td>
				</tr>
				<tr>
					<td>
						Temperature (&#176;C)
					</td>
					<td>&nbsp;

					</td>
					<td  colspan=2>
						Before Width
					</td>
					<td>&nbsp;

					</td>
					<td>
						Taker in Tension
					</td>
					<td>&nbsp;

					</td>
					<td>&nbsp;

					</td>
					<td  colspan=2>
						Pile RPM
					</td>
					<td>&nbsp;

					</td>
				</tr>
				<tr>
					<td>
						M/C Speed(m/m)
					</td>
					<td>&nbsp;

					</td>
					<td  colspan=2>
						Set Width
					</td>
					<td>&nbsp;

					</td>
					<td  colspan=2>
						Drum RPM
					</td>
					<td>&nbsp;

					</td>
					<td  colspan=2>
						Counter RPM
					</td>
					<td>&nbsp;

					</td>
				</tr>
				<tr>
					<td>
						Over Feed %
					</td>
					<td>&nbsp;

					</td>
					<td  colspan=2>
						After Width
					</td>
					<td>&nbsp;

					</td>
					<td  colspan=2 height="30"><b>Signature &amp; Date</b></td>
					<td  colspan=4>&nbsp;

					</td>
				</tr>
				<tr>
					<td>
						Presure/Steam
					</td>
					<td>&nbsp;

					</td>
					<td  colspan=2>
						After GSM
					</td>
					<td>&nbsp;

					</td>
					<td  colspan=6 align="center"><b>
						Singeing
					M/C</b>
				</td>
			</tr>
			<tr>
				<td>
					Compaction%
				</td>
				<td>
					L%=
				</td>
				<td  colspan=3>
					W%=
				</td>
				<td>
					Temperature (&#176;C)
				</td>
				<td>&nbsp;

				</td>
				<td>&nbsp;

				</td>
				<td  colspan=2>
					Speed
				</td>
				<td>&nbsp;

				</td>
			</tr>
			<tr>
				<td>
					Shrinkage %
				</td>
				<td>
					L%=
				</td>
				<td  colspan=2>
					W%=
				</td>
				<td>
					<span style="font-size:12px;">Spi%=</span>
				</td>
				<td>
					Burner Distance
				</td>
				<td>&nbsp;

				</td>
				<td>&nbsp;

				</td>
				<td  colspan=2>
					Burner count
				</td>
				<td>&nbsp;

				</td>
			</tr>
			<tr>
				<td height="30"><b>Signature &amp; Date</b></td>
				<td  colspan=4>&nbsp;

				</td>
				<td  colspan=2><b>Signature &amp; Date</b></td>
				<td  colspan=4>&nbsp;

				</td>
			</tr>
			<tr>
				<td  colspan=5 align="center"><b>Brush M/C</b></td>
				<td  colspan=6 align="center"><b>Bag Sweing</b></td>
			</tr>
			<tr>
				<td> M/C Speed(m/m)</td>
				<td  colspan=4>&nbsp;</td>
				<td>
					M/C Speed (m/m)
				</td>
				<td>&nbsp;

				</td>
				<td  colspan=4>&nbsp;

				</td>
			</tr>
			<tr>
				<td>
					Lower Drum RPM
				</td>
				<td>&nbsp;

				</td>
				<td  colspan=2>
					Upper
					Drum RPM
				</td>
				<td>&nbsp;

				</td>
				<td  colspan=2 height="30"><b>Signature &amp; Date</b></td>
				<td  colspan=4>&nbsp;

				</td>
			</tr>
			<tr>
				<td>
					Pile
				</td>
				<td>&nbsp;

				</td>
				<td  colspan=2>
					Pile
				</td>
				<td>&nbsp;

				</td>

				<td  colspan=6 align="center"><b>
				Biancalani/ Tumble Dry</b>
			</td>
		</tr>
		<tr>
			<td>
				Counter
			</td>
			<td>&nbsp;

			</td>
			<td  colspan=2>
				Counter
			</td>
			<td>&nbsp;

			</td>
			<td  colspan=2>
				Temperature (&#176;C)
			</td>
			<td>&nbsp;

			</td>
			<td  colspan=2>
				Blower
			</td>
			<td>&nbsp;

			</td>
		</tr>
		<tr>
			<td height="30"><b>Signature &amp; Date</b></td>
			<td  colspan=4>&nbsp;

			</td>
			<td  colspan=2>
				M/C Speed (m/m)
			</td>
			<td>&nbsp;

			</td>
			<td  colspan=2>
				Time
			</td>
			<td>&nbsp;

			</td>
		</tr>
		<tr>
			<td  colspan=5 align="center"><b>Stenter / Santex Dryer (2)</b></td>
			<td colspan=2 height="30"><b>Signature &amp; Date</b></td>
			<td  colspan=4>&nbsp;

			</td>
		</tr>
		<tr>
			<td>
				Temperature (&#176;C)
			</td>
			<td>&nbsp;

			</td>
			<td>
				2nd P.Pressure
			</td>
			<td  colspan=2>&nbsp;

			</td>
			<td  colspan=6 align="center"><b>
				Normal
			Compacting / Sanforizing Compacting</b>
		</td>
	</tr>
	<tr>
		<td>
			M/C Speed(m/m)
		</td>
		<td>&nbsp;

		</td>
		<td>
			Set Width
		</td>
		<td  colspan=2>&nbsp;

		</td>
		<td  colspan=2>
			Temperature (&#176;C)
		</td>
		<td>&nbsp;

		</td>
		<td  colspan=2>
			Before Width
		</td>
		<td>&nbsp;

		</td>
	</tr>
	<tr>
		<td>
			Over Feed %
		</td>
		<td>&nbsp;

		</td>
		<td>
			After Width
		</td>
		<td  colspan=2>&nbsp;

		</td>
		<td>
			M/C Speed (m/m)
		</td>
		<td>&nbsp;

		</td>
		<td>&nbsp;

		</td>
		<td  colspan=2>
			Set Width
		</td>
		<td>&nbsp;

		</td>
	</tr>
	<tr>
		<td>
			1st P.Pressure
		</td>
		<td>&nbsp;

		</td>
		<td>
			After GSM
		</td>
		<td  colspan=2>&nbsp;

		</td>
		<td  colspan=2>
			Over Feed %
		</td>
		<td>&nbsp;

		</td>
		<td  colspan=2>
			After Width
		</td>
		<td>&nbsp;

		</td>
	</tr>
	<tr>
		<td>
			Shrinkage %
		</td>
		<td>
			L=
		</td>
		<td>
			W=
		</td>
		<td  colspan=2>
			<span style="font-size:12px;">Spi%=</span>
		</td>
		<td  colspan=2>
			Presure/Steam
		</td>
		<td>&nbsp;

		</td>
		<td  colspan=2>
			After GSM
		</td>
		<td>&nbsp;

		</td>
	</tr>
	<tr>
		<td>
			Softener Use(G/L)
		</td>
		<td>&nbsp;

		</td>
		<td>&nbsp;

		</td>
		<td  colspan=2>&nbsp;

		</td>
		<td  colspan=2>
			Compaction%
		</td>
		<td>
			L%=
		</td>
		<td  colspan=2>
			W%=
		</td>
		<td>&nbsp;

		</td>
	</tr>
	<tr>
		<td height="30"><b>Signature &amp; Date</b></td>
		<td  colspan=4>&nbsp;

		</td>
		<td  colspan=2>
			Shrinkage %
		</td>
		<td>
			L%=
		</td>
		<td  colspan=2>
			W%=
		</td>
		<td>
			<span style="font-size:12px;">Spi%=</span>
		</td>
	</tr>
	<tr>
		<td  colspan=5 align="center"><b>
			Shearing
		M/C</b>
	</td>
	<td  colspan=2 height="30"><b>Signature &amp; Date</b></td>
	<td  colspan=3>&nbsp;

	</td>
	<td>&nbsp;

	</td>
</tr>
<tr>
	<td>
		M/C Speed(m/m)
	</td>
	<td>&nbsp;

	</td>
	<td>
		Cylender RPM
	</td>
	<td  colspan=2>&nbsp;

	</td>
	<td  colspan=6 align="center"><b>
		Final
	Packing List</b>
</td>
</tr>
<tr>
	<td>
		Blade Distance
	</td>
	<td>&nbsp;

	</td>
	<td>
		Taker Tension
	</td>
	<td  colspan=2>&nbsp;

	</td>
	<td>
		F/Dia
	</td>
	<td>
		Grey
		Wt.
	</td>
	<td>
		Fin Wt.
	</td>
	<td>
		F/Dia
	</td>
	<td>
		Grey
		Wt.
	</td>
	<td>
		Fin Wt.
	</td>
</tr>
<tr>
	<td height="30"><b>Signature &amp; Date</b></td>
	<td  colspan=4>&nbsp;

	</td>
	<td>&nbsp;

	</td>
	<td>&nbsp;

	</td>
	<td>&nbsp;

	</td>
	<td>&nbsp;

	</td>
	<td>&nbsp;

	</td>
	<td>&nbsp;

	</td>
</tr>
<tr>
	<td  colspan=5 align="center"><b>
		Compacting
	Finish (2)</b>
</td>
<td>&nbsp;

</td>
<td>&nbsp;

</td>
<td>&nbsp;

</td>
<td>&nbsp;

</td>
<td>&nbsp;

</td>
<td>&nbsp;

</td>
</tr>
<tr>
	<td  colspan=5 align="center"> Normal Compacting / Sanforizing Compacting </td>
	<td>
		Total=
	</td>
	<td>&nbsp;

	</td>
	<td>&nbsp;

	</td>
	<td>
		P.Loss%
	</td>
	<td>&nbsp;

	</td>
	<td>&nbsp;

	</td>
</tr>
<tr>
	<td>
		Temperature (&#176;C)
	</td>
	<td>&nbsp;

	</td>

	<td>
		Before Width
	</td>
	<td  colspan=2>&nbsp;

	</td>
	<td  colspan=6 rowspan=2 valign=top>
		Comments:
	</td>
</tr>
<tr>
	<td>
		M/C Speed(m/m)
	</td>
	<td>&nbsp;

	</td>
	<td>
		Set Width
	</td>
	<td  colspan=2>&nbsp;

	</td>
</tr>
<tr>
	<td>
		Over Feed %
	</td>
	<td>&nbsp;

	</td>
	<td>
		After Width
	</td>
	<td  colspan=2>&nbsp;

	</td>
	<td  colspan=6 rowspan=2 valign=top>
		Shade :
	</td>
</tr>
<tr>
	<td>
		Presure/Steam
	</td>
	<td>&nbsp;

	</td>
	<td>
		After GSM
	</td>
	<td  colspan=2>&nbsp;

	</td>
</tr>
<tr>
	<td>
		Compaction%
	</td>
	<td>
		L%=
	</td>
	<td  colspan=3>
		W%=
	</td>
	<td  colspan=6 rowspan=2 valign=top>Quality :
	</td>
</tr>
<tr>
	<td>
		Shrinkage %
	</td>
	<td>L%=</td>
	<td>W%=</td>
	<td  colspan=2><span style="font-size:12px;">Spi%=</span></td>
</tr>
<tr>
	<td height="50"><b>Signature &amp; Date</b></td>
	<td  colspan=4>&nbsp;

	</td>
	<td><b>Signature &amp; Date</b></td>
	<td  colspan=5>&nbsp;

	</td>
</tr>
</table>

</div>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 30,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }

        generateBarcode('<? echo $dataArray[0][csf('batch_no')]; ?>');
    </script>
    <?
    exit();
}
if ($action == "batch_card_print_10") {
	//echo load_html_head_contents("Batch Info","../../", 1, 1, '','','');
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$batch_update_id = $data[1];
	$working_company = $data[7];
	$batch_mst_update_id = str_pad($batch_update_id, 10, '0', STR_PAD_LEFT);
	//echo $batch_mst_update_id;die;
	$batch_sl_no = $data[2];
	$booking_no_id = $data[6];

	$roll_maintained = $data[8];
	if($roll_maintained==0)
	{
		echo "<p width='250'><b>Sorry, This Print Report is in roll level</b></p>";
		die;
	}
	//echo $data[3].$data[4];die;

	$company_sql = sql_select("select id, company_name,company_short_name from lib_company");
	foreach ($company_sql as $val)
	{
		$company_library[$val[csf("id")]] = $val[csf("company_name")];
		$company_library_short[$val[csf("id")]] = $val[csf("company_short_name")];
	}

	//$supplier_library = return_library_array("select id, short_name from lib_supplier", "id", "short_name");

	$machine_lib_sql = sql_select("select id, machine_no, dia_width, gauge,prod_capacity from lib_machine_name");

	foreach ($machine_lib_sql as $row) {
		$dya_gauge_arr[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$dya_gauge_arr[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$dya_gauge_arr[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
		$dya_gauge_arr[$row[csf("id")]]["prod_capacity"] = $row[csf("prod_capacity")];
	}

	$cons_comp_sql = sql_select("select a.id, a.construction,c.composition_name, b.percent
		from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b, lib_composition_array c
		where a.id = b.mst_id and b.copmposition_id = c.id  and a.status_active=1 and b.status_active=1 and c.status_active=1");
	foreach ($cons_comp_sql as  $val)
	{
		$cons_comp_arr[$val[csf("id")]]["const"] = $val[csf("construction")];
		$cons_comp_arr[$val[csf("id")]]["compo"] .=$val[csf("composition_name")] .",". $val[csf("percent")] . "% ";

	}

	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$floor_arr = return_library_array("select id,floor_name from lib_prod_floor  where status_active =1 and is_deleted=0", 'id', 'floor_name');

	$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
	$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');
	$yarn_type_from_prod = return_library_array("select id,yarn_type from  product_details_master where item_category_id=1 ", "id", "yarn_type");

	$job_array = array();
	$job_sql = "select distinct(a.buyer_name) as buyer_name,a.style_ref_no, a.job_no_prefix_num, a.job_no, b.pub_shipment_date, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
		$job_array[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
	}

	if ($db_type == 0) {
		$sql = "select a.id, a.company_id,a.working_company_id,a.batch_no,a.is_sales, a.booking_no_id, a.booking_no,a.booking_without_order,a.sales_order_no,a.color_id,a.floor_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.batch_date,a.cuff_qty, a.SAVE_STRING, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, group_concat(b.barcode_no) as barcode_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id,a.company_id,a.working_company_id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id,a.floor_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id, a.sales_order_no,a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty,a.batch_date, a.cuff_qty, a.SAVE_STRING";
	} else {
		$sql = "select a.id, a.company_id,a.working_company_id,a.batch_no,a.is_sales, a.booking_no_id,a.booking_no,a.booking_without_order,a.sales_order_no, a.color_id,a.floor_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty,a.batch_date, a.cuff_qty, a.SAVE_STRING, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id,LISTAGG(CAST(b.barcode_no AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.barcode_no) AS barcode_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id,a.company_id,a.working_company_id, a.batch_no,a.is_sales, a.color_id,a.floor_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for,a.sales_order_no, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty,a.batch_date,a.cuff_qty, a.SAVE_STRING";
	}
	 //echo $sql;
	$dataArray = sql_select($sql);

	$po_number = "";
	$job_number = "";
	$job_style = "";
	$buyer_id = "";
	$ship_date = "";
	$internal_ref = "";
	$file_nos = "";
	$po_id = array_unique(explode(",", $dataArray[0][csf('po_id')]));
	$barcode_no = implode(",",array_unique(explode(",", $dataArray[0][csf('barcode_no')])));
	$booking_no = $dataArray[0][csf('booking_no')];
	$is_sales = $dataArray[0][csf('is_sales')];
	$batch_against_id = $dataArray[0][csf('batch_against')];
	$batch_booking_id = $dataArray[0][csf('booking_no_id')];
	$batch_product_id = $dataArray[0][csf('prod_id')];
	$sales_order_no = $dataArray[0][csf('sales_order_no')];
	$batch_booking_without = $dataArray[0][csf('booking_without_order')];
	foreach ($po_id as $val) {
		if ($po_number == "") $po_number = $job_array[$val]['po']; else $po_number .= ',' . $job_array[$val]['po'];
		if ($job_number == "") $job_number = $job_array[$val]['job']; else $job_number .= ',' . $job_array[$val]['job'];
		if ($job_style == "") $job_style = $job_array[$val]['style']; else $job_style .= ',' . $job_array[$val]['style'];
		if($is_sales!=1)
		{
			if ($buyer_id == "") $buyer_id = $job_array[$val]['buyer']; else $buyer_id .= ',' . $job_array[$val]['buyer'];
		}
		if ($ship_date == "") $ship_date = change_date_format($job_array[$val]['ship_date']); else $ship_date .= ',' . change_date_format($job_array[$val]['ship_date']);

		if ($internal_ref == "") $internal_ref = $job_array[$val]['ref']; else $internal_ref .= ',' . $job_array[$val]['ref'];
		if ($job_array[$val]['file_no'] > 0) {
			if ($file_nos == "") $file_nos = $job_array[$val]['file_no']; else $file_nos .= ',' . $job_array[$val]['file_no'];
		}
	}

	$job_no = implode(",", array_unique(explode(",", $job_number)));
	$jobstyle = implode(",", array_unique(explode(",", $job_style)));
	$po_numbers = implode(",", array_unique(explode(",", $po_number)));
	$buyer = implode(",", array_unique(explode(",", $buyer_id)));
	$internal_ref = implode(",", array_unique(array_filter(explode(",", $internal_ref))));
	$file_nos = implode(",", array_unique(explode(",", $file_nos)));
	//$booking_without_order=return_field_value( "booking_no as booking_no", "wo_non_ord_samp_booking_mst","booking_no=$booking_no","booking_no");

	if ($dataArray[0][csf('booking_without_order')] == 1) {
		$booking_without_order = sql_select("select a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id,b.process_loss from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$company and a.booking_no='$booking_no' and a.booking_type=4");
		foreach ($booking_without_order as $row) {
			$color_type_array[$row[csf('booking_no')]][$row[csf('body_part')]]['color_type_id'] = $row[csf('color_type_id')];
			$process_loss_array[$row[csf('booking_no')]][$row[csf('body_part')]]['color_type_id'] = $row[csf('process_loss')];
			//$booking_id = $row[csf('booking_no_prefix_num')];
			$buyer = $row[csf('buyer_id')];
		}


	} else {


		$booking_with_order = sql_select("select booking_no_prefix_num, buyer_id from wo_booking_mst where company_id=$company and booking_no='$booking_no'");
		//$booking_id = $booking_with_order[0][csf('booking_no_prefix_num')];
		$buyer = $booking_with_order[0][csf('buyer_id')];

		$color_sql = "select  b.booking_no,b.dia_width,b.fabric_color_id,c.color_type_id,c.body_part_id ,c.lib_yarn_count_deter_id, c.gsm_weight,c.avg_process_loss from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company and a.booking_no='$booking_no' and a.booking_type=1 and b.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.booking_no,b.dia_width,b.fabric_color_id,c.color_type_id,c.body_part_id, c.lib_yarn_count_deter_id, c.gsm_weight,c.avg_process_loss";
		$color_sql_result = sql_select($color_sql);
		foreach ($color_sql_result as $row) {
			//$color_type_array[$row[csf('booking_no')]][$row[csf('body_part_id')]]['color_type_id'] = $row[csf('color_type_id')];
			$color_type_array_precost[$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]]['color_type_id'] = $row[csf('color_type_id')];
			//$process_loss_array[$row[csf('booking_no')]][$row[csf('body_part_id')]]['color_type_id'] = $row[csf('avg_process_loss')];
			$process_loss_array[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('fabric_color_id')]] = $row[csf('avg_process_loss')];
		}
	}
	$sales_sql = "select a.buyer_id,a.po_buyer,a.within_group,b.job_no_mst as booking_no,b.color_type_id,b.body_part_id,b.process_loss from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company and a.job_no='$sales_order_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.job_no_mst ,b.color_type_id,b.body_part_id,b.process_loss,a.buyer_id,a.po_buyer,a.within_group";
	$sales_result = sql_select($sales_sql);
	foreach ($sales_result as $row) {
		$sales_color_type_array[$row[csf('booking_no')]][$row[csf('body_part_id')]]['color_type_id'] = $row[csf('color_type_id')];
		$sales_process_loss_array[$row[csf('booking_no')]][$row[csf('body_part_id')]]['color_type_id'] = $row[csf('process_loss')];
		if($row[csf('within_group')]==1)
		{
			$buyer = $row[csf('po_buyer')];
		}
		else {
			$buyer = $row[csf('buyer_id')];
		}
	}


	?>
	<div style="width:1060px;">
		<table width="1060" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="6" align="center" style="font-size:22px">
					<strong><? echo $company_library[$working_company]; ?></strong></td>
					<td colspan="2" align="left">Print Time:<? echo $date = date("F j, Y, g:i a"); ?></td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:18px"><strong><u>Batch Card</u></strong></td>
					<td colspan="2" id="barcode_img_id" align="right" style="font-size:24px"></td>
				</tr>
				<tr>
					<td colspan="8">&nbsp; </td>
				</tr>
				<tr>
					<td colspan="6" align="left" style="font-size:22px"><strong><u>Reference Details</u></strong></td>
					<td style="font-size:24px; border: solid 2px;" align="center" colspan="2">&nbsp;<? echo $dataArray[0][csf('organic')]; ?></td>
				</tr>
				<tr style="font-size:20px">
					<td width="113"><strong>Buyer</strong></td>
					<td width="146">:
						<?
						$buyer_ids = explode(",", $buyer);
						$buyerName = "";
						foreach ($buyer_ids as $val) {

							if ($buyerName == "")
							{
								$buyerName = $buyer_arr[$val];
							}
							else {

								$buyerName .= $buyer_arr[$val] . ",";
							}
						}

						$buyerName = chop($buyerName, ',');
						//echo "**".$buyer_id_booking."==".$dataArray[0][csf('batch_against')];die;
						if ($dataArray[0][csf('batch_against')] == 3)
						{
							echo $buyer_arr[$buyer];
						} else {
							echo $buyerName;
						}
						?>
					</td>
					<td width="125"><strong>Dyeing Comp</strong></td>
					<td width="168">:&nbsp;<? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
					<td width="115"><strong>B. Against</strong></td>
					<td width="153">:&nbsp;<? echo $batch_against[$dataArray[0][csf('batch_against')]]; ?></td>
					<td width="125"><strong>Batch Weight</strong></td>
					<td width="155">:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?></td>
				</tr>
				<tr style="font-size:18px">
					<td><strong>Order No</strong></td>
					<td>:&nbsp;<? echo $po_numbers; ?></td>
					<td><strong>Floor</strong></td>
					<td>:&nbsp;<? echo $floor_arr[$dataArray[0][csf('floor_id')]]; ?></td>
					<td><strong>Batch For</strong></td>
					<td>:&nbsp;<? echo $batch_for[$dataArray[0][csf('batch_for')]]; ?></td>
					<td><strong>Ship Date</strong></td>
					<td>:
						<? if (trim($ship_date) != "0000-00-00" && trim($ship_date) != "") echo implode(",", array_unique(explode(",", $ship_date))); else echo "&nbsp;"; ?></td>
					</tr>
					<tr style="font-size:18px">
						<td><strong>Color</strong></td>
						<td>  :&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
						<td><strong>Color Range</strong></td>
						<td>:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range_id')]]; ?></td>

						<td><strong>Style Ref.</strong></td>
						<td>:&nbsp;<? echo $jobstyle; ?></td>

						<td><strong>Collar Qty (Pcs)</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('collar_qty')]; ?></td>
					</tr>
					<tr style="font-size:18px">
						<td><strong>Batch No</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('batch_no')];//$company_library_short[$dataArray[0][csf('working_company_id')]].'-'. ?></td>
						<td><strong>Batch Ext.</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('extention_no')]; ?></td>
						<td><strong>Batch Date</strong></td>
						<td>:&nbsp;<? echo change_date_format($dataArray[0][csf('batch_date')]); ?></td>
						<td><strong>Cuff Qty (Pcs)</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('cuff_qty')]; ?></td>
					</tr>
					<tr style="font-size:18px">
						<td><strong>Dyeing Machine</strong></td>
						<td>:
							<?
							if ($db_type == 2) {
								$dyeing_machine = return_field_value("(machine_no || '-' || brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
							} else if ($db_type == 0) {
								$dyeing_machine = return_field_value("concat(machine_no,'-',brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
							}
							echo $dyeing_machine;
							?></td>
							<td><strong>M/C Capacity</strong></td>
							<td>:&nbsp;<? echo $dya_gauge_arr[$dataArray[0][csf('dyeing_machine')]]["prod_capacity"]; ?></td>
							<td><strong>Remarks</strong></td>
							<td colspan="3">:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
						</tr>
						<tr style="font-size:18px">
							<td><strong>Booking No</strong></td>
							<td colspan="7">:&nbsp;<? echo $booking_no; ?></td>
						</tr>

					</table>
					<div style="float:left; font-size:22px;"><strong><u>Fabrication Details</u></strong></div>
					<table align="left" cellspacing="0"  style="font-size:18px" width="1060" border="1" rules="all" class="rpt_table"	style="border-top:none">
						<thead bgcolor="#dddddd" align="center">
							<tr>
								<th width="30">Cut Mark</th>
								<th width="60">Prog. No</th>
								<th width="80">Machine / Knitting Floor</th>
								<th width="70">Body part</th>
								<th width="80">Color Type</th>
								<th width="150">Const. & Comp.</th>
								<th width="40">Fin. GSM</th>
								<th width="40">Fin. Dia</th>
								<th width="70">M/Dia X Gauge</th>
								<th width="70">D/W Type</th>
								<th width="40">S.Length</th>
								<th width="70">Grey Qty.</th>
								<th width="50">Total Roll</th>
								<th width="80"><strong>Brand</strong></th>
								<th width="60">Yarn Lot</th>
								<th width="80">Count & Type</th>
								<th>P. Loss</th>
							</tr>
						</thead>
						<?
						$i = 1;



						if ($db_type == 0) {
							$recv_basis_grop = " group_concat(a.receive_basis) AS receive_basis";
						} else {
							$recv_basis_grop = " LISTAGG(a.receive_basis, ',') WITHIN GROUP (ORDER BY a.receive_basis) AS receive_basis";
						}

						if ($dataArray[0][csf('booking_without_order')] == 1 && $batch_against_id == 3) {

							$receive_basis = return_field_value("$recv_basis_grop", "inv_receive_master a,pro_roll_details b", "a.id=b.mst_id  and (a.booking_id='" . $booking_no_id . "' OR b.barcode_no in($barcode_no)) and a.entry_form in(2,22) and b.entry_form in(2,22) and a.receive_basis in (0,1,11)", "receive_basis");
						} else {

							$receive_basis = return_field_value("$recv_basis_grop", "inv_receive_master a,pro_roll_details b", "a.id=b.mst_id and (a.booking_id='" . $booking_no_id . "' OR b.barcode_no in($barcode_no)) and a.entry_form in(2,22) and b.entry_form in(2,22) and a.receive_basis in (0,1,11)", "receive_basis");
						}

						$receivebasis = array_unique(explode(",", $receive_basis));
						foreach ($receivebasis as $rcvid) {
							if ($rcvid == 0 || $rcvid == 1 || $rcvid == 11) {
								$machine_info = "d.machine_dia,d.machine_gg,";
							} else {
								$machine_info = "";
							}
						}
						$sql_dtls = "select d.id as dtls_id,a.dyeing_machine,a.booking_no_id,e.receive_basis,e.booking_id,a.booking_without_order,a.is_sales, a.color_id, (b.batch_qnty) AS batch_qnty, (b.roll_no) as roll_no,d.febric_description_id,d.gsm,d.width, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, b.width_dia_type as num_of_rows, d.machine_no_id,$machine_info c.barcode_no,d.yarn_lot,d.yarn_count,d.brand_id,d.yarn_prod_id,d.floor_id,d.stitch_length
						from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
						where a.id=b.mst_id and b.roll_id = c.id and c.dtls_id=d.id and d.prod_id=b.prod_id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22) and c.entry_form in(2,22)
						order by b.program_no";
	//echo $sql_dtls;die;dyeing_machine floor_id
 	//echo $sql_dtls;
						$sql_result = sql_select($sql_dtls);
						foreach ($sql_result as $row) {

							$yarn_lot=$row[csf('yarn_lot')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['program_no'].=$row[csf('program_no')].',';
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['barcode_no'].=$row[csf('barcode_no')].',';
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['dyeing_machine']=$row[csf('dyeing_machine')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['booking_id']=$row[csf('booking_id')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['yarn_lot'].=$row[csf('yarn_lot')].',';
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['brand_id'].=$row[csf('brand_id')].',';
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['yarn_count']=$row[csf('yarn_count')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['yarn_prod_id'].=$row[csf('yarn_prod_id')].',';
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['roll_no']=$row[csf('roll_no')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['item_description']=$row[csf('item_description')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['febric_description_id']=$row[csf('febric_description_id')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['machine_no_id']=$row[csf('machine_no_id')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['gsm']=$row[csf('gsm')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['prod_id']=$row[csf('prod_id')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['batch_qnty']+=$row[csf('batch_qnty')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['width']=$row[csf('width')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['body_part_id']=$row[csf('body_part_id')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['width']=$row[csf('width')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['width_dia_type']=$row[csf('width_dia_type')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['receive_basis']=$row[csf('receive_basis')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['floor_id']=$row[csf('floor_id')];
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['stitch_length'].=$row[csf('stitch_length')].',';

							$is_sales=$row[csf('is_sales')];
							if($is_sales==1) //Sales
							{
								$color_type_id=$sales_color_type_array[$sales_order_no][$row[csf('body_part_id')]]['color_type_id'];

								$process_loss=$sales_process_loss_array[$sales_order_no][$row[csf('body_part_id')]]['color_type_id'];
							}
							else
							{
								if($row[csf('booking_without_order')] == 1)
								{
									$color_type_id=$color_type_array[$booking_no][$row[csf('body_part_id')]]['color_type_id'];
									$process_loss=$process_loss_array[$booking_no][$row[csf('body_part_id')]]['color_type_id'];
								}
								else
								{
									$color_type_id=$color_type_array_precost[$booking_no][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]]['color_type_id'];

									$process_loss= $process_loss_array[$booking_no][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('color_id')]];
								}

							}
							if ($row[csf('receive_basis')] == 0 || $row[csf('receive_basis')] == 1 || $row[csf('receive_basis')] == 4 || $row[csf('receive_basis')] == 11) //from Entry page
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
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['color_type_id']=$color_type_id;
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['mc_dia']=$machine_dia_width;
							$fabric_roll_arr[$row[csf('prod_id')]][$yarn_lot]['mc_gg']=$machine_gauge;

						}
						$all_barcode = "";
						foreach ($fabric_roll_arr as $prod_id=>$yarnLot_arr) {
							foreach ($yarnLot_arr as $yarnLot=>$row) {
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								$desc = explode(",", $row[('item_description')]);

								$all_barcode[$row[('machine_no_id')]][$row[("barcode_no")]]= chop($row[("barcode_no")],',');
							 //$yarn_lots=chop($row[('yarn_lot')],',');
								$yarn_lots=chop($yarnLot,',');
								$yarn_lot = implode(",", array_unique(explode(",", $yarn_lots)));
								$y_count = array_unique(explode(",", $row[('yarn_count')]));
								$brand_id = array_unique(explode(",", $row[('brand_id')]));
								$yarn_prod_id = array_unique(explode(",", $row[('yarn_prod_id')]));
								$yarn_count_value = "";
								foreach ($y_count as $val) {
									if ($val > 0) {
										if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
									}
								}
								$brand_value = array();
								foreach ($brand_id as $bid) {
									if ($bid > 0) {
										$brand_value[$brand_name_arr[$bid]]=$brand_name_arr[$bid];
									}
								}
								$type_value = "";
								foreach ($yarn_prod_id as $tid) {
									if ($tid > 0) {
										if ($type_value == '') $type_value = $yarn_type[$yarn_type_from_prod[$tid]]; else $type_value .= ", " . $yarn_type[$yarn_type_from_prod[$tid]];
									}
								}

								$stitch_length=chop($row[('stitch_length')],',');
								$stitch = implode(",", array_unique(explode(",", $stitch_length)));

								$barcode_nos=rtrim($row[('barcode_no')],',');
								$program_nos=rtrim($row[('program_no')],',');
								$num_of_rows=count(array_unique(explode(",", $barcode_nos)));
								$program_nos = implode(",", array_unique(explode(",", $program_nos)));
								$machine_dia_width=chop($row[('mc_dia')],',');
								$machine_gauge=chop($row[('mc_gg')],',');
								$dya_gage = $machine_dia_width . "<br>" . $machine_gauge;


								?>
								<tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:21px">
									<td width="30"></td>
									<td width="60" align="center" style="word-break:break-all;"><? echo $program_nos; ?></td>
									<?

									$machin_knit_com = $dya_gauge_arr[$row[('machine_no_id')]]["machine_no"].'<br>'.$floor_arr[$row[('floor_id')]];
									?>
									<td width="80" style="word-break:break-all;" align="center"><? echo $machin_knit_com; ?></td>
									<td width="70" style="word-break:break-all;"><? echo $body_part[$row[('body_part_id')]]; ?></td>
									<td width="80" style="word-break:break-all;"><? echo $color_type[$color_type_id]; ?></td>

									<td width="150" style="word-break:break-all;"><? echo $cons_comp_arr[$row[("febric_description_id")]]["const"].'<br>'.$cons_comp_arr[$row[("febric_description_id")]]["compo"]; ?></td>
									<td width="40" align="center" style="word-break:break-all;"><? echo $row[('gsm')];//$desc[2]; ?></td>
									<td width="40" align="center" style="word-break:break-all;"><? echo $row[('width')];//$desc[3]; ?></td>
									<td width="70" align="center" style="word-break:break-all;"><? echo $dya_gage; ?></td>
									<td width="70"
									style="word-break:break-all;"><? echo $fabric_typee[$row[('width_dia_type')]]; ?></td>
									<td width="40" align="center" style="word-break:break-all;"><? echo $stitch; ?></td>
									<td width="70" align="right"
									style="word-break:break-all;"><? echo number_format($row[('batch_qnty')], 2); ?></td>
									<td align="center" width="50"
									style="word-break:break-all;"><? echo $num_of_rows; ?></td>
									<td width="80" style="word-break:break-all;"><? echo implode(',',$brand_value); ?></td>
									<td width="60"
									style="word-break:break-all;"><? echo implode(',', array_unique(explode(",", $yarn_lot))); ?></td>
									<td width="80" style="word-break:break-all;" align="center"><?
									if($yarn_count_value!="" && $type_value!="") echo $yarn_count_value.' & '.$type_value;
									else if($yarn_count_value!="" && $type_value=="") echo $yarn_count_value;
									else if($yarn_count_value=="" && $type_value=="") echo "";
									?></td>
									<td align="right"><? echo $process_loss; ?></td>
								</tr>
								<?php
								$total_roll_number += $num_of_rows;
								$total_batch_qty += $row[('batch_qnty')];
								$i++;
							}
						}
		//$all_barcode = implode(",", array_unique(explode(",", chop($all_barcode, ","))));
						?>
						<tr  style="font-size:18px">
							<td style="border:none; word-break:break-all;" colspan="10" rowspan="3" valign="top"><p>
								<?
					/*foreach($all_barcode as $machine_id=>$barcodeArr){
						$allBarcode=implode(", ", $barcodeArr);
					//	echo '[ <b>'.$dya_gauge_arr[$machine_id]["machine_no"].':</b>'.implode(", ", $barcodeArr).' ]';
					}*/

					$barcode_noss=rtrim($all_barcodes,',');
					echo implode(", ",array_unique(explode(",",$barcode_noss)));

					?>
				</p></td>
				<td style="border:none;" align="right"><b>Sum:</b></td>
				<td align="right"><b><? echo number_format($total_batch_qty, 2); ?> </b></td>
				<td align="center"><b><? echo $total_roll_number; ?> </b></td>
				<td colspan="5" style="border:none;">&nbsp;</td>
			</tr>
			<tr  style="font-size:18px">
				<td style="border:none;" align="right"><b>Trims Weight:</b></td>
				<td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')], 2); ?> </b>
				</td>
				<td colspan="6" style="border:none;">&nbsp;</td>
			</tr>
			<tr  style="font-size:18px">
				<td style="border:none;" align="right"><b>Total:</b></td>
				<td align="right">
					<b><? echo number_format($total_batch_qty + $dataArray[0][csf('total_trims_weight')], 2); ?> </b>
				</td>
				<td colspan="6" style="border:none;">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="17" align="right">&nbsp; </td>
			</tr>
			<tr  style="font-size:18px">
				<td colspan="17">
					<?
					$save_str = $dataArray[0][csf('SAVE_STRING')];
					$save_ref = explode("!!", $save_str);
					if (!empty($dataArray[0][csf('SAVE_STRING')]) > 0) {
						?>
						<table align="left" rules="all" class="rpt_table" width="100%" border="1">
							<thead>
								<tr>
									<th align="left" style="font-size:20px;" colspan="4"><strong>Trims Details</strong></th>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="250">Item Description</th>
									<th width="150">Weight In Kg</th>
									<th>Remarks</th>
								</tr>
							</thead>
							<tbody>
								<?
								$i = 1;
								foreach ($save_ref as $data_ref) {
									$data_ref = explode("_", $data_ref);
									?>
									<tr>
										<td align="center"><? echo $i; ?></td>
										<td><? echo $data_ref[0]; ?> </td>
										<td align="right"><? echo number_format($data_ref[1], 2); ?></td>
										<td align="center"><? echo $data_ref[2]; ?></td>
									</tr>
									<?
									$i++;
								}
								?>
							</tbody>
						</table>
						<?
					}
					?>

				</td>
			</tr>
			<tr  style="font-size:18px">
				<td colspan="17" align="right">
					<?
					$process = $dataArray[0][csf('process_id')];
					$process_id = explode(',', $process);
					//print_r($process_id);
					$process_value = '';
					$i = 1;
					foreach ($process_id as $val) {
						if ($process_value == '') $process_value = $i . '. ' . $conversion_cost_head_array[$val]; else $process_value .= ", " . $i . '. ' . $conversion_cost_head_array[$val];
						$i++;
					}
					?>

				</td>
			</tr>
		</table>
		<br>
		<table align="left" rules="all" class="rpt_table" width="1060"  style="font-size:18px">
			<tr>
				<th align="left" style="font-size:20px;"><strong>Process Required</strong></th>
			</tr>
			<tr>
				<td style="font-size:20px;" title="<? echo $process_value; ?>">
					<p><? echo $process_value; ?></p>
				</td>
			</tr>
		</table>
		<br>
		<table border="1" rules="all" width="1060"  style="font-size:18px">
			<tr>
				<td align="left" style="font-size:19px;">Loading Date & Time:</td>
				<td align="left" style="font-size:19px;">Un-loading Date & Time:</td>
			</tr>
		</table>
		<br>

		<br>
		<?
		echo get_spacial_instruction($batch_sl_no,"1060px",64);
		echo signature_table(52, $company, "1060px");
		?>
		<style>
			@media print {
				.break {page-break-after: always!important;}
			}
		</style>

		<p class="break"></p>

		<table id="batch_form" border="1" cellpadding="0" cellspacing="0" rules="all"    style="font-size:18px;" class="rpt_table" width="100%">
			<tr>
				<td style="border-bottom:1px solid #fff;" align="center" colspan="12">
					<b style="font-size:20px;"><? echo $company_library[$working_company];?></b>
				</td>
			</tr>
			<tr>
				<td  colspan=12 align="center"><b>Finishing</b></td>
			</tr>

			<tr style="height:40px;">
				<td align="left" width="100" colspan="7"><b>RQD DIA:</b></td>

				<td align="left" width="100"  colspan="7"><b>ACT DIA:</b></td>

			</tr>
			<tr style="height:40px;">
				<td align="left" width="100"  colspan="7"><b>RQD G.S.M:</b></td>

				<td align="left" width="100"  colspan="7"><b>ACT G.S.M:</b></td>

			</tr>
			<tr>
				<td colspan="6" align="center"><b>GREY INFORMATION</b></td>
				<td rowspan="12" width="5"></td>
				<td colspan="6" align="center"><b>HEAT SET INFORMATION</b></td>
			</tr>
			<tr style="height:50px;">
				<td width=129 colspan="6">M/C DIA :</td>

				<td width=100 colspan="3">Temp :</td>
				<td width=100 colspan="4">Speed:</td>

			</tr>
			<tr style="height:50px;">
				<td colspan="6">ACT DIA :</td>

				<td  colspan="3"  width=100  align="left">O.F:</td>
				<td  colspan="4"  width=100  align="left">UNF:</td>

			</tr>
			<tr style="height:50px;">
				<td colspan="6">G/C. S. M :</td>

				<td colspan="7">SHAPE/PIN SETTING:</td>

			</tr>
			<tr style="height:50px;">
				<td height="30" colspan="6"><b>DATE :</b></td>

				<td colspan="3" width="100" ><b>A/HEAT SET DIA:</b></td>
				<td colspan="4" width="100" >G.S.M:</td>
			</tr>
			<tr style="height:50px;">
				<td height="30" colspan="6"><b>CHECKED BY :</b></td>

				<td colspan="3" style="border-bottom:1px"  width="100"><b>DATE :</b></td>
				<td colspan="4" style="border-bottom:1px"  width="100">OPERATOR:</td>
			</tr>

		</table>
		<br>
		<table id="batch_form" border="1" cellpadding="0" cellspacing="0"  style="font-size:18px" rules="all"  class="rpt_table" width="100%">
			<tr style="height:25px;">
				<td colspan="6" align="center"><b>FINISH STENTER</b></td>
				<td rowspan="10"  width="5"></td>
				<td colspan="7" align="center"><b>COMPACTING INFORMATION</b></td>
			</tr>
			<tr style="height:50px;">
				<td width=129 colspan="3">TEMP :</td>
				<td width=129 colspan="3">SPEED :</td>

				<td width=100 colspan="3">TEMP :</td>
				<td width=100 colspan="4">SPEED:</td>

			</tr>
			<tr style="height:50px;">
				<td colspan="3">O.F:</td>
				<td colspan="3">UNF :</td>

				<td  colspan="3"  width=100  align="left">O.F:</td>
				<td  colspan="4"  width=100  align="left">UNF:</td>

			</tr>
			<tr style="height:50px;">
				<td colspan="3">SHAPE/PIN SETTING:</td>
				<td colspan="3"></td>

				<td colspan="3">SHAPE/PIN SETTING:</td>
				<td colspan="4"></td>

			</tr>
			<tr style="height:50px;">
				<td height="30" colspan="3"><b>STENTER DIA :</b></td>
				<td height="30" colspan="3"><b>A/G.S.M :</b></td>

				<td colspan="3"  width=100 ><b>A/COMPATOR DIA:</b></td>
				<td colspan="4"  width=100 >G.S.M:</td>
			</tr>
			<tr style="height:50px;">
				<td height="30" colspan="3"><b>DATE :</b></td>
				<td colspan="3">OPERATOR:	</td>

				<td colspan="3" style="border-bottom:1px"  width=100 ><b>DATE :</b></td>
				<td colspan="4"  style="border-bottom:1px"  width=100 >OPERATOR:	</td>
			</tr>

		</table>
		<br><br><br><br><br><br>
		<table id="signatureTblId" width="100%"  style="font-size:21px">
			<tr>
				<td valign="top" colspan="6"><strong style="text-decoration:overline">F/SUPERVISON</strong></td>
				<td></td>
				<td valign="top" colspan="6"><strong style="text-decoration:overline"> F/IN-Charge</strong></td>
			</tr>
		</table>

	</div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 30,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }

        generateBarcode('<? echo $dataArray[0][csf('batch_no')]; ?>');
    </script>
    <?
    exit();
}

if ($action == "batch_card_print_5") {     // this button roll level only

	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$batch_update_id = $data[1];
	$working_company = $data[7];
	$batch_mst_update_id = str_pad($batch_update_id, 10, '0', STR_PAD_LEFT);
	$batch_sl_no = $data[2];
	$booking_no_id = $data[6];

	$roll_maintained = $data[8];
	if($roll_maintained==0)
	{
		echo "<p width='250'><b>Sorry, This Print Report is in roll level</b></p>";
		die;
	}

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$machine_library = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
	$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');

	$job_array = array();
	$job_sql = "select distinct(a.buyer_name) as buyer_name,a.style_ref_no, a.job_no_prefix_num, a.job_no, b.pub_shipment_date, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
		$job_array[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
	}

	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no,a.batch_date, a.booking_no_id, a.booking_no,a.booking_without_order,a.sales_order_no,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, group_concat(b.barcode_no) as barcode_no,a.is_sales from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.batch_no,a.batch_date, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id, a.sales_order_no,a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty, a.cuff_qty, a.SAVE_STRING,a.is_sales";
	} else {
		$sql = "select a.id, a.batch_no,a.batch_date, a.booking_no_id,a.booking_no,a.booking_without_order,a.sales_order_no, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id,LISTAGG(CAST(b.barcode_no AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.barcode_no) AS barcode_no,a.is_sales from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.batch_no,a.batch_date, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for,a.sales_order_no, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty,a.cuff_qty, a.SAVE_STRING,a.is_sales";
	}
	//echo $sql;
	$dataArray = sql_select($sql);

	$po_number = "";
	$job_number = "";
	$job_style = "";
	$buyer_id = "";
	$ship_date = "";
	$internal_ref = "";
	$file_nos = "";
	$body_part_type = return_library_array("select id, body_part_type from lib_body_part", 'id', 'body_part_type');
	$brandSupplier = return_library_array("select a.lot,b.short_name from  product_details_master  a, lib_supplier b where a.supplier_id=b.id and a.item_category_id=1 and a.company_id=$company ", 'lot', 'short_name');
	//print_r($brandSupplier);
	$po_id = array_unique(explode(",", $dataArray[0][csf('po_id')]));
	$barcode_no = implode(",",array_unique(explode(",", $dataArray[0][csf('barcode_no')])));
	$booking_no = $dataArray[0][csf('booking_no')];
	$batch_against_id = $dataArray[0][csf('batch_against')];
	$batch_booking_id = $dataArray[0][csf('booking_no_id')];
	$batch_product_id = $dataArray[0][csf('prod_id')];
	$sales_order_no = $dataArray[0][csf('sales_order_no')];
	$batch_booking_without = $dataArray[0][csf('booking_without_order')];

	if ($dataArray[0][csf('is_sales')] == 1) {
		$sales_order_id = $po_id[0];
		$sales_data = sql_select("select id,job_no,sales_booking_no,within_group,buyer_id,style_ref_no from fabric_sales_order_mst where id=$sales_order_id");
		if ($sales_data[0][csf("within_group")] == 1) {
			$booking_data = sql_select("select b.job_no,a.buyer_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no='$booking_no' and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 group by b.job_no,a.buyer_id");
			$job_number = $booking_data[0][csf("job_no")];
			$buyer_id = $booking_data[0][csf("buyer_id")];
			$po_number = $sales_data[0][csf("job_no")];
			$job_style = $job_array[$job_number]['style_ref_no'];
			$ship_date = "";
		} else {
			$po_number = $sales_data[0][csf("job_no")];
			$job_number = "";
			$buyer_id = $sales_data[0][csf("buyer_id")];
			$job_style = $sales_data[0][csf("style_ref_no")];
			$ship_date = "";
		}
	} else {
		foreach ($po_id as $val) {
			if ($po_number == "") $po_number = $job_array[$val]['po']; else $po_number .= ',' . $job_array[$val]['po'];
			if ($job_number == "") $job_number = $job_array[$val]['job']; else $job_number .= ',' . $job_array[$val]['job'];
			if ($job_style == "") $job_style = $job_array[$val]['style']; else $job_style .= ',' . $job_array[$val]['style'];
			if ($buyer_id == "") $buyer_id = $job_array[$val]['buyer']; else $buyer_id .= ',' . $job_array[$val]['buyer'];
			if ($ship_date == "") $ship_date = change_date_format($job_array[$val]['ship_date']); else $ship_date .= ',' . change_date_format($job_array[$val]['ship_date']);

			if ($internal_ref == "") $internal_ref = $job_array[$val]['ref']; else $internal_ref .= ',' . $job_array[$val]['ref'];
			if ($job_array[$val]['file_no'] > 0) {
				if ($file_nos == "") $file_nos = $job_array[$val]['file_no']; else $file_nos .= ',' . $job_array[$val]['file_no'];
			}
		}
	}

	/*foreach ($po_id as $val) {
		if ($po_number == "") $po_number = $job_array[$val]['po']; else $po_number .= ',' . $job_array[$val]['po'];
		if ($job_number == "") $job_number = $job_array[$val]['job']; else $job_number .= ',' . $job_array[$val]['job'];
		if ($job_style == "") $job_style = $job_array[$val]['style']; else $job_style .= ',' . $job_array[$val]['style'];
		if ($buyer_id == "") $buyer_id = $job_array[$val]['buyer']; else $buyer_id .= ',' . $job_array[$val]['buyer'];
		if ($ship_date == "") $ship_date = change_date_format($job_array[$val]['ship_date']); else $ship_date .= ',' . change_date_format($job_array[$val]['ship_date']);

		if ($internal_ref == "") $internal_ref = $job_array[$val]['ref']; else $internal_ref .= ',' . $job_array[$val]['ref'];
		if ($job_array[$val]['file_no'] > 0) {
			if ($file_nos == "") $file_nos = $job_array[$val]['file_no']; else $file_nos .= ',' . $job_array[$val]['file_no'];
		}
	}*/

	$job_no = implode(",", array_unique(explode(",", $job_number)));
	$jobstyle = implode(",", array_unique(explode(",", $job_style)));
	$buyer = implode(",", array_unique(explode(",", $buyer_id)));
	$internal_ref = implode(",", array_unique(array_filter(explode(",", $internal_ref))));
	$file_nos = implode(",", array_unique(explode(",", $file_nos)));
	//$booking_without_order=return_field_value( "booking_no as booking_no", "wo_non_ord_samp_booking_mst","booking_no=$booking_no","booking_no");

	if ($dataArray[0][csf('booking_without_order')] == 1) {
		$booking_without_order = sql_select("select a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$company and a.booking_no='$booking_no' and a.booking_type=4");
		foreach ($booking_without_order as $row) {
			$color_type_array[$row[csf('booking_no')]][$row[csf('body_part')]]['color_type_id'] = $row[csf('color_type_id')];
			$booking_id = $row[csf('booking_no_prefix_num')];
			$buyer_id_booking = $row[csf('buyer_id')];
		}


	} else {
		$booking_with_order = sql_select("select booking_no_prefix_num, buyer_id from wo_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
		$booking_id = $booking_with_order[0][csf('booking_no_prefix_num')];
		$buyer_id_booking = $booking_with_order[0][csf('buyer_id')];

		$color_sql = "select  b.booking_no,c.color_type_id,c.body_part_id from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company and a.booking_no='$booking_no' and a.booking_type=1 and b.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.booking_no,c.color_type_id,c.body_part_id";
		$color_sql_result = sql_select($color_sql);
		foreach ($color_sql_result as $row) {
			$color_type_array[$row[csf('body_part_id')]][$row[csf('body_part_id')]]['color_type_id'] = $row[csf('color_type_id')];
		}
	}
	$sales_sql = "select b.job_no_mst as booking_no,b.color_id,b.color_type_id,b.body_part_id from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company and a.job_no='$sales_order_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.job_no_mst ,b.color_type_id,b.body_part_id,b.color_id";
	$sales_result = sql_select($sales_sql);
	foreach ($sales_result as $row) {
		$sales_color_type_array[$row[csf('body_part_id')]]['color_type_id'] = $row[csf('color_type_id')];
	}
	$prog_sql = "SELECT a.color_type_id,a.booking_no,b.color_id,b.id as program_id, b.id as program_no,c.body_part_id FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,ppl_planning_entry_plan_dtls c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.booking_no='" . $booking_no . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$prog_result = sql_select($prog_sql);
	foreach ($prog_result as $row) {
		$prog_color_type_array[$row[csf('body_part_id')]]['color_type_id'] = $row[csf('color_type_id')];
	}

//	$program_no_array = return_library_array("SELECT b.id as program_id, b.id as program_no FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b WHERE a.id=b.mst_id and a.booking_no='" . $row[csf('booking_no')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", 'program_id', 'program_no');


	?>
	<div style="width:1060px;">
		<table width="1060" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="6" align="center" style="font-size:22px">
					<strong><? echo $company_library[$working_company]; ?></strong></td>
					<td colspan="2" align="left">Batch Date:<? echo $date = date("F j, Y",strtotime($dataArray[0][csf('batch_date')])); ?></td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:18px"><strong><u>Batch Card</u></strong></td>
					<td colspan="2" id="barcode_img_id" align="right" style="font-size:24px"></td>
				</tr>
				<tr>
					<td colspan="8">&nbsp; </td>
					<td>&nbsp; </td>
				</tr>
				<tr>
					<td colspan="6" align="left" style="font-size:18px"><strong><u>Reference Details</u></strong></td>
					<td style="font-size:24px; border: solid 2px;" align="center" colspan="2">&nbsp;<? echo $dataArray[0][csf('organic')]; ?></td>
				</tr>
				<tr>
					<td width="120"><strong>Buyer</strong></td>
					<td width="135">:&nbsp;<? if ($dataArray[0][csf('batch_against')] == 3) echo $buyer_arr[$buyer_id_booking]; else echo $buyer_arr[$buyer];?></td>
					<td width="120"><strong>Batch No</strong></td>
					<td width="135" style="font-size:20px">:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
					<td width="120"><strong>Dying Machine</strong></td>
					<td width="135">:&nbsp;
						<?
						if ($db_type == 2) {
							$dyeing_machine = return_field_value("(machine_no || '-' || brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
						} else if ($db_type == 0) {
							$dyeing_machine = return_field_value("concat(machine_no,'-',brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
						}
						echo $dyeing_machine;
						?>
					</td>
					<td width="120"><strong>Booking No.</strong></td>
					<td width="135">:&nbsp;<? echo $dataArray[0][csf('booking_no')]; ?></td>
				</tr>
				<tr>
					<td><strong>Job</strong></td>
					<td>:&nbsp;<? echo $job_no; ?></td>
					<td><strong>B. Color</strong></td>
					<td>:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
					<td><strong>B. Weight</strong></td>
					<td>:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><strong>Int. Ref.</strong></td>
					<td>:&nbsp;<? echo $internal_ref; ?></td>
					<td><strong>Batch SL</strong></td>
					<td>:&nbsp;<? echo $batch_sl_no; ?></td>
					<td><strong>Color Ran.</strong></td>
					<td>:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range_id')]]; ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><strong>Style Ref.</strong></td>
					<td>:&nbsp;<? echo $jobstyle; ?></td>
					<td><strong>Remarks</strong></td>
					<td colspan="3">:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
				</tr>
			</table>
			<div style=" font-size:17px;" ><strong><u>Fabrication Details</u></strong></div>
			<table align="left" cellspacing="0" width="670" border="1" rules="all" class="rpt_table"	style="border-top:none">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="100">Body part</th>
						<th width="80">Color Type</th>
						<th width="150">Const. & Comp.</th>
						<th width="100">Fin. GSM</th>
						<th width="100">Fin. Dia</th>
						<th width="100">Grey Qty.</th>
						<th width="">Total Roll</th>
					</tr>
				</thead>
				<?
				if ($db_type == 0) {
					$recv_basis_grop = " group_concat(a.receive_basis) AS receive_basis";
				} else {
					$recv_basis_grop = " LISTAGG(a.receive_basis, ',') WITHIN GROUP (ORDER BY a.receive_basis) AS receive_basis";
				}

				if ($dataArray[0][csf('booking_without_order')] == 1 && $batch_against_id == 3) {
					$receive_basis = return_field_value("$recv_basis_grop", "inv_receive_master a,pro_roll_details b", "a.id=b.mst_id  and (a.booking_id='" . $booking_no_id . "' OR b.barcode_no in($barcode_no)) and a.entry_form in(2,22) and b.entry_form in(2,22)", "receive_basis");
				} else {
					$receive_basis = return_field_value("$recv_basis_grop", "inv_receive_master a,pro_roll_details b", "a.id=b.mst_id and (a.booking_id='" . $booking_no_id . "' OR b.barcode_no in($barcode_no)) and a.entry_form in(2,22) and b.entry_form in(2,22)", "receive_basis");
				}

				$receivebasis = array_unique(explode(",", $receive_basis));
				foreach ($receivebasis as $rcvid) {
					if ($rcvid == 0 || $rcvid == 1 || $rcvid == 11) {
						$machine_info = "d.machine_dia,d.machine_gg,";
					} else {
						$machine_info = "";
					}
				}

				$sql_dtls = "select a.booking_no_id,e.receive_basis,e.booking_id,a.booking_without_order,a.is_sales, b.batch_qnty AS batch_qnty, b.roll_no as roll_no, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, b.width_dia_type as num_of_rows, d.machine_no_id,d.machine_dia,d.machine_gg, $machine_info d.yarn_lot,d.yarn_count,d.brand_id,c.barcode_no, d.stitch_length as stitch_length,  e.knitting_source, e.knitting_company,c.qc_pass_qnty_pcs ,c.coller_cuff_size
				from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d, inv_receive_master e
				where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
				order by b.program_no";

				//echo $sql_dtls;die;
				$sql_result = sql_select($sql_dtls);
				//$coller_cuff_data_arr=array();
				$all_barcode = "";
				foreach ($sql_result as $row)
				{

					$data_array_1[$row[csf('body_part_id')]][$row[csf('item_description')]]["qnty"] += $row[csf('batch_qnty')];
					$data_array_1[$row[csf('body_part_id')]][$row[csf('item_description')]]["receive_basis"]= $row[csf('receive_basis')];
					if($count_num_of_rows[$row[csf('body_part_id')]."*".$row[csf('item_description')]] == "")
					{
						$count_num_of_rows[$row[csf('body_part_id')]."*".$row[csf('item_description')]] = $row[csf('body_part_id')]."*".$row[csf('item_description')];
						$data_array_1[$row[csf('body_part_id')]][$row[csf('item_description')]]["num_of_rows"] = 1;
					}
					else
					{
						$data_array_1[$row[csf('body_part_id')]][$row[csf('item_description')]]["num_of_rows"]++;
					}

					if ($row[csf('knitting_source')] == 1)
					{
						$machin_knit_com = $machine_library[$row[csf('machine_no_id')]];
					}
					else
					{
						$machin_knit_com = $supplier_library[$row[csf('knitting_company')]];
					}

					if($body_part_type[$row[csf('body_part_id')]] == 40 || $body_part_type[$row[csf('body_part_id')]] == 50){
						if($row[csf('coller_cuff_size')] != "" || $row[csf('grey_used_qty')] != "")
						{
							$coller_cuff_data_arr[$body_part_type[$row[csf('body_part_id')]]][$row[csf('coller_cuff_size')]]['collor_cuff_size']=$row[csf('coller_cuff_size')];
							$coller_cuff_data_arr[$body_part_type[$row[csf('body_part_id')]]][$row[csf('coller_cuff_size')]]['qc_pass_qnty_pcs']+=$row[csf('qc_pass_qnty_pcs')];
						}
					}


					$data_array_2[$row[csf('program_no')]]["machine_no"] .= $machin_knit_com.",";
					$data_array_2[$row[csf('program_no')]]["width_dia_type"] .= $fabric_typee[$row[csf('width_dia_type')]].",";
					$data_array_2[$row[csf('program_no')]]["stitch_length"] .= $row[csf('stitch_length')].",";
					$data_array_2[$row[csf('program_no')]]["yarn_lot"] .= $row[csf('yarn_lot')].",";
					//$data_array_2[$row[csf('program_no')]]["brand_id"] .= $brand_name_arr[$row[csf('brand_id')]].",";
					foreach(explode(",",$$row[csf('yarn_lot')]) as $single_lot)
					{
						$data_array_2[$row[csf('program_no')]]["brand_id"] .= $brandSupplier[$single_lot].",";
					}
					$data_array_2[$row[csf('program_no')]]["yarn_count"] .= $row[csf('yarn_count')].",";
					$data_array_2[$row[csf('program_no')]]["machine_dia_guage"] .= $row[csf('machine_dia')]."X".$row[csf('machine_gg')].",";
				}

				foreach ($data_array_1 as $body_part_id => $body_part_data)
				{
					foreach ($body_part_data as $item_description => $row) {

						$receive_basis=$row['receive_basis'];
						//echo $receive_basis.'DD';
						if($receive_basis==2)
						{
							$color_type_id=$color_type[$prog_color_type_array[$body_part_id]['color_type_id']];
						}
						else
						{
							$color_type_id=$color_type[$sales_color_type_array[$body_part_id]['color_type_id']];
						}


						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$desc = explode(",", $item_description);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">

							<td width="100" style="word-break:break-all;"><? echo $body_part[$body_part_id]; ?></td>
							<td width="80" style="word-break:break-all;"><? echo $color_type_id; ?></td>
							<td width="150" style="word-break:break-all;"><? echo $desc[0] . "," . $desc[1]; ?></td>
							<td width="100" align="center" style="word-break:break-all;font-size:20px"><? echo $desc[2]; ?></td>
							<td width="100" align="center" style="word-break:break-all;"><? echo $desc[3]; ?></td>
							<td width="100" align="right" style="word-break:break-all;font-size:20px"><? echo number_format($row['qnty'], 2); ?></td>
							<td style="word-break:break-all;"><? echo $row['num_of_rows']; ?></td>

						</tr>
						<?php
						$total_roll_number += $row['num_of_rows'];
						$total_batch_qty += $row['qnty'];
						$i++;
					}
				}
				?>
				<tr>
					<td style="border:none;" colspan="5" align="right"><b>Sum:</b></td>
					<td align="right"><b><? echo number_format($total_batch_qty, 2); ?> </b></td>
					<td align="center"><b><? echo $total_roll_number; ?> </b></td>
				</tr>
				<tr>
					<td style="border:none;" colspan="5" align="right"><b>Trims Weight:</b></td>
					<td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')], 2); ?> </b>
					</td>
					<td style="border:none;">&nbsp;</td>
				</tr>
				<tr>
					<td style="border:none;" colspan="5" align="right"><b>Total:</b></td>
					<td align="right">
						<b><? echo number_format($total_batch_qty + $dataArray[0][csf('total_trims_weight')], 2); ?> </b>
					</td>
					<td style="border:none;">&nbsp;</td>
				</tr>
			</table>
			<br> <br>
			<table align="left" cellspacing="0" width="960" border="1" rules="all" class="rpt_table"	style="margin-top:5px;">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="40">SL</th>
						<th width="100">Prog. No</th>
						<th width="100">Machine / Knitting Com</th>
						<th width="100">M/C Dia / M/C Gauge</th>
						<th width="100">D/W Type</th>
						<th width="100">S. Length</th>
						<th width="100">Yarn Lot</th>
						<th width="100">Brand</th>
						<th width="100">Yarn Count</th>
						<th>ID Code</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;

					foreach ($data_array_2 as $program_no => $val)
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						$machin_knit_com = implode(",", array_filter(array_unique(explode(",", chop($val['machine_no'])))));
						$width_dia_type = implode(",", array_filter(array_unique(explode(",", chop($val['width_dia_type'])))));
						$stitch = implode(",", array_filter(array_unique(explode(",", chop($val['stitch_length'])))));
						$yarn_lot = implode(",", array_filter(array_unique(explode(",", chop($val['yarn_lot'])))));
						$brand_value = implode(",", array_filter(array_unique(explode(",", chop($val['brand_id'])))));
					//$yarn_count_value = implode(",", array_filter(array_unique(explode(",", chop($val['yarn_count'])))));
						$yarn_count_val_arr = array_filter(array_unique(explode(",", chop($val['yarn_count']))));
						$machine_dia_guage_val = implode(",",array_filter(array_unique(explode(",", chop($val['machine_dia_guage'])))));
						$yarn_count_value="";

						?>
						<tr>
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="60" align="center" style="word-break:break-all;"><? echo $program_no; ?></td>
							<td width="80" style="word-break:break-all;" align="center"><? echo $machin_knit_com; ?></td>
							<td width="80" style="word-break:break-all;" align="center"><? echo $machine_dia_guage_val; ?></td>
							<td width="70" style="word-break:break-all;"><? echo $width_dia_type; ?></td>
							<td width="60" align="center" style="word-break:break-all;"><? echo $stitch; ?></td>
							<td width="80" style="word-break:break-all;"><? echo $yarn_lot; ?></td>
							<td width="80" style="word-break:break-all;"><?	echo $brand_value;	?></td>
							<td width="80" style="word-break:break-all;" align="center">
								<?
								foreach ($yarn_count_val_arr as $y_c)
								{
									$yarn_count_value .= $yarncount[$y_c].",";
								}
								echo chop($yarn_count_value,",");
								?>
							</td>
							<td>&nbsp;</td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
			</table>

			<br/> <br/> <br/> <br/>

			<table width="1060" cellspacing="0" align="center" style="margin-top:40px; padding-top:20px;">
				<tr>
					<?php if($dataArray[0][csf('SAVE_STRING')]!="")
					{
						?>
						<td valign="top" align="left" width="520">
							<table cellspacing="0" border="1" rules="all" class="rpt_table" width="530">
								<thead>
									<tr>
										<th colspan="4"><strong>Trims Details</strong></th>
									</tr>
									<tr>
										<th  width="30">SL</th>
										<th width="150">Item Description</th>
										<th	width="70">Weight In Kg</th>
										<th>Remarks</th>
									</tr>
								</thead>
								<?
								$trims_details_row=explode(",",$dataArray[0][csf('SAVE_STRING')]);
								foreach($trims_details_row as $single_row){
									$single_row_arr=explode("_",$single_row);
									$sl++;
									?>
									<tr>
										<td style="width:30px; text-align:center;"><? echo $sl; ?></td>
										<td ><? echo $single_row_arr[0]; ?></td>
										<td ><? echo $single_row_arr[1]; ?></td>
										<td ><? echo $single_row_arr[2]; ?></td>
									</tr>
									<?
								}
								?>
							</table>
						</td>

						<?php

					}
					?>

					<?php
					if(count($coller_cuff_data_arr)>0)
					{

						ksort($coller_cuff_data_arr);
						foreach($coller_cuff_data_arr as $key=>$coller_cuff_data)
						{
							if($key==40) $coller_cuff_body_part="Coller"; else $coller_cuff_body_part="Cuff";
							?>
							<td width="200" valign="top" align="left">
								<table cellspacing="0" border="1" rules="all" class="rpt_table" width="200">
									<tr>
										<th colspan="2"><strong><?php  echo $coller_cuff_body_part; ?> </strong></th>
									</tr>
									<tr>
										<th  width="100"><strong>Size </strong></th>
										<th width="100"><strong>Pcs </strong></th>
									</tr>
									<?php

									foreach($coller_cuff_data as $barcode=>$coller_cuff_single)
									{

										?>
										<tr>
											<td  width="100"><?php echo  $coller_cuff_single['collor_cuff_size'];?></td>
											<td width="100"><?php echo $coller_cuff_single['qc_pass_qnty_pcs'];?></td>
										</tr>

										<?php
									}

									?>
								</table>
							</td>
							<?php
						}

					}
					?>
				</tr>
			</table>

			<br><br>
			<table align="left" cellspacing="0" width="1060" border="1" rules="all" class="rpt_table"	style="margin-top:5px;">
				<tr>
					<td colspan="9" align="right">
						<?
						$process = $dataArray[0][csf('process_id')];
						$process_id = explode(',', $process);
					//print_r($process_id);
						$process_value = '';
						$i = 1;
						foreach ($process_id as $val) {
							if ($process_value == '') $process_value = $i . '. ' . $conversion_cost_head_array[$val]; else $process_value .= ", " . $i . '. ' . $conversion_cost_head_array[$val];
							$i++;
						}
						?>
						<table align="left" rules="all" class="rpt_table" width="1060">
							<tr>
								<th align="left" style="font-size:20px;"><strong>Process Required</strong></th>
							</tr>
							<tr>
								<td style="font-size:20px;" title="<? echo $process_value; ?>">
									<p><? echo $process_value; ?></p>
								</td>
							</tr>
							<tr>
								<td align="left" style="font-size:19px;">
									Heat Setting:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp; Loading Date & Time: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp; UnLoading Date & Time:&nbsp;
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<div style="float:left; margin-left:10px;"><strong> Quality Instruction(Hand Written)</strong></div>
			<table width="1060" cellspacing="0" align="center">
				<tr>
					<td valign="top" align="left" width="520">
						<table cellspacing="0" width="515" align="left" border="1" rules="all" class="rpt_table">
							<tr>
								<th>SL</th>
								<th>Roll No</th>
								<th>Roll Mark</th>
								<th>Actual Dia</th>
								<th>Roll Wgt.</th>
								<th>Remarks</th>
							</tr>
							<tr height="30">
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
							</tr>
							<tr height="30">
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
							</tr>
							<tr height="30">
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
							</tr>
							<tr height="30">
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
							</tr>
							<tr height="30">
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
							</tr>
							<tr height="30">
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
							</tr>
						</table>
					</td>
					<td width="10" align="justify" valign="top"></td>
					<td width="520" valign="top" align="right">
						<table width="515" cellspacing="0" border="1" rules="all" class="rpt_table">
							<tr>
								<th>SL</th>
								<th>Roll No</th>
								<th>Actual Dia</th>
								<th>Roll Wgt.</th>
								<th>Remarks</th>
							</tr>
							<tr height="30">
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
							</tr>
							<tr height="30">
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
							</tr>
							<tr height="30">
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
							</tr>
							<tr height="30">
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
							</tr>
						</table>
					</td>
				</tr>
				<tr style="display:none">
					<td width="520" valign="top">
						<table width="515" cellspacing="0" border="1" rules="all" class="rpt_table" >
							<tr>
								<th align="left"><strong>Shade Result(<i>Hand Written</i>)</strong></th>
							</tr>
							<tr>
								<td colspan="1" style="width:475px; height:80px">&nbsp;</td>
							</tr>
						</table>
					</td>
					<td width="10" align="justify" valign="top">&nbsp;</td>
					<td width="520" valign="top" align="right">
						<table cellspacing="0" border="1" rules="all" class="rpt_table" width="515">
							<tr>
								<th align="left" colspan="3"><strong>Shrinkage(<i>Hand Written</i>)</strong></th>
							</tr>
							<tr>
								<th><b>Length % </b></th>
								<th><b>Width % </b></th>
								<th><b> Twist % </b></th>
							</tr>
							<tr height="30">
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
							</tr>
							<tr height="30">
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
							</tr>
							<tr height="30">
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
							</tr>
							<tr height="30">
								<td>&nbsp; </td>
								<td>&nbsp; </td>
								<td>&nbsp; </td>
							</tr>
						</table>
					</td>
				</tr>
				<tr style="display:none">
					<td width="1060" colspan="3">
						<table cellspacing="0" border="1" rules="all" class="rpt_table" width="1060">
							<tr>
								<th colspan="2" align="center"><strong>Dyeing Finishing Information(<i>Hand
								Written</i>)</strong></th>
							</tr>
							<tr>
								<th> Dyeing</th>
								<th> Finishing</th>
							</tr>
							<tr>
								<td style="width:530px; height:120px">&nbsp;</td>
								<td style="width:530px; height:120px">&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<?
				$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no='".str_replace("'","",$batch_sl_no)."' and entry_form > 0 order by id");
				if(count($data_array)>0)
				{
					?>
					<tr>
						<td width="1060" colspan="3">
							<table cellspacing="0" border="1" rules="all" class="rpt_table" width="1060">
								<tr>
									<th>SL</th>
									<th><strong>Terms & Condition/Notes</strong></th>
								</tr>
								<?
								$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no='".str_replace("'","",$batch_sl_no)."' and entry_form > 0 order by id");
								foreach($data_array as $row){
									$k++;
									?>
									<tr>
										<td style="width:30px; text-align:center;"><? echo $k; ?></td>
										<td style=""><? echo $row[csf('terms')] ?></td>
									</tr>
									<?
								}
								?>
							</table>
						</td>
					</tr>

					<?php
				}

				?>

			</table>
			<br>
			<?
			echo signature_table(52, $company, "1060px");
			?>
		</div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 30,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $dataArray[0][csf('batch_no')]; ?>');
    </script>
    <?
    exit();
}



if ($action == "trims_weight_popup") {
	echo load_html_head_contents("Trims Weight Entry", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	//echo $save_data;

	?>
	<script>
		var permission = '<? echo $permission; ?>';
		function fn_addRow_trims(i) {
            //var row_num=$('#tbl_item_details tbody tr').length;
            //var lastTrId = $('#tbl_list tbody tr:last').attr('id').split('_');
            //alert(lastTrId);
            //var row_num=lastTrId[1];
            var row_num = $('#tbl_list tbody tr').length;
            //alert(lastTrId[1]);
            if (row_num != i) {
            	return false;
            }
            else {
            	i++;

            	$("#tbl_list tbody tr:last").clone().find("input,select").each(function () {

            		$(this).attr({
            			'id': function (_, id) {
            				var id = id.split("_");
            				return id[0] + "_" + i
            			},
            			'name': function (_, name) {
            				return name
            			},
            			'value': function (_, value) {
            				return value
            			}
            		});

            	}).end().appendTo("#tbl_list");

            	$('#slTd_' + i).val('');
            	$('#txtitemDesc_' + i).val('');
            	$('#trimsWeight_' + i).val('');
            	$('#remarks_' + i).val('');
            	$("#tbl_list tbody tr:last").removeAttr('id').attr('id', 'tr_' + i);
            	$('#tr_' + i).find("td:eq(0)").removeAttr('id').attr('id', 'slTd_' + i);
            	$('#tr_' + i).find("td:eq(0)").text(i);

            	$('#increase_' + i).removeAttr("value").attr("value", "+");
            	$('#decrease_' + i).removeAttr("value").attr("value", "-");
            	$('#increase_' + i).removeAttr("onclick").attr("onclick", "fn_addRow_trims(" + i + ");");
            	$('#decrease_' + i).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + i + ");");
            }
            set_all_onclick();
        }

        function fn_deleteRow(rowNo) {

        	var row_num = $('#tbl_list tbody tr').length;

        	if (row_num != 1) {
                //alert(row_num);
                $("#tr_" + rowNo).remove();
            }
        }

        function window_close() {
        	var save_data = '';
        	var tot_trims_qnty = '';

        	$("#tbl_list").find('tr').each(function () {
        		var txtitemDesc = $(this).find('input[name="txtitemDesc[]"]').val();
        		var trimsWeight = $(this).find('input[name="trimsWeight[]"]').val();
        		var remarks = $(this).find('input[name="remarks[]"]').val();
        		if (trimsWeight * 1 > 0) {

        			if (save_data == "") {
        				save_data = txtitemDesc + "_" + trimsWeight + "_" + remarks;
        			}
        			else {
        				save_data += "!!" + txtitemDesc + "_" + trimsWeight + "_" + remarks;
        			}
        			tot_trims_qnty = tot_trims_qnty * 1 + trimsWeight * 1;

        		}

        	});
            //alert(tot_trims_qnty);
            $('#save_data').val(save_data);
            $('#tot_trims_qnty').val(tot_trims_qnty);
            parent.emailwindow.hide();
        }

        function calculate_trims_qnty() {
        	var total_trims_qnty = '';
        	$("#tbl_list tbody").find('tr').each(function () {
        		var trimsQnty = $(this).find('input[name="trimsWeight[]"]').val();
        		total_trims_qnty = total_trims_qnty * 1 + trimsQnty * 1;
        	});

        	$('#txt_total_trims_qnty').val(total_trims_qnty.toFixed(2));

        }

    </script>

</head>

<body>
	<div align="center">
		<? echo load_freeze_divs("../../", $permission, 1); ?>
		<form name="trimsWeight_1" id="trimsWeight_1">

			<fieldset style="width:600px; margin-top:10px">
				<legend>Batch Trims Pop Up</legend>
				<?
				if ($save_data != "") {
					?>
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600"
					id="tbl_list">
					<thead>
						<th width="40">SL</th>
						<th width="200">Item Description</th>
						<th width="80">Weight In Kg</th>
						<th width="150">Remarks</th>
						<th></th>
						<input type="hidden" name="save_data" id="save_data" class="text_boxes">
						<input type="hidden" name="tot_trims_qnty" id="tot_trims_qnty" class="text_boxes">
					</thead>
					<tbody>

						<?
						$tot_trims_wgt = 0;
						$k = 0;
						$explSaveData = explode("!!", $save_data);
						for ($z = 0; $z < count($explSaveData); $z++) {
							$data_all = explode("_", $explSaveData[$z]);
							$item_des = $data_all[0];
							$trims_wgt = $data_all[1];
							$remark = $data_all[2];
							$tot_trims_wgt += $trims_wgt;
							$k++;

							?>
							<tr id="tr_<? echo $k; ?>">
								<td id="slTd_<? echo $k; ?>" width="30"><? echo $k; ?></td>
								<td>
									<input type="text" name="txtitemDesc[]" id="txtitemDesc_<? echo $k; ?>"
									class="text_boxes" style="width:200px;" value="<? echo $item_des; ?>"/>
								</td>
								<td>
									<input type="text" name="trimsWeight[]" id="trimsWeight_<? echo $k; ?>"
									class="text_boxes_numeric" style="width:80px;"
									onKeyUp="calculate_trims_qnty();" value="<? echo $trims_wgt; ?>"/>
								</td>
								<td>
									<input type="text" name="remarks[]" id="remarks_<? echo $k; ?>"
									class="text_boxes"
									style="width:150px;" value="<? echo $remark; ?>"/>
								</td>

								<td>
									<input type="button" id="increase_<? echo $k; ?>" name="increase[]"
									style="width:30px" class="formbuttonplasminus" value="+"
									onClick="fn_addRow_trims(<? echo $k; ?>)"/>
									<input type="button" id="decrease_<? echo $k; ?>" name="decrease[]"
									style="width:30px" class="formbuttonplasminus" value="-"
									onClick="fn_deleteRow(<? echo $k; ?>);"/>
								</td>
							</tr>
							<?
						}
						?>

					</tbody>
					<tfoot class="tbl_bottom">
						<td>&nbsp;</td>

						<td>Sum</td>
						<td><input type="text" name="txt_total_trims_qnty" id="txt_total_trims_qnty"
							class="text_boxes_numeric" style="width:80px" readonly
							value="<? echo $tot_trims_wgt; ?>"/></td>

							<td colspan="2">&nbsp;</td>
						</tfoot>
					</table>
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600">

						<tr>
							<td colspan="5" align="center">

								<input type="button" name="close" class="formbutton" value="Close" id="main_close"
								onClick="window_close();" style="width:80px"/>

							</td>
						</tr>
					</table>

					<?
				} else { ?>
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600"
					id="tbl_list">
					<thead>
						<th width="40">SL</th>
						<th width="200">Item Description</th>
						<th width="80">Weight In Kg</th>
						<th width="150">Remarks</th>
						<th></th>
						<input type="hidden" name="save_data" id="save_data" class="text_boxes">
						<input type="hidden" name="tot_trims_qnty" id="tot_trims_qnty" class="text_boxes">
					</thead>
					<tbody>
						<tr id="tr_1">
							<td id="slTd_1" width="30">1</td>
							<td>
								<input type="text" name="txtitemDesc[]" id="txtitemDesc_1" class="text_boxes"
								style="width:200px;"/>
							</td>
							<td>
								<input type="text" name="trimsWeight[]" id="trimsWeight_1"
								class="text_boxes_numeric"
								style="width:80px;" onKeyUp="calculate_trims_qnty();"/>
							</td>
							<td>
								<input type="text" name="remarks[]" id="remarks_1" class="text_boxes"
								style="width:150px;"/>
							</td>

							<td>
								<input type="button" id="increase_1" name="increase[]" style="width:30px"
								class="formbuttonplasminus" value="+" onClick="fn_addRow_trims(1)"/>
								<input type="button" id="decrease_1" name="decrease[]" style="width:30px"
								class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
							</td>
						</tr>

					</tbody>
					<tfoot class="tbl_bottom">
						<td>&nbsp;</td>

						<td>Sum</td>
						<td><input type="text" name="txt_total_trims_qnty" id="txt_total_trims_qnty"
							class="text_boxes_numeric" style="width:80px" readonly/></td>

							<td colspan="2">&nbsp;</td>
						</tfoot>
					</table>
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600">

						<tr>
							<td colspan="5" align="center">

								<input type="button" name="close" class="formbutton" value="Close" id="main_close"
								onClick="window_close();" style="width:80px"/>

							</td>
						</tr>
					</table>
				<? }
				?>
			</fieldset>
		</form>
	</div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if ($action == "batch_card_print_6") {
	//echo load_html_head_contents("Batch Info","../../", 1, 1, '','','');
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$batch_update_id = $data[1];
	$working_company = $data[7];
	$batch_mst_update_id = str_pad($batch_update_id, 10, '0', STR_PAD_LEFT);
	//echo $batch_mst_update_id;die;
	$batch_sl_no = $data[2];
	$booking_no_id = $data[6];
	$roll_maintained = $data[8];
	if($roll_maintained==0)
	{
		echo "<p width='250'><b>Sorry, This Print Report is in roll level</b></p>";
		die;
	}
	//echo $data[3].$data[4];die;
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$machine_library = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');

	$cons_comp_sql = sql_select("select a.id, a.construction,c.composition_name, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b left join lib_composition_array c on b.copmposition_id = c.id and c.status_active=1 where a.id = b.mst_id and a.status_active=1 and b.status_active=1");
	foreach ($cons_comp_sql as  $val)
	{
		$cons_comp_arr[$val[csf("id")]]["const"] = $val[csf("construction")];
		$cons_comp_arr[$val[csf("id")]]["compo"] .= $val[csf("composition_name")] ." ". $val[csf("percent")] . "% ";

	}

	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no,a.batch_date, a.booking_no_id, a.booking_no,a.booking_without_order,a.sales_order_no,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, group_concat(b.barcode_no) as barcode_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.batch_no,a.batch_date, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id, a.sales_order_no,a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty, a.cuff_qty, a.SAVE_STRING";
	} else {
		$sql = "select a.id, a.batch_no,a.batch_date, a.booking_no_id,a.booking_no,a.booking_without_order,a.sales_order_no, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id,LISTAGG(CAST(b.barcode_no AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.barcode_no) AS barcode_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.batch_no,a.batch_date, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for,a.sales_order_no, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty,a.cuff_qty, a.SAVE_STRING";
	}
	//echo $sql;
	$dataArray = sql_select($sql);

	$po_number = "";
	$job_number = "";
	$job_style = "";
	$buyer_id = "";
	$ship_date = "";
	$internal_ref = "";
	$dealing_marchant ="";
	$file_nos = "";
	$po_id = array_filter(array_unique(explode(",", $dataArray[0][csf('po_id')])));
	$po_ids = implode(",", $po_id);

	$job_array = array();
	$job_sql = "select distinct(a.buyer_name) as buyer_name,a.style_ref_no, a.job_no_prefix_num, a.job_no, b.pub_shipment_date, b.id, b.po_number, b.grouping, b.file_no, c.team_member_name from wo_po_details_master a left join lib_mkt_team_member_info c on a.dealing_marchant = c.id, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in ($po_ids)";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
		$job_array[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['dealing_marchant'] = $row[csf('team_member_name')];
	}

	$barcode_no = implode(",",array_unique(explode(",", $dataArray[0][csf('barcode_no')])));
	$booking_no = $dataArray[0][csf('booking_no')];
	$batch_against_id = $dataArray[0][csf('batch_against')];
	$batch_booking_id = $dataArray[0][csf('booking_no_id')];
	$batch_product_id = $dataArray[0][csf('prod_id')];
	$sales_order_no = $dataArray[0][csf('sales_order_no')];
	$batch_booking_without = $dataArray[0][csf('booking_without_order')];
	foreach ($po_id as $val) {
		if ($po_number == "") $po_number = $job_array[$val]['po']; else $po_number .= ',' . $job_array[$val]['po'];
		if ($job_number == "") $job_number = $job_array[$val]['job']; else $job_number .= ',' . $job_array[$val]['job'];
		if ($job_style == "") $job_style = $job_array[$val]['style']; else $job_style .= '*' . $job_array[$val]['style'];
		if ($buyer_id == "") $buyer_id = $job_array[$val]['buyer']; else $buyer_id .= ',' . $job_array[$val]['buyer'];
		if ($ship_date == "") $ship_date = change_date_format($job_array[$val]['ship_date']); else $ship_date .= ',' . change_date_format($job_array[$val]['ship_date']);

		if ($internal_ref == "") $internal_ref = $job_array[$val]['ref']; else $internal_ref .= ','.$job_array[$val]['ref'];
		if ($job_array[$val]['file_no'] > 0) {
			if ($file_nos == "") $file_nos = $job_array[$val]['file_no']; else $file_nos .= ','.$job_array[$val]['file_no'];
		}
		if ($dealing_marchant == "") $dealing_marchant = $job_array[$val]['dealing_marchant']; else $dealing_marchant .= ',' . $job_array[$val]['dealing_marchant'];
	}

	$job_no = implode(",", array_unique(explode(",", $job_number)));
	$jobstyle = implode(",", array_unique(explode("*", $job_style)));
	$buyer = implode(",", array_unique(explode(",", $buyer_id)));
	$internal_ref = implode(",", array_unique(array_filter(explode(",", $internal_ref))));
	$file_nos = implode(",", array_unique(explode(",", $file_nos)));
	$po_number = implode(",", array_unique(explode(",", $po_number)));
	$dealing_marchant = implode(",", array_unique(explode(",", $dealing_marchant)));

	if ($dataArray[0][csf('booking_without_order')] == 1)
	{
		$booking_without_order = sql_select("select a.booking_no_prefix_num, a.buyer_id, b.team_member_name from wo_non_ord_samp_booking_mst a left join lib_mkt_team_member_info b on a.dealing_marchant = b.id where  a.company_id=$company and a.booking_no='$booking_no' and a.booking_type=4");

		foreach ($booking_without_order as $row) {
			$booking_id = $row[csf('booking_no_prefix_num')];
			$buyer_id_booking = $row[csf('buyer_id')];
			$dealing_marchant = $row[csf('team_member_name')];
		}
	} else {
		$booking_with_order = sql_select("select booking_no_prefix_num, buyer_id from wo_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
		$booking_id = $booking_with_order[0][csf('booking_no_prefix_num')];
		$buyer_id_booking = $booking_with_order[0][csf('buyer_id')];
	}

	$lab_dip_no_sql=sql_select("select b.id, b.lapdip_no from wo_booking_dtls a, wo_po_lapdip_approval_info b where a.job_no = b.job_no_mst and b.job_no_mst = '$job_no' and a.booking_no ='$booking_no' and a.fabric_color_id = b.color_name_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.approval_status =3 and b.color_name_id = ". $dataArray[0][csf('color_id')]." group by b.id, b.lapdip_no");

	foreach($lab_dip_no_sql as $row)
	{
		$lab_dip_no_arr[$row[csf("lapdip_no")]] = $row[csf("lapdip_no")];
	}

	?>
	<div style="width:1200px;">
		<table width="1200" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="8" align="center" style="font-size:22px">
					<strong><? echo $company_library[$working_company]; ?></strong></td>
					<td colspan="2" align="left" style="font-size: 11px;">Print Time:<? echo $date = date("F j, Y, g:i a"); ?></td>
				</tr>
				<tr>
					<td colspan="8" align="center" style="font-size:18px"><strong><u>Batch Card</u></strong></td>
					<td colspan="2" id="barcode_img_id" align="right" style="font-size:24px"></td>
				</tr>
				<tr>
					<td colspan="8">&nbsp; </td>
					<td>&nbsp; </td>
				</tr>
				<tr>
					<td colspan="8" align="left" style="font-size:18px"><strong><u>Reference Details</u></strong>
					</td>
					<td style="font-size:24px; border: solid 2px;" align="center" colspan="2">
						&nbsp;<? echo $dataArray[0][csf('organic')]; ?></td>
					</tr>
					<tr>
						<td width="100"><strong>Buyer</strong></td>
						<td width="150">:&nbsp;<? if ($dataArray[0][csf('batch_against')] == 3) echo $buyer_arr[$buyer_id_booking]; else echo $buyer_arr[$buyer]; ?></td>
						<td width="100"><strong>Booking no</strong></td>
						<td width="170">:&nbsp;<? echo $dataArray[0][csf('booking_no')]; ?></td>
						<td width="100"><strong>Batch No</strong></td>
						<td width="150">:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
						<td width="90"><strong>Ext. No.</strong></td>
						<td width="120">:&nbsp;<? echo $dataArray[0][csf('extention_no')]?></td>

						<td width="80"><strong>Lab Dip</strong></td>
						<td width="">:&nbsp;<? echo implode(",", $lab_dip_no_arr) ?></td>
					</tr>
					<tr>
						<td><strong>Job</strong></td>
						<td>:&nbsp;<? echo $job_no; ?></td>
						<td><strong>Style Ref.</strong></td>
						<td>:&nbsp;<? echo $jobstyle; ?></td>
						<td><strong>B. Color</strong></td>
						<td>:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
						<td><strong>B. Weight</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?></td>
						<td><strong>B. Against</strong></td>
						<td>:&nbsp;<? echo $batch_against[$batch_against_id];?></td>
					</tr>
					<tr>
						<td><strong>Order No</strong></td>
						<td>:&nbsp;<? echo $po_number; ?></td>
						<td><strong>Batch SL</strong></td>
						<td>:&nbsp;<? echo $batch_sl_no; ?></td>
						<td><strong>Color Ran.</strong></td>
						<td>:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range_id')]]; ?></td>
						<td><strong>Machine</strong></td>
						<td colspan="3">:&nbsp;
							<?
							if ($db_type == 2) {
								$dyeing_machine = return_field_value("(machine_no || '-' || brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
							} else if ($db_type == 0) {
								$dyeing_machine = return_field_value("concat(machine_no,'-',brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
							}
							echo $dyeing_machine;
							?>
						</td>

					</tr>
					<tr>
						<td><strong>Remarks</strong></td>
						<td colspan="5">:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
						<td><strong>Dealing Marchant</strong></td>
						<td colspan="3">:&nbsp;<? echo $dealing_marchant; ?></td>
					</tr>
				</table>
				<div style="float:left; font-size:17px;"><strong><u>Fabrication Details</u></strong></div>
				<table align="center" cellspacing="0" width="1200" border="1" rules="all" class="rpt_table" style="border-top:none">
					<thead bgcolor="#dddddd" align="center">
						<tr>
							<th width="30">SL</th>
							<th width="100">Body part</th>
							<th width="200">Const.& Comp.</th>
							<th width="70">Grey Dia</th>
							<th width="70">Gauge</th>
							<th width="70">S. Length</th>
							<th width="70">Yarn Brand</th>
							<th width="80">Yarn Lot</th>
							<th width="100">Yarn Count</th>
							<th width="70">Fin. Dia</th>
							<th width="70">Fin. GSM</th>
							<th width="50">Total Roll</th>
							<th width="100">D/W Type</th>
							<th width="">Grey Qty.</th>

						</tr>
					</thead>
					<?
					$i = 1;
					$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
					$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');
					$brand_sql = sql_select("select brand,id,lot from  product_details_master where item_category_id=1 and brand <> 0");
					foreach ($brand_sql as  $val)
					{
						$brand_from_prod[$val[csf("id")]] = $brand_name_arr[$val[csf("brand")]];
						$brand_from_lot[$val[csf("lot")]] .= $brand_name_arr[$val[csf("brand")]].",";
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

					if ($db_type == 0) {
						$sql_dtls = "select a.booking_no_id,e.receive_basis,e.booking_id, a.booking_without_order,a.is_sales, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no,d.febric_description_id,d.gsm, d.width, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows,$machine_info group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count) as yarn_count, d.stitch_length as stitch_length, group_concat(d.brand_id) as brand_id,  group_concat(c.barcode_no) as barcode_no, group_concat(d.yarn_prod_id) as yarn_prod_id
						from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
						where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
						group by a.booking_no_id,e.booking_id,$machine_info a.booking_without_order,a.is_sales,e.receive_basis,b.prod_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.stitch_length,d.febric_description_id,d.gsm, d.width order by b.program_no";

					} else {
						$sql_dtls = "select a.booking_no_id,e.receive_basis,e.booking_id,a.booking_without_order,a.is_sales, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no,d.febric_description_id,d.gsm, d.width, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows, $machine_info LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count, d.stitch_length as stitch_length, LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id, LISTAGG(CAST(c.barcode_no AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY c.barcode_no) as barcode_no,listagg(cast(d.yarn_prod_id as varchar2(4000)), ',') within group (order by d.yarn_prod_id) as yarn_prod_id
						from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
						where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
						group by a.booking_no_id,e.receive_basis,$machine_info e.booking_id,a.booking_without_order,a.is_sales,b.prod_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.stitch_length,d.febric_description_id,d.gsm, d.width order by b.program_no";
					}

					$sql_result = sql_select($sql_dtls);
					foreach ($sql_result as $row)
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$desc = explode(",", $row[csf('item_description')]);

						$yarn_lot = implode(",", array_filter(array_unique(explode(",", $row[csf('yarn_lot')]))));
						$y_count = array_unique(explode(",", $row[csf('yarn_count')]));
						$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
						$yarn_prod_id = array_filter(array_unique(explode(",", $row[csf('yarn_prod_id')])));
						$yarn_count_value = "";
						foreach ($y_count as $val) {
							if ($val > 0) {
								if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
							}
						}
						$brand_value = "";
						foreach ($brand_id as $bid) {
							if ($bid > 0) {
								if ($brand_value == '') $brand_value = $brand_name_arr[$bid]; else $brand_value .= ", " . $brand_name_arr[$bid];
							}
						}

						$yarn_brand_value = "";
						foreach ($yarn_prod_id as $yid) {
							if ($yid != "") {
								if ($yarn_brand_value == '') $yarn_brand_value = $brand_from_prod[$yid]; else $yarn_brand_value .= "," . $brand_from_prod[$yid];
							}
						}

						if($yarn_brand_value =="")
						{
							foreach(explode(",",$yarn_lot) as $lot_no)
							{
								if ($yarn_brand_value == "")
								{
									$yarn_brand_value = chop($brand_from_lot[$lot_no],",");
								}
								else
								{
									$yarn_brand_value .= ",". $brand_from_lot[$lot_no];
								}
							}
						}

						if ($row[csf('receive_basis')] == 0 || $row[csf('receive_basis')] == 1 || $row[csf('receive_basis')] == 11) //from Entry page
						{
							$machine_dia_width = $row[csf('machine_dia')];
							$machine_gauge = $row[csf('machine_gg')];
						} else if ($row[csf('receive_basis')] == 2) //Knitting Plan
						{
							$program_data = sql_select("select width_dia_type, machine_dia, machine_gg, machine_id from ppl_planning_info_entry_dtls where id='" . $row[csf('booking_id')] . "'");

							$machine_dia_width = $program_data[0][csf('machine_dia')];
							$machine_gauge = $program_data[0][csf('machine_gg')];
						}

						$stitch = implode(",", array_unique(explode(",", $row[csf('stitch_length')])));
						$yarn_brand_value = implode(",", array_unique(explode(",", chop($yarn_brand_value))));

						$dya_gage = $machine_dia_width . "<br>" . $machine_gauge;

						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="30" align="center"><? echo $i; ?></td>

							<td width="100" style="word-break:break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>

							<td width="200" style="word-break:break-all;">
								<?
								//echo $desc[0] . "," . $desc[1];
								echo $cons_comp_arr[$row[csf("febric_description_id")]]["const"] . "," . $cons_comp_arr[$row[csf("febric_description_id")]]["compo"];
								?>
							</td>
							<td width="70" align="center" style="word-break:break-all;"><? echo $machine_dia_width; ?></td>
							<td width="70" align="center" style="word-break:break-all;"><? echo $machine_gauge; ?></td>
							<td width="70" align="center" style="word-break:break-all;"><? echo $stitch; ?></td>
							<td width="70" align="center" style="word-break:break-all;"><? echo $yarn_brand_value; ?></td>
							<td width="80" style="word-break:break-all;"><? echo implode(',', array_unique(explode(",", $yarn_lot))); ?></td>
							<td width="100" style="word-break:break-all;" align="center"><? echo $yarn_count_value; ?></td>
							<td width="70" align="center" style="word-break:break-all;"><? echo $row[csf('width')];//$desc[3]; ?></td>
							<td width="70" align="center" style="word-break:break-all;"><? echo $row[csf('gsm')];//$desc[2]; ?></td>
							<td align="center" width="50" style="word-break:break-all;"><? echo $row[csf('num_of_rows')]; ?></td>
							<td width="100" style="word-break:break-all;"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
							<td width="" align="right" style="word-break:break-all;"><? echo number_format($row[csf('batch_qnty')], 2); ?></td>
						</tr>
						<?php
						$total_roll_number += $row[csf('num_of_rows')];
						$total_batch_qty += $row[csf('batch_qnty')];
						$i++;
					}
					$all_barcode = implode(",", array_unique(explode(",", chop($all_barcode, ","))));
					?>
					<tr>
						<td style="border:none; word-break:break-all;" colspan="11" align="right" valign="top"><b>Total</b></td>
						<td align="center"><b><? echo $total_roll_number; ?> </b></td>
						<td align="right"><b>Total:</b></td>
						<td align="right"><b><? echo number_format($total_batch_qty, 2); ?> </b></td>
					</tr>
					<tr>
						<td style="border:none; word-break:break-all;" colspan="11" align="left" valign="top"><b>Process Required</b></td>
						<td align="right" colspan="2"><b>Trims Weight:</b></td>
						<td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')], 2); ?> </b></td>
					</tr>
					<tr>
						<td style="border:none; word-break:break-all;" colspan="11" align="left" valign="top">
							<?
							$process = $dataArray[0][csf('process_id')];
							$process_id = explode(',', $process);
					//print_r($process_id);
							$process_value = '';
							$i = 1;
							foreach ($process_id as $val) {
								if ($process_value == '') $process_value = $i . '. ' . $conversion_cost_head_array[$val]; else $process_value .= ", " . $i . '. ' . $conversion_cost_head_array[$val];
								$i++;
								$process_id_arr[$val] = $val;
							}
							?>
							<p><? echo $process_value; ?></p>
						</td>
						<td align="right" colspan="2"><b>Total:</b></td>
						<td align="right">
							<b><? echo number_format($total_batch_qty + $dataArray[0][csf('total_trims_weight')], 2); ?> </b>
						</td>
					</tr>

					<tr>
						<td colspan="13">
							<?
							$save_str = $dataArray[0][csf('SAVE_STRING')];
							$save_ref = explode(",", $save_str);
							if (!empty($dataArray[0][csf('SAVE_STRING')]) > 0) {
								?>
								<table align="left" rules="all" class="rpt_table" width="100%" border="1">
									<thead>
										<tr>
											<th align="left" style="font-size:20px;" colspan="4"><strong>Trims
											Details</strong></th>
										</tr>
										<tr>
											<th width="50">SL</th>
											<th width="250">Item Description</th>
											<th width="150">Weight In Kg</th>
											<th>Remarks</th>
										</tr>
									</thead>
									<tbody>
										<?
										$i = 1;
										foreach ($save_ref as $data_ref) {
											$data_ref = explode("_", $data_ref);
											?>
											<tr>
												<td align="center"><? echo $i; ?></td>
												<td><? echo $data_ref[0]; ?> </td>
												<td align="right"><? echo number_format($data_ref[1], 2); ?></td>
												<td align="center"><? echo $data_ref[2]; ?></td>
											</tr>
											<?
											$i++;
										}
										?>
									</tbody>
								</table>
								<?
							}
							?>

						</td>
					</tr>

				</table>
				<br>
			</div>

			<div style="margin-right: 2px;margin-bottom: 2px; float: left;">
				<div style="margin-right: 2px; float: left;">
					<table cellspacing="0" width="170" align="left" border="1" rules="all" class="rpt_table" style="margin-bottom: 2px;">
						<tr><th colspan="2">Dyeing Information</th></tr>
						<tr>
							<td width="70">Date & Shift </td>
							<td width="100">&nbsp; </td>
						</tr>
						<tr>
							<td>Operator</td>
							<td>&nbsp; </td>
						</tr>
						<tr>
							<td>Machine</td>
							<td>&nbsp; </td>
						</tr>
						<tr>
							<td>Load Time</td>
							<td>&nbsp; </td>
						</tr>
						<tr>
							<td>Unload Time</td>
							<td>&nbsp; </td>
						</tr>
						<tr>
							<td>DYE. M/C Brand</td>
							<td>&nbsp; </td>
						</tr>
					</table>
				</div>
				<div style="float: left;">
					<table width="170" cellspacing="0" border="1" rules="all" class="rpt_table">
						<tr><th colspan="2">Slitting Info</th></tr>
						<tr height="30">
							<td width="70">Date & Shift</td>
							<td width="100">&nbsp; </td>
						</tr>
						<tr height="30">
							<td>Operator</td>
							<td>&nbsp; </td>
						</tr>
						<tr height="30">
							<td>Machine </td>
							<td>&nbsp; </td>
						</tr>
						<tr height="30">
							<td>In time </td>
							<td>&nbsp; </td>
						</tr>
						<tr height="30">
							<td>Out Time </td>
							<td>&nbsp; </td>
						</tr>
					</table>
				</div>
				<br><br><br><br><br><br><br><br><br><br>

				<table width="245" cellspacing="0" border="1" rules="all" class="rpt_table">
					<tr><th colspan="2">Quality  Inspection Info</th></tr>
					<tr>
						<td width="100">Date & Shift</td>
						<td width="145">&nbsp; </td>
					</tr>
					<tr height="30">
						<td>Inspector</td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Pass Rolls </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Fail Rolls </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Remarks</td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Shade Result</td>
						<td>&nbsp; </td>
					</tr>
				</table>
			</div>

			<div style="margin-right: 2px;margin-bottom: 2px; float: left;">
				<table width="170" cellspacing="0" border="1" rules="all" class="rpt_table">
					<tr><th colspan="2">Stenter Info 1</th></tr>
					<tr>
						<td width="70">Date & Shift</td>
						<td width="100">&nbsp; </td>
					</tr>
					<tr>
						<td>Operator</td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Machine </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Temp.</td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Width </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>GSM </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Speed </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>A/Dia </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Over Feed  </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Under Feed</td>
						<td>&nbsp; </td>
					</tr>
				</table>
			</div>
			<div style="margin-right: 2px;margin-bottom: 2px; float: left;">
				<table width="170" cellspacing="0" border="1" rules="all" class="rpt_table">
					<tr><th colspan="2">Stenter Info 2</th></tr>
					<tr>
						<td width="70">Date & Shift</td>
						<td width="100">&nbsp; </td>
					</tr>
					<tr>
						<td>Operator</td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Machine </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Temp.</td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Width </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>GSM </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Speed </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>A/Dia </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Over Feed  </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Under Feed</td>
						<td>&nbsp; </td>
					</tr>
				</table>
			</div>

			<div style="margin-right: 2px;margin-bottom: 2px; float: left;">
				<table width="170" cellspacing="0" border="1" rules="all" class="rpt_table">
					<tr><th colspan="2">Compacting Info	</th></tr>
					<tr>
						<td width="70">Date & Shift</td>
						<td width="100">&nbsp; </td>
					</tr>
					<tr>
						<td>Operator</td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Machine </td>
						<td>&nbsp; </td>
					</tr>

					<tr>
						<td>F.Width </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>F. GSM </td>
						<td>&nbsp; </td>
					</tr>
				</table>
				<br>
				<table width="170" cellspacing="0" border="1" rules="all" class="rpt_table">
					<tr><th colspan="2">Sueding Info</th></tr>
					<tr>
						<td width="70">Date & Shift</td>
						<td width="100">&nbsp; </td>
					</tr>
					<tr>
						<td>Operator</td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Speed </td>
						<td>&nbsp; </td>
					</tr>
				</table>
			</div>
			<div style="margin-right: 2px;margin-bottom: 2px; float: left;">
				<table width="170" cellspacing="0" border="1" rules="all" class="rpt_table">
					<tr><th colspan="2">Brush/Raising Info 	</th></tr>
					<tr>
						<td width="70">Date & Shift</td>
						<td width="100">&nbsp; </td>
					</tr>
					<tr>
						<td>Operator</td>
						<td>&nbsp; </td>
					</tr>
				</table>
				<br>
				<table width="170" cellspacing="0" border="1" rules="all" class="rpt_table">
					<tr><th colspan="2">Searing Info</th></tr>
					<tr>
						<td width="70">Date & Shift</td>
						<td width="100">&nbsp; </td>
					</tr>
					<tr>
						<td>Operator</td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Speed </td>
						<td>&nbsp; </td>
					</tr>
				</table>
			</div>

			<div style="margin-right: 2px;margin-bottom: 2px; float: left;">
				<table width="170" cellspacing="0" border="1" rules="all" class="rpt_table">
					<tr><th colspan="2">Heat Setting</th></tr>
					<tr>
						<td width="70">Date & Shift</td>
						<td width="100">&nbsp; </td>
					</tr>
					<tr>
						<td>Operator</td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Speed </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Temp.</td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Over Feed  </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>Under Feed</td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>A/Dia </td>
						<td>&nbsp; </td>
					</tr>
					<tr>
						<td>A/GSM </td>
						<td>&nbsp; </td>
					</tr>
				</table>
			</div>

			<br>
			<?
			echo signature_table(52, $company, "1060px");
			?>

			<script type="text/javascript" src="../../js/jquery.js"></script>
			<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
			<script>
				function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 30,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $dataArray[0][csf('batch_no')]; ?>');
    </script>
    <?
    exit();
}

if ($action == "batch_card_print_7") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$batch_update_id = $data[1];
	$working_company = $data[7];
	$batch_mst_update_id = str_pad($batch_update_id, 10, '0', STR_PAD_LEFT);

	$batch_sl_no = $data[2];

	$roll_maintained = $data[8];

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$machine_library = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');

	$job_array = array();
	$job_sql = "select distinct(a.buyer_name) as buyer_name,a.style_ref_no, a.job_no_prefix_num, a.job_no, b.pub_shipment_date, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
		$job_array[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
		$job_array[$row[csf('job_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
	}

	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id
		from pro_batch_create_mst a, pro_batch_create_dtls b
		where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty, a.cuff_qty, a.SAVE_STRING";
	} else {
		$sql = "select a.id, a.batch_no, a.booking_no_id,a.booking_no,a.booking_without_order, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id,a.is_sales
		from pro_batch_create_mst a, pro_batch_create_dtls b
		where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.batch_no, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty,a.cuff_qty, a.SAVE_STRING,a.is_sales";
	}
	//echo $sql;
	$dataArray = sql_select($sql);

	$po_number = "";
	$job_number = "";
	$job_style = "";
	$buyer_id = "";
	$ship_date = "";
	$internal_ref = "";
	$file_nos = "";
	$po_id = array_unique(explode(",", $dataArray[0][csf('po_id')]));
	$booking_no = $dataArray[0][csf('booking_no')];
	$batch_against_id = $dataArray[0][csf('batch_against')];
	$batch_booking_id = $dataArray[0][csf('booking_no_id')];
	$batch_product_id = $dataArray[0][csf('prod_id')];
	$batch_booking_without = $dataArray[0][csf('booking_without_order')];

	if ($dataArray[0][csf('is_sales')] == 1) {
		$sales_order_id = $po_id[0];
		$sales_data = sql_select("select id,job_no,sales_booking_no,within_group,buyer_id,style_ref_no from fabric_sales_order_mst where id=$sales_order_id");
		if ($sales_data[0][csf("within_group")] == 1) {
			$booking_data = sql_select("select b.job_no,a.buyer_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no='$booking_no' and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 group by b.job_no,a.buyer_id");
			$job_number = $booking_data[0][csf("job_no")];
			$buyer_id = $booking_data[0][csf("buyer_id")];
			$po_number = $sales_data[0][csf("job_no")];
			$job_style = $job_array[$job_number]['style_ref_no'];
			$ship_date = "";
		} else {
			$po_number = $sales_data[0][csf("job_no")];
			$job_number = "";
			$buyer_id = $sales_data[0][csf("buyer_id")];
			$job_style = $sales_data[0][csf("style_ref_no")];
			$ship_date = "";
		}
	} else {
		foreach ($po_id as $val) {
			if ($po_number == "") $po_number = $job_array[$val]['po']; else $po_number .= ',' . $job_array[$val]['po'];
			if ($job_number == "") $job_number = $job_array[$val]['job']; else $job_number .= ',' . $job_array[$val]['job'];
			if ($job_style == "") $job_style = $job_array[$val]['style']; else $job_style .= ',' . $job_array[$val]['style'];
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

	if ($dataArray[0][csf('booking_without_order')] == 1) {
		$booking_without_order = sql_select("select booking_no_prefix_num, buyer_id from wo_non_ord_samp_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
		$booking_id = $booking_without_order[0][csf('booking_no_prefix_num')];
		$buyer_id_booking = $booking_without_order[0][csf('buyer_id')];
	} else {
		$booking_with_order = sql_select("select booking_no_prefix_num, buyer_id from wo_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
		$booking_id = $booking_with_order[0][csf('booking_no_prefix_num')];
		$buyer_id_booking = $booking_with_order[0][csf('buyer_id')];
	}


	?>
	<div style="width:1060px;">
		<table width="1060" cellspacing="0" align="center" border="0" style="font-size: 17px;">
			<tr>
				<td colspan="6" align="center" style="font-size:22px">
					<strong><? echo $company_library[$working_company]; ?></strong></td>
					<td colspan="2" align="left">Print Time:<? echo $date = date("F j, Y, g:i a"); ?></td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:18px"><strong><u>Batch Card</u></strong></td>
					<td colspan="2" id="barcode_img_id" align="right" style="font-size:24px"></td>
				</tr>
				<tr>
					<td colspan="8">&nbsp; </td>
					<td>&nbsp; </td>
				</tr>
				<tr>
					<td colspan="6" align="left" style="font-size:18px"><strong><u>Reference Details</u></strong>
					</td>
					<td style="font-size:24px; border: solid 2px;" align="center" colspan="2">
						&nbsp;<? echo $dataArray[0][csf('organic')]; ?></td>
					</tr>
					<tr>
						<td width="120"><strong>Batch No</strong></td>
						<td width="135px">:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
						<td width="120"><strong>Batch SL</strong></td>
						<td width="135px">:&nbsp;<? echo $batch_sl_no; ?></td>
						<td width="120"><strong>B. Color</strong></td>
						<td width="135px">:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
						<td width="120"><strong>Color Ran.</strong></td>
						<td width="135px">:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range_id')]]; ?></td>
					</tr>
					<tr>
						<td><strong>Batch Against</strong></td>
						<td>:&nbsp;<? echo $batch_against[$dataArray[0][csf('batch_against')]]; ?></td>
						<td><strong>Batch Ext.</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('extention_no')]; ?></td>
						<td><strong>Batch For</strong></td>
						<td>:&nbsp;<? echo $batch_for[$dataArray[0][csf('batch_for')]]; ?></td>
						<td><strong>B. Weight</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?></td>
					</tr>
					<tr>
						<td style="font-size:14px;"><strong>Buyer</strong></td>
						<td style="font-size:14px;">:&nbsp;<? if ($dataArray[0][csf('batch_against')] == 3) echo $buyer_arr[$buyer_id_booking]; else echo $buyer_arr[$buyer]; ?>
					</td>
					<?
					if ($dataArray[0][csf('batch_against')] == 3) {
						?>
						<td  style="font-size:14px;"><strong>Booking no</strong></td>
						<td  style="font-size:14px;">:&nbsp;<? echo $booking_id; ?></td>

					<? } else { ?>
						<td  style="font-size:14px;"><strong>Job</strong></td>
						<td  style="font-size:14px;">:&nbsp;<? echo $job_no; ?></td>
					<? }
					?>

					<td><strong>Order/FSO No</strong></td>
					<td>:&nbsp;<? echo $po_number; ?></td>
					<td><strong>Ship Date</strong></td>
					<td>:&nbsp;<? if (trim($ship_date) != "0000-00-00" && trim($ship_date) != "") echo implode(",", array_unique(explode(",", $ship_date))); else echo "&nbsp;"; ?></td>
				</tr>
				<tr>
					<td><strong>Collar Qty (Pcs)</strong></td>
					<td>:&nbsp;<? echo $dataArray[0][csf('collar_qty')]; ?></td>
					<td><strong>Cuff Qty (Pcs)</strong></td>
					<td>:&nbsp;<? echo $dataArray[0][csf('cuff_qty')]; ?></td>
					<td  style="font-size:14px;"><strong>Int. Ref.</strong></td>
					<td  style="font-size:14px;">:&nbsp;<? echo $internal_ref; ?></td>
					<td><strong>File No</strong></td>
					<td>:&nbsp;<? echo $file_nos; ?></td>
				</tr>
				<tr>
					<td><strong>Dying Machine</strong></td>
					<td>:&nbsp;
						<?
						if ($db_type == 2) {
							$dyeing_machine = return_field_value("(machine_no || '-' || brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
						} else if ($db_type == 0) {
							$dyeing_machine = return_field_value("concat(machine_no,'-',brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
						}
						echo $dyeing_machine;
						?></td>
						<td><strong>Remarks</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
						<td><strong>Booking No.</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('booking_no')]; ?></td>
						<td><strong>Style Ref.</strong></td>
						<td>:&nbsp;<? echo $jobstyle; ?></td>
					</tr>
				</table>
				<br/>
				<div style="float:left; font-size:17px;"><strong><u>Fabrication Details</u></strong></div>
				<table align="center" cellspacing="0" width="1060" border="1" rules="all" class="rpt_table"
				style="border-top:none;font-size: 17px;">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30">SL</th>
						<th width="60">Prog. No</th>
						<th width="80">Machine / Knitting Com</th>
						<th width="80">Body part</th>
						<th width="150">Const. & Comp.</th>
						<th width="50"  style="font-size:14px;">Fin. GSM</th>
						<th width="50"  style="font-size:14px;">Fin. Dia</th>
						<th width="70">M/Dia X Gauge</th>
						<th width="70"  style="font-size:14px;">D/W Type</th>
						<th width="60">S. Length</th>
						<th width="70">Grey Qty.</th>
						<th width="50">Roll No.</th>
						<th width="80">Yarn Lot</th>
						<th width="80"><strong>Brand</strong></th>
						<th width="80"  style="font-size:14px;">Yarn Count</th>
						<th>ID Code</th>
					</tr>
				</thead>
				<?
				$i = 1;
				$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
				$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');
				$supplier_library = return_library_array("select id,short_name from  lib_supplier", "id", "short_name");


				$machine_lib_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
				foreach ($machine_lib_sql as $row) {
					$dya_gauge_arr[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
					$dya_gauge_arr[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
					$dya_gauge_arr[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
				}



				$booking_no_id = $data[6];


				$yarn_lot_arr = array();
				$sample_arr = array();
				$yarn_count = array();
				$s_length = array();
				if ($batch_against_id == 3 && $batch_booking_without == 1)
				{
					$yarn_lot_data = sql_select("select p.booking_id, a.prod_id, a.yarn_lot, a.yarn_count, a.stitch_length,a.machine_no_id, p.knitting_company,p.knitting_source from inv_receive_master p, pro_grey_prod_entry_dtls a where  p.id=a.mst_id and p.booking_id='$batch_booking_id' and p.booking_without_order=1 and a.prod_id in($batch_product_id) and p.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0");
					foreach ($yarn_lot_data as $rows)
					{
						$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['yarncount'] = $rows[csf('yarn_count')];
						$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['stitch_length'] = $rows[csf('stitch_length')];
						$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['samplelot'] = $rows[csf('yarn_lot')];
						$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['machine_no_id'] = $rows[csf('machine_no_id')];

						$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['knitting_company'] = $rows[csf('knitting_company')];
						$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['knitting_source'] = $rows[csf('knitting_source')];
					}
				}
				else
				{
					$yarn_lot_data = sql_select("select  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot as yarn_lot, a.yarn_count, a.stitch_length as stitch_length,a.machine_no_id as machine_no_id, c.knitting_company,c.knitting_source
						from pro_grey_prod_entry_dtls a, order_wise_pro_details b, inv_receive_master c
						where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,22) and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

					foreach ($yarn_lot_data as $rows)
					{
						$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['lot'] .= $rows[csf('yarn_lot')] . ",";
						$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['stitch_length'] .= $rows[csf('stitch_length')] . ",";
						$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['brand_id'] .= $rows[csf('brand_id')] . ",";
						$yarn_count[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['yarn_count'] .= $rows[csf('yarn_count')] . ",";
						$machine_no_id[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['machine_no_id'] .= $rows[csf('machine_no_id')] . ",";

						$machine_no_id[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['knitting_company'] .= $rows[csf('knitting_company')] . ",";
						$machine_no_id[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['knitting_source'] .= $rows[csf('knitting_source')] . ",";
					}
				}



				$sql_dtls = "select a.batch_no, a.total_trims_weight, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.booking_without_order, a.process_id, a.extention_no, sum(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no, b.item_description, b.program_no, b.po_id, b.prod_id, b.body_part_id, b.width_dia_type from pro_batch_create_mst a,pro_batch_create_dtls b where a.company_id='$data[0]' and a.id=b.mst_id and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by  a.batch_no, a.total_trims_weight, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.booking_without_order, a.process_id, a.extention_no, b.item_description, b.program_no, b.po_id, b.prod_id, b.body_part_id, b.width_dia_type";

	//echo $sql_dtls;//die;
				$sql_result = sql_select($sql_dtls);
				foreach ($sql_result as $row)
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$desc = explode(",", $row[csf('item_description')]);

					if ($batch_against_id == 3 && $row[csf('booking_without_order')] == 1)
					{
						$yarn_lot = $sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['samplelot'];
						$y_count = $sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['yarncount'];
						$stitch = $sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['stitch_length'];
						$yarn_count_value = $yarncount[$y_count];
						$machine_no_id_arr = explode(',',$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['machine_no_id']);


					} else {
						$y_count = chop($yarn_count[$row[csf('prod_id')]][$row[csf('po_id')]]['yarn_count'], ",");
						$y_count_id = array_unique(explode(',', $y_count));
						$yarn_count_value = '';
						$machine_no_id_arr = array_unique(explode(',', chop($machine_no_id[$row[csf('prod_id')]][$row[csf('po_id')]]['machine_no_id'], ",")));


						$knitting_company = implode(",",array_unique(explode(',', chop($machine_no_id[$row[csf('prod_id')]][$row[csf('po_id')]]['knitting_company'], ","))));
						$knitting_source = implode(",",array_unique(explode(',', chop($machine_no_id[$row[csf('prod_id')]][$row[csf('po_id')]]['knitting_source'], ","))));

						foreach ($machine_no_id_arr as $machine_id) {
							$dya_gage=$dya_gauge_arr[$machine_id]["dia_width"]."<br>".$dya_gauge_arr[$machine_id]["gauge"];

						}

						foreach ($y_count_id as $val) {
							if ($val > 0) {
								if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
							}
						}

						$stitch = implode(", ", array_unique(explode(",", chop($yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['stitch_length'], ","))));
						$yarn_lot = implode(", ", array_unique(explode(",", chop($yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['lot'], ","))));
						$yarn_brand = array_unique(explode(",", chop($yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['brand_id'], ",")));
					}


					$brand_suplier = "";
					foreach ($yarn_brand as $brand_id) {
						if ($brand_suplier == "") $brand_suplier = $brand_name_arr[$brand_id]; else $brand_suplier .= "," . $brand_name_arr[$brand_id];
					}

					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="60" align="center" style="word-break:break-all;"><? echo $row[csf('program_no')]; ?></td>
						<?

						if ($knitting_source == 1)
						{
							foreach ($machine_no_id_arr as $machine_id)
							{
								$machin_knit_com =   $dya_gauge_arr[$machine_id]["machine_no"];;

							}
						}
						else
						{
							$machin_knit_com = $supplier_library[$knitting_company];
						}
						?>
						<td width="80" style="word-break:break-all;" align="center"><? echo $machin_knit_com; ?></td>
						<td width="80" style="word-break:break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
						<td width="150" style="word-break:break-all;"><? echo $desc[0] . "," . $desc[1]; ?></td>
						<td width="50" align="center" style="word-break:break-all; font-size:14px;"><? echo $desc[2]; ?></td>
						<td width="50" align="center" style="word-break:break-all; font-size:14px;"><? echo $desc[3]; ?></td>
						<td width="70" align="center" style="word-break:break-all;"><? echo $dya_gage; ?></td>
						<td width="70" style="word-break:break-all; font-size:14px;"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
						<td width="60" align="center" style="word-break:break-all;"><? echo $stitch; ?></td>
						<td width="70" align="right" style="word-break:break-all;"><? echo number_format($row[csf('batch_qnty')], 2); ?></td>
						<td align="center" width="50" style="word-break:break-all;"><? echo $row[csf('roll_no')]; ?></td>
						<td width="80" style="word-break:break-all;"><? echo implode(',', array_unique(explode(",", $yarn_lot))); ?></td>
						<td width="80" style="word-break:break-all;"> <? echo $brand_suplier; ?></td>
						<td width="80" style="word-break:break-all; font-size:14px;"align="center" ><? echo $yarn_count_value; ?></td>
						<td>&nbsp;</td>
					</tr>
					<?php
					$total_roll_number += $row[csf('roll_no')];
					$total_batch_qty += $row[csf('batch_qnty')];
					$i++;
				}
				?>
				<tr>
                <td style="border:none;" colspan="10" align="right"><b>Sum:</b> <? //echo $b_qty;
                ?> </td>
                <td align="right"><b><? echo number_format($total_batch_qty, 2); ?> </b></td>
                <td align="center"><b><? echo $total_roll_number; ?> </b></td>
                <td colspan="4" style="border:none;">&nbsp;</td>
            </tr>
            <tr>
                <td style="border:none;" colspan="10" align="right"><b>Trims Weight:</b> <? //echo $b_qty;
                ?> </td>
                <td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')], 2); ?> </b>
                </td>
                <td colspan="5" style="border:none;">&nbsp;</td>
            </tr>
            <tr>
            	<td style="border:none;" colspan="10" align="right"><b>Total:</b></td>
            	<td align="right">
            		<b><? echo number_format($total_batch_qty + $dataArray[0][csf('total_trims_weight')], 2); ?> </b>
            	</td>
            	<td colspan="5" style="border:none;">&nbsp;</td>
            </tr>
            <tr>
            	<td colspan="16" align="right">&nbsp; </td>
            </tr>
            <tr>
            	<td colspan="16">
            		<?
            		$save_str = $dataArray[0][csf('SAVE_STRING')];
            		$save_ref = explode(",", $save_str);
            		if (!empty($dataArray[0][csf('SAVE_STRING')]) > 0) {
            			?>
            			<table align="left" rules="all" class="rpt_table" width="100%" border="1"
            			style="font-size: 12px;">
            			<thead>
            				<tr>
            					<th align="left" style="font-size:20px;" colspan="4"><strong>Trims
            					Details</strong></th>
            				</tr>
            				<tr>
            					<th width="50">SL</th>
            					<th width="250">Item Description</th>
            					<th width="150">Weight In Kg</th>
            					<th>Remarks</th>
            				</tr>
            			</thead>
            			<tbody>
            				<?
            				$i = 1;
            				foreach ($save_ref as $data_ref) {
            					$data_ref = explode("_", $data_ref);
            					?>
            					<tr>
            						<td align="center"><? echo $i; ?></td>
            						<td><? echo $data_ref[0]; ?> </td>
            						<td align="right"><? echo number_format($data_ref[1], 2); ?></td>
            						<td align="center"><? echo $data_ref[2]; ?></td>
            					</tr>
            					<?
            					$i++;
            				}
            				?>
            			</tbody>
            		</table>
            		<?
            	}
            	?>

            </td>
        </tr>
        <tr>
        	<td colspan="16" align="right">
        		<?
        		$process = $dataArray[0][csf('process_id')];
        		$process_id = explode(',', $process);
					//print_r($process_id);
        		$process_value = '';
        		$i = 1;
        		foreach ($process_id as $val) {
        			if ($process_value == '') $process_value = $i . '. ' . $conversion_cost_head_array[$val]; else $process_value .= ", " . $i . '. ' . $conversion_cost_head_array[$val];
        			$i++;
        		}
        		?>
        		<table align="left" rules="all" class="rpt_table" width="1060" style="font-size: 17px;">
        			<tr>
        				<th align="left" style="font-size:20px;"><strong>Process Required</strong></th>
        			</tr>
        			<tr>
        				<td style="font-size:20px;" title="<? echo $process_value; ?>">
        					<p><? echo $process_value; ?></p>
        				</td>
        			</tr>
        			<tr>
        				<td align="left" style="font-size:19px;">
        					Heat Setting:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;
        					&nbsp;&nbsp;&nbsp;&nbsp;
        					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        					&nbsp; Loading Date & Time: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        					&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        					&nbsp; UnLoading Date & Time: &nbsp;
        				</td>
        			</tr>
        		</table>
        	</td>
        </tr>
    </table>
    <div style="float:left; margin-left:10px;"><strong> Quality Instruction(Hand Written)</strong></div>
    <table width="1060" cellspacing="0" align="center" style="font-size: 17px;">
    	<tr>
    		<td valign="top" align="left" width="520">
    			<table cellspacing="0" width="515" align="left" border="1" rules="all" class="rpt_table"
    			style="font-size: 17px;">
    			<tr>
    				<th>SL</th>
    				<th>Roll No</th>
    				<th>Roll Mark</th>
    				<th>Actual Dia</th>
    				<th>Roll Wgt.</th>
    				<th>Remarks</th>
    			</tr>
    			<tr height="30">
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    			</tr>
    			<tr height="30">
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    			</tr>
    			<tr height="30">
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    			</tr>
    			<tr height="30">
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    			</tr>
    			<tr height="30">
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    			</tr>
    			<tr height="30">
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    				<td>&nbsp; </td>
    			</tr>
    		</table>
    	</td>
    	<td width="10" align="justify" valign="top"></td>
    	<td width="520" valign="top" align="right">
    		<table width="515" cellspacing="0" border="1" rules="all" class="rpt_table"
    		style="font-size: 17px;">
    		<tr>
    			<th>SL</th>
    			<th>Roll No</th>
    			<th>Actual Dia</th>
    			<th>Roll Wgt.</th>
    			<th>Remarks</th>
    		</tr>
    		<tr height="30">
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    		</tr>
    		<tr height="30">
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    		</tr>
    		<tr height="30">
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    		</tr>
    		<tr height="30">
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    			<td>&nbsp; </td>
    		</tr>
    	</table>
    </td>
</tr>
<tr>
	<td width="520" valign="top">
		<table width="515" cellspacing="0" border="1" rules="all" class="rpt_table"
		style="font-size: 17px;">
		<tr>
			<th align="left"><strong>Shade Result(<i>Hand Written</i>)</strong></th>
		</tr>
		<tr>
			<td colspan="1" style="width:475px; height:80px">&nbsp;</td>
		</tr>
	</table>
</td>
<td width="10" align="justify" valign="top">&nbsp;</td>
<td width="520" valign="top" align="right">
	<table cellspacing="0" border="1" rules="all" class="rpt_table" width="515"
	style="font-size: 17px;">
	<tr>
		<th align="left" colspan="3"><strong>Shrinkage(<i>Hand Written</i>)</strong></th>
	</tr>
	<tr>
		<th><b>Length % </b></th>
		<th><b>Width % </b></th>
		<th><b> Twist % </b></th>
	</tr>
	<tr height="30">
		<td>&nbsp; </td>
		<td>&nbsp; </td>
		<td>&nbsp; </td>
	</tr>
	<tr height="30">
		<td>&nbsp; </td>
		<td>&nbsp; </td>
		<td>&nbsp; </td>
	</tr>
	<tr height="30">
		<td>&nbsp; </td>
		<td>&nbsp; </td>
		<td>&nbsp; </td>
	</tr>
	<tr height="30">
		<td>&nbsp; </td>
		<td>&nbsp; </td>
		<td>&nbsp; </td>
	</tr>
</table>
</td>
</tr>
<tr>
	<td width="1060" colspan="3">
		<table cellspacing="0" border="1" rules="all" class="rpt_table" width="1060"
		style="font-size: 17px;">
		<tr>
			<th colspan="2" align="center"><strong>Dyeing & Finishing Information(<i>Hand
			Written</i>)</strong></th>
		</tr>
		<tr>
			<th> Dyeing</th>
			<th> Finishing</th>
		</tr>
		<tr>
			<td style="width:530px; height:120px">&nbsp;</td>
			<td style="width:530px; height:120px">&nbsp;</td>
		</tr>
	</table>
</td>
</tr>
</table>
<br>
<?
echo signature_table(52, $company, "1060px");
?>
</div>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	function generateBarcode(valuess) {
		var value = valuess;

		var btype = 'code39';
		var renderer = 'bmp';

		var settings = {
			output: renderer,
			bgColor: '#FFFFFF',
			color: '#000000',
			barWidth: 1,
			barHeight: 30,
			moduleSize: 5,
			posX: 10,
			posY: 20,
			addQuietZone: 1
		};

		value = {code: value, rect: false};

		$("#barcode_img_id").show().barcode(value, btype, settings);
	}
	generateBarcode('<? echo $dataArray[0][csf('batch_no')]; ?>');
</script>
<?
exit();
}

if($action == "batch_card_print_8")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$batch_update_id = $data[1];
	$working_company = $data[7];
	$batch_mst_update_id = str_pad($batch_update_id, 10, '0', STR_PAD_LEFT);

	$batch_sl_no = $data[2];

	$roll_maintained = $data[8];
	if($roll_maintained==0)
	{
		echo "<p width='250'><b>Sorry, This Print Report is in roll level</b></p>";
		die;
	}
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$machine_library = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');

	$lapdip_no=sql_select("select job_no_mst,color_name_id,po_break_down_id,lapdip_no from wo_po_lapdip_approval_info where status_active=1 and is_deleted=0 and approval_status=3");
	foreach ($$lapdip_no as $row) {
		$lapdip_no_arr[$row[csf('job_no_mst')]][$row[csf('po_break_down_id')]][$row[csf('color_name_id')]]['lapdip_no'] = $row[csf('lapdip_no')];
	}


	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id ,a.is_sales, a.batch_date
		from pro_batch_create_mst a, pro_batch_create_dtls b
		where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty, a.cuff_qty, a.SAVE_STRING ,a.is_sales, a.batch_date ";
	} else {
		$sql = "select a.id, a.batch_no, a.booking_no_id,a.booking_no,a.booking_without_order, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id,a.is_sales, a.batch_date
		from pro_batch_create_mst a, pro_batch_create_dtls b
		where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.batch_no, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty,a.cuff_qty, a.SAVE_STRING,a.is_sales, a.batch_date";
	}
	//echo $sql;
	$dataArray = sql_select($sql);

	$po_number = "";
	$job_number = "";
	$job_style = "";
	$buyer_id = "";
	$ship_date = "";
	$internal_ref = "";
	$file_nos = "";
	$po_id = array_unique(explode(",", $dataArray[0][csf('po_id')]));
	$booking_no = $dataArray[0][csf('booking_no')];
	$batch_against_id = $dataArray[0][csf('batch_against')];
	$batch_booking_id = $dataArray[0][csf('booking_no_id')];
	$batch_product_id = $dataArray[0][csf('prod_id')];
	$batch_booking_without = $dataArray[0][csf('booking_without_order')];


	$job_array = array();$po_quantity_sum=0;
	$job_sql = "select distinct(a.buyer_name) as buyer_name,a.style_ref_no, a.job_no_prefix_num, a.job_no, b.pub_shipment_date, b.id, b.po_number, b.grouping, b.file_no,b.po_quantity from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c where a.job_no=b.job_no_mst and  b.id = c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.booking_no = '".$booking_no."'";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
		$job_array[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
		$job_array[$row[csf('job_no')]]['style_ref_no'] = $row[csf('style_ref_no')];

		if($po_check[$row[csf('id')]] == "")
		{
			$po_quantity_sum += $row[csf('po_quantity')];
			$po_check[$row[csf('id')]] = $row[csf('id')];
		}
	}



	if ($dataArray[0][csf('is_sales')] == 1) {
		$sales_order_id = $po_id[0];
		$sales_data = sql_select("select id,job_no,sales_booking_no,within_group,buyer_id,style_ref_no, po_buyer from fabric_sales_order_mst where id=$sales_order_id");

		if ($sales_data[0][csf("within_group")] == 1) {
			$booking_data = sql_select("select b.job_no,a.buyer_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no='$booking_no' and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 group by b.job_no,a.buyer_id");
			$job_number = $booking_data[0][csf("job_no")];
			$buyer_id = $booking_data[0][csf("buyer_id")];
			$po_number = $sales_data[0][csf("job_no")];
			$job_style = $job_array[$job_number]['style_ref_no'];
			$ship_date = "";
			$sales_buyer_id = $sales_data[0][csf("po_buyer")];
		} else {
			$po_number = $sales_data[0][csf("job_no")];
			$job_number = "";
			$buyer_id = $sales_data[0][csf("buyer_id")];
			$job_style = $sales_data[0][csf("style_ref_no")];
			$ship_date = "";
			$sales_buyer_id = $sales_data[0][csf("buyer_id")];
		}

	} else {
		foreach ($po_id as $val) {
			if ($po_number == "") $po_number = $job_array[$val]['po']; else $po_number .= ',' . $job_array[$val]['po'];
			if ($job_number == "") $job_number = $job_array[$val]['job']; else $job_number .= ',' . $job_array[$val]['job'];
			if ($job_style == "") $job_style = $job_array[$val]['style']; else $job_style .= ',' . $job_array[$val]['style'];
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

	if ($dataArray[0][csf('booking_without_order')] == 1) {
		$booking_without_order = sql_select("select booking_no_prefix_num, buyer_id from wo_non_ord_samp_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
		$booking_id = $booking_without_order[0][csf('booking_no_prefix_num')];
		$buyer_id_booking = $booking_without_order[0][csf('buyer_id')];
	} else {
		$booking_with_order = sql_select("select booking_no_prefix_num, buyer_id from wo_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
		$booking_id = $booking_with_order[0][csf('booking_no_prefix_num')];
		$buyer_id_booking = $booking_with_order[0][csf('buyer_id')];
	}
	if ($job_no!="") {$job_cond="and job_no_mst in('$job_no')";}else{$job_cond="";}
	$lapdip_no=sql_select("select job_no_mst,color_name_id,po_break_down_id,lapdip_no from wo_po_lapdip_approval_info where status_active=1 and is_deleted=0 and approval_status=3 $job_cond");
	foreach ($lapdip_no as $row) {

		$lapdip_no_arr[$row[csf('job_no_mst')]][$row[csf('color_name_id')]]['lapdip_no'] = $row[csf('lapdip_no')];
	}
	?>

	<div style="width:1060px;">
		<table width="1060" cellspacing="0" align="center" border="0" style="font-size: 17px; height:220px">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$working_company]; ?></strong></td>
				<td colspan="2" align="left">Print Time:<? echo $date = date("F j, Y, g:i a"); ?></td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u>Batch Card</u></strong></td>
				<td colspan="2" id="barcode_img_id" align="right" style="font-size:24px"></td>
			</tr>
			<tr>
				<td colspan="8">&nbsp; </td>
				<td>&nbsp; </td>
			</tr>
			<tr>
				<td colspan="6" align="left" style="font-size:18px"><strong><u>Reference Details</u></strong></td>
				<td style="font-size:24px; border: solid 2px;" align="center" colspan="2">&nbsp;<? echo $dataArray[0][csf('organic')]; ?></td>
			</tr>
			<tr>
				<td width="120"><strong>Batch No</strong></td>
				<td width="135px">:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
				<td width="120"><strong>Batch SL</strong></td>
				<td width="135px">:&nbsp;<? echo $batch_sl_no; ?></td>
				<td width="120"><strong>B. Color</strong></td>
				<td width="135px">:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
				<td width="120"><strong>Date</strong></td>
				<td width="135px">:&nbsp;<? echo change_date_format($dataArray[0][csf('batch_date')]); ?></td>

			</tr>
			<tr>
				<td><strong>Batch Against</strong></td>
				<td>:&nbsp;<? echo $batch_against[$dataArray[0][csf('batch_against')]]; ?></td>
				<td><strong>Batch Ext.</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('extention_no')]; ?></td>
				<td><strong>Lab dip No</strong></td>
				<td>:&nbsp;<? echo $lapdip_no_arr[$job_no][$dataArray[0][csf('color_id')]]['lapdip_no']; ?></td>
				<td><strong>Color Ran.</strong></td>
				<td>:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range_id')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:14px;"><strong>Buyer</strong></td>
				<td style="font-size:14px;">
					:&nbsp;
					<?
					if($dataArray[0][csf('is_sales')] ==1)
					{

						echo $buyer_arr[$sales_buyer_id];
					}
					else if ($dataArray[0][csf('batch_against')] == 3)
					{
						echo $buyer_arr[$buyer_id_booking];
					}
					else
					{
						echo $buyer_arr[$buyer];
					}
					?>

				</td>
				<?
				if ($dataArray[0][csf('batch_against')] == 3) {
					?>
					<td  style="font-size:14px;"><strong>Booking no</strong></td>
					<td  style="font-size:14px;">:&nbsp;<? echo $booking_id; ?></td>

				<? } else { ?>
					<td  style="font-size:14px;"><strong>Job</strong></td>
					<td  style="font-size:14px;">:&nbsp;<? echo $job_no; ?></td>
				<? }
				?>
				<td><strong>Batch For</strong></td>
				<td>:&nbsp;<? echo $batch_for[$dataArray[0][csf('batch_for')]]; ?></td>
				<td><strong>Booking Qty<br><p style="font-size: 9px;">(RMG)</p></strong></td>
				<td>
					:&nbsp;<? echo $po_quantity_sum; ?></td>
				</tr>
				<tr>
					<td><strong>Collar Qty (Pcs)</strong></td>
					<td>:&nbsp;<? echo $dataArray[0][csf('collar_qty')]; ?></td>
					<td><strong>Cuff Qty (Pcs)</strong></td>
					<td>:&nbsp;<? echo $dataArray[0][csf('cuff_qty')]; ?></td>
					<td><strong>Order/FSO No</strong></td>
					<td>:&nbsp;<? echo $po_number; ?></td>

					<td><strong>Booking No.</strong></td>
					<td>:&nbsp;<? echo $dataArray[0][csf('booking_no')]; ?></td>
				</tr>
				<tr>
					<td><strong>Dying Machine</strong></td>
					<td>:&nbsp;
						<?
						if ($db_type == 2) {
							$dyeing_machine = return_field_value("(machine_no || '-' || brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
						} else if ($db_type == 0) {
							$dyeing_machine = return_field_value("concat(machine_no,'-',brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
						}
						echo $dyeing_machine;
						?></td>
						<td><strong>Remarks</strong></td>
						<td colspan="3">:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
						<td><strong>Style Ref.</strong></td>
						<td>:&nbsp;<? echo $jobstyle; ?></td>
					</tr>
					<tr>
						<td><strong>Ship Date</strong></td>
						<td>:&nbsp;<? if (trim($ship_date) != "0000-00-00" && trim($ship_date) != "") echo implode(",", array_unique(explode(",", $ship_date))); else echo "&nbsp;"; ?>
					</td>
				</tr>
			</table>

			<div style="float:left; font-size:17px;"><strong><u>Fabrication Details</u></strong></div>
			<table align="center" cellspacing="0" width="1060" border="1" rules="all" class="rpt_table"	style="border-top:none;font-size: 17px; height:200px;">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30">SL</th>
						<th width="60">Prog. No</th>
						<th width="80">Machine / Knitting Com</th>
						<th width="80">Body part</th>
						<th width="150">Const. & Comp.</th>
						<th width="50"  style="font-size:14px;">Fin. GSM</th>
						<th width="50"  style="font-size:14px;">Fin. Dia</th>
						<th width="70">M/Dia X Gauge</th>
						<th width="70"  style="font-size:14px;">D/W Type</th>
						<th width="60">S. Length</th>
						<th width="70">Grey Qty.</th>
						<th width="50">Roll No.</th>
						<th width="80">Yarn Lot</th>
						<th width="80"><strong>Brand</strong></th>
						<th width=""  style="font-size:14px;">Yarn Count</th>
					</tr>
				</thead>
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

				$booking_no_id = $data[6];
				if ($batch_booking_without == 1) {
					$receive_basis = return_field_value("receive_basis", "inv_receive_master", "booking_no='" . $booking_no . "' and entry_form in(2,22)");
				} else {

					$receive_basis = return_field_value("receive_basis", "inv_receive_master", "booking_id='" . $booking_no_id . "' and entry_form in(2,22)");

				}

				if ($receive_basis == 0 || $receive_basis == 1) {
					$machine_info = "d.machine_dia,d.machine_gg,";
				} else if ($receive_basis == 2) {
					$machine_info = "";
				}

				if ($db_type == 0) {
					$sql_dtls = "select a.booking_no_id,e.receive_basis,e.booking_id, a.booking_without_order, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows,d.machine_dia,d.machine_gg, d.machine_no_id, group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count) as yarn_count, d.stitch_length as stitch_length, group_concat(d.brand_id) as brand_id, e.knitting_source, e.knitting_company
					from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
					where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
					group by a.booking_no_id,e.booking_id,d.machine_dia,d.machine_gg, a.booking_without_order,e.receive_basis,b.prod_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, d.stitch_length, e.knitting_source, e.knitting_company order by b.program_no";

				} else {
					$sql_dtls = "select a.booking_no_id,e.receive_basis,e.booking_id,a.booking_without_order, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows, d.machine_no_id,d.machine_dia,d.machine_gg, LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count, d.stitch_length as stitch_length, LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id, e.knitting_source, e.knitting_company
					from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
					where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
					group by a.booking_no_id,e.receive_basis,d.machine_dia,d.machine_gg, e.booking_id,a.booking_without_order,b.prod_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, d.stitch_length, e.knitting_source, e.knitting_company order by b.program_no";
				}
        //echo $sql_dtls;

				$sql_result = sql_select($sql_dtls);
				foreach ($sql_result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

					$desc = explode(",", $row[csf('item_description')]);
					$yarn_lot = implode(",", array_unique(explode(",", $row[csf('yarn_lot')])));
					$y_count = array_unique(explode(",", $row[csf('yarn_count')]));
					$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
					$yarn_count_value = "";
					foreach ($y_count as $val) {
						if ($val > 0) {
							if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
						}
					}
					$brand_value = "";
					foreach ($brand_id as $bid) {
						if ($bid > 0) {
							if ($brand_value == '') $brand_value = $brand_name_arr[$bid]; else $brand_value .= ", " . $brand_name_arr[$bid];
						}
					}

					$machine_dia_width = $row[csf('machine_dia')];
					$machine_gauge = $row[csf('machine_gg')];

					$stitch = implode(",", array_unique(explode(",", $row[csf('stitch_length')])));
					$dya_gage = $machine_dia_width . "<br>" . $machine_gauge;

					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="60" align="center" style="word-break:break-all;"><? echo $row[csf('program_no')]; ?></td>
						<?
						if ($row[csf('knitting_source')] == 1) {
							$machin_knit_com = $machine_library[$row[csf('machine_no_id')]];
						} else {
							$machin_knit_com = $supplier_library[$row[csf('knitting_company')]];
						}
						?>
						<td width="80" style="word-break:break-all;" align="center"><? echo $machin_knit_com; ?></td>
						<td width="80" style="word-break:break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
						<td width="150" style="word-break:break-all;"><? echo $desc[0] . "," . $desc[1]; ?> </td>
						<td width="50" align="center" style="word-break:break-all; font-size:14px;"><? echo $desc[2]; ?></td>
						<td width="50" align="center" style="word-break:break-all; font-size:14px;"><? echo $desc[3]; ?></td>
						<td width="70" align="center" style="word-break:break-all;"><? echo $dya_gage; ?></td>
						<td width="70" style="word-break:break-all; font-size:14px;"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
						<td width="60" align="center" style="word-break:break-all;"><? echo $stitch; ?></td>
						<td width="70" align="right" style="word-break:break-all;"><? echo number_format($row[csf('batch_qnty')], 2); ?></td>
						<td align="center" width="50" style="word-break:break-all;"><? echo $row[csf('num_of_rows')]; ?></td>
						<td width="80" style="word-break:break-all;"><? echo implode(',', array_unique(explode(",", $yarn_lot))); ?></td>
						<td width="80" style="word-break:break-all;">
							<?

							echo $brand_value;
							?></td>
							<td width="" style="word-break:break-all; font-size:14px;" align="center" ><? echo $yarn_count_value; ?></td>
						</tr>
						<?php
						$total_roll_number += $row[csf('num_of_rows')];
						$total_batch_qty += $row[csf('batch_qnty')];
						$i++;
					}
					?>
					<tr>
                    <td style="border:none;" colspan="10" align="right"><b>Sum:</b> <? //echo $b_qty;
                    ?> </td>
                    <td align="right"><b><? echo number_format($total_batch_qty, 2); ?> </b></td>
                    <td align="center"><b><? echo $total_roll_number; ?> </b></td>
                    <td colspan="4" style="border:none;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="border:none;" colspan="10" align="right"><b>Trims Weight:</b> <? //echo $b_qty;
                    ?> </td>
                    <td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')], 2); ?> </b>
                    </td>
                    <td colspan="5" style="border:none;">&nbsp;</td>
                </tr>
                <tr>
                	<td style="border:none;" colspan="10" align="right"><b>Total:</b></td>
                	<td align="right">
                		<b><? echo number_format($total_batch_qty + $dataArray[0][csf('total_trims_weight')], 2); ?> </b>
                	</td>
                	<td colspan="5" style="border:none;">&nbsp;</td>
                </tr>
                <!-- <tr>
                    <td colspan="16" align="right">&nbsp; </td>
                </tr> -->
                <tr>
                	<td colspan="16">
                		<?
                		$save_str = $dataArray[0][csf('SAVE_STRING')];
                		$save_ref = explode(",", $save_str);
                		if (!empty($dataArray[0][csf('SAVE_STRING')]) > 0) {
                			?>
                			<table align="left" rules="all" class="rpt_table" width="100%" border="1"
                			style="font-size: 12px;">
                			<thead>
                				<tr>
                					<th align="left" style="font-size:20px;" colspan="4"><strong>Trims
                					Details</strong></th>
                				</tr>
                				<tr>
                					<th width="50">SL</th>
                					<th width="250">Item Description</th>
                					<th width="150">Weight In Kg</th>
                					<th>Remarks</th>
                				</tr>
                			</thead>
                			<tbody>
                				<?
                				$i = 1;
                				foreach ($save_ref as $data_ref) {
                					$data_ref = explode("_", $data_ref);
                					?>
                					<tr>
                						<td align="center"><? echo $i; ?></td>
                						<td><? echo $data_ref[0]; ?> </td>
                						<td align="right"><? echo number_format($data_ref[1], 2); ?></td>
                						<td align="center"><? echo $data_ref[2]; ?></td>
                					</tr>
                					<?
                					$i++;
                				}
                				?>
                			</tbody>
                		</table>
                		<?
                	}
                	?>

                </td>
            </tr>
            <tr>
            	<td colspan="16" align="right">
            		<?
            		$process = $dataArray[0][csf('process_id')];
            		$process_id = explode(',', $process);

            		$process_value = '';
            		$i = 1;
            		foreach ($process_id as $val) {
            			if ($process_value == '') $process_value = $i . '. ' . $conversion_cost_head_array[$val]; else $process_value .= ", " . $i . '. ' . $conversion_cost_head_array[$val];
            			$i++;
            		}
            		?>
            		<table align="left" rules="all" class="rpt_table" width="1060" style="font-size: 17px;">
            			<tr>
            				<th align="left" style="font-size:20px;"><strong>Process Required</strong></th>
            			</tr>
            			<tr>
            				<td style="font-size:20px;" title="<? echo $process_value; ?>">
            					<p><? echo $process_value; ?></p>
            				</td>
            			</tr>
            			<tr>
            				<td align="left">
            					<table style="font-size:19px;" width="1060">
            						<tr>
            							<td width="330">Heat Setting:</td>
            							<td width="350">Loading Date & Time:</td>
            							<td >UnLoading Date & Time:</td>
            						</tr>
            					</table>

            				</td>
            			</tr>
            		</table>
            	</td>
            </tr>
        </table>


    </div>

    <?
    $barcode_arr = sql_select("select barcode_no, roll_no, qnty from pro_roll_details where mst_id =$batch_update_id and entry_form =64 and status_active =1 order by roll_no");

    foreach ($barcode_arr as $val)
    {
    	$row_barcode[]=$val[csf("roll_no")]."*".$val[csf("qnty")];
    }

    $show_row_arr=array();
    $ind=0;
    $k=1;
    for($i=0;$i<count($row_barcode);$i++)
    {
    	if($k==3)
    	{
    		$ind++;
    		$k=1;
    	}
    	if(isset($show_row_arr[$ind]) )
    		$show_row_arr[$ind].='*'.$row_barcode[$i];
    	else  $show_row_arr[$ind]=$row_barcode[$i];
    	$k++;
    }
    ?>

    <div style="margin-top: 2px; width: 1060px; padding: 0 3px; text-align:center; float:left">
    	<table width="1060" cellpadding="0" cellspacing="0" class="rpt_table" border="1">
    		<thead>
    			<tr><td colspan="11" align="left"><b><? echo "Batch No: ".$dataArray[0][csf('batch_no')];?></b></td></tr>
    			<tr bgcolor="#dddddd" align="center">
    				<th>Roll No</th>
    				<th>Grey Wgt.</th>
    				<th>F. Wgt</th>
    				<th>P. Loss</th>
    				<th>Roll Mark</th>
    				<th>&nbsp;</th>
    				<th>Roll No</th>
    				<th>Grey Wgt.</th>
    				<th>F. Wgt</th>
    				<th>P. Loss</th>
    				<th>Roll Mark</th>
    			</tr>
    		</thead>
    		<tbody>
    			<?
    			foreach ($show_row_arr as $val)
    			{
    				$ref_data = explode("*", $val);
    				?>
    				<tr bgcolor='' align="center" style="height: 30px;">
    					<td><? echo $ref_data[0] ?></td>
    					<td><? echo $ref_data[1] ?></td>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    					<td style="margin-bottom: none; ">&nbsp;</td>
    					<td><? echo $ref_data[2] ?></td>
    					<td><? echo $ref_data[3] ?></td>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    				</tr>
    				<?
    			}
    			?>
    		</tbody>
    	</table>
    </div>

    <div width="1060" cellspacing="0" align="left" style="font-size: 17px; ">
    	<?
    	echo signature_table(52, $company, "1000px");
    	?>
    </div>

    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    	function generateBarcode(valuess) {
    		var value = valuess;

    		var btype = 'code39';
    		var renderer = 'bmp';

    		var settings = {
    			output: renderer,
    			bgColor: '#FFFFFF',
    			color: '#000000',
    			barWidth: 1,
    			barHeight: 30,
    			moduleSize: 5,
    			posX: 10,
    			posY: 20,
    			addQuietZone: 1
    		};
    		value = {code: value, rect: false};
    		$("#barcode_img_id").show().barcode(value, btype, settings);
    	}
    	generateBarcode('<? echo $dataArray[0][csf('batch_no')]; ?>');
    </script>
    <?
    exit();
}

if ($action == "batch_card_print_9")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$batch_update_id = $data[1];
	$working_company = $data[7];
	$batch_mst_update_id = str_pad($batch_update_id, 10, '0', STR_PAD_LEFT);
	$batch_sl_no = $data[2];
	$roll_maintained = $data[8];
	if($roll_maintained==0)
	{
		echo "<p width='250'><b>Sorry, This Print Report is in roll level</b></p>";
		die;
	}
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$machine_library = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$season_arr = return_library_array("select id,season_name from lib_buyer_season", 'id', 'season_name');
	$user_arr = return_library_array("select id,user_full_name from user_passwd", 'id', 'user_full_name');

	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.save_string, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id ,a.is_sales, a.sales_order_id, a.insert_date,a.inserted_by, a.update_date, a.updated_by
		from pro_batch_create_mst a, pro_batch_create_dtls b
		where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty, a.cuff_qty, a.save_string ,a.is_sales, a.sales_order_id, a.insert_date,a.inserted_by, a.update_date, a.updated_by ";
	} else {
		$sql = "select a.id, a.batch_no, a.booking_no_id,a.booking_no,a.booking_without_order, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, a.save_string, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id,a.is_sales, a.sales_order_id, a.insert_date,a.inserted_by, a.update_date, a.updated_by
		from pro_batch_create_mst a, pro_batch_create_dtls b
		where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.batch_no, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty,a.cuff_qty, a.save_string,a.is_sales, a.sales_order_id, a.insert_date,a.inserted_by, a.update_date, a.updated_by";
	}

	$dataArray = sql_select($sql);

	$po_number = "";$job_number = "";$job_style = "";$buyer_id = "";
	$po_id = array_filter(array_unique(explode(",", $dataArray[0][csf('po_id')])));
	$booking_no = $dataArray[0][csf('booking_no')];
	$batch_against_id = $dataArray[0][csf('batch_against')];
	$batch_booking_id = $dataArray[0][csf('booking_no_id')];
	$batch_product_id = $dataArray[0][csf('prod_id')];
	$batch_booking_without = $dataArray[0][csf('booking_without_order')];
	$sales_order_id = $dataArray[0][csf('sales_order_id')];
	if($dataArray[0][csf('updated_by')])
	{
		$last_user = $dataArray[0][csf('updated_by')];
	}else{
		$last_user = $dataArray[0][csf('inserted_by')];
	}

	if($dataArray[0][csf('update_date')])
	{
		$last_up_time = $dataArray[0][csf('update_date')];
	}else{
		$last_up_time = $dataArray[0][csf('insert_date')];
	}

	$job_array = array();
	if ($dataArray[0][csf('is_sales')] == 1)
	{
		$sales_data = sql_select("select a.id,a.job_no,a.sales_booking_no,a.within_group,a.booking_without_order,a.buyer_id,a.style_ref_no, a.po_buyer , b.pre_cost_fabric_cost_dtls_id, b.pre_cost_remarks,a.season,a.season_id,a.style_ref_no,a.delivery_date from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=$sales_order_id and a.id = b.mst_id and b.status_active =1 and b.is_deleted =0");
		foreach ($sales_data as $value)
		{
			$pre_cost_remarks_arr[$value[csf("pre_cost_remarks")]] = $value[csf("pre_cost_remarks")];
			$pre_cost_fabric_dtls_arr[$value[csf("pre_cost_fabric_cost_dtls_id")]] = $value[csf("pre_cost_fabric_cost_dtls_id")];
			$season_id = $value[csf("season_id")];
		}
		//echo "select id, terms from  wo_booking_terms_condition where booking_no='$batch_sl_no' and entry_form=64";die;
		$terms_sql = sql_select("select id, terms from  wo_booking_terms_condition where booking_no='$batch_sl_no' and entry_form=64");
		foreach ($terms_sql as $key => $row)
		{
			$terms_condition .= $row[csf('terms')].',';
		}
		//die;
		$delivery_date = $sales_data[0][csf("delivery_date")];
		$job_style = $sales_data[0][csf("style_ref_no")];

		if ($sales_data[0][csf("within_group")] == 1 && $sales_data[0][csf("booking_without_order")] ==0)
		{
			$job_sql = "select e.job_no, e.buyer_name,e.style_ref_no, e.job_no_prefix_num, d.pub_shipment_date, d.id, d.po_number,b.contrast_color_id
			from wo_po_details_master e , wo_po_break_down d ,wo_booking_dtls c
			left join  wo_pre_cost_fabric_cost_dtls a on  a.id = c.pre_cost_fabric_cost_dtls_id and a.color_size_sensitive =3
			left join wo_pre_cos_fab_co_color_dtls b on a.id = b.pre_cost_fabric_cost_dtls_id
			where c.po_break_down_id = d.id and d.job_no_mst=e.job_no and c.booking_no='$booking_no'";

			$min_shipment_date = $max_shipment_date = "";
			$job_sql_result = sql_select($job_sql);
			foreach ($job_sql_result as $row)
			{
				$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
				$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
				$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
				$job_array[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
				$job_array[$row[csf('job_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
				$contrast_color[$color_arr[$row[csf('contrast_color_id')]]] = $color_arr[$row[csf('contrast_color_id')]];

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

				$job_number .= $row[csf('job_no')].",";

			}

			$contrast_color_names = implode(",",array_filter($contrast_color));

			$job_number = chop($job_number,",");
			$buyer_id = $booking_data[0][csf("buyer_id")];
			$po_number = $sales_data[0][csf("job_no")];
			$sales_buyer_id = $sales_data[0][csf("po_buyer")];
		}
		else
		{
			$po_number = $sales_data[0][csf("job_no")];
			$job_number = "";
			$buyer_id = $sales_data[0][csf("buyer_id")];
			$sales_buyer_id = $sales_data[0][csf("po_buyer")];
		}
	}
	else
	{
		if(!empty($po_id))
		{
			$po_ids = implode(",", $po_id);
			$job_sql = "select e.job_no, e.buyer_name,e.style_ref_no, e.job_no_prefix_num, d.pub_shipment_date, d.id, d.po_number,b.contrast_color_id
			from wo_po_details_master e , wo_po_break_down d ,wo_booking_dtls c
			left join  wo_pre_cost_fabric_cost_dtls a on  a.id = c.pre_cost_fabric_cost_dtls_id and a.color_size_sensitive =3
			left join wo_pre_cos_fab_co_color_dtls b on a.id = b.pre_cost_fabric_cost_dtls_id
			where c.po_break_down_id = d.id and d.job_no_mst=e.job_no and d.id in ($po_ids)";

			$min_shipment_date=$max_shipment_date="";
			$job_sql_result = sql_select($job_sql);
			foreach ($job_sql_result as $row)
			{
				$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
				$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
				$job_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];

				$job_style .= $row[csf('style_ref_no')].",";
				$buyer_id .= $row[csf('buyer_name')].",";
				$job_number .= $row[csf('job_no')].",";

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
		}
	}


	$job_no = implode(",", array_unique(explode(",", chop($job_number))));
	$jobstyle = implode(",", array_unique(explode(",", chop($job_style))));
	$buyer = implode(",", array_unique(explode(",", chop($buyer_id))));

	if ($dataArray[0][csf('booking_without_order')] == 1)
	{
		$booking_without_order = sql_select("select booking_no_prefix_num, buyer_id from wo_non_ord_samp_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
		$booking_id = $booking_without_order[0][csf('booking_no_prefix_num')];
		$buyer_id_booking = $booking_without_order[0][csf('buyer_id')];
	} else {
		$booking_with_order = sql_select("select booking_no_prefix_num, buyer_id from wo_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
		$booking_id = $booking_with_order[0][csf('booking_no_prefix_num')];
		$buyer_id_booking = $booking_with_order[0][csf('buyer_id')];
	}

	if ($job_no!="") {$job_cond="and job_no_mst in('$job_no')";}else{$job_cond="";}
	$lapdip_no=sql_select("select job_no_mst,color_name_id,po_break_down_id,lapdip_no from wo_po_lapdip_approval_info where status_active=1 and is_deleted=0 and approval_status=3 $job_cond");
	foreach ($lapdip_no as $row)
	{
		$lapdip_no_arr[$row[csf('job_no_mst')]][$row[csf('color_name_id')]]['lapdip_no'] = $row[csf('lapdip_no')];
	}
	?>
	<div style="width:1060px;">
		<table width="1060" cellspacing="0" align="center" border="0" style="font-size: 17px;">
			<tr>
				<td colspan="6" align="center" style="font-size:22px">
					<strong><? echo $company_library[$working_company]; ?></strong></td>
					<td colspan="2" align="left">Print Time:<? echo $date = date("F j, Y, g:i a"); ?></td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:18px"><strong><u>Batch Card</u></strong></td>
					<td colspan="2" id="barcode_img_id" align="right" style="font-size:24px"></td>
				</tr>
				<tr>
					<td colspan="8">&nbsp; </td>
					<td>&nbsp; </td>
				</tr>
				<tr>
					<td colspan="6" align="left" style="font-size:18px"><strong><u>Reference Details</u></strong>
					</td>
					<td style="font-size:24px; border: solid 2px;" align="center" colspan="2">
						&nbsp;<? echo $dataArray[0][csf('organic')]; ?></td>
					</tr>
					<tr>
						<td width="120"><strong>Batch No</strong></td>
						<td width="135px">:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
						<td width="120"><strong>Batch SL</strong></td>
						<td width="135px">:&nbsp;<? echo $batch_sl_no; ?></td>
						<td width="120"><strong>B. Color</strong></td>
						<td width="135px">:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
						<td width="120"><strong>Color Ran.</strong></td>
						<td width="135px">:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range_id')]]; ?></td>
					</tr>
					<tr>
						<td><strong>Batch Against</strong></td>
						<td>:&nbsp;<? echo $batch_against[$dataArray[0][csf('batch_against')]]; ?></td>
						<td><strong>Batch Ext.</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('extention_no')]; ?></td>
						<td><strong>C.Color</strong></td>
						<td>:&nbsp;<?
						echo $contrast_color_names;
						?></td>
						<td><strong>B. Weight</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?></td>
					</tr>

					<tr>
						<td style="font-size:14px;"><strong>Buyer</strong></td>
						<td style="font-size:14px;">
							:&nbsp;
							<?
							if($dataArray[0][csf('is_sales')] ==1)
							{

								echo $buyer_arr[$sales_buyer_id];
							}
							else if ($dataArray[0][csf('batch_against')] == 3)
							{
								echo $buyer_arr[$buyer_id_booking];
							}
							else
							{
								echo $buyer_arr[$buyer];
							}
							?>

						</td>
						<?
						if ($dataArray[0][csf('batch_against')] == 3) {
							?>
							<td  style="font-size:14px;"><strong>Booking no</strong></td>
							<td  style="font-size:14px;">:&nbsp;<? echo $booking_id; ?></td>

						<? } else { ?>
							<td  style="font-size:14px;"><strong>Job</strong></td>
							<td  style="font-size:14px;">:&nbsp;<? echo $job_no; ?></td>
						<? }
						?>

						<td><strong>Lab dip No</strong></td>
						<td>:&nbsp;<?
						echo $lapdip_no_arr[$job_no][$dataArray[0][csf('color_id')]]['lapdip_no'];
						?></td>
						<td><strong>Ship Date</strong></td>
						<td>
							:&nbsp;
							<?
							if (trim($ship_date) != "0000-00-00" && trim($ship_date) != "") echo implode(",", array_unique(explode(",", $ship_date))); else echo "&nbsp;";
							if($min_shipment_date != "")
							{
								echo "First: ".change_date_format($min_shipment_date).", Last: ".change_date_format($max_shipment_date);
							}
							?>
						</td>
					</tr>
					<tr>
						<td><strong>Collar Qty (Pcs)</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('collar_qty')]; ?></td>
						<td><strong>Cuff Qty (Pcs)</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('cuff_qty')]; ?></td>
						<td><strong>Batch For</strong></td>
						<td>:&nbsp;<? echo $batch_for[$dataArray[0][csf('batch_for')]]; ?></td>
						<td><strong>Fb Delivery Date</strong></td>
						<td>:&nbsp;<? echo change_date_format($delivery_date); ?></td>
					</tr>

					<tr>
						<td><strong>Dying Machine</strong></td>
						<td>:&nbsp;
							<?
							if ($db_type == 2) {
								$dyeing_machine = return_field_value("(machine_no || '-' || brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
							} else if ($db_type == 0) {
								$dyeing_machine = return_field_value("concat(machine_no,'-',brand) as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
							}
							echo $dyeing_machine;
							?>
						</td>
						<td><strong>Booking No.</strong></td>
						<td>:&nbsp;<? echo $dataArray[0][csf('booking_no')]; ?></td>
						<td><strong>Order/FSO No</strong></td>
						<td>:&nbsp;<? echo $po_number; ?></td>
						<td><strong>Season</strong></td>
						<td>:&nbsp;<? echo $season_arr[$season_id]; ?></td>
					</tr>
					<tr>
						<td><strong>Style Ref.</strong></td>
						<td>:&nbsp;<? echo $jobstyle; ?></td>

						<td><strong>Last User</strong></td>
						<td>:&nbsp;<? echo $user_arr[$last_user]; ?></td>
						<td><strong>Date & Time</strong></td>
						<td>:&nbsp;<? echo $last_up_time; ?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>

					</tr>
					<tr>
						<td><strong>Remarks</strong></td>
						<td colspan="6">:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>

					</tr>
					<tr>
						<td><strong>Terms and Condition </strong></td>
						<td colspan="6">:&nbsp;<? echo chop($terms_condition,','); ?></td>

					</tr>
				</table>
				<br/>
				<div style="float:left; font-size:17px;"><strong><u>Fabrication Details</u></strong></div>
				<table align="center" cellspacing="0" width="1060" border="1" rules="all" class="rpt_table"
				style="border-top:none;font-size: 17px;">
				<thead bgcolor="#dddddd" align="center">
					<tr>
									<!-- <th width="30">SL</th>
										<th width="60">Prog. No</th> -->
										<th width="80">M/C -Knit Com</th>
										<th width="80">Body part</th>
										<th width="150">Const. & Comp.</th>
										<th width="50"  style="font-size:14px;">Fin. GSM</th>
										<th width="50"  style="font-size:14px;">Fin. Dia</th>
										<th width="70">M/Dia X Gauge</th>
										<th width="70"  style="font-size:14px;">D/W Type</th>
										<th width="60">S. Length</th>
										<th width="70">Grey Qty.</th>
										<th width="50">Roll No.</th>
										<th width="80">Yarn Lot</th>
										<th width="80"><strong>Brand</strong></th>
										<th width="" style="font-size:14px;">Count & type</th>
									</tr>
								</thead>
								<?
								$i = 1;
								$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
								$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');
								$supplier_array_lib = return_library_array("select id,short_name from  lib_supplier", "id", "short_name");
								$machine_array_lib_dia = return_library_array("select id,dia_width from  lib_machine_name", "id", "dia_width");
								$machine_array_lib_gauge = return_library_array("select id,gauge from  lib_machine_name", "id", "gauge");
								$yarn_type_from_prod = return_library_array("select id,yarn_type from  product_details_master where item_category_id=1 ", "id", "yarn_type");

								$machine_lib_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
								foreach ($machine_lib_sql as $row) {
									$dya_gauge_arr[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
									$dya_gauge_arr[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
									$dya_gauge_arr[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
								}


								$booking_no_id = $data[6];
								if ($batch_booking_without == 1) {
									$receive_basis = return_field_value("receive_basis", "inv_receive_master", "booking_no='" . $booking_no . "' and entry_form in(2,22)");
								} else {

									$receive_basis = return_field_value("receive_basis", "inv_receive_master", "booking_id='" . $booking_no_id . "' and entry_form in(2,22)");

								}

								if ($receive_basis == 0 || $receive_basis == 1) {
									$machine_info = "d.machine_dia,d.machine_gg,";
								} else if ($receive_basis == 2) {
									$machine_info = "";
								}

								if ($db_type == 0) {
									$sql_dtls = "select a.booking_no_id,e.receive_basis,e.booking_id, a.booking_without_order, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows,d.machine_dia,d.machine_gg, d.machine_no_id, group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count) as yarn_count, d.stitch_length as stitch_length, group_concat(d.brand_id) as brand_id, e.knitting_source, e.knitting_company
									from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
									where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
									group by a.booking_no_id,e.booking_id,d.machine_dia,d.machine_gg, a.booking_without_order,e.receive_basis,b.prod_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, d.stitch_length, e.knitting_source, e.knitting_company order by b.program_no";

								} else {
									$sql_dtls = "select a.booking_no_id,e.receive_basis,e.booking_id,a.booking_without_order, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows, d.machine_no_id,d.machine_dia,d.machine_gg, LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count, d.stitch_length as stitch_length, LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id,LISTAGG(CAST(d.yarn_prod_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_prod_id) as yarn_prod_id, e.knitting_source, e.knitting_company
									from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
									where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
									group by a.booking_no_id,e.receive_basis,d.machine_dia,d.machine_gg, e.booking_id,a.booking_without_order,b.prod_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, d.stitch_length, e.knitting_source, e.knitting_company order by b.program_no";
								}

								$sql_result = sql_select($sql_dtls);
								foreach ($sql_result as $row)
								{
									if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
									$desc = explode(",", $row[csf('item_description')]);



									$yarn_lot = implode(",", array_unique(explode(",", $row[csf('yarn_lot')])));
									$y_count = array_unique(explode(",", $row[csf('yarn_count')]));
									$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
									$yarn_prod_id = array_unique(explode(",", $row[csf('yarn_prod_id')]));
									$yarn_count_value = "";
									foreach ($y_count as $val) {
										if ($val > 0) {
											if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
										}
									}
									$brand_value = "";
									foreach ($brand_id as $bid) {
										if ($bid > 0) {
											if ($brand_value == '') $brand_value = $brand_name_arr[$bid]; else $brand_value .= ", " . $brand_name_arr[$bid];
										}
									}
									$type_value = "";
									foreach ($yarn_prod_id as $tid) {
										if ($tid > 0) {
											if ($type_value == '') $type_value = $yarn_type[$yarn_type_from_prod[$tid]]; else $type_value .= ", " . $yarn_type[$yarn_type_from_prod[$tid]];
										}
									}



									$machine_dia_width = $row[csf('machine_dia')];
									$machine_gauge = $row[csf('machine_gg')];


									$stitch = implode(",", array_unique(explode(",", $row[csf('stitch_length')])));
									$dya_gage = $machine_dia_width . "<br>" . $machine_gauge;
									?>
									<tr bgcolor="<? echo $bgcolor; ?>">
									<!-- <td width="30" align="center"><? //echo $i; ?></td>
										<td width="60" align="center" style="word-break:break-all;"><? //echo $row[csf('program_no')]; ?></td> -->
										<?
										if ($row[csf('knitting_source')] == 1) {
											$machin_knit_com = $machine_library[$row[csf('machine_no_id')]];
										} else {
											$machin_knit_com = $supplier_library[$row[csf('knitting_company')]];
										}
										?>
										<td width="80" style="word-break:break-all;" align="center"><? echo $machin_knit_com; ?></td>
										<td width="80" style="word-break:break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
										<td width="150" style="word-break:break-all;"><? echo $desc[0] . "," . $desc[1]; ?></td>
										<td width="50" align="center" style="word-break:break-all; font-size:14px;"><? echo $desc[2]; ?></td>
										<td width="50" align="center" style="word-break:break-all; font-size:14px;"><? echo $desc[3]; ?></td>
										<td width="70" align="center" style="word-break:break-all;"><? echo $dya_gage; ?></td>
										<td width="70" style="word-break:break-all; font-size:14px;"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
										<td width="60" align="center" style="word-break:break-all;"><? echo $stitch; ?></td>
										<td width="70" align="right" style="word-break:break-all;"><? echo number_format($row[csf('batch_qnty')], 2); ?></td>
										<td align="center" width="50" style="word-break:break-all;"><? echo $row[csf('num_of_rows')]; ?></td>
										<td width="80" style="word-break:break-all;"><? echo implode(',', array_unique(explode(",", $yarn_lot))); ?></td>
										<td width="80" style="word-break:break-all;">
											<?
											echo $brand_value;
											?>
										</td>
										<td width="" style="word-break:break-all; font-size:14px;"align="center" ><? echo $yarn_count_value." ,".$type_value; ?></td>

									</tr>
									<?php
									$total_roll_number += $row[csf('num_of_rows')];
									$total_batch_qty += $row[csf('batch_qnty')];
									$i++;
								}
								?>
								<tr>
                <td style="border:none;" colspan="8" align="right"><b>Sum:</b> <? //echo $b_qty;
                ?> </td>
                <td align="right"><b><? echo number_format($total_batch_qty, 2); ?> </b></td>
                <td align="center"><b><? echo $total_roll_number; ?> </b></td>
                <td colspan="4" style="border:none;">&nbsp;</td>
            </tr>
            <tr>
            	<td style="border:none;" colspan="8" align="right"><b>Trims Weight:</b> <?
            	?> </td>
            	<td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')], 2); ?> </b>
            	</td>
            	<td colspan="5" style="border:none;">&nbsp;</td>
            </tr>
            <tr>
            	<td style="border:none;" colspan="8" align="right"><b>Total:</b></td>
            	<td align="right">
            		<b><? echo number_format($total_batch_qty + $dataArray[0][csf('total_trims_weight')], 2); ?> </b>
            	</td>
            	<td colspan="5" style="border:none;">&nbsp;</td>
            </tr>
            <tr>
            	<td colspan="14" align="right">&nbsp; </td>
            </tr>
            <tr>
            	<td colspan="14">
            		<?
            		$save_str = $dataArray[0][csf('SAVE_STRING')];
            		$save_ref = explode(",", $save_str);
            		if (!empty($dataArray[0][csf('SAVE_STRING')]) > 0)
            		{
            			?>
            			<table align="left" rules="all" class="rpt_table" width="100%" border="1"
            			style="font-size: 12px;">
            			<thead>
            				<tr>
            					<th align="left" style="font-size:20px;" colspan="4"><strong>Trims
            					Details</strong></th>
            				</tr>
            				<tr>
            					<th width="50">SL</th>
            					<th width="250">Item Description</th>
            					<th width="150">Weight In Kg</th>
            					<th>Remarks</th>
            				</tr>
            			</thead>
            			<tbody>
            				<?
            				$i = 1;
            				foreach ($save_ref as $data_ref) {
            					$data_ref = explode("_", $data_ref);
            					?>
            					<tr>
            						<td align="center"><? echo $i; ?></td>
            						<td><? echo $data_ref[0]; ?> </td>
            						<td align="right"><? echo number_format($data_ref[1], 2); ?></td>
            						<td align="center"><? echo $data_ref[2]; ?></td>
            					</tr>
            					<?
            					$i++;
            				}
            				?>
            			</tbody>
            		</table>
            		<?
            	}
            	?>
            </td>
        </tr>
        <tr>
        	<td colspan="16" align="right">
        		<?
        		$process = $dataArray[0][csf('process_id')];
        		$process_id = explode(',', $process);
        		$process_value = '';
        		$i = 1;
        		foreach ($process_id as $val) {
        			if ($process_value == '') $process_value = $i . '. ' . $conversion_cost_head_array[$val]; else $process_value .= ", " . $i . '. ' . $conversion_cost_head_array[$val];
        			$i++;
        		}
        		?>
        		<table align="left" rules="all" class="rpt_table" width="1060" style="font-size: 17px;">
        			<tr>
        				<th align="left" style="font-size:20px;"><strong>Process Required</strong></th>
        			</tr>
        			<tr>
        				<td style="font-size:20px;" title="<? echo $process_value; ?>">
        					<p><? echo $process_value; ?></p>
        				</td>
        			</tr>
        			<tr>
        				<td align="left" style="font-size:19px;">
        					Heat Setting:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;
        					&nbsp;&nbsp;&nbsp;&nbsp;
        					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        					&nbsp; Loading Date & Time: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        					&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        					&nbsp; UnLoading Date & Time: &nbsp;
        				</td>
        			</tr>
        		</table>
        	</td>
        </tr>
    </table>
    <div style="float:left; margin-left:10px;"><strong> Quality Instruction(Hand Written)</strong></div>
    <table width="1060" cellspacing="0" align="center" style="font-size: 17px;">
    	<tr>
    		<td valign="top" align="left" width="520">
    			<table cellspacing="0" width="515" align="left" border="1" rules="all" class="rpt_table" style="font-size: 17px;">
    				<tr>
    					<th>SL</th>
    					<th>Roll No</th>
    					<th>Roll Mark</th>
    					<th>Actual Dia</th>
    					<th>Roll Wgt.</th>
    					<th>Remarks</th>
    				</tr>
    				<tr height="20">
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    				</tr>
    				<tr height="20">
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    				</tr>
    				<tr height="20">
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    				</tr>
    			</table>
    		</td>
    		<td width="10" align="justify" valign="top"></td>
    		<td width="520" valign="top" align="right">
    			<table cellspacing="0" border="1" rules="all" class="rpt_table" width="515"	style="font-size: 17px;">
    				<tr>
    					<th align="left" colspan="3"><strong>Shrinkage(<i>Hand Written</i>)</strong></th>
    				</tr>
    				<tr>
    					<th><b>Length % </b></th>
    					<th><b>Width % </b></th>
    					<th><b> Twist % </b></th>
    				</tr>
    				<tr height="20">
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    				</tr>
    				<tr height="20">
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    					<td>&nbsp; </td>
    				</tr>
    			</table>
    		</td>
    	</tr>
    	<tr>
    		<td width="520" valign="top">
    			<table width="515" cellspacing="0" border="1" rules="all" class="rpt_table"	style="font-size: 17px;">
    				<tr>
    					<th align="left"><strong>Shade Result(<i>Hand Written</i>)</strong></th>
    				</tr>
    				<tr>
    					<td colspan="1" style="width:475px; height:60px">&nbsp;</td>
    				</tr>
    			</table>
    		</td>
    		<td width="10" align="justify" valign="top">&nbsp;</td>
    		<td width="520" valign="top" align="right">

    		</td>
    	</tr>
    	<tr>
    		<td width="1060" colspan="3">
    			<table cellspacing="0" border="1" rules="all" class="rpt_table" width="1060"
    			style="font-size: 17px;">
    			<tr>
    				<th colspan="2" align="center"><strong>Dyeing & Finishing Information(<i>Hand
    				Written</i>)</strong></th>
    			</tr>
    			<tr>
    				<th> Dyeing</th>
    				<th> Finishing</th>
    			</tr>
    			<tr>
    				<td style="width:530px; height:60px">&nbsp;</td>
    				<td style="width:530px; height:60px">&nbsp;</td>
    			</tr>
    		</table>
    	</td>
    </tr>
</table>
<br>
<div style="margin-top: -100px;">
	<?
	echo signature_table(52, $company, "1060px");
	?>
</div>
</div>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	function generateBarcode(valuess) {
		var value = valuess;
		var btype = 'code39';
		var renderer = 'bmp';

		var settings = {
			output: renderer,
			bgColor: '#FFFFFF',
			color: '#000000',
			barWidth: 1,
			barHeight: 30,
			moduleSize: 5,
			posX: 10,
			posY: 20,
			addQuietZone: 1
		};
		value = {code: value, rect: false};

		$("#barcode_img_id").show().barcode(value, btype, settings);
	}
	generateBarcode('<? echo $dataArray[0][csf('batch_no')]; ?>');
</script>
<?
exit();
}

?>
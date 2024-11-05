<?
/*-------------------------------------------- Comments -----------------------
Version (MySql)          :
Version (Oracle)         :
Converted by             :
Converted Date           :
Purpose			         :
Functionality	         :
JS Functions	         :
Created by		         : Jahid Hasan
Creation date 	         : 08-05-2017
Requirment Client        : Urmi
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         :
-------------------------------------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
include('../../includes/common.php');
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$type = $_REQUEST['type'];
$permission = $_SESSION['page_permission'];

/*
2 => "Stripe [Y/D]"
3 => "Cross Over [Y/D]"
4 => "Check [Y/D]"
6 => "Solid [Y/D]"
7 => "AOP Stripe"
32 => "Space Y/D"
33 => "Faulty Y/D"
34 => "Solid Stripe"
44=>"Stripe [Y/D Melange]"
47=>"Stripe [Y/D AOP]"
48=>"Stripe [Y/D Burn-Out AOP]"
*/
$colorTypeId = '2,3,4,6,7,32,33,34,44,47,48,71';

//----------------------------------------------------Start---------------------------------------------------------
//*************************************************Master Form Start************************************************
if ($action == "load_drop_down_buyer_30052021")
{
	echo create_drop_down("cbo_buyer_name", 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
}

if ($action == "load_drop_down_buyer")
{
	$expData = explode('_', $data);
	if($expData[1] == 1)
	{
		echo create_drop_down("cbo_buyer_name", 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$expData[0]."' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
	}
	else
	{
		$sql = "SELECT id, buyer_name FROM lib_buyer WHERE id IN (SELECT customer_buyer FROM fabric_sales_order_mst WHERE status_active = 1 AND is_deleted = 0 AND company_id = ".$expData[0].")";
		echo create_drop_down("cbo_buyer_name", 160, $sql, "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
	}
}

if ($action == "sales_order_popup")
{
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][480] );

	?>
	<script>
		<?
		if (!empty($data_arr))
		 {
			echo "var field_level_data= ". $data_arr . ";\n";
		}
		else
		{
			echo "var field_level_data='';\n";
		}
		?>
		function js_set_value(sales_order_no)
		{
			document.getElementById('selected_sales_order').value = sales_order_no;
			parent.emailwindow.hide();
		}
		
		//func_onChange_company()
		function func_load_drop_down()
		{
			var company_id = $('#cbo_company_mst').val();
			var within_group = $('#cbo_within_group').val();
			load_drop_down( 'stripe_color_measurement_controller_urmi', company_id+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' )
		}
	</script>
</head>
<body>
	<div align="center" style="width:100%;">
		<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
			<table width="980" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
				<tr>
					<td align="center" width="100%">
						<table cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
							<thead>
								<th width="150">Company Name</th>
								<th width="80">Within Group</th>
								<th width="150">Cust. Buyer/ Buyer</th>
								<th width="100">Sales Order No</th>
								<th width="150">Sales Job/ Booking No</th>
								<th width="200">Sales Order Date Range</th>
								<th></th>
							</thead>
							<tr>
								<td><input type="hidden" id="selected_sales_order">
									<?
									//echo create_drop_down("cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", '', "load_drop_down( 'stripe_color_measurement_controller_urmi', this.value, 'load_drop_down_buyer', 'buyer_td' ); setFieldLevelAccess(this.value);");
									echo create_drop_down("cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", '', "func_load_drop_down(); setFieldLevelAccess(this.value);");
									?>
								</td>
								<td align="center">
									<?
									echo create_drop_down("cbo_within_group", 80, array(1 => "Yes", 2 => "No"), "", "", "", 1, "func_load_drop_down();");
									?>
								</td>
								<td id="buyer_td">
									<?
									echo create_drop_down("cbo_buyer_name", 172, $blank_array, '', 1, "-- Select Buyer --");
									?>
								</td>
								<td><input name="txt_sales_order" id="txt_sales_order" placeholder="Write"
									class="text_boxes" style="width:100px"></td>
                                <td><input name="txt_booking_no" id="txt_booking_no" placeholder="Write"
                                    class="text_boxes" style="width:150px"></td>
                                <td>
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From"
                                    style="width:70px">
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To"
                                    style="width:70px">
                                </td>
                                <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show"
                                    onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_sales_order').value+'_'+document.getElementById('cbo_within_group').value, 'create_sales_order_search_list_view', 'search_div', 'stripe_color_measurement_controller_urmi', 'setFilterGrid(\'list_view\',-1)')"
                                    style="width:100px;"/></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" height="40" valign="middle">
                            <?
                            echo create_drop_down("cbo_year_selection", 70, $year, "", 1, "-- Select --", date('Y'), "", 0);
                            ?>
                            <? echo load_month_buttons(); ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="top" id="search_div">

                        </td>
                    </tr>
                </table>

            </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if ($action == "create_sales_order_search_list_view")
{
	$data = explode('_', $data);

	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	$company_id = $data[0];
	$buyer_id = str_replace("'", "", $data[1]);
	$date_from = trim($data[2]);
	$date_to = trim($data[3]);
	$booking = str_replace("'", "", trim($data[4]));
	$year = $data[5];
	$sales_order = str_replace("'", "", trim($data[6]));
	$within_group = str_replace("'", "", trim($data[7]));
	$year_field = "";
	$date_cond = '';

	if ($date_from != "" && $date_to != "") {
		if ($db_type == 0) {
			$date_cond = "and a.insert_date between '" . change_date_format($date_from, "yyyy-mm-dd", "-") . "' and '" . change_date_format($date_to, "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and a.insert_date between '" . change_date_format($date_from, '', '', 1) . "' and '" . change_date_format($date_to, '', '', 1) . "'";
		}
	}
	$booking_cond = ($booking != "") ? " and a.sales_booking_no like '%" . $booking . "%'" : "";
	$sales_order_cond = ($sales_order != "") ? " and a.job_no_prefix_num like '%" . $sales_order . "%'" : "";
	$within_group_cond = ($within_group != "") ? " and a.within_group =$within_group" : "";
	$year_field = ($db_type == 0) ? " year(a.insert_date)" : "to_char(a.insert_date,'YYYY')";
	
	//for buyer
	//$buyer_cond = ($buyer_id != 0) ? " and b.buyer_id=$buyer_id" : "";
	$buyer_cond = "";
	$buyer_cond2 = "";
	if($buyer_id != 0)
	{
		$buyer_cond = " and b.buyer_id = ".$buyer_id."";
		$buyer_cond2 = " and a.customer_buyer = ".$buyer_id."";
	}
	
	
	if ($within_group == 1) {
		$sql = "select a.id, $year_field as year, a.job_no_prefix_num, a.job_no, a.within_group, a.company_id, a.sales_booking_no, a.booking_date, b.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a, wo_booking_mst b where a.sales_booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $booking_cond $sales_order_cond $date_cond $buyer_cond $within_group_cond and $year_field=$year and a.id in(select c.mst_id from fabric_sales_order_dtls c where a.id=c.mst_id and c.color_type_id in( ".$colorTypeId."))
		union all
		select a.id, $year_field as year, a.job_no_prefix_num, a.job_no, a.within_group, a.company_id, a.sales_booking_no, a.booking_date, b.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a,wo_non_ord_samp_booking_mst b where a.sales_booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $booking_cond $sales_order_cond $date_cond $buyer_cond $within_group_cond and $year_field=$year and a.id in(select c.mst_id from fabric_sales_order_dtls c where a.id=c.mst_id and c.color_type_id in(".$colorTypeId."))"; //  2,3,4,6,32,33  color type YD is considered only
    } else {
		$sql = "select a.id, $year_field as year, a.job_no_prefix_num, a.job_no, a.within_group, a.company_id, a.sales_booking_no, a.booking_date, a.style_ref_no, a.location_id, a.customer_buyer as buyer_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $booking_cond $sales_order_cond $date_cond $within_group_cond $buyer_cond2 and $year_field=$year and a.id in(select c.mst_id from fabric_sales_order_dtls c where a.id=c.mst_id and c.color_type_id in(".$colorTypeId.")) order by id"; // 2,3,4,6,32,33 color type YD is
	}//$buyer_cond
	//echo $sql;
	$result = sql_select($sql);
	//print_r($sql)
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="120">Sales Job/ Booking No</th>
			<th width="70">Cust. Buyer/ Po Buyer</th>
			<th width="80">Booking date</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:800px; max-height:260px;" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table"
		id="tbl_list_search">
		<?php
		if (!empty($result)) {
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				$booking_data = $row[csf('id')] . "*" . $row[csf('job_no')] . "*" . $row[csf('buyer_id')] . "*" . $row[csf('style_ref_no')] . "*" . $row[csf('company_id')] . "*" . $row[csf('within_group')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value('<? echo $booking_data; ?>');">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="90" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
					<td width="60" align="center"><? echo $row[csf('year')]; ?></td>
					<td width="80" align="center"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
					<td width="120" align="center"><? echo $row[csf('sales_booking_no')]; ?></td>
					<td width="70" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="110" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
					<td align="center"><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
				</tr>
				<?
				$i++;
			}
		} else {
			?>
			<tr>
				<td colspan="10" style="text-align: center; color: red; font-weight: bold;">No Data Found</td>
			</tr>
			<?php
		}
		?>
	</table>
</div>
<?
exit();
}

if ($action == "show_fabric_color_listview") {
	?>
	<fieldset style="width:810px; margin-top: 10px;">
		<form id="fabriccost_3" autocomplete="off">
			<input type="hidden" id="tr_sales_order" name="tr_sales_order" value="" width="500"/>
			<table width="810" cellspacing="0" class="rpt_table" border="0" id="tbl_fabric_cost" rules="all">
				<thead>
					<tr>
						<th width="415">Fabric Description</th>
						<th width="150">Gmts Item</th>
						<th width="115">Fab Nature</th>
						<th width="125">Color</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$fab_description = array();
					$gmts_item_id = array();
					$fab_description_array = sql_select("select a.id,a.job_no,a.within_group,b.fabric_desc, b.determination_id, b.item_number_id,b.color_type_id,b.body_part_id,b.pre_cost_fabric_cost_dtls_id,b.gsm_weight,b.dia,b.width_dia_type from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.id=$data and b.status_active=1 and b.is_deleted=0 and b.color_type_id in(".$colorTypeId.") group by a.id,a.job_no,b.fabric_desc,b.determination_id,b.color_type_id,b.item_number_id,b.body_part_id,b.pre_cost_fabric_cost_dtls_id,b.gsm_weight,b.dia,b.width_dia_type,a.within_group");
					foreach ($fab_description_array as $fab_description_row) {
						$fab_description[$fab_description_row[csf("id")] . '_' . $fab_description_row[csf("body_part_id")] . '_' . $fab_description_row[csf("color_type_id")] . '_' . $fab_description_row[csf("fabric_desc")] . '_' . $fab_description_row[csf("item_number_id")] . '_' . $fab_description_row[csf("pre_cost_fabric_cost_dtls_id")] . '_' . $fab_description_row[csf("gsm_weight")] . '_' . $fab_description_row[csf("dia")] . '_' . $fab_description_row[csf("width_dia_type")] . '_' . $fab_description_row[csf("within_group")]] = $body_part[$fab_description_row[csf("body_part_id")]] . ', ' . $color_type[$fab_description_row[csf("color_type_id")]] . ', ' . $fab_description_row[csf("fabric_desc")] . ', ' . $fab_description_row[csf("gsm_weight")] . ', ' . $fab_description_row[csf("dia")];
						$gmts_item_id[$fab_description_row[csf("item_number_id")]] = $garments_item[$fab_description_row[csf("item_number_id")]];
					}
					//   2,3,4,6,32,33
					?>
					<tr id="fabriccosttbltr_<? echo $i; ?>" align="center">
						<td>
							<input type="hidden" id="libyarncountdeterminationid" name="libyarncountdeterminationid"
							class="text_boxes" style="width:10px"/>
							<?php echo create_drop_down("fabricdescription", 415, $fab_description, "", 1, " -- Select--", "", "set_data(this.value)", "", ""); ?>
						</td>
						<td>
							<?php echo create_drop_down("cbogmtsitem", 150, $gmts_item_id, "", 1, "Display", "", "", 1, "", ""); ?>
						</td>
						<td>
							<? echo create_drop_down("cbofabricnature", 115, $item_category, "", 1, "Display", "", "", 1, "2,3"); ?>
						</td>
						<td id="color_td">
							<? echo create_drop_down("cbo_color_name", 125, $blank_array, "", 1, "-- Select Color --", $selected, "open_color_popup()"); ?>
							<input type="hidden" id="updateid" name="updateid" class="text_boxes" style="width:20px"/>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</fieldset>
	<?
}

if ($action == "set_data") {
	$data = explode('_', str_replace("'", "", $data));
	$within_group = $data[9];
	$item_number_sql = ($within_group == 1) ? " and b.item_number_id=$data[4]" : "";
	$item_array = sql_select("select b.item_number_id from fabric_sales_order_dtls b where b.mst_id=$data[0] and b.body_part_id=$data[1] and b.fabric_desc='$data[3]' and b.color_type_id=$data[2] $item_number_sql and b.gsm_weight=$data[6] and b.dia='$data[7]' and b.width_dia_type=$data[8] and b.status_active=1 and b.is_deleted=0 and b.color_type_id in(".$colorTypeId.") group by b.item_number_id");
	//2,3,4,6,32,33
	foreach ($item_array as $row) {
		echo "document.getElementById('cbogmtsitem').value = '" . $row[csf("item_number_id")] . "';\n";
		echo "document.getElementById('cbofabricnature').value = '2';\n";
	}
	die;
}

if ($action == "load_drop_down_color") {
	$data = explode('_', str_replace("'", "", $data));
	$within_group = $data[9];
	if ($within_group == 1) {
		$dia_type_cond = ($data[8] != "")?" and b.width_dia_type=$data[8]":"";
		echo create_drop_down("cbo_color_name", 125, "select b.id,b.color_id,c.color_name,b.pre_cost_fabric_cost_dtls_id,b.color_type_id from fabric_sales_order_dtls b,lib_color c where b.color_id=c.id and b.mst_id=$data[0] and b.body_part_id=$data[1] and b.fabric_desc='$data[3]' and b.color_type_id=$data[2] and b.item_number_id=$data[4] and b.gsm_weight=$data[6] and b.dia='$data[7]' $dia_type_cond and b.status_active=1 and b.is_deleted=0", "color_id,color_name,id,pre_cost_fabric_cost_dtls_id,color_type_id", 1, "-- Select Color --", $selected_index, "open_color_popup()", $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name, "", "2,3,4");
	} else {
		echo create_drop_down("cbo_color_name", 125, "select b.id,b.color_id,c.color_name,b.pre_cost_fabric_cost_dtls_id,b.color_type_id from fabric_sales_order_dtls b,lib_color c where b.color_id=c.id and b.mst_id=$data[0] and b.body_part_id=$data[1] and b.fabric_desc='$data[3]' and b.color_type_id=$data[2] and b.gsm_weight=$data[6] and b.dia='$data[7]' and b.width_dia_type=$data[8] and b.status_active=1 and b.is_deleted=0", "color_id,color_name,id,pre_cost_fabric_cost_dtls_id,color_type_id", 1, "-- Select Color --", $selected_index, "open_color_popup()", $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name, "", "2,3,4");
	}
	die;
}

if ($action == "open_color_list_view") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var permission = '<? echo $permission; ?>';
		function add_break_down_set_tr(i) {
			var row_num = $('table#tbl_set_details tbody tr').length;
			if (row_num != i) {
				return false;
			}
			if (form_validation('stcolor_' + i, 'Stripe Color') == false) {
				return;
			}
			else {
				var progYwdDone=$('#progYwdDone').val()*1;
				var systemNo=$('#systemNo_' + i).val();
				if(progYwdDone>0)
				{
					alert("Found Yarn Dyeing Work Order Sales/Program " + systemNo );
					return;
				}
				i++;
				$("table#tbl_set_details tbody tr:last").clone().find("input,select,a").each(function () {
					$(this).attr({
						'id': function (_, id) {
							var id = id.split("_");
							return id[0] + "_" + i
						},
						'name': function (_, name) {
							return name + i
						},
						'value': function (_, value) {
							return value
						}
					});
				}).end().appendTo("table#tbl_set_details tbody");
				$('#measurement_' + i).removeAttr("onChange").attr("onChange", "calculate_fidder(" + i + ")");
				$('#totfidder_' + i).removeAttr("onChange").attr("onChange", "calculate_fidder(" + i + ")");
				$('#increaseset_' + i).removeAttr("onClick").attr("onClick", "add_break_down_set_tr(" + i + ")");
				$('#decreaseset_' + i).removeAttr("onClick").attr("onClick", "fn_delete_down_tr(" + i + ",'tbl_set_details')");
				

				$('#stcolor_' + i).val('');
				$('#measurement_' + i).val('');
				$('#cboorderuom_' + i).val('');
				$('#totfidder_' + i).val('');
				$('#colorIdFoundStatus_' + i).val('');
				$('#sequence_' + i).val('');
				$('#sequence_' + i).val(i);

				$('#stcolor_' + i).removeAttr("disabled","disabled");
				$('#measurement_' + i).removeAttr("disabled","disabled");
				$('#cboorderuom_' + i).removeAttr("disabled","disabled");
				$('#totfidder_' + i).removeAttr("disabled","disabled");
				$('#yarndyed_' + i).removeAttr("disabled","disabled");
			}
		}

		function fn_delete_down_tr(rowNo, table_id) {
			if (table_id == 'tbl_set_details') {
				var numRow = $('table#tbl_set_details tbody tr').length;
				if (numRow == rowNo && rowNo != 1) {
					//var colorIdFoundFlag=$('#colorIdFoundStatus_' + rowNo).val()*1;
					var progYwdDone=$('#progYwdDone').val()*1;
					var systemNo=$('#systemNo_' + rowNo).val();
					if(progYwdDone>0)
					{
						alert("Found Yarn Dyeing Work Order Sales/Program " + systemNo );
						return;
					}
					else
					{
						$('#tbl_set_details tbody tr:last').remove();
						set_sum();
						calculate_fab_req()
					}
				}
			}
		}

		function color_select_popup(buyer_name, texbox_id) {
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'stripe_color_measurement_controller_urmi.php?action=color_popup&buyer_name=' + buyer_name, 'Color Select Pop Up', 'width=250px,height=300px,center=1,resize=1,scrolling=0', '../../')
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var color_name = this.contentDoc.getElementById("color_name");
				if (color_name.value != "") {
					$('#' + texbox_id).val(color_name.value);
				}
			}
		}

		function calculate_fidder(i) {
			set_sum();
			calculate_fab_req();
		}
		function set_sum() {
			var tottalmeasurement = 0;
			var totaltotfidder = 0;
			var row_num = $('table#tbl_set_details tbody tr').length;
			for (var i = 1; i <= row_num; i++) {
				var measurement = document.getElementById('measurement_' + i).value * 1;
				var totfidder = document.getElementById('totfidder_' + i).value * 1;
				tottalmeasurement += measurement;
				totaltotfidder += totfidder;
			}
			if (tottalmeasurement > 0) {
				document.getElementById('tottalmeasurement').value = number_format_common(tottalmeasurement, 5, 0);
			}
			else {
				document.getElementById('tottalmeasurement').value = '';
			}
			if (totaltotfidder > 0) {
				document.getElementById('totaltotfidder').value = number_format_common(totaltotfidder, 5, 0);
			}
			else {
				document.getElementById('totaltotfidder').value = '';
			}
		}

		function calculate_fab_req() {
			var consdzn = document.getElementById('consdzn').value * 1;
			var TotalGreyreq = document.getElementById('TotalGreyreq').value * 1;
			var totaltotfidder = document.getElementById('totaltotfidder').value * 1;
			var tottalmeasurement = document.getElementById('tottalmeasurement').value * 1;
			var row_num = $('table#tbl_set_details tbody tr').length;
			var totalfabreq = 0;
			for (var i = 1; i <= row_num; i++) {
				var measurement = document.getElementById('measurement_' + i).value * 1;
				var fabreq = 0;
				var fabreqtotkg = 0;
				if (measurement > 0) {
					fabreq = (consdzn / tottalmeasurement) * measurement;
					totalfabreq += fabreq;
					fabreqtotkg = (TotalGreyreq / tottalmeasurement) * measurement;

				} else {
					var totfidder = document.getElementById('totfidder_' + i).value * 1;
					fabreq = (consdzn / totaltotfidder) * totfidder;
					totalfabreq += fabreq;
					fabreqtotkg = (TotalGreyreq / totaltotfidder) * totfidder;
				}
				document.getElementById('fabreq_' + i).value = number_format_common(fabreq, 5, 0);
				document.getElementById('fabreqtotkg_' + i).value = number_format_common(fabreqtotkg, 5, 0);
			}
			if (totalfabreq > 0) {
				document.getElementById('totalfabreq').value = number_format_common(totalfabreq, 5, 0);
			} else {
				document.getElementById('totalfabreq').value = '';
			}
		}

		function fnc_stripe_color(operation) {
			if (operation == 2) {
				alert("Delete Restricted")
				return;
			}
			var row_num = $('table#tbl_set_details tbody tr').length;
			var data_all = "";
			for (var i = 1; i <= row_num; i++) {
			//                if (form_validation('stcolor_' + i + '*measurement_' + i + '*cboorderuom_' + i, 'Stripe Color*Measurement*UOM') == false) {
			//                    return;
			//                }
		if (form_validation('stcolor_' + i, 'Stripe Color') == false) {
			return;
		}
		data_all = data_all + get_submitted_data_string('txt_sales_order_no*cbogmtsitem*fabric_cost_id*cbo_color_name*hdnSalesDtlsId*hdnWithinGroup*stcolor_' + i + '*measurement_' + i + '*cboorderuom_' + i + '*totfidder_' + i + '*fabreq_' + i + '*fabreqtotkg_' + i + '*yarndyed_' + i + '*sequence_' + i, "../../", i);
		}

		var data = "action=save_update_delete&operation=" + operation + '&total_row=' + row_num + data_all;
		freeze_window(operation);
		http.open("POST", "stripe_color_measurement_controller_urmi.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_stripe_color_reponse;
		}

		function fnc_stripe_color_reponse() {

			if (http.readyState == 4) {
				var reponse = trim(http.responseText).split('**');
				if (reponse[0].length > 2) reponse[0] = 10;
				release_freezing();
				if (reponse[0] == 0 || reponse[0] == 1) {
					parent.emailwindow.hide();
				}
			}
		}

	</script>
</head>
<body>
	<div align="center" style="width:100%;">
		<? echo load_freeze_divs("../../", $permission); ?>
		<?php
		$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
		$sales_order_info = sql_select("select fabric_desc, grey_qty,color_id,item_number_id,pre_cost_fabric_cost_dtls_id,determination_id,color_type_id,body_part_id from fabric_sales_order_dtls a where id=$sales_dtls_id");
		?>
		<table width="460" cellspacing="0" class="rpt_table" border="1" rules="all">
			<table width="460" cellspacing="0" class="rpt_table" border="1" rules="all">

				<tr>
					<td width="150">Consumpiton</td>
					<td width="150" align="right">
						<input type="hidden" id="TotalGreyreq"
						value="<?php echo $sales_order_info[0][csf('grey_qty')]; ?> "/>
						<input type="hidden" id="hdnSalesDtlsId" value="<?php echo $sales_dtls_id; ?> "/>
						<input type="hidden" id="consdzn"
						value="<?php echo $sales_order_info[0][csf('grey_qty')]; ?>"/>
						<?php echo number_format($sales_order_info[0][csf('grey_qty')], 4); ?>
					</td>
					<td width="60">Kg <? //echo $plan_cut_qnty; ?></td>
				</tr>
			</tr>
			<tr>
				<td width="150">Fabric Desc</td>
				<td width="150" colspan="2"><?php echo $sales_order_info[0][csf('fabric_desc')]; ?></td>
			</tr>
		</tr>
		<tr>
			<td width="150">Body Color</td>
			<td width="150" colspan="2"><? echo $color_library[$sales_order_info[0][csf('color_id')]]; ?></td>
		</tr>
	</table>
	<br/>
	<input type="hidden" id="txt_sales_order_no" name="txt_sales_order_no" style="width:150px"
	class="text_boxes" value="<? echo $hdn_sales_order; ?>"/>
	<input type="hidden" id="hdnWithinGroup" name="hdnWithinGroup" value="<?php echo $hdn_within_group; ?> "/>
	<input type="hidden" id="cbogmtsitem" name="cbogmtsitem" style="width:150px" class="text_boxes"
	value="<? echo $cbogmtsitem; ?>"/>
	<input type="hidden" id="fabric_cost_id" name="fabric_cost_id" style="width:150px" class="text_boxes"
	value="<? echo $pre_cost_id; ?>"/>
	<input type="hidden" id="cbo_color_name" name="cbo_color_name" style="width:150px" class="text_boxes"
	value="<? echo $cbo_color_name; ?>"/>
	<table width="780" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
		<thead>
			<tr>
				<th width="150">Stripe Color</th>
				<th width="150">Measurement</th>
				<th width="60">UOM</th>
				<th width="80">Total Feeder</th>
				<th width="70">Fab Req. Qty (kg)</th>
				<th width="70">Yarn Dyed</th>
				<th width="50">Seq.</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?

			$pre_cost_fabric_cost_dtls_id=$sales_order_info[0][csf('pre_cost_fabric_cost_dtls_id')];
			$determination_id=$sales_order_info[0][csf('determination_id')];
			$color_type_id=$sales_order_info[0][csf('color_type_id')];
			$body_part_id=$sales_order_info[0][csf('body_part_id')];
			$color_id =$sales_order_info[0][csf('color_id')];
 
			$found_prog_by_sales= sql_select("select a.DTLS_ID from ppl_planning_entry_plan_dtls a,fabric_sales_order_mst b where a.po_id=b.id and a.company_id=$cbo_company_name  and b.job_no='$hdn_sales_order'  and a.status_active=1 and a.is_deleted=0 and a.is_sales=1");
			if($found_prog_by_sales[0]['DTLS_ID']!="")
			{
				$if_program_found_disable="disabled";
			}
		
			$color_from_library = return_field_value("color_from_library", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=23  and status_active=1 and is_deleted=0");
			if ($color_from_library == 1) {
				$readonly = "readonly='readonly'";
				$plachoder = "placeholder='Click'";
				$onClick = "onClick='color_select_popup($cbo_buyer_name,this.id)'";
			} else {
				$readonly = "";
				$plachoder = "";
				$onClick = "";
			}
			$save_update = 1;
			$pre_cost_con = ($hdn_within_group == 1) ? "and pre_cost_fabric_cost_dtls_id=$pre_cost_id" : "";
			//echo "select stripe_color,measurement,uom,totfidder,fabreq,fabreqtotkg,yarn_dyed from wo_pre_stripe_color where color_number_id=$cbo_color_name and sales_dtls_id=$sales_dtls_id $pre_cost_con and status_active=1 and is_deleted=0 ";
			$sql_data = sql_select("select stripe_color,measurement,uom,totfidder,fabreq,fabreqtotkg,yarn_dyed,sequence from wo_pre_stripe_color where color_number_id=$cbo_color_name and sales_dtls_id=$sales_dtls_id $pre_cost_con and status_active=1 and is_deleted=0 ");
			
			//-------------validation if found Planning Info Entry For Sales Order entry page------------------
			$found_prog_chk= sql_select("select a.dtls_id,a.body_part_id,a.determination_id,a.pre_cost_fabric_cost_dtls_id,  a.color_type_id,d.stripe_color_id  from ppl_color_wise_break_down c,ppl_planning_feeder_dtls d,ppl_planning_entry_plan_dtls a,fabric_sales_order_mst b where c.plan_id=a.mst_id  and c.plan_id=d.mst_id and c.program_no=d.dtls_id and d.dtls_id=a.dtls_id and c.program_no=a.dtls_id and a.po_id=b.id and a.company_id=$cbo_company_name  and b.job_no='$hdn_sales_order' and c.color_id=$color_id and a.body_part_id=$body_part_id and a.determination_id=$determination_id and a.pre_cost_fabric_cost_dtls_id=$pre_cost_fabric_cost_dtls_id and a.color_type_id=$color_type_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.is_sales=1");
			if(!empty($found_prog_chk))
			{
				foreach ($found_prog_chk as $rows)
				{
					$progYwdDone=1;
					//$disableActive="disabled";
					//$colorIdFoundStatus=1;
					$disableActiveArr[$rows[csf('stripe_color_id')]]="disabled";
					$colorIdFoundStatusArr[$rows[csf('stripe_color_id')]]=1;
					$systemNo=$rows[csf('dtls_id')];
				}
			}
			
			//-------------------------end--------------------------------------------------------------
			//-------------validation if found yarn dyeing work order sales entry page------------------
			if(empty($found_prog_chk))
			{
				$yarnDyeingSql=sql_select("select a.ydw_no,b.yarn_color from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.is_sales=1 and a.entry_form=135 and b.entry_form=135 and a.company_id=$cbo_company_name  and b.job_no='$hdn_sales_order' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

				foreach ($yarnDyeingSql as $rows)
				{
					$progYwdDone=2;
					$stripColorArr[$rows[csf('yarn_color')]]['colorIdFoundStatus']=1;
					$stripColorArr[$rows[csf('yarn_color')]]['ydw_no']=$rows[csf('ydw_no')];
				}
			}
			//-------------------------end--------------------------------------------------------------

			if (count($sql_data) > 0) {
				$i = 1;
				$totmeasurement = 0;
				$totfidder = 0;
				$fabreq = 0;
				foreach ($sql_data as $row) {
					$totmeasurement += $row[csf('measurement')];
					$totfidder += $row[csf('totfidder')];
					$fabreq += $row[csf('fabreq')];

					if($stripColorArr[$row[csf('stripe_color')]]['colorIdFoundStatus']==1)
					{
						$disableActiveArr[$row[csf('stripe_color')]]="disabled";
						$colorIdFoundStatusArr[$row[csf('stripe_color')]]=1;
						$systemNo=$stripColorArr[$row[csf('stripe_color')]]['ydw_no'];
					}

					if($progYwdDone==1)
					{ 
						//$colorIdFoundStatus=$colorIdFoundStatus;
						//$disableActive=$disableActive;
						$colorIdFoundStatus= $colorIdFoundStatusArr[$row[csf('stripe_color')]];
						$disableActive= $disableActiveArr[$row[csf('stripe_color')]];
					}
					else
					{
						$colorIdFoundStatus= $colorIdFoundStatusArr[$row[csf('stripe_color')]];
						$disableActive= $disableActiveArr[$row[csf('stripe_color')]];
					}  
					?>
					<tr>
						<th>
							<input type="text" id="stcolor_<? echo $i; ?>" name="stcolor_<? echo $i; ?>"
							style="width:150px"
							class="text_boxes" <?php echo ($color_type_id == 6) ? "disabled" : ""; ?>
							value="<? echo $color_library[$row[csf('stripe_color')]]; ?>" <? echo $onClick . " " . $readonly . " " . $plachoder; ?> <? echo $disableActive; ?> <? //echo $if_program_found_disable; ?> />

							<input type="hidden" name="" id="colorIdFoundStatus_<? echo $i; ?>" value="<? echo $colorIdFoundStatus; ?>" <? echo $disableActive; ?> >
							<input type="hidden" name="" id="systemNo_<? echo $i; ?>" value="<? echo $systemNo; ?>" <? echo $disableActive; ?> >
							<input type="hidden" name="" id="progYwdDone" value="<? echo $progYwdDone; ?>" >
						</th>
						<th>
							<input type="text" id="measurement_<? echo $i; ?>" name="measurement_<? echo $i; ?>"
							style="width:150px" class="text_boxes_numeric"
							value="<? echo $row[csf('measurement')]; ?>"
							onChange="calculate_fidder(<? echo $i; ?>)" <? echo $disableActive; ?>/>
						</th>
						<th><? echo create_drop_down("cboorderuom_" . $i, 60, $unit_of_measurement, "", 1, "-Select-", $row[csf('uom')], "",$colorIdFoundStatus , "25,26,29"); ?></th>
						<th>
							<input type="text" id="totfidder_<? echo $i; ?>" name="totfidder_<? echo $i; ?>"
							style="width:80px" class="text_boxes_numeric"
							value="<? echo $row[csf('totfidder')]; ?>"
							onChange="calculate_fidder(<? echo $i; ?>)" <? echo $disableActive; ?>/>
						</th>
						<th>
							<input type="text" id="fabreq_<? echo $i; ?>" name="fabreq_<? echo $i; ?>"
							style="width:70px" class="text_boxes_numeric"
							value="<? echo $row[csf('fabreq')]; ?>"
							readonly />
							<input type="hidden" id="fabreqtotkg_<? echo $i; ?>" name="fabreqtotkg_<? echo $i; ?>"
							style="width:70px" class="text_boxes_numeric"
							value="<? echo $row[csf('fabreqtotkg')]; ?>" readonly/>
						</th>
						<th><? echo create_drop_down("yarndyed_" . $i, 60, $yes_no, "", 0, "", $row[csf('yarn_dyed')], "", $colorIdFoundStatus, ""); ?></th>
						<th>
							<input type="text" id="sequence_<? echo $i; ?>" name="sequence_<? echo $i; ?>"
							style="width:50px" class="text_boxes_numeric"
							value="<? echo $row[csf('sequence')]; ?>" readonly disabled />
						</th>
						<th>
							<?
							if ($color_type_id != 6) {
								?>
								<input type="button" id="increaseset_<? echo $i; ?>" style="width:30px"
								title="Add new row"
								class="formbutton" value="+"
								onClick="add_break_down_set_tr(<? echo $i; ?>)"/>
								<input type="button" id="decreaseset_<? echo $i; ?>" style="width:30px"
								title="Delete this row"
								class="formbutton" value="-"
								onClick="javascript:fn_delete_down_tr(<? echo $i; ?> ,'tbl_set_details' );"/>
								<?
							}
							?>
						</th>
					</tr>
					<?
					$i++;
				}
			} else {
				$save_update = 0;
				if ($color_type_id == 6) {
					$color = $color_library[$cbo_color_name];
					$dis = "disabled";
				} else {
					$color = "";
					$dis = "";
				}
				?>
				<tr>
					<th><input type="text" id="stcolor_1" name="stcolor_1" style="width:150px"
						class="text_boxes" <? echo $onClick . " " . $readonly . " " . $plachoder . " " . $dis; ?>
						value="<? echo $color; ?>"/></th>
						<th><input type="text" id="measurement_1" name="measurement_1" style="width:150px"
							class="text_boxes_numeric" onChange="calculate_fidder(<? echo $i; ?>)"/></th>

							<th><? echo create_drop_down("cboorderuom_1", 60, $unit_of_measurement, "", 0, "", 25, "", "", "25,26,29"); ?></th>
							<th><input type="text" id="totfidder_1" onChange="calculate_fidder(<? echo $i; ?>)"
								name="totfidder_1" style="width:80px"
								class="text_boxes_numeric"/></th>
								<th>
									<input type="text" id="fabreq_1" name="fabreq_1" style="width:70px"
									class="text_boxes_numeric"
									readonly/>
									<input type="hidden" id="fabreqtotkg_1" name="fabreqtotkg_1" style="width:70px"
									class="text_boxes_numeric" readonly/>
								</th>
								<th><? echo create_drop_down("yarndyed_1", 60, $yes_no, "", 0, "", "", "", "", ""); ?></th>
								<th>
									<input type="text" id="sequence_1" name="sequence_1"
									style="width:50px" class="text_boxes_numeric"
									value="1" readonly disabled/>
								</th>
								<th>
									<?
									if ($color_type_id != 6) {
										?>
										<input type="button" id="increaseset_1" title="Add new row" style="width:30px"
										class="formbutton" value="+"
										onClick="add_break_down_set_tr(1)"/>
										<input type="button" id="decreaseset_1" style="width:30px" title="Delete this row"
										class="formbutton" value="-"
										onClick="javascript:fn_delete_down_tr(1 ,'tbl_set_details' );"/>
										<?
									}
									?>
								</th>
							</tr>
							<?
						}
						?>
					</tbody>

					<tfoot>
						<tr>
							<th style=" width:150px"></th>
							<th><input type="text" id="tottalmeasurement" name="tottalmeasurement" style="width:150px"
								class="text_boxes_numeric" value="<? echo number_format($totmeasurement, 4); ?>"
								readonly/>
							</th>
							<th style=" width:80px"></th>
							<th><input type="text" id="totaltotfidder" name="totaltotfidder" style="width:80px"
								class="text_boxes_numeric" value="<? echo number_format($totfidder, 4); ?>" readonly/>
							</th>
							<th>
								<input type="text" id="totalfabreq" name="totalfabreq" style="width:70px"
								class="text_boxes_numeric"
								value="<? echo number_format($fabreq, 4); ?>" readonly/>
							</th>
							<th style=" width:70px"></th>
							<th>
							</th>
							<th></th>
						</tr>
						<tr>
							<td align="center" valign="middle" class="button_container" colspan="8">
								<?
								if (count($sql_data) > 0) {
									echo load_submit_buttons($permission, "fnc_stripe_color", 1, 0, "", 1, 1);

								} else {
									echo load_submit_buttons($permission, "fnc_stripe_color", 0, 0, "", 1, 1);
								}
								?>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</body>
		<script>
        //set_sum();
    </script>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    die;
}

if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	
	if ($operation == 0) {
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		if (check_table_status($_SESSION['menu_id'], 1) == 0) {
			echo "15**0";
			disconnect($con);
			die;
		}
		$new_array_color = array();
		$id = return_next_id("id", "wo_pre_stripe_color", 1);
		$field_array = "id,job_no,item_number_id,pre_cost_fabric_cost_dtls_id,color_number_id,stripe_color,measurement,uom,totfidder,fabreq,fabreqtotkg,yarn_dyed,inserted_by,insert_date,sales_dtls_id,sequence";
		for ($i = 1; $i <= $total_row; $i++) {
			$stcolor = "stcolor_" . $i;
			$measurement = "measurement_" . $i;
			$cboorderuom = "cboorderuom_" . $i;
			$fabreqtotkg = "fabreqtotkg_" . $i;
			$totfidder = "totfidder_" . $i;
			$fabreq = "fabreq_" . $i;
			$yarndyed = "yarndyed_" . $i;
			$sequence = "sequence_" . $i;

			//stripe_color_measurement_controller_urmi ai page ar entry page paoya jai nai tai 999 bosano hoase
			if(str_replace("'","",$$stcolor)!="")
			{
				if (!in_array(str_replace("'","",$$stcolor),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$stcolor), $color_library, "lib_color", "id,color_name","411");
					//echo $$txtColorName.'='.$color_id.'<br>';
					$new_array_color[$color_id]=str_replace("'","",$$stcolor);

				}
				else $color_id =  array_search(str_replace("'","",$$stcolor), $new_array_color);
			}
			else
			{
				$color_id=0;
			}

			if ($i != 1) $data_array .= ",";
			$data_array .= "(" . $id . "," . $txt_sales_order_no . "," . $cbogmtsitem . "," . $fabric_cost_id . "," . $cbo_color_name . "," . $color_id . "," . $$measurement . "," . $$cboorderuom . "," . $$totfidder . "," . $$fabreq . "," . $$fabreqtotkg . "," . $$yarndyed . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $hdnSalesDtlsId . "," . $$sequence . ")";
			$id = $id + 1;
		}
		$rID = sql_insert("wo_pre_stripe_color", $field_array, $data_array, 1);
		check_table_status($_SESSION['menu_id'], 0);
		if ($db_type == 0) {
			if ($rID) {
				mysql_query("COMMIT");
				echo "0";
			} else {
				mysql_query("ROLLBACK");
				echo "10";
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($rID) {
				oci_commit($con);
				echo "0";
			} else {
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}

	if ($operation == 1)  // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		if (check_table_status($_SESSION['menu_id'], 1) == 0) {
			echo "15**0";
			disconnect($con);
			die;
		}
		$new_array_color = array();
		$id = return_next_id("id", "wo_pre_stripe_color", 1);
		$field_array = "id,job_no,item_number_id,pre_cost_fabric_cost_dtls_id,color_number_id,stripe_color,measurement,uom,totfidder,fabreq,fabreqtotkg,yarn_dyed,inserted_by,insert_date,sales_dtls_id,sequence";

		for ($i = 1; $i <= $total_row; $i++) {
			$stcolor = "stcolor_" . $i;
			$measurement = "measurement_" . $i;
			$cboorderuom = "cboorderuom_" . $i;
			$fabreqtotkg = "fabreqtotkg_" . $i;
			$totfidder = "totfidder_" . $i;
			$fabreq = "fabreq_" . $i;
			$yarndyed = "yarndyed_" . $i;			
			$sequence = "sequence_" . $i;			

			if(str_replace("'","",$$stcolor)!="")
			{
				if (!in_array(str_replace("'","",$$stcolor),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$stcolor), $color_library, "lib_color", "id,color_name","411");
					//echo $$txtColorName.'='.$color_id.'<br>';
					$new_array_color[$color_id]=str_replace("'","",$$stcolor);

				}
				else $color_id =  array_search(str_replace("'","",$$stcolor), $new_array_color);
			}
			else
			{
				$color_id=0;
			}

			if ($i != 1) $data_array .= ",";
			$data_array .= "(" . $id . "," . $txt_sales_order_no . "," . $cbogmtsitem . "," . $fabric_cost_id . "," . $cbo_color_name . "," . $color_id . "," . $$measurement . "," . $$cboorderuom . "," . $$totfidder . "," . $$fabreq . "," . $$fabreqtotkg . "," . $$yarndyed . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $hdnSalesDtlsId . "," . $$sequence . ")";
			$id = $id + 1;
		}
		$precost_con = ($hdnWithinGroup == 1) ? "and pre_cost_fabric_cost_dtls_id =$fabric_cost_id" : "";
		$rID_de3 = execute_query("delete from wo_pre_stripe_color where  color_number_id=$cbo_color_name $precost_con and sales_dtls_id=$hdnSalesDtlsId", 0);
		$rID = sql_insert("wo_pre_stripe_color", $field_array, $data_array, 1);
		check_table_status($_SESSION['menu_id'], 0);
		if ($db_type == 0) {
			if ($rID) {
				mysql_query("COMMIT");
				echo "1";
			} else {
				mysql_query("ROLLBACK");
				echo "10";
			}
		}
		if ($db_type == 2 || $db_type == 1) {
			if ($rID) {
				oci_commit($con);
				echo "1";
			} else {
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "stripe_color_list_view") {
	$data = explode("_", $data);
	$fab_description = array();
	$fab_description_id = array();
	$fab_description_string = array();
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$fab_description_array = sql_select("select a.id,a.job_no,a.within_group,b.id sales_dtls_id,b.fabric_desc, b.determination_id, b.item_number_id,b.color_type_id,b.body_part_id,b.pre_cost_fabric_cost_dtls_id, b.gsm_weight,b.dia,b.width_dia_type from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.job_no='$data[0]' and b.status_active=1 and b.is_deleted=0 and b.color_type_id in(".$colorTypeId.") group by a.id,a.job_no,b.id,b.fabric_desc,b.determination_id,b.color_type_id,b.item_number_id,b.body_part_id,b.pre_cost_fabric_cost_dtls_id, b.gsm_weight,b.dia,b.width_dia_type,a.within_group");

	foreach ($fab_description_array as $row_fab_description_array) {
		$fab_description[$row_fab_description_array[csf("job_no")]][$row_fab_description_array[csf("sales_dtls_id")]] = $body_part[$row_fab_description_array[csf("body_part_id")]] . ', ' . $color_type[$row_fab_description_array[csf("color_type_id")]] . ', ' . $row_fab_description_array[csf("fabric_desc")] . ', ' . $row_fab_description_array[csf("gsm_weight")] . ', ' . $row_fab_description_array[csf("dia")];

		$fab_description_id[$row_fab_description_array[csf("job_no")]][$row_fab_description_array[csf("sales_dtls_id")]] = $row_fab_description_array[csf("body_part_id")] . '_' . $row_fab_description_array[csf("color_type_id")] . '_' . $row_fab_description_array[csf("fabric_desc")] . '_' . $row_fab_description_array[csf("item_number_id")] . '_' . $row_fab_description_array[csf("pre_cost_fabric_cost_dtls_id")] . '_' . $row_fab_description_array[csf("gsm_weight")] . '_' . $row_fab_description_array[csf("dia")] . '_' . $row_fab_description_array[csf("width_dia_type")] . '_' . $row_fab_description_array[csf("within_group")];

		$fab_description_string[$row_fab_description_array[csf("job_no")]][$row_fab_description_array[csf("sales_dtls_id")]] = $row_fab_description_array[csf("body_part_id")] . ', ' . $row_fab_description_array[csf("color_type_id")] . ', ' . $row_fab_description_array[csf("pre_cost_fabric_cost_dtls_id")];

	}
	//	echo "<pre>";
	//	print_r($fab_description_id);
	//echo $fab_description['WG-FSOE-17-00022'][10508];

	//-------------validation if found Planning Info Entry For Sales Order entry page------------------

	$found_prog_chk= sql_select("select c.color_id,b.job_no,a.dtls_id,a.body_part_id,a.determination_id,a.pre_cost_fabric_cost_dtls_id,  a.color_type_id,d.stripe_color_id  from ppl_color_wise_break_down c,ppl_planning_feeder_dtls d,ppl_planning_entry_plan_dtls a,fabric_sales_order_mst b where c.plan_id=a.mst_id  and c.plan_id=d.mst_id and c.program_no=d.dtls_id and d.dtls_id=a.dtls_id and c.program_no=a.dtls_id and a.po_id=b.id   and b.job_no='$data[0]'  and a.color_type_id in(".$colorTypeId.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.is_sales=1");
	if(!empty($found_prog_chk))
	{
		foreach ($found_prog_chk as $rows)
		{
			$progYwdDone=1;
			$colorIdFoundStatusArr[$rows[csf('body_part_id')] . ', ' . $rows[csf('color_type_id')] . ', ' . $rows[csf('pre_cost_fabric_cost_dtls_id')]]=1;
			$systemNos[$rows[csf('body_part_id')] . ', ' . $rows[csf('color_type_id')] . ', ' . $rows[csf('pre_cost_fabric_cost_dtls_id')]]=$rows[csf('dtls_id')];
		}
	}
	//print_r($systemNo);
	//-------------------------end--------------------------------------------------------------
	//-------------validation if found yarn dyeing work order sales entry page------------------
	if(empty($found_prog_chk))
	{
		$yarnDyeingSql=sql_select("select a.ydw_no,b.job_no from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.is_sales=1 and a.entry_form=135 and b.entry_form=135  and b.job_no='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

		foreach ($yarnDyeingSql as $rows)
		{
			$progYwdDone=2;
			$stripColorArr[$rows[csf('job_no')]]['colorIdFoundStatus']=1;
			$stripColorArr[$rows[csf('job_no')]]['ydw_no']=$rows[csf('ydw_no')];
		}
	}
	//-------------------------end--------------------------------------------------------------


	$sql_data = sql_select("select job_no,pre_cost_fabric_cost_dtls_id,color_number_id,sales_dtls_id from wo_pre_stripe_color where job_no='$data[0]' and is_deleted=0 and status_active=1 group by job_no,pre_cost_fabric_cost_dtls_id,color_number_id,sales_dtls_id");
	$i = 1;

	foreach ($sql_data as $row) {

		if($progYwdDone==1)
		{
			$statusFlag=$colorIdFoundStatusArr[$fab_description_string[$row[csf('job_no')]][$row[csf('sales_dtls_id')]]];
			$systemNo=$systemNos[$fab_description_string[$row[csf('job_no')]][$row[csf('sales_dtls_id')]]];
		}
		else
		{
			$statusFlag=$stripColorArr[$row[csf('job_no')]]['colorIdFoundStatus'];
			$systemNo=$stripColorArr[$rows[csf('job_no')]]['ydw_no'];
		}
		?>
		<div style="width:95%; float:left">
			<h3 align="left" class="accordion_h" style="padding: 6px 13px 0;"
			onClick="show_content_data('<? echo $fab_description_id[$row[csf('job_no')]][$row[csf('sales_dtls_id')]]; ?>', <? echo $row[csf('color_number_id')]; ?>)">
			<div style="width:50%; float:left"
			title="<? echo $fab_description[$row[csf('job_no')]][$row[csf('sales_dtls_id')]] . ", " . $color_library[$row[csf('color_number_id')]]; ?>"><? echo $fab_description[$row[csf('job_no')]][$row[csf('sales_dtls_id')]] . ", " . $color_library[$row[csf('color_number_id')]]; ?></div>
			<div style="width:50%; float:left; text-align:right; color:#F00"></div>
		</h3>
	</div>
	<div style="width:5%; float:left;">

		<input type="hidden" id="statusChkGlobal" value="<? echo $statusFlag; ?>">
		<?
		if($statusFlag==1)
		{
			?>
			<input type="hidden" id="statusChk_<? echo $i; ?>" value="<? echo $statusFlag; ?>">
			<input type="hidden" id="yrnDynProgramNo_<? echo $i; ?>" value="<? echo $systemNo; ?>">
			<?
		}
		else
		{
			if($statusFlag==""){$statusFlag=0;}
			?>
			<input type="hidden" id="statusChk_<? echo $i; ?>" value="<? echo $statusFlag; ?>">
			<input type="hidden" id="yrnDynProgramNo_<? echo $i; ?>" value="<? echo $systemNo; ?>">
			<?
		}
		?>


		<input type="button" id="decreaseyarn_<? echo $i; ?>" title="Click here to delete this record"
		style="width:30px; margin-top: 2px; padding: 6px 0 !important;" class="formbutton" value="-"
		onClick="javascript:fn_deletebreak_down_tr(<? echo $row[csf('pre_cost_fabric_cost_dtls_id')]; ?>, <? echo $row[csf('color_number_id')]; ?>, <? echo $statusFlag; ?>, <? echo $i; ?>, <? echo $systemNo; ?> );"/>
	</div>
	<?
	$i++;
}
}

if ($action == "delete_row") {
	$data = explode("_", $data);
	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}
	$rID_de3 = execute_query("delete from wo_pre_stripe_color where  pre_cost_fabric_cost_dtls_id =" . $data[0] . " and color_number_id=$data[1]", 0);
	if ($db_type == 0) {
		if ($rID_de3) {
			mysql_query("COMMIT");
			echo 1;
		} else {
			mysql_query("ROLLBACK");
			echo 10;
		}
	}

	if ($db_type == 2 || $db_type == 1) {
		if ($rID_de3) {
			oci_commit($con);
			echo 1;
		} else {
			oci_rollback($con);
			echo 10;
		}
	}
	disconnect($con);
}

if($action=="color_popup")
{
	echo load_html_head_contents("Consumption Entry","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			document.getElementById('color_name').value=data;
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<body>
		<div align="center">
			<form>
				<input type="hidden" id="color_name" name="color_name" />
				<?
				if($buyer_name=="" || $buyer_name==0)
				{
					$sql="select color_name,id FROM lib_color  WHERE status_active=1 and is_deleted=0";
				}
				else
				{
					$sql="select a.color_name,a.id FROM lib_color a, lib_color_tag_buyer b  WHERE a.id=b.color_id and b.buyer_id=$buyer_name and  status_active=1 and is_deleted=0";
				}
				echo  create_list_view("list_view", "Color Name", "160","210","420",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name", "requires/sample_booking_non_order_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0,0,2,2,2,2,2') ;



				?>
			</form>
		</div>
	</body>
	</html>
	<?
}
?>